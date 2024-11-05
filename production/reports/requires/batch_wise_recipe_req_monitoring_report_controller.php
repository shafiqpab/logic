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
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}

/*$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$order_library=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
if($db_type==0) $field_concat="concat(machine_no,'-',brand) as machine_name"; 
else if($db_type==2) $field_concat="machine_no || '-' || brand as machine_name";
$machine_arr=return_library_array( "select id,$field_concat from  lib_machine_name",'id','machine_name');
$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );*/

//--------------------------------------------------------------------------------------------------------------------
if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $cbo_year_id.'aziz';
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
                        <th width="140">Buyer</th>
                        <th width="130">Search By</th>
                        <th width="110" id="search_by_td_up">Please Enter Job No</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                        <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td><? echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 ); ?></td>                 
                            <td>	
                                <?
                                    $search_by_arr=array(1=>"Job No",2=>"Style Ref");
                                    $dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
                                    echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                                ?>
                            </td>     
                            <td id="search_by_td"><input type="text" style="width:100px" class="text_boxes" name="txt_search_common" id="txt_search_common" /></td> 	
                            <td><input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_batch_type; ?>', 'create_job_no_search_list_view', 'search_div', 'batch_wise_recipe_req_monitoring_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" /></td>
                        </tr>
                    </tbody>
                </table>
                <div style="margin-top:15px" id="search_div"></div>
            </fieldset>
        </form>
    </div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$batch_type=$data[5];
	//echo $month_id;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($batch_type==2)
	{
		if($data[1]==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and party_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond=" and party_id=$data[1]";
		
		
		if($data[3]!="") { if($search_by==1) $search_field="job_no_prefix_num=$data[3]"; } $search_field="";
		//$year="year(insert_date)";
		if($db_type==0)
		{
			if($year_id!=0) $year_cond=" and year(insert_date)=$year_id"; else $year_cond="";	
			$year_field="YEAR(insert_date) as year"; 
		}
		else if($db_type==2)
		{
			$year_field_con=" and to_char(insert_date,'YYYY')";
			if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
			$year_field="to_char(insert_date,'YYYY') as year";
		}
		//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
		$arr=array (0=>$company_arr,1=>$buyer_arr,4=>$currency);
		$sql= "select id, subcon_job, job_no_prefix_num, company_id, party_id, currency_id, $year_field from subcon_ord_mst where status_active=1 and is_deleted=0 and company_id=$company_id and entry_form=238 $search_field $buyer_id_cond $year_cond  order by id DESC";
		echo create_list_view("tbl_list_search", "Company,Party Name,Job No,Year,Currency", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_id,party_id,0,0,currency_id", $arr , "company_id,party_id,job_no_prefix_num,year,currency_id", "",'','0,0,0,0,0','') ;
	}
	else
	{
		if($data[1]==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond=" and buyer_name=$data[1]";
		
		$search_by=$data[2];
		$search_string="%".trim($data[3])."%";
		if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
		//$year="year(insert_date)";
		if($db_type==0)
		{
			if($year_id!=0) $year_cond=" and year(insert_date)=$year_id"; else $year_cond="";	
			$year_field="YEAR(insert_date) as year"; 
		}
		else if($db_type==2)
		{
			$year_field_con=" and to_char(insert_date,'YYYY')";
			if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
			$year_field="to_char(insert_date,'YYYY') as year";
		}
		//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
		$arr=array (0=>$company_arr,1=>$buyer_arr);
		$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by id DESC";
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
	}
	exit(); 
} // Job Search end

if ($action == "booking_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_booking_id").val(splitData[0]); 
			$("#hide_booking_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center">
			<form name="bookingform_1" id="bookingform_1">
				<fieldset style="width:740px;">
					<table width="740" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th width="140">Buyer</th>
							<th width="110">Please Enter Booking No</th>
							<th width="130" colspan="2">Booking Date</th>

							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('bookingform_1','search_div','','','','');"></th>
							<input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" />
							<input type="hidden" name="hide_booking_id" id="hide_booking_id" value="" />
						</thead>
						<tbody>
							<tr class="general">
								<td><? echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 ); ?></td>   
								<td><input type="text" style="width:100px" class="text_boxes" name="txt_booking_no" id="txt_booking_no" /></td> 
								<td><input type="text" style="width:55px" class="datepicker" name="txt_date_from" id="txt_date_from" placeholder="From Date"/></td>
								<td><input type="text" style="width:55px" class="datepicker" name="txt_date_to" id="txt_date_to" placeholder="To Date"/></td>
								<td><input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_booking_no_search_list_view', 'search_div', 'batch_wise_recipe_req_monitoring_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" /></td>
							</tr>
							<tr>
								<td colspan="5" align="center"><? echo load_month_buttons(1); ?></td>
							</tr>
						</tbody>
					</table>
					<div id="search_div"></div>
				</fieldset>
			</form>
		</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
    exit();  
}
 
if($action == "create_booking_no_search_list_view")
{
	$data=explode('**',$data);

	if ($data[0]!=0) $company="  a.company_id='$data[0]'"; 
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";
	if ($data[2]!=0) $booking_no=" and a.booking_no_prefix_num='$data[2]'"; else $booking_no='';
	if($db_type==0)
	{
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and s.booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	else if($db_type==2)
	{
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}

	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category,6=>$fabric_source,7=>$suplier,8=>$approved,9=>$is_ready);
	//pro_batch_create_mst//wo_non_ord_samp_booking_mst
 	 $sql= "(select a.id,a.booking_no_prefix_num as no_prefix_num,a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_booking_mst a,pro_batch_create_mst b where $company $buyer $booking_no $booking_date and a.booking_no=b.booking_no and a.booking_type=1 and a.is_short=2 and a.status_active=1 and a.is_deleted=0 
	union all
	 select a.id,a.booking_no_prefix_num as no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no,a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_non_ord_samp_booking_mst a ,pro_batch_create_mst b where $company $buyer $booking_no $booking_date  and a.booking_no=b.booking_no and a.booking_type=4 and b.status_active=1 and b.is_deleted=0) order by id Desc";
	//echo "select a.id,a.booking_no_prefix_num as no_prefix_num,a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_booking_mst a,wo_non_ord_samp_booking_mst b where $company $buyer $booking_no $booking_date and a.booking_no=b.booking_no and a.booking_type=1 and a.is_short=2 and a.status_active=1 and a.is_deleted=0 "; 
	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "110,80,80,80,90,120,80,80,60,50","910","320",0, $sql , "js_set_value", "id,no_prefix_num", "", 1, "0,0,company_id,buyer_id,0,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no,booking_date,company_id,buyer_id,job_no,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','setFilterGrid(\'list_view\',-1);','0,3,0,0,0,0,0,0,0,0','','');
	exit(); 
}
// Booking Search end

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
                        <th width="140">Buyer</th>
                        <th>Search By</th>
                        <th id="search_by_td_up" width="110">Please Enter Order No</th>
                        <th width="130" colspan="2">Shipment Date</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                        <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                        <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td><? echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 ); ?></td>                 
                            <td>	
                                <?
                                    $search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
                                    $dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
                                    echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                                ?>
                            </td>     
                            <td id="search_by_td"><input type="text" style="width:100px" class="text_boxes" name="txt_search_common" id="txt_search_common" /></td> 
                            <td><input type="text" style="width:55px" class="datepicker" name="txt_date_from" id="txt_date_from" placeholder="From Date"/></td>
                            <td><input type="text" style="width:55px" class="datepicker" name="txt_date_to" id="txt_date_to" placeholder="To Date"/></td>	
                            <td><input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_batch_type; ?>', 'create_order_no_search_list_view', 'search_div', 'batch_wise_recipe_req_monitoring_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="6" valign="center"><? echo load_month_buttons(1); ?></td>
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
	$year_id=$data[6];
	$batch_type=$data[7];
	
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	if($batch_type==2)
	{
		if($data[1]==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.party_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond=" and a.party_id=$data[1]";
		
		$search_by=$data[2];
		$search_string="%".trim($data[3])."%";
		if($search_by==1) $search_field="b.order_no"; else if($search_by==2) $search_field="b.cust_style_ref"; else $search_field="a.job_no";
		
		$start_date =trim($data[4]);
		$end_date =trim($data[5]);	
		if($start_date!="" && $end_date!="")
		{
			if($db_type==0)
			{
				$date_cond="and b.delivery_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
			}
			else if($db_type==2)
			{
				$date_cond="and b.delivery_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";	
			}
		}
		else $date_cond="";
		
		if($db_type==0)
		{
			if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";	
			$year_field="YEAR(a.insert_date)"; 
		}
		else if($db_type==2)
		{
			if($year_id!=0) $year_cond="and to_char(a.insert_date,'YYYY')=$year_id"; else $year_cond="";	
			$year_field="to_char(a.insert_date,'YYYY')";
		}
		
		$arr=array(0=>$company_arr,1=>$buyer_arr);
		//$sql= "select id, subcon_job, job_no_prefix_num, company_id, party_id, currency_id, $year_field from subcon_ord_mst where status_active=1 and is_deleted=0 and company_id=$company_id $search_field $buyer_id_cond $year_cond  order by id DESC";
		
		$sql= "select b.id, $year_field as year, a.subcon_job, a.job_no_prefix_num, a.company_id, a.party_id, b.cust_style_ref as style_ref_no, b.order_no, b.delivery_date from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=238 and a.company_id=$company_id and $search_field like '$search_string' $year_cond $buyer_id_cond $date_cond order by b.id DESC";
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Cust. Style, Po No, Delivery Date", "130,120,50,60,120,110","760","220",0, $sql , "js_set_value", "id,order_no", "", 1, "company_id,party_id,0,0,0,0,0", $arr , "company_id,party_id,year,job_no_prefix_num,style_ref_no,order_no,delivery_date", "",'','0,0,0,0,0,0,3','',1) ;
	}
	else
	{
		if($data[1]==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond=" and a.buyer_name=$data[1]";
		
		$search_by=$data[2];
		$search_string="%".trim($data[3])."%";
		if($search_by==1) $search_field="b.po_number"; else if($search_by==2) $search_field="a.style_ref_no"; else $search_field="a.job_no";
		
		$start_date =trim($data[4]);
		$end_date =trim($data[5]);	
		if($start_date!="" && $end_date!="")
		{
			if($db_type==0)
			{
				$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
			}
			else if($db_type==2)
			{
				$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";	
			}
		}
		else $date_cond="";
		
		if($db_type==0)
		{
			if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";	
			$year_field="YEAR(a.insert_date) as year"; 
		}
		else if($db_type==2)
		{
			if($year_id!=0) $year_cond="and to_char(a.insert_date,'YYYY')=$year_id"; else $year_cond="";	
			$year_field="to_char(a.insert_date,'YYYY') as year";
		}
		
		$arr=array(0=>$company_arr,1=>$buyer_arr);
		$sql= "select b.id, $year_field as year, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $year_cond $buyer_id_cond $date_cond order by b.id DESC";
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,130,50,60,130,130","760","220",0, $sql , "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr , "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date", "",'','0,0,0,0,0,0,3','',1) ;
	}
   exit(); 
}//Order Search End

if($action=="batch_no_search_popup")
{
	echo load_html_head_contents("Batch No Info", "../../../", 1, 1,'','','');
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
			//alert(str);
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
            <fieldset style="width:760px;">
                <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table">
                    <thead>
                        <th width="130">Batch No </th>
                        <th width="150" colspan="2">Batch Date</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;">
                        <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                        <input type="hidden" name="hide_order_id" id="hide_order_id" value="" /></th> 
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td id="search_by_td"><input type="text" style="width:120px" class="text_boxes" name="txt_search_common" id="txt_search_common" /></td> 
                            <td><input type="text" style="width:55px" class="datepicker" name="txt_date_from" id="txt_date_from" placeholder="From Date"/></td>
                            <td><input type="text" style="width:55px" class="datepicker" name="txt_date_to" id="txt_date_to" placeholder="To Date"/></td>	
                            <td><input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_batch_no_search_list_view', 'search_div', 'batch_wise_recipe_req_monitoring_report_controller', 'setFilterGrid(\'tbl_list\',-1)');" style="width:100px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="4" valign="center"><? echo load_month_buttons(1); ?></td>
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

if($action=="create_batch_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	
	$batch_no=$data[1];
	$search_string="%".trim($data[3])."%";
	//if($batch_no=='') $search_field="b.po_number";  else  $search_field="b.po_number";
	if ($batch_no=="") $batch_no_cond=""; else $batch_no_cond=" and a.batch_no in ('$batch_no') "; 
	
	$start_date =trim($data[2]);
	$end_date =trim($data[3]);	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.batch_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
		}
		else if($db_type==2)
		{
			$date_cond="and a.batch_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";	
		}
	}
	else $date_cond="";
	//if($db_type==0) $year_field="YEAR(a.insert_date) as year,"; 
	//else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	
	$sql="select a.id, a.batch_no, a.batch_for, a.booking_no, a.color_id, a.batch_weight from pro_batch_create_mst a where a.company_id=$company_id and a.is_deleted=0 and a.status_active=1 $date_cond $batch_no_cond order by a.id DESC";	
$arr=array(1=>$color_library,3=>$batch_for);
	echo  create_list_view("tbl_list", "Batch no,Color,Booking no, Batch for,Batch weight ", "150,100,150,100,70","700","350",0, $sql, "js_set_value", "id,batch_no", "", 1, "0,color_id,0,batch_for,0", $arr , "batch_no,color_id,booking_no,batch_for,batch_weight", "",'','0','',1) ;
	exit();
}//Batch Search End

if($action=="recipe_popup")
{
	echo load_html_head_contents("Batch No Info", "../../../", 1, 1,'','','');
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
			//alert(str);
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
				name += selected_name[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_recipe_id').val( id );
			$('#hide_recipe_no').val( name );
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
            <fieldset style="width:760px;">
                <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table">
                    <thead>
                        <th width="130">Recipe No </th>
                        <th width="150" colspan="2">Recipe Date</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;">
                        <input type="hidden" name="hide_recipe_no" id="hide_recipe_no" value="" />
                        <input type="hidden" name="hide_recipe_id" id="hide_recipe_id" value="" /></th> 
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td id="search_by_td"><input type="text" style="width:120px" class="text_boxes" name="txt_search_common" id="txt_search_common" /></td> 
                            <td><input type="text" style="width:55px" class="datepicker" name="txt_date_from" id="txt_date_from" placeholder="From Date"/></td>
                            <td><input type="text" style="width:55px" class="datepicker" name="txt_date_to" id="txt_date_to" placeholder="To Date"/></td>	
                            <td><input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_recipe_list_view', 'search_div', 'batch_wise_recipe_req_monitoring_report_controller', 'setFilterGrid(\'tbl_list\',-1)');" style="width:100px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="4" valign="center"><? echo load_month_buttons(1); ?></td>
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

if($action=="create_recipe_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	
	$recipe_no=$data[1];
	$search_string="%".trim($data[3])."%";
	//if($batch_no=='') $search_field="b.po_number";  else  $search_field="b.po_number";
	if ($recipe_no=="") $recipe_cond=""; else $recipe_cond=" and a.id in ($recipe_no) "; 
	
	$start_date =trim($data[2]);
	$end_date =trim($data[3]);	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.recipe_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
		}
		else if($db_type==2)
		{
			$date_cond="and a.recipe_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";	
		}
	}
	else $date_cond="";
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$batchArr=return_library_array( "select id,batch_no from pro_batch_create_mst", "id", "batch_no");
	$bookingArr=return_library_array( "select booking_no_id,booking_no from pro_batch_create_mst", "booking_no_id", "booking_no");
	//if($db_type==0) $year_field="YEAR(a.insert_date) as year,"; 
	//else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	
	$arr=array(1=>$batchArr,2=>$batchArr,3=>$bookingArr);
	
	$sql="select a.id, a.recipe_no_prefix_num, a.batch_id, a.color_id, a.booking_id, a.recipe_date from pro_recipe_entry_mst a where a.company_id=$company_id and a.is_deleted=0 and a.status_active=1 $date_cond $recipe_cond order by a.id DESC";	

	echo  create_list_view("tbl_list", "Recipe No,Batch,Color,Booking No, Recipe Date", "70,100,150,110,70","700","240",0, $sql, "js_set_value", "id,recipe_no_prefix_num", "", 1, "0,batch_id,color_id,booking_id,0", $arr , "recipe_no_prefix_num,batch_id,color_id,booking_id,recipe_date", "",'','0,0,0,0,3','',1) ;
	exit();
}//Recipe Search End

if($action=="requisition_popup")
{
	echo load_html_head_contents("Batch No Info", "../../../", 1, 1,'','','');
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
			//alert(str);
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
				name += selected_name[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_req_id').val( id );
			$('#hide_req_no').val( name );
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
            <fieldset style="width:760px;">
                <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table">
                    <thead>
                        <th width="130">Req. No </th>
                        <th width="150" colspan="2">Req. Date</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;">
                        <input type="hidden" name="hide_req_no" id="hide_req_no" value="" />
                        <input type="hidden" name="hide_req_id" id="hide_req_id" value="" /></th> 
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td id="search_by_td"><input type="text" style="width:120px" class="text_boxes" name="txt_search_common" id="txt_search_common" /></td> 
                            <td><input type="text" style="width:55px" class="datepicker" name="txt_date_from" id="txt_date_from" placeholder="From Date"/></td>
                            <td><input type="text" style="width:55px" class="datepicker" name="txt_date_to" id="txt_date_to" placeholder="To Date"/></td>	
                            <td><input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_requisation_list_view', 'search_div', 'batch_wise_recipe_req_monitoring_report_controller', 'setFilterGrid(\'tbl_list\',-1)');" style="width:100px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="4" valign="center"><? echo load_month_buttons(1); ?></td>
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

if($action=="create_requisation_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	
	$req_no=$data[1];
	$search_string="%".trim($data[3])."%";
	//if($batch_no=='') $search_field="b.po_number";  else  $search_field="b.po_number";
	if ($req_no=="") $req_cond=""; else $req_cond=" and a.requ_prefix_num in ('$req_no') "; 
	
	$start_date =trim($data[2]);
	$end_date =trim($data[3]);	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.requisition_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
		}
		else if($db_type==2)
		{
			$date_cond="and a.requisition_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";	
		}
	}
	else $date_cond="";
	//if($db_type==0) $year_field="YEAR(a.insert_date) as year,"; 
	//else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$batchArr=return_library_array( "select id,batch_no from pro_batch_create_mst", "id", "batch_no");
	$recipeArr=return_library_array( "select id,recipe_no from pro_recipe_entry_mst", "id", "recipe_no");
	
	$arr=array(1=>$receive_basis_arr,2=>$batchArr,3=>$recipeArr);
	
	$sql="select a.id, a.requ_prefix_num, a.requisition_basis, a.batch_id, a.recipe_id, a.requisition_date from dyes_chem_issue_requ_mst a where a.company_id=$company_id and a.is_deleted=0 and a.status_active=1 $date_cond $req_cond order by a.id DESC";	

	echo  create_list_view("tbl_list", "Req. No,Req. Basis,Batch,Recipe,Req. Date", "70,120,150,110,70","700","240",0, $sql, "js_set_value", "id,requ_prefix_num", "", 1, "0,requisition_basis,batch_id,recipe_id,0", $arr , "requ_prefix_num,requisition_basis,batch_id,recipe_id,requisition_date", "",'','0,0,0,0,3','',1) ;
	exit();
}//Requisition Search End

$tmplte=explode("**",$data);
if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//'******************txt_date_from*txt_date_to',"../../")+'&report_title='+report_title; 
	$batch_type= str_replace("'","",$cbo_batch_type);
	$company_name= str_replace("'","",$cbo_company_name);
	$buyer_name= str_replace("'","",$cbo_buyer_name);
	$working_company= str_replace("'","",$cbo_working_company);
	$cbo_year= str_replace("'","",$cbo_year);
	$job_no=str_replace("'","",$txt_job_no);
	$job_id=str_replace("'","",$txt_job_id);
	$booking_no = str_replace("'","",$txt_booking_no);
	$booking_id = str_replace("'","",$txt_hide_booking_id);
	$order_no = str_replace("'","",$txt_order_no);
	$order_id = str_replace("'","",$hide_order_id);
	$batch_no=str_replace("'","",$txt_batch_no);
	$batch_id=str_replace("'","",$hide_batch_id);
	
	$recipe_no= str_replace("'","",$txt_recipe_no);
	$recipe_id= str_replace("'","",$hide_recipe_id);
	$req_no= str_replace("'","",$txt_req_no);
	$req_id= str_replace("'","",$hide_req_id);
	$search_date= str_replace("'","",$cbo_search_date);
	
	if($buyer_name==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$buyer_name";
	
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
		$batchDate_cond=""; $recipeDate_cond=""; $reqDate_cond="";
		if($search_date==1) $batchDate_cond=" and a.batch_date between '$start_date' and '$end_date'";
		else if($search_date==2) $recipeDate_cond=" and a.recipe_date between '$start_date' and '$end_date'";
		else if($search_date==3) $reqDate_cond=" and a.requisition_date between '$start_date' and '$end_date'";
		else if($search_date==4) $issueDate_cond=" and a.issue_date between '$start_date' and '$end_date'";
		else if($search_date==5) $dyeingDate_cond=" and a.process_end_date between '$start_date' and '$end_date'";
	}
	
	if($db_type==0)
	{
		//$year_field_by="and YEAR(a.insert_date)"; 
		$year_field="SUBSTRING_INDEX(a.insert_date, '-', 1) as year"; 
		if($cbo_year!=0) $year_cond=" and year(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		//$year_field_by=" and to_char(a.insert_date,'YYYY')";
		$year_field="to_char(a.insert_date,'YYYY') as year";
		if($cbo_year!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";	
	}
	//echo $year_cond;
	//if(trim($cbo_year)!=0) $year_cond=" $year_field_by=$cbo_year"; else $year_cond="";
	if ($working_company==0) $workingCompany_cond=""; else $workingCompany_cond=" and a.working_company_id=".$working_company." ";
	if ($company_name==0) $workingCompany_cond.=""; else $workingCompany_cond.=" and a.company_id=".$company_name." ";
	
	if ($company_name>0) $sub_conCompany_cond="and a.company_id=".$company_name." ";
	else if ($working_company>0) $sub_conCompany_cond="and a.company_id=".$working_company." ";
	else $sub_conCompany_cond="";
	
	//SubCon
	if($batch_type==2 || $batch_type==0)
	{
		if ($cbo_buyer_name==0) $sub_buyer_cond=""; else $sub_buyer_cond="and a.party_id='".$cbo_buyer_name."' ";
		if ($order_no!='') $suborder_no="and b.order_no='$order_no'"; else $suborder_no="";
		if ($job_no!='') $sub_job_cond="  and a.job_no_prefix_num='".$job_no."' "; else $sub_job_cond="";
	}
	
	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ('$job_no') ";
	
	$booking_no_cond="";
	if ($booking_id!=0) $booking_no_cond.=" and a.booking_no_id in($booking_id) ";
	else if ($booking_no!='') $booking_no_cond=" and a.booking_no LIKE '%$booking_no%'"; 
	
	if($order_no=="") $po_cond="";
	else
	{
		if($order_id!="") $po_cond="and b.id in(".$order_id.")";
		else
		{
			$po_number=trim($order_no);
			$po_cond="and b.po_number='$po_number'";
		}
	}
	
	if ($batch_no=="") $batch_no_cond=""; else $batch_no_cond=" and a.batch_no in ('$batch_no')";
	
	if ($working_company==0) $company_name_cond2=""; else $company_name_cond2="  and a.style_owner=".$working_company." ";
	if ($company_name==0) $company_name_cond2.=""; else $company_name_cond2.="  and a.company_name=".$company_name." ";
	if ($working_company==0) $dyeing_company_cond=""; else $dyeing_company_cond="  and a.service_company=".$working_company." ";
	if ($company_name==0) $dyeing_company_cond.=""; else $dyeing_company_cond.="  and a.company_id=".$company_name." ";
	if ($working_company==0) $knit_company_cond=""; else $knit_company_cond="  and a.knitting_company=".$working_company." ";
	if ($company_name==0) $knit_company_cond.=""; else $knit_company_cond.="  and a.company_id=".$company_name." ";
	//echo $booking_no_cond.'dd'; a.company_id=$company_name
	
	$recipe_no= str_replace("'","",$txt_recipe_no);
	$recipe_id= str_replace("'","",$hide_recipe_id);
	$req_no= str_replace("'","",$txt_req_no);
	$req_id= str_replace("'","",$hide_req_id);
	$recipeCond="";
	if ($recipe_id!='') $recipeCond=" and a.id in ($recipe_id)";
	else if ($recipe_no!='') $recipeCond.=" and a.id in ($recipe_no)";
	
	$reqCond="";
	if ($req_id!='') $reqCond=" and a.id in ($req_id)";
	else if($req_no!='') $reqCond.=" and a.requ_prefix_num in ($req_no)";
	
	$recipeBatchId="";  $recipeBatchId_Cond="";
	if($recipe_id!="" || $recipe_no!="" || $recipeDate_cond !="")
	{
		$recipeBatchSql="select a.id, a.batch_id from pro_recipe_entry_mst a, pro_recipe_entry_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in (59,60) $recipeDate_cond $recipeCond";
		$recipeBatchSqlRes=sql_select($recipeBatchSql);
		foreach($recipeBatchSqlRes as $row)
		{
			if($recipeBatchId=='') $recipeBatchId=$row[csf('batch_id')]; else $recipeBatchId.=','.$row[csf('batch_id')];
		}
		unset($recipeBatchSqlRes);
		
		$recipeBatchId=implode(",",array_filter(array_unique(explode(",",$recipeBatchId))));
		$receipebatch_ids=count(explode(",",$recipeBatchId));
		if($db_type==2 && $receipebatch_ids>1000)
		{
			$recipeBatchId_Cond=" and (";
			$recipeBatchIdsArr=array_chunk(explode(",",$recipeBatchId),999);
			foreach($recipeBatchIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$recipeBatchId_Cond.=" a.id in($ids) or"; 
			}
			$recipeBatchId_Cond=chop($recipeBatchId_Cond,'or ');
			$recipeBatchId_Cond.=")";
		}
		else $recipeBatchId_Cond=" and a.id in($recipeBatchId)";
	}
	
	$reqBatchId=""; $reqBatchId_Cond="";
	if($req_id!="" || $req_no!="" || $reqDate_cond !="")
	{
		$reqBatchSql="select a.id as req_id, b.batch_id from dyes_chem_issue_requ_mst a, dyes_chem_issue_requ_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $reqDate_cond $reqCond";
		$reqBatchSqlRes=sql_select($reqBatchSql);
		foreach($reqBatchSqlRes as $row)
		{
			if($reqBatchId=='') $reqBatchId=$row[csf('batch_id')]; else $reqBatchId.=','.$row[csf('batch_id')];
		}
		unset($reqBatchSqlRes);
		
		$reqBatchIds=implode(",",array_filter(array_unique(explode(",",$reqBatchId))));
		$reqbatch_ids=count(explode(",",$reqBatchIds));
		if($db_type==2 && $reqbatch_ids>1000)
		{
			$reqBatchId_Cond=" and (";
			$reqBatchIdsArr=array_chunk(explode(",",$reqBatchIds),999);
			foreach($reqBatchIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$reqBatchId_Cond.=" a.id in($ids) or"; 
			}
			$reqBatchId_Cond=chop($reqBatchId_Cond,'or ');
			$reqBatchId_Cond.=")";
		}
		else $reqBatchId_Cond=" and a.id in($reqBatchIds)";
	}

	// Dyes And Chemical Issue start
	$issueBatchId=""; $issueBatchId_Cond="";
	if($issueDate_cond !="")
	{
		$issBatchSql="SELECT a.batch_no as batch_id from inv_issue_master a where a.entry_form=5 and a.status_active=1 and a.is_deleted=0 $issueDate_cond";
		// echo $issBatchSql;die;
		$issBatchSqlRes=sql_select($issBatchSql);
		foreach($issBatchSqlRes as $row)
		{
			if($issueBatchId=='') $issueBatchId=$row[csf('batch_id')]; else $issueBatchId.=','.$row[csf('batch_id')];
		}
		unset($issBatchSqlRes);
		
		$issBatchIds=implode(",",array_filter(array_unique(explode(",",$issueBatchId))));
		$issbatch_ids=count(explode(",",$issBatchIds));
		if($db_type==2 && $issbatch_ids>1000)
		{
			$issBatchId_Cond=" and (";
			$issBatchIdsArr=array_chunk(explode(",",$issBatchIds),999);
			foreach($issBatchIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$issueBatchId_Cond.=" a.id in($ids) or"; 
			}
			$issueBatchId_Cond=chop($issueBatchId_Cond,'or ');
			$issueBatchId_Cond.=")";
		}
		else $issueBatchId_Cond=" and a.id in($issBatchIds)";
	}
	// echo $issueBatchId_Cond;die;

	// Dyeing Production
	$dyeingBatchId=""; $dyeingBatchId_Cond="";
	if($dyeingDate_cond !="")
	{
		$dyeingBatchSql="SELECT a.batch_id from pro_fab_subprocess a where a.entry_form=35 and a.status_active=1 and a.is_deleted=0 and load_unload_id=2 $dyeingDate_cond";
		// echo $dyeingBatchSql;die;
		$dyeingBatchSqlRes=sql_select($dyeingBatchSql);
		foreach($dyeingBatchSqlRes as $row)
		{
			if($dyeingBatchId=='') $dyeingBatchId=$row[csf('batch_id')]; else $dyeingBatchId.=','.$row[csf('batch_id')];
		}
		unset($dyeingBatchSqlRes);
		
		$dyeingBatchIds=implode(",",array_filter(array_unique(explode(",",$dyeingBatchId))));
		$dyeingbatch_ids=count(explode(",",$dyeingBatchIds));
		if($db_type==2 && $dyeingbatch_ids>1000)
		{
			$issBatchId_Cond=" and (";
			$dyeingBatchIdsArr=array_chunk(explode(",",$dyeingBatchIds),999);
			foreach($dyeingBatchIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$dyeingBatchId_Cond.=" a.id in($ids) or"; 
			}
			$dyeingBatchId_Cond=chop($dyeingBatchId_Cond,'or ');
			$dyeingBatchId_Cond.=")";
		}
		else $dyeingBatchId_Cond=" and a.id in($dyeingBatchIds)";
	}
	// echo $dyeingBatchId_Cond;die;
	// ====================================================================
	$po_cond_for_in="";  $sub_po_cond_for_in="";
	if($order_no!="" || $job_no!="" || $buyer_name!=0 || $cbo_year!=0)
	{
		if($batch_type==1 || $batch_type==0) // All
		{
			$poIdDataArray=sql_select("SELECT b.id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.status_active!=0 $buyer_id_cond $po_cond $job_no_cond $year_cond ");
			//echo "SELECT b.id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.status_active!=0 $buyer_id_cond $po_cond $job_no_cond $year_cond ";
			$self_all_po_id="";
			foreach($poIdDataArray as $row)
			{
				if($self_all_po_id=="") $self_all_po_id=$row[csf('id')]; else $self_all_po_id.=",".$row[csf('id')];
			}
			unset($poIdDataArray);
			
			$poIds=implode(",",array_filter(array_unique(explode(",",$self_all_po_id))));
			if($poIds!="")
			{
				$po_ids=count(explode(",",$poIds));
				if($db_type==2 && $po_ids>1000)
				{
					$po_cond_for_in=" and (";
					$poIdsArr=array_chunk(explode(",",$poIds),999);
					foreach($poIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$po_cond_for_in.=" b.po_id in($ids) or"; 
					}
					$po_cond_for_in=chop($po_cond_for_in,'or ');
					$po_cond_for_in.=")";
				}
				else $po_cond_for_in=" and b.po_id in($poIds)";
			}
		}
		//echo $po_cond_for_in;
		
		if($batch_type==2 || $batch_type==0) //SubCon
		{
			$subpoIdDataArray=sql_select("SELECT b.id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and b.status_active=1 and b.is_deleted=0 $sub_buyer_cond $suborder_no $job_no_cond $year_cond $sub_job_cond ");
			//echo "SELECT b.id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and b.status_active=1 and b.is_deleted=0 $sub_buyer_cond $suborder_no $job_no_cond $year_cond $sub_job_cond ";
			$subc_all_po_id="";
			foreach($subpoIdDataArray as $row)
			{
				if($subc_all_po_id=="") $subc_all_po_id=$row[csf('id')]; else $subc_all_po_id.=",".$row[csf('id')];
			} 
			unset($subpoIdDataArray);
			
			$subpoIds=implode(",",array_filter(array_unique(explode(",",$subc_all_po_id))));
			if($subpoIds!="")
			{
				$subpo_ids=count(explode(",",$subpoIds));
				if($db_type==2 && $subpo_ids>1000)
				{
					$sub_po_cond_for_in=" and (";
					$subpoIdsArr=array_chunk(explode(",",$subpoIds),999);
					foreach($subpoIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$sub_po_cond_for_in.=" b.po_id in($ids) or"; 
					}
					$sub_po_cond_for_in=chop($sub_po_cond_for_in,'or ');
					$sub_po_cond_for_in.=")";
				}
				else $sub_po_cond_for_in=" and b.po_id in($subpoIds)";
			} //echo  $sub_po_cond_for_in;
		}
	}
	
	ob_start();
	$batch_type_arr=array(0=>"All Batch",1=>"Self Batch",2=>"SubCon Batch",3=>"Sample Batch");	
	$colorArr=return_library_array( "select id,color_name from lib_color", "id", "color_name");	
	$companyArr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");			
	$floorArr=return_library_array( "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=3", "id", "floor_name");			
	if($batch_type==1) $type_cond="and a.entry_form=0"; else if($batch_type==2) $type_cond="and a.entry_form=36"; else if($batch_type==3) $type_cond="and a.booking_without_order=1"; else $type_cond="";
	
	if($db_type==2) $batchpoidCond="rtrim(xmlagg(xmlelement(e,b.po_id,',').extract('//text()') order by b.po_id).GetClobVal(),',')"; else $batchpoidCond="group_concat(b.po_id)";
	
	$sql_data="SELECT a.id, a.batch_no, a.batch_date, a.extention_no, a.floor_id, a.is_sales, a.batch_against, a.color_id, a.booking_no, a.entry_form, a.batch_against, $batchpoidCond as po_id, sum(b.batch_qnty) as batch_qty
		from pro_batch_create_mst a, pro_batch_create_dtls b
		where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 
		$batch_no_cond $batchDate_cond $booking_no_cond $workingCompany_cond $type_cond $po_cond_for_in $sub_po_cond_for_in $recipeBatchId_Cond $reqBatchId_Cond $issueBatchId_Cond $dyeingBatchId_Cond
		group by a.id, a.batch_no, a.batch_date, a.extention_no, a.floor_id, a.is_sales, a.batch_against, a.color_id, a.booking_no, a.entry_form";
	//echo $sql_data;
	$sql_dataRes=sql_select($sql_data);
	
	$allBatchId=""; $allPoId=""; $allSubPoId=""; $allFsoId="";
	foreach($sql_dataRes as $row)
	{
		if($db_type==2) $row[csf('po_id')]= $row[csf('po_id')]->load();
		if($allBatchId=="") $allBatchId="'".$row[csf('id')]."'"; else $allBatchId.=",'".$row[csf('id')]."'";
		if($row[csf('entry_form')]==36)
		{
			if($allSubPoId=="") $allSubPoId=$row[csf('po_id')]; else $allSubPoId.=",".$row[csf('po_id')];
		}
		else
		{
			if($allPoId=="") $allPoId=$row[csf('po_id')]; else $allPoId.=",".$row[csf('po_id')];
		}
		if ($row[csf('is_sales')]==1) 
		{
			if($allFsoId=="") $allFsoId=$row[csf('po_id')]; else $allFsoId.=",".$row[csf('po_id')];
		}
	}

	$allFsoIds=implode(",",array_filter(array_unique(explode(",",$allFsoId))));
	$fsoIdCond="";
	$po_ids=count(explode(",",$allFsoIds));
	if($db_type==2 && $po_ids>1000)
	{
		$fsoIdCond=" and (";
		$poIdsArr=array_chunk(explode(",",$allFsoIds),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$poIdCond.=" a.id in($ids) or"; 
		}
		$fsoIdCond=chop($fsoIdCond,'or ');
		$fsoIdCond.=")";
	}
	else $fsoIdCond=" and a.id in($allFsoIds)";
	
	if($batch_type==1 || $batch_type==0) // All, Self & Sample
	{
		$fsoDataArray=sql_select("SELECT a.id, a.customer_buyer, a.sales_booking_no
		from fabric_sales_order_mst a where a.status_active=1 $fsoIdCond");
		
		$cust_buyer_array=array();
		foreach($fsoDataArray as $row)
		{
			$cust_buyer_array[$row[csf('sales_booking_no')]]=$row[csf('customer_buyer')];
		}
		unset($fsoDataArray);
	}
	
	$subAllPoIds=implode(",",array_filter(array_unique(explode(",",$allSubPoId))));
	$subPoIdCond="";
	$subpo_ids=count(array_unique(explode(",",$subAllPoIds)));
	if($db_type==2 && $subpo_ids>1000)
	{
		$subPoIdCond=" and (";
		$spoIdsArr=array_chunk(explode(",",$subAllPoIds),999);
		foreach($spoIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$subPoIdCond.=" b.id in($ids) or"; 
		}
		$subPoIdCond=chop($subPoIdCond,'or ');
		$subPoIdCond.=")";
	}
	else $subPoIdCond=" and b.id in($subAllPoIds)";
	
	if($batch_type==2 || $batch_type==0) //SubCon & All
	{
		$subPoDataArray=sql_select("SELECT b.id, $year_field, b.order_no as po_number, a.party_id as buyer_name, a.subcon_job as job_no_prefix_num from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and b.status_active=1 and b.is_deleted=0 $subPoIdCond");
		$sub_job_array=array();
		foreach($subPoDataArray as $row)
		{
			$sub_job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$sub_job_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$sub_job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		} 
		unset($subPoDataArray);
	}
	
	$allPoIds=implode(",",array_filter(array_unique(explode(",",$allPoId))));
	$poIdCond="";
	$po_ids=count(explode(",",$allPoIds));
	if($db_type==2 && $po_ids>1000)
	{
		$poIdCond=" and (";
		$poIdsArr=array_chunk(explode(",",$allPoIds),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$poIdCond.=" b.id in($ids) or"; 
		}
		$poIdCond=chop($poIdCond,'or ');
		$poIdCond.=")";
	}
	else $poIdCond=" and b.id in($allPoIds)";
	
	if($batch_type==1 || $batch_type==0) // All, Self & Sample
	{
		$poDataArray=sql_select("SELECT b.id, b.pub_shipment_date, $year_field, b.po_number, a.buyer_name, a.job_no_prefix_num, a.style_ref_no as style 
		from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.status_active!=0 $poIdCond");
		
		$job_array=array();
		foreach($poDataArray as $row)
		{
			$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$job_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		unset($poDataArray);
	}
	
	$batchIds=implode(",",array_filter(array_unique(explode(",",$allBatchId))));
	$batchId_issCond=""; $batchId_recProdCond=""; $receipebatchCond="";
	$batch_ids=count(explode(",",$allBatchId));
	if($db_type==2 && $batch_ids>1000)
	{
		$receipebatchCond=" and (";
		$batchId_issCond=" and (";
		$batchId_recProdCond=" and (";
		$batchIdsArr=array_chunk(explode(",",$batchIds),999);
		foreach($batchIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$receipebatchCond.=" a.batch_id in($ids) or"; 
			$batchId_issCond.=" batch_id in($ids) or"; 
			$batchId_recProdCond.=" b.batch_id in($ids) or"; 
		}
		$receipebatchCond=chop($receipebatchCond,'or ');
		$receipebatchCond.=")";
		
		$batchId_issCond=chop($batchId_issCond,'or ');
		$batchId_issCond.=")";
		
		$batchId_recProdCond=chop($batchId_recProdCond,'or ');
		$batchId_recProdCond.=")";
	}
	else
	{
		$receipebatchCond=" and a.batch_id in($batchIds)";
		$batchId_issCond=" and batch_id in($batchIds)";
		$batchId_recProdCond=" and b.batch_id in($batchIds)";
	}
	
	$dyeing_data=array();
	$load_data=sql_select("select a.batch_id, a.load_unload_id, a.result, b.production_qty from pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.id=b.mst_id and a.entry_form in(35,38) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $receipebatchCond");
	foreach($load_data as $row_dyeing)
	{
		if($row_dyeing[csf('result')]>0)
		{
		$dyeing_data[$row_dyeing[csf('batch_id')]]['result']=$row_dyeing[csf('result')];
		}
		$dyeing_data[$row_dyeing[csf('batch_id')]]['status']=$row_dyeing[csf('load_unload_id')];
		if($row_dyeing[csf('load_unload_id')]==2)
			$dyeing_data[$row_dyeing[csf('batch_id')]]['qty']+=$row_dyeing[csf('production_qty')];
	}
	unset($load_data);
	
	$recipeArr=array();
	$recipeSql="select a.id, a.batch_id, a.batch_qty, a.entry_form, b.total_liquor, b.ratio, b.dose_base, b.new_batch_weight from pro_recipe_entry_mst a, pro_recipe_entry_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in (59,60) $receipebatchCond";
	
	$recipeSqlRes=sql_select($recipeSql);
	foreach($recipeSqlRes as $row)
	{
		$recipe_qnty=0;
		$ratio = $row[csf('ratio')];
		if ($row[csf('dose_base')] == 1) 
		{
			$perc_calculate_qnty = $row[csf('total_liquor')];
			$recipe_qnty = ($perc_calculate_qnty * $ratio) / 1000;
		} 
		else if ($row[csf('dose_base')] == 2) 
		{
			if ($row[csf('entry_form')] == 60) {
				$perc_calculate_qnty = $row[csf('new_batch_weight')];
			} else {
				$perc_calculate_qnty = $row[csf('batch_qty')];
			}
			$recipe_qnty = ($perc_calculate_qnty * $ratio) / 100;
		}
		$recipeArr[$row[csf('batch_id')]]['recipe_id']=$row[csf('id')];
		$recipeArr[$row[csf('batch_id')]]['recipe_qty']+=$recipe_qnty;
	}
	unset($recipeSqlRes);
	
	$reqArr=array();
	$reqSql="select a.id as req_id, b.batch_id, b.recipe_id, b.required_qnty as reqQty from dyes_chem_issue_requ_mst a, dyes_chem_issue_requ_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batchId_recProdCond";
	
	$reqSqlRes=sql_select($reqSql);
	foreach($reqSqlRes as $row)
	{
		$reqArr[$row[csf('batch_id')]]['req_id']=$row[csf('id')];
		$reqArr[$row[csf('batch_id')]]['recipe_id']=$row[csf('recipe_id')];
		$reqArr[$row[csf('batch_id')]]['reqQty']+=$row[csf('reqQty')];
	}
	unset($reqSqlRes);
	
	$dyeIssue_arr=array();
	$sql_dye_issue="select batch_id, req_qny_edit from dyes_chem_issue_dtls where status_active=1 and is_deleted=0 $batchId_issCond";
	$sql_dye_issueRes=sql_select($sql_dye_issue);
	foreach($sql_dye_issueRes as $row)
	{
		$dyeIssue_arr[$row[csf('batch_id')]]['issQty']+=$row[csf('req_qny_edit')];
	}
	unset($sql_dye_issueRes);
	
	$finishData_arr=array();
	$sql_finish=sql_select("SELECT a.entry_form, b.trans_id, b.batch_id, b.receive_qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66) and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batchId_recProdCond");
	foreach($sql_finish as $row)
	{
		if($row[csf('entry_form')]==7)
			$finishData_arr[$row[csf('batch_id')]]['prod']+=$row[csf('receive_qnty')];
		if($row[csf('trans_id')]!=0)
			$finishData_arr[$row[csf('batch_id')]]['rec']+=$row[csf('receive_qnty')];
	}
	unset($sql_finish);
	
	$sql_issDtls=sql_select("SELECT a.entry_form, b.batch_id, b.issue_qnty from inv_issue_master a, inv_finish_fabric_issue_dtls b where a.id=b.mst_id and a.entry_form in (18) and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batchId_recProdCond");
	foreach($sql_issDtls as $row)
	{
		if($row[csf('entry_form')]==18)
			$finishData_arr[$row[csf('batch_id')]]['iss']+=$row[csf('issue_qnty')];
	}
	unset($sql_issDtls);
	
	?>
    <div>
        <table width="1790" cellspacing="0" cellpadding="0" border="0" rules="all" >
            <tr class="form_caption">
                <td colspan="19" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?>  <? echo '('.$batch_type_arr[$batch_type].')'; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="19" align="center"><? if($company_name!=0) echo $companyArr[$company_name];else echo $companyArr[$working_company]; ?><br>
                </b><? echo  ($start_date == '0000-00-00' || $start_date == '' ? '' : change_date_format($start_date)).' To ';echo  ($end_date == '0000-00-00' || $end_date == '' ? '' : change_date_format($end_date)); ?> </b>
                </td>
            </tr>
        </table>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1790" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="70">Batch Date</th>
                <th width="100">Batch No</th>
                <th width="50">Ext.No</th>
                <th width="100">Floor</th>
                <th width="80">Batch Against</th>
                <th width="100">Color Name</th>
                <th width="110">Buyer</th>
                <th width="110">Cust. Buyer</th>
                <th width="70">Job No</th>
                <th width="110">F.Booking No</th>
                <th width="110">Order No</th>
                
                <th width="80">Batch Qty.</th>
                <th width="80">Recipe Qty.</th>
                <th width="80">Requisition Qty.</th>
                <th width="80">Issue Qty.</th>
                <th width="80">Dyeing Qty.</th>
                <th width="80">Fin Qty.</th>
                <th width="80">Fab. Rcv Qty.</th>
                <th width="80">Fab. Issue Qty.</th>
                <th>Unloading Result</th>
            </thead>
        </table>
        <div style="width:1810px; max-height:450px; overflow-y:scroll" id="scroll_body" > 
        	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1790" class="rpt_table" id="table_body">
	    		<?
				$i=1;
				foreach($sql_dataRes as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$poNo=""; $jobNo=""; $buyerName="";
					if($db_type==2) $row[csf('po_id')]= $row[csf('po_id')]->load();
					$expoid=array_unique(explode(',',$row[csf('po_id')]));
					foreach($expoid as $idpo)
					{
						if($row[csf('entry_form')]==0)
						{
							$poNo.=$job_array[$idpo]['po'].','; 
							$jobNo.=$job_array[$idpo]['job'].','; 
							$buyerName.=$buyerArr[$job_array[$idpo]['buyer']].',';
						}
						else if($row[csf('entry_form')]==36)
						{
							$poNo.=$sub_job_array[$idpo]['po'].',';
							$jobNo.=$sub_job_array[$idpo]['job'].','; 
							$buyerName.=$buyerArr[$sub_job_array[$idpo]['buyer']].',';
						}
					}
					
					$poNo=implode(",",array_filter(array_unique(explode(",",$poNo))));
					$jobNo=implode(",",array_filter(array_unique(explode(",",$jobNo))));
					$buyerName=implode(",",array_filter(array_unique(explode(",",$buyerName))));

					$cust_buyer=$cust_buyer_array[$row[csf('booking_no')]];
					
					?>
	                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
	                	<td width="30"><? echo $i; ?></td>
	                    <td width="70" style="word-break:break-all"><? echo change_date_format($row[csf('batch_date')]); ?></td>
	                    <td width="100" style="word-break:break-all"><? echo $row[csf('batch_no')]; ?></td>
	                    <td width="50"><? echo $row[csf('extention_no')]; ?></td>
	                    <td width="100"><? echo $floorArr[$row[csf('floor_id')]]; ?></td>
	                    <td width="80" style="word-break:break-all"><? echo $batch_against[$row[csf('batch_against')]]; ?></td>
	                    <td width="100" style="word-break:break-all"><? echo $colorArr[$row[csf('color_id')]]; ?></td>
	                    <td width="110" style="word-break:break-all"><? echo $buyerName; ?></td>
	                    <td width="110" style="word-break:break-all"><? echo $buyerArr[$cust_buyer]; ?></td>
	                    <td width="70" style="word-break:break-all"><? echo $jobNo; ?></td>
	                    <td width="110" style="word-break:break-all"><? echo $row[csf('booking_no')]; ?></td>
	                    <td width="110" style="word-break:break-all"><? echo $poNo; ?></td>
	                    
	                    <td width="80" align="right"><? echo number_format($row[csf('batch_qty')], 2,'.',''); ?></td>
	                    <td width="80" align="right"><a href='#report_details' onClick="generate_report('<? echo $company_name; ?>','<? echo $row[csf('id')]; ?>','recipeQty_popup');"><? echo number_format($recipeArr[$row[csf('id')]]['recipe_qty'], 2,'.',''); ?></a></td>
	                    <td width="80" align="right"><a href='#report_details' onClick="generate_report('<? echo $company_name; ?>','<? echo $row[csf('id')]; ?>','reqQty_popup');"><? echo number_format($reqArr[$row[csf('id')]]['reqQty'], 2,'.',''); ?></a></td>
	                    <td width="80" align="right"><a href='#report_details' onClick="generate_report('<? echo $company_name; ?>','<? echo $row[csf('id')]; ?>','dyeissueQty_popup');"><? echo number_format($dyeIssue_arr[$row[csf('id')]]['issQty'], 2,'.',''); ?></a></td>
	                    <td width="80" align="right"><? echo number_format($dyeing_data[$row[csf('id')]]['qty'], 2,'.',''); ?></td>
	                    <td width="80" align="right"><? echo number_format($finishData_arr[$row[csf('id')]]['prod'], 2,'.',''); ?></td>
	                    <td width="80" align="right"><a href='#report_details' onClick="generate_report('<? echo $company_name; ?>','<? echo $row[csf('id')]; ?>','finrecQty_popup');"><? echo number_format($finishData_arr[$row[csf('id')]]['rec'], 2,'.',''); ?></a></td>
	                    <td width="80" align="right"><a href='#report_details' onClick="generate_report('<? echo $company_name; ?>','<? echo $row[csf('id')]; ?>','finissueQty_popup');"><? echo number_format($finishData_arr[$row[csf('id')]]['iss'], 2,'.',''); ?></a></td>
	                    <td style="word-break:break-all"><? echo $dyeing_result[$dyeing_data[$row[csf('id')]]['result']]; ?></td>
	                </tr>
	                <?
					$i++;
					
					$grandBatchQty+=$row[csf('batch_qty')];
					$grandRecipeQty+=$recipeArr[$row[csf('id')]]['recipe_qty'];
					$grandReqQty+=$reqArr[$row[csf('id')]]['reqQty'];
					$grandIssueQty+=$dyeIssue_arr[$row[csf('id')]]['issQty'];
					$grandDyeQty+=$dyeing_data[$row[csf('id')]]['qty'];
					$grandProdQty+=$finishData_arr[$row[csf('id')]]['prod'];
					$grandRecQty+=$finishData_arr[$row[csf('id')]]['rec'];
					$grandFinIssueQty+=$finishData_arr[$row[csf('id')]]['iss'];
				}
				?>
        	</table>
        </div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1790" class="tbl_bottom">
            <tr style="font-size:13px">
                <td width="30">&nbsp;</td> 
                <td width="70">&nbsp;</td>
                <td width="100">&nbsp;</td>   
                <td width="50">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="110">&nbsp;</td>
                <td width="110">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="110">&nbsp;</td>
                
                <td width="110" align="right">Total : </td>
                <td width="80" align="right" id="value_td_batch"><? echo number_format($grandBatchQty, 2,'.',''); ?></td>   
                <td width="80" align="right" id="value_td_recipe"><? echo number_format($grandRecipeQty, 2,'.',''); ?></td>
                <td width="80" align="right" id="value_td_req"><? echo number_format($grandReqQty, 2,'.',''); ?></td>
                <td width="80" align="right" id="value_td_dyeIss"><? echo number_format($grandIssueQty, 2,'.',''); ?></td>
                <td width="80" align="right" id="value_td_dye"><? echo number_format($grandDyeQty, 2,'.',''); ?></td>
                <td width="80" align="right" id="value_td_prod"><? echo number_format($grandProdQty, 2,'.',''); ?></td>
                <td width="80" align="right" id="value_td_rec"><? echo number_format($grandRecQty, 2,'.',''); ?></td>
                <td width="80" align="right" id="value_td_finIss"><? echo number_format($grandFinIssueQty, 2,'.',''); ?></td>
                <td>&nbsp;</td>
             </tr>
        </table>
    </div>
    <?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

if($action=="recipeQty_popup")
{
	echo load_html_head_contents("Recipe Dtls Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
	</script>	
	<div style="width:730px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:720px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="700" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="8"><b>Recipe Details</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="110">Sys. ID</th>
                    <th width="70">Recipe Date</th>
                    <th width="80">Recipe Type</th>
                    <th width="120">Batch No</th>
                    <th width="60">Ext.</th>
                    <th width="105">Booking No</th>
                    <th>Color</th>
				</thead>
             </table>
             <div style="width:720px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="700" cellpadding="0" cellspacing="0">
                    <?
                    $i=1;
                    $colorArr=return_library_array("select id, color_name from lib_color", "id", "color_name");
					$recipeArr=return_library_array("select id, recipe_no_prefix_num from pro_recipe_entry_mst", "id", "recipe_no_prefix_num");
					$batchArr=array();
					$sql_batch="SELECT a.id, a.batch_no, a.extention_no, a.color_id, a.booking_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id='$batchid' ";
					$sql_batchRes=sql_select($sql_batch);
					foreach($sql_batchRes as $row)
					{
						$batchArr[$row[csf('id')]]['batchno']=$row[csf('batch_no')];
						$batchArr[$row[csf('id')]]['ext']=$row[csf('extention_no')];
						$batchArr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
						$batchArr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
					}
					unset($sql_batchRes);
	
					$sql="select a.id, a.recipe_no_prefix_num, a.recipe_no, a.entry_form, a.recipe_date, a.batch_id from pro_recipe_entry_mst a where a.is_deleted=0 and a.status_active=1 and a.batch_id='$batchid' and a.entry_form in (59,60) order by a.id ASC";
					//echo $sql;
                    $result=sql_select($sql); 
					
        			foreach($result as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						$recipeNo=''; $rType='';
						
						if($row[csf('entry_form')]==59) $rType='Main'; else $rType='Adding Toping';

						if($row[csf('recipe_no')]=='') $recipeNo=$row[csf('id')]; else $recipeNo=$row[csf('recipe_no')];
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="110"><? echo $recipeNo; ?></td>
								<td width="70" style="word-break:break-all"><? echo change_date_format($row[csf('recipe_date')]); ?></td>
								<td width="80" style="word-break:break-all"><? echo $rType; ?></td>
                                
                                <td width="120" style="word-break:break-all"><? echo $batchArr[$row[csf('batch_id')]]['batchno']; ?></td>
                                <td width="60" style="word-break:break-all"><? echo $batchArr[$row[csf('batch_id')]]['ext']; ?></td>
								<td width="105" style="word-break:break-all"><? echo $batchArr[$row[csf('batch_id')]]['booking_no']; ?></td>
								<td style="word-break:break-all"><? echo $colorArr[$batchArr[$row[csf('batch_id')]]['color_id']]; ?></td>
							</tr>
						<?
						$i++;
                    }
                    ?>
                </table>
            </div>	
        </div>
	</fieldset>   
	<?
    exit();	
}

if($action=="reqQty_popup")
{
	echo load_html_head_contents("Requisition Dtls Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
	</script>	
	<div style="width:875px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:870px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="12"><b>Dyes & Chemical Issue Requisition Details</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="60">Req. ID</th>
                    <th width="65">Req. Date</th>
                    <th width="60">Req. Type</th>
                    <th width="60">Recipie No</th>
                    <th width="110">Batch No</th>
                    <th width="105">Booking No</th>
                    <th width="110">Color</th>
                    <th width="60">Chem. Qty.</th>
                    <th width="60">Dyes Qty.</th>
                    <th width="60">Aux. Chem. Qty.</th>
                    <th>Dyes & Chem. Aux. Qty.</th>
				</thead>
             </table>
             <div style="width:870px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0">
                    <?
                    $i=1;
                    $colorArr=return_library_array("select id, color_name from lib_color", "id", "color_name");
					$recipeArr=array();
					$recipeSql="select id, recipe_no_prefix_num, entry_form from pro_recipe_entry_mst where entry_form in (59,60)";
					$recipeSqlRes=sql_select($recipeSql);
					foreach($recipeSqlRes as $row)
					{
						$rType='';
						if($row[csf('entry_form')]==59) $rType='Main'; else $rType='Adding Toping';
						
						$recipeArr[$row[csf('id')]]['rno']=$row[csf('recipe_no_prefix_num')];
						$recipeArr[$row[csf('id')]]['rType']=$rType;
					}
					unset($recipeSqlRes);
					
					$batchArr=array();
					$sql_batch="SELECT a.id, a.batch_no, a.extention_no, a.color_id, a.booking_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id='$batchid' ";
					$sql_batchRes=sql_select($sql_batch);
					foreach($sql_batchRes as $row)
					{
						$batchArr[$row[csf('id')]]['batchno']=$row[csf('batch_no')];
						$batchArr[$row[csf('id')]]['ext']=$row[csf('extention_no')];
						$batchArr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
						$batchArr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
					}
					unset($sql_batchRes);
					
					$sql="select a.id as req_id, a.requ_no, a.requ_prefix_num, a.requisition_date, b.recipe_id, b.batch_id,
					
					SUM(CASE WHEN c.item_category_id=5 THEN b.required_qnty END) AS chemicalsQty,
					SUM(CASE WHEN c.item_category_id=6 THEN b.required_qnty END) AS dyesQty,
					SUM(CASE WHEN c.item_category_id=7 THEN b.required_qnty END) AS auxChemicalsQty,
					SUM(CASE WHEN c.item_category_id=23 THEN b.required_qnty END) AS dyeAuxChemicalsQty
					
					from dyes_chem_issue_requ_mst a, dyes_chem_issue_requ_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.batch_id='$batchid' group by a.id, a.requ_no, a.requ_prefix_num, a.requisition_date, b.recipe_id, b.batch_id order by a.id ASC";
					//echo $sql;
                    $result=sql_select($sql); 
					
        			foreach($result as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						$recipeNo=0;
						
						if($recipeArr[$row[csf('recipe_id')]]['rno']==0) $recipeNo=$row[csf('recipe_id')]; else $recipeNo=$recipeArr[$row[csf('recipe_id')]]['rno'];
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="60"><? echo $row[csf('requ_prefix_num')]; ?></td>
								<td width="65" style="word-break:break-all"><? echo change_date_format($row[csf('requisition_date')]); ?></td>
								<td width="60" style="word-break:break-all"><? echo $recipeArr[$row[csf('recipe_id')]]['rType']; ?></td>
								<td width="60"><? echo $recipeNo; ?></td>
                                
                                <td width="110" style="word-break:break-all"><? echo $batchArr[$row[csf('batch_id')]]['batchno']; ?></td>
								<td width="105" style="word-break:break-all"><? echo $batchArr[$row[csf('batch_id')]]['booking_no']; ?></td>
								<td width="110" style="word-break:break-all"><? echo $colorArr[$batchArr[$row[csf('batch_id')]]['color_id']]; ?></td>
                                
								<td width="60" align="right"><? echo number_format($row[csf('chemicalsQty')],2); ?></td>
								<td width="60" align="right"><? echo number_format($row[csf('dyesQty')],2); ?></td>
								<td width="60" align="right"><? echo number_format($row[csf('auxChemicalsQty')],2); ?></td>
								<td align="right"><? echo number_format($row[csf('dyeAuxChemicalsQty')],2); ?></td>
							</tr>
						<?
						$i++;
						
						$chemicalsQty+=$row[csf('chemicalsQty')];
						$dyesQty+=$row[csf('dyesQty')];
						$auxChemicalsQty+=$row[csf('auxChemicalsQty')];
						$dyeAuxChemicalsQty+=$row[csf('dyeAuxChemicalsQty')];
                    }
                    ?>
                    <tfoot>
                        <th colspan="8" align="right">Total:</th>
                        <th align="right"><? echo number_format($chemicalsQty,2); ?></th>
                        <th align="right"><? echo number_format($dyesQty,2); ?></th>
                        <th align="right"><? echo number_format($auxChemicalsQty,2); ?></th>
                        <th align="right"><? echo number_format($dyeAuxChemicalsQty,2); ?></th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
	<?
    exit();
}

if($action=="dyeissueQty_popup")
{
	echo load_html_head_contents("Dyes And Chemical Issue Dtls Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
	</script>	
	<div style="width:875px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:870px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="12"><b>Dyes & Chemical Issue Details</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="60">Issue ID</th>
                    <th width="65">Issue Date</th>
                    <th width="70">Req. No</th>
                    <th width="60">Recipie No</th>
                    <th width="110">Batch No</th>
                    <th width="100">Booking No</th>
                    <th width="110">Color</th>
                    <th width="60">Chem. Qty.</th>
                    <th width="60">Dyes Qty.</th>
                    <th width="60">Aux. Chem. Qty.</th>
                    <th>Dyes & Chem. Aux. Qty.</th>
				</thead>
             </table>
             <div style="width:870px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0">
                    <?
                    $i=1;
                    $colorArr=return_library_array("select id, color_name from lib_color", "id", "color_name");
					$recipeArr=return_library_array("select id, recipe_no_prefix_num from pro_recipe_entry_mst", "id", "recipe_no_prefix_num");
					$batchArr=array();
					$sql_batch="SELECT a.id, a.batch_no, a.extention_no, a.color_id, a.booking_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id='$batchid' ";
					$sql_batchRes=sql_select($sql_batch);
					foreach($sql_batchRes as $row)
					{
						$batchArr[$row[csf('id')]]['batchno']=$row[csf('batch_no')];
						$batchArr[$row[csf('id')]]['ext']=$row[csf('extention_no')];
						$batchArr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
						$batchArr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
					}
					unset($sql_batchRes);
					
					$sql="select a.id, a.issue_number_prefix_num, a.issue_number, a.issue_date, b.requ_no, b.batch_id, b.recipe_id,
					
					SUM(CASE WHEN c.item_category_id=5 THEN b.req_qny_edit END) AS chemicalsQty,
					SUM(CASE WHEN c.item_category_id=6 THEN b.req_qny_edit END) AS dyesQty,
					SUM(CASE WHEN c.item_category_id=7 THEN b.req_qny_edit END) AS auxChemicalsQty,
					SUM(CASE WHEN c.item_category_id=23 THEN b.req_qny_edit END) AS dyeAuxChemicalsQty
					
					from inv_issue_master a, dyes_chem_issue_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.batch_id='$batchid' group by a.id, a.issue_number_prefix_num, a.issue_number, a.issue_date, b.requ_no, b.batch_id, b.recipe_id order by a.id ASC";
					//$sql_dye_issue="select batch_id, req_qny_edit from dyes_chem_issue_dtls where status_active=1 and is_deleted=0 $batchId_issCond";
					//echo $sql;
                    $result=sql_select($sql); 
					
        			foreach($result as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						$recipeNo=0;

						if($recipeArr[$row[csf('recipe_id')]]==0) $recipeNo=$row[csf('recipe_id')]; else $recipeNo=$recipeArr[$row[csf('recipe_id')]];
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="60"><? echo $row[csf('issue_number_prefix_num')]; ?></td>
								<td width="65" style="word-break:break-all"><? echo change_date_format($row[csf('issue_date')]); ?></td>
								<td width="70" style="word-break:break-all"><? echo $row[csf('requ_no')]; ?></td>
								<td width="60"><? echo $recipeNo; ?></td>
                                
                                <td width="110" style="word-break:break-all"><? echo $batchArr[$row[csf('batch_id')]]['batchno']; ?></td>
								<td width="100" style="word-break:break-all"><? echo $batchArr[$row[csf('batch_id')]]['booking_no']; ?></td>
								<td width="110" style="word-break:break-all"><? echo $colorArr[$batchArr[$row[csf('batch_id')]]['color_id']]; ?></td>
                                
								<td width="60" align="right"><? echo number_format($row[csf('chemicalsQty')],2); ?></td>
								<td width="60" align="right"><? echo number_format($row[csf('dyesQty')],2); ?></td>
								<td width="60" align="right"><? echo number_format($row[csf('auxChemicalsQty')],2); ?></td>
								<td align="right"><? echo number_format($row[csf('dyeAuxChemicalsQty')],2); ?></td>
							</tr>
						<?
						$i++;
						
						$chemicalsQty+=$row[csf('chemicalsQty')];
						$dyesQty+=$row[csf('dyesQty')];
						$auxChemicalsQty+=$row[csf('auxChemicalsQty')];
						$dyeAuxChemicalsQty+=$row[csf('dyeAuxChemicalsQty')];
                    }
                    ?>
                    <tfoot>
                        <th colspan="8" align="right">Total:</th>
                        <th align="right"><? echo number_format($chemicalsQty,2); ?></th>
                        <th align="right"><? echo number_format($dyesQty,2); ?></th>
                        <th align="right"><? echo number_format($auxChemicalsQty,2); ?></th>
                        <th align="right"><? echo number_format($dyeAuxChemicalsQty,2); ?></th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
	<?
    exit();
}

if($action=="finrecQty_popup")
{
	echo load_html_head_contents("Finish Fabric Receive Dtls Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
	</script>	
	<div style="width:875px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:870px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="10"><b>Finish Fabric Receive Details</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="110">Receive ID</th>
                    <th width="65">Receive Date</th>
                    <th width="80">Receive Basis</th>
                    <th width="110">Batch No</th>
                    <th width="60">Ext.</th>
                    <th width="100">Booking No</th>
                    <th width="110">Color</th>
                    <th width="60">Receive Qty.</th>
                    <th>Reject Qty.</th>
				</thead>
             </table>
             <div style="width:870px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0">
                    <?
                    $i=1;
                    $colorArr=return_library_array("select id, color_name from lib_color", "id", "color_name");
					$batchArr=array();
					$sql_batch="SELECT a.id, a.batch_no, a.extention_no, a.color_id, a.booking_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id='$batchid' ";
					$sql_batchRes=sql_select($sql_batch);
					foreach($sql_batchRes as $row)
					{
						$batchArr[$row[csf('id')]]['batchno']=$row[csf('batch_no')];
						$batchArr[$row[csf('id')]]['ext']=$row[csf('extention_no')];
						$batchArr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
						$batchArr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
					}
					unset($sql_batchRes);
					
					$receive_basis_arr=array(1=>"PI Based",2=>"WO/Booking Based",4=>"Independent",6=>"Opening Balance",9=>"Production",10=>"Delivery From Textile",11=>"Service Booking Based");
					$sql="SELECT a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.receive_date, b.batch_id, b.receive_qnty, b.reject_qty from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37) and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.trans_id!=0 and b.batch_id='$batchid'";
	
					//echo $sql;
                    $result=sql_select($sql); 
					
        			foreach($result as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="110"><? echo $row[csf('recv_number')]; ?></td>
								<td width="65" style="word-break:break-all"><? echo change_date_format($row[csf('receive_date')]); ?></td>
								<td width="80" style="word-break:break-all"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
                                
                                <td width="110" style="word-break:break-all"><? echo $batchArr[$row[csf('batch_id')]]['batchno']; ?></td>
                                <td width="60" align="center"><? echo $batchArr[$row[csf('batch_id')]]['ext']; ?></td>
								<td width="100" style="word-break:break-all"><? echo $batchArr[$row[csf('batch_id')]]['booking_no']; ?></td>
								<td width="110" style="word-break:break-all"><? echo $colorArr[$batchArr[$row[csf('batch_id')]]['color_id']]; ?></td>
                                
								<td width="60" align="right"><? echo number_format($row[csf('receive_qnty')],2); ?></td>
								<td align="right"><? echo number_format($row[csf('reject_qty')],2); ?></td>
							</tr>
						<?
						$i++;
						
						$receiveQty+=$row[csf('receive_qnty')];
						$rejectQty+=$row[csf('reject_qty')];
                    }
                    ?>
                    <tfoot>
                        <th colspan="8" align="right">Total:</th>
                        <th align="right"><? echo number_format($receiveQty,2); ?></th>
                        <th align="right"><? echo number_format($rejectQty,2); ?></th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
	<?
    exit();
}

if($action=="finissueQty_popup")
{
	echo load_html_head_contents("Finish Fabric Issue Dtls Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
	</script>	
	<div style="width:875px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:870px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="9"><b>Finish Fabric Issue Details</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="110">Issue ID</th>
                    <th width="65">Issue Date</th>
                    <th width="100">Issue Purpose</th>
                    <th width="110">Batch No</th>
                    <th width="60">Ext.</th>
                    <th width="100">Booking No</th>
                    <th width="130">Color</th>
                    <th>Issue Qty.</th>
				</thead>
             </table>
             <div style="width:870px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0">
                    <?
                    $i=1;
                    $colorArr=return_library_array("select id, color_name from lib_color", "id", "color_name");
					$batchArr=array();
					$sql_batch="SELECT a.id, a.batch_no, a.extention_no, a.color_id, a.booking_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id='$batchid' ";
					$sql_batchRes=sql_select($sql_batch);
					foreach($sql_batchRes as $row)
					{
						$batchArr[$row[csf('id')]]['batchno']=$row[csf('batch_no')];
						$batchArr[$row[csf('id')]]['ext']=$row[csf('extention_no')];
						$batchArr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
						$batchArr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
					}
					unset($sql_batchRes);
					
					$sql="SELECT a.id, a.issue_number_prefix_num, a.issue_number, a.issue_purpose, a.issue_date, b.batch_id, b.issue_qnty from inv_issue_master a, inv_finish_fabric_issue_dtls b where a.id=b.mst_id and a.entry_form in (18) and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.batch_id='$batchid'";
					//echo $sql;
                    $result=sql_select($sql); 
        			foreach($result as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="110"><? echo $row[csf('issue_number')]; ?></td>
								<td width="65" style="word-break:break-all"><? echo change_date_format($row[csf('issue_date')]); ?></td>
								<td width="100" style="word-break:break-all"><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></td>
                                
                                <td width="110" style="word-break:break-all"><? echo $batchArr[$row[csf('batch_id')]]['batchno']; ?></td>
                                <td width="60" align="center"><? echo $batchArr[$row[csf('batch_id')]]['ext']; ?></td>
								<td width="100" style="word-break:break-all"><? echo $batchArr[$row[csf('batch_id')]]['booking_no']; ?></td>
								<td width="130" style="word-break:break-all"><? echo $colorArr[$batchArr[$row[csf('batch_id')]]['color_id']]; ?></td>
								<td align="right"><? echo number_format($row[csf('issue_qnty')],2); ?></td>
							</tr>
						<?
						$i++;
						
						$issueQty+=$row[csf('issue_qnty')];
                    }
                    ?>
                    <tfoot>
                        <th colspan="8" align="right">Total:</th>
                        <th align="right"><? echo number_format($issueQty,2); ?></th>
                   </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
	<?
    exit();
}
?>	
