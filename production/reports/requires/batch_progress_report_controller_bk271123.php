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

$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$order_library=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
if($db_type==0) $field_concat="concat(machine_no,'-',brand) as machine_name"; 
else if($db_type==2) $field_concat="machine_no || '-' || brand as machine_name";
$machine_arr=return_library_array( "select id,$field_concat from  lib_machine_name",'id','machine_name');
$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );

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
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>', 'create_job_no_search_list_view', 'search_div', 'batch_progress_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
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
	$month_id=$data[5];
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
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by job_no";
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
	exit(); 
} // Job Search end

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 100, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=3 and company_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/batch_creation_controller',this.value, 'load_drop_machine', 'td_dyeing_machine' );",0 );

	exit();
}
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
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:740px;">
					<table width="740" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Buyer</th>
							<th width="170">Please Enter Booking No</th>
							<th>Booking Date</th>

							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
							<input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" />
							<input type="hidden" name="hide_booking_id" id="hide_booking_id" value="" />
						</thead>
						<tbody>
							<tr>
								<td align="center">
									<? 
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
									?>
								</td>   
								<td align="center">				
									<input type="text" style="width:150px" class="text_boxes" name="txt_booking_no" id="txt_booking_no" />	
								</td> 
								<td align="center">	
									<input type="text" style="width:70px" class="datepicker" name="txt_date_from" id="txt_date_from" readonly/> To
									<input type="text" style="width:70px" class="datepicker" name="txt_date_to" id="txt_date_to" readonly/>
								</td>     

								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_booking_no_search_list_view', 'search_div', 'batch_progress_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
								</td>
							</tr>
							<tr>
								<td colspan="4" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
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
	if($db_type==2)
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
	 select a.id,a.booking_no_prefix_num as no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no,a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_non_ord_samp_booking_mst a ,pro_batch_create_mst b where $company $buyer $booking_no $booking_date  and a.booking_no=b.booking_no and a.booking_type=4 and b.status_active=1 and b.is_deleted=0) order by id Desc
	";
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
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_order_no_search_list_view', 'search_div', 'batch_progress_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	//echo $data[1];
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
	$start_date =trim($data[4]);
	$end_date =trim($data[5]);	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	if($db_type==0) $year_field="YEAR(a.insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	$arr=array(0=>$company_arr,1=>$buyer_arr);
	$sql= "select b.id, $year_field a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond order by b.id, b.pub_shipment_date";
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,130,50,60,130,130","760","220",0, $sql , "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr , "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date", "",'','0,0,0,0,0,0,3','',1) ;
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
                  
                    <th>Batch No </th>
                    <th>Batch Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                        </td>	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_batch_no_search_list_view', 'search_div', 'batch_progress_report_controller', 'setFilterGrid(\'tbl_list\',-1)');" style="width:100px;" />
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
if($action=="create_batch_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	//echo $data[1];
	
	//$batch_no=$data[1];
	$search_string="%".trim($data[3])."%";
	$batch_no="%".trim($data[1])."%";
	//if($batch_no=='') $search_field="b.po_number";  else  $search_field="b.po_number";
	if ($batch_no=="") $batch_no_cond=""; else $batch_no_cond=" and a.batch_no like '$batch_no' "; 
		
	
	$start_date =trim($data[2]);
	$end_date =trim($data[3]);	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.batch_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.batch_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	//if($db_type==0) $year_field="YEAR(a.insert_date) as year,"; 
	//else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	
	$sql="select a.id,a.batch_no,a.batch_for,a.booking_no,a.color_id,a.batch_weight from pro_batch_create_mst a where a.company_id=$company_id and a.is_deleted=0 and a.status_active=1 $date_cond $batch_no_cond";	
$arr=array(1=>$color_library,3=>$batch_for);
	echo  create_list_view("tbl_list", "Batch no,Color,Booking no, Batch for,Batch weight ", "150,100,150,100,70","700","350",0, $sql, "js_set_value", "id,batch_no", "", 1, "0,color_id,0,batch_for,0", $arr , "batch_no,color_id,booking_no,batch_for,batch_weight", "",'','0','',1) ;
	exit();
}//Batch Search End
$tmplte=explode("**",$data);
if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name= str_replace("'","",$cbo_company_name);
	$working_company= str_replace("'","",$cbo_working_company);
	$job_no=str_replace("'","",$txt_job_no);
	$batch_type=str_replace("'","",$cbo_batch_type);
	$batch_no=str_replace("'","",$txt_batch_no);
	$buyer_name= str_replace("'","",$cbo_buyer_name);
	$cbo_year= str_replace("'","",$cbo_year);
	$txt_file_no= str_replace("'","",$txt_file_no);
	$txt_ref_no= str_replace("'","",$txt_ref_no);
	$floor_no = str_replace("'","",$cbo_floor);
	$hide_booking_id = str_replace("'","",$txt_hide_booking_id);
	$txt_booking_no = str_replace("'","",$txt_booking_no);
	//echo $program_no;
	$cbo_search_date= str_replace("'","",$cbo_search_date);
	$order_no = str_replace("'","",$txt_order_no);
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
		$buyer_id_cond=" and a.buyer_name in (".str_replace("'","",$buyer_name).")";
	}
	//$prod_detail_arr=return_library_array( "select id, item_description from product_details_master", "id", "item_description"  );
	
	$prod_sql= sql_select("select id,gsm,product_name_details from product_details_master");
	foreach($prod_sql as $row)
	{
		$prod_detail_arr[$row[csf("id")]]=$row[csf("product_name_details")];
		$prod_detail_gsm_arr[$row[csf("id")]]=$row[csf("gsm")];
	}
	
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		if($cbo_search_date==1)
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
				$date_cond=" and a.batch_date between '$start_date' and '$end_date'";
				$date_cond_dyeing=" and c.batch_date between '$start_date' and '$end_date'";
				$ship_date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
		else
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
				$date_cond=" and c.process_end_date between '$start_date' and '$end_date'";
				$date_cond_dyeing=" and a.process_end_date between '$start_date' and '$end_date'";
				$ship_date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
	
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
	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ('$job_no') ";
	if ($batch_no=="") $batch_no_cond=""; else $batch_no_cond=" and a.batch_no in ('$batch_no') ";
	if ($floor_no==0) $floor_num=""; else $floor_num="  and a.floor_id='".$floor_no."'";
	
	if ($txt_file_no=="") $file_cond=""; else $file_cond="  and b.file_no=$txt_file_no";
	if ($txt_ref_no=="") $ref_cond=""; else $ref_cond="  and b.grouping='$txt_ref_no'";
	if ($txt_booking_no!='') $booking_no_cond="  and a.booking_no LIKE '%$txt_booking_no%'"; else $booking_no_cond="";
	if ($hide_booking_id!=0) $booking_no_cond.="  and a.booking_no_id in($hide_booking_id) "; else $booking_no_cond.="";
	if ($working_company==0) $workingCompany_cond=""; else $workingCompany_cond="  and a.working_company_id=".$working_company." ";
	if ($company_name==0) $workingCompany_cond.=""; else $workingCompany_cond.="  and a.company_id=".$company_name." ";
	if ($company_name>0) 
	{ 
		$sub_conCompany_cond="and a.company_id=".$company_name." ";
	}
	else if ($working_company>0) 
	{ 
		$sub_conCompany_cond="and a.company_id=".$working_company." ";
	}
	else $sub_conCompany_cond="";
	
	
	if ($working_company==0) $company_name_cond2=""; else $company_name_cond2="  and a.style_owner=".$working_company." ";
	if ($company_name==0) $company_name_cond2.=""; else $company_name_cond2.="  and a.company_name=".$company_name." ";
	if ($working_company==0) $dyeing_company_cond=""; else $dyeing_company_cond="  and a.service_company=".$working_company." ";
	if ($company_name==0) $dyeing_company_cond.=""; else $dyeing_company_cond.="  and a.company_id=".$company_name." ";
	if ($working_company==0) $knit_company_cond=""; else $knit_company_cond="  and a.knitting_company=".$working_company." ";
	if ($company_name==0) $knit_company_cond.=""; else $knit_company_cond.="  and a.company_id=".$company_name." ";
	//echo $booking_no_cond.'dd'; a.company_id=$company_name
	
	//SubCon
	if($batch_type==2)
	{
		if ($cbo_buyer_name==0) $sub_buyer_cond=""; else $sub_buyer_cond="and a.party_id='".$cbo_buyer_name."' ";
		if ($order_no!='') $suborder_no="and b.order_no='$order_no'"; else $suborder_no="";
		if ($job_no!='') $sub_job_cond="  and a.job_no_prefix_num='".$job_no."' "; else $sub_job_cond="";
	}
	
	if($order_no=="")
	{
		$po_cond="";
	}
	else
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id=str_replace("'","",$hide_order_id);
			$po_cond="and b.id in(".$po_id.")";
		}
		else
		{
			$po_number=trim($order_no)."%";
			$po_cond="and b.po_number like '$po_number'";
		}
	}
	// ====================================================================
	if($batch_type==0) // All
	{
		$poDataArray=sql_select("SELECT b.id,b.pub_shipment_date,$year_field, b.po_number,a.buyer_name,a.job_no_prefix_num,a.style_ref_no as style,b.file_no,b.grouping 
		from  wo_po_break_down b,wo_po_details_master a 
		where  a.job_no=b.job_no_mst   $file_cond $ref_cond and b.status_active!=0   $buyer_id_cond $po_cond $job_no_cond $year_cond ");// $ship_date_cond
		
		$self_all_po_id='';
		$job_array=array(); $all_job_id='';
		foreach($poDataArray as $row)
		{
			$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$job_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
			$job_array[$row[csf('id')]]['style_no']=$row[csf('style')];
			$job_array[$row[csf('id')]]['file']=$row[csf('file_no')];
			$job_array[$row[csf('id')]]['refNo']=$row[csf('grouping')];			
			if($self_all_po_id=="") $self_all_po_id=$row[csf('id')]; else $self_all_po_id.=",".$row[csf('id')]; //echo $all_po_id;
		} 
		//echo $self_all_po_id;

		$poDataArray=sql_select("SELECT b.id,b.cust_style_ref,$year_field, b.order_no as po_number,a.party_id as buyer_name,a.subcon_job as job_no_prefix_num 
		from  subcon_ord_dtls b,subcon_ord_mst a 
		where  a.subcon_job=b.job_no_mst and b.status_active=1 and b.is_deleted=0  $sub_buyer_cond $suborder_no $job_no_cond $year_cond $sub_job_cond ");
		$subc_all_po_id='';
		$sub_job_array=array();
		/*echo "SELECT b.id,b.buyer_style_ref,$year_field, b.order_no as po_number,a.party_id as buyer_name,a.subcon_job as job_no_prefix_num 
		from  subcon_ord_dtls b,subcon_ord_mst a 
		where  a.subcon_job=b.job_no_mst and b.status_active=1 and b.is_deleted=0  $sub_buyer_cond $suborder_no $job_no_cond $year_cond $sub_job_cond ";die;*/
		foreach($poDataArray as $row)
		{
			$sub_job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$sub_job_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$sub_job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
			$sub_job_array[$row[csf('id')]]['style_no']=$row[csf('cust_style_ref')];
			if($subc_all_po_id=="") $subc_all_po_id=$row[csf('id')]; else $subc_all_po_id.=",".$row[csf('id')];
		} 
		//echo $subc_all_po_id;
	}
	else if($batch_type==1 || $batch_type==3)
	{
		$poDataArray=sql_select("SELECT b.id,b.pub_shipment_date,$year_field, b.po_number,a.buyer_name,a.job_no_prefix_num,a.style_ref_no as style,b.file_no,b.grouping 
		from  wo_po_break_down b,wo_po_details_master a 
		where  a.job_no=b.job_no_mst $file_cond $ref_cond and b.status_active!=0 and b.is_deleted=0  $buyer_id_cond $po_cond $job_no_cond $year_cond ");// $ship_date_cond
		$self_all_po_id='';
		$job_array=array(); $all_job_id='';
		foreach($poDataArray as $row)
		{
			$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$job_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
			$job_array[$row[csf('id')]]['style_no']=$row[csf('style')];
			$job_array[$row[csf('id')]]['file']=$row[csf('file_no')];
			$job_array[$row[csf('id')]]['refNo']=$row[csf('grouping')];
			if($self_all_po_id=="")
				$self_all_po_id=$row[csf('id')];
			else
				$self_all_po_id.=",".$row[csf('id')]; //echo $all_po_id;
		} //echo $all_po_id;
	}
	else if($batch_type==2) //SubCon //subcon_ord_dtls c, subcon_ord_mst d
	{
		$poDataArray=sql_select("SELECT b.id,b.cust_style_ref,$year_field, b.order_no as po_number,a.party_id as buyer_name,a.subcon_job as job_no_prefix_num 
		from  subcon_ord_dtls b,subcon_ord_mst a 
		where  a.subcon_job=b.job_no_mst and b.status_active=1 and b.is_deleted=0  $sub_buyer_cond $suborder_no $job_no_cond $year_cond $sub_job_cond ");
	
		// $ship_date_cond

		$subc_all_po_id='';
		$sub_job_array=array();
		foreach($poDataArray as $row)
		{
			$sub_job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$sub_job_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$sub_job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
			$sub_job_array[$row[csf('id')]]['style_no']=$row[csf('cust_style_ref')];
			if($subc_all_po_id=="") $subc_all_po_id=$row[csf('id')]; else $subc_all_po_id.=",".$row[csf('id')]; //echo $all_po_id;
		} //echo $all_po_id;
	}

	$heat_setting_arr=array();
	$sql_batch_h=sql_select("select a.batch_id,sum(CASE WHEN a.entry_form=32 THEN b.batch_qty ELSE 0 END) AS heat_qty,a.machine_id  from  pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.entry_form in(32,30)  and a.status_active=1 and a.is_deleted=0 and a.batch_id>0 $dyeing_company_cond group by a.batch_id,a.machine_id");

	foreach($sql_batch_h as $row_h)
	{
		$heat_setting_arr[$row_h[csf('batch_id')]]['qty']=$row_h[csf('heat_qty')];
		//$heat_setting_arr[$row_h[csf('batch_id')]]['machine']=$row_h[csf('machine_id')];
	} //var_dump($heat_setting_arr);

	$variable_production_roll=return_field_value("fabric_roll_level", "variable_settings_production", "variable_list=3 and item_category_id=50 and status_active=1 and is_deleted=0 and company_name=$cbo_company_name");
	$page_upto_id=return_field_value("page_upto_id","variable_settings_production","company_name =$cbo_company_name and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
	// echo $page_upto_id.'=='.$variable_production_roll;die;
	if(($page_upto_id==3 || $page_upto_id>3) && $variable_production_roll==1)
	{
	}
	$slitting_arr=array();$stentering_arr=array();$compacting_arr=array();$drying_arr=array();$special_arr=array();
	$sql_slitting=sql_select("SELECT a.batch_id,sum(b.batch_qty) AS slitting_qty,a.machine_id  from  pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.entry_form in(30)  and a.status_active=1 and a.is_deleted=0 and a.batch_id>0 $dyeing_company_cond  group by a.batch_id,a.machine_id");
	/*$sql_slitting=sql_select("SELECT a.id as batch_id, sum(b.batch_qnty) as slitting_qty 
	from pro_batch_create_dtls b,pro_batch_create_mst a 
	where a.id=b.mst_id and a.entry_form in(0,36) and b.status_active=1 and b.is_deleted=0  and a.status_active=1 and a.is_deleted=0 $dyeing_company_cond
	group by a.id
	union all 
	SELECT a.batch_id, sum(b.batch_qty) as slitting_qty
	from pro_fab_subprocess_dtls b, pro_fab_subprocess a 
	where a.id=b.mst_id and a.entry_form in(30) and a.status_active=1 and a.is_deleted=0 and a.batch_id>0 $dyeing_company_cond 
	group by  a.batch_id");*/
	foreach($sql_slitting as $row_s)
	{
		$slitting_arr[$row_s[csf('batch_id')]]['slitting']=$row_s[csf('slitting_qty')];
	}

	$sql_stenter=sql_select("SELECT a.batch_id,sum(b.production_qty) AS stentering_qty from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.entry_form in(48) and a.status_active=1 and a.is_deleted=0 and a.batch_id>0 $dyeing_company_cond group by a.batch_id"); //  and a.re_stenter_no=0
	foreach($sql_stenter as $row_st)
	{
		$stentering_arr[$row_st[csf('batch_id')]]['stentering']=$row_st[csf('stentering_qty')];
	}
	$sql_compect=sql_select("SELECT a.batch_id,sum(b.production_qty) AS compact_qty  from  pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.entry_form in(33)  and a.status_active=1 and a.is_deleted=0 and a.batch_id>0 $dyeing_company_cond group by a.batch_id"); // and a.re_stenter_no=0  
	foreach($sql_compect as $row_com)
	{
		$compacting_arr[$row_com[csf('batch_id')]]['compact']=$row_com[csf('compact_qty')];
	}
	$sql_drying=sql_select("select a.batch_id,sum(b.production_qty) AS drying_qty from  pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.entry_form in(31)  and a.status_active=1 and a.is_deleted=0 and a.batch_id>0  $dyeing_company_cond group by a.batch_id");
	foreach($sql_drying as $row_d)
	{
		$drying_arr[$row_d[csf('batch_id')]]['drying']=$row_d[csf('drying_qty')];
	}
	$sql_special=sql_select("select a.batch_id,sum(b.production_qty) AS special_qty from  pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.entry_form in(34) and a.status_active=1 and a.is_deleted=0 and a.batch_id>0 $dyeing_company_cond group by a.batch_id");
	foreach($sql_special as $row_sp)
	{
		$special_arr[$row_sp[csf('batch_id')]]['special']=$row_sp[csf('special_qty')];
	}
	if($db_type==0) $group_cond_batch="group_concat( distinct a.result) AS result";
	if($db_type==2) $group_cond_batch="listagg(a.result ,',') within group (order by a.result) AS result";
	
	$loading_data_arr=array();
	if($cbo_search_date==1)
	{	
		$load_data=sql_select("SELECT c.id, sum(CASE WHEN a.load_unload_id=1 THEN c.batch_weight ELSE 0 END) AS deying_load_qty,sum(CASE WHEN a.load_unload_id=2 THEN c.batch_weight ELSE 0 END) AS deying_unload_qty 
		from pro_fab_subprocess a,pro_batch_create_mst c 
		where  a.batch_id=c.id and a.load_unload_id in(1,2) and a.entry_form in(35,38) $date_cond_dyeing and a.status_active=1 and a.is_deleted=0 $dyeing_company_cond  group by c.id ");	
		/*echo "SELECT c.id, sum(CASE WHEN a.load_unload_id=1 THEN c.batch_weight ELSE 0 END) AS deying_load_qty,sum(CASE WHEN a.load_unload_id=2 THEN c.batch_weight ELSE 0 END) AS deying_unload_qty 
		from pro_fab_subprocess a,pro_batch_create_mst c 
		where  a.batch_id=c.id and a.load_unload_id in(1,2) and a.entry_form in(35,38) $date_cond_dyeing and a.status_active=1 and a.is_deleted=0 $dyeing_company_cond  group by c.id ";*/
	}
	else
	{
		$load_data=sql_select("SELECT c.id, sum(CASE WHEN a.load_unload_id=1 THEN c.batch_weight ELSE 0 END) AS deying_load_qty,sum(CASE WHEN a.load_unload_id=2 THEN c.batch_weight ELSE 0 END) AS deying_unload_qty 
		from pro_fab_subprocess a,pro_batch_create_mst c 
		where  a.batch_id=c.id and a.load_unload_id in(1,2) and a.entry_form in(35,38) $date_cond_dyeing and a.status_active=1 and a.is_deleted=0 $dyeing_company_cond group by c.id ");	
	}	
	
	foreach($load_data as $row_dyeing)// for Loading time
	{
		$loading_data_arr[$row_dyeing[csf('id')]]['load']=$row_dyeing[csf('deying_load_qty')];
		$loading_data_arr[$row_dyeing[csf('id')]]['unload']=$row_dyeing[csf('deying_unload_qty')];
	}
	
	$unloading_data=array();
	$load_data=sql_select("select a.batch_id,a.floor_id,a.result,a.machine_id,a.remarks from pro_fab_subprocess a where  a.load_unload_id in(2) and a.entry_form in(35,38) and a.status_active=1 and a.is_deleted=0  $dyeing_company_cond $floor_num  group by a.batch_id,a.floor_id,a.result,a.machine_id,a.remarks ");
	foreach($load_data as $row_dyeing)// for Loading time
	{
		$unloading_data[$row_dyeing[csf('batch_id')]]['result']=$row_dyeing[csf('result')];
		$unloading_data[$row_dyeing[csf('batch_id')]]['machine']=$row_dyeing[csf('machine_id')];
		$unloading_data[$row_dyeing[csf('batch_id')]]['remarks']=$row_dyeing[csf('remarks')];
		$unloading_data[$row_dyeing[csf('batch_id')]]['floor_id']=$row_dyeing[csf('floor_id')];
	}
	$finish_data_arr=array();
	$sql_dtls=sql_select("SELECT b.batch_id,sum(b.receive_qnty) as finish_qty	
	from inv_receive_master a,pro_finish_fabric_rcv_dtls b 
	where a.id=b.mst_id  and a.entry_form in(7,66)  and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $knit_company_cond 
	group by b.batch_id");
	foreach($sql_dtls as $row_fin)// for Finish Production
	{
		$finish_data_arr[$row_fin[csf('batch_id')]]['finish_qty']=$row_fin[csf('finish_qty')];
		//$finish_data_arr[$row_fin[csf('batch_id')]]['batch_status']=$row_fin[csf('batch_status')];
	}
	$finish_data_status=array();
	$sql_dtls_status=sql_select("select b.batch_id,a.recv_number as recv_number,b.batch_status as batch_status	from  inv_receive_master a,pro_finish_fabric_rcv_dtls b where  a.id=b.mst_id and a.entry_form in(7,66) and a.item_category=2 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $knit_company_cond   order by b.id ");
	foreach($sql_dtls_status as $row_st)
	{
		$finish_data_status[$row_st[csf('batch_id')]]['batch_status']=$row_st[csf('batch_status')];
	}
	$delivery_data_arr=array();
	/*$sql_dtls=sql_select("SELECT b.program_no as batch_id,sum(b.current_delivery) as delivery_qty from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b where  a.id=b.mst_id and a.entry_form=54 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $knit_company_cond  group by b.program_no");*/
	$sql_dtls=sql_select("SELECT b.program_no, b.batch_id, a.entry_form, b.current_delivery as delivery_qty, a.delevery_date 
	from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b 
	where  a.id=b.mst_id and a.entry_form in(54,67) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $knit_company_cond");
	foreach($sql_dtls as $row_del)
	{
		if ($row_del[csf('entry_form')]==67) // finish roll
		{
			$delivery_data_arr[$row_del[csf('batch_id')]]['delivery']+=$row_del[csf('delivery_qty')];
			$delivery_data_arr[$row_del[csf('batch_id')]]['delivery_date'].=$row_del[csf('delevery_date')].',';
		}
		else
		{
			$delivery_data_arr[$row_del[csf('program_no')]]['delivery']+=$row_del[csf('delivery_qty')];
			$delivery_data_arr[$row_del[csf('program_no')]]['delivery_date'].=$row_del[csf('delevery_date')].',';
		}
	}
	// echo "<pre>";print_r($delivery_data_arr);die;
	
	ob_start();
	$batch_type_arr=array(1=>"Self Batch",2=>"SubCon Batch",3=>"Sample Batch",4=>"Sales Batch");
	//b.gsm,b.fin_dia
	//listagg(cast(b.item_description as varchar2(4000)),'**') within group (order by b.item_description) AS fabric_type,
	/*if($db_type==0) $group_concat="group_concat( distinct b.prod_id,'**') AS fabric_type,group_concat( distinct b.gsm,'**') AS gsm,group_concat(distinct b.po_id)  AS po_id,group_concat(distinct b.width_dia_type) AS width_dia_type"; 
	else if($db_type==2)  $group_concat="rtrim(xmlagg(xmlelement(e,b.prod_id,',').extract('//text()') order by b.prod_id).GetClobVal(),',') as fabric_type,
	rtrim(xmlagg(xmlelement(e,b.po_id,',').extract('//text()') order by b.po_id).GetClobVal(),',') as po_id,
	rtrim(xmlagg(xmlelement(e,b.gsm,',').extract('//text()') order by b.gsm).GetClobVal(),',') as gsm,
	rtrim(xmlagg(xmlelement(e,b.width_dia_type,',').extract('//text()') order by b.width_dia_type).GetClobVal(),',') as width_dia_type";*/

	//rtrim(xmlagg(xmlelement(e,c.id,',').extract('//text()') order by c.id).GetClobVal(),',') as id
	//	else if($db_type==2)  $group_concat="listagg(b.prod_id ,'**') within group (order by b.prod_id) AS fabric_type,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id,listagg(b.gsm ,'**') within group (order by b.gsm) AS gsm,listagg(b.width_dia_type ,',') within group (order by b.width_dia_type) AS width_dia_type";
	$po_id_cond="";
	if($order_no!="" || $job_no!="" || str_replace("'","",$cbo_buyer_name)!=0 || $cbo_year!=0  || $start_date!="")
	{
		$po_id_cond=" $self_all_po_id";
	}
	//	if ($txt_file_no=="") $file_cond=""; else $file_cond="  and b.file_no=$txt_file_no";
	//if ($txt_ref_no=="")
	$self_po_id_cond="";
	if($order_no!="" || $job_no!="" || str_replace("'","",$cbo_buyer_name)!=0 || $cbo_year!=0 || $txt_file_no!="" || $txt_ref_no!="")
	{
		$self_po_id_cond=" $self_all_po_id";
		//echo  $self_po_id_cond.'D';
	}

	$subc_po_id_cond="";
	if($order_no!="" || $job_no!="" || str_replace("'","",$cbo_buyer_name)!=0 || $cbo_year!=0 || $txt_file_no!="" || $txt_ref_no!="" )
	{
		$subc_po_id_cond=" $subc_all_po_id";
	}

	//echo $subc_all_po_id.'DDD';;
	$po_id_cond_split=array_chunk(array_unique(explode(",",$po_id_cond)),999);
	$self_po_id_cond_split=array_chunk(array_unique(explode(",",$self_po_id_cond)),999);
	$subc_po_id_cond_split=array_chunk(array_unique(explode(",",$subc_po_id_cond)),999);
	
	if($order_no!="" || $job_no!="" || str_replace("'","",$cbo_buyer_name)!=0 || $cbo_year!=0 || $txt_file_no!="" || $txt_ref_no!="")
	{
		$poIds=chop($self_po_id_cond,','); $po_cond_for_in="";
		$po_ids=count(array_unique(explode(",",$self_po_id_cond)));
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
		else
		{
			$po_cond_for_in=" and b.po_id in($poIds)";
			
		}
		$subpoIds=chop($subc_po_id_cond,','); $sub_po_cond_for_in="";
		$subpo_ids=count(array_unique(explode(",",$subc_po_id_cond)));
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
		else
		{
			$sub_po_cond_for_in=" and b.po_id in($subpoIds)";
			
		}
	}
		
	//echo  $po_cond_for_in.'D';

	if($batch_type==1)
		$type_cond="and a.entry_form=0";
	else if($batch_type==2)
		$type_cond="and a.entry_form=36";
	else
		$type_cond="";
		
	if($cbo_search_date==1)
	{
		if ($batch_type==1) // Self
		{
			$sql_data="(SELECT a.id,a.batch_no,a.save_string,a.floor_id,a.entry_form,a.batch_against,sum(b.batch_qnty) as batch_qty, count(b.id) as no_of_roll, a.batch_date,a.batch_weight,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,a.total_trims_weight,a.color_range_id 
			from pro_batch_create_dtls b,pro_batch_create_mst a 
			where a.id=b.mst_id and a.batch_against !=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $batch_no_cond $date_cond $booking_no_cond $workingCompany_cond $type_cond $po_cond_for_in $floor_num group by a.batch_no,a.floor_id,a.batch_against,a.entry_form,a.id,a.save_string,a.batch_date,a.batch_weight,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.po_id,b.width_dia_type,a.total_trims_weight,a.color_range_id)";
		}
		else if ($batch_type==2) // Subcon
		{
			$sql_data="(SELECT a.id,a.batch_no,a.floor_id,a.save_string,a.entry_form,a.batch_against,(b.batch_qnty) as batch_qty, a.batch_date,a.batch_weight,b.prod_id,b.po_id,b.gsm,b.item_description,b.width_dia_type,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,a.total_trims_weight,a.color_range_id  
			from pro_batch_create_dtls b,pro_batch_create_mst a 
			where a.id=b.mst_id and a.batch_against !=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $batch_no_cond $floor_num $date_cond $booking_no_cond $sub_conCompany_cond $type_cond $sub_po_cond_for_in ) order by a.id";

			/*$p=1;
			foreach($subc_po_id_cond_split as $po_row)
			{
			if($p==1) $sql_data .=" and (b.po_id  in(".implode(',',$po_row).")"; else  $sql_data .=" OR b.po_id in(".implode(',',$po_row).")";
			$p++;
			}
			$sql_data .=")";
			$sql_data .=" group by a.batch_no,a.batch_against,a.entry_form,a.id,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no)";*/
			//echo $sql_data ;
		}
		else if ($batch_type==3) // Sample
		{
			// $sql_data="SELECT a.id,a.batch_no,a.floor_id,a.entry_form,a.batch_against,sum(b.batch_qnty) as batch_qty, a.batch_date,a.batch_weight,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.booking_no,a.extention_no 
			//from pro_batch_create_dtls b,pro_batch_create_mst a 
			//where a.id=b.mst_id and a.batch_against=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.entry_form=0 $batch_no_cond $date_cond $booking_no_cond $workingCompany_cond $po_cond_for_in
			//group by a.batch_no,a.batch_against,a.floor_id,a.entry_form,a.id,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,b.prod_id,b.po_id,b.width_dia_type";
			 $sql_data="SELECT a.id,a.batch_no,a.floor_id,a.save_string,a.entry_form,a.batch_against,sum(b.batch_qnty) as batch_qty, a.batch_date,a.batch_weight,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,a.total_trims_weight,a.color_range_id
			from pro_batch_create_dtls b, pro_batch_create_mst a 
			where a.id=b.mst_id and a.batch_against=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.entry_form=0 $batch_no_cond $floor_num $date_cond $booking_no_cond $workingCompany_cond $po_cond_for_in
			group by a.batch_no,a.batch_against,a.floor_id,a.entry_form,a.save_string,a.id,a.batch_date,a.batch_weight,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.po_id,b.width_dia_type,a.total_trims_weight,a.color_range_id
			union
			SELECT a.id,a.batch_no,a.floor_id,a.save_string,a.entry_form,a.batch_against,sum(b.batch_qnty) as batch_qty, a.batch_date,a.batch_weight,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.total_trims_weight,a.color_range_id 
			from pro_batch_create_dtls b,pro_batch_create_mst a 
			where a.id=b.mst_id and a.batch_against=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1  and a.is_deleted=0 and a.entry_form=0 
			and b.po_id=0
			$batch_no_cond $floor_num $date_cond $booking_no_cond $workingCompany_cond
			group by a.batch_no,a.batch_against,a.floor_id,a.save_string,a.entry_form,a.id,a.batch_date,a.batch_weight,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.po_id,b.width_dia_type, a.total_trims_weight,a.color_range_id";
		}
		else if ($batch_type==4) // Sales
		{
			
			$sql_data="(SELECT a.id,a.batch_no,a.floor_id,a.save_string,a.entry_form,a.batch_against,sum(b.batch_qnty) as batch_qty, a.batch_date,a.batch_weight,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.style_ref_no,a.booking_no,a.sales_order_no,a.sales_order_id,a.extention_no,c.within_group,c.sales_booking_no,c.buyer_id,c.po_buyer,c.po_job_no, a.total_trims_weight,a.color_range_id 
			from pro_batch_create_dtls b,pro_batch_create_mst a ,fabric_sales_order_mst c
			where a.id=b.mst_id and c.id=a.sales_order_id and a.batch_against !=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.is_sales=1 and a.entry_form=0 $batch_no_cond $floor_num $date_cond $booking_no_cond $workingCompany_cond   group by a.batch_no,a.batch_against,a.entry_form,a.save_string,a.floor_id,a.id,a.batch_date,a.batch_weight,a.color_id,a.style_ref_no,a.booking_no,a.sales_order_no,a.sales_order_id,a.extention_no,b.prod_id,b.po_id,b.width_dia_type,c.within_group,c.sales_booking_no,c.buyer_id,c.po_buyer,c.po_job_no,a.total_trims_weight,a.color_range_id)";
		}
		else // All
		{
			// Self
			$self_sql_data="(SELECT a.id,a.batch_no,a.floor_id,a.save_string,a.entry_form,a.batch_against,sum(b.batch_qnty) as batch_qty, count(b.id) as no_of_roll, a.batch_date,a.batch_weight,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,a.total_trims_weight,a.color_range_id  
			from pro_batch_create_dtls b,pro_batch_create_mst a 
			where a.id=b.mst_id and a.batch_against !=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.is_sales=0 and a.entry_form=0 $batch_no_cond $date_cond $floor_num $booking_no_cond $workingCompany_cond  $po_cond_for_in group by a.batch_no,a.batch_against,a.entry_form,a.floor_id,a.save_string,a.id,a.batch_date,a.batch_weight,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.po_id,b.width_dia_type,a.total_trims_weight,a.color_range_id)";

			/*$p=1;
			foreach($self_po_id_cond_split as $po_row)
			{
			if($p==1) $self_sql_data .=" and (b.po_id  in(".implode(',',$po_row).")"; else  $self_sql_data .=" OR b.po_id in(".implode(',',$po_row).")";
			$p++;
			}
			$self_sql_data .=")";
			$self_sql_data .=" group by a.batch_no,a.batch_against,a.entry_form,a.id,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no)";*/
			//echo $self_sql_data.'<br>';


			// Subcon
			 $subc_sql_data="(SELECT a.id,a.batch_no,a.floor_id,a.entry_form,a.batch_against,(b.batch_qnty) as batch_qty, a.batch_date,a.batch_weight,b.prod_id,b.po_id,b.gsm,b.item_description,b.width_dia_type,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,a.total_trims_weight,a.color_range_id  
			from pro_batch_create_dtls b,pro_batch_create_mst a 
			where a.id=b.mst_id and a.batch_against !=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.entry_form=36 $batch_no_cond $floor_num $date_cond $booking_no_cond $sub_conCompany_cond  $sub_po_cond_for_in ) order by a.id";

			/*$p=1;
			foreach($subc_po_id_cond_split as $po_row)
			{
			if($p==1) $subc_sql_data .=" and (b.po_id  in(".implode(',',$po_row).")"; else  $subc_sql_data .=" OR b.po_id in(".implode(',',$po_row).")";
			$p++;
			}
			$subc_sql_data .=")";
			$subc_sql_data .=" group by a.batch_no,a.batch_against,a.entry_form,a.id,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no)";*/
			//echo $subc_sql_data.'<br>';


			// Sample
			 $samp_sql_data="SELECT a.id,a.batch_no,a.floor_id,a.save_string,a.entry_form,a.batch_against,sum(b.batch_qnty) as batch_qty, a.batch_date,a.batch_weight,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.total_trims_weight,a.color_range_id  
			from pro_batch_create_dtls b,pro_batch_create_mst a 
			where a.id=b.mst_id and a.batch_against=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.entry_form=0 $batch_no_cond $floor_num $date_cond $booking_no_cond $workingCompany_cond $po_cond_for_in
			group by a.batch_no,a.batch_against,a.floor_id,a.entry_form,a.save_string,a.id,a.batch_date,a.batch_weight,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.po_id,b.width_dia_type,a.total_trims_weight,a.color_range_id 
			union
			SELECT a.id,a.batch_no,a.floor_id,a.save_string,a.entry_form,a.batch_against,sum(b.batch_qnty) as batch_qty, a.batch_date,a.batch_weight,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.total_trims_weight,a.color_range_id  
			from pro_batch_create_dtls b,pro_batch_create_mst a 
			where a.id=b.mst_id and a.batch_against=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1  and a.is_deleted=0 and a.entry_form=0 
			and b.po_id=0
			$batch_no_cond $floor_num $date_cond $booking_no_cond $workingCompany_cond
			group by a.batch_no,a.batch_against,a.floor_id,a.entry_form,a.save_string,a.id,a.batch_date,a.batch_weight,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.po_id,b.width_dia_type,a.total_trims_weight,a.color_range_id";
			//echo $samp_sql_data.'<br>';
			
			 $sales_sql_data="(SELECT a.id,a.batch_no,a.floor_id,a.save_string,a.entry_form,a.batch_against,sum(b.batch_qnty) as batch_qty, a.batch_date,a.batch_weight,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.style_ref_no,a.booking_no,a.sales_order_no,a.sales_order_id,a.extention_no,c.within_group,c.sales_booking_no,c.buyer_id,c.po_buyer,c.po_job_no, a.total_trims_weight,a.color_range_id 
			from pro_batch_create_dtls b,pro_batch_create_mst a ,fabric_sales_order_mst c
			where a.id=b.mst_id and c.id=a.sales_order_id and a.batch_against !=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.is_sales=1 and a.entry_form=0 $batch_no_cond $date_cond $floor_num $booking_no_cond $workingCompany_cond group by a.batch_no,a.batch_against,a.entry_form,a.floor_id,a.save_string,a.id,a.batch_date,a.batch_weight,a.color_id,a.style_ref_no,a.booking_no,a.sales_order_no,a.sales_order_id,a.extention_no,b.prod_id,b.po_id,b.width_dia_type,c.within_group,c.sales_booking_no,c.buyer_id,c.po_buyer,c.po_job_no,a.total_trims_weight,a.color_range_id)";
		}	
		//echo $sql_data;
	}
	else if($cbo_search_date==2)
	{
		if ($batch_type==1) // Self
		{
			$sql_data="(SELECT a.id,a.batch_no,a.floor_id,a.save_string,a.entry_form,a.batch_against,sum(b.batch_qnty) as batch_qty, count(b.id) as no_of_roll, c.process_end_date as batch_date,a.batch_weight,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.total_trims_weight,a.color_range_id  
			from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess c 
			where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and c.entry_form=35 and a.batch_against !=3 and a.is_sales=0 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $batch_no_cond  $date_cond $floor_num $booking_no_cond $workingCompany_cond $type_cond $po_cond_for_in group by a.batch_no,a.floor_id,a.batch_against,a.entry_form,a.save_string,a.id,c.process_end_date,a.batch_weight,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.po_id,b.width_dia_type, a.total_trims_weight,a.color_range_id)";
			/*$p=1;
			foreach($po_id_cond_split as $po_row)
			{
			if($p==1) $sql_data .=" and (b.po_id  in(".implode(',',$po_row).")"; else  $sql_data .=" OR b.po_id in(".implode(',',$po_row).")";
			$p++;
			}
			$sql_data .=")";
			$sql_data .=" group by a.batch_no,a.batch_against,a.entry_form,a.id,c.process_end_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no)";	*/
		}
		else if ($batch_type==4) // Sales
		{
			$sql_data="(SELECT a.id,a.batch_no,a.floor_id,a.save_string,a.entry_form,a.batch_against,sum(b.batch_qnty) as batch_qty, c.process_end_date as batch_date,a.batch_weight,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,a.sales_order_no,a.sales_order_id,d.within_group,d.sales_booking_no,d.buyer_id,d.po_buyer,d.po_job_no, a.total_trims_weight,a.color_range_id 
			from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess c,fabric_sales_order_mst d 
			where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and d.id=a.sales_order_id  and c.entry_form=35 and a.batch_against !=3 and a.is_sales=1 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $batch_no_cond  $date_cond $floor_num $booking_no_cond $workingCompany_cond $type_cond group by a.batch_no,a.floor_id,a.save_string,a.batch_against,a.entry_form,a.id,c.process_end_date,a.batch_weight,a.color_id,a.style_ref_no,a.booking_no,a.sales_order_no,a.sales_order_id,a.extention_no,b.prod_id,b.po_id,b.width_dia_type,d.sales_booking_no,d.buyer_id,d.po_buyer,d.within_group,d.po_job_no, a.total_trims_weight,a.color_range_id)";
		
		}

		else if ($batch_type==2) // Subcon
		{
			$sql_data="(SELECT a.id,a.batch_no,a.floor_id,a.entry_form,a.batch_against,(b.batch_qnty) as batch_qty, c.process_end_date as batch_date,a.batch_weight,b.prod_id,b.po_id,b.gsm,b.item_description,b.width_dia_type,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,a.total_trims_weight,a.color_range_id  
			from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess c 
			where  a.id=b.mst_id  and c.batch_id=a.id  and c.batch_id=b.mst_id and c.entry_form=38 and a.batch_against !=3 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $batch_no_cond $floor_num $date_cond $booking_no_cond $sub_conCompany_cond $type_cond $sub_po_cond_for_in ) order by a.id";
			/*$p=1;
			foreach($po_id_cond_split as $po_row)
			{
			if($p==1) $sql_data .=" and (b.po_id  in(".implode(',',$po_row).")"; else  $sql_data .=" OR b.po_id in(".implode(',',$po_row).")";
			$p++;
			}
			$sql_data .=")";
			$sql_data .=" group by a.batch_no,a.batch_against,a.entry_form,a.id,c.process_end_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no)";	*/
		}

		else if ($batch_type==3) // Sample
		{
			$sql_data="SELECT a.id,a.batch_no,a.entry_form,a.floor_id,a.save_string,a.batch_against,sum(b.batch_qnty) as batch_qty, c.process_end_date as batch_date,a.batch_weight,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.total_trims_weight,a.color_range_id from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess c
			where a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and a.batch_against=3 and c.entry_form=35  and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.entry_form=0 $batch_no_cond $floor_num  $date_cond $booking_no_cond $workingCompany_cond  $po_cond_for_in 
			group by a.batch_no,a.floor_id,a.batch_against,a.entry_form,a.save_string,a.id,c.process_end_date,a.batch_weight,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.po_id,b.width_dia_type,a.total_trims_weight,a.color_range_id";
		}
		else // All
		{
			// Self
			 $self_sql_data="(SELECT a.id,a.batch_no,a.floor_id,a.save_string,a.entry_form,a.batch_against,sum(b.batch_qnty) as batch_qty, count(b.id) as no_of_roll, c.process_end_date as batch_date,a.batch_weight,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,a.total_trims_weight,a.color_range_id  
			from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess c 
			where  a.id=b.mst_id  and c.batch_id=a.id  and c.batch_id=b.mst_id and c.entry_form=35 and a.batch_against !=3 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $batch_no_cond $floor_num  $date_cond $booking_no_cond $workingCompany_cond $type_cond $po_cond_for_in  group by a.batch_no,a.batch_against,a.entry_form,a.id,a.floor_id,a.save_string,c.process_end_date,a.batch_weight,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.po_id,b.width_dia_type,a.total_trims_weight,a.color_range_id)";
			/*$p=1;
			foreach($self_po_id_cond_split as $po_row)
			{
			if($p==1) $self_sql_data .=" and (b.po_id  in(".implode(',',$po_row).")"; else  $self_sql_data .=" OR b.po_id in(".implode(',',$po_row).")";
			$p++;
			}
			$self_sql_data .=")";
			$self_sql_data .=" group by a.batch_no,a.batch_against,a.entry_form,a.id,c.process_end_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no)";*/	


			// Subcon
			$subc_sql_data="(SELECT a.id,a.batch_no,a.floor_id,a.entry_form,a.batch_against,(b.batch_qnty) as batch_qty, c.process_end_date as batch_date,a.batch_weight,b.prod_id,b.po_id,b.gsm,b.width_dia_type,b.item_description,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,a.total_trims_weight,a.color_range_id 
			from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess c 
			where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and c.entry_form=38 and a.batch_against !=3 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $batch_no_cond $floor_num $date_cond $booking_no_cond $workingCompany_cond $type_cond  $sub_po_cond_for_in) order by a.id";
			/*$p=1;
			foreach($subc_po_id_cond_split as $po_row)
			{
			if($p==1) $subc_sql_data .=" and (b.po_id  in(".implode(',',$po_row).")"; else  $subc_sql_data .=" OR b.po_id in(".implode(',',$po_row).")";
			$p++;
			}
			$subc_sql_data .=")";
			$subc_sql_data .=" group by a.batch_no,a.batch_against,a.entry_form,a.id,c.process_end_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no)";*/

			// Sample
			  $samp_sql_data="SELECT a.id,a.batch_no,a.floor_id,a.save_string,a.entry_form,a.batch_against,sum(b.batch_qnty) as batch_qty, c.process_end_date as batch_date,a.batch_weight,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.total_trims_weight,a.color_range_id from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess c
			where a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and a.batch_against=3 and c.entry_form=35 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.entry_form=0 $batch_no_cond $floor_num $date_cond $booking_no_cond $workingCompany_cond $po_cond_for_in
			group by a.batch_no,a.batch_against,a.floor_id,a.entry_form,a.save_string,a.id,c.process_end_date,a.batch_weight,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.po_id,b.width_dia_type,a.total_trims_weight,a.color_range_id 
			union
			SELECT a.id,a.batch_no,a.floor_id,a.save_string,a.entry_form,a.batch_against,sum(b.batch_qnty) as batch_qty, c.process_end_date as batch_date,a.batch_weight,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,a.total_trims_weight,a.color_range_id from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess c
			where a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and a.batch_against=3 and c.entry_form=35  and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.entry_form=0 and b.po_id=0 $batch_no_cond $floor_num  $date_cond $booking_no_cond $workingCompany_cond  
			group by a.batch_no,a.batch_against,a.floor_id,a.entry_form,a.save_string,a.id,c.process_end_date,a.batch_weight,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.po_id,b.width_dia_type,a.total_trims_weight,a.color_range_id 
			";
			
			$sales_sql_data="(SELECT a.id,a.batch_no,a.floor_id,a.save_string,a.entry_form,a.batch_against,sum(b.batch_qnty) as batch_qty, c.process_end_date as batch_date,a.batch_weight,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,a.sales_order_no,a.sales_order_id,d.within_group,d.sales_booking_no,d.buyer_id,d.po_buyer,d.po_job_no, a.total_trims_weight,a.color_range_id 
			from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess c,fabric_sales_order_mst d 
			where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and d.id=a.sales_order_id  and c.entry_form=35 and a.batch_against !=3 and a.is_sales=1 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $batch_no_cond $floor_num  $date_cond $booking_no_cond $workingCompany_cond $type_cond group by a.batch_no,a.floor_id,a.batch_against,a.entry_form,a.save_string,a.id,c.process_end_date,a.batch_weight,a.color_id,a.style_ref_no,a.booking_no,a.sales_order_no,a.sales_order_id,a.extention_no,b.prod_id,b.po_id,b.width_dia_type,d.sales_booking_no,d.buyer_id,d.po_buyer,d.within_group,d.po_job_no,a.total_trims_weight,a.color_range_id)";
		}
		//echo $sql_data;
	}
	//echo $sql_data;
	if ($batch_type==0) 
	{
		///echo $self_sql_data;
		//echo $samp_sql_data;
		$self_nameArray=sql_select($self_sql_data);
		$subc_nameArray=sql_select($subc_sql_data);
		$samp_nameArray=sql_select($samp_sql_data);
		$sales_nameArray=sql_select($sales_sql_data);
		$batch_wise_arr=array();$booking_no_type_check=array('SM','SMN');

		foreach($self_nameArray as $row)
	    {
		 	
			
			$booking_no=explode("-",$row[csf('booking_no')]);
			$booking_no_type=$booking_no[1];
						
			$batch_wise_arr[$row[csf('id')]]['batch_against']=$row[csf('batch_against')];
			$batch_wise_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$batch_wise_arr[$row[csf('id')]]['fabric_desc']=$row[csf('fabric_desc')];
			$batch_wise_arr[$row[csf('id')]]['id']=$row[csf('id')];
			$batch_wise_arr[$row[csf('id')]]['entry_form']=$row[csf('entry_form')];
			$batch_wise_arr[$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
			$batch_wise_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$batch_wise_arr[$row[csf('id')]]['batch_qty']+=$row[csf('batch_qty')];
			$batch_wise_arr[$row[csf('id')]]['no_of_roll']+=$row[csf('no_of_roll')];
			$batch_wise_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
			$batch_wise_arr[$row[csf('id')]]['fabric_type'].=$row[csf('prod_id')].',';
			$batch_wise_arr[$row[csf('id')]]['batch_date']=$row[csf('batch_date')];
			$batch_wise_arr[$row[csf('id')]]['batch_weight']=$row[csf('batch_weight')];
			$batch_wise_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
			$batch_wise_arr[$row[csf('id')]]['color_range_id']=$row[csf('color_range_id')];
			$batch_wise_arr[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
			$batch_wise_arr[$row[csf('id')]]['po_id'].=$row[csf('po_id')].',';
			$batch_wise_arr[$row[csf('id')]]['width_dia_type'].=$row[csf('width_dia_type')].',';
			$batch_wise_arr[$row[csf('id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
			$batch_wise_arr[$row[csf('id')]]['save_string']=$row[csf('save_string')];
		}

		foreach($sales_nameArray as $row)
	    {
		 	
			
			$booking_no=explode("-",$row[csf('booking_no')]);
			$booking_no_type=$booking_no[1];
			//,a.sales_order_no,a.sales_order_id c.within_group,c.sales_booking_no,c.buyer_id,c.po_buyer,c.po_job_no
			if($row[csf('within_group')]==2)
			{
				//echo $buyer_library[$row[csf('buyer_id')]].'XZZZ';
				$sales_batch_wise_arr[$row[csf('id')]]['buyer']=$buyer_library[$row[csf('buyer_id')]];
			}
			else {
				$sales_batch_wise_arr[$row[csf('id')]]['buyer']=$company_library[$row[csf('buyer_id')]];
			}
			$sales_batch_wise_arr[$row[csf('id')]]['batch_against']=$row[csf('batch_against')];
			$sales_batch_wise_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$sales_batch_wise_arr[$row[csf('id')]]['fabric_desc']=$row[csf('fabric_desc')];
			$sales_batch_wise_arr[$row[csf('id')]]['id']=$row[csf('id')];
			$sales_batch_wise_arr[$row[csf('id')]]['entry_form']=$row[csf('entry_form')];
			$sales_batch_wise_arr[$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
			$sales_batch_wise_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$sales_batch_wise_arr[$row[csf('id')]]['batch_qty']+=$row[csf('batch_qty')];
			$sales_batch_wise_arr[$row[csf('id')]]['sales_order_id']=$row[csf('sales_order_id')];
			$sales_batch_wise_arr[$row[csf('id')]]['booking_no']=$row[csf('sales_order_no')];
			$sales_batch_wise_arr[$row[csf('id')]]['fabric_type'].=$row[csf('prod_id')].',';
			$sales_batch_wise_arr[$row[csf('id')]]['batch_date']=$row[csf('batch_date')];
			$sales_batch_wise_arr[$row[csf('id')]]['batch_weight']=$row[csf('batch_weight')];
			$sales_batch_wise_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
			$sales_batch_wise_arr[$row[csf('id')]]['color_range_id']=$row[csf('color_range_id')];
			$sales_batch_wise_arr[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
			$sales_batch_wise_arr[$row[csf('id')]]['po_id'].=$row[csf('po_id')].',';
			$sales_batch_wise_arr[$row[csf('id')]]['width_dia_type'].=$row[csf('width_dia_type')].',';
			$sales_batch_wise_arr[$row[csf('id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
			$sales_batch_wise_arr[$row[csf('id')]]['save_string']=$row[csf('save_string')];
		}
		foreach($subc_nameArray as $row)
	    {
		 
			
			$booking_no=explode("-",$row[csf('booking_no')]);
			$booking_no_type=$booking_no[1];
					//b.prod_id,b.po_id,b.gsm,b.width_dia_type,b.item_description
			$sub_batch_wise_arr[$row[csf('id')]]['batch_against']=$row[csf('batch_against')];
			$sub_batch_wise_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$sub_batch_wise_arr[$row[csf('id')]]['item_description'].=$row[csf('item_description')].',';
			$sub_batch_wise_arr[$row[csf('id')]]['gsm'].=$row[csf('gsm')].',';
			$sub_batch_wise_arr[$row[csf('id')]]['fin_dia']=$row[csf('fin_dia')];
			$sub_batch_wise_arr[$row[csf('id')]]['id']=$row[csf('id')];
			$sub_batch_wise_arr[$row[csf('id')]]['entry_form']=$row[csf('entry_form')];
			$sub_batch_wise_arr[$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
			$sub_batch_wise_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$sub_batch_wise_arr[$row[csf('id')]]['batch_qty']+=$row[csf('batch_qty')];
			$sub_batch_wise_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
			$sub_batch_wise_arr[$row[csf('id')]]['fabric_type'].=$row[csf('prod_id')].',';
			$sub_batch_wise_arr[$row[csf('id')]]['batch_date']=$row[csf('batch_date')];
			$sub_batch_wise_arr[$row[csf('id')]]['batch_weight']=$row[csf('batch_weight')];
			$sub_batch_wise_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
			$sub_batch_wise_arr[$row[csf('id')]]['color_range_id']=$row[csf('color_range_id')];
			$sub_batch_wise_arr[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
			$sub_batch_wise_arr[$row[csf('id')]]['po_id'].=$row[csf('po_id')].',';
			$sub_batch_wise_arr[$row[csf('id')]]['width_dia_type'].=$row[csf('width_dia_type')].',';
			$sub_batch_wise_arr[$row[csf('id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
		}
		foreach($samp_nameArray as $row)
	    {
		 	//$row[csf('po_id')]= $row[csf('po_id')]->load();
			//$row[csf('width_dia_type')]= $row[csf('width_dia_type')]->load();
			//$row[csf('gsm')]= $row[csf('gsm')]->load();
			//$row[csf('fabric_type')]= $row[csf('fabric_type')]->load();
			
			$booking_no=explode("-",$row[csf('booking_no')]);
			$booking_no_type=$booking_no[1];
						
			$samp_batch_wise_arr[$row[csf('id')]]['batch_against']=$row[csf('batch_against')];
			$samp_batch_wise_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$samp_batch_wise_arr[$row[csf('id')]]['fabric_desc']=$row[csf('fabric_desc')];
			$samp_batch_wise_arr[$row[csf('id')]]['id']=$row[csf('id')];
			$samp_batch_wise_arr[$row[csf('id')]]['entry_form']=$row[csf('entry_form')];
			$samp_batch_wise_arr[$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
			$samp_batch_wise_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$samp_batch_wise_arr[$row[csf('id')]]['batch_qty']+=$row[csf('batch_qty')];
			$samp_batch_wise_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
			$samp_batch_wise_arr[$row[csf('id')]]['fabric_type'].=$row[csf('prod_id')].',';
			$samp_batch_wise_arr[$row[csf('id')]]['batch_date']=$row[csf('batch_date')];
			$samp_batch_wise_arr[$row[csf('id')]]['batch_weight']=$row[csf('batch_weight')];
			$samp_batch_wise_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
			$samp_batch_wise_arr[$row[csf('id')]]['color_range_id']=$row[csf('color_range_id')];
			$samp_batch_wise_arr[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
			$samp_batch_wise_arr[$row[csf('id')]]['po_id'].=$row[csf('po_id')].',';
			$samp_batch_wise_arr[$row[csf('id')]]['width_dia_type'].=$row[csf('width_dia_type')].',';
			$samp_batch_wise_arr[$row[csf('id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
			$samp_batch_wise_arr[$row[csf('id')]]['save_string']=$row[csf('save_string')];
		}
	}
	else 
	{
	 	//echo $sql_data.'DDDS';
		$nameArray=sql_select($sql_data);
		$batch_wise_arr=array();$booking_no_type_check=array('SM','SMN');
		foreach($nameArray as $row)
	    {
		 	//$row[csf('po_id')]= $row[csf('po_id')]->load();
			//$row[csf('width_dia_type')]= $row[csf('width_dia_type')]->load();
			//$row[csf('gsm')]= $row[csf('gsm')]->load();
			//$row[csf('fabric_type')]= $row[csf('fabric_type')]->load();
			
			$booking_no=explode("-",$row[csf('booking_no')]);
			$booking_no_type=$booking_no[1];
			//	echo $booking_no_type.', ';
			if(($batch_type==3 && ($booking_no_type=='SM' || $booking_no_type=='SMN')))
			{
				
				$samp_batch_wise_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
				$samp_batch_wise_arr[$row[csf('id')]]['fabric_desc']=$row[csf('fabric_desc')];
				$samp_batch_wise_arr[$row[csf('id')]]['batch_against']=$row[csf('batch_against')];
				$samp_batch_wise_arr[$row[csf('id')]]['id']=$row[csf('id')];
				$samp_batch_wise_arr[$row[csf('id')]]['entry_form']=$row[csf('entry_form')];
				$samp_batch_wise_arr[$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
				$samp_batch_wise_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
				$samp_batch_wise_arr[$row[csf('id')]]['batch_qty']=$row[csf('batch_qty')];
				$samp_batch_wise_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
				$samp_batch_wise_arr[$row[csf('id')]]['fabric_type'].=$row[csf('prod_id')].',';
				$samp_batch_wise_arr[$row[csf('id')]]['batch_date']=$row[csf('batch_date')];
				$samp_batch_wise_arr[$row[csf('id')]]['batch_weight']=$row[csf('batch_weight')];
				$samp_batch_wise_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
				$samp_batch_wise_arr[$row[csf('id')]]['color_range_id']=$row[csf('color_range_id')];
				$samp_batch_wise_arr[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
				$samp_batch_wise_arr[$row[csf('id')]]['po_id'].=$row[csf('po_id')].',';
				$samp_batch_wise_arr[$row[csf('id')]]['width_dia_type'].=$row[csf('width_dia_type')].',';
				$samp_batch_wise_arr[$row[csf('id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
				$samp_batch_wise_arr[$row[csf('id')]]['save_string']=$row[csf('save_string')];
			}

			else if(($batch_type==1) && (!in_array($booking_no_type,$booking_no_type_check)) )
			{
				//echo $booking_no_type.", ";
				$batch_wise_arr[$row[csf('id')]]['batch_against']=$row[csf('batch_against')];
				$batch_wise_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
				$batch_wise_arr[$row[csf('id')]]['fabric_desc']=$row[csf('fabric_desc')];
				$batch_wise_arr[$row[csf('id')]]['id']=$row[csf('id')];
				$batch_wise_arr[$row[csf('id')]]['entry_form']=$row[csf('entry_form')];
				$batch_wise_arr[$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
				$batch_wise_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
				$batch_wise_arr[$row[csf('id')]]['batch_qty']=$row[csf('batch_qty')];
				$batch_wise_arr[$row[csf('id')]]['no_of_roll']=$row[csf('no_of_roll')];
				$batch_wise_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
				$batch_wise_arr[$row[csf('id')]]['fabric_type'].=$row[csf('prod_id')].',';
				$batch_wise_arr[$row[csf('id')]]['batch_date']=$row[csf('batch_date')];
				$batch_wise_arr[$row[csf('id')]]['batch_weight']=$row[csf('batch_weight')];
				$batch_wise_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
				$batch_wise_arr[$row[csf('id')]]['color_range_id']=$row[csf('color_range_id')];
				$batch_wise_arr[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
				$batch_wise_arr[$row[csf('id')]]['po_id'].=$row[csf('po_id')].',';
				$batch_wise_arr[$row[csf('id')]]['width_dia_type'].=$row[csf('width_dia_type')].',';
				$batch_wise_arr[$row[csf('id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
				$batch_wise_arr[$row[csf('id')]]['save_string']=$row[csf('save_string')];
			}
			else if(($batch_type==4))
			{
				//echo $booking_no_type.", ";
				if($row[csf('within_group')]==2)
				{
					//echo $buyer_library[$row[csf('buyer_id')]].'XZZZ';
					$sales_batch_wise_arr[$row[csf('id')]]['buyer']=$buyer_library[$row[csf('buyer_id')]];
				}
				else {
					$sales_batch_wise_arr[$row[csf('id')]]['buyer']=$company_library[$row[csf('buyer_id')]];
				}
			
				$sales_batch_wise_arr[$row[csf('id')]]['batch_against']=$row[csf('batch_against')];
				$sales_batch_wise_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
				$sales_batch_wise_arr[$row[csf('id')]]['fabric_desc']=$row[csf('fabric_desc')];
				$sales_batch_wise_arr[$row[csf('id')]]['id']=$row[csf('id')];
				$sales_batch_wise_arr[$row[csf('id')]]['entry_form']=$row[csf('entry_form')];
				$sales_batch_wise_arr[$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
				$sales_batch_wise_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
				$sales_batch_wise_arr[$row[csf('id')]]['batch_qty']+=$row[csf('batch_qty')];
				$sales_batch_wise_arr[$row[csf('id')]]['sales_order_id']=$row[csf('sales_order_id')];
				$sales_batch_wise_arr[$row[csf('id')]]['booking_no']=$row[csf('sales_order_no')];
				$sales_batch_wise_arr[$row[csf('id')]]['fabric_type'].=$row[csf('prod_id')].',';
				$sales_batch_wise_arr[$row[csf('id')]]['batch_date']=$row[csf('batch_date')];
				$sales_batch_wise_arr[$row[csf('id')]]['batch_weight']=$row[csf('batch_weight')];
				$sales_batch_wise_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
				$sales_batch_wise_arr[$row[csf('id')]]['color_range_id']=$row[csf('color_range_id')];
				$sales_batch_wise_arr[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
				$sales_batch_wise_arr[$row[csf('id')]]['po_id'].=$row[csf('po_id')].',';
				$sales_batch_wise_arr[$row[csf('id')]]['width_dia_type'].=$row[csf('width_dia_type')].',';
				$sales_batch_wise_arr[$row[csf('id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
				$sales_batch_wise_arr[$row[csf('id')]]['save_string']=$row[csf('save_string')];
			}
			else if(($batch_type==2) && (!in_array($booking_no_type,$booking_no_type_check)) )
			{
				//echo $booking_no_type.", ";
				$sub_batch_wise_arr[$row[csf('id')]]['batch_against']=$row[csf('batch_against')];
				$sub_batch_wise_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
				//$sub_batch_wise_arr[$row[csf('id')]]['fabric_desc']=$row[csf('fabric_desc')];
				$sub_batch_wise_arr[$row[csf('id')]]['item_description'].=$row[csf('item_description')].',';
				$sub_batch_wise_arr[$row[csf('id')]]['gsm'].=$row[csf('gsm')].',';
				$sub_batch_wise_arr[$row[csf('id')]]['fin_dia']=$row[csf('fin_dia')];
				$sub_batch_wise_arr[$row[csf('id')]]['id']=$row[csf('id')];
				$sub_batch_wise_arr[$row[csf('id')]]['entry_form']=$row[csf('entry_form')];
				$sub_batch_wise_arr[$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
				$sub_batch_wise_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
				$sub_batch_wise_arr[$row[csf('id')]]['batch_qty']+=$row[csf('batch_qty')];
				$sub_batch_wise_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
				$sub_batch_wise_arr[$row[csf('id')]]['fabric_type'].=$row[csf('prod_id')].',';
				$sub_batch_wise_arr[$row[csf('id')]]['batch_date']=$row[csf('batch_date')];
				$sub_batch_wise_arr[$row[csf('id')]]['batch_weight']=$row[csf('batch_weight')];
				$sub_batch_wise_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
				$sub_batch_wise_arr[$row[csf('id')]]['color_range_id']=$row[csf('color_range_id')];
				$sub_batch_wise_arr[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
				$sub_batch_wise_arr[$row[csf('id')]]['po_id'].=$row[csf('po_id')].',';
				$sub_batch_wise_arr[$row[csf('id')]]['width_dia_type'].=$row[csf('width_dia_type')].',';
				$sub_batch_wise_arr[$row[csf('id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
			}
		}
	}
		 
	//echo "<pre>";print_r($sales_batch_wise_arr);
	
	?>
	<div>
	<table width="2580" cellspacing="0" cellpadding="0" border="0" rules="all" >
	    <tr class="form_caption">
	        <td colspan="30" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
	    </tr>
	    <tr class="form_caption">
	        <td colspan="30" align="center"><?  if($company_name!=0) echo $company_library[$company_name];else echo $company_library[$working_company]; ?><br>
	        </b>
	        <? //
	      //  echo  change_date_format($start_date).' '.To.' '.change_date_format($end_date);
			echo  ($start_date == '0000-00-00' || $start_date == '' ? '' : change_date_format($start_date)).' To ';echo  ($end_date == '0000-00-00' || $end_date == '' ? '' : change_date_format($end_date));
	        ?> </b>
	        </td>
	    </tr>
	</table>
    <?
	if($batch_type==0 || $batch_type==1 ) // Self Start
	{
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="3310" class="rpt_table" id="table_header_1">
			<caption> <b style=" float:left"><? if($batch_type>0) echo $batch_type_arr[$batch_type];else echo "Self Batch";?></b></caption>
			<thead>
				<th width="30">SL</th>
				<th width="80"><? if($cbo_search_date==1){ echo "Batch Date";}else{echo "Dyeing Date";} ?></th>
				<th width="80">Batch No</th>
				<th width="60">Ext.No</th>
				<th width="80">Batch Against</th>
				<th width="90">Color Name</th>
				<th width="100">Color Range</th>
				<th width="110">Buyer</th>
				<th width="80">Style Ref</th>
				<th width="80">Job No</th>
				<th width="110">F.Booking No</th>
				<th width="110">Order No</th>
				<th width="70">File No</th>
				<th width="70">Ref. No</th>
				<th width="150">Fabrics Type</th>
				<th width="50">GSM</th>
				<th width="60"><p>Dia/ Width Type</p></th>
	            <th width="70"><p>Batch Floor</p></th>
				<th width="70"><p>HeatSetting / Singeing</p></th>
                <th width="100">Trims Description.</th>
                
				<th width="80">Trims Weight</th>
				<th width="80">Batch Qty.</th>
				<th width="50">No. of Roll</th>
	            <th width="70"><p>Prod. Floor</p></th>
				<th width="100">M/C No</th>
				<th width="80">Dyeing Loding</th>
				<th width="70"><p>Dyeing Un-Loding</p></th>
				<th width="80">Un-Loding Result</th>
				<th width="60"><p>Slitting / Squeezing</p></th>
				<th width="80">Stentering</th>
				<th width="80">Compacting</th>
				<th width="80">Drying</th>
				<th width="80">Special Finish</th>
				<th width="80"><p>Fin.Fab.Prod. Entry<p></th>
				<th width="80"><p>Process Loss Qty<p></th>
				<th width="60"><p>Process Loss %<p></th>
				<th width="100"><p>Fin.Fab.Delivery to Store</p></th>
				<th width="100"><p>Delivery Date</p></th>
				<th width="80"><p>Delivery Balance Qty.</p></th>
				<th width="">Batch Status</th>
			</thead>
		</table>
		<div style="width:3310px; overflow-y:scroll; max-height:450px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="3290" class="rpt_table" id="table_body">
				<?
			    $i=1;
				$roll_arr=array();
				$total_batch_qty=$total_finish_qty=$total_process_loss_qty=$total_delivery_qty=$total_balance_qty=$total_load_deying_qty=$total_unload_deying_qty=$total_slitting_qty=$total_stentering_qty=$total_compacting_qty=$total_drying_qty=$total_special_qty=$total_heatset_qty=0;
				$total_trims_weight_gt = 0;
				foreach($batch_wise_arr as $batch_id=>$row)
				{
				    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				    $batch_id=$row[('id')];
				    $batch_no=$row[('batch_no')];
					if($row[('save_string')])
					{
						$save_stringData=array_unique(explode("!!",$row[('save_string')]));
						$trim_wgt_desc='';
						foreach($save_stringData as $desData)
						{
							$desc_stringArr=array_unique(explode("_",$desData));
							$desc_string=$desc_stringArr[0];
							if($trim_wgt_desc=='') $trim_wgt_desc=$desc_string;else $trim_wgt_desc.=",".$desc_string;
						}
					}
				    $booking_no=$row[('booking_no')];
					$fabric_type=rtrim($row[('fabric_type')],',');
					$fabric_desc=array_unique(explode(",",$fabric_type));
					$fabric_desc_arr=''; $fabric_gsm_arr='';
					$fab='';
				    foreach($fabric_desc as $pid)
				    {
				       // $fabdesc_type=explode(",",$desc);
					   $fab_desc=$prod_detail_arr[$pid];	
						if($fab=='')
						{
							$fabdesc_type=explode(",",$fab_desc);
							$fab=$fabdesc_type[0].",".$fabdesc_type[1];	
							$fab_gsm=$prod_detail_gsm_arr[$pid];	
						}
						else
						{
							$fabdesc_type=explode(",",$fab_desc);
							$fab.="<br>".$fabdesc_type[0].",".$fabdesc_type[1];
							$fab_gsm.=", ".$prod_detail_gsm_arr[$pid];
						}
				    }//print  $fabric_desc_arr;//
				    //$desc=implode(',',explode(",",$fabric_desc_arr));
					$po_id=rtrim($row[('po_id')],',');
				    $po_numbers=""; $job_no=""; $buyer=""; $file=""; $refNo="";$buyer_style="";
				    $po_id=array_unique(explode(",",$po_id));
				    foreach($po_id as $id)
				    {
						if($row[('entry_form')]==36) //SubCon
						{
							//if($po_number=="") $po_number=$sub_job_array[$id]['po']; else $po_number.=",".$sub_job_array[$id]['po'];
							//if($job_no=="") $job_no=$sub_job_array[$id]['job']; else $job_no.=",".$sub_job_array[$id]['job'];
							//if($buyer=="") $buyer=$buyer_library[$sub_job_array[$id]['buyer']]; else $buyer.=",".$buyer_library[$sub_job_array[$id]['buyer']];
						}
						else
						{
							if($po_numbers=="") $po_numbers=$job_array[$id]['po']; else $po_numbers.=",".$job_array[$id]['po'];
							if($job_no=="") $job_no=$job_array[$id]['job']; else $job_no.=",".$job_array[$id]['job'];
							if($buyer=="") $buyer=$buyer_library[$job_array[$id]['buyer']]; else $buyer.=",".$buyer_library[$job_array[$id]['buyer']];
							if($file=="") $file=$job_array[$id]['file']; else $file.=",".$job_array[$id]['file']; // file
							if($refNo=="") $refNo=$job_array[$id]['refNo']; else $refNo.=",".$job_array[$id]['refNo']; // ref
							if($buyer_style=="") $buyer_style=$job_array[$id]['style_no']; else $buyer_style.=','.$job_array[$id]['style_no'];
						}
					
				    }
					//echo $buyer.'sssss';
				    $job=implode(',',array_unique(explode(",",$job_no)));
					$po_numbers=implode(', ',array_unique(explode(",",$po_numbers)));
				    $buyer_name=implode(',',array_unique(explode(",",$buyer)));
					
					$file_no=implode(',',array_unique(explode(",",$file)));
					$refNo_no=implode(',',array_unique(explode(",",$refNo)));
				    
				    $dia_type='';  $width_dia_type=rtrim($row[('width_dia_type')],',');
				    $dia_type_id=array_unique(explode(",",$width_dia_type));
				    foreach($dia_type_id as $dia_id)
				    {	
				      if($dia_type=="") $dia_type=$fabric_typee[$dia_id]; else $dia_type.=",".$fabric_typee[$dia_id];
				    }
				    $dia_type_data=implode(',',array_unique(explode(",",$dia_type)));
					 $result=$unloading_data[$batch_id]['result'];
					 $machine=$unloading_data[$batch_id]['machine'];
					// $remark=$unloading_data[$batch_id]['remarks'];
					 $gsm=$finish_data_arr[$batch_id]['gsm'];
					//print_r( $result);
				    $heat_qty=$heat_setting_arr[$batch_id]['qty'];
					$load_deying_qty=$loading_data_arr[$batch_id]['load'];
					$unload_deying_qty=$loading_data_arr[$batch_id]['unload'];
					//$loading_data_arr[$batch_id]['load_qty']=$row_dyeing[csf('deying_load_id')];
					//$loading_data_arr[$batch_id]['unload_qty']=$row_dyeing[csf('deying_unload_id')];
					
					$shade_macth=$loading_data_arr[$batch_id]['result'];//$loading_data_arr[$batch_id]['result'];
					$slitting_qty=$slitting_arr[$batch_id]['slitting'];
					$stentering_qty=$stentering_arr[$batch_id]['stentering'];
					$compacting_qty=$compacting_arr[$batch_id]['compact'];
					$drying_qty=$drying_arr[$batch_id]['drying'];
					$special_qty=$special_arr[$batch_id]['special'];
					$finish_qty=$finish_data_arr[$batch_id]['finish_qty'];
					$process_loss_qty=$row[('batch_qty')]-$finish_qty;
					$process_loss_qty_percent=($process_loss_qty/$row[('batch_qty')])*100;
					$delivery_qty=$delivery_data_arr[$batch_id]['delivery'];
					$total_delivery_balance_qty=$finish_qty-$delivery_qty;
					$batch_status_id=$finish_data_status[$batch_id]['batch_status'];//$finish_data_arr[$batch_id]['batch_status'];
					//$finish_data_status[$row_st[csf('batch_id')]]['batch_status']
				    //echo $po_id;		
				    $delivery_dates=$delivery_data_arr[$batch_id]['delivery_date'];
				    $delivery_dates=implode(",", array_unique(explode(",", $delivery_dates)));
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trself_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trself_<? echo $i; ?>"> 
					    <td width="30"><? echo $i; ?></td>
					    <td width="80"><p><? echo change_date_format($row[('batch_date')]); ?></p></td>
					    <td width="80" onclick="print_batch_card(<?=$batch_id?>, '<?=$row[('batch_no')]?>');"  title="Batch ID=<? echo $batch_id;?>"><p><a href="##"><? echo $row[('batch_no')]; ?></a></p></td>
					    <td width="60"><p><? echo $row[('extention_no')]; ?></p></td>
						 <td width="80"><p><? echo $batch_against[$row[('batch_against')]]; ?></p></td>
						 <td width="90"><p><? echo $color_library[$row[('color_id')]]; ?></p></td>
						 <td width="100"><p><? echo $color_range[$row[('color_range_id')]]; ?></p></td>
					   
					    <td width="110"><p><? echo $buyer_name; ?></p></td>
					    <td width="80"><p><? echo $buyer_style; ?></p></td>
					    <td width="80"><p><? echo $job; ?></p></td>
					    <td width="110"><p><? echo $row[('booking_no')]; ?></p></td>
					    <td width="110" style=""><div style="word-wrap:break-word;width:110px"><? echo $po_numbers; ?></div></td>
					    
					    <td width="70"><p><? echo $file_no; ?></p></td>
					    <td width="70"><p><? echo $refNo_no; ?></p></td>
					    
					    <td width="150"><p><? echo $fab;?></p></td>
					    <td style="word-break:break-word; width:50px"><p><? echo  $fab_gsm;?></p></td>
					    <td width="60"><p><? echo $dia_type_data; ?></p></td>
					    <td width="70" align="right"><p><? echo $floor_library[$row[('floor_id')]];  ?></p></td>
					    <td width="70" align="right"><p><? echo number_format($heat_qty,2);  ?></p></td>

					    <td width="100" align="center"><p><?  echo $trim_wgt_desc; ?></p></td>
				        <td width="80" align="right"><p><?  echo number_format($row[('total_trims_weight')],2); ?></p></td>
					    <td width="80" align="right"><p><?  echo number_format($row[('batch_qty')],2); ?></p></td>
					    <td width="50" align="right"><p><?  echo $row[('no_of_roll')]; ?></p></td>
					    <td width="70" align="right"><p><?  echo $floor_library[$unloading_data[$batch_id]['floor_id']]; ?></p></td>
					    <td width="100" title="Machine"><p><?  echo $machine_arr[$machine]; ?></p></td>
					    <td width="80" align="right"><p><? echo number_format($load_deying_qty,2); ?></p></td>
					    <td width="70" align="right"><p><? echo number_format($unload_deying_qty,2); ?></p></td>
					    <td align="right" width="80"><p><? echo $dyeing_result[$result]; ?></p></td>
					    <td align="right" width="60"><p><? echo number_format($slitting_qty,2); ?></p></td>
					    <td align="right" width="80"><p> <? echo number_format($stentering_qty,2); ?></p></td>
					    <td align="right" width="80"><p> <? echo number_format($compacting_qty,2); ?></p></td>
					    <td width="80" align="right"><? echo number_format($drying_qty,2); ?> </td>
					    <td width="80" align="right"><? echo number_format($special_qty,2,'.',''); ?></td>
					    <td width="80" align="right"><p><? echo number_format($finish_qty,2); ?></p></td>
					    <td width="80" align="right"><p><? echo number_format($process_loss_qty,2); ?></p></td>
					    <td width="60" align="right"><p><? echo number_format($process_loss_qty_percent,2); ?></p></td>
					    <td width="100" align="right"><p><? echo number_format($delivery_qty,2); ?></p></td>
					    <td width="100" align="right"><p><? echo chop($delivery_dates,","); ?></p></td>
					    <td width="80" align="right"><p><? echo number_format($total_delivery_balance_qty,2); ?></p></td>
					    <td><p><? echo  $batch_status_array[$batch_status_id]; ?></p></td>
					</tr>
				    <?
					$total_batch_qty+=$row[('batch_qty')];
					$total_trims_weight_gt+=$row[('total_trims_weight')];
					$total_load_deying_qty+=$load_deying_qty;
					$total_unload_deying_qty+=$unload_deying_qty;
					$total_heatset_qty+=$heat_qty;
					$total_slitting_qty+=$slitting_qty;
					$total_stentering_qty+=$stentering_qty;
					$total_compacting_qty+=$compacting_qty;
					$total_drying_qty+=$drying_qty;
					$total_special_qty+=$special_qty;
					$total_finish_qty+=$finish_qty;
					$total_process_loss_qty+=$process_loss_qty;
					$total_delivery_qty+=$delivery_qty;
					$total_balance_qty+=$total_delivery_balance_qty;
					
				    $i++;
			    }
			    ?>
			</table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="3290" class="rpt_table" id="report_table_footer">
				<tfoot>
				    <th width="30"></th>
				    <th width="80"></th>
				    <th width="80"></th>
				    <th width="60"></th>
					<th width="80"></th>
				    <th width="90"></th>
				    <th width="100"></th>
				    <th width="110"></th>
				    <th width="80"></th>
				    <th width="80"></th>
				    <th width="110"></th>
				    <th width="110"></th>    
				    <th width="70"></th>
				    <th width="70"></th>    
				    <th width="150"></th>
				    <th width="50"></th>
				    <th width="60"></th>
			        <th width="70">Grand Total</th>
			        <th width="70"><? echo number_format($total_heatset_qty,2,'.',''); ?></th>
		            <th width="100"><? //echo number_format($total_heatset_qty,2,'.',''); ?></th>
				    <th width="80" id="total_trims_weight_gt"><? echo number_format($total_trims_weight_gt,2,'.',''); ?></th>
				    <th width="80" id="total_batch_qty"><? echo number_format($total_batch_qty,2,'.',''); ?></th>
			        <th width="50"></th>
			        <th width="70"></th>
				    <th width="100"></th>
				    <th width="80" id="total_load_qty"><? echo number_format($total_load_deying_qty,2,'.',''); ?></th>
				    <th width="70" id="total_unload_qty"><? echo number_format($total_unload_deying_qty,2,'.',''); ?></th>
				    <th width="80"></th>
				    <th width="60" id="total_sliting_qty"><? echo number_format($total_slitting_qty,2,'.',''); ?></th>
				    <th width="80" id="total_stenter_qty"><? echo number_format($total_stentering_qty,2,'.',''); ?></th>
				    <th width="80" id="total_compect_qty"><? echo number_format($total_compacting_qty,2,'.',''); ?></th>
				    <th width="80"  id="total_drying_qty"><? echo number_format($total_drying_qty,2,'.',''); ?></th>
				    <th width="80" id="total_special_qty"><? echo number_format($total_special_qty,2,'.',''); ?></th>
				    <th width="80" id="total_finish_qty"><? echo number_format($total_finish_qty,2); ?></th>
				    <th width="80" id="total_process_loss_qty"><? echo number_format($total_process_loss_qty,2); ?></th>
				    <th width="60"></th>
				   	<th width="100" id="total_delivery_qty"><? echo number_format($total_delivery_qty,2); ?></th>
				   	<th width="100"></th>
				   	<th width="80" id="total_balance_qty"><? echo number_format($total_balance_qty,2); ?></th>
				   	<th width=""></th>
				</tfoot>
			</table>
		</div>
	    <?
	} // Self End
	?>
	<br>
	<? 
	if($batch_type==0 || $batch_type==2 ) // subcon Start
	{
		?>
	    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="3000" class="rpt_table" id="table_header_1">
			<caption> <b style=" float:left"><? if($batch_type>0) echo $batch_type_arr[$batch_type];else echo "SubCon Batch";?></b></caption>
			<thead>
				<th width="40">SL</th>
				<th width="80"><? if($cbo_search_date==1){ echo "Batch Date";}else{echo "Dyeing Date";} ?></th>
				<th width="80">Batch No</th>
				<th width="60">Ext.No</th>

				<th width="80">Batch Against</th>
				<th width="90">Color Name</th>
				<th width="100">Color Range</th>
				<th width="110">Buyer</th>

				<th width="80">Style Ref</th>
				<th width="80">Job No</th>
				<th width="110">F.Booking No</th>
				<th width="70">Order No</th>

				<th width="70">File No</th>
				<th width="70">Ref. No</th>
				<th width="150">Fabrics Type</th>
				<th width="50">GSM</th>

				<th width="60"><p>Dia/Width Type</p></th>
				<th width="70"><p>HeatSetting/Singeing</p></th>
	            <th width="70"><p>Batch Floor</p></th>
				<th width="80">Trims Weight</th>

				<th width="80">Batch Qty.</th>
	            <th width="70"><p>Prod. Floor</p></th>
	            <th width="100">M/C No</th>
				<th width="80">Dyeing Loding</th>

				<th width="70"><p>Dyeing Un-Loding</p></th>
				<th width="80">Un-Loding Result</th>
				<th width="60"><p>Slitting/Squeezing</p></th>
				<th width="80">Stentering</th>

				<th width="80">Compacting</th>
				<th width="80">Drying</th>
				<th width="80">Special Finish</th>
				<th width="80"><p>Fin.Fab.Prod. Entry<p></th>

				<th width="80"><p>Process Loss Qty<p></th>
				<th width="60"><p>Process Loss %<p></th>
				<th width="100"><p>Fin.Fab.Delivery to Store</p></th>
				<th width="80"><p>Delivery Balance Qty.</p></th>
				<th width="">Batch Status</th>
			</thead>
		</table>
		 <div style="width:3020px; overflow-y:scroll; max-height:450px;" id="scroll_body">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="3000" class="rpt_table" id="table_body2">

		<?	

	    $i=1;  $roll_arr=array();$total_batch_qty=$total_finish_qty=$total_delivery_qty=$total_balance_qty=$total_load_deying_qty=$total_unload_deying_qty=$total_slitting_qty=$total_stentering_qty=$total_compacting_qty=$total_drying_qty=$total_special_qty=$total_process_loss_qty=$total_heat_qty=0;
		$total_trims_weight_gt = 0;
	     foreach($sub_batch_wise_arr as $batch_id=>$row)
	     {
	     if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	     $batch_id=$row[('id')];
	     $batch_no=$row[('batch_no')];
	     $booking_no=$row[('booking_no')];
		$item_desc=rtrim($row[('item_description')],',');
		
		$fabric_gsm=rtrim($row[('gsm')],',');
		// echo  $item_desc.', ';	
		$fabric_desc=implode(",",array_unique(explode(",",$item_desc)));
		$fab_gsm=implode(",",array_unique(explode(",",$fabric_gsm)));
		//$dia_type_data=implode(",",array_unique(explode("**",$fabric_fin_dia)));
		//$fabric_desc_arr=''; $fabric_gsm_arr='';
		/*$fab='';
	    foreach($fabric_desc as $pid)
	    {
	       // $fabdesc_type=explode(",",$desc);
		   $fab_desc=$prod_detail_arr[$pid];
		  // echo  $fab_desc.', ';	
			if($fab=='')
			{
				$fabdesc_type=$fab_desc;//explode(",",$fab_desc);
			//	$fab=$fabdesc_type[0].",".$fabdesc_type[1];	
				//$fabric_gsm=$prod_detail_gsm_arr[$pid];	
			}
			else
			{
				$fabdesc_type.=",".$fab_desc;
				//$fab.="<br>".$fabdesc_type[0].",".$fabdesc_type[1];
				//$fabric_gsm.=", ".$prod_detail_gsm_arr[$pid];
			}
	    }*////print  $fabric_desc_arr;//
	    //$desc=implode(',',explode(",",$fabric_desc_arr));
	    $po_number=''; $job_no=""; $buyer=""; $file=""; $refNo="";$sub_buyer_style="";
		$po_ids=rtrim($row[('po_id')],',');
		//echo $po_ids.'FDD';
	    $po_id=array_unique(explode(",",$po_ids));
	    foreach($po_id as $id)
	    {
			if($row[('entry_form')]==36) //SubCon
			{
				if($po_number=="") $po_number=$sub_job_array[$id]['po']; else $po_number.=", ".$sub_job_array[$id]['po'];
				if($job_no=="") $job_no=$sub_job_array[$id]['job']; else $job_no.=",".$sub_job_array[$id]['job'];
				if($buyer=="") $buyer=$buyer_library[$sub_job_array[$id]['buyer']]; else $buyer.=",".$buyer_library[$sub_job_array[$id]['buyer']];
				if($sub_buyer_style=="") $sub_buyer_style=$sub_job_array[$id]['style_no']; else $sub_buyer_style.=','.$sub_job_array[$id]['style_no'];
				/*echo $sub_buyer_style;*/
			}
			else
			{
				if($po_number=="") $po_number=$job_array[$id]['po']; else $po_number.=", ".$job_array[$id]['po'];
				if($job_no=="") $job_no=$job_array[$id]['job']; else $job_no.=",".$job_array[$id]['job'];
				if($buyer=="") $buyer=$buyer_library[$job_array[$id]['buyer']]; else $buyer.=",".$buyer_library[$job_array[$id]['buyer']];
				if($file=="") $file=$job_array[$id]['file']; else $file.=",".$job_array[$id]['file']; // file
				if($refNo=="") $refNo=$job_array[$id]['refNo']; else $refNo.=",".$job_array[$id]['refNo']; // ref
				if($sub_buyer_style=="") $sub_buyer_style=$job_array[$id]['style_no']; else $sub_buyer_style.=','.$job_array[$id]['style_no'];
				/*echo '2'.$sub_buyer_style;die;*/
			}
		
	    }
		//echo $buyer.'sssss';
	    $job=implode(',',array_unique(explode(",",$job_no)));
	    $buyer_name=implode(',',array_unique(explode(",",$buyer)));
		
		$file_no=implode(',',array_unique(explode(",",$file)));
		$sub_buyer_style_no=implode(',',array_unique(explode(",",$sub_buyer_style)));
		$refNo_no=implode(',',array_unique(explode(",",$refNo)));
	     $width_dia_type=rtrim($row[('width_dia_type')],',');
	    $dia_type='';$width_dia=rtrim($width_dia_type,',');
	    $dia_type_id=array_unique(explode(",",$width_dia));
	    /*echo $sub_buyer_style_no;die;*/
	    foreach($dia_type_id as $dia_id)
	    {	
	      if($dia_type=="") $dia_type=$fabric_typee[$dia_id]; else $dia_type.=",".$fabric_typee[$dia_id];
	    }
	    $dia_type_data=implode(',',array_unique(explode(",",$dia_type)));
		 $result=$unloading_data[$batch_id]['result'];
		 $machine=$unloading_data[$batch_id]['machine'];
		// $remark=$unloading_data[$batch_id]['remarks'];
		 $gsm=$finish_data_arr[$batch_id]['gsm'];
		//print_r( $result);
	    $heat_qty=$heat_setting_arr[$batch_id]['qty'];
		$load_deying_qty=$loading_data_arr[$batch_id]['load'];
		$unload_deying_qty=$loading_data_arr[$batch_id]['unload'];
		//$loading_data_arr[$batch_id]['load_qty']=$row_dyeing[csf('deying_load_id')];
		//$loading_data_arr[$batch_id]['unload_qty']=$row_dyeing[csf('deying_unload_id')];
		
		$shade_macth=$loading_data_arr[$batch_id]['result'];//$loading_data_arr[$batch_id]['result'];
		$slitting_qty=$slitting_arr[$batch_id]['slitting'];
		$stentering_qty=$stentering_arr[$batch_id]['stentering'];
		$compacting_qty=$compacting_arr[$batch_id]['compact'];
		$drying_qty=$drying_arr[$batch_id]['drying'];
		$special_qty=$special_arr[$batch_id]['special'];
		$finish_qty=$finish_data_arr[$batch_id]['finish_qty'];
		$process_loss_qty=$row[('batch_qty')]-$finish_qty;
		$process_loss_qty_percent=($process_loss_qty/$row[('batch_qty')])*100;
		$delivery_qty=$delivery_data_arr[$batch_id]['delivery'];
		$total_delivery_balance_qty=$finish_qty-$delivery_qty;
		$batch_status_id=$finish_data_status[$batch_id]['batch_status'];//$finish_data_arr[$batch_id]['batch_status'];
		//$finish_data_status[$row_st[csf('batch_id')]]['batch_status']
	    //echo $po_id;		
		?>
		<td width="80" onclick="print_batch_card(<?=$batch_id?>, '<?=$row[('batch_no')]?>');"  title="Batch ID=<? echo $batch_id;?>"><p><a href="##"><? echo $row[('batch_no')]; ?></a></p></td>


	 <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trsubcon_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trsubcon_<? echo $i; ?>"> 
	    <td width="40"><? echo $i; ?></td>
	    <td width="80"><p><? echo change_date_format($row[('batch_date')]); ?></p></td>
	    <!-- <td width="80" title="Batch ID=<? //echo $batch_id;?>"><p><? //echo $row[('batch_no')]; ?></p></td> -->
		<td width="80" onclick="print_batch_card(<?=$batch_id?>, '<?=$row[('batch_no')]?>');"  title="Batch ID=<? echo $batch_id;?>"><p><a href="##"><? echo $row[('batch_no')]; ?></a></p></td>
	    <td width="60"><p><? echo $row[('extention_no')]; ?></p></td>

		 <td width="80"><p><? echo $batch_against[$row[('batch_against')]]; ?></p></td>
		 <td width="90"><p><? echo $color_library[$row[('color_id')]]; ?></p></td>
		 <td width="100"><p><? echo $color_range[$row[('color_range_id')]]; ?></p></td>
	     <td width="110"><p><? echo $buyer_name; ?></p></td>

	    <td width="80"><p><? echo $sub_buyer_style; ?></p></td>
	    <td width="80"><p><? echo $job; ?></p></td>
	    <td width="110"><p><? echo $row[('booking_no')]; ?></p></td>
	    <td width="70"><div style="word-wrap:break-word;width:70px"><? echo $po_number; ?></div></td>
	    
	    <td width="70"><p><? echo $file_no; ?></p></td>
	    <td width="70"><p><? echo $refNo_no; ?></p></td>
	    <td width="150"><p><? echo $fabric_desc;?></p></td>
	    <td style="word-break:break-word; width:50px"><p><? echo  $fab_gsm;?></p></td>

	    <td width="60"><p><? echo $dia_type_data; ?></p></td>
	    <td width="70" align="right"><p><? echo number_format($heat_qty,2);  ?></p></td>
	    <td width="70" align="right"><p><? echo $floor_library[$row[('floor_id')]];  ?></p></td>
	    <td width="80" align="right"><p><?  echo number_format($row[('total_trims_weight')],2); ?></p></td>

	    <td width="80" align="right"><p><?  echo number_format($row[('batch_qty')],2); ?></p></td>
	    <td width="70" align="right"><p><?  echo $floor_library[$unloading_data[$batch_id]['floor_id']]; ?></p></td>
		<td width="100" title="Machine"><p><?  echo $machine_arr[$machine]; ?></p></td>
	    <td width="80" align="right"><p><? echo number_format($load_deying_qty,2); ?></p></td>

	    <td width="70" align="right"><p><? echo number_format($unload_deying_qty,2); ?></p></td>
	    <td width="80" align="right"><p><? echo $dyeing_result[$result]; ?></p></td>
	    <td align="right" width="60"><p><? echo number_format($slitting_qty,2); ?></p></td>
	    <td align="right" width="80"><p> <? echo number_format($stentering_qty,2); ?></p></td>

	    <td align="right" width="80"><p> <? echo number_format($compacting_qty,2); ?></p></td>
	    <td align="right" width="80"><? echo number_format($drying_qty,2); ?> </td>
	    <td align="right" width="80"><? echo number_format($special_qty,2,'.',''); ?></td>
	    <td width="80" align="right"><p><? echo number_format($finish_qty,2); ?></p></td>

	    <td width="80" align="right"><p><? echo number_format($process_loss_qty,2); ?></p></td>
	    <td width="60" align="right"><p><? echo number_format($process_loss_qty_percent,2); ?></p></td>
	    <td width="100" align="right"><p><? echo number_format($delivery_qty,2); ?></p></td>
	    <td width="80" align="right"><p><? echo number_format($total_delivery_balance_qty,2); ?></p></td>
	    <td><p><? echo  $batch_status_array[$batch_status_id]; ?></p></td>
	    </tr>
	    <?
		$total_batch_qty+=$row[('batch_qty')];
		$total_trims_weight_gt+=$row[('total_trims_weight')];
		$total_load_deying_qty+=$load_deying_qty;
		$total_unload_deying_qty+=$unload_deying_qty;
		$total_slitting_qty+=$slitting_qty;
		$total_stentering_qty+=$stentering_qty;
		$total_compacting_qty+=$compacting_qty;
		$total_drying_qty+=$drying_qty;
		$total_special_qty+=$special_qty;
		$total_heat_qty+=$heat_qty;
		$total_finish_qty+=$finish_qty;
		$total_process_loss_qty+=$process_loss_qty;
		$total_delivery_qty+=$delivery_qty;
		$total_balance_qty+=$total_delivery_balance_qty;
	    $i++;
	    }
	    ?>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="3000" class="rpt_table" id="report_table_footer">
		 <tfoot>
		    <th width="40"></th>
		    <th width="80"></th>
		    <th width="80"></th>
		    <th width="60"></th>

			<th width="80"></th>
		    <th width="90"></th>
		    <th width="100"></th>
		    <th width="110"></th>

		    <th width="80"></th>
		    <th width="80"></th>
		    <th width="110"></th>
		    <th width="70"></th>

		    <th width="70"></th>
		    <th width="70"></th>    
		    <th width="150"></th>
		    <th width="50"></th> 

		    <th width="60">GrandTotal</th>
	        <th width="70"><? echo number_format($total_heat_qty,2,'.',''); ?></th>
	        <th width="70"></th>
		    <th width="80"><? echo number_format($total_trims_weight_gt,2,'.',''); ?></th>

	        <th width="80"><? echo number_format($total_batch_qty,2,'.',''); ?></th>
		    <th width="70" ></th>
	        <th width="100"></th>
		    <th width="80"  id="total_load_qty"><? echo number_format($total_load_deying_qty,2,'.',''); ?></th>

		    <th width="70" id="total_unload_qty"><? echo number_format($total_unload_deying_qty,2,'.',''); ?></th>
		    <th width="80"></th>
		    <th width="60"  id="total_sliting_qty"><? echo number_format($total_slitting_qty,2,'.',''); ?></th>
		    <th width="80" id="total_stenter_qty"><? echo number_format($total_stentering_qty,2,'.',''); ?></th>

		    <th width="80" id="total_compact_qty"><? echo number_format($total_compacting_qty,2,'.',''); ?></th>
		    <th width="80" id="total_drying_qty"><? echo number_format($total_drying_qty,2,'.',''); ?></th>
		    <th width="80" id="total_special_qty"><? echo number_format($total_special_qty,2,'.',''); ?></th>
		    <th width="80" id="total_finish_qty2"><? echo number_format($total_finish_qty,2); ?></th>

		    <th width="80" id="total_process_loss_qty"><? echo number_format($total_process_loss_qty,2); ?></th>
			<th width="60"></th>
		   <th width="100" id="total_delivery_qty2"><? echo number_format($total_delivery_qty,2); ?></th>
		   <th width="80" id="total_balance_qty2"><? echo number_format($total_balance_qty,2); ?></th>
		   <th width=""></th>
		</tfoot>
		</table>
		</div>
	    <?
	} // subcon End
	?>
	<br>
	<?
	if($batch_type==0 || $batch_type==3 ) // Sample Start
	{
		?>
	    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="3220" class="rpt_table" id="table_header_1">
			<caption> <b style=" float:left"><? if($batch_type>0) echo $batch_type_arr[$batch_type];else echo "Sample Batch";?></b></caption>
			<thead>
				<th width="40">SL</th>
				<th width="80"><? if($cbo_search_date==1){ echo "Batch Date";}else{echo "Dyeing Date";} ?></th>
				<th width="80">Batch No</th>
				<th width="60">Ext.No</th>

				<th width="80">Batch Against</th>
				<th width="90">Color Name</th>
				<th width="100">Color Range</th>
				<th width="110">Buyer</th>

				<th width="80">Style Ref</th>
				<th width="80">Job No</th>
				<th width="110">F.Booking No</th>
				<th width="70">Order No</th>

				<th width="70">File No</th>
				<th width="70">Ref. No</th>
				<th width="150">Fabrics Type</th>
				<th width="50">GSM</th>

				<th width="60"><p>Dia/Width Type</p></th>
				<th width="70"><p>HeatSetting / Singeing</p></th>
	            <th width="70"><p>Batch Floor</p></th>
                <th width="100">Trims Description.</th>

				<th width="80">Trims Weight</th>
				<th width="80">Batch Qty.</th>
	            <th width="70"><p>Prod. Floor</p></th>
	            <th width="100">M/C No</th>

				<th width="80">Dyeing Loding</th>
				<th width="70"><p>Dyeing Un-Loding</p></th>
				<th width="80">Un-Loding Result</th>
				<th width="60"><p>Slitting / Squeezing</p></th>

				<th width="80">Stentering</th>
				<th width="80">Compacting</th>
				<th width="80">Drying</th>
				<th width="80">Special Finish</th>

				<th width="80"><p>Fin.Fab.Prod. Entry<p></th>
				<th width="80"><p>Process Loss Qty<p></th>
				<th width="60"><p>Process Loss %<p></th>
				<th width="100"><p>Fin.Fab.Delivery to Store</p></th>
				<th width="100"><p>Delivery Date</p></th>

				<th width="80"><p>Delivery Balance Qty.</p></th>
				<th width="">Batch Status</th>
			</thead>
		</table>
		<div style="width:3220px; overflow-y:scroll; max-height:450px;" id="scroll_body">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="3200" class="rpt_table" id="table_body3">
		<?	
		$non_order_arr=array();
		$sql_non_order="SELECT c.id,c.company_id,c.grouping as samp_ref_no,c.style_desc, c.buyer_id as buyer_name, b.booking_no, b.bh_qty 
		from wo_non_ord_samp_booking_mst c, wo_non_ord_samp_booking_dtls b 
		where c.booking_no=b.booking_no and c.booking_type=4 and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$result_sql_order=sql_select($sql_non_order);
		foreach($result_sql_order as $row)
		{
			
			$non_order_arr[$row[csf('booking_no')]]['buyer_name']=$row[csf('buyer_name')];
			$non_order_arr[$row[csf('booking_no')]]['samp_ref_no']=$row[csf('samp_ref_no')];
			$non_order_arr[$row[csf('booking_no')]]['style_desc']=$row[csf('style_desc')];
			$non_order_arr[$row[csf('booking_no')]]['bh_qty']=$row[csf('bh_qty')];
		}
		unset($result_sql_order);

	    $i=1;
		$roll_arr=array();
		$total_batch_qty=$total_finish_qty=$samp_total_process_loss_qty=$total_delivery_qty=$total_balance_qty=$total_load_deying_qty=$total_unload_deying_qty=0;
		$total_slitting_qty=$total_stentering_qty=$total_compacting_qty=$total_drying_qty=$total_special_qty=$total_samp_heat_qty=0;
		$total_trims_weight_gt = 0;
	     foreach($samp_batch_wise_arr as $batch_id=>$row)
	     {
	     if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	     $batch_id=$row[('id')];
	     $batch_no=$row[('batch_no')];
	     $booking_no=$row[('booking_no')];
		 if($row[('save_string')])
		{
			$save_stringData=array_unique(explode("!!",$row[('save_string')]));
			$trim_wgt_desc='';
			foreach($save_stringData as $desData)
			{
				$desc_stringArr=array_unique(explode("_",$desData));
				$desc_string=$desc_stringArr[0];
				if($trim_wgt_desc=='') $trim_wgt_desc=$desc_string;else $trim_wgt_desc.=",".$desc_string;
			}
		}
		
		$fabric_type=rtrim($row[('fabric_type')],',');
		$fabric_desc=array_unique(explode(",",$fabric_type));
		$fabric_desc_arr=''; $fabric_gsm_arr='';
		$fab='';
	    foreach($fabric_desc as $pid)
	    {
	       // $fabdesc_type=explode(",",$desc);
		   $fab_desc=$prod_detail_arr[$pid];	
			if($fab=='')
			{
				$fabdesc_type=explode(",",$fab_desc);
				$fab=$fabdesc_type[0].",".$fabdesc_type[1];	
				$fab_gsm=$prod_detail_gsm_arr[$pid];	
			}
			else
			{
				$fabdesc_type=explode(",",$fab_desc);
				$fab.="<br>".$fabdesc_type[0].",".$fabdesc_type[1];
				$fab_gsm.=", ".$prod_detail_gsm_arr[$pid];
			}
	    }//print  $fabric_desc_arr;//
	    //$desc=implode(',',explode(",",$fabric_desc_arr));
		$po_ids=rtrim($row[('po_id')],',');
	    $po_number=''; $job_no="";  $file="";$buyer=""; $refNo="";$buyer_style="";
	    $po_id=array_unique(explode(",",$po_ids));
	    foreach($po_id as $id)
	    {
			if($row[('entry_form')]==36) //SubCon
			{
				if($po_number=="") $po_number=$sub_job_array[$id]['po']; else $po_number.=", ".$sub_job_array[$id]['po'];
				if($job_no=="") $job_no=$sub_job_array[$id]['job']; else $job_no.=",".$sub_job_array[$id]['job'];
				if($buyer=="") $buyer=$buyer_library[$sub_job_array[$id]['buyer']]; else $buyer.=",".$buyer_library[$sub_job_array[$id]['buyer']];
				if($buyer_style=="") $buyer_style=$sub_job_array[$id]['style_no']; else $buyer_style.=','.$sub_job_array[$id]['style_no'];
			}
			else
			{
				if($po_number=="") $po_number=$job_array[$id]['po']; else $po_number.=", ".$job_array[$id]['po'];
				if($job_no=="") $job_no=$job_array[$id]['job']; else $job_no.=",".$job_array[$id]['job'];
				if($buyer=="") $buyer=$buyer_library[$job_array[$id]['buyer']]; else $buyer.=",".$buyer_library[$job_array[$id]['buyer']];
				if($file=="") $file=$job_array[$id]['file']; else $file.=",".$job_array[$id]['file']; // file
				if($refNo=="") $refNo=$job_array[$id]['refNo']; else $refNo.=",".$job_array[$id]['refNo']; // ref
				if($buyer_style=="") $buyer_style=$job_array[$id]['style_no']; else $buyer_style.=','.$job_array[$id]['style_no'];
			}
		
	    }
	    if($buyer_style==""){$buyer_style = $non_order_arr[$row[('booking_no')]]['style_desc'];}
	    if($buyer==""){$buyer = $buyer_library[$non_order_arr[$row[('booking_no')]]['buyer_name']];}

		//$buyer=$row[('buyer')];
		//echo $buyer.'sssss';
	    $job=implode(',',array_unique(explode(",",$job_no)));
	    $buyer_name=implode(',',array_unique(explode(",",$buyer)));
		
		$file_no=implode(',',array_unique(explode(",",$file)));
		$refNo_no=implode(',',array_unique(explode(",",$refNo)));
	    
	    $width_dia_type=rtrim($row[('width_dia_type')],',');
		$dia_type='';
	    $dia_type_id=array_unique(explode(",",$width_dia_type));
	    foreach($dia_type_id as $dia_id)
	    {	
	      if($dia_type=="") $dia_type=$fabric_typee[$dia_id]; else $dia_type.=",".$fabric_typee[$dia_id];
	    }
	    $dia_type_data=implode(',',array_unique(explode(",",$dia_type)));
		 $result=$unloading_data[$batch_id]['result'];
		 $machine=$unloading_data[$batch_id]['machine'];
		// $remark=$unloading_data[$batch_id]['remarks'];
		 $gsm=$finish_data_arr[$batch_id]['gsm'];
		//print_r( $result);
	    $heat_qty=$heat_setting_arr[$batch_id]['qty'];
		$load_deying_qty=$loading_data_arr[$batch_id]['load'];
		$unload_deying_qty=$loading_data_arr[$batch_id]['unload'];
		//$loading_data_arr[$batch_id]['load_qty']=$row_dyeing[csf('deying_load_id')];
		//$loading_data_arr[$batch_id]['unload_qty']=$row_dyeing[csf('deying_unload_id')];
		
		$shade_macth=$loading_data_arr[$batch_id]['result'];//$loading_data_arr[$batch_id]['result'];
		$slitting_qty=$slitting_arr[$batch_id]['slitting'];
		$stentering_qty=$stentering_arr[$batch_id]['stentering'];
		$compacting_qty=$compacting_arr[$batch_id]['compact'];
		$drying_qty=$drying_arr[$batch_id]['drying'];
		$special_qty=$special_arr[$batch_id]['special'];
		$finish_qty=$finish_data_arr[$batch_id]['finish_qty'];
		$process_loss_qty=$row[('batch_qty')]-$finish_qty;
		$process_loss_qty_percent=($process_loss_qty/$row[('batch_qty')])*100;
		$delivery_qty=$delivery_data_arr[$batch_id]['delivery'];
		$total_delivery_balance_qty=$finish_qty-$delivery_qty;
		$batch_status_id=$finish_data_status[$batch_id]['batch_status'];//$finish_data_arr[$batch_id]['batch_status'];
		//$finish_data_status[$row_st[csf('batch_id')]]['batch_status']
	    //echo $po_id;		
	    $delivery_dates=$delivery_data_arr[$batch_id]['delivery_date'];
	    $delivery_dates=implode(",", array_unique(explode(",", $delivery_dates)));
		?>
	 <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trsamp_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trsamp_<? echo $i; ?>"> 
	    <td width="40"><? echo $i; ?></td>
	    <td width="80"><p><? echo change_date_format($row[('batch_date')]); ?></p></td>
	    <!-- <td width="80" title="Batch ID=<? //echo $batch_id;?>"><p><? //echo $row[('batch_no')]; ?></p></td> -->
		<td width="80" onclick="print_batch_card(<?=$batch_id?>, '<?=$row[('batch_no')]?>');"  title="Batch ID=<? echo $batch_id;?>"><p><a href="##"><? echo $row[('batch_no')]; ?></a></p></td>
	    <td width="60"><p><? echo $row[('extention_no')]; ?></p></td>
		 <td width="80"><p><? echo $batch_against[$row[('batch_against')]]; ?></p></td>
		 <td width="90"><p><? echo $color_library[$row[('color_id')]]; ?></p></td>
		 <td width="100"><p><? echo $color_range[$row[('color_range_id')]]; ?></p></td>
	   
	    <td width="110"><p><? echo $buyer_name; ?></p></td>
	    <td width="80"><p><? echo $buyer_style; ?></p></td>
	    <td width="80"><p><? echo $job; ?></p></td>
	    <td width="110"><p><? echo $row[('booking_no')]; ?></p></td>
	    <td width="70" style=""><div style="word-wrap:break-word;width:70px"><? echo $po_number; ?></div></td>
	    
	    <td width="70"><p><? echo $file_no; ?></p></td>
	    <td width="70"><p><? echo $refNo_no; ?></p></td>
	    
	    <td width="150"><p><? echo $fab;?></p></td>
	    <td style="word-break:break-word; width:50px"><p><? echo  $fab_gsm;?></p></td>
	    <td width="60"><p><? echo $dia_type_data; ?></p></td>
	    <td width="70" align="right"><p><? echo number_format($heat_qty,2);  ?></p></td>
	     <td width="70" align="right"><p><? echo $floor_library[$row[('floor_id')]];  ?></p></td>
	    <td width="100" align="right"><p><?  echo $trim_wgt_desc; ?></p></td>
        <td width="80" align="right"><p><?  echo number_format($row[('total_trims_weight')],2); ?></p></td>
	    <td width="80" align="right"><p><?  echo number_format($row[('batch_qty')],2); ?></p></td>
	    <td width="70" align="right"><p><?  echo $floor_library[$unloading_data[$batch_id]['floor_id']]; ?></p></td>
	      <td width="100" title="Machine"><p><?  echo $machine_arr[$machine]; ?></p></td>
	    <td width="80" align="right"><p><? echo number_format($load_deying_qty,2); ?></p></td>
	    <td width="70" align="right"><p><? echo number_format($unload_deying_qty,2); ?></p></td>
	    <td width="80" align="right"><p><? echo $dyeing_result[$result]; ?></p></td>
	    <td align="right" width="60"><p><? echo number_format($slitting_qty,2); ?></p></td>
	    <td align="right" width="80"><p> <? echo number_format($stentering_qty,2); ?></p></td>
	    <td align="right" width="80"><p> <? echo number_format($compacting_qty,2); ?></p></td>
	    <td align="right" width="80"><? echo number_format($drying_qty,2); ?> </td>
	    <td align="right" width="80"><? echo number_format($special_qty,2,'.',''); ?></td>
	    <td width="80" align="right"><p><? echo number_format($finish_qty,2); ?></p></td>
	    <td width="80" align="right"><p><? echo number_format($process_loss_qty,2); ?></p></td>
	    <td width="60" align="right"><p><? echo number_format($process_loss_qty_percent,2); ?></p></td>
	    <td width="100" align="right"><p><? echo number_format($delivery_qty,2); ?></p></td>
	    <td width="100" align="right"><p><? echo chop($delivery_dates,","); ?></p></td>
	    <td width="80" align="right" title="finish_qty-delivery_qty=<? echo $finish_qty-$delivery_qty; ?>"><p><? echo number_format($total_delivery_balance_qty,2); ?></p></td>
	    <td><p><? echo  $batch_status_array[$batch_status_id]; ?></p></td>
	    </tr>
	    <?
		$total_batch_qty+=$row[('batch_qty')];
		$total_trims_weight_gt+=$row[('total_trims_weight')];
		$total_load_deying_qty+=$load_deying_qty;
		$total_unload_deying_qty+=$unload_deying_qty;
		
		$total_slitting_qty+=$slitting_qty;
		$total_stentering_qty+=$stentering_qty;
		$total_compacting_qty+=$compacting_qty;
		$total_drying_qty+=$drying_qty;
		$total_special_qty+=$special_qty;
		$samp_total_process_loss_qty+=$process_loss_qty;
		
		$total_finish_qty+=$finish_qty;$total_samp_heat_qty+=$heat_qty;
		$total_process_loss_qty+=$process_loss_qty;
		$total_delivery_qty+=$delivery_qty;
		$total_balance_qty+=$total_delivery_balance_qty;
		
	    $i++;
	    }
	    ?>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="3200" class="rpt_table" id="report_table_footer">
		 <tfoot>
		    <th width="40"></th>
		    <th width="80"></th>
		    <th width="80"></th>
		    <th width="60"></th>

			<th width="80"></th>
		    <th width="90"></th>
		    <th width="100"></th>
		    <th width="110"></th>

		    <th width="80"></th>
		    <th width="80"></th>
		    <th width="110"></th>
		    <th width="70"></th>  

		    <th width="70"></th>
		    <th width="70"></th>    
		    <th width="150"></th>
		    <th width="50"></th>

		    <th width="60">GrandTotal</th>
		    <th width="70"><? echo number_format($total_samp_heat_qty,2,'.',''); ?></th>
	        <th width="70"></th>
            <th width="100"></th>

		    <th width="80"><? echo number_format($total_trims_weight_gt,2,'.',''); ?></th>
		    <th width="80"><? echo number_format($total_batch_qty,2,'.',''); ?></th>
	        <th width="70"></th>
	        <th width="100"></th>

		    <th width="80" id="total_load_qty"><? echo number_format($total_load_deying_qty,2,'.',''); ?></th>
		    <th width="70" id="total_unload_qty"><? echo number_format($total_unload_deying_qty,2,'.',''); ?></th>
		    <th width="80"></th>
		    <th width="60" id="total_sliting_qty"><? echo number_format($total_slitting_qty,2,'.',''); ?></th>

		    <th width="80" id="total_stenter_qty"><? echo number_format($total_stentering_qty,2,'.',''); ?></th>
		    <th width="80" id="total_compact_qty"><? echo number_format($total_compacting_qty,2,'.',''); ?></th>
		    <th width="80" id="total_drying_qty"><? echo number_format($total_drying_qty,2,'.',''); ?></th>
		    <th width="80" id="total_spceial_qty"><? echo number_format($total_special_qty,2,'.',''); ?></th>

		    <th width="80"><? echo number_format($total_finish_qty,2); ?></th>
		    <th width="80"  id="total_process_loss_qty"><? echo number_format($samp_total_process_loss_qty,2); ?></th>
		    <th width="60"></th>
		   <th width="100"><? echo number_format($total_delivery_qty,2); ?></th>
		   <th width="100"></th>

		   <th width="80"><? echo number_format($total_balance_qty,2); ?></th>
		   <th width=""></th>
		</tfoot>
		</table>
		</div>
	    <?
		 
	} // Sample End
	if($batch_type==0 || $batch_type==4 ) // Sales Start
	{
		?>
	    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="3240" class="rpt_table" id="table_header_1">
			<caption> <b style=" float:left"><? if($batch_type>0) echo $batch_type_arr[$batch_type];else echo "Sales Batch";?></b></caption>
			<thead>
				<th width="40">SL</th>
				<th width="80"><? if($cbo_search_date==1){ echo "Batch Date";}else{echo "Dyeing Date";} ?></th>
				<th width="80">Batch No</th>
				<th width="60">Ext.No</th>
				<th width="80">Batch Against</th>
				<th width="90">Color Name</th>
				<th width="100">Color Range</th>
				<th width="110">Buyer</th>
				<th width="80">Style Ref</th>
				<th width="80">Job No</th>
				<th width="110">Sales/F.Booking No</th>
				<th width="70">Order No</th>
				<th width="70">File No</th>
				<th width="70">Ref. No</th>
				<th width="150">Fabrics Type</th>
				<th width="50">GSM</th>
				<th width="60"><p>Dia/Width Type</p></th>
				<th width="70"><p>HeatSetting / Singeing</p></th>
	            <th width="70"><p>Batch Floor</p></th>
				<th width="100">Trims Description.</th>
                <th width="80">Trims Weight</th>
				<th width="80">Batch Qty.</th>
	            <th width="70"><p>Prod. Floor</p></th>
	            <th width="100">M/C No</th>
				<th width="80">Dyeing Loding</th>
				<th width="70"><p>Dyeing Un-Loding</p></th>
				<th width="80">Un-Loding Result</th>
				<th width="60"><p>Slitting / Squeezing</p></th>
				<th width="80">Stentering</th>
				<th width="80">Compacting</th>
				<th width="80">Drying</th>
				<th width="80">Special Finish</th>
				<th width="80"><p>Fin.Fab.Prod. Entry<p></th>
				<th width="80"><p>Process Loss Qty<p></th>
				<th width="60"><p>Process Loss %<p></th>
				<th width="100"><p>Fin.Fab.Delivery to Store</p></th>
				<th width="100"><p>Delivery Date</p></th>
				<th width="80"><p>Delivery Balance Qty.</p></th>
				<th width="">Batch Status</th>
			</thead>
		</table>
		<div style="width:3220px; overflow-y:scroll; max-height:450px;" id="scroll_body">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="3200" class="rpt_table" id="table_body4">
			<?
		    $i=1;  $roll_arr=array();$total_batch_qty=$total_finish_qty=$samp_total_process_loss_qty=$total_delivery_qty=$total_balance_qty=$total_load_deying_qty=$total_unload_deying_qty=0;
			$total_slitting_qty=$total_stentering_qty=$total_compacting_qty=$total_drying_qty=$total_special_qty=$total_sales_heat_qty=0;
			$total_trims_weight_gt = 0;
		    foreach($sales_batch_wise_arr as $batch_id=>$row)
			{
			    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			    $batch_id=$row[('id')];
			    $batch_no=$row[('batch_no')];
			    $booking_no=$row[('booking_no')];
				if($row[('save_string')])
				{
					$save_stringData=array_unique(explode("!!",$row[('save_string')]));
					$trim_wgt_desc='';
					foreach($save_stringData as $desData)
					{
						$desc_stringArr=array_unique(explode("_",$desData));
						$desc_string=$desc_stringArr[0];
						if($trim_wgt_desc=='') $trim_wgt_desc=$desc_string;else $trim_wgt_desc.=",".$desc_string;
					}
				}
				
				$fabric_type=rtrim($row[('fabric_type')],',');
				$fabric_desc=array_unique(explode(",",$fabric_type));
				$fabric_desc_arr=''; $fabric_gsm_arr='';
				$fab='';
			    foreach($fabric_desc as $pid)
			    {
			       // $fabdesc_type=explode(",",$desc);
				   $fab_desc=$prod_detail_arr[$pid];	
					if($fab=='')
					{
						$fabdesc_type=explode(",",$fab_desc);
						$fab=$fabdesc_type[0].",".$fabdesc_type[1];	
						$fab_gsm=$prod_detail_gsm_arr[$pid];	
					}
					else
					{
						$fabdesc_type=explode(",",$fab_desc);
						$fab.="<br>".$fabdesc_type[0].",".$fabdesc_type[1];
						$fab_gsm.=", ".$prod_detail_gsm_arr[$pid];
					}
			    }//print  $fabric_desc_arr;//
			    //$desc=implode(',',explode(",",$fabric_desc_arr));
				$po_ids=rtrim($row[('po_id')],',');
			    $po_number=''; $job_no=""; $file=""; $refNo="";$buyer_style="";
			    $po_id=array_unique(explode(",",$po_ids));
			    foreach($po_id as $id)
			    {
					if($row[('entry_form')]==36) //SubCon
					{
						if($po_number=="") $po_number=$sub_job_array[$id]['po']; else $po_number.=", ".$sub_job_array[$id]['po'];
						if($job_no=="") $job_no=$sub_job_array[$id]['job']; else $job_no.=",".$sub_job_array[$id]['job'];
						//if($buyer=="") $buyer=$buyer_library[$sub_job_array[$id]['buyer']]; else $buyer.=",".$buyer_library[$sub_job_array[$id]['buyer']];
						if($buyer_style=="") $buyer_style=$sub_job_array[$id]['style_no']; else $buyer_style.=','.$sub_job_array[$id]['style_no'];
					}
					else
					{
						if($po_number=="") $po_number=$job_array[$id]['po']; else $po_number.=", ".$job_array[$id]['po'];
						if($job_no=="") $job_no=$job_array[$id]['job']; else $job_no.=",".$job_array[$id]['job'];
						//if($buyer=="") $buyer=$buyer_library[$job_array[$id]['buyer']]; else $buyer.=",".$buyer_library[$job_array[$id]['buyer']];
						if($file=="") $file=$job_array[$id]['file']; else $file.=",".$job_array[$id]['file']; // file
						if($refNo=="") $refNo=$job_array[$id]['refNo']; else $refNo.=",".$job_array[$id]['refNo']; // ref
						if($buyer_style=="") $buyer_style=$job_array[$id]['style_no']; else $buyer_style.=','.$job_array[$id]['style_no'];

					}
				
			    }
				$buyer=$row[('buyer')];
			    $job=implode(',',array_unique(explode(",",$job_no)));
			    $buyer_name=implode(',',array_unique(explode(",",$buyer)));
				
				$file_no=implode(',',array_unique(explode(",",$file)));
				$refNo_no=implode(',',array_unique(explode(",",$refNo)));
			    
			    $width_dia_type=rtrim($row[('width_dia_type')],',');
				$dia_type='';
			    $dia_type_id=array_unique(explode(",",$width_dia_type));
			    foreach($dia_type_id as $dia_id)
			    {	
			      if($dia_type=="") $dia_type=$fabric_typee[$dia_id]; else $dia_type.=",".$fabric_typee[$dia_id];
			    }
			    $dia_type_data=implode(',',array_unique(explode(",",$dia_type)));
				 $result=$unloading_data[$batch_id]['result'];
				 $machine=$unloading_data[$batch_id]['machine'];
				// $remark=$unloading_data[$batch_id]['remarks'];
				 $gsm=$finish_data_arr[$batch_id]['gsm'];
				//print_r( $result);
			    $heat_qty=$heat_setting_arr[$batch_id]['qty'];
				$load_deying_qty=$loading_data_arr[$batch_id]['load'];
				$unload_deying_qty=$loading_data_arr[$batch_id]['unload'];
				//$loading_data_arr[$batch_id]['load_qty']=$row_dyeing[csf('deying_load_id')];
				//$loading_data_arr[$batch_id]['unload_qty']=$row_dyeing[csf('deying_unload_id')];
				
				$shade_macth=$loading_data_arr[$batch_id]['result'];//$loading_data_arr[$batch_id]['result'];
				$slitting_qty=$slitting_arr[$batch_id]['slitting'];
				$stentering_qty=$stentering_arr[$batch_id]['stentering'];
				$compacting_qty=$compacting_arr[$batch_id]['compact'];
				$drying_qty=$drying_arr[$batch_id]['drying'];
				$special_qty=$special_arr[$batch_id]['special'];
				$finish_qty=$finish_data_arr[$batch_id]['finish_qty'];
				$process_loss_qty=$row[('batch_qty')]-$finish_qty;
				$process_loss_qty_percent=($process_loss_qty/$row[('batch_qty')])*100;
				$delivery_qty=$delivery_data_arr[$batch_id]['delivery'];
				$total_delivery_balance_qty=$finish_qty-$delivery_qty;
				$batch_status_id=$finish_data_status[$batch_id]['batch_status'];//$finish_data_arr[$batch_id]['batch_status'];
				//$finish_data_status[$row_st[csf('batch_id')]]['batch_status']
			    //echo $po_id;		
			    $delivery_dates=$delivery_data_arr[$batch_id]['delivery_date'];
			    $delivery_dates=implode(",", array_unique(explode(",", $delivery_dates)));
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trsales_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trsales_<? echo $i; ?>"> 
				    <td width="40"><? echo $i; ?></td>
				    <td width="80"><p><? echo change_date_format($row[('batch_date')]); ?></p></td>
					<td width="80" onclick="print_batch_card(<?=$batch_id?>, '<?=$row[('batch_no')]?>');"  title="Batch ID=<? echo $batch_id;?>"><p><a href="##"><? echo $row[('batch_no')]; ?></a></p></td>
				    <!-- <td width="80" title="Batch ID=<? //echo $batch_id;?>"><p><? //echo $row[('batch_no')]; ?></p></td> -->
				    <td width="60"><p><? echo $row[('extention_no')]; ?></p></td>
					<td width="80"><p><? echo $batch_against[$row[('batch_against')]]; ?></p></td>
					<td width="90"><p><? echo $color_library[$row[('color_id')]]; ?></p></td>
					<td width="100"><p><? echo $color_range[$row[('color_range_id')]]; ?></p></td>
				   
				    <td width="110"><p><? echo $buyer_name; ?></p></td>
				    <td width="80"><p><? echo $buyer_style; ?></p></td>
				    <td width="80"><p><? echo $job; ?></p></td>
				    <td width="110"><p><? echo $row[('booking_no')]; ?></p></td>
				    <td width="70" style=""><div style="word-wrap:break-word;width:70px"><? echo $po_number; ?></div></td>
				    
				    <td width="70"><p><? echo $file_no; ?></p></td>
				    <td width="70"><p><? echo $refNo_no; ?></p></td>
				    
				    <td width="150"><p><? echo $fab;?></p></td>
				    <td style="word-break:break-word; width:50px"><p><? echo  $fab_gsm;?></p></td>
				    <td width="60"><p><? echo $dia_type_data; ?></p></td>
				    <td width="70" align="right"><p><? echo number_format($heat_qty,2);  ?></p></td>
				     <td width="70" align="right"><p><? echo $floor_library[$row[('floor_id')]];  ?></p></td>
				    <td width="100" align="right"><p><?  echo $trim_wgt_desc; ?></p></td>
			        <td width="80" align="right"><p><?  echo number_format($row[('total_trims_weight')],2); ?></p></td>


				    <td width="80" align="right"><p><?  echo number_format($row[('batch_qty')],2); ?></p></td>
				    <td width="70" align="right"><p><?  echo $floor_library[$unloading_data[$batch_id]['floor_id']]; ?></p></td>
				    <td width="100" title="Machine"><p><?  echo $machine_arr[$machine]; ?></p></td>
				    <td width="80" align="right"><p><? echo number_format($load_deying_qty,2); ?></p></td>
				    <td width="70" align="right"><p><? echo number_format($unload_deying_qty,2); ?></p></td>
				    <td width="80" align="right"><p><? echo $dyeing_result[$result]; ?></p></td>
				    <td align="right" width="60"><p><? echo number_format($slitting_qty,2); ?></p></td>
				    <td align="right" width="80"><p> <? echo number_format($stentering_qty,2); ?></p></td>
				    <td align="right" width="80"><p> <? echo number_format($compacting_qty,2); ?></p></td>
				    <td align="right" width="80"><? echo number_format($drying_qty,2); ?> </td>
				    <td align="right" width="80"><? echo number_format($special_qty,2,'.',''); ?></td>
				    <td width="80" align="right"><p><? echo number_format($finish_qty,2); ?></p></td>
				    <td width="80" align="right"><p><? echo number_format($process_loss_qty,2); ?></p></td>
				    <td width="60" align="right"><p><? echo number_format($process_loss_qty_percent,2); ?></p></td>
				    <td width="100" align="right"><p><? echo number_format($delivery_qty,2); ?></p></td>
				    <td width="100" align="right"><p><? echo chop($delivery_dates,","); ?></p></td>
				    <td width="80" align="right"><p><? echo number_format($total_delivery_balance_qty,2); ?></p></td>
				    <td><p><? echo  $batch_status_array[$batch_status_id]; ?></p></td>
				</tr>
			    <?
				$total_batch_qty+=$row[('batch_qty')];
				$total_trims_weight_gt+=$row[('total_trims_weight')];
				$total_load_deying_qty+=$load_deying_qty;
				$total_unload_deying_qty+=$unload_deying_qty;
				
				$total_slitting_qty+=$slitting_qty;
				$total_stentering_qty+=$stentering_qty;
				$total_compacting_qty+=$compacting_qty;
				$total_drying_qty+=$drying_qty;
				$total_special_qty+=$special_qty;
				$samp_total_process_loss_qty+=$process_loss_qty;
				
				$total_finish_qty+=$finish_qty;$total_sales_heat_qty+=$heat_qty;
				$total_process_loss_qty+=$process_loss_qty;
				$total_delivery_qty+=$delivery_qty;
				$total_balance_qty+=$total_delivery_balance_qty;
				
			    $i++;
			}
		    ?>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="3200" class="rpt_table" id="report_table_footer">
			<tfoot>
			    <th width="40"></th>
			    <th width="80"></th>
			    <th width="80"></th>
			    <th width="60"></th>
				<th width="80"></th>
			    <th width="90"></th>
			    <th width="100"></th>
			    <th width="110"></th>
			    <th width="80"></th>
			    <th width="80"></th>
			    <th width="110"></th>
			    <th width="70"></th>    
			    <th width="70"></th>
			    <th width="70"></th>    
			    <th width="150"></th>
			    <th width="50"></th>
			    <th width="60">GrandTotal</th>
			    <th width="70"><? echo number_format($total_sales_heat_qty,2,'.',''); ?></th>
		        <th width="70"></th>
	            <th width="100"></th>
			    <th width="80" id="total_trims_weight_gt3"><? echo number_format($total_trims_weight_gt,2,'.',''); ?></th>
			    <th width="80" id="total_batch_qty3"><? echo number_format($total_batch_qty,2,'.',''); ?></th>
		        <th width="70"></th>
		        <th width="100"></th>
			    <th width="80" id="total_load_qty"><? echo number_format($total_load_deying_qty,2,'.',''); ?></th>
			    <th width="70" id="total_unload_qty"><? echo number_format($total_unload_deying_qty,2,'.',''); ?></th>
			    <th width="80"></th>
			    <th width="60" id="total_sliting_qty"><? echo number_format($total_slitting_qty,2,'.',''); ?></th>
			    <th width="80" id="total_stenter_qty"><? echo number_format($total_stentering_qty,2,'.',''); ?></th>
			    <th width="80" id="total_compact_qty"><? echo number_format($total_compacting_qty,2,'.',''); ?></th>
			    <th width="80" id="total_drying_qty"><? echo number_format($total_drying_qty,2,'.',''); ?></th>
			    <th width="80" id="total_spceial_qty"><? echo number_format($total_special_qty,2,'.',''); ?></th>
			    <th width="80" id="total_finish_qty3"><? echo number_format($total_finish_qty,2); ?></th>
			    <th width="80" id="total_process_loss_qty"><? echo number_format($samp_total_process_loss_qty,2); ?></th>
			    <th width="60"></th>
			   <th width="100" id="total_delivery_qty3"><? echo number_format($total_delivery_qty,2); ?></th>
			   <th width="100"></th>
			   <th width="80" id="total_balance_qty3"><? echo number_format($total_balance_qty,2); ?></th>
			   <th width=""></th>
			</tfoot>
		</table>
		</div>
	    <?
	} // Sales End
	?>
	</div>
    <?
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    	@unlink($filename);

    }
    //---------end------------//
    $name=time();
    $filename=$user_name."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename";
	exit();
}
if ($action == "batch_card_print_11") 
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$batch_update_id = $data[1];
	$batch_no = $data[2];

	$sql = "select id, batch_no, batch_sl_no,EXTENTION_NO, BOOKING_NO_ID,WORKING_COMPANY_ID from PRO_BATCH_CREATE_MST where id = $batch_update_id and status_active = 1 and is_deleted = 0";
	$data_arr_para = sql_select($sql);
	
	$working_company = $data_arr_para[0]['WORKING_COMPANY_ID'];


	$nameArray= sql_select("select fabric_roll_level from variable_settings_production where company_name='$company' and item_category_id=50 and variable_list=3 and status_active=1 and is_deleted= 0 order by id");
	foreach($nameArray as $row)
	{
		$roll_maintained = $row[csf('fabric_roll_level')];
	}

	if ($roll_maintained == "" || $roll_maintained == 2) $roll_maintained = 0; else $roll_maintained = $roll_maintained;


	
	$batch_mst_update_id = str_pad($batch_update_id, 10, '0', STR_PAD_LEFT);

	$batch_sl_no = $data_arr_para[0]['BATCH_SL_NO'];;


	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_library = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$machine_library = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$season_name_arr = return_library_array("select id,season_name from lib_buyer_season", 'id', 'season_name');


	$job_array = array();
	$job_sql = "SELECT distinct(a.buyer_name) as buyer_name,a.style_ref_no, a.job_no_prefix_num, a.job_no, b.pub_shipment_date, b.id, b.po_number, b.grouping, b.file_no,c.article_number, a.season_buyer_wise as season
	from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,pro_batch_create_dtls d
	where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.id=d.po_id and d.po_id=c.po_break_down_id and d.mst_id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	// echo $job_sql;
	$job_sql_result = sql_select($job_sql);
	foreach ($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
		$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['ship_date'] = $row[csf('pub_shipment_date')];
		$job_array[$row[csf('id')]]['ref'] = $row[csf('grouping')];
		$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
		$job_array[$row[csf('id')]]['style'] = $row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['season_id'] = $row[csf('season')];
		$job_array[$row[csf('id')]]['article_number']= $row[csf('article_number')];
		$job_array[$row[csf('job_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
		$job_array[$row[csf('job_no')]]['season'] = $row[csf('season')];
	}
	/*echo "<pre>";
	print_r($job_array);*/

	if ($db_type == 0)
	{
		$sql = "select a.id, a.batch_no, a.booking_no_id,a.batch_date, a.booking_no,a.batch_type_id,a.booking_without_order,a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks, a.collar_qty, a.cuff_qty, a.SAVE_STRING, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id
		from pro_batch_create_mst a, pro_batch_create_dtls b
		where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id, a.batch_no, a.booking_no_id, a.booking_no,a.batch_type_id,a.booking_without_order,a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id, a.batch_for, a.batch_weight, a.dyeing_machine,a.remarks,a.collar_qty, a.cuff_qty, a.SAVE_STRING";
	}
	else
	{
		$sql = "SELECT a.id, a.batch_no, a.booking_no_id,a.batch_date,a.booking_no,a.batch_type_id,a.booking_without_order, a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks, a.collar_qty, a.cuff_qty, a.SAVE_STRING, LISTAGG(b.po_id, ',') WITHIN GROUP (ORDER BY b.po_id) AS po_id , LISTAGG(CAST(b.prod_id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.prod_id) AS prod_id,a.is_sales
		from pro_batch_create_mst a, pro_batch_create_dtls b
		where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id, a.batch_no, a.color_id, a.batch_against,a.batch_type_id, a.color_range_id, a.organic, a.extention_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.total_trims_weight,a.batch_date, a.process_id, a.batch_for, a.batch_weight, a.dyeing_machine,a.remarks,a.collar_qty,a.cuff_qty, a.SAVE_STRING,a.is_sales";
	}
	//echo $sql;
	$dataArray = sql_select($sql);

	$po_number = "";
	$job_number = "";
	$job_style = "";
	$job_season = "";
	$buyer_id = "";
	$ship_date = "";
	$internal_ref = "";
	$file_nos = "";
	$article_number="";
	$po_id = array_unique(explode(",", $dataArray[0][csf('po_id')]));
	$booking_no = $dataArray[0][csf('booking_no')];
	$batch_against_id = $dataArray[0][csf('batch_against')];
	$batch_booking_id = $dataArray[0][csf('booking_no_id')];
	$batch_product_id = $dataArray[0][csf('prod_id')];
	$batch_booking_without = $dataArray[0][csf('booking_without_order')];

	if ($dataArray[0][csf('is_sales')] == 1)
	{
		$sales_order_id = $po_id[0];
		$sales_data = sql_select("select id,job_no,sales_booking_no,within_group,buyer_id,style_ref_no, season_id as season from fabric_sales_order_mst where id=$sales_order_id");
		if ($sales_data[0][csf("within_group")] == 1)
		{
			$booking_data = sql_select("select b.job_no,a.buyer_id from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no='$booking_no' and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 group by b.job_no,a.buyer_id");
			$job_number = $booking_data[0][csf("job_no")];
			$job_nos ="'".$booking_data[0][csf("job_no")]."'";
			$buyer_id = $booking_data[0][csf("buyer_id")];
			$po_number = $sales_data[0][csf("job_no")];
			$job_style = $job_array[$job_number]['style_ref_no'];
			$job_season = $job_array[$job_number]['season'];
			$ship_date = "";
		}
		else
		{
			$po_number = $sales_data[0][csf("job_no")];
			$job_number = "";
			$buyer_id = $sales_data[0][csf("buyer_id")];
			$job_style = $sales_data[0][csf("style_ref_no")];
			$job_season = $sales_data[0][csf("season")];
			$ship_date = "";
		}
	}
	else
	{
		foreach ($po_id as $val)
		{
			if ($po_number == "") $po_number = $job_array[$val]['po']; else $po_number .= ',' . $job_array[$val]['po'];
			if ($job_number == "") $job_number = $job_array[$val]['job']; else $job_number .= ',' . $job_array[$val]['job'];
			if ($job_style == "") $job_style = $job_array[$val]['style']; else $job_style .= ',' . $job_array[$val]['style'];
			if ($job_season == "") $job_season = $job_array[$val]['season_id']; else $job_season .= ',' . $job_array[$val]['season_id'];
			if ($buyer_id == "") $buyer_id = $job_array[$val]['buyer']; else $buyer_id .= ',' . $job_array[$val]['buyer'];
			if ($ship_date == "") $ship_date = change_date_format($job_array[$val]['ship_date']); else $ship_date .= ',' . change_date_format($job_array[$val]['ship_date']);

			if ($internal_ref == "") $internal_ref = $job_array[$val]['ref']; else $internal_ref .= ',' . $job_array[$val]['ref'];
			if ($job_array[$val]['file_no'] > 0) {
				if ($file_nos == "") $file_nos = $job_array[$val]['file_no']; else $file_nos .= ',' . $job_array[$val]['file_no'];
			}
			if ($article_number == "") $article_number = $job_array[$val]['article_number']; else $article_number .= ',' . $job_array[$val]['article_number'];
		}
	}

	$job_no = implode(",", array_unique(explode(",", $job_number)));
	$job_nos = implode(",", array_unique(explode(",", $job_nos)));
	$jobstyle = implode(",", array_unique(explode(",", $job_style)));
	$jobSeason = implode(",", array_unique(explode(",", $job_season)));
	$buyer = implode(",", array_unique(explode(",", $buyer_id)));
	$internal_ref = implode(",", array_unique(array_filter(explode(",", $internal_ref))));
	$file_nos = implode(",", array_unique(explode(",", $file_nos)));
	$article_numbers = implode(",", array_unique(explode(",", $article_number)));
	$sam_style_ref_no='';
	if ($dataArray[0][csf('booking_without_order')] == 1)
	{
		$booking_without_order = sql_select("select a.booking_no_prefix_num,a.grouping, a.buyer_id,b.style_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and a.company_id=$company and a.booking_no='$booking_no' and a.booking_type=4 and a.status_active=1");
		// echo "select a.booking_no_prefix_num,a.grouping, a.buyer_id,b.style_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and a.company_id=$company and a.booking_no='$booking_no' and a.booking_type=4 and a.status_active=1";
		$booking_id = $booking_without_order[0][csf('booking_no_prefix_num')];
		$style_id = $booking_without_order[0][csf('style_id')];
		$internal_ref=	 $booking_without_order[0][csf('grouping')];
		//echo $style_id .'DDDDDDDDDD';
		$sam_style_ref_no = return_field_value("style_ref_no  as style_ref_no", "sample_development_mst", "id=" . $style_id, "style_ref_no");
		$jobSeason = return_field_value("season", "sample_development_mst", "id=" . $style_id, "season");

		$buyer_id_booking = $booking_without_order[0][csf('buyer_id')];
	}
	else
	{
		$booking_with_order = sql_select("select booking_no_prefix_num, buyer_id from wo_booking_mst where company_id=$company and booking_no='$booking_no' and booking_type=4");
		$booking_id = $booking_with_order[0][csf('booking_no_prefix_num')];
		$buyer_id_booking = $booking_with_order[0][csf('buyer_id')];
	}

	$lab_dip_no_sql=sql_select("select b.id, b.lapdip_no from wo_booking_dtls a, wo_po_lapdip_approval_info b where a.job_no = b.job_no_mst and b.job_no_mst in($job_nos) and a.booking_no ='$booking_no' and a.fabric_color_id = b.color_name_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.approval_status =3 and b.color_name_id = ". $dataArray[0][csf('color_id')]." group by b.id, b.lapdip_no");

	foreach($lab_dip_no_sql as $row)
	{
		$lab_dip_no_arr[$row[csf("lapdip_no")]] = $row[csf("lapdip_no")];
	}
	?>
	<div style="width:1060px;">
		<div style="width: 100%; background-image: url(../draft.jpg); background-attachment: scroll; background-repeat: no-repeat; background-position-y: 160px; background-size: 100% 70%;">
		<table width="1060" cellspacing="0" align="center" border="0" style="font-size: 17px;">
			<tr>
				<td colspan="6" align="center" style="font-size:22px">
					<strong><? echo $company_library[$working_company]; ?></strong></td>
				<td colspan="2" align="left">Print Time:<? echo $date = date("F j, Y,g:i a"); ?></td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><u>Batch Card</u></strong></td>
				<td colspan="2" id="barcode_img_id" align="right" style="font-size:24px"></td>
			</tr>
			<tr>
				<td colspan="8">&nbsp; </td>
				<td>&nbsp; </td>
			</tr>
			<tr>
				<td colspan="6" align="left" style="font-size:18px"><strong><u>Reference Details</u></strong>
				</td>
				<td style="font-size:24px; border: solid 2px;" align="center" colspan="2">
					&nbsp;<? echo $dataArray[0][csf('organic')]; ?></td>
			</tr>
			<tr>
				<td width="120"><strong>Batch No</strong></td>
				<td width="135px">:&nbsp;<? echo $dataArray[0][csf('batch_no')]; ?></td>
				<td width="120"><strong>Batch SL</strong></td>
				<td width="135px">:&nbsp;<? echo $batch_sl_no; ?></td>
				<td width="120"><strong>Batch Date</strong></td>
				<td width="135px">:&nbsp;<? echo $dataArray[0][csf('batch_date')]; ?></td>
				<td width="120"><strong>B. Weight</strong></td>
				<td width="135px">:&nbsp;<? echo $dataArray[0][csf('batch_weight')]; ?></td>
			</tr>
			<tr>
				<td><strong>Batch Against</strong></td>
				<td>:&nbsp;<? echo $batch_against[$dataArray[0][csf('batch_against')]]; ?></td>

				<td><strong>Lab Dip No</strong></td>
				<td>:&nbsp;<? echo  implode(",", $lab_dip_no_arr); ?></td>
				<td><strong>Batch For</strong></td>
				<td>:&nbsp;<? echo $batch_for[$dataArray[0][csf('batch_for')]]; ?></td>
                <td><strong>Ship Date</strong></td>
				<td>:&nbsp;<? if (trim($ship_date) != "0000-00-00" && trim($ship_date) != "") echo implode(",", array_unique(explode(",", $ship_date))); else echo "&nbsp;"; ?></td>
			</tr>
			<tr>
				<td style="font-size:14px;"><strong>Buyer</strong></td>
				<td style="font-size:14px;">:&nbsp;<? if ($dataArray[0][csf('batch_against')] == 3) echo $buyer_arr[$buyer_id_booking]; else echo $buyer_arr[$buyer]; ?></td>
				<?
				//if ($dataArray[0][csf('batch_against')] == 3) {
					?>
					<td  style="font-size:14px;"><strong>B. Color</strong></td>
					<td  style="font-size:14px;">:&nbsp;<? echo $color_arr[$dataArray[0][csf('color_id')]];//echo $booking_id; ?></td>

				<? //} else { ?>
					<td  style="font-size:14px;"><strong>Color Ran.</strong></td>
					<td  style="font-size:14px;">:&nbsp;<? echo $color_range[$dataArray[0][csf('color_range_id')]];//echo $job_no; ?></td>
				<? //}
				?>

				<td><strong>File No</strong></td>
				<td>:&nbsp;<? //echo $po_number; ?></td>
			</tr>
			<tr>
                <td><strong>Style Ref.</strong></td>
				<td>:&nbsp;<? if($sam_style_ref_no) echo $sam_style_ref_no;else echo $jobstyle;// ?></td>
				<td><strong>Article No</strong></td>
				<td>:&nbsp;<? echo $article_numbers; ?></td>
				<td><strong>Int. Ref.</strong></td>
				<td>:&nbsp;<? //echo $dataArray[0][csf('cuff_qty')]; ?></td>
				<td  style="font-size:14px;"><strong>Job</strong></td>
				<td  style="font-size:14px;">:&nbsp;<? echo $job_no; ?></td>
			</tr>
			<tr>
				<td  style="font-size:14px;"><strong>Order No</strong></td>
				<td  style="font-size:14px;">:&nbsp;<? echo $po_number; ?></td>
                <td><strong>Dying Machine</strong></td>
				<td>:&nbsp;
					<?
					if ($db_type == 2) {
						$dyeing_machine = return_field_value("(machine_no || '-' || brand) as machine_name", "lib_machine_name", "id=" . $dataArray[0][csf('dyeing_machine')], "machine_name");
					} else if ($db_type == 0) {
						$dyeing_machine = return_field_value("concat(machine_no,'-',brand) as machine_name", "lib_machine_name", "id=" . $dataArray[0][csf('dyeing_machine')], "machine_name");
					}
					echo $dyeing_machine;
					?>
				</td>
				<td><strong>Batch Ext.</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('extention_no')];// $dataArray[0][csf('remarks')];?></td>
				<td><strong>Booking No.</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('booking_no')]; ?></td>
			</tr>
            <tr>
            	<td><strong>Collar Qty(Pcs)</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('collar_qty')]; ?></td>
				<td><strong>Cuff Qty(Pcs)</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('cuff_qty')]; ?></td>
				<td><strong>Remarks</strong></td>
				<td  style="font-size:14px;">:&nbsp;<? echo $dataArray[0][csf('remarks')]; ?></td>
				<td><strong>Season</strong></td>
				<td  style="font-size:14px;">:&nbsp;<? echo $season_name_arr[$jobSeason]; ?></td>
			</tr>
            <tr>
            <td><strong>Batch Type</strong></td>
            <td>:&nbsp;<?  echo $batch_type_arr[$dataArray[0][csf('batch_type_id')]];  ?></td>
            </tr>
		</table>
		<br/>
		<div style="float:left; font-size:17px;"><strong><u>Fabrication Details</u></strong></div>
			<table align="center" cellspacing="0" width="1060" border="1" rules="all" class="rpt_table" style="border-top:none;font-size: 17px;  opacity: 0.75;">
                <thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="30">SL</th>
						<th width="60">Prog. No</th>
						<th width="80">Machine / Knitting Com</th>
						<th width="80">Body part</th>
						<th width="150">Const. & Comp.</th>
						<th width="50"  style="font-size:14px;">Fin. GSM</th>
						<th width="50"  style="font-size:14px;">Fin. Dia</th>
						<th width="70">M/Dia X Gauge</th>
						<th width="70"  style="font-size:14px;">D/W Type</th>
						<th width="60">S. Length</th>
						<th width="70">Grey Qty.</th>
						<th width="50">Total Roll</th>
						<th width="80">Yarn Lot</th>
						<th width="80"><strong>Brand</strong></th>
						<th width="80"  style="font-size:14px;">Yarn Count</th>
						<th>Yarn Type</th>
					</tr>
				</thead>
				<?
				$i = 1;
				$yarncount = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
				$brand_name_arr = return_library_array("select id, brand_name from  lib_brand", 'id', 'brand_name');
				$supplier_library = return_library_array("select id,short_name from  lib_supplier", "id", "short_name");
				$machine_lib_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
				$yarn_type_from_prod = return_library_array("select id,yarn_type from  product_details_master where item_category_id=1 ", "id", "yarn_type");
				foreach ($machine_lib_sql as $row)
				{
					$dya_gauge_arr[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
					$dya_gauge_arr[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
					$dya_gauge_arr[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
				}

				$receive_basis = sql_select("select a.receive_basis from inv_receive_master a,pro_roll_details b where a.id=b.mst_id  and (a.booking_id='" . $booking_no_id . "' OR b.barcode_no in($barcode_no)) and a.entry_form in(2,22) and b.entry_form in(2,22) group by a.receive_basis");

				foreach ($receive_basis as $val)
				{
					$receive_basis_arr[$val[csf("receive_basis")]];
				}

				//$receivebasis = array_unique(explode(",", $receive_basis));

				$receivebasis = array_filter($receive_basis_arr);

				foreach ($receivebasis as $rcvid)
				{
					if ($rcvid == 0 || $rcvid == 1 || $rcvid == 11) {
						$machine_info = "d.machine_dia,d.machine_gg,";
					} else {
						$machine_info = "";
					}
				}
				if ($db_type == 0)
				{
					$sql_dtls = "SELECT a.booking_no_id,a.color_id,e.receive_basis,e.booking_id, a.booking_without_order,a.is_sales, SUM(b.batch_qnty) AS batch_qnty, sum(b.roll_no) as roll_nox, count(b.barcode_no) as roll_no, d.febric_description_id,d.gsm, b.item_description, b.program_no, b.prod_id, b.body_part_id, b.width_dia_type, count(b.width_dia_type) as num_of_rows,$machine_info d.machine_no_id, group_concat(d.yarn_lot) as yarn_lot, group_concat(d.yarn_prod_id) as yarn_prod_id, group_concat(d.yarn_count) as yarn_count, d.stitch_length as stitch_length, group_concat(d.brand_id) as brand_id, e.knitting_source, e.knitting_company, group_concat(c.barcode_no) as barcode_no, d.gsm, d.width
					from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
					where a.id=b.mst_id and b.barcode_no=c.barcode_no and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$data[0] and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22) and c.entry_form  in(2,22)
					group by a.booking_no_id,a.color_id,e.booking_id,$machine_info a.booking_without_order,a.is_sales,e.receive_basis,b.prod_id,b.program_no,b.body_part_id,b.item_description,b.width_dia_type, d.machine_no_id, d.stitch_length,d.febric_description_id,d.gsm, e.knitting_source, e.knitting_company, d.gsm, d.width order by b.program_no";
				}
				else
				{
					$sql_dtls= "SELECT a.booking_no_id,a.color_id,e.receive_basis,e.booking_id,a.booking_without_order,a.is_sales, SUM(b.batch_qnty) AS batch_qnty, sum(b.roll_no) as roll_nox, count(b.barcode_no) as roll_no, d.febric_description_id,d.gsm, b.item_description, b.program_no, b.prod_id, b.body_part_id, b.width_dia_type, count(b.width_dia_type) as num_of_rows, d.machine_no_id,$machine_info LISTAGG(CAST(d.yarn_prod_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_prod_id) as yarn_prod_id, LISTAGG(CAST(d.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) as yarn_lot, LISTAGG(CAST(d.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_count) as yarn_count, d.stitch_length as stitch_length, LISTAGG(CAST(d.brand_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.brand_id) as brand_id, e.knitting_source, e.knitting_company, LISTAGG(CAST(c.barcode_no AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY c.barcode_no) as barcode_no, d.gsm, d.width
					from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
					where a.id=b.mst_id and b.barcode_no=c.barcode_no and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$data[0] and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22) and c.entry_form  in(2,22)
					group by a.booking_no_id,a.color_id,e.receive_basis,$machine_info e.booking_id,a.booking_without_order,a.is_sales,b.prod_id,b.program_no,b.body_part_id,b.item_description,b.width_dia_type, d.machine_no_id, d.stitch_length,d.febric_description_id,d.gsm, e.knitting_source, e.knitting_company, d.gsm, d.width order by b.program_no";
					//and b.roll_id=c.id
				}

				// echo $sql_dtls;//die;
				$sql_result = sql_select($sql_dtls);
				foreach ($sql_result as $row)
				{
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					$desc = explode(",", $row[csf('item_description')]);
					$receive_basis_id=$row[csf('receive_basis')];

					$all_barcode .= $row[csf("barcode_no")] . ",";

					$yarn_prod_id = array_unique(explode(",", $row[csf('yarn_prod_id')]));
					$yarn_lot = implode(",", array_unique(explode(",", $row[csf('yarn_lot')])));
					$y_count = array_unique(explode(",", $row[csf('yarn_count')]));
					$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
					$yarn_type_value = "";
					foreach ($yarn_prod_id as $tid) {
						if ($tid > 0) {
							if ($yarn_type_value == '') $yarn_type_value = $yarn_type[$yarn_type_from_prod[$tid]]; else $yarn_type_value .= ", " . $yarn_type[$yarn_type_from_prod[$tid]];
						}
					}
					$yarn_count_value = "";
					foreach ($y_count as $val)
					{
						if ($val > 0) {
							if ($yarn_count_value == '') $yarn_count_value = $yarncount[$val]; else $yarn_count_value .= ", " . $yarncount[$val];
						}
					}
					$brand_value = "";
					foreach ($brand_id as $bid)
					{
						if ($bid > 0) {
							if ($brand_value == '') $brand_value = $brand_name_arr[$bid]; else $brand_value .= ", " . $brand_name_arr[$bid];
						}
					}
					if ($row[csf('receive_basis')] == 0 || $row[csf('receive_basis')] == 1 || $row[csf('receive_basis')] == 11) //from Entry page
					{
						$machine_dia_width = $row[csf('machine_dia')];
						$machine_gauge = $row[csf('machine_gg')];
					}
					else if ($row[csf('receive_basis')] == 2) //Knitting Plan
					{
						$prog_sql = "SELECT a.color_type_id,a.booking_no,b.color_id,b.id as program_id, b.id as program_no,c.body_part_id FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b,ppl_planning_entry_plan_dtls c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id =".$row[csf('program_no')];
						$prog_result = sql_select($prog_sql);
						foreach ($prog_result as $row2)
						{
							$prog_wise_color_type_array[$row2[csf('program_no')]] = $row2[csf('color_type_id')];
						}

						$program_data = sql_select("select width_dia_type, machine_dia, machine_gg, machine_id from ppl_planning_info_entry_dtls where id='" . $row[csf('booking_id')] . "'");

						$machine_dia_width = $program_data[0][csf('machine_dia')];
						$machine_gauge = $program_data[0][csf('machine_gg')];
					}

					$stitch = implode(",", array_unique(explode(",", $row[csf('stitch_length')])));

					$dya_gage = $machine_dia_width . "X" . $machine_gauge;

					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="60" align="center" style="word-break:break-all;"><? echo $row[csf('program_no')]; ?></td>
						<?
						if ($row[csf('knitting_source')] == 1) {
							$machin_knit_com = $machine_library[$row[csf('machine_no_id')]];
						} else {
							$machin_knit_com = $supplier_library[$row[csf('knitting_company')]];
						}
						?>
						<td width="80" title="<? echo $row[csf('prod_id')].'='.$row[csf('po_id')];?>" style="word-break:break-all;" align="center"><? echo $machin_knit_com; ?></td>
						<td width="80" style="word-break:break-all;"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
						<td width="150" style="word-break:break-all;"><? echo $desc[0] . "," . $desc[1]; ?></td>
						<td width="50" align="center" style="word-break:break-all; font-size:14px;"><? echo $row[csf('gsm')]//$desc[2]; ?></td>
						<td width="50" align="center" style="word-break:break-all; font-size:14px;"><? echo $row[csf('width')];//$desc[3]; ?></td>
						<td width="70" align="center" style="word-break:break-all;"><? echo $dya_gage; ?></td>
						<td width="70" style="word-break:break-all; font-size:14px;"><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></td>
						<td width="60" align="center" style="word-break:break-all;"><? echo $stitch; ?></td>
						<td width="70" align="right" style="word-break:break-all;"><? echo number_format($row[csf('batch_qnty')], 2); ?></td>
						<td align="center" width="50" style="word-break:break-all;"><? echo $row[csf('roll_no')]; ?></td>
						<td width="80" style="word-break:break-all;"><? echo implode(',', array_unique(explode(",", $yarn_lot))); ?></td>
						<td width="80" style="word-break:break-all;"> <? echo $brand_value; ?></td>
						<td width="80" style="word-break:break-all; font-size:14px;"align="center" ><? echo $yarn_count_value; ?></td>
						<td width="70" style="word-break:break-all; font-size:14px;"align="center" ><? echo $yarn_type_value; ?></td>
					</tr>
					<?php
					// $all_barcode[]=$row[csf('barcode_no')];
					$total_roll_number += $row[csf('roll_no')];
					$total_batch_qty += $row[csf('batch_qnty')];
					$i++;
				}
				//$all_barcode = implode(",",$all_barcode);
				$all_barcode = implode(", ", array_unique(explode(",", chop($all_barcode, ","))));
				?>
				<tr>
	                <td style="border:none; word-break:break-all;" colspan="9"  valign="top"><p><span
					style="font-weight:bold;">Roll Id : </span> <? echo $all_barcode; ?></p></td>
	                <td style="border:none;"  align="right"><b>Sum:</b> <? //echo $b_qty;
	                ?> </td>
	                <td align="right"><b><? echo number_format($total_batch_qty, 2); ?> </b></td>
	                <td align="center"><b><? echo $total_roll_number; ?> </b></td>
	                <td colspan="4" style="border:none;">&nbsp;</td>
	            </tr>
	            <tr>
	                <td style="border:none;" colspan="10" align="right"><b>Trims Weight:</b> <? //echo $b_qty;
	                ?> </td>
	                <td align="right"><b><? echo number_format($dataArray[0][csf('total_trims_weight')], 2); ?> </b>
	                </td>
	                <td colspan="5" style="border:none;">&nbsp;</td>
	            </tr>
	            <tr>
	            	<td style="border:none;" colspan="10" align="right"><b>Total:</b></td>
	            	<td align="right"><b><? echo number_format($total_batch_qty + $dataArray[0][csf('total_trims_weight')], 2); ?> </b></td>
	            	<td colspan="5" style="border:none;">&nbsp;</td>
	            </tr>
	            <tr>
	            	<td colspan="16" align="right">&nbsp; </td>
	            </tr>
	            <tr>

	                <td colspan="10" valign="top">
	                <br/>
					<?php
					$body_part_type = return_library_array("select id, body_part_type from lib_body_part", 'id', 'body_part_type');
					$sql_dtls = "select a.booking_no_id,e.receive_basis,e.booking_id,a.booking_without_order,a.is_sales, b.batch_qnty AS batch_qnty, b.roll_no as roll_no, b.item_description, b.program_no, b.prod_id, b.body_part_id, b.width_dia_type, b.width_dia_type as num_of_rows, d.machine_no_id,d.machine_dia,d.machine_gg, $machine_info d.yarn_lot,d.yarn_count,d.brand_id,c.barcode_no, d.stitch_length as stitch_length,  e.knitting_source, e.knitting_company,c.qc_pass_qnty_pcs ,c.coller_cuff_size
					from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d, inv_receive_master e
					where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$data[0] and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
					order by b.program_no";

					//echo $sql_dtls;die;
					$sql_result = sql_select($sql_dtls);
					//$coller_cuff_data_arr=array();
					$all_barcode = "";
					foreach ($sql_result as $row)
					{

						$data_array_1[$row[csf('body_part_id')]][$row[csf('item_description')]]["qnty"] += $row[csf('batch_qnty')];
						$data_array_1[$row[csf('body_part_id')]][$row[csf('item_description')]]["receive_basis"]= $row[csf('receive_basis')];
						if($count_num_of_rows[$row[csf('body_part_id')]."*".$row[csf('item_description')]] == "")
						{
							$count_num_of_rows[$row[csf('body_part_id')]."*".$row[csf('item_description')]] = $row[csf('body_part_id')]."*".$row[csf('item_description')];
							$data_array_1[$row[csf('body_part_id')]][$row[csf('item_description')]]["num_of_rows"] = 1;
						}
						else
						{
							$data_array_1[$row[csf('body_part_id')]][$row[csf('item_description')]]["num_of_rows"]++;
						}

						if ($row[csf('knitting_source')] == 1)
						{
							$machin_knit_com = $machine_library[$row[csf('machine_no_id')]];
						}
						else
						{
							$machin_knit_com = $supplier_library[$row[csf('knitting_company')]];
						}

						if($body_part_type[$row[csf('body_part_id')]] == 40 || $body_part_type[$row[csf('body_part_id')]] == 50){
							if($row[csf('coller_cuff_size')] != "" || $row[csf('grey_used_qty')] != "")
							{
								$coller_cuff_data_arr[$body_part_type[$row[csf('body_part_id')]]][$row[csf('coller_cuff_size')]]['collor_cuff_size']=$row[csf('coller_cuff_size')];
								$coller_cuff_data_arr[$body_part_type[$row[csf('body_part_id')]]][$row[csf('coller_cuff_size')]]['qc_pass_qnty_pcs']+=$row[csf('qc_pass_qnty_pcs')];
							}
						}


						$data_array_2[$row[csf('program_no')]]["machine_no"] .= $machin_knit_com.",";
						$data_array_2[$row[csf('program_no')]]["width_dia_type"] .= $fabric_typee[$row[csf('width_dia_type')]].",";
						$data_array_2[$row[csf('program_no')]]["stitch_length"] .= $row[csf('stitch_length')].",";
						$data_array_2[$row[csf('program_no')]]["yarn_lot"] .= $row[csf('yarn_lot')].",";
						//$data_array_2[$row[csf('program_no')]]["brand_id"] .= $brand_name_arr[$row[csf('brand_id')]].",";
						foreach(explode(",",$$row[csf('yarn_lot')]) as $single_lot)
						{
							$data_array_2[$row[csf('program_no')]]["brand_id"] .= $brandSupplier[$single_lot].",";
						}
						$data_array_2[$row[csf('program_no')]]["yarn_count"] .= $row[csf('yarn_count')].",";
						$data_array_2[$row[csf('program_no')]]["machine_dia_guage"] .= $row[csf('machine_dia')]."X".$row[csf('machine_gg')].",";
					}


						if(count($coller_cuff_data_arr)>0)
						{

							ksort($coller_cuff_data_arr);
							foreach($coller_cuff_data_arr as $key=>$coller_cuff_data)
							{
								if($key==40) $coller_cuff_body_part="Coller"; else $coller_cuff_body_part="Cuff";
								?>

									<table cellspacing="0" border="1" rules="all" class="rpt_table" width="200">
										<tr>
											<th colspan="2"><strong><?php  echo $coller_cuff_body_part; ?> </strong></th>
										</tr>
										<tr>
											<th  width="100"><strong>Size </strong></th>
											<th width="100"><strong>Pcs </strong></th>
										</tr>
										<?php

										foreach($coller_cuff_data as $barcode=>$coller_cuff_single)
										{

											?>
											<tr>
												<td  width="100"><?php echo  $coller_cuff_single['collor_cuff_size'];?></td>
												<td width="100"><?php echo $coller_cuff_single['qc_pass_qnty_pcs'];?></td>
											</tr>

											<?php
										}

										?>
									</table>

								<?php
							}

						}
						?>
	            	</td>
	            	<td colspan="6">
	            		<?
	            		$save_str = $dataArray[0][csf('SAVE_STRING')];
	            		$save_ref = explode("!!", $save_str);
	            		if (!empty($dataArray[0][csf('SAVE_STRING')]) > 0) {
	            			?>
	            			<table align="left" rules="all" class="rpt_table" width="100%" border="1" style="font-size: 12px;">
	            			 <thead>
	            				<tr>
	            					<th align="left" style="font-size:20px;" colspan="4"><strong>Trims Details</strong></th>
	            				</tr>
	            				<tr>
	            					<th width="50">SL</th>
	            					<th width="250">Item Description</th>
	            					<th width="150">Weight In Kg</th>
	            					<th>Remarks</th>
	            				</tr>
	            			</thead>
	            			<tbody>
	            				<?
	            				$i = 1;
	            				foreach ($save_ref as $data_ref) {
	            					$data_ref = explode("_", $data_ref);
	            					?>
	            					<tr>
	            						<td align="center"><? echo $i; ?></td>
	            						<td><? echo $data_ref[0]; ?> </td>
	            						<td align="right"><? echo number_format($data_ref[1], 2); ?></td>
	            						<td align="center"><? echo $data_ref[2]; ?></td>
	            					</tr>
	            					<?
	            					$i++;
	            				}
	            				?>
	            			</tbody>
	            		</table>
	            		<?
	            	}
	            	?>
	                </td>
		        </tr>
		        <tr>
		        	<td colspan="16" align="right">
		        		<?
		        		$process = $dataArray[0][csf('process_id')];
		        		$process_id = explode(',', $process);
							//print_r($process_id);
		        		$process_value = '';
		        		$i = 1;
		        		foreach ($process_id as $val) {
		        			if ($process_value == '') $process_value = $i . '. ' . $conversion_cost_head_array[$val]; else $process_value .= ", " . $i . '. ' . $conversion_cost_head_array[$val];
		        			$i++;
		        		}
		        		?>
		        		<table align="left" rules="all" class="rpt_table" width="1060" style="font-size: 17px;">
		        			<tr>
		        				<th align="left" style="font-size:20px;"><strong>Process Required</strong></th>
		        			</tr>
		        			<tr>
		        				<td style="font-size:20px;" title="<? echo $process_value; ?>">
		        					<p><? echo $process_value; ?></p>
		        				</td>
		        			</tr>
		        			<tr>
		        				<td align="left" style="font-size:19px;">
		        					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Loading Date & Time: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		        					&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		        					&nbsp; UnLoading Date & Time: &nbsp;
		        				</td>
		        			</tr>
		        		</table>
		                <br><br>
					<table cellspacing="0" border="1" rules="all" width="1060">
					<tr>
						<th colspan="2">Dyeing Parameter</th>
						<th colspan="2" align="center"><strong>Dyeing Finishing Information(<i>Hand	Written</i>)</strong>
						</th>
					</tr>
					<tr>
						<td width="12%">Rope Length</td>
						<td></td>
						<td width="33%">Dyeing</td>
						<td width="33%">Finishing</td>
					</tr>
					<tr>
						<td>Cycle Time</td>
						<td></th>
							<td rowspan="5">&nbsp;</td>
							<td rowspan="5">&nbsp;</td>
						</tr>
						<tr>
							<td>Reel Speed</td>
							<td></td>
						</tr>
						<tr>
							<td>Pump Speed</td>
							<td></td>
						</tr>
						<tr>
							<td>Gram/Meter</td>
							<td></td>
						</tr>
						<tr>
							<td>Operator</td>
							<td></td>
						</tr>
					</table>
		        	</td>
		        </tr>
    		</table>
		</td>
		</tr>
	</table>
	<br>
	<?
	echo get_spacial_instruction($batch_sl_no, "1060px",64);
	echo signature_table(52, $company, "1060px");
	?>
		</div>
	</div>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess;

			var btype = 'code39';
			var renderer = 'bmp';

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};

			value = {code: value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
	
		generateBarcode( '<? echo $dataArray[0][csf('batch_no')];  ?>');
	</script>
	
	<?
	exit();
}
?>