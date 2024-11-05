<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$user_name			= $_SESSION['logic_erp']['user_id'];
$data				= $_REQUEST['data'];
$action				= $_REQUEST['action'];
$company_arr		= return_library_array( "select id, company_name from lib_company",'id','company_name');
$color_library		= return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$order_no_library	= return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"  );
$buyer_arr			= return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$table_arr			= return_library_array( "select id, table_no from lib_cutting_table",'id','table_no');
$floor_arr			= return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');

//--------------------------------------------------------------------------------------------------------------------
if($action=="style_search_popup_")
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $job_no; ?>', 'create_style_no_search_list_view', 'search_div', 'cutting_status_report_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$arr=array (0=>$company_library,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql= "SELECT a.id,a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $job_no_cond $date_cond group by a.id,a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,a.insert_date order by a.job_no desc";
	// echo $sql;
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No", "120,100,50,80,140","570","220",0, $sql , "js_set_value", "id,job_no,style_ref_no","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no,style_ref_no","",'','0,0,0,0,0','',1) ;
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $job_no; ?>', 'create_int_ref_search_list_view', 'search_div', 'cutting_status_report_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="fileno_search_popup_")
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
	echo create_list_view("list_view", "File. No,Job No,Year","160,90,100","410","300",0, $sql , "js_set_value", "id,job_no,grouping,file_no", "", 1, "0", $arr, "file_no,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","","") ;	
	//echo "<input type='hidden' id='txt_selected_id' />";
	//echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	
	exit();
}

if($action=="fileno_search_popup")
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $job_no; ?>', 'create_fileno_search_list_view', 'search_div', 'cutting_status_report_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	// echo $sql;
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Int. Ref. No, Po No, Shipment Date", "80,80,50,70,140,170","760","220",0, $sql , "js_set_value", "id,po_number,file_no","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,file_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3','',1) ;
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $job_no; ?>', 'create_order_no_search_list_view', 'search_div', 'cutting_status_report_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	//echo $sql; die;
		
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

if ($action=="load_drop_down_gmts_item")
{
	//echo "select gmts_item_id from wo_po_details_master where job_no='$data'";
	$gmts_item=return_field_value("gmts_item_id","wo_po_details_master","job_no='$data'","gmts_item_id");
	
	echo create_drop_down( "cbo_gmts_item", 100, $garments_item,"", 1, "-- Select --", $selected, "","",$gmts_item,"" );     	 
	exit();
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');

	extract($_REQUEST);
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $sytle_ref_no; ?>', 'create_job_no_search_list_view', 'search_div', 'cutting_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	$company_id=$data[0];
	//$year_id=$data[4];
	//$month_id=$data[5];
	//echo $month_id;
	
	var_dump($data);
	
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
		$buyer_id_cond=" and buyer_name=$data[1]";
	}
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}

	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	echo $sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and style_ref_no='$data[4]' and $search_field  like '$search_string' $buyer_id_cond $year_cond order by job_no";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','','') ;
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
	                               <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value, 'create_cutting_search_list_view', 'search_div', 'cutting_status_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
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
	
	$sql_order="select a.id,a.cut_num_prefix_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.marker_length, a.marker_width, a.fabric_width,d.order_id,d.color_id,$year FROM ppl_cut_lay_mst a,wo_po_details_master b,wo_po_break_down c,ppl_cut_lay_dtls d where  a.id=d.mst_id and a.job_no=b.job_no and b.job_no=c.job_no_mst and a.entry_form=76 and c.id=d.order_id $conpany_cond  $cut_cond $job_cond $sql_cond $order_cond order by id";
	$arr=array(2=>$table_arr,4=>$order_no_library,5=>$color_library);
	echo create_list_view("list_view", "Cut No,Year,Table No,Job No,Order NO,Color,Marker Length,Markar Width,Fabric Width,Entry Date","90,50,60,120,120,100,80,80,80,120","950","270",0, $sql_order , "js_set_cutting_value", "cut_num_prefix_no", "", 1, "0,0,table_no,0,order_id,color_id,0,0,0,0,0", $arr, "cut_num_prefix_no,year,table_no,job_no,order_id,color_id,marker_length,marker_width,fabric_width,entry_date", "","setFilterGrid('list_view',-1)") ;
}

if ($action == "report_generate" )
{
	// var_dump($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$rept_type			= str_replace( "'", "", $type );
	$company_name		= str_replace( "'", "", $cbo_company_name );
	$buyer_name			= str_replace( "'", "", $cbo_buyer_name );
	$ref_no				= str_replace( "'", "", $txt_ref_no);
	$txt_style_ref_id	= str_replace( "'", "", $txt_style_ref_id);
	$job_no				= str_replace( "'", "", $txt_job_no );
	$job_id_hidden		= str_replace( "'", "", $txt_job_no_hidden);
	$order_no			= str_replace( "'", "", $txt_order_no );
	$hide_order_id		= str_replace( "'", "", $hide_order_id );
	$int_ref			= str_replace( "'", "", $int_ref);
	$file_no			= str_replace( "'", "", $file_no );	
	$gmts_item			= str_replace( "'", "", $cbo_gmts_item );
	
	$sql_cond	= "";
	$po_cond 	= "";
	
	if($company_name>0) $sql_cond=" AND a.company_id=$company_name";
	if($buyer_name>0) $sql_cond.=" AND c.buyer_name=$buyer_name";
	if($gmts_item>0) $sql_cond.=" AND b.gmt_item_id in($gmts_item)";
	if($gmts_item>0) $gmt_item_cond=" AND item_number_id in($gmts_item)";else $gmt_item_cond="";
	if($order_no>0) $po_id_con=" AND po_break_down_id in($hide_order_id)";else $po_id_con="";
	// if($job_no !="") $sql_cond.=" AND c.job_no='$job_no' ";
	if($job_id_hidden !="") $sql_cond.=" AND c.id in($job_id_hidden)";
	// if($order_no !="") $sql_cond.=" AND d.po_number='$order_no' ";
	if($hide_order_id !="") $sql_cond.=" AND d.id in($hide_order_id) ";
	// if($file_no !="") $sql_cond.=" AND d.file_no='$file_no' ";
	// if($int_ref !="") $sql_cond.=" AND d.grouping='$int_ref' ";
	
	if($order_no=="")
	{
		$po_cond="";
	}
	else
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id=str_replace("'","",$hide_order_id);
			$po_cond="and d.id in(".$po_id.")";
		}
		else
		{
			$po_number="%".trim($order_no)."%";
			$po_cond="and d.po_number like '$po_number'";
		}
	}
	
	if($rept_type==1) 
	{
		// ========================================= MAIN QUERY ========================================		
		/*$sql="SELECT a.id AS cut_id,a.company_id,a.location_id,a.cutting_no,a.marker_length,a.marker_width,b.marker_qty as mark_qty,g.floor_id, a.table_no,a.job_no,a.batch_id,
        a.lay_fabric_wght,a.cad_marker_cons, a.entry_date,a.working_company_id, b.order_ids AS order_ids, b.order_id AS po_id,b.gmt_item_id AS gmt_id,
        b.order_cut_no, b.color_id,b.plies,c.buyer_name, c.style_ref_no,c.style_description, d.id, d.file_no, d.GROUPING, d.po_number, f.dia_width,h.body_part_id,h.costing_per
    	FROM 
	    	ppl_cut_lay_mst a, 
	    	ppl_cut_lay_dtls b, 
	    	wo_po_details_master c, 
	    	wo_po_break_down d, 
	    	wo_booking_mst e, 
	    	wo_booking_dtls f, 
	    	lib_cutting_table g, 
	    	wo_pre_cost_fabric_cost_dtls h,
	    	wo_pre_cos_fab_co_avg_con_dtls i
   		WHERE   b.mst_id = a.id
	        AND d.id = b.order_id
	        AND d.job_no_mst = c.job_no
	        AND c.job_no = e.job_no
	        AND g.id = a.table_no
	        AND g.company_id = $company_name
	        AND e.booking_no = f.booking_no
	        AND c.job_no = h.job_no
	        AND h.id = i.pre_cost_fabric_cost_dtls_id
	        AND f.dia_width=i.dia_width
	        AND i.dia_width= to_char( a.marker_width)
	        AND b.order_id IS NOT NULL
	        AND a.job_no = c.job_no $sql_cond $po_cond
	        AND a.status_active=1
	        AND c.status_active=1
	        AND e.status_active=1
	        AND g.status_active=1
	        AND h.status_active=1
	        AND h.body_part_type in(1,20)
		ORDER BY a.cutting_no, b.color_id ASC";*/
		
		$sql="SELECT a.id AS CUT_ID, a.company_id AS COMPANY_ID, a.location_id AS LOCATION_ID, a.cutting_no AS CUTTING_NO, a.marker_length AS MARKER_LENGTH, a.marker_width AS MARKER_WIDTH, b.marker_qty AS MARK_QTY, g.floor_id AS FLOOR_ID, a.table_no AS TABLE_NO, a.job_no AS JOB_NO, a.batch_id AS BATCH_ID, a.lay_fabric_wght AS LAY_FABRIC_WGHT, a.cad_marker_cons AS CAD_MARKER_CONS, a.entry_date AS ENTRY_DATE, a.working_company_id AS WORKING_COMPANY_ID, b.order_ids AS ORDER_IDS, b.order_id AS PO_ID, b.gmt_item_id AS GMT_ID, b.order_cut_no AS ORDER_CUT_NO, b.color_id AS COLOR_ID, b.plies AS PLIES, c.buyer_name AS BUYER_NAME, c.style_ref_no AS STYLE_REF_NO, c.style_description AS STYLE_DESCRIPTION, d.file_no AS FILE_NO, d.grouping AS GROUPING, d.po_number AS PO_NUMBER, f.dia_width AS DIA_WIDTH, h.body_part_id AS BODY_PART_ID, h.costing_per AS COSTING_PER
    	FROM 
	    	ppl_cut_lay_mst a, 
	    	ppl_cut_lay_dtls b, 
	    	wo_po_details_master c, 
	    	wo_po_break_down d, 
	    	wo_booking_mst e, 
	    	wo_booking_dtls f, 
	    	lib_cutting_table g, 
	    	wo_pre_cost_fabric_cost_dtls h,
	    	wo_pre_cos_fab_co_avg_con_dtls i
   		WHERE  a.company_id = $company_name
			AND a.id = b.mst_id
			AND a.job_no = c.job_no
	        AND b.order_id = d.id
			AND b.order_id IS NOT NULL
	        AND c.id = d.job_id
	        AND c.job_no = f.job_no
	        AND g.id = a.table_no
	        
	        AND e.booking_no = f.booking_no
			
			AND d.id = f.po_break_down_id
			AND f.po_break_down_id = i.po_break_down_id
			AND f.pre_cost_fabric_cost_dtls_id = h.id
			AND f.pre_cost_fabric_cost_dtls_id = i.pre_cost_fabric_cost_dtls_id
			
	        AND c.id = h.job_id
			AND c.id = i.job_id
			AND d.id = i.po_break_down_id
	        AND h.id = i.pre_cost_fabric_cost_dtls_id
			AND b.color_id=i.color_number_id
	        AND f.dia_width=i.dia_width
	        AND i.dia_width= to_char( a.marker_width)
	        
	         $sql_cond $po_cond
	        AND a.status_active=1
	        AND c.status_active=1
	        AND e.status_active=1
	        AND g.status_active=1
	        AND h.status_active=1
	        AND h.body_part_type in(1,20)
		ORDER BY a.cutting_no, b.color_id ASC";
		// echo $sql;die;	
		$sql_res=sql_select($sql);	
		if(count($sql_res)==0)
		{
			echo "<div style='text-align:center;color:red;font-size:20px;font-weight:bold;'>No Data Found.</div>";
			die;
		}
		$cut_color_arr=array();
		$all_po_id='';
		$dia_wies_fab_lay_qty=array();
		foreach($sql_res as $row)
		{
			if($row['PO_ID']!="") $all_po_id.=$row['PO_ID'].",";			
			$all_cut_id.=$row['CUT_ID'].",";

			$cut_color_arr[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']][$row['CUTTING_NO']]['cuting_date'] 		= $row['ENTRY_DATE'];
			//$cut_color_arr[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']][$row['CUTTING_NO']]['cutting_no']		= $row['CUTTING_NO'];
			$cut_color_arr[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']][$row['CUTTING_NO']]['company_id']		= $row['COMPANY_ID'];
			$cut_color_arr[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']][$row['CUTTING_NO']]['location_id']		= $row['LOCATION_ID'];
			$cut_color_arr[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']][$row['CUTTING_NO']]['working_company']	= $row['WORKING_COMPANY_ID'];
			$cut_color_arr[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']][$row['CUTTING_NO']]['table_no']			= $table_arr[$row['TABLE_NO']];
			//$cut_color_arr[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']][$row['CUTTING_NO']]['order_id']		= $row[csf('order_id')];
			$cut_color_arr[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']][$row['CUTTING_NO']]['order_cut_no']		= $row['ORDER_CUT_NO'];
			$cut_color_arr[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']][$row['CUTTING_NO']]['job_no']			= $row['JOB_NO'];
			$cut_color_arr[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']][$row['CUTTING_NO']]['batch_id']			= $row['BATCH_ID'];
			$cut_color_arr[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']][$row['CUTTING_NO']]['plies']				= $row['PLIES'];
			$cut_color_arr[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']][$row['CUTTING_NO']]['buyer_name']		= $row['BUYER_NAME'];
			$cut_color_arr[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']][$row['CUTTING_NO']]['style_ref_no']		= $row['STYLE_REF_NO'];
			$cut_color_arr[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']][$row['CUTTING_NO']]['style_description'] = $row['STYLE_DESCRIPTION'];
			$cut_color_arr[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']][$row['CUTTING_NO']]['lay_fabric_wght']	= $row['LAY_FABRIC_WGHT'];
			$cut_color_arr[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']][$row['CUTTING_NO']]['cad_marker_cons']	= $row['CAD_MARKER_CONS'];
			//$cut_color_arr[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']][$row['CUTTING_NO']]['marker_qty'] 		= $row[csf('marker_qty')];
			$cut_color_arr[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']][$row['CUTTING_NO']]['gmt_item_id'] 		= $row['GMT_ID'];
			$cut_color_arr[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']][$row['CUTTING_NO']]['grouping']			= $row['GROUPING'];
			$cut_color_arr[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']][$row['CUTTING_NO']]['file_no'] 			= $row['FILE_NO'];
			$cut_color_arr[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']][$row['CUTTING_NO']]['po_number'] 		= $row['PO_NUMBER'];
			$cut_color_arr[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']][$row['CUTTING_NO']]['floor_id'] 			= $row['FLOOR_ID'];
			$cut_color_arr[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']][$row['CUTTING_NO']]['marker_length'] 	= $row['MARKER_WIDTH'];
			$cut_color_arr[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']][$row['CUTTING_NO']]['marker_width'] 		= $row['marker_width'];			
			$cut_color_arr[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']][$row['CUTTING_NO']]['mark_qty'] 			+= $row['MARK_QTY'];			
			$cut_color_arr[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']][$row['CUTTING_NO']]['costing_per'] 		= $row['COSTING_PER'];			
			$dia_wies_fab_lay_qty[$row['PO_ID']][$row['GMT_ID']][$row['COLOR_ID']][$row['DIA_WIDTH']] 									+= $row['MARK_QTY'];		
		}
		// echo "<pre>";
		// print_r($cut_color_arr);
		// echo "</pre>";
		//echo $all_po_id;
		$all_po_id=chop($all_po_id,",");
		$all_cut_id=chop($all_cut_id,",");		
				
		
		$po_chnk=array_chunk(array_unique(explode(",",$all_po_id)),1000, true);
		 $po_cond="";
		   $x=0;
		   foreach($po_chnk as $key=> $value)
		   {
			   if($x==0)
			   {
					$po_cond=" and id  in(".implode(",",$value).")"; 	
			   }
			   else
			   {
					$po_cond.=" or id  in(".implode(",",$value).")";	
			   }
			   $x++;
		   }
				   
		
		$all_po_id=implode(",",array_unique(explode(",",$all_po_id)));
		$all_cut_id=implode(",",array_unique(explode(",",$all_cut_id)));

		// ================================ GETTING FAB LAY QNTY ================================================
		/*$lay_sql = "SELECT a.cutting_no,a.marker_width as dia_width, b.color_id,b.gmt_item_id,b.order_id as po_id,sum(c.roll_wgt) as qnty FROM ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_roll_dtls c WHERE a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and b.order_id in($all_po_id) and a.status_active=1 and b.status_active=1 and c.status_active=1 GROUP BY a.cutting_no,a.marker_width, b.color_id,b.gmt_item_id,b.order_id";
		// echo $lay_sql;die();
		$lay_sql_res = sql_select($lay_sql);
		$fab_lay_qnty_arr = array();
		$fab_lay_qnty_color_arr = array();
		foreach ($lay_sql_res as $key => $row) 
		{
			$fab_lay_qnty_arr[$row[csf('po_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('dia_width')]][$row[csf('cutting_no')]] = $row[csf('qnty')];
			$fab_lay_qnty_color_arr[$row[csf('po_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('dia_width')]] += $row[csf('qnty')];
		}*/

		$lay_sql = "SELECT a.cutting_no,a.marker_width as dia_width, b.color_id,b.gmt_item_id,b.order_id as po_id,b.roll_data FROM ppl_cut_lay_mst a,ppl_cut_lay_dtls b WHERE a.id=b.mst_id and b.order_id in($all_po_id) and a.status_active=1 and b.status_active=1 GROUP BY a.cutting_no,a.marker_width, b.color_id,b.gmt_item_id,b.order_id,b.roll_data";
		// echo $lay_sql;die();
		$lay_sql_res = sql_select($lay_sql);
		$fab_lay_qnty_arr = array();
		$fab_lay_qnty_color_arr = array();
		foreach ($lay_sql_res as $key => $row) 
		{
			$multi_roll_data_arr = explode("**", $row[csf('roll_data')]);
			foreach ($multi_roll_data_arr as $val) 
			{
				$roll_data_arr = explode("=", $val);
				$fab_lay_qnty_arr[$row[csf('po_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('dia_width')]][$row[csf('cutting_no')]] += $roll_data_arr[3];
				$fab_lay_qnty_color_arr[$row[csf('po_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('dia_width')]] += $roll_data_arr[3];
			}
			
		}

		// echo "<pre>";
		// print_r($fab_lay_qnty_arr);
		// echo "</pre>";

		//================================= GETTING BUNDLE DATA (color size quantity) ===========================================
		$bundle_array=array();
		$bundle_sql=sql_select("SELECT a.cutting_no,a.marker_width as dia_width, b.color_id,b.gmt_item_id as gmt_id, c.size_id, c.size_qty,b.order_id as po_id
		from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c  where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id in($all_cut_id)");
		$bundle_data_array=array();
		$bundle_color_data_array=array();
		$bundle_size_data_array=array();
		$cut_tot_bundle=array();
		$color_qty_bundle=array();
		$bundle_num=array();
		foreach($bundle_sql as $row)
		{
			$bundle_data_array[$row[csf('cutting_no')]][$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('po_id')]][$row[csf('dia_width')]][$row[csf('size_id')]]+=$row[csf('size_qty')];
			$bundle_color_data_array[$row[csf('cutting_no')]][$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('po_id')]][$row[csf('dia_width')]]+=$row[csf('size_qty')];
			$bundle_size_data_array[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('po_id')]][$row[csf('size_id')]]+=$row[csf('size_qty')];
			$bundle_num[$row[csf('po_id')]][$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]++;
			$cut_tot_bundle[$row[csf('cutting_no')]][$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('po_id')]]++;
		}
		// echo "<pre>";
		// print_r($bundle_color_data_array);
		// echo "</pre>";
		// ====================================== GETTING SIZE =====================================		
		$size_arr=return_library_array( "SELECT id, size_name from lib_size",'id','size_name');
		$sql_query=sql_select("SELECT po_break_down_id, country_type, country_id, size_number_id, plan_cut_qnty, order_quantity, country_ship_date, size_order,color_number_id,item_number_id 
		from wo_po_color_size_breakdown where  status_active=1 and is_deleted=0 and po_break_down_id in($all_po_id) order by size_order");
		
		$sizeId_arr=$size_order_data=$order_dtls_arr=$color_order_data=array();
		foreach($sql_query as $row)
		{
			$sizeId_arr[$row[csf('size_number_id')]]=$row[csf("size_number_id")];
			$size_order_data[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order_quantity']+=$row[csf('order_quantity')];
			$size_order_data[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['plan_cut_qnty']+=$row[csf('plan_cut_qnty')];
			$color_order_data[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['plan_cut_qnty']+=$row[csf('plan_cut_qnty')];
		}
		// echo "<pre>";
		// print_r($sizeId_arr);die;
		// echo count($sizeId_arr);

		$pcs_value=0;
		$set_item_ratio = "SELECT a.set_item_ratio,b.item_number_id as gmts_item,c.color_number_id as color_id,c.dia_width,c.po_break_down_id as po_id
		FROM wo_po_details_mas_set_details a, wo_pre_cost_fabric_cost_dtls b, wo_pre_cos_fab_co_avg_con_dtls c
		WHERE a.job_no=b.job_no and b.id=c.pre_cost_fabric_cost_dtls_id and c.po_break_down_id in($all_po_id) and b.status_active=1";

		$set_item_ratio_res = sql_select($set_item_ratio);
		$set_item_ratio_array = array();
		foreach ($set_item_ratio_res as $key => $val) 
		{
			$set_item_ratio_array[$val[csf('po_id')]][$val[csf('gmts_item')]][$val[csf('color_id')]][$val[csf('dia_width')]] += $val[csf('set_item_ratio')];
		}
		// echo "<pre>";
		// print_r($set_item_ratio_array);
		// echo "</pre>";
		// $set_item_ratio=return_field_value("set_item_ratio", "wo_po_details_mas_set_details", "job_no='$job_no'");

		// ========================================= FOR COLOR QUANTITY ==================================

		$color_qnty_sql=sql_select("SELECT a.item_number_id as gmt_id,a.costing_per, b.id,b.po_break_down_id as po_id, b.color_number_id as color_id, b.gmts_sizes,b.dia_width, b.item_size,b.cons,
        b.process_loss_percent,b.requirment, b.pcs,b.color_size_table_id, b.rate,b.amount,b.remarks   
        FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b
   		WHERE A.JOB_NO=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id in($all_po_id)
		ORDER BY id");
		$color_qty_bundle=array();
		$color_lay_qty=array();
		foreach($color_qnty_sql as $row)
		{			
			$color_qty_bundle[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('po_id')]][$row[csf('dia_width')]]+=$row[csf('requirment')];//[$row[csf('cutting_no')]]
			$color_lay_qty[$row[csf('po_id')]][$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('dia_width')]]+=$row[csf('requirment')];
		}

		/*$bundle_sql=sql_select("SELECT a.cutting_no, b.color_id,b.gmt_item_id as gmt_id, c.size_id, c.size_qty,b.order_id as po_id, d.dia_width,b.marker_qty as mark_qty
		from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c,wo_booking_dtls d  where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and b.order_id=d.po_break_down_id and d.gmts_color_id=b.color_id and d.status_active=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id in($all_cut_id)");
		$color_qty_bundle=array();
		$color_lay_qty=array();
		foreach($bundle_sql as $row)
		{			
			$color_qty_bundle[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('po_id')]][$row[csf('dia_width')]]+=$row[csf('size_qty')];//[$row[csf('cutting_no')]]
			$color_lay_qty[$row[csf('po_id')]][$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('dia_width')]]=$row[csf('mark_qty')];
		}*/
		// echo "<pre>";
		// print_r($color_lay_qty);
		// echo "</pre>";

		// ========================================== FOR COLOR REQUIRED QUANTITY =========================
		$color_req_qnty_sql="SELECT a.item_number_id as gmt_id,a.costing_per,b.po_break_down_id as po_id, b.fabric_color_id as color_id,b.dia_width , sum(b.fin_fab_qnty) fin_qnty
        FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b
   		WHERE A.JOB_NO=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id in($all_po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
   		GROUP by a.item_number_id ,a.costing_per,b.po_break_down_id, b.fabric_color_id,b.dia_width";
   		// echo $color_req_qnty_sql;
		$color_req_qnty_sql_res = sql_select($color_req_qnty_sql);
		$color_req_qty_arr=array();
		foreach($color_req_qnty_sql_res as $row)
		{
			$color_req_qty_arr[$row[csf('po_id')]][$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('dia_width')]] = $row[csf('fin_qnty')];
		}
		// echo "<pre>";
		// print_r($color_req_qty_arr);
		// echo "</pre>";
		// ========================================== GETTING RATIO ======================================
		$sratio_sql=sql_select("SELECT a.cutting_no,a.marker_width as dia_width, c.color_id, c.size_ratio,b.order_id,b.gmt_item_id as gmt_id
		from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_size_dtls c  where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.order_id in($all_po_id)");
		$size_ratio_array=array();
		foreach($sratio_sql as $row)
		{
			$size_ratio_array[$row[csf('cutting_no')]][$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('dia_width')]][$row[csf('order_id')]]+=$row[csf('size_ratio')];	
		}
       	// echo "<pre>";
       	// print_r($size_ratio_array);
       	// echo "</pre>";

		// ======================================= GETTING BATCH ========================================
		$batch_sql=sql_select("SELECT a.cutting_no,a.marker_width as dia_width, c.batch_no,b.order_id,b.color_id,b.gmt_item_id as gmt_id
		from ppl_cut_lay_mst a, ppl_cut_lay_dtls b,pro_roll_details c  where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form!=99 and c.status_active=1 and c.is_deleted=0 and a.id in($all_cut_id)");
		
		$batch_array=array();
		foreach($batch_sql as $row)
		{					
			$batch_array[$row[csf('cutting_no')]][$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('dia_width')]][$row[csf('order_id')]].=$row[csf('batch_no')].',';
		}
		// print_r($size_ratio_array);
		//print_r($cut_color_arr);die;
		// ===================================== FOR FINISH FAB RCV QUANTITY ======================================
		$sql_fin = "SELECT a.dia_width, e.color_id, e.po_breakdown_id AS po_id, sum(e.quantity) as qnty
	  	FROM product_details_master a,  order_wise_pro_details e
       	WHERE a.id = e.prod_id and e.trans_id>0 and a.item_category_id=2 and e.trans_type=1 and e.entry_form in(7,37,66,68,225) and e.po_breakdown_id in($all_po_id) and a.status_active=1 and e.status_active=1 group by a.dia_width, e.color_id, e.po_breakdown_id";
       	$sql_fin_res = sql_select($sql_fin);
       	$fin_qnty_arr = array();
       	// $fin_qnty_arr2 = array();
       	foreach ($sql_fin_res as $key => $val) 
       	{
       		$fin_qnty_arr[$val[csf('po_id')]][$val[csf('color_id')]][$val[csf('dia_width')]]=$val[csf('qnty')];
       		// $fin_qnty_arr2[$val[csf('po_id')]][$val[csf('color_id')]]=+$val[csf('qnty')];
       	}
       	// echo "<pre>";
       	// print_r($fin_qnty_arr);
       	// echo "</pre>";
		// ===================================== FOR FINISH FAB ISSUE QUANTITY ======================================
		$sql_fin_fab_issue_sql = "SELECT a.dia_width, e.color_id, e.po_breakdown_id AS po_id, sum(e.quantity) as qnty
	  	FROM product_details_master a,  order_wise_pro_details e
       	WHERE a.id = e.prod_id  and e.trans_id>0 and a.item_category_id=2 and e.trans_type=2 and e.entry_form in(18,71) and e.po_breakdown_id in($all_po_id) and a.status_active=1 and e.status_active=1 group by a.dia_width, e.color_id, e.po_breakdown_id"; // inv_finish_fabric_issue_dtls b,  b.gmt_item_id and a.id = b.prod_id ,b.gmt_item_id
       	// echo $sql_fin_fab_issue_sql;
       	$sql_fin_fab_issue_sql_res = sql_select($sql_fin_fab_issue_sql);
       	$fin_fab_issue_qnty_arr = array();
       	foreach ($sql_fin_fab_issue_sql_res as $key => $val) 
       	{
       		// $fin_fab_issue_qnty_arr[$val[csf('po_id')]][$val[csf('gmt_item_id')]][$val[csf('color_id')]][$val[csf('dia_width')]]=$val[csf('qnty')];
       		$fin_fab_issue_qnty_arr[$val[csf('po_id')]][$val[csf('color_id')]][$val[csf('dia_width')]]=$val[csf('qnty')];
       	}
       	// echo "<pre>";
       	// print_r($fin_fab_issue_qnty_arr);
       	// echo "</pre>";
       	// ===================================== FOR FINISH CONS ======================================
		$sql_fin_con = "SELECT a.avg_finish_cons,a.item_number_id,a.avg_cons,b.cons,b.po_break_down_id as po_id,b.dia_width,b.requirment,b.color_number_id as color_id 
		FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b
		WHERE a.job_no=b.job_no	and a.id = b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id in($all_po_id)
		and a.status_active=1 ";
       	$sql_fin_con_res = sql_select($sql_fin_con);
       	$fin_con_arr = array();
       	foreach ($sql_fin_con_res as $key => $val) 
       	{
       		$fin_con_arr[$val[csf('po_id')]][$val[csf('item_number_id')]][$val[csf('color_id')]][$val[csf('dia_width')]]=$val[csf('cons')];
       	}
       	// echo "<pre>";
       	// print_r($fin_con_arr);
       	// echo "</pre>";

		//echo $sql;
		$rowSpan = array();
		$rowSpanDia = array();
		$size_wise_cut_arr = array();
		$color_size_sub_tot_arr = array();
		$lay_sub_tot_arr = array();
		$fin_qnty_sub_tot_arr = array();
		$inhand_qnty_sub_tot_arr = array();
		foreach ($cut_color_arr as $po_id => $po_data) 
		{
		 	foreach ($po_data as $gmts_item_id => $gmts_item_data) 
		 	{
		 		foreach ($gmts_item_data as $color_id => $color_data) 
		 		{ 
		 			// foreach ($color_data as $body_part_id => $body_part_data) 
		 			// {
			 			foreach ($color_data as $dia => $dia_data) 
			 			{
			 				// $fin_qnty_sub_tot_arr[$po_id][$color_id] += $fin_qnty_arr[$po_id][$color_id][$dia];
			 				$fin_qnty_sub_tot_arr[$po_id][$color_id] += $fin_fab_issue_qnty_arr[$po_id][$color_id][$dia];// [$gmts_item_id]
			 				$inhand_qnty_sub_tot_arr[$po_id][$gmts_item_id][$color_id] += $dia_wies_fab_lay_qty[$po_id][$gmts_item_id][$color_id][$dia];
				 			foreach ($dia_data as $cut_no => $row) 
					        {	
			 					$lay_sub_tot_arr[$po_id][$gmts_item_id][$color_id] += $fab_lay_qnty_arr[$po_id][$gmts_item_id][$color_id][$dia][$cut_no];

								foreach($sizeId_arr as $key=>$value)
								{
									
									$color_size_sub_tot_arr[$po_id][$gmts_item_id][$color_id] += $bundle_data_array[$cut_no][$gmts_item_id][$color_id][$po_id][$dia][$key];
								}

					        	$rowSpan[$po_id][$gmts_item_id][$color_id]++;
					        	$rowSpanDia[$po_id][$gmts_item_id][$color_id][$dia]++;
					        	// $lay_sub_tot_arr[$po_id][$gmts_item_id][$color_id] += $row['mark_qty'];
					        }
					    }
					//}
		 		}
		 	}
		}

		// echo "<pre>";
		// print_r($fin_qnty_sub_tot_arr);
		// echo "</pre>";
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
				font-weight: bold;
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
		<?
		ob_start();
		
		$col_span=25+count($sizeId_arr);
		$table_width=2640+(count($sizeId_arr)*80);
		$div_width=$table_width+20;
		$i=1; 
		$total_layf_balance=0; 
		$total_markerf_qty=0; 
		$total_sizef_ratio=0; 
		$sizeDataArray=array();
		$plan_cut_qty=array();
	    //print_r($sizeDataArrayplan);die;            
		?>
	    <div style="width:<? echo $div_width; ?>px;">
	        <table class="rpt_table" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
	            <caption style="font-size:16px; font-weight:bold;">
	            <?
	            $com_name = str_replace( "'", "", $cbo_company_name );
	            echo $company_arr[$com_name]."<br/>"."Cutting Status Report V2 ";
	            ?>
	                <div style="color:red; text-align:left; font-size:14px;"></div>
	            </caption>
	        </table>
	       
	        <table class="rpt_table" width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
	            <thead>
	            	
	                <tr>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="40">Sl</th>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="100">Buyer Name</th>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="100">Int. Ref.</th>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="100">File No.</th>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="100">Style Reff</th>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="120">Style Description</th>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="160">Gmts Item</th>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="100">PO No</th>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="100">GMT Color </th>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="80">Booking Dia</th>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="80">Color Req. Qty (kg)</th>
	                    <th width="80">Finish Fab. Rec.</th>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="80">Recv. Balance</th>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="80">Cutting Date</th>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="80">System Cut No.</th>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="80">Cutting Floor</th>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="80">Manual Cut No.</th>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="80">Table No</th>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="80">Batch No</th>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="80">CAD Marker Length</th>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="80">CAD Marker Width</th>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="80">Fab. Lay Qnty.  (kg)</th>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="80">Cutting Fab. Inhand</th>
	                    <th width="80">Total Size Ratio</th>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="80">Plies</th>
	                    <th width="80">Total Bundle No</th>
	                    <?
	                  	foreach($sizeId_arr as $po_id=>$po_data)
	                    {
	                    	//foreach($po_data as $size_id=>$val)
	                    	//{
	                        	echo '<th style="word-wrap: break-word;word-break: break-all;" width="80"></th>';
	                       // }
	                    }
	                    ?>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="80">Total</th>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="80">Booking cons/dzn</th>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="80">Cut cons/dzn</th>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="80">Variance</th>
	                    <th style="word-wrap: break-word;word-break: break-all;" width="80">Fab. Need To Cut B.qty</th>	                   
	                </tr>
		        </thead>
		    </table>
		    <div style=" max-height:400px; width:<? echo $div_width; ?>px; overflow-y:scroll;" id="scroll_body">
		        <table class="rpt_table" id="table_body" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
		            <tbody>
		            	<?
		            	$s=1;	
		            	$i=1;
		            	$grnd_color_qnty 	= 0;	            	 		
		            	$grnd_fab_rcv_qnty 	= 0;	            	 		
		            	$grnd_rcv_balance 	= 0;	            	 		
		            	$grnd_fab_lay_qnty 	= 0;	            	 		
		            	$grnd_fab_inhand 	= 0;	            	 		
            	 		$grnd_size_ratio 	= 0;	            	 		
            	 		$grnd_plies 		= 0;	            	 		
            	 		$grnd_bundle_no 	= 0;	            	 		
            	 		$grnd_cut_conz_dzn 	= 0;	            	 
            	 		$grnd_fab_nd_to_cut	= 0;	            	 
		            	 foreach ($cut_color_arr as $po_id => $po_data) 
		            	 {
		            	 	foreach ($po_data as $gmts_item_id => $gmts_item_data) 
		            	 	{		            	 		
		            	 		foreach ($gmts_item_data as $color_id => $color_data) 
		            	 		{ 
		            	 			$r=0;
		            	 			?>
		            	 			<tr>
					                	<th style="word-wrap: break-word;word-break: break-all;" colspan="25" style=" border-color:transparent"></th>
					                    <td style="word-wrap: break-word;word-break: break-all;">Size</td> 
					                    <?
											foreach($sizeId_arr as $key=>$value)
											{
												echo '<td style="word-wrap: break-word;word-break: break-all;" align="center">'.$size_arr[$key].'</td>';
											} 
										?>
					                    <td style="word-wrap: break-word;word-break: break-all;" width="80" align="center"><b>Total</span></b></td>
					                </tr>
						            <tr>
						                <th style="word-wrap: break-word;word-break: break-all;" colspan="25" style=" border-color:transparent"></th>
						                <td style="word-wrap: break-word;word-break: break-all;">Order QTY</td>
						                <?
											$total_order_qty='';
											foreach($sizeId_arr as $key=>$value)
											{
												
												echo '<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">'.$size_order_data[$po_id][$gmts_item_id][$color_id][$key]['order_quantity'].'</td>';
												$total_order_qty+=$size_order_data[$po_id][$gmts_item_id][$color_id][$key]['order_quantity'];
											} 
										 ?>
						                <td style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $total_order_qty; ?></td>
					                </tr>
						            <tr>
						                <th style="word-wrap: break-word;word-break: break-all;" colspan="25" style=" border-color:transparent"></th>
						                <td style="word-wrap: break-word;word-break: break-all;">Plan Cut</td>
										<?
											$total_plan_qty='';
											foreach($sizeId_arr as $key=>$value)
											{
												echo '<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">'.$size_order_data[$po_id][$gmts_item_id][$color_id][$key]['plan_cut_qnty'].'</td>';
												$total_plan_qty+=$size_order_data[$po_id][$gmts_item_id][$color_id][$key]['plan_cut_qnty'];
											} 
										 ?>
						                 <td style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $total_plan_qty; ?></td>
					                </tr>
					                <tr>
						                <th style="word-wrap: break-word;word-break: break-all;" colspan="25" style=" border-color:transparent"></th>
						                <td style="word-wrap: break-word;word-break: break-all;">Lay Balance</td>
										<?
											$total_lay_bal_qty='';
											foreach($sizeId_arr as $key=>$value)
											{
												?>
												<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
												<? 
												$plan_cut = $size_order_data[$po_id][$gmts_item_id][$color_id][$key]['plan_cut_qnty'];
												$size_total_cut = $bundle_size_data_array[$gmts_item_id][$color_id][$po_id][$key];
												$balance = $plan_cut - $size_total_cut;
												// $total_lay_bal_qty+=$size_order_data[$po_id][$gmts_item_id][$color_id][$key]['plan_cut_qnty'];
												$total_lay_bal_qty+=$balance;
												echo $balance;
												?>
												</td>
												<?
												
											} 
										 ?>
						                 <td style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $total_lay_bal_qty; ?></td>
					                </tr>
		            	 			<?	
			            	 		$sub_color_qnty 	= 0;	            	 		
					            	$sub_fab_rcv_qnty 	= 0;	            	 		
					            	$sub_rcv_balance 	= 0;	            	 		
					            	$sub_fab_lay_qnty 	= 0;	            	 		
					            	$sub_fab_inhand 	= 0;	            	 		
			            	 		$sub_size_ratio 	= 0;	            	 		
			            	 		$sub_plies 			= 0;	            	 		
			            	 		$sub_bundle_no 		= 0;	            	 		
			            	 		$sub_cut_conz_dzn 	= 0;
			            	 		$sub_fab_nd_to_cut 	= 0;
			            	 		$sub_row_total 		= 0;

			            	 		$tot_sub_size_qty=$po_item_size_data_array[$gmts_item_id][$color_id][$po_id]['sub_size_qty']; 
		            	 			// foreach ($color_data as $body_part_id => $body_part_data) 
				            	 	// {
				            	 		foreach ($color_data as $dia => $dia_data) 
				            	 		{
					            	 		$d=0;
					            	 		foreach ($dia_data as $cut_no => $row) 
					            	 		{	
					            	 			$bgcolor = ($i%2==0) ? "#E9F3FF" : "#FFFFFF";
								            	?>
								            	<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
								            		<? if($r==0){?>
								            		<td style="word-wrap: break-word;word-break: break-all;" width="40" rowspan="<? echo $rowSpan[$po_id][$gmts_item_id][$color_id];?>" valign="middle"><? echo $s; ?></td>
								            		<td style="word-wrap: break-word;word-break: break-all;" width="100" rowspan="<? echo $rowSpan[$po_id][$gmts_item_id][$color_id];?>" valign="middle" ><? echo $buyer_arr[$row['buyer_name']]; ?></td>
								            		<td style="word-wrap: break-word;word-break: break-all;" width="100" rowspan="<? echo $rowSpan[$po_id][$gmts_item_id][$color_id];?>" valign="middle" ><? echo $row['grouping']; ?></td>
								            		<td style="word-wrap: break-word;word-break: break-all;" width="100" rowspan="<? echo $rowSpan[$po_id][$gmts_item_id][$color_id];?>" valign="middle" ><? echo $row['file_no']; ?></td>
								            		<td style="word-wrap: break-word;word-break: break-all;" width="100" rowspan="<? echo $rowSpan[$po_id][$gmts_item_id][$color_id];?>" valign="middle" ><? echo $row['style_ref_no']; ?></td>
								            		<td style="word-wrap: break-word;word-break: break-all;" width="120" rowspan="<? echo $rowSpan[$po_id][$gmts_item_id][$color_id];?>" valign="middle" ><? echo $row['style_description']; ?></td>
								            		<td style="word-wrap: break-word;word-break: break-all;" width="160" rowspan="<? echo $rowSpan[$po_id][$gmts_item_id][$color_id];?>" valign="middle" title="<? echo 'Item ID = '.$gmts_item_id;?>" ><? echo $garments_item[$gmts_item_id]; ?></td>
								            		<td style="word-wrap: break-word;word-break: break-all;" width="100" rowspan="<? echo $rowSpan[$po_id][$gmts_item_id][$color_id];?>" valign="middle" ><? echo $row['po_number']; ?></td>
								            		<td style="word-wrap: break-word;word-break: break-all;" width="100" rowspan="<? echo $rowSpan[$po_id][$gmts_item_id][$color_id];?>" title="<? echo 'Color ID = '.$color_id;?>" valign="middle" ><? echo $color_library[$color_id]; ?></td>
								            		<?} if($d==0){?>
								            		<td style="word-wrap: break-word;word-break: break-all;" width="80" rowspan="<? echo $rowSpanDia[$po_id][$gmts_item_id][$color_id][$dia];?>" valign="middle" align="center" ><? echo $dia; ?></td>

								            			<?
								            				$requirement 	= 	$color_req_qty_arr[$po_id][$gmts_item_id][$color_id][$dia];
								            				// $requirement 	= 	$color_qty_bundle[$gmts_item_id][$color_id][$po_id][$dia];
									            			$ratio 			=  	$set_item_ratio_array[$po_id][$gmts_item_id][$color_id][$dia];
									            			$plan_cut_qnty 	=  	$color_order_data[$po_id][$gmts_item_id][$color_id]['plan_cut_qnty'];
									            			$costing_per	=	$row['costing_per'];
									            			if($costing_per		==1) {$pcs_value=1*12*$ratio;}
															else if($costing_per==2) {$pcs_value=1*1*$ratio;}
															else if($costing_per==3) {$pcs_value=2*12*$ratio;}
															else if($costing_per==4) {$pcs_value=3*12*$ratio;}
															else if($costing_per==5) {$pcs_value=4*12*$ratio;}

									            			// $pcs_value		=	(1*12*$ratio);

									            			// $color_qnty 	= 	($requirement/$pcs_value)*$plan_cut_qnty;
									            			$color_qnty 	= 	$requirement;
									            			// $color_qnty 	=	number_format($color_qnty,2);
									            			$sub_color_qnty	+=	$color_qnty;
									            			$grnd_color_qnty+=	$color_qnty;
								            			?>
								            		<td style="word-wrap: break-word;word-break: break-all;" width="80" rowspan="<? echo $rowSpanDia[$po_id][$gmts_item_id][$color_id][$dia];?>" valign="middle" align="right" title='<? echo "req=$requirement,ratio=$ratio,plan cut=$plan_cut_qnty";?>' >
								            			<? 									            			
									            			echo number_format($color_qnty,2);
								            			?>								            				
								            			</td>
								            		<td style="word-wrap: break-word;word-break: break-all;" width="80" rowspan="<? echo $rowSpanDia[$po_id][$gmts_item_id][$color_id][$dia];?>" valign="middle" align="right" title="Finish qnty">
								            			<?
								            			 // $finQnty = $fin_qnty_arr[$po_id][$color_id][$dia];
								            			 $finQnty = $fin_fab_issue_qnty_arr[$po_id][$color_id][$dia];//[$gmts_item_id]
								            			 $sub_fab_rcv_qnty+=$finQnty;
								            			 $grnd_fab_rcv_qnty += $finQnty;
								            			 echo number_format($finQnty,2);
								            			 ?>
								            				
								            			</td>
								            		<td style="word-wrap: break-word;word-break: break-all;" width="80" rowspan="<? echo $rowSpanDia[$po_id][$gmts_item_id][$color_id][$dia];?>" valign="middle" align="right">
								            			<? 
								            			$rcv_balance = $color_qnty - $finQnty;
								            			echo number_format($rcv_balance,2); 
								            			$sub_rcv_balance += $rcv_balance;
								            			$grnd_rcv_balance+= $rcv_balance;
								            			?>
								            				
								            			</td>
								            		<?}?>
								            		<td style="word-wrap: break-word;word-break: break-all;" width="80" align="center"><? echo change_date_format($row['cuting_date']); ?></td>
								            		<td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo $cut_no; ?></td>
								            		<td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo $floor_arr[$row['floor_id']]; ?></td>
								            		<td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo $row['order_cut_no']; ?></td>
								            		<td style="word-wrap: break-word;word-break: break-all;" width="80" align="center"><? echo $row['table_no']; ?></td>
								            		<td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo $row['batch_id']; //$batch_array[$cut_no][$gmts_item_id][$color_id][$po_id];  ?></td>
								            		<td style="word-wrap: break-word;word-break: break-all;" width="80" align="center"><? echo $row['marker_length']; ?></td>
								            		<td style="word-wrap: break-word;word-break: break-all;" width="80" align="center"><? echo $row['marker_width']; ?></td>
								            		<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
								            			<? 
								            			$layQnty = $fab_lay_qnty_arr[$po_id][$gmts_item_id][$color_id][$dia][$cut_no];
								            			echo number_format($layQnty,2);
								            			$sub_fab_lay_qnty+=$layQnty;
								            			$grnd_fab_lay_qnty+=$layQnty;
								            			?>
								            				
								            			</td>
								            		<? if($d==0){								            			 
								            			// $inhand = $dia_wies_fab_lay_qty[$po_id][$gmts_item_id][$color_id][$dia];
								            			$dia_wise_tot_lay = $fab_lay_qnty_color_arr[$po_id][$gmts_item_id][$color_id][$dia];
								            			$inhandQnty = ($finQnty - $dia_wise_tot_lay);
								            			?>
								            		<td style="word-wrap: break-word;word-break: break-all;" width="80" rowspan="<? echo $rowSpanDia[$po_id][$gmts_item_id][$color_id][$dia];?>" valign="middle" align="right" title='<? echo "Fin Qnty=$finQnty,Dia wise tot lay=$dia_wise_tot_lay"; ?>'>
								            			<?
								            			echo number_format($inhandQnty,2);
								            			$sub_fab_inhand+=$inhandQnty;
								            			$grnd_fab_inhand+=$inhandQnty;
								            			?>
								            				
								            			</td>
								            		<?}?>
								            		<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? $ratio = $size_ratio_array[$cut_no][$gmts_item_id][$color_id][$dia][$po_id]; echo number_format($ratio,2); ?></td>
								            		<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($row['plies'],2); ?></td>
								            		<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
								            			<? 
								            			$bundleNum = $bundle_num[$po_id][$gmts_item_id][$color_id][$cut_no]; 
								            			echo number_format($bundleNum,2);
								            			$sub_bundle_no+=$bundleNum;
								            			$grnd_bundle_no+=$bundleNum;
								            			?>
								            				
								            			</td>
								            		<?
													$row_total=0;
													foreach($sizeId_arr as $key=>$value)
													{
														?>
						                                <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
														<? 
														// $bundle_data_array[$row[csf('cutting_no')]][$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('po_id')]][$row[csf('size_id')]];
															echo number_format($bundle_data_array[$cut_no][$gmts_item_id][$color_id][$po_id][$dia][$key],2) ;
															$row_total+= $bundle_data_array[$cut_no][$gmts_item_id][$color_id][$po_id][$dia][$key];
															$size_total[$key]+=$bundle_data_array[$cut_no][$gmts_item_id][$color_id][$po_id][$dia][$key];
															$totsize_total[$key]+=$bundle_data_array[$cut_no][$gmts_item_id][$color_id][$po_id][$dia][$key];
															$totalsize_total[$key]+=$bundle_data_array[$cut_no][$gmts_item_id][$color_id][$po_id][$dia][$key];
														?> 
						                                </td>
						                                <?
													}
													?>
													<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($row_total,2); ?></td>
													<?
													if($d==0){
													?>
													<td style="word-wrap: break-word;word-break: break-all;" width="80" rowspan="<? echo $rowSpanDia[$po_id][$gmts_item_id][$color_id][$dia];?>" valign="middle" align="right" >
														<?
														$booking_con = $fin_con_arr[$po_id][$gmts_item_id][$color_id][$dia];
														$res = ($total_order_qty*12) / $booking_con;
														echo number_format($booking_con,2);

														 ?>
															
														</td>
													<?}?>
													<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right" title='<? echo "Lay qty=$layQnty,Size Total=$row_total";?>'>
														<?
														 $cutConz = ($layQnty / $row_total)*12;
														 echo number_format($cutConz,2);
														// $sub_cut_conz_dzn+=$cutConz;
														// $grnd_cut_conz_dzn+=$cutConz;
														?>
															
														</td>
													<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right" >
														<? 
														$variance = $booking_con - $cutConz; echo number_format($variance,2);
														?>
															
														</td>
													<? if($r==0){														 
														$sub_col_size_tot 	= 0;
														$sub_lay_tot 		= 0;
														$sub_col_size_tot 	=  $color_size_sub_tot_arr[$po_id][$gmts_item_id][$color_id];
														$sub_lay_tot		=  $lay_sub_tot_arr[$po_id][$gmts_item_id][$color_id];
														?>
													<td style="word-wrap: break-word;word-break: break-all;" width="80" rowspan="<? echo $rowSpan[$po_id][$gmts_item_id][$color_id];?>" valign="middle" align="right" title='<? echo "Tot Cut=$total_plan_qty,Sub size total=$sub_col_size_tot,Sub lay total=$sub_lay_tot,Fab inhand=$sub_fab_inhand" ?>'>
														<?
														$fa_need_to_cut = ($total_plan_qty - $sub_col_size_tot)*($sub_lay_tot / $sub_col_size_tot) - ($fin_qnty_sub_tot_arr[$po_id][$color_id] - $lay_sub_tot_arr[$po_id][$gmts_item_id][$color_id]);//$sub_fab_inhand;
														echo number_format($fa_need_to_cut,2);
														$sub_fab_nd_to_cut+=$fa_need_to_cut;
														$grnd_fab_nd_to_cut+=$fa_need_to_cut;
														?>
														</td>
													<?}?>
								            	</tr>
								            	<?
								            	$r++;
								            	$d++;
								            	$i++;
						            	 		// sub total
						            	 		// $sub_color_qnty		+=$color_qty_bundle[$gmts_item_id][$color_id][$po_id][$dia];
						            	 		// $sub_fab_rcv_qnty	+=0;
						            	 		// $sub_rcv_balance	+=0;
								            	// $sub_fab_lay_qnty 	+= 0;	            	 		
								            	// $sub_fab_inhand 	+= 0;	            	 		
						            	 		$sub_size_ratio 	+= $ratio;	            	 		
						            	 		$sub_plies 			+= $row['plies'];	            	 		
						            	 		// $sub_bundle_no 		+= 0;	            	 		
						            	 		// $sub_cut_conz_dzn 	+= 0;
								            	// grand total
								            	// $grnd_color_qnty 	+= $color_qty_bundle[$gmts_item_id][$color_id][$po_id][$dia];     	 		
								            	// $grnd_fab_rcv_qnty 	+= 0;                    	 		
								            	// $grnd_rcv_balance 	+= 0;        	            	 		
								            	// $grnd_fab_lay_qnty 	+= 0;	            	 		
								            	// $grnd_fab_inhand 	+= 0;	            	 		
						            	 		$grnd_size_ratio 	+= $ratio;	            	 		
						            	 		$grnd_plies 		+= $row['plies'];	            	 		
						            	 		// $grnd_bundle_no 	+= 0;	            	 		
						            	 		// $grnd_cut_conz_dzn 	+= 0;
								            }
								        }
							        //}
						            ?>
						            <tr class="gd-color">
						            	<td style="word-wrap: break-word;word-break: break-all;" width="40"></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="120"></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="160"></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">Sub Total</td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sub_color_qnty,2); ?></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sub_fab_rcv_qnty,2); ?></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sub_rcv_balance,2); ?></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sub_fab_lay_qnty,2); ?></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sub_fab_inhand,0); ?></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sub_size_ratio,2); ?></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sub_plies,2); ?></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sub_bundle_no,2); ?></td>
						            	<?
											$size_row_total=$row_total=$sub_total_size_qty=0;$totsize_total=array();
											foreach($sizeId_arr as $key=>$value)
											{
												?>
				                                <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
												<? 
													$size_row_total=$size_total[$key];
													echo number_format($size_total[$key],2);
													$size_total[$key]=0;
													$sub_total_size_qty+=$size_row_total;
													$gt_total_size_qty[$key]+=$size_row_total;
												?> 
				                                </td>
				                                <?
											}
						            		$s++;
						            		//$po_item_sub_total_size_qty[$po_id][$gmts_item_id][$color_id]=$sub_total_size_qty;
											?>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sub_total_size_qty,2);$sub_total_size_qty=0;?></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right" title="(Sub tot lay*sub color size total)*12">AVG : <? 					            															 
										$sub_col_size_tot 	= 0;
										$sub_lay_tot 		= 0;
										$sub_col_size_tot 	=  $color_size_sub_tot_arr[$po_id][$gmts_item_id][$color_id];
										$sub_lay_tot		=  $lay_sub_tot_arr[$po_id][$gmts_item_id][$color_id];
										$sub_cut_conz_dzn   = ($sub_lay_tot / $sub_col_size_tot)*12;
										$grnd_cut_conz_dzn+=$sub_cut_conz_dzn;
						            	echo number_format($sub_cut_conz_dzn,2); 
						            	?></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
						            	<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sub_fab_nd_to_cut,2); ?></td>
						            </tr>
						            <?
								}
		            	 	}
		            	 }
		            	?>
		            </tbody>
		        </table>
		    </div>
		    <table  border="1" class=""  width="<? echo $table_width; ?>" rules="all" id="" cellpadding="0" cellspacing="0">
				<tr class="gd-color2">
					<td style="word-wrap: break-word;word-break: break-all;" width="40"></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="120"></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="160"></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="80">Grand Total</td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($grnd_color_qnty,2); ?></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($grnd_fab_rcv_qnty,2); ?></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($grnd_rcv_balance,2); ?></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($grnd_fab_lay_qnty,2); ?></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($grnd_fab_inhand,2); ?></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($grnd_size_ratio,2); ?></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($grnd_plies,2); ?></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($grnd_bundle_no,2); ?></td>
	            	<?
					$gt_row_total=0;
					foreach($sizeId_arr as $key=>$value)
					{
						?>
						<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
							<? echo number_format($gt_total_size_qty[$key],2); ?>
						</td>
						<?
						$gt_row_total+=$gt_total_size_qty[$key];
					}
					?>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($gt_row_total,2); ?></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
	            		<? 
	            		$grnd_cut_conz_dzn = ($grnd_fab_lay_qnty / $gt_row_total)*12;
	            		echo number_format($grnd_cut_conz_dzn,2); 
	            		?>
	            			
	            		</td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
	            	<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($grnd_fab_nd_to_cut,2); ?></td>
				</tr>		    	
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