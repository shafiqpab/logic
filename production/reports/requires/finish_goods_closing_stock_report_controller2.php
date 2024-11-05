<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 100, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company in($data) and a.status_active=1 and a.is_deleted=0 order by a.buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );
  	exit();	 
}

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 100, "select id,store_name from lib_store_location where status_active =1 and is_deleted=0 and company_id in($data) order by store_name","id,store_name", 1, "-- Select Store --", $selected, "",0 );     
	exit();	
}
if($action=="style_popup")
{
	echo load_html_head_contents("Search Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_id = new Array; var selected_name = new Array;var selected_id_arr = new Array;
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
			 
			if( jQuery.inArray( str[0], selected_id_arr ) == -1 ) {
				selected_id_arr.push( str[0] );
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
				name += selected_name[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
		}
	
    </script>

	</head>
	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:710px;">
	            <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th>Company Name</th>
	                    <th>Buyer</th>
	                    <th>Style Reference</th>
	                    <th>Job No</th>
	                    <th>PO Number</th>
	                    <th>
                            <input type="reset" name="button" class="formbutton" value="Reset"  style="width:80px;"> 
                            <input type="hidden" name="hide_style_ref" id="hide_style_ref" value="" />
                            <input type="hidden" name="hide_style_ref_id" id="hide_style_ref_id" value="" />
                        </th>
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
								<?
									echo create_drop_down( "cbo_company_id", 100, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company_name, "load_drop_down('requires/finish_goods_closing_stock_report_controller2', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
								?>
	                        </td>                  
	                        <td align="center" id="buyer_td"> 
	                        	<? 
									echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_name $buyer_cond order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
								?>
	                        </td>                   
	                        <td align="center">				
	                            <input type="text" style="width:100px" class="text_boxes" name="txt_style_ref" id="txt_style_ref" />	
	                        </td> 
	                        <td align="center">				
	                            <input type="text" style="width:100px" class="text_boxes" name="txt_job_no" id="txt_job_no" />	
	                        </td> 
	                        <td align="center">				
	                            <input type="text" style="width:100px" class="text_boxes" name="txt_po_no" id="txt_po_no" />	
	                        </td> 
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_style_ref').value+'**'+document.getElementById('txt_job_no').value+'**'+document.getElementById('txt_po_no').value, 'search_list_view', 'search_div', 'finish_goods_closing_stock_report_controller2', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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

if($action=="job_popup")
{
	echo load_html_head_contents("Search Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_id = new Array; var selected_name = new Array;var selected_id_arr = new Array;
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
			 
			if( jQuery.inArray( str[0], selected_id_arr ) == -1 ) {
				selected_id_arr.push( str[0] );
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
				name += selected_name[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
		}
	
    </script>

	</head>
	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:710px;">
	            <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th>Company Name</th>
	                    <th>Buyer</th>
	                    <th>Style Reference</th>
	                    <th>Job No</th>
	                    <th>PO Number</th>
	                    <th>
                            <input type="reset" name="button" class="formbutton" value="Reset"  style="width:80px;"> 
                            <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                            <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                        </th>
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
								<?
									echo create_drop_down( "cbo_company_id", 100, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company_name, "load_drop_down('requires/finish_goods_closing_stock_report_controller2', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
								?>
	                        </td>                  
	                        <td align="center" id="buyer_td"> 
	                        	<? 
									echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_name $buyer_cond order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
								?>
	                        </td>                   
	                        <td align="center">				
	                            <input type="text" style="width:100px" class="text_boxes" name="txt_style_ref" id="txt_style_ref" />	
	                        </td> 
	                        <td align="center">				
	                            <input type="text" style="width:100px" class="text_boxes" name="txt_job_no" id="txt_job_no" />	
	                        </td> 
	                        <td align="center">				
	                            <input type="text" style="width:100px" class="text_boxes" name="txt_po_no" id="txt_po_no" />	
	                        </td> 
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_style_ref').value+'**'+document.getElementById('txt_job_no').value+'**'+document.getElementById('txt_po_no').value, 'search_list_view', 'search_div', 'finish_goods_closing_stock_report_controller2', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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
if($action=="order_popup")
{
	echo load_html_head_contents("Search Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_id = new Array; var selected_name = new Array;var selected_id_arr = new Array;
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
			 
			if( jQuery.inArray( str[0], selected_id_arr ) == -1 ) {
				selected_id_arr.push( str[0] );
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
				name += selected_name[i] + ',';
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
		<form name="order_form" id="order_form">
			<fieldset style="width:710px;">
	            <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th>Company Name</th>
	                    <th>Buyer</th>
	                    <th>Style Reference</th>
	                    <th>Job No</th>
	                    <th>PO Number</th>
	                    <th>
                            <input type="reset" name="button" class="formbutton" value="Reset"  style="width:80px;"> 
                            <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                            <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
                        </th>
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
								<?
									echo create_drop_down( "cbo_company_id", 100, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company_name, "load_drop_down('requires/finish_goods_closing_stock_report_controller2', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
								?>
	                        </td>                  
	                        <td align="center" id="buyer_td"> 
	                        	<? 
									echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_name $buyer_cond order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
								?>
	                        </td>                   
	                        <td align="center">				
	                            <input type="text" style="width:100px" class="text_boxes" name="txt_style_ref" id="txt_style_ref" />	
	                        </td> 
	                        <td align="center">				
	                            <input type="text" style="width:100px" class="text_boxes" name="txt_job_no" id="txt_job_no" />	
	                        </td> 
	                        <td align="center">				
	                            <input type="text" style="width:100px" class="text_boxes" name="txt_ordr_no" id="txt_order_no" />	
	                        </td> 
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_style_ref').value+'**'+document.getElementById('txt_job_no').value+'**'+document.getElementById('txt_order_no').value, 'search_list_view1', 'search_div', 'finish_goods_closing_stock_report_controller2', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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


if($action=="search_list_view")
{
	$data=explode('**',$data);
	$company_id=str_replace("'", "", $data[0]);
	$buyer_id=str_replace("'", "", $data[1]);
	$style_ref=str_replace("'", "", $data[2]);
	$job_no=str_replace("'", "", $data[3]);
	$po_no=str_replace("'", "", $data[4]);
		
	$search_string='';
	if($company_id!=0)
	{
		$search_string.=" and a.company_name=$company_id ";
	}
	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $search_string.=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
		}
	}
	else
	{
		$search_string.=" and a.buyer_name=$buyer_id ";
	}
	if($style_ref!='')
	{
		$search_string.=" and a.style_ref_no='".trim($style_ref)."' ";
	}
	if($job_no!='')
	{
		$search_string.=" and a.job_no like '%".$job_no."' ";
	}
	if($po_no!='')
	{
		$search_string.=" and b.po_number='".trim($po_no)."' ";
	}

	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$arr=array (0=>$company_library,1=>$buyer_arr);
	
	if($db_type==0) $po_number="group_concat(distinct(b.po_number)) as po_number,"; 
	else if($db_type==2) $po_number="listagg(cast(b.po_number as varchar(4000)),', ') within group(order by b.id) as po_number";
	else  $po_number="";

	$sql= "SELECT a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $po_number from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 $search_string group by a.id, a.job_no, a.insert_date, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no order by a.id desc"; 
    // echo $sql;
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Style,Job No,Order No", "100,100,100,100","650","220",0, $sql , "js_set_value", "id,job_no,style_ref_no","",1,"company_name,buyer_name,0,0,0,0",$arr,"company_name,buyer_name,style_ref_no,job_no,po_number","",'','0,0,0,0,0','',1) ;
   exit(); 
}

if($action=="search_list_view1")
{
	$data=explode('**',$data);
	$company_id=str_replace("'", "", $data[0]);
	$buyer_id=str_replace("'", "", $data[1]);
	$style_ref=str_replace("'", "", $data[2]);
	$job_no=str_replace("'", "", $data[3]);
	$po_no=str_replace("'", "", $data[4]);
		
	$search_string='';
	if($company_id!=0)
	{
		$search_string.=" and a.company_name=$company_id ";
	}
	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $search_string.=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
		}
	}
	else
	{
		$search_string.=" and a.buyer_name=$buyer_id ";
	}
	if($style_ref!='')
	{
		$search_string.=" and a.style_ref_no='".trim($style_ref)."' ";
	}
	if($job_no!='')
	{
		$search_string.=" and a.job_no like '%".$job_no."' ";
	}
	if($po_no!='')
	{
		$search_string.=" and b.po_number='".trim($po_no)."' ";
	}

	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$arr=array (0=>$company_library,1=>$buyer_arr);
	
	if($db_type==0) $po_number="group_concat(distinct(b.po_number)) as po_number,"; 
	else if($db_type==2) $po_number="listagg(cast(b.po_number as varchar(4000)),', ') within group(order by b.id) as po_number";
	else  $po_number="";

	$sql= "SELECT b.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.po_number from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 $search_string group by b.id, a.job_no, a.insert_date, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.po_number order by b.id desc"; 
     //echo $sql;
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Style,Job No,Order No", "100,100,100,100","650","220",0, $sql , "js_set_value", "id,po_number,job_no,style_ref_no","",1,"company_name,buyer_name,0,0,0,0",$arr,"company_name,buyer_name,style_ref_no,job_no,po_number","",'','0,0,0,0,0','',1) ;
   exit(); 
}
	
if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$company_arr = return_library_array("SELECT id,company_name from lib_company", "id", "company_name");
	$color_arr = return_library_array("SELECT id,color_name from lib_color", "id", "color_name");
	$store_arr = return_library_array("SELECT id,store_name from lib_store_location", "id", "store_name");
	$buyer_arr = return_library_array("SELECT id,buyer_name from lib_buyer", "id", "buyer_name");
	$location_arr = return_library_array("SELECT id,location_name from lib_location", "id", "location_name");
	$country_arr = return_library_array("SELECT id,country_name from lib_country", "id", "country_name");
	// $floor_arr = return_library_array("SELECT id,floor_name from lib_prod_floor", "id", "floor_name");
	$floor_arr = return_library_array("select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.floor_id,a.floor_room_rack_name order by a.floor_room_rack_name", "floor_id", "floor_room_rack_name");
	
	

	$company_name = str_replace("'","",$cbo_company_name);
	$store_name = str_replace("'","",$cbo_store_name);
	$year = str_replace("'","",$cbo_year);
	$buyer_id = str_replace("'","",$cbo_buyer_id);
	$job_id = str_replace("'","",$hidden_job_id);
	$style_ref = str_replace("'","",$hidden_style_ref_id);
	$order_id = str_replace("'","",$hidden_order_id);
	$from_date = str_replace("'","",$from_date);
	//$to_date = str_replace("'","",$to_date);
	$value_with = str_replace("'","",$value_with);
	$get_upto = str_replace("'","",$get_upto);
	$days = str_replace("'","",$txt_days);
	$get_upto_qnty = str_replace("'","",$get_upto_qnty);
	$qnty = str_replace("'","",$txt_qnty);
	
	$sql_cond = "";
	$sql_cond .= ($company_name!="") ? " and a.company_name in($company_name)" : "";
	$sql_cond .= ($store_name!="") ? " and d.store_id in($store_name)" : "";
	$sql_cond .= ($buyer_id!=0) ? " and a.buyer_name=$buyer_id" : "";
	$sql_cond .= ($job_id!="") ? " and a.id=$job_id" : "";
	$sql_cond .= ($style_ref!="") ? " and a.style_ref_no=$style_ref" : "";
	$sql_cond .= ($order_id!="") ? " and b.id=$order_id" : "";

	if($from_date!="")
	{
		if($db_type==0) 
		{       
			$from_date=change_date_format($from_date,'yyyy-mm-dd');
			
		}
		else if($db_type==2) 
		{               
			$from_date=change_date_format($from_date,'','',1);
			
		}
		//$sql_cond .= " and f.delivery_date between '$from_date' and '$to_date'";
	}


	$sql = "SELECT a.id,a.job_no,a.buyer_name,a.style_ref_no,a.location_name,b.id as po_id,b.po_number,c.country_ship_date,c.color_number_id as color_id,c.country_id,f.floor_id as floor_id,
	sum(case when d.production_type=81 then e.production_qnty else 0 end) as rcv_qty,
	sum(case when d.production_type=82 then e.production_qnty else 0 end) as issue_qty,
	sum(case when d.production_type=83 then e.production_qnty else 0 end) as issue_return,
	sum(case when d.production_type=84 then e.production_qnty else 0 end) as rcv_return, 
	min(case when d.production_type=81 then f.delivery_date end) as rcv_date,
	min(case when d.production_type=82 then f.delivery_date end) as issue_date
	from  wo_po_details_master a,  wo_po_break_down b,  wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e, pro_gmts_delivery_mst f
	where a.id=b.job_id and b.id=c.po_break_down_id and c.id=e.color_size_break_down_id and b.id=d.po_break_down_id and f.id=d.delivery_mst_id and d.id=e.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0  and d.production_type in(81,82,83,84) and f.delivery_date <='$from_date' $sql_cond and e.production_qnty>0
	group by a.id,a.job_no,a.buyer_name,a.style_ref_no,a.location_name,b.id,b.po_number,c.country_ship_date,c.color_number_id,c.country_id,f.floor_id";
	
	//  echo $sql;die;
	$res = sql_select($sql);
	$data_array = array();
	foreach($res as $val)
	{
		$data_array[$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['buyer_name'] = $val['BUYER_NAME'];
		$data_array[$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['job_no'] = $val['JOB_NO'];
		$data_array[$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['style_ref_no'] = $val['STYLE_REF_NO'];
		$data_array[$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['po_number'] = $val['PO_NUMBER'];
		$data_array[$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['location_name'] = $val['LOCATION_NAME'];
		$data_array[$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['color_id'] = $val['COLOR_ID'];
		$data_array[$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['country_id'] = $val['COUNTRY_ID'];
		$data_array[$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['rcv_qty'] += $val['RCV_QTY'];
		$data_array[$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['issue_qty'] += $val['ISSUE_QTY'];
		$data_array[$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['issue_return'] += $val['ISSUE_RETURN'];
		$data_array[$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['rcv_return']  += $val['RCV_RETURN'];
		$data_array[$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['rcv_date'] = $val['RCV_DATE'];
		
	
		$data_array[$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['issue_date'] = $val['ISSUE_DATE'];
		$data_array[$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['floor_id'] .= $val['FLOOR_ID']."*";
		$data_array[$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['country_ship_date'] = $val['COUNTRY_SHIP_DATE'];


	}
	
	// echo "<pre>";print_r($data_array);die;

	$tbl_width=1660;
	ob_start();	
	?>
	<style>
		.wrd_brk{word-break: break-all;}
		.center{text-align: center;}
		.right{text-align: right;}
	</style>
	<fieldset>
		<table width="<?=$tbl_width;?>" border="0">
			<tr class="form_caption" style="border:none;">
				<td colspan="22" align="center" style="border:none;font-size:16px; font-weight:bold" ><p>Closing Stock Report (Export Godown)</p></td> 
			</tr>
			<tr class="form_caption" style="border:none;">
	
				<td colspan="22" align="center" style="border:none;font-size:16px; font-weight:bold" ><p>Company Name :<?=$company_arr[$company_name];?></p></td> 
			</tr>
			<tr class="form_caption" style="border:none;">
				<td colspan="22" align="center" style="border:none;font-size:16px; font-weight:bold" ><p>Date :<?=$from_date;?></p></td> 
			</tr>
		</table>
		<table width="<?=$tbl_width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left"> 
			<thead>
			<tr>
				<th rowspan="2" width="30">SL</th>
				<th colspan="8" width="800">Description</th>	
				<th colspan="4" width="250">Receive</th>	
				<th colspan="5" width="300">Issue</th>	
				<th rowspan="2" width="70">Country Ship. Date</th>
				<th rowspan="2" width="50" >AGE</th>
				<th rowspan="2" width="50" >DOH</th>
				<th rowspan="2" width="100" >Remarks</th>
			</tr>	
			<tr>		
				<th width="100"><p>Buyer</p></th>
				<th width="100"><p>Job</p></th>
				<th width="100" ><p>Style</p></th>
				<th width="100" ><p>Order</p></th>
				<th width="100" ><p>Country</p></th>
				<th width="100" ><p>Color</p></th>
				<th width="100" ><p>Location</p></th>
				<th width="100" ><p>Recv. Unit</p></th>

				<th width="100" ><p>1st Recv. Date</p></th>                    
				<th width="50" ><p>Recv.Qty </p></th>                    
				<th width="50" ><p>Issue Rtn</p></th>                    
				<th width="50" ><p>Cum.Rcv Qty</p></th> 

				<th width="100" ><p>Issue Date</p></th>                    
				<th width="50" ><p>Issue Qty</p></th>                    
				<th width="50" ><p>Recv.Rtn</p></th>                    
				<th width="50" ><p>Cum.Issue Qty</p></th>	
				<th width="50" ><p>Stock Qty.</p></th>						
			</tr>

			</thead>
		</table>  
		<div style="width:<?=$tbl_width+18;?>px; overflow-y:scroll; max-height:250px" id="scroll_body" > 
			<table width="<?=$tbl_width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left"> 
				<tbody>
					<?
					$i=1;	
					foreach($data_array as $po_id=>$po_data)
					{	
						foreach($po_data as $country_id=>$country_data)
						{
							foreach($country_data as $color_id=>$val)
							{
								if ($i%2==0)$bgcolor="#E9F3FF";	else $bgcolor="#FFFFFF"; 
								$cuml_rcv_qty = $val["rcv_qty"] + $val["issue_return"];
								$cuml_issue_qty = $val["issue_qty"] + $val["rcv_return"];
								$age = datediff('d',$val["rcv_date"] ,date("Y/m/d"));
								
								$doh =datediff('d',$val["issue_date"] ,date("Y/m/d"));
								$stock_qty=$cuml_rcv_qty - $cuml_issue_qty ;
								$floor_id_arr = array_unique(array_filter(explode("*",$val["floor_id"])));
								$floor_name = "";
								foreach ($floor_id_arr as $v) 
								{
									$floor_name.= ($floor_name=="") ? $floor_arr[$v] : ", ".$floor_arr[$v];
								}
								if(($value_with==2 && $stock_qty>0 || $value_with==1))
								{

									?>
									<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">        
										<td width="30" align="center"><? echo $i; ?></td>            
										<td  width="100"><p><? echo $buyer_arr[$val["buyer_name"]]; ?></p></td>  
										<td width="100"><p><? echo $val["job_no"]; ?></p> </td>  
										<td width="100"><p><? echo $val["style_ref_no"]; ?></p> </td>  
										<td width="100"><p><? echo $val["po_number"]; ?></p></td>  
										<td width="100"><p><? echo $country_arr[$val["country_id"]];?></p></td>  
										<td width="100"><p><? echo $color_arr[$val["color_id"]];  ?></p></td>  
										<td width="100"><p><? echo $location_arr[$val["location_name"]]; ?></p></td>  
										<td width="100"><p><? echo $floor_name;?></p></td>  
										<td width="100"><p><? echo $val["rcv_date"]; ?></p></td>  
										<td width="50"><p>
										   <a href="##" onClick="open_popup(<?=$po_id ?>,<?=$country_id ?>,<?=$color_id ?>,'rcv_qty_popup')">	
											   <? echo $val["rcv_qty"]; ?>
										   </a></p>

									    </td>  
										<td width="50"><p>
										<a href="##" onClick="open_popup(<?=$po_id ?>,<?=$country_id ?>,<?=$color_id ?>,'issue_rtn_popup')">	
											   <? echo $val["issue_return"]; ?>
										   </a></p>
										</td>  
										<td width="50"><p><? echo $cuml_rcv_qty; ?></p></td>  
										<td width="100"><p><? echo $val["issue_date"]; ?></p></td>  
										<td width="50"><p>
										<a href="##" onClick="open_popup(<?=$po_id ?>,<?=$country_id ?>,<?=$color_id ?>,'issue_qty_popup')">	
											   <? echo $val["issue_qty"]; ?>
										   </a></p>
										</td>  
										<td width="50"><p>
										<a href="##" onClick="open_popup(<?=$po_id ?>,<?=$country_id ?>,<?=$color_id ?>,'rcv_return_popup')">	
											  <? echo $val["rcv_return"]; ?>
							                </a></p>
										</td>  
										<td width="50"><p><? echo $cuml_issue_qty ;?></p></td>  
										<td width="50"><p><? echo $cuml_rcv_qty - $cuml_issue_qty ?></p></td>  
										<td width="70"><p><? echo $val["country_ship_date"]; ?></p></td>  
										<td width="50"><p><? echo $age ?></p></td>  
										<td width="50"><p><?  echo $doh  ?></p></td>  
										<td width="100"><p><? echo $buyer_arr[$val[""]]; ?></p></td>  

									</tr>
									<?
									$i++;		
									$total_rcv_qty+=$cuml_rcv_qty;		
									$total_issue_qty+=$cuml_issue_qty;	
									$total_no_of_pcs+=$cuml_rcv_qty - $cuml_issue_qty;
								}
								
								
								
							}	
						}
					} 
					?>
				</tbody> 
			</table> 
		</div>
		<table width="<?=$tbl_width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" align="left"> 
			<tfoot>
				<tr>		
					<th colspan="1" width="105" style="text-align: center">Total</th>
					<th  width="80"></th>
					<th  width="80"></th>
					<th  width="81" ></th>
					<th  width="81" ></th>
					<th width="81" ></th>
					<th width="81" ></th>
					<th width="80" ></th>
					

					<th width="81" ></th>                    
					<th width="40" ></th>                    
					<th width="40" ></th>                    
					<th style="text-align: left" width="40" ><p><?=$total_rcv_qty;?></p></th> 
					

					<th width="80" ></th>                    
					<th width="41" ></th>                    
					<th width="40" ></th>                    
					<th style="text-align: left" width="40" ><p><?=$total_issue_qty;?></p></th>	        
					<th style="text-align: left" width="40" ><p><?=$total_no_of_pcs;?></p></th>    

					<th width="57" ></th>	                   
					<th width="40" ></th>	                   
					<th width="40" ></th>	 
					<th width="81" ></th>                  
												
				</tr>

			</tfoot>
		</table> 
	</fieldset>
		
	<?
	
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) {
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename**$rpt_type"; 
	exit();
}


if($action=="rcv_qty_popup")
{
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	//echo $po_id;
   
   $store_arr = return_library_array("SELECT a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and b.category_type in(30) and a.status_active =1 and a.is_deleted=0 order by a.store_name", "id", "store_name");

   $floor_arr = return_library_array("SELECT b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.floor_id,a.floor_room_rack_name order by a.floor_room_rack_name", "floor_id", "floor_room_rack_name");

   $room_arr = return_library_array("select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name", "room_id", "floor_room_rack_name");
   $rack_arr = return_library_array("select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name", "rack_id", "floor_room_rack_name");
   $self_arr = return_library_array("select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name", "shelf_id", "floor_room_rack_name");

    $sql = "SELECT a.id,a.job_no,a.buyer_name,a.style_ref_no,a.location_name,b.id as po_id,b.po_number,b.pub_shipment_date,c.color_number_id as color_id,c.country_id,d.challan_no,f.floor_id as floor_id,
	sum(case when d.production_type=81 then e.production_qnty else 0 end) as rcv_qty, d.store_id, d.room_id, d.rack_id, d.shelf_id, f.delivery_date
	-- sum(case when d.production_type=82 then e.production_qnty else 0 end) as issue_qty,
	-- sum(case when d.production_type=83 then e.production_qnty else 0 end) as issue_return,
	-- sum(case when d.production_type=84 then e.production_qnty else 0 end) as rcv_return, 
	-- min(case when d.production_type=81 then f.delivery_date end) as rcv_date,
	-- min(case when d.production_type=82 then f.delivery_date end) as issue_date
	from  wo_po_details_master a,  wo_po_break_down b,  wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e, pro_gmts_delivery_mst f
	where a.id=b.job_id and b.id=c.po_break_down_id and c.id=e.color_size_break_down_id and b.id=d.po_break_down_id and f.id=d.delivery_mst_id and d.id=e.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0  and d.production_type in (81) and b.id=$po_id and c.color_number_id=$color_id and c.country_id=$country_id and e.production_qnty>0
	group by a.id,a.job_no,a.buyer_name,a.style_ref_no,a.location_name,b.id,b.po_number,b.pub_shipment_date,c.color_number_id,c.country_id,d.challan_no,f.floor_id, d.store_id, d.room_id, d.rack_id, d.shelf_id, f.delivery_date";
	 //echo $sql; die;
	$data_arr=sql_select($sql);  
	?> 
    <style>
      .wrd_brk{word-break: break-all;}
      .left{text-align: left;}
      .center{text-align: center;}
      .right{text-align: right;}
    </style>   
    <div id="data_panel" align="center" style="width:100%">
      <fieldset style="width: 98%">
        <table width="750" cellspacing="0" class="rpt_table" border="0" id="tbl_returnable_details" rules="all">
          <thead>
            <tr>
              <th width="30">Sl</th>
              <th width="120">Challan NO</th>
              <th width="120">Challan Date</th>
              <th width="100">QTY</th>
              <th width="100">Store</th>
              <th width="100">Floor</th>
              <th width="100">Room</th>
              <th width="100">Rack</th>
              <th width="100">Shelf</th>
              <!-- <th >Stock qty.</th> -->
            </tr>
          </thead>  
          <tbody>
            <?
              $i=1;
			  $rcv_qty=0;

              foreach ($data_arr as $row) 
              {  
					if ($i%2==0)  $bgcolor="#E9F3FF";	else $bgcolor="#FFFFFF";
                    ?>                         
                    <tr bgcolor="<?=$bgcolor; ?>" style="cursor:pointer">
                        <td class="center"><? echo $i;?></td>
                        <td class="center"><? echo $row[csf('challan_no')];?></td>
                        <td class="center"><? echo change_date_format($row[csf('delivery_date')]);?></td>
                        <td  style="text-align: center"><? echo  $row[csf('rcv_qty')];?></td>
                        <td  style="text-align: center"><? echo  $store_arr[$row[csf('store_id')]];?></td>
                        <td  style="text-align: center"><? echo  $floor_arr[$row[csf('floor_id')]];?></td>
                        <td  style="text-align: center"><? echo  $room_arr[$row[csf('room_id')]];?></td>
                        <td  style="text-align: center"><? echo  $rack_arr[$row[csf('rack_id')]];?></td>
                        <td  style="text-align: center"><? echo  $self_arr[$row[csf('shelf_id')]];?></td>
                        
                    </tr>
                    <?
                    $rcv_qty+=$row[csf('rcv_qty')];;
                    $i++;    
                               
              }
            ?>
          </tbody>   
          <tfoot>
            <tr>
              <th colspan="3"><b>Total</b></th>
              <th style="text-align: center"><?echo $rcv_qty;?></th>
              <th colspan="5"><b></b></th>
            </tr>
          </tfoot>    
        </table>
      </fieldset>
    </div> 
    <?
    exit(); 
}

if($action=="issue_rtn_popup")
{
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	echo $po_id;

	$store_arr = return_library_array("SELECT a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and b.category_type in(30) and a.status_active =1 and a.is_deleted=0 order by a.store_name", "id", "store_name");

   $floor_arr = return_library_array("select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.floor_id,a.floor_room_rack_name order by a.floor_room_rack_name", "floor_id", "floor_room_rack_name");

   $room_arr = return_library_array("select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name", "room_id", "floor_room_rack_name");
   $rack_arr = return_library_array("select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name", "rack_id", "floor_room_rack_name");
   $self_arr = return_library_array("select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name", "shelf_id", "floor_room_rack_name");

    $sql = "SELECT a.id,a.job_no,a.buyer_name,a.style_ref_no,a.location_name,b.id as po_id,b.po_number,b.pub_shipment_date,c.color_number_id as color_id,c.country_id,d.challan_no,d.floor_id,
	-- sum(case when d.production_type=81 then e.production_qnty else 0 end) as rcv_qty,
	-- sum(case when d.production_type=82 then e.production_qnty else 0 end) as issue_qty,
	sum(case when d.production_type=83 then e.production_qnty else 0 end) as issue_return, d.store_id, d.room_id, d.rack_id, d.shelf_id, f.delivery_date
	-- sum(case when d.production_type=84 then e.production_qnty else 0 end) as rcv_return, 
	-- min(case when d.production_type=81 then f.delivery_date end) as rcv_date,
	-- min(case when d.production_type=82 then f.delivery_date end) as issue_date
	from  wo_po_details_master a,  wo_po_break_down b,  wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e, pro_gmts_delivery_mst f
	where a.id=b.job_id and b.id=c.po_break_down_id and c.id=e.color_size_break_down_id and b.id=d.po_break_down_id and f.id=d.delivery_mst_id and d.id=e.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0  and d.production_type in(83) and b.id=$po_id and c.color_number_id=$color_id and c.country_id=$country_id
	group by a.id,a.job_no,a.buyer_name,a.style_ref_no,a.location_name,b.id,b.po_number,b.pub_shipment_date,c.color_number_id,c.country_id,d.challan_no,d.floor_id,  d.store_id, d.room_id, d.rack_id, d.shelf_id, f.delivery_date";
	 //echo $sql; die;
	$data_arr=sql_select($sql);  
	?> 
    <style>
      .wrd_brk{word-break: break-all;}
      .left{text-align: left;}
      .center{text-align: center;}
      .right{text-align: right;}
    </style>   
    <div id="data_panel" align="center" style="width:100%">
      <fieldset style="width: 98%">
        <table width="750" cellspacing="0" class="rpt_table" border="0" id="tbl_returnable_details" rules="all">
          <thead>
            <tr>
              <th width="30">Sl</th>
              <th width="120">Challan NO</th>
              <th width="120">Challan Date</th>
              <th width="100">QTY</th>
              <th width="100">Store</th>
              <th width="100">Floor</th>
              <th width="100">Room</th>
              <th width="100">Rack</th>
              <th width="100">Shelf</th>
              <!-- <th >Stock qty.</th> -->
            </tr>
          </thead>  
          <tbody>
            <?
              $i=1;
			  $issue_return=0;

              foreach ($data_arr as $row) 
              {  
                    ?>                         
                    <tr>
                    	<td class="center"><? echo $i;?></td>
                        <td class="center"><? echo $row[csf('challan_no')];?></td>
                        <td class="center"><? echo change_date_format($row[csf('delivery_date')]);?></td>
                        <td  style="text-align: center"><? echo  $row[csf('issue_return')];?></td>
                        <td  style="text-align: center"><? echo  $store_arr[$row[csf('store_id')]];?></td>
                        <td  style="text-align: center"><? echo  $floor_arr[$row[csf('floor_id')]];?></td>
                        <td  style="text-align: center"><? echo  $room_arr[$row[csf('room_id')]];?></td>
                        <td  style="text-align: center"><? echo  $rack_arr[$row[csf('rack_id')]];?></td>
                        <td  style="text-align: center"><? echo  $self_arr[$row[csf('shelf_id')]];?></td>
                    </tr>
                    <?
                    $issue_return+=$row[csf('issue_return')];;
                    $i++;    
                               
              }
            ?>
          </tbody>   
          <tfoot>
            <tr>
              <th colspan="3"><b>Total</b></th>
              <td style="text-align: center"><?echo $issue_return;?></td>
            </tr>
          </tfoot>    
        </table>
      </fieldset>
    </div> 
    <?
    exit(); 
}

if($action=="issue_qty_popup")
{
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	echo $po_id;
   

   $store_arr = return_library_array("SELECT a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and b.category_type in(30) and a.status_active =1 and a.is_deleted=0 order by a.store_name", "id", "store_name");

   $floor_arr = return_library_array("select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.floor_id,a.floor_room_rack_name order by a.floor_room_rack_name", "floor_id", "floor_room_rack_name");

   $room_arr = return_library_array("select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name", "room_id", "floor_room_rack_name");
   $rack_arr = return_library_array("select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name", "rack_id", "floor_room_rack_name");
   $self_arr = return_library_array("select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name", "shelf_id", "floor_room_rack_name");

    $sql = "SELECT a.id,a.job_no,a.buyer_name,a.style_ref_no,a.location_name,b.id as po_id,b.po_number,b.pub_shipment_date,c.color_number_id as color_id,c.country_id,d.challan_no,d.floor_id,

	sum(case when d.production_type=82 then e.production_qnty else 0 end) as issue_qty,  d.store_id, d.room_id, d.rack_id, d.shelf_id, f.delivery_date
	
	from  wo_po_details_master a,  wo_po_break_down b,  wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e, pro_gmts_delivery_mst f
	where a.id=b.job_id and b.id=c.po_break_down_id and c.id=e.color_size_break_down_id and b.id=d.po_break_down_id and f.id=d.delivery_mst_id and d.id=e.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0  and d.production_type in(82) and b.id=$po_id and c.color_number_id=$color_id and c.country_id=$country_id
	group by a.id,a.job_no,a.buyer_name,a.style_ref_no,a.location_name,b.id,b.po_number,b.pub_shipment_date,c.color_number_id,c.country_id,d.challan_no,d.floor_id,  d.store_id, d.room_id, d.rack_id, d.shelf_id, f.delivery_date";
	 //echo $sql; die;
	$data_arr=sql_select($sql);  
	?> 
    <style>
      .wrd_brk{word-break: break-all;}
      .left{text-align: left;}
      .center{text-align: center;}
      .right{text-align: right;}
    </style>   
    <div id="data_panel" align="center" style="width:100%">
      <fieldset style="width: 98%">
        <table width="750" cellspacing="0" class="rpt_table" border="0" id="tbl_returnable_details" rules="all">
          <thead>
            <tr>
              <th width="30">Sl</th>
              <th width="120">Challan NO</th>
              <th width="120">Challan Date</th>
              <th width="100">QTY</th>
              <th width="100">Store</th>
              <th width="100">Floor</th>
              <th width="100">Room</th>
              <th width="100">Rack</th>
              <th width="100">Shelf</th>
              <!-- <th >Stock qty.</th> -->
            </tr>
          </thead>  
          <tbody>
            <?
              $i=1;
			  $issue_qty=0;

              foreach ($data_arr as $row) 
              {  
                    ?>                         
                    <tr><td class="center"><? echo $i;?></td>
                        <td class="center"><? echo $row[csf('challan_no')];?></td>
                        <td class="center"><? echo change_date_format($row[csf('delivery_date')]);?></td>
                        <td  style="text-align: center"><? echo  $row[csf('issue_qty')];?></td>
                        <td  style="text-align: center"><? echo  $store_arr[$row[csf('store_id')]];?></td>
                        <td  style="text-align: center"><? echo  $floor_arr[$row[csf('floor_id')]];?></td>
                        <td  style="text-align: center"><? echo  $room_arr[$row[csf('room_id')]];?></td>
                        <td  style="text-align: center"><? echo  $rack_arr[$row[csf('rack_id')]];?></td>
                        <td  style="text-align: center"><? echo  $self_arr[$row[csf('shelf_id')]];?></td>                  
                    </tr>
                    <?
                    $issue_qty+=$row[csf('issue_qty')];;
                    $i++;    
                               
              }
            ?>
          </tbody>   
          <tfoot>
            <tr>
              <th colspan="3"><b>Total</b></th>
              <td style="text-align: center"><?echo $issue_qty;?></td>
            </tr>
          </tfoot>    
        </table>
      </fieldset>
    </div> 
    <?
    exit(); 
}


if($action=="rcv_return_popup")
{
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	//echo $po_id;

	$store_arr = return_library_array("SELECT a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and b.category_type in(30) and a.status_active =1 and a.is_deleted=0 order by a.store_name", "id", "store_name");

   $floor_arr = return_library_array("select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.floor_id,a.floor_room_rack_name order by a.floor_room_rack_name", "floor_id", "floor_room_rack_name");

   $room_arr = return_library_array("select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name", "room_id", "floor_room_rack_name");
   $rack_arr = return_library_array("select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name", "rack_id", "floor_room_rack_name");
   $self_arr = return_library_array("select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name", "shelf_id", "floor_room_rack_name");
   

    $sql = "SELECT a.id,a.job_no,a.buyer_name,a.style_ref_no,a.location_name,b.id as po_id,b.po_number,b.pub_shipment_date,c.color_number_id as color_id,c.country_id,d.challan_no,d.floor_id,
	sum(case when d.production_type=84 then e.production_qnty else 0 end) as rcv_return, d.store_id, d.room_id, d.rack_id, d.shelf_id, f.delivery_date
	
	from  wo_po_details_master a,  wo_po_break_down b,  wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e, pro_gmts_delivery_mst f
	where a.id=b.job_id and b.id=c.po_break_down_id and c.id=e.color_size_break_down_id and b.id=d.po_break_down_id and f.id=d.delivery_mst_id and d.id=e.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0  and d.production_type in(84) and b.id=$po_id and c.color_number_id=$color_id and c.country_id=$country_id
	group by a.id,a.job_no,a.buyer_name,a.style_ref_no,a.location_name,b.id,b.po_number,b.pub_shipment_date,c.color_number_id,c.country_id,d.challan_no,d.floor_id, d.store_id, d.room_id, d.rack_id, d.shelf_id, f.delivery_date";
	 //echo $sql; die;
	$data_arr=sql_select($sql);  
	?> 
    <style>
      .wrd_brk{word-break: break-all;}
      .left{text-align: left;}
      .center{text-align: center;}
      .right{text-align: right;}
    </style>   
    <div id="data_panel" align="center" style="width:100%">
      <fieldset style="width: 98%">
        <table width="750" cellspacing="0" class="rpt_table" border="0" id="tbl_returnable_details" rules="all">
          <thead>
            <tr>
              <th width="30">Sl</th>
              <th width="120">Challan NO</th>
              <th width="120">Challan Date</th>
              <th width="100">QTY</th>
              <th width="100">Store</th>
              <th width="100">Floor</th>
              <th width="100">Room</th>
              <th width="100">Rack</th>
              <th width="100">Shelf</th>
              <!-- <th >Stock qty.</th> -->
            </tr>
          </thead>  
          <tbody>
            <?
              $i=1;
			  $rcv_return=0;

              foreach ($data_arr as $row) 
              {  
                    ?>                         
                    <tr>
                    	<td class="center"><? echo $i;?></td>
                        <td class="center"><? echo $row[csf('challan_no')];?></td>
                        <td class="center"><? echo change_date_format($row[csf('delivery_date')]);?></td>
                        <td  style="text-align: center"><? echo  $row[csf('rcv_return')];?></td>
                        <td  style="text-align: center"><? echo  $store_arr[$row[csf('store_id')]];?></td>
                        <td  style="text-align: center"><? echo  $floor_arr[$row[csf('floor_id')]];?></td>
                        <td  style="text-align: center"><? echo  $room_arr[$row[csf('room_id')]];?></td>
                        <td  style="text-align: center"><? echo  $rack_arr[$row[csf('rack_id')]];?></td>
                        <td  style="text-align: center"><? echo  $self_arr[$row[csf('shelf_id')]];?></td>
                    </tr>
                    <?
                    $rcv_return+=$row[csf('rcv_return')];;
                    $i++;    
                               
              }
            ?>
          </tbody>   
          <tfoot>
            <tr>
              <th colspan="3"><b>Total</b></th>
              <td style="text-align: center"><?echo $rcv_return;?></td>
            </tr>
          </tfoot>    
        </table>
      </fieldset>
    </div> 
    <?
    exit(); 
}
?>
