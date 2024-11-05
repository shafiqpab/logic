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
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.commercials.php');
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
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
			exit();
}


if ($action=="report_button_setting")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=11 and report_id=62 and is_deleted=0 and status_active=1");
	echo "print_report_button_setting('$print_report_format');\n"; 
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


//if($action=="quotation_popup")
if($action=="quotation_popup")
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1,'','','');
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
                    <th id="search_by_td_up" width="170">Please Enter Style </th>
                    <th>Quot. Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Style Ref",2=>"Inquery Id",3=>"Quotation Id",4=>"Mkt No");
							$dd="change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "3",$dd,0 );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_quotation_no_search_list_view', 'search_div', 'order_sheet_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
if($action=="create_quotation_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	//echo $data[1];
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_id=$data[1]";
	}
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	$search_field_cond="";
	if($data[3]!="")
	{
	if($search_by==1) 
		$search_field_cond=" and a.style_ref LIKE '%".trim($data[3])."%'"; 
	else if($search_by==2) 
		$search_field_cond=" and a.inquery_id='".trim($data[3])."'"; 
	else if($search_by==3) 
		$search_field_cond=" and a.id=".trim($data[3])."";
	else if($search_by==4) 
		$search_field_cond=" and a.mkt_no='".trim($data[3])."'"; 	
	}
	
	$start_date =trim($data[4]);
	$end_date =trim($data[5]);	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.quot_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.quot_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	if($db_type==0) $year_field="YEAR(a.insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	$arr=array(0=>$company_library,1=>$buyer_arr);
	$sql= "select a.id, $year_field a.inquery_id, a.company_id, a.buyer_id,a.quot_date, a.style_ref from wo_price_quotation a where  a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond $buyer_id_cond $date_cond order by a.id, a.quot_date";
	echo create_list_view("tbl_list_search", "Company,Buyer,Year,Quotation No,Style Ref.,Inquery ID, Quotation Date", "80,130,50,60,130,130","760","220",0, $sql , "js_set_value", "id,id", "", 1, "company_id,buyer_id,0,0,0,0,0", $arr , "company_id,buyer_id,year,id,style_ref,inquery_id,quot_date", "",'','0,0,0,0,0,0,3','',1) ;
   exit(); 
}//Order Search End

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
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
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
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
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" value="<? echo $txt_job_no?>" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+'<? echo $type; ?>', 'create_job_no_search_list_view', 'search_div', 'order_sheet_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	$type_id=$data[6];
	//echo $type_id;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
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
	$search_string=trim($data[3]);
	$search_cond="";
	if($search_string!="")
	{
		if($search_by==2) $search_cond=" and a.style_ref_no='$data[3]'";
		elseif($search_by==1) $search_cond="and a.job_no_prefix_num=$data[3]";
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
	if($type_id==1) $job_cond="id,job_no_prefix_num";
	else if($type_id==2) $job_cond="id,style_ref_no";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	if($type_id==1 || $type_id==2 )
	{
		$sql= "select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $year_field from wo_po_details_master a where a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $search_cond $buyer_id_cond $year_cond  order by a.job_no";
	
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "$job_cond", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
	}
	else
	{
		  $sql= "select a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name,a.style_ref_no, $year_field,b.id,b.po_number from wo_po_details_master a,wo_po_break_down b where  a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $search_cond $buyer_id_cond $year_cond  order by a.job_no";
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No,PO No", "120,130,80,60,100","700","240",0, $sql , "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number", "",'','0,0,0,0,0,0','') ;
	}
	exit(); 
} // Job Search end


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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year_selection').value+'**'+'<? echo $txt_style_ref; ?>'+'**'+'<? echo $style_ref_id; ?>', 'order_search_list_view', 'search_div', 'order_sheet_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
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



if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$reporttype=str_replace("'","",$reporttype);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_style_owner=str_replace("'","",$cbo_style_owner);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$job_no=str_replace("'","",$txt_job_no);
	$job_no_id=str_replace("'","",$txt_job_no_id);
	$year_id=str_replace("'","",$cbo_year);
	//echo $job_no.'dD';die;

	$txt_style_ref=str_replace("'","",$txt_style_ref);
	
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$style_ref_id=str_replace("'","",$txt_style_ref_id);
	if($cbo_company_name==0) $company_name_cond=""; else $company_name_cond=" and a.company_name='$cbo_company_name' ";
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{ 
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_id_cond2=" and buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else{
					$buyer_id_cond="";
					$buyer_id_cond2="";
			}
		}
		else
		{
			$buyer_id_cond="";
			$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
		$buyer_id_cond2=" and buyer_id=$cbo_buyer_name";
	}
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
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
	}
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
	$job_cond="";
	if(trim(str_replace("'","",$job_no))!="")
	{
		if(str_replace("'","",$job_no_id)!="")
		{
			$job_cond=" and a.id in(".str_replace("'","",$job_no_id).")";
		}
		else
		{
			$job_cond=" and a.job_no_prefix_num = '".trim(str_replace("'","",$job_no))."'";
		}
	}

	ob_start();

	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	
	if($reporttype==1) //Budget Button
	{
		$sql="select a.job_no_prefix_num as job_prefix,a.job_no, a.company_name, a.buyer_name,a.gauge, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id,b.po_quantity,b.po_total_price, b.po_number,b.unit_price, b.pub_shipment_date,c.order_quantity,c.plan_cut_qnty,c.color_number_id,c.size_number_id from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c  where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and  a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_name_cond $date_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $job_cond $year_cond order  by c.size_order,c.color_order";
		
		$sql_po_result=sql_select($sql);
		$all_po_id="";$all_job="";$all_full_job="";$all_jobs_full="";$all_style="";$all_gauge="";$all_po_no=""; $all_buyer=""; $all_style_desc=""; 
		$order_qty_pcs=0;$color_order_qty_pcs=0;$total_fob_value=0;$total_order_qty=0;$total_unit_price=0;$color_plan_cut_qnty=0;
		//echo $buyer_name;die;
		
		foreach($sql_po_result as $row)
		{
			if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
			if($all_po_no=="") $all_po_no=$row[csf("po_number")]; else $all_po_no.=",".$row[csf("po_number")];
			if($all_job=="") $all_job=$row[csf("job_prefix")]; else $all_job.=",".$row[csf("job_prefix")];
			if($all_jobs_full=="") $all_jobs_full=$row[csf("job_no")]; else $all_jobs_full.=",".$row[csf("job_no")];
			if($all_buyer=="") $all_buyer=$buyer_arr[$row[csf("buyer_name")]]; else $all_buyer.=",".$buyer_arr[$row[csf("buyer_name")]];
			if($all_full_job=="") $all_full_job="'".$row[csf('job_no')]."'"; else $all_full_job.=","."'".$row[csf('job_no')]."'";
			if($all_style=="") $all_style=$row[csf("style_ref_no")]; else $all_style.=",".$row[csf("style_ref_no")];
			if($all_style_desc=="") $all_style_desc=$row[csf("style_desc")]; else $all_style_desc.=",".$row[csf("style_desc")];
			if($row[csf("gauge")]!="")
			{
			if($all_gauge=="") $all_gauge=$gauge_arr[$row[csf("gauge")]]; else $all_gauge.=",".$gauge_arr[$row[csf("gauge")]];
			}
			
			$order_qty_pcs+=$row[csf('po_quantity')]*$row[csf('ratio')];
			$color_order_qty_pcs+=$row[csf('order_quantity')];
			$color_plan_cut_qnty+=$row[csf('plan_cut_qnty')];
			$total_order_qty+=$row[csf('po_quantity')];
			$total_unit_price+=$row[csf('unit_price')];
			$total_fob_value+=$row[csf('po_total_price')];
			$po_qty_by_job[$row[csf("job_no")]]=$row[csf('po_quantity')]*$row[csf('ratio')];
			$size_number_arr[$row[csf("size_number_id")]]=$row[csf('size_number_id')];
			$size_number_qty_arr[$row[csf("color_number_id")]][$row[csf("size_number_id")]]+=$row[csf('order_quantity')];
			$size_number_plan_qty_arr[$row[csf("color_number_id")]][$row[csf("size_number_id")]]+=$row[csf('plan_cut_qnty')];
			$color_size_arr[$row[csf("color_number_id")]]=$row[csf('color_number_id')];
		} 
		//print_r($po_qty_by_job);
		$all_gauge=rtrim($all_gauge,',');
		$all_job_no=array_unique(explode(",",$all_full_job));
		$all_po_nos=implode(",",array_unique(explode(",",$all_po_no)));
		$all_job_nos=implode(",",array_unique(explode(",",$all_job)));
		$all_jobs_full=implode(",",array_unique(explode(",",$all_jobs_full)));
		$all_gauge=implode(",",array_unique(explode(",",$all_gauge)));
		$all_style=implode(",",array_unique(explode(",",$all_style)));
		$all_style_desc=implode(",",array_unique(explode(",",$all_style_desc)));
		$all_buyer=implode(",",array_unique(explode(",",$all_buyer)));
		
		$all_jobs="";$all_yarn_comm="";$all_pi_id="";$all_supplier="";
		foreach($all_job_no as $jno)
		{
				if($all_jobs=="") $all_jobs=$jno; else $all_jobs.=",".$jno;
		}
		$sql_recev = sql_select("select c.product_name_details as yarn_desc,a.receive_basis,a.booking_id,a.supplier_id from  inv_receive_master a, inv_transaction b , product_details_master c 
		where b.job_no in($all_jobs) and a.id=b.mst_id and b.prod_id=c.id and a.entry_form=248");
		//echo "select c.product_name_details as yarn_desc,a.receive_basis,a.booking_id,a.supplier_id from  inv_receive_master a, inv_transaction b , product_details_master c 
		//where b.job_no in($all_jobs) and a.id=b.mst_id and b.prod_id=c.id  and a.entry_form=248";
		
		foreach($sql_recev as $row)
		{
			//echo $row[csf('item_description')].'XX';
			if($row[csf('yarn_desc')]!="")
			{
			$yarn_comm=explode(",",$row[csf('yarn_desc')]);
			if($all_yarn_comm=="") $all_yarn_comm=$yarn_comm[0]; else $all_yarn_comm.=",".$yarn_comm[1];
			if($row[csf('receive_basis')]==1)
			{
				if($all_pi_id=="") $all_pi_id=$row[csf('booking_id')]; else $all_pi_id.=",".$row[csf('booking_id')];
			}
			if($all_supplier=="") $all_supplier=$supplier_arr[$row[csf('supplier_id')]]; else $all_supplier.=",".$supplier_arr[$row[csf('supplier_id')]];
			}
		}
		$sql_pi = sql_select("select a.pi_number,c.id as id, c.lc_number as lc_number,a.supplier_id as supplier_id,a.currency_id as currency_id
		from com_pi_master_details a
		left join com_btb_lc_pi b on a.id=b.pi_id
		left join com_btb_lc_master_details c on b.com_btb_lc_master_details_id=c.id
		where
		a.item_category_id = 1 and
		a.status_active=1 and a.is_deleted=0 and
		a.id in($all_pi_id)");
		
		$all_lc_number="";$all_pi_number="";
		foreach($sql_pi as $row)
		{
			//$lc_number=$row[csf("lc_number")];
			$pi_number=$row[csf("pi_number")];
			if($all_lc_number=="") $all_lc_number=$row[csf("lc_number")]; else $all_lc_number.=",".$row[csf("lc_number")];
			if($all_pi_number=="") $all_pi_number=$row[csf("pi_number")]; else $all_pi_number.=",".$row[csf("pi_number")];
		}
	
		$all_pi_number=implode(",",array_unique(explode(",",$all_pi_number)));
		$all_lc_number=implode(",",array_unique(explode(",",$all_lc_number)));
		$all_supplier=implode(",",array_unique(explode(",",$all_supplier)));
		$all_yarn_comm=rtrim($all_yarn_comm,',');
		
	    //echo $all_jobs;
		$styleRef=explode(",",$txt_style_ref);
		$all_style_job="";
		foreach($styleRef as $sid)
		{
				if($all_style_job=="") $all_style_job="'".$sid."'"; else $all_style_job.=","."'".$sid."'";
		}
		$sql_strip=sql_select("select color_number_id,stripe_color,sample_color from wo_pre_stripe_color where job_no in($all_jobs) and status_active=1 order by color_number_id,sample_color");
		//echo "select color_number_id,stripe_color from wo_pre_stripe_color where job_no in($all_jobs) and status_active=1";
		foreach($sql_strip as $row)
		{
			if($row[csf("color_number_id")]>0)
			{
				$color_stripe_arr[$row[csf("color_number_id")]].=$row[csf("stripe_color")].',';
				$color_sample_arr[$row[csf("color_number_id")]][$row[csf("stripe_color")]]=$row[csf("sample_color")];
			}
		}
		$sql_weight=sql_select("select consdznlbs from wo_pre_cost_fab_yarn_cost_dtls where job_no in($all_jobs) and status_active=1");
		//echo "select avg_cons_qnty from wo_pre_cost_fab_yarn_cost_dtls where job_no in($all_jobs) and status_active=1";
		$tot_weight_per_dzn=0;
		foreach($sql_weight as $row)
		{
			if($row[csf("consdznlbs")]>0)
			{
				$tot_weight_per_dzn+=$row[csf("consdznlbs")];
			}
		}
		
		//print_r($color_stripe_arr);
		$style1="#E9F3FF"; 
		$style="#FFFFFF";
				
		?>
        <div style="width:100%">
		        <style>
					@media print {
						/* #page_break_div { 
							page-break-before: always;
						} */
						.footer_signature {
							position:fixed;
							height:auto;
							bottom:0;
							width:100%;
						}
						/* .print+.print {
								page-break-before: always;
						}  */
					}
					hr {
						height: 2px;
						background-color: #ccc;
						border: none;
					}
				</style>
		       <!-- <div class="footer_signature" >
		         <?
		          //echo signature_table(109, $cbo_company_name, "850px");
				 ?>
		      	</div>-->
        
	             <table width="800px" style="margin-left:10px">
	           
	                <tr>
	                    <td align="center" colspan="8" class="form_caption"><strong><? echo $company_library[$cbo_company_name].'<br>';if($cbo_style_owner!=0) echo 'Style Owner: '.$company_library[$cbo_style_owner]; ?></strong></td>
	                </tr>
					  <tr class="form_caption">
	                    <td colspan="8" align="center"><strong style=" font-size:18px"><? echo $report_title;?></strong></td>
	                </tr>
	            </table>
	             <table width="800" style="margin-left:10px;" class="rpt_table" cellpadding="0" cellspacing="0" border="2" rules="all" id="table_header_1">
		                <tr>
		                 	<th  colspan="6" align="center" style="font-size:16px; "> <strong>Summary</strong></th>
		               	</tr> 
			            <tr bgcolor="<? echo $style;?>">
						 	<td>Style No : </td> <td><? echo $all_style;?></td>
							<td>Gauge: </td> <td><p><? echo $all_gauge;?></p></td>
							<td>Yarn Composition : </td> <td><p><? echo $all_yarn_comm;?></p></td>
						</tr>
						  <tr bgcolor="<? echo $style1;?>">
							 	<td>Style Desc : </td> <td><p><? echo $all_style_desc;?></td>
								<td>Order No: </td> <td style=""><p><? echo $all_po_nos;?></p></td>
								<td>Yarn Supplier : </td> <td><p><? echo $all_supplier;?></p></td>
			             </tr>
					   <tr bgcolor="<? echo $style;?>">
						 	<td>Job No: </td> <td><p><? echo $all_jobs_full;?></p></td>
							<td>PI No: </td> <td><p><? echo $all_pi_number;?></p></td>
							<td>L/C No : </td> <td><p><? echo $all_lc_number;?></p></td>
		            	</tr>
					  <tr bgcolor="<? echo $style1;?>">
					 	<td>Weight Per Dzn: </td> <td><? echo number_format($tot_weight_per_dzn,2);?></td>
						<td>Buyer: </td> <td><p><? echo $all_buyer;?></p></td>
						<td>Total PO Qty : </td> <td><? echo number_format($color_order_qty_pcs,0);?></td>
		              </tr>
                       <tr bgcolor="<? echo $style;?>">
						<td>Plan Knit Qty : </td> <td><? echo number_format($color_plan_cut_qnty,0);?></td>
                        <td colspan="4"> </td>
                        
		              </tr>
	            </table>
          		 <br/>
			   <?
			   $width="800";
			   ?>
			   <table id="table_header_1" style="margin-left:10px" class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
	           		<caption> <b style="float:left">Color Details</b></caption>
						<thead>
						<tr>
	                    	<th rowspan="2" width="150">Color Name</th>
							<th rowspan="2" width="80">Sample Color</th>
							<th  colspan="<? echo count($size_number_arr);?>">Size</th>
							<th rowspan="2" width="100">Qty(PCS)</th>
						</tr>
						<tr>
							<?
							foreach($size_number_arr as $size_key=>$val)
							{
							?>
							<th width=""><? echo $itemSizeArr[$size_key];?></th>
							<?
							}
							?>
						</tr>	
	                    </thead>
						<?
							$k=1;$tot_color_qty=0;$tot_plan_color_qty=0;
							foreach($color_size_arr as $color_id=>$color_data)
							{
							if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$color_stripe=rtrim($color_stripe_arr[$color_id],',');
							//echo $color_stripe.'x';
							$color_stripes=array_unique(explode(",",$color_stripe));
							$po_color_stripe_arr[$color_stripe]=$color_stripe;
							 ?>
							 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
								<td width="150"><b> <? echo $color_library[$color_id].'(PO Qty)'.'<hr>'.$color_library[$color_id].'(Plan Knit)'; ?></b></td>
								<td width="80"><b><? //echo $color_library[$color_id]; ?></b></td>
								<?
								foreach($size_number_arr as $size_key=>$val)
								{
								$size_qty=$size_number_qty_arr[$color_id][$size_key];
								$plan_size_qty=$size_number_plan_qty_arr[$color_id][$size_key];
								$color_qty[$color_id]+=$size_qty;
								$color_plan_qty[$color_id]+=$plan_size_qty;
								$size_qty_arr[$size_key]+=$size_qty;
								$size_plan_qty_arr[$size_key]+=$plan_size_qty;
								?>
								<td width=""  align="right"><? echo number_format($size_qty,0).'<hr>'.number_format($plan_size_qty,0); ?></td>
								<?
								}
								?>
								<td width="100"  align="right"><? echo number_format($color_qty[$color_id],0).'<hr>'.number_format($color_plan_qty[$color_id],0); ?></td>
							</tr>
							 <?
							 $k++;
							$tot_color_qty+=$color_qty[$color_id];
							$tot_plan_color_qty+=$color_plan_qty[$color_id];
							if($color_stripe!="")
							{
							  foreach($color_stripes as $scolor)
							  {
							  
						?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
								<td width="150" title="Stripe Color"><? echo $color_library[$scolor]; ?></td>
								<td width="80" title="Sample Color<? echo $color_sample_arr[$color_id][$scolor]?>"><? echo $color_library[$color_sample_arr[$color_id][$scolor]]; ?></td>
								<?
								foreach($size_number_arr as $size_key=>$val)
								{
								//$size_qty=$size_number_qty_arr[$color_id][$size_key];
								//$color_qty[$color_id]+=$size_qty;
								//$size_qty[$size_key]+=$size_qty;
								?>
								<td width=""  align="right"><? //echo $size_qty; ?></td>
								<?
								}
								?>
								<td width="100"  align="right"><? //echo $color_qty[$color_id]; ?></td>
							</tr>
							<?
								$k++;
								//$tot_color_qty+=$color_qty[$color_id];
								}
							 
							  }
							}
							?>
							<tr bgcolor="#CCCCCC">
								<td align="right" colspan="2"><b>Total PO Qty: </b> </td>
								
								<?
								foreach($size_number_arr as $size_key=>$val)
								{
								?>
								<td   align="right"><? echo number_format($size_qty_arr[$size_key],0); ?></td>
								<?
								}
								?>
								<td   width="100" align="right"><? echo number_format($tot_color_qty,0); ?></td>
							</tr>
                            <tr bgcolor="#CCCCCC">
								<td align="right" colspan="2"><b>Total Plan Knit: </b> </td>
								
								<?
								foreach($size_number_arr as $size_key=>$val)
								{
								?>
								<td   align="right"><? echo number_format($size_plan_qty_arr[$size_key],0); ?></td>
								<?
								}
								?>
								<td   width="100" align="right"><? echo number_format($tot_plan_color_qty,0); ?></td>
							</tr>
							<tr>
								<td align="left"><b>Remarks: </b> </td>
								<td colspan="<? echo count($size_number_arr)+2;?>"   align="center">&nbsp;</td>
							</tr>
	            </table>
			
	             <?
				//die;
	                 echo signature_table(163, $cbo_company_name, "800px");
	            ?>
	           <div id="page_break_div" class="print">
	          
	            </div>
           
        </div> <!--Main Div End-->
		<?
	}
	else if($reporttype==2)
	{

				 $sql="select a.job_no_prefix_num as job_prefix,a.job_no, a.company_name, a.buyer_name,a.gauge, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id,b.po_quantity,b.po_total_price, b.po_number,b.unit_price, b.pub_shipment_date,c.order_quantity,c.plan_cut_qnty,c.color_number_id,c.size_number_id from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c  where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and  a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_name_cond $date_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $job_cond $year_cond order  by c.size_order,c.color_order";
				
				$sql_po_result=sql_select($sql);
				$all_po_id="";$all_job="";$all_full_job="";$all_jobs_full="";$all_style="";$all_gauge="";$all_po_no=""; $all_buyer=""; $all_style_desc=""; 
				$order_qty_pcs=0;$color_order_qty_pcs=0;$total_fob_value=0;$total_order_qty=0;$total_unit_price=0;$color_plan_cut_qnty=0;

				$job_no_arr=array();
				//echo $buyer_name;die;
				
				foreach($sql_po_result as $row)
				{
					array_push($job_no_arr, $row[csf('job_no')]);
					if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
					if($all_po_no=="") $all_po_no=$row[csf("po_number")]; else $all_po_no.=",".$row[csf("po_number")];
					if($all_job=="") $all_job=$row[csf("job_prefix")]; else $all_job.=",".$row[csf("job_prefix")];
					if($all_jobs_full=="") $all_jobs_full=$row[csf("job_no")]; else $all_jobs_full.=",".$row[csf("job_no")];
					if($all_buyer=="") $all_buyer=$buyer_arr[$row[csf("buyer_name")]]; else $all_buyer.=",".$buyer_arr[$row[csf("buyer_name")]];
					if($all_full_job=="") $all_full_job="'".$row[csf('job_no')]."'"; else $all_full_job.=","."'".$row[csf('job_no')]."'";
					if($all_style=="") $all_style=$row[csf("style_ref_no")]; else $all_style.=",".$row[csf("style_ref_no")];
					if($all_style_desc=="") $all_style_desc=$row[csf("style_desc")]; else $all_style_desc.=",".$row[csf("style_desc")];
					if($row[csf("gauge")]!="")
					{
						if($all_gauge=="") $all_gauge=$gauge_arr[$row[csf("gauge")]]; else $all_gauge.=",".$gauge_arr[$row[csf("gauge")]];
					}
					
					$order_qty_pcs+=$row[csf('po_quantity')]*$row[csf('ratio')];
					$color_order_qty_pcs+=$row[csf('order_quantity')];
					$total_order_qty+=$row[csf('po_quantity')];
					$color_plan_cut_qnty+=$row[csf('plan_cut_qnty')];
					$total_unit_price+=$row[csf('unit_price')];
					$total_fob_value+=$row[csf('po_total_price')];
					$po_qty_by_job[$row[csf("job_no")]]=$row[csf('po_quantity')]*$row[csf('ratio')];
					$size_number_arr[$row[csf("size_number_id")]]=$row[csf('size_number_id')];
					$size_number_qty_arr[$row[csf("color_number_id")]][$row[csf("size_number_id")]]+=$row[csf('order_quantity')];
					$size_number_plan_qty_arr[$row[csf("color_number_id")]][$row[csf("size_number_id")]]+=$row[csf('plan_cut_qnty')];
					$color_size_arr[$row[csf("color_number_id")]]=$row[csf('color_number_id')];
				} 
				//print_r($po_qty_by_job);
				$all_gauge=rtrim($all_gauge,',');
				$all_job_no=array_unique(explode(",",$all_full_job));
				$all_po_nos=implode(",",array_unique(explode(",",$all_po_no)));
				$all_job_nos=implode(",",array_unique(explode(",",$all_job)));
				$all_jobs_full=implode(",",array_unique(explode(",",$all_jobs_full)));
				$all_gauge=implode(",",array_unique(explode(",",$all_gauge)));
				$all_style=implode(",",array_unique(explode(",",$all_style)));
				$all_style_desc=implode(",",array_unique(explode(",",$all_style_desc)));
				$all_buyer=implode(",",array_unique(explode(",",$all_buyer)));
				
				$all_jobs="";$all_yarn_comm="";$all_pi_id="";$all_supplier="";
				foreach($all_job_no as $jno)
				{
						if($all_jobs=="") $all_jobs=$jno; else $all_jobs.=",".$jno;
				}
				$sql_recev=	sql_select("select c.product_name_details as yarn_desc,a.receive_basis,a.booking_id,a.supplier_id from  inv_receive_master a, inv_transaction b , product_details_master c 
				where b.job_no in($all_jobs) and a.id=b.mst_id and b.prod_id=c.id and a.entry_form=248");
				//echo "select c.product_name_details as yarn_desc,a.receive_basis,a.booking_id,a.supplier_id from  inv_receive_master a, inv_transaction b , product_details_master c 
				//where b.job_no in($all_jobs) and a.id=b.mst_id and b.prod_id=c.id  and a.entry_form=248";
				
				foreach($sql_recev as $row)
				{
						//echo $row[csf('item_description')].'XX';
						
						if($row[csf('yarn_desc')]!="")
						{
						$yarn_comm=explode(",",$row[csf('yarn_desc')]);
						if($all_yarn_comm=="") $all_yarn_comm=$yarn_comm[0]; else $all_yarn_comm.=",".$yarn_comm[1];
						if($row[csf('receive_basis')]==1)
						{
							if($all_pi_id=="") $all_pi_id=$row[csf('booking_id')]; else $all_pi_id.=",".$row[csf('booking_id')];
						}
						if($all_supplier=="") $all_supplier=$supplier_arr[$row[csf('supplier_id')]]; else $all_supplier.=",".$supplier_arr[$row[csf('supplier_id')]];
						}
				}
				$sql_pi = sql_select("select a.pi_number,c.id as id, c.lc_number as lc_number,a.supplier_id as supplier_id,a.currency_id as currency_id
				from com_pi_master_details a
				left join com_btb_lc_pi b on a.id=b.pi_id
				left join com_btb_lc_master_details c on b.com_btb_lc_master_details_id=c.id
				where
				a.item_category_id = 1 and
				a.status_active=1 and a.is_deleted=0 and
				a.id in($all_pi_id)");
				
				$all_lc_number="";$all_pi_number="";
				foreach($sql_pi as $row)
				{
					//$lc_number=$row[csf("lc_number")];
					$pi_number=$row[csf("pi_number")];
					if($all_lc_number=="") $all_lc_number=$row[csf("lc_number")]; else $all_lc_number.=",".$row[csf("lc_number")];
					if($all_pi_number=="") $all_pi_number=$row[csf("pi_number")]; else $all_pi_number.=",".$row[csf("pi_number")];
				}
			
				$all_pi_number=implode(",",array_unique(explode(",",$all_pi_number)));
				$all_lc_number=implode(",",array_unique(explode(",",$all_lc_number)));
				$all_supplier=implode(",",array_unique(explode(",",$all_supplier)));
				$all_yarn_comm=rtrim($all_yarn_comm,',');
				
			    //echo $all_jobs;
				$styleRef=explode(",",$txt_style_ref);
				$all_style_job="";
				foreach($styleRef as $sid)
				{
						if($all_style_job=="") $all_style_job="'".$sid."'"; else $all_style_job.=","."'".$sid."'";
				}
				$sql_strip=sql_select("select color_number_id,stripe_color,sample_color from wo_pre_stripe_color where job_no in($all_jobs) and status_active=1 order by color_number_id,sample_color");
				//echo "select color_number_id,stripe_color from wo_pre_stripe_color where job_no in($all_jobs) and status_active=1";
				foreach($sql_strip as $row)
				{
					if($row[csf("color_number_id")]>0)
					{
						$color_stripe_arr[$row[csf("color_number_id")]].=$row[csf("stripe_color")].',';
						$color_sample_arr[$row[csf("color_number_id")]][$row[csf("stripe_color")]]=$row[csf("sample_color")];
					}
				}
				$sql_weight=sql_select("select consdznlbs from wo_pre_cost_fab_yarn_cost_dtls where job_no in($all_jobs) and status_active=1");
				//echo "select avg_cons_qnty from wo_pre_cost_fab_yarn_cost_dtls where job_no in($all_jobs) and status_active=1";
				$tot_weight_per_dzn=0;
				foreach($sql_weight as $row)
				{
					if($row[csf("consdznlbs")]>0)
					{
						$tot_weight_per_dzn+=$row[csf("consdznlbs")];
					}
				}
				
				//print_r($color_stripe_arr);
				$style1="#E9F3FF"; 
				$style="#FFFFFF";
						
				?>
		        <div style="width:100%">
				        <style>
							@media print {
									#page_break_div { 
										page-break-before: always;
									} 
									.footer_signature {
										position:fixed;
										height:auto;
										bottom:0;
										width:100%;
									}
									
								}
								hr {
									height: 2px;
									background-color: #ccc;
									border: none;
							    }
								
						</style>
				       <!-- <div class="footer_signature" >
				         <?
				          //echo signature_table(109, $cbo_company_name, "850px");
						 ?>
				      	</div>-->
		        
			             <table width="800px" style="margin-left:10px">
			           
			                <tr>
			                    <td align="center" colspan="8" class="form_caption"><strong><? echo $company_library[$cbo_company_name].'<br>';if($cbo_style_owner!=0) echo 'Style Owner: '.$company_library[$cbo_style_owner]; ?></strong></td>
			                </tr>
							  <tr class="form_caption">
			                    <td colspan="8" align="center"><strong style=" font-size:18px"><? echo $report_title;?></strong></td>
			                </tr>
			            </table>
			             <table width="800" style="margin-left:10px;" class="rpt_table" cellpadding="0" cellspacing="0" border="2" rules="all" id="table_header_1">
				                <tr>
				                 	<th  colspan="6" align="center" style="font-size:16px; "> <strong>Summary</strong></th>
				               	</tr> 
					            <tr bgcolor="<? echo $style;?>">
					            	<td>Job No: </td> <td><p><? echo $all_jobs_full;?></p></td>
								 	<td>Style No : </td> <td><? echo $all_style;?></td>
								 	<td>Buyer: </td> <td><p><? echo $all_buyer;?></p></td>
									
									
								</tr>
								  <tr bgcolor="<? echo $style1;?>">
									 	<td>Style Desc : </td> <td><p><? echo $all_style_desc;?></td>
										<td>Order No: </td> <td style=""><p><? echo $all_po_nos;?></p></td>
										<td>Gauge: </td> <td><p><? echo $all_gauge;?></p></td>
					             </tr>
							   
							  <tr bgcolor="<? echo $style;?>">
								<td colspan="3">Total Job Qty.(PCS) : </td> 
                                <td><? echo number_format($color_order_qty_pcs,0);?></td>
                                <td>Plan Knit Qty(PCS) : </td> <td><? echo number_format($color_plan_cut_qnty,0);?></td>
				              </tr>
			            </table>
		          		 <br/>
					   <?
					   $width="800";
					   ?>
					   <table id="table_header_1" style="margin-left:10px" class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
			           		<caption> <b style="float:left">Color  and Size Details</b></caption>
								<thead>
								<tr>
			                    	<th rowspan="2" width="150">Color Name</th>
                                    <th rowspan="2" width="80">Sample Color</th>
									
									<th  colspan="<? echo count($size_number_arr);?>">Size</th>
									<th rowspan="2" width="100">Qty(PCS)</th>
								</tr>
								<tr>
									<?
									foreach($size_number_arr as $size_key=>$val)
									{
										?>
										<th width=""><? echo $itemSizeArr[$size_key];?></th>
										<?
									}
									?>
								</tr>	
			                    </thead>
								<?
									$k=1;$tot_color_qty=0;$tot_plan_color_qty=0;
									foreach($color_size_arr as $color_id=>$color_data)
									{
										if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										$color_stripe=rtrim($color_stripe_arr[$color_id],',');
										//echo $color_stripe.'x';
										$color_stripes=array_unique(explode(",",$color_stripe));
										$po_color_stripe_arr[$color_stripe]=$color_stripe;//size_number_plan_qty_arr
										 ?>
										 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
											<td width="150"><b><? echo $color_library[$color_id].'(PO Qty)<hr>'.$color_library[$color_id].'(Plan Knit)'; ?></b></td>
                                            <td width="80" title="Gmts Color<? //echo $color_sample_arr[$color_id][$scolor]?>"><? //echo $color_library[$color_sample_arr[$color_id][$scolor]]; ?></td>
											
											<?
											foreach($size_number_arr as $size_key=>$val)
											{
											$size_qty=$size_number_qty_arr[$color_id][$size_key];
											$plan_size_qty=$size_number_plan_qty_arr[$color_id][$size_key];
											
											$color_qty[$color_id]+=$size_qty;
											$size_qty_arr[$size_key]+=$size_qty;
											
											$color_plan_qty[$color_id]+=$plan_size_qty;
											$size_paln_qty_arr[$size_key]+=$plan_size_qty;
											?>
											<td width=""  align="right"><? echo number_format($size_qty,0).'<hr>'.number_format($plan_size_qty,0); ?></td>
											<?
											}
											?>
											<td width="100"  align="right"><? echo number_format($color_qty[$color_id],0).'<hr>'.number_format($color_plan_qty[$color_id],0); ?></td>
										</tr>
									 	<?
									 	$k++;
										$tot_color_qty+=$color_qty[$color_id];
										$tot_plan_color_qty+=$color_plan_qty[$color_id];
										if($color_stripe!="")
										{
										  foreach($color_stripes as $scolor)
										  {
										  
												?>
													<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
														<td width="150" title="Stripe Color"><? echo $color_library[$scolor]; ?></td>
														<td width="80" title="Sample Color<? echo $color_sample_arr[$color_id][$scolor]?>"><? echo $color_library[$color_sample_arr[$color_id][$scolor]]; ?></td>
														<?
														foreach($size_number_arr as $size_key=>$val)
														{
														//$size_qty=$size_number_qty_arr[$color_id][$size_key];
														//$color_qty[$color_id]+=$size_qty;
														//$size_qty[$size_key]+=$size_qty;
														?>
														<td width=""  align="right"><? //echo $size_qty; ?></td>
														<?
														}
														?>
														<td width="100"  align="right"><? //echo $color_qty[$color_id]; ?></td>
													</tr>
													<?
														$k++;
														//$tot_color_qty+=$color_qty[$color_id];
											}
									 
									  	}
									}
									?>
									<tr bgcolor="#CCCCCC">
										<td align="right" colspan="2"><b>Total Po Qty: </b> </td>
										
										<?
										foreach($size_number_arr as $size_key=>$val)
										{
										?>
										<td   align="right"><? echo number_format($size_qty_arr[$size_key],0); ?></td>
										<?
										}
										?>
										<td   width="100" align="right"><? echo number_format($tot_color_qty,0); ?></td>
									</tr>
                                    <tr bgcolor="#CCCCCC">
										<td align="right"  colspan="2"><b>Total Plan knit: </b> </td>
										<?
										foreach($size_number_arr as $size_key=>$val)
										{
										?>
										<td   align="right"><? echo number_format($size_paln_qty_arr[$size_key],0); ?></td>
										<?
										}
										?>
										<td   width="100" align="right"><? echo number_format($tot_plan_color_qty,0); ?></td>
									</tr>
									<tr>
										<td align="left"><b>Remarks: </b> </td>
										<td colspan="<? echo count($size_number_arr)+1;?>"   align="center">&nbsp;</td>
									</tr>
			            </table>

			            <br>
			            <br>

			            <?php 
			            	$job_no_arr=array_unique($job_no_arr);
			            	$job_no_cond= where_con_using_array($job_no_arr,1,"a.job_no");

			            	$data_sql='select a.id, a.fabric_cost_dtls_id, a.job_no, a.count_id, a.copm_one_id, a.percent_one, a.copm_two_id, a.percent_two, a.type_id, a.cons_ratio, a.cons_qnty, a.rate, a.amount, a.avg_cons_qnty, a.supplier_id, a.color, a.consdznlbs, a.rate_dzn, b.item_number_id, b.body_part_id, b.fabric_description, b.color_type_id, b.uom, c.color_number_id, c.stripe_color, c.measurement from wo_pre_cost_fab_yarn_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_pre_stripe_color c where a.fabric_cost_dtls_id=b.id and b.id=c.pre_cost_fabric_cost_dtls_id and a.color=c.stripe_color ' .$job_no_cond.' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1';
			            	//echo $data_sql;
							$data_arr_yarn=sql_select($data_sql);

							

							$sql_po="select a.job_no, a.total_set_qnty, b.id, b.po_number, b.grouping, b.file_no, b.pub_shipment_date, c.item_number_id, c.country_id, c.color_number_id, c.size_number_id, c.order_quantity, c.plan_cut_qnty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id ".$job_no_cond." and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id ASC";
							$sql_po_data=sql_select($sql_po); 
							$gmts_item_color_qty_arr=array();
							foreach($sql_po_data as $sql_po_row)
							{
								
								$gmts_item_color_qty_arr[$sql_po_row[csf('item_number_id')]][$sql_po_row[csf('color_number_id')]]+=$sql_po_row[csf('plan_cut_qnty')];
							}

							$job_no_cond=str_replace("a.job_no", "job_no", $job_no_cond);

							$costing_per_arr=return_library_array( "select job_no,costing_per from wo_pre_cost_mst where status_active=1 ".$job_no_cond, "job_no", "costing_per");


			             ?>

			                 <table id="table_header_1" class="rpt_table" width="1100" cellpadding="0" cellspacing="0" border="1" rules="all">
			                     <caption> <b style="float:left">Yarn Details :</b></caption>

			                     <thead>
			                     	<tr>
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
			                     	</tr>
			                        
			                     </thead>
			                     <tbody>
			             		<?
			                     $i=1; $totCons=0; $totReq=0; $totAmt=0;
			                     foreach ($data_arr_yarn as $yarn)
			                     {
			                     	$costing_per=$costing_per_arr[$yarn[csf('job_no')]];
			                     	$order_price_per_dzn=1;

									if($costing_per==1){
								        $order_price_per_dzn=12;
								        $costing_for=" DZN";
								    }
								    else if($costing_per==2){
								        $order_price_per_dzn=1;
								        $costing_for=" PCS";
								    }
								    else if($costing_per==3){
								        $order_price_per_dzn=24;
								        $costing_for=" 2 DZN";
								    }
								    else if($costing_per==4){
								        $order_price_per_dzn=36;
								        $costing_for=" 3 DZN";
								    }
								    else if($costing_per==5){
								        $order_price_per_dzn=48;
								        $costing_for=" 4 DZN";
								    }
			             			$poQty=0; $yarn_req_kg=0; $yarn_req_lbs=0;
			             			$poQty=$gmts_item_color_qty_arr[$yarn[csf('item_number_id')]][$yarn[csf('color_number_id')]];
			                         $yarn_req_kg=($yarn[csf('measurement')]/$order_price_per_dzn)*$poQty;
			             			$yarn_req_lbs=$yarn_req_kg*2.20462;
			                         $amount_req=$yarn_req_lbs*$yarn[csf('rate')];
			                         ?>
			                         <tr style="font-size:13px">
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
			             		unset($data_arr_yarn);
			                     ?>
			                     </tbody>
			                     <tfoot>
			                         <tr style="font-weight:bold; font-size:12px; text-align:right">
			                             <th colspan="8">Total:</th>
			                             <th align="right"><? echo number_format($totConsKg,4); ?></th>
			                             <th align="right"><? echo number_format($totConsLbs,4); ?> </th>
			                             <th align="right">&nbsp;</th>
			                             <th align="right"><? echo number_format($totAmt,4); ?> </th>
			                         </tr>
			                     </tfoot>
			                 </table>


			                 <br>
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

			                 		$job_no_cond= where_con_using_array($job_no_arr,1,"c.job_no");

			                 		$sql_pi="select a.id,a.supplier_id,a.pi_number,a.pi_date, a.remarks,b.work_order_dtls_id,c.yarn_comp_type1st,c.color_name,c.yarn_count as count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_type as type_id,c.supplier_order_quantity as qty,c.amount,c.rate from com_pi_master_details a, com_pi_item_details b,wo_non_order_info_dtls c where a.id=b.pi_id and b.work_order_dtls_id=c.id and a.item_category_id=1 and a.pi_basis_id=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ".$job_no_cond." order by c.id";
			                 	//echo $sql_pi;  
			                 	$result_data=sql_select($sql_pi);
			                 	$lib_yarn_count=return_library_array( "select yarn_count, id from lib_yarn_count", "id", "yarn_count");
			                         $i=1; $tot_qty=0; $totReq=0; $tot_amt=0;
			                         foreach ($result_data as $yarn)
			                         {
			                 			
			                 		 if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			                 			
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
					
			             <?
						//die;
			                 echo signature_table(163, $cbo_company_name, "800px");
			            ?>
			           <div id="page_break_div">
			          
			            </div>
		           
		        </div> <!--Main Div End-->
				<?
	}
   
	
	
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

?>
