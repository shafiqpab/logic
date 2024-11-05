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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>', 'create_job_no_search_list_view', 'search_div', 'batch_wise_process_loss_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_booking_no_search_list_view', 'search_div', 'batch_wise_process_loss_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_batch_no_search_list_view', 'search_div', 'batch_wise_process_loss_report_controller', 'setFilterGrid(\'tbl_list\',-1)');" style="width:100px;" />
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
	$batch_no=str_replace("'","",$txt_batch_no);
	$buyer_name= str_replace("'","",$cbo_buyer_name);
	$cbo_year= str_replace("'","",$cbo_year);
	$hide_booking_id = str_replace("'","",$txt_hide_booking_id);
	$txt_booking_no = str_replace("'","",$txt_booking_no);
	$cbo_search_date= str_replace("'","",$cbo_search_date);
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
		$year_field="to_char(a.insert_date,'YYYY') as year";
		if($cbo_year!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";	
	}

	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ('$job_no') ";
	if ($batch_no=="") $batch_no_cond=""; else $batch_no_cond=" and a.batch_no in ('$batch_no') ";

	if ($txt_booking_no!='') $booking_no_cond="  and a.booking_no LIKE '%$txt_booking_no%'"; else $booking_no_cond="";
	if ($hide_booking_id!=0) $booking_no_cond.="  and a.booking_no_id in($hide_booking_id) "; else $booking_no_cond.="";
	if ($working_company==0) $workingCompany_cond=""; else $workingCompany_cond="  and a.working_company_id=".$working_company." ";
	if ($company_name==0) $workingCompany_cond.=""; else $workingCompany_cond.="  and a.company_id=".$company_name." ";

	if ($working_company==0) $knit_company_cond=""; else $knit_company_cond="  and a.knitting_company=".$working_company." ";
	if ($company_name==0) $knit_company_cond.=""; else $knit_company_cond.="  and a.company_id=".$company_name." ";

	$poDataArray=sql_select("SELECT b.id,$year_field, a.buyer_name,a.job_no_prefix_num,a.style_ref_no as style
	from  wo_po_break_down b,wo_po_details_master a 
	where  a.job_no=b.job_no_mst and b.status_active!=0 and b.is_deleted=0  $buyer_id_cond $job_no_cond $year_cond ");// $ship_date_cond
	$self_all_po_id='';
	$job_array=array(); $all_job_id='';
	foreach($poDataArray as $row)
	{
		$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
		$job_array[$row[csf('id')]]['style']=$row[csf('style')];
		if($self_all_po_id=="") $self_all_po_id=$row[csf('id')]; else $self_all_po_id.=",".$row[csf('id')];
	} //echo $all_po_id;

	// ========================================Start===========================================
	$finish_data_arr=array();
	$sql_dtls=sql_select("SELECT b.batch_id,sum(b.receive_qnty) as finish_qty	
	from inv_receive_master a,pro_finish_fabric_rcv_dtls b 
	where a.id=b.mst_id  and a.entry_form=7  and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $knit_company_cond 
	group by b.batch_id");
	foreach($sql_dtls as $row_fin)// for Finish Production
	{
		$finish_data_arr[$row_fin[csf('batch_id')]]['finish_qty']=$row_fin[csf('finish_qty')];
	}
	$delivery_data_arr=array();
	$sql_dtls=sql_select("select b.program_no as batch_id,sum(b.current_delivery) as delivery_qty from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b where  a.id=b.mst_id and a.entry_form=54 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $knit_company_cond  group by b.program_no");
	foreach($sql_dtls as $row_del)// for Loading time
	{
		$delivery_data_arr[$row_del[csf('batch_id')]]['delivery']=$row_del[csf('delivery_qty')];
	}
	//var_dump($finish_data_arr);die;

	// ========================================End============================================
	
	ob_start();
	$po_id_cond="";
	if($job_no!="" || str_replace("'","",$cbo_buyer_name)!=0 || $cbo_year!=0  || $start_date!="")
	{
		$po_id_cond=" $self_all_po_id";
	}

	$self_po_id_cond="";
	if($job_no!="" || str_replace("'","",$cbo_buyer_name)!=0 || $cbo_year!=0)
	{
		$self_po_id_cond=" $self_all_po_id";
		//echo  $self_po_id_cond.'D';
	}

	$subc_po_id_cond="";
	if($job_no!="" || str_replace("'","",$cbo_buyer_name)!=0 || $cbo_year!=0 )
	{
		$subc_po_id_cond=" $subc_all_po_id";
	}

	//echo $subc_all_po_id.'DDD';
	
	if($job_no!="" || str_replace("'","",$cbo_buyer_name)!=0 || $cbo_year!=0)
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
	}		
	//echo  $po_cond_for_in.'D';
						
	$type_cond="and a.entry_form=0";
	// Only for Self Batch -- Following Batch Progress Report
	if($cbo_search_date==1)
	{
		$sql_data="SELECT a.id,a.batch_no,a.floor_id,a.entry_form,a.batch_against,sum(b.batch_qnty) as batch_qty, a.batch_date,a.batch_weight, a.working_company_id,b.prod_id,b.po_id,a.color_id,a.booking_no,a.extention_no, b.item_description, a.booking_without_order  
		from pro_batch_create_mst a, pro_batch_create_dtls b 
		where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $batch_no_cond $date_cond $booking_no_cond $workingCompany_cond $type_cond $po_cond_for_in 
		group by a.batch_no,a.floor_id,a.batch_against,a.entry_form,a.id,a.batch_date,a.batch_weight, a.working_company_id,a.color_id,a.booking_no,a.extention_no,b.prod_id,b.po_id, b.item_description, a.booking_without_order  
		order by a.id";
		//echo $sql_data; //  and a.batch_against!=3
	}
	else if($cbo_search_date==2)
	{
		$sql_data="SELECT a.id,a.batch_no,a.floor_id,a.entry_form,a.batch_against,sum(b.batch_qnty) as batch_qty, c.process_end_date as batch_date,a.batch_weight, a.working_company_id,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.booking_no,a.extention_no, b.item_description, a.booking_without_order 
		from pro_batch_create_mst a, pro_batch_create_dtls b,pro_fab_subprocess c 
		where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and c.entry_form=35 and a.is_sales=0 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $batch_no_cond $date_cond $booking_no_cond $workingCompany_cond $type_cond $po_cond_for_in
		group by a.batch_no,a.floor_id,a.batch_against,a.entry_form,a.id,c.process_end_date,a.batch_weight, a.working_company_id, a.color_id,a.booking_no,a.extention_no,b.prod_id,b.po_id,b.width_dia_type, b.item_description, a.booking_without_order  
		order by a.id";		
		// echo $sql_data; //  and a.batch_against!=3
	}
	//echo $sql_data;

	$nameArray=sql_select($sql_data);
	$self_po_id="";
	foreach($nameArray as $row)
    {
		$batch_wise_process_arr[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['id']=$row[csf('id')];
		$batch_wise_process_arr[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['booking_no']=$row[csf('booking_no')];		
		$batch_wise_process_arr[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['color_id']=$row[csf('color_id')];
		$batch_wise_process_arr[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['batch_no']=$row[csf('batch_no')];
		$batch_wise_process_arr[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['item_description']=$row[csf('item_description')];
		$batch_wise_process_arr[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['extention_no']=$row[csf('extention_no')];
		$batch_wise_process_arr[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['po_id']=$row[csf('po_id')];
		$batch_wise_process_arr[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['working_company_id']=$row[csf('working_company_id')];
		$batch_wise_process_arr[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['working_company_id']=$row[csf('working_company_id')];

		$batch_wise_process_arr[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['batch_qty']+=$row[csf('batch_qty')];
		
		if($self_po_id=="") $self_po_id=$row[csf('po_id')]; else $self_po_id.=",".$row[csf('po_id')];
	}
	// echo "<pre>";
	// print_r($color_id_rowspan);die;

	// =======================Total Fabric Booking Qty (Fin.Fab.) with order Start ==============
	//$poIds=chop($self_po_id,','); 
	$poIds=implode(",", array_unique(explode(",",chop($self_po_id,','))));
	$po_cond_in="";
	$po_ids=count(array_unique(explode(",",$self_po_id)));
	if($db_type==2 && $po_ids>1000)
	{
		$po_cond_in=" and (";
		$poIdsArr=array_chunk(array_unique(explode(",",$poIds)),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$po_cond_in.=" b.po_break_down_id in($ids) or"; 
		}
		$po_cond_in=chop($po_cond_in,'or ');
		$po_cond_in.=")";
	}
	else
	{
		$po_cond_in=" and b.po_break_down_id in($poIds)";
	}

	$sql_booking="SELECT a.booking_no, b.fabric_color_id, b.construction, b.fin_fab_qnty, b.grey_fab_qnty 
	from wo_booking_mst a, wo_booking_dtls b
	where a.booking_no=b.booking_no and company_id=$company_name and b.booking_type=1 $po_cond_in and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$bookingArray=sql_select($sql_booking);
	$fab_booking_qty_arr=$grey_fab_booking_qnty_arr=array();
	foreach ($bookingArray as $value) 
	{
		$fab_booking_qty_arr[$value[csf('booking_no')]][$value[csf('fabric_color_id')]][$value[csf('construction')]]['fin_fab_qnty']+=$value[csf('fin_fab_qnty')];
		$grey_fab_booking_qnty_arr[$value[csf('booking_no')]][$value[csf('fabric_color_id')]][$value[csf('construction')]]['grey_fab_qnty']+=$value[csf('grey_fab_qnty')];
	}
	/*echo "<pre>";
	print_r($fab_booking_qty_arr);*/
	// =========================== Total Fabric Booking Qty (Fin.Fab.) with order End ===================


	?>
	<div>	
		<table width="1350" cellspacing="0" cellpadding="0" border="0" rules="all" >
		    <tr class="form_caption">
		        <td colspan="30" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
		    </tr>
		    <tr class="form_caption">
		        <td colspan="30" align="center"><?  if($company_name!=0) echo $company_library[$company_name];else echo $company_library[$working_company]; ?><br>
		        </b>
		        <?
				echo ($start_date == '0000-00-00' || $start_date == '' ? '' : change_date_format($start_date)).' To ';echo  ($end_date == '0000-00-00' || $end_date == '' ? '' : change_date_format($end_date));
		        ?> </b>
		        </td>
		    </tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1620" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="110">Buyer</th>
				<th width="80">Job No</th>
				<th width="120">Style</th>	
				<th width="110">Fabric Booking No</th>			
				<th width="90">Color Name</th>
				<th width="110">Total Fabric Booking Qty (Fin.Fab.)</th>
				<th width="110">Batch No</th>
				<th width="60">Ext.No</th>				
				<th width="70">Working Company</th>
				<th width="150">Fabrics Type</th>
				<th width="80">Batch Qty. (Gray Fab)</th>
				<th width="80"><p>Finis  Fab. Production Entry<p></th>
				<th width="100"><p>Delivery To Store</p></th>
				<th width="80"><p>Actul Process Loss Qty<p></th>
				<th width="80"><p>K&D  Process Loss</p></th>
				<th width="60"><p>Actual Process Loss %<p></th>
				<th width="">Process Los Status</th>
			</thead>
		</table>

		<div style="width:1640px; overflow-y:scroll; max-height:450px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1620" class="rpt_table" id="table_body">
				<?
				// =========================Booking, color row_span start====================
				foreach($batch_wise_process_arr as $booking_key=>$booking_value)
				{
					$booking_row_span=0;
					foreach ($booking_value as $color_id => $color_value)
					{
						$color_row_span=0;
						foreach ($color_value as $batch_id => $batch_no) 
						{
							foreach ($batch_no as $item_description => $row) 
							{
								$booking_row_span++; $color_row_span++;
							}
							$booking_rowspan_arr[$booking_key]=$booking_row_span;
							$color_rowspan_arr[$booking_key][$color_id]=$color_row_span;
						}
					}
				}
				//print_r($booking_rowspan_arr);	
				// ==================Booking, color row_span end============================		

			    $i=1;
			    $grand_total_booking_qty=$grand_total_batch_qty=$grand_finish_qty=$grand_delivery_qty=$grand_total_process_loss_qty=0;
				foreach($batch_wise_process_arr as $booking_key=>$booking_value)
				{
					$total_booking_qty=$total_batch_qty=$total_finish_qty=$total_delivery_qty=$total_process_loss_qty=0;
					$b=1; // booking row span increment
					foreach ($booking_value as $color_id => $color_value)
					{
						$c=1; // color row span increment
						foreach ($color_value as $batch_id => $batch_no) 
						{
							foreach ($batch_no as $item_description => $row) 
							{
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$po_id=rtrim($row[('po_id')],',');
							    $job_no=""; $buyer="";
							    $po_id=array_unique(explode(",",$po_id));
							    foreach($po_id as $id)
							    {
									if($row[('entry_form')]==36) //SubCon
									{

									}
									else
									{
										if($job_no=="") $job_no=$job_array[$id]['job']; else $job_no.=",".$job_array[$id]['job'];
										if($buyer=="") $buyer=$buyer_library[$job_array[$id]['buyer']]; else $buyer.=",".$buyer_library[$job_array[$id]['buyer']];
										if($style_no=="") $style_no=$job_array[$id]['style']; else $style_no.=",".$job_array[$id]['style'];
									}
							    }
								//echo $buyer.'sssss';
							    $job=implode(',',array_unique(explode(",",$job_no)));
							    $buyer_name=implode(',',array_unique(explode(",",$buyer)));
							    $style_no=implode(',',array_unique(explode(",",$style_no)));
								$desc = explode(",", $row['item_description']);
								//echo $row[('booking_no')].'**'.$row[('color_id')].'**'.$desc[0].'<br>';
								$fab_book_qty = $fab_booking_qty_arr[$row[('booking_no')]][$row[('color_id')]][$desc[0]]['fin_fab_qnty'];
								$grey_fab_book_qnty = $grey_fab_booking_qnty_arr[$row[('booking_no')]][$row[('color_id')]][$desc[0]]['grey_fab_qnty'];
								if($fab_book_qty>0)
								{
									$kd_process_loss=(($grey_fab_book_qnty-$fab_book_qty)/$fab_book_qty)*100;
								} else $kd_process_loss=0;
							
								$finish_qty=$finish_data_arr[$batch_id]['finish_qty'];
								$delivery_qty=$delivery_data_arr[$batch_id]['delivery'];

								$booking_rowspan=$booking_rowspan_arr[$booking_key];
								$color_rowspan=$color_rowspan_arr[$booking_key][$color_id];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trself_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trself_<? echo $i; ?>">
									<?
									if($b==1)
									{
									?>
									<td width="30" rowspan="<? echo $booking_rowspan;?>"><? echo $i; ?></td>
									<td width="110" rowspan="<? echo $booking_rowspan;?>"><p><? echo $buyer_name; ?></p></td>
									<td width="80" rowspan="<? echo $booking_rowspan;?>"><p><? echo $job; ?></p></td>
									<td width="120" rowspan="<? echo $booking_rowspan;?>" style="word-break: break-all; word-wrap: break-word;"><p><? echo $style_no; ?></p></td>
									<td width="110" rowspan="<? echo $booking_rowspan;?>"><p><? echo $row[('booking_no')]; ?></p></td>
									<?
									}

									if($c==1)
									{
									?>
									<td width="90" rowspan="<? echo $color_rowspan;?>"><p><? echo $color_library[$row[('color_id')]]; ?></p></td>
									<td width="110" rowspan="<? echo $color_rowspan;?>" align="right" title="<? echo $row[('booking_no')].'**'.$color_library[$row[('color_id')]].'**'.$desc[0]?>"><? echo number_format($fab_book_qty,2,'.',''); ?></td>
									<?
									}
									?>
									<td width="110" title="Batch ID=<? echo $batch_id;?>"><p><? echo $row[('batch_no')]; ?></p></td>
									<td width="60"><p><? echo $row[('extention_no')]; ?></p></td>
									<td width="70"><p><? echo $company_library[$row[('working_company_id')]]; ?></p></td>
									<td width="150"><p><? echo $desc[0];?></p></td>
									<td width="80" align="right"><p><?  echo number_format($row[('batch_qty')],2); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($finish_qty,2); ?></p></td>
									<td width="100" align="right"><p><? echo number_format($delivery_qty,2); ?></p></td>
									<td width="80" align="right"><p><? 
									if ( $finish_qty==$delivery_qty && $finish_qty!=0 && $delivery_qty!=0) 
									{ 
										echo number_format($actul_process_loss_qty=$row[('batch_qty')]-$finish_qty,2);
									} else{ echo $actul_process_loss_qty=0; } ?>
									</p></td>
									<td width="80" align="right" title="<? echo "(grey_fab_book_qnty($grey_fab_book_qnty)-fab_book_qty($fab_book_qty))/fab_book_qty($fab_book_qty)*100" ?>"><p><? echo number_format($kd_process_loss,2,'.',''); ?></p></td>
									<td width="60" align="right"><p><? echo number_format($actul_process_loss_qty/$row[('batch_qty')]*100,2); ?></p></td>
									<?
									if($actul_process_loss_qty<$kd_process_loss)
										$process_loss_status_color="style='background-color: green;'";
									elseif($actul_process_loss_qty>$kd_process_loss)
										$process_loss_status_color="style='background-color: red;'";
								 	?>
									<td <? echo $process_loss_status_color; ?>><p>
										<? 
										if($actul_process_loss_qty<$kd_process_loss)
											$process_loss_status="Decrease";
										elseif($actul_process_loss_qty>$kd_process_loss)
											$process_loss_status="Increase";
										echo $process_loss_status;
									 	?></p>
									</td>
							    </tr>
							    <?
								$total_booking_qty+=$fab_book_qty;
								$total_batch_qty+=$row[('batch_qty')];
								$total_finish_qty+=$finish_qty;
								$total_delivery_qty+=$delivery_qty;
								$total_process_loss_qty+=$actul_process_loss_qty;
								
							    $i++; $b++; $c++;
							}
							
						}
				    }
				    ?>
				    <tr class="tbl_bottom">
						<td colspan="6" align="right"><strong>Booking Total:</strong></td>
						<td width="110" align="right"><strong><? echo number_format($total_booking_qty,2,'.',''); ?></strong></td>
						<td width="110" align="right"><strong><?  ?></strong></td>
						<td width="60"></td>
						<td width="70"></td>    
						<td width="150"></td>
						<td width="80" align="right"><strong><? echo $total_batch_qty; ?></strong></td>
						<td width="80" align="right"><strong><? echo $total_finish_qty; ?></strong></td>
						<td width="100" align="right"><strong><? echo $total_delivery_qty; ?></strong></td>
						<td width="80" align="right"><strong><? echo $total_process_loss_qty; ?></strong></td>	       
						<td width="80"></td>
						<td width="60"></td>
						<td width=""></td>
					</tr>
				    <?
				    $grand_total_booking_qty+=$total_booking_qty;
				    $grand_total_batch_qty+=$total_batch_qty;
				    $grand_finish_qty+=$total_finish_qty;
				    $grand_delivery_qty+=$total_delivery_qty;
				    $grand_total_process_loss_qty+=$total_process_loss_qty;
				}
			    ?>
			</table>

			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1620" class="rpt_table" id="report_table_footer">
				<tfoot>
					<th width="30"></th>
					<th width="110"></th>
					<th width="80"></th>
					<th width="120"></th>
					<th width="110"></th>
					<th width="90">Grand Total: </th>
					<th width="110" align="right"><strong><? echo number_format($grand_total_booking_qty,2,'.',''); ?></strong></th>
					<th width="110" align="right"><strong><?  ?></strong></th>  
					<th width="60"></th>
					<th width="70"></th>    
					<th width="150"></th>
					<th width="80" align="right"><strong><? echo $grand_total_batch_qty; ?></strong></th> 
					<th width="80" align="right"><strong><? echo $grand_finish_qty; ?></strong></th>
					<th width="100" align="right"><strong><? echo $grand_delivery_qty; ?></strong></th>
					<th width="80" align="right"><strong><? echo $grand_total_process_loss_qty; ?></strong></th>       
					<th width="80"></th>
					<th width="60"></th>
					<th width=""></th>
				</tfoot>
			</table>
		</div>	
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

?>	
