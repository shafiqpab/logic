<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.commisions.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.fabrics.php');
require_once('../../../../includes/class4/class.commercials.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.washes.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$item_library=return_library_array( "select id,item_name from  lib_item_group", "id", "item_name"  );
$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id", "supplier_name"  );
$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );


if($db_type==2) $select_date=" to_char(a.insert_date,'YYYY')";
else if ($db_type==0) $select_date=" year(a.insert_date)";

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond  group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	exit();
}


if ($action=="job_popup")
{
  	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
        function js_set_value(str)
        {
            $("#hide_job_no").val(str);
            parent.emailwindow.hide(); 
        }
    </script>
<?
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
			$year_cond=" and YEAR(insert_date)=$cbo_year";
			$year_field="YEAR(insert_date)";
		}
		else
		{
			$year_cond=" and to_char(insert_date,'YYYY')=$cbo_year";	
			$year_field="to_char(insert_date,'YYYY')";
		}
	}
	else $year_cond="";

	$arr=array (2=>$company_library,3=>$buyer_arr);
	$sql= "select a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,$year_field as year from wo_po_details_master a where a.company_name=$company_id $buyer_cond $year_cond order by a.id";
	//echo $sql;
	echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No", "70,70,120,100,100","570","320",0, $sql , "js_set_value", "year,job_no", "", 1, "0,0,company_name,buyer_name,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no", "","setFilterGrid('list_view',-1)",'0,0,0,0,0');
	echo "<input type='hidden' id='hide_job_no' />";
	
	exit();
}


if($action=="style_ref_search")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $buyer_name;
	?>
    <script>
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
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
				selected_no.push( str );				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_no.splice( i, 1 ); 
			}
			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_no[i] + ','; 
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 ); 
			num 	= num.substr( 0, num.length - 1 );
			//alert(num);
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name ); 
			$('#txt_selected_no').val( num );
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="100">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
							 if($buyer_name!=0) $buyer_name_cond="and  buy.id in($buyer_name) ";else $buyer_name_cond="";
														 
								echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_name_cond $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $txt_style_ref_no; ?>'+'**'+'<? echo $txt_style_ref_id; ?>'+'**'+'<? echo $txt_style_ref; ?>', 'create_list_style_search', 'search_div', 'style_wise_cm_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}
if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=11 and report_id=201 and is_deleted=0 and status_active=1");
    $printButton=explode(',',$print_report_format);
    foreach($printButton as $id){
        if($id==108)$buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:45px; margin-top: 2px; margin-right: 2px;" value="Show" onClick="fn_report_generated(1)" />';
        if($id==448)$buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:60px; margin-top: 2px; margin-right: 2px;" value="CM Details" onClick="fn_report_generated(2)" />';
        if($id==447)$buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:80px; margin-top: 2px; margin-right: 2px;" value="OBS Report" onClick="fn_report_generated(4)" />';
        if($id==449)$buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:60px; margin-top: 2px; margin-right: 2px;" value="Post Cost" onClick="fn_report_generated(3)" />';
        if($id==450)$buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:60px; margin-top: 2px; margin-right: 2px;" value="Complete" onClick="fn_report_generated(5)" />';
		if($id==195)$buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:45px; margin-top: 2px; margin-right: 2px;" value="Show2" onClick="fn_report_generated(7)" />';
    }
    echo "document.getElementById('load_print_button').innerHTML = '".$buttonHtml."';\n";
    exit();
}
//style search------------------------------//
if($action=="create_list_style_search")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	list($company,$buyer,$search_type,$search_value,$cbo_year,$txt_style_ref_no,$txt_style_ref_id,$txt_style_ref)=explode('**',$data);

	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$cbo_year=str_replace("'","",$cbo_year);
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
	if($db_type==0) if($cbo_year!=0) $job_cond=" and year(a.insert_date)='$cbo_year'";
	else if($cbo_year!=0) $job_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
	
	if($search_type==1 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value')";	
		
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";	
	}

	//echo $search_type;
	
	if($buyer!=0) $buyer_cond="and a.buyer_name=$buyer"; else $buyer_cond="";
	 $sql = "select a.id,b.buyer_name,a.style_ref_no,a.job_no,a.job_no_prefix_num,$select_date as job_year from wo_po_details_master a,lib_buyer b where a.buyer_name=b.id and a.company_name=$company $buyer_cond $year_cond $job_cond $search_con and a.is_deleted=0 order by a.job_no_prefix_num"; 
	//echo $sql; die;
	echo create_list_view("list_view", "Style Ref No,Job No,Year,Buyer","130,90,50,110","450","200",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,job_year,buyer_name", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_style_ref_no;?>';
	var style_id='<? echo $txt_style_ref_id;?>';
	var style_des='<? echo $txt_style_ref;?>';
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
if($action=="order_search")
{

	echo load_html_head_contents("Order No Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
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
			var str_or = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
			
			toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );
			
			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
				selected_no.push( str_or );				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_no.splice( i, 1 ); 
			}
			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_no[i] + ','; 
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 ); 
			num 	= num.substr( 0, num.length - 1 );
			//alert(num);
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name ); 
			$('#txt_selected_no').val( num );
		}
    </script>

</head>

<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up">Please Enter Order No</th>
                    <th>Shipment Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:80px;"></th> 
                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td" width="130">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;" readonly>
                            To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;" readonly>
                        </td>	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year_selection').value+'**'+'<? echo $txt_style_ref; ?>'+'**'+'<? echo $style_ref_id; ?>', 'order_search_list_view', 'search_div', 'cost_breakup_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
}

if($action=="order_search_list_view")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	list($company,$buyer,$search_type,$search_value,$start_date,$end_date,$cbo_year_selection,$txt_style_ref,$style_ref_id)=explode('**',$data);
	
	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$style_ref_id=str_replace("'","",$style_ref_id);
	$cbo_year=str_replace("'","",$cbo_year_selection);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0)
		{
			//$year_cond=" and YEAR(b.insert_date)=$cbo_year";
		}
		else
		{
			//$year_cond=" and to_char(b.insert_date,'YYYY')=$cbo_year";	
		}
	}
	
	if($search_type==1 && $search_value!=''){
		$search_con=" and a.po_number like('%$search_value')";	
	}
	elseif($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value')";		
	}
	elseif($search_type==3 && $search_value!=''){
		$search_con=" and a.job_no_mst like('%$search_value')";		
	}
	
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.pub_shipment_date between '".change_date_format(trim($start_date),"yyyy-mm-dd")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd")."'";
		}
		else
		{
			$date_cond="and a.pub_shipment_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	
	//echo $txt_style_ref.'='.$style_ref_id;
	$job_style_cond="";
	if(trim(str_replace("'","",$txt_style_ref))!="")
	{
		if(str_replace("'","",$style_ref_id)!="")
		{
			$job_style_cond=" and b.id in(".str_replace("'","",$style_ref_id).") ";
		}
		else
		{
			$job_style_cond=" and b.style_ref_no like '%".trim(str_replace("'","",$txt_style_ref))."%'";
		}
	}
	
	if($buyer!=0) $buyer_cond="and b.buyer_name=$buyer"; else $buyer_cond="";
	if($txt_style_ref!="")
	{
		//if($db_type==0) $style_cond="and b.job_no_prefix_num in($txt_style_ref) and year(b.insert_date)= '$cbo_year' "; 
		//else $style_cond="and b.job_no_prefix_num in($txt_style_ref) and to_char(b.insert_date,'YYYY')= '$cbo_year' ";
	}
	//else $style_cond="";
	
	//echo $style_cond."jahid";die;
	$sql = "select a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$select_date as job_year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and b.company_name=$company $buyer_cond $year_cond $job_style_cond $search_con $date_cond and a.status_active=1"; 
	//echo $sql;
	echo create_list_view("list_view", "Order NO,Job No,Year,Style Ref No","150,80,70,150","500","150",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_no_prefix_num,job_year,style_ref_no", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_order_id_no;?>';
	var style_id='<? echo $txt_order_id;?>';
	var style_des='<? echo $txt_order;?>';
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


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$reporttype=str_replace("'","",$reporttype);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_style_owner=str_replace("'","",$cbo_style_owner);
	$style_ref_id=str_replace("'","",$txt_style_ref_id);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	if($cbo_style_owner==0) $style_owner_cond=""; else $style_owner_cond=" and a.style_owner='$cbo_style_owner' ";
	if($cbo_company_name==0) $company_name_cond=""; else $company_name_cond=" and a.company_name='$cbo_company_name' ";
	$job_style_cond="";
	if(trim(str_replace("'","",$txt_style_ref))!="")
	{
		if(str_replace("'","",$style_ref_id)!="")
		{
			$job_style_cond=" and a.id in(".str_replace("'","",$style_ref_id).")";
		}
		else
		{
			$job_style_cond=" and a.style_ref_no = '".trim(str_replace("'","",$txt_style_ref))."'";
		}
	}

	if(str_replace("'","",$cbo_buyer_name)==0)
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
		$buyer_id_cond=" and a.buyer_name in($cbo_buyer_name)";
	}
	$date_cond="";
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
			$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
		}
	
	
	$style_row=count($txt_style_ref);
	ob_start();
	
	/*$pre_sql="select a.costing_per,a.job_no,a.exchange_rate from wo_pre_cost_mst a where a.status_active=1 and a.is_deleted=0 ";
	$sql_pre_result=sql_select($pre_sql);
	foreach($sql_pre_result as $row)
	{
		$costing_per_arr[$row[csf("job_no")]]['cost_per']=$row[csf("costing_per")];
		$costing_per_arr[$row[csf("job_no")]]['ex_rate']=$row[csf("exchange_rate")];
	}*/
	
	$sql="select a.id,a.job_no_prefix_num as job_prefix,a.ship_mode,a.avg_unit_price, a.job_no, a.company_name, a.client_id,a.buyer_name, a.team_leader, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id as po_id, b.po_number,b.is_confirmed, b.pub_shipment_date,b.shipment_date,b.up_charge ,b.po_quantity,b.plan_cut,b.matrix_type, b.unit_price, b.po_total_price,b.status_active,c.item_number_id,c.plan_cut_qnty,c.order_quantity,c.order_total,c.color_number_id from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and c.po_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active=1  and c.is_deleted=0   $company_name_cond $job_style_cond $buyer_id_cond $date_cond  order  by b.pub_shipment_date";
	
	$sql_po_result=sql_select($sql);
	$all_po_id="";$all_job="";$all_full_job="";$all_style="";$all_style_desc=""; $all_buyer=""; $all_buyer_client=""; $all_order_uom=""; 
	$order_qty_pcs=0;$total_fob_value=0;$total_order_qty=0;$total_unit_price=0;$total_job_unit_price=0;
	//echo $buyer_name;die;
	$po_numer_arr=$po_data_arr=array();$tot_count=0;$total_order_upcharge=0;$item_color_size_array=array();$job_id_arr=array();
	foreach($sql_po_result as $row)
	{
		if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
		if($all_job=="") $all_job=$row[csf("id")]; else $all_job.=",".$row[csf("id")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_no']=$row[csf("po_number")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['style_ref_no']=$row[csf("style_ref_no")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_qty_pcs']+=$row[csf("order_quantity")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['plan_cut']+=$row[csf("plan_cut_qnty")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_value']+=$row[csf("order_total")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['avg_unit_price']=$row[csf("avg_unit_price")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_id'].=$row[csf("po_id")].",";
	
		$job_buyer_name_arr[$row[csf("job_no")]]['buyer_name']=$row[csf("buyer_name")];
		$item_color_size_array[$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]+=$row[csf("plan_cut_qnty")];
		
		$job_id_arr[$row[csf("id")]]=$row[csf("id")];
  } 
 // echo $all_po_id.'DDDDDDDD';
  if($all_po_id=="") {echo "<div style='color:red; font-size:30px;' align='center'>No PO No Found </div>";die;}
	//echo $tot_count;
	//echo $all_po_id.'dsd';
		$all_job_no=array_unique(explode(",",$all_full_job));
		$all_jobs="";
		foreach($all_job_no as $jno)
		{
				if($all_jobs=="") $all_jobs=$jno; else $all_jobs.=",".$jno;
		}
		$all_po=array_unique(explode(",",$all_po_id));
		$all_poIDs=implode(",",array_unique(explode(",",$all_po_id)));
		$po_arr_cond=array_chunk($all_po,1000, true);
		$po_cond_for_in="";$po_cond_for_in2="";$po_cond_for_in3="";
		$pi=0;
		foreach($po_arr_cond as $key=>$value)
		{
		   if($pi==0)
		   {
			$po_cond_for_in=" and ( c.po_break_down_id  in(".implode(",",$value).")"; 
			$po_cond_for_in2=" and ( d.po_break_down_id  in(".implode(",",$value).")"; 
			
		   }
		   else //po_break_down_id
		   {
			$po_cond_for_in.=" or c.po_break_down_id  in(".implode(",",$value).")";
			$po_cond_for_in2.=" or d.po_break_down_id  in(".implode(",",$value).")";
			
		   }
		   $pi++;
		}	
		$po_cond_for_in.=" )";
		$po_cond_for_in2.=" )";
		
		$condition= new condition();
		$condition->company_name("=$cbo_company_name");
		if(str_replace("'","",$cbo_buyer_name)>0){
			  $condition->buyer_name("in($cbo_buyer_name)");
		 }
		 if($all_po_id!='' || $all_po_id!=0)
		 {
			$condition->po_id_in("$all_poIDs"); 
		 }
		 if(str_replace("'","",$txt_style_ref)!='')
		 {
			//echo "in($txt_order_id)".'dd';die;
			//$condition->job_no("in($all_jobs)");
		 }
		$condition->init();
		$costPerArr=$condition->getCostingPerArr();
		$emblishment= new emblishment($condition); 
	
		$wash= new wash($condition);
		$commercial= new commercial($condition);
		
	//	$trims_ReqQty_arr=$trims->getQtyArray_by_precostdtlsid();//getQtyArray_by_jobAndPrecostdtlsid();
		//print_r($trims_ReqQty_arr);
		$trims= new trims($condition);
		//echo $trims->getQuery(); die;
		//echo $emblishment->getQuery(); die;
		$trims_costing_arr=$trims->getAmountArray_by_order();//getAmountArray_by_jobAndPrecostdtlsid();
		
		$emblishment_costing_arr=$emblishment->getAmountArray_by_order();
		$wash_amount_arr=$wash->getAmountArray_by_jobEmbnameAndEmbtype();
		$commercial_costing_arr=$commercial->getAmountArray_by_order();
		$commission= new commision($condition);
		$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
		$yarn= new yarn($condition);
		//echo $yarn->getQuery(); die;
		$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
		$other= new other($condition);
		$other_costing_arr=$other->getAmountArray_by_order();
		
			
		$jobIds=chop($all_job,',');
		$prod_cond_for_in="";
		$prod_ids=count(array_unique(explode(",",$all_job)));
		if($db_type==2 && $prod_ids>1000)
		{
		$prod_cond_for_in=" and (";
		$prodIdsArr=array_chunk(explode(",",$jobIds),999);
		foreach($prodIdsArr as $ids)
		{
		$ids=implode(",",$ids);
		$prod_cond_for_in.=" a.job_id in($ids) or"; 
		}
		$prod_cond_for_in=chop($prod_cond_for_in,'or ');
		$prod_cond_for_in.=")";
		}
		else
		{
		$jobIds=implode(",",array_unique(explode(",",$jobIds)));
		$prod_cond_for_in=" and a.job_id in($jobIds)";
		}
	 $data_sql="select a.id, a.fabric_cost_dtls_id,b.costing_per, a.job_no,a.job_id, a.count_id, a.copm_one_id, a.percent_one, a.copm_two_id, a.percent_two, a.type_id, a.cons_ratio, a.cons_qnty, a.rate, a.amount, a.avg_cons_qnty, a.supplier_id, a.color, a.consdznlbs, a.rate_dzn, 
	b.item_number_id, b.body_part_id, b.fabric_description, b.color_type_id, b.uom, c.color_number_id, c.stripe_color, c.measurement 
	from wo_pre_cost_fab_yarn_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_pre_stripe_color c where a.fabric_cost_dtls_id=b.id and b.id=c.pre_cost_fabric_cost_dtls_id and a.color=c.stripe_color  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $prod_cond_for_in";
	//echo $data_sql; die;
	$result_data=sql_select($data_sql);
	//$lib_yarn_count=return_library_array( "select yarn_count, id from lib_yarn_count", "id", "yarn_count");
        $i=1; $totCons=0; $totReq=0; $totAmt=0;$amount_req=0;
        foreach ($result_data as $yarn)
        {
			//echo $job_id=$yarn[csf('job_id')];
			$costPerQty=$costPerArr[$yarn[csf("job_no")]];
			//echo $costPerQty.'dd';
			$job_buyer=$job_buyer_name_arr[$yarn[csf("job_no")]]['buyer_name'];
			$poQty=0; $yarn_req_kg=0; $yarn_req_lbs=0;
			$poQty=$item_color_size_array[$job_buyer][$yarn[csf("job_no")]][$yarn[csf('item_number_id')]][$yarn[csf('color_number_id')]];
            $yarn_req_kg=($yarn[csf('measurement')]/$costPerQty)*$poQty;
			$yarn_req_lbs=$yarn_req_kg*2.20462;
			
            $amount_req=$yarn_req_lbs*$yarn[csf('rate')];
			//echo $amount_req.'=='.$poQty.'<br>';
			if($amount_req>0)
			{
			$yarn_req_arr[$job_buyer][$yarn[csf('job_no')]]+= $amount_req;
			}
		}
		$financial_para=array();
		$sql_std_para=sql_select("select interest_expense,income_tax,cost_per_minute from lib_standard_cm_entry where company_id=$cbo_company_name and status_active=1 and	is_deleted=0 order by id");
		foreach($sql_std_para as $sql_std_row){
		$financial_para[interest_expense]=$sql_std_row[csf('interest_expense')];
		$financial_para[income_tax]=$sql_std_row[csf('income_tax')];
		$financial_para[cost_per_minute]=$sql_std_row[csf('cost_per_minute')];
		}
		unset($sql_std_para);
		
		$sql_pre=sql_select("select job_no,margin_dzn from wo_pre_cost_dtls where  status_active=1 and	is_deleted=0  ".where_con_using_array($job_id_arr,0,'job_id')."  order by job_no");
		foreach($sql_pre as $row){
		$pre_cost_mergin_arr[$row[csf('job_no')]]=$row[csf('margin_dzn')];
		}
		//print_r($pre_cost_mergin_arr);
		unset($sql_pre);
		
		
		
				$style1="#E9F3FF"; 
				$style="#FFFFFF";
	?>
        <div style="width:100%; padding-left:0px;">
        <br><br><br> <br> 
           <?
          	$style1="#E9F3FF"; 
			$style="#FFFFFF";
			//echo count($buyer_job_arr);
			//print_r($buyer_job_arr);
			$imge_arr=return_library_array( "select master_tble_id,image_location from  common_photo_library where file_type=1 and form_name='knit_order_entry' ",'master_tble_id','image_location');
		   ?>
           <style>
	    table tr td { height:30px; font-size:14px;
		  
	   }
	   /*#outerdiv {
            position: absolute;
            top: 0;
            left: 0;
            right: 5em;
        }
        #innerdiv {
            width: 100%;
            overflow-x:scroll;
            margin-left: 5em;
            overflow-y:visible;
            padding-bottom:1px;
        }
        .headcol {
            position:absolute;
            width:5em;
            left:0;
            top:auto;
            border-right: 0px none black;
            border-top-width:3px;
            /*only relevant for first row
            margin-top:-3px;
            /*compensate for top border
        }
        .headcol:before {
            content:'Row ';
        }*/
        table#height_td tr th:first-child, table#height_td td:first-child{
		  position: sticky;
		  width: 100px;
		  left: 0;
		  z-index: 10;
		}
		table#height_td tr th:first-child{
		  z-index: 11;
		}
		table#height_td tr th{
		  position: sticky;
		  top: 0;
		  z-index: 9;
		}
		</style> 
		
         <div style="margin-left:10px">
          <table width="100%" style="margin-left:10px">
             <tr class="form_caption" style="font-size:24px;">
               <td align="center" width="100%" colspan="10" class="form_caption"><? echo $company_library[$cbo_company_name].'<br>';if($cbo_style_owner!=0) echo 'Style Owner: '.$company_library[$cbo_style_owner]; ?></td>
                    
                </tr>
                <tr>
                  <td colspan="10" style="font-size:24px" align="center"><strong>Style Wise CM Report</strong></td>
                </tr>
            </table>
            <table cellspacing="0" width="100%"  border="0">
            <tr valign="top">
       		<?
            $tot_po_qty=0;$tot_po_qty_arr=array();$tot_po_value_arr=array();$total_cost_arr=array();$other_cost_arr=array();$tot_plan_qty_arr=array();$tot_jobPlan_qty_arr=array();
			$tot_trims_cost_arr=array();
			$tot_trim_cal_arr=array();$tot_yarn_cal_arr=array();$tot_job_qty_arr=array();$tot_job_balance_arr=array();$tot_job_data_arr=array();$tot_buyer_job_data_arr=array();
         	 $width="";
			 $sub_yarn_cost=$sub_trims_cost=$other_cost=0;
		    foreach($buyer_job_arr as $buyer_id => $buyer_data)
            {
				//echo count($buyer_data);
          	 $width=75*count($buyer_data)+200;
		    
			 $bg_color="#00CC66";
		    ?>
            <td>
            <div id="outerdiv">
    		<div id="innerdiv">
			<table cellspacing="0" width="<? echo $width ?>px" id="height_td"  border="1" rules="all" class="rpt_table" style="margin:0px;" >
            <thead align="center">
            <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Photo</b></td>
            <?
				 $m=1;
            foreach($buyer_data as $jobNo => $row)
            {
				 if($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 //echo $bgcolor.'dd';
            ?>
            <td width="75"  align="center" bgcolor="<? echo $bgcolor;?>">
            <?
            if($imge_arr[$jobNo]!="")
			{
				 $src="../../../".$imge_arr[$jobNo];
			?>
		
			<a href="##" onClick="generate_image_view('<?= $src;?>','<?=$jobNo;?>','image_view')">  <img  src='../../../<? echo $imge_arr[$jobNo]; ?>' id="image_id" height='150'   width='130' /> </a>
            <?
			}
			else "&nbsp;";
			?>
            </td>
            <?
			$m++;
            }
            ?>
            <td width="75"  bgcolor="<? echo  $bg_color;?>"  align="center"><b>Buyer Total</b></td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Buyer Name</b></td>
                <?
                $b=1;
                foreach($buyer_data as $jobNo => $row)
                {
					 if($b%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<td width="75" bgcolor="<? echo $bgcolor;?>"  align="center"><?  echo $buyer_arr[$buyer_id];?></td>
					<?
					$b++;
                }
                ?>
                 <td width="75" align="center" bgcolor="<? echo $bg_color;?>"><? //echo number_format($tot_balance_value,2); ?></td>
            </tr>
            <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Style Ref</b></td>
            <?
			 $s=1;
          	foreach($buyer_data as $jobNo => $row)
            {
				 if($s%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
          	 <td width="75"  bgcolor="<? echo $bgcolor;?>" align="center"><p><?  echo $row['style_ref_no'];?></p></td>
            <?
			 $s++;
            }
            ?>
             <td width="75" align="center" bgcolor="<? echo $bg_color;?>"><? //echo number_format($tot_balance_value,2); ?></td>
            </tr>
            <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Job No</b></td>
            <?
			$j=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
            <td width="75" bgcolor="<? echo $bgcolor;?>" align="center"> <p> <? echo $jobNo;?></p> </td>
            <?
			$j++;
            }
            ?>
             <td width="75" align="center" bgcolor="<? echo $bg_color;?>"><? //echo number_format($tot_balance_value,2); ?></td>
            </tr>
            <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Avg.FOB/Pcs($)</b></td>
            <?
			$a=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($a%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
            <td width="75"  bgcolor="<? echo $bgcolor;?>" align="right">
         	 <?  echo $row['avg_unit_price'];?>
            </td>
            <?
			$a++;
            }
            ?>
            <td width="75" align="center" bgcolor="<? echo $bg_color;?>"><? //echo number_format($tot_balance_value,2); ?></td>
            </tr>
            <tr>
             <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Order Qty.(Pcs)</b></td> 
            <?
			$o=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($o%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
            <td width="75" bgcolor="<? echo $bgcolor;?>"  align="right">
         	 <?  echo number_format($row['po_qty_pcs'],0);?>
            </td>
            <? 
			$tot_po_qty_arr[$buyer_id]+=$row['po_qty_pcs'];
			$tot_job_qty_arr[$jobNo]+=$row['po_qty_pcs'];
			$o++;
            }
            ?>
            <td width="75" align="right"  bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_po_qty_arr[$buyer_id],2); ?></td>
            </tr>
            
            <tr>
             <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Plan Knit Qty</b></td> 
            <?
			$o=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($o%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
            <td width="75" bgcolor="<? echo $bgcolor;?>"  align="right">
         	 <?  echo number_format($row['plan_cut'],0);?>
            </td>
            <?
			$tot_plan_qty_arr[$buyer_id]+=$row['plan_cut'];
			$tot_jobPlan_qty_arr[$jobNo]+=$row['plan_cut'];
			$o++;
            }
            ?>
            <td width="75" align="right"  bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_plan_qty_arr[$buyer_id],2); ?></td>
            </tr>
             <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>FOB Value($)</b></td>
            <?
			$po_val_cal=0;$f=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($f%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$po_val_cal=$row['po_value'];
            ?>
            <td width="75" bgcolor="<? echo $bgcolor;?>" align="right">
         	 <?  echo number_format($row['po_value'],2);?>
            </td>
            <?
			$tot_po_value_arr[$buyer_id]+=$row['po_value'];
			$tot_job_value_arr[$jobNo]+=$row['po_value'];
			$f++;
            }
            ?>
            <td width="75" align="right"  bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_po_value_arr[$buyer_id],2); ?></td>
            </tr>
           <tr>
            <td class="headcol" width="100" bgcolor="<? echo $bg_color;?>" align="center"><b>Yarn Cost($)</b></td>
            <?
			
			$y=1;$yarn_cost=0;
            foreach($buyer_data as $jobNo => $row)
            {
				if($y%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$poid=rtrim($row['po_id'],',');
				$poids=array_unique(explode(",",$poid));
				
				$po_id=rtrim($row['po_id'],',');
				$po_ids=implode(",",array_unique(explode(",",$po_id)));
				
				foreach($poids as $poId )
				{
					
					//$yarn_cost+=$yarn_costing_arr[$poId];
					$sub_yarn_cost+=$yarn_costing_arr[$poId];
				}
				$yarn_cost=$yarn_req_arr[$buyer_id][$jobNo];
				if($sub_yarn_cost>0)
				{
				$tot_yarn_cal_arr[$jobNo]+=$yarn_cost;$sub_yarn_cost=0;
				}
				$tot_job_data_arr[$jobNo]['yarn']=($yarn_cost/$tot_jobPlan_qty_arr[$jobNo])*12;
				
				//$yarn_costing_arr;
            ?>
            <td width="75" bgcolor="<? echo $bgcolor;?>" align="right"  >
         	 <a href="##" onClick="generate_trims_detail('<? echo $po_ids; ?>','<? echo $jobNo; ?>','2','yarns_popup')"><? echo number_format($yarn_cost,2); ?></a> <? // echo number_format($yarn_cost,2);?>
            </td>
            <?
			$y++;
			$tot_yarn_cost_arr[$buyer_id]+=$yarn_cost;
			$tot_buyer_job_data_arr[$buyer_id]['yarn']+=($yarn_cost/$tot_jobPlan_qty_arr[$jobNo])*12;
            }
            ?>
            <td width="75" align="right" bgcolor="<? echo  $bg_color;?>"><? echo number_format($tot_yarn_cost_arr[$buyer_id],2); ?></td>
            </tr>
            <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Accessories Cost($)</b></td>
            <?
			$ac=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($ac%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$poid=rtrim($row['po_id'],',');
				$poids=array_unique(explode(",",$poid));
				$po_id=rtrim($row['po_id'],',');
				$po_ids=implode(",",array_unique(explode(",",$po_id)));
				
				$trims_cost=0;
				foreach($poids as $poId )
				{
					$trims_cost+=$trims_costing_arr[$poId];
					$sub_trims_cost+=$trims_costing_arr[$poId];
				}
				if($sub_trims_cost>0)
				{
				$tot_trim_cal_arr[$jobNo]+=$sub_trims_cost;$sub_trims_cost=0;
				}
				$tot_job_data_arr[$jobNo]['trim']=($trims_cost/$tot_job_qty_arr[$jobNo])*12;
				//$tot_buyer_job_data_arr[$buyer_id][$jobNo]['trim']=($trims_cost/$tot_job_qty_arr[$jobNo])*12;
            ?>
            <td width="75" bgcolor="<? echo $bgcolor;?>" align="right" >
         	 <a href="##" onClick="generate_trims_detail('<? echo $po_ids; ?>','<? echo $jobNo; ?>','1','trims_popup')"> <? echo number_format($trims_cost,2); ?></a>
            </td>
            <?
			$tot_trims_cost_arr[$buyer_id]+=$trims_cost;
			$tot_buyer_job_data_arr[$buyer_id]['trim']+=($trims_cost/$tot_job_qty_arr[$jobNo])*12;
			$ac++;
			
            }
            ?>
            <td width="75" align="right" bgcolor="<? echo  $bg_color;?>" ><? echo number_format($tot_trims_cost_arr[$buyer_id],2); ?></td>
            </tr>
             <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Others Cost($)</b></td>
            <?
			$oc=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($oc%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$poid=rtrim($row['po_id'],',');
				$poids=array_unique(explode(",",$poid));
				
				$po_id=rtrim($row['po_id'],',');
				$po_ids=implode(",",array_unique(explode(",",$po_id)));
				$testing_cost=$inspection_cost=$certificate_cost=$common_oh=$currier_cost=$embl_cost=$cm_cost=$design_cost=$studio_cost=$commission_cost=$commercial_cost=$depr_amor_pre_cost=$freight_cost=0;
				//other_popup
				foreach($poids as $poId )
				{
					$testing_cost+=$other_costing_arr[$poId]['lab_test'];
					//echo $other_costing_arr[$poId]['common_oh'].'ddd';
					$freight_cost+=$other_costing_arr[$poId]['freight'];
					$inspection_cost+=$other_costing_arr[$poId]['inspection'];
					$certificate_cost+=$other_costing_arr[$poId]['certificate_pre_cost'];
					$common_oh+=$other_costing_arr[$poId]['common_oh'];
					$currier_cost+=$other_costing_arr[$poId]['currier_pre_cost'];
					$wash_cost=$emblishment_costing_arr_name[$poId][3];
					$embl_cost+=$emblishment_costing_arr[$poId]+$wash_cost;
					
					$cm_cost+=$other_costing_arr[$poId]['cm_cost'];
					$depr_amor_pre_cost+=$other_costing_arr[$poId]['depr_amor_pre_cost'];
					$design_cost+=$other_costing_arr[$poId]['design_cost'];
					$studio_cost+=$other_costing_arr[$poId]['studio_cost'];
					
					$commercial_cost+=$commercial_costing_arr[$poId];
                    $local=$commission_costing_arr[$poId][2];
					$foreign=$commission_costing_arr[$poId][1];
					$commission_cost+=$foreign+$local;
				}
				$tot_job_value=$tot_job_value_arr[$jobNo];
				$interest_cost=$tot_job_value*$financial_para[interest_expense]/100;
				$incometax_cost=$tot_job_value*$financial_para[income_tax]/100;
							
				/* $other_cost=$testing_cost+$freight_cost+$inspection_cost+$certificate_cost+$common_oh+$currier_cost+$commercial_cost+$commission_cost+$embl_cost+$cm_cost+$design_cost+$studio_cost+$interest_cost+$incometax_cost+$depr_amor_pre_cost; */
				//change for 18216
				$other_cost=$testing_cost+$freight_cost+$inspection_cost+$certificate_cost+$common_oh+$commercial_cost+$commission_cost+$embl_cost;
				if($other_cost>0)
				{
				$tot_other_cal_arr[$jobNo]+=$other_cost;
				}
				$tot_job_data_arr[$jobNo]['other']=($other_cost/$tot_job_qty_arr[$jobNo])*12;
				 
				
            ?>
            <td width="75" bgcolor="<? echo $bgcolor;?>"  align="right"  title="Lab(<?=$testing_cost;?>)+Freight(<?=$freight_cost;?>)+Inspection(<?=$inspection_cost;?>)+Certificate(<?=$certificate_cost;?>)+OH(<?=$common_oh;?>)+Commercial(<?=$commercial_cost;?>)+Emblishment(<?=$embl_cost;?>)+Commission(<?=$commission_cost;?>)">
         	 <a href="##" title="PO=<? echo $row['po_id'];?>" onClick="generate_trims_detail('<? echo $po_ids; ?>','<? echo $jobNo; ?>','3','others_popup')"> <? echo number_format($other_cost,2); ?></a> <? // echo number_format($other_cost,2);?>
            </td>
            <?
			$oc++;
			$other_cost_arr[$buyer_id]+=$other_cost;
			$tot_buyer_job_data_arr[$buyer_id]['other']+=($other_cost/$tot_job_qty_arr[$jobNo])*12;
            }
            ?>
            <td width="75" align="right" bgcolor="<? echo $bg_color;?>"><? echo number_format($other_cost_arr[$buyer_id],2); ?></td>
            </tr>
            <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Total Cost($)</b></td>
            <?
			$total_cost_cal=0;$tc=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($tc%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$total_cost=$tot_yarn_cost_arr[$buyer_id]+$tot_trims_cost_arr[$buyer_id]+$other_cost_arr[$buyer_id];
				$total_cost_cal=$tot_trim_cal_arr[$jobNo]+$tot_other_cal_arr[$jobNo]+$tot_yarn_cal_arr[$jobNo];
			$tot_cost_cal_arr[$jobNo]+=$total_cost_cal;	
            ?>
            <td width="75"  bgcolor="<? echo $bgcolor;?>" align="right">
         	 <?  echo number_format($total_cost_cal,2);?>
            </td>
            <?
			$total_cost_arr[$buyer_id]+=$total_cost_cal;
			$total_cost_cal=0;
			$tc++;
			//$total_cost_cal_arr[$buyer_id]=$total_cost_cal;
            }
            ?>
            <td width="75" align="right" bgcolor="<? echo $bg_color;?>"><? echo number_format($total_cost_arr[$buyer_id],2); ?></td>
            </tr>
            <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Total CM Value($)</b></td>
            <?
			$bc=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($bc%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$po_balance=$tot_job_value_arr[$jobNo]-$tot_cost_cal_arr[$jobNo];
				
				//$po_balance=$po_val_cal-$total_cost_cal;
				
            ?>
            <td width="75" bgcolor="<? echo $bgcolor;?>" align="right" title="Job Value-Total Cost">
         	 <?  echo number_format($po_balance,2);?>
            </td>
            <?
			$tot_po_balance_arr[$buyer_id]+=$po_balance; 
			$tot_job_balance_arr[$jobNo]+=$tot_job_value_arr[$jobNo];
			
			//$tot_buyer_job_data_arr[$buyer_id]['fob']+=$tot_job_value_arr[$jobNo];
			
			$bc++;
            }
            ?>
            <td width="75" align="right" bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_po_balance_arr[$buyer_id],2); ?></td>
            </tr>
            <tr>
           
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color; ?>" align="center"><b>CM/DZN($)</b></td>
            <?
			$cm=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($cm%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$fob_dzn_val=$tot_job_balance_arr[$jobNo]/$tot_job_qty_arr[$jobNo]*12;
				
				$tot_yarn=$tot_job_data_arr[$jobNo]['yarn'];
				$tot_trim=$tot_job_data_arr[$jobNo]['trim'];
				$tot_other=$tot_job_data_arr[$jobNo]['other'];
				//$tot_other=$tot_job_data_arr[$jobNo]['other'];
				
				//$cm_dzn=$fob_dzn_val-($tot_yarn+$tot_trim+$tot_other);//
				$cm_dzn=$pre_cost_mergin_arr[$jobNo];//
				//$tot_job_balance_arr[$jobNo]/$tot_job_qty_arr[$jobNo]*12;
            ?>
            <td width="75"  bgcolor="<? echo $bgcolor;?>" title="(<? echo $cm_dzn?>)"  align="right">
         	 <?  echo number_format($cm_dzn,2);?>
            </td>
            <?
			$tot_cm_dzn_value_arr[$buyer_id]+=$row['po_value'];
			//$tot_buyer_job_data_arr[$buyer_id]['other']+=$cm_dzn;
			
			$cm++;
            }
			$tot_job_cm_dzn=0;
			//$tot_buyer_job_data_arr[$buyer_id]['other'];
			
				$buyer_fob_dzn_val=$tot_po_value_arr[$buyer_id]/$tot_po_qty_arr[$buyer_id]*12;
				
				$tot_buyer_yarn=$tot_buyer_job_data_arr[$buyer_id]['yarn'];//$tot_buyer_job_data_arr[$buyer_id]['yarn']
				$tot_buyer_trim=$tot_buyer_job_data_arr[$buyer_id]['trim'];
				$tot_buyer_other=$tot_buyer_job_data_arr[$buyer_id]['other'];
				//echo $buyer_fob_dzn_val.'='.$tot_buyer_yarn.'='.$tot_buyer_trim.'='.$tot_buyer_other;
				//$tot_job_cm_dzn=$buyer_fob_dzn_val-($tot_buyer_yarn+$tot_buyer_trim+$tot_buyer_other); // previous formula

				$tot_job_cm_dzn=($tot_po_balance_arr[$buyer_id]/$tot_po_qty_arr[$buyer_id])*12
				
			//$tot_job_cm_dzn=$tot_po_balance_arr[$buyer_id]/$tot_po_qty_arr[$buyer_id]*12;
            ?>
            <td width="75" align="right" title="(Total CM Value/Order Qty.(Pcs))*12" bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_job_cm_dzn,2); ?></td>
            </tr>
        </tbody>
        </table>
        </div>
        </div>
         </td>
        <?
			}
		?>
       		
            </tr>
    </table>
    <br>
    
            </div>
        </div>
<?

	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****$reporttype"; 
    exit();
}

if($action=="report_generate2") //Show 2
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$reporttype=str_replace("'","",$reporttype);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_style_owner=str_replace("'","",$cbo_style_owner);
	$style_ref_id=str_replace("'","",$txt_style_ref_id);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	if($cbo_style_owner==0) $style_owner_cond=""; else $style_owner_cond=" and a.style_owner='$cbo_style_owner' ";
	if($cbo_company_name==0) $company_name_cond=""; else $company_name_cond=" and a.company_name='$cbo_company_name' ";
	$job_style_cond="";
	if(trim(str_replace("'","",$txt_style_ref))!="")
	{
		if(str_replace("'","",$style_ref_id)!="")
		{
			$job_style_cond=" and a.id in(".str_replace("'","",$style_ref_id).")";
		}
		else
		{
			$job_style_cond=" and a.style_ref_no = '".trim(str_replace("'","",$txt_style_ref))."'";
		}
	}

	if(str_replace("'","",$cbo_buyer_name)==0)
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
		$buyer_id_cond=" and a.buyer_name in($cbo_buyer_name)";
	}
	$date_cond="";
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
			$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
		}
	
	
	$style_row=count($txt_style_ref);
	ob_start();
	
	/*$pre_sql="select a.costing_per,a.job_no,a.exchange_rate from wo_pre_cost_mst a where a.status_active=1 and a.is_deleted=0 ";
	$sql_pre_result=sql_select($pre_sql);
	foreach($sql_pre_result as $row)
	{
		$costing_per_arr[$row[csf("job_no")]]['cost_per']=$row[csf("costing_per")];
		$costing_per_arr[$row[csf("job_no")]]['ex_rate']=$row[csf("exchange_rate")];
	}*/
	
	  $sql="select a.id,a.job_no_prefix_num as job_prefix,a.ship_mode,a.avg_unit_price, a.job_no, a.company_name, a.client_id,a.buyer_name, a.team_leader, a.style_description as style_desc, a.style_ref_no, a.quotation_id,a.inquiry_id,a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id as po_id, b.po_number,b.is_confirmed, b.pub_shipment_date,b.shipment_date,b.up_charge ,b.po_quantity,b.plan_cut,b.matrix_type, b.unit_price, b.po_total_price,b.status_active,c.item_number_id,c.plan_cut_qnty,c.order_quantity,c.order_total,c.color_number_id from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and c.po_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active=1  and c.is_deleted=0   $company_name_cond $job_style_cond $buyer_id_cond $date_cond  order  by b.pub_shipment_date";
	
	$sql_po_result=sql_select($sql);
	$all_po_id="";$all_job="";$all_full_job="";$all_style="";$all_style_desc=""; $all_buyer=""; $all_buyer_client=""; $all_order_uom=""; 
	$order_qty_pcs=0;$total_fob_value=0;$total_order_qty=0;$total_unit_price=0;$total_job_unit_price=0;
	//echo $buyer_name;die;
	$po_numer_arr=$po_data_arr=array();$tot_count=0;$total_order_upcharge=0;$item_color_size_array=array();$job_id_arr=array();
	foreach($sql_po_result as $row)
	{
		if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
		if($all_job=="") $all_job=$row[csf("id")]; else $all_job.=",".$row[csf("id")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_no']=$row[csf("po_number")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['style_ref_no']=$row[csf("style_ref_no")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_qty_pcs']+=$row[csf("order_quantity")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['plan_cut']+=$row[csf("plan_cut_qnty")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_value']+=$row[csf("order_total")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['avg_unit_price']=$row[csf("avg_unit_price")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_id'].=$row[csf("po_id")].",";
		$quotation_idArr[$row[csf('quotation_id')]]=$row[csf('quotation_id')];
	
		$job_buyer_name_arr[$row[csf("job_no")]]['buyer_name']=$row[csf("buyer_name")];
		$item_color_size_array[$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]+=$row[csf("plan_cut_qnty")];
		
		$job_id_arr[$row[csf("id")]]=$row[csf("id")];
  } 
  if(count($quotation_idArr)>0)
  {
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."  and ENTRY_FORM=158");
		oci_commit($con);
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 158, 1, $quotation_idArr, $empty_arr);//Quot. ID Ref from=1
	 

	    $sqlQc="select c.job_no,a.qc_no,a.offer_qty, a.costing_per as costing_per, b.confirm_fob from qc_mst a, qc_confirm_mst b,wo_po_details_master c,gbl_temp_engine g where a.qc_no=b.cost_sheet_id  and c.quotation_id=a.qc_no and c.quotation_id=b.cost_sheet_id and a.qc_no=g.ref_val and b.cost_sheet_id=g.ref_val and c.quotation_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=158    and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";
	$dataQc=sql_select($sqlQc); 
	//print_r($dataQc);
	foreach($dataQc as $qcrow)
	{
		//echo $qcrow[csf('offer_qty')].'=';
		// $quaOfferQnty=$jobQty;//$qcrow[csf('offer_qty')];
		// $quaConfirmPrice=$qcrow[csf('confirm_fob')];
		// $quaConfirmPriceDzn=$qcrow[csf('confirm_fob')];
		// $quaPriceWithCommnPcs=$qcrow[csf('confirm_fob')];
		$quaCostingPer=$qcrow[csf('costing_per')];
		$qc_no=$qcrow[csf('qc_no')];
		//$quaCostingPerQty=0;
		//if($quaCostingPer==1)
		//$quaCostingPerQty=1;
		$quaCostingPerArr[$qcrow[csf('job_no')]]=$quaCostingPer;
	}
	unset($dataQc);
  }
  //print_r($quaCostingPerArr);

  
    $sqlQc_dtls_other="select a.buyer_id,c.job_no,a.qc_no,b.tot_fab_cost,b.tot_accessories_cost,b.operating_exp,b.tot_fright_cost,b.tot_lab_test_cost,b.tot_other_cost,b.tot_commission_cost,b.tot_wash_cost,b.commercial_cost,b.tot_cm_cost,b.tot_sp_operation_cost from qc_mst a,qc_tot_cost_summary b,wo_po_details_master c,gbl_temp_engine g where a.qc_no=b.mst_id  and c.quotation_id=b.mst_id   and a.qc_no=g.ref_val and b.mst_id=g.ref_val and c.quotation_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=158 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";
  $dataQcOthers=sql_select($sqlQc_dtls_other);
  foreach($dataQcOthers as $row)
	{
		$quaCostingPer=$quaCostingPerArr[$row[csf('job_no')]];
		$tot_fab_cost=$row[csf('tot_fab_cost')];
		$tot_accessories_cost=$row[csf('tot_accessories_cost')];
		$tot_fright_cost=$row[csf('tot_fright_cost')];
		$tot_lab_test_cost=$row[csf('tot_lab_test_cost')];
		$tot_other_cost=$row[csf('tot_other_cost')];
		$tot_wash_cost=$row[csf('tot_wash_cost')];
		$tot_sp_operation_cost=$row[csf('tot_sp_operation_cost')];
		$operating_exp=$row[csf('operating_exp')];
		 
		if($quaCostingPer==1)
		{
			$quaCostingPerQty=12;
		}
		else $quaCostingPerQty=1;
		//
		$order_qty_pcs=$buyer_job_arr[$row[csf("buyer_id")]][$row[csf("job_no")]]['po_qty_pcs'];
		//echo  $tot_fab_cost.'='.$quaCostingPerQty.'='.$order_qty_pcs.'='.$quaCostingPer.'<br>';
		$qoutCostArr[$row[csf("buyer_id")]][$row[csf("job_no")]]['cm_dzn']+=$row[csf("tot_cm_cost")];
		$qoutCostArr[$row[csf("buyer_id")]][$row[csf("job_no")]]['yarn_cost']+=($tot_fab_cost/$quaCostingPerQty)*$order_qty_pcs;
		$qoutCostArr[$row[csf("buyer_id")]][$row[csf("job_no")]]['trim_cost']+=($tot_accessories_cost/$quaCostingPerQty)*$order_qty_pcs;
		$qoutCostArr[$row[csf("buyer_id")]][$row[csf("job_no")]]['freight_cost']+=($tot_fright_cost/$quaCostingPerQty)*$order_qty_pcs;
		$qoutCostArr[$row[csf("buyer_id")]][$row[csf("job_no")]]['lab_cost']+=($tot_lab_test_cost/$quaCostingPerQty)*$order_qty_pcs;
		$qoutCostArr[$row[csf("buyer_id")]][$row[csf("job_no")]]['other_cost']+=($tot_other_cost/$quaCostingPerQty)*$order_qty_pcs;
		$qoutCostArr[$row[csf("buyer_id")]][$row[csf("job_no")]]['wash_cost']+=($tot_wash_cost/$quaCostingPerQty)*$order_qty_pcs;
		$qoutCostArr[$row[csf("buyer_id")]][$row[csf("job_no")]]['sp_op_cost']+=($tot_sp_operation_cost/$quaCostingPerQty)*$order_qty_pcs;$qoutCostArr[$row[csf("buyer_id")]][$row[csf("job_no")]]['operating_exp']+=($operating_exp/$quaCostingPerQty)*$order_qty_pcs;
	}
 //print_r($qoutCostArr);
 unset($dataQcOthers);
 
 execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."  and ENTRY_FORM=158");
 oci_commit($con);
 disconnect($con);
 // echo $all_po_id.'DDDDDDDD';
  if($all_po_id=="") {echo "<div style='color:red; font-size:30px;' align='center'>No PO No Found </div>";die;}
	//echo $tot_count;
	//echo $all_po_id.'dsd';
		$all_job_no=array_unique(explode(",",$all_full_job));
		$all_jobs="";
		foreach($all_job_no as $jno)
		{
				if($all_jobs=="") $all_jobs=$jno; else $all_jobs.=",".$jno;
		}
		$all_po=array_unique(explode(",",$all_po_id));
		$all_poIDs=implode(",",array_unique(explode(",",$all_po_id)));
		$po_arr_cond=array_chunk($all_po,1000, true);
		$po_cond_for_in="";$po_cond_for_in2="";$po_cond_for_in3="";
		$pi=0;
		foreach($po_arr_cond as $key=>$value)
		{
		   if($pi==0)
		   {
			$po_cond_for_in=" and ( c.po_break_down_id  in(".implode(",",$value).")"; 
			$po_cond_for_in2=" and ( d.po_break_down_id  in(".implode(",",$value).")"; 
			
		   }
		   else //po_break_down_id
		   {
			$po_cond_for_in.=" or c.po_break_down_id  in(".implode(",",$value).")";
			$po_cond_for_in2.=" or d.po_break_down_id  in(".implode(",",$value).")";
			
		   }
		   $pi++;
		}	
		$po_cond_for_in.=" )";
		$po_cond_for_in2.=" )";
		
	// 	$condition= new condition();
	// 	$condition->company_name("=$cbo_company_name");
	// 	if(str_replace("'","",$cbo_buyer_name)>0){
	// 		  $condition->buyer_name("in($cbo_buyer_name)");
	// 	 }
	// 	 if($all_po_id!='' || $all_po_id!=0)
	// 	 {
	// 		$condition->po_id_in("$all_poIDs"); 
	// 	 }
	// 	 if(str_replace("'","",$txt_style_ref)!='')
	// 	 {
	// 		//echo "in($txt_order_id)".'dd';die;
	// 		//$condition->job_no("in($all_jobs)");
	// 	 }
	// 	$condition->init();
	// 	$costPerArr=$condition->getCostingPerArr();
	// 	$emblishment= new emblishment($condition); 
	
	// 	$wash= new wash($condition);
	// 	$commercial= new commercial($condition);
		
	// //	$trims_ReqQty_arr=$trims->getQtyArray_by_precostdtlsid();//getQtyArray_by_jobAndPrecostdtlsid();
	// 	//print_r($trims_ReqQty_arr);
	// 	$trims= new trims($condition);
	// 	//echo $trims->getQuery(); die;
	// 	//echo $emblishment->getQuery(); die;
	// 	$trims_costing_arr=$trims->getAmountArray_by_order();//getAmountArray_by_jobAndPrecostdtlsid();
		
	// 	$emblishment_costing_arr=$emblishment->getAmountArray_by_order();
	// 	$wash_amount_arr=$wash->getAmountArray_by_jobEmbnameAndEmbtype();
	// 	$commercial_costing_arr=$commercial->getAmountArray_by_order();
	// 	$commission= new commision($condition);
	// 	$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
	// 	$yarn= new yarn($condition);
	// 	//echo $yarn->getQuery(); die;
	// 	$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
	// 	$other= new other($condition);
	// 	$other_costing_arr=$other->getAmountArray_by_order();
		
			
		$jobIds=chop($all_job,',');
		$prod_cond_for_in="";
		$prod_ids=count(array_unique(explode(",",$all_job)));
		if($db_type==2 && $prod_ids>1000)
		{
		$prod_cond_for_in=" and (";
		$prodIdsArr=array_chunk(explode(",",$jobIds),999);
		foreach($prodIdsArr as $ids)
		{
		$ids=implode(",",$ids);
		$prod_cond_for_in.=" a.job_id in($ids) or"; 
		}
		$prod_cond_for_in=chop($prod_cond_for_in,'or ');
		$prod_cond_for_in.=")";
		}
		else
		{
		$jobIds=implode(",",array_unique(explode(",",$jobIds)));
		$prod_cond_for_in=" and a.job_id in($jobIds)";
		}
	 $data_sql="select a.id, a.fabric_cost_dtls_id,b.costing_per, a.job_no,a.job_id, a.count_id, a.copm_one_id, a.percent_one, a.copm_two_id, a.percent_two, a.type_id, a.cons_ratio, a.cons_qnty, a.rate, a.amount, a.avg_cons_qnty, a.supplier_id, a.color, a.consdznlbs, a.rate_dzn, 
	b.item_number_id, b.body_part_id, b.fabric_description, b.color_type_id, b.uom, c.color_number_id, c.stripe_color, c.measurement 
	from wo_pre_cost_fab_yarn_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_pre_stripe_color c where a.fabric_cost_dtls_id=b.id and b.id=c.pre_cost_fabric_cost_dtls_id and a.color=c.stripe_color  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $prod_cond_for_in";
	//echo $data_sql; die;
	$result_data=sql_select($data_sql);
	//$lib_yarn_count=return_library_array( "select yarn_count, id from lib_yarn_count", "id", "yarn_count");
        $i=1; $totCons=0; $totReq=0; $totAmt=0;$amount_req=0;
        foreach ($result_data as $yarn)
        {
			//echo $job_id=$yarn[csf('job_id')];
			$costPerQty=$costPerArr[$yarn[csf("job_no")]];
			//echo $costPerQty.'dd';
			$job_buyer=$job_buyer_name_arr[$yarn[csf("job_no")]]['buyer_name'];
			$poQty=0; $yarn_req_kg=0; $yarn_req_lbs=0;
			$poQty=$item_color_size_array[$job_buyer][$yarn[csf("job_no")]][$yarn[csf('item_number_id')]][$yarn[csf('color_number_id')]];
            $yarn_req_kg=($yarn[csf('measurement')]/$costPerQty)*$poQty;
			$yarn_req_lbs=$yarn_req_kg*2.20462;
			
            $amount_req=$yarn_req_lbs*$yarn[csf('rate')];
			//echo $amount_req.'=='.$poQty.'<br>';
			if($amount_req>0)
			{
			$yarn_req_arr[$job_buyer][$yarn[csf('job_no')]]+= $amount_req;
			}
		}
		$financial_para=array();
		$sql_std_para=sql_select("select interest_expense,income_tax,cost_per_minute from lib_standard_cm_entry where company_id=$cbo_company_name and status_active=1 and	is_deleted=0 order by id");
		foreach($sql_std_para as $sql_std_row){
		$financial_para[interest_expense]=$sql_std_row[csf('interest_expense')];
		$financial_para[income_tax]=$sql_std_row[csf('income_tax')];
		$financial_para[cost_per_minute]=$sql_std_row[csf('cost_per_minute')];
		}
		unset($sql_std_para);
		
		$sql_pre=sql_select("select job_no,margin_dzn from wo_pre_cost_dtls where  status_active=1 and	is_deleted=0  ".where_con_using_array($job_id_arr,0,'job_id')."  order by job_no");
		foreach($sql_pre as $row){
		$pre_cost_mergin_arr[$row[csf('job_no')]]=$row[csf('margin_dzn')];
		}
		//print_r($pre_cost_mergin_arr);
		unset($sql_pre);
		
		
		
				$style1="#E9F3FF"; 
				$style="#FFFFFF";
	?>
        <div style="width:100%; padding-left:0px;">
        <br><br><br> <br> 
           <?
          	$style1="#E9F3FF"; 
			$style="#FFFFFF";
			//echo count($buyer_job_arr);
			//print_r($buyer_job_arr);
			$imge_arr=return_library_array( "select master_tble_id,image_location from  common_photo_library where file_type=1 and form_name='knit_order_entry' ",'master_tble_id','image_location');
		   ?>
           <style>
	    table tr td { height:30px; font-size:14px;
		  
	   }
	   /*#outerdiv {
            position: absolute;
            top: 0;
            left: 0;
            right: 5em;
        }
        #innerdiv {
            width: 100%;
            overflow-x:scroll;
            margin-left: 5em;
            overflow-y:visible;
            padding-bottom:1px;
        }
        .headcol {
            position:absolute;
            width:5em;
            left:0;
            top:auto;
            border-right: 0px none black;
            border-top-width:3px;
            /*only relevant for first row
            margin-top:-3px;
            /*compensate for top border
        }
        .headcol:before {
            content:'Row ';
        }*/
        table#height_td tr th:first-child, table#height_td td:first-child{
		  position: sticky;
		  width: 100px;
		  left: 0;
		  z-index: 10;
		}
		table#height_td tr th:first-child{
		  z-index: 11;
		}
		table#height_td tr th{
		  position: sticky;
		  top: 0;
		  z-index: 9;
		}
		</style> 
		
         <div style="margin-left:10px">
          <table width="100%" style="margin-left:10px">
             <tr class="form_caption" style="font-size:24px;">
               <td align="center" width="100%" colspan="10" class="form_caption"><? echo $company_library[$cbo_company_name].'<br>';if($cbo_style_owner!=0) echo 'Style Owner: '.$company_library[$cbo_style_owner]; ?></td>
                    
                </tr>
                <tr>
                  <td colspan="10" style="font-size:24px" align="center"><strong>Style Wise CM Report</strong></td>
                </tr>
            </table>
            <table cellspacing="0" width="100%"  border="0">
            <tr valign="top">
       		<?
            $tot_po_qty=0;$tot_po_qty_arr=array();$tot_po_value_arr=array();$total_cost_arr=array();$other_cost_arr=array();$tot_plan_qty_arr=array();$tot_jobPlan_qty_arr=array();
			$tot_trims_cost_arr=array();
			$tot_trim_cal_arr=array();$tot_yarn_cal_arr=array();$tot_job_qty_arr=array();$tot_job_balance_arr=array();$tot_job_data_arr=array();$tot_buyer_job_data_arr=array();
         	 $width="";
			 $sub_yarn_cost=$sub_trims_cost=$other_cost=0;
		    foreach($buyer_job_arr as $buyer_id => $buyer_data)
            {
				//echo count($buyer_data);
          	 $width=75*count($buyer_data)+210;
		    
			 $bg_color="#00CC66";
		    ?>
            <td>
            <div id="outerdiv">
    		<div id="innerdiv">
			<table cellspacing="0" width="<? echo $width ?>px" id="height_td"  border="1" rules="all" class="rpt_table" style="margin:0px;" >
            <thead align="center">
            <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Photo</b></td>
            <?
				 $m=1;
            foreach($buyer_data as $jobNo => $row)
            {
				 if($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 //echo $bgcolor.'dd';
            ?>
            <td width="75"  align="center" bgcolor="<? echo $bgcolor;?>">
            <?
            if($imge_arr[$jobNo]!="")
			{
				 $src="../../../".$imge_arr[$jobNo];
			?>
		
			<a href="##" onClick="generate_image_view('<?= $src;?>','<?=$jobNo;?>','image_view')">  <img  src='../../../<? echo $imge_arr[$jobNo]; ?>' id="image_id" height='150'   width='130' /> </a>
            <?
			}
			else "&nbsp;";
			?>
            </td>
            <?
			$m++;
            }
            ?>
            <td width="85"  bgcolor="<? echo  $bg_color;?>"  align="center"><b>Buyer Total</b></td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Buyer Name</b></td>
                <?
                $b=1;
                foreach($buyer_data as $jobNo => $row)
                {
					 if($b%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<td width="75" bgcolor="<? echo $bgcolor;?>"  align="center"><?  echo $buyer_arr[$buyer_id];?></td>
					<?
					$b++;
                }
                ?>
                 <td width="85" align="center" bgcolor="<? echo $bg_color;?>"><? //echo number_format($tot_balance_value,2); ?>&nbsp;&nbsp;&nbsp;</td>
            </tr>
            <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Style Ref</b></td>
            <?
			 $s=1;
          	foreach($buyer_data as $jobNo => $row)
            {
				 if($s%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
          	 <td width="75"  bgcolor="<? echo $bgcolor;?>" align="center"><p><?  echo $row['style_ref_no'];?></p></td>
            <?
			 $s++;
            }
            ?>
             <td width="85" align="center" bgcolor="<? echo $bg_color;?>"><? //echo number_format($tot_balance_value,2); ?>&nbsp;&nbsp;&nbsp;</td>
            </tr>
            <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Job No</b></td>
            <?
			$j=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
            <td width="75" bgcolor="<? echo $bgcolor;?>" align="center"> <p> <? echo $jobNo;?></p> </td>
            <?
			$j++;
            }
            ?>
             <td width="85" align="center" bgcolor="<? echo $bg_color;?>"><? //echo number_format($tot_balance_value,2); ?>&nbsp;&nbsp;&nbsp;</td>
            </tr>
            <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Avg.FOB/Pcs($)</b></td>
            <?
			$a=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($a%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
            <td width="75"  bgcolor="<? echo $bgcolor;?>" align="right">
         	 <?  echo $row['avg_unit_price'];?>
            </td>
            <?
			$a++;
            }
            ?>
            <td width="85" align="center" bgcolor="<? echo $bg_color;?>"><? //echo number_format($tot_balance_value,2); ?>&nbsp;&nbsp;&nbsp;</td>
            </tr>
            <tr>
             <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Order Qty.(Pcs)</b></td> 
            <?
			$o=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($o%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
            <td width="75" bgcolor="<? echo $bgcolor;?>"  align="right">
         	 <?  echo number_format($row['po_qty_pcs'],0);?>
            </td>
            <? 
			$tot_po_qty_arr[$buyer_id]+=$row['po_qty_pcs'];
			$tot_job_qty_arr[$jobNo]+=$row['po_qty_pcs'];
			$o++;
            }
            ?>
            <td width="85" align="right"  bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_po_qty_arr[$buyer_id],2); ?>&nbsp;&nbsp;&nbsp;</td>
            </tr>
            
            <tr>
             <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Plan Knit Qty</b></td> 
            <?
			$o=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($o%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
            <td width="75" bgcolor="<? echo $bgcolor;?>"  align="right">
         	 <?  echo number_format($row['plan_cut'],0);?>
            </td>
            <?
			$tot_plan_qty_arr[$buyer_id]+=$row['plan_cut'];
			$tot_jobPlan_qty_arr[$jobNo]+=$row['plan_cut'];
			$o++;
            }
            ?>
            <td width="85" align="right"  bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_plan_qty_arr[$buyer_id],2); ?>&nbsp;&nbsp;&nbsp;</td>
            </tr>
             <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>FOB Value($)</b></td>
            <?
			$po_val_cal=0;$f=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($f%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$po_val_cal=$row['po_value'];
            ?>
            <td width="75" bgcolor="<? echo $bgcolor;?>" align="right">
         	 <?  echo number_format($row['po_value'],2);?>
            </td>
            <?
			$tot_po_value_arr[$buyer_id]+=$row['po_value'];
			$tot_job_value_arr[$jobNo]+=$row['po_value'];
			$f++;
            }
            ?>
            <td width="85" align="right"  bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_po_value_arr[$buyer_id],2); ?>&nbsp;&nbsp;&nbsp;</td>
            </tr>
           <tr>
            <td class="headcol" width="100" bgcolor="<? echo $bg_color;?>" align="center"><b>Yarn Cost($)</b></td>
            <?
			
			$y=1;$yarn_cost=0;
            foreach($buyer_data as $jobNo => $row)
            {
				if($y%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$poid=rtrim($row['po_id'],',');
				$poids=array_unique(explode(",",$poid));
				
				$po_id=rtrim($row['po_id'],',');
				$po_ids=implode(",",array_unique(explode(",",$po_id)));
				
				foreach($poids as $poId )
				{
					
					//$yarn_cost+=$yarn_costing_arr[$poId];
					//$sub_yarn_cost+=$yarn_costing_arr[$poId];
				}
				$yarn_cost=$qoutCostArr[$buyer_id][$jobNo]['yarn_cost'];
				if($yarn_cost>0)
				{
				$tot_yarn_cal_arr[$jobNo]+=$yarn_cost;$sub_yarn_cost=0;
				}
				$tot_job_data_arr[$jobNo]['yarn']=($yarn_cost/$tot_jobPlan_qty_arr[$jobNo])*12;
				
				//$yarn_costing_arr;
				
            ?>
            <td width="75" bgcolor="<? echo $bgcolor;?>" align="right"  > 
         	 <a href="##" onClick="generate_trims_detail('<? echo $po_ids; ?>','<? echo $jobNo; ?>','2','yarns_popup2')"><?  //echo number_format($yarn_cost,2); ?></a> <?  echo number_format($yarn_cost,2);?>
            </td>
            <?
			$y++;
			$tot_yarn_cost_arr[$buyer_id]+=$yarn_cost;
			$tot_buyer_job_data_arr[$buyer_id]['yarn']+=($yarn_cost/$tot_jobPlan_qty_arr[$jobNo])*12;
            }
            ?>
            <td width="85" align="right" bgcolor="<? echo  $bg_color;?>"><? echo number_format($tot_yarn_cost_arr[$buyer_id],2); ?>&nbsp;&nbsp;&nbsp;</td>
            </tr>
            <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Accessories Cost($)</b></td>
            <?
			$ac=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($ac%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$poid=rtrim($row['po_id'],',');
				$poids=array_unique(explode(",",$poid));
				$po_id=rtrim($row['po_id'],',');
				$po_ids=implode(",",array_unique(explode(",",$po_id)));
				
				$trims_cost=0;
				foreach($poids as $poId )
				{
					$trims_cost+=$trims_costing_arr[$poId];
					$sub_trims_cost+=$trims_costing_arr[$poId];
				}
				$trims_cost=$qoutCostArr[$buyer_id][$jobNo]['trim_cost'];
				if($trims_cost>0)
				{
				$tot_trim_cal_arr[$jobNo]+=$trims_cost;//$sub_trims_cost=0;
				}
				$tot_job_data_arr[$jobNo]['trim']=($trims_cost/$tot_job_qty_arr[$jobNo])*12;
				//$tot_buyer_job_data_arr[$buyer_id][$jobNo]['trim']=($trims_cost/$tot_job_qty_arr[$jobNo])*12;
            ?>
            <td width="75" bgcolor="<? echo $bgcolor;?>" align="right" >
         	 <a href="##" onClick="generate_trims_detail('<? echo $po_ids; ?>','<? echo $jobNo; ?>','1','trims_popup2')"> <? //echo number_format($trims_cost,2); ?></a><?  echo number_format($trims_cost,2);?>
            </td>
            <?
			$tot_trims_cost_arr[$buyer_id]+=$trims_cost;
			$tot_buyer_job_data_arr[$buyer_id]['trim']+=($trims_cost/$tot_job_qty_arr[$jobNo])*12;
			$ac++;
			
            }
            ?>
            <td width="85" align="right" bgcolor="<? echo  $bg_color;?>" ><? echo number_format($tot_trims_cost_arr[$buyer_id],2); ?>&nbsp;&nbsp;&nbsp;</td>
            </tr>
             <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Others Cost($)</b></td>
            <?
			$oc=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($oc%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$poid=rtrim($row['po_id'],',');
				$poids=array_unique(explode(",",$poid));
				
				$po_id=rtrim($row['po_id'],',');
				$po_ids=implode(",",array_unique(explode(",",$po_id)));
				$testing_cost=$inspection_cost=$certificate_cost=$common_oh=$currier_cost=$embl_cost=$cm_cost=$design_cost=$studio_cost=$commission_cost=$commercial_cost=$depr_amor_pre_cost=$freight_cost=0;
				
				$tot_job_value=$tot_job_value_arr[$jobNo];
				$interest_cost=$tot_job_value*$financial_para[interest_expense]/100;
				$incometax_cost=$tot_job_value*$financial_para[income_tax]/100;

				$freight_cost=$qoutCostArr[$buyer_id][$jobNo]['freight_cost'];
				$testing_cost=$qoutCostArr[$buyer_id][$jobNo]['lab_cost'];
				$qout_other_cost=$qoutCostArr[$buyer_id][$jobNo]['other_cost'];
				$wash_cost=$qoutCostArr[$buyer_id][$jobNo]['wash_cost'];
				$sp_op_cost=$qoutCostArr[$buyer_id][$jobNo]['sp_op_cost'];
				$operating_exp=$qoutCostArr[$buyer_id][$jobNo]['operating_exp'];
							
				/* $other_cost=$testing_cost+$freight_cost+$inspection_cost+$certificate_cost+$common_oh+$currier_cost+$commercial_cost+$commission_cost+$embl_cost+$cm_cost+$design_cost+$studio_cost+$interest_cost+$incometax_cost+$depr_amor_pre_cost; */
				//change for 18216
				$other_cost=$testing_cost+$freight_cost+$qout_other_cost+$wash_cost+$sp_op_cost+$operating_exp;
				if($other_cost>0)
				{
				$tot_other_cal_arr[$jobNo]+=$other_cost;
				}
				$tot_job_data_arr[$jobNo]['other']=($other_cost/$tot_job_qty_arr[$jobNo])*12;
				 
				
            ?>
            <td width="75" bgcolor="<? echo $bgcolor;?>"  align="right"  title="Lab(<?=$testing_cost;?>)+Freight(<?=$freight_cost;?>)+OtherCost(<?=$qout_other_cost;?>)+Wash(<?=$wash_cost;?>)+Special OP(<?=$sp_op_cost;?>)+operating exp(<?=$operating_exp;?>);?>)">
         	 <a href="##" title="PO=<? echo $row['po_id'];?>" onClick="generate_trims_detail('<? echo $po_ids; ?>','<? echo $jobNo; ?>','3','others_popup2')"> <? //echo number_format($other_cost,2); ?></a> <?  echo number_format($other_cost,2);?>
            </td>
            <?
			$oc++;
			$other_cost_arr[$buyer_id]+=$other_cost;
			$tot_buyer_job_data_arr[$buyer_id]['other']+=($other_cost/$tot_job_qty_arr[$jobNo])*12;
            }
            ?>
            <td width="85" align="right" bgcolor="<? echo $bg_color;?>"><? echo number_format($other_cost_arr[$buyer_id],2); ?>&nbsp;&nbsp;&nbsp;</td>
            </tr>
            <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Total Cost($)</b></td>
            <?
			$total_cost_cal=0;$tc=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($tc%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$total_cost=$tot_yarn_cost_arr[$buyer_id]+$tot_trims_cost_arr[$buyer_id]+$other_cost_arr[$buyer_id];
				$total_cost_cal=$tot_trim_cal_arr[$jobNo]+$tot_other_cal_arr[$jobNo]+$tot_yarn_cal_arr[$jobNo];
			$tot_cost_cal_arr[$jobNo]+=$total_cost_cal;	
            ?>
            <td width="75"  bgcolor="<? echo $bgcolor;?>" align="right">
         	 <?  echo number_format($total_cost_cal,2);?>
            </td>
            <?
			$total_cost_arr[$buyer_id]+=$total_cost_cal;
			$total_cost_cal=0;
			$tc++;
			//$total_cost_cal_arr[$buyer_id]=$total_cost_cal;
            }
            ?>
            <td width="85" align="right" bgcolor="<? echo $bg_color;?>"><? echo number_format($total_cost_arr[$buyer_id],2); ?>&nbsp;&nbsp;&nbsp;</td>
            </tr>
            <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Total CM Value($)</b></td>
            <?
			$bc=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($bc%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$po_balance=$tot_job_value_arr[$jobNo]-$tot_cost_cal_arr[$jobNo];
				
				//$po_balance=$po_val_cal-$total_cost_cal;
				
            ?>
            <td width="75" bgcolor="<? echo $bgcolor;?>" align="right" title="Job Value-Total Cost">
         	 <?  echo number_format($po_balance,2);?>
            </td>
            <?
			$tot_po_balance_arr[$buyer_id]+=$po_balance; 
			$tot_job_balance_arr[$jobNo]+=$tot_job_value_arr[$jobNo];
			
			//$tot_buyer_job_data_arr[$buyer_id]['fob']+=$tot_job_value_arr[$jobNo];
			
			$bc++;
            }
            ?>
            <td width="85" align="right" bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_po_balance_arr[$buyer_id],2); ?>&nbsp;&nbsp;&nbsp;</td>
            </tr>
            <tr>
           
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color; ?>" align="center"><b>CM/DZN($)</b></td>
            <?
			$cm=1;$tot_cm_dzn_arr=array();
            foreach($buyer_data as $jobNo => $row)
            {
				if($cm%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$fob_dzn_val=$tot_job_balance_arr[$jobNo]/$tot_job_qty_arr[$jobNo]*12;
				
				$tot_yarn=$tot_job_data_arr[$jobNo]['yarn'];
				$tot_trim=$tot_job_data_arr[$jobNo]['trim'];
				$tot_other=$tot_job_data_arr[$jobNo]['other'];
				$cm_dzn=$qoutCostArr[$buyer_id][$jobNo]['cm_dzn'];
				//$tot_other=$tot_job_data_arr[$jobNo]['other'];
				
				//$cm_dzn=$fob_dzn_val-($tot_yarn+$tot_trim+$tot_other);//
				//$cm_dzn=$pre_cost_mergin_arr[$jobNo];//
				// $tot_job_balance_arr[$jobNo]/$tot_job_qty_arr[$jobNo]*12;
            ?>
            <td width="75"  bgcolor="<? echo $bgcolor;?>" title="(<? echo $cm_dzn?>)"  align="right">
         	 <?  echo number_format($cm_dzn,2);?>
            </td>
            <?
			$tot_cm_dzn_arr[$buyer_id]+=$cm_dzn;
			//$tot_buyer_job_data_arr[$buyer_id]['other']+=$cm_dzn;
			
			$cm++;
            }
			$tot_job_cm_dzn=0;
			//$tot_buyer_job_data_arr[$buyer_id]['other'];
			
				$buyer_fob_dzn_val=$tot_po_value_arr[$buyer_id]/$tot_po_qty_arr[$buyer_id]*12;
				
				$tot_buyer_yarn=$tot_buyer_job_data_arr[$buyer_id]['yarn'];//$tot_buyer_job_data_arr[$buyer_id]['yarn']
				$tot_buyer_trim=$tot_buyer_job_data_arr[$buyer_id]['trim'];
				$tot_buyer_other=$tot_buyer_job_data_arr[$buyer_id]['other'];
				//echo $buyer_fob_dzn_val.'='.$tot_buyer_yarn.'='.$tot_buyer_trim.'='.$tot_buyer_other;
				//$tot_job_cm_dzn=$buyer_fob_dzn_val-($tot_buyer_yarn+$tot_buyer_trim+$tot_buyer_other); // previous formula
//cm_dzn
				$tot_job_cm_dzn=($tot_po_balance_arr[$buyer_id]/$tot_po_qty_arr[$buyer_id])*12
				
			//$tot_job_cm_dzn=$tot_po_balance_arr[$buyer_id]/$tot_po_qty_arr[$buyer_id]*12;
            ?>
            <td width="85" align="right" title="Cm Value/PO Qty Pcs*12" bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_job_cm_dzn,2); ?>&nbsp;&nbsp;&nbsp;</td>
            </tr>
        </tbody>
        </table>
        </div>
        </div>
         </td>
        <?
			}
		?>
       		
            </tr>
    </table>
    <br>
    
            </div>
        </div>
<?

	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****$reporttype"; 
    exit();
}


if($action=="report_generate_pre_cost")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$reporttype=str_replace("'","",$reporttype);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_style_owner=str_replace("'","",$cbo_style_owner);
	$style_ref_id=str_replace("'","",$txt_style_ref_id);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	if($cbo_style_owner==0) $style_owner_cond=""; else $style_owner_cond=" and a.style_owner='$cbo_style_owner' ";
	if($cbo_company_name==0) $company_name_cond=""; else $company_name_cond=" and a.company_name='$cbo_company_name' ";
	$job_style_cond="";
	if(trim(str_replace("'","",$txt_style_ref))!="")
	{
		if(str_replace("'","",$style_ref_id)!="")
		{
			$job_style_cond=" and a.id in(".str_replace("'","",$style_ref_id).")";
		}
		else
		{
			$job_style_cond=" and a.style_ref_no = '".trim(str_replace("'","",$txt_style_ref))."'";
		}
	}

	if(str_replace("'","",$cbo_buyer_name)==0)
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
		$buyer_id_cond=" and a.buyer_name in($cbo_buyer_name)";
	}
	$date_cond="";
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
			$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
		}
	
	
	$style_row=count($txt_style_ref);
	ob_start();
	
	/*$pre_sql="select a.costing_per,a.job_no,a.exchange_rate from wo_pre_cost_mst a where a.status_active=1 and a.is_deleted=0 ";
	$sql_pre_result=sql_select($pre_sql);
	foreach($sql_pre_result as $row)
	{
		$costing_per_arr[$row[csf("job_no")]]['cost_per']=$row[csf("costing_per")];
		$costing_per_arr[$row[csf("job_no")]]['ex_rate']=$row[csf("exchange_rate")];
	}*/
	
	$sql="select a.id,a.job_no_prefix_num as job_prefix,a.ship_mode,a.avg_unit_price, a.job_no, a.company_name, a.client_id,a.buyer_name, a.team_leader, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id as po_id, b.po_number,b.is_confirmed, b.pub_shipment_date,b.shipment_date,b.up_charge ,b.po_quantity,b.plan_cut,b.matrix_type, b.unit_price, b.po_total_price,b.status_active,c.item_number_id,c.plan_cut_qnty,c.order_quantity,c.order_total,c.color_number_id from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and c.po_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active=1  and c.is_deleted=0   $company_name_cond $job_style_cond $buyer_id_cond $date_cond  order  by b.pub_shipment_date";
	// echo $sql;die;
	$sql_po_result=sql_select($sql);
	$all_po_id="";$all_job="";$all_full_job="";$all_style="";$all_style_desc=""; $all_buyer=""; $all_buyer_client=""; $all_order_uom=""; 
	$order_qty_pcs=0;$total_fob_value=0;$total_order_qty=0;$total_unit_price=0;$total_job_unit_price=0;
	//echo $buyer_name;die;
	$po_numer_arr=$po_data_arr=array();$tot_count=0;$total_order_upcharge=0;$item_color_size_array=array();
	foreach($sql_po_result as $row)
	{
		if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
		if($all_job=="") $all_job=$row[csf("id")]; else $all_job.=",".$row[csf("id")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_no']=$row[csf("po_number")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['style_ref_no']=$row[csf("style_ref_no")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_qty_pcs']+=$row[csf("order_quantity")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['plan_cut']+=$row[csf("plan_cut")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['plan_cut_qnty']+=$row[csf("plan_cut_qnty")];
		

		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_value']+=$row[csf("order_total")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['avg_unit_price']=$row[csf("avg_unit_price")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_id'].=$row[csf("po_id")].",";
	
		$job_buyer_name_arr[$row[csf("job_no")]]['buyer_name']=$row[csf("buyer_name")];
		$item_color_size_array[$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]+=$row[csf("plan_cut_qnty")];
  } 
 // echo $all_po_id.'DDDDDDDD';
 //	print_r($buyer_job_arr);
  if($all_po_id=="") {echo "<div style='color:red; font-size:30px;' align='center'>No PO No Found </div>";die;}
	//echo $tot_count;
	//echo $all_po_id.'dsd';
		$all_job_no=array_unique(explode(",",$all_full_job));
		$all_jobs="";
		foreach($all_job_no as $jno)
		{
				if($all_jobs=="") $all_jobs=$jno; else $all_jobs.=",".$jno;
		}
		$all_po=array_unique(explode(",",$all_po_id));
		$all_poIDs=implode(",",array_unique(explode(",",$all_po_id)));
		$po_arr_cond=array_chunk($all_po,1000, true);
		$po_cond_for_in="";$po_cond_for_in2="";$po_cond_for_in3="";
		$pi=0;
		foreach($po_arr_cond as $key=>$value)
		{
		   if($pi==0)
		   {
			$po_cond_for_in=" and ( c.po_break_down_id  in(".implode(",",$value).")"; 
			$po_cond_for_in2=" and ( d.po_break_down_id  in(".implode(",",$value).")"; 
			
		   }
		   else //po_break_down_id
		   {
			$po_cond_for_in.=" or c.po_break_down_id  in(".implode(",",$value).")";
			$po_cond_for_in2.=" or d.po_break_down_id  in(".implode(",",$value).")";
			
		   }
		   $pi++;
		}	
		$po_cond_for_in.=" )";
		$po_cond_for_in2.=" )";
		
		$condition= new condition();
		$condition->company_name("=$cbo_company_name");
		if(str_replace("'","",$cbo_buyer_name)>0){
			  $condition->buyer_name("in($cbo_buyer_name)");
		 }
		 if($all_po_id!='' || $all_po_id!=0)
		 {
			$condition->po_id_in("$all_poIDs"); 
		 }
		 if(str_replace("'","",$txt_style_ref)!='')
		 {
			//echo "in($txt_order_id)".'dd';die;
			//$condition->job_no("in($all_jobs)");
		 }
		$condition->init();
		$costPerArr=$condition->getCostingPerArr();
		$emblishment= new emblishment($condition); 
	
		$wash= new wash($condition);
		$commercial= new commercial($condition);
		
	//	$trims_ReqQty_arr=$trims->getQtyArray_by_precostdtlsid();//getQtyArray_by_jobAndPrecostdtlsid();
		//print_r($trims_ReqQty_arr);
		$trims= new trims($condition);
		//echo $trims->getQuery(); die;
		//echo $emblishment->getQuery(); die;
		$trims_costing_arr=$trims->getAmountArray_by_order();//getAmountArray_by_jobAndPrecostdtlsid();
		
		$emblishment_costing_arr=$emblishment->getAmountArray_by_order();
		$wash_amount_arr=$wash->getAmountArray_by_jobEmbnameAndEmbtype();
		$commercial_costing_arr=$commercial->getAmountArray_by_order();
		$commission= new commision($condition);
		$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
		$yarn= new yarn($condition);
		//echo $yarn->getQuery(); die;
		$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
		$other= new other($condition);
		$other_costing_arr=$other->getAmountArray_by_order();
		
			
		$jobIds=chop($all_job,',');
		$prod_cond_for_in="";
		$prod_ids=count(array_unique(explode(",",$all_job)));
		if($db_type==2 && $prod_ids>1000)
		{
		$prod_cond_for_in=" and (";
		$prodIdsArr=array_chunk(explode(",",$jobIds),999);
		foreach($prodIdsArr as $ids)
		{
		$ids=implode(",",$ids);
		$prod_cond_for_in.=" a.job_id in($ids) or"; 
		}
		$prod_cond_for_in=chop($prod_cond_for_in,'or ');
		$prod_cond_for_in.=")";
		}
		else
		{
		$jobIds=implode(",",array_unique(explode(",",$jobIds)));
		$prod_cond_for_in=" and a.job_id in($jobIds)";
		}
	 $data_sql="select a.id, a.fabric_cost_dtls_id,b.costing_per, a.job_no,a.job_id, a.count_id, a.copm_one_id, a.percent_one, a.copm_two_id, a.percent_two, a.type_id, a.cons_ratio, a.cons_qnty, a.rate, a.amount, a.avg_cons_qnty, a.supplier_id, a.color, a.consdznlbs, a.rate_dzn, 
	b.item_number_id, b.body_part_id, b.fabric_description, b.color_type_id, b.uom, c.color_number_id, c.stripe_color, c.measurement 
	from wo_pre_cost_fab_yarn_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_pre_stripe_color c where a.fabric_cost_dtls_id=b.id and b.id=c.pre_cost_fabric_cost_dtls_id and a.color=c.stripe_color  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $prod_cond_for_in";
	//echo $data_sql; die;
	$result_data=sql_select($data_sql);
	//$lib_yarn_count=return_library_array( "select yarn_count, id from lib_yarn_count", "id", "yarn_count");
        $i=1; $totCons=0; $totReq=0; $totAmt=0;$amount_req=0;
        foreach ($result_data as $yarn)
        {
			//echo $job_id=$yarn[csf('job_id')];
			$costPerQty=$costPerArr[$yarn[csf("job_no")]];
			//echo $costPerQty.'dd';
			$job_buyer=$job_buyer_name_arr[$yarn[csf("job_no")]]['buyer_name'];
			$poQty=0; $yarn_req_kg=0; $yarn_req_lbs=0;
			$poQty=$item_color_size_array[$job_buyer][$yarn[csf("job_no")]][$yarn[csf('item_number_id')]][$yarn[csf('color_number_id')]];
            $yarn_req_kg=($yarn[csf('measurement')]/$costPerQty)*$poQty;
			$yarn_req_lbs=$yarn_req_kg*2.20462;
			
            $amount_req=$yarn_req_lbs*$yarn[csf('rate')];
			//echo $amount_req.'=='.$poQty.'<br>';
			if($amount_req>0)
			{
			$yarn_req_arr[$job_buyer][$yarn[csf('job_no')]]+= $amount_req;
			}
		}
		$financial_para=array();
		$sql_std_para=sql_select("select interest_expense,income_tax,cost_per_minute from lib_standard_cm_entry where company_id=$cbo_company_name and status_active=1 and	is_deleted=0 order by id");
		foreach($sql_std_para as $sql_std_row){
		$financial_para[interest_expense]=$sql_std_row[csf('interest_expense')];
		$financial_para[income_tax]=$sql_std_row[csf('income_tax')];
		$financial_para[cost_per_minute]=$sql_std_row[csf('cost_per_minute')];
		}
		unset($sql_std_para);

		$yarn_data =sql_select("select a.recv_number, a.receive_purpose, b.id, a.booking_id, a.receive_basis, b.pi_wo_batch_no,c.product_name_details, c.lot, b.order_uom, b.order_qnty, b.order_rate, b.order_ile_cost, b.order_amount, b.cons_amount, b.job_no, b.buyer_id, b.style_ref_no
		from inv_receive_master a, inv_transaction b, product_details_master c
		where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category=1 and a.entry_form=248 and b.status_active=1 and b.is_deleted=0 ");
		$yarn_rate_arr=array();
		
		foreach($yarn_data as $row){
			$yarn_rate_arr[$row[csf('job_no')]]=$row[csf('order_rate')];			 
		}
		unset($yarn_data);

			$trims_pro=sql_select("select id, trans_id, prod_id, item_group_id, item_description, item_description_color_size, brand_supplier, receive_qnty, rate, amount, reject_receive_qnty, gmts_color_id, gmts_size_id, item_size, order_uom, order_id, order_id_2, save_string, save_string_2, save_string_3, item_color, ile, ile_cost, book_keeping_curr, sensitivity, payment_over_recv,floor,room_no,rack_no,self_no,box_bin_no,remarks from inv_trims_entry_dtls");
			//echo $sql;
			$trims_qty_pro=array();
			$trims_rate_pro=array();
			foreach($trims_pro as $row){

				$trims_qty_pro[$row[csf('order_id')]] =$row[csf('receive_qnty')];
				$trims_rate_pro[$row[csf('order_id')]] =$row[csf('rate')];
			}

		

		
		$yarn_data_qty =sql_select("select a.company_id, a.issue_basis, b.id, b.supplier_id, b.cons_uom, b.cons_quantity, b.return_qnty, b.item_return_qty, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, b.dyeing_color_id, b.room, b.rack, b.self, b.floor_id, b.using_item, b.job_no, b.buyer_id, b.style_ref_no, c.allocated_qnty, c.available_qnty, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_type, c.color, c.brand, c.id as prod_id, b.booking_no, b.pi_wo_dtls_id, c.is_supp_comp 
		from inv_issue_master a, inv_transaction b, product_details_master c 
		where a.id=b.mst_id and b.prod_id=c.id  and b.transaction_type=2 and b.item_category=1");
		$yarn_qty_arr=array();
		
		foreach($yarn_data_qty as $row){

			$yarn_qty_arr[$row[csf('job_no')]]=$row[csf('cons_quantity')];			 
		}
		unset($yarn_data_qty);


		$commercial_data=sql_select("select job_no,amount from wo_actual_cost_entry where  cost_head='6' and status_active=1 and is_deleted=0");
		$commercial_cost_arr=array();
		foreach($commercial_data as $row){

			$commercial_cost_arr[$row[csf('job_no')]]=$row[csf('amount')];			 
		}
		unset($commercial_data);

		$style1="#E9F3FF"; 
		$style="#FFFFFF";
	?>
        <div style="width:100%; padding-left:0px;">
        <br><br><br> <br> 
           <?
          	$style1="#E9F3FF"; 
			$style="#FFFFFF";
			//echo count($buyer_job_arr);
			//print_r($buyer_job_arr);
			$imge_arr=return_library_array( "select master_tble_id,image_location from  common_photo_library where file_type=1 and form_name='knit_order_entry' ",'master_tble_id','image_location');
		   ?>
           <style>
	    table tr td { height:30px; font-size:14px;
		  
	   }
	   /*#outerdiv {
            position: absolute;
            top: 0;
            left: 0;
            right: 5em;
        }
        #innerdiv {
            width: 100%;
            overflow-x:scroll;
            margin-left: 5em;
            overflow-y:visible;
            padding-bottom:1px;
        }
        .headcol {
            position:absolute;
            width:5em;
            left:0;
            top:auto;
            border-right: 0px none black;
            border-top-width:3px;
            /*only relevant for first row
            margin-top:-3px;
            /*compensate for top border
        }
        .headcol:before {
            content:'Row ';
        }*/
        table#height_td tr th:first-child, table#height_td td:first-child{
		  position: sticky;
		  width: 100px;
		  left: 0;
		  z-index: 10;
		}
		table#height_td tr th:first-child{
		  z-index: 11;
		}
		table#height_td tr th{
		  position: sticky;
		  top: 0;
		  z-index: 9;
		}
		</style> 
         <div style="margin-left:10px">
          <table width="100%" style="margin-left:10px">
             <tr class="form_caption" style="font-size:24px;">
               <td align="center" width="100%" colspan="10" class="form_caption"><? echo $company_library[$cbo_company_name].'<br>';if($cbo_style_owner!=0) echo 'Style Owner: '.$company_library[$cbo_style_owner]; ?></td>
                    
                </tr>
                <tr>
                  <td colspan="10" style="font-size:24px" align="center"><strong>Style Wise Pre Cost Report</strong></td>
                </tr>
            </table>
            <table cellspacing="0" width="100%"  border="0">
            <tr valign="top">
       		<?
            $tot_po_qty=0;$tot_po_qty_arr=array();$tot_po_value_arr=array();$total_cost_arr=array();$other_cost_arr=array();$tot_plan_qty_arr=array();$tot_jobPlan_qty_arr=array();
			$tot_trims_cost_arr=array();
			$tot_trim_cal_arr=array();$tot_yarn_cal_arr=array();$tot_job_qty_arr=array();$tot_job_balance_arr=array();
         	 $width="";
			 $sub_yarn_cost=$sub_trims_cost=$other_cost=0;
			 
		    foreach($buyer_job_arr as $buyer_id => $buyer_data)
            {
				//echo count($buyer_data);
          	 $width=100*count($buyer_data)+350;
		    
			 $bg_color="#00CC66";
		    ?>
            <td>
            <div id="outerdiv">
    		<div id="innerdiv">
			<table cellspacing="0" width="<? echo $width ?>px"   border="1" rules="all" class="rpt_table" style="margin:0px;" >
            <thead align="center">
            <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Photo</b></td>
            <?
				 $m=1;
            foreach($buyer_data as $jobNo => $row)
            {
				 if($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 //echo $bgcolor.'dd';
            ?>
            <td width="100"  align="center" colspan="3" bgcolor="<? echo $bgcolor;?>">
            <?
            if($imge_arr[$jobNo]!="")
			{
			?>
            <img  src='../../../<? echo $imge_arr[$jobNo]; ?>' height='150' width='130' />
            <?
			}
			else "Photo";
			?>
            </td>
            <?
			$m++;
            }
            ?>
		
            <td width="100"  bgcolor="<? echo  $bg_color;?>"  align="center" rowspan="7"><b>Buyer Total</b></td>
			<td width="100"  bgcolor="<? echo  $bg_color;?>"  align="center" rowspan="7"><b>Buyer Total</b></td>
			<td width="100"  bgcolor="<? echo  $bg_color;?>" style="height:100%" align="center" rowspan="7"><b>Variance Total</b></td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Buyer Name</b></td>
                <?
                $b=1;
                foreach($buyer_data as $jobNo => $row)
                {
					 if($b%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<td width="75" bgcolor="<? echo $bgcolor;?>" colspan="3" align="center"><?  echo $buyer_arr[$buyer_id];?>&nbsp;</td>
					<?
					$b++;
                }
                ?>
                
            </tr>
            <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Style Ref</b></td>
            <?
			 $s=1;
          	foreach($buyer_data as $jobNo => $row)
            {
				 if($s%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
          	 <td width="75"  bgcolor="<? echo $bgcolor;?>" colspan="3" align="center"><p><?  echo $row['style_ref_no'];?></p></td>
            <?
			 $s++;
            }
            ?>
          
            </tr>
            <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Job No</b></td>
            <?
			$j=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
            <td width="75" bgcolor="<? echo $bgcolor;?>" colspan="3" align="center"> <p> <? echo $jobNo;?></p> </td>
            <?
			$j++;
            }
            ?>
           
            </tr>
            <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Avg.FOB/Pcs</b></td>
            <?
			$a=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($a%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
            <td width="75" colspan="3" bgcolor="<? echo $bgcolor;?>" align="center">$
         	 <?  echo $row['avg_unit_price'];?>
            </td>
            <?
			$a++;
            }
            ?>
           
            </tr>
            <tr>
             <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Order Qty.(Pcs)</b></td> 
            <?
			$o=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($o%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
            <td width="75" colspan="3" bgcolor="<? echo $bgcolor;?>"  align="center">
         	 <?  echo number_format($row['po_qty_pcs'],0);?>
            </td>
            <? 
			$tot_po_qty_arr[$buyer_id]+=$row['po_qty_pcs'];
			$tot_job_qty_arr[$jobNo]+=$row['po_qty_pcs'];
			$o++;
            }
            ?>
          
            </tr>
            
            <tr>
             <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Plan Knit Qty</b></td> 
            <?
			$o=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($o%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
           	 <td width="75" colspan="3" bgcolor="<? echo $bgcolor;?>"  align="center"> <?  echo number_format($row['plan_cut_qnty'],0);?> </td>
				<?
				$tot_plan_qty_arr[$buyer_id]+=$row['plan_cut_qnty'];
				$tot_jobPlan_qty_arr[$jobNo]+=$row['plan_cut_qnty'];
				$o++;
				}
           	 ?>
           
            </tr>
			<tr>
			
			<td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Comparision</b></td> 
				<?
					$o=1;
					foreach($buyer_data as $jobNo => $row)
					{
						if($o%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
						<td class="headcol" width="120" bgcolor="#FFFFFF" align="center"><b>Pre Cost</b></td> 
						<td class="headcol" width="120" bgcolor="#FFFFFF" align="center"><b>Post Cost</b></td> 
						<td class="headcol" width="120" bgcolor="#FFFFFF" align="center"><b>Varrience</b></td> 
				<?
			
				$o++;
				}
           	 ?>

				<td class="headcol" width="100" bgcolor="#FFFFFF" align="center"><b>Pre TOTAL</b></td> 
				<td class="headcol" width="100" bgcolor="#FFFFFF" align="center"><b>Post TOTAL</b></td> 
				<td class="headcol" width="100" bgcolor="#FFFFFF" align="center"><b>Variance TOTAL</b></td> 
			</tr>
			 <tr>
             <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Order Qty.(Pcs)</b></td> 
				<?
				$o=1;
				foreach($buyer_data as $jobNo => $row)
				{
					if($o%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<td width="120" bgcolor="#FFFFFF"  align="center">
				<?  echo number_format($row['po_qty_pcs'],0);?>
				</td>
				<td width="120" align="right"  bgcolor="#FFFFFF"><? echo number_format($row['plan_cut_qnty'],0);?>&nbsp;</td>
				<td width="90"  bgcolor="<? echo $bgcolor;?>"  align="center"> <?  echo $row['plan_cut_qnty']-$row['po_qty_pcs'];?> </td>
				<?
				$total_plan_qty_arr[$buyer_id]+=$row['plan_cut_qnty'];
			
	
				
				$total_po_qty_arr[$buyer_id]+=$row['po_qty_pcs'];
				$tot_dff_arr[$buyer_id]+=$total_plan_qty_arr[$buyer_id]-$total_po_qty_arr[$buyer_id];
				$o++;
				}
				?>
				
				<td width="120" align="right"  bgcolor="<? echo $bg_color;?>"><? echo number_format($total_po_qty_arr[$buyer_id],2); ?>&nbsp;</td>
				<td width="120" align="right"  bgcolor="<? echo $bg_color;?>"><? echo number_format($total_plan_qty_arr[$buyer_id],2); ?>&nbsp;</td>
				<td width="120" align="right"  bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_dff_arr[$buyer_id],2); ?>&nbsp;</td>
            </tr>
			<tr>
            <td class="headcol" width="120" bgcolor="<? echo  $bg_color;?>" align="center"><b>FOB Value</b></td>
            <?
			$po_val_cal=0;$f=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($f%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$po_val_cal=$row['po_value'];
            ?>
            <td width="120" bgcolor="<? echo $bgcolor;?>" align="right">$
         	 <?  echo number_format($row['po_value'],2);?>
            </td>
			<td class="headcol" width="120" bgcolor="#FFFFFF" align="center"><? $fob_cost=$row['plan_cut_qnty']*$row['avg_unit_price'];echo number_format($fob_cost,2);?> </td> 
				<td class="headcol" width="120" bgcolor="#FFFFFF" align="center"><?= number_format($fob_cost-$po_val_cal,2);?></td> 
            <?
			$tot_po_value_arr[$buyer_id]+=$row['po_value'];
			$tot_fob_cost_arr[$buyer_id]+=$fob_cost;
			$f++;
            }
            ?>
			
            <td width="120" align="center"  bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_po_value_arr[$buyer_id],2); ?>&nbsp;</td>
			<td width="120" align="center"  bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_fob_cost_arr[$buyer_id],2); ?>&nbsp;</td>
			<td width="120" align="center"  bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_fob_cost_arr[$buyer_id]-$tot_po_value_arr[$buyer_id],2); ?>&nbsp;</td>
            </tr>
           <tr>
            <td class="headcol" width="120" bgcolor="<? echo $bg_color;?>" align="center"><b>Yarn Cost</b></td>
            <?
			
			$y=1;$yarn_cost=0;
            foreach($buyer_data as $jobNo => $row)
            {
				if($y%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$poid=rtrim($row['po_id'],',');
				$poids=array_unique(explode(",",$poid));
				
				$po_id=rtrim($row['po_id'],',');
				$po_ids=implode(",",array_unique(explode(",",$po_id)));

				//
				foreach($poids as $poId )
				{
					//$yarn_cost+=$yarn_costing_arr[$poId];
					$sub_yarn_cost+=$yarn_costing_arr[$poId];
				}
				$yarn_cost=$yarn_req_arr[$buyer_id][$jobNo];
				if($sub_yarn_cost>0)
				{
				$tot_yarn_cal_arr[$jobNo]+=$yarn_cost;$sub_yarn_cost=0;
				}
				//$yarn_costing_arr;
				$pre_yarn_cost=$yarn_rate_arr[$jobNo]*$yarn_qty_arr[$jobNo];
            ?>
		
            <td width="120" bgcolor="<? echo $bgcolor;?>" align="right"  >$ <? echo number_format($yarn_cost,2); ?></td>
			<td class="headcol" width="120" bgcolor="#FFFFFF" align="center">$ <? echo  number_format($pre_yarn_cost,2); ?></td> 
			<td class="headcol" width="120" bgcolor="#FFFFFF" align="center">$ <? echo number_format($pre_yarn_cost-$yarn_cost,2); ?></td> 
            <?
			$y++;
			$tot_yarn_cost_arr[$buyer_id]+=$yarn_cost;
			$tot_pre_yarn_cost_arr[$buyer_id]+=$pre_yarn_cost;
            }
            ?>
            <td width="120" align="right" bgcolor="<? echo  $bg_color;?>"><? echo number_format($tot_yarn_cost_arr[$buyer_id],2); ?>&nbsp;</td>
			<td width="120" align="right" bgcolor="<? echo  $bg_color;?>"><? echo number_format($tot_pre_yarn_cost_arr[$buyer_id],2); ?>&nbsp;</td>
			<td width="120" align="right" bgcolor="<? echo  $bg_color;?>"><? echo number_format($tot_yarn_cost_arr[$buyer_id]-$tot_pre_yarn_cost_arr[$buyer_id],2); ?>&nbsp;</td>
            </tr>
            <tr>
            <td class="headcol" width="120" bgcolor="<? echo  $bg_color;?>" align="center"><b>Accessories Cost</b></td>
            <?
			$ac=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($ac%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$poid=rtrim($row['po_id'],',');
				$poids=array_unique(explode(",",$poid));
				$po_id=rtrim($row['po_id'],',');
				$po_ids=implode(",",array_unique(explode(",",$po_id)));
			
				$trims_cost=0;
				foreach($poids as $poId )
				{
					$trims_cost+=$trims_costing_arr[$poId];
					$sub_trims_cost+=$trims_costing_arr[$poId];
					$trim_pro=$trims_qty_pro[$poId];
					$trim_rate=$trims_rate_pro[$poId];
				}
				if($sub_trims_cost>0)
				{
				$tot_trim_cal_arr[$jobNo]+=$sub_trims_cost;$sub_trims_cost=0;
				}
            ?>
		
            <td width="120" bgcolor="<? echo $bgcolor;?>" align="right" >
         	$ <? echo number_format($trims_cost,2); ?>
            </td>
			<td class="headcol" width="120" bgcolor="#FFFFFF" align="center"><?=number_format($trim_pro*$trim_rate,2);?></td> 
				<td class="headcol" width="120" bgcolor="#FFFFFF" align="center">&nbsp;</td> 
            <?
			$tot_trims_cost_arr[$buyer_id]+=$trims_cost;
			$tot_pre_trims_cost_arr[$buyer_id]+=$trim_pro*$trim_rate;
			$tot_dff_trims_cost[$buyer_id]+=$tot_pre_trims_cost_arr[$buyer_id]-$tot_trims_cost_arr[$buyer_id];
			$ac++;
			
            }
            ?>
				
          	 	 <td width="120" align="right" bgcolor="<? echo  $bg_color;?>" >$<? echo number_format($tot_trims_cost_arr[$buyer_id],2); ?>&nbsp;</td>
				<td width="120" align="right" bgcolor="<? echo  $bg_color;?>" >$<? echo number_format($tot_pre_trims_cost_arr[$buyer_id],2); ?>&nbsp;</td>
				<td width="120" align="right" bgcolor="<? echo  $bg_color;?>" >$<? echo number_format($tot_dff_trims_cost[$buyer_id],2); ?>&nbsp;</td>
            </tr>
			<tr>
            <td class="headcol" width="120" bgcolor="<? echo  $bg_color;?>" align="center"><b>Commercial Cost</b></td>
            <?
			$ac=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($ac%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$poid=rtrim($row['po_id'],',');
				$poids=array_unique(explode(",",$poid));
				
				$po_id=rtrim($row['po_id'],',');
				$po_ids=implode(",",array_unique(explode(",",$po_id)));
				
				//other_popup
				foreach($poids as $poId )
				{
				$commercial_cost+=$commercial_costing_arr[$poId];
				}
				
            ?>
		
            <td width="120" bgcolor="<? echo $bgcolor;?>" align="right" >$<? echo number_format($commercial_cost,2); ?></td>
            <?
		

		?>
				<td class="headcol" width="120" bgcolor="#FFFFFF" align="center">$<?= number_format($commercial_cost_arr[$jobNo],2);?></td> 
				<td class="headcol" width="120" bgcolor="#FFFFFF" align="center">$<?=number_format($commercial_cost_arr[$jobNo]-$commercial_cost,2);?></td> <?

					$tot_commercial_cost_arr[$buyer_id]+=$commercial_cost;
					$tot_pre_commercial_cost_arr[$buyer_id]+=$commercial_cost_arr[$jobNo];

          	$ac++;  }
            ?>
			
         	  	 <td width="120" align="right" bgcolor="<? echo  $bg_color;?>" >$<? echo number_format($tot_commercial_cost_arr[$buyer_id],2); ?>&nbsp;</td>
				<td width="120" align="right" bgcolor="<? echo  $bg_color;?>" >$<? echo number_format($tot_pre_commercial_cost_arr[$buyer_id],2); ?>&nbsp;</td>
				<td width="120" align="right" bgcolor="<? echo  $bg_color;?>" >$<? echo number_format($tot_pre_commercial_cost_arr[$buyer_id]-$tot_commercial_cost_arr[$buyer_id],2); ?>&nbsp;</td>
            </tr>
			<tr>
            <td class="headcol" width="120" bgcolor="<? echo  $bg_color;?>" align="center"><b>Lab Test</b></td>
            <?
			$ac=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($ac%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$poid=rtrim($row['po_id'],',');
				$poids=array_unique(explode(",",$poid));
				
				$po_id=rtrim($row['po_id'],',');
				$po_ids=implode(",",array_unique(explode(",",$po_id)));
				
				//other_popup
				foreach($poids as $poId )
				{
					$testing_cost+=$other_costing_arr[$poId]['lab_test'];
					$currier_cost+=$other_costing_arr[$poId]['currier_pre_cost'];
				}
            ?>
		
            <td width="120" bgcolor="<? echo $bgcolor;?>" align="right" >$<? echo number_format($testing_cost,2); ?></td>
            <?
			$tot_testing_cost_arr[$buyer_id]+=$testing_cost;
			$ac++;?>
				<td class="headcol" width="120" bgcolor="#FFFFFF" align="center">$<?=number_format($testing_cost,2);?></td> 
				<td class="headcol" width="120" bgcolor="#FFFFFF" align="center">$<?=number_format($testing_cost-$testing_cost,2);?></td> <?
            }
            ?>
				
         	  	 <td width="120" align="right" bgcolor="<? echo  $bg_color;?>" >$<? echo number_format($tot_testing_cost_arr[$buyer_id],2); ?>&nbsp;</td>
				<td width="120" align="right" bgcolor="<? echo  $bg_color;?>" >$<? echo number_format($tot_testing_cost_arr[$buyer_id],2); ?>&nbsp;</td>
				<td width="120" align="right" bgcolor="<? echo  $bg_color;?>" >$<? echo number_format($tot_testing_cost_arr[$buyer_id]-$tot_testing_cost_arr[$buyer_id],2); ?>&nbsp;</td>
            </tr>
			<tr>
            <td class="headcol" width="120" bgcolor="<? echo  $bg_color;?>" align="center"><b>Courier Cost</b></td>
            <?
			$ac=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($ac%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$poid=rtrim($row['po_id'],',');
				$poids=array_unique(explode(",",$poid));
				
				$po_id=rtrim($row['po_id'],',');
				$po_ids=implode(",",array_unique(explode(",",$po_id)));
				$testing_cost=$inspection_cost=$certificate_cost=$common_oh=$currier_cost=$embl_cost=$cm_cost=$design_cost=$studio_cost=$commission_cost=$commercial_cost=$depr_amor_pre_cost=0;
				//other_popup
				foreach($poids as $poId )
				{
					$testing_cost+=$other_costing_arr[$poId]['lab_test'];
					$currier_cost+=$other_costing_arr[$poId]['currier_pre_cost'];
				}
				
							
				
				
            ?>
		
            <td width="120" bgcolor="<? echo $bgcolor;?>" align="right" >$<? echo number_format($currier_cost,2);?></td>
            <?
			$tot_currier_cost_arr[$buyer_id]+=$currier_cost;
			$ac++;?>
				<td class="headcol" width="120" bgcolor="#FFFFFF" align="center">$<?=number_format($currier_cost,2);?></td> 
				<td class="headcol" width="120" bgcolor="#FFFFFF" align="center">$<?=number_format($currier_cost-$currier_cost,2);?></td> <?
            }
            ?>
           	 <td width="120" align="right" bgcolor="<? echo  $bg_color;?>" ><? echo number_format($tot_currier_cost_arr[$buyer_id],2); ?>&nbsp;</td>
				<td width="120" align="right" bgcolor="<? echo  $bg_color;?>" ><? echo number_format($tot_currier_cost_arr[$buyer_id],2); ?>&nbsp;</td>
				<td width="120" align="right" bgcolor="<? echo  $bg_color;?>" ><? echo number_format($tot_currier_cost_arr[$buyer_id]-$tot_currier_cost_arr[$buyer_id],2); ?>&nbsp;</td>
            </tr>
			
             <tr>
            <td class="headcol" width="120" bgcolor="<? echo  $bg_color;?>" align="center"><b>Others Cost</b></td>
            <?
			$oc=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($oc%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$poid=rtrim($row['po_id'],',');
				$poids=array_unique(explode(",",$poid));
				
				$po_id=rtrim($row['po_id'],',');
				$po_ids=implode(",",array_unique(explode(",",$po_id)));
				$testing_cost=$inspection_cost=$certificate_cost=$common_oh=$currier_cost=$embl_cost=$cm_cost=$design_cost=$studio_cost=$commission_cost=$commercial_cost=$depr_amor_pre_cost=$freight_cost=0;
				//other_popup
				foreach($poids as $poId )
				{
					$testing_cost+=$other_costing_arr[$poId]['lab_test'];
					//echo $other_costing_arr[$poId]['common_oh'].'ddd';
					$freight_cost+=$other_costing_arr[$poId]['freight'];
					$inspection_cost+=$other_costing_arr[$poId]['inspection'];
					$certificate_cost+=$other_costing_arr[$poId]['certificate_pre_cost'];
					$common_oh+=$other_costing_arr[$poId]['common_oh'];
					$currier_cost+=$other_costing_arr[$poId]['currier_pre_cost'];
					$wash_cost=$emblishment_costing_arr_name[$poId][3];
					$embl_cost+=$emblishment_costing_arr[$poId]+$wash_cost;
					
					$cm_cost+=$other_costing_arr[$poId]['cm_cost'];
					$depr_amor_pre_cost+=$other_costing_arr[$poId]['depr_amor_pre_cost'];
					$design_cost+=$other_costing_arr[$poId]['design_cost'];
					$studio_cost+=$other_costing_arr[$poId]['studio_cost'];
					
					$commercial_cost+=$commercial_costing_arr[$poId];
                    $local=$commission_costing_arr[$poId][2];
					$foreign=$commission_costing_arr[$poId][1];
					$commission_cost+=$foreign+$local;
				}
				$tot_job_value=$tot_job_value_arr[$jobNo];
				$interest_cost=$tot_job_value*$financial_para[interest_expense]/100;
				$incometax_cost=$tot_job_value*$financial_para[income_tax]/100;
							
				$other_cost=$freight_cost+$inspection_cost+$certificate_cost+$common_oh+$commission_cost+$embl_cost+$cm_cost+$design_cost+$studio_cost+$interest_cost+$incometax_cost+$depr_amor_pre_cost;
				if($other_cost>0)
				{
				$tot_other_cal_arr[$jobNo]+=$other_cost;
				}
				
            ?>
			
            <td width="120" bgcolor="<? echo $bgcolor;?>"  align="right"  title="Freight+Inspection+Certificate+OH+Emblishment+Commission">$<? echo number_format($other_cost,2); ?>
            </td>
			<td class="headcol" width="120" bgcolor="#FFFFFF" align="center">$ <? echo number_format($other_cost,2); ?></td> 
				<td class="headcol" width="120" bgcolor="#FFFFFF" align="center">$ <? echo number_format($other_cost-$other_cost,2); ?></td> 
            <?
			$oc++;
			$other_cost_arr[$buyer_id]+=$other_cost;
            }
            ?>
            <td width="120" align="right" bgcolor="<? echo $bg_color;?>"><? echo number_format($other_cost_arr[$buyer_id],2); ?>&nbsp;</td>
			<td width="120" align="right" bgcolor="<? echo $bg_color;?>"><? echo number_format($other_cost_arr[$buyer_id],2); ?>&nbsp;</td>
			<td width="120" align="right" bgcolor="<? echo $bg_color;?>"><? echo number_format($other_cost_arr[$buyer_id]-$other_cost_arr[$buyer_id],2); ?>&nbsp;</td>
            </tr>
            <tr>
            <td class="headcol" width="120" bgcolor="<? echo  $bg_color;?>" align="center"><b>Total Cost</b></td>
            <?
			$total_cost_cal=0;$tc=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($tc%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$poid=rtrim($row['po_id'],',');
				$poids=array_unique(explode(",",$poid));
				
				$po_id=rtrim($row['po_id'],',');
				$po_ids=implode(",",array_unique(explode(",",$po_id)));
				foreach($poids as $poId )
				{
					$testing+=$other_costing_arr[$poId]['lab_test'];
					$currier+=$other_costing_arr[$poId]['currier_pre_cost'];
					$commercial+=$commercial_costing_arr[$poId];
					$trims+=$trims_costing_arr[$poId];
				}
				
				$total_cost=$tot_yarn_cost_arr[$buyer_id]+$tot_trims_cost_arr[$buyer_id]+$other_cost_arr[$buyer_id];
				$pre_yarn=$yarn_rate_arr[$jobNo]*$yarn_qty_arr[$jobNo];
				$total_cost_cal=$tot_trim_cal_arr[$jobNo]+$tot_other_cal_arr[$jobNo]+$tot_yarn_cal_arr[$jobNo];
				$total_cost_cal_post=$testing+$currier+$commercial+$yarn_cost+$trims+$other_cost;
				$total_cost_cal_pre=$testing+$currier+$commercial+$pre_yarn+$trims+$other_cost;
				
			$tot_cost_cal_arr[$jobNo]+=$total_cost_cal;	
            ?>
            <td width="120"  bgcolor="<? echo $bgcolor;?>" align="right">$
         	 <?  echo number_format($total_cost_cal_post,2);$total_cost_cal=0;?>
            </td>
			<td class="headcol" width="120" bgcolor="#FFFFFF" align="center"><?=number_format($total_cost_cal_pre,2);?></td> 
				<td class="headcol" width="120" bgcolor="#FFFFFF" align="center"><?=number_format($total_cost_cal_pre-$total_cost_cal_post,2);?></td> 
            <?
			$total_cost_arr[$buyer_id]+=$total_cost_cal_post;
			$total_pre_cost_arr[$buyer_id]+=$total_cost_cal_pre;
			$tc++;
			
            }
            ?>
		
            <td width="120" align="center" bgcolor="<? echo $bg_color;?>"><? echo number_format($total_cost_arr[$buyer_id],2); ?>&nbsp;</td>
			<td width="120" align="center" bgcolor="<? echo $bg_color;?>"><? echo number_format($total_pre_cost_arr[$buyer_id],2); ?>&nbsp;</td>
			<td width="120" align="center" bgcolor="<? echo $bg_color;?>"><? echo number_format($total_pre_cost_arr[$buyer_id]-$total_cost_arr[$buyer_id],2); ?>&nbsp;</td>
            </tr>
            <tr>
            <td class="headcol" width="120" bgcolor="<? echo  $bg_color;?>" align="center"><b>Total CM Value</b></td>
            <?
			$bc=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($bc%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$po_balance=$row['po_value']-$total_cost_cal_post;
				
				//$po_balance=$po_val_cal-$total_cost_cal;
				
            ?>
            <td width="120" bgcolor="<? echo $bgcolor;?>" align="right" title="Job Value-Total Cost">$
         	 <?  echo number_format($po_balance,2);
			  
			  $fob_cost=$row['plan_cut']*$row['avg_unit_price'];
			 ?>
            </td>
			<td class="headcol" width="120" bgcolor="#FFFFFF" align="center"><?=number_format($fob_cost-$total_cost_cal_pre,2);?></td> 
			<td class="headcol" width="120" bgcolor="#FFFFFF" align="center"><?=number_format($fob_cost-$total_cost_cal_pre-$po_balance,2);?></td> 
            <?
			$tot_po_balance_arr[$buyer_id]+=$po_balance;
			$tot_plan_balance_arr[$buyer_id]+=$fob_cost-$total_cost_cal_pre;
			$bc++;
            }
            ?>
		
            <td width="120" align="right" bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_po_balance_arr[$buyer_id],2); ?>&nbsp;</td>
			<td width="120" align="right" bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_plan_balance_arr[$buyer_id],2); ?>&nbsp;</td>
			<td width="120" align="right" bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_plan_balance_arr[$buyer_id]-$tot_po_balance_arr[$buyer_id],2); ?>&nbsp;</td>
            </tr>
            <tr>
           
            <td class="headcol" width="120" bgcolor="<? echo  $bg_color; ?>" align="center"><b>CM/DZN</b></td>
            <?
			$cm=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($cm%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$cm_dzn=$tot_job_balance_arr[$jobNo]/$tot_job_qty_arr[$jobNo]*12;
				$f_post=($row['po_value']/$row['po_qty_pcs'])*12;
				$s_post=($yarn_cost/$row['po_qty_pcs'])*12+($total_cost_cal_post/$row['po_qty_pcs'])*12;
				$f=($fob_cost/$row['plan_cut_qnty'])*12;
				$s=($pre_yarn/$row['plan_cut_qnty'])*12+($total_cost_cal_pre+$other_cost)/$row['plan_cut_qnty']*12;

				$post=$f_post-$s_post;
				$pre=$f-$s
            ?>
		
            <td width="120"  bgcolor="<? echo $bgcolor;?>" title="Job Balance/Job Qty Pcs*12"  align="right">$
         	 <?  echo number_format($f_post-$s_post,2);?>
            </td>
			<td class="headcol" width="120" bgcolor="#FFFFFF" align="center"><?=number_format($f-$s,2);?></td> 
			<td class="headcol" width="120" bgcolor="#FFFFFF" align="center"><?=number_format($pre-$post,2);?></td> 
            <?
			$tot_cm_dzn_value_arr[$buyer_id]+=$post;
			$tot_pre_cm_dzn_value_arr[$buyer_id]+=$pre;
			$cm++;
            }
			$tot_job_cm_dzn=0;
			$tot_job_cm_dzn=$tot_po_balance_arr[$buyer_id]/$tot_po_qty_arr[$buyer_id]*12;
            ?>
            <td width="120" align="right" title="Buyer Job Balance/Buyer Job Qty Pcs*12" bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_cm_dzn_value_arr[$buyer_id],2); ?>&nbsp;</td>
			<td width="120" align="right" title="Buyer Job Balance/Buyer Job Qty Pcs*12" bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_pre_cm_dzn_value_arr[$buyer_id],2); ?>&nbsp;</td>
			<td width="120" align="right" title="Buyer Job Balance/Buyer Job Qty Pcs*12" bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_pre_cm_dzn_value_arr[$buyer_id]-$tot_cm_dzn_value_arr[$buyer_id],2); ?>&nbsp;</td>
            </tr>
        </tbody>
        </table>
        </div>
        </div>
         </td>
        <?
			}
		?>
       		
            </tr>
    </table>
    <br>
    
            </div>
        </div>
 <?

	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****$reporttype"; 
    exit();
}

if($action=="report_generate_pq_vs_budged") // button => P.Q Vs Budged=> md mamun ahmed sagor => ISD=12502
{

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$reporttype=str_replace("'","",$reporttype);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_style_owner=str_replace("'","",$cbo_style_owner);
	$style_ref_id=str_replace("'","",$txt_style_ref_id);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	if($cbo_style_owner==0) $style_owner_cond=""; else $style_owner_cond=" and a.style_owner='$cbo_style_owner' ";
	if($cbo_company_name==0) $company_name_cond=""; else $company_name_cond=" and a.company_name='$cbo_company_name' ";
	$job_style_cond="";
	if(trim(str_replace("'","",$txt_style_ref))!="")
	{
		if(str_replace("'","",$style_ref_id)!="")
		{
			$job_style_cond=" and a.id in(".str_replace("'","",$style_ref_id).")";
		}
		else
		{
			$job_style_cond=" and a.style_ref_no = '".trim(str_replace("'","",$txt_style_ref))."'";
		}
	}

	if(str_replace("'","",$cbo_buyer_name)==0)
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
		$buyer_id_cond=" and a.buyer_name in($cbo_buyer_name)";
	}
	$date_cond="";
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
			$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
		}
	
	
	$style_row=count($txt_style_ref);
	ob_start();
	
	/*$pre_sql="select a.costing_per,a.job_no,a.exchange_rate from wo_pre_cost_mst a where a.status_active=1 and a.is_deleted=0 ";
	$sql_pre_result=sql_select($pre_sql);
	foreach($sql_pre_result as $row)
	{
		$costing_per_arr[$row[csf("job_no")]]['cost_per']=$row[csf("costing_per")];
		$costing_per_arr[$row[csf("job_no")]]['ex_rate']=$row[csf("exchange_rate")];
	}*/
	
	$sql="select a.id,a.job_no_prefix_num as job_prefix,a.ship_mode,a.avg_unit_price, a.job_no, a.company_name, a.client_id,a.buyer_name, a.team_leader, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id as po_id, b.po_number,b.is_confirmed, b.pub_shipment_date,b.shipment_date,b.up_charge ,b.po_quantity,b.plan_cut,b.matrix_type, b.unit_price, b.po_total_price,b.status_active,c.item_number_id,c.plan_cut_qnty,c.order_quantity,c.order_total,c.color_number_id from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and c.po_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active=1  and c.is_deleted=0   $company_name_cond $job_style_cond $buyer_id_cond $date_cond  order  by b.pub_shipment_date";
	// echo $sql;die;
	$sql_po_result=sql_select($sql);
	$all_po_id="";$all_job="";$all_full_job="";$all_style="";$all_style_desc=""; $all_buyer=""; $all_buyer_client=""; $all_order_uom=""; 
	$order_qty_pcs=0;$total_fob_value=0;$total_order_qty=0;$total_unit_price=0;$total_job_unit_price=0;
	//echo $buyer_name;die;
	$po_numer_arr=$po_data_arr=array();$tot_count=0;$total_order_upcharge=0;$item_color_size_array=array();
	foreach($sql_po_result as $row)
	{
		if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
		if($all_job=="") $all_job=$row[csf("id")]; else $all_job.=",".$row[csf("id")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_no']=$row[csf("po_number")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['style_ref_no']=$row[csf("style_ref_no")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['job_prefix']=$row[csf("job_prefix")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_qty_pcs']+=$row[csf("order_quantity")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['plan_cut']+=$row[csf("plan_cut")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['plan_cut_qnty']+=$row[csf("plan_cut_qnty")];
		

		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_value']+=$row[csf("order_total")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['avg_unit_price']=$row[csf("avg_unit_price")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_id'].=$row[csf("po_id")].",";
	
		$job_buyer_name_arr[$row[csf("job_no")]]['buyer_name']=$row[csf("buyer_name")];
		$item_color_size_array[$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]+=$row[csf("plan_cut_qnty")];
		$buyer_id_arr[$row[csf("buyer_name")]]=$row[csf("buyer_name")];
  } 
 // echo $all_po_id.'DDDDDDDD';
 //	print_r($buyer_job_arr);
 





		
		


		//=========================================================Price Qouatation=====================================================
		
		$pq_data=sql_select("select b.id,a.cost_sheet_no, b.mst_id, b.buyer_agent_id, b.location_id, b.no_of_pack, b.is_confirm, b.is_cm_calculative, b.mis_lumsum_cost, b.commision_per, b.tot_fab_cost, b.tot_sp_operation_cost, b.tot_wash_cost, b.tot_accessories_cost, b.tot_cm_cost, b.tot_fright_cost, b.tot_lab_test_cost, b.tot_miscellaneous_cost, b.tot_other_cost, b.commercial_cost, b.tot_commission_cost, b.tot_cost, b.tot_fob_cost, b.operating_exp, b.knitting_time, b.makeup_time, b.finishing_time,a.style_ref,a.buyer_id  from qc_mst a , qc_tot_cost_summary b where  b.mst_id=a.qc_no ".where_con_using_array($buyer_id_arr,1,'a.buyer_id')." and a.status_active=1 and a.is_deleted=0");
		foreach($pq_data as $val){

			$pq_cost_data[$val[csf('buyer_id')]][$val[csf('style_ref')]]['cm_cost']+=$val[csf('tot_cm_cost')];
			$pq_cost_data[$val[csf('buyer_id')]][$val[csf('style_ref')]]['accessories_cost']+=$val[csf('tot_accessories_cost')];	
			$pq_cost_data[$val[csf('buyer_id')]][$val[csf('style_ref')]]['fright_cost']+=$val[csf('tot_fright_cost')];
			$pq_cost_data[$val[csf('buyer_id')]][$val[csf('style_ref')]]['lab_test_cost']+=$val[csf('tot_lab_test_cost')];
			$pq_cost_data[$val[csf('buyer_id')]][$val[csf('style_ref')]]['commercial_cost']+=$val[csf('commercial_cost')];
			$pq_cost_data[$val[csf('buyer_id')]][$val[csf('style_ref')]]['commission_cost']+=$val[csf('tot_commission_cost')];
			$pq_cost_data[$val[csf('buyer_id')]][$val[csf('style_ref')]]['operating_exp']+=$val[csf('operating_exp')];
			$pq_cost_data[$val[csf('buyer_id')]][$val[csf('style_ref')]]['fob_dzn']+=$val[csf('tot_fob_cost')];
			$pq_cost_data[$val[csf('buyer_id')]][$val[csf('style_ref')]]['fabric_cost']+=$val[csf('tot_fab_cost')];
			$pq_cost_data[$val[csf('buyer_id')]][$val[csf('style_ref')]]['fob_pcs']+=$val[csf('tot_fob_cost')]/12;
			
			$pq_cost_data[$val[csf('buyer_id')]][$val[csf('style_ref')]]['qc_no']=$val[csf('mst_id')];
			$pq_cost_data[$val[csf('buyer_id')]][$val[csf('style_ref')]]['cost_sheet_no']=$val[csf('cost_sheet_no')];

	
			$pq_cost_data[$val[csf('buyer_id')]][$val[csf('style_ref')]]['others_cost']+=$val[csf('tot_other_cost')]+$val[csf('tot_fright_cost')]+$val[csf('tot_lab_test_cost')]+$val[csf('commercial_cost')]+$val[csf('tot_commission_cost')]+$val[csf('operating_exp')]+$val[csf('tot_cm_cost')]+$val[csf('tot_sp_operation_cost')]+$val[csf('tot_wash_cost')];
		}
	
		//=========================================================Pre Cost =====================================================
		
		$pre_data=sql_select("select b.id, fabric_cost, trims_cost,wash_cost,comm_cost,commission, lab_test,cm_cost, freight,common_oh,embel_cost,currier_pre_cost,certificate_pre_cost,price_pcs_or_set,  price_dzn, margin_dzn,margin_dzn_percent,deffdlc_cost,design_cost,studio_cost,common_oh,price_pcs_or_set, cost_pcs_set,inspection, margin_pcs_set,a.buyer_name,a.style_ref_no ,c.costing_date,a.job_no,a.company_name ,c.costing_per	 from wo_po_details_master a,wo_pre_cost_dtls b,wo_pre_cost_mst c where a.ID=b.job_id and c.job_id=a.id  ".where_con_using_array($buyer_id_arr,1,'a.buyer_name')." and b.status_active=1 and b.is_deleted=0");

		foreach($pre_data as $val){

			$pre_cost_data[$val[csf('buyer_name')]][$val[csf('style_ref_no')]]['company']=$val[csf('company_name')];
			$pre_cost_data[$val[csf('buyer_name')]][$val[csf('style_ref_no')]]['job_no']=$val[csf('job_no')];
			$pre_cost_data[$val[csf('buyer_name')]][$val[csf('style_ref_no')]]['buyer_name']=$val[csf('buyer_name')];
			$pre_cost_data[$val[csf('buyer_name')]][$val[csf('style_ref_no')]]['costing_date']=$val[csf('costing_date')];
			$pre_cost_data[$val[csf('buyer_name')]][$val[csf('style_ref_no')]]['costing_per']=$val[csf('costing_per')];
	

			$pre_cost_data[$val[csf('buyer_name')]][$val[csf('style_ref_no')]]['cm_cost']+=$val[csf('margin_dzn')];
			$pre_cost_data[$val[csf('buyer_name')]][$val[csf('style_ref_no')]]['cmm_cost']+=$val[csf('cm_cost')];
			$pre_cost_data[$val[csf('buyer_name')]][$val[csf('style_ref_no')]]['accessories_cost']+=$val[csf('trims_cost')];
			$pre_cost_data[$val[csf('buyer_name')]][$val[csf('style_ref_no')]]['other_cost']+=$val[csf('tot_other_cost')];
			$pre_cost_data[$val[csf('buyer_name')]][$val[csf('style_ref_no')]]['fright_cost']+=$val[csf('freight')];
			$pre_cost_data[$val[csf('buyer_name')]][$val[csf('style_ref_no')]]['lab_test_cost']+=$val[csf('lab_test')];
			$pre_cost_data[$val[csf('buyer_name')]][$val[csf('style_ref_no')]]['commercial_cost']+=$val[csf('comm_cost')];
			$pre_cost_data[$val[csf('buyer_name')]][$val[csf('style_ref_no')]]['commission_cost']+=$val[csf('commission')];
			$pre_cost_data[$val[csf('buyer_name')]][$val[csf('style_ref_no')]]['operating_exp']+=$val[csf('common_oh')];
			$pre_cost_data[$val[csf('buyer_name')]][$val[csf('style_ref_no')]]['fob_dzn']+=$val[csf('price_dzn')];
			$pre_cost_data[$val[csf('buyer_name')]][$val[csf('style_ref_no')]]['fabric_cost']+=$val[csf('fabric_cost')];
			$pre_cost_data[$val[csf('buyer_name')]][$val[csf('style_ref_no')]]['fob_pcs']+=$val[csf('price_pcs_or_set')];
			$pre_cost_data[$val[csf('buyer_name')]][$val[csf('style_ref_no')]]['others_cost']+=$val[csf('embel_cost')]+$val[csf('wash_cost')]+$val[csf('comm_cost')]+$val[csf('lab_test')]+$val[csf('inspection')]+$val[csf('freight')]+$val[csf('currier_pre_cost')]+$val[csf('certificate_pre_cost')]+$val[csf('deffdlc_cost')]+$val[csf('design_cost')]+$val[csf('studio_cost')]+$val[csf('common_oh')]+$val[csf('commission')]+$val[csf('cm_cost')];;
		}
		// echo "<pre>";
		// print_r($buyer_wise_pre_data);

	?>
        <div style="width:100%; padding-left:0px;">
        <br><br><br> <br> 
           <?
          	$style1="#E9F3FF"; 
			$style="#FFFFFF";
			//echo count($buyer_job_arr);
			//print_r($buyer_job_arr);
			$imge_arr=return_library_array( "select master_tble_id,image_location from  common_photo_library where file_type=1 and form_name='knit_order_entry' ",'master_tble_id','image_location');
		   ?>
           <style>
	    table tr td { height:30px; font-size:14px;
		  
	   }
	   /*#outerdiv {
            position: absolute;
            top: 0;
            left: 0;
            right: 5em;
        }
        #innerdiv {
            width: 100%;
            overflow-x:scroll;
            margin-left: 5em;
            overflow-y:visible;
            padding-bottom:1px;
        }
        .headcol {
            position:absolute;
            width:5em;
            left:0;
            top:auto;
            border-right: 0px none black;
            border-top-width:3px;
            /*only relevant for first row
            margin-top:-3px;
            /*compensate for top border
        }
        .headcol:before {
            content:'Row ';
        }*/
        table#height_td tr th:first-child, table#height_td td:first-child{
		  position: sticky;
		  width: 100px;
		  left: 0;
		  z-index: 10;
		}
		table#height_td tr th:first-child{
		  z-index: 11;
		}
		table#height_td tr th{
		  position: sticky;
		  top: 0;
		  z-index: 9;
		}
		</style> 
         <div style="margin-left:10px">
          <table width="100%" style="margin-left:10px">
             <tr class="form_caption" style="font-size:24px;">
               <td align="center" width="100%" colspan="10" class="form_caption"><? echo $company_library[$cbo_company_name].'<br>';if($cbo_style_owner!=0) echo 'Style Owner: '.$company_library[$cbo_style_owner]; ?></td>
                    
                </tr>
                <tr>
                  <td colspan="10" style="font-size:24px" align="center"><strong>Style Wise Pre Cost Report</strong></td>
                </tr>
            </table>
            <table cellspacing="0" width="100%"  border="0">
            <tr valign="top">
       		<?
            $tot_po_qty=0;$tot_po_qty_arr=array();$tot_po_value_arr=array();$total_cost_arr=array();$other_cost_arr=array();$tot_plan_qty_arr=array();$tot_jobPlan_qty_arr=array();
			$tot_trims_cost_arr=array();
			$tot_trim_cal_arr=array();$tot_yarn_cal_arr=array();$tot_job_qty_arr=array();$tot_job_balance_arr=array();
         	 $width="";
			 $sub_yarn_cost=$sub_trims_cost=$other_cost=0;
			 
		    foreach($buyer_job_arr as $buyer_id => $buyer_data)
            {
				
				//echo count($buyer_data);
          	 $width=120*count($buyer_data)+360;
		    
			 $bg_color="#00CC66";
		    ?>
            <td>
            <div id="outerdiv">
    		<div id="innerdiv">
			<table cellspacing="0" width="<? echo $width ?>px"   border="1" rules="all" class="rpt_table" style="margin:0px;" >
            <thead align="center">
            <tr>
            <td class="headcol" width="120" bgcolor="<? echo  $bg_color;?>" align="center"><b>Photo</b></td>
            <?
				 $m=1;
            foreach($buyer_data as $jobNo => $row)
            {
				 if($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 //echo $bgcolor.'dd';
            ?>
            <td width="120"  align="center" colspan="3" bgcolor="<? echo $bgcolor;?>">
            <?
            if($imge_arr[$jobNo]!="")
			{
			?>
            <img  src='../../../<? echo $imge_arr[$jobNo]; ?>' height='150' width='130' />
            <?
			}
			else "Photo";
			?>
            </td>
            <?
			$m++;
            }
            ?>
		
            <td width="120"  bgcolor="<? echo  $bg_color;?>"  align="center" rowspan="7"><b>Buyer Total</b></td>
			<td width="120"  bgcolor="<? echo  $bg_color;?>"  align="center" rowspan="7"><b>Buyer Total</b></td>
			<td width="120"  bgcolor="<? echo  $bg_color;?>" style="height:100%" align="center" rowspan="7"><b>Var. Total</b></td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="headcol" width="120" bgcolor="<? echo  $bg_color;?>" align="center"><b>Buyer Name</b></td>
                <?
                $b=1;
                foreach($buyer_data as $jobNo => $row)
                {
					 if($b%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<td width="120" bgcolor="<? echo $bgcolor;?>" colspan="3" align="center"><?  echo $buyer_arr[$buyer_id];?>&nbsp;</td>
					<?
					$b++;
                }
                ?>
                
            </tr>
            <tr>
            <td class="headcol" width="120" bgcolor="<? echo  $bg_color;?>" align="center"><b>Style Ref</b></td>
            <?
			 $s=1;
          	foreach($buyer_data as $jobNo => $row)
            {
				 if($s%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
          	 <td width="120"  bgcolor="<? echo $bgcolor;?>" colspan="3" align="center"><p><?  echo $row['style_ref_no'];?></p></td>
            <?
			 $s++;
            }
            ?>
          
            </tr>
          
          
            
          
			<tr>
			
			<td class="headcol" width="120" bgcolor="<? echo  $bg_color;?>" align="center"><b>Budget Stage</b></td> 
				<?
					$o=1;
					foreach($buyer_data as $jobNo => $row)
					{
						if($o%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<td class="headcol" width="120" bgcolor="#FFFFFF" align="center"><b>Short Quot. Cost</b></td> 
						<td class="headcol" width="120" bgcolor="#FFFFFF" align="center"><b>Pre Cost</b></td> 						
						<td class="headcol" width="120" bgcolor="#FFFFFF" align="center"><b>Var.</b></td> 
						<?	$o++;
					}
           			 ?>

				<td class="headcol" width="120" bgcolor="#FFFFFF" align="center"><b>Short Quot. Cost Total</b></td> 
				<td class="headcol" width="120" bgcolor="#FFFFFF" align="center"><b>Pre Total</b></td> 
				<td class="headcol" width="120" bgcolor="#FFFFFF" align="center"><b>Var. Total</b></td> 
			</tr>
			<tr>
					<td class="headcol" width="120" bgcolor="<? echo  $bg_color;?>" align="center"><b>Job No</b></td>
					<?
					$j=1;
					foreach($buyer_data as $jobNo => $row)
					{
						if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						
						$company=$pre_cost_data[$buyer_id][$row['style_ref_no']]['company'];
						$style_ref=$row['style_ref_no'];
						$job_no=$row['job_prefix'];
						$buyer_name=$pre_cost_data[$buyer_id][$row['style_ref_no']]['buyer_name'];
					?>
					
					<td width="120" bgcolor="<? echo $bgcolor;?>"  align="center"> <p> </p> </td>
					<td width="120" bgcolor="<? echo $bgcolor;?>"  align="center"> <p> <a href="##"  onclick="generate_report('<? echo $job_no;?>','<? echo $company;?>','<? echo $buyer_name;?>','<? echo $style_ref;?>','','','',0)" > <? echo $row['job_prefix'];?></a></p> </td>
					<td width="120" bgcolor="<? echo $bgcolor;?>"  align="center"> <p> </p> </td>
					<?
					$j++;
					}
					?>
           			<td width="120" bgcolor="<? echo $bgcolor;?>"  align="center"> <p> </p> </td>
					<td width="120" bgcolor="<? echo $bgcolor;?>"  align="center"> <p> </p> </td>
					<td width="120" bgcolor="<? echo $bgcolor;?>"  align="center"> <p> </p> </td>
            </tr>
			 
            <tr>
				<td class="headcol" width="120" bgcolor="<? echo  $bg_color;?>" align="center"><b>Avg.FOB/Pcs</b></td>
				<?
				$a=1;
				foreach($buyer_data as $jobNo => $row)
				{
					if($a%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";


					
		
				?>
				
				<td width="120"  bgcolor="<? echo $bgcolor;?>" align="right"><?  echo number_format($pq_cost_data[$buyer_id][$row['style_ref_no']]['fob_pcs'],2);?></td>
				<td width="120"  bgcolor="<? echo $bgcolor;?>" align="right"><?  echo number_format($row['avg_unit_price'],2);?></td>
				<td width="120"  bgcolor="<? echo $bgcolor;?>" align="right"><?  echo number_format($pq_cost_data[$buyer_id][$row['style_ref_no']]['fob_pcs']-$row['avg_unit_price'],2);;?></td>
				<?
				$a++;
				$buyer_priceQ_fob_pcs_tot[$buyer_id]+=$pq_cost_data[$buyer_id][$row['style_ref_no']]['fob_pcs'];
				$buyer_pre_fob_pcs_tot[$buyer_id]+=$row['avg_unit_price'];
				}
				?>
         	  	<td width="120"  bgcolor="<? echo $bgcolor;?>" align="right"><?  echo number_format($buyer_priceQ_fob_pcs_tot[$buyer_id],2);?></td>
				<td width="120"  bgcolor="<? echo $bgcolor;?>" align="right"><?  echo number_format($buyer_pre_fob_pcs_tot[$buyer_id],2);?></td>
				<td width="120"  bgcolor="<? echo $bgcolor;?>" align="right"><?  echo number_format($buyer_priceQ_fob_pcs_tot[$buyer_id]-$buyer_pre_fob_pcs_tot[$buyer_id],2);?></td>
            </tr>
			
			<tr>
            <td class="headcol" width="120" bgcolor="<? echo  $bg_color;?>" align="center"><b>FOB Value($)[DZN]</b></td>
            <?
			$po_val_cal=0;$f=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($f%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$po_val_cal=$row['po_value'];


				
            ?>
			<td class="headcol" width="120" bgcolor="#FFFFFF" align="right"><?  echo number_format($pq_cost_data[$buyer_id][$row['style_ref_no']]['fob_dzn'],2);?> </td> 
            <td width="120" bgcolor="<? echo $bgcolor;?>" align="right"><?  echo number_format($pre_cost_data[$buyer_id][$row['style_ref_no']]['fob_dzn'],2);?></td>
		
			<td class="headcol" width="120" bgcolor="#FFFFFF" align="right"><?= number_format($pq_cost_data[$buyer_id][$row['style_ref_no']]['fob_dzn']-$pre_cost_data[$buyer_id][$row['style_ref_no']]['fob_dzn'],2);?></td> 
            <?
			$buyer_priceQ_fob_dzn_tot[$buyer_id]+=$pq_cost_data[$buyer_id][$row['style_ref_no']]['fob_dzn'];
			$buyer_pre_fob_dzn_tot[$buyer_id]+=$pre_cost_data[$buyer_id][$row['style_ref_no']]['fob_dzn'];
			$f++;
            }
            ?>
			
			<td width="120" align="right"  bgcolor="<? echo $bg_color;?>"><? echo number_format($buyer_priceQ_fob_dzn_tot[$buyer_id],2); ?>&nbsp;</td>
			<td width="120" align="right"  bgcolor="<? echo $bg_color;?>"><? echo number_format($buyer_pre_fob_dzn_tot[$buyer_id],2); ?>&nbsp;</td>
			<td width="120" align="right"  bgcolor="<? echo $bg_color;?>"><? echo number_format($buyer_priceQ_fob_dzn_tot[$buyer_id]-$buyer_pre_fob_dzn_tot[$buyer_id],2); ?>&nbsp;</td>
            </tr>
           <tr>
            <td class="headcol" width="120" bgcolor="<? echo $bg_color;?>" align="center"><b>Yarn Cost</b></td>
            <?




			
			$y=1;$yarn_cost=0;
            foreach($buyer_data as $jobNo => $row)
            {
				if($y%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
            ?>
			<td class="headcol" width="120" bgcolor="#FFFFFF" align="right"> <? echo  number_format($pq_cost_data[$buyer_id][$row['style_ref_no']]['fabric_cost'],2); ?></td> 
            <td width="120" bgcolor="<? echo $bgcolor;?>" align="right"  ><? echo number_format($pre_cost_data[$buyer_id][$row['style_ref_no']]['fabric_cost'],2); ?></td>				
			<td class="headcol" width="120" bgcolor="#FFFFFF" align="right"><? echo number_format($pq_cost_data[$buyer_id][$row['style_ref_no']]['fabric_cost']-$pre_cost_data[$buyer_id][$row['style_ref_no']]['fabric_cost'],2); ?></td> 
            <?
			$y++;
			$tot_pre_yarn_cost_arr[$buyer_id]+=$pre_cost_data[$buyer_id][$row['style_ref_no']]['fabric_cost'];
			$tot_price_yarn_cost_arr[$buyer_id]+=$pq_cost_data[$buyer_id][$row['style_ref_no']]['fabric_cost'];
            }
            ?>
			<td width="120" align="right" bgcolor="<? echo  $bg_color;?>"><? echo number_format($tot_price_yarn_cost_arr[$buyer_id],2); ?>&nbsp;</td>
            <td width="120" align="right" bgcolor="<? echo  $bg_color;?>"><? echo number_format($tot_pre_yarn_cost_arr[$buyer_id],2); ?>&nbsp;</td>			
			<td width="120" align="right" bgcolor="<? echo  $bg_color;?>"><? echo number_format($tot_price_yarn_cost_arr[$buyer_id]-$tot_pre_yarn_cost_arr[$buyer_id],2); ?>&nbsp;</td>
            </tr>
            <tr>
            <td class="headcol" width="120" bgcolor="<? echo  $bg_color;?>" align="center"><b>Accessories Cost</b></td>
            <?


			$ac=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($ac%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
            ?>
		
           
			<td class="headcol" width="120" bgcolor="#FFFFFF" align="right"><?=number_format($pq_cost_data[$buyer_id][$row['style_ref_no']]['accessories_cost'],2);?></td> 
			<td width="120" bgcolor="<? echo $bgcolor;?>" align="right" ><? echo number_format($pre_cost_data[$buyer_id][$row['style_ref_no']]['accessories_cost'],2); ?></td>
			<td class="headcol" width="120" bgcolor="#FFFFFF" align="right">&nbsp;<?=number_format($pq_cost_data[$buyer_id][$row['style_ref_no']]['accessories_cost']-$pre_cost_data[$buyer_id][$row['style_ref_no']]['accessories_cost'],2); ?></td> 
            <?
			$tot_pre_trims_cost_arr[$buyer_id]+=$pre_cost_data[$buyer_id][$row['style_ref_no']]['accessories_cost'];
			$tot_price_trims_cost_arr[$buyer_id]+=$pq_cost_data[$buyer_id][$row['style_ref_no']]['accessories_cost'];
			$tot_dff_trims_cost[$buyer_id]+=$tot_price_trims_cost_arr[$buyer_id]-$tot_pre_trims_cost_arr[$buyer_id];
			$ac++;
			
            }
            ?>
				
          	 	 
				<td width="120" align="right" bgcolor="<? echo  $bg_color;?>" ><? echo number_format($tot_price_trims_cost_arr[$buyer_id],2); ?>&nbsp;</td>
				<td width="120" align="right" bgcolor="<? echo  $bg_color;?>" ><? echo number_format($tot_pre_trims_cost_arr[$buyer_id],2); ?>&nbsp;</td>
				<td width="120" align="right" bgcolor="<? echo  $bg_color;?>" ><? echo number_format($tot_dff_trims_cost[$buyer_id],2); ?>&nbsp;</td>
            </tr>
		
			
			
             <tr>
            <td class="headcol" width="120" bgcolor="<? echo  $bg_color;?>" align="center"><b>Others Cost</b></td>
            <?

			$oc=1;
            foreach($buyer_data as $jobNo => $row)
            {


				if($oc%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		
				
            ?>
			<td class="headcol" width="120" bgcolor="#FFFFFF" align="right"><? echo number_format($pq_cost_data[$buyer_id][$row['style_ref_no']]['others_cost'],2); ?></td> 
            <td width="120" bgcolor="<? echo $bgcolor;?>"  align="right"  ><? echo number_format($pre_cost_data[$buyer_id][$row['style_ref_no']]['others_cost'],2); ?> </td>	
			<td class="headcol" width="120" bgcolor="#FFFFFF" align="right"><? echo number_format($pq_cost_data[$buyer_id][$row['style_ref_no']]['others_cost']-$pre_cost_data[$buyer_id][$row['style_ref_no']]['others_cost'],2); ?></td> 
            <?
			$oc++;
			$other_pre_cost_arr[$buyer_id]+=$pre_cost_data[$buyer_id][$row['style_ref_no']]['others_cost'];
			$other_price_cost_arr[$buyer_id]+=$pq_cost_data[$buyer_id][$row['style_ref_no']]['others_cost'];
            }
            ?>
				<td width="120" align="right" bgcolor="<? echo $bg_color;?>"><? echo number_format($other_price_cost_arr[$buyer_id],2); ?>&nbsp;</td>
            <td width="120" align="right" bgcolor="<? echo $bg_color;?>"><? echo number_format($other_pre_cost_arr[$buyer_id],2); ?>&nbsp;</td>		
			<td width="120" align="right" bgcolor="<? echo $bg_color;?>"><? echo number_format($other_price_cost_arr[$buyer_id]-$other_pre_cost_arr[$buyer_id],2); ?>&nbsp;</td>
            </tr>
            <tr>
            <td class="headcol" width="120" bgcolor="<? echo  $bg_color;?>" align="center"><b>Total Cost</b></td>
            <?
			$total_cost_cal=0;$tc=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($tc%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				$total_price_cost+=$pq_cost_data[$buyer_id][$row['style_ref_no']]['fabric_cost']+$pq_cost_data[$buyer_id][$row['style_ref_no']]['accessories_cost']+$pq_cost_data[$buyer_id][$row['style_ref_no']]['others_cost'];
				$total_pre_cost+=$pre_cost_data[$buyer_id][$row['style_ref_no']]['fabric_cost']+$pre_cost_data[$buyer_id][$row['style_ref_no']]['accessories_cost']+$pre_cost_data[$buyer_id][$row['style_ref_no']]['others_cost'];
	
            ?>
			<td class="headcol" width="120" bgcolor="#FFFFFF" align="right"><?=number_format($total_price_cost,2);?></td> 
            <td width="120"  bgcolor="<? echo $bgcolor;?>" align="right"><?  echo number_format($total_pre_cost,2);$total_cost_cal=0;?>    </td>		
			<td class="headcol" width="120" bgcolor="#FFFFFF" align="right"><?=number_format($total_price_cost-$total_pre_cost,2);?></td> 
            <?
			$total_price_cost_arr[$buyer_id]+=$total_price_cost;
			$total_pre_cost_arr[$buyer_id]+=$total_pre_cost;
			$tc++;
			
            }
            ?>
			<td width="120" align="right" bgcolor="<? echo $bg_color;?>"><? echo number_format($total_price_cost_arr[$buyer_id],2); ?>&nbsp;</td>
            <td width="120" align="right" bgcolor="<? echo $bg_color;?>"><? echo number_format($total_pre_cost_arr[$buyer_id],2); ?>&nbsp;</td>		
			<td width="120" align="right" bgcolor="<? echo $bg_color;?>"><? echo number_format($total_price_cost_arr[$buyer_id]-$total_pre_cost_arr[$buyer_id],2); ?>&nbsp;</td>
            </tr>
          
            <tr>
           
            <td class="headcol" width="120" bgcolor="<? echo  $bg_color; ?>" align="center"><b>CM/DZN</b></td>
            <?
			$cm=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($cm%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				

			$qc_no=$pq_cost_data[$buyer_id][$row['style_ref_no']]['qc_no'];
			$cost_sheet_no=$pq_cost_data[$buyer_id][$row['style_ref_no']]['cost_sheet_no'];
			$report_val=$qc_no.'*'.$cost_sheet_no.'*Short Quotation [Sweater]';

			$company=$pre_cost_data[$buyer_id][$row['style_ref_no']]['company'];
			$style_ref=$row['style_ref_no'];
			$job_no=$pre_cost_data[$buyer_id][$row['style_ref_no']]['job_no'];
			$buyer_name=$pre_cost_data[$buyer_id][$row['style_ref_no']]['buyer_name'];
			$costing_date=$pre_cost_data[$buyer_id][$row['style_ref_no']]['costing_date'];
			$costing_per=$pre_cost_data[$buyer_id][$row['style_ref_no']]['costing_per'];
			
			
			
			
			

				
            ?>
			<td class="headcol" width="120" bgcolor="#FFFFFF" align="right"><a href="##"  onclick="fnc_print_report('<? echo $report_val;?>','quick_costing_print')" ><?=number_format($pq_cost_data[$buyer_id][$row['style_ref_no']]['cm_cost'],2);?></a></td> 
            <td width="120"  bgcolor="<? echo $bgcolor;?>" align="right"> <a href="##"  onclick="generate_report('<? echo $job_no;?>','<? echo $company;?>','<? echo $buyer_name;?>','<? echo $style_ref;?>','<? echo $costing_date;?>','','<? echo $costing_per;?>','preCostRpt2')" ><?  echo number_format($pre_cost_data[$buyer_id][$row['style_ref_no']]['cmm_cost'],2);?></a></td>
		
			<td class="headcol" width="120" bgcolor="#FFFFFF" align="right"><?=number_format($pq_cost_data[$buyer_id][$row['style_ref_no']]['cm_cost']-$pre_cost_data[$buyer_id][$row['style_ref_no']]['cmm_cost'],2);?></td> 
            <?
			$tot_cm_dzn_value_arr[$buyer_id]+=$pq_cost_data[$buyer_id][$row['style_ref_no']]['cm_cost'];
			$tot_pre_cm_dzn_value_arr[$buyer_id]+=$pre_cost_data[$buyer_id][$row['style_ref_no']]['cmm_cost'];
			$cm++;
            }
			$tot_job_cm_dzn=0;
			$tot_job_cm_dzn=$tot_po_balance_arr[$buyer_id]/$tot_po_qty_arr[$buyer_id]*12;
            ?>
            <td width="120" align="right"  bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_cm_dzn_value_arr[$buyer_id],2); ?>&nbsp;</td>	
			<td width="120" align="right"  bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_pre_cm_dzn_value_arr[$buyer_id],2); ?>&nbsp;</td>	
			<td width="120" align="right"   bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_cm_dzn_value_arr[$buyer_id]-$tot_pre_cm_dzn_value_arr[$buyer_id],2); ?>&nbsp;</td>
            </tr>
        </tbody>
        </table>
        </div>
        </div>
         </td>
        <?
			}
		?>
       		
            </tr>
    </table>
    <br>
    
            </div>
        </div>
 <?

	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****$reporttype"; 
    exit();
}

if($action=="report_generate_cm")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$reporttype=str_replace("'","",$reporttype);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_style_owner=str_replace("'","",$cbo_style_owner);
	$style_ref_id=str_replace("'","",$txt_style_ref_id);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	if($cbo_style_owner==0) $style_owner_cond=""; else $style_owner_cond=" and a.style_owner='$cbo_style_owner' ";
	if($cbo_company_name==0) $company_name_cond=""; else $company_name_cond=" and a.company_name='$cbo_company_name' ";
	$job_style_cond="";
	if(trim(str_replace("'","",$txt_style_ref))!="")
	{
		if(str_replace("'","",$style_ref_id)!="")
		{
			$job_style_cond=" and a.id in(".str_replace("'","",$style_ref_id).")";
		}
		else
		{
			$job_style_cond=" and a.style_ref_no = '".trim(str_replace("'","",$txt_style_ref))."'";
		}
	}

	if(str_replace("'","",$cbo_buyer_name)==0)
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
		$buyer_id_cond=" and a.buyer_name in($cbo_buyer_name)";
	}
	$date_cond="";
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
			$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
		}
	
	
	$style_row=count($txt_style_ref);
	ob_start();
	
	/*$pre_sql="select a.costing_per,a.job_no,a.exchange_rate from wo_pre_cost_mst a where a.status_active=1 and a.is_deleted=0 ";
	$sql_pre_result=sql_select($pre_sql);
	foreach($sql_pre_result as $row)
	{
		$costing_per_arr[$row[csf("job_no")]]['cost_per']=$row[csf("costing_per")];
		$costing_per_arr[$row[csf("job_no")]]['ex_rate']=$row[csf("exchange_rate")];
	}*/
	
	$sql="select a.id,a.gauge,a.job_no_prefix_num as job_prefix,a.avg_unit_price, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,a.total_set_qnty as ratio, b.id as po_id, b.po_number,b.is_confirmed, b.pub_shipment_date,b.plan_cut,b.unit_price, c.item_number_id,c.color_number_id,c.plan_cut_qnty,c.order_quantity,c.order_total from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and c.po_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active=1  and c.is_deleted=0   $company_name_cond $job_style_cond $buyer_id_cond $date_cond  order  by a.id";
	
	$sql_po_result=sql_select($sql);
	$all_po_id="";$all_job="";$all_full_job="";$all_style="";$all_style_desc=""; $all_buyer=""; $all_buyer_client=""; $all_order_uom=""; 
	$order_qty_pcs=0;$total_fob_value=0;$total_order_qty=0;$total_unit_price=0;$total_job_unit_price=0;
	//echo $buyer_name;die;
	$po_numer_arr=$po_data_arr=array();$tot_count=0;$total_order_upcharge=0;$item_color_size_array=array();
	foreach($sql_po_result as $row)
	{
		if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
		if($all_job=="") $all_job=$row[csf("id")]; else $all_job.=",".$row[csf("id")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_no']=$row[csf("po_number")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['gauge']=$row[csf("gauge")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['style_ref_no']=$row[csf("style_ref_no")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_qty_pcs']+=$row[csf("order_quantity")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['plan_cut']+=$row[csf("plan_cut")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_value']+=$row[csf("order_total")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['avg_unit_price']=$row[csf("avg_unit_price")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_id'].=$row[csf("po_id")].",";
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['pub_shipment_date'].=$row[csf("pub_shipment_date")].",";
	
		$job_id_arr[$row[csf("id")]]=$row[csf("id")];
		$job_buyer_name_arr[$row[csf("job_no")]]['buyer_name']=$row[csf("buyer_name")];
		$item_color_size_array[$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]+=$row[csf("plan_cut_qnty")];
		//echo $row[csf('buyer_name')].'='.$row[csf("job_no")].'='.$row[csf("item_number_id")].'='.$row[csf("color_number_id")].'<br>';
  } 
 // echo $all_po_id.'DDDDDDDD';
  if($all_po_id=="") {echo "<div style='color:red; font-size:30px;' align='center'>No PO No Found </div>";die;}
	//echo $tot_count;
	//echo $all_po_id.'dsd';
		$all_job_no=array_unique(explode(",",$all_full_job));
		$all_jobs="";
		foreach($all_job_no as $jno)
		{
				if($all_jobs=="") $all_jobs=$jno; else $all_jobs.=",".$jno;
		}
		$all_po=array_unique(explode(",",$all_po_id));
		$all_poIDs=implode(",",array_unique(explode(",",$all_po_id)));
		$po_arr_cond=array_chunk($all_po,1000, true);
		$po_cond_for_in="";$po_cond_for_in2="";$po_cond_for_in3="";
		$pi=0;
		foreach($po_arr_cond as $key=>$value)
		{
		   if($pi==0)
		   {
			$po_cond_for_in=" and ( c.po_break_down_id  in(".implode(",",$value).")"; 
			$po_cond_for_in2=" and ( d.po_break_down_id  in(".implode(",",$value).")"; 
			
		   }
		   else //po_break_down_id
		   {
			$po_cond_for_in.=" or c.po_break_down_id  in(".implode(",",$value).")";
			$po_cond_for_in2.=" or d.po_break_down_id  in(".implode(",",$value).")";
			
		   }
		   $pi++;
		}	
		$po_cond_for_in.=" )";
		$po_cond_for_in2.=" )";
		
		
		$condition= new condition();
		$condition->company_name("=$cbo_company_name");
		if(str_replace("'","",$cbo_buyer_name)>0){
			  $condition->buyer_name("in($cbo_buyer_name)");
		 }
		 if($all_po_id!='' || $all_po_id!=0)
		 {
			$condition->po_id_in("$all_poIDs"); 
		 }
		 if(str_replace("'","",$txt_style_ref)!='')
		 {
			//echo "in($txt_order_id)".'dd';die;
			//$condition->job_no("in($all_jobs)");
		 }
		$condition->init();
		$costPerArr=$condition->getCostingPerArr();
		$emblishment= new emblishment($condition); 
		$wash= new wash($condition);
		$commercial= new commercial($condition);
	//	$trims_ReqQty_arr=$trims->getQtyArray_by_precostdtlsid();//getQtyArray_by_jobAndPrecostdtlsid();
		//print_r($trims_ReqQty_arr);
		$trims= new trims($condition);
		//echo $trims->getQuery(); die;
		$trims_costing_arr=$trims->getAmountArray_by_order();//getAmountArray_by_jobAndPrecostdtlsid();
		$emblishment_costing_arr=$emblishment->getAmountArray_by_order();
		$wash_amount_arr=$wash->getAmountArray_by_jobEmbnameAndEmbtype();
		$commercial_costing_arr=$commercial->getAmountArray_by_order();
		$commission= new commision($condition);
		$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
		$other= new other($condition);
		//echo $other->getQuery(); die;
		$other_costing_arr=$other->getAmountArray_by_order();
		//print_r($other_costing_arr);
		$jobIds=chop($all_job,',');
		$prod_cond_for_in="";
		$prod_ids=count(array_unique(explode(",",$all_job)));
		if($db_type==2 && $prod_ids>1000)
		{
		$prod_cond_for_in=" and (";
		$prodIdsArr=array_chunk(explode(",",$jobIds),999);
		foreach($prodIdsArr as $ids)
		{
		$ids=implode(",",$ids);
		$prod_cond_for_in.=" b.job_id in($ids) or"; 
		}
		$prod_cond_for_in=chop($prod_cond_for_in,'or ');
		$prod_cond_for_in.=")";
		}
		else
		{
		$jobIds=implode(",",array_unique(explode(",",$jobIds)));
		$prod_cond_for_in=" and b.job_id in($jobIds)";
		}
	 $data_sql="select a.id,a.buyer_name,a.gauge, b.id as fab_dtls_id,b.costing_per, a.job_no,b.job_id, 
	b.item_number_id, b.body_part_id, b.fabric_description, b.color_type_id, b.uom 
	from wo_po_details_master a, wo_pre_cost_fabric_cost_dtls b where a.id=b.job_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $prod_cond_for_in"; 
	//echo $data_sql; die;
	$result_data=sql_select($data_sql);
	//$lib_yarn_count=return_library_array( "select yarn_count, id from lib_yarn_count", "id", "yarn_count");
        $i=1; $totCons=0; $totReq=0; $totAmt=0;$amount_req=0;
        foreach ($result_data as $yarn)
        {
			//echo $job_id=$yarn[csf('job_id')];
			$buyer_job_arr[$yarn[csf("buyer_name")]][$yarn[csf("job_no")]]['fabric_description'].=$yarn[csf("fabric_description")].',';
			//$buyer_job_arr[$yarn[csf("buyer_name")]][$yarn[csf("job_no")]]['cm_cost']=$other_costing_arr[]['cm_cost'];
		}
		$data_sql="select a.id, a.fabric_cost_dtls_id,b.costing_per, a.job_no,a.job_id, a.count_id, a.copm_one_id, a.percent_one, a.copm_two_id, a.percent_two, a.type_id, a.cons_ratio, a.cons_qnty, a.rate, a.amount, a.avg_cons_qnty, a.supplier_id, a.color, a.consdznlbs, a.rate_dzn, 
	b.item_number_id, b.body_part_id, b.fabric_description, b.color_type_id, b.uom, c.color_number_id, c.stripe_color, c.measurement 
	from wo_pre_cost_fab_yarn_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_pre_stripe_color c where a.fabric_cost_dtls_id=b.id and b.id=c.pre_cost_fabric_cost_dtls_id and a.color=c.stripe_color  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $prod_cond_for_in";
	// echo $data_sql; die;
	$result_data=sql_select($data_sql);
	//$lib_yarn_count=return_library_array( "select yarn_count, id from lib_yarn_count", "id", "yarn_count");
        $i=1; $totCons=0; $totReq=0; $totAmt=0;$amount_req=0;
        foreach ($result_data as $yarn)
        {
			//echo $job_id=$yarn[csf('job_id')];
			$costPerQty=$costPerArr[$yarn[csf("job_no")]];
			//echo $costPerQty.'dd';
			$job_buyer=$job_buyer_name_arr[$yarn[csf("job_no")]]['buyer_name'];
			$poQty=0; $yarn_req_kg=0; $yarn_req_lbs=0;
			$poQty=$item_color_size_array[$job_buyer][$yarn[csf("job_no")]][$yarn[csf('item_number_id')]][$yarn[csf('color_number_id')]];
			//echo $poQty.'=='.$costPerQty;
            $yarn_req_kg=($yarn[csf('measurement')]/$costPerQty)*$poQty;
			$yarn_req_lbs=$yarn_req_kg*2.20462;
			
            $amount_req=$yarn_req_lbs*$yarn[csf('rate')];
			//echo $amount_req.'=='.$poQty.'<br>';
			if($amount_req>0)
			{
			$yarn_req_arr[$job_buyer][$yarn[csf('job_no')]]+= $amount_req;
			}
		}
		//print_r($yarn_req_arr);
		$financial_para=array();
		$sql_std_para=sql_select("select interest_expense,income_tax,cost_per_minute from lib_standard_cm_entry where company_id=$cbo_company_name and status_active=1 and	is_deleted=0 order by id");
		foreach($sql_std_para as $sql_std_row){
		$financial_para[interest_expense]=$sql_std_row[csf('interest_expense')];
		$financial_para[income_tax]=$sql_std_row[csf('income_tax')];
		$financial_para[cost_per_minute]=$sql_std_row[csf('cost_per_minute')];
		}
		unset($sql_std_para);
		$sql_pre=sql_select("select job_no,margin_dzn from wo_pre_cost_dtls where  status_active=1 and	is_deleted=0  ".where_con_using_array($job_id_arr,0,'job_id')."  order by job_no");
		foreach($sql_pre as $row){
		$pre_cost_mergin_arr[$row[csf('job_no')]]=$row[csf('margin_dzn')];
		}
		
				$style1="#E9F3FF"; 
				$style="#FFFFFF";
		
		foreach($buyer_job_arr as $buyer_id=>$data_val)
		{
		$buyer_rowspan=0;
		 foreach($data_val as $job_no=>$val)
		  {
			  $buyer_rowspan++;
		  }
		  $buyer_wise_rowspan_arr[$buyer_id]=$buyer_rowspan;
		}
		//print_r($buyer_wise_rowspan_arr);
	?>
        <div style="width:100%; padding-left:0px;">
        <br><br><br> <br> 
           <?
          	$style1="#E9F3FF"; 
			$style="#FFFFFF";
			//echo count($buyer_job_arr);
			//print_r($buyer_job_arr);
			//$imge_arr=return_library_array( "select master_tble_id,image_location from  common_photo_library where file_type=1 and form_name='knit_order_entry' ",'master_tble_id','image_location');
			 $tbl_width="940";
		   ?>
           <style>
	    table tr td { height:30px; font-size:14px;
		  
	   }
	  
        table#height_td tr th:first-child, table#height_td td:first-child{
		  position: sticky;
		  width: 100px;
		  left: 0;
		  z-index: 10;
		}
		table#height_td tr th:first-child{
		  z-index: 11;
		}
		table#height_td tr th{
		  position: sticky;
		  top: 0;
		  z-index: 9;
		}
		</style> 
         <div style="margin:0 auto; width:<? echo $tbl_width+20;?>px;">
          <table width="100%">
             <tr class="form_caption" style="font-size:24px;">
               <td align="center" width="100%" colspan="12" style="font-size:24px" class="form_caption"><? echo $company_library[$cbo_company_name].'<br>';if($cbo_style_owner!=0) echo 'Style Owner: '.$company_library[$cbo_style_owner]; ?></td>
                    
                </tr>
                <tr>
                  <td colspan="12" style="font-size:24px" align="center"><strong>Style Wise CM Report</strong></td>
                </tr>
            </table>
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" align="left" cellspacing="0" border="1" rules="all" id="table_header_1">
             <thead>
                 <tr style="font-size:18px">
                     <th width="20">SL</th>
                     <th width="100">Buyer</th>
                     <th width="100">Style No</th>
                     <th width="90">Job No</th>
                     <th width="40">GG</th>
                     <th width="120">Yarn</th>
                     <th width="70">Qty(Pcs)</th>
                     <th width="40">Unit Price</th>
                     <th width="100">Total FOB</th>
                     <th width="65">CM/ Dzn</th>
                     <th width="100">Total CM</th>
                     <th width="">Shipment Date</th>
            	 </tr>
             </thead>
   			 </table>
             
              <div  class="scroll_div_inner" style="width:<? echo $tbl_width+18;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
              <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" align="left" cellspacing="0" border="1" rules="all" id="table_body">
            	
            <?
           				$k=1;
						$tot_order_qty=$tot_po_value=$tot_cm_value=0;
						
						foreach($buyer_job_arr as $buyer_id=>$data_val)
						{
							$m=1;
						 foreach($data_val as $job_no=>$val)
						  {
							if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$ratio=$val["ratio"];
							$poids=rtrim($val["po_id"],',');
							$po_ids=array_unique(explode(",",$poids));
							
							//$poid=rtrim($row['po_id'],',');
							//$poids=array_unique(explode(",",$poid));
							$testing_cost=$inspection_cost=$certificate_cost=$design_cost=$studio_cost=$interest_cost=$cm_cost=$incometax_cost=$common_oh=$currier_cost=$embl_cost=$commission_cost=$commercial_cost=$trims_cost=$depr_amor_pre_cost=0;
							//other_popup
							foreach($po_ids as $poId )
							{
								$testing_cost+=$other_costing_arr[$poId]['lab_test'];
								//echo $other_costing_arr[$poId]['common_oh'].'ddd';
								$freight_cost+=$other_costing_arr[$poId]['freight'];
								$inspection_cost+=$other_costing_arr[$poId]['inspection'];
								
								$cm_cost+=$other_costing_arr[$poId]['cm_cost'];
								$design_cost+=$other_costing_arr[$poId]['design_cost'];
								$studio_cost+=$other_costing_arr[$poId]['studio_cost'];
								$depr_amor_pre_cost+=$other_costing_arr[$poId]['depr_amor_pre_cost'];
							//	$incometax_cost+=$other_costing_arr[$poId]['incometax_cost'];
								
								$certificate_cost+=$other_costing_arr[$poId]['certificate_pre_cost'];
								$common_oh+=$other_costing_arr[$poId]['common_oh'];
								$currier_cost+=$other_costing_arr[$poId]['currier_pre_cost'];
								$wash_cost=$emblishment_costing_arr_name[$poId][3];
								$embl_cost+=$emblishment_costing_arr[$poId]+$wash_cost;
								
								$commercial_cost+=$commercial_costing_arr[$poId];
								$local=$commission_costing_arr[$poId][2];
								$foreign=$commission_costing_arr[$poId][1];
								$commission_cost+=$foreign+$local;
								$trims_cost+=$trims_costing_arr[$poId];
							}
							//echo $incometax_cost.'='.$interest_cost.'<br>';
								
				
							
							
							$yarn_req_amt=$yarn_req_arr[$buyer_id][$job_no];
							$tot_po_qnty=$val["po_qty_pcs"];
							$pub_shipment_date=rtrim($val["pub_shipment_date"],',');
							$pub_shipment_dates=array_unique(explode(",",$pub_shipment_date));
							$pub_shipmentDate=min($pub_shipment_dates);
							
							$fabric_description=rtrim($val["fabric_description"],',');
							$fabric_desc=implode(",",array_unique(explode(",",$fabric_description)));
							
							$plan_cut_qty=$val["plan_cut"];
							$po_value=$val["po_value"];
							
							//$buyer_wise_rowspan=$buyer_wise_rowspan_arr[$buyer_id];
							//$job_no=$val["job_no"];
							$dzn_qnty=0; $balance=0; $job_mkt_required=0; $yarn_issued=0;
							if($costing_per_id_library[$job_no]==1) $dzn_qnty=12;
							else if($costing_per_id_library[$job_no]==3) $dzn_qnty=12*2;
							else if($costing_per_id_library[$job_no]==4) $dzn_qnty=12*3;
							else if($costing_per_id_library[$job_no]==5) $dzn_qnty=12*4;
							else $dzn_qnty=1;
							$dzn_qnty=$dzn_qnty*$ratio;
							
							//$interest_expense=$po_value*$financial_para[interest_expense]/100;
						///	$income_tax=$po_value*$financial_para[income_tax]/100;
						//echo $financial_para[interest_expense].'<br>';
							$interest_cost=$po_value*$financial_para[interest_expense]/100;
							$incometax_cost=$po_value*$financial_para[income_tax]/100;
							
							$tot_other_cost=$testing_cost+$freight_cost+$inspection_cost+$certificate_cost+$common_oh+$currier_cost+$commercial_cost+$commission_cost+$embl_cost+$cm_cost+$design_cost+$studio_cost+$interest_cost+$incometax_cost+$depr_amor_pre_cost;;
							
						$total_cost=$tot_other_cost+$trims_cost+$yarn_req_amt;
						 $tot_cm_cost=$po_value-$total_cost;
						  $tot_cm_cost_dzn=$pre_cost_mergin_arr[$job_no];
						// $tot_cm_cost_dzn=($tot_cm_cost/$tot_po_qnty)*12;
						
					
								
							/*$tot_cm_cost=0;
							foreach($po_ids as $poID)
							{
							$tot_cm_cost+=$other_costing_arr[$poID]['cm_cost'];
							}*/
							?>
                         
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>" style="font-size:18px">
								<?
                                if($m==1)
								{
								?>
                                <td width="20" valign="top" rowspan="<? echo $buyer_wise_rowspan_arr[$buyer_id];?>"><? echo $k; ?></td>
								<td width="100" valign="top" rowspan="<? echo $buyer_wise_rowspan_arr[$buyer_id];?>"><div style="word-wrap:break-word; width:100px"><b><? echo $buyer_arr[$buyer_id]; ?></b></div></td>
                                <?
								}
								?>
								<td width="100" align="center"><div style="word-wrap:break-word; width:100px"></b><? echo $val["style_ref_no"]; ?>&nbsp;</b></div></td>
								<td width="90" align="center"><div style="word-wrap:break-word; width:90px"><b><? echo $job_no; ?>&nbsp;</b></div></td>
								<td width="40"><div style="word-wrap:break-word; width:40px"><b><? echo $gauge_arr[$val["gauge"]]; ?></b></div></td>
                               
								<td width="120"><div style="word-wrap:break-word; width:120px"><? echo $fabric_desc; ?></div></td>
								<td width="70" align="right" bgcolor="#FFFFCC"><p><? echo number_format($val["po_qty_pcs"],0); ?></p></td>
								<td width="40" align="right" ><p><?  echo number_format($val["avg_unit_price"],2); ?></p></td>
								<td width="100" align="right" ><div style="word-wrap:break-word; width:100px"><? echo number_format($val["po_value"],2); ?></div></td>
                                <td width="65" align="right"  title="Tot CM/PO Qty*12"><div style="word-wrap:break-word; width:65px"><? echo number_format($tot_cm_cost_dzn,2); ?></div></td>
								<td width="100" align="right" title="Po Value(<? echo $po_value;?>)-Tot Cost(<? echo $total_cost;?>)"><p><? echo number_format($tot_cm_cost,2); ?></p></td>
                                <td width=""><p><? if(trim($pub_shipmentDate)!="" && trim($pub_shipmentDate)!='0000-00-00') echo change_date_format($pub_shipmentDate); ?>&nbsp;</p></td>
                           </tr>
                            <?
							$m++;
							$tot_order_qty+=$val["po_qty_pcs"];
							$tot_po_value+=$val["po_value"];
							$tot_cm_value+=$tot_cm_cost;
						  }
						  $k++;
						}
			?>
           			 
            	</table>
             </div>
             <table width="<? echo $tbl_width;?>" align="left" class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr style="font-size:18px">
                    
                     <td width="20"></td>
                     <td width="100"></td>
                     <td width="100"></td>
                     <td width="90"></td>
                     <td width="40"></td>
                     <td width="120"><b>Total</b></td>
                    <td width="70" align="right"><? echo number_format($tot_order_qty,0); ?></td>
                    <td width="40">&nbsp;</td>
                    <td width="100" align="right"><? echo number_format($tot_po_value,2); ?></td>
                    <td width="65">&nbsp;</td>
                     <td width="100" align="right"><? echo number_format($tot_cm_value,2); ?></td>
                    <td width="">&nbsp;</td>
                </tr>
           </table>
        </div>
 <?

	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****$reporttype"; 
    exit();
}

if($action=="report_generate_obs_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$reporttype=str_replace("'","",$reporttype);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_style_owner=str_replace("'","",$cbo_style_owner);
	$style_ref_id=str_replace("'","",$txt_style_ref_id);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	if($cbo_style_owner==0) $style_owner_cond=""; else $style_owner_cond=" and a.style_owner='$cbo_style_owner' ";
	if($cbo_company_name==0) $company_name_cond=""; else $company_name_cond=" and a.company_name='$cbo_company_name' ";
	$job_style_cond="";
	if(trim(str_replace("'","",$txt_style_ref))!="")
	{
		if(str_replace("'","",$style_ref_id)!="")
		{
			$job_style_cond=" and a.id in(".str_replace("'","",$style_ref_id).")";
		}
		else
		{
			$job_style_cond=" and a.style_ref_no = '".trim(str_replace("'","",$txt_style_ref))."'";
		}
	}

	if(str_replace("'","",$cbo_buyer_name)==0)
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
		$buyer_id_cond=" and a.buyer_name in($cbo_buyer_name)";
	}
	$date_cond="";
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
			$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
		}
	
	
	$style_row=count($txt_style_ref);
	ob_start();

	$cm_cost_sql="select a.id,a.gauge,a.job_no_prefix_num as job_prefix,a.avg_unit_price, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,a.total_set_qnty as ratio, b.id as po_id, b.po_number,b.is_confirmed, b.pub_shipment_date,b.plan_cut,b.unit_price, c.item_number_id,c.color_number_id,c.plan_cut_qnty,c.order_quantity,c.order_total,d.cm_cost,a.set_smv, e.particular_id, e.cost
	from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_dtls d, wo_pre_cost_cm_cost_dtls e
	where a.id=b.job_id and a.id=c.job_id and c.po_break_down_id=b.id and d.job_no =a.job_no and d.job_no = e.job_no and e.particular_id in (1,3,8,9,11,12)
	and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active=1  and c.is_deleted=0   $company_name_cond $job_style_cond $buyer_id_cond $date_cond  
	order  by a.id";

	$sql_cm_cost_result=sql_select($cm_cost_sql);
    $cm_cost_arr=array();
	foreach($sql_cm_cost_result as $row)
	{
		$cm_cost_arr[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("particular_id")]]=$row[csf("cost")];
		// $cm_cost_arr[$row[csf("job_no")]]['cost_per']=$row[csf("costing_per")];
		// $cm_cost_arr[$row[csf("job_no")]]['ex_rate']=$row[csf("exchange_rate")];
	}
	
    // echo "<pre>";
    // print_r($cm_cost_arr);

	/*$pre_sql="select a.costing_per,a.job_no,a.exchange_rate from wo_pre_cost_mst a where a.status_active=1 and a.is_deleted=0 ";
	$sql_pre_result=sql_select($pre_sql);
	foreach($sql_pre_result as $row)
	{
		$costing_per_arr[$row[csf("job_no")]]['cost_per']=$row[csf("costing_per")];
		$costing_per_arr[$row[csf("job_no")]]['ex_rate']=$row[csf("exchange_rate")];
	}*/
	
	$sql="select a.id,a.gauge,a.job_no_prefix_num as job_prefix,a.avg_unit_price, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,a.total_set_qnty as ratio, b.id as po_id, b.po_number,b.is_confirmed, b.pub_shipment_date,b.plan_cut,b.unit_price, c.item_number_id,c.color_number_id,c.plan_cut_qnty,c.order_quantity,c.order_total,d.cm_cost,a.set_smv, e.particular_id, e.cost,a.job_quantity
	from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_dtls d, wo_pre_cost_cm_cost_dtls e
	where a.id=b.job_id and a.id=c.job_id and c.po_break_down_id=b.id and d.job_no =a.job_no and d.job_no = e.job_no and e.particular_id in (1,3,8,9,11,12)
	and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active=1  and c.is_deleted=0   $company_name_cond $job_style_cond $buyer_id_cond $date_cond  
	order  by a.id";
	// echo "$sql";
	$sql_po_result=sql_select($sql);
	$all_po_id="";$all_job="";$all_full_job="";$all_style="";$all_style_desc=""; $all_buyer=""; $all_buyer_client=""; $all_order_uom=""; 
	$order_qty_pcs=0;$total_fob_value=0;$total_order_qty=0;$total_unit_price=0;$total_job_unit_price=0;
	//echo $buyer_name;die;
	$po_numer_arr=$po_data_arr=array();$tot_count=0;$total_order_upcharge=0;$item_color_size_array=array();
	foreach($sql_po_result as $row)
	{
		if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
		if($all_job=="") $all_job=$row[csf("id")]; else $all_job.=",".$row[csf("id")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_no']=$row[csf("po_number")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['gauge']=$row[csf("gauge")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['style_ref_no']=$row[csf("style_ref_no")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_qty_pcs']+=$row[csf("order_quantity")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['plan_cut']+=$row[csf("plan_cut")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_value']+=$row[csf("order_total")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['avg_unit_price']=$row[csf("avg_unit_price")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_id'].=$row[csf("po_id")].",";
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['pub_shipment_date'].=$row[csf("pub_shipment_date")].",";
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['cm_cost']=$row[csf("cm_cost")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['set_smv']=$row[csf("set_smv")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['job_quantity']=$row[csf("job_quantity")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['knitting_cm']=$cm_cost_arr[$row[csf("buyer_name")]][$row[csf("job_no")]][1];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['linking_cm']=$cm_cost_arr[$row[csf("buyer_name")]][$row[csf("job_no")]][3];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['trimming_cm']=$cm_cost_arr[$row[csf("buyer_name")]][$row[csf("job_no")]][9];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['mending_cm']=$cm_cost_arr[$row[csf("buyer_name")]][$row[csf("job_no")]][8];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['finishing_cm']=$cm_cost_arr[$row[csf("buyer_name")]][$row[csf("job_no")]][11];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['fixed_cost_cm']=$cm_cost_arr[$row[csf("buyer_name")]][$row[csf("job_no")]][12];
	
		$job_buyer_name_arr[$row[csf("job_no")]]['buyer_name']=$row[csf("buyer_name")];
		$item_color_size_array[$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]+=$row[csf("plan_cut_qnty")];
		//echo $row[csf('buyer_name')].'='.$row[csf("job_no")].'='.$row[csf("item_number_id")].'='.$row[csf("color_number_id")].'<br>';
    } 
	//   echo "<pre>";
	//   print_r($buyer_job_arr);

   // echo $all_po_id.'DDDDDDDD';
  if($all_po_id=="") {echo "<div style='color:red; font-size:30px;' align='center'>No PO No Found </div>";die;}
	//echo $tot_count;
	//echo $all_po_id.'dsd';
		$all_job_no=array_unique(explode(",",$all_full_job));
		$all_jobs="";
		foreach($all_job_no as $jno)
		{
				if($all_jobs=="") $all_jobs=$jno; else $all_jobs.=",".$jno;
		}
		$all_po=array_unique(explode(",",$all_po_id));
		$all_poIDs=implode(",",array_unique(explode(",",$all_po_id)));
		$po_arr_cond=array_chunk($all_po,1000, true);
		$po_cond_for_in="";$po_cond_for_in2="";$po_cond_for_in3="";
		$pi=0;
		foreach($po_arr_cond as $key=>$value)
		{
		   if($pi==0)
		   {
			$po_cond_for_in=" and ( c.po_break_down_id  in(".implode(",",$value).")"; 
			$po_cond_for_in2=" and ( d.po_break_down_id  in(".implode(",",$value).")"; 
			
		   }
		   else //po_break_down_id
		   {
			$po_cond_for_in.=" or c.po_break_down_id  in(".implode(",",$value).")";
			$po_cond_for_in2.=" or d.po_break_down_id  in(".implode(",",$value).")";
			
		   }
		   $pi++;
		}	
		$po_cond_for_in.=" )";
		$po_cond_for_in2.=" )";
		
		
		$condition= new condition();
		$condition->company_name("=$cbo_company_name");
		if(str_replace("'","",$cbo_buyer_name)>0){
			  $condition->buyer_name("in($cbo_buyer_name)");
		 }
		 if($all_po_id!='' || $all_po_id!=0)
		 {
			$condition->po_id_in("$all_poIDs"); 
		 }
		 if(str_replace("'","",$txt_style_ref)!='')
		 {
			//echo "in($txt_order_id)".'dd';die;
			//$condition->job_no("in($all_jobs)");
		 }
		$condition->init();
		$costPerArr=$condition->getCostingPerArr();
		$emblishment= new emblishment($condition); 
		$wash= new wash($condition);
		$commercial= new commercial($condition);
	//	$trims_ReqQty_arr=$trims->getQtyArray_by_precostdtlsid();//getQtyArray_by_jobAndPrecostdtlsid();
		//print_r($trims_ReqQty_arr);
		$trims= new trims($condition);
		//echo $trims->getQuery(); die;
		$trims_costing_arr=$trims->getAmountArray_by_order();//getAmountArray_by_jobAndPrecostdtlsid();
		$emblishment_costing_arr=$emblishment->getAmountArray_by_order();
		$wash_amount_arr=$wash->getAmountArray_by_jobEmbnameAndEmbtype();
		$commercial_costing_arr=$commercial->getAmountArray_by_order();
		$commission= new commision($condition);
		$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
		$other= new other($condition);
		//echo $other->getQuery(); die;
		$other_costing_arr=$other->getAmountArray_by_order();
		//print_r($other_costing_arr);
		$jobIds=chop($all_job,',');
		$prod_cond_for_in="";
		$prod_ids=count(array_unique(explode(",",$all_job)));
		if($db_type==2 && $prod_ids>1000)
		{
			$prod_cond_for_in=" and (";
			$prodIdsArr=array_chunk(explode(",",$jobIds),999);
			foreach($prodIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$prod_cond_for_in.=" b.job_id in($ids) or"; 
			}
			$prod_cond_for_in=chop($prod_cond_for_in,'or ');
			$prod_cond_for_in.=")";
		}
		else
		{
			$jobIds=implode(",",array_unique(explode(",",$jobIds)));
			$prod_cond_for_in=" and b.job_id in($jobIds)";
		}
	 $data_sql="select a.id,a.buyer_name,a.gauge, b.id as fab_dtls_id,b.costing_per, a.job_no,b.job_id, 
	b.item_number_id, b.body_part_id, b.fabric_description, b.color_type_id, b.uom 
	from wo_po_details_master a, wo_pre_cost_fabric_cost_dtls b where a.id=b.job_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $prod_cond_for_in"; 
	//echo $data_sql; die;
	$result_data=sql_select($data_sql);
	//$lib_yarn_count=return_library_array( "select yarn_count, id from lib_yarn_count", "id", "yarn_count");
        $i=1; $totCons=0; $totReq=0; $totAmt=0;$amount_req=0;
        foreach ($result_data as $yarn)
        {
			//echo $job_id=$yarn[csf('job_id')];
			$buyer_job_arr[$yarn[csf("buyer_name")]][$yarn[csf("job_no")]]['fabric_description'].=$yarn[csf("fabric_description")].',';
			//$buyer_job_arr[$yarn[csf("buyer_name")]][$yarn[csf("job_no")]]['cm_cost']=$other_costing_arr[]['cm_cost'];
		}
		$data_sql="select a.id, a.fabric_cost_dtls_id,b.costing_per, a.job_no,a.job_id, a.count_id, a.copm_one_id, a.percent_one, a.copm_two_id, a.percent_two, a.type_id, a.cons_ratio, a.cons_qnty, a.rate, a.amount, a.avg_cons_qnty, a.supplier_id, a.color, a.consdznlbs, a.rate_dzn, 
		b.item_number_id, b.body_part_id, b.fabric_description, b.color_type_id, b.uom, c.color_number_id, c.stripe_color, c.measurement 
		from wo_pre_cost_fab_yarn_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_pre_stripe_color c where a.fabric_cost_dtls_id=b.id and b.id=c.pre_cost_fabric_cost_dtls_id and a.color=c.stripe_color  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $prod_cond_for_in";
	// echo $data_sql;
	$result_data=sql_select($data_sql);
	//$lib_yarn_count=return_library_array( "select yarn_count, id from lib_yarn_count", "id", "yarn_count");
        $i=1; $totCons=0; $totReq=0; $totAmt=0;$amount_req=0;
        foreach ($result_data as $yarn)
        {
			//echo $job_id=$yarn[csf('job_id')];
			$costPerQty=$costPerArr[$yarn[csf("job_no")]];
			//echo $costPerQty.'dd';
			$job_buyer=$job_buyer_name_arr[$yarn[csf("job_no")]]['buyer_name'];
			$poQty=0; $yarn_req_kg=0; $yarn_req_lbs=0;
			$poQty=$item_color_size_array[$job_buyer][$yarn[csf("job_no")]][$yarn[csf('item_number_id')]][$yarn[csf('color_number_id')]];
			//echo $poQty.'=='.$costPerQty;
            $yarn_req_kg=($yarn[csf('measurement')]/$costPerQty)*$poQty;
			$yarn_req_lbs=$yarn_req_kg*2.20462;
			
            $amount_req=$yarn_req_lbs*$yarn[csf('rate')];
			//echo $amount_req.'=='.$poQty.'<br>';
			if($amount_req>0)
			{
			$yarn_req_arr[$job_buyer][$yarn[csf('job_no')]]+= $amount_req;
			}
		}
		//print_r($yarn_req_arr);
		$financial_para=array();
		$sql_std_para=sql_select("select interest_expense,income_tax,cost_per_minute from lib_standard_cm_entry where company_id=$cbo_company_name and status_active=1 and	is_deleted=0 order by id");
		foreach($sql_std_para as $sql_std_row){
		$financial_para[interest_expense]=$sql_std_row[csf('interest_expense')];
		$financial_para[income_tax]=$sql_std_row[csf('income_tax')];
		$financial_para[cost_per_minute]=$sql_std_row[csf('cost_per_minute')];
		}
		unset($sql_std_para);
		
				$style1="#E9F3FF"; 
				$style="#FFFFFF";
		
		foreach($buyer_job_arr as $buyer_id=>$data_val)
		{
		$buyer_rowspan=0;
		 foreach($data_val as $job_no=>$val)
		  {
			  $buyer_rowspan++;
		  }
		  $buyer_wise_rowspan_arr[$buyer_id]=$buyer_rowspan;
		}
		//print_r($buyer_wise_rowspan_arr);
	?>
        <div style="width:100%; padding-left:0px;">
        <br><br><br> <br> 
           <?
          	$style1="#E9F3FF"; 
			$style="#FFFFFF";
			//echo count($buyer_job_arr);
			//print_r($buyer_job_arr);
			//$imge_arr=return_library_array( "select master_tble_id,image_location from  common_photo_library where file_type=1 and form_name='knit_order_entry' ",'master_tble_id','image_location');
			 $tbl_width="2440";
		   ?>
            <style>
				table tr td { height:30px; font-size:14px;}
				table#height_td tr th:first-child, table#height_td td:first-child
					{
						position: sticky;
						width: 100px;
						left: 0;
						z-index: 10;
					}
				table#height_td tr th:first-child
					{
						z-index: 11;
					}
				table#height_td tr th
					{
						position: sticky;
						top: 0;
						z-index: 9;
					}
		    </style> 
        <div style="margin:0 auto; width:<? echo $tbl_width+20;?>px;">
			    <table width="100%">
					<tr class="form_caption" style="font-size:24px;">
						<td align="center" width="100%" colspan="12" style="font-size:24px" class="form_caption"><? echo $company_library[$cbo_company_name].'<br>';if($cbo_style_owner!=0) echo 'Style Owner: '.$company_library[$cbo_style_owner]; ?></td>  
					</tr>
					<tr>
						<td colspan="12" style="font-size:24px" align="center"><strong>Details CM With OBS Report</strong></td>
					</tr>
				</table>
                <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" align="left" cellspacing="0" border="1" rules="all" id="table_header_1">
					<thead>
						<tr style="font-size:18px">
							<th width="20">SL</th>
							<th width="100">Buyer</th>
							<th width="90">Job No</th>
							<th width="100">Style No</th>
							<th width="40">GG</th>

							<th width="100">Order Qty in pcs</th>
							<th width="100">Knitting SMV</th>
							<th width="100">CM</th>
							<th width="100">CM Per Pcs</th>
							<th width="100">Knitting CM</th>
							<th width="100">Total Knitting CM</th>
							<th width="100">Linking CM</th>
							<th width="100">Total Linking CM</th>
							<th width="100">Trimming  CM</th>
							<th width="100">Total Trimming  CM</th>
							<th width="100">Mending CM</th>
							<th width="100">Total Mending CM</th>
							<th width="100">Finishing CM</th>
							<th width="100">Total Finishing CM</th>
							<th width="100">Fixed Cost CM</th>
							<th width="100">Total Fixed Cost CM</th>
							<th width="100">Total CM</th>
							<th width="100">Total Minute</th>
							<th width="100"> FOB in US$ </th>
							<th width="100"> Shipment date</th>
						</tr>
					</thead>
   			    </table>
             
            <div  class="scroll_div_inner" style="width:<? echo $tbl_width+18;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
                <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" align="left" cellspacing="0" border="1" rules="all" id="table_body">
            	
                    <?
           				$k=1;
						$tot_order_qty=$tot_po_value=$tot_cm_value=0;
						
						foreach($buyer_job_arr as $buyer_id=>$data_val)
						{
							$m=1;
						    foreach($data_val as $job_no=>$val)
						    {
								if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$ratio=$val["ratio"];
								$poids=rtrim($val["po_id"],',');
								$po_ids=array_unique(explode(",",$poids));
								
								//$poid=rtrim($row['po_id'],',');
								//$poids=array_unique(explode(",",$poid));
								$testing_cost=$inspection_cost=$certificate_cost=$design_cost=$studio_cost=$interest_cost=$cm_cost=$incometax_cost=$common_oh=$currier_cost=$embl_cost=$commission_cost=$commercial_cost=$trims_cost=$depr_amor_pre_cost=$freight_cost=0;
								//other_popup
								foreach($po_ids as $poId )
								{
									$testing_cost+=$other_costing_arr[$poId]['lab_test'];
									//echo $other_costing_arr[$poId]['common_oh'].'ddd';
									$freight_cost+=$other_costing_arr[$poId]['freight'];
									$inspection_cost+=$other_costing_arr[$poId]['inspection'];
									
									$cm_cost+=$other_costing_arr[$poId]['cm_cost'];
									$design_cost+=$other_costing_arr[$poId]['design_cost'];
									$studio_cost+=$other_costing_arr[$poId]['studio_cost'];
									$depr_amor_pre_cost+=$other_costing_arr[$poId]['depr_amor_pre_cost'];
								//	$incometax_cost+=$other_costing_arr[$poId]['incometax_cost'];
									
									$certificate_cost+=$other_costing_arr[$poId]['certificate_pre_cost'];
									$common_oh+=$other_costing_arr[$poId]['common_oh'];
									$currier_cost+=$other_costing_arr[$poId]['currier_pre_cost'];
									$wash_cost=$emblishment_costing_arr_name[$poId][3];
									$embl_cost+=$emblishment_costing_arr[$poId]+$wash_cost;
									
									$commercial_cost+=$commercial_costing_arr[$poId];
									$local=$commission_costing_arr[$poId][2];
									$foreign=$commission_costing_arr[$poId][1];
									$commission_cost+=$foreign+$local;
									$trims_cost+=$trims_costing_arr[$poId];
								}
								//echo $incometax_cost.'='.$interest_cost.'<br>';

								$yarn_req_amt=$yarn_req_arr[$buyer_id][$job_no];
								$tot_po_qnty=$val["po_qty_pcs"];
								$pub_shipment_date=rtrim($val["pub_shipment_date"],',');
								$pub_shipment_dates=array_unique(explode(",",$pub_shipment_date));
								// echo "<pre>";
								// print_r($pub_shipment_dates);die;
								// $pub_shipmentDate=min($pub_shipment_dates);
								$pub_shipmentDate=$pub_shipment_dates[0];
								
								$fabric_description=rtrim($val["fabric_description"],',');
								$fabric_desc=implode(",",array_unique(explode(",",$fabric_description)));
								
								$plan_cut_qty=$val["plan_cut"];
								$po_value=$val["po_value"];
								$total_job_quantity=$val["job_quantity"];

								$cm_per_pcs=$val["cm_cost"]/12;
								$total_knitting_cm= ($val['knitting_cm']/12)*$total_job_quantity; 
								$total_linking_cm= ($val['linking_cm']/12)*$total_job_quantity; 
								$total_trimming_cm= ($val['trimming_cm']/12)*$total_job_quantity; 
								$total_mending_cm= ($val['mending_cm']/12)*$total_job_quantity; 
								$total_finishing_cm= ($val['finishing_cm']/12)*$total_job_quantity; 
								$total_fixed_cost_cm= ($val['fixed_cost_cm']/12)*$total_job_quantity; 
								$total_cm= number_format($cm_per_pcs,2)*$total_job_quantity; 
								$total_minute= $val["set_smv"]*$total_job_quantity; 

								
								//$buyer_wise_rowspan=$buyer_wise_rowspan_arr[$buyer_id];
								//$job_no=$val["job_no"];
								$dzn_qnty=0; $balance=0; $job_mkt_required=0; $yarn_issued=0;
								if($costing_per_id_library[$job_no]==1) $dzn_qnty=12;
								else if($costing_per_id_library[$job_no]==3) $dzn_qnty=12*2;
								else if($costing_per_id_library[$job_no]==4) $dzn_qnty=12*3;
								else if($costing_per_id_library[$job_no]==5) $dzn_qnty=12*4;
								else $dzn_qnty=1;
								$dzn_qnty=$dzn_qnty*$ratio;
								
								//$interest_expense=$po_value*$financial_para[interest_expense]/100;
								//$income_tax=$po_value*$financial_para[income_tax]/100;
								//echo $financial_para[interest_expense].'<br>';
								$interest_cost=$po_value*$financial_para[interest_expense]/100;
								$incometax_cost=$po_value*$financial_para[income_tax]/100;
								
								$tot_other_cost=$testing_cost+$freight_cost+$inspection_cost+$certificate_cost+$common_oh+$currier_cost+$commercial_cost+$commission_cost+$embl_cost+$cm_cost+$design_cost+$studio_cost+$interest_cost+$incometax_cost+$depr_amor_pre_cost;;
								
								$total_cost=$tot_other_cost+$trims_cost+$yarn_req_amt;
								$tot_cm_cost=$po_value-$total_cost;
								$tot_cm_cost_dzn=($tot_cm_cost/$tot_po_qnty)*12;
									
								/*$tot_cm_cost=0;
								foreach($po_ids as $poID)
								{
								$tot_cm_cost+=$other_costing_arr[$poID]['cm_cost'];
								}*/
								?>
							
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>"          style="font-size:18px">
										<?
										if($m==1)
											{
										?>
												<td width="20" valign="top" rowspan="<? echo $buyer_wise_rowspan_arr[$buyer_id];?>"><? echo $k; ?></td>
												<td width="100" valign="top" rowspan="<? echo $buyer_wise_rowspan_arr[$buyer_id];?>"><div style="word-wrap:break-word; width:100px"><b><? echo $buyer_arr[$buyer_id]; ?></b></div></td>
										<?
											}
										?>
									<td width="90" align="center"><div style="word-wrap:break-word; width:90px"><b><? echo $job_no; ?>&nbsp;</b></div></td>
									<td width="100" align="center"><div style="word-wrap:break-word; width:100px"></b><? echo $val["style_ref_no"]; ?>&nbsp;</b></div></td>
									<td width="40"  align="center"><div style="word-wrap:break-word; width:40px"><b><? echo $gauge_arr[$val["gauge"]]; ?></b></div></td>
									
									<td width="100" align="right"><p><? echo number_format($total_job_quantity,0); ?></p></td>
									<td width="100" align="center" style="word-wrap:break-word; width:100px"> <? echo $val["set_smv"]; ?> </td>
									<td width="100" align="center"  title="Tot CM/PO Qty*12"><div style="word-wrap:break-word; width:65px">$<? echo $val["cm_cost"]; ?></div></td>
									<td width="100" align="center" style="word-wrap:break-word; width:100px"> $<? echo number_format($cm_per_pcs,2); ?> </td>
									<td width="100" align="center" style="word-wrap:break-word; width:100px"> $<? echo number_format($val['knitting_cm'],2); ?> </td>
									<td width="100" align="center" style="word-wrap:break-word; width:100px"> $<? echo number_format($total_knitting_cm,2); ?> </td>
									<td width="100" align="center" style="word-wrap:break-word; width:100px"> $<? echo number_format($val['linking_cm'],2); ?></td>
									<td width="100" align="center" style="word-wrap:break-word; width:100px"> $<? echo number_format($total_linking_cm,2);?> </td>
									<td width="100" align="center" style="word-wrap:break-word; width:100px"> $<? echo number_format($val['trimming_cm'],2); ?> </td>
									<td width="100" align="center" style="word-wrap:break-word; width:100px"> $<? echo number_format($total_trimming_cm,2);?> </td>
									<td width="100" align="center" style="word-wrap:break-word; width:100px"> $<? echo number_format($val['mending_cm'],2); ?> </td>
									<td width="100" align="center" style="word-wrap:break-word; width:100px"> $<? echo number_format($total_mending_cm,2);?> </td>
									<td width="100" align="center" style="word-wrap:break-word; width:100px"> $<? echo number_format($val['finishing_cm'],2); ?> </td>
									<td width="100" align="center" style="word-wrap:break-word; width:100px"> $<? echo number_format($total_finishing_cm,2);?> </td>
									<td width="100" align="center" style="word-wrap:break-word; width:100px"> $<? echo number_format($val['fixed_cost_cm'],2); ?> </td>
									<td width="100" align="center" style="word-wrap:break-word; width:100px"> $<? echo number_format($total_fixed_cost_cm,2);?> </td>
									<td width="100" align="center" style="word-wrap:break-word; width:100px"> $<? echo number_format($total_cm,0); ?> </td>
									<td width="100" align="center" style="word-wrap:break-word; width:100px">  <? echo number_format($total_minute,0); ?> </td>
									<td width="100" align="center"><p>$<? echo number_format($val["avg_unit_price"],2); ?></p></td>
									<td width="100" align="center"><p><? if(trim($pub_shipmentDate)!="" && trim($pub_shipmentDate)!='0000-00-00') echo change_date_format($pub_shipmentDate); ?>&nbsp;</p></td>
								
								</tr>
								<?

								$m++;

								$tot_order_qty+=$val["job_quantity"];
								$tot_knitting_smv+=$val["set_smv"];
								$tot_cm+=$val["cm_cost"];
								$tot_cm_per_pcs+=$cm_per_pcs;
								$tot_knitting_cm+=$val['knitting_cm'];
								$tot_knitting_cm_value+=$total_knitting_cm;
								$tot_linking_cm+=$val['linking_cm'];
								$tot_linking_cm_value+=$total_linking_cm;
								$tot_trimming_cm+=$val['trimming_cm'];
								$tot_trimming_cm_value+=$total_trimming_cm;
								$tot_mending_cm+=$val['mending_cm'];
								$tot_mending_cm_value+=$total_mending_cm;
								$tot_finishing_cm+=$val['finishing_cm'];
								$tot_finishing_cm_value+=$total_finishing_cm;
								$tot_fixed_cost_cm+=$val['fixed_cost_cm'];
								$tot_fixed_cost_cm_value+=$total_fixed_cost_cm;
								$tot_cm_value+=$total_cm;
								$tot_minute_value+=$total_minute;
						    }
						 $k++;
						}
			        ?>
           			 
            	</table>
            </div>
                <table width="<? echo $tbl_width;?>" align="left" class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tr style="font-size:18px">
						<td width="20"></td>
						<td width="100"></td>
						<td width="90"></td>
						<td width="100"></td>
						<td width="40">Total</td>

						<td width="100"><b><? echo number_format($tot_order_qty,2); ?></b></td>
						<td width="100" style="text-align: center;"><? echo number_format($tot_knitting_smv,0); ?></td>
						<td width="100" style="text-align: center;"><? echo $tot_cm; ?></td>
						<td width="100" style="text-align: center;"><? echo number_format($tot_cm_per_pcs,2); ?></td>
						<td width="100" style="text-align: center;"><? echo number_format($tot_knitting_cm,2); ?></td>
						<td width="100" style="text-align: center;"><? echo number_format($tot_knitting_cm_value,2); ?></td>
						<td width="100" style="text-align: center;"><? echo number_format($tot_linking_cm,2); ?></td>
						<td width="100" style="text-align: center;"><? echo number_format($tot_linking_cm_value,2); ?></td>
						<td width="100" style="text-align: center;"><? echo number_format($tot_trimming_cm,2); ?></td>
						<td width="100" style="text-align: center;"><? echo number_format($tot_trimming_cm_value,2); ?></td>
						<td width="100" style="text-align: center;"><? echo number_format($tot_mending_cm,2); ?></td>
						<td width="100" style="text-align: center;"><? echo number_format($tot_mending_cm_value,2); ?></td>
						<td width="100" style="text-align: center;"><? echo number_format($tot_finishing_cm,2); ?></td>
						<td width="100" style="text-align: center;"><? echo number_format($tot_finishing_cm_value,2); ?></td>
						<td width="100" style="text-align: center;"><? echo number_format($tot_fixed_cost_cm,2); ?></td>
						<td width="100" style="text-align: center;"><? echo number_format($tot_fixed_cost_cm_value,2); ?></td>
						<td width="100" style="text-align: center;"><? echo number_format($tot_cm_value,0); ?></td>
						<td width="100" style="text-align: center;"><? echo number_format($tot_minute_value,0); ?></td>
						<td width="100"></td>
						<td width="100">&nbsp;</td>
                    </tr>
                </table>
        </div>
 <?

	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****$reporttype"; 
    exit();
}

if($action=="report_generate_approve")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$reporttype=str_replace("'","",$reporttype);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_style_owner=str_replace("'","",$cbo_style_owner);
	$style_ref_id=str_replace("'","",$txt_style_ref_id);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	if($cbo_style_owner==0) $style_owner_cond=""; else $style_owner_cond=" and a.style_owner='$cbo_style_owner' ";
	if($cbo_company_name==0) $company_name_cond=""; else $company_name_cond=" and a.company_name='$cbo_company_name' ";
	$job_style_cond="";
	if(trim(str_replace("'","",$txt_style_ref))!="")
	{
		if(str_replace("'","",$style_ref_id)!="")
		{
			$job_style_cond=" and a.id in(".str_replace("'","",$style_ref_id).")";
		}
		else
		{
			$job_style_cond=" and a.style_ref_no = '".trim(str_replace("'","",$txt_style_ref))."'";
		}
	}

	if(str_replace("'","",$cbo_buyer_name)==0)
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
		$buyer_id_cond=" and a.buyer_name in($cbo_buyer_name)";
	}
	$date_cond="";
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
			$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
		}
	
	
	$style_row=count($txt_style_ref);
	ob_start();
	
	/*$pre_sql="select a.costing_per,a.job_no,a.exchange_rate from wo_pre_cost_mst a where a.status_active=1 and a.is_deleted=0 ";
	$sql_pre_result=sql_select($pre_sql);
	foreach($sql_pre_result as $row)
	{
		$costing_per_arr[$row[csf("job_no")]]['cost_per']=$row[csf("costing_per")];
		$costing_per_arr[$row[csf("job_no")]]['ex_rate']=$row[csf("exchange_rate")];
	}*/
	
	$sql="SELECT a.id,a.job_no_prefix_num as job_prefix,a.ship_mode,a.avg_unit_price, a.job_no, a.company_name, a.client_id,a.buyer_name, a.team_leader, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id as po_id, b.po_number,b.is_confirmed, b.pub_shipment_date,b.shipment_date,b.up_charge ,b.po_quantity,b.plan_cut,b.matrix_type, b.unit_price, b.po_total_price,b.status_active,c.item_number_id,c.plan_cut_qnty,c.order_quantity,c.order_total,c.color_number_id from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_color_size_breakdown c on  c.po_break_down_id=b.id join wo_pre_cost_mst d on a.id=d.job_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active=1  and c.is_deleted=0 and  d.approved in (1,3)  $company_name_cond $job_style_cond $buyer_id_cond $date_cond  order  by b.pub_shipment_date";
	//echo $sql;die();
	$sql_po_result=sql_select($sql);
	$all_po_id="";$all_job="";$all_full_job="";$all_style="";$all_style_desc=""; $all_buyer=""; $all_buyer_client=""; $all_order_uom=""; 
	$order_qty_pcs=0;$total_fob_value=0;$total_order_qty=0;$total_unit_price=0;$total_job_unit_price=0;
	//echo $buyer_name;die;
	$po_numer_arr=$po_data_arr=array();$tot_count=0;$total_order_upcharge=0;$item_color_size_array=array();$job_id_arr=array();
	foreach($sql_po_result as $row)
	{
		if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
		if($all_job=="") $all_job=$row[csf("id")]; else $all_job.=",".$row[csf("id")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_no']=$row[csf("po_number")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['style_ref_no']=$row[csf("style_ref_no")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_qty_pcs']+=$row[csf("order_quantity")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['plan_cut']+=$row[csf("plan_cut_qnty")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_value']+=$row[csf("order_total")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['avg_unit_price']=$row[csf("avg_unit_price")];
		$buyer_job_arr[$row[csf("buyer_name")]][$row[csf("job_no")]]['po_id'].=$row[csf("po_id")].",";
	
		$job_buyer_name_arr[$row[csf("job_no")]]['buyer_name']=$row[csf("buyer_name")];
		$item_color_size_array[$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]+=$row[csf("plan_cut_qnty")];
		
		$job_id_arr[$row[csf("id")]]=$row[csf("id")];
  } 
 // echo $all_po_id.'DDDDDDDD';
  if($all_po_id=="") {echo "<div style='color:red; font-size:30px;' align='center'>No PO No Found </div>";die;}
	//echo $tot_count;
	//echo $all_po_id.'dsd';
		$all_job_no=array_unique(explode(",",$all_full_job));
		$all_jobs="";
		foreach($all_job_no as $jno)
		{
				if($all_jobs=="") $all_jobs=$jno; else $all_jobs.=",".$jno;
		}
		$all_po=array_unique(explode(",",$all_po_id));
		$all_poIDs=implode(",",array_unique(explode(",",$all_po_id)));
		$po_arr_cond=array_chunk($all_po,1000, true);
		$po_cond_for_in="";$po_cond_for_in2="";$po_cond_for_in3="";
		$pi=0;
		foreach($po_arr_cond as $key=>$value)
		{
		   if($pi==0)
		   {
			$po_cond_for_in=" and ( c.po_break_down_id  in(".implode(",",$value).")"; 
			$po_cond_for_in2=" and ( d.po_break_down_id  in(".implode(",",$value).")"; 
			
		   }
		   else //po_break_down_id
		   {
			$po_cond_for_in.=" or c.po_break_down_id  in(".implode(",",$value).")";
			$po_cond_for_in2.=" or d.po_break_down_id  in(".implode(",",$value).")";
			
		   }
		   $pi++;
		}	
		$po_cond_for_in.=" )";
		$po_cond_for_in2.=" )";
		
		$condition= new condition();
		$condition->company_name("=$cbo_company_name");
		if(str_replace("'","",$cbo_buyer_name)>0){
			  $condition->buyer_name("in($cbo_buyer_name)");
		 }
		 if($all_po_id!='' || $all_po_id!=0)
		 {
			$condition->po_id_in("$all_poIDs"); 
		 }
		 if(str_replace("'","",$txt_style_ref)!='')
		 {
			//echo "in($txt_order_id)".'dd';die;
			//$condition->job_no("in($all_jobs)");
		 }
		$condition->init();
		$costPerArr=$condition->getCostingPerArr();
		$emblishment= new emblishment($condition); 
	
		$wash= new wash($condition);
		$commercial= new commercial($condition);
		
	//	$trims_ReqQty_arr=$trims->getQtyArray_by_precostdtlsid();//getQtyArray_by_jobAndPrecostdtlsid();
		//print_r($trims_ReqQty_arr);
		$trims= new trims($condition);
		//echo $trims->getQuery(); die;
		//echo $emblishment->getQuery(); die;
		$trims_costing_arr=$trims->getAmountArray_by_order();//getAmountArray_by_jobAndPrecostdtlsid();
		
		$emblishment_costing_arr=$emblishment->getAmountArray_by_order();
		$wash_amount_arr=$wash->getAmountArray_by_jobEmbnameAndEmbtype();
		$commercial_costing_arr=$commercial->getAmountArray_by_order();
		$commission= new commision($condition);
		$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
		$yarn= new yarn($condition);
		//echo $yarn->getQuery(); die;
		$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
		$other= new other($condition);
		$other_costing_arr=$other->getAmountArray_by_order();
		
			
		$jobIds=chop($all_job,',');
		$prod_cond_for_in="";
		$prod_ids=count(array_unique(explode(",",$all_job)));
		if($db_type==2 && $prod_ids>1000)
		{
		$prod_cond_for_in=" and (";
		$prodIdsArr=array_chunk(explode(",",$jobIds),999);
		foreach($prodIdsArr as $ids)
		{
		$ids=implode(",",$ids);
		$prod_cond_for_in.=" a.job_id in($ids) or"; 
		}
		$prod_cond_for_in=chop($prod_cond_for_in,'or ');
		$prod_cond_for_in.=")";
		}
		else
		{
		$jobIds=implode(",",array_unique(explode(",",$jobIds)));
		$prod_cond_for_in=" and a.job_id in($jobIds)";
		}
	 $data_sql="select a.id, a.fabric_cost_dtls_id,b.costing_per, a.job_no,a.job_id, a.count_id, a.copm_one_id, a.percent_one, a.copm_two_id, a.percent_two, a.type_id, a.cons_ratio, a.cons_qnty, a.rate, a.amount, a.avg_cons_qnty, a.supplier_id, a.color, a.consdznlbs, a.rate_dzn, 
	b.item_number_id, b.body_part_id, b.fabric_description, b.color_type_id, b.uom, c.color_number_id, c.stripe_color, c.measurement 
	from wo_pre_cost_fab_yarn_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_pre_stripe_color c where a.fabric_cost_dtls_id=b.id and b.id=c.pre_cost_fabric_cost_dtls_id and a.color=c.stripe_color  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $prod_cond_for_in";
	//echo $data_sql; die;
	$result_data=sql_select($data_sql);
	//$lib_yarn_count=return_library_array( "select yarn_count, id from lib_yarn_count", "id", "yarn_count");
        $i=1; $totCons=0; $totReq=0; $totAmt=0;$amount_req=0;
        foreach ($result_data as $yarn)
        {
			//echo $job_id=$yarn[csf('job_id')];
			$costPerQty=$costPerArr[$yarn[csf("job_no")]];
			//echo $costPerQty.'dd';
			$job_buyer=$job_buyer_name_arr[$yarn[csf("job_no")]]['buyer_name'];
			$poQty=0; $yarn_req_kg=0; $yarn_req_lbs=0;
			$poQty=$item_color_size_array[$job_buyer][$yarn[csf("job_no")]][$yarn[csf('item_number_id')]][$yarn[csf('color_number_id')]];
            $yarn_req_kg=($yarn[csf('measurement')]/$costPerQty)*$poQty;
			$yarn_req_lbs=$yarn_req_kg*2.20462;
			
            $amount_req=$yarn_req_lbs*$yarn[csf('rate')];
			//echo $amount_req.'=='.$poQty.'<br>';
			if($amount_req>0)
			{
			$yarn_req_arr[$job_buyer][$yarn[csf('job_no')]]+= $amount_req;
			}
		}
		$financial_para=array();
		$sql_std_para=sql_select("select interest_expense,income_tax,cost_per_minute from lib_standard_cm_entry where company_id=$cbo_company_name and status_active=1 and	is_deleted=0 order by id");
		foreach($sql_std_para as $sql_std_row){
		$financial_para[interest_expense]=$sql_std_row[csf('interest_expense')];
		$financial_para[income_tax]=$sql_std_row[csf('income_tax')];
		$financial_para[cost_per_minute]=$sql_std_row[csf('cost_per_minute')];
		}
		unset($sql_std_para);
		
		$sql_pre=sql_select("select job_no,margin_dzn from wo_pre_cost_dtls where  status_active=1 and	is_deleted=0  ".where_con_using_array($job_id_arr,0,'job_id')."  order by job_no");
		foreach($sql_pre as $row){
		$pre_cost_mergin_arr[$row[csf('job_no')]]=$row[csf('margin_dzn')];
		}
		//print_r($pre_cost_mergin_arr);
		unset($sql_pre);
		
		
		
				$style1="#E9F3FF"; 
				$style="#FFFFFF";
	?>
        <div style="width:100%; padding-left:0px;">
        <br><br><br> <br> 
           <?
          	$style1="#E9F3FF"; 
			$style="#FFFFFF";
			//echo count($buyer_job_arr);
			//print_r($buyer_job_arr);
			$imge_arr=return_library_array( "select master_tble_id,image_location from  common_photo_library where file_type=1 and form_name='knit_order_entry' ",'master_tble_id','image_location');
		   ?>
           <style>
	    table tr td { height:30px; font-size:14px;
		  
	   }
	   /*#outerdiv {
            position: absolute;
            top: 0;
            left: 0;
            right: 5em;
        }
        #innerdiv {
            width: 100%;
            overflow-x:scroll;
            margin-left: 5em;
            overflow-y:visible;
            padding-bottom:1px;
        }
        .headcol {
            position:absolute;
            width:5em;
            left:0;
            top:auto;
            border-right: 0px none black;
            border-top-width:3px;
            /*only relevant for first row
            margin-top:-3px;
            /*compensate for top border
        }
        .headcol:before {
            content:'Row ';
        }*/
        table#height_td tr th:first-child, table#height_td td:first-child{
		  position: sticky;
		  width: 100px;
		  left: 0;
		  z-index: 10;
		}
		table#height_td tr th:first-child{
		  z-index: 11;
		}
		table#height_td tr th{
		  position: sticky;
		  top: 0;
		  z-index: 9;
		}
		</style> 
		
         <div style="margin-left:10px">
          <table width="100%" style="margin-left:10px">
             <tr class="form_caption" style="font-size:24px;">
               <td align="center" width="100%" colspan="10" class="form_caption"><? echo $company_library[$cbo_company_name].'<br>';if($cbo_style_owner!=0) echo 'Style Owner: '.$company_library[$cbo_style_owner]; ?></td>
                    
                </tr>
                <tr>
                  <td colspan="10" style="font-size:24px" align="center"><strong>Style Wise CM Report</strong></td>
                </tr>
            </table>
            <table cellspacing="0" width="100%"  border="0">
            <tr valign="top">
       		<?
            $tot_po_qty=0;$tot_po_qty_arr=array();$tot_po_value_arr=array();$total_cost_arr=array();$other_cost_arr=array();$tot_plan_qty_arr=array();$tot_jobPlan_qty_arr=array();
			$tot_trims_cost_arr=array();
			$tot_trim_cal_arr=array();$tot_yarn_cal_arr=array();$tot_job_qty_arr=array();$tot_job_balance_arr=array();$tot_job_data_arr=array();$tot_buyer_job_data_arr=array();
         	 $width="";
			 $sub_yarn_cost=$sub_trims_cost=$other_cost=0;
		    foreach($buyer_job_arr as $buyer_id => $buyer_data)
            {
				//echo count($buyer_data);
          	 $width=75*count($buyer_data)+200;
		    
			 $bg_color="#00CC66";
		    ?>
            <td>
            <div id="outerdiv">
    		<div id="innerdiv">
			<table cellspacing="0" width="<? echo $width ?>px" id="height_td"  border="1" rules="all" class="rpt_table" style="margin:0px;" >
            <thead align="center">
            <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Photo</b></td>
            <?
				 $m=1;
            foreach($buyer_data as $jobNo => $row)
            {
				 if($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 //echo $bgcolor.'dd';
            ?>
            <td width="75"  align="center" bgcolor="<? echo $bgcolor;?>">
            <?
            if($imge_arr[$jobNo]!="")
			{
				 $src="../../../".$imge_arr[$jobNo];
			?>
		
			<a href="##" onClick="generate_image_view('<?= $src;?>','<?=$jobNo;?>','image_view')">  <img  src='../../../<? echo $imge_arr[$jobNo]; ?>' id="image_id" height='150'   width='130' /> </a>
            <?
			}
			else "&nbsp;";
			?>
            </td>
            <?
			$m++;
            }
            ?>
            <td width="75"  bgcolor="<? echo  $bg_color;?>"  align="center"><b>Buyer Total</b></td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Buyer Name</b></td>
                <?
                $b=1;
                foreach($buyer_data as $jobNo => $row)
                {
					 if($b%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<td width="75" bgcolor="<? echo $bgcolor;?>"  align="center"><?  echo $buyer_arr[$buyer_id];?></td>
					<?
					$b++;
                }
                ?>
                 <td width="75" align="center" bgcolor="<? echo $bg_color;?>"><? //echo number_format($tot_balance_value,2); ?></td>
            </tr>
            <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Style Ref</b></td>
            <?
			 $s=1;
          	foreach($buyer_data as $jobNo => $row)
            {
				 if($s%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
          	 <td width="75"  bgcolor="<? echo $bgcolor;?>" align="center"><p><?  echo $row['style_ref_no'];?></p></td>
            <?
			 $s++;
            }
            ?>
             <td width="75" align="center" bgcolor="<? echo $bg_color;?>"><? //echo number_format($tot_balance_value,2); ?></td>
            </tr>
            <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Job No</b></td>
            <?
			$j=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
            <td width="75" bgcolor="<? echo $bgcolor;?>" align="center"> <p> <? echo $jobNo;?></p> </td>
            <?
			$j++;
            }
            ?>
             <td width="75" align="center" bgcolor="<? echo $bg_color;?>"><? //echo number_format($tot_balance_value,2); ?></td>
            </tr>
            <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Avg.FOB/Pcs($)</b></td>
            <?
			$a=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($a%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
            <td width="75"  bgcolor="<? echo $bgcolor;?>" align="right">
         	 <?  echo $row['avg_unit_price'];?>
            </td>
            <?
			$a++;
            }
            ?>
            <td width="75" align="center" bgcolor="<? echo $bg_color;?>"><? //echo number_format($tot_balance_value,2); ?></td>
            </tr>
            <tr>
             <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Order Qty.(Pcs)</b></td> 
            <?
			$o=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($o%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
            <td width="75" bgcolor="<? echo $bgcolor;?>"  align="right">
         	 <?  echo number_format($row['po_qty_pcs'],0);?>
            </td>
            <? 
			$tot_po_qty_arr[$buyer_id]+=$row['po_qty_pcs'];
			$tot_job_qty_arr[$jobNo]+=$row['po_qty_pcs'];
			$o++;
            }
            ?>
            <td width="75" align="right"  bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_po_qty_arr[$buyer_id],2); ?></td>
            </tr>
            
            <tr>
             <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Plan Knit Qty</b></td> 
            <?
			$o=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($o%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
            <td width="75" bgcolor="<? echo $bgcolor;?>"  align="right">
         	 <?  echo number_format($row['plan_cut'],0);?>
            </td>
            <?
			$tot_plan_qty_arr[$buyer_id]+=$row['plan_cut'];
			$tot_jobPlan_qty_arr[$jobNo]+=$row['plan_cut'];
			$o++;
            }
            ?>
            <td width="75" align="right"  bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_plan_qty_arr[$buyer_id],2); ?></td>
            </tr>
             <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>FOB Value($)</b></td>
            <?
			$po_val_cal=0;$f=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($f%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$po_val_cal=$row['po_value'];
            ?>
            <td width="75" bgcolor="<? echo $bgcolor;?>" align="right">
         	 <?  echo number_format($row['po_value'],2);?>
            </td>
            <?
			$tot_po_value_arr[$buyer_id]+=$row['po_value'];
			$tot_job_value_arr[$jobNo]+=$row['po_value'];
			$f++;
            }
            ?>
            <td width="75" align="right"  bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_po_value_arr[$buyer_id],2); ?></td>
            </tr>
           <tr>
            <td class="headcol" width="100" bgcolor="<? echo $bg_color;?>" align="center"><b>Yarn Cost($)</b></td>
            <?
			
			$y=1;$yarn_cost=0;
            foreach($buyer_data as $jobNo => $row)
            {
				if($y%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$poid=rtrim($row['po_id'],',');
				$poids=array_unique(explode(",",$poid));
				
				$po_id=rtrim($row['po_id'],',');
				$po_ids=implode(",",array_unique(explode(",",$po_id)));
				
				foreach($poids as $poId )
				{
					
					//$yarn_cost+=$yarn_costing_arr[$poId];
					$sub_yarn_cost+=$yarn_costing_arr[$poId];
				}
				$yarn_cost=$yarn_req_arr[$buyer_id][$jobNo];
				if($sub_yarn_cost>0)
				{
				$tot_yarn_cal_arr[$jobNo]+=$yarn_cost;$sub_yarn_cost=0;
				}
				$tot_job_data_arr[$jobNo]['yarn']=($yarn_cost/$tot_jobPlan_qty_arr[$jobNo])*12;
				
				//$yarn_costing_arr;
            ?>
            <td width="75" bgcolor="<? echo $bgcolor;?>" align="right"  >
         	 <a href="##" onClick="generate_trims_detail('<? echo $po_ids; ?>','<? echo $jobNo; ?>','2','yarns_popup')"><? echo number_format($yarn_cost,2); ?></a> <? // echo number_format($yarn_cost,2);?>
            </td>
            <?
			$y++;
			$tot_yarn_cost_arr[$buyer_id]+=$yarn_cost;
			$tot_buyer_job_data_arr[$buyer_id]['yarn']+=($yarn_cost/$tot_jobPlan_qty_arr[$jobNo])*12;
            }
            ?>
            <td width="75" align="right" bgcolor="<? echo  $bg_color;?>"><? echo number_format($tot_yarn_cost_arr[$buyer_id],2); ?></td>
            </tr>
            <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Accessories Cost($)</b></td>
            <?
			$ac=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($ac%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$poid=rtrim($row['po_id'],',');
				$poids=array_unique(explode(",",$poid));
				$po_id=rtrim($row['po_id'],',');
				$po_ids=implode(",",array_unique(explode(",",$po_id)));
				
				$trims_cost=0;
				foreach($poids as $poId )
				{
					$trims_cost+=$trims_costing_arr[$poId];
					$sub_trims_cost+=$trims_costing_arr[$poId];
				}
				if($sub_trims_cost>0)
				{
				$tot_trim_cal_arr[$jobNo]+=$sub_trims_cost;$sub_trims_cost=0;
				}
				$tot_job_data_arr[$jobNo]['trim']=($trims_cost/$tot_job_qty_arr[$jobNo])*12;
				//$tot_buyer_job_data_arr[$buyer_id][$jobNo]['trim']=($trims_cost/$tot_job_qty_arr[$jobNo])*12;
            ?>
            <td width="75" bgcolor="<? echo $bgcolor;?>" align="right" >
         	 <a href="##" onClick="generate_trims_detail('<? echo $po_ids; ?>','<? echo $jobNo; ?>','1','trims_popup')"> <? echo number_format($trims_cost,2); ?></a>
            </td>
            <?
			$tot_trims_cost_arr[$buyer_id]+=$trims_cost;
			$tot_buyer_job_data_arr[$buyer_id]['trim']+=($trims_cost/$tot_job_qty_arr[$jobNo])*12;
			$ac++;
			
            }
            ?>
            <td width="75" align="right" bgcolor="<? echo  $bg_color;?>" ><? echo number_format($tot_trims_cost_arr[$buyer_id],2); ?></td>
            </tr>
             <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Others Cost($)</b></td>
            <?
			$oc=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($oc%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$poid=rtrim($row['po_id'],',');
				$poids=array_unique(explode(",",$poid));
				
				$po_id=rtrim($row['po_id'],',');
				$po_ids=implode(",",array_unique(explode(",",$po_id)));
				$testing_cost=$inspection_cost=$certificate_cost=$common_oh=$currier_cost=$embl_cost=$cm_cost=$design_cost=$studio_cost=$commission_cost=$commercial_cost=$depr_amor_pre_cost=$freight_cost=0;
				//other_popup
				foreach($poids as $poId )
				{
					$testing_cost+=$other_costing_arr[$poId]['lab_test'];
					//echo $other_costing_arr[$poId]['common_oh'].'ddd';
					$freight_cost+=$other_costing_arr[$poId]['freight'];
					$inspection_cost+=$other_costing_arr[$poId]['inspection'];
					$certificate_cost+=$other_costing_arr[$poId]['certificate_pre_cost'];
					$common_oh+=$other_costing_arr[$poId]['common_oh'];
					$currier_cost+=$other_costing_arr[$poId]['currier_pre_cost'];
					$wash_cost=$emblishment_costing_arr_name[$poId][3];
					$embl_cost+=$emblishment_costing_arr[$poId]+$wash_cost;
					
					$cm_cost+=$other_costing_arr[$poId]['cm_cost'];
					$depr_amor_pre_cost+=$other_costing_arr[$poId]['depr_amor_pre_cost'];
					$design_cost+=$other_costing_arr[$poId]['design_cost'];
					$studio_cost+=$other_costing_arr[$poId]['studio_cost'];
					
					$commercial_cost+=$commercial_costing_arr[$poId];
                    $local=$commission_costing_arr[$poId][2];
					$foreign=$commission_costing_arr[$poId][1];
					$commission_cost+=$foreign+$local;
				}
				$tot_job_value=$tot_job_value_arr[$jobNo];
				$interest_cost=$tot_job_value*$financial_para[interest_expense]/100;
				$incometax_cost=$tot_job_value*$financial_para[income_tax]/100;
							
				$other_cost=$testing_cost+$freight_cost+$inspection_cost+$certificate_cost+$common_oh+$currier_cost+$commercial_cost+$commission_cost+$embl_cost+$cm_cost+$design_cost+$studio_cost+$interest_cost+$incometax_cost+$depr_amor_pre_cost;
				if($other_cost>0)
				{
				$tot_other_cal_arr[$jobNo]+=$other_cost;
				}
				$tot_job_data_arr[$jobNo]['other']=($other_cost/$tot_job_qty_arr[$jobNo])*12;
				 
				
            ?>
            <td width="75" bgcolor="<? echo $bgcolor;?>"  align="right"  title="Lab+Freight+Inspection+Certificate+Currier+OH+Commercial+Emblishment+Commission">
         	 <a href="##" title="PO=<? echo $row['po_id'];?>" onClick="generate_trims_detail('<? echo $po_ids; ?>','<? echo $jobNo; ?>','3','others_popup')"> <? echo number_format($other_cost,2); ?></a><? // echo number_format($other_cost,2);?>
            </td>
            <?
			$oc++;
			$other_cost_arr[$buyer_id]+=$other_cost;
			$tot_buyer_job_data_arr[$buyer_id]['other']+=($other_cost/$tot_job_qty_arr[$jobNo])*12;
            }
            ?>
            <td width="75" align="right" bgcolor="<? echo $bg_color;?>"><? echo number_format($other_cost_arr[$buyer_id],2); ?></td>
            </tr>
            <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Total Cost($)</b></td>
            <?
			$total_cost_cal=0;$tc=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($tc%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$total_cost=$tot_yarn_cost_arr[$buyer_id]+$tot_trims_cost_arr[$buyer_id]+$other_cost_arr[$buyer_id];
				$total_cost_cal=$tot_trim_cal_arr[$jobNo]+$tot_other_cal_arr[$jobNo]+$tot_yarn_cal_arr[$jobNo];
			$tot_cost_cal_arr[$jobNo]+=$total_cost_cal;	
            ?>
            <td width="75"  bgcolor="<? echo $bgcolor;?>" align="right">
         	 <?  echo number_format($total_cost_cal,2);?>
            </td>
            <?
			$total_cost_arr[$buyer_id]+=$total_cost_cal;
			$total_cost_cal=0;
			$tc++;
			//$total_cost_cal_arr[$buyer_id]=$total_cost_cal;
            }
            ?>
            <td width="75" align="right" bgcolor="<? echo $bg_color;?>"><? echo number_format($total_cost_arr[$buyer_id],2); ?></td>
            </tr>
            <tr>
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color;?>" align="center"><b>Total CM Value($)</b></td>
            <?
			$bc=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($bc%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$po_balance=$tot_job_value_arr[$jobNo]-$tot_cost_cal_arr[$jobNo];
				
				//$po_balance=$po_val_cal-$total_cost_cal;
				
            ?>
            <td width="75" bgcolor="<? echo $bgcolor;?>" align="right" title="Job Value-Total Cost">
         	 <?  echo number_format($po_balance,2);?>
            </td>
            <?
			$tot_po_balance_arr[$buyer_id]+=$po_balance; 
			$tot_job_balance_arr[$jobNo]+=$tot_job_value_arr[$jobNo];
			
			//$tot_buyer_job_data_arr[$buyer_id]['fob']+=$tot_job_value_arr[$jobNo];
			
			$bc++;
            }
            ?>
            <td width="75" align="right" bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_po_balance_arr[$buyer_id],2); ?></td>
            </tr>
            <tr>
           
            <td class="headcol" width="100" bgcolor="<? echo  $bg_color; ?>" align="center"><b>CM/DZN($)</b></td>
            <?
			$cm=1;
            foreach($buyer_data as $jobNo => $row)
            {
				if($cm%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$fob_dzn_val=$tot_job_balance_arr[$jobNo]/$tot_job_qty_arr[$jobNo]*12;
				
				$tot_yarn=$tot_job_data_arr[$jobNo]['yarn'];
				$tot_trim=$tot_job_data_arr[$jobNo]['trim'];
				$tot_other=$tot_job_data_arr[$jobNo]['other'];
				//$tot_other=$tot_job_data_arr[$jobNo]['other'];
				
				//$cm_dzn=$fob_dzn_val-($tot_yarn+$tot_trim+$tot_other);//
				$cm_dzn=$pre_cost_mergin_arr[$jobNo];//
				//$tot_job_balance_arr[$jobNo]/$tot_job_qty_arr[$jobNo]*12;
            ?>
            <td width="75"  bgcolor="<? echo $bgcolor;?>" title="(<? echo $cm_dzn?>)"  align="right">
         	 <?  echo number_format($cm_dzn,2);?>
            </td>
            <?
			$tot_cm_dzn_value_arr[$buyer_id]+=$row['po_value'];
			//$tot_buyer_job_data_arr[$buyer_id]['other']+=$cm_dzn;
			
			$cm++;
            }
			$tot_job_cm_dzn=0;
			//$tot_buyer_job_data_arr[$buyer_id]['other'];
			
				$buyer_fob_dzn_val=$tot_po_value_arr[$buyer_id]/$tot_po_qty_arr[$buyer_id]*12;
				
				$tot_buyer_yarn=$tot_buyer_job_data_arr[$buyer_id]['yarn'];//$tot_buyer_job_data_arr[$buyer_id]['yarn']
				$tot_buyer_trim=$tot_buyer_job_data_arr[$buyer_id]['trim'];
				$tot_buyer_other=$tot_buyer_job_data_arr[$buyer_id]['other'];
				//echo $buyer_fob_dzn_val.'='.$tot_buyer_yarn.'='.$tot_buyer_trim.'='.$tot_buyer_other;
				//$tot_job_cm_dzn=$buyer_fob_dzn_val-($tot_buyer_yarn+$tot_buyer_trim+$tot_buyer_other); // previous formula

				$tot_job_cm_dzn=($tot_po_balance_arr[$buyer_id]/$tot_po_qty_arr[$buyer_id])*12
				
			//$tot_job_cm_dzn=$tot_po_balance_arr[$buyer_id]/$tot_po_qty_arr[$buyer_id]*12;
            ?>
            <td width="75" align="right" title="(Total CM Value/Order Qty.(Pcs))*12" bgcolor="<? echo $bg_color;?>"><? echo number_format($tot_job_cm_dzn,2); ?></td>
            </tr>
        </tbody>
        </table>
        </div>
        </div>
         </td>
        <?
			}
		?>
       		
            </tr>
    </table>
    <br>
    
            </div>
        </div>
<?

	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****$reporttype"; 
    exit();
}
if($action=='trims_popup')
{
	echo load_html_head_contents("Trims Details info", "../../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id."*".$tot_po_qnty;die;
	
	//echo $ratio;die;
	
 $sql_pi="select a.id,a.pi_number,a.pi_date from com_pi_master_details a, com_pi_item_details b,wo_booking_dtls c where a.id=b.pi_id and b.work_order_dtls_id=c.id and a.item_category_id=4 and a.pi_basis_id=1 and c.booking_type=2 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.job_no ='$job_no' order by c.id";
 $pi_res =sql_select($sql_pi);
 $costing_uom_sql = sql_select("select A.STYLE_REF_NO,B.COSTING_PER from WO_PRE_COST_MST b,WO_PO_DETAILS_MASTER a where  a.id=b.job_id and b.job_no = '$job_no' and b.status_active = 1 and b.is_deleted = 0");
 $style_ref_no=$costing_uom_sql[0]['STYLE_REF_NO'];
$pi_number="";
foreach($pi_res as $row)
{
	//echo $row[csf("pi_number")].'dd';
	//if($pi_number=="") $pi_number=$row[csf("pi_number")];else $pi_number.=",".$row[csf("pi_number")];
	$piIdArr[$row[csf("id")]]=$row[csf("id")];
}


?>	
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:650px;" >
<legend>Accessories Status pop up</legend>
    <div style="100%" id="report_container">
       <table id="table_header_1" class="rpt_table" width="1100" cellpadding="0" cellspacing="0" border="1" rules="all">
        <caption> <b style="float:left">Trims Details:</b></caption>
        <thead>
            <th width="30">SL</th>
            <th width="100">Item</th>
            <th width="120">Description</th>
            <th width="100">Supplier</th>
            <th width="50">UOM</th> 
            <th width="80">Cons/<?=$costing_per[$costing_uom_sql[0]['COSTING_PER']] ?></th>
            <th width="80">Total Qty </th>
            <th width="80">Rate</th>
            <th width="80">Item Total Amount</th>
        </thead>
        <tbody>
        <?
	$condition= new condition();
    if($po_id!='' || $po_id!=0)
    {
    	$condition->po_id("in($po_id)"); 
    }
    if(str_replace("'","",$job_no)!='')
    {
    	$condition->job_no("in('$job_no')");
    }
    
    $condition->init();
    $trims= new trims($condition);
    $emblishment= new emblishment($condition);
    $wash= new wash($condition);

    $item_group_arr=return_library_array( "select item_name,id from lib_item_group where item_category=4 order by item_name", "id", "item_name");
    $supplier_library=return_library_array( "select a.supplier_name,a.id from  lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id  and a.is_deleted=0  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");
    
    $trims_qty_arr=$trims->getQtyArray_by_jobItemidDescriptionGmtcolorAndSizeid();
	$trims_amt_arr=$trims->getAmountArray_by_jobItemidDescriptionGmtcolorAndSizeid();
    $trims_item_qty_arr=$trims->getQtyArray_by_jobAndItemid();
    $trims_desc_qty_arr=$trims->getQtyArray_by_jobItemidAndDescription();
	$trims_desc_amt_arr=$trims->getAmountArray_by_jobItemidAndDescription();
  // print_r($trims_amt_arr);
    
    $sql_trim_color="select c.id, c.job_no, c.trim_group, c.description, c.brand_sup_ref, c.nominated_supp, c.cons_uom, c.remark, d.wo_pre_cost_trim_cost_dtls_id as fab_dtl_id, d.item_size, d.color_number_id, d.item_number_id, d.size_number_id, d.excess_per, d.cons, d.tot_cons from wo_pre_cost_trim_cost_dtls c, wo_pre_cost_trim_co_cons_dtls d
    where c.job_no=d.job_no and d.wo_pre_cost_trim_cost_dtls_id=c.id and c.status_active=1 and c.is_deleted=0 and d.cons>0 and c.job_no='$job_no' order by c.id, c.trim_group";
    $trim_result=sql_select($sql_trim_color);
    foreach($trim_result as $row)
    {
		$trims_color_arr[$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("item_size")]]['description']=$row[csf("description")];
		$trims_color_arr[$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("item_size")]]['nominated_supp']=$row[csf("nominated_supp")];
		$trims_color_arr[$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("item_size")]]['size_number_id']=$row[csf("size_number_id")];
		$trims_color_arr[$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("item_size")]]['job_no']=$row[csf("job_no")];
		$trims_color_arr[$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("item_size")]]['item_size']=$row[csf("item_size")];
		$trims_color_arr[$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("item_size")]]['remark']=$row[csf("remark")];
		$trims_color_arr[$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("item_size")]]['cons_uom']=$row[csf("cons_uom")];
		$trims_color_arr[$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("item_size")]]['cons']+=$row[csf("cons")];
		
		$trims_color_arr[$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("item_size")]]['tot_size_cons']+=$trims_qty_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]];
		$trims_color_arr[$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("item_size")]]['amount']+=$trims_amt_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]];
		
		$trims_color_arr[$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("item_size")]]['tot_cons']+=$row[csf("tot_cons")];
		$trims_color_arr[$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("item_size")]]['excess_per']=$row[csf("excess_per")];
    }
	unset($trim_result);
	
    foreach($trims_color_arr as $trim_key=>$trim_data)
    {
		$trim_color_rowspan=0;
		foreach($trim_data as $desc_key=>$desc_data)
		{
			$trim_desc_rowspan=0;
			foreach($desc_data as $gmt_color_key=>$color_data)
			{
				$item_size_rowspan=0;
				foreach($color_data as $size_key=>$val)
				{
					$item_size_rowspan++;
					$trim_desc_rowspan++;
					$trim_color_rowspan++;
				}
				$item_size_rowspan_arr[$trim_key][$desc_key][$gmt_color_key]=$item_size_rowspan;
				$trim_desc_rowspan_arr[$trim_key][$desc_key]=$trim_desc_rowspan;
				$trim_size_rowspan_arr[$trim_key]=$trim_color_rowspan;
			}
		}
    }
        $j=1; $total_trim_cons=$total_tot_trim_cons=$total_tot_item_qty_cons=$total_trim_qty_cons=0;
        foreach($trims_color_arr as $trim_key=>$trim_data)
        {
			$k=1;
			foreach($trim_data as $desc_key=>$desc_data)
			{
				$n=1;
				foreach($desc_data as $gmt_color_key=>$color_data)
				{
					$m=1;
					foreach($color_data as $size_key=>$val)
					{
						if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if($col_size_cons_arr[$trim_key][$gmt_color_key]>0)
						{
							$tot_size_qty=$col_size_cons_arr[$trim_key][$gmt_color_key];
						}
						if($item_tot_cons_arr[$trim_key]>0)
						{
							$item_tot_size_qty=$item_tot_cons_arr[$trim_key];
						}
						$job_no=$val['job_no'];
						$item_size=$val['item_size'];
						$tot_size_cons=$val['tot_size_cons'];
						$gmt_item_desc_qty=$trims_desc_qty_arr[$job_no][$trim_key][$desc_key];
						$gmt_item_desc_amt=$trims_desc_amt_arr[$job_no][$trim_key][$desc_key];
						$gmt_size_qty=$tot_size_cons;
						$rate=$val['amount']/$gmt_size_qty;
						?>
							
						<?
						$total_tot_trim_qty+=$val['cons'];
						$total_trim_cons+=$val['tot_cons'];
						$total_trim_qty_cons+=$gmt_size_qty;
						$total_trim_amount+=$val['amount'];
						$k++;$m++;$n++;
					}
				}
				$total_tot_item_qty_cons+=$gmt_item_desc_qty;
				$total_tot_item_amt+=$gmt_item_desc_amt;
			}
			?>
			<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $j; ?>">  <?
							 
				?>
				<td ><? echo $j; ?></td>
				<td ><? echo $item_group_arr[$trim_key]; ?></td> 
				<td ><? echo  $desc_key; ?></td> 
				<td  align="center" style="word-break:break-all"><? echo $supplier_library[$val['nominated_supp']]; ?></td>
				<td align="center"><? echo $unit_of_measurement[$val['cons_uom']]; ?></td>
				<td align="right" style="word-break:break-all"><? echo number_format($val['tot_cons'],4); ?></td>
				<td  align="right" style="word-break:break-all"><? echo number_format($gmt_item_desc_qty,4); ?></td>
				<td align="center" style="word-break:break-all"><? echo number_format($rate,4); ?></td>
				<td  align="right" style="word-break:break-all"><? echo  number_format($gmt_item_desc_amt,4); ?></td>
			</tr>
			<?
			$j++;
        }
        ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="8" align="right"> Grand Total</th>
                <th align="right"><? echo number_format($total_tot_item_amt,4); ?></th>
            </tr>
        </tfoot>
    </table>  

	<br>
    <table id="table_header_1" class="rpt_table" width="770" cellpadding="0" cellspacing="0" border="1" rules="all">
        <caption> <b style="float:left">PI Details :</b></caption>
        <thead>
            <th width="30">SL</th>
            <th width="100">PI No</th>
            <th width="80">PI Date</th>
            <th width="150">Supplier</th>
			<th width="70">UOM</th>
			<th width="150">Accessories Item</th>
            <th width="70">Total Qty</th>
            <th width="50">Rate</th>
            <th width="70">Total Value</th>
        </thead>
        <tbody>
		<?
		//$pi_numb=implode(",",array_unique(explode(",",$pi_number)));
		$piIds=implode(",",$piIdArr);
		$sql_pi="select a.id,a.supplier_id,a.pi_number,a.pi_date, a.remarks,sum(b.amount) as amount,b.rate,sum(b.quantity) as wo_qnty,b.item_group,b.uom from com_pi_master_details a, com_pi_item_details b,wo_booking_dtls c where a.id=b.pi_id and b.work_order_dtls_id=c.id and b.item_group=c.trim_group and a.item_category_id=4 and a.pi_basis_id=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.is_deleted=0 and a.id in($piIds) and b.buyer_style_ref='$style_ref_no' group by  a.id,a.supplier_id,a.pi_number,a.pi_date, a.remarks,b.rate,b.item_group,b.uom ";
		$result_data=sql_select($sql_pi);
        $i=1; $tot_qty=0; $totReq=0; $tot_amt=0;
        foreach ($result_data as $yarn)
        {
			
		 if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
            <tr style="font-size:13px" bgcolor="<? echo $bgcolor;?>">
                <td><? echo $i; ?></td>
                <td style="word-break:break-all"><? echo $yarn[csf('pi_number')]; ?></td>
                <td style="word-break:break-all"><? echo change_date_format($yarn[csf('pi_date')]); ?></td>
                <td style="word-break:break-all"><? echo $supplier_library[$yarn[csf('supplier_id')]]; ?></td>
				<td style="word-break:break-all"><? echo $unit_of_measurement[$yarn[csf('uom')]]; ?></td>
				<td style="word-break:break-all"><? echo $item_group_arr[$yarn[csf('item_group')]]; ?></td>
                <td align="right"><? echo number_format($yarn[csf('wo_qnty')],4); ?></td>
                <td align="right"><? echo number_format($yarn[csf('rate')],4); ?> </td>
                <td align="right"><? echo number_format($yarn[csf('amount')],4); ?></td>
            </tr>
            <?
            $tot_qnty+=$yarn[csf('wo_qnty')];
			$tot_amnt+=$yarn[csf('amount')];
            $i++;
        }
		unset($data);
        ?>
        </tbody>
		<tfoot>
            <tr>
                <th colspan="6" align="right"> Grand Total</th>
                <th align="right"><? echo number_format($tot_qnty,4); ?></th>
				<th align="right"><? //echo number_format($tot_qnty,4); ?></th>
				<th align="right"><? echo number_format($tot_amnt,4); ?></th>
            </tr>
        </tfoot>
        </div>
    </fieldset>
<?	

	exit();
}
if($action=='yarns_popup')
{
	echo load_html_head_contents("Trims Details info", "../../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_id;die;
	
	//echo $ratio;die;

?>	
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:650px;" >
<legend>Yarns pop up</legend>
    <div style="100%" id="report_container">
       <table id="table_header_1" class="rpt_table" width="1100" cellpadding="0" cellspacing="0" border="1" rules="all">
        <caption> <b style="float:left">Yarn Details :</b></caption>
        <thead>
            <th width="30">SL</th>
            <th width="100">Gmt Item</th>
            <th width="100">Body Part</th>
            <th width="200">Yarn. Desc</th>
            <th width="100">Color Type</th>
            <th width="100">Gmt Color</th>
            <th width="100">Yarn. Color</th>
            <th width="50">UOM</th> 
            <th width="90">Total Yarn (Kg)</th>
            <th width="90">Total Yarn (Lbs)</th>
            <th width="60">Rate /Lbs</th>
            <th>Total Amount</th>
        </thead>
        <tbody>
		<?
		$result =sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.ship_mode, a.order_uom, a.gmts_item_id, b.id as po_id, b.po_number, b.po_received_date, b.pub_shipment_date, b.file_no, b.grouping, c.costing_date, c.costing_per, d.item_number_id, d.color_number_id, d.size_number_id, d.order_quantity as order_quantity, d.plan_cut_qnty as plan_cut from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c, wo_po_color_size_breakdown d where a.job_no=b.job_no_mst and b.id=d.po_break_down_id and c.job_no=b.job_no_mst and c.job_no=a.job_no and b.job_no_mst='$job_no'  and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 order by  d.color_order, d.size_order ASC");
	
	$job_in_orders = array(); $job_in_ship_arr = array(); $job_in_ref = array(); $job_in_file = array(); $posize_array=array(); $color_size_array=array(); $color_size_plan_arr=array();
	$pulich_ship_date=''; $shipment_date_arr='';
	
	foreach ($result as $val)
	{
		$job_in_orders_arr[$val[csf('po_id')]]=$val[csf('po_number')];
		$job_in_ship_arr[$val[csf('po_id')]]= $val[csf('pub_shipment_date')];
		$job_in_recvDate_arr[$val[csf('po_id')]]= change_date_format($val[csf('po_received_date')]);
		$job_in_ref_arr[$val[csf('po_id')]]= $val[csf('grouping')];
		$job_in_file_arr[$val[csf('po_id')]]= $val[csf('file_no')];
		
		$gmts_item_id=$val[csf('gmts_item_id')];
		$order_uom=$val[csf('order_uom')];
		$shipment_date_arr.=change_date_format($val[csf('pub_shipment_date')]).',';
		
		$job_no=$val[csf('job_no')];
		$buyer_name=$buyer_arr[$val[csf('buyer_name')]];
		$style_ref_no=$val[csf('style_ref_no')];
		
		$costing_date=$val[csf('costing_date')];
		$costing_per_name=$costing_per[$val[csf('costing_per')]];
		
		$posize_array[$val[csf("size_number_id")]]=$val[csf("size_number_id")];
		$color_size_plan_arr[$val[csf("po_id")]][$val[csf("item_number_id")]][$val[csf("color_number_id")]]+=$val[csf("plan_cut")];
		$color_size_array[$val[csf("po_id")]][$val[csf("item_number_id")]][$val[csf("color_number_id")]]+=$val[csf("order_quantity")];
		$po_size_qty_array[$val[csf("po_id")]][$val[csf("color_number_id")]][$val[csf("size_number_id")]]['po_qty']+=$val[csf("order_quantity")];
		$po_array[$val[csf("po_id")]]['po_no']=$val[csf("po_number")];
		$po_array[$val[csf("po_id")]]['item_number_id']=$val[csf("item_number_id")];
		
		$po_color_array[$val[csf("po_id")]][$val[csf("item_number_id")]]['color_number_id'].=$val[csf("color_number_id")].',';
		
		$item_color_size_array[$val[csf("item_number_id")]][$val[csf("color_number_id")]]+=$val[csf("plan_cut")];
		
		if($val[csf("costing_per")]==1) $order_price_per_dzn=12; 
        else if($val[csf("costing_per")]==2) $order_price_per_dzn=1;
        else if($val[csf("costing_per")]==3) $order_price_per_dzn=24;
        else if($val[csf("costing_per")]==4)$order_price_per_dzn=36; 
		else if($val[csf("costing_per")]==5) $order_price_per_dzn=48;
	}
	unset($result);
		
		 $data_sql="select a.id, a.fabric_cost_dtls_id,b.costing_per, a.job_no, a.count_id, a.copm_one_id, a.percent_one, a.copm_two_id, a.percent_two, a.type_id, a.cons_ratio, a.cons_qnty, a.rate, a.amount, a.avg_cons_qnty, a.supplier_id, a.color, a.consdznlbs, a.rate_dzn, 
	b.item_number_id, b.body_part_id, b.fabric_description, b.color_type_id, b.uom, c.color_number_id, c.stripe_color, c.measurement 
	
	from wo_pre_cost_fab_yarn_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_pre_stripe_color c where a.fabric_cost_dtls_id=b.id and b.id=c.pre_cost_fabric_cost_dtls_id and a.color=c.stripe_color and a.job_no ='$job_no' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";
	//echo $data_sql; die;
	$result_data=sql_select($data_sql);
	$lib_yarn_count=return_library_array( "select yarn_count, id from lib_yarn_count", "id", "yarn_count");
        $i=1; $totCons=0; $totReq=0; $totAmt=0;
        foreach ($result_data as $yarn)
        {
			
		 if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$poQty=0; $yarn_req_kg=0; $yarn_req_lbs=0;
			$poQty=$item_color_size_array[$yarn[csf('item_number_id')]][$yarn[csf('color_number_id')]];
            $yarn_req_kg=($yarn[csf('measurement')]/$order_price_per_dzn)*$poQty;
			$yarn_req_lbs=$yarn_req_kg*2.20462;
            $amount_req=$yarn_req_lbs*$yarn[csf('rate')];
            ?>
            <tr style="font-size:13px" bgcolor="<? echo $bgcolor;?>">
                <td><? echo $i; ?></td>
                <td style="word-break:break-all"><? echo $garments_item[$yarn[csf('item_number_id')]]; ?></td>
                <td style="word-break:break-all"><? echo $body_part[$yarn[csf('body_part_id')]]; ?></td>
                <td style="word-break:break-all"><? echo $lib_yarn_count[$yarn[csf('count_id')]]." ".$composition[$yarn[csf('copm_one_id')]]." ".$yarn_type[$yarn[csf('type_id')]]; ?></td>
                <td style="word-break:break-all"><? echo $color_type[$yarn[csf('color_type_id')]]; ?></td>
                <td style="word-break:break-all"><? echo $color_library[$yarn[csf('color_number_id')]]; ?></td>
                <td style="word-break:break-all"><? echo $color_library[$yarn[csf('color')]]; ?></td>
                <td><? echo $unit_of_measurement[$yarn[csf('uom')]]; ?></td> 
                <td align="right" title="<? echo "Stripe Cons=".$yarn[csf('measurement')]." /Costing Per=".$order_price_per_dzn."*Item Color Po Qty=".$poQty; ?>"><? echo number_format($yarn_req_kg,4); ?></td>
                <td align="right"><? echo number_format($yarn_req_lbs,4); ?> </td>
                <td align="right"><? echo number_format($yarn[csf('rate')],4); ?></td>
                <td align="right"><? echo number_format($amount_req,4); ?></td>
            </tr>
            <?
            $totConsKg+=$yarn_req_kg;
			$totConsLbs+=$yarn_req_lbs;
            $totAmt+=$amount_req;
            $i++;
        }
		unset($data);
        ?>
        </tbody>
        <tfoot>
            <tr style="font-weight:bold;background:#999999;  font-size:12px; text-align:right">
                <td colspan="8">Total:</td>
                <td align="right"><? echo number_format($totConsKg,4); ?></td>
                <td align="right"><? echo number_format($totConsLbs,4); ?> </td>
                <td align="right">&nbsp;</td>
                <td align="right"><? echo number_format($totAmt,4); ?> </td>
            </tr>
        </tfoot>
    </table>  
    <br>
    <table id="table_header_1" class="rpt_table" width="1100" cellpadding="0" cellspacing="0" border="1" rules="all">
        <caption> <b style="float:left">PI Details :</b></caption>
        <thead>
            <th width="30">SL</th>
            <th width="100">PI No</th>
            <th width="80">PI Date</th>
            <th width="150">Supplier</th>
            <th width="70">Count</th>
            <th width="70">Composition</th>
            <th width="70">Type</th>
            <th width="110">Color</th> 
            <th width="70">Qnty</th>
            <th width="50">Rate</th>
            <th width="70">Value</th>
             <th width="100">Remarks</th>
            <th>BTB Number</th>
        </thead>
        <tbody>
		<?

		$sql_pi="select a.id,a.supplier_id,a.pi_number,a.pi_date, a.remarks,b.work_order_dtls_id,c.yarn_comp_type1st,c.color_name,c.yarn_count as count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_type as type_id,c.supplier_order_quantity as qty,c.amount,c.rate from com_pi_master_details a, com_pi_item_details b,wo_non_order_info_dtls c where a.id=b.pi_id and b.work_order_dtls_id=c.id and a.item_category_id=1 and a.pi_basis_id=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.job_no ='$job_no' order by c.id";
	//echo $sql_pi;  
	$result_data=sql_select($sql_pi);
	$lib_yarn_count=return_library_array( "select yarn_count, id from lib_yarn_count", "id", "yarn_count");
        $i=1; $tot_qty=0; $totReq=0; $tot_amt=0;
        foreach ($result_data as $yarn)
        {
			
		 if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			/*$poQty=0; $yarn_req_kg=0; $yarn_req_lbs=0;
			$poQty=$item_color_size_array[$yarn[csf('item_number_id')]][$yarn[csf('color_number_id')]];
            $yarn_req_kg=($yarn[csf('measurement')]/$order_price_per_dzn)*$poQty;
			$yarn_req_lbs=$yarn_req_kg*2.20462;
            $amount_req=$yarn_req_lbs*$yarn[csf('rate')];*/
		//	com_btb_lc_master_details
		$lc_number = return_field_value("lc_number","com_btb_lc_master_details","status_active=1 and is_deleted=0 and pi_id='".$yarn[csf('id')]."' ","lc_number");
		
            ?>
            <tr style="font-size:13px" bgcolor="<? echo $bgcolor;?>">
                <td><? echo $i; ?></td>
                <td style="word-break:break-all"><? echo $yarn[csf('pi_number')]; ?></td>
                <td style="word-break:break-all"><? echo change_date_format($yarn[csf('pi_date')]); ?></td>
                <td style="word-break:break-all"><? echo $supplier_library[$yarn[csf('supplier_id')]];//echo $lib_yarn_count[$yarn[csf('count_id')]]." ".$composition[$yarn[csf('copm_one_id')]]." ".$yarn_type[$yarn[csf('type_id')]]; ?></td>
                <td style="word-break:break-all"><? echo $lib_yarn_count[$yarn[csf('count_id')]];//$color_type[$yarn[csf('color_type_id')]]; ?></td>
                <td style="word-break:break-all"><? echo $composition[$yarn[csf('yarn_comp_type1st')]]; ?></td>
                <td style="word-break:break-all"><? echo $yarn_type[$yarn[csf('type_id')]];//$color_library[$yarn[csf('color')]]; ?></td>
                <td><? echo $color_library[$yarn[csf('color_name')]]; ?></td> 
                <td align="right"><? echo number_format($yarn[csf('qty')],4); ?></td>
                <td align="right"><? echo number_format($yarn[csf('rate')],4); ?> </td>
                <td align="right"><? echo number_format($yarn[csf('amount')],4); ?></td>
                <td align="right"><? echo $yarn[csf('remarks')]; ?></td>
                <td align="center"><? echo $lc_number; ?></td>
            </tr>
            <?
            $tot_qty+=$yarn[csf('qty')];
			$tot_amt+=$yarn[csf('amount')];
           // $totAmt+=$amount_req;
            $i++;
        }
		unset($data);
        ?>
        </tbody>
        <tfoot>
            <tr  style="font-weight:bold; background:#999999; font-size:12px; text-align:right">
                <td colspan="8">Total:</td>
                <td align="right"><? echo number_format($tot_qty,4); ?></td>
                 <td align="right"><? //echo number_format($totConsKg,4); ?></td>
                <td align="right"><? echo number_format($tot_amt,4); ?> </td>
                <td align="right">&nbsp;</td>
                <td align="right"><? //echo number_format($totAmt,4); ?> </td>
            </tr>
        </tfoot>
    </table>
        </div>
    </fieldset>
<?	

	exit();
}

if($action=='yarns_popup2')
{
	echo load_html_head_contents("Trims Details info", "../../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_id;die;
	
	//echo $ratio;die;

?>	
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:650px;" >
<legend>Yarns pop up</legend>
    <div style="100%" id="report_container">
       <table id="table_header_1" class="rpt_table" width="1100" cellpadding="0" cellspacing="0" border="1" rules="all">
        <caption> <b style="float:left">Yarn Details :</b></caption>
        <thead>
            <th width="30">SL</th>
            <th width="100">Gmt Item</th>
            <th width="100">Body Part</th>
            <th width="200">Yarn. Desc</th>
            <th width="100">Color Type</th>
            <th width="100">Gmt Color</th>
            <th width="100">Yarn. Color</th>
            <th width="50">UOM</th> 
            <th width="90">Total Yarn (Kg)</th>
            <th width="90">Total Yarn (Lbs)</th>
            <th width="60">Rate /Lbs</th>
            <th>Total Amount</th>
        </thead>
        <tbody>
		<?
		$result =sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.ship_mode, a.order_uom, a.gmts_item_id, b.id as po_id, b.po_number, b.po_received_date, b.pub_shipment_date, b.file_no, b.grouping, c.costing_date, c.costing_per, d.item_number_id, d.color_number_id, d.size_number_id, d.order_quantity as order_quantity, d.plan_cut_qnty as plan_cut from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c, wo_po_color_size_breakdown d where a.job_no=b.job_no_mst and b.id=d.po_break_down_id and c.job_no=b.job_no_mst and c.job_no=a.job_no and b.job_no_mst='$job_no'  and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 order by  d.color_order, d.size_order ASC");
	
	$job_in_orders = array(); $job_in_ship_arr = array(); $job_in_ref = array(); $job_in_file = array(); $posize_array=array(); $color_size_array=array(); $color_size_plan_arr=array();
	$pulich_ship_date=''; $shipment_date_arr='';
	
	foreach ($result as $val)
	{
		$job_in_orders_arr[$val[csf('po_id')]]=$val[csf('po_number')];
		$job_in_ship_arr[$val[csf('po_id')]]= $val[csf('pub_shipment_date')];
		$job_in_recvDate_arr[$val[csf('po_id')]]= change_date_format($val[csf('po_received_date')]);
		$job_in_ref_arr[$val[csf('po_id')]]= $val[csf('grouping')];
		$job_in_file_arr[$val[csf('po_id')]]= $val[csf('file_no')];
		
		$gmts_item_id=$val[csf('gmts_item_id')];
		$order_uom=$val[csf('order_uom')];
		$shipment_date_arr.=change_date_format($val[csf('pub_shipment_date')]).',';
		
		$job_no=$val[csf('job_no')];
		$buyer_name=$buyer_arr[$val[csf('buyer_name')]];
		$style_ref_no=$val[csf('style_ref_no')];
		
		$costing_date=$val[csf('costing_date')];
		$costing_per_name=$costing_per[$val[csf('costing_per')]];
		
		$posize_array[$val[csf("size_number_id")]]=$val[csf("size_number_id")];
		$color_size_plan_arr[$val[csf("po_id")]][$val[csf("item_number_id")]][$val[csf("color_number_id")]]+=$val[csf("plan_cut")];
		$color_size_array[$val[csf("po_id")]][$val[csf("item_number_id")]][$val[csf("color_number_id")]]+=$val[csf("order_quantity")];
		$po_size_qty_array[$val[csf("po_id")]][$val[csf("color_number_id")]][$val[csf("size_number_id")]]['po_qty']+=$val[csf("order_quantity")];
		$po_array[$val[csf("po_id")]]['po_no']=$val[csf("po_number")];
		$po_array[$val[csf("po_id")]]['item_number_id']=$val[csf("item_number_id")];
		
		$po_color_array[$val[csf("po_id")]][$val[csf("item_number_id")]]['color_number_id'].=$val[csf("color_number_id")].',';
		
		$item_color_size_array[$val[csf("item_number_id")]][$val[csf("color_number_id")]]+=$val[csf("plan_cut")];
		
		if($val[csf("costing_per")]==1) $order_price_per_dzn=12; 
        else if($val[csf("costing_per")]==2) $order_price_per_dzn=1;
        else if($val[csf("costing_per")]==3) $order_price_per_dzn=24;
        else if($val[csf("costing_per")]==4)$order_price_per_dzn=36; 
		else if($val[csf("costing_per")]==5) $order_price_per_dzn=48;
	}
	unset($result);
		
		 $data_sql="select a.id, a.fabric_cost_dtls_id,b.costing_per, a.job_no, a.count_id, a.copm_one_id, a.percent_one, a.copm_two_id, a.percent_two, a.type_id, a.cons_ratio, a.cons_qnty, a.rate, a.amount, a.avg_cons_qnty, a.supplier_id, a.color, a.consdznlbs, a.rate_dzn, 
	b.item_number_id, b.body_part_id, b.fabric_description, b.color_type_id, b.uom, c.color_number_id, c.stripe_color, c.measurement 
	
	from wo_pre_cost_fab_yarn_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_pre_stripe_color c where a.fabric_cost_dtls_id=b.id and b.id=c.pre_cost_fabric_cost_dtls_id and a.color=c.stripe_color and a.job_no ='$job_no' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";
	//echo $data_sql; die;
	$result_data=sql_select($data_sql);
	$lib_yarn_count=return_library_array( "select yarn_count, id from lib_yarn_count", "id", "yarn_count");
        $i=1; $totCons=0; $totReq=0; $totAmt=0;
        foreach ($result_data as $yarn)
        {
			
		 if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$poQty=0; $yarn_req_kg=0; $yarn_req_lbs=0;
			$poQty=$item_color_size_array[$yarn[csf('item_number_id')]][$yarn[csf('color_number_id')]];
            $yarn_req_kg=($yarn[csf('measurement')]/$order_price_per_dzn)*$poQty;
			$yarn_req_lbs=$yarn_req_kg*2.20462;
            $amount_req=$yarn_req_lbs*$yarn[csf('rate')];
            ?>
            <tr style="font-size:13px" bgcolor="<? echo $bgcolor;?>">
                <td><? echo $i; ?></td>
                <td style="word-break:break-all"><? echo $garments_item[$yarn[csf('item_number_id')]]; ?></td>
                <td style="word-break:break-all"><? echo $body_part[$yarn[csf('body_part_id')]]; ?></td>
                <td style="word-break:break-all"><? echo $lib_yarn_count[$yarn[csf('count_id')]]." ".$composition[$yarn[csf('copm_one_id')]]." ".$yarn_type[$yarn[csf('type_id')]]; ?></td>
                <td style="word-break:break-all"><? echo $color_type[$yarn[csf('color_type_id')]]; ?></td>
                <td style="word-break:break-all"><? echo $color_library[$yarn[csf('color_number_id')]]; ?></td>
                <td style="word-break:break-all"><? echo $color_library[$yarn[csf('color')]]; ?></td>
                <td><? echo $unit_of_measurement[$yarn[csf('uom')]]; ?></td> 
                <td align="right" title="<? echo "Stripe Cons=".$yarn[csf('measurement')]." /Costing Per=".$order_price_per_dzn."*Item Color Po Qty=".$poQty; ?>"><? echo number_format($yarn_req_kg,4); ?></td>
                <td align="right"><? echo number_format($yarn_req_lbs,4); ?> </td>
                <td align="right"><? echo number_format($yarn[csf('rate')],4); ?></td>
                <td align="right"><? echo number_format($amount_req,4); ?></td>
            </tr>
            <?
            $totConsKg+=$yarn_req_kg;
			$totConsLbs+=$yarn_req_lbs;
            $totAmt+=$amount_req;
            $i++;
        }
		unset($data);
        ?>
        </tbody>
        <tfoot>
            <tr style="font-weight:bold;background:#999999;  font-size:12px; text-align:right">
                <td colspan="8">Total:</td>
                <td align="right"><? echo number_format($totConsKg,4); ?></td>
                <td align="right"><? echo number_format($totConsLbs,4); ?> </td>
                <td align="right">&nbsp;</td>
                <td align="right"><? echo number_format($totAmt,4); ?> </td>
            </tr>
        </tfoot>
    </table>  
    <br>
    <table id="table_header_1" class="rpt_table" width="1100" cellpadding="0" cellspacing="0" border="1" rules="all">
        <caption> <b style="float:left">PI Details :</b></caption>
        <thead>
            <th width="30">SL</th>
            <th width="100">PI No</th>
            <th width="80">PI Date</th>
            <th width="150">Supplier</th>
            <th width="70">Count</th>
            <th width="70">Composition</th>
            <th width="70">Type</th>
            <th width="110">Color</th> 
            <th width="70">Qnty</th>
            <th width="50">Rate</th>
            <th width="70">Value</th>
             <th width="100">Remarks</th>
            <th>BTB Number</th>
        </thead>
        <tbody>
		<?

		$sql_pi="select a.id,a.supplier_id,a.pi_number,a.pi_date, a.remarks,b.work_order_dtls_id,c.yarn_comp_type1st,c.color_name,c.yarn_count as count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_type as type_id,c.supplier_order_quantity as qty,c.amount,c.rate from com_pi_master_details a, com_pi_item_details b,wo_non_order_info_dtls c where a.id=b.pi_id and b.work_order_dtls_id=c.id and a.item_category_id=1 and a.pi_basis_id=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.job_no ='$job_no' order by c.id";
	//echo $sql_pi;  
	$result_data=sql_select($sql_pi);
	$lib_yarn_count=return_library_array( "select yarn_count, id from lib_yarn_count", "id", "yarn_count");
        $i=1; $tot_qty=0; $totReq=0; $tot_amt=0;
        foreach ($result_data as $yarn)
        {
			
		 if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			/*$poQty=0; $yarn_req_kg=0; $yarn_req_lbs=0;
			$poQty=$item_color_size_array[$yarn[csf('item_number_id')]][$yarn[csf('color_number_id')]];
            $yarn_req_kg=($yarn[csf('measurement')]/$order_price_per_dzn)*$poQty;
			$yarn_req_lbs=$yarn_req_kg*2.20462;
            $amount_req=$yarn_req_lbs*$yarn[csf('rate')];*/
		//	com_btb_lc_master_details
		$lc_number = return_field_value("lc_number","com_btb_lc_master_details","status_active=1 and is_deleted=0 and pi_id='".$yarn[csf('id')]."' ","lc_number");
		
            ?>
            <tr style="font-size:13px" bgcolor="<? echo $bgcolor;?>">
                <td><? echo $i; ?></td>
                <td style="word-break:break-all"><? echo $yarn[csf('pi_number')]; ?></td>
                <td style="word-break:break-all"><? echo change_date_format($yarn[csf('pi_date')]); ?></td>
                <td style="word-break:break-all"><? echo $supplier_library[$yarn[csf('supplier_id')]];//echo $lib_yarn_count[$yarn[csf('count_id')]]." ".$composition[$yarn[csf('copm_one_id')]]." ".$yarn_type[$yarn[csf('type_id')]]; ?></td>
                <td style="word-break:break-all"><? echo $lib_yarn_count[$yarn[csf('count_id')]];//$color_type[$yarn[csf('color_type_id')]]; ?></td>
                <td style="word-break:break-all"><? echo $composition[$yarn[csf('yarn_comp_type1st')]]; ?></td>
                <td style="word-break:break-all"><? echo $yarn_type[$yarn[csf('type_id')]];//$color_library[$yarn[csf('color')]]; ?></td>
                <td><? echo $color_library[$yarn[csf('color_name')]]; ?></td> 
                <td align="right"><? echo number_format($yarn[csf('qty')],4); ?></td>
                <td align="right"><? echo number_format($yarn[csf('rate')],4); ?> </td>
                <td align="right"><? echo number_format($yarn[csf('amount')],4); ?></td>
                <td align="right"><? echo $yarn[csf('remarks')]; ?></td>
                <td align="center"><? echo $lc_number; ?></td>
            </tr>
            <?
            $tot_qty+=$yarn[csf('qty')];
			$tot_amt+=$yarn[csf('amount')];
           // $totAmt+=$amount_req;
            $i++;
        }
		unset($data);
        ?>
        </tbody>
        <tfoot>
            <tr  style="font-weight:bold; background:#999999; font-size:12px; text-align:right">
                <td colspan="8">Total:</td>
                <td align="right"><? echo number_format($tot_qty,4); ?></td>
                 <td align="right"><? //echo number_format($totConsKg,4); ?></td>
                <td align="right"><? echo number_format($tot_amt,4); ?> </td>
                <td align="right">&nbsp;</td>
                <td align="right"><? //echo number_format($totAmt,4); ?> </td>
            </tr>
        </tfoot>
    </table>
        </div>
    </fieldset>
<?	

	exit();
}
if($action=='image_view')
{
	echo load_html_head_contents("Trims Details info", "../../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_id;die;	
	//echo $ratio;die;
	
?>	
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}

			function zoomin() {
				var image = document.getElementById("image_id");
				var currWidth = image.clientWidth;
				var currHeight = image.clientHeight;
				image.style.width = (currWidth + 50) + "px";
				image.style.height = (currHeight + 50) + "px";
			}
				
			function zoomout() {
				var image = document.getElementById("image_id");
				var currWidth = image.clientWidth;
				var currHeight = image.clientHeight;
				image.style.width = (currWidth - 50) + "px";
				image.style.height = (currHeight - 50) + "px";
		 	}

</script>	
<fieldset style="width:650px;" >
<legend>Image Views</legend>
    <div style="100%" id="report_container">
       <table id="table_header_1" class="rpt_table" width="1100" cellpadding="0" cellspacing="0" border="1" rules="all">  
			 <tr>
			 	<td align="center"><input type="button" id="show_button" class="formbutton" style="width:100px" value="zoom in"  onclick="zoomin()" /> <input type="button" id="show_button" class="formbutton" style="width:100px" value="zoom out"  onclick="zoomout()" /></td>
				
			 </tr>
	   </table>       
		<div align="center">
		<img  src="../<? echo $link; ?>" id="image_id" height='150'   width='130' /> 
		</div>
		
            
  
        </div>
    </fieldset>
<?	

	exit();
}





//Ex-Factory Delv. and Return
if($action=="others_popup")
{
 	echo load_html_head_contents("Ex-Factory Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $id;//$job_no;
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:500px"> 
        <div class="form_caption" align="center"><strong>Other Details</strong></div><br />
            <div style="width:100%"> 
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Particulars</th>
                        <th width="100">Amount</th>
                     </tr>   
                </thead> 	 	
            </table>  
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
				 $condition= new condition();
				if($po_id!='' || $po_id!=0)
				{
					$condition->po_id("in($po_id)"); 
				}
				if(str_replace("'","",$job_no)!='')
				{
					$condition->job_no("in('$job_no')");
				}
				
				$condition->init();
				$yarn= new yarn($condition);
				$emblishment= new emblishment($condition);
				$wash= new wash($condition);
				$commercial= new commercial($condition);
				$emblishment_costing_arr=$emblishment->getAmountArray_by_order();
				$wash_amount_arr=$wash->getAmountArray_by_jobEmbnameAndEmbtype();
				$commercial_costing_arr=$commercial->getAmountArray_by_order();
				//print_r($commercial_costing_arr);
				$commission= new commision($condition);
				$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
				$other= new other($condition);
				$other_costing_arr=$other->getAmountArray_by_order();
				//print_r($other_costing_arr);
				$poid=rtrim($po_id,',');
				// echo $poid;
				$poids=array_unique(explode(",",$poid));
				$testing_cost=$inspection_cost=$certificate_cost=$common_oh=$currier_cost=$embl_cost=$commission_cost=$commercial_cost=$freight_cost=0;
				//echo $poids
				foreach($poids as $poId )
				{
					$testing_cost+=$other_costing_arr[$poId]['lab_test'];
					//echo $testing_cost.'DDD';
					$freight_cost+=$other_costing_arr[$poId]['freight'];
					$inspection_cost+=$other_costing_arr[$poId]['inspection'];
					$certificate_cost+=$other_costing_arr[$poId]['certificate_pre_cost'];
					//echo $other_costing_arr[$poId]['common_oh'].'DD';
					$common_oh+=$other_costing_arr[$poId]['common_oh'];
					$currier_cost+=$other_costing_arr[$poId]['currier_pre_cost'];
					$wash_cost=$emblishment_costing_arr_name[$poId][3];
					$embl_cost+=$emblishment_costing_arr[$poId]+$wash_cost;
					$commercial_cost+=$commercial_costing_arr[$poId];
                    $local=$commission_costing_arr[$poId][2];
					$foreign=$commission_costing_arr[$poId][1];
					$commission_cost+=$foreign+$local;
				}
				
				//$other_cost=$testing_cost+$freight_cost+$inspection_cost+$certificate_cost+$common_oh+$currier_cost+$commercial_cost+$commission_cost+$embl_cost;
				
				
                $i=1;
              
				
                    $bgcolor="#EFEFEF"; $bgcolor2="#FFFFFF";                               
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="30">1</td> 
                        <td width="100">Commission </td>
                         <td width="100" align="right"><? echo number_format($commission_cost,2); ?></td>
                    </tr>
                     <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="30">2</td> 
                        <td width="100">Emblishment </td>
                         <td width="100" align="right"><? echo number_format($embl_cost,2); ?></td>
                    </tr>
                     <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="30">3</td> 
                        <td width="100">Commercial </td>
                         <td width="100" align="right"><? echo number_format($commercial_cost,2); ?></td>
                    </tr>
                     <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="30">4</td> 
                        <td width="100">Lab Test Cost </td>
                         <td width="100" align="right"><? echo number_format($testing_cost,2); ?></td>
                    </tr>
                     <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="30">5</td> 
                        <td width="100">Freight Cost </td>
                         <td width="100" align="right"><? echo number_format($freight_cost,2); ?></td>
                    </tr>
                     <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="30">6</td> 
                        <td width="100">Inspection Cost </td>
                         <td width="100" align="right"><? echo number_format($inspection_cost,2); ?></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="30">7</td> 
                        <td width="100">Certificate Cost </td>
                         <td width="100" align="right"><? echo number_format($certificate_cost,2); ?></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="30">8</td> 
                        <td width="100">Common OH Cost </td>
                         <td width="100" align="right"><? echo number_format($common_oh,2); ?></td>
                    </tr>
                    <? 
                    $total_other_cost+=$commission_cost+$embl_cost+$commercial_cost+$freight_cost+$inspection_cost+$certificate_cost+$testing_cost+$common_oh;
					 
                    $i++;
               
                ?>
                <tfoot>
                <tr>
                    <th colspan="2">Total</th>
                    <th><? echo number_format($total_other_cost,2); ?></th>
                   
                </tr>
                
                </tfoot>
            </table>
        </div> 
		</fieldset>
	</div>    
	<?
    exit();	
}
//disconnect($con);
?>
