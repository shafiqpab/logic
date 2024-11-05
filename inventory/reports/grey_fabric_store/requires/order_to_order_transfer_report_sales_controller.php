
<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
	$permission=$_SESSION['page_permission'];

	$data=$_REQUEST['data'];
	$action=$_REQUEST['action'];

	if($db_type==0)
	{
		$select_year="year";
		$year_con="";
	}
	else
	{
		$select_year="to_char";
		$year_con=",'YYYY'";
	}

/*	
	$composition_arr=array();
	$construction_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$construction_arr))
			{
				$construction_arr[$row[csf('id')]]=$construction_arr[$row[csf('id')]];
			}
			else
			{
				$construction_arr[$row[csf('id')]]=$row[csf('construction')];
			}
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]];
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}
*/

	if ($action=="load_drop_down_buyer")
	{
		echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ,0);
		exit();
	}

    if ($action == "load_drop_down_cust_buyer") 
    {
        echo create_drop_down("cbo_cust_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Cust Buyer --", $selected, "", 0);
       
        exit();
    }

	if ($action=="load_drop_down_store")
	{
		echo create_drop_down( "cbo_store_name", 100, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$data and a.status_active=1 and a.is_deleted=0 and b.category_type=13 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
		exit();
	}



if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Sales Order Info","../../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(booking_data)
		{
			document.getElementById('hidden_booking_data').value=booking_data;
			parent.emailwindow.hide();
		}

	</script>
</head>
<body>
	<div align="center">
		<fieldset style="width:820px;margin-left:4px;">
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Within Group</th>
						<th>Search By</th>
						<th>Search</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" value="">
						</th> 
					</thead>
					<tr class="general">
						<td align="center"><? echo create_drop_down( "cbo_within_group", 150, $yes_no,"",1, "--Select--", "",$dd,0 ); ?></td>   
						<td align="center">	
							<?
							$search_by_arr=array(1=>"Sales Order No",2=>"Sales / Booking No",3=>"Style Ref.");
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>                 
						<td align="center">				
							<input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
						</td> 						
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('cbo_within_group').value, 'create_order_no_search_list_view', 'search_div', 'order_to_order_transfer_report_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
				</table>
				<div id="search_div" style="margin-top:10px"></div>   
			</form>
		</fieldset>
	</div>
</body>           
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_order_no_search_list_view")
{
	$data=explode('_',$data);
	
	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	
	$search_string=trim($data[0]);
	$search_by =$data[1];
	$company_id =$data[2];
	$within_group=$data[3];
	
	$search_field_cond='';
	if($search_string!="")
	{
		if($search_by==1) $search_field_cond=" and job_no like '%".$search_string."'";
		else if($search_by==2) $search_field_cond=" and sales_booking_no like '%".$search_string."'";
		else $search_field_cond=" and style_ref_no like '".$search_string."%'";
	}

	if($within_group==0) $within_group_cond=""; else $within_group_cond=" and within_group=$within_group";
	
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	$booking_arr = array();
	$booking_info = sql_select("select a.id,a.booking_no, a.booking_type, a.company_id, a.entry_form, a.fabric_source, a.item_category, a.job_no, a.po_break_down_id, a.is_approved, is_short from wo_booking_mst a where a.is_deleted = 0 and a.status_active=1");
	foreach ($booking_info as $row) {
		$booking_arr[$row[csf('booking_no')]]['id'] = $row[csf('id')];
		$booking_arr[$row[csf('booking_no')]]['booking_no'] = $row[csf('booking_no')];
		$booking_arr[$row[csf('booking_no')]]['booking_type'] = $row[csf('booking_type')];
		$booking_arr[$row[csf('booking_no')]]['company_id'] = $row[csf('company_id')];
		$booking_arr[$row[csf('booking_no')]]['entry_form'] = $row[csf('entry_form')];
		$booking_arr[$row[csf('booking_no')]]['fabric_source'] = $row[csf('fabric_source')];
		$booking_arr[$row[csf('booking_no')]]['item_category'] = $row[csf('item_category')];
		$booking_arr[$row[csf('booking_no')]]['job_no'] = $row[csf('job_no')];
		$booking_arr[$row[csf('booking_no')]]['po_break_down_id'] = $row[csf('po_break_down_id')];
		$booking_arr[$row[csf('booking_no')]]['is_approved'] = $row[csf('is_approved')];
		$booking_arr[$row[csf('booking_no')]]['is_short'] = $row[csf('is_short')];
	}
	$sql = "select id, $year_field, job_no_prefix_num, job_no, within_group, sales_booking_no, booking_date, buyer_id, style_ref_no, location_id from fabric_sales_order_mst where status_active=1 and is_deleted=0 and company_id=$company_id $within_group_cond $search_field_cond order by id DESC"; 

	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" align="left">
		<thead>
			<th width="40">SL</th>
			<th width="90">Sales Order No</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">Buyer</th>               
			<th width="120">Sales/ Booking No</th>
			<th width="80">Booking date</th>
			<th width="110">Style Ref.</th>
			<th>Location</th>
		</thead>
	</table>
	<div style="width:800px; max-height:300px; overflow-y:scroll; float:left;" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_list_search" align="left">  
			<?
			$i=1;
			foreach ($result as $row)
			{  
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($row[csf('within_group')]==1)
					$buyer=$company_arr[$row[csf('buyer_id')]]; 
				else
					$buyer=$buyer_arr[$row[csf('buyer_id')]];

				$booking_data =$row[csf('id')]."**".$row[csf('job_no')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $booking_data; ?>');"> 
					<td width="40"><? echo $i; ?></td>
					<td width="90"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
					<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="80"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
					<td width="70"><p><? echo $buyer; ?>&nbsp;</p></td>
					<td width="120"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>               
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<td><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<?	
	exit();	
}

if($action=="booking_no_search_popup")
{
	echo load_html_head_contents("Sales Order Info","../../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(booking_data)
		{
			document.getElementById('hidden_booking_data').value=booking_data;
			parent.emailwindow.hide();
		}

	</script>
	</head>
	<body>
		<div align="center">
			<fieldset style="width:820px;margin-left:4px;">
				<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
					<table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
						<thead>
							<th>Within Group</th>
							<th>Search By</th>
							<th>Search</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
								<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" value="">
							</th> 
						</thead>
						<tr class="general">
							<td align="center"><? echo create_drop_down( "cbo_within_group", 150, $yes_no,"",1, "--Select--", "",$dd,0 ); ?></td>   
							<td align="center">	
								<?
								$search_by_arr=array(1=>"Sales Order No",2=>"Sales / Booking No",3=>"Style Ref.");
								echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", 2,$dd,0 );
								?>
							</td>                 
							<td align="center">				
								<input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
							</td> 						
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('cbo_within_group').value, 'create_booking_no_search_list_view', 'search_div', 'order_to_order_transfer_report_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
							</td>
						</tr>
					</table>
					<div id="search_div" style="margin-top:10px"></div>   
				</form>
			</fieldset>
		</div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_booking_no_search_list_view")
{
	$data=explode('_',$data);
	
	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	
	$search_string=trim($data[0]);
	$search_by =$data[1];
	$company_id =$data[2];
	$within_group=$data[3];
	
	$search_field_cond='';
	if($search_string!="")
	{
		if($search_by==1) $search_field_cond=" and job_no like '%".$search_string."'";
		else if($search_by==2) $search_field_cond=" and sales_booking_no like '%".$search_string."'";
		else $search_field_cond=" and style_ref_no like '".$search_string."%'";
	}

	if($within_group==0) $within_group_cond=""; else $within_group_cond=" and within_group=$within_group";
	
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	$booking_arr = array();
	$booking_info = sql_select("select a.id,a.booking_no, a.booking_type, a.company_id, a.entry_form, a.fabric_source, a.item_category, a.job_no, a.po_break_down_id, a.is_approved, is_short from wo_booking_mst a where a.is_deleted = 0 and a.status_active=1");
	foreach ($booking_info as $row) {
		$booking_arr[$row[csf('booking_no')]]['id'] = $row[csf('id')];
		$booking_arr[$row[csf('booking_no')]]['booking_no'] = $row[csf('booking_no')];
		$booking_arr[$row[csf('booking_no')]]['booking_type'] = $row[csf('booking_type')];
		$booking_arr[$row[csf('booking_no')]]['company_id'] = $row[csf('company_id')];
		$booking_arr[$row[csf('booking_no')]]['entry_form'] = $row[csf('entry_form')];
		$booking_arr[$row[csf('booking_no')]]['fabric_source'] = $row[csf('fabric_source')];
		$booking_arr[$row[csf('booking_no')]]['item_category'] = $row[csf('item_category')];
		$booking_arr[$row[csf('booking_no')]]['job_no'] = $row[csf('job_no')];
		$booking_arr[$row[csf('booking_no')]]['po_break_down_id'] = $row[csf('po_break_down_id')];
		$booking_arr[$row[csf('booking_no')]]['is_approved'] = $row[csf('is_approved')];
		$booking_arr[$row[csf('booking_no')]]['is_short'] = $row[csf('is_short')];
	}
	$sql = "select id, $year_field, job_no_prefix_num, job_no, within_group, sales_booking_no, booking_date, buyer_id, style_ref_no, location_id from fabric_sales_order_mst where status_active=1 and is_deleted=0 and company_id=$company_id $within_group_cond $search_field_cond order by id DESC"; 

	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" align="left">
		<thead>
			<th width="40">SL</th>
			<th width="90">Sales Order No</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">Buyer</th>               
			<th width="120">Sales/ Booking No</th>
			<th width="80">Booking date</th>
			<th width="110">Style Ref.</th>
			<th>Location</th>
		</thead>
	</table>
	<div style="width:800px; max-height:300px; overflow-y:scroll; float:left;" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_list_search" align="left">  
			<?
			$i=1;
			foreach ($result as $row)
			{  
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($row[csf('within_group')]==1)
					$buyer=$company_arr[$row[csf('buyer_id')]]; 
				else
					$buyer=$buyer_arr[$row[csf('buyer_id')]];

				$booking_data =$row[csf('sales_booking_no')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $booking_data; ?>');"> 
					<td width="40"><? echo $i; ?></td>
					<td width="90"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
					<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="80"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
					<td width="70"><p><? echo $buyer; ?>&nbsp;</p></td>
					<td width="120"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>               
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<td><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<?	
	exit();	
}

/*
|--------------------------------------------------------------------------
| item_description_search
|--------------------------------------------------------------------------
|
*/
if($action=="item_description_search")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array();
		var selected_name = new Array();
		var selected_no = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
			for( var i = 1; i <= tbl_row_count; i++ )
			{
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );

			}
		}

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style )
			{ 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( strCon ) 
		{
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( selectID, selected_id ) == -1 )
			{
				selected_id.push( selectID );
				selected_name.push( selectDESC );
				selected_no.push(str);					
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == selectID )
						break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 ); 
				selected_no.splice( i, 1 );
			}
			
			var id = '';
			var name = '';
			var job = '';
			var num='';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_no[i] + ','; 
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 );
			num 	= num.substr( 0, num.length - 1 ); 
			
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name ); 
			$('#txt_selected_no').val( num );
		}

		function fn_check_lot()
		{ 
			show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+'<? echo $company; ?>'+'_'+document.getElementById('txt_prod_id').value, 'create_lot_search_list_view', 'search_div', 'order_to_order_transfer_report_sales_controller', 'setFilterGrid("list_view",-1)');
		}
	</script>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<tr>                	 
							<th>Search By</th>
							<th align="center" width="180" id="search_by_td_up">Enter Item Description</th>
							<th align="center" width="120">Product Id</th>
							<th width="120">
								<input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  />
								<input type='hidden' id='txt_selected_id' />
								<input type='hidden' id='txt_selected' />
								<input type='hidden' id='txt_selected_no' />
							</th>
						</tr>
					</thead>
					<tbody>
						<tr align="center">
							<td align="center">
								<?php 
								$search_by = array(1=>'Item Description');
								$dd="";
								echo create_drop_down( "cbo_search_by", 150, $search_by, "", 0, "--Select--", "", "", 0);
								?>
							</td>
							<td  align="center" id="search_by_td">				
								<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">				
								<input type="text" style="width:90px" class="text_boxes"  name="txt_prod_id" id="txt_prod_id" />
							</td> 
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="fn_check_lot()" style="width:100px;" />				
							</td>
						</tr>
					</tbody>
					</tr>         
				</table>    
				<div align="center" valign="top" style="margin-top:5px" id="search_div"> </div> 
			</form>
		</div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script> 
	</html>
	<?
	exit();
}

/*
|--------------------------------------------------------------------------
| create_lot_search_list_view
|--------------------------------------------------------------------------
|
*/
if($action=="create_lot_search_list_view")
{
	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];
	$prod_id = $ex_data[3];
	
	$sql_cond = "";
	if(trim($txt_search_common) != "")
	{
		if(trim($txt_search_by) == 1) // for LOT NO
		{
			//$sql_cond = " AND product_name_details LIKE '%$txt_search_common%'";	 
			$sql_cond = " AND item_description LIKE '%$txt_search_common%'";	 
		}
		else if(trim($txt_search_by) == 2) // for Yarn Count
		{
			if($txt_search_common == 0)
			{
				$sql_cond = " ";	 	
			}
			else
			{
				$sql_cond = " AND item_group_id LIKE '%$txt_search_common%'";	 	
			}
		} 
	} 
	
	if($prod_id != "")
		$sql_cond .= " AND id = ".$prod_id."";
	
	$sql = "SELECT id, product_name_details, gsm, dia_width FROM product_details_master WHERE company_id IN(".$company.") AND item_category_id = 13 ".$sql_cond.""; 
	$arr=array();
	echo create_list_view("list_view", "Product Id, Item Description, GSM, Dia","70,230,100","550","260",0, $sql, "js_set_value", "id,product_name_details", "", 1, "0,0,0,0", $arr, "id,product_name_details,gsm,dia_width", "","","0","",1);
	exit();
}

if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name    =str_replace("'","",$cbo_company_name);
	$cbo_buyer_name      =str_replace("'","",$cbo_buyer_name);
	$cbo_cust_buyer_name =str_replace("'","",$cbo_cust_buyer_name);
    $txt_booking_no      =str_replace("'","",$txt_booking_no);
	$cbo_store_name      =str_replace("'","",$cbo_store_name);
	$txt_date_from       =str_replace("'","",$txt_date_from);
	$txt_date_to         =str_replace("'","",$txt_date_to);
	$txt_order           =str_replace("'","",$txt_order);
	$txt_order_id        =str_replace("'","",$txt_order_id);
	$cbo_order_type      =str_replace("'","",$cbo_order_type);
	$cbo_based_on        =str_replace("'","",$cbo_based_on);
	$cbo_knitting_source =str_replace("'","",$cbo_knitting_source);
	$product 			 =str_replace("'","",$txt_product);
	$productId 			 =str_replace("'","",$txt_product_id);
	$productNo 			 =str_replace("'","",$txt_product_no);
	$txt_style_no 		 =str_replace("'","",$txt_style_no);


	if($db_type==0)
	{
		$txt_date_from=change_date_format($txt_date_from,"yyyy-mm-dd");
		$txt_date_to=change_date_format($txt_date_to,"yyyy-mm-dd");
	}
	else
	{
		$txt_date_from=change_date_format($txt_date_from,"","",1);
		$txt_date_to=change_date_format($txt_date_to,"","",1);
	}

	$str_cond="";
	if($cbo_store_name>0) $str_cond.=" and c.store_id=$cbo_store_name";
	if($txt_order_id) $str_cond .=" and a.id =$txt_order_id";
	if($cbo_order_type==2) $str_cond .=" and a.booking_without_order =1";
	if($cbo_order_type==1) $str_cond .=" and a.booking_without_order !=1 and a.within_group=1";
    if($cbo_cust_buyer_name>0) $str_cona.=" and a.customer_buyer=$cbo_cust_buyer_name";

    if($txt_booking_no) $str_cond .=" and a.sales_booking_no in('$txt_booking_no')";
    // echo $str_cond;die;
	if($cbo_buyer_name>0)
	{
		$str_cond .= " and ((a.within_group = 2 and a.buyer_id = $cbo_buyer_name) or (a.within_group = 1 and a.po_buyer = $cbo_buyer_name)) ";
	}

	if($txt_date_from != "" && $txt_date_to != "")
	{
		if($cbo_based_on == 1) 
		{
			$date_cond = " and c.transaction_date between '$txt_date_from' and '$txt_date_to' ";
		}
		else
		{
			if($db_type==0)
			{
				if($txt_date_from!="" && $txt_date_to!="") $date_cond="and c.insert_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."  00:00:01' and '".change_date_format($txt_date_to,"yyyy-mm-dd")." 23:59:59' "; else $date_cond="";
			}else
			{
				if($txt_date_from!="" && $txt_date_to!="") $date_cond="and c.insert_date between '".date("j-M-Y",strtotime($txt_date_from))."  01:00:01 AM' and '".date("j-M-Y",strtotime($txt_date_to))."  11:59:59 PM' "; else $date_cond="";
			}
		}
	}

	//productIdCondition
	$productIdCondition = '';
	if($productId != '')
	{
		$expProductId = explode(",",$productId);
		if($db_type==2 && count($expProductId) >1000)
		{
			$productIdCondition = " AND (";
			$prodIdsArr=array_chunk($expProductId,999);
			foreach($prodIdsArr as $pids)
			{
				$pids=implode(",",$pids);
				$productIdCondition .= " b.prod_id IN(".$pids.") OR ";
			}
			$productIdCondition = chop($productIdCondition,'OR ');
			$productIdCondition .= ")";
		}
		else
		{
			$productIdCondition = " AND b.prod_id IN(".$productId.")";
		}
	}

	$company_sql = sql_select("select id, company_name, company_short_name from lib_company");
	foreach ($company_sql as  $val) 
	{
		$company_array[$val[csf("id")]] = $val[csf("company_name")];
		$company_short_array[$val[csf("id")]] = $val[csf("company_short_name")];
	}
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

	$con = connect();
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (2021,2022,2023,2024)");
	execute_query("delete from tmp_barcode_no where userid=$user_id");
	oci_commit($con);

	/*
	|--------------------------------------------------------------------------
	| for roll query
	| fso order to order transfer
	|--------------------------------------------------------------------------
	|
	*/
	$sqlNoOfRoll="SELECT a.job_no, a.sales_booking_no, a.customer_buyer, a.buyer_id, a.style_ref_no, a.company_id, a.remarks, b.prod_id, b.po_breakdown_id, b.trans_type, e.qnty, e.barcode_no, f.transfer_system_id, f.transfer_date, f.from_order_id, f.to_order_id 
	FROM fabric_sales_order_mst a, order_wise_pro_details b, inv_transaction c, inv_item_transfer_dtls d, pro_roll_details e, inv_item_transfer_mst f
	WHERE a.id=b.po_breakdown_id and b.trans_id=c.id and b.dtls_id=d.id and d.id=e.dtls_id and e.mst_id=f.id and c.mst_id=f.id and d.mst_id=f.id and b.status_active = 1 AND b.is_deleted = 0 AND b.entry_form IN(133) and e.entry_form=133 AND b.trans_type IN(5,6) AND c.status_active = 1 AND c.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 and e.is_sales=1 AND a.company_id=$cbo_company_name $str_cond $date_cond $productIdCondition";
	// echo $sqlNoOfRoll; die;
	$sqlNoOfRollResult = sql_select($sqlNoOfRoll);
	foreach($sqlNoOfRollResult as $row) // Transfered barcode insert into tmp_barcode_no table
	{
		$transbarcodearr[$row[csf("barcode_no")]] =$row[csf("barcode_no")];
		$order_arr[$row[csf("from_order_id")]] =$row[csf("from_order_id")];
		$order_arr[$row[csf("to_order_id")]] =$row[csf("to_order_id")];

		if( $barcode_no_check[$row[csf('barcode_no')]] =="" )
        {
            $barcode_no_check[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
            $barcodeno = $row[csf('barcode_no')];
            // echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_id,$barcodeno)";
            execute_query("insert into tmp_barcode_no (userid, barcode_no) values ($user_id,$barcodeno)");
        }
	}
	// echo "<pre>";print_r($order_arr);
	// ============================== Roll wise Grey Sales Order To Sales Order Transfer End =================
	
	// ============================== $productionBarcodeData Start ===========================================
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 2021, 1,$order_arr, $empty_arr);
	// fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 2022, 1,$transbarcodearr, $empty_arr);
	oci_commit($con);

	if(!empty($transbarcodearr))
	{
		$production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id,a.prod_id,b.booking_no, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, c.knitting_source, c.challan_no as production_challan,c.knitting_company, a.yarn_prod_id, a.body_part_id 
		from inv_receive_master c, pro_grey_prod_entry_dtls a, pro_roll_details b, tmp_barcode_no d
		where a.mst_id = c.id and a.id=b.dtls_id and b.barcode_no=d.barcode_no and d.userid=$user_id and c.entry_form = 2 and b.entry_form in(2) and a.trans_id=0 and a.status_active=1 and b.status_active=1");
		foreach ($production_sql as $row) 
		{
			$prodBarcodeData[$row[csf("barcode_no")]]["prod_basis"] =$row[csf("receive_basis")];
			$prodBarcodeData[$row[csf("barcode_no")]]["prog_book"] =$row[csf("booking_no")];
			$prodBarcodeData[$row[csf("barcode_no")]]["color_range_id"] =$row[csf("color_range_id")];
			$prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"] =$row[csf("yarn_lot")];
			$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"] =$row[csf("yarn_count")];
			$prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"] =$row[csf("yarn_prod_id")];
			$prodBarcodeData[$row[csf("barcode_no")]]["prod_id"] =$row[csf("prod_id")];
			$prodBarcodeData[$row[csf("barcode_no")]]["color_id"] =$row[csf("color_id")];
			$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"] =$row[csf("febric_description_id")];
			$prodBarcodeData[$row[csf("barcode_no")]]["gsm"] =$row[csf("gsm")];
			$prodBarcodeData[$row[csf("barcode_no")]]["width"] =$row[csf("width")];
			$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"] =$row[csf("stitch_length")];
			$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"] =$row[csf("machine_dia")];
			$prodBarcodeData[$row[csf("barcode_no")]]["machine_gg"] =$row[csf("machine_gg")];
			$prodBarcodeData[$row[csf("barcode_no")]]["machine_no_id"] =$row[csf("machine_no_id")];
			$prodBarcodeData[$row[csf("barcode_no")]]["prod_challan"] =$row[csf("production_challan")];
			$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"] =$row[csf("knitting_source")];
			$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"] =$row[csf("knitting_company")];
			$prodBarcodeData[$row[csf("barcode_no")]]["body_part_id"] =$row[csf("body_part_id")];

			$allDeterArr[$row[csf("febric_description_id")]] =$row[csf("febric_description_id")];
			$allColorArr[$row[csf("color_id")]] =$row[csf("color_id")];
		}
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 2023, 1,$allDeterArr, $empty_arr);
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 2024, 2,$allColorArr, $empty_arr);
		oci_commit($con);
	}
	// ============================== $productionBarcodeData End ==================

	// ============================== Receive Data Array Start ====================
	$data_array = array();$poArr = array();
	foreach ($sqlNoOfRollResult  as $val) 
	{
		if(($prodBarcodeData[$val[csf("barcode_no")]]["knitting_source"] == $cbo_knitting_source) || $cbo_knitting_source ==0)
		{
			$orderId = $val[csf('po_breakdown_id')];
			$febric_description_id=$prodBarcodeData[$val[csf("barcode_no")]]["febric_description_id"];
			$color_id=$prodBarcodeData[$val[csf("barcode_no")]]["color_id"];
			if ($color_id=="") 
			{
				$color_id=0;
			}

			$data_array[$val[csf("transfer_system_id")]][$febric_description_id][$color_id]["job_no"] = $val[csf("job_no")];
			$data_array[$val[csf("transfer_system_id")]][$febric_description_id][$color_id]["sales_booking_no"] = $val[csf("sales_booking_no")];
			$data_array[$val[csf("transfer_system_id")]][$febric_description_id][$color_id]["cust_buyer"] = $val[csf("customer_buyer")];
			$data_array[$val[csf("transfer_system_id")]][$febric_description_id][$color_id]["transfer_date"] = $val[csf("transfer_date")];

			if ($val[csf("trans_type")]==6) 
			{
				$data_array[$val[csf("transfer_system_id")]][$febric_description_id][$color_id]["to_order_id"] = $val[csf("to_order_id")];
				$data_array[$val[csf("transfer_system_id")]][$febric_description_id][$color_id]["trans_out_qty"] +=  $val[csf("qnty")];
			}
			else
			{
				$data_array[$val[csf("transfer_system_id")]][$febric_description_id][$color_id]["from_order_id"] = $val[csf("from_order_id")];
				$data_array[$val[csf("transfer_system_id")]][$febric_description_id][$color_id]["trans_in_qty"] +=  $val[csf("qnty")];
			}
		}
	}
	// echo '<pre>';print_r($data_array);
	// ============================== Receive Data Array End =======================
	
	// =================== for yarn_count_determination Start ======================
	$allDeterArr = array_filter($allDeterArr);
	if(!empty($allDeterArr))
	{
		$construction_arr=array(); $composition_arr=array();
		$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b, GBL_TEMP_ENGINE c where a.id=b.mst_id and a.id=c.ref_val and c.entry_form=2023 and c.user_id=$user_id and c.ref_from=1";
		$deter_array=sql_select($sql_deter);
		foreach( $deter_array as $row )
		{
			$construction_arr[$row[csf('id')]]=$row[csf('construction')];

			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
		unset($deter_array);
	}
	// =================== for yarn_count_determination end ========================

	// =================== for lib_color Start =====================================
	$allColorArr = array_filter($allColorArr);
	if(!empty($allColorArr))
	{
		$color_array=return_library_array( "SELECT b.id, b.color_name from GBL_TEMP_ENGINE a, lib_color b where b.status_active=1 and a.ref_val=b.id and a.entry_form=2024 and a.user_id=$user_id and a.ref_from=2 $allColorCond", "id", "color_name");
	}
	// =================== for lib_color end =======================================

	// =================== For FSO Start =======================================
	$fso_sql="SELECT b.id, b.job_no, b.sales_booking_no, b.customer_buyer, b.buyer_id, b.style_ref_no
	FROM GBL_TEMP_ENGINE a, fabric_sales_order_mst b
	WHERE a.ref_val=b.id and a.entry_form=2021 and a.user_id=$user_id and a.ref_from=1";
	// echo $fso_sql; die;
	$fso_sql_result = sql_select($fso_sql);
	foreach($fso_sql_result as $row)
	{
		$booking_no_arr[$row[csf("id")]]=$row[csf("sales_booking_no")];
		$fso_no_arr[$row[csf("id")]]=$row[csf("job_no")];
	}
	// =================== For FSO End =========================================
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (2021,2022,2023,2024)");
	execute_query("delete from tmp_barcode_no where userid=$user_id");
	oci_commit($con);

	ob_start();
	?>
	<!-- <style type="text/css">
		.word_wrap_break {
			word-wrap: break-word;
			word-break: break-all;
		}
	</style> -->
	<div style="width:1200px" id="main_body">
		<table width="1200" id="" align="left">
			<tr class="form_caption" style="border:none;">
				<td colspan="7" align="center" style="border:none;font-size:16px; font-weight:bold" >Order To Order Transfer Report Sales</td>
			</tr>
			<tr style="border:none;">
				<td colspan="7" align="center" style="border:none; font-size:14px;">
					Company Name : <? echo $company_array[str_replace("'","",$cbo_company_name)]; ?>
				</td>
			</tr>
		</table>
		<br />
		<table width="1180" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="110">FSO</th>
					<th width="120">Sales Job/ Booking No.</th>
					<th width="100">Cust Buyer</th>
					<th width="80">Color</th>
					<th width="100">Construction</th>
					<th width="100">Trans. Date</th>
					<th width="110">Transfer ID</th>
					<th width="80">Trans. In Qty</th>
					<th width="120">Trans. From</th>
					<th width="80">Trans. Out Qty</th>
					<th width="">Trans. To</th>
				</tr>
			</thead>
		</table>
		<div style="width:1200px; overflow-y: scroll; max-height:250px; float: left;" id="scroll_body">
			<table width="1180" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
				<tbody>
					<?
					$i=1;
					foreach($data_array as $transfer_k => $transfer_v)
					{
						foreach($transfer_v as $detar_id_k => $detar_id_v)
						{
							foreach($detar_id_v as $color_k => $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="30" align="center"><? echo $i;?></td>
	                                <td width="110"><p><? echo $row["job_no"];?></p></td>
	                                <td width="120"><p><? echo $row["sales_booking_no"];?></p></td>
	                                <td width="100" align="center"><p><? echo $buyer_arr[$row["cust_buyer"]];?></p></td>
	                                <td width="80" title="<?echo $color_k;?>">
                                		<p><? 
                                		$color_names="";
                                		foreach (explode(",",$color_k) as $key => $color) 
                                		{
                                			$color_names .= $color_array[$color].",";
                                		}
                                		echo chop($color_names,",");
                                		?>
                                		</p>
	                                </td>
	                                <td width="100" title="<?echo $detar_id_k;?>"><p><? echo $construction_arr[$detar_id_k];?></p></td>	                                
	                                <td width="100"><p><? echo change_date_format($row["transfer_date"]); ?></p></td>
	                                <td width="110"><p><? echo $transfer_k; ?></p></td>
	                                <td width="80" align="right"><p><? echo number_format($row["trans_in_qty"],2,'.',''); ?></p></td>
	                                <td width="120" align="center"><p><? echo $booking_no_arr[$row["from_order_id"]]; ?></p></td>
	                                <td width="80" align="right"><p><? echo number_format($row["trans_out_qty"],2,'.',''); ?></p></td>
	                                <td width="" align="center"><? echo $booking_no_arr[$row["to_order_id"]]; ?></td>
								</tr>
								<?
								$total_trans_in_qnty += $row["trans_in_qty"];
								$total_trans_out_qty += $row["trans_out_qty"];
								$i++;
							}
						}
					}				
					?>
				</tbody>
			</table>
		</div>
		<table width="1180" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all"  align="left">
			<tfoot>
				<tr>
					<th width="30"></th>
					<th width="110"></th>
					<th width="120"></th>
					<th width="100"></th>
					<th width="80"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="110" align="right"><strong>Total</strong></th>
					<th width="80" align="right" id="value_total_trans_in_qnty"><strong><? echo number_format($total_trans_in_qnty,2,'.',''); ?></strong></th>
					<th width="120"></th>
					<th width="80" align="right" id="value_total_trans_out_qty"><strong><? echo number_format($total_trans_out_qty,2,'.',''); ?></strong></th>
					<th width=""></th>
				</tr>
			</tfoot>
		</table>
	</div>
	<?
	
	foreach (glob($user_id."*.xls") as $filename) {
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}

	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$html**$filename**$rptType";
	disconnect($con);
	exit();
}

?>
