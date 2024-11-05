<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
// -----------------------------------------------------------------------------------------------------------------
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
if ($action == "fso_no_popup") 
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>
		var hidden_job_id='<? echo $hidden_job_id; ?>';
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
			
			$('#hidden_job_id').val( id );
			$('#hidden_job_no').val( name );
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
							<input type="hidden" name="hidden_job_no" id="hidden_job_no" value="" />
							<input type="hidden" name="hidden_job_id" id="hidden_job_id" value="" />

						</thead>
						<tbody>
							<tr>
								<td>
									<?
									echo create_drop_down( "cbo_company_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $company_name, "",1 );
									?>
								</td>
								<td align="center">
									<? 
									echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and b.tag_company=$company_name and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
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
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_name; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_year').value+'**'+document.getElementById('cbo_within_group').value+'**'+document.getElementById('txt_fso_no').value+'**'+document.getElementById('txt_booking_no').value+'**'+'<? echo $hidden_fso_id;?>', 'create_fso_no_search_list_view', 'search_div', 'dyeing_and_finishing_cost_sales_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

	$sql_2 ="select a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,a.buyer_id
	from fabric_sales_order_mst a, fabric_sales_order_dtls b 
	where a.id = b.mst_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and a.company_id = $company_id $search_cond and a.within_group = '2' $buyer_cond_with_2
	group by a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,a.buyer_id 
	order by id desc";

	$sql_1 = "select a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,c.buyer_id
	from fabric_sales_order_mst a, fabric_sales_order_dtls b ,wo_booking_mst c
	where a.id = b.mst_id and a.sales_booking_no = c.booking_no and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 
	and b.is_deleted = 0 and a.company_id = $company_id $search_cond and a.within_group = '1' $buyer_cond_with_1
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
	
	$batch_no=$data[1];
	$search_string="%".trim($data[3])."%";

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

	
	$sql="select a.id,a.batch_no,a.batch_for,a.booking_no,a.color_id,a.batch_weight from pro_batch_create_mst a where a.company_id=$company_id and a.is_deleted=0 and a.status_active=1 and a.entry_form in(0,36) $date_cond $batch_no_cond";	
	$arr=array(1=>$color_library,3=>$batch_for);
	echo  create_list_view("tbl_list", "Batch no,Color,Booking no, Batch for,Batch weight ", "150,100,150,100,70","700","350",0, $sql, "js_set_value", "id,batch_no", "", 1, "0,color_id,0,batch_for,0", $arr , "batch_no,color_id,booking_no,batch_for,batch_weight", "",'','0','',1) ;
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company = str_replace("'","",$cbo_company_name);
	$issue_type = str_replace("'","",$cbo_issue_type);
	$batch_number = str_replace("'","",$batch_number_show);
	$batch_id = str_replace("'","",$batch_id);
	$fso_no = str_replace("'","",$txt_job_no);
	$hidden_fso_id = str_replace("'","",$txt_job_hidden_id);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$year = str_replace("'","",$cbo_year_selection);

	if($db_type==0) $year_field_by="and YEAR(a.insert_date)"; 
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	if($db_type==0) $field_concat="concat(machine_no,'-',brand) as machine_name"; 
	else if($db_type==2) $field_concat="machine_no || '-' || brand as machine_name";
	if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";


	$issue_fso_cond=($hidden_fso_id)? " and a.order_id like '%$hidden_fso_id%'" : '';
	$fsodata=($hidden_fso_id)? " and d.id in (".$hidden_fso_id .")" : '';
	if($hidden_fso_id=="")
	{
		$fsodata =($fso_no)? " and d.job_no like '%$fso_no%'" : '';     
	}

	if ($batch_number=="") $batch_num=""; else $batch_num="  and a.batch_no='".trim($batch_number)."' ";
	if ($batch_number=="") $issue_batch_num=""; else $issue_batch_num="  and d.batch_no='".trim($batch_number)."' ";
	if ($fso_no=="") $issue_fso_no_cond=""; else $issue_fso_no_cond="  and d.sales_order_no='".trim($fso_no)."' ";


	$yarncount_arr = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
	$supplier_name_arr = return_library_array("select id, supplier_name from lib_supplier where status_active=1", 'id', 'supplier_name');
	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}

	if ($ext_num=="") $ext_no=""; else $ext_no="  and a.extention_no=$ext_num ";
	if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
	if($txt_date_from && $txt_date_to)
	{
		if($db_type==0)
		{
			$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');

			$dates_com=" and  f.process_end_date BETWEEN '$date_from' AND '$date_to' ";
			$issue_dates=" and  a.issue_date BETWEEN '$date_from' AND '$date_to' ";
		}
		elseif($db_type==2)
		{
			$date_from=change_date_format($txt_date_from,'','',1);
			$date_to=change_date_format($txt_date_to,'','',1);

			$dates_com="and  f.process_end_date BETWEEN '$date_from' AND '$date_to'";
			$issue_dates="and  a.issue_date BETWEEN '$date_from' AND '$date_to'";
		}
	}

	$machine_result=sql_select("select id,prod_capacity,$field_concat from  lib_machine_name where status_active=1 and  category_id = 2 order by seq_no ");

	foreach($machine_result as $row)
	{
		$machine_arr[$row[csf('id')]]=$row[csf('machine_name')];
		$machine_capacity_arr[$row[csf('id')]]=$row[csf('prod_capacity')];
		$total_machine_arr[$row[csf('id')]]=$row[csf('id')];
	}
	$floor_arr=return_library_array( "select id,floor_name from  lib_prod_floor",'id','floor_name');
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	$con = connect();
    $r_id=execute_query("delete from tmp_batch_or_iss where userid=$user_name");
    oci_commit($con);

    if ($issue_type==2) // With issue
	{
		if ($batch_number!="") 
		{
			$sql_batch =sql_select("SELECT a.id as batch_id, a.batch_no from pro_batch_create_mst a
			where a.status_active=1 and a.is_deleted=0 $batch_num");
			$dyes_chemical_arr=array();
			foreach($sql_batch as $row)
			{
				$batch_id_arr[$row[csf("batch_id")]]=$row[csf("batch_id")];
			}
			unset($sql_batch);
		}
		if (!empty($batch_id_arr)) 
		{
			$batch_id_cond = " and a.batch_no like '%".implode(',', $batch_id_arr)."%'";
		}
		$sql_chemical_issue =sql_select("SELECT a.issue_number, a.order_id, a.batch_no, a.issue_basis
		from inv_issue_master a, inv_transaction b, dyes_chem_issue_dtls c 
		where a.id=b.mst_id and a.id=c.mst_id and b.id=c.trans_id and b.transaction_type=2 and a.entry_form=5 and a.batch_no is not null and b.item_category in (5,6,7)  $issue_dates $issue_fso_cond $batch_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.issue_number, a.order_id, a.batch_no, a.issue_basis");
		$dyes_chemical_arr=array();
		foreach($sql_chemical_issue as $val)
		{
			$all_batch_no=explode(",",$val[csf("batch_no")]);
			foreach ($all_batch_no as $key => $batchId) 
			{
				$issue_batch_id_arr[$batchId]=$batchId;
			}
		}
		unset($sql_chemical_issue);

		$issue_batch_cond = " and a.id in(".implode(',', $issue_batch_id_arr).")";
	}
	// echo "<pre>";print_r($issue_batch_id_arr);die;
	// echo "<pre>";print_r($issue_batch_cond);die;
	// ==========================================Inhouse Batch Start============================================
	$inhouse_batch_sql = "SELECT f.insert_date, a.batch_no, a.batch_weight, a.id as batch_id,a.sales_order_id, a.batch_against,a.color_id, a.color_range_id, a.extention_no, a.total_trims_weight, a.process_id as process_name, (b.batch_qnty) as batch_qnty, b.program_no,b.item_description, b.po_id, b.prod_id,b.body_part_id, b.width_dia_type, d.job_no_prefix_num, d.buyer_id, d.po_buyer, f.remarks, f.shift_name, f.production_date as process_end_date, f.process_end_date as production_date, f.end_hours,f.system_no, f.floor_id,f.multi_batch_load_id, f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id, f.fabric_type,f.responsibility_id, f.result, a.booking_no, d.booking_without_order, d.season, d.job_no  as sales_no,d.style_ref_no , b.barcode_no, f.process_id, f.ltb_btb_id, d.within_group, d.booking_id
	from pro_batch_create_dtls b, fabric_sales_order_mst d, pro_fab_subprocess f, pro_batch_create_mst a 
	where f.batch_id=a.id and a.company_id=$company $dates_com $fsodata $batch_num $year_cond $issue_batch_cond and a.entry_form=0  and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,11,2,3) and b.po_id=d.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.is_sales=1 and b.is_sales=1 and f.result=1";
	// echo $inhouse_batch_sql;die;

	$inhouse_batchdata=sql_select($inhouse_batch_sql);
	foreach($inhouse_batchdata as $row)
	{
		$all_prog_idArr[$row[csf("program_no")]]=$row[csf("program_no")];
		$all_batch[$row[csf("batch_id")]]=$row[csf("batch_id")];
		$all_barcode[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		$sales_order_idArr[$row[csf("sales_order_id")]]=$row[csf("sales_order_id")];
		if($row[csf("booking_id")]!="") $all_booking_id[$row[csf("booking_id")]]=$row[csf("booking_id")];

		if( $batch_id_check[$row[csf('batch_id')]] =="" )
        {
            $batch_id_check[$row[csf('batch_id')]]=$row[csf('batch_id')];
            $batch_id = $row[csf('batch_id')];
            // echo "insert into tmp_batch_or_iss (userid, batch_issue_id, batch_issue_no, type, entry_form) values ($user_name,$batch_id,'$batch_id',1,0)";
            $r_id=execute_query("insert into tmp_batch_or_iss (userid, batch_issue_id, batch_issue_no, type, entry_form) values ($user_name,$batch_id,'$batch_id',1,0)");
        }
	}
	oci_commit($con);
	// $all_batch_ids= "'".implode("','",array_filter(array_unique($all_batch)))."'";
	$all_batch_ids= implode(",",array_filter(array_unique($all_batch)));
	//print_r($sales_order_idArr);
	//if($all_batch_ids=="") echo "Data Not Found";die;

	$bookingCond = $dyeing_booking_id_cond=""; 
	if($db_type==2 && count($all_booking_id)>999)
	{
		$all_booking_chunk=array_chunk($all_booking_id,999) ;
		foreach($all_booking_chunk as $chunk_arr)
		{
			$bookingCond.=" id in(".implode(",",$chunk_arr).") or ";	
		}
		$dyeing_booking_id_cond.=" and (".chop($bookingCond,'or ').")";	
	}
	else $dyeing_booking_id_cond=" and id in(".implode(",",$all_booking_id).")";

	$sql_booking_type="SELECT booking_no, booking_type, is_short,1 as with_order_status from wo_booking_mst where status_active=1 and is_deleted=0 $dyeing_booking_id_cond
	union all select booking_no, booking_type, is_short, 2 as with_order_status from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0 $dyeing_booking_id_cond";
	$sql_booking_type_data=sql_select($sql_booking_type);
	foreach ($sql_booking_type_data as $value) 
	{
		if($value[csf("booking_type")]==1 && $value[csf("is_short")]==2) $booking_type_arr[$value[csf("booking_no")]]="Main";
		else if($value[csf("booking_type")]==1 && $value[csf("is_short")]==1) $booking_type_arr[$value[csf("booking_no")]]="Short";
		else if($value[csf("booking_type")]==4) $booking_type_arr[$value[csf("booking_no")]]="Sample";
	}
	//====================
	$batchCond3 = $all_batch_no_cond3 = "";  $batchCond2 = $all_batch_no_cond2 = ""; 
	$all_batch_arr=explode(",",$all_batch_ids);
	if($db_type==2 && count($all_batch_arr)>999)
	{
		$all_batch_chunk=array_chunk($all_batch_arr,999) ;
		foreach($all_batch_chunk as $chunk_arr)
		{
			$batchCond2.=" a.batch_id in(".implode(",",$chunk_arr).") or ";	
			$batchCond3.=" a.batch_id in(".implode(",",$chunk_arr).") or ";	
		}
		$all_batch_no_cond2.=" and (".chop($batchCond2,'or ').")";			
		$all_batch_no_cond3.=" and (".chop($batchCond3,'or ').")";			
	}
	else
	{ 	
		$all_batch_no_cond2=" and a.batch_id in($all_batch_ids)";
		$all_batch_no_cond3=" and a.batch_id in($all_batch_ids)";
	}
	//echo $all_batch_no_cond3.'DD';
	//=======================	
	$sales_cond_for_in=where_con_using_array($sales_order_idArr,0,"a.id");
	$req_batch_cond_for_in=where_con_using_array($all_batch_arr,0,"a.batch_id");
	$progId_batch_cond_for_in=where_con_using_array($all_prog_idArr,0,"b.id");
	
	if (!empty($all_prog_idArr)) 
	{
		$sql_prog="SELECT b.id as program_id, b.id as program_no,a.color_type_id FROM ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b WHERE a.dtls_id=b.id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.is_sales=1 $progId_batch_cond_for_in";
		$result_prog=sql_select($sql_prog);
		foreach ($result_prog as $row) 
		{
			$prog_sales_arr[$row[csf("program_id")]]= $row[csf("color_type_id")];
		}
		// echo "<pre>"; print_r($prog_sales_arr);die;
	}

	if (!empty($all_batch)) // in-hounse batch
	{
		$sql_prod_sales= sql_select("SELECT b.id as dtls_id,a.id,a.batch_id,a.multi_batch_load_id,a.load_unload_id, b.prod_id, b.const_composition, (b.batch_qty) as batch_qty, (b.production_qty) as production_qty,c.body_part_id,c.po_id as sales_id,c.program_no 
		from  pro_fab_subprocess a, pro_fab_subprocess_dtls b,pro_batch_create_dtls c, tmp_batch_or_iss d
		where a.id = b.mst_id and c.mst_id=a.batch_id and b.prod_id=c.prod_id and a.batch_id=d.batch_issue_id and d.type=1 and d.entry_form=0 and d.userid=$user_name and a.load_unload_id in(2) and b.load_unload_id in(2) and b.entry_page=35 and a.status_active = 1 and b.status_active=1");// $all_batch_no_cond2
		foreach ($sql_prod_sales as $row) 
		{
			$program_no=$row[csf("program_no")];
			if($dtlsChkArr[$row[csf("dtls_id")]]=='')
			{
				$color_typeId=$prog_sales_arr[$program_no];
				$dtlsChkArr[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
			}
			$sales_batch_product_arr[$row[csf("batch_id")]][$color_typeId] += $row[csf("production_qty")];
			$prog_colorType_arr[$row[csf("batch_id")]][$row[csf("prod_id")]] = $program_no;
		}
		//print_r($sales_batch_product_arr);
		unset($sql_prod_sales);

		$sql_prod_ref= sql_select("SELECT a.id,a.batch_id,a.multi_batch_load_id,a.load_unload_id, b.prod_id, b.const_composition, (b.batch_qty) as batch_qty, (b.production_qty) as production_qty
		from  pro_fab_subprocess a, pro_fab_subprocess_dtls b, tmp_batch_or_iss c
		where a.id = b.mst_id and a.batch_id=c.batch_issue_id and c.type=1 and c.entry_form=0 and c.userid=$user_name and a.load_unload_id in(1,2) and b.load_unload_id in(1,2) and b.entry_page=35 and a.status_active = 1 and b.status_active=1"); // and a.batch_id in ($all_batch_ids)  $all_batch_no_cond2

		foreach ($sql_prod_ref as $val) 
		{
			if($val[csf("load_unload_id")]==2)
			{
				$program_no=$prog_colorType_arr[$val[csf("batch_id")]][$val[csf("prod_id")]];
				$colortype=$prog_sales_arr[$program_no];
				$batch_product_arr[$val[csf("batch_id")]][$val[csf("prod_id")]] += $val[csf("production_qty")];
				$batch_product_arr2[$val[csf("batch_id")]]["batch_prod_tot"] += $val[csf("production_qty")];
				$batch_product_arr3[$val[csf("batch_id")]][$val[csf("prod_id")]][$colortype] += $val[csf("production_qty")];
			}
			else
			{
				$multi_batch_arr[$val[csf("batch_id")]]= $val[csf("multi_batch_load_id")];
			}
		}
		// echo "<pre>"; print_r($batch_product_arr);
	}

	$yarn_lot_arr=array();
	$all_barcode_nos = implode(",", array_filter($all_barcode));
	if($to_poids=="") $to_poids=0;
	$barCond = $barcode_cond = ""; 
	$all_barcode_arr=explode(",",$all_barcode_nos);
	if(count($all_barcode_arr)>1)
	{
		if($db_type==2 && count($all_barcode_arr)>999)
		{
			$all_barcode_chunk=array_chunk($all_barcode_arr,999) ;
			foreach($all_barcode_chunk as $chunk_arr)
			{
				$barCond.=" b.barcode_no in(".implode(",",$chunk_arr).") or ";	
			}
			$barcode_cond.=" and (".chop($barCond,'or ').")";
		}
		else
		{ 	
			$barcode_cond=" and b.barcode_no in($all_barcode_nos)";
		}
		$yarn_lot_data=sql_select("SELECT b.barcode_no, a.prod_id,c.
		yarn_comp_type1st,c.yarn_comp_percent1st, c.yarn_count_id,c.yarn_type,c.color,c.lot from ppl_yarn_requisition_entry a, pro_roll_details b, product_details_master c where b.entry_form=2 and b.receive_basis=2 and cast(a.knit_id as varchar2(4000)) = b.booking_no and a.prod_id = c.id and c.item_category_id=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 $barcode_cond");

		foreach($yarn_lot_data as $row)
		{ 
			$yarn_lot_arr[$row[csf('barcode_no')]] .=$row[csf('lot')].",";
			if($row[csf('yarn_count_id')]>0)
			{
			$yarn_data_arr[$row[csf('barcode_no')]]['yarn_count_id'] .=$yarncount_arr[$row[csf('yarn_count_id')]].",";
			}
			if($row[csf('yarn_type')]>0)
			{
			$yarn_data_arr[$row[csf('barcode_no')]]['yarn_type'] .=$yarn_type[$row[csf('yarn_type')]].",";
			}
			if($row[csf('color')]>0)
			{
			$yarn_data_arr[$row[csf('barcode_no')]]['color'] .=$color_library[$row[csf('color')]].",";
			}
			if($row[csf('yarn_comp_type1st')]>0)
			{
			$yarn_data_arr[$row[csf('barcode_no')]]['yarn_comp_type1st'] .=$composition[$row[csf('yarn_comp_type1st')]].",";
			}
		}
	}
	$dyes_cat_arr=6;$chemical_cat_arr=array(5,7);
	foreach($inhouse_batchdata as $row)
	{
		$program_no=$row[csf("program_no")];
		$color_typeId=$prog_sales_arr[$program_no];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["insert_date"] = $row[csf("insert_date")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["batch_no"] = $row[csf("batch_no")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["system_no"] = $row[csf("system_no")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["batch_weight"] = $row[csf("batch_weight")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["color_id"] = $row[csf("color_id")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["color_type"] .= $color_typeId.',';
		$inhouse_batch_data_array[$row[csf("batch_id")]]["color_range_id"] = $row[csf("color_range_id")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["extention_no"] = $row[csf("extention_no")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["total_trims_weight"] = $row[csf("total_trims_weight")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["batch_qnty"] += $row[csf("batch_qnty")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["item_description"] .= $row[csf("item_description")].',';
		$inhouse_batch_data_array[$row[csf("batch_id")]]["po_id"] = $row[csf("po_id")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["body_part"] .= $row[csf("body_part_id")].',';
		$inhouse_batch_data_array[$row[csf("batch_id")]]["width_dia_type"] = $row[csf("width_dia_type")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["job_no_prefix_num"] = $row[csf("job_no_prefix_num")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["remarks"] = $row[csf("remarks")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["shift_name"] = $row[csf("shift_name")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["within_group"] = $row[csf("within_group")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["po_buyer"] = $row[csf("po_buyer")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["buyer_id"] = $row[csf("buyer_id")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["process_end_date"] = $row[csf("process_end_date")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["production_date"] = $row[csf("production_date")];
		// $inhouse_batch_data_array[$row[csf("batch_id")]]["production_qnty"] = $batch_product_arr[$row[csf("batch_id")]][$row[csf("prod_id")]];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["production_qnty"] += $row[csf("batch_qnty")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["prod_qnty_tot"] = $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["end_hours"] = $row[csf("end_hours")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["floor_id"] = $row[csf("floor_id")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["hour_unload_meter"] = $row[csf("hour_unload_meter")]; 
		$inhouse_batch_data_array[$row[csf("batch_id")]]["water_flow_meter"] = $row[csf("water_flow_meter")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["end_minutes"] = $row[csf("end_minutes")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["machine_id"] = $row[csf("machine_id")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["load_unload_id"] = $row[csf("load_unload_id")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["fabric_type"] = $row[csf("fabric_type")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["result"] = $row[csf("result")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["booking_no"] = $row[csf("booking_no")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["sales_no"] = $row[csf("sales_no")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["booking_without_order"] = $row[csf("booking_without_order")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["season"] = $row[csf("season")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["style_ref_no"] = $row[csf("style_ref_no")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["barcode_no"][] = $row[csf("barcode_no")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["process_id"] = $row[csf("process_id")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["required_process_name"] = $row[csf("process_name")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["ltb_btb_id"] = $row[csf("ltb_btb_id")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["multi_batch_load_id"] = $row[csf("multi_batch_load_id")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["batch_againstId"] = $row[csf("batch_against")];
		$inhouse_batch_data_array[$row[csf("batch_id")]]["add_tp_strip"] = $add_tp_stri_batch_arr[$row[csf("batch_id")]];
		
		$ttl_batch_prod_tot_arr[$row[csf("batch_id")]]['prod_qty']=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
		$ttl_batch_prod_tot_arr[$row[csf("batch_id")]]['trims']=$row[csf("total_trims_weight")];
		
		$machine_id_arr[$row[csf("batch_id")]]['machine_id']=$row[csf("machine_id")];
		$multi_batch_load_id=$multi_batch_arr[$row[csf("batch_id")]];
		//echo $row[csf("batch_against")].'dsd';
		if($row[csf("extention_no")]=='') $row[csf("extention_no")]=0;
	}
	// echo "<pre>";print_r($inhouse_batch_data_array);die;
	// ==========================================Inhouse Batch End============================================
	   /////////////////////////////////////////	**********  //////////////////////////////////////////////
	// ==========================================Inbound Batch Start==========================================
	$sql_subcon="SELECT f.insert_date, a.batch_no, a.batch_weight,a.batch_against, a.id as batch_id, a.color_id, a.color_range_id, a.extention_no, a.total_trims_weight, b.batch_qnty AS sub_batch_qnty, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.subcon_job as job_no_prefix_num, d.party_id as buyer_id, d.party_id as po_buyer, f.remarks, f.shift_name, f.production_date as process_end_date,f.system_no,f.responsibility_id, f.process_end_date as production_date, f.end_hours, f.floor_id,f.multi_batch_load_id, f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id, f.fabric_type, f.result, 0 as season, c.cust_style_ref as style_ref_no, 0 as barcode_no, f.process_id, f.ltb_btb_id, 0 as within_group, 0 as booking_id 
	from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a, pro_fab_subprocess f
	where f.batch_id=a.id and f.batch_id=b.mst_id and a.company_id=$company and a.entry_form=36 and a.id=b.mst_id and f.entry_form=38 and f.load_unload_id=2 and a.batch_against in(1,11,2,3) and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and f.result=1 $dates_com $batch_num $fsodata $year_con $issue_batch_cond";
	// and a.batch_no='QC-Sub-Contact'
	// echo $sql_subcon;die;
	$subbatchdata=sql_select($sql_subcon); $all_sub_batch=array();
	foreach($subbatchdata as $row) 
	{
		$all_sub_batch[$row[csf("batch_id")]]=$row[csf("batch_id")];

		if( $batch_id_check[$row[csf('batch_id')]] =="" )
        {
            $batch_id_check[$row[csf('batch_id')]]=$row[csf('batch_id')];
            $batch_id = $row[csf('batch_id')];
            // echo "insert into tmp_batch_or_iss (userid, batch_issue_id, batch_issue_no, type, entry_form) values ($user_name,$batch_id,'$batch_id',2,36)";
            $r_id2=execute_query("insert into tmp_batch_or_iss (userid, batch_issue_id, batch_issue_no, type, entry_form) values ($user_name,$batch_id,'$batch_id',2,36)");
        }
	}
	oci_commit($con);

	$all_sub_batch_ids=implode(",",$all_sub_batch);
	//====================

	if (!empty($all_sub_batch)) 
	{
		$batchCond =$sub_dyeing_batch_id_cond= "";  $subbatchCond2 = $all_sub_batch_no_cond2 = ""; 
		$all_sub_batch_arr=explode(",",$all_sub_batch_ids);
		if($db_type==2 && count($all_sub_batch_arr)>999)
		{
			$all_batch_chunk=array_chunk($all_sub_batch_arr,999) ;
			foreach($all_batch_chunk as $chunk_arr)
			{
				//$batchCond.=" f.batch_id in(".implode(",",$chunk_arr).") or ";	
				$subbatchCond2.=" a.batch_id in(".implode(",",$chunk_arr).") or ";	
			}
			$sub_dyeing_batch_id_cond.=" and (".chop($batchCond,'or ').")";			
			$all_sub_batch_no_cond2.=" and (".chop($subbatchCond2,'or ').")";			
		}
		else
		{
			$sub_dyeing_batch_id_cond=" and f.batch_id in($all_sub_batch_ids)";
			$all_sub_batch_no_cond2=" and a.batch_id in($all_sub_batch_ids)";
		}
		//=======================
		$sub_sql_prod_ref= sql_select("SELECT a.batch_id,a.multi_batch_load_id,a.load_unload_id,b.prod_id,(b.batch_qnty) as production_qty
		from  pro_fab_subprocess a,pro_batch_create_dtls b
		where a.batch_id =b.mst_id and a.load_unload_id in(1,2) and a.load_unload_id in(1,2) and a.entry_form=38 and a.status_active=1 and b.status_active=1 $all_sub_batch_no_cond2 "); // and a.batch_id in ($all_batch_ids)
		foreach ($sub_sql_prod_ref as $val) 
		{
			if($val[csf("load_unload_id")]==2)
			{
			$sub_batch_product_arr[$val[csf("batch_id")]][$val[csf("prod_id")]] = $val[csf("production_qty")];
			$sub_batch_product_arr2[$val[csf("batch_id")]]["batch_prod_tot"] += $val[csf("production_qty")];
			}
			else
			{
				$multi_batch_arr[$val[csf("batch_id")]] = $val[csf("multi_batch_load_id")];
			}
		}
		//print_r($batch_product_arr2);
	}

	//$yarn_lot_arr=array();
	//	print_r($color_wise_batch_arr);
	$subbatch_data_arr=array();
	$sub_total_color_wise_batch_qty=$sub_tot_machine_capacity=0;
	$dyes_cat_arr=6;$chemical_cat_arr=array(5,7);
	foreach($subbatchdata as $row)
	{
		$subbatch_data_arr[$row[csf("batch_id")]]["insert_date"] = $row[csf("insert_date")];
		$subbatch_data_arr[$row[csf("batch_id")]]["batch_no"] = $row[csf("batch_no")];
		$subbatch_data_arr[$row[csf("batch_id")]]["system_no"] = $row[csf("system_no")];
		$subbatch_data_arr[$row[csf("batch_id")]]["batch_weight"] = $row[csf("batch_weight")];
		$subbatch_data_arr[$row[csf("batch_id")]]["color_id"] = $row[csf("color_id")];
		$subbatch_data_arr[$row[csf("batch_id")]]["color_range_id"] = $row[csf("color_range_id")];
		$subbatch_data_arr[$row[csf("batch_id")]]["extention_no"] = $row[csf("extention_no")];
		$subbatch_data_arr[$row[csf("batch_id")]]["total_trims_weight"] = $row[csf("total_trims_weight")];
		$subbatch_data_arr[$row[csf("batch_id")]]["batch_qnty"] += $row[csf("sub_batch_qnty")];
		$subbatch_data_arr[$row[csf("batch_id")]]["item_description"] .= $row[csf("item_description")].',';
		$subbatch_data_arr[$row[csf("batch_id")]]["po_id"] = $row[csf("po_id")];
		$subbatch_data_arr[$row[csf("batch_id")]]["width_dia_type"] = $row[csf("width_dia_type")];
		$subbatch_data_arr[$row[csf("batch_id")]]["job_no_prefix_num"] = $row[csf("job_no_prefix_num")];
		$subbatch_data_arr[$row[csf("batch_id")]]["remarks"] = $row[csf("remarks")];
		$subbatch_data_arr[$row[csf("batch_id")]]["shift_name"] = $row[csf("shift_name")];
		$subbatch_data_arr[$row[csf("batch_id")]]["within_group"] = $row[csf("within_group")];
		$subbatch_data_arr[$row[csf("batch_id")]]["po_buyer"] = $row[csf("po_buyer")];
		$subbatch_data_arr[$row[csf("batch_id")]]["buyer_id"] = $row[csf("buyer_id")];
		$subbatch_data_arr[$row[csf("batch_id")]]["process_end_date"] = $row[csf("process_end_date")];
		$subbatch_data_arr[$row[csf("batch_id")]]["production_date"] = $row[csf("production_date")];
		$subbatch_data_arr[$row[csf("batch_id")]]["production_qnty"] += $row[csf("sub_batch_qnty")];
		$subbatch_data_arr[$row[csf("batch_id")]]["prod_qnty_tot"] += $sub_batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
		//echo $sub_batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"].'jj';
		$subbatch_data_arr[$row[csf("batch_id")]]["end_hours"] = $row[csf("end_hours")];
		$subbatch_data_arr[$row[csf("batch_id")]]["floor_id"] = $row[csf("floor_id")];
		$subbatch_data_arr[$row[csf("batch_id")]]["hour_unload_meter"] = $row[csf("hour_unload_meter")];
		$subbatch_data_arr[$row[csf("batch_id")]]["water_flow_meter"] = $row[csf("water_flow_meter")];
		$subbatch_data_arr[$row[csf("batch_id")]]["end_minutes"] = $row[csf("end_minutes")];
		$subbatch_data_arr[$row[csf("batch_id")]]["machine_id"] = $row[csf("machine_id")];
		$subbatch_data_arr[$row[csf("batch_id")]]["load_unload_id"] = $row[csf("load_unload_id")];
		$subbatch_data_arr[$row[csf("batch_id")]]["fabric_type"] = $row[csf("fabric_type")];
		$subbatch_data_arr[$row[csf("batch_id")]]["result"] = $row[csf("result")];
		$subbatch_data_arr[$row[csf("batch_id")]]["booking_no"] = $row[csf("booking_no")];
		$subbatch_data_arr[$row[csf("batch_id")]]["booking_without_order"] = $row[csf("booking_without_order")];
		$subbatch_data_arr[$row[csf("batch_id")]]["season"] = $row[csf("season")];
		$subbatch_data_arr[$row[csf("batch_id")]]["style_ref_no"] = $row[csf("style_ref_no")];
		$subbatch_data_arr[$row[csf("batch_id")]]["barcode_no"][] = $row[csf("barcode_no")];
		$subbatch_data_arr[$row[csf("batch_id")]]["process_id"] = $row[csf("process_id")];
		$subbatch_data_arr[$row[csf("batch_id")]]["ltb_btb_id"] = $row[csf("ltb_btb_id")];
		$subbatch_data_arr[$row[csf("batch_id")]]["multi_batch_load_id"] = $row[csf("multi_batch_load_id")];
		$subbatch_data_arr[$row[csf("batch_id")]]["batch_against"] = $row[csf("batch_against")];
		$subbatch_data_arr[$row[csf("batch_id")]]["add_tp_strip"] = $add_tp_stri_batch_arr[$row[csf("batch_id")]];

		$multi_batch_load_id=$multi_batch_arr[$row[csf("batch_id")]];
		$batch_againstId=$row[csf("batch_against")];
		$sub_ttl_batch_prod_tot_arr[$row[csf("batch_id")]]["prod_qty"]= $row[csf("sub_batch_qnty")];
		$sub_ttl_batch_prod_tot_arr[$row[csf("batch_id")]]["trims"]=$row[csf("total_trims_weight")];
	}
	// echo "<pre>"; echo print_r($subbatch_data_arr);die;
	// =======================================================================================================
	// ==========================================Inbound Batch End============================================
	// =======================================================================================================

	// =======================================================================================================
	// ==========================================Batch Based Batch Start======================================
	// =======================================================================================================
	$batch_based_sql =sql_select("SELECT a.batch_no, a.issue_basis, a.issue_purpose, a.knit_dye_source ,c.sub_process,b.item_category, b.cons_amount as dyes_chemical_cost
	from inv_issue_master a, inv_transaction b, dyes_chem_issue_dtls c, pro_batch_create_mst d
	where a.id=b.mst_id and a.id=c.mst_id and b.id=c.trans_id
	and cast(d.id as varchar2(4000)) = a.batch_no $issue_dates $issue_batch_num $issue_fso_no_cond
	and b.transaction_type=2 and a.entry_form=5 and a.issue_basis=5 and c.sub_process!=92 and a.batch_no  is not null  and   b.item_category in (5,6,7)");
	// and d.batch_no='RS-5-Batch' and d.sales_order_no='UG-FSOE-22-00009'
	$batch_based_dyes_chemical_arr=array(); $batch_based_data_arr=array();
	foreach($batch_based_sql as $val)
	{
		$sub_process=$val[csf("sub_process")];
		$all_batch_no=explode(",",$val[csf("batch_no")]);
		foreach ($all_batch_no as $key => $batchId) 
		{
			$batch_based_data_arr[$batchId]['issue_basis']= $receive_basis_arr[$val[csf("issue_basis")]];
			$batch_based_data_arr[$batchId]['issue_purpose']= $yarn_issue_purpose[$val[csf("issue_purpose")]];
			$batch_based_data_arr[$batchId]['issue_source']= $val[csf("knit_dye_source")];
			if($sub_process!=92)
			{
				$batch_based_dyes_chemical_arr[$batchId][$val[csf("item_category")]]['chemical_cost']+=$val[csf("dyes_chemical_cost")];
			}
			else
			{
				$batch_based_dyes_chemical_arr[$batchId][$val[csf("item_category")]]['chemical_cost_finish']+=$val[csf("dyes_chemical_cost")];
			}
			$batch_based_all_batch[$batchId]=$batchId;
			// echo $batchId;
			if( $batch_based_id_check[$batchId] =="" )
	        {
	            $batch_based_id_check[$batchId]=$batchId;
	            $batch_id = $batchId;
	            // echo "insert into tmp_batch_or_iss (userid, batch_issue_id, batch_issue_no, type, entry_form) values ($user_name,$batch_id,'$batch_id',1,0)";
	            $r_id3=execute_query("insert into tmp_batch_or_iss (userid, batch_issue_id, batch_issue_no, type, entry_form) values ($user_name,$batch_id,'$batch_id',3,5)");
	        }
		}
	}
	oci_commit($con);
	unset($batch_based_sql);

	if (!empty($batch_based_all_batch))
	{
		$batch_based_sql_prod_sales= sql_select("SELECT b.id as dtls_id,a.id,a.batch_id,a.multi_batch_load_id,a.load_unload_id, b.prod_id, b.const_composition, (b.batch_qty) as batch_qty, (b.production_qty) as production_qty,c.body_part_id,c.po_id as sales_id,c.program_no 
		from  pro_fab_subprocess a, pro_fab_subprocess_dtls b,pro_batch_create_dtls c, tmp_batch_or_iss d
		where a.id = b.mst_id and c.mst_id=a.batch_id and b.prod_id=c.prod_id and a.batch_id=d.batch_issue_id and d.type=3 and d.entry_form=5 and d.userid=$user_name and a.load_unload_id in(2) and b.load_unload_id in(2) and b.entry_page=35 and a.status_active = 1 and b.status_active=1");// $all_batch_no_cond2
		foreach ($batch_based_sql_prod_sales as $row) 
		{
			$program_no=$row[csf("program_no")];
			if($dtlsChkArr[$row[csf("dtls_id")]]=='')
			{
				$color_typeId=$batch_based_prog_sales_arr[$program_no];
				$dtlsChkArr[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
			}
			$sales_batch_product_arr[$row[csf("batch_id")]][$color_typeId] += $row[csf("production_qty")];
			$batch_based_prog_colorType_arr[$row[csf("batch_id")]][$row[csf("prod_id")]] = $program_no;
		}
		//print_r($sales_batch_product_arr);
		unset($batch_based_sql_prod_sales);

		$batch_based_sql_prod_ref= sql_select("SELECT a.id,a.batch_id,a.multi_batch_load_id,a.load_unload_id, b.prod_id, b.const_composition, (b.batch_qty) as batch_qty, (b.production_qty) as production_qty
		from  pro_fab_subprocess a, pro_fab_subprocess_dtls b, tmp_batch_or_iss c
		where a.id = b.mst_id and a.batch_id=c.batch_issue_id and c.type=3 and c.entry_form=5 and c.userid=$user_name and a.load_unload_id in(1,2) and b.load_unload_id in(1,2) and b.entry_page=35 and a.status_active = 1 and b.status_active=1"); // and a.batch_id in ($all_batch_ids)  $all_batch_no_cond2

		foreach ($batch_based_sql_prod_ref as $val) 
		{
			if($val[csf("load_unload_id")]==2)
			{
				$program_no=$batch_based_prog_colorType_arr[$val[csf("batch_id")]][$val[csf("prod_id")]];
				$colortype=$batch_based_prog_sales_arr[$program_no];
				$batch_based_product_arr[$val[csf("batch_id")]][$val[csf("prod_id")]] += $val[csf("production_qty")];
				$batch_based_product_arr2[$val[csf("batch_id")]]["batch_prod_tot"] += $val[csf("production_qty")];
				$batch_based_product_arr3[$val[csf("batch_id")]][$val[csf("prod_id")]][$colortype] += $val[csf("production_qty")];
			}
			else
			{
				$multi_batch_arr[$val[csf("batch_id")]]= $val[csf("multi_batch_load_id")];
			}
		}

		$batch_based_inhouse_batch_sql = "SELECT f.insert_date, a.batch_no, a.batch_weight, a.id as batch_id,a.sales_order_id, a.batch_against,a.color_id, a.color_range_id, a.extention_no, a.total_trims_weight, a.process_id as process_name, (b.batch_qnty) as batch_qnty, b.program_no,b.item_description, b.po_id, b.prod_id,b.body_part_id, b.width_dia_type, d.job_no_prefix_num, d.buyer_id, d.po_buyer, f.remarks, f.shift_name, f.production_date as process_end_date, f.process_end_date as production_date, f.end_hours,f.system_no, f.floor_id,f.multi_batch_load_id, f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id, f.fabric_type,f.responsibility_id, f.result, a.booking_no, d.booking_without_order, d.season, d.job_no  as sales_no,d.style_ref_no , b.barcode_no, f.process_id, f.ltb_btb_id, d.within_group, d.booking_id
		from pro_batch_create_dtls b, fabric_sales_order_mst d, pro_fab_subprocess f, pro_batch_create_mst a, tmp_batch_or_iss c 
		where f.batch_id=a.id and a.company_id=$company  $fsodata $batch_num $year_cond and a.entry_form=0  and a.id=b.mst_id and f.batch_id=b.mst_id   and a.id=c.batch_issue_id and c.type=3 and c.entry_form=5 and c.userid=$user_name  and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,11,2,3) and b.po_id=d.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.is_sales=1 and b.is_sales=1 and f.result=1";//$dates_com
		// echo $batch_based_inhouse_batch_sql;die;
		$batch_based_inhouse_batchdata=sql_select($batch_based_inhouse_batch_sql);
		foreach($batch_based_inhouse_batchdata as $row)
		{
			$program_no=$row[csf("program_no")];
			$color_typeId=$prog_sales_arr[$program_no];
			$batch_based_data_arr[$row[csf("batch_id")]]["insert_date"] = $row[csf("insert_date")];
			$batch_based_data_arr[$row[csf("batch_id")]]["batch_no"] = $row[csf("batch_no")];
			$batch_based_data_arr[$row[csf("batch_id")]]["system_no"] = $row[csf("system_no")];
			$batch_based_data_arr[$row[csf("batch_id")]]["batch_weight"] = $row[csf("batch_weight")];
			$batch_based_data_arr[$row[csf("batch_id")]]["color_id"] = $row[csf("color_id")];
			$batch_based_data_arr[$row[csf("batch_id")]]["color_type"] .= $color_typeId.',';
			$batch_based_data_arr[$row[csf("batch_id")]]["color_range_id"] = $row[csf("color_range_id")];
			$batch_based_data_arr[$row[csf("batch_id")]]["extention_no"] = $row[csf("extention_no")];
			$batch_based_data_arr[$row[csf("batch_id")]]["total_trims_weight"] = $row[csf("total_trims_weight")];
			$batch_based_data_arr[$row[csf("batch_id")]]["batch_qnty"] += $row[csf("batch_qnty")];
			$batch_based_data_arr[$row[csf("batch_id")]]["item_description"] .= $row[csf("item_description")].',';
			$batch_based_data_arr[$row[csf("batch_id")]]["po_id"] = $row[csf("po_id")];
			$batch_based_data_arr[$row[csf("batch_id")]]["body_part"] .= $row[csf("body_part_id")].',';
			$batch_based_data_arr[$row[csf("batch_id")]]["width_dia_type"] = $row[csf("width_dia_type")];
			$batch_based_data_arr[$row[csf("batch_id")]]["job_no_prefix_num"] = $row[csf("job_no_prefix_num")];
			$batch_based_data_arr[$row[csf("batch_id")]]["remarks"] = $row[csf("remarks")];
			$batch_based_data_arr[$row[csf("batch_id")]]["shift_name"] = $row[csf("shift_name")];
			$batch_based_data_arr[$row[csf("batch_id")]]["within_group"] = $row[csf("within_group")];
			$batch_based_data_arr[$row[csf("batch_id")]]["po_buyer"] = $row[csf("po_buyer")];
			$batch_based_data_arr[$row[csf("batch_id")]]["buyer_id"] = $row[csf("buyer_id")];
			$batch_based_data_arr[$row[csf("batch_id")]]["process_end_date"] = $row[csf("process_end_date")];
			$batch_based_data_arr[$row[csf("batch_id")]]["production_date"] = $row[csf("production_date")];
			// $batch_based_data_arr[$row[csf("batch_id")]]["production_qnty"] = $batch_based_product_arr[$row[csf("batch_id")]][$row[csf("prod_id")]];
			$batch_based_data_arr[$row[csf("batch_id")]]["production_qnty"] += $row[csf("batch_qnty")];
			$batch_based_data_arr[$row[csf("batch_id")]]["prod_qnty_tot"] = $batch_based_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
			$batch_based_data_arr[$row[csf("batch_id")]]["end_hours"] = $row[csf("end_hours")];
			$batch_based_data_arr[$row[csf("batch_id")]]["floor_id"] = $row[csf("floor_id")];
			$batch_based_data_arr[$row[csf("batch_id")]]["hour_unload_meter"] = $row[csf("hour_unload_meter")]; 
			$batch_based_data_arr[$row[csf("batch_id")]]["water_flow_meter"] = $row[csf("water_flow_meter")];
			$batch_based_data_arr[$row[csf("batch_id")]]["end_minutes"] = $row[csf("end_minutes")];
			$batch_based_data_arr[$row[csf("batch_id")]]["machine_id"] = $row[csf("machine_id")];
			$batch_based_data_arr[$row[csf("batch_id")]]["load_unload_id"] = $row[csf("load_unload_id")];
			$batch_based_data_arr[$row[csf("batch_id")]]["fabric_type"] = $row[csf("fabric_type")];
			$batch_based_data_arr[$row[csf("batch_id")]]["result"] = $row[csf("result")];
			$batch_based_data_arr[$row[csf("batch_id")]]["booking_no"] = $row[csf("booking_no")];
			$batch_based_data_arr[$row[csf("batch_id")]]["sales_no"] = $row[csf("sales_no")];
			$batch_based_data_arr[$row[csf("batch_id")]]["booking_without_order"] = $row[csf("booking_without_order")];
			$batch_based_data_arr[$row[csf("batch_id")]]["season"] = $row[csf("season")];
			$batch_based_data_arr[$row[csf("batch_id")]]["style_ref_no"] = $row[csf("style_ref_no")];
			$batch_based_data_arr[$row[csf("batch_id")]]["barcode_no"][] = $row[csf("barcode_no")];
			$batch_based_data_arr[$row[csf("batch_id")]]["process_id"] = $row[csf("process_id")];
			$batch_based_data_arr[$row[csf("batch_id")]]["required_process_name"] = $row[csf("process_name")];
			$batch_based_data_arr[$row[csf("batch_id")]]["ltb_btb_id"] = $row[csf("ltb_btb_id")];
			$batch_based_data_arr[$row[csf("batch_id")]]["multi_batch_load_id"] = $row[csf("multi_batch_load_id")];
			$batch_based_data_arr[$row[csf("batch_id")]]["batch_againstId"] = $row[csf("batch_against")];
			$batch_based_data_arr[$row[csf("batch_id")]]["add_tp_strip"] = $add_tp_stri_batch_arr[$row[csf("batch_id")]];
			
			$ttl_batch_prod_tot_arr[$row[csf("batch_id")]]['prod_qty']=$batch_based_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
			$ttl_batch_prod_tot_arr[$row[csf("batch_id")]]['trims']=$row[csf("total_trims_weight")];
			
			$machine_id_arr[$row[csf("batch_id")]]['machine_id']=$row[csf("machine_id")];
			$multi_batch_load_id=$multi_batch_arr[$row[csf("batch_id")]];
			//echo $row[csf("batch_against")].'dsd';
			if($row[csf("extention_no")]=='') $row[csf("extention_no")]=0;
		}
	}
	// echo "<pre>";print_r($batch_based_data_arr);die;
	// =======================================================================================================
	// ==========================================Batch Based Batch End========================================
	// =======================================================================================================

	// =======================================================================================================
	// ==========================================Independent and Machine Wash Start===========================
	// =======================================================================================================
	if ($fso_no == "" && $batch_number == "" && $txt_date_from !="" && $txt_date_to !="") 
	{
		// ===================================Independent Start=============================
		$independent_based_sql =sql_select("SELECT a.id, a.issue_date, a.batch_no, a.issue_basis, a.issue_purpose, a.knit_dye_source ,c.sub_process,b.item_category, b.cons_amount as dyes_chemical_cost
		from inv_issue_master a, inv_transaction b, dyes_chem_issue_dtls c
		where a.id=b.mst_id and a.id=c.mst_id and b.id=c.trans_id $issue_dates
		and b.transaction_type=2 and a.entry_form=5 and a.issue_basis=4 and a.batch_no is null  and   b.item_category in (5,6,7)");
		$independent_based_dyes_chemical_arr=array(); $independent_based_data_arr=array();
		foreach($independent_based_sql as $val)
		{
			$sub_process=$val[csf("sub_process")];
			$independent_based_data_arr[$val[csf("id")]]['issue_date']= $val[csf("issue_date")];
			$independent_based_data_arr[$val[csf("id")]]['issue_basis']= $receive_basis_arr[$val[csf("issue_basis")]];
			$independent_based_data_arr[$val[csf("id")]]['issue_purpose']= $yarn_issue_purpose[$val[csf("issue_purpose")]];
			$independent_based_data_arr[$val[csf("id")]]['issue_source']= $val[csf("knit_dye_source")];
			if($sub_process!=92)
			{
				$independent_based_dyes_chemical_arr[$val[csf("id")]][$val[csf("item_category")]]['chemical_cost']+=$val[csf("dyes_chemical_cost")];
			}
			else
			{
				$independent_based_dyes_chemical_arr[$val[csf("id")]][$val[csf("item_category")]]['chemical_cost_finish']+=$val[csf("dyes_chemical_cost")];
			}
		}
		unset($independent_based_sql);
		// ===================================Independent End=============================

		// ===================================Machine Wash Start=============================
		$machine_wash_sql =sql_select("SELECT a.id, a.issue_date, a.batch_no, a.issue_basis, a.issue_purpose, a.knit_dye_source ,c.sub_process,b.item_category, b.cons_amount as dyes_chemical_cost, d.requ_no, d.machine_id
		from inv_issue_master a, inv_transaction b, dyes_chem_issue_dtls c, dyes_chem_issue_requ_mst d
		where a.id=b.mst_id and a.id=c.mst_id and b.id=c.trans_id
		and a.req_no=cast(d.id as varchar2(4000)) and b.requisition_no=d.id and a.issue_purpose=13 $issue_dates and b.transaction_type=2 and a.entry_form=5 and a.issue_basis=7 and b.item_category in (5,6,7)");
		$machine_wash_dyes_chemical_arr=array(); $machine_wash_data_arr=array();
		foreach($machine_wash_sql as $val)
		{
			$sub_process=$val[csf("sub_process")];
			$machine_wash_data_arr[$val[csf("id")]]['issue_date']= $val[csf("issue_date")];
			$machine_wash_data_arr[$val[csf("id")]]['machine_id']= $val[csf("machine_id")];
			$machine_wash_data_arr[$val[csf("id")]]['issue_basis']= $receive_basis_arr[$val[csf("issue_basis")]];
			$machine_wash_data_arr[$val[csf("id")]]['issue_purpose']= $yarn_issue_purpose[$val[csf("issue_purpose")]];
			$machine_wash_data_arr[$val[csf("id")]]['requ_no']= $val[csf("requ_no")];
			if($sub_process!=92)
			{
				$machine_wash_dyes_chemical_arr[$val[csf("id")]][$val[csf("item_category")]]['chemical_cost']+=$val[csf("dyes_chemical_cost")];
			}
			else
			{
				$machine_wash_dyes_chemical_arr[$val[csf("id")]][$val[csf("item_category")]]['chemical_cost_finish']+=$val[csf("dyes_chemical_cost")];
			}
		}
		unset($machine_wash_sql);
		// ===================================Machine Wash End=============================
	}
	// =======================================================================================================
	// ==========================================Independent and Machine Wash End=============================
	// =======================================================================================================

	// ==============================Common Inhoue and Inbound Start==========================================
	// requ_no sql
 	$sql_dyes = "SELECT a.id as recipe_id,a.batch_id,d.item_category_id,c.ratio, c.requ_no, c.id as dtls_id
 	from pro_recipe_entry_mst a, dyes_chem_issue_requ_dtls c,dyes_chem_requ_recipe_att b,product_details_master d, tmp_batch_or_iss e
 	where b.recipe_id=a.id and c.mst_id=b.mst_id and d.id=c.product_id and a.batch_id=e.batch_issue_id and e.type in(1,2,3) and e.entry_form in(0,36,5) and e.userid=$user_name and a.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.status_active=1";
	// echo $sql_dyes;die();
	$dyes_res = sql_select($sql_dyes);
	 
	foreach ($dyes_res as $row) 
	{
		if ($row[csf('item_category_id')]==6) 
		{
			if($ChkBatchArr[$row[csf("dtls_id")]]=='')
			{
				$batch_item_ratio_dyes_arr[$row[csf('batch_id')]]['ratio_total']+= $row[csf('ratio')];
				$ChkBatchArr[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
			}
		}
		$requ_no_arr[$row[csf('batch_id')]]['requ_no'] .= $row[csf('requ_no')].',';
	}
	unset($dyes_res);
	// echo "<pre>"; print_r($batch_item_ratio_dyes_arr);die;

	// recipe_no sql
	$add_tp_stri_batch_sql=sql_select("SELECT a.id as recipe_id, a.entry_form,a.batch_id, a.dyeing_re_process,b.prod_id,b.ratio,c.item_category_id from pro_recipe_entry_mst a,pro_recipe_entry_dtls b,product_details_master c, tmp_batch_or_iss d
	where a.id=b.mst_id and c.id=b.prod_id and a.batch_id=d.batch_issue_id and d.type in(1,2,3) and d.entry_form in(0,36,5) and d.userid=$user_name and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.item_category_id in(5,7,6) ");// $all_batch_no_cond2
	 
	foreach ($add_tp_stri_batch_sql as $val) 
	{
		$entry_formId=$val[csf("entry_form")];
		if($entry_formId==60)
		{
			$all_categry_add_tp_stri_batch_arr[$val[csf("batch_id")]].=$val[csf("item_category_id")].',';
			
			if($val[csf("item_category_id")]==6) //Dyes
			{
				$dyes_add_tp_stri_batch_arr[$val[csf("batch_id")]]= $val[csf("item_category_id")];
			}
			else if($val[csf("item_category_id")]==5 || $val[csf("item_category_id")]==7) //Chemical
			{
				$chemical_add_tp_stri_batch_arr[$val[csf("batch_id")]]= $val[csf("item_category_id")];
			}
			else if($val[csf("item_category_id")]==23) //Dyes+Chemical
			{
				$dyes_chemical_add_tp_stri_batch_arr[$val[csf("batch_id")]]= $val[csf("item_category_id")];
			}
			else
			{
				//$category_add_tp_stri_batch_arr[$val[csf("batch_id")]][$val[csf("prod_id")]]= $val[csf("item_category_id")];
			}
			$add_tp_stri_batch_arr[$val[csf("batch_id")]]= $val[csf("dyeing_re_process")];
		}
		//====================Shade Percentage=================
		$batch_item_ratio_total_arr[$val[csf('batch_id')]][$val[csf('item_category_id')]]['ratio_total'] += $val[csf('ratio')]; // yes
		$batch_item_ratio_total_arr[$val[csf('batch_id')]][$val[csf('item_category_id')]]['ratio_count'] += 1;
		$batchTotal_arr[$val[csf('batch_id')]]['batch_ratio_total']  += $val[csf('ratio')]; // yes
		$recipe_no_arr[$val[csf('batch_id')]]['recipe_no'] .= $val[csf('recipe_id')].',';
	}
	unset($add_tp_stri_batch_sql);
	// =============================================================================================
	$load_hr=array();
	$load_min=array();
	$load_date=array();
	$water_flow_arr=array(); $load_hour_meter_arr=array();
	if ($working_company==0) $workingCompany_name_cond1=""; else $workingCompany_name_cond1="  and service_company='".$working_company."' ";
	if ($working_company==0) $workingCompany_name_cond13=""; else $workingCompany_name_cond13="  and f.service_company='".$working_company."' ";
	if ($company==0) $companyCond1=""; else $companyCond1="  and f.company_id=$company";

	if (!empty($all_batch))
	{
		$load_time_data=sql_select("select f.batch_id, f.water_flow_meter, f.batch_no, f.load_unload_id, f.process_end_date, f.end_hours, f.hour_load_meter, f.end_minutes from pro_fab_subprocess f where f.load_unload_id=1 and f.entry_form=35 $companyCond1 and f.status_active=1  and f.is_deleted=0 $workingCompany_name_cond13 $dyeing_batch_id_cond");
	
		foreach($load_time_data as $row_time)// for Loading time
		{
			$load_hr[$row_time[csf('batch_id')]]=$row_time[csf('end_hours')];
			$load_min[$row_time[csf('batch_id')]]=$row_time[csf('end_minutes')];
			$load_date[$row_time[csf('batch_id')]]=$row_time[csf('process_end_date')];
			$water_flow_arr[$row_time[csf('batch_id')]]=$row_time[csf('water_flow_meter')];
			$load_hour_meter_arr[$row_time[csf('batch_id')]]=$row_time[csf('hour_load_meter')];
		}
		unset($load_time_data);
	}
	
	$sub_load_time_data=sql_select("SELECT f.batch_id, f.water_flow_meter, f.batch_no, f.load_unload_id, f.process_end_date, f.end_hours, f.hour_load_meter, f.end_minutes from pro_fab_subprocess f where f.load_unload_id=1 and f.entry_form=38 $companyCond1 and f.status_active=1  and f.is_deleted=0 $workingCompany_name_cond13 $sub_dyeing_batch_id_cond");
	
	foreach($sub_load_time_data as $row_time)// for Loading time
	{
		$sub_load_hr[$row_time[csf('batch_id')]]=$row_time[csf('end_hours')];
		$sub_load_min[$row_time[csf('batch_id')]]=$row_time[csf('end_minutes')];
		$sub_load_date[$row_time[csf('batch_id')]]=$row_time[csf('process_end_date')];
		$sub_water_flow_arr[$row_time[csf('batch_id')]]=$row_time[csf('water_flow_meter')];
		$sub_load_hour_meter_arr[$row_time[csf('batch_id')]]=$row_time[csf('hour_load_meter')];
	}
	unset($sub_load_time_data);
	
	// =================================Dyes Chemical cost Start======================================
    $sql_dyes_cost =sql_select("SELECT a.batch_no, a.issue_basis, a.issue_purpose,c.sub_process,b.item_category,sum(b.cons_amount) as dyes_chemical_cost
    from inv_issue_master a, inv_transaction b,dyes_chem_issue_dtls c
    where a.id=b.mst_id and a.id=c.mst_id and b.id=c.trans_id and b.transaction_type=2 and a.entry_form=5 and a.batch_no  is not null  and   b.item_category in (5,6,7)
    group by a.batch_no, a.issue_basis, a.issue_purpose,b.item_category,c.sub_process "); // and c.batch_id in('16027,16028','15880')
	
	$dyes_chemical_arr=array();
	foreach($sql_dyes_cost as $val)
	{
		$sub_process=$val[csf("sub_process")];
		$all_batch_no=explode(",",$val[csf("batch_no")]);
		foreach ($all_batch_no as $key => $batchId) 
		{
			if($sub_process!=92)
			{
				$dyes_chemical_arr[$batchId][$val[csf("item_category")]]['chemical_cost']+=$val[csf("dyes_chemical_cost")];
			}
			else
			{
				$dyes_chemical_arr[$batchId][$val[csf("item_category")]]['chemical_cost_finish']+=$val[csf("dyes_chemical_cost")];
			}
			if ($val[csf("issue_basis")]==7) 
			{
				$chemical_issue_batch_arr[$batchId]['issue_basis']= $receive_basis_arr[$val[csf("issue_basis")]];
				$chemical_issue_batch_arr[$batchId]['issue_purpose']= $yarn_issue_purpose[$val[csf("issue_purpose")]];
			}
		}
	}
	// echo "<pre>";print_r($dyes_chemical_arr);die;
	// =================================Dyes Chemical cost End=======================================
	// =================================Common Inhoue and Inbound End============================

	$r_id=execute_query("delete from tmp_batch_or_iss where userid=$user_name");
	oci_commit($con);

	ob_start();

	$div_width=4770;
	$table_width=4750;
	?>
	<style type="text/css">
		.word_wrap_break {
			word-break: break-all;
			word-wrap: break-word;
		}
	</style>
    <div align="left">
        <fieldset style="width:4770px;">
            <div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong>
            	<br><b>
                <?
                $date_head="";
                if( $date_from)
                {
                	$date_head .= change_date_format($date_from).' To ';
                }
                if( $date_to)
                {
                	$date_head .= change_date_format($date_to);
                }
                echo $date_head;
                ?> </b>
            </div>
            <!-- Inhouse Batch Start-->
            <div align="left">
            	<caption><b>Inhouse Batch</b></caption>
                <table class="rpt_table" width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">                	
                    <thead>
                        <tr>
                            <th width="30" rowspan="2" class="word_wrap_break">SL</th>
                            <th width="50" rowspan="2" class="word_wrap_break">Date</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Machine No</th>
                            <th width="110" rowspan="2" class="word_wrap_break">Floor</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Shift</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Buyer</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Style</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Season</th>
                            <th width="100" rowspan="2" class="word_wrap_break">FSO No</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Booking No</th>
                            <th width="50" rowspan="2" class="word_wrap_break">Booking Type</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Body Part</th>
                            <th width="150" rowspan="2" class="word_wrap_break">Fabric Description</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Dia/ Width Type</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Lot No</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Yarn Info.</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Color Type</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Color Name</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Color Range</th>
                            <th width="60" rowspan="2" class="word_wrap_break">Dyes Percent</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Shade%</th>
                            <th width="150" rowspan="2" class="word_wrap_break">Batch No</th>
                            <th width="60" rowspan="2" class="word_wrap_break">Ext. No</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Fabric Wgt.</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Trims Wgt.</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Batch Weight(Kg)</th>
                            <th width="100" rowspan="2" class="word_wrap_break">M/C Capacity.</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Loading %.</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Load Date Time</th>
                            <th width="100" rowspan="2" class="word_wrap_break">UnLoad Date Time</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Time Used</th>
                            <th width="70" rowspan="2" class="word_wrap_break">BTB / LTB</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Dyeing Fab. Type</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Result</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Process Name</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Dyeing Re Process Name</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Hour Load Meter</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Hour UnLoad Meter</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Total Time</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Water Loading Flow</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Water UnLoading Flow</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Water Cons.</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Remark</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Recipe No</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Requisition No</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Basis</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Issue Purpose</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Required Process Name</th>

                            <th colspan="4" class="word_wrap_break">Dyeing Cost</th>
                            <th colspan="2" class="word_wrap_break">Finishing Cost Detail</th>

                            <th width="70" rowspan="2" class="word_wrap_break">Total Cost (Tk)</th>
                            <th width="" rowspan="2" class="word_wrap_break">Total Per Kg Cost (Tk)</th>
                        </tr>
                        <tr>
                        	<th width="90" class="word_wrap_break">Tot Chem Cost (Tk)</th>
                            <th width="90" class="word_wrap_break">Tot Dyes Cost (Tk)</th>
                            <th width="90" class="word_wrap_break">Tot Chem + Dyes Cost (Tk)</th>
                            <th width="90" class="word_wrap_break">Cost Per Kg (Tk)</th>
                            <th width="90" class="word_wrap_break">Tot Finishing Cost</th>
                            <th width="90" class="word_wrap_break">Tot Finishing Cost Per Kg (Tk)</th>
                        </tr>
                    </thead>
                </table>
                <div style=" max-height:350px; width:<?echo $div_width;?>px; overflow-y:scroll;" id="scroll_body">
                    <table class="rpt_table" id="table_body" width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                        <tbody>
                            <?
                            $i=1;
                            $dyesReqTotal=0;
                            foreach($inhouse_batch_data_array as $batch_id=>$row)
                            {
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$batch_qnty = $row["prod_qnty_tot"]+$row["total_trims_weight"];
								$batch_weight=$row['batch_weight'];
								$water_cons_unload=$row['water_flow_meter'];
								$water_cons_load=$water_flow_arr[$batch_id];
								$load_hour_meter=$load_hour_meter_arr[$batch_id];
								$water_cons_diff=($water_cons_unload-$water_cons_load)/$batch_weight*1000;

								$batch_no=$row['id'];

								if($date_search_type==1)
								{
									$date_type_cond=$row['production_date'];
								}
								else
								{
									$date_typecond=explode(" ",$row['insert_date']);
									$date_type_cond=$date_typecond[0];
								}
								$BatchTotal 				= $batchTotal_arr[$batch_id]['batch_ratio_total'];
								//$chemicalsTotal 			= $batch_item_ratio_total_arr[$batch_id][5]['ratio_total'];
								$dyesTotal 					= $batch_item_ratio_total_arr[$batch_id][6]['ratio_total'];
								$dyesReqTotal 				= $batch_item_ratio_dyes_arr[$batch_id]['ratio_total'];
								//$auxiChemicalsTotal			= $batch_item_ratio_total_arr[$batch_id][7]['ratio_total'];
								
								$shade_percentageOfColor = 0;
								if($dyesTotal>0 && $BatchTotal>0 )
								{
									//	echo $row[csf('batch_id')].'=='.$dyesTotal.'TTTTTTTT'.$BatchTotal;
									$shade_percentageOfColor=($dyesTotal/$BatchTotal)*100;
								}

								// $recipe_no=$recipe_no_arr[$batch_id]['recipe_no'];
								// $requ_no=$requ_no_arr[$batch_id]['requ_no'];
								$recipe_no= chop(implode(", ",array_filter(array_unique(explode(",", $recipe_no_arr[$batch_id]['recipe_no'])))),',');
								$requ_no= chop(implode(", ",array_filter(array_unique(explode(",", $requ_no_arr[$batch_id]['requ_no'])))),',');

								$issue_basis=$chemical_issue_batch_arr[$batch_id]['issue_basis'];
								$issue_purpose=$chemical_issue_batch_arr[$batch_id]['issue_purpose'];

								$first_chemi_cost=$dyes_chemical_arr[$batch_id][5]['chemical_cost']+$dyes_chemical_arr[$batch_id][7]['chemical_cost'];
				                $first_dyeing_cost=$dyes_chemical_arr[$batch_id][6]['chemical_cost'];
				                $chemical_cost_finish=$dyes_chemical_arr[$batch_id][5]['chemical_cost_finish']+$dyes_chemical_arr[$batch_id][7]['chemical_cost_finish'];
				                
								
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
                                    <td class="word_wrap_break" width="30"><? echo $i; ?></td>
                                    <td class="word_wrap_break" width="50"><? echo change_date_format($date_type_cond); $unload_date=$row['process_end_date']; ?></td>
                                    <td width="100" align="center" title="<? echo $row['machine_id']; ?>"><p><? echo $machine_arr[$row['machine_id']]; ?></p></td>
                                    <td class="word_wrap_break" width="110" title="<? echo $row['FSO_ID']; ?>"><? echo $floor_arr[$row['floor_id']]; ?></td>
                                    <td width="80"><p class="word_wrap_break"><? echo $shift_name[$row['shift_name']]; ?></p></td>
                                    <td width="80"><p class="word_wrap_break">
										<? 
										if($row["within_group"]==1)
										{
											echo $buyer_arr[$row['po_buyer']]; 
										}
										else
										{
											echo $buyer_arr[$row['buyer_id']]; 
										}
										?>
                                    </p></td>
                                    <td width="80"><p class="word_wrap_break"><? echo $row['style_ref_no']; ?></p>
                                    <td width="80"><p class="word_wrap_break"><? echo $row['season']; ?></p>
                                    <td width="100"><p class="word_wrap_break"><? echo $row['sales_no']; ?></p>       
                                    <td class="word_wrap_break" width="100"><p><? echo $row['booking_no']; ?></p></td>
                                    <td width="50"><p class="word_wrap_break"><? echo $booking_type_arr[$row['booking_no']]; ?></p>
                                    </td>
                                    <td width="100" class="word_wrap_break" align="center" title="<? echo $determination_id; ?>"><? 
	                                    $body_part_arr =array_filter(array_unique(explode(",", $row["body_part"])));
	                                    $body_part_name="";
	                                    foreach ($body_part_arr as $body) 
										{
											$body_part_name .= chop($body_part[$body],",").",";		
										}
										echo implode(", ",array_filter(array_unique(explode(",", $body_part_name)))); ?></td>
                                    <td width="150" align="center"><p class="word_wrap_break">
                                    	<?
										echo implode(",",array_filter(array_unique(explode(",", chop($row["item_description"],",")))));?></p>

                                    	<? //echo $row['item_description']; ?></p></td>
                                    <td width="80" align="center"><p class="word_wrap_break"><? echo $fabric_typee[$row['width_dia_type']]; ?></p></td>
                                    <td width="100" class="word_wrap_break" title="<? echo implode(", ",array_filter(array_unique($row["barcode_no"])));?>">
										<p>
											<? $bar_lot_arr= array();$yarnlots="";$yarn_types="";$yarn_count_name="";$yarn_comp_type1st="";
											$bar_lot_arr =array_filter(array_unique($row["barcode_no"]));
											if(count($bar_lot_arr)>0)
											{
												foreach ($bar_lot_arr as $bcode) 
												{
													$yarnlots .= chop($yarn_lot_arr[$bcode],",").",";
													
													$yarn_types .= chop($yarn_data_arr[$bcode]['yarn_type'],",").",";
													$yarn_comp_type1st .= chop($yarn_data_arr[$bcode]['yarn_comp_type1st'],",").",";
													$yarn_count_name .= chop($yarn_data_arr[$bcode]['yarn_count_id'],",").",";
													$colors .= chop($yarn_data_arr[$bcode]['color'],",").",";		
												}
												echo implode(", ",array_filter(array_unique(explode(",", $yarnlots))));  
											}
											$yarn_types=implode(", ",array_filter(array_unique(explode(",", $yarn_types))));
											$yarn_count_name=implode(", ",array_filter(array_unique(explode(",", $yarn_count_name))));
											$yarn_comp_type1st=implode(", ",array_filter(array_unique(explode(",", $yarn_comp_type1st))));
											$colors=implode(", ",array_filter(array_unique(explode(",", $colors))));
											?>
										</p>
									</td>
                                    <td width="100"><p><? echo $yarn_count_name.', '.$yarn_comp_type1st.', '.$yarn_types; ?></p></td>
                                    <td width="100"><p>
                                    	<?
	                                    $color_type_ids_arr =array_filter(array_unique(explode(",", $row["color_type"])));
	                                    $color_type_name="";
	                                    foreach ($color_type_ids_arr as $ctype) 
										{
											$color_type_name .= chop($color_type[$ctype],",").",";		
										}
										echo implode(", ",array_filter(array_unique(explode(",", $color_type_name))));?></p>
									</td>
                                    <td width="100"><p><? echo $color_library[$row['color_id']]; ?></p></p></td>
                                    <td width="100" title="Grey Dia"><p><? echo $color_range[$row['color_range_id']]; ?></p></td>
                                    <td width="60" class="word_wrap_break" align="center"><p><? echo number_format($dyesReqTotal,2); ?></p></td>
                                    <td width="80" class="word_wrap_break" title="(dyesTotalRatio(<? echo $dyesTotal;?>)/BatchTotalRatio(<? echo $BatchTotal;?>))*100;" ><p><? echo number_format($shade_percentageOfColor,2); ?></p></td>
                                    <td width="150" class="word_wrap_break" title="<? echo $batch_id; ?>"><p><? echo $row['batch_no']; ?></p></td>
                                    <td class="word_wrap_break" width="60"><p><? echo $row['extention_no']; ?></p></td>
                                    <td class="word_wrap_break" width="100" align="right"><p><? echo number_format($row['production_qnty'],2); $grand_total_production_qnty += $row['production_qnty']; $sub_production_qnty += $row['production_qnty'];?></p></td>
                                    <td width="100" align="right"><p><?echo number_format($row["total_trims_weight"],2);  ?></p></td>
                                    <td width="100" align="right"><p><?echo number_format($batch_qnty,2);  ?></p></td>
                                    <td width="100" align="right"><p class="word_wrap_break"><? echo number_format($machine_capacity_arr[$row['machine_id']],2);  ?></p></td>
                                    <td align="right" width="100" title="Batch Weight/Machine Capacity*100" ><? echo number_format(($batch_qnty/$machine_capacity_arr[$row['machine_id']]*100),2);  ?></td>
                                    <td width="100" rowspan="<? echo $batch_td_span?>" title="<? echo $load_date[$batch_id];?>"><p  style="word-wrap:break-word;">&nbsp;<? $load_t=$load_hr[$batch_id].':'.$load_min[$batch_id]; echo  ($load_date[$batch_id] == '0000-00-00' || $load_date[$batch_id] == '' ? '' : change_date_format($load_date[$batch_id])).' <br> '.$load_t; ?></p></td>
                                    <td width="100"><? $hr=strtotime($unload_date,$load_t); $min=($row['end_minutes'])-($load_min[$batch_id]);
										echo  ($row['process_end_date'] == '0000-00-00' || $row['process_end_date'] == '' ? '' : change_date_format($row['process_end_date'])).'<br>'.$unload_time=$row['end_hours'].':'.$row['end_minutes']; ?>
									</td>
                                    <td width="70">
                                    	<? 
                                    	$new_date_time_unload=($unload_date.' '.$unload_time.':'.'00');
										$new_date_time_load=($load_date[$batch_id].' '.$load_t.':'.'00');
										$total_time=datediff(n,$new_date_time_load,$new_date_time_unload);
										echo floor($total_time/60).":".$total_time%60;
										?>
									</td>
                                    <td width="70"><p><? echo $ltb_btb[$row["ltb_btb_id"]];?></p></td>
                                    <td width="70"><p><? echo $fabric_type_for_dyeing[$row['fabric_type']];?></p></td>
                                    <td width="70"><p><? echo $dyeing_result[$row['result']]; ?></p></td>
                                    <td width="70"><p><? echo $conversion_cost_head_array[$row["process_id"]]?></p></td>
                                    <td width="70" title="<? echo $row["add_tp_strip"];?>"><p>
                                    	<? 
										if($row["process_id"]==148 && $row["add_tp_strip"] ==1)
										{
											echo "Re-Wash";
										}
										else{
											echo $dyeing_re_process[$row["add_tp_strip"]];
										} 
										?></p></td>
                                    <td width="70" align="right"><? echo number_format($load_hour_meter,2);  ?></td>
                                    <td width="70" align="right"><p><? echo number_format($row['hour_unload_meter'],2);  ?></p></td>
                                    <td width="70" align="right"><p><? echo number_format($row['hour_unload_meter']-$load_hour_meter,2);  ?></p></td>
                                    <td width="70" align="right"><p><? echo $water_cons_load;  ?></p></td>
                                    <td width="70" align="right"><p><? echo $water_cons_unload;  ?></p></td>
                                    <td width="70" align="right" title="<? echo "Water Cons Unload-Water Cons Load*1000/Batch Qnty"?>"><p><? echo number_format((($water_cons_unload-$water_cons_load)*1000)/$row['batch_qnty'],2);  ?></p></td>
                                    <td width="70" class="word_wrap_break"><? echo $row['remarks']; ?></td>

                                    <td width="70" class="word_wrap_break"><? echo $recipe_no; ?></td>
                                    <td width="70" class="word_wrap_break"><? echo $requ_no; ?></td>
                                    <td width="70" class="word_wrap_break"><? echo $issue_basis; ?></td>
                                    <td width="70" class="word_wrap_break"><? echo $issue_purpose; ?></td>
                                    <td width="70" class="word_wrap_break"><? $req_process_name_arr =array_filter(array_unique(explode(",", $row["required_process_name"])));
		                                foreach ($req_process_name_arr as $processId) 
										{
											$req_process_name .= chop($conversion_cost_head_array[$processId],",").",";		
										}
										echo implode(", ",array_filter(array_unique(explode(",", $req_process_name)))); ?>
									</td>
                                    <td width="90" align="right" title="Tot Chem Cost (Tk)"><?echo number_format($first_chemi_cost,4,".",""); $total_chemical_cost+=$first_chemi_cost;?></td>
		                            <td width="90" align="right" title="Tot Dyes Cost (Tk)"><?echo number_format($first_dyeing_cost,4,".",""); $total_dyes_cost+=$first_dyeing_cost; ?></td>
		                            <td width="90" align="right" title="Tot Chem + Dyes Cost (Tk)"><?$batch_chemical_price=$first_chemi_cost+$first_dyeing_cost; 
		                            	echo number_format($batch_chemical_price,4,".",""); 
		                            	$total_batch_chemical_price+=$batch_chemical_price;?></td>
		                            <td width="90" align="right" title="Tot Chemical+Dyes Cost/Batch Weight"><?echo number_format($batch_chemical_price/$batch_weight,4,".","");?></td>
		                            <td width="90" align="right"  title="Category=Chemical and Auxilary Chemicals"><?
					                echo number_format($chemical_cost_finish,4,".",""); 
					                $tot_re_dying_chemical_cost+=$chemical_cost_finish;
					                ?></td>
		                            <td width="90" align="right"  title="Tot Finishing Cost/Tot Batch Weight"><? echo number_format($chemical_cost_finish/$batch_weight,4,".","");  $tot_re_dying_dyes_cost+=$chemical_cost_finish/$batch_weight; ?>
		                            </td>

		                            <td width="70" align="right" title="Chemical Cost+Dyes Cost+Finishing Cost"><? echo number_format($chemical_cost_finish+$batch_chemical_price,4,".",""); ?></td>
                                    <td width="" align="right" title="Tot Chemical+Dyes+Finish Cost/Tot Batch Weight"><? echo number_format(($chemical_cost_finish+$batch_chemical_price)/$batch_weight,4,".","");  ?></td>
                                </tr>
                                <?
                                $i++;
                                $tot_fabric_wgt+=$row['production_qnty'];
								$tot_trims_qnty+=$row["total_trims_weight"];
                                $tot_batch_qnty+=$batch_qnty;
								$tot_machine_capacity+=$machine_capacity_arr[$row['machine_id']];
								$total_batch_weight+=$batch_weight;
		                    }
                            ?>
                        </tbody>
                    </table>
                </div>
                <table class="rpt_table" width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tfoot>
                        <tr>
                        	<th width="30">&nbsp;</th>  
                            <th width="50">&nbsp;</th>
                            <th width="100" title="Revised Number">&nbsp;</th>
                            <th width="110">&nbsp;</th>
                            <th width="80" title="Buyer">&nbsp;</th>
                            <th width="80" title="Client">&nbsp;</th>
                            <th width="80" title="Prod. Dept.">&nbsp;</th>
                            <th width="80" title="Season">&nbsp;</th>
                            <th width="100" title="Style Ref">&nbsp;</th>
                            <th width="100" title="booking_type">&nbsp;</th>
                            <th width="50" title="Color Type">&nbsp;</th>
                            <th width="100" title="Fabrication">&nbsp;</th>
                            <th width="150" title="Composition">&nbsp;</th>
                            <th width="80" title="Composition">&nbsp;</th>
                            <th width="100" title="Count">&nbsp;</th>
                            <th width="100" title="Lot">&nbsp;</th>
                            <th width="100" title="Stitch">&nbsp;</th>
                            <th width="100" title="Program">&nbsp;</th>
                            <th width="100" title="Grey Dia">&nbsp;</th>
                            <th width="60" title="GSM">&nbsp;</th>
                            <th width="80" title="Finish Dia">&nbsp;</th>
                            <th width="150" title="Source">&nbsp;</th>
                            <th width="60" title="Party Name"><strong>Total</strong></th>
                            <th width="100"><? echo number_format($tot_fabric_wgt,2,'.',''); ?></th>
                            <th width="100"><? echo number_format($tot_trims_qnty,2,'.',''); ?></th>
                            <th width="100"><? echo number_format($tot_batch_qnty,2,'.',''); ?></th>
                            <th width="100"><? echo number_format($tot_machine_capacity,2,'.',''); ?></th>
                            <th width="100" title="Process Loss">&nbsp;</th>
                            <th width="100" title="Grey Req"></th>
                            <th width="100" title="Inside Prod">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70" title="Stock in Hand">&nbsp;</th>
                            <th width="70" title="Order Sheet Rcv Date">&nbsp;</th>
                            <th width="70" title="Po Received Date">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70" title="Delivery Date">&nbsp;</th>

                            <th width="90"><? echo number_format($total_chemical_cost,4); ?></th>
                            <th width="90"><? echo number_format($total_dyes_cost,4); ?></th>
                            <th width="90"><? echo number_format($total_batch_chemical_price,4); ?></th>
                            <th width="90"><? echo number_format($total_batch_chemical_price/$total_batch_weight,4); ?></th>
                            <th width="90"><? echo number_format($tot_re_dying_chemical_cost,4); ?></th>
                            <th width="90"><? echo number_format($tot_re_dying_dyes_cost,4); ?></th>
                            <th width="70"><? echo number_format($total_batch_chemical_price+$tot_re_dying_dyes_chemical_cost,4); ?></th>
                            <th width="">&nbsp;</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!-- Inhouse Batch End-->

            <!-- Inbound Subcontract Batch Start-->
            <div align="left">
                <caption><b>Inbound Subcontract Batch</b></caption>
                <table class="rpt_table" width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                    <thead>
                        <tr>
                            <th width="30" rowspan="2" class="word_wrap_break">SL</th>
                            <th width="50" rowspan="2" class="word_wrap_break">Date</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Machine No</th>
                            <th width="110" rowspan="2" class="word_wrap_break">Floor</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Shift</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Buyer</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Style</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Season</th>
                            <th width="100" rowspan="2" class="word_wrap_break">FSO No</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Booking No</th>
                            <th width="50" rowspan="2" class="word_wrap_break">Booking Type</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Body Part</th>
                            <th width="150" rowspan="2" class="word_wrap_break">Fabric Description</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Dia/ Width Type</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Lot No</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Yarn Info.</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Color Type</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Color Name</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Color Range</th>
                            <th width="60" rowspan="2" class="word_wrap_break">Dyes Percent</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Shade%</th>
                            <th width="150" rowspan="2" class="word_wrap_break">Batch No</th>
                            <th width="60" rowspan="2" class="word_wrap_break">Ext. No</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Fabric Wgt.</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Trims Wgt.</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Batch Weight(Kg)</th>
                            <th width="100" rowspan="2" class="word_wrap_break">M/C Capacity.</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Loading %.</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Load Date Time</th>
                            <th width="100" rowspan="2" class="word_wrap_break">UnLoad Date Time</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Time Used</th>
                            <th width="70" rowspan="2" class="word_wrap_break">BTB / LTB</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Dyeing Fab. Type</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Result</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Process Name</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Dyeing Re Process Name</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Hour Load Meter</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Hour UnLoad Meter</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Total Time</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Water Loading Flow</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Water UnLoading Flow</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Water Cons.</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Remark</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Recipe No</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Requisition No</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Basis</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Issue Purpose</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Required Process Name</th>

                            <th colspan="4" class="word_wrap_break">Dyeing Cost</th>
                            <th colspan="2" class="word_wrap_break">Finishing Cost Detail</th>

                            <th width="70" rowspan="2" class="word_wrap_break">Total Cost (Tk)</th>
                            <th width="" rowspan="2" class="word_wrap_break">Total Per Kg Cost (Tk)</th>
                        </tr>
                        <tr>
                        	<th width="90" class="word_wrap_break">Tot Chem Cost (Tk)</th>
                            <th width="90" class="word_wrap_break">Tot Dyes Cost (Tk)</th>
                            <th width="90" class="word_wrap_break">Tot Chem + Dyes Cost (Tk)</th>
                            <th width="90" class="word_wrap_break">Cost Per Kg (Tk)</th>
                            <th width="90" class="word_wrap_break">Tot Finishing Cost</th>
                            <th width="90" class="word_wrap_break">Tot Finishing Cost Per Kg (Tk)</th>
                        </tr>
                    </thead>
                </table>
                <div style=" max-height:350px; width:<?echo $div_width;?>px; overflow-y:scroll;" id="scroll_body_inbound">
                    <table class="rpt_table" id="table_body2" width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                        <tbody>
                            <?
                            $j=1;
                            foreach($subbatch_data_arr as $batch_id => $row)
                            {
								if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$sub_batch_weight=$row['batch_weight'];
								$water_cons_unload=$row['water_flow_meter'];
								$water_cons_load=$sub_water_flow_arr[$batch_id];
								$load_hour_meter=$sub_load_hour_meter_arr[$batch_id];
								$water_cons_diff=($water_cons_unload-$water_cons_load)/$sub_batch_weight*1000;

								$desc=explode(",",$row['item_description']); 
								$batch_no=$row['id'];

								if($date_search_type==1)
								{
									$date_type_cond=$row['production_date'];
								}
								else
								{
									$date_typecond=explode(" ",$row['insert_date']);
									$date_type_cond=$date_typecond[0];
								}

								//$recipe_no=$recipe_no_arr[$batch_id]['recipe_no'];
								//$requ_no=$requ_no_arr[$batch_id]['requ_no'];
								$recipe_no= chop(implode(", ",array_filter(array_unique(explode(",", $recipe_no_arr[$batch_id]['recipe_no'])))),',');
								$requ_no= chop(implode(", ",array_filter(array_unique(explode(",", $requ_no_arr[$batch_id]['requ_no'])))),',');
								$issue_basis=$chemical_issue_batch_arr[$batch_id]['issue_basis'];
								$issue_purpose=$chemical_issue_batch_arr[$batch_id]['issue_purpose'];

								$sub_first_chemi_cost=$dyes_chemical_arr[$batch_id][5]['chemical_cost']+$dyes_chemical_arr[$batch_id][7]['chemical_cost'];
				                $sub_first_dyeing_cost=$dyes_chemical_arr[$batch_id][6]['chemical_cost'];
				                $sub_chemical_cost_finish=$dyes_chemical_arr[$batch_id][5]['chemical_cost_finish']+$dyes_chemical_arr[$batch_id][7]['chemical_cost_finish'];

                                ?>
	                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr2_<? echo $j; ?>" onClick="change_color('tr2_<? echo $j; ?>','<? echo $bgcolor; ?>')">
                                    <td class="word_wrap_break" width="30"><? echo $j; ?></td>
                                    <td class="word_wrap_break" width="50"><? echo change_date_format($date_type_cond); $unload_date=$row['process_end_date']; ?></td>
                                    <td width="100" align="center" title="<? echo $row['machine_id']; ?>"><p><? echo $machine_arr[$row['machine_id']]; ?></p></td>
                                    <td class="word_wrap_break" width="110" title="<? echo $row['FSO_ID']; ?>"><? echo $floor_arr[$row['floor_id']]; ?></td>
                                    <td width="80"><p class="word_wrap_break"><? echo $shift_name[$row['shift_name']]; ?></p></td>
                                    <td width="80"><p class="word_wrap_break">
										<? 
										if($row["within_group"]==1)
										{
											echo $buyer_arr[$row['po_buyer']]; 
										}
										else
										{
											echo $buyer_arr[$row['buyer_id']]; 
										}
										?>
                                    </p></td>
                                    <td width="80"><p class="word_wrap_break"><? echo $row['style_ref_no']; ?></p>
                                    <td width="80"><p class="word_wrap_break"><? echo $row['season']; ?></p>
                                    <td width="100"><p class="word_wrap_break"><? echo $row['job_no_prefix_num']; ?></p>       
                                    <td class="word_wrap_break" width="100"><p><? echo $row['booking_no']; ?></p></td>
                                    <td width="50"><p class="word_wrap_break"><? echo $booking_type_arr[$row['booking_no']]; ?></p>
                                    </td>
                                    <td width="100" class="word_wrap_break" align="center"><? 
	                                    $body_part_arr =array_filter(array_unique(explode(",", $row["body_part"])));
	                                    $body_part_name="";
	                                    foreach ($body_part_arr as $body) 
										{
											$body_part_name .= chop($body_part[$body],",").",";		
										}
										echo implode(", ",array_filter(array_unique(explode(",", $body_part_name)))); ?></td>
                                    <td width="150" align="center"><p class="word_wrap_break">
                                    	<?
										echo implode(",",array_filter(array_unique(explode(",", chop($row["item_description"],",")))));?></p></td>
                                    <td width="80" align="center"><p class="word_wrap_break"><? echo $fabric_typee[$row['width_dia_type']]; ?></p></td>
									<td width="100" title="<? echo implode(",",array_filter(array_unique($row["barcode_no"])));?>"><p>
										<? $bar_lot_arr= array();$yarnlots="";
										$bar_lot_arr =array_filter(array_unique($row["barcode_no"]));
										if(count($bar_lot_arr)>0){
											foreach ($bar_lot_arr as $bcode) 
											{
												$yarnlots .= chop($yarn_lot_arr[$bcode],",").",";
											}
											echo implode(",",array_filter(array_unique(explode(",", $yarnlots))));  
										}?></p>
									</td>
                                    <td width="100"><p><? echo $yarn_count_name.', '.$yarn_comp_type1st.', '.$yarn_types; ?></p></td>
                                    <td width="100"><p>
                                    	<?
	                                    $color_type_ids_arr =array_filter(array_unique(explode(",", $row["color_type"])));
	                                    $color_type_name="";
	                                    foreach ($color_type_ids_arr as $ctype) 
										{
											$color_type_name .= chop($color_type[$ctype],",").",";		
										}
										echo implode(", ",array_filter(array_unique(explode(",", $color_type_name))));?></p>
									</td>
                                    <td width="100"><p><? echo $color_library[$row['color_id']]; ?></p></p></td>
                                    <td width="100"><p><? echo $color_range[$row['color_range_id']]; ?></p></td>
                                    <td width="60" class="word_wrap_break"><p></p></td>
                                    <td width="80" class="word_wrap_break"><p></p></td>
                                    <td width="150" class="word_wrap_break"><p><? echo $row['batch_no']; ?></p></td>
                                    <td class="word_wrap_break" width="60"><p><? echo $row['extention_no']; ?></p></td>
                                    <td class="word_wrap_break" width="100" align="right"><p><? echo number_format($row['production_qnty'],2); $grand_total_production_qnty += $row['production_qnty']; $sub_production_qnty += $row['production_qnty'];?></p></td>
                                    <td width="100" align="right"><p><? echo number_format($row["total_trims_weight"],2);  ?></p></td>
                                    <td width="100" align="right"><p class="word_wrap_break"><? $batch_qnty=$row["production_qnty"]+$row["total_trims_weight"]; echo number_format($batch_qnty,2);  ?></p></td>
                                    <td align="right" width="100"><? echo number_format($machine_capacity_arr[$row['machine_id']],2);  ?></td>
                                    <td align="right" width="100" title="Batch Weight/Machine Capacity*100"><? echo number_format(($batch_qnty/$machine_capacity_arr[$row['machine_id']]*100),2);  ?></td>
                                    <td width="100"><p style="word-wrap:break-word;" title="<? echo $sub_load_date[$batch_id];?>"><? $load_t=$sub_load_hr[$batch_id].':'.$sub_load_min[$batch_id];echo  ($sub_load_date[$batch_id] == '0000-00-00' || $sub_load_date[$batch_id] == '' ? '' : change_date_format($sub_load_date[$batch_id])).' <br> '.$load_t;?></p></td>
                                    <td width="100"><? $hr=strtotime($unload_date,$load_t); $min=($row['end_minutes'])-($sub_load_min[$batch_id]);
										echo  ($row['process_end_date'] == '0000-00-00' || $row['process_end_date'] == '' ? '' : change_date_format($row['process_end_date'])).'<br>'.$unload_time=$row['end_hours'].':'.$row['end_minutes']; ?></td>
                                    <td width="70">
                                    	<?     
										$new_date_time_unload=($unload_date.' '.$unload_time.':'.'00');
										$new_date_time_load=($sub_load_date[$batch_id].' '.$load_t.':'.'00');
										$total_time=datediff(n,$new_date_time_load,$new_date_time_unload);
										echo floor($total_time/60).":".$total_time%60;
										?>
									</td>
                                    <td width="70"><p><? echo $ltb_btb[$row["ltb_btb_id"]];?></p></td>
                                    <td width="70"><p><? echo $fabric_type_for_dyeing[$row['fabric_type']];?></p></td>
                                    <td width="70"><p><? echo $dyeing_result[$row['result']]; ?></p></td>
                                    <td width="70"><p><? echo $conversion_cost_head_array[$row["process_id"]]; ?></p></td>
                                    <td width="70" title="<? echo $row["add_tp_strip"];?>"><p>
                                    	<? 
										if($row["process_id"]==148 && $row["add_tp_strip"] ==1)
										{
											echo "Re-Wash";
										}
										else{
											echo $dyeing_re_process[$row["add_tp_strip"]];
										} 
										?></p></td>
                                    <td width="70" align="right"><? echo number_format($load_hour_meter,2);  ?></td>
                                    <td width="70" align="right"><p><? echo number_format($row['hour_unload_meter'],2);  ?></p></td>
                                    <td width="70" align="right"><p><? echo number_format($row['hour_unload_meter']-$load_hour_meter,2);  ?></p></td>
                                    <td width="70" align="right"><p><? echo $water_cons_load;  ?></p></td>
                                    <td width="70" align="right"><p><? echo $water_cons_unload;  ?></p></td>
                                    <td width="70" align="right" title="<? echo "Water Cons Unload-Water Cons Load*1000/Batch Qnty"?>"><p><? echo number_format((($water_cons_unload-$water_cons_load)*1000)/$row['batch_qnty'],2);  ?></p></td>
                                    <td width="70"><p><? echo $row['remarks']; ?></p></td>

                                    <td width="70" class="word_wrap_break"><? echo $recipe_no; ?></td>
                                    <td width="70" class="word_wrap_break"><? echo $requ_no; ?></td>
                                    <td width="70" class="word_wrap_break"><? echo $issue_basis; ?></td>
                                    <td width="70" class="word_wrap_break"><? echo $issue_purpose; ?></td>
                                    <td width="70" class="word_wrap_break"><? $req_process_name_arr =array_filter(array_unique(explode(",", $row["required_process_name"])));
		                                foreach ($req_process_name_arr as $processId) 
										{
											$req_process_name .= chop($conversion_cost_head_array[$processId],",").",";		
										}
										echo implode(", ",array_filter(array_unique(explode(",", $req_process_name)))); ?>
									</td>
                                    <td width="90" align="right" title="Tot Chem Cost (Tk)"><?echo number_format($sub_first_chemi_cost,4,".",""); $total_sub_chemical_cost+=$sub_first_chemi_cost;?></td>
		                            <td width="90" align="right" title="Tot Dyes Cost (Tk)"><?echo number_format($sub_first_dyeing_cost,4,".",""); $total_sub_dyes_cost+=$sub_first_dyeing_cost; ?></td>
		                            <td width="90" align="right" title="Tot Chem + Dyes Cost (Tk)"><?$sub_batch_chemical_price=$sub_first_chemi_cost+$sub_first_dyeing_cost; 
		                            	echo number_format($sub_batch_chemical_price,4,".",""); 
		                            	$total_sub_batch_chemical_price+=$sub_batch_chemical_price;?></td>
		                            <td width="90" align="right" title="Tot Chemical+Dyes Cost/Batch Weight"><?echo number_format($sub_batch_chemical_price/$sub_batch_weight,4,".","");?></td>
		                            <td width="90" align="right" title="Category=Chemical and Auxilary Chemicals"><?
					                echo number_format($sub_chemical_cost_finish,4,".",""); 
					                $tot_sub_re_dying_chemical_cost+=$sub_chemical_cost_finish;
					                ?></td>
		                            <td width="90" align="right" title="Tot Finishing Cost/Tot Batch Weight"><? echo number_format($sub_chemical_cost_finish/$sub_batch_weight,4,".","");  $tot_sub_re_dying_dyes_cost+=$sub_chemical_cost_finish/$sub_batch_weight; ?>
		                            </td>
		                            <td width="70" align="right" title="Chemical Cost+Dyes Cost+Finishing Cost"><? echo number_format($sub_chemical_cost_finish+$sub_batch_chemical_price,4,".",""); ?></td>
                                    <td width="" align="right" title="Tot Chemical+Dyes+Finish Cost/Tot Batch Weight"><? echo number_format(($sub_chemical_cost_finish+$sub_batch_chemical_price)/$sub_batch_weight,4,".","");  ?></td>
                                </tr>
                                <?
                                $j++;
                                $tot_sub_fabric_wgt+=$row['production_qnty'];
								$tot_sub_trims_qnty+=$row["total_trims_weight"];
                                $tot_sub_batch_qnty+=$batch_qnty;
								$tot_sub_machine_capacity+=$machine_capacity_arr[$row['machine_id']];
								$tot_sub_batch_weight+=$sub_batch_weight;
		                    }
                            ?>
                        </tbody>
                    </table>
                </div>
                <table class="rpt_table" width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tfoot>
                        <tr>
                        	<th width="30">&nbsp;</th>  
                            <th width="50">&nbsp;</th>
                            <th width="100" title="Revised Number">&nbsp;</th>
                            <th width="110">&nbsp;</th>
                            <th width="80" title="Buyer">&nbsp;</th>
                            <th width="80" title="Client">&nbsp;</th>
                            <th width="80" title="Prod. Dept.">&nbsp;</th>
                            <th width="80" title="Season">&nbsp;</th>
                            <th width="100" title="Style Ref">&nbsp;</th>
                            <th width="100" title="booking_type">&nbsp;</th>
                            <th width="50" title="Color Type">&nbsp;</th>
                            <th width="100" title="Fabrication">&nbsp;</th>
                            <th width="150" title="Composition">&nbsp;</th>
                            <th width="80" title="Composition">&nbsp;</th>
                            <th width="100" title="Count">&nbsp;</th>
                            <th width="100" title="Lot">&nbsp;</th>
                            <th width="100" title="Stitch">&nbsp;</th>
                            <th width="100" title="Program">&nbsp;</th>
                            <th width="100" title="Grey Dia">&nbsp;</th>
                            <th width="60" title="GSM">&nbsp;</th>
                            <th width="80" title="Finish Dia">&nbsp;</th>
                            <th width="150" title="Source">&nbsp;</th>
                            <th width="60" title="Party Name"><strong>Total</strong></th>
                            <th width="100"><? echo number_format($tot_sub_fabric_wgt,2,'.',''); ?></th>
                            <th width="100"><? echo number_format($tot_sub_trims_qnty,2,'.',''); ?></th>
                            <th width="100"><? echo number_format($tot_sub_batch_qnty,2,'.',''); ?></th>
                            <th width="100"><? echo number_format($tot_sub_machine_capacity,2,'.',''); ?></th>
                            <th width="100" title="Process Loss">&nbsp;</th>
                            <th width="100" title="Grey Req">&nbsp;</th>
                            <th width="100" title="Inside Prod">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70" title="Stock in Hand">&nbsp;</th>
                            <th width="70" title="Order Sheet Rcv Date">&nbsp;</th>
                            <th width="70" title="Po Received Date">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70" title="Remarks">&nbsp;</th>

                            <th width="90"><? echo number_format($total_sub_chemical_cost,4); ?></th>
                            <th width="90"><? echo number_format($total_sub_dyes_cost,4); ?></th>
                            <th width="90"><? echo number_format($total_sub_batch_chemical_price,4); ?></th>
                            <th width="90"><? echo number_format($total_sub_batch_chemical_price/$tot_sub_batch_weight,4); ?></th>
                            <th width="90"><? echo number_format($tot_sub_re_dying_chemical_cost,4); ?></th>
                            <th width="90"><? echo number_format($tot_sub_re_dying_dyes_cost,4); ?></th>
                            <th width="70"><? echo number_format($total_sub_batch_chemical_price+$tot_re_dying_dyes_chemical_cost,4); ?></th>
                            <th width="">&nbsp;</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!-- Inbound Subcontract Batch End-->

            <!-- Batch Based Start-->
            <div align="left">
                <caption><b>Batch Based</b></caption>
                <table class="rpt_table" width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                    <thead>
                        <tr>
                            <th width="30" rowspan="2" class="word_wrap_break">SL</th>
                            <th width="50" rowspan="2" class="word_wrap_break">Date</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Machine No</th>
                            <th width="110" rowspan="2" class="word_wrap_break">Floor</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Shift</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Buyer</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Style</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Season</th>
                            <th width="100" rowspan="2" class="word_wrap_break">FSO No</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Booking No</th>
                            <th width="50" rowspan="2" class="word_wrap_break">Booking Type</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Body Part</th>
                            <th width="150" rowspan="2" class="word_wrap_break">Fabric Description</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Dia/ Width Type</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Lot No</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Yarn Info.</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Color Type</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Color Name</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Color Range</th>
                            <th width="60" rowspan="2" class="word_wrap_break">Dyes Percent</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Shade%</th>
                            <th width="150" rowspan="2" class="word_wrap_break">Batch No</th>
                            <th width="60" rowspan="2" class="word_wrap_break">Ext. No</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Fabric Wgt.</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Trims Wgt.</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Batch Weight(Kg)</th>
                            <th width="100" rowspan="2" class="word_wrap_break">M/C Capacity.</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Loading %.</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Load Date Time</th>
                            <th width="100" rowspan="2" class="word_wrap_break">UnLoad Date Time</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Time Used</th>
                            <th width="70" rowspan="2" class="word_wrap_break">BTB / LTB</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Dyeing Fab. Type</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Result</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Process Name</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Dyeing Re Process Name</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Hour Load Meter</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Hour UnLoad Meter</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Total Time</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Water Loading Flow</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Water UnLoading Flow</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Water Cons.</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Remark</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Recipe No</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Requisition No</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Basis</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Issue Purpose</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Required Process Name</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Fabric Type</th>

                            <th colspan="4" class="word_wrap_break">Dyeing Cost</th>
                            <th colspan="2" class="word_wrap_break">Finishing Cost Detail</th>

                            <th width="" rowspan="2" class="word_wrap_break">Total Cost (Tk)</th>
                        </tr>
                        <tr>
                        	<th width="90" class="word_wrap_break">Tot Chem Cost (Tk)</th>
                            <th width="90" class="word_wrap_break">Tot Dyes Cost (Tk)</th>
                            <th width="90" class="word_wrap_break">Tot Chem + Dyes Cost (Tk)</th>
                            <th width="90" class="word_wrap_break">Cost Per Kg (Tk)</th>
                            <th width="90" class="word_wrap_break">Tot Finishing Cost</th>
                            <th width="90" class="word_wrap_break">Tot Finishing Cost Per Kg (Tk)</th>
                        </tr>
                    </thead>
                </table>
                <div style=" max-height:350px; width:<?echo $div_width;?>px; overflow-y:scroll;" id="scroll_body_batch">
                    <table class="rpt_table" id="table_body3" width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                        <tbody>
                            <?
                            $j=1;
                            foreach($batch_based_data_arr as $batch_id => $row)
                            {
								if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$batchQnty = $row["prod_qnty_tot"]+$row["total_trims_weight"];
								$batch_based_batch_weight=$row['batch_weight'];
								$water_cons_unload=$row['water_flow_meter'];
								$water_cons_load=$sub_water_flow_arr[$batch_id];
								$load_hour_meter=$sub_load_hour_meter_arr[$batch_id];
								$water_cons_diff=($water_cons_unload-$water_cons_load)/$batch_based_batch_weight*1000;

								$desc=explode(",",$row['item_description']); 
								$batch_no=$row['id'];

								if($date_search_type==1)
								{
									$date_type_cond=$row['production_date'];
								}
								else
								{
									$date_typecond=explode(" ",$row['insert_date']);
									$date_type_cond=$date_typecond[0];
								}
								$BatchTotal=$dyesTotal=$dyesReqTotal=$shade_percentageOfColor=0;
								if ($row['issue_source']==1) 
								{
									$BatchTotal 				= $batchTotal_arr[$batch_id]['batch_ratio_total'];
									//$chemicalsTotal 			= $batch_based_item_ratio_total_arr[$batch_id][5]['ratio_total'];
									$dyesTotal 					= $batch_item_ratio_total_arr[$batch_id][6]['ratio_total'];
									$dyesReqTotal 				= $batch_item_ratio_dyes_arr[$batch_id]['ratio_total'];
									//$auxiChemicalsTotal			= $batch_based_item_ratio_total_arr[$batch_id][7]['ratio_total'];
									
									$shade_percentageOfColor = 0;
									if($dyesTotal>0 && $BatchTotal>0 )
									{
										//	echo $row[csf('batch_id')].'=='.$dyesTotal.'TTTTTTTT'.$BatchTotal;
										$shade_percentageOfColor=($dyesTotal/$BatchTotal)*100;
									}
								}
								

								//$recipe_no=$recipe_no_arr[$batch_id]['recipe_no'];
								//$requ_no=$requ_no_arr[$batch_id]['requ_no'];
								$recipe_no= chop(implode(", ",array_filter(array_unique(explode(",", $recipe_no_arr[$batch_id]['recipe_no'])))),',');
								$requ_no= chop(implode(", ",array_filter(array_unique(explode(",", $requ_no_arr[$batch_id]['requ_no'])))),',');
								$issue_basis=$row['issue_basis'];
								$issue_purpose=$row['issue_purpose'];

								$batch_based_first_chemi_cost=$batch_based_dyes_chemical_arr[$batch_id][5]['chemical_cost']+$batch_based_dyes_chemical_arr[$batch_id][7]['chemical_cost'];
				                $batch_based_first_dyeing_cost=$batch_based_dyes_chemical_arr[$batch_id][6]['chemical_cost'];
				                $batch_based_chemical_cost_finish=$batch_based_dyes_chemical_arr[$batch_id][5]['chemical_cost_finish']+$batch_based_dyes_chemical_arr[$batch_id][7]['chemical_cost_finish'];

                                ?>
	                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr3_<? echo $j; ?>" onClick="change_color('tr3_<? echo $j; ?>','<? echo $bgcolor; ?>')">
                                    <td class="word_wrap_break" width="30"><? echo $j; ?></td>
                                    <td class="word_wrap_break" width="50"><? echo change_date_format($date_type_cond); $unload_date=$row['process_end_date']; ?></td>
                                    <td width="100" align="center" title="<? echo $row['machine_id']; ?>"><p><? echo $machine_arr[$row['machine_id']]; ?></p></td>
                                    <td class="word_wrap_break" width="110" title="<? echo $row['FSO_ID']; ?>"><? echo $floor_arr[$row['floor_id']]; ?></td>
                                    <td width="80"><p class="word_wrap_break"><? echo $shift_name[$row['shift_name']]; ?></p></td>
                                    <td width="80"><p class="word_wrap_break">
										<? 
										if($row["within_group"]==1)
										{
											echo $buyer_arr[$row['po_buyer']]; 
										}
										else
										{
											echo $buyer_arr[$row['buyer_id']]; 
										}
										?>
                                    </p></td>
                                    <td width="80"><p class="word_wrap_break"><? echo $row['style_ref_no']; ?></p>
                                    <td width="80"><p class="word_wrap_break"><? echo $row['season']; ?></p>
                                    <td width="100"><p class="word_wrap_break"><? echo $row['job_no_prefix_num']; ?></p>       
                                    <td class="word_wrap_break" width="100"><p><? echo $row['booking_no']; ?></p></td>
                                    <td width="50"><p class="word_wrap_break"><? echo $booking_type_arr[$row['booking_no']]; ?></p>
                                    </td>
                                    <td width="100" class="word_wrap_break" align="center"><? 
	                                    $body_part_arr =array_filter(array_unique(explode(",", $row["body_part"])));
	                                    $body_part_name="";
	                                    foreach ($body_part_arr as $body) 
										{
											$body_part_name .= chop($body_part[$body],",").",";		
										}
										echo implode(", ",array_filter(array_unique(explode(",", $body_part_name)))); ?></td>
                                    <td width="150" align="center"><p class="word_wrap_break">
                                    	<?
										echo implode(",",array_filter(array_unique(explode(",", chop($row["item_description"],",")))));?></p></td>
                                    <td width="80" align="center"><p class="word_wrap_break"><? echo $fabric_typee[$row['width_dia_type']]; ?></p></td>
									<td width="100" title="<? echo implode(",",array_filter(array_unique($row["barcode_no"])));?>"><p>
										<? $bar_lot_arr= array();$yarnlots="";
										$bar_lot_arr =array_filter(array_unique($row["barcode_no"]));
										if(count($bar_lot_arr)>0){
											foreach ($bar_lot_arr as $bcode) 
											{
												$yarnlots .= chop($yarn_lot_arr[$bcode],",").",";
											}
											echo implode(",",array_filter(array_unique(explode(",", $yarnlots))));  
										}?></p>
									</td>
                                    <td width="100"><p><? echo $yarn_count_name.', '.$yarn_comp_type1st.', '.$yarn_types; ?></p></td>
                                    <td width="100"><p>
                                    	<?
	                                    $color_type_ids_arr =array_filter(array_unique(explode(",", $row["color_type"])));
	                                    $color_type_name="";
	                                    foreach ($color_type_ids_arr as $ctype) 
										{
											$color_type_name .= chop($color_type[$ctype],",").",";		
										}
										echo implode(", ",array_filter(array_unique(explode(",", $color_type_name))));?></p>
									</td>
                                    <td width="100"><p><? echo $color_library[$row['color_id']]; ?></p></p></td>
                                    <td width="100"><p><? echo $color_range[$row['color_range_id']]; ?></p></td>
                                    <td width="60" class="word_wrap_break" align="center"><p><? echo number_format($dyesReqTotal,2); ?></p></td>
                                    <td width="80" class="word_wrap_break" title="(dyesTotalRatio(<? echo $dyesTotal;?>)/BatchTotalRatio(<? echo $BatchTotal;?>))*100;" ><p><? echo number_format($shade_percentageOfColor,2); ?></p></td>
                                    <td width="150" class="word_wrap_break"><p><? echo $row['batch_no']; ?></p></td>
                                    <td class="word_wrap_break" width="60"><p><? echo $row['extention_no']; ?></p></td>
                                    <td class="word_wrap_break" width="100" align="right"><p><? echo number_format($row['production_qnty'],2); $grand_total_production_qnty += $row['production_qnty']; $sub_production_qnty += $row['production_qnty'];?></p></td>
                                    <td width="100" align="right"><p><? echo number_format($row["total_trims_weight"],2);  ?></p></td>

                                    <td width="100" align="right"><p class="word_wrap_break"><?  echo number_format($batchQnty,2);  ?></p></td>
                                    <td align="right" width="100"><? echo number_format($machine_capacity_arr[$row['machine_id']],2);  ?></td>
                                    <td align="right" width="100" title="Batch Weight/Machine Capacity*100"><? echo number_format(($batchQnty/$machine_capacity_arr[$row['machine_id']]*100),2);  ?></td>
                                    <td width="100"><p style="word-wrap:break-word;" title="<? echo $sub_load_date[$batch_id];?>"><? $load_t=$sub_load_hr[$batch_id].':'.$sub_load_min[$batch_id];echo  ($sub_load_date[$batch_id] == '0000-00-00' || $sub_load_date[$batch_id] == '' ? '' : change_date_format($sub_load_date[$batch_id])).' <br> '.$load_t;?></p></td>
                                    <td width="100"><? $hr=strtotime($unload_date,$load_t); $min=($row['end_minutes'])-($sub_load_min[$batch_id]);
										echo  ($row['process_end_date'] == '0000-00-00' || $row['process_end_date'] == '' ? '' : change_date_format($row['process_end_date'])).'<br>'.$unload_time=$row['end_hours'].':'.$row['end_minutes']; ?></td>
                                    <td width="70">
                                    	<?     
										$new_date_time_unload=($unload_date.' '.$unload_time.':'.'00');
										$new_date_time_load=($sub_load_date[$batch_id].' '.$load_t.':'.'00');
										$total_time=datediff(n,$new_date_time_load,$new_date_time_unload);
										echo floor($total_time/60).":".$total_time%60;
										?>
									</td>
                                    <td width="70"><p><? echo $ltb_btb[$row["ltb_btb_id"]];?></p></td>
                                    <td width="70"><p><? echo $fabric_type_for_dyeing[$row['fabric_type']];?></p></td>
                                    <td width="70"><p><? echo $dyeing_result[$row['result']]; ?></p></td>
                                    <td width="70"><p><? echo $conversion_cost_head_array[$row["process_id"]]; ?></p></td>
                                    <td width="70" title="<? echo $row["add_tp_strip"];?>"><p>
                                    	<? 
										if($row["process_id"]==148 && $row["add_tp_strip"] ==1)
										{
											echo "Re-Wash";
										}
										else{
											echo $dyeing_re_process[$row["add_tp_strip"]];
										} 
										?></p></td>
                                    <td width="70"><? echo number_format($load_hour_meter,2);  ?></td>
                                    <td width="70"><p><? echo number_format($row['hour_unload_meter'],2);  ?></p></td>
                                    <td width="70"><p><? echo number_format($row['hour_unload_meter']-$load_hour_meter,2);  ?></p></td>
                                    <td width="70"><p><? echo $water_cons_load;  ?></p></td>
                                    <td width="70"><p><? echo $water_cons_unload;  ?></p></td>
                                    <td width="70" title="<? echo "Water Cons Unload-Water Cons Load*1000/Batch Qnty"?>"><p><? echo number_format((($water_cons_unload-$water_cons_load)*1000)/$row['batch_qnty'],2);  ?></p></td>
                                    <td width="70"><p><? echo $row['remarks']; ?></p></td>

                                    <td width="70" class="word_wrap_break"><? echo $recipe_no; ?></td>
                                    <td width="70" class="word_wrap_break"><? echo $requ_no; ?></td>
                                    <td width="70" class="word_wrap_break"><? echo $issue_basis; ?></td>
                                    <td width="70" class="word_wrap_break"><? echo $issue_purpose; ?></td>
                                    <td width="70" class="word_wrap_break"><? $req_process_name_arr =array_filter(array_unique(explode(",", $row["required_process_name"])));
		                                foreach ($req_process_name_arr as $processId) 
										{
											$req_process_name .= chop($conversion_cost_head_array[$processId],",").",";		
										}
										echo implode(", ",array_filter(array_unique(explode(",", $req_process_name)))); ?>
									</td>
									<td width="70" class="word_wrap_break"><? echo $fabric_type_for_dyeing[$row["fabric_type"]]; ?></td>
                                    <td width="90" align="right" title="Tot Chem Cost (Tk)"><?echo number_format($batch_based_first_chemi_cost,4,".",""); $total_batch_based_chemical_cost+=$batch_based_first_chemi_cost;?></td>
		                            <td width="90" align="right" title="Tot Dyes Cost (Tk)"><?echo number_format($batch_based_first_dyeing_cost,4,".",""); $total_batch_based_dyes_cost+=$batch_based_first_dyeing_cost; ?></td>
		                            <td width="90" align="right" title="Tot Chem + Dyes Cost (Tk)"><?$batch_based_batch_chemical_price=$batch_based_first_chemi_cost+$batch_based_first_dyeing_cost; 
		                            	echo number_format($batch_based_batch_chemical_price,4,".",""); 
		                            	$total_batch_based_batch_chemical_price+=$batch_based_batch_chemical_price;?></td>
		                            <td width="90" align="right" title="Tot Chemical+Dyes Cost/Batch Weight"><?echo number_format($batch_based_batch_chemical_price/$batch_based_batch_weight,4,".","");?></td>
		                            <td width="90" align="right" title="Category=Chemical and Auxilary Chemicals"><?
					                echo number_format($batch_based_chemical_cost_finish,4,".",""); 
					                $tot_batch_based_re_dying_chemical_cost+=$batch_based_chemical_cost_finish;
					                ?></td>
		                            <td width="90" align="right" title="Tot Finishing Cost/Tot Batch Weight"><? echo number_format($batch_based_chemical_cost_finish/$batch_based_batch_weight,4,".","");  $tot_batch_based_re_dying_dyes_cost+=$batch_based_chemical_cost_finish/$batch_based_batch_weight; ?>
		                            </td>
		                            <td width="" align="right" title="Chemical Cost+Dyes Cost+Finishing Cost"><? echo number_format($batch_based_chemical_cost_finish+$batch_based_batch_chemical_price,4,".",""); ?></td>
                                </tr>
                                <?
                                $j++;
                                $tot_batch_based_fabric_wgt+=$row['production_qnty'];
								$tot_batch_based_trims_qnty+=$row["total_trims_weight"];
                                $tot_batch_based_batch_qnty+=$batchQnty;
								$tot_batch_based_machine_capacity+=$machine_capacity_arr[$row['machine_id']];
								$total_batch_based_batch_weight+=$batch_weight;
		                    }
                            ?>
                        </tbody>
                    </table>
                </div>
                <table class="rpt_table" width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tfoot>
                        <tr>
                        	<th width="30">&nbsp;</th>  
                            <th width="50">&nbsp;</th>
                            <th width="100" title="Revised Number">&nbsp;</th>
                            <th width="110">&nbsp;</th>
                            <th width="80" title="Buyer">&nbsp;</th>
                            <th width="80" title="Client">&nbsp;</th>
                            <th width="80" title="Prod. Dept.">&nbsp;</th>
                            <th width="80" title="Season">&nbsp;</th>
                            <th width="100" title="Style Ref">&nbsp;</th>
                            <th width="100" title="booking_type">&nbsp;</th>
                            <th width="50" title="Color Type">&nbsp;</th>
                            <th width="100" title="Fabrication">&nbsp;</th>
                            <th width="150" title="Composition">&nbsp;</th>
                            <th width="80" title="Composition">&nbsp;</th>
                            <th width="100" title="Count">&nbsp;</th>
                            <th width="100" title="Lot">&nbsp;</th>
                            <th width="100" title="Stitch">&nbsp;</th>
                            <th width="100" title="Program">&nbsp;</th>
                            <th width="100" title="Grey Dia">&nbsp;</th>
                            <th width="60" title="GSM">&nbsp;</th>
                            <th width="80" title="Finish Dia">&nbsp;</th>
                            <th width="150" title="Source">&nbsp;</th>
                            <th width="60" title="Party Name"><strong>Total</strong></th>
                            <th width="100"><? echo number_format($tot_batch_based_fabric_wgt,2,'.',''); ?></th>
                            <th width="100"><? echo number_format($tot_batch_based_trims_qnty,2,'.',''); ?></th>
                            <th width="100"><? echo number_format($tot_batch_based_batch_qnty,2,'.',''); ?></th>
                            <th width="100"><? echo number_format($tot_batch_based_machine_capacity,2,'.',''); ?></th>
                            <th width="100" title="Process Loss">&nbsp;</th>
                            <th width="100" title="Grey Req">&nbsp;</th>
                            <th width="100" title="Inside Prod">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70" title="Stock in Hand">&nbsp;</th>
                            <th width="70" title="Order Sheet Rcv Date">&nbsp;</th>
                            <th width="70" title="Po Received Date">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70" title="Remarks">&nbsp;</th>
                            <th width="70" title="Fabric Type"></th>
                            <th width="90"><? echo number_format($total_batch_based_chemical_cost,4); ?></th>
                            <th width="90"><? echo number_format($total_batch_based_dyes_cost,4); ?></th>
                            <th width="90"><? echo number_format($total_batch_based_batch_chemical_price,4); ?></th>
                            <th width="90"><? echo number_format($total_batch_based_batch_chemical_price/$total_batch_based_batch_weight,4); ?></th>
                            <th width="90"><? echo number_format($tot_batch_based_re_dying_chemical_cost,4); ?></th>
                            <th width="90"><? echo number_format($tot_batch_based_re_dying_dyes_cost,4); ?></th>
                            <th width=""><? echo number_format($total_batch_based_batch_chemical_price+$tot_batch_based_re_dying_dyes_chemical_cost,4); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!-- Batch Based End-->

            <!-- Independent Based Start-->
            <div align="left">
                <caption><b>Independent</b></caption>
                <table class="rpt_table" width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                    <thead>
                        <tr>
                            <th width="30" rowspan="2" class="word_wrap_break">SL</th>
                            <th width="50" rowspan="2" class="word_wrap_break">Date</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Machine No</th>
                            <th width="110" rowspan="2" class="word_wrap_break">Floor</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Shift</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Buyer</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Style</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Season</th>
                            <th width="100" rowspan="2" class="word_wrap_break">FSO No</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Booking No</th>
                            <th width="50" rowspan="2" class="word_wrap_break">Booking Type</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Body Part</th>
                            <th width="150" rowspan="2" class="word_wrap_break">Fabric Description</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Dia/ Width Type</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Lot No</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Yarn Info.</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Color Type</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Color Name</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Color Range</th>
                            <th width="60" rowspan="2" class="word_wrap_break">Dyes Percent</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Shade%</th>
                            <th width="150" rowspan="2" class="word_wrap_break">Batch No</th>
                            <th width="60" rowspan="2" class="word_wrap_break">Ext. No</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Fabric Wgt.</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Trims Wgt.</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Batch Weight(Kg)</th>
                            <th width="100" rowspan="2" class="word_wrap_break">M/C Capacity.</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Loading %.</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Load Date Time</th>
                            <th width="100" rowspan="2" class="word_wrap_break">UnLoad Date Time</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Time Used</th>
                            <th width="70" rowspan="2" class="word_wrap_break">BTB / LTB</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Dyeing Fab. Type</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Result</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Process Name</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Dyeing Re Process Name</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Hour Load Meter</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Hour UnLoad Meter</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Total Time</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Water Loading Flow</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Water UnLoading Flow</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Water Cons.</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Remark</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Recipe No</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Requisition No</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Basis</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Issue Purpose</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Required Process Name</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Fabric Type</th>

                            <th colspan="4" class="word_wrap_break">Dyeing Cost</th>
                            <th colspan="2" class="word_wrap_break">Finishing Cost Detail</th>

                            <th width="" rowspan="2" class="word_wrap_break">Total Cost (Tk)</th>
                        </tr>
                        <tr>
                        	<th width="90" class="word_wrap_break">Tot Chem Cost (Tk)</th>
                            <th width="90" class="word_wrap_break">Tot Dyes Cost (Tk)</th>
                            <th width="90" class="word_wrap_break">Tot Chem + Dyes Cost (Tk)</th>
                            <th width="90" class="word_wrap_break">Cost Per Kg (Tk)</th>
                            <th width="90" class="word_wrap_break">Tot Finishing Cost</th>
                            <th width="90" class="word_wrap_break">Tot Finishing Cost Per Kg (Tk)</th>
                        </tr>
                    </thead>
                </table>
                <div style=" max-height:350px; width:<?echo $div_width;?>px; overflow-y:scroll;" id="scroll_body_indep">
                    <table class="rpt_table" id="table_body4" width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                        <tbody>
                            <?
                            $j=1;
                            foreach($independent_based_data_arr as $batch_id => $row)
                            {
								if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";							
								// $independent_based_batch_weight=0;	
								$issue_basis=$row['issue_basis'];
								$issue_purpose=$row['issue_purpose'];

								$independent_based_first_chemi_cost=$independent_based_dyes_chemical_arr[$batch_id][5]['chemical_cost']+$independent_based_dyes_chemical_arr[$batch_id][7]['chemical_cost'];
				                $independent_based_first_dyeing_cost=$independent_based_dyes_chemical_arr[$batch_id][6]['chemical_cost'];
				                $independent_based_chemical_cost_finish=$independent_based_dyes_chemical_arr[$batch_id][5]['chemical_cost_finish']+$independent_based_dyes_chemical_arr[$batch_id][7]['chemical_cost_finish'];

                                ?>
	                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr4_<? echo $j; ?>" onClick="change_color('tr4_<? echo $j; ?>','<? echo $bgcolor; ?>')">
                                    <td class="word_wrap_break" width="30"><? echo $j; ?></td>
                                    <td width="50"><? echo change_date_format($row['issue_date']); ?></td>
                                    <td width="100"></td>
                                    <td width="110"></td>
                                    <td width="80"></td>
                                    <td width="80"></td>
                                    <td width="80"></td>
                                    <td width="80"></td>
                                    <td width="100"></td>      
                                    <td width="100"></td>
                                    <td width="50"></td>
                                    <td width="100"></td>
                                    <td width="150"></td>
                                    <td width="80"></td>
									<td width="100"></td>
                                    <td width="100"></td>
                                    <td width="100"></td>
                                    <td width="100"></td>
                                    <td width="100"></td>
                                    <td width="60"></td>
                                    <td width="80"></td>
                                    <td width="150"></td>
                                    <td width="60"></td>
                                    <td width="100"></td>
                                    <td width="100"></td>
                                    <td width="100"></td>
                                    <td width="100"></td>
                                    <td width="100"></td>
                                    <td width="100"></td>
                                    <td width="100"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>

                                    <td width="70" class="word_wrap_break"><? echo $issue_basis; ?></td>
                                    <td width="70" class="word_wrap_break"><? echo $issue_purpose; ?></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="90" align="right" title="Tot Chem Cost (Tk)"><?echo number_format($independent_based_first_chemi_cost,4,".",""); $total_independent_based_chemical_cost+=$independent_based_first_chemi_cost;?></td>
		                            <td width="90" align="right" title="Tot Dyes Cost (Tk)"><?echo number_format($independent_based_first_dyeing_cost,4,".",""); $total_independent_based_dyes_cost+=$independent_based_first_dyeing_cost; ?></td>
		                            <td width="90" align="right" title="Tot Chem + Dyes Cost (Tk)"><?$independent_based_batch_chemical_price=$independent_based_first_chemi_cost+$independent_based_first_dyeing_cost; 
		                            	echo number_format($independent_based_batch_chemical_price,4,".",""); 
		                            	$total_independent_based_batch_chemical_price+=$independent_based_batch_chemical_price;?></td>
		                            <td width="90" align="right" title="Tot Chemical+Dyes Cost"></td>
		                            <td width="90" align="right" title="Category=Chemical and Auxilary Chemicals"><?
					                echo number_format($independent_based_chemical_cost_finish,4,".",""); 
					                $tot_independent_based_re_dying_chemical_cost+=$independent_based_chemical_cost_finish;
					                ?></td>
		                            <td width="90" align="right" title="Tot Finishing Cost"></td>
		                            <td width="" align="right" title="Chemical Cost+Dyes Cost+Finishing Cost"><? echo number_format($independent_based_chemical_cost_finish+$independent_based_batch_chemical_price,4,".",""); ?></td>
                                </tr>
                                <?
                                $j++;
								$total_batch_based_batch_weight=0;
		                    }
                            ?>
                        </tbody>
                    </table>
                </div>
                <table class="rpt_table" width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tfoot>
                        <tr>
                        	<th width="30">&nbsp;</th>  
                            <th width="50">&nbsp;</th>
                            <th width="100" title="Revised Number">&nbsp;</th>
                            <th width="110">&nbsp;</th>
                            <th width="80" title="Buyer">&nbsp;</th>
                            <th width="80" title="Client">&nbsp;</th>
                            <th width="80" title="Prod. Dept.">&nbsp;</th>
                            <th width="80" title="Season">&nbsp;</th>
                            <th width="100" title="Style Ref">&nbsp;</th>
                            <th width="100" title="booking_type">&nbsp;</th>
                            <th width="50" title="Color Type">&nbsp;</th>
                            <th width="100" title="Fabrication">&nbsp;</th>
                            <th width="150" title="Composition">&nbsp;</th>
                            <th width="80" title="Composition">&nbsp;</th>
                            <th width="100" title="Count">&nbsp;</th>
                            <th width="100" title="Lot">&nbsp;</th>
                            <th width="100" title="Stitch">&nbsp;</th>
                            <th width="100" title="Program">&nbsp;</th>
                            <th width="100" title="Grey Dia">&nbsp;</th>
                            <th width="60" title="GSM">&nbsp;</th>
                            <th width="80" title="Finish Dia">&nbsp;</th>
                            <th width="150" title="Source">&nbsp;</th>
                            <th width="60" title="Party Name"><strong>Total</strong></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="100" title="Process Loss">&nbsp;</th>
                            <th width="100" title="Grey Req">&nbsp;</th>
                            <th width="100" title="Inside Prod">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70" title="Stock in Hand">&nbsp;</th>
                            <th width="70" title="Order Sheet Rcv Date">&nbsp;</th>
                            <th width="70" title="Po Received Date">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70" title="Remarks">&nbsp;</th>
                            <th width="70" title="Fabric Type">&nbsp;</th>

                            <th width="90"><? echo number_format($total_independent_based_chemical_cost,4); ?></th>
                            <th width="90"><? echo number_format($total_independent_based_dyes_cost,4); ?></th>
                            <th width="90"><? echo number_format($total_independent_based_batch_chemical_price,4); ?></th>
                            <th width="90"></th>
                            <th width="90"><? echo number_format($tot_independent_based_re_dying_chemical_cost,4); ?></th>
                            <th width="90"></th>
                            <th width=""><? echo number_format($total_independent_based_batch_chemical_price+$tot_independent_based_re_dying_dyes_chemical_cost,4); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!-- Independent Based End-->

            <!-- Machine Wash Start-->
            <div align="left">
                <caption><b>Machine Wash</b></caption>
                <table class="rpt_table" width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                    <thead>
                        <tr>
                            <th width="30" rowspan="2" class="word_wrap_break">SL</th>
                            <th width="50" rowspan="2" class="word_wrap_break">Date</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Machine No</th>
                            <th width="110" rowspan="2" class="word_wrap_break">Floor</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Shift</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Buyer</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Style</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Season</th>
                            <th width="100" rowspan="2" class="word_wrap_break">FSO No</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Booking No</th>
                            <th width="50" rowspan="2" class="word_wrap_break">Booking Type</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Body Part</th>
                            <th width="150" rowspan="2" class="word_wrap_break">Fabric Description</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Dia/ Width Type</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Lot No</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Yarn Info.</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Color Type</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Color Name</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Color Range</th>
                            <th width="60" rowspan="2" class="word_wrap_break">Dyes Percent</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Shade%</th>
                            <th width="150" rowspan="2" class="word_wrap_break">Batch No</th>
                            <th width="60" rowspan="2" class="word_wrap_break">Ext. No</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Fabric Wgt.</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Trims Wgt.</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Batch Weight(Kg)</th>
                            <th width="100" rowspan="2" class="word_wrap_break">M/C Capacity.</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Loading %.</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Load Date Time</th>
                            <th width="100" rowspan="2" class="word_wrap_break">UnLoad Date Time</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Time Used</th>
                            <th width="70" rowspan="2" class="word_wrap_break">BTB / LTB</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Dyeing Fab. Type</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Result</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Process Name</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Dyeing Re Process Name</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Hour Load Meter</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Hour UnLoad Meter</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Total Time</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Water Loading Flow</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Water UnLoading Flow</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Water Cons.</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Remark</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Recipe No</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Requisition No</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Basis</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Issue Purpose</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Required Process Name</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Fabric Type</th>

                            <th colspan="4" class="word_wrap_break">Dyeing Cost</th>
                            <th colspan="2" class="word_wrap_break">Finishing Cost Detail</th>

                            <th width="" rowspan="2" class="word_wrap_break">Total Cost (Tk)</th>
                        </tr>
                        <tr>
                        	<th width="90" class="word_wrap_break">Tot Chem Cost (Tk)</th>
                            <th width="90" class="word_wrap_break">Tot Dyes Cost (Tk)</th>
                            <th width="90" class="word_wrap_break">Tot Chem + Dyes Cost (Tk)</th>
                            <th width="90" class="word_wrap_break">Cost Per Kg (Tk)</th>
                            <th width="90" class="word_wrap_break">Tot Finishing Cost</th>
                            <th width="90" class="word_wrap_break">Tot Finishing Cost Per Kg (Tk)</th>
                        </tr>
                    </thead>
                </table>
                <div style=" max-height:350px; width:<?echo $div_width;?>px; overflow-y:scroll;" id="scroll_body_mc">
                    <table class="rpt_table" id="table_body5" width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                        <tbody>
                            <?
                            $j=1;
                            foreach($machine_wash_data_arr as $batch_id => $row)
                            {
								if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$issue_basis=$row['issue_basis'];
								$issue_purpose=$row['issue_purpose'];

								$machine_wash_first_chemi_cost=$machine_wash_dyes_chemical_arr[$batch_id][5]['chemical_cost']+$machine_wash_dyes_chemical_arr[$batch_id][7]['chemical_cost'];
				                $machine_wash_first_dyeing_cost=$machine_wash_dyes_chemical_arr[$batch_id][6]['chemical_cost'];
				                $machine_wash_chemical_cost_finish=$machine_wash_dyes_chemical_arr[$batch_id][5]['chemical_cost_finish']+$machine_wash_dyes_chemical_arr[$batch_id][7]['chemical_cost_finish'];

                                ?>
	                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr5_<? echo $j; ?>" onClick="change_color('tr5_<? echo $j; ?>','<? echo $bgcolor; ?>')">
                                    <td class="word_wrap_break" width="30"><? echo $j; ?></td>
                                    <td width="50" class="word_wrap_break"><? echo change_date_format($row['issue_date']); ?></td>
                                    <td width="100" class="word_wrap_break"><? echo $machine_arr[$row['machine_id']];?></td>
                                    <td width="110"></td>
                                    <td width="80"></td>
                                    <td width="80"></td>
                                    <td width="80"></td>
                                    <td width="80"></td>
                                    <td width="100"></td>      
                                    <td width="100"></td>
                                    <td width="50"></td>
                                    <td width="100"></td>
                                    <td width="150"></td>
                                    <td width="80"></td>
									<td width="100"></td>
                                    <td width="100"></td>
                                    <td width="100"></td>
                                    <td width="100"></td>
                                    <td width="100"></td>
                                    <td width="60"></td>
                                    <td width="80"></td>
                                    <td width="150"></td>
                                    <td width="60"></td>
                                    <td width="100"></td>
                                    <td width="100"></td>
                                    <td width="100"></td>
                                    <td width="100"></td>
                                    <td width="100"></td>
                                    <td width="100"></td>
                                    <td width="100"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="70" class="word_wrap_break"><? echo $row['requ_no']; ?></td>

                                    <td width="70" class="word_wrap_break"><? echo $issue_basis; ?></td>
                                    <td width="70" class="word_wrap_break"><? echo $issue_purpose; ?></td>
                                    <td width="70"></td>
                                    <td width="70"></td>
                                    <td width="90" align="right" title="Tot Chem Cost (Tk)"><?echo number_format($machine_wash_first_chemi_cost,4,".",""); $total_machine_wash_chemical_cost+=$machine_wash_first_chemi_cost;?></td>
		                            <td width="90" align="right" title="Tot Dyes Cost (Tk)"><?echo number_format($machine_wash_first_dyeing_cost,4,".",""); $total_machine_wash_dyes_cost+=$machine_wash_first_dyeing_cost; ?></td>
		                            <td width="90" align="right" title="Tot Chem + Dyes Cost (Tk)"><?$machine_wash_batch_chemical_price=$machine_wash_first_chemi_cost+$machine_wash_first_dyeing_cost; 
		                            	echo number_format($machine_wash_batch_chemical_price,4,".",""); 
		                            	$total_machine_wash_batch_chemical_price+=$machine_wash_batch_chemical_price;?></td>
		                            <td width="90" align="right" title="Tot Chemical+Dyes Cost"></td>
		                            <td width="90" align="right" title="Category=Chemical and Auxilary Chemicals"><?
					                echo number_format($machine_wash_chemical_cost_finish,4,".",""); 
					                $tot_machine_wash_re_dying_chemical_cost+=$machine_wash_chemical_cost_finish;
					                ?></td>
		                            <td width="90" align="right" title="Tot Finishing Cost"></td>
		                            <td width="" align="right" title="Chemical Cost+Dyes Cost+Finishing Cost"><? echo number_format($machine_wash_chemical_cost_finish+$machine_wash_batch_chemical_price,4,".",""); ?></td>
                                </tr>
                                <?
                                $j++;
								$total_batch_based_batch_weight=0;
		                    }
                            ?>
                        </tbody>
                    </table>
                </div>
                <table class="rpt_table" width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tfoot>
                        <tr>
                        	<th width="30">&nbsp;</th>  
                            <th width="50">&nbsp;</th>
                            <th width="100" title="Revised Number">&nbsp;</th>
                            <th width="110">&nbsp;</th>
                            <th width="80" title="Buyer">&nbsp;</th>
                            <th width="80" title="Client">&nbsp;</th>
                            <th width="80" title="Prod. Dept.">&nbsp;</th>
                            <th width="80" title="Season">&nbsp;</th>
                            <th width="100" title="Style Ref">&nbsp;</th>
                            <th width="100" title="booking_type">&nbsp;</th>
                            <th width="50" title="Color Type">&nbsp;</th>
                            <th width="100" title="Fabrication">&nbsp;</th>
                            <th width="150" title="Composition">&nbsp;</th>
                            <th width="80" title="Composition">&nbsp;</th>
                            <th width="100" title="Count">&nbsp;</th>
                            <th width="100" title="Lot">&nbsp;</th>
                            <th width="100" title="Stitch">&nbsp;</th>
                            <th width="100" title="Program">&nbsp;</th>
                            <th width="100" title="Grey Dia">&nbsp;</th>
                            <th width="60" title="GSM">&nbsp;</th>
                            <th width="80" title="Finish Dia">&nbsp;</th>
                            <th width="150" title="Source">&nbsp;</th>
                            <th width="60" title="Party Name"><strong>Total</strong></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="100" title="Process Loss">&nbsp;</th>
                            <th width="100" title="Grey Req">&nbsp;</th>
                            <th width="100" title="Inside Prod">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70" title="Stock in Hand">&nbsp;</th>
                            <th width="70" title="Order Sheet Rcv Date">&nbsp;</th>
                            <th width="70" title="Po Received Date">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70" title="Remarks">&nbsp;</th>
                            <th width="70" title="Fabric Type">&nbsp;</th>

                            <th width="90"><? echo number_format($total_machine_wash_chemical_cost,4); ?></th>
                            <th width="90"><? echo number_format($total_machine_wash_dyes_cost,4); ?></th>
                            <th width="90"><? echo number_format($total_machine_wash_batch_chemical_price,4); ?></th>
                            <th width="90"></th>
                            <th width="90"><? echo number_format($tot_machine_wash_re_dying_chemical_cost,4); ?></th>
                            <th width="90"></th>
                            <th width=""><? echo number_format($total_machine_wash_batch_chemical_price+$tot_machine_wash_re_dying_dyes_chemical_cost,4); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!-- Machine Wash End-->
        </fieldset>
    </div>
	<?

    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
        @unlink($filename);
    }
    $name=time();
    $filename=$user_name."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$report_type";
    exit();
}

?>