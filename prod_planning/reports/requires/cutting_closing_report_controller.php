<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.yarns.php');
require_once('../../../includes/class4/class.conversions.php');
require_once('../../../includes/class4/class.emblishments.php');
require_once('../../../includes/class4/class.commisions.php');
require_once('../../../includes/class4/class.commercials.php');
require_once('../../../includes/class4/class.others.php');
require_once('../../../includes/class4/class.trims.php');
require_once('../../../includes/class4/class.fabrics.php');
require_once('../../../includes/class4/class.washes.php');
if (!function_exists('pre')) 
{
	 function pre($arr)
	 {
		echo "<pre>";
		print_r($arr);
		echo "</pre>";
	 }
}

$user_name			= $_SESSION['logic_erp']['user_id'];
$data				= $_REQUEST['data'];
$action				= $_REQUEST['action'];
$company_arr		= return_library_array( "select id, company_name from lib_company",'id','company_name');
$color_library		= return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$size_library		= return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
// $order_no_library	= return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"  );
$buyer_arr			= return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$brand_arr			= return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
$buyer_brand_arr	= return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
$buyer_season_arr	= return_library_array( "select id,season_name from lib_buyer_season",'id','season_name');
$table_arr			= return_library_array( "select id, table_no from lib_cutting_table",'id','table_no');
$floor_arr			= return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');

//--------------------------------------------------------------------------------------------------------------------
if($action=="style_search_popup_")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	echo $style_id;die;

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
	
	 $sql = "SELECT a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$select_date as year from wo_po_details_master a where a.company_name=$company $buyer_cond  and is_deleted=0 order by job_no_prefix_num DESC,$select_date"; 
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

if($action=="style_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_id = new Array; var selected_name = new Array;var selected_style = new Array;
		
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
				selected_style.push( str[3] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_style.splice( i, 1 );
			}
			var id = ''; var name = ''; var style = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
				style += selected_style[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			style = style.substr( 0, style.length - 1 );
			
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
			$('#hide_style_no').val( style );
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
	                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
	                    <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
	                    <input type="hidden" name="hide_style_no" id="hide_style_no" value="" />
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	 <? 
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
								?>
	                        </td>                 
	                        <td align="center">	
	                    	<?
	                       		$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No",4=>"Int Ref");
								$dd="change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";							
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $job_no; ?>', 'create_style_no_search_list_view', 'search_div', 'cutting_closing_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	<script type="text/javascript">
		$("#cbo_buyer_name").val('<?=$buyer;?>');
	</script>    
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}

if($action=="create_style_no_search_list_view")
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
	else if($search_by==3)
		$search_field="a.job_no";
	else if($search_by==4)
		$search_field="b.grouping";
		
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
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$arr=array (0=>$company_library,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql= "SELECT a.id,a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, listagg(distinct b.grouping, ',') within group (order by b.grouping) as grouping
	from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $job_no_cond $date_cond group by a.id,a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,a.insert_date order by a.job_no desc";
	// echo $sql;
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No,Internal Ref", "120,100,50,80,140,100","670","220",0, $sql , "js_set_value", "id,job_no,style_ref_no","",1,"company_name,buyer_name,0,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no,style_ref_no,grouping","",'','0,0,0,0,0,0','',1) ;
   exit(); 
}
if($action=="intref_search_popup_")
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
	
	$sql = "SELECT a.id,b.grouping,b.file_no,a.job_no,a.job_no_prefix_num,$select_date as year from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company $buyer_cond  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 order by job_no_prefix_num DESC,$select_date"; 
	//echo $sql; die;
	echo create_list_view("list_view", "Int. Ref. No,Job No,Year","160,90,100","410","300",0, $sql , "js_set_value", "id,job_no,grouping,file_no", "", 1, "0", $arr, "grouping,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","","") ;	
	//echo "<input type='hidden' id='txt_selected_id' />";
	//echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	
	exit();
}

if($action=="intref_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_id = new Array; var selected_name = new Array; var selected_intref = new Array;
		
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
				selected_intref.push( str[3] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_intref.splice( i, 1 );
			}
			var id = ''; var name = ''; var intref = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
				intref += selected_intref[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			intref = intref.substr( 0, intref.length - 1 );
			
			$('#hide_order_id').val( id );
			$('#hide_order_no').val( name );
			$('#hide_int_ref').val( intref );
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
	                    <input type="hidden" name="hide_int_ref" id="hide_int_ref" value="" />
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	 <? 
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
								?>
	                        </td>                 
	                        <td align="center">	
	                    	<?
	                       		$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No",4=>"Int Ref");
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $job_no; ?>', 'create_int_ref_search_list_view', 'search_div', 'cutting_closing_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	<script type="text/javascript">
		$("#cbo_buyer_name").val('<?=$buyer;?>');
	</script>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}

if($action=="create_int_ref_search_list_view")
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
	{
		$search_field="b.po_number"; 
	}
	else if($search_by==2)
	{
		$search_field="a.style_ref_no"; 	
	}
	else if($search_by==4)
	{
		$search_field="b.grouping";
	}
	else
	{
	   $search_field="a.job_no";
	}

	
		
		
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
	
	$sql= "SELECT b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, b.grouping, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $job_no_cond $date_cond order by b.id, b.pub_shipment_date";
	//echo $sql;
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Int. Ref. No, Po No, Shipment Date", "80,80,50,70,140,170","760","220",0, $sql , "js_set_value", "id,po_number,grouping","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,grouping,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3','',1) ;
   exit(); 
}


if($action=="fileno_search_popup")
{
	echo load_html_head_contents("File No Pop-Up", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_id = new Array; var selected_name = new Array; var selected_intref = new Array;
		
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
				selected_intref.push( str[3] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_intref.splice( i, 1 );
			}
			var id = ''; var name = ''; var intref = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
				intref += selected_intref[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			intref = intref.substr( 0, intref.length - 1 );
			
			$('#hide_order_id').val( id );
			$('#hide_order_no').val( name );
			$('#file_no').val( intref );
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
	                    <input type="hidden" name="file_no" id="file_no" value="" />
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	 <? 
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $job_no; ?>', 'create_fileno_search_list_view', 'search_div', 'cutting_closing_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	<script type="text/javascript">
		$("#cbo_buyer_name").val('<?=$buyer;?>');
	</script>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}

if($action=="create_fileno_search_list_view")
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
	
	$sql= "SELECT b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, b.file_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $job_no_cond $date_cond order by b.id, b.pub_shipment_date";
	 echo $sql;
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Int. Ref. No, Po No, Shipment Date", "80,80,50,70,140,170","760","220",0, $sql , "js_set_value", "id,po_number,file_no","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,file_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3','',1) ;
   exit(); 
}

if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $cbo_buyer_name;
	if($cbo_buyer_name!=0) $buyer_cond=" and a.buyer_name=$buyer"; else $buyer_cond="";
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? 
								echo $companyID; ?>'+'**'+
								document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $job_no; ?>', 
								'create_order_no_search_list_view', 'search_div', 'cutting_closing_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	// echo "<pre>";
	// print_r($data);
	// echo "</pre>";
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
	//echo $sql; //die;
		
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
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "onchange_buyer()" );     	 
	exit();
}

if ($action=="load_drop_down_gmts_color")
{
	$sql="SELECT b.id,b.color_name from wo_po_color_size_breakdown a, lib_color b where job_no_mst='$data' and b.id=a.color_number_id ";
	//echo $sql;
	$gmts_color_arr=return_library_array( $sql, "id","color_name" );
	echo create_drop_down( "cbo_gmts_color", 100, $gmts_color_arr,"", 1, "-- Select --", 0, "","","","" ); 
	exit();
}
if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');

	extract($_REQUEST);
	// echo $buyer_name;
	// echo $companyID;
	//echo $cbo_year;
	?>
	<script>
	
		function js_set_value(str)
		{
			$("#hide_job_no").val(str); 
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
                    <th>
                        <input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"> 					
                        <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                    </th>
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (
							'<? echo $companyID; ?>'+'**'+
							document.getElementById('cbo_buyer_name').value+'**'+
							document.getElementById('cbo_search_by').value+'**'+
							document.getElementById('txt_search_common').value+'**'+<?=$cbo_year?>, 'create_job_no_search_list_view', 'search_div', 'cutting_closing_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:5px" id="search_div"></div>
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
	// echo "<pre>";
	// print_r($data);
	// echo "</pre>";
	$company_id=$data[0];
	$year_id=$data[4];
	//$month_id=$data[5];
	//echo $month_id;
	//	var_dump($data);
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
	if($search_by==2) $search_field="a.style_ref_no"; else $search_field="a.job_no";
	$year="year(a.insert_date)";
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
	// $sql= "SELECT b.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $year_field from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and company_name=$company_id and  $search_field  like '$search_string' $buyer_id_cond $year_cond order by a.job_no";

	$sql= "SELECT a.id, a.job_no,  a.company_name, a.buyer_name, a.style_ref_no, $year_field from wo_po_details_master a where  a.status_active=1 and a.is_deleted=0 and company_name=$company_id and  $search_field  like '$search_string' $buyer_id_cond $year_cond order by a.job_no";
    // echo $sql;die();
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no,year,style_ref_no", "",'','','') ;
	exit();
} // Job Search end

if($action=="cutting_number_popup")
{
  	echo load_html_head_contents("Batch Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_cutting_value(strCon ) 
		{
			document.getElementById('hdn_cut_no').value=strCon;
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:100%; overflow-y:hidden;" >
	    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	            <table width="950" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
	            <thead>
	                <tr>                	 
	                    <th width="140">Company name</th>
	                    <th width="130">Cutting No</th>
	                    <th width="130">Job No</th>
	                    <th width="130">Order No</th>
	                    <th width="250">Date Range</th>
	                    <th width="120"><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
	                </tr>
	            </thead>
	            <tbody>
	                  <tr>                    
	                        <td>
	                              <? 
	                              echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 0, "-- Select Company --",$company_id, "",1);
	                             ?>
	                        </td>
	                      
	                        <td align="center" >
	                                <input type="text" id="txt_cut_no" name="txt_cut_no" style="width:120px"  class="text_boxes"/>
	                                <input type="hidden" id="hdn_cut_no" name="hdn_cut_no" />
	                        </td>
	                        <td align="center">
	                               <input name="txt_job_search" id="txt_job_search" class="text_boxes" style="width:120px"  />
	                        </td>
	                        <td align="center">
	                               <input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px"  />
	                        </td>
	                        <td align="center" width="250">
	                               <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" />
	                               <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date" />
	                        </td>
	                        <td align="center">
	                               <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value, 'create_cutting_search_list_view', 'search_div', 'cutting_closing_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
	                        </td>
	                 </tr>
	        		 <tr>                  
	                        <td align="center" height="40" valign="middle" colspan="6">
	                            <? echo load_month_buttons(1);  ?>
	                        </td>
	                </tr>   
	            </tbody>
	         </tr>         
	      </table> 
	     <div align="center" valign="top" id="search_div"> </div>  
	  </form>
	</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_cutting_search_list_view")
{
    $ex_data = explode("_",$data);
	$company = $ex_data[0];	
	$cutting_no = $ex_data[1];
	$job_no = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$cut_year= $ex_data[5];
	$order_no= $ex_data[6];
    if($db_type==2) { $year_cond=" and extract(year from a.insert_date)=$cut_year"; $year=" extract(year from a.insert_date) as year ";}
    if($db_type==0) {$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year"; $year=" SUBSTRING_INDEX(a.insert_date, '-', 1) as year ";}
	if(str_replace("'","",$company)==0) $conpany_cond=""; else $conpany_cond="and a.company_id=".str_replace("'","",$company)."";
	if(str_replace("'","",$cutting_no)=="") $cut_cond=""; else $cut_cond="and a.cut_num_prefix_no='".str_replace("'","",$cutting_no)."'  $year_cond";
	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and b.job_no_prefix_num='".str_replace("'","",$job_no)."'";
	if(str_replace("'","",$order_no)=="") $order_cond=""; else $order_cond="and c.po_number like '%".trim($order_no)."%' ";
	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==0)
	       {
			      $sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		   }
	  if($db_type==2)
	       {
			      $sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		   }
	}
	
	$sql_order="SELECT a.id,a.cut_num_prefix_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.marker_length, a.marker_width, a.fabric_width,d.order_id,d.color_id,$year,c.po_number FROM ppl_cut_lay_mst a,wo_po_details_master b,wo_po_break_down c,ppl_cut_lay_dtls d where  a.id=d.mst_id and a.job_no=b.job_no and b.id=c.job_id and a.entry_form=76 and c.id=d.order_id $conpany_cond  $cut_cond $job_cond $sql_cond $order_cond order by id";
	$arr=array(2=>$table_arr,5=>$color_library);
	echo create_list_view("list_view", "Cut No,Year,Table No,Job No,Order NO,Color,Marker Length,Markar Width,Fabric Width,Entry Date","90,50,60,120,120,100,80,80,80,120","950","270",0, $sql_order , "js_set_cutting_value", "cut_num_prefix_no", "", 1, "0,0,table_no,0,0,color_id,0,0,0,0,0", $arr, "cut_num_prefix_no,year,table_no,job_no,po_number,color_id,marker_length,marker_width,fabric_width,entry_date", "","setFilterGrid('list_view',-1)") ;
}

if ($action == "report_generate" )
{
	//   var_dump($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	// =================All Searching Data Rcv=========================
	$rept_type			= str_replace( "'", "", $type );
	$company_name		= str_replace( "'", "", $cbo_company_name );
	$buyer_name			= str_replace( "'", "", $cbo_buyer_name );
	$cbo_season_year	= str_replace( "'", "", $cbo_season_year );	
	$job_no				= str_replace( "'", "", $txt_job_no );
	$color_id			= str_replace( "'", "", $cbo_gmts_color );
	$order_no			= str_replace( "'", "", $txt_order_no);
	$file_no			= str_replace( "'", "", $file_no );	
	$ref_no				= str_replace( "'", "", $txt_ref_no);
	
	$txt_style_ref_id	= str_replace( "'", "", $txt_style_ref_id);
	$txt_style_ref_no	= str_replace( "'", "", $txt_style_ref_no);
	$job_id_hidden		= str_replace( "'", "", $txt_job_id);
	$hide_order_id		= str_replace( "'", "", $hide_order_id);
	
	// $int_ref			= str_replace( "'", "", $int_ref);
	// $int_ref_id			= str_replace( "'", "", $int_ref_id);
	// =================================================================

	// =============All Searching Data Check=====
	/*echo "type: ".$rept_type."<br>"
	."company_id: ".$company_name."<br>"
	."buyer_id: ".$buyer_name."<br>"
	."cbo_season_year: ".$cbo_season_year."<br>"
	."job_no: ".$job_no."<br>"
	."color_id: ".$color_id."<br>"
	."order_no: ".$order_no."<br>"
	."file_no: ".$file_no."<br>"
	."ref_no: ".$ref_no."<br>"
	."txt_style_ref_id: ".$txt_style_ref_id."<br>"
	."job_id_hidden: ".$job_id_hidden."<br>"
	."hide_order_id ".$hide_order_id;*/
	// =============================================

	$body_part = return_library_array("select id,body_part_full_name from  lib_body_part where status_active=1 and is_deleted=0 order by body_part_full_name", "id", "body_part_full_name");
	
	$sql_cond	= "";
	$po_cond 	= "";
	if($company_name>0) $sql_cond=" AND a.company_id=$company_name";
	if($buyer_name>0) $sql_cond.=" AND c.buyer_name=$buyer_name";
	// if($gmts_item>0) $sql_cond.=" AND b.gmt_item_id in($gmts_item)";
	// if($gmts_item>0) $gmt_item_cond=" AND item_number_id in($gmts_item)";else $gmt_item_cond="";
	
	if($order_no>0) $po_id_con=" AND po_break_down_id in($hide_order_id)";else $po_id_con="";
	if($color_id!=0) $color_con=" AND c.color_number_id =$color_id";else $color_con="";

	if($job_id_hidden !="") $sql_cond.=" AND b.id in($job_id_hidden)";
	if($hide_order_id !="") $sql_cond.=" AND d.id in($hide_order_id) ";
	if($order_no=="")
	{
		$po_cond="";
	}
	else
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id=str_replace("'","",$hide_order_id);
			$po_cond="and a.id in(".$po_id.")";
		}
		else
		{
			$po_number="%".trim($order_no)."%";
			$po_cond="and a.po_number like '$po_number'";
		}
	}
	if($job_no=="")
	{
		$job_cond="";
	}
	else
	{
		$job_cond="b.job_no like '$job_no' AND";
	}
	

	$sql_job="SELECT a.po_number,a.id,b.job_no,b.gmts_item_id,c.item_number_id as item_id,c.color_number_id,c.size_number_id,b.style_ref_no,b.product_dept,b.buyer_name,b.season_buyer_wise,b.brand_id,b.remarks,a.po_received_date,a.shipment_date,c.order_quantity,c.id as color_size_id, a.grouping
	from 
	wo_po_break_down a, 
	wo_po_details_master b,
	wo_po_color_size_breakdown c
	Where $job_cond c.po_break_down_id=a.id
	and b.id = a.job_id
	and b.company_name=$company_name  $color_con $po_cond
	and a.status_active=1 and a.is_deleted=0
	and b.status_active=1 and b.is_deleted=0
	and c.status_active in(1,2,3) and c.is_deleted=0
	";
	// echo $sql_job;
	$sql_res=sql_select($sql_job);
	
	$color_size_array= array();

	if ($rept_type==1)  //show button
	{ 
		if(count($sql_res)==0 || $job_no=="")
		{
			?>
			<div style="text-align: center;font-size: 20px;font-weight: bold;color: red;">Data not found.</div>
			<?
			die();
		}
		foreach ($sql_res as $row) 
		{
			$color_size_array[$row[csf("color_number_id")]][$row[csf("size_number_id")]]["po_number"].=$row[csf("po_number")];
			$color_size_array[$row[csf("color_number_id")]][$row[csf("size_number_id")]]["order_qty"]+=$row[csf("order_quantity")];
			$color_size_array[$row[csf("color_number_id")]][$row[csf("size_number_id")]]["order_id"].=$row[csf("id")].",";
			$color_size_array[$row[csf("color_number_id")]][$row[csf("size_number_id")]]["remarks"].=$row[csf("remarks")].",";
			$order_id_arr[$row[csf("id")]]=$row[csf("id")];
		}
	}
	else if ($rept_type==2) // show2 button
	{ 
		foreach ($sql_res as $row) 
		{
			$po_color_size_array[$row[csf("id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["po_number"].=$row[csf("po_number")];
			$po_color_size_array[$row[csf("id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["order_qty"]+=$row[csf("order_quantity")];
			$po_color_size_array[$row[csf("id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["order_id"].=$row[csf("id")].",";
			$po_color_size_array[$row[csf("id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["remarks"].=$row[csf("remarks")].",";
			$order_id_arr[$row[csf("id")]]=$row[csf("id")];
		}
	}
	else if ($rept_type==3)  //show button
	{ 
		if(count($sql_res)==0 || $job_no=="")
		{
			?>
			<div style="text-align: center;font-size: 20px;font-weight: bold;color: red;">Data not found.</div>
			<?
			die();
		}
		foreach ($sql_res as $row) 
		{
			$color_size_array[$row[csf("item_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["po_number"].=$row[csf("po_number")];
			$color_size_array[$row[csf("item_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["order_qty"]+=$row[csf("order_quantity")];
			$color_size_array[$row[csf("item_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["order_id"].=$row[csf("id")].",";
			$color_size_array[$row[csf("item_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["remarks"].=$row[csf("remarks")].",";
			$order_id_arr[$row[csf("id")]]=$row[csf("id")];
		}
	}

    /*===============================================================================/
    /                                  Job Image                                     /
    /============================================================================== */

    $imge_arr = return_library_array("SELECT master_tble_id,image_location from common_photo_library where file_type=1 and master_tble_id='$job_no'", 'master_tble_id', 'image_location');
    // print_r($imge_arr);
	
	$order_id=implode(",",$order_id_arr);
	 //echo $order_id;
	//  echo "<pre>"; print_r($sql_res); die;
	foreach ($sql_res as  $row) 
	{
		//$job_no.=$row[csf("job_no")].",";
		//$item_number.=$garments_item[$row[csf("item_number_id")]].",";
		$style_ref_no.=$row[csf("style_ref_no")].",";
		$int_ref_no.=$row[csf("grouping")].",";
		//$product_dept.=$row[csf("product_dept")].",";
		//$buyer_name.=$buyer_arr[$row[csf("buyer_name")]].",";
		$season.=$buyer_season_arr[$row[csf("season_buyer_wise")]].",";
		if ($row[csf("brand_id")]) {$brand.=$buyer_brand_arr[$row[csf("brand_id")]].",";}
		$po_received_date.=change_date_format($row[csf("po_received_date")]).",";
		$shipment_date.=change_date_format($row[csf("shipment_date")]).",";
	}
	//$job_no=implode(", ",array_unique(explode(",",chop($job_no,','))));
	//$buyer_name=implode(", ",array_unique(explode(",",chop($buyer_name,','))));
	$style_ref_no=implode(", ",array_unique(explode(",",chop($style_ref_no,','))));
	$int_ref_no=implode(", ",array_unique(explode(",",chop($int_ref_no,','))));
	//$product_dept=implode(", ",array_unique(explode(",",chop($product_dept,','))));
	//$item_number=implode(", ",array_unique(explode(",",chop($item_number,','))));
	$po_received_date=implode(", ",array_unique(explode(",",chop($po_received_date,','))));
	$shipment_date=implode(", ",array_unique(explode(",",chop($shipment_date,','))));
	$brand=implode(", ",array_unique(explode(",",chop($brand,','))));
	$season=implode(", ",array_unique(explode(",",chop($season,','))));
	
    $div_width=1950;
    $table_width=1900;
    ob_start();
	if ($rept_type==1)// Show Button
	{
		?>
	    <div style="width:100%">
	        <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
	            <caption style="font-size:16px; font-weight:bold;">
				<?
	            $com_name = str_replace( "'", "", $cbo_company_name );
	            echo $company_arr[$com_name]."<br/>"."CLOSING REPORT [CUTTING SECTION]";
	            ?>
	          
	                <div style="color:red; text-align:left; font-size:14px;"></div>
	            </caption>
	        </table>
			<br>
			<!-- ========= JOB NO Table ============== -->
			<div style="width: 10%;float: left;">						 
				<img class="zoom" src='../../<?= $imge_arr[$job_no]; ?>' width='70%' height='70%' />					
			</div>
			<div  style="width: 90%;float: left;">
				<table class="rpt_table" width="950px" cellpadding="0" cellspacing="0" border="1" rules="all">
	                <tr>  
	                    <td width="120"><strong>Job No</strong></td>
	                    <td width="120"><?=$row[csf("job_no")];?></td>
	                    <td width="120"><strong>STYLE NO</strong></td>
	                    <td width="120"><?=$style_ref_no;?></td>
	                    <td width="120"><strong>BUYER</strong></td>
	                    <td width="120"><?=$buyer_arr[$row[csf("buyer_name")]];?></td>
	                </tr>
	                <tr>
	                    <td ><strong>Item Name</strong></td>
	                    <td><?=$garments_item[$row[csf("gmts_item_id")]];?></td>
	                    <td ><strong>Prod. Dept.</strong></td>
	                    <td><?=$product_dept[$row[csf("product_dept")]];?></td>
	                    <td><strong>Job Receive Date</strong></td>
	                    <td><?=$po_received_date;?></td>
	                </tr>
	                <tr>
	                    <td ><strong>Brand Name</strong></td>
	                    <td><?=$brand;?></td>
	                    <td ><strong>Season</strong></td>
	                    <td><?=$season;?></td>
	                    <td><strong>Shipment Date</strong></td>
	                    <td><?=$shipment_date;?></td>
	                </tr>
	                <tr>
	                    <td ><strong>IR/IB</strong></td>
	                    <td><?=$int_ref_no;?></td>
	                </tr>
		        </table>
	        </div>
	        <br clear="all">
	    </div>
		<br>
		<!-- ========= Shipment Date Table ============== -->
		<div style="width:100%">
			<table class="rpt_table" width="550px" cellpadding="0" cellspacing="0" border="1" rules="all">
	                 <thead>
					 <tr>
                        <th><strong>PO No.</strong></th>
                        <th><strong>Shipment Date</strong></th>
                        <th><strong>Color</strong></th>
                        <th><strong>Color Close</strong></th>
                        <th><strong>Closing Date</strong></th>
                    </tr>
					 </thead>
				<?
				$color_con = str_replace("c.color_number_id", "c.color_id", $color_con);
				/* $sql_ship="SELECT a.id,b.id as mst_id,a.po_number,a.po_quantity,a.shipment_date,c.color_id,SUM(c.qc_pass_qty) as qc_pass_qty,b.cutting_qc_date,d.order_qty
				from wo_po_break_down a, pro_gmts_cutting_qc_mst b,pro_gmts_cutting_qc_dtls c,ppl_cut_lay_dtls d ,ppl_cut_lay_bundle e
				Where b.job_no='$job_no'
				and a.job_no_mst=b.job_no
				and c.mst_id=b.id
				and c.order_id=a.id
				and b.company_id=$company_name $color_con
				and c.color_id=d.color_id
				and d.mst_id=e.mst_id 
				and a.id=e.order_id
				and a.status_active=1 and a.is_deleted=0
				and b.status_active=1 and b.is_deleted=0
				and c.status_active=1 and c.is_deleted=0
				group by a.id,b.id,a.po_number,a.po_quantity,a.shipment_date,c.color_id,b.cutting_qc_date,d.order_qty order by a.id,b.id";
				 */
				$sql_ship="SELECT a.id,b.id as mst_id,a.po_number,a.po_quantity as order_qty,a.shipment_date,c.color_id,SUM(c.qc_pass_qty) as qc_pass_qty,b.cutting_qc_date
				from wo_po_break_down a, pro_gmts_cutting_qc_mst b,pro_gmts_cutting_qc_dtls c
				Where b.job_no='$job_no'
				and a.job_no_mst=b.job_no
				and c.mst_id=b.id
				and c.order_id=a.id
				and b.company_id=$company_name $color_con 
				and a.status_active=1 and a.is_deleted=0
				and b.status_active=1 and b.is_deleted=0
				and c.status_active=1 and c.is_deleted=0
				group by a.id,b.id,a.po_number,a.po_quantity,a.shipment_date,c.color_id,b.cutting_qc_date order by a.id,b.id";
				//echo $sql_ship;die;
				$sql_ress_fabric=sql_select($sql_ship);
				$ship_data_arr=array();
				foreach ($sql_ress_fabric as $row) 
				{
					$ship_data_arr[$row[csf("id")]][$row[csf("color_id")]]["po_number"]=$row[csf("po_number")];
					$ship_data_arr[$row[csf("id")]][$row[csf("color_id")]]["shipment_date"]=$row[csf("shipment_date")];
					$ship_data_arr[$row[csf("id")]][$row[csf("color_id")]]["color_id"]=$row[csf("color_id")];
					$ship_data_arr[$row[csf("id")]][$row[csf("color_id")]]["qc_pass_qty"]+=$row[csf("qc_pass_qty")];
					$ship_data_arr[$row[csf("id")]][$row[csf("color_id")]]["order_qty"]=$row[csf("order_qty")];
					$ship_data_arr[$row[csf("id")]][$row[csf("color_id")]]["cutting_qc_date"]=$row[csf("cutting_qc_date")];
				}
				foreach ($ship_data_arr as $po_id => $po_value) 
				{
					foreach ($po_value as $color_id => $value) {
						$count_po[$po_id]++;
					}
				}
				?>
				<tbody>
					<?php 
					$po_chk=array();
					foreach ($ship_data_arr as $po_id => $po_value)  
					{
						foreach ($po_value as  $row)
						{
							$rowspan=$count_po[$po_id];	
							?>
							<tr>
							<?
								if(!in_array($row["po_number"],$po_chk))
								{
									$po_chk[]=$row["po_number"];
									?>
									<td rowspan="<?=$rowspan;?>"><?=$row["po_number"];?></td>
									<td rowspan="<?=$rowspan;?>"><?=$row["shipment_date"];?></td>
									<?
								}
									?>
									<td><?=$color_library[$row["color_id"]];?></td>
									<? 
									//echo $row["order_qty")]."<br/>".$row["qc_pass_qty")];
								if ($row["order_qty"]<=$row["qc_pass_qty"])
								{
									?>
									<td>Yes</td>
									<td>
										<? echo date("j-M-Y",strtotime(str_replace("'","",$row["cutting_qc_date"])));?>
									</td>
									<?
								}else 
								{
									?>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<?
								}
								?>
							</tr>
								<?
							}
						}
								?>
				</tbody>
		    </table>
	    </div>
		<br>
		<!-- ========= Fabric Details Table ============== -->
		<div style="width:100%">
			<table class="rpt_table" width="1020" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th colspan="10">Fabric Details</th>
					</tr>
					
					<tr>
						<th width="120" rowspan="2">PART</th>
						<th width="70" rowspan="2">FABRIC TYPE</th>
						<th width="100" rowspan="2">REQUIRED QTY[Kg]</th>
						<th width="120" rowspan="2">RECEIVED QTY(KG) BY STORE</th>
						<th width="80" rowspan="2">Return to Store</th>
						<th width="80" rowspan="2">Cutting Receive</th>
						<th width="200"  colspan="2">CONSUMPTION</th>
						<th width="160" rowspan="2">REMARKS</th>
					</tr>
					<tr>
						<th width="100">GIVEN</th>
						<th width="100">ACTUAL</th>
					</tr>
				</thead>
				<?php
				$sql_body_part="SELECT body_part_id,construction,composition,plan_cut_qty,lib_yarn_count_deter_id,avg_finish_cons
				FROM wo_pre_cost_fabric_cost_dtls 
				WHERE company_id='$company_name'
				and job_no='$job_no'
				and status_active=1 and is_deleted=0";

				// echo $sql_body_part;
				$sql_arr=sql_select($sql_body_part);
				$body_part_arr=array();
				$body_part_count_arr=array();

				foreach ($sql_arr as $row) 
				{
					$body_part_arr[$row["BODY_PART_ID"]][$row["LIB_YARN_COUNT_DETER_ID"]]["PLAN_CUT_QTY"]+=$row["PLAN_CUT_QTY"];

					$body_part_arr[$row["BODY_PART_ID"]][$row["LIB_YARN_COUNT_DETER_ID"]]["CONSTRUCTION"]=$row["CONSTRUCTION"];
					$body_part_arr[$row["BODY_PART_ID"]][$row["LIB_YARN_COUNT_DETER_ID"]]["AVG_FINISH_CONS"]+=$row["AVG_FINISH_CONS"];
					$body_part_count_arr[$row["BODY_PART_ID"]][$row["LIB_YARN_COUNT_DETER_ID"]]++;
				}
				// echo "<pre>";
				// print_r($body_part_arr);
				// echo "</pre>";
				// ================================== FF Rcv ================================
				$sql_rcv="SELECT A.BODY_PART_ID,A.FABRIC_DESCRIPTION_ID,A.RECEIVE_QNTY,B.QNTY,B.QC_PASS_QNTY,B.ENTRY_FORM
				FROM PRO_FINISH_FABRIC_RCV_DTLS A,PRO_ROLL_DETAILS B
				WHERE 
				B.PO_BREAKDOWN_ID IN ($order_id)
				AND A.ID=B.DTLS_ID
				AND B.ENTRY_FORM IN (68,126)
				AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0
				AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0";
				//echo $sql_rcv;
				$sql_rcv_arr=sql_select($sql_rcv);
				$body_part_rcv_arr=array();
				foreach ($sql_rcv_arr as $row) 
				{
					$body_part_rcv_arr[$row["BODY_PART_ID"]][$row["FABRIC_DESCRIPTION_ID"]][$row["ENTRY_FORM"]]["RCV_QNTY"]+=$row["RECEIVE_QNTY"];
					$body_part_rcv_arr[$row["BODY_PART_ID"]][$row["FABRIC_DESCRIPTION_ID"]][$row["ENTRY_FORM"]]["QC_PASS_QNTY"]+=$row["QC_PASS_QNTY"];
					$body_part_rcv_arr[$row["BODY_PART_ID"]][$row["FABRIC_DESCRIPTION_ID"]][$row["ENTRY_FORM"]]["QNTY"]+=$row["QNTY"];
				}
				// echo "<pre>";print_r($body_part_rcv_arr);
				//  ================================ FF Issue ================================
				$sql_issue="SELECT B.BODY_PART_ID,B.ISSUE_QNTY,c.DETARMINATION_ID FROM PRO_ROLL_DETAILS A, INV_FINISH_FABRIC_ISSUE_DTLS B,PRODUCT_DETAILS_MASTER c 
				WHERE A.PO_BREAKDOWN_ID in ($order_id) and b.prod_id=c.id and c.item_category_id=2
				AND A.DTLS_ID=B.ID 
				AND A.ENTRY_FORM=71 
				AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 
				AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0";
				// echo $sql_issue;
				$sql_issue_arr=sql_select($sql_issue);
				$body_issue_arr=array();
				foreach ($sql_issue_arr as $row) 
				{
					$body_issue_arr[$row["BODY_PART_ID"]][$row["DETARMINATION_ID"]]+=$row["ISSUE_QNTY"];
				}

				$condition= new condition();     
			    $condition->po_id_in($order_id);     
			    $condition->init();

				$fabric= new fabric($condition);
				// echo $fabric->getQuery();
			    $fabric_req_qty_arr= $fabric->getQtyArray_by_JobColorFabricColorBodyPartIdDeterminIdGsmAndDiaWidth_knitAndwoven_greyAndfinish();
			    // echo "<pre>";print_r($fabric_req_qty_arr);die();
			    $fabric_req_qty_array = array();
			    foreach ($fabric_req_qty_arr['knit']['finish'] as $job_key => $job_value) 
			    {
			    	foreach ($job_value as $gc_key => $gc_value) 
			    	{
			    		foreach ($gc_value as $fc_key => $fc_value) 
			    		{
			    			foreach ($fc_value as $bpid_key => $bp_value) 
			    			{
			    				foreach ($bp_value as $dtr_key => $dtr_value) 
			    				{
			    					foreach ($dtr_value as $gsm => $gsm_value) 
			    					{
			    						foreach ($gsm_value as $dia_key => $dia_value) 
			    						{
			    							foreach ($dia_value as $uom_key => $val) 
			    							{
			    								// print_r($val);echo "<br>";
			    								$fabric_req_qty_array[$job_key][$bpid_key][$dtr_key] += array_sum($val);
			    							}
			    						}
			    					}
			    				}
			    			}
			    		}
			    	}
			    }
			    // echo "<pre>";print_r($fabric_req_qty_array);die();
				?>
				<tbody>
					<?
					foreach ($body_part_arr as $bod_key => $body_part_data) 
					{ 
						foreach ($body_part_data as $fabric_key => $row) 
						{
							$body_part_count = $body_part_count_arr[$bod_key][$fabric_key];
							?>
							<tr>
								<td align="left" title="<?=$bod_key;?>"><? echo $body_part[$bod_key];?></td>
								<td align="left" title="<?=$fabric_key;?>"><? echo $row["CONSTRUCTION"];?></td>
								<td align="right">
									<? echo number_format($fabric_req_qty_array[$job_no][$bod_key][$fabric_key],2);?>
								</td>
								<td align="right">
									<? echo number_format($body_part_rcv_arr[$bod_key][$fabric_key][68]["RCV_QNTY"],2);?>
								</td>
								<td align="right">
									<? echo number_format($body_part_rcv_arr[$bod_key][$fabric_key][126]["QNTY"],2);?></td>
								<td align="right">
									<? echo number_format($body_issue_arr[$bod_key][$fabric_key],2);?>
								
								</td>
							
								<td align="right">
									<? echo number_format(($row['AVG_FINISH_CONS']/$body_part_count),2); ?></td>
								
								<td align="right"></td>
								<td align="right"></td>
							</tr>
							<?
						}
					}
					?>
				</tbody>
			</table>
	    </div>
		<br>
		<!-- =====================Details Part=================== -->
		<?php 
		$sql_cutting="SELECT b.color_id,a.order_id,a.size_qty,a.size_id,b.order_qty
			from ppl_cut_lay_bundle a,ppl_cut_lay_dtls b
			Where 
			a.dtls_id=b.id
			and b.mst_id=a.mst_id
			and a.status_active=1 and a.is_deleted=0
			and b.status_active=1 and b.is_deleted=0
			and a.order_id in($order_id)";
		// echo $sql_cutting;
		$sql_cutting_data=sql_select($sql_cutting);
		$cut_and_lay_data_array=array();
		foreach ($sql_cutting_data as  $row) 
		{
			$cut_and_lay_data_array[$row["COLOR_ID"]][$row["SIZE_ID"]]["layqty"]+=$row["SIZE_QTY"];
			// $color_count[$row["COLOR_ID"]]++;
		}
		//echo $sql_cutting;
		$sql_production="SELECT b.production_qnty,b.reject_qty,b.replace_qty,b.bundle_qty,b.alter_qty,b.spot_qty,b.production_type,c.color_number_id,c.order_quantity,c.size_number_id,c.po_break_down_id,c.plan_cut_qnty,a.embel_name
			from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown c 
			Where a.company_id = $company_name
			and c.id=b.color_size_break_down_id
			and b.mst_id=a.id
			and b.production_type in (1,2,3,4,5,64)
			and a.status_active=1 and a.is_deleted=0
			and b.status_active=1 and b.is_deleted=0
			and c.status_active in(1,2,3) and c.is_deleted=0
			and c.job_id = '$job_id_hidden'
			and a.po_break_down_id in($order_id)
			";
			//echo $sql_production;

			$sql_production_data=sql_select($sql_production);
			$production_data=array();
			foreach ($sql_production_data as  $val) 
			{
				$production_data[$val[csf("color_number_id")]][$val[csf("size_number_id")]][$val[csf("production_type")]]["pqnty"]+=$val[csf("production_qnty")];

				$production_data[$val[csf("color_number_id")]][$val[csf("size_number_id")]][$val[csf("production_type")]]["bundle_qty"]+=$val[csf("bundle_qty")];

				$production_data[$val[csf("color_number_id")]][$val[csf("size_number_id")]][$val[csf("production_type")]]["replace_qty"]+=$val[csf("replace_qty")];

				$production_data[$val[csf("color_number_id")]][$val[csf("size_number_id")]][$val[csf("production_type")]][$val[csf("embel_name")]]["reject_qty"]+=$val[csf("reject_qty")];

				$production_data[$val[csf("color_number_id")]][$val[csf("size_number_id")]][$val[csf("production_type")]]["alter_qty"]+=$val[csf("alter_qty")];

				$production_data[$val[csf("color_number_id")]][$val[csf("size_number_id")]][$val[csf("production_type")]]["spot_qty"]+=$val[csf("spot_qty")];

				$production_data[$val[csf("color_number_id")]][$val[csf("size_number_id")]][$val[csf("production_type")]]["order_quantity"]+=$val[csf("order_quantity")];

				$production_data[$val[csf("color_number_id")]][$val[csf("size_number_id")]][$val[csf("production_type")]]["plan_cut_qnty"]+=$val[csf("plan_cut_qnty")];
			}
			// echo "<pre>";
			// print_r($production_data);
			// echo "</pre>";
		?>
		<!-- ========= Details Table ============== -->
		<div style="<? echo $div_width+40; ?>px;">
			<table class="rpt_table" width="<? echo $table_width+80; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th width="80" rowspan="2">Color</th>
						<th width="80" rowspan="2">Size</th>
						<th width="80"  rowspan="2">Order Quantity</th>

						<th colspan="10">Cutting Details</th>
						<th colspan="14">QC Details (Rejection)</th>
						<th colspan="2">Physically Found</th>

						<th  width="120" rowspan="2">Remarks</th>
					</tr>
					<tr>
						<th width="80">Cut and Lay Quantity</th>
						<th width="50">%</th>
						<th width="80">Cutting QC Passed</th>
						<th width="50">%</th>
						<th width="80">Cutting QC WIP</th>
						<th width="50">%</th>
						<th width="80">Sewing Input </th>
						<th width="50">%</th>
						<th width="80">Input WIP</th>
						<th width="50">%</th>

						<th width="80">Cutting</th>
						<th width="50">%</th>
						<th width="80">Print</th>
						<th width="50">%</th>
						<th width="80">Emb</th>
						<th width="50">%</th>
						<th width="80">Sewing</th>
						<th width="50">%</th>
						<th width="80">Alter</th>
						<th width="50">%</th>
						<th width="80">Spot</th>
						<th width="50">%</th>
						<th width="80">Replace</th>
						<th width="50">%</th>

						<th width="80">Replace</th>
						<th width="50">%</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					$color_chk=array();
					foreach ($color_size_array as $color_key => $color_value) 
					{
						foreach ($color_value as $size_key => $row) 
						{
							$color_count[$color_key]++;
						}
					}
					$grand_total_color_qty=0;

					$grand_total_cut_lay_qty=0;
					$grand_total_cut_lay_percent=0;

					$grand_total_plan_cut_qnty=0;
					$grand_total_plan_cut_qnty_percent=0;	

					$grand_total_cutting_qc_wip=0;
					$grand_total_cutting_qc_wip_percent=0;

					$grand_total_prnting=0;
					$grand_total_prnting_percent=0;	

					$grand_total_emb=0;
					$grand_total_emb_percent=0;

					$grand_total_swing_output=0;
					$grand_total_swing_output_percent=0;

					$grand_total_swing_input_wip=0;
					$grand_total_swing_input_percent=0;	

					$grand_qs_pass_qty=0;
					$grand_total_qs_pass_percent=0;	

					$grand_total_alter_qty=0;
					$grand_total_alter_qty_percent=0;

					$grand_total_spot_qty=0;
					$grand_total_alter_qty_percent+=0;
						
					$grand_total_replace_qty=0;
					$grand_total_replace_qty_percent=0;
						
					foreach ($color_size_array as $color_key => $color_value)
					{
						$total_color_order_qty=0;
						$total_cut_lay_qty=0;
						$total_qs_pass_qty=0;
						$total_cutting_qc_wip=0;
						$total_swing_input_wip=0;
						$total_plan_cut_qnty=0;

						$total_swing_input=0;

						$total_prnting=0;
						$total_alter_qty=0;
						$total_spot_qty=0;
						$total_replace_qty=0;
						$total_qs_pass_percent=0;
						$total_cut_lay_percent=0;
						$total_plan_cut_qnty_percent=0;
						$total_cutting_qc_wip_percent=0;
						$total_swing_input_wip_percent=0;

						$total_swing_output=0;
						$total_swing_output_percent=0;

						$total_prnting_percent=0;
						$total_alter_qty_percent=0;
						$total_spot_qty_percent=0;
						$total_replace_qty_percent=0;

						foreach ($color_value as $size_key => $row) 
						{
							$cut_lay_qnty=$cut_and_lay_data_array[$color_key][$size_key]["layqty"];

							$pqrty=$production_data[$color_key][$size_key][1]["pqnty"];

							$swing_input=$production_data[$color_key][$size_key][4]["pqnty"];							
							$swing_output=$production_data[$color_key][$size_key][5]["pqnty"];							
							$prnting=$production_data[$color_key][$size_key][2]["pqnty"];
							$emb=$production_data[$color_key][$size_key][64]["pqnty"];

							$bundle_qty=$production_data[$color_key][$size_key][1]["bundle_qty"];

							
							$reject_qty=$production_data[$color_key][$size_key][1][0]["reject_qty"];
							$print_reject_qty=$production_data[$color_key][$size_key][3][1]["reject_qty"];
							$emb_reject_qty=$production_data[$color_key][$size_key][3][2]["reject_qty"];
							$sew_reject_qty=$production_data[$color_key][$size_key][5][0]["reject_qty"];
							$alter_qty=$production_data[$color_key][$size_key][4]["alter_qty"];
							$spot_qty=$production_data[$color_key][$size_key][4]["spot_qty"];
							$replace_qty=$production_data[$color_key][$size_key][4]["replace_qty"];
							$order_quantity=$production_data[$color_key][$size_key][1]["order_quantity"];
							$plan_cut_qnty=$production_data[$color_key][$size_key][1]["plan_cut_qnty"];
							//echo $pqrty."test";  
                            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
								<? 
								if(!in_array($color_key,$color_chk))
								{
									$color_chk[]=$color_key;
									?>
									<td valign="middle" align="center" rowspan="<?=$color_count[$color_key];?>"><strong>
										<?=$color_library[$color_key];?>
									</strong></td>
									<?
								}?>
								<td align="right"><?=$size_library[$size_key];?></td>
								<td align="right"><?=$row["order_qty"];?></td>
								<td align="right"><?=$cut_lay_qnty;?></td>
								<td align="right">
									<?
									$cut_lay_percent=($row["order_qty"]>0) ? $cut_lay_qnty/$row["order_qty"]*100 : 0;
									echo number_format($cut_lay_percent,2)."%";
									?>
								</td>
								<td align="right"><?=$pqrty;?></td>
								<td align="right">
									<?
									$qs_pass_percent=($row["order_qty"]>0) ? $pqrty/$row["order_qty"]*100 : 0;
									echo number_format($qs_pass_percent,2)."%";
									?>
								</td>
								<td align="right">
									<?=$cutting_qc_wip=$row["order_qty"]-$pqrty;?>

								</td>
								<td align="right">
									<?
									$cutting_qc_wip_percent=($row["order_qty"]>0) ? $cutting_qc_wip/$row["order_qty"]*100 : 0;
									echo number_format($cutting_qc_wip_percent,2)."%";
									?>
								</td>
								<td align="right"><?=$swing_input?></td>
								<td align="right">
									<?
									$swing_input_percent=($row["order_qty"]>0) ? $swing_input/$row["order_qty"]*100 : 0;
									echo number_format($swing_input_percent,2)."%";
									?>
								</td>
								<td align="right"><?=$swing_input_wip=$row["order_qty"]-$swing_input;?></td>
								<td align="right">
									<?
									$swing_input_wip_percent=($row["order_qty"]>0) ? $swing_input_wip/$row["order_qty"]*100 : 0;
									echo number_format($swing_input_wip_percent,2)."%";
									?>
								</td>






								<td align="right"><?=number_format($reject_qty,0);?></td>
								<td align="right">
									<?
									$plan_cut_qnty_percent=($row["order_qty"]>0) ? $reject_qty/$row["order_qty"]*100 : 0;
									echo number_format($plan_cut_qnty_percent,2)."%";
									?>
								</td>
								<td align="right"><?=number_format($print_reject_qty,0);?></td>
								<td align="right">
									<?
									$prnting_percent=($row["order_qty"]>0) ? $print_reject_qty/$row["order_qty"]*100 : 0;
									echo number_format($prnting_percent,2)."%";
									?>
								</td>
								<td align="right"><?=number_format($emb_reject_qty,0);?></td>
								<td align="right">
									<?
									$emb_percent=($row["order_qty"]>0) ? $emb_reject_qty/$row["order_qty"]*100 : 0;
									echo number_format($emb_percent,2)."%";
									?>
								</td>
								<td align="right"><?=number_format($sew_reject_qty,0)?></td>
								<td align="right">
									<?
									$swing_output_percent=($row["order_qty"]>0) ? $sew_reject_qty/$row["order_qty"]*100 : 0;
									echo number_format($swing_output_percent,2)."%";
									?>
								</td>
								<td align="right"><?=number_format($alter_qty,0);?></td>
								<td align="right">
									<?
									$alter_qty_percent=($row["order_qty"]>0) ? $alter_qty/$row["order_qty"]*100 : 0;
									echo number_format($alter_qty_percent,2)."%";
									?>
								</td>
								<td align="right"><?=number_format($spot_qty,0);?></td>
								<td align="right">
									<?
									$spot_qty_percent=($row["order_qty"]>0) ? $spot_qty/$row["order_qty"]*100 : 0;
									echo number_format($spot_qty_percent,2)."%";
									?>
								</td>
								<td align="right"><?=number_format($replace_qty,0);?></td>
								<td align="right">
									<?
									$replace_qty_percent=($row["order_qty"]>0) ? $replace_qty/$row["order_qty"]*100 : 0;
									echo number_format($replace_qty_percent,2)."%";
									?>
								</td>

								<td align="right"></td>
								<td align="right">
								</td>
								<td align="right"></td>
							</tr>
							<?
							$i++;
							$total_color_order_qty+=$row["order_qty"];
							$total_cut_lay_qty+=$cut_lay_qnty;
							$total_cut_rej_qnty+=$reject_qty;
							$total_qs_pass_qty+=$pqrty;
							$total_cutting_qc_wip+=$cutting_qc_wip;
							$total_swing_input+=$swing_input;
							$total_swing_input_percent+=$swing_input_percent;
							$total_swing_input_wip+=$swing_input_wip;
							$total_swing_input_wip_percent+=$swing_input_wip_percent;
							$total_print_reject_qty+=$print_reject_qty;
							$total_swing_output+=$sew_reject_qty;
							$total_swing_output_percent+=$swing_output_percent;

							$total_alter_qty+=$alter_qty;
							$total_spot_qty+=$spot_qty;
							$total_replace_qty+=$replace_qty;
							$total_cut_lay_percent+=$cut_lay_percent;
							$total_cut_rej_qnty_percent+=$plan_cut_qnty_percent;
							$total_qs_pass_percent+=$qs_pass_percent;
							$total_cutting_qc_wip_percent+=$cutting_qc_wip_percent;

							$print_reject_qty_percent+=$prnting_percent;
							$total_emb+=$emb_reject_qty;
							$total_emb_percent+=$emb_percent;


							$total_alter_qty_percent+=$alter_qty_percent;
							$total_spot_qty_percent+=$spot_qty_percent;
							$total_replace_qty_percent+=$replace_qty_percent;
						}
						?>
						
						<tr style="text-align: right;font-weight: bold;background: #cddcdc;">
							<td colspan="2" align="right"><strong>Color Total</strong></td>
							<td align="right"><strong><?=$total_color_order_qty;?></strong></td>

							<td align="right"><strong><?=$total_cut_lay_qty?></strong></td>
							<td align="right"><strong><?=number_format((($total_cut_lay_qty/$total_color_order_qty)*100),2)."%";?></strong></td>

							<td align="right"><strong><?=$total_qs_pass_qty;?></strong></td>
							<td align="right"><strong><?=number_format(($total_qs_pass_qty/$total_color_order_qty)*100,2)."%";?></strong></td>

							<td align="right"><strong><?=$total_cutting_qc_wip;?></strong></td>
							<td align="right"><strong><?=number_format(($total_cutting_qc_wip/$total_color_order_qty)*100,2)."%";?></strong></td>
						
							<td align="right"><strong><?=$total_swing_input;?></strong></td>

							<td align="right"><strong><?=number_format(($total_swing_input/$total_color_order_qty)*100,2)."%";
							
							?></strong></td>
							<td align="right"><strong><?=$total_swing_input_wip;?></strong></td>
							<td align="right"><strong><?=number_format(($total_swing_input_wip/$total_color_order_qty)*100,2)."%";?></strong></td>



							
							<td align="right"><strong><?=$total_cut_rej_qnty?></strong></td>
							<td align="right"><strong><?=number_format(($total_cut_rej_qnty/$total_color_order_qty)*100,2)."%";?></strong></td>

							<td align="right"><strong><?=$total_print_reject_qty?></strong></td>
							<td align="right"><strong><?=number_format(($total_print_reject_qty/$total_color_order_qty)*100,2).'%';?></strong></td>
							
							<td align="right"><strong><?=$total_emb;?></strong></td>
							<td align="right"><strong><?=number_format(($total_emb/$total_color_order_qty)*100,2).'%' ;?></strong></td>

							<td align="right"><strong><?=$total_swing_output;?></strong></td>
							<td align="right"><strong><?=number_format(($total_swing_output/$total_color_order_qty)*100,2).'%';?></strong></td>

							<td align="right"><strong><?=$total_alter_qty?></strong></td>
							<td align="right"><strong><?=number_format(($total_alter_qty/$total_color_order_qty)*100,2)."%";?></strong></td>



							<td align="right"><strong><?=$total_spot_qty?></strong></td>
							<td align="right"><strong><?=number_format(($total_spot_qty/$total_color_order_qty)*100,2)."%";?></strong></td>

							<td align="right"><strong><?=$total_replace_qty?></strong></td>
							<td align="right"><strong><?=number_format(($total_replace_qty/$total_color_order_qty)*100,2)."%";?></strong></td>

							<td></td>
							<td></td>
							<td></td>
					
						</tr>
						<?	
						$grand_total_color_qty+=$total_color_order_qty;

						$grand_total_cut_lay_qty+=$total_cut_lay_qty;
						$grand_total_cut_lay_percent+=$total_cut_lay_percent;

						$grand_total_cut_rej_qnty+=$total_cut_rej_qnty;
						$grand_total_cut_rej_qnty_percent+=$total_cut_rej_qnty_percent;

						$grand_qs_pass_qty+=$total_qs_pass_qty;	
						$grand_total_qs_pass_percent+=$total_qs_pass_percent;

						$grand_total_cutting_qc_wip+=$total_cutting_qc_wip;	
						$grand_total_cutting_qc_wip_percent+=$total_cutting_qc_wip_percent;	
						
						$grand_total_swing_input+=$total_swing_input;
						$grand_total_swing_input_percent+=$total_swing_input_percent;	

						$grand_total_swing_input_wip+=$total_swing_input_wip;
						$grand_total_swing_input_wip_percent+=$total_swing_input_wip_percent;	

						$grand_print_reject_qty+=$total_print_reject_qty;
						$grand_print_reject_qty_percent+=$print_reject_qty_percent;

						$grand_total_emb+=$total_emb;
						$grand_total_emb_percent+=$total_emb_percent;

						$grand_total_swing_output+=$total_swing_output;
						$grand_total_swing_output_percent+=$total_swing_output_percent;	

						$grand_total_alter_qty+=$total_alter_qty;
						$grand_total_alter_qty_percent+=$total_alter_qty_percent;

						$grand_total_spot_qty+=$total_spot_qty;
						$grand_total_spot_qty_percent+=$total_spot_qty_percent;

						$grand_total_replace_qty+=$total_replace_qty;
						$grand_total_replace_qty_percent+=$total_replace_qty_percent;	
						
					}
					?>
					</tbody>
					<tfoot>						
						<tr>
							<th colspan="2" align="right"><strong>Grand Total</strong></th>
							<th align="right"><strong><?=$grand_total_color_qty;?></strong></th>
							<th align="right"><strong><?=$grand_total_cut_lay_qty;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_cut_lay_qty/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th align="right"><strong><?=$grand_qs_pass_qty;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_qs_pass_qty/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th align="right"><strong><?=$grand_total_cutting_qc_wip;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_cutting_qc_wip/$grand_total_color_qty)*100),2)."%";?></strong></th>
							
							<th align="right"><strong><?=$grand_total_swing_input;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_swing_input/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th align="right"><strong><?=$grand_total_swing_input_wip;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_swing_input_wip/$grand_total_color_qty)*100),2)."%";?></strong></th>



							<th align="right"><strong><?=$grand_total_cut_rej_qnty;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_cut_rej_qnty/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th align="right"><strong><?=$grand_print_reject_qty;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_print_reject_qty/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th align="right"><strong><?=$grand_total_emb;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_emb/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th align="right"><strong><?=$grand_total_swing_output;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_swing_output/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th align="right"><strong><?=$grand_total_alter_qty;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_alter_qty/$grand_total_color_qty)*100),2)."%";?></strong></th>
							
							<th align="right"><strong><?=$grand_total_spot_qty;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_spot_qty/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th align="right"><strong><?=$grand_total_replace_qty;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_replace_qty/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th></th>
							<th></th>

							<th></th>
						
							
						</tr>
					</tfoot>
				</table>
	    </div>
		<?
	}
	if ($rept_type==2)// Show2 Button
	{
		?>
	    <div style="width:100%">
	        <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
	            <caption style="font-size:16px; font-weight:bold;">
				<?
	            $com_name = str_replace( "'", "", $cbo_company_name );
	            echo $company_arr[$com_name]."<br/>"."CLOSING REPORT [CUTTING SECTION]";
	            ?>
	          
	                <div style="color:red; text-align:left; font-size:14px;"></div>
	            </caption>
	        </table>
			<br>
			<!-- ========= JOB NO Table ============== -->
			<div style="width: 10%;float: left;">						 
				<img class="zoom" src='../../<?= $imge_arr[$job_no]; ?>' width='70%' height='70%' />					
			</div>
			<div  style="width: 90%;float: left;">
				<table class="rpt_table" width="950px" cellpadding="0" cellspacing="0" border="1" rules="all">
	                <tr>  
	                    <td width="120"><strong>Job No</strong></td>
	                    <td width="120"><?=$row[csf("job_no")];?></td>
	                    <td width="120"><strong>STYLE NO</strong></td>
	                    <td width="120"><?=$style_ref_no;?></td>
	                    <td width="120"><strong>BUYER</strong></td>
	                    <td width="120"><?=$buyer_arr[$row[csf("buyer_name")]];?></td>
	                </tr>
	                <tr>
	                    <td ><strong>Item Name</strong></td>
	                    <td><?=$garments_item[$row[csf("gmts_item_id")]];?></td>
	                    <td ><strong>Prod. Dept.</strong></td>
	                    <td><?=$product_dept[$row[csf("product_dept")]];?></td>
	                    <td><strong>Job Receive Date</strong></td>
	                    <td><?=$po_received_date;?></td>
	                </tr>
	                <tr>
	                    <td ><strong>Brand Name</strong></td>
	                    <td><?=$brand;?></td>
	                    <td ><strong>Season</strong></td>
	                    <td><?=$season;?></td>
	                    <td><strong>Shipment Date</strong></td>
	                    <td><?=$shipment_date;?></td>
	                </tr>
		        </table>
	        </div>
	        <br clear="all">
	    </div>
		<br>
		<!-- ========= Shipment Date Table ============== -->
		<div style="width:100%">
			<table class="rpt_table" width="550px" cellpadding="0" cellspacing="0" border="1" rules="all">
	                 <thead>
					 <tr>
                        <th><strong>PO No.</strong></th>
                        <th><strong>Shipment Date</strong></th>
                        <th><strong>Color</strong></th>
                        <th><strong>Color Close</strong></th>
                        <th><strong>Closing Date</strong></th>
                    </tr>
					 </thead>
				<?
				$color_con = str_replace("c.color_number_id", "c.color_id", $color_con);
				$sql_ship="SELECT a.id,b.id as mst_id,a.po_number,a.po_quantity as order_qty,a.shipment_date,c.color_id,SUM(c.qc_pass_qty) as qc_pass_qty,b.cutting_qc_date
				from wo_po_break_down a, pro_gmts_cutting_qc_mst b,pro_gmts_cutting_qc_dtls c
				Where $job_cond a.job_no_mst=b.job_no
				and c.mst_id=b.id
				and c.order_id=a.id
				and b.company_id=$company_name $color_con $po_cond 
				and a.status_active=1 and a.is_deleted=0
				and b.status_active=1 and b.is_deleted=0
				and c.status_active=1 and c.is_deleted=0
				group by a.id,b.id,a.po_number,a.po_quantity,a.shipment_date,c.color_id,b.cutting_qc_date order by a.id,b.id";
				//echo $sql_ship;die;
				$sql_ress_fabric=sql_select($sql_ship);
				$ship_data_arr=array();
				foreach ($sql_ress_fabric as $row) 
				{
					$ship_data_arr[$row[csf("id")]][$row[csf("color_id")]]["po_number"]=$row[csf("po_number")];

					$ship_data_arr[$row[csf("id")]][$row[csf("color_id")]]["shipment_date"]=$row[csf("shipment_date")];

					$ship_data_arr[$row[csf("id")]][$row[csf("color_id")]]["color_id"]=$row[csf("color_id")];

					$ship_data_arr[$row[csf("id")]][$row[csf("color_id")]]["qc_pass_qty"]+=$row[csf("qc_pass_qty")];

					$ship_data_arr[$row[csf("id")]][$row[csf("color_id")]]["order_qty"]=$row[csf("order_qty")];

					$ship_data_arr[$row[csf("id")]][$row[csf("color_id")]]["cutting_qc_date"]=$row[csf("cutting_qc_date")];

					
				}
				foreach ($ship_data_arr as $po_id => $po_value) 
				{
					foreach ($po_value as $color_id => $value) {
						$count_po[$po_id]++;
					}
				}
				?>
				<tbody>
					<?php 
					$po_chk=[];
					foreach ($ship_data_arr as $po_id => $po_value)  
					{
						foreach ($po_value as  $row)
						{
							$rowspan=$count_po[$po_id];	
							?>
							<tr>
							<?
								if(!in_array($row["po_number"],$po_chk))
								{
									$po_chk[]=$row["po_number"];
									?>
									<td rowspan="<?=$rowspan;?>"><?=$row["po_number"];?></td>
									<td rowspan="<?=$rowspan;?>"><?=$row["shipment_date"];?></td>
									<?
								}
									?>
									<td><?=$color_library[$row["color_id"]];?></td>
									<? 
									//echo $row["order_qty")]."<br/>".$row["qc_pass_qty")];
								if ($row["order_qty"]<=$row["qc_pass_qty"])
								{
									?>
									<td>Yes</td>
									<td>
										<? echo date("j-M-Y",strtotime(str_replace("'","",$row["cutting_qc_date"])));?>
									</td>
									<?
								}else 
								{
									?>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<?
								}
								?>
							</tr>
								<?
							}
						}
								?>
				</tbody>
		    </table>
	    </div>
		<br>
		<!-- ========= Fabric Details Table ============== -->
		<div style="width:100%">
			<table class="rpt_table" width="1020" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th colspan="10">Fabric Details</th>
					</tr>
					
					<tr>
						<th width="120" rowspan="2">PART</th>
						<th width="70" rowspan="2">FABRIC TYPE</th>
						<th width="100" rowspan="2">REQUIRED QTY[Kg]</th>
						<th width="120" rowspan="2">RECEIVED QTY(KG) BY STORE</th>
						<th width="80" rowspan="2">Return to Store</th>
						<th width="80" rowspan="2">Cutting Receive</th>
						<th width="200"  colspan="2">CONSUMPTION</th>
						<th width="160" rowspan="2">REMARKS</th>
					</tr>
					<tr>
						<th width="100">GIVEN</th>
						<th width="100">ACTUAL</th>
					</tr>
				</thead>
				<?php
				$sql_body_part="SELECT body_part_id,construction,composition,plan_cut_qty,lib_yarn_count_deter_id,avg_finish_cons
				FROM wo_pre_cost_fabric_cost_dtls 
				WHERE company_id='$company_name'
				and job_no='$job_no'
				and status_active=1 and is_deleted=0";

				// echo $sql_body_part;
				$sql_arr=sql_select($sql_body_part);
				$body_part_arr=array();
				$body_part_count_arr=array();

				foreach ($sql_arr as $row) 
				{
					$body_part_arr[$row["BODY_PART_ID"]][$row["LIB_YARN_COUNT_DETER_ID"]]["PLAN_CUT_QTY"]+=$row["PLAN_CUT_QTY"];

					$body_part_arr[$row["BODY_PART_ID"]][$row["LIB_YARN_COUNT_DETER_ID"]]["CONSTRUCTION"]=$row["CONSTRUCTION"];
					$body_part_arr[$row["BODY_PART_ID"]][$row["LIB_YARN_COUNT_DETER_ID"]]["AVG_FINISH_CONS"]+=$row["AVG_FINISH_CONS"];
					$body_part_count_arr[$row["BODY_PART_ID"]][$row["LIB_YARN_COUNT_DETER_ID"]]++;
				}
				// echo "<pre>";
				// print_r($body_part_arr);
				// echo "</pre>";
				// ================================== FF Rcv ================================
				$sql_rcv="SELECT A.BODY_PART_ID,A.FABRIC_DESCRIPTION_ID,A.RECEIVE_QNTY,B.QNTY,B.QC_PASS_QNTY,B.ENTRY_FORM
				FROM PRO_FINISH_FABRIC_RCV_DTLS A,PRO_ROLL_DETAILS B
				WHERE 
				B.PO_BREAKDOWN_ID IN ($order_id)
				AND A.ID=B.DTLS_ID
				AND B.ENTRY_FORM IN (68,126)
				AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0
				AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0
				";
				//echo $sql_rcv;
				$sql_rcv_arr=sql_select($sql_rcv);
				$body_part_rcv_arr=array();
				foreach ($sql_rcv_arr as $row) 
				{
					$body_part_rcv_arr[$row["BODY_PART_ID"]][$row["FABRIC_DESCRIPTION_ID"]][$row["ENTRY_FORM"]]["RCV_QNTY"]+=$row["RECEIVE_QNTY"];
					$body_part_rcv_arr[$row["BODY_PART_ID"]][$row["FABRIC_DESCRIPTION_ID"]][$row["ENTRY_FORM"]]["QC_PASS_QNTY"]+=$row["QC_PASS_QNTY"];
					$body_part_rcv_arr[$row["BODY_PART_ID"]][$row["FABRIC_DESCRIPTION_ID"]][$row["ENTRY_FORM"]]["QNTY"]+=$row["QNTY"];
				}
				// echo "<pre>";print_r($body_part_rcv_arr);
				//  ================================ FF Issue ================================
				$sql_issue="SELECT B.BODY_PART_ID,B.ISSUE_QNTY,c.DETARMINATION_ID FROM PRO_ROLL_DETAILS A, INV_FINISH_FABRIC_ISSUE_DTLS B,PRODUCT_DETAILS_MASTER c 
				WHERE A.PO_BREAKDOWN_ID in ($order_id) and b.prod_id=c.id and c.item_category_id=2
				AND A.DTLS_ID=B.ID 
				AND A.ENTRY_FORM=71 
				AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 
				AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0";
				// echo $sql_issue;
				$sql_issue_arr=sql_select($sql_issue);
				$body_issue_arr=array();
				foreach ($sql_issue_arr as $row) 
				{
					$body_issue_arr[$row["BODY_PART_ID"]][$row["DETARMINATION_ID"]]+=$row["ISSUE_QNTY"];
				}

				$condition= new condition();     
			    $condition->po_id_in($order_id);     
			    $condition->init();

				$fabric= new fabric($condition);
				// echo $fabric->getQuery();
			    $fabric_req_qty_arr= $fabric->getQtyArray_by_JobColorFabricColorBodyPartIdDeterminIdGsmAndDiaWidth_knitAndwoven_greyAndfinish();
			    // echo "<pre>";print_r($fabric_req_qty_arr);die();
			    $fabric_req_qty_array = array();
			    foreach ($fabric_req_qty_arr['knit']['finish'] as $job_key => $job_value) 
			    {
			    	foreach ($job_value as $gc_key => $gc_value) 
			    	{
			    		foreach ($gc_value as $fc_key => $fc_value) 
			    		{
			    			foreach ($fc_value as $bpid_key => $bp_value) 
			    			{
			    				foreach ($bp_value as $dtr_key => $dtr_value) 
			    				{
			    					foreach ($dtr_value as $gsm => $gsm_value) 
			    					{
			    						foreach ($gsm_value as $dia_key => $dia_value) 
			    						{
			    							foreach ($dia_value as $uom_key => $val) 
			    							{
			    								// print_r($val);echo "<br>";
			    								$fabric_req_qty_array[$job_key][$bpid_key][$dtr_key] += array_sum($val);
			    							}
			    						}
			    					}
			    				}
			    			}
			    		}
			    	}
			    }
			    // echo "<pre>";print_r($fabric_req_qty_array);die();
				?>
				<tbody>
					<?
					foreach ($body_part_arr as $bod_key => $body_part_data) 
					{ 
						foreach ($body_part_data as $fabric_key => $row) 
						{
							$body_part_count = $body_part_count_arr[$bod_key][$fabric_key];
							?>
							<tr>
								<td align="left" title="<?=$bod_key;?>"><? echo $body_part[$bod_key];?></td>
								<td align="left" title="<?=$fabric_key;?>"><? echo $row["CONSTRUCTION"];?></td>
								<td align="right">
									<? echo number_format($fabric_req_qty_array[$job_no][$bod_key][$fabric_key],2);?>
								</td>
								<td align="right">
									<? echo number_format($body_part_rcv_arr[$bod_key][$fabric_key][68]["RCV_QNTY"],2);?>
								</td>
								<td align="right">
									<? echo number_format($body_part_rcv_arr[$bod_key][$fabric_key][126]["QNTY"],2);?></td>
								<td align="right">
									<? echo number_format($body_issue_arr[$bod_key][$fabric_key],2);?>
								
								</td>
							
								<td align="right">
									<? echo number_format(($row['AVG_FINISH_CONS']/$body_part_count),2); ?></td>
								
								<td align="right"></td>
								<td align="right"></td>
							</tr>
							<?
						}
					}
					?>
				</tbody>
			</table>
	    </div>
		<br>
		<!-- =====================Details Part=================== -->
		<?php 
		$sql_cutting="SELECT b.color_id,a.order_id,a.size_qty,a.size_id,b.order_qty
			from ppl_cut_lay_bundle a,ppl_cut_lay_dtls b
			Where 
			a.dtls_id=b.id
			and b.mst_id=a.mst_id
			and a.status_active=1 and a.is_deleted=0
			and b.status_active=1 and b.is_deleted=0
			and a.order_id in($order_id)";
		// echo $sql_cutting;
		$sql_cutting_data=sql_select($sql_cutting);
		$cut_and_lay_data_array=array();
		foreach ($sql_cutting_data as  $row) 
		{
			$cut_and_lay_data_array[$row["ORDER_ID"]][$row["COLOR_ID"]][$row["SIZE_ID"]]["layqty"]+=$row["SIZE_QTY"];
			// $color_count[$row["COLOR_ID"]]++;
		}
		//echo $sql_cutting;
		$sql_production="SELECT b.production_qnty,b.reject_qty,b.replace_qty,b.bundle_qty,b.alter_qty,b.spot_qty,b.production_type,c.color_number_id,c.order_quantity,c.size_number_id,c.po_break_down_id,c.plan_cut_qnty,a.embel_name
			from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown c 
			Where a.company_id = $company_name
			and c.id=b.color_size_break_down_id
			and b.mst_id=a.id
			and b.production_type in (1,2,3,4,5,64)
			and a.status_active=1 and a.is_deleted=0
			and b.status_active=1 and b.is_deleted=0
			and c.status_active in(1,2,3) and c.is_deleted=0
			and c.job_id = '$job_id_hidden'
			and a.po_break_down_id in($order_id)
			";
			//echo $sql_production;

			$sql_production_data=sql_select($sql_production);
			$production_data=array();
			foreach ($sql_production_data as  $val) 
			{
				$production_data[$val[csf("po_break_down_id")]][$val[csf("color_number_id")]][$val[csf("size_number_id")]][$val[csf("production_type")]]["pqnty"]+=$val[csf("production_qnty")];

				$production_data[$val[csf("po_break_down_id")]][$val[csf("color_number_id")]][$val[csf("size_number_id")]][$val[csf("production_type")]]["bundle_qty"]+=$val[csf("bundle_qty")];

				$production_data[$val[csf("po_break_down_id")]][$val[csf("color_number_id")]][$val[csf("size_number_id")]][$val[csf("production_type")]]["replace_qty"]+=$val[csf("replace_qty")];

				$production_data[$val[csf("po_break_down_id")]][$val[csf("color_number_id")]][$val[csf("size_number_id")]][$val[csf("production_type")]][$val[csf("embel_name")]]["reject_qty"]+=$val[csf("reject_qty")];

				$production_data[$val[csf("po_break_down_id")]][$val[csf("color_number_id")]][$val[csf("size_number_id")]][$val[csf("production_type")]]["alter_qty"]+=$val[csf("alter_qty")];

				$production_data[$val[csf("po_break_down_id")]][$val[csf("color_number_id")]][$val[csf("size_number_id")]][$val[csf("production_type")]]["spot_qty"]+=$val[csf("spot_qty")];

				$production_data[$val[csf("po_break_down_id")]][$val[csf("color_number_id")]][$val[csf("size_number_id")]][$val[csf("production_type")]]["order_quantity"]+=$val[csf("order_quantity")];

				$production_data[$val[csf("po_break_down_id")]][$val[csf("color_number_id")]][$val[csf("size_number_id")]][$val[csf("production_type")]]["plan_cut_qnty"]+=$val[csf("plan_cut_qnty")];
			}
			// echo "<pre>";
			// print_r($production_data);
			// echo "</pre>";
		?>
		<!-- ========= Details Table ============== -->
		<div style="<? echo $div_width+40; ?>px;">
			<table class="rpt_table" width="<? echo $table_width+180; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th width="80" rowspan="2">PO Number</th>
						<th width="80" rowspan="2">Color</th>
						<th width="80" rowspan="2">Size</th>
						<th width="80"  rowspan="2">Order Quantity</th>

						<th colspan="10">Cutting Details</th>
						<th colspan="14">QC Details (Rejection)</th>
						<th colspan="2">Physically Found</th>

						<th  width="120" rowspan="2">Remarks</th>
					</tr>
					<tr>
						<th width="80">Cut and Lay Quantity</th>
						<th width="50">%</th>
						<th width="80">Cutting QC Passed</th>
						<th width="50">%</th>
						<th width="80">Cutting QC WIP</th>
						<th width="50">%</th>
						<th width="80">Sewing Input </th>
						<th width="50">%</th>
						<th width="80">Input WIP</th>
						<th width="50">%</th>

						<th width="80">Cutting</th>
						<th width="50">%</th>
						<th width="80">Print</th>
						<th width="50">%</th>
						<th width="80">Emb</th>
						<th width="50">%</th>
						<th width="80">Sewing</th>
						<th width="50">%</th>
						<th width="80">Alter</th>
						<th width="50">%</th>
						<th width="80">Spot</th>
						<th width="50">%</th>
						<th width="80">Replace</th>
						<th width="50">%</th>

						<th width="80">Replace</th>
						<th width="50">%</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					$po_color_chk=array();
					foreach ($po_color_size_array as $po_key => $po_value) 
					{
						foreach($po_value as $color_key => $color_value)
						{
							foreach ($color_value as $size_key => $row) 
							{
								$color_count[$po_key][$color_key]++;
							}
						}
					}
					
					$grand_total_color_qty=0;

					$grand_total_cut_lay_qty=0;
					$grand_total_cut_lay_percent=0;

					$grand_total_plan_cut_qnty=0;
					$grand_total_plan_cut_qnty_percent=0;	

					$grand_total_cutting_qc_wip=0;
					$grand_total_cutting_qc_wip_percent=0;

					$grand_total_prnting=0;
					$grand_total_prnting_percent=0;	

					$grand_total_emb=0;
					$grand_total_emb_percent=0;

					$grand_total_swing_output=0;
					$grand_total_swing_output_percent=0;

					$grand_total_swing_input_wip=0;
					$grand_total_swing_input_percent=0;	

					$grand_qs_pass_qty=0;
					$grand_total_qs_pass_percent=0;	

					$grand_total_alter_qty=0;
					$grand_total_alter_qty_percent=0;

					$grand_total_spot_qty=0;
					$grand_total_alter_qty_percent+=0;
						
					$grand_total_replace_qty=0;
					$grand_total_replace_qty_percent=0;
						
					foreach ($po_color_size_array as $po_key => $po_value)
					{
					foreach($po_value as $color_key => $color_value)
					{
						//echo "<pre>";print_r($po_value);
						$total_color_order_qty=0;
						$total_cut_lay_qty=0;
						$total_qs_pass_qty=0;
						$total_cutting_qc_wip=0;
						$total_swing_input_wip=0;
						$total_plan_cut_qnty=0;

						$total_swing_input=0;

						$total_prnting=0;
						$total_alter_qty=0;
						$total_spot_qty=0;
						$total_replace_qty=0;
						$total_qs_pass_percent=0;
						$total_cut_lay_percent=0;
						$total_plan_cut_qnty_percent=0;
						$total_cutting_qc_wip_percent=0;
						$total_swing_input_wip_percent=0;

						$total_swing_output=0;
						$total_swing_output_percent=0;

						$total_prnting_percent=0;
						$total_alter_qty_percent=0;
						$total_spot_qty_percent=0;
						$total_replace_qty_percent=0;

					
						foreach ($color_value as $size_key => $row) 
						{
							$cut_lay_qnty=$cut_and_lay_data_array[$po_key][$color_key][$size_key]["layqty"];

							$pqrty=$production_data[$po_key][$color_key][$size_key][1]["pqnty"];

							$swing_input=$production_data[$po_key][$color_key][$size_key][4]["pqnty"];							
							$swing_output=$production_data[$po_key][$color_key][$size_key][5]["pqnty"];							
							$prnting=$production_data[$po_key][$color_key][$size_key][2]["pqnty"];
							$emb=$production_data[$po_key][$color_key][$size_key][64]["pqnty"];

							$bundle_qty=$production_data[$po_key][$color_key][$size_key][1]["bundle_qty"];

							
							$reject_qty=$production_data[$po_key][$color_key][$size_key][1][0]["reject_qty"];
							$print_reject_qty=$production_data[$po_key][$color_key][$size_key][3][1]["reject_qty"];
							$emb_reject_qty=$production_data[$po_key][$color_key][$size_key][3][2]["reject_qty"];
							$sew_reject_qty=$production_data[$po_key][$color_key][$size_key][5][0]["reject_qty"];
							$alter_qty=$production_data[$po_key][$color_key][$size_key][4]["alter_qty"];
							$spot_qty=$production_data[$po_key][$color_key][$size_key][4]["spot_qty"];
							$replace_qty=$production_data[$po_key][$color_key][$size_key][4]["replace_qty"];
							$order_quantity=$production_data[$po_key][$color_key][$size_key][1]["order_quantity"];
							$plan_cut_qnty=$production_data[$po_key][$color_key][$size_key][1]["plan_cut_qnty"];
							$check_arr_key=$po_key."**".$color_key;
							//echo $pqrty."test";  
                            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
								
								

								<? 
								if(!in_array($check_arr_key,$po_color_chk))
								{

									$po_color_chk[]=$check_arr_key;
									?>
									<td valign="middle" align="center" rowspan="<?=$color_count[$po_key][$color_key];?>"><strong>
										<?=$row["po_number"];?>
									</strong></td>
									<td valign="middle" align="center" rowspan="<?=$color_count[$po_key][$color_key];?>"><strong>
										<?=$color_library[$color_key];?>
									</strong></td>
									<?
								}?>

								<td align="right"><?=$size_library[$size_key];?></td>
								<td align="right"><?=$row["order_qty"];?></td>
								<td align="right"><?=$cut_lay_qnty;?></td>
								<td align="right">
									<?
									$cut_lay_percent=($row["order_qty"]>0) ? $cut_lay_qnty/$row["order_qty"]*100 : 0;
									echo number_format($cut_lay_percent,2)."%";
									?>
								</td>
								<td align="right"><?=$pqrty;?></td>
								<td align="right">
									<?
									$qs_pass_percent=($row["order_qty"]>0) ? $pqrty/$row["order_qty"]*100 : 0;
									echo number_format($qs_pass_percent,2)."%";
									?>
								</td>
								<td align="right">
									<?=$cutting_qc_wip=$row["order_qty"]-$pqrty;?>

								</td>
								<td align="right">
									<?
									$cutting_qc_wip_percent=($row["order_qty"]>0) ? $cutting_qc_wip/$row["order_qty"]*100 : 0;
									echo number_format($cutting_qc_wip_percent,2)."%";
									?>
								</td>
								<td align="right"><?=$swing_input?></td>
								<td align="right">
									<?
									$swing_input_percent=($row["order_qty"]>0) ? $swing_input/$row["order_qty"]*100 : 0;
									echo number_format($swing_input_percent,2)."%";
									?>
								</td>
								<td align="right"><?=$swing_input_wip=$row["order_qty"]-$swing_input;?></td>
								<td align="right">
									<?
									$swing_input_wip_percent=($row["order_qty"]>0) ? $swing_input_wip/$row["order_qty"]*100 : 0;
									echo number_format($swing_input_wip_percent,2)."%";
									?>
								</td>




								<td align="right"><?=number_format($reject_qty,0);?></td>
								<td align="right">
									<?
									$plan_cut_qnty_percent=($row["order_qty"]>0) ? $reject_qty/$row["order_qty"]*100 : 0;
									echo number_format($plan_cut_qnty_percent,2)."%";
									?>
								</td>
								<td align="right"><?=number_format($print_reject_qty,0);?></td>
								<td align="right">
									<?
									$prnting_percent=($row["order_qty"]>0) ? $print_reject_qty/$row["order_qty"]*100 : 0;
									echo number_format($prnting_percent,2)."%";
									?>
								</td>
								<td align="right"><?=number_format($emb_reject_qty,0);?></td>
								<td align="right">
									<?
									$emb_percent=($row["order_qty"]>0) ? $emb_reject_qty/$row["order_qty"]*100 : 0;
									echo number_format($emb_percent,2)."%";
									?>
								</td>
								<td align="right"><?=number_format($sew_reject_qty,0)?></td>
								<td align="right">
									<?
									$swing_output_percent=($row["order_qty"]>0) ? $sew_reject_qty/$row["order_qty"]*100 : 0;
									echo number_format($swing_output_percent,2)."%";
									?>
								</td>
								<td align="right"><?=number_format($alter_qty,0);?></td>
								<td align="right">
									<?
									$alter_qty_percent=($row["order_qty"]>0) ? $alter_qty/$row["order_qty"]*100 : 0;
									echo number_format($alter_qty_percent,2)."%";
									?>
								</td>
								<td align="right"><?=number_format($spot_qty,0);?></td>
								<td align="right">
									<?
									$spot_qty_percent=($row["order_qty"]>0) ? $spot_qty/$row["order_qty"]*100 : 0;
									echo number_format($spot_qty_percent,2)."%";
									?>
								</td>
								<td align="right"><?=number_format($replace_qty,0);?></td>
								<td align="right">
									<?
									$replace_qty_percent=($row["order_qty"]>0) ? $replace_qty/$row["order_qty"]*100 : 0;
									echo number_format($replace_qty_percent,2)."%";
									?>
								</td>

								<td align="right"></td>
								<td align="right">
								</td>
								<td align="right"></td>
							</tr>
							<?
							$i++;
							$total_color_order_qty+=$row["order_qty"];
							$total_cut_lay_qty+=$cut_lay_qnty;
							$total_cut_rej_qnty+=$reject_qty;
							$total_qs_pass_qty+=$pqrty;
							$total_cutting_qc_wip+=$cutting_qc_wip;
							$total_swing_input+=$swing_input;
							$total_swing_input_percent+=$swing_input_percent;
							$total_swing_input_wip+=$swing_input_wip;
							$total_swing_input_wip_percent+=$swing_input_wip_percent;
							$total_print_reject_qty+=$print_reject_qty;
							$total_swing_output+=$sew_reject_qty;
							$total_swing_output_percent+=$swing_output_percent;

							$total_alter_qty+=$alter_qty;
							$total_spot_qty+=$spot_qty;
							$total_replace_qty+=$replace_qty;
							$total_cut_lay_percent+=$cut_lay_percent;
							$total_cut_rej_qnty_percent+=$plan_cut_qnty_percent;
							$total_qs_pass_percent+=$qs_pass_percent;
							$total_cutting_qc_wip_percent+=$cutting_qc_wip_percent;

							$print_reject_qty_percent+=$prnting_percent;
							$total_emb+=$emb_reject_qty;
							$total_emb_percent+=$emb_percent;


							$total_alter_qty_percent+=$alter_qty_percent;
							$total_spot_qty_percent+=$spot_qty_percent;
							$total_replace_qty_percent+=$replace_qty_percent;
						}
					
						?>
						
						<tr style="text-align: right;font-weight: bold;background: #cddcdc;">
							<td colspan="3" align="right"><strong>Color Total</strong></td>
							<td align="right"><strong><?=$total_color_order_qty;?></strong></td>

							<td align="right"><strong><?=$total_cut_lay_qty?></strong></td>
							<td align="right"><strong><?=number_format((($total_cut_lay_qty/$total_color_order_qty)*100),2)."%";?></strong></td>

							<td align="right"><strong><?=$total_qs_pass_qty;?></strong></td>
							<td align="right"><strong><?=number_format((($total_qs_pass_qty/$total_color_order_qty)*100),2)."%";?></strong></td>

							<td align="right"><strong><?=$total_cutting_qc_wip;?></strong></td>
							<td align="right"><strong><?=number_format((($total_cutting_qc_wip/$total_color_order_qty)*100),2)."%";?></strong></td>
						
							<td align="right"><strong><?=$total_swing_input;?></strong></td>
							<td align="right"><strong><?=number_format((($total_swing_input/$total_color_order_qty)*100),2)."%";
							
							?></strong></td>
							<td align="right"><strong><?=$total_swing_input_wip;?></strong></td>
							<td align="right"><strong><?=number_format((($total_swing_input_wip/$total_color_order_qty)*100),2)."%";?></strong></td>
	
							<td align="right"><strong><?=$total_cut_rej_qnty?></strong></td>
							<td align="right"><strong><?=number_format($total_cut_rej_qnty_percent,2);?></strong></td>

							<td align="right"><strong><?=$total_print_reject_qty?></strong></td>
							<td align="right"><strong><?=number_format($print_reject_qty_percent,2);?></strong></td>
							
							<td align="right"><strong><?=$total_emb;?></strong></td>
							<td align="right"><strong><?=number_format($total_emb_percent,2);?></strong></td>

							<td align="right"><strong><?=$total_swing_output;?></strong></td>
							<td align="right"><strong><?=number_format($total_swing_output_percent,2);?></strong></td>

							<td align="right"><strong><?=$total_alter_qty?></strong></td>
							<td align="right"><strong><?=number_format($total_alter_qty_percent,2)."%";?></strong></td>

							<td align="right"><strong><?=$total_spot_qty?></strong></td>
							<td align="right"><strong><?=number_format($total_spot_qty_percent,2)."%";?></strong></td>

							<td align="right"><strong><?=$total_replace_qty?></strong></td>
							<td align="right"><strong><?=number_format($total_replace_qty_percent,2)."%";?></strong></td>

							<td></td>
							<td></td>
							<td></td>
					
						</tr>
						<?	

					
						$grand_total_color_qty+=$total_color_order_qty;

						$grand_total_cut_lay_qty+=$total_cut_lay_qty;
						$grand_total_cut_lay_percent+=$total_cut_lay_percent;

						$grand_total_cut_rej_qnty+=$total_cut_rej_qnty;
						$grand_total_cut_rej_qnty_percent+=$total_cut_rej_qnty_percent;

						$grand_qs_pass_qty+=$total_qs_pass_qty;	
						$grand_total_qs_pass_percent+=$total_qs_pass_percent;

						$grand_total_cutting_qc_wip+=$total_cutting_qc_wip;	
						$grand_total_cutting_qc_wip_percent+=$total_cutting_qc_wip_percent;	
						
						$grand_total_swing_input+=$total_swing_input;
						$grand_total_swing_input_percent+=$total_swing_input_percent;	

						$grand_total_swing_input_wip+=$total_swing_input_wip;
						$grand_total_swing_input_wip_percent+=$total_swing_input_wip_percent;	

						$grand_print_reject_qty+=$total_print_reject_qty;
						$grand_print_reject_qty_percent+=$print_reject_qty_percent;

						$grand_total_emb+=$total_emb;
						$grand_total_emb_percent+=$total_emb_percent;

						$grand_total_swing_output+=$total_swing_output;
						$grand_total_swing_output_percent+=$total_swing_output_percent;	

						$grand_total_alter_qty+=$total_alter_qty;
						$grand_total_alter_qty_percent+=$total_alter_qty_percent;

						$grand_total_spot_qty+=$total_spot_qty;
						$grand_total_spot_qty_percent+=$total_spot_qty_percent;

						$grand_total_replace_qty+=$total_replace_qty;
						$grand_total_replace_qty_percent+=$total_replace_qty_percent;	
					}
					}
					?>
					</tbody>
					<tfoot>						
						<tr>
							<th colspan="3" align="right"><strong>Grand Total</strong></th>
							<th align="right"><strong><?=$grand_total_color_qty;?></strong></th>
							<th align="right"><strong><?=$grand_total_cut_lay_qty;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_cut_lay_qty/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th align="right"><strong><?=$grand_qs_pass_qty;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_qs_pass_qty/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th align="right"><strong><?=$grand_total_cutting_qc_wip;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_cutting_qc_wip/$grand_total_color_qty)*100),2)."%";?></strong></th>
							
							<th align="right"><strong><?=$grand_total_swing_input;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_swing_input/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th align="right"><strong><?=$grand_total_swing_input_wip;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_swing_input_wip/$grand_total_color_qty)*100),2)."%";?></strong></th>



							<th align="right"><strong><?=$grand_total_cut_rej_qnty;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_cut_rej_qnty/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th align="right"><strong><?=$grand_print_reject_qty;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_print_reject_qty/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th align="right"><strong><?=$grand_total_emb;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_emb/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th align="right"><strong><?=$grand_total_swing_output;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_swing_output/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th align="right"><strong><?=$grand_total_alter_qty;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_alter_qty/$grand_total_color_qty)*100),2)."%";?></strong></th>
							
							<th align="right"><strong><?=$grand_total_spot_qty;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_spot_qty/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th align="right"><strong><?=$grand_total_replace_qty;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_replace_qty/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th></th>
							<th></th>

							<th></th>
								
						</tr>
					</tfoot>
				</table>
	    </div>
		<?
	}
	if ($rept_type==3)// Show3 Button
	{
		$div_width=2020;
    	$table_width=1980;
		?>
	    <div style="width:100%">
	        <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
	            <caption style="font-size:16px; font-weight:bold;">
				<?
	            $com_name = str_replace( "'", "", $cbo_company_name );
	            echo $company_arr[$com_name]."<br/>"."CLOSING REPORT [CUTTING SECTION]";
	            ?>
	          
	                <div style="color:red; text-align:left; font-size:14px;"></div>
	            </caption>
	        </table>
			<br>
			<!-- ========= JOB NO Table ============== -->
			<div style="width: 10%;float: left;">						 
				<img class="zoom" src='../../<?= $imge_arr[$job_no]; ?>' width='70%' height='70%' />					
			</div>
			<div  style="width: 90%;float: left;">
				<table class="rpt_table" width="950px" cellpadding="0" cellspacing="0" border="1" rules="all">
	                <tr>  
	                    <td width="120"><strong>Job No</strong></td>
	                    <td width="120"><?=$row[csf("job_no")];?></td>
	                    <td width="120"><strong>STYLE NO</strong></td>
	                    <td width="120"><?=$style_ref_no;?></td>
	                    <td width="120"><strong>BUYER</strong></td>
	                    <td width="120"><?=$buyer_arr[$row[csf("buyer_name")]];?></td>
	                </tr>
	                <tr>
	                    <td ><strong>Item Name</strong></td>
	                    <td><?=$garments_item[$row[csf("gmts_item_id")]];?></td>
	                    <td ><strong>Prod. Dept.</strong></td>
	                    <td><?=$product_dept[$row[csf("product_dept")]];?></td>
	                    <td><strong>Job Receive Date</strong></td>
	                    <td><?=$po_received_date;?></td>
	                </tr>
	                <tr>
	                    <td ><strong>Brand Name</strong></td>
	                    <td><?=$brand;?></td>
	                    <td ><strong>Season</strong></td>
	                    <td><?=$season;?></td>
	                    <td><strong>Shipment Date</strong></td>
	                    <td><?=$shipment_date;?></td>
	                </tr>
	                <tr>
	                    <td ><strong>IR/IB</strong></td>
	                    <td><?=$int_ref_no;?></td>
	                </tr>
		        </table>
	        </div>
	        <br clear="all">
	    </div>
		<br>
		<!-- ========= Shipment Date Table ============== -->
		<div style="width:100%">
			<table class="rpt_table" width="550px" cellpadding="0" cellspacing="0" border="1" rules="all">
	                 <thead>
					 <tr>
                        <th><strong>PO No.</strong></th>
                        <th><strong>Shipment Date</strong></th>
                        <th><strong>Color</strong></th>
                        <th><strong>Color Close</strong></th>
                        <th><strong>Closing Date</strong></th>
                    </tr>
					 </thead>
				<?
				$color_con = str_replace("c.color_number_id", "c.color_id", $color_con);
				$sql_ship="SELECT a.id,b.id as mst_id,a.po_number,a.po_quantity as order_qty,a.shipment_date,c.color_id,SUM(c.qc_pass_qty) as qc_pass_qty,b.cutting_qc_date
				from wo_po_break_down a, pro_gmts_cutting_qc_mst b,pro_gmts_cutting_qc_dtls c
				Where b.job_no='$job_no'
				and a.job_no_mst=b.job_no
				and c.mst_id=b.id
				and c.order_id=a.id
				and b.company_id=$company_name $color_con 
				and a.status_active=1 and a.is_deleted=0
				and b.status_active=1 and b.is_deleted=0
				and c.status_active=1 and c.is_deleted=0
				group by a.id,b.id,a.po_number,a.po_quantity,a.shipment_date,c.color_id,b.cutting_qc_date order by a.id,b.id";
				//echo $sql_ship;die;
				$sql_ress_fabric=sql_select($sql_ship);
				$ship_data_arr=array();
				foreach ($sql_ress_fabric as $row) 
				{
					$ship_data_arr[$row[csf("id")]][$row[csf("color_id")]]["po_number"]=$row[csf("po_number")];
					$ship_data_arr[$row[csf("id")]][$row[csf("color_id")]]["shipment_date"]=$row[csf("shipment_date")];
					$ship_data_arr[$row[csf("id")]][$row[csf("color_id")]]["color_id"]=$row[csf("color_id")];
					$ship_data_arr[$row[csf("id")]][$row[csf("color_id")]]["qc_pass_qty"]+=$row[csf("qc_pass_qty")];
					$ship_data_arr[$row[csf("id")]][$row[csf("color_id")]]["order_qty"]=$row[csf("order_qty")];
					$ship_data_arr[$row[csf("id")]][$row[csf("color_id")]]["cutting_qc_date"]=$row[csf("cutting_qc_date")];
				}
				foreach ($ship_data_arr as $po_id => $po_value) 
				{
					foreach ($po_value as $color_id => $value) {
						$count_po[$po_id]++;
					}
				}
				?>
				<tbody>
					<?php 
					$po_chk=array();
					foreach ($ship_data_arr as $po_id => $po_value)  
					{
						foreach ($po_value as  $row)
						{
							$rowspan=$count_po[$po_id];	
							?>
							<tr>
							<?
								if(!in_array($row["po_number"],$po_chk))
								{
									$po_chk[]=$row["po_number"];
									?>
									<td rowspan="<?=$rowspan;?>"><?=$row["po_number"];?></td>
									<td rowspan="<?=$rowspan;?>"><?=$row["shipment_date"];?></td>
									<?
								}
									?>
									<td><?=$color_library[$row["color_id"]];?></td>
									<? 
									//echo $row["order_qty")]."<br/>".$row["qc_pass_qty")];
								if ($row["order_qty"]<=$row["qc_pass_qty"])
								{
									?>
									<td>Yes</td>
									<td>
										<? echo date("j-M-Y",strtotime(str_replace("'","",$row["cutting_qc_date"])));?>
									</td>
									<?
								}else 
								{
									?>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<?
								}
								?>
							</tr>
								<?
							}
						}
								?>
				</tbody>
		    </table>
	    </div>
		<br>
		<!-- ========= Fabric Details Table ============== -->
		<div style="width:100%">
			<table class="rpt_table" width="1020" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th colspan="10">Fabric Details</th>
					</tr>
					
					<tr>
						<th width="120" rowspan="2">PART</th>
						<th width="70" rowspan="2">FABRIC TYPE</th>
						<th width="100" rowspan="2">REQUIRED QTY[Kg]</th>
						<th width="120" rowspan="2">RECEIVED QTY(KG) BY STORE</th>
						<th width="80" rowspan="2">Return to Store</th>
						<th width="80" rowspan="2">Cutting Receive</th>
						<th width="200"  colspan="2">CONSUMPTION</th>
						<th width="160" rowspan="2">REMARKS</th>
					</tr>
					<tr>
						<th width="100">GIVEN</th>
						<th width="100">ACTUAL</th>
					</tr>
				</thead>
				<?php
				$sql_body_part="SELECT body_part_id,construction,composition,plan_cut_qty,lib_yarn_count_deter_id,avg_finish_cons
				FROM wo_pre_cost_fabric_cost_dtls 
				WHERE company_id='$company_name'
				and job_no='$job_no'
				and status_active=1 and is_deleted=0";

				// echo $sql_body_part;
				$sql_arr=sql_select($sql_body_part);
				$body_part_arr=array();
				$body_part_count_arr=array();

				foreach ($sql_arr as $row) 
				{
					$body_part_arr[$row["BODY_PART_ID"]][$row["LIB_YARN_COUNT_DETER_ID"]]["PLAN_CUT_QTY"]+=$row["PLAN_CUT_QTY"];

					$body_part_arr[$row["BODY_PART_ID"]][$row["LIB_YARN_COUNT_DETER_ID"]]["CONSTRUCTION"]=$row["CONSTRUCTION"];
					$body_part_arr[$row["BODY_PART_ID"]][$row["LIB_YARN_COUNT_DETER_ID"]]["AVG_FINISH_CONS"]+=$row["AVG_FINISH_CONS"];
					$body_part_count_arr[$row["BODY_PART_ID"]][$row["LIB_YARN_COUNT_DETER_ID"]]++;
				}
				// echo "<pre>";
				// print_r($body_part_arr);
				// echo "</pre>";
				// ================================== FF Rcv ================================
				$sql_rcv="SELECT A.BODY_PART_ID,A.FABRIC_DESCRIPTION_ID,A.RECEIVE_QNTY,B.QNTY,B.QC_PASS_QNTY,B.ENTRY_FORM
				FROM PRO_FINISH_FABRIC_RCV_DTLS A,PRO_ROLL_DETAILS B
				WHERE 
				B.PO_BREAKDOWN_ID IN ($order_id)
				AND A.ID=B.DTLS_ID
				AND B.ENTRY_FORM IN (68,126)
				AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0
				AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0";
				//echo $sql_rcv;
				$sql_rcv_arr=sql_select($sql_rcv);
				$body_part_rcv_arr=array();
				foreach ($sql_rcv_arr as $row) 
				{
					$body_part_rcv_arr[$row["BODY_PART_ID"]][$row["FABRIC_DESCRIPTION_ID"]][$row["ENTRY_FORM"]]["RCV_QNTY"]+=$row["RECEIVE_QNTY"];
					$body_part_rcv_arr[$row["BODY_PART_ID"]][$row["FABRIC_DESCRIPTION_ID"]][$row["ENTRY_FORM"]]["QC_PASS_QNTY"]+=$row["QC_PASS_QNTY"];
					$body_part_rcv_arr[$row["BODY_PART_ID"]][$row["FABRIC_DESCRIPTION_ID"]][$row["ENTRY_FORM"]]["QNTY"]+=$row["QNTY"];
				}
				// echo "<pre>";print_r($body_part_rcv_arr);
				//  ================================ FF Issue ================================
				$sql_issue="SELECT B.BODY_PART_ID,B.ISSUE_QNTY,c.DETARMINATION_ID FROM PRO_ROLL_DETAILS A, INV_FINISH_FABRIC_ISSUE_DTLS B,PRODUCT_DETAILS_MASTER c 
				WHERE A.PO_BREAKDOWN_ID in ($order_id) and b.prod_id=c.id and c.item_category_id=2
				AND A.DTLS_ID=B.ID 
				AND A.ENTRY_FORM=71 
				AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 
				AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0";
				// echo $sql_issue;
				$sql_issue_arr=sql_select($sql_issue);
				$body_issue_arr=array();
				foreach ($sql_issue_arr as $row) 
				{
					$body_issue_arr[$row["BODY_PART_ID"]][$row["DETARMINATION_ID"]]+=$row["ISSUE_QNTY"];
				}

				$condition= new condition();     
			    $condition->po_id_in($order_id);     
			    $condition->init();

				$fabric= new fabric($condition);
				// echo $fabric->getQuery();
			    $fabric_req_qty_arr= $fabric->getQtyArray_by_JobColorFabricColorBodyPartIdDeterminIdGsmAndDiaWidth_knitAndwoven_greyAndfinish();
			    // echo "<pre>";print_r($fabric_req_qty_arr);die();
			    $fabric_req_qty_array = array();
			    foreach ($fabric_req_qty_arr['knit']['finish'] as $job_key => $job_value) 
			    {
			    	foreach ($job_value as $gc_key => $gc_value) 
			    	{
			    		foreach ($gc_value as $fc_key => $fc_value) 
			    		{
			    			foreach ($fc_value as $bpid_key => $bp_value) 
			    			{
			    				foreach ($bp_value as $dtr_key => $dtr_value) 
			    				{
			    					foreach ($dtr_value as $gsm => $gsm_value) 
			    					{
			    						foreach ($gsm_value as $dia_key => $dia_value) 
			    						{
			    							foreach ($dia_value as $uom_key => $val) 
			    							{
			    								// print_r($val);echo "<br>";
			    								$fabric_req_qty_array[$job_key][$bpid_key][$dtr_key] += array_sum($val);
			    							}
			    						}
			    					}
			    				}
			    			}
			    		}
			    	}
			    }
			    // echo "<pre>";print_r($fabric_req_qty_array);die();
				?>
				<tbody>
					<?
					foreach ($body_part_arr as $bod_key => $body_part_data) 
					{ 
						foreach ($body_part_data as $fabric_key => $row) 
						{
							$body_part_count = $body_part_count_arr[$bod_key][$fabric_key];
							?>
							<tr>
								<td align="left" title="<?=$bod_key;?>"><? echo $body_part[$bod_key];?></td>
								<td align="left" title="<?=$fabric_key;?>"><? echo $row["CONSTRUCTION"];?></td>
								<td align="right">
									<? echo number_format($fabric_req_qty_array[$job_no][$bod_key][$fabric_key],2);?>
								</td>
								<td align="right">
									<? echo number_format($body_part_rcv_arr[$bod_key][$fabric_key][68]["RCV_QNTY"],2);?>
								</td>
								<td align="right">
									<? echo number_format($body_part_rcv_arr[$bod_key][$fabric_key][126]["QNTY"],2);?></td>
								<td align="right">
									<? echo number_format($body_issue_arr[$bod_key][$fabric_key],2);?>
								
								</td>
							
								<td align="right">
									<? echo number_format(($row['AVG_FINISH_CONS']/$body_part_count),2); ?></td>
								
								<td align="right"></td>
								<td align="right"></td>
							</tr>
							<?
						}
					}
					?>
				</tbody>
			</table>
	    </div>
		<br>
		<!-- =====================Details Part=================== -->
		<?php 
		$sql_cutting="SELECT b.color_id,a.order_id,a.size_qty,a.size_id,b.order_qty
			from ppl_cut_lay_bundle a,ppl_cut_lay_dtls b
			Where 
			a.dtls_id=b.id
			and b.mst_id=a.mst_id
			and a.status_active=1 and a.is_deleted=0
			and b.status_active=1 and b.is_deleted=0
			and a.order_id in($order_id)";
		// echo $sql_cutting;
		$sql_cutting_data=sql_select($sql_cutting);
		$cut_and_lay_data_array=array();
		foreach ($sql_cutting_data as  $row) 
		{
			$cut_and_lay_data_array[$row["COLOR_ID"]][$row["SIZE_ID"]]["layqty"]+=$row["SIZE_QTY"];
			// $color_count[$row["COLOR_ID"]]++;
		}
		//echo $sql_cutting;
		$sql_production="SELECT b.production_qnty,b.reject_qty,b.replace_qty,b.bundle_qty,b.alter_qty,b.spot_qty,b.production_type,c.color_number_id,c.order_quantity,c.size_number_id,c.po_break_down_id,c.plan_cut_qnty,a.embel_name
			from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown c 
			Where a.company_id = $company_name
			and c.id=b.color_size_break_down_id
			and b.mst_id=a.id
			and b.production_type in (1,2,3,4,5,64)
			and a.status_active=1 and a.is_deleted=0
			and b.status_active=1 and b.is_deleted=0
			and c.status_active in(1,2,3) and c.is_deleted=0
			and c.job_id = '$job_id_hidden'
			and a.po_break_down_id in($order_id)
			";
			//echo $sql_production;

			$sql_production_data=sql_select($sql_production);
			$production_data=array();
			foreach ($sql_production_data as  $val) 
			{
				$production_data[$val[csf("color_number_id")]][$val[csf("size_number_id")]][$val[csf("production_type")]]["pqnty"]+=$val[csf("production_qnty")];

				$production_data[$val[csf("color_number_id")]][$val[csf("size_number_id")]][$val[csf("production_type")]]["bundle_qty"]+=$val[csf("bundle_qty")];

				$production_data[$val[csf("color_number_id")]][$val[csf("size_number_id")]][$val[csf("production_type")]]["replace_qty"]+=$val[csf("replace_qty")];

				$production_data[$val[csf("color_number_id")]][$val[csf("size_number_id")]][$val[csf("production_type")]][$val[csf("embel_name")]]["reject_qty"]+=$val[csf("reject_qty")];

				$production_data[$val[csf("color_number_id")]][$val[csf("size_number_id")]][$val[csf("production_type")]]["alter_qty"]+=$val[csf("alter_qty")];

				$production_data[$val[csf("color_number_id")]][$val[csf("size_number_id")]][$val[csf("production_type")]]["spot_qty"]+=$val[csf("spot_qty")];

				$production_data[$val[csf("color_number_id")]][$val[csf("size_number_id")]][$val[csf("production_type")]]["order_quantity"]+=$val[csf("order_quantity")];

				$production_data[$val[csf("color_number_id")]][$val[csf("size_number_id")]][$val[csf("production_type")]]["plan_cut_qnty"]+=$val[csf("plan_cut_qnty")];
			}
			// echo "<pre>";
			// print_r($production_data);
			// echo "</pre>";
		?>
		<!-- ========= Details Table ============== -->
		<div style="<? echo $div_width+40; ?>px;">
			<table class="rpt_table" width="<? echo $table_width+80; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th width="80" rowspan="2">Item Name</th>
						<th width="80" rowspan="2">Color</th>
						<th width="80" rowspan="2">Size</th>
						<th width="80"  rowspan="2">Order Quantity</th>

						<th colspan="10">Cutting Details</th>
						<th colspan="14">QC Details (Rejection)</th>
						<th colspan="2">Physically Found</th>

						<th  width="120" rowspan="2">Remarks</th>
					</tr>
					<tr>
						<th width="80">Cut and Lay Quantity</th>
						<th width="50">%</th>
						<th width="80">Cutting QC Passed</th>
						<th width="50">%</th>
						<th width="80">Cutting QC WIP</th>
						<th width="50">%</th>
						<th width="80">Sewing Input </th>
						<th width="50">%</th>
						<th width="80">Input WIP</th>
						<th width="50">%</th>

						<th width="80">Cutting</th>
						<th width="50">%</th>
						<th width="80">Print</th>
						<th width="50">%</th>
						<th width="80">Emb</th>
						<th width="50">%</th>
						<th width="80">Sewing</th>
						<th width="50">%</th>
						<th width="80">Alter</th>
						<th width="50">%</th>
						<th width="80">Spot</th>
						<th width="50">%</th>
						<th width="80">Replace</th>
						<th width="50">%</th>

						<th width="80">Replace</th>
						<th width="50">%</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					$color_chk=array();
					foreach ($color_size_array as $item_key => $item_value) 
					{
						foreach ($item_value as $color_key => $color_value) 
						{
							foreach ($color_value as $size_key => $row) 
							{
								$color_count[$color_key]++;
							}
						}
					}
					$grand_total_color_qty=0;

					$grand_total_cut_lay_qty=0;
					$grand_total_cut_lay_percent=0;

					$grand_total_plan_cut_qnty=0;
					$grand_total_plan_cut_qnty_percent=0;	

					$grand_total_cutting_qc_wip=0;
					$grand_total_cutting_qc_wip_percent=0;

					$grand_total_prnting=0;
					$grand_total_prnting_percent=0;	

					$grand_total_emb=0;
					$grand_total_emb_percent=0;

					$grand_total_swing_output=0;
					$grand_total_swing_output_percent=0;

					$grand_total_swing_input_wip=0;
					$grand_total_swing_input_percent=0;	

					$grand_qs_pass_qty=0;
					$grand_total_qs_pass_percent=0;	

					$grand_total_alter_qty=0;
					$grand_total_alter_qty_percent=0;

					$grand_total_spot_qty=0;
					$grand_total_alter_qty_percent+=0;
						
					$grand_total_replace_qty=0;
					$grand_total_replace_qty_percent=0;
					foreach ($color_size_array as $item_key => $item_value) 
					{	
						foreach ($item_value as $color_key => $color_value)
						{
							$total_color_order_qty=0;
							$total_cut_lay_qty=0;
							$total_qs_pass_qty=0;
							$total_cutting_qc_wip=0;
							$total_swing_input_wip=0;
							$total_plan_cut_qnty=0;

							$total_swing_input=0;

							$total_prnting=0;
							$total_alter_qty=0;
							$total_spot_qty=0;
							$total_replace_qty=0;
							$total_qs_pass_percent=0;
							$total_cut_lay_percent=0;
							$total_plan_cut_qnty_percent=0;
							$total_cutting_qc_wip_percent=0;
							$total_swing_input_wip_percent=0;

							$total_swing_output=0;
							$total_swing_output_percent=0;

							$total_prnting_percent=0;
							$total_alter_qty_percent=0;
							$total_spot_qty_percent=0;
							$total_replace_qty_percent=0;

							foreach ($color_value as $size_key => $row) 
							{
								$cut_lay_qnty=$cut_and_lay_data_array[$color_key][$size_key]["layqty"];

								$pqrty=$production_data[$color_key][$size_key][1]["pqnty"];

								$swing_input=$production_data[$color_key][$size_key][4]["pqnty"];							
								$swing_output=$production_data[$color_key][$size_key][5]["pqnty"];							
								$prnting=$production_data[$color_key][$size_key][2]["pqnty"];
								$emb=$production_data[$color_key][$size_key][64]["pqnty"];

								$bundle_qty=$production_data[$color_key][$size_key][1]["bundle_qty"];

								
								$reject_qty=$production_data[$color_key][$size_key][1][0]["reject_qty"];
								$print_reject_qty=$production_data[$color_key][$size_key][3][1]["reject_qty"];
								$emb_reject_qty=$production_data[$color_key][$size_key][3][2]["reject_qty"];
								$sew_reject_qty=$production_data[$color_key][$size_key][5][0]["reject_qty"];
								$alter_qty=$production_data[$color_key][$size_key][4]["alter_qty"];
								$spot_qty=$production_data[$color_key][$size_key][4]["spot_qty"];
								$replace_qty=$production_data[$color_key][$size_key][4]["replace_qty"];
								$order_quantity=$production_data[$color_key][$size_key][1]["order_quantity"];
								$plan_cut_qnty=$production_data[$color_key][$size_key][1]["plan_cut_qnty"];
								//echo $pqrty."test";  
								$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
									<td><p><?=$garments_item[$item_key];?></p></td>
									<? 
									if(!in_array($color_key,$color_chk))
									{
										$color_chk[]=$color_key;
										?>
										<td valign="middle" align="center" rowspan="<?=$color_count[$color_key];?>"><strong>
											<?=$color_library[$color_key];?>
										</strong></td>
										<?
									}?>
									<td align="right"><?=$size_library[$size_key];?></td>
									<td align="right"><?=$row["order_qty"];?></td>
									<td align="right"><?=$cut_lay_qnty;?></td>
									<td align="right">
										<?
										$cut_lay_percent=($row["order_qty"]>0) ? $cut_lay_qnty/$row["order_qty"]*100 : 0;
										echo number_format($cut_lay_percent,2)."%";
										?>
									</td>
									<td align="right"><?=$pqrty;?></td>
									<td align="right">
										<?
										$qs_pass_percent=($row["order_qty"]>0) ? $pqrty/$row["order_qty"]*100 : 0;
										echo number_format($qs_pass_percent,2)."%";
										?>
									</td>
									<td align="right">
										<?=$cutting_qc_wip=$row["order_qty"]-$pqrty;?>

									</td>
									<td align="right">
										<?
										$cutting_qc_wip_percent=($row["order_qty"]>0) ? $cutting_qc_wip/$row["order_qty"]*100 : 0;
										echo number_format($cutting_qc_wip_percent,2)."%";
										?>
									</td>
									<td align="right"><?=$swing_input?></td>
									<td align="right">
										<?
										$swing_input_percent=($row["order_qty"]>0) ? $swing_input/$row["order_qty"]*100 : 0;
										echo number_format($swing_input_percent,2)."%";
										?>
									</td>
									<td align="right"><?=$swing_input_wip=$row["order_qty"]-$swing_input;?></td>
									<td align="right">
										<?
										$swing_input_wip_percent=($row["order_qty"]>0) ? $swing_input_wip/$row["order_qty"]*100 : 0;
										echo number_format($swing_input_wip_percent,2)."%";
										?>
									</td>






									<td align="right"><?=number_format($reject_qty,0);?></td>
									<td align="right">
										<?
										$plan_cut_qnty_percent=($row["order_qty"]>0) ? $reject_qty/$row["order_qty"]*100 : 0;
										echo number_format($plan_cut_qnty_percent,2)."%";
										?>
									</td>
									<td align="right"><?=number_format($print_reject_qty,0);?></td>
									<td align="right">
										<?
										$prnting_percent=($row["order_qty"]>0) ? $print_reject_qty/$row["order_qty"]*100 : 0;
										echo number_format($prnting_percent,2)."%";
										?>
									</td>
									<td align="right"><?=number_format($emb_reject_qty,0);?></td>
									<td align="right">
										<?
										$emb_percent=($row["order_qty"]>0) ? $emb_reject_qty/$row["order_qty"]*100 : 0;
										echo number_format($emb_percent,2)."%";
										?>
									</td>
									<td align="right"><?=number_format($sew_reject_qty,0)?></td>
									<td align="right">
										<?
										$swing_output_percent=($row["order_qty"]>0) ? $sew_reject_qty/$row["order_qty"]*100 : 0;
										echo number_format($swing_output_percent,2)."%";
										?>
									</td>
									<td align="right"><?=number_format($alter_qty,0);?></td>
									<td align="right">
										<?
										$alter_qty_percent=($row["order_qty"]>0) ? $alter_qty/$row["order_qty"]*100 : 0;
										echo number_format($alter_qty_percent,2)."%";
										?>
									</td>
									<td align="right"><?=number_format($spot_qty,0);?></td>
									<td align="right">
										<?
										$spot_qty_percent=($row["order_qty"]>0) ? $spot_qty/$row["order_qty"]*100 : 0;
										echo number_format($spot_qty_percent,2)."%";
										?>
									</td>
									<td align="right"><?=number_format($replace_qty,0);?></td>
									<td align="right">
										<?
										$replace_qty_percent=($row["order_qty"]>0) ? $replace_qty/$row["order_qty"]*100 : 0;
										echo number_format($replace_qty_percent,2)."%";
										?>
									</td>

									<td align="right"></td>
									<td align="right">
									</td>
									<td align="right"></td>
								</tr>
								<?
								$i++;
								$total_color_order_qty+=$row["order_qty"];
								$total_cut_lay_qty+=$cut_lay_qnty;
								$total_cut_rej_qnty+=$reject_qty;
								$total_qs_pass_qty+=$pqrty;
								$total_cutting_qc_wip+=$cutting_qc_wip;
								$total_swing_input+=$swing_input;
								$total_swing_input_percent+=$swing_input_percent;
								$total_swing_input_wip+=$swing_input_wip;
								$total_swing_input_wip_percent+=$swing_input_wip_percent;
								$total_print_reject_qty+=$print_reject_qty;
								$total_swing_output+=$sew_reject_qty;
								$total_swing_output_percent+=$swing_output_percent;

								$total_alter_qty+=$alter_qty;
								$total_spot_qty+=$spot_qty;
								$total_replace_qty+=$replace_qty;
								$total_cut_lay_percent+=$cut_lay_percent;
								$total_cut_rej_qnty_percent+=$plan_cut_qnty_percent;
								$total_qs_pass_percent+=$qs_pass_percent;
								$total_cutting_qc_wip_percent+=$cutting_qc_wip_percent;

								$print_reject_qty_percent+=$prnting_percent;
								$total_emb+=$emb_reject_qty;
								$total_emb_percent+=$emb_percent;


								$total_alter_qty_percent+=$alter_qty_percent;
								$total_spot_qty_percent+=$spot_qty_percent;
								$total_replace_qty_percent+=$replace_qty_percent;
							}
							?>
							
							<tr style="text-align: right;font-weight: bold;background: #cddcdc;">
								<td colspan="3" align="right"><strong>Color Total</strong></td>
								<td align="right"><strong><?=$total_color_order_qty;?></strong></td>

								<td align="right"><strong><?=$total_cut_lay_qty?></strong></td>
								<td align="right"><strong><?=number_format((($total_cut_lay_qty/$total_color_order_qty)*100),2)."%";?></strong></td>

								<td align="right"><strong><?=$total_qs_pass_qty;?></strong></td>
								<td align="right"><strong><?=number_format(($total_qs_pass_qty/$total_color_order_qty)*100,2)."%";?></strong></td>

								<td align="right"><strong><?=$total_cutting_qc_wip;?></strong></td>
								<td align="right"><strong><?=number_format(($total_cutting_qc_wip/$total_color_order_qty)*100,2)."%";?></strong></td>
							
								<td align="right"><strong><?=$total_swing_input;?></strong></td>

								<td align="right"><strong><?=number_format(($total_swing_input/$total_color_order_qty)*100,2)."%";
								
								?></strong></td>
								<td align="right"><strong><?=$total_swing_input_wip;?></strong></td>
								<td align="right"><strong><?=number_format(($total_swing_input_wip/$total_color_order_qty)*100,2)."%";?></strong></td>



								
								<td align="right"><strong><?=$total_cut_rej_qnty?></strong></td>
								<td align="right"><strong><?=number_format(($total_cut_rej_qnty/$total_color_order_qty)*100,2)."%";?></strong></td>

								<td align="right"><strong><?=$total_print_reject_qty?></strong></td>
								<td align="right"><strong><?=number_format(($total_print_reject_qty/$total_color_order_qty)*100,2).'%';?></strong></td>
								
								<td align="right"><strong><?=$total_emb;?></strong></td>
								<td align="right"><strong><?=number_format(($total_emb/$total_color_order_qty)*100,2).'%' ;?></strong></td>

								<td align="right"><strong><?=$total_swing_output;?></strong></td>
								<td align="right"><strong><?=number_format(($total_swing_output/$total_color_order_qty)*100,2).'%';?></strong></td>

								<td align="right"><strong><?=$total_alter_qty?></strong></td>
								<td align="right"><strong><?=number_format(($total_alter_qty/$total_color_order_qty)*100,2)."%";?></strong></td>



								<td align="right"><strong><?=$total_spot_qty?></strong></td>
								<td align="right"><strong><?=number_format(($total_spot_qty/$total_color_order_qty)*100,2)."%";?></strong></td>

								<td align="right"><strong><?=$total_replace_qty?></strong></td>
								<td align="right"><strong><?=number_format(($total_replace_qty/$total_color_order_qty)*100,2)."%";?></strong></td>

								<td></td>
								<td></td>
								<td></td>
						
							</tr>
							<?	
							$grand_total_color_qty+=$total_color_order_qty;

							$grand_total_cut_lay_qty+=$total_cut_lay_qty;
							$grand_total_cut_lay_percent+=$total_cut_lay_percent;

							$grand_total_cut_rej_qnty+=$total_cut_rej_qnty;
							$grand_total_cut_rej_qnty_percent+=$total_cut_rej_qnty_percent;

							$grand_qs_pass_qty+=$total_qs_pass_qty;	
							$grand_total_qs_pass_percent+=$total_qs_pass_percent;

							$grand_total_cutting_qc_wip+=$total_cutting_qc_wip;	
							$grand_total_cutting_qc_wip_percent+=$total_cutting_qc_wip_percent;	
							
							$grand_total_swing_input+=$total_swing_input;
							$grand_total_swing_input_percent+=$total_swing_input_percent;	

							$grand_total_swing_input_wip+=$total_swing_input_wip;
							$grand_total_swing_input_wip_percent+=$total_swing_input_wip_percent;	

							$grand_print_reject_qty+=$total_print_reject_qty;
							$grand_print_reject_qty_percent+=$print_reject_qty_percent;

							$grand_total_emb+=$total_emb;
							$grand_total_emb_percent+=$total_emb_percent;

							$grand_total_swing_output+=$total_swing_output;
							$grand_total_swing_output_percent+=$total_swing_output_percent;	

							$grand_total_alter_qty+=$total_alter_qty;
							$grand_total_alter_qty_percent+=$total_alter_qty_percent;

							$grand_total_spot_qty+=$total_spot_qty;
							$grand_total_spot_qty_percent+=$total_spot_qty_percent;

							$grand_total_replace_qty+=$total_replace_qty;
							$grand_total_replace_qty_percent+=$total_replace_qty_percent;	
							
						}
					}
					?>
					</tbody>
					<tfoot>						
						<tr>
							<th colspan="3" align="right"><strong>Grand Total</strong></th>
							<th align="right"><strong><?=$grand_total_color_qty;?></strong></th>
							<th align="right"><strong><?=$grand_total_cut_lay_qty;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_cut_lay_qty/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th align="right"><strong><?=$grand_qs_pass_qty;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_qs_pass_qty/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th align="right"><strong><?=$grand_total_cutting_qc_wip;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_cutting_qc_wip/$grand_total_color_qty)*100),2)."%";?></strong></th>
							
							<th align="right"><strong><?=$grand_total_swing_input;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_swing_input/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th align="right"><strong><?=$grand_total_swing_input_wip;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_swing_input_wip/$grand_total_color_qty)*100),2)."%";?></strong></th>



							<th align="right"><strong><?=$grand_total_cut_rej_qnty;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_cut_rej_qnty/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th align="right"><strong><?=$grand_print_reject_qty;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_print_reject_qty/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th align="right"><strong><?=$grand_total_emb;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_emb/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th align="right"><strong><?=$grand_total_swing_output;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_swing_output/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th align="right"><strong><?=$grand_total_alter_qty;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_alter_qty/$grand_total_color_qty)*100),2)."%";?></strong></th>
							
							<th align="right"><strong><?=$grand_total_spot_qty;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_spot_qty/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th align="right"><strong><?=$grand_total_replace_qty;?></strong></th>
							<th align="right"><strong><?=number_format((($grand_total_replace_qty/$grand_total_color_qty)*100),2)."%";?></strong></th>

							<th></th>
							<th></th>

							<th></th>
						
							
						</tr>
					</tfoot>
				</table>
	    </div>
		<?
	}

	foreach (glob("*.xls") as $filename)
	{		
		@unlink($filename);

	}
	$name=time().".xls";
	$create_new_excel = fopen($name, 'w');	
	$report_data=ob_get_contents();
	ob_clean();
	$is_created = fwrite($create_new_excel,$report_data);
	echo $report_data."####".$name;
	exit();
}
?>