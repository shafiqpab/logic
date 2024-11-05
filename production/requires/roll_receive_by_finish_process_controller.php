<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 130, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select --", $selected, "" );	
	exit();	 
}

if($action=="challan_popup")
{
	echo load_html_head_contents("Challan Info", "../../", 1, 1,'','','', 1);
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(data,barcode_nos,hidden_batch_id)
		{			
			$('#hidden_data').val(data);
			$('#hidden_barcode_nos').val(barcode_nos);
			$('#hidden_batch_id').val(hidden_batch_id);
			parent.emailwindow.hide();
		}
	</script>

</head>

<body>
	<div align="center" style="width:760px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:760px; margin-left:2px">
				<table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Company</th>
						<th>Delivery Date Range</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="180">Please Enter Challan No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="hidden_data" id="hidden_data">  
							<input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">  
							<input type="hidden" name="hidden_batch_id" id="hidden_batch_id">
							<input type="hidden" name="hidden_batch" id="hidden_batch">
						</th> 
					</thead>
					<tr class="general">
						<td align="center">
							<? echo create_drop_down( "cbo_company_id", 150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '-- Select Company --',0,"",0); ?>        
						</td>
						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
						</td>
						<td align="center">	
							<?
							$search_by_arr=array(1=>"Challan No",2=>"Barcode No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
							?>
						</td>     
						<td align="center" id="search_by_td">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
						</td> 						
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_id').value, 'create_challan_search_list_view', 'search_div', 'roll_receive_by_finish_process_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_challan_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0]);
	
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];

	if($company_id==0) { echo "Please Select Company First."; die; }
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and delevery_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and delevery_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	if($db_type==2) 
	{
		$group_con="LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id desc) as mst_id";
	}
	else
	{
		$group_con="group_concat(mst_id order by mst_id desc) as mst_id";
	}
	
	if($search_by==2)
	{
		$barcode_no=trim($data[0]);
		$mst_id='';
		//echo "select $group_con from pro_grey_prod_delivery_dtls barcode_num=$search_string and entry_form=56 and status_active=1 and is_deleted=0";
		if($barcode_no!='')
		{
			$mst_id= return_field_value("$group_con","pro_grey_prod_delivery_dtls","barcode_num=$barcode_no and entry_form=56 and status_active=1 and is_deleted=0 ","mst_id");
		}
	}
	
	$search_field_cond="";
	if(trim($data[0])!="")
	{
		if($search_by==1) $search_field_cond="and sys_number like '$search_string'";
		else if($search_by==2 && $mst_id!="") $search_field_cond="and id in($mst_id)";
	}
	
	if($db_type==0) 
	{
		$year_field="YEAR(insert_date) as year,";
		$barcode_arr=return_library_array( "select mst_id, group_concat(barcode_num order by id desc) as barcode_num from pro_grey_prod_delivery_dtls where entry_form=56 and status_active=1 and is_deleted=0 group by mst_id",'mst_id','barcode_num');
	}
	else if($db_type==2) 
	{
		$year_field="to_char(insert_date,'YYYY') as year,";
		$barcode_arr=return_library_array( "select mst_id, LISTAGG(barcode_num, ',') WITHIN GROUP (ORDER BY id desc) as barcode_num from pro_grey_prod_delivery_dtls where entry_form=56 and status_active=1 and is_deleted=0 group by mst_id",'mst_id','barcode_num');
	}
	else $year_field="";//defined Later
	
	$sql = "select id, $year_field sys_number_prefix_num, sys_number, company_id, knitting_source, knitting_company, location_id, delevery_date from pro_grey_prod_delivery_mst where entry_form=56 and status_active=1 and is_deleted=0 and company_id=$company_id $search_field_cond $date_cond order by id"; 
	//echo $sql;//die;
	$result = sql_select($sql);

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="130">Company</th>
			<th width="120">Location</th>
			<th width="70">Challan No</th>
			<th width="60">Year</th>
			<th width="90">Knitting Source</th>
			<th width="130">Knitting Company</th>
			<th>Delivery date</th>
		</thead>
	</table>
	<div style="width:750px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="tbl_list_search">  
			<?
			$i=1;
			foreach ($result as $row)
			{  
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	

				$knit_comp="&nbsp;";
				if($row[csf('knitting_source')]==1)
					$knit_comp=$company_arr[$row[csf('knitting_company')]]; 
				else
					$knit_comp=$supllier_arr[$row[csf('knitting_company')]];
				
				$data=$row[csf('id')]."**".$row[csf('sys_number')]."**".change_date_format($row[csf('delevery_date')])."**".$row[csf('company_id')]."**".$row[csf('location_id')]."**".$row[csf('knitting_source')]."**".$row[csf('knitting_company')]."**".$knit_comp;
				$barcode_nos=$barcode_arr[$row[csf('id')]];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $data; ?>','<? echo $barcode_nos; ?>');"> 
					<td width="40"><? echo $i; ?></td>
					<td width="130"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
					<td width="120"><p><? echo $location_arr[$row[csf('location_id')]]; ?>&nbsp;</p></td>
					<td width="70"><p>&nbsp;<? echo $row[csf('sys_number_prefix_num')]; ?></p></td>
					<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="90"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?>&nbsp;</p></td>
					<td width="130"><p><? echo $knit_comp; ?>&nbsp;</p></td>
					<td align="center"><? echo change_date_format($row[csf('delevery_date')]); ?></td>
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

if($action=="update_system_popup")
{
	echo load_html_head_contents("Challan Info", "../../", 1, 1,'','','', 1);
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(data, receive_date, batch_no)
		{
			$('#hidden_receive_no').val(data);
			$('#hidden_receive_date').val(receive_date);
			$('#hidden_batch_no').val(batch_no);
			parent.emailwindow.hide();
		}
	</script>

</head>

<body>
	<div align="center" style="width:100%;" >
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:760px; margin-left:2px">
				<legend>Receive Number Popup</legend>           
				<table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<th>Company</th>
						<th width="250">System ID</th>
						<th width="250">Receive Date</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="hidden_receive_no" id="hidden_receive_no">
							<input type="hidden" name="hidden_receive_date" id="hidden_receive_date">
							<input type="hidden" name="hidden_batch_no" id="hidden_batch_no" />
						</th> 
					</thead>
					<tr class="general">
						<td align="center">
							<? echo create_drop_down( "cbo_company_id", 170,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '-- Select Company --',0,"",0); ?>        
						</td>
						<td align="center">	
							<input style="width:130px" class="text_boxes" name="txt_system_no" id="txt_system_no" title="  Allowed Characters: abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890.-,%@!/&lt;&gt;?+[]{};: " type="text">
						</td>
						<td align="center">	
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:90px" readonly> To 
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:90px" readonly>
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_system_no').value+'_' + document.getElementById('txt_date_from').value +'_'+document.getElementById('txt_date_to').value+'_', 'create_update_search_list_view', 'search_div', 'roll_receive_by_finish_process_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_update_search_list_view")
{	
	$data 				= explode("_",$data);	
	$company_id 		= $data[0];
	$txt_system_no 		= $data[1];
	$start_date 		= $data[2];
	$end_date 			= $data[3];
	if($company_id == 0) { echo "Please Select Company First."; die; }
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and s.receive_date >= '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and s.receive_date <= '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."' ";
		} else
		{
			
			$date_cond="and receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	if($txt_system_no != ''){
		$sys_cond = " and s.recv_number like '%$txt_system_no%'";
	}else{
		$sys_cond = '';
	}
	
	if(trim($year_id) != 0) 
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$year_id";
		else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$year_id";
		else $year_cond="";
	}
	else $year_cond="";
	
	$search_field_cond="";
	if(trim($receive_number)!="")
	{
		$receiv_cond="and a.recv_number_prefix_num like '%$receive_number%' ";
	}
	
	if($db_type==0) 
	{
		$year_field=" YEAR(a.insert_date) as year";
	}
	else if($db_type==2) 
	{
		$year_field=" to_char(a.insert_date,'YYYY') as year";

	}
	else $year_field=""; //defined Later
	
	$sql="select s.recv_number, s.receive_date, s.batch_no, s.load_unload_id, s.result, s.company_id, s.machine_id,s.service_company, s.service_source
			from pro_fab_subprocess s
			where s.company_id = $company_id and s.entry_form = 115 and s.load_unload_id = 2 
			and s.result = 1 $date_cond $sys_cond order by s.recv_number desc";

	$result = sql_select($sql);
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="140">Company</th>
			<th width="80">Receive No</th>
			<th width="70">Year</th>
			<th width="120">Dyeing Source</th>
			<th width="140">Dyeing Company</th>
			<th width="130">Receive date</th>
		</thead>
	</table>
	<div style="width:820px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_list_search">  
			<?
			$i=1;
			foreach ($result as $row)
			{  
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	

				$knit_comp="&nbsp;";
				if($row[csf('dyeing_source')]==1)
					$knit_comp=$company_arr[$row[csf('service_company')]]; 
				else
					$knit_comp=$supllier_arr[$row[csf('service_company')]];

				$qc_pass_qnty=return_field_value("sum(qc_pass_qnty) as qc_pass_qnty","pro_roll_details","mst_id='".$row[csf('id')]."' and entry_form=62 and status_active=1 and is_deleted=0","qc_pass_qnty");

				$data = $row[csf('recv_number')] . "**" . $row[csf('service_source')] . "**" . $knit_comp;

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $data; ?>', '<? echo $row[csf('receive_date')]; ?>', '<? echo $row[csf('batch_no')]; ?>');">
					<td width="40"><? echo $i; ?></td>
					<td width="140"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
					<td width="80"><p>&nbsp;<? echo $row[csf('recv_number')]; ?></p></td>
					<td width="70" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="120"><p><? echo $knitting_source[$row[csf('service_source')]]; ?>&nbsp;</p></td>
					<td width="140"><p><? echo $knit_comp; ?>&nbsp;</p></td>
					<td width="130" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
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

if($action=="get_barcode_batch_no")
{
	$batch_no = sql_select("select (select E.BATCH_NO from pro_batch_create_mst e where d.mst_id = e.id) batch_no
		from pro_roll_details c, pro_batch_create_dtls d
		where c.id = D.ROLL_ID and c.barcode_no in('$data')");
	echo $batch_no[0]['BATCH_NO'];
	exit();	
}
if($action=="barcode_popup")
{
	echo load_html_head_contents("Barcode Info", "../../", 1, 1,'','','', 1);
	extract($_REQUEST);
	if($company_id>0) $disable=1; else $disable=0;  
	?> 
	<script>
		var selected_id = new Array();
		var selected_batch = new Array();
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			var selected_batch_id = [];
			if( jQuery.inArray( $('#txt_batch_id' + str).val(), selected_batch ) == -1 ) {				
				if(selected_batch.length > 0){
					toggle( document.getElementById( 'search' + str ), '#FFFFFF' );
					alert("Multiple Batch is not allowed");
				}else{
					selected_batch.push( $('#txt_batch_id' + str).val() );
					selected_batch_id.push( $('#txtBatchId' + str).val() );

					if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
						selected_id.push( $('#txt_individual_id' + str).val() );		
					} else {
						for( var i = 0; i < selected_id.length; i++ ) {
							if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
						}
						selected_id.splice( i, 1 );
					}
					var id = '';
					for( var i = 0; i < selected_id.length; i++ ) {
						id += selected_id[i] + ',';
					}
					id = id.substr( 0, id.length - 1 );
					$('#hidden_barcode_nos').val( id );
				}
			}else{
				if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
					selected_id.push( $('#txt_individual_id' + str).val() );		
				} else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
					}
					selected_id.splice( i, 1 );
				}
				var id = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
				}
				id = id.substr( 0, id.length - 1 );
				$('#hidden_barcode_nos').val( id );
			}

			var batch_id = '';
			var batchId = '';
			for( var i = 0; i < selected_batch.length; i++ ) {
				batch_id += selected_batch[i] + ',';
				batchId += selected_batch_id[i] + ',';
			}
			batch_id = batch_id.substr( 0, batch_id.length - 1 );
			selected_batch_id = batchId.substr( 0, batchId.length - 1 );
			//alert(batch_id);
			$('#hidden_batch_id').val( batch_id );
			$('#hiddenBatchId').val( selected_batch_id);
			
		}
		
		function fnc_close()
		{
			parent.emailwindow.hide();
		}
		
		function reset_hide_field()
		{
			$('#hidden_barcode_nos').val( '' );
			$('#hidden_batch_id').val( '' );
			selected_id = new Array();
		}

	</script>
</head>
<body>
	<div align="center" style="width:900px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:880px; margin-left:5px">
				<!--<legend>Enter search words</legend>-->           
				<table cellpadding="0" cellspacing="0" align="center" width="100%" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Company</th>
						<th>Location</th>
						<th>Batch No</th>
						<th width="250">Dyeing Production Date Range</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton" />
							<input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">  
							<input type="hidden" name="hidden_batch_id" id="hidden_batch_id">
							<input type="hidden" name="hiddenBatchId" id="hiddenBatchId">
						</th> 
					</thead>
					<tr class="general">
						<td align="center">
							<? 
							echo create_drop_down( "cbo_company_id", 150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '-- Select Company --',$company_id,"load_drop_down( 'roll_receive_by_finish_process_controller', this.value, 'load_drop_down_location', 'location_td' );",$disable); ?>        
						</td>
						<td align="center" id="location_td">	
							<? 
							if($company_id>0)
							{
								echo create_drop_down( "cbo_location_id", 130, "select id,location_name from lib_location where company_id='$company_id' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select --", $location_id, "",1 );	
							}
							else
							{
								echo create_drop_down( "cbo_location_id", 130, $blank_array,"", 1, "-- Select --", $selected, "",1,"" ); 
							}
							?>
						</td>     
						<td align="center">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_batch_no" id="txt_batch_no" />	
						</td>
						<td align="center">	
							<input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:90px" readonly /> To 
							<input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:90px" readonly />
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_id').value + '_' +  
							document.getElementById('txt_date_from').value +'_'+document.getElementById('txt_date_to').value+'_' + document.getElementById('txt_batch_no').value, 'create_barcode_search_list_view', 'search_div', 'roll_receive_by_finish_process_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:70px;" />
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_barcode_search_list_view")
{
	$data = explode("_",$data);
	$company_id		= $data[0];
	$start_date 	= $data[2];
	$end_date 		= $data[3];
	$batch_no		= $data[4];

	if($company_id == 0) { echo "Please Select Company First."; die; }
	
	if($start_date!="" && $end_date!="")
	{
		// prepare date field value
		if($db_type==0)
		{
			$start_date = ($start_date != '')?date('Y-m-d H:i:s',strtotime($start_date)) : ''; 
			$end_date = ($end_date != '')?date('Y-m-d H:i:s',strtotime($end_date . '23:59:59')) : ''; 
			$date_cond = "and s.insert_date >= '$start_date' and s.insert_date <='$end_date'";
		} else
		{
			$date_cond = "and s.insert_date between to_date('$start_date 00:00:00', 'dd/mm/yyyy hh24:mi:ss')  and to_date('$end_date 23:59:59', 'dd/mm/yyyy hh24:mi:ss') ";
		}
	} else {
		// batch no is mendatory if date range is not selected
		if($batch_no == '') { echo "Batch No is required."; die; }
		$date_cond = "";
	}

	// prepare sql to fetch row that are Un loaded and Shade Matched entered from Dyeing Production form
	$sql = "select s.batch_id,s.batch_no, s.load_unload_id, s.result, s.company_id, s.machine_id,s.entry_form, 
				bd.body_part_id,bd.roll_no, bd.roll_id,bd.po_id, bd.barcode_no, bd.batch_qnty, bd.program_no,
				(select b.color_id from pro_batch_create_mst b where s.batch_id = b.id)color_id
				from pro_fab_subprocess s 
				left join pro_batch_create_dtls bd on s.batch_id = bd.mst_id
				where s.company_id = $company_id and s.entry_form in(35) and s.load_unload_id = 2 
				and s.batch_no like '%$batch_no%'
				and s.result = 1 and bd.barcode_no != 0 $date_cond
				and s.id not in( select a.mst_id from pro_fab_subprocess_dtls a where a.roll_id = bd.roll_id and a.entry_page = 115 and s.id = a.mst_id)";
	$result = sql_select($sql);
	
	// prepare array libraries
	$company_arr 	= return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name");
	$location_arr 	= return_library_array( "select id, location_name from lib_location", "id", "location_name");
	$machine_arr 	= return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no");
	$color_arr 		= return_library_array( "select id, color_name from lib_color",'id','color_name');
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table">
		<thead>
			<th width="40">SL</th>			
			<th width="80">Roll No</th>			
			<th width="100">Barcode No</th>
			<th width="120">Batch Quantity</th>
			<th width="80">Color</th>
			<th width="120">Batch No</th>
			<th width="120">Company</th>
		</thead>
	</table>
	<div style="width:900px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table" id="tbl_list_search">  
			<?
			$i=1;
			foreach ($result as $row)           
			{  
				$color = '';
				$color_ids = explode(",",$row[csf('color_id')]);
				foreach($color_ids as $color_id)
				{
					if($color_id>0) $color .= $color_arr[$color_id].",";
				}
				$color = chop($color,',');
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
					<td width="40"><? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<? echo $row[csf('barcode_no')]; ?>"/>
						<input type="hidden" name="txt_batch_id" id="txt_batch_id<?php echo $i; ?>" value="<? echo $row[csf('batch_no')]; ?>"/>
						<input type="hidden" name="txt_batch" id="txt_batch_id<?php echo $i; ?>" value="<? echo $row[csf('batch_no')]; ?>"/>
						<input type="hidden" name="txtBatchId" id="txtBatchId<?php echo $i; ?>" value="<? echo $row[csf('batch_id')]; ?>"/>
					</td>					
					<td width="80"><? echo $row[csf('roll_no')]; ?></td>					
					<td width="100"><? echo $row[csf('barcode_no')]; ?></td>
					<td width="120"><? echo $row[csf('batch_qnty')]; ?></td>
					<td width="80"><? echo $color; ?></td>
					<td width="120"><p><? echo $row[csf('batch_no')]; ?></td>
					<td width="120"><? echo $company_arr[$row[csf('company_id')]]; ?></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<table width="680">
		<tr>
			<td align="center" >
				<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
			</td>
		</tr>
	</table>
	<?	
	exit();
}

if($action=="populate_barcode_data")
{
	$barcodeData 		= ''; 
	$po_ids_arr 		= array(); 
	$po_details_array	= array(); 
	$barcodeDataArr 	= array(); 
	$barcodeBuyerArr 	= array();

	// prepare array libraries
	$company_name_array = return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$machine_arr = return_library_array( "select id,machine_no from lib_machine_name", "id", "machine_no");
	$buyer_name_array = return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$color_arr = return_library_array( "select id, color_name from lib_color",'id','color_name');

	// prepare const./composition data
	$composition_arr = array(); $constructtion_arr=array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	// prepare sql to get details by Barcode
	$data_array = sql_select("select a.id, a.company_id, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, a.buyer_id, 
		b.id as dtls_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.body_part_id,b.machine_no_id, b.febric_description_id, b.color_id,
		c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
		where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($data)");
	$roll_details_array = array(); 
	$barcode_array = array(); 
	foreach($data_array as $row)
	{
		$color = '';
		$color_ids = explode(",",$row[csf('color_id')]);
		foreach($color_ids as $color_id)
		{
			if($color_id>0) $color .= $color_arr[$color_id].",";
		}
		$color = chop($color,',');

		if($row[csf("knitting_source")] == 1)
		{
			$knit_company = $company_name_array[$row[csf("knitting_company")]];
		}
		else if($row[csf("knitting_source")] == 3)
		{
			$knit_company = $supplier_arr[$row[csf("knitting_company")]];
		}
		$prodQnty = number_format(($row[csf("qnty")] + $row[csf("reject_qnty")]),2,'.','');		
		
		if($row[csf("booking_without_order")] != 1)
		{
			$po_ids_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
		}
		
		// 
		$barcodeDataArr[$row[csf('barcode_no')]] = $row[csf("barcode_no")].
		"**" . $row[csf("id")] . 
		"**" . $row[csf("company_id")] . 
		"**" . $body_part[$row[csf("body_part_id")]] . 
		"**" . $row[csf("booking_no")] . 
		"**" . $row[csf("knitting_source")] . 
		"**" . $row[csf("knitting_company")] . 
		"**" . $knit_company . 
		"**" . $row[csf("location_id")] . 
		"**" . $row[csf("dtls_id")] . 
		"**" . $row[csf("prod_id")] . 
		"**" . $row[csf("gsm")] . 
		"**" . $row[csf("width")] . 
		"**" . $row[csf("roll_id")] . 
		"**" . $row[csf("roll_no")] . 
		"**" . $row[csf("po_breakdown_id")] . 
		"**" . $color . 
		"**" . $row[csf("qnty")] . 
		"**" . $prodQnty . 
		"**" . $machine_arr[$row[csf("machine_no_id")]] . 
		"**" . $composition_arr[$row[csf('febric_description_id')]];
		
		$barcodeBuyerArr[$row[csf('barcode_no')]] = $row[csf("booking_without_order")]."__".$row[csf("po_breakdown_id")]."__".$row[csf("buyer_id")];
	}
	
	if(count($po_ids_arr)>0)
	{
		$data_array=sql_select("SELECT a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, b.id as po_id 
			FROM wo_po_details_master a, wo_po_break_down b 
			WHERE a.job_no=b.job_no_mst and b.id in(".implode(",",$po_ids_arr).")");
		$po_details_array = array();
		foreach($data_array as $row)
		{
			$po_details_array[$row[csf("po_id")]]['job_no'] = $row[csf("job_no_prefix_num")];
			$po_details_array[$row[csf("po_id")]]['buyer_name'] = $row[csf("buyer_name")];
			$po_details_array[$row[csf("po_id")]]['year'] = date("Y",strtotime($row[csf("insert_date")]));
			$po_details_array[$row[csf("po_id")]]['po_number'] = $row[csf("po_number")];
		}
	}
	
	if(count($barcodeDataArr)>0)
	{
		foreach($barcodeDataArr as $barcode_no => $value)
		{
			$barcodeDatas=explode("__", $barcodeBuyerArr[$barcode_no]);
			$booking_without_order = $barcodeDatas[0];
			$po_id = $barcodeDatas[1];
			
			if($booking_without_order == 1) 
			{
				$buyer_id = $barcodeDatas[2];
				$po_no = '';
				$job_no = '';
				$year = '';
			}
			else
			{
				$buyer_id = $po_details_array[$po_id]['buyer_name'];
				$po_no = $po_details_array[$po_id]['po_number'];
				$job_no = $po_details_array[$po_id]['job_no'];
				$year = $po_details_array[$po_id]['year'];
			}
			
			if($po_id == '') { $po_id = 0; }
			
			$barcodeData .= $value."**".$po_id."**".$buyer_id."**".$buyer_name_array[$buyer_id]."**".$po_no."**".$job_no."**".$year."_";
		}
		echo substr($barcodeData,0,-1);
	}
	else
	{
		echo "0";
	}
	
	exit();	
}

if($action=="populate_barcode_data_update")
{
	$barcodeData 		= ''; 
	$po_ids_arr 		= array(); 
	$po_details_array	= array(); 
	$barcodeDataArr 	= array(); 
	$barcodeBuyerArr 	= array();

	$data=explode('_',$data);

	// prepare array libraries
	$company_name_array = return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$machine_arr = return_library_array( "select id,machine_no from lib_machine_name", "id", "machine_no");
	$buyer_name_array = return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$color_arr = return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");

	// prepare const./composition data
	$composition_arr = array(); $constructtion_arr=array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	// prepare sql to get details by Barcode
	$data_array = sql_select("select a.recv_number, a.receive_date, a.batch_no, a.load_unload_id, a.result, a.company_id, a.machine_id,a.service_company, 
		a.service_source, a.booking_no, (select g.color_id from pro_batch_create_mst g where a.batch_id = g.id)color_id,
		b.id sub_dtls_id, b.roll_no, b.const_composition, b.gsm, b.dia_width, b.batch_qty, b.prod_id, b.roll_id,
		c.barcode_no, c.mst_id, c.dtls_id, c.po_breakdown_id,c.reject_qnty,
		d.febric_description_id, d.body_part_id, e.job_no_mst, f.buyer_name, e.t_year, e.file_no, e.po_number, f.job_no_prefix_num
		from pro_fab_subprocess a
		inner join pro_fab_subprocess_dtls b on a.id = b.mst_id
		inner join pro_roll_details c on b.roll_id = c.id
		inner join pro_grey_prod_entry_dtls d on c.dtls_id = d.id
		inner join wo_po_break_down e on c.po_breakdown_id = e.id
		inner join wo_po_details_master f on e.job_no_mst = f.job_no
		where a.company_id = $data[1] and a.entry_form = 115 and a.load_unload_id = 2
		and a.result = 1 and a.recv_number = '$data[0]'
		order by a.recv_number desc");

	$i = 1;
	if(!empty($data_array)){
		foreach($data_array as $row)
		{
			$color = '';
			$color_ids = explode(",",$row[csf('color_id')]);
			foreach($color_ids as $color_id)
			{
				if($color_id>0) $color .= $color_arr[$color_id].",";
			}
			$color = chop($color,',');

			if($row[csf("service_source")] == 1)
			{
				$knit_company = $company_name_array[$row[csf("service_company")]];
			}
			else if($row[csf("service_source")] == 3)
			{
				$knit_company = $supplier_arr[$row[csf("service_company")]];
			}
			$prodQnty = number_format(($row[csf("qnty")] + $row[csf("reject_qnty")]),2,'.','');		

			?>
			<tr id="tr_<? echo $i; ?>" align="center" valign="middle">
				<td width="30" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
				<td width="50" id="roll_<? echo $i; ?>"><? echo $row[csf("roll_no")]; ?></td>
				<td width="80" id="barcode_<? echo $i; ?>"><? echo $row[csf("barcode_no")]; ?></td>
				<td width="100" id="bodypart_<? echo $i; ?>"><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
				<td width="130" id="composition_<? echo $i; ?>" align="left"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
				<td width="40" id="gsm_<? echo $i; ?>"><? echo $row[csf("gsm")]; ?></td>
				<td width="45" id="dia_<? echo $i; ?>"><? echo $row[csf("dia_width")]; ?></td>
				<td width="100" id="color_<? echo $i; ?>" align="right"><? echo $color; ?></td>
				<td width="40" id="rollweight_<? echo $i; ?>" align="right"><? echo $row[csf("batch_qty")]; ?></td>
				<td width="80" id="job_<? echo $i; ?>"><? echo $row[csf("job_no_prefix_num")]; ?></td>
				<td width="40" id="year_<? echo $i; ?>" align="center"><? echo $row[csf("t_year")]; ?></td>
				<td width="55" id="buyer_<? echo $i; ?>"><? echo $buyer_name_array[$row[csf("buyer_name")]]; ?></td>
				<td width="130" id="order_<? echo $i; ?>"><? echo $row[csf("po_number")]; ?></td>
				<td width="40" id="file_<? echo $i; ?>"><? echo $row[csf("file_no")]; ?></td>
				<td width="75" id="knitcomp_<? echo $i; ?>"><? echo $knit_company; ?></td>
				<td width="75" id="mc_<? echo $i; ?>"><? echo $machine_arr[$row[csf("machine_id")]]; ?></td>
				<td width="85" id="program_<? echo $i; ?>"><? echo $row[csf("booking_no")]; ?></td>           
				<td width="50" id="batch_<? echo $i; ?>" style="word-break:break-all;" align="left"><? echo $row[csf("batch_no")]; ?></td>           
				<td id="button_<? echo $i; ?>" align="center">
					<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" <? echo $disable; ?> />
					<input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $i; ?>" value="<? echo $row[csf('barcode_no')]; ?>"/>
					<input type="hidden" name="productionId[]" id="productionId_<? echo $i; ?>" value="<? echo $row[csf("mst_id")]; ?>"/>
					<input type="hidden" name="productionDtlsId[]" id="productionDtlsId_<? echo $i; ?>" value="<? echo $row[csf("dtls_id")]; ?>"/>
					<input type="hidden" name="deterId[]" id="deterId_<? echo $i; ?>" value="<? echo $row[csf('febric_description_id')]; ?>"/>
					<input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<? echo $row[csf("prod_id")]; ?>"/>
					<input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row[csf("po_breakdown_id")]; ?>"/>
					<input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $row[csf("roll_no")]; ?>"/>
					<input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo $row[csf("roll_id")]; ?>"/>
					<input type="hidden" name="sub_dtls_id[]" id="sub_dtls_id_<? echo $i; ?>" value="<? echo $row[csf("sub_dtls_id")]; ?>"/>
					<input type="hidden" name="tot_row" id="tot_row" value="<? echo count($data_array); ?>"/>


				</td>
			</tr>
			<?
			$i ++;
		}
	}else{
		?>
		<tr id="tr_1" align="center" valign="middle">
			<td width="30" id="sl_1"></td>
			<td width="50" id="roll_1"></td>
			<td width="80" id="barcode_1"></td>
			<td width="100" id="bodypart_1"></td>
			<td width="130" id="composition_1"></td>
			<td width="40" id="gsm_1"></td>
			<td width="45" id="dia_1"></td>
			<td width="100" id="color_1"></td>
			<td width="40" id="rollweight_1"></td>
			<td width="80" id="job_1"></td>
			<td width="40" id="year_1"></td>
			<td width="55" id="buyer_1"></td>
			<td width="130" id="order_1"></td>
			<td width="40" id="file_1"></td>
			<td width="75" id="knitcomp_1"></td>
			<td width="75" id="mc_1"></td>
			<td width="85" id="program_1" align="right"></td>
			<td width="50" id="batch_1" align="right"></td>
			<td id="button_1" align="center">
				<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
				<input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/>
				<input type="hidden" name="productId[]" id="productId_1"/>
				<input type="hidden" name="rollId[]" id="rollId_1"/>
				<input type="hidden" name="orderId[]" id="orderId_1"/>
				<input type="hidden" name="sub_dtls_id[]" id="sub_dtls_id_1" />
			</td>
		</tr>
		<?
	}
	
	exit();	
}
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if($db_type==0) // MySQL
	{
		$receiveDte = ($delivery_date != '')?date('Y-m-d',strtotime($delivery_date)) : date('Y-m-d'); 
	}else{
		$receiveDte = ($delivery_date != '')? date('d-M-Y',strtotime($delivery_date)) : date('d-M-Y'); 
	}

	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if($db_type == 0)
		{
			mysql_query("BEGIN");
		}
		$data_array_dtls = '';
		$id = return_next_id( "id", "pro_fab_subprocess", 1 );
		$id_dtls = return_next_id( "id", "pro_fab_subprocess_dtls", 1 );
		
		if($db_type==0) $year_cond="YEAR(insert_date)"; 
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";
		// prepare Receive No format
		$new_recv_number = explode("*",return_mrr_number($companyNo, '', 'RRF', date("Y",time()), 5, "select recv_number_prefix,recv_number_prefix_num from pro_fab_subprocess where company_id= $companyNo and entry_form='115' and $year_cond=".date('Y',time())." order by id desc ", "recv_number_prefix", "recv_number_prefix_num"));

		// prepare DML array
		$field_array = "id, recv_number_prefix, recv_number_prefix_num, recv_number,receive_date, company_id, service_source, service_company, load_unload_id, result, batch_id, batch_no, entry_form, booking_no, inserted_by, insert_date";
		$data_array = "(".$id.",'".$new_recv_number[1]."','".$new_recv_number[2]."','".$new_recv_number[0]."','" . $receiveDte . "',".$companyNo.",".$knitingSource.",".$knitingCompany."," . 2 ."," . 1 . ",". $batchID . ",'". "$batchNo" . "', 115 ,'".$bookingNo."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$mst_update_id = str_replace("'", "", $id);

		$field_array_dtls = "id, mst_id, entry_page, prod_id, const_composition, gsm,dia_width, batch_qty, roll_no, roll_id, inserted_by, insert_date";
		for($i = 0; $i < $tot_row; $i++)
		{
			$prod_id 			= "productId".$i;
			$txtconscomp 		= "const_comp".$i;
			$gsm 				= "gsm".$i;
			$dia 				= "dia".$i;
			$txtbatchqnty 		= "roll_weight".$i;
			$txtroll 			= "rollNo".$i;
			$roll_id 			= "rollId".$i;
			$Itemprod_id 		= str_replace("'","", $$prod_id);
			if($Itemprod_id != ""){
				if($data_array_dtls!="") $data_array_dtls.=","; 
				$data_array_dtls .= "(".$id_dtls . ",".$mst_update_id . ", 115, " . $Itemprod_id .",'" . $$txtconscomp . "'," . $$gsm . ",'" . $$dia ."',". $$txtbatchqnty . "," . $$txtroll . "," . $$roll_id . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time."')";
			}
			$id_dtls = $id_dtls + 1;
		}
		$rID = $rID2 = true;
		
		if($data_array_dtls != "")
		{
			$rID = sql_insert("pro_fab_subprocess", $field_array, $data_array, 0);
			$rID2 = sql_insert("pro_fab_subprocess_dtls", $field_array_dtls, $data_array_dtls, 0);
		}
		

		if($db_type==0) // MySQL
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "0**".$new_recv_number[0]."**";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_recv_number[0]."**";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);
				echo "0**".$new_recv_number[0]."**";
			}
			else
			{
				oci_rollback($con);
				echo "10**".$new_recv_number[0]."**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here 
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$data_array_update = '';
		$data_array_dtls_insert = '';
		
		$mst_id = return_field_value("id","pro_fab_subprocess","recv_number='$txt_system_no'","id");
		$id_dtls = return_next_id( "id", "pro_fab_subprocess_dtls", 1 );

		$field_array_update = "receive_date, update_by, update_date";
		$data_array_update ="'". $receiveDte . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time."'";
		

		for($i = 0; $i < $tot_row; $i++)
		{			
			$sub_dtls_id 		= "sub_dtls_id".$i;

			if($$sub_dtls_id != ""){
				

			}else{
				$field_array_dtls_insert = "id, mst_id, entry_page, prod_id, const_composition, gsm,dia_width, batch_qty, roll_no, roll_id, inserted_by, insert_date";

				$prod_id 			= "productId".$i;
				$txtconscomp 		= "const_comp".$i;
				$gsm 				= "gsm".$i;
				$dia 				= "dia".$i;
				$txtbatchqnty 		= "roll_weight".$i;
				$txtroll 			= "rollNo".$i;
				$roll_id 			= "rollId".$i;
				$dia_width_type 	= "dia_width_type".$i;
				$Itemprod_id 		= str_replace("'","", $$prod_id);
				if($Itemprod_id != ""){
				if($data_array_dtls_insert!="") $data_array_dtls_insert.=","; 
					$data_array_dtls_insert .= "(".$id_dtls . ",'". $mst_id . "', 115, " . $Itemprod_id .",'" . $$txtconscomp . "'," . $$gsm . ",'" . $$dia ."',". $$txtbatchqnty . "," . $$txtroll . "," . $$roll_id . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time."')";
				}
				$id_dtls = $id_dtls + 1;
			}
			
		}
		//echo $data_array_dtls_insert;
		$rID = $rID2 = true;
		//$rID = sql_update("pro_fab_subprocess",$field_array_update,$data_array_update,"id",$mst_id,1);
		if($data_array_dtls_insert != "")
		{
			$rID2 = sql_insert("pro_fab_subprocess_dtls", $field_array_dtls_insert, $data_array_dtls_insert, 0);
		}

		if($txt_deleted_id != "")
		{
			$delete_roll = execute_query( "delete from pro_fab_subprocess_dtls where id in ($txt_deleted_id)",0);
		}	

		if($db_type==0)
		{
			if($rID2)
			{
				mysql_query("COMMIT");  
				echo "0**".$txt_system_no."**";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$txt_system_no."**";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID2)
			{
				oci_commit($con);
				echo "0**".$txt_system_no."**";
			}
			else
			{
				oci_rollback($con);
				echo "10**".$txt_system_no."**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // delete Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$rID = $rID2 = true;
		// get id by Booking No
		$mst_id = return_field_value("id","pro_fab_subprocess","recv_number='$txt_system_no'","id");
		if($mst_id != ''){
			$rID2 = execute_query( "delete from pro_fab_subprocess_dtls where mst_id in ($mst_id)",0);
			$rID = execute_query("delete from pro_fab_subprocess where id = ".$mst_id." ");
		}
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "0**".$mst_id."**";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$mst_id."**";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);
				echo "0**".$mst_id."**";
			}
			else
			{
				oci_rollback($con);
				echo "10**".$mst_id."**";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="load_scanned_barcode_nos")
{
	$scanned_arr=array();
	$dataArr=sql_select("select barcode_no as BARCODE_NO from pro_roll_details where entry_form=56 and status_active=1 and is_deleted=0");
	foreach($dataArr as $row)
	{
		$scanned_arr[]=$row['BARCODE_NO'];
	}
	$jsbarcode_array= json_encode($scanned_arr);
	echo $jsbarcode_array;
	exit();	
}

if($action=="roll_receive_finish_print")
{
	extract($_REQUEST);
	$data = explode('*',$data);

	$company 		= $data[0];
	$batch_no 		= $data[1];
	$receive_no 	= $data[2];
	$receive_date 	= $data[3];
	$dyeing_source 	= $data[4];
	$dyeing_company = $data[5];

	// prepare array libraries
	$company_name_array = return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$machine_arr = return_library_array( "select id,machine_no from lib_machine_name", "id", "machine_no");
	$buyer_name_array = return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$color_arr = return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");

	// prepare const./composition data
	$composition_arr = array(); $constructtion_arr=array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	// prepare sql to get details by Barcode
	$data_array = sql_select("select a.recv_number, a.receive_date, a.batch_id,a.batch_no, a.load_unload_id, a.result, a.company_id, a.machine_id,a.service_company, 
		a.service_source, a.booking_no, (select g.color_id from pro_batch_create_mst g where a.batch_id = g.id)color_id,
		b.id sub_dtls_id, b.roll_no, b.const_composition, b.gsm, b.dia_width, b.batch_qty, b.prod_id, b.roll_id,
		c.barcode_no, c.mst_id, c.dtls_id, c.po_breakdown_id,c.reject_qnty,
		d.febric_description_id, d.body_part_id, e.job_no_mst, f.buyer_name, e.t_year, e.file_no, e.po_number, f.job_no_prefix_num
		from pro_fab_subprocess a
		left join pro_fab_subprocess_dtls b on a.id = b.mst_id
		left join pro_roll_details c on b.roll_id = c.id
		left join pro_grey_prod_entry_dtls d on c.dtls_id = d.id
		left join wo_po_break_down e on c.po_breakdown_id = e.id
		left join wo_po_details_master f on e.job_no_mst = f.job_no
		where a.company_id = $data[0] and a.entry_form = 115 and a.load_unload_id = 2
		and a.result = 1 and a.recv_number = '$data[2]'
		order by a.recv_number desc");
		?>
		<div>
			<table width="100%" cellspacing="0" align="center" border="0" style="font: 12px Tahoma;">
				<tr>
					<td colspan="6" align="center" style="font-size:x-large"><strong><? echo $company_name_array[$company]; ?></strong></td>
				</tr>
				<tr>
					<td colspan="6" align="center" style="font-size:14px; border-bottom: 1px solid #999;"><strong>Roll Receive by Finish Process</strong></td>
				</tr>
				<tr>
					<td width="100"><strong>System ID </strong></td>
					<td colspan="5">:&nbsp;<? echo $receive_no; ?></td>
				</tr>
				<tr>
					<td width="100"><strong>Company Name </strong></td>
					<td width="200">:&nbsp;<? echo $company_name_array[$company]; ?></td>
					<td width="90"><strong>Dyeing Source </strong></td>
					<td>:&nbsp; <? echo $knitting_source[$dyeing_source]; ?></td>
					<td width="110"><strong>Dyeing Company </strong></td>
					<td>:&nbsp; <?php echo $dyeing_company;?></td>
				</tr>
				<tr>
					<td><strong>Receive Date</strong></td>
					<td colspan="4">:&nbsp;<? echo change_date_format($receive_date); ?></td>
				</tr>
			</table>
			<br>
			<table cellpadding="4" cellspacing="0" width="100%" border="1" style="font: 11px Tahoma; border-collapse: collapse;">
				<thead>
					<th>SL</th>
					<th>Roll No</th>
					<th>Barcode No</th>
					<th>Body Part</th>
					<th>Const./ Composition</th>
					<th>GSM</th>
					<th>Dia</th>
					<th>Color</th>
					<th>Roll Wgt.</th>
					<th>Job No</th>
					<th>Year</th>
					<th>Buyer</th>
					<th>Order No</th>
					<th>File No</th>
					<th>Knitting Company</th>
					<th>M/C No</th>                  
					<th>Booking/ Programm No</th>
					<th>Batch No.</th>
				</thead>
				<?

				$i = 1;
				foreach($data_array as $row)
				{
					$batch = $row[csf("batch_no")];
					$data_array = sql_select("select g.color_id from pro_batch_create_mst g where a.batch_no = g.batch_no)color_id");
					$group_con="LISTAGG(color_id, ',') WITHIN GROUP (ORDER BY mst_id desc) as color_id";
					$mst_id= return_field_value("$group_con","pro_batch_create_mst","batch_no=$barcode_no","mst_id");
					$color = '';
					$color_ids = explode(",",$row[csf('color_id')]);
					foreach($color_ids as $color_id)
					{
						if($color_id>0) $color .= $color_arr[$color_id].",";
					}
					$color = chop($color,',');

					if($row[csf("service_source")] == 1)
					{
						$knit_company = $company_name_array[$row[csf("service_company")]];
					}
					else if($row[csf("service_source")] == 3)
					{
						$knit_company = $supplier_arr[$row[csf("service_company")]];
					}
					$prodQnty = number_format(($row[csf("qnty")] + $row[csf("reject_qnty")]),2,'.','');		

					?>
					<tr align="center" valign="middle">
						<td><? echo $i; ?></td>
						<td><? echo $row[csf("roll_no")]; ?></td>
						<td><? echo $row[csf("barcode_no")]; ?></td>
						<td><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
						<td align="left"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
						<td><? echo $row[csf("gsm")]; ?></td>
						<td><? echo $row[csf("dia_width")]; ?></td>
						<td align="right"><?php echo $color;?></td>
						<td align="right"><? echo $row[csf("batch_qty")]; ?></td>
						<td><? echo $row[csf("job_no_prefix_num")]; ?></td>
						<td align="center"><? echo $row[csf("t_year")]; ?></td>
						<td><? echo $buyer_name_array[$row[csf("buyer_name")]]; ?></td>
						<td><? echo $row[csf("po_number")]; ?></td>
						<td><? echo $row[csf("file_no")]; ?></td>
						<td><? echo $knit_company; ?></td>
						<td><? echo $machine_arr[$row[csf("machine_id")]]; ?></td>
						<td><? echo $row[csf("booking_no")]; ?></td>           
						<td align="left"><? echo $row[csf("batch_no")]; ?></td>
					</tr>	
					<?
					$i ++;
				}
				?>
			</table><br /><br />
			<div>
				<table width="100%" style="font: 12px Tahoma;">
					<tbody>
						<tr>
							<?
							// signature list
							$nameArray = sql_select( "select designation, name, sequence_no from variable_settings_signature where company_id='$company' and report_id= 104 and status_active=1 and is_deleted=0" );
							if(!empty($nameArray)){
								foreach ($nameArray as $key => $value) {
								?>
								<td width="332" valign="top" align="center">
									<? echo ($value['NAME'] != '') ? $value['NAME'] : '';?><br />
									<strong style="text-decoration:overline"><? echo $value['DESIGNATION'];?></strong>
								</td>
							<? 
								}
							} 
							?>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?
	}

	if($action=="grey_delivery_print_machine")
	{
		extract($_REQUEST);
		$data=explode('*',$data);

		$company=$data[0];
		$txt_challan_no=$data[1];
		$update_id=$data[2];
		$kniting_source=$data[4];

		$company_array=array();
		$company_data=sql_select("select id, company_name, company_short_name from lib_company");
		foreach($company_data as $row)
		{
			$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
			$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
		}

		$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
		$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
		$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count");
		$machine_details=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no");
		$brand_details=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name");
		$location_arr=return_library_array( "select id, location_name from lib_location", "id", "location_name");

		$machine_sql=sql_select("select id, machine_no, dia_width from lib_machine_name");
		$machine_details=array();
		foreach($machine_sql as $row)
		{
			$machine_details[$row[csf("id")]]["machine_no"]=$row[csf("machine_no")];
			$machine_details[$row[csf("id")]]["dia_width"]=$row[csf("dia_width")];
		}
	//$machine_details=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no");

		$mstData=sql_select("select company_id, delevery_date, knitting_source, knitting_company from pro_grey_prod_delivery_mst where id=$update_id");

		$job_array=array();
		$job_sql="select a.job_no_prefix_num, a.job_no, b.id, b.po_number, b.file_no, b.grouping as ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$job_sql_result=sql_select($job_sql);
		foreach($job_sql_result as $row)
		{
			$job_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
			$job_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];
			$job_array[$row[csf('id')]]['ref_no']=$row[csf('ref_no')];
		}

		$composition_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
		$data_array=sql_select($sql_deter);
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


		?>
		<div style="width:1550px;">
			<table width="1290" cellspacing="0" align="center" border="0">
				<tr>
					<td align="center" style="font-size:x-large"><strong><? echo $company_array[$company]['name']; ?></strong></td>
				</tr>
				<tr>
					<td align="center" style="font-size:18px"><strong><u>Delivery Challan</u></strong></td>
				</tr>
				<tr>
					<td align="center" style="font-size:16px"><strong><u>Knitting Section</u></strong></td>
				</tr>
			</table> 
			<br>
			<table width="1290" cellspacing="0" align="center" border="0">
				<tr>
					<td style="font-size:16px; font-weight:bold;" width="80">Challan No</td>
					<td width="170">:&nbsp;<? echo $txt_challan_no; ?></td>
					<td style="font-size:16px; font-weight:bold;" width="60">Location</td>
					<td width="170" id="location_td"></td>
					<td width="810" id="barcode_img_id" align="right"></td>
				</tr>
				<tr>
					<td style="font-size:16px; font-weight:bold;">Delivery Date</td>
					<td colspan="4">:&nbsp;<? echo change_date_format($mstData[0][csf('delevery_date')]); ?></td>
				</tr>
			</table>
			<br>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1550" class="rpt_table" >
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="90">Order No</th>
						<th width="60">Buyer <br> Job</th>
						<th width="60">File No <br> Ref No</th>
						<th width="50">System ID</th>
						<th width="65">Prog./ Book. No</th>
						<th width="80">Production Basis</th>
						<th width="70">Production Date</th><!--new-->
						<th width="40">Shift</th><!--new-->
						<th width="70">Knitting Company</th>
						<th width="50">Yarn Count</th>
						<th width="70">Yarn Brand</th>
						<th width="60">Lot No</th>
						<th width="70">Fab Color</th>
						<th width="70">Color Range</th>
						<th width="150">Fabric Type</th>
						<th width="50">Stich</th>
						<th width="50">Fin GSM</th>
						<th width="40">Fab. Dia</th>
						<th width="40" >MC. No</th>
						<th width="50" >MC. dia</th>
						<th width="80">Barcode No</th>
						<th width="40">Roll No</th>
						<th>QC Pass Qty</th>
					</tr>
				</thead>
				<?
				$i=0; $tot_qty=0; $receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");

				if($kniting_source==1)//in-house
				{
					$sql="SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name, c.barcode_no, c.roll_no, c.po_breakdown_id, d.current_delivery, c.booking_no as bwo, c.booking_without_order FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d, lib_machine_name e WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and b.machine_no_id=e.id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 order by e.seq_no";
				}
				else 
				{			
					$sql="SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name, c.barcode_no, c.roll_no, c.po_breakdown_id, d.current_delivery, c.booking_no as bwo, c.booking_without_order FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 order by c.roll_no";
				}
				
				$result=sql_select($sql);
				$loc_arr=array();
				$loc_nm=": ";
				foreach($result as $row)
				{
					if($loc_arr[$row[csf('location_id')]]=="")
					{
						$loc_arr[$row[csf('location_id')]]=$row[csf('location_id')];
						$loc_nm.=$location_arr[$row[csf('location_id')]].', ';
					}

					$knit_company="&nbsp;";
					if($row[csf("knitting_source")]==1)
					{
						$knit_company=$company_array[$row[csf("knitting_company")]]['shortname'];
					}
					else if($row[csf("knitting_source")]==3)
					{
						$knit_company=$supplier_arr[$row[csf("knitting_company")]];
					}
					
					$count='';
					$yarn_count=explode(",",$row[csf('yarn_count')]);
					foreach($yarn_count as $count_id)
					{
						if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
					}
					
					if($row[csf('receive_basis')]==1)
					{
						$booking_no=explode("-",$row[csf('booking_no')]);	
						$prog_book_no=(int)$booking_no[3];
					}
					else $prog_book_no=$row[csf('booking_no')];
					$i++;
					?>
					<tr>
						<td width="30"><? echo $i; ?></td>
						<?
						if($row[csf('receive_basis')]==1)
						{
							?>
							<td width="90" style="word-break:break-all;"><? echo $row[csf('bwo')]; ?></td>
							<td width="60" style="word-break:break-all;"><? echo $buyer_array[$row[csf('buyer_id')]]; ?></td>
							<td width="60" style="word-break:break-all;"><? echo "F:<br>R:"; ?></td>
							<?	
						}
						else
						{
							?>
							<td width="90" style="word-break:break-all;"><? echo $job_array[$row[csf('po_breakdown_id')]]['po']; ?></td>
							<td width="60" style="word-break:break-all;"><? echo $buyer_array[$row[csf('buyer_id')]]."<br>".$job_array[$row[csf('po_breakdown_id')]]['job']; ?></td>
							<td width="60" style="word-break:break-all;"><? echo "F:".$job_array[$row[csf('po_breakdown_id')]]['file_no']."<br>R:".$job_array[$row[csf('po_breakdown_id')]]['ref_no']; ?></td>
							<?
						}
						?>
						<td width="50"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
						<td width="65" style="word-break:break-all;"><? echo $prog_book_no; ?></td>
						<td width="80" style="word-break:break-all;"><? echo $receive_basis[$row[csf('receive_basis')]]; ?></td>
						<td width="70" style="word-break:break-all;"><? echo change_date_format($row[csf("receive_date")]); ?></td>
						<td width="40" style="word-break:break-all;"><? echo $shift_name[$row[csf("shift_name")]]; ?></td>
						<td width="70" style="word-break:break-all;"><? echo $knit_company; ?></td>
						<td width="50" style="word-break:break-all;"><? echo $count; ?></td>
						<td width="70" style="word-break:break-all;"><? echo $brand_details[$row[csf("brand_id")]]; ?></td>
						<td width="60" style="word-break:break-all;"><? echo $row[csf('yarn_lot')]; ?></td>
						<td width="70" style="word-break:break-all;">
							<? 
						//echo $color_arr[$row[csf("color_id")]]; 
							$color_id_arr=array_unique(explode(",",$row[csf("color_id")]));
							$all_color_name="";
							foreach($color_id_arr as $c_id)
							{
								$all_color_name.=$color_arr[$c_id].",";
							}
							$all_color_name=chop($all_color_name,",");
							echo $all_color_name;
							?>
						</td>
						<td width="70" style="word-break:break-all;"><? echo $color_range[$row[csf("color_range_id")]]; ?></td>
						<td width="150" style="word-break:break-all;"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
						<td width="50" style="word-break:break-all;" align="center"><? echo $row[csf('stitch_length')]; ?></td>
						<td width="50" style="word-break:break-all;" align="center"><? echo $row[csf('gsm')]; ?></td>
						<td width="40" style="word-break:break-all;" align="center"><? echo $row[csf('width')]; ?></td>
						<td width="40" style="word-break:break-all;" align="center"><? echo $machine_details[$row[csf('machine_no_id')]]["machine_no"]; ?></td>
						<td width="50" style="word-break:break-all;" align="center"><? echo $machine_details[$row[csf('machine_no_id')]]["dia_width"]; ?></td>
						<td width="80" align="center"><? echo $row[csf('barcode_no')]; ?></td>
						<td width="40" align="center"><? echo $row[csf('roll_no')]; ?></td>
						<td align="right"><? echo number_format($row[csf('current_delivery')],2); ?></td>
					</tr>
					<?
					$tot_qty+=$row[csf('current_delivery')];
				}

				$loc_nm=rtrim($loc_nm,', ');
				?>
				<tr> 
					<td align="right" colspan="22"><strong>Total</strong></td>
					<td align="right"><? echo $i; ?></td>
					<td align="right"><? echo number_format($tot_qty,2,'.',''); ?></td>
				</tr>
				<tr>
					<td colspan="2" align="left"><b>Remarks:</b></td>
					<td colspan="22">&nbsp;</td>
				</tr>
			</table>
		</div>
		<? echo signature_table(44, $company, "1550px"); ?>
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		<script>
			function generateBarcode( valuess )
			{
			var value = valuess;//$("#barcodeValue").val();
		  	//alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();

			var settings = {
				output:renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 40,
				moduleSize:5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {code:value, rect: false};
			
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
		generateBarcode('<? echo $txt_challan_no; ?>');
		document.getElementById('location_td').innerHTML='<? echo $loc_nm; ?>';
	</script>
	<?
	exit();
}

if($action=="grey_delivery_print_fabric_label")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	
	$company=$data[0];
	$txt_challan_no=$data[1];
	$update_id=$data[2];
	$kniting_source=$data[4];

	$company_array=array();
	$company_data=sql_select("select id, company_name, company_short_name from lib_company");
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
	}
	
	
	$machine_details=array();
	$machine_sql=sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach($machine_sql as $row)
	{
		$machine_details[$row[csf("id")]]["machine_no"]=$row[csf("machine_no")];
		$machine_details[$row[csf("id")]]["dia_width"]=$row[csf("dia_width")];
		$machine_details[$row[csf("id")]]["gauge"]=$row[csf("gauge")];
	}

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$machine_details_arr=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no");
	$brand_details=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name");
	$location_arr=return_library_array( "select id, location_name from lib_location", "id", "location_name");
	
	$mstData=sql_select("select company_id, delevery_date, knitting_source, knitting_company from pro_grey_prod_delivery_mst where id=$update_id");
	
	$job_array=array();
	$job_sql="select a.job_no_prefix_num, a.job_no, b.id, b.po_number, b.file_no, b.grouping as ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];
		$job_array[$row[csf('id')]]['ref_no']=$row[csf('ref_no')];
	}
	
	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
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
	
	
	?>
	<div style="width:1500px;">
		<table width="1290" cellspacing="0" align="center" border="0">
			<tr>
				<td align="center" style="font-size:x-large"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr>
				<td align="center" style="font-size:18px"><strong><u>Delivery Challan</u></strong></td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px"><strong><u>Knitting Section</u></strong></td>
			</tr>
		</table> 
		<br>
		<table width="1290" cellspacing="0" align="center" border="0">
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="80">Challan No</td>
				<td width="170">:&nbsp;<? echo $txt_challan_no; ?></td>
				<td style="font-size:16px; font-weight:bold;" width="60">Location</td>
				<td width="170" id="location_td"></td>
				<td width="810" id="barcode_img_id" align="right"></td>
			</tr>
			<tr>
				<td style="font-size:16px; font-weight:bold;">Delivery Date</td>
				<td colspan="4">:&nbsp;<? echo change_date_format($mstData[0][csf('delevery_date')]); ?></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1500" class="rpt_table" >
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="90">Order No</th>
					<th width="60">Buyer <br> Job</th>
					<th width="60">File No <br> Ref No</th>
					<th width="50">System ID</th>
					<th width="65">Prog./ Book. No</th>
					<th width="80">Production Basis</th>
					<th width="90">Production Date</th><!--new-->
					<th width="40">Shift</th><!--new-->
					<th width="70">Knitting Company</th>
					<th width="80">Yarn Count</th>
					<th width="70">Yarn Brand</th>
					<th width="80">Lot No</th>
					<th width="90">Fab Color</th>
					<th width="70">Color Range</th>
					<th width="150">Fabric Type</th>
					<th width="50">Stich</th>
					<th width="50">Fin GSM</th>
					<th width="40">Fab. Dia</th>
					<th width="40" >Machine No</th>
					<th width="40" >Mac. Dia</th>
					<th width="40">No Of Roll</th>
					<th>QC Pass Qty</th>
				</tr>
			</thead>
			<?
			$i=0; $tot_qty=0; $receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");

				if($kniting_source==1)//in-house
				{
					$sql="SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,count(c.barcode_no) as num_of_roll,c.po_breakdown_id, sum(d.current_delivery) as current_delivery ,c.booking_no as bwo, c.booking_without_order,e.seq_no FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d, lib_machine_name e WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and b.machine_no_id=e.id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name, c.po_breakdown_id , c.booking_no, c.booking_without_order,e.seq_no order by e.seq_no";
				}
				else 
					{	$sql="SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,count(c.barcode_no) as num_of_roll,c.po_breakdown_id, sum(d.current_delivery) as current_delivery ,c.booking_no as bwo, c.booking_without_order,e.seq_no FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d, lib_machine_name e WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and b.machine_no_id=e.id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name, c.po_breakdown_id , c.booking_no, c.booking_without_order,e.seq_no order by e.seq_no";
			}

			$result=sql_select($sql);
			$loc_arr=array();
			$loc_nm=": ";
			foreach($result as $row)
			{
				if($loc_arr[$row[csf('location_id')]]=="")
				{
					$loc_arr[$row[csf('location_id')]]=$row[csf('location_id')];
					$loc_nm.=$location_arr[$row[csf('location_id')]].', ';
				}

				$knit_company="&nbsp;";
				if($row[csf("knitting_source")]==1)
				{
					$knit_company=$company_array[$row[csf("knitting_company")]]['shortname'];
				}
				else if($row[csf("knitting_source")]==3)
				{
					$knit_company=$supplier_arr[$row[csf("knitting_company")]];
				}

				$count='';
				$yarn_count=explode(",",$row[csf('yarn_count')]);
				foreach($yarn_count as $count_id)
				{
					if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
				}

				if($row[csf('receive_basis')]==1)
				{
					$booking_no=explode("-",$row[csf('booking_no')]);	
					$prog_book_no=(int)$booking_no[3];
				}
				else $prog_book_no=$row[csf('booking_no')];
				$i++;
				?>
				<tr>
					<td width="30"><? echo $i; ?></td>
					<?
					if($row[csf('receive_basis')]==1)
					{
						?>
						<td width="90" style="word-break:break-all;"><? echo $row[csf('bwo')]; ?></td>
						<td width="60" style="word-break:break-all;"><? echo $buyer_array[$row[csf('buyer_id')]]; ?></td>
						<td width="60" style="word-break:break-all;"><? echo "F:<br>R:"; ?></td>
						<?	
					}
					else
					{
						?>
						<td width="90" style="word-break:break-all;"><? echo $job_array[$row[csf('po_breakdown_id')]]['po']; ?></td>
						<td width="60" style="word-break:break-all;"><? echo $buyer_array[$row[csf('buyer_id')]]."<br>".$job_array[$row[csf('po_breakdown_id')]]['job']; ?></td>
						<td width="60" style="word-break:break-all;"><? echo "F:".$job_array[$row[csf('po_breakdown_id')]]['file_no']."<br>R:".$job_array[$row[csf('po_breakdown_id')]]['ref_no']; ?></td>
						<?
					}
					?>
					<td width="50"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
					<td width="65" style="word-break:break-all;"><? echo $prog_book_no; ?></td>
					<td width="80" style="word-break:break-all;"><? echo $receive_basis[$row[csf('receive_basis')]]; ?></td>
					<td width="70" style="word-break:break-all;"><? echo change_date_format($row[csf("receive_date")]); ?></td>
					<td width="40" style="word-break:break-all;"><? echo $shift_name[$row[csf("shift_name")]]; ?></td>
					<td width="70" style="word-break:break-all;"><? echo $knit_company; ?></td>
					<td width="50" style="word-break:break-all;"><? echo $count; ?></td>
					<td width="70" style="word-break:break-all;"><? echo $brand_details[$row[csf("brand_id")]]; ?></td>
					<td width="60" style="word-break:break-all;"><? echo $row[csf('yarn_lot')]; ?></td>
					<td width="70" style="word-break:break-all;">
						<? 
						//echo $color_arr[$row[csf("color_id")]]; 
						$color_id_arr=array_unique(explode(",",$row[csf("color_id")]));
						$all_color_name="";
						foreach($color_id_arr as $c_id)
						{
							$all_color_name.=$color_arr[$c_id].",";
						}
						$all_color_name=chop($all_color_name,",");
						echo $all_color_name;
						?>
					</td>
					<td width="70" style="word-break:break-all;"><? echo $color_range[$row[csf("color_range_id")]]; ?></td>
					<td width="150" style="word-break:break-all;"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
					<td width="50" style="word-break:break-all;" align="center"><? echo $row[csf('stitch_length')]; ?></td>
					<td width="50" style="word-break:break-all;" align="center"><? echo $row[csf('gsm')]; ?></td>
					<td width="40" style="word-break:break-all;" align="center"><? echo $row[csf('width')]; ?></td>
					<td width="40" style="word-break:break-all;" align="center"><? echo $machine_details_arr[$row[csf('machine_no_id')]]; ?></td>
					<td width="40"  align="center"><? echo $machine_details[$row[csf('machine_no_id')]]["dia_width"]; ?></td>
					<td width="40" align="center"><? echo $row[csf('num_of_roll')]; ?></td>
					<td align="right"><? echo number_format($row[csf('current_delivery')],2); ?></td>
				</tr>
				<?
				$tot_qty+=$row[csf('current_delivery')];
			}

			$loc_nm=rtrim($loc_nm,', ');
			?>
			<tr> 
				<td align="right" colspan="21"><strong>Total</strong></td>
				<td align="center"><? echo $i; ?></td>
				<td align="right"><? echo number_format($tot_qty,2,'.',''); ?></td>
			</tr>
			<tr>
				<td colspan="2" align="left"><b>Remarks:</b></td>
				<td colspan="21">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2" align="left"><b>No Of Roll:</b></td>
				<td colspan="21"><p style="word-wrap:break-word; width:1350px;">

					<?
					if($db_type==0)
					{
						$sql=sql_select("SELECT group_concat(c.roll_no) as roll_no  FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d, lib_machine_name e WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and b.machine_no_id=e.id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 order by e.seq_no");
					}
					else
					{
						$sql=sql_select("SELECT listagg((cast(c.roll_no as varchar2(100))),',')within group (order by roll_no) as roll_no  FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d, lib_machine_name e WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and b.machine_no_id=e.id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 order by e.seq_no");
					}
					echo $sql[0][csf('roll_no')];


					?>
				</p></td>
			</tr>
		</table>
	</div>
	<? echo signature_table(44, $company, "1500px"); ?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
		  	//alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();

			var settings = {
				output:renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 40,
				moduleSize:5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {code:value, rect: false};
			
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
		generateBarcode('<? echo $txt_challan_no; ?>');
		document.getElementById('location_td').innerHTML='<? echo $loc_nm; ?>';
	</script>
	<?
	exit();
}

?>