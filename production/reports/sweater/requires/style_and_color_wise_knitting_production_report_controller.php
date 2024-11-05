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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_quotation_no_search_list_view', 'search_div', 'style_and_color_wise_knitting_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+'<? echo $type; ?>', 'create_job_no_search_list_view', 'search_div', 'style_and_color_wise_knitting_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year_selection').value+'**'+'<? echo $txt_style_ref; ?>'+'**'+'<? echo $style_ref_id; ?>', 'order_search_list_view', 'search_div', 'style_and_color_wise_knitting_production_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
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
			$cbo_ship_status=str_replace("'","",$cbo_status);
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
			
			$ship_status_cond="";
            if($cbo_ship_status==1) $ship_status_cond="and b.shiping_status in (1,2)"; else if($cbo_ship_status==2) $ship_status_cond="and b.shiping_status in (3)";
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

	if($reporttype==1) //Show Button
	{
		// ========================================= MAIN QUERY =======================================================================================    

		// $sql="select a.job_no_prefix_num as job_prefix,a.job_no, a.company_name, a.buyer_name,a.gauge, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id,b.po_quantity,b.po_total_price, b.po_number,b.unit_price, b.pub_shipment_date,c.order_quantity,c.color_number_id,c.size_number_id,a.yarn_quality,d.id,d.cutting_no,d.cut_num_prefix_no
		// from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,ppl_cut_lay_mst d
		// where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and  a.job_no=c.job_no_mst and a.job_no = d.job_no
		// and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active = 1
		// and d.is_deleted = 0 $company_name_cond $date_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $job_cond $year_cond 
		// order  by c.size_order,c.color_order";
		    
			$sql="SELECT a.job_no_prefix_num     AS job_prefix,
			a.job_no,
			a.buyer_name,
			a.gauge, 
			a.style_ref_no,
			a.job_quantity,
			c.color_number_id,
			c.size_number_id,
			c.order_quantity,
			a.yarn_quality,
			d.id,
			d.cutting_no,
			d.cut_num_prefix_no,
			f.lot
	   FROM wo_po_details_master      a,
			wo_po_break_down          b,
			wo_po_color_size_breakdown c,
			ppl_cut_lay_mst  d,
			ppl_cut_lay_dtls e,
			ppl_cut_lay_prod_dtls f
	  WHERE     a.id = b.job_id
			AND b.id = c.po_break_down_id
			AND a.id = c.job_id
			AND a.job_no = d.job_no
			and d.id=e.mst_id
			and c.color_number_id=e.color_id
			and d.id=f.mst_id
			and e.id=f.dtls_id
			AND a.status_active = 1
			AND a.is_deleted = 0
			AND b.status_active = 1
			AND b.is_deleted = 0
			AND c.status_active = 1
			AND c.is_deleted = 0
			AND d.status_active = 1
			AND d.is_deleted = 0
			 $company_name_cond $date_cond $job_no_cond $job_style_cond $ship_status_cond $buyer_id_cond $job_cond $year_cond 
			order  by c.size_order,c.color_order";

			// echo $sql;
		
		$sql_po_result=sql_select($sql);
        // $job_array=array();
		$data_array=array();
		foreach($sql_po_result as $value)
		{
			$data_array[$value[csf('job_no')]][$value[csf('color_number_id')]][$value[csf('cutting_no')]]['cut_num_prefix_no']=$value[csf('cut_num_prefix_no')];
			// $job_array[$value[csf('job_no')]]['job_quantity']= $value[csf('job_quantity')];
		}
		// $all_job = "'".implode("','",$job_array)."'";
		// echo"<pre>";
		// print_r($job_array);die;

		$all_job="";$all_full_job="";$all_jobs_full="";$all_style="";$all_gauge=""; $all_buyer=""; $all_style_desc=""; 
		//echo $buyer_name;die;
		
		foreach($sql_po_result as $row)
		{
			// echo "<pre>";
			// print_r($row);
			if($all_jobs_full=="") $all_jobs_full=$row[csf("job_no")]; else $all_jobs_full.=",".$row[csf("job_no")];
			if($all_buyer=="") $all_buyer=$buyer_arr[$row[csf("buyer_name")]]; else $all_buyer.=",".$buyer_arr[$row[csf("buyer_name")]];
			if($all_full_job=="") $all_full_job="'".$row[csf('job_no')]."'"; else $all_full_job.=","."'".$row[csf('job_no')]."'";
			
			$size_number_arr[$row[csf("size_number_id")]]=$row[csf('size_number_id')];
			// $size_number_qty_arr[$row[csf("color_number_id")]][$row[csf("size_number_id")]]+=$row[csf('order_quantity')];
			$color_size_arr[$row[csf("color_number_id")]]=$row[csf('color_number_id')];
		} 
		//print_r($po_qty_by_job);
		$all_job_no=array_unique(explode(",",$all_full_job));
		$all_job_nos=implode(",",array_unique(explode(",",$all_job)));
		$all_jobs_full=implode(",",array_unique(explode(",",$all_jobs_full)));
		
		
		$all_jobs="";$all_yarn_comm="";$all_pi_id="";$all_supplier="";
		foreach($all_job_no as $jno)
		{
				if($all_jobs=="") $all_jobs=$jno; else $all_jobs.=",".$jno;
		}
		//echo $all_jobs;
		
		// ========================================= Strip Color Query ======================================================
		$sql_strip=sql_select("select job_no, color_number_id,stripe_color,sample_color from wo_pre_stripe_color where job_no in($all_jobs) and status_active=1 order by color_number_id,sample_color");
		// echo "select color_number_id,stripe_color from wo_pre_stripe_color where job_no in($all_jobs) and status_active=1";
		foreach($sql_strip as $row)
		{
			if($row[csf("color_number_id")]>0)
			{
				$color_stripe_arr[$row[csf("job_no")]][$row[csf("color_number_id")]].=$row[csf("stripe_color")].',';
				$color_sample_arr[$row[csf("job_no")]][$row[csf("color_number_id")]][$row[csf("stripe_color")]]=$row[csf("sample_color")];
			}
		}
		// echo"<pre>";
		// print_r($color_sample_arr);
		//print_r($color_stripe_arr);
		// ========================================= Lot Name Query ==============================================================

		$lot_sql="SELECT a.color_id,a.lot,b.job_no,b.cutting_no FROM ppl_cut_lay_prod_dtls a, ppl_cut_lay_mst  b, ppl_cut_lay_dtls c WHERE  a.mst_id = b.id AND a.dtls_id = c.id AND b.id = c.mst_id AND b.job_no IN($all_jobs) AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0";
		// echo $lot_sql;
        $lot_sql_result=sql_select($lot_sql);
	    $lot_data_array= array();
		foreach($lot_sql_result as $res)
		{
			$lot_data_array[$res[csf('job_no')]][$res[csf('cutting_no')]]['lot'].=$res[csf('lot')].',';
		}
		// echo "<pre>";
		// print_r($lot_data_array);
		// ========================================= Total Bundle Qnty Query ======================================================

		$bundle_sql="SELECT b.job_no, COUNT(a.id) as total_bundle, b.cutting_no FROM ppl_cut_lay_bundle a, ppl_cut_lay_mst b, ppl_cut_lay_dtls c WHERE a.mst_id = b.id AND a.dtls_id = c.id AND b.id = c.mst_id AND b.job_no IN ($all_jobs) AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 GROUP BY 	b.job_no,
		b.cutting_no";
        $bundle_sql_result=sql_select($bundle_sql);	
		$bundle_sql_data_array= array();
		foreach($bundle_sql_result as $res)
		{
			$bundle_sql_data_array[$res[csf('job_no')]][$res[csf('cutting_no')]]['total_bundle']+=$res[csf('total_bundle')];
		}
		// echo "<pre>";
		// print_r($bundle_sql_data_array);
        // ========================================= Knitting Qnty Query ==========================================================
		
		$knitting_qty_sql="SELECT f.job_no, d.size_number_id, d.color_number_id, c.cut_no, SUM (c.production_qnty) AS production_qnty FROM pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown  d,  wo_po_break_down e, wo_po_details_master f WHERE a.id = c.mst_id AND c.color_size_break_down_id = d.id AND d.po_break_down_id = e.id AND a.po_break_down_id = e.id AND e.job_no_mst = f.job_no AND a.production_type = 51 AND f.job_no IN ($all_jobs) AND c.status_active = 1 AND c.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND e.status_active = 1 AND e.is_deleted = 0 AND f.status_active = 1 AND f.is_deleted = 0 GROUP BY  f.job_no, d.size_number_id, d.color_number_id, c.cut_no ORDER BY f.job_no ASC";
		$knitting_qty_sql_result=sql_select($knitting_qty_sql);
		$knitting_qty_array=array();
		$knitting_qty_tot_array=array();
		$knitting_qty_size_wise_tot_array=array();
		$knitting_qty_job_wise_tot_array=array();
		foreach($knitting_qty_sql_result as $res)
		{
            $knitting_qty_array[$res[csf('job_no')]][$res[csf('color_number_id')]][$res[csf('cut_no')]][$res[csf('size_number_id')]]['knitting_qnty']=$res[csf('production_qnty')];
            $knitting_qty_tot_array[$res[csf('job_no')]][$res[csf('color_number_id')]]+=$res[csf('production_qnty')];
            $knitting_qty_size_wise_tot_array[$res[csf('job_no')]][$res[csf('size_number_id')]]+=$res[csf('production_qnty')];
            $knitting_qty_job_wise_tot_array[$res[csf('job_no')]]+=$res[csf('production_qnty')];
		}
        // echo "<pre>";
		// print_r($knitting_qty_job_wise_tot_array);
		// ========================================= Order Qnty Query ============================================================
		$order_qnty_sql="SELECT a.job_no_mst,a.color_number_id,a.size_number_id,a.order_quantity FROM wo_po_color_size_breakdown a WHERE a.job_no_mst IN ($all_jobs) AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.size_order";

		$order_qnty_sql_result=sql_select($order_qnty_sql);
		$order_qnty_array=array();
		$order_qnty_size_wise_tot_array=array();
		$order_qnty_tot_array=array();
		foreach($order_qnty_sql_result as $res)
		{
			$order_qnty_array[$res[csf('job_no_mst')]][$res[csf('color_number_id')]][$res[csf('size_number_id')]]+=$res[csf('order_quantity')];
			$order_qnty_size_wise_tot_array[$res[csf('job_no_mst')]][$res[csf('size_number_id')]]+=$res[csf('order_quantity')];
			$order_qnty_tot_array[$res[csf('job_no_mst')]]+=$res[csf('order_quantity')];
		}
		// echo "<pre>";
		// print_r($order_qnty_tot_array);

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
				</style>
				<table width="1100px" style="margin-left:10px">
				
					<tr>
						<td align="center" colspan="8" class="form_caption"><strong><? echo $company_library[$cbo_company_name].'<br>';if($cbo_style_owner!=0) echo 'Style Owner: '.$company_library[$cbo_style_owner]; ?></strong></td>
					</tr>
						<tr class="form_caption">
						<td colspan="8" align="center"><strong style=" font-size:18px"><? echo $report_title;?></strong></td>
					</tr>
				</table>
				<?
				$width="1100"; 
				$color_row_span=array();
				$combo_color_rowspan=array();
				$size_rowspan=array();
				$ex_Sh_rowspan=array();
				foreach($data_array as $job_no=>$job_data) 
				{				
					foreach($job_data as $color_id=>$color_value) 
					{ 
						
						foreach($color_value as $cutting=>$row) 
						{ 
							$color_row_span[$job_no][$color_id]++;
							$ex_Sh_rowspan[$job_no][$color_id]++;
							$combo_color_rowspan[$job_no][$color_id]++;
							$size_rowspan[$job_no][$color_id]++;
						}
					}
				}
				// echo"<pre>";
				// print_r($ex_Sh_rowspan);
				?>
				<?
				$job_array_new=array();
				foreach($sql_po_result as $row)
		         {
					$job_array_new[$row[csf('job_no')]]['job_no']= $row[csf('job_no')];
					$job_array_new[$row[csf('job_no')]]['buyer_name']= $row[csf('buyer_name')];
					$job_array_new[$row[csf('job_no')]]['style_ref_no']= $row[csf('style_ref_no')];
					$job_array_new[$row[csf('job_no')]]['gauge']= $row[csf('gauge')];
					$job_array_new[$row[csf('job_no')]]['job_quantity']= $row[csf('job_quantity')];
					$job_array_new[$row[csf('job_no')]]['yarn_quality']= $row[csf('yarn_quality')];
				 }
				//  var_dump($job_array_new);
			    ?>
				<?
				foreach($data_array as $job_no=>$job_data)
				{
					?>
					<br>
					<table width="1100" style="margin-left:10px;" class="rpt_table" cellpadding="0" cellspacing="0" border="2" rules="all" id="table_header_1">
						<tr bgcolor="<? echo $style;?>">
							<td><b> Buyer : </b></td> <td><b><? echo $buyer_arr[$job_array_new[$job_no]['buyer_name']];?></b></td>
								<td><b> Style Ref. : </b></td> <td><b><? echo $job_array_new[$job_no]['style_ref_no'];?></b></td>
							<td><b>Gauge : </b></td> <td><b><? echo $gauge_arr[$job_array_new[$job_no]['gauge']];?></b></td>
							<td><b>Job No : </b></td> <td><b><? echo $job_array_new[$job_no]['job_no'];?></b></td>
							<td><b>Order Qty : </b></td> <td><b><? echo $job_array_new[$job_no]['job_quantity'];?></b></td>
						</tr>
						<tr bgcolor="<?// echo $style1;?>">
								<td><b>Yarn Quality : </b></td> <td colspan="9"><p><b><?php echo $job_array_new[$job_no]['yarn_quality'];?></b></td>
						</tr>
				    </table>
					<br>
					<table id="table_header_1" style="margin-left:10px" class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1"  rules="all">
						<caption> <b style="float:left">Color Details</b></caption>
						<thead>
							<tr>
								<th  width="150">Garments Color</th>
								<th  width="80">Combo Color</th>
								<th  width="80">Yarn Lot Ration</th>
								<th  width="80">Number of Bundle</th>
								<th  width="80">Yarn Lot Name</th>
								<th  width="80">SIZE</th>
								<!-- <th  colspan="<? echo count($size_number_arr);?>">Size</th>
									-->
								<?
									foreach($size_number_arr as $size_key=>$val)
									{
									?>
									<th width=""><? echo $itemSizeArr[$size_key];?></th>
									<?
									}
								?>

								<th width="100">Qty(PCS)</th>
								<th width="100" align="center">Excess/Short Knit %</th>
							</tr>
						</thead>
							<?
							$k=1;
							// foreach($data_array as $job_no=>$job_data) 
							// {				
								foreach($job_data as $color_id=>$color_value) 
								{ 
									$color=0;
									$ex_sh=0;
									$combo_color=0;
									$size=0;
									    ?>
									        <tr bgcolor="#F4F3C4">
											    <td colspan="5"></td>
												<td align="left"><b>Order Qty : </b> </td>
													<?
													foreach($size_number_arr as $size_key=>$val)
													{
														$order_size_qty=$order_qnty_array[$job_no][$color_id][$size_key];
														$order_color_qty[$color_id]+=$order_size_qty;
														$total_color_wise_order_qty=$order_color_qty[$color_id];
														$order_size_qty_arr[$size_key]+=$order_size_qty;
														?>
														<td align="right"><? echo number_format($order_size_qty,0); ?></td>
														<?
													}
													?>
												<td width="100" align="right"><? echo number_format($total_color_wise_order_qty,0); ?></td>
												<td></td>
											</tr>
									    <?
									$color_wise_total_array=array();
									// $$color_size_wise_total_array=array();
									foreach($color_value as $cutting=>$row) 
									{ 
										if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												
										?>
											<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
												<?
												if($color==0){
													?>
														<td width="150" valign="middle" rowspan="<? echo $color_row_span[$job_no][$color_id]; ?>" align="center"><b><? echo $color_library[$color_id]; ?></b></td>
													<? 
													$color++;
												} 
												?>
												<?
												if($combo_color==0){
												    ?>
														<td width="120" rowspan="<? echo $combo_color_rowspan[$job_no][$color_id]; ?>" valign="middle" align="center">
															<b>
															<?
															$color_stripe=rtrim($color_stripe_arr[$job_no][$color_id],',');
															//echo $color_stripe.'x';
															$color_stripes=array_unique(explode(",",$color_stripe));
															$po_color_stripe_arr[$color_stripe]=$color_stripe;
															//   echo "<pre>";
															//   print_r($po_color_stripe_arr);
															if($color_stripe!="")
																{
																	foreach($color_stripes as $scolor)
																		{
																				// echo "<pre>";
																				// print_r($scolor);
															?>
																			<? echo $color_library[$scolor]; ?> 
																			<? echo $color_library[$color_sample_arr[$job_no][$color_id][$scolor]]; ?> <br>
															<?
																			$k++;
																		}
															
																}
															?>
															
															</b>
														</td>
													<?
											       $combo_color++;	
												}
												?>
												<td width="80" align="center"><b> <? echo $row['cut_num_prefix_no']; ?> </b></td>
												<td width="80" align="center"><b> <? echo $bundle_sql_data_array[$job_no][$cutting]['total_bundle'];?> </b></td>
												<td width="80"><b> <? echo $lot_data_array[$job_no][$cutting]['lot']; ?></b></td>
												<?
												if($size==0){
													?>
														<td width="80" valign="middle" rowspan="<? echo $size_rowspan[$job_no][$color_id];?>" align="center">
															<b> Knitted Qty </b>
														</td>
													<?
												   $size++;
												}
												?>
												<?
												// echo "<pre>";
												// print_r($size_number_arr);
												foreach($size_number_arr as $size_key=>$val)
										        {
													$size_qty=$knitting_qty_array[$job_no][$color_id][$cutting][$size_key]['knitting_qnty'];
													$color_qty[$color_id]+=$size_qty;
													$total_size_qty[$cutting][$color_id]+=$size_qty;
													$size_qty_arr[$size_key]+=$size_qty;
													$size_qty_arr[$color_id][$size_key]+=$size_qty;
													?>
													<td align="right"><? echo number_format($size_qty,0); ?></td>
													<?
													$color_wise_total_array[$size_key]+=$size_qty;
												}
												?>
												<td width="100"  align="right"><? echo number_format($total_size_qty[$cutting][$color_id],0); ?></td>
												<?
													if($ex_sh==0){
														?>
															<td width="100" valign="middle" rowspan="<? echo $ex_Sh_rowspan[$job_no][$color_id]; ?>"  align="center">
																<?
																$excess_short_knit = (($knitting_qty_tot_array[$job_no][$color_id]-$total_color_wise_order_qty)/$total_color_wise_order_qty)*100;
                                                                echo number_format($excess_short_knit,2); ?> %
															</td>
													<? 
														$ex_sh++;
													} 
												?>		
											</tr>
									    <? 
									}
									 ?>
									        <tr bgcolor="#CCCCCC">
												<td align="right" colspan="6"><b>Color Total : </b> </td>
												<?
												$tot_color_qty =0;
												foreach($size_number_arr as $size_key=>$val)
												{
													?>
													   <td align="right"><? echo number_format($color_wise_total_array[$size_key],0); ?></td>
													<?
													$tot_color_qty+=$color_wise_total_array[$size_key];
												}
												?>
												<td   width="100" align="right"><? echo number_format($tot_color_qty,0); ?></td>
												<td></td>
											</tr>
									 <?
								} 
							// }
							?>
							                <tr>
												<td align="right" colspan="6"><b>Total PO Qty :  </b> </td>
													<?
													foreach($size_number_arr as $size_key=>$val)
													{
														?>
														<td align="right"><? echo number_format($order_qnty_size_wise_tot_array[$job_no][$size_key],0); ?></td>
														<?
													}
				                                      ?>
												<td width="100" align="right"><? echo number_format($order_qnty_tot_array[$job_no],0); ?></td>
												<td></td>
							                </tr>	
											<tr>
												<td align="right" colspan="6"><b>Total Kintted Qty :   </b> </td>
													<?
													foreach($size_number_arr as $size_key=>$val)
													{
														?>
														<td align="right"><? echo number_format($knitting_qty_size_wise_tot_array[$job_no][$size_key],0); ?></td>
														<?
													}
				                                      ?>
												<td width="100" align="right"><? echo number_format($knitting_qty_job_wise_tot_array[$job_no],0); ?></td>
												<td></td>
							                </tr>	
				    </table>
				    <?
                   //  var_dump($job_id_new);
				}
				?>
					<br/>
				<?
						echo signature_table(163, $cbo_company_name, "1100px");
				?>
				<div id="page_break_div">
				
				</div>
			
		</div> <!--Main Div End-->
		<?
	}
	//Show Button end	
	if($reporttype==2) //Lot Ratio Dtls Button
	{
		// ========================================= MAIN QUERY =======================================================================================    
			$sql="SELECT a.job_no_prefix_num     AS job_prefix,
			a.job_no,
			a.buyer_name,
			a.gauge, 
			a.style_ref_no,
			a.job_quantity,
			c.color_number_id,
			c.size_number_id,
			c.order_quantity,
			a.yarn_quality,
			d.id,
			d.cutting_no,
			d.cut_num_prefix_no,
			d.entry_date,
            d.size_set_no,
			f.lot
	   FROM wo_po_details_master      a,
			wo_po_break_down          b,
			wo_po_color_size_breakdown c,
			ppl_cut_lay_mst  d,
			ppl_cut_lay_dtls e,
			ppl_cut_lay_prod_dtls f
	  WHERE     a.id = b.job_id
			AND b.id = c.po_break_down_id
			AND a.id = c.job_id
			AND a.job_no = d.job_no
			and d.id=e.mst_id
			and c.color_number_id=e.color_id
			and d.id=f.mst_id
			and e.id=f.dtls_id
			AND a.status_active = 1
			AND a.is_deleted = 0
			AND b.status_active = 1
			AND b.is_deleted = 0
			AND c.status_active = 1
			AND c.is_deleted = 0
			AND d.status_active = 1
			AND d.is_deleted = 0
			 $company_name_cond $date_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $job_cond $year_cond 
			order  by c.size_order,c.color_order,d.cut_num_prefix_no DESC";

			// echo $sql;
		
		$sql_po_result=sql_select($sql);
        $job_array=array();
		$data_array=array();
		foreach($sql_po_result as $value)
		{
			$data_array[$value[csf('job_no')]][$value[csf('color_number_id')]][$value[csf('cutting_no')]]['cut_num_prefix_no']=$value[csf('cut_num_prefix_no')];
			$data_array[$value[csf('job_no')]][$value[csf('color_number_id')]][$value[csf('cutting_no')]]['lot_date']=$value[csf('entry_date')];
			$data_array[$value[csf('job_no')]][$value[csf('color_number_id')]][$value[csf('cutting_no')]]['size_set_no']=$value[csf('size_set_no')];
			$job_array[$value[csf('job_no')]]['job_quantity']= $value[csf('job_quantity')];
		}
		// $all_job = "'".implode("','",$job_array)."'";
		// echo"<pre>";
		// print_r($job_array);die;

		$all_job="";$all_full_job="";$all_jobs_full="";$all_style="";$all_gauge=""; $all_buyer=""; $all_style_desc=""; 
		//echo $buyer_name;die;
		
		foreach($sql_po_result as $row)
		{
			// echo "<pre>";
			// print_r($row);
			if($all_jobs_full=="") $all_jobs_full=$row[csf("job_no")]; else $all_jobs_full.=",".$row[csf("job_no")];
			if($all_buyer=="") $all_buyer=$buyer_arr[$row[csf("buyer_name")]]; else $all_buyer.=",".$buyer_arr[$row[csf("buyer_name")]];
			if($all_full_job=="") $all_full_job="'".$row[csf('job_no')]."'"; else $all_full_job.=","."'".$row[csf('job_no')]."'";
			$size_number_arr[$row[csf("size_number_id")]]=$row[csf('size_number_id')];
			// $size_number_qty_arr[$row[csf("color_number_id")]][$row[csf("size_number_id")]]+=$row[csf('order_quantity')];
			$color_size_arr[$row[csf("color_number_id")]]=$row[csf('color_number_id')];
		} 
		//print_r($po_qty_by_job);
		$all_job_no=array_unique(explode(",",$all_full_job));
		$all_job_nos=implode(",",array_unique(explode(",",$all_job)));
		$all_jobs_full=implode(",",array_unique(explode(",",$all_jobs_full)));
		
		$all_jobs="";$all_yarn_comm="";$all_pi_id="";$all_supplier="";
		foreach($all_job_no as $jno)
		{
				if($all_jobs=="") $all_jobs=$jno; else $all_jobs.=",".$jno;
		}
		//echo $all_jobs;
		
		// ========================================= Strip Color Query ======================================================
		$sql_strip=sql_select("select job_no, color_number_id,stripe_color,sample_color from wo_pre_stripe_color where job_no in($all_jobs) and status_active=1 order by color_number_id,sample_color");
		// echo "select color_number_id,stripe_color from wo_pre_stripe_color where job_no in($all_jobs) and status_active=1";
		foreach($sql_strip as $row)
		{
			if($row[csf("color_number_id")]>0)
			{
				$color_stripe_arr[$row[csf("job_no")]][$row[csf("color_number_id")]].=$row[csf("stripe_color")].',';
				$color_sample_arr[$row[csf("job_no")]][$row[csf("color_number_id")]][$row[csf("stripe_color")]]=$row[csf("sample_color")];
			}
		}
		// echo"<pre>";
		// print_r($color_sample_arr);
		//print_r($color_stripe_arr);
		// ========================================= Lot Name Query ==============================================================

		$lot_sql="SELECT a.color_id,a.lot,b.job_no,b.cutting_no FROM ppl_cut_lay_prod_dtls a, ppl_cut_lay_mst  b, ppl_cut_lay_dtls c WHERE  a.mst_id = b.id AND a.dtls_id = c.id AND b.id = c.mst_id AND b.job_no IN($all_jobs) AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0";
		// echo $lot_sql;
        $lot_sql_result=sql_select($lot_sql);
	    $lot_data_array= array();
		foreach($lot_sql_result as $res)
		{
			$lot_data_array[$res[csf('job_no')]][$res[csf('cutting_no')]]['lot'].=$res[csf('lot')].',';
		}
		// echo "<pre>";
		// print_r($lot_data_array);
		// ========================================= Total Bundle Qnty Query ======================================================

		$bundle_sql="SELECT b.job_no, COUNT(a.id) as total_bundle, b.cutting_no FROM ppl_cut_lay_bundle a, ppl_cut_lay_mst b, ppl_cut_lay_dtls c WHERE a.mst_id = b.id AND a.dtls_id = c.id AND b.id = c.mst_id AND b.job_no IN ($all_jobs) AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 GROUP BY 	b.job_no,
		b.cutting_no";
        $bundle_sql_result=sql_select($bundle_sql);	
		$bundle_sql_data_array= array();
		foreach($bundle_sql_result as $res)
		{
			$bundle_sql_data_array[$res[csf('job_no')]][$res[csf('cutting_no')]]['total_bundle']+=$res[csf('total_bundle')];
		}
		// echo "<pre>";
		// print_r($bundle_sql_data_array);
		// ========================================= Total Ratio Qnty Query ======================================================

		$ratio_qty_sql="SELECT b.job_no, COUNT (a.id) AS total_bundle, b.cutting_no, d.color_id,d.size_id,d.marker_qty FROM ppl_cut_lay_bundle a, ppl_cut_lay_mst b, ppl_cut_lay_dtls c, ppl_cut_lay_size_dtls d WHERE a.mst_id = b.id AND a.dtls_id = c.id AND b.id = c.mst_id AND d.mst_id = b.id AND d.dtls_id = c.id AND a.size_id = d.size_id AND b.job_no IN ($all_jobs) AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 GROUP BY b.job_no, b.cutting_no, d.color_id,d.size_id,d.marker_qty";
        $ratio_qty_sql_result=sql_select($ratio_qty_sql);	
		$ratio_qty_data_array= array();
		$ratio_qty_tot_data_array=array();
		$ratio_qty_ratio_wise_tot_data_array=array();
		$ratio_qty_job_wise_tot_data_array=array();
		foreach($ratio_qty_sql_result as $res)
		{
			$ratio_qty_data_array[$res[csf('job_no')]][$res[csf('color_id')]][$res[csf('cutting_no')]][$res[csf('size_id')]]['ratio_qty']+=$res[csf('marker_qty')];
			$ratio_qty_tot_data_array[$res[csf('job_no')]][$res[csf('color_id')]]+=$res[csf('marker_qty')];
			$ratio_qty_ratio_wise_tot_data_array[$res[csf('job_no')]][$res[csf('size_id')]]+=$res[csf('marker_qty')];
			$ratio_qty_job_wise_tot_data_array[$res[csf('job_no')]]+=$res[csf('marker_qty')];
		}
		// echo "<pre>";
		// print_r($ratio_qty_job_wise_tot_data_array);
        // ========================================= Knitting Qnty Query ==========================================================
		
		$knitting_qty_sql="SELECT f.job_no, d.size_number_id, d.color_number_id, c.cut_no, SUM (c.production_qnty) AS production_qnty FROM pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown  d,  wo_po_break_down e, wo_po_details_master f WHERE a.id = c.mst_id AND c.color_size_break_down_id = d.id AND d.po_break_down_id = e.id AND a.po_break_down_id = e.id AND e.job_no_mst = f.job_no AND a.production_type = 77 AND f.job_no IN ($all_jobs) AND c.status_active = 1 AND c.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND e.status_active = 1 AND e.is_deleted = 0 AND f.status_active = 1 AND f.is_deleted = 0 GROUP BY  f.job_no, d.size_number_id, d.color_number_id, c.cut_no ORDER BY f.job_no ASC";
		$knitting_qty_sql_result=sql_select($knitting_qty_sql);
		$knitting_qty_array=array();
		$knitting_qty_tot_array=array();
		foreach($knitting_qty_sql_result as $res)
		{
            $knitting_qty_array[$res[csf('job_no')]][$res[csf('color_number_id')]][$res[csf('cut_no')]][$res[csf('size_number_id')]]['knitting_qnty']=$res[csf('production_qnty')];
            $knitting_qty_tot_array[$res[csf('job_no')]][$res[csf('color_number_id')]]+=$res[csf('production_qnty')];
		}
        // echo "<pre>";
		// print_r($knitting_qty_tot_array);
		// ========================================= Order Qnty Query ============================================================
		$order_qnty_sql="SELECT a.job_no_mst,a.color_number_id,a.size_number_id,a.order_quantity FROM wo_po_color_size_breakdown a WHERE a.job_no_mst IN ($all_jobs) AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.size_order";

		$order_qnty_sql_result=sql_select($order_qnty_sql);
		$order_qnty_array=array();
		$order_qnty_size_wise_tot_array=array();
		$order_qnty_tot_array=array();
		foreach($order_qnty_sql_result as $res)
		{
			$order_qnty_array[$res[csf('job_no_mst')]][$res[csf('color_number_id')]][$res[csf('size_number_id')]]+=$res[csf('order_quantity')];
			$order_qnty_size_wise_tot_array[$res[csf('job_no_mst')]][$res[csf('size_number_id')]]+=$res[csf('order_quantity')];
			$order_qnty_tot_array[$res[csf('job_no_mst')]]+=$res[csf('order_quantity')];
		}
		// echo "<pre>";
		// print_r($order_qnty_array);

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
				</style>
				<table width="1230px" style="margin-left:10px">
				
					<tr>
						<td align="center" colspan="8" class="form_caption"><strong style=" font-size:16px"><? echo $company_library[$cbo_company_name].'<br>';if($cbo_style_owner!=0) echo 'Style Owner: '.$company_library[$cbo_style_owner]; ?></strong></td>
					</tr>
						<tr class="form_caption">
						<td colspan="8" align="center"><strong style=" font-size:18px">Yarn Lot Ration Break Down Report</strong></td>
					</tr>
				</table>
					<br/>
				<?
				$width="1230"; 
				$color_row_span=array();
				$combo_color_rowspan=array();
				$size_rowspan=array();
				$ex_Sh_rowspan=array();
				foreach($data_array as $job_no=>$job_data) 
				{				
					foreach($job_data as $color_id=>$color_value) 
					{ 
						
						foreach($color_value as $cutting=>$row) 
						{ 
							$color_row_span[$job_no][$color_id]++;
							$ex_Sh_rowspan[$job_no][$color_id]++;
							$combo_color_rowspan[$job_no][$color_id]++;
							$size_rowspan[$job_no][$color_id]++;
						}
					}
				}
				// echo"<pre>";
				// print_r($ex_Sh_rowspan);
				?>
				<?
				$job_array_new=array();
				foreach($sql_po_result as $row)
		         {
					$job_array_new[$row[csf('job_no')]]['job_no']= $row[csf('job_no')];
					$job_array_new[$row[csf('job_no')]]['buyer_name']= $row[csf('buyer_name')];
					$job_array_new[$row[csf('job_no')]]['style_ref_no']= $row[csf('style_ref_no')];
					$job_array_new[$row[csf('job_no')]]['gauge']= $row[csf('gauge')];
					$job_array_new[$row[csf('job_no')]]['job_quantity']= $row[csf('job_quantity')];
					$job_array_new[$row[csf('job_no')]]['yarn_quality']= $row[csf('yarn_quality')];
				 }
				//  var_dump($job_array_new);
			    ?>
				<?
				foreach($data_array as $job_no=>$job_data)
				{
					?>
					<br>
					<table width="1100" style="margin-left:10px;" class="rpt_table" cellpadding="0" cellspacing="0" border="2" rules="all" id="table_header_1">
						<tr bgcolor="<? echo $style;?>">
							<td><b> Buyer : </b></td> <td><b><? echo $buyer_arr[$job_array_new[$job_no]['buyer_name']];?></b></td>
								<td><b> Style Ref. : </b></td> <td><b><? echo $job_array_new[$job_no]['style_ref_no'];?></b></td>
							<td><b>Gauge : </b></td> <td><b><? echo $gauge_arr[$job_array_new[$job_no]['gauge']];?></b></td>
							<td><b>Job No : </b></td> <td><b><? echo $job_array_new[$job_no]['job_no'];?></b></td>
							<td><b>Order Qty : </b></td> <td><b><? echo $job_array_new[$job_no]['job_quantity'];?></b></td>
						</tr>
						<tr bgcolor="<?// echo $style1;?>">
								<td><b>Yarn Quality : </b></td> <td colspan="9"><p><b><?php echo $job_array_new[$job_no]['yarn_quality'];?></b></td>
						</tr>
				    </table>
					<br>
					<table id="table_header_1" style="margin-left:10px" class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
							<caption> <b style="float:left">Color Details</b></caption>
							<thead>
								<tr>
									<th  width="150">Garments Color</th>
									<th  width="80">Combo Color</th>
									<th  width="80">Yarn Lot Ration No</th>
									<th  width="80">Yarn Lot Ration Date</th>
									<th  width="80">Size Set No</th>
									<th  width="80">Yarn Lot Name</th>
									<th  width="80">SIZE</th>
									<!-- <th  colspan="<? echo count($size_number_arr);?>">Size</th>
										-->
									<?
										foreach($size_number_arr as $size_key=>$val)
										{
										?>
										<th width=""><? echo $itemSizeArr[$size_key];?></th>
										<?
										}
									?>

									<th width="100">Qty(PCS)</th>
									<th width="100" align="center">Excess/Short Knit %</th>
								</tr>
							</thead>
								<?
								$k=1;
								// foreach($data_array as $job_no=>$job_data) 
								// {				
									foreach($job_data as $color_id=>$color_value) 
									{ 
										$color=0;
										$ex_sh=0;
										$combo_color=0;
										$size=0;
											?>
												<tr bgcolor="#F4F3C4">
													<td colspan="6"></td>
													<td align="left"><b>Order Qty : </b> </td>
														<?
														foreach($size_number_arr as $size_key=>$val)
														{
															$order_size_qty=$order_qnty_array[$job_no][$color_id][$size_key];
															$order_color_qty[$color_id]+=$order_size_qty;
															$total_color_wise_order_qty=$order_color_qty[$color_id];
															$order_size_qty_arr[$size_key]+=$order_size_qty;
															?>
															<td align="right"><? echo number_format($order_size_qty,0); ?></td>
															<?
														}
														?>
													<td width="100" align="right"><? echo number_format($total_color_wise_order_qty,0); ?></td>
													<td></td>
												</tr>
											<?
										$color_wise_total_array=array();
										// $$color_size_wise_total_array=array();
										foreach($color_value as $cutting=>$row) 
										{ 
											if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													
											?>
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
													<?
													if($color==0){
														?>
															<td width="150" valign="middle" rowspan="<? echo $color_row_span[$job_no][$color_id]; ?>" align="center"><b><? echo $color_library[$color_id]; ?></b></td>
														<? 
														$color++;
													} 
													?>
													<?
													if($combo_color==0){
														?>
															<td width="120" rowspan="<? echo $combo_color_rowspan[$job_no][$color_id]; ?>" valign="middle" align="center">
																<b>
																<?
																$color_stripe=rtrim($color_stripe_arr[$job_no][$color_id],',');
																//echo $color_stripe.'x';
																$color_stripes=array_unique(explode(",",$color_stripe));
																$po_color_stripe_arr[$color_stripe]=$color_stripe;
																//   echo "<pre>";
																//   print_r($po_color_stripe_arr);
																if($color_stripe!="")
																	{
																		foreach($color_stripes as $scolor)
																			{
																					// echo "<pre>";
																					// print_r($scolor);
																?>
																				<? echo $color_library[$scolor]; ?> 
																				<? echo $color_library[$color_sample_arr[$job_no][$color_id][$scolor]]; ?> <br>
																<?
																				$k++;
																			}
																
																	}
																?>
																
																</b>
															</td>
														<?
													$combo_color++;	
													}
													?>
													<td width="80" align="center"><b> <? echo $row['cut_num_prefix_no']; ?> </b></td>
													<td width="80" align="center"><b> <? echo $row['lot_date'];?> </b></td>
													<td width="130" align="center"><b> <? echo $row['size_set_no'];?> </b></td>
													<td width="80"><b> <? echo $lot_data_array[$job_no][$cutting]['lot']; ?></b></td>
													<?
													if($size==0){
														?>
															<td width="80" valign="middle" rowspan="<? echo $size_rowspan[$job_no][$color_id];?>" align="center">
																<b> Ratio Qty </b>
															</td>
														<?
													$size++;
													}
													?>
													<?
													// echo "<pre>";
													// print_r($size_number_arr);
													foreach($size_number_arr as $size_key=>$val)
													{
														// $size_qty=$knitting_qty_array[$job_no][$color_id][$cutting][$size_key]['knitting_qnty'];
														$size_qty=$ratio_qty_data_array[$job_no][$color_id][$cutting][$size_key]['ratio_qty'];
														$color_qty[$color_id]+=$size_qty;
														$total_size_qty[$cutting][$color_id]+=$size_qty;
														$size_qty_arr[$size_key]+=$size_qty;
														$size_qty_arr[$color_id][$size_key]+=$size_qty;
														?>
														<td align="right"><? echo number_format($size_qty,0); ?></td>
														<?
														$color_wise_total_array[$size_key]+=$size_qty;
													}
													?>
													<td width="100"  align="right"><? echo number_format($total_size_qty[$cutting][$color_id],0); ?></td>
													<?
														if($ex_sh==0){
															?>
																<td width="100" valign="middle" rowspan="<? echo $ex_Sh_rowspan[$job_no][$color_id]; ?>"  align="center">
																	<?
																	$excess_short_knit = (($ratio_qty_tot_data_array[$job_no][$color_id]-$total_color_wise_order_qty)/$total_color_wise_order_qty)*100;
																	echo number_format($excess_short_knit,2); ?> %
																</td>
														<? 
															$ex_sh++;
														} 
													?>		
												</tr>
											<? 
										}
										?>
												<tr bgcolor="#CCCCCC">
													<td align="right" colspan="7"><b>Color Total : </b> </td>
													<?
													$tot_color_qty =0;
													foreach($size_number_arr as $size_key=>$val)
													{
														?>
														<td align="right"><? echo number_format($color_wise_total_array[$size_key],0); ?></td>
														<?
														$tot_color_qty+=$color_wise_total_array[$size_key];
													}
													?>
													<td   width="100" align="right"><? echo number_format($tot_color_qty,0); ?></td>
													<td></td>
												</tr>
												<tr>
													<td align="right" colspan="7"><b>Balance : </b> </td>
														<?
														foreach($size_number_arr as $size_key=>$val)
														{
															?>
															<td align="right"> <? $balance=$order_qnty_array[$job_no][$color_id][$size_key]-$color_wise_total_array[$size_key]; 

															if($balance>0){
																?>
																<span style="color:red"><? echo $balance; ?></span>
																<?
															}else{
																echo $balance; 
															}
															?> </td>
															<?
														}
														?>
													<td   width="100" align="right"><? $tot_balance_qty=$total_color_wise_order_qty-$ratio_qty_tot_data_array[$job_no][$color_id]; 
															if($tot_balance_qty>0){
																?>
																<span style="color:red"><? echo number_format($tot_balance_qty,0); ?></span>
																<?
															}else{
																echo number_format($tot_balance_qty,0);
															}
													
													?></td>
													<td></td>
												</tr>
										<?
									} 
								// }
								?>
												<tr>
													<td align="right" colspan="7"><b>Total PO Qty :  </b> </td>
														<?
														foreach($size_number_arr as $size_key=>$val)
														{
															?>
															<td align="right"><? echo number_format($order_qnty_size_wise_tot_array[$job_no][$size_key],0); ?></td>
															<?
														}
														?>
													<td width="100" align="right"><? echo number_format($order_qnty_tot_array[$job_no],0); ?></td>
													<td></td>
												</tr>	
												<tr>
													<td align="right" colspan="7"><b>Total Ratio Qty :   </b> </td>
														<?
														foreach($size_number_arr as $size_key=>$val)
														{
															// $tot_knitting_size_qty+=$size_qty_arr[$size_key];
															?>
															<td align="right"><? echo number_format($ratio_qty_ratio_wise_tot_data_array[$job_no][$size_key],0); ?></td>
															<?
														}
														?>
													<td width="100" align="right"><? echo number_format($ratio_qty_job_wise_tot_data_array[$job_no],0); ?></td>
													<td></td>
												</tr>		
					</table>
				  <?
                   //  var_dump($job_id_new);
				}
				?>
					<br/>
			
				<?
						echo signature_table(163, $cbo_company_name, "1230px");
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
