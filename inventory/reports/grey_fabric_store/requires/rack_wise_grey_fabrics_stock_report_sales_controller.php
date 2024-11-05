<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" )
{
	header("location:login.php");
	die;
}

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
extract($_REQUEST);
$storeNameArr=return_library_array( "SELECT id,store_name from lib_store_location ", "id", "store_name" );
//manual precision settings here
ini_set('precision',8);
/*
|--------------------------------------------------------------------------
| load_drop_down_buyer
|--------------------------------------------------------------------------
|
*/
if($action=="load_drop_down_buyer")
{
	if($type==1)
		$party="1,3,21,90";
	else
		$party="80";
	echo create_drop_down( "cbo_buyer_id", 140, "SELECT buy.id, buy.buyer_name FROM lib_buyer buy, lib_buyer_tag_company b WHERE buy.status_active =1 AND buy.is_deleted=0 AND b.buyer_id=buy.id AND b.tag_company IN (".$choosenCompany.") ".$buyer_cond." AND buy.id IN (SELECT buyer_id FROM lib_buyer_party_type WHERE party_type IN (".$party.")) GROUP BY buy.id, buy.buyer_name ORDER BY buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" )."**";
	exit();
}

/*
|--------------------------------------------------------------------------
| for_report_settings
|--------------------------------------------------------------------------
|
*/

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=6 and report_id=221 and is_deleted=0 and status_active=1");
    echo "print_report_button_setting('".$print_report_format."');\n";
    exit();
}

/*
|--------------------------------------------------------------------------
| fso_no_popup
|--------------------------------------------------------------------------
|
*/

if($action=="fso_no_popup")
{
	echo load_html_head_contents("Sales Order Info","../../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(job_no, job_id)
		{
			document.getElementById('hide_job_no').value=job_no;
			document.getElementById('hide_job_id').value=job_id;
			parent.emailwindow.hide();
		}

	</script>
</head>
<body>
	<div align="center">
		<fieldset style="width:830px;margin-left:4px;">
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Year</th>
						<th>Within Group</th>
						<th>Search By</th>
						<th>Search</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="hide_job_no" id="hide_job_no" value="">
							<input type="hidden" name="hide_job_id" id="hide_job_id" value="">
						</th>
					</thead>
					<tr class="general">
						<td align="center">
							<input type="text" style="width:140px" class="text_boxes" value="<? echo $cbo_year_id?>" disabled readonly />
						</td>
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
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('cbo_within_group').value+'_'+<? echo $cbo_year_id; ?>, 'create_fso_no_search_list_view', 'search_div', 'rack_wise_grey_fabrics_stock_report_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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



/*
|--------------------------------------------------------------------------
| create_fso_no_search_list_view
|--------------------------------------------------------------------------
|
*/
if($action=="create_fso_no_search_list_view")
{
	$data=explode('_',$data);

	$search_string=trim($data[0]);
	$search_by =$data[1];
	$company_id =$data[2];
	$within_group=$data[3];
	$cbo_year = $data[4];

	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');

	$search_field_cond='';
	if($search_string!="")
	{
		if($search_by==1) $search_field_cond=" and a.job_no like '%".$search_string."'";
		else if($search_by==2) $search_field_cond=" and a.sales_booking_no like '%".$search_string."'";
		else $search_field_cond=" and a.style_ref_no like '".$search_string."%'";
	}

	if ($db_type == 0)
	{
		if($cbo_year>0)
		{
			$sales_order_year_condition=" and YEAR(a.insert_date)=$cbo_year";
		}
	}
	else if ($db_type == 2)
	{
		if($cbo_year>0)
		{
			$sales_order_year_condition=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		}
	}

	if($within_group==0) $within_group_cond=""; else $within_group_cond=" and a.within_group=$within_group";

	if($db_type==0) $year_field="YEAR(a.insert_date) as year";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later

	$sql = "SELECT a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, a.customer_buyer from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond $sales_order_year_condition order by a.id DESC";
	//echo $sql;//die;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="90">Sales Order No</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">Cust. Buyer</th>
			<th width="120">Sales/ Booking No</th>
			<th width="80">Booking date</th>
			<th width="110">Style Ref.</th>
			<th>Location</th>
		</thead>
	</table>
	<div style="width:800px; max-height:300px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			foreach ($result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				/*if($row[csf('within_group')]==1)
					$buyer=$company_arr[$row[csf('buyer_id')]];
				else
					$buyer=$buyer_arr[$row[csf('buyer_id')]];*/
				$buyer=$buyer_arr[$row[csf('customer_buyer')]];

				//$booking_data =$row[csf('id')]."**".$row[csf('job_no_prefix_num')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('id')]; ?>');">
					<td width="40"><? echo $i; ?></td>
					<td width="90"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
					<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="80"><p><? echo $yes_no[$row[csf('within_group')]]; ?></p></td>
					<td width="70"><p><? echo $buyer; ?></p></td>
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
| booking_no_popup
|--------------------------------------------------------------------------
|
*/
if($action=="booking_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>
		var selected_id = new Array();
		var selected_name = new Array;

		function check_all_datas()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_div' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click');
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

		/*function js_set_value2( str )
		{
			if (str!="") str=str.split("_");
			$('#hide_job_id').val( str[0] );
			$('#hide_job_no').val( str[1] );
			parent.emailwindow.hide();
		}*/

		function js_set_value2( str )
		{
			if (str!="") str=str.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			if( jQuery.inArray( str[1], selected_id ) == -1 )
			{
				selected_id.push( str[1] );
				selected_name.push( str[2] );
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );

			}
			
			var id = ''; 
			var name = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';

			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
			//$('#hide_recv_id').val( rec_id );
			//$("#hide_booing_type").val(str[3]);
		}
		
		function func_onchange_booking_search_by(data)
		{
			//alert('su..re');
			//1 = booking no
			//2 = job no
			//3 = Style Ref.
			var jobNo = '<?php echo $txt_job_no; ?>';
			if(data == 2 && jobNo != '')
			{
				$('#txt_search_common').val('<? echo $txt_job_no; ?>').attr('disabled', 'disabled');
			}
			else
			{
				$('#txt_search_common').removeAttr('disabled');
			}
		}
	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:680px;">
				<table width="670" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Cust. Buyer</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Booking No</th>
						<th>
                            <input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;">
                            <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                            <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
						</th>
						<!--<input type="hidden" name="hide_recv_id" id="hide_recv_id" value="" />-->
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								//is_disabled
								$is_disabled = ($buyer_name != 0 ? '1' : '0');

								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",$is_disabled );
								?>
							</td>

							<td align="center">
								<?
								$search_by_arr=array(1=>"Booking No",2=>"FSO No",3=>"Style Ref");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../');func_onchange_booking_search_by(this.value) ";
								echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "1",$dd,0 );
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+'<? echo $txt_job_no; ?>', 'create_booking_no_search_list_view', 'search_div', 'rack_wise_grey_fabrics_stock_report_sales_controller', 'setFilterGrid(\'tbl_list_div\',-1)');" style="width:100px;" />
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

/*
|--------------------------------------------------------------------------
| create_booking_no_search_list_view
|--------------------------------------------------------------------------
|
*/
if($action=="create_booking_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond = " AND a.customer_buyer IN (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond = " AND a.customer_buyer=$data[1]";
	}

	$search_by = $data[2];
	$search_string = "%".trim($data[3])."%";

	if($search_by == 3)
	{
		$search_field = "a.style_ref_no";
	}
	else if($search_by == 2)
	{
		$search_field = "a.job_no";
	}
	else
		$search_field = "a.sales_booking_no";

	if($db_type == 0)
		$year_field_by = " AND YEAR(a.insert_date)";
	else if($db_type == 2)
		$year_field_by = " AND TO_CHAR(a.insert_date,'YYYY')";
	else
		$year_field_by = "";
	
	
	if($db_type == 0)
		$month_field_by = " AND month(a.insert_date)";
	else if($db_type == 2)
		$month_field_by = " AND to_char(a.insert_date,'MM')";
	else
		$month_field_by = "";
	
	if($db_type == 0)
		$year_field = " YEAR(a.insert_date) AS year";
	else if($db_type == 2)
		$year_field = " TO_CHAR(a.insert_date,'YYYY') AS year";
	else
		$year_field = "";

	if($year_id != 0)
		$year_cond = " ".$year_field_by." = ".$year_id."";
	else
		$year_cond = "";
	
	if($month_id != 0)
		$month_cond = " ".$month_field_by." = ".$month_id."";
	else
		$month_cond = "";


	$sql = "SELECT a.company_id, a.buyer_id, a.job_no as fso_no, a.style_ref_no, a.sales_booking_no, a.id as fso_id, a.customer_buyer from fabric_sales_order_mst a where a.status_active=1 AND a.company_id IN(".$company_id.") and ".$search_field." LIKE '".$search_string."' ".$buyer_id_cond." ".$year_cond." ".$month_cond;

	//echo $sql;
	$sqlResult=sql_select($sql);
	if(empty($sqlResult))
	{
		echo get_empty_data_msg();
		die;
	}
	
	$buyerIdArr = array();
	foreach($sqlResult as $row)
	{
		$buyerIdArr[$row[csf('buyer_id')]] = $row[csf('buyer_id')];
	}
	
	// $buyer_arr=return_library_array( "SELECT id, buyer_name FROM lib_buyer WHERE id IN (".implode(',', $buyerIdArr).")",'id','buyer_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$company_arr=return_library_array( "SELECT id, company_name FROM lib_company WHERE id IN (".$company_id.")",'id','company_name');
	?>
	<div align="center">

		<fieldset style="width:650px;margin-left:10px">
			<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="670" class="rpt_table">
					<thead>
						<th width="30">SL</th>
						<th width="130">Company</th>
						<th width="110">Cust. Buyer</th>
						<th width="110">Sales Order No</th>
						<th width="120">Style Ref.</th>
						<th width="">Booking No</th>
					</thead>
				</table>
				<div style="width:670px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_div">
						<?
						$i=1;
						foreach($sqlResult as $row )
						{
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							// $data = $row[csf('fso_id')].'_'.$row[csf('sales_booking_no')];
							$data = $i.'_'.$row[csf('fso_id')].'_'.$row[csf('sales_booking_no')];
							?>
							<tr id="tr_<?php echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="js_set_value2('<? echo $data;?>')">
								<td width="30" align="center"><?php echo $i; ?>
								<td width="130"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
								<td width="110"><p><? echo $buyer_arr[$row[csf('customer_buyer')]]; ?></p></td>
								<td width="110"><p><? echo $row[csf('fso_no')]; ?></p></td>
								<td width="120"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
								<td width=""><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
							</tr>
							<?
							$i++;
						}
						?>
					</table>
				</div>
				<table width="650" cellspacing="0" cellpadding="0" style="border:none" align="center">
					<tr>
						<td align="center" height="30" valign="bottom">
							<div style="width:100%">
								<div style="width:50%; float:left" align="left">
									<input type="checkbox" name="check_all" id="check_all" onClick="check_all_datas()"/>
									Check / Uncheck All
								</div>
								<div style="width:50%; float:left" align="left">
									<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px"/>
								</div>
							</div>
						</td>
					</tr>
				</table>
			</form>
		</fieldset>
	</div>
	<?php
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
			show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+'<? echo $company; ?>'+'_'+document.getElementById('txt_prod_id').value, 'create_lot_search_list_view', 'search_div', 'rack_wise_grey_fabrics_stock_report_sales_controller', 'setFilterGrid("list_view",-1)');
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

/*
|--------------------------------------------------------------------------
| store_popup
|--------------------------------------------------------------------------
|
*/
if ($action=="store_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	?>	
    <script>
		var selected_id = new Array();
		var selected_name = new Array();
		var selected_attach_id = new Array();

		function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
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
			
		function js_set_value(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];
		
			if( jQuery.inArray(  str , selected_id ) == -1 )
			{
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#store_id').val( id );
			$('#store_name').val( ddd );
		}
	</script>
    <input type="hidden" id="store_id" />
    <input type="hidden" id="store_name" />
 	<?
	$store_sql = "SELECT a.id, a.store_name, a.company_id, a.store_location from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=13 and a.company_id IN($data[0]) $store_location_credential_cond and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name, a.company_id, a.store_location order by a.store_name";
	// echo $store_sql; 
	$companyArr = return_library_array( "SELECT id, company_name FROM lib_company WHERE id IN (".$data[0].")", "id", "company_name" );
	$arr=array(0=>$companyArr,1=>$locationArr,2=>$storeArr);
	echo  create_list_view("list_view", "Company,Location,Store", "70,100,150","420","360",0, $store_sql, "js_set_value", "id,store_name", "", 1, "company_id", $arr , "company_id,store_location,store_name", "rack_wise_grey_fabrics_stock_report_controller",'setFilterGrid("list_view",-1);','0,0,0','',1) ;
	disconnect($con);
	exit();
}

/*
|--------------------------------------------------------------------------
| floor_popup
|--------------------------------------------------------------------------
|
*/
if ($action=="floor_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	?>	
    <script>
		var selected_id = new Array();
		var selected_name = new Array();
		var selected_attach_id = new Array();

		function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
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
			
		function js_set_value(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];
		
			if( jQuery.inArray(  str , selected_id ) == -1 )
			{
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#floor_id').val( id );
			$('#floor_name').val( ddd );
		}
	</script>
    <input type="hidden" id="floor_id" />
    <input type="hidden" id="floor_name" />
 	<?		

 	if ($data[1]=="")
		$store_cond = "";
	else
		$store_cond = " AND b.store_id IN(".$data[1].")";

	$floor_sql = "SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
	FROM lib_floor_room_rack_mst a
	INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.floor_id
	INNER JOIN lib_store_location_category c ON b.store_id = c.store_location_id
	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$data[0].") $store_cond and c.category_type in(13,14)
	GROUP BY a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
	ORDER BY a.floor_room_rack_name";
	
	$companyArr = return_library_array( "SELECT id, company_name FROM lib_company WHERE id IN (".$data[0].")", "id", "company_name" );
	$locationArr = return_library_array( "SELECT id, location_name FROM lib_location WHERE status_active = 1 AND is_deleted = 0 AND company_id IN (SELECT id FROM lib_company WHERE id IN (".$data[0].")) ORDER BY location_name","id","location_name" );
	$storeArr = return_library_array("SELECT id, store_name FROM lib_store_location WHERE status_active = 1 AND is_deleted=0 ORDER BY store_name","id","store_name");
	$arr=array(0=>$companyArr,1=>$locationArr,2=>$storeArr);
	echo  create_list_view("list_view", "Company,Location,Store,Floor", "70,100,150,150","520","360",0, $floor_sql, "js_set_value", "floor_room_rack_id,floor_room_rack_name", "", 1, "company_id,location_id,store_id,0", $arr , "company_id,location_id,store_id,floor_room_rack_name", "rack_wise_grey_fabrics_stock_report_sales_controller",'setFilterGrid("list_view",-1);','0,0,0,0','',1) ;
	disconnect($con);
	exit();
}

/*
|--------------------------------------------------------------------------
| room_popup
|--------------------------------------------------------------------------
|
*/
if ($action=="room_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	?>	
    <script>
		var selected_id = new Array();
		var selected_name = new Array();
		var selected_attach_id = new Array();
		function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
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
			
		function js_set_value(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];
		
			if( jQuery.inArray(  str , selected_id ) == -1 )
			{
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#room_id').val( id );
			$('#room_name').val( ddd );
		}
	</script>

    <input type="hidden" id="room_id" />
    <input type="hidden" id="room_name" />
 	<?
	if ($data[1]=="")
		$floor_cond = "";
	else
		$floor_cond = " AND b.floor_id IN(".$data[1].")";
	
	$room_sql = "
		SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id, b.floor_id
    	FROM lib_floor_room_rack_mst a
		INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.room_id 
		INNER JOIN lib_store_location_category c ON b.store_id = c.store_location_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$data[0].") ".$floor_cond."  and c.category_type in(13,14)
    	GROUP BY a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id, b.floor_id
    	ORDER BY a.floor_room_rack_name
	";
    //echo $room_sql;die;

	$companyArr = return_library_array( "SELECT id, company_name FROM lib_company WHERE id IN(".$data[0].")", "id", "company_name" );
	$locationArr = return_library_array( "SELECT id, location_name FROM lib_location WHERE status_active = 1 AND is_deleted = 0 AND company_id IN (SELECT id FROM lib_company WHERE id IN (".$data[0].")) ORDER BY location_name","id","location_name" );
	$storeArr = return_library_array("SELECT id, store_name FROM lib_store_location WHERE status_active = 1 AND is_deleted = 0 ORDER BY store_name","id","store_name");
	$floorArr = return_library_array("SELECT a.floor_room_rack_name, a.floor_room_rack_id FROM lib_floor_room_rack_mst a INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.floor_id WHERE a.company_id in(".$data[0].") ".$floor_cond." AND a.is_deleted = 0 AND a.status_active = 1 GROUP BY a.floor_room_rack_name, a.floor_room_rack_id","floor_room_rack_id","floor_room_rack_name");
	$arr=array(0=>$companyArr,1=>$locationArr,2=>$storeArr,3=>$floorArr);
	echo  create_list_view("list_view", "Company,Location,Store,Floor,Room", "70,100,150,80,80","520","360",0, $room_sql, "js_set_value", "floor_room_rack_id,floor_room_rack_name", "", 1, "company_id,location_id,store_id,floor_id,0", $arr , "company_id,location_id,store_id,floor_id,floor_room_rack_name", "rack_wise_grey_fabrics_stock_report_sales_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','',1) ;
	disconnect($con);
	exit();
}

/*
|--------------------------------------------------------------------------
| rack_popup
|--------------------------------------------------------------------------
|
*/
if ($action=="rack_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	?>	
    <script>
		var selected_id = new Array();
		var selected_name = new Array();
		var selected_attach_id = new Array();

		function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
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
			
		function js_set_value(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];
		
			if( jQuery.inArray(  str , selected_id ) == -1 )
			{
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#rack_id').val( id );
			$('#rack_name').val( ddd );
		}
	</script>
    <input type="hidden" id="rack_id" />
    <input type="hidden" id="rack_name" />
 	<?
	if ($data[1]=="")
		$floor_cond="";
	else
		$floor_cond=" AND b.floor_id IN(".$data[1].")";
		
	if ($data[2]=="")
		$room_cond="";
	else
		$room_cond=" AND b.room_id IN(".$data[2].")";
	
    $rack_sql = "SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id, b.floor_id, b.room_id
    FROM lib_floor_room_rack_mst a 
    INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.rack_id 
    INNER JOIN lib_store_location_category c ON b.store_id = c.store_location_id
    WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$data[0].") ".$floor_cond." ".$room_cond."  and c.category_type in(13,14)
    GROUP BY a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id, b.floor_id, b.room_id
    ORDER BY a.floor_room_rack_name";

	$companyArr = return_library_array( "SELECT id, company_name FROM lib_company WHERE id IN(".$data[0].")", "id", "company_name" );
	$locationArr = return_library_array( "SELECT id, location_name FROM lib_location WHERE status_active = 1 AND is_deleted = 0 AND company_id IN (SELECT id FROM lib_company WHERE id IN (".$data[0].")) ORDER BY location_name","id","location_name" );
	$storeArr = return_library_array("SELECT id, store_name FROM lib_store_location WHERE status_active = 1 AND is_deleted = 0 ORDER BY store_name","id","store_name");
	$floorArr = return_library_array("SELECT a.floor_room_rack_name, a.floor_room_rack_id FROM lib_floor_room_rack_mst a INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.floor_id WHERE a.company_id in(".$data[0].") ".$floor_cond." AND a.is_deleted = 0 AND a.status_active = 1 GROUP BY a.floor_room_rack_name, a.floor_room_rack_id","floor_room_rack_id","floor_room_rack_name");
	$roomArr = return_library_array("SELECT a.floor_room_rack_name, a.floor_room_rack_id FROM lib_floor_room_rack_mst a INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.room_id WHERE  a.company_id IN(".$data[0].") ".$floor_cond." ".$room_cond." AND a.is_deleted = 0 AND a.status_active = 1 GROUP BY a.floor_room_rack_name, a.floor_room_rack_id","floor_room_rack_id","floor_room_rack_name");
	$arr=array(0=>$companyArr,1=>$locationArr,2=>$storeArr,3=>$floorArr,4=>$roomArr);
	echo  create_list_view("list_view", "Company,Location,Store,Floor,Room,Rack", "70,100,150,80,80,100","590","360",0, $rack_sql, "js_set_value", "floor_room_rack_id,floor_room_rack_name", "", 1, "company_id,location_id,store_id,floor_id,room_id,0", $arr , "company_id,location_id,store_id,floor_id,room_id,floor_room_rack_name", "rack_wise_grey_fabrics_stock_report_sales_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0','',1) ;
	disconnect($con);
	exit();
}

/*
|--------------------------------------------------------------------------
| report_generate
|--------------------------------------------------------------------------
|
*/
if($action=="report_generate")
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	ob_start();
	$companyId = str_replace("'", "", $cbo_company_id);
	$buyerId = str_replace("'", "", $cbo_buyer_id);
	$year = str_replace("'", "", $cbo_year);
	$jobNo = trim(str_replace("'", "", $txt_job_no));
	$jobId = str_replace("'", "", $txt_job_id);
	$bookingNo = str_replace("'", "", $txt_booking_no);
	$bookingId = str_replace("'", "", $txt_booking_id);
	$searchBy = str_replace("'", "", $cbo_search_by);
	$searchCommon = str_replace("'", "", $txt_search_comm);
	$product = str_replace("'", "", $txt_product);
	$productId = str_replace("'", "", $txt_product_id);
	$productNo = str_replace("'", "", $txt_product_no);
	$storeId = str_replace("'", "", $txt_store_id);
	$floorId = str_replace("'", "", $txt_floor_id);
	$roomId = str_replace("'", "", $txt_room_id);
	$rackId = str_replace("'", "", $txt_rack_id);
	$valueType = str_replace("'", "", $cbo_value_type);
	$fromDate = str_replace("'", "", $txt_date_from);
	$toDate = str_replace("'", "", $txt_date_to);
	$reportType = str_replace("'", "", $rpt_type);
	//$_SESSION["date_from"] = date('Y-m-d', strtotime($date_from));
	//$_SESSION["date_to"] = date('Y-m-d', strtotime($date_to));
	$int_ref 		= trim(str_replace("'","",$txt_int_ref));
	
	//buyerIdCondition
	$buyerIdCondition = '';
	if($buyerId != 0)
	{
		$buyerIdCondition = " AND d.customer_buyer = ".$buyerId."";
	}
	
	//yearCondition;
	$yearCondition = '';
	if ($year>0) 
	{
		if($db_type==0)
		{
			$yearCondition = " AND YEAR(d.insert_date) = ".$year."";
		}
		else if($db_type==2)
		{
			$yearCondition = " AND TO_CHAR(d.insert_date,'YYYY') = ".$year."";
		}
	}	
	
	//jobNoCondition
	$jobNoCondition = '';

	if($jobNo != '')
	{
		if($jobId != '')
		{
			$jobNoCondition = " AND d.id =".$jobId."";
		}else{
			$jobNoCondition = " AND d.job_no like '%".$jobNo."%'";
		}
	}
	
	//bookingNoCondition
	$bookingNoCondition = '';
	if($bookingNo != '')
	{
		$str_arry = explode(",",$bookingNo);
	    $all_bookingNo="";
	    foreach ($str_arry as $key => $booking) 
	    {
	        if ($all_bookingNo=="") 
	        {
	            $all_bookingNo.= $booking;
	        }
	        else 
	        {
	            $all_bookingNo.= "','".$booking;
	        }
	    }
	    // echo $all_bookingNo;die;
		$bookingNoCondition = " AND d.sales_booking_no in('$all_bookingNo')";
	}
	// echo $bookingNoCondition;die;

	if ($int_ref!="")
	{
		$po_sql="SELECT a.id, a.po_number, a.grouping, b.booking_no, b.booking_mst_id from wo_po_break_down a, wo_booking_dtls b, FABRIC_SALES_ORDER_MST c 
		where a.id=b.po_break_down_id and b.BOOKING_MST_ID=c.BOOKING_ID and c.within_group=1 and a.grouping='$int_ref' and b.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		// echo $po_sql;die;
		$po_sql_result=sql_select($po_sql);
		$refBooking_cond="";
		foreach ($po_sql_result as $key => $row) 
		{
			//$po_id_arr[$row[csf('id')]]=$row[csf('id')];
			$bookingNo_arr[$row[csf('booking_mst_id')]] = $row[csf('booking_mst_id')];
		}
		//$poIds=implode(",",array_unique(explode(",",$po_id_arr)));
		$refBooking_cond=" and d.booking_id in('".implode("','",$bookingNo_arr)."') ";
	}
	
	//searchByCondition
	$searchByCondition = '';
	if($searchCommon != '')
	{
		$expSearchCommon = explode(',', $searchCommon);
		$dataSearchCommon = '';
		foreach($expSearchCommon as $val)
		{
			if($dataSearchCommon != '')
			{
				$dataSearchCommon .= ',';
			}
			$dataSearchCommon .= "'".$val."'";
		}
		
		//style no
		if($searchBy == 1)
		{
			$searchByCondition = " AND d.style_ref_no IN(".$dataSearchCommon.")";
		}
		//order no
		else if($searchBy == 2)
		{
			$searchByCondition = " AND c.po_number IN(".$dataSearchCommon.")";
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
				$productIdCondition .= " e.prod_id IN(".$pids.") OR ";
			}
			$productIdCondition = chop($productIdCondition,'OR ');
			$productIdCondition .= ")";
		}
		else
		{
			$productIdCondition = " AND e.prod_id IN(".$productId.")";
		}
	}

	//storeCondition
	$storeCondition = '';
	if($storeId != '')
	{
		$storeCondition = " AND f.store_id IN(".$storeId.")";
	}

	//floorCondition
	$floorCondition = '';
	if($floorId != '')
	{
		$floorCondition = " AND f.floor_id IN(".$floorId.")";
	}

	//roomCondition
	$roomCondition = '';
	if($roomId != '')
	{
		$roomCondition = " AND f.room IN(".$roomId.")";
	}

	//rackCondition
	$rackCondition = '';
	if($rackId != '')
	{
		$rackCondition = " AND f.rack IN(".$rackId.")";
	}
	
	//dateCondition
	$dateCondition = '';
	$dateCondition2 = '';
	if($toDate != '')
	{
		if($db_type == 0)
		{
			$endDate = change_date_format($toDate,"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$endDate = change_date_format($toDate,"","",1);
		}

		$dateCondition = " AND f.transaction_date <= '".$endDate."'";
		$dateCondition2 = " AND a.transfer_date <= '".$endDate."'";
	}

	$con = connect();
    $r_id=execute_query("delete from tmp_barcode_no where userid=$user_id");
    $r_id3=execute_query("delete from tmp_prod_id where userid=$user_id");
    execute_query("DELETE from tmp_booking_id where userid=$user_id");
    oci_commit($con);
	//only for roll basis 
	/*
	|--------------------------------------------------------------------------
	| for recv roll query
	|--------------------------------------------------------------------------
	|
	*/
	$sqlRcvRollQty = "
		SELECT
			d.job_no, d.sales_booking_no, d.customer_buyer, d.buyer_id, d.style_ref_no, d.company_id, d.booking_id, d.remarks, e.prod_id, e.po_breakdown_id, e.entry_form, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, SUM(h.qnty) AS rcv_qty, COUNT(h.id) AS no_of_roll_rcv, h.barcode_no
        FROM
			fabric_sales_order_mst d
			INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id
            INNER JOIN inv_transaction f ON e.trans_id = f.id
			INNER JOIN pro_grey_prod_entry_dtls g ON f.id = g.trans_id
			LEFT JOIN pro_roll_details h ON g.id = h.dtls_id
        WHERE
			e.status_active = 1 
			AND e.is_deleted = 0
			AND e.entry_form IN(2,22,58,84)
			AND e.trans_type IN(1,4)
			AND e.trans_id > 0
			AND f.status_active = 1
			AND f.is_deleted = 0
			AND d.company_id IN(".$companyId.")
			".$buyerIdCondition."
			".$yearCondition."
			".$jobNoCondition."
			".$bookingNoCondition."
			".$searchByCondition."
			".$productIdCondition."
			".$storeCondition."
			".$floorCondition."
			".$roomCondition."
			".$rackCondition."
			".$dateCondition."
			".$refBooking_cond."
			AND h.entry_form IN(2,22,58,84) and h.is_sales=1
        GROUP BY 
			d.job_no, d.sales_booking_no, d.customer_buyer, d.buyer_id, d.style_ref_no, d.company_id, d.booking_id, d.remarks, e.prod_id, e.po_breakdown_id, e.entry_form, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, h.barcode_no
	";	// and h.barcode_no in( 22020004633,22020004645,22020004627)
	//echo $sqlRcvRollQty; //die;
	$sqlRcvRollRslt = sql_select($sqlRcvRollQty);
	foreach($sqlRcvRollRslt as $row) // recv barcode insert into tmp_barcode_no table
	{
		/*if( $barcode_no_check[$row[csf('barcode_no')]] =="" )
        {
            $barcode_no_check[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
            $barcodeno = $row[csf('barcode_no')];
            // echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_id,$barcodeno)";
            $r_id=execute_query("insert into tmp_barcode_no (userid, barcode_no, entry_form) values ($user_id,$barcodeno,58)");
        }*/
        if( $prod_id_check[$row[csf('prod_id')]] == "" )
        {
            $prod_id_check[$row[csf('prod_id')]]=$row[csf('prod_id')];
            $prod_id = $row[csf('prod_id')];
            // echo "insert into tmp_prod_id (userid, prod_id) values ($user_id,$prod_id)";
            $r_id3=execute_query("insert into tmp_prod_id (userid, prod_id) values ($user_id,$prod_id)");
        }
        $recv_barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
        $recv_booking_id_arr[$row[csf('booking_id')]]=$row[csf('booking_id')];
	}
	oci_commit($con);
	
	/*echo "<pre>";
	print_r($dataArr); die;*/

	/*
	|--------------------------------------------------------------------------
	| for issue qty and roll query
	| order to order transfer
	|--------------------------------------------------------------------------
	|
	*/
	$sqlNoOfRoll="
		SELECT
			d.job_no, d.sales_booking_no, d.customer_buyer, d.buyer_id, d.style_ref_no, d.company_id, d.booking_id, d.remarks, e.prod_id, e.po_breakdown_id, e.trans_type, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, SUM(e.quantity) AS rcv_qty, SUM(h.qnty) AS roll_rcv_qty, COUNT(g.id) AS issue_roll, h.barcode_no
		FROM 
			fabric_sales_order_mst d
			INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id
			INNER JOIN inv_transaction f ON e.trans_id = f.id 
			INNER JOIN inv_item_transfer_dtls g ON e.dtls_id = g.id 
			LEFT JOIN pro_roll_details h ON g.id = h.dtls_id 
		WHERE
			e.status_active = 1 
			AND e.is_deleted = 0 
			AND e.entry_form IN(133) 
			and h.entry_form=133
			AND e.trans_type IN(5,6) 
			and g.TRANS_ID>0 and g.TO_TRANS_ID>0
			AND f.status_active = 1 
			AND f.is_deleted = 0 
			AND g.status_active = 1 
			AND g.is_deleted = 0
			and h.is_sales=1
			AND d.company_id IN(".$companyId.")	
			".$buyerIdCondition."
			".$yearCondition."
			".$jobNoCondition."
			".$bookingNoCondition."
			".$searchByCondition."
			".$productIdCondition."
			".$storeCondition."
			".$floorCondition."
			".$roomCondition."
			".$rackCondition."
			".$dateCondition."
			".$refBooking_cond."
        GROUP BY 
			d.job_no, d.sales_booking_no, d.customer_buyer, d.buyer_id, d.style_ref_no, d.company_id, d.booking_id, d.remarks, e.prod_id, e.po_breakdown_id, e.trans_type, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, h.barcode_no
		UNION ALL
		SELECT
			d.job_no, d.sales_booking_no, d.customer_buyer, d.buyer_id, d.style_ref_no, d.company_id, d.booking_id, d.remarks, e.prod_id, e.po_breakdown_id, e.trans_type, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, SUM(e.quantity) AS rcv_qty, 0 AS roll_rcv_qty, SUM(g.roll) AS issue_roll, 0 as barcode_no
		FROM
			fabric_sales_order_mst d
			INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id
			INNER JOIN inv_transaction f ON e.trans_id = f.id 
			INNER JOIN inv_item_transfer_dtls g ON e.dtls_id = g.id 
		WHERE
			e.status_active = 1 
			AND e.is_deleted = 0 
			AND e.entry_form IN(362) 
			AND e.trans_type IN(5,6) 
			AND f.status_active = 1 
			AND f.is_deleted = 0 
			AND g.status_active = 1 
			AND g.is_deleted = 0
			AND d.company_id IN(".$companyId.")
			".$buyerIdCondition."
			".$yearCondition."
			".$jobNoCondition."
			".$bookingNoCondition."
			".$searchByCondition."
			".$productIdCondition."
			".$storeCondition."
			".$floorCondition."
			".$roomCondition."
			".$rackCondition."
			".$dateCondition."
			".$refBooking_cond."
        GROUP BY 
			d.job_no, d.sales_booking_no, d.customer_buyer, d.buyer_id, d.style_ref_no, d.company_id, d.booking_id, d.remarks, e.prod_id, e.po_breakdown_id, e.trans_type, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box
	";
	//echo "<br>".$sqlNoOfRoll; //die;
	$sqlNoOfRollResult = sql_select($sqlNoOfRoll);
	foreach($sqlNoOfRollResult as $row) // Transfered barcode insert into tmp_barcode_no table
	{
		/*if( $barcode_no_check2[$row[csf('barcode_no')]] =="" )
        {
            $barcode_no_check2[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
            $barcodeno = $row[csf('barcode_no')];
            // echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_id,$barcodeno)";
            $r_id2=execute_query("insert into tmp_barcode_no (userid, barcode_no, entry_form) values ($user_id,$barcodeno,133)");
        }*/
        if( $prod_id_check[$row[csf('prod_id')]] == "" )
        {
            $prod_id_check[$row[csf('prod_id')]]=$row[csf('prod_id')];
            $prod_id = $row[csf('prod_id')];
            // echo "insert into tmp_prod_id (userid, prod_id) values ($user_id,$prod_id)";
            $r_id3=execute_query("insert into tmp_prod_id (userid, prod_id) values ($user_id,$prod_id)");
        }
        $recv_barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
        $recv_booking_id_arr[$row[csf('booking_id')]]=$row[csf('booking_id')];
	}
	oci_commit($con);

	if(!empty($recv_barcode_no_arr))
	{
		foreach($recv_barcode_no_arr as $barcodeno)
		{
			execute_query("insert into tmp_barcode_no (userid, barcode_no, entry_form) values ($user_id,$barcodeno,58133)");
		}
		oci_commit($con);
	}
	if(!empty($recv_booking_id_arr))
	{
		foreach($recv_booking_id_arr as $id)
		{
			execute_query("insert into tmp_booking_id (userid, booking_id) values ($user_id,$id)");
		}
		oci_commit($con);
	}
	/*
	|--------------------------------------------------------------------------
	| for internal ref no
	|--------------------------------------------------------------------------
	|
	*/
	$ref_sql="SELECT b.booking_no, c.grouping from tmp_booking_id a, wo_booking_dtls b, wo_po_break_down c 
	where a.booking_id=b.booking_mst_id and b.po_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.booking_no, c.grouping";
	$ref_sql_result=sql_select($ref_sql);
	foreach ($ref_sql_result as $row)
	{
		$int_ref_arr[$row[csf('booking_no')]].=$row[csf('grouping')].',';
	}

	/*
	|--------------------------------------------------------------------------
	| for knitting production roll
	|--------------------------------------------------------------------------
	|
	*/
	$production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count, a.brand_id,b.po_breakdown_id, a.prod_id, b.booking_no,c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, c.knitting_source, c.challan_no as production_challan, c.knitting_company, a.yarn_prod_id, a.body_part_id, b.coller_cuff_size, c.entry_form
    from pro_grey_prod_entry_dtls a,pro_roll_details b, inv_receive_master c, tmp_barcode_no d 
    where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2,22,58) and b.entry_form in(2,22,58)  and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid= $user_id and d.entry_form in(58133)");
    $yarn_prod_id_check=array();$prog_no_check=array();
    foreach ($production_sql as $row)
    {
        //$prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"] =$row[csf("receive_basis")];
        //$prodBarcodeData[$row[csf("barcode_no")]]["prog_book"] =$row[csf("booking_no")];
        //$prodBarcodeData[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
        $prodBarcodeData[$row[csf('barcode_no')]]["brand_id"]=$row[csf('brand_id')];
        $prodBarcodeData[$row[csf('barcode_no')]]["entry_form"]=$row[csf('entry_form')];
        $prodBarcodeData[$row[csf("barcode_no")]]["color_range_id"] =$row[csf("color_range_id")];
        $prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"] =$row[csf("yarn_lot")];
        $prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"] =$row[csf("yarn_count")];
        $prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"] =$row[csf("yarn_prod_id")];
        
        $prodBarcodeData[$row[csf("barcode_no")]]["prod_id"] =$row[csf("prod_id")];
        $prodBarcodeData[$row[csf("barcode_no")]]["color_id"] =$row[csf("color_id")];
        $prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"] =$row[csf("febric_description_id")];
        //$prodBarcodeData[$row[csf("barcode_no")]]["gsm"] =$row[csf("gsm")];
        //$prodBarcodeData[$row[csf("barcode_no")]]["width"] =$row[csf("width")];
        $prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"] =$row[csf("stitch_length")];
        $prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"] =$row[csf("machine_dia")];
        // $prodBarcodeData[$row[csf("barcode_no")]]["machine_gg"] =$row[csf("machine_gg")];
        //$prodBarcodeData[$row[csf("barcode_no")]]["item_size"] =$row[csf("coller_cuff_size")];
        //$prodBarcodeData[$row[csf("barcode_no")]]["machine_no_id"] =$row[csf("machine_no_id")];
        //$prodBarcodeData[$row[csf("barcode_no")]]["prod_challan"] =$row[csf("production_challan")];
        //$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"] =$row[csf("knitting_source")];
        //$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"] =$row[csf("knitting_company")];
        //$prodBarcodeData[$row[csf("barcode_no")]]["body_part_id"] =$row[csf("body_part_id")];
        $allDeterArr[$row[csf("febric_description_id")]] =$row[csf("febric_description_id")];
        $allColorArr[$row[csf("color_id")]] =$row[csf("color_id")];
        $allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];

        if( $yarn_prod_id_check[$row[csf('yarn_prod_id')]] == "")
        {
            $yarn_prod_id_check[$row[csf('yarn_prod_id')]]=$row[csf('yarn_prod_id')];
            $yarn_prod_id = $row[csf('yarn_prod_id')];
            // echo "insert into tmp_prog_no (userid, prog_no) values ($user_id,$yarn_prod_id)";
            $r_id5=execute_query("insert into tmp_prod_id (userid, prod_id) values ($user_id,$yarn_prod_id)");
        }

        if($row[csf('receive_basis')] == 2 )
        {
            $prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"] =$row[csf("machine_dia")];
            $prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"] =$row[csf("yarn_prod_id")];
        }
    }
    oci_commit($con);
    // echo '<pre>';print_r($prodBarcodeData);die;

    $brand_arr = return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
    $yarn_prod_id_arr = array_filter($allYarnProdArr);
    if(count($yarn_prod_id_arr)>0)
    {    	
        $yarn_sql=  sql_select("SELECT a.id, a.yarn_type, a.yarn_comp_type1st, a.brand from product_details_master a, tmp_prod_id b where a.id=b.prod_id and a.status_active = 1 and a.item_category_id =1");// $yarn_prod_id_cond
        foreach ($yarn_sql as $row)
        {
            $yarn_ref[$row[csf("id")]]["type"] = $yarn_type[$row[csf("yarn_type")]];
            $yarn_ref[$row[csf("id")]]["comp"] = $composition[$row[csf("yarn_comp_type1st")]];
            $yarn_ref[$row[csf("id")]]["brand"] = $brand_arr[$row[csf("brand")]];
        }
    }

	/*
	|--------------------------------------------------------------------------
	| for data array prepard roll
	|--------------------------------------------------------------------------
	|
	*/
	$dataArr = array();
	$poArr = array();
	foreach($sqlRcvRollRslt as $row) // Receive data array
	{
		$compId = $row[csf('company_id')];
		$productId = $row[csf('prod_id')];
		$orderId = $row[csf('po_breakdown_id')];
		$storeId = $row[csf('store_id')]*1;
		$floorId = $row[csf('floor_id')]*1;
		$roomId = $row[csf('room')]*1;
		$rackId = $row[csf('rack')]*1;
		$selfId = $row[csf('self')]*1;
		$binId = $row[csf('bin_box')]*1;
		$poArr[$orderId] = $orderId;

		$bookingInfoArr[$orderId]['fso_no'] = $row[csf('job_no')];
		$bookingInfoArr[$orderId]['customer_buyer'] = $row[csf('customer_buyer')];
		$bookingInfoArr[$orderId]['buyer_id'] = $row[csf('buyer_id')];
		$bookingInfoArr[$orderId]['booking_no'] = $row[csf('sales_booking_no')];
		$bookingInfoArr[$orderId]['style_ref_no'] = $row[csf('style_ref_no')];
		$bookingInfoArr[$orderId]['fso_remarks'] = $row[csf('remarks')];
		$bookingInfoArr[$orderId]['within_group'] = $row[csf('within_group')];
		
		if ($reportType == 1 || $reportType == 3) // Summary and Rack Wise
		{
			//$rollRcvQty += $row[csf('no_of_roll_rcv')];
			$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollRcvQty'] += $row[csf('no_of_roll_rcv')];
			if($row[csf('entry_form')]  == 84)
			{
				//$issueReturnQty += $row[csf('rcv_qty')];
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['issueReturnQty'] += $row[csf('rcv_qty')];

				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['machine_dia'].=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['stitch_length'].=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['color_id'].=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['febric_descri'].=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['color_range_id'].=$prodBarcodeData[$row[csf("barcode_no")]]["color_range_id"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['yarn_count'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['yarn_prod_id'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['yarn_lot'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"].',';
			}
			else
			{
				//$rcvQty += $row[csf('rcv_qty')];
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rcvQty'] += $row[csf('rcv_qty')];

				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['machine_dia'].=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['stitch_length'].=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['color_id'].=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['febric_descri'].=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['color_range_id'].=$prodBarcodeData[$row[csf("barcode_no")]]["color_range_id"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['yarn_count'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['yarn_prod_id'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['yarn_lot'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"].',';
			}
		}
		elseif ($reportType == 2) // FSO Wise
		{
			//$rollRcvQty += $row[csf('no_of_roll_rcv')];
			//$rcvQty += $row[csf('rcv_qty')];
			
			$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollRcvQty'] += $row[csf('no_of_roll_rcv')];
			if($row[csf('entry_form')]  == 84)
			{
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['issueReturnQty'] += $row[csf('rcv_qty')];

				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['machine_dia'].=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['stitch_length'].=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['color_id'].=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['color_range_id'].=$prodBarcodeData[$row[csf("barcode_no")]]["color_range_id"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['yarn_count'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['yarn_prod_id'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['yarn_lot'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['brand_id'].=$prodBarcodeData[$row[csf("barcode_no")]]["brand_id"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['entry_form']=$prodBarcodeData[$row[csf("barcode_no")]]["entry_form"];
			}
			else
			{
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rcvQty'] += $row[csf('rcv_qty')];

				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['machine_dia'].=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['stitch_length'].=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['color_id'].=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['color_range_id'].=$prodBarcodeData[$row[csf("barcode_no")]]["color_range_id"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['yarn_count'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['yarn_prod_id'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['yarn_lot'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['brand_id'].=$prodBarcodeData[$row[csf("barcode_no")]]["brand_id"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['entry_form']=$prodBarcodeData[$row[csf("barcode_no")]]["entry_form"];
			}
		}
		elseif ($reportType == 4)
		{
		}
	}
	unset($sqlRcvRollRslt);

	// echo '<pre>';print_r($dataArr);

	foreach($sqlNoOfRollResult as $row) // Transfer data arr
	{
		$compId = $row[csf('company_id')];
		$productId = $row[csf('prod_id')];
		$orderId = $row[csf('po_breakdown_id')];
		$storeId = $row[csf('store_id')]*1;
		$floorId = $row[csf('floor_id')]*1;
		$roomId = $row[csf('room')]*1;
		$rackId = $row[csf('rack')]*1;
		$selfId = $row[csf('self')]*1;
		$binId = $row[csf('bin_box')]*1;

		if($row[csf("barcode_no")])
		{
			$rcv_qty = $row[csf("roll_rcv_qty")];
		}else{
			$rcv_qty = $row[csf("rcv_qty")];
		}

		if ($reportType == 1 || $reportType == 3) // Summary and Rack Wise
		{
			if($row[csf('trans_type')] == 5)
			{
				//$rollRcvQty += $row[csf('issue_roll')];
				//$transferInQty += $row[csf('rcv_qty')];
				$poArr[$orderId] = $orderId;
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollRcvQty'] += $row[csf('issue_roll')];
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['transferInQty'] += $rcv_qty;

				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['machine_dia'].=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['stitch_length'].=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['color_id'].=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['febric_descri'].=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['color_range_id'].=$prodBarcodeData[$row[csf("barcode_no")]]["color_range_id"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['yarn_count'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['yarn_prod_id'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['brand_id'].=$prodBarcodeData[$row[csf("barcode_no")]]["brand_id"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['yarn_lot'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"].',';
			}
			if($row[csf('trans_type')] == 6)
			{
				//$rollIssueQty += $row[csf('issue_roll')];
				//$transferOutQty += $row[csf('rcv_qty')];
				
				$transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollIssueQty'] += $row[csf('issue_roll')];
				$transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['transferOutQty'] += $rcv_qty;
			}
		}
		elseif ($reportType == 2) // FSO Wise
		{
			if($row[csf('trans_type')] == 5)
			{
				//$rollRcvQty += $row[csf('issue_roll')];
				//$transferInQty += $row[csf('rcv_qty')];
				
				$poArr[$orderId] = $orderId;
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollRcvQty'] += $row[csf('issue_roll')];
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['transferInQty'] += $rcv_qty;

				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['machine_dia'].=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['stitch_length'].=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['color_id'].=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['color_range_id'].=$prodBarcodeData[$row[csf("barcode_no")]]["color_range_id"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['yarn_count'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['yarn_prod_id'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['brand_id'].=$prodBarcodeData[$row[csf("barcode_no")]]["brand_id"].',';
				$dataArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['yarn_lot'].=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"].',';
			}
			if($row[csf('trans_type')] == 6)
			{
				//$rollIssueQty += $row[csf('issue_roll')];
				//$transferOutQty += $row[csf('rcv_qty')];
				
				$transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollIssueQty'] += $row[csf('issue_roll')];
				$transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['transferOutQty'] += $rcv_qty;
			}
		}
		
		$bookingInfoArr[$orderId]['fso_no'] = $row[csf('job_no')];
		$bookingInfoArr[$orderId]['customer_buyer'] = $row[csf('customer_buyer')];
		$bookingInfoArr[$orderId]['buyer_id'] = $row[csf('buyer_id')];
		$bookingInfoArr[$orderId]['booking_no'] = $row[csf('sales_booking_no')];
		$bookingInfoArr[$orderId]['style_ref_no'] = $row[csf('style_ref_no')];
		$bookingInfoArr[$orderId]['fso_remarks'] = $row[csf('remarks')];
	}
	unset($sqlNoOfRollResult);
	//echo $rollRcvQty."=".$transferInQty."=".$rollIssueQty."=".$transferOutQty;
	// echo "<pre>"; print_r($transOutArr);
	
	/*
	|--------------------------------------------------------------------------
	| for issue qty and roll
	|--------------------------------------------------------------------------
	|
	*/
	if(!empty($poArr))
	{
		$con = connect();
		execute_query("DELETE FROM TMP_PO_ID WHERE USER_ID = ".$user_id."");
		oci_commit($con);
		
		$con = connect();
		foreach($poArr as $poId)
		{
			execute_query("INSERT INTO TMP_PO_ID(PO_ID,USER_ID) VALUES(".$poId.", ".$user_id.")");
		}
		oci_commit($con);
		//disconnect($con);
		
		$sqlNoOfRollIssue="
			SELECT
				d.company_id,
				e.prod_id, e.po_breakdown_id, SUM(e.quantity) AS issue_qty,
				f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box,
				SUM(g.no_of_roll) AS issue_roll
			FROM
				TMP_PO_ID t
				INNER JOIN fabric_sales_order_mst d ON t.po_id = d.id and t.USER_ID = $user_id
				INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id and t.PO_ID = e.po_breakdown_id  and t.USER_ID = $user_id
				INNER JOIN inv_transaction f ON e.trans_id = f.id
				INNER JOIN inv_grey_fabric_issue_dtls g ON e.dtls_id = g.id
			WHERE
				e.status_active = 1
				AND e.is_deleted = 0
				AND e.entry_form IN(16) and e.is_sales=1
				AND e.trans_type = 2
				AND f.status_active = 1
				AND f.is_deleted = 0
				AND g.status_active = 1
				AND g.is_deleted = 0
				AND d.company_id IN(".$companyId.")
				".$dateCondition."
			GROUP BY 
				d.company_id,
				e.prod_id, e.po_breakdown_id,
				f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box
			UNION ALL
			SELECT
				d.company_id,
				e.prod_id, e.po_breakdown_id, SUM(e.quantity) AS issue_qty,
				f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box,
				COUNT(g.id) AS issue_roll
			FROM
				TMP_PO_ID t
				INNER JOIN fabric_sales_order_mst d ON t.po_id = d.id and t.USER_ID = $user_id
				INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id and t.PO_ID = e.po_breakdown_id  and t.USER_ID = $user_id
				INNER JOIN inv_transaction f ON e.trans_id = f.id
				INNER JOIN pro_roll_details g ON e.dtls_id = g.dtls_id
				INNER JOIN tmp_barcode_no h ON g.barcode_no = h.barcode_no
			WHERE
				e.status_active = 1
				AND e.is_deleted = 0
				AND e.entry_form IN(61)
				AND e.trans_type = 2
				AND f.status_active = 1
				AND f.is_deleted = 0
				AND g.status_active = 1
				AND g.is_deleted = 0
				AND g.entry_form IN(61) and g.is_sales=1
				and h.userid=$user_id and h.entry_form in(58133)
				AND d.company_id IN(".$companyId.")
				".$dateCondition."
			GROUP BY 
				d.company_id,
				e.prod_id, e.po_breakdown_id,
				f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box
		";
		//disconnect($con); die;
		//echo $sqlNoOfRollIssue; //die;
		$sqlNoOfRollIssueResult = sql_select($sqlNoOfRollIssue);
		$noOfRollIssueArr = array();
		foreach($sqlNoOfRollIssueResult as $row)
		{
			$compId = $row[csf('company_id')];
			$productId = $row[csf('prod_id')];
			$orderId = $row[csf('po_breakdown_id')];
			$storeId = $row[csf('store_id')]*1;
			$floorId = $row[csf('floor_id')]*1;
			$roomId = $row[csf('room')]*1;
			$rackId = $row[csf('rack')]*1;
			$selfId = $row[csf('self')]*1;
			$binId = $row[csf('bin_box')]*1;
			
			if ($reportType == 1 || $reportType == 3)
			{
				$issueQtyArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['issueQty'] += $row[csf('issue_qty')];
				$noOfRollIssueArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollIssueQty'] += $row[csf('issue_roll')];
			}
			elseif ($reportType == 2) // FSO Wise
			{
				$issueQtyArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['issueQty'] += $row[csf('issue_qty')];
				$noOfRollIssueArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollIssueQty'] += $row[csf('issue_roll')];
			}
			elseif ($reportType == 4)
			{
			}
		}
		unset($sqlNoOfRollIssueResult);
	}
	//echo $issueQty."=".$rollIssueQty;
	//echo "<pre>";
	//print_r($issueQtyArr);
	
	if(empty($dataArr))
	{
		echo get_empty_data_msg();
		die;
	}
	
	$company_arr = return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_array = return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$count_arr = return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');	
	$color_arr = return_library_array( 'SELECT id, color_name FROM lib_color', 'id', 'color_name');	
	$floorRoomRackSelfArr = return_library_array( "SELECT a.floor_room_rack_id, a.floor_room_rack_name FROM lib_floor_room_rack_mst a  WHERE a.status_active = 1 AND a.is_deleted = 0 AND a.company_id IN(".$companyId.")", 'floor_room_rack_id', 'floor_room_rack_name');

		
	//composition and constructtion
	$composition_arr=array();
	$constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row) 
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($deter_array);

	//for order wise and rack wise button
	if ($reportType == 1 || $reportType == 2 || $reportType == 3)
	{
		$prodArray = array();
		$poArray = array();
		foreach($dataArr as $compId=>$compArr)
		{
			foreach($compArr as $productId=>$productArr)
			{
				foreach($productArr as $orderId=>$orderArr)
				{
					foreach($orderArr as $storeId=>$storeArr)
					{
						foreach($storeArr as $floorId=>$floorArr)
						{
							foreach($floorArr as $roomId=>$roomArr)
							{
								foreach($roomArr as $rackId=>$rackArr)
								{
									foreach($rackArr as $selfId=>$selfArr)
									{
										foreach($selfArr as $binId=>$row)
										{
											$prodArray[$productId] = $productId;
											$poArray[$orderId] = $orderId;
										}
									}
								}
							}
						}
					}
				}
			}
		}

		$sqlYarn = "SELECT e.prod_id, e.entry_form, g.febric_description_id, g.gsm, g.width, g.machine_dia, g.stitch_length, g.color_id, g.color_range_id, g.yarn_count, g.brand_id, g.yarn_lot, d.booking_id, g.yarn_prod_id from order_wise_pro_details e inner join pro_grey_prod_entry_dtls g on e.dtls_id = g.id   inner join inv_receive_master d on d.id = g.mst_id
		where e.entry_form in(2,22,58,84) ".where_con_using_array($prodArray, '0', 'e.prod_id')."";
		// echo $sqlYarn;
		$sqlYarnRslt = sql_select($sqlYarn);
		$yarnInfoArr = array();
		foreach($sqlYarnRslt as $row)
		{
			$prodId = $row[csf('prod_id')];
			// echo $prodId.'===<br>';
			$yarnInfoArr[$prodId]['construction'] = $constructtion_arr[$row[csf('febric_description_id')]];
			$yarnInfoArr[$prodId]['composition'] = $composition_arr[$row[csf('febric_description_id')]];
			$yarnInfoArr[$prodId]['gsm'] = $row[csf('gsm')];
			$yarnInfoArr[$prodId]['width'] = $row[csf('width')];
			$yarnInfoArr[$prodId]['machine_dia'] = $row[csf('machine_dia')];
			$yarnInfoArr[$prodId]['stitch_length'] = $row[csf('stitch_length')];
			if ($row[csf('entry_form')]==2) 
			{
				$yarnInfoArr[$prodId]['program_no'] = $row[csf('booking_id')];
			}
			
			$expColor = explode(',', $row[csf('color_id')]);
			$clrArr = array();
			foreach($expColor as $clr)
			{
				$clrArr[$clr] = $clr;
			}
			
			$yarnInfoArr[$prodId]['color_id'] = implode(',', $clrArr);
			$yarnInfoArr[$prodId]['color_range_id'] = $color_range[$row[csf('color_range_id')]];
			$yarnInfoArr[$prodId]['yarn_count'] = $row[csf('yarn_count')];
			$yarnInfoArr[$prodId]['brand_id'] = $row[csf('brand_id')];
			$yarnInfoArr[$prodId]['yarn_lot'] = $row[csf('yarn_lot')];

			if($row[csf("yarn_prod_id")] !=""){
				$yarnInfoArr[$prodId]['yarn_prod_id'] = $row[csf('yarn_prod_id')];
				$all_yarn_prod_id_arr[$row[csf("yarn_prod_id")]] = $row[csf("yarn_prod_id")];
			}
		}
		unset($sqlYarnRslt);
		//echo "<pre>";
		//print_r($all_yarn_prod_id_arr);die;
		$all_yarn_prod_id_arr = array_filter($all_yarn_prod_id_arr);

		if(count($all_yarn_prod_id_arr) > 0)
		{
			$all_yarn_prod_id = implode(",", $all_yarn_prod_id_arr);
			$yarnProdCond = $all_yarn_prod_id_cond = "";
			if($db_type==2 && count($all_yarn_prod_id_arr)>999)
			{
				$all_yarn_prod_id_chunk=array_chunk($all_yarn_prod_id_arr,999) ;
				foreach($all_yarn_prod_id_chunk as $chunk_prog)
				{
					$chunk_prog_val=implode(",",$chunk_prog);
					$yarnProdCond.=" a.id in($chunk_prog_val) or ";
				}

				$all_yarn_prod_id_cond.=" and (".chop($yarnProdCond,'or ').")";

			}
			else
			{
				$all_yarn_prod_id_cond=" and a.id in($all_yarn_prod_id)";
			}

			$supplier_yarn = sql_select("select a.id, b.short_name, a.yarn_type, a.yarn_comp_type1st from product_details_master a, lib_supplier b where  a.supplier_id = b.id $all_yarn_prod_id_cond and b.status_active = 1 and a.status_active=1");
			foreach ($supplier_yarn as $val) {
				$yarnProdArr[$val[csf('id')]]['yarn_supplier'] = $val[csf('short_name')];
				$yarnProdArr[$val[csf('id')]]['yarn_type'] = $val[csf('yarn_type')];
				$yarnProdArr[$val[csf('id')]]['yarn_comp_type1st'] = $val[csf('yarn_comp_type1st')];
			}
			unset($supplier_yarn);
		}
	}
	
	/*
	|--------------------------------------------------------------------------
	| Summary
	|--------------------------------------------------------------------------
	|
	*/
	
	if ($reportType == 1)
    {
		$con = connect();
		execute_query("DELETE FROM TMP_PO_ID WHERE USER_ID = ".$user_id."");
		oci_commit($con);
		
		$con = connect();
		foreach($poArray as $poId)
		{
			$dataPoArr[]= "(".$poId.",".$user_id.")";
		}
		$con = connect();
		$rID = sql_insert_zs("TMP_PO_ID", 'PO_ID,USER_ID', $dataPoArr, 1, 0);
		oci_commit($con);
		
		foreach($dataArr as $compId=>$compArr)
		{
			foreach($compArr as $productId=>$productArr)
			{
				foreach($productArr as $orderId=>$orderArr)
				{
					foreach($orderArr as $storeId=>$storeArr)
					{
						foreach($storeArr as $floorId=>$floorArr)
						{
							foreach($floorArr as $roomId=>$roomArr)
							{
								foreach($roomArr as $rackId=>$rackArr)
								{
									foreach($rackArr as $selfId=>$selfArr)
									{
										foreach($selfArr as $binId=>$row)
										{
											//total receive calculation
											$row['totalRcvQty'] = number_format($row['rcvQty'],2,'.','')+number_format($row['issueReturnQty'],2,'.','')+number_format($row['transferInQty'],2,'.','');
											
											//total issue calculation
											$row['issueQty'] = $issueQtyArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['issueQty'];
											$row['rcvReturnQty'] = 0;
											$row['transferOutQty'] = $transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['transferOutQty'];
											$row['totalIssueQty'] = number_format($row['issueQty'],2,'.','')+number_format($row['rcvReturnQty'],2,'.','')+number_format($row['transferOutQty'],2,'.','');
											
											//stock qty calculation
											$row['stockQty'] = $row['totalRcvQty'] - $row['totalIssueQty'];

											//roll balance calculation
											$row['rollIssueQty'] = $transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollIssueQty']+$noOfRollIssueArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollIssueQty'];
											$row['rollBalanceQty'] = $row['rollRcvQty'] - $row['rollIssueQty'];

											$fabric_construction = $yarnInfoArr[$productId]['construction'];
											$fab_color_id = $yarnInfoArr[$productId]['color_id'];

											$newDataArr[$compId][$orderId][$fabric_construction]['fab_color_id'] .=$fab_color_id.',';
											$newDataArr[$compId][$orderId][$fabric_construction]['rcvQty'] +=$row['rcvQty'];
											$newDataArr[$compId][$orderId][$fabric_construction]['issueReturnQty'] +=$row['issueReturnQty'];
											$newDataArr[$compId][$orderId][$fabric_construction]['transferInQty'] +=$row['transferInQty'];
											$newDataArr[$compId][$orderId][$fabric_construction]['totalRcvQty'] +=$row['totalRcvQty'];

											$newDataArr[$compId][$orderId][$fabric_construction]['issueQty'] +=$row['issueQty'];
											$newDataArr[$compId][$orderId][$fabric_construction]['rcvReturnQty'] +=$row['rcvReturnQty'];
											$newDataArr[$compId][$orderId][$fabric_construction]['transferOutQty'] +=$row['transferOutQty'];
											$newDataArr[$compId][$orderId][$fabric_construction]['totalIssueQty'] +=$row['totalIssueQty'];
											$newDataArr[$compId][$orderId][$fabric_construction]['stockQty'] +=$row['stockQty'];

											$newDataArr[$compId][$orderId][$fabric_construction]['rollRcvQty'] +=$row['rollRcvQty'];
											$newDataArr[$compId][$orderId][$fabric_construction]['rollIssueQty'] +=$row['rollIssueQty'];
											$newDataArr[$compId][$orderId][$fabric_construction]['rollBalanceQty'] +=$row['rollBalanceQty'];
											$productIdsArr[$compId][$orderId][$fabric_construction][$productId] =$productId;
											

										}
									}
								}
							}
						}
					}
				}
			}
		}

		$sql_fso_book_qty = sql_select("select a.booking_id, a.sales_booking_no, a.within_group, a.job_no, b.mst_id, b.color_id, b.determination_id, b.grey_qty 
		from fabric_sales_order_mst a, fabric_sales_order_dtls b, tmp_po_id c 
		where a.id=b.mst_id and b.mst_id=c.po_id and c.user_id=$user_id and b.status_active=1 and b.is_deleted=0");

		foreach ($sql_fso_book_qty as $val) 
		{
			$fabric_construction = $constructtion_arr[$val[csf('determination_id')]];
			$fso_booking_req[$val[csf("mst_id")]][$fabric_construction]["req_qty"] +=$val[csf("grey_qty")];

			$sales_ord_ref[$val[csf("mst_id")]]['within_group'] = $val[csf("within_group")];
		}

		$width = 2080;
		?>
		<fieldset style="width:<?php echo $width+20; ?>px;margin:5px auto;">
			<table width="<?php echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
				<thead>
                    <tr class="form_caption" style="border:none;">
                    	<th align="center" colspan="24" style="font-size:16px"><strong>Rack Wise Grey Fabrics Stock Report</strong></th>
                    </tr>
                    <tr>
						<th width="30">SL</th>
						<th width="60">Company</th>

						<th width="120">Sales Order No</th>
						<th width="100">Sales Job/ Booking No.</th>
						<th width="100">IR/IB.</th>
						<th width="100">Customer Name</th>
						<th width="100">Cust. Buyer Name</th>

						<th width="110">Fab. Constraction</th>
						<th width="100">Fabric Color</th>

						<th width="100">Required Qty</th>
						<th width="80">Receive</th>
						<th width="80">Issue Return</th>
						<th width="80">Transfer In</th>
						<th width="80">Total Recv.</th>
						<th width="80">No of Roll Recv.</th>
						<th width="80">Receive Balance</th>
						<th width="80">Issue</th>
						<th width="80">Receive Return</th>
						<th width="80">Transfer Out</th>
						<th width="80">Total Issue</th>
						<th width="80">No of Roll Issued</th>
						<th width="80">Issue Balance</th>
						<th width="100">Stock Qty.</th>
						<th width="100">No Of Roll Bal.</th>
					</tr>
				</thead>
			</table>
			<div style="width:<?php echo $width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="<?php echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
					<tbody>
					<?php
					$sl = 0;
					$grandTotal = array();
					// echo "<pre>";print_r($newDataArr);
					foreach($newDataArr as $compId=>$compArr)
					{
						foreach($compArr as $orderId=>$orderArr)
						{
							foreach($orderArr as $fabric_construction=>$row)
							{
								//foreach($fabric_constructionArr as $fab_color_id=>$row)
								//{
								if($valueType != 1 && $row['stockQty'] == 0)
								{
									continue;
								}
								
								if($row['stockQty'] >= 0)
								{
									$sl++;
									$row['fso_no'] = $bookingInfoArr[$orderId]['fso_no'];
									$row['buyer_id'] = $bookingInfoArr[$orderId]['buyer_id'];
									$row['customer_buyer'] = $bookingInfoArr[$orderId]['customer_buyer'];
									$row['style_ref_no'] = $bookingInfoArr[$orderId]['style_ref_no'];
									$row['booking_no'] = $bookingInfoArr[$orderId]['booking_no'];
									
									$productIds = implode("*",$productIdsArr[$compId][$orderId][$fabric_construction]);
									$required_qnty = $fso_booking_req[$orderId][$fabric_construction]["req_qty"];

									$within_group = $sales_ord_ref[$val[csf("mst_id")]]['within_group'];

									$color_id_arr=array_unique(explode(",", chop($row['fab_color_id'],",")));
									$color_names="";
									foreach ($color_id_arr as $key => $id) 
									{
										$color_names.=$color_arr[$id].',';
									}
									$color_names=chop($color_names,",");

									$buyer_name ='';
									if($within_group==1)
									{
										$buyer_name = $company_arr[$row['buyer_id']];
									}
									else
									{
										$buyer_name = $buyer_array[$row['buyer_id']];
									}
									
									?>
                                    <tr valign="middle">
                                        <td width="30" align="center"><?php echo $sl; ?></td>
                                        <td width="60"><div style="word-break:break-all"><?php echo $company_arr[$compId]; ?></div></td>

                                        <td width="120"><div style="word-break:break-all">
											<a href="##" onclick="fabric_sales_order_print6('<? echo $compId;?>','<? //echo $compId;?>','<? echo $row['booking_no'];?>','<? echo $row['fso_no'];?>', '<? echo $within_group;?>');"><?php echo $row['fso_no']; ?></a></div>
										</td>
                                        <td width="100"><div style="word-break:break-all"><?php echo $row['booking_no']; ?></div></td>
                                        <td width="100"><div style="word-break:break-all"><?php echo chop($int_ref_arr[$row['booking_no']],","); ?></div></td>
                                        <td width="100">
                                        	<div style="word-break:break-all"><?php echo $buyer_name; ?></div>
                                        </td>
                                        <td width="100">
                                        	<div style="word-break:break-all"><?php echo $buyer_array[$row['customer_buyer']]; ?></div>
                                        </td>
                                        <td width="110"><div style="word-break:break-all"><?php echo $fabric_construction; ?></div></td>
                                        <td width="100"><div style="word-break:break-all" title="<?=$fab_color_id;?>"><?php echo $color_names; ?></div></td>
                                        <td width="100" align="right"><?php echo number_format($required_qnty,2); ?></td>

                                        <td width="80" align="right"><?php echo number_format($row['rcvQty'],2); ?></td>
                                        <td width="80" align="right"><?php echo number_format($row['issueReturnQty'],2); ?></td>
                                        <td width="80" align="right"><?php echo number_format($row['transferInQty'],2); ?></td>
                                        <td width="80" align="right"><?php echo number_format($row['totalRcvQty'],2); ?></td>
                                        <td width="80" align="right">
                                        	<a href='#report_details' onClick="openmypage('<? echo $orderId; ?>','<? echo $productIds; ?>','','grey_recv_issue_popup',1);"><? echo number_format($row['rollRcvQty'],2); ?></a>
                                        </td>
                                        <td width="80" align="right"><?php echo number_format($required_qnty-$row['totalRcvQty'],2); ?></td>

                                        <td width="80" align="right"><?php echo number_format($row['issueQty'],2); ?></td>
                                        <td width="80" align="right"><?php echo number_format($row['rcvReturnQty'],2); ?></td>
                                        <td width="80" align="right"><?php echo number_format($row['transferOutQty'],2); ?></td>
                                        <td width="80" align="right"><?php echo number_format($row['totalIssueQty'],2); ?></td>
                                        <td width="80" align="right">
                                        	<a href='#report_details' onClick="openmypage('<? echo $orderId; ?>','<? echo $productIds; ?>','','grey_recv_issue_popup',2);"><? echo number_format($row['rollIssueQty'],2); ?></a>
                                        </td>
                                        <td width="80" align="right"><?php echo number_format($required_qnty-$row['totalIssueQty'],2); ?></td>

                                        <td width="100" align="right"><?php echo number_format($row['stockQty'],2); ?></td>
                                        <td width="100" align="right"><a href="##" onclick="openmypage_rollbal('<? echo $orderId;?>','<? echo $productIds;?>','');"><?php echo $row['rollBalanceQty']; ?></a></td>
                                    </tr>
									<?php
									//$grandTotal
									$grandTotal['requ_qty'] += number_format($required_qnty,2,'.','');
									$grandTotal['rcvQty'] += number_format($row['rcvQty'],2,'.','');
									$grandTotal['issueReturnQty'] += number_format($row['issueReturnQty'],2,'.','');
									$grandTotal['transferInQty'] += number_format($row['transferInQty'],2,'.','');
									$grandTotal['totalRcvQty'] += number_format($row['totalRcvQty'],2,'.','');
									$grandTotal['totalRollRcvQty'] += number_format($row['rollRcvQty'],2,'.','');
									$grandTotal['totalRcvBalanceQty'] += number_format($required_qnty-$row['totalRcvQty'],2,'.','');
							
									$grandTotal['issueQty'] += number_format($row['issueQty'],2,'.','');
									$grandTotal['rcvReturnQty'] += number_format($row['rcvReturnQty'],2,'.','');
									$grandTotal['transferOutQty'] += number_format($row['transferOutQty'],2,'.','');
									$grandTotal['totalIssueQty'] += number_format($row['totalIssueQty'],2,'.','');
									$grandTotal['totalRollIssueQty'] += number_format($row['rollIssueQty'],2,'.','');
									$grandTotal['totalIssueBalanceQty'] += number_format($required_qnty-$row['totalIssueQty'],2,'.','');
									$grandTotal['totalStockQty'] += number_format($row['stockQty'],2,'.','');
									$grandTotal['totalRollBalanceQty'] += $row['rollBalanceQty'];
								}			
								//}
							}
						}
					}
                    ?>
                    </tbody>
                </table>
            </div>
            <table width="<?php echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_foot">
                <tfoot>
                    <tr>

                        <th width="30">&nbsp;</th>
                        <th width="60">&nbsp;</th>

                        <th width="120">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>

                        <th width="110">&nbsp;</th>
						<th width="100">Total</th>
                        <th width="100" align="right" id="value_requQty"><?php echo number_format($grandTotal['requ_qty'],2); ?></th>

						<th width="80" align="right" id="value_RcvQty"><?php echo number_format($grandTotal['rcvQty'],2); ?></th>
						<th width="80" align="right" id="value_issueRtnQty"><?php echo number_format($grandTotal['issueReturnQty'],2); ?></th>
						<th width="80" align="right" id="value_transInQty"><?php echo number_format($grandTotal['transferInQty'],2); ?></th>
						<th width="80" align="right" id="value_totalRcvQty"><?php echo number_format($grandTotal['totalRcvQty'],2); ?></th>
                        <th width="80" align="right" id="value_totalRollRcvQty"><?php echo number_format($grandTotal['totalRollRcvQty'],2); ?></th>
                        <th width="80" align="right" id="value_RcvBalanceQty"><?php echo number_format($grandTotal['totalRcvBalanceQty'],2); ?></th>

                        <th width="80" align="right" id="value_issueQty"><?php echo number_format($grandTotal['issueQty'],2); ?></th>
                        <th width="80" align="right" id="value_recvRtnQty"><?php echo number_format($grandTotal['rcvReturnQty'],2); ?></th>
                        <th width="80" align="right" id="value_transOutQty"><?php echo number_format($grandTotal['transferOutQty'],2); ?></th>
                        <th width="80" align="right" id="value_totalIssueQty"><?php echo number_format($grandTotal['totalIssueQty'],2); ?></th>
                        <th width="80" align="right" id="value_totalRollIssueQty"><?php echo number_format($grandTotal['totalRollIssueQty'],2); ?></th>
                        <th width="80" align="right" id="value_issueBalanceQty"><?php echo number_format($grandTotal['totalIssueBalanceQty'],2); ?></th>

                        <th width="100" align="right" id="value_totalStockQty"><?php echo number_format($grandTotal['totalStockQty'],2); ?></th>
                        <th width="100" align="right" id="value_totalRollBalanceQty"><?php echo $grandTotal['totalRollBalanceQty']; ?></th>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
        <?php
    }

	/*
	|--------------------------------------------------------------------------
	| FSO Wise
	|--------------------------------------------------------------------------
	|
	*/
	else if ($reportType == 2)
	{
		$con = connect();
		execute_query("DELETE FROM TMP_PO_ID WHERE USER_ID = ".$user_id."");
		oci_commit($con);
		
		$con = connect();
		foreach($poArray as $poId)
		{
			$dataPoArr[]= "(".$poId.",".$user_id.")";
		}
		$con = connect();
		$rID = sql_insert_zs("TMP_PO_ID", 'PO_ID,USER_ID', $dataPoArr, 1, 0);
		oci_commit($con);
		//disconnect($con);

		//for booking information
		
		//echo "<pre>";
		//print_r($bookingInfoArr);
				
		$width = 3190;
		?>
		<fieldset style="width:<?php echo $width+20; ?>px;margin:5px auto;">
			<table width="<?php echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
				<div style="text-align:center;" class="search_type"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --","","","","2,3,4" ); ?></div>
				<thead>
                    <tr class="form_caption" style="border:none;">
                    	<th align="center" colspan="38" style="font-size:16px"><strong>Rack Wise Grey Fabrics Stock Report</strong></th>
                    </tr>
                    <tr>
						<th width="30">SL</th>
						<th width="60">Company</th>
						<th width="60">Floor</th>
						<th width="60">Room</th>
						<th width="60">Rack</th>
						<th width="60">Shelf</th>
						<th width="60">Bin</th>

						<th width="120">Sales Order No</th>
						<th width="120">Sales Order Remarks</th>
						<th width="100">Sales Job/ Booking No.</th>
						<th width="100">IR/IB</th>
						<th width="100">Style Ref. No.</th>
						<th width="100">Customer Name</th>
						<th width="100">Cust. Buyer Name</th>

						<th width="110">Fab. Constraction</th>
						<th width="120">Fab. Composition</th>
						<th width="50">GSM</th>
						<th width="50">F/Dia</th>
						<th width="50">M/C Dia</th>
						<th width="60">Stich Length</th>
						<th width="100">Fabric Color</th>
						<th width="100">Color Range</th>
						<th width="60">Y. Count</th>
						<th width="120">Y. Composition</th>
						<th width="80">Y. Type</th>
						<th width="60">Y. Brand</th>
						<th width="100">Y. Lot</th>

						<th width="80">Recv. Qty.</th>
						<th width="80">Issue Ret. Qty.</th>
						<th width="80">Transf. In Qty.</th>
						<th width="80">Total Recv.</th>
						<th width="80">No of Roll Recv.</th>
						<th width="80">Issue Qty.</th>
						<th width="80">Recv. Ret. Qty.</th>
						<th width="80">Transf. Out Qty.</th>
						<th width="80">Total Issue</th>
						<th width="80">No of Roll Issued</th>
						<th width="100">Stock Qty.</th>
						<th width="100">No Of Roll Bal.</th>
					</tr>
				</thead>
			</table>
			<div style="width:<?php echo $width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="<?php echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
					<tbody>
					<?php
					$sl = 0;
					$grandTotal = array();
					// echo '<pre>';print_r($dataArr);
					foreach($dataArr as $compId=>$compArr)
					{
						foreach($compArr as $productId=>$productArr)
						{
							foreach($productArr as $orderId=>$orderArr)
							{
								foreach($orderArr as $storeId=>$storeArr)
								{
									foreach($storeArr as $floorId=>$floorArr)
									{
										foreach($floorArr as $roomId=>$roomArr)
										{
											foreach($roomArr as $rackId=>$rackArr)
											{
												foreach($rackArr as $selfId=>$selfArr)
												{
													foreach($selfArr as $binId=>$row)
													{
														// echo $roomId.'<br>';
														//echo "[compId=$compId][productId=$productId][orderId=$orderId][storeId=$storeId][floorId=$floorId][roomId=$roomId][rackId=$rackId][selfId=$selfId][binId=$binId]<br>";
														//total receive calculation
														$row['totalRcvQty'] = number_format($row['rcvQty'],2,'.','')+number_format($row['issueReturnQty'],2,'.','')+number_format($row['transferInQty'],2,'.','');
														
														//total issue calculation
														$row['issueQty'] = $issueQtyArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['issueQty'];
														$row['rcvReturnQty'] = 0;
														$row['transferOutQty'] = $transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['transferOutQty'];
														$row['totalIssueQty'] = number_format($row['issueQty'],2,'.','')+number_format($row['rcvReturnQty'],2,'.','')+number_format($row['transferOutQty'],2,'.','');
														
														//stock qty calculation
														$row['stockQty'] = $row['totalRcvQty'] - $row['totalIssueQty'];

														//roll balance calculation
														$row['rollIssueQty'] = $transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollIssueQty']+$noOfRollIssueArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollIssueQty'];
														$row['rollBalanceQty'] = $row['rollRcvQty'] - $row['rollIssueQty'];

														// ======= basic information start ========
                                            			$machine_dia =implode(",",array_filter(array_unique(explode(",", $row['machine_dia']))));
														$stitch_length =implode(",",array_filter(array_unique(explode(",", $row['stitch_length']))));

														$color_id_arr = array_filter(array_unique(explode(",", $row['color_id'])));
			                                            $colors="";
			                                            foreach ($color_id_arr as $color) 
			                                            {
			                                                $colors .= $color_arr[$color] . ",";
			                                            }
			                                            $colors = rtrim($colors, ", ");

			                                            $color_range_id_arr = array_filter(array_unique(explode(",", $row['color_range_id'])));
			                                            $color_range_name="";
			                                            foreach ($color_range_id_arr as $colorRange) 
			                                            {
			                                                $color_range_name .= $color_range[$colorRange] . ",";
			                                            }
			                                            $color_range_name = rtrim($color_range_name, ", ");

			                                            $yarn_counts_arr = array_unique(array_filter(explode(",", $row['yarn_count'])));
			                                            $yarn_counts="";
			                                            foreach ($yarn_counts_arr as $count) {
			                                                $yarn_counts .= $count_arr[$count] . ",";
			                                            }
			                                            // $yarn_counts = rtrim($yarn_counts, ", ");
			                                            $yarn_counts =implode(",",array_filter(array_unique(explode(",", $yarn_counts))));

			                                            if ($row['entry_form']==22) // for create barcode from Knit Grey Fabric Receive
			                                            {
			                                            	$brand_id_arr = array_unique(array_filter(explode(",", $row['brand_id'])));
				                                            $yarn_brand = "";
				                                            foreach ($brand_id_arr as $bid)
				                                            {
				                                                $yarn_brand .= ($yarn_brand =="") ? $brand_arr[$bid] :  ",". $brand_arr[$bid];
				                                            }
				                                            $yarn_brand =implode(",",array_filter(array_unique(explode(",", $yarn_brand))));

				                                            $yarn_comp = $yarnInfoArr[$productId]['composition'];
			                                            }
			                                            else
			                                            {
			                                            	$yarn_id_arr = array_unique(array_filter(explode(",", $row['yarn_prod_id'])));
				                                            $yarn_brand = $yarn_comp = $yarn_type_name = "";
				                                            foreach ($yarn_id_arr as $yid)
				                                            {
				                                                $yarn_brand .= ($yarn_brand =="") ? $yarn_ref[$yid]["brand"] :  ",". $yarn_ref[$yid]["brand"];
				                                                $yarn_comp .= ($yarn_comp =="") ? $yarn_ref[$yid]["comp"] :  ",". $yarn_ref[$yid]["comp"];
				                                                $yarn_type_name .= ($yarn_type_name =="") ? $yarn_ref[$yid]["type"] :  ",". $yarn_ref[$yid]["type"];
				                                            }
				                                            $yarn_comp =implode(",",array_filter(array_unique(explode(",", $yarn_comp))));
				                                            $yarn_type_name =implode(",",array_filter(array_unique(explode(",", $yarn_type_name))));
				                                            $yarn_brand =implode(",",array_filter(array_unique(explode(",", $yarn_brand))));
			                                            }			                                            

			                                            $yarn_lot =implode(",",array_filter(array_unique(explode(",", $row['yarn_lot']))));
			                                            // ====== basic information end ========

														if($valueType != 1 && $row['stockQty'] == 0)
														{
															continue;
														}
														
														if($row['stockQty'] >= 0)
														{
															$sl++;
															/*if($sl == 10000)
															{
																break;
															}*/
															//echo $yarnCount;
															//print_r($yarnCountArr);
															$row['fso_no'] = $bookingInfoArr[$orderId]['fso_no'];
															$row['fso_remarks'] = $bookingInfoArr[$orderId]['fso_remarks'];
															$row['buyer_id'] = $bookingInfoArr[$orderId]['buyer_id'];
															$row['customer_buyer'] = $bookingInfoArr[$orderId]['customer_buyer'];
															$row['style_ref_no'] = $bookingInfoArr[$orderId]['style_ref_no'];
															$row['booking_no'] = $bookingInfoArr[$orderId]['booking_no'];
															
															$row['construction'] = $yarnInfoArr[$productId]['construction'];
															$row['composition'] = $yarnInfoArr[$productId]['composition'];
															$row['gsm'] = $yarnInfoArr[$productId]['gsm'];
															$row['width'] = $yarnInfoArr[$productId]['width'];
															$row['machine_dia'] = $yarnInfoArr[$productId]['machine_dia'];
															$row['stitch_length'] = $yarnInfoArr[$productId]['stitch_length'];
															$row['program_no'] = $yarnInfoArr[$productId]['program_no'];
															// echo $row['program_no'].'<br>';
															$row['color_id'] = $yarnInfoArr[$productId]['color_id'];
															$row['color_range_id'] = $yarnInfoArr[$productId]['color_range_id'];
															$row['yarn_count'] = $yarnInfoArr[$productId]['yarn_count'];
															$row['yarn_prod_id'] = $yarnInfoArr[$productId]['yarn_prod_id'];
															
															$yarnCountArr=explode(',', $row['yarn_count']);
															$yarnCount="";
															foreach ($yarnCountArr as $key => $yCount) 
															{
																if ($yarnCount=="") 
																{
																	$yarnCount.=$count_arr[$yCount];
																}
																else
																{
																	$yarnCount.=', '.$count_arr[$yCount];
																}
															}
															$row['brand_id'] = $yarnInfoArr[$productId]['brand_id'];
															$row['yarn_lot'] = $yarnInfoArr[$productId]['yarn_lot'];

															$yarn_prod_idArr=explode(',', $row['yarn_prod_id']);
															$yarn_supplier=$yarn_type_no=$yarn_compositions="";
															foreach ($yarn_prod_idArr as $yProd) 
															{
																$yarn_supplier .= $yarnProdArr[$yProd]['yarn_supplier'].",";
																$yarn_type_no .= $yarn_type[$yarnProdArr[$yProd]['yarn_type']].",";
																$yarn_compositions .=$composition_arr[$yarnProdArr[$yProd]['yarn_comp_type1st']]."*";
															}
															$yarn_supplier = implode(",",array_unique(array_filter(explode(",", chop($yarn_supplier,",")))));
															$yarn_type_no = implode(",",array_unique(array_filter(explode(",", chop($yarn_type_no,",")))));
															$yarn_compositions = implode("*",array_unique(array_filter(explode("*", chop($yarn_compositions,"*")))));

															if ($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
															?>
                                                            <tr valign="middle" bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $sl;?>','<? echo $bgcolor;?>')" id="tr<? echo $sl;?>">	
                                                                <td width="30" align="center"><?php echo $sl; ?></td>
                                                                <td width="60"><div style="word-break:break-all"><?php echo $company_arr[$compId]; ?></div></td>
                                                                <td width="60"><div style="word-break:break-all"><?php echo $floorRoomRackSelfArr[$floorId]; ?></div></td>
                                                                <td width="60"><div style="word-break:break-all"><?php echo $floorRoomRackSelfArr[$roomId]; ?></div></td>
                                                                <td width="60"><div style="word-break:break-all"><?php echo $floorRoomRackSelfArr[$rackId]; ?></div></td>
                                                                <td width="60"><div style="word-break:break-all"><?php echo $floorRoomRackSelfArr[$selfId]; ?></div></td>
                                                                <td width="60"><div style="word-break:break-all"><?php echo $floorRoomRackSelfArr[$binId]; ?></div></td>

                                                                <td width="120"><div style="word-break:break-all"><?php echo $row['fso_no']; ?></div></td>
                                                                <td width="120"><div style="word-break:break-all"><?php echo $row['fso_remarks']; ?></div></td>
                                                                <td width="100"><div style="word-break:break-all"><?php echo $row['booking_no']; ?></div></td>
                                                                <td width="100"><div style="word-break:break-all"><?php echo chop($int_ref_arr[$row['booking_no']],","); ?></div></td>
                                                                <td width="100"><div style="word-break:break-all"><?php echo $row['style_ref_no']; ?></div></td>
                                                                <td width="100">
                                                                	<div style="word-break:break-all"><?php echo $buyer_array[$row['buyer_id']]; ?></div>
                                                                </td>
                                                                <td width="100">
                                                                	<div style="word-break:break-all"><?php echo $buyer_array[$row['customer_buyer']]; ?></div>
                                                                </td>
                                                                
                                                                <td width="110"><div style="word-break:break-all"><?php echo $row['construction']; ?></div></td>
                                                                <td width="120"><div style="word-break:break-all"><?php echo $row['composition']; ?></div></td>
                                                                <td width="50" title="prodId: <? echo $prodId.'='.$productId; ?>"><?php echo $row['gsm']; ?></td>
                                                                <td width="50"><?php echo $row['width']; ?></td>
                                                                <td width="50"><?php echo $machine_dia;//$row['machine_dia']; ?></td>
                                                                <td width="60"><p><?php echo $stitch_length;//$row['stitch_length']; ?></p></td>
                                                                <td width="100"><div style="word-break:break-all"><?php echo $colors;//$row['color_id']; ?></div></td>
                                                                <td width="100"><div style="word-break:break-all"><?php echo $color_range_name;//$row['color_range_id']; ?></div></td>
                                                                <td width="60"><div style="word-break:break-all"><?php echo $yarn_counts;//$yarnCount;//$count_arr[$row['yarn_count']]; ?></div></td>
                                                                <td width="120"><div style="word-break:break-all"><?php echo $yarn_comp;//$yarn_compositions;?></div></td>
                                                                <td width="80"><div style="word-break:break-all"><?php echo $yarn_type_name;//$yarn_type_no;?></div></td>

                                                                <td width="60"><div style="word-break:break-all"><?php echo $yarn_brand;//$yarn_supplier;//$brand_arr[$row['brand_id']]; ?></div></td>
                                                                <td width="100"><div style="word-break:break-all"><?php echo $yarn_lot;//$row['yarn_lot']; ?></div></td>
                                                                <td width="80" align="right"><?php echo number_format($row['rcvQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['issueReturnQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['transferInQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['totalRcvQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['rollRcvQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['issueQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['rcvReturnQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['transferOutQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['totalIssueQty'],2); ?></td>
                                                                <td width="80" align="right"><?php echo number_format($row['rollIssueQty'],2); ?></td>
                                                                <td width="100" align="right"><?php echo number_format($row['stockQty'],2); ?></td>
                                                                <td width="100" align="right"><?php echo $row['rollBalanceQty']; ?></td>
                                                            </tr>
															<?php
															//$grandTotal
															$grandTotal['rcvQty'] += number_format($row['rcvQty'],2,'.','');
															$grandTotal['issueReturnQty'] += number_format($row['issueReturnQty'],2,'.','');
															$grandTotal['transferInQty'] += number_format($row['transferInQty'],2,'.','');
															$grandTotal['totalRcvQty'] += number_format($row['totalRcvQty'],2,'.','');
															$grandTotal['totalRollRcvQty'] += number_format($row['rollRcvQty'],2,'.','');
													
															$grandTotal['issueQty'] += number_format($row['issueQty'],2,'.','');
															$grandTotal['rcvReturnQty'] += number_format($row['rcvReturnQty'],2,'.','');
															$grandTotal['transferOutQty'] += number_format($row['transferOutQty'],2,'.','');
															$grandTotal['totalIssueQty'] += number_format($row['totalIssueQty'],2,'.','');
															$grandTotal['totalRollIssueQty'] += number_format($row['rollIssueQty'],2,'.','');
															$grandTotal['totalStockQty'] += number_format($row['stockQty'],2,'.','');
															$grandTotal['totalRollBalanceQty'] += $row['rollBalanceQty'];
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
                    ?>
                    </tbody>
                </table>
            </div>
            <table width="<?php echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_foot">
                <tfoot>
                    <tr>
                        <th width="30">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>

                        <th width="120">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>

                        <th width="110">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>

                        <th width="60">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="100">Total</th>

                        <th width="80" align="right" id="value_rcvQty"><?php //echo number_format($grandTotal['rcvQty'],2); ?></th>
                        <th width="80" align="right" id="value_issueReturnQty"><?php //echo number_format($grandTotal['issueReturnQty'],2); ?></th>
                        <th width="80" align="right" id="value_transferInQty"><?php //echo number_format($grandTotal['transferInQty'],2); ?></th>
                        <th width="80" align="right" id="value_totalRcvQty"><?php //echo number_format($grandTotal['totalRcvQty'],2); ?></th>
                        <th width="80" align="right" id="value_totalRollRcvQty"><?php //echo number_format($grandTotal['totalRollRcvQty'],2); ?></th>
                        <th width="80" align="right" id="value_issueQty"><?php //echo number_format($grandTotal['issueQty'],2); ?></th>
                        <th width="80" align="right" id="value_rcvReturnQty"><?php //echo number_format($grandTotal['rcvReturnQty'],2); ?></th>
                        <th width="80" align="right" id="value_transferOutQty"><?php //echo number_format($grandTotal['transferOutQty'],2); ?></th>
                        <th width="80" align="right" id="value_totalIssueQty"><?php //echo number_format($grandTotal['totalIssueQty'],2); ?></th>
                        <th width="80" align="right" id="value_totalRollIssueQty"><?php //echo number_format($grandTotal['totalRollIssueQty'],2); ?></th>
                        <th width="100" align="right" id="value_totalStockQty"><?php echo number_format($grandTotal['totalStockQty'],2); ?></th>
                        <th width="100" align="right" id="value_totalRollBalanceQty"><?php echo $grandTotal['totalRollBalanceQty']; ?></th>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
        <?php
    }
	
	/*
	|--------------------------------------------------------------------------
	| Rack Wise
	|--------------------------------------------------------------------------
	|
	*/
	else if ($reportType == 3)
	{
		$width = 1650;
		?>
		<fieldset style="width:<? echo $width+20; ?>px;margin:5px auto;">
			<table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
				<thead>
                    <tr class="form_caption" style="border:none;">
                    	<th align="center" colspan="22" style="font-size:16px"><strong>Rack Wise Grey Fabrics Stock Report</strong></th>
                    </tr>
                    <tr>
						<th width="30">SL</th>
						<th width="60">Company</th>
						<th width="60">Floor</th>
						<th width="60">Room</th>
						<th width="60">Rack</th>
						<th width="60">Shelf</th>
						<th width="60">Bin</th>
						<th width="110">Fab. Constraction</th>
						<th width="120">Fab. Composition</th>
						<th width="50">GSM</th>
						<th width="50">F/Dia</th>
						<th width="50">M/C Dia</th>
						<th width="60">Stich Length</th>
						<th width="100">Fabric Color</th>
						<th width="100">Color Range</th>
						<th width="60">Y. Count</th>
						<th width="120">Y. Composition</th>
						<th width="80">Y. Type</th>
						<th width="60">Supplier</th>
						<th width="100">Y. Lot</th>
						<th width="100">Stock Qty.</th>
						<th>No Of Roll Bal.</th>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
					<tbody>
					<?php
					$sl = 0;
					$grandTotal = array();
					// echo '<pre>';print_r($dataArr);
					foreach($dataArr as $compId=>$compArr)
					{
						foreach($compArr as $productId=>$productArr)
						{
							foreach($productArr as $orderId=>$orderArr)
							{
								foreach($orderArr as $storeId=>$storeArr)
								{
									foreach($storeArr as $floorId=>$floorArr)
									{
										foreach($floorArr as $roomId=>$roomArr)
										{
											foreach($roomArr as $rackId=>$rackArr)
											{
												$stockQty = 0;
												$rollBalanceQty = 0;$all_color='';$all_febric_descri='';
												foreach($rackArr as $selfId=>$selfArr)
												{
													foreach($selfArr as $binId=>$row)
													{
														//roll balance calculation
														$rollIssueQty = $transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollIssueQty']+$noOfRollIssueArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['rollIssueQty'];
														$rollBlncQty = $row['rollRcvQty'] - $rollIssueQty;
														
														
														//total receive calculation
														$totalRcvQty = number_format($row['rcvQty'],2,'.','')+number_format($row['issueReturnQty'],2,'.','')+number_format($row['transferInQty'],2,'.','');
														//total issue calculation
														$issueQty = $issueQtyArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['issueQty'];
														$rcvReturnQty = 0;
														$transferOutQty = $transOutArr[$compId][$productId][$orderId][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['transferOutQty'];
														$totalIssueQty = number_format($issueQty,2,'.','')+number_format($rcvReturnQty,2,'.','')+number_format($transferOutQty,2,'.','');
														//$isqty+=$issueQty;
														//$tQty+=$transferOutQty;
														
														//stock calculation
														$stkQty = $totalRcvQty - $totalIssueQty;
														if($stkQty > 0)
														{
															$stockQty += $stkQty;
															$rollBalanceQty += $rollBlncQty;
														}
														$all_color.=$row['color_id'].',';
														$all_febric_descri.=$row['febric_descri'].',';
													}
												}
												
												if($valueType != 1 && $stockQty == 0)
												{
													continue;
												}
												if($stockQty >= 0)
												{
													$sl++;
													$all_febric_descri_arr = array_filter(array_unique(explode(",", $all_febric_descri)));
		                                            $construction="";
		                                            foreach ($all_febric_descri_arr as $febric_desc) 
		                                            {
		                                                $construction .= $constructtion_arr[$febric_desc] . ",";
		                                            }		                                            
		                                            $row['construction'] = rtrim($construction, ", ");
													// $row['construction'] = $yarnInfoArr[$productId]['construction'];
													
													$row['composition'] = $yarnInfoArr[$productId]['composition'];
													$row['gsm'] = $yarnInfoArr[$productId]['gsm'];
													$row['width'] = $yarnInfoArr[$productId]['width'];
													/*$row['machine_dia'] = $yarnInfoArr[$productId]['machine_dia'];
													$row['stitch_length'] = $yarnInfoArr[$productId]['stitch_length'];
													$row['color_id'] = $yarnInfoArr[$productId]['color_id'];
													$row['color_range_id'] = $yarnInfoArr[$productId]['color_range_id'];
													$row['yarn_count'] = $yarnInfoArr[$productId]['yarn_count'];
													
													$yarnCountArr=explode(',', $row['yarn_count']);
													$yarnCount="";
													foreach ($yarnCountArr as $key => $yCount) 
													{
														if ($yarnCount=="") 
														{
															$yarnCount.=$count_arr[$yCount];
														}
														else
														{
															$yarnCount.=', '.$count_arr[$yCount];
														}
													}
													$row['brand_id'] = $yarnInfoArr[$productId]['brand_id'];
													$row['yarn_lot'] = $yarnInfoArr[$productId]['yarn_lot'];

													$row['yarn_prod_id'] = $yarnInfoArr[$productId]['yarn_prod_id'];
													$yarn_prod_idArr=explode(',', $row['yarn_prod_id']);
													$yarn_supplier=$yarn_type_no=$yarn_compositions="";
													foreach ($yarn_prod_idArr as $yProd) 
													{
														$yarn_supplier .= $yarnProdArr[$yProd]['yarn_supplier'].",";
														$yarn_type_no .= $yarn_type[$yarnProdArr[$yProd]['yarn_type']].",";
														$yarn_compositions .=$composition_arr[$yarnProdArr[$yProd]['yarn_comp_type1st']]."*";
													}
													$yarn_supplier = implode(",",array_unique(array_filter(explode(",", chop($yarn_supplier,",")))));
													$yarn_type_no = implode(",",array_unique(array_filter(explode(",", chop($yarn_type_no,",")))));
													$yarn_compositions = implode("*",array_unique(array_filter(explode("*", chop($yarn_compositions,"*")))));*/

													// ======= basic information start ========
                                        			$machine_dia =implode(",",array_filter(array_unique(explode(",", $row['machine_dia']))));
													$stitch_length =implode(",",array_filter(array_unique(explode(",", $row['stitch_length']))));

													// $color_id_arr = array_filter(array_unique(explode(",", $row['color_id'])));
		                                            $color_id_arr = array_filter(array_unique(explode(",", $all_color)));
		                                            $colors="";
		                                            foreach ($color_id_arr as $color) 
		                                            {
		                                                $colors .= $color_arr[$color] . ",";
		                                            }
		                                            $colors = rtrim($colors, ", ");

		                                            $color_range_id_arr = array_filter(array_unique(explode(",", $row['color_range_id'])));
		                                            $color_range_name="";
		                                            foreach ($color_range_id_arr as $colorRange) 
		                                            {
		                                                $color_range_name .= $color_range[$colorRange] . ",";
		                                            }
		                                            $color_range_name = rtrim($color_range_name, ", ");

		                                            $yarn_counts_arr = array_unique(array_filter(explode(",", $row['yarn_count'])));
		                                            $yarn_counts="";
		                                            foreach ($yarn_counts_arr as $count) {
		                                                $yarn_counts .= $count_arr[$count] . ",";
		                                            }
		                                            // $yarn_counts = rtrim($yarn_counts, ", ");
		                                            $yarn_counts =implode(",",array_filter(array_unique(explode(",", $yarn_counts))));

		                                            $yarn_id_arr = array_unique(array_filter(explode(",", $row['yarn_prod_id'])));
		                                            $yarn_brand = $yarn_comp = $yarn_type_name = "";
		                                            foreach ($yarn_id_arr as $yid)
		                                            {
		                                                $yarn_brand .= ($yarn_brand =="") ? $yarn_ref[$yid]["brand"] :  ",". $yarn_ref[$yid]["brand"];
		                                                $yarn_comp .= ($yarn_comp =="") ? $yarn_ref[$yid]["comp"] :  ",". $yarn_ref[$yid]["comp"];
		                                                $yarn_type_name .= ($yarn_type_name =="") ? $yarn_ref[$yid]["type"] :  ",". $yarn_ref[$yid]["type"];
		                                            }
		                                            $yarn_comp =implode(",",array_filter(array_unique(explode(",", $yarn_comp))));
		                                            $yarn_type_name =implode(",",array_filter(array_unique(explode(",", $yarn_type_name))));
		                                            $yarn_brand =implode(",",array_filter(array_unique(explode(",", $yarn_brand))));
		                                            $yarn_lot =implode(",",array_filter(array_unique(explode(",", $row['yarn_lot']))));
		                                            // ====== basic information end ========
													
													?>
													<tr>
														<td width="30" align="center"><?php echo $sl; ?></td>
														<td width="60"><div style="word-break:break-all"><?php echo $company_arr[$compId]; ?></div></td>
														<td width="60"><div style="word-break:break-all"><?php echo $floorRoomRackSelfArr[$floorId]; ?></div></td>
														<td width="60"><div style="word-break:break-all"><?php echo $floorRoomRackSelfArr[$roomId]; ?></div></td>
														<td width="60"><div style="word-break:break-all"><?php echo $floorRoomRackSelfArr[$rackId]; ?></div></td>
														<td width="60"><div style="word-break:break-all"><?php echo $floorRoomRackSelfArr[$selfId]; ?></div></td>
														<td width="60"><div style="word-break:break-all"><?php echo $floorRoomRackSelfArr[$binId]; ?></div></td>
														<td width="110"><div style="word-break:break-all"><?php echo $row['construction']; ?></div></td>
														<td width="120"><div style="word-break:break-all"><?php echo $row['composition']; ?></div></td>
														<td width="50" title="prodId: <? echo $prodId.'='.$productId; ?>"><?php echo $row['gsm']; ?></td>
														<td width="50"><?php echo $row['width']; ?></td>
														<td width="50"><?php echo $machine_dia;//$row['machine_dia']; ?></td>
														<td width="60"><p><?php echo $stitch_length;//$row['stitch_length']; ?></p></td>
														<td width="100"><div style="word-break:break-all"><?php echo $colors;//$row['color_id']; ?></div></td>
														<td width="100"><div style="word-break:break-all"><?php echo $color_range_name;//$row['color_range_id']; ?></div></td>
														<td width="60"><div style="word-break:break-all"><?php echo $yarn_counts;//$yarnCount;//$count_arr[$row['yarn_count']]; ?></div></td>
														<td width="120"><div style="word-break:break-all"><?php echo $yarn_comp;//$yarn_compositions; ?></div></td>
														<td width="80"><div style="word-break:break-all"><?php echo $yarn_type_name;//$yarn_type_no; ?></div></td>
														<td width="60"><div style="word-break:break-all"><?php echo $yarn_brand;//$yarn_supplier; ?></div></td>
														<td width="100"><div style="word-break:break-all"><?php echo $yarn_lot;//$row['yarn_lot']; ?></div></td>
														<td width="100" align="right" title="<?php echo $productId."=".$orderId."=".$rackId; ?>"><?php echo number_format($stockQty,2); ?></td>
														<td align="right"><?php echo $rollBalanceQty; ?></td>
													</tr>
													<?php
													//$grandTotal
													$grandTotal['totalStockQty'] += $stockQty;
													$grandTotal['totalRollBalanceQty'] += $rollBalanceQty;
												}
											}
										}
									}
								}
							}
						}
                    }
                    ?>
                    </tbody>
				</table> 
            </div>
            <table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
                <tfoot>
                    <tr>
                        <th width="30">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">Total</th>
                        <th width="100" align="right" id="value_totalStockQty"><?php echo number_format($grandTotal['totalStockQty'],2); ?></th>
                        <th align="right" id="value_totalRollBalanceQty"><?php echo $grandTotal['totalRollBalanceQty']; ?></th>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
        <?
    }

	execute_query("DELETE FROM TMP_PO_ID WHERE USER_ID = ".$user_id."");
	execute_query("DELETE from tmp_barcode_no where userid=$user_id");
	execute_query("DELETE from tmp_prod_id where userid=$user_id");
	execute_query("DELETE from tmp_booking_id where userid=$user_id");
	oci_commit($con);

	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	
	echo $html."####".$filename."####".$reportType;
	disconnect($con);
	die;
}

/*
|--------------------------------------------------------------------------
| report_generate_summary
|--------------------------------------------------------------------------
|
*/
if($action=="report_generate_summary")
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	ob_start();
	$companyId = str_replace("'", "", $cbo_company_id);
	$buyerId = str_replace("'", "", $cbo_buyer_id);
	$year = str_replace("'", "", $cbo_year);
	$jobNo = trim(str_replace("'", "", $txt_job_no));
	$jobId = str_replace("'", "", $txt_job_id);
	$bookingNo = str_replace("'", "", $txt_booking_no);
	$bookingId = str_replace("'", "", $txt_booking_id);
	$searchBy = str_replace("'", "", $cbo_search_by);
	$searchCommon = str_replace("'", "", $txt_search_comm);
	$product = str_replace("'", "", $txt_product);
	$productId = str_replace("'", "", $txt_product_id);
	$productNo = str_replace("'", "", $txt_product_no);
	$storeId = str_replace("'", "", $txt_store_id);
	$floorId = str_replace("'", "", $txt_floor_id);
	$roomId = str_replace("'", "", $txt_room_id);
	$rackId = str_replace("'", "", $txt_rack_id);
	$valueType = str_replace("'", "", $cbo_value_type);
	$fromDate = str_replace("'", "", $txt_date_from);
	$toDate = str_replace("'", "", $txt_date_to);
	$reportType = str_replace("'", "", $rpt_type);
	//$_SESSION["date_from"] = date('Y-m-d', strtotime($date_from));
	//$_SESSION["date_to"] = date('Y-m-d', strtotime($date_to));
	
	//buyerIdCondition
	$buyerIdCondition = '';
	if($buyerId != 0)
	{
		$buyerIdCondition = " AND d.customer_buyer = ".$buyerId."";
	}
	
	//yearCondition;
	$yearCondition = '';
	if ($year>0) 
	{
		if($db_type==0)
		{
			$yearCondition = " AND YEAR(d.insert_date) = ".$year."";
		}
		else if($db_type==2)
		{
			$yearCondition = " AND TO_CHAR(d.insert_date,'YYYY') = ".$year."";
		}
	}
	
	//jobNoCondition
	$jobNoCondition = '';

	if($jobNo != '')
	{
		if($jobId != '')
		{
			$jobNoCondition = " AND d.id =".$jobId."";
		}else{
			$jobNoCondition = " AND d.job_no like '%".$jobNo."%'";
		}
	}
	
	//bookingNoCondition
	$bookingNoCondition = '';
	if($bookingNo != '')
	{
		$str_arry = explode(",",$bookingNo);
	    $all_bookingNo="";
	    foreach ($str_arry as $key => $booking) 
	    {
	        if ($all_bookingNo=="") 
	        {
	            $all_bookingNo.= $booking;
	        }
	        else 
	        {
	            $all_bookingNo.= "','".$booking;
	        }
	    }
	    // echo $all_bookingNo;die;
		$bookingNoCondition = " AND d.sales_booking_no in('$all_bookingNo')";
	}
	// echo $bookingNoCondition;die;
	//searchByCondition
	$searchByCondition = '';
	if($searchCommon != '')
	{
		$expSearchCommon = explode(',', $searchCommon);
		$dataSearchCommon = '';
		foreach($expSearchCommon as $val)
		{
			if($dataSearchCommon != '')
			{
				$dataSearchCommon .= ',';
			}
			$dataSearchCommon .= "'".$val."'";
		}
		
		//style no
		if($searchBy == 1)
		{
			$searchByCondition = " AND d.style_ref_no IN(".$dataSearchCommon.")";
		}
		//order no
		else if($searchBy == 2)
		{
			$searchByCondition = " AND c.po_number IN(".$dataSearchCommon.")";
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
				$productIdCondition .= " e.prod_id IN(".$pids.") OR ";
			}
			$productIdCondition = chop($productIdCondition,'OR ');
			$productIdCondition .= ")";
		}
		else
		{
			$productIdCondition = " AND e.prod_id IN(".$productId.")";
		}
	}

	//storeCondition
	$storeCondition = '';
	if($storeId != '')
	{
		$storeCondition = " AND f.store_id IN(".$storeId.")";
	}

	//floorCondition
	$floorCondition = '';
	if($floorId != '')
	{
		$floorCondition = " AND f.floor_id IN(".$floorId.")";
	}

	//roomCondition
	$roomCondition = '';
	if($roomId != '')
	{
		$roomCondition = " AND f.room IN(".$roomId.")";
	}

	//rackCondition
	$rackCondition = '';
	if($rackId != '')
	{
		$rackCondition = " AND f.rack IN(".$rackId.")";
	}
	
	//dateCondition
	$dateCondition = '';
	$dateCondition2 = '';
	if($toDate != '')
	{
		if($db_type == 0)
		{
			$endDate = change_date_format($toDate,"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$endDate = change_date_format($toDate,"","",1);
		}

		$dateCondition = " AND f.transaction_date <= '".$endDate."'";
		$dateCondition2 = " AND a.transfer_date <= '".$endDate."'";
	}
	

	$con = connect();
	execute_query("DELETE FROM TMP_PO_ID WHERE USER_ID = ".$user_id."");
	execute_query("DELETE from tmp_barcode_no where userid=$user_id");
	oci_commit($con);

	/*
	|--------------------------------------------------------------------------
	| for roll recv qty
	|--------------------------------------------------------------------------
	|
	*/
	$sqlRcvRollQty = "
		SELECT
			d.job_no, d.sales_booking_no, d.customer_buyer, d.buyer_id, d.style_ref_no, d.company_id, e.prod_id, e.po_breakdown_id, e.entry_form, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, h.qnty AS rcv_qty, h.id AS no_of_roll_rcv, h.barcode_no
        FROM
			fabric_sales_order_mst d
			INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id
            INNER JOIN inv_transaction f ON e.trans_id = f.id
			INNER JOIN pro_grey_prod_entry_dtls g ON f.id = g.trans_id
			INNER JOIN pro_roll_details h ON g.id = h.dtls_id
        WHERE
			e.status_active = 1
			AND e.is_deleted = 0
			AND e.entry_form IN(2,22,58,84)
			AND e.trans_type IN(1,4)
			AND e.trans_id > 0
			AND f.status_active = 1
			AND f.is_deleted = 0
			AND d.company_id IN(".$companyId.")
			".$buyerIdCondition."
			".$yearCondition."
			".$jobNoCondition."
			".$bookingNoCondition."
			".$searchByCondition."
			".$productIdCondition."
			".$storeCondition."
			".$floorCondition."
			".$roomCondition."
			".$rackCondition."
			".$dateCondition."
			AND h.entry_form IN(2,22,58,84) AND h.status_active = 1 and h.is_sales=1
	";	
	// echo $sqlRcvRollQty; //die;
	$sqlRcvRollRslt = sql_select($sqlRcvRollQty);
	$barcode_no_arr = array();
	foreach($sqlRcvRollRslt as $row)
	{
        $barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}
	// unset($sqlRcvRollRslt);
	// echo "<pre>"; print_r($dataArr); die;

	/*
	|--------------------------------------------------------------------------
	| for issue qty and roll
	| order to order transfer
	|--------------------------------------------------------------------------
	|
	*/
	$sqlNoOfRoll="
		SELECT
			d.job_no, d.sales_booking_no, d.customer_buyer, d.buyer_id, d.style_ref_no, d.company_id, e.prod_id, e.po_breakdown_id, e.trans_type, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, e.quantity AS rcv_qty, h.qnty as roll_rcv_qty, g.id AS issue_roll, h.barcode_no, i.transfer_criteria
		FROM 
			fabric_sales_order_mst d
			INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id
			INNER JOIN inv_transaction f ON e.trans_id = f.id 
			INNER JOIN inv_item_transfer_dtls g ON e.dtls_id = g.id 
			INNER JOIN pro_roll_details h ON g.id = h.dtls_id  and g.mst_id=h.mst_id and e.dtls_id=h.dtls_id
			INNER JOIN INV_ITEM_TRANSFER_MST i on i.id=h.mst_id and i.id=G.MST_ID and i.id= F.MST_ID
		WHERE
			e.status_active = 1 
			AND e.is_deleted = 0 
			AND e.entry_form IN(133) AND h.entry_form IN(133) and i.entry_form=133
			AND e.trans_type IN(5,6) 
			AND f.status_active = 1 
			AND f.is_deleted = 0 
			AND g.status_active = 1 
			AND g.is_deleted = 0
			AND h.status_active = 1 
			AND h.is_deleted = 0
			and h.is_sales=1
			AND d.company_id IN(".$companyId.")	
			".$buyerIdCondition."
			".$yearCondition."
			".$jobNoCondition."
			".$bookingNoCondition."
			".$searchByCondition."
			".$productIdCondition."
			".$storeCondition."
			".$floorCondition."
			".$roomCondition."
			".$rackCondition."
			".$dateCondition."
	";
	//echo "<br>".$sqlNoOfRoll; //die;
	$sqlNoOfRollResult = sql_select($sqlNoOfRoll);
	foreach($sqlNoOfRollResult as $row)
	{
        $barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}
	// unset($sqlNoOfRollResult);
	//echo $rollRcvQty."=".$transferInQty."=".$rollIssueQty."=".$transferOutQty;
	// echo "<pre>"; print_r($barcode_no_arr);
	
	/*
	|--------------------------------------------------------------------------
	| for production
	|--------------------------------------------------------------------------
	|
	*/
	if(!empty($barcode_no_arr))
	{
		foreach($barcode_no_arr as $barcode_no)
		{
			if( $barcode_no_check[$barcode_no] =="" )
	        {
	            $barcode_no_check[$barcode_no]=$barcode_no;
	            $barcodeno = $barcode_no;
	            // echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_id,$barcodeno)";
	            execute_query("insert into tmp_barcode_no (userid, barcode_no,entry_form) values ($user_id,$barcodeno,12423)");
	        }			
		}
		oci_commit($con);

		$production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no,c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, c.knitting_source, c.challan_no as production_challan, c.knitting_company, a.yarn_prod_id, a.body_part_id, b.coller_cuff_size
		from pro_grey_prod_entry_dtls a, pro_roll_details b, inv_receive_master c, tmp_barcode_no d 
		where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2,22,58) and b.entry_form in(2,22,58)  and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid= $user_id and d.entry_form=12423 $color_id_cond ");
        $yarn_prod_id_check=array();$prog_no_check=array();
        foreach ($production_sql as $row)
        {
            $prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"] =$row[csf("receive_basis")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prog_book"] =$row[csf("booking_no")];
            $prodBarcodeData[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
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
            $prodBarcodeData[$row[csf("barcode_no")]]["item_size"] =$row[csf("coller_cuff_size")];
            $prodBarcodeData[$row[csf("barcode_no")]]["machine_no_id"] =$row[csf("machine_no_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prod_challan"] =$row[csf("production_challan")];
            $prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"] =$row[csf("knitting_source")];
            $prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"] =$row[csf("knitting_company")];
            $prodBarcodeData[$row[csf("barcode_no")]]["body_part_id"] =$row[csf("body_part_id")];
            $allDeterArr[$row[csf("febric_description_id")]] =$row[csf("febric_description_id")];
            $allColorArr[$row[csf("color_id")]] =$row[csf("color_id")];
            $allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
        }
	}

	$dataArr = array();
	$poArr = array();
	foreach($sqlRcvRollRslt as $row)
	{
		$compId = $row[csf('company_id')];
		$productId = $row[csf('prod_id')];
		$orderId = $row[csf('po_breakdown_id')];
		$storeId = $row[csf('store_id')]*1;
		$floorId = $row[csf('floor_id')]*1;
		$roomId = $row[csf('room')]*1;
		$rackId = $row[csf('rack')]*1;
		$selfId = $row[csf('self')]*1;
		$binId = $row[csf('bin_box')]*1;
		$poArr[$orderId] = $orderId;
		$febric_description_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];
		$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];
		if ($color_id=="") 
		{
			$color_id=0;
		}

		$bookingInfoArr[$orderId]['fso_no'] = $row[csf('job_no')];
		$bookingInfoArr[$orderId]['customer_buyer'] = $row[csf('customer_buyer')];
		$bookingInfoArr[$orderId]['buyer_id'] = $row[csf('buyer_id')];
		$bookingInfoArr[$orderId]['booking_no'] = $row[csf('sales_booking_no')];
		$bookingInfoArr[$orderId]['style_ref_no'] = $row[csf('style_ref_no')];
		
		// $str_ref=$productId."*".$storeId."*".$floorId."*".$roomId."*".$rackId."*".$selfId."*".$binId
		$str_ref=$productId;
		$dataArr[$compId][$orderId][$febric_description_id]['rollRcvQty'] += count($row[csf('no_of_roll_rcv')]);
		$dataArr[$compId][$orderId][$febric_description_id]['color_id'] .= $color_id.',';
		$dataArr[$compId][$orderId][$febric_description_id]['prod_id'] .= $productId.',';
		if($row[csf('entry_form')]  == 84)
		{
			$dataArr[$compId][$orderId][$febric_description_id]['issueReturnQty'] += $row[csf('rcv_qty')];
		}
		else
		{
			$dataArr[$compId][$orderId][$febric_description_id]['rcvQty'] += $row[csf('rcv_qty')];
		}
	}
	unset($sqlRcvRollRslt);
	// echo "<pre>";print_r($dataArr);die;

	foreach($sqlNoOfRollResult as $row)
	{
		$compId = $row[csf('company_id')];
		$productId = $row[csf('prod_id')];
		$orderId = $row[csf('po_breakdown_id')];
		$storeId = $row[csf('store_id')]*1;
		$floorId = $row[csf('floor_id')]*1;
		$roomId = $row[csf('room')]*1;
		$rackId = $row[csf('rack')]*1;
		$selfId = $row[csf('self')]*1;
		$binId = $row[csf('bin_box')]*1;
		$febric_description_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];
		$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];
		if ($color_id=="") 
		{
			$color_id=0;
		}
		$str_ref=$productId;
		if($row[csf('trans_type')] == 5)
		{
			$poArr[$orderId] = $orderId;
			$dataArr[$compId][$orderId][$febric_description_id]['prod_id'] .= $productId.',';
			$dataArr[$compId][$orderId][$febric_description_id]['color_id'] .= $color_id.',';
			$dataArr[$compId][$orderId][$febric_description_id]['rollRcvQty'] += count($row[csf('issue_roll')]);

			if ($row[csf('transfer_criteria')] !=2 ) // without store to store transfer in qty
			{
				if($row[csf('roll_rcv_qty')])
				{
					$dataArr[$compId][$orderId][$febric_description_id]['transferInQty'] += $row[csf('roll_rcv_qty')];
				}
				else{
					$dataArr[$compId][$orderId][$febric_description_id]['transferInQty'] += $row[csf('rcv_qty')];
				}
			}
			
		}
		if($row[csf('trans_type')] == 6)
		{
			$transOutArr[$compId][$orderId][$febric_description_id]['rollIssueQty'] += count($row[csf('issue_roll')]);

			if ($row[csf('transfer_criteria')] !=2 ) // without store to store transfer out qty
			{
				if($row[csf('roll_rcv_qty')])
				{
					$transOutArr[$compId][$orderId][$febric_description_id]['transferOutQty'] += $row[csf('roll_rcv_qty')];
				}
				else{
					$transOutArr[$compId][$orderId][$febric_description_id]['transferOutQty'] += $row[csf('rcv_qty')];
				}
			}
		}
		
		$bookingInfoArr[$orderId]['fso_no'] = $row[csf('job_no')];
		$bookingInfoArr[$orderId]['customer_buyer'] = $row[csf('customer_buyer')];
		$bookingInfoArr[$orderId]['buyer_id'] = $row[csf('buyer_id')];
		$bookingInfoArr[$orderId]['booking_no'] = $row[csf('sales_booking_no')];
		$bookingInfoArr[$orderId]['style_ref_no'] = $row[csf('style_ref_no')];
	}
	unset($sqlNoOfRollResult);
	// echo "<pre>";print_r($dataArr);


	/*
	|--------------------------------------------------------------------------
	| for issue qty and roll
	|--------------------------------------------------------------------------
	|
	*/
	// echo "<pre>";print_r($poArr);die;
	if(!empty($poArr))
	{
		$con = connect();
		foreach($poArr as $poId)
		{
			// echo "insert into TMP_PO_ID (PO_ID, USER_ID) values ($poId,$user_id)";
			execute_query("INSERT INTO TMP_PO_ID(PO_ID,USER_ID) VALUES(".$poId.", ".$user_id.")");
		}
		oci_commit($con);
		//disconnect($con);

		//===== For Roll Splitting After Issue start ============
	    $split_chk_sql = sql_select("SELECT D.BARCODE_NO, D.QNTY 
	    from pro_roll_split C, pro_roll_details D, TMP_PO_ID E 
	    where c.entry_form = 75 and C.split_from_id = D.roll_split_from and C.status_active = 1 and D.status_active = 1 and d.po_breakdown_id=e.po_id and e.user_id=$user_id");

	    if(!empty($split_chk_sql))
	    {
	        foreach ($split_chk_sql as $val)
	        {
	            $split_barcode_arr[$val['BARCODE_NO']]= $val['BARCODE_NO'];
	            if ($split_barcode_check[$val['BARCODE_NO']]=="") 
	            {
	                $split_barcode_check[$val['BARCODE_NO']]=$val['BARCODE_NO'];
	                $split_barcode=$val['BARCODE_NO'];
	                execute_query("INSERT INTO tmp_barcode_no(barcode_no,userid,entry_form) VALUES(".$split_barcode.", ".$user_id.",12424)");
	            }
	        }
	        oci_commit($con);

	        $split_ref_sql = sql_select("SELECT A.BARCODE_NO, A.QNTY, A.ROLL_ID, B.BARCODE_NO AS MOTHER_BARCODE 
	            from tmp_barcode_no t, pro_roll_details A, pro_roll_details B 
	            where t.barcode_no=a.barcode_no and t.userid=$user_id and t.entry_form=12424 and A.entry_form = 61 and A.roll_id = B.id and A.status_active =1 and B.status_active=1");
	        if(!empty($split_ref_sql))
	        {
	            foreach ($split_ref_sql as $value)
	            {
	                $mother_barcode_arr[$value['BARCODE_NO']] = $value['MOTHER_BARCODE'];
	            }
	        }
	    }
	    unset($split_chk_sql);
	    unset($split_ref_sql);
	    // ======== For Roll Splitting After Issue end =========
		
		$sqlNoOfRollIssue="
			SELECT
				d.company_id,
				e.prod_id, e.po_breakdown_id, SUM(g.qnty) AS issue_qty,
				f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box,
				COUNT(g.id) AS issue_roll, g.barcode_no
			FROM
				fabric_sales_order_mst d
				INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id
				INNER JOIN inv_transaction f ON e.trans_id = f.id
				INNER JOIN pro_roll_details g ON e.dtls_id = g.dtls_id
			WHERE
				e.status_active = 1
				AND e.is_deleted = 0
				AND e.entry_form IN(61)
				AND e.trans_type = 2
				AND f.status_active = 1
				AND f.is_deleted = 0
				AND g.status_active = 1
				AND g.is_deleted = 0
				AND g.entry_form IN(61) and g.is_sales=1
				AND d.company_id IN(".$companyId.")
				".$dateCondition."
				AND e.po_breakdown_id in(SELECT PO_ID FROM TMP_PO_ID WHERE USER_ID = ".$user_id.")
			GROUP BY 
				d.company_id,
				e.prod_id, e.po_breakdown_id,
				f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, g.barcode_no
		";
		//disconnect($con); die;
		//echo $sqlNoOfRollIssue; //die;
		$sqlNoOfRollIssueResult = sql_select($sqlNoOfRollIssue);
		$noOfRollIssueArr = array();
		foreach($sqlNoOfRollIssueResult as $row)
		{
			$compId = $row[csf('company_id')];
			$productId = $row[csf('prod_id')];
			$orderId = $row[csf('po_breakdown_id')];
			$storeId = $row[csf('store_id')]*1;
			$floorId = $row[csf('floor_id')]*1;
			$roomId = $row[csf('room')]*1;
			$rackId = $row[csf('rack')]*1;
			$selfId = $row[csf('self')]*1;
			$binId = $row[csf('bin_box')]*1;
			
			$deter_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];
			//$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];

			$noOfRollIssueArr[$compId][$orderId][$deter_id]['rollIssueQty'] += $row[csf('issue_roll')];//without split barcode count
			
	        $mother_barcode_no = $mother_barcode_arr[$row[csf("barcode_no")]];
	        if($mother_barcode_no != "")
	        {
	            $deter_id=$prodBarcodeData[$mother_barcode_no]["febric_description_id"];
	            //$color_id=$prodBarcodeData[$mother_barcode_no]["color_id"];
	        }
	        $issueQtyArr[$compId][$orderId][$deter_id]['issueQty'] += $row[csf('issue_qty')];
		}
		unset($sqlNoOfRollIssueResult);
	}
	//echo $issueQty."=".$rollIssueQty;
	// echo "<pre>"; print_r($issueQtyArr);
	
	if(empty($dataArr))
	{
		echo get_empty_data_msg();
		die;
	}
	
	$company_arr = return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_array = return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$color_arr = return_library_array( 'SELECT id, color_name FROM lib_color', 'id', 'color_name');
		
	//composition and constructtion
	$composition_arr=array();
	$constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row) 
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($deter_array);

    /*
	|--------------------------------------------------------------------------
	| FSO req_qty
	|--------------------------------------------------------------------------
	|
	*/
	$sql_fso_book_qty = sql_select("SELECT a.booking_id, a.sales_booking_no, a.within_group, a.job_no, b.mst_id, b.color_id, b.determination_id, b.grey_qty 
	from fabric_sales_order_mst a, fabric_sales_order_dtls b, tmp_po_id c 
	where a.id=b.mst_id and b.mst_id=c.po_id and c.user_id=$user_id and b.status_active=1 and b.is_deleted=0");

	foreach ($sql_fso_book_qty as $val) 
	{
		$fabric_construction = $val[csf('determination_id')];
		$fso_booking_req[$val[csf("mst_id")]][$fabric_construction]["req_qty"] +=$val[csf("grey_qty")];

		$sales_ord_ref[$val[csf("mst_id")]]['within_group'] = $val[csf("within_group")];
		// echo $val[csf("mst_id")].'*'.$fabric_construction.'*'.$val[csf("color_id")].'<br>';
	}

	$width = 1990;
	?>
	<style type="text/css">
		.word_break {
			word-break: break-all;
			word-wrap: break-word;
		}
	</style>
	<fieldset style="width:<?php echo $width+20; ?>px;margin:5px auto;">
		<table width="<?php echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<thead>
                <tr class="form_caption" style="border:none;">
                	<th align="center" colspan="23" style="font-size:16px"><strong>Rack Wise Grey Fabrics Stock Report</strong></th>
                </tr>
                <tr>
					<th width="30">SL</th>
					<th width="60" class="word_break">Company</th>

					<th width="120" class="word_break">Sales Order No</th>
					<th width="100" class="word_break">Sales Job/ Booking No.</th>
					<th width="100" class="word_break">Customer Name</th>
					<th width="100" class="word_break">Cust. Buyer Name</th>

					<th width="110" class="word_break">Fab. Constraction</th>
					<th width="100" class="word_break">Fabric Color</th>

					<th width="100" class="word_break">Required Qty</th>
					<th width="80" class="word_break">Receive</th>
					<th width="80" class="word_break">Issue Return</th>
					<th width="80" class="word_break">Transfer In</th>
					<th width="80" class="word_break">Total<br>Recv.</th>
					<th width="80" class="word_break">No of Roll<br>Recv.</th>
					<th width="80" class="word_break">Receive<br>Balance</th>
					<th width="80" class="word_break">Issue</th>
					<th width="80" class="word_break">Receive<br>Return</th>
					<th width="80" class="word_break">Transfer Out</th>
					<th width="80" class="word_break">Total Issue</th>
					<th width="80" class="word_break">No of Roll<br>Issued</th>
					<th width="80" class="word_break">Issue<br>Balance</th>
					<th width="100" class="word_break">Stock Qty.</th>
					<th width="" class="word_break">No Of Roll Bal.</th>
				</tr>
			</thead>
		</table>
		<div style="width:<?php echo $width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table width="<?php echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
				<tbody>
				<?php
				$sl = 0;
				$grandTotal = array();
				// echo "<pre>";print_r($newDataArr);
				foreach($dataArr as $compId=>$compArr)
				{
					foreach($compArr as $orderId=>$orderArr)
					{
						foreach($orderArr as $fabric_construction=>$row)
						{
							//foreach($fabric_constructionArr as $fab_color_id=>$row)
							//{//foreach($color_id_val as $str_ref=>$row)
								
								//$str_ref_arr = explode("*", $str_ref);
                                //$productIds=$str_ref_arr[0];
                                $productIds =implode(",",array_unique(explode(",",chop($row["prod_id"],","))));
								//total receive calculation
								$row['totalRcvQty'] = number_format($row['rcvQty'],2,'.','')+number_format($row['issueReturnQty'],2,'.','')+number_format($row['transferInQty'],2,'.','');
								
								//total issue calculation
								$row['issueQty'] = $issueQtyArr[$compId][$orderId][$fabric_construction]['issueQty'];
								$row['rcvReturnQty'] = 0;
								$row['transferOutQty'] = $transOutArr[$compId][$orderId][$fabric_construction]['transferOutQty'];
								$row['totalIssueQty'] = number_format($row['issueQty'],2,'.','')+number_format($row['rcvReturnQty'],2,'.','')+number_format($row['transferOutQty'],2,'.','');
								
								//stock qty calculation
								$row['stockQty'] = $row['totalRcvQty'] - $row['totalIssueQty'];

								//roll balance calculation
								$row['rollIssueQty'] = $transOutArr[$compId][$orderId][$fabric_construction]['rollIssueQty']+$noOfRollIssueArr[$compId][$orderId][$fabric_construction]['rollIssueQty'];
								$row['rollBalanceQty'] = $row['rollRcvQty'] - $row['rollIssueQty'];

								$color_id_arr = array_filter(array_unique(explode(",", $row["color_id"])));
                                $colors="";
                                foreach ($color_id_arr as $color) 
                                {
                                    $colors .= $color_arr[$color] . ",";
                                }
                                $colors = rtrim($colors, ", ");

								if($valueType != 1 && $row['stockQty'] == 0)
								{
									continue;
								}
								
								if($row['stockQty'] >= 0)
								{
									$sl++;
									$row['fso_no'] = $bookingInfoArr[$orderId]['fso_no'];
									$row['buyer_id'] = $bookingInfoArr[$orderId]['buyer_id'];
									$row['customer_buyer'] = $bookingInfoArr[$orderId]['customer_buyer'];
									$row['style_ref_no'] = $bookingInfoArr[$orderId]['style_ref_no'];
									$row['booking_no'] = $bookingInfoArr[$orderId]['booking_no'];
									
									//$productIds = implode("*",$productIdsArr[$compId][$orderId][$fabric_construction][$fab_color_id]); 
									// echo $orderId.'='.$fabric_construction.'='.$fab_color_id.'<br>';
									$required_qnty = $fso_booking_req[$orderId][$fabric_construction]["req_qty"];

									$within_group = $sales_ord_ref[$val[csf("mst_id")]]['within_group'];
									if($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr valign="middle" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $sl; ?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')">
                                        <td width="30" align="center"><?php echo $sl; ?></td>
                                        <td width="60" class="word_break"><p><div style="word-break:break-all"><?php echo $company_arr[$compId]; ?></div></p></td>

                                        <td width="120" class="word_break"><div style="word-break:break-all">
											<a href="##" onclick="fabric_sales_order_print6('<? echo $compId;?>','<? //echo $compId;?>','<? echo $row['booking_no'];?>','<? echo $row['fso_no'];?>', '<? echo $within_group;?>');"><?php echo $row['fso_no']; ?></a></div>
										</td>
                                        <td width="100" class="word_break"><div><?php echo $row['booking_no']; ?></div></td>
                                        <td width="100" class="word_break">
                                        	<div><?php echo $buyer_array[$row['buyer_id']]; ?></div>
                                        </td>
                                        <td width="100" class="word_break">
                                        	<div><?php echo $buyer_array[$row['customer_buyer']]; ?></div>
                                        </td>
                                        <td width="110" class="word_break"><div><?php echo $constructtion_arr[$fabric_construction]; ?></div></td>
                                        <td width="100" class="word_break" title="<?=$row["color_id"];?>"><div><?php echo $colors; ?></div></td>
                                        <td width="100" class="word_break" align="right"><?php echo number_format($required_qnty,2); ?></td>

                                        <td width="80" class="word_break" align="right"><?php echo number_format($row['rcvQty'],2); ?></td>
                                        <td width="80" class="word_break" align="right"><?php echo number_format($row['issueReturnQty'],2); ?></td>
                                        <td width="80" class="word_break" align="right"><?php echo number_format($row['transferInQty'],2); ?></td>
                                        <td width="80" class="word_break" align="right"><?php echo number_format($row['totalRcvQty'],2); ?></td>
                                        <td width="80" class="word_break" align="right">
                                        	<a href='#report_details' onClick="openmypage('<? echo $orderId; ?>','<? echo $productIds; ?>','','grey_recv_issue_popup',1);"><? echo number_format($row['rollRcvQty'],2); ?></a>
                                        </td>

                                        <td width="80" class="word_break" align="right"><?php echo number_format($required_qnty-$row['totalRcvQty'],2); ?></td>

                                        <td width="80" class="word_break" align="right"><?php echo number_format($row['issueQty'],2); ?></td>
                                        <td width="80" class="word_break" align="right"><?php echo number_format($row['rcvReturnQty'],2); ?></td>
                                        <td width="80" class="word_break" align="right"><?php echo number_format($row['transferOutQty'],2); ?></td>
                                        <td width="80" class="word_break" align="right"><?php echo number_format($row['totalIssueQty'],2); ?></td>
                                        <td width="80" class="word_break" align="right">
                                        	<a href='#report_details' onClick="openmypage('<? echo $orderId; ?>','<? echo $productIds; ?>','','grey_recv_issue_popup',2);"><? echo number_format($row['rollIssueQty'],2); ?></a>
                                        </td>
                                        
                                        <td width="80" class="word_break" align="right"><?php echo number_format($required_qnty-$row['totalIssueQty'],2); ?></td>

                                        <td width="100" class="word_break" align="right"><?php echo number_format($row['stockQty'],2); ?></td>
                                        <td width="" class="word_break" align="right"><a href="##" onclick="openmypage_rollbal('<? echo $orderId;?>','<? echo $productIds;?>','');"><?php echo $row['rollBalanceQty']; ?></a></td>
                                    </tr>
									<?php
									//$grandTotal
									$grandTotal['requ_qty'] += number_format($required_qnty,2,'.','');
									$grandTotal['rcvQty'] += number_format($row['rcvQty'],2,'.','');
									$grandTotal['issueReturnQty'] += number_format($row['issueReturnQty'],2,'.','');
									$grandTotal['transferInQty'] += number_format($row['transferInQty'],2,'.','');
									$grandTotal['totalRcvQty'] += number_format($row['totalRcvQty'],2,'.','');
									$grandTotal['totalRollRcvQty'] += number_format($row['rollRcvQty'],2,'.','');
									$grandTotal['totalRcvBalanceQty'] += number_format($required_qnty-$row['totalRcvQty'],2,'.','');
							
									$grandTotal['issueQty'] += number_format($row['issueQty'],2,'.','');
									$grandTotal['rcvReturnQty'] += number_format($row['rcvReturnQty'],2,'.','');
									$grandTotal['transferOutQty'] += number_format($row['transferOutQty'],2,'.','');
									$grandTotal['totalIssueQty'] += number_format($row['totalIssueQty'],2,'.','');
									$grandTotal['totalRollIssueQty'] += number_format($row['rollIssueQty'],2,'.','');
									$grandTotal['totalIssueBalanceQty'] += number_format($required_qnty-$row['totalIssueQty'],2,'.','');
									$grandTotal['totalStockQty'] += number_format($row['stockQty'],2,'.','');
									$grandTotal['totalRollBalanceQty'] += $row['rollBalanceQty'];
								}			
							//}
						}
					}
				}
                ?>
                </tbody>
            </table>
        </div>
        <table width="<?php echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_foot">
            <tfoot>
                <tr>
                    <th width="30">&nbsp;</th>
                    <th width="60">&nbsp;</th>

                    <th width="120">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>

                    <th width="110">&nbsp;</th>
					<th width="100">Total</th>
                    <th width="100" align="right" id="value_requQty"><?php echo number_format($grandTotal['requ_qty'],2); ?></th>

					<th width="80" align="right" id="value_RcvQty"><?php echo number_format($grandTotal['rcvQty'],2); ?></th>
					<th width="80" align="right" id="value_issueRtnQty"><?php echo number_format($grandTotal['issueReturnQty'],2); ?></th>
					<th width="80" align="right" id="value_transInQty"><?php echo number_format($grandTotal['transferInQty'],2); ?></th>
					<th width="80" align="right" id="value_totalRcvQty"><?php echo number_format($grandTotal['totalRcvQty'],2); ?></th>
                    <th width="80" align="right" id="value_totalRollRcvQty"><?php echo number_format($grandTotal['totalRollRcvQty'],2); ?></th>
                    <th width="80" align="right" id="value_RcvBalanceQty"><?php echo number_format($grandTotal['totalRcvBalanceQty'],2); ?></th>

                    <th width="80" align="right" id="value_issueQty"><?php echo number_format($grandTotal['issueQty'],2); ?></th>
                    <th width="80" align="right" id="value_recvRtnQty"><?php echo number_format($grandTotal['rcvReturnQty'],2); ?></th>
                    <th width="80" align="right" id="value_transOutQty"><?php echo number_format($grandTotal['transferOutQty'],2); ?></th>
                    <th width="80" align="right" id="value_totalIssueQty"><?php echo number_format($grandTotal['totalIssueQty'],2); ?></th>
                    <th width="80" align="right" id="value_totalRollIssueQty"><?php echo number_format($grandTotal['totalRollIssueQty'],2); ?></th>
                    <th width="80" align="right" id="value_issueBalanceQty"><?php echo number_format($grandTotal['totalIssueBalanceQty'],2); ?></th>

                    <th width="100" align="right" id="value_totalStockQty"><?php echo number_format($grandTotal['totalStockQty'],2); ?></th>
                    <th width="" align="right" id="value_totalRollBalanceQty"><?php echo $grandTotal['totalRollBalanceQty']; ?></th>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    <?php
    
	execute_query("DELETE FROM TMP_PO_ID WHERE USER_ID = ".$user_id."");
	execute_query("DELETE from tmp_barcode_no where userid=$user_id");
	oci_commit($con);

	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	
	echo $html."####".$filename."####".$reportType;
	disconnect($con);
	die;
}

/*
|--------------------------------------------------------------------------
| generate_stock_management_report
|--------------------------------------------------------------------------
|
*/
if($action=="generate_stock_management_report")
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	ob_start();
	$companyId = str_replace("'", "", $cbo_company_id);
	$buyerId = str_replace("'", "", $cbo_buyer_id);
	$year = str_replace("'", "", $cbo_year);
	$jobNo = trim(str_replace("'", "", $txt_job_no));
	$jobId = str_replace("'", "", $txt_job_id);
	$bookingNo = str_replace("'", "", $txt_booking_no);
	$bookingId = str_replace("'", "", $txt_booking_id);
	$searchBy = str_replace("'", "", $cbo_search_by);
	$searchCommon = str_replace("'", "", $txt_search_comm);
	$product = str_replace("'", "", $txt_product);
	$productId = str_replace("'", "", $txt_product_id);
	$productNo = str_replace("'", "", $txt_product_no);
	$storeId = str_replace("'", "", $txt_store_id);
	$floorId = str_replace("'", "", $txt_floor_id);
	$roomId = str_replace("'", "", $txt_room_id);
	$rackId = str_replace("'", "", $txt_rack_id);
	$valueType = str_replace("'", "", $cbo_value_type);
	$fromDate = str_replace("'", "", $txt_date_from);
	$toDate = str_replace("'", "", $txt_date_to);
	$reportType = str_replace("'", "", $rpt_type);
	//$_SESSION["date_from"] = date('Y-m-d', strtotime($date_from));
	//$_SESSION["date_to"] = date('Y-m-d', strtotime($date_to));
	$int_ref 		= trim(str_replace("'","",$txt_int_ref));

	if ($int_ref!="")
	{
		$po_sql="SELECT a.id, a.po_number, a.grouping, b.booking_no, b.booking_mst_id from wo_po_break_down a, wo_booking_dtls b, fabric_sales_order_mst c 
		where a.id=b.po_break_down_id and b.BOOKING_MST_ID=c.BOOKING_ID and c.within_group=1 and a.grouping='$int_ref' and b.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		// echo $po_sql;die;
		$po_sql_result=sql_select($po_sql);
		$refBooking_cond="";
		foreach ($po_sql_result as $key => $row) 
		{
			//$po_id_arr[$row[csf('id')]]=$row[csf('id')];
			$bookingNo_arr[$row[csf('booking_mst_id')]] = $row[csf('booking_mst_id')];
		}
		//$poIds=implode(",",array_unique(explode(",",$po_id_arr)));
		$refBooking_cond=" and d.booking_id in('".implode("','",$bookingNo_arr)."') ";
	}
	
	//buyerIdCondition
	$buyerIdCondition = '';
	if($buyerId != 0)
	{
		$buyerIdCondition = " AND d.customer_buyer = ".$buyerId."";
	}
	
	//yearCondition;
	$yearCondition = '';
	if ($year>0) 
	{
		if($db_type==0)
		{
			$yearCondition = " AND YEAR(d.insert_date) = ".$year."";
		}
		else if($db_type==2)
		{
			$yearCondition = " AND TO_CHAR(d.insert_date,'YYYY') = ".$year."";
		}
	}
	
	//jobNoCondition
	$jobNoCondition = '';

	if($jobNo != '')
	{
		if($jobId != '')
		{
			$jobNoCondition = " AND d.id =".$jobId."";
		}else{
			$jobNoCondition = " AND d.job_no like '%".$jobNo."%'";
		}
	}
	
	//bookingNoCondition
	$bookingNoCondition = '';
	if($bookingNo != '')
	{
		$str_arry = explode(",",$bookingNo);
	    $all_bookingNo="";
	    foreach ($str_arry as $key => $booking) 
	    {
	        if ($all_bookingNo=="") 
	        {
	            $all_bookingNo.= $booking;
	        }
	        else 
	        {
	            $all_bookingNo.= "','".$booking;
	        }
	    }
	    // echo $all_bookingNo;die;
		$bookingNoCondition = " AND d.sales_booking_no in('$all_bookingNo')";
	}
	// echo $bookingNoCondition;die;
	//searchByCondition
	$searchByCondition = '';
	if($searchCommon != '')
	{
		$expSearchCommon = explode(',', $searchCommon);
		$dataSearchCommon = '';
		foreach($expSearchCommon as $val)
		{
			if($dataSearchCommon != '')
			{
				$dataSearchCommon .= ',';
			}
			$dataSearchCommon .= "'".$val."'";
		}
		
		//style no
		if($searchBy == 1)
		{
			$searchByCondition = " AND d.style_ref_no IN(".$dataSearchCommon.")";
		}
		//order no
		else if($searchBy == 2)
		{
			$searchByCondition = " AND c.po_number IN(".$dataSearchCommon.")";
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
				$productIdCondition .= " e.prod_id IN(".$pids.") OR ";
			}
			$productIdCondition = chop($productIdCondition,'OR ');
			$productIdCondition .= ")";
		}
		else
		{
			$productIdCondition = " AND e.prod_id IN(".$productId.")";
		}
	}

	//storeCondition
	$storeCondition = '';
	if($storeId != '')
	{
		$storeCondition = " AND f.store_id IN(".$storeId.")";
	}

	//floorCondition
	$floorCondition = '';
	if($floorId != '')
	{
		$floorCondition = " AND f.floor_id IN(".$floorId.")";
	}

	//roomCondition
	$roomCondition = '';
	if($roomId != '')
	{
		$roomCondition = " AND f.room IN(".$roomId.")";
	}

	//rackCondition
	$rackCondition = '';
	if($rackId != '')
	{
		$rackCondition = " AND f.rack IN(".$rackId.")";
	}
	
	//dateCondition
	$dateCondition = '';
	$dateCondition2 = '';
	if($toDate != '')
	{
		if($db_type == 0)
		{
			$endDate = change_date_format($toDate,"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$endDate = change_date_format($toDate,"","",1);
		}

		$dateCondition = " AND f.transaction_date <= '".$endDate."'";
		$dateCondition2 = " AND a.transfer_date <= '".$endDate."'";
	}
	

	$con = connect();
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=82");
	execute_query("DELETE from tmp_barcode_no where userid=$user_id");
	oci_commit($con);

	/*
	|--------------------------------------------------------------------------
	| for roll recv qty
	|--------------------------------------------------------------------------
	|
	*/
	$sqlRcvRollQty = "
		SELECT
			d.job_no, d.sales_booking_no, d.customer_buyer, d.buyer_id, d.style_ref_no, d.company_id, d.booking_id, e.prod_id, e.po_breakdown_id, e.entry_form, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, h.qnty AS rcv_qty, h.id AS no_of_roll_rcv, h.barcode_no
        FROM
			fabric_sales_order_mst d
			INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id
            INNER JOIN inv_transaction f ON e.trans_id = f.id
			INNER JOIN pro_grey_prod_entry_dtls g ON f.id = g.trans_id
			INNER JOIN pro_roll_details h ON g.id = h.dtls_id
        WHERE
			e.status_active = 1
			AND e.is_deleted = 0
			AND e.entry_form IN(2,22,58,84)
			AND e.trans_type IN(1,4)
			AND e.trans_id > 0
			AND f.status_active = 1
			AND f.is_deleted = 0
			AND d.company_id IN(".$companyId.")
			".$buyerIdCondition."
			".$yearCondition."
			".$jobNoCondition."
			".$bookingNoCondition."
			".$searchByCondition."
			".$productIdCondition."
			".$storeCondition."
			".$floorCondition."
			".$roomCondition."
			".$rackCondition."
			".$dateCondition."
			".$refBooking_cond."
			AND h.entry_form IN(2,22,58,84) AND h.status_active = 1 and h.is_sales=1
	";	
	// echo $sqlRcvRollQty; die;
	$sqlRcvRollRslt = sql_select($sqlRcvRollQty);
	$barcode_no_arr = array();$po_idArr = array();
	foreach($sqlRcvRollRslt as $row)
	{
        $barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}
	// unset($sqlRcvRollRslt);
	// echo "<pre>"; print_r($dataArr); die;

	/*
	|--------------------------------------------------------------------------
	| for issue qty and roll
	| order to order transfer
	|--------------------------------------------------------------------------
	|
	*/
	$sqlNoOfRoll="
		SELECT
			d.job_no, d.sales_booking_no, d.customer_buyer, d.buyer_id, d.style_ref_no, d.company_id, d.booking_id, e.prod_id, e.po_breakdown_id, e.trans_type, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, e.quantity AS rcv_qty, h.qnty as roll_rcv_qty, g.id AS issue_roll, h.barcode_no, i.transfer_criteria
		FROM 
			fabric_sales_order_mst d
			INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id
			INNER JOIN inv_transaction f ON e.trans_id = f.id 
			INNER JOIN inv_item_transfer_dtls g ON e.dtls_id = g.id 
			INNER JOIN pro_roll_details h ON g.id = h.dtls_id  and g.mst_id=h.mst_id and e.dtls_id=h.dtls_id
			INNER JOIN INV_ITEM_TRANSFER_MST i on i.id=h.mst_id and i.id=G.MST_ID and i.id= F.MST_ID
		WHERE
			e.status_active = 1 
			AND e.is_deleted = 0 
			AND e.entry_form IN(133) AND h.entry_form IN(133) and i.entry_form=133
			AND e.trans_type IN(5,6) 
			AND f.status_active = 1 
			AND f.is_deleted = 0 
			AND g.status_active = 1 
			AND g.is_deleted = 0
			AND h.status_active = 1 
			AND h.is_deleted = 0
			and h.is_sales=1
			AND d.company_id IN(".$companyId.")	
			".$buyerIdCondition."
			".$yearCondition."
			".$jobNoCondition."
			".$bookingNoCondition."
			".$searchByCondition."
			".$productIdCondition."
			".$storeCondition."
			".$floorCondition."
			".$roomCondition."
			".$rackCondition."
			".$dateCondition."
			".$refBooking_cond."
	";
	// echo "<br>".$sqlNoOfRoll; die;
	$sqlNoOfRollResult = sql_select($sqlNoOfRoll);
	foreach($sqlNoOfRollResult as $row)
	{
        $barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}
	// unset($sqlNoOfRollResult);
	//echo $rollRcvQty."=".$transferInQty."=".$rollIssueQty."=".$transferOutQty;
	// echo "<pre>"; print_r($barcode_no_arr);
	
	/*
	|--------------------------------------------------------------------------
	| for production
	|--------------------------------------------------------------------------
	|
	*/
	if(!empty($barcode_no_arr))
	{
		foreach($barcode_no_arr as $barcode_no)
		{
			if( $barcode_no_check[$barcode_no] =="" )
	        {
	            $barcode_no_check[$barcode_no]=$barcode_no;
	            $barcodeno = $barcode_no;
	            // echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_id,$barcodeno)";
	            execute_query("insert into tmp_barcode_no (userid, barcode_no,entry_form) values ($user_id,$barcodeno,12423)");
	        }			
		}
		oci_commit($con);

		$production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no,c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, c.knitting_source, c.challan_no as production_challan, c.knitting_company, a.yarn_prod_id, a.body_part_id, b.coller_cuff_size
		from pro_grey_prod_entry_dtls a, pro_roll_details b, inv_receive_master c, tmp_barcode_no d 
		where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2,22,58) and b.entry_form in(2,22,58)  and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid= $user_id and d.entry_form=12423 $color_id_cond ");
        $yarn_prod_id_check=array();$prog_no_check=array();
        foreach ($production_sql as $row)
        {
            $prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"] =$row[csf("receive_basis")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prog_book"] =$row[csf("booking_no")];
            $prodBarcodeData[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
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
            $prodBarcodeData[$row[csf("barcode_no")]]["item_size"] =$row[csf("coller_cuff_size")];
            $prodBarcodeData[$row[csf("barcode_no")]]["machine_no_id"] =$row[csf("machine_no_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prod_challan"] =$row[csf("production_challan")];
            $prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"] =$row[csf("knitting_source")];
            $prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"] =$row[csf("knitting_company")];
            $prodBarcodeData[$row[csf("barcode_no")]]["body_part_id"] =$row[csf("body_part_id")];
            $allDeterArr[$row[csf("febric_description_id")]] =$row[csf("febric_description_id")];
            $allColorArr[$row[csf("color_id")]] =$row[csf("color_id")];
            $allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
        }
	}

	$dataArr = array();
	$poArr = array();$booking_idArr = array();
	foreach($sqlRcvRollRslt as $row)
	{
		$compId = $row[csf('company_id')];
		$productId = $row[csf('prod_id')];
		$orderId = $row[csf('po_breakdown_id')];
		$poArr[$orderId] = $orderId;
		$booking_idArr[$row[csf('booking_id')]] = $row[csf('booking_id')];
		$febric_description_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];
		$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];
		if ($color_id=="") 
		{
			$color_id=0;
		}
		$gsm=$prodBarcodeData[$row[csf("barcode_no")]]["gsm"];

		$bookingInfoArr[$orderId]['fso_no'] = $row[csf('job_no')];
		$bookingInfoArr[$orderId]['customer_buyer'] = $row[csf('customer_buyer')];
		$bookingInfoArr[$orderId]['buyer_id'] = $row[csf('buyer_id')];
		$bookingInfoArr[$orderId]['booking_no'] = $row[csf('sales_booking_no')];
		$bookingInfoArr[$orderId]['style_ref_no'] = $row[csf('style_ref_no')];
		
		$dataArr[$orderId][$febric_description_id][$color_id][$gsm]['rollRcvQty'] += count($row[csf('no_of_roll_rcv')]);
		$dataArr[$orderId][$febric_description_id][$color_id][$gsm]['prod_id'] .= $productId.',';
		if($row[csf('entry_form')]  == 84)
		{
			$dataArr[$orderId][$febric_description_id][$color_id][$gsm]['issueReturnQty'] += $row[csf('rcv_qty')];
		}
		else
		{
			$dataArr[$orderId][$febric_description_id][$color_id][$gsm]['rcvQty'] += $row[csf('rcv_qty')];
		}
	}
	unset($sqlRcvRollRslt);
	// echo "<pre>";print_r($dataArr);die;

	foreach($sqlNoOfRollResult as $row)
	{
		$compId = $row[csf('company_id')];
		$productId = $row[csf('prod_id')];
		$orderId = $row[csf('po_breakdown_id')];
		$febric_description_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];
		$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];
		if ($color_id=="") 
		{
			$color_id=0;
		}
		$gsm=$prodBarcodeData[$row[csf("barcode_no")]]["gsm"];

		if($row[csf('trans_type')] == 5)
		{
			$poArr[$orderId] = $orderId;
			$booking_idArr[$row[csf('booking_id')]] = $row[csf('booking_id')];
			$dataArr[$orderId][$febric_description_id][$color_id][$gsm]['prod_id'] .= $productId.',';
			$dataArr[$orderId][$febric_description_id][$color_id][$gsm]['rollRcvQty'] += count($row[csf('issue_roll')]);

			if ($row[csf('transfer_criteria')] !=2 ) // without store to store transfer in qty
			{
				if($row[csf('roll_rcv_qty')])
				{
					$dataArr[$orderId][$febric_description_id][$color_id][$gsm]['transferInQty'] += $row[csf('roll_rcv_qty')];
				}
				else{
					$dataArr[$orderId][$febric_description_id][$color_id][$gsm]['transferInQty'] += $row[csf('rcv_qty')];
				}
			}
			
		}
		if($row[csf('trans_type')] == 6)
		{
			$transOutArr[$orderId][$febric_description_id][$color_id][$gsm]['rollIssueQty'] += count($row[csf('issue_roll')]);

			if ($row[csf('transfer_criteria')] !=2 ) // without store to store transfer out qty
			{
				if($row[csf('roll_rcv_qty')])
				{
					$transOutArr[$orderId][$febric_description_id][$color_id][$gsm]['transferOutQty'] += $row[csf('roll_rcv_qty')];
				}
				else{
					$transOutArr[$orderId][$febric_description_id][$color_id][$gsm]['transferOutQty'] += $row[csf('rcv_qty')];
				}
			}
		}
		
		$bookingInfoArr[$orderId]['fso_no'] = $row[csf('job_no')];
		$bookingInfoArr[$orderId]['customer_buyer'] = $row[csf('customer_buyer')];
		$bookingInfoArr[$orderId]['buyer_id'] = $row[csf('buyer_id')];
		$bookingInfoArr[$orderId]['booking_no'] = $row[csf('sales_booking_no')];
		$bookingInfoArr[$orderId]['style_ref_no'] = $row[csf('style_ref_no')];
	}
	unset($sqlNoOfRollResult);
	// echo "<pre>";print_r($dataArr);


	/*
	|--------------------------------------------------------------------------
	| for issue qty and roll
	|--------------------------------------------------------------------------
	|
	*/
	// echo "<pre>";print_r($poArr);die;
	if(!empty($poArr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 82, 1,$poArr, $empty_arr);
		oci_commit($con);

		//===== For Roll Splitting After Issue start ============
	    $split_chk_sql = sql_select("SELECT D.BARCODE_NO, D.QNTY 
	    from pro_roll_split C, pro_roll_details D, GBL_TEMP_ENGINE e 
	    where c.entry_form = 75 and C.split_from_id = D.roll_split_from and C.status_active = 1 and D.status_active = 1 and d.po_breakdown_id=e.ref_val and e.user_id=$user_id and e.entry_form=82 and e.ref_from=1");

	    if(!empty($split_chk_sql))
	    {
	        foreach ($split_chk_sql as $val)
	        {
	            $split_barcode_arr[$val['BARCODE_NO']]= $val['BARCODE_NO'];
	            if ($split_barcode_check[$val['BARCODE_NO']]=="") 
	            {
	                $split_barcode_check[$val['BARCODE_NO']]=$val['BARCODE_NO'];
	                $split_barcode=$val['BARCODE_NO'];
	                execute_query("INSERT INTO tmp_barcode_no(barcode_no,userid,entry_form) VALUES(".$split_barcode.", ".$user_id.",12424)");
	            }
	        }
	        oci_commit($con);

	        $split_ref_sql = sql_select("SELECT A.BARCODE_NO, A.QNTY, A.ROLL_ID, B.BARCODE_NO AS MOTHER_BARCODE 
	            from tmp_barcode_no t, pro_roll_details A, pro_roll_details B 
	            where t.barcode_no=a.barcode_no and t.userid=$user_id and t.entry_form=12424 and A.entry_form = 61 and A.roll_id = B.id and A.status_active =1 and B.status_active=1");
	        if(!empty($split_ref_sql))
	        {
	            foreach ($split_ref_sql as $value)
	            {
	                $mother_barcode_arr[$value['BARCODE_NO']] = $value['MOTHER_BARCODE'];
	            }
	        }
	    }
	    unset($split_chk_sql);
	    unset($split_ref_sql);
	    // ======== For Roll Splitting After Issue end =========
		
		$sqlNoOfRollIssue="
			SELECT
				d.company_id,
				e.prod_id, e.po_breakdown_id, SUM(g.qnty) AS issue_qty,
				f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box,
				COUNT(g.id) AS issue_roll, g.barcode_no, h.knit_dye_source
			FROM GBL_TEMP_ENGINE a
				INNER JOIN fabric_sales_order_mst d  ON a.ref_val=d.id and a.user_id=$user_id and a.entry_form=82 and a.ref_from=1
				INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id
				INNER JOIN inv_transaction f ON e.trans_id = f.id
				INNER JOIN pro_roll_details g ON e.dtls_id = g.dtls_id
				INNER JOIN INV_ISSUE_MASTER h ON g.mst_id = h.id and  f.mst_id = h.id 
			WHERE
				e.status_active = 1
				AND e.is_deleted = 0
				AND h.entry_form IN(61)
				AND e.entry_form IN(61)
				AND e.trans_type = 2
				AND f.status_active = 1
				AND f.is_deleted = 0
				AND g.status_active = 1
				AND g.is_deleted = 0
				AND g.entry_form IN(61) and g.is_sales=1
				AND d.company_id IN(".$companyId.")
				".$dateCondition."
			GROUP BY 
				d.company_id,
				e.prod_id, e.po_breakdown_id,
				f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, g.barcode_no, h.knit_dye_source
		";
		//disconnect($con); die;
		// echo $sqlNoOfRollIssue; die;
		$sqlNoOfRollIssueResult = sql_select($sqlNoOfRollIssue);
		$noOfRollIssueArr = array();
		foreach($sqlNoOfRollIssueResult as $row)
		{
			$compId = $row[csf('company_id')];
			$productId = $row[csf('prod_id')];
			$orderId = $row[csf('po_breakdown_id')];
			
			$deter_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];
			$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];
			$gsm=$prodBarcodeData[$row[csf("barcode_no")]]["gsm"];

			$noOfRollIssueArr[$orderId][$deter_id][$color_id][$gsm]['rollIssueQty'] += $row[csf('issue_roll')];//without split barcode count
			
	        $mother_barcode_no = $mother_barcode_arr[$row[csf("barcode_no")]];
	        if($mother_barcode_no != "")
	        {
	            $deter_id=$prodBarcodeData[$mother_barcode_no]["febric_description_id"];
	            $color_id=$prodBarcodeData[$mother_barcode_no]["color_id"];
	            $gsm=$prodBarcodeData[$mother_barcode_no]["gsm"];
	        }
	        if ($row[csf('knit_dye_source')]==1) 
	        {
	        	$issueQtyArr[$orderId][$deter_id][$color_id][$gsm]['in_side_issueQty'] += $row[csf('issue_qty')];
	        }
	        else
	        {
	        	$issueQtyArr[$orderId][$deter_id][$color_id][$gsm]['out_side_issueQty'] += $row[csf('issue_qty')];
	        }
		}
		unset($sqlNoOfRollIssueResult);
	}
	//echo $issueQty."=".$rollIssueQty;
	// echo "<pre>"; print_r($issueQtyArr);
	
	if(empty($dataArr))
	{
		echo get_empty_data_msg();
		die;
	}

	if(!empty($booking_idArr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 82, 2,$booking_idArr, $empty_arr);
		oci_commit($con);

		$int_ref_sql="SELECT a.id, a.po_number, a.grouping, b.booking_no, b.booking_mst_id 
		from GBL_TEMP_ENGINE g, fabric_sales_order_mst c, wo_booking_dtls b, wo_po_break_down a 
		where g.ref_val=c.booking_id and g.user_id=$user_id and g.entry_form=82 and g.ref_from=2 and c.BOOKING_ID=b.BOOKING_MST_ID and b.po_break_down_id=a.id and c.within_group=1 and b.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		// echo $po_sql;die;
		$int_ref_sql_result=sql_select($int_ref_sql);
		$int_ref_arr=array();
		foreach ($int_ref_sql_result as $key => $row) 
		{
			$int_ref_arr[$row[csf('booking_no')]] = $row[csf('grouping')];
		}
	}
	// echo "<pre>";print_r($booking_idArr);die;

	
	$company_arr = return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_array = return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$color_arr = return_library_array( 'SELECT id, color_name FROM lib_color', 'id', 'color_name');
		
	//composition and constructtion
	$composition_arr=array();
	$constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row) 
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($deter_array);

    /*
	|--------------------------------------------------------------------------
	| FSO req_qty
	|--------------------------------------------------------------------------
	|
	*/
	$sql_fso_book_qty = sql_select("SELECT a.booking_id, a.sales_booking_no, a.within_group, a.job_no, b.mst_id, b.color_id, b.determination_id, b.grey_qty , b.gsm_weight
	from fabric_sales_order_mst a, fabric_sales_order_dtls b, GBL_TEMP_ENGINE c 
	where a.id=b.mst_id and b.mst_id=c.ref_val and c.user_id=$user_id and c.entry_form=82 and c.ref_from=1 and b.status_active=1 and b.is_deleted=0");
	//$dataArr=array();
	foreach ($sql_fso_book_qty as $val) 
	{
		$fabric_construction = $val[csf('determination_id')];
		//$fso_booking_req[$val[csf("mst_id")]][$fabric_construction][$val[csf("color_id")]][$val[csf("gsm_weight")]]["req_qty"] +=$val[csf("grey_qty")];
		$dataArr[$val[csf("mst_id")]][$fabric_construction][$val[csf("color_id")]][$val[csf("gsm_weight")]]["req_qty"] +=$val[csf("grey_qty")];

		$sales_ord_ref[$val[csf("mst_id")]]['within_group'] = $val[csf("within_group")];
		// echo $val[csf("mst_id")].'*'.$fabric_construction.'*'.$val[csf("color_id")].'<br>';
	}
	// echo "<pre>";print_r($dataArr);

	$width = 1990;
	?>
	<style type="text/css">
		.word_break {
			word-break: break-all;
			word-wrap: break-word;
		}
	</style>
	<fieldset style="width:<?php echo $width+20; ?>px;margin:5px auto;">
		<table width="<?php echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<thead>
                <tr class="form_caption" style="border:none;">
                	<th align="center" colspan="23" style="font-size:16px"><strong>Rack Wise Grey Fabrics Stock Report</strong></th>
                </tr>
                <tr>
					<th width="30">SL</th>
					<th width="100" class="word_break">Cust. Buyer Name</th>
					<th width="100" class="word_break">IR/IB</th>
					<th width="100" class="word_break">Sales Job/ Booking No.</th>
					<th width="120" class="word_break">Sales Order No</th>
					<th width="110" class="word_break">Fab. Constraction</th>
					<th width="100" class="word_break">Fabric Color</th>
					<th width="60" class="word_break">GSM</th>
					<th width="100" class="word_break">Required Qty</th>
					<th width="80" class="word_break">Receive From Knitting</th>
					<th width="80" class="word_break">Transfer In</th>
					<th width="80" class="word_break">Transfer Out</th>
					<th width="80" class="word_break">Total<br>Recv.</th>
					<th width="80" class="word_break">Receive<br>Balance</th>
					<th width="80" class="word_break">No of Roll<br>Recv.</th>
					<th width="80" class="word_break">In Side Issue</th>
					<th width="80" class="word_break">Out Side Issue</th>
					<th width="80" class="word_break">Issue Return</th>					
					<th width="80" class="word_break">Total Issue</th>
					<th width="80" class="word_break">Issue<br>Balance</th>
					<th width="80" class="word_break">No of Roll<br>Issued</th>
					<th width="100" class="word_break">Stock Qty.</th>
					<th width="" class="word_break">No Of Roll Bal.</th>
				</tr>
			</thead>
		</table>
		<div style="width:<?php echo $width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table width="<?php echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
				<tbody>
				<?php
				$sl = 0;
				$grandTotal = array();
				// echo "<pre>";print_r($newDataArr);
				
				foreach($dataArr as $orderId=>$orderArr)
				{
					foreach($orderArr as $fabric_construction=>$fabric_constructionArr)
					{
						foreach($fabric_constructionArr as $fab_color_id=>$fab_color_id_v)
						{
							foreach($fab_color_id_v as $gsmk=>$row)
							{
	                            $productIds =implode(",",array_unique(explode(",",chop($row["prod_id"],","))));
								//total receive calculation
								$row['transferOutQty'] = $transOutArr[$orderId][$fabric_construction][$fab_color_id][$gsmk]['transferOutQty'];
								$row['totalRcvQty'] = (number_format($row['rcvQty'],2,'.','')+number_format($row['transferInQty'],2,'.',''))-number_format($row['transferOutQty'],2,'.','');
								
								//total issue calculation
								$row['in_side_issueQty'] = $issueQtyArr[$orderId][$fabric_construction][$fab_color_id][$gsmk]['in_side_issueQty'];
								$row['out_side_issueQty'] = $issueQtyArr[$orderId][$fabric_construction][$fab_color_id][$gsmk]['out_side_issueQty'];
								
								$row['totalIssueQty'] = (number_format($row['in_side_issueQty'],2,'.','')+number_format($row['out_side_issueQty'],2,'.',''))-number_format($row['issueReturnQty'],2,'.','');
								
								//stock qty calculation
								$row['stockQty'] = $row['totalRcvQty'] - $row['totalIssueQty'];

								//roll balance calculation
								$row['rollIssueQty'] = $transOutArr[$orderId][$fabric_construction][$fab_color_id][$gsmk]['rollIssueQty']+$noOfRollIssueArr[$orderId][$fabric_construction][$fab_color_id][$gsmk]['rollIssueQty'];
								$row['rollBalanceQty'] = $row['rollRcvQty'] - $row['rollIssueQty'];

								$all_fab_color_arr=array_unique(explode(",", $fab_color_id));
								$color_name="";
								foreach ($all_fab_color_arr as $key => $color) 
								{
									$color_name.=$color_arr[$color].',';
								}

								if($valueType != 1 && $row['stockQty'] == 0)
								{
									continue;
								}
								
								if($row['stockQty'] >= 0)
								{
									$sl++;
									$row['fso_no'] = $bookingInfoArr[$orderId]['fso_no'];
									$row['buyer_id'] = $bookingInfoArr[$orderId]['buyer_id'];
									$row['customer_buyer'] = $bookingInfoArr[$orderId]['customer_buyer'];
									$row['style_ref_no'] = $bookingInfoArr[$orderId]['style_ref_no'];
									$row['booking_no'] = $bookingInfoArr[$orderId]['booking_no'];

									$int_ref=$int_ref_arr[$row['booking_no']];
									
									//$productIds = implode("*",$productIdsArr[$orderId][$fabric_construction][$fab_color_id][$gsmk]); 
									//echo $orderId.'='.$fabric_construction.'='.$fab_color_id.'='.$gsmk.'='.$productIds.'<br>';
									$productIds=implode(",", array_unique(explode(",", chop($row['prod_id'],","))));
									$required_qnty = $fso_booking_req[$orderId][$fabric_construction][$fab_color_id][$gsmk]["req_qty"];

									$within_group = $sales_ord_ref[$val[csf("mst_id")]]['within_group'];
									if($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr valign="middle" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $sl; ?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')">
	                                    <td width="30" align="center"><?php echo $sl; ?></td>
	                                    <td width="100" class="word_break"><p><div style="word-break:break-all"><?php echo $buyer_array[$row['customer_buyer']]; ?></div></p></td>
	                                    <td width="100" class="word_break">
	                                    	<div><?php echo $int_ref; ?></div>
	                                    </td>
	                                    <td width="100" class="word_break"><div><?php echo $row['booking_no']; ?></div></td>
	                                    <td width="120" class="word_break"><div style="word-break:break-all">
											<a href="##" onclick="fabric_sales_order_print6('<? echo $compId;?>','<? //echo $compId;?>','<? echo $row['booking_no'];?>','<? echo $row['fso_no'];?>', '<? echo $within_group;?>');"><?php echo $row['fso_no']; ?></a></div>
										</td>
	                                    <td width="110" class="word_break"><div><?php echo $constructtion_arr[$fabric_construction]; ?></div></td>
	                                    <td width="100" class="word_break" title="<?=$fab_color_id;?>"><div><?php echo chop($color_name,","); ?></div></td>
	                                    <td width="60" class="word_break">
	                                    	<div><?php echo $gsmk; ?></div>
	                                    </td>
	                                    <td width="100" class="word_break" align="right">
	                                    	<a href='#report_details' onClick="openmypage_required_qty('<? echo $orderId; ?>','<? echo $fabric_construction; ?>','<? echo $fab_color_id; ?>','<? echo $gsmk; ?>','required_qty_popup',1);"><? echo number_format($required_qnty=$row['req_qty'],2);//$required_qnty; ?></a>
	                                    </td>

	                                    <td width="80" class="word_break" align="right">
	                                    	<a href='#report_details' onClick="openmypage_popup('<? echo $orderId; ?>','<? echo $fabric_construction; ?>','<? echo $fab_color_id; ?>','<? echo $gsmk; ?>','<? echo $productIds; ?>','recv_qty_popup',1);"><? echo number_format($row['rcvQty'],2); ?></a>
	                                    </td>
	                                    <td width="80" class="word_break" align="right">
	                                    	<a href='#report_details' onClick="openmypage_popup('<? echo $orderId; ?>','<? echo $fabric_construction; ?>','<? echo $fab_color_id; ?>','<? echo $gsmk; ?>','<? echo $productIds; ?>','transferIn_qty_popup',1);"><? echo number_format($row['transferInQty'],2); ?></a>
	                                    </td>
	                                    <td width="80" class="word_break" align="right">
	                                    	<a href='#report_details' onClick="openmypage_popup('<? echo $orderId; ?>','<? echo $fabric_construction; ?>','<? echo $fab_color_id; ?>','<? echo $gsmk; ?>','<? echo $productIds; ?>','transferOut_qty_popup',1);"><? echo number_format($row['transferOutQty'],2); ?></a>
	                                    </td>
	                                    <td width="80" class="word_break" align="right" title="Receive From Knitting +Transfer In -Transfer Out  =Total Receive">
	                                    	<a href='#report_details' onClick="openmypage_popup('<? echo $orderId; ?>','<? echo $fabric_construction; ?>','<? echo $fab_color_id; ?>','<? echo $gsmk; ?>','<? echo $productIds; ?>','totalRcv_qty_popup',3);"><? echo number_format($row['totalRcvQty'],2); ?></a>
	                                    </td>
	                                    <td width="80" class="word_break" align="right"><?php echo number_format($required_qnty-$row['totalRcvQty'],2); ?></td>
	                                    <td width="80" class="word_break" align="right">
	                                    	<a href='#report_details' onClick="openmypage_popup('<? echo $orderId; ?>','<? echo $fabric_construction; ?>','<? echo $fab_color_id; ?>','<? echo $gsmk; ?>','<? echo $productIds; ?>','no_of_roll_recv_popup',1);"><? echo number_format($row['rollRcvQty'],2); ?></a>
	                                    </td>

	                                    <td width="80" class="word_break" align="right">
	                                    	<a href='#report_details' onClick="openmypage_popup('<? echo $orderId; ?>','<? echo $fabric_construction; ?>','<? echo $fab_color_id; ?>','<? echo $gsmk; ?>','<? echo $productIds; ?>','issueQty_popup',1);"><? echo number_format($row['in_side_issueQty'],2); ?></a>
	                                    </td>
	                                    <td width="80" class="word_break" align="right">
	                                    	<a href='#report_details' onClick="openmypage_popup('<? echo $orderId; ?>','<? echo $fabric_construction; ?>','<? echo $fab_color_id; ?>','<? echo $gsmk; ?>','<? echo $productIds; ?>','issueQty_popup',2);"><? echo number_format($row['out_side_issueQty'],2); ?></a>
	                                    </td>
	                                    <td width="80" class="word_break" align="right">
	                                    	<a href='#report_details' onClick="openmypage_popup('<? echo $orderId; ?>','<? echo $fabric_construction; ?>','<? echo $fab_color_id; ?>','<? echo $gsmk; ?>','<? echo $productIds; ?>','issueReturnQty_popup',1);"><? echo number_format($row['issueReturnQty'],2); ?></a>
	                                    </td>
	                                    <td width="80" class="word_break" align="right">
	                                    	<a href='#report_details' onClick="openmypage_popup('<? echo $orderId; ?>','<? echo $fabric_construction; ?>','<? echo $fab_color_id; ?>','<? echo $gsmk; ?>','<? echo $productIds; ?>','totalIssQty_popup',1);"><? echo number_format($row['totalIssueQty'],2); ?></a>
	                                    </td>
	                                    <td width="80" class="word_break" align="right"><?php echo number_format($required_qnty-$row['totalIssueQty'],2); ?></td>
	                                    <td width="80" class="word_break" align="right">
	                                    	<a href='#report_details' onClick="openmypage_popup('<? echo $orderId; ?>','<? echo $fabric_construction; ?>','<? echo $fab_color_id; ?>','<? echo $gsmk; ?>','<? echo $productIds; ?>','no_of_roll_recv_popup',2);"><? echo number_format($row['rollIssueQty'],2); ?></a>
	                                    </td>	                                    

	                                    <td width="100" class="word_break" align="right">
	                                    	<a href='#report_details' onClick="openmypage_popup('<? echo $orderId; ?>','<? echo $fabric_construction; ?>','<? echo $fab_color_id; ?>','<? echo $gsmk; ?>','<? echo $productIds; ?>','stockQty_popup',1);"><? echo number_format($row['stockQty'],2); ?></a>
	                                    </td>
	                                    <td width="" class="word_break" align="right"><a href="##" onclick="openmypage_rollbal('<? echo $orderId;?>','<? echo $productIds;?>','<? echo $fab_color_id;?>');"><?php echo $row['rollBalanceQty']; ?></a></td>
	                                </tr>
									<?php
									//$grandTotal
									$grandTotal['requ_qty'] += number_format($required_qnty,2,'.','');
									$grandTotal['rcvQty'] += number_format($row['rcvQty'],2,'.','');
									$grandTotal['issueReturnQty'] += number_format($row['issueReturnQty'],2,'.','');
									$grandTotal['transferInQty'] += number_format($row['transferInQty'],2,'.','');
									$grandTotal['totalRcvQty'] += number_format($row['totalRcvQty'],2,'.','');
									$grandTotal['totalRollRcvQty'] += number_format($row['rollRcvQty'],2,'.','');
									$grandTotal['totalRcvBalanceQty'] += number_format($required_qnty-$row['totalRcvQty'],2,'.','');
							
									$grandTotal['in_side_issueQty'] += number_format($row['in_side_issueQty'],2,'.','');
									$grandTotal['out_side_issueQty'] += number_format($row['out_side_issueQty'],2,'.','');
									$grandTotal['transferOutQty'] += number_format($row['transferOutQty'],2,'.','');
									$grandTotal['totalIssueQty'] += number_format($row['totalIssueQty'],2,'.','');
									$grandTotal['totalRollIssueQty'] += number_format($row['rollIssueQty'],2,'.','');
									$grandTotal['totalIssueBalanceQty'] += number_format($required_qnty-$row['totalIssueQty'],2,'.','');
									$grandTotal['totalStockQty'] += number_format($row['stockQty'],2,'.','');
									$grandTotal['totalRollBalanceQty'] += $row['rollBalanceQty'];
								}
							}		
						}
					}
				}
				
                ?>
                </tbody>
            </table>
        </div>
        <table width="<?php echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_foot">
            <tfoot>
                <tr>
                    <th width="30">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>                    
                    <th width="120">&nbsp;</th>                    
                    <th width="110">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="60">Total</th>
                    <th width="100" align="right" id="value_requQty"><?php echo number_format($grandTotal['requ_qty'],2); ?></th>

					<th width="80" align="right" id="value_RcvQty"><?php echo number_format($grandTotal['rcvQty'],2); ?></th>					
					<th width="80" align="right" id="value_transInQty"><?php echo number_format($grandTotal['transferInQty'],2); ?></th>
                    <th width="80" align="right" id="value_transOutQty"><?php echo number_format($grandTotal['transferOutQty'],2); ?></th>
					<th width="80" align="right" id="value_totalRcvQty"><?php echo number_format($grandTotal['totalRcvQty'],2); ?></th>
                    <th width="80" align="right" id="value_RcvBalanceQty"><?php echo number_format($grandTotal['totalRcvBalanceQty'],2); ?></th>
                    <th width="80" align="right" id="value_totalRollRcvQty"><?php echo number_format($grandTotal['totalRollRcvQty'],2); ?></th>

                    <th width="80" align="right" id="value_in_side_issueqty"><?php echo number_format($grandTotal['in_side_issueQty'],2); ?></th>
                    <th width="80" align="right" id="value_out_side_issueQty"><?php echo number_format($grandTotal['out_side_issueQty'],2); ?></th>
                    <th width="80" align="right" id="value_issueRtnQty"><?php echo number_format($grandTotal['issueReturnQty'],2); ?></th>
                    <th width="80" align="right" id="value_totalIssueQty"><?php echo number_format($grandTotal['totalIssueQty'],2); ?></th>
                    <th width="80" align="right" id="value_issueBalanceQty"><?php echo number_format($grandTotal['totalIssueBalanceQty'],2); ?></th>
                    <th width="80" align="right" id="value_totalRollIssueQty"><?php echo number_format($grandTotal['totalRollIssueQty'],2); ?></th>

                    <th width="100" align="right" id="value_totalStockQty"><?php echo number_format($grandTotal['totalStockQty'],2); ?></th>
                    <th width="" align="right" id="value_totalRollBalanceQty"><?php echo $grandTotal['totalRollBalanceQty']; ?></th>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    <?php
    
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=82");
	execute_query("DELETE from tmp_barcode_no where userid=$user_id");
	oci_commit($con);
	disconnect($con);

	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	
	echo $html."####".$filename."####".$reportType;
	disconnect($con);
	die;
}

/*
|--------------------------------------------------------------------------
| report_generate_summary 3
|--------------------------------------------------------------------------
|
*/
if($action=="report_generate_summary3")
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	ob_start();
	$companyId = str_replace("'", "", $cbo_company_id);
	$buyerId = str_replace("'", "", $cbo_buyer_id);
	$year = str_replace("'", "", $cbo_year);
	$jobNo = trim(str_replace("'", "", $txt_job_no));
	$jobId = str_replace("'", "", $txt_job_id);
	$bookingNo = str_replace("'", "", $txt_booking_no);
	$bookingId = str_replace("'", "", $txt_booking_id);
	$searchBy = str_replace("'", "", $cbo_search_by);
	$searchCommon = str_replace("'", "", $txt_search_comm);
	$product = str_replace("'", "", $txt_product);
	$productId = str_replace("'", "", $txt_product_id);
	$productNo = str_replace("'", "", $txt_product_no);
	$storeId = str_replace("'", "", $txt_store_id);
	$floorId = str_replace("'", "", $txt_floor_id);
	$roomId = str_replace("'", "", $txt_room_id);
	$rackId = str_replace("'", "", $txt_rack_id);
	$valueType = str_replace("'", "", $cbo_value_type);
	$fromDate = str_replace("'", "", $txt_date_from);
	$toDate = str_replace("'", "", $txt_date_to);
	$reportType = str_replace("'", "", $rpt_type);
	//$_SESSION["date_from"] = date('Y-m-d', strtotime($date_from));
	//$_SESSION["date_to"] = date('Y-m-d', strtotime($date_to));
	$int_ref 		= trim(str_replace("'","",$txt_int_ref));
	
	//buyerIdCondition
	$buyerIdCondition = '';
	if($buyerId != 0)
	{
		$buyerIdCondition = " AND d.customer_buyer = ".$buyerId."";
	}
	
	//yearCondition;
	$yearCondition = '';
	if ($year>0) 
	{
		if($db_type==0)
		{
			$yearCondition = " AND YEAR(d.insert_date) = ".$year."";
		}
		else if($db_type==2)
		{
			$yearCondition = " AND TO_CHAR(d.insert_date,'YYYY') = ".$year."";
		}
	}
	
	//jobNoCondition
	$jobNoCondition = '';

	if($jobNo != '')
	{
		if($jobId != '')
		{
			$jobNoCondition = " AND d.id =".$jobId."";
		}else{
			$jobNoCondition = " AND d.job_no like '%".$jobNo."%'";
		}
	}
	
	//bookingNoCondition
	$bookingNoCondition = '';
	if($bookingNo != '')
	{
		$str_arry = explode(",",$bookingNo);
	    $all_bookingNo="";
	    foreach ($str_arry as $key => $booking) 
	    {
	        if ($all_bookingNo=="") 
	        {
	            $all_bookingNo.= $booking;
	        }
	        else 
	        {
	            $all_bookingNo.= "','".$booking;
	        }
	    }
	    // echo $all_bookingNo;die;
		$bookingNoCondition = " AND d.sales_booking_no in('$all_bookingNo')";
	}
	// echo $bookingNoCondition;die;

	if ($int_ref!="")
	{
		$po_sql="SELECT a.id, a.po_number, a.grouping, b.booking_no, b.booking_mst_id from wo_po_break_down a, wo_booking_dtls b, FABRIC_SALES_ORDER_MST c 
		where a.id=b.po_break_down_id and b.BOOKING_MST_ID=c.BOOKING_ID and c.within_group=1 and a.grouping='$int_ref' and b.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		// echo $po_sql;die;
		$po_sql_result=sql_select($po_sql);
		$refBooking_cond="";
		foreach ($po_sql_result as $key => $row) 
		{
			//$po_id_arr[$row[csf('id')]]=$row[csf('id')];
			$bookingNo_arr[$row[csf('booking_mst_id')]] = $row[csf('booking_mst_id')];
		}
		//$poIds=implode(",",array_unique(explode(",",$po_id_arr)));
		$refBooking_cond=" and d.booking_id in('".implode("','",$bookingNo_arr)."') ";
	}

	//searchByCondition
	$searchByCondition = '';
	if($searchCommon != '')
	{
		$expSearchCommon = explode(',', $searchCommon);
		$dataSearchCommon = '';
		foreach($expSearchCommon as $val)
		{
			if($dataSearchCommon != '')
			{
				$dataSearchCommon .= ',';
			}
			$dataSearchCommon .= "'".$val."'";
		}
		
		//style no
		if($searchBy == 1)
		{
			$searchByCondition = " AND d.style_ref_no IN(".$dataSearchCommon.")";
		}
		//order no
		else if($searchBy == 2)
		{
			$searchByCondition = " AND c.po_number IN(".$dataSearchCommon.")";
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
				$productIdCondition .= " e.prod_id IN(".$pids.") OR ";
			}
			$productIdCondition = chop($productIdCondition,'OR ');
			$productIdCondition .= ")";
		}
		else
		{
			$productIdCondition = " AND e.prod_id IN(".$productId.")";
		}
	}

	//storeCondition
	$storeCondition = '';
	if($storeId != '')
	{
		$storeCondition = " AND f.store_id IN(".$storeId.")";
	}

	//floorCondition
	$floorCondition = '';
	if($floorId != '')
	{
		$floorCondition = " AND f.floor_id IN(".$floorId.")";
	}

	//roomCondition
	$roomCondition = '';
	if($roomId != '')
	{
		$roomCondition = " AND f.room IN(".$roomId.")";
	}

	//rackCondition
	$rackCondition = '';
	if($rackId != '')
	{
		$rackCondition = " AND f.rack IN(".$rackId.")";
	}
	
	//dateCondition
	$dateCondition = '';
	$dateCondition2 = '';
	if($toDate != '')
	{
		if($db_type == 0)
		{
			$endDate = change_date_format($toDate,"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$endDate = change_date_format($toDate,"","",1);
		}

		$dateCondition = " AND f.transaction_date <= '".$endDate."'";
		$dateCondition2 = " AND a.transfer_date <= '".$endDate."'";
	}
	

	$con = connect();
	execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=166");
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=166");
	oci_commit($con);

	/*
	|--------------------------------------------------------------------------
	| for roll recv qty
	|--------------------------------------------------------------------------
	|
	*/
	$sqlRcvRollQty = "
		SELECT
			d.job_no, d.sales_booking_no, d.customer_buyer, d.buyer_id, d.style_ref_no, d.booking_id, d.company_id, e.prod_id, e.po_breakdown_id, e.entry_form, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, h.qnty AS rcv_qty, h.id AS no_of_roll_rcv, h.barcode_no, d.within_group
        FROM
			fabric_sales_order_mst d
			INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id
            INNER JOIN inv_transaction f ON e.trans_id = f.id
			INNER JOIN pro_grey_prod_entry_dtls g ON f.id = g.trans_id
			INNER JOIN pro_roll_details h ON g.id = h.dtls_id
        WHERE
			e.status_active = 1
			AND e.is_deleted = 0
			AND e.entry_form IN(2,22,58,84)
			AND e.trans_type IN(1,4)
			AND e.trans_id > 0
			AND f.status_active = 1
			AND f.is_deleted = 0
			AND d.company_id IN(".$companyId.")
			".$buyerIdCondition."
			".$yearCondition."
			".$jobNoCondition."
			".$bookingNoCondition."
			".$searchByCondition."
			".$productIdCondition."
			".$storeCondition."
			".$floorCondition."
			".$roomCondition."
			".$rackCondition."
			".$dateCondition."
			".$refBooking_cond."
			AND h.entry_form IN(2,22,58,84) AND h.status_active = 1 and h.is_sales=1
	";	
	// echo $sqlRcvRollQty; //die;
	$sqlRcvRollRslt = sql_select($sqlRcvRollQty);
	$barcode_no_arr = array();
	foreach($sqlRcvRollRslt as $row)
	{
        $barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
        $recv_booking_id_arr[$row[csf('booking_id')]]=$row[csf('booking_id')];
	}
	// unset($sqlRcvRollRslt);
	// echo "<pre>"; print_r($dataArr); die;

	/*
	|--------------------------------------------------------------------------
	| for issue qty and roll
	| order to order transfer
	|--------------------------------------------------------------------------
	|
	*/
	$sqlNoOfRoll="
		SELECT
			d.job_no, d.sales_booking_no, d.customer_buyer, d.buyer_id, d.style_ref_no, d.booking_id, d.company_id, e.prod_id, e.po_breakdown_id, e.trans_type, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, e.quantity AS rcv_qty, h.qnty as roll_rcv_qty, g.id AS issue_roll, h.barcode_no, i.transfer_criteria, d.within_group
		FROM 
			fabric_sales_order_mst d
			INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id
			INNER JOIN inv_transaction f ON e.trans_id = f.id 
			INNER JOIN inv_item_transfer_dtls g ON e.dtls_id = g.id 
			INNER JOIN pro_roll_details h ON g.id = h.dtls_id  and g.mst_id=h.mst_id and e.dtls_id=h.dtls_id
			INNER JOIN INV_ITEM_TRANSFER_MST i on i.id=h.mst_id and i.id=G.MST_ID and i.id= F.MST_ID
		WHERE
			e.status_active = 1 
			AND e.is_deleted = 0 
			AND e.entry_form IN(133) AND h.entry_form IN(133) and i.entry_form=133
			AND e.trans_type IN(5,6) 
			and g.TRANS_ID>0 and g.TO_TRANS_ID>0
			AND f.status_active = 1 
			AND f.is_deleted = 0 
			AND g.status_active = 1 
			AND g.is_deleted = 0
			AND h.status_active = 1 
			AND h.is_deleted = 0
			and h.is_sales=1
			AND d.company_id IN(".$companyId.")	
			".$buyerIdCondition."
			".$yearCondition."
			".$jobNoCondition."
			".$bookingNoCondition."
			".$searchByCondition."
			".$productIdCondition."
			".$storeCondition."
			".$floorCondition."
			".$roomCondition."
			".$rackCondition."
			".$dateCondition."
			".$refBooking_cond."
	";
	//echo "<br>".$sqlNoOfRoll; //die;
	$sqlNoOfRollResult = sql_select($sqlNoOfRoll);
	foreach($sqlNoOfRollResult as $row)
	{
        $barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
        $recv_booking_id_arr[$row[csf('booking_id')]]=$row[csf('booking_id')];
	}
	// unset($sqlNoOfRollResult);
	//echo $rollRcvQty."=".$transferInQty."=".$rollIssueQty."=".$transferOutQty;
	// echo "<pre>"; print_r($barcode_no_arr);
	
	/*
	|--------------------------------------------------------------------------
	| for production
	|--------------------------------------------------------------------------
	|
	*/
	if(!empty($barcode_no_arr))
	{
		foreach($barcode_no_arr as $barcode_no)
		{
			if( $barcode_no_check[$barcode_no] =="" )
	        {
	            $barcode_no_check[$barcode_no]=$barcode_no;
	            $barcodeno = $barcode_no;
	            // echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_id,$barcodeno)";
	            execute_query("insert into tmp_barcode_no (userid, barcode_no,entry_form) values ($user_id,$barcodeno,166)");
	        }			
		}
		oci_commit($con);

		$production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no,c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, c.knitting_source, c.challan_no as production_challan, c.knitting_company, a.yarn_prod_id, a.body_part_id, b.coller_cuff_size
		from pro_grey_prod_entry_dtls a, pro_roll_details b, inv_receive_master c, tmp_barcode_no d 
		where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2) and b.entry_form in(2)  and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid= $user_id and d.entry_form=166 $color_id_cond ");
        $yarn_prod_id_check=array();$prog_no_check=array();
        foreach ($production_sql as $row)
        {
            $prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"] =$row[csf("receive_basis")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prog_book"] =$row[csf("booking_no")];
            $prodBarcodeData[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
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
            $prodBarcodeData[$row[csf("barcode_no")]]["item_size"] =$row[csf("coller_cuff_size")];
            $prodBarcodeData[$row[csf("barcode_no")]]["machine_no_id"] =$row[csf("machine_no_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prod_challan"] =$row[csf("production_challan")];
            $prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"] =$row[csf("knitting_source")];
            $prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"] =$row[csf("knitting_company")];
            $prodBarcodeData[$row[csf("barcode_no")]]["body_part_id"] =$row[csf("body_part_id")];
            $allDeterArr[$row[csf("febric_description_id")]] =$row[csf("febric_description_id")];
            $allColorArr[$row[csf("color_id")]] =$row[csf("color_id")];
            $allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
        }
	}

	// Receive Data Array
	$dataArr = array();
	$poArr = array();
	foreach($sqlRcvRollRslt as $row)
	{
		$compId = $row[csf('company_id')];
		$productId = $row[csf('prod_id')];
		$orderId = $row[csf('po_breakdown_id')];
		$storeId = $row[csf('store_id')]*1;
		$floorId = $row[csf('floor_id')]*1;
		$roomId = $row[csf('room')]*1;
		$rackId = $row[csf('rack')]*1;
		$selfId = $row[csf('self')]*1;
		$binId = $row[csf('bin_box')]*1;
		$poArr[$orderId] = $orderId;
		$febric_description_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];
		$mc_dia=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"];
		$stitch_length=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"];
		$color_range_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_range_id"];
		$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];
		$gsm=$prodBarcodeData[$row[csf("barcode_no")]]["gsm"];
        $dia=$prodBarcodeData[$row[csf("barcode_no")]]["width"];
        $yarn_prod_id=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"];
        $yarn_count=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"];
		if ($color_id=="") 
		{
			$color_id=0;
		}

		$bookingInfoArr[$orderId]['fso_no'] = $row[csf('job_no')];
		$bookingInfoArr[$orderId]['customer_buyer'] = $row[csf('customer_buyer')];
		$bookingInfoArr[$orderId]['buyer_id'] = $row[csf('buyer_id')];
		$bookingInfoArr[$orderId]['booking_no'] = $row[csf('sales_booking_no')];
		$bookingInfoArr[$orderId]['style_ref_no'] = $row[csf('style_ref_no')];
		$bookingInfoArr[$orderId]['within_group'] = $row[csf('within_group')];
		
		$str_ref=$febric_description_id."*".$mc_dia."*".$color_id."*".$color_range_id."*".$gsm."*".$dia;
		$dataArr[$compId][$orderId][$productId][$str_ref]['stitch_length'].=$stitch_length.',';
		$dataArr[$compId][$orderId][$productId][$str_ref]['yarn_prod_id'].=$yarn_prod_id.',';
		$dataArr[$compId][$orderId][$productId][$str_ref]['yarn_count'].=$yarn_count.',';
		$dataArr[$compId][$orderId][$productId][$str_ref]['rollRcvQty'] += count($row[csf('no_of_roll_rcv')]);
		if($row[csf('entry_form')]  == 84)
		{
			$dataArr[$compId][$orderId][$productId][$str_ref]['issueReturnQty'] += $row[csf('rcv_qty')];
		}
		else
		{
			$dataArr[$compId][$orderId][$productId][$str_ref]['rcvQty'] += $row[csf('rcv_qty')];
		}
	}
	unset($sqlRcvRollRslt);
	// echo "<pre>";print_r($dataArr);die;

	// Transfer Data array
	foreach($sqlNoOfRollResult as $row)
	{
		$compId = $row[csf('company_id')];
		$productId = $row[csf('prod_id')];
		$orderId = $row[csf('po_breakdown_id')];
		$storeId = $row[csf('store_id')]*1;
		$floorId = $row[csf('floor_id')]*1;
		$roomId = $row[csf('room')]*1;
		$rackId = $row[csf('rack')]*1;
		$selfId = $row[csf('self')]*1;
		$binId = $row[csf('bin_box')]*1;
		$febric_description_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];
		$mc_dia=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"];
		$stitch_length=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"];
		$color_range_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_range_id"];
		$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];
		$gsm=$prodBarcodeData[$row[csf("barcode_no")]]["gsm"];
        $dia=$prodBarcodeData[$row[csf("barcode_no")]]["width"];
        $yarn_prod_id=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"];
        $yarn_count=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"];
		if ($color_id=="") 
		{
			$color_id=0;
		}
		$str_ref=$febric_description_id."*".$mc_dia."*".$color_id."*".$color_range_id."*".$gsm."*".$dia;
		if($row[csf('trans_type')] == 5)
		{
			$poArr[$orderId] = $orderId;
			
			$dataArr[$compId][$orderId][$productId][$str_ref]['stitch_length'].=$stitch_length.',';
			$dataArr[$compId][$orderId][$productId][$str_ref]['yarn_prod_id'].=$yarn_prod_id.',';
			$dataArr[$compId][$orderId][$productId][$str_ref]['yarn_count'].=$yarn_count.',';
			$dataArr[$compId][$orderId][$productId][$str_ref]['rollRcvQty'] += count($row[csf('issue_roll')]);
			$dataArr[$compId][$orderId][$productId][$str_ref]['transferInQty'] += $row[csf('roll_rcv_qty')];

			/*if ($row[csf('transfer_criteria')] !=2 ) // without store to store transfer in qty
			{
				if($row[csf('roll_rcv_qty')])
				{
					$dataArr[$compId][$orderId][$productId][$str_ref]['transferInQty'] += $row[csf('roll_rcv_qty')];
				}
				else{
					$dataArr[$compId][$orderId][$productId][$str_ref]['transferInQty'] += $row[csf('rcv_qty')];
				}
			}*/
		}
		if($row[csf('trans_type')] == 6)
		{
			$transOutArr[$compId][$orderId][$productId][$str_ref]['rollIssueQty'] += count($row[csf('issue_roll')]);
			$transOutArr[$compId][$orderId][$productId][$str_ref]['transferOutQty'] += $row[csf('roll_rcv_qty')];
			$transOutArr[$compId][$orderId][$productId][$str_ref]['barcode_no'].= $row[csf('barcode_no')].'='.$row[csf('roll_rcv_qty')].',';

			/*if ($row[csf('transfer_criteria')] !=2 ) // without store to store transfer out qty
			{
				if($row[csf('roll_rcv_qty')])
				{
					$transOutArr[$compId][$orderId][$productId][$str_ref]['transferOutQty'] += $row[csf('roll_rcv_qty')];
				}
				else{
					$transOutArr[$compId][$orderId][$productId][$str_ref]['transferOutQty'] += $row[csf('rcv_qty')];
				}
			}*/
		}
		
		$bookingInfoArr[$orderId]['fso_no'] = $row[csf('job_no')];
		$bookingInfoArr[$orderId]['customer_buyer'] = $row[csf('customer_buyer')];
		$bookingInfoArr[$orderId]['buyer_id'] = $row[csf('buyer_id')];
		$bookingInfoArr[$orderId]['booking_no'] = $row[csf('sales_booking_no')];
		$bookingInfoArr[$orderId]['style_ref_no'] = $row[csf('style_ref_no')];
		$bookingInfoArr[$orderId]['within_group'] = $row[csf('within_group')];
	}
	unset($sqlNoOfRollResult);
	// echo "<pre>";print_r($dataArr);

	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 166, 1,$poArr, $empty_arr); // po Id temp entry
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 166, 2,$allYarnProdArr, $empty_arr);//yarn_prod Id temp
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 166, 3,$recv_booking_id_arr, $empty_arr);//booking Id temp

	/*
	|--------------------------------------------------------------------------
	| for issue qty and roll
	|--------------------------------------------------------------------------
	|
	*/
	// echo "<pre>";print_r($poArr);die;
	if(!empty($poArr))
	{
		//===== For Roll Splitting After Issue start ============
	    $split_chk_sql = sql_select("SELECT D.BARCODE_NO, D.QNTY 
	    from GBL_TEMP_ENGINE e, pro_roll_details d, pro_roll_split c
	    where e.ref_val=d.po_breakdown_id and e.entry_form=166 and e.ref_from=1 and e.user_id=$user_id and c.entry_form = 75 and C.split_from_id = D.roll_split_from and C.status_active = 1 and D.status_active = 1");

	    if(!empty($split_chk_sql))
	    {
	        foreach ($split_chk_sql as $val)
	        {
	            $split_barcode_arr[$val['BARCODE_NO']]= $val['BARCODE_NO'];
	            if ($split_barcode_check[$val['BARCODE_NO']]=="") 
	            {
	                $split_barcode_check[$val['BARCODE_NO']]=$val['BARCODE_NO'];
	                $split_barcode=$val['BARCODE_NO'];
	                execute_query("INSERT INTO tmp_barcode_no(barcode_no,userid,entry_form) VALUES(".$split_barcode.", ".$user_id.",166)");
	            }
	        }
	        oci_commit($con);

	        $split_ref_sql = sql_select("SELECT A.BARCODE_NO, A.QNTY, A.ROLL_ID, B.BARCODE_NO AS MOTHER_BARCODE 
	            from tmp_barcode_no t, pro_roll_details A, pro_roll_details B 
	            where t.barcode_no=a.barcode_no and t.userid=$user_id and t.entry_form=166 and A.entry_form = 61 and A.roll_id = B.id and A.status_active =1 and B.status_active=1");
	        if(!empty($split_ref_sql))
	        {
	            foreach ($split_ref_sql as $value)
	            {
	                $mother_barcode_arr[$value['BARCODE_NO']] = $value['MOTHER_BARCODE'];
	            }
	        }
	    }
	    unset($split_chk_sql);
	    unset($split_ref_sql);
	    // ======== For Roll Splitting After Issue end =========
		
		$sqlNoOfRollIssue="
			SELECT
				d.company_id,
				e.prod_id, e.po_breakdown_id, SUM(g.qnty) AS issue_qty,
				f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box,
				COUNT(g.id) AS issue_roll, g.barcode_no
			FROM GBL_TEMP_ENGINE a, fabric_sales_order_mst d, order_wise_pro_details e, inv_transaction f, pro_roll_details g
			WHERE a.ref_val=d.id and a.entry_form=166 and a.ref_from=1 and a.user_id=$user_id and d.id = e.po_breakdown_id and e.trans_id = f.id and e.dtls_id = g.dtls_id and 
				e.status_active = 1
				AND e.is_deleted = 0
				AND e.entry_form IN(61)
				AND e.trans_type = 2
				AND f.status_active = 1
				AND f.is_deleted = 0
				AND g.status_active = 1
				AND g.is_deleted = 0
				AND g.entry_form IN(61) and g.is_sales=1
				AND d.company_id IN(".$companyId.")
				".$dateCondition."
			GROUP BY 
				d.company_id,
				e.prod_id, e.po_breakdown_id,
				f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, g.barcode_no
		";
		//echo $sqlNoOfRollIssue; die;
		$sqlNoOfRollIssueResult = sql_select($sqlNoOfRollIssue);
		$noOfRollIssueArr = array();
		foreach($sqlNoOfRollIssueResult as $row)
		{
			$compId = $row[csf('company_id')];
			$productId = $row[csf('prod_id')];
			$orderId = $row[csf('po_breakdown_id')];
			$storeId = $row[csf('store_id')]*1;
			$floorId = $row[csf('floor_id')]*1;
			$roomId = $row[csf('room')]*1;
			$rackId = $row[csf('rack')]*1;
			$selfId = $row[csf('self')]*1;
			$binId = $row[csf('bin_box')]*1;
			
			$deter_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];
			$mc_dia=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"];
			$stitch_length=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"];
			$color_range_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_range_id"];
			$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];
			$gsm=$prodBarcodeData[$row[csf("barcode_no")]]["gsm"];
        	$dia=$prodBarcodeData[$row[csf("barcode_no")]]["width"];
			if ($color_id=="") 
			{
				$color_id=0;
			}

			$str_ref=$deter_id."*".$mc_dia."*".$color_id."*".$color_range_id."*".$gsm."*".$dia;
			$noOfRollIssueArr[$compId][$orderId][$productId][$str_ref]['rollIssueQty'] += $row[csf('issue_roll')];//without split barcode count
			
	        $mother_barcode_no = $mother_barcode_arr[$row[csf("barcode_no")]];
	        if($mother_barcode_no != "")
	        {
	            $deter_id=$prodBarcodeData[$mother_barcode_no]["febric_description_id"];
	            $mc_dia=$prodBarcodeData[$mother_barcode_no]["machine_dia"];
				$stitch_length=$prodBarcodeData[$mother_barcode_no]["stitch_length"];
				$color_range_id=$prodBarcodeData[$mother_barcode_no]["color_range_id"];
				$color_id=$prodBarcodeData[$mother_barcode_no]["color_id"];
				$gsm=$prodBarcodeData[$mother_barcode_no]["gsm"];
        		$dia=$prodBarcodeData[$mother_barcode_no]["width"];
				if ($color_id=="") 
				{
					$color_id=0;
				}
				$str_ref=$deter_id."*".$mc_dia."*".$color_id."*".$color_range_id."*".$gsm."*".$dia;
	        }
	        $issueQtyArr[$compId][$orderId][$productId][$str_ref]['issueQty'] += $row[csf('issue_qty')];
		}
		unset($sqlNoOfRollIssueResult);
	}
	//echo $issueQty."=".$rollIssueQty;
	// echo "<pre>"; print_r($issueQtyArr);

	/*
	|--------------------------------------------------------------------------
	| for internal ref no
	|--------------------------------------------------------------------------
	|
	*/
	$ref_sql="SELECT b.booking_no, c.grouping from GBL_TEMP_ENGINE a, wo_booking_dtls b, wo_po_break_down c 
	where a.ref_val=b.booking_mst_id and a.entry_form=166 and a.ref_from=3 and a.user_id=$user_id and b.po_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.booking_no, c.grouping";
	$ref_sql_result=sql_select($ref_sql);
	foreach ($ref_sql_result as $row)
	{
		$int_ref_arr[$row[csf('booking_no')]].=$row[csf('grouping')].',';
	}

	/*
	|--------------------------------------------------------------------------
	| for yarn info
	|--------------------------------------------------------------------------
	|
	*/
	$brand_arr = return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$yarn_sql=  sql_select("SELECT a.id, a.yarn_count_id, a.yarn_type, a.yarn_comp_type1st, a.brand, a.lot 
	from GBL_TEMP_ENGINE g , product_details_master a
	where g.ref_val=a.id and g.entry_form=166 and g.ref_from=2 and g.user_id=$user_id and a.status_active = 1 and a.item_category_id =1");
    foreach ($yarn_sql as $row)
    {
        $yarn_ref[$row[csf("id")]]["y_count_id"] = $row[csf("yarn_count_id")];
        $yarn_ref[$row[csf("id")]]["comp"] = $composition[$row[csf("yarn_comp_type1st")]];
        $yarn_ref[$row[csf("id")]]["type"] = $yarn_type[$row[csf("yarn_type")]];
        $yarn_ref[$row[csf("id")]]["brand"] = $brand_arr[$row[csf("brand")]];
        $yarn_ref[$row[csf("id")]]["lot"] = $row[csf("lot")];
    }
    // echo '<pre>';print_r($yarn_ref);
	
	if(empty($dataArr))
	{
		echo get_empty_data_msg();
		die;
	}
	
	$company_arr = return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_array = return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$color_arr = return_library_array( 'SELECT id, color_name FROM lib_color', 'id', 'color_name');
	$yarncount_arr=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count"  );
		
	//composition and constructtion
	$composition_arr=array();
	$constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row) 
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($deter_array);

	$width = 2640;
	?>
	<style type="text/css">
		.word_break {
			word-break: break-all;
			word-wrap: break-word;
		}
	</style>
	<fieldset style="width:<?php echo $width+20; ?>px;margin:5px auto;">
		<table width="<?php echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<thead>
                <tr class="form_caption" style="border:none;">
                	<th align="center" colspan="32" style="font-size:16px"><strong>Rack Wise Grey Fabrics Stock Report</strong></th>
                </tr>
                <tr>
					<th width="30">SL</th>
					<th width="60" class="word_break">Company</th>
					<th width="100" class="word_break">Buyer Name</th>
					<th width="100" class="word_break">Style Ref. No.</th>
					<th width="120" class="word_break">Sales Order No</th>
					<th width="100">IR/IB</th>
					<th width="110" class="word_break">Fab. Constraction</th>
					<th width="110" class="word_break">Fab. Composition</th>
					<th width="50">Gsm</th>
					<th width="50">Dia</th>
					<th width="50">M/C Dia</th>
					<th width="50">Stich Length</th>
					<th width="100" class="word_break">Fabric Color</th>
					<th width="100" class="word_break">Color Range</th>

					<th width="100" class="word_break">Y. Count</th>
					<th width="100" class="word_break">Y. Composition</th>
					<th width="100" class="word_break">Y. Type</th>
					<th width="100" class="word_break">Y. Brand</th>
					<th width="100" class="word_break">Y. Lot</th>

					<th width="80" class="word_break">Receive</th>
					<th width="80" class="word_break">Issue Return</th>
					<th width="80" class="word_break">Transfer In</th>
					<th width="80" class="word_break">Total<br>Recv.</th>
					<th width="80" class="word_break">No of Roll<br>Recv.</th>
					<th width="80" class="word_break">Issue</th>
					<th width="80" class="word_break">Receive<br>Return</th>
					<th width="80" class="word_break">Transfer Out</th>
					<th width="80" class="word_break">Total Issue</th>
					<th width="80" class="word_break">No of Roll<br>Issued</th>

					<th width="80" class="word_break">Stock Qty.</th>
					<th width="" class="word_break">No Of Roll Bal.</th>
				</tr>
			</thead>
		</table>
		<div style="width:<?php echo $width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table width="<?php echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
				<tbody>
				<?php
				$sl = 0;
				$grandTotal = array();
				// echo "<pre>";print_r($newDataArr);
				foreach($dataArr as $compId=>$compArr)
				{
					foreach($compArr as $orderId=>$orderArr)
					{
						foreach($orderArr as $productId=>$productId_arr)
						{
							foreach($productId_arr as $str_ref=>$row)
							{
								$str_ref_arr = explode("*", $str_ref);
                                $fab_dter_id=$str_ref_arr[0];
                                $mc_dia=$str_ref_arr[1];
                                // $stitch_length=$str_ref_arr[2];
                                $color_id=$str_ref_arr[2];
                                $color_range_id=$str_ref_arr[3];
                                $gsm=$str_ref_arr[4];
                                $dia=$str_ref_arr[5];
                                $stitch_length=implode(",",array_unique(explode(",",chop($row['stitch_length'],","))));

								//total receive calculation
								$row['totalRcvQty'] = number_format($row['rcvQty'],2,'.','')+number_format($row['issueReturnQty'],2,'.','')+number_format($row['transferInQty'],2,'.','');
								
								//total issue calculation
								$row['issueQty'] = $issueQtyArr[$compId][$orderId][$productId][$str_ref]['issueQty'];
								$row['rcvReturnQty'] = 0;
								$row['transferOutQty'] = $transOutArr[$compId][$orderId][$productId][$str_ref]['transferOutQty'];
								$row['totalIssueQty'] = number_format($row['issueQty'],2,'.','')+number_format($row['rcvReturnQty'],2,'.','')+number_format($row['transferOutQty'],2,'.','');
								
								//stock qty calculation
								$row['stockQty'] = $row['totalRcvQty'] - $row['totalIssueQty'];

								//roll balance calculation
								$row['rollIssueQty'] = $transOutArr[$compId][$orderId][$productId][$str_ref]['rollIssueQty']+$noOfRollIssueArr[$compId][$orderId][$productId][$str_ref]['rollIssueQty'];
								$row['rollBalanceQty'] = $row['rollRcvQty'] - $row['rollIssueQty'];

								$color_id_arr = array_filter(array_unique(explode(",", $color_id)));
                                $colors="";
                                foreach ($color_id_arr as $color) 
                                {
                                    $colors .= $color_arr[$color] . ",";
                                }
                                $colors = rtrim($colors, ", ");

                                $yarn_counts_arr = array_unique(array_filter(explode(",", $row['yarn_count'])));
                                $yarn_counts="";
                                foreach ($yarn_counts_arr as $count) 
                                {
                                    $yarn_counts .= $yarncount_arr[$count] . ",";
                                }
                                $yarn_counts =implode(",",array_filter(array_unique(explode(",", $yarn_counts))));

                                $yarn_id_arr = array_unique(array_filter(explode(",", $row['yarn_prod_id'])));
                                $yarn_brand = $yarn_comp = $yarn_type_name = ""; $yarn_lot = "";
                                foreach ($yarn_id_arr as $yid)
                                {
                                    $yarn_comp .= ($yarn_comp =="") ? $yarn_ref[$yid]["comp"] :  ",". $yarn_ref[$yid]["comp"];
                                    $yarn_type_name .= ($yarn_type_name =="") ? $yarn_ref[$yid]["type"] :  ",". $yarn_ref[$yid]["type"];
                                    $yarn_brand .= ($yarn_brand =="") ? $yarn_ref[$yid]["brand"] :  ",". $yarn_ref[$yid]["brand"];
                                    $yarn_lot .= ($yarn_type_name =="") ? $yarn_ref[$yid]["lot"] :  ",". $yarn_ref[$yid]["lot"];
                                }

                                $yarn_comp =implode(",",array_filter(array_unique(explode(",", $yarn_comp))));
                                $yarn_type_name =implode(",",array_filter(array_unique(explode(",", $yarn_type_name))));
                                $yarn_brand =implode(",",array_filter(array_unique(explode(",", $yarn_brand))));
                                $yarn_lot =implode(",",array_filter(array_unique(explode(",", $yarn_lot))));

								if($valueType != 1 && $row['stockQty'] == 0)
								{
									continue;
								}
								
								if($row['stockQty'] >= 0)
								{
									$sl++;
									$row['fso_no'] = $bookingInfoArr[$orderId]['fso_no'];
									$row['buyer_id'] = $bookingInfoArr[$orderId]['buyer_id'];
									$row['customer_buyer'] = $bookingInfoArr[$orderId]['customer_buyer'];
									$row['style_ref_no'] = $bookingInfoArr[$orderId]['style_ref_no'];
									$row['booking_no'] = $bookingInfoArr[$orderId]['booking_no'];
									$row['within_group'] = $bookingInfoArr[$orderId]['within_group'];

									/* $buyer_name ='';
									if($row['within_group']==1)
									{
										$buyer_name = $company_arr[$row['buyer_id']];
									}
									else
									{
										$buyer_name = $buyer_array[$row['buyer_id']];
									} */

									$within_group = $sales_ord_ref[$val[csf("mst_id")]]['within_group'];
									if($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr valign="middle" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $sl; ?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')">
                                        <td width="30" align="center"><?php echo $sl; ?></td>
                                        <td width="60" class="word_break"><p><div style="word-break:break-all"><?php echo $company_arr[$compId]; ?></div></p></td>
                                        <td width="100" class="word_break" >
											<div><?php echo $buyer_array[$row['customer_buyer']]; ?></div>
										</td>
                                        <td width="100" class="word_break"><div><?php echo $row['style_ref_no']; ?></div></td>
                                        <td width="120" class="word_break"><div style="word-break:break-all"><?php echo $row['fso_no']; ?></div></td>
                                        <td width="100" class="word_break">
                                        	<div><?php echo chop($int_ref_arr[$row['booking_no']],","); ?></div>
                                        </td>
                                        <td width="110" class="word_break"><div><?php echo $constructtion_arr[$fab_dter_id]; ?></div></td>
                                        <td width="110" class="word_break"><div><?php echo $composition_arr[$fab_dter_id]; ?></div></td>
                                        <td width="50"><?php echo $gsm; ?></td>
                                        <td width="50"><?php echo $dia; ?></td>
                                        <td width="50"><?php echo $mc_dia; ?></td>
                                        <td width="50"><?php echo $stitch_length; ?></td>
                                        <td width="100" class="word_break" title="<?=$row["color_id"];?>"><div><?php echo $colors; ?></div></td>
                                        <td width="100" class="word_break"><div><?php echo $color_range[$color_range_id]; ?></div></td>

                                        <td width="100" class="word_break"><?php echo $yarn_counts; ?></td>
                                        <td width="100" class="word_break"><?php echo $yarn_comp; ?></td>
                                        <td width="100" class="word_break"><?php echo $yarn_type_name; ?></td>
                                        <td width="100" class="word_break"><?php echo $yarn_brand; ?></td>
                                        <td width="100" class="word_break"><?php echo $yarn_lot; ?></td>

                                        <td width="80" class="word_break" align="right"><?php echo number_format($row['rcvQty'],2); ?></td>
                                        <td width="80" class="word_break" align="right"><?php echo number_format($row['issueReturnQty'],2); ?></td>
                                        <td width="80" class="word_break" align="right"><?php echo number_format($row['transferInQty'],2); ?></td>
                                        <td width="80" class="word_break" align="right"><?php echo number_format($row['totalRcvQty'],2); ?></td>
                                        <td width="80" class="word_break" align="right">
                                        	<a href='#report_details' onClick="openmypage('<? echo $orderId; ?>','<? echo $productId; ?>','<? echo $color_id; ?>','grey_recv_issue_popup',1);"><? echo number_format($row['rollRcvQty'],2); ?></a>
                                        </td>

                                        <td width="80" class="word_break" align="right"><?php echo number_format($row['issueQty'],2); ?></td>
                                        <td width="80" class="word_break" align="right"><?php echo number_format($row['rcvReturnQty'],2); ?></td>
                                        <td width="80" class="word_break" align="right"><?php echo number_format($row['transferOutQty'],2); ?></td>
                                        <td width="80" class="word_break" align="right"><?php echo number_format($row['totalIssueQty'],2); ?></td>
                                        <td width="80" class="word_break" align="right">
                                        	<a href='#report_details' onClick="openmypage('<? echo $orderId; ?>','<? echo $productId; ?>','<? echo $color_id; ?>','grey_recv_issue_popup',2);"><? echo number_format($row['rollIssueQty'],2); ?></a>
                                        </td>

                                        <td width="80" class="word_break" align="right"><?php echo number_format($row['stockQty'],2); ?></td>

                                        <td width="100" class="word_break" align="right">
	                                    	<a href='#report_details' onClick="openmypage_popup('<? echo $orderId; ?>','<? echo $fab_dter_id; ?>','<? echo $color_id; ?>','<? echo $gsm; ?>','<? echo $productId; ?>','no_of_roll_bal_popup',1);"><? echo $row['rollBalanceQty']; ?></a>
	                                    </td>
                                    </tr>
									<?php
									//$grandTotal
									$grandTotal['rcvQty'] += number_format($row['rcvQty'],2,'.','');
									$grandTotal['issueReturnQty'] += number_format($row['issueReturnQty'],2,'.','');
									$grandTotal['transferInQty'] += number_format($row['transferInQty'],2,'.','');
									$grandTotal['totalRcvQty'] += number_format($row['totalRcvQty'],2,'.','');
									$grandTotal['totalRollRcvQty'] += number_format($row['rollRcvQty'],2,'.','');
							
									$grandTotal['issueQty'] += number_format($row['issueQty'],2,'.','');
									$grandTotal['rcvReturnQty'] += number_format($row['rcvReturnQty'],2,'.','');
									$grandTotal['transferOutQty'] += number_format($row['transferOutQty'],2,'.','');
									$grandTotal['totalIssueQty'] += number_format($row['totalIssueQty'],2,'.','');
									$grandTotal['totalRollIssueQty'] += number_format($row['rollIssueQty'],2,'.','');

									$grandTotal['totalStockQty'] += number_format($row['stockQty'],2,'.','');
									$grandTotal['totalRollBalanceQty'] += $row['rollBalanceQty'];
								}			
							}
						}
					}
				}
                ?>
                </tbody>
            </table>
        </div>
        <table width="<?php echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_foot">
            <tfoot>
                <tr>
                    <th width="30">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="120">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="50" title="stl">&nbsp;</th>
					<th width="100"></th>
					<th width="100"></th>

					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100">Total</th>

					<th width="80" align="right" id="value_RcvQty"><?php echo number_format($grandTotal['rcvQty'],2); ?></th>
					<th width="80" align="right" id="value_issueRtnQty"><?php echo number_format($grandTotal['issueReturnQty'],2); ?></th>
					<th width="80" align="right" id="value_transInQty"><?php echo number_format($grandTotal['transferInQty'],2); ?></th>
					<th width="80" align="right" id="value_totalRcvQty"><?php echo number_format($grandTotal['totalRcvQty'],2); ?></th>
                    <th width="80" align="right" id="value_totalRollRcvQty"><?php echo number_format($grandTotal['totalRollRcvQty'],2); ?></th>

                    <th width="80" align="right" id="value_issueQty"><?php echo number_format($grandTotal['issueQty'],2); ?></th>
                    <th width="80" align="right" id="value_recvRtnQty"><?php echo number_format($grandTotal['rcvReturnQty'],2); ?></th>
                    <th width="80" align="right" id="value_transOutQty"><?php echo number_format($grandTotal['transferOutQty'],2); ?></th>
                    <th width="80" align="right" id="value_totalIssueQty"><?php echo number_format($grandTotal['totalIssueQty'],2); ?></th>
                    <th width="80" align="right" id="value_totalRollIssueQty"><?php echo number_format($grandTotal['totalRollIssueQty'],2); ?></th>

                    <th width="80" align="right" id="value_totalStockQty"><?php echo number_format($grandTotal['totalStockQty'],2); ?></th>
                    <th width="" align="right" id="value_totalRollBalanceQty"><?php echo $grandTotal['totalRollBalanceQty']; ?></th>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    <?php
    
	execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=166");
	execute_query("DELETE from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=166");
	oci_commit($con);

	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	
	echo $html."####".$filename."####".$reportType;
	disconnect($con);
	die;
}

function sql_insert_zs( $strTable, $arrNames, $arrValues, $commit, $contain_lob )
{
	//global $con ;
	$con = connect();
	if($contain_lob=="") $contain_lob=0;
	if( $contain_lob==0)
	{
		$count=count($arrValues);
		//for multi row
		if( $count >1 )
		{
			$k=1;	
			foreach( $arrValues as $rows)
			{
				if($k==1)
				{
					$strQuery= "INSERT ALL \n";
				}
				$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$rows." \n";
				if( $count==$k )
				{
					$count=$count-$k;
					$strQuery .= "SELECT * FROM dual";
					$stid =  oci_parse($con, $strQuery);
					$exestd=oci_execute($stid, OCI_NO_AUTO_COMMIT);
					if(!$exestd)
						return 0;
						
					$strQuery="";
					$k=0;
				}
				else if ( $k==50 )
				{
					$count=$count-$k;
					$strQuery .= "SELECT * FROM dual";
					//return $strQuery;
					$stid =  oci_parse($con, $strQuery);
					$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
					if(!$exestd)
						return 0;
						
					$strQuery="";
					$k=0;
				}
				$k++;
			}
			return 1;
			//return $strQuery; 
		}
		//for single row
		else
		{
			$strQuery= "INSERT  \n";
			foreach( $arrValues as $rows)
			{
				$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$rows." \n";
			}
			//return $strQuery;
			$stid =  oci_parse($con, $strQuery);
			$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
			if (!$exestd) return 0; 
			else return 1;
		}
	}
}


if ($action=="stock_roll_balance_popup_bk")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);

	$cbo_company_id=$data[0];
	$fso_id=$data[1];
	$product_ids=$data[2];

	$issue_sql= sql_select("SELECT x.po_breakdown_id, x.barcode_no, x.store_id, x.floor_id, x.room, x.rack, x.self, x.bin_box, sum(x.qnty) as qnty
	from
	(
	SELECT c.po_breakdown_id, c.barcode_no, d.store_name as store_id, d.floor_id, d.room, cast(d.rack as varchar2(100)) as rack, d.self, d.bin_box, sum(c.qnty) as qnty
	from pro_roll_details c, inv_grey_fabric_issue_dtls d 
	where c.entry_form=61 and c.dtls_id=d.id and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($fso_id) and d.prod_id in($product_ids) and c.booking_without_order = 0
	group by c.po_breakdown_id, c.barcode_no, d.store_name, d.floor_id, d.room, d.rack, d.self, d.bin_box
	union all
	select a.po_breakdown_id, c.barcode_no, b.from_store as store_id, b.floor_id, b.room, cast(b.rack as varchar2(100)) as rack, b.shelf, b.bin_box, sum(c.qnty) as qnty 
	from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c 
	where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=133 and c.status_active=1 and c.is_deleted=0 and a.trans_type=6 and a.po_breakdown_id in($fso_id) and b.from_prod_id in($product_ids) and c.booking_without_order = 0
	group by c.barcode_no, a.po_breakdown_id, b.from_store, b.floor_id, b.room,b.rack, b.shelf,b.bin_box
	) x
	group by x.po_breakdown_id, x.barcode_no, x.store_id, x.floor_id, x.room, x.rack, x.self, x.bin_box
	");

	foreach ($issue_sql as  $row) 
	{
		$storeId = $row[csf('store_id')]*1;
		$floorId = $row[csf('floor_id')]*1;
		$roomId = $row[csf('room')]*1;
		$rackId = $row[csf('rack')]*1;
		$selfId = $row[csf('self')]*1;
		$binId = $row[csf('bin_box')]*1;

		$issue_roll_arr[$row[csf('barcode_no')]][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['issue_roll'] += $row[csf('qnty')];
	}

	$rcv_arr =sql_select("SELECT x.barcode_no,  x.roll_no, x.store_id,x.floor_id,x.room, x.rack, x.self, x.bin_box, sum(x.qnty) as qnty
	from
	(
		select c.barcode_no, c.roll_no, a.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, sum(c.qnty) as qnty
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
		where a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58,84) and c.entry_form in(2,22,58,84) and c.status_active=1 and c.is_deleted=0 and b.prod_id in($product_ids) and c.po_breakdown_id in($fso_id) and c.is_sales=1
		group by c.barcode_no, c.roll_no, a.store_id,b.floor_id, b.room, b.rack, b.self,b.bin_box
		union all
		select  c.barcode_no, c.roll_no, b.to_store as store_id, b.to_floor_id as floor_id, b.to_room as room,b.to_rack as rack, b.to_shelf as self, b.to_bin_box, sum(c.qnty) as qnty
		from inv_item_transfer_mst a,  inv_item_transfer_dtls b, pro_roll_details c
		where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(133) and c.entry_form in(133) and c.status_active=1 and c.is_deleted=0 and b.from_prod_id in($product_ids) and a.to_order_id in($fso_id) 
		group by  c.barcode_no, c.roll_no, b.to_store, b.to_floor_id, b.to_room,b.to_rack, b.to_shelf,b.to_bin_box
	) x
	group by x.barcode_no,  x.roll_no, x.store_id,x.floor_id,x.room, x.rack, x.self, x.bin_box");

	$floorRoomRackSelfArr = return_library_array( "SELECT a.floor_room_rack_id, a.floor_room_rack_name FROM lib_floor_room_rack_mst a  WHERE a.status_active = 1 AND a.is_deleted = 0 AND a.company_id IN(".$cbo_company_id.")", 'floor_room_rack_id', 'floor_room_rack_name');

	?>    
    <div id="data_panel" align="center" style="width:100%">
        <fieldset style="width: 98%">
        <table width="780" cellspacing="0" class="rpt_table" border="0" id="tbl_returnable_details" rules="all">
                <thead>
            <tr>
              <th width="50">Sl</th>
              <th width="130">Barcode no</th>
              <th width="130">Store no</th>
              <th width="130">Roll weight</th>
              <th width="100">Floor</th>
              <th width="100">Room</th>
              <th width="100">Rack</th>
              <th width="100">Shelf</th>
              <th width="100">Bin</th>
            </tr>
          </thead>  
                <tbody>
                <?
                $i=1;
                foreach ($rcv_arr as $row) 
                {
					$storeId = $row[csf('store_id')]*1;
					$floorId = $row[csf('floor_id')]*1;
					$roomId = $row[csf('room')]*1;
					$rackId = $row[csf('rack')]*1;
					$selfId = $row[csf('self')]*1;
					$binId = $row[csf('bin_box')]*1;

					$issue_qnty = $issue_roll_arr[$row[csf('barcode_no')]][$storeId][$floorId][$roomId][$rackId][$selfId][$binId]['issue_roll'];
					$bal_qnty = $row[csf('qnty')] - $issue_qnty;
					if($bal_qnty>0)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
					?>                         
					<tr bgcolor="<? echo $bgcolor; ?>">
					  <td width="50" align="center"><? echo $i; ?></td>
					  <td width="130" align="center"><? echo $row[csf('barcode_no')]; ?></td>
					  <td width="130" align="center"><? echo $storeId; ?></td>
					  <td width="130" align="center"><? echo number_format($bal_qnty,2); ?></td>
					  <td width="100" align="center"><? echo $floorRoomRackSelfArr[$floorId]; ?></td>
					  <td width="100" align="right"><? echo $floorRoomRackSelfArr[$roomId]; ?></td>
					  <td width="100" align="right"><? echo $floorRoomRackSelfArr[$rackId]; ?></td>
					  <td width="100" align="right"><? echo $floorRoomRackSelfArr[$selfId]; ?></td>
					  <td width="100" align="right"><? echo $floorRoomRackSelfArr[$binId]; ?></td>
					</tr>

					<?
					$i++;
					}                                     
                }
             ?>
             </tbody>       
        </table>
      </fieldset>
    </div> 
    <?	
    
	exit();
}

if ($action=="stock_roll_balance_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);

	$cbo_company_id=$data[0];
	$fso_id=$data[1];
	$product_ids=$data[2];
	$color_id=$data[3];
	$product_ids=str_replace("*", ",", $product_ids);

	if ($data[4]!="") $store_cond=" and a.store_id in($data[4])";
	if ($data[5]!="") $floor_cond=" and b.floor_id in($data[5])";
	if ($data[6]!="") $room_cond=" and b.room in($data[6])";
	if ($data[7]!="") $rack_cond=" and b.rack in($data[7])";

	if ($data[4]!="") $store_in_cond=" and b.to_store in($data[4])";
	if ($data[5]!="") $floor_in_cond=" and b.to_floor_id in($data[5])";
	if ($data[6]!="") $room_in_cond=" and b.to_room in($data[6])";
	if ($data[7]!="") $rack_in_cond=" and b.to_rack in($data[7])";

	if ($data[4]!="") $store_iss_cond=" and d.store_name in($data[4])";
	if ($data[5]!="") $floor_iss_cond=" and d.floor_id in($data[5])";
	if ($data[6]!="") $room_iss_cond=" and d.room in($data[6])";
	if ($data[7]!="") $rack_iss_cond=" and d.rack in($data[7])";

	$issue_sql= sql_select("SELECT x.po_breakdown_id, x.barcode_no, x.store_id, x.floor_id, x.room, x.rack, x.self, x.bin_box, sum(x.qnty) as qnty
	from
	(
	SELECT c.po_breakdown_id, c.barcode_no, d.store_name as store_id, d.floor_id, d.room, cast(d.rack as varchar2(100)) as rack, d.self, d.bin_box, sum(c.qnty) as qnty
	from pro_roll_details c, inv_grey_fabric_issue_dtls d 
	where c.entry_form=61 and c.dtls_id=d.id and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($fso_id) and d.prod_id in($product_ids) and c.booking_without_order = 0 and c.is_returned=0 $store_iss_cond $floor_iss_cond $room_iss_cond $rack_iss_cond
	group by c.po_breakdown_id, c.barcode_no, d.store_name, d.floor_id, d.room, d.rack, d.self, d.bin_box
	) x
	group by x.po_breakdown_id, x.barcode_no, x.store_id, x.floor_id, x.room, x.rack, x.self, x.bin_box
	");

	foreach ($issue_sql as  $row) 
	{
		$storeId = $row[csf('store_id')]*1;
		$floorId = $row[csf('floor_id')]*1;
		$roomId = $row[csf('room')]*1;
		$rackId = $row[csf('rack')]*1;
		$selfId = $row[csf('self')]*1;
		$binId = $row[csf('bin_box')]*1;

		$issued_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
	}
	if ($color_id!="") $color_cond=" and d.color_id='$color_id'";

	$rcv_sql = "SELECT y.barcode_no, y.roll_no, y.store_id,y.floor_id,y.room, y.rack, y.self, y.bin_box, y.qnty 
	FROM
	(
		SELECT x.barcode_no, x.roll_no, x.store_id,x.floor_id,x.room, x.rack, x.self, x.bin_box, sum(x.qnty) as qnty 
		from ( 
		select c.barcode_no, c.roll_no, a.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, sum(c.qnty) as qnty 
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
		where a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58,84) and c.entry_form in(2,22,58,84) and c.status_active=1 
		and c.is_deleted=0 and b.prod_id in($product_ids) and c.po_breakdown_id in($fso_id) and c.is_sales=1 and c.re_transfer=0 $store_cond $floor_cond $room_cond $rack_cond
		group by c.barcode_no, c.roll_no, a.store_id,b.floor_id, b.room, b.rack, b.self,b.bin_box 
		union all 
		select c.barcode_no, c.roll_no, b.to_store as store_id, b.to_floor_id as floor_id, b.to_room as room,b.to_rack as rack, b.to_shelf as self, b.to_bin_box, 
		sum(c.qnty) as qnty 
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c 
		where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(133) and c.entry_form in(133) and c.status_active=1 and c.is_deleted=0 
		and b.from_prod_id in($product_ids) and a.to_order_id in($fso_id) and c.re_transfer=0 $store_in_cond $floor_in_cond $room_in_cond $rack_in_cond 
		group by c.barcode_no, c.roll_no, b.to_store, b.to_floor_id, b.to_room,b.to_rack, b.to_shelf,b.to_bin_box 
		) x 
		group by x.barcode_no, x.roll_no, x.store_id,x.floor_id,x.room, x.rack, x.self, x.bin_box
	) y, pro_roll_details z, pro_grey_prod_entry_dtls d
	where y.barcode_no=z.barcode_no and z.dtls_id=d.id and z.entry_form in (2,22) $color_cond and z.status_active=1";
	// echo $rcv_sql;
	$rcv_arr =sql_select($rcv_sql);

	$floorRoomRackSelfArr = return_library_array( "SELECT a.floor_room_rack_id, a.floor_room_rack_name FROM lib_floor_room_rack_mst a  WHERE a.status_active = 1 AND a.is_deleted = 0 AND a.company_id IN(".$cbo_company_id.")", 'floor_room_rack_id', 'floor_room_rack_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');

	?>    
    <div id="data_panel" align="center" style="width:100%">
        <fieldset style="width: 98%">
        <table width="780" cellspacing="0" class="rpt_table" border="0" id="tbl_returnable_details" rules="all">
	        <thead>
	            <tr>
	              <th width="50">Sl</th>
	              <th width="130">Barcode no</th>
	              <th width="130">Store no</th>
	              <th width="130">Roll weight</th>
	              <th width="100">Floor</th>
	              <th width="100">Room</th>
	              <th width="100">Rack</th>
	              <th width="100">Shelf</th>
	              <th width="100">Bin</th>
	            </tr>
	        </thead>  
                <tbody>
                <?
                $i=1;
                foreach ($rcv_arr as $row) 
                {
					if($issued_barcode_arr[$row[csf('barcode_no')]]=="")
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>                         
						<tr bgcolor="<? echo $bgcolor; ?>">
						<td width="50" align="center"><? echo $i; ?></td>
						<td width="130" align="center"><? echo $row[csf('barcode_no')]; ?></td>
						<td width="130" align="center"><? echo $store_arr[$row[csf('store_id')]]; ?></td>
						<td width="130" align="center"><? echo number_format($row[csf('qnty')],2); ?></td>
						<td width="100" align="center"><? echo $floorRoomRackSelfArr[$row[csf('floor_id')]]; ?></td>
						<td width="100" align="right"><? echo $floorRoomRackSelfArr[$row[csf('room')]]; ?></td>
						<td width="100" align="right"><? echo $floorRoomRackSelfArr[$row[csf('rack')]]; ?></td>
						<td width="100" align="right" title="<?=$row[csf('self')];?>"><? echo $floorRoomRackSelfArr[$row[csf('self')]]; ?></td>
						<td width="100" align="right"><? echo $floorRoomRackSelfArr[$row[csf('bin_box')]]; ?></td>
						</tr>
						</tr>

						<?
						$i++;
					}                                     
                }
             ?>
             </tbody>       
        </table>
      </fieldset>
    </div> 
    <?	
    
	exit();
}

if($action=="grey_recv_issue_popup") // No of Roll Recv and No of Roll Issued
{
	echo load_html_head_contents("Grey Receive Info", "../../../../", 1, 1,'','','');
	// echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$companyID= $companyID;
	$orderID=$fso_id;
	$product_ids=$product_ids;
	$fab_color_id=$fab_color_id;
	$product_ids=str_replace("*", ",", $product_ids);

	if ($txt_store!="") $store_cond=" and b.store_id in($txt_store)";
	if ($txt_floor_id!="") $floor_cond=" and b.floor_id in($txt_floor_id)";
	if ($txt_room!="") $room_cond=" and b.room in($txt_room)";
	if ($txt_rack_id!="") $rack_cond=" and b.rack in($txt_rack_id)";

	if ($txt_store!="") $store_in_cond=" and b.to_store in($txt_store)";
	if ($txt_floor_id!="") $floor_in_cond=" and b.to_floor_id in($txt_floor_id)";
	if ($txt_room!="") $room_in_cond=" and b.to_room in($txt_room)";
	if ($txt_rack_id!="") $rack_in_cond=" and b.to_rack in($txt_rack_id)";

	if ($txt_store!="") $store_out_cond=" and b.from_store in($txt_store)";
	if ($txt_floor_id!="") $floor_out_cond=" and b.floor_id in($txt_floor_id)";
	if ($txt_room!="") $room_out_cond=" and b.room in($txt_room)";
	if ($txt_rack_id!="") $rack_out_cond=" and b.rack in($txt_rack_id)";

	$store_name_arr = return_library_array("select id, store_name from  lib_store_location", 'id', 'store_name');
	$floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where status_active =1 and is_deleted=0","floor_room_rack_id","floor_room_rack_name");

	$con = connect();
	execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=82");
	oci_commit($con);

	?>
	<fieldset style="width:<? echo $popup_width;?>; margin-left:3px">
		<div id="report_container">
			<!-- ====================Start========================= -->
			<table border="1" class="rpt_table" rules="all" width="900" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                		<?
                		if ($type==1) {$tbl_title='Number Of Roll Receive Popup';}else{$tbl_title='Number Of Roll Issue Popup';}
                		?>
                		<th colspan="11"><b><? echo $tbl_title; ?></b></th>
                	</tr>
                	<tr>
                		<?
                		if ($type==1) {$tbl_title='Receive';}else{$tbl_title='Issue';}
                		?>
                        <th colspan="11"><b><? echo $tbl_title; ?></b></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="120">Transaction Reff</th>
                        <th width="80">Transaction Date</th>
                        <th width="80">Barcode No.</th>
                        <th width="100">Store</th>
                        <th width="100">Floor</th>
                        <th width="80">Room</th>
                        <th width="80">Rack </th>
                        <th width="80">Shelf</th>
                        <th width="80">Bin</th>
                        <th>Qty</th>
                    </tr>
				</thead>
            </table>
            <div style="width:920px; max-height:410px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="900" cellpadding="0" cellspacing="0" id="table_body">
                    <?
                    if ($fab_color_id!="") $color_cond=" and d.color_id in('$fab_color_id')";
                    if ($type==1) // Recv
                    {
                    	$programData=sql_select("SELECT a.recv_number as sys_number,a.receive_date as sys_no_date,sum(e.qnty) as grey_receive_qnty,b.floor_id,b.room,b.rack,b.self,b.bin_box,d.no_of_roll,b.store_id,b.cons_uom,c.po_breakdown_id,b.prod_id, e.barcode_no
						from inv_receive_master a, inv_transaction b, order_wise_pro_details c,pro_grey_prod_entry_dtls d, pro_roll_details e 
						where a.id=b.mst_id and b.id=c.trans_id and a.id=d.mst_id and c.dtls_id=d.id and a.id=e.mst_id and d.id=e.dtls_id and c.dtls_id=e.dtls_id and a.entry_form in(2,22,58) and c.trans_id <>0 and c.trans_type=1 and c.entry_form in (2,22,58) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_id=$companyID and c.po_breakdown_id in($fso_id) and c.prod_id in($product_ids) $color_cond $store_cond $floor_cond $room_cond $rack_cond
						group by a.recv_number,a.receive_date,b.floor_id,b.room,b.rack,b.self,b.bin_box,b.cons_uom,d.no_of_roll,b.store_id,c.po_breakdown_id,b.prod_id, e.barcode_no");
                    }
                    else // Issue
                    {
                    	$programData=sql_select("SELECT a.issue_number as sys_number,a.issue_date as sys_no_date,sum(e.qnty) as grey_receive_qnty,b.floor_id,b.room,b.rack,b.self,b.bin_box,d.no_of_roll,b.store_id,b.cons_uom,c.po_breakdown_id,b.prod_id, e.barcode_no 
                    	from inv_issue_master a, inv_transaction b, order_wise_pro_details c, inv_grey_fabric_issue_dtls d, pro_roll_details e 
                    	where a.id=b.mst_id and b.id=c.trans_id and c.dtls_id=d.id and a.id=e.mst_id and c.dtls_id=e.dtls_id and d.id=e.dtls_id and a.entry_form in(16,61) and c.trans_id <>0 and c.trans_type=2 and c.entry_form in (16,61) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_id=$companyID and c.po_breakdown_id in($fso_id) and c.prod_id in($product_ids) $color_cond $store_cond $floor_cond $room_cond $rack_cond
                    	group by a.issue_number,a.issue_date,b.floor_id,b.room,b.rack,b.self,b.bin_box,b.cons_uom,d.no_of_roll,b.store_id,c.po_breakdown_id,b.prod_id, e.barcode_no");
                    }
					
					$i=1;
					foreach ($programData as $row) 
					{						
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                        <td width="30"><? echo $i; ?></td>
	                        <td width="120"><p><? echo $row[csf('sys_number')]; ?></p></td>
	                        <td width="80"  align="center"><? echo change_date_format($row[csf('sys_no_date')]); ?></td>
	                        <td width="80"><div style="word-break:break-all"><p><? echo $row[csf('barcode_no')]; ?></p></div></td>
	                        <td width="100"><div style="word-break:break-all"><? echo $store_name_arr[$row[csf('store_id')]]; ?></div></td>
	                        <td width="100" align="center"><div style="word-break:break-all"><? echo $floor_room_rack_arr[$row[csf('floor_id')]]; ?></div></td>
	                        <td width="80" align="center"><div style="word-break:break-all"><? echo $floor_room_rack_arr[$row[csf('room')]]; ?></div></td>
	                        <td width="80" align="center"><div style="word-break:break-all"><? echo $floor_room_rack_arr[$row[csf('rack')]]; ?></div></td>
	                        <td width="80" align="center"><div style="word-break:break-all"><? echo $floor_room_rack_arr[$row[csf('self')]]; ?></div></td>
	                        <td width="80" align="center"><p><? echo $floor_room_rack_arr[$row[csf('bin_box')]]; ?></p>&nbsp;</td>
	                        <td align="right"><? echo $row[csf('grey_receive_qnty')]; ?>&nbsp;</td>
	                    </tr>
	                    <?
	                    $total_recv_qty+=$row[csf('grey_receive_qnty')];
	                    $i++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="10" align="right">Sub Total</th>
                            <th align="right"><? echo number_format($total_recv_qty,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
			</div>
			<!-- =====================End========================== -->
			
			<!-- ====================Start========================= -->
			<table border="1" class="rpt_table" rules="all" width="900" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                		<?
                		if ($type==1) {$tbl_title='Issue Return';}else{$tbl_title='Receive Return';}
                		?>
                        <th colspan="11"><b><? echo $tbl_title; ?></b></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="120">Transaction Reff</th>
                        <th width="80">Transaction Date</th>
                        <th width="80">Barcode No.</th>
                        <th width="100">Store</th>
                        <th width="100">Floor</th>
                        <th width="80">Room</th>
                        <th width="80">Rack </th>
                        <th width="80">Shelf</th>
                        <th width="80">Bin</th>
                        <th>Qty</th>
                    </tr>
				</thead>
            </table>
            <div style="width:920px; max-height:410px; overflow-y:scroll" id="scroll_body2">
                <table border="1" class="rpt_table" rules="all" width="900" cellpadding="0" cellspacing="0" id="table_body2">
                    <?	
                    if ($type==1) // Issue Return
                    {
                    	$programData=sql_select("SELECT a.recv_number as sys_number,a.receive_date as sys_number_date,sum(b.cons_quantity) as grey_issue_rtn_qnty,b.floor_id,b.room,b.rack,b.self,b.bin_box,d.no_of_roll,b.store_id,b.cons_uom,c.po_breakdown_id,b.prod_id,count(d.id) as roll_no, e.barcode_no  
						from inv_receive_master a, inv_transaction b, order_wise_pro_details c,pro_grey_prod_entry_dtls d, pro_roll_details e   
						where a.id=b.mst_id and b.id=c.trans_id and a.id=d.mst_id and c.dtls_id=d.id and a.id=e.mst_id and d.id=e.dtls_id and c.dtls_id=e.dtls_id and b.mst_id=e.mst_id and a.entry_form in(84,51) and c.trans_id <>0 and c.entry_form in (84,51) and c.trans_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_id=$companyID and c.po_breakdown_id in($fso_id) and c.prod_id in($product_ids)  $color_cond
						group by a.recv_number,a.receive_date,b.floor_id,b.room,b.rack,b.self,b.bin_box,b.cons_uom,d.no_of_roll,b.store_id,c.po_breakdown_id,b.prod_id, e.barcode_no");
                    }
                    else // Receive Return
                    {
						$programData=sql_select("SELECT a.issue_number as sys_number,a.issue_date as sys_number_date,sum(b.cons_quantity) as grey_issue_rtn_qnty,b.room,b.rack,b.self,b.bin_box,d.no_of_roll,b.store_id,b.cons_uom,c.po_breakdown_id,b.prod_id,count(d.id) as roll_no, e.barcode_no 
						from inv_issue_master a, inv_transaction b, order_wise_pro_details c, pro_grey_prod_entry_dtls d, pro_roll_details e  where a.id=b.mst_id and b.id=c.trans_id and a.id=d.mst_id and a.id=e.mst_id and d.id=e.dtls_id and c.dtls_id=e.dtls_id and b.mst_id=e.mst_id and a.entry_form in(45) and c.trans_id <>0 and c.entry_form in (45) and c.trans_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_id=1 and c.po_breakdown_id in($fso_id) and c.prod_id in($product_ids) group by a.issue_number,a.issue_date,b.room,b.rack,b.self,b.bin_box,b.cons_uom,d.no_of_roll,b.store_id,c.po_breakdown_id,b.prod_id, e.barcode_no ");
					}
					
					$ii=1;
					foreach ($programData as $row) 
					{
						$store_arr[$row[csf('prod_id')]]['store_id']=$row[csf('store_id')];
                    	?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $ii; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $ii;?>">
	                        <td width="30"><? echo $ii; ?></td>
	                        <td width="120"><p><? echo $row[csf('sys_number')]; ?></p></td>
	                        <td width="80"  align="center"><? echo change_date_format($row[csf('sys_number_date')]); ?></td>
	                        <td width="80"><div style="word-break:break-all"><p><? echo $row[csf('barcode_no')]; ?></p></div></td>
	                        <td width="100"><div style="word-break:break-all"><? echo $store_name_arr[$row[csf('store_id')]]; ?></div></td>
	                        <td width="100" align="center"><div style="word-break:break-all"><? echo $floor_room_rack_arr[$row[csf('floor_id')]]; ?></div></td>
	                        <td width="80" align="center"><div style="word-break:break-all"><? echo $floor_room_rack_arr[$row[csf('room')]]; ?></div></td>
	                        <td width="80" align="center"><div style="word-break:break-all"><? echo $floor_room_rack_arr[$row[csf('rack')]]; ?></div></td>
	                        <td width="80" align="center"><div style="word-break:break-all"><? echo $floor_room_rack_arr[$row[csf('self')]]; ?></div></td>
	                        <td width="80" align="center"><p><? echo $floor_room_rack_arr[$row[csf('bin_box')]]; ?></p>&nbsp;</td>
	                        <td align="right"><? echo number_format($row[csf('grey_issue_rtn_qnty')],2); ?>&nbsp;</td>
	                    </tr>
	                    <?
	                    $total_grey_issue_rtn_qnty+=$row[csf('grey_issue_rtn_qnty')];
	                    $ii++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="10" align="right">Sub Total</th>
                            <th align="right"><? echo number_format($total_grey_issue_rtn_qnty,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
			</div>
			<!-- =====================End========================== -->

			<!-- ====================Start========================= -->
			<table border="1" class="rpt_table" rules="all" width="1030" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                		<?
                		if ($type==1) {$tbl_title='Transfer In';}else{$tbl_title='Transfer Out';}
                		?>
                        <th colspan="12"><b><? echo $tbl_title; ?></b></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="70">Transaction Reff</th>
                        <th width="120">Transaction Date</th>
                        <th width="100"><?if ($type==1) echo "From Job";else echo "To Job";?></th>
                        <th width="80">Barcode No.</th>
                        <th width="80">Store</th>
                        <th width="60">Floor</th>
                        <th width="80">Room</th>
                        <th width="100">Rack </th>
                        <th width="100">Shelf</th>
                        <th width="60">Bin</th>
                        <th>Qty</th>
                    </tr>
				</thead>
            </table>
            <div style="width:1050px; max-height:410px; overflow-y:scroll" id="scroll_body3">
                <table border="1" class="rpt_table" rules="all" width="1030" cellpadding="0" cellspacing="0" id="table_body3">
                    <?	
                    if ($type==1) // Trans in
                    {
						$programData=sql_select("SELECT a.transfer_system_id,a.transfer_date, b.to_store as store_id, b.to_floor_id as floor_id, b.to_room as room, b.to_rack as rack, b.to_shelf as shelf,b.to_bin_box as bin_box,sum(b.transfer_qnty) as transfer_qnty,b.uom,b.no_of_roll,c.po_breakdown_id,b.from_prod_id as prod_id,d.roll_no ,sum(d.qnty) as roll_qnty, d.barcode_no, a.from_order_id as order_id, a.to_order_id, e.job_no
						from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c,pro_roll_details d, fabric_sales_order_mst e
						where a.id=b.mst_id and b.to_trans_id=c.trans_id and b.id=d.dtls_id and a.id=d.mst_id and a.to_order_id=e.id and a.entry_form in(133) AND d.entry_form IN (133) and c.trans_id <>0 and c.entry_form in (133) and c.trans_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.to_company=$companyID and c.prod_id in($product_ids) and c.po_breakdown_id in($fso_id) and d.is_sales=1 $store_in_cond $floor_in_cond $room_in_cond $rack_in_cond
						group by c.po_breakdown_id,b.from_prod_id,a.transfer_system_id,a.transfer_date,b.to_store, b.to_floor_id, b.to_room,b.to_rack ,b.to_shelf,b.to_bin_box,b.uom,b.no_of_roll,d.roll_no, d.barcode_no, a.from_order_id, a.to_order_id, e.job_no");    	                 
					}			
					else // Trans out
					{
						$programData=sql_select("SELECT a.transfer_system_id,a.transfer_date,b.from_store as store_id, b.floor_id, b.room, b.rack,b.shelf,b.bin_box,sum(b.transfer_qnty) as transfer_qnty,b.uom,b.no_of_roll,c.po_breakdown_id,b.from_prod_id as prod_id,d.roll_no,sum(d.qnty) as roll_qnty, d.barcode_no, a.from_order_id, a.to_order_id as order_id, e.job_no
						from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, pro_roll_details d, fabric_sales_order_mst e
						where a.id=b.mst_id and b.trans_id=c.trans_id and  b.id=d.dtls_id and a.id=d.mst_id and a.FROM_ORDER_ID=e.id and a.entry_form in(133) and c.trans_id <>0 and b.trans_id <>0 and b.TO_TRANS_ID>0 and c.entry_form in (133) and c.trans_type=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id=$companyID  and c.prod_id in($product_ids) and c.po_breakdown_id in($fso_id) and d.is_sales=1 $store_out_cond $floor_out_cond $room_out_cond $rack_out_cond
						group by c.po_breakdown_id,b.from_prod_id,a.transfer_system_id,a.transfer_date, b.from_store, b.floor_id, b.room,b.rack,b.shelf,b.bin_box,b.uom,b.no_of_roll,d.roll_no, d.barcode_no, a.from_order_id, a.to_order_id, e.job_no");
					}

					foreach ($programData as $key => $row) 
					{
						$barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
					}

					// Production Sql
					if(!empty($barcode_no_arr))
					{
						foreach($barcode_no_arr as $barcode_no)
						{
							if( $barcode_no_check[$barcode_no] =="" )
					        {
					            $barcode_no_check[$barcode_no]=$barcode_no;
					            $barcodeno = $barcode_no;
					            execute_query("insert into tmp_barcode_no (userid, barcode_no,entry_form) values ($user_id,$barcodeno,82)");
					        }			
						}
						oci_commit($con);

						$production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no,c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, a.brand_id, c.knitting_source, c.challan_no as production_challan, c.knitting_company, a.yarn_prod_id, a.body_part_id, b.coller_cuff_size
						from pro_grey_prod_entry_dtls a, pro_roll_details b, inv_receive_master c, tmp_barcode_no d 
						where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2) and b.entry_form in(2)  and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid= $user_id and d.entry_form=82");
				        $yarn_prod_id_check=array();$prog_no_check=array();
				        foreach ($production_sql as $row)
				        {
				            $prodBarcodeData[$row[csf("barcode_no")]]["color_id"] =$row[csf("color_id")];
				        }
					}

					$iii=1;
					foreach ($programData as $row) 
					{
						$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];
						// echo $color_id.'=='.$fab_color_id.'<br>';
						if ($color_id==$fab_color_id) 
						{
							//if ($barcodenoCheck[$row[csf("barcode_no")]]=="") 
							//{
								$barcodenoCheck[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		                    	?>
		                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $iii; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $iii;?>">
		                            <td width="30"><? echo $iii; ?></td>
		                            <td width="70"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
		                            <td width="120"  align="center"><? echo change_date_format($row[csf('transfer_date')]);// ?></td>
		                            <td width="100"><div style="word-break:break-all"><p><? echo $row[csf("job_no")]; ?></p></div></td>
		                            <td width="80" align="center"><? echo $row[csf('barcode_no')]; ?>&nbsp;</td>
									<td width="80"><div style="word-break:break-all"><? echo $store_name_arr[$row[csf('store_id')]]; ?></div></td>							
		                            <td width="60" align="center"><? echo $floor_room_rack_arr[$row[csf("floor_id")]]; ?>&nbsp;</td>
									<td width="80" align="center"><div style="word-break:break-all"><? echo $floor_room_rack_arr[$row[csf('room')]]; ?></div></td>
		                            <td width="100" align="center"><div style="word-break:break-all"><? echo $floor_room_rack_arr[$row[csf('rack')]]; ?></div></td>
		                            <td width="100" align="center"><div style="word-break:break-all"><? echo $floor_room_rack_arr[$row[csf('shelf')]]; ?></div></td>
		                            <td width="60" align="center"><p><? echo $floor_room_rack_arr[$row[csf('bin_box')]]; ?></p>&nbsp;</td>
		                            <td align="right"><? echo $row[csf('roll_qnty')]; ?>&nbsp;</td>
		                        </tr>
			                    <?
			                    $total_trnsf_qty+=$row[csf('roll_qnty')];
			                    $iii++;
		                	//}
	                	}
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="11" align="right">Sub Total</th>
                            <th align="right"><? echo number_format($total_trnsf_qty,2); ?></th>
                        </tr>
                        <tr>
                            <th colspan="11" align="right">Grand Total</th>
                            <th align="right"><? echo number_format($total_recv_qty+$total_grey_issue_rtn_qnty+$total_trnsf_qty,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
			</div>
			<!-- ====================End=========================== -->
			<?//die('die for check');?>			
		</div>
	</fieldset>	
	<? 
	execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=82");
	oci_commit($con);
	disconnect($con);
	exit();
}

if ($action=="required_qty_popup") // Required Qty popup
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$companyID= $companyID;
	$orderID=$fso_id;
	$determination_id=$fabric_construction;
	$color_id=$fab_color_id;
	$gsm=$gsm;

	$sql_fso_book_qty = sql_select("SELECT a.booking_id, a.sales_booking_no, a.within_group, a.job_no, b.mst_id, b.color_id, b.determination_id, b.gsm_weight, b.dia, b.process_loss, b.grey_qty, b.finish_qty
	from fabric_sales_order_mst a, fabric_sales_order_dtls b 
	where a.id=b.mst_id and a.id=$orderID and b.determination_id=$determination_id and b.color_id=$color_id and b.gsm_weight=$gsm and b.status_active=1 and b.is_deleted=0");
	//composition and constructtion
	$composition_arr=array();
	$constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row) 
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($deter_array);

	?>    
    <div id="data_panel" align="center" style="width:100%">
        <fieldset style="width: 98%">
        <table width="380" cellspacing="0" class="rpt_table" border="0" id="tbl_returnable_details" rules="all">
	        <thead>
	            <tr>
	              <th width="50">Sl</th>
	              <th width="130">Fab. Composition</th>
	              <th width="130">Dia</th>
	              <th width="130">Finish Qty</th>
	              <th width="100">P/L %</th>
	            </tr>
	        </thead>  
            <tbody>
                <?
                $i=1;
                foreach ($sql_fso_book_qty as $row) 
                {
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>                         
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td width="50" align="center"><? echo $i; ?></td>
						<td width="100" align="center"><? echo $composition_arr[$row[csf('determination_id')]]; ?></td>
						<td width="50" align="center"><? echo $row[csf('dia')]; ?></td>
						<td width="80" align="right"><? echo number_format($row[csf('finish_qty')],2); ?></td>
						<td width="50" align="right"><? echo $row[csf('process_loss')]; ?></td>
					<?
					$i++;                               
                }
             	?>
            </tbody>       
        </table>
      </fieldset>
    </div> 
    <?	
    
	exit();
}

if($action=="recv_qty_popup") // Receive From Knitting qty Popup
{
	echo load_html_head_contents("Grey Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$companyID= $companyID;
	$orderID=$fso_id;
	$productIds=$productIds;
	$fabric_construction=$fabric_construction;
	$fab_color_id=$fab_color_id;

	$company_library = return_library_array("select id, company_short_name from lib_company", "id", "company_short_name");
	$supplier_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$count_arr = return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr = return_library_array( "select id, brand_name from lib_brand",'id','brand_name');

	$con = connect();
	execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=82");
	oci_commit($con);

	if ($orderID>0 && $productIds>0 && $fabric_construction>0 && $fab_color_id>0) 
	{
		$recv_sql="SELECT a.recv_number, a.receive_date, a.knitting_company, a.knitting_source, b.prod_id, c.po_breakdown_id, d.febric_description_id, d.width, d.stitch_length, d.yarn_count, d.yarn_lot, e.qnty, e.barcode_no
		from inv_receive_master a, inv_transaction b, order_wise_pro_details c,pro_grey_prod_entry_dtls d, pro_roll_details e 
		where a.id=b.mst_id and b.id=c.trans_id and a.id=d.mst_id and c.dtls_id=d.id and a.id=e.mst_id and d.id=e.dtls_id and c.dtls_id=e.dtls_id and a.entry_form in(2,22,58) and c.trans_id <>0 and c.trans_type=1 and c.entry_form in (2,22,58) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_id=$companyID and c.po_breakdown_id in($orderID) and c.prod_id in($productIds) and d.febric_description_id=$fabric_construction and d.color_id='$fab_color_id' and e.is_sales=1";
		//echo $recv_sql;
		$recv_sqlData=sql_select($recv_sql);
	}
	
	if(empty($recv_sqlData))
	{
		echo get_empty_data_msg();
		die;
	}

	foreach ($recv_sqlData as $key => $row) 
	{
		$barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		if( $barcode_no_check[$row[csf('barcode_no')]] =="" )
        {
            $barcode_no_check[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
            $barcodeno = $row[csf('barcode_no')];
            execute_query("insert into tmp_barcode_no (userid, barcode_no,entry_form) values ($user_id,$barcodeno,82)");
        }
	}
	oci_commit($con);

	if(!empty($barcode_no_arr))
	{
		$production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no,c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, a.brand_id, c.knitting_source, c.challan_no as production_challan, c.knitting_company, a.yarn_prod_id, a.body_part_id, b.coller_cuff_size
		from pro_grey_prod_entry_dtls a, pro_roll_details b, inv_receive_master c, tmp_barcode_no d 
		where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2,22,58) and b.entry_form in(2,22,58)  and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid= $user_id and d.entry_form=82");
        $yarn_prod_id_check=array();$prog_no_check=array();
        foreach ($production_sql as $row)
        {
            $prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"] =$row[csf("receive_basis")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prog_book"] =$row[csf("booking_no")];
            $prodBarcodeData[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
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
            $prodBarcodeData[$row[csf("barcode_no")]]["item_size"] =$row[csf("coller_cuff_size")];
            $prodBarcodeData[$row[csf("barcode_no")]]["machine_no_id"] =$row[csf("machine_no_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prod_challan"] =$row[csf("production_challan")];
            $prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"] =$row[csf("knitting_source")];
            $prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"] =$row[csf("knitting_company")];
            $prodBarcodeData[$row[csf("barcode_no")]]["body_part_id"] =$row[csf("body_part_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["brand_id"] =$row[csf("brand_id")];
            $allDeterArr[$row[csf("febric_description_id")]] =$row[csf("febric_description_id")];
            $allColorArr[$row[csf("color_id")]] =$row[csf("color_id")];
            $allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
        }
	}

	foreach ($recv_sqlData as $key => $row) 
	{
		$machine_dia=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"];
		$yarn_prod_id=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"];
		$brand_id=$prodBarcodeData[$row[csf("barcode_no")]]["brand_id"];

		$recv_data_arr[$row[csf("recv_number")]]["receive_date"] =$row[csf("receive_date")];
		if ($row[csf("knitting_source")]==1) 
		{
			$recv_data_arr[$row[csf("recv_number")]]["knitting_company"] =$company_library[$row[csf("knitting_company")]];
		}
		else
		{
			$recv_data_arr[$row[csf("recv_number")]]["knitting_company"] =$supplier_library[$row[csf("knitting_company")]];
		}
		$recv_data_arr[$row[csf("recv_number")]]["febric_descr_id"] =$row[csf("febric_description_id")];
		$recv_data_arr[$row[csf("recv_number")]]["machine_dia"] .=$machine_dia.',';
		$recv_data_arr[$row[csf("recv_number")]]["fdia"].=$row[csf("width")].',';
		$recv_data_arr[$row[csf("recv_number")]]["stitch_length"] .=$row[csf("stitch_length")].',';
		$recv_data_arr[$row[csf("recv_number")]]["yarn_count"] .=$row[csf("yarn_count")].',';
		$recv_data_arr[$row[csf("recv_number")]]["yarn_lot"] .=$row[csf("yarn_lot")].',';
		$recv_data_arr[$row[csf("recv_number")]]["yarn_prod_id"] .=$yarn_prod_id.',';
		$recv_data_arr[$row[csf("recv_number")]]["brand_id"] .=$brand_id.',';
		$recv_data_arr[$row[csf("recv_number")]]["rcv_qnty"] +=$row[csf("qnty")];
	}

	//composition and constructtion
	$composition_arr=array();
	$constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row) 
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($deter_array);

	execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=82");
	oci_commit($con);
	disconnect($con);
	?>
	<fieldset style="width:<? echo $popup_width;?>; margin-left:3px">
		<div id="report_container">
			<!-- ====================Start========================= -->
			<table border="1" class="rpt_table" rules="all" width="1000" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="12"><b>Receive Popup</b></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="80">Date</th>
                        <th width="120">Chalan Number</th>
                        <th width="80">Factory Name</th>
                        <th width="120">Fab. Composition</th>
                        <th width="80">M/C Dia</th>
                        <th width="80">F/Dia</th>
                        <th width="80">Stich Length</th>
                        <th width="80">Y. Count</th>
                        <th width="80">Y. Brand</th>
                        <th width="80">Y. Lot</th>
                        <th>Recv. Qty.</th>
                    </tr>
				</thead>
            </table>
            <div style="width:1020px; max-height:410px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="1000" cellpadding="0" cellspacing="0" id="table_body">
                    <?
					$i=1;
					foreach ($recv_data_arr as $recv_number => $row) 
					{
						$yarn_counts_arr = array_unique(array_filter(explode(",", chop($row['yarn_count'],","))));
                        $yarn_counts="";
                        foreach ($yarn_counts_arr as $count) {
                            $yarn_counts .= $count_arr[$count] . ",";
                        }
                        // $yarn_counts = rtrim($yarn_counts, ", ");
                        $yarn_counts =implode(",",array_filter(array_unique(explode(",", $yarn_counts))));

                        $brand_id_id_arr = array_unique(array_filter(explode(",", chop($row['brand_id'],","))));
                        $yarn_brand = "";
                        foreach ($brand_id_id_arr as $bid)
                        {
                            $yarn_brand .= ($yarn_brand =="") ? $brand_arr[$bid] :  ",". $brand_arr[$bid];
                        }
                        $yarn_brand =implode(",",array_filter(array_unique(explode(",", $yarn_brand))));
                        $stitch_length =implode(",",array_filter(array_unique(explode(",", chop($row['stitch_length'],",")))));
                        $fdia =implode(",",array_filter(array_unique(explode(",", chop($row['fdia'],",")))));
                        $yarn_lot =implode(",",array_filter(array_unique(explode(",", chop($row['yarn_lot'],",")))));
                        $machine_dia =implode(",",array_filter(array_unique(explode(",", chop($row['machine_dia'],",")))));
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                        <td width="30"><? echo $i; ?></td>
	                        <td width="80"  align="center"><? echo change_date_format($row['receive_date']); ?></td>
	                        <td width="120"><p><? echo $recv_number; ?></p></td>
	                        <td width="80"><div style="word-break:break-all"><p><? echo $row['knitting_company']; ?></p></div></td>
	                        <td width="120"><div style="word-break:break-all"><? echo $composition_arr[$row['febric_descr_id']]; ?></div></td>
	                        <td width="80" align="center"><div style="word-break:break-all"><? echo $machine_dia; ?></div></td>
	                        <td width="80" align="center"><div style="word-break:break-all"><? echo $fdia; ?></div></td>
	                        <td width="80" align="center"><div style="word-break:break-all"><? echo $stitch_length; ?></div></td>
	                        <td width="80" align="center"><div style="word-break:break-all"><? echo $yarn_counts; ?></div></td>
	                        <td width="80" align="center"><p><? echo $yarn_brand; ?></p>&nbsp;</td>
	                        <td width="80" align="center"><p><? echo $yarn_lot; ?></p>&nbsp;</td>
	                        <td align="right"><? echo $row['rcv_qnty']; ?>&nbsp;</td>
	                    </tr>
	                    <?
	                    $total_recv_qty+=$row['rcv_qnty'];
	                    $i++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="11" align="right">Sub Total</th>
                            <th align="right"><? echo number_format($total_recv_qty,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
			</div>
			<!-- =====================End========================== -->	
		</div>
	</fieldset>	
	<? 
	exit();
}

if($action=="transferIn_qty_popup") // Transfer inqty poup
{
	echo load_html_head_contents("Transfer In Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$companyID= $companyID;
	$orderID=$fso_id;
	$productIds=$productIds;
	$fabric_construction=$fabric_construction;
	$fab_color_id=$fab_color_id;
	// echo $fab_color_id;die;
	$count_arr = return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr = return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$color_arr = return_library_array( 'SELECT id, color_name FROM lib_color', 'id', 'color_name');	

	$con = connect();
	execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=82");
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=82");
	oci_commit($con);

	$transfer_in_sql="SELECT a.transfer_system_id as transfer_number,a.transfer_date, a.to_color_id, a.from_order_id as order_id, a.to_order_id,c.po_breakdown_id, d.qnty, d.barcode_no
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c,pro_roll_details d
	where a.id=b.mst_id and b.to_trans_id=c.trans_id and b.id=d.dtls_id and a.id=d.mst_id and a.entry_form in(133) AND d.entry_form IN (133) and c.trans_id <>0 and c.entry_form in (133) and c.trans_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id=$companyID and c.prod_id in($productIds) and c.po_breakdown_id in($orderID) and d.is_sales=1";
	// echo $transfer_in_sql;
	$transfer_in_sql_data=sql_select($transfer_in_sql);
	if(empty($transfer_in_sql_data))
	{
		echo get_empty_data_msg();
		die;
	}
	foreach ($transfer_in_sql_data as $key => $row) 
	{
		$barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		if( $barcode_no_check[$row[csf('barcode_no')]] =="" )
        {
            $barcode_no_check[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
            $barcodeno = $row[csf('barcode_no')];
            execute_query("insert into tmp_barcode_no (userid, barcode_no,entry_form) values ($user_id,$barcodeno,82)");
        }
        $from_order_idArr[$row[csf('order_id')]] = $row[csf('order_id')];
	}
	oci_commit($con);

	if(!empty($barcode_no_arr))
	{
		$production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no,c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, a.brand_id, c.knitting_source, c.challan_no as production_challan, c.knitting_company, a.yarn_prod_id, a.body_part_id, b.coller_cuff_size
		from pro_grey_prod_entry_dtls a, pro_roll_details b, inv_receive_master c, tmp_barcode_no d 
		where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2,22,58) and b.entry_form in(2,22,58)  and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid= $user_id and d.entry_form=82");
        $yarn_prod_id_check=array();$prog_no_check=array();
        foreach ($production_sql as $row)
        {
            $prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"] =$row[csf("receive_basis")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prog_book"] =$row[csf("booking_no")];
            $prodBarcodeData[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
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
            $prodBarcodeData[$row[csf("barcode_no")]]["item_size"] =$row[csf("coller_cuff_size")];
            $prodBarcodeData[$row[csf("barcode_no")]]["machine_no_id"] =$row[csf("machine_no_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prod_challan"] =$row[csf("production_challan")];
            $prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"] =$row[csf("knitting_source")];
            $prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"] =$row[csf("knitting_company")];
            $prodBarcodeData[$row[csf("barcode_no")]]["body_part_id"] =$row[csf("body_part_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["brand_id"] =$row[csf("brand_id")];
            $allDeterArr[$row[csf("febric_description_id")]] =$row[csf("febric_description_id")];
            $allColorArr[$row[csf("color_id")]] =$row[csf("color_id")];
            $allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
        }
	}

	// To IR/IB
	if(!empty($from_order_idArr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 82, 2,$from_order_idArr, $empty_arr);
		oci_commit($con);

		$int_ref_sql="SELECT c.id as fsoId, a.id, a.po_number, a.grouping, b.booking_no, b.booking_mst_id 
		from GBL_TEMP_ENGINE g, fabric_sales_order_mst c, wo_booking_dtls b, wo_po_break_down a 
		where g.ref_val=c.id and g.user_id=$user_id and g.entry_form=82 and g.ref_from=2 and c.BOOKING_ID=b.BOOKING_MST_ID and b.po_break_down_id=a.id and c.within_group=1 and b.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		// echo $int_ref_sql;die;
		$int_ref_sql_result=sql_select($int_ref_sql);
		$int_ref_arr=array();
		foreach ($int_ref_sql_result as $key => $row) 
		{
			$int_ref_arr[$row[csf('fsoId')]] = $row[csf('grouping')];
		}
	}
	// echo "<pre>";print_r($int_ref_arr);die;

	foreach ($transfer_in_sql_data as $key => $row) 
	{
		$machine_dia=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"];
		$yarn_prod_id=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"];
		$brand_id=$prodBarcodeData[$row[csf("barcode_no")]]["brand_id"];
		$width=$prodBarcodeData[$row[csf("barcode_no")]]["width"];
		$stitch_length=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"];
		$yarn_count=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"];
		$yarn_lot=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"];
		$febric_description_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];
		$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];
		$int_ref=$int_ref_arr[$row[csf('order_id')]];
		// echo $color_id.'=='.$fab_color_id.'<br>';
		if ($color_id==$fab_color_id) 
		{
			$transfer_in_data_arr[$row[csf("transfer_number")]]["transfer_date"] =$row[csf("transfer_date")];
			$transfer_in_data_arr[$row[csf("transfer_number")]]["int_ref"] =$int_ref;
			$transfer_in_data_arr[$row[csf("transfer_number")]]["to_color_id"] =$row[csf("to_color_id")];
			$transfer_in_data_arr[$row[csf("transfer_number")]]["febric_descr_id"] =$febric_description_id;
			$transfer_in_data_arr[$row[csf("transfer_number")]]["machine_dia"] .=$machine_dia.',';
			$transfer_in_data_arr[$row[csf("transfer_number")]]["fdia"].=$width.',';
			$transfer_in_data_arr[$row[csf("transfer_number")]]["stitch_length"] .=$stitch_length.',';
			$transfer_in_data_arr[$row[csf("transfer_number")]]["yarn_count"] .=$yarn_count.',';
			$transfer_in_data_arr[$row[csf("transfer_number")]]["yarn_lot"] .=$yarn_lot.',';
			$transfer_in_data_arr[$row[csf("transfer_number")]]["yarn_prod_id"] .=$yarn_prod_id.',';
			$transfer_in_data_arr[$row[csf("transfer_number")]]["brand_id"] .=$brand_id.',';
			$transfer_in_data_arr[$row[csf("transfer_number")]]["trans_in_qnty"] +=$row[csf("qnty")];
		}
	}

	//composition and constructtion
	$composition_arr=array();
	$constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row) 
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($deter_array);

	execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=82");
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=82");
	oci_commit($con);
	disconnect($con);
	?>
	<fieldset style="width:<? echo $popup_width;?>; margin-left:3px">
		<div id="report_container">
			<!-- ====================Start========================= -->
			<table border="1" class="rpt_table" rules="all" width="1000" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="13"><b>Transfer In</b></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="70">Date</th>
                        <th width="120">Transfer Id</th>
                        <th width="100">To IR/IB</th>
                        <th width="80">To Colour</th>
                        <th width="120">Fab. Composition</th>
                        <th width="40">M/C Dia</th>
                        <th width="40">F/Dia</th>
                        <th width="80">Stich Length</th>
                        <th width="80">Y. Count</th>
                        <th width="80">Y. Brand</th>
                        <th width="80">Y. Lot</th>
                        <th>Transfer In Qty</th>
                    </tr>
				</thead>
            </table>
            <div style="width:1020px; max-height:410px; overflow-y:scroll" id="scroll_body3">
                <table border="1" class="rpt_table" rules="all" width="1000" cellpadding="0" cellspacing="0" id="table_body3">
                    <?					
					$iii=1;
					foreach ($transfer_in_data_arr as $transfer_number => $row) 
					{
						$yarn_counts_arr = array_unique(array_filter(explode(",", chop($row['yarn_count'],","))));
                        $yarn_counts="";
                        foreach ($yarn_counts_arr as $count) {
                            $yarn_counts .= $count_arr[$count] . ",";
                        }
                        // $yarn_counts = rtrim($yarn_counts, ", ");
                        $yarn_counts =implode(",",array_filter(array_unique(explode(",", $yarn_counts))));

                        $brand_id_id_arr = array_unique(array_filter(explode(",", chop($row['brand_id'],","))));
                        $yarn_brand = "";
                        foreach ($brand_id_id_arr as $bid)
                        {
                            $yarn_brand .= ($yarn_brand =="") ? $brand_arr[$bid] :  ",". $brand_arr[$bid];
                        }
                        $yarn_brand =implode(",",array_filter(array_unique(explode(",", $yarn_brand))));
                        $stitch_length =implode(",",array_filter(array_unique(explode(",", chop($row['stitch_length'],",")))));
                        $fdia =implode(",",array_filter(array_unique(explode(",", chop($row['fdia'],",")))));
                        $yarn_lot =implode(",",array_filter(array_unique(explode(",", chop($row['yarn_lot'],",")))));
                        $machine_dia =implode(",",array_filter(array_unique(explode(",", chop($row['machine_dia'],",")))));
                    	?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $iii; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $iii;?>">
                            <td width="30"><? echo $iii; ?></td>
                            <td width="70"  align="center"><? echo change_date_format($row['transfer_date']);// ?></td>
                            <td width="120"><p><? echo $transfer_number; ?></p></td>
                            <td width="100"><div style="word-break:break-all"><p><? echo $row["int_ref"]; ?></p></div></td>
                            <td width="80" align="center"><? echo $color_arr[$row['to_color_id']]; ?>&nbsp;</td>
							<td width="120"><div style="word-break:break-all"><? echo $composition_arr[$row['febric_descr_id']]; ?></div></td>
	                        <td width="40" align="center"><div style="word-break:break-all"><? echo $machine_dia; ?></div></td>
	                        <td width="40" align="center"><div style="word-break:break-all"><? echo $fdia; ?></div></td>
	                        <td width="80" align="center"><div style="word-break:break-all"><? echo $stitch_length; ?></div></td>
	                        <td width="80" align="center"><div style="word-break:break-all"><? echo $yarn_counts; ?></div></td>
	                        <td width="80" align="center"><p><? echo $yarn_brand; ?></p>&nbsp;</td>
	                        <td width="80" align="center"><p><? echo $yarn_lot; ?></p>&nbsp;</td>
	                        <td align="right"><? echo $row['trans_in_qnty']; ?>&nbsp;</td>
                        </tr>
	                    <?
	                    $total_trnsf_qty+=$row['trans_in_qnty'];
	                    $iii++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="12" align="right">Total</th>
                            <th align="right"><? echo number_format($total_trnsf_qty,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
			</div>
			<!-- ====================End=========================== -->
			<?//die('die for check');?>			
		</div>
	</fieldset>	
	<? 
	exit();
}

if($action=="transferOut_qty_popup") // Transfer outqty poup
{
	echo load_html_head_contents("Transfer In Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$companyID= $companyID;
	$orderID=$fso_id;
	$productIds=$productIds;
	$fabric_construction=$fabric_construction;
	$fab_color_id=$fab_color_id;
	// echo $fab_color_id;die;
	$count_arr = return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr = return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$color_arr = return_library_array( 'SELECT id, color_name FROM lib_color', 'id', 'color_name');	

	$con = connect();
	execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=82");
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=82");
	oci_commit($con);

	$transfer_out_sql="SELECT a.transfer_system_id as transfer_number,a.transfer_date, a.to_color_id, a.from_order_id as order_id, a.to_order_id,c.po_breakdown_id, d.qnty, d.barcode_no
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c,pro_roll_details d
	where a.id=b.mst_id and b.trans_id=c.trans_id and b.id=d.dtls_id and a.id=d.mst_id and a.entry_form in(133) AND d.entry_form IN (133) and c.trans_id <>0 and c.entry_form in (133) and c.trans_type=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id=$companyID and c.prod_id in($productIds) and c.po_breakdown_id in($orderID) and d.is_sales=1";
	// echo $transfer_out_sql;die;

	$transfer_out_sql_data=sql_select($transfer_out_sql);
	if(empty($transfer_out_sql_data))
	{
		echo get_empty_data_msg();
		die;
	}
	foreach ($transfer_out_sql_data as $key => $row) 
	{
		$barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		if( $barcode_no_check[$row[csf('barcode_no')]] =="" )
        {
            $barcode_no_check[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
            $barcodeno = $row[csf('barcode_no')];
            execute_query("insert into tmp_barcode_no (userid, barcode_no,entry_form) values ($user_id,$barcodeno,82)");
        }
        $to_order_idArr[$row[csf('order_id')]] = $row[csf('order_id')];
        $to_order_idArr[$row[csf('to_order_id')]] = $row[csf('to_order_id')];
	}
	oci_commit($con);
	// echo "<pre>";print_r($to_order_idArr);die;

	if(!empty($barcode_no_arr))
	{
		$production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no,c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, a.brand_id, c.knitting_source, c.challan_no as production_challan, c.knitting_company, a.yarn_prod_id, a.body_part_id, b.coller_cuff_size
		from pro_grey_prod_entry_dtls a, pro_roll_details b, inv_receive_master c, tmp_barcode_no d 
		where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2,22,58) and b.entry_form in(2,22,58)  and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid= $user_id and d.entry_form=82");
        $yarn_prod_id_check=array();$prog_no_check=array();
        foreach ($production_sql as $row)
        {
            $prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"] =$row[csf("receive_basis")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prog_book"] =$row[csf("booking_no")];
            $prodBarcodeData[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
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
            $prodBarcodeData[$row[csf("barcode_no")]]["item_size"] =$row[csf("coller_cuff_size")];
            $prodBarcodeData[$row[csf("barcode_no")]]["machine_no_id"] =$row[csf("machine_no_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prod_challan"] =$row[csf("production_challan")];
            $prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"] =$row[csf("knitting_source")];
            $prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"] =$row[csf("knitting_company")];
            $prodBarcodeData[$row[csf("barcode_no")]]["body_part_id"] =$row[csf("body_part_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["brand_id"] =$row[csf("brand_id")];
            $allDeterArr[$row[csf("febric_description_id")]] =$row[csf("febric_description_id")];
            $allColorArr[$row[csf("color_id")]] =$row[csf("color_id")];
            $allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
        }
	}

	// To IR/IB
	if(!empty($to_order_idArr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 82, 2,$to_order_idArr, $empty_arr);
		oci_commit($con);

		$int_ref_sql="SELECT c.id as fsoId, a.id, a.po_number, a.grouping, b.booking_no, b.booking_mst_id 
		from GBL_TEMP_ENGINE g, fabric_sales_order_mst c, wo_booking_dtls b, wo_po_break_down a 
		where g.ref_val=c.id and g.user_id=$user_id and g.entry_form=82 and g.ref_from=2 and c.BOOKING_ID=b.BOOKING_MST_ID and b.po_break_down_id=a.id and c.within_group=1 and b.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		// echo $int_ref_sql;die;
		$int_ref_sql_result=sql_select($int_ref_sql);
		$int_ref_arr=array();
		foreach ($int_ref_sql_result as $key => $row) 
		{
			$int_ref_arr[$row[csf('fsoId')]] = $row[csf('grouping')];
		}
	}
	// echo "<pre>";print_r($booking_idArr);die;

	foreach ($transfer_out_sql_data as $key => $row) 
	{
		$machine_dia=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"];
		$yarn_prod_id=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"];
		$brand_id=$prodBarcodeData[$row[csf("barcode_no")]]["brand_id"];
		$width=$prodBarcodeData[$row[csf("barcode_no")]]["width"];
		$stitch_length=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"];
		$yarn_count=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"];
		$yarn_lot=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"];
		$febric_description_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];
		$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];
		$from_int_ref=$int_ref_arr[$row[csf('order_id')]];
		$to_int_ref=$int_ref_arr[$row[csf('to_order_id')]];
		// echo $color_id.'=='.$fab_color_id.'<br>';
		if ($color_id==$fab_color_id) 
		{
			$transfer_out_data_arr[$row[csf("transfer_number")]]["transfer_date"] =$row[csf("transfer_date")];
			$transfer_out_data_arr[$row[csf("transfer_number")]]["from_int_ref"] =$from_int_ref;
			$transfer_out_data_arr[$row[csf("transfer_number")]]["to_int_ref"] =$to_int_ref;
			$transfer_out_data_arr[$row[csf("transfer_number")]]["to_color_id"] =$row[csf("to_color_id")];
			$transfer_out_data_arr[$row[csf("transfer_number")]]["febric_descr_id"] =$febric_description_id;
			$transfer_out_data_arr[$row[csf("transfer_number")]]["machine_dia"] .=$machine_dia.',';
			$transfer_out_data_arr[$row[csf("transfer_number")]]["fdia"].=$width.',';
			$transfer_out_data_arr[$row[csf("transfer_number")]]["stitch_length"] .=$stitch_length.',';
			$transfer_out_data_arr[$row[csf("transfer_number")]]["yarn_count"] .=$yarn_count.',';
			$transfer_out_data_arr[$row[csf("transfer_number")]]["yarn_lot"] .=$yarn_lot.',';
			$transfer_out_data_arr[$row[csf("transfer_number")]]["yarn_prod_id"] .=$yarn_prod_id.',';
			$transfer_out_data_arr[$row[csf("transfer_number")]]["brand_id"] .=$brand_id.',';
			$transfer_out_data_arr[$row[csf("transfer_number")]]["trans_out_qnty"] +=$row[csf("qnty")];
		}
	}

	//composition and constructtion
	$composition_arr=array();
	$constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row) 
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($deter_array);

	execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=82");
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=82");
	oci_commit($con);
	disconnect($con);
	?>
	<fieldset style="width:<? echo $popup_width;?>; margin-left:3px">
		<div id="report_container">
			<!-- ====================Start========================= -->
			<table border="1" class="rpt_table" rules="all" width="1180" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="15"><b>Transfer Out</b></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="70">Date</th>
                        <th width="120">Transfer Id</th>
                        <th width="100">From IR/IB</th>
                        <th width="100">To IR/IB</th>
                        <th width="80">From Colour</th>
                        <th width="80">To Colour</th>
                        <th width="120">Fab. Composition</th>
                        <th width="40">M/C Dia</th>
                        <th width="40">F/Dia</th>
                        <th width="80">Stich Length</th>
                        <th width="80">Y. Count</th>
                        <th width="80">Y. Brand</th>
                        <th width="80">Y. Lot</th>
                        <th>Transfer In Qty</th>
                    </tr>
				</thead>
            </table>
            <div style="width:1200px; max-height:410px; overflow-y:scroll" id="scroll_body3">
                <table border="1" class="rpt_table" rules="all" width="1180" cellpadding="0" cellspacing="0" id="table_body3">
                    <?					
					$iii=1;
					foreach ($transfer_out_data_arr as $transfer_number => $row) 
					{
						$yarn_counts_arr = array_unique(array_filter(explode(",", chop($row['yarn_count'],","))));
                        $yarn_counts="";
                        foreach ($yarn_counts_arr as $count) {
                            $yarn_counts .= $count_arr[$count] . ",";
                        }
                        // $yarn_counts = rtrim($yarn_counts, ", ");
                        $yarn_counts =implode(",",array_filter(array_unique(explode(",", $yarn_counts))));

                        $brand_id_id_arr = array_unique(array_filter(explode(",", chop($row['brand_id'],","))));
                        $yarn_brand = "";
                        foreach ($brand_id_id_arr as $bid)
                        {
                            $yarn_brand .= ($yarn_brand =="") ? $brand_arr[$bid] :  ",". $brand_arr[$bid];
                        }
                        $yarn_brand =implode(",",array_filter(array_unique(explode(",", $yarn_brand))));
                        $stitch_length =implode(",",array_filter(array_unique(explode(",", chop($row['stitch_length'],",")))));
                        $fdia =implode(",",array_filter(array_unique(explode(",", chop($row['fdia'],",")))));
                        $yarn_lot =implode(",",array_filter(array_unique(explode(",", chop($row['yarn_lot'],",")))));
                        $machine_dia =implode(",",array_filter(array_unique(explode(",", chop($row['machine_dia'],",")))));
                    	?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $iii; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $iii;?>">
                            <td width="30"><? echo $iii; ?></td>
                            <td width="70"  align="center"><? echo change_date_format($row['transfer_date']);// ?></td>
                            <td width="120"><p><? echo $transfer_number; ?></p></td>
                            <td width="100"><div style="word-break:break-all"><p><? echo $row["from_int_ref"]; ?></p></div></td>
                            <td width="100"><div style="word-break:break-all"><p><? echo $row["to_int_ref"]; ?></p></div></td>
                            <td width="80" align="center"><? echo $color_arr[$fab_color_id]; ?>&nbsp;</td>
                            <td width="80" align="center"><? echo $color_arr[$row['to_color_id']]; ?>&nbsp;</td>
							<td width="120"><div style="word-break:break-all"><? echo $composition_arr[$row['febric_descr_id']]; ?></div></td>
	                        <td width="40" align="center"><div style="word-break:break-all"><? echo $machine_dia; ?></div></td>
	                        <td width="40" align="center"><div style="word-break:break-all"><? echo $fdia; ?></div></td>
	                        <td width="80" align="center"><div style="word-break:break-all"><? echo $stitch_length; ?></div></td>
	                        <td width="80" align="center"><div style="word-break:break-all"><? echo $yarn_counts; ?></div></td>
	                        <td width="80" align="center"><p><? echo $yarn_brand; ?></p>&nbsp;</td>
	                        <td width="80" align="center"><p><? echo $yarn_lot; ?></p>&nbsp;</td>
	                        <td align="right"><? echo $row['trans_out_qnty']; ?>&nbsp;</td>
                        </tr>
	                    <?
	                    $total_trnsf_qty+=$row['trans_out_qnty'];
	                    $iii++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="14" align="right">Total</th>
                            <th align="right"><? echo number_format($total_trnsf_qty,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
			</div>
			<!-- ====================End=========================== -->
			<?//die('die for check');?>			
		</div>
	</fieldset>	
	<? 
	exit();
}

if($action=="totalRcv_qty_popup") // Total Receiveqty poup
{
	echo load_html_head_contents("Transfer In Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$companyID= $companyID;
	$orderID=$fso_id;
	$productIds=$productIds;
	$fabric_construction=$fabric_construction;
	$fab_color_id=$fab_color_id;
	// echo $fab_color_id;die;
	$count_arr = return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr = return_library_array( "select id, brand_name from lib_brand",'id','brand_name');	

	$con = connect();
	execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=82");
	oci_commit($con);

	// Recv sql
	$recv_sql="SELECT a.recv_number, a.receive_date, a.knitting_company, a.knitting_source, b.prod_id, c.po_breakdown_id, d.febric_description_id, d.width, d.stitch_length, d.yarn_count, d.yarn_lot, e.qnty, e.barcode_no
	from inv_receive_master a, inv_transaction b, order_wise_pro_details c,pro_grey_prod_entry_dtls d, pro_roll_details e 
	where a.id=b.mst_id and b.id=c.trans_id and a.id=d.mst_id and c.dtls_id=d.id and a.id=e.mst_id and d.id=e.dtls_id and c.dtls_id=e.dtls_id and a.entry_form in(2,22,58) and c.trans_id <>0 and c.trans_type=1 and c.entry_form in (2,22,58) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_id=$companyID and c.po_breakdown_id in($orderID) and c.prod_id in($productIds) and d.febric_description_id=$fabric_construction and d.color_id=$fab_color_id and e.is_sales=1";
	// echo $recv_sql;
	$recv_sqlData=sql_select($recv_sql);
	foreach ($recv_sqlData as $key => $row) 
	{
		$barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}
	// echo "<pre>";print_r($barcode_no_arr);die;

	// transfer in Sql
	$transfer_in_sql="SELECT a.transfer_system_id as transfer_number,a.transfer_date, a.to_color_id, a.from_order_id as order_id, a.to_order_id,c.po_breakdown_id, d.qnty, d.barcode_no
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c,pro_roll_details d
	where a.id=b.mst_id and b.to_trans_id=c.trans_id and b.id=d.dtls_id and a.id=d.mst_id and a.entry_form in(133) AND d.entry_form IN (133) and c.trans_id <>0 and c.entry_form in (133) and c.trans_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id=$companyID and c.prod_id in($productIds) and c.po_breakdown_id in($orderID) and d.is_sales=1";
	// echo $transfer_in_sql;
	$transfer_in_sql_data=sql_select($transfer_in_sql);
	foreach ($transfer_in_sql_data as $key => $row) 
	{
		$barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}
	// echo "<pre>";print_r($barcode_no_arr);die;

	// Transfer Out Sql
	$transfer_out_sql="SELECT a.transfer_system_id as transfer_number,a.transfer_date, a.to_color_id, a.from_order_id as order_id, a.to_order_id,c.po_breakdown_id, d.qnty, d.barcode_no
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c,pro_roll_details d
	where a.id=b.mst_id and b.trans_id=c.trans_id and b.id=d.dtls_id and a.id=d.mst_id and a.entry_form in(133) AND d.entry_form IN (133) and c.trans_id <>0 and c.entry_form in (133) and c.trans_type=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id=$companyID and c.prod_id in($productIds) and c.po_breakdown_id in($orderID) and d.is_sales=1";
	// echo $transfer_out_sql;die;
	$transfer_out_sql_data=sql_select($transfer_out_sql);
	foreach ($transfer_out_sql_data as $key => $row) 
	{
		$barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}

	// Production Sql
	if(!empty($barcode_no_arr))
	{
		foreach($barcode_no_arr as $barcode_no)
		{
			if( $barcode_no_check[$barcode_no] =="" )
	        {
	            $barcode_no_check[$barcode_no]=$barcode_no;
	            $barcodeno = $barcode_no;
	            execute_query("insert into tmp_barcode_no (userid, barcode_no,entry_form) values ($user_id,$barcodeno,82)");
	        }			
		}
		oci_commit($con);

		$production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no,c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, a.brand_id, c.knitting_source, c.challan_no as production_challan, c.knitting_company, a.yarn_prod_id, a.body_part_id, b.coller_cuff_size
		from pro_grey_prod_entry_dtls a, pro_roll_details b, inv_receive_master c, tmp_barcode_no d 
		where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2,22,58) and b.entry_form in(2,22,58)  and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid= $user_id and d.entry_form=82");
        $yarn_prod_id_check=array();$prog_no_check=array();
        foreach ($production_sql as $row)
        {
            $prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"] =$row[csf("receive_basis")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prog_book"] =$row[csf("booking_no")];
            $prodBarcodeData[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
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
            $prodBarcodeData[$row[csf("barcode_no")]]["item_size"] =$row[csf("coller_cuff_size")];
            $prodBarcodeData[$row[csf("barcode_no")]]["machine_no_id"] =$row[csf("machine_no_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prod_challan"] =$row[csf("production_challan")];
            $prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"] =$row[csf("knitting_source")];
            $prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"] =$row[csf("knitting_company")];
            $prodBarcodeData[$row[csf("barcode_no")]]["body_part_id"] =$row[csf("body_part_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["brand_id"] =$row[csf("brand_id")];
            $allDeterArr[$row[csf("febric_description_id")]] =$row[csf("febric_description_id")];
            $allColorArr[$row[csf("color_id")]] =$row[csf("color_id")];
            $allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
        }
	}

	// Receive data array
	$recv_data_arr=array();
	foreach ($recv_sqlData as $key => $row) 
	{
		$machine_dia=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"];
		$width=$prodBarcodeData[$row[csf("barcode_no")]]["width"];
		$stitch_length=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"];
		$yarn_count=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"];
		$brand_id=$prodBarcodeData[$row[csf("barcode_no")]]["brand_id"];
		$yarn_lot=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"];			
		$deter_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];
		$knit_source=$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"];
		$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];

		$str_ref=$machine_dia.'*'.$width.'*'.$stitch_length.'*'.$yarn_count.'*'.$brand_id.'*'.$yarn_lot;
		$recv_data_arr[$deter_id][$str_ref]["rcv_qnty"] +=$row[csf("qnty")];
	}

	// transfer in data array
	// echo "<pre>"; print_r($transfer_in_sql_data);die;
	foreach ($transfer_in_sql_data as $key => $row) 
	{
		$machine_dia=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"];
		$width=$prodBarcodeData[$row[csf("barcode_no")]]["width"];
		$stitch_length=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"];
		$yarn_count=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"];
		$brand_id=$prodBarcodeData[$row[csf("barcode_no")]]["brand_id"];
		$yarn_lot=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"];			
		$deter_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];
		$knit_source=$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"];
		$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];

		$int_ref=$int_ref_arr[$row[csf('to_order_id')]];
		// echo $febric_descr_id.'<br>';
		$str_ref1=$machine_dia.'*'.$width.'*'.$stitch_length.'*'.$yarn_count.'*'.$brand_id.'*'.$yarn_lot;
		// echo $color_id.'=='.$fab_color_id.'<br>';
		if ($color_id==$fab_color_id) 
		{
			$recv_data_arr[$deter_id][$str_ref1]["trans_in_qnty"] +=$row[csf("qnty")];			
		}
	}
	// echo "<pre>";print_r($recv_data_arr);

	// Transfer Out data array
	foreach ($transfer_out_sql_data as $key => $row) 
	{
		$machine_dia=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"];
		$width=$prodBarcodeData[$row[csf("barcode_no")]]["width"];
		$stitch_length=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"];
		$yarn_count=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"];
		$brand_id=$prodBarcodeData[$row[csf("barcode_no")]]["brand_id"];
		$yarn_lot=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"];			
		$deter_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];
		$knit_source=$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"];
		$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];
		// echo $color_id.'=='.$fab_color_id.'<br>';

		$str_ref2=$machine_dia.'*'.$width.'*'.$stitch_length.'*'.$yarn_count.'*'.$brand_id.'*'.$yarn_lot;
		if ($color_id==$fab_color_id) 
		{
			$transfer_out_data_arr[$deter_id][$str_ref2]["trans_out_qnty"] +=$row[csf("qnty")];
		}
	}
	// echo "<pre>";print_r($transfer_out_data_arr);die;

	//composition and constructtion
	$composition_arr=array();
	$constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row) 
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($deter_array);

	execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=82");
	oci_commit($con);
	disconnect($con);
	echo $popup_width;
	?>
	<fieldset style="width:<? echo $popup_width;?>; margin-left:3px">
		<div id="report_container">
			<!-- ====================Start========================= -->
			<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="9"><b>Total Receive</b></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="120">Fab. Composition</th>
                        <th width="40">M/C Dia</th>
                        <th width="40">F/Dia</th>
                        <th width="80">Stich Length</th>
                        <th width="80">Y. Count</th>
                        <th width="80">Y. Brand</th>
                        <th width="80">Y. Lot</th>
                        <th>Total Receive Qty</th>
                    </tr>
				</thead>
            </table>
            <div style="width:650px; max-height:410px; overflow-y:scroll" id="scroll_body3">
                <table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="table_body3">
                    <?					
					$iii=1;
					foreach ($recv_data_arr as $febric_descr_id => $febric_descr_idv) 
					{
						foreach ($febric_descr_idv as $strRef => $row) 
						{
							$strdata=explode("*", $strRef);
							$machineDia=$strdata[0];
							$findia=$strdata[1];
							$stitchLength=$strdata[2];
							$yarn_count=$strdata[3];
							$brand_id=$strdata[4];
							$yarnLot=$strdata[5];

							$yarn_counts_arr = array_unique(array_filter(explode(",", chop($yarn_count,","))));
	                        $yarn_counts="";
	                        foreach ($yarn_counts_arr as $count) {
	                            $yarn_counts .= $count_arr[$count] . ",";
	                        }
	                        // $yarn_counts = rtrim($yarn_counts, ", ");
	                        $yarn_counts =implode(",",array_filter(array_unique(explode(",", $yarn_counts))));

	                        $brand_id_id_arr = array_unique(array_filter(explode(",", chop($brand_id,","))));
	                        $yarn_brand = "";
	                        foreach ($brand_id_id_arr as $bid)
	                        {
	                            $yarn_brand .= ($yarn_brand =="") ? $brand_arr[$bid] :  ",". $brand_arr[$bid];
	                        }
	                        $yarn_brand =implode(",",array_filter(array_unique(explode(",", $yarn_brand))));
	                        $stitch_length =implode(",",array_filter(array_unique(explode(",", chop($stitchLength,",")))));
	                        $fdia =implode(",",array_filter(array_unique(explode(",", chop($findia,",")))));
	                        $yarn_lot =implode(",",array_filter(array_unique(explode(",", chop($yarnLot,",")))));
	                        $machine_dia =implode(",",array_filter(array_unique(explode(",", chop($machineDia,",")))));
	                        $transfer_out_qty=$transfer_out_data_arr[$febric_descr_id][$strRef]["trans_out_qnty"];
	                        $total_rcv=($row['rcv_qnty']+$row['trans_in_qnty'])-$transfer_out_qty;
	                    	?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $iii; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $iii;?>">
	                            <td width="30"><? echo $iii; ?></td>
								<td width="120"><div style="word-break:break-all"><? echo $composition_arr[$febric_descr_id]; ?></div></td>
		                        <td width="40" align="center"><div style="word-break:break-all"><? echo $machine_dia; ?></div></td>
		                        <td width="40" align="center"><div style="word-break:break-all"><? echo $fdia; ?></div></td>
		                        <td width="80" align="center"><div style="word-break:break-all"><? echo $stitch_length; ?></div></td>
		                        <td width="80" align="center"><div style="word-break:break-all"><? echo $yarn_counts; ?></div></td>
		                        <td width="80" align="center"><p><? echo $yarn_brand; ?></p>&nbsp;</td>
		                        <td width="80" align="center"><p><? echo $yarn_lot; ?></p>&nbsp;</td>
		                        <td align="right"><? echo $total_rcv; ?>&nbsp;</td>
	                        </tr>
		                    <?
		                    $total_trnsf_qty+=$total_rcv;
		                    $iii++;
		                }
		            }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="8" align="right">Total</th>
                            <th align="right"><? echo number_format($total_trnsf_qty,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
			</div>
			<!-- ====================End=========================== -->
			<?//die('die for check');?>			
		</div>
	</fieldset>	
	<? 
	exit();
}

if($action=="no_of_roll_recv_popup")
{
	echo load_html_head_contents("Grey Receive Info", "../../../../", 1, 1,'','','');
	// echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$companyID= $companyID;
	$orderID=$fso_id;
	$product_ids=$productIds;
	$fabric_construction=$fabric_construction;
	$fab_color_id=$fab_color_id;
	$type=$type;

	$store_name_arr = return_library_array("select id, store_name from  lib_store_location", 'id', 'store_name');
	$floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where status_active =1 and is_deleted=0","floor_room_rack_id","floor_room_rack_name");

	$con = connect();
	execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=82");
	oci_commit($con);

	?>
	<fieldset style="width:<? echo $popup_width;?>; margin-left:3px">
		<div id="report_container">
			<!-- ====================Receive Issue Start========================= -->
			<table border="1" class="rpt_table" rules="all" width="900" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                		<?
                		if ($type==1) {$tbl_title='Number Of Roll Receive Popup';}else{$tbl_title='Number Of Roll Issue Popup';}
                		?>
                		<th colspan="11"><b><? echo $tbl_title; ?></b></th>
                	</tr>
                	<tr>
                		<?
                		if ($type==1) {$tbl_title='Receive';}else{$tbl_title='Issue';}
                		?>
                        <th colspan="11"><b><? echo $tbl_title; ?></b></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="120">Transaction Reff</th>
                        <th width="80">Transaction Date</th>
                        <th width="80">Barcode No.</th>
                        <th width="100">Store</th>
                        <th width="100">Floor</th>
                        <th width="80">Room</th>
                        <th width="80">Rack </th>
                        <th width="80">Shelf</th>
                        <th width="80">Bin</th>
                        <th>Qty</th>
                    </tr>
				</thead>
            </table>
            <div style="width:920px; max-height:410px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="900" cellpadding="0" cellspacing="0" id="table_body">
                    <?
                    if ($type==1) // recv
                    {
                    	$recv_sql="SELECT a.recv_number as sys_number,a.receive_date as sys_no_date,e.qnty as grey_receive_qnty,b.floor_id,b.room,b.rack,b.self,b.bin_box,d.no_of_roll,b.store_id,b.cons_uom,c.po_breakdown_id,b.prod_id, e.barcode_no
						from inv_receive_master a, inv_transaction b, order_wise_pro_details c,pro_grey_prod_entry_dtls d, pro_roll_details e 
						where a.id=b.mst_id and b.id=c.trans_id and a.id=d.mst_id and c.dtls_id=d.id and a.id=e.mst_id and d.id=e.dtls_id and c.dtls_id=e.dtls_id and a.entry_form in(2,22,58) and c.trans_id <>0 and c.trans_type=1 and c.entry_form in (2,22,58) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_id=$companyID and c.po_breakdown_id in($fso_id) and c.prod_id in($product_ids) and d.febric_description_id=$fabric_construction and d.color_id='$fab_color_id'";
						// echo $recv_sql;die;
						$rcvIssData=sql_select($recv_sql);
						foreach ($rcvIssData as $key => $row) 
						{
							$barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
						}
                    }
                    else // $type=2 issue
                    {
                    	$iss_sql="SELECT a.issue_number as sys_number,a.issue_date as sys_no_date,e.qnty as grey_receive_qnty,b.floor_id,b.room,b.rack,b.self,b.bin_box,d.no_of_roll,b.store_id,b.cons_uom,c.po_breakdown_id,b.prod_id, e.barcode_no 
                    	from inv_issue_master a, inv_transaction b, order_wise_pro_details c, inv_grey_fabric_issue_dtls d, pro_roll_details e 
                    	where a.id=b.mst_id and b.id=c.trans_id and c.dtls_id=d.id and a.id=e.mst_id and c.dtls_id=e.dtls_id and d.id=e.dtls_id and a.entry_form in(61) and c.trans_id <>0 and c.trans_type=2 and c.entry_form in (61) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_id=$companyID and c.po_breakdown_id in($orderID) and c.prod_id in($product_ids) and d.color_id=$fab_color_id";
                    	// echo $iss_sql;
                    	$rcvIssData=sql_select($iss_sql);
                    }
					
					$i=1;
					foreach ($rcvIssData as $row) 
					{
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                        <td width="30"><? echo $i; ?></td>
	                        <td width="120"><p><? echo $row[csf('sys_number')]; ?></p></td>
	                        <td width="80"  align="center"><? echo change_date_format($row[csf('sys_no_date')]); ?></td>
	                        <td width="80"><div style="word-break:break-all"><p><? echo $row[csf('barcode_no')]; ?></p></div></td>
	                        <td width="100"><div style="word-break:break-all"><? echo $store_name_arr[$row[csf('store_id')]]; ?></div></td>
	                        <td width="100" align="center"><div style="word-break:break-all"><? echo $floor_room_rack_arr[$row[csf('floor_id')]]; ?></div></td>
	                        <td width="80" align="center"><div style="word-break:break-all"><? echo $floor_room_rack_arr[$row[csf('room')]]; ?></div></td>
	                        <td width="80" align="center"><div style="word-break:break-all"><? echo $floor_room_rack_arr[$row[csf('rack')]]; ?></div></td>
	                        <td width="80" align="center"><div style="word-break:break-all"><? echo $floor_room_rack_arr[$row[csf('self')]]; ?></div></td>
	                        <td width="80" align="center"><p><? echo $floor_room_rack_arr[$row[csf('bin_box')]]; ?></p>&nbsp;</td>
	                        <td align="right"><? echo $row[csf('grey_receive_qnty')]; ?>&nbsp;</td>
	                    </tr>
	                    <?
	                    $total_recv_qty+=$row[csf('grey_receive_qnty')];
	                    $i++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="10" align="right">Total</th>
                            <th align="right"><? echo number_format($total_recv_qty,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
			</div>
			<!-- =====================Receive Issue End========================== -->
			
			<!-- ====================Issue Return Start========================= -->
			<?if ($type==2) {?>
			<table border="1" class="rpt_table" rules="all" width="900" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="11"><b>Issue Return</b></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="120">Transaction Reff</th>
                        <th width="80">Transaction Date</th>
                        <th width="80">Barcode No.</th>
                        <th width="100">Store</th>
                        <th width="100">Floor</th>
                        <th width="80">Room</th>
                        <th width="80">Rack </th>
                        <th width="80">Shelf</th>
                        <th width="80">Bin</th>
                        <th>Qty</th>
                    </tr>
				</thead>
            </table>
            <div style="width:920px; max-height:410px; overflow-y:scroll" id="scroll_body2">
                <table border="1" class="rpt_table" rules="all" width="900" cellpadding="0" cellspacing="0" id="table_body2">
                    <?
                	$issRtnData=sql_select("SELECT a.recv_number as sys_number,a.receive_date as sys_number_date,e.qnty as grey_issue_rtn_qnty,b.floor_id,b.room,b.rack,b.self,b.bin_box,d.no_of_roll,b.store_id,b.cons_uom,c.po_breakdown_id,b.prod_id, e.barcode_no  
					from inv_receive_master a, inv_transaction b, order_wise_pro_details c,pro_grey_prod_entry_dtls d, pro_roll_details e   
					where a.id=b.mst_id and b.id=c.trans_id and a.id=d.mst_id and c.dtls_id=d.id and a.id=e.mst_id and d.id=e.dtls_id and c.dtls_id=e.dtls_id and b.mst_id=e.mst_id and a.entry_form in(84) and c.trans_id <>0 and c.entry_form in (84) and c.trans_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_id=$companyID and c.po_breakdown_id in($orderID) and c.prod_id in($product_ids) and d.color_id='$fab_color_id'");
					
					$ii=1;
					foreach ($issRtnData as $row) 
					{
						$store_arr[$row[csf('prod_id')]]['store_id']=$row[csf('store_id')];
                    	?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $ii; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $ii;?>">
	                        <td width="30"><? echo $ii; ?></td>
	                        <td width="120"><p><? echo $row[csf('sys_number')]; ?></p></td>
	                        <td width="80"  align="center"><? echo change_date_format($row[csf('sys_number_date')]); ?></td>
	                        <td width="80"><div style="word-break:break-all"><p><? echo $row[csf('barcode_no')]; ?></p></div></td>
	                        <td width="100"><div style="word-break:break-all"><? echo $store_name_arr[$row[csf('store_id')]]; ?></div></td>
	                        <td width="100" align="center"><div style="word-break:break-all"><? echo $floor_room_rack_arr[$row[csf('floor_id')]]; ?></div></td>
	                        <td width="80" align="center"><div style="word-break:break-all"><? echo $floor_room_rack_arr[$row[csf('room')]]; ?></div></td>
	                        <td width="80" align="center"><div style="word-break:break-all"><? echo $floor_room_rack_arr[$row[csf('rack')]]; ?></div></td>
	                        <td width="80" align="center"><div style="word-break:break-all"><? echo $floor_room_rack_arr[$row[csf('self')]]; ?></div></td>
	                        <td width="80" align="center"><p><? echo $floor_room_rack_arr[$row[csf('bin_box')]]; ?></p>&nbsp;</td>
	                        <td align="right"><? echo number_format($row[csf('grey_issue_rtn_qnty')],2); ?>&nbsp;</td>
	                    </tr>
	                    <?
	                    $total_grey_issue_rtn_qnty+=$row[csf('grey_issue_rtn_qnty')];
	                    $ii++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="10" align="right">Sub Total</th>
                            <th align="right"><? echo number_format($total_grey_issue_rtn_qnty,2); ?></th>
                        </tr>
                        <tr>
                            <th colspan="10" align="right">Grand Total</th>
                            <th align="right"><? echo number_format($total_recv_qty-$total_grey_issue_rtn_qnty,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
			</div>
			<?}?>
			<!-- ===================== Issue Return End ========================== -->

			<!-- ==================== Transfer In Start ========================= -->
			<?if ($type==1){?>
			<table border="1" class="rpt_table" rules="all" width="1030" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="12"><b>Transfer In</b></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="70">Transaction Reff</th>
                        <th width="120">Transaction Date</th>
                        <th width="100">From Job</th>
                        <th width="80">Barcode No.</th>
                        <th width="80">Store</th>
                        <th width="60">Floor</th>
                        <th width="80">Room</th>
                        <th width="100">Rack </th>
                        <th width="100">Shelf</th>
                        <th width="60">Bin</th>
                        <th>Qty</th>
                    </tr>
				</thead>
            </table>
            <div style="width:1050px; max-height:410px; overflow-y:scroll" id="scroll_body3">
                <table border="1" class="rpt_table" rules="all" width="1030" cellpadding="0" cellspacing="0" id="table_body3">
                    <?
					$trans_in_sql="SELECT a.transfer_system_id,a.transfer_date, b.to_store as store_id, b.to_floor_id as floor_id, b.to_room as room, b.to_rack as rack, b.to_shelf as shelf,b.to_bin_box as bin_box,b.uom,b.no_of_roll,c.po_breakdown_id,b.from_prod_id as prod_id,d.roll_no,d.qnty as roll_qnty, d.barcode_no, a.from_order_id as order_id, a.to_order_id, e.job_no
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c,pro_roll_details d, fabric_sales_order_mst e
					where a.id=b.mst_id and b.to_trans_id=c.trans_id and b.id=d.dtls_id and a.id=d.mst_id and a.from_order_id=e.id and a.entry_form in(133) AND d.entry_form IN (133) and c.trans_id <>0 and c.entry_form in (133) and c.trans_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id=$companyID and c.prod_id in($product_ids) and c.po_breakdown_id in($orderID) and d.is_sales=1";
					$transInData=sql_select($trans_in_sql);
					foreach ($transInData as $key => $row) 
					{
						$barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
					}

					// Production Sql
					if(!empty($barcode_no_arr))
					{
						foreach($barcode_no_arr as $barcode_no)
						{
							if( $barcode_no_check[$barcode_no] =="" )
					        {
					            $barcode_no_check[$barcode_no]=$barcode_no;
					            $barcodeno = $barcode_no;
					            execute_query("insert into tmp_barcode_no (userid, barcode_no,entry_form) values ($user_id,$barcodeno,82)");
					        }			
						}
						oci_commit($con);

						$production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no,c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, a.brand_id, c.knitting_source, c.challan_no as production_challan, c.knitting_company, a.yarn_prod_id, a.body_part_id, b.coller_cuff_size
						from pro_grey_prod_entry_dtls a, pro_roll_details b, inv_receive_master c, tmp_barcode_no d 
						where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2,22,58) and b.entry_form in(2,22,58)  and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid= $user_id and d.entry_form=82");
				        $yarn_prod_id_check=array();$prog_no_check=array();
				        foreach ($production_sql as $row)
				        {
				            $prodBarcodeData[$row[csf("barcode_no")]]["color_id"] =$row[csf("color_id")];
				        }
					}
					$iii=1;
					foreach ($transInData as $row) 
					{
						$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];
						if ($color_id==$fab_color_id) 
						{
                    	?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $iii; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $iii;?>">
                            <td width="30"><? echo $iii; ?></td>
                            <td width="70"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                            <td width="120"  align="center"><? echo change_date_format($row[csf('transfer_date')]);// ?></td>
                            <td width="100"><div style="word-break:break-all"><p><? echo $row[csf("job_no")]; ?></p></div></td>
                            <td width="80" align="center"><? echo $row[csf('barcode_no')]; ?>&nbsp;</td>
							<td width="80"><div style="word-break:break-all"><? echo $store_name_arr[$row[csf('store_id')]]; ?></div></td>							
                            <td width="60" align="center"><? echo $floor_room_rack_arr[$row[csf("floor_id")]]; ?>&nbsp;</td>
							<td width="80" align="center"><div style="word-break:break-all"><? echo $floor_room_rack_arr[$row[csf('room')]]; ?></div></td>
                            <td width="100" align="center"><div style="word-break:break-all"><? echo $floor_room_rack_arr[$row[csf('rack')]]; ?></div></td>
                            <td width="100" align="center"><div style="word-break:break-all"><? echo $floor_room_rack_arr[$row[csf('shelf')]]; ?></div></td>
                            <td width="60" align="center"><p><? echo $floor_room_rack_arr[$row[csf('bin_box')]]; ?></p>&nbsp;</td>
                            <td align="right"><? echo $row[csf('roll_qnty')]; ?>&nbsp;</td>
                        </tr>
	                    <?
	                    $total_trnsf_in_qty+=$row[csf('roll_qnty')];
	                    $iii++;
	                	}
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="11" align="right">Sub Total</th>
                            <th align="right"><? echo number_format($total_trnsf_in_qty,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
			</div>
			<?}?>
			<!-- ==================== Transfer In End =========================== -->	

			<!-- ==================== Transfer Out Start ========================= -->
			<?if ($type==1){?>
			<table border="1" class="rpt_table" rules="all" width="1030" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="12"><b>Transfer Out</b></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="70">Transaction Reff</th>
                        <th width="120">Transaction Date</th>
                        <th width="100">To Job</th>
                        <th width="80">Barcode No.</th>
                        <th width="80">Store</th>
                        <th width="60">Floor</th>
                        <th width="80">Room</th>
                        <th width="100">Rack </th>
                        <th width="100">Shelf</th>
                        <th width="60">Bin</th>
                        <th>Qty</th>
                    </tr>
				</thead>
            </table>
            <div style="width:1050px; max-height:410px; overflow-y:scroll" id="scroll_body3">
                <table border="1" class="rpt_table" rules="all" width="1030" cellpadding="0" cellspacing="0" id="table_body3">
                    <?
					$trans_out_sql="SELECT a.transfer_system_id,a.transfer_date,b.from_store as store_id, b.floor_id, b.room, b.rack,b.shelf,b.bin_box,b.uom,b.no_of_roll,c.po_breakdown_id,b.from_prod_id as prod_id,d.roll_no,d.qnty as roll_qnty, d.barcode_no, a.from_order_id, a.to_order_id as order_id, e.job_no
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, pro_roll_details d, fabric_sales_order_mst e
					where a.id=b.mst_id and b.trans_id=c.trans_id and  b.id=d.dtls_id and a.id=d.mst_id and a.to_order_id=e.id and a.entry_form in(133) and c.trans_id <>0 and c.entry_form in (133) and c.trans_type=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id=$companyID  and c.prod_id in($product_ids) and c.po_breakdown_id in($orderID) and d.is_sales=1";
					$transOutData=sql_select($trans_out_sql);
					$iii=1;
					foreach ($transOutData as $row) 
					{
						$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];
						if ($color_id==$fab_color_id) 
						{
                    	?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $iii; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $iii;?>">
                            <td width="30"><? echo $iii; ?></td>
                            <td width="70"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                            <td width="120"  align="center"><? echo change_date_format($row[csf('transfer_date')]);// ?></td>
                            <td width="100"><div style="word-break:break-all"><p><? echo $row[csf("job_no")]; ?></p></div></td>
                            <td width="80" align="center"><? echo $row[csf('barcode_no')]; ?>&nbsp;</td>
							<td width="80"><div style="word-break:break-all"><? echo $store_name_arr[$row[csf('store_id')]]; ?></div></td>							
                            <td width="60" align="center"><? echo $floor_room_rack_arr[$row[csf("floor_id")]]; ?>&nbsp;</td>
							<td width="80" align="center"><div style="word-break:break-all"><? echo $floor_room_rack_arr[$row[csf('room')]]; ?></div></td>
                            <td width="100" align="center"><div style="word-break:break-all"><? echo $floor_room_rack_arr[$row[csf('rack')]]; ?></div></td>
                            <td width="100" align="center"><div style="word-break:break-all"><? echo $floor_room_rack_arr[$row[csf('shelf')]]; ?></div></td>
                            <td width="60" align="center"><p><? echo $floor_room_rack_arr[$row[csf('bin_box')]]; ?></p>&nbsp;</td>
                            <td align="right"><? echo $row[csf('roll_qnty')]; ?>&nbsp;</td>
                        </tr>
	                    <?
	                    $total_trnsf_out_qty+=$row[csf('roll_qnty')];
	                    $iii++;
	                	}
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="11" align="right">Sub Total</th>
                            <th align="right"><? echo number_format($total_trnsf_out_qty,2); ?></th>
                        </tr>
                        <tr>
                            <th colspan="11" align="right">Grand Total</th>
                            <th align="right"><? echo number_format($total_recv_qty+$total_trnsf_in_qty-$total_trnsf_out_qty,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
			</div>
			<?}?>
			<!-- ==================== Transfer Out End =========================== -->	
		</div>
	</fieldset>	
	<? 
	execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=82");
	oci_commit($con);
	disconnect($con);
	exit();
}

if($action=="issueQty_popup") // in side and out side issue qty popup
{
	echo load_html_head_contents("Grey Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$companyID= $companyID;
	$orderID=$fso_id;
	$productIds=$productIds;
	$fabric_construction=$fabric_construction;
	$fab_color_id=$fab_color_id;

	$supplier_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$count_arr = return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr = return_library_array( "select id, brand_name from lib_brand",'id','brand_name');

	$con = connect();
	execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=82");
	oci_commit($con);

	if ($orderID>0 && $productIds>0 && $fabric_construction>0 && $fab_color_id>0) 
	{
		$source_cond='';
		if ($type==1) 
		{
			$dye_source_cond=" and a.knit_dye_source=1";
		}
		if ($type==2) 
		{
			$dye_source_cond=" and a.knit_dye_source=3";
		}
		$issue_sql="SELECT a.issue_number,a.issue_date, a.knit_dye_source, a.knit_dye_company,e.qnty,c.po_breakdown_id,b.prod_id, e.barcode_no 
		from inv_issue_master a, inv_transaction b, order_wise_pro_details c, inv_grey_fabric_issue_dtls d, pro_roll_details e 
		where a.id=b.mst_id and b.id=c.trans_id and c.dtls_id=d.id and a.id=e.mst_id and c.dtls_id=e.dtls_id and d.id=e.dtls_id and a.entry_form in(61) and c.trans_id <>0 and c.trans_type=2 and c.entry_form in (61) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_id=$companyID and c.po_breakdown_id in($orderID) and c.prod_id in($productIds) and d.color_id=$fab_color_id $dye_source_cond";
		// echo $issue_sql;
		$issue_sqlData=sql_select($issue_sql);
	}
	
	if(empty($issue_sqlData))
	{
		echo get_empty_data_msg();
		die;
	}

	foreach ($issue_sqlData as $key => $row) 
	{
		$barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		if( $barcode_no_check[$row[csf('barcode_no')]] =="" )
        {
            $barcode_no_check[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
            $barcodeno = $row[csf('barcode_no')];
            execute_query("insert into tmp_barcode_no (userid, barcode_no,entry_form) values ($user_id,$barcodeno,82)");
        }
	}
	oci_commit($con);

	// Production info
	if(!empty($barcode_no_arr))
	{
		$production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no,c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, a.brand_id, c.knitting_source, c.challan_no as production_challan, c.knitting_company, a.yarn_prod_id, a.body_part_id, b.coller_cuff_size
		from pro_grey_prod_entry_dtls a, pro_roll_details b, inv_receive_master c, tmp_barcode_no d 
		where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2,22,58) and b.entry_form in(2,22,58)  and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid= $user_id and d.entry_form=82");
        $yarn_prod_id_check=array();$prog_no_check=array();
        foreach ($production_sql as $row)
        {
            $prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"] =$row[csf("receive_basis")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prog_book"] =$row[csf("booking_no")];
            $prodBarcodeData[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
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
            $prodBarcodeData[$row[csf("barcode_no")]]["item_size"] =$row[csf("coller_cuff_size")];
            $prodBarcodeData[$row[csf("barcode_no")]]["machine_no_id"] =$row[csf("machine_no_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prod_challan"] =$row[csf("production_challan")];
            $prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"] =$row[csf("knitting_source")];
            $prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"] =$row[csf("knitting_company")];
            $prodBarcodeData[$row[csf("barcode_no")]]["body_part_id"] =$row[csf("body_part_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["brand_id"] =$row[csf("brand_id")];
            $allDeterArr[$row[csf("febric_description_id")]] =$row[csf("febric_description_id")];
            $allColorArr[$row[csf("color_id")]] =$row[csf("color_id")];
            $allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
        }
	}

	// iss_data_arr data array
	foreach ($issue_sqlData as $key => $row) 
	{
		$machine_dia=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"];
		$width=$prodBarcodeData[$row[csf("barcode_no")]]["width"];
		$stitch_length=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"];
		$yarn_count=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"];
		$brand_id=$prodBarcodeData[$row[csf("barcode_no")]]["brand_id"];
		$yarn_lot=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"];
		$febric_description_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];

		if ($row[csf("knit_dye_source")]==3) 
		{
			$iss_data_arr[$row[csf("issue_number")]]["knit_dye_company"] =$supplier_library[$row[csf("knit_dye_company")]];
		}
		$iss_data_arr[$row[csf("issue_number")]]["issue_date"] =$row[csf("issue_date")];
		$iss_data_arr[$row[csf("issue_number")]]["febric_descr_id"] =$febric_description_id;
		$iss_data_arr[$row[csf("issue_number")]]["machine_dia"] .=$machine_dia.',';
		$iss_data_arr[$row[csf("issue_number")]]["fdia"].=$width.',';
		$iss_data_arr[$row[csf("issue_number")]]["stitch_length"] .=$stitch_length.',';
		$iss_data_arr[$row[csf("issue_number")]]["yarn_count"] .=$yarn_count.',';
		$iss_data_arr[$row[csf("issue_number")]]["brand_id"] .=$brand_id.',';
		$iss_data_arr[$row[csf("issue_number")]]["yarn_lot"] .=$yarn_lot.',';
		$iss_data_arr[$row[csf("issue_number")]]["iss_qnty"] +=$row[csf("qnty")];
	}

	//composition and constructtion
	$composition_arr=array();
	$constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row) 
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($deter_array);

	execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=82");
	oci_commit($con);
	disconnect($con);
	if ($type==2) $thcolspan=13; else $thcolspan=12;
	if ($type==2) $title='Out Side Issue Details'; else $title='In Side Issue Details';
	?>
	<fieldset style="width:<? echo $popup_width;?>; margin-left:3px">
		<div id="report_container">
			<!-- ====================Start========================= -->
			<table border="1" class="rpt_table" rules="all" width="1010" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="<? echo $thcolspan; ?>"><b><? echo $title; ?></b></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="80">Date</th>
                        <th width="80">Dyeing Scource</th>
                        <? if ($type==2) echo '<th width="80">Factory Name</th>';?>
                        <th width="120">Chalan Number</th>
                        <th width="120">Fab. Composition</th>
                        <th width="80">M/C Dia</th>
                        <th width="80">F/Dia</th>
                        <th width="80">Stich Length</th>
                        <th width="80">Y. Count</th>
                        <th width="80">Y. Brand</th>
                        <th width="80">Y. Lot</th>
                        <th>Issue Qty</th>
                    </tr>
				</thead>
            </table>
            <div style="width:1030px; max-height:410px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="1010" cellpadding="0" cellspacing="0" id="table_body">
                    <?
					$i=1;
					foreach ($iss_data_arr as $iss_number => $row) 
					{
						$yarn_counts_arr = array_unique(array_filter(explode(",", chop($row['yarn_count'],","))));
                        $yarn_counts="";
                        foreach ($yarn_counts_arr as $count) {
                            $yarn_counts .= $count_arr[$count] . ",";
                        }
                        // $yarn_counts = rtrim($yarn_counts, ", ");
                        $yarn_counts =implode(",",array_filter(array_unique(explode(",", $yarn_counts))));

                        $brand_id_id_arr = array_unique(array_filter(explode(",", chop($row['brand_id'],","))));
                        $yarn_brand = "";
                        foreach ($brand_id_id_arr as $bid)
                        {
                            $yarn_brand .= ($yarn_brand =="") ? $brand_arr[$bid] :  ",". $brand_arr[$bid];
                        }
                        $yarn_brand =implode(",",array_filter(array_unique(explode(",", $yarn_brand))));
                        $stitch_length =implode(",",array_filter(array_unique(explode(",", chop($row['stitch_length'],",")))));
                        $fdia =implode(",",array_filter(array_unique(explode(",", chop($row['fdia'],",")))));
                        $yarn_lot =implode(",",array_filter(array_unique(explode(",", chop($row['yarn_lot'],",")))));
                        $machine_dia =implode(",",array_filter(array_unique(explode(",", chop($row['machine_dia'],",")))));
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                        <td width="30"><? echo $i; ?></td>
	                        <td width="80"  align="center"><? echo change_date_format($row['issue_date']); ?></td>
	                        <td width="80"><div style="word-break:break-all"><p><? if ($type==1) echo 'In House'; else echo 'Out-bound Subconttract'; ?></p></div></td>
	                        <? if ($type==2) echo '<td width="80"><p>'.$row['knit_dye_company'].'</p></td>'; ?>
	                        <td width="120"><p><? echo $iss_number; ?></p></td>
	                        <td width="120"><div style="word-break:break-all"><? echo $composition_arr[$row['febric_descr_id']]; ?></div></td>
	                        <td width="80" align="center"><div style="word-break:break-all"><? echo $machine_dia; ?></div></td>
	                        <td width="80" align="center"><div style="word-break:break-all"><? echo $fdia; ?></div></td>
	                        <td width="80" align="center"><div style="word-break:break-all"><? echo $stitch_length; ?></div></td>
	                        <td width="80" align="center"><div style="word-break:break-all"><? echo $yarn_counts; ?></div></td>
	                        <td width="80" align="center"><p><? echo $yarn_brand; ?></p>&nbsp;</td>
	                        <td width="80" align="center"><p><? echo $yarn_lot; ?></p>&nbsp;</td>
	                        <td align="right"><? echo $row['iss_qnty']; ?>&nbsp;</td>
	                    </tr>
	                    <?
	                    $total_iss_qty+=$row['iss_qnty'];
	                    $i++;
                    }
                    if ($type==2) $colspan=12; else $colspan=11;
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="<? echo $colspan; ?>" align="right">Sub Total</th>
                            <th align="right"><? echo number_format($total_iss_qty,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
			</div>
			<!-- =====================End========================== -->	
		</div>
	</fieldset>	
	<? 
	exit();
}

if($action=="issueReturnQty_popup") // Issue Return qty Popup
{
	echo load_html_head_contents("Grey Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$companyID= $companyID;
	$orderID=$fso_id;
	$productIds=$productIds;
	$fabric_construction=$fabric_construction;
	$fab_color_id=$fab_color_id;

	$company_library = return_library_array("select id, company_short_name from lib_company", "id", "company_short_name");
	$supplier_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$count_arr = return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr = return_library_array( "select id, brand_name from lib_brand",'id','brand_name');

	$con = connect();
	execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=82");
	oci_commit($con);

	if ($orderID>0 && $productIds>0 && $fabric_construction>0 && $fab_color_id>0) 
	{
		$iss_rtn_sql="SELECT a.recv_number, a.receive_date, a.knitting_company, a.knitting_source, b.prod_id, c.po_breakdown_id, d.febric_description_id, d.width, d.stitch_length, d.yarn_count, d.yarn_lot, e.qnty, e.barcode_no
		from inv_receive_master a, inv_transaction b, order_wise_pro_details c,pro_grey_prod_entry_dtls d, pro_roll_details e 
		where a.id=b.mst_id and b.id=c.trans_id and a.id=d.mst_id and c.dtls_id=d.id and a.id=e.mst_id and d.id=e.dtls_id and c.dtls_id=e.dtls_id and a.entry_form in(84) and c.trans_id <>0 and c.trans_type=4 and c.entry_form in (84) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_id=$companyID and c.po_breakdown_id in($orderID) and c.prod_id in($productIds) and d.febric_description_id=$fabric_construction and d.color_id='$fab_color_id' and e.is_sales=1";
		// echo $iss_rtn_sql;
		$iss_rtn_sqlData=sql_select($iss_rtn_sql);
	}
	
	if(empty($iss_rtn_sqlData))
	{
		echo get_empty_data_msg();
		die;
	}

	foreach ($iss_rtn_sqlData as $key => $row) 
	{
		$barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		if( $barcode_no_check[$row[csf('barcode_no')]] =="" )
        {
            $barcode_no_check[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
            $barcodeno = $row[csf('barcode_no')];
            execute_query("insert into tmp_barcode_no (userid, barcode_no,entry_form) values ($user_id,$barcodeno,82)");
        }
	}
	oci_commit($con);

	// Production info
	if(!empty($barcode_no_arr))
	{
		$production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no,c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, a.brand_id, c.knitting_source, c.challan_no as production_challan, c.knitting_company, a.yarn_prod_id, a.body_part_id, b.coller_cuff_size
		from pro_grey_prod_entry_dtls a, pro_roll_details b, inv_receive_master c, tmp_barcode_no d 
		where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2,22,58) and b.entry_form in(2,22,58)  and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid= $user_id and d.entry_form=82");
        $yarn_prod_id_check=array();$prog_no_check=array();
        foreach ($production_sql as $row)
        {
            $prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"] =$row[csf("receive_basis")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prog_book"] =$row[csf("booking_no")];
            $prodBarcodeData[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
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
            $prodBarcodeData[$row[csf("barcode_no")]]["item_size"] =$row[csf("coller_cuff_size")];
            $prodBarcodeData[$row[csf("barcode_no")]]["machine_no_id"] =$row[csf("machine_no_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prod_challan"] =$row[csf("production_challan")];
            $prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"] =$row[csf("knitting_source")];
            $prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"] =$row[csf("knitting_company")];
            $prodBarcodeData[$row[csf("barcode_no")]]["body_part_id"] =$row[csf("body_part_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["brand_id"] =$row[csf("brand_id")];
            $allDeterArr[$row[csf("febric_description_id")]] =$row[csf("febric_description_id")];
            $allColorArr[$row[csf("color_id")]] =$row[csf("color_id")];
            $allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
        }
	}

	foreach ($iss_rtn_sqlData as $key => $row) 
	{
		$machine_dia=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"];
		$yarn_prod_id=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"];
		$brand_id=$prodBarcodeData[$row[csf("barcode_no")]]["brand_id"];
		$knit_source=$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"];
		$knitting_company=$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"];

		$iss_rtn_data_arr[$row[csf("recv_number")]]["receive_date"] =$row[csf("receive_date")];
		if ($knit_source==3) 
		{
			$iss_rtn_data_arr[$row[csf("recv_number")]]["knitting_company"] =$supplier_library[$knitting_company];
		}
		$iss_rtn_data_arr[$row[csf("recv_number")]]["febric_descr_id"] =$row[csf("febric_description_id")];
		$iss_rtn_data_arr[$row[csf("recv_number")]]["machine_dia"] .=$machine_dia.',';
		$iss_rtn_data_arr[$row[csf("recv_number")]]["fdia"].=$row[csf("width")].',';
		$iss_rtn_data_arr[$row[csf("recv_number")]]["stitch_length"] .=$row[csf("stitch_length")].',';
		$iss_rtn_data_arr[$row[csf("recv_number")]]["yarn_count"] .=$row[csf("yarn_count")].',';
		$iss_rtn_data_arr[$row[csf("recv_number")]]["yarn_lot"] .=$row[csf("yarn_lot")].',';
		$iss_rtn_data_arr[$row[csf("recv_number")]]["yarn_prod_id"] .=$yarn_prod_id.',';
		$iss_rtn_data_arr[$row[csf("recv_number")]]["brand_id"] .=$brand_id.',';
		$iss_rtn_data_arr[$row[csf("recv_number")]]["rcv_qnty"] +=$row[csf("qnty")];
		$iss_rtn_data_arr[$row[csf("recv_number")]]["knit_source"] =$knit_source;
	}

	//composition and constructtion
	$composition_arr=array();
	$constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row) 
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($deter_array);

	execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=82");
	oci_commit($con);
	disconnect($con);
	?>
	<fieldset style="width:<? echo $popup_width;?>; margin-left:3px">
		<div id="report_container">
			<!-- ====================Start========================= -->
			<table border="1" class="rpt_table" rules="all" width="1000" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="13"><b>Issue Return Popup</b></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="50">Date</th>
                        <th width="80">Dyeing Scource</th>
                        <th width="120">Chalan Number</th>
                        <th width="80">Factory Name</th>
                        <th width="120">Fab. Composition</th>
                        <th width="50">M/C Dia</th>
                        <th width="50">F/Dia</th>
                        <th width="80">Stich Length</th>
                        <th width="80">Y. Count</th>
                        <th width="80">Y. Brand</th>
                        <th width="80">Y. Lot</th>
                        <th>Retarn Qty</th>
                    </tr>
				</thead>
            </table>
            <div style="width:1020px; max-height:410px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="1000" cellpadding="0" cellspacing="0" id="table_body">
                    <?
					$i=1;
					foreach ($iss_rtn_data_arr as $iss_rtn_number => $row) 
					{
						$yarn_counts_arr = array_unique(array_filter(explode(",", chop($row['yarn_count'],","))));
                        $yarn_counts="";
                        foreach ($yarn_counts_arr as $count) {
                            $yarn_counts .= $count_arr[$count] . ",";
                        }
                        // $yarn_counts = rtrim($yarn_counts, ", ");
                        $yarn_counts =implode(",",array_filter(array_unique(explode(",", $yarn_counts))));

                        $brand_id_id_arr = array_unique(array_filter(explode(",", chop($row['brand_id'],","))));
                        $yarn_brand = "";
                        foreach ($brand_id_id_arr as $bid)
                        {
                            $yarn_brand .= ($yarn_brand =="") ? $brand_arr[$bid] :  ",". $brand_arr[$bid];
                        }
                        $yarn_brand =implode(",",array_filter(array_unique(explode(",", $yarn_brand))));
                        $stitch_length =implode(",",array_filter(array_unique(explode(",", chop($row['stitch_length'],",")))));
                        $fdia =implode(",",array_filter(array_unique(explode(",", chop($row['fdia'],",")))));
                        $yarn_lot =implode(",",array_filter(array_unique(explode(",", chop($row['yarn_lot'],",")))));
                        $machine_dia =implode(",",array_filter(array_unique(explode(",", chop($row['machine_dia'],",")))));
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                        <td width="30"><? echo $i; ?></td>
	                        <td width="50"  align="center"><? echo change_date_format($row['receive_date']); ?></td>
	                        <td width="80"><p><? echo $knitting_source[$row['knit_source']]; ?></p></td>
	                        <td width="120"><p><? echo $iss_rtn_number; ?></p></td>
	                        <td width="80"><div style="word-break:break-all"><p><? echo $row['knitting_company']; ?></p></div></td>
	                        <td width="120"><div style="word-break:break-all"><? echo $composition_arr[$row['febric_descr_id']]; ?></div></td>
	                        <td width="50" align="center"><div style="word-break:break-all"><? echo $machine_dia; ?></div></td>
	                        <td width="50" align="center"><div style="word-break:break-all"><? echo $fdia; ?></div></td>
	                        <td width="80" align="center"><div style="word-break:break-all"><? echo $stitch_length; ?></div></td>
	                        <td width="80" align="center"><div style="word-break:break-all"><? echo $yarn_counts; ?></div></td>
	                        <td width="80" align="center"><p><? echo $yarn_brand; ?></p>&nbsp;</td>
	                        <td width="80" align="center"><p><? echo $yarn_lot; ?></p>&nbsp;</td>
	                        <td align="right"><? echo $row['rcv_qnty']; ?>&nbsp;</td>
	                    </tr>
	                    <?
	                    $total_recv_qty+=$row['rcv_qnty'];
	                    $i++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="12" align="right">Sub Total</th>
                            <th align="right"><? echo number_format($total_recv_qty,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
			</div>
			<!-- =====================End========================== -->	
		</div>
	</fieldset>	
	<? 
	exit();
}

if($action=="totalIssQty_popup") // total issue qty popup
{
	echo load_html_head_contents("Grey Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$companyID= $companyID;
	$orderID=$fso_id;
	$productIds=$productIds;
	$fabric_construction=$fabric_construction;
	$fab_color_id=$fab_color_id;

	$supplier_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$count_arr = return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr = return_library_array( "select id, brand_name from lib_brand",'id','brand_name');

	$con = connect();
	execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=82");
	oci_commit($con);

	if ($orderID>0 && $productIds>0 && $fabric_construction>0 && $fab_color_id>0) 
	{
		$issue_sql="SELECT a.issue_number,a.issue_date, a.knit_dye_source, a.knit_dye_company,e.qnty,c.po_breakdown_id,b.prod_id, e.barcode_no 
		from inv_issue_master a, inv_transaction b, order_wise_pro_details c, inv_grey_fabric_issue_dtls d, pro_roll_details e 
		where a.id=b.mst_id and b.id=c.trans_id and c.dtls_id=d.id and a.id=e.mst_id and c.dtls_id=e.dtls_id and d.id=e.dtls_id and a.entry_form in(61) and c.trans_id <>0 and c.trans_type=2 and c.entry_form in (61) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_id=$companyID and c.po_breakdown_id in($orderID) and c.prod_id in($productIds)and d.color_id=$fab_color_id";
		// echo $issue_sql;
		$issue_sqlData=sql_select($issue_sql);
	}
	
	if(empty($issue_sqlData))
	{
		echo get_empty_data_msg();
		die;
	}

	foreach ($issue_sqlData as $key => $row) 
	{
		$barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		if( $barcode_no_check[$row[csf('barcode_no')]] =="" )
        {
            $barcode_no_check[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
            $barcodeno = $row[csf('barcode_no')];
            execute_query("insert into tmp_barcode_no (userid, barcode_no,entry_form) values ($user_id,$barcodeno,82)");
        }
	}
	oci_commit($con);

	// Production info
	if(!empty($barcode_no_arr))
	{
		$production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no,c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, a.brand_id, c.knitting_source, c.challan_no as production_challan, c.knitting_company, a.yarn_prod_id, a.body_part_id, b.coller_cuff_size
		from pro_grey_prod_entry_dtls a, pro_roll_details b, inv_receive_master c, tmp_barcode_no d 
		where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2,22,58) and b.entry_form in(2,22,58)  and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid= $user_id and d.entry_form=82");
        $yarn_prod_id_check=array();$prog_no_check=array();
        foreach ($production_sql as $row)
        {
            $prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"] =$row[csf("receive_basis")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prog_book"] =$row[csf("booking_no")];
            $prodBarcodeData[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
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
            $prodBarcodeData[$row[csf("barcode_no")]]["item_size"] =$row[csf("coller_cuff_size")];
            $prodBarcodeData[$row[csf("barcode_no")]]["machine_no_id"] =$row[csf("machine_no_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prod_challan"] =$row[csf("production_challan")];
            $prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"] =$row[csf("knitting_source")];
            $prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"] =$row[csf("knitting_company")];
            $prodBarcodeData[$row[csf("barcode_no")]]["body_part_id"] =$row[csf("body_part_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["brand_id"] =$row[csf("brand_id")];
            $allDeterArr[$row[csf("febric_description_id")]] =$row[csf("febric_description_id")];
            $allColorArr[$row[csf("color_id")]] =$row[csf("color_id")];
            $allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
        }
	}

	// iss_data_arr data array
	foreach ($issue_sqlData as $key => $row) 
	{
		$machine_dia=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"];
		$width=$prodBarcodeData[$row[csf("barcode_no")]]["width"];
		$stitch_length=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"];
		$yarn_count=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"];
		$brand_id=$prodBarcodeData[$row[csf("barcode_no")]]["brand_id"];
		$yarn_lot=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"];
		$febric_description_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];

		$str_ref=$machine_dia.'*'.$width.'*'.$stitch_length.'*'.$yarn_count.'*'.$brand_id.'*'.$yarn_lot;
		$iss_data_arr[2][$row[csf("knit_dye_source")]][$febric_description_id][$str_ref]["iss_qnty"] +=$row[csf("qnty")];
	}

	$iss_rtn_sql="SELECT a.recv_number, a.receive_date, a.knitting_company, a.knitting_source, b.prod_id, c.po_breakdown_id, d.febric_description_id, d.width, d.stitch_length, d.yarn_count, d.yarn_lot, e.qnty, e.barcode_no
	from inv_receive_master a, inv_transaction b, order_wise_pro_details c,pro_grey_prod_entry_dtls d, pro_roll_details e 
	where a.id=b.mst_id and b.id=c.trans_id and a.id=d.mst_id and c.dtls_id=d.id and a.id=e.mst_id and d.id=e.dtls_id and c.dtls_id=e.dtls_id and a.entry_form in(84) and c.trans_id <>0 and c.trans_type=4 and c.entry_form in (84) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_id=$companyID and c.po_breakdown_id in($orderID) and c.prod_id in($productIds) and d.febric_description_id=$fabric_construction and d.color_id=$fab_color_id and e.is_sales=1";
	// echo $iss_rtn_sql;
	$iss_rtn_sqlData=sql_select($iss_rtn_sql);
	foreach ($iss_rtn_sqlData as $key => $row) 
	{
		$machine_dia=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"];
		$brand_id=$prodBarcodeData[$row[csf("barcode_no")]]["brand_id"];
		$knit_source=$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"];

		$str_ref2=$machine_dia.'*'.$row[csf("width")].'*'.$row[csf("stitch_length")].'*'.$row[csf("yarn_count")].'*'.$brand_id.'*'.$row[csf("yarn_lot")];
		$iss_data_arr[4][$knit_source][$row[csf("febric_description_id")]][$str_ref2]["iss_rtn_qnty"] +=$row[csf("qnty")];
	}

	//composition and constructtion
	$composition_arr=array();
	$constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row) 
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($deter_array);

	execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=82");
	oci_commit($con);
	disconnect($con);
	if ($type==2) $thcolspan=13; else $thcolspan=12;
	?>
	<fieldset style="width:<? echo $popup_width;?>; margin-left:3px">
		<div id="report_container">
			<!-- ====================Start========================= -->
			<table border="1" class="rpt_table" rules="all" width="980" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="10"><b>Total Issue</b></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="120">Dyeing Scource</th>
                        <th width="150">Fab. Composition</th>
                        <th width="80">M/C Dia</th>
                        <th width="80">F/Dia</th>
                        <th width="80">Stich Length</th>
                        <th width="80">Y. Count</th>
                        <th width="100">Y. Brand</th>
                        <th width="100">Y. Lot</th>
                        <th>Total Issue Qty</th>
                    </tr>
				</thead>
            </table>
            <div style="width:1000px; max-height:410px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="980" cellpadding="0" cellspacing="0" id="table_body">
                    <?
					$i=1;$total_iss_qty=0;
					foreach ($iss_data_arr as $iss_type => $iss_type_v) 
					{
						foreach ($iss_type_v as $knit_source => $knit_sourcev) 
						{
							foreach ($knit_sourcev as $febric_descr_id => $febric_descr_idv) 
							{
								foreach ($febric_descr_idv as $strRef => $row) 
								{
									$strdata=explode("*", $strRef);
									$machineDia=$strdata[0];
									$findia=$strdata[1];
									$stitchLength=$strdata[2];
									$yarn_count=$strdata[3];
									$brand_id=$strdata[4];
									$yarnLot=$strdata[5];

									if($iss_type==4)
									{
										$iss_qnty='-'.$row['iss_rtn_qnty'];
										$iss_return_qnty='-Issue Return';
									}
									else
									{
										$iss_qnty=$row['iss_qnty'];
									}

									$yarn_counts_arr = array_unique(array_filter(explode(",", chop($yarn_count,","))));
			                        $yarn_counts="";
			                        foreach ($yarn_counts_arr as $count) {
			                            $yarn_counts .= $count_arr[$count] . ",";
			                        }
			                        // $yarn_counts = rtrim($yarn_counts, ", ");
			                        $yarn_counts =implode(",",array_filter(array_unique(explode(",", $yarn_counts))));

			                        $brand_id_id_arr = array_unique(array_filter(explode(",", chop($brand_id,","))));
			                        $yarn_brand = "";
			                        foreach ($brand_id_id_arr as $bid)
			                        {
			                            $yarn_brand .= ($yarn_brand =="") ? $brand_arr[$bid] :  ",". $brand_arr[$bid];
			                        }
			                        $yarn_brand =implode(",",array_filter(array_unique(explode(",", $yarn_brand))));
			                        $stitch_length =implode(",",array_filter(array_unique(explode(",", chop($stitchLength,",")))));
			                        $fdia =implode(",",array_filter(array_unique(explode(",", chop($findia,",")))));
			                        $yarn_lot =implode(",",array_filter(array_unique(explode(",", chop($yarnLot,",")))));
			                        $machine_dia =implode(",",array_filter(array_unique(explode(",", chop($machineDia,",")))));
				                    ?>
				                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
				                        <td width="30"><? echo $i; ?></td>
				                        <td width="120"><div style="word-break:break-all"><p><? echo $knitting_source[$knit_source] .$iss_return_qnty; ?></p></div></td>
				                        <td width="150"><div style="word-break:break-all"><? echo $composition_arr[$febric_descr_id]; ?></div></td>
				                        <td width="80" align="center"><div style="word-break:break-all"><? echo $machine_dia; ?></div></td>
				                        <td width="80" align="center"><div style="word-break:break-all"><? echo $fdia; ?></div></td>
				                        <td width="80" align="center"><div style="word-break:break-all"><? echo $stitch_length; ?></div></td>
				                        <td width="80" align="center"><div style="word-break:break-all"><? echo $yarn_counts; ?></div></td>
				                        <td width="100" align="center"><p><? echo $yarn_brand; ?></p>&nbsp;</td>
				                        <td width="100" align="center"><p><? echo $yarn_lot; ?></p>&nbsp;</td>
				                        <td align="right"><? echo $iss_qnty; ?>&nbsp;</td>
				                    </tr>
				                    <?
				                    $total_iss_qty+=$row['iss_qnty']-$row['iss_rtn_qnty'];
				                    $i++;
								}
							}						
	                    }
                	}
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="9" align="right">Total</th>
                            <th align="right"><? echo number_format($total_iss_qty,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
			</div>
			<!-- =====================End========================== -->	
		</div>
	</fieldset>	
	<? 
	exit();
}

if($action=="stockQty_popup") // stock qty popup
{
	echo load_html_head_contents("Grey Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$companyID= $companyID;
	$orderID=$fso_id;
	$productIds=$productIds;
	$fabric_construction=$fabric_construction;
	$fab_color_id=$fab_color_id;

	$count_arr = return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr = return_library_array( "select id, brand_name from lib_brand",'id','brand_name');

	$con = connect();
	execute_query("DELETE from tmp_barcode_no where userid=$user_id");
	oci_commit($con);

	/*
	|--------------------------------------------------------------------------
	| for roll recv qty
	|--------------------------------------------------------------------------
	|
	*/
	$sqlRcvRollQty = "
		SELECT
			d.job_no, d.sales_booking_no, d.customer_buyer, d.buyer_id, d.style_ref_no, d.company_id, d.booking_id, e.prod_id, e.po_breakdown_id, e.entry_form, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, h.qnty AS rcv_qty, h.id AS no_of_roll_rcv, h.barcode_no
        FROM
			fabric_sales_order_mst d
			INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id
            INNER JOIN inv_transaction f ON e.trans_id = f.id
			INNER JOIN pro_grey_prod_entry_dtls g ON f.id = g.trans_id
			INNER JOIN pro_roll_details h ON g.id = h.dtls_id
        WHERE
			e.status_active = 1
			AND e.is_deleted = 0
			AND e.entry_form IN(2,22,58,84)
			AND e.trans_type IN(1,4)
			AND e.trans_id > 0
			AND f.status_active = 1
			AND f.is_deleted = 0
			AND d.company_id IN(".$companyID.")
			and d.id=$orderID
			and e.prod_id in($productIds)
			and g.febric_description_id=$fabric_construction 
			and g.color_id='$fab_color_id'
			AND h.entry_form IN(2,22,58,84) AND h.status_active = 1 and h.is_sales=1
	";	
	// echo $sqlRcvRollQty; die;
	$sqlRcvRollRslt = sql_select($sqlRcvRollQty);
	$barcode_no_arr = array();$po_idArr = array();
	foreach($sqlRcvRollRslt as $row)
	{
        $barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}
	// unset($sqlRcvRollRslt);
	// echo "<pre>"; print_r($dataArr); die;

	/*
	|--------------------------------------------------------------------------
	| for issue qty and roll
	| order to order transfer
	|--------------------------------------------------------------------------
	|
	*/
	$sqlNoOfRoll="
		SELECT
			d.job_no, d.sales_booking_no, d.customer_buyer, d.buyer_id, d.style_ref_no, d.company_id, d.booking_id, e.prod_id, e.po_breakdown_id, e.trans_type, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, e.quantity AS rcv_qty, h.qnty as roll_rcv_qty, g.id AS issue_roll, h.barcode_no, i.transfer_criteria
		FROM 
			fabric_sales_order_mst d
			INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id
			INNER JOIN inv_transaction f ON e.trans_id = f.id 
			INNER JOIN inv_item_transfer_dtls g ON e.dtls_id = g.id 
			INNER JOIN pro_roll_details h ON g.id = h.dtls_id  and g.mst_id=h.mst_id and e.dtls_id=h.dtls_id
			INNER JOIN INV_ITEM_TRANSFER_MST i on i.id=h.mst_id and i.id=G.MST_ID and i.id= F.MST_ID
		WHERE
			e.status_active = 1 
			AND e.is_deleted = 0 
			AND e.entry_form IN(133) AND h.entry_form IN(133) and i.entry_form=133
			AND e.trans_type IN(5,6) 
			AND f.status_active = 1 
			AND f.is_deleted = 0 
			AND g.status_active = 1 
			AND g.is_deleted = 0
			AND h.status_active = 1 
			AND h.is_deleted = 0
			and h.is_sales=1
			AND d.company_id IN(".$companyID.")
			and e.prod_id in($productIds) and e.po_breakdown_id in($orderID)
	";
	// echo "<br>".$sqlNoOfRoll; die;
	$sqlNoOfRollResult = sql_select($sqlNoOfRoll);
	foreach($sqlNoOfRollResult as $row)
	{
        $barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}
	// unset($sqlNoOfRollResult);
	// echo "<pre>"; print_r($barcode_no_arr);
	
	/*
	|--------------------------------------------------------------------------
	| for production
	|--------------------------------------------------------------------------
	|
	*/
	if(!empty($barcode_no_arr))
	{
		foreach($barcode_no_arr as $barcode_no)
		{
			if( $barcode_no_check[$barcode_no] =="" )
	        {
	            $barcode_no_check[$barcode_no]=$barcode_no;
	            $barcodeno = $barcode_no;
	            // echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_id,$barcodeno)";
	            execute_query("insert into tmp_barcode_no (userid, barcode_no,entry_form) values ($user_id,$barcodeno,82)");
	        }			
		}
		oci_commit($con);

		$production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no,c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, a.brand_id, c.knitting_source, c.challan_no as production_challan, c.knitting_company, a.yarn_prod_id, a.body_part_id, b.coller_cuff_size
		from pro_grey_prod_entry_dtls a, pro_roll_details b, inv_receive_master c, tmp_barcode_no d 
		where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2,22,58) and b.entry_form in(2,22,58)  and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid= $user_id and d.entry_form=82 $color_id_cond ");
        $yarn_prod_id_check=array();$prog_no_check=array();
        foreach ($production_sql as $row)
        {
            $prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"] =$row[csf("receive_basis")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prog_book"] =$row[csf("booking_no")];
            $prodBarcodeData[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
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
            $prodBarcodeData[$row[csf("barcode_no")]]["item_size"] =$row[csf("coller_cuff_size")];
            $prodBarcodeData[$row[csf("barcode_no")]]["machine_no_id"] =$row[csf("machine_no_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prod_challan"] =$row[csf("production_challan")];
            $prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"] =$row[csf("knitting_source")];
            $prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"] =$row[csf("knitting_company")];
            $prodBarcodeData[$row[csf("barcode_no")]]["body_part_id"] =$row[csf("body_part_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["brand_id"] =$row[csf("brand_id")];
            $allDeterArr[$row[csf("febric_description_id")]] =$row[csf("febric_description_id")];
            $allColorArr[$row[csf("color_id")]] =$row[csf("color_id")];
            $allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
        }
	}

	// Receive data array
	$dataArr = array();$poArr=array();
	foreach($sqlRcvRollRslt as $row)
	{
		$orderId = $row[csf('po_breakdown_id')];
		$poArr[$orderId] = $orderId;

		$machine_dia=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"];
		$width=$prodBarcodeData[$row[csf("barcode_no")]]["width"];
		$stitch_length=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"];
		$yarn_count=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"];
		$brand_id=$prodBarcodeData[$row[csf("barcode_no")]]["brand_id"];
		$yarn_lot=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"];			
		$deter_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];
		$knit_source=$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"];
		$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];
		$gsm=$prodBarcodeData[$row[csf("barcode_no")]]["gsm"];

		$str_ref=$machine_dia.'*'.$width.'*'.$stitch_length.'*'.$yarn_count.'*'.$brand_id.'*'.$yarn_lot;
		if($row[csf('entry_form')]  == 84)
		{
			$issueReturnArr[$knit_source][$deter_id][$str_ref]['issueReturnQty'] += $row[csf('rcv_qty')];
		}
		else
		{
			$dataArr[$knit_source][$deter_id][$str_ref]['rcvQty'] += $row[csf('rcv_qty')];
		}
	}
	unset($sqlRcvRollRslt);
	// echo "<pre>";print_r($dataArr);die;
	// echo "<pre>";print_r($issueReturnArr);die;

	// Transfer data array
	foreach($sqlNoOfRollResult as $row)
	{
		$machine_dia=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"];
		$width=$prodBarcodeData[$row[csf("barcode_no")]]["width"];
		$stitch_length=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"];
		$yarn_count=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"];
		$brand_id=$prodBarcodeData[$row[csf("barcode_no")]]["brand_id"];
		$yarn_lot=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"];			
		$deter_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];
		$knit_source=$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"];
		$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];
		if ($color_id=="") 
		{
			$color_id=0;
		}
		$str_ref1=$machine_dia.'*'.$width.'*'.$stitch_length.'*'.$yarn_count.'*'.$brand_id.'*'.$yarn_lot;

		if ($color_id==$fab_color_id) 
		{
			if($row[csf('trans_type')] == 5)
			{
				$orderId = $row[csf('po_breakdown_id')];
				$poArr[$orderId] = $orderId;
				if ($row[csf('transfer_criteria')] !=2 ) // without store to store transfer in qty
				{
					if($row[csf('roll_rcv_qty')])
					{
						$dataArr[$knit_source][$deter_id][$str_ref1]['transferInQty'] += $row[csf('roll_rcv_qty')];
					}
					else{
						$dataArr[$knit_source][$deter_id][$str_ref1]['transferInQty'] += $row[csf('rcv_qty')];
					}
				}
				
			}
			// echo $row[csf('trans_type')].'<br>';
			if($row[csf('trans_type')] == 6)
			{
				//echo $row[csf('transfer_criteria')].'='.$row[csf('roll_rcv_qty')].'='.$color_id.'<br>';
				if ($row[csf('transfer_criteria')] !=2 ) // without store to store transfer out qty
				{
					//echo $row[csf('roll_rcv_qty')].'='.$row[csf('rcv_qty')].'<br>';
					if($row[csf('roll_rcv_qty')])
					{
						$dataArr[$knit_source][$deter_id][$str_ref1]['transferOutQty'] += $row[csf('roll_rcv_qty')];
					}
					else{
						$dataArr[$knit_source][$deter_id][$str_ref1]['transferOutQty'] += $row[csf('rcv_qty')];
					}
				}
			}
		}
	}
	unset($sqlNoOfRollResult);
	// echo "<pre>";print_r($dataArr);die;
	// echo "<pre>";print_r($transOutArr);die;

	/*
	|--------------------------------------------------------------------------
	| for issue qty and roll
	|--------------------------------------------------------------------------
	|
	*/
	if(!empty($poArr))
	{
		//===== For Roll Splitting After Issue start ============
	    $split_chk_sql = sql_select("SELECT D.BARCODE_NO, D.QNTY 
	    from pro_roll_split C, pro_roll_details D
	    where c.entry_form = 75 and C.split_from_id = D.roll_split_from and C.status_active = 1 and D.status_active = 1 and d.po_breakdown_id in($orderID)");

	    if(!empty($split_chk_sql))
	    {
	        foreach ($split_chk_sql as $val)
	        {
	            $split_barcode_arr[$val['BARCODE_NO']]= $val['BARCODE_NO'];
	            if ($split_barcode_check[$val['BARCODE_NO']]=="") 
	            {
	                $split_barcode_check[$val['BARCODE_NO']]=$val['BARCODE_NO'];
	                $split_barcode=$val['BARCODE_NO'];
	                execute_query("INSERT INTO tmp_barcode_no(barcode_no,userid,entry_form) VALUES(".$split_barcode.", ".$user_id.",82)");
	            }
	        }
	        oci_commit($con);

	        $split_ref_sql = sql_select("SELECT A.BARCODE_NO, A.QNTY, A.ROLL_ID, B.BARCODE_NO AS MOTHER_BARCODE 
	            from tmp_barcode_no t, pro_roll_details A, pro_roll_details B 
	            where t.barcode_no=a.barcode_no and t.userid=$user_id and t.entry_form=82 and A.entry_form = 61 and A.roll_id = B.id and A.status_active =1 and B.status_active=1");
	        if(!empty($split_ref_sql))
	        {
	            foreach ($split_ref_sql as $value)
	            {
	                $mother_barcode_arr[$value['BARCODE_NO']] = $value['MOTHER_BARCODE'];
	            }
	        }
	    }
	    unset($split_chk_sql);
	    unset($split_ref_sql);
	    // ======== For Roll Splitting After Issue end =========
		
		/*$sqlNoOfRollIssue="
			SELECT
				d.company_id,
				e.prod_id, e.po_breakdown_id, g.qnty AS issue_qty,
				f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, g.barcode_no, h.knit_dye_source
			FROM fabric_sales_order_mst d
				INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id
				INNER JOIN inv_transaction f ON e.trans_id = f.id
				INNER JOIN pro_roll_details g ON e.dtls_id = g.dtls_id
				INNER JOIN INV_ISSUE_MASTER h ON g.mst_id = h.id and  f.mst_id = h.id 
			WHERE
				e.status_active = 1
				AND e.is_deleted = 0
				AND h.entry_form IN(61)
				AND e.entry_form IN(61)
				AND e.trans_type = 2
				AND f.status_active = 1
				AND f.is_deleted = 0
				AND g.status_active = 1
				AND g.is_deleted = 0
				AND g.entry_form IN(61) and g.is_sales=1
				AND d.company_id IN(".$companyID.")
				".$dateCondition."
				 and e.prod_id in($productIds) and e.po_breakdown_id in($orderID) and d.color_id=$fab_color_id
		";*/
		$sqlNoOfRollIssue="SELECT a.issue_number,a.issue_date, a.knit_dye_source, a.knit_dye_company,e.qnty,c.po_breakdown_id,b.prod_id, e.barcode_no 
		from inv_issue_master a, inv_transaction b, order_wise_pro_details c, inv_grey_fabric_issue_dtls d, pro_roll_details e 
		where a.id=b.mst_id and b.id=c.trans_id and c.dtls_id=d.id and a.id=e.mst_id and c.dtls_id=e.dtls_id and d.id=e.dtls_id and a.entry_form in(61) and c.trans_id <>0 and c.trans_type=2 and c.entry_form in (61) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_id=$companyID and c.po_breakdown_id in($orderID) and c.prod_id in($productIds) and d.color_id=$fab_color_id";
		// echo $sqlNoOfRollIssue; die;
		$sqlNoOfRollIssueResult = sql_select($sqlNoOfRollIssue);
		$noOfRollIssueArr = array();
		foreach($sqlNoOfRollIssueResult as $row)
		{
			$machine_dia=$prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"];
			$width=$prodBarcodeData[$row[csf("barcode_no")]]["width"];
			$stitch_length=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"];
			$yarn_count=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"];
			$brand_id=$prodBarcodeData[$row[csf("barcode_no")]]["brand_id"];
			$yarn_lot=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"];			
			$deter_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];
			$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];
			$knit_source=$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"];
			
	        $mother_barcode_no = $mother_barcode_arr[$row[csf("barcode_no")]];
	        if($mother_barcode_no != "")
	        {
	            $machine_dia=$prodBarcodeData[$mother_barcode_no]["machine_dia"];
				$width=$prodBarcodeData[$mother_barcode_no]["width"];
				$stitch_length=$prodBarcodeData[$mother_barcode_no]["stitch_length"];
				$yarn_count=$prodBarcodeData[$mother_barcode_no]["yarn_count"];
				$brand_id=$prodBarcodeData[$mother_barcode_no]["brand_id"];
				$yarn_lot=$prodBarcodeData[$mother_barcode_no]["yarn_lot"];			
				$deter_id=$prodBarcodeData[$mother_barcode_no]["febric_description_id"];
				$color_id=$prodBarcodeData[$mother_barcode_no]["color_id"];
	        }
	        $str_ref2=$machine_dia.'*'.$width.'*'.$stitch_length.'*'.$yarn_count.'*'.$brand_id.'*'.$yarn_lot;
	        $issueQtyArr[$knit_source][$deter_id][$str_ref2]['issue_qty'] += $row[csf('qnty')];
		}
		unset($sqlNoOfRollIssueResult);
	}
	// echo "<pre>"; print_r($issueQtyArr);
	
	if(empty($dataArr))
	{
		echo get_empty_data_msg();
		die;
	}

	//composition and constructtion
	$composition_arr=array();
	$constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row) 
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($deter_array);

	execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=82");
	oci_commit($con);
	disconnect($con);

	?>
	<fieldset style="width:<? echo $popup_width;?>; margin-left:3px">
		<div id="report_container">
			<!-- ====================Start========================= -->
			<table border="1" class="rpt_table" rules="all" width="980" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="10"><b>Stock Qty</b></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="120">Knitting Scource</th>
                        <th width="150">Fab. Composition</th>
                        <th width="80">M/C Dia</th>
                        <th width="80">F/Dia</th>
                        <th width="80">Stich Length</th>
                        <th width="80">Y. Count</th>
                        <th width="100">Y. Brand</th>
                        <th width="100">Y. Lot</th>
                        <th>Stock Qty</th>
                    </tr>
				</thead>
            </table>
            <div style="width:1000px; max-height:410px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="980" cellpadding="0" cellspacing="0" id="table_body">
                    <?
					$i=1;$total_iss_qty=0;
					foreach ($dataArr as $knit_source => $knit_sourcev) 
					{
						foreach ($knit_sourcev as $febric_descr_id => $febric_descr_idv) 
						{
							foreach ($febric_descr_idv as $strRef => $row) 
							{
								$strdata=explode("*", $strRef);
								$machineDia=$strdata[0];
								$findia=$strdata[1];
								$stitchLength=$strdata[2];
								$yarn_count=$strdata[3];
								$brand_id=$strdata[4];
								$yarnLot=$strdata[5];

								//total receive calculation
								$row['totalRcvQty'] = (number_format($row['rcvQty'],2,'.','')+number_format($row['transferInQty'],2,'.',''))-number_format($row['transferOutQty'],2,'.','');

								//total issue calculation
								$row['issueReturnQty'] = $issueReturnArr[$knit_source][$febric_descr_id][$strRef]['issueReturnQty'];
								$row['issue_qty'] = $issueQtyArr[$knit_source][$febric_descr_id][$strRef]['issue_qty'];		
								//echo $row['issue_qty'].'-'.$row['issueReturnQty'].'<br>';						
								$row['totalIssueQty'] = number_format($row['issue_qty'],2,'.','')-number_format($row['issueReturnQty'],2,'.','');
								
								//stock qty calculation
								$row['stockQty'] = $row['totalRcvQty'] - $row['totalIssueQty'];

								$yarn_counts_arr = array_unique(array_filter(explode(",", chop($yarn_count,","))));
		                        $yarn_counts="";
		                        foreach ($yarn_counts_arr as $count) {
		                            $yarn_counts .= $count_arr[$count] . ",";
		                        }
		                        // $yarn_counts = rtrim($yarn_counts, ", ");
		                        $yarn_counts =implode(",",array_filter(array_unique(explode(",", $yarn_counts))));

		                        $brand_id_id_arr = array_unique(array_filter(explode(",", chop($brand_id,","))));
		                        $yarn_brand = "";
		                        foreach ($brand_id_id_arr as $bid)
		                        {
		                            $yarn_brand .= ($yarn_brand =="") ? $brand_arr[$bid] :  ",". $brand_arr[$bid];
		                        }
		                        $yarn_brand =implode(",",array_filter(array_unique(explode(",", $yarn_brand))));
		                        $stitch_length =implode(",",array_filter(array_unique(explode(",", chop($stitchLength,",")))));
		                        $fdia =implode(",",array_filter(array_unique(explode(",", chop($findia,",")))));
		                        $yarn_lot =implode(",",array_filter(array_unique(explode(",", chop($yarnLot,",")))));
		                        $machine_dia =implode(",",array_filter(array_unique(explode(",", chop($machineDia,",")))));
		                        if ($row['stockQty']>0) 
		                        {
			                    ?>
			                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
			                        <td width="30"><? echo $i; ?></td>
			                        <td width="120"><div style="word-break:break-all"><p><? echo $knitting_source[$knit_source]; ?></p></div></td>
			                        <td width="150"><div style="word-break:break-all"><? echo $composition_arr[$febric_descr_id]; ?></div></td>
			                        <td width="80" align="center"><div style="word-break:break-all"><? echo $machine_dia; ?></div></td>
			                        <td width="80" align="center"><div style="word-break:break-all"><? echo $fdia; ?></div></td>
			                        <td width="80" align="center"><div style="word-break:break-all"><? echo $stitch_length; ?></div></td>
			                        <td width="80" align="center"><div style="word-break:break-all"><? echo $yarn_counts; ?></div></td>
			                        <td width="100" align="center"><p><? echo $yarn_brand; ?></p>&nbsp;</td>
			                        <td width="100" align="center"><p><? echo $yarn_lot; ?></p>&nbsp;</td>
			                        <td align="right"><? echo $row['stockQty']; ?>&nbsp;</td>
			                    </tr>
			                    <?
			                    $total_iss_qty+=$row['stockQty'];
			                    $i++;
			                	}
							}
						}						
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="9" align="right">Total</th>
                            <th align="right"><? echo number_format($total_iss_qty,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
			</div>
			<!-- =====================End========================== -->	
		</div>
	</fieldset>	
	<? 
	exit();
}

if($action=="no_of_roll_bal_popup") // no_of_roll_bal_popup for summary 3
{
	echo load_html_head_contents("Grey Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$companyID= $companyID;
	$orderID=$fso_id;
	$productIds=$productIds;
	$fabric_deter_id=$fabric_construction;
	$fab_color_id=$fab_color_id;

	if ($txt_store!="") $store_cond=" and f.store_id in($txt_store)";
	if ($txt_floor_id!="") $floor_cond=" and f.floor_id in($txt_floor_id)";
	if ($txt_room!="") $room_cond=" and f.room in($txt_room)";
	if ($txt_rack_id!="") $rack_cond=" and f.rack in($txt_rack_id)";

	if ($txt_store!="") $store_iss_cond=" and b.store_name in($txt_store)";
	if ($txt_floor_id!="") $floor_iss_cond=" and b.floor_id in($txt_floor_id)";
	if ($txt_room!="") $room_iss_cond=" and b.room in($txt_room)";
	if ($txt_rack_id!="") $rack_iss_cond=" and b.rack in($txt_rack_id)";

	$con = connect();
	execute_query("DELETE from tmp_barcode_no where userid=$user_id");
	oci_commit($con);

	/*
	|--------------------------------------------------------------------------
	| for roll recv qty
	|--------------------------------------------------------------------------
	|
	*/
	$sqlRcvRollQty = "
		SELECT
			d.job_no, d.sales_booking_no, d.customer_buyer, d.buyer_id, d.style_ref_no, d.company_id, d.booking_id, e.prod_id, e.po_breakdown_id, e.entry_form, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, h.qnty AS rcv_qty, h.id AS no_of_roll_rcv, h.barcode_no
        FROM
			fabric_sales_order_mst d
			INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id
            INNER JOIN inv_transaction f ON e.trans_id = f.id
			INNER JOIN pro_grey_prod_entry_dtls g ON f.id = g.trans_id
			INNER JOIN pro_roll_details h ON g.id = h.dtls_id
        WHERE
			e.status_active = 1
			AND e.is_deleted = 0
			AND e.entry_form IN(2,22,58,84)
			AND e.trans_type IN(1,4)
			AND e.trans_id > 0
			AND f.status_active = 1
			AND f.is_deleted = 0
			AND d.company_id IN(".$companyID.")
			and d.id=$orderID
			and e.prod_id in($productIds)
			and g.febric_description_id=$fabric_deter_id 
			and g.color_id='$fab_color_id' $store_cond $floor_cond $room_cond $rack_cond
			AND h.entry_form IN(2,22,58,84) AND h.status_active = 1 and h.is_sales=1
	";	
	// echo $sqlRcvRollQty; die;
	$sqlRcvRollRslt = sql_select($sqlRcvRollQty);
	$barcode_no_arr = array();$po_idArr = array();
	foreach($sqlRcvRollRslt as $row)
	{
        $barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}
	// unset($sqlRcvRollRslt);
	// echo "<pre>"; print_r($dataArr); die;

	/*
	|--------------------------------------------------------------------------
	| for issue qty and roll
	| order to order transfer
	|--------------------------------------------------------------------------
	|
	*/
	$sqlNoOfRoll="
		SELECT
			d.job_no, d.sales_booking_no, d.customer_buyer, d.buyer_id, d.style_ref_no, d.company_id, d.booking_id, e.prod_id, e.po_breakdown_id, e.trans_type, f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, e.quantity AS rcv_qty, h.qnty as roll_rcv_qty, g.id AS issue_roll, h.barcode_no, i.transfer_criteria
		FROM 
			fabric_sales_order_mst d
			INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id
			INNER JOIN inv_transaction f ON e.trans_id = f.id 
			INNER JOIN inv_item_transfer_dtls g ON e.dtls_id = g.id 
			INNER JOIN pro_roll_details h ON g.id = h.dtls_id  and g.mst_id=h.mst_id and e.dtls_id=h.dtls_id
			INNER JOIN INV_ITEM_TRANSFER_MST i on i.id=h.mst_id and i.id=G.MST_ID and i.id= F.MST_ID
		WHERE
			e.status_active = 1 
			AND e.is_deleted = 0 
			AND e.entry_form IN(133) AND h.entry_form IN(133) and i.entry_form=133
			AND e.trans_type IN(5,6) and g.TRANS_ID>0 and g.TO_TRANS_ID>0
			AND f.status_active = 1 
			AND f.is_deleted = 0 
			AND g.status_active = 1 
			AND g.is_deleted = 0
			AND h.status_active = 1 
			AND h.is_deleted = 0
			and h.is_sales=1
			AND d.company_id IN(".$companyID.") $store_cond $floor_cond $room_cond $rack_cond
			and e.prod_id in($productIds) and e.po_breakdown_id in($orderID)
	";
	// echo "<br>".$sqlNoOfRoll; die;
	$sqlNoOfRollResult = sql_select($sqlNoOfRoll);
	foreach($sqlNoOfRollResult as $row)
	{
        $barcode_no_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}
	// unset($sqlNoOfRollResult);
	// echo "<pre>"; print_r($barcode_no_arr);
	
	/*
	|--------------------------------------------------------------------------
	| for production
	|--------------------------------------------------------------------------
	|
	*/
	if(!empty($barcode_no_arr))
	{
		foreach($barcode_no_arr as $barcode_no)
		{
			if( $barcode_no_check[$barcode_no] =="" )
	        {
	            $barcode_no_check[$barcode_no]=$barcode_no;
	            $barcodeno = $barcode_no;
	            // echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_id,$barcodeno)";
	            execute_query("insert into tmp_barcode_no (userid, barcode_no,entry_form) values ($user_id,$barcodeno,82)");
	        }			
		}
		oci_commit($con);

		$production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no,c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, a.brand_id, c.knitting_source, c.challan_no as production_challan, c.knitting_company, a.yarn_prod_id, a.body_part_id, b.coller_cuff_size
		from pro_grey_prod_entry_dtls a, pro_roll_details b, inv_receive_master c, tmp_barcode_no d 
		where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2,22,58) and b.entry_form in(2,22,58)  and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid= $user_id and d.entry_form=82 $color_id_cond ");
        $yarn_prod_id_check=array();$prog_no_check=array();
        foreach ($production_sql as $row)
        {
            $prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"] =$row[csf("receive_basis")];
            $prodBarcodeData[$row[csf("barcode_no")]]["color_range_id"] =$row[csf("color_range_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["color_id"] =$row[csf("color_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"] =$row[csf("febric_description_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["gsm"] =$row[csf("gsm")];
        }
	}

	// Receive data array
	$dataArr = array();$poArr=array();
	foreach($sqlRcvRollRslt as $row)
	{
		$orderId = $row[csf('po_breakdown_id')];
		$poArr[$orderId] = $orderId;			
		$deter_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];
		$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];

		if($row[csf('entry_form')]  == 84)
		{
			$issueReturnArr[$row[csf("barcode_no")]]['issueReturnQty'] += $row[csf('rcv_qty')];
		}
		else
		{
			$dataArr[$row[csf("barcode_no")]]['rcvQty'] += $row[csf('rcv_qty')];
		}
		$dataArr[$row[csf("barcode_no")]]['store_id'] = $row[csf('store_id')];
		$dataArr[$row[csf("barcode_no")]]['floor_id'] = $row[csf('floor_id')];
		$dataArr[$row[csf("barcode_no")]]['room'] = $row[csf('room')];
		$dataArr[$row[csf("barcode_no")]]['rack'] = $row[csf('rack')];
		$dataArr[$row[csf("barcode_no")]]['self'] = $row[csf('self')];
		$dataArr[$row[csf("barcode_no")]]['bin_box'] = $row[csf('bin_box')];
	}
	unset($sqlRcvRollRslt);
	// echo "<pre>";print_r($dataArr);die;
	// echo "<pre>";print_r($issueReturnArr);die;

	// Transfer data array
	foreach($sqlNoOfRollResult as $row)
	{			
		$deter_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];
		$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];
		if ($color_id=="") 
		{
			$color_id=0;
		}

		if ($color_id==$fab_color_id && $deter_id==$fabric_deter_id) 
		{
			if($row[csf('trans_type')] == 5)
			{
				$orderId = $row[csf('po_breakdown_id')];
				$poArr[$orderId] = $orderId;
				/*if ($row[csf('transfer_criteria')] !=2 ) // without store to store transfer in qty
				{
					if($row[csf('roll_rcv_qty')])
					{
						$dataArr[$row[csf("barcode_no")]]['transferInQty'] += $row[csf('roll_rcv_qty')];
					}
					else{
						$dataArr[$row[csf("barcode_no")]]['transferInQty'] += $row[csf('rcv_qty')];
					}
				}*/
				$dataArr[$row[csf("barcode_no")]]['transferInQty'] += $row[csf('roll_rcv_qty')];
			}
			// echo $row[csf('trans_type')].'<br>';
			if($row[csf('trans_type')] == 6)
			{
				//echo $row[csf('transfer_criteria')].'='.$row[csf('roll_rcv_qty')].'='.$color_id.'<br>';
				/*if ($row[csf('transfer_criteria')] !=2 ) // without store to store transfer out qty
				{
					//echo $row[csf('roll_rcv_qty')].'='.$row[csf('rcv_qty')].'<br>';
					if($row[csf('roll_rcv_qty')])
					{
						$dataArr[$row[csf("barcode_no")]]['transferOutQty'] += $row[csf('roll_rcv_qty')];
					}
					else{
						$dataArr[$row[csf("barcode_no")]]['transferOutQty'] += $row[csf('rcv_qty')];
					}
				}*/
				$dataArr[$row[csf("barcode_no")]]['transferOutQty'] += $row[csf('roll_rcv_qty')];
			}
			$dataArr[$row[csf("barcode_no")]]['store_id'] = $row[csf('store_id')];
			$dataArr[$row[csf("barcode_no")]]['floor_id'] = $row[csf('floor_id')];
			$dataArr[$row[csf("barcode_no")]]['room'] = $row[csf('room')];
			$dataArr[$row[csf("barcode_no")]]['rack'] = $row[csf('rack')];
			$dataArr[$row[csf("barcode_no")]]['self'] = $row[csf('self')];
			$dataArr[$row[csf("barcode_no")]]['bin_box'] = $row[csf('bin_box')];
		}
	}
	unset($sqlNoOfRollResult);
	// echo "<pre>";print_r($dataArr);die;
	// echo "<pre>";print_r($transOutArr);die;

	/*
	|--------------------------------------------------------------------------
	| for issue qty and roll
	|--------------------------------------------------------------------------
	|
	*/
	if(!empty($poArr))
	{
		//===== For Roll Splitting After Issue start ============
	    $split_chk_sql = sql_select("SELECT D.BARCODE_NO, D.QNTY 
	    from pro_roll_split C, pro_roll_details D
	    where c.entry_form = 75 and C.split_from_id = D.roll_split_from and C.status_active = 1 and D.status_active = 1 and d.po_breakdown_id in($orderID)");

	    if(!empty($split_chk_sql))
	    {
	        foreach ($split_chk_sql as $val)
	        {
	            $split_barcode_arr[$val['BARCODE_NO']]= $val['BARCODE_NO'];
	            if ($split_barcode_check[$val['BARCODE_NO']]=="") 
	            {
	                $split_barcode_check[$val['BARCODE_NO']]=$val['BARCODE_NO'];
	                $split_barcode=$val['BARCODE_NO'];
	                execute_query("INSERT INTO tmp_barcode_no(barcode_no,userid,entry_form) VALUES(".$split_barcode.", ".$user_id.",82)");
	            }
	        }
	        oci_commit($con);

	        $split_ref_sql = sql_select("SELECT A.BARCODE_NO, A.QNTY, A.ROLL_ID, B.BARCODE_NO AS MOTHER_BARCODE 
	            from tmp_barcode_no t, pro_roll_details A, pro_roll_details B 
	            where t.barcode_no=a.barcode_no and t.userid=$user_id and t.entry_form=82 and A.entry_form = 61 and A.roll_id = B.id and A.status_active =1 and B.status_active=1");
	        if(!empty($split_ref_sql))
	        {
	            foreach ($split_ref_sql as $value)
	            {
	                $mother_barcode_arr[$value['BARCODE_NO']] = $value['MOTHER_BARCODE'];
	            }
	        }
	    }
	    unset($split_chk_sql);
	    unset($split_ref_sql);
	    // ======== For Roll Splitting After Issue end =========
		
		/*$sqlNoOfRollIssue="
			SELECT
				d.company_id,
				e.prod_id, e.po_breakdown_id, g.qnty AS issue_qty,
				f.store_id, f.floor_id, f.room, f.rack, f.self, f.bin_box, g.barcode_no, h.knit_dye_source
			FROM fabric_sales_order_mst d
				INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id
				INNER JOIN inv_transaction f ON e.trans_id = f.id
				INNER JOIN pro_roll_details g ON e.dtls_id = g.dtls_id
				INNER JOIN INV_ISSUE_MASTER h ON g.mst_id = h.id and  f.mst_id = h.id 
			WHERE
				e.status_active = 1
				AND e.is_deleted = 0
				AND h.entry_form IN(61)
				AND e.entry_form IN(61)
				AND e.trans_type = 2
				AND f.status_active = 1
				AND f.is_deleted = 0
				AND g.status_active = 1
				AND g.is_deleted = 0
				AND g.entry_form IN(61) and g.is_sales=1
				AND d.company_id IN(".$companyID.")
				".$dateCondition."
				 and e.prod_id in($productIds) and e.po_breakdown_id in($orderID) and d.color_id=$fab_color_id
		";*/
		$sqlNoOfRollIssue="SELECT a.issue_number,a.issue_date, a.knit_dye_source, a.knit_dye_company,e.qnty,c.po_breakdown_id,b.prod_id, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, e.barcode_no 
		from inv_issue_master a, inv_transaction b, order_wise_pro_details c, inv_grey_fabric_issue_dtls d, pro_roll_details e 
		where a.id=b.mst_id and b.id=c.trans_id and c.dtls_id=d.id and a.id=e.mst_id and c.dtls_id=e.dtls_id and d.id=e.dtls_id and a.entry_form in(61) and c.trans_id <>0 and c.trans_type=2 and c.entry_form in (61) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_id=$companyID and c.po_breakdown_id in($orderID) and c.prod_id in($productIds) and d.color_id='$fab_color_id' $store_iss_cond $floor_iss_cond $room_iss_cond $rack_iss_cond";
		// echo $sqlNoOfRollIssue; die;
		$sqlNoOfRollIssueResult = sql_select($sqlNoOfRollIssue);
		$noOfRollIssueArr = array();
		foreach($sqlNoOfRollIssueResult as $row)
		{			
			$deter_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];
			$color_id=$prodBarcodeData[$row[csf("barcode_no")]]["color_id"];
			
			$barcode_no=$row[csf("barcode_no")];
	        $mother_barcode_no = $mother_barcode_arr[$row[csf("barcode_no")]];
	        if($mother_barcode_no != "")
	        {		
				$deter_id=$prodBarcodeData[$mother_barcode_no]["febric_description_id"];
				$color_id=$prodBarcodeData[$mother_barcode_no]["color_id"];
				$barcode_no=$mother_barcode_no;
	        }
	        if ($color_id==$fab_color_id && $deter_id==$fabric_deter_id) 
			{
	        	$issueQtyArr[$barcode_no]['issue_qty'] += $row[csf('qnty')];
	        }
		}
		unset($sqlNoOfRollIssueResult);
	}
	// echo "<pre>"; print_r($issueQtyArr);die;
	
	if(empty($dataArr))
	{
		echo get_empty_data_msg();
		die;
	}

	$floorRoomRackSelfArr = return_library_array( "SELECT a.floor_room_rack_id, a.floor_room_rack_name FROM lib_floor_room_rack_mst a  WHERE a.status_active = 1 AND a.is_deleted = 0 AND a.company_id IN(".$companyID.")", 'floor_room_rack_id', 'floor_room_rack_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');

	execute_query("DELETE from tmp_barcode_no where userid=$user_id and entry_form=82");
	oci_commit($con);
	disconnect($con);

	?>
	<fieldset style="width:<? echo $popup_width;?>; margin-left:3px">
		<div id="report_container">
			<!-- ====================Start========================= -->
			<table border="1" class="rpt_table" rules="all" width="980" cellpadding="0" cellspacing="0">
                <thead>
		            <tr>
		              <th width="50">Sl</th>
		              <th width="130">Barcode no</th>
		              <th width="130">Store no</th>
		              <th width="130">Roll weight</th>
		              <th width="100">Floor</th>
		              <th width="100">Room</th>
		              <th width="100">Rack</th>
		              <th width="100">Shelf</th>
		              <th width="100">Bin</th>
		            </tr>
		        </thead>
            </table>
            <div style="width:1000px; max-height:410px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="980" cellpadding="0" cellspacing="0" id="table_body">
                    <?
					$i=1;$total_iss_qty=0;
					foreach ($dataArr as $barcode => $row) 
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						//total receive calculation
						$row['totalRcvQty'] = (number_format($row['rcvQty'],2,'.','')+number_format($row['transferInQty'],2,'.',''))-number_format($row['transferOutQty'],2,'.','');

						//total issue calculation
						$row['issueReturnQty'] = $issueReturnArr[$barcode]['issueReturnQty'];
						$row['issue_qty'] = $issueQtyArr[$barcode]['issue_qty'];		
						//echo $row['issue_qty'].'-'.$row['issueReturnQty'].'<br>';						
						$row['totalIssueQty'] = number_format($row['issue_qty'],2,'.','')-number_format($row['issueReturnQty'],2,'.','');
						
						//stock qty calculation
						$row['stockQty'] = $row['totalRcvQty'] - $row['totalIssueQty'];

						if ($row['stockQty']>0) 
		                {
							?>                         
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td width="50" align="center"><? echo $i; ?></td>
								<td width="130" align="center"><? echo $barcode; ?></td>
								<td width="130" align="center"><? echo $store_arr[$row['store_id']]; ?></td>
								<td width="130" align="center"><? echo number_format($row['totalRcvQty'],2); ?></td>
								<td width="100" align="center"><? echo $floorRoomRackSelfArr[$row['floor_id']]; ?></td>
								<td width="100" align="right"><? echo $floorRoomRackSelfArr[$row['room']]; ?></td>
								<td width="100" align="right"><? echo $floorRoomRackSelfArr[$row['rack']]; ?></td>
								<td width="100" align="right" title="<?=$row[csf('self')];?>"><? echo $floorRoomRackSelfArr[$row['self']]; ?></td>
								<td width="100" align="right"><? echo $floorRoomRackSelfArr[$row['bin_box']]; ?></td>
							</tr>
							<?
							$i++; 
						}                                 
	                }
                    ?>
                </table>
			</div>
			<!-- =====================End========================== -->	
		</div>
	</fieldset>	
	<? 
	exit();
}
?>