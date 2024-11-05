<? 
session_start();
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.fabrics.php');
//header('Content-type:text/html; charset=utf-8');
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name");
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
$costing_per_id_library=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per");
$floor_library=return_library_array( "select id, floor_name from lib_prod_floor", "id", "floor_name");

if ($action=="load_drop_down_buyer")
{
	extract($_REQUEST);
    $choosenCompany = $choosenCompany; 
	echo create_drop_down( "cbo_buyer_name", 120, "select distinct buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($choosenCompany) $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );
  exit();	 
}
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id = ".$data." order by location_name","id,location_name", 0, "--Select Location--", $selected, "" );
	exit();     	 
}

//item style-------------------------------------------------------------------------//
if($action=="style_wise_search")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
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
	if($company==0) $company_name=""; else $company_name="and a.company_name=$company";
	if($buyer==0) $buyer_name=""; else $buyer_name="and a.buyer_name=$buyer";
	/*if(str_replace("'","",$job_id)!="")  $job_cond="and b.id in(".str_replace("'","",$job_id).")";
    else  */if (str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and a.job_no_mst='".$job_no."'";
    if($db_type==2) $year_cond="  extract(year from a.insert_date) as year";
	if($db_type==0) $year_cond="  SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
	
	if($db_type==0)
	{
		$year_field_con=" and SUBSTRING_INDEX(a.insert_date, '-', 1)";
		if($cbo_year!=0) $year_cond_id=" $year_field_con=$cbo_year"; else $year_cond_id="";	
	}
	else
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($cbo_year!=0) $year_cond_id=" $year_field_con=$cbo_year"; else $year_cond_id="";	
	}
	
	$sql = "select a.id, a.style_ref_no, a.job_no_prefix_num, $year_cond from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and  b.shiping_status!=3 $company_name $buyer_name $year_cond_id  group by a.id, a.style_ref_no, a.job_no_prefix_num, a.insert_date order by a.id DESC"; 
	
	echo create_list_view("list_view", "Style Refference,Job no,Year","190,100,100","440","310",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}
if($action=="job_wise_search")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $txt_job_no;
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
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
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Order No");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );//txt_job_no
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>'+'**'+'<? echo $type; ?>'+'**'+'<? echo $txt_job_no; ?>', 'create_job_no_search_list_view', 'search_div', 'style_wise_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	$type_id=$data[5];
	$job=$data[6];
	
	//echo $month_id;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
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
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}
	if($job!='') $job_cond="and a.job_no_prefix_num='$job'";else $job_cond="";
	$search_by=$data[2];
	//$search_string="%".trim($data[3])."%";
	$search_value=$data[3];
	//if($search_by==2) $search_field="a.style_ref_no"; else $search_field="a.job_no";
	if($search_by==1 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value')";	
	}
	else if($search_by==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";	
	}
	else if($search_by==3 && $search_value!=''){
		$search_con=" and b.po_number like('%$search_value%')";	
	}
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	if($type_id==1)
	{
		$field_type="id,job_no_prefix_num";
	}
	else if($type_id==2)
	{
		$field_type="id,style_ref_no";
	}
	else if($type_id==3)
	{
		$field_type="id,po_number";
	}
	 $sql= "select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.po_number,b.pub_shipment_date, $year_field from wo_po_details_master a,wo_po_break_down b where  b.job_no_mst=a.job_no and a.status_active=1 and a.is_deleted=0 and a.company_name in($company_id) $search_con $buyer_id_cond $year_cond $job_cond  order by a.job_no";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref.,PO No,Ship Date", "120,130,80,50,120,80","750","240",0, $sql , "js_set_value", "$field_type", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number,pub_shipment_date", "",'','0,0,0,0,0,0,3','') ;
	exit(); 
} // Job Search end

//order wise browse-------------------------------------------------------------------------------//
if($action=="job_wise_search_old") //Not used
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
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
	//cbo_year
	if($company==0) $company_name=""; else $company_name=" and b.company_name=$company";//job_no
	if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name=$buyer";
    if($db_type==2) $year_cond="  extract(year from b.insert_date) as year";
	if($db_type==0) $year_cond="  SUBSTRING_INDEX(b.insert_date, '-', 1) as year";
	if($db_type==0)
	{
		$year_field_con=" and SUBSTRING_INDEX(b.insert_date, '-', 1)";
		if($cbo_year!=0) $year_cond_id=" $year_field_con=$cbo_year"; else $year_cond_id="";	
	}
	else
	{
		$year_field_con=" and to_char(b.insert_date,'YYYY')";
		if($cbo_year!=0) $year_cond_id=" $year_field_con=$cbo_year"; else $year_cond_id="";	
	}//echo $year_cond_id;die;
	$sql = "select distinct b.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$year_cond from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $company_name  $buyer_name $year_cond_id";
	
	echo create_list_view("list_view", "Order Number,Job No,Year,Style Ref","150,100,100,100","500","310",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0", $arr, "po_number,job_no_prefix_num,year,style_ref_no", "","setFilterGrid('list_view',-1)","0","",1) ;
		
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}

//order wise browse------------------------------//
if($action=="order_wise_search")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
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
	
	if($company==0) $company_name=""; else $company_name=" and b.company_name=$company";
	if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name=$buyer";
	if(str_replace("'","",$job_id)!="")  $job_cond="and a.id in(".str_replace("'","",$job_id).")";
    else  if (str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and a.job_no_mst='".$job_no."'";
	if(str_replace("'","",$style_id)!="")  $style_cond="and b.id in(".str_replace("'","",$style_id).")";
    else  if (str_replace("'","",$style_no)=="") $style_cond=""; else $style_cond="and b.style_ref_no='".$style_no."'";
	if($db_type==2) $year_cond="  extract(year from b.insert_date) as year";
	if($db_type==0) $year_cond="  SUBSTRING_INDEX(b.insert_date, '-', 1) as year";
	if($db_type==0)
	{
		$year_field_con=" and SUBSTRING_INDEX(b.insert_date, '-', 1)";
		if($cbo_year!=0) $year_cond_id=" $year_field_con=$cbo_year"; else $year_cond_id="";	
	}
	else
	{
		$year_field_con=" and to_char(b.insert_date,'YYYY')";
		if($cbo_year!=0) $year_cond_id=" $year_field_con=$cbo_year"; else $year_cond_id="";	
	}
	
	$sql = "select distinct a.id,a.po_number,b.style_ref_no,b.job_no_prefix_num,$year_cond from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $company_name $job_cond  $buyer_name $style_cond $year_cond_id";
	echo create_list_view("list_view", "Style Ref,Order Number,Job No, Year","150,150,100,100,","550","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "style_ref_no,po_number,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}

if($action=="generate_report")
{ 
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
	//echo $txt_ref_no; die;
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$job_cond_id=""; 
	$style_cond="";
	$order_cond="";
   	if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and b.company_name in(".str_replace("'","",$cbo_company_name).")";
	if(str_replace("'","",$cbo_buyer_name)==0)  $buyer_name=""; else $buyer_name="and b.buyer_name in(".str_replace("'","",$cbo_buyer_name).")";
	
  	//if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and b.company_name=".str_replace("'","",$cbo_company_name)."";
	//if(str_replace("'","",$cbo_buyer_name)==0)  $buyer_name=""; else $buyer_name="and b.buyer_name=".str_replace("'","",$cbo_buyer_name)."";
	
	/*if(str_replace("'","",$hidden_job_id)!="")  $job_cond_id="and b.id in(".str_replace("'","",$hidden_job_id).")";
	else  */if (str_replace("'","",$txt_job_no)=="") $job_cond_id=""; else $job_cond_id="and b.job_no_prefix_num=".trim($txt_job_no)."";
	
	
	
	/*if(str_replace("'","",$hidden_style_id)!="")  $style_cond="and b.id in(".str_replace("'","",$hidden_style_id).")";
	else  */
	if (str_replace("'","",$txt_style_no)=="") $style_cond=""; else $style_cond="and b.style_ref_no=".trim($txt_style_no)."";
	if(str_replace("'","",$txt_style_ref)==0) $txt_style=""; else $txt_style=" and b.style_ref_no = $txt_style_ref";

	
	
	
	/*if (str_replace("'","",$hidden_order_id)!=""){ $job_cond_id="and a.id in (".str_replace("'","",$hidden_order_id).")";  }
	else */if (str_replace("'","",$txt_order_no)=="") $order_cond=""; else $order_cond="and a.po_number=".trim($txt_order_no).""; 
	if (str_replace("'","",$txt_po_no)=="") $po_order_cond=""; else $po_order_cond="and a.po_number=".trim($txt_po_no).""; 
	
	if (str_replace("'","",$cbo_ship_status)==0) $ship_status_cond=""; else $ship_status_cond="and a.shiping_status=".trim($cbo_ship_status).""; 
	
    $serch_by=str_replace("'","",$cbo_search_by);
	$cbo_year=str_replace("'","",$cbo_year);
	$year_cond="";
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0) $year_cond=" and YEAR(b.insert_date)=$cbo_year";
		else if($db_type==2) $year_cond=" and to_char(b.insert_date,'YYYY')=$cbo_year";
	} //echo $year_cond.'assss';
	
	ob_start(); 
	
	if(str_replace("'","",$report_type)==1)
	{
		if(str_replace("'","",$cbo_search_by)==1 || str_replace("'","",$cbo_search_by)==3)
		{
			?>
			<fieldset style="width:3800px;">
                <table width="1880"  cellspacing="0"   >
                    <tr class="form_caption" style="border:none;">
                    	<td colspan="29" align="center" style="border:none;font-size:14px; font-weight:bold" >Daily Style Wise Garments Production Status Report</td>
                    </tr>
                    <tr style="border:none;">
                        <td colspan="29" align="center" style="border:none; font-size:16px; font-weight:bold">
                        Company Name:<? 
							$com_arr=explode(",",str_replace("'","",$cbo_company_name));
							$comName="";
							foreach($com_arr as $comID)
							{
								$comName.=$company_library[$comID].',';
							}
							echo chop($comName,",");
							//echo $company_library[str_replace("'","",$cbo_company_name)]; 
						?>                                
                        </td>
                    </tr>
                    <tr style="border:none;">
                        <td colspan="29" align="center" style="border:none;font-size:12px; font-weight:bold">
                        <? echo "Date: ". str_replace("'","",$txt_date_from) ;?>
                        </td>
                    </tr>
                </table>
                <br />	
                <table class="rpt_table" width="3785" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr >
                            <th width="40" rowspan="2">SL</th>
                            <th width="100" rowspan="2">Buyer</th>
                            <th width="140" rowspan="2">Style</th>
                            <th width="60" rowspan="2">Job No</th>
                            <th width="50" rowspan="2">Year</th>
                            <th width="100" rowspan="2">Job Qty.</th>
                            <th width="100" rowspan="2">Ship Date</th>
                            <th width="120" rowspan="2">Shiping Status</th>
                            <th width="100" rowspan="2">Fin. Fab. Issued</th>
                            <th width="100" rowspan="2">Possible Cut Qty.</th>
                            <th width="240" colspan="3">Cutting</th>
                            <th width="240" colspan="3">EMBL Issue	</th>
                            <th width="240" colspan="3">EMBL Receive</th>
                            <th width="240" colspan="3">Sewing Input	</th>
                            <th width="240" colspan="3">Sewing Output</th>
                            <th width="240" colspan="3">Iron</th>
                            <th width="200" colspan="2">Re-Iron Qty</th>
                            <th width="160" colspan="2">Sewing Reject</th>
                            <th width="240" colspan="3">Finish	</th>
                            <th colspan="3">Ex- Factory</th>
                        </tr>
                        <tr>
                            <th width="100" >Today.</th>
                            <th width="100" >Total </th>
                            <th width="100">WIP/ Bal. </th>
                            <th width="100" >Today</th>
                            <th width="100" >Total</th>
                            <th width="100" >Issue Bal.</th>
                            <th width="100" >Today </th>
                            <th width="100" >Total</th>
                            <th width="100" >WIP Bal</th>
                            <th width="100" > Today </th>
                            <th width="100" >Total </th>
                            <th width="100" >WIP/ Bal.</th>
                            <th width="100" >Today </th>
                            <th width="100" >Total </th>
                            <th width="100" >WIP/ Bal.</th>
                            <th width="100" >Today </th>
                            <th width="100" >Total </th>
                            <th width="100" >WIP/ Bal.</th>
                            <th width="100" >Today </th>
                            <th width="100" >Total </th>
                            <th width="100" >Today </th>
                            <th width="100" >Total</th>
                            <th width="100" >Today </th>
                            <th width="100" >Total</th>
                            <th width="100" >WIP /Bal.</th>
                            <th width="100" >Today </th>
                            <th width="100" >Total</th>
                            <th>Ex-fac. Bal.</th>
                        </tr>
                    </thead>
                </table>
                <?
                $result_consumtion=array();
                $sql_consumtiont_qty=sql_select(" select a.job_no, a.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs from wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id=b.pre_cost_fabric_cost_dtls_id   GROUP BY a.job_no, a.body_part_id");
				//echo  "select a.job_no, a.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs from wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id=b.pre_cost_fabric_cost_dtls_id   GROUP BY a.job_no, a.body_part_id";
				
				/*$sql_consumtiont_qty=sql_select(" select b.po_break_down_id, b.color_number_id, c.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs
                              from  wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c
                              where b.pre_cost_fabric_cost_dtls_id=c.id and b.pcs>0 group by b.po_break_down_id, b.color_number_id, c.body_part_id");*/
                
                foreach($sql_consumtiont_qty as $row_consum)
                {
					//$con_avg= $row_consum[csf("conjunction")];///str_replace("'","",$row_sew[csf("pcs")]);
					//$con_per_pcs=$con_avg/$row_consum[csf("pcs")];	
					$result_consumtion[$row_consum[csf('job_no')]]+=$row_consum[csf("requirment")]/$row_consum[csf("pcs")];
                }
				unset($sql_consumtiont_qty); 
                //var_dump($result_consumtion['FAL-15-00198']);die;
                
                $production_data_arr=array();  		
                $production_mst_sql= sql_select("SELECT po_break_down_id,
					sum(CASE WHEN production_type ='1' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty,
					sum(CASE WHEN production_type ='2' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS printing_qnty,
					sum(CASE WHEN production_type ='3' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS printreceived_qnty,
					sum(CASE WHEN production_type ='4' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewingin_qnty,
					sum(CASE WHEN production_type ='5' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewing_out_qnty,
					sum(CASE WHEN production_type ='7' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS iron_qnty,
					sum(CASE WHEN production_type ='7' and production_date=".$txt_date_from." THEN re_production_qty ELSE 0 END) AS re_iron_qnty,
					sum(CASE WHEN production_type ='5' and production_date=".$txt_date_from." THEN reject_qnty ELSE 0 END) AS reject_today,
					sum(CASE WHEN production_type ='8' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS finish_qnty,
					
					sum(CASE WHEN production_type ='1' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty_pre,
					sum(CASE WHEN production_type ='2' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS printing_qnty_pre,
					sum(CASE WHEN production_type ='3' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS printreceived_qnty_pre,
					sum(CASE WHEN production_type ='4' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewingin_qnty_pre,
					sum(CASE WHEN production_type ='5' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewing_out_pre,
					sum(CASE WHEN production_type ='7' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS iron_pre,
					sum(CASE WHEN production_type ='7' and production_date<".$txt_date_from." THEN re_production_qty ELSE 0 END) AS re_iron_pre,
					sum(CASE WHEN production_type ='8' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS finish_pre,
					sum(CASE WHEN production_type ='5' and production_date<".$txt_date_from." THEN reject_qnty ELSE 0 END) AS reject_pre
					from 
					pro_garments_production_mst 
					where  
					is_deleted=0 and status_active=1
					group by po_break_down_id "); //reject_qnty
                
                foreach($production_mst_sql as $val)
                {
					$production_data_arr[$val[csf('po_break_down_id')]]['cutting_qnty']=$val[csf('cutting_qnty')];
					$production_data_arr[$val[csf('po_break_down_id')]]['printing_qnty']=$val[csf('printing_qnty')];
					$production_data_arr[$val[csf('po_break_down_id')]]['printreceived_qnty']=$val[csf('printreceived_qnty')];
					$production_data_arr[$val[csf('po_break_down_id')]]['sewingin_qnty']=$val[csf('sewingin_qnty')];
					$production_data_arr[$val[csf('po_break_down_id')]]['sewing_out_qnty']=$val[csf('sewing_out_qnty')];
					$production_data_arr[$val[csf('po_break_down_id')]]['iron_qnty']=$val[csf('iron_qnty')];
					$production_data_arr[$val[csf('po_break_down_id')]]['finish_qnty']=$val[csf('finish_qnty')];
					$production_data_arr[$val[csf('po_break_down_id')]]['reject_today']=$val[csf('reject_today')];
					$production_data_arr[$val[csf('po_break_down_id')]]['cutting_qnty_pre']=$val[csf('cutting_qnty_pre')];
					$production_data_arr[$val[csf('po_break_down_id')]]['printing_qnty_pre']=$val[csf('printing_qnty_pre')];
					$production_data_arr[$val[csf('po_break_down_id')]]['printreceived_qnty_pre']=$val[csf('printreceived_qnty_pre')];
					$production_data_arr[$val[csf('po_break_down_id')]]['sewingin_qnty_pre']=$val[csf('sewingin_qnty_pre')];
					$production_data_arr[$val[csf('po_break_down_id')]]['sewing_out_pre']=$val[csf('sewing_out_pre')];
					$production_data_arr[$val[csf('po_break_down_id')]]['iron_pre']=$val[csf('iron_pre')];
					$production_data_arr[$val[csf('po_break_down_id')]]['finish_pre']=$val[csf('finish_pre')];
					$production_data_arr[$val[csf('po_break_down_id')]]['reject_pre']=$val[csf('reject_pre')];
					$production_data_arr[$val[csf('po_break_down_id')]]['re_iron_qnty']=$val[csf('re_iron_qnty')];	
					$production_data_arr[$val[csf('po_break_down_id')]]['re_iron_pre']=$val[csf('re_iron_pre')];		
                }
				unset($production_mst_sql); 
                //print_r($production_data_arr[2961]);die;
                $exfactory_sql=sql_select("SELECT po_break_down_id,
					sum(CASE WHEN  ex_factory_date=".$txt_date_from." AND entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN  ex_factory_date=".$txt_date_from." AND entry_form=85 THEN ex_factory_qnty ELSE 0 END) AS exfac_qty,
					sum(CASE WHEN  ex_factory_date<".$txt_date_from." AND entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN  ex_factory_date<".$txt_date_from." AND entry_form=85 THEN ex_factory_qnty ELSE 0 END) AS exfac_pre
					from 
					pro_ex_factory_mst 
					where  
					is_deleted=0 and status_active=1
					group by po_break_down_id ");
                $exfactory_data_arr=array();
                foreach($exfactory_sql as $value)
                {
					$exfactory_data_arr[$value[csf('po_break_down_id')]]['ex_qnty']=$value[csf('exfac_qty')];
					$exfactory_data_arr[$value[csf('po_break_down_id')]]['exfac_pre']=$value[csf('exfac_pre')];
                }
				unset($exfactory_sql); 
                $sql_fabric_qty=sql_select( "SELECT a.po_breakdown_id,
					sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =2  AND a.entry_form =18  THEN a.quantity ELSE 0 END ) AS fabric_qty,
					sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_qty,
					sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_qty
					FROM order_wise_pro_details a,inv_transaction b
					WHERE a.trans_id = b.id 
					and b.status_active=1 and a.entry_form in(18,15) and a.quantity!=0 and  b.is_deleted=0  group by a.po_breakdown_id");
                $fabric_data_arr=array();
                foreach($sql_fabric_qty as $inf)
                {
                	$fabric_data_arr[$inf[csf("po_breakdown_id")]]["fab_iss"]=$inf[csf("fabric_qty")]+$inf[csf("trans_in_qty")]-$inf[csf("trans_out_qty")];
                }
				unset($sql_fabric_qty); 	
                ?>
                <div style="width:3800px; max-height:425px; overflow-y:scroll"   id="scroll_body">
                    <table class="rpt_table" width="3770" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<?
                        if($db_type==0)
                        {
							$sql_data="select b.job_no,b.job_no_prefix_num,b.company_name,b.buyer_name,b.style_ref_no,
								sum(a.po_quantity) as job_quantity ,group_concat(a.id) as po_id,sum(a.plan_cut) as plan_cut,
								year(b.insert_date) as year,max(pub_shipment_date) as ship_date,group_concat(a.shiping_status) as status 
								from  wo_po_break_down a,wo_po_details_master b
								where a.job_no_mst=b.job_no  $company_name $buyer_name $job_cond_id $style_cond $order_cond $po_order_cond $ship_status_cond $year_cond and b.status_active=1 and                               b.is_deleted=0 and a.status_active=1   
								group by b.company_name,b.job_no_prefix_num,b.job_no,b.insert_date order by b.buyer_name,b.job_no_prefix_num";
							$sql=sql_select($sql_data);	
                        }
                        else if($db_type==2)
                        {
							$sql_data="select b.job_no, b.job_no_prefix_num, b.company_name, b.buyer_name, b.style_ref_no, sum(a.po_quantity) as job_quantity, 
								listagg(a.id,',') within group (order by a.id) as po_id,
								sum(a.plan_cut) as plan_cut, extract(year from b.insert_date) as year,
								max(pub_shipment_date) as ship_date, 
								listagg(a.shiping_status,',') within group (order by a.shiping_status) as status
								from wo_po_break_down a,wo_po_details_master b
								where a.job_no_mst=b.job_no $company_name $buyer_name $job_cond_id $style_cond $order_cond $po_order_cond $ship_status_cond $year_cond 
								and b.status_active=1 and b.is_deleted=0 and a.status_active=1 
								group by b.company_name, b.job_no, b.job_no_prefix_num, b.buyer_name, b.style_ref_no, b.insert_date order by b.buyer_name, b.job_no_prefix_num";
							$sql=sql_select($sql_data);
									
                        }
						
                        //print_r($sql);
                        $fabric_iss=0;
                        $grand_cut=$grand_cut_total=$grand_embl_issue=$grand_embl_iss_total=$grand_sew_in=$grand_sew_in_total=$grand_sew_out=$grand_sew_out_total=0;
                        $grand_iron=$grand_iron_total=$grand_finish=$grand_finish_total=$grand_reject=$grand_reject_total=$grand_exfactory=$grand_exfa_total=0; 
                        $grand_fabric_iss=$grand_embl_rec=$grand_embl_rev_total=$grand_plan_cut=$job_total=0;	
                        $tot_rows=count($sql);
                        $i=1;	   		
                        foreach($sql as $row)	
                        {
							$dzn_qnty=0;
							if($costing_per_id_library[$row[csf('job_no')]]==1) $dzn_qnty=12;
							else if($costing_per_id_library[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
							else if($costing_per_id_library[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
							else if($costing_per_id_library[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
							else $dzn_qnty=1;
                        
							$all_ship=0;
							$partial_ship=0;
							$full_pend=0;
							$ship_status=0;
							$full_ship=$fabric_iss=$exfactory_total=0;
							$cut_today=$embl_issue_today=$embl_rcv_today=$sewing_in_today=$sew_out_today=$iron_today=$finish_today=$reject_today=$exfactory_qty=0;
							$re_iron_today=$re_iron_total=0;
							$fabric_iss=$cut_total=$embl_issue_total=$embl_rcv_total=$sewing_in_total=$iron_total=$sew_out_total=$finish_total=$reject_total=0;
                        
							$po_id_all=explode(',',$row[csf('po_id')]);
							$k=0;
							foreach( $po_id_all as $val_a)
							{
								$cut_today+=$production_data_arr[$val_a]['cutting_qnty'];
								$embl_issue_today+=$production_data_arr[$val_a]['printing_qnty'];
								$embl_rcv_today+=$production_data_arr[$val_a]['printreceived_qnty'];
								$sewing_in_today+=$production_data_arr[$val_a]['sewingin_qnty'];
								$sew_out_today+=$production_data_arr[$val_a]['sewing_out_qnty'];
								$iron_today+=$production_data_arr[$val_a]['iron_qnty'];
								$re_iron_today+=$production_data_arr[$val_a]['re_iron_qnty'];
								
								$finish_today+=$production_data_arr[$val_a]['finish_qnty'];
								$reject_today+=$production_data_arr[$val_a]['reject_today'];
								$exfactory_qty+=$exfactory_data_arr[$val_a]['ex_qnty'];
								$fabric_iss+=$fabric_data_arr[$val_a]['fab_iss'];
								$cut_total+=$production_data_arr[$val_a]['cutting_qnty_pre']+$production_data_arr[$val_a]['cutting_qnty'];
								$embl_issue_total+=$production_data_arr[$val_a]['printing_qnty_pre']+$production_data_arr[$val_a]['printing_qnty'];
								$embl_rcv_total+=$production_data_arr[$val_a]['printreceived_qnty_pre']+$production_data_arr[$val_a]['printreceived_qnty'];
								$sewing_in_total+=$production_data_arr[$val_a]['sewingin_qnty_pre']+$production_data_arr[$val_a]['sewingin_qnty'];
								$iron_total+=$production_data_arr[$val_a]['iron_pre']+$production_data_arr[$val_a]['iron_qnty'];
								$re_iron_total+=$production_data_arr[$val_a]['re_iron_qnty']+$production_data_arr[$val_a]['re_iron_pre'];
								
								$sew_out_total+=$production_data_arr[$val_a]['sewing_out_pre']+$production_data_arr[$val_a]['sewing_out_qnty'];
								$finish_total+=$production_data_arr[$val_a]['finish_pre']+$production_data_arr[$val_a]['finish_qnty'];
								$reject_total+=$production_data_arr[$val_a]['reject_today']+$production_data_arr[$val_a]['reject_pre'];
								$exfactory_total+=$exfactory_data_arr[$val_a]['ex_qnty']+$exfactory_data_arr[$val_a]['exfac_pre'];
								$k++;
							}	
                        
                       		//if($fabric_iss!=0 || ($cut_today!=0 || $embl_issue_today!=0 || $embl_rcv_today!=0 || $sewing_in_today!=0  || $sew_out_today!=0  || $iron_today!=0 || $exfactory_qty!=0  || $finish_today!=0))
							   if($cut_today!=0 || $embl_issue_today!=0 || $embl_rcv_today!=0 || $sewing_in_today!=0  || $sew_out_today!=0  || $iron_today!=0 || $exfactory_qty!=0  || $finish_today!=0)

                        	{
								$status_all=array_unique(explode(',',$row[csf('status')]));
								foreach($status_all as $ship_val)
								{
									if($ship_val==3) $full_ship=$ship_val;
									if($ship_val==2) $partial_ship=$ship_val;
									if($ship_val==1) $full_pend=$ship_val;
									if($ship_val==0) $all_ship=$ship_val;
								}
								if($full_ship==0)
								{
									if($partial_ship==0)
									{
										if($full_pend==0)
										{
											$ship_status=0;
										}
										else
										{
											$ship_status=1;
										}
									}
									else
									{
										$ship_status=2; 
									}
								}
								else
								{
									if($partial_ship==0)
									{
										if($full_pend==0)
										{
											$ship_status=3;
										}
										else
										{
											$ship_status=2;
										}  
									}
									else
									{  
										$ship_status=2;
									}
								} 
                        
								/*if($ship_status!=3)
								{*/
									// echo $ship_status."<br>";
									$possible_cut_pcs=0;
									$possible_cut_pcs=$fabric_iss/$result_consumtion[$row[csf('job_no')]];
									$grand_possible_cut_pcs+=$possible_cut_pcs;
									$grand_cut+=$cut_today;
									$grand_cut_total+=$cut_total;
									$grand_embl_issue+=$embl_issue_today;
									$grand_embl_iss_total+=$embl_issue_total;
									$grand_embl_rec+=$embl_rcv_today;
									$grand_embl_rev_total+=$embl_rcv_total;
									$grand_sew_in+=$sewing_in_today;
									$grand_sew_in_total+=$sewing_in_total;
									$grand_sew_out+=$sew_out_today;
									$grand_sew_out_total+=$sew_out_total;
									$grand_iron+=$iron_today;
									$grand_iron_total+=$iron_total;
									$grand_finish+=$finish_today;
									$grand_finish_total+=$finish_total;
									$grand_reject+=$reject_today;
									$grand_reject_total+=$reject_total;
									
									$grand_re_iron_today+=$re_iron_today;
									$grand_re_iron_total+=$re_iron_total;
									$grand_exfactory+=$exfactory_qty;
									$grand_exfa_total+=$exfactory_total;
									$grand_fabric_iss+=$fabric_iss;
									$grand_plan_cut+=$row[csf('plan_cut')];
									$job_total+=$row[csf('job_quantity')];
									$txt_date=str_replace("'","",$txt_date_from);
									$diff=datediff("d",$txt_date,$row[csf('ship_date')]);
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
								?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                                    <td width="40"><? echo $i;?></td>
                                    <td width="100" align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                                    <td width="140" align="center"><p><? echo $row[csf('style_ref_no')];?>&nbsp;</p></td>
                                    <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')];?></td>
                                    <td width="50" align="center"><? echo $row[csf('year')];?></td>
                                    <td width="100" align="right"><a href="#report_details" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no_prefix_num')]; ?>', '<? echo $row[csf('year')]; ?>','job_color_style',850)"><? echo number_format($row[csf('job_quantity')]); ?></a></td>
                                    <td width="100" align="center"><? echo change_date_format($row[csf('ship_date')]);?></td>
                                    <td width="120" align="center"><p><a href="#report_details" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no_prefix_num')]; ?>', '<? echo $row[csf('year')]; ?>','shipping_sataus',400)"><? echo $shipment_status[$ship_status]; ?></a></p></td>
                                    <td width="100" align="right"><? echo number_format($fabric_iss,2); ?></td>
                                    <td width="100" align="right" title="Fabric Issue/Consumtion (<? echo number_format($result_consumtion[$row[csf('job_no')]],5); ?>)"><? echo number_format($possible_cut_pcs); ?></a></td>
                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'cut_entry_today',1050)"><? if($cut_today>0){echo number_format($cut_today);} ?></a></td>
                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'cut_entry_total',1050)"><? if($cut_total>0){echo number_format($cut_total);} ?></a></td>
                                    <td width="100" align="right"><? if(($row[csf('job_quantity')]-$cut_total)!=0){echo number_format(($row[csf('job_quantity')]-$cut_total));} ?></td>
                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'embl_today',1000)"><? if($embl_issue_today>0){echo number_format($embl_issue_today);}?></a></td>
                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'embl_total',1000)"><? if($embl_issue_total>0){echo number_format($embl_issue_total);} ?></a></td>
                                    <td width="100" align="right"><? if(($cut_total-$embl_issue_total)!=0){echo number_format(($cut_total-$embl_issue_total));} ?></td>
                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'embl_receive_today',900)"><? if($embl_rcv_today>0){echo number_format($embl_rcv_today);} ?></a></td>
                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'embl_receive_total',900)"><? if($embl_rcv_total>0){echo number_format($embl_rcv_total);} ?></a></td>
                                    <td width="100" align="right"><?  if(($embl_issue_total-$embl_rcv_total)!=0){echo number_format(($embl_issue_total-$embl_rcv_total));} ?></td>
                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'sewing_input_today',900)"><? if($sewing_in_today>0){echo number_format($sewing_in_today);} ?></a></td>
                                    <td width="100" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'sewing_input_total',900)"><?  if($sewing_in_total>0){echo number_format($sewing_in_total);} ?></td>
                                    <td width="100" align="right"><? if(($cut_total-$sewing_in_total)!=0){echo number_format(($cut_total-$sewing_in_total));} ?></td>
                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'sewing_output_today',900)"><? if($sew_out_today>0){echo number_format($sew_out_today);} ?></a></td>
                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'sewing_ooutput_total',900)"><? if($sew_out_total>0){echo number_format($sew_out_total);} ?></a></td>
                                    <td width="100" align="right"><? if(($sewing_in_total-$sew_out_total)!=0){echo number_format(($sewing_in_total-$sew_out_total));} ?></td>
                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'iron_today',900)"><? if($iron_today>0){echo number_format($iron_today);} ?></a></td>
                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'iron_total',900)"><? if($iron_total>0){ echo number_format($iron_total);} ?></a></td>
                                    <td width="100" align="right"><?  if(($sew_out_total-$iron_total)!=0){ echo number_format(($sew_out_total-$iron_total));} ?></td>
                                    <td width="100" align="right"><? if($re_iron_today>0){echo number_format($re_iron_today);} ?></td>
                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'re_iron_total',850)"><?  if($re_iron_total>0){echo number_format($re_iron_total);} ?></a></td>
                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'reject_today',850)"><? if($reject_today>0){echo number_format($reject_today);} ?></a></td>
                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'reject_total',850)"><? if($reject_total>0){echo number_format($reject_total);} ?></a></td>
                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'finish_today',900)"><?  if($finish_today>0){echo number_format($finish_today);} ?></a></td>
                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'finish_total',900)"><?  if($finish_total>0){echo number_format($finish_total);} ?></a></td>
                                    <td width="100" align="right"><? if(($sew_out_total-$finish_total)!=0){echo number_format(($sew_out_total-$finish_total));} ?></td>
                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'exfactory_today',900)"><? if($exfactory_qty>0){echo number_format($exfactory_qty);} ?></a></td>
                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'exfactory_total',900)"><? if($exfactory_total>0){echo number_format(($exfactory_total));} ?></a></td>
                                    <td align="right"><?  if(($row[csf('job_quantity')]-$exfactory_total)!=0){echo number_format(($row[csf('job_quantity')]-$exfactory_total));}  ?></td>
                                </tr>    
							<?
                            $ship_status="";
                            $i++;
                            //}
                        }
					}
					?>
                </table>
                </div>
                <table style="width:3770px" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all">
                    <tr>
                        <td width="40">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="140"><strong>Grand Total:</strong></td>
                        <td width="60">&nbsp;</td>
                        <td width="50">&nbsp;</td>
                        <td width="100" align="right" id="value_job_total"><? echo number_format($job_total); ?></td>
                        <td width="100">&nbsp;</td>
                        <td width="120">&nbsp;</td>
                        <td width="100" align="right" id="value_fabric_issue"><? echo number_format($grand_fabric_iss); ?></td>
                        <td width="100" align="right" id="value_plan_cut"><? echo number_format($grand_possible_cut_pcs); ?></td>
                        <td width="100" align="right" id="value_cut_today"><? echo number_format($grand_cut); ?></td>
                        <td width="100" align="right" id="value_cut_total"><? echo number_format($grand_cut_total); ?></td>
                        <td width="100" align="right" id="value_cut_bal"><? echo number_format(($job_total-$grand_cut_total)); ?></td>
                        <td width="100" align="right" id="value_embl_iss"><? echo number_format($grand_embl_issue); ?></td>
                        <td width="100" align="right" id="value_embl_iss_total"><? echo number_format($grand_embl_iss_total);?></td>
                        <td width="100" align="right" id="value_embl_iss_bal"><? echo number_format(($grand_cut_total-$grand_embl_iss_total)); ?></td>
                        <td width="100" align="right" id="value_embl_rec"><? echo number_format($grand_embl_rec); ?></td>
                        <td width="100" align="right" id="value_embl_rec_total"><? echo number_format($grand_embl_rev_total); ?></td>
                        <td width="100" align="right" id="value_embl_rec_bal"><? echo number_format(($grand_embl_iss_total-$grand_embl_rev_total)); ?></td>
                        <td width="100" align="right" id="value_sew_in"><? echo number_format($grand_sew_in); ?></td>
                        <td width="100" align="right" id="value_sew_in_to"><? echo number_format($grand_sew_in_total); ?></td>
                        <td width="100" align="right" id="value_sew_in_bal"><? echo number_format(($grand_cut_total-$grand_sew_in_total)); ?></td>
                        <td width="100" align="right" id="value_sew_out"><? echo number_format($grand_sew_out); ?></td>
                        <td width="100" align="right" id="value_sew_out_total"><? echo number_format($grand_sew_out_total); ?></td>
                        <td width="100" align="right" id="value_sew_out_bal"><? echo number_format(($grand_sew_in_total-$grand_sew_out_total)); ?></td>
                        <td width="100" align="right" id="value_iron"><? echo number_format($grand_iron); ?></td>
                        <td width="100" align="right" id="value_iron_to"><? echo number_format($grand_iron_total); ?></td>
                        <td width="100" align="right" id="value_iron_bal"><? echo number_format($grand_sew_out_total-$grand_iron_total); ?></td>
                        <td width="100" align="right" id="value_re_iron"><? echo number_format($grand_re_iron_today); ?></td>
                        <td width="100" align="right" id="value_re_iron_to"><? echo number_format($grand_re_iron_total); ?></td>
                        <td width="100" align="right" id="value_reject"><? echo number_format($grand_reject); ?></td>
                        <td width="100" align="right" id="value_reject_to"><? echo number_format($grand_reject_total); ?></td>
                        <td width="100" align="right" id="value_finish"><? echo number_format($grand_finish); ?></td>
                        <td width="100" align="right" id="value_finish_to"><? echo number_format($grand_finish_total); ?></td>
                        <td width="100" align="right" id="value_finish_bal"><? echo number_format(($grand_sew_out_total-$grand_finish_total));?></td>
                        <td width="100" align="right" id="value_exfactory"><? echo number_format($grand_exfactory); ?></td>
                        <td width="100" align="right" id="value_exfactory_to"><? echo number_format($grand_exfa_total); ?></td>
                        <td align="right" id="value_exfac_bal"><? echo number_format(($job_total-$grand_exfa_total)); ?></td>
                    </tr> 
                </table>
			</fieldset>
			<?	
		}
		else if(str_replace("'","",$cbo_search_by)==2)
		{
			?>
			<fieldset style="width:3800px;">
			<table width="1880"  cellspacing="0"   >
                <tr class="form_caption" style="border:none;">
                	<td colspan="29" align="center" style="border:none;font-size:14px; font-weight:bold" >Daily Order Wise Garments Production Status Report</td>
                </tr>
                <tr style="border:none;">
                    <td colspan="29" align="center" style="border:none; font-size:16px; font-weight:bold">
                    Company Name:<? 
						$com_arr=explode(",",str_replace("'","",$cbo_company_name));
						$comName="";
						foreach($com_arr as $comID)
						{
							$comName.=$company_library[$comID].',';
						}
						echo chop($comName,",");
						//echo $company_library[str_replace("'","",$cbo_company_name)]; 
					?>                                
                    </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="29" align="center" style="border:none;font-size:12px; font-weight:bold">
                    <? echo "Date: ". str_replace("'","",$txt_date_from) ;?>
                    </td>
                </tr>
			</table>
			<br />	
			<table class="rpt_table" width="3772" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr >
                        <th width="40"  rowspan="2">SL</th>
                        <th width="100" rowspan="2">Buyer</th>
                        <th width="150" rowspan="2">Style</th>
                        <th width="60" rowspan="2">Job No</th>
                        <th width="50" rowspan="2">Year</th>
                        <th width="200" rowspan="2">Order No</th>
                        <th width="100" rowspan="2">Order Qty.</th>
                        <th width="100" rowspan="2">Ship Date</th>
                        <th width="120" rowspan="2">Shiping Status</th>
                        <th width="100" rowspan="2">Fin. Fab. Issued</th>
                        <th width="100" rowspan="2">Possible Cut Qty.</th>
                        <th width="300" colspan="3">Cutting</th>
                        <th width="300" colspan="3">EMBL Issue	</th>
                        <th width="300" colspan="3">EMBL Receive</th>
                        <th width="300" colspan="3">Sewing Input	</th>
                        <th width="300" colspan="3">Sewing Output</th>
                        <th width="300" colspan="3">Iron</th>
                        <th width="200" colspan="2">Sewing Reject</th>
                        <th width="300" colspan="3">Finish	</th>
                        <th colspan="3">Ex- Factory</th>
                    </tr>
                    <tr>
                        <th width="100" >Today.</th>
                        <th width="100">Total </th>
                        <th width="100" >WIP Bal. </th>
                        <th width="100" >Today</th>
                        <th width="100" >Total</th>
                        <th width="100" >Issue Bal.</th>
                        <th width="100" >Today </th>
                        <th width="100" >Total</th>
                        <th width="100" >WIP Bal</th>
                        <th width="100" > Today </th>
                        <th width="100" >Total </th>
                        <th width="100" >WIP/ Bal.</th>
                        <th width="100" >Today </th>
                        <th width="100" >Total </th>
                        <th width="100" >WIP/ Bal.</th>
                        <th width="100" >Today </th>
                        <th width="100" >Total </th>
                        <th width="100" >Iron Bal.</th>
                        <th width="100" >Today </th>
                        <th width="100" >Total</th>
                        <th width="100" >Today </th>
                        <th width="100" >Total</th>
                        <th width="100" >WIP/ Bal.</th>
                        <th width="100" >Today </th>
                        <th width="100" >Total</th>
                        <th  >Ex-fac. Bal.</th>
                    </tr>
                </thead>
			</table>
			<?
			$production_data_arr=array();		
			$production_mst_sql= sql_select("SELECT po_break_down_id,
				sum(CASE WHEN production_type ='1' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty,
				sum(CASE WHEN production_type ='2' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS printing_qnty,
				sum(CASE WHEN production_type ='3' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS printreceived_qnty,
				sum(CASE WHEN production_type ='4' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewingin_qnty,
				sum(CASE WHEN production_type ='5' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewing_out_qnty,
				sum(CASE WHEN production_type ='7' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS iron_qnty,
				sum(CASE WHEN production_type ='8' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS finish_qnty,
				sum(CASE WHEN production_type ='5' and production_date=".$txt_date_from." THEN reject_qnty ELSE 0 END) AS reject_today,
				sum(CASE WHEN production_type ='1' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty_pre,
				sum(CASE WHEN production_type ='2' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS printing_qnty_pre,
				sum(CASE WHEN production_type ='3' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS printreceived_qnty_pre,
				sum(CASE WHEN production_type ='4' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewingin_qnty_pre,
				sum(CASE WHEN production_type ='5' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewing_out_pre,
				sum(CASE WHEN production_type ='7' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS iron_pre,
				sum(CASE WHEN production_type ='8' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS finish_pre,
				sum(CASE WHEN production_type ='5' and production_date<".$txt_date_from." THEN reject_qnty ELSE 0 END) AS reject_pre
				from 
				pro_garments_production_mst 
				where  
				is_deleted=0 and status_active=1 
				group by po_break_down_id "); //reject_qnty
			foreach($production_mst_sql as $val)
			{
				$production_data_arr[$val[csf('po_break_down_id')]]['cutting_qnty']=$val[csf('cutting_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printing_qnty']=$val[csf('printing_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printreceived_qnty']=$val[csf('printreceived_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewingin_qnty']=$val[csf('sewingin_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewing_out_qnty']=$val[csf('sewing_out_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['iron_qnty']=$val[csf('iron_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['finish_qnty']=$val[csf('finish_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['reject_today']=$val[csf('reject_today')];
				$production_data_arr[$val[csf('po_break_down_id')]]['cutting_qnty_pre']=$val[csf('cutting_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printing_qnty_pre']=$val[csf('printing_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printreceived_qnty_pre']=$val[csf('printreceived_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewingin_qnty_pre']=$val[csf('sewingin_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewing_out_pre']=$val[csf('sewing_out_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['iron_pre']=$val[csf('iron_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['finish_pre']=$val[csf('finish_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['reject_pre']=$val[csf('reject_pre')];	
			}
			 $result_consumtion=array();
			$sql_consumtiont_qty=sql_select(" select b.po_break_down_id, a.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs from wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id=b.pre_cost_fabric_cost_dtls_id   GROUP BY b.po_break_down_id, a.body_part_id");
			//echo  "select b.po_break_down_id, a.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs from wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id=b.pre_cost_fabric_cost_dtls_id   GROUP BY b.po_break_down_id, a.body_part_id";
			
			/*$sql_consumtiont_qty=sql_select(" select b.po_break_down_id, b.color_number_id, c.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs
						  from  wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c
						  where b.pre_cost_fabric_cost_dtls_id=c.id and b.pcs>0 group by b.po_break_down_id, b.color_number_id, c.body_part_id");*/
			
			foreach($sql_consumtiont_qty as $row_consum)
			{
				//$con_avg= $row_consum[csf("conjunction")];///str_replace("'","",$row_sew[csf("pcs")]);
				//$con_per_pcs=$con_avg/$row_consum[csf("pcs")];	
				$result_consumtion[$row_consum[csf('po_break_down_id')]]+=$row_consum[csf("requirment")]/$row_consum[csf("pcs")];
			}
			unset($sql_consumtiont_qty);
			$exfactory_sql=sql_select("SELECT po_break_down_id,
				sum(CASE WHEN  ex_factory_date=".$txt_date_from." AND entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN  ex_factory_date=".$txt_date_from." AND entry_form=85 THEN ex_factory_qnty ELSE 0 END) AS exfac_qty,
				sum(CASE WHEN  ex_factory_date<".$txt_date_from." AND entry_form!=85  THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN  ex_factory_date<".$txt_date_from." AND entry_form=85  THEN ex_factory_qnty ELSE 0 END) AS exfac_pre
				from 
				pro_ex_factory_mst 
				where  
				is_deleted=0 and status_active=1
				group by po_break_down_id ");
				
			$exfactory_data_arr=array();
			foreach($exfactory_sql as $value)
			{
				$exfactory_data_arr[$value[csf('po_break_down_id')]]['ex_qnty']=$value[csf('exfac_qty')];
				$exfactory_data_arr[$value[csf('po_break_down_id')]]['exfac_pre']=$value[csf('exfac_pre')];
			}
			$sql_fabric_qty=sql_select( "SELECT a.po_breakdown_id,
				sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =2  AND a.entry_form =18  THEN a.quantity ELSE 0 END ) AS fabric_qty,
				sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_qty,
				sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_qty
				FROM order_wise_pro_details a,inv_transaction b
				WHERE a.trans_id = b.id 
				and b.status_active=1 and a.entry_form in(18,15) and a.quantity!=0 and  b.is_deleted=0  group by a.po_breakdown_id");
			$fabric_data_arr=array();
			foreach($sql_fabric_qty as $inf)
			{
				$fabric_data_arr[$inf[csf("po_breakdown_id")]]["fab_iss"]=$inf[csf("fabric_qty")]+$inf[csf("trans_in_qty")]-$inf[csf("trans_out_qty")];
			}	
			?>
			<div style="width:3772px; max-height:425px; overflow-y:scroll"   id="scroll_body">
                <table class="rpt_table" width="3750" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<?
                    if($db_type==0)
                    {
                    	$sql_data="select b.company_name,a.id as po_id,a.po_number,b.job_no_prefix_num,b.buyer_name,b.style_ref_no,a.po_quantity,a.plan_cut as plan_cut,year(b.insert_date) as year,pub_shipment_date as ship_date,a.shiping_status as status from  wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no $company_name $buyer_name $job_cond_id $style_cond $order_cond $po_order_cond $ship_status_cond $year_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1   order by b.buyer_name,b.job_no_prefix_num";
						$sql=sql_select($sql_data);	
                    }
                    else if($db_type==2)
                    {
                    	$sql_data="select b.company_name,a.id as po_id,a.po_number,b.job_no_prefix_num,b.buyer_name,b.style_ref_no,a.po_quantity,a.plan_cut as plan_cut,extract(year from b.insert_date) as year,pub_shipment_date as ship_date,a.shiping_status as status from  wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no $company_name $buyer_name $job_cond_id $style_cond $order_cond $po_order_cond $ship_status_cond $year_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 order by b.buyer_name,b.job_no_prefix_num";
						$sql=sql_select($sql_data);	
                    }
					
					//echo $sql_data;
					
					$grand_fabric_iss=$grand_embl_rec=$grand_embl_rev_total=$grand_plan_cut=$job_total=$exfactory_total=0;
					$cut_today=$embl_issue_today=$embl_rcv_today=$sewing_in_today=$sew_out_today=$iron_today=$finish_today=$reject_today=$exfactory_qty=0;
					$fabric_iss=$cut_total=$embl_issue_total=$embl_rcv_total=$sewing_in_total=$iron_total=$sew_out_total=$finish_total=$reject_total=0;
					$grand_cut=$grand_cut_total=$grand_embl_issue=$grand_embl_iss_total=$grand_sew_in=$grand_sew_in_total=$grand_sew_out=$grand_sew_out_total=0;
					$grand_iron=$grand_iron_total=$grand_finish=$grand_finish_total=$grand_reject=$grand_reject_total=$grand_exfactory=$grand_exfa_total=0; 
					$tot_rows=count($sql);	
                    $i=1;	
                    foreach($sql as $row)	
                    {
						$cut_today=$production_data_arr[$row[csf('po_id')]]['cutting_qnty'];
						$embl_issue_today=$production_data_arr[$row[csf('po_id')]]['printing_qnty'];
						$embl_rcv_today=$production_data_arr[$row[csf('po_id')]]['printreceived_qnty'];
						$sewing_in_today=$production_data_arr[$row[csf('po_id')]]['sewingin_qnty'];
						$sew_out_today=$production_data_arr[$row[csf('po_id')]]['sewing_out_qnty'];
						$iron_today=$production_data_arr[$row[csf('po_id')]]['iron_qnty'];
						$finish_today=$production_data_arr[$row[csf('po_id')]]['finish_qnty'];
						$reject_today=$production_data_arr[$row[csf('po_id')]]['reject_today'];
						$exfactory_qty=$exfactory_data_arr[$row[csf('po_id')]]['ex_qnty'];
						$fabric_iss=$fabric_data_arr[$row[csf('po_id')]]['fab_iss'];
						$cut_total=$production_data_arr[$row[csf('po_id')]]['cutting_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['cutting_qnty'];
						$embl_issue_total=$production_data_arr[$row[csf('po_id')]]['printing_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['printing_qnty'];
						$embl_rcv_total=$production_data_arr[$row[csf('po_id')]]['printreceived_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['printreceived_qnty'];
						$sewing_in_total=$production_data_arr[$row[csf('po_id')]]['sewingin_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['sewingin_qnty'];
						$iron_total=$production_data_arr[$row[csf('po_id')]]['iron_pre']+$production_data_arr[$row[csf('po_id')]]['iron_qnty'];
						$sew_out_total+=$production_data_arr[$row[csf('po_id')]]['sewing_out_pre']+$production_data_arr[$row[csf('po_id')]]['sewing_out_qnty'];
						$finish_total=$production_data_arr[$row[csf('po_id')]]['finish_pre']+$production_data_arr[$row[csf('po_id')]]['finish_qnty'];
						$reject_total=$production_data_arr[$row[csf('po_id')]]['reject_today']+$production_data_arr[$row[csf('po_id')]]['reject_pre'];
						$exfactory_total=$exfactory_data_arr[$row[csf('po_id')]]['ex_qnty']+$exfactory_data_arr[$row[csf('po_id')]]['exfac_pre'];
						
						//if($fabric_iss!=0 || ($cut_today!=0 || $embl_issue_today!=0 || $embl_rcv_today!=0 || $sewing_in_today!=0  || $sew_out_today!=0  || $iron_today!=0 || $exfactory_qty!=0  || $finish_today!=0))
						if($cut_today!=0 || $embl_issue_today!=0 || $embl_rcv_today!=0 || $sewing_in_today!=0  || $sew_out_today!=0  || $iron_today!=0 || $exfactory_qty!=0  || $finish_today!=0)
						{
							$possible_cut_pcs=0;
							$possible_cut_pcs=$fabric_iss/$result_consumtion[$row[csf('po_id')]];
							$grand_possible_cut_pcs+=$possible_cut_pcs;
							$grand_cut+=$cut_today;
							$grand_cut_total+=$cut_total;
							$grand_embl_issue+=$embl_issue_today;
							$grand_embl_iss_total+=$embl_issue_total;
							$grand_embl_rec+=$embl_rcv_today;
							$grand_embl_rev_total+=$embl_rcv_total;
							$grand_sew_in+=$sewing_in_today;
							$grand_sew_in_total+=$sewing_in_total;
							$grand_sew_out+=$sew_out_today;
							$grand_sew_out_total+=$sew_out_total;
							$grand_iron+=$iron_today;
							$grand_iron_total+=$iron_total;
							$grand_finish+=$finish_today;
							$grand_finish_total+=$finish_total;
							$grand_reject+=$reject_today;
							$grand_reject_total+=$reject_total;
							$grand_exfactory+=$exfactory_qty;
							$grand_exfa_total+=$exfactory_total;
							$grand_fabric_iss+=$fabric_iss;
							$grand_plan_cut+=$row[csf('plan_cut')];
							$posible_cut_pcs=$result_consumtion[$row[csf('po_id')]];
							$job_total+=$row[csf('po_quantity')];
							$txt_date=str_replace("'","",$txt_date_from);
							$diff=datediff("d",$txt_date,$row[csf('ship_date')]);
							
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                                <td width="40"><? echo $i;?></td>
                                <td width="100" align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                                <td width="150" align="center"><p><? echo $row[csf('style_ref_no')];?>&nbsp;</p></td>
                                <td width="60" align="center"><?  echo $row[csf('job_no_prefix_num')];?></td>
                                <td width="50" align="center"><?  echo $row[csf('year')];?></td>
                                <td width="200" align="center"><p><?  echo $row[csf('po_number')];?></p></td>
                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'order_quantity',1050,250)"><?  echo number_format($row[csf('po_quantity')]); ?></a></td>
                                <td width="100" align="center"><? echo change_date_format($row[csf('ship_date')]);?></td>
                                <td width="120" align="center"><p><? echo $shipment_status[$row[csf('status')]]; ?></p></td>
                                <td width="100" align="right"><? echo number_format($fabric_iss,2); ?></td>
                                <td width="100" align="right" title="Fabric Issue/Consumtion (<? echo number_format($result_consumtion[$row[csf('po_id')]],5); ?>)"><? echo number_format($possible_cut_pcs); ?></td>
                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'today_order_cutting',850,250)"><?  if($cut_today>0){ echo number_format($cut_today);} ?></a></td>
                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'today_order_cutting',850,250)"><?  if($cut_total>0){ echo number_format($cut_total);} ?></a></td>
                                <td width="100" align="right"><?  if(($row[csf('po_quantity')]-$cut_total)!=0){  echo number_format(($row[csf('po_quantity')]-$cut_total));} ?></td>
                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_issue_order',850,250)"><?  if($embl_issue_today>0){ echo number_format($embl_issue_today);} ?></a></td>
                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_issue_order',850,250)"><?   if($embl_issue_total>0){ echo number_format($embl_issue_total);} ?></a></td>
                                <td width="100" align="right"><?  if(($cut_total-$embl_issue_total)!=0){echo number_format(($cut_total-$embl_issue_total),0);} ?></td>
                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_receive_order',850,250)"><?   if($embl_rcv_today>0){echo number_format($embl_rcv_today);} ?></a></td>
                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_receive_order',850,250)"><?   if($embl_rcv_total>0){ echo number_format($embl_rcv_total);} ?></a></td>
                                <td width="100" align="right"><?  if(($embl_issue_total-$embl_rcv_total)!=0){ echo number_format(($embl_issue_total-$embl_rcv_total));} ?></td>
                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'sewing_input_order',850,250)"><?  if($sewing_in_today>0){  echo number_format($sewing_in_today);} ?></a></td>
                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'sewing_input_order',850,250)"><?   if($sewing_in_total>0){ echo number_format($sewing_in_total);} ?></a></td>
                                <td width="100" align="right"><?   if(($cut_total-$sewing_in_total)!=0){ echo number_format(($cut_total-$sewing_in_total),2);} ?></td>
                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'sewing_output_order',850,250)"><?  if($sew_out_today>0){ echo number_format($sew_out_today);} ?></a></td>
                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'sewing_output_order',850,250)"><?   if($sew_out_total>0){ echo number_format($sew_out_total);} ?></a></td>
                                <td width="100" align="right"><?  if(($sewing_in_total-$sew_out_total)!=0){ echo number_format(($sewing_in_total-$sew_out_total));} ?></td>
                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'iron__entry_order',850,300)"><?   if($iron_today>0){ echo number_format($iron_today);} ?></a></td>
                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'iron__entry_order',850,300)"><?   if($iron_total>0){ echo number_format($iron_total);} ?></a></td>
                                <td width="100" align="right"><?  if(($sew_out_total-$iron_total)!=0){ echo number_format(($sew_out_total-$iron_total),0);} ?></td>
                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'reject_qty_order',700,250)"><?  if($reject_today>0){  echo number_format($reject_today);} ?></a></td>
                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'reject_qty_order',700,250)"><?   if($reject_total>0){ echo number_format($reject_total);} ?></a></td>
                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'finish_qty_order',850,300)"><? if($finish_today>0){  echo number_format($finish_today);} ?></a></td>
                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'finish_qty_order',850,300)"><?   if($finish_total>0){ echo number_format($finish_total);} ?></a></td>
                                <td width="100" align="right"><?  if(($sew_out_total-$finish_total)!=0){   echo number_format(($sew_out_total-$finish_total));} ?></td>
                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'exfactrory__entry_order',850,250)"><?  if($exfactory_qty>0){ echo number_format($exfactory_qty);} ?></a></td>
                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'exfactrory__entry_order',850,250)"><?  if($exfactory_total>0){  echo number_format(($exfactory_total));} ?></a></td>
                                <td align="right"><?  if(($row[csf('po_quantity')]-$exfactory_total)!=0){ echo number_format(($row[csf('po_quantity')]-$exfactory_total));} ?></td>
							</tr>    
							<?
							$i++;
						}
                    }
                    ?>
                </table>     
                <table class="rpt_table" width="3750" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tfoot>
                        <tr>
                            <th width="40"><? // echo $i;?></th>
                            <th width="100"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></th>
                            <th width="150"><strong>Grand Total:</strong></th>
                            <th width="60"></th>
                            <th width="50"><? //echo $pro_date_sql_row[csf("po_number")];?></th>
                            <th width="160"><? //echo $pro_date_sql_row[csf("po_number")];?></th>
                            <th width="100" align="right" id="value_job_total"><?  echo number_format($job_total); ?></th>
                            <th width="100"></th>
                            <th width="120"></th>
                            <th width="100" id="value_fabric_issue"><? echo number_format($grand_fabric_iss); ?></th>
                            <th width="100" align="right" id="value_plan_cut"><? echo number_format($grand_possible_cut_pcs); ?></th>
                            <th width="100" align="right" id="value_cut_today"><?  echo number_format($grand_cut); ?></th>
                            <th width="100" align="right" id="value_cut_total"><?  echo number_format($grand_cut_total); ?></th>
                            <th width="100" align="right" id="value_cut_bal"><?  echo number_format(($job_total-$grand_cut_total)); ?></th>
                            <th width="100" align="right" id="value_embl_iss"><?  echo number_format($grand_embl_issue); ?></th>
                            <th width="100" align="right" id="value_embl_iss_total"><?  echo number_format($grand_embl_iss_total);?></th>
                            <th width="100" align="right" id="value_embl_iss_bal"><?  echo number_format(($grand_cut_total-$grand_embl_iss_total)); ?></th>
                            <th width="100" align="right" id="value_embl_rec"><?  echo number_format($grand_embl_rec); ?></th>
                            <th width="100" align="right" id="value_embl_rec_total"><?  echo number_format($grand_embl_rev_total); ?></th>
                            <th width="100" align="right" id="value_embl_rec_bal"><?  echo number_format(($grand_embl_iss_total-$grand_embl_rev_total)); ?></th>
                            <th width="100" align="right" id="value_sew_in"><?  echo number_format($grand_sew_in); ?></th>
                            <th width="100" align="right" id="value_sew_in_to"><?  echo number_format($grand_sew_in_total); ?></th>
                            <th width="100" align="right" id="value_sew_in_bal"><?  echo number_format(($grand_cut_total-$grand_sew_in_total)); ?></th>
                            <th width="100" align="right" id="value_sew_out"><?  echo number_format($grand_sew_out); ?></th>
                            <th width="100" align="right" id="value_sew_out_total"><?  echo number_format($grand_sew_out_total); ?></th>
                            <th width="100" align="right" id="value_sew_out_bal"><?  echo number_format(($grand_sew_in_total-$grand_sew_out_total)); ?></th>
                            <th width="100" align="right" id="value_iron"><?  echo number_format($grand_iron); ?></th>
                            <th width="100" align="right" id="value_iron_to"><?  echo number_format($grand_iron_total); ?></th>
                            <th width="100" align="right" id="value_iron_bal"><?  echo number_format($grand_sew_out_total-$grand_iron_total); ?></th>
                            <th width="100" align="right" id="value_reject"><?  echo number_format($grand_reject); ?></th>
                            <th width="100" align="right" id="value_reject_to"><?  echo number_format($grand_reject_total); ?></th>
                            <th width="100" align="right" id="value_finish"><?  echo number_format($grand_finish); ?></th>
                            <th width="100" align="right" id="value_finish_to"><?  echo number_format($grand_finish_total); ?></th>
                            <th width="100" align="right" id="value_finish_bal"><?  echo number_format(($grand_sew_out_total-$grand_finish_total));?></th>
                            <th width="100" align="right" id="value_exfactory"><?  echo number_format($grand_exfactory); ?></th>
                            <th width="100" align="right" id="value_exfactory_to"><?  echo number_format($grand_exfa_total); ?></th>
                            <th width="100" align="right" id="value_exfac_bal"><?  echo number_format(($job_total-$grand_exfa_total)); ?></th>
                        </tr> 
                    </tfoot>
                </table>        
			</div>
			</fieldset>
			<?	
		}
	 	else if(str_replace("'","",$cbo_search_by)==4)
		{ 
			?>
			<fieldset style="width:3590px;">
                <table width="1880"  cellspacing="0">
                    <tr class="form_caption" style="border:none;">
                    	<td colspan="29" align="center" style="border:none;font-size:14px; font-weight:bold" >Daily Order Wise Garments Production Status Report</td>
                    </tr>
                    <tr style="border:none;">
                        <td colspan="29" align="center" style="border:none; font-size:16px; font-weight:bold">
                        Company Name:<? 
						$com_arr=explode(",",str_replace("'","",$cbo_company_name));
						$comName="";
						foreach($com_arr as $comID)
						{
							$comName.=$company_library[$comID].',';
						}
						echo chop($comName,",");
						//echo $company_library[str_replace("'","",$cbo_company_name)]; 
						?>                                
                        </td>
                    </tr>
                    <tr style="border:none;">
                        <td colspan="29" align="center" style="border:none;font-size:12px; font-weight:bold">
                        <? echo "Date: ". str_replace("'","",$txt_date_from) ;?>
                        </td>
                    </tr>
                </table>
                <br />	
                <table cellspacing="0" cellpadding="0" border="1" rules="all"  width="3590" class="rpt_table">
                    <thead>
                        <tr >
                            <th width="40" rowspan="2">SL</th>
                            <th width="100" rowspan="2">Buyer</th>
                            <th width="100" rowspan="2">File No.</th>
                            <th width="100" rowspan="2">Int Ref. No.</th>
                            <th width="140" rowspan="2">Style</th>
                            <th width="60" rowspan="2">Job No</th>
                            <th width="50" rowspan="2">Year</th>
                            <th width="200" rowspan="2">Order No</th>
                            <th width="100" rowspan="2">Order Qty.</th>
                            <th width="50" rowspan="2">UOM</th>
                            <th width="100" rowspan="2">Order Qty Pcs</th>
                            <th width="100" rowspan="2">Ship Date</th>
                            <th width="120" rowspan="2">Shiping Status</th>
                            <th width="100" rowspan="2">Fin. Fab. Issued</th>
                            <th width="100" rowspan="2">Possible Cut Qty.</th>
                            <th width="240" colspan="3">Cutting</th>
                            <th width="240" colspan="3">EMBL Issue	</th>
                            <th width="240" colspan="3">EMBL Receive</th>
                            <th width="240" colspan="3">Sewing Input	</th>
                            <th width="240" colspan="3">Sewing Output</th>
                            <th width="240" colspan="3">Iron</th>
                            <th width="160" colspan="2">Sewing Reject</th>
                            <th width="240" colspan="3">Finish	</th>
                            <th colspan="3">Ex- Factory</th>
                        </tr>
                        <tr>
                            <th width="80">Today.</th>
                            <th width="80" >Total </th>
                            <th width="80" >WIP Bal. </th>
                            <th width="80" >Today</th>
                            <th width="80" >Total</th>
                            <th width="80" >Issue Bal.</th>
                            <th width="80" >Today </th>
                            <th width="80" >Total</th>
                            <th width="80" >WIP/ Bal</th>
                            <th width="80" > Today </th>
                            <th width="80" >Total </th>
                            <th width="80" >WIP/ Bal.</th>
                            <th width="80" >Today </th>
                            <th width="80" >Total </th>
                            <th width="80" >WIP/ Bal.</th>
                            <th width="80" >Today </th>
                            <th width="80" >Total </th>
                            <th width="80" >Iron Bal.</th>
                            <th width="80" >Today </th>
                            <th width="80" >Total</th>
                            <th width="80" >Today </th>
                            <th width="80" >Total</th>
                            <th width="80" >WIP/ Bal.</th>
                            <th width="80" >Today </th>
                            <th width="80" >Total</th>
                            <th  >Ex-fac. Bal.</th>
                        </tr>
                    </thead>
                </table>
                <?
                $production_data_arr=array();		
                $production_mst_sql= sql_select("SELECT po_break_down_id,
					sum(CASE WHEN production_type ='1' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty,
					sum(CASE WHEN production_type ='2' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS printing_qnty,
					sum(CASE WHEN production_type ='3' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS printreceived_qnty,
					sum(CASE WHEN production_type ='4' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewingin_qnty,
					sum(CASE WHEN production_type ='5' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewing_out_qnty,
					sum(CASE WHEN production_type ='7' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS iron_qnty,
					sum(CASE WHEN production_type ='8' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS finish_qnty,
					sum(CASE WHEN production_type ='5' and production_date=".$txt_date_from." THEN reject_qnty ELSE 0 END) AS reject_today,
					sum(CASE WHEN production_type ='1' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty_pre,
					sum(CASE WHEN production_type ='2' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS printing_qnty_pre,
					sum(CASE WHEN production_type ='3' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS printreceived_qnty_pre,
					sum(CASE WHEN production_type ='4' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewingin_qnty_pre,
					sum(CASE WHEN production_type ='5' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewing_out_pre,
					sum(CASE WHEN production_type ='7' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS iron_pre,
					sum(CASE WHEN production_type ='8' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS finish_pre,
					sum(CASE WHEN production_type ='5' and production_date<".$txt_date_from." THEN reject_qnty ELSE 0 END) AS reject_pre
					from 
					pro_garments_production_mst 
					where  
					is_deleted=0 and status_active=1 
					group by po_break_down_id "); //reject_qnty
				foreach($production_mst_sql as $val)
				{
					$production_data_arr[$val[csf('po_break_down_id')]]['cutting_qnty']=$val[csf('cutting_qnty')];
					$production_data_arr[$val[csf('po_break_down_id')]]['printing_qnty']=$val[csf('printing_qnty')];
					$production_data_arr[$val[csf('po_break_down_id')]]['printreceived_qnty']=$val[csf('printreceived_qnty')];
					$production_data_arr[$val[csf('po_break_down_id')]]['sewingin_qnty']=$val[csf('sewingin_qnty')];
					$production_data_arr[$val[csf('po_break_down_id')]]['sewing_out_qnty']=$val[csf('sewing_out_qnty')];
					$production_data_arr[$val[csf('po_break_down_id')]]['iron_qnty']=$val[csf('iron_qnty')];
					$production_data_arr[$val[csf('po_break_down_id')]]['finish_qnty']=$val[csf('finish_qnty')];
					$production_data_arr[$val[csf('po_break_down_id')]]['reject_today']=$val[csf('reject_today')];
					$production_data_arr[$val[csf('po_break_down_id')]]['cutting_qnty_pre']=$val[csf('cutting_qnty_pre')];
					$production_data_arr[$val[csf('po_break_down_id')]]['printing_qnty_pre']=$val[csf('printing_qnty_pre')];
					$production_data_arr[$val[csf('po_break_down_id')]]['printreceived_qnty_pre']=$val[csf('printreceived_qnty_pre')];
					$production_data_arr[$val[csf('po_break_down_id')]]['sewingin_qnty_pre']=$val[csf('sewingin_qnty_pre')];
					$production_data_arr[$val[csf('po_break_down_id')]]['sewing_out_pre']=$val[csf('sewing_out_pre')];
					$production_data_arr[$val[csf('po_break_down_id')]]['iron_pre']=$val[csf('iron_pre')];
					$production_data_arr[$val[csf('po_break_down_id')]]['finish_pre']=$val[csf('finish_pre')];
					$production_data_arr[$val[csf('po_break_down_id')]]['reject_pre']=$val[csf('reject_pre')];	
				}
				
				$result_consumtion=array();
                $sql_consumtiont_qty=sql_select(" select b.po_break_down_id, a.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs from wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id=b.pre_cost_fabric_cost_dtls_id   GROUP BY b.po_break_down_id, a.body_part_id");
				//echo  "select a.job_no, a.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs from wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id=b.pre_cost_fabric_cost_dtls_id   GROUP BY a.job_no, a.body_part_id";
				
				/*$sql_consumtiont_qty=sql_select(" select b.po_break_down_id, b.color_number_id, c.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs
                              from  wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c
                              where b.pre_cost_fabric_cost_dtls_id=c.id and b.pcs>0 group by b.po_break_down_id, b.color_number_id, c.body_part_id");*/
                
                foreach($sql_consumtiont_qty as $row_consum)
                {
					//$con_avg= $row_consum[csf("conjunction")];///str_replace("'","",$row_sew[csf("pcs")]);
					//$con_per_pcs=$con_avg/$row_consum[csf("pcs")];	
					$result_consumtion[$row_consum[csf('po_break_down_id')]]+=$row_consum[csf("requirment")]/$row_consum[csf("pcs")];
                }
				unset($sql_consumtiont_qty); 
                //var_dump($result_consumtion['FAL-15-00198']);die;
				$exfactory_sql=sql_select("SELECT po_break_down_id,
					sum(CASE WHEN  ex_factory_date=".$txt_date_from." AND entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN  ex_factory_date=".$txt_date_from." AND entry_form=85 THEN ex_factory_qnty ELSE 0 END) AS exfac_qty,
					sum(CASE WHEN  ex_factory_date<".$txt_date_from."  AND entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN  ex_factory_date<".$txt_date_from."  AND entry_form=85 THEN ex_factory_qnty ELSE 0 END) AS exfac_pre
					from 
					pro_ex_factory_mst 
					where  
					is_deleted=0 and status_active=1
					group by po_break_down_id ");
				$exfactory_data_arr=array();
				foreach($exfactory_sql as $value)
				{
					$exfactory_data_arr[$value[csf('po_break_down_id')]]['ex_qnty']=$value[csf('exfac_qty')];
					$exfactory_data_arr[$value[csf('po_break_down_id')]]['exfac_pre']=$value[csf('exfac_pre')];
				}
                $sql_fabric_qty=sql_select( "SELECT a.po_breakdown_id,
					sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =2  AND a.entry_form =18  THEN a.quantity ELSE 0 END ) AS fabric_qty,
					sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_qty,
					sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_qty
					FROM order_wise_pro_details a,inv_transaction b
					WHERE a.trans_id = b.id 
					and b.status_active=1 and a.entry_form in(18,15) and a.quantity!=0 and  b.is_deleted=0  group by a.po_breakdown_id");
                $fabric_data_arr=array();
                foreach($sql_fabric_qty as $inf)
                {
                	$fabric_data_arr[$inf[csf("po_breakdown_id")]]["fab_iss"]=$inf[csf("fabric_qty")]+$inf[csf("trans_in_qty")]-$inf[csf("trans_out_qty")];
                }	
                ?>
                <div style="width:3610px; max-height:425px; overflow-y:scroll"   id="scroll_body">
                    <table class="rpt_table" width="3590" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<?
						$file_cond="";
						if(str_replace("'","",$txt_file_no)!="") $file_cond=" and a.file_no=$txt_file_no";
						$file_cond2="";
						if(str_replace("'","",$txt_ref_no)!="") $file_cond2="and a.grouping=$txt_ref_no";
						if(str_replace("'","",$txt_style_ref)!="") $style=" and b.style_ref_no=$txt_style_ref"; else {$style="";}

                        if($db_type==0)
                        {
                         $sql=sql_select("select a.id as po_id,a.po_number,a.grouping,a.file_no,b.order_uom,b.total_set_qnty,b.company_name,b.job_no_prefix_num,b.buyer_name,b.style_ref_no,a.po_quantity,a.plan_cut as plan_cut,year(b.insert_date) as year,pub_shipment_date as ship_date,a.shiping_status as status 
							from  wo_po_break_down a,wo_po_details_master b 
							where a.job_no_mst=b.job_no $company_name $style $buyer_name $job_cond_id $style_cond $order_cond $po_order_cond $ship_status_cond $year_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 $file_cond $file_cond2  order by b.buyer_name,b.job_no_prefix_num");	
                        }
                        else if($db_type==2)
                        {
                       	 
						  $sql=sql_select("select a.id as po_id,a.po_number,a.grouping,a.file_no,b.order_uom,b.total_set_qnty,b.company_name,b.job_no_prefix_num,b.buyer_name,b.style_ref_no,a.po_quantity,a.plan_cut as plan_cut,extract(year from b.insert_date) as year,pub_shipment_date as ship_date,a.shiping_status as status 
						 from  wo_po_break_down a,wo_po_details_master b 
						 where a.job_no_mst=b.job_no $company_name $style $buyer_name $job_cond_id $style_cond $order_cond  $po_order_cond $ship_status_cond $year_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 $file_cond $file_cond2 order by b.buyer_name,b.job_no_prefix_num");
                        }
						//die;
                        $grand_fabric_iss=$grand_embl_rec=$grand_embl_rev_total=$grand_plan_cut=$job_total=$exfactory_total=0;
                        $cut_today=$embl_issue_today=$embl_rcv_today=$sewing_in_today=$sew_out_today=$iron_today=$finish_today=$reject_today=$exfactory_qty=0;
                        $fabric_iss=$cut_total=$embl_issue_total=$embl_rcv_total=$sewing_in_total=$iron_total=$sew_out_total=$finish_total=$reject_total=0;
                        $grand_cut=$grand_cut_total=$grand_embl_issue=$grand_embl_iss_total=$grand_sew_in=$grand_sew_in_total=$grand_sew_out=$grand_sew_out_total=0;
                        $grand_iron=$grand_iron_total=$grand_finish=$grand_finish_total=$grand_reject=$grand_reject_total=$grand_exfactory=$grand_exfa_total=0; 
                        $tot_rows=count($sql);
                        $i=1;		
                        foreach($sql as $row)	
                        {
							$cut_today=$production_data_arr[$row[csf('po_id')]]['cutting_qnty'];
							$embl_issue_today=$production_data_arr[$row[csf('po_id')]]['printing_qnty'];
							$embl_rcv_today=$production_data_arr[$row[csf('po_id')]]['printreceived_qnty'];
							$sewing_in_today=$production_data_arr[$row[csf('po_id')]]['sewingin_qnty'];
							$sew_out_today=$production_data_arr[$row[csf('po_id')]]['sewing_out_qnty'];
							$iron_today=$production_data_arr[$row[csf('po_id')]]['iron_qnty'];
							$finish_today=$production_data_arr[$row[csf('po_id')]]['finish_qnty'];
							$reject_today=$production_data_arr[$row[csf('po_id')]]['reject_today'];
							$exfactory_qty=$exfactory_data_arr[$row[csf('po_id')]]['ex_qnty'];
							$fabric_iss=$fabric_data_arr[$row[csf('po_id')]]['fab_iss'];
							
							$cut_total=$production_data_arr[$row[csf('po_id')]]['cutting_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['cutting_qnty'];
							$embl_issue_total=$production_data_arr[$row[csf('po_id')]]['printing_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['printing_qnty'];
							$embl_rcv_total=$production_data_arr[$row[csf('po_id')]]['printreceived_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['printreceived_qnty'];
							$sewing_in_total=$production_data_arr[$row[csf('po_id')]]['sewingin_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['sewingin_qnty'];
							$iron_total=$production_data_arr[$row[csf('po_id')]]['iron_pre']+$production_data_arr[$row[csf('po_id')]]['iron_qnty'];
							$sew_out_total=$production_data_arr[$row[csf('po_id')]]['sewing_out_pre']+$production_data_arr[$row[csf('po_id')]]['sewing_out_qnty'];
							$finish_total=$production_data_arr[$row[csf('po_id')]]['finish_pre']+$production_data_arr[$row[csf('po_id')]]['finish_qnty'];
							$reject_total=$production_data_arr[$row[csf('po_id')]]['reject_today']+$production_data_arr[$row[csf('po_id')]]['reject_pre'];
							$exfactory_total=$exfactory_data_arr[$row[csf('po_id')]]['ex_qnty']+$exfactory_data_arr[$row[csf('po_id')]]['exfac_pre'];
							//if($fabric_iss!=0 || ($cut_today!=0 || $embl_issue_today!=0 || $embl_rcv_today!=0 || $sewing_in_today!=0  || $sew_out_today!=0  || $iron_today!=0 || $exfactory_qty!=0  || $finish_today!=0))
							if($cut_today!=0 || $embl_issue_today!=0 || $embl_rcv_today!=0 || $sewing_in_today!=0  || $sew_out_today!=0  || $iron_today!=0 || $exfactory_qty!=0  || $finish_today!=0)
							{
								$possible_cut_pcs=0;
								$possible_cut_pcs=$fabric_iss/$result_consumtion[$row[csf('po_id')]];
								$grand_possible_cut_pcs+=$possible_cut_pcs;
								$grand_cut+=$cut_today;
								$grand_cut_total+=$cut_total;
								$grand_embl_issue+=$embl_issue_today;
								$grand_embl_iss_total+=$embl_issue_total;
								$grand_embl_rec+=$embl_rcv_today;
								$grand_embl_rev_total+=$embl_rcv_total;
								$grand_sew_in+=$sewing_in_today;
								$grand_sew_in_total+=$sewing_in_total;
								$grand_sew_out+=$sew_out_today;
								$grand_sew_out_total+=$sew_out_total;
								$grand_iron+=$iron_today;
								$grand_iron_total+=$iron_total;
								$grand_finish+=$finish_today;
								$grand_finish_total+=$finish_total;
								$grand_reject+=$reject_today;
								$grand_reject_total+=$reject_total;
								$grand_exfactory+=$exfactory_qty;
								$grand_exfa_total+=$exfactory_total;
								$grand_fabric_iss+=$fabric_iss;
								$grand_plan_cut+=$row[csf('plan_cut')];
								$job_total+=$row[csf('po_quantity')];
								$txt_date=str_replace("'","",$txt_date_from);
								$diff=datediff("d",$txt_date,$row[csf('ship_date')]);
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                                    <td width="40"><? echo $i;?></td>
                                    <td width="100" align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                                    <td width="100" align="center"><? echo $row[csf('file_no')]; ?></td>
                                    <td width="100" align="center"><? echo $row[csf('grouping')]; ?></td>
                                    <td width="140" align="center"><p><? echo $row[csf('style_ref_no')];?>&nbsp;</p></td>
                                    <td width="60" align="center"><?  echo $row[csf('job_no_prefix_num')];?></td>
                                    <td width="50" align="center"><?  echo $row[csf('year')];?></td>
                                    <td width="200" align="center"><p><?  echo $row[csf('po_number')];?></p></td>
                                    <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'order_quantity',800,250)"><? echo number_format($row[csf('po_quantity')]);?></a></td>
                                    <td width="50" align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]];?></td>
                                    <td width="100" align="right">
										<?
											$order_qnty_pcs = $row[csf('total_set_qnty')]*$row[csf('po_quantity')];
											echo number_format($order_qnty_pcs);
										?>
									</td>
                                    <td width="100" align="center"><? echo change_date_format($row[csf('ship_date')]);?></td>
                                    <td width="120" align="center"><p><? echo $shipment_status[$row[csf('status')]]; ?></p></td>
                                    <td width="100" align="right"><? echo number_format($fabric_iss,2); ?></td>
                                    <td width="100" align="right" title="Fabric Issue/Consumtion (<? echo number_format($result_consumtion[$row[csf('po_id')]],5); ?>)"><?  echo number_format($possible_cut_pcs); ?></td>
                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'today_order_cutting',850,250)"><? if($cut_today>0){echo number_format($cut_today);}   ?></a></td>
                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'today_order_cutting',850,250)"><? if($cut_total>0){echo number_format($cut_total);}?></a></td>
                                    <td width="80" align="right">
										<?
											if(($row[csf('po_quantity')]-$cut_total)>0 || ($row[csf('po_quantity')]-$cut_total)<0)
											{
												echo number_format($order_qnty_pcs-$cut_total);
											}
										?>
									</td>
                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_issue_order',850,250)"><?   if($embl_issue_today>0){echo number_format($embl_issue_today);}?></a></td>
                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_issue_order',850,250)"><?   if($embl_issue_total>0){echo number_format($embl_issue_total);} ?></a></td>
                                    <td width="80" align="right"><?   if(($cut_total-$embl_issue_total)>0 || ($cut_total-$embl_issue_total)<0){echo number_format(($cut_total-$embl_issue_total));} ?></td>
                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_receive_order',850,250)"><?  if($embl_rcv_today>0){ echo number_format($embl_rcv_today);} ?></a></td>
                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_receive_order',850,250)"><?   if($embl_rcv_total>0){echo number_format($embl_rcv_total);} ?></a></td>
                                    <td width="80" align="right"><?   if(($embl_issue_total-$embl_rcv_total)>0 || ($embl_issue_total-$embl_rcv_total)<0){ echo number_format(($embl_issue_total-$embl_rcv_total));} ?></td>
                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'sewing_input_order',850,250)"><?   if($sewing_in_today>0){echo number_format($sewing_in_today);} ?></a></td>
                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'sewing_input_order',850,250)"><?   if($sewing_in_total>0){echo number_format($sewing_in_total);} ?></a></td>
                                    <td width="80" align="right"><?    if(($cut_total-$sewing_in_total)>0 || ($cut_total-$sewing_in_total)<0){echo number_format(($cut_total-$sewing_in_total));} ?></td>
                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'sewing_output_order',850,250)"><?   if($sew_out_today>0){echo number_format($sew_out_today);} ?></a></td>
                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'sewing_output_order',850,250)"><?  if($sew_out_total>0){ echo number_format($sew_out_total);} ?></a></td>
                                    <td width="80" align="right"><?  if(($sewing_in_total-$sew_out_total)>0 || ($sewing_in_total-$sew_out_total)<0){ echo number_format(($sewing_in_total-$sew_out_total));} ?></td>
                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'iron__entry_order',850,300)"><?  if($iron_today>0){ echo number_format($iron_today);} ?></a></td>
                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'iron__entry_order',850,300)"><?  if($iron_total>0){  echo number_format($iron_total);} ?></a></td>
                                    <td width="80" align="right"><?   if(($sew_out_total-$iron_total)>0 || ($sew_out_total-$iron_total)<0){ echo number_format(($sew_out_total-$iron_total));} ?></td>
                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'reject_qty_order',700,250)"><?  if($reject_today>0){ echo number_format($reject_today);} ?></a></td>
                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'reject_qty_order',700,250)"><?  if($reject_total>0){  echo number_format($reject_total);} ?></a></td>
                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'finish_qty_order',850,300)"><?  if($finish_today>0){  echo number_format($finish_today);} ?></a></td>
                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'finish_qty_order',850,300)"><?  if($finish_total>0){  echo number_format($finish_total);} ?></a></td>
                                    <td width="80" align="right"><?  if(($sew_out_total-$finish_total)>0 || ($sew_out_total-$finish_total)<0){ echo number_format(($sew_out_total-$finish_total));} ?></td>
                                   
                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'exfactrory__entry_order',850,250)"><?  if($exfactory_qty>0){ echo number_format($exfactory_qty);} ?></a></td>
                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'exfactrory__entry_order',850,250)"><?  if($exfactory_total>0){  echo number_format(($exfactory_total));} ?></a></td>
                                    <td  align="right" >
										<? 
											if(($row[csf('po_quantity')]-$exfactory_total)>0 || ($row[csf('po_quantity')]-$exfactory_total)<0)
											{
												echo number_format($order_qnty_pcs-$exfactory_total);
											} 
										?>
									</td>
								</tr>    
								<?
								$i++;
							}
                        }
                        ?>
                        
                 </table>
                 </div>       
                <table class="rpt_table" width="3590" cellpadding="0" cellspacing="0" border="1" rules="all">
                        <tfoot>
                            <tr>
                                <th width="40"><? // echo $i;?></th>
                                <th width="100"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></th>
                                <th width="100"></th>
                                <th width="100"></th>
                                <th width="140"><strong>Grand Total:</strong></td>
                                <th width="60"></th>
                                <th width="50"><? //echo $pro_date_sql_row[csf("po_number")];?></th>
                                <th width="200"><? //echo $pro_date_sql_row[csf("po_number")];?></th>
                                <th width="100" align="right" id="value_job_total"><?  echo number_format($job_total); ?></th>
                                <th width="50"></th>
                                <th width="100"></th>
                                <th width="100"> </th>
                                <th width="120" id="value_fabric_issues"><? //echo number_format($grand_fabric_iss); ?></th>
                                <th width="100" align="right" id="value_plan_cut_"><? //echo number_format($grand_possible_cut_pcs); ?></th>
                                <th width="80"></th>
                                <th width="100" align="right" id="value_cut_today"><?  echo number_format($grand_cut); ?></th>
                                <th width="80" align="right" id="value_cut_total"><?  echo number_format($grand_cut_total); ?></th>
                                <th width="80" align="right" id="value_cut_bal"><?  echo number_format(($job_total-$grand_cut_total)); ?></th>
                                <th width="80" align="right" id="value_embl_iss"><?  echo number_format($grand_embl_issue); ?></th>
                                <th width="80" align="right" id="value_embl_iss_total"><?  echo number_format($grand_embl_iss_total);?></th>
                                <th width="80" align="right" id="value_embl_iss_bal"><?  echo number_format(($grand_cut_total-$grand_embl_iss_total)); ?></th>
                                <th width="80" align="right" id="value_embl_rec"><?  echo number_format($grand_embl_rec); ?></th>
                                <th width="80" align="right" id="value_embl_rec_total"><?  echo number_format($grand_embl_rev_total); ?></th>
                                <th width="80" align="right" id="value_embl_rec_bal"><?  echo number_format(($grand_embl_iss_total-$grand_embl_rev_total)); ?></th>
                                <th width="80" align="right" id="value_sew_in"><?  echo number_format($grand_sew_in); ?></th>
                                <th width="80" align="right" id="value_sew_in_to"><?  echo number_format($grand_sew_in_total); ?></th>
                                <th width="80" align="right" id="value_sew_in_bal"><?  echo number_format(($grand_cut_total-$grand_sew_in_total)); ?></th>
                                <th width="80" align="right" id="value_sew_out"><?  echo number_format($grand_sew_out); ?></th>
                                <th width="80" align="right" id="value_sew_out_total"><?  echo number_format($grand_sew_out_total); ?></th>
                                <th width="80" align="right" id="value_sew_out_bal"><?  echo number_format(($grand_sew_in_total-$grand_sew_out_total)); ?></th>
                                <th width="80" align="right" id="value_iron"><?  echo number_format($grand_iron); ?></th>
                                <th width="80" align="right" id="value_iron_to"><?  echo number_format($grand_iron_total); ?></th>
                                <th width="80" align="right" id="value_iron_bal"><?  echo number_format($grand_sew_out_total-$grand_iron_total); ?></th>
                                <th width="80" align="right" id="value_reject"><?  echo number_format($grand_reject); ?></th>
                                <th width="80" align="right" id="value_reject_to"><?  echo number_format($grand_reject_total); ?></th>
                                <th width="80" align="right" id="value_finish"><?  echo number_format($grand_finish); ?></th>
                                <th width="80" align="right" id="value_finish_to"><?  echo number_format($grand_finish_total); ?></th>
                                <th width="80" align="right" id="value_finish_bal"><?  echo number_format(($grand_sew_out_total-$grand_finish_total));?></th>
                                <th width="80" align="right" id="value_exfactory"><?  echo number_format($grand_exfactory); ?></th>
                                <th width="80" align="right" id="value_exfactory_to"><?  echo number_format($grand_exfa_total); ?></th>
                                <th  align="right" id="value_exfac_bal"><?  echo number_format(($job_total-$grand_exfa_total)); ?></th>
                            </tr> 
                            <tr>
                            	<th colspan=""></th>
                            </tr>
                        </tfoot>   
                    </table>     
			</fieldset>
			<?	
		}
	}
	else if(str_replace("'","",$report_type)==5)
	{
			if(str_replace("'","",$cbo_ship_status)==0 || str_replace("'","",$cbo_ship_status)==1 || str_replace("'","",$cbo_ship_status)==2)
			{
				if(str_replace("'","",$cbo_search_by)==1 || str_replace("'","",$cbo_search_by)==3)
				{
					?>
					<fieldset style="width:3800px;">
		                <table width="1880"  cellspacing="0"   >
		                    <tr class="form_caption" style="border:none;">
		                    	<td colspan="29" align="center" style="border:none;font-size:14px; font-weight:bold" >Daily Style Wise Garments Production Status Report</td>
		                    </tr>
		                    <tr style="border:none;">
		                        <td colspan="29" align="center" style="border:none; font-size:16px; font-weight:bold">
		                        Company Name:<? 
									$com_arr=explode(",",str_replace("'","",$cbo_company_name));
									$comName="";
									foreach($com_arr as $comID)
									{
										$comName.=$company_library[$comID].',';
									}
									echo chop($comName,",");
									//echo $company_library[str_replace("'","",$cbo_company_name)]; 
								?>                                
		                        </td>
		                    </tr>
		                    <tr style="border:none;">
		                        <td colspan="29" align="center" style="border:none;font-size:12px; font-weight:bold">
		                        <? echo "Date: ". str_replace("'","",$txt_date_from) ;?>
		                        </td>
		                    </tr>
		                </table>
		                <br />	
		                
		                <?
		                $result_consumtion=array();
		                $sql_consumtiont_qty=sql_select(" select a.job_no, a.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs from wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id=b.pre_cost_fabric_cost_dtls_id   GROUP BY a.job_no, a.body_part_id");
						//echo  "select a.job_no, a.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs from wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id=b.pre_cost_fabric_cost_dtls_id   GROUP BY a.job_no, a.body_part_id";
						
						/*$sql_consumtiont_qty=sql_select(" select b.po_break_down_id, b.color_number_id, c.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs
		                              from  wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c
		                              where b.pre_cost_fabric_cost_dtls_id=c.id and b.pcs>0 group by b.po_break_down_id, b.color_number_id, c.body_part_id");*/
		                
		                foreach($sql_consumtiont_qty as $row_consum)
		                {
							//$con_avg= $row_consum[csf("conjunction")];///str_replace("'","",$row_sew[csf("pcs")]);
							//$con_per_pcs=$con_avg/$row_consum[csf("pcs")];	
							$result_consumtion[$row_consum[csf('job_no')]]+=$row_consum[csf("requirment")]/$row_consum[csf("pcs")];
		                }
						unset($sql_consumtiont_qty); 
		                //var_dump($result_consumtion['FAL-15-00198']);die;
		                
		                $production_data_arr=array();
						/*if (str_replace("'","",$txt_po_no)=="")
						{ */ 		
		                $production_mst_sql= sql_select("SELECT po_break_down_id,
							sum(CASE WHEN production_type ='1' THEN production_quantity ELSE 0 END) AS all_cutting_qnty,
							sum(CASE WHEN production_type ='2' THEN production_quantity ELSE 0 END) AS all_printing_qnty,
							sum(CASE WHEN production_type ='3' THEN production_quantity ELSE 0 END) AS all_printreceived_qnty,
							sum(CASE WHEN production_type ='4' THEN production_quantity ELSE 0 END) AS all_sewingin_qnty,
							sum(CASE WHEN production_type ='5' THEN production_quantity ELSE 0 END) AS all_sewing_out_qnty,
							sum(CASE WHEN production_type ='7' THEN production_quantity ELSE 0 END) AS all_iron_qnty,
							sum(CASE WHEN production_type ='7' THEN re_production_qty ELSE 0 END) AS all_re_iron_qnty,
							sum(CASE WHEN production_type ='5' THEN reject_qnty ELSE 0 END) AS all_reject_today,
							sum(CASE WHEN production_type ='8' THEN production_quantity ELSE 0 END) AS all_finish_qnty,
							
							sum(CASE WHEN production_type ='1' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty,
							sum(CASE WHEN production_type ='2' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS printing_qnty,
							sum(CASE WHEN production_type ='3' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS printreceived_qnty,
							sum(CASE WHEN production_type ='4' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewingin_qnty,
							sum(CASE WHEN production_type ='5' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewing_out_qnty,
							sum(CASE WHEN production_type ='7' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS iron_qnty,
							sum(CASE WHEN production_type ='7' and production_date=".$txt_date_from." THEN re_production_qty ELSE 0 END) AS re_iron_qnty,
							sum(CASE WHEN production_type ='5' and production_date=".$txt_date_from." THEN reject_qnty ELSE 0 END) AS reject_today,
							sum(CASE WHEN production_type ='8' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS finish_qnty,
							
							sum(CASE WHEN production_type ='1' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty_pre,
							sum(CASE WHEN production_type ='2' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS printing_qnty_pre,
							sum(CASE WHEN production_type ='3' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS printreceived_qnty_pre,
							sum(CASE WHEN production_type ='4' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewingin_qnty_pre,
							sum(CASE WHEN production_type ='5' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewing_out_pre,
							sum(CASE WHEN production_type ='7' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS iron_pre,
							sum(CASE WHEN production_type ='7' and production_date<".$txt_date_from." THEN re_production_qty ELSE 0 END) AS re_iron_pre,
							sum(CASE WHEN production_type ='8' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS finish_pre,
							sum(CASE WHEN production_type ='5' and production_date<".$txt_date_from." THEN reject_qnty ELSE 0 END) AS reject_pre
							from 
							pro_garments_production_mst 
							where  
							is_deleted=0 and status_active=1
							group by po_break_down_id "); //reject_qnty
							
						//}
						
		                
		                foreach($production_mst_sql as $val)
		                {
							
							$production_data_arr[$val[csf('po_break_down_id')]]['all_cutting_qnty']=$val[csf('all_cutting_qnty')];
							$production_data_arr[$val[csf('po_break_down_id')]]['all_printing_qnty']=$val[csf('all_printing_qnty')];
							$production_data_arr[$val[csf('po_break_down_id')]]['all_printreceived_qnty']=$val[csf('all_printreceived_qnty')];
							$production_data_arr[$val[csf('po_break_down_id')]]['all_sewingin_qnty']=$val[csf('all_sewingin_qnty')];
							$production_data_arr[$val[csf('po_break_down_id')]]['all_sewing_out_qnty']=$val[csf('all_sewing_out_qnty')];
							$production_data_arr[$val[csf('po_break_down_id')]]['all_iron_qnty']=$val[csf('all_iron_qnty')];
							$production_data_arr[$val[csf('po_break_down_id')]]['all_finish_qnty']=$val[csf('all_finish_qnty')];
							$production_data_arr[$val[csf('po_break_down_id')]]['all_reject_today']=$val[csf('all_reject_today')];
							$production_data_arr[$val[csf('po_break_down_id')]]['all_re_iron_qnty']=$val[csf('all_re_iron_qnty')];	
							
							$production_data_arr[$val[csf('po_break_down_id')]]['cutting_qnty']=$val[csf('cutting_qnty')];
							$production_data_arr[$val[csf('po_break_down_id')]]['printing_qnty']=$val[csf('printing_qnty')];
							$production_data_arr[$val[csf('po_break_down_id')]]['printreceived_qnty']=$val[csf('printreceived_qnty')];
							$production_data_arr[$val[csf('po_break_down_id')]]['sewingin_qnty']=$val[csf('sewingin_qnty')];
							$production_data_arr[$val[csf('po_break_down_id')]]['sewing_out_qnty']=$val[csf('sewing_out_qnty')];
							$production_data_arr[$val[csf('po_break_down_id')]]['iron_qnty']=$val[csf('iron_qnty')];
							$production_data_arr[$val[csf('po_break_down_id')]]['finish_qnty']=$val[csf('finish_qnty')];
							$production_data_arr[$val[csf('po_break_down_id')]]['reject_today']=$val[csf('reject_today')];
							$production_data_arr[$val[csf('po_break_down_id')]]['cutting_qnty_pre']=$val[csf('cutting_qnty_pre')];
							$production_data_arr[$val[csf('po_break_down_id')]]['printing_qnty_pre']=$val[csf('printing_qnty_pre')];
							$production_data_arr[$val[csf('po_break_down_id')]]['printreceived_qnty_pre']=$val[csf('printreceived_qnty_pre')];
							$production_data_arr[$val[csf('po_break_down_id')]]['sewingin_qnty_pre']=$val[csf('sewingin_qnty_pre')];
							$production_data_arr[$val[csf('po_break_down_id')]]['sewing_out_pre']=$val[csf('sewing_out_pre')];
							$production_data_arr[$val[csf('po_break_down_id')]]['iron_pre']=$val[csf('iron_pre')];
							$production_data_arr[$val[csf('po_break_down_id')]]['finish_pre']=$val[csf('finish_pre')];
							$production_data_arr[$val[csf('po_break_down_id')]]['reject_pre']=$val[csf('reject_pre')];
							$production_data_arr[$val[csf('po_break_down_id')]]['re_iron_qnty']=$val[csf('re_iron_qnty')];	
							$production_data_arr[$val[csf('po_break_down_id')]]['re_iron_pre']=$val[csf('re_iron_pre')];		
		                }
						unset($production_mst_sql); 
		                //print_r($production_data_arr[2961]);die;
		                $exfactory_sql=sql_select("SELECT po_break_down_id,
							sum(CASE WHEN  ex_factory_date=".$txt_date_from." AND entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN  ex_factory_date=".$txt_date_from." AND entry_form=85 THEN ex_factory_qnty ELSE 0 END) AS exfac_qty,sum(CASE WHEN  entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) AS all_exfac_qty,
							sum(CASE WHEN  ex_factory_date<".$txt_date_from." AND entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN  ex_factory_date<".$txt_date_from." AND entry_form=85 THEN ex_factory_qnty ELSE 0 END) AS exfac_pre
							from 
							pro_ex_factory_mst 
							where  
							is_deleted=0 and status_active=1
							group by po_break_down_id ");
		                $exfactory_data_arr=array();
		                foreach($exfactory_sql as $value)
		                {
							$exfactory_data_arr[$value[csf('po_break_down_id')]]['all_ex_qnty']=$value[csf('all_exfac_qty')];
							$exfactory_data_arr[$value[csf('po_break_down_id')]]['ex_qnty']=$value[csf('exfac_qty')];
							$exfactory_data_arr[$value[csf('po_break_down_id')]]['exfac_pre']=$value[csf('exfac_pre')];
		                }
						unset($exfactory_sql); 
		                $sql_fabric_qty=sql_select( "SELECT a.po_breakdown_id,
							sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =2  AND a.entry_form =18  THEN a.quantity ELSE 0 END ) AS fabric_qty,
							sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_qty,
							sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_qty
							FROM order_wise_pro_details a,inv_transaction b
							WHERE a.trans_id = b.id 
							and b.status_active=1 and a.entry_form in(18,15) and a.quantity!=0 and  b.is_deleted=0  group by a.po_breakdown_id");
		                $fabric_data_arr=array();
		                foreach($sql_fabric_qty as $inf)
		                {
		                	$fabric_data_arr[$inf[csf("po_breakdown_id")]]["fab_iss"]=$inf[csf("fabric_qty")]+$inf[csf("trans_in_qty")]-$inf[csf("trans_out_qty")];
		                }
						unset($sql_fabric_qty); 	
		                ?>
		               
								<?
		                        if($db_type==0)
		                        {
									$sql_data="select b.job_no,b.job_no_prefix_num,b.company_name,b.buyer_name,b.style_ref_no,
										sum(a.po_quantity) as job_quantity ,group_concat(a.id) as po_id,sum(a.plan_cut) as plan_cut,
										year(b.insert_date) as year,max(pub_shipment_date) as ship_date,group_concat(a.shiping_status) as status 
										from  wo_po_break_down a,wo_po_details_master b
										where a.job_no_mst=b.job_no  $company_name $buyer_name $job_cond_id $style_cond $order_cond $po_order_cond $ship_status_cond $year_cond and b.status_active=1 and                               b.is_deleted=0 and a.status_active=1   
										group by b.company_name,b.job_no_prefix_num,b.job_no,b.insert_date order by b.buyer_name,b.job_no_prefix_num";
									$sql=sql_select($sql_data);	
		                        }
		                        else if($db_type==2)
		                        {
									$sql_data="select b.job_no, b.job_no_prefix_num, b.company_name, b.buyer_name, b.style_ref_no, sum(a.po_quantity) as job_quantity, 
										listagg(a.id,',') within group (order by a.id) as po_id,
										sum(a.plan_cut) as plan_cut, extract(year from b.insert_date) as year,
										max(pub_shipment_date) as ship_date, 
										listagg(a.shiping_status,',') within group (order by a.shiping_status) as status
										from wo_po_break_down a,wo_po_details_master b
										where a.job_no_mst=b.job_no $company_name $buyer_name $job_cond_id $style_cond $order_cond $po_order_cond $ship_status_cond $year_cond 
										and b.status_active=1 and b.is_deleted=0 and a.status_active=1 
										group by b.company_name, b.job_no, b.job_no_prefix_num, b.buyer_name, b.style_ref_no, b.insert_date order by b.buyer_name, b.job_no_prefix_num";
									$sql=sql_select($sql_data);
											
		                        }
								?>
		                        
								  <table cellspacing="0" cellpadding="0" border="1" rules="all"  width="2390" class="rpt_table">
				                	<p style=" width:300px; font-weight:bold;">Buyer Wise Summary Part</p>
				                    <thead>
				                        <tr >
				                            <th width="40" rowspan="2">SL</th>
		                                    <th width="100" rowspan="2">Company</th>
				                            <th width="100" rowspan="2">Buyer</th>
				                           
				                            <th width="240" colspan="3">Cutting</th>
				                            <th width="240" colspan="3">EMBL Issue	</th>
				                            <th width="240" colspan="3">EMBL Receive</th>
				                            <th width="240" colspan="3">Sewing Input	</th>
				                            <th width="240" colspan="3">Sewing Output</th>
				                            <th width="240" colspan="3">Iron</th>
				                            <th width="160" colspan="2">Sewing Reject</th>
				                            <th width="240" colspan="3">Finish	</th>
				                            <th colspan="3">Ex- Factory</th>
				                        </tr>
				                        <tr>
				                            <th width="80">Today.</th>
				                            <th width="80" >Total </th>
				                            <th width="80" >WIP Bal. </th>
				                            <th width="80" >Today</th>
				                            <th width="80" >Total</th>
				                            <th width="80" >Issue Bal.</th>
				                            <th width="80" >Today </th>
				                            <th width="80" >Total</th>
				                            <th width="80" >WIP/ Bal</th>
				                            <th width="80" > Today </th>
				                            <th width="80" >Total </th>
				                            <th width="80" >WIP/ Bal.</th>
				                            <th width="80" >Today </th>
				                            <th width="80" >Total </th>
				                            <th width="80" >WIP/ Bal.</th>
				                            <th width="80" >Today </th>
				                            <th width="80" >Total </th>
				                            <th width="80" >Iron Bal.</th>
				                            <th width="80" >Today </th>
				                            <th width="80" >Total</th>
				                            <th width="80" >Today </th>
				                            <th width="80" >Total</th>
				                            <th width="80" >WIP/ Bal.</th>
				                            <th width="80" >Today </th>
				                            <th width="80" >Total</th>
				                            <th  >Ex-fac. Bal.</th>
				                        </tr>
				                    </thead>
				                </table>
		               			<div style="width:2410px; max-height:425px; overflow-y:scroll"   id="scroll_body_summary">
				                    <table class="rpt_table" width="2390" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_summary">
		                            <? 
									
									$summeryDataArrPoQty=array(); 
		                            foreach($sql as $row)	
			                        {
										
										$po_id_all=explode(',',$row[csf('po_id')]);
										$k=0;
										foreach( $po_id_all as $val_a)
										{
											
				  							if($production_data_arr[$val_a]['all_cutting_qnty']!=0 || $production_data_arr[$val_a]['all_printing_qnty']!=0 || $production_data_arr[$val_a]['all_printreceived_qnty']!=0 || $production_data_arr[$val_a]['all_sewingin_qnty']!=0  || $production_data_arr[$val_a]['all_sewing_out_qnty']!=0  || $production_data_arr[$val_a]['all_iron_qnty']!=0 || $exfactory_data_arr[$val_a]['all_ex_qnty']!=0  || $production_data_arr[$val_a]['all_finish_qnty']!=0)
											{
											
											$key=$row[csf('company_name')].$row[csf('buyer_name')];
											$summeryDataArr[$key]=$row;
											$summeryDataArrPoQty[$key]+=$row[csf('job_quantity')];
											
											
											$all_cut_todayArr[$key]+=$production_data_arr[$val_a]['all_cutting_qnty'];
											$all_embl_issue_todayArr[$key]+=$production_data_arr[$val_a]['all_printing_qnty'];
											$all_embl_rcv_todayArr[$key]+=$production_data_arr[$val_a]['all_printreceived_qnty'];
											$all_sewing_in_todayArr[$key]+=$production_data_arr[$val_a]['all_sewingin_qnty'];
											$all_sew_out_todayArr[$key]+=$production_data_arr[$val_a]['all_sewing_out_qnty'];
											$all_iron_todayArr[$key]+=$production_data_arr[$val_a]['all_iron_qnty'];
											$all_finish_todayArr[$key]+=$production_data_arr[$val_a]['all_finish_qnty'];
											$all_reject_todayArr[$key]+=$production_data_arr[$val_a]['all_reject_today'];
											$all_exfactory_qtyArr[$key]+=$exfactory_data_arr[$val_a]['all_ex_qnty'];
											
											$cut_todayArr[$key]+=$production_data_arr[$val_a]['cutting_qnty'];
											$embl_issue_todayArr[$key]+=$production_data_arr[$val_a]['printing_qnty'];
											$embl_rcv_todayArr[$key]+=$production_data_arr[$val_a]['printreceived_qnty'];
											$sewing_in_todayArr[$key]+=$production_data_arr[$val_a]['sewingin_qnty'];
											$sew_out_todayArr[$key]+=$production_data_arr[$val_a]['sewing_out_qnty'];
											$iron_todayArr[$key]+=$production_data_arr[$val_a]['iron_qnty'];
											$finish_todayArr[$key]+=$production_data_arr[$val_a]['finish_qnty'];
											$reject_todayArr[$key]+=$production_data_arr[$val_a]['reject_today'];
											$exfactory_qtyArr[$key]+=$exfactory_data_arr[$val_a]['ex_qnty'];
											$fabric_issArr[$key]+=$fabric_data_arr[$val_a]['fab_iss'];
											
											$cut_today_preArr[$key]+=$production_data_arr[$val_a]['cutting_qnty_pre'];
											$embl_issue_today_preArr[$key]+=$production_data_arr[$val_a]['printing_qnty_pre'];
											$embl_rcv_today_preArr[$key]+=$production_data_arr[$val_a]['printreceived_qnty_pre'];
											$sewing_in_today_preArr[$key]+=$production_data_arr[$val_a]['sewingin_qnty_pre'];
											$sew_out_today_preArr[$key]+=$production_data_arr[$val_a]['sewing_out_pre'];
											$iron_today_preArr[$key]+=$production_data_arr[$val_a]['iron_pre'];
											$finish_today_preArr[$key]+=$production_data_arr[$val_a]['finish_pre'];
											$reject_today_preArr[$key]+=$production_data_arr[$val_a]['reject_pre'];
											$exfactory_today_preArr[$key]+=$exfactory_data_arr[$val_a]['exfac_pre'];
											
											}
										}
									}
									//var_dump($summeryDataArrPoQty); die;
									$inc=1;
									foreach($summeryDataArr as $key=>$row)
									{
										
										$poQty=$summeryDataArrPoQty[$key];
										
										
										$all_cut_today=$all_cut_todayArr[$key];	
										$all_embl_issue_today=$all_embl_issue_todayArr[$key];
										$all_embl_rcv_today=$all_embl_rcv_todayArr[$key];
										$all_sewing_in_today=$all_sewing_in_todayArr[$key];
										$all_sew_out_today=$all_sew_out_todayArr[$key];
										$all_iron_today=$all_iron_todayArr[$key];
										$all_finish_today=$all_finish_todayArr[$key];
										$all_reject_today=$all_reject_todayArr[$key];	
										$all_exfactory_qty=$all_exfactory_qtyArr[$key];
										
										$cut_today=$cut_todayArr[$key];	
										$embl_issue_today=$embl_issue_todayArr[$key];
										$embl_rcv_today=$embl_rcv_todayArr[$key];
										$sewing_in_today=$sewing_in_todayArr[$key];
										$sew_out_today=$sew_out_todayArr[$key];
										$iron_today=$iron_todayArr[$key];
										$finish_today=$finish_todayArr[$key];
										$reject_today=$reject_todayArr[$key];	
										
										$exfactory_qty=$exfactory_qtyArr[$key];
										$fabric_iss=$fabric_issArr[$key];
										
										$cut_total=$all_cut_todayArr[$key]+$cut_today_preArr[$key];
										$embl_issue_total=$all_embl_issue_todayArr[$key]+$embl_issue_today_preArr[$key];
										$embl_rcv_total=$all_embl_rcv_todayArr[$key]+$embl_rcv_today_preArr[$key];
										$sewing_in_total=$all_sewing_in_todayArr[$key]+$sewing_in_today_preArr[$key];
										$iron_total=$all_iron_todayArr[$key]+$iron_today_preArr[$key];
										$sew_out_total=$all_sew_out_todayArr[$key]+$sew_out_today_preArr[$key];
										$finish_total=$all_finish_todayArr[$key]+$finish_today_preArr[$key];
										$reject_total=$all_reject_todayArr[$key]+$reject_today_preArr[$key];
										
										$exfactory_total=$all_exfactory_qtyArr[$key]+$exfactory_today_preArr[$key];
										
									//if($cut_today!=0 || $embl_issue_today!=0 || $embl_rcv_today!=0 || $sewing_in_today!=0  || $sew_out_today!=0  || $iron_today!=0 || $exfactory_qty!=0  || $finish_today!=0)
									if($cut_total!=0 || $embl_issue_total!=0 || $embl_rcv_total!=0 || $sewing_in_total!=0  || $sew_out_total!=0  || $iron_total!=0 || $exfactory_total!=0  || $finish_total!=0)
									{
										
										$job_total+=$poQty;
										$grand_cut+=$cut_today;
										$grand_cut_total+=$cut_total;
										$grand_embl_issue+=$embl_issue_today;
										$grand_embl_iss_total+=$embl_issue_total;
										$grand_embl_rec+=$embl_rcv_today;
										$grand_embl_rev_total+=$embl_rcv_total;
										$grand_sew_in+=$sewing_in_today;
										$grand_sew_in_total+=$sewing_in_total;
										$grand_sew_out+=$sew_out_today;
										$grand_sew_out_total+=$sew_out_total;
										$grand_iron+=$iron_today;
										$grand_iron_total+=$iron_total;
										$grand_finish+=$finish_today;
										$grand_finish_total+=$finish_total;
										$grand_reject+=$reject_today;
										$grand_reject_total+=$reject_total;
										$grand_exfactory+=$exfactory_qty;
										$grand_exfa_total+=$exfactory_total;
										
										
										if ($inc%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>
		                            	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $inc; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $inc; ?>">
		                                    <td width="40"><? echo $inc;?></td>
		                                    <td width="100" align="center"><? echo $company_library[$row[csf('company_name')]]; ?></td>
		                                    <td width="100" align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
		                                    <td width="80" align="right"><? if($cut_today>0){echo number_format($cut_today);} ?></td>
		                                    <td width="80" align="right"><? if($cut_total>0){echo number_format($cut_total);}?></td>
		                                    <td width="80" align="right"><? if(($poQty-$cut_total)>0 || ($poQty-$cut_total)<0){echo number_format(($poQty-$cut_total));} ?></td>
		                                    <td width="80" align="right"><?   if($embl_issue_today>0){echo number_format($embl_issue_today);}?></td>
		                                    <td width="80" align="right"><?   if($embl_issue_total>0){echo number_format($embl_issue_total);} ?></td>
		                                    <td width="80" align="right"><?   if(($cut_total-$embl_issue_total)>0 || ($cut_total-$embl_issue_total)<0){echo number_format(($cut_total-$embl_issue_total));} ?></td>
		                                    <td width="80" align="right"><?  if($embl_rcv_today>0){ echo number_format($embl_rcv_today);} ?></td>
		                                    <td width="80" align="right"><?  if($embl_rcv_total>0){echo number_format($embl_rcv_total);} ?></td>
		                                    <td width="80" align="right"><?  if(($embl_issue_total-$embl_rcv_total)>0 || ($embl_issue_total-$embl_rcv_total)<0){ echo number_format(($embl_issue_total-$embl_rcv_total));} ?></td>
		                                    <td width="80" align="right"><?   if($sewing_in_today>0){echo number_format($sewing_in_today);} ?></td>
		                                    <td width="80" align="right"><?   if($sewing_in_total>0){echo number_format($sewing_in_total);} ?></td>
		                                    <td width="80" align="right"><?   if(($cut_total-$sewing_in_total)>0 || ($cut_total-$sewing_in_total)<0){echo number_format(($cut_total-$sewing_in_total));} ?></td>
		                                    <td width="80" align="right"><?  if($sew_out_today>0){echo number_format($sew_out_today);} ?></td>
		                                    <td width="80" align="right"><?  if($sew_out_total>0){ echo number_format($sew_out_total);} ?></td>
		                                    <td width="80" align="right"><?  if(($sewing_in_total-$sew_out_total)>0 || ($sewing_in_total-$sew_out_total)<0){ echo number_format(($sewing_in_total-$sew_out_total));} ?></td>
		                                    <td width="80" align="right"><?  if($iron_today>0){ echo number_format($iron_today);} ?></td>
		                                    <td width="80" align="right"><?  if($iron_total>0){  echo number_format($iron_total);} ?></td>
		                                    <td width="80" align="right"><?  if(($sew_out_total-$iron_total)>0 || ($sew_out_total-$iron_total)<0){ echo number_format(($sew_out_total-$iron_total));} ?></td>
		                                    <td width="80" align="right"><?  if($reject_today>0){ echo number_format($reject_today);} ?></td>
		                                    <td width="80" align="right"><?  if($reject_total>0){  echo number_format($reject_total);} ?></td>
		                                    <td width="80" align="right"><?  if($finish_today>0){  echo number_format($finish_today);} ?></td>
		                                    <td width="80" align="right"><?  if($finish_total>0){  echo number_format($finish_total);} ?></td>
		                                    <td width="80" align="right"><?  if(($sew_out_total-$finish_total)>0 || ($sew_out_total-$finish_total)<0){ echo number_format(($sew_out_total-$finish_total));} ?></td>
		                                   
		                                    <td width="80" align="right"><?  if($exfactory_qty>0){ echo number_format($exfactory_qty);} ?></td>
		                                    <td width="80" align="right"><?  if($exfactory_total>0){  echo number_format(($exfactory_total));} ?></td>
		                                    <td  align="right" ><? if(($poQty-$exfactory_total)>0 || ($poQty-$exfactory_total)<0){ echo number_format(($poQty-$exfactory_total));} ?></td>
										</tr> 
		                                <? 
										$inc++; 
										}
									} ?>
									</table>
				                 </div>
		                         <table class="rpt_table" width="2390" cellpadding="0" cellspacing="0" border="1" rules="all">
		                        <tfoot>
		                            <tr>
		                                <th width="40"><? // echo $i;?></th>
		                                <th width="100" align="right"></th>
		                                <th width="100" align="right"><strong>Grand Total:</strong></th>
		                                <th width="80" align="right"><?  echo number_format($grand_cut); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_cut_total); ?></th>
		                                <th width="80" align="right"><?  echo number_format(($job_total-$grand_cut_total)); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_embl_issue); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_embl_iss_total);?></th>
		                                <th width="80" align="right"><?  echo number_format(($grand_cut_total-$grand_embl_iss_total)); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_embl_rec); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_embl_rev_total); ?></th>
		                                <th width="80" align="right"><?  echo number_format(($grand_embl_iss_total-$grand_embl_rev_total)); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_sew_in); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_sew_in_total); ?></th>
		                                <th width="80" align="right"><?  echo number_format(($grand_cut_total-$grand_sew_in_total)); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_sew_out); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_sew_out_total); ?></th>
		                                <th width="80" align="right"><?  echo number_format(($grand_sew_in_total-$grand_sew_out_total)); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_iron); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_iron_total); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_sew_out_total-$grand_iron_total); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_reject); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_reject_total); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_finish); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_finish_total); ?></th>
		                                <th width="80" align="right"><?  echo number_format(($grand_sew_out_total-$grand_finish_total));?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_exfactory); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_exfa_total); ?></th>
		                                <th  align="right"><?  echo number_format(($job_total-$grand_exfa_total)); ?></th>
		                            </tr> 
		                            <tr>
		                            	<th colspan=""></th>
		                            </tr>
		                        </tfoot>   
		                    </table>     
		                        <br>
		                
		                <table class="rpt_table" width="3785" cellpadding="0" cellspacing="0" border="1" rules="all">
		                    <p style="float:left; width:300px; font-weight:bold;">Buyer Wise Details Part</p>
		                    <thead>
		                        <tr >
		                            <th width="40" rowspan="2">SL</th>
		                            <th width="100" rowspan="2">Buyer</th>
		                            <th width="140" rowspan="2">Style</th>
		                            <th width="60" rowspan="2">Job No</th>
		                            <th width="50" rowspan="2">Year</th>
		                            <th width="100" rowspan="2">Job Qty.</th>
		                            <th width="100" rowspan="2">Ship Date</th>
		                            <th width="120" rowspan="2">Shiping Status</th>
		                            <th width="100" rowspan="2">Fin. Fab. Issued</th>
		                            <th width="100" rowspan="2">Possible Cut Qty.</th>
		                            <th width="240" colspan="3">Cutting</th>
		                            <th width="240" colspan="3">EMBL Issue	</th>
		                            <th width="240" colspan="3">EMBL Receive</th>
		                            <th width="240" colspan="3">Sewing Input	</th>
		                            <th width="240" colspan="3">Sewing Output</th>
		                            <th width="240" colspan="3">Iron</th>
		                            <th width="200" colspan="2">Re-Iron Qty</th>
		                            <th width="160" colspan="2">Sewing Reject</th>
		                            <th width="240" colspan="3">Finish	</th>
		                            <th colspan="3">Ex- Factory</th>
		                        </tr>
		                        <tr>
		                            <th width="100" >Today.</th>
		                            <th width="100" >Total </th>
		                            <th width="100">WIP/ Bal. </th>
		                            <th width="100" >Today</th>
		                            <th width="100" >Total</th>
		                            <th width="100" >Issue Bal.</th>
		                            <th width="100" >Today </th>
		                            <th width="100" >Total</th>
		                            <th width="100" >WIP Bal</th>
		                            <th width="100" > Today </th>
		                            <th width="100" >Total </th>
		                            <th width="100" >WIP/ Bal.</th>
		                            <th width="100" >Today </th>
		                            <th width="100" >Total </th>
		                            <th width="100" >WIP/ Bal.</th>
		                            <th width="100" >Today </th>
		                            <th width="100" >Total </th>
		                            <th width="100" >WIP/ Bal.</th>
		                            <th width="100" >Today </th>
		                            <th width="100" >Total </th>
		                            <th width="100" >Today </th>
		                            <th width="100" >Total</th>
		                            <th width="100" >Today </th>
		                            <th width="100" >Total</th>
		                            <th width="100" >WIP /Bal.</th>
		                            <th width="100" >Today </th>
		                            <th width="100" >Total</th>
		                            <th>Ex-fac. Bal.</th>
		                        </tr>
		                    </thead>
		                </table>
		                 <div style="width:3800px; max-height:425px; overflow-y:scroll"   id="scroll_body">
		                    <table class="rpt_table" width="3770" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
		                <?
		                        //print_r($sql);
		                        $fabric_iss=0;
		                        $grand_cut=$grand_cut_total=$grand_embl_issue=$grand_embl_iss_total=$grand_sew_in=$grand_sew_in_total=$grand_sew_out=$grand_sew_out_total=0;
		                        $grand_iron=$grand_iron_total=$grand_finish=$grand_finish_total=$grand_reject=$grand_reject_total=$grand_exfactory=$grand_exfa_total=0; 
		                        $grand_fabric_iss=$grand_embl_rec=$grand_embl_rev_total=$grand_plan_cut=$job_total=0;	
		                        $tot_rows=count($sql);
		                        $i=1;	   		
		                        foreach($sql as $row)	
		                        {
									$dzn_qnty=0;
									if($costing_per_id_library[$row[csf('job_no')]]==1) $dzn_qnty=12;
									else if($costing_per_id_library[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
									else if($costing_per_id_library[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
									else if($costing_per_id_library[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
									else $dzn_qnty=1;
		                        
									$all_ship=0;
									$partial_ship=0;
									$full_pend=0;
									$ship_status=0;
									$full_ship=$fabric_iss=$exfactory_total=0;
									$cut_today=$embl_issue_today=$embl_rcv_today=$sewing_in_today=$sew_out_today=$iron_today=$finish_today=$reject_today=$exfactory_qty=0;
									$re_iron_today=$re_iron_total=0;
									$fabric_iss=$cut_total=$embl_issue_total=$embl_rcv_total=$sewing_in_total=$iron_total=$sew_out_total=$finish_total=$reject_total=0;
		                        
									$po_id_all=explode(',',$row[csf('po_id')]);
									$k=0;
									foreach( $po_id_all as $val_a)
									{
										
										$all_cut_today+=$production_data_arr[$val_a]['all_cutting_qnty'];
										$all_embl_issue_today+=$production_data_arr[$val_a]['all_printing_qnty'];
										$all_embl_rcv_today+=$production_data_arr[$val_a]['all_printreceived_qnty'];
										$all_sewing_in_today+=$production_data_arr[$val_a]['all_sewingin_qnty'];
										$all_sew_out_today+=$production_data_arr[$val_a]['all_sewing_out_qnty'];
										$all_iron_today+=$production_data_arr[$val_a]['all_iron_qnty'];
										$all_re_iron_today+=$production_data_arr[$val_a]['all_re_iron_qnty'];
										$all_finish_today+=$production_data_arr[$val_a]['all_finish_qnty'];
										$all_reject_today+=$production_data_arr[$val_a]['all_reject_today'];
										$all_exfactory_qty+=$exfactory_data_arr[$val_a]['all_ex_qnty'];
										
										$cut_today+=$production_data_arr[$val_a]['cutting_qnty'];
										$embl_issue_today+=$production_data_arr[$val_a]['printing_qnty'];
										$embl_rcv_today+=$production_data_arr[$val_a]['printreceived_qnty'];
										$sewing_in_today+=$production_data_arr[$val_a]['sewingin_qnty'];
										$sew_out_today+=$production_data_arr[$val_a]['sewing_out_qnty'];
										$iron_today+=$production_data_arr[$val_a]['iron_qnty'];
										$re_iron_today+=$production_data_arr[$val_a]['re_iron_qnty'];
										$finish_today+=$production_data_arr[$val_a]['finish_qnty'];
										$reject_today+=$production_data_arr[$val_a]['reject_today'];
										$exfactory_qty+=$exfactory_data_arr[$val_a]['ex_qnty'];
										$fabric_iss+=$fabric_data_arr[$val_a]['fab_iss'];
										
										$cut_total+=$production_data_arr[$val_a]['cutting_qnty_pre']+$production_data_arr[$val_a]['all_cutting_qnty'];
										$embl_issue_total+=$production_data_arr[$val_a]['printing_qnty_pre']+$production_data_arr[$val_a]['all_printing_qnty'];
										$embl_rcv_total+=$production_data_arr[$val_a]['printreceived_qnty_pre']+$production_data_arr[$val_a]['all_printreceived_qnty'];
										$sewing_in_total+=$production_data_arr[$val_a]['sewingin_qnty_pre']+$production_data_arr[$val_a]['all_sewingin_qnty'];
										$iron_total+=$production_data_arr[$val_a]['iron_pre']+$production_data_arr[$val_a]['all_iron_qnty'];
										$re_iron_total+=$production_data_arr[$val_a]['re_iron_qnty']+$production_data_arr[$val_a]['all_re_iron_pre'];
										
										$sew_out_total+=$production_data_arr[$val_a]['sewing_out_pre']+$production_data_arr[$val_a]['all_sewing_out_qnty'];
										$finish_total+=$production_data_arr[$val_a]['finish_pre']+$production_data_arr[$val_a]['all_finish_qnty'];
										$reject_total+=$production_data_arr[$val_a]['all_reject_today']+$production_data_arr[$val_a]['reject_pre'];
										$exfactory_total+=$exfactory_data_arr[$val_a]['all_ex_qnty']+$exfactory_data_arr[$val_a]['exfac_pre'];
										$k++;
									}	
		                        
		                       		//if($fabric_iss!=0 || ($cut_today!=0 || $embl_issue_today!=0 || $embl_rcv_today!=0 || $sewing_in_today!=0  || $sew_out_today!=0  || $iron_today!=0 || $exfactory_qty!=0  || $finish_today!=0))
									   //if($cut_today!=0 || $embl_issue_today!=0 || $embl_rcv_today!=0 || $sewing_in_today!=0  || $sew_out_today!=0  || $iron_today!=0 || $exfactory_qty!=0  || $finish_today!=0)
									if($cut_total!=0 || $embl_issue_total!=0 || $embl_rcv_total!=0 || $sewing_in_total!=0  || $sew_out_total!=0  || $iron_total!=0 || $exfactory_total!=0  || $finish_total!=0)
		
		                        	{
										$status_all=array_unique(explode(',',$row[csf('status')]));
										foreach($status_all as $ship_val)
										{
											if($ship_val==3) $full_ship=$ship_val;
											if($ship_val==2) $partial_ship=$ship_val;
											if($ship_val==1) $full_pend=$ship_val;
											if($ship_val==0) $all_ship=$ship_val;
										}
										if($full_ship==0)
										{
											if($partial_ship==0)
											{
												if($full_pend==0)
												{
													$ship_status=0;
												}
												else
												{
													$ship_status=1;
												}
											}
											else
											{
												$ship_status=2; 
											}
										}
										else
										{
											if($partial_ship==0)
											{
												if($full_pend==0)
												{
													$ship_status=3;
												}
												else
												{
													$ship_status=2;
												}  
											}
											else
											{  
												$ship_status=2;
											}
										} 
		                        
										/*if($ship_status!=3)
										{*/
											// echo $ship_status."<br>";
											$possible_cut_pcs=0;
											$possible_cut_pcs=$fabric_iss/$result_consumtion[$row[csf('job_no')]];
											$grand_possible_cut_pcs+=$possible_cut_pcs;
											$grand_cut+=$cut_today;
											$grand_cut_total+=$cut_total;
											$grand_embl_issue+=$embl_issue_today;
											$grand_embl_iss_total+=$embl_issue_total;
											$grand_embl_rec+=$embl_rcv_today;
											$grand_embl_rev_total+=$embl_rcv_total;
											$grand_sew_in+=$sewing_in_today;
											$grand_sew_in_total+=$sewing_in_total;
											$grand_sew_out+=$sew_out_today;
											$grand_sew_out_total+=$sew_out_total;
											$grand_iron+=$iron_today;
											$grand_iron_total+=$iron_total;
											$grand_finish+=$finish_today;
											$grand_finish_total+=$finish_total;
											$grand_reject+=$reject_today;
											$grand_reject_total+=$reject_total;
											
											$grand_re_iron_today+=$re_iron_today;
											$grand_re_iron_total+=$re_iron_total;
											$grand_exfactory+=$exfactory_qty;
											$grand_exfa_total+=$exfactory_total;
											$grand_fabric_iss+=$fabric_iss;
											$grand_plan_cut+=$row[csf('plan_cut')];
											$job_total+=$row[csf('job_quantity')];
											$txt_date=str_replace("'","",$txt_date_from);
											$diff=datediff("d",$txt_date,$row[csf('ship_date')]);
											if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
										?>
		                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
		                                    <td width="40"><? echo $i;?></td>
		                                    <td width="100" align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
		                                    <td width="140" align="center"><p><? echo $row[csf('style_ref_no')];?>&nbsp;</p></td>
		                                    <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')];?></td>
		                                    <td width="50" align="center"><? echo $row[csf('year')];?></td>
		                                    <td width="100" align="right"><a href="#report_details" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no_prefix_num')]; ?>', '<? echo $row[csf('year')]; ?>','job_color_style',850)"><? echo number_format($row[csf('job_quantity')]); ?></a></td>
		                                    <td width="100" align="center"><? echo change_date_format($row[csf('ship_date')]);?></td>
		                                    <td width="120" align="center"><p><a href="#report_details" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no_prefix_num')]; ?>', '<? echo $row[csf('year')]; ?>','shipping_sataus',400)"><? echo $shipment_status[$ship_status]; ?></a></p></td>
		                                    <td width="100" align="right"><? echo number_format($fabric_iss,2); ?></td>
		                                    <td width="100" align="right" title="Fabric Issue/Consumtion (<? echo number_format($result_consumtion[$row[csf('job_no')]],5); ?>)"><? echo number_format($possible_cut_pcs); ?></td>
		                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'cut_entry_today',1050)"><? if($cut_today>0){echo number_format($cut_today);} ?></a></td>
		                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'cut_entry_total',1050)"><? if($cut_total>0){echo number_format($cut_total);} ?></a></td>
		                                    <td width="100" align="right"><? if(($row[csf('job_quantity')]-$cut_total)!=0){echo number_format(($row[csf('job_quantity')]-$cut_total));} ?></td>
		                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'embl_today',1000)"><? if($embl_issue_today>0){echo number_format($embl_issue_today);}?></a></td>
		                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'embl_total',1000)"><? if($embl_issue_total>0){echo number_format($embl_issue_total);} ?></a></td>
		                                    <td width="100" align="right"><? if(($cut_total-$embl_issue_total)!=0){echo number_format(($cut_total-$embl_issue_total));} ?></td>
		                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'embl_receive_today',900)"><? if($embl_rcv_today>0){echo number_format($embl_rcv_today);} ?></a></td>
		                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'embl_receive_total',900)"><? if($embl_rcv_total>0){echo number_format($embl_rcv_total);} ?></a></td>
		                                    <td width="100" align="right"><?  if(($embl_issue_total-$embl_rcv_total)!=0){echo number_format(($embl_issue_total-$embl_rcv_total));} ?></td>
		                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'sewing_input_today',900)"><? if($sewing_in_today>0){echo number_format($sewing_in_today);} ?></a></td>
		                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'sewing_input_total',900)"><?  if($sewing_in_total>0){echo number_format($sewing_in_total);} ?></a></td>
		                                    <td width="100" align="right"><? if(($cut_total-$sewing_in_total)!=0){echo number_format(($cut_total-$sewing_in_total));} ?></td>
		                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'sewing_output_today',900)"><? if($sew_out_today>0){echo number_format($sew_out_today);} ?></a></td>
		                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'sewing_ooutput_total',900)"><? if($sew_out_total>0){echo number_format($sew_out_total);} ?></a></td>
		                                    <td width="100" align="right"><? if(($sewing_in_total-$sew_out_total)!=0){echo number_format(($sewing_in_total-$sew_out_total));} ?></td>
		                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'iron_today',900)"><? if($iron_today>0){echo number_format($iron_today);} ?></a></td>
		                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'iron_total',900)"><? if($iron_total>0){ echo number_format($iron_total);} ?></a></td>
		                                    <td width="100" align="right"><?  if(($sew_out_total-$iron_total)!=0){ echo number_format(($sew_out_total-$iron_total));} ?></td>
		                                    <td width="100" align="right"><? if($re_iron_today>0){echo number_format($re_iron_today);} ?></td>
		                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'re_iron_total',850)"><?  if($re_iron_total>0){echo number_format($re_iron_total);} ?></a></td>
		                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'reject_today',850)"><? if($reject_today>0){echo number_format($reject_today);} ?></a></td>
		                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'reject_total',850)"><? if($reject_total>0){echo number_format($reject_total);} ?></a></td>
		                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'finish_today',900)"><?  if($finish_today>0){echo number_format($finish_today);} ?></a></td>
		                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'finish_total',900)"><?  if($finish_total>0){echo number_format($finish_total);} ?></a></td>
		                                    <td width="100" align="right"><? if(($sew_out_total-$finish_total)!=0){echo number_format(($sew_out_total-$finish_total));} ?></td>
		                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'exfactory_today',900)"><? if($exfactory_qty>0){echo number_format($exfactory_qty);} ?></a></td>
		                                    <td width="100" align="right"><a href="##" onClick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'exfactory_total',900)"><? if($exfactory_total>0){echo number_format(($exfactory_total));} ?></a></td>
		                                    <td align="right"><?  if(($row[csf('job_quantity')]-$exfactory_total)!=0){echo number_format(($row[csf('job_quantity')]-$exfactory_total));}  ?></td>
		                                </tr>    
									<?
		                            $ship_status="";
		                            $i++;
		                            //}
		                        }
							}
							?>
		                </table>
		                </div>
		                <table style="width:3770px" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all">
		                    <tr>
		                        <td width="40">&nbsp;</td>
		                        <td width="100">&nbsp;</td>
		                        <td width="140"><strong>Grand Total:</strong></td>
		                        <td width="60">&nbsp;</td>
		                        <td width="50">&nbsp;</td>
		                        <td width="100" align="right" id="value_job_total"><? echo number_format($job_total); ?></td>
		                        <td width="100">&nbsp;</td>
		                        <td width="120">&nbsp;</td>
		                        <td width="100" align="right" id="value_fabric_issue"><? echo number_format($grand_fabric_iss); ?></td>
		                        <td width="100" align="right" id="value_plan_cut"><? echo number_format($grand_possible_cut_pcs); ?></td>
		                        <td width="100" align="right" id="value_cut_today"><? echo number_format($grand_cut); ?></td>
		                        <td width="100" align="right" id="value_cut_total"><? echo number_format($grand_cut_total); ?></td>
		                        <td width="100" align="right" id="value_cut_bal"><? echo number_format(($job_total-$grand_cut_total)); ?></td>
		                        <td width="100" align="right" id="value_embl_iss"><? echo number_format($grand_embl_issue); ?></td>
		                        <td width="100" align="right" id="value_embl_iss_total"><? echo number_format($grand_embl_iss_total);?></td>
		                        <td width="100" align="right" id="value_embl_iss_bal"><? echo number_format(($grand_cut_total-$grand_embl_iss_total)); ?></td>
		                        <td width="100" align="right" id="value_embl_rec"><? echo number_format($grand_embl_rec); ?></td>
		                        <td width="100" align="right" id="value_embl_rec_total"><? echo number_format($grand_embl_rev_total); ?></td>
		                        <td width="100" align="right" id="value_embl_rec_bal"><? echo number_format(($grand_embl_iss_total-$grand_embl_rev_total)); ?></td>
		                        <td width="100" align="right" id="value_sew_in"><? echo number_format($grand_sew_in); ?></td>
		                        <td width="100" align="right" id="value_sew_in_to"><? echo number_format($grand_sew_in_total); ?></td>
		                        <td width="100" align="right" id="value_sew_in_bal"><? echo number_format(($grand_cut_total-$grand_sew_in_total)); ?></td>
		                        <td width="100" align="right" id="value_sew_out"><? echo number_format($grand_sew_out); ?></td>
		                        <td width="100" align="right" id="value_sew_out_total"><? echo number_format($grand_sew_out_total); ?></td>
		                        <td width="100" align="right" id="value_sew_out_bal"><? echo number_format(($grand_sew_in_total-$grand_sew_out_total)); ?></td>
		                        <td width="100" align="right" id="value_iron"><? echo number_format($grand_iron); ?></td>
		                        <td width="100" align="right" id="value_iron_to"><? echo number_format($grand_iron_total); ?></td>
		                        <td width="100" align="right" id="value_iron_bal"><? echo number_format($grand_sew_out_total-$grand_iron_total); ?></td>
		                        <td width="100" align="right" id="value_re_iron"><? echo number_format($grand_re_iron_today); ?></td>
		                        <td width="100" align="right" id="value_re_iron_to"><? echo number_format($grand_re_iron_total); ?></td>
		                        <td width="100" align="right" id="value_reject"><? echo number_format($grand_reject); ?></td>
		                        <td width="100" align="right" id="value_reject_to"><? echo number_format($grand_reject_total); ?></td>
		                        <td width="100" align="right" id="value_finish"><? echo number_format($grand_finish); ?></td>
		                        <td width="100" align="right" id="value_finish_to"><? echo number_format($grand_finish_total); ?></td>
		                        <td width="100" align="right" id="value_finish_bal"><? echo number_format(($grand_sew_out_total-$grand_finish_total));?></td>
		                        <td width="100" align="right" id="value_exfactory"><? echo number_format($grand_exfactory); ?></td>
		                        <td width="100" align="right" id="value_exfactory_to"><? echo number_format($grand_exfa_total); ?></td>
		                        <td align="right" id="value_exfac_bal"><? echo number_format(($job_total-$grand_exfa_total)); ?></td>
		                    </tr> 
		                </table>
					</fieldset>
					<?	
				}
				else if(str_replace("'","",$cbo_search_by)==2)
				{
					?>
					<fieldset style="width:3800px;">
					<table width="1880"  cellspacing="0"   >
		                <tr class="form_caption" style="border:none;">
		                	<td colspan="29" align="center" style="border:none;font-size:14px; font-weight:bold" >Daily Order Wise Garments Production Status Report</td>
		                </tr>
		                <tr style="border:none;">
		                    <td colspan="29" align="center" style="border:none; font-size:16px; font-weight:bold">
		                    Company Name:<? 
								$com_arr=explode(",",str_replace("'","",$cbo_company_name));
								$comName="";
								foreach($com_arr as $comID)
								{
									$comName.=$company_library[$comID].',';
								}
								echo chop($comName,",");
								//echo $company_library[str_replace("'","",$cbo_company_name)]; 
							?>                                
		                    </td>
		                </tr>
		                <tr style="border:none;">
		                    <td colspan="29" align="center" style="border:none;font-size:12px; font-weight:bold">
		                    <? echo "Date: ". str_replace("'","",$txt_date_from) ;?>
		                    </td>
		                </tr>
					</table>
					<br />	
					<table class="rpt_table" width="3772" cellpadding="0" cellspacing="0" border="1" rules="all">
		                <thead>
		                    <tr >
		                        <th width="40"  rowspan="2">SL</th>
		                        <th width="100" rowspan="2">Buyer</th>
		                        <th width="150" rowspan="2">Style</th>
		                        <th width="60" rowspan="2">Job No</th>
		                        <th width="50" rowspan="2">Year</th>
		                        <th width="200" rowspan="2">Order No</th>
		                        <th width="100" rowspan="2">Order Qty.</th>
		                        <th width="100" rowspan="2">Ship Date</th>
		                        <th width="120" rowspan="2">Shiping Status</th>
		                        <th width="100" rowspan="2">Fin. Fab. Issued</th>
		                        <th width="100" rowspan="2">Possible Cut Qty.</th>
		                        <th width="300" colspan="3">Cutting</th>
		                        <th width="300" colspan="3">EMBL Issue	</th>
		                        <th width="300" colspan="3">EMBL Receive</th>
		                        <th width="300" colspan="3">Sewing Input	</th>
		                        <th width="300" colspan="3">Sewing Output</th>
		                        <th width="300" colspan="3">Iron</th>
		                        <th width="200" colspan="2">Sewing Reject</th>
		                        <th width="300" colspan="3">Finish	</th>
		                        <th colspan="3">Ex- Factory</th>
		                    </tr>
		                    <tr>
		                        <th width="100" >Today.</th>
		                        <th width="100">Total </th>
		                        <th width="100" >WIP Bal. </th>
		                        <th width="100" >Today</th>
		                        <th width="100" >Total</th>
		                        <th width="100" >Issue Bal.</th>
		                        <th width="100" >Today </th>
		                        <th width="100" >Total</th>
		                        <th width="100" >WIP Bal</th>
		                        <th width="100" > Today </th>
		                        <th width="100" >Total </th>
		                        <th width="100" >WIP/ Bal.</th>
		                        <th width="100" >Today </th>
		                        <th width="100" >Total </th>
		                        <th width="100" >WIP/ Bal.</th>
		                        <th width="100" >Today </th>
		                        <th width="100" >Total </th>
		                        <th width="100" >Iron Bal.</th>
		                        <th width="100" >Today </th>
		                        <th width="100" >Total</th>
		                        <th width="100" >Today </th>
		                        <th width="100" >Total</th>
		                        <th width="100" >WIP/ Bal.</th>
		                        <th width="100" >Today </th>
		                        <th width="100" >Total</th>
		                        <th  >Ex-fac. Bal.</th>
		                    </tr>
		                </thead>
					</table>
					<?
					$production_data_arr=array();		
					$production_mst_sql= sql_select("SELECT po_break_down_id,
						sum(CASE WHEN production_type ='1' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty,
						sum(CASE WHEN production_type ='2' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS printing_qnty,
						sum(CASE WHEN production_type ='3' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS printreceived_qnty,
						sum(CASE WHEN production_type ='4' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewingin_qnty,
						sum(CASE WHEN production_type ='5' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewing_out_qnty,
						sum(CASE WHEN production_type ='7' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS iron_qnty,
						sum(CASE WHEN production_type ='8' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS finish_qnty,
						sum(CASE WHEN production_type ='5' and production_date=".$txt_date_from." THEN reject_qnty ELSE 0 END) AS reject_today,
						sum(CASE WHEN production_type ='1' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty_pre,
						sum(CASE WHEN production_type ='2' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS printing_qnty_pre,
						sum(CASE WHEN production_type ='3' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS printreceived_qnty_pre,
						sum(CASE WHEN production_type ='4' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewingin_qnty_pre,
						sum(CASE WHEN production_type ='5' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewing_out_pre,
						sum(CASE WHEN production_type ='7' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS iron_pre,
						sum(CASE WHEN production_type ='8' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS finish_pre,
						sum(CASE WHEN production_type ='5' and production_date<".$txt_date_from." THEN reject_qnty ELSE 0 END) AS reject_pre
						from 
						pro_garments_production_mst 
						where  
						is_deleted=0 and status_active=1 
						group by po_break_down_id "); //reject_qnty
					foreach($production_mst_sql as $val)
					{
						$production_data_arr[$val[csf('po_break_down_id')]]['cutting_qnty']=$val[csf('cutting_qnty')];
						$production_data_arr[$val[csf('po_break_down_id')]]['printing_qnty']=$val[csf('printing_qnty')];
						$production_data_arr[$val[csf('po_break_down_id')]]['printreceived_qnty']=$val[csf('printreceived_qnty')];
						$production_data_arr[$val[csf('po_break_down_id')]]['sewingin_qnty']=$val[csf('sewingin_qnty')];
						$production_data_arr[$val[csf('po_break_down_id')]]['sewing_out_qnty']=$val[csf('sewing_out_qnty')];
						$production_data_arr[$val[csf('po_break_down_id')]]['iron_qnty']=$val[csf('iron_qnty')];
						$production_data_arr[$val[csf('po_break_down_id')]]['finish_qnty']=$val[csf('finish_qnty')];
						$production_data_arr[$val[csf('po_break_down_id')]]['reject_today']=$val[csf('reject_today')];
						$production_data_arr[$val[csf('po_break_down_id')]]['cutting_qnty_pre']=$val[csf('cutting_qnty_pre')];
						$production_data_arr[$val[csf('po_break_down_id')]]['printing_qnty_pre']=$val[csf('printing_qnty_pre')];
						$production_data_arr[$val[csf('po_break_down_id')]]['printreceived_qnty_pre']=$val[csf('printreceived_qnty_pre')];
						$production_data_arr[$val[csf('po_break_down_id')]]['sewingin_qnty_pre']=$val[csf('sewingin_qnty_pre')];
						$production_data_arr[$val[csf('po_break_down_id')]]['sewing_out_pre']=$val[csf('sewing_out_pre')];
						$production_data_arr[$val[csf('po_break_down_id')]]['iron_pre']=$val[csf('iron_pre')];
						$production_data_arr[$val[csf('po_break_down_id')]]['finish_pre']=$val[csf('finish_pre')];
						$production_data_arr[$val[csf('po_break_down_id')]]['reject_pre']=$val[csf('reject_pre')];	
					}
					 $result_consumtion=array();
					$sql_consumtiont_qty=sql_select(" select b.po_break_down_id, a.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs from wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id=b.pre_cost_fabric_cost_dtls_id   GROUP BY b.po_break_down_id, a.body_part_id");
					//echo  "select b.po_break_down_id, a.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs from wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id=b.pre_cost_fabric_cost_dtls_id   GROUP BY b.po_break_down_id, a.body_part_id";
					
					/*$sql_consumtiont_qty=sql_select(" select b.po_break_down_id, b.color_number_id, c.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs
								  from  wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c
								  where b.pre_cost_fabric_cost_dtls_id=c.id and b.pcs>0 group by b.po_break_down_id, b.color_number_id, c.body_part_id");*/
					
					foreach($sql_consumtiont_qty as $row_consum)
					{
						//$con_avg= $row_consum[csf("conjunction")];///str_replace("'","",$row_sew[csf("pcs")]);
						//$con_per_pcs=$con_avg/$row_consum[csf("pcs")];	
						$result_consumtion[$row_consum[csf('po_break_down_id')]]+=$row_consum[csf("requirment")]/$row_consum[csf("pcs")];
					}
					unset($sql_consumtiont_qty);
					$exfactory_sql=sql_select("SELECT po_break_down_id,
						sum(CASE WHEN  ex_factory_date=".$txt_date_from." AND entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN  ex_factory_date=".$txt_date_from." AND entry_form=85 THEN ex_factory_qnty ELSE 0 END) AS exfac_qty,
						sum(CASE WHEN  ex_factory_date<".$txt_date_from." AND entry_form!=85  THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN  ex_factory_date<".$txt_date_from." AND entry_form=85  THEN ex_factory_qnty ELSE 0 END) AS exfac_pre
						from 
						pro_ex_factory_mst 
						where  
						is_deleted=0 and status_active=1
						group by po_break_down_id ");
						
					$exfactory_data_arr=array();
					foreach($exfactory_sql as $value)
					{
						$exfactory_data_arr[$value[csf('po_break_down_id')]]['ex_qnty']=$value[csf('exfac_qty')];
						$exfactory_data_arr[$value[csf('po_break_down_id')]]['exfac_pre']=$value[csf('exfac_pre')];
					}
					$sql_fabric_qty=sql_select( "SELECT a.po_breakdown_id,
						sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =2  AND a.entry_form =18  THEN a.quantity ELSE 0 END ) AS fabric_qty,
						sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_qty,
						sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_qty
						FROM order_wise_pro_details a,inv_transaction b
						WHERE a.trans_id = b.id 
						and b.status_active=1 and a.entry_form in(18,15) and a.quantity!=0 and  b.is_deleted=0  group by a.po_breakdown_id");
					$fabric_data_arr=array();
					foreach($sql_fabric_qty as $inf)
					{
						$fabric_data_arr[$inf[csf("po_breakdown_id")]]["fab_iss"]=$inf[csf("fabric_qty")]+$inf[csf("trans_in_qty")]-$inf[csf("trans_out_qty")];
					}	
					?>
					<div style="width:3772px; max-height:425px; overflow-y:scroll"   id="scroll_body">
		                <table class="rpt_table" width="3750" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
							<?
		                    if($db_type==0)
		                    {
		                    	$sql_data="select b.company_name,a.id as po_id,a.po_number,b.job_no_prefix_num,b.buyer_name,b.style_ref_no,a.po_quantity,a.plan_cut as plan_cut,year(b.insert_date) as year,pub_shipment_date as ship_date,a.shiping_status as status from  wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no $company_name $buyer_name $job_cond_id $style_cond $order_cond $po_order_cond $ship_status_cond $year_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1   order by b.buyer_name,b.job_no_prefix_num";
								$sql=sql_select($sql_data);	
		                    }
		                    else if($db_type==2)
		                    {
		                    	$sql_data="select b.company_name,a.id as po_id,a.po_number,b.job_no_prefix_num,b.buyer_name,b.style_ref_no,a.po_quantity,a.plan_cut as plan_cut,extract(year from b.insert_date) as year,pub_shipment_date as ship_date,a.shiping_status as status from  wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no $company_name $buyer_name $job_cond_id $style_cond $order_cond $po_order_cond $ship_status_cond $year_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 order by b.buyer_name,b.job_no_prefix_num";
								$sql=sql_select($sql_data);	
		                    }
							
							//echo $sql_data;
							
							$grand_fabric_iss=$grand_embl_rec=$grand_embl_rev_total=$grand_plan_cut=$job_total=$exfactory_total=0;
							$cut_today=$embl_issue_today=$embl_rcv_today=$sewing_in_today=$sew_out_today=$iron_today=$finish_today=$reject_today=$exfactory_qty=0;
							$fabric_iss=$cut_total=$embl_issue_total=$embl_rcv_total=$sewing_in_total=$iron_total=$sew_out_total=$finish_total=$reject_total=0;
							$grand_cut=$grand_cut_total=$grand_embl_issue=$grand_embl_iss_total=$grand_sew_in=$grand_sew_in_total=$grand_sew_out=$grand_sew_out_total=0;
							$grand_iron=$grand_iron_total=$grand_finish=$grand_finish_total=$grand_reject=$grand_reject_total=$grand_exfactory=$grand_exfa_total=0; 
							$tot_rows=count($sql);	
		                    $i=1;	
		                    foreach($sql as $row)	
		                    {
								$cut_today=$production_data_arr[$row[csf('po_id')]]['cutting_qnty'];
								$embl_issue_today=$production_data_arr[$row[csf('po_id')]]['printing_qnty'];
								$embl_rcv_today=$production_data_arr[$row[csf('po_id')]]['printreceived_qnty'];
								$sewing_in_today=$production_data_arr[$row[csf('po_id')]]['sewingin_qnty'];
								$sew_out_today=$production_data_arr[$row[csf('po_id')]]['sewing_out_qnty'];
								$iron_today=$production_data_arr[$row[csf('po_id')]]['iron_qnty'];
								$finish_today=$production_data_arr[$row[csf('po_id')]]['finish_qnty'];
								$reject_today=$production_data_arr[$row[csf('po_id')]]['reject_today'];
								$exfactory_qty=$exfactory_data_arr[$row[csf('po_id')]]['ex_qnty'];
								$fabric_iss=$fabric_data_arr[$row[csf('po_id')]]['fab_iss'];
								$cut_total=$production_data_arr[$row[csf('po_id')]]['cutting_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['cutting_qnty'];
								$embl_issue_total=$production_data_arr[$row[csf('po_id')]]['printing_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['printing_qnty'];
								$embl_rcv_total=$production_data_arr[$row[csf('po_id')]]['printreceived_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['printreceived_qnty'];
								$sewing_in_total=$production_data_arr[$row[csf('po_id')]]['sewingin_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['sewingin_qnty'];
								$iron_total=$production_data_arr[$row[csf('po_id')]]['iron_pre']+$production_data_arr[$row[csf('po_id')]]['iron_qnty'];
								$sew_out_total+=$production_data_arr[$row[csf('po_id')]]['sewing_out_pre']+$production_data_arr[$row[csf('po_id')]]['sewing_out_qnty'];
								$finish_total=$production_data_arr[$row[csf('po_id')]]['finish_pre']+$production_data_arr[$row[csf('po_id')]]['finish_qnty'];
								$reject_total=$production_data_arr[$row[csf('po_id')]]['reject_today']+$production_data_arr[$row[csf('po_id')]]['reject_pre'];
								$exfactory_total=$exfactory_data_arr[$row[csf('po_id')]]['ex_qnty']+$exfactory_data_arr[$row[csf('po_id')]]['exfac_pre'];
								
								//if($fabric_iss!=0 || ($cut_today!=0 || $embl_issue_today!=0 || $embl_rcv_today!=0 || $sewing_in_today!=0  || $sew_out_today!=0  || $iron_today!=0 || $exfactory_qty!=0  || $finish_today!=0))
								if($cut_today!=0 || $embl_issue_today!=0 || $embl_rcv_today!=0 || $sewing_in_today!=0  || $sew_out_today!=0  || $iron_today!=0 || $exfactory_qty!=0  || $finish_today!=0)
								{
									$possible_cut_pcs=0;
									$possible_cut_pcs=$fabric_iss/$result_consumtion[$row[csf('po_id')]];
									$grand_possible_cut_pcs+=$possible_cut_pcs;
									$grand_cut+=$cut_today;
									$grand_cut_total+=$cut_total;
									$grand_embl_issue+=$embl_issue_today;
									$grand_embl_iss_total+=$embl_issue_total;
									$grand_embl_rec+=$embl_rcv_today;
									$grand_embl_rev_total+=$embl_rcv_total;
									$grand_sew_in+=$sewing_in_today;
									$grand_sew_in_total+=$sewing_in_total;
									$grand_sew_out+=$sew_out_today;
									$grand_sew_out_total+=$sew_out_total;
									$grand_iron+=$iron_today;
									$grand_iron_total+=$iron_total;
									$grand_finish+=$finish_today;
									$grand_finish_total+=$finish_total;
									$grand_reject+=$reject_today;
									$grand_reject_total+=$reject_total;
									$grand_exfactory+=$exfactory_qty;
									$grand_exfa_total+=$exfactory_total;
									$grand_fabric_iss+=$fabric_iss;
									$grand_plan_cut+=$row[csf('plan_cut')];
									$posible_cut_pcs=$result_consumtion[$row[csf('po_id')]];
									$job_total+=$row[csf('po_quantity')];
									$txt_date=str_replace("'","",$txt_date_from);
									$diff=datediff("d",$txt_date,$row[csf('ship_date')]);
									
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
		                                <td width="40"><? echo $i;?></td>
		                                <td width="100" align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
		                                <td width="150" align="center"><p><? echo $row[csf('style_ref_no')];?>&nbsp;</p></td>
		                                <td width="60" align="center"><?  echo $row[csf('job_no_prefix_num')];?></td>
		                                <td width="50" align="center"><?  echo $row[csf('year')];?></td>
		                                <td width="200" align="center"><p><?  echo $row[csf('po_number')];?></p></td>
		                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'order_quantity',1050,250)"><?  echo number_format($row[csf('po_quantity')]); ?></a></td>
		                                <td width="100" align="center"><? echo change_date_format($row[csf('ship_date')]);?></td>
		                                <td width="120" align="center"><p><? echo $shipment_status[$row[csf('status')]]; ?></p></td>
		                                <td width="100" align="right"><? echo number_format($fabric_iss,2); ?></td>
		                                <td width="100" align="right" title="Fabric Issue/Consumtion (<? echo number_format($result_consumtion[$row[csf('po_id')]],5); ?>)"><? echo number_format($possible_cut_pcs); ?></td>
		                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'today_order_cutting',850,250)"><?  if($cut_today>0){ echo number_format($cut_today);} ?></a></td>
		                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'today_order_cutting',850,250)"><?  if($cut_total>0){ echo number_format($cut_total);} ?></a></td>
		                                <td width="100" align="right"><?  if(($row[csf('po_quantity')]-$cut_total)!=0){  echo number_format(($row[csf('po_quantity')]-$cut_total));} ?></td>
		                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_issue_order',850,250)"><?  if($embl_issue_today>0){ echo number_format($embl_issue_today);} ?></a></td>
		                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_issue_order',850,250)"><?   if($embl_issue_total>0){ echo number_format($embl_issue_total);} ?></a></td>
		                                <td width="100" align="right"><?  if(($cut_total-$embl_issue_total)!=0){echo number_format(($cut_total-$embl_issue_total),0);} ?></td>
		                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_receive_order',850,250)"><?   if($embl_rcv_today>0){echo number_format($embl_rcv_today);} ?></a></td>
		                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_receive_order',850,250)"><?   if($embl_rcv_total>0){ echo number_format($embl_rcv_total);} ?></a></td>
		                                <td width="100" align="right"><?  if(($embl_issue_total-$embl_rcv_total)!=0){ echo number_format(($embl_issue_total-$embl_rcv_total));} ?></td>
		                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'sewing_input_order',850,250)"><?  if($sewing_in_today>0){  echo number_format($sewing_in_today);} ?></a></td>
		                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'sewing_input_order',850,250)"><?   if($sewing_in_total>0){ echo number_format($sewing_in_total);} ?></a></td>
		                                <td width="100" align="right"><?   if(($cut_total-$sewing_in_total)!=0){ echo number_format(($cut_total-$sewing_in_total),2);} ?></td>
		                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'sewing_output_order',850,250)"><?  if($sew_out_today>0){ echo number_format($sew_out_today);} ?></a></td>
		                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'sewing_output_order',850,250)"><?   if($sew_out_total>0){ echo number_format($sew_out_total);} ?></a></td>
		                                <td width="100" align="right"><?  if(($sewing_in_total-$sew_out_total)!=0){ echo number_format(($sewing_in_total-$sew_out_total));} ?></td>
		                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'iron__entry_order',850,300)"><?   if($iron_today>0){ echo number_format($iron_today);} ?></a></td>
		                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'iron__entry_order',850,300)"><?   if($iron_total>0){ echo number_format($iron_total);} ?></a></td>
		                                <td width="100" align="right"><?  if(($sew_out_total-$iron_total)!=0){ echo number_format(($sew_out_total-$iron_total),0);} ?></td>
		                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'reject_qty_order',700,250)"><?  if($reject_today>0){  echo number_format($reject_today);} ?></a></td>
		                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'reject_qty_order',700,250)"><?   if($reject_total>0){ echo number_format($reject_total);} ?></a></td>
		                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'finish_qty_order',850,300)"><? if($finish_today>0){  echo number_format($finish_today);} ?></a></td>
		                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'finish_qty_order',850,300)"><?   if($finish_total>0){ echo number_format($finish_total);} ?></a></td>
		                                <td width="100" align="right"><?  if(($sew_out_total-$finish_total)!=0){   echo number_format(($sew_out_total-$finish_total));} ?></td>
		                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'exfactrory__entry_order',850,250)"><?  if($exfactory_qty>0){ echo number_format($exfactory_qty);} ?></a></td>
		                                <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'exfactrory__entry_order',850,250)"><?  if($exfactory_total>0){  echo number_format(($exfactory_total));} ?></a></td>
		                                <td align="right"><?  if(($row[csf('po_quantity')]-$exfactory_total)!=0){ echo number_format(($row[csf('po_quantity')]-$exfactory_total));} ?></td>
									</tr>    
									<?
									$i++;
								}
		                    }
		                    ?>
		                </table>     
		                <table class="rpt_table" width="3750" cellpadding="0" cellspacing="0" border="1" rules="all">
		                    <tfoot>
		                        <tr>
		                            <th width="40"><? // echo $i;?></th>
		                            <th width="100"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></th>
		                            <th width="150"><strong>Grand Total:</strong></th>
		                            <th width="60"></th>
		                            <th width="50"><? //echo $pro_date_sql_row[csf("po_number")];?></th>
		                            <th width="160"><? //echo $pro_date_sql_row[csf("po_number")];?></th>
		                            <th width="100" align="right" id="value_job_total"><?  echo number_format($job_total); ?></th>
		                            <th width="100"></th>
		                            <th width="120"></th>
		                            <th width="100" id="value_fabric_issue"><? echo number_format($grand_fabric_iss); ?></th>
		                            <th width="100" align="right" id="value_plan_cut"><? echo number_format($grand_possible_cut_pcs); ?></th>
		                            <th width="100" align="right" id="value_cut_today"><?  echo number_format($grand_cut); ?></th>
		                            <th width="100" align="right" id="value_cut_total"><?  echo number_format($grand_cut_total); ?></th>
		                            <th width="100" align="right" id="value_cut_bal"><?  echo number_format(($job_total-$grand_cut_total)); ?></th>
		                            <th width="100" align="right" id="value_embl_iss"><?  echo number_format($grand_embl_issue); ?></th>
		                            <th width="100" align="right" id="value_embl_iss_total"><?  echo number_format($grand_embl_iss_total);?></th>
		                            <th width="100" align="right" id="value_embl_iss_bal"><?  echo number_format(($grand_cut_total-$grand_embl_iss_total)); ?></th>
		                            <th width="100" align="right" id="value_embl_rec"><?  echo number_format($grand_embl_rec); ?></th>
		                            <th width="100" align="right" id="value_embl_rec_total"><?  echo number_format($grand_embl_rev_total); ?></th>
		                            <th width="100" align="right" id="value_embl_rec_bal"><?  echo number_format(($grand_embl_iss_total-$grand_embl_rev_total)); ?></th>
		                            <th width="100" align="right" id="value_sew_in"><?  echo number_format($grand_sew_in); ?></th>
		                            <th width="100" align="right" id="value_sew_in_to"><?  echo number_format($grand_sew_in_total); ?></th>
		                            <th width="100" align="right" id="value_sew_in_bal"><?  echo number_format(($grand_cut_total-$grand_sew_in_total)); ?></th>
		                            <th width="100" align="right" id="value_sew_out"><?  echo number_format($grand_sew_out); ?></th>
		                            <th width="100" align="right" id="value_sew_out_total"><?  echo number_format($grand_sew_out_total); ?></th>
		                            <th width="100" align="right" id="value_sew_out_bal"><?  echo number_format(($grand_sew_in_total-$grand_sew_out_total)); ?></th>
		                            <th width="100" align="right" id="value_iron"><?  echo number_format($grand_iron); ?></th>
		                            <th width="100" align="right" id="value_iron_to"><?  echo number_format($grand_iron_total); ?></th>
		                            <th width="100" align="right" id="value_iron_bal"><?  echo number_format($grand_sew_out_total-$grand_iron_total); ?></th>
		                            <th width="100" align="right" id="value_reject"><?  echo number_format($grand_reject); ?></th>
		                            <th width="100" align="right" id="value_reject_to"><?  echo number_format($grand_reject_total); ?></th>
		                            <th width="100" align="right" id="value_finish"><?  echo number_format($grand_finish); ?></th>
		                            <th width="100" align="right" id="value_finish_to"><?  echo number_format($grand_finish_total); ?></th>
		                            <th width="100" align="right" id="value_finish_bal"><?  echo number_format(($grand_sew_out_total-$grand_finish_total));?></th>
		                            <th width="100" align="right" id="value_exfactory"><?  echo number_format($grand_exfactory); ?></th>
		                            <th width="100" align="right" id="value_exfactory_to"><?  echo number_format($grand_exfa_total); ?></th>
		                            <th width="100" align="right" id="value_exfac_bal"><?  echo number_format(($job_total-$grand_exfa_total)); ?></th>
		                        </tr> 
		                    </tfoot>
		                </table>        
					</div>
					</fieldset>
					<?	
				}
			 	else if(str_replace("'","",$cbo_search_by)==4)
				{ 
					?>
					<fieldset style="width:3590px;">
		                <table width="1880"  cellspacing="0">
		                    <tr class="form_caption" style="border:none;">
		                    	<td colspan="29" align="center" style="border:none;font-size:14px; font-weight:bold" >Daily Order Wise Garments Production Status Report</td>
		                    </tr>
		                    <tr style="border:none;">
		                        <td colspan="29" align="center" style="border:none; font-size:16px; font-weight:bold">
		                        Company Name:<? 
								$com_arr=explode(",",str_replace("'","",$cbo_company_name));
								$comName="";
								foreach($com_arr as $comID)
								{
									$comName.=$company_library[$comID].',';
								}
								echo chop($comName,",");
								//echo $company_library[str_replace("'","",$cbo_company_name)]; 
								?>                                
		                        </td>
		                    </tr>
		                    <tr style="border:none;">
		                        <td colspan="29" align="center" style="border:none;font-size:12px; font-weight:bold">
		                        <? echo "Date: ". str_replace("'","",$txt_date_from) ;?>
		                        </td>
		                    </tr>
		                </table>
		                <br />
		                <?
		                $production_data_arr=array();
						
						
						/*if (str_replace("'","",$txt_po_no)=="")
						{		
		                $production_mst_sql= sql_select("SELECT po_break_down_id,
							sum(CASE WHEN production_type ='1' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty,
							sum(CASE WHEN production_type ='2' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS printing_qnty,
							sum(CASE WHEN production_type ='3' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS printreceived_qnty,
							sum(CASE WHEN production_type ='4' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewingin_qnty,
							sum(CASE WHEN production_type ='5' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewing_out_qnty,
							sum(CASE WHEN production_type ='7' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS iron_qnty,
							sum(CASE WHEN production_type ='8' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS finish_qnty,
							sum(CASE WHEN production_type ='5' and production_date=".$txt_date_from." THEN reject_qnty ELSE 0 END) AS reject_today,
							sum(CASE WHEN production_type ='1' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty_pre,
							sum(CASE WHEN production_type ='2' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS printing_qnty_pre,
							sum(CASE WHEN production_type ='3' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS printreceived_qnty_pre,
							sum(CASE WHEN production_type ='4' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewingin_qnty_pre,
							sum(CASE WHEN production_type ='5' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewing_out_pre,
							sum(CASE WHEN production_type ='7' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS iron_pre,
							sum(CASE WHEN production_type ='8' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS finish_pre,
							sum(CASE WHEN production_type ='5' and production_date<".$txt_date_from." THEN reject_qnty ELSE 0 END) AS reject_pre
							from 
							pro_garments_production_mst 
							where  
							is_deleted=0 and status_active=1 
							group by po_break_down_id "); //reject_qnty
							
						}
						else
						{*/
		                $production_mst_sql= sql_select("SELECT po_break_down_id,
							sum(CASE WHEN production_type ='1' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty,
							sum(CASE WHEN production_type ='2' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS printing_qnty,
							sum(CASE WHEN production_type ='3' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS printreceived_qnty,
							sum(CASE WHEN production_type ='4' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewingin_qnty,
							sum(CASE WHEN production_type ='5' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewing_out_qnty,
							sum(CASE WHEN production_type ='7' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS iron_qnty,
							sum(CASE WHEN production_type ='8' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS finish_qnty,
							sum(CASE WHEN production_type ='5' and production_date=".$txt_date_from." THEN reject_qnty ELSE 0 END) AS reject_today,
							sum(CASE WHEN production_type ='1' THEN production_quantity ELSE 0 END) AS all_cutting_qnty,
							sum(CASE WHEN production_type ='2' THEN production_quantity ELSE 0 END) AS all_printing_qnty,
							sum(CASE WHEN production_type ='3' THEN production_quantity ELSE 0 END) AS all_printreceived_qnty,
							sum(CASE WHEN production_type ='4' THEN production_quantity ELSE 0 END) AS all_sewingin_qnty,
							sum(CASE WHEN production_type ='5' THEN production_quantity ELSE 0 END) AS all_sewing_out_qnty,
							sum(CASE WHEN production_type ='7' THEN production_quantity ELSE 0 END) AS all_iron_qnty,
							sum(CASE WHEN production_type ='8' THEN production_quantity ELSE 0 END) AS all_finish_qnty,
							sum(CASE WHEN production_type ='5' THEN reject_qnty ELSE 0 END) AS all_reject_today,
							sum(CASE WHEN production_type ='1' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty_pre,
							sum(CASE WHEN production_type ='2' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS printing_qnty_pre,
							sum(CASE WHEN production_type ='3' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS printreceived_qnty_pre,
							sum(CASE WHEN production_type ='4' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewingin_qnty_pre,
							sum(CASE WHEN production_type ='5' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewing_out_pre,
							sum(CASE WHEN production_type ='7' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS iron_pre,
							sum(CASE WHEN production_type ='8' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS finish_pre,
							sum(CASE WHEN production_type ='5' and production_date<".$txt_date_from." THEN reject_qnty ELSE 0 END) AS reject_pre
							from 
							pro_garments_production_mst 
							where  
							is_deleted=0 and status_active=1 
							group by po_break_down_id "); //reject_qnty
						//}
							
							
						foreach($production_mst_sql as $val)
						{
							
							/*if (str_replace("'","",$txt_po_no)!="")
							{*/
								$production_data_arr[$val[csf('po_break_down_id')]]['all_cutting_qnty']=$val[csf('all_cutting_qnty')];
								$production_data_arr[$val[csf('po_break_down_id')]]['all_printing_qnty']=$val[csf('all_printing_qnty')];
								$production_data_arr[$val[csf('po_break_down_id')]]['all_printreceived_qnty']=$val[csf('all_printreceived_qnty')];
								$production_data_arr[$val[csf('po_break_down_id')]]['all_sewingin_qnty']=$val[csf('all_sewingin_qnty')];
								$production_data_arr[$val[csf('po_break_down_id')]]['all_sewing_out_qnty']=$val[csf('all_sewing_out_qnty')];
								$production_data_arr[$val[csf('po_break_down_id')]]['all_iron_qnty']=$val[csf('all_iron_qnty')];
								$production_data_arr[$val[csf('po_break_down_id')]]['all_finish_qnty']=$val[csf('all_finish_qnty')];
								$production_data_arr[$val[csf('po_break_down_id')]]['all_reject_today']=$val[csf('all_reject_today')];
							//}
							
							$production_data_arr[$val[csf('po_break_down_id')]]['cutting_qnty']=$val[csf('cutting_qnty')];
							$production_data_arr[$val[csf('po_break_down_id')]]['printing_qnty']=$val[csf('printing_qnty')];
							$production_data_arr[$val[csf('po_break_down_id')]]['printreceived_qnty']=$val[csf('printreceived_qnty')];
							$production_data_arr[$val[csf('po_break_down_id')]]['sewingin_qnty']=$val[csf('sewingin_qnty')];
							$production_data_arr[$val[csf('po_break_down_id')]]['sewing_out_qnty']=$val[csf('sewing_out_qnty')];
							$production_data_arr[$val[csf('po_break_down_id')]]['iron_qnty']=$val[csf('iron_qnty')];
							$production_data_arr[$val[csf('po_break_down_id')]]['finish_qnty']=$val[csf('finish_qnty')];
							$production_data_arr[$val[csf('po_break_down_id')]]['reject_today']=$val[csf('reject_today')];
							$production_data_arr[$val[csf('po_break_down_id')]]['cutting_qnty_pre']=$val[csf('cutting_qnty_pre')];
							$production_data_arr[$val[csf('po_break_down_id')]]['printing_qnty_pre']=$val[csf('printing_qnty_pre')];
							$production_data_arr[$val[csf('po_break_down_id')]]['printreceived_qnty_pre']=$val[csf('printreceived_qnty_pre')];
							$production_data_arr[$val[csf('po_break_down_id')]]['sewingin_qnty_pre']=$val[csf('sewingin_qnty_pre')];
							$production_data_arr[$val[csf('po_break_down_id')]]['sewing_out_pre']=$val[csf('sewing_out_pre')];
							$production_data_arr[$val[csf('po_break_down_id')]]['iron_pre']=$val[csf('iron_pre')];
							$production_data_arr[$val[csf('po_break_down_id')]]['finish_pre']=$val[csf('finish_pre')];
							$production_data_arr[$val[csf('po_break_down_id')]]['reject_pre']=$val[csf('reject_pre')];	
						}
						
						$result_consumtion=array();
		                $sql_consumtiont_qty=sql_select(" select b.po_break_down_id, a.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs from wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id=b.pre_cost_fabric_cost_dtls_id   GROUP BY b.po_break_down_id, a.body_part_id");
						//echo  "select a.job_no, a.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs from wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id=b.pre_cost_fabric_cost_dtls_id   GROUP BY a.job_no, a.body_part_id";
						
						/*$sql_consumtiont_qty=sql_select(" select b.po_break_down_id, b.color_number_id, c.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs
		                              from  wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c
		                              where b.pre_cost_fabric_cost_dtls_id=c.id and b.pcs>0 group by b.po_break_down_id, b.color_number_id, c.body_part_id");*/
		                
		                foreach($sql_consumtiont_qty as $row_consum)
		                {
							//$con_avg= $row_consum[csf("conjunction")];///str_replace("'","",$row_sew[csf("pcs")]);
							//$con_per_pcs=$con_avg/$row_consum[csf("pcs")];	
							$result_consumtion[$row_consum[csf('po_break_down_id')]]+=$row_consum[csf("requirment")]/$row_consum[csf("pcs")];
		                }
						unset($sql_consumtiont_qty); 
		                //var_dump($result_consumtion['FAL-15-00198']);die;
						$exfactory_sql=sql_select("SELECT po_break_down_id,
							sum(CASE WHEN  ex_factory_date=".$txt_date_from." AND entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN  ex_factory_date=".$txt_date_from." AND entry_form=85 THEN ex_factory_qnty ELSE 0 END) AS exfac_qty,sum(CASE WHEN  entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) AS all_exfac_qty,
							sum(CASE WHEN  ex_factory_date<".$txt_date_from."  AND entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN  ex_factory_date<".$txt_date_from."  AND entry_form=85 THEN ex_factory_qnty ELSE 0 END) AS exfac_pre
							from 
							pro_ex_factory_mst 
							where  
							is_deleted=0 and status_active=1
							group by po_break_down_id ");
						$exfactory_data_arr=array();
						foreach($exfactory_sql as $value)
						{
							$exfactory_data_arr[$value[csf('po_break_down_id')]]['ex_qnty']=$value[csf('exfac_qty')];
							$exfactory_data_arr[$value[csf('po_break_down_id')]]['all_ex_qnty']=$value[csf('all_exfac_qty')];
							$exfactory_data_arr[$value[csf('po_break_down_id')]]['exfac_pre']=$value[csf('exfac_pre')];
						}
		                $sql_fabric_qty=sql_select( "SELECT a.po_breakdown_id,
							sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =2  AND a.entry_form =18  THEN a.quantity ELSE 0 END ) AS fabric_qty,
							sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_qty,
							sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_qty
							FROM order_wise_pro_details a,inv_transaction b
							WHERE a.trans_id = b.id 
							and b.status_active=1 and a.entry_form in(18,15) and a.quantity!=0 and  b.is_deleted=0  group by a.po_breakdown_id");
		                $fabric_data_arr=array();
		                foreach($sql_fabric_qty as $inf)
		                {
		                	$fabric_data_arr[$inf[csf("po_breakdown_id")]]["fab_iss"]=$inf[csf("fabric_qty")]+$inf[csf("trans_in_qty")]-$inf[csf("trans_out_qty")];
		                }	
		                ?>
		                
								<?
								$file_cond="";
								if(str_replace("'","",$txt_file_no)!="") $file_cond=" and a.file_no=$txt_file_no";
								$file_cond2="";
								if(str_replace("'","",$txt_ref_no)!="") $file_cond2="and a.grouping=$txt_ref_no";
								if(str_replace("'","",$txt_style_ref)!="") $style=" and b.style_ref_no=$txt_style_ref"; else {$style="";}
								if (str_replace("'","",$cbo_ship_status)==0) $ship_status_cond=" and a.shiping_status not in(3)"; else $ship_status_cond="and a.shiping_status=".trim($cbo_ship_status).""; 

		                        if($db_type==0)
		                        {
		                         $sql=sql_select("select a.id as po_id,a.po_number,a.grouping,a.file_no,b.order_uom,b.total_set_qnty,b.company_name,b.job_no_prefix_num,b.buyer_name,b.style_ref_no,a.po_quantity,a.plan_cut as plan_cut,year(b.insert_date) as year,pub_shipment_date as ship_date,a.shiping_status as status 
									from  wo_po_break_down a,wo_po_details_master b 
									where a.job_no_mst=b.job_no $company_name $style $buyer_name $job_cond_id $style_cond $order_cond $ship_status_cond $po_order_cond  $year_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 $file_cond $file_cond2  order by b.buyer_name,b.job_no_prefix_num");	
		                        }
		                        else if($db_type==2)
		                        {
		                       	 
								  $sql=sql_select("select a.id as po_id,a.po_number,a.grouping,a.file_no,b.order_uom,b.total_set_qnty,b.company_name,b.job_no_prefix_num,b.buyer_name,b.style_ref_no,a.po_quantity,a.plan_cut as plan_cut,extract(year from b.insert_date) as year,pub_shipment_date as ship_date,a.shiping_status as status 
								 from  wo_po_break_down a,wo_po_details_master b 
								 where a.job_no_mst=b.job_no $company_name $style $buyer_name $job_cond_id $style_cond $order_cond $ship_status_cond  $po_order_cond  $year_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 $file_cond $file_cond2 order by b.buyer_name,b.job_no_prefix_num");
		                        }
								?>
		                        
		                        <table cellspacing="0" cellpadding="0" border="1" rules="all"  width="2490" class="rpt_table">
				                	<p style=" width:300px; font-weight:bold;">Buyer Wise Summary Part</p>
				                    <thead>
				                        <tr >
				                            <th width="40" rowspan="2">SL</th>
		                                    <th width="200" rowspan="2">Company</th>
				                            <th width="100" rowspan="2">Buyer</th>
				                           
				                            <th width="240" colspan="3">Cutting</th>
				                            <th width="240" colspan="3">EMBL Issue	</th>
				                            <th width="240" colspan="3">EMBL Receive</th>
				                            <th width="240" colspan="3">Sewing Input	</th>
				                            <th width="240" colspan="3">Sewing Output</th>
				                            <th width="240" colspan="3">Iron</th>
				                            <th width="160" colspan="2">Sewing Reject</th>
				                            <th width="240" colspan="3">Finish	</th>
				                            <th colspan="3">Ex- Factory</th>
				                        </tr>
				                        <tr>
				                            <th width="80">Today.</th>
				                            <th width="80" >Total </th>
				                            <th width="80" >WIP Bal. </th>
				                            <th width="80" >Today</th>
				                            <th width="80" >Total</th>
				                            <th width="80" >Issue Bal.</th>
				                            <th width="80" >Today </th>
				                            <th width="80" >Total</th>
				                            <th width="80" >WIP/ Bal</th>
				                            <th width="80" > Today </th>
				                            <th width="80" >Total </th>
				                            <th width="80" >WIP/ Bal.</th>
				                            <th width="80" >Today </th>
				                            <th width="80" >Total </th>
				                            <th width="80" >WIP/ Bal.</th>
				                            <th width="80" >Today </th>
				                            <th width="80" >Total </th>
				                            <th width="80" >Iron Bal.</th>
				                            <th width="80" >Today </th>
				                            <th width="80" >Total</th>
				                            <th width="80" >Today </th>
				                            <th width="80" >Total</th>
				                            <th width="80" >WIP/ Bal.</th>
				                            <th width="80" >Today </th>
				                            <th width="80" >Total</th>
				                            <th  >Ex-fac. Bal.</th>
				                        </tr>
				                    </thead>
				                </table>
		               			<div style="width:2510px; max-height:425px; overflow-y:scroll"   id="scroll_body_summary">
				                    <table class="rpt_table" width="2490" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_summary">
		                            <? 
									
									$summeryDataArrPoQty=array(); 
		                            foreach($sql as $row)	
			                        {
										
										
			  							//if($production_data_arr[$row[csf('po_id')]]['cutting_qnty']!=0 || $production_data_arr[$row[csf('po_id')]]['printing_qnty']!=0 || $production_data_arr[$row[csf('po_id')]]['printreceived_qnty']!=0 || $production_data_arr[$row[csf('po_id')]]['sewingin_qnty']!=0  || $production_data_arr[$row[csf('po_id')]]['sewing_out_qnty']!=0  || $production_data_arr[$row[csf('po_id')]]['iron_qnty']!=0 || $exfactory_data_arr[$row[csf('po_id')]]['ex_qnty']!=0  || $production_data_arr[$row[csf('po_id')]]['finish_qnty']!=0)
										if($production_data_arr[$row[csf('po_id')]]['all_cutting_qnty']!=0 || $production_data_arr[$row[csf('po_id')]]['all_printing_qnty']!=0 || $production_data_arr[$row[csf('po_id')]]['all_printreceived_qnty']!=0 || $production_data_arr[$row[csf('po_id')]]['all_sewingin_qnty']!=0  || $production_data_arr[$row[csf('po_id')]]['all_sewing_out_qnty']!=0  || $production_data_arr[$row[csf('po_id')]]['all_iron_qnty']!=0 || $exfactory_data_arr[$row[csf('po_id')]]['all_ex_qnty']!=0  || $production_data_arr[$row[csf('po_id')]]['all_finish_qnty']!=0)
										{
										
										$key=$row[csf('company_name')].$row[csf('buyer_name')];
										$summeryDataArr[$key]=$row;
										$summeryDataArrPoQty[$key]+=$row[csf('po_quantity')];
										
										
										/*if (str_replace("'","",$txt_po_no)!="")
										{*/
											$all_cut_todayArr[$key]+=$production_data_arr[$row[csf('po_id')]]['all_cutting_qnty'];
											$all_embl_issue_todayArr[$key]+=$production_data_arr[$row[csf('po_id')]]['all_printing_qnty'];
											$all_embl_rcv_todayArr[$key]+=$production_data_arr[$row[csf('po_id')]]['all_printreceived_qnty'];
											$all_sewing_in_todayArr[$key]+=$production_data_arr[$row[csf('po_id')]]['all_sewingin_qnty'];
											$all_sew_out_todayArr[$key]+=$production_data_arr[$row[csf('po_id')]]['all_sewing_out_qnty'];
											$all_iron_todayArr[$key]+=$production_data_arr[$row[csf('po_id')]]['all_iron_qnty'];
											$all_finish_todayArr[$key]+=$production_data_arr[$row[csf('po_id')]]['all_finish_qnty'];
											$all_reject_todayArr[$key]+=$production_data_arr[$row[csf('po_id')]]['all_reject_today'];
											$all_exfactory_qtyArr[$key]+=$exfactory_data_arr[$row[csf('po_id')]]['all_ex_qnty'];
		
										//}
										
										$cut_todayArr[$key]+=$production_data_arr[$row[csf('po_id')]]['cutting_qnty'];
										$embl_issue_todayArr[$key]+=$production_data_arr[$row[csf('po_id')]]['printing_qnty'];
										$embl_rcv_todayArr[$key]+=$production_data_arr[$row[csf('po_id')]]['printreceived_qnty'];
										$sewing_in_todayArr[$key]+=$production_data_arr[$row[csf('po_id')]]['sewingin_qnty'];
										$sew_out_todayArr[$key]+=$production_data_arr[$row[csf('po_id')]]['sewing_out_qnty'];
										$iron_todayArr[$key]+=$production_data_arr[$row[csf('po_id')]]['iron_qnty'];
										$finish_todayArr[$key]+=$production_data_arr[$row[csf('po_id')]]['finish_qnty'];
										$reject_todayArr[$key]+=$production_data_arr[$row[csf('po_id')]]['reject_today'];
										$exfactory_qtyArr[$key]+=$exfactory_data_arr[$row[csf('po_id')]]['ex_qnty'];
										$fabric_issArr[$key]+=$fabric_data_arr[$row[csf('po_id')]]['fab_iss'];
										
										$cut_today_preArr[$key]+=$production_data_arr[$row[csf('po_id')]]['cutting_qnty_pre'];
										$embl_issue_today_preArr[$key]+=$production_data_arr[$row[csf('po_id')]]['printing_qnty_pre'];
										$embl_rcv_today_preArr[$key]+=$production_data_arr[$row[csf('po_id')]]['printreceived_qnty_pre'];
										$sewing_in_today_preArr[$key]+=$production_data_arr[$row[csf('po_id')]]['sewingin_qnty_pre'];
										$sew_out_today_preArr[$key]+=$production_data_arr[$row[csf('po_id')]]['sewing_out_pre'];
										$iron_today_preArr[$key]+=$production_data_arr[$row[csf('po_id')]]['iron_pre'];
										$finish_today_preArr[$key]+=$production_data_arr[$row[csf('po_id')]]['finish_pre'];
										$reject_today_preArr[$key]+=$production_data_arr[$row[csf('po_id')]]['reject_pre'];
										$exfactory_today_preArr[$key]+=$exfactory_data_arr[$row[csf('po_id')]]['exfac_pre'];
										
										}
										
									}
									//var_dump($summeryDataArrPoQty); die;
									$inc=1;
									foreach($summeryDataArr as $key=>$row)
									{
										
										$poQty=$summeryDataArrPoQty[$key];
										
										
									/*	if (str_replace("'","",$txt_po_no)!="")
										{*/
											$all_cut_today=$all_cut_todayArr[$key];	
											$all_embl_issue_today=$all_embl_issue_todayArr[$key];
											$all_embl_rcv_today=$all_embl_rcv_todayArr[$key];
											$all_sewing_in_today=$all_sewing_in_todayArr[$key];
											$all_sew_out_today=$all_sew_out_todayArr[$key];
											$all_iron_today=$all_iron_todayArr[$key];
											$all_finish_today=$all_finish_todayArr[$key];
											$all_reject_today=$all_reject_todayArr[$key];
											$all_exfactory_qty=$all_exfactory_qtyArr[$key];
		
										//}
										$cut_today=$cut_todayArr[$key];	
										$embl_issue_today=$embl_issue_todayArr[$key];
										$embl_rcv_today=$embl_rcv_todayArr[$key];
										$sewing_in_today=$sewing_in_todayArr[$key];
										$sew_out_today=$sew_out_todayArr[$key];
										$iron_today=$iron_todayArr[$key];
										$finish_today=$finish_todayArr[$key];
										$reject_today=$reject_todayArr[$key];	
										
										$exfactory_qty=$exfactory_qtyArr[$key];
										$fabric_iss=$fabric_issArr[$key];
										
										/*if (str_replace("'","",$txt_po_no)!="")
										{*/
											$cut_total=$all_cut_todayArr[$key]+$cut_today_preArr[$key];
											$embl_issue_total=$all_embl_issue_todayArr[$key]+$embl_issue_today_preArr[$key];
											$embl_rcv_total=$all_embl_rcv_todayArr[$key]+$embl_rcv_today_preArr[$key];
											$sewing_in_total=$all_sewing_in_todayArr[$key]+$sewing_in_today_preArr[$key];
											$iron_total=$all_iron_todayArr[$key]+$iron_today_preArr[$key];
											$sew_out_total=$all_sew_out_todayArr[$key]+$sew_out_today_preArr[$key];
											$finish_total=$all_finish_todayArr[$key]+$finish_today_preArr[$key];
											$reject_total=$all_reject_todayArr[$key]+$reject_today_preArr[$key];
											$exfactory_total=$all_exfactory_qtyArr[$key]+$exfactory_today_preArr[$key];
		
										/*}
										else
										{
											$cut_total=$cut_todayArr[$key]+$cut_today_preArr[$key];
											$embl_issue_total=$embl_issue_todayArr[$key]+$embl_issue_today_preArr[$key];
											$embl_rcv_total=$embl_rcv_todayArr[$key]+$embl_rcv_today_preArr[$key];
											$sewing_in_total=$sewing_in_todayArr[$key]+$sewing_in_today_preArr[$key];
											$iron_total=$iron_todayArr[$key]+$iron_today_preArr[$key];
											$sew_out_total=$sew_out_todayArr[$key]+$sew_out_today_preArr[$key];
											$finish_total=$finish_todayArr[$key]+$finish_today_preArr[$key];
											$reject_total=$reject_todayArr[$key]+$reject_today_preArr[$key];
										}*/
										
									//if($cut_today!=0 || $embl_issue_today!=0 || $embl_rcv_today!=0 || $sewing_in_today!=0  || $sew_out_today!=0  || $iron_today!=0 || $exfactory_qty!=0  || $finish_today!=0)
									if($cut_total!=0 || $embl_issue_total!=0 || $embl_rcv_total!=0 || $sewing_in_total!=0  || $sew_out_total!=0  || $iron_total!=0 || $exfactory_total!=0  || $finish_total!=0)
									{
									
										$job_total+=$poQty;
										
										$grand_cut+=$cut_today;
										$grand_embl_issue+=$embl_issue_today;
										$grand_embl_rec+=$embl_rcv_today;
										$grand_sew_in+=$sewing_in_today;
										$grand_sew_out+=$sew_out_today;
										$grand_iron+=$iron_today;
										$grand_finish+=$finish_today;
										$grand_reject+=$reject_today;
										/*if (str_replace("'","",$txt_po_no)!="")
										{
											$grand_cut_total+=$cut_total;
											$grand_embl_iss_total+=$embl_issue_total;
											$grand_embl_rev_total+=$embl_rcv_total;
											$grand_sew_in_total+=$sewing_in_total;
											$grand_sew_out_total+=$sew_out_total;
											$grand_iron_total+=$iron_total;
											$grand_finish_total+=$finish_total;
											$grand_reject_total+=$reject_total;
										}
										else
										{*/
											$grand_cut_total+=$cut_total;
											$grand_embl_iss_total+=$embl_issue_total;
											$grand_embl_rev_total+=$embl_rcv_total;
											$grand_sew_in_total+=$sewing_in_total;
											$grand_sew_out_total+=$sew_out_total;
											$grand_iron_total+=$iron_total;
											$grand_finish_total+=$finish_total;
											$grand_reject_total+=$reject_total;
										//}
										
										$grand_exfactory+=$exfactory_qty;
										$grand_exfa_total+=$exfactory_total;
										
										
										if ($inc%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>
		                            	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $inc; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $inc; ?>">
		                                    <td width="40"><? echo $inc;?></td>
		                                    <td width="200" align="center"><? echo $company_library[$row[csf('company_name')]]; ?></td>
		                                    <td width="100" align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
		                                    <td width="80" align="right"><? if($cut_today>0){echo number_format($cut_today);} ?></td>
		                                    <td width="80" align="right"><? if($cut_total>0){echo number_format($cut_total);}?></td>
		                                    <td width="80" align="right"><? if(($poQty-$cut_total)>0 || ($poQty-$cut_total)<0){echo number_format(($poQty-$cut_total));} ?></td>
		                                    <td width="80" align="right"><?   if($embl_issue_today>0){echo number_format($embl_issue_today);}?></td>
		                                    <td width="80" align="right"><?   if($embl_issue_total>0){echo number_format($embl_issue_total);} ?></td>
		                                    <td width="80" align="right"><?   if(($cut_total-$embl_issue_total)>0 || ($cut_total-$embl_issue_total)<0){echo number_format(($cut_total-$embl_issue_total));} ?></td>
		                                    <td width="80" align="right"><?  if($embl_rcv_today>0){ echo number_format($embl_rcv_today);} ?></td>
		                                    <td width="80" align="right"><?  if($embl_rcv_total>0){echo number_format($embl_rcv_total);} ?></td>
		                                    <td width="80" align="right"><?  if(($embl_issue_total-$embl_rcv_total)>0 || ($embl_issue_total-$embl_rcv_total)<0){ echo number_format(($embl_issue_total-$embl_rcv_total));} ?></td>
		                                    <td width="80" align="right"><?   if($sewing_in_today>0){echo number_format($sewing_in_today);} ?></td>
		                                    <td width="80" align="right"><?   if($sewing_in_total>0){echo number_format($sewing_in_total);} ?></td>
		                                    <td width="80" align="right"><?   if(($cut_total-$sewing_in_total)>0 || ($cut_total-$sewing_in_total)<0){echo number_format(($cut_total-$sewing_in_total));} ?></td>
		                                    <td width="80" align="right"><?  if($sew_out_today>0){echo number_format($sew_out_today);} ?></td>
		                                    <td width="80" align="right"><?  if($sew_out_total>0){ echo number_format($sew_out_total);} ?></td>
		                                    <td width="80" align="right"><?  if(($sewing_in_total-$sew_out_total)>0 || ($sewing_in_total-$sew_out_total)<0){ echo number_format(($sewing_in_total-$sew_out_total));} ?></td>
		                                    <td width="80" align="right"><?  if($iron_today>0){ echo number_format($iron_today);} ?></td>
		                                    <td width="80" align="right"><?  if($iron_total>0){  echo number_format($iron_total);} ?></td>
		                                    <td width="80" align="right"><?  if(($sew_out_total-$iron_total)>0 || ($sew_out_total-$iron_total)<0){ echo number_format(($sew_out_total-$iron_total));} ?></td>
		                                    <td width="80" align="right"><?  if($reject_today>0){ echo number_format($reject_today);} ?></td>
		                                    <td width="80" align="right"><?  if($reject_total>0){  echo number_format($reject_total);} ?></td>
		                                    <td width="80" align="right"><?  if($finish_today>0){  echo number_format($finish_today);} ?></td>
		                                    <td width="80" align="right"><?  if($finish_total>0){  echo number_format($finish_total);} ?></td>
		                                    <td width="80" align="right"><?  if(($sew_out_total-$finish_total)>0 || ($sew_out_total-$finish_total)<0){ echo number_format(($sew_out_total-$finish_total));} ?></td>
		                                   
		                                    <td width="80" align="right"><?  if($exfactory_qty>0){ echo number_format($exfactory_qty);} ?></td>
		                                    <td width="80" align="right"><?  if($exfactory_total>0){  echo number_format(($exfactory_total));} ?></td>
		                                    <td  align="right" ><? if(($poQty-$exfactory_total)>0 || ($poQty-$exfactory_total)<0){ echo number_format(($poQty-$exfactory_total));} ?></td>
										</tr> 
		                                <? 
										$inc++; 
										}
									} ?>
									</table>
				                 </div>
		                         <table class="rpt_table" width="2490" cellpadding="0" cellspacing="0" border="1" rules="all">
		                        <tfoot>
		                            <tr>
		                                <th width="40"><? // echo $i;?></th>
		                                <th width="200" align="right"></th>
		                                <th width="100" align="right"><strong>Grand Total:</strong></th>
		                                <th width="80" align="right"><?  echo number_format($grand_cut); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_cut_total); ?></th>
		                                <th width="80" align="right"><?  echo number_format(($job_total-$grand_cut_total)); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_embl_issue); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_embl_iss_total);?></th>
		                                <th width="80" align="right"><?  echo number_format(($grand_cut_total-$grand_embl_iss_total)); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_embl_rec); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_embl_rev_total); ?></th>
		                                <th width="80" align="right"><?  echo number_format(($grand_embl_iss_total-$grand_embl_rev_total)); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_sew_in); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_sew_in_total); ?></th>
		                                <th width="80" align="right"><?  echo number_format(($grand_cut_total-$grand_sew_in_total)); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_sew_out); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_sew_out_total); ?></th>
		                                <th width="80" align="right"><?  echo number_format(($grand_sew_in_total-$grand_sew_out_total)); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_iron); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_iron_total); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_sew_out_total-$grand_iron_total); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_reject); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_reject_total); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_finish); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_finish_total); ?></th>
		                                <th width="80" align="right"><?  echo number_format(($grand_sew_out_total-$grand_finish_total));?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_exfactory); ?></th>
		                                <th width="80" align="right"><?  echo number_format($grand_exfa_total); ?></th>
		                                <th  align="right"><?  echo number_format(($job_total-$grand_exfa_total)); ?></th>
		                            </tr> 
		                            <tr>
		                            	<th colspan=""></th>
		                            </tr>
		                        </tfoot>   
		                    </table>     
		                        <br>
		                <table cellspacing="0" cellpadding="0" border="1" rules="all"  width="3590" class="rpt_table">
		                    <p style="float:left; width:300px; font-weight:bold;">Buyer Wise Details Part</p>
		                    <thead>
		                        <tr >
		                            <th width="40" rowspan="2">SL</th>
		                            <th width="100" rowspan="2">Buyer</th>
		                            <th width="100" rowspan="2">File No.</th>
		                            <th width="100" rowspan="2">Int Ref. No.</th>
		                            <th width="140" rowspan="2">Style</th>
		                            <th width="60" rowspan="2">Job No</th>
		                            <th width="50" rowspan="2">Year</th>
		                            <th width="200" rowspan="2">Order No</th>
		                            <th width="100" rowspan="2">Order Qty.</th>
		                            <th width="50" rowspan="2">UOM</th>
		                            <th width="100" rowspan="2">Order Qty Pcs</th>
		                            <th width="100" rowspan="2">Ship Date</th>
		                            <th width="120" rowspan="2">Shiping Status</th>
		                            <th width="100" rowspan="2">Fin. Fab. Issued</th>
		                            <th width="100" rowspan="2">Possible Cut Qty.</th>
		                            <th width="240" colspan="3">Cutting</th>
		                            <th width="240" colspan="3">EMBL Issue	</th>
		                            <th width="240" colspan="3">EMBL Receive</th>
		                            <th width="240" colspan="3">Sewing Input	</th>
		                            <th width="240" colspan="3">Sewing Output</th>
		                            <th width="240" colspan="3">Iron</th>
		                            <th width="160" colspan="2">Sewing Reject</th>
		                            <th width="240" colspan="3">Finish	</th>
		                            <th colspan="3">Ex- Factory</th>
		                        </tr>
		                        <tr>
		                            <th width="80">Today.</th>
		                            <th width="80" >Total </th>
		                            <th width="80" >WIP Bal. </th>
		                            <th width="80" >Today</th>
		                            <th width="80" >Total</th>
		                            <th width="80" >Issue Bal.</th>
		                            <th width="80" >Today </th>
		                            <th width="80" >Total</th>
		                            <th width="80" >WIP/ Bal</th>
		                            <th width="80" > Today </th>
		                            <th width="80" >Total </th>
		                            <th width="80" >WIP/ Bal.</th>
		                            <th width="80" >Today </th>
		                            <th width="80" >Total </th>
		                            <th width="80" >WIP/ Bal.</th>
		                            <th width="80" >Today </th>
		                            <th width="80" >Total </th>
		                            <th width="80" >Iron Bal.</th>
		                            <th width="80" >Today </th>
		                            <th width="80" >Total</th>
		                            <th width="80" >Today </th>
		                            <th width="80" >Total</th>
		                            <th width="80" >WIP/ Bal.</th>
		                            <th width="80" >Today </th>
		                            <th width="80" >Total</th>
		                            <th  >Ex-fac. Bal.</th>
		                        </tr>
		                    </thead>
		                </table>
						<div style="width:3610px; max-height:425px; overflow-y:scroll"   id="scroll_body">
		                    <table class="rpt_table" width="3590" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
								<?
								//die;
		                        $grand_fabric_iss=$grand_embl_rec=$grand_embl_rev_total=$grand_plan_cut=$job_total=$exfactory_total=0;
		                        $cut_today=$embl_issue_today=$embl_rcv_today=$sewing_in_today=$sew_out_today=$iron_today=$finish_today=$reject_today=$exfactory_qty=0;
		                        $fabric_iss=$cut_total=$embl_issue_total=$embl_rcv_total=$sewing_in_total=$iron_total=$sew_out_total=$finish_total=$reject_total=0;
		                        $grand_cut=$grand_cut_total=$grand_embl_issue=$grand_embl_iss_total=$grand_sew_in=$grand_sew_in_total=$grand_sew_out=$grand_sew_out_total=0;
		                        $grand_iron=$grand_iron_total=$grand_finish=$grand_finish_total=$grand_reject=$grand_reject_total=$grand_exfactory=$grand_exfa_total=0; 
		                        $tot_rows=count($sql);
		                        $i=1;		
		                        foreach($sql as $row)	
		                        {
									
									
									$all_cut_today=$production_data_arr[$row[csf('po_id')]]['all_cutting_qnty'];
									$all_embl_issue_today=$production_data_arr[$row[csf('po_id')]]['all_printing_qnty'];
									$all_embl_rcv_today=$production_data_arr[$row[csf('po_id')]]['all_printreceived_qnty'];
									$all_sewing_in_today=$production_data_arr[$row[csf('po_id')]]['all_sewingin_qnty'];
									$all_sew_out_today=$production_data_arr[$row[csf('po_id')]]['all_sewing_out_qnty'];
									$all_iron_today=$production_data_arr[$row[csf('po_id')]]['all_iron_qnty'];
									$all_finish_today=$production_data_arr[$row[csf('po_id')]]['all_finish_qnty'];
									$all_reject_today=$production_data_arr[$row[csf('po_id')]]['all_reject_today'];
									$all_exfactory_qty=$exfactory_data_arr[$row[csf('po_id')]]['all_ex_qnty'];
									
									$cut_today=$production_data_arr[$row[csf('po_id')]]['cutting_qnty'];
									$embl_issue_today=$production_data_arr[$row[csf('po_id')]]['printing_qnty'];
									$embl_rcv_today=$production_data_arr[$row[csf('po_id')]]['printreceived_qnty'];
									$sewing_in_today=$production_data_arr[$row[csf('po_id')]]['sewingin_qnty'];
									$sew_out_today=$production_data_arr[$row[csf('po_id')]]['sewing_out_qnty'];
									$iron_today=$production_data_arr[$row[csf('po_id')]]['iron_qnty'];
									$finish_today=$production_data_arr[$row[csf('po_id')]]['finish_qnty'];
									$reject_today=$production_data_arr[$row[csf('po_id')]]['reject_today'];
									$exfactory_qty=$exfactory_data_arr[$row[csf('po_id')]]['ex_qnty'];
									$fabric_iss=$fabric_data_arr[$row[csf('po_id')]]['fab_iss'];
									
									$cut_total=$production_data_arr[$row[csf('po_id')]]['cutting_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['all_cutting_qnty'];
									$embl_issue_total=$production_data_arr[$row[csf('po_id')]]['printing_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['all_printing_qnty'];
									$embl_rcv_total=$production_data_arr[$row[csf('po_id')]]['printreceived_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['all_printreceived_qnty'];
									$sewing_in_total=$production_data_arr[$row[csf('po_id')]]['sewingin_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['all_sewingin_qnty'];
									$iron_total=$production_data_arr[$row[csf('po_id')]]['iron_pre']+$production_data_arr[$row[csf('po_id')]]['all_iron_qnty'];
									$sew_out_total=$production_data_arr[$row[csf('po_id')]]['sewing_out_pre']+$production_data_arr[$row[csf('po_id')]]['all_sewing_out_qnty'];
									$finish_total=$production_data_arr[$row[csf('po_id')]]['finish_pre']+$production_data_arr[$row[csf('po_id')]]['all_finish_qnty'];
									$reject_total=$production_data_arr[$row[csf('po_id')]]['all_reject_today']+$production_data_arr[$row[csf('po_id')]]['reject_pre'];
									$exfactory_total=$exfactory_data_arr[$row[csf('po_id')]]['all_ex_qnty']+$exfactory_data_arr[$row[csf('po_id')]]['exfac_pre'];
									//if($fabric_iss!=0 || ($cut_today!=0 || $embl_issue_today!=0 || $embl_rcv_today!=0 || $sewing_in_today!=0  || $sew_out_today!=0  || $iron_today!=0 || $exfactory_qty!=0  || $finish_today!=0))
									//if($cut_today!=0 || $embl_issue_today!=0 || $embl_rcv_today!=0 || $sewing_in_today!=0  || $sew_out_today!=0  || $iron_today!=0 || $exfactory_qty!=0  || $finish_today!=0)
									if($cut_total!=0 || $embl_issue_total!=0 || $embl_rcv_total!=0 || $sewing_in_total!=0  || $sew_out_total!=0  || $iron_total!=0 || $exfactory_total!=0  || $finish_total!=0)
									{
										$possible_cut_pcs=0;
										$possible_cut_pcs=$fabric_iss/$result_consumtion[$row[csf('po_id')]];
										$grand_possible_cut_pcs+=$possible_cut_pcs;
										$grand_cut+=$cut_today;
										$grand_cut_total+=$cut_total;
										$grand_embl_issue+=$embl_issue_today;
										$grand_embl_iss_total+=$embl_issue_total;
										$grand_embl_rec+=$embl_rcv_today;
										$grand_embl_rev_total+=$embl_rcv_total;
										$grand_sew_in+=$sewing_in_today;
										$grand_sew_in_total+=$sewing_in_total;
										$grand_sew_out+=$sew_out_today;
										$grand_sew_out_total+=$sew_out_total;
										$grand_iron+=$iron_today;
										$grand_iron_total+=$iron_total;
										$grand_finish+=$finish_today;
										$grand_finish_total+=$finish_total;
										$grand_reject+=$reject_today;
										$grand_reject_total+=$reject_total;
										$grand_exfactory+=$exfactory_qty;
										$grand_exfa_total+=$exfactory_total;
										$grand_fabric_iss+=$fabric_iss;
										$grand_plan_cut+=$row[csf('plan_cut')];
										$job_total+=$row[csf('po_quantity')];
										$txt_date=str_replace("'","",$txt_date_from);
										$diff=datediff("d",$txt_date,$row[csf('ship_date')]);
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
		                                    <td width="40"><? echo $i;?></td>
		                                    <td width="100" align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
		                                    <td width="100" align="center"><? echo $row[csf('file_no')]; ?></td>
		                                    <td width="100" align="center"><? echo $row[csf('grouping')]; ?></td>
		                                    <td width="140" align="center"><p><? echo $row[csf('style_ref_no')];?>&nbsp;</p></td>
		                                    <td width="60" align="center"><?  echo $row[csf('job_no_prefix_num')];?></td>
		                                    <td width="50" align="center"><?  echo $row[csf('year')];?></td>
		                                    <td width="200" align="center"><p><?  echo $row[csf('po_number')];?></p></td>
		                                    <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'order_quantity',800,250)"><? echo number_format($row[csf('po_quantity')]);?></a></td>
		                                    <td width="50" align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]];?></td>
		                                    <td width="100" align="right"><? echo $row[csf('total_set_qnty')]*$row[csf('po_quantity')];?></td>
		                                    <td width="100" align="center"><? echo change_date_format($row[csf('ship_date')]);?></td>
		                                    <td width="120" align="center"><p><? echo $shipment_status[$row[csf('status')]]; ?></p></td>
		                                    <td width="100" align="right"><? echo number_format($fabric_iss,2); ?></td>
		                                    <td width="100" align="right" title="Fabric Issue/Consumtion (<? echo number_format($result_consumtion[$row[csf('po_id')]],5); ?>)"><?  echo number_format($possible_cut_pcs); ?></td>
		                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'today_order_cutting',850,250)"><? if($cut_today>0){echo number_format($cut_today);}   ?></a></td>
		                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'today_order_cutting',850,250)"><? if($cut_total>0){echo number_format($cut_total);}?></a></td>
		                                    <td width="80" align="right"><?   if(($row[csf('po_quantity')]-$cut_total)>0 || ($row[csf('po_quantity')]-$cut_total)<0){echo number_format(($row[csf('po_quantity')]-$cut_total));} ?></td>
		                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_issue_order',850,250)"><?   if($embl_issue_today>0){echo number_format($embl_issue_today);}?></a></td>
		                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_issue_order',850,250)"><?   if($embl_issue_total>0){echo number_format($embl_issue_total);} ?></a></td>
		                                    <td width="80" align="right"><?   if(($cut_total-$embl_issue_total)>0 || ($cut_total-$embl_issue_total)<0){echo number_format(($cut_total-$embl_issue_total));} ?></td>
		                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_receive_order',850,250)"><?  if($embl_rcv_today>0){ echo number_format($embl_rcv_today);} ?></a></td>
		                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_receive_order',850,250)"><?   if($embl_rcv_total>0){echo number_format($embl_rcv_total);} ?></a></td>
		                                    <td width="80" align="right"><?   if(($embl_issue_total-$embl_rcv_total)>0 || ($embl_issue_total-$embl_rcv_total)<0){ echo number_format(($embl_issue_total-$embl_rcv_total));} ?></td>
		                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'sewing_input_order',850,250)"><?   if($sewing_in_today>0){echo number_format($sewing_in_today);} ?></a></td>
		                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'sewing_input_order',850,250)"><?   if($sewing_in_total>0){echo number_format($sewing_in_total);} ?></a></td>
		                                    <td width="80" align="right"><?    if(($cut_total-$sewing_in_total)>0 || ($cut_total-$sewing_in_total)<0){echo number_format(($cut_total-$sewing_in_total));} ?></td>
		                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'sewing_output_order',850,250)"><?   if($sew_out_today>0){echo number_format($sew_out_today);} ?></a></td>
		                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'sewing_output_order',850,250)"><?  if($sew_out_total>0){ echo number_format($sew_out_total);} ?></a></td>
		                                    <td width="80" align="right"><?  if(($sewing_in_total-$sew_out_total)>0 || ($sewing_in_total-$sew_out_total)<0){ echo number_format(($sewing_in_total-$sew_out_total));} ?></td>
		                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'iron__entry_order',850,300)"><?  if($iron_today>0){ echo number_format($iron_today);} ?></a></td>
		                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'iron__entry_order',850,300)"><?  if($iron_total>0){  echo number_format($iron_total);} ?></a></td>
		                                    <td width="80" align="right"><?   if(($sew_out_total-$iron_total)>0 || ($sew_out_total-$iron_total)<0){ echo number_format(($sew_out_total-$iron_total));} ?></td>
		                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'reject_qty_order',700,250)"><?  if($reject_today>0){ echo number_format($reject_today);} ?></a></td>
		                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'reject_qty_order',700,250)"><?  if($reject_total>0){  echo number_format($reject_total);} ?></a></td>
		                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'finish_qty_order',850,300)"><?  if($finish_today>0){  echo number_format($finish_today);} ?></a></td>
		                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'finish_qty_order',850,300)"><?  if($finish_total>0){  echo number_format($finish_total);} ?></a></td>
		                                    <td width="80" align="right"><?  if(($sew_out_total-$finish_total)>0 || ($sew_out_total-$finish_total)<0){ echo number_format(($sew_out_total-$finish_total));} ?></td>
		                                   
		                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'exfactrory__entry_order',850,250)"><?  if($exfactory_qty>0){ echo number_format($exfactory_qty);} ?></a></td>
		                                    <td width="80" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'exfactrory__entry_order',850,250)"><?  if($exfactory_total>0){  echo number_format(($exfactory_total));} ?></a></td>
		                                    <td  align="right" ><? if(($row[csf('po_quantity')]-$exfactory_total)>0 || ($row[csf('po_quantity')]-$exfactory_total)<0){ echo number_format(($row[csf('po_quantity')]-$exfactory_total));} ?></td>
										</tr>    
										<?
										$i++;
									}
		                        }
								
								
		                        ?>
		                        
		                 </table>
		                 </div>       
		                <table class="rpt_table" width="3590" cellpadding="0" cellspacing="0" border="1" rules="all">
		                        <tfoot>
		                            <tr>
		                                <th width="40"><? // echo $i;?></th>
		                                <th width="100"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></th>
		                                <th width="100"></th>
		                                <th width="100"></th>
		                                <th width="140"><strong>Grand Total:</strong></td>
		                                <th width="60"></th>
		                                <th width="50"><? //echo $pro_date_sql_row[csf("po_number")];?></th>
		                                <th width="200"><? //echo $pro_date_sql_row[csf("po_number")];?></th>
		                                <th width="100" align="right" id="value_job_total"><?  echo number_format($job_total); ?></th>
		                                <th width="50"></th>
		                                <th width="100"></th>
		                                <th width="100"> </th>
		                                <th width="120" id="value_fabric_issues"><? //echo number_format($grand_fabric_iss); ?></th>
		                                <th width="100" align="right" id="value_plan_cut_"><? //echo number_format($grand_possible_cut_pcs); ?></th>
		                                <th width="100"></th>
		                                <th width="80" align="right" id="value_cut_today"><?  echo number_format($grand_cut); ?></th>
		                                <th width="80" align="right" id="value_cut_total"><?  echo number_format($grand_cut_total); ?></th>
		                                <th width="80" align="right" id="value_cut_bal"><?  echo number_format(($job_total-$grand_cut_total)); ?></th>
		                                <th width="80" align="right" id="value_embl_iss"><?  echo number_format($grand_embl_issue); ?></th>
		                                <th width="80" align="right" id="value_embl_iss_total"><?  echo number_format($grand_embl_iss_total);?></th>
		                                <th width="80" align="right" id="value_embl_iss_bal"><?  echo number_format(($grand_cut_total-$grand_embl_iss_total)); ?></th>
		                                <th width="80" align="right" id="value_embl_rec"><?  echo number_format($grand_embl_rec); ?></th>
		                                <th width="80" align="right" id="value_embl_rec_total"><?  echo number_format($grand_embl_rev_total); ?></th>
		                                <th width="80" align="right" id="value_embl_rec_bal"><?  echo number_format(($grand_embl_iss_total-$grand_embl_rev_total)); ?></th>
		                                <th width="80" align="right" id="value_sew_in"><?  echo number_format($grand_sew_in); ?></th>
		                                <th width="80" align="right" id="value_sew_in_to"><?  echo number_format($grand_sew_in_total); ?></th>
		                                <th width="80" align="right" id="value_sew_in_bal"><?  echo number_format(($grand_cut_total-$grand_sew_in_total)); ?></th>
		                                <th width="80" align="right" id="value_sew_out"><?  echo number_format($grand_sew_out); ?></th>
		                                <th width="80" align="right" id="value_sew_out_total"><?  echo number_format($grand_sew_out_total); ?></th>
		                                <th width="80" align="right" id="value_sew_out_bal"><?  echo number_format(($grand_sew_in_total-$grand_sew_out_total)); ?></th>
		                                <th width="80" align="right" id="value_iron"><?  echo number_format($grand_iron); ?></th>
		                                <th width="80" align="right" id="value_iron_to"><?  echo number_format($grand_iron_total); ?></th>
		                                <th width="80" align="right" id="value_iron_bal"><?  echo number_format($grand_sew_out_total-$grand_iron_total); ?></th>
		                                <th width="80" align="right" id="value_reject"><?  echo number_format($grand_reject); ?></th>
		                                <th width="80" align="right" id="value_reject_to"><?  echo number_format($grand_reject_total); ?></th>
		                                <th width="80" align="right" id="value_finish"><?  echo number_format($grand_finish); ?></th>
		                                <th width="80" align="right" id="value_finish_to"><?  echo number_format($grand_finish_total); ?></th>
		                                <th width="80" align="right" id="value_finish_bal"><?  echo number_format(($grand_sew_out_total-$grand_finish_total));?></th>
		                                <th width="80" align="right" id="value_exfactory"><?  echo number_format($grand_exfactory); ?></th>
		                                <th width="80" align="right" id="value_exfactory_to"><?  echo number_format($grand_exfa_total); ?></th>
		                                <th  align="right" id="value_exfac_bal"><?  echo number_format(($job_total-$grand_exfa_total)); ?></th>
		                            </tr> 
		                            <tr>
		                            	<th colspan=""></th>
		                            </tr>
		                        </tfoot>   
		                    </table>     
					</fieldset>
					<?	
				}
			}
	}
	else if(str_replace("'","",$report_type)==2)
	{
		if(str_replace("'","",$cbo_search_by)==4)
		{
			//echo "kausar";
			$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_name and variable_list=23 and is_deleted=0 and status_active=1");
			?>
			<fieldset style="width:4200px;">
                <table width="1880"  cellspacing="0"   >
                    <tr class="form_caption" style="border:none;">
                        <td colspan="29" align="center" style="border:none;font-size:14px; font-weight:bold" >
                        Daily Order Wise Garments Production Status Report</td>
                    </tr>
                    <tr style="border:none;">
                        <td colspan="29" align="center" style="border:none; font-size:16px; font-weight:bold">
                        Company Name:<? 
							$com_arr=explode(",",str_replace("'","",$cbo_company_name));
							$comName="";
							foreach($com_arr as $comID)
							{
								$comName.=$company_library[$comID].',';
							}
							echo chop($comName,",");
							//echo $company_library[str_replace("'","",$cbo_company_name)]; 
						?>                                
                        </td>
                    </tr>
                    <tr style="border:none;">
                        <td colspan="29" align="center" style="border:none;font-size:12px; font-weight:bold">
                        <? echo "Date: ". str_replace("'","",$txt_date_from) ;?>
                        </td>
                    </tr>
                </table>
				<br />	
                <table class="rpt_table" width="4250" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr >
                            <th width="40"  rowspan="2">SL</th>
                            <th width="100" rowspan="2">Buyer</th>
                            <th width="150" rowspan="2">Style</th>
                            <th width="60"  rowspan="2">Job No</th>
                            <th width="50"  rowspan="2">Year</th>
                            <th width="140" rowspan="2">Order No</th>
                            <th width="100" rowspan="2">Gmt. Item</th>
                            <th width="100" rowspan="2">Order Qty.</th>
                            <th width="100" rowspan="2">Ship Date</th>
                            <th width="100" rowspan="2">Shiping Status</th>
                            <th width="70" rowspan="2">Fin. Fab. Issued</th>
                            <th width="100"  rowspan="2">Fin  /Pic</th>
                            <th width="100" rowspan="2">Possible Cut Qty.</th>
                            <th width="210" colspan="3">Cutting</th>
                            <th width="210" colspan="3">Print issue</th>
                            <th width="210" colspan="3">Print Receive</th>
                            <th width="210" colspan="3">Embro. Issue</th>
                            <th width="210" colspan="3">Embro. Rcv</th>
                            <th width="210" colspan="3">Special Works. Issue</th>
                            <th width="210" colspan="3">Special Works. Rcv</th>
                            <th width="210" colspan="3">Sewing Input	</th>
                            <th width="100" rowspan="2">Sewing Line</th>
                            <th width="210" colspan="3">Sewing Output</th>
                            <th width="210" colspan="3">Wash. Issue</th>
                            <th width="210" colspan="3">Wash. Rcv</th>
                            <th width="210" colspan="3">Iron</th>
                            <th width="210" colspan="3">Finish	</th>
                            <th colspan="3">Ex- Factory</th>
                        </tr>
                        <tr>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                        </tr>
                    </thead>
                </table>
			<?
			$sew_line_arr=array();
			if($db_type==0)
			{
				$sql_line=sql_select("select group_concat(distinct a.sewing_line) as line_id,a.po_break_down_id from pro_garments_production_mst a where  a.production_type='4'  group by a.po_break_down_id");
			}
			else if($db_type==2)
			{
				$sql_line=sql_select("select listagg(cast(a.sewing_line as varchar2(2000)),',') within group (order by a.sewing_line) as line_id,a.po_break_down_id from pro_garments_production_mst a where  a.production_type='4'  group by a.po_break_down_id");
			}
			foreach($sql_line as $row_sew)
			{
				$sew_line_arr[$row_sew[csf('po_break_down_id')]]['line']= implode(',',array_unique(explode(',',$row_sew[csf('line_id')])));
			}
			unset($sql_line);
			$production_data_arr=array();		
			$production_mst_sql= sql_select("SELECT po_break_down_id,
				sum(CASE WHEN production_type ='1' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty,
				sum(CASE WHEN production_type ='2' and embel_name=1  and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS printing_qnty,
				sum(CASE WHEN production_type ='3' and embel_name=1  and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS printreceived_qnty,
				sum(CASE WHEN production_type ='2' and embel_name=2  and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS embl_qnty,
				sum(CASE WHEN production_type ='3' and embel_name=2  and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS emblreceive_qnty,
				sum(CASE WHEN production_type ='2'  and embel_name=3  and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS wash_qnty,
				sum(CASE WHEN production_type ='3' and embel_name=3  and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS washreceive_qnty,
				sum(CASE WHEN production_type ='2' and embel_name=4  and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sp_qnty,
				sum(CASE WHEN production_type ='3' and embel_name=4  and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS spre_qnty,
				sum(CASE WHEN production_type ='2' and embel_name=1  and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS printing_qnty_pre,
				sum(CASE WHEN production_type ='3' and embel_name=1  and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS printreceived_qnty_pre,
				sum(CASE WHEN production_type ='2' and embel_name=2  and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS embl_qnty_pre,
				sum(CASE WHEN production_type ='3' and embel_name=2  and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS emblreceived_qnty_pre,
				sum(CASE WHEN production_type ='2' and embel_name=3  and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS wash_qnty_pre,
				sum(CASE WHEN production_type ='3' and embel_name=3  and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS wash_received_qnty_pre,
				sum(CASE WHEN production_type ='2' and embel_name=4  and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sp_qnty_pre,
				sum(CASE WHEN production_type ='3' and embel_name=4  and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS spreceived_qnty_pre,				
				sum(CASE WHEN production_type ='4' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewingin_qnty,
				sum(CASE WHEN production_type ='5' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewing_out_qnty,
				sum(CASE WHEN production_type ='7' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS iron_qnty,
				sum(CASE WHEN production_type ='8' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS finish_qnty,
				sum(CASE WHEN production_type ='5' and production_date=".$txt_date_from." THEN reject_qnty ELSE 0 END) AS reject_today,
				sum(CASE WHEN production_type ='1' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty_pre,
				sum(CASE WHEN production_type ='4' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewingin_qnty_pre,
				sum(CASE WHEN production_type ='5' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewing_out_pre,
				sum(CASE WHEN production_type ='7' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS iron_pre,
				sum(CASE WHEN production_type ='8' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS finish_pre,
				sum(CASE WHEN production_type ='5' and production_date<".$txt_date_from." THEN reject_qnty ELSE 0 END) AS reject_pre
				from 
				pro_garments_production_mst 
				where  
				is_deleted=0 and status_active=1 
				group by po_break_down_id "); //reject_qnty
			foreach($production_mst_sql as $val)
			{
				$production_data_arr[$val[csf('po_break_down_id')]]['cutting_qnty']=$val[csf('cutting_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printing_qnty']=$val[csf('printing_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printreceived_qnty']=$val[csf('printreceived_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['embl_qnty']=$val[csf('embl_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['emblreceive_qnty']=$val[csf('emblreceive_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['wash_qnty']=$val[csf('wash_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['washreceive_qnty']=$val[csf('washreceive_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sp_qnty']=$val[csf('sp_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['spre_qnty']=$val[csf('spre_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printing_qnty_pre']=$val[csf('printing_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printreceived_qnty_pre']=$val[csf('printreceived_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['embl_qnty_pre']=$val[csf('embl_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['emblreceived_qnty_pre']=$val[csf('emblreceived_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['wash_qnty_pre']=$val[csf('wash_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['wash_received_qnty_pre']=$val[csf('wash_received_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sp_qnty_pre']=$val[csf('sp_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['spreceived_qnty_pre']=$val[csf('spreceived_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewingin_qnty']=$val[csf('sewingin_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewing_out_qnty']=$val[csf('sewing_out_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['iron_qnty']=$val[csf('iron_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['finish_qnty']=$val[csf('finish_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['reject_today']=$val[csf('reject_today')];
				$production_data_arr[$val[csf('po_break_down_id')]]['cutting_qnty_pre']=$val[csf('cutting_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewingin_qnty_pre']=$val[csf('sewingin_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewing_out_pre']=$val[csf('sewing_out_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['iron_pre']=$val[csf('iron_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['finish_pre']=$val[csf('finish_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['reject_pre']=$val[csf('reject_pre')];	
			}
			unset($production_mst_sql);
			//print_r($production_data_arr);
			$exfactory_sql=sql_select("SELECT po_break_down_id,
				sum(CASE WHEN  ex_factory_date=".$txt_date_from." AND entry_form!=85  THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN  ex_factory_date=".$txt_date_from." AND entry_form=85  THEN ex_factory_qnty ELSE 0 END) AS exfac_qty,
				sum(CASE WHEN  ex_factory_date<".$txt_date_from." AND entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN  ex_factory_date<".$txt_date_from." AND entry_form=85 THEN ex_factory_qnty ELSE 0 END) AS exfac_pre
				from 
				pro_ex_factory_mst 
				where  
				is_deleted=0 and status_active=1
				group by po_break_down_id ");
			$exfactory_data_arr=array();
			foreach($exfactory_sql as $value)
			{
				$exfactory_data_arr[$value[csf('po_break_down_id')]]['ex_qnty']=$value[csf('exfac_qty')];
				$exfactory_data_arr[$value[csf('po_break_down_id')]]['exfac_pre']=$value[csf('exfac_pre')];
			}
			unset($exfactory_sql);
			$sql_fabric_qty=sql_select( "SELECT a.po_breakdown_id,
				sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =2  AND a.entry_form =18  THEN a.quantity ELSE 0 END ) AS fabric_qty,
				sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_qty,
				sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_qty
				FROM order_wise_pro_details a,inv_transaction b
				WHERE a.trans_id = b.id 
				and b.status_active=1 and a.entry_form in(18,15) and a.quantity!=0 and  b.is_deleted=0  group by a.po_breakdown_id");
			$fabric_data_arr=array();
			foreach($sql_fabric_qty as $inf)
			{
				$fabric_data_arr[$inf[csf("po_breakdown_id")]]["fab_iss"]=$inf[csf("fabric_qty")]+$inf[csf("trans_in_qty")]-$inf[csf("trans_out_qty")];
			}
			unset($sql_fabric_qty);	
			?>
			<div style="width:4270px; max-height:425px; overflow-y:scroll"   id="scroll_body" align="left">
                <table class="rpt_table" width="4250" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
                <?
                if($db_type==0)
                {
                	$sql=sql_select("select b.company_name,a.id as po_id,a.po_number,b.job_no_prefix_num,b.buyer_name,b.style_ref_no,a.po_quantity,a.plan_cut as plan_cut,year(b.insert_date) as year,pub_shipment_date as ship_date,a.shiping_status as status,b.set_break_down from  wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no $company_name $buyer_name $job_cond_id $style_cond $txt_style $order_cond $ship_status_cond $year_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 order by b.buyer_name,b.job_no_prefix_num");

					//echo "select b.company_name,a.id as po_id,a.po_number,b.job_no_prefix_num,b.buyer_name,b.style_ref_no,a.po_quantity,a.plan_cut as plan_cut,year(b.insert_date) as year,pub_shipment_date as ship_date,a.shiping_status as status,b.set_break_down from  wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no $company_name $buyer_name $job_cond_id $style_cond $order_cond $ship_status_cond $year_cond $txt_style and b.status_active=1 and b.is_deleted=0 and a.status_active=1 order by b.buyer_name,b.job_no_prefix_num";
                }
                else if($db_type==2)
                {
                	$sql=sql_select("select b.company_name,a.id as po_id,a.po_number,b.job_no_prefix_num,b.buyer_name,b.style_ref_no,a.po_quantity,a.plan_cut as plan_cut,extract(year from b.insert_date) as year,pub_shipment_date as ship_date,a.shiping_status as status,b.set_break_down from  wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no $company_name $buyer_name $job_cond_id $style_cond $txt_style $order_cond $ship_status_cond $year_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 order by b.buyer_name,b.job_no_prefix_num");
					//echo "select b.company_name,a.id as po_id,a.po_number,b.job_no_prefix_num,b.buyer_name,b.style_ref_no,a.po_quantity,a.plan_cut as plan_cut,extract(year from b.insert_date) as year,pub_shipment_date as ship_date,a.shiping_status as status,b.set_break_down from  wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no $company_name $buyer_name $job_cond_id $style_cond $txt_style $order_cond $ship_status_cond $year_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 order by b.buyer_name,b.job_no_prefix_num";
						
                }
				//echo $sql;die();
				
                $grand_fabric_iss=$grand_embl_rec=$grand_embl_rev_total=$grand_plan_cut=$job_total=$exfactory_total=0;
                $cut_today=$embl_issue_today=$embl_rcv_today=$sewing_in_today=$sew_out_today=$iron_today=$finish_today=$reject_today=$exfactory_qty=0;
                $fabric_iss=$cut_total=$embl_issue_total=$embl_rcv_total=$sewing_in_total=$iron_total=$sew_out_total=$finish_total=$reject_total=0;
                $grand_cut=$grand_cut_total=$grand_embl_issue=$grand_embl_iss_total=$grand_sew_in=$grand_sew_in_total=$grand_sew_out=$grand_sew_out_total=0;
                $grand_iron=$grand_iron_total=$grand_finish=$grand_finish_total=$grand_reject=$grand_reject_total=$grand_exfactory=$grand_exfa_total=0; 
                $print_issue_today=$pring_rcv_today=$wash_issue_today=$wash_rcv_today=$sp_issue_today=$sp_rcv_today=0;
                $print_issue_total=$print_rcv_total=$wash_issue_total=$wash_rcv_total=$sp_issue_total=$sp_rcv_total=0;
                $grand_print_issue=$grand_print_rcv=$$grand_print_issue_tot=$grand_print_rcv_tot=0;
                $tot_rows=count($sql);	
                $i=1;
                foreach($sql as $row)	
                {
					$sew_out_total=0;
					$line_id_all=$sew_line_arr[$row[csf('po_id')]]['line'];
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
					$setArr = explode("__",$row[csf("set_break_down")] );
					$countArr = count($setArr); 
					$gmt_item="";
					for($j=0;$j<$countArr;$j++)
					{
						$setItemArr = explode("_",$setArr[$j]);
						if($gmt_item!="")  $gmt_item.="<br/>".$garments_item[$setItemArr[0]];
						else $gmt_item=$garments_item[$setItemArr[0]];
					}
					$cut_today=$production_data_arr[$row[csf('po_id')]]['cutting_qnty'];
					$print_issue_today=$production_data_arr[$row[csf('po_id')]]['printing_qnty'];
					$pring_rcv_today=$production_data_arr[$row[csf('po_id')]]['printreceived_qnty'];
					$embl_issue_today=$production_data_arr[$row[csf('po_id')]]['embl_qnty'];
					$embl_rcv_today=$production_data_arr[$row[csf('po_id')]]['emblreceive_qnty'];
					$wash_issue_today=$production_data_arr[$row[csf('po_id')]]['wash_qnty'];
					$wash_rcv_today=$production_data_arr[$row[csf('po_id')]]['washreceive_qnty'];
					$sp_issue_today=$production_data_arr[$row[csf('po_id')]]['sp_qnty'];
					$sp_rcv_today=$production_data_arr[$row[csf('po_id')]]['spre_qnty'];
					$print_issue_total=$production_data_arr[$row[csf('po_id')]]['printing_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['printing_qnty'];
					$print_rcv_total=$production_data_arr[$row[csf('po_id')]]['printreceived_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['printreceived_qnty'];
                
					$embl_issue_total=$production_data_arr[$row[csf('po_id')]]['embl_qnty']+$production_data_arr[$row[csf('po_id')]]['embl_qnty_pre'];
					$embl_rcv_total=$production_data_arr[$row[csf('po_id')]]['emblreceive_qnty']+$production_data_arr[$row[csf('po_id')]]['emblreceived_qnty_pre'];
					$wash_issue_total=$production_data_arr[$row[csf('po_id')]]['wash_qnty']+$production_data_arr[$row[csf('po_id')]]['wash_qnty_pre'];
					$wash_rcv_total=$production_data_arr[$row[csf('po_id')]]['washreceive_qnty']+$production_data_arr[$row[csf('po_id')]]['wash_received_qnty_pre'];
					$sp_issue_total=$production_data_arr[$row[csf('po_id')]]['sp_qnty']+$production_data_arr[$row[csf('po_id')]]['sp_qnty_pre'];
					$sp_rcv_total=$production_data_arr[$row[csf('po_id')]]['spre_qnty']+$production_data_arr[$row[csf('po_id')]]['spreceived_qnty_pre'];
					$sewing_in_today=$production_data_arr[$row[csf('po_id')]]['sewingin_qnty'];
					$sew_out_today=$production_data_arr[$row[csf('po_id')]]['sewing_out_qnty'];
					$iron_today=$production_data_arr[$row[csf('po_id')]]['iron_qnty'];
					$finish_today=$production_data_arr[$row[csf('po_id')]]['finish_qnty'];
					$reject_today=$production_data_arr[$row[csf('po_id')]]['reject_today'];
					$exfactory_qty=$exfactory_data_arr[$row[csf('po_id')]]['ex_qnty'];
					$fabric_iss=$fabric_data_arr[$row[csf('po_id')]]['fab_iss'];
					$cut_total=$production_data_arr[$row[csf('po_id')]]['cutting_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['cutting_qnty'];
					$sewing_in_total=$production_data_arr[$row[csf('po_id')]]['sewingin_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['sewingin_qnty'];
					$iron_total=$production_data_arr[$row[csf('po_id')]]['iron_pre']+$production_data_arr[$row[csf('po_id')]]['iron_qnty'];
					$sew_out_total+=$production_data_arr[$row[csf('po_id')]]['sewing_out_pre']+$production_data_arr[$row[csf('po_id')]]['sewing_out_qnty'];
					$finish_total=$production_data_arr[$row[csf('po_id')]]['finish_pre']+$production_data_arr[$row[csf('po_id')]]['finish_qnty'];
					$reject_total=$production_data_arr[$row[csf('po_id')]]['reject_today']+$production_data_arr[$row[csf('po_id')]]['reject_pre'];
					$exfactory_total=$exfactory_data_arr[$row[csf('po_id')]]['ex_qnty']+$exfactory_data_arr[$row[csf('po_id')]]['exfac_pre'];
                
					if($fabric_iss!=0 || ($cut_today!=0 || $embl_issue_today!=0 || $embl_rcv_today!=0 || $sewing_in_today!=0  || $sew_out_today!=0  || $iron_today!=0 || $exfactory_qty!=0  || $finish_today!=0))
					{		
						$grand_cut+=$cut_today;
						$grand_print_issue+=$print_issue_today;
						$grand_print_rcv=$pring_rcv_today;
						$grand_print_issue_tot+=$print_issue_total;
						$grand_print_rcv_tot=$print_rcv_total;
						$grand_wash_issue+=$wash_issue_today;
						$grand_wash_rcv=$wash_rcv_today;
						$grand_wash_issue_tot+=$wash_issue_total;
						$grand_wash_rcv_tot=$wash_rcv_total;
						$grand_sp_issue+=$sp_issue_today;
						$grand_sp_rcv=$sp_rcv_today;
						$grand_sp_issue_tot+=$sp_issue_total;
						$grand_sp_rcv_tot=$sp_rcv_total;
						$grand_cut_total+=$cut_total;
						$grand_embl_issue+=$embl_issue_today;
						$grand_embl_iss_total+=$embl_issue_total;
						$grand_embl_rec+=$embl_rcv_today;
						$grand_embl_rev_total+=$embl_rcv_total;
						$grand_sew_in+=$sewing_in_today;
						$grand_sew_in_total+=$sewing_in_total;
						$grand_sew_out+=$sew_out_today;
						$grand_sew_out_total+=$sew_out_total;
						$grand_iron+=$iron_today;
						$grand_iron_total+=$iron_total;
						$grand_finish+=$finish_today;
						$grand_finish_total+=$finish_total;
						$grand_reject+=$reject_today;
						$grand_reject_total+=$reject_total;
						$grand_exfactory+=$exfactory_qty;
						$grand_exfa_total+=$exfactory_total;
						$grand_fabric_iss+=$fabric_iss;
						$grand_plan_cut+=$row[csf('plan_cut')];
						$job_total+=$row[csf('po_quantity')];
						$txt_date=str_replace("'","",$txt_date_from);
						$diff=datediff("d",$txt_date,$row[csf('ship_date')]);
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                            <td width="40"><? echo $i;?></td>
                            <td width="100" align="center"><p>test<? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></p></td>
                            <td width="150" align="center"><p><? echo $row[csf('style_ref_no')];?>&nbsp;</p></td>
                            <td width="60" align="center"><?  echo $row[csf('job_no_prefix_num')];?></td>
                            <td width="50" align="center"><?  echo $row[csf('year')];?></td>
                            <td width="140" align="center"><p><?  echo $row[csf('po_number')];?></p></td>
                            <td width="100" align="center"><p> <?  echo $gmt_item; ?></p></td>
                            <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'order_quantity',1050,250)"><?  echo number_format($row[csf('po_quantity')]);?></a></td>
                            <td width="98" align="center"><? echo change_date_format($row[csf('ship_date')]);?></td>
                            <td width="100" align="center"><p><? echo $shipment_status[$row[csf('status')]]; ?></p></td>
                            <td width="70" align="right"><?  echo number_format($fabric_iss); ?></td>
                            <td width="100" align="right"><? // echo $fabric_iss; ?></td>
                            <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'plan_cut_quantity',800,250)"><?  echo $row[csf('plan_cut')]; ?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'today_order_cutting',850,250)"><?   echo number_format($cut_today,0); ?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'today_order_cutting',850,250)"><?   echo number_format($cut_total,0);?></a></td>
                            <td width="70" align="right"><?   echo number_format(($row[csf('plan_cut')]-$cut_total),0); ?></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_issue_order',850,250,1)"><?   echo number_format($print_issue_today,0);?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_issue_order',850,250,1)"><?   echo number_format($print_issue_total,0);; ?></a></td>
                            <td width="70" align="right"><?   echo number_format(($cut_total-$print_issue_total),0); ?></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_receive_order',850,250,1)"><?   echo number_format($pring_rcv_today,0); ?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_receive_order',850,250,1)"><?   echo number_format($print_rcv_total,0); ?></a></td>
                            <td width="70" align="right"><?   echo number_format(($print_issue_total-$print_rcv_total),0); ?></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_issue_order',850,250,2)"><?   echo number_format($embl_issue_today,0);?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_issue_order',850,250,2)"><?   echo number_format($embl_issue_total,0);; ?></a></td>
                            <td width="70" align="right"><?   echo number_format(($cut_total-$embl_issue_total),0); ?></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_receive_order',850,250,2)"><?   echo number_format($embl_rcv_today,0); ?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_receive_order',850,250,2)"><?   echo number_format($embl_rcv_total,0); ?></a></td>
                            <td width="70" align="right"><?   echo number_format(($embl_issue_total-$embl_rcv_total),0); ?></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_issue_order',850,250,4)"><?   echo number_format($sp_issue_today,0);?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_issue_order',850,250,4)"><?   echo number_format($sp_issue_total,0);; ?></a></td>
                            <td width="70" align="right"><?   echo number_format(($cut_total-$sp_issue_total),0); ?></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_receive_order',850,250,4)"><?   echo number_format($sp_rcv_today,0); ?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_receive_order',850,250,4)"><?   echo number_format($sp_rcv_total,0); ?></a></td>
                            <td width="70" align="right"><?   echo number_format(($sp_issue_total-$sp_rcv_total),0); ?></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'sewing_input_order',850,250)"><?   echo number_format($sewing_in_today,0); ?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'sewing_input_order',850,250)"><?   echo number_format($sewing_in_total,0); ?></a></td>
                            <td width="70" align="right"><?   echo number_format(($cut_total-$sewing_in_total),0); ?></td>
                            <td width="100" align="center"><p><?   echo  $line_name; ?></p></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'sewing_output_order',850,250)"><?   echo number_format($sew_out_today,0); ?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'sewing_output_order',850,250)"><?   echo number_format($sew_out_total,0); ?></a></td>
                            <td width="70" align="right"><?   echo number_format(($sewing_in_total-$sew_out_total),0); ?></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_issue_order',850,250,3)"><?   echo number_format($wash_issue_today,0); ?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_issue_order',850,250,3)"><?   echo number_format($wash_issue_total,0); ?></a></td>
                            <td width="70" align="right"><?   echo number_format(($sew_out_total-$wash_issue_total),0); ?></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_receive_order',850,250,3)"><?   echo number_format($wash_rcv_today,0); ?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_receive_order',850,250,3)"><?   echo number_format($wash_rcv_total,0); ?></a></td>
                            <td width="70" align="right"><?   echo number_format(($wash_issue_total-$wash_rcv_total),0); ?></td>
                            
                            
                            <td width="70" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'iron__entry_order',850,300)"><?   echo number_format($iron_today,0);?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'iron__entry_order',850,300)"><?   echo number_format($iron_total,0); ?></a></td>
                            <td width="70" align="right"><?   echo number_format(($sew_out_total-$iron_total),0); ?></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'finish_qty_order',850,300)"><?   echo number_format($finish_today,0); ?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'finish_qty_order',850,300)"><?   echo number_format($finish_total,2); ?></a></td>
                            <td width="70" align="right"><?   echo number_format(($iron_total-$finish_total),0); ?></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'exfactrory__entry_order',850,250)"><?   echo number_format($exfactory_qty,0); ?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'exfactrory__entry_order',850,250)"><?   echo number_format(($exfactory_total),0); ?></a></td>
                            <td  align="right" width="70" ><?  echo number_format(($finish_total-$exfactory_total),0);   ?></td>
						</tr>    
						<?
						$i++;
					}
                }
                ?>
            </table>     
            <table class="rpt_table" width="4250" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tfoot>
                    <tr>
                        <th width="40"><? // echo $i;?></th>
                        <th width="100"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></th>
                        <th width="150"><strong>Grand Total:</strong></td>
                        <th width="60"></td>
                        <th width="50"><? //echo $pro_date_sql_row[csf("po_number")];?></th>
                        <th width="140"><? //echo $pro_date_sql_row[csf("po_number")];?></th>
                        <th width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></th>
                        <th width="100" align="right" id="value_job_total"><?  echo number_format($job_total,0); ?></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="70" id="value_fabric_issue"><? echo number_format($grand_fabric_iss,0); ?></th>
                        <th width="100"></th>
                        <th width="100" align="right" id="value_plan_cut"><? echo number_format($grand_plan_cut,0); ?></th>
                        <th width="70" align="right" id="value_cut_today"><?  echo number_format($grand_cut,0); ?></th>
                        <th width="70" align="right" id="value_cut_total"><?  echo number_format($grand_cut_total,0); ?></th>
                        <th width="70" align="right" id="value_cut_bal"><?  echo number_format(($job_total-$grand_cut_total),0); ?></th>
                        <th width="70" align="right" id="value_print_iss"><?  echo number_format($grand_print_issue,0); ?></th>
                        <th width="70" align="right" id="value_print_iss_total"><?  echo number_format($grand_print_issue_tot,0);?></th>
                        <th width="70" align="right" id="value_print_iss_bal"><?  echo number_format(($grand_cut_total-$grand_print_issue_tot),0); ?></th>
                        <th width="70" align="right" id="value_print_rec"><?  echo number_format($grand_print_rcv,0); ?></th>
                        <th width="70" align="right" id="value_print_rec_total"><?  echo number_format($grand_print_rcv_tot,0); ?></th>
                        <th width="70" align="right" id="value_print_rec_bal"><?  echo number_format(($grand_print_issue_tot-$grand_print_rcv_tot),0); ?></th>
                        <th width="70" align="right" id="value_embl_iss"><?  echo number_format($grand_embl_issue,0); ?></th>
                        <th width="70" align="right" id="value_embl_iss_total"><?  echo number_format($grand_embl_iss_total,0);?></th>
                        <th width="70" align="right" id="value_embl_iss_bal"><?  echo number_format(($grand_cut_total-$grand_embl_iss_total),0); ?></th>
                        <th width="70" align="right" id="value_embl_rec"><?  echo number_format($grand_embl_rec,0); ?></th>
                        <th width="70" align="right" id="value_embl_rec_total"><?  echo number_format($grand_embl_rev_total,0); ?></th>
                        <th width="70" align="right" id="value_embl_rec_bal"><?  echo number_format(($grand_embl_iss_total-$grand_embl_rev_total),0); ?></th>
                        <th width="70" align="right" id="value_sp_iss"><?  echo number_format($grand_sp_issue,0); ?></th>
                        <th width="70" align="right" id="value_sp_iss_total"><?  echo number_format($grand_sp_issue_tot,0);?></th>
                        <th width="70" align="right" id="value_sp_iss_bal"><?  echo number_format(($grand_cut_total-$grand_sp_issue_tot),0); ?></th>
                        <th width="70" align="right" id="value_sp_rec"><?  echo number_format($grand_sp_rcv,0); ?></th>
                        <th width="70" align="right" id="value_sp_rec_total"><?  echo number_format($grand_sp_rcv_tot,0); ?></th>
                        <th width="70" align="right" id="value_sp_rec_bal"><?  echo number_format(($grand_sp_issue_tot-$grand_sp_rcv_tot),0); ?></th>
                        <th width="70" align="right" id="value_sew_in"><?  echo number_format($grand_sew_in,0); ?></th>
                        <th width="70" align="right" id="value_sew_in_to"><?  echo number_format($grand_sew_in_total,0); ?></th>
                        <th width="70" align="right" id="value_sew_in_bal"><?  echo number_format(($grand_cut_total-$grand_sew_in_total),0); ?></th>
                        <th width="100" align="right" id=""><?  //echo number_format(($grand_cut_total-$grand_sew_in_total),0); ?></th>
                        <th width="70" align="right" id="value_sew_out"><?  echo number_format($grand_sew_out,0); ?></th>
                        <th width="70" align="right" id="value_sew_out_total"><?  echo number_format($grand_sew_out_total,0); ?></th>
                        <th width="70" align="right" id="value_sew_out_bal"><?  echo number_format(($grand_sew_in_total-$grand_sew_out_total),0); ?></th>
                        <th width="70" align="right" id="value_wash_in"><?  echo number_format($grand_wash_issue,0); ?></th>
                        <th width="70" align="right" id="value_wash_in_to"><?  echo number_format($grand_wash_issue_tot,0); ?></th>
                        <th width="70" align="right" id="value_wash_in_bal"><?  echo number_format(($grand_sew_out-$grand_wash_issue_tot),0); ?></th>
                        <th width="70" align="right" id="value_wash_out"><?  echo number_format($grand_wash_rcv,0); ?></th>
                        <th width="70" align="right" id="value_wash_out_total"><?  echo number_format($grand_wash_rcv_tot,0); ?></th>
                        <th width="70" align="right" id="value_wash_out_bal"><?  echo number_format(($grand_wash_issue_tot-$grand_wash_rcv_tot),0); ?></th>
                        <th width="70" align="right" id="value_iron"><?  echo number_format($grand_iron,0); ?></th>
                        <th width="70" align="right" id="value_iron_to"><?  echo number_format($grand_iron_total,0); ?></th>
                        <th width="70" align="right" id="value_iron_bal"><?  echo number_format($grand_sew_out-$grand_iron_total,0); ?></th>
                        <th width="70" align="right" id="value_finish"><?  echo number_format($grand_finish,0); ?></th>
                        <th width="70" align="right" id="value_finish_to"><?  echo number_format($grand_finish_total,0); ?></th>
                        <th width="70" align="right" id="value_finish_bal"><?  echo number_format(($grand_iron_total-$grand_finish_total),0);?></th>
                        <th width="70" align="right" id="value_exfactory"><?  echo number_format($grand_exfactory,0); ?></th>
                        <th width="70" align="right" id="value_exfactory_to"><?  echo number_format($grand_exfa_total,0); ?></th>
                        <th width="70" align="right" id="value_exfac_bal"><?  echo number_format(($grand_finish_total-$grand_exfa_total),0); ?></th>
                    </tr> 
                </tfoot>
            </table>        
            </div>
            </fieldset>
            <?	
        }
    }
	else if(str_replace("'","",$report_type)==6)
	{
		if(str_replace("'","",$cbo_search_by)==4)
		{
			//echo "kausar";
			$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_name and variable_list=23 and is_deleted=0 and status_active=1");
			?>
			<fieldset style="width:4200px;">
                <table width="1880"  cellspacing="0"   >
                    <tr class="form_caption" style="border:none;">
                        <td colspan="29" align="center" style="border:none;font-size:14px; font-weight:bold" >
                        Daily Order Wise Garments Production Status Report</td>
                    </tr>
                    <tr style="border:none;">
                        <td colspan="29" align="center" style="border:none; font-size:16px; font-weight:bold">
                        Company Name:<? 
							$com_arr=explode(",",str_replace("'","",$cbo_company_name));
							$comName="";
							foreach($com_arr as $comID)
							{
								$comName.=$company_library[$comID].',';
							}
							echo chop($comName,",");
							//echo $company_library[str_replace("'","",$cbo_company_name)]; 
						?>                                
                        </td>
                    </tr>
                    <tr style="border:none;">
                        <td colspan="29" align="center" style="border:none;font-size:12px; font-weight:bold">
                        <? echo "Date: ". str_replace("'","",$txt_date_from) ;?>
                        </td>
                    </tr>
                </table>
				<br />	
                <table class="rpt_table" width="4460" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr >
                            <th width="40"  rowspan="2">SL</th>
                            <th width="100" rowspan="2">Buyer</th>
                            <th width="150" rowspan="2">Style</th>
                            <th width="60"  rowspan="2">Job No</th>
                            <th width="50"  rowspan="2">Year</th>
                            <th width="140" rowspan="2">Order No</th>
                            <th width="100" rowspan="2">Gmt. Item</th>
                            <th width="100" rowspan="2">Order Qty.</th>
                            <th width="100" rowspan="2">Ship Date</th>
                            <th width="100" rowspan="2">Shiping Status</th>
                            <th width="70" rowspan="2">Fin. Fab. Issued</th>
                            <th width="100"  rowspan="2">Fin  /Pic</th>
                            <th width="100" rowspan="2">Possible Cut Qty.</th>
                            <th width="210" colspan="3">Cutting</th>
                            <th width="210" colspan="3">Print issue</th>
                            <th width="210" colspan="3">Print Receive</th>
                            <th width="210" colspan="3">Embro. Issue</th>
                            <th width="210" colspan="3">Embro. Rcv</th>
                            <th width="210" colspan="3">Special Works. Issue</th>
                            <th width="210" colspan="3">Special Works. Rcv</th>
                            <th width="210" colspan="3">Sewing Input	</th>
                            <th width="100" rowspan="2">Sewing Line</th>
                            <th width="210" colspan="3">Sewing Output</th>
                            <th width="210" colspan="3">Wash. Issue</th>
                            <th width="210" colspan="3">Wash. Rcv</th>
                            <th width="210" colspan="3">Finishing Input</th>
                            <th width="210" colspan="3">Poly</th>
                            <th width="210" colspan="3">Packing & Finishing	</th>
                            <th colspan="3">Ex- Factory</th>
                        </tr>
                        <tr>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
							<th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>

                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                            <th width="70" >Today</th>
                            <th width="70">Total </th>
                            <th width="70" >WIP /Bal.</th>
                        </tr>
                    </thead>
                </table>
			<?
			$sew_line_arr=array();
			if($db_type==0)
			{
				$sql_line=sql_select("select group_concat(distinct a.sewing_line) as line_id,a.po_break_down_id from pro_garments_production_mst a where  a.production_type='4'  group by a.po_break_down_id");
			}
			else if($db_type==2)
			{
				$sql_line=sql_select("select listagg(cast(a.sewing_line as varchar2(2000)),',') within group (order by a.sewing_line) as line_id,a.po_break_down_id from pro_garments_production_mst a where  a.production_type='4'  group by a.po_break_down_id");
			}
			foreach($sql_line as $row_sew)
			{
				$sew_line_arr[$row_sew[csf('po_break_down_id')]]['line']= implode(',',array_unique(explode(',',$row_sew[csf('line_id')])));
			}
			unset($sql_line);
			$production_data_arr=array();		
			$production_mst_sql= sql_select("SELECT po_break_down_id,
				sum(CASE WHEN production_type ='1' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty,
				sum(CASE WHEN production_type ='2' and embel_name=1  and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS printing_qnty,
				sum(CASE WHEN production_type ='3' and embel_name=1  and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS printreceived_qnty,
				sum(CASE WHEN production_type ='2' and embel_name=2  and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS embl_qnty,
				sum(CASE WHEN production_type ='3' and embel_name=2  and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS emblreceive_qnty,
				sum(CASE WHEN production_type ='2'  and embel_name=3  and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS wash_qnty,
				sum(CASE WHEN production_type ='3' and embel_name=3  and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS washreceive_qnty,
				sum(CASE WHEN production_type ='2' and embel_name=4  and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sp_qnty,
				sum(CASE WHEN production_type ='3' and embel_name=4  and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS spre_qnty,
				sum(CASE WHEN production_type ='2' and embel_name=1  and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS printing_qnty_pre,
				sum(CASE WHEN production_type ='3' and embel_name=1  and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS printreceived_qnty_pre,
				sum(CASE WHEN production_type ='2' and embel_name=2  and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS embl_qnty_pre,
				sum(CASE WHEN production_type ='3' and embel_name=2  and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS emblreceived_qnty_pre,
				sum(CASE WHEN production_type ='2' and embel_name=3  and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS wash_qnty_pre,
				sum(CASE WHEN production_type ='3' and embel_name=3  and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS wash_received_qnty_pre,
				sum(CASE WHEN production_type ='2' and embel_name=4  and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sp_qnty_pre,
				sum(CASE WHEN production_type ='3' and embel_name=4  and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS spreceived_qnty_pre,				
				sum(CASE WHEN production_type ='4' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewingin_qnty,
				sum(CASE WHEN production_type ='5' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewing_out_qnty,
				sum(CASE WHEN production_type ='80' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS finsihing_input_qnty_woven,
				sum(CASE WHEN production_type ='11' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS poly_qnty,
				sum(CASE WHEN production_type ='8' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS finish_qnty,
				sum(CASE WHEN production_type ='5' and production_date=".$txt_date_from." THEN reject_qnty ELSE 0 END) AS reject_today,
				sum(CASE WHEN production_type ='1' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty_pre,
				sum(CASE WHEN production_type ='4' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewingin_qnty_pre,
				sum(CASE WHEN production_type ='5' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewing_out_pre,
				sum(CASE WHEN production_type ='80' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS finsihing_input_pre_woven,
				sum(CASE WHEN production_type ='11' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS poly_pre,
				sum(CASE WHEN production_type ='8' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS finish_pre,
				sum(CASE WHEN production_type ='5' and production_date<".$txt_date_from." THEN reject_qnty ELSE 0 END) AS reject_pre
				from 
				pro_garments_production_mst 
				where  
				is_deleted=0 and status_active=1 
				group by po_break_down_id "); //reject_qnty
				
			
			foreach($production_mst_sql as $val)
			{
				$production_data_arr[$val[csf('po_break_down_id')]]['cutting_qnty']=$val[csf('cutting_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printing_qnty']=$val[csf('printing_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printreceived_qnty']=$val[csf('printreceived_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['embl_qnty']=$val[csf('embl_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['emblreceive_qnty']=$val[csf('emblreceive_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['wash_qnty']=$val[csf('wash_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['washreceive_qnty']=$val[csf('washreceive_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sp_qnty']=$val[csf('sp_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['spre_qnty']=$val[csf('spre_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printing_qnty_pre']=$val[csf('printing_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printreceived_qnty_pre']=$val[csf('printreceived_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['embl_qnty_pre']=$val[csf('embl_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['emblreceived_qnty_pre']=$val[csf('emblreceived_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['wash_qnty_pre']=$val[csf('wash_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['wash_received_qnty_pre']=$val[csf('wash_received_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sp_qnty_pre']=$val[csf('sp_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['spreceived_qnty_pre']=$val[csf('spreceived_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewingin_qnty']=$val[csf('sewingin_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewing_out_qnty']=$val[csf('sewing_out_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['finsihing_input_qnty_woven']+=$val[csf('finsihing_input_qnty_woven')];
				$production_data_arr[$val[csf('po_break_down_id')]]['poly_qnty']=$val[csf('poly_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['finish_qnty']=$val[csf('finish_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['reject_today']=$val[csf('reject_today')];
				$production_data_arr[$val[csf('po_break_down_id')]]['cutting_qnty_pre']=$val[csf('cutting_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewingin_qnty_pre']=$val[csf('sewingin_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewing_out_pre']=$val[csf('sewing_out_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['finishing_input_pre_woven']=$val[csf('finsihing_input_pre_woven')];
				$production_data_arr[$val[csf('po_break_down_id')]]['poly_pre']=$val[csf('poly_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['finish_pre']=$val[csf('finish_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['reject_pre']=$val[csf('reject_pre')];	
			}
			unset($production_mst_sql);
			//print_r($production_data_arr);
			$exfactory_sql=sql_select("SELECT po_break_down_id,
				sum(CASE WHEN  ex_factory_date=".$txt_date_from." AND entry_form!=85  THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN  ex_factory_date=".$txt_date_from." AND entry_form=85  THEN ex_factory_qnty ELSE 0 END) AS exfac_qty,
				sum(CASE WHEN  ex_factory_date<".$txt_date_from." AND entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN  ex_factory_date<".$txt_date_from." AND entry_form=85 THEN ex_factory_qnty ELSE 0 END) AS exfac_pre
				from 
				pro_ex_factory_mst 
				where  
				is_deleted=0 and status_active=1
				group by po_break_down_id ");
			$exfactory_data_arr=array();
			foreach($exfactory_sql as $value)
			{
				$exfactory_data_arr[$value[csf('po_break_down_id')]]['ex_qnty']=$value[csf('exfac_qty')];
				$exfactory_data_arr[$value[csf('po_break_down_id')]]['exfac_pre']=$value[csf('exfac_pre')];
			}
			unset($exfactory_sql);
			$sql_fabric_qty=sql_select( "SELECT a.po_breakdown_id,
				sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =2  AND a.entry_form =18  THEN a.quantity ELSE 0 END ) AS fabric_qty,
				sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_qty,
				sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_qty
				FROM order_wise_pro_details a,inv_transaction b
				WHERE a.trans_id = b.id 
				and b.status_active=1 and a.entry_form in(18,15) and a.quantity!=0 and  b.is_deleted=0  group by a.po_breakdown_id");
			$fabric_data_arr=array();
			foreach($sql_fabric_qty as $inf)
			{
				$fabric_data_arr[$inf[csf("po_breakdown_id")]]["fab_iss"]=$inf[csf("fabric_qty")]+$inf[csf("trans_in_qty")]-$inf[csf("trans_out_qty")];
			}
			unset($sql_fabric_qty);	
			?>
			<div style="width:4480px; max-height:425px; overflow-y:scroll"   id="scroll_body" align="left">
                <table class="rpt_table" width="4460" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
                <?
                if($db_type==0)
                {
                	$sql=sql_select("select b.company_name,a.id as po_id,a.po_number,b.job_no_prefix_num,b.buyer_name,b.style_ref_no,a.po_quantity,a.plan_cut as plan_cut,year(b.insert_date) as year,pub_shipment_date as ship_date,a.shiping_status as status,b.set_break_down from  wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no $company_name $buyer_name $job_cond_id $style_cond $txt_style $order_cond $ship_status_cond $year_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 order by b.buyer_name,b.job_no_prefix_num");

					//echo "select b.company_name,a.id as po_id,a.po_number,b.job_no_prefix_num,b.buyer_name,b.style_ref_no,a.po_quantity,a.plan_cut as plan_cut,year(b.insert_date) as year,pub_shipment_date as ship_date,a.shiping_status as status,b.set_break_down from  wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no $company_name $buyer_name $job_cond_id $style_cond $order_cond $ship_status_cond $year_cond $txt_style and b.status_active=1 and b.is_deleted=0 and a.status_active=1 order by b.buyer_name,b.job_no_prefix_num";
                }
                else if($db_type==2)
                {
                	$sql=sql_select("select b.company_name,a.id as po_id,a.po_number,b.job_no_prefix_num,b.buyer_name,b.style_ref_no,a.po_quantity,a.plan_cut as plan_cut,extract(year from b.insert_date) as year,pub_shipment_date as ship_date,a.shiping_status as status,b.set_break_down from  wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no $company_name $buyer_name $job_cond_id $style_cond $txt_style $order_cond $ship_status_cond $year_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 order by b.buyer_name,b.job_no_prefix_num");
					//echo "select b.company_name,a.id as po_id,a.po_number,b.job_no_prefix_num,b.buyer_name,b.style_ref_no,a.po_quantity,a.plan_cut as plan_cut,extract(year from b.insert_date) as year,pub_shipment_date as ship_date,a.shiping_status as status,b.set_break_down from  wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no $company_name $buyer_name $job_cond_id $style_cond $txt_style $order_cond $ship_status_cond $year_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 order by b.buyer_name,b.job_no_prefix_num";
						
                }
				//echo $sql;die();
				
                $grand_fabric_iss=$grand_embl_rec=$grand_embl_rev_total=$grand_plan_cut=$job_total=$exfactory_total=0;
                $cut_today=$embl_issue_today=$embl_rcv_today=$sewing_in_today=$sew_out_today=$poly_today=$finish_today=$reject_today=$exfactory_qty=0;
                $fabric_iss=$cut_total=$embl_issue_total=$embl_rcv_total=$sewing_in_total=$poly_total=$sew_out_total=$finish_total=$reject_total=0;
                $grand_cut=$grand_cut_total=$grand_embl_issue=$grand_embl_iss_total=$grand_sew_in=$grand_sew_in_total=$grand_sew_out=$grand_sew_out_total=0;
                $grand_input_woven=$grand_input_total_woven=$grand_poly=$grand_poly_total=$grand_finish=$grand_finish_total=$grand_reject=$grand_reject_total=$grand_exfactory=$grand_exfa_total=0; 
                $print_issue_today=$pring_rcv_today=$wash_issue_today=$wash_rcv_today=$sp_issue_today=$sp_rcv_today=0;
                $print_issue_total=$print_rcv_total=$wash_issue_total=$wash_rcv_total=$sp_issue_total=$sp_rcv_total=0;
                $grand_print_issue=$grand_print_rcv=$$grand_print_issue_tot=$grand_print_rcv_tot=0;
                $tot_rows=count($sql);	
                $i=1;
                foreach($sql as $row)	
                {
					$sew_out_total=0;
					$line_id_all=$sew_line_arr[$row[csf('po_id')]]['line'];
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
					$setArr = explode("__",$row[csf("set_break_down")] );
					$countArr = count($setArr); 
					$gmt_item="";
					for($j=0;$j<$countArr;$j++)
					{
						$setItemArr = explode("_",$setArr[$j]);
						if($gmt_item!="")  $gmt_item.="<br/>".$garments_item[$setItemArr[0]];
						else $gmt_item=$garments_item[$setItemArr[0]];
					}
					$cut_today=$production_data_arr[$row[csf('po_id')]]['cutting_qnty'];
					$print_issue_today=$production_data_arr[$row[csf('po_id')]]['printing_qnty'];
					$pring_rcv_today=$production_data_arr[$row[csf('po_id')]]['printreceived_qnty'];
					$embl_issue_today=$production_data_arr[$row[csf('po_id')]]['embl_qnty'];
					$embl_rcv_today=$production_data_arr[$row[csf('po_id')]]['emblreceive_qnty'];
					$wash_issue_today=$production_data_arr[$row[csf('po_id')]]['wash_qnty'];
					$wash_rcv_today=$production_data_arr[$row[csf('po_id')]]['washreceive_qnty'];
					$sp_issue_today=$production_data_arr[$row[csf('po_id')]]['sp_qnty'];
					$sp_rcv_today=$production_data_arr[$row[csf('po_id')]]['spre_qnty'];
					$print_issue_total=$production_data_arr[$row[csf('po_id')]]['printing_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['printing_qnty'];
					$print_rcv_total=$production_data_arr[$row[csf('po_id')]]['printreceived_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['printreceived_qnty'];
                
					$embl_issue_total=$production_data_arr[$row[csf('po_id')]]['embl_qnty']+$production_data_arr[$row[csf('po_id')]]['embl_qnty_pre'];
					$embl_rcv_total=$production_data_arr[$row[csf('po_id')]]['emblreceive_qnty']+$production_data_arr[$row[csf('po_id')]]['emblreceived_qnty_pre'];
					$wash_issue_total=$production_data_arr[$row[csf('po_id')]]['wash_qnty']+$production_data_arr[$row[csf('po_id')]]['wash_qnty_pre'];
					$wash_rcv_total=$production_data_arr[$row[csf('po_id')]]['washreceive_qnty']+$production_data_arr[$row[csf('po_id')]]['wash_received_qnty_pre'];
					$sp_issue_total=$production_data_arr[$row[csf('po_id')]]['sp_qnty']+$production_data_arr[$row[csf('po_id')]]['sp_qnty_pre'];
					$sp_rcv_total=$production_data_arr[$row[csf('po_id')]]['spre_qnty']+$production_data_arr[$row[csf('po_id')]]['spreceived_qnty_pre'];
					$sewing_in_today=$production_data_arr[$row[csf('po_id')]]['sewingin_qnty'];
					$sew_out_today=$production_data_arr[$row[csf('po_id')]]['sewing_out_qnty'];
					$finish_input_today_woven=$production_data_arr[$row[csf('po_id')]]['finsihing_input_qnty_woven'];

					$poly_today=$production_data_arr[$row[csf('po_id')]]['poly_qnty'];
					$finish_today=$production_data_arr[$row[csf('po_id')]]['finish_qnty'];
					$reject_today=$production_data_arr[$row[csf('po_id')]]['reject_today'];
					$exfactory_qty=$exfactory_data_arr[$row[csf('po_id')]]['ex_qnty'];
					$fabric_iss=$fabric_data_arr[$row[csf('po_id')]]['fab_iss'];
					$cut_total=$production_data_arr[$row[csf('po_id')]]['cutting_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['cutting_qnty'];
					$sewing_in_total=$production_data_arr[$row[csf('po_id')]]['sewingin_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['sewingin_qnty'];
					$finish_input_total_woven=$production_data_arr[$row[csf('po_id')]]['finsihing_input_pre_woven']+$production_data_arr[$row[csf('po_id')]]['finsihing_input_qnty_woven'];
					$poly_total=$production_data_arr[$row[csf('po_id')]]['poly_pre']+$production_data_arr[$row[csf('po_id')]]['poly_qnty'];
					$sew_out_total+=$production_data_arr[$row[csf('po_id')]]['sewing_out_pre']+$production_data_arr[$row[csf('po_id')]]['sewing_out_qnty'];
					$finish_total=$production_data_arr[$row[csf('po_id')]]['finish_pre']+$production_data_arr[$row[csf('po_id')]]['finish_qnty'];
					$reject_total=$production_data_arr[$row[csf('po_id')]]['reject_today']+$production_data_arr[$row[csf('po_id')]]['reject_pre'];
					$exfactory_total=$exfactory_data_arr[$row[csf('po_id')]]['ex_qnty']+$exfactory_data_arr[$row[csf('po_id')]]['exfac_pre'];
                
					if($fabric_iss!=0 || ($cut_today!=0 || $embl_issue_today!=0 || $embl_rcv_today!=0 || $sewing_in_today!=0  || $sew_out_today!=0  || $poly_today!=0 || $exfactory_qty!=0  || $finish_today!=0))
					{		
						$grand_cut+=$cut_today;
						$grand_print_issue+=$print_issue_today;
						$grand_print_rcv=$pring_rcv_today;
						$grand_print_issue_tot+=$print_issue_total;
						$grand_print_rcv_tot=$print_rcv_total;
						$grand_wash_issue+=$wash_issue_today;
						$grand_wash_rcv=$wash_rcv_today;
						$grand_wash_issue_tot+=$wash_issue_total;
						$grand_wash_rcv_tot=$wash_rcv_total;
						$grand_sp_issue+=$sp_issue_today;
						$grand_sp_rcv=$sp_rcv_today;
						$grand_sp_issue_tot+=$sp_issue_total;
						$grand_sp_rcv_tot=$sp_rcv_total;
						$grand_cut_total+=$cut_total;
						$grand_embl_issue+=$embl_issue_today;
						$grand_embl_iss_total+=$embl_issue_total;
						$grand_embl_rec+=$embl_rcv_today;
						$grand_embl_rev_total+=$embl_rcv_total;
						$grand_sew_in+=$sewing_in_today;
						$grand_sew_in_total+=$sewing_in_total;
						$grand_sew_out+=$sew_out_today;
						$grand_sew_out_total+=$sew_out_total;
						$grand_finish_input_woven+=$finish_input_today_woven;
						$grand_finish_input_woven_total+=$finish_input_total_woven;
						$grand_poly+=$poly_today;
						$grand_poly_total+=$poly_total;
						$grand_finish+=$finish_today;
						$grand_finish_total+=$finish_total;
						$grand_reject+=$reject_today;
						$grand_reject_total+=$reject_total;
						$grand_exfactory+=$exfactory_qty;
						$grand_exfa_total+=$exfactory_total;
						$grand_fabric_iss+=$fabric_iss;
						$grand_plan_cut+=$row[csf('plan_cut')];
						$job_total+=$row[csf('po_quantity')];
						$txt_date=str_replace("'","",$txt_date_from);
						$diff=datediff("d",$txt_date,$row[csf('ship_date')]);
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                            <td width="40"><? echo $i;?></td>
                            <td width="100" align="center"><p>test<? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></p></td>
                            <td width="150" align="center"><p><? echo $row[csf('style_ref_no')];?>&nbsp;</p></td>
                            <td width="60" align="center"><?  echo $row[csf('job_no_prefix_num')];?></td>
                            <td width="50" align="center"><?  echo $row[csf('year')];?></td>
                            <td width="140" align="center"><p><?  echo $row[csf('po_number')];?></p></td>
                            <td width="100" align="center"><p> <?  echo $gmt_item; ?></p></td>
                            <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'order_quantity',1050,250)"><?  echo number_format($row[csf('po_quantity')]);?></a></td>
                            <td width="98" align="center"><? echo change_date_format($row[csf('ship_date')]);?></td>
                            <td width="100" align="center"><p><? echo $shipment_status[$row[csf('status')]]; ?></p></td>
                            <td width="70" align="right"><?  echo number_format($fabric_iss); ?></td>
                            <td width="100" align="right"><? // echo $fabric_iss; ?></td>
                            <td width="100" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'plan_cut_quantity',800,250)"><?  echo $row[csf('plan_cut')]; ?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'today_order_cutting',850,250)"><?   echo number_format($cut_today,0); ?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'today_order_cutting',850,250)"><?   echo number_format($cut_total,0);?></a></td>
                            <td width="70" align="right"><?   echo number_format(($row[csf('plan_cut')]-$cut_total),0); ?></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_issue_order',850,250,1)"><?   echo number_format($print_issue_today,0);?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_issue_order',850,250,1)"><?   echo number_format($print_issue_total,0);; ?></a></td>
                            <td width="70" align="right"><?   echo number_format(($cut_total-$print_issue_total),0); ?></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_receive_order',850,250,1)"><?   echo number_format($pring_rcv_today,0); ?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_receive_order',850,250,1)"><?   echo number_format($print_rcv_total,0); ?></a></td>
                            <td width="70" align="right"><?   echo number_format(($print_issue_total-$print_rcv_total),0); ?></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_issue_order',850,250,2)"><?   echo number_format($embl_issue_today,0);?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_issue_order',850,250,2)"><?   echo number_format($embl_issue_total,0);; ?></a></td>
                            <td width="70" align="right"><?   echo number_format(($cut_total-$embl_issue_total),0); ?></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_receive_order',850,250,2)"><?   echo number_format($embl_rcv_today,0); ?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_receive_order',850,250,2)"><?   echo number_format($embl_rcv_total,0); ?></a></td>
                            <td width="70" align="right"><?   echo number_format(($embl_issue_total-$embl_rcv_total),0); ?></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_issue_order',850,250,4)"><?   echo number_format($sp_issue_today,0);?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_issue_order',850,250,4)"><?   echo number_format($sp_issue_total,0);; ?></a></td>
                            <td width="70" align="right"><?   echo number_format(($cut_total-$sp_issue_total),0); ?></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_receive_order',850,250,4)"><?   echo number_format($sp_rcv_today,0); ?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_receive_order',850,250,4)"><?   echo number_format($sp_rcv_total,0); ?></a></td>
                            <td width="70" align="right"><?   echo number_format(($sp_issue_total-$sp_rcv_total),0); ?></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'sewing_input_order',850,250)"><?   echo number_format($sewing_in_today,0); ?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'sewing_input_order',850,250)"><?   echo number_format($sewing_in_total,0); ?></a></td>
                            <td width="70" align="right"><?   echo number_format(($cut_total-$sewing_in_total),0); ?></td>
                            <td width="100" align="center"><p><?   echo  $line_name; ?></p></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'sewing_output_order',850,250)"><?   echo number_format($sew_out_today,0); ?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'sewing_output_order',850,250)"><?   echo number_format($sew_out_total,0); ?></a></td>
                            <td width="70" align="right"><?   echo number_format(($sewing_in_total-$sew_out_total),0); ?></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_issue_order',850,250,3)"><?   echo number_format($wash_issue_today,0); ?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_issue_order',850,250,3)"><?   echo number_format($wash_issue_total,0); ?></a></td>
                            <td width="70" align="right"><?   echo number_format(($sew_out_total-$wash_issue_total),0); ?></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_receive_order',850,250,3)"><?   echo number_format($wash_rcv_today,0); ?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_embl('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_receive_order',850,250,3)"><?   echo number_format($wash_rcv_total,0); ?></a></td>
                            <td width="70" align="right"><?   echo number_format(($wash_issue_total-$wash_rcv_total),0); ?></td>
							<td width="70" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'finish_input_woven_order',850,300)"><?   echo number_format($finish_input_today_woven,0);?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'finish_input_woven_order',850,300)"><?   echo number_format($finish_input_total_woven,0); ?></a></td>
                            <td width="70" align="right"><?   echo number_format(($wash_rcv_total-$finish_input_total_woven),0); ?></td>
                            
                            
                            <td width="70" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'poly__entry_order',850,300)"><?   echo number_format($poly_today,0);?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'poly__entry_order',850,300)"><?   echo number_format($poly_total,0); ?></a></td>
                            <td width="70" align="right"><?   echo number_format(($finish_input_total_woven-$poly_total),0); ?></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'finish_qty_order',850,300)"><?   echo number_format($finish_today,0); ?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'finish_qty_order',850,300)"><?   echo number_format($finish_total,2); ?></a></td>
                            <td width="70" align="right"><?   echo number_format(($poly_total-$finish_total),0); ?></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'exfactrory__entry_order',850,250)"><?   echo number_format($exfactory_qty,0); ?></a></td>
                            <td width="70" align="right"><a href="##" onClick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'exfactrory__entry_order',850,250)"><?   echo number_format(($exfactory_total),0); ?></a></td>
                            <td  align="right" width="70" ><?  echo number_format(($finish_total-$exfactory_total),0);   ?></td>
						</tr>    
						<?
						$i++;
					}
                }
                ?>
            </table>     
            <table class="rpt_table" width="4460" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tfoot>
                    <tr>
                        <th width="40"><? // echo $i;?></th>
                        <th width="100"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></th>
                        <th width="150"><strong>Grand Total:</strong></td>
                        <th width="60"></td>
                        <th width="50"><? //echo $pro_date_sql_row[csf("po_number")];?></th>
                        <th width="140"><? //echo $pro_date_sql_row[csf("po_number")];?></th>
                        <th width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></th>
                        <th width="100" align="right" id="value_job_total"><?  echo number_format($job_total,0); ?></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="70" id="value_fabric_issue"><? echo number_format($grand_fabric_iss,0); ?></th>
                        <th width="100"></th>
                        <th width="100" align="right" id="value_plan_cut"><? echo number_format($grand_plan_cut,0); ?></th>
                        <th width="70" align="right" id="value_cut_today"><?  echo number_format($grand_cut,0); ?></th>
                        <th width="70" align="right" id="value_cut_total"><?  echo number_format($grand_cut_total,0); ?></th>
                        <th width="70" align="right" id="value_cut_bal"><?  echo number_format(($job_total-$grand_cut_total),0); ?></th>
                        <th width="70" align="right" id="value_print_iss"><?  echo number_format($grand_print_issue,0); ?></th>
                        <th width="70" align="right" id="value_print_iss_total"><?  echo number_format($grand_print_issue_tot,0);?></th>
                        <th width="70" align="right" id="value_print_iss_bal"><?  echo number_format(($grand_cut_total-$grand_print_issue_tot),0); ?></th>
                        <th width="70" align="right" id="value_print_rec"><?  echo number_format($grand_print_rcv,0); ?></th>
                        <th width="70" align="right" id="value_print_rec_total"><?  echo number_format($grand_print_rcv_tot,0); ?></th>
                        <th width="70" align="right" id="value_print_rec_bal"><?  echo number_format(($grand_print_issue_tot-$grand_print_rcv_tot),0); ?></th>
                        <th width="70" align="right" id="value_embl_iss"><?  echo number_format($grand_embl_issue,0); ?></th>
                        <th width="70" align="right" id="value_embl_iss_total"><?  echo number_format($grand_embl_iss_total,0);?></th>
                        <th width="70" align="right" id="value_embl_iss_bal"><?  echo number_format(($grand_cut_total-$grand_embl_iss_total),0); ?></th>
                        <th width="70" align="right" id="value_embl_rec"><?  echo number_format($grand_embl_rec,0); ?></th>
                        <th width="70" align="right" id="value_embl_rec_total"><?  echo number_format($grand_embl_rev_total,0); ?></th>
                        <th width="70" align="right" id="value_embl_rec_bal"><?  echo number_format(($grand_embl_iss_total-$grand_embl_rev_total),0); ?></th>
                        <th width="70" align="right" id="value_sp_iss"><?  echo number_format($grand_sp_issue,0); ?></th>
                        <th width="70" align="right" id="value_sp_iss_total"><?  echo number_format($grand_sp_issue_tot,0);?></th>
                        <th width="70" align="right" id="value_sp_iss_bal"><?  echo number_format(($grand_cut_total-$grand_sp_issue_tot),0); ?></th>
                        <th width="70" align="right" id="value_sp_rec"><?  echo number_format($grand_sp_rcv,0); ?></th>
                        <th width="70" align="right" id="value_sp_rec_total"><?  echo number_format($grand_sp_rcv_tot,0); ?></th>
                        <th width="70" align="right" id="value_sp_rec_bal"><?  echo number_format(($grand_sp_issue_tot-$grand_sp_rcv_tot),0); ?></th>
                        <th width="70" align="right" id="value_sew_in"><?  echo number_format($grand_sew_in,0); ?></th>
                        <th width="70" align="right" id="value_sew_in_to"><?  echo number_format($grand_sew_in_total,0); ?></th>
                        <th width="70" align="right" id="value_sew_in_bal"><?  echo number_format(($grand_cut_total-$grand_sew_in_total),0); ?></th>
                        <th width="100" align="right" id=""><?  //echo number_format(($grand_cut_total-$grand_sew_in_total),0); ?></th>
                        <th width="70" align="right" id="value_sew_out"><?  echo number_format($grand_sew_out,0); ?></th>
                        <th width="70" align="right" id="value_sew_out_total"><?  echo number_format($grand_sew_out_total,0); ?></th>
                        <th width="70" align="right" id="value_sew_out_bal"><?  echo number_format(($grand_sew_in_total-$grand_sew_out_total),0); ?></th>
                        <th width="70" align="right" id="value_wash_in"><?  echo number_format($grand_wash_issue,0); ?></th>
                        <th width="70" align="right" id="value_wash_in_to"><?  echo number_format($grand_wash_issue_tot,0); ?></th>
                        <th width="70" align="right" id="value_wash_in_bal"><?  echo number_format(($grand_sew_out-$grand_wash_issue_tot),0); ?></th>
                        <th width="70" align="right" id="value_wash_out"><?  echo number_format($grand_wash_rcv,0); ?></th>
                        <th width="70" align="right" id="value_wash_out_total"><?  echo number_format($grand_wash_rcv_tot,0); ?></th>
                        <th width="70" align="right" id="value_wash_out_bal"><?  echo number_format(($grand_wash_issue_tot-$grand_wash_rcv_tot),0); ?></th>
						<th width="70" align="right" id="value_woven"><?  echo number_format($grand_finish_input_woven,0); ?></th>
                        <th width="70" align="right" id="value_woven_to"><?  echo number_format($grand_finish_input_woven_total,0); ?></th>
                        <th width="70" align="right" id="value_woven_bal"><?  echo number_format($grand_wash_rcv_tot-$grand_finish_input_woven_total,0); ?></th>
                        <th width="70" align="right" id="value_poly"><?  echo number_format($grand_poly,0); ?></th>
                        <th width="70" align="right" id="value_poly_to"><?  echo number_format($grand_poly_total,0); ?></th>
                        <th width="70" align="right" id="value_poly_bal"><?  echo number_format($grand_finish_input_woven_total-$grand_poly_total,0); ?></th>
                        <th width="70" align="right" id="value_finish"><?  echo number_format($grand_finish,0); ?></th>
                        <th width="70" align="right" id="value_finish_to"><?  echo number_format($grand_finish_total,0); ?></th>
                        <th width="70" align="right" id="value_finish_bal"><?  echo number_format(($grand_poly_total-$grand_finish_total),0);?></th>
                        <th width="70" align="right" id="value_exfactory"><?  echo number_format($grand_exfactory,0); ?></th>
                        <th width="70" align="right" id="value_exfactory_to"><?  echo number_format($grand_exfa_total,0); ?></th>
                        <th width="70" align="right" id="value_exfac_bal"><?  echo number_format(($grand_finish_total-$grand_exfa_total),0); ?></th>
                    </tr> 
                </tfoot>
            </table>        
            </div>
            </fieldset>
            <?	
        }
    }
	else if(str_replace("'","",$report_type)==3)
	{
		if(str_replace("'","",$cbo_search_by)==1 || str_replace("'","",$cbo_search_by)==3 || str_replace("'","",$cbo_search_by)==4 || str_replace("'","",$cbo_search_by)==5)
		{
		
			if(str_replace("'","",$hidden_order_id)!=""){
				$order_cond="and a.id in(".str_replace("'","",$hidden_order_id).")";
			}
			else{$order_cond="";}
		
		
		
				//SQL...............................................................start;
				//Today Production.....................
                $sql="SELECT b.color_size_break_down_id,
					sum(CASE WHEN a.production_type ='1' and a.production_date=".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS cutting_qnty,
					sum(CASE WHEN a.production_type ='2' and a.production_date=".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS printing_qnty,
					sum(CASE WHEN a.production_type ='3' and a.production_date=".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS printreceived_qnty,
					sum(CASE WHEN a.production_type ='4' and a.production_date=".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS sewingin_qnty,
					sum(CASE WHEN a.production_type ='5' and a.production_date=".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS sewing_out_qnty,
					sum(CASE WHEN a.production_type ='7' and a.production_date=".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS iron_qnty,
					sum(CASE WHEN a.production_type ='5' and a.production_date=".$txt_date_from." THEN b.reject_qty ELSE 0 END) AS reject_today,
					sum(CASE WHEN a.production_type ='8' and a.production_date=".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS finish_qnty
					from 
					pro_garments_production_mst a,pro_garments_production_dtls b
					where
					a.id=b.mst_id and
					a.production_type=b.production_type and
					a.is_deleted=0 and 
					a.status_active=1 and a.production_date=".$txt_date_from." and
					b.color_size_break_down_id >0 and a.company_id=$cbo_company_name
					group by b.color_size_break_down_id";
				
				$color_size_break_down_id_arr=array(0);
				$production_data_arr=array();  		
                $today_production_sql= sql_select($sql);
                foreach($today_production_sql as $val)
                {
					$production_data_arr[$val[csf('color_size_break_down_id')]]['cutting_qnty']+=$val[csf('cutting_qnty')];
					$production_data_arr[$val[csf('color_size_break_down_id')]]['printing_qnty']+=$val[csf('printing_qnty')];
					$production_data_arr[$val[csf('color_size_break_down_id')]]['printreceived_qnty']+=$val[csf('printreceived_qnty')];
					$production_data_arr[$val[csf('color_size_break_down_id')]]['sewingin_qnty']+=$val[csf('sewingin_qnty')];
					$production_data_arr[$val[csf('color_size_break_down_id')]]['sewing_out_qnty']+=$val[csf('sewing_out_qnty')];
					$production_data_arr[$val[csf('color_size_break_down_id')]]['iron_qnty']+=$val[csf('iron_qnty')];
					$production_data_arr[$val[csf('color_size_break_down_id')]]['finish_qnty']+=$val[csf('finish_qnty')];
					$production_data_arr[$val[csf('color_size_break_down_id')]]['reject_today']+=$val[csf('reject_today')];
					$color_size_break_down_id_arr[$val[csf('color_size_break_down_id')]]=$val[csf('color_size_break_down_id')];
			   
			    }
				unset($today_production_sql);
				   // echo $sql;

		//Today exfactory qty------------------------------------------	
		
                $sql="SELECT max(a.ex_factory_date) as ex_factory_date,a.shiping_status,b.color_size_break_down_id,
					sum(CASE WHEN a.ex_factory_date=".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS ex_factory_qty
					
					from 
					pro_ex_factory_mst a,pro_ex_factory_dtls b
					where
					a.id=b.mst_id and
					a.is_deleted=0 and 
					b.color_size_break_down_id >0 and
					a.status_active=1 and a.ex_factory_date=".$txt_date_from." 
					group by a.shiping_status,b.color_size_break_down_id";
				
					 //echo $sql;
                $ex_factory_sql= sql_select($sql);
                foreach($ex_factory_sql as $val)
                {
					$production_data_arr[$val[csf('color_size_break_down_id')]]['ex_factory_qty']+=$val[csf('ex_factory_qty')];
					$production_data_arr[$val[csf('color_size_break_down_id')]]['ex_factory_date']=$val[csf('ex_factory_date')];
					$production_data_arr[$val[csf('color_size_break_down_id')]]['shiping_status']=$val[csf('shiping_status')];
					$color_size_break_down_id_arr[$val[csf('color_size_break_down_id')]]=$val[csf('color_size_break_down_id')];

				}
				$color_size_break_down_id_string=implode(',',$color_size_break_down_id_arr);
				unset($ex_factory_sql);





		//Get All Job from color breakdown id------------------------------------------------------------
			$sql="select c.job_no_mst from  wo_po_color_size_breakdown c where c.status_active=1  and c.id in($color_size_break_down_id_string)";
		$sql_result=sql_select($sql);
		foreach($sql_result as $row){
			$job_no_arr[$row[csf('job_no_mst')]]=$row[csf('job_no_mst')];
		}
		$job_no_arr_string=implode("','",$job_no_arr);
		unset($sql_result);
		unset($color_size_break_down_id_arr);
		//-----------------------------------------------------------------------



		//Order Data------------------------------------------------------------
		if($db_type==0)
		{
			$sql="select b.job_no,b.job_no_prefix_num,b.buyer_name,b.style_ref_no,sum(c.order_quantity) as job_qty_pcs ,group_concat(a.id) as po_id,c.color_number_id,group_concat(c.id) as color_size_table_id
				from  wo_po_break_down a,wo_po_details_master b,wo_po_color_size_breakdown c
				where a.id=c.po_break_down_id and a.job_no_mst=b.job_no  $company_name $buyer_name $job_cond_id $style_cond $order_cond $ship_status_cond $year_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1  and b.job_no in('$job_no_arr_string')  
				group by b.job_no,b.job_no_prefix_num, b.buyer_name, b.style_ref_no,c.color_number_id
				order by b.buyer_name,b.job_no_prefix_num";	
		}
		else if($db_type==2)
		{
			$sql="select b.job_no,b.job_no_prefix_num,b.buyer_name,b.style_ref_no,sum(c.order_quantity) as job_qty_pcs , listagg(a.id,',') within group (order by a.id) as po_id,c.color_number_id,listagg(c.id,',') within group (order by c.id) as color_size_table_id
				from wo_po_break_down a,wo_po_details_master b,wo_po_color_size_breakdown c
				where a.id=c.po_break_down_id and a.job_no_mst=b.job_no $company_name $buyer_name $job_cond_id $style_cond $order_cond $ship_status_cond $year_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and b.job_no in('$job_no_arr_string') 
				group by b.job_no,b.job_no_prefix_num, b.buyer_name, b.style_ref_no,c.color_number_id
				order by b.buyer_name, b.job_no_prefix_num";
					
		}
	
	      //echo $sql;
		$po_id_arr=array(0);
		$sql_result=sql_select($sql);
		
		foreach($sql_result as $row){
			$dataArr[$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('color_number_id')]]+=$row[csf('job_qty_pcs')];
			$style_arr[$row[csf('job_no')]]=$row[csf('style_ref_no')];
			$color_size_table_id_arr[$row[csf('job_no')]][$row[csf('color_number_id')]]=$row[csf('color_size_table_id')];
			$po_id_arr[$row[csf('job_no')]]=$row[csf('po_id')];
			$buyer_row_sapn_arr[$row[csf('buyer_name')]]+=1;
			$job_row_sapn_arr[$row[csf('buyer_name')]][$row[csf('job_no')]]+=1;
		}
		$po_break_down_id_string=implode(',',$po_id_arr);
		unset($sql_result);





		//Previous Production...............................
					$sql="SELECT b.color_size_break_down_id,
						
						sum(CASE WHEN a.production_type ='1' and a.production_date<".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS cutting_qnty_pre,
						sum(CASE WHEN a.production_type ='2' and a.production_date<".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS printing_qnty_pre,
						sum(CASE WHEN a.production_type ='3' and a.production_date<".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS printreceived_qnty_pre,
						sum(CASE WHEN a.production_type ='4' and a.production_date<".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS sewingin_qnty_pre,
						sum(CASE WHEN a.production_type ='5' and a.production_date<".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS sewing_out_pre,
						sum(CASE WHEN a.production_type ='7' and a.production_date<".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS iron_pre,
						sum(CASE WHEN a.production_type ='7' and a.production_date<".$txt_date_from." THEN a.re_production_qty ELSE 0 END) AS re_iron_pre,
						sum(CASE WHEN a.production_type ='8' and a.production_date<".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS finish_pre,					
						sum(CASE WHEN a.production_type ='5' and a.production_date<".$txt_date_from." THEN b.reject_qty ELSE 0 END) AS reject_pre
						
						
						from 
						pro_garments_production_mst a,pro_garments_production_dtls b
						where
						a.id=b.mst_id and
						a.production_type=b.production_type and
						a.is_deleted=0 and 
						a.status_active=1 and a.production_date<".$txt_date_from." and
						a.po_break_down_id in($po_break_down_id_string)
						group by b.color_size_break_down_id";
					
						//echo $sql;
		
					$pre_production_sql= sql_select($sql);
					foreach($pre_production_sql as $val)
					{
						$production_data_arr[$val[csf('color_size_break_down_id')]]['cutting_qnty_pre']=$val[csf('cutting_qnty_pre')];
						$production_data_arr[$val[csf('color_size_break_down_id')]]['printing_qnty_pre']=$val[csf('printing_qnty_pre')];
						$production_data_arr[$val[csf('color_size_break_down_id')]]['printreceived_qnty_pre']=$val[csf('printreceived_qnty_pre')];
						$production_data_arr[$val[csf('color_size_break_down_id')]]['sewingin_qnty_pre']=$val[csf('sewingin_qnty_pre')];
						$production_data_arr[$val[csf('color_size_break_down_id')]]['sewing_out_pre']=$val[csf('sewing_out_pre')];
						$production_data_arr[$val[csf('color_size_break_down_id')]]['iron_pre']=$val[csf('iron_pre')];
						$production_data_arr[$val[csf('color_size_break_down_id')]]['finish_pre']=$val[csf('finish_pre')];
						$production_data_arr[$val[csf('color_size_break_down_id')]]['reject_pre']=$val[csf('reject_pre')];
						$production_data_arr[$val[csf('color_size_break_down_id')]]['re_iron_pre']=$val[csf('re_iron_pre')];
				
					}
					unset($pre_production_sql); 

					$sql="SELECT max(a.ex_factory_date) as ex_factory_date,a.shiping_status,b.color_size_break_down_id,
						sum(CASE WHEN a.ex_factory_date<".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS ex_factory_qty_pre
						
						from 
						pro_ex_factory_mst a,pro_ex_factory_dtls b
						where
						a.id=b.mst_id and
						a.is_deleted=0 and 
						a.status_active=1 and a.ex_factory_date<".$txt_date_from." and
						a.po_break_down_id in($po_break_down_id_string)
						group by a.shiping_status,b.color_size_break_down_id";
					
						//echo $sql;
		
					$ex_factory_sql= sql_select($sql);
					foreach($ex_factory_sql as $val)
					{
						$production_data_arr[$val[csf('color_size_break_down_id')]]['ex_factory_qty_pre']+=$val[csf('ex_factory_qty_pre')];
					}
				unset($ex_factory_sql);
		
		
		
		
		
					//Re Production.....................
					
					$sql="SELECT a.po_break_down_id,
						sum(CASE WHEN a.production_type ='7' and a.production_date=".$txt_date_from." THEN a.production_quantity ELSE 0 END) AS iron_qnty,
						sum(CASE WHEN a.production_type ='7' and a.production_date<".$txt_date_from." THEN a.production_quantity ELSE 0 END) AS iron_qnty_pre,
						sum(CASE WHEN a.production_type ='7' and a.production_date=".$txt_date_from." THEN a.re_production_qty ELSE 0 END) AS re_iron_qnty,
						
						sum(CASE WHEN a.production_type ='8' and a.production_date=".$txt_date_from." THEN a.carton_qty ELSE 0 END) AS carton_qty,
						sum(CASE WHEN a.production_type ='8' and a.production_date<".$txt_date_from." THEN a.carton_qty ELSE 0 END) AS carton_qty_pre,
						sum(CASE WHEN a.production_type ='8' and a.production_date=".$txt_date_from." THEN a.production_quantity ELSE 0 END) AS finish_qty,
						sum(CASE WHEN a.production_type ='8' and a.production_date<".$txt_date_from." THEN a.production_quantity ELSE 0 END) AS finish_qty_pre,
						sum(CASE WHEN a.production_type ='7' and a.production_date<".$txt_date_from." THEN a.re_production_qty ELSE 0 END) AS re_iron_qnty_pre,
						sum(CASE WHEN a.production_type ='5' and a.production_date=".$txt_date_from." THEN a.alter_qnty ELSE 0 END) AS alter_today,
						sum(CASE WHEN a.production_type ='8' and a.production_date=".$txt_date_from." THEN a.spot_qnty ELSE 0 END) AS spot_today,
						sum(CASE WHEN a.production_type ='8' and a.production_date<".$txt_date_from." THEN a.spot_qnty ELSE 0 END) AS spot_pre,
						sum(CASE WHEN a.production_type ='5' and a.production_date<".$txt_date_from." THEN a.alter_qnty ELSE 0 END) AS alter_pre
						
						
						from 
						pro_garments_production_mst a
						where
						a.is_deleted=0 and 
						a.status_active=1 and
						a.po_break_down_id in($po_break_down_id_string)
						group by a.po_break_down_id";
						//echo $sql;
					
					$today_production_sql= sql_select($sql);
					foreach($today_production_sql as $val)
					{
						$production_data_arr[$val[csf('po_break_down_id')]]['re_iron_qnty']+=$val[csf('re_iron_qnty')];
						$production_data_arr[$val[csf('po_break_down_id')]]['re_iron_qnty_pre']+=$val[csf('re_iron_qnty_pre')];
						$production_data_arr[$val[csf('po_break_down_id')]]['iron_qnty_by_order']+=$val[csf('iron_qnty')];
						$production_data_arr[$val[csf('po_break_down_id')]]['iron_qnty_by_order_pre']+=$val[csf('iron_qnty_pre')];
						$production_data_arr[$val[csf('po_break_down_id')]]['finish_qty_by_order']+=$val[csf('finish_qty')];
						$production_data_arr[$val[csf('po_break_down_id')]]['finish_qty_by_order_pre']+=$val[csf('finish_qty_pre')];
						$production_data_arr[$val[csf('po_break_down_id')]]['carton_qty']+=$val[csf('carton_qty')];
						$production_data_arr[$val[csf('po_break_down_id')]]['carton_qty_pre']+=$val[csf('carton_qty_pre')];
					
					
						$production_data_arr[$val[csf('po_break_down_id')]]['alter_today']+=$val[csf('alter_today')];
						$production_data_arr[$val[csf('po_break_down_id')]]['alter_pre']+=$val[csf('alter_pre')];
						$production_data_arr[$val[csf('po_break_down_id')]]['spot_today']+=$val[csf('spot_today')];
						$production_data_arr[$val[csf('po_break_down_id')]]['spot_pre']+=$val[csf('spot_pre')];
					
					}
					unset($today_production_sql);
					
				//echo $production_data_arr[25406]['alter_today']	;
		
		//SQL................................................................end;	
				
		//summary part start---------------------------------------------------------------------------------------
				$target_qty_arr=return_library_array( "select a.floor_id,sum(b.target_per_hour*b.working_hour) as target_per_hour from prod_resource_mst a,prod_resource_dtls b where b.pr_date=$txt_date_from and a.company_id=$cbo_company_name group by a.floor_id", "floor_id", "target_per_hour");
				
			//echo "select a.floor_id,sum(b.target_per_hour*b.working_hour) as target_per_hour from prod_resource_mst a,prod_resource_dtls b where b.pr_date=$txt_date_from and a.company_id=$cbo_company_name group by a.floor_id";	
				// data.......................................................
				$sql="SELECT a.floor_id,
						
						sum(CASE WHEN a.production_type ='8' and a.production_date=".$txt_date_from." THEN a.spot_qnty ELSE 0 END) AS spot_qty,
						sum(CASE WHEN a.production_type ='7' and a.production_date=".$txt_date_from." THEN a.production_quantity ELSE 0 END) AS iron_qty,
						sum(CASE WHEN a.production_type ='7' and a.production_date=".$txt_date_from." THEN a.re_production_qty ELSE 0 END) AS re_iron_qty,
						sum(CASE WHEN a.production_type ='8' and a.production_date=".$txt_date_from." THEN a.carton_qty ELSE 0 END) AS carton_qty,
						sum(CASE WHEN a.production_type ='8' and a.production_date=".$txt_date_from." THEN a.production_quantity ELSE 0 END) AS finish_qty
						
						from 
						pro_garments_production_mst a
						where
						a.is_deleted=0 and 
						a.status_active=1 and
						a.production_date=".$txt_date_from." and
						a.company_id=$cbo_company_name
						group by a.floor_id";
					$production_arr= sql_select($sql);	
					foreach($production_arr as $row){
							$tot_iron_qty+=$row[csf(iron_qty)];
							$tot_re_iron_qty+=$row[csf(re_iron_qty)];
							$tot_carton_qty+=$row[csf(carton_qty)];
							$tot_poly_qty+=$row[csf(finish_qty)];
							$tot_spot_qty+=$row[csf(spot_qty)];
					}
						
				//production data.......................................................	
				$sql="SELECT a.floor_id,
						sum(CASE WHEN a.production_type ='5' and a.production_date=".$txt_date_from." THEN a.alter_qnty ELSE 0 END) AS alter_qty,
						sum(CASE WHEN a.production_type ='5' and a.production_date=".$txt_date_from." THEN a.production_quantity ELSE 0 END) AS sewing_out_qty
						
						from 
						pro_garments_production_mst a
						where
						a.is_deleted=0 and 
						a.status_active=1 and
						a.production_date=".$txt_date_from." and
						a.company_id=$cbo_company_name and
						a.floor_id!=0
						group by a.floor_id";
					
				$production_arr= sql_select($sql);
				
				?>
				<table><tr><td width="25%">
				<fieldset style="width:500px;">
					<h3>Sewing PDN & Spot (Floor wise)</h3>
					<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<th>SL</th>
							<th>Floor</th>
							<th>Sewing Target Qty.</th>
							<th>Achive Qty.</th>
							<th>Achive%</th>
							<th>Alter Qty.[Sewing]</th>
							<th>Alter Qty.%</th>
						</thead>
						<? 
						$i=1;
						foreach($production_arr as $row){
							if($row[csf(sewing_out_qty)]){
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
							?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $i;?></td>
							<td><? echo $floor_library[$row[csf(floor_id)]];?></td>
							<td align="right"><? echo $target_qty=$target_qty_arr[$row[csf(floor_id)]];$tot_target_qty+=$target_qty;?></td>
							<td align="right"><? echo $sewing_out_qty=$row[csf(sewing_out_qty)];$tot_sewing_out_qty+=$sewing_out_qty;?></td>
							<td align="right"><? echo number_format(($row[csf(sewing_out_qty)]/$target_qty_arr[$row[csf(floor_id)]])*100,2);?></td>
							<td align="right"><? echo $alter_qty=$row[csf(alter_qty)];$tot_alter_qty+=$alter_qty;?></td>
							<td align="right"><? echo number_format(($row[csf(alter_qty)]/$row[csf(sewing_out_qty)])*100,2);?></td>
						</tr>
						<? $i++;}} ?>
						<tfoot>
							<th></th>
							<th></th>
							<th><? echo $tot_target_qty;?></th>
							<th><? echo $tot_sewing_out_qty;?></th>
							<th><? echo number_format(($tot_sewing_out_qty/$tot_target_qty)*100,2);?></th>
							<th><? echo $tot_alter_qty;?></th>
							<th></th>
						</tfoot>
						
					</table>
				</fieldset>            
			</td><td valign="top"> 
				<fieldset style="width:500px;">
				<h3>Today Finishing Summery</h3>
					<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
						<thead>
							<th>Category</th>
							<th>Receive [ Sewing out]</th>	
							<th>Iron</th>	
							<th>Re-Iron</th>	
							<th>Poly</th>	
							<th>CTN</th>	
							<th>Spot [ Finishing]</th>	
							<th>Poly Balance</th>
						</thead>
						<tr>
							<td>Qty</td>
							<td align="right"><? echo $tot_sewing_out_qty;?></td>	
							<td align="right"><? echo $tot_iron_qty;?></td>	
							<td align="right"><? echo $tot_re_iron_qty;?></td>	
							<td align="right"><? echo $tot_poly_qty;?></td>	
							<td align="right"><? echo $tot_carton_qty;?></td>	
							<td align="right"><? echo $tot_spot_qty;?></td>	
							<td align="right"><? echo $tot_iron_qty-$tot_poly_qty;?></td>
						</tr>
						<tr>
							<td>Per%</td>
							<td align="right"><? //echo $sewing_out_qty=$row[csf(sewing_out_qty)];?></td>	
							<td align="right"><? //echo $iron_qty=$row[csf(iron_qty)];?></td>	
							<td align="right"><? echo number_format(($tot_re_iron_qty/$tot_iron_qty)*100,2);?></td>	
							<td align="right"></td>	
							<td align="right"></td>	
							<td align="right"><? //echo $spot_qty=$row[csf(spot_qty)];?></td>	
							<td align="right"><? echo number_format((($tot_iron_qty-$tot_poly_qty)/$tot_sewing_out_qty)*100,2);?></td>
						</tr>
						
						
					</table>
				</fieldset>            
			</td></tr><tr><td colspan="2">
				
				<fieldset style="width:2470px;">
					<table width="100%"  cellspacing="0">
						<tr class="form_caption" style="border:none;">
							<td colspan="36" align="center" style="border:none;font-size:14px; font-weight:bold" >
								Daily Style Wise Garments Production Status Report
							</td>
						</tr>
						<tr style="border:none;">
							<td colspan="36" align="center" style="border:none; font-size:16px; font-weight:bold">
								Company Name:<? 
									$com_arr=explode(",",str_replace("'","",$cbo_company_name));
									$comName="";
									foreach($com_arr as $comID)
									{
										$comName.=$company_library[$comID].',';
									}
									echo chop($comName,",");
									//echo $company_library[str_replace("'","",$cbo_company_name)]; 
								
								?>                        </td>
						</tr>
						<tr style="border:none;">
							<td colspan="36" align="center" style="border:none;font-size:12px; font-weight:bold">
								<? echo "Date: ". str_replace("'","",$txt_date_from) ;?>
							</td>
						</tr>
					</table>
					<br />	
					<table class="rpt_table" width="2450" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<tr >
								<th width="40" rowspan="2">SL</th>
								<th colspan="4">Style Description</th>
								<th colspan="3">Finish Recive/Sewing Output</th>
								<th colspan="7">Iron & Re-Iron</th>
								<th colspan="4">Alter Sewing</th>
								<th colspan="3">Finishing Spot </th>
								<th colspan="3">Poly</th>
								<th colspan="6">Carton</th>
								<th colspan="5">EX-Factory</th>
							</tr>
							<tr>
								<th width="100">Buyer</th>
								<th width="100">Style</th>
								<th width="140">Colour</th>
								<th width="60">Order Qty)</th>
								<th width="60">Today Recive</th>
								<th width="60">Total Receive</th>
								<th width="60">Receive Balance</th>
								<th width="60">Today Iron</th>
								<th width="60">Total Iron</th>
								<th width="60">Iron Balance</th>
								<th width="60">Today Re-Iron</th>
								<th width="60">Today Re-Iron%</th>
								<th width="60">Total Re-Iron</th>
								<th width="60">Total Re-Iron %</th>
								<th width="60">Today Alter</th>
								<th width="60">Today Alter%</th>
								<th width="60">Total Alter</th>
								<th width="60">Total Alter%</th>
								<th width="60">Spot</th>
								<th width="60">Total Spot</th>
								<th width="60">Total Spot %</th>
								<th width="60">Today Poly</th>
								<th width="60">Total Poly</th>
								<th width="60">Poly Balance</th>
								<th width="60">Pcs Per CTN</th>
								<th width="60">Today CTN</th>
								<th width="60">Today pcs of CTN</th>
								<th width="60">Total CTN</th>
								<th width="60">Pcs TTL CTN</th>
								<th width="60">Pcs CTN Balance</th>
								<th width="80">Last Ship Date</th>
								<th width="60">Today Ship out Qty.</th>
								<th width="60">Total Ship out Qty.</th>
								<th width="100">Shiping Status</th>
								<th>Balance</th>
							</tr>
						</thead>
					</table>
					<div style="width:2470px; max-height:425px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="2450" cellpadding="0" cellspacing="0" border="1" rules="all">
					<? 
					
					$s=1;$i=0;
					foreach($dataArr as $buyerId=>$buyerRows){$i++;
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						$buyer_row_sapn=$buyer_row_sapn_arr[$buyerId]+count($job_row_sapn_arr[$buyerId]);
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td rowspan="<? echo $buyer_row_sapn;?>" width="40"><? echo $s;?></td>
								<td rowspan="<? echo $buyer_row_sapn;?>" width="100"><? echo $buyer_short_library[$buyerId];?></td>
						<?
						$tr2=0;
						foreach($buyerRows as $job_no=>$jobRows){
							
							$job_row_sapn=$job_row_sapn_arr[$buyerId][$job_no];
								if($tr2!=0){
									$i++; 
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
									echo '<tr bgcolor="'.$bgcolor.'" id="tr_'.$i.'" onclick="change_color(\'tr_'.$i.'\',\''.$bgcolor.'\')">';
									}
									echo '<td rowspan="'.$job_row_sapn.'" width="100"><p>'.$style_arr[$job_no].'</p></td>';
							$tr3=0;$tr2=1;
							foreach($jobRows as $color_id=>$order_qty){
								if($tr3!=0){
									$i++; 
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
									echo '<tr bgcolor="'.$bgcolor.'" id="tr_'.$i.'" onclick="change_color(\'tr_'.$i.'\',\''.$bgcolor.'\')">';
								}
								//$tr3=1;
								$sub_tot_order_qty+=$order_qty;
								
								$sewing_out_pre=0;
								$sewing_out_qnty=0;
								$iron_pre=0;
								$iron_qnty=0;
								$ex_factory_qty_pre=0;
								$ex_factory_qty=0;
								$ex_factory_date=0;
								$shiping_status=0;
								$finish_qnty=0;
								$finish_pre=0;
								foreach(explode(",",$color_size_table_id_arr[$job_no][$color_id]) as $color_size_table_id){
									
									$sewing_out_pre+=$production_data_arr[$color_size_table_id]['sewing_out_pre'];	
									$sewing_out_qnty+=$production_data_arr[$color_size_table_id]['sewing_out_qnty'];
									$iron_pre+=$production_data_arr[$color_size_table_id]['iron_pre'];	
									$iron_qnty+=$production_data_arr[$color_size_table_id]['iron_qnty'];
									//Note: finish qty = poly tqy........................................
									$finish_pre+=$production_data_arr[$color_size_table_id]['finish_pre'];	
									$finish_qnty+=$production_data_arr[$color_size_table_id]['finish_qnty'];
								
									$ex_factory_qty_pre+=$production_data_arr[$color_size_table_id]['ex_factory_qty_pre'];	
									$ex_factory_qty+=$production_data_arr[$color_size_table_id]['ex_factory_qty'];
									if($ex_factory_date==0){
									$ex_factory_date=$production_data_arr[$color_size_table_id]['ex_factory_date'];	
									}
									if($shiping_status==0){
									$shiping_status=$production_data_arr[$color_size_table_id]['shiping_status'];
									}
								}
								
								$re_iron_qnty=0;$re_iron_qnty_pre=0;$iron_qnty_by_order=0;$iron_qnty_by_order_pre=0;
								$finish_qty_by_order=0;$finish_qty_by_order_pre=0;$carton_qty=0;$carton_qty_pre=0;
								$alter_pre=0; $alter_today=0; $spot_pre=0; $spot_today=0;
								foreach(array_unique(explode(',',$po_id_arr[$job_no])) as $break_down_id){
									$re_iron_qnty+=$production_data_arr[$break_down_id]['re_iron_qnty'];
									$re_iron_qnty_pre+=$production_data_arr[$break_down_id]['re_iron_qnty_pre'];		
									$iron_qnty_by_order+=$production_data_arr[$break_down_id]['iron_qnty_by_order'];
									$iron_qnty_by_order_pre+=$production_data_arr[$break_down_id]['iron_qnty_by_order_pre'];
									$finish_qty_by_order+=$production_data_arr[$break_down_id]['finish_qty_by_order'];
									$finish_qty_by_order_pre+=$production_data_arr[$break_down_id]['finish_qty_by_order_pre'];
									$carton_qty+=$production_data_arr[$break_down_id]['carton_qty'];
									$carton_qty_pre+=$production_data_arr[$break_down_id]['carton_qty_pre'];
								
									$alter_pre+=$production_data_arr[$break_down_id]['alter_pre'];	
									$alter_today+=$production_data_arr[$break_down_id]['alter_today'];
									$spot_pre+=$production_data_arr[$break_down_id]['spot_pre'];	
									$spot_today+=$production_data_arr[$break_down_id]['spot_today'];
								
								}
								//echo $iron_qnty_by_order;
							//$jobRowsSpan=count($jobRows);	
								
						?>
						
							<td width="140"><p><? echo $color_arr[$color_id];?></p></td>
							<td width="60" align="right"><a href="javascript:mypopup('<? echo $job_no.'**'.$color_id.'**'. $color_arr[$color_id].'**'.$style_arr[$job_no];?>','color_wise_order_qty_break_down',600,300,'Detail Veiw');"><? echo $order_qty;?></a></td>
							<td width="60" align="right">
								<a href="javascript:mypopup('<? echo $job_no.'**'.$color_id.'**'. $color_arr[$color_id].'**'.$style_arr[$job_no].'**'.str_replace("'","",$txt_date_from);?>','today_sewing_out_qty_break_down',600,300,'Today Sewing Out Detail Veiw');">
								<? 
									echo round($sewing_out_qnty); $subTotTodayRecive+=$sewing_out_qnty;
								?>
								</a>
							</td>
							<td width="60" align="right">
								<? 
									echo $totalReceive=round(($sewing_out_qnty+$sewing_out_pre));
									$subTotTotalReceive+=$totalReceive;
								?>
							</td>
							<td width="60" align="right">
								<? 
									echo $receiveBalanc=round($order_qty-($sewing_out_qnty+$sewing_out_pre));
									$subTotReceiveBalance+=$receiveBalanc;
								?>
							</td>
							
							<td width="60" align="right">
								<? 
									echo $todayIron=round($iron_qnty);
									$subTotTodayIron+=$todayIron;
								?>
							</td>
							<td width="60" align="right">
							<? 
								echo $totalIron=round($iron_pre+$iron_qnty);
								$subTotTotalIron+=$totalIron;
							?>
							</td>
							<td width="60" align="right">
								<? 
									echo $ironBalance=round(($sewing_out_qnty+$sewing_out_pre)-($iron_pre+$iron_qnty));
									$subTotIronBalance+=$ironBalance;
								?>
							</td>
							<? if($tr3==0){?>
							<td rowspan="<? echo $job_row_sapn;?>" width="60" align="right">
								<? 
									echo round($re_iron_qnty);
									$subTotTodayReIron+=$re_iron_qnty;
								?>
							</td>
							<td rowspan="<? echo $job_row_sapn;?>" width="60" align="right">
								<? 
									$totalReIronPer=($re_iron_qnty*100)/$iron_qnty_by_order;
									$subTotTotalReIronPer+=$totalReIronPer;
									echo number_format($totalReIronPer,2);
								?>
							</td>
							<td rowspan="<? echo $job_row_sapn;?>" width="60" align="right">
								<? 
									echo $totalReIron=round($re_iron_qnty+$re_iron_qnty_pre);
									$subTotTotalReIron+=$totalReIron;
									
								?>
							</td>
							<td rowspan="<? echo $job_row_sapn;?>" width="60" align="right">
								<? 
									$totalReIronPer=($re_iron_qnty+$re_iron_qnty_pre)*100/($iron_qnty_by_order+$iron_qnty_by_order_pre);
									$subTotTotalReIronPer+=$totalReIronPer;
									echo number_format($totalReIronPer,2);
								?>
							</td>
							<td rowspan="<? echo $job_row_sapn;?>" width="60" align="right">
								<? 
									echo round($alter_today);
									$subTotTodayAlter+=$alter_today;
								?>
							</td>
							<td rowspan="<? echo $job_row_sapn;?>" width="60" align="right">
								<? 
									$todayAlterPer=($alter_today*100)/$iron_qnty;
									$subTotTodayAlterPer+=$todayAlterPer;
									echo number_format($todayAlterPer,2);
								?>
							</td>
							<td rowspan="<? echo $job_row_sapn;?>" width="60" align="right">
								<? 	
									echo $totalAlter=round(($alter_today+$alter_pre));
									$subTotTotalAlter=$totalAlter;
								?>
							</td>
							<td rowspan="<? echo $job_row_sapn;?>" width="60" align="right">
								<? 
									$totalAlterPer=(($alter_today+$alter_pre)*100)/($iron_pre+$iron_qnty);
									$subTotTotalAlterPer=$totalAlterPer;
									echo number_format($totalAlterPer,2);
								?>
							</td>
							<td rowspan="<? echo $job_row_sapn;?>" width="60" align="right">
								<? 
									echo $spot=round($spot_today);
									$sub_totSpot+=$spot;
								?>
							</td>
							<td rowspan="<? echo $job_row_sapn;?>" width="60" align="right">
								<? 
									echo $totalSpot=round(($spot_today+$spot_pre));
									$subTotTotalSpot+=$totalSpot;
								?>
							</td>
							<td rowspan="<? echo $job_row_sapn;?>" width="60" align="right">
								<? 
									$totalSpotPer=(($spot_today+$spot_pre)*100)/$iron_qnty;
									$subTotTotalSpotPer+=$totalSpotPer;
									echo number_format($totalSpotPer,2);
									
								?>
							</td>
							
							<? 
							//$tr3=1;
							} 
							?>
							
							
							<td width="60" align="right">
								<? 
									echo $todayPoly=round($finish_qnty);
									$subTotTodayPoly+=$todayPoly;
								?>
							</td>
							<td width="60" align="right">
								<? 
									echo $totalPoly=round($finish_qnty+$finish_pre);
									$subTotTotalPoly+=$totalPoly;
									
								?>
							</td>
							<td width="60" align="right">
								<? 
									echo $polyBalance=round(($sewing_out_qnty+$sewing_out_pre)-($finish_qnty+$finish_pre));
									$subTotPolyBalance+=$polyBalance;
								?>
							</td>
							<? if($tr3==0){?>
							<td width="60" align="right" rowspan="<? echo $job_row_sapn;?>">
								<? 
									echo $pcsPerCTN=round(($finish_qty_by_order+$finish_qty_by_order_pre)/($carton_qty+$carton_qty_pre));
									$subTotPcsPerCTN+=$pcsPerCTN;
								?>
							</td>
							<td width="60" align="right" rowspan="<? echo $job_row_sapn;?>">
								<? 
									echo round($carton_qty);
									$subTotTodayCTN+=$carton_qty;
								?>
							</td>
							<td width="60" align="right" rowspan="<? echo $job_row_sapn;?>">
								<? 
									//echo $todaypcsofCTN=round($finish_qty_by_order/$carton_qty);
									//$subTotTodaypcsofCTN=$todaypcsofCTN;
									
									echo $todaypcsofCTN=round($carton_qty*$pcsPerCTN);
									$subTotTodaypcsofCTN=$todaypcsofCTN;
									
								?>
							</td>
							<td width="60" align="right" rowspan="<? echo $job_row_sapn;?>">
								<? 
									echo $totalCTN=round(($carton_qty+$carton_qty_pre));
									$subTotTotalCTN+=$totalCTN;
								?>
							</td>
							
							
							<td width="60" align="right" rowspan="<? echo $job_row_sapn;?>">
								<? 
									echo $pcsTTLCTN=round(($finish_qty_by_order+$finish_qty_by_order_pre));
									$subTotPcsTTLCTN+=$pcsTTLCTN;
								?>
							</td>
							<td width="60" align="right" rowspan="<? echo $job_row_sapn;?>">
								<? 
									echo $pcsCTNBalance=round(($totalPoly-$pcsTTLCTN));
									$subTotPcsCTNBalance+=$pcsCTNBalance;
								?>
							
							</td>
							<? 
							$tr3=1;
							} 
							?>
							
							<td width="80" align="center">
								<? 
									echo change_date_format($ex_factory_date);
								?>
							</td>
							<td width="60" align="right">
								<? 
									echo round($ex_factory_qty);
									$subTotTodayShipoutQty+=$ex_factory_qty;
								?>
								</td>
							<td width="60" align="right">
								<? 
									echo $totalShipoutQty=round(($ex_factory_qty_pre+$ex_factory_qty));
									$subTotTotalShipoutQty+=$totalShipoutQty;
								?>
							</td>
							<td width="100" align="center">
								<? 
									echo $shipment_status[$shiping_status];
								?>
								</td>
							<td align="right">
								<? 
									echo $balance=round(($sewing_out_qnty+$sewing_out_pre)-($ex_factory_qty_pre+$ex_factory_qty));
									$subTotBalance+=$balance;
								?>
								</td>
						</tr>
					<? 
							}
							
							?>
							<tr style="background:#CCC; font-weight:bold;">
								<td colspan="2" align="center">SUB TOTAL</td>
								<td align="right"><p><? echo $sub_tot_order_qty;?></p></td>
								<td align="right"><p><? echo $subTotTodayRecive;?></p></td>
								<td align="right"><p><? echo $subTotTotalReceive;?></p></td>
								<td align="right"><p><? echo $subTotReceiveBalance;?></p></td>
								<td align="right"><p><? echo $subTotTodayIron;?></p></td>
								<td align="right"><p><? echo $subTotTotalIron;?></p></td>
								<td align="right"><p><? echo $subTotIronBalance;?></p></td>
								<td align="right"><p><? echo $subTotTodayReIron;?></p></td>
								<td align="right"><p><? //echo number_format($subTotTotalReIronPer,2);?></p></td>
								<td align="right"><p><? echo $subTotTotalReIron;?></p></td>
								<td align="right"><p><? //echo number_format($subTotTotalReIronPer,2);?></p></td>
								<td align="right"><p><? echo $subTotTodayAlter;?></p></td>
								<td align="right"><p><? //echo number_format($subTotTodayAlterPer,2);?></p></td>
								<td align="right"><p><? echo $subTotTotalAlter;?></p></td>
								<td align="right"><p><? //echo number_format($subTotTotalAlterPer,2);?></p></td>
								<td align="right"><p><? echo $subTotSpot;?></p></td>
								<td align="right"><p><? echo $subTotTotalSpot;?></p></td>
								<td align="right"><p><? //echo number_format($subTotTotalSpotPer,2);?></p></td>
								<td align="right"><p><? echo $subTotTodayPoly;?></p></td>
								<td align="right"><p><? echo $subTotTotalPoly;?></p></td>
								<td align="right"><p><? echo $subTotPolyBalance;?></p></td>
								<td align="right"><p><? echo $subTotPcsPerCTN;?></p></td>
								<td align="right"><p><? echo $subTotTodayCTN;?></p></td>
								<td align="right"><p><? echo $subTotTodaypcsofCTN;?></p></td>
								<td align="right"><p><? echo $subTotTotalCTN;?></p></td>
								<td align="right"><p><? echo $subTotPcsTTLCTN;?></p></td>
								<td align="right"><p><? echo $subTotPcsCTNBalance;?></p></td>
								<td></td>
								<td align="right"><p><? echo $subTotTodayShipoutQty;?></p></td>
								<td align="right"><p><? echo $subTotTotalShipoutQty;?></p></td>
								<td></td>
								<td align="right"><p><? echo $subTotBalance;?></p></td>
							</tr>
							<?
							$grn_tot_order_qty+=$sub_tot_order_qty;
							$grnTotTodayRecive+=$subTotTodayRecive;
							$grnTotTotalReceive+=$subTotTotalReceive;
							$grnTotReceiveBalance+=$subTotReceiveBalance;
							$grnTotTodayIron+=$subTotTodayIron;
							$grnTotTotalIron+=$subTotTotalIron;
							$grnTotIronBalance+=$subTotIronBalance;
							$grnTotTodayReIron+=$subTotTodayReIron;
							$grnTotTodayReIron+=$subTotTodayReIron;
							$grnTotTotalReIron+=$subTotTotalReIron;
							$grnTotTotalReIronPer+=$subTotTotalReIronPer;
							$grnTotTodayAlter+=$subTotTodayAlter;
							$grnTotTodayAlterPer+=$subTotTodayAlterPer;
							$grnTotTotalAlter+=$subTotTotalAlter;
							$grnTotTotalAlterPer+=$subTotTotalAlterPer;
							$grnTotSpot+=$subTotSpot;
							$grnTotTotalSpot+=$subTotTotalSpot;
							$grnTotTotalSpotPer+=$subTotTotalSpotPer;
							$grnTotTodayPoly+=$subTotTodayPoly;
							$grnTotTotalPoly+=$subTotTotalPoly;
							$grnTotPolyBalance+=$subTotPolyBalance;
							$grnTotPcsPerCTN+=$subTotPcsPerCTN;
							$grnTotTodayCTN+=$subTotTodayCTN;
							$grnTotTodaypcsofCTN+=$subTotTodaypcsofCTN;
							$grnTotTotalCTN+=$subTotTotalCTN;
							$grnTotPcsTTLCTN+=$subTotPcsTTLCTN;
							$grnTotPcsCTNBalance+=$subTotPcsCTNBalance;
							$grnTotTodayShipoutQty+=$subTotTodayShipoutQty;
							$grnTotTotalShipoutQty+=$subTotTotalShipoutQty;
							$grnTotBalance+=$subTotBalance;
							
							
							$sub_tot_order_qty=0;
							$subTotTodayRecive=0;
							$subTotTotalReceive=0;
							$subTotReceiveBalance=0;
							$subTotTodayIron=0;
							$subTotTotalIron=0;
							$subTotIronBalance=0;
							$subTotTodayReIron=0;
							$subTotTodayReIron=0;
							$subTotTotalReIron=0;
							$subTotTotalReIronPer=0;
							$subTotTodayAlter=0;
							$subTotTodayAlterPer=0;
							$subTotTotalAlter=0;
							$subTotTotalAlterPer=0;
							$subTotSpot=0;
							$subTotTotalSpot=0;
							$subTotTotalSpotPer=0;
							$subTotTodayPoly=0;
							$subTotTotalPoly=0;
							$subTotPolyBalance=0;
							$subTotPcsPerCTN=0;
							$subTotTodayCTN=0;
							$subTotTodaypcsofCTN=0;
							$subTotTotalCTN=0;
							$subTotPcsTTLCTN=0;
							$subTotPcsCTNBalance=0;
							$subTotTodayShipoutQty=0;
							$subTotTotalShipoutQty=0;
							$subTotBalance=0;
						}
						$s++; 
					} 
					
					?>    
				</table>
			</div>
			
			<div style="width:2470px; max-height:425px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="2450" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tfoot>
						<th colspan="4" align="center">GRAND TOTAL</th>
						<th align="right" width="60"><p><? echo $grn_tot_order_qty;?></p></th>
						<th align="right" width="60"><p><? echo $grnTotTodayRecive;?></p></th>
						<th align="right" width="60"><p><? echo $grnTotTotalReceive;?></p></th>
						<th align="right" width="60"><p><? echo $grnTotReceiveBalance;?></p></th>
						<th align="right" width="60"><p><? echo $grnTotTodayIron;?></p></th>
						<th align="right" width="60"><p><? echo $grnTotTotalIron;?></p></th>
						<th align="right" width="60"><p><? echo $grnTotIronBalance;?></p></th>
						<th align="right" width="60"><p><? echo $grnTotTodayReIron;?></p></th>
						<th align="right" width="60"><p><? //echo number_format($grnTotTotalReIronPer,2);?></p></th>
						<th align="right" width="60"><p><? echo $grnTotTotalReIron;?></p></th>
						<th align="right" width="60"><p><? //echo number_format($grnTotTotalReIronPer,2);?></p></th>
						<th align="right" width="60"><p><? echo $grnTotTodayAlter;?></p></th>
						<th align="right" width="60"><p><? //echo number_format($grnTotTodayAlterPer,2);?></p></th>
						<th align="right" width="60"><p><? echo $grnTotTotalAlter;?></p></th>
						<th align="right" width="60"><p><? //echo number_format($grnTotTotalAlterPer,2);?></p></th>
						<th align="right" width="60"><p><? echo $grnTotSpot;?></p></th>
						<th align="right" width="60"><p><? echo $grnTotTotalSpot;?></p></th>
						<th align="right" width="60"><p><? //echo number_format($grnTotTotalSpotPer,2);?></p></th>
						<th align="right" width="60"><p><? echo $grnTotTodayPoly;?></p></th>
						<th align="right" width="60"><p><? echo $grnTotTotalPoly;?></p></th>
						<th align="right" width="60"><p><? echo $grnTotPolyBalance;?></p></th>
						<th align="right" width="60"><p><? echo $grnTotPcsPerCTN;?></p></th>
						<th align="right" width="60"><p><? echo $grnTotTodayCTN;?></p></th>
						<th align="right" width="60"><p><? echo $grnTotTodaypcsofCTN;?></p></th>
						<th align="right" width="60"><p><? echo $grnTotTotalCTN;?></p></th>
						<th align="right" width="60"><p><? echo $grnTotPcsTTLCTN;?></p></th>
						<th align="right" width="60"><p><? echo $grnTotPcsCTNBalance;?></p></th>
						<th width="80"></th>
						<th align="right" width="60"><p><? echo $grnTotTodayShipoutQty;?></p></th>
						<th align="right" width="60"><p><? echo $grnTotTotalShipoutQty;?></p></th>
						<th width="100"></th>
						<th align="right" width="113"><p><? echo $grnTotBalance;?></p></th>
					</tfoot>
				</table>	
			</div>
			</fieldset>
			
			
		</td> </tr></table>
			<?	
			}
		}
	
		else if(str_replace("'","",$report_type)==4) //Report 3 
		{
			if(str_replace("'","",$cbo_search_by)==1 || str_replace("'","",$cbo_search_by)==3 || str_replace("'","",$cbo_search_by)==4 || str_replace("'","",$cbo_search_by)==5)
			{
			
				if(str_replace("'","",$hidden_order_id)!=""){
					$order_cond="and a.id in(".str_replace("'","",$hidden_order_id).")";
				}
				else{$order_cond="";}
			
			
			
			//SQL...............................................................start;
					//Today Production.....................
					$sql="SELECT b.color_size_break_down_id,
						sum(CASE WHEN a.production_type ='1' and a.production_date=".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS cutting_qnty,
						sum(CASE WHEN a.production_type ='2' and a.production_date=".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS printing_qnty,
						sum(CASE WHEN a.production_type ='3' and a.production_date=".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS printreceived_qnty,
						sum(CASE WHEN a.production_type ='4' and a.production_date=".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS sewingin_qnty,
						sum(CASE WHEN a.production_type ='5' and a.production_date=".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS sewing_out_qnty,
						sum(CASE WHEN a.production_type ='7' and a.production_date=".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS iron_qnty,
						sum(CASE WHEN a.production_type ='5' and a.production_date=".$txt_date_from." THEN b.reject_qty ELSE 0 END) AS reject_today,
						sum(CASE WHEN a.production_type ='8' and a.production_date=".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS finish_qnty
						from 
						pro_garments_production_mst a,pro_garments_production_dtls b
						where
						a.id=b.mst_id and
						a.production_type=b.production_type and
						a.is_deleted=0 and 
						a.status_active=1 and a.production_date=".$txt_date_from." and
						b.color_size_break_down_id >0 and a.company_id=$cbo_company_name
						group by b.color_size_break_down_id";
					
					$color_size_break_down_id_arr=array(0);
					$production_data_arr=array();  		
					$today_production_sql= sql_select($sql);
					foreach($today_production_sql as $val)
					{
						$production_data_arr[$val[csf('color_size_break_down_id')]]['cutting_qnty']+=$val[csf('cutting_qnty')];
						$production_data_arr[$val[csf('color_size_break_down_id')]]['printing_qnty']+=$val[csf('printing_qnty')];
						$production_data_arr[$val[csf('color_size_break_down_id')]]['printreceived_qnty']+=$val[csf('printreceived_qnty')];
						$production_data_arr[$val[csf('color_size_break_down_id')]]['sewingin_qnty']+=$val[csf('sewingin_qnty')];
						$production_data_arr[$val[csf('color_size_break_down_id')]]['sewing_out_qnty']+=$val[csf('sewing_out_qnty')];
						$production_data_arr[$val[csf('color_size_break_down_id')]]['iron_qnty']+=$val[csf('iron_qnty')];
						$production_data_arr[$val[csf('color_size_break_down_id')]]['finish_qnty']+=$val[csf('finish_qnty')];
						$production_data_arr[$val[csf('color_size_break_down_id')]]['reject_today']+=$val[csf('reject_today')];
						$color_size_break_down_id_arr[$val[csf('color_size_break_down_id')]]=$val[csf('color_size_break_down_id')];
				
					}
					unset($today_production_sql);
					// echo $sql;

		//Today exfactory qty------------------------------------------	
			
				$sql="SELECT max(a.ex_factory_date) as ex_factory_date,a.shiping_status,b.color_size_break_down_id,
						sum(CASE WHEN a.ex_factory_date=".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS ex_factory_qty
						
						from 
						pro_ex_factory_mst a,pro_ex_factory_dtls b
						where
						a.id=b.mst_id and
						a.is_deleted=0 and 
						b.color_size_break_down_id >0 
						and a.shiping_status!=3 and
						a.status_active=1 and a.ex_factory_date=".$txt_date_from." 
						group by a.shiping_status,b.color_size_break_down_id";
					
						//echo $sql;
					$ex_factory_sql= sql_select($sql);
					foreach($ex_factory_sql as $val)
					{
						$production_data_arr[$val[csf('color_size_break_down_id')]]['ex_factory_qty']+=$val[csf('ex_factory_qty')];
						$production_data_arr[$val[csf('color_size_break_down_id')]]['ex_factory_date']=$val[csf('ex_factory_date')];
						$production_data_arr[$val[csf('color_size_break_down_id')]]['shiping_status']=$val[csf('shiping_status')];
						$color_size_break_down_id_arr[$val[csf('color_size_break_down_id')]]=$val[csf('color_size_break_down_id')];

					}
					$color_size_break_down_id_string=implode(',',$color_size_break_down_id_arr);
					unset($ex_factory_sql);





		//Get All Job from color breakdown id------------------------------------------------------------
				$sql="select c.job_no_mst from  wo_po_color_size_breakdown c where c.status_active=1  and c.id in($color_size_break_down_id_string)";
			$sql_result=sql_select($sql);
			foreach($sql_result as $row){
				$job_no_arr[$row[csf('job_no_mst')]]=$row[csf('job_no_mst')];
			}
		$job_no_arr_string=implode("','",$job_no_arr);
		unset($sql_result);
		unset($color_size_break_down_id_arr);
			//-----------------------------------------------------------------------



			//Order Data------------------------------------------------------------
		if($db_type==0)
		{
			$sql="select b.job_no,b.job_no_prefix_num,b.buyer_name,b.style_ref_no,sum(c.order_quantity) as job_qty_pcs ,group_concat(a.id) as po_id,c.color_number_id,group_concat(c.id) as color_size_table_id
				from  wo_po_break_down a,wo_po_details_master b,wo_po_color_size_breakdown c
				where a.id=c.po_break_down_id and a.job_no_mst=b.job_no  $company_name $buyer_name $job_cond_id $style_cond $order_cond $ship_status_cond $year_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and b.job_no in('$job_no_arr_string')  
				group by b.job_no,b.job_no_prefix_num, b.buyer_name, b.style_ref_no,c.color_number_id
				order by b.buyer_name,b.job_no_prefix_num";	
		}
		else if($db_type==2)
		{
			$sql="select b.job_no,b.job_no_prefix_num,b.buyer_name,b.style_ref_no,sum(c.order_quantity) as job_qty_pcs , listagg(a.id,',') within group (order by a.id) as po_id,c.color_number_id,listagg(c.id,',') within group (order by c.id) as color_size_table_id
				from wo_po_break_down a,wo_po_details_master b,wo_po_color_size_breakdown c
				where a.id=c.po_break_down_id and a.job_no_mst=b.job_no $company_name $buyer_name $job_cond_id $style_cond $order_cond $ship_status_cond $year_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and b.job_no in('$job_no_arr_string') 
				group by b.job_no,b.job_no_prefix_num, b.buyer_name, b.style_ref_no,c.color_number_id
				order by b.buyer_name, b.job_no_prefix_num";
					
		}
	
	    // echo $sql;
		$po_id_arr=array(0);
		$sql_result=sql_select($sql);
		
		foreach($sql_result as $row){
			$production_data_arr[$val[csf('color_size_break_down_id')]]['sewing_out_qnty'];
			$dataArr[$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('color_number_id')]]+=$row[csf('job_qty_pcs')];
			$style_arr[$row[csf('job_no')]]=$row[csf('style_ref_no')];
			$color_size_table_id_arr[$row[csf('job_no')]][$row[csf('color_number_id')]]=$row[csf('color_size_table_id')];
			$po_id_arr[$row[csf('job_no')]]=$row[csf('po_id')];
			//$buyer_row_sapn_arr[$row[csf('buyer_name')]]+=1;
			//$job_row_sapn_arr[$row[csf('buyer_name')]][$row[csf('job_no')]]+=1;
		}
		$po_break_down_id_string=implode(',',$po_id_arr);
		unset($sql_result);





				//Previous Production...............................
				$sql="SELECT b.color_size_break_down_id,
					
					sum(CASE WHEN a.production_type ='1' and a.production_date<".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS cutting_qnty_pre,
					sum(CASE WHEN a.production_type ='2' and a.production_date<".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS printing_qnty_pre,
					sum(CASE WHEN a.production_type ='3' and a.production_date<".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS printreceived_qnty_pre,
					sum(CASE WHEN a.production_type ='4' and a.production_date<".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS sewingin_qnty_pre,
					sum(CASE WHEN a.production_type ='5' and a.production_date<".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS sewing_out_pre,
					sum(CASE WHEN a.production_type ='7' and a.production_date<".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS iron_pre,
					sum(CASE WHEN a.production_type ='7' and a.production_date<".$txt_date_from." THEN a.re_production_qty ELSE 0 END) AS re_iron_pre,
					sum(CASE WHEN a.production_type ='8' and a.production_date<".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS finish_pre,					
					sum(CASE WHEN a.production_type ='5' and a.production_date<".$txt_date_from." THEN b.reject_qty ELSE 0 END) AS reject_pre
					
					
					from 
					pro_garments_production_mst a,pro_garments_production_dtls b
					where
					a.id=b.mst_id and
					a.production_type=b.production_type and
					a.is_deleted=0 and 
					a.status_active=1 and a.production_date<".$txt_date_from." and
					a.po_break_down_id in($po_break_down_id_string)
					group by b.color_size_break_down_id";
				
					 //echo $sql;
	
                $pre_production_sql= sql_select($sql);
                foreach($pre_production_sql as $val)
                {
					$production_data_arr[$val[csf('color_size_break_down_id')]]['cutting_qnty_pre']=$val[csf('cutting_qnty_pre')];
					$production_data_arr[$val[csf('color_size_break_down_id')]]['printing_qnty_pre']=$val[csf('printing_qnty_pre')];
					$production_data_arr[$val[csf('color_size_break_down_id')]]['printreceived_qnty_pre']=$val[csf('printreceived_qnty_pre')];
					$production_data_arr[$val[csf('color_size_break_down_id')]]['sewingin_qnty_pre']=$val[csf('sewingin_qnty_pre')];
					$production_data_arr[$val[csf('color_size_break_down_id')]]['sewing_out_pre']=$val[csf('sewing_out_pre')];
					$production_data_arr[$val[csf('color_size_break_down_id')]]['iron_pre']=$val[csf('iron_pre')];
					$production_data_arr[$val[csf('color_size_break_down_id')]]['finish_pre']=$val[csf('finish_pre')];
					$production_data_arr[$val[csf('color_size_break_down_id')]]['reject_pre']=$val[csf('reject_pre')];
					$production_data_arr[$val[csf('color_size_break_down_id')]]['re_iron_pre']=$val[csf('re_iron_pre')];
			   
			    }
				unset($pre_production_sql); 

                $sql="SELECT max(a.ex_factory_date) as ex_factory_date,a.shiping_status,b.color_size_break_down_id,a.shiping_status,
					sum(CASE WHEN a.ex_factory_date<".$txt_date_from." THEN b.production_qnty ELSE 0 END) AS ex_factory_qty_pre
					
					from 
					pro_ex_factory_mst a,pro_ex_factory_dtls b
					where
					a.id=b.mst_id and
					a.is_deleted=0 and 
					a.status_active=1 and a.ex_factory_date<".$txt_date_from." and
					a.po_break_down_id in($po_break_down_id_string)
					group by a.shiping_status,b.color_size_break_down_id,a.shiping_status";
				
					 //echo $sql;
	
                $ex_factory_sql= sql_select($sql);
                foreach($ex_factory_sql as $val)
                {
					$production_data_arr[$val[csf('color_size_break_down_id')]]['ex_factory_qty_pre']+=$val[csf('ex_factory_qty_pre')];
					$production_data_arr[$val[csf('color_size_break_down_id')]]['shiping_status']=$val[csf('shiping_status')];
				}
				unset($ex_factory_sql);
	
	
				//Re Production.....................
				
			    $sql="SELECT a.po_break_down_id,
					sum(CASE WHEN a.production_type ='7' and a.production_date=".$txt_date_from." THEN a.production_quantity ELSE 0 END) AS iron_qnty,
					sum(CASE WHEN a.production_type ='7' and a.production_date<".$txt_date_from." THEN a.production_quantity ELSE 0 END) AS iron_qnty_pre,
					sum(CASE WHEN a.production_type ='7' and a.production_date=".$txt_date_from." THEN a.re_production_qty ELSE 0 END) AS re_iron_qnty,
					
					sum(CASE WHEN a.production_type ='8' and a.production_date=".$txt_date_from." THEN a.carton_qty ELSE 0 END) AS carton_qty,
					sum(CASE WHEN a.production_type ='8' and a.production_date<".$txt_date_from." THEN a.carton_qty ELSE 0 END) AS carton_qty_pre,
					sum(CASE WHEN a.production_type ='8' and a.production_date=".$txt_date_from." THEN a.production_quantity ELSE 0 END) AS finish_qty,
					sum(CASE WHEN a.production_type ='8' and a.production_date<".$txt_date_from." THEN a.production_quantity ELSE 0 END) AS finish_qty_pre,
					sum(CASE WHEN a.production_type ='7' and a.production_date<".$txt_date_from." THEN a.re_production_qty ELSE 0 END) AS re_iron_qnty_pre,
					sum(CASE WHEN a.production_type ='5' and a.production_date=".$txt_date_from." THEN a.alter_qnty ELSE 0 END) AS alter_today,
					sum(CASE WHEN a.production_type ='8' and a.production_date=".$txt_date_from." THEN a.spot_qnty ELSE 0 END) AS spot_today,
					sum(CASE WHEN a.production_type ='8' and a.production_date<".$txt_date_from." THEN a.spot_qnty ELSE 0 END) AS spot_pre,
					sum(CASE WHEN a.production_type ='5' and a.production_date<".$txt_date_from." THEN a.alter_qnty ELSE 0 END) AS alter_pre
					
					
					from 
					pro_garments_production_mst a
					where
					a.is_deleted=0 and 
					a.status_active=1 and
					a.po_break_down_id in($po_break_down_id_string)
					group by a.po_break_down_id";
					//echo $sql;
				
                $today_production_sql= sql_select($sql);
                foreach($today_production_sql as $val)
                {
					$production_data_arr[$val[csf('po_break_down_id')]]['re_iron_qnty']+=$val[csf('re_iron_qnty')];
					$production_data_arr[$val[csf('po_break_down_id')]]['re_iron_qnty_pre']+=$val[csf('re_iron_qnty_pre')];
					$production_data_arr[$val[csf('po_break_down_id')]]['iron_qnty_by_order']+=$val[csf('iron_qnty')];
					$production_data_arr[$val[csf('po_break_down_id')]]['iron_qnty_by_order_pre']+=$val[csf('iron_qnty_pre')];
					$production_data_arr[$val[csf('po_break_down_id')]]['finish_qty_by_order']+=$val[csf('finish_qty')];
					$production_data_arr[$val[csf('po_break_down_id')]]['finish_qty_by_order_pre']+=$val[csf('finish_qty_pre')];
					$production_data_arr[$val[csf('po_break_down_id')]]['carton_qty']+=$val[csf('carton_qty')];
					$production_data_arr[$val[csf('po_break_down_id')]]['carton_qty_pre']+=$val[csf('carton_qty_pre')];
				
				
					$production_data_arr[$val[csf('po_break_down_id')]]['alter_today']+=$val[csf('alter_today')];
					$production_data_arr[$val[csf('po_break_down_id')]]['alter_pre']+=$val[csf('alter_pre')];
					$production_data_arr[$val[csf('po_break_down_id')]]['spot_today']+=$val[csf('spot_today')];
					$production_data_arr[$val[csf('po_break_down_id')]]['spot_pre']+=$val[csf('spot_pre')];
				
				}
				unset($today_production_sql);
				 
			//echo $production_data_arr[25406]['alter_today']	;
	
			//SQL................................................................end;	
			
			//summary part start---------------------------------------------------------------------------------------
			$target_qty_arr=return_library_array( "select a.floor_id,sum(b.target_per_hour*b.working_hour) as target_per_hour from prod_resource_mst a,prod_resource_dtls b where b.pr_date=$txt_date_from and a.company_id=$cbo_company_name group by a.floor_id", "floor_id", "target_per_hour");
			
		 
			// data.......................................................
			  $sql="SELECT a.floor_id,
					
					sum(CASE WHEN a.production_type ='8' and a.production_date=".$txt_date_from." THEN a.spot_qnty ELSE 0 END) AS spot_qty,
					sum(CASE WHEN a.production_type ='7' and a.production_source=1 and a.production_date=".$txt_date_from." THEN a.production_quantity ELSE 0 END) AS iron_in_qty,
					sum(CASE WHEN a.production_type ='7' and a.production_source=3 and a.production_date=".$txt_date_from." THEN a.production_quantity ELSE 0 END) AS iron_out_qty,
					sum(CASE WHEN a.production_type ='7' and a.production_date=".$txt_date_from." THEN a.re_production_qty ELSE 0 END) AS re_iron_qty,
					sum(CASE WHEN a.production_type ='8' and a.production_date=".$txt_date_from." THEN a.carton_qty ELSE 0 END) AS carton_qty,
					sum(CASE WHEN a.production_type ='8' and a.production_source=1 and a.production_date=".$txt_date_from." THEN a.production_quantity ELSE 0 END) AS finish_qty_in,
						sum(CASE WHEN a.production_type ='8' and a.production_source=3 and a.production_date=".$txt_date_from." THEN a.production_quantity ELSE 0 END) AS finish_qty_out
					
					from 
					pro_garments_production_mst a
					where
					a.is_deleted=0 and 
					a.status_active=1 and
					a.production_date=".$txt_date_from." and
					a.company_id=$cbo_company_name
					group by a.floor_id";
				 $production_arr= sql_select($sql);	
				 $tot_iron_in_qty=$tot_re_iron_qty=$tot_iron_out_qty=$tot_carton_qty=$tot_finish_qty_in=$tot_finish_qty_out=0;
				 foreach($production_arr as $row){
						$tot_iron_in_qty+=$row[csf('iron_in_qty')];
						$tot_iron_out_qty+=$row[csf('iron_out_qty')];
						$tot_re_iron_qty+=$row[csf('re_iron_qty')];
						$tot_carton_qty+=$row[csf('carton_qty')];
						$tot_finish_qty_in+=$row[csf('finish_qty_in')];
						$tot_finish_qty_out+=$row[csf('finish_qty_out')];
						
				 }
					
			//production data.......................................................	
			$sql="SELECT a.floor_id,
					sum(CASE WHEN a.production_type ='5' and a.production_date=".$txt_date_from." THEN a.alter_qnty ELSE 0 END) AS alter_qty,
					sum(CASE WHEN a.production_type ='5' and a.production_source=3 and a.production_date=".$txt_date_from." THEN a.production_quantity ELSE 0 END) AS sewing_out_qty,
					sum(CASE WHEN a.production_type ='5' and a.production_source=1 and a.production_date=".$txt_date_from." THEN a.production_quantity ELSE 0 END) AS sewing_in_qty
					
					from 
					pro_garments_production_mst a
					where
					a.is_deleted=0 and 
					a.status_active=1 and
					a.production_date=".$txt_date_from." and
					a.company_id=$cbo_company_name and
					 a.floor_id!=0
					group by a.floor_id";
				
			  $production_arr= sql_select($sql);
			  $tot_sewing_out_qty=$tot_sewing_in_qty=0;
			   foreach($production_arr as $row)
			   {
				   $tot_sewing_out_qty+=$row[csf('sewing_out_qty')]; 
				   $tot_sewing_in_qty+=$row[csf('sewing_in_qty')];
			   }
			
			?>
			<table><tr>
            <td valign="top"> 
            <fieldset style="width:850px;">
          
                <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                 <caption> <h3>Today Finishing Summary</h3></caption>
                    <thead>
                    <tr>
                        <th rowspan="2"  width="100">Category</th>
                        <th  colspan="2" width="170">Today Receive[Sewing out]</th>	
                        <th colspan="2"  width="150">Today Iron	</th>	
                        <th  rowspan="2"  width="100">Re-Iron</th>	
                        <th  colspan="2"  width="150">Today Packing</th>	
                        <th  rowspan="2"  width="100">Today Carton</th>	
                        <th rowspan="2"  width="100">Total Packing Balance</th>	
                      </tr>
                      <tr>
                       		<th>In-House</th>
                            <th>Out-Bound</th>
                            <th>In-House</th>
                            <th>Out-Bound</th>
                             <th>In-House</th>
                            <th>Out-Bound</th>
                      </tr>
                    </thead>
                    <tr bgcolor="#FFFFFF">
                        <td><b>Qty</b></td>
                        <td align="right"><? echo number_format($tot_sewing_in_qty,2);?></td>	
                        <td align="right"><? echo number_format($tot_sewing_out_qty,2);?></td>	
                        <td align="right"><? echo number_format($tot_iron_in_qty,2);?></td>	
                        <td align="right"><? echo number_format($tot_iron_out_qty,2);?></td>	
                        <td align="right"><? echo number_format($tot_re_iron_qty,2);?></td>	
                        <td align="right"><? echo number_format($tot_finish_qty_in,2);?></td>	
                        <td align="right"><? echo number_format($tot_finish_qty_out,2);?></td>	
                        <td align="right"><? echo number_format($tot_carton_qty,2);?></td>	
                        <td align="right"><? echo number_format($tot_finish_qty_in+$tot_finish_qty_out,2);?></td>
                    </tr>
                    <tr bgcolor="#CCCCCC">
                        <td><b>Total</b></td>
                        <td align="center" colspan="2"><? echo number_format($tot_sewing_in_qty+$tot_sewing_out_qty,2);?></td>
                        <td align="center" colspan="2"><? echo number_format($tot_iron_in_qty+$tot_iron_out_qty,2);?></td>	
                        <td align="right"><? echo number_format($tot_re_iron_qty,2);?></td>	
                        <td align="center"  colspan="2"><? echo number_format($tot_finish_qty_in+$tot_finish_qty_out,2);?></td>	
                        <td align="right"><? echo number_format($tot_carton_qty,2);?></td>	
                        <td align="right"><? echo number_format($tot_finish_qty_in+$tot_finish_qty_out,2);?></td>
                    </tr>
				</table>
			</fieldset>            
           </td></tr><tr><td colspan="2">
            
            <fieldset style="width:2530px;">
                <table width="100%"  cellspacing="0">
                    <tr class="form_caption" style="border:none;">
                    	<td colspan="36" align="center" style="border:none;font-size:14px; font-weight:bold" >
                        	Daily Style Wise Garments Production Status Report
                        </td>
                    </tr>
                    <tr style="border:none;">
                        <td colspan="36" align="center" style="border:none; font-size:16px; font-weight:bold">
                        	Company Name:<? 
							$com_arr=explode(",",str_replace("'","",$cbo_company_name));
							$comName="";
							foreach($com_arr as $comID)
							{
								$comName.=$company_library[$comID].',';
							}
							echo chop($comName,",");
							//echo $company_library[str_replace("'","",$cbo_company_name)]; 
							?> 
                         </td>
                    </tr>
                    <tr style="border:none;">
                        <td colspan="36" align="center" style="border:none;font-size:12px; font-weight:bold">
                        	<? echo "Date: ". str_replace("'","",$txt_date_from) ;?>
                        </td>
                    </tr>
                </table>
                <br />	
                <table class="rpt_table" width="2530" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr >
                            <th width="40" rowspan="2">SL</th>
                            <th colspan="4">Style Description</th>
                            <th colspan="3">Finish Recive/Sewing Output</th>
                            <th colspan="7">Iron & Re-Iron</th>
                            <th colspan="4">Alter Sewing</th>
                            <th colspan="3">Finishing Spot </th>
                            <th colspan="4">Finishing & Packing</th>
                            <th colspan="6">Carton</th>
                            <th colspan="5">EX-Factory</th>
                        </tr>
                        <tr>
                            <th width="100">Buyer</th>
                            <th width="100">Style</th>
                            <th width="140">Colour</th>
                            <th width="60">Order Qty)</th>
                            <th width="60">Today Recive</th>
                            <th width="60">Total Receive</th>
                            <th width="60">Receive Balance</th>
                            <th width="60">Today Iron</th>
                            <th width="60">Total Iron</th>
                            <th width="60">Iron Balance</th>
                            <th width="60">Today Re-Iron</th>
                            <th width="60">Today Re-Iron%</th>
                            <th width="60">Total Re-Iron</th>
                            <th width="60">Total Re-Iron %</th>
                            <th width="60">Today Alter</th>
                            <th width="60">Today Alter%</th>
                            <th width="60">Total Alter</th>
                            <th width="60">Total Alter%</th>
                            <th width="60">Spot</th>
                            <th width="60">Total Spot</th>
                            <th width="60">Total Spot %</th>
                            <th width="60">Today Packing</th>
                            <th width="60">Total Packing</th>
                            <th width="60">Packing Balance</th> 
                            <th width="60">Yet to Packing</th>
                            <th width="60">Pcs Per CTN</th>
                            <th width="60">Today CTN</th>
                            <th width="60">Today pcs of CTN</th>
                            <th width="60">Total CTN</th>
                            <th width="60">Pcs TTL CTN</th>
                            <th width="60">Pcs CTN Balance</th>
                            <th width="80">Last Ship Date</th>
                            <th width="60">Today Ship out Qty.</th>
                            <th width="60">Total Ship out Qty.</th>
                            <th width="100">Shiping Status</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                </table>
                <div style="width:2530px; max-height:425px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="2510" cellpadding="0" cellspacing="0" border="1" rules="all">
                  <? 
				   
				   $i=1;
				   foreach($dataArr as $buyerId=>$buyerRows)
				   {
					     $show= false;
						 foreach($buyerRows as $job_no=>$jobRows)
						 {
							    foreach($jobRows as $color_id=>$order_qty)
								{
					  				 $i++;
					 				  if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					   ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="40"><? echo $i;?></td>
                            <td  width="100"><? echo $buyer_short_library[$buyerId];?></td>
                             <td  width="100"><? echo $style_arr[$job_no];?></td>
                       <?
							$sub_tot_order_qty+=$order_qty;
							$buyer_sub_tot_order_qty+=$order_qty;
							
							$sewing_out_pre=0;
							$sewing_out_qnty=0;
							$iron_pre=0;
							$iron_qnty=0;
							$ex_factory_qty_pre=0;
							$ex_factory_qty=0;
							$ex_factory_date=0;
							$shiping_status=0;
							$finish_qnty=0;
							$finish_pre=0;
							foreach(explode(",",$color_size_table_id_arr[$job_no][$color_id]) as $color_size_table_id){
								
								$sewing_out_pre+=$production_data_arr[$color_size_table_id]['sewing_out_pre'];	
								$sewing_out_qnty+=$production_data_arr[$color_size_table_id]['sewing_out_qnty'];
								$iron_pre+=$production_data_arr[$color_size_table_id]['iron_pre'];	
								$iron_qnty+=$production_data_arr[$color_size_table_id]['iron_qnty'];
								//Note: finish qty = poly tqy........................................
								$finish_pre+=$production_data_arr[$color_size_table_id]['finish_pre'];	
								$finish_qnty+=$production_data_arr[$color_size_table_id]['finish_qnty'];

							
								$ex_factory_qty_pre+=$production_data_arr[$color_size_table_id]['ex_factory_qty_pre'];	
								$ex_factory_qty+=$production_data_arr[$color_size_table_id]['ex_factory_qty'];
								if($ex_factory_date==0){
								$ex_factory_date=$production_data_arr[$color_size_table_id]['ex_factory_date'];	
								}
								if($shiping_status==0){
								$shiping_status=$production_data_arr[$color_size_table_id]['shiping_status'];
								}
							}
							
							$re_iron_qnty=0;$re_iron_qnty_pre=0;$iron_qnty_by_order=0;$iron_qnty_by_order_pre=0;
							$finish_qty_by_order=0;$finish_qty_by_order_pre=0;$carton_qty=0;$carton_qty_pre=0;
							$alter_pre=0; $alter_today=0; $spot_pre=0; $spot_today=0;
							foreach(array_unique(explode(',',$po_id_arr[$job_no])) as $break_down_id){
								$re_iron_qnty+=$production_data_arr[$break_down_id]['re_iron_qnty'];
								$re_iron_qnty_pre+=$production_data_arr[$break_down_id]['re_iron_qnty_pre'];		
								$iron_qnty_by_order+=$production_data_arr[$break_down_id]['iron_qnty_by_order'];
								$iron_qnty_by_order_pre+=$production_data_arr[$break_down_id]['iron_qnty_by_order_pre'];
								$finish_qty_by_order+=$production_data_arr[$break_down_id]['finish_qty_by_order'];
								$finish_qty_by_order_pre+=$production_data_arr[$break_down_id]['finish_qty_by_order_pre'];
								$carton_qty+=$production_data_arr[$break_down_id]['carton_qty'];
								$carton_qty_pre+=$production_data_arr[$break_down_id]['carton_qty_pre'];
							
								$alter_pre+=$production_data_arr[$break_down_id]['alter_pre'];	
								$alter_today+=$production_data_arr[$break_down_id]['alter_today'];
								$spot_pre+=$production_data_arr[$break_down_id]['spot_pre'];	
								$spot_today+=$production_data_arr[$break_down_id]['spot_today'];
							
							}
							 $totalReceive=round(($sewing_out_qnty+$sewing_out_pre));
							 $subTotTotalReceive+=$totalReceive;$buyerTotTotalReceive+=$totalReceive;
							//if($subTotTotalReceive>0)
							
							
					?>
                    
                        <td width="140"><p><? echo $color_arr[$color_id];?></p></td>
                        <td width="60" align="right"><a href="javascript:mypopup('<? echo $job_no.'**'.$color_id.'**'. $color_arr[$color_id].'**'.$style_arr[$job_no];?>','color_wise_order_qty_break_down',600,300,'Detail Veiw');"><? echo $order_qty;?></a></td>
                        <td width="60" align="right">
							<a href="javascript:mypopup('<? echo $job_no.'**'.$color_id.'**'. $color_arr[$color_id].'**'.$style_arr[$job_no].'**'.str_replace("'","",$txt_date_from);?>','today_sewing_out_qty_break_down',600,300,'Today Sewing Out Detail Veiw');">
							<? 
                                echo round($sewing_out_qnty); $subTotTodayRecive+=$sewing_out_qnty;$buyer_subTotTodayRecive+=$sewing_out_qnty;
                            ?>
                            </a>
                        </td>
                        <td width="60" align="right" title="Today Recv+Previous Recv">
                      
							<? 
								
								echo $totalReceive;
								
							?>
                        </td>
                        <td width="60" align="right">
							<? 
                                echo $receiveBalanc=round($order_qty-($sewing_out_qnty+$sewing_out_pre));
                                $subTotReceiveBalance+=$receiveBalanc; $buyer_subTotReceiveBalance+=$receiveBalanc;
                            ?>
                        </td>
                        
                        <td width="60" align="right">
							<? 
								echo $todayIron=round($iron_qnty);
								$subTotTodayIron+=$todayIron;$buyer_subTotTodayIron+=$todayIron;
                            ?>
                        </td>
                        <td width="60" align="right">
						<? 
							echo $totalIron=round($iron_pre+$iron_qnty);
							$subTotTotalIron+=$totalIron;$buyer_subTotTotalIron+=$totalIron;
						?>
                        </td>
                        <td width="60" align="right">
							<? 
								echo $ironBalance=round(($sewing_out_qnty+$sewing_out_pre)-($iron_pre+$iron_qnty));
								$subTotIronBalance+=$ironBalance;$buyer_subTotIronBalance+=$ironBalance;
							?>
                        </td>
                      
                        <td  width="60" align="right">
							<? 
								echo round($re_iron_qnty);
								$subTotTodayReIron+=$re_iron_qnty;$buyer_subTotTodayReIron+=$re_iron_qnty;
							?>
                        </td>
                        <td  width="60" align="right">
							<? 
								$totalReIronPer=($re_iron_qnty*100)/$iron_qnty_by_order;
								$subTotTotalReIronPer+=$totalReIronPer;
								echo number_format($totalReIronPer,2);
							?>
                        </td>
                        <td  width="60" align="right">
							<? 
								echo $totalReIron=round($re_iron_qnty+$re_iron_qnty_pre);
								$subTotTotalReIron+=$totalReIron;	$buyer_subTotTotalReIron+=$totalReIron;
							?>
                        </td>
                        <td  width="60" align="right">
							<? 
								 $totalReIronPer=($re_iron_qnty+$re_iron_qnty_pre)*100/($iron_qnty_by_order+$iron_qnty_by_order_pre);
								$subTotTotalReIronPer+=$totalReIronPer;
								echo number_format($totalReIronPer,2);
							?>
                        </td>
                        <td  width="60" align="right">
							<? 
								echo round($alter_today);
								$subTotTodayAlter+=$alter_today;$buyer_subTotTodayAlter+=$alter_today;
							
							?>
                        </td>
                        <td  width="60" align="right">
							<? 
								$todayAlterPer=($alter_today*100)/$iron_qnty;
								$subTotTodayAlterPer+=$todayAlterPer;
								echo number_format($todayAlterPer,2);
							?>
                        </td>
                        <td  width="60" align="right">
                        
							<? 	
								echo $totalAlter=round(($alter_today+$alter_pre));
								$subTotTotalAlter+=$totalAlter;$buyer_subTotTotalAlter+=$totalAlter;
							?>
                        </td>
                        <td width="60" align="right">
							<? 
								$totalAlterPer=(($alter_today+$alter_pre)*100)/($iron_pre+$iron_qnty);
								$subTotTotalAlterPer=$totalAlterPer;
								echo number_format($totalAlterPer,2);
							?>
                        </td>
                        <td  width="60" align="right">
							<? 
								echo $spot=round($spot_today);
								$sub_totSpot+=$spot;$buyer_sub_totSpot+=$spot;
							?>
                        </td>
                        <td  width="60" align="right">
							<? 
								echo $totalSpot=round(($spot_today+$spot_pre));
								$subTotTotalSpot+=$totalSpot;$buyer_subTotTotalSpot+=$totalSpot;
							?>
                        </td>
                        <td  width="60" align="right">
							<? 
								$totalSpotPer=(($spot_today+$spot_pre)*100)/$iron_qnty;
								$subTotTotalSpotPer+=$totalSpotPer;
								echo number_format($totalSpotPer,2);
							?>
                        </td>
                        <td width="60" align="right">
							<? 
								echo $todayPoly=round($finish_qnty);
								$subTotTodayPoly+=$todayPoly;$buyer_subTotTodayPoly+=$todayPoly;
							?>
                        </td>
                        <td width="60" align="right">
							<? 
								echo $totalPoly=round($finish_qnty+$finish_pre);
								$subTotTotalPoly+=$totalPoly;$buyer_subTotTotalPoly+=$totalPoly;
								
							?>
                        </td>
                        <td width="60" align="right">
							<? 
								echo $polyBalance=round(($sewing_out_qnty+$sewing_out_pre)-($finish_qnty+$finish_pre));
								$subTotPolyBalance+=$polyBalance;$buyer_subTotPolyBalance+=$polyBalance;
							?>
                        </td>
                        
                         <td width="60" align="right">
							<? 
								echo $yet_toBalance=round($order_qty-$totalPoly);
								$subTotyetToBalance+=$yet_toBalance;$buyer_subTotyetToBalance+=$yet_toBalance;
							?>
                        </td>
                     
                        <td width="60" align="right">
							<? 
								echo $pcsPerCTN=round(($finish_qty_by_order+$finish_qty_by_order_pre)/($carton_qty+$carton_qty_pre));
								$subTotPcsPerCTN+=$pcsPerCTN;
							?>
                        </td>

                        <td width="60" align="right">
							<? 
								echo round($carton_qty);
								$subTotTodayCTN+=$carton_qty;$buyer_subTotTodayCTN+=$carton_qty;
							?>
                        </td>
                        <td width="60" align="right">
							<? 
								echo $todaypcsofCTN=round($carton_qty*$pcsPerCTN);
								$subTotTodaypcsofCTN=$todaypcsofCTN;$buyer_subTotTodaypcsofCTN=$todaypcsofCTN;
								
							?>
                        </td>
                        <td width="60" align="right">
							<? 
								echo $totalCTN=round(($carton_qty+$carton_qty_pre));
								$subTotTotalCTN+=$totalCTN;$buyer_subTotTotalCTN+=$totalCTN;
							?>
                        </td>
                        
                        
                        <td width="60" align="right" >
							<? 
								echo $pcsTTLCTN=round(($finish_qty_by_order+$finish_qty_by_order_pre));
								$subTotPcsTTLCTN+=$pcsTTLCTN;$buyer_subTotPcsTTLCTN+=$pcsTTLCTN;
							?>
                        </td>
                        <td width="60" align="right" >
							<? 
								echo $pcsCTNBalance=round(($totalPoly-$pcsTTLCTN));
								$subTotPcsCTNBalance+=$pcsCTNBalance;$buyer_subTotPcsCTNBalance+=$pcsCTNBalance;
							?>
                        
                        </td>
                      
                        
                        <td width="80" align="center">
							<? 
								echo change_date_format($ex_factory_date);
							?>
                        </td>
                        <td width="60" align="right">
							<? 
								echo round($ex_factory_qty);
								$subTotTodayShipoutQty+=$ex_factory_qty;$buyer_subTotTodayShipoutQty+=$ex_factory_qty;
							?>
                            </td>
                        <td width="60" align="right">
							<? 
								echo $totalShipoutQty=round(($ex_factory_qty_pre+$ex_factory_qty));
								$subTotTotalShipoutQty+=$totalShipoutQty;$buyer_subTotTotalShipoutQty+=$totalShipoutQty;
							?>
                        </td>
                        <td width="100" align="center">
							<? 
								echo $shipment_status[$shiping_status];
							?>
                            </td>
                        <td align="right">
							<? 
								echo $balance=round(($sewing_out_qnty+$sewing_out_pre)-($ex_factory_qty_pre+$ex_factory_qty));
								$subTotBalance+=$balance;$buyer_subTotBalance+=$balance;
							?>
                            </td>
                    </tr>
                <? 
						}
						
						?>
						<tr style="background:#CCC; font-weight:bold;">
							<td colspan="4" align="right">Style Sub Total</td>
							<td align="right"><p><? echo $sub_tot_order_qty;?></p></td>
							<td align="right"><p><? echo $subTotTodayRecive;?></p></td>
							<td align="right"><p><? echo $subTotTotalReceive;?></p></td>
							<td align="right"><p><? echo $subTotReceiveBalance;?></p></td>
							<td align="right"><p><? echo $subTotTodayIron;?></p></td>
							<td align="right"><p><? echo $subTotTotalIron;?></p></td>
							<td align="right"><p><? echo $subTotIronBalance;?></p></td>
							<td align="right"><p><? echo $subTotTodayReIron;?></p></td>
							<td align="right"><p><? //echo number_format($subTotTotalReIronPer,2);?></p></td>
							<td align="right"><p><? echo $subTotTotalReIron;?></p></td>
							<td align="right"><p><? //echo number_format($subTotTotalReIronPer,2);?></p></td>
							<td align="right"><p><? echo $subTotTodayAlter;?></p></td>
							<td align="right"><p><? //echo number_format($subTotTodayAlterPer,2);?></p></td>
							<td align="right"><p><? echo $subTotTotalAlter;?></p></td>
							<td align="right"><p><? //echo number_format($subTotTotalAlterPer,2);?></p></td>
							<td align="right"><p><? echo $subTotSpot;?></p></td>
							<td align="right"><p><? echo $subTotTotalSpot;?></p></td>
							<td align="right"><p><? //echo number_format($subTotTotalSpotPer,2);?></p></td>
							<td align="right"><p><? echo $subTotTodayPoly;?></p></td>
							<td align="right"><p><? echo $subTotTotalPoly;?></p></td>
							<td align="right"><p><? echo $subTotPolyBalance;?></p></td>
                            <td align="right"><p><? echo $subTotyetToBalance;?></p></td>

							<td align="right"><p><? echo $subTotPcsPerCTN;?></p></td>
							<td align="right"><p><? echo $subTotTodayCTN;?></p></td>
							<td align="right"><p><? echo $subTotTodaypcsofCTN;?></p></td>
							<td align="right"><p><? echo $subTotTotalCTN;?></p></td>
							<td align="right"><p><? echo $subTotPcsTTLCTN;?></p></td>
							<td align="right"><p><? echo $subTotPcsCTNBalance;?></p></td>
							<td></td>
							<td align="right"><p><? echo $subTotTodayShipoutQty;?></p></td>
							<td align="right"><p><? echo $subTotTotalShipoutQty;?></p></td>
							<td></td>
							<td align="right"><p><? echo $subTotBalance;?></p></td>
						</tr>
						<?
						$grn_tot_order_qty+=$sub_tot_order_qty;
						$grnTotTodayRecive+=$subTotTodayRecive;
						$grnTotTotalReceive+=$subTotTotalReceive;
						$grnTotReceiveBalance+=$subTotReceiveBalance;
						$grnTotTodayIron+=$subTotTodayIron;
						$grnTotTotalIron+=$subTotTotalIron;
						$grnTotIronBalance+=$subTotIronBalance;
						$grnTotTodayReIron+=$subTotTodayReIron;
						$grnTotTodayReIron+=$subTotTodayReIron;
						$grnTotTotalReIron+=$subTotTotalReIron;
						$grnTotTotalReIronPer+=$subTotTotalReIronPer;
						$grnTotTodayAlter+=$subTotTodayAlter;
						$grnTotTodayAlterPer+=$subTotTodayAlterPer;
						$grnTotTotalAlter+=$subTotTotalAlter;
						$grnTotTotalAlterPer+=$subTotTotalAlterPer;
						$grnTotSpot+=$subTotSpot;
						$grnTotTotalSpot+=$subTotTotalSpot;
						$grnTotTotalSpotPer+=$subTotTotalSpotPer;
						$grnTotTodayPoly+=$subTotTodayPoly;
						$grnTotTotalPoly+=$subTotTotalPoly;
						$grnTotTotalpack_yetTo+=$subTotyetToBalance;
						$grnTotPolyBalance+=$subTotPolyBalance;
						$grnTotPcsPerCTN+=$subTotPcsPerCTN;
						$grnTotTodayCTN+=$subTotTodayCTN;
						$grnTotTodaypcsofCTN+=$subTotTodaypcsofCTN;
						$grnTotTotalCTN+=$subTotTotalCTN;
						$grnTotPcsTTLCTN+=$subTotPcsTTLCTN;
						$grnTotPcsCTNBalance+=$subTotPcsCTNBalance;
						$grnTotTodayShipoutQty+=$subTotTodayShipoutQty;
						$grnTotTotalShipoutQty+=$subTotTotalShipoutQty;
						$grnTotBalance+=$subTotBalance;
						
						
						$sub_tot_order_qty=0;
						$subTotTodayRecive=0;
						$subTotTotalReceive=0;
						$subTotReceiveBalance=0;
						$subTotTodayIron=0;
						$subTotTotalIron=0;
						$subTotIronBalance=0;
						$subTotTodayReIron=0;
						$subTotTodayReIron=0;
						$subTotTotalReIron=0;
						$subTotTotalReIronPer=0;
						$subTotTodayAlter=0;
						$subTotTodayAlterPer=0;
						$subTotTotalAlter=0;
						$subTotTotalAlterPer=0;
						$subTotSpot=0;
						$subTotTotalSpot=0;
						$subTotTotalSpotPer=0;
						$subTotTodayPoly=0;
						$subTotyetToBalance=0;
						$subTotTotalPoly=0;
						$subTotPolyBalance=0;
						$subTotPcsPerCTN=0;
						$subTotTodayCTN=0;
						$subTotTodaypcsofCTN=0;
						$subTotTotalCTN=0;
						$subTotPcsTTLCTN=0;
						$subTotPcsCTNBalance=0;
						$subTotTodayShipoutQty=0;
						$subTotTotalShipoutQty=0;
						$subTotBalance=0;
					}
					$i++; 
					?>
					<tr style="background:#CCC; font-weight:bold;">
							<td colspan="4" align="left">Buyer Sub Total</td>
							<td align="right"><p><? echo $buyer_sub_tot_order_qty;$buyer_sub_tot_order_qty=0;?></p></td>
							<td align="right"><p><? echo $buyer_subTotTodayRecive;$buyer_subTotTodayRecive=0;?></p></td>
							<td align="right"><p><? echo $buyerTotTotalReceive;$buyerTotTotalReceive=0;?></p></td>
							<td align="right"><p><? echo $buyer_subTotReceiveBalance;$buyer_subTotReceiveBalance=0;?></p></td>
							<td align="right"><p><? echo $buyer_subTotTodayIron;$buyer_subTotTodayIron=0;?></p></td>
							<td align="right"><p><? echo $buyer_subTotTotalIron;$buyer_subTotTotalIron=0;?></p></td>
							<td align="right"><p><? echo $buyer_subTotIronBalance;$buyer_subTotIronBalance=0;?></p></td>
							<td align="right"><p><? echo $buyer_subTotTodayReIron; $buyer_subTotTodayReIron=0;?></p></td>
							<td align="right"><p><? //echo number_format($subTotTotalReIronPer,2);?></p></td>
							<td align="right"><p><? echo $buyer_subTotTotalReIron;$buyer_subTotTotalReIron=0;?></p></td>
							<td align="right"><p><? //echo number_format($subTotTotalReIronPer,2);?></p></td>
							<td align="right"><p><? echo $buyer_subTotTodayAlter;$buyer_subTotTodayAlter=0;?></p></td>
							<td align="right"><p><? //echo number_format($subTotTodayAlterPer,2);?></p></td>
							<td align="right"><p><? echo $buyer_subTotTotalAlter;$buyer_subTotTotalAlter=0;?></p></td>
							<td align="right"><p><? //echo number_format($subTotTotalAlterPer,2);?></p></td>
							<td align="right"><p><? echo $buyer_sub_totSpot;$buyer_sub_totSpot=0;?></p></td>
							<td align="right"><p><? echo $buyer_subTotTotalSpot;$buyer_subTotTotalSpot=0;?></p></td>
							<td align="right"><p><? //echo number_format($subTotTotalSpotPer,2);?></p></td>
							<td align="right"><p><? echo $buyer_subTotTodayPoly;$buyer_subTotTodayPoly=0;?></p></td>
							<td align="right"><p><? echo $buyer_subTotTotalPoly;$buyer_subTotTotalPoly=0;?></p></td>
							<td align="right"><p><? echo $buyer_subTotPolyBalance;$buyer_subTotPolyBalance=0;?></p></td>
                            <td align="right"><p><? echo $buyer_subTotyetToBalance; $$buyer_subTotyetToBalance=0;?></p></td>
							<td align="right"><p><? echo $buyer_subTotTodayCTN; $buyer_subTotTodayCTN=0;?></p></td>
							<td align="right"><p><? echo $buyer_subTotTodayCTN;$buyer_subTotTodayCTN=0;?></p></td>
							<td align="right"><p><?  echo $buyer_subTotTodaypcsofCTN;$buyer_subTotTodaypcsofCTN=0;?></p></td>
							<td align="right"><p><? echo $buyer_subTotTotalCTN;$buyer_subTotTotalCTN=0;?></p></td>
							<td align="right"><p><? echo $buyer_subTotPcsTTLCTN;$buyer_subTotPcsTTLCTN=0;?></p></td>
							<td align="right"><p><? echo $buyer_subTotPcsCTNBalance;$buyer_subTotPcsCTNBalance=0;?></p></td>
							<td></td>
							<td align="right"><p><? echo $buyer_subTotTodayShipoutQty;$buyer_subTotTodayShipoutQty=0;?></p></td>
							<td align="right"><p><? echo $buyer_subTotTotalShipoutQty;$buyer_subTotTotalShipoutQty=0;?></p></td>
							<td></td>
							<td align="right"><p><? echo $buyer_subTotBalance;$buyer_subTotBalance=0;?></p></td>
						</tr>
                        <?
				} 
				
				?>    
            </table>
		</div>
        
        <div style="width:2530px; max-height:425px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="2510" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tfoot>
                    <th colspan="4" align="center">Grand Total</th>
                    <th align="right" width="60"><p><? echo $grn_tot_order_qty;?></p></th>
                    <th align="right" width="60"><p><? echo $grnTotTodayRecive;?></p></th>
                    <th align="right" width="60"><p><? echo $grnTotTotalReceive;?></p></th>
                    <th align="right" width="60"><p><? echo $grnTotReceiveBalance;?></p></th>
                    <th align="right" width="60"><p><? echo $grnTotTodayIron;?></p></th>
                    <th align="right" width="60"><p><? echo $grnTotTotalIron;?></p></th>
                    <th align="right" width="60"><p><? echo $grnTotIronBalance;?></p></th>
                    <th align="right" width="60"><p><? echo $grnTotTodayReIron;?></p></th>
                    <th align="right" width="60"><p><? //echo number_format($grnTotTotalReIronPer,2);?></p></th>
                    <th align="right" width="60"><p><? echo $grnTotTotalReIron;?></p></th>
                    <th align="right" width="60"><p><? //echo number_format($grnTotTotalReIronPer,2);?></p></th>
                    <th align="right" width="60"><p><? echo $grnTotTodayAlter;?></p></th>
                    <th align="right" width="60"><p><? //echo number_format($grnTotTodayAlterPer,2);?></p></th>
                    <th align="right" width="60"><p><? echo $grnTotTotalAlter;?></p></th>
                    <th align="right" width="60"><p><? //echo number_format($grnTotTotalAlterPer,2);?></p></th>
                    <th align="right" width="60"><p><? echo $grnTotSpot;?></p></th>
                    <th align="right" width="60"><p><? echo $grnTotTotalSpot;?></p></th>
                    <th align="right" width="60"><p><? //echo number_format($grnTotTotalSpotPer,2);?></p></th>
                    <th align="right" width="60"><p><? echo $grnTotTodayPoly;?></p></th>
                    <th align="right" width="60"><p><? echo $grnTotTotalPoly;?></p></th>
                    <th align="right" width="60"><p><? echo $grnTotPolyBalance;?></p></th>
                      <th align="right" width="60"><p><? echo $grnTotTotalpack_yetTo;?></p></th>
                    <th align="right" width="60"><p><? echo $grnTotPcsPerCTN;?></p></th>
                    <th align="right" width="60"><p><? echo $grnTotTodayCTN;?></p></th>
                    <th align="right" width="60"><p><? echo $grnTotTodaypcsofCTN;?></p></th>
                    <th align="right" width="60"><p><? echo $grnTotTotalCTN;?></p></th>
                    <th align="right" width="60"><p><? echo $grnTotPcsTTLCTN;?></p></th>
                    <th align="right" width="60"><p><? echo $grnTotPcsCTNBalance;?></p></th>
                    <th width="80"></th>
                    <th align="right" width="60"><p><? echo $grnTotTodayShipoutQty;?></p></th>
                    <th align="right" width="60"><p><? echo $grnTotTotalShipoutQty;?></p></th>
                    <th width="100"></th>
                    <th align="right" width="113"><p><? echo $grnTotBalance;?></p></th>
                </tfoot>
        	</table>	
		</div>
		</fieldset>
        
        
       </td> </tr></table>
		<?	
		}
	}
	else if(str_replace("'","",$report_type)==7)
	{
		//Description: This report will generate only for one job_no.
		//$_POST variables: cbo_company_name, cbo_working_company_name, cbo_buyer_name, txt_file_no, txt_job_no, txt_style_ref, txt_ref_no, txt_po_no, txt_date_from, cbo_search_by, cbo_year, cbo_ship_status, cbo_location_name, cbo_floor_name, report_title, report_type
		
		$companyId = str_replace("'","",$cbo_company_name);
		$buyerId 	= str_replace("'","",$cbo_buyer_name);
		$jobNoPrefix = str_replace("'","",$txt_job_no);
		$styleRef = str_replace("'","",$txt_style_ref);
		$grouping = str_replace("'","",$txt_ref_no);
		$poNumber = str_replace("'","",$txt_po_no);
		$jobYear = str_replace("'","",$cbo_year); 
		$location = str_replace("'","",$cbo_location_name);
		$filter = "";
		$companyId !='' 			? 	$filter	.= " A.COMPANY_NAME IN ($companyId) ": '';
		//$cbo_working_company_name 	? 	$filter	.= " AND A.COMPANY_ID=$cbo_company_name ": '';
		$buyerId !=0					? 	$filter .= " AND A.BUYER_NAME=$buyerId ": '';
		//$txt_file_no 				? 	$filter .= " AND A.COMPANY_ID=$cbo_company_name ": '';
		$jobNoPrefix !='' 			? 	$filter .= " AND A.JOB_NO_PREFIX_NUM=$jobNoPrefix ": '';
		$styleRef !=''					? 	$filter .= " AND A.STYLE_REF_NO=$styleRef ": '';
		$grouping !=''				? 	$filter .= " AND B.GROUPING=$grouping ": '';
		$poNumber !=''		    	? 	$filter .= " AND B.PO_NUMBER=$poNumber ": '';
		$jobYear !=0	 			? 	$filter .= " AND TO_CHAR(A.INSERT_DATE, 'YYYY')=$jobYear ": '';
		//$cbo_ship_status 			? 	$filter .= " AND A.COMPANY_ID=$cbo_company_name ": '';
		$location !=0			? 	$filter .= " AND A.LOCATION_NAME=$location ": '';
		//$cbo_floor_name 			? 	$filter .= " AND A.COMPANY_ID=$cbo_floor_name ": '';
		//$report_type 				? 	$filter .= " AND A.COMPANY_ID=$cbo_company_name ": '';

		if(($jobYear == 0) || $jobNoPrefix =''){
			echo "<h3 style='color: red; text-align: center;margin-top:30px;'>Job No. and Year is mendatory for this report</h3>"; exit();
		}
		if(!empty($txt_date_from)){
			$pDateCondMain = " AND F.PRODUCTION_DATE=$txt_date_from";
			$pDateCondRMG = " AND B.PR_DATE=$txt_date_from";
		}
		
		//echo $filter; exit();
		//$color_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name");
		
		$teamLeaderArray = return_library_array("select id, team_leader_name from lib_marketing_team where project_type=1", "id", "team_leader_name");
		$dealingMarchantArray = return_library_array("select id, team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
		$itemArray = return_library_array("select id, item_name from lib_garment_item", "id", "item_name");
		
		$styleSql = "SELECT A.ID AS JOB_ID, B.ID AS PO_ID, A.COMPANY_NAME, A.BUYER_NAME, A.JOB_NO, A.STYLE_REF_NO, A.DEALING_MARCHANT, B.PO_NUMBER, C.ITEM_NUMBER_ID, C.COLOR_NUMBER_ID, C.SIZE_NUMBER_ID, C.ORDER_QUANTITY, C.PLAN_CUT_QNTY, B.SHIPMENT_DATE, A.TEAM_LEADER, B.SHIPING_STATUS, C.COUNTRY_ID,
		A.AVG_UNIT_PRICE AS FOB, A.TOTAL_PRICE AS FOB_VALUE, D.SEW_SMV AS MARKETING_SMV, D.SEW_EFFI_PERCENT AS MARKETING_EFFICIENCY, E.TOTAL_SMV AS PRODUCTION_SMV, NULL AS PRODUCTION_EFFICEINCY, G.PRODUCTION_TYPE, G.PRODUCTION_QNTY, F.EMBEL_NAME, H.PRODUCTION_QNTY AS EX_FACTOTY_QTY, I.GREY_FAB_QNTY, I.BOOKING_NO, I.BOOKING_MST_ID AS BOOKING_ID, J.QUANTITY AS YARN_BOOKING_QTY
			 	
						FROM 
							WO_PO_DETAILS_MASTER A
							JOIN WO_PO_BREAK_DOWN B ON A.ID = B.JOB_ID 
							JOIN WO_PO_COLOR_SIZE_BREAKDOWN C 
								ON A.ID = C.JOB_ID and B.ID = C.PO_BREAK_DOWN_ID
							LEFT JOIN WO_PRE_COST_MST D ON A.ID = D.JOB_ID AND D.STATUS_ACTIVE = 1 AND D.IS_DELETED = 0
							LEFT JOIN PPL_GSD_ENTRY_MST E ON A.ID = E.JOB_ID AND E.STATUS_ACTIVE = 1 AND E.IS_DELETED = 0
							LEFT JOIN PRO_GARMENTS_PRODUCTION_MST F ON B.ID = F.PO_BREAK_DOWN_ID AND F.STATUS_ACTIVE = 1 AND F.IS_DELETED = 0
							LEFT JOIN PRO_GARMENTS_PRODUCTION_DTLS G 
								ON F.ID = G.MST_ID AND G.COLOR_SIZE_BREAK_DOWN_ID = C.ID AND G.STATUS_ACTIVE = 1 AND G.IS_DELETED = 0
							LEFT JOIN PRO_EX_FACTORY_DTLS H ON H.COLOR_SIZE_BREAK_DOWN_ID = C.ID AND H.STATUS_ACTIVE = 1 AND H.IS_DELETED = 0 
							LEFT JOIN WO_BOOKING_DTLS I ON B.ID = I.PO_BREAK_DOWN_ID AND C.COLOR_NUMBER_ID=I.GMTS_COLOR_ID AND BOOKING_TYPE=1 AND I.STATUS_ACTIVE = 1 AND I.IS_DELETED = 0 AND I.GREY_FAB_QNTY IS NOT NULL
							LEFT JOIN INV_PURCHASE_REQUISITION_DTLS J ON J.JOB_ID=A.ID AND J.STATUS_ACTIVE = 1 AND J.IS_DELETED = 0
						WHERE
							$filter 
							AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0
							AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0
							AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0
							";
		//echo $styleSql; exit();
		$styleData = sql_select($styleSql);
		$styleWise = array();
		$colorItemWise = array();
		$attributes=array('COMPANY_NAME','BUYER_NAME', 'JOB_NO', 'STYLE_REF_NO','PO_NUMBER','ITEM_NUMBER_ID', 'SHIPMENT_DATE', 'TEAM_LEADER', 'DEALING_MARCHANT',  'SHIPING_STATUS', 'COUNTRY_ID', 'COLOR_NUMBER_ID','YARN_BOOKING_QTY', 'BOOKING_NO');
		foreach($styleData as $data){

			foreach($attributes as $attr){
				$styleWise[$data['JOB_ID']][$data['PO_ID']][$data['ITEM_NUMBER_ID']][$attr]		= $data[$attr];
				$colorItemWise[$data['JOB_ID']][$data['PO_ID']][$data['ITEM_NUMBER_ID']][$data['COLOR_NUMBER_ID']][$attr]		= $data[$attr];
			}
		
			$poItemQtyArray[$data['JOB_ID']][$data['PO_ID']][$data['ITEM_NUMBER_ID']][$data['COLOR_NUMBER_ID']][$data['SIZE_NUMBER_ID']][$data['COUNTRY_ID']]['ORDER_QUANTITY']		= $data['ORDER_QUANTITY'];
			$poItemQtyArray[$data['JOB_ID']][$data['PO_ID']][$data['ITEM_NUMBER_ID']][$data['COLOR_NUMBER_ID']][$data['SIZE_NUMBER_ID']][$data['COUNTRY_ID']]['PLAN_CUT_QNTY']		= $data['PLAN_CUT_QNTY'];
			$poItemQtyArray[$data['JOB_ID']][$data['PO_ID']][$data['ITEM_NUMBER_ID']][$data['COLOR_NUMBER_ID']][$data['SIZE_NUMBER_ID']][$data['COUNTRY_ID']]['EX_FACTOTY_QTY']		= $data['EX_FACTOTY_QTY'];
			$poItemQtyArray[$data['JOB_ID']][$data['PO_ID']][$data['ITEM_NUMBER_ID']][$data['COLOR_NUMBER_ID']][$data['SIZE_NUMBER_ID']][$data['COUNTRY_ID']]['GREY_FAB_QNTY']		= $data['GREY_FAB_QNTY'];


			//Summery Information immidiate summery.
			$styleSummery[$data['JOB_ID']]['FOB']						= $data['FOB'];
			$styleSummery[$data['JOB_ID']]['FOB_VALUE']					= $data['FOB_VALUE'];
			$styleSummery[$data['JOB_ID']]['MARKETING_SMV']				= $data['MARKETING_SMV'];
			$styleSummery[$data['JOB_ID']]['MARKETING_EFFICIENCY']		= $data['MARKETING_EFFICIENCY'];
			$styleSummery[$data['JOB_ID']]['PRODUCTION_SMV']			= $data['PRODUCTION_SMV'];
			$styleSummery[$data['JOB_ID']]['PRODUCTION_EFFICEINCY']		= $data['PRODUCTION_EFFICEINCY'];
			$emblelArray[$data['JOB_ID']][$data['PRODUCTION_TYPE']][$data['EMBEL_NAME']]['PRODUCTION_QNTY']		+= $data['PRODUCTION_QNTY'];
			
			//Extra.
			$jobStyleArray[$data['JOB_ID']] 				= $data['STYLE_REF_NO'];
			$bookingIdArray[$data['BOOKING_ID']] 			= $data['BOOKING_ID'];
			$bookingArray[$data['BOOKING_NO']] 				= $data['BOOKING_NO'];
			$poCount[$data['JOB_ID']][$data['PO_ID']]		= $data['PO_ID'] ;
			$poIdArray[$data['PO_ID']]						= $data['PO_ID'] ;
			$jobBookingArray[$data['JOB_ID']]				= $data['BOOKING_NO'] ;
			$jobArray[$data['JOB_ID']]						= $data['JOB_ID'];
		}
		unset($styleData);
		$jobIds = "'".implode("','", $jobArray)."'";
		$commaBooking = "'".implode("','", $bookingArray)."'";

		$yarnBookingSQl = "SELECT SUM(A.QUANTITY) AS YARN_BOOKING_QTY, A.JOB_ID FROM INV_PURCHASE_REQUISITION_DTLS A WHERE JOB_ID IN ($jobIds) AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 GROUP BY A.JOB_ID";
		$yarnBookingArray = array();
		foreach(sql_select($yarnBookingSQl) as $yarn){
			$yarnBookingArray[$yarn['JOB_ID']] = $yarn['YARN_BOOKING_QTY'];
		}

		$allocateData = sql_select("SELECT SUM(QNTY) AS ALLOCATE_QUANTITY FROM INV_MATERIAL_ALLOCATION_DTLS WHERE BOOKING_NO IN ($commaBooking) AND STATUS_ACTIVE=1 AND IS_DELETED=0");
		$allocateYarn = 0;
		foreach($allocateData as $allocate){
			$allocateYarn = $allocate['ALLOCATE_QUANTITY'];
		}

		$jobSmvArray = array();
		$smvSQL = "SELECT A.JOB_ID, SUM(A.TOTAL_SMV) AS PRODUCTION_SMV
					FROM PPL_GSD_ENTRY_MST A 
						WHERE JOB_ID IN ($jobIds) AND A.STATUS_ACTIVE=1 AND IS_DELETED=0
						GROUP BY A.JOB_ID";
		foreach(sql_select($smvSQL) as $smv){
			$jobSmvArray[$smv['JOB_ID']] = $smv['PRODUCTION_SMV'];
		}

		$poIDS = implode(",", $poIdArray); 
		$condition= new condition();     
		$condition->po_id_in($poIDS);     
		$condition->init();
		$fabric= new fabric($condition);
		$fabric_costing_arr = $fabric->getQtyArray_by_orderGmtsitemAndGmtscolor_knitAndwoven_greyAndfinish();
		//echo "<pre>"; print_r($fabric_costing_arr); exit();
		$fabricSQL = "SELECT A.GREY_FAB_QNTY, A.PO_BREAK_DOWN_ID, B.JOB_ID, B.ITEM_NUMBER_ID, A.GMTS_COLOR_ID
						FROM WO_BOOKING_DTLS A,
							WO_PRE_COST_FABRIC_COST_DTLS B
						WHERE A.PRE_COST_FABRIC_COST_DTLS_ID=B.ID
							AND A.PO_BREAK_DOWN_ID IN ($poIDS)
							AND BOOKING_TYPE=1
							AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0
							AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0
							";
		//echo $fabricSQL; exit();
		$fabricArray = array();
		foreach(sql_select($fabricSQL) as $data){
			$fabricArray[$data['JOB_ID']][$data['PO_BREAK_DOWN_ID']][$data['ITEM_NUMBER_ID']][$data['GMTS_COLOR_ID']]['GREY_FAB_QNTY'] += $data['GREY_FAB_QNTY'];
			//$total_wo_qty+=$data['GREY_FAB_QNTY'];
		}
		//echo $total_wo_qty; die;
		//echo "<pre>"; print_r($fabricArray); exit();

		$itemWiseQuantityArray = array();
		foreach($poItemQtyArray as $job_id => $poWise){
			foreach($poWise as $po_id => $itemWise){
				foreach($itemWise as $item_id => $colorWise){
					foreach($colorWise as $color_id => $sizeWise){
						foreach($sizeWise as $size_id => $sizeData){
							foreach($sizeData as $country_id => $countryWise){
								$itemWiseQuantityArray[$job_id][$po_id][$item_id]['ORDER_QUANTITY'] 	+= $countryWise['ORDER_QUANTITY'];
								$itemWiseQuantityArray[$job_id][$po_id][$item_id]['PLAN_CUT_QNTY'] 		+= $countryWise['PLAN_CUT_QNTY'];
								$itemWiseQuantityArray[$job_id]['EX_FACTOTY_QTY'] 		+= $countryWise['EX_FACTOTY_QTY'];
								$itemWiseQuantityArray[$job_id][$po_id][$item_id][$color_id]['ORDER_QUANTITY'] 	+= $countryWise['ORDER_QUANTITY'];
								$itemWiseQuantityArray[$job_id][$po_id][$item_id][$color_id]['GREY_FAB_QNTY'] 	+= $countryWise['GREY_FAB_QNTY'];
								
							}
						}
					}
				}
			}
		}
		/// print_r($bookingIdArray); exit();
		$fsoProdSQL =   "SELECT D.JOB_NO,D.SALES_BOOKING_NO,D.STYLE_REF_NO,D.COMPANY_ID,D.BOOKING_ID,E.PROD_ID,E.PO_BREAKDOWN_ID,H.QNTY,
							G.COLOR_ID, G.MST_ID, D.ID AS SALES_ORDER_PO, H.ENTRY_FORM
						FROM FABRIC_SALES_ORDER_MST D
							INNER JOIN ORDER_WISE_PRO_DETAILS E ON D.ID = E.PO_BREAKDOWN_ID
							INNER JOIN PRO_GREY_PROD_ENTRY_DTLS G ON E.DTLS_ID = G.ID
							INNER JOIN PRO_ROLL_DETAILS H ON G.ID = H.DTLS_ID
						WHERE     E.STATUS_ACTIVE = 1
							AND E.IS_DELETED = 0
							AND E.ENTRY_FORM IN (2,66)
							AND D.COMPANY_ID IN ($companyId)
							AND H.ENTRY_FORM IN (2,66)
							AND H.IS_SALES = 1
							AND D.BOOKING_ID IN (".rtrim(implode(",", $bookingIdArray),',').")";
		//echo $fsoProdSQL; exit();
		$receiveMstIdArray = array();
		$styleProArray = array();
		$salesOrderPoArray = array();
		$fsoResult = sql_select($fsoProdSQL);
		foreach($fsoResult as $data){
			$styleProArray[$data['ENTRY_FORM']][$data['STYLE_REF_NO']] 	+= $data['QNTY'];
			$receiveMstIdArray[$data['MST_ID']] 	= $data['MST_ID'];
			$salesOrderPoArray[$data['SALES_ORDER_PO']] = $data['SALES_ORDER_PO'];
		}
		unset($fsoResult);
		//echo implode(",", $receiveMstIdArray); exit();
		//Knitting QC. STart.
		$knittingQcSQL =   "SELECT D.STYLE_REF_NO,H.ROLL_WEIGHT
						FROM FABRIC_SALES_ORDER_MST D
							INNER JOIN ORDER_WISE_PRO_DETAILS E ON D.ID = E.PO_BREAKDOWN_ID
							INNER JOIN PRO_GREY_PROD_ENTRY_DTLS G ON E.DTLS_ID = G.ID
							INNER JOIN PRO_QC_RESULT_MST H ON G.ID = H.PRO_DTLS_ID
						WHERE     E.STATUS_ACTIVE = 1
							AND E.IS_DELETED = 0
							AND E.ENTRY_FORM IN (2)
							AND D.COMPANY_ID IN ($companyId)
							AND D.BOOKING_ID IN (".rtrim(implode(",", $bookingIdArray),',').")";
		//echo $fsoProdSQL; exit();
		$knittingQcResult = sql_select($knittingQcSQL);
		$styleProQcArray = array();
		foreach($knittingQcResult as $data){
			$styleProQcArray[$data['STYLE_REF_NO']] 	+= $data['ROLL_WEIGHT'];
		}
		unset($fsoResult);
		//Knitting QC.

		$yarnIssSQL =   "SELECT D.JOB_NO,D.SALES_BOOKING_NO,D.STYLE_REF_NO,D.COMPANY_ID,D.BOOKING_ID,E.PROD_ID,E.PO_BREAKDOWN_ID, D.ID AS SALES_ORDER_PO, E.ENTRY_FORM, E.QUANTITY
						FROM FABRIC_SALES_ORDER_MST D
							INNER JOIN ORDER_WISE_PRO_DETAILS E ON D.ID = E.PO_BREAKDOWN_ID
						WHERE     E.STATUS_ACTIVE = 1
							AND E.IS_DELETED = 0
							AND E.ENTRY_FORM IN (3)
							AND D.COMPANY_ID IN ($companyId)
							AND D.BOOKING_ID IN (".rtrim(implode(",", $bookingIdArray),',').")";
		//echo $yarnIssSQL; exit();
		$issueArray = array();
		$issueResult = sql_select($yarnIssSQL);
		foreach($issueResult as $data){
			$issueArray[$data['STYLE_REF_NO']] 	+= $data['QUANTITY'];
		}
		unset($issueResult);

		$deliverySQL = "SELECT A.CURRENT_DELIVERY, B.ENTRY_FORM, B.QNTY
							FROM 
								PRO_GREY_PROD_DELIVERY_DTLS A,
								PRO_ROLL_DETAILS B
						 WHERE 
						 	A.ID=B.DTLS_ID
							AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0
							AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0
							AND B.ENTRY_FORM IN (56)
						 	AND A.GREY_SYS_ID IN (".implode(",", $receiveMstIdArray).") ";
		//echo $deliverySQL; exit();
		$deliveryQtyArray = array();
		$deliveryResult = sql_select($deliverySQL);
		foreach($deliveryResult as $data){
			$deliveryQtyArray[$data['ENTRY_FORM']]['KNITTING_DELIVERY'] += $data['QNTY'];
			$deliveryQtyArray[$data['ENTRY_FORM']]['DYEING_DELIVERY'] 	+= $data['QNTY'];
			$deliveryQtyArray[$data['ENTRY_FORM']]['FABRIC_RECEIVED'] 	+= $data['QNTY'];
		}
		unset($deliveryResult);
		//echo $deliverySQL; exit();

		$rowspan_job=array();
		foreach($colorItemWise as $job_id => $jobWise){
			foreach($jobWise as $poWise){
				foreach($poWise as $itemWise){
					foreach($itemWise as $colorWise){
						$rowspan_job[$job_id]++;
					}
				}
			}
		}

		$styleRowspan = array();
		foreach($styleWise as $job_id => $jobWise){
			foreach($jobWise as $poWise){
				foreach($poWise as $itemWise){
					$styleRowspan[$job_id]++;
				}
			}
		}

		$salesPoIds = implode(",", $salesOrderPoArray); 
		$batchSQL = "SELECT A.ID AS BATCH_ID, A.BOOKING_NO, B.BATCH_QNTY
					
					FROM PRO_BATCH_CREATE_MST 	A,
						PRO_BATCH_CREATE_DTLS 	B
					WHERE 
							A.ID=B.MST_ID AND A.ENTRY_FORM=0 and b.is_sales=1
							AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0
							AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0
							AND A.SALES_ORDER_ID IN (".$salesPoIds.")";
		//echo $batchSQL; exit();
		$bookingQtyArray = array();
		$batchResult = sql_select($batchSQL);
		$batchIdsArray = array();
		foreach($batchResult as $batch){
			//$bookingQtyArray[$batch['BOOKING_NO']]['BATCH_QNTY'] 			+= $batch['BATCH_QNTY'];
			$bookingQtyArray['BATCH_QNTY'] 			+= $batch['BATCH_QNTY'];
			$batchIdsArray[$batch['BATCH_ID']] = $batch['BATCH_ID'];
		}
		unset($batchResult);

		$dyingDeliverySQL = "SELECT A.CURRENT_DELIVERY, B.ENTRY_FORM, B.QNTY
							FROM 
								PRO_GREY_PROD_DELIVERY_DTLS A,
								PRO_ROLL_DETAILS B
						 WHERE 
						 	A.ID=B.DTLS_ID
							AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0
							AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0
							AND B.ENTRY_FORM IN (67)
						 	AND B.PO_BREAKDOWN_ID IN (".$salesPoIds.") ";
		//echo $deliverySQL; exit();
		$dyingDeliveryArray = array();
		$dyingDeliveryResult = sql_select($dyingDeliverySQL);
		foreach($dyingDeliveryResult as $data){
			$dyingDeliveryArray[$data['ENTRY_FORM']]['DYING_DELIVERY'] += $data['QNTY'];
		}
		unset($deliveryResult);
		//echo "<pre>"; print_r($batchSQL); exit();
		$batchIds = implode(",", $batchIdsArray); 
		//echo $batchIds; die;
		$dyeingSQL = "SELECT  D.PRODUCTION_QTY AS DYEING_PROD_QTY, C.ENTRY_FORM
					
					FROM 
						PRO_FAB_SUBPROCESS		C,
						PRO_FAB_SUBPROCESS_DTLS D
					WHERE 
							C.ID=D.MST_ID AND C.ENTRY_FORM IN(35, 66) AND C.LOAD_UNLOAD_ID=2 AND D.LOAD_UNLOAD_ID=2
							AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0
							AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0
							AND C.BATCH_ID IN (".$batchIds.")";
		//echo $dyeingSQL; exit();
		$dyingQtyArray = array();
		$dyingResult = sql_select($dyeingSQL);
		foreach($dyingResult as $dying){
			$dyingQtyArray[$dying['ENTRY_FORM']]['DYEING_PROD_QTY'] 		+= $dying['DYEING_PROD_QTY'];
		}

		//REceive data

		$receiveSQL = "SELECT A.ENTRY_FORM, C.RECEIVE_QNTY
					
					FROM 
						INV_RECEIVE_MASTER		A,
						INV_TRANSACTION B,
						PRO_FINISH_FABRIC_RCV_DTLS C
					WHERE 
							A.ID=B.MST_ID AND A.ID=C.MST_ID AND B.ID=C.TRANS_ID
							AND A.ENTRY_FORM=68
							AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0
							AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0
							AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0
							AND C.BATCH_ID IN (".$batchIds.")";
		//echo $receiveSQL; exit();
		$receiveQtyArray = array();
		$receiveResult = sql_select($receiveSQL);
		foreach($receiveResult as $receive){
			$receiveQtyArray[$receive['ENTRY_FORM']]['RECEIVE_QNTY'] 		+= $receive['RECEIVE_QNTY'];
		}
		//Receive data end

		$summery2 = "SELECT A.ID AS JOB_ID, A.JOB_NO, B.ID AS PO_ID, B.PO_NUMBER, A.STYLE_REF_NO, A.COMPANY_NAME, C.ITEM_NUMBER_ID, C.COLOR_NUMBER_ID, C.SIZE_NUMBER_ID, E.PRODUCTION_TYPE, E.PRODUCTION_QNTY, D.SEWING_LINE, D.FLOOR_ID, D.PRODUCTION_DATE
						FROM 
							WO_PO_DETAILS_MASTER A
							JOIN WO_PO_BREAK_DOWN B ON A.ID = B.JOB_ID 
							JOIN WO_PO_COLOR_SIZE_BREAKDOWN C 
								ON A.ID = C.JOB_ID and B.ID = C.PO_BREAK_DOWN_ID
							INNER JOIN PRO_GARMENTS_PRODUCTION_MST D ON B.ID=D.PO_BREAK_DOWN_ID AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0
							INNER JOIN PRO_GARMENTS_PRODUCTION_DTLS E ON D.ID=E.MST_ID AND C.ID=E.COLOR_SIZE_BREAK_DOWN_ID AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0

						WHERE
							$filter
							AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0
							AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0
							AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0
							"; 

		$columnsArray 	= array("JOB_ID", "PO_ID", "PRODUCTION_DATE", "FLOOR_ID", "SEWING_LINE", "STYLE_REF_NO", "ITEM_NUMBER_ID", "JOB_NO", "PO_NUMBER");
		$summery2Result 			= sql_select($summery2);
		$summerProdArray = array();
		foreach($summery2Result as $row){
			$summerProdArray[$row["JOB_ID"]][$row['PRODUCTION_TYPE']]['PRODUCTION_QNTY']		+= $row['PRODUCTION_QNTY'];
		}
		

		$rmgSQL = "SELECT A.ID AS JOB_ID, A.JOB_NO, B.ID AS PO_ID, B.PO_NUMBER, A.STYLE_REF_NO, A.COMPANY_NAME, C.ITEM_NUMBER_ID, C.COLOR_NUMBER_ID, C.SIZE_NUMBER_ID, E.PRODUCTION_TYPE, E.PRODUCTION_QNTY, D.SEWING_LINE, D.FLOOR_ID, D.PRODUCTION_DATE
						FROM 
							WO_PO_DETAILS_MASTER A
							JOIN WO_PO_BREAK_DOWN B ON A.ID = B.JOB_ID 
							JOIN WO_PO_COLOR_SIZE_BREAKDOWN C 
								ON A.ID = C.JOB_ID and B.ID = C.PO_BREAK_DOWN_ID
							INNER JOIN PRO_GARMENTS_PRODUCTION_MST D ON B.ID=D.PO_BREAK_DOWN_ID AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0
							INNER JOIN PRO_GARMENTS_PRODUCTION_DTLS E ON D.ID=E.MST_ID AND C.ID=E.COLOR_SIZE_BREAK_DOWN_ID AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0

						WHERE
							$filter and e.PRODUCTION_TYPE = 5
							AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0
							AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0
							AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0
							"; 
		//echo $rmgSQL; exit();
		$rmgResult 			= sql_select($rmgSQL);
		$rmgDataArray 		= array();
		$prodArray 			= array();
		$sewingLineArray 	= array();
		
		$prodTypeArray = array();
		foreach($rmgResult as $row){
			foreach($columnsArray as $column){
				if($row["PRODUCTION_TYPE"] == 5){
					$rmgDataArray[$row["JOB_ID"]][$row["PO_ID"]][$row["ITEM_NUMBER_ID"]][$row["FLOOR_ID"]][$row["SEWING_LINE"]][$row["PRODUCTION_DATE"]][$column]=$row[$column];
				}
				
			}
			$prodArray[$row["JOB_ID"]][$row["PO_ID"]][$row["ITEM_NUMBER_ID"]][$row["FLOOR_ID"]][$row["SEWING_LINE"]][$row["PRODUCTION_DATE"]][$row["PRODUCTION_TYPE"]]['PRODUCTION_QNTY']+=$row['PRODUCTION_QNTY']; //productionType 5 = sewingOutput.
			$sewingLineArray[$row["SEWING_LINE"]] = $row["SEWING_LINE"];
			$prodTypeArray[$row["JOB_ID"]][$row['PRODUCTION_TYPE']]['PRODUCTION_QNTY']		+= $row['PRODUCTION_QNTY'];
		}

		//echo "<pre>"; print_r($prodTypeArray); exit();
		$sewingLines = implode(",", $sewingLineArray); 
		//echo $sewingLineArray; exit();
		unset($rmgResult);

		$resourceSQL=("select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$companyId and a.id in($sewingLines) and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0");
		//echo $resourceSQL; exit();
		$resourceData = sql_select($resourceSQL);
		foreach($resourceData as $val)
		{
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['man_power']	=$val[csf('man_power')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['operator']		=$val[csf('operator')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['helper']		=$val[csf('helper')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['capacity']		=$val[csf('capacity')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['working_hour']	=$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['total_target']	=$val[csf('target_per_hour')]*$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust']	=$val[csf('smv_adjust')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['line_number']	=$val[csf('line_number')];
		}

		$smvSQL = "SELECT B.TOTAL_SMV, A.JOB_ID, A.GMTS_ITEM_ID
					FROM PPL_GSD_ENTRY_MST A,
						PPL_GSD_ENTRY_DTLS B
					WHERE A.ID=B.MST_ID
						AND A.JOB_ID IN ($jobIds)
						AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0
						AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0
						";
		//echo $smvSQL; exit();
		$jobSMV = array();
		foreach(sql_select($smvSQL) as $data)
		{
			$jobSMV[$data['JOB_ID']][$data['GMTS_ITEM_ID']]  = $data['TOTAL_SMV'];
		}

		ob_start();

		?>
			<fieldset style="width:1230px; margin-top: 30px;">
				<h3 style="text-align: center;">Summary Information</h3>
				<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="100">Company</th>
						<th width="100">Buyer</th>
						<th width="100">Job</th>
						<th width="100">Style</th>
						<th width="100">PO(pcs)</th>
						<th width="100">Item Name</th>
						<th width="100">Order Qty(Pcs)</th>
						<th width="100">Plan cut Qty(pcs)</th>
						<th width="100">Shipment Date</th>
						<th width="100">Teamleader</th>
						<th width="100">Dealing Merchant</th>
						<th width="100">Ship Status</th>
					</thead>
					<tbody>
						<? 
						$i=1;
						
						foreach($styleWise as $job_id => $poWise)
						{
							$k = 0;
							foreach($poWise as $po_id => $itemWise)
							{
								
								foreach($itemWise as $itemId => $row)
								{
									?>
									<tr>
										<? if($k==0){ ?>
											<td width="30" style="vertical-align:middle; text-align:center;"  rowspan="<?=$styleRowspan[$job_id];?>" ><?=$i;?></td>
											<td width="100" style="vertical-align:middle; text-align:center;"  rowspan="<?=$styleRowspan[$job_id];?>" ><?=$company_library[$row['COMPANY_NAME']];?></td>
											<td width="100" style="vertical-align:middle; text-align:center;"  rowspan="<?=$styleRowspan[$job_id];?>" ><?=$buyer_short_library[$row['BUYER_NAME']];?></td>
											<td width="100" style="vertical-align:middle; text-align:center;"  rowspan="<?=$styleRowspan[$job_id];?>" ><?=$row['JOB_NO']?></td>
											<td width="100" style="vertical-align:middle; text-align:center;"  rowspan="<?=$styleRowspan[$job_id];?>" ><?=$row['STYLE_REF_NO']?></td>
										<? } ?>
										<td width="100"><?=$row['PO_NUMBER']?></td>
										<td width="100"><?=$itemArray[$row['ITEM_NUMBER_ID']];?></td>
										<td width="100" style="text-align: right;"><?=$itemWiseQuantityArray[$job_id][$po_id][$itemId]['ORDER_QUANTITY'];?></td>
										<td width="100" style="text-align: right;"><?=$itemWiseQuantityArray[$job_id][$po_id][$itemId]['PLAN_CUT_QNTY'];?></td>
										<? if($k==0){ ?>
											<td width="100" style="vertical-align:middle; text-align:center;"  rowspan="<?=$styleRowspan[$job_id];?>"><?=$row['SHIPMENT_DATE']?></td>
											<td width="100" style="vertical-align:middle; text-align:center;"  rowspan="<?=$styleRowspan[$job_id];?>"><?=$teamLeaderArray[$row['TEAM_LEADER']];?></td>
											<td width="100" style="vertical-align:middle; text-align:center;"  rowspan="<?=$styleRowspan[$job_id];?>"><?=$dealingMarchantArray[$row['DEALING_MARCHANT']];?></td>
										<? } ?>
										<td width="100"><?=$shipment_status[$row['SHIPING_STATUS']];?></td>
									</tr>
									<? 
									$k++;
								}
								
							}
							$i++;
						}
						?>
					</tbody>
				</table>
			</fieldset> <br><br>


			<fieldset style="width:1400px; margin-top: 30px;">
				<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="100">FOB (Pcs)</th>
						<th width="100">FOB Value</th>
						<th width="100">Marketing SMV (Pcs)</th>
						<th width="100">Production SMV (Pcs)</th>
						<th width="100">Marketing Efficiency</th>
						<th width="100">Production Efficiency</th>
						<th width="100">Cutting</th>
						<th width="100">Print Rec.</th>
						<th width="100">Embroidery Rec</th>
						<th width="100">Sewing Output</th>
						<th width="100">Finishing</th>
						<th width="100">Ex-Factory Qty</th>
						<th width="100">Ex-Factory Value</th>
						<th width="100">Profit/Loss</th>
					</thead>
					<tbody>
						<? 
							$i=1;
							foreach($styleSummery as $job_id => $row)
							{
								$exFactValue = $itemWiseQuantityArray[$job_id]['EX_FACTOTY_QTY'] * $row['FOB'];
								$lossProfit = $exFactValue-$row['FOB_VALUE'];
								?>
								<tr>
									<td width="100"  style="text-align: right;"><?=$row['FOB']?></td>
									<td width="100"  style="text-align: right;"><?=$row['FOB_VALUE']?></td>
									<td width="100"  style="text-align: right;"><?=$row['MARKETING_SMV']?></td>
									<td width="100"  style="text-align: right;"><?=$jobSmvArray[$job_id]?></td>
									<td width="100"  style="text-align: right;"><?=$row['MARKETING_EFFICIENCY']?></td>
									<td width="100" style="text-align: right;" class="prodEffecincy"><?=$row['PRODUCTION_EFFICEINCY']?></td>
									<td width="100"  style="text-align: right;"><?=$summerProdArray[$job_id][1]['PRODUCTION_QNTY'];?></td>
									<td width="100"  style="text-align: right;"><?=$emblelArray[$job_id][3][1]['PRODUCTION_QNTY']?></td>
									<td width="100"  style="text-align: right;"><?=$emblelArray[$job_id][2][1]['PRODUCTION_QNTY']?></td>
									<td width="100"  style="text-align: right;"><?=$prodTypeArray[$job_id][5]['PRODUCTION_QNTY']?></td>
									<td width="100"  style="text-align: right;"><?=$summerProdArray[$job_id][8]['PRODUCTION_QNTY']?></td>
									<td width="100"  style="text-align: right;"><?=$itemWiseQuantityArray[$job_id]['EX_FACTOTY_QTY']?></td>
									<td width="100" style="text-align: right;"><?=$exFactValue;?></td>
									<td width="100" style="text-align: right;"><?=$lossProfit;?></td>
								</tr>
								<? 
							}
						?>
					</tbody>
				</table>
			</fieldset> 

			<fieldset style="width:1900px; margin-top: 30px;">
				<h3 style="text-align: center;">Textile details</h3>
				<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="100">Style</th>
						<th width="100">PO</th>
						<th width="100">Color</th>
						<th width="100">Item Name</th>
						<th width="100">Order Qty</th>
						<th width="100">Fabric Required Qty</th>
						<th width="100">Fabric WO</th>
						<th width="100">Yarn Booking</th>
						<th width="100">Yarn Allocate</th>
						<th width="100">Yarn Issue</th>
						<th width="100">Knitting Prod.</th>
						<th width="100">Knitting QC</th>
						<th width="100">Knitting Delivery</th>
						<th width="100">Dyeing Batch Qty</th>
						<th width="100">Dyeing Prod.</th>
						<th width="100">Dyeing QC</th>
						<th width="100">Dyeing Delivery</th>
						<th width="100">Fabric Received</th>
						<th width="100">Fabric Balance</th>
					</thead>
					<tbody>
						<? 
							$i=1;
							
							foreach($colorItemWise as $job_id => $poWise)
							{
								$k = 0;
								foreach($poWise as $po_id => $itemWise)
								{
									foreach($itemWise as $item_id => $colorWise)
									{	
										
										foreach($colorWise as $color_id => $row)
										{
											$exFactValue = $itemWiseQuantityArray[$job_id]['EX_FACTOTY_QTY'] * $row['FOB'];
											$lossProfit = $exFactValue-$row['FOB_VALUE'];
											?>
											<tr>
												<td width="100" ><?=$row['STYLE_REF_NO']?></td>
												<td width="100" ><?=$row['PO_NUMBER']?></td>
												<td width="100" ><?=$color_arr[$row['COLOR_NUMBER_ID']]?></td>
												<td width="100" ><?=$itemArray[$row['ITEM_NUMBER_ID']];?></td>
												<td width="100" style="text-align: right;"><?=$itemWiseQuantityArray[$job_id][$po_id][$item_id][$color_id]['ORDER_QUANTITY']?></td>
												<td width="100" style="text-align: right;" title="<?= $po_id.'--'.$item_id.'--'.$color_id ?>">
													<?php 
														  $finish_febri_req_qty=(array_sum($fabric_costing_arr['knit']['grey'][$po_id][$item_id][$color_id]) + (array_sum($fabric_costing_arr['woven']['grey'][$po_id][$item_id][$color_id])));
														  echo number_format($finish_febri_req_qty,2);
													?>
												</td>
												<td width="100" style="text-align: right;"><?=number_format($fabricArray[$job_id][$po_id][$item_id][$color_id]['GREY_FAB_QNTY'],2)?></td>
												<? if($k==0) { ?>
												<td width="100" style="vertical-align:middle; text-align:center;" width="100" rowspan="<? echo $rowspan_job[$job_id];?>" ><?=$yarnBookingArray[$job_id]?></td>
												<td width="100" style="vertical-align:middle; text-align:center;" width="100" rowspan="<? echo $rowspan_job[$job_id];?>" ><?=$allocateYarn;?></td>
												
												<td width="100" style="vertical-align:middle; text-align:center;" rowspan="<? echo $rowspan_job[$job_id];?>" ><?=$issueArray[$jobStyleArray[$job_id]];?></td>
												
												<td style="vertical-align:middle; text-align:center;" width="100" rowspan="<? echo $rowspan_job[$job_id];?>" ><?=$styleProArray[2][$jobStyleArray[$job_id]];?></td>
												<td style="vertical-align:middle; text-align:center;" width="100" rowspan="<? echo $rowspan_job[$job_id];?>"><?=$styleProQcArray[$jobStyleArray[$job_id]];?></td>
												<td style="vertical-align:middle; text-align:center;" width="100" rowspan="<? echo $rowspan_job[$job_id];?>"><?=$deliveryQtyArray[56]['KNITTING_DELIVERY'];?></td> 
												<td style="vertical-align:middle; text-align:center;" width="100" rowspan="<? echo $rowspan_job[$job_id];?>"><?=$bookingQtyArray['BATCH_QNTY'];?></td>
												<td style="vertical-align:middle; text-align:center;" width="100" rowspan="<? echo $rowspan_job[$job_id];?>"><?=$dyingQtyArray[35]['DYEING_PROD_QTY'] ;?></td>
												<td style="vertical-align:middle; text-align:center;" width="100" rowspan="<? echo $rowspan_job[$job_id];?>"><?=$styleProArray[66][$jobStyleArray[$job_id]];?></td>
												<td style="vertical-align:middle; text-align:center;" width="100" rowspan="<? echo $rowspan_job[$job_id];?>"><?=$dyingDeliveryArray[67]['DYING_DELIVERY'];?></td>
												<td style="vertical-align:middle; text-align:center;" width="100" rowspan="<? echo $rowspan_job[$job_id];?>"><?=$receiveQtyArray[68]['RECEIVE_QNTY'];?></td>
												<td style="vertical-align:middle; text-align:center;" width="100" rowspan="<? echo $rowspan_job[$job_id];?>"><?=$receiveQtyArray[68]['RECEIVE_QNTY'] - $dyingDeliveryArray[67]['DYING_DELIVERY'];?></td>
												<? } ?>
												
											</tr>
											<? 
											$k++;
										}
									}
								}
								
							}
						?>
					</tbody>
				</table>
			</fieldset> 

			<fieldset style="width:1900px; margin-top: 30px;margin-bottom: 30px;">
				<h3 style="text-align: center;">RMG details</h3>
				<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="100">Prod. Date</th>
						<th width="100">Floor</th>
						<th width="100">Line NO</th>
						<th width="100">Job No.</th>
						<th width="100">Style</th>
						<th width="100">PO</th>
						<th width="100">Item</th>
						<th width="100">SMV</th>
						<th width="100">Operator</th>
						<th width="100">Helper</th>
						<th width="100">Manpower</th>
						<th width="100">Capacity</th>
						<th width="100">Total Working Hour</th>
						<th width="100">Total Target</th>
						<th width="100">Total Production (Sweing)</th>
						<th width="100">Available Minutes</th>
						<th width="100">Produced Minutes</th>
						<th width="100">Efficiency (%)</th>
					</thead>
					<tbody>
						<? 
							$i=1;
							$sum_efficeincy = 0;
							foreach($rmgDataArray as $jobId => $jobWise)
							{
								foreach($jobWise as $poId => $poWise)
								{
									foreach($poWise as $itemId => $itemWise)
									{	
										foreach($itemWise as $floorId => $floorWise)
										{
											foreach($floorWise as $sewingLine => $lineWise)
											{
												ksort($lineWise);
												foreach($lineWise as $productionDate => $data)
												{
													$rowspan 		= count($colorItemWise[$job_id]);
													$exFactValue 	= $itemWiseQuantityArray[$job_id]['EX_FACTOTY_QTY'] * $row['FOB'];
													$lossProfit 	= $exFactValue-$row['FOB_VALUE'];
													$smvAdjust 		= $prod_resource_array[$sewingLine][$productionDate]['smv_adjust'];
													$manPower 		= $prod_resource_array[$sewingLine][$productionDate]['man_power'];
													$workingHour 	= $prod_resource_array[$sewingLine][$productionDate]['working_hour'];
													$availableMinutes = $smvAdjust + ($manPower * $workingHour * 60);
													$producesMinutes = $jobSMV[$jobId][$itemId] * $prodArray[$jobId][$poId][$itemId][$floorId][$sewingLine][$productionDate][5]['PRODUCTION_QNTY'];
													$efficiency = ($producesMinutes / $availableMinutes) * 100;
													$sum_efficeincy += $efficiency;
													?>
													<tr> 
														<td width="30" ><?=$i;?></td>
														<td width="100" ><?=$productionDate;?></td>
														<td width="100" ><?=$floor_library[$floorId];?></td>
														<td width="100" ><?=$lineArr[$prod_resource_array[$sewingLine][$productionDate]['line_number']];?></td>
														<td width="100" ><?=$row['JOB_NO']?></td>
														<td width="100" ><?=$row['STYLE_REF_NO']?></td>
														<td width="100" ><?=$row['PO_NUMBER']?></td>
														<td width="100" ><?=$itemArray[$itemId];?></td>
														<td width="100" style="text-align: right;"><?=$jobSMV[$jobId][$itemId];?></td>
														<td width="100" style="text-align: right;"><?=$prod_resource_array[$sewingLine][$productionDate]['operator'];?></td>
														<td width="100"  style="text-align: right;"><?=$prod_resource_array[$sewingLine][$productionDate]['helper'];?></td>
														<td width="100" style="text-align: right;"><?=$prod_resource_array[$sewingLine][$productionDate]['man_power'];?></td>
														<td width="100" style="text-align: right;"><?=$prod_resource_array[$sewingLine][$productionDate]['capacity'];?></td> 
														<td width="100" style="text-align: right;"><?=$prod_resource_array[$sewingLine][$productionDate]['working_hour'];?></td>
														<td width="100" style="text-align: right;"><?=$prod_resource_array[$sewingLine][$productionDate]['total_target'];?></td>
														<td width="100" style="text-align: right;"><?=$prodArray[$jobId][$poId][$itemId][$floorId][$sewingLine][$productionDate][5]['PRODUCTION_QNTY'];?></td>
														<td width="100" style="text-align: right;"><?=$availableMinutes;?></td>
														<td width="100" style="text-align: right;"><?=$producesMinutes;?></td>
														<td width="100" style="text-align: right;"><?=number_format($efficiency,2)." %";?></td>
														
													</tr>
													<? 
													$i++;
												}
											}
										}
									}
								}
								
							}
							$avgEfficeincy = number_format($sum_efficeincy/($i-1), 2);
						?>
						<input type="hidden" id="avgProdEffeciency" value="<?=$avgEfficeincy;?>"/>
					</tbody>
				</table>
			</fieldset> 
			
		<?

			foreach (glob("$user_id*.xls") as $filename) 
			{
				if( @filemtime($filename) < (time()-$seconds_old) )
				@unlink($filename);
			}
			//---------end------------//
			$name=time();
			$filename=$user_id."_".$name.".xls";
			$create_new_doc = fopen($filename,'w');
			$is_created = fwrite($create_new_doc,ob_get_contents());
			echo "$html****$report_type****$filename";
			exit();
		
	}
	
	
	$html = ob_get_contents();
	ob_clean();
	$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
	echo "$html****$report_type";
	exit();	
}
if($action=="print_button_variable_setting")
{
	//  echo "<pre>";
	//  print_r($data);
    $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name in($data) and module_id=7 and report_id=63 and is_deleted=0 and status_active=1","format_id","format_id");
    echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
    exit(); 
}
	//=============all popup for order =============================================
	//Today Sewing Out Qty;
if($action=="today_sewing_out_qty_break_down")
{
   echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
   list($job_id,$color_id,$color_name,$style,$txt_date_from)=explode('**',$data);
	
	$sql="SELECT  e.id as order_id,e.po_number,d.size_number_id, d.color_number_id, b.production_qnty as order_quantity
		 FROM 
			pro_garments_production_mst a,
			pro_garments_production_dtls b,
			wo_po_color_size_breakdown d, 
			wo_po_break_down e
			
		 WHERE
		
			a.id=b.mst_id and
			a.production_type=b.production_type and
			a.is_deleted=0 and 
			a.status_active=1 and a.production_date='".$txt_date_from."' and
			b.color_size_break_down_id >0 and
			b.production_type ='5' and
			b.color_size_break_down_id=d.id and
			d.po_break_down_id=e.id and 
			d.job_no_mst='$job_id' and 
			d.color_number_id=$color_id and 
			d.is_deleted =0 AND 
			d.status_active =1 AND 
			e.status_active =1";
	
	
	//echo $sql;
	
    $itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
		$sizeArr[$row[csf('size_number_id')]]=$itemSizeArr[$row[csf('size_number_id')]];
		$orderArr[$row[csf('order_id')]]=$row[csf('po_number')];
		$order_size_qty_array[$row[csf('order_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
		$size_qty_array[$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
	}
	 ?> 
     <div id="data_panel" align="center" style="width:100%">
     <fieldset style="width:580px;">  
        <label><strong>Job:</strong> <? echo $job_id; ?>, <strong>Style:</strong> <? echo $style; ?>, <strong>Color Name:</strong> <? echo $color_name; ?><label/>
        <table width="100%"  align="center" border="1" rules="all" class="rpt_table" >
            <thead>
                <tr>
                    <th>Order No</th>
                    <th width="80">Order Total</th>
                    <?
					foreach($sizeArr as $value)
                    {
					?>
						<th width="60"><? echo $value;?></th>
					<?
					}
					?>
                </tr>
            </thead>
            <?
			$i=1;
			foreach($order_size_qty_array as $order_id=>$value)
            {
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td><? echo $orderArr[$order_id]; ?></td>
				 <td align="right"><? echo array_sum($order_size_qty_array[$order_id]); ?></td>
				 <?
					foreach($sizeArr as $size_id=>$value)
					{
						?>
						<td width="60" align="right"><? echo $order_size_qty_array[$order_id][$size_id];?></td>
						<?
					}
					?>
				 </tr>
				<?
				$i++;
			}
			?>
            <tfoot>
             <tr >
             <th>Total</th>
             <th align="right"><? echo array_sum($size_qty_array); ?></th>
             <?
					foreach($sizeArr as $size_id=>$value)
                    {
						?>
						<th width="60" align="right"><? echo $size_qty_array[$size_id];?></th>
						<?
					}
					?>
             </tr>
           </tfoot>
         </table>
       </fieldset>
    </div>
    <br />
	 <?
	 exit();
}

//color_wise_order_qty
if($action=="color_wise_order_qty_break_down")
{
   echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
   list($job_id,$color_id,$color_name,$style)=explode('**',$data);
	$sql="SELECT  e.id as order_id,e.po_number,d.size_number_id, d.color_number_id, d.order_quantity
		 FROM wo_po_color_size_breakdown d, wo_po_break_down e
		 WHERE
	     d.po_break_down_id=e.id and d.job_no_mst='$job_id' and d.color_number_id=$color_id and d.is_deleted =0 AND d.status_active =1 AND e.status_active =1";
	
    $itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
		$sizeArr[$row[csf('size_number_id')]]=$itemSizeArr[$row[csf('size_number_id')]];
		$orderArr[$row[csf('order_id')]]=$row[csf('po_number')];
		$order_size_qty_array[$row[csf('order_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
		$size_qty_array[$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
	}
	 ?> 
     <div id="data_panel" align="center" style="width:100%">
     <fieldset style="width:580px;">  
        <label><strong>Job:</strong> <? echo $job_id; ?>, <strong>Style:</strong> <? echo $style; ?>, <strong>Color Name:</strong> <? echo $color_name; ?><label/>
        <table width="100%"  align="center" border="1" rules="all" class="rpt_table" >
            <thead>
                <tr>
                    <th>Order No</th>
                    <th width="80">Order Total</th>
                    <?
					foreach($sizeArr as $value)
                    {
					?>
						<th width="60"><? echo $value;?></th>
					<?
					}
					?>
                </tr>
            </thead>
            <?
			$i=1;
			foreach($order_size_qty_array as $order_id=>$value)
            {
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td><? echo $orderArr[$order_id]; ?></td>
				 <td align="right"><? echo array_sum($order_size_qty_array[$order_id]); ?></td>
				 <?
					foreach($sizeArr as $size_id=>$value)
					{
						?>
						<td width="60" align="right"><? echo $order_size_qty_array[$order_id][$size_id];?></td>
						<?
					}
					?>
				 </tr>
				<?
				$i++;
			}
			?>
            <tfoot>
             <tr >
             <th>Total</th>
             <th align="right"><? echo array_sum($size_qty_array); ?></th>
             <?
					foreach($sizeArr as $size_id=>$value)
                    {
						?>
						<th width="60" align="right"><? echo $size_qty_array[$size_id];?></th>
						<?
					}
					?>
             </tr>
           </tfoot>
         </table>
       </fieldset>
    </div>
    <br />
	 <?
	 exit();
}



// job qty for style
if($action=="order_quantity")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	if($db_type==0) $year_cond=" and year(a.insert_date)=$insert_date";
	if($db_type==2) $year_cond=" and  extract(year from a.insert_date)=$insert_date";
	
	$sql="SELECT  d.size_number_id, d.color_number_id, d.order_quantity
		 FROM wo_po_color_size_breakdown d, wo_po_break_down e
		 WHERE
	     d.po_break_down_id=e.id and d.po_break_down_id=$order_id and d.is_deleted =0 AND d.status_active =1 AND e.status_active =1";
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
    $itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$job_size_array=array();
	$job_size_qnty_array=array();
	$job_color_array=array();
	$job_color_qnty_array=array();
	$job_color_size_qnty_array=array();
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
	$job_size_array[$order_id][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
	$job_size_qnty_array[$order_id][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
	$job_color_array[$order_id][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
	$job_color_qnty_array[$order_id][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
	$job_color_size_qnty_array[$order_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
	}
	$job_color_tot=0;
	 ?> 
     <div id="data_panel" align="center" style="width:100%">
     <fieldset style="width:780px;">  
        <label> <strong>Po Number: <? echo $order_number; ?></strong><label/>
        <table  align="center" border="1" rules="all" class="rpt_table" >
            <thead>
                <tr>
                    <th width="200">Color</th>
                    <th width="80">Color Total</th>
                    <?
					foreach($job_size_array[$order_id] as $key=>$value)
                    {
						if($value !="")
						{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
						}
					}
					?>
                </tr>
            </thead>
            <?
			$i=1;
			foreach($job_color_array[$order_id] as $key_c=>$value_c)
            {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			if($value_c != "")
		 	{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$order_id][$value_c]; $job_color_tot+=$job_color_qnty_array[$order_id][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$order_id] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="60" align="right"><? echo $job_color_size_qnty_array[$order_id][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
			}
			?>
            <tfoot>
             <tr >
             <th>Total</th>
             <th align="right"><? echo  $job_color_tot; ?></th>
             <?
					foreach($job_size_array[$order_id] as $key_s=>$value_s)
                    {
						if($value_s !="")
						{
							?>
							<th width="60" align="right"><? echo $job_size_qnty_array[$order_id][$value_s];?></th>
							<?
						}
					}
					?>
             </tr>
           </tfoot>
         </table>
       </fieldset>
    </div>
    <br />
	 <?
	 exit();
}

   
   
   
   // job qty for style
if($action=="plan_cut_quantity")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql="SELECT  d.size_number_id, d.color_number_id, d.plan_cut_qnty
		 FROM wo_po_color_size_breakdown d, wo_po_break_down e
		 WHERE
		 d.po_break_down_id=e.id and d.po_break_down_id=$order_id and d.is_deleted =0 AND d.status_active =1 AND e.status_active =1";
		//echo $sql;
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
    $itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$job_size_array=array();
	$job_size_qnty_array=array();
	$job_color_array=array();
	$job_color_qnty_array=array();
	$job_color_size_qnty_array=array();
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
	$job_size_array[$order_id][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
	$job_size_qnty_array[$order_id][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
	$job_color_array[$order_id][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
	$job_color_qnty_array[$order_id][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
	$job_color_size_qnty_array[$order_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
	}
	$job_color_tot=0;
	 ?> 
  <div id="data_panel" align="center" style="width:100%">
     <fieldset  style="width:780px">  
        <label> <strong>Po Number: <? echo $order_number; ?></strong><label/>
        <table  align="center" border="1" rules="all" class="rpt_table" >
            <thead>
                <tr>
                    <th width="180">Color</th>
                    <th width="70">Color Total</th>
                    <?
					foreach($job_size_array[$order_id] as $key=>$value)
                    {
						if($value !="")
						{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
						}
					}
					?>
                </tr>
            </thead>
            <?
			$i=1;
			foreach($job_color_array[$order_id] as $key_c=>$value_c)
            {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			if($value_c != "")
		 	{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$order_id][$value_c]; $job_color_tot+=$job_color_qnty_array[$order_id][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$order_id] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="60" align="right"><? echo $job_color_size_qnty_array[$order_id][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
			}
			?>
            <tfoot>
             <tr bgcolor="<? // echo $bgcolor;?>">
             <th>Total</th>
             <th align="right"><? echo  $job_color_tot; ?></th>
             <?
					foreach($job_size_array[$order_id] as $key_s=>$value_s)
                    {
						if($value_s !="")
						{
							?>
							<th width="60" align="right"><? echo $job_size_qnty_array[$order_id][$value_s];?></th>
							<?
						}
					}
					?>
             </tr>
           </tfoot>
        </table>
     </fieldset>
     </div>
              <br />
	 <?
	
}
     // job qty for style
if($action=="cut_qty")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	if($db_type==0) $year_cond=" and year(a.insert_date)=$insert_date";
	if($db_type==2) $year_cond=" and  extract(year from a.insert_date)=$insert_date";
	
	$sql="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.plan_cut_qnty
			FROM wo_po_details_master a
			LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
			AND c.is_deleted =0
			AND c.status_active =1
			LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
			AND c.id = d.po_break_down_id
			AND d.is_deleted =0
			AND d.status_active =1
			WHERE
			a.job_no_prefix_num=$jobnumber_prefix and a.company_name=$company_id  $year_cond and
			a.is_deleted =0 and
			a.status_active =1";
		//echo $sql;die;
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
    $itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
    $po_number=array();
	$job_size_array=array();
	$job_size_qnty_array=array();
	$job_color_array=array();
	$job_color_qnty_array=array();
	$job_color_size_qnty_array=array();
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
		{
			$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
			$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
			$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
		}
	  ?>
       <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px"> 
      <?
	    foreach($po_number as $key_po=>$value_po)
                    {
					$job_color_tot=0;
	 ?> 
  
        <label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
            <thead>
                <tr>
                    <th width="180">Color</th>  <th width="70">Color Total</th>
                    <?
					foreach($job_size_array[$value_po] as $key=>$value)
                    {
						if($value !="")
						{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
						}
					}
					?>
                </tr>
            </thead>
            <?
			$i=1;
			foreach($job_color_array[$value_po] as $key_c=>$value_c)
            {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			if($value_c != "")
            {
			?>
           
             <tr bgcolor="<? echo $bgcolor;?>">
             <td align="center"><? echo  $colorArr[$value_c]; ?></td>
             <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
             <?
					foreach($job_size_array[$value_po] as $key_s=>$value_s)
                    {
						if($value_s !="")
						{
							?>
							<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
							<?
						}
					}
					?>
             </tr>
            <?
			$i++;
			}
			}
			?>
            <tfoot>
             <tr bgcolor="<? // echo $bgcolor;?>">
             <th>Total</th>
             <th align="right"><? echo  $job_color_tot; ?></th>
             <?
					foreach($job_size_array[$value_po] as $key_s=>$value_s)
                    {
						if($value_s !="")
						{
							?>
							<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
							<?
						}
					}
					?>
             </tr>
           </tfoot>
        </table>
	 <?
    }
	?>
    <br />
       
  </div>
     </fieldset>
    <?
		
}

if($action=="today_order_cutting")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select cutting_update,production_entry from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('cutting_update')];  
	  }
	 
	 if($type==1)  $insert_cond=" and  d.production_date='$insert_date'";
     if($type==2)  $insert_cond=" and  d.production_date<='$insert_date'";
	$sql_job=sql_select("select a.buyer_name,a.job_no,a.style_ref_no,b.po_number,shipment_date,b.po_quantity as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and b.id='$order_id' and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1 ");
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="200">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
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
                       <td align="center"><? echo $row[csf('job_no')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
      </fieldset>
       <br />
    <?
	  
	if($entry_break_down==1)
	  {
		  
		  	$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=1 $insert_cond and
			d.po_break_down_id=$order_id  and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			c.is_deleted =0 AND c.status_active =1 and 
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset  style="widows:400px">
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Cutting Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  
						  
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		$sql="SELECT  sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=1  and
		    e.is_deleted =0 and
			e.status_active =1   $insert_cond
			group by f.size_number_id,f.color_number_id";
			
		//echo $sql;
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$order_number][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$order_number][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		 $job_color_tot=0;
		 ?> 
        <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">  
			<label> <strong>Po Number: <? echo $order_number; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
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
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$order_number] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$order_number][$value_c]; $job_color_tot+=$job_color_qnty_array[$order_number][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="60" align="right"><? echo $job_color_size_qnty_array[$order_number][$value_c][$value_s];?></td>
						<?
							}
						}
				 ?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
						<?
							}
						}
				?>
				 </tr>
			  </tfoot>
		</table>
				  <br />
     </fieldset>
 </div>
				 
		 <?
		}
}
//today cut qty  $txt_date_from

if($action=="embl_issue_order")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	
	$sql_result = sql_select("select printing_emb_production,production_entry from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('printing_emb_production')];  
	  }
	$sql_job=sql_select("select a.buyer_name,a.job_no,a.style_ref_no,b.po_number,shipment_date,b.po_quantity as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and b.id='$order_id'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  ");

	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="200">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
      </fieldset>
       <br />
    <?
	if($type==1)  $insert_cond="   and  d.production_date='$insert_date'";
	if($type==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if(empty($embl_type))
	{
		$embl_type = '1,2,3,4';
	}
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.embel_name in($embl_type) and
			d.production_type=2 $insert_cond and
			d.po_break_down_id=$order_id and a.company_name=$company_id  
			and a.is_deleted =0 and a.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			and d.is_deleted =0 and d.status_active =1
			group by c.po_number";
			// echo $sql;die;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Embl. Issue Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  
						  
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		 $sql="SELECT  sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=2  and
			d.embel_name in($embl_type) and
		    e.is_deleted =0 and
			e.status_active =1   $insert_cond
			group by f.size_number_id,f.color_number_id";
		 //echo $sql;die;
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
			$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$order_number][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array[$order_number][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
			$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
			$job_color_tot=0;
		 ?>  
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px"> 
			<label> <strong>Po Number: <? echo $order_number; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
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
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$order_number] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$order_number][$value_c]; $job_color_tot+=$job_color_qnty_array[$order_number][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="60" align="right"><? echo $job_color_size_qnty_array[$order_number][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<th width="60" align="right"><? echo $job_size_qnty_array[$order_number][$value_s];?></th>
								<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		 </table>
				  <br />
      </fieldset>
   </div> 
		 <?
		}
}

if($action=="embl_receive_order")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select printing_emb_production,production_entry from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('printing_emb_production')];  
	  }
	
	  $sql_job=sql_select("select a.buyer_name,a.job_no,a.style_ref_no,b.po_number,shipment_date,b.po_quantity as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and b.id='$order_id' and a.company_name=$company_id  and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1 ");
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="200">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
      </fieldset>
       <br />
    <?
	
    if($type==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($type==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if(empty($embl_type))
	{
		$embl_type = '1,2,3,4';
	}
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=3 $insert_cond and
			d.embel_name in($embl_type) and
			d.po_break_down_id=$order_id and a.company_name=$company_id  
			and a.is_deleted =0 and a.status_active =1 and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Embl. Receive Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	  }
	else
	 {
		 $sql="SELECT  sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=3  and
			d.embel_name in($embl_type) and
		    e.is_deleted =0 and
			e.status_active =1   $insert_cond
			group by f.size_number_id,f.color_number_id";
			//echo $sql;die;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
			$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$order_number][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array[$order_number][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
			$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
			$job_color_tot=0;
		 ?> 
         <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">  
			<label> <strong>Po Number: <? echo $order_number; ?></strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
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
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$order_number] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$order_number][$value_c]; $job_color_tot+=$job_color_qnty_array[$order_number][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="60" align="right"><? echo $job_color_size_qnty_array[$order_number][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<th width="60" align="right"><? echo $job_size_qnty_array[$order_number][$value_s];?></th>
								<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		 </table>
				  <br />
      </fieldset>            
 </div>
		 <?
		}
}

if($action=="sewing_input_order")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select sewing_production,production_entry from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }

	  $sql_job=sql_select("select a.buyer_name,a.job_no,a.style_ref_no,b.po_number,shipment_date,b.po_quantity as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and b.id='$order_id' and a.company_name=$company_id  and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1 ");
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="200">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
      </fieldset>
       <br />
    <?
	if($type==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($type==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=4 $insert_cond and
			d.po_break_down_id=$order_id and a.company_name=$company_id 
			and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset style="width:400px">
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Swing Input Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	  }
	else
	 {
		 $sql="SELECT  sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=4  and
		    e.is_deleted =0 and
			e.status_active =1   $insert_cond
			group by f.size_number_id,f.color_number_id";
		//echo $sql;
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
			$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$order_number][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array[$order_number][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
			$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
			$job_color_tot=0;
		 ?>  
       <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px"> 
			<label> <strong>Po Number: <? echo $order_number; ?><strong><label/>
			<table  align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
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
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$order_number] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$order_number][$value_c]; $job_color_tot+=$job_color_qnty_array[$order_number][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="60" align="right"><? echo $job_color_size_qnty_array[$order_number][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<th width="60" align="right"><? echo $job_size_qnty_array[$order_number][$value_s];?></th>
								<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		 </table>
				  <br />
      </fieldset>
  </div>
		 <?
		}
}



if($action=="sewing_output_order")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select sewing_production,production_entry from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }
	$sql_job=sql_select("select a.buyer_name,a.job_no,a.style_ref_no,b.po_number,shipment_date,b.po_quantity as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and b.id='$order_id' and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1");

	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
       <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:635px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="200">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
      </fieldset>
       <br />
    <?
	if($type==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($type==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=5 $insert_cond and
			d.po_break_down_id=$order_id and a.company_name=$company_id
			and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset style="width:400px">
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Swing Output Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	  }
	else
	 {
		 $sql="SELECT  sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=5  and
		    e.is_deleted =0 and
			e.status_active =1   $insert_cond
			group by f.size_number_id,f.color_number_id";
		//echo $sql;
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
			$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$order_number][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array[$order_number][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
			$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
			$job_color_tot=0;
		 ?>  
       <div id="data_panel" align="center" style="width:100%">
          <fieldset  style="width:820px"> 
		  <label> <strong>Po Number: <? echo $order_number; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
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
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$order_number] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$order_number][$value_c]; $job_color_tot+=$job_color_qnty_array[$order_number][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="60" align="right"><? echo $job_color_size_qnty_array[$order_number][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$order_number][$value_s];?></th>
						<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		 </table>
				  <br />
     </fieldset>
 </div>
		 <?
		}
}





if($action=="iron__entry_order")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select iron_update,production_entry from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('iron_update')];  
	  }
	$sql_job=sql_select("select a.buyer_name,a.job_no,a.style_ref_no,b.po_number,shipment_date,b.po_quantity as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and b.id='$order_id'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1");
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
       <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:635px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="200">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
      </fieldset>
       <br />
    <?
if($type==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($type==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=7 $insert_cond and
			d.po_break_down_id=$order_id and a.company_name=$company_id
			and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset style="width:400px">
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Iron Qty Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	  }
	else
	 {
		 $sql="SELECT  sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=7  and
		    e.is_deleted =0 and
			e.status_active =1   $insert_cond
			group by f.size_number_id,f.color_number_id";
		//echo $sql;
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
			$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$order_number][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array[$order_number][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
			$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
			$job_color_tot=0;
		 ?> 
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">  
			<label> <strong>Po Number: <? echo $order_number; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
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
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$order_number] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$order_number][$value_c]; $job_color_tot+=$job_color_qnty_array[$order_number][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="60" align="right"><? echo $job_color_size_qnty_array[$order_number][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$order_number][$value_s];?></th>
						<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		 </table>
				  <br />
        </fieldset>
 </div>
		 <?
		}
}
if($action=="finish_input_woven_order")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select poly_update,production_entry from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('poly_update')];  
	  }
	$sql_job=sql_select("select a.buyer_name,a.job_no,a.style_ref_no,b.po_number,shipment_date,b.po_quantity as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and b.id='$order_id'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1");
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
       <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:635px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="200">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
      </fieldset>
       <br />
    <?
if($type==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($type==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=11 $insert_cond and
			d.po_break_down_id=$order_id and a.company_name=$company_id
			and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset style="width:400px">
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Iron Qty Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	  }
	else
	 {
		 $sql="SELECT  sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=11  and
		    e.is_deleted =0 and
			e.status_active =1   $insert_cond
			group by f.size_number_id,f.color_number_id";
		//echo $sql;
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
			$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$order_number][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array[$order_number][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
			$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
			$job_color_tot=0;
		 ?> 
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">  
			<label> <strong>Po Number: <? echo $order_number; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
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
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$order_number] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$order_number][$value_c]; $job_color_tot+=$job_color_qnty_array[$order_number][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="60" align="right"><? echo $job_color_size_qnty_array[$order_number][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$order_number][$value_s];?></th>
						<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		 </table>
				  <br />
        </fieldset>
 </div>
		 <?
		}
}




if($action=="poly__entry_order")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select poly_update,production_entry from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('poly_update')];  
	  }
	$sql_job=sql_select("select a.buyer_name,a.job_no,a.style_ref_no,b.po_number,shipment_date,b.po_quantity as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and b.id='$order_id'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1");
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
       <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:635px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="200">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
      </fieldset>
       <br />
    <?
if($type==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($type==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=11 $insert_cond and
			d.po_break_down_id=$order_id and a.company_name=$company_id
			and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset style="width:400px">
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Iron Qty Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	  }
	else
	 {
		 $sql="SELECT  sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=11  and
		    e.is_deleted =0 and
			e.status_active =1   $insert_cond
			group by f.size_number_id,f.color_number_id";
		//echo $sql;
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
			$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$order_number][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array[$order_number][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
			$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
			$job_color_tot=0;
		 ?> 
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">  
			<label> <strong>Po Number: <? echo $order_number; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
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
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$order_number] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$order_number][$value_c]; $job_color_tot+=$job_color_qnty_array[$order_number][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="60" align="right"><? echo $job_color_size_qnty_array[$order_number][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$order_number][$value_s];?></th>
						<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		 </table>
				  <br />
        </fieldset>
 </div>
		 <?
		}
}



if($action=="finish_qty_order")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select finishing_update,production_entry from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('finishing_update')];  
	  }
	
	$sql_job=sql_select("select a.buyer_name,a.job_no,a.style_ref_no,b.po_number,shipment_date,b.po_quantity as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and b.id='$order_id'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1");

	  foreach($sql_result as $val)
		  {
			$entry_break_down=$val[csf('cutting_update')];  
		   }
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
       <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:635px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="200">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
      </fieldset>
       <br />
    <?
	    
	if($entry_break_down!=1)
	{
		
		 if($type==1)  $insert_cond="   and  d.production_date='$insert_date'";
		 if($type==2)  $insert_cond="   and  d.production_date<='$insert_date'";
		 $sql="SELECT  sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=8 and
		    e.is_deleted =0 and
			e.status_active =1   $insert_cond
			group by f.size_number_id,f.color_number_id";
		//echo $sql;
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
			$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$order_number][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array[$order_number][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
			$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
			$job_color_tot=0;
		 ?> 
  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">  
			<label> <strong>Po Number: <? echo $order_number; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
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
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$order_number] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$order_number][$value_c]; $job_color_tot+=$job_color_qnty_array[$order_number][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="60" align="right"><? echo $job_color_size_qnty_array[$order_number][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$order_number][$value_s];?></th>
						<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		 </table>
				  <br />
                  
      </fieldset>
 </div>
		 <?
		}
}



if($action=="exfactrory__entry_order")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select ex_factory,production_entry from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('ex_factory')];  
	  }
	$sql_job=sql_select("select a.buyer_name,a.job_no,a.style_ref_no,b.po_number,shipment_date,b.po_quantity as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and b.id='$order_id'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1 ");

	  foreach($sql_result as $val)
		  {
			$entry_break_down=$val[csf('cutting_update')];  
		   }
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
       <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:635px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="200">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
      </fieldset>
       <br />
    <?
	    
	if($entry_break_down!=1)
	{
		
		 if($type==1)  $insert_cond="   and  d.ex_factory_date='$insert_date'";
		 if($type==2)  $insert_cond="   and  d.ex_factory_date<='$insert_date'";
		 $sql="SELECT  
		 sum(CASE WHEN d.entry_form!=85 THEN e.production_qnty ELSE 0 END) as product_qty,
		 sum(CASE WHEN d.entry_form=85 THEN e.production_qnty ELSE 0 END) as ret_product_qty,
		 f.size_number_id,f.color_number_id
			FROM pro_ex_factory_mst d,pro_ex_factory_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
		    e.is_deleted =0 and
			e.status_active =1   $insert_cond
			group by f.size_number_id,f.color_number_id";
		//echo $sql;
		  
		  
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
			$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$order_number][$row[csf('size_number_id')]]+=$row[csf('product_qty')]-$row[csf('ret_product_qty')];
			$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array[$order_number][$row[csf('color_number_id')]]+=$row[csf('product_qty')]-$row[csf('ret_product_qty')];
			$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')]-$row[csf('ret_product_qty')];
		}
			$job_color_tot=0;
		 ?> 
 <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">  
			<label> <strong>Po Number: <? echo $order_number; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
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
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$order_number] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$order_number][$value_c]; $job_color_tot+=$job_color_qnty_array[$order_number][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="60" align="right"><? echo $job_color_size_qnty_array[$order_number][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$order_number][$value_s];?></th>
						<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		 </table>
				  <br />
       </fieldset>
 </div>
		 <?
		}
}

if($action=="reject_qty_order")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

			$sql_job=sql_select("select a.buyer_name,a.job_no,a.style_ref_no,b.po_number,shipment_date,b.po_quantity as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and b.id='$order_id'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1 ");

	  foreach($sql_result as $val)
		  {
			$entry_break_down=$val[csf('cutting_update')];  
		   }
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:670px">
       <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:635px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
         <table width="650px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="150">Buyer Name</th>
              <th width="80">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="150">Order No</th>
              <th width="80">Ship Date</th>
              <th width="80">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
      </fieldset>
       <br />
    <?
			
		 if($type==1)  $insert_cond="   and  d.production_date='$insert_date'";
		 if($type==2)  $insert_cond="   and  d.production_date<='$insert_date'";
		 $sql="SELECT  d.production_date,sum(d.reject_qnty) as reject_qnty
			FROM pro_garments_production_mst d
			WHERE 
			d.po_break_down_id=$order_id  and
			d.production_type=5 and
		    d.is_deleted =0 and
			d.status_active =1   $insert_cond
			group by d.production_date";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        
        <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:400px">
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Production Date</th>
                        <th width="100">Sewing Rejact Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('production_date')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('reject_qnty')]; echo  $row[csf('reject_qnty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
      </fieldset>
 </div>
		<?
	
}


//============================================finish all popup for order=================================================================================
// for shipping status popup all shipment

//========================================================================================================================================================
if($action=="shipping_sataus_style")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

?>
	<fieldset style="width:350px; margin-left:5px">
        <table border="1" class="rpt_table" rules="all" width="350" cellpadding="0" cellspacing="0">
            <thead>
                <th width="40">SL</th>
                <th width="150">order Number</th>
                <th width="150">Shiping status</th>
               
            </thead>
         </table>
         <div style="width:380px; max-height:270px; overflow-y:scroll" id="scroll_body">
             <table border="1" class="rpt_table" rules="all" width="350" cellpadding="0" cellspacing="0">
                <?
				if($db_type==0) $year_cond=" and year(b.insert_date)=$insert_date";
				if($db_type==2) $year_cond=" and  extract(year from b.insert_date)=$insert_date";
                $i=1; $total_qnty=0;
                $sql="select a.po_number,a.shiping_status  as status from  wo_po_break_down a,wo_po_details_master b where  a.job_no_mst=b.job_no and b.job_no_prefix_num=$jobnumber_prefix and b.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1 $year_cond  group by a.po_number,a.shiping_status order by a.po_number";
				//echo $sql;
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if ($i%2==0)  
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";	
                
                    $total_qnty+=$row[csf('qnty')];
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="150" align="center"><p><? echo $row[csf('po_number')]; ?>&nbsp;</td>
                        <td width="150" align="center"><p><? echo $shipment_status[$row[csf('status')]]; ?>&nbsp;</p></td>
                 
                    </tr>
                <?
                $i++;
                }
                ?>
              
            </table>
        </div>	
	</fieldset>   
<?
exit();
}


if($action=="shipping_sataus")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

?>
	<fieldset style="width:350px; margin-left:5px">
        <table border="1" class="rpt_table" rules="all" width="350" cellpadding="0" cellspacing="0">
            <thead>
                <th width="40">SL</th>
                <th width="150">order Number</th>
                <th width="150">Shiping status</th>
               
            </thead>
         </table>
         <div style="width:380px; max-height:270px; overflow-y:scroll" id="scroll_body">
             <table border="1" class="rpt_table" rules="all" width="350" cellpadding="0" cellspacing="0">
                <?
				if($db_type==0) $year_cond=" and year(b.insert_date)=$insert_date";
				if($db_type==2) $year_cond=" and  extract(year from b.insert_date)=$insert_date";
                $i=1; $total_qnty=0;
                $sql="select a.po_number,a.shiping_status  as status from  wo_po_break_down a,wo_po_details_master b where  a.job_no_mst=b.job_no and b.job_no_prefix_num=$jobnumber_prefix and b.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1 $year_cond  group by a.po_number,a.shiping_status order by a.po_number";
				//echo $sql;
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if ($i%2==0)  
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";	
                
                    $total_qnty+=$row[csf('qnty')];
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="150" align="center"><p><? echo $row[csf('po_number')]; ?>&nbsp;</td>
                        <td width="150" align="center"><p><? echo $shipment_status[$row[csf('status')]]; ?>&nbsp;</p></td>
                 
                    </tr>
                <?
                $i++;
                }
                ?>
              
            </table>
        </div>	
	</fieldset>   
<?
exit();
}

// for job qty all  job_qty_all



if($action=="job_color_size")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	if($db_type==0) $year_cond=" and year(a.insert_date)=$insert_date";
	if($db_type==2) $year_cond=" and  extract(year from a.insert_date)=$insert_date";
	
	$sql="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity
		FROM wo_po_details_master a
		LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
		AND c.is_deleted =0
		AND c.status_active =1
		LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
		AND c.id = d.po_break_down_id
		AND d.is_deleted =0
		AND d.status_active =1
		WHERE
		a.job_no_prefix_num=$jobnumber_prefix and a.company_name=$company_id  $year_cond and
		a.is_deleted =0 and
		a.status_active =1
		";
		//echo $sql;die;
	
	
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
    $itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
    $po_number=array();
	$job_size_array=array();
	$job_size_qnty_array=array();
	$job_color_array=array();
	$job_color_qnty_array=array();
	$job_color_size_qnty_array=array();
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
    $po_number[$row[csf('po_number')]]=$row[csf('po_number')];
	$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
	$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
	$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
	$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
	$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
	}
	
	// print_r($job_size_array);die;
	?>

     <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
    <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:635px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
      <?
	    foreach($po_number as $key_po=>$value_po)
            {
			$job_color_tot=0;
			
				
	 ?>  
   
            <label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
            <table width="" align="center" border="1" rules="all" class="rpt_table" >
                 
                <thead>
                    
                
                    <tr>
                        <th width="180">Color</th>
                        <th width="80">Color Total</th>
                        <?
                        
                        foreach($job_size_array[$value_po] as $key=>$value)
                        {
                            if($value !="")
                            {
                        ?>
                        <th width="60"><? echo $itemSizeArr[$value];?></th>
                        <?
                            }
                        }
                        ?>
                    </tr>
                </thead>
                <?
                $i=1;
                foreach($job_color_array[$value_po] as $key_c=>$value_c)
                {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                if($value_c != "")
                {
                ?>
               
                 <tr bgcolor="<? echo $bgcolor;?>">
                 <td><? echo  $colorArr[$value_c]; ?></td>
                 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
                 <?
                        foreach($job_size_array[$value_po] as $key_s=>$value_s)
                        {
                            if($value_s !="")
                            {
                        ?>
                        <td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
                        <?
                            }
                        }
                        ?>
                 </tr>
                <?
                $i++;
                }
                }
                ?>
                <tfoot>
                 <tr bgcolor="<? // echo $bgcolor;?>">
                 <th>Total</th>
                 <th align="right"><? echo  $job_color_tot; ?></th>
                 <?
                        foreach($job_size_array[$value_po] as $key_s=>$value_s)
                        {
                            if($value_s !="")
                            {
                        ?>
                        <th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
                        <?
                            }
                        }
                        ?>
                 </tr>
               </tfoot>
                  </table>
                  <br />
                 
	 <?
        }
		?>
  </fieldset>
 
 <?   
}

     // job qty for style
if($action=="job_color_style")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	if($db_type==0) $year_cond=" and year(a.insert_date)=$insert_date";
	if($db_type==2) $year_cond=" and  extract(year from a.insert_date)=$insert_date";
    $sql="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity
			FROM wo_po_details_master a
			LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
			AND c.is_deleted =0
			AND c.status_active =1
			LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
			AND c.id = d.po_break_down_id
			AND d.is_deleted =0
			AND d.status_active =1
			WHERE
			a.job_no_prefix_num=$jobnumber_prefix and a.company_name=$company_id  $year_cond and
			a.is_deleted =0 and
			a.status_active =1";
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
    $itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
    $po_number=array();
	$job_size_array=array();
	$job_size_qnty_array=array();
	$job_color_array=array();
	$job_color_qnty_array=array();
	$job_color_size_qnty_array=array();
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
    $po_number[$row[csf('po_number')]]=$row[csf('po_number')];
	$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
	$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
	$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
	$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
	$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
	}
	?>
      <div id="data_panel" align="center" style="width:100%">
    <fieldset  style="width:820px">
    <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:635px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
      <?
	    foreach($po_number as $key_po=>$value_po)
                    {
					$job_color_tot=0;
	 ?>   
        <label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
        <table width="" align="center" border="1" rules="all" class="rpt_table" >
            <thead>
                <tr>
                    <th width="180">Color</th><th width="80">Color Total</th>
                    <?
					foreach($job_size_array[$value_po] as $key=>$value)
                    {
					if($value !="")
						{
							?>
							<th width="60" align="center"><? echo $itemSizeArr[$value];?></th>
							<?
						}
					}
					?>
                </tr>
            </thead>
            <?
			$i=1;
			foreach($job_color_array[$value_po] as $key_c=>$value_c)
            {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			if($value_c != "")
            {
			?>
             <tr bgcolor="<? echo $bgcolor;?>">
             <td align="center"><? echo  $colorArr[$value_c]; ?></td>
             <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
             <?
					foreach($job_size_array[$value_po] as $key_s=>$value_s)
                    {
						if($value_s !="")
						{
							?>
							<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
							<?
						}
					}
					?>
             </tr>
            <?
			$i++;
			}
			}
			?>
            <tfoot>
             <tr bgcolor="<? // echo $bgcolor;?>">
             <th>Total</th>
             <th align="right"><? echo  $job_color_tot; ?></th>
             <?
					foreach($job_size_array[$value_po] as $key_s=>$value_s)
                    {
						if($value_s !="")
						{
							?>
							<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
							<?
						}
					}
					?>
             </tr>
           </tfoot>
        </table>
              <br />
             
	 <?
        }
	?>
    </fieldset>
    </div>
    <?
	
}

     // job qty for style
if($action=="cut_qty_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	if($db_type==0) $year_cond=" and year(a.insert_date)=$insert_date";
	if($db_type==2) $year_cond=" and  extract(year from a.insert_date)=$insert_date";
	
	$sql="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.plan_cut_qnty
		FROM wo_po_details_master a
		LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
		AND c.is_deleted =0
		AND c.status_active =1
		LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
		AND c.id = d.po_break_down_id
		AND d.is_deleted =0
		AND d.status_active =1
		WHERE
		a.job_no_prefix_num=$jobnumber_prefix and a.company_name=$company_id  $year_cond and
		a.is_deleted =0 and
		a.status_active =1";
		//echo $sql;die;
	
	
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
    $itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
    $po_number=array();
	$job_size_array=array();
	$job_size_qnty_array=array();
	$job_color_array=array();
	$job_color_qnty_array=array();
	$job_color_size_qnty_array=array();
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
    $po_number[$row[csf('po_number')]]=$row[csf('po_number')];
	$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
	$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
	$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
	$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
	$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
	}
	
	// print_r($job_size_array);die;
	?>

    
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
       <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:635px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
      <?
	    foreach($po_number as $key_po=>$value_po)
                    {
					$job_color_tot=0;
	 ?>   
     
        <label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
        <table width="" align="center" border="1" rules="all" class="rpt_table" >
             
            <thead>
                
            
                <tr>
                    <th width="60">Color</th>
                    <th width="60">Color Total</th>
                    <?
					
					foreach($job_size_array[$value_po] as $key=>$value)
                    {
						if($value !="")
						{
					?>
                    <th width="60"><? echo $itemSizeArr[$value];?></th>
                    <?
						}
					}
					?>
                </tr>
            </thead>
            <?
			$i=1;
			foreach($job_color_array[$value_po] as $key_c=>$value_c)
            {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			if($value_c != "")
            {
			?>
           
             <tr bgcolor="<? echo $bgcolor;?>">
             <td><? echo  $colorArr[$value_c]; ?></td>
             <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
             <?
					foreach($job_size_array[$value_po] as $key_s=>$value_s)
                    {
						if($value_s !="")
						{
					?>
                    <td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
                    <?
						}
					}
					?>
             </tr>
            <?
			$i++;
			}
			}
			?>
            <tfoot>
             <tr bgcolor="<? // echo $bgcolor;?>">
             <th>Total</th>
             <th align="right"><? echo  $job_color_tot; ?></th>
             <?
					foreach($job_size_array[$value_po] as $key_s=>$value_s)
                    {
						if($value_s !="")
						{
					?>
                    <th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
                    <?
						}
					}
					?>
             </tr>
           </tfoot>
              </table>
              <br />
             
	 <?
        }
	?>
	  </fieldset>
   </div>
	
	<?	
}

//for  ====================================style with full shipment========================================================================================


// total cut quantity 

if($action=="cut_entry_total_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select cutting_update,production_entry from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('cutting_update')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
       <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:635px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	
	
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=1 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond 
			and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
       <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Cutting Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  
						  
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
      </fieldset>
		<?
	}
	else
	 {
		
		
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=1 and  d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  
			and a.is_deleted =0 and a.status_active =1 
			and e.is_deleted =0 and e.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
			
		//echo $sql;
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		
		// print_r($job_size_array);die;
		?>
		 <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
		
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		 
			<label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				 
				<thead>
					
				
					<tr>
						<th width="180">Color</th>
						<th width="80">Color Total</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
			   
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
						<?
							}
						}
						?>
				 </tr>
			   </tfoot>
				  </table>
				  <br />
				 
		 <?
		}
	 ?>
     </fieldset>
 </div>
  
  <?
	}
}
//today cut qty  $txt_date_from

if($action=="cut_entry_today_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select cutting_update,production_entry from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('cutting_update')];  
	  }
	
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1 group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
       <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:635px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	
	
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=1 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond 
			and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
       <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Cutting Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
       </fieldset>
		<?
	}
	else
	 {
		
		
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=1 and  d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  
			and a.is_deleted =0 and a.status_active =1 
			and e.is_deleted =0 and e.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
			
		//echo $sql;
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
			$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
			$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
			$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		
		// print_r($job_size_array);die;
		?>
	
		<div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
		
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		 
			<label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				 
				<thead>
					
				
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
			   
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
						<?
							}
						}
						?>
				 </tr>
			   </tfoot>
				  </table>
				  <br />
				 
		 <?
		}
		?>
           </fieldset>
 </div>
 <?
	}
}
// fro embleshment total
if($action=="embl_today_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select printing_emb_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('printing_emb_production')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1 group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
       <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:635px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=2 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond 
			and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
       <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Embl. Issue Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  
						  
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
      </fieldset>
		<?
	}
	else
	 {
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=2 and  d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  
			and a.is_deleted =0 and a.status_active =1 
			and e.is_deleted =0 and e.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
				{
				   $job_color_tot=0;
		 ?>   
 <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
			<label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="80">Color Total</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
						<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		  </table>
	     <br />
		 <?
		}
	 ?>
     </fieldset>
 </div>
  <?
	}
}


if($action=="embl_total_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select printing_emb_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('printing_emb_production')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=2 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond 
			and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
       <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Embl. Issue Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  
						  
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
      </fieldset>
		<?
	}
	else
	 {
		
		
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=2 and  d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  
			and a.is_deleted =0 and a.status_active =1 
			and e.is_deleted =0 and e.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
			
		//echo $sql;
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		
		// print_r($job_size_array);die;
		?>
        <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">	
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
  
			<label><strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
			   
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
							?>
							<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
							<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
								<?
							}
						}
						?>
				 </tr>
			   </tfoot>
				  </table>
				  <br />
				 
		 <?
		}
	 ?>
      
     </fieldset>
  </div>
  <?
	}

}

if($action=="embl_receive_total_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select printing_emb_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('printing_emb_production')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=3 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond 
			and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Embl. Receive Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  
						  
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
      </fieldset>
		<?
	}
	else
	 {
		
		
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=3 and  d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  
			and a.is_deleted =0 and a.status_active =1 
			and e.is_deleted =0 and e.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
			
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		?>
  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		<label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				 
				<thead>
					
				
					<tr>
						<th width="180">Color</th>
						<th width="80">Color Total</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
			   
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
						<?
							}
						}
						?>
				 </tr>
			   </tfoot>
				  </table>
				  <br />
				 
		 <?
		}
	   ?>
     </fieldset>
 </div>
  <?
	}
}

if($action=="embl_receive_today_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select printing_emb_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('printing_emb_production')];  
	  }
	  if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=3 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond 
			and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
       <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Embl. Receive Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
       </fieldset>
		<?
	}
	else
	 {
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=3 and  d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  
			and a.is_deleted =0 and a.status_active =1 
			and e.is_deleted =0 and e.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
			
		//echo $sql;
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		
	 ?>
    <div  align="center" style="width:100%">
       <fieldset  style="width:820px">
	  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
				{
				   $job_color_tot=0;
		 ?>   
			<label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="60"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
							?>
							<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
							<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		  </table>
	     <br />
		 <?
		}
	   ?>
     </fieldset>
 </div>
  <?
  
	}
}
//fro sewing input qty


if($action=="sewing_input_total_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select sewing_production from variable_settings_production where company_name='$company_id' and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }
 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=4 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond 
			and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Sewing Input Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  
						  
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
      </fieldset>
		<?
	}
	else
	 {
		
		
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=4 and  d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  
			and a.is_deleted =0 and a.status_active =1 
			and e.is_deleted =0 and e.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
			
		//echo $sql;
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		?>
  <div  align="center" style="width:100%">
       <fieldset  style="width:820px">
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
			<label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="60"><? echo $itemSizeArr[$value];?></th>
								<?
									}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
								<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		  </table>
				  <br />
		 <?
		}
		  ?>
     </fieldset>
 </div>
  <?
	}
}

if($action=="sewing_input_today_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select sewing_production from variable_settings_production where company_name='$company_id' and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }
	   if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=4 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond 
			and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
       <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Sewing Input Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
      </fieldset>
		<?
	}
	else
	 {
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=4 and  d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  
			and a.is_deleted =0 and a.status_active =1 
			and e.is_deleted =0 and e.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		?>
    <div  align="center" style="width:100%">
       <fieldset  style="width:820px">
        
        <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
				{
				   $job_color_tot=0;
		 ?>   
			<label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="60"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
								<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		  </table>
	     <br />
		 <?
		}
	   ?>
     </fieldset>
 </div>
      <?
	}
}

if($action=="sewing_ooutput_total_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select sewing_production from variable_settings_production where company_name='$company_id' and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                       <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=5 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond 
			and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
       <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Sewing Output Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
      </fieldset>
		<?
	}
	else
	 {
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=5 and  d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
		//echo $sql;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		?>
      <div  align="center" style="width:100%">
       <fieldset  style="width:820px">
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
			<label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="60"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
								<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		  </table>
				  <br />
		 <?
		}
	}
}

if($action=="sewing_output_today_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select sewing_production from variable_settings_production where company_name='$company_id' and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }
	  if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=5 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond 
			and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Sewing Output Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
		<?
	}
	else
	 {
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=5 and  d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		?>
      <div  align="center" style="width:100%">
       <fieldset  style="width:820px">
       <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
				{
				   $job_color_tot=0;
		 ?>   
			<label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="60"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td  align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
								<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		  </table>
	     <br />
		 <?
		}
		  ?>
     </fieldset>
 </div>
  <?
	}
}

if($action=="iron_total_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select sewing_production from variable_settings_production where company_name='$company_id' and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";


	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=7 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond 
			and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Iron Entry Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
      </fieldset>
		<?
	}
	else
	 {
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=7 and  d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
		//echo $sql;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		 ?>
    <div  align="center" style="width:100%">
       <fieldset  style="width:820px">
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
			<label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="60"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
								<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		  </table>
				  <br />
		 <?
		}
		  ?>
     </fieldset>
 </div>
  <?
	}
}

if($action=="iron_today_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select iron_update from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('iron_update')];  
	  }
	  if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=7 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond 
			and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
       <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Iron Entry Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
      </fieldset>
		<?
	}
	else
	 {
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=7 and  d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
	   ?>
  <div  align="center" style="width:100%">
       <fieldset  style="width:820px">
       <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
				{
				   $job_color_tot=0;
		 ?>   
			<label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="1030px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Color Total</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
						<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		  </table>
	     <br />
		 <?
		}
		  ?>
     </fieldset>
 </div>
  <?
	}
}


// for reject qty  reject_today


if($action=="reject_today_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
		$sql="SELECT  c.po_number,sum(d.reject_qnty) as reject_qnty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=5 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond 
			and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
      <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Sewing Rejact Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('reject_qnty')]; echo  $row[csf('reject_qnty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
      </fieldset>
		<?
	
}

if($action=="reject_total_all")
{
	 echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
     extract($_REQUEST);
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
		$sql="SELECT  c.po_number,sum(d.reject_qnty) as reject_qnty,d.production_date
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=5 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond 
			and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number,d.production_date";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
      <fieldset>
          <table width="450px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">production Date </th>
                        <th width="100">Sewing Reject Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="center"><? echo  $row[csf('production_date')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('reject_qnty')]; echo  $row[csf('reject_qnty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200"> </th>
                        <th width="100">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        
      </fieldset>
		<?
}


if($action=="finish_total_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select finishing_update from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('finishing_update')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=8 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond 
			and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
      <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Finish Qty.</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        
       </fieldset>
		<?
	}
	else
	 {
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=8 and  d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
		//echo $sql;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		?>
  <div  align="center" style="width:100%">
       <fieldset  style="width:820px">
	    <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
			<label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="60"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
								<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		  </table>
				  <br />
		 <?
		}
	  ?>
     </fieldset>
 </div>
  <?
	}
}

if($action=="finish_today_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select sewing_production from variable_settings_production where company_name='$company_id' and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }
	  
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
      </table>
    </fieldset>
       <br />
       <?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=8 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond 
			and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
      <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Finish Qty.</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
      </fieldset>
		<?
	}
	else
	 {
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=8 and  d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		?>
       <div  align="center" style="width:100%">
       <fieldset  style="width:820px">
	   <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
				{
				   $job_color_tot=0;
		 ?>   
			<label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
								<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		  </table>
	     <br />
		 <?
		}
    
	}
}
// for iron entry 

if($action=="exfactory_total_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select ex_factory from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('ex_factory')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.ex_factory_qnty) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_ex_factory_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.ex_factory_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond 
			and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Exfactory Qty.</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
		<?
	}
	else
	 {
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_ex_factory_mst d, pro_ex_factory_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			 d.ex_factory_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
		//echo $sql;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		?>
  <div  align="center" style="width:100%">
       <fieldset  style="width:820px">
		 <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
			<label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="1030px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Color Total</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
						<?
							}
						}
						?>

				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
						<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		  </table>
				  <br />
		 <?
		}
	}
}

if($action=="exfactory_today_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select ex_factory from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('ex_factory')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.ex_factory_qnty) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_ex_factory_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.ex_factory_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Exfactory Qty.</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
		<?
	}
	else
	 {
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_ex_factory_mst d, pro_ex_factory_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			 d.ex_factory_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
		//echo $sql;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		?>
  <div  align="center" style="width:100%">
       <fieldset  style="width:820px">
		 <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
			<label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="1030px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Color Total</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
						<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		  </table>
				  <br />
		 <?
		}
	}
}

//================================================finish style with full shipment=====================================================================




// total cut quantity 

if($action=="cut_entry_total")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select cutting_update,production_entry from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
//  echo "select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no";
	$sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  group by a.buyer_name,a.style_ref_no");

	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('cutting_update')];  
	  }
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
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
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
    <?
	
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=1 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset style="width:400px">
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Cutting Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,sum(f.order_quantity) as order_quantity,sum(f.plan_cut_qnty) as plan_cut_qnty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=1 and  d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
			
		//echo $sql;
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		//******************************************************
		
	    $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
		FROM wo_po_details_master a
		LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
		AND c.is_deleted =0
		AND c.status_active =1
		LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
		AND c.id = d.po_break_down_id
		AND d.is_deleted =0
		AND d.status_active =1
		WHERE
		a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
		a.is_deleted =0 and
		a.status_active =1";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
        //******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
		{
			$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
			$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
		}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
		{
			$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
			$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
			$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
			$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
			
			$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
			$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
		}

		?>
	
		 <fieldset>
		
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		
			<label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="930px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="40"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
                            ?>
                            <td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
                            <?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
                            ?>
                            <td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
                            <?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
				<?
				$i++;
				}
				}
				?>
				  </table>
				  <br />
		 <?
		}
		?>
      </fieldset>
        <?
	}
	?>
  
    </div>
    
    <?
}

if($action=="cut_entry_today")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select cutting_update,production_entry from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('cutting_update')];  
	  }
     if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	 // echo "select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no";
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                       <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
       </table>
       </fieldset>
       <br />
	<?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.shiping_status!=3 and
			c.id=d.po_break_down_id and
			d.production_type=1 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Cutting Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
					 if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		  $sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id  and
				d.id=e.mst_id and
				e.color_size_break_down_id=f.id and
				c.id=f.po_break_down_id and
				e.production_type=1 and  d.production_date='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
				a.status_active =1 and e.is_deleted =0 and
				e.status_active =1
				group by c.po_number,f.size_number_id,f.color_number_id";
				
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		
	    $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
		FROM wo_po_details_master a
		LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
		AND c.is_deleted =0
		AND c.status_active =1
		LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
		AND c.id = d.po_break_down_id
		AND d.is_deleted =0
		AND d.status_active =1
		WHERE
		a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
		a.is_deleted =0 and
		a.status_active =1";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
		{
			$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
			$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
		}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
		{
			$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
			$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
			$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
			$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
			
			$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
			$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			
		}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="930px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="40"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
                            ?>
                            <td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
                            <?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>
				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
                            ?>
                            <td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
                            <?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				  </table>
				  <br />
		 <?
		}
		?>
      </fieldset>
        <?
	}
	?>
    </div>
    <?
}

// fro embleshment total
if($action=="embl_today")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select printing_emb_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('printing_emb_production')];  
	  }
	
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty 
	                      from  wo_po_details_master a,wo_po_break_down b
						  where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and  a.company_name=$company_id 
						  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                       <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
	<?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=2 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Embl. Issue Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		
		
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=2 and  d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		
	    $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
		FROM wo_po_details_master a
		LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
		AND c.is_deleted =0
		AND c.status_active =1
		LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
		AND c.id = d.po_break_down_id
		AND d.is_deleted =0
		AND d.status_active =1
		WHERE
		a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
		a.is_deleted =0 and
		a.status_active =1";

		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
			{
				$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
				$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
				$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
		  {
			$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
			$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
			$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
			$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
			
			$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
			$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
		  }
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="930px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="40"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                          <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                     <td align="left">Production Qty</td>
                          <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 </tr>
				<?
				$i++;
				}
			}
				?>
	  </table>
		 <br />
		 <?
		}
		?>
     </fieldset>
        <?
	}
	?>
    </div>
    <?
}

if($action=="embl_total")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select printing_emb_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('printing_emb_production')];  
	  }
	
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	 // echo "select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no";
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=2 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Embl. Issue Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		  $sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
				WHERE 
				a.job_no = c.job_no_mst and
				c.shiping_status!=3 and
				c.id=d.po_break_down_id  and
				d.id=e.mst_id and
				e.color_size_break_down_id=f.id and
				c.id=f.po_break_down_id and
				e.production_type=2 and  d.production_date<='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
				a.status_active =1 and e.is_deleted =0 and
				e.status_active =1
				group by c.po_number,f.size_number_id,f.color_number_id";
				
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		
	    $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
					FROM wo_po_details_master a
					LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
					AND c.is_deleted =0
					AND c.status_active =1
					LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
					AND c.id = d.po_break_down_id
					AND d.is_deleted =0
					AND d.status_active =1
					WHERE
					a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
					a.is_deleted =0 and
					a.status_active =1";

		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
			{
				$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
				$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
				$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
			{
				$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
				$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
				$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="930px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="40"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
				<?
				$i++;
				}
				}
				?>
				  </table>
               
				  <br />
		 <?
		}
		?>
      </fieldset>
        <?
	}
	?>
    </div>
    <?
}

if($action=="embl_receive_total")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select printing_emb_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('printing_emb_production')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                       <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
       </table>
       </fieldset>
       <br />
       <?
	if($entry_break_down==1)
	  {
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.shiping_status!=3 and
			c.id=d.po_break_down_id and
			d.production_type=3 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
		$sql_data = sql_select($sql);
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Embl. Receive Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
	   	$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id  and
				d.id=e.mst_id and
				e.color_size_break_down_id=f.id and
				c.id=f.po_break_down_id and
				e.production_type=3 and  d.production_date<='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
				a.status_active =1 and e.is_deleted =0 and
				e.status_active =1
				group by c.po_number,f.size_number_id,f.color_number_id";
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql); 
	    $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
		FROM wo_po_details_master a
		LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
		AND c.is_deleted =0
		AND c.status_active =1
		LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
		AND c.id = d.po_break_down_id
		AND d.is_deleted =0
		AND d.status_active =1
		WHERE
		a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
		a.is_deleted =0 and
		a.status_active =1
		";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
		{
			$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
			$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
		}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
		{
			$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
			$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
			$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
			$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
			$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
			$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
		}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="830px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="40"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
				<?
				$i++;
				}
				}
				?>
				  </table>
				  <br />
		 <?
		}
		?>
      </fieldset>
        <?
	}
	?>
  
    </div>
    
    <?
}

if($action=="embl_receive_today")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select printing_emb_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('printing_emb_production')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                       <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
       </table>
       </fieldset>
       <br />
       <?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			c.shiping_status!=3 and
			d.production_type=3 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Embl. Receive Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=3 and  d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
				
	    $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
		FROM wo_po_details_master a
		LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
		AND c.is_deleted =0
		AND c.status_active =1
		LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
		AND c.id = d.po_break_down_id
		AND d.is_deleted =0
		AND d.status_active =1
		WHERE
		a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
		a.is_deleted =0 and
		a.status_active =1";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
			{
				$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
				$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
				$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
			{
				$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
				$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
				$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				
				$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				
			}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="830px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="40"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?>                      </td>

				 </tr>
				<?
				$i++;
				}
				}
				?>
	     </table>
	  <br />
		 <?
		}
		?>
      </fieldset>
        <?
	}
	?>
    </div>
    <?
}



if($action=="sewing_input_total")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select sewing_production from variable_settings_production where company_name='$company_id' and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }
	
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                       <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
	<?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.shiping_status!=3 and
			c.id=d.po_break_down_id and
			d.production_type=4 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Sewing Input Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                          ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		  $sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id  and
				d.id=e.mst_id and
				e.color_size_break_down_id=f.id and
				c.id=f.po_break_down_id and
				e.production_type=4 and  d.production_date<='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
				a.status_active =1 and e.is_deleted =0 and
				e.status_active =1
				group by c.po_number,f.size_number_id,f.color_number_id";
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		
	   $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
					FROM wo_po_details_master a
					LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
					AND c.is_deleted =0
					AND c.status_active =1
					LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
					AND c.id = d.po_break_down_id
					AND d.is_deleted =0
					AND d.status_active =1
					WHERE
					a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
					a.is_deleted =0 and
					a.status_active =1";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
			{
				$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
				$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
				$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
			{
				$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
				$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
				$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				
				$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				
			}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="830px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="40"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
				<?
				$i++;
				}
				}
				?>
		   </table>
	     <br />
		 <?
		}
		?>
      </fieldset>
        <?
	}
	?>
    </div>
    <?
}

if($action=="sewing_input_today")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select sewing_production from variable_settings_production where company_name='$company_id' and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }
	  if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                       <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
	<?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=4 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Sewing Input Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		  $sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id  and
				d.id=e.mst_id and
				e.color_size_break_down_id=f.id and
				c.id=f.po_break_down_id and
				e.production_type=4 and  d.production_date='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
				a.status_active =1 and e.is_deleted =0 and
				e.status_active =1
				group by c.po_number,f.size_number_id,f.color_number_id";
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		
	    $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
					FROM wo_po_details_master a
					LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
					AND c.is_deleted =0
					AND c.status_active =1
					LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
					AND c.id = d.po_break_down_id
					AND d.is_deleted =0
					AND d.status_active =1
					WHERE
					a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
					a.is_deleted =0 and
					a.status_active =1";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
			{
				$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
				$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
				$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
			{
				$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
				$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
				$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="830px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="40"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
				<?
				$i++;
				}
			}
		?>
	 </table>
	 <br />
		 <?
	}
		?>
     </fieldset>
        <?
	}
	?>
    </div>
    <?
}


if($action=="sewing_ooutput_total")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select sewing_production from variable_settings_production where company_name='$company_id' and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }
	
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                       <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
	<?
	
	if($entry_break_down==1)
	{
		  $sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id and
				d.production_type=5 and d.production_date<='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
				group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Sewing Output Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						 if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		  $sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id  and
				d.id=e.mst_id and
				e.color_size_break_down_id=f.id and
				c.id=f.po_break_down_id and
				e.production_type=5 and  d.production_date<='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
				a.status_active =1 and e.is_deleted =0 and
				e.status_active =1
				group by c.po_number,f.size_number_id,f.color_number_id";
		//echo $sql;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		
		$sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
					FROM wo_po_details_master a
					LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
					AND c.is_deleted =0
					AND c.status_active =1
					LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
					AND c.id = d.po_break_down_id
					AND d.is_deleted =0
					AND d.status_active =1
					WHERE
					a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
					a.is_deleted =0 and
					a.status_active =1";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
			{
				$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
				$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
				$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
			{
				$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
				$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
				$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				
				$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				
			}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="830px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="40"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 </tr>
				<?
				$i++;
				}
				}
				?>
			 </table>
				  <br />
		 <?
		}
		?>
      </fieldset>
        <?
	}
	?>
    </div>
    <?
}

if($action=="sewing_output_today")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select sewing_production from variable_settings_production where company_name='$company_id' and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }
	  if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
       </table>
       </fieldset>
       <br />
	<?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=5 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Sewing Output Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		  $sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id  and
				d.id=e.mst_id and
				e.color_size_break_down_id=f.id and
				c.id=f.po_break_down_id and
				e.production_type=5 and  d.production_date='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
				a.status_active =1 and e.is_deleted =0 and
				e.status_active =1
				group by c.po_number,f.size_number_id,f.color_number_id";
			$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
			$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		
		
	   $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
					FROM wo_po_details_master a
					LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
					AND c.is_deleted =0
					AND c.status_active =1
					LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
					AND c.id = d.po_break_down_id
					AND d.is_deleted =0
					AND d.status_active =1
					WHERE
					a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
					a.is_deleted =0 and
					a.status_active =1";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
			{
				$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
				$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
				$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
			{
				$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
				$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
				$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?></strong><label/>

			<table width="830px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="40"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 </tr>
				<?
				$i++;
				}
				}
				?>
		  </table>
		  <br />
		 <?
		}
		?>
      </fieldset>
        <?
	}
	?>
    </div>
    <?
}


if($action=="iron_total")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select sewing_production from variable_settings_production where company_name='$company_id' and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                       <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
       </table>
       </fieldset>
       <br />
	<?
	
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=7 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Iron Entry Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		 $sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id  and
				d.id=e.mst_id and
				e.color_size_break_down_id=f.id and
				c.id=f.po_break_down_id and
				e.production_type=7 and  d.production_date<='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
				a.status_active =1 and e.is_deleted =0 and
				e.status_active =1
				group by c.po_number,f.size_number_id,f.color_number_id";
			//echo $sql;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		
	    $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
					FROM wo_po_details_master a
					LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
					AND c.is_deleted =0
					AND c.status_active =1
					LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
					AND c.id = d.po_break_down_id
					AND d.is_deleted =0
					AND d.status_active =1
					WHERE
					a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
					a.is_deleted =0 and
					a.status_active =1";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
			{
				$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
				$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
				$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
			{
				$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
				$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
				$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="830px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="40"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
				<?
				$i++;
				}
				}
				?>
		  </table>
		  <br />
		 <?
		}
		?>
      </fieldset>
        <?
	}
	?>
    </div>
    <?
}


if($action=="iron_today")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select iron_update from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('iron_update')];  
	  }
	   if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	 // echo "select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no";
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                       <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
	<?
	if($entry_break_down==1)
	{
		  $sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id and
				d.production_type=7 and d.production_date='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
				group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Iron Entry Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		 $sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id  and
				d.id=e.mst_id and
				e.color_size_break_down_id=f.id and
				c.id=f.po_break_down_id and
				e.production_type=7 and  d.production_date='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
				a.status_active =1 and e.is_deleted =0 and
				e.status_active =1
				group by c.po_number,f.size_number_id,f.color_number_id";
	    $colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	    $itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		
		
	    $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
					FROM wo_po_details_master a
					LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
					AND c.is_deleted =0
					AND c.status_active =1
					LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
					AND c.id = d.po_break_down_id
					AND d.is_deleted =0
					AND d.status_active =1
					WHERE
					a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
					a.is_deleted =0 and
					a.status_active =1";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
			{
				$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
				$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
				$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
			{
				$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
				$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
				$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="830px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="40"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
                            ?>
                            <td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
                            <?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
				<?
				$i++;
				}
				}
				?>
			 </table>
				  <br />
		 <?
		}
		?>
      </fieldset>
        <?
	}
	?>
    </div>
    <?
}


// for reject qty  reject_today


if($action=="reject_today")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	 // echo "select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no";
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
	<?
		$sql="SELECT  c.po_number,sum(d.reject_qnty) as reject_qnty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=5 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset  style="width:400px">
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Sewing Output Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('reject_qnty')]; echo  $row[csf('reject_qnty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	
}


if($action=="re_iron_total")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	 // echo "select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no";
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
	<?
	
		$sql="SELECT  c.po_number,sum(d.re_production_qty) as re_iron,d.production_date
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=7 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_condand a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number,d.production_date";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset  style="width:500px">
          <table width="450px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">production Date </th>
                        <th width="100">Re-Iron Qty</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="center"><? echo  $row[csf('production_date')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('re_iron')]; echo  $row[csf('re_iron')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200"> </th>
                        <th width="100">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
}





if($action=="reject_total")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	 // echo "select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no";
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                       <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
       </table>
       </fieldset>
       <br />
	<?
	
		  $sql="SELECT  c.po_number,sum(d.reject_qnty) as reject_qnty,d.production_date
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id and
				d.production_type=5 and d.production_date<='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_condand a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
				group by c.po_number,d.production_date";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset  style="width:500px">
          <table width="450px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">production Date </th>
                        <th width="100">Sewing Reject</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="center"><? echo  $row[csf('production_date')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('reject_qnty')]; echo  $row[csf('reject_qnty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200"> </th>
                        <th width="100">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
}


if($action=="finish_total")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select finishing_update from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('finishing_update')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	 // echo "select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no";
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                       <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
       </table>
       </fieldset>
       <br />
	<?
	
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=8 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset  style="width:400px">
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Finish Qty.</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		  $sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id  and
				d.id=e.mst_id and
				e.color_size_break_down_id=f.id and
				c.id=f.po_break_down_id and
				e.production_type=8 and  d.production_date<='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
				a.status_active =1 and e.is_deleted =0 and
				e.status_active =1
				group by c.po_number,f.size_number_id,f.color_number_id";
		//echo $sql;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
	    $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
					FROM wo_po_details_master a
					LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
					AND c.is_deleted =0
					AND c.status_active =1
					LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
					AND c.id = d.po_break_down_id
					AND d.is_deleted =0
					AND d.status_active =1
					WHERE
					a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
					a.is_deleted =0 and
					a.status_active =1	";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
			{
				$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
				$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
				$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
			{
				$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
				$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
				$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="830px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="40"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
				<?
				$i++;
				}
				}
				?>
		 </table>
				  <br />
		 <?
		}
		?>
      </fieldset>
        <?
	}
	?>
  
    </div>
    
    <?
}


if($action=="finish_today")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select sewing_production from variable_settings_production where company_name='$company_id' and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }
	   if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                       <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
	<?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=8 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset  style="width:400px">
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Finish Qty.</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
       </fieldset>
		<?
	}
	else
	 {
	  	$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id  and
				d.id=e.mst_id and
				e.color_size_break_down_id=f.id and
				c.id=f.po_break_down_id and
				e.production_type=8 and  d.production_date='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
				a.status_active =1 and e.is_deleted =0 and
				e.status_active =1
				group by c.po_number,f.size_number_id,f.color_number_id";
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		
	    $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
					FROM wo_po_details_master a
					LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
					AND c.is_deleted =0
					AND c.status_active =1
					LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
					AND c.id = d.po_break_down_id
					AND d.is_deleted =0
					AND d.status_active =1
					WHERE
					a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
					a.is_deleted =0 and
					a.status_active =1";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
		{
			$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
			$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
		}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
		{
			$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
			$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
			$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
			$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
			$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
			$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
		}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="830px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="40"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
                            ?>
                            <td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
                            <?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>
				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
                            ?>
                            <td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
                            <?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 </tr>
				<?
				$i++;
				}
				}
				?>
		  </table>
	  <br />
				 
		 <?
		}
		?>
    </fieldset>
        <?
    }
	?>
    </div>
    <?
}



if($action=="exfactory_total")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select ex_factory from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('ex_factory')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id and a.is_deleted =0 AND a.status_active =1 and b.is_deleted =0 AND b.status_active =1  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
	<?
	
	if($entry_break_down==1)
	{
		 $sql="SELECT  c.po_number,sum(CASE WHEN d.entry_form!=85 THEN d.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN d.entry_form=85 THEN d.ex_factory_qnty ELSE 0 END) as product_qty
				FROM wo_po_details_master a,wo_po_break_down c,pro_ex_factory_mst d
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id and
				c.shiping_status!=3 and
				d.ex_factory_date<='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
				group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Exfactory Qty.</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
	   	$sql="SELECT  c.po_number,sum(CASE WHEN d.entry_form!=85 THEN e.production_qnty ELSE 0 END)-sum(CASE WHEN d.entry_form=85 THEN e.production_qnty ELSE 0 END)  as product_qty,f.size_number_id,f.color_number_id
				FROM wo_po_details_master a,wo_po_break_down c,pro_ex_factory_mst d, pro_ex_factory_dtls e,wo_po_color_size_breakdown f
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id  and
				d.id=e.mst_id and
				e.color_size_break_down_id=f.id and
				c.id=f.po_break_down_id and
				 d.ex_factory_date<='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
				a.status_active =1 and e.is_deleted =0 and
				e.status_active =1
				group by c.po_number,f.size_number_id,f.color_number_id";
		//echo $sql;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		
	    $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
		FROM wo_po_details_master a
		LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
		AND c.is_deleted =0
		AND c.status_active =1
		LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
		AND c.id = d.po_break_down_id
		AND d.is_deleted =0
		AND d.status_active =1
		WHERE
		a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
		a.is_deleted =0 and
		a.status_active =1";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
			{
				$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
				$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
				$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
			{
				$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
				$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
				$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?></strong><label/>
			<table width="830px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="40"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c];                        ?></td>
				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>
				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 </tr>
				<?
				$i++;
				}
				}
				?>
		  </table>
		 <br />
		 <?
		}
		?>
      </fieldset>
        <?
	}
	?>
    </div>
    <?
}

if($action=="exfactory_today")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select ex_factory from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('ex_factory')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
	<?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number, sum(CASE WHEN d.entry_form!=85 THEN d.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN d.entry_form=85 THEN d.ex_factory_qnty ELSE 0 END) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_ex_factory_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.ex_factory_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and a.status_active =1 
			and d.is_deleted =0 and d.status_active =1
			and c.is_deleted =0 AND c.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Exfactory Qty.</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		  $sql="SELECT  c.po_number,sum(CASE WHEN d.entry_form!=85 THEN e.production_qnty ELSE 0 END)-sum(CASE WHEN d.entry_form=85 THEN e.production_qnty ELSE 0 END) as product_qty,f.size_number_id,f.color_number_id
				FROM wo_po_details_master a,wo_po_break_down c,pro_ex_factory_mst d, pro_ex_factory_dtls e,wo_po_color_size_breakdown f
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id  and
				d.id=e.mst_id and
				e.color_size_break_down_id=f.id and
				c.id=f.po_break_down_id and
				 d.ex_factory_date='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
				a.status_active =1 and e.is_deleted =0 and
				e.status_active =1
				group by c.po_number,f.size_number_id,f.color_number_id";
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		
	    $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
					FROM wo_po_details_master a
					LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
					AND c.is_deleted =0
					AND c.status_active =1
					LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
					AND c.id = d.po_break_down_id
					AND d.is_deleted =0
					AND d.status_active =1
					WHERE
					a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
					a.is_deleted =0 and
					a.status_active =1";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
			{
				$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
				$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
				$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
			{
				$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
				$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
				$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="830px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="40"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
				<?
				$i++;
				}
				}
				?>
		  </table>
		  <br />
		 <?
		}
		?>
      </fieldset>
        <?
	}
	?>
    </div>
    <?
}
