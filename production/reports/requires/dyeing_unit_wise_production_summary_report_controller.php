<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']['user_id'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action=="load_drop_down_floor")
{
	$ex_data=explode('_',$data);
	if($ex_data[1]!=0) $location_cond=" and b.location_id='$ex_data[1]'"; else $location_cond="";
	
	echo create_drop_down( "cbo_floor_id", 100, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=2 and b.company_id  in($ex_data[0]) and b.status_active in(1,2) and b.is_deleted=0  $location_cond group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", 0, "","" );
	exit();	 
}

if ($action == "fso_no_popup") 
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>
		var hide_fso_id='<? echo $hide_fso_id; ?>';
		var selected_id = new Array, selected_name = new Array();
		
		function check_all_data(is_checked)
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
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
		
		function set_all()
		{
			var old=document.getElementById('txt_fso_row_id').value;
			if(old!="")
			{   
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{  
					js_set_value( old[i] ) 
				}
			}
		}

		function js_set_value( str) 
		{

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) 
			{
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
				
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id =''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_fso_id').val( id );
			$('#hide_fso_no').val( name );
		}
		
	</script>

	</head>
	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:710px;">
					<table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Company</th>
							<th>Buyer Name</th>
							<th>Job Year</th>
							<th>Within Group</th>
							<th>FSO NO.</th>
							<th>Booking NO.</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
							<input type="hidden" name="hide_fso_no" id="hide_fso_no" value="" />
							<input type="hidden" name="hide_fso_id" id="hide_fso_id" value="" />

						</thead>
						<tbody>
							<tr>
								<td>
									<?
									//echo $company_name.'dsds';
									if($company_name) $comp_cond=" and comp.id in($company_name)";
									else $comp_cond="";
									//echo "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond $comp_cond order by comp.company_name";
									echo create_drop_down( "cbo_company_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond $comp_cond order by comp.company_name","id,company_name", 1, "-- Select Company --","", "","" );
									?>
								</td>
								<td align="center">
									<? 
									echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and b.tag_company in($company_name) and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
									?>
								</td>                 
								<td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td> 
								<td><? echo create_drop_down( "cbo_within_group", 80, $yes_no,"", 1, "-- Select --", $selected, "",0,"" );?></td>    
								<td>				
									<input type="text" style="width:100px" class="text_boxes" name="txt_fso_no" id="txt_fso_no" />	
								</td> 	
								<td>				
									<input type="text" style="width:100px" class="text_boxes" name="txt_booking_no" id="txt_booking_no" />	
								</td> 
								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_name; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_year').value+'**'+document.getElementById('cbo_within_group').value+'**'+document.getElementById('txt_fso_no').value+'**'+document.getElementById('txt_booking_no').value+'**'+'<? echo $hidden_fso_id;?>', 'create_fso_no_search_list_view', 'search_div', 'dyeing_unit_wise_production_summary_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
								</td>
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

if($action=="create_fso_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$buyer_id=$data[1];
	$year=$data[2];
	$within_group=$data[3];
	$fso_no=trim($data[4]);
	$booking_no=trim($data[5]);

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	$search_cond = "";

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") 
			{
				$buyer_cond_with_1=" and c.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_cond_with_2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
			} 
			else 
			{
				$buyer_cond_with_1 =  "";
				$buyer_cond_with_2 =  "";
			}
		}
		else
		{
			$buyer_cond_with_1 =  "";
			$buyer_cond_with_2 =  "";
		}
	}
	else
	{
		$buyer_cond_with_1 =  " and c.buyer_id=$data[1]";
		$buyer_cond_with_2 =  " and a.buyer_id=$data[1]";
	}
	

	if($fso_no != "")
	{
		$search_cond .= " and a.job_no like '%$fso_no%'" ;
	}
	if($booking_no != "")
	{
		$search_cond .= " and a.sales_booking_no like '%$booking_no%'" ;
	}
	if($db_type==0)
	{
		if($year!=0) $search_cond .=" and YEAR(a.insert_date)=$year"; else $search_cond .="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year!=0) $search_cond .=" $year_field_con=$year"; else $search_cond .="";
	}

	$sql_2 ="SELECT a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,a.buyer_id
	from fabric_sales_order_mst a, fabric_sales_order_dtls b 
	where a.id = b.mst_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and a.company_id in($company_id) $search_cond and a.within_group = '2' $buyer_cond_with_2
	group by a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,a.buyer_id 
	order by id desc";

	$sql_1 = "SELECT a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,c.buyer_id
	from fabric_sales_order_mst a, fabric_sales_order_dtls b ,wo_booking_mst c
	where a.id = b.mst_id and a.sales_booking_no = c.booking_no and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 
	and b.is_deleted = 0 and a.company_id in($company_id) $search_cond and a.within_group = '1' $buyer_cond_with_1
	group by a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,c.buyer_id";

	if($within_group == 1)
	{
		$sql = $sql_1 ;
	}
	else if($within_group == 2)
	{
		$sql = $sql_2;
	}else
	{
		$sql = $sql_1." union all ". $sql_2 ;
	}
	//echo $sql;
	?>
	
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" align="left">
		<thead>
			<th width="40">SL</th>
			<th width="130">Company</th>
			<th width="120">Buyer</th>
			<th width="150">FSO No</th>
			<th width="">Booking No</th>
		</thead>
	</table>
	<div style="width:700px; overflow-y:scroll; max-height:250px; float: left;" id="buyer_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="682" class="rpt_table" id="tbl_list_search" >
			<?php 
			$i=1; $fso_row_id="";
			$nameArray=sql_select( $sql );
			foreach ($nameArray as $selectResult)
			{
				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";	
				?>

				<tr height="20" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
					<td width="40" align="center"><?php echo "$i"; ?>
					<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $selectResult[csf('id')]; ?>"/>
					<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $selectResult[csf('job_no')]; ?>"/>
				</td>	
				<td width="130"><p><?php echo $company_arr[$selectResult[csf('company_id')]]; ?></p></td>
				<td width="120" title="<? echo $selectResult[csf('buyer_id')];?>"><?php echo $buyer_arr[$selectResult[csf('buyer_id')]]; ?></td>
				<td width="150"><p><?php echo $selectResult[csf('job_no')];?></p></td>
				<td width=""><?php echo $selectResult[csf('sales_booking_no')];?></td> 
			</tr>
			<?
			$i++;
		}
		?>
	</table>
	</div>

	<table width="600" cellspacing="0" cellpadding="0" style="border:none" align="left">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%"> 
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
						<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
					</div>
				</div>
			</td>
		</tr>
	</table>

	<?
	exit(); 
}

if($action=="booking_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array; var selected_name = new Array;
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_div' ).rows.length;
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
		
		function js_set_value2( str ) {
			
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
			
			
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
			$("#hide_booing_type").val(str[3]);
		}
	</script>

	</head>

	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:680px;">
					<table width="670" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Buyer</th>
							<th>Booking Type</th>
							<th>Within Group</th>
							<th>Search By</th>
							<th id="search_by_td_up" width="170">Please Enter Booking No</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
							<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
							<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
							<input type="hidden" name="hide_booing_type" id="hide_booing_type" value="" />

						</thead>
						<tbody>
							<tr>
								<td align="center">
									<? 
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($companyID) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
									?>
								</td>   
								<td align="center">	
									<?
									$search_by_arr=array(1=>"With Order",2=>"Without Order");
									$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
									echo create_drop_down( "cbo_booking_type", 100, $search_by_arr,"",0, "--Select--", "3",$dd,0 );
									?>
								</td>   
								<td><? echo create_drop_down( "cbo_within_group", 80, $yes_no,"", 1, "-- Select --", $selected, "",0,"" );?></td>             
								<td align="center">	
									<?
									$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Booking No");
									$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
									echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "3",$dd,0 );
									?>
								</td>     
								<td align="center" id="search_by_td">				
									<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
								</td> 	
								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+document.getElementById('cbo_booking_type').value+'**'+document.getElementById('cbo_within_group').value, 'create_booking_no_search_list_view', 'search_div', 'dyeing_unit_wise_production_summary_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
								</td>
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

if($action=="create_booking_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	$booking_type=$data[6];
	$within_group=$data[7];

	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{ 
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_id_cond2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else 
			{ 
				$buyer_id_cond="";$buyer_id_cond2="";
			}
		}
		else
		{
			$buyer_id_cond="";$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
		$buyer_id_cond2=" and a.buyer_id=$data[1]";
	}
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==2) 
	{
		$search_field="a.style_ref_no";
		$search_field2="a.style_ref_no";
	}
	else if($search_by==1)
	{
		$search_field="a.job_no_prefix_num";
		$search_field2="a.job_no";
	}
	else 
	{
		$search_field="b.booking_no";
		$search_field2="a.sales_booking_no";
	}
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by="and YEAR(a.insert_date)"; 
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	else $year_field_by="";
	if($db_type==0) $month_field_by="and month(a.insert_date)"; 
	else if($db_type==2) $month_field_by=" and to_char(a.insert_date,'MM')";
	else $month_field_by="";
	if($db_type==0) $year_field=" YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="  to_char(a.insert_date,'YYYY') as year";
	else $year_field="";

	if($year_id!=0) $year_cond=" $year_field_by=$year_id"; else $year_cond="";
	if($month_id!=0) $month_cond=" $month_field_by=$month_id"; else $month_cond="";
	if ($within_group==1) 
	{
		if($booking_type==1)
		{
			$sql= "SELECT a.job_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.booking_no,c.id as booking_id  from wo_po_details_master a,wo_booking_dtls b ,wo_booking_mst c where a.job_no=b.job_no and b.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.booking_type in(1,4,3) and a.company_name in($company_id) and $search_field like '$search_string' $buyer_id_cond $year_cond $month_cond group by a.job_no,b.booking_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,c.id  order by a.job_no desc";
		}
		else
		{
			$sql= "SELECT a.company_id as company_name, a.buyer_id as buyer_name, b.style_des as style_ref_no,a.booking_no,a.id as booking_id  
			from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.booking_type=4 and a.company_id in($company_id) and $search_field like '$search_string' $buyer_id_cond2 $year_cond $month_cond group by a.booking_no,a.company_id, a.buyer_id, b.style_des,a.id  order by a.booking_no desc";
		}
	}
	else
	{
		$sql ="SELECT a.id, a.job_no, a.sales_booking_no as booking_no, a.booking_id,a.within_group,a.company_id as company_name,a.customer_buyer as buyer_name, a.style_ref_no
		from fabric_sales_order_mst a, fabric_sales_order_dtls b 
		where a.id = b.mst_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and a.company_id=$company_id and $search_field2 like '$search_string' $sales_booking_cond and a.within_group = '2'
		group by a.id, a.job_no, a.sales_booking_no, a.booking_id,a.within_group,a.company_id,a.customer_buyer, a.style_ref_no 
		order by id desc";
	}
	

	$sqlResult=sql_select($sql);
	?>

	<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="130">Company</th> 
				<th width="110">Buyer</th>
				<th width="110">Job No</th>
				<th width="120">Style Ref.</th>
				<th width="">Booking No</th>

			</thead>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			foreach($sqlResult as $row )
			{
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

				$data=$i.'_'.$row[csf('booking_id')].'_'.$row[csf('booking_no')].'_'.$booking_type;

				?>
				<tr id="tr_<?php echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="js_set_value2('<? echo $data;?>')">
					<td width="30" align="center"><?php echo $i; ?>
					<td width="130"><p><? echo $company_arr[$row[csf('company_name')]]; ?></p></td>
					<td width="110"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
					<td width="110"><p><? echo $row[csf('job_no')]; ?></p></td>
					<td width="120"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<td width=""><p><? echo $row[csf('booking_no')]; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
		<table width="600" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"/>
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

	<?

	exit(); 
}

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
			
			$('#hide_batch_id').val( id );
			$('#hide_batch_no').val( name );
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
							<input type="hidden" name="hide_batch_no" id="hide_batch_no" value="" />
							<input type="hidden" name="hide_batch_id" id="hide_batch_id" value="" />
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
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_batch_no_search_list_view', 'search_div', 'dyeing_unit_wise_production_summary_report_controller', 'setFilterGrid(\'tbl_list\',-1)');" style="width:100px;" />
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
	
	$batch_no=$data[1];
	$search_string="%".trim($data[3])."%";
	if ($batch_no=="") $batch_no_cond=""; else $batch_no_cond=" and a.batch_no in ('$batch_no') "; 
	$entry_form=" and a.entry_form in(0)";	
	
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

	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$sql="SELECT a.id,a.batch_no,a.batch_for,a.booking_no,a.color_id,a.batch_weight from pro_batch_create_mst a where a.company_id in($company_id) and a.is_deleted=0 and a.status_active=1 $date_cond $batch_no_cond $entry_form";	
	$arr=array(1=>$color_library,3=>$batch_for);
	echo  create_list_view("tbl_list", "Batch no,Color,Booking no, Batch for,Batch weight ", "150,100,150,100,70","700","350",0, $sql, "js_set_value", "id,batch_no", "", 1, "0,color_id,0,batch_for,0", $arr , "batch_no,color_id,booking_no,batch_for,batch_weight", "",'','0','',1) ;
	exit();
}

if($action=="machine_no_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	list($im_data,$floor_id)=explode('_',$data);
	 
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
			
			$('#hid_machine_id').val( id );
			$('#hid_machine_name').val( name );
		}
		
		function hidden_field_reset()
		{
			$('#hid_machine_id').val('');
			$('#hid_machine_name').val( '' );
			selected_id = new Array();
			selected_name = new Array();
		}
	</script>
	</head>
	<input type="hidden" name="hid_machine_id" id="hid_machine_id" />
	<input type="hidden" name="hid_machine_name" id="hid_machine_name" />
	<?
	if($floor_id>0){$whereCon=" and a.floor_id=$floor_id";}
		
	$sql = "select a.id, a.machine_no, a.brand, a.origin, a.machine_group, b.floor_name  from lib_machine_name a, lib_prod_floor b where a.floor_id=b.id and a.category_id=2 and a.company_id in($im_data) and a.status_active in(1,2) and a.is_deleted=0 and a.is_locked=0 $whereCon order by a.machine_no, b.floor_name ";
		 //echo  $sql;

	echo create_list_view("tbl_list_search", "Machine Name,Machine Group,Floor Name", "200,110,110","450","350",0, $sql , "js_set_value", "id,machine_no", "", 1, "0,0,0", $arr , "machine_no,machine_group,floor_name", "",'setFilterGrid(\'tbl_list_search\',-1);','0,0,0','',1) ;

	exit(); 
}

if ($action == "report_generate") 
{	
	$process = array(&$_POST);
	
	extract(check_magic_quote_gpc($process));
	if($db_type==0) $year_field_by="and YEAR(a.insert_date)"; 
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	$company = str_replace("'","",$cbo_company_name);
	$year = str_replace("'", "", $cbo_year);
	$fso_no = trim(str_replace("'","",$fso_no));
	$hidden_fso_no = str_replace("'","",$hidden_fso_no);
	$booking_no = str_replace("'","",$booking_no);
	$hidden_booking_no = str_replace("'","",$hidden_booking_no);
	$batch_no = str_replace("'","",$batch_number_show);
	$batch_number_hidden = str_replace("'","",$batch_number);
	$floor_name=str_replace("'","",$cbo_floor_id);	
	$shift = str_replace("'","",$cbo_shift_name);
	$machine=str_replace("'","",$txt_machine_id);
	$txt_date_from = str_replace("'", "", $txt_date_from);
	$txt_date_to = str_replace("'", "", $txt_date_to);
	$report_type = str_replace("'", "", $report_type);

	if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
	$fsodata=($hidden_fso_no)? " and d.id in (".$hidden_fso_no .")" : '';
	// echo $fsodata;die;
	if($hidden_fso_no=="")
	{
		$fsodata =($fso_no)? " and d.job_no like '%".str_pad($fso_no,5,'0',STR_PAD_LEFT)."%'" : ''; 
		if($year!=0)
		{
			$fsodata.=($year)? " and d.job_no like '%-".substr( $year, -2)."-%'" : ''; 
		}    
	}
	// echo $fsodata;die;
	$hidden_booking_cond="";
	if($booking_no!="")
	{
		$hidden_booking_cond="and a.booking_no like '%$booking_no%' ";
	}
	if ($batch_no=="") $batch_num=""; else $batch_num="  and a.batch_no='".trim($batch_no)."' ";
	if ($floor_name==0 || $floor_name=='')
	{
		$floor_id_cond="";
	}
	else {
		$floor_id_cond=" and f.floor_id=$floor_name";
	}
	if ($shift==0) $shift_name_cond=""; else $shift_name_cond="  and f.shift_name='".$shift."' ";
	if ($machine=="") $machine_cond=""; else $machine_cond =" and f.machine_id in ( $machine ) ";

	$from_date = $txt_date_from;
	if (str_replace("'", "", $txt_date_to) == "") $to_date = $from_date;
	else $to_date = $txt_date_to;

	$date_con = "";$dates_com="";
	if ($from_date != "" && $to_date != "") 
	{
		if($db_type==0)
		{
			$dates_com = "and f.process_end_date between '".change_date_format($from_date, "yyyy-mm-dd", "-")."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$dates_com = "and f.process_end_date between '".change_date_format($from_date,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
		}
	}

	$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
	// =================================================================================
	$sql = "SELECT a.id as batch_id, b.batch_qty, f.shift_name, f.floor_id, f.result, f.fabric_type
	from pro_fab_subprocess f, pro_fab_subprocess_dtls b, pro_batch_create_mst a, fabric_sales_order_mst d 
	where f.batch_id=a.id and f.id=b.mst_id and d.id=a.sales_order_id and f.batch_id=a.id and f.service_company=$company  $dates_com $fsodata $batch_num $year_cond $shift_name_cond $machine_cond $floor_id_cond $hidden_booking_cond 
	and a.entry_form=0 and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,11,2,3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0  and a.is_sales=1 and f.fabric_type is not null";
	// echo $sql;die;
	$batchdata=sql_select($sql);// crm 11156
	if(count($batchdata)==0)
	{
		?>
		<div style="font-weight: bold;color: red;font-size: 20px;text-align: center;">Data not found! Please try again.</div>
		<?
		die();
	}
	foreach ($batchdata as $key => $row) 
	{
		if ($row[csf('result')]==2) // re-dyeing
		{
			$re_dyeing_qty_arr[$row[csf("floor_id")]]['batch_qty']+=$row[csf("batch_qty")];
		}
		else
		{
			$data_arr[$row[csf("fabric_type")]]=$row[csf("fabric_type")];
			$production_qty_arr[$row[csf("fabric_type")]][$row[csf("floor_id")]]['batch_qty']+=$row[csf("batch_qty")];
		}
		$unit_arr[$row[csf('floor_id')]]=$row[csf('floor_id')];
	}
	$count_unit=count($unit_arr);

	$tbl_width = ($count_unit*400)+100;
	ob_start();
	?>
	<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
		<tr>
			<td align="center" width="100%" colspan="<? echo $ship_count + 23; ?>" class="form_caption" style="font-size:18px"><? echo $report_title; ?></td>
		</tr>
		<tr>
			<td align="center" width="100%" colspan="<? echo $ship_count + 23; ?>" class="form_caption" style="font-size:16px"><? echo $company_arr[str_replace("'", "", $cbo_company_name)]; ?></td>
		</tr>
		<tr>
			<td align="center" width="100%" colspan="<? echo $ship_count + 23; ?>" class="form_caption" style="font-size:12px"><strong><? if (str_replace("'", "", $txt_date_from) != "") echo "From " . str_replace("'", "", $txt_date_from);
				if (str_replace("'", "", $txt_date_to) != "") echo " To " . str_replace("'", "", $txt_date_to); ?></strong></td>
		</tr>
	</table>

	<fieldset style="width:<? echo $tbl_width + 20; ?>px;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table">
			<thead>
				<tr>
					<th width="30" rowspan="2">SL</th>
					<th width="150" rowspan="2">Dyeing Category</th>
					<?
					foreach ($unit_arr as $key => $unit) 
                	{
                		?>
                		<th width="100" colspan="2" title="<?=$unit;?>"><? echo $floor_arr[$unit]; ?></th>
                		<?
                	}
					?>
					<th width="100" colspan="2">Total</th>
				</tr>
				<tr>
					<?
					foreach ($unit_arr as $key => $unit) 
                	{
                		?>
                		<th width="100">Qty. Kg</th>	
						<th width="100">% Age</th>
                		<?
                	}
					?>
					<th width="100">Qty. Kg</th>	
					<th width="100">% Age</th>
				</tr>
			</thead>
		</table>
		<div style="width:<?= $tbl_width + 20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
            <table width="<?= $tbl_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<tbody>
					<?
					$dyeing_total=0;$re_dyeing_tot=0;
					foreach ($data_arr as $fabric_type => $row) // for dyeing total qty
					{						
						foreach ($unit_arr as $key => $unit) 
	                	{
	                		$production_qty=$production_qty_arr[$fabric_type][$unit]['batch_qty'];
	                		$unit_wise_dyeing_total[$unit]+=$production_qty;
	                		$dyeing_total+=$production_qty;
	                	}
					}
					foreach ($unit_arr as $key => $unit) // for total re_dyeing_qty
                	{
                		$re_dyeing_qty=$re_dyeing_qty_arr[$unit]['batch_qty'];
                		$unit_wise_re_dyeing_arr[$unit]+=$re_dyeing_qty;
                		$re_dyeing_tot+=$re_dyeing_qty;
                	}
                	// echo $dyeing_total;

					$i = 1;
					// echo "<pre>";print_r($data_arr);
					// $tot_production_qty=0;
					foreach ($data_arr as $fabric_type => $row) 
					{
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="top">
							<td width="30" align="center"><? echo $i;  ?></td>
							<td width="150" title="<? echo $fabric_type; ?>"><? echo $fabric_type_for_dyeing[$fabric_type]; ?></td>

							<?$tot_production_qty=0;
							foreach ($unit_arr as $key => $unit) 
		                	{
		                		$production_qty=$production_qty_arr[$fabric_type][$unit]['batch_qty'];
		                		?>
		                		<td width="100" align="right"><? echo number_format($production_qty,2,'.',''); $tot_production_qty+=$production_qty; ?></td>
								<td width="100" align="right"><? if ($production_qty>0) {
									$parce_age=($production_qty/$unit_wise_dyeing_total[$unit])*100; echo number_format($parce_age,2,'.','').'%';
								}?>
								</td>
		                		<?
		                		$unit_total_batch_qty_arr[$unit]+=$production_qty;
		                		$unit_total_parce_age_arr[$unit]+=$parce_age;
		                	}
							?>	
							<td width="100" align="right"><? echo number_format($tot_production_qty,2,'.','');  ?></td>
							<td width="100" align="right"><? $age_tot=$tot_production_qty/$dyeing_total*100; echo number_format($age_tot,2,'.','').'%'; ?></td>
						</tr>
						<?
						$i++;
						$tot_dyeing_qty+=$tot_production_qty;
						$tot_age+=$age_tot;
					}					
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<th width="30" align="right"></th>
						<th width="150" align="right">Dyeing Total</th>
						<?
						foreach ($unit_arr as $key => $unit) 
	                	{
	                		$unit_total_batch_qty = $unit_total_batch_qty_arr[$unit];
	                		$unit_total_parce_age = $unit_total_parce_age_arr[$unit];
	                		?>
	                		<th width="100" align="right"><? echo number_format($unit_total_batch_qty,2,'.',''); ?></th>
							<th width="100" align="right"><? echo number_format($unit_total_parce_age,2,'.','').'%'; ?></th>
	                		<?
	                	}
						?>
						<th width="100" align="right"><? echo number_format($tot_dyeing_qty,2,'.',''); ?></th>
						<th width="100" align="right"><? echo number_format($tot_age,2,'.','').'%'; ?></th>
					</tr>
					<tr class="tbl_bottom">
						<th width="30" align="right"></th>
						<th width="150" align="right">Reprocess (Re-dyeing)</th>
						<?
						$tot_re_dyeing_qty=0;
						foreach ($unit_arr as $key => $unit) 
	                	{
	                		$re_dyeing_qty=$re_dyeing_qty_arr[$unit]['batch_qty'];
	                		?>
	                		<th width="100" align="right"><? echo number_format($re_dyeing_qty,2,'.',''); 
	                		$tot_re_dyeing_qty+=$re_dyeing_qty; ?></th>
							<th width="100" align="right"><? if ($unit_wise_re_dyeing_arr[$unit]>0) 
							{
								$re_dyeing_parce_age=($unit_wise_re_dyeing_arr[$unit]/$unit_wise_dyeing_total[$unit])*100; echo number_format($re_dyeing_parce_age,2,'.','').'%';
							}?>
							</th>
	                		<?
	                	}
						?>
						<th width="100" align="right"><? echo number_format($tot_re_dyeing_qty,2,'.',''); ?></th>
						<th width="100" align="right"><? echo number_format($tot_re_dyeing_qty/$tot_dyeing_qty*100,2,'.','').'%'; ?></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<br>
	<?
	foreach (glob("../../../ext_resource/tmp_report/$user_id*.xls") as $filename) {
		if (@filemtime($filename) < (time() - $seconds_old))
			@unlink($filename);
	}
	$name = time();
	$filename = "../../../ext_resource/tmp_report/" . $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, ob_get_contents());
	$filename = "../../../ext_resource/tmp_report/" . $user_id . "_" . $name . ".xls";
	echo "$total_data####$filename";
	exit();	
}

?>