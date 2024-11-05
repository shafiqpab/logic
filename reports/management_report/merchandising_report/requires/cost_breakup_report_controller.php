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
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.washes.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$item_library=return_library_array( "select id,item_name from  lib_item_group", "id", "item_name"  );
$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id", "supplier_name"  );


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


if($action=="style_refarence_search")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
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
								echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $txt_style_ref_no; ?>'+'**'+'<? echo $txt_style_ref_id; ?>'+'**'+'<? echo $txt_style_ref; ?>', 'create_list_style_search', 'search_div', 'cost_breakup_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
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
	$sql = "select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$select_date as job_year from wo_po_details_master a where a.company_name=$company $buyer_cond $year_cond $job_cond $search_con and is_deleted=0 order by job_no_prefix_num"; 
	//echo $sql; die;
	echo create_list_view("list_view", "Style Ref No,Job No,Year","160,90,100","400","200",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,job_year", "","setFilterGrid('list_view',-1)","0","",1) ;	
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
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
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
		$search_con=" and a.po_number like('%$search_value%')";	
	}
	elseif($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";		
	}
	elseif($search_type==3 && $search_value!=''){
		$search_con=" and a.job_no_mst like('%$search_value%')";		
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
if($action=="season_search_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Season No Info", "../../../../", 1, 1,'','','');
	?>
	<script>
		$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });
		var selected_name = new Array;var selected_id = new Array;
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
			//alert(str);
			if (str!="") str=str.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			if( jQuery.inArray( str[1], selected_name ) == -1 ) {
				selected_name.push( str[1] );
				selected_id.push( str[2] );
			}
			else {
				for( var i = 0; i < selected_name.length; i++ ) {
					if( selected_name[i] == str[1] ) break;
				}
				selected_name.splice( i, 1 );
				selected_id.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_name.length; i++ ) {
				name += selected_name[i] + ',';
				id += selected_id[i] + ',';
			}
			
			name = name.substr( 0, name.length - 1 );
			id = id.substr( 0, id.length - 1 );
			
			$('#hide_season').val( name );
			$('#hide_season_id').val( id );
		}
    </script>
   
    
    </head>
    <body>
        <div align="center">
            <form name="styleRef_form" id="styleRef_form">
                <fieldset style="width:350px;">
                    <input type="text" name="hide_season" id="hide_season" value="" />
                     <input type="text" name="hide_season_id" id="hide_season_id" value="" />
                    <?
                        if($buyerID==0)
                        {
                            if ($_SESSION['logic_erp']["data_level_secured"]==1)
                            {
                                if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
                            }
                            else $buyer_id_cond="";
                        }
                        else $buyer_id_cond=" and a.buyer_name=$buyerID";
                        
                       // if($job_no!=0) $jobno=" and job_no_prefix_num in (".$job_no.")"; else $jobno="";
                       
                           $sql="select distinct(b.season_name) as season,b.id from wo_po_details_master a,lib_buyer_season b where a.SEASON_BUYER_WISE=b.id and a.status_active=1 and a.is_deleted=0 and a.company_name=$companyID $jobno  $buyer_id_cond order by b.season_name";
						   //$sql="select distinct(season) as season from lib_buyer_season where status_active=1 and is_deleted=0  $buyer_id_cond order by season";
                       
                        //echo $sql;	
                        echo create_list_view("tbl_list_search", "Season", "200","300","280",0, $sql , "js_set_value", "season,id", "", 1, "0", $arr , "season", "","",'0','',1) ;
                        ?>
                </fieldset>
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
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
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_season_id=str_replace("'","",$txt_season_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$style_ref_id=str_replace("'","",$txt_style_ref_id);
	if($cbo_style_owner==0) $style_owner_cond=""; else $style_owner_cond=" and a.style_owner='$cbo_style_owner' ";
	if($cbo_company_name==0) $company_name_cond=""; else $company_name_cond=" and a.company_name='$cbo_company_name' ";

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
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}
	
	
	$style_row=count($txt_style_ref);
	
	//$job_no_cond="";$job_style_cond="";
	//if($style_ref_id!="") $job_no_cond="and a.id in($style_ref_id)";
	//if($style_row==1)
	///{
	//if(trim($txt_style_ref)!="") $job_style_cond="and  a.style_ref_no LIKE '%$txt_style_ref%'";
	//}
	$job_style_cond="";
	if(trim(str_replace("'","",$txt_style_ref))!="")
	{
		if(str_replace("'","",$style_ref_id)!="")
		{
			//$job_style_cond=" and a.id in(".str_replace("'","",$style_ref_id).")";
			 $style_ref_ids=explode(",",$style_ref_id);
			 $job_style_cond=where_con_using_array(array_filter(array_unique($style_ref_ids)),0,"a.id");
		}
		else
		{
			$job_style_cond=" and a.style_ref_no = '".trim(str_replace("'","",$txt_style_ref))."'";
		}
	}
	//$order_cond="";
	//if(trim($txt_order)!="") $order_cond="and b.po_number='$txt_order'";
	//if($txt_order_id!="") $order_cond="and b.id in($txt_order_id)";
	
	$order_cond="";
	if(trim(str_replace("'","",$txt_order))!="")
	{
		if(str_replace("'","",$txt_order_id)!="")
		{
			$order_cond=" and b.id in(".str_replace("'","",$txt_order_id).")";
		}
		else
		{
			$order_cond=" and b.po_number = '".trim(str_replace("'","",$txt_order))."'";
		}
	}
	
	if($txt_season_id!="") $season_cond="and a.season_matrix in($txt_season_id)";
	
	ob_start();
	
	//$costing_per_arr=return_library_array( "select job_no,costing_per from wo_pre_cost_mst ", "job_no", "costing_per");
	
	
	  $sql="select a.id,a.job_no_prefix_num as job_prefix,a.ship_mode,a.avg_unit_price, a.job_no, a.company_name, a.client_id,a.buyer_name, a.team_leader, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id as po_id, b.po_number,b.is_confirmed, b.pub_shipment_date,b.shipment_date,b.up_charge ,b.po_quantity,b.plan_cut,b.matrix_type, b.unit_price, b.po_total_price,b.status_active from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and a.status_active=1 and a.is_deleted=0 and b.status_active!=0  and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond order  by b.pub_shipment_date, b.id";
	
	$sql_po_result=sql_select($sql);
	$all_po_id="";$all_job="";$all_full_job="";$all_style="";$all_style_desc=""; $all_buyer=""; $all_buyer_client=""; $all_order_uom=""; 
	$order_qty_pcs=0;$total_fob_value=0;$total_order_qty=0;$total_unit_price=0;$total_job_unit_price=0;
	//echo $buyer_name;die;
	$po_numer_arr=$po_data_arr=array();$tot_count=0;$total_order_upcharge=0;
	foreach($sql_po_result as $row)
	{
		if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
		if($all_job=="") $all_job=$row[csf("job_prefix")]; else $all_job.=",".$row[csf("job_prefix")];
		if($all_buyer=="") $all_buyer=$buyer_arr[$row[csf("buyer_name")]]; else $all_buyer.=",".$buyer_arr[$row[csf("buyer_name")]];
		if($all_buyer_client=="") $all_buyer_client=$buyer_arr[$row[csf("client_id")]]; else $all_buyer_client.=",".$buyer_arr[$row[csf("client_id")]];
		if($all_full_job=="") $all_full_job="'".$row[csf('job_no')]."'"; else $all_full_job.=","."'".$row[csf('job_no')]."'";
		if($all_style=="") $all_style=$row[csf("style_ref_no")]; else $all_style.=",".$row[csf("style_ref_no")];
		if($all_style_desc=="") $all_style_desc=$row[csf("style_desc")]; else $all_style_desc.=",".$row[csf("style_desc")];
		if($all_order_uom=="") $all_order_uom=$unit_of_measurement[$row[csf("order_uom")]]; else $all_order_uom.=",".$unit_of_measurement[$row[csf("order_uom")]];
		
		$po_numer_arr[$row[csf("po_id")]]=$row[csf("po_number")];
		$job_numer_arr[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
		$job_numer_arr[$row[csf("po_id")]]['style']=$row[csf("style_ref_no")];
		$po_numer_arr[$row[csf("po_id")]][buyer]=$row[csf("buyer_name")];
		$po_data_arr[$row[csf("po_id")]][po_qty]=$row[csf("po_quantity")];
		$po_data_arr[$row[csf("po_id")]][plan_cut]=$row[csf("plan_cut")];
		$po_data_arr[$row[csf("po_id")]][po_price]=$row[csf("po_total_price")];
		$po_data_arr_qty[$row[csf("po_id")]][po_qty_trim]+=$row[csf("po_quantity")]/$row[csf('ratio')];
		$po_data_arr_qty[$row[csf("po_id")]][unit_rate]+=$row[csf("unit_price")];
		$po_data_arr[$row[csf("po_id")]][ratio]=$row[csf("ratio")];
		$JobIdArr[$row[csf("id")]]=$row[csf("id")];
		
		$status_active=$row[csf("status_active")];
		if($status_active==1)
		{
	
		if($row[csf("matrix_type")]==1) $avg_unit=$row[csf("unit_price")];else $avg_unit=$row[csf("avg_unit_price")];
		//$dzn_qnty=$dzn_qnty*$row[csf("ratio")];
		//echo $row[csf('ratio')];die;
	
		/*$po_data_arr_qty[$row[csf("po_id")]][po_qty_trim]+=$row[csf("po_quantity")]/$row[csf('ratio')];
		$po_data_arr[$row[csf("po_id")]][ratio]=$row[csf("ratio")];
		$po_data_arr[$row[csf("po_id")]][plan_cut]=$row[csf("plan_cut")];
		$po_data_arr[$row[csf("po_id")]][po_price]=$row[csf("po_total_price")];*/
		$po_data_arr[$row[csf("po_id")]][unit_rate]=$avg_unit;
		$po_data_arr[$row[csf("po_id")]][up_charge]+=$row[csf("up_charge")];
		$order_qty_pcs+=$row[csf('po_quantity')]*$row[csf('ratio')];
		$total_order_qty+=$row[csf('po_quantity')];
		$total_order_upcharge+=$row[csf('up_charge')];
		$total_unit_price+=$row[csf('unit_price')];
		
		$total_fob_value+=$row[csf('po_total_price')];
		$tot_count+=count($row[csf('po_id')]);
		$total_job_unit_price=($total_fob_value/$total_order_qty);
		}
		//echo $total_fob_value.'='.$total_order_qty.'='.$dzn_qnty;
	} 
	$jobId_cond=where_con_using_array($JobIdArr,0,'a.job_id');

	$pre_sql="select a.costing_per,a.job_no,a.exchange_rate from wo_pre_cost_mst a where a.status_active=1 and a.is_deleted=0 $jobId_cond";
	$sql_pre_result=sql_select($pre_sql);
	foreach($sql_pre_result as $row)
	{
		$costing_per_arr[$row[csf("job_no")]]['cost_per']=$row[csf("costing_per")];
		$costing_per_arr[$row[csf("job_no")]]['ex_rate']=$row[csf("exchange_rate")];
	}

	//echo $tot_count;
	//echo $all_po_id.'dsd';
					$all_job_no=array_unique(explode(",",$all_full_job));
					$all_jobs="";
					foreach($all_job_no as $jno)
					{
							if($all_jobs=="") $all_jobs=$jno; else $all_jobs.=",".$jno;
					}
					$all_po=array_unique(explode(",",$all_po_id));
					
					$po_arr_cond=array_chunk($all_po,1000, true);
					$po_cond_for_in="";$po_cond_for_in2="";$po_cond_for_in3="";$po_cond_for_in4="";$po_cond_for_in5="";$po_cond_for_in6="";
					$pi=0;
					foreach($po_arr_cond as $key=>$value)
					{
					   if($pi==0)
					   {
						$po_cond_for_in=" and ( c.po_break_down_id  in(".implode(",",$value).")"; 
						$po_cond_for_in2=" and ( d.po_break_down_id  in(".implode(",",$value).")"; 
						$po_cond_for_in3=" and ( b.id  in(".implode(",",$value).")"; 
						$po_cond_for_in4=" and ( b.po_break_down_id  in(".implode(",",$value).")"; 
						$po_cond_for_in5=" and ( e.order_id  in(".implode(",",$value).")"; 
						$po_cond_for_in6=" and ( d.order_id  in(".implode(",",$value).")"; 
					   }
					   else //po_break_down_id
					   {
						$po_cond_for_in.=" or c.po_break_down_id  in(".implode(",",$value).")";
						$po_cond_for_in2.=" or d.po_break_down_id  in(".implode(",",$value).")";
						$po_cond_for_in3.=" and b.id  in(".implode(",",$value).")"; 
						$po_cond_for_in4.=" and b.po_break_down_id  in(".implode(",",$value).")"; 
						$po_cond_for_in5.=" and e.order_id  in(".implode(",",$value).")"; 
						$po_cond_for_in6.=" and d.order_id  in(".implode(",",$value).")"; 
					   }
					   $pi++;
					}	
					$po_cond_for_in.=" )";
					$po_cond_for_in2.=" )";
					$po_cond_for_in3.=" )";
					$po_cond_for_in4.=" )";
					$po_cond_for_in5.=" )";
					$po_cond_for_in6.=" )";
					 $sql_emb="select
					c.po_break_down_id as po_id,sum(c.amount) as famount 
					from  wo_pre_cost_mst a,wo_pre_cost_embe_cost_dtls b ,wo_booking_dtls c ,wo_booking_mst d 
					where a.job_no=b.job_no and a.job_no=c.job_no and c.booking_no=d.booking_no and a.job_no=d.job_no and b.id=c.pre_cost_fabric_cost_dtls_id  and c.booking_type in(6) and c.is_short=2 and  d.item_category=25 and a.is_deleted=0 and  a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and c.wo_qnty>0 $po_cond_for_in
					group by c.po_break_down_id order by c.po_break_down_id";
					$emb_result=sql_select($sql_emb);
					$embl_data_arr=array();
					foreach($emb_result as $row)
					{
						$embl_data_arr[$row[csf('po_id')]]['eamount']=$row[csf('famount')];
					}
					 $sql_trims="select
					c.po_break_down_id as po_id,sum(c.amount) as famount,
					sum(CASE WHEN d.booking_type=2 and d.is_short=2 and d.entry_form=87 THEN c.amount ELSE 0 END) as trims_main_amount,
					sum(CASE WHEN d.booking_type=2 and d.is_short=1 THEN c.amount ELSE 0 END) as trims_short_amount,
					sum(CASE WHEN d.booking_type=5 and d.is_short=2 THEN c.amount ELSE 0 END) as trims_with_ord_amount
						 
					from  wo_booking_dtls c ,wo_booking_mst d 
					where    c.booking_no=d.booking_no and c.booking_type in(2,5) and c.is_short in(1,2) and c.is_deleted=0 and c.status_active=1 and c.amount>0 $po_cond_for_in
					group by c.po_break_down_id order by c.po_break_down_id";
					$trims_result=sql_select($sql_trims);
					$trims_data_arr=array();
					foreach($trims_result as $row)
					{
						$trims_data_arr[$row[csf('po_id')]]['tamount']=$row[csf('trims_main_amount')]+$row[csf('trims_short_amount')]+$row[csf('trims_with_ord_amount')];
					}
	
					$po_color_size_arr=array();
					$pos_sql="select d.po_break_down_id as po_id,(d.order_quantity) as po_qty ,(d.order_total) as order_amt,d.status_active
					from  wo_po_color_size_breakdown d  where d.status_active!=0 and d.is_deleted=0 $po_cond_for_in2 ";//group by d.po_break_down_id,d.status_active
					$res_sql=sql_select($pos_sql);
					foreach($res_sql as $row)
					{
						$status_active=$row[csf("status_active")];
						
						
						if($status_active==1)
						{
							$po_color_size_arr[$row[csf('po_id')]]['qty']+=$row[csf('po_qty')];
							$po_color_size_arr[$row[csf('po_id')]]['amt']+=$row[csf('order_amt')];
							
							$color_size_poQty+=$row[csf("po_qty")]; 
						$color_size_amt+=$row[csf("order_amt")];
						$po_dtls_size_arr[$row[csf('po_id')]]['qty']+=$row[csf('po_qty')];
						$po_dtls_size_arr[$row[csf('po_id')]]['amt']+=$row[csf('order_amt')];
						}
					}
					//echo $color_size_poQty;
					
				/*$sql_datas=("select b.po_breakdown_id as po_id,max(a.invoice_date) as invoice_date,
					sum(CASE WHEN a.shipping_mode=1 THEN b.current_invoice_qnty ELSE 0 END) as sea_qnty,
					sum(CASE WHEN a.shipping_mode=2 THEN b.current_invoice_qnty ELSE 0 END) as air_qnty
					 from  com_export_invoice_ship_mst a,com_export_invoice_ship_dtls b where a.id=b.mst_id  and a.shipping_mode in(1,2)  and a.status_active ='1' and a.is_deleted ='0'  $po_cond_for_in4 group by b.po_breakdown_id"); 
					 $sql_inv_result=sql_select($sql_datas);
					
					$export_invoice_arr=array();
					foreach($sql_inv_result as $row)
					{
						
						$export_invoice_arr[$row[csf('po_id')]][1]['sea']+=$row[csf('sea_qnty')];
						$export_invoice_arr[$row[csf('po_id')]][2]['air']+=$row[csf('air_qnty')];
					}*/
					$sql_inv=sql_select("select b.po_break_down_id as po_id,max(b.ex_factory_date) as factory_date,
					sum(CASE WHEN b.shiping_mode=1 and  b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as sea_qnty,
					sum(CASE WHEN b.shiping_mode=1 and  b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ret_sea_qnty,
					sum(CASE WHEN b.shiping_mode=2 and  b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as air_qnty,
					sum(CASE WHEN b.shiping_mode=2 and  b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ret_air_qnty,
					sum(CASE WHEN b.shiping_mode=3 and  b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as road_qnty,
					sum(CASE WHEN b.shiping_mode=3 and  b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ret_road_qnty,
					sum(CASE WHEN b.entry_form!=85 THEN b.total_carton_qnty ELSE 0 END) as tot_carton_qty,
					sum(CASE WHEN b.entry_form=85 THEN b.total_carton_qnty ELSE 0 END) as tot_ret_carton_qty
					from  pro_ex_factory_mst b where  b.shiping_mode in(1,2)  and b.status_active=1 and b.is_deleted =0 $po_cond_for_in4 group by b.po_break_down_id");
					
					
					$export_invoice_arr=array();//$exfactory_data_array=array();
					$total_ship_value=0;
					foreach($sql_inv as $row)
					{
						$export_invoice_arr[$row[csf('po_id')]][1]['sea']+=$row[csf('sea_qnty')]-$row[csf('ret_sea_qnty')];
						$export_invoice_arr[$row[csf('po_id')]][2]['air']+=$row[csf('air_qnty')]-$row[csf('ret_air_qnty')];
						$export_invoice_arr[$row[csf('po_id')]][3]['road']+=$row[csf('road_qnty')]-$row[csf('ret_road_qnty')];
						
						
						$unit_rate=$po_color_size_arr[$row[csf('po_id')]]['amt']/$po_color_size_arr[$row[csf('po_id')]]['qty'];
						
						$total_ship_value+=(($row[csf('sea_qnty')]-$row[csf('ret_sea_qnty')])*$unit_rate)+(($row[csf('air_qnty')]-$row[csf('ret_air_qnty')]))*$unit_rate;
						//$exfactory_data_array[$row[csf('po_id')]]['carton_qty']=$row[csf('tot_carton_qty')]-$row[csf('tot_ret_carton_qty')];
						//$export_invoice_arr[$row[csf('po_id')]]['date']=$row[csf('factory_date')]; 
					}
					//echo $total_ship_value.'DD';
			
					/*$ex_factory_arr=array();
					$ex_sql=sql_select("select po_break_down_id as po_id, 
					sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as exf_qty,
					sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ret_exf_qty 
					from  pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id");
					foreach($ex_sql as $row)
					{
						$ex_factory_arr[$row[csf('po_id')]]['exf_qty']=$row[csf('exf_qty')]-$row[csf('ret_exf_qty')];
					}*/
					$sql_fab_arr= "select c.id,c.color_type_id,c.fabric_description,c.gsm_weight,c.avg_cons,c.uom,c.item_number_id,c.job_no,c.item_number_id,c.body_part_id,c.uom,d.po_break_down_id as po_id, sum(c.amount) as amount  from
 wo_pre_cost_fabric_cost_dtls c,wo_pre_cos_fab_co_avg_con_dtls d
 where c.job_no=c.job_no and  d.pre_cost_fabric_cost_dtls_id=c.id and c.fab_nature_id=2 $po_cond_for_in2 group by  c.id,
  c.item_number_id,c.color_type_id,c.fabric_description,c.gsm_weight,c.body_part_id,c.job_no,c.uom,c.avg_cons,c.item_number_id,d.po_break_down_id,c.uom";
					 $fab_results=sql_select($sql_fab_arr);
					  foreach( $fab_results as $row )
					  {
						$item_name=$garments_item[$row[csf("item_number_id")]];
						 $fab_data_arr[$row[csf("id")]]['body']=$row[csf("body_part_id")];
						 $fab_data_arr[$row[csf("id")]]['item']=$row[csf("item_number_id")];
						 $fab_data_arr[$row[csf("id")]]['uom']=$row[csf("uom")];
						 $fab_desc=$color_type[$row[csf('color_type_id')]].','.$row[csf('fabric_description')].','.$row[csf('gsm_weight')];
						 $fab_data_arr[$row[csf("id")]]['des']=$fab_desc;
						 $fab_data_cons_arr[$row[csf("id")]]['avg_cons']=$row[csf("avg_cons")];
					  }
					//print_r($fab_data_cons_arr);
					if($db_type==2)
					{
						$group_con="LISTAGG(cast(a.job_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.job_no) as job_no,LISTAGG(cast(b.po_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.po_number) as po_number,LISTAGG(cast(a.style_ref_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.style_ref_no) as style_ref_no,LISTAGG(cast(b.id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_id";
						
						$group_con2="LISTAGG(cast(a.job_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.job_no) as job_no,LISTAGG(cast(b.po_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.po_number) as po_number,LISTAGG(cast(a.style_ref_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.style_ref_no) as style_ref_no,LISTAGG(cast(b.id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_id";
					}
					else
					{
						$group_con="group_concat(distinct(a.job_no)) as job_no,group_concat(distinct(a.style_ref_no)) as style_ref_no ,group_concat(distinct(b.po_number)) as po_number,group_concat(distinct(b.id)) as po_id";
						$group_con2="group_concat(distinct(a.job_no)) as job_no,group_concat(distinct(a.style_ref_no)) as style_ref_no ,group_concat(distinct(b.po_number)) as po_number,group_concat(distinct(b.id)) as po_id";
					}//fin_fab_qnty
						
						 $sql_fab_book= "select a.job_no,a.style_ref_no,b.id as po_id,b.po_number,c.booking_date,c.booking_no,c.booking_type,c.entry_form,c.is_short,c.supplier_id,c.short_booking_type as book_type,c.fabric_source,c.item_category,c.pay_mode,c.is_approved,d.pre_cost_fabric_cost_dtls_id as fab_dtls_id,d.gmt_item,d.color_type,d.construction,d.copmposition,d.gsm_weight,d.uom,d.dia_width,d.trim_group,d.grey_fab_qnty as grey_fab_qnty, d.fin_fab_qnty as fin_fab_qnty,d.rate as rate,c.remarks,
						(d.amount) as amount,
						(CASE WHEN c.booking_type=1 and c.is_short=2 THEN d.amount ELSE 0 END) as fab_main_amount,
						(CASE WHEN c.booking_type=1 and c.is_short=1 and c.short_booking_type=1 THEN d.amount ELSE 0 END) as fab_short_amount,
						(CASE WHEN c.booking_type=4 and c.is_short=2 THEN d.amount ELSE 0 END) as fab_with_ord_amount
						 from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c,wo_booking_dtls d  where a.job_no=b.job_no_mst  and b.id=d.po_break_down_id and d.booking_no=c.booking_no and c.booking_type in(1,4) and c.short_booking_type not in(2,3) and b.status_active!=0 and 
   b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.fin_fab_qnty>0  $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond order by c.booking_no,d.pre_cost_fabric_cost_dtls_id";
   				//echo $sql_fab_book;
				$book_result=sql_select($sql_fab_book);
				$sum_total_fab_amount=0;$fabric_booking_arr=array(); $avg_cons=0;
				foreach($book_result as $rows)
				{
					$item_name=$garments_item[$fab_data_arr[$rows[csf("fab_dtls_id")]]['item']];
					$item_name_id=$fab_data_arr[$rows[csf("fab_dtls_id")]]['item'];
					$body_parts=$body_part[$fab_data_arr[$rows[csf("fab_dtls_id")]]['body']];
					$uom_id=$fab_data_arr[$rows[csf("fab_dtls_id")]]['uom'];
					$body_part_id=$fab_data_arr[$rows[csf("fab_dtls_id")]]['body'];
					//$amount=$grey_fab_qnty*$row[csf('fab_dtls_id')];
					//echo $rows[csf('is_short')].'===='.$rows[csf('booking_type')];
					 $avg_cons=$fab_data_cons_arr[$rows[csf("fab_dtls_id")]]['avg_cons'];
					// echo $avg_cons.',';
					$fab_print_data=$rows[csf('fabric_source')].'**'.$rows[csf('item_category')].'**'.$rows[csf('is_approved')];
					$booking_type_data=$rows[csf('booking_type')].'**'.$rows[csf('is_short')].'**'.$rows[csf('book_type')];
					$fab_desc= $fab_data_arr[$rows[csf("fab_dtls_id")]]['des'].','.$rows[csf('dia_width')];//$item_name.','.$color_type[$rows[csf('color_type')]].','.$rows[csf('construction')].','.$rows[csf('copmposition')].','.$rows[csf('gsm_weight')].','.$rows[csf('dia_width')];
					
					$entry_form=$rows[csf('entry_form')];
					if($entry_form!=108)
					{
						 $grey_fab_qnty=$rows[csf('grey_fab_qnty')];
						 	// $grey_fab_qnty=$rows[csf('fin_fab_qnty')];
						 if($grey_fab_qnty==0)
						 {
							 $avg_rate=0;
							 $amount=0;
							 }
							 else{
						$avg_rate=$rows[csf('amount')]/$grey_fab_qnty;
						$amount=$grey_fab_qnty*$avg_rate;
								 }
					}
					else
					{
						$grey_fab_qnty=$rows[csf('fin_fab_qnty')];
						 if($grey_fab_qnty==0)
						 {
							 $avg_rate=0;
							 $amount=0;
							 }
							 else{
						$avg_rate=$rows[csf('amount')]/$grey_fab_qnty;
						$amount=$grey_fab_qnty*$avg_rate;
								 }

					}
					/*if($rows[csf('booking_type')]==1 && $rows[csf('is_short')]==2)
						{
							$total_fab_main_amount+=$rows[csf('amount')];
						}
						if($rows[csf('booking_type')]==1 && $rows[csf('is_short')]==1)
						{
							$total_fab_short_amount+=$rows[csf('amount')];
						}
						else if($rows[csf('booking_type')]==4 && $rows[csf('is_short')]==2)
						{
							$total_fab_with_ord_amount+=$rows[csf('amount')];
						}*/
					
					if($grey_fab_qnty==0) $grey_fab_qnty=0;else $grey_fab_qnty=$grey_fab_qnty;
					//if($rows[csf('fin_fab_qnty')]==0) $rows[csf('fin_fab_qnty')]=0;else $rows[csf('fin_fab_qnty')]=$rows[csf('fin_fab_qnty')];
					if($rows[csf('amount')]==0) $rows[csf('amount')]=0;else $rows[csf('amount')]=$rows[csf('amount')];
					
					$fabric_booking_arr[$rows[csf('booking_no')]][$booking_type_data][$body_parts][$fab_desc][$uom_id]['eform']=$rows[csf('entry_form')];
					$fabric_booking_arr[$rows[csf('booking_no')]][$booking_type_data][$body_parts][$fab_desc][$uom_id]['job_no'].=$rows[csf('job_no')].',';
					$fabric_booking_arr[$rows[csf('booking_no')]][$booking_type_data][$body_parts][$fab_desc][$uom_id]['style'].=$rows[csf('style_ref_no')].',';
					$fabric_booking_arr[$rows[csf('booking_no')]][$booking_type_data][$body_parts][$fab_desc][$uom_id]['po_id'].=$rows[csf('po_id')];
					$fabric_booking_arr[$rows[csf('booking_no')]][$booking_type_data][$body_parts][$fab_desc][$uom_id]['booking_date']=$rows[csf('booking_date')];
					$fabric_booking_arr[$rows[csf('booking_no')]][$booking_type_data][$body_parts][$fab_desc][$uom_id]['booking_type']=$rows[csf('booking_type')];		
					$fabric_booking_arr[$rows[csf('booking_no')]][$booking_type_data][$body_parts][$fab_desc][$uom_id]['book_type']=$rows[csf('book_type')];
					$fabric_booking_arr[$rows[csf('booking_no')]][$booking_type_data][$body_parts][$fab_desc][$uom_id]['supplier_id']=$rows[csf('supplier_id')];
					$fabric_booking_arr[$rows[csf('booking_no')]][$booking_type_data][$body_parts][$fab_desc][$uom_id]['item']=$item_name_id;
					$fabric_booking_arr[$rows[csf('booking_no')]][$booking_type_data][$body_parts][$fab_desc][$uom_id]['pay_mode']=$rows[csf('pay_mode')];
					$fabric_booking_arr[$rows[csf('booking_no')]][$booking_type_data][$body_parts][$fab_desc][$uom_id]['is_short']=$rows[csf('is_short')];
					$fabric_booking_arr[$rows[csf('booking_no')]][$booking_type_data][$body_parts][$fab_desc][$uom_id]['po_number'].=$rows[csf('po_number')].',';
					$fabric_booking_arr[$rows[csf('booking_no')]][$booking_type_data][$body_parts][$fab_desc][$uom_id]['fab_qnty']+=$grey_fab_qnty;
					$fabric_booking_arr[$rows[csf('booking_no')]][$booking_type_data][$body_parts][$fab_desc][$uom_id]['print_data']=$fab_print_data;				
					$fabric_booking_arr[$rows[csf('booking_no')]][$booking_type_data][$body_parts][$fab_desc][$uom_id]['amount']+=$rows[csf('amount')];
					$fabric_booking_arr[$rows[csf('booking_no')]][$booking_type_data][$body_parts][$fab_desc][$uom_id]['avg_cons']=$avg_cons;
					$fabric_booking_arr[$rows[csf('booking_no')]][$booking_type_data][$body_parts][$fab_desc][$uom_id]['remarks']=$rows[csf('remarks')];
					
					$fabric_booking_arr[$rows[csf('booking_no')]][$booking_type_data][$body_parts][$fab_desc][$uom_id]['main_amount']+=$rows[csf('fab_main_amount')];
					$fabric_booking_arr[$rows[csf('booking_no')]][$booking_type_data][$body_parts][$fab_desc][$uom_id]['short_amount']+=$rows[csf('fab_short_amount')];
					$fabric_booking_arr[$rows[csf('booking_no')]][$booking_type_data][$body_parts][$fab_desc][$uom_id]['with_ord_amount']+=$rows[csf('fab_with_ord_amount')];
					
					
					
					$sum_total_fab_amount+=$amount;
				}
			//print_r($fabric_booking_arr);die;
				//echo $sum_total_fab_amount.'<br>';die;
				if($db_type==2)
					{
						$group_des="LISTAGG(cast(d.description as varchar2(4000)), ',') WITHIN GROUP (ORDER BY d.description) as description";
					}
					else
					{
						$group_des="group_concat(distinct(d.description)) as description";
					}
					
				 $sql_trim_des="select c.id as dtls_id, c.trim_group,c.uom,c.booking_no,d.description from wo_booking_dtls c,wo_trim_book_con_dtls d where c.id=d.wo_trim_booking_dtls_id  and  c.status_active=1 and c.is_deleted=0 $po_cond_for_in2 group by  c.id ,c.booking_no,c.trim_group,c.uom,d.description";//
				$trims_data_des_arr=array();
				$trim_result_des=sql_select($sql_trim_des);
					foreach($trim_result_des as $row)
					{
						$trims_data_des_arr[$row[csf('dtls_id')]][$row[csf('booking_no')]][$row[csf('trim_group')]][$row[csf('uom')]]['description']=$row[csf('description')];
					}
					if($db_type==2)
					{
						$group_con="LISTAGG(cast(a.job_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.job_no) as job_no,LISTAGG(cast(b.po_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.po_number) as po_number,LISTAGG(cast(b.id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_id,LISTAGG(cast(a.style_ref_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.style_ref_no) as style_ref_no";
					}
					else
					{
						$group_con="group_concat(distinct(a.job_no)) as job_no,group_concat(distinct(a.style_ref_no)) as style_ref_no ,group_concat(distinct(b.po_number)) as po_number";
					}
				 $sql_trims_book= "select a.job_no,a.style_ref_no,b.po_number,b.id as po_id,c.booking_date,c.currency_id,c.supplier_id,c.booking_no,c.booking_type,c.is_short,c.short_booking_type as book_type,c.fabric_source,c.item_category,c.is_approved,d.uom,d.trim_group,d.id as dtls_id, (d.wo_qnty) as wo_qnty,(d.rate) as rate,(d.exchange_rate) as exchange_rate,
				(d.amount) as amount,
				(CASE WHEN c.booking_type=2 and c.is_short=2 and c.entry_form=87 THEN d.amount ELSE 0 END) as trims_main_amount,
				(CASE WHEN c.booking_type=2 and c.is_short=1 and  c.entry_form=87  THEN d.amount ELSE 0 END) as trims_short_amount,
				(CASE WHEN c.booking_type=5 and c.is_short=2 THEN d.amount ELSE 0 END) as trims_with_ord
				
				 from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c,wo_booking_dtls d  where a.job_no=b.job_no_mst  and b.id=d.po_break_down_id and d.booking_no=c.booking_no and c.booking_type in(2,5)  and  b.status_active!=0 and  c.status_active=1 and  d.status_active=1 and  c.item_category=4 and c.is_deleted=0 and d.wo_qnty>0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond   $po_cond_for_in2 ";
				
				$trim_result=sql_select($sql_trims_book);
				$sum_total_trims_amount=0;$amount=0;$rate=0;$avg_rate=0;$sumary_total_trims_amount=0;
				foreach($trim_result as $row)
				{
					$avg_rate=$row[csf('amount')]/$row[csf('wo_qnty')];
					$rate=($avg_rate/$row[csf('exchange_rate')]);
					//$rate=$rate*1;
					$amount=number_format($row[csf('wo_qnty')]*$rate,6,'.','');// number_format($avg_rate,'.','');$row[csf('trims_main_amount')]
					$sum_total_trims_amount+=$amount+$row[csf('trims_short_amount')];
					$sumary_total_trims_amount+=$amount;
					
					$description=$trims_data_des_arr[$row[csf('dtls_id')]][$row[csf('booking_no')]][$row[csf('trim_group')]][$row[csf('uom')]]['description'];
					//$description=implode(",",array_unique(explode(",",$description_data)));
					
					$trims_booking_arr[$row[csf('booking_no')]][$row[csf('trim_group')]][$description][$row[csf('uom')]]['po_id'].=$row[csf('po_id')].',';
					$trims_booking_arr[$row[csf('booking_no')]][$row[csf('trim_group')]][$description][$row[csf('uom')]]['po_number'].=$row[csf('po_number')].',';
					$trims_booking_arr[$row[csf('booking_no')]][$row[csf('trim_group')]][$description][$row[csf('uom')]]['job_no'].=$row[csf('job_no')].',';
					$trims_booking_arr[$row[csf('booking_no')]][$row[csf('trim_group')]][$description][$row[csf('uom')]]['style_ref_no'].=$row[csf('style_ref_no')].',';
					$trims_booking_arr[$row[csf('booking_no')]][$row[csf('trim_group')]][$description][$row[csf('uom')]]['trims_main_amount']+=$row[csf('trims_main_amount')];
					$trims_booking_arr[$row[csf('booking_no')]][$row[csf('trim_group')]][$description][$row[csf('uom')]]['trims_short_amount']+=$row[csf('trims_short_amount')];			
					$trims_booking_arr[$row[csf('booking_no')]][$row[csf('trim_group')]][$description][$row[csf('uom')]]['trims_with_ord_amount']+=$row[csf('trims_with_ord')];
					$trims_booking_arr[$row[csf('booking_no')]][$row[csf('trim_group')]][$description][$row[csf('uom')]]['booking_date']=$row[csf('booking_date')];
					$trims_booking_arr[$row[csf('booking_no')]][$row[csf('trim_group')]][$description][$row[csf('uom')]]['book_type']=$row[csf('book_type')];
					$trims_booking_arr[$row[csf('booking_no')]][$row[csf('trim_group')]][$description][$row[csf('uom')]]['currency_id']=$row[csf('currency_id')];
					$trims_booking_arr[$row[csf('booking_no')]][$row[csf('trim_group')]][$description][$row[csf('uom')]]['booking_type']=$row[csf('booking_type')];
					$trims_booking_arr[$row[csf('booking_no')]][$row[csf('trim_group')]][$description][$row[csf('uom')]]['supplier_id']=$row[csf('supplier_id')];
					$trims_booking_arr[$row[csf('booking_no')]][$row[csf('trim_group')]][$description][$row[csf('uom')]]['is_short']=$row[csf('is_short')];
					$trims_booking_arr[$row[csf('booking_no')]][$row[csf('trim_group')]][$description][$row[csf('uom')]]['wo_qnty']+=$row[csf('wo_qnty')];
					$trims_booking_arr[$row[csf('booking_no')]][$row[csf('trim_group')]][$description][$row[csf('uom')]]['rate']=$row[csf('amount')]/$row[csf('wo_qnty')];
					$trims_booking_arr[$row[csf('booking_no')]][$row[csf('trim_group')]][$description][$row[csf('uom')]]['amount']+=$row[csf('amount')];
					$trims_booking_arr[$row[csf('booking_no')]][$row[csf('trim_group')]][$description][$row[csf('uom')]]['exchange_rate']=$row[csf('exchange_rate')];
					$trims_booking_arr[$row[csf('booking_no')]][$row[csf('trim_group')]][$description][$row[csf('uom')]]['fabric_source']=$row[csf('fabric_source')];
					$trims_booking_arr[$row[csf('booking_no')]][$row[csf('trim_group')]][$description][$row[csf('uom')]]['item_category']=$row[csf('item_category')];
					$trims_booking_arr[$row[csf('booking_no')]][$row[csf('trim_group')]][$description][$row[csf('uom')]]['is_approved']=$row[csf('is_approved')];
					
					
				} 
				//print_r($trims_booking_arr);
				
				
				//print_r($mrr_recv_arr);
					/*$sql_gen_acc= "select $group_con2,c.issue_number,c.supplier_id,c.issue_date,d.prod_id,e.product_name_details,e.item_group_id,d.cons_uom,
				 sum(f.issue_qnty) as cons_quantity,avg(d.cons_rate) as rate,f.recv_trans_id,
				sum(d.cons_amount) as amount,
				sum(CASE WHEN d.transaction_type=2  and c.entry_form=21 THEN d.cons_amount ELSE 0 END) as gen_acc_amount,
				sum(CASE WHEN d.transaction_type=2  and c.entry_form=21 THEN d.cons_quantity ELSE 0 END) as gen_acc_cons_qty
				 from wo_po_details_master a, wo_po_break_down b, inv_issue_master c,inv_transaction d,product_details_master e,inv_mrr_wise_issue_details f  where a.job_no=b.job_no_mst  and b.id=d.order_id and c.id=d.mst_id and d.transaction_type in(2) and f.issue_trans_id=d.id and e.id=f.prod_id and c.entry_form=21 and f.entry_form=21  and e.id=d.prod_id and  d.status_active=1 and  d.item_category=4 and c.is_deleted=0 and d.cons_quantity>0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond   $po_cond_for_in3 group by c.issue_number,c.supplier_id,c.issue_date,d.prod_id,e.product_name_details,e.item_group_id,d.cons_uom,f.recv_trans_id";*/
				 $sql_gen_acc= "select $group_con2,c.issue_number,c.supplier_id,c.issue_date,d.prod_id,e.product_name_details,e.item_group_id,d.cons_uom,
				 sum(f.issue_qnty) as cons_quantity,avg(d.cons_rate) as rate,f.recv_trans_id,
				sum(d.cons_amount) as amount,
				sum(CASE WHEN d.transaction_type=2  and c.entry_form=21 THEN d.cons_amount ELSE 0 END) as gen_acc_amount,
				sum(CASE WHEN d.transaction_type=2  and c.entry_form=21 THEN d.cons_quantity ELSE 0 END) as gen_acc_cons_qty
				 from wo_po_details_master a, wo_po_break_down b, inv_issue_master c,inv_transaction d,product_details_master e,inv_mrr_wise_issue_details f  where a.job_no=b.job_no_mst  and b.id=d.order_id and c.id=d.mst_id and d.transaction_type in(2) and f.issue_trans_id=d.id and e.id=f.prod_id and c.entry_form=21 and f.entry_form=21  and e.id=d.prod_id and  d.status_active=1 and  d.item_category=4 and b.status_active!=0 and c.is_deleted=0 and d.cons_quantity>0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond   $po_cond_for_in6 group by c.issue_number,c.supplier_id,c.issue_date,d.prod_id,e.product_name_details,e.item_group_id,d.cons_uom,f.recv_trans_id";
				$gen_trim_result=sql_select($sql_gen_acc);
				$sum_total_accesso_amount=0;
				//$exchane_rate=return_field_value("conversion_rate","currency_conversion_rate"," currency=2  and is_deleted=0 and status_active=1 order by id desc");
				$trans_rec_ids='';
				foreach($gen_trim_result as $rows)
				{
					if($trans_rec_ids=='') $trans_rec_ids=$rows[csf('recv_trans_id')];else $trans_rec_ids.=",".$rows[csf('recv_trans_id')];
				}
				$all_transID=array_unique(explode(",",$trans_rec_ids));
					
					$trans_arr_cond=array_chunk($all_transID,1000, true);
					$trans_arr_cond_in="";
					$t=0;
					foreach($trans_arr_cond as $key=>$value)
					{
					   if($t==0)
					   {
						$trans_arr_cond_in=" and f.recv_trans_id  in(".implode(",",$value).")"; 
						
					   }
					   else 
					   {
						$trans_arr_cond_in.=" or f.recv_trans_id  in(".implode(",",$value).")";
						
					   }
					   $t++;
					}	//
					
				$sql_mrr_recv= "select a.recv_number,b.id as trans_id,f.issue_qnty,f.issue_trans_id,b.order_rate,b.order_ile,e.product_name_details,e.item_group_id
						 from  inv_receive_master a, inv_transaction b,product_details_master e,inv_mrr_wise_issue_details f  where a.id=b.mst_id and e.id=b.prod_id  and f.recv_trans_id=b.id and b.transaction_type=1 and a.entry_form=20 and a.company_id=$cbo_company_name  and b.status_active=1 and  b.is_deleted=0  $trans_arr_cond_in ";
				$mrr_recv_result=sql_select($sql_mrr_recv);
				$mrr_recv_arr=array();
				foreach($mrr_recv_result as $row)
				{
					$mrr_recv_arr[$row[csf('trans_id')]]['mrr_no']=$row[csf('recv_number')];
					$mrr_recv_arr[$row[csf('trans_id')]]['issue_trans_id']=$row[csf('issue_trans_id')];
					$mrr_recv_arr[$row[csf('trans_id')]]['issue_qnty']=$row[csf('issue_qnty')];
					$mrr_recv_arr[$row[csf('trans_id')]]['item_group_id']=$row[csf('item_group_id')];
					$mrr_recv_arr[$row[csf('trans_id')]]['desc']=$row[csf('product_name_details')];
					$mrr_recv_arr[$row[csf('trans_id')]]['rate']=$row[csf('order_rate')]+$row[csf('order_ile')];
				}
				foreach($gen_trim_result as $row) //For Summary
				{
					$itemIdArr[$row[csf("item_group_id")]]=$row[csf("item_group_id")];
				}
				$itemId_cond=where_con_using_array($itemIdArr,0,'a.id');
				$conv_sql="select a.id as item_id,a.conversion_factor from lib_item_group a,product_details_master b where a.id=b.item_group_id and b.entry_form=20 and b.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $itemId_cond ";
				$sql_conv_result=sql_select($conv_sql);
				foreach($sql_conv_result as $row)
				{
					$conversion_arr[$row[csf("item_id")]]['conver_rate']=$row[csf("conversion_factor")];
				}

				foreach($gen_trim_result as $row) //For Summary
				{
					$converrate=$conversion_arr[$row[csf("item_group_id")]]['conver_rate'];
					 $orderrate=($mrr_recv_arr[$row[csf('recv_trans_id')]]['rate']/$converrate);
					 $gen_amount=$row[csf('cons_quantity')]*$orderrate;
					//echo $amount=number_format($rows[csf('cons_quantity')]*$gen_rate,6,'.','');
					$sum_total_accesso_amount+=number_format($gen_amount,6,'.','');
				}
				 $sql_gen_acc_dzn= "select b.id as po_id,e.item_group_id,f.recv_trans_id,
				sum(f.issue_qnty) as issue_qnty,
				sum(CASE WHEN d.transaction_type=2  and c.entry_form=21 THEN d.cons_amount ELSE 0 END) as gen_acc_amount,
				sum(CASE WHEN d.transaction_type=2  and c.entry_form=21 THEN d.cons_quantity ELSE 0 END) as gen_acc_cons_qty
				 from wo_po_details_master a, wo_po_break_down b, inv_issue_master c,inv_transaction d,product_details_master e,inv_mrr_wise_issue_details f  where a.job_no=b.job_no_mst  and b.id=d.order_id and c.id=d.mst_id and f.issue_trans_id=d.id and e.id=f.prod_id and d.transaction_type in(2) and e.id=d.prod_id and  d.status_active=1 and  d.item_category=4 and c.entry_form=21  and b.status_active!=0 and c.is_deleted=0 and d.cons_quantity>0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond   $po_cond_for_in6 group by b.id,e.item_group_id,f.recv_trans_id";
				$gen_dzn_result=sql_select($sql_gen_acc_dzn);
				
				foreach($gen_dzn_result as $rows)
				{
					
					$converrate_dzn=$conversion_arr[$rows[csf("item_group_id")]]['conver_rate'];
					 $orderrate_dzn=($mrr_recv_arr[$rows[csf('recv_trans_id')]]['rate']/$converrate_dzn);
					 $gen_amount_dzn=$rows[csf('issue_qnty')]*$orderrate_dzn;
					$gen_acces_data_arr[$rows[csf('po_id')]]['amt']+=$gen_amount_dzn;
					
				}
				//print_r($gen_acces_data_arr);
				
				  $sql_aop_book= "select c.booking_date,c.booking_no,c.is_approved,c.fabric_source,c.item_category,c.booking_type,$group_con,d.pre_cost_fabric_cost_dtls_id as fab_dtls_id,d.fabric_color_id,d.gmts_color_id as gmts_color_id,d.uom,d.dia_width as dia_width,d.gsm_weight,d.color_type, sum(d.wo_qnty) as wo_qnty,avg(d.rate) as rate,
						sum(d.amount) as amount,
						sum(CASE WHEN d.booking_type=3 THEN d.amount ELSE 0 END) as aop_amount,
						sum(CASE WHEN d.booking_type=3 THEN d.wo_qnty ELSE 0 END) as aop_wo_qty
						
						 from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c,wo_booking_dtls d  where a.job_no=b.job_no_mst  and b.id=d.po_break_down_id and d.booking_no=c.booking_no and c.booking_type in(3) and d.booking_type in(3)  and  b.is_deleted=0 and b.status_active!=0 and  c.status_active=1 and  c.item_category=12  and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.wo_qnty>0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond   $po_cond_for_in2 group by c.booking_no,c.booking_date,c.booking_type,c.is_approved,c.fabric_source,c.item_category,d.fabric_color_id,d.gmts_color_id,d.uom,d.dia_width,d.gsm_weight,d.color_type,d.pre_cost_fabric_cost_dtls_id";
				$aop_result=sql_select($sql_aop_book);
				$sum_total_aop_amount=0;$aop_po_data_arr=array();
				foreach($aop_result as $rows)
				{
					$po_num=array_unique(explode(",",$rows[csf('po_id')]));
					foreach($po_num as $po)
					{
						$aop_po_data_arr[$po]['aop']+=$rows[csf('amount')];
					}
					$sum_total_aop_amount+=$rows[csf('amount')];
					
				}
				
					if($db_type==2)
					{
						$group_con_lab="LISTAGG(cast(d.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY d.po_id) as po_id";
					}
					else
					{
						$group_con_lab="group_concat(distinct(d.po_id)) as po_id";
						
					}//fin_fab_qnty
				/*  $sql_lab_book="select c.id mst_id,c.labtest_no,c.supplier_id,d.test_for,sum(e.wo_value) as lab_rate,d.test_item_id,$group_con_lab, avg(d.labtest_charge) as rate,sum(d.wo_value) as amount
						 from wo_po_details_master a, wo_po_break_down b, wo_labtest_mst c,wo_labtest_dtls d,wo_labtest_order_dtls e  where a.job_no=b.job_no_mst   and a.job_no=d.job_no and c.id=d.mst_id and e.dtls_id=d.id and e.order_id=b.id and c.status_active=1 and b.status_active!=0 and d.status_active=1 and e.status_active=1 and  c.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond   $po_cond_for_in5 group by c.labtest_no,c.id,d.test_for,d.test_item_id,c.supplier_id";*/
				 $sql_lab_book= "select c.id mst_id,c.labtest_no,c.supplier_id,d.test_for,sum(d.wo_value) as lab_rate,d.test_item_id,$group_con_lab, avg(d.labtest_charge) as rate,
						sum(d.wo_value) as amount
						 from wo_po_details_master a, wo_po_break_down b, wo_labtest_mst c,wo_labtest_dtls d  where a.job_no=b.job_no_mst  and a.job_no=d.job_no and c.id=d.mst_id  and d.po_id=b.id and c.status_active=1 and  c.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond   $po_cond_for_in3 group by c.labtest_no,c.id,d.test_for,d.test_item_id,c.supplier_id";
				$lab_result=sql_select($sql_lab_book);
				$sum_total_lab_amount=0;$lab_po_data_arr=array();
				foreach($lab_result as $rows)
				{
					$po_num=array_unique(explode(",",$rows[csf('po_id')]));
					foreach($po_num as $po)
					{
						$lab_po_data_arr[$po]['lab']+=$rows[csf('amount')];
					}
					 $sum_total_lab_amount+=$rows[csf('amount')];
				}
				
				//echo $tot_sum_total_trims_with_ord_amount;
				if($db_type==2)
					{
					//	$group_con="LISTAGG(cast(a.job_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.job_no) as job_no,LISTAGG(cast(b.po_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.po_number) as po_number,LISTAGG(cast(a.style_ref_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.style_ref_no) as style_ref_no";
						$group_con2="LISTAGG(cast(b.id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_id";
					}
					else
					{
						//$group_con="group_concat(distinct(a.job_no)) as job_no,group_concat(distinct(a.style_ref_no)) as style_ref_no ,group_concat(distinct(b.po_number)) as po_number";
						$group_con2="group_concat(distinct(b.id)) as job_no,group_concat(distinct(b.id)) as po_id";
					}
						 $sql_embl_book= "select sum(b.po_total_price) as po_total_price,c.booking_date,c.fabric_source,c.item_category,c.is_approved,c.supplier_id,c.pay_mode,c.booking_no,$group_con,d.pre_cost_fabric_cost_dtls_id as emb_id,d.uom,d.emblishment_name, sum(d.wo_qnty) as wo_qnty,avg(d.rate) as rate,sum(d.amount) as amount from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c,wo_booking_dtls d  where a.job_no=b.job_no_mst and a.job_no=c.job_no and b.id=d.po_break_down_id and d.booking_no=c.booking_no and c.booking_type in(6)  and b.status_active!=0 and  c.status_active=1 and  c.item_category=25 and c.is_deleted=0 and  d.status_active=1  and d.wo_qnty>0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond  group by c.booking_no,c.fabric_source,c.supplier_id,c.pay_mode,c.item_category,c.is_approved, c.booking_date,d.pre_cost_fabric_cost_dtls_id,d.emblishment_name,d.uom";
						$emb_result=sql_select($sql_embl_book);
						$sum_total_embl_amount=0;
					foreach($emb_result as $rows)
					{
						$sum_total_embl_amount+=$rows[csf('amount')];
					}
					//echo $sum_total_embl_amount;
						$total_raw_metarial_cost=0;
			 		$total_raw_metarial_cost=$sum_total_embl_amount+$sum_total_fab_amount+$sumary_total_trims_amount+$sum_total_aop_amount+$sum_total_lab_amount+$sum_total_accesso_amount;
					//echo $sum_total_embl_amount.'=='.$sum_total_fab_amount.'=='.$sum_total_trims_amount.'=='.$sum_total_aop_amount.'=='.$sum_total_lab_amount.'=='.$sum_total_accesso_amount;
					$condition= new condition();
					$condition->company_name("=$cbo_company_name");
				  if(str_replace("'","",$cbo_buyer_name)>0){
					  $condition->buyer_name("=$cbo_buyer_name");
				 }
				 if($txt_order_id!='' || $txt_order_id!=0)
				 {
					$condition->po_id("in($txt_order_id)"); 
				 }
				 if(str_replace("'","",$txt_style_ref)!='')
				 {
					//echo "in($txt_order_id)".'dd';die;
					$condition->job_no("in($all_jobs)");
				 }
				$condition->init();
				$emblishment= new emblishment($condition);
				$trims= new trims($condition);
				$wash= new wash($condition);
				//echo $trims->getQuery(); die;
				$trims_ReqQty_arr=$trims->getQtyArray_by_precostdtlsid();//getQtyArray_by_jobAndPrecostdtlsid();
				//print_r($trims_ReqQty_arr);
				$trims= new trims($condition);
				$trims_costing_arr=$trims->getAmountArray_precostdtlsid();//getAmountArray_by_jobAndPrecostdtlsid();
				//print_r($trims_ReqQty_arr);
				//echo $emblishment->getQuery(); die;
				$emblishment_costing_arr=$emblishment->getAmountArray_by_order();
				$emblishment_qty_arr=$emblishment->getQtyArray_by_jobEmbnameAndEmbtype();
				//$emblishment= new emblishment($condition);
				$emblishment_amount_arr=$emblishment->getAmountArray_by_jobEmbnameAndEmbtype();
				
				$wash_qty_arr=$wash->getQtyArray_by_jobEmbnameAndEmbtype();
				$wash_amount_arr=$wash->getAmountArray_by_jobEmbnameAndEmbtype();
				
				$commission= new commision($condition);
				$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
				$fabric= new fabric($condition);
				$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
			//print_r($fabric_costing_arr);
	
				$style1="#E9F3FF"; 
				$style="#FFFFFF";
	?>
   
       
        <div style="width:100%">
             <table width="1100px" style="margin-left:10px">
             <tr class="form_caption">
                    <td colspan="10" align="center"><strong>Cost Break Up Report</strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="10" class="form_caption"><? echo $company_library[$cbo_company_name].'<br>';if($cbo_style_owner!=0) echo 'Style Owner: '.$company_library[$cbo_style_owner]; ?></td>
                </tr>
            </table>
            <table width="auto" style="margin-left:10px" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
            <tr>
            <td>
            
            <table width="auto" style="margin-left:10px" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_12">
               <caption> <strong>Summary</strong></caption>
                	
                    <tr bgcolor="<? echo $style1;?>" align="left">
                     	<td width="160"><b>Buyer &nbsp;&nbsp;</b> </td>
                     	<td>  <? 
						$total_fob_value_with_upcharge=$total_fob_value+$total_order_upcharge;
						if($cbo_buyer_name==0) echo implode(",",array_unique(explode(",",$all_buyer)));
						else echo $buyer_arr[$cbo_buyer_name];?></td>
                    </tr>
					  <tr bgcolor="<? echo $style; ?>" align="left">
                     	<td width="160"><b>Client &nbsp;&nbsp;</b> </td>
                     	<td>  <? 
						
						//if($cbo_buyer_name==0) echo implode(",",array_unique(explode(",",$all_buyer_client)));
						////else echo $buyer_arr[$all_buyer_client];
						echo implode(",",array_unique(explode(",",$all_buyer_client)));
						?></td>
                    </tr>
					
                	<tr bgcolor="<? echo $style1;?>" align="left">
                        <td width="70"><b>Job No &nbsp;&nbsp; </b></td>
                        <td  width="auto">  <? echo implode(", ",array_unique(explode(",",$all_job)));?></td>
                    </tr>
                 	<tr bgcolor="<? echo $style ?>" align="left">
                         <td width="80" ><b>Style No&nbsp;&nbsp;</b></td>
                         <td width="auto">  <? echo implode(", ",array_unique(explode(",",$all_style)));?></td>
                    </tr>
                    <tr bgcolor="<? echo $style1 ?>" align="left">
                         <td width="80" ><b>Order UOM&nbsp;&nbsp;</b></td>
                         <td width="auto">  <? echo implode(", ",array_unique(explode(",",$all_order_uom)));?></td>
                    </tr>
                    <tr  bgcolor="<? echo $style?>" align="left">
                         <td width="80" ><b>Total Qty. In Pcs :</b></td>
                         <td align="left" id="po_qty_pcs_td">  <? $order_qty_pcs=$color_size_poQty;echo $order_qty_pcs;?></td>
                    </tr>
                     <tr  bgcolor="<? echo $style11;?>" align="left">
                          <td width="80"><b>Total FOB [$] :</b></td>
                          <td  align="left" id="total_fob_td">  <? $total_fob_value=0;$total_fob_value=$color_size_amt;echo number_format($total_fob_value,4);?></td>
                    </tr>
                     <tr  bgcolor="<? echo $style;?>" align="left">
                          <td width="80"><b>Total Up-Charge [$]:</b></td>
                          <td  align="left" id="total_fob_td_upcharge">  <? echo number_format($total_order_upcharge,4);?></td>
                    </tr>
                      <tr  bgcolor="<? echo $style1?>" align="left">
                          <td width="100"><b>Total FOB with Up-Charge[$] :</b></td>
                          <td  align="left" id="total_fob_td_with_upcharge">  <?  echo number_format($total_order_upcharge+$total_fob_value,4);?></td>
                    </tr>
                    <tr bgcolor="<? echo $style ?>" align="left">
                          <td width="80"><b>Comission [$] :</b></td>
                          <td align="left">  <? 
						  $po_ids=explode(",",$all_po_id);
						 
						  $total_embell_cost=0;
						  $total_commisssion=0;
						  $total_embl_amt=0; 
						  $total_trims_amt=0;
						  $total_finish_amt=0;
						  $foreign=0;$local=0;
						 // print_r($po_ids);
						  foreach($po_ids as $pid)
						  {
							 
							   $foreign+=$commission_costing_arr[$pid][1];
								$local+=$commission_costing_arr[$pid][2];
								$total_commisssion=$foreign+$local;
							    $total_embl_amt+=$emblishment_costing_arr[$pid];
							    $total_finish_amt+=$fabric_costing_arr['knit']['finish'][$pid][12];
							    $total_trims_amt+=$trims_costing_arr[$pid];
								//$total_raw_metarial_cost=$total_finish_amt+$total_embl_amt+$total_trims_amt;
						  }
						
						  echo number_format($total_commisssion,4);?></td>
                    </tr>
                    <tr bgcolor="<? echo $style1 ?>" align="left">
                         <td width="80"  title="Trims(<? echo number_format($sum_total_trims_amount); ?>)+Emblish(<? echo number_format($sum_total_embl_amount); ?>)+Fabric(<? echo number_format($sum_total_fab_amount); ?>)+AOP(<? echo number_format($sum_total_aop_amount); ?>)+Lab Test(<? echo number_format($sum_total_lab_amount); ?>)+Gen Accsso Amount(<? echo number_format($sum_total_accesso_amount); ?>)"><b>Raw Material Cost :</b></td>
                         <td id="td_sum_raw_material_cost">  <? 
						 
						 echo number_format($total_raw_metarial_cost,4);?></td>
                    </tr>
                    
                    <tr  bgcolor="<? echo $style?>" align="left">
                          <td width="80" title="Total Gross CM/Total Po qty)*12"><b>Gross CM/Dzn &nbsp;&nbsp;</b></td>
                          <td id="gross_cm_td">  <? echo number_format((($total_fob_value_with_upcharge-($total_raw_metarial_cost+$total_commisssion))/$order_qty_pcs)*12,4);?></td>
                    </tr>
                       
                    <tr bgcolor="<? echo $style1?>"  align="left">
                           <td width="80" title="Total Fob-Tot Raw Material Cost"><b>Gross CM Total [$] :</b></td>
                           <td id="gross_cm_total">  <? echo number_format($total_fob_value_with_upcharge-($total_raw_metarial_cost+$total_commisssion),4);?></td>
                    </tr> 
                      <tr  bgcolor="<? echo $style ?>" align="left">
                           <td width="80"><b>Style Description :</b></td>
                           <td width="auto">  <? echo implode(",",array_unique(explode(",",$all_style_desc)));?></td>
                    </tr> 
                    <tr  bgcolor="<? echo $style ?>" align="left">
                           <td width="80"><b>FOB/UNIT Price :</b></td>
                           <td width="auto" title="FOB Value/Order Qty ">  <? echo number_format($total_job_unit_price,4);///$tot_count ?></td>
                    </tr> 
           </table>
           </td>
            <td>
            <?
			 $img_sql="select master_tble_id,image_location from common_photo_library where form_name='knit_order_entry' and master_tble_id in(".$all_full_job.") and file_type=1 ";
			$res_img=sql_select($img_sql);
			?>
            <br>
            <br>
                     <table width="250" style="margin-left:35px" class="rpt_table" cellpadding="0" cellspacing="0" border="0" rules="all" id="table_header_1">
                         <?
                         foreach($res_img as $row)
						 {
						 ?>
                         <tr style="border:hidden">
                             <td width="250" valign="middle" style=" border: medium"><img src='../../../<? echo $row[csf('image_location')]; ?>'  height='100px' width='230px' /> </td>
                         </tr>
                         <?
						 }
						 ?>
                     </table>
           </tr>
           </table>
           <br/>
		    <table width="400"  style="margin-left:10px"  cellpadding="0" class="rpt_table"  rules="all" cellspacing="0" border="1">
			  
				<tr bgcolor="#E9F3FF">
					<td align=""><strong>Total Ship Value With Up Charge[$]</strong></td>
					<td  align="right"> <strong><? echo number_format($total_ship_value,2);
					$gross_cm_total_baseon_ship=($total_ship_value+$total_order_upcharge)-($total_commisssion+$total_raw_metarial_cost);
					$gross_cm_total_baseon_ship_dzn=($gross_cm_total_baseon_ship/$order_qty_pcs)*12;
					?></strong></td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td> <strong>Gross CM/Dzn Based on Shipment</strong> </td>
					<td  align="right" title="Gross CM Total/Total Qty.In Pcs*12"><? echo number_format($gross_cm_total_baseon_ship_dzn,2);?> </td>
				</tr>
				<tr bgcolor="#E9F3FF">
					<td> <strong>Gross CM Total[$]Based on Shipment</strong> </td>
					<td  align="right" title="Ship Value+Up Charge-TTL Commission-Tot Raw Material" ><?  echo number_format($gross_cm_total_baseon_ship,2);?> </td>
				</tr>
		  </table>
		   <br/>
           
           <table id="table_header_1"   class="rpt_table" width="980" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">PO Details</b></caption>
					<thead>
                    	<th width="30">SL</th>
						<th width="50">Job</th>
						<th width="100">PO Number</th>
                        <th width="80">Order Status</th>
						<th width="100">PO Qty. [Pcs]</th>
						<th width="80">FOB /Unit Price </th>
                        <th width="100">Total FOB Value </th> 
                        <th width="100">Up-Charge</th>
                        <th width="60">Ship Mode</th>
						<th width="100">Ori. ShipDate</th>
                        <th width="">Ship. Qty.</th>
						<th width="100">Ship. Value</th>
                        
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:1000px; max-height:400px;overflow-y:scroll;" align="left" id="scroll_body1">
					<table class="rpt_table" width="980" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
					$i=1;$total_exfact_qty=$total_po_shipment_value=0;$total_fob_val=0;$total_up_charge_val=$total_po_qty_pcs_qty=0;
					foreach($sql_po_result as $row)
					{
                   		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$status_active=$row[csf("status_active")];
						if($status_active==1)
						{
						$order_qty_pcss=$po_dtls_size_arr[$row[csf('po_id')]]['qty'];
						$order_amt=$po_dtls_size_arr[$row[csf('po_id')]]['amt'];
						
						$order_qty_pcss=$order_qty_pcss;//$row[csf('po_quantity')]*$row[csf('ratio')];
						//$unit_price=$row[csf('unit_price')];
						//if($row[csf("matrix_type")]==1) $avg_unit=$row[csf("unit_price")];else $avg_unit=$row[csf("avg_unit_price")];
						$exfactory_qty=$export_invoice_arr[$row[csf('po_id')]][1]['sea']+$export_invoice_arr[$row[csf('po_id')]][2]['air']+$export_invoice_arr[$row[csf('po_id')]][3]['road'];
						$avg_unit=0;
							if($po_color_size_arr[$row[csf('po_id')]]['amt']>0 && $po_color_size_arr[$row[csf('po_id')]]['qty']){
							$avg_unit=$po_color_size_arr[$row[csf('po_id')]]['amt']/$po_color_size_arr[$row[csf('po_id')]]['qty'];
							}
							$up_charge=$po_data_arr[$row[csf("po_id")]][up_charge];
						
					//echo $row[csf('po_id')];
					?>
                    
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="50"><? echo $row[csf('job_prefix')]; ?></td>
							<td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('po_number')]; ?></div></td>
                            <td width="80" align="center"><div style="word-break:break-all"><? echo $order_status[$row[csf('is_confirmed')]]; ?></div></td>
							<td width="100" align="center" ><div style="word-break:break-all"><? echo $order_qty_pcss; ?></div></td>
							<td width="80" title="Color Size Amount/Color Size Qty" align="right"><div style="word-break:break-all"><? echo number_format($avg_unit,2); ?></div></td>
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($order_amt,2); ?></div></td>
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($up_charge,2); ?></div></td>
                            <td width="60"><div style="word-break:break-all"><? echo $shipment_mode[$row[csf('ship_mode')]]; ?></div></td>
							<td width="100"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                            <td width="" align="right"><? echo number_format($exfactory_qty,0); ?></td>
							<td width="100" align="right"><? echo number_format($exfactory_qty*$avg_unit,0); ?></td>
                            </tr>
                            <?
							$total_exfact_qty+=$exfactory_qty;
							$total_fob_val+=$order_amt;
							$total_up_charge_val+=$up_charge;
							$total_po_qty_pcs_qty+=$order_qty_pcss;
							$total_po_shipment_value+=$exfactory_qty*$avg_unit;
							
							$i++;
						}
                            
					}
							?>
                            <tfoot>
                            <tr>
                                <th colspan="4"><strong>Total</strong> </th>
                                <th align="right"><strong><? echo number_format($total_po_qty_pcs_qty,2);?> </strong></th>
                                <th> </th>
                                <th align="right"><strong><? echo number_format($total_fob_val,2);?> </strong></th>
                                <th align="right"><strong><? echo number_format($total_up_charge_val,2);?> </strong></th>
                                
                                <th> </th>
                                <th align="right"><strong><? echo number_format($total_exfact_qty,0);?></strong> </th>
								<th align="right"><strong><? echo number_format($total_po_shipment_value,0);?></strong> </th>
                            </tr>
                            </tfoot>
                    
                    </table>
                    </div>
                    
            <br/><br/>
            <?
			//$po_cond_for_in
           
		$sql_trims= "select c.id,c.job_no,c.unit_price, c.trim_group,c.description as descrip,c.brand_sup_ref, c.cons_uom, c.cons_dzn_gmts,avg(d.cons) as totcons,avg(d.tot_cons) as tot_cons,avg(d.ex_cons) as ex_cons,avg(d.excess_per) as excess_per, c.rate, c.amount, c.apvl_req, c.nominated_supp,c.status_active from wo_pre_cost_trim_cost_dtls c,wo_pre_cost_trim_co_cons_dtls d  where   d.wo_pre_cost_trim_cost_dtls_id=c.id and  c.is_deleted=0 and c.status_active=1 and c.cons_dzn_gmts>0 $po_cond_for_in2 group by c.id,c.job_no,c.unit_price, c.trim_group,c.description,c.brand_sup_ref, c.cons_uom, c.cons_dzn_gmts, c.rate, c.amount, c.apvl_req, c.nominated_supp,c.status_active order by c.trim_group,c.job_no";
			$data_array2=sql_select($sql_trims);
			if(count($data_array2)>0)
			{
				//echo count($data_array2).'dsds';
			?>
              <br/><br/>
            <div><strong>Consumption and Unit Price Details : </strong></div>
           <table id="table_header_2" class="rpt_table" width="920" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                    <th width="40">SL</th>
						<th width="100">Item Group</th>
						<th width="150">Item Desc.</th>
						<th width="60">UOM</th>
					
                        <th width="70">Cons. / Dzn</th>
						<th width="50">Excess %</th>
                        <th width="70">Total</th>
                        <th width="70">Req./ Dzn</th>
                        <th width="100">Req. Qty.</th>
						<th width="70">Unit Price</th>
                        <th width="">Req./BOM Val</th>
                    </thead>
           </table>	
                    <div class="scroll_div_inner"  style="width:940px; max-height:400px;overflow-y:scroll;" align="left" id="scroll_body2">
					<table class="rpt_table" width="920" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2">
                    <?
									
					$i=1;$total_trims_req_qty=0;$total_trims_cost=0;
					foreach($data_array2 as $row)
					{
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$trims_req_qty=$trims_ReqQty_arr[$row[csf('id')]];
					$trims_cost=$trims_costing_arr[$row[csf('id')]];
					if($row[csf('descrip')]=='') $desc=0;else $desc=$row[csf('descrip')];
					$trims_avg_cons_arr[$row[csf('trim_group')]][$row[csf('cons_uom')]]['avg_cons']=$row[csf('cons_dzn_gmts')];
					if($trims_cost!='') $trims_cost=$trims_cost;else $trims_cost=0;
					if($trims_req_qty!='') $trims_req_qty=$trims_req_qty;else $trims_req_qty=0;
					//echo $trims_cost.'='.$trims_req_qty;
					$agv_rate=$trims_cost/$trims_req_qty;
					$agvrate=number_format($agv_rate,4);
					if($agvrate=='nan') $agvrate=0;else $agvrate=$agvrate;
					//$order_qty_pcss=$row[csf('po_quantity')]*$row[csf('ratio')];
					$trims_req_dzn=($row[csf('tot_cons')]+($row[csf('tot_cons')]*$row[csf('excess_per')])/100)
					//getQtyArray_by_job
					//echo $row[csf('po_id')];c.cons_dzn_gmts
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trt_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trt_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="100" title="<? echo $row[csf('job_no')];?>"><p><? echo $item_library[$row[csf('trim_group')]]; ?></p></td>
							<td width="150" align="center"><p><? echo $row[csf('descrip')]; ?></p></td>
							<td width="60" align="center" ><div style="word-break:break-all"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></div></td>
							
                            <td width="70" align="right"><div style="word-break:break-all"><? echo number_format($row[csf('tot_cons')],4); ?></div></td>
							<td width="50" align="center"><? echo number_format($row[csf('excess_per')],4); ?></td>
                            <td width="70" align="center"><? echo number_format($row[csf('totcons')],4); ?></td>
                            <td width="70" align="right" title="Cons/Dzn+(Cons/Dzn*Excess%)/100"><p><? echo number_format($trims_req_dzn,4); ?></p></td>
							<td width="100" align="right"><? echo number_format($trims_req_qty,4); ?></td>
                            <td width="70" align="right"><? echo $agvrate; ?></td>
                             <td width="" align="right"><? echo number_format($trims_cost,4); ?></td>
                            </tr>
                            <?
							$total_trims_req_qty+=$trims_req_qty;
							$total_trims_cost+=$trims_cost;
							$i++;
                            
					}
							?>
                            <tfoot>
                            <tr>
                            <th colspan="7" align="right">Total </th>
                            <th align="right"><? //echo number_format($total_fob_price,2);?> </th>
                            <th align="right"><? //echo number_format($total_trims_req_qty,2);?>  </th>
                            <th> </th>
                            <th align="right"><? echo number_format($total_trims_cost,4);?> </th>
                            </tr>
                            </tfoot>
                    
                    </table>
                    </div>
           <?
			}
			?>
            <br/>
            <table id="table_header_3" class="rpt_table" width="660" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left"> Emblishment Cost as per BOM	</b></caption>
					<thead>
                   		 <th width="40">SL</th>
						<th width="120">Emblishment Name</th>
                        <th width="150">Emblishment Type</th>
						<th width="100">Cons. / Dzn</th>
						<th width="80">Total Qty in Dzn</th>
                        <th width="70">Rate</th>
                         <th width="">Total Amount</th>
                    </thead>
          		</table>	
                    <div  class="scroll_div_inner"  style="width:660px; max-height:400px;overflow-y:scroll;" align="left" id="scroll_body3">
					<table class="rpt_table" width="640" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body3">
                    <?
					//print_r($emblishment_amount_arr);
					$sql_pre_embl= "select c.job_no,c.emb_name,c.emb_type,avg(c.cons_dzn_gmts) as cons_dzn_gmts, avg(c.rate) as rate,sum(c.amount) as amount,sum(d.amount) as cons_amount,avg(d.rate) as cons_rate,avg(d.requirment) as requirment from wo_pre_cost_embe_cost_dtls c,wo_pre_cos_emb_co_avg_con_dtls d  where   d.pre_cost_emb_cost_dtls_id=c.id and c.job_no=d.job_no and  c.is_deleted=0 and c.status_active=1 and c.cons_dzn_gmts>0 $po_cond_for_in2  group by c.job_no,c.emb_name,c.emb_type  order by c.emb_name,c.emb_type";
			$result_embl=sql_select($sql_pre_embl);
				foreach($result_embl as $row)
				{
					$pre_cost_embl_arr[$row[csf('emb_name')]][$row[csf('emb_type')]]['cons_dzn_gmts']=$row[csf('cons_dzn_gmts')];
					$pre_cost_embl_arr[$row[csf('emb_name')]][$row[csf('emb_type')]]['amount']=$row[csf('amount')];
					$pre_cost_embl_arr[$row[csf('emb_name')]][$row[csf('emb_type')]]['job_no']=$row[csf('job_no')];
					$pre_cost_embl_arr[$row[csf('emb_name')]][$row[csf('emb_type')]]['cons_dzn_gmts']=$row[csf('cons_dzn_gmts')];
					$pre_cost_embl_arr[$row[csf('emb_name')]][$row[csf('emb_type')]]['cons_rate']=$row[csf('cons_rate')];
					$pre_cost_embl_arr[$row[csf('emb_name')]][$row[csf('emb_type')]]['cons_amount']+=$row[csf('cons_amount')];
				}
					
					$i=1;$total_qnty=0;$total_amount=0;
					foreach($pre_cost_embl_arr as $emb_name=>$emb_data)
					{
						foreach($emb_data as $emb_type=>$row)
						{
						
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$cons_dzn_gmts=$row[('cons_dzn_gmts')];
						if($emb_name!=3)
						{
						$cons_qty=$emblishment_qty_arr[$row['job_no']][$emb_name][$emb_type];
						$cons_rate=$row[('cons_rate')];
						$cons_amount=$emblishment_amount_arr[$row['job_no']][$emb_name][$emb_type];
						}
						else
						{
							$cons_qty=$wash_qty_arr[$row['job_no']][$emb_name][$emb_type];
							$cons_rate=$row[('cons_rate')];
							$cons_amount=$wash_amount_arr[$row['job_no']][$emb_name][$emb_type];
						}
					
					?>
                    
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trfabembl_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trfabembl_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
                            <td width="120" title=""><? echo $emblishment_name_array[$emb_name]; ?></td>
							<td width="150"><? echo $emblishment_print_type[$emb_type];?></td>
							<td width="100" align="right" ><div style="word-break:break-all"><? echo number_format($cons_dzn_gmts,2); ?></div></td>
							<td width="80" align="right"><div style="word-break:break-all"><? echo number_format($cons_qty,4); ?></div></td>
                          
                            <td width="70" align="right"><div style="word-break:break-all"><? echo number_format($cons_amount/$cons_qty,4); ?></div></td>
                            <td width="" align="right"><div style="word-break:break-all"><? echo number_format($cons_amount,2); ?></div></td>
                            </tr>
                            <?
							$total_amount+=$cons_amount;
							$total_qnty+=$cons_qty;
							$i++;
						}
					}
							?>
                            <tfoot>
                            <tr>
                            <th colspan="4" align="right">Total </th>
                             <th align="right"><? echo number_format($total_qnty,2);?> </th>
                             <th> <? //echo number_format($total_fin_fab_qnty,2);?></th>
                             <th align="right"> <? echo number_format($total_amount,2);?></th>
                             </tr>
                            </tfoot>
                    
                    </table>
                    </div>
                    
              <br/><br/>
            <?
			if($db_type==2)
					{
						$group_cons="LISTAGG(cast(d.po_break_down_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY d.po_break_down_id) as po_id";
					}
					else
					{
					$group_cons="group_concat(distinct d.po_break_down_id)";	
					}
			//$row[csf('amount')]/$row[csf('grey_fab_qnty')]
			 $sql_fab= "select c.job_no,c.entry_form,c.booking_no,$group_cons,c.is_approved,c.fabric_source,c.item_category,sum(d.fin_fab_qnty) as fin_fab_qnty, sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.amount) as amount,c.is_short from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c,wo_booking_dtls d ,wo_po_color_size_breakdown e where a.job_no=b.job_no_mst and a.job_no=d.job_no and b.id=d.po_break_down_id and e.id=d.COLOR_SIZE_TABLE_ID and b.id=e.PO_BREAK_DOWN_ID and d.PO_BREAK_DOWN_ID=e.PO_BREAK_DOWN_ID and d.booking_no=c.booking_no and c.booking_type in(1,4) and c.short_booking_type not in(2,3) and b.status_active!=0 and c.status_active=1 and c.is_deleted=0   and e.status_active=1 and e.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and d.grey_fab_qnty>0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond  group by c.booking_no,c.item_category,c.job_no,c.fabric_source,c.is_approved,c.is_short,c.entry_form";
			
			$data_result=sql_select($sql_fab);
			if(count($data_result)<=0)
			{
			  $sql_fab= "select c.job_no,c.entry_form,c.booking_no,$group_cons,c.is_approved,c.fabric_source,c.item_category,sum(d.fin_fab_qnty) as fin_fab_qnty, sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.amount) as amount,c.is_short from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c,wo_booking_dtls d  where a.job_no=b.job_no_mst and a.job_no=d.job_no and b.id=d.po_break_down_id and d.booking_no=c.booking_no and c.booking_type in(1,4) and c.short_booking_type not in(2,3) and b.status_active!=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and d.grey_fab_qnty>0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond  group by c.booking_no,c.item_category,c.job_no,c.fabric_source,c.is_approved,c.is_short,c.entry_form";
				  $data_result=sql_select($sql_fab);
			}

			if(count($data_result)>0)
			{
				$print_report_format_ids=return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name."  and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
				$print_report_format_ids_short=return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name."  and module_id=2 and report_id=2 and is_deleted=0 and status_active=1");
		$button_print_ids=explode(",",$print_report_format_ids);
		$button_print_ids_short=explode(",",$print_report_format_ids_short);
		//print_r($button_ids);
			
		   ?>
           
           <table id="table_header_3" class="rpt_table" width="660" cellpadding="0" cellspacing="0" border="1" rules="all">
           <caption> <b style="float:left"> Fabric Booking Details</b></caption>
					<thead>
                    <th width="40">SL</th>
						<th width="120">Booking No</th>
                        <th width="150">Item Cat.</th>
						
						<th width="100">Req. Qty.</th>
						<th width="80">Unit Price</th>
                        <th width="">Req./BOM Val.</th>
						
                        
                    </thead>
          </table>	
                    <div  class="scroll_div_inner"  style="width:660px; max-height:400px;overflow-y:scroll;" align="left" id="scroll_body3">
					<table class="rpt_table" width="640" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body3">
                    <?
					
					$i=1;$total_grey_fab_qnty=0;$total_amount=0;
						
					foreach($data_result as $row)
					{
						
						
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
					//	$rate=$row[csf('rate')];
						$entry_form=$row[csf('entry_form')];
						//$fin_fab_qnty*$row[csf('rate')];
						$is_short=$row[csf('is_short')];
						
							if($is_short==2)
							{	
								foreach($button_print_ids as $row_id)
								{
									 if($row_id==45) //Urmi
										{ 
										 $button_name="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$cbo_company_name."','".$row[csf('po_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','show_fabric_booking_report_urmi','".$i."')\" '> ".$row[csf('booking_no')]."<a/>";
									   }
									   else
									   {
										 $button_name=$row[csf('booking_no')];  
									   }
								}
							}
							else
							{
									foreach($button_print_ids_short as $row_id)
									{
									 if($row_id==46) //Urmi
										{ 
										 $button_name="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$cbo_company_name."','".$row[csf('po_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$row_id."','show_fabric_booking_report_urmi','".$i."')\" '> ".$row[csf('booking_no')]."<a/>";
									   }
									   else
									   {
										 $button_name=$row[csf('booking_no')];  
									   }
								   }
							}
						/*}
						else
						{
							 $button_name="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$cbo_company_name."','".$row[csf('po_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','".$entry_form."','show_fabric_booking_report_urmi','".$i."')\" '> ".$row[csf('booking_no')]."<a/>";
							 
							 
						}*/
						$grey_fab_qnty=$row[csf('grey_fab_qnty')];
							 $avg_rate=$row[csf('amount')]/$grey_fab_qnty;
							$amount=$grey_fab_qnty*$avg_rate;
					?>
                    
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trfab_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trfab_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
                            <td width="120" title="<?  if($row_id==45) echo "Urmi";else echo "No Found Urmi Button"; ?>"><? echo $button_name; ?></td>
							<td width="150"><? echo $item_category[$row[csf('item_category')]];
							
							 ?></td>
							
							<td width="100" align="right" ><div style="word-break:break-all"><? echo number_format($grey_fab_qnty,2); ?></div></td>
							<td width="80" align="right"><div style="word-break:break-all"><? echo number_format($amount/$grey_fab_qnty,4); ?></div></td>
                            <td width="" align="right"><div style="word-break:break-all"><? echo number_format($amount,2); ?></div></td>
                            </tr>
                            <?
							$total_amount+=$amount;
							$total_grey_fab_qnty+=$grey_fab_qnty;
							$i++;
					}
							?>
                            <tfoot>
                            <tr>
                            <th colspan="3" align="right">Total </th>
                             <th align="right"><? echo number_format($total_grey_fab_qnty,2);?> </th>
                             <th> <? //echo number_format($total_fin_fab_qnty,2);?></th>
                             <th align="right"> <? echo number_format($total_amount,2);?></th>
                             </tr>
                            </tfoot>
                    
                    </table>
                    </div>
                    
           <br/><br/>
           <?
			}
		   ?>
            <br/><br/>
           <div  style="display:none">
           <table id="table_header_5" class="rpt_table" width="1840" cellpadding="0" cellspacing="0" border="1" rules="all">
           <caption> <strong>Cost Details / DZN </strong></caption>
					<thead>
                    <tr>
                   		<th width="40" rowspan="2">SL</th>
						<th width="100" rowspan="2">PO Number</th>
                        <th width="100"  rowspan="2">FOB Price/Dzn</th>
						<th colspan="2">Commission /Dzn</th>
                        <th colspan="2">Fabric</th>
                        <th colspan="2">Emblishment</th>
                        <th colspan="2">Accessories</th>
                        <th colspan="2">Gen. Accessories</th>
                        <th colspan="2">AOP</th>
                        <th colspan="2">Lab Test</th>
                        <th colspan="2">CM</th>
                    </tr>
                    <tr>
                     <th width="100">Est.</th>
                     <th width="100">Act.</th>
                      <th width="100">Est.</th>
                      <th width="100">Act.</th>
                      <th width="100">Est.</th>
                      <th width="100">Act.</th>
                      <th width="100">Est.</th>
                      <th width="100">Act.</th>
                      <th width="100">Est.</th>
                      <th width="100">Act.</th>
                      <th width="100">Est.</th>
                      <th width="100">Act.</th>
                      <th width="100">Est.</th>
                      <th width="100">Act.</th>
                      <th width="100">Est.</th>
                      <th width="">Act.</th>
                    </tr>
                    </thead>
                    
       			</table>
                    <div  class="scroll_div_inner" style="width:1860px; max-height:400px;overflow-y:scroll;" align="left" id="scroll_body">
					<table class="rpt_table" width="1840" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                    <?
					
					//$body_part_id_arr=return_library_array( "select id,body_part_id from wo_pre_cost_fabric_cost_dtls ", "id", "body_part_id");
				/*$sql_fab_arr= "select c.id,c.color_type_id,c.fabric_description,c.job_no,c.item_number_id,c.body_part_id,c.uom,d.po_break_down_id as po_id, sum(c.amount) as amount  from
 wo_pre_cost_fabric_cost_dtls c,wo_pre_cos_fab_co_avg_con_dtls d
 where c.job_no=c.job_no and  d.pre_cost_fabric_cost_dtls_id=c.id and c.fab_nature_id=2 $po_cond_for_in2 group by  c.id,
  c.item_number_id,c.color_type_id,c.fabric_description,c.body_part_id,c.job_no,c.uom, d.po_break_down_id";
					 $fab_results=sql_select($sql_fab_arr);
					  foreach( $fab_results as $row )
					  {
						 $fab_data_arr[$row[csf("id")]]['body']=$row[csf("body_part_id")];
						 $fab_data_arr[$row[csf("id")]]['item']=$row[csf("item_number_id")];
						 $fab_data_arr[$row[csf("id")]]['uom']=$row[csf("uom")];
						//  $fab_data_arr[$row[csf("id")]]['des']=$row[csf("fabric_description")];
						  //$fab_data_arr[$row[csf("id")]]['color']=$row[csf("color_type_id")];
					  }*/
					    $sql_fab_arr2= "select d.id as conv_id,c.color_type_id,c.fabric_description,c.body_part_id from
 wo_pre_cost_fabric_cost_dtls c, wo_pre_cost_fab_conv_cost_dtls d,wo_po_break_down b
 where c.job_id=d.job_id and  d.fabric_description=c.id and c.job_id=b.job_id  and d.job_id=b.job_id $po_cond_for_in2  group by  d.id,c.body_part_id,c.fabric_description,c.color_type_id";
					 $fab_results2=sql_select($sql_fab_arr2);
					  foreach( $fab_results2 as $row )
					  {
						 $fab_data_arr2[$row[csf("conv_id")]]['body']=$row[csf("body_part_id")];
						 $fab_data_arr2[$row[csf("conv_id")]]['des']=$row[csf("fabric_description")];
						//  $fab_data_arr2[$row[csf("conv_id")]]['conv_id']=$row[csf("conv_id")];
						 $fab_data_arr2[$row[csf("conv_id")]]['color']=$row[csf("color_type_id")];
					  }
					if($db_type==2) $group_cn="LISTAGG(cast(c.uom as varchar2(4000)), ',') WITHIN GROUP (ORDER BY c.uom) as uom";
					else if($db_type==0) $group_cn="group_concat(c.uom) as uom";
					
					/* $sql_fab= "select c.job_no,$group_cn,d.po_break_down_id as po_id, sum(c.amount) as amount  from
 wo_pre_cost_fabric_cost_dtls c,wo_pre_cos_fab_co_avg_con_dtls d
 where c.job_no=c.job_no and  d.pre_cost_fabric_cost_dtls_id=c.id and c.fab_nature_id=2 $po_cond_for_in2 group by 
  c.job_no, d.po_break_down_id";*/
  $sql_fab= "select c.job_no,$group_cn,b.id as po_id,b.po_number, sum(c.amount) as amount  from
 wo_pre_cost_fabric_cost_dtls c,wo_pre_cos_fab_co_avg_con_dtls d,wo_po_details_master a, wo_po_break_down b
 where c.job_id=d.job_id and  d.pre_cost_fabric_cost_dtls_id=c.id and a.id=b.job_id and a.id=c.job_id and b.id=d.po_break_down_id  and c.fab_nature_id=2  and b.status_active!=0 $po_cond_for_in2  $company_name_cond group by  c.job_no,b.id,b.po_number";
					$i=1;$total_po_cm_val=0;
					$fab_result=sql_select($sql_fab);
					$uom_data_arr=array();
					foreach($fab_result as $row)
					{
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					//$grey_fab_qnty=$row[csf('grey_fab_qnty')];
					//$rate=$row[csf('rate')];
					//$amount=$grey_fab_qnty*$row[csf('uom')];
					$uom_id=array_unique(explode(",",$row[csf("uom")]));
					$uom_data_arr[$row[csf("po_id")]][uom]=$row[csf("uom")];
					$po_quantity=$po_data_arr[$row[csf("po_id")]][po_qty];
					$ratio=$po_data_arr[$row[csf("po_id")]][ratio];
					$plan_cut=$po_data_arr[$row[csf("po_id")]][plan_cut];
					$po_price=$po_data_arr[$row[csf("po_id")]][po_price];
					$unit_rate=$po_color_size_arr[$row[csf('po_id')]]['amt']/$po_color_size_arr[$row[csf('po_id')]]['qty'];//$po_data_arr[$row[csf("po_id")]][unit_rate];
					
					$dzn_qnty=0;
					$costing_per_id=$costing_per_arr[$row[csf('job_no')]]['cost_per'];
					if($costing_per_id==1) $dzn_qnty=12;
					else if($costing_per_id==3) $dzn_qnty=12*2;
					else if($costing_per_id==4) $dzn_qnty=12*3;
					else if($costing_per_id==5) $dzn_qnty=12*4;
					else $dzn_qnty=1;
					
					$dzn_qnty=$dzn_qnty*$ratio;
					
					$plan_cut_qnty=$plan_cut*$ratio;
					$po_qty_pcs=$po_quantity*$ratio;
					$foreign=$commission_costing_arr[$row[csf('po_id')]][1];
					$local=$commission_costing_arr[$row[csf('po_id')]][2];
					$total_commisssion=$foreign+$local;
					
					$commission_dzn=($total_commisssion/$po_qty_pcs)*$dzn_qnty;
					$fab_cost_knit=0;$tot_finish_amt=0;
					foreach($uom_id as $u_id)
					{
						$fab_cost_knit=$fabric_costing_arr['knit']['finish'][$row[csf('po_id')]][$u_id];
						$tot_finish_amt=$fabric_costing_arr['knit']['finish'][$row[csf('po_id')]][$u_id];
					}
					
					
					$fabric_dzn=($fab_cost_knit/$plan_cut_qnty)*$dzn_qnty;
					//echo $fab_cost_knit.'/'.$plan_cut_qnty.'*'.$dzn_qnty;
					//$emblishment_cost=$emblishment_costing_arr[$row[csf('po_id')]];
					$emblishment_dzn=($embl_data_arr[$row[csf('po_id')]]['eamount']/$po_qty_pcs)*$dzn_qnty;
					$trims_dzn=($trims_data_arr[$row[csf('po_id')]]['tamount']/$po_qty_pcs)*$dzn_qnty;
					
					//$tot_po_cm_val=$row[csf('po_total_price')]-$tot_material_cost;
					
					$tot_embl_amt=$embl_data_arr[$row[csf('po_id')]]['eamount'];//$emblishment_costing_arr[$row[csf('po_id')]];
					
					$tot_trims_amt=$trims_data_arr[$row[csf('po_id')]]['tamount'];//$trims_costing_arr[$row[csf('po_id')]];
					$aop_amount=$aop_po_data_arr[$row[csf('po_id')]]['aop'];
					$aop_amount_dzn=($aop_amount/$plan_cut_qnty)*12;
					
					$lab_amount=$lab_po_data_arr[$row[csf('po_id')]]['lab'];
					$lab_amount_dzn=($lab_amount/$plan_cut_qnty)*12;
					
					$gen_accesso_amount=$gen_acces_data_arr[$row[csf('po_id')]]['amt'];
					$gen_amount_dzn=($gen_accesso_amount/$po_qty_pcs)*$dzn_qnty;
					
					$tot_material_cost=$tot_finish_amt+$tot_trims_amt+$tot_embl_amt+$aop_amount+$lab_amount+$gen_accesso_amount;
					$tot_po_cm_val=(($po_price-$tot_material_cost)/$po_qty_pcs)*12;
					
					
					//echo $row[csf('po_id')];///$plan_cut_qnty)*12;$lab_po_data_arr[$po]['lab']
					?>
                    
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trcost_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trcost_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
                            <td width="100"><div style="word-break:break-all"><? echo $row[csf('po_number')]; ?></div></td>
							<td width="100" align="center"  title="Unit Rate*Per dzn"><? echo number_format($unit_rate*$dzn_qnty,2); ?></td>
							<td width="100" align="right"><p><? //echo $commission_dzn; ?></p></td>
                            <td width="100" title="Total (Budget) Commission/Order Qty Pcs*dzn_per"  align="right" ><div style="word-break:break-all"><? echo number_format($commission_dzn,4); ?></div></td>
							<td width="100"  title="Fab Amount/Plan Cut*dzn_per" align="right"><div style="word-break:break-all"><? //echo $fabric_dzn; ?></div></td>
                            
							<td width="100" title=" Fab(Budget)Amount/Plan Cut*dzn_per" align="right" ><div style="word-break:break-all">F<? echo number_format($fabric_dzn,4); ?></div></td>
							<td width="100"   align="right"><div style="word-break:break-all"><? //echo $fabric_dzn; ?></div></td>
                            
                            <td width="100" align="right" title="Embl(Booking)Amount/Order Qty Pcs*dzn_per:<? //echo $tot_embl_amt;?>"><? echo number_format($emblishment_dzn,4); ?></td>
							<td width="100"  align="right"><? //echo $emblishment_dzn; ?></td>
							<td width="100" title="Trims(Booking)Amount/Order Qty Pcs*dzn_per:: <? //echo $tot_trims_amt;?>"  align="right"><p><? echo number_format($trims_dzn,4); ?></p></td>
                            <td width="100"  align="right"><? //echo $emblishment_dzn; ?></td>
							<td width="100" title="Gen Accesso Amount/Order Qty Pcs*dzn_per:: <? echo $gen_amount_dzn;?>"  align="right"><p><? echo number_format($gen_amount_dzn,4); ?></p></td>
							<td width="100"  align="right" title="<? echo $aop_amount;?>"><? //echo $emblishment_dzn; ?></td>
							<td width="100" title="Aop(Booking)Amount/Plan Cut*dzn_per:: <? //echo $tot_trims_amt;?>"  align="right"><p><? echo number_format($aop_amount_dzn,4); ?></p></td>
                            <td width="100"  align="right" title="<? echo $lab_amount;?>"><? //echo $emblishment_dzn; ?></td>
							<td width="100" title="Lab Test Amount/Plan Cut*dzn_per:: <? //echo $tot_trims_amt;?>"  align="right"><p><? echo number_format($lab_amount_dzn,4); ?></p></td>
                            <td width="100" title="Po Total Price-Total Material_cost(Trims Amount+Fab Cost+Embl Cost+AOP+LabTest+Gen Accesso Amount)/PO Qty Pcs*12"  align="right" ><div style="word-break:break-all"><? echo number_format($tot_po_cm_val,4); ?></div></td>
							<td width="" align="right"><div style="word-break:break-all"><? //echo number_format($rate,4); ?></div></td>
                           
                            </tr>
                            <?
							$total_po_cm_val+=$tot_po_cm_val;
							
							$i++;
					}
							?>
                            <tfoot>
                            <tr>
                            <th colspan="17" align="right">Total </th>
                             <th align="right"><? echo number_format($total_po_cm_val,2);?> </th>
                             <th> <? //echo number_format($total_fin_fab_qnty,2);?></th>
                             </tr>
                              </tfoot>
                     </table>	
                    </div>
                    </div>
                     <br/><br/>
             <?
				if(count($book_result)>0)
				{
			 ?>
           <div>Work Order wise Details<br/>Finish Fabric Details
</div>
           <table id="table_header_5" class="rpt_table" width="1730" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
                    <tr>
                    <th width="40">SL</th>
						<th width="120">WO No</th>
                        <th width="120">Supplier</th>
                        <th width="100">WO Type</th>
						<th width="120">Order No</th>
                        <th width="120">Style Ref</th>
                        <th width="100">Job no</th>
                        <th width="100">Item Name</th> 
                        <th width="100">Body Part</th>
                        <th width="150">Item Desc.</th>
                        <th width="80">UOM</th>
                        <th width="80">Cons. / Dzn</th>
                    	<th width="100">Booking Qty</th>
                     	<th width="100">Unit Price</th>
                        <th width="100">Amount</th>
                        <th width="70">Booking Date</th>
                        <th width="">Remark</th>
                    </tr>
                    </thead>
            </table>	
                    <div  class="scroll_div_inner" style="width:1750px; max-height:400px;overflow-y:scroll;" align="left" id="scroll_body">
					<table class="rpt_table" width="1730" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                    <?
						$print_report_format_ids=return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name."  and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
						$button_print_ids=explode(",",$print_report_format_ids);
		
						$print_report_format_ids2=return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name."  and module_id=2 and report_id=2 and is_deleted=0 and status_active=1");
						$button_print_ids2=explode(",",$print_report_format_ids2);
					
						$i=1;$total_fab_qnty=0;$total_fab_amount=0;$total_fab_main_amount=0;$total_fab_short_amount=0;$total_fab_with_ord_amount=0;
						
					foreach($fabric_booking_arr as $booking_key=>$bookingdata)
					{
						foreach($bookingdata as $booking_typess=>$typegdata)
						{
							foreach($typegdata as $body_key=>$bodygdata)
							{
								foreach($bodygdata as $desc_key=>$descgdata)
								{
									foreach($descgdata as $uom_key=>$val)
									{
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						//$item_name=$garments_item[$fab_data_arr[$row[csf("fab_dtls_id")]]['item']];
						//$body_parts=$body_part[$fab_data_arr[$row[csf("fab_dtls_id")]]['body']];
						//$uom_id=$fab_data_arr[$row[csf("fab_dtls_id")]]['uom'];
						//$amount=$grey_fab_qnty*$row[csf('fab_dtls_id')];
						$pay_mode=$val['pay_mode'];
						$print_data=$val['print_data'];
						$avg_cons=$val[("avg_cons")];
						$isshort=$val['is_short'];
						$bookingtype=$val['booking_type'];
						$book_type=$val['book_type'];
						$item=$val['item'];
						$supplier_id=$val['supplier_id'];
						
						$print_data=explode("**",$print_data);
						$fabric_source=$print_data[0];
						$item_category=$print_data[1];
						$is_approved=$print_data[2];
						
						$booking_typess=explode("**",$booking_typess);
						//$bookingtype=$booking_typess[0];
						//$isshort=$booking_typess[1];	
						//$book_type=$booking_typess[2];
						$grey_fab_qnty=$val['fab_qnty'];
						$booking_date=$val['booking_date'];
						$po_id=rtrim($val['po_id'],',');
						$job_no=rtrim($val['job_no'],',');
						$po_number=rtrim($val['po_number'],',');
						$style=rtrim($val['style'],',');
						 $entry_form=$val['eform'];
						//echo $is_short.'='.$booking_type_id.'<br>';
						$amount=$val['amount'];
						$main_amount=$val['main_amount'];
						$short_amount=$val['short_amount'];
						$with_ord_amount=$val['with_ord_amount'];
						$remarks=$val['remarks'];
						
						$total_fab_main_amount+=$main_amount;
						$total_fab_short_amount+=$short_amount;
						$total_fab_with_ord_amount+=$with_ord_amount;
						
						$avg_rate=$amount/$grey_fab_qnty;
						$fab_amount=$grey_fab_qnty*$avg_rate;
						//$fab_desc=$item_name.','.$color_type[$row[csf('color_type')]].','.$row[csf('construction')].','.$row[csf('copmposition')].','.$row[csf('gsm_weight')].','.$row[csf('dia_width')];
						//$entry_form=$row[csf('entry_form')];
						if($bookingtype==1 && $isshort==2)
						{
							$wo_type="Main";	
						}
						if($bookingtype==1 && $isshort==1)
						{
							
							$wo_type="Short";	
						}
						if($bookingtype==1 && ($book_type==1 || $book_type==2))
						{
							$wo_type=$short_booking_type[$book_type];	
						}
						if($bookingtype==4 && $isshort==2)
						{
							$wo_type="Sample With Order";	
						}
						/*if($entry_form!=108)
						{
							 $grey_fab_qnty=$row[csf('grey_fab_qnty')];
							 if($grey_fab_qnty==0)
							 {
								   $avg_rate=0;
									$amount=0;
								 }
							 else
							 {
								 $avg_rate=$row[csf('amount')]/$grey_fab_qnty;
									$amount=$grey_fab_qnty*$avg_rate;
								 }
							if($bookingtype==1 && $isshort==2)
							{
								$total_fab_main_amount+=$amount;
							}
							if($bookingtype==1 && $isshort==1)
							{
								$total_fab_short_amount+=$amount;
							}
							else if($bookingtype==4 && $isshort==2)
							{
								$total_fab_with_ord_amount+=$amount;
							}
						}
						else //Partail Booking
						{
							$grey_fab_qnty=$row[csf('fin_fab_qnty')];
							$avg_rate=$row[csf('amount')]/$grey_fab_qnty;
							$amount=$grey_fab_qnty*$avg_rate;
							$total_fab_main_amount+=$amount;//$row[csf('fab_main_amount')];
						}*/
						$po_id=implode(",",array_unique(explode(",",$po_id)));
						$job_no=implode(",",array_unique(explode(",",$job_no)));
						$po_number=implode(",",array_unique(explode(",",$po_number)));
						$style=implode(",",array_unique(explode(",",$style)));
						
						if($entry_form!=108)
						{
							
							//echo $bookingtype.'='.$isshort;
							if($bookingtype==1 && $isshort==2) //show_fabric_booking_report_urmi
							{
								//echo $bookingtype.'=='.$isshort;
								foreach($button_print_ids as $row_id)
									{
									 if($row_id==45) //Urmi
										{ 
										 $button_name="<a href='#' onClick=\"generate_worder_report('".$booking_key."','".$cbo_company_name."','".$po_id."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."','".$row_id."','show_fabric_booking_report_urmi','".$i."')\" '> ".$booking_key."<a/>";
									   }
									}
							}
							else if($bookingtype==1 && $isshort==1) //show_fabric_booking_report_urmi
							{
									//echo $bookingtype.'=='.$isshort;
									foreach($button_print_ids2 as $row_id)
									{
									 if($row_id==46) //Urmi
										{ 
										 $button_name="<a href='#' onClick=\"generate_worder_report('".$booking_key."','".$cbo_company_name."','".$po_id."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."','".$row_id."','show_fabric_booking_report_urmi','".$i."')\" '> ".$booking_key."<a/>";
										// echo $row[csf('booking_no')].'<br>';
									   }
									}
							}
							else
							{
								$button_name=$booking_key;
							}
						}
						else
						{
	 $button_name="<a href='#' onClick=\"generate_worder_report('".$booking_key."','".$cbo_company_name."','".$po_id."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."','".$entry_form."','show_fabric_booking_report_urmi','".$i."')\" '> ".$booking_key."<a/>";
						}
							//$po_no=implode(",",array_unique(explode(",",$row[csf('po_number')])));
							if($reporttype==1)
							{
								$view_buttton="<a href='#' onClick=\"setdata_po('".$po_number."')\">View<a/>";
							}
							else
							{
								$view_buttton=$po_number;
							}
					?>
                    
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trfin_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trfin_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
                            <td width="120"><? echo $button_name; ?></td>
                             <td width="120"><? if($pay_mode==3 || $pay_mode==5) echo $company_library[$supplier_id]; else echo $supplier_library[$supplier_id]; ?></td>
							<td width="100"><? echo $wo_type; ?></td>
							<td width="120" align="center"><div style="word-break:break-all"> <? 
							echo $view_buttton;
							//echo  "<a href='#' onClick=\"setdata_po('".$po_no."')\"> View<a/>";
							
							;//$row[csf('po_number')]; ?></div></td>
                            <td width="120"  align="center" ><div style="word-break:break-all"><? echo $style; ?></div></td>
                            <td width="100"  align="center"><div style="word-break:break-all"><? echo $job_no; ?></div></td>
                            <td width="100"  align="center"><div style="word-break:break-all"><? echo $garments_item[$val['item']]; ?></div></td>
                             <td width="100"  align="center"><div style="word-break:break-all"><? echo $body_key; ?></div></td>
                            
							<td width="150"  align="center" ><div style="word-break:break-all"><? echo $desc_key; ?></div></td>
							<td width="80"   align="center"><div style="word-break:break-all"><? echo $unit_of_measurement[$uom_key]; ?></div></td>
                            <td width="80"   align="center"><div style="word-break:break-all"><? echo number_format($avg_cons,4); ?></div></td>
                            
                            <td width="100" align="right" title="Grey Fab Qnty"><? echo number_format($grey_fab_qnty,2); ?></td>
							<td width="100"  align="right"><? 
							if($grey_fab_qnty==0)
							{ echo '0.00';
							}
							else{
								echo number_format($fab_amount/$grey_fab_qnty,4);
								}
							 ?></td>
							<td width="100" align="right"><p><? echo number_format($fab_amount,2) ; ?></p></td>
							<td width="70" align="right"><div style="word-break:break-all"><? echo change_date_format($booking_date); ?></div></td>
							<td width="" align="right"><div style="word-break:break-all"><? echo $remarks; ?></div></td>
                            </tr>
                            <?
							$total_fab_qnty+=$grey_fab_qnty;
							$total_fab_amount+=$fab_amount;
							
							$i++;
									}
								}
							}
						}
					}
							?>
                            <tfoot>
                            <tr>
                            <th colspan="12" align="right">Total </th>
                             <th align="right"><? echo number_format($total_fab_qnty,2);?> </th>
                             <th> <? //echo number_format($total_fin_fab_qnty,2);?></th>
                             <th> <? echo number_format($total_fab_amount,2);?></th>
                             <th> <? //echo number_format($total_fin_fab_qnty,2);?></th>
                             <th> <? //echo number_format($total_fin_fab_qnty,2);?></th>
                             </tr>
                             </tfoot>
                    </table>
                    </div>
                      
           <br/><br/>
           <?
				}
				
			 if(count($trim_result)>0)
				{
		   ?>
            <div style="width:100%">
           <div><strong>Accessories</strong></div>
           <table id="table_header_6" class="rpt_table" width="1400" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
                    <tr>
                    <th width="40">SL</th>
						<th width="120">WO No</th>
                        <th width="80">WO Type</th>
						<th width="120">Order No</th>
                        <th width="120">Style Ref</th>
                        <th width="100">Job no</th>
                        <th width="100">Item Name</th>
                        <th width="150">Item Desc.</th>
                        <th width="60">Unit</th>
                        <th width="70">Cons. / Dzn</th>
                    	<th width="100">Booking Qty</th>
                     	<th width="60">Unit Price</th>
                        <th width="100">Amount</th>
                        <th width="80">Booking Date</th>
                        <th width="">Supplier</th>
                    </tr>
                    </thead>
            </table>	
                    <div  class="scroll_div_inner" style="width:1420px; max-height:400px;overflow-y:scroll;overflow-x:hidden;" align="left" id="scroll_body">
					<table class="rpt_table" width="1400" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_accss">
                    <?
					$print_report_format_ids3=return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name."  and module_id=2 and report_id=26 and is_deleted=0 and status_active=1");
		$button_print_ids3=explode(",",$print_report_format_ids3);
					//c.supplier_id
					//$costing_per_arr=return_library_array( "select job_no,costing_per from wo_pre_cost_mst ", "job_no", "costing_per");
					$i=1;$total_fab_qnty=0;$total_trims_amount=0;$total_trims_main_amount=0;$total_trims_short_amount=0;$total_trims_with_ord_amount=0;
					
					foreach($trims_booking_arr as $booking_no=>$booking_data)
					{
						foreach($booking_data as $trim_group=>$item_data)
						{
							foreach($item_data as $description=>$desc_data)
							{
								foreach($desc_data as $uom=>$row)
								{
									//$trims_booking_arr[$row[csf('booking_no')]][$row[csf('trim_group')]][$description][$row[csf('uom')]]['po_id']
										if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										
									$currency_id=$row[('currency_id')];
									$avg_rate=number_format(($row[('amount')]/$row[('wo_qnty')]),6,'.','');
									if($currency_id==1) //Taka
									{
										$rate=number_format($avg_rate/$row[('exchange_rate')],6,'.','');
									}
									else
									{
										$rate=number_format($avg_rate,6,'.','');
									}
									
									$amount=($row[('wo_qnty')]*$rate);//round( 1040.56789, 4, PHP_ROUND_HALF_EVEN)
									$amount=number_format($amount,4,'.','');
									
									if($row[('booking_type')]==2 && $row[('is_short')]==2)
									{
										$wo_type="Main";
										$total_trims_main_amount+=$amount;//$row[('trims_main_amount')];	
									}
									if($row[('booking_type')]==2 && $row[('is_short')]==1)
									{
										$wo_type="Short";
										$total_trims_short_amount+=$amount;	
									}
									if($row[('booking_type')]==5)
									{
										$wo_type="Sample With Order";
										$total_trims_with_ord_amount+=$amount;	
									}
									/*if($row[csf('booking_type')]==1 && ($row[csf('book_type')]==1 || $row[csf('book_type')]==2))
									{
										$wo_type=$short_booking_type[$row[csf('book_type')]];	
									}*/
									
									//$amount=12056;
									if($description=='') $decs=0;else $decs=$description;
									
									
									//echo ($amount).'<br>';
									//$total_trims_main_amount+=$amount;//$row[('trims_main_amount')];
									//$total_trims_short_amount+=$row[('trims_short_amount')];//$row[('trims_main_amount')]
									//$total_trims_with_ord_amount+=$row[('trims_with_ord_amount')];
									//$description=implode(",",array_unique(explode(",",$trims_data_des_arr[$row[csf('booking_no')]][$trim_group][$uom]['description'])));
									$po_id=rtrim($row[('po_id')],',');
									$po_idss=implode(",",array_unique(explode(",",$po_id)));
									$po_ids=array_unique(explode(",",$po_id));
									$job_con="";$tot_po_qty=0;
									foreach($po_ids as $pid)
									{
										if($job_con=="") $job_con=$job_numer_arr[$pid]['job_no'];else $job_con.=",".$job_numer_arr[$pid]['job_no'];
										
										$tot_po_qty+=$po_data_arr_qty[$pid][po_qty_trim];
									}
									
									$avg_cons=($row[('wo_qnty')]/$tot_po_qty)*12;
									
									$job_no=rtrim($row[('job_no')],',');
									$job_nos=implode(",",array_unique(explode(",",$job_no)));
									
									if($row[('booking_type')]==2 && $row[('is_short')]==2) //show_fabric_booking_report_urmi
									{
											foreach($button_print_ids3 as $row_id) //show_trim_booking_report
											{
													 if($row_id==67) //Urmi
														{ 
														 $button_name="<a href='#' onClick=\"generate_worder_report('".$booking_no."','".$cbo_company_name."','".$po_idss."','".$row[('item_category')]."','".$row[('fabric_source')]."','".$job_nos."','".$row[('is_approved')]."','".$row_id."','show_trim_booking_report2','".$i."')\" '> ".$booking_no."<a/>";
														// echo $row[csf('booking_no')].'<br>';
													   }
											}
									}
									else if($row[('booking_type')]==2 && $row[('is_short')]==1) //show_fabric_booking_report_urmi
									{
											foreach($button_print_ids3 as $row_id) //show_trim_booking_report
											{
												
														 $button_name="<a href='#' onClick=\"generate_worder_report('".$booking_no."','".$cbo_company_name."','".$po_idss."','".$row[('item_category')]."','".$row[('fabric_source')]."','".$job_nos."','".$row[('is_approved')]."',67,'show_fabric_booking_report_urmi','".$i."')\" '> ".$booking_no."<a/>";
														// echo $row[csf('booking_no')].'<br>';
											}
											
									}
									else
									{
										$button_name=$booking_no;
									}
									
									$po_number=rtrim($row[('po_number')],',');
									$po_no=implode(",",array_unique(explode(",",$po_number)));
									$style_ref_no=rtrim($row[('style_ref_no')],',');
									$style_ref_no=implode(",",array_unique(explode(",",$style_ref_no)));
									if($reporttype==1)
										{
											$view_buttton="<a href='#' onClick=\"setdata_po('".$po_no."')\">View<a/>";
										}
										else
										{
											$view_buttton=$po_no;
										}
										
									//if($amount>0)
									//{
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tracc1_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tracc1_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
                            <td width="120"><? echo $button_name; ?></td>
							<td width="80"><? echo $wo_type; ?></td>
							<td width="120" align="center"><div style="word-break:break-all">
							<?  echo $view_buttton;?></div></td>
                            <td width="120"  align="center" ><div style="word-break:break-all"><? echo $style_ref_no; ?></div></td>
							<td width="100"  align="center"><div style="word-break:break-all"><? echo implode(",",array_unique(explode(",",$job_no)));//$row[csf('job_no')]; ?></div></td>
                            <td width="100" align="center"><div style="word-break:break-all"><? echo $item_library[$trim_group]; ?></div></td>
							<td width="150" align="center" ><div style="word-break:break-all"><? echo $description; ?></div></td>
							<td width="60"  align="center"><div style="word-break:break-all"><? echo $unit_of_measurement[$uom]; ?></div></td>
                            <td width="70"  align="right"><div style="word-break:break-all"><? echo number_format($avg_cons,4); ?></div></td>
                            <td width="100" align="right" title="<? echo 'PO Qty'.$tot_po_qty;?>"><? echo number_format($row[('wo_qnty')],2); ?></td>
							<td width="60"  align="right"  title="Rate=<? echo number_format($avg_rate,4);?> Exchange Rate=<? echo $row[('exchange_rate')];?>"><? echo number_format($rate,4); ?></td>
							<td width="100" align="right"><p><? echo number_format($amount,2) ; ?></p></td>
							<td width="80" align="right"><div style="word-break:break-all"><? echo change_date_format($row[('booking_date')]); ?></div></td>
                            <td width="" align="right"><p><? echo $supplier_library[$row[('supplier_id')]] ; ?></p></td>
                            </tr>
                            <?
						//}
							$total_fab_qnty+=$row[('wo_qnty')];
							$total_trims_amount+=$amount;
							$i++;
					    		 }
							}
						}
					}
							?>
                    </table>
                    </div>
                    <table  width="1400"  class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all">
                            <tr>
                            	<td width="40"></td>
                                <td width="120"></td>
                                <td width="80"></td>
                                <td width="120"></td>
                                <td width="120"></td>
                                <td width="100"></td>
                                <td width="100"></td>
                                <td width="150"></td>
                                <td width="60">Total</td>
                                <td width="70"></td>
                                <td width="100" align="right" id="value_total_fab_qnty"><? echo number_format($total_fab_qnty,2);?></td>
                                 <td width="60"></td>
                                 <td width="100" align="right" id="value_total_trims_amount"><? echo number_format($total_trims_amount,2);?></td>
                                 <td width="80"></td>
                                 <td width=""></td>
                             </tr>
                    </table>
                    </div>
           <?
					}
					?>
                    <br>
                  <?
                if(count($gen_trim_result)>0)
				{
		   ?>
            
            <div style="width:100%">
           <div><strong>General Accessories</strong></div>
           <table id="table_header_111" class="rpt_table" width="1370" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
                    <tr>
                    <th width="30">SL</th>
						<th width="120">Issue Id</th>
                        <th width="80">Issue Date</th>
                        <th width="120">MRR No</th>
						<th width="120">Style Ref.</th>
                        <th width="120">Job no</th>
                        <th width="150">Order No</th>
                        <th width="100">Item Name</th>
                        <th width="150">Item Desc.</th>
                        <th width="60">Unit</th>
                    	<th width="100">Issue Qty</th>
                     	<th width="60">Unit Price</th>
                        <th width="">Amount</th>
                        
                    </tr>
                    </thead>
            </table>	
                    <div  class="scroll_div_inner" style="width:1370px; max-height:400px;overflow-y:scroll;overflow-x:hidden;" align="left" id="scroll_body">
					<table class="rpt_table" width="1350" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_gen_accss">
                    <?
					
					$i=1;$total_gen_qnty=0;$total_gen_amount=0;
					foreach($gen_trim_result as $row)
					{
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
					$description=$row[csf('product_name_details')];
					$po_id=$row[csf('po_id')];
					$po_ids=array_unique(explode(",",$po_id));
					$job_con="";	$po_con="";$style_con="";
					foreach($po_ids as $pid)
					{
						if($job_con=="") $job_con=$job_numer_arr[$pid]['job_no'];else $job_con.=",".$job_numer_arr[$pid]['job_no'];
						if($style_con=="") $style_con=$job_numer_arr[$pid]['style'];else $style_con.=",".$job_numer_arr[$pid]['style'];
						if($po_con=="") $po_con=$po_numer_arr[$pid];else $po_con.=",".$po_numer_arr[$pid];
					}
					
					
					$po_no=implode(",",array_unique(explode(",",$po_con)));
					if($reporttype==1)
						{
							$view_buttton="<a href='#' onClick=\"setdata_po('".$po_no."')\">View<a/>";
						}
						else
						{
							$view_buttton=$po_no;
						}
						//echo $row[csf("item_group_id")].',';
						$conver_rate=$conversion_arr[$row[csf("item_group_id")]]['conver_rate'];
						if($conver_rate=='') $conver_rate=0;else $conver_rate=$conver_rate;
						$mrr_no=$mrr_recv_arr[$row[csf('recv_trans_id')]]['mrr_no'];
						$order_rate=($mrr_recv_arr[$row[csf('recv_trans_id')]]['rate']/$conver_rate);
						
						$issue_qnty=$row[csf('cons_quantity')];
						$issue_number=$row[csf('issue_number')];
						$amount=$issue_qnty*$order_rate;
						$issue_date=$row[csf('issue_date')];
						$cons_uom=$row[csf('cons_uom')];//$issue_arr[$key_trans]['cons_uom'];
					
						$item_group_id=$row[csf('item_group_id')];
						
						//$description=$val['desc'];
						$ord_rate=$mrr_recv_arr[$row[csf('recv_trans_id')]]['rate'];
					?>
                    	
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('traccg_<? echo $i; ?>','<? echo $bgcolor;?>')" id="traccg_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $issue_number; ?></p></td>
							<td width="80"><? echo change_date_format($issue_date); ?></td>
                            <td width="120"><p><? echo $mrr_no; ?></p></td>
							<td width="120" align="center" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><div style="word-break:break-all">
							
							<? echo implode(",",array_unique(explode(",",$style_con)));?></div></td>
                            <td width="120"  align="center" ><div style="word-break:break-all"><? echo implode(",",array_unique(explode(",",$job_con))); ?></div></td>
							<td width="150"  align="center"><div style="word-break:break-all"> <? echo $view_buttton; ?></div></td>
                            <td width="100"  align="center"><div style="word-break:break-all"><? echo $item_library[$item_group_id]; ?></div></td>
                            
							<td width="150"  align="center" ><div style="word-break:break-all"><? echo $description; ?></div></td>
							<td width="60"   align="center"><div style="word-break:break-all"><? echo $unit_of_measurement[$cons_uom]; ?></div></td>
                            
                            <td width="100" align="right" title="Cons Qty"><? echo number_format($issue_qnty,4); ?></td>
							<td width="60"  align="right" title="<? echo 'Conv Rate: '.$conver_rate.'&nbsp;'.'Order Rate: '.number_format($ord_rate,4);?>"><? echo number_format($order_rate,4); ?></td>
							<td width="" align="right"><p><? echo number_format($amount,4) ; ?></p></td>
                            </tr>
                            <?
							$total_gen_qnty+=$issue_qnty;
							$total_gen_amount+=$amount;
							
							$i++;
					     }
							?>
                    
                    </table>
                    
                    </div>
                    <table  width="1350"  class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all">
                            <tr>
                            	<td width="30"></td>
                                <td width="120"></td>
                                <td width="80"></td>
                                 <td width="120"></td>
                                <td width="120"></td>
                                <td width="120"></td>
                                <td width="150"></td>
                                <td width="100"></td>
                                <td width="150"></td>
                                <td width="60">Total</td>
                                <td width="100" align="right" id="value_total_gen_qnty"><? echo number_format($total_gen_qnty,4);?></td>
                                <td width="60"></td>
                                <td width="" align="right" id="value_total_gen_amount"><? echo number_format($total_gen_amount,4);?></td>
                             </tr>
                    </table>
                    </div>
           <?
					}
					?>
                     <br/><br/>
                    <?
				
				if(count($aop_result)>0) //	//AOP
				{
					$color_name_arr=return_library_array( "select id,color_name from lib_color ", "id", "color_name");
		   ?>
          
           <div><strong>Fabric Service Booking AOP</strong></div>
           <table id="table_header_6" class="rpt_table" width="1230" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
                    <tr>
                    <th width="30">SL</th>
						<th width="120">WO No</th>
                        <th width="100">Style</th>
						<th width="120">Order No</th>
                      
                        <th width="100">Item Desc.</th>
                        <th width="100">Gmt. Color</th>
                        <th width="150">Item Color</th>
                        <th width="80">UOM</th>
                    	<th width="100">Booking Qty</th>
                     	<th width="100">Unit Price</th>
                        <th width="100">Amount</th>
                        <th width="">Booking Date</th>
                    </tr>
                    </thead>
            </table>	
                    <div  class="scroll_div_inner" style="width:1270px; max-height:400px;overflow-y:scroll; " align="left" id="scroll_body">
					<table class="rpt_table" width="1230" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                    <?
					//$costing_per_arr=return_library_array( "select job_no,costing_per from wo_pre_cost_mst ", "job_no", "costing_per");
					$i=1;$total_aop_fab_qnty=0;$total_aop_amount=0;$total_aop_main_amount=0;
					
					foreach($aop_result as $row)
					{
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$body_parts=$body_part[$fab_data_arr2[$row[csf("fab_dtls_id")]]['body']];
					$desc=$fab_data_arr2[$row[csf("fab_dtls_id")]]['des'];
					$colortype=$fab_data_arr2[$row[csf("fab_dtls_id")]]['color'];

					$total_aop_main_amount+=$row[csf('aop_amount')];
					//aop_amount,aop_wo_qty 
					
					//$total_trims_with_ord_amount+=$row[csf('trims_with_ord_amount')];
					$description=$body_parts.','.$color_type[$colortype].','.$desc;//implode(",",array_unique(explode(",",$trims_data_des_arr[$row[csf('booking_no')]]['description'])));
				
					 $button_name="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$cbo_company_name."','".$po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."',61,'show_trim_booking_report1','".$i."')\" '> ".$row[csf('booking_no')]."<a/>";
					 
					$po_no=implode(",",array_unique(explode(",",$row[csf('po_number')])));
					if($reporttype==1)
						{
							$view_buttton="<a href='#' onClick=\"setdata_po('".$po_no."')\">View<a/>";
						}
						else
						{
							$view_buttton=$po_no;
						}
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('traop_<? echo $i; ?>','<? echo $bgcolor;?>')" id="traop_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
                            <td width="120"><? echo $button_name; ?></td>
							<td width="100"><? echo  implode(",",array_unique(explode(",",$row[csf('style_ref_no')]))); ?></td>
							<td width="120" align="center"><div style="word-break:break-all">
								<? echo $view_buttton;?></div>
                            </td>
                           
							<td width="100"  align="center"><div style="word-break:break-all"><? echo $description;//$row[csf('job_no')]; ?></div></td>
                            <td width="100"  align="center"><div style="word-break:break-all"><? echo $color_name_arr[$row[csf('gmts_color_id')]]; ?></div></td>
                            
							<td width="150"  align="center" ><div style="word-break:break-all"><? echo  $color_name_arr[$row[csf('fabric_color_id')]]; ?></div></td>
							<td width="80"   align="center"><div style="word-break:break-all"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></div></td>
                            <td width="100" align="right" title="Wo Qty"><? echo number_format($row[csf('aop_wo_qty')],2); ?></td>
							<td width="100"  align="right"><? echo number_format($row[csf('rate')],4); ?></td>
							<td width="100" align="right"><p><? echo number_format($row[csf('amount')],2) ; ?></p></td>
							<td width="" align="right"><div style="word-break:break-all"><? echo change_date_format($row[csf('booking_date')]); ?></div></td>
                           
                            
                            </tr>
                            <?
							$total_aop_fab_qnty+=$row[csf('aop_wo_qty')];
							$total_aop_amount+=$row[csf('aop_amount')];
							
							$i++;
					     }
							?>
                            <tfoot>
                            <tr>
                            <th colspan="8" align="right">Total </th>
                             <th align="right"><? echo number_format($total_aop_fab_qnty,2);?> </th>
                             <th> <? //echo number_format($total_fin_fab_qnty,2);?></th>
                             <th align="right"> <? echo number_format($total_aop_amount,2);?></th>
                             <th> <? //echo number_format($total_fin_fab_qnty,2);?></th>
                             </tr>
                             </tfoot>
                    
                    </table>
                    </div>
           <?
					} //AOP End
					//WO LabTest
					?>
                   <br/><br/> 
                    <?
					
				if(count($lab_result)>0) //	//Lab
				{
					$color_name_arr=return_library_array( "select id,color_name from lib_color ", "id", "color_name");
					$lab_test_rate_library=return_library_array( "select id, test_item from lib_lab_test_rate_chart", "id", "test_item"  );
		   ?>
             
           <div><strong>Fabric Service Booking Lab-Test	</strong></div>
           <table id="table_header_6" class="rpt_table" width="760" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
                    <tr>
                    <th width="40">SL</th>
						<th width="120">WO No</th>
                        <th width="100">Style</th>
						<th width="120">Order No</th>
                        <th width="120">Test For</th>
                        <th width="100">Test Item</th>
                        <th width="60">Amount</th>
                       <th width="">Supplier</th>
                    </tr>
                    </thead>
            </table>	
                    <div  class="scroll_div_inner" style="width:780px; max-height:400px;overflow-y:scroll;" align="left" id="scroll_body">
					<table class="rpt_table" width="760" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                    <?
					//$costing_per_arr=return_library_array( "select job_no,costing_per from wo_pre_cost_mst ", "job_no", "costing_per");
					$i=1;$total_lab_fab_qnty=0;$total_lab_amount=0;$total_lab_main_amount=0;
					
					foreach($lab_result as $row)
					{
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					//$worate=explode(",",$row[csf('lab_rate')]);
					$test_item_id=array_unique(explode(",",$row[csf('test_item_id')]));
					//print_r($test_item_id);wo_value
					 $worate_cont=count($worate);
					// echo $worate_cont.'<br/>';
					/*$wo_rate=0;
					foreach($worate as $rate)
					{
						$wo_rate+=$rate;
					}*/
					
					$po_num=array_unique(explode(",",$row[csf('po_id')]));
					$job_nos='';$po_nos='';$style_ref='';
					foreach($po_num as $po)
					{
						//if($job_nos=='') $job_nos=$job_numer_arr[$po]['job_no'];else $job_nos.=",".$job_numer_arr[$po]['job_no'];
						if($po_nos=='') $po_nos=$po_numer_arr[$po];else $po_nos.=",".$po_numer_arr[$po];
						if($style_ref=='') $style_ref=$job_numer_arr[$po]['style'];else $style_ref.=",".$job_numer_arr[$po]['style'];
						
					}
					
					$lab_wo_rate=$row[csf('amount')];
					
					$test_item='';
					foreach($test_item_id as $item_id)
					{
						if($test_item=='') $test_item=$lab_test_rate_library[$item_id];else $test_item.=",".$lab_test_rate_library[$item_id];
					}
					//	show_trim_booking_report
					 $button_name="<a href='#' onClick=\"generate_worder_report3('".$cbo_company_name."','".$row[csf('mst_id')]."',102,'show_trim_booking_report_new','".$i."')\" '> ".$row[csf('labtest_no')]."<a/>";
					$po_no=implode(",",array_unique(explode(",",$po_nos)));
					if($reporttype==1)
						{
							$view_buttton="<a href='#' onClick=\"setdata_po('".$po_no."')\">View<a/>";
						}
						else
						{
							$view_buttton=$po_no;
						}
					?>
                    
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trlab_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trlab_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
                            <td width="120"><? echo $button_name; ?></td>
							<td width="100"><? echo  implode(",",array_unique(explode(",",$style_ref))); ?></td>
							<td width="120" align="center"><div style="word-break:break-all">
							 
							<? echo $view_buttton; ?></div></td>
                            <td width="120"  align="center" ><div style="word-break:break-all"><? echo $test_for[$row[csf('test_for')]]; ?></div></td>
							<td width="100"  align="center"><div style="word-break:break-all"><? echo $test_item; ?></div></td>
                            <td width="60"  align="center"><div style="word-break:break-all"><? echo number_format($lab_wo_rate,2); ?></div></td>
                            <td width=""  align="center"><div style="word-break:break-all"><? echo $supplier_library[$row[csf('supplier_id')]]; ?></div></td>
                            
                            </tr>
                            <?
							$i++;
					     }
							?>
                            <tfoot>
                            <tr>
                            <th colspan="8" align="right">&nbsp; </th>
                            
                             </tr>
                             </tfoot>
                    
                    </table>
                    </div>
                     
           <?
					} //Lab End
					
		   ?>
            <br/><br/>
           <div><strong>Emblishment</strong></div>
           <table id="table_header_7" class="rpt_table" width="1290" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
                    <tr>
                    <th width="40">SL</th>
						<th width="120">WO No</th>
                        <th width="120">Supplier</th>
                        <th width="100">WO Type</th>
                        <th width="120">Style</th>
                        <th width="100">Job no</th>
						<th width="120">Order No</th>
                        <th width="100">Emb. Type</th>
                        <th width="80">Unit</th>
                    	<th width="100">Booking Qty</th>
                     	<th width="100">Unit Price</th>
                        <th width="100">Amount</th>
                        <th width="">Booking Date</th>
                    </tr>
                    </thead>
            </table>	
                    <div class="scroll_div_inner"  style="width:13100px; max-height:400px;overflow-y:scroll;" align="left" id="scroll_body">
					<table class="rpt_table" width="1290" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                    <?
					$emb_type_arr=return_library_array( "select id,emb_type from wo_pre_cost_embe_cost_dtls ", "id", "emb_type");
					$i=1;$total_embl_qnty=0;$total_embl_amount=0;$tot_po_total_price=0;
					foreach($emb_result as $row)
					{
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					//$grey_fab_qnty=$row[csf('grey_fab_qnty')];
					$emblishment_name=$emblishment_name_array[$row[csf('emblishment_name')]];
					//$amount=$grey_fab_qnty*$row[csf('pay_mode')];
					$tot_po_total_price+=$row[csf('po_total_price')];
					$po_ids=explode(",",$row[csf('po_id')]);
					$po_no=="";$job_no=="";$style_no=="";
					foreach($po_ids as $pid)
					{
						if($po_no=="") $po_no=$po_numer_arr[$pid];else $po_no.=",".$po_numer_arr[$pid];
						if($job_no=="") $job_no=$job_numer_arr[$pid]['job_no'];else $job_no.=",".$job_numer_arr[$pid]['job_no'];
						if($style_no=="") $style_no=$job_numer_arr[$pid]['style'];else $style_no.=",".$job_numer_arr[$pid]['style'];
					}
					
					//	show_trim_booking_report
					 $button_name="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$cbo_company_name."','".$row[csf('po_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$job_no."','".$row[csf('is_approved')]."',100,'show_trim_booking_report_urmi','".$i."')\" '> ".$row[csf('booking_no')]."<a/>";
					$po_no=implode(",",array_unique(explode(",",$row[csf('po_number')])));
					if($reporttype==1)
						{
							$view_buttton="<a href='#' onClick=\"setdata_po('".$po_no."')\">View<a/>";
						}
						else
						{
							$view_buttton=$po_no;
						}
						$pay_mode=$row[csf('pay_mode')];
					?>
                    
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tremb_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tremb_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
                            <td width="120"><? echo $button_name; ?></td>
                            <td width="120"><? /*if($pay_mode==3 || $pay_mode==5) echo $company_library[$row[csf('supplier_id')]]; else*/ echo $supplier_library[$row[csf('supplier_id')]]; ?></td>
							<td width="100"><? echo $emblishment_name; ?></td>
							<td width="120"  align="center"><div style="word-break:break-all"><? echo implode(",",array_unique(explode(",",$style_no))); ?></div></td>
                           
							<td width="100"  align="center"><div style="word-break:break-all"><? echo implode(",",array_unique(explode(",",$job_no))); ?></div></td>
                            <td width="120" align="center"><div style="word-break:break-all">
							<? echo $view_buttton; ?></div></td>
                            <td width="100"  align="center"><div style="word-break:break-all"><? echo $emblishment_print_type[$emb_type_arr[$row[csf('emb_id')]]]; ?></div></td>
							<td width="80"   align="center"><div style="word-break:break-all"><? echo 'DZN'//$unit_of_measurement[$row[csf('uom')]]; ?></div></td>
                            
                            <td width="100" align="right" title="Grey Fab Qnty"><? echo number_format($row[csf('wo_qnty')],2); ?></td>
							<td width="100"  align="right"><? echo number_format($row[csf('rate')],4); ?></td>
							<td width="100" align="right"><p><? echo number_format($row[csf('amount')],2) ; ?></p></td>
							<td width="" align="right"><div style="word-break:break-all"><? echo change_date_format($row[csf('booking_date')]); ?></div></td>
                            </tr>
                            <?
							$total_embl_qnty+=$row[csf('wo_qnty')];
							$total_embl_amount+=$row[csf('amount')];
							
							$i++;
					}
							?>
                            <tfoot>
                            <tr>
                             <th colspan="9" align="right">Total </th>
                             <th align="right"><? echo number_format($total_embl_qnty,2);?> </th>
                             <th align="right"><? //echo number_format($total_embl_qnty,2);?> </th>
                             <th> <? echo number_format($total_embl_amount,2);?></th>
                             <th> <? //echo number_format($total_amount,2);?></th>
                             </tr>
                             
                             <tr>
                             <th colspan="9" align="right"> Grand Total</th>
                             <th id="grand_tot_material_cost"> <? echo number_format($total_embl_amount+$total_trims_amount+$total_fab_amount,2);?> </th>
                             <th> </th>
                             <th> </th>
                             <th> </th>
                             </tr>
                          </tfoot>   
                    
                    </table>
                    </div>
          
            <br/><br/>
           <?
          	$style1="#E9F3FF"; 
			$style="#FFFFFF";
		   ?>
           <div><strong>Summary</strong></div>
           <table  id="table_header_8" class="rpt_table" width="650" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
                    <tr>
                    <th width="40">SL</th>
						<th width="120">TOTAL</th>
                        <th width="150">Main Booking Amount</th>
                        <th width="120">Short / Additional Amount</th>
                        <th width="100">Sample With Order</th>
                         <th width="100">Fabric Booking Aop</th>
                         <th width="100">Lab test</th>
						<th width="120">Total Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr bgcolor="<? echo $style1;?>">
							<td width="40">1</td>
                            <td width="120">Finish Fab</td>
							<td width="100" align="right"><? echo number_format($total_fab_main_amount,2);?></td>
                            <td width="100" align="right"><? echo number_format($total_fab_short_amount,2)?></td> 
                            <td width="100" align="right"><? echo number_format($total_fab_with_ord_amount,2)?></td>
                             <td width="100" align="right"><? //echo number_format($total_fab_short_amount,2)?></td> 
                            <td width="100" align="right"><? //echo number_format($total_fab_with_ord_amount,2)?></td>
                            <td width="100" align="right"><? echo number_format($total_fab_main_amount+$total_fab_short_amount+$total_fab_with_ord_amount,2);?></td>
                    </tr>
                     <tr bgcolor="<? echo $style;?>">
							<td width="40">2</td>
                            <td width="120">Accessories(Booking)</td>
							<td width="100"  align="right"><? echo number_format($total_trims_main_amount,2);?></td>
                            <td width="100"  align="right"><? echo number_format($total_trims_short_amount,2);?></td> 
                            <td width="100"  align="right"><? echo number_format($total_trims_with_ord_amount,2);?></td>
                             <td width="100" align="right"><? //echo number_format($total_fab_short_amount,2)?></td> 
                            <td width="100" align="right"><? //echo number_format($total_fab_with_ord_amount,2)?></td>
                            <td width="100"  align="right"><? echo number_format($total_trims_main_amount+$total_trims_short_amount+$total_trims_with_ord_amount,2);?></td>
                    </tr>
                     <tr bgcolor="<? echo $style1;?>">
							<td width="40">3</td>
                            <td width="120">Accessories(General)</td>
							<td width="100"  align="right"><? echo number_format($sum_total_accesso_amount,2);?></td>
                            <td width="100"  align="right"><? //echo number_format($total_trims_short_amount,2);?></td> 
                            <td width="100"  align="right"><? //echo number_format($total_trims_with_ord_amount,2);?></td>
                             <td width="100" align="right"><? //echo number_format($total_fab_short_amount,2)?></td> 
                            <td width="100" align="right"><? //echo number_format($total_fab_with_ord_amount,2)?></td>
                            <td width="100"  align="right"><? echo number_format($sum_total_accesso_amount,2);?></td>
                    </tr>
                     <tr bgcolor="<? echo $style;?>">
							<td width="40">4</td>
                            <td width="120">Emblishment</td>
							<td width="100" align="right"><? echo number_format($total_embl_amount,2);?></td>
                            <td width="100"></td> 
                            <td width="100"></td>
                              <td width="100"></td> 
                            <td width="100"></td>
                            <td width="100"  align="right"><? echo number_format($total_embl_amount,2);?></td>
                    </tr>
                    <tr bgcolor="<? echo $style1;?>">
							<td width="40">5</td>
                            <td width="120">AOP</td>
							<td width="100" align="right"></td>
                            <td width="100"></td> 
                            <td width="100"></td>
                             <td width="100" align="right"><? echo number_format($sum_total_aop_amount,2);?></td> 
                            <td width="100"></td>
                            <td width="100"  align="right"><? echo number_format($sum_total_aop_amount,2);?></td>
                    </tr>
                     <tr bgcolor="<? echo $style;?>">
							<td width="40">6</td>
                            <td width="120">Lab Test</td>
							<td width="100" align="right"></td>
                            <td width="100"></td> 
                            <td width="100"></td>
                             <td width="100"></td> 
                            <td width="100" align="right"><? echo number_format($sum_total_lab_amount,2);?></td>
                            <td width="100"  align="right"><? echo number_format($sum_total_lab_amount,2);?></td>
                    </tr>
                    <tr bgcolor="#CCCCCC">
							<td width="40"></td>
                            <td width="120"><b>Grand Total</b></td>
							<td width="100" align="right"><strong><? echo number_format($total_fab_main_amount+$total_trims_main_amount+$total_embl_amount+$sum_total_accesso_amount,2);//$sum_total_aop_amount+$sum_total_lab_amount ?></strong></td>
                            <td width="100" align="right"><strong><? echo number_format($total_fab_short_amount+$total_trims_short_amount,2);?></strong></td> 
                            <td width="100" align="right"><strong><? echo number_format($total_fab_with_ord_amount+$total_trims_with_ord_amount,2);?></strong></td>
                             <td width="100" align="right"><strong><? echo number_format($sum_total_aop_amount,2);?></strong></td> 
                            <td width="100" align="right"><strong><? echo number_format($sum_total_lab_amount,2);?></strong></td>
                            <td width="100" align="right"><strong><? echo number_format($total_fab_main_amount+$total_fab_short_amount+$total_fab_with_ord_amount+$total_trims_main_amount+$total_trims_short_amount+$total_trims_with_ord_amount+$total_embl_amount+$sum_total_aop_amount+$sum_total_lab_amount+$sum_total_accesso_amount,2);?></strong></td>
                    </tr>
                    
                    </tbody>
            </table>
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
    echo "$html****$filename"; 
    exit();
}

if($action=='trims_popup')
{
	echo load_html_head_contents("Trims Details info", "../../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id."*".$tot_po_qnty;die;
	
	//echo $ratio;die;

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
       <table width="650" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th colspan="7">Accessories Status</th>
                </tr>
                <tr>
                    <th width="110">Item</th>
                    <th width="70">UOM</th>
                    <th width="90">Req. Qty.</th>
                    <th width="90">Received</th>
                    <th width="90">Recv. Balance</th>
                    <th width="90">Issued</th>
                    <th>Left Over</th>
                </tr>
            </thead>
            <?
			$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
			$trims_array=array();
			$trimsDataArr=sql_select("select b.item_group_id,  
									sum(CASE WHEN a.entry_form=24 THEN a.quantity ELSE 0 END) AS recv_qnty,
									sum(CASE WHEN a.entry_form=25 THEN a.quantity ELSE 0 END) AS issue_qnty
									from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and a.po_breakdown_id in($po_break_down_id) and b.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by b.item_group_id");
			foreach($trimsDataArr as $row)	
			{
				$trims_array[$row[csf('item_group_id')]]['recv']=$row[csf('recv_qnty')];
				$trims_array[$row[csf('item_group_id')]]['iss']=$row[csf('issue_qnty')];
			}
			
			
			//$costing_per_arr=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per"  );
			$trimsDataArr=sql_select("select c.po_break_down_id, max(a.costing_per) as costing_per, b.trim_group, max(b.cons_uom) as cons_uom, sum(b.cons_dzn_gmts) cons_dzn_gmts from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b , wo_pre_cost_trim_co_cons_dtls c where a.job_no=b.job_no and b.id=c.wo_pre_cost_trim_cost_dtls_id and b.status_active=1 and b.is_deleted=0 and c.po_break_down_id=$po_break_down_id group by b.trim_group, c.po_break_down_id");
			$i=1; $tot_accss_req_qnty=0; $tot_recv_qnty=0; $tot_iss_qnty=0; $tot_recv_bl_qnty=0; $tot_trims_left_over_qnty=0;
			foreach($trimsDataArr as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$dzn_qnty='';
				if($row[csf('costing_per')]==1) $dzn_qnty=12;
				else if($row[csf('costing_per')]==3) $dzn_qnty=12*2;
				else if($row[csf('costing_per')]==4) $dzn_qnty=12*3;
				else if($row[csf('costing_per')]==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;
				
				$dzn_qnty=$dzn_qnty*$ratio;
        		$accss_req_qnty=($row[csf('cons_dzn_gmts')]/$dzn_qnty)*$tot_po_qnty;
				
				$trims_recv=$trims_array[$row[csf('trim_group')]]['recv'];
				$trims_issue=$trims_array[$row[csf('trim_group')]]['iss'];
				$recv_bl=$accss_req_qnty-$trims_recv;
				$trims_left_over=$trims_recv-$trims_issue;
			?>
            	<tr bgcolor="<? echo $bgcolor; ?>">
                    <td><p><? echo $item_library[$row[csf('trim_group')]]; ?>&nbsp;</p></td>
                    <td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($accss_req_qnty,2,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($trims_recv,2,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($recv_bl,2,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($trims_issue,2,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($trims_left_over,2,'.',''); ?>&nbsp;</td>
                </tr>
            <?
				$tot_accss_req_qnty+=$accss_req_qnty;
				$tot_recv_qnty+=$trims_recv;
				$tot_recv_bl_qnty+=$recv_bl;
				$tot_iss_qnty+=$trims_issue;
				$tot_trims_left_over_qnty+=$trims_left_over;
				$i++;
			}
			$tot_trims_left_over_qnty_perc=($tot_trims_left_over_qnty/$tot_recv_qnty)*100;
			?>
            <tfoot>
                <tr>
                    <th align="right">&nbsp;</th>
                    <th align="right">Total</th>
                    <th align="right"><? echo number_format($tot_accss_req_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_recv_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_recv_bl_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_iss_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_trims_left_over_qnty,0,'.',''); ?>&nbsp;</th>
                </tr>
             </tfoot>
        </table>  
        </div>
    </fieldset>
<?	

	exit();
}
//Ex-Factory Delv. and Return
if($action=="ex_factory_popup")
{
 	echo load_html_head_contents("Ex-Factory Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $id;//$job_no;
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:500px"> 
        <div class="form_caption" align="center"><strong>Ex-Factory Details</strong></div><br />
            <div style="width:100%"> 
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="90">Ex-fac. Date</th>
                        <th width="120">System /Challan no</th>
                        <th width="100">Ex-Fact. Del.Qty.</th>
                        <th width="">Ex-Fact.Return Qty.</th>
                       
                     </tr>   
                </thead> 	 	
            </table>  
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1;
              
				$exfac_sql=("select b.challan_no,a.sys_number,b.ex_factory_date, 
				CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
				CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty 
				from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.po_break_down_id in($id) ");
                $sql_dtls=sql_select($exfac_sql);
                
                foreach($sql_dtls as $row_real)
                { 
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";                               
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="35"><? echo $i; ?></td> 
                        <td width="90"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
                        <td width="120"><? echo $row_real[csf("sys_number")]; ?></td>
                        <td width="100" align="right"><? echo $row_real[csf("ex_factory_qnty")]; ?></td>
                         <td width="" align="right"><? echo $row_real[csf("ex_factory_return_qnty")]; ?></td>
                    </tr>
                    <? 
                    $rec_qnty+=$row_real[csf("ex_factory_qnty")];
					 $rec_return_qnty+=$row_real[csf("ex_factory_return_qnty")];
                    $i++;
                }
                ?>
                <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th><? echo number_format($rec_qnty,2); ?></th>
                    <th><? echo number_format($rec_return_qnty,2); ?></th>
                </tr>
                <tr>
                 <th colspan="3">Total Balance</th>
                 <th colspan="2" align="right"><? echo number_format($rec_qnty-$rec_return_qnty,2); ?></th>
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
