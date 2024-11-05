<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
 

if($action=="print_button_variable_setting")
    {
    	 
        $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name in($data) and module_id=7 and report_id=63 and is_deleted=0 and status_active=1","format_id","format_id");
        echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
        exit(); 
    }

if ($action=="load_report_button")
{
	extract($_REQUEST);
    $choosenCompany = $choosenCompany; 
    $sql = "SELECT id, format_id from lib_report_template where template_name=$choosenCompany and module_id=7 and report_id=63 order by id";
    $res = sql_select($sql);
    $format_id = "";
    foreach ($res as $key => $value) {
    	$format_id .= ($format_id=="") ? $value[csf('format_id')] : ','.$value[csf('format_id')];
    }
	echo $format_id;
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
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					
                    <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>'+'**'+'<? echo $type; ?>'+'**'+'<? echo $txt_job_no; ?>', 'create_job_no_search_list_view', 'search_div', 'style_wise_dhu_report_urmi_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	 $sql= "select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.po_number,b.pub_shipment_date, $year_field from wo_po_details_master a,wo_po_break_down b where  b.job_no_mst=a.job_no and a.status_active=1 and a.is_deleted=0 and a.company_name in($company_id) $search_con $buyer_id_cond $year_cond $job_cond  order by a.id desc";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref.,PO No,Ship Date", "120,130,80,50,120,80","750","240",0, $sql , "js_set_value", "$field_type", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number,pub_shipment_date", "",'','0,0,0,0,0,0,3','') ;
	exit(); 
} // Job Search end

if ($action=="load_drop_down_buyer")  
{

	echo create_drop_down( "cbo_buyer_name", 150, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data'   order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 100, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/style_wise_dhu_report_urmi_controller', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/style_wise_dhu_report_urmi_controller' );",0 );     	 
}

if ($action=="load_drop_down_floor")  //document.getElementById('cbo_floor').value
{ 
	echo create_drop_down( "cbo_floor", 100, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (5,1) order by floor_name","id,floor_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/style_wise_dhu_report_urmi_controller', this.value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'load_drop_down_line', 'line_td' );",0 ); 
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

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	// echo "<pre>";
	// print_r($process);
	extract(check_magic_quote_gpc( $process ));	

	$client_arr=return_library_array( "SELECT  a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id   ", "id", "buyer_name");


	$company_library=return_library_array( "SELECT id,company_name from lib_company", "id", "company_name");
	$buyer_arr = return_library_array("SELECT id,short_name from lib_buyer where status_active=1 and is_deleted=0","id","short_name"); 
	$color_arr = return_library_array("SELECT id,color_name from lib_color  where status_active=1 and is_deleted=0","id","color_name"); 
	$season_arr = return_library_array("SELECT id,season_name from lib_buyer_season  where status_active=1 and is_deleted=0","id","season_name"); 
	$floor_arr=return_library_array( "select id, floor_name from   lib_prod_floor", "id", "floor_name");
	$floor_group_arr=return_library_array( "select id, group_name from   lib_prod_floor", "id", "group_name");
	$cbo_company_name=str_replace("'","", $cbo_company_name);

	$cbo_wo_company_name=str_replace("'","", $cbo_wo_company_name);
	$cbo_location=str_replace("'","", $cbo_location);
	$cbo_floor=str_replace("'","", $cbo_floor);
	$cbo_floor_group_name=str_replace("'","", $cbo_floor_group_name);

	$cbo_buyer_name=str_replace("'","", $cbo_buyer_name);

	$txt_int_ref=str_replace("'","", $txt_int_ref);

	$txt_job_no=str_replace("'","", $txt_job_no);
	$txt_style_ref=str_replace("'","", $txt_style_ref);
	$txt_po_no=str_replace("'","", $txt_po_no);

	$cbo_production_type=str_replace("'","", $cbo_production_status);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	// echo $cbo_wo_company_name.$cbo_location.$cbo_floor.$cbo_floor_group_name.$txt_int_ref.$cbo_production_status.$txt_date_from.$txt_date_to; die;

	$report_type=str_replace("'","", $report_type); 
	$all_cond="";
	if($cbo_company_name)$all_cond.=" and d.company_id='$cbo_company_name'";
	if($cbo_buyer_name)$all_cond.=" and a.buyer_name='$cbo_buyer_name'";
	if($txt_job_no)$all_cond.=" and a.job_no_prefix_num='$txt_job_no'";
	if($txt_style_ref)$all_cond.=" and a.style_ref_no like '%$txt_style_ref%'";
	if($txt_po_no)$all_cond.=" and b.po_number like '%$txt_po_no%'";

	if($cbo_wo_company_name)$all_cond.=" and d.serving_company='$cbo_wo_company_name'";
	if($cbo_location)$all_cond.=" and d.location='$cbo_location'";
	if($cbo_floor)$all_cond.=" and d.floor_id='$cbo_floor'";
	if($txt_int_ref)$all_cond.=" and b.grouping='$txt_int_ref'";

	if($txt_date_from !="" && $txt_date_to !="" ){
		$all_cond.=" and d.production_date between'$txt_date_from' and '$txt_date_to'";
	}
	
	  
	$main_array=array();
	if($report_type==1) 
	{
		   $sql = "SELECT a.client_id, b.id, d.production_type, b.po_number,c.color_number_id , a.buyer_name,a.season_buyer_wise, a.style_ref_no, sum(c.order_quantity) as po_quantity, sum(e.production_qnty) as qc_pass_qty, sum(e.alter_qty) as alter_qnty,  sum(case when is_rescan=0 then  e.reject_qty else 0 end )  as reject_qnty,  sum(case when e.production_type=1 then  e.reject_qty else 0 end )  as reject_qnty_cut, sum(e.spot_qty) as spot_qnty, sum(e.replace_qty) as replace_qty,sum(case when is_rescan=1 then e.production_qnty else 0 end ) as today_rescan FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.po_break_down_id=d.po_break_down_id and d.id=e.mst_id and c.id= e.color_size_break_down_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $all_cond and d.production_type in(1,5) group by a.client_id,b.id,d.production_type,b.po_number,c.color_number_id , a.buyer_name,a.season_buyer_wise, a.style_ref_no  ";

		 /*$sql_def = "SELECT b.id, d.production_type, b.po_number,c.color_number_id , a.buyer_name,a.season_buyer_wise, a.style_ref_no, f.defect_type_id, f.defect_point_id, f.defect_qty   FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,pro_garments_production_mst d,pro_garments_production_dtls e,pro_gmts_prod_dft f  WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.po_break_down_id=d.po_break_down_id and d.id=e.mst_id and c.id= e.color_size_break_down_id and e.barcode_no=f.bundle_no and d.id=f.mst_id  and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $all_cond and d.production_type in(1,5) group by b.id, d.production_type, b.po_number,c.color_number_id , a.buyer_name,a.season_buyer_wise, a.style_ref_no, f.defect_type_id, f.defect_point_id, f.defect_qty  ";*/
		 $all_po_arr=array();
		 
		 foreach(sql_select($sql) as $val)
		 {
		 	$all_po_arr[$val[csf("id")]]=$val[csf("id")];
		 	$main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["production_type"]=$val[csf("production_type")];
		 	$main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["color_number_id"].=','.$val[csf("color_number_id")];
		 	$main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["po_quantity"]=$val[csf("po_quantity")];
		 	$qnty_arr[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]][$val[csf("production_type")]]["chk"]+=$val[csf("qc_pass_qty")]+$val[csf("alter_qnty")]+$val[csf("reject_qnty")]+$val[csf("spot_qnty")] ;

		 	$qnty_arr[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]][$val[csf("production_type")]]["reject"]+=$val[csf("reject_qnty")] ;
		 	$qnty_arr[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]][$val[csf("production_type")]]["reject_cut"]+=$val[csf("reject_qnty_cut")] ;
		 	$qnty_arr[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]][$val[csf("production_type")]]["pass"]+=$val[csf("qc_pass_qty")] ;

		 	$qnty_arr[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]][$val[csf("production_type")]]["replace"]+=$val[csf("replace_qty")] ;

		 	$main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["buyer"]=$buyer_arr[$val[csf("buyer_name")]];
		 	$main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["client"]=$client_arr[$val[csf("client_id")]];
		 	$main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["po_number"]=$val[csf("po_number")];
		 	$main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["season_buyer_wise"]=$season_arr [$val[csf("season_buyer_wise")]];
		 	if($main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["colors"]=="")
		 		$main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["colors"].=$color_arr[$val[csf("color_number_id")]];
		 	else
		 		$main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["colors"].=','.$color_arr[$val[csf("color_number_id")]];

		 }
		 //echo "<pre>";print_r($qnty_arr);die;
		 $po_ids=implode(",",$all_po_arr);
		 foreach(sql_select("SELECT po_break_down_id,color_number_id,sum(order_quantity) as qnty from wo_po_color_size_breakdown where status_active=1 and po_break_down_id in($po_ids)   group by po_break_down_id,color_number_id ") as $values  )
		 {
		 	$po_color_wise_qnty[$values[csf("po_break_down_id")]][$values[csf("color_number_id")]]=$values[csf("qnty")];
		 }
		 //print_r($po_color_wise_qnty);
		   $sql_def = "SELECT b.id, d.production_type, b.po_number,c.color_number_id , a.buyer_name,a.season_buyer_wise, a.style_ref_no, f.defect_type_id, f.defect_point_id, sum(f.defect_qty ) as defect_qty  FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,pro_garments_production_mst d,pro_garments_production_dtls e,pro_gmts_prod_dft f  WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.po_break_down_id=d.po_break_down_id and d.id=e.mst_id and c.id= e.color_size_break_down_id and e.barcode_no=f.bundle_no and d.id=f.mst_id  and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $all_cond and d.production_type in(1,5) group by b.id, d.production_type, b.po_number,c.color_number_id , a.buyer_name,a.season_buyer_wise, a.style_ref_no, f.defect_type_id, f.defect_point_id   ";
		 
		   foreach(sql_select($sql_def) as $val)
		   {
		   	$def_arr[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]][$val[csf("production_type")]][$val[csf("defect_type_id")]][$val[csf("defect_point_id")]] +=$val[csf("defect_qty")];
		   	$qnty_arr[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]][$val[csf("production_type")]]["defect"]+=$val[csf("defect_qty")] ;
		   	$def_arr2[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]][$val[csf("production_type")]][$val[csf("defect_type_id")]]  +=$val[csf("defect_qty")];

		   }
		 

			 ob_start();
			 ?>
		 
		 <div id="main_div">
		 <style type="text/css">
		 	.alignment_css
		 	{
		 		word-break: break-all;
		 		word-wrap: break-word;
		 	}
		 	.table_sub table{
		 		 
		 	}
		 	.no_border tr ,td
		 	{
		 		 
  				   
 
		 	}
		 </style>
		 	<table width="1600" cellspacing="0" >
		 		<tr class="form_caption" style="border:none;">
		 			<td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >
		 				<strong>Style wise DHU % Report	</strong>
		 			</td>
		 		</tr>
		 		<tr class="form_caption" style="border:none;">
		 			<td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >
		 				<strong><? echo $company_library[$cbo_company_name];?></strong>
		 			</td>
		 		</tr>
		 		<tr class="form_caption" style="border:none;">
		 			<td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >
		 				<strong> </strong>
		 			</td>
		 		</tr>
		 	</table>

		 	<table cellspacing="0" cellpadding="0"  border="1" rules="all"  width="1540" class="rpt_table" align="left">
		 		<thead>
		 			<tr>
		 				<th class="alignment_css" width="40">SL</th>
		 				<th class="alignment_css" width="100">Buyer</th>
		 				<th class="alignment_css" width="100">Client</th>
		 				<th class="alignment_css" width="100">Style No</th>
		 				<th class="alignment_css" width="100">Season</th>
		 				<th class="alignment_css" width="100">Order No</th>
		 				<th class="alignment_css" width="100">Color</th>
		 				<th class="alignment_css" width="100">Order Qty</th>
		 				<th class="alignment_css" width="200">Cutting Defect Name</th>
		 				<th class="alignment_css" width="100">Cutting Defect<br> Qty</th>
		 				<th class="alignment_css" width="100">Defect % </th>
		 				<th class="alignment_css" width="200">Sewing  Defect Name</th>
		 				<th class="alignment_css" width="100">Sewing  Defect <br>Qty</th>
		 				<th class="alignment_css" width="100">Defect %</th>
		 			</tr>
		 		</thead>
		 	</table>
		 	<div style="max-height:425px; overflow-y:scroll; width:1560px;" id="scroll_body">
		 		<table cellpadding="0" cellspacing="0"  align="left" border="1" class="rpt_table"  width="1540" rules="all" id="table_body" >
			 		<tbody>
				 		<?
				 		 
				 		$cutting_def_count=count($cutting_qc_reject_type);
				 		$sewing_def_count=count($sew_fin_alter_defect_type)+count($sew_fin_spot_defect_type);
				 		$diff_cut=0; 
				 		if($sewing_def_count>$cutting_def_count)
				 			$diff_cut=abs($sewing_def_count-$cutting_def_count);

				 		$diff_sew=0; 
				 		if($sewing_def_count<$cutting_def_count)
				 			$diff_sew=abs($cutting_def_count-$sewing_def_count );

				 		$span=($cutting_def_count>$sewing_def_count)?$cutting_def_count : $sewing_def_count;
				 		$span=$span+7;
				 		$i=1;
				 		foreach($main_array as $style_id=>$buyer_data )
				 		{
				 			foreach($buyer_data as $buyer_id=>$po_data )
				 			{
				 				foreach($po_data as $po_id=>$rows )
				 				{
				 					  
				 					if ($i%2==0)
				 						$bgcolor="#E9F3FF";
				 					else
				 						$bgcolor="#FFFFFF";
				 					$po_qty=0;
				 					$color_ids=array_unique(explode(",", trim($rows["color_number_id"],",")));
				 					foreach($color_ids as $vv)
				 					{
				 						$po_qty+=$po_color_wise_qnty[$po_id][$vv];
				 					}
															 
				 					?>
				 					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
				 						<td  valign="top" class="alignment_css" rowspan="<? echo $rowspan;?>" width="40" align="center"><? echo $i; ?></td>
				 						<td  valign="top"  class="alignment_css" rowspan="<? echo $rowspan;?>"  width="100" align="center"><? echo $rows["buyer"]; ?></td>
				 						<td  valign="top"  class="alignment_css"  rowspan="<? echo $rowspan;?>" width="100" align="center"><? echo $rows["client"]; ?></td>
				 						<td  valign="top"  class="alignment_css" rowspan="<? echo $rowspan;?>"  width="100" align="center"><? echo $style_id; ?></td>
				 						<td  valign="top"  class="alignment_css" rowspan="<? echo $rowspan;?>"  width="100" align="center"><? echo $rows["season_buyer_wise"]; ?></td>
				 						<td  valign="top"  class="alignment_css" rowspan="<? echo $rowspan;?>"  width="100" align="center"><? echo $rows["po_number"]; ?></td>
				 						<td  valign="top"  class="alignment_css" rowspan="<? echo $rowspan;?>" width="100" align="center"><? echo implode(",", array_unique(explode(",", $rows["colors"]))); ?></td>
				 						<td  valign="top"  class="alignment_css"  rowspan="<? echo $rowspan;?>" width="100" align="center"><? echo $po_qty; ?></td>
				 						 
				 						<td class="alignment_css"  width="200" >
				 							<table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" class="table_sub">
				 								<tr class="no_border">
				 									<td class="alignment_css" width="200">Total Defected pannels</td>

				 									
				 								</tr>
				 								<tr class='no_border'>
				 									<td class="alignment_css" width="200">Total Gmts Reject</td>


				 								</tr>

				 								<tr class='no_border'>
				 									<td class="alignment_css" width="200">Reject% To Total QC</td>


				 								</tr>


				 								<tr class='no_border'>
				 									<td class="alignment_css" width="200">Total Gmts Replace</td>


				 								</tr>

				 								<tr class='no_border'>
				 									<td class="alignment_css" width="200">Total Qc Pass Qty</td>


				 								</tr>

				 								<tr class='no_border'>
				 									<td class="alignment_css" width="200" title="( Qc Pass + Alter+ Reject + Spot)">Total Check Qty</td>


				 								</tr>
				 								<tr class='no_border'>
				 									<td class="alignment_css" width="200">DHU%</td>

				 								</tr>
				 								<?
				 								foreach($cutting_qc_reject_type as $cut_key=>$cut_val)
				 								{
				 									?>
				 									<tr class=' '>
				 									<td class="alignment_css" width="200"><? echo $cut_val;?></td>

				 									</tr>

				 									<?
				 								}
				 								if($diff_cut)
				 								{
				 									for($ii=1;$ii<=$diff_cut;$ii++)
				 									{
				 										?>
				 										<tr class='no_border'>
				 										<td class="alignment_css" width="200">&nbsp; </td>

				 										</tr>

				 									<?

				 									}
				 								}
				 								?>
				 							</table>
				 						</td>

				 						 <td class="alignment_css"  width="100" >
				 							<table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" class="table_sub">
				 								<tr class="">
				 									<td class="alignment_css" width="100"> <? 
				 									$chk=$qnty_arr[$style_id][$buyer_id][$po_id][1]["chk"];
				 									$defect=$def_arr2[$style_id][$buyer_id][$po_id][1][3];
				 									$pass=$qnty_arr[$style_id][$buyer_id][$po_id][1]["pass"];
				 									if($defect)echo $defect;else echo 0;
				 									?></td>

				 									
				 								</tr>
				 								<tr class=''>
				 									<td class="alignment_css" width="100">
				 										<? 

				 									$reject=$qnty_arr[$style_id][$buyer_id][$po_id][1]["reject_cut"];
				 									if($reject)echo $reject;else echo 0;
				 									?>
				 									</td>


				 								</tr>

				 								<tr class=''>
				 									<td class="alignment_css" width="100">
				 									<? 
				 									$pec=($reject/$pass)*100;
				 									if($pec)echo number_format($pec,2);else echo 0;
				 									?></td>


				 								</tr>


				 								<tr class=''>
				 									<td class="alignment_css" width="100">
				 									<? 
				 									$replace=$qnty_arr[$style_id][$buyer_id][$po_id][1]["replace"];
				 									if($replace)echo $replace;else echo 0;
				 									?>
				 										
				 									</td>


				 								</tr>

				 								<tr class=''>
				 									<td class="alignment_css" width="100">
				 									<? 
				 									
				 									if($pass)echo $pass;else echo 0;
				 									?>
				 										
				 									</td>


				 								</tr>

				 								<tr class=''>
				 									<td class="alignment_css" width="100">
				 									<? 
				 									
				 									if($chk)echo $chk;else echo 0;
				 									?>
				 										
				 									</td>


				 								</tr>
				 								<tr class=''>
				 									<td class="alignment_css" width="100"><? 
				 									$dhu=($defect/$chk)*100;
				 									$dhu=number_format($dhu,2);
				 									if($dhu)echo $dhu." %";else echo  "0%";
				 									?></td>

				 								</tr>
				 								<?
				 								foreach($cutting_qc_reject_type as $cut_key=>$cut_val)
				 								{
				 									?>
				 									<tr class=''>
				 									<td class="alignment_css" width="100">
				 									<? 
				 									$cut_def=$def_arr[$style_id][$buyer_id][$po_id][1][3][$cut_key];
				 									if($cut_def) 	echo $cut_def; else echo 0;
				 									?></td>

				 									</tr>

				 									<?
				 								}
				 								if($diff_cut)
				 								{
				 									for($ii=1;$ii<=$diff_cut;$ii++)
				 									{
				 										?>
				 										<tr class='no_border'>
				 										<td class="alignment_css" width="100">&nbsp; </td>

				 										</tr>

				 									<?

				 									}
				 								}

				 								?>
				 							</table>
				 						</td>


				 						 <td class="alignment_css"  width="100" >
				 							<table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" class="table_sub">
				 								<tr class="no_border">
				 									<td class="alignment_css" width="100"> </td>

				 									
				 								</tr>
				 								<tr class='no_border'>
				 									<td class="alignment_css" width="100"> </td>


				 								</tr>

				 								<tr class='no_border'>
				 									<td class="alignment_css" width="100"> </td>


				 								</tr>


				 								<tr class='no_border'>
				 									<td class="alignment_css" width="100"> </td>


				 								</tr>

				 								<tr class='no_border'>
				 									<td class="alignment_css" width="100"> </td>


				 								</tr>

				 								<tr class='no_border'>
				 									<td class="alignment_css" width="100"> </td>


				 								</tr>
				 								<tr class='no_border'>
				 									<td class="alignment_css" width="100"> </td>

				 								</tr>
				 								<?
				 								foreach($cutting_qc_reject_type as $cut_key=>$cut_val)
				 								{
				 									?>
				 									<tr class='no_border'>
				 									<td class="alignment_css" width="100"><? 
				 									$cut_def=$def_arr[$style_id][$buyer_id][$po_id][1][3][$cut_key];
				 									$cut_def=($cut_def/$defect)*100;
				 									$cut_def=number_format($cut_def,2);
				 									if($cut_def) 	echo $cut_def; else echo 0;
				 									?></td>

				 									</tr>

				 									<?
				 								}
				 								if($diff_cut)
				 								{
				 									for($ii=1;$ii<=$diff_cut;$ii++)
				 									{
				 										?>
				 										<tr class='no_border'>
				 										<td class="alignment_css" width="100">&nbsp; </td>

				 										</tr>

				 									<?

				 									}
				 								}
				 								?>
				 							</table>
				 						</td>

				 						<td class="alignment_css"  width="200" >
				 							<table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" class="table_sub">
				 								<tr class="no_border">
				 									<td class="alignment_css" width="200">Total Defected pannels</td>

				 									
				 								</tr>
				 								<tr class='no_border'>
				 									<td class="alignment_css" width="200">Total Gmts Reject</td>


				 								</tr>

				 								<tr class='no_border'>
				 									<td class="alignment_css" width="200">Reject% To Total QC</td>


				 								</tr>


				 								<tr class='no_border'>
				 									<td class="alignment_css" width="200">Total Gmts Replace</td>


				 								</tr>

				 								<tr class='no_border'>
				 									<td class="alignment_css" width="200">Total Qc Pass Qty</td>


				 								</tr>

				 								<tr class='no_border'>
				 									<td class="alignment_css" width="200" title="( Qc Pass + Alter+ Reject + Spot)">Total Check Qty</td>


				 								</tr>
				 								<tr class='no_border'>
				 									<td class="alignment_css" width="200">DHU%</td>

				 								</tr>
				 								<?
				 								foreach($sew_fin_alter_defect_type as $cut_key=>$cut_val)
				 								{
				 									?>
				 									<tr class='no_border'>
				 									<td class="alignment_css" width="200"><? echo $cut_val;?></td>

				 									</tr>

				 									<?
				 								}

				 								foreach($sew_fin_spot_defect_type as $cut_key=>$cut_val)
				 								{
				 									?>
				 									<tr class='no_border'>
				 									<td class="alignment_css" width="200"><? echo $cut_val;?></td>

				 									</tr>

				 									<?
				 								}

				 								if($diff_sew)
				 								{
				 									for($pp=1;$pp<=$diff_sew;$pp++)
				 									{
				 										?>
				 										<tr class='no_border'>
				 										<td class="alignment_css" width="200">&nbsp; </td>

				 										</tr>

				 									<?

				 									}
				 								}



				 								?>
				 							</table>
				 						</td>

				 						 <td class="alignment_css"  width="100" >
				 							<table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" class="table_sub">
				 								<tr class="">
				 									<td class="alignment_css" width="100"> <? 
				 									$chk=$qnty_arr[$style_id][$buyer_id][$po_id][5]["chk"];
				 									$defect_sew=$def_arr2[$style_id][$buyer_id][$po_id][5][3]+$def_arr2[$style_id][$buyer_id][$po_id][5][4];
				 									$pass=$qnty_arr[$style_id][$buyer_id][$po_id][5]["pass"];
				 									if($defect_sew)echo $defect_sew;else echo 0;
				 									?></td>

				 									
				 								</tr>
				 								<tr class=''>
				 									<td class="alignment_css" width="100">
				 										<? 
				 									$reject=$qnty_arr[$style_id][$buyer_id][$po_id][5]["reject"];
				 									if($reject)echo $reject;else echo 0;
				 									?>
				 									</td>


				 								</tr>

				 								<tr class=''>
				 									<td class="alignment_css" width="100">
				 									<? 
				 									$pec2=($reject/$pass)*100;
				 									if($pec2)echo number_format($pec2,2);else echo 0;
				 									?></td>


				 								</tr>


				 								<tr class=''>
				 									<td class="alignment_css" width="100">
				 									<? 
				 									$replace=$qnty_arr[$style_id][$buyer_id][$po_id][5]["replace"];
				 									if($replace)echo $replace;else echo 0;
				 									?>
				 										
				 									</td>


				 								</tr>

				 								<tr class=''>
				 									<td class="alignment_css" width="100">
				 									<? 
				 									
				 									if($pass)echo $pass;else echo 0;
				 									?>
				 										
				 									</td>


				 								</tr>

				 								<tr class=''>
				 									<td class="alignment_css" width="100">
				 									<? 
				 									
				 									if($chk)echo $chk;else echo 0;
				 									?>
				 										
				 									</td>


				 								</tr>
				 								<tr class=''>
				 									<td class="alignment_css" width="100"><? 
				 									$dhu=($defect_sew/$chk)*100;
				 									$dhu=number_format($dhu,2);
				 									if($dhu)echo $dhu." %";else echo  "0%";
				 									?></td>

				 								</tr>
				 								<?
				 								foreach($sew_fin_alter_defect_type as $alt_key=>$cut_val)
				 								{
				 									?>
				 									<tr class=''>
				 									<td class="alignment_css" width="100"><? 
				 									$alt_def=$def_arr[$style_id][$buyer_id][$po_id][5][3][$alt_key];
				 									if($alt_def) 	echo $alt_def; else echo 0;
				 									?></td>

				 									</tr>

				 									<?
				 								}
				 								foreach($sew_fin_spot_defect_type as $spot_key=>$cut_val)
				 								{
				 									?>
				 									<tr class=''>
				 									<td class="alignment_css" width="200"><? 
				 									$spot_def=$def_arr[$style_id][$buyer_id][$po_id][5][4][$spot_key];
				 									if($spot_def) 	echo $spot_def; else echo 0;
				 									?></td>

				 									</tr>

				 									<?
				 								}
				 								if($diff_sew)
				 								{
				 									for($pp=1;$pp<=$diff_sew;$pp++)
				 									{
				 										?>
				 										<tr class='no_border'>
				 										<td class="alignment_css" width="100">&nbsp; </td>

				 										</tr>

				 									<?

				 									}
				 								}
				 								?>
				 							</table>
				 						</td>


				 						 <td class="alignment_css"  width="100" >
				 							<table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" class="table_sub">
				 								<tr class="no_border">
				 									<td class="alignment_css" width="100"> </td>

				 									
				 								</tr>
				 								<tr class='no_border'>
				 									<td class="alignment_css" width="100"> </td>


				 								</tr>

				 								<tr class='no_border'>
				 									<td class="alignment_css" width="100"> </td>


				 								</tr>


				 								<tr class='no_border'>
				 									<td class="alignment_css" width="100"></td>


				 								</tr>

				 								<tr class='no_border'>
				 									<td class="alignment_css" width="100"> </td>


				 								</tr>

				 								<tr class='no_border'>
				 									<td class="alignment_css" width="100"> </td>


				 								</tr>
				 								<tr class='no_border'>
				 									<td class="alignment_css" width="100"> </td>

				 								</tr>
				 								<?
				 								foreach($sew_fin_alter_defect_type as $alt_key=>$cut_val)
				 								{
				 									?>
				 									<tr class='no_border'>
				 									<td class="alignment_css" width="100"><? 
				 									$alt_def=$def_arr[$style_id][$buyer_id][$po_id][5][3][$alt_key];
				 									$alt_def=($alt_def/$defect_sew)*100;
				 									$alt_def=number_format($alt_def,2);
				 									if($alt_def) 	echo $alt_def; else echo 0;
				 									?></td>

				 									</tr>

				 									<?
				 								}

				 								foreach($sew_fin_spot_defect_type as $spot_key=>$cut_val)
				 								{
				 									?>
				 									<tr class=' '>
				 									<td class="alignment_css" width="200"><? 
				 									$spot_def=$def_arr[$style_id][$buyer_id][$po_id][5][4][$spot_key];
				 									$spot_def=($spot_def/$defect_sew)*100;
				 									$spot_def=number_format($spot_def,2);
				 									if($spot_def) 	echo $spot_def; else echo 0;
				 									?></td>

				 									</tr>

				 									<?
				 								}
				 								if($diff_sew)
				 								{
				 									for($pp=1;$pp<=$diff_sew;$pp++)
				 									{
				 										?>
				 										<tr class=' '>
				 										<td class="alignment_css" width="100">&nbsp; </td>

				 										</tr>

				 									<?

				 									}
				 								}

				 								?>
				 							</table>
				 						</td>

				 						 
				 						 
				 						 
				 						
				 					</tr>

				 					<?
				 					$i++;
				 				}
				 			}

				 		}


				 		?>
			 		</tbody>
		 		</table>
		 	</div>


		 </div>
		 <script type="text/javascript">
		 	
		 	$('#main_div td').each(function() { 
		 		if(trim(this.textContent)=="nan"  || this.textContent=='nan %'   )this.textContent=0;
		 		 
		 	});
		 </script>


		 <?



	}
	else if($report_type==2) 
	{
		$sql = "SELECT a.job_no, a.id as style_id, a.client_id, b.id, d.production_type, b.po_number,c.color_number_id , a.buyer_name,a.season_buyer_wise, a.style_ref_no, sum(c.order_quantity) as po_quantity, sum(e.production_qnty) as qc_pass_qty, sum(e.alter_qty) as alter_qnty,  sum(case when is_rescan=0 then  e.reject_qty else 0 end )  as reject_qnty,  sum(case when e.production_type=1 then  e.reject_qty else 0 end )  as reject_qnty_cut, sum(e.spot_qty) as spot_qnty, sum(e.replace_qty) as replace_qty,sum(case when is_rescan=1 then e.production_qnty else 0 end ) as today_rescan FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.po_break_down_id=d.po_break_down_id and d.id=e.mst_id and c.id= e.color_size_break_down_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $all_cond and d.production_type in(1,5) group by  a.job_no,a.id,a.client_id,b.id,d.production_type,b.po_number,c.color_number_id , a.buyer_name,a.season_buyer_wise, a.style_ref_no  ";


		$all_po_arr=array();

		foreach(sql_select($sql) as $val)
		{
			$all_po_arr[$val[csf("id")]]=$val[csf("id")];
			$main_array[$val[csf("style_id")]][$val[csf("buyer_name")]][$val[csf("color_number_id")]]["production_type"]=$val[csf("production_type")];
			$main_array[$val[csf("style_id")]][$val[csf("buyer_name")]][$val[csf("color_number_id")]]["style_ref_no"]=$val[csf("style_ref_no")];
			$main_array[$val[csf("style_id")]][$val[csf("buyer_name")]][$val[csf("color_number_id")]]["job_no"]=$val[csf("job_no")];
			$main_array[$val[csf("style_id")]][$val[csf("buyer_name")]][$val[csf("color_number_id")]]["colors"]=$color_arr[$val[csf("color_number_id")]];
			$main_array[$val[csf("style_id")]][$val[csf("buyer_name")]][$val[csf("color_number_id")]]["po_quantity"]=$val[csf("po_quantity")];
			$qnty_arr[$val[csf("style_id")]][$val[csf("buyer_name")]][$val[csf("color_number_id")]][$val[csf("production_type")]]["chk"]+=$val[csf("qc_pass_qty")]+$val[csf("alter_qnty")]+$val[csf("reject_qnty")]+$val[csf("spot_qnty")] ;

			$qnty_arr[$val[csf("style_id")]][$val[csf("buyer_name")]][$val[csf("color_number_id")]][$val[csf("production_type")]]["reject"]+=$val[csf("reject_qnty")] ;
			$qnty_arr[$val[csf("style_id")]][$val[csf("buyer_name")]][$val[csf("color_number_id")]][$val[csf("production_type")]]["reject_cut"]+=$val[csf("reject_qnty_cut")] ;
			$qnty_arr[$val[csf("style_id")]][$val[csf("buyer_name")]][$val[csf("color_number_id")]][$val[csf("production_type")]]["pass"]+=$val[csf("qc_pass_qty")] ;

			$qnty_arr[$val[csf("style_id")]][$val[csf("buyer_name")]][$val[csf("color_number_id")]][$val[csf("production_type")]]["replace"]+=$val[csf("replace_qty")] ;

			$main_array[$val[csf("style_id")]][$val[csf("buyer_name")]][$val[csf("color_number_id")]]["buyer"]=$buyer_arr[$val[csf("buyer_name")]];
			$main_array[$val[csf("style_id")]][$val[csf("buyer_name")]][$val[csf("color_number_id")]]["client"]=$client_arr[$val[csf("client_id")]];
			$main_array[$val[csf("style_id")]][$val[csf("buyer_name")]][$val[csf("color_number_id")]]["po_number"].=','.$val[csf("po_number")];
			$main_array[$val[csf("style_id")]][$val[csf("buyer_name")]][$val[csf("color_number_id")]]["season_buyer_wise"]=$season_arr [$val[csf("season_buyer_wise")]];


		}
 
		$po_ids=implode(",",$all_po_arr);
		foreach(sql_select("SELECT job_no_mst,color_number_id,sum(order_quantity) as qnty from wo_po_color_size_breakdown where status_active=1 and po_break_down_id in($po_ids)   group by job_no_mst,color_number_id ") as $values  )
		{
			$po_color_wise_qnty[$values[csf("job_no_mst")]][$values[csf("color_number_id")]]=$values[csf("qnty")];
		}

		$sql_def = "SELECT a.id as style_id, b.id, d.production_type, b.po_number,c.color_number_id , a.buyer_name,a.season_buyer_wise, a.style_ref_no, f.defect_type_id, f.defect_point_id, sum(f.defect_qty ) as defect_qty  FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,pro_garments_production_mst d,pro_garments_production_dtls e,pro_gmts_prod_dft f  WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.po_break_down_id=d.po_break_down_id and d.id=e.mst_id and c.id= e.color_size_break_down_id and e.barcode_no=f.bundle_no and d.id=f.mst_id  and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $all_cond and d.production_type in(1,5) group by  a.id ,b.id, d.production_type, b.po_number,c.color_number_id , a.buyer_name,a.season_buyer_wise, a.style_ref_no, f.defect_type_id, f.defect_point_id   ";

		foreach(sql_select($sql_def) as $val)
		{
			$def_arr[$val[csf("style_id")]][$val[csf("buyer_name")]][$val[csf("color_number_id")]][$val[csf("production_type")]][$val[csf("defect_type_id")]][$val[csf("defect_point_id")]] +=$val[csf("defect_qty")];
			$qnty_arr[$val[csf("style_id")]][$val[csf("buyer_name")]][$val[csf("color_number_id")]][$val[csf("production_type")]]["defect"]+=$val[csf("defect_qty")] ;
			$def_arr2[$val[csf("style_id")]][$val[csf("buyer_name")]][$val[csf("color_number_id")]][$val[csf("production_type")]][$val[csf("defect_type_id")]]  +=$val[csf("defect_qty")];

		}


		ob_start();
		?>
		 
		<div id="main_div">
			 <style type="text/css">
			 	.alignment_css
			 	{
			 		word-break: break-all;
			 		word-wrap: break-word;
			 	}
			 	.table_sub table{
			 		 
			 	}
			 	.no_border tr ,td
			 	{
			 		 
	  				   
	 
			 	}
			 </style>
			 <table width="1600" cellspacing="0" >
			 	<tr class="form_caption" style="border:none;">
			 		<td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >
			 			<strong>Style wise DHU % Report	</strong>
			 		</td>
			 	</tr>
			 	<tr class="form_caption" style="border:none;">
			 		<td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >
			 			<strong><? echo $company_library[$cbo_company_name];?></strong>
			 		</td>
			 	</tr>
			 	<tr class="form_caption" style="border:none;">
			 		<td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >
			 			<strong> </strong>
			 		</td>
			 	</tr>
			 </table>

			 <table cellspacing="0" cellpadding="0"  border="1" rules="all"  width="1540" class="rpt_table" align="left">
			 	<thead>
			 		<tr>
			 			<th class="alignment_css" width="40">SL</th>
			 			<th class="alignment_css" width="100">Buyer</th>
			 			<th class="alignment_css" width="100">Client</th>
			 			<th class="alignment_css" width="100">Style No</th>
			 			<th class="alignment_css" width="100">Season</th>
			 			<th class="alignment_css" width="100">Order No</th>
			 			<th class="alignment_css" width="100">Color</th>
			 			<th class="alignment_css" width="100">Order Qty</th>
			 			<th class="alignment_css" width="200">Cutting Defect Name</th>
			 			<th class="alignment_css" width="100">Cutting Defect<br> Qty</th>
			 			<th class="alignment_css" width="100">Defect % </th>
			 			<th class="alignment_css" width="200">Sewing  Defect Name</th>
			 			<th class="alignment_css" width="100">Sewing  Defect <br>Qty</th>
			 			<th class="alignment_css" width="100">Defect %</th>
			 		</tr>
			 	</thead>
			 </table>
		 	<div style="max-height:425px; overflow-y:scroll; width:1560px;" id="scroll_body">
		 		<table cellpadding="0" cellspacing="0"  align="left" border="1" class="rpt_table"  width="1540" rules="all" id="table_body" >
		 			<tbody>
		 				<?

		 				$cutting_def_count=count($cutting_qc_reject_type);
		 				$sewing_def_count=count($sew_fin_alter_defect_type)+count($sew_fin_spot_defect_type);
		 				$diff_cut=0; 
		 				if($sewing_def_count>$cutting_def_count)
		 					$diff_cut=abs($sewing_def_count-$cutting_def_count);

		 				$diff_sew=0; 
		 				if($sewing_def_count<$cutting_def_count)
		 					$diff_sew=abs($cutting_def_count-$sewing_def_count );

		 				$span=($cutting_def_count>$sewing_def_count)?$cutting_def_count : $sewing_def_count;
		 				$span=$span+7;
		 				$i=1;
		 				foreach($main_array as $style_id=>$buyer_data )
		 				{
		 					foreach($buyer_data as $buyer_id=>$color_data )
		 					{
		 						//foreach($po_data as $po_id=>$color_data )
		 						//{
		 							foreach($color_data as $color_id=>$rows )
		 							{




		 								if ($i%2==0)
		 									$bgcolor="#E9F3FF";
		 								else
		 									$bgcolor="#FFFFFF";
		 								$job_no=$rows["job_no"];
		 								
		 								$po_qty=$po_color_wise_qnty[$job_no][$color_id];
		 								//$style_id=$rows["style_ref_no"];
		 								

		 								?>
		 								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
		 									<td  valign="top" class="alignment_css" rowspan="<? echo $rowspan;?>" width="40" align="center"><? echo $i; ?></td>
		 									<td  valign="top"  class="alignment_css" rowspan="<? echo $rowspan;?>"  width="100" align="center"><? echo $rows["buyer"]; ?></td>
		 									<td  valign="top"  class="alignment_css"  rowspan="<? echo $rowspan;?>" width="100" align="center"><? echo $rows["client"]; ?></td>
		 									<td  valign="top"  class="alignment_css" rowspan="<? echo $rowspan;?>"  width="100" align="center"><? echo $rows["style_ref_no"]; ?></td>
		 									<td  valign="top"  class="alignment_css" rowspan="<? echo $rowspan;?>"  width="100" align="center"><? echo $rows["season_buyer_wise"]; ?></td>
		 									<td  valign="top"  class="alignment_css" rowspan="<? echo $rowspan;?>"  width="100" align="center"><? echo implode(",", array_unique(explode(",", trim($rows["po_number"],",")))); ?></td>
		 									<td  valign="top"  class="alignment_css" rowspan="<? echo $rowspan;?>" width="100" align="center"><? echo implode(",", array_unique(explode(",", $rows["colors"]))); ?></td>
		 									<td  valign="top"  class="alignment_css"  rowspan="<? echo $rowspan;?>" width="100" align="center"><? echo $po_qty; ?></td>

		 									<td class="alignment_css"  width="200" >
		 										<table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" class="table_sub">
		 											<tr class="no_border">
		 												<td class="alignment_css" width="200">Total Defected pannels</td>


		 											</tr>
		 											<tr class='no_border'>
		 												<td class="alignment_css" width="200">Total Gmts Reject</td>


		 											</tr>

		 											<tr class='no_border'>
		 												<td class="alignment_css" width="200">Reject% To Total QC</td>


		 											</tr>


		 											<tr class='no_border'>
		 												<td class="alignment_css" width="200">Total Gmts Replace</td>


		 											</tr>

		 											<tr class='no_border'>
		 												<td class="alignment_css" width="200">Total Qc Pass Qty</td>


		 											</tr>

		 											<tr class='no_border'>
		 												<td class="alignment_css" width="200" title="( Qc Pass + Alter+ Reject + Spot)">Total Check Qty</td>


		 											</tr>
		 											<tr class='no_border'>
		 												<td class="alignment_css" width="200">DHU%</td>

		 											</tr>
		 											<?
		 											foreach($cutting_qc_reject_type as $cut_key=>$cut_val)
		 											{
		 												?>
		 												<tr class=' '>
		 													<td class="alignment_css" width="200"><? echo $cut_val;?></td>

		 												</tr>

		 												<?
		 											}
		 											if($diff_cut)
		 											{
		 												for($ii=1;$ii<=$diff_cut;$ii++)
		 												{
		 													?>
		 													<tr class='no_border'>
		 														<td class="alignment_css" width="200">&nbsp; </td>

		 													</tr>

		 													<?

		 												}
		 											}
		 											?>
		 										</table>
		 									</td>

		 									<td class="alignment_css"  width="100" >
		 										<table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" class="table_sub">
		 											<tr class="">
		 												<td class="alignment_css" width="100"> <? 
		 													$chk=$qnty_arr[$style_id][$buyer_id][$color_id][1]["chk"];
		 													$defect=$def_arr2[$style_id][$buyer_id][$color_id][1][3];
		 													$pass=$qnty_arr[$style_id][$buyer_id][$color_id][1]["pass"];
		 													if($defect)echo $defect;else echo 0;
		 													?></td>


		 												</tr>
		 												<tr class=''>
		 													<td class="alignment_css" width="100">
		 														<? 

		 														$reject=$qnty_arr[$style_id][$buyer_id][$color_id][1]["reject_cut"];
		 														if($reject)echo $reject;else echo 0;
		 														?>
		 													</td>


		 												</tr>

		 												<tr class=''>
		 													<td class="alignment_css" width="100">
		 														<? 
		 														$pec=($reject/$pass)*100;
		 														if($pec)echo number_format($pec,2);else echo 0;
		 														?></td>


		 													</tr>


		 													<tr class=''>
		 														<td class="alignment_css" width="100">
		 															<? 
		 															$replace=$qnty_arr[$style_id][$buyer_id][$color_id][1]["replace"];
		 															if($replace)echo $replace;else echo 0;
		 															?>

		 														</td>


		 													</tr>

		 													<tr class=''>
		 														<td class="alignment_css" width="100">
		 															<? 
		 															
		 															if($pass)echo $pass;else echo 0;

		 															?>

		 														</td>


		 													</tr>

		 													<tr class=''>
		 														<td class="alignment_css" width="100">
		 															<? 

		 															if($chk)echo $chk;else echo 0;
		 															?>

		 														</td>


		 													</tr>
		 													<tr class=''>
		 														<td class="alignment_css" width="100"><? 
		 															$dhu=($defect/$chk)*100;
		 															$dhu=number_format($dhu,2);
		 															if($dhu)echo $dhu." %";else echo  "0%";
		 															?></td>

		 														</tr>
		 														<?
		 														foreach($cutting_qc_reject_type as $cut_key=>$cut_val)
		 														{
		 															?>
		 															<tr class=''>
		 																<td class="alignment_css" width="100">
		 																	<? 
		 																	$cut_def=$def_arr[$style_id][$buyer_id][$color_id][1][3][$cut_key];
		 																	if($cut_def) 	echo $cut_def; else echo 0;
		 																	?></td>

		 																</tr>

		 																<?
		 															}
		 															if($diff_cut)
		 															{
		 																for($ii=1;$ii<=$diff_cut;$ii++)
		 																{
		 																	?>
		 																	<tr class='no_border'>
		 																		<td class="alignment_css" width="100">&nbsp; </td>

		 																	</tr>

		 																	<?

		 																}
		 															}

		 															?>
		 														</table>
		 													</td>


		 													<td class="alignment_css"  width="100" >
		 														<table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" class="table_sub">
		 															<tr class="no_border">
		 																<td class="alignment_css" width="100"> </td>


		 															</tr>
		 															<tr class='no_border'>
		 																<td class="alignment_css" width="100"> </td>


		 															</tr>

		 															<tr class='no_border'>
		 																<td class="alignment_css" width="100"> </td>


		 															</tr>


		 															<tr class='no_border'>
		 																<td class="alignment_css" width="100"> </td>


		 															</tr>

		 															<tr class='no_border'>
		 																<td class="alignment_css" width="100"> </td>


		 															</tr>

		 															<tr class='no_border'>
		 																<td class="alignment_css" width="100"> </td>


		 															</tr>
		 															<tr class='no_border'>
		 																<td class="alignment_css" width="100"> </td>

		 															</tr>
		 															<?
		 															foreach($cutting_qc_reject_type as $cut_key=>$cut_val)
		 															{
		 																?>
		 																<tr class='no_border'>
		 																	<td class="alignment_css" width="100"><? 
		 																		$cut_def=$def_arr[$style_id][$buyer_id][$color_id][1][3][$cut_key];
		 																		$cut_def=($cut_def/$defect)*100;
		 																		$cut_def=number_format($cut_def,2);
		 																		if($cut_def) 	echo $cut_def; else echo 0;
		 																		?></td>

		 																	</tr>

		 																	<?
		 																}
		 																if($diff_cut)
		 																{
		 																	for($ii=1;$ii<=$diff_cut;$ii++)
		 																	{
		 																		?>
		 																		<tr class='no_border'>
		 																			<td class="alignment_css" width="100">&nbsp; </td>

		 																		</tr>

		 																		<?

		 																	}
		 																}
		 																?>
		 															</table>
		 														</td>

		 														<td class="alignment_css"  width="200" >
		 															<table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" class="table_sub">
		 																<tr class="no_border">
		 																	<td class="alignment_css" width="200">Total Defected pannels</td>


		 																</tr>
		 																<tr class='no_border'>
		 																	<td class="alignment_css" width="200">Total Gmts Reject</td>


		 																</tr>

		 																<tr class='no_border'>
		 																	<td class="alignment_css" width="200">Reject% To Total QC</td>


		 																</tr>


		 																<tr class='no_border'>
		 																	<td class="alignment_css" width="200">Total Gmts Replace</td>


		 																</tr>

		 																<tr class='no_border'>
		 																	<td class="alignment_css" width="200">Total Qc Pass Qty</td>


		 																</tr>

		 																<tr class='no_border'>
		 																	<td class="alignment_css" width="200" title="( Qc Pass + Alter+ Reject + Spot)">Total Check Qty</td>


		 																</tr>
		 																<tr class='no_border'>
		 																	<td class="alignment_css" width="200">DHU%</td>

		 																</tr>
		 																<?
		 																foreach($sew_fin_alter_defect_type as $cut_key=>$cut_val)
		 																{
		 																	?>
		 																	<tr class='no_border'>
		 																		<td class="alignment_css" width="200"><? echo $cut_val;?></td>

		 																	</tr>

		 																	<?
		 																}

		 																foreach($sew_fin_spot_defect_type as $cut_key=>$cut_val)
		 																{
		 																	?>
		 																	<tr class='no_border'>
		 																		<td class="alignment_css" width="200"><? echo $cut_val;?></td>

		 																	</tr>

		 																	<?
		 																}

		 																if($diff_sew)
		 																{
		 																	for($pp=1;$pp<=$diff_sew;$pp++)
		 																	{
		 																		?>
		 																		<tr class='no_border'>
		 																			<td class="alignment_css" width="200">&nbsp; </td>

		 																		</tr>

		 																		<?

		 																	}
		 																}



		 																?>
		 															</table>
		 														</td>

		 														<td class="alignment_css"  width="100" >
		 															<table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" class="table_sub">
		 																<tr class="">
		 																	<td class="alignment_css" width="100"> <?
		 																		$pass=$qnty_arr[$style_id][$buyer_id][$color_id][5]["pass"]; 
		 																		$chk=$qnty_arr[$style_id][$buyer_id][$color_id][5]["chk"];
		 																		$defect_sew=$def_arr2[$style_id][$buyer_id][$color_id][5][3]+$def_arr2[$style_id][$buyer_id][$color_id][5][4];
		 																		if($defect_sew)echo $defect_sew;else echo 0;
		 																		?></td>


		 																	</tr>
		 																	<tr class=''>
		 																		<td class="alignment_css" width="100">
		 																			<? 
		 																			$reject=$qnty_arr[$style_id][$buyer_id][$color_id][5]["reject"];
		 																			if($reject)echo $reject;else echo 0;
		 																			?>
		 																		</td>


		 																	</tr>

		 																	<tr class=''>
		 																		<td class="alignment_css" width="100">
		 																			<? 
		 																			$pec2=($reject/$pass)*100;
		 																			if($pec2)echo number_format($pec2,2);else echo 0;
		 																			?></td>


		 																		</tr>


		 																		<tr class=''>
		 																			<td class="alignment_css" width="100">
		 																				<? 
		 																				$replace=$qnty_arr[$style_id][$buyer_id][$color_id][5]["replace"];
		 																				if($replace)echo $replace;else echo 0;
		 																				?>

		 																			</td>


		 																		</tr>

		 																		<tr class=''>
		 																			<td class="alignment_css" width="100">
		 																				<? 
		 																				
		 																				if($pass)echo $pass;else echo 0;
		 																				?>

		 																			</td>


		 																		</tr>

		 																		<tr class=''>
		 																			<td class="alignment_css" width="100">
		 																				<? 

		 																				if($chk)echo $chk;else echo 0;
		 																				?>

		 																			</td>


		 																		</tr>
		 																		<tr class=''>
		 																			<td class="alignment_css" width="100"><? 
		 																				$dhu=($defect_sew/$chk)*100;
		 																				$dhu=number_format($dhu,2);
		 																				if($dhu)echo $dhu." %";else echo  "0%";
		 																				?></td>

		 																			</tr>
		 																			<?
		 																			foreach($sew_fin_alter_defect_type as $alt_key=>$cut_val)
		 																			{
		 																				?>
		 																				<tr class=''>
		 																					<td class="alignment_css" width="100"><? 
		 																						$alt_def=$def_arr[$style_id][$buyer_id][$color_id][5][3][$alt_key];
		 																						if($alt_def) 	echo $alt_def; else echo 0;
		 																						?></td>

		 																					</tr>

		 																					<?
		 																				}
		 																				foreach($sew_fin_spot_defect_type as $spot_key=>$cut_val)
		 																				{
		 																					?>
		 																					<tr class=''>
		 																						<td class="alignment_css" width="200"><? 
		 																							$spot_def=$def_arr[$style_id][$buyer_id][$color_id][5][4][$spot_key];
		 																							if($spot_def) 	echo $spot_def; else echo 0;
		 																							?></td>

		 																						</tr>

		 																						<?
		 																					}
		 																					if($diff_sew)
		 																					{
		 																						for($pp=1;$pp<=$diff_sew;$pp++)
		 																						{
		 																							?>
		 																							<tr class='no_border'>
		 																								<td class="alignment_css" width="100">&nbsp; </td>

		 																							</tr>

		 																							<?

		 																						}
		 																					}
		 																					?>
		 																				</table>
		 																			</td>


		 																			<td class="alignment_css"  width="100" >
		 																				<table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" class="table_sub">
		 																					<tr class="no_border">
		 																						<td class="alignment_css" width="100"> </td>


		 																					</tr>
		 																					<tr class='no_border'>
		 																						<td class="alignment_css" width="100"> </td>


		 																					</tr>

		 																					<tr class='no_border'>
		 																						<td class="alignment_css" width="100"> </td>


		 																					</tr>


		 																					<tr class='no_border'>
		 																						<td class="alignment_css" width="100"></td>


		 																					</tr>

		 																					<tr class='no_border'>
		 																						<td class="alignment_css" width="100"> </td>


		 																					</tr>

		 																					<tr class='no_border'>
		 																						<td class="alignment_css" width="100"> </td>


		 																					</tr>
		 																					<tr class='no_border'>
		 																						<td class="alignment_css" width="100"> </td>

		 																					</tr>
		 																					<?
		 																					foreach($sew_fin_alter_defect_type as $alt_key=>$cut_val)
		 																					{
		 																						?>
		 																						<tr class='no_border'>
		 																							<td class="alignment_css" width="100"><? 
		 																								$alt_def=$def_arr[$style_id][$buyer_id][$color_id][5][3][$alt_key];
		 																								$alt_def=($alt_def/$defect_sew)*100;
		 																								$alt_def=number_format($alt_def,2);
		 																								if($alt_def) 	echo $alt_def; else echo 0;
		 																								?></td>

		 																							</tr>

		 																							<?
		 																						}

		 																						foreach($sew_fin_spot_defect_type as $spot_key=>$cut_val)
		 																						{
		 																							?>
		 																							<tr class=' '>
		 																								<td class="alignment_css" width="200"><? 
		 																									$spot_def=$def_arr[$style_id][$buyer_id][$color_id][5][4][$spot_key];
		 																									$spot_def=($spot_def/$defect_sew)*100;
		 																									$spot_def=number_format($spot_def,2);
		 																									if($spot_def) 	echo $spot_def; else echo 0;
		 																									?></td>

		 																								</tr>

		 																								<?
		 																							}
		 																							if($diff_sew)
		 																							{
		 																								for($pp=1;$pp<=$diff_sew;$pp++)
		 																								{
		 																									?>
		 																									<tr class=' '>
		 																										<td class="alignment_css" width="100">&nbsp; </td>

		 																									</tr>

		 																									<?

		 																								}
		 																							}

		 																							?>
		 																						</table>
		 																					</td>





		 																				</tr>

		 																				<?
		 																				$i++;
		 																			}
		 																		//}
		 																	}

		 																}


		 																?>
		 															</tbody>
		 		</table>
		 	</div>


		 </div>
		 <script type="text/javascript">
		 	
		 	$('#main_div td').each(function() { 
		 		if(trim(this.textContent)=="nan"  || this.textContent=='nan %'   )this.textContent=0;
		 		 
		 	});
		 </script>


		 <?



	}
	else if($report_type==3) 
	{
		if($cbo_production_type==1)
		{
			// $sql = "SELECT a.client_id, b.id, d.production_type, b.po_number,c.color_number_id , a.buyer_name,a.season_buyer_wise, a.style_ref_no, sum(c.order_quantity) as po_quantity, sum(e.production_qnty) as qc_pass_qty, sum(e.alter_qty) as alter_qnty,  sum(case when is_rescan=0 then  e.reject_qty else 0 end )  as reject_qnty,  sum(case when e.production_type=1 then  e.reject_qty else 0 end )  as reject_qnty_cut, sum(e.spot_qty) as spot_qnty, sum(e.replace_qty) as replace_qty,sum(case when is_rescan=1 then e.production_qnty else 0 end ) as today_rescan FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.po_break_down_id=d.po_break_down_id and d.id=e.mst_id and c.id= e.color_size_break_down_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $all_cond and d.production_type in(1,5) group by a.client_id,b.id, d.production_type,b.po_number,c.color_number_id , a.buyer_name,a.season_buyer_wise, a.style_ref_no  ";
			$sql = "SELECT a.client_id, b.id, b.grouping,d.floor_id, d.production_type, b.po_number,c.color_number_id , a.buyer_name,a.season_buyer_wise, a.style_ref_no, sum(c.order_quantity) as po_quantity, sum(e.production_qnty) as qc_pass_qty, sum(e.alter_qty) as alter_qnty,  sum(case when is_rescan=0 then  e.reject_qty else 0 end )  as reject_qnty,  sum(case when e.production_type=1 then  e.reject_qty else 0 end )  as reject_qnty_cut, sum(e.spot_qty) as spot_qnty, sum(e.replace_qty) as replace_qty,sum(case when is_rescan=1 then e.production_qnty else 0 end ) as today_rescan FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.po_break_down_id=d.po_break_down_id and d.id=e.mst_id and c.id= e.color_size_break_down_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $all_cond and d.production_type in(1,5) group by a.client_id,b.id, b.grouping,d.floor_id, d.production_type,b.po_number,c.color_number_id , a.buyer_name,a.season_buyer_wise, a.style_ref_no  ";
			// echo $sql;
 
		  /*$sql_def = "SELECT b.id, d.production_type, b.po_number,c.color_number_id , a.buyer_name,a.season_buyer_wise, a.style_ref_no, f.defect_type_id, f.defect_point_id, f.defect_qty   FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,pro_garments_production_mst d,pro_garments_production_dtls e,pro_gmts_prod_dft f  WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.po_break_down_id=d.po_break_down_id and d.id=e.mst_id and c.id= e.color_size_break_down_id and e.barcode_no=f.bundle_no and d.id=f.mst_id  and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $all_cond and d.production_type in(1,5) group by b.id, d.production_type, b.po_number,c.color_number_id , a.buyer_name,a.season_buyer_wise, a.style_ref_no, f.defect_type_id, f.defect_point_id, f.defect_qty  ";*/
		  $all_po_arr=array();
		  
		  foreach(sql_select($sql) as $val)
		  {
			  $all_po_arr[$val[csf("id")]]=$val[csf("id")];
			  $main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["production_type"]=$val[csf("production_type")];
			  $main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["color_number_id"].=','.$val[csf("color_number_id")];
			  $main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["po_quantity"]=$val[csf("po_quantity")];
			  $qnty_arr[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]][$val[csf("production_type")]]["chk"]+=$val[csf("qc_pass_qty")]+$val[csf("alter_qnty")]+$val[csf("reject_qnty")]+$val[csf("spot_qnty")] ;
 
			  $qnty_arr[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]][$val[csf("production_type")]]["reject"]+=$val[csf("reject_qnty")] ;
			  $qnty_arr[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]][$val[csf("production_type")]]["reject_cut"]+=$val[csf("reject_qnty_cut")] ;
			  $qnty_arr[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]][$val[csf("production_type")]]["pass"]+=$val[csf("qc_pass_qty")] ;
 
			  $qnty_arr[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]][$val[csf("production_type")]]["replace"]+=$val[csf("replace_qty")] ;
 
			  $main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["buyer"]=$buyer_arr[$val[csf("buyer_name")]];
			  $main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["client"]=$client_arr[$val[csf("client_id")]];
			  $main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["po_number"]=$val[csf("po_number")];
			  $main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["internal_ref"]=$val[csf("grouping")];
			  $main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["floor_id"]=$val[csf("floor_id")];
			  $main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["season_buyer_wise"]=$season_arr [$val[csf("season_buyer_wise")]];
			  if($main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["colors"]=="")
				  $main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["colors"].=$color_arr[$val[csf("color_number_id")]];
			  else
				  $main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["colors"].=','.$color_arr[$val[csf("color_number_id")]];
 
		  }
		  //echo "<pre>";print_r($qnty_arr);die;
		  $po_ids=implode(",",$all_po_arr);
		  foreach(sql_select("SELECT po_break_down_id,color_number_id,sum(order_quantity) as qnty from wo_po_color_size_breakdown where status_active=1 and po_break_down_id in($po_ids)   group by po_break_down_id,color_number_id ") as $values  )
		  {
			  $po_color_wise_qnty[$values[csf("po_break_down_id")]][$values[csf("color_number_id")]]=$values[csf("qnty")];
		  }
		  //print_r($po_color_wise_qnty);
			$sql_def = "SELECT b.id, d.production_type, b.po_number,c.color_number_id , a.buyer_name,a.season_buyer_wise, a.style_ref_no, f.defect_type_id, f.defect_point_id, sum(f.defect_qty ) as defect_qty  FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,pro_garments_production_mst d,pro_garments_production_dtls e,pro_gmts_prod_dft f  WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.po_break_down_id=d.po_break_down_id and d.id=e.mst_id and c.id= e.color_size_break_down_id and e.barcode_no=f.bundle_no and d.id=f.mst_id  and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $all_cond and d.production_type in(1,5) group by b.id, d.production_type, b.po_number,c.color_number_id , a.buyer_name,a.season_buyer_wise, a.style_ref_no, f.defect_type_id, f.defect_point_id   ";
		  
			foreach(sql_select($sql_def) as $val)
			{
				$def_arr[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]][$val[csf("production_type")]][$val[csf("defect_type_id")]][$val[csf("defect_point_id")]] +=$val[csf("defect_qty")];
				$qnty_arr[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]][$val[csf("production_type")]]["defect"]+=$val[csf("defect_qty")] ;
				$def_arr2[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]][$val[csf("production_type")]][$val[csf("defect_type_id")]]  +=$val[csf("defect_qty")];
 
			}
			// echo "<pre>";
			// print_r($def_arr2);
		  
 
			  ob_start();
			  ?>
		  
		  <div id="main_div">
		  <style type="text/css">
			  .alignment_css
			  {
				  word-break: break-all;
				  word-wrap: break-word;
			  }
			  .table_sub table{
				   
			  }
			  .no_border tr ,td
			  {
				   
					  
  
			  }
		  </style>
			  <table width="1400" cellspacing="0" >
				  <tr class="form_caption" style="border:none;">
					  <td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >
						  <strong>Style wise DHU % Report (Cutting)	</strong>
					  </td>
				  </tr>
				  <tr class="form_caption" style="border:none;">
					  <td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >
						  <strong><? echo $company_library[$cbo_company_name];?></strong>
					  </td>
				  </tr>
				  <tr class="form_caption" style="border:none;">
					  <td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >
						  <strong> </strong>
					  </td>
				  </tr>
			  </table>
 
			  <table cellspacing="0" cellpadding="0"  border="1" rules="all"  width="1340" class="rpt_table" align="left">
				  <thead>
					  <tr>
						  <th class="alignment_css" width="40">SL</th>
						  <th class="alignment_css" width="100">Floor Group</th>
						  <th class="alignment_css" width="100">Floor</th>
						  <th class="alignment_css" width="100">Buyer</th>
						  <th class="alignment_css" width="100">Style No</th>
						  <th class="alignment_css" width="100">Internal Ref.</th>
						  <th class="alignment_css" width="100">Season</th>
						  <th class="alignment_css" width="100">Order No</th>
						  <th class="alignment_css" width="100">Color</th>
						  <th class="alignment_css" width="100">Order Qty</th>
						  <th class="alignment_css" width="200"> Defect Name</th>
						  <th class="alignment_css" width="100"> Defect<br> Qty</th>
						  <th class="alignment_css" width="100">Defect % </th>
					  </tr>
				  </thead>
			  </table>
			  <div style="max-height:425px; overflow-y:scroll; width:1360px;" id="scroll_body">
				  <table cellpadding="0" cellspacing="0"  align="left" border="1" class="rpt_table"  width="1340" rules="all" id="table_body" >
					  <tbody>
						  <?
						  $sewing_def_count=count($sew_fin_alter_defect_type)+count($sew_fin_spot_defect_type);
						  $diff_cut=0; 
						  if($sewing_def_count>$cutting_def_count)
							  $diff_cut=abs($sewing_def_count-$cutting_def_count);
 
						  $diff_sew=0; 
						  if($sewing_def_count<$cutting_def_count)
							  $diff_sew=abs($cutting_def_count-$sewing_def_count );
 
						  $span=($cutting_def_count>$sewing_def_count)?$cutting_def_count : $sewing_def_count;
						  $span=$span+7;
						  $i=1;
						  foreach($main_array as $style_id=>$buyer_data )
						  {
							  foreach($buyer_data as $buyer_id=>$po_data )
							  {
								  foreach($po_data as $po_id=>$rows )
								  {
										
									  if ($i%2==0)
										  $bgcolor="#E9F3FF";
									  else
										  $bgcolor="#FFFFFF";
									  $po_qty=0;
									  $color_ids=array_unique(explode(",", trim($rows["color_number_id"],",")));
									  foreach($color_ids as $vv)
									  {
										  $po_qty+=$po_color_wise_qnty[$po_id][$vv];
									  }
															  
									  ?>
									  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
										  <td  valign="top" class="alignment_css" rowspan="<? echo $rowspan;?>" width="40" align="center"><? echo $i; ?></td>
										  <td  valign="top"  class="alignment_css" rowspan="<? echo $rowspan;?>"  width="100" align="center"><? echo $floor_group_arr[$rows["floor_id"]]; ?></td>
										  <td  valign="top"  class="alignment_css" rowspan="<? echo $rowspan;?>"  width="100" align="center"><? echo $floor_arr[$rows["floor_id"]]; ?></td>
										  <td  valign="top"  class="alignment_css" rowspan="<? echo $rowspan;?>"  width="100" align="center"><? echo $rows["buyer"]; ?></td>
										  <td  valign="top"  class="alignment_css" rowspan="<? echo $rowspan;?>"  width="100" align="center"><? echo $style_id; ?></td>
										  <td  valign="top"  class="alignment_css" rowspan="<? echo $rowspan;?>"  width="100" align="center"><? echo $rows["internal_ref"]; ?></td>
										  <td  valign="top"  class="alignment_css" rowspan="<? echo $rowspan;?>"  width="100" align="center"><? echo $rows["season_buyer_wise"]; ?></td>
										  <td  valign="top"  class="alignment_css" rowspan="<? echo $rowspan;?>"  width="100" align="center"><? echo $rows["po_number"]; ?></td>
										  <td  valign="top"  class="alignment_css" rowspan="<? echo $rowspan;?>" width="100" align="center"><? echo implode(",", array_unique(explode(",", $rows["colors"]))); ?></td>
										  <td  valign="top"  class="alignment_css"  rowspan="<? echo $rowspan;?>" width="100" align="center"><? echo $po_qty; ?></td>
										   
										  <td class="alignment_css"  width="200" >
											  <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" class="table_sub">
												  <tr class="no_border">
													  <td class="alignment_css" width="200">Total Defected pannels</td>
 
													  
												  </tr>
												  <tr class='no_border'>
													  <td class="alignment_css" width="200">Total Gmts Reject</td>
 
 
												  </tr>
 
												  <tr class='no_border'>
													  <td class="alignment_css" width="200">Reject% To Total QC</td>
 
 
												  </tr>
 
 
												  <tr class='no_border'>
													  <td class="alignment_css" width="200">Total Gmts Replace</td>
 
 
												  </tr>
 
												  <tr class='no_border'>
													  <td class="alignment_css" width="200">Total Qc Pass Qty</td>
 
 
												  </tr>
 
												  <tr class='no_border'>
													  <td class="alignment_css" width="200" title="( Qc Pass + Alter+ Reject + Spot)">Total Check Qty</td>
 
 
												  </tr>
												  <tr class='no_border'>
													  <td class="alignment_css" width="200">DHU%</td>
 
												  </tr>
												  <?
												  
												  foreach($cutting_qc_reject_type as $cut_key=>$cut_val)
												  {
													$cut_def=$def_arr[$style_id][$buyer_id][$po_id][1][3][$cut_key];
													if ($cut_def > 0)
													{
														?>
															<tr class=' '>
																<td class="alignment_css" width="200">
																	
																	<? echo $cut_val; ?>	
																</td>
															</tr>
	
														<?
													}
												  }
												  ?>
											  </table>
										  </td>
 
										   <td class="alignment_css"  width="100" >
											  <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" class="table_sub">
												  <tr class="">
													  <td class="alignment_css" width="100"> <? 
													  $chk=$qnty_arr[$style_id][$buyer_id][$po_id][1]["chk"];
													  $defect=$def_arr2[$style_id][$buyer_id][$po_id][1][3];
													  $pass=$qnty_arr[$style_id][$buyer_id][$po_id][1]["pass"];
													  if($defect)echo $defect;else echo 0;
													  ?></td>
 
													  
												  </tr>
												  <tr class=''>
													  <td class="alignment_css" width="100">
														  <? 
 
													  $reject=$qnty_arr[$style_id][$buyer_id][$po_id][1]["reject_cut"];
													  if($reject)echo $reject;else echo 0;
													  ?>
													  </td>
 
 
												  </tr>
 
												  <tr class=''>
													  <td class="alignment_css" width="100">
													  <? 
													  $pec=($reject/$pass)*100;
													  if($pec)echo number_format($pec,2);else echo 0;
													  ?></td>
 
 
												  </tr>
 
 
												  <tr class=''>
													  <td class="alignment_css" width="100">
													  <? 
													  $replace=$qnty_arr[$style_id][$buyer_id][$po_id][1]["replace"];
													  if($replace)echo $replace;else echo 0;
													  ?>
														  
													  </td>
 
 
												  </tr>
 
												  <tr class=''>
													  <td class="alignment_css" width="100">
													  <? 
													  
													  if($pass)echo $pass;else echo 0;
													  ?>
														  
													  </td>
 
 
												  </tr>
 
												  <tr class=''>
													  <td class="alignment_css" width="100">
													  <? 
													  
													  if($chk)echo $chk;else echo 0;
													  ?>
														  
													  </td>
 
 
												  </tr>
												  <tr class=''>
													  <td class="alignment_css" width="100"><? 
													  $dhu=($defect/$chk)*100;
													  $dhu=number_format($dhu,2);
													  if($dhu)echo $dhu." %";else echo  "0%";
													  ?></td>
 
												  </tr>
												  <?
												  foreach($cutting_qc_reject_type as $cut_key=>$cut_val)
												  {
													$cut_def=$def_arr[$style_id][$buyer_id][$po_id][1][3][$cut_key];
													if($cut_def>0)
													{
													  ?>
													  <tr class=''>
													  <td class="alignment_css" width="100">
													  <? 
													  
                                                        echo $cut_def; 
													 	
													  ?>
													  </td>
 
													  </tr>
 
													  <?
													} 
												  }
 
												  ?>
											  </table>
										  </td>
 
 
										   <td class="alignment_css"  width="100" >
											  <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" class="table_sub">
												  <tr class="no_border">
													  <td class="alignment_css" width="100">&nbsp; </td>
 
													  
												  </tr>
												  <tr class='no_border'>
													  <td class="alignment_css" width="100">&nbsp; </td>
 
 
												  </tr>
 
												  <tr class='no_border'>
													  <td class="alignment_css" width="100">&nbsp; </td>
 
 
												  </tr>
 
 
												  <tr class='no_border'>
													  <td class="alignment_css" width="100">&nbsp; </td>
 
 
												  </tr>
 
												  <tr class='no_border'>
													  <td class="alignment_css" width="100">&nbsp; </td>
 
 
												  </tr>
 
												  <tr class='no_border'>
													  <td class="alignment_css" width="100">&nbsp; </td>
 
 
												  </tr>
												  <tr class='no_border'>
													  <td class="alignment_css" width="100">&nbsp; </td>
 
												  </tr>
												  <?
												  foreach($cutting_qc_reject_type as $cut_key=>$cut_val)
												  {
													$cut_def=$def_arr[$style_id][$buyer_id][$po_id][1][3][$cut_key];
													if($cut_def>0)
													{
														?>
														<tr class='no_border'>
														<td class="alignment_css" width="100"><? 
														// $cut_def=$def_arr[$style_id][$buyer_id][$po_id][1][3][$cut_key];
														$cut_def=($cut_def/$defect)*100;
														$cut_def=number_format($cut_def,2);
														if($cut_def) 	echo $cut_def; else echo 0;
														?></td>
	
														</tr>
	
														<?
													}
												  }
												  ?>
											  </table>
										  </td>
 
									  </tr>
 
									  <?
									  $i++;
								  }
							  }
 
						  }
 
 
						  ?>
					  </tbody>
				  </table>
			  </div>
 
 
		  </div>
		  <script type="text/javascript">
			  
			  $('#main_div td').each(function() { 
				  if(trim(this.textContent)=="nan"  || this.textContent=='nan %'   )this.textContent=0;
				   
			  });
		  </script>
 
 
		  <?
 
 
 
	    }
		if($cbo_production_type==2)
		{
			$sql = "SELECT a.client_id, b.id, b.grouping,d.floor_id, d.production_type, b.po_number,c.color_number_id , a.buyer_name,a.season_buyer_wise, a.style_ref_no, sum(c.order_quantity) as po_quantity, sum(e.production_qnty) as qc_pass_qty, sum(e.alter_qty) as alter_qnty,  sum(case when is_rescan=0 then  e.reject_qty else 0 end )  as reject_qnty,  sum(case when e.production_type=1 then  e.reject_qty else 0 end )  as reject_qnty_cut, sum(e.spot_qty) as spot_qnty, sum(e.replace_qty) as replace_qty,sum(case when is_rescan=1 then e.production_qnty else 0 end ) as today_rescan FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.po_break_down_id=d.po_break_down_id and d.id=e.mst_id and c.id= e.color_size_break_down_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $all_cond and d.production_type in(1,5) group by a.client_id,b.id, b.grouping,d.floor_id, d.production_type,b.po_number,c.color_number_id , a.buyer_name,a.season_buyer_wise, a.style_ref_no  ";
 
		  /*$sql_def = "SELECT b.id, d.production_type, b.po_number,c.color_number_id , a.buyer_name,a.season_buyer_wise, a.style_ref_no, f.defect_type_id, f.defect_point_id, f.defect_qty   FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,pro_garments_production_mst d,pro_garments_production_dtls e,pro_gmts_prod_dft f  WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.po_break_down_id=d.po_break_down_id and d.id=e.mst_id and c.id= e.color_size_break_down_id and e.barcode_no=f.bundle_no and d.id=f.mst_id  and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $all_cond and d.production_type in(1,5) group by b.id, d.production_type, b.po_number,c.color_number_id , a.buyer_name,a.season_buyer_wise, a.style_ref_no, f.defect_type_id, f.defect_point_id, f.defect_qty  ";*/
		  $all_po_arr=array();
		  
		  foreach(sql_select($sql) as $val)
		  {
			  $all_po_arr[$val[csf("id")]]=$val[csf("id")];
			  $main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["production_type"]=$val[csf("production_type")];
			  $main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["color_number_id"].=','.$val[csf("color_number_id")];
			  $main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["po_quantity"]=$val[csf("po_quantity")];
			  $qnty_arr[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]][$val[csf("production_type")]]["chk"]+=$val[csf("qc_pass_qty")]+$val[csf("alter_qnty")]+$val[csf("reject_qnty")]+$val[csf("spot_qnty")] ;
 
			  $qnty_arr[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]][$val[csf("production_type")]]["reject"]+=$val[csf("reject_qnty")] ;
			  $qnty_arr[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]][$val[csf("production_type")]]["reject_cut"]+=$val[csf("reject_qnty_cut")] ;
			  $qnty_arr[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]][$val[csf("production_type")]]["pass"]+=$val[csf("qc_pass_qty")] ;
 
			  $qnty_arr[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]][$val[csf("production_type")]]["replace"]+=$val[csf("replace_qty")] ;
 
			  $main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["buyer"]=$buyer_arr[$val[csf("buyer_name")]];
			  $main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["client"]=$client_arr[$val[csf("client_id")]];
			  $main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["po_number"]=$val[csf("po_number")];
			  $main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["internal_ref"]=$val[csf("grouping")];
			  $main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["floor_id"]=$val[csf("floor_id")];
			  $main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["season_buyer_wise"]=$season_arr [$val[csf("season_buyer_wise")]];
			  if($main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["colors"]=="")
				  $main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["colors"].=$color_arr[$val[csf("color_number_id")]];
			  else
				  $main_array[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]]["colors"].=','.$color_arr[$val[csf("color_number_id")]];
 
		  }
		  //echo "<pre>";print_r($qnty_arr);die;
		  $po_ids=implode(",",$all_po_arr);
		  foreach(sql_select("SELECT po_break_down_id,color_number_id,sum(order_quantity) as qnty from wo_po_color_size_breakdown where status_active=1 and po_break_down_id in($po_ids)   group by po_break_down_id,color_number_id ") as $values  )
		  {
			  $po_color_wise_qnty[$values[csf("po_break_down_id")]][$values[csf("color_number_id")]]=$values[csf("qnty")];
		  }
		  //print_r($po_color_wise_qnty);
			$sql_def = "SELECT b.id, d.production_type, b.po_number,c.color_number_id , a.buyer_name,a.season_buyer_wise, a.style_ref_no, f.defect_type_id, f.defect_point_id, sum(f.defect_qty ) as defect_qty  FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,pro_garments_production_mst d,pro_garments_production_dtls e,pro_gmts_prod_dft f  WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.po_break_down_id=d.po_break_down_id and d.id=e.mst_id and c.id= e.color_size_break_down_id and e.barcode_no=f.bundle_no and d.id=f.mst_id  and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $all_cond and d.production_type in(1,5) group by b.id, d.production_type, b.po_number,c.color_number_id , a.buyer_name,a.season_buyer_wise, a.style_ref_no, f.defect_type_id, f.defect_point_id   ";
		  
			foreach(sql_select($sql_def) as $val)
			{
				$def_arr[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]][$val[csf("production_type")]][$val[csf("defect_type_id")]][$val[csf("defect_point_id")]] +=$val[csf("defect_qty")];
				$qnty_arr[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]][$val[csf("production_type")]]["defect"]+=$val[csf("defect_qty")] ;
				$def_arr2[$val[csf("style_ref_no")]][$val[csf("buyer_name")]][$val[csf("id")]][$val[csf("production_type")]][$val[csf("defect_type_id")]]  +=$val[csf("defect_qty")];
 
			}
		  
 
			  ob_start();
			  ?>
		  
		  <div id="main_div">
		  <style type="text/css">
			  .alignment_css
			  {
				  word-break: break-all;
				  word-wrap: break-word;
			  }
			  .table_sub table{
				   
			  }
			  .no_border tr ,td
			  {
				   
					  
  
			  }
		  </style>
			  <table width="1400" cellspacing="0" >
				  <tr class="form_caption" style="border:none;">
					  <td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >
						  <strong>Style wise DHU % Report (Sewing)	</strong>
					  </td>
				  </tr>
				  <tr class="form_caption" style="border:none;">
					  <td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >
						  <strong><? echo $company_library[$cbo_company_name];?></strong>
					  </td>
				  </tr>
				  <tr class="form_caption" style="border:none;">
					  <td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >
						  <strong> </strong>
					  </td>
				  </tr>
			  </table>
 
			  <table cellspacing="0" cellpadding="0"  border="1" rules="all"  width="1340" class="rpt_table" align="left">
				  <thead>
					  <tr>
						  <th class="alignment_css" width="40">SL</th>
						  <th class="alignment_css" width="100">Floor Group</th>
						  <th class="alignment_css" width="100">Floor</th>
						  <th class="alignment_css" width="100">Buyer</th>
						  <th class="alignment_css" width="100">Style No</th>
						  <th class="alignment_css" width="100">Internal Ref.</th>
						  <th class="alignment_css" width="100">Season</th>
						  <th class="alignment_css" width="100">Order No</th>
						  <th class="alignment_css" width="100">Color</th>
						  <th class="alignment_css" width="100">Order Qty</th>
						  <th class="alignment_css" width="200"> Defect Name</th>
						  <th class="alignment_css" width="100"> Defect <br>Qty</th>
						  <th class="alignment_css" width="100">Defect %</th>
					  </tr>
				  </thead>
			  </table>
			  <div style="max-height:425px; overflow-y:scroll; width:1360px;" id="scroll_body">
				  <table cellpadding="0" cellspacing="0"  align="left" border="1" class="rpt_table"  width="1340" rules="all" id="table_body" >
					  <tbody>
						  <?
						   
						  $cutting_def_count=count($cutting_qc_reject_type);
						  $sewing_def_count=count($sew_fin_alter_defect_type)+count($sew_fin_spot_defect_type);
						  $diff_cut=0; 
						  if($sewing_def_count>$cutting_def_count)
							  $diff_cut=abs($sewing_def_count-$cutting_def_count);
 
						  $diff_sew=0; 
						  if($sewing_def_count<$cutting_def_count)
							  $diff_sew=abs($cutting_def_count-$sewing_def_count );
 
						  $span=($cutting_def_count>$sewing_def_count)?$cutting_def_count : $sewing_def_count;
						  $span=$span+7;
						  $i=1;
						  foreach($main_array as $style_id=>$buyer_data )
						  {
							  foreach($buyer_data as $buyer_id=>$po_data )
							  {
								  foreach($po_data as $po_id=>$rows )
								  {
										
									  if ($i%2==0)
										  $bgcolor="#E9F3FF";
									  else
										  $bgcolor="#FFFFFF";
									  $po_qty=0;
									  $color_ids=array_unique(explode(",", trim($rows["color_number_id"],",")));
									  foreach($color_ids as $vv)
									  {
										  $po_qty+=$po_color_wise_qnty[$po_id][$vv];
									  }
															  
									  ?>
									  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
										  <td  valign="top" class="alignment_css" rowspan="<? echo $rowspan;?>" width="40" align="center"><? echo $i; ?></td>
										  <td  valign="top"  class="alignment_css" rowspan="<? echo $rowspan;?>"  width="100" align="center"><? echo $floor_group_arr[$rows["floor_id"]]; ?></td>
										  <td  valign="top"  class="alignment_css" rowspan="<? echo $rowspan;?>"  width="100" align="center"><? echo $floor_arr[$rows["floor_id"]]; ?></td>
										  <td  valign="top"  class="alignment_css" rowspan="<? echo $rowspan;?>"  width="100" align="center"><? echo $rows["buyer"]; ?></td>
										  <td  valign="top"  class="alignment_css" rowspan="<? echo $rowspan;?>"  width="100" align="center"><? echo $style_id; ?></td>
										  <td  valign="top"  class="alignment_css" rowspan="<? echo $rowspan;?>"  width="100" align="center"><? echo $rows["internal_ref"]; ?></td>
										  <td  valign="top"  class="alignment_css" rowspan="<? echo $rowspan;?>"  width="100" align="center"><? echo $rows["season_buyer_wise"]; ?></td>
										  <td  valign="top"  class="alignment_css" rowspan="<? echo $rowspan;?>"  width="100" align="center"><? echo $rows["po_number"]; ?></td>
										  <td  valign="top"  class="alignment_css" rowspan="<? echo $rowspan;?>" width="100" align="center"><? echo implode(",", array_unique(explode(",", $rows["colors"]))); ?></td>
										  <td  valign="top"  class="alignment_css"  rowspan="<? echo $rowspan;?>" width="100" align="center"><? echo $po_qty; ?></td>
 
										  <td class="alignment_css"  width="200" >
											  <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" class="table_sub">
												  <tr class="no_border">
													  <td class="alignment_css" width="200">Total Defected pannels</td>
 
													  
												  </tr>
												  <tr class='no_border'>
													  <td class="alignment_css" width="200">Total Gmts Reject</td>
 
 
												  </tr>
 
												  <tr class='no_border'>
													  <td class="alignment_css" width="200">Reject% To Total QC</td>
 
 
												  </tr>
 
 
												  <tr class='no_border'>
													  <td class="alignment_css" width="200">Total Gmts Replace</td>
 
 
												  </tr>
 
												  <tr class='no_border'>
													  <td class="alignment_css" width="200">Total Qc Pass Qty</td>
 
 
												  </tr>
 
												  <tr class='no_border'>
													  <td class="alignment_css" width="200" title="( Qc Pass + Alter+ Reject + Spot)">Total Check Qty</td>
 
 
												  </tr>
												  <tr class='no_border'>
													  <td class="alignment_css" width="200">DHU%</td>
 
												  </tr>
												  <?
												  foreach($sew_fin_alter_defect_type as $cut_key=>$cut_val)
												  {
													$alt_def=$def_arr[$style_id][$buyer_id][$po_id][5][3][$cut_key];
													if ($alt_def>0)
													{
													  ?>
													  <tr class='no_border'>
													  <td class="alignment_css" width="200"><? echo $cut_val;?></td>
 
													  </tr>
 
													  <?
													}
												  }
 
												  foreach($sew_fin_spot_defect_type as $cut_key=>$cut_val)
												  {
													$spot_def=$def_arr[$style_id][$buyer_id][$po_id][5][4][$cut_key];
													if ($spot_def>0)
													{
													  ?>
													  <tr class='no_border'>
													  <td class="alignment_css" width="200"><? echo $cut_val;?></td>
 
													  </tr>
 
													  <?
													}
												  }
 
												  if($diff_sew)
												  {
													  for($pp=1;$pp<=$diff_sew;$pp++)
													  {
														  ?>
														  <tr class='no_border'>
														  <td class="alignment_css" width="200">&nbsp; </td>
 
														  </tr>
 
													  <?
 
													  }
												  }
 
 
 
												  ?>
											  </table>
										  </td>
 
										   <td class="alignment_css"  width="100" >
											  <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" class="table_sub">
												  <tr class="">
													  <td class="alignment_css" width="100"> <? 
													  $chk=$qnty_arr[$style_id][$buyer_id][$po_id][5]["chk"];
													  $defect_sew=$def_arr2[$style_id][$buyer_id][$po_id][5][3]+$def_arr2[$style_id][$buyer_id][$po_id][5][4];
													  $pass=$qnty_arr[$style_id][$buyer_id][$po_id][5]["pass"];
													  if($defect_sew)echo $defect_sew;else echo 0;
													  ?></td>
 
													  
												  </tr>
												  <tr class=''>
													  <td class="alignment_css" width="100">
														  <? 
													  $reject=$qnty_arr[$style_id][$buyer_id][$po_id][5]["reject"];
													  if($reject)echo $reject;else echo 0;
													  ?>
													  </td>
 
 
												  </tr>
 
												  <tr class=''>
													  <td class="alignment_css" width="100">
													  <? 
													  $pec2=($reject/$pass)*100;
													  if($pec2)echo number_format($pec2,2);else echo 0;
													  ?></td>
 
 
												  </tr>
 
 
												  <tr class=''>
													  <td class="alignment_css" width="100">
													  <? 
													  $replace=$qnty_arr[$style_id][$buyer_id][$po_id][5]["replace"];
													  if($replace)echo $replace;else echo 0;
													  ?>
														  
													  </td>
 
 
												  </tr>
 
												  <tr class=''>
													  <td class="alignment_css" width="100">
													  <? 
													  
													  if($pass)echo $pass;else echo 0;
													  ?>
														  
													  </td>
 
 
												  </tr>
 
												  <tr class=''>
													  <td class="alignment_css" width="100">
													  <? 
													  
													  if($chk)echo $chk;else echo 0;
													  ?>
														  
													  </td>
 
 
												  </tr>
												  <tr class=''>
													  <td class="alignment_css" width="100"><? 
													  $dhu=($defect_sew/$chk)*100;
													  $dhu=number_format($dhu,2);
													  if($dhu)echo $dhu." %";else echo  "0%";
													  ?></td>
 
												  </tr>
												  <?
												  foreach($sew_fin_alter_defect_type as $alt_key=>$cut_val)
												  {
													$alt_def=$def_arr[$style_id][$buyer_id][$po_id][5][3][$alt_key];
													if ($alt_def>0)
													{
														?>
														<tr class=''>
															<td class="alignment_css" width="100">
																<? 
																	echo $alt_def; 
																?>
															</td>
	
														</tr>
														<?
													}
												  }
												  foreach($sew_fin_spot_defect_type as $spot_key=>$cut_val)
												  {
													$spot_def=$def_arr[$style_id][$buyer_id][$po_id][5][4][$spot_key];
													if ($spot_def>0)
													{
													  ?>
													  <tr class=''>
														<td class="alignment_css" width="200">
															<? 
																echo $spot_def;
														    ?>
														</td>
													  </tr>
													  <?
													}
												  }
												  if($diff_sew)
												  {
													  for($pp=1;$pp<=$diff_sew;$pp++)
													  {
														  ?>
														  <tr class='no_border'>
														  <td class="alignment_css" width="100">&nbsp; </td>
 
														  </tr>
 
													  <?
 
													  }
												  }
												  ?>
											  </table>
										  </td>
 
 
										   <td class="alignment_css"  width="100" >
											  <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" class="table_sub">
												  <tr class="no_border">
													  <td class="alignment_css" width="100"> </td>
 
													  
												  </tr>
												  <tr class='no_border'>
													  <td class="alignment_css" width="100"> </td>
 
 
												  </tr>
 
												  <tr class='no_border'>
													  <td class="alignment_css" width="100"> </td>
 
 
												  </tr>
 
 
												  <tr class='no_border'>
													  <td class="alignment_css" width="100"></td>
 
 
												  </tr>
 
												  <tr class='no_border'>
													  <td class="alignment_css" width="100"> </td>
 
 
												  </tr>
 
												  <tr class='no_border'>
													  <td class="alignment_css" width="100"> </td>
 
 
												  </tr>
												  <tr class='no_border'>
													  <td class="alignment_css" width="100"> </td>
 
												  </tr>
												  <?
												  foreach($sew_fin_alter_defect_type as $alt_key=>$cut_val)
												  {
													$alt_def=$def_arr[$style_id][$buyer_id][$po_id][5][3][$alt_key];
													if($alt_def>0)
													{
													  ?>
													  <tr class='no_border'>
													  <td class="alignment_css" width="100"><? 
													  $alt_def=($alt_def/$defect_sew)*100;
													  $alt_def=number_format($alt_def,2);
													  if($alt_def) 	echo $alt_def; else echo 0;
													  ?></td>
 
													  </tr>
 
													  <?
													}
												  }
 
												  foreach($sew_fin_spot_defect_type as $spot_key=>$cut_val)
												  {
													$spot_def=$def_arr[$style_id][$buyer_id][$po_id][5][4][$spot_key];
													if($spot_def>0)
													{
														?>
														<tr class=' '>
														<td class="alignment_css" width="200"><? 
														$spot_def=($spot_def/$defect_sew)*100;
														$spot_def=number_format($spot_def,2);
														if($spot_def) 	echo $spot_def; else echo 0;
														?></td>
	
														</tr>
	
														<?
													}
												  }
												  if($diff_sew)
												  {
													  for($pp=1;$pp<=$diff_sew;$pp++)
													  {
														  ?>
														  <tr class=' '>
														  <td class="alignment_css" width="100">&nbsp; </td>
 
														  </tr>
 
													  <?
 
													  }
												  }
 
												  ?>
											  </table>
										  </td>
 
									  </tr>
 
									  <?
									  $i++;
								  }
							  }
 
						  }
 
 
						  ?>
					  </tbody>
				  </table>
			  </div>
 
 
		  </div>
		  <script type="text/javascript">
			  
			  $('#main_div td').each(function() { 
				  if(trim(this.textContent)=="nan"  || this.textContent=='nan %'   )this.textContent=0;
				   
			  });
		  </script>
 
 
		  <?
 
 
 
	    }
	}
	$html = ob_get_contents();
    ob_clean();
     foreach (glob("$user_id*.xls") as $filename) {
     @unlink($filename);
    }
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****$type";

	exit();
}


 
?>