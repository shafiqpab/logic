<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$floor_arr=return_library_array( "select id,floor_name from  lib_prod_floor",'id','floor_name');

$ltb_btb=array(1=>'BTB',2=>'LTB');

//--------------------------------------------------------------------------------------------------------------------

if($action=="check_color_id")
	{	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
extract($_REQUEST);
?>
<script type="text/javascript">
	function js_set_value(id)
      { //alert(id);
      	document.getElementById('selected_id').value=id;
      	parent.emailwindow.hide();
      }
  </script>
  <input type="hidden" id="selected_id" name="selected_id" /> 
  <?
  $sql="select id, color_name from lib_color where is_deleted=0 and status_active=1 order by id";
  $arr=array(1=>$color_library);
  echo  create_list_view("list_view", "ID,Color Name", "50,200","300","300",0, $sql, "js_set_value", "id,color_name", "", 1, "0,0", $arr , "id,color_name", "",'setFilterGrid("list_view",-1);','0') ;
  exit();	
}

if ($action=="load_drop_down_knitting_com") {
	//$data = explode("**", $data);
	if ($data[0] == 1) {
		echo create_drop_down("cbo_party_id", 70, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select --", "", "", "");
	} else if ($data[0] == 3) {
		echo create_drop_down("cbo_party_id", 70, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "");
	} else {
		echo create_drop_down("cbo_party_id", 70, $blank_array, "", 1, "-- Select --", 0, "");
	}
	exit();
}

if($action=="batchnumbershow")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script type="text/javascript">
		function js_set_value(id)
		{ 
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" id="selected_id" name="selected_id" /> 
	<? if($db_type==0) $field_grpby=" GROUP BY batch_no"; 
	else if($db_type==2) $field_grpby="GROUP BY batch_no,id,batch_no,batch_for,booking_no,color_id,batch_weight";
	$batch_type = str_replace("'","",$batch_type);
	if ($batch_type==0 || $batch_type==1)
	{
		$sql="select id,batch_no,batch_for,booking_no,color_id,batch_weight from pro_batch_create_mst where company_id=$company_name and entry_form in(0) and is_deleted = 0 $field_grpby ";	
	}
	if ($batch_type==0 || $batch_type==2)
	{
		
		$sql="select id,batch_no,batch_for,booking_no,color_id,batch_weight from pro_batch_create_mst where company_id=$company_name and entry_form in(36) and is_deleted = 0 $field_grpby ";	
	}
	$arr=array(1=>$color_library);
	echo  create_list_view("list_view", "Batch no,Color,Booking no, Batch for,Batch weight ", "100,100,100,100,170","620","350",0, $sql, "js_set_value", "id,batch_no", "", 1, "0,color_id,0,0,0", $arr , "batch_no,color_id,booking_no,batch_for,batch_weight", "employee_info_controller",'setFilterGrid("list_view",-1);','0') ;
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $batch_type;?>', 'create_batch_no_search_list_view', 'search_div', 'batch_progress_report_controller', 'setFilterGrid(\'tbl_list\',-1)');" style="width:100px;" />
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

	$batch_type=$data[4];
	
	$batch_no=$data[1];
	$search_string="%".trim($data[3])."%";

	if ($batch_no=="") $batch_no_cond=""; else $batch_no_cond=" and a.batch_no in ('$batch_no') "; 

	if ($batch_type==1)
	{
		$entry_form=" and a.entry_form in(0)";	
	}
	else if ( $batch_type==2)
	{
		$entry_form="and a.entry_form in(36)";	
	}
	else if ($batch_type==0 )
	{
		$entry_form="and a.entry_form in(0,36)";	
	}
	
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

	
	$sql="select a.id,a.batch_no,a.batch_for,a.booking_no,a.color_id,a.batch_weight from pro_batch_create_mst a where a.company_id=$company_id and a.is_deleted=0 and a.status_active=1 $date_cond $batch_no_cond $entry_form";	
	$arr=array(1=>$color_library,3=>$batch_for);
	echo  create_list_view("tbl_list", "Batch no,Color,Booking no, Batch for,Batch weight ", "150,100,150,100,70","700","350",0, $sql, "js_set_value", "id,batch_no", "", 1, "0,color_id,0,batch_for,0", $arr , "batch_no,color_id,booking_no,batch_for,batch_weight", "",'','0','',1) ;
	exit();
}

if ($action=="load_drop_down_floor")
{
	$ex_data=explode('_',$data);
	if($ex_data[1]!=0) $location_cond=" and b.location_id='$ex_data[1]'"; else $location_cond="";
	
	echo create_drop_down( "cbo_floor_id", 100, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=2 and b.company_id='$ex_data[0]' and b.status_active=1 and b.is_deleted=0  $location_cond group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", 0, "","" );
	exit();	 
}

if($action=="machine_no_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$im_data=explode('_',$data);
	//print_r ($im_data);
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
$sql = "select a.id, a.machine_no, a.brand, a.origin, a.machine_group, b.floor_name  from lib_machine_name a, lib_prod_floor b where a.floor_id=b.id and a.category_id=2 and a.company_id=$im_data[0] and a.status_active=1 and a.is_deleted=0 and a.is_locked=0 order by a.machine_no, b.floor_name ";
	//echo  $sql;

echo create_list_view("tbl_list_search", "Machine Name,Machine Group,Floor Name", "200,110,110","450","350",0, $sql , "js_set_value", "id,machine_no", "", 1, "0,0,0", $arr , "machine_no,machine_group,floor_name", "",'setFilterGrid(\'tbl_list_search\',-1);','0,0,0','',1) ;

exit(); 
}
if($action=="load_drop_down_buyer")
{ 
	//echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	//extract($_REQUEST);
	$data=explode('_',$data);
	$company=$data[0];
	$report_type=$data[1];
	//echo $report_type;
	if($report_type==1 || $report_type==3)
	{
		//echo "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name";
		echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
	}
	else if($report_type==2)
	{
		echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
	}
	else if($report_type==0)
	{
		//echo  "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,2,3,21,90))  order by buyer_name";
		echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,2,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
	}
	else
	{
		echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"","","","");	
	}
	exit();
}//cbo_buyer_name_td


if($action=="jobnumbershow")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script type="text/javascript">
		function js_set_value(id)
		{
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" id="selected_id" name="selected_id" /> 
	<?
	$year_job = str_replace("'","",$year);
	$batch_type = str_replace("'","",$batch_type);
	if($db_type==0)
	{
		$year_field_by="and YEAR(a.insert_date)"; 
		$year_field="SUBSTRING_INDEX(a.insert_date, '-', 1) as year"; 
		$field_grpby="GROUP BY a.job_no order by b.id desc"; 
	}
	else if($db_type==2)
	{
		$year_field_by=" and to_char(a.insert_date,'YYYY')";
		$year_field="to_char(a.insert_date,'YYYY') as year";	
		$field_grpby=" GROUP BY a.job_no,a.id,a.buyer_name,a.style_ref_no,a.gmts_item_id,b.po_number,a.job_no_prefix_num,a.insert_date,b.id order by b.id,a.job_no_prefix_num  desc ";
	}

	if(trim($year)!=0) $year_cond=" $year_field_by=$year_job"; else $year_cond="";
//echo $year_job;
//$cbo_buyer_name=($cbo_buyer_name==0)?"%%" : "%$cbo_buyer_name%";
	if(trim($cbo_buyer_name)==0) $buyer_name_cond=""; else $buyer_name_cond=" and a.buyer_name=$cbo_buyer_name";
	if(trim($cbo_buyer_name)==0) $sub_buyer_name_cond=""; else $sub_buyer_name_cond=" and a.party_id=$cbo_buyer_name";
	if ($batch_type==0 || $batch_type==1)
	{
		$sql="select a.id,a.buyer_name,a.style_ref_no,a.gmts_item_id,b.po_number,a.job_no_prefix_num as job_prefix,$year_field, a.job_no from wo_po_details_master a,wo_po_break_down b where b.job_no_mst=a.job_no and a.company_name=$company_id  $buyer_name_cond $year_cond and a.is_deleted = 0 $field_grpby";
	}
	else
	{
		$sql="select a.id,a.party_id as buyer_name,c.item_id as gmts_item_id,b.cust_style_ref as style_ref_no,b.order_no as po_number,a.job_no_prefix_num as job_prefix,$year_field, a.subcon_job as job_no from  subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where b.job_no_mst=a.subcon_job and a.id=c.mst_id and b.id=c.order_id and a.company_id=$company_id  $sub_buyer_name_cond $year_cond and a.is_deleted = 0 group by a.id,a.subcon_job,a.party_id,b.cust_style_ref,b.order_no ,a.job_no_prefix_num,a.insert_date,c.item_id";

	}
//echo $sql;
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	?>
	<table width="580" border="1" rules="all" class="rpt_table">
		<thead>
			<tr><th colspan="7"><? if($batch_type==0 || $batch_type==1)
			{ echo "Self Batch Order";} else if($batch_type==2) { echo "SubCon Batch Order";}?>  </th></tr>

			<tr>
				<th width="35">SL</th>
				<th width="100">Po Number</th>
				<th width="100">Job no</th>
				<th width="50">Year</th>
				<th width="80">Buyer</th>
				<th width="100">Style</th>
				<th>Item Name</th>
			</tr>
		</thead>
	</table>
	<div style="max-height:300px; overflow-y:scroll; width:600px;">
		<table id="table_body2" width="580" border="1" rules="all" class="rpt_table">
			<? $rows=sql_select($sql);
			$i=1;
			foreach($rows as $data)
				{  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $data[csf('job_no')]; ?>')" style="cursor:pointer;">
				<td width="35"><? echo $i; ?></td>
				<td width="100"><p><? echo $data[csf('po_number')]; ?></p></td>
				<td width="100"><p><? echo $data[csf('job_no')]; ?></p></td>
				<td width="50"><p><? echo $data[csf('year')]; ?></p></td>
				<td width="80"><p><? echo $buyer[$data[csf('buyer_name')]]; ?></p></td>
				<td width="100"><p><? echo $data[csf('style_ref_no')]; ?></p></td>
				<td><p><? 
				$itemid=explode(",",$data[csf('gmts_item_id')]);
				foreach($itemid as $index=>$id){
					echo ($itemid[$index]==end($itemid))? $garments_item[$id] : $garments_item[$id].', ';
				}
				?></p></td>
			</tr>
			<? $i++; } ?>
		</table>
	</div>
	<script> setFilterGrid("table_body2",-1); </script>
	<?
	exit();
}

if ($action == "FSO_No_popup") 
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_name; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_year').value+'**'+document.getElementById('cbo_within_group').value+'**'+document.getElementById('txt_fso_no').value+'**'+document.getElementById('txt_booking_no').value+'**'+'<? echo $hidden_fso_id;?>', 'create_fso_no_search_list_view', 'search_div', 'dyeing_production_sales_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								?>
							</td>   
							<td align="center">	
								<?
								$search_by_arr=array(1=>"With Order",2=>"Without Order");
								$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
								echo create_drop_down( "cbo_booking_type", 100, $search_by_arr,"",0, "--Select--", "3",$dd,0 );
								?>
							</td>               
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+document.getElementById('cbo_booking_type').value, 'create_booking_no_search_list_view', 'search_div', 'dyeing_production_sales_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	}
	else if($search_by==1)
	{
		$search_field="a.job_no_prefix_num";
	}
	else $search_field="b.booking_no";
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
	if($booking_type==1)
	{
		$sql= "select a.job_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.booking_no,c.id as booking_id  from wo_po_details_master a,wo_booking_dtls b ,wo_booking_mst c where a.job_no=b.job_no and b.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.booking_type in(1,4,3) and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond $month_cond group by a.job_no,b.booking_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,c.id  order by a.job_no desc";
	}
	else
	{
		$sql= "select a.company_id as company_name, a.buyer_id as buyer_name, b.style_des as style_ref_no,a.booking_no,a.id as booking_id  from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.booking_type=4 and a.company_id=$company_id and $search_field like '$search_string' $buyer_id_cond2 $year_cond $month_cond group by a.booking_no,a.company_id, a.buyer_id, b.style_des,a.id  order by a.booking_no desc";
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

if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if($db_type==0) $year_field_by="and YEAR(a.insert_date)"; 
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	if($db_type==0) $field_concat="concat(machine_no,'-',brand) as machine_name"; 
	else if($db_type==2) $field_concat="machine_no || '-' || brand as machine_name";
	$company = str_replace("'","",$cbo_company_name);
	$working_company = str_replace("'","",$cbo_working_company_name);
	$buyer = str_replace("'","",$cbo_buyer_name);
	$cbo_prod_type = str_replace("'","",$cbo_prod_type);
	$cbo_party_id = str_replace("'","",$cbo_party_id);

	$date_search_type = str_replace("'","",$search_type);
	$job_number = str_replace("'","",$job_number);
	$job_number_id = str_replace("'","",$job_number_show);
	$batch_no = str_replace("'","",$batch_number_show);

	$machine=str_replace("'","",$txt_machine_id);
	$floor_name=str_replace("'","",$cbo_floor_id);

	$batch_number_hidden = str_replace("'","",$batch_number);
	$ext_num = str_replace("'","",$txt_ext_no);
	$hidden_ext = str_replace("'","",$hidden_ext_no);

	$fso_no = trim(str_replace("'","",$fso_no));
	$hidden_fso_no = str_replace("'","",$hidden_fso_no);
	$hidden_booking_no = str_replace("'","",$hidden_booking_no);
	$booking_no = str_replace("'","",$booking_no);

	$cbo_type = str_replace("'","",$cbo_type);
	$cbo_result_name = str_replace("'","",$cbo_result_name);
	$year = str_replace("'","",$cbo_year);
	$shift = str_replace("'","",$cbo_shift_name);

	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	if ($buyer==0) $sub_buyer_cond=""; else $sub_buyer_cond="and d.party_id='".$buyer."' ";
	if ($cbo_prod_type>0 && $cbo_party_id>0) $cbo_prod_type_cond="and f.service_company in($cbo_party_id) "; else $cbo_prod_type_cond="";
	if ($cbo_prod_type>0) $cbo_prod_source_cond="and f.service_source in($cbo_prod_type) "; else $cbo_prod_source_cond="";

	if ($cbo_result_name==0) $result_name_cond=""; else $result_name_cond="  and f.result='".$cbo_result_name."' ";
	if ($shift==0) $shift_name_cond=""; else $shift_name_cond="  and f.shift_name='".$shift."' ";
	if ($machine=="") $machine_cond=""; else $machine_cond =" and f.machine_id in ( $machine ) ";
	if ($floor_name==0 || $floor_name=='')
	{
		$floor_id_cond="";$floor_id_cond2="";
	}
	 else {
		 $floor_id_cond=" and f.floor_id=$floor_name";
		  $floor_id_cond2=" and floor_id=$floor_name";
	 }
	
	$hidden_booking_cond="";
	if($booking_no!="")
	{
		$hidden_booking_cond="and a.booking_no like '%$booking_no%' ";
	}
	//echo $hidden_booking_cond.'='.$booking_no;

	if ($buyer==0) $buyerdata=""; else $buyerdata=" and ((d.buyer_id = $buyer and d.within_group =2 ) or (d.po_buyer = $buyer and d.within_group =1))";


	if ($batch_no=="") $batch_num=""; else $batch_num="  and a.batch_no='".trim($batch_no)."' ";

	if ($batch_no=="") $unload_batch_cond=""; else $unload_batch_cond="  and batch_no='".trim($batch_no)."' ";
	if ($batch_no=="") $unload_batch_cond2=""; else $unload_batch_cond2="  and f.batch_no='".trim($batch_no)."' ";
	//if ($batch_no=="") $$sub_job_cond=""; else $$sub_job_cond="  and f.batch_no='".trim($batch_no)."' ";
	if ($working_company==0) $workingCompany_name_cond2=""; else $workingCompany_name_cond2="  and a.working_company_id='".$working_company."' ";
	if ($company==0) $companyCond=""; else $companyCond="  and a.company_id=$company";
	
	if ($working_company==0) $subcon_working_company_cond=""; else $subcon_working_company_cond="  and a.company_id='".$working_company."' ";
	if ($company==0) $subcon_companyCond=""; else $subcon_companyCond="  and a.company_id=$company"; 

	if(trim($ext_no)!="") $ext_no_search="%".trim($ext_no)."%"; else $ext_no_search="%%";

	$fsodata=($hidden_fso_no)? " and d.id in (".$hidden_fso_no .")" : '';
	if($hidden_fso_no=="")
	{
		$fsodata =($fso_no)? " and d.job_no like '%".str_pad($fso_no,5,'0',STR_PAD_LEFT)."%'" : ''; 
		if($year!=0)
		{
			$fsodata.=($year)? " and d.job_no like '%-".substr( $year, -2)."-%'" : ''; 
		}    
	}

	if($job_number!=""){
		$po_job_cond = " d.po_job_no ";
		$job_ref_arr = explode(",", $job_number);
		foreach ($job_ref_arr as $val) 
		{
			$po_job_cond .=" like '%".$val."%' or ";
		}
		$po_job_cond = " and ".chop($po_job_cond," or");
		
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
		{
			//if ($txt_order!='') $suborder_no="and c.order_no='$txt_order'"; else $suborder_no="";
			if ($job_number_id!='') $sub_job_cond="  and d.subcon_job like '%".$job_number_id."%' "; else $sub_job_cond="";
		}
	
	}

	$yarncount_arr = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');


	if ($ext_num=="") $ext_no=""; else $ext_no="  and a.extention_no=$ext_num ";
	if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
	if($txt_date_from && $txt_date_to)
	{
		if($db_type==0)
		{
			$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');

			$start_dd= strtotime(change_date_format($date_from,"yyyy-mm-dd","-"));
			$end_dd= strtotime(change_date_format($date_from,"yyyy-mm-dd","-"));
			$start_d=date("Y-m-d",$start_dd);
			$end_d=date("Y-m-d",$end_dd);
			$date_from2 = strtotime($start_d);
			$date_to2 = strtotime($end_d);
			$date_start = date("Y-m-d",strtotime("-5 day", $date_from2));
			$date_end = date("Y-m-d",strtotime("+5 day", $date_to2));
			$date_start= change_date_format($date_start,"yyyy-mm-dd","-",1);
			$date_end= change_date_format($date_end,"yyyy-mm-dd","-",1);

			$second_month_ldate=date("Y-m-t",strtotime($date_to));
			$dateFrom= explode("-",$date_from);
			$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
			$prod_last_day=change_date_format($second_month_ldate,'yyyy-mm-dd');
			$prod_date_upto=" and  f.process_end_date BETWEEN '$fromdate' AND '$prod_last_day' ";
			if($date_search_type==1)
			{
				$dates_com=" and  f.process_end_date BETWEEN '$date_from' AND '$date_to' ";
				$dates_com2=" and  f.process_end_date BETWEEN '$date_start' AND '$date_end' ";
			}
			else
			{
				$dates_com=" and f.insert_date between '".$date_from."' and '".$date_to." 23:59:59' ";
				$dates_com2=" and f.insert_date between '".$date_start."' and '".$date_end." 23:59:59' ";
			}
		}
		elseif($db_type==2)
		{
			$date_from=change_date_format($txt_date_from,'','',1);
			$date_to=change_date_format($txt_date_to,'','',1);

			$start_dd= strtotime(change_date_format($date_from,"dd-mm-yyyy","-"));
			$end_dd= strtotime(change_date_format($date_from,"dd-mm-yyyy","-"));
			$start_d=date("Y-m-d",$start_dd);
			$end_d=date("Y-m-d",$end_dd);
			$date_from2 = strtotime($start_d);
			$date_to2 = strtotime($end_d);
			$date_start = date("Y-m-d",strtotime("-5 day", $date_from2));
			$date_end = date("Y-m-d",strtotime("+5 day", $date_to2));
			$date_start= change_date_format($date_start,"dd-mm-yyyy","-",1);
			$date_end= change_date_format($date_end,"dd-mm-yyyy","-",1);

			$dateFrom= explode("-",$date_from);
			$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
			$second_month_ldate=date("Y-M-t",strtotime($date_to));
			$prod_last_day=change_date_format($second_month_ldate,'','',1);
			$prod_date_upto="and  f.process_end_date BETWEEN '$fromdate' AND '$prod_last_day'";
			if($date_search_type==1)
			{
				$dates_com="and  f.process_end_date BETWEEN '$date_from' AND '$date_to'";
				$dates_com2="and  f.process_end_date BETWEEN '$date_start' AND '$date_end'";
			}
			else
			{
				$dates_com=" and f.insert_date between '".$date_from."' and '".$date_to." 11:59:59 PM' ";
				$dates_com2=" and f.insert_date between '".$date_start."' and '".$date_end." 11:59:59 PM' ";
			}
		}
	}

	$group_by=str_replace("'",'',$cbo_group_by);
	if($group_by==1)
	{
		$order_by="order by f.floor_id";
		$order_by2="order by floor_id";
	}
	else if($group_by==2)
	{
		$order_by="order by f.shift_name";
		$order_b2y="order by shift_name";
	}
	else if($group_by==3)
	{
		$order_by="order by f.machine_id";
		$order_by2="order by machine_id";
	}
	else
	{
		$order_by="order by f.process_end_date,f.machine_id";
		$order_by2="order by process_end_date,machine_id";
	}

		$machine_result=sql_select("select id,prod_capacity,$field_concat from  lib_machine_name where status_active=1 and  category_id = 2 order by seq_no ");
		foreach($machine_result as $row)
		{
			$machine_arr[$row[csf('id')]]=$row[csf('machine_name')];
			$machine_capacity_arr[$row[csf('id')]]=$row[csf('prod_capacity')];
			$total_machine_arr[$row[csf('id')]]=$row[csf('id')];
		}
		
	if($cbo_type==1)
	{
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
		{
			 $sql="select a.id,a.company_id,a.batch_no,a.batch_date,a.batch_weight,a.color_id,null as color_range_id, a.booking_no_id, a.extention_no,a.batch_against,a.total_trims_weight, SUM(b.batch_qnty) AS batch_qnty, b.item_description, b.po_id,b.prod_id,b.body_part_id,b.width_dia_type, d.job_no_prefix_num as fso_no, d.job_no  as sales_no, d.po_job_no,d.sales_booking_no, d.style_ref_no, d.within_group, d.buyer_id,d.po_buyer, f.process_end_date, f.process_end_date as production_date, f.end_hours,f.end_minutes,f.machine_id,f.shift_name, f.floor_id, f.remarks,f.process_id from pro_batch_create_mst a,pro_batch_create_dtls b,fabric_sales_order_mst d, pro_fab_subprocess f where  a.id=b.mst_id and f.batch_id=a.id and f.batch_id=b.mst_id and a.is_sales=1 and b.is_sales=1 and b.po_id=d.id $companyCond $workingCompany_name_cond2 $dates_com $po_job_cond $fsodata $batch_num $buyerdata $year_cond  $machine_cond $floor_id_cond $cbo_prod_type_cond and a.entry_form=0  and f.entry_form=35 and f.load_unload_id=1 and a.batch_against in(1,2,3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $batchIds_cond $cbo_prod_type_cond $cbo_prod_source_cond $hidden_booking_cond GROUP BY  a.id,f.shift_name,f.floor_id,a.company_id,a.batch_no,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id, a.booking_no_id, a.extention_no, a.batch_against,b.item_description, b.po_id,b.body_part_id,b.prod_id, b.width_dia_type ,d.po_job_no,d.sales_booking_no, d.job_no, d.style_ref_no, d.job_no_prefix_num, d.within_group, d.buyer_id, d.po_buyer, f.process_end_date, f.end_hours, f.end_minutes, f.machine_id, f.remarks,f.process_id $order_by";
		}
	}
	else if($cbo_type==2)
	{
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
		{
		 $sql = "select f.insert_date, a.batch_no, a.batch_weight, a.id as batch_id, a.color_id, a.color_range_id, a.extention_no, a.total_trims_weight, (b.batch_qnty) as batch_qnty, b.item_description, b.po_id, b.prod_id,b.body_part_id, b.width_dia_type, d.job_no_prefix_num, d.buyer_id, d.po_buyer, f.remarks, f.shift_name, f.production_date as process_end_date, f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id, f.fabric_type, f.result, a.booking_no, d.booking_without_order, d.season, d.job_no  as sales_no,d.style_ref_no , b.barcode_no, f.process_id, f.ltb_btb_id, d.within_group, d.booking_id 
			from pro_batch_create_dtls b, fabric_sales_order_mst d, pro_fab_subprocess f, pro_batch_create_mst a 
			where f.batch_id=a.id 
			$companyCond $workingCompany_name_cond2  $dates_com $po_job_cond $fsodata $buyerdata $batch_num $year_cond $result_name_cond $shift_name_cond $machine_cond $floor_id_cond $cbo_prod_type_cond $cbo_prod_source_cond $hidden_booking_cond

			and a.entry_form=0  and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,11,2,3) and b.po_id=d.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.is_sales=1 and b.is_sales=1
			$order_by";
		}
		
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
		{
			 $sql_subcon="select f.insert_date, a.batch_no, a.batch_weight, a.id as batch_id, a.color_id, a.color_range_id, a.extention_no, a.total_trims_weight, b.batch_qnty AS sub_batch_qnty, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.subcon_job as job_no_prefix_num, d.party_id as buyer_id, d.party_id as po_buyer, 
			 f.remarks, f.shift_name, f.production_date as process_end_date, f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id, f.fabric_type, f.result, 0 as season, c.cust_style_ref as style_ref_no, 0 as barcode_no, f.process_id, f.ltb_btb_id, 0 as within_group, 0 as booking_id 
			
			from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a, pro_fab_subprocess f
			
			where f.batch_id=a.id and f.batch_id=b.mst_id $subcon_working_company_cond $subcon_companyCond and a.entry_form=36 and a.id=b.mst_id and f.entry_form=38 and f.load_unload_id=2 and a.batch_against in(1,11,2,3) and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $dates_com $sub_job_cond $batch_num $fsodata $sub_buyer_cond $suborder_no $result_name_cond $year_cond $shift_name_cond $machine_cond $floor_id_cond $cbo_prod_type_cond $cbo_prod_source_cond $hidden_booking_cond $order_by";
		}
	}	

	//echo $sql_subcon; 

	if($cbo_type==2) 
	{
		/*$sql_booking_type="SELECT booking_no, booking_type, is_short,1 as with_order_status from wo_booking_mst where status_active=1 and is_deleted=0
		union all select booking_no, booking_type, is_short, 2 as with_order_status from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0";
		$sql_booking_type_data=sql_select($sql_booking_type);
		foreach ($sql_booking_type_data as $value) 
		{
			if($value[csf("booking_type")]==1 && $value[csf("is_short")]==2) $booking_type_arr[$value[csf("booking_no")]]="Main";
			else if($value[csf("booking_type")]==1 && $value[csf("is_short")]==1) $booking_type_arr[$value[csf("booking_no")]]="Short";
			else if($value[csf("booking_type")]==4) $booking_type_arr[$value[csf("booking_no")]]="Sample";
		}*/

		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
		{
			//echo $sql;
			$batchdata=sql_select($sql);
			foreach($batchdata as $row)
			{
				$all_batch[$row[csf("batch_id")]]=$row[csf("batch_id")];
				$all_barcode[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
				if($row[csf("booking_id")]!="") $all_booking_id[$row[csf("booking_id")]]=$row[csf("booking_id")];
			}
			$all_batch_ids= implode(",",array_filter(array_unique($all_batch)));

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

			$batchCond = $dyeing_batch_id_cond = "";  $batchCond2 = $all_batch_no_cond2 = ""; 
			$all_batch_arr=explode(",",$all_batch_ids);
			if($db_type==2 && count($all_batch_arr)>999)
			{
				$all_batch_chunk=array_chunk($all_batch_arr,999) ;
				foreach($all_batch_chunk as $chunk_arr)
				{
					$batchCond.=" f.batch_id in(".implode(",",$chunk_arr).") or ";	
					$batchCond2.=" a.batch_id in(".implode(",",$chunk_arr).") or ";	
				}
				$dyeing_batch_id_cond.=" and (".chop($batchCond,'or ').")";			
				$all_batch_no_cond2.=" and (".chop($batchCond2,'or ').")";			
			}
			else
			{ 	
				$dyeing_batch_id_cond=" and f.batch_id in($all_batch_ids)";
				$all_batch_no_cond2=" and a.batch_id in($all_batch_ids)";
			}
			//echo $dyeing_batch_id_cond.'DD';
			//=======================
			//if($all_batch_ids!="") $dyeing_batch_id_cond= "and f.batch_id in($all_batch_ids)"; else $dyeing_batch_id_cond="";

			$sql_prod_ref= sql_select("select a.id,a.batch_id, b.prod_id, b.const_composition, (b.batch_qty) as batch_qty, (b.production_qty) as production_qty
				from  pro_fab_subprocess a, pro_fab_subprocess_dtls b
				where a.id = b.mst_id and a.load_unload_id = 2 and b.load_unload_id=2 and b.entry_page=35 and a.status_active = 1 and b.status_active=1 $all_batch_no_cond2"); // and a.batch_id in ($all_batch_ids)
				//echo "select a.id,a.batch_id, b.prod_id, b.const_composition, (b.batch_qty) as batch_qty, (b.production_qty) as production_qty
			//	from  pro_fab_subprocess a, pro_fab_subprocess_dtls b
				//where a.id = b.mst_id and a.load_unload_id = 2 and b.load_unload_id=2 and b.entry_page=35 and a.status_active = 1 and b.status_active=1 $all_batch_no_cond2";

			foreach ($sql_prod_ref as $val) 
			{
				$batch_product_arr[$val[csf("batch_id")]][$val[csf("prod_id")]] += $val[csf("production_qty")];
				$batch_product_arr2[$val[csf("batch_id")]]["batch_prod_tot"] += $val[csf("production_qty")];
			}
			

			$add_tp_stri_batch_sql=sql_select("select  a.batch_id, a.dyeing_re_process from pro_recipe_entry_mst a where a.entry_form = 60 and a.status_active = 1 and a.is_deleted = 0 $all_batch_no_cond2 group by a.batch_id, a.dyeing_re_process");
			//echo "select  a.batch_id, a.dyeing_re_process from pro_recipe_entry_mst a where a.entry_form = 60 and a.status_active = 1 and a.is_deleted = 0 $all_batch_no_cond2 group by a.batch_id, a.dyeing_re_process";
			foreach ($add_tp_stri_batch_sql as $val) 
			{
				$add_tp_stri_batch_arr[$val[csf("batch_id")]] = $val[csf("dyeing_re_process")];
			}
			unset($add_tp_stri_batch_sql);

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
				$yarn_lot_data=sql_select("select b.barcode_no, a.prod_id,c.
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
			$total_color_wise_batch_qty=$tot_machine_capacity=0;
			foreach($batchdata as $row)
			{
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["insert_date"] = $row[csf("insert_date")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["batch_no"] = $row[csf("batch_no")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["batch_weight"] = $row[csf("batch_weight")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["color_id"] = $row[csf("color_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["color_range_id"] = $row[csf("color_range_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["extention_no"] = $row[csf("extention_no")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["total_trims_weight"] = $row[csf("total_trims_weight")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["batch_qnty"] += $row[csf("batch_qnty")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["item_description"] = $row[csf("item_description")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["po_id"] = $row[csf("po_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["body_part"] = $body_part[$row[csf("body_part_id")]];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["width_dia_type"] = $row[csf("width_dia_type")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["job_no_prefix_num"] = $row[csf("job_no_prefix_num")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["remarks"] = $row[csf("remarks")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["shift_name"] = $row[csf("shift_name")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["within_group"] = $row[csf("within_group")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["po_buyer"] = $row[csf("po_buyer")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["buyer_id"] = $row[csf("buyer_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["process_end_date"] = $row[csf("process_end_date")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["production_date"] = $row[csf("production_date")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["production_qnty"] = $batch_product_arr[$row[csf("batch_id")]][$row[csf("prod_id")]];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["prod_qnty_tot"] = $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["end_hours"] = $row[csf("end_hours")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["floor_id"] = $row[csf("floor_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["hour_unload_meter"] = $row[csf("hour_unload_meter")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["water_flow_meter"] = $row[csf("water_flow_meter")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["end_minutes"] = $row[csf("end_minutes")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["machine_id"] = $row[csf("machine_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["load_unload_id"] = $row[csf("load_unload_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["fabric_type"] = $row[csf("fabric_type")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["result"] = $row[csf("result")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["booking_no"] = $row[csf("booking_no")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["sales_no"] = $row[csf("sales_no")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["booking_without_order"] = $row[csf("booking_without_order")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["season"] = $row[csf("season")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["style_ref_no"] = $row[csf("style_ref_no")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["barcode_no"][] = $row[csf("barcode_no")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["process_id"] = $row[csf("process_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["ltb_btb_id"] = $row[csf("ltb_btb_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["add_tp_strip"] = $add_tp_stri_batch_arr[$row[csf("batch_id")]];
				
				$ttl_batch_prod_tot_arr[$row[csf("batch_id")]]['prod_qty']=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
				$ttl_batch_prod_tot_arr[$row[csf("batch_id")]]['trims']=$row[csf("total_trims_weight")];
				
				$machine_id_arr[$row[csf("batch_id")]]['machine_id']=$row[csf("machine_id")];
				
				$tot_process_end_date_arr[$row[csf('production_date')]]= $row[csf('production_date')];
				if($row[csf('machine_id')] != 0)
				{
					$total_running_machine_arr[$row[csf('machine_id')]]=$row[csf('machine_id')];
					$tot_machine_capacity_arr[$row[csf('batch_id')]]= $machine_capacity_arr[$row[csf('machine_id')]];
				}
				
				//if($row[csf('result')]!= 4 && $row[csf('extention_no')]=="")
				//{
				/*$color_wise_batch_arr[$row[csf("color_range_id")]]["batch_qnty"]= $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"]+$row[csf("total_trims_weight")];
				$color_wise_batch_arr[$row[csf("color_range_id")]]["total_trims_weight"]= $row[csf("total_trims_weight")];
				$total_color_wise_batch_qty+= $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"]+$row[csf("total_trims_weight")];;
				$color_wise_batch_arr[$row[csf("color_range_id")]]["batch_ids"].=$row[csf("batch_id")].',';*/
				//}

				if($batch_product_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]>0)
				{
					$production_date_count[$row[csf("production_date")]] = $row[csf("production_date")];
				}

				//re-process
				if($row[csf("extention_no")]>0 && $row[csf("result")]==1)
				{
					if($chkBatch[$row[csf("batch_id")]] =="")
					{
							//$total_reprocess_qty += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
						$total_reprocess_qty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch[$row[csf("batch_id")]] =$row[csf("batch_id")];
					}
				}

				//adding
				if($row[csf("extention_no")]==0 && $row[csf("result")]==1 && $add_tp_stri_batch_arr[$row[csf("batch_id")]]==2)
				{
					if($chkBatch_1[$row[csf("batch_id")]] =="")
					{
						//$total_adding_qnty += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
						$total_adding_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch_1[$row[csf("batch_id")]] =$row[csf("batch_id")];
						//echo 'X,';
						$btb_shade_matched[$row[csf("ltb_btb_id")]]["shade_matched"] ++;
					}
				}

				//rft
				if($row[csf("extention_no")]==0 && $row[csf("result")]==1 && $add_tp_stri_batch_arr[$row[csf("batch_id")]]=="")
				{
					if($chkBatch_2[$row[csf("batch_id")]] =="")
					{
					//echo 'B,';
						//$total_rft_qnty += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
						$total_rft_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch_2[$row[csf("batch_id")]] =$row[csf("batch_id")];
					}
				}

				//if($chkBatch_3[$row[csf("batch_id")]] =="")
				if($chkBatch_3[$row[csf("batch_id")]] =="" && $row[csf("result")]==1)
				{
					//$fabric_type_production_arr[$row[csf("fabric_type")]] += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
					//$fabric_type_production_total +=$batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
					$fabric_type_production_arr[$row[csf("fabric_type")]] += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$fabric_type_production_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$chkBatch_3[$row[csf("batch_id")]] =$row[csf("batch_id")];
				}

				if($row[csf("extention_no")]>0 && $row[csf("result")]==1 && $chkBatch_6[$row[csf("batch_id")]] =="")
				{
					$chkBatch_6[$row[csf("batch_id")]] =$row[csf("batch_id")];
					if($row[csf("within_group")]==1)
					{
						$buyer_wise_summary[$row[csf("po_buyer")]] += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					}
					else{
						$buyer_wise_summary[$row[csf("buyer_id")]] += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					}
					$buyer_wise_summary_batch_total += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];

					$dyeing_process_wise_batch_qnty_arr[$row[csf("process_id")]] +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$dyeing_process_wise_batch_qnty_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
				}

				//shade Matched
				if($row[csf("result")]==1) 
				{
					if($row[csf("within_group")]==1)
					{
						if($booking_type_arr[$row[csf("booking_no")]] == "Main" || $booking_type_arr[$row[csf("booking_no")]] == "Short")
						{
							//$shade_matched_wise_summary[$row[csf("po_buyer")]]["self"]+=$row[csf("batch_qnty")];
							//$shade_matched_buyer_total +=$row[csf("batch_qnty")];
							if($row[csf("extention_no")]<1){
								if($chkBatch_4[$row[csf("batch_id")]] == ""){
									$chkBatch_4[$row[csf("batch_id")]] =$row[csf("batch_id")];

									$shade_matched_wise_summary[$row[csf("po_buyer")]]["self"]+=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
									$shade_matched_buyer_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];

									$shade_matched_wise_summary[$row[csf("po_buyer")]]["trim"]+=$row[csf("total_trims_weight")];
									$shade_matched_buyer_total +=$row[csf("total_trims_weight")];
								}
							}
						}
						else if($booking_type_arr[$row[csf("booking_no")]]=="Sample")
						{
							if($row[csf("booking_without_order")]==1)
							{
								//$shade_matched_wise_summary[$row[csf("po_buyer")]]["smn"]+=$row[csf("batch_qnty")];
								//$shade_matched_buyer_total +=$row[csf("batch_qnty")];
								if($row[csf("extention_no")]<1){
									if($chkBatch_4[$row[csf("batch_id")]] == ""){
										$chkBatch_4[$row[csf("batch_id")]] =$row[csf("batch_id")];

										$shade_matched_wise_summary[$row[csf("po_buyer")]]["smn"]+=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
										$shade_matched_buyer_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];

										$shade_matched_wise_summary[$row[csf("po_buyer")]]["trim"]+=$row[csf("total_trims_weight")];
										$shade_matched_buyer_total +=$row[csf("total_trims_weight")];
									}
								}
							}
							else
							{
								//$shade_matched_wise_summary[$row[csf("po_buyer")]]["sm"]+=$row[csf("batch_qnty")];
								//$shade_matched_buyer_total +=$row[csf("batch_qnty")];
								if($row[csf("extention_no")]<1){
									if($chkBatch_4[$row[csf("batch_id")]] == ""){
										$chkBatch_4[$row[csf("batch_id")]] =$row[csf("batch_id")];

										$shade_matched_wise_summary[$row[csf("po_buyer")]]["sm"]+=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
										$shade_matched_buyer_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];

										$shade_matched_wise_summary[$row[csf("po_buyer")]]["trim"]+=$row[csf("total_trims_weight")];
										$shade_matched_buyer_total +=$row[csf("total_trims_weight")];
									}
								}
							}
						}
					}
					else
					{
						//$shade_matched_wise_summary[$row[csf("buyer_id")]]["self"]+=$row[csf("batch_qnty")];
						//$shade_matched_buyer_total +=$row[csf("batch_qnty")];
						if($row[csf("extention_no")]<1){
							if($chkBatch_4[$row[csf("batch_id")]] == ""){
								$chkBatch_4[$row[csf("batch_id")]] =$row[csf("batch_id")];

								$shade_matched_wise_summary[$row[csf("buyer_id")]]["self"]+=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
								$shade_matched_buyer_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];

								$shade_matched_wise_summary[$row[csf("buyer_id")]]["trim"]+=$row[csf("total_trims_weight")];
								$shade_matched_buyer_total +=$row[csf("total_trims_weight")];
							}
						}
					}
				}
				else
				{
					if($row[csf("within_group")]==1)
					{
						if($booking_type_arr[$row[csf("booking_no")]] == "Main" || $booking_type_arr[$row[csf("booking_no")]] == "Short")
						{
							//$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["self"]+=$row[csf("batch_qnty")];
							//$shade_not_matched_buyer_total +=$row[csf("batch_qnty")];
							if($chkBatch_4[$row[csf("batch_id")]] == ""){
								$chkBatch_4[$row[csf("batch_id")]] =$row[csf("batch_id")];

								$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["self"]+=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
								$shade_not_matched_buyer_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];

								$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["trim"]+=$row[csf("total_trims_weight")];
								$shade_not_matched_buyer_total +=$row[csf("total_trims_weight")];
							}
						}
						else if($booking_type_arr[$row[csf("booking_no")]]=="Sample")
						{
							if($row[csf("booking_without_order")]==1)
							{
								//$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["smn"]+=$row[csf("batch_qnty")];
								//$shade_not_matched_buyer_total +=$row[csf("batch_qnty")];
								if($chkBatch_4[$row[csf("batch_id")]] == ""){
									$chkBatch_4[$row[csf("batch_id")]] =$row[csf("batch_id")];

									$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["smn"]+=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
									$shade_not_matched_buyer_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];

									$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["trim"]+=$row[csf("total_trims_weight")];
									$shade_not_matched_buyer_total +=$row[csf("total_trims_weight")];
								}
							}
							else
							{
								//$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["sm"]+=$row[csf("batch_qnty")];
								//$shade_not_matched_buyer_total +=$row[csf("batch_qnty")];
								if($chkBatch_4[$row[csf("batch_id")]] == ""){
									$chkBatch_4[$row[csf("batch_id")]] =$row[csf("batch_id")];

									$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["sm"]+=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
									$shade_not_matched_buyer_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];

									$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["trim"]+=$row[csf("total_trims_weight")];
									$shade_not_matched_buyer_total +=$row[csf("total_trims_weight")];
								}
							}
						}
					}
					else
					{
						//$shade_not_matched_wise_summary[$row[csf("buyer_id")]]["self"]+=$row[csf("batch_qnty")];
						//$shade_not_matched_buyer_total +=$row[csf("batch_qnty")];
						if($chkBatch_4[$row[csf("batch_id")]] == ""){
							$chkBatch_4[$row[csf("batch_id")]] =$row[csf("batch_id")];

							$shade_not_matched_wise_summary[$row[csf("buyer_id")]]["self"]+=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
							$shade_not_matched_buyer_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];

							$shade_not_matched_wise_summary[$row[csf("buyer_id")]]["trim"]+=$row[csf("total_trims_weight")];
							$shade_not_matched_buyer_total +=$row[csf("total_trims_weight")];
						}

					}
				}

				//$dyeing_process_wise_batch_qnty_arr[$row[csf("process_id")]] +=$row[csf("batch_qnty")];
				//$dyeing_process_wise_batch_qnty_total +=$row[csf("batch_qnty")];

				//$btb_ltb_count=1;
				if($chkBatch_5[$row[csf("batch_id")]] == "")
				{
					$chkBatch_5[$row[csf("batch_id")]] =$row[csf("batch_id")];
					if($row[csf("extention_no")]==0 && $add_tp_stri_batch_arr[$row[csf("batch_id")]]=="" && $row[csf("result")] ==1){
						$btb_shade_matched[$row[csf("ltb_btb_id")]]["shade_matched"] ++;//+=$btb_ltb_count;
						//echo "Z".$row[csf("ltb_btb_id")];
					}
					$btb_shade_matched[$row[csf("ltb_btb_id")]]["total_batch"] ++;
				}
			}
			
			//print_r($tot_process_end_date_arr);
			$batch_row_span_arr =array();$ttl_batch_qty=0;$machine_check_array=array();
			$x=1;
			foreach ($data_array_batch as $batch_id => $batch_data_arr) 
			{
				$batch_row_span = 0;
				foreach ($batch_data_arr as $prod_id => $val) 
				{
					$batch_row_span++;
					//$val["prod_qnty_tot"];
					//$ttl_batch_qty+=$val["prod_qnty_tot"];
				
					
				}
				//echo $val["total_trims_weight"].'FD';
				$color_wise_batch_arr[$val[("color_range_id")]]["summary_batch_qnty"]+=$ttl_batch_prod_tot_arr[$batch_id]['prod_qty'];
				if($ttl_batch_prod_tot_arr[$batch_id]['trims']>0)
				{
				$color_wise_batch_arr[$val[("color_range_id")]]["total_trims_weight"]+=$ttl_batch_prod_tot_arr[$batch_id]['trims'];
				}
				$total_color_wise_batch_qty+= $ttl_batch_prod_tot_arr[$batch_id]['prod_qty']+$ttl_batch_prod_tot_arr[$batch_id]['trims'];
				$color_wise_batch_arr[$val[("color_range_id")]]["batch_ids"].=$batch_id.',';
				
				$ttl_batch_qty+=$ttl_batch_prod_tot_arr[$batch_id]['prod_qty']+$ttl_batch_prod_tot_arr[$batch_id]['trims'];
				$batch_row_span_arr[$batch_id] = $batch_row_span;
				
				$machine_id=$machine_id_arr[$batch_id]['machine_id'];
				if (!in_array($machine_id,$machine_check_array))
					{ $x++;
						
						
						 $machine_check_array[]=$machine_id;
						 $machine_capacity=$tot_machine_capacity_arr[$batch_id];
					}
					else
					{
						 $machine_capacity=0;
					}
					
				$tot_machine_capacity+=$machine_capacity;
			}
			//echo $ttl_batch_qty.'SS';	
			//print_r($color_wise_batch_arr);			
		}
		//echo $tot_machine_capacity.'DD';
		
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2 )
		{
				$subbatchdata=sql_select($sql_subcon); $all_sub_batch=array();
				foreach($subbatchdata as $row) 
				{
					$all_sub_batch[$row[csf("batch_id")]]=$row[csf("batch_id")];
				}
			//print_r($all_sub_batch);
			//$all_sub_batch_id= array_unique($all_sub_batch);
			$all_sub_batch_ids=implode(",",$all_sub_batch);
			//if($all_sub_batch_ids=="") { echo "Data Not Found"; die; }
			//====================

			$batchCond =$sub_dyeing_batch_id_cond= "";  $subbatchCond2 = $all_sub_batch_no_cond2 = ""; 
			$all_sub_batch_arr=explode(",",$all_sub_batch_ids);
			//print_r($all_sub_batch_arr);
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
			//if($all_batch_ids!="") $dyeing_batch_id_cond= "and f.batch_id in($all_batch_ids)"; else $dyeing_batch_id_cond="";
			
			
			 $sub_sql_prod_ref= sql_select("select a.batch_id,b.prod_id,sum(b.batch_qnty) as production_qty
				from  pro_fab_subprocess a,pro_batch_create_dtls b
				where a.batch_id =b.mst_id and a.load_unload_id=2 and a.load_unload_id=2 and a.entry_form=38 and a.status_active=1 and b.status_active=1 $all_sub_batch_no_cond2 
				group by a.id, a.batch_id, b.prod_id"); // and a.batch_id in ($all_batch_ids)
				
				
			foreach ($sub_sql_prod_ref as $val) 
			{
				$batch_product_arr[$val[csf("batch_id")]][$val[csf("prod_id")]] = $val[csf("production_qty")];
				$batch_product_arr2[$val[csf("batch_id")]]["batch_prod_tot"] += $val[csf("production_qty")];
			}
			

			$add_tp_stri_batch_sql=sql_select("select  a.batch_id, a.dyeing_re_process from pro_recipe_entry_mst a where a.entry_form = 60 and a.status_active = 1 and a.is_deleted = 0 $all_batch_no_cond2 group by a.batch_id, a.dyeing_re_process");
			foreach ($add_tp_stri_batch_sql as $val) 
			{
				$add_tp_stri_batch_arr[$val[csf("batch_id")]] = $val[csf("dyeing_re_process")];
			}
			unset($add_tp_stri_batch_sql);

			//$yarn_lot_arr=array();
		//	print_r($subbatchdata);
			$subbatch_data_arr=array();
			$sub_total_color_wise_batch_qty=$sub_tot_machine_capacity=0;
			foreach($subbatchdata as $row)
			{
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["insert_date"] = $row[csf("insert_date")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["batch_no"] = $row[csf("batch_no")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["batch_weight"] = $row[csf("batch_weight")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["color_id"] = $row[csf("color_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["color_range_id"] = $row[csf("color_range_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["extention_no"] = $row[csf("extention_no")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["total_trims_weight"] = $row[csf("total_trims_weight")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["batch_qnty"] += $row[csf("sub_batch_qnty")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["item_description"] = $row[csf("item_description")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["po_id"] = $row[csf("po_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["width_dia_type"] = $row[csf("width_dia_type")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["job_no_prefix_num"] = $row[csf("job_no_prefix_num")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["remarks"] = $row[csf("remarks")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["shift_name"] = $row[csf("shift_name")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["within_group"] = $row[csf("within_group")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["po_buyer"] = $row[csf("po_buyer")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["buyer_id"] = $row[csf("buyer_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["process_end_date"] = $row[csf("process_end_date")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["production_date"] = $row[csf("production_date")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["production_qnty"] = $row[csf("sub_batch_qnty")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["prod_qnty_tot"] = $sub_batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
				//echo $sub_batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"].'jj';
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["end_hours"] = $row[csf("end_hours")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["floor_id"] = $row[csf("floor_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["hour_unload_meter"] = $row[csf("hour_unload_meter")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["water_flow_meter"] = $row[csf("water_flow_meter")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["end_minutes"] = $row[csf("end_minutes")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["machine_id"] = $row[csf("machine_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["load_unload_id"] = $row[csf("load_unload_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["fabric_type"] = $row[csf("fabric_type")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["result"] = $row[csf("result")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["booking_no"] = $row[csf("booking_no")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["booking_without_order"] = $row[csf("booking_without_order")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["season"] = $row[csf("season")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["style_ref_no"] = $row[csf("style_ref_no")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["barcode_no"][] = $row[csf("barcode_no")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["process_id"] = $row[csf("process_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["ltb_btb_id"] = $row[csf("ltb_btb_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["add_tp_strip"] = $add_tp_stri_batch_arr[$row[csf("batch_id")]];
				//echo  $sub_batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"].'BB';
				//echo  $row[csf("sub_batch_qnty")].'X';
				$sub_ttl_batch_prod_tot_arr[$row[csf("batch_id")]]["prod_qty"]= $row[csf("sub_batch_qnty")];
				$sub_ttl_batch_prod_tot_arr[$row[csf("batch_id")]]["trims"]=$row[csf("total_trims_weight")];
				
				
				
				//$sub_tot_day_process_end_date.= $row[csf('process_end_date')].',';
				$tot_process_end_date_arr[$row[csf('production_date')]]= $row[csf('production_date')];
				
				/*if($row[csf('result')]!= 4 && $row[csf('extention_no')]==0)
				{
				$color_wise_batch_arr[$row[csf("color_range_id")]]["batch_qnty"]= $row[csf("sub_batch_qnty")]+$row[csf("total_trims_weight")];
				$color_wise_batch_arr[$row[csf("color_range_id")]]["total_trims_weight"]+= $row[csf("total_trims_weight")];
				$color_wise_batch_arr[$row[csf("color_range_id")]]["batch_ids"].=$row[csf("batch_id")].',';
				$sub_total_color_wise_batch_qty+= $row[csf("sub_batch_qnty")]+$row[csf("total_trims_weight")];
				}
				*/
				if($row[csf('machine_id')] != 0)
				{
					$sub_machine_id_arr[$row[csf("batch_id")]]['machine_id']=$row[csf('machine_id')];
					
					$total_running_machine_arr[$row[csf('machine_id')]]=$row[csf('machine_id')];
					$sub_tot_machine_capacity_arr[$row[csf("batch_id")]]= $machine_capacity_arr[$row[csf('machine_id')]];
				}

				if($batch_product_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]>0)
				{
					$production_date_count[$row[csf("production_date")]] = $row[csf("production_date")];
				}

				//re-process
				if($row[csf("extention_no")]>0 && $row[csf("result")]==1)
				{
					if($chkBatch[$row[csf("batch_id")]] =="")
					{
						//$total_reprocess_qty += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
						$total_reprocess_qty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch[$row[csf("batch_id")]] =$row[csf("batch_id")];
						
					//	echo $row[csf("buyer_id")].'D';
						
						$buyer_wise_summary[$row[csf("buyer_id")]] += $sub_batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						
						$buyer_wise_summary_batch_total += $sub_batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
	
						$dyeing_process_wise_batch_qnty_arr[$row[csf("process_id")]] +=$sub_batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$dyeing_process_wise_batch_qnty_total +=$sub_batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						}
				}

				//adding
				//echo "ASSSS".$add_tp_stri_batch_arr[$row[csf("batch_id")]];
				if($row[csf("extention_no")]==0 && $row[csf("result")]==1 && $add_tp_stri_batch_arr[$row[csf("batch_id")]]==2)
				{
					if($chkBatch_1[$row[csf("batch_id")]] =="")
					{
						//$total_adding_qnty += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
					//echo "V";
						$total_adding_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch_1[$row[csf("batch_id")]] =$row[csf("batch_id")];
						$btb_shade_matched[$row[csf("ltb_btb_id")]]["shade_matched"] ++;
					}
				}

				//rft
				if($row[csf("extention_no")]==0 && $row[csf("result")]==1 && $add_tp_stri_batch_arr[$row[csf("batch_id")]]=="")
				{
					if($chkBatch_2[$row[csf("batch_id")]] =="")
					{
						//$total_rft_qnty += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
						$total_rft_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch_2[$row[csf("batch_id")]] =$row[csf("batch_id")];
					}
				}

				//if($chkBatch_3[$row[csf("batch_id")]] =="")
				if($chkBatch_3[$row[csf("batch_id")]] =="" && $row[csf("result")]==1)
				{
					//$fabric_type_production_arr[$row[csf("fabric_type")]] += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
					//$fabric_type_production_total +=$batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
					$fabric_type_production_arr[$row[csf("fabric_type")]] += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$fabric_type_production_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$chkBatch_3[$row[csf("batch_id")]] =$row[csf("batch_id")];
				}

				if($row[csf("extention_no")]>0 && $row[csf("result")]==1 && $chkBatch_6[$row[csf("batch_id")]] =="")
				{
					$chkBatch_6[$row[csf("batch_id")]] =$row[csf("batch_id")];
					
					$buyer_wise_summary[$row[csf("buyer_id")]] += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					
					$buyer_wise_summary_batch_total += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$dyeing_process_wise_batch_qnty_arr[$row[csf("process_id")]] +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$dyeing_process_wise_batch_qnty_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
				}

				//shade Matched
				if($row[csf("result")]==1) 
				{
					//if($row[csf("extention_no")]<1){
						//if($chkBatch_4[$row[csf("batch_id")]] == ""){
							$chkBatch_4[$row[csf("batch_id")]] =$row[csf("batch_id")];

							$shade_matched_wise_summary[$row[csf("buyer_id")]]["subcon"]+=$row[csf("sub_batch_qnty")];
							$shade_matched_buyer_total +=$row[csf("sub_batch_qnty")];

							$shade_matched_wise_summary[$row[csf("buyer_id")]]["trim"]+=$row[csf("total_trims_weight")];
							$shade_matched_buyer_total +=$row[csf("total_trims_weight")];
						//}
					//}
				}
				else
				{
					//$shade_not_matched_wise_summary[$row[csf("buyer_id")]]["self"]+=$row[csf("batch_qnty")];
					//$shade_not_matched_buyer_total +=$row[csf("batch_qnty")];
					//if($chkBatch_4[$row[csf("batch_id")]] == ""){
						$chkBatch_4[$row[csf("batch_id")]] =$row[csf("batch_id")];

						$shade_not_matched_wise_summary[$row[csf("buyer_id")]]["subcon"]+=$row[csf("sub_batch_qnty")];
						$shade_not_matched_buyer_total +=$row[csf("sub_batch_qnty")];

						$shade_not_matched_wise_summary[$row[csf("buyer_id")]]["trim"]+=$row[csf("total_trims_weight")];
						$shade_not_matched_buyer_total +=$row[csf("total_trims_weight")];
					//}
				}

				//$dyeing_process_wise_batch_qnty_arr[$row[csf("process_id")]] +=$row[csf("batch_qnty")];
				//$dyeing_process_wise_batch_qnty_total +=$row[csf("batch_qnty")];

				//$btb_ltb_count=1;
				if($chkBatch_5[$row[csf("batch_id")]] == "")
				{
					$chkBatch_5[$row[csf("batch_id")]] =$row[csf("batch_id")];
					if($row[csf("extention_no")]==0 && $add_tp_stri_batch_arr[$row[csf("batch_id")]]=="" && $row[csf("result")] ==1){
					//$btb_shade_matched[$row[csf("ltb_btb_id")]]["shade_matched"];
						$btb_shade_matched[$row[csf("ltb_btb_id")]]["shade_matched"]++;//+=$btb_ltb_count;
						//echo "T".$row[csf("ltb_btb_id")];
					}
					$btb_shade_matched[$row[csf("ltb_btb_id")]]["total_batch"] ++;
				}
			}
			//print_r($buyer_wise_summary);
			$x=1;
			$sub_batch_row_span_arr =array();$sub_ttl_batch_prod=0;$sub_machine_check_array=array();
			foreach ($subbatch_data_arr as $batch_id => $batch_data_arr) 
			{
				$sub_batch_row_span = 0;
				foreach ($batch_data_arr as $prod_id=>$row) 
				{
					$sub_batch_row_span++;
					$sub_batch_qnty_arr[$batch_id]+= $row["production_qnty"]; 
					
					//$sub_ttl_batch_prod+=$row["production_qnty"]+$row["total_trims_weight"];//$sub_ttl_batch_prod_tot_arr[$batch_id]["trims"];
					$sub_buyer_batch_qnty_arr[$row["buyer_id"]]+= $row["production_qnty"]+$sub_ttl_batch_prod_tot_arr[$batch_id]["trims"];
				}
			//	$sub_buyer_batch_qnty_arr[$row[csf("batch_id")]]["prod_qty"]= $sub_batch_qnty_arr[$batch_id]+$sub_ttl_batch_prod_tot_arr[$batch_id]["trims"];
				//$sub_ttl_batch_prod_tot_arr[$row[csf("batch_id")]]["trims"]
				//prod_qnty_tot
				//echo $sub_ttl_batch_prod_tot_arr[$batch_id]["prod_qty"].'FD';
				$sub_ttl_batch_prod+=$sub_batch_qnty_arr[$batch_id]+$sub_ttl_batch_prod_tot_arr[$batch_id]["trims"];
				$color_wise_batch_arr[$row[("color_range_id")]]["summary_batch_qnty"]+=$sub_batch_qnty_arr[$batch_id];//$sub_ttl_batch_prod_tot_arr[$batch_id]["prod_qty"];
				if($sub_ttl_batch_prod_tot_arr[$batch_id]["trims"]>0)
				{
				$color_wise_batch_arr[$row[("color_range_id")]]["total_trims_weight"]+= $sub_ttl_batch_prod_tot_arr[$batch_id]["trims"];
				}
				$color_wise_batch_arr[$row[("color_range_id")]]["batch_ids"].=$batch_id.',';
				$sub_total_color_wise_batch_qty+= $sub_batch_qnty_arr[$batch_id]+$sub_ttl_batch_prod_tot_arr[$batch_id]["trims"];
				
				//echo $sub_ttl_batch_prod_tot_arr[$batch_id]["prod_qty"].'DD';
				//$sub_ttl_batch_prod+=$sub_ttl_batch_prod_tot_arr[$batch_id]["prod_qty"]+$sub_ttl_batch_prod_tot_arr[$batch_id]["trims"];
				$sub_batch_row_span_arr[$batch_id] = $sub_batch_row_span;
				
				$machine_id=$sub_machine_id_arr[$batch_id]['machine_id'];
				if (!in_array($machine_id,$sub_machine_check_array))
					{ $x++;
						
						
						 $sub_machine_check_array[]=$machine_id;
						 $sub_machine_capacity=$sub_tot_machine_capacity_arr[$batch_id];
					}
					else
					{
						 $sub_machine_capacity=0;
					}
					
				$sub_tot_machine_capacity+=$sub_machine_capacity;
			}	
			//echo $sub_ttl_batch_prod.'XXX';		
		}			
	}
	$all_days_total_date=count($tot_process_end_date_arr);
		
		if($date_search_type==1) $date_type_msg="Dyeing Date"; else $date_type_msg="Insert Date";
		//echo $date_type_msg;
		$load_hr=array();
		$load_min=array();
		$load_date=array();
		$water_flow_arr=array(); $load_hour_meter_arr=array();
		if ($working_company==0) $workingCompany_name_cond1=""; else $workingCompany_name_cond1="  and service_company='".$working_company."' ";
		if ($working_company==0) $workingCompany_name_cond13=""; else $workingCompany_name_cond13="  and f.service_company='".$working_company."' ";
		if ($company==0) $companyCond1=""; else $companyCond1="  and f.company_id=$company";
	
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
		$sub_load_time_data=sql_select("select f.batch_id, f.water_flow_meter, f.batch_no, f.load_unload_id, f.process_end_date, f.end_hours, f.hour_load_meter, f.end_minutes from pro_fab_subprocess f where f.load_unload_id=1 and f.entry_form=38 $companyCond1 and f.status_active=1  and f.is_deleted=0 $workingCompany_name_cond13 $sub_dyeing_batch_id_cond");
		//echo "select f.batch_id, f.water_flow_meter, f.batch_no, f.load_unload_id, f.process_end_date, f.end_hours, f.hour_load_meter, f.end_minutes from pro_fab_subprocess f where f.load_unload_id=1 and f.entry_form=38 $companyCond1 and f.status_active=1  and f.is_deleted=0 $workingCompany_name_cond13 $sub_dyeing_batch_id_cond";
		
		foreach($sub_load_time_data as $row_time)// for Loading time
		{
			$sub_load_hr[$row_time[csf('batch_id')]]=$row_time[csf('end_hours')];
			$sub_load_min[$row_time[csf('batch_id')]]=$row_time[csf('end_minutes')];
			$sub_load_date[$row_time[csf('batch_id')]]=$row_time[csf('process_end_date')];
			$sub_water_flow_arr[$row_time[csf('batch_id')]]=$row_time[csf('water_flow_meter')];
			$sub_load_hour_meter_arr[$row_time[csf('batch_id')]]=$row_time[csf('hour_load_meter')];
		}
		unset($sub_load_time_data);
		
	
		
	//print_r($btb_shade_matched[1]["shade_matched"]);
		/*	
			$subcon_load_hr=array();
			$subcon_load_min=array();
			$subcon_load_date=array();$subcon_load_hour_meter_arr=array();
			$subcon_water_flow_arr=array();
			$subcon_load_time_data=sql_select("select f.batch_id,f.water_flow_meter,f.batch_no,f.load_unload_id,f.process_end_date,f.end_hours,f.hour_load_meter,f.end_minutes from pro_fab_subprocess f where f.load_unload_id=1 and f.entry_form=38 $companyCond1  and f.status_active=1  and f.is_deleted=0 $unload_batch_cond2  $cbo_prod_source_cond $machine_cond $floor_id_cond $cbo_prod_type_cond $workingCompany_name_cond1 $dyeing_batch_id_cond");
			foreach($subcon_load_time_data as $row_time)// for Loading time
			{
				$subcon_load_hr[$row_time[csf('batch_id')]]=$row_time[csf('end_hours')];
				$subcon_load_min[$row_time[csf('batch_id')]]=$row_time[csf('end_minutes')];
				$subcon_load_date[$row_time[csf('batch_id')]]=$row_time[csf('process_end_date')];
				$subcon_water_flow_arr[$row_time[csf('batch_id')]]=$row_time[csf('water_flow_meter')];
				$subcon_load_hour_meter_arr[$row_time[csf('batch_id')]]=$row_time[csf('hour_load_meter')];
			}
			$subcon_unload_hr=array();
			$subcon_unload_min=array();
			$subcon_unload_date=array();
			$subcon_unload_time_data=sql_select("select f.batch_id,f.batch_no,f.load_unload_id,f.production_date,f.end_hours,f.end_minutes from pro_fab_subprocess f where f.load_unload_id=2 and f.entry_form=38 $companyCond1 $workingCompany_name_cond1 and f.status_active=1  and f.is_deleted=0 $unload_batch_cond2  $cbo_prod_source_cond $machine_cond $floor_id_cond $cbo_prod_type_cond $dyeing_batch_id_cond");
			foreach($subcon_load_time_data as $row_time)// for Loading time
			{
				$subcon_unload_hr[$row_time[csf('batch_id')]]=$row_time[csf('end_hours')];
				$subcon_unload_min[$row_time[csf('batch_id')]]=$row_time[csf('end_minutes')];
				$subcon_unload_time_data[$row_time[csf('batch_id')]]=$row_time[csf('production_date')];
			}
			//var_dump($load_hr);
			
			$m_capacity=array();
			$unload_min=array();
			$machine_arr=return_library_array( "select id,$field_concat from  lib_machine_name order by seq_no ",'id','machine_name');
			$machine_capacity_data=sql_select("select id,prod_capacity as m_capacity  from lib_machine_name where status_active=1  and is_deleted=0 ");
			foreach($machine_capacity_data as $capacity)// for Un-Loading time
			{
				$m_capacity[$capacity[csf('id')]]=$capacity[csf('m_capacity')];
			}
			
			$sql_batch_id=("select f.batch_id from  pro_fab_subprocess f where f.entry_form=35 $companyCond1 and f.load_unload_id=2 and f.status_active=1 and f.is_deleted=0  $unload_batch_cond2 $dates_com $cbo_prod_source_cond $machine_cond $floor_id_cond $cbo_prod_type_cond $dyeing_batch_id_cond");
			$tot_row=1;$batch_id='';
			foreach($sql_batch_id as $row_batch)
			{
				if($batch_id=='') $batch_id=$row_batch[csf('batch_id')];else $batch_id.=",".$row_batch[csf('batch_id')];
				$batch_unload_check[$row_batch[csf('batch_id')]]=$row_batch[csf('batch_id')];

			}
			unset($sql_batch_id);

			if($batch_id!='')
			{
				$batchIds=chop($batch_id,','); $batchIds_cond="";
				$tot_ids=count(array_unique(explode(",",$batch_id)));
					
				if($db_type==2 && $tot_ids>999)
				{
					$batchIds_cond=" and (";
					$batchIdsArr=array_chunk(explode(",",$batchIds),999);
					foreach($batchIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$batchIds_cond.=" a.id not in($ids) or ";
					}
					$batchIds_cond=chop($batchIds_cond,'or ');
					$batchIds_cond.=")";
				}
				else
				{
					$batchIds=implode(",",array_unique(explode(",",$batchIds)));
					$batchIds_cond=" and a.id not in($batchIds)";
				}
			}

			if($cbo_type==1)
			{
				$sub_sql_batch_id=sql_select("select f.batch_id from  pro_fab_subprocess f where f.entry_form=38 and f.load_unload_id=2 and f.status_active=1 and f.is_deleted=0 $unload_batch_cond2 $dates_com $cbo_prod_source_cond $shift_name_cond $cbo_prod_type_cond  $dye_sub_batch_id_cond ");

				$k=1;
				foreach($sub_sql_batch_id as $row_batch)
				{
					if($k!=1) $sub_batch_id.=",";	
					$sub_batch_id.=$row_batch[csf('batch_id')];	
					
					$k++;
				}
				if($batch_id=="") $batch_id=0;
				if($sub_batch_id=="") $sub_batch_id=0;
			}
		*/
			
			ob_start();
			if($cbo_type==1)
			{
				?>
				<div>
					<fieldset style="width:1450px;">
						<div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong><br><strong> Dyeing WIP </strong> </div>
						<div>
							<? 
							//$batchdata = sql_select($sql);
							$batchdata = sql_select($sql);
							foreach($batchdata as $row)
							{
								$batch_id.=$row[csf('id')].",";
							}
							$batch_ids=rtrim($batch_id,',');
							$baIds=chop($batch_id,','); $ba_cond_in="";
							$ba_ids=count(array_unique(explode(",",$batch_ids)));
							if($ba_ids>1000 && $db_type==2)
							{
							$ba_cond_in=" and (";
							$baIdsArr=array_chunk(explode(",",$baIds),999);
							foreach($baIdsArr as $ids)
							{
							$ids=implode(",",$ids);
							$ba_cond_in.=" f.batch_id in($ids) or"; 
							}
							$ba_cond_in=chop($ba_cond_in,'or ');
							$ba_cond_in.=")";
							}
							else
							{
							$ba_cond_in=" and f.batch_id in($baIds)";
							}
				
							$sql_batch_id=sql_select("select f.batch_id from  pro_fab_subprocess f where f.entry_form =35 $companyCond1 and f.load_unload_id=2 and f.status_active=1 and f.is_deleted=0  $unload_batch_cond2  $cbo_prod_source_cond $machine_cond $floor_id_cond $cbo_prod_type_cond $dyeing_batch_id_cond $ba_cond_in");
							//echo "select f.batch_id from  pro_fab_subprocess f where f.entry_form =35 $companyCond1 and f.load_unload_id=2 and f.status_active=1 and f.is_deleted=0  $unload_batch_cond2  $cbo_prod_source_cond $machine_cond $floor_id_cond $cbo_prod_type_cond $dyeing_batch_id_cond $ba_cond_in";
						
							//$tot_row=1;$batch_id='';
							foreach($sql_batch_id as $row_batch)
							{
							//if($batch_id=='') $batch_id=$row_batch[csf('batch_id')];else $batch_id.=",".$row_batch[csf('batch_id')];
							$batch_unload_check[$row_batch[csf('batch_id')]]=$row_batch[csf('batch_id')];
							}
							unset($sql_batch_id);
							
							if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
							{

								if (count($batchdata)>0)
								{
									?>
									<div align="left"> <b>Self batch D</b></div>
									<table class="rpt_table" width="1450" cellpadding="0" cellspacing="0" border="1" rules="all" align="left" id="table_header_1">
										<thead>
											<tr>
												<th width="30">SL</th>
												<? if($group_by==3 || $group_by==0){ ?>
													<th width="80">M/C No</th>
												<? } ?>
												<? if($group_by==2 || $group_by==0){ ?>
													<th width="80">Floor</th>  
												<? } if($group_by==1 || $group_by==0){ ?> 
													<th width="80">Shift</th>
												<? } ?>
												<th width="100">Booking No</th>
												<th width="100">Buyer</th>
												<th width="80">Style</th>
												<th width="90">FSO No</th>
												<th width="100">Fabrics Desc</th>
												<th width="70">Dia/Width Type</th>
												<th width="80">Color Name</th>
												<th width="90">Batch No</th>
												<th width="40">Ext. No</th>
												<th width="80">Fabric Weight</th>
												<th width="80">Trims Weight</th>
												<th width="80">Total Batch Weight</th>
												<th width="100">Loading Date & Time</th>
												<th>process</th>
											</tr>
										</thead>
									</table>
									<div style=" max-height:360px; width:1470px; overflow-y:scroll;" id="scroll_body" align="left">
										<table class="rpt_table" id="table_body" width="1450" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
											<tbody>
												<? 
												$i=1;$k=1;
												$f=0;
												$btq=0;$grand_total_batch_qty=0; $sub_trim_tot=0;$sub_batch_wgt=0;
												$batch_chk_arr=array();$group_by_arr=array();
												foreach($batchdata as $batch)
												{ 
													if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													if($batch_unload_check[$batch[csf('id')]]=='')
													{
														if($group_by!=0)
														{
															if($group_by==1)
															{
																$group_value=$batch[csf('floor_id')];
																$group_name="Floor";
																$group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
															}
															else if($group_by==2)
															{
																$group_value=$batch[csf('shift_name')];
																$group_name="Shift";
																$group_dtls_value=$shift_name[$batch[csf('shift_name')]];
															}
															else if($group_by==3)
															{
																$group_value=$batch[csf('machine_id')];
																$group_name="machine";
																$group_dtls_value=$machine_arr[$batch[csf('machine_id')]];
															}
															if (!in_array($group_value,$group_by_arr) )
															{
																if($k!=1)
																{ 	
																	?>
																	<tr class="tbl_bottom">
																		<td width="30">&nbsp;</td>
																		<? if($group_by==3 || $group_by==0){ ?>
																			<td width="80">&nbsp;</td> 
																		<? } ?>
																		<? if($group_by==2 || $group_by==0){ ?>
																			<td width="80">&nbsp;</td> 
																		<? } if($group_by==1 || $group_by==0){ ?>  
																			<td width="80">&nbsp;</td>
																		<? } ?>
																		<td width="100">&nbsp;</td>
																		<td width="100">&nbsp;</td>
																		<td width="80">&nbsp;</td>
																		<td width="90">&nbsp;</td>
																		<td width="100">&nbsp;</td>
																		<td width="70">&nbsp;</td>
																		<td width="80">&nbsp;</td>

																		<td width="90" >Sub.Total: </td>
																		<td width="40"></td>
																		<td width="80"><? echo number_format($btq,2); ?></td>
																		<td width="80"><? echo number_format($sub_trim_tot,2); ?></td>
																		<td width="80"><? echo number_format($sub_batch_wgt,2); ?></td>
																		<td width="100"></td>

																		<td>&nbsp;</td>
																	</tr>                                
																	<?
																	unset($btq);unset($sub_trim_tot);unset($sub_batch_wgt);
																}
																?>
																<tr bgcolor="#EFEFEF">
																	<td colspan="16" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
																</tr>
																<?
																$group_by_arr[]=$group_value;            
																$k++;
															}					
														}

														$order_id=$batch[csf('po_id')];
														$color_id=$batch[csf('color_id')];
														$desc=explode(",",$batch[csf('item_description')]); 
														$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')]))); 
														?>
														<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">

															<td width="30"><? echo $i; ?></td>

															<? if($group_by==3 || $group_by==0){ ?>
																<td  align="center" width="80" title="<? echo $machine_arr[$batch[csf('machine_id')]]; ?>"><p><? echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
																<?
															}
															if($group_by==2 || $group_by==0){ ?>
																<td width="80"><p><? echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
															<? } if($group_by==1 || $group_by==0){ ?>
																<td width="80"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
															<? } ?>
															<td  width="100" align="center"><p><? echo $batch[csf('sales_booking_no')]; ?></p></td>
															<td width="100" align="center" title="<? echo $batch[csf('po_buyer')];?>"><p><? if($batch[csf('within_group')]==2) echo $buyer_arr[$batch[csf('buyer_id')]]; else echo $buyer_arr[$batch[csf('po_buyer')]];?></p></td>

															<td  width="80" align="center"><p style="word-break: break-all;"><? echo $batch[csf('style_ref_no')]; ?></p></td>
															<td width="90" align="center"><p><? echo $batch[csf('fso_no')]; ?></p></td>

															<td  width="100"><p style="word-break: break-all;"><? echo $batch[csf('item_description')]; ?></p></td>
															<td  width="70" title="<? echo $fabric_typee[$batch[csf('width_dia_type')]]; ?>"><p><? echo $fabric_typee[$batch[csf('width_dia_type')]]; ?></p></td>
															<td  width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
															<td  align="center" width="90" style="word-break: break-all;"><p><? echo $batch[csf('batch_no')]; ?></p></td>
															<td  align="center" width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
															<td align="right" width="80"   title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
															<? 
															$total_batch_wgt=0;
															if($batchTrimChk[$batch[csf('id')]]=="")
															{
																$total_batch_wgt = $batch[csf('batch_qnty')]+ $batch[csf('total_trims_weight')];
																?>
																<td align="right" width="80" ><? echo number_format($batch[csf('total_trims_weight')],2);  ?></td>
																<td align="right" width="80" ><? echo number_format($total_batch_wgt,2);  ?></td>
																<?
																$batchTrimChk[$batch[csf('id')]] = $batch[csf('id')]; 
																$sub_trim_tot += $batch[csf('total_trims_weight')];
																$grand_trim_tot += $batch[csf('total_trims_weight')];
															}
															else
															{
																$total_batch_wgt = $batch[csf('batch_qnty')];
																?>
																<td align="right" width="80" ><?  ?></td>
																<td align="right" width="80" ><? echo number_format($total_batch_wgt,2);  ?></td>
																<?
															}
															?>

															<td width="100" align="center"><p><?  echo  ($batch[csf('process_end_date')] == '0000-00-00' || $batch[csf('process_end_date')] == '' ? '' : change_date_format($batch[csf('process_end_date')])).'<br>'.$batch[csf('end_hours')].':'.$batch[csf('end_minutes')]; ?></p></td>

															<td align="center"><? echo $conversion_cost_head_array[$batch_against[$batch[csf('process_id')]]];?> &nbsp;</td>
														</tr>
														<? 
														$i++;
														$btq+=$batch[csf('batch_qnty')];
														$grand_total_batch_qty+=$batch[csf('batch_qnty')];

														$sub_batch_wgt += $total_batch_wgt;
														$grand_batch_wgt += $total_batch_wgt;
													}
												} 
												?>
											</tbody>
										</table>
										<table class="rpt_table" width="1450" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
											<tfoot>
												<?
												if($group_by!=0)
												{
													?>
													<tr>
														<th width="30">&nbsp;</th>
														<? if($group_by==3 || $group_by==0){ ?>
															<th width="80">&nbsp;</th>
														<? } ?>
														<? if($group_by==2 || $group_by==0){ ?>
															<th width="80">&nbsp;</th>  
														<? } if($group_by==1 || $group_by==0){ ?> 
															<th width="80">&nbsp;</th>
														<? } ?>
														<th width="100">&nbsp;</th>
														<th width="100">&nbsp;</th>
														<th width="80">&nbsp;</th>
														<th width="90">&nbsp;</th>
														<th width="100">&nbsp;</th>
														<th width="70">&nbsp;</th>
														<th width="80">&nbsp;</th>
														<th width="130" colspan="2">Total</th>
														<th width="80" align="right"><? echo number_format($btq,2); ?></th>
														<th width="80" align="right"><? echo number_format($sub_trim_tot,2);?></th>
														<th width="80" align="right"><? echo number_format($sub_batch_wgt,2);?></th>
														<th width="100">&nbsp;</th>
														<th>&nbsp;</th>
													</tr>
													<?
												}
												?>
												<tr>
													<th width="30">&nbsp;</th>
													<? if($group_by==3 || $group_by==0){ ?>
														<th width="80">&nbsp;</th>
													<? } ?>
													<? if($group_by==2 || $group_by==0){ ?>
														<th width="80">&nbsp;</th>  
													<? } if($group_by==1 || $group_by==0){ ?> 
														<th width="80">&nbsp;</th>
													<? } ?>
													<th width="100">&nbsp;</th>
													<th width="100">&nbsp;</th>
													<th width="80">&nbsp;</th>
													<th width="90">&nbsp;</th>
													<th width="100">&nbsp;</th>
													<th width="70">&nbsp;</th>
													<th width="80">&nbsp;</th>

													<th width="90">GrandTotal</th>
													<th width="40"></th>
													<th width="80" align="right" id="value_grand_batch_qnty"><? echo number_format($grand_total_batch_qty,2); ?></th>
													<th width="80" align="right" id="value_grand_trims_qnty"><? echo number_format($grand_trim_tot,2); ?></th>
													<th width="80" align="right" id="value_grand_tot_batch_wgt"><? echo number_format($grand_batch_wgt,2); ?></th>
													<th width="100">&nbsp;</th>
													<th>&nbsp;</th>
												</tr>
											</tfoot>
										</table>
									</div>
									<br/>
									<? 
								}
							}

							?>
						</div>
					</fieldset>
					<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
				</div>
				<?
			}
			else if($cbo_type==2) 
			{
				?>
				<div>
					<fieldset style="width:1350px;">
						<div align="center"><strong> <? echo $company_library[$company]; ?> </strong><br> <strong> Daily Dyeing Production </strong><br>
							<?

							echo  ($date_from == '0000-00-00' || $date_from == '' ? '' : change_date_format($date_from)).' To ';echo  ($date_to == '0000-00-00' || $date_to == '' ? '' : change_date_format($date_to));
							?>
						</div>
						<?  

						if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
						{
							if (count($batchdata)>0)
							{
								?>
								<table cellpadding="0"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
									<thead>
										<tr>
											<th colspan="3">Production Summary</th>
										</tr>
										<tr>
											<th width="130">Details </th> 
											<th width="120">Prod. Qty. </th>
											<th>%</th>
										</tr>
									</thead>
									<tbody>
										<tr bgcolor="#E9F3FF">
											<td>Total RFT</td>
											<td align="right"><? echo number_format($total_rft_qnty,2);?></td>
											<td align="right"><? echo number_format(($total_rft_qnty*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty),2);?></td>
										</tr>
										<tr bgcolor="#FFFFFF">
											<td>Total Adding</td> 
											<td align="right"><? echo number_format($total_adding_qnty,2);?></td>
											<td align="right"><? echo number_format(($total_adding_qnty*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty),2); ?></td>
										</tr>
                                        <tr bgcolor="#FFFF00">
											<td>Total:</td> 
											<td align="right"><? 
											$total_rft_add=0; $total_rft_add_per=0;
											$total_rft_add=$total_rft_qnty+$total_adding_qnty;
											$total_rft_add_per=(($total_rft_qnty*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty))+(($total_adding_qnty*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty));
											echo number_format($total_rft_add,2);?></td>
											<td align="right"><? echo number_format($total_rft_add_per,2);?></td>
										</tr>
										<tr bgcolor="#E9F3FF">
											<td>Total Re-Process</td> 
											<td align="right"><? echo number_format($total_reprocess_qty,2);?></td> 
											<td align="right"><? echo number_format(($total_reprocess_qty*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty),2);?></td> 
										</tr>
										<tr bgcolor="#EBEBEB">
											<td align="right"><b>Grand Total:</b></td> 
											<td align="right">
												<b><? echo number_format($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty,2); ?></b>
											</td> 
											<td align="right"><b><? echo number_format((($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty)*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty),2);?></b></td>  
											</tr>
											<tr bgcolor="#FFFFFF">
												<td><b>Avg. Prod. Per Day</b></td> 
												<td align="right" title="<? echo "days=".implode(",",array_filter(array_unique($production_date_count)));?>">
													<b><? 
													echo number_format(($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty)/count(array_filter(array_unique($production_date_count))),2);
													?></b>	
												</td> 
												<td></td> 
											</tr>
											<? 
											$ftsl=2;
											foreach($fabric_type_production_arr as $fabType=>$val)
											{ 
												if ($ftsl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													<td><? echo $fabric_type_for_dyeing[$fabType];?></td> 
													<td align="right"><? echo number_format($val,2);?></td> 
													<td align="right"><? echo number_format(($val/$fabric_type_production_total)*100,2);?></td> 
												</tr>
												<?
												$ftsl++;
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th>Total</th> 
												<th align="right"><? echo number_format($fabric_type_production_total,2);?></th> 
												<th align="right"><? echo number_format(100,2);?></th> 
											</tr>
										</tfoot>
									</table>

									<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
										<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
									</table>

									<table cellpadding="0"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<thead>
											<tr>
												<th colspan="4">Re-Process Summary (Buyer Wise)</th>
											</tr>
											<tr>
												<th width="40">Sl </th> 
												<th width="100">Buyer</th>
												<th width="100">Batch Total</th>
												<th>%</th>
											</tr>
										</thead>
										<tbody>
											<? 
											$sl=1;
											foreach($buyer_wise_summary as $buyer=> $bs)
											{
												if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													<td align="center"><? echo $sl;?></td>
													<td align="center"><? echo $buyer_arr[$buyer];?></td>
													<td align="right"><? echo number_format($bs,2);?></td>
													<td align="right"><? echo number_format(($bs/$buyer_wise_summary_batch_total)*100,2);?></td>
												</tr>
												<?
												$sl++;
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="2">Total </th>
												<th><? echo number_format($buyer_wise_summary_batch_total,2);?></th>
												<th><? if ($buyer_wise_summary_batch_total>0) echo "100.00"; else echo "0.00";?></th>
											</tr>
										</tfoot>
									</table>
<table cellpadding="0"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<thead>
											<tr>
												<th colspan="5">Color Range wise Summary</th>
											</tr>
											<tr>
												<th width="40">Sl </th> 
												<th width="100">Color Range</th>
												<th width="100">Quantity (Kgs)</th>
												<th  width="50">Percentage%</th>
												<th>No. Of Batches</th>
											</tr>
										</thead>
										<tbody>
											<? 
											$sl=1;$tot_batch_qty=$tot_no_of_batch=$tot_total_trims_weight=0;
											//print_r($color_wise_batch_arr);ttl_batch_prod_tot
											foreach($color_wise_batch_arr as $color_rang_id=> $val)
											{
												$total_color_qty+=$val['summary_batch_qnty']+$val['total_trims_weight'];
											}
											foreach($color_wise_batch_arr as $color_rang_id=> $summ)
											{
												if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												$batch_ids=rtrim($summ[batch_ids],',');
												$batch_ids_arr=array_unique(explode(",",$batch_ids));
												$tot_total_trims_weight+=$summ['total_trims_weight'];
												//echo count($batch_ids_arr).',';
												//echo $val['total_trims_weight'].'D';
												$tot_prod_qty_summ=$summ['summary_batch_qnty']+$summ['total_trims_weight'];
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													<td align="center"><? echo $sl;?></td>
													<td align="center"><? echo $color_range[$color_rang_id];?></td>
													<td align="right"><? echo number_format($tot_prod_qty_summ,2);?></td>
													<td align="right" title="Tot batch Qty=<? echo $total_color_qty;?>">
													<? echo number_format(($tot_prod_qty_summ/($total_color_qty))*100,2);?></td>
													<td align="right"><? echo count($batch_ids_arr);?></td>
												</tr>
												<?
												$sl++;
												$tot_batch_qty+=$tot_prod_qty_summ;
												$tot_no_of_batch+=count($batch_ids_arr);
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="2">Total </th>
												<th><? echo number_format($tot_batch_qty,2);?></th>
												<th><? echo number_format(($tot_batch_qty/($total_color_qty))*100,2);?></th>
												<th><? echo number_format($tot_no_of_batch,0);?></th>
											</tr>
										</tfoot>
									</table>
									<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
										<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
									</table>

									<table cellpadding="0"  width="640" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<thead>
											<tr>
												<th colspan="8">Summary Total (Shade Match)</th>
											</tr>
											<tr>
												<th width="100">Buyer</th>
												<th width="100">Self batch</th> 
												<th width="100">Smpl. Batch With Order</th>
												<th width="60">Smpl. Batch Without Order</th>
												<th width="60">Trims Weight</th>
												<th width="60">Inbound subcontract</th>
												<th width="100">Buyer Total</th>
												<th>%</th>
											</tr>
										</thead>
										<tbody>
											<? 
											$sl=1;
											foreach($shade_matched_wise_summary as $buyer=> $val)
											{
												if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												$tot_buyer_match_shade_subcon = $sub_buyer_batch_qnty_arr[$buyer];//$val["subcon"];
												$tot_buyer_match_shade = ($val["self"]+$val["sm"]+$val["smn"]+$val["trim"])+$tot_buyer_match_shade_subcon;
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													<td align="center"><? echo $buyer_arr[$buyer];?></td>
													<td align="right"><? echo number_format($val["self"],2); ?></td>
													<td align="right"><? echo number_format($val["sm"],2); ?></td>
													<td align="right"><? echo number_format($val["smn"],2); ?></td>
													<td align="right"><? echo number_format($val["trim"],2); ?></td>
													<td align="right"><? echo number_format($tot_buyer_match_shade_subcon,2); ?></td>
													<td align="right"><? echo number_format($tot_buyer_match_shade,2);?></td>
													<td align="right"><? echo number_format(($tot_buyer_match_shade/$shade_matched_buyer_total)*100,2);?></td>
												</tr>
												<?
												$sl++;
												$total_self += $val["self"];
												$total_sm += $val["sm"];
												$total_smn += $val["smn"];
												$total_trim += $val["trim"];
												$total_subcon += $tot_buyer_match_shade_subcon;
												$total_tot_buyer_match_shade += $tot_buyer_match_shade;
												$total_summury_shade_matched +=($tot_buyer_match_shade/$shade_matched_buyer_total)*100;

												$grand_total_self += $val["self"];
												$grand_total_sm += $val["sm"];
												$grand_total_smn += $val["smn"];
												$grand_total_trim += $val["trim"];
												$grand_total_subcon += $tot_buyer_match_shade_subcon;
												$grand_total_buyer += $tot_buyer_match_shade;
											}
											unset($shade_matched_wise_summary);
											?>
											<tr style="background-color: #eee;font-weight: bold;">
												<td width="100" align="right">Total</th>
													<td width="100" align="right"><? echo number_format($total_self,2);?></td> 
													<td width="100" align="right"><? echo number_format($total_sm,2);?></td>
													<td width="60" align="right"><? echo number_format($total_smn,2);?></td>
													<td width="60" align="right"><? echo number_format($total_trim,2);?></td>
													<td width="60"><? echo number_format($grand_total_subcon,2);?></td>
													<td width="100" align="right"><? echo number_format($total_tot_buyer_match_shade,2);?></td>
													<td align="right"><? echo number_format($total_summury_shade_matched,2);?></td>
												</tr>
											</tbody>
											<thead>
												<tr>
													<th colspan="8">Summary Total (Shade Not Match)</th>
												</tr>
											</thead>
											<tbody>
												<?
												foreach($shade_not_matched_wise_summary as $buyer=> $val)
												{
													if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
													$tot_buyer_match_not_shade_subcon = $val["subcon"];
													$tot_buyer_match_not_shade = ($val["self"]+$val["sm"]+$val["smn"]+$val["trim"])+$tot_buyer_match_not_shade_subcon;
													?>
													<tr bgcolor="<? echo $bgcolor_dyeing;?>">
														<td align="center"><? echo $buyer_arr[$buyer];?></td>
														<td align="right"><? echo number_format($val["self"],2);?></td>
														<td align="right"><? echo number_format($val["sm"],2);?></td>
														<td align="right"><? echo number_format($val["smn"],2);?></td>
														<td align="right"><? echo number_format($val["trim"],2);?></td>
														<td align="right"><? echo number_format($tot_buyer_match_not_shade_subcon,2); ?></td>
														<td align="right"><? echo number_format($tot_buyer_match_not_shade,2);?></td>
														<td align="right"><? echo number_format(($tot_buyer_match_not_shade/$shade_not_matched_buyer_total)*100,2);?></td>
													</tr>
													<?
													$sl++;

													$total_self_not_matched += $val["self"];
													$total_sm_not_matched += $val["sm"];
													$total_smn_not_matched += $val["smn"];
													$total_trim_not_matched += $val["trim"];
													$total_subcon_not_matched += $tot_buyer_match_not_shade_subcon;
													$total_tot_buyer_match_shade_not_matched += $tot_buyer_match_not_shade;
													$total_summury_shade_matched_not_matched +=($tot_buyer_match_not_shade/$shade_not_matched_buyer_total)*100;

													$grand_total_self += $val["self"];
													$grand_total_sm += $val["sm"];
													$grand_total_smn += $val["smn"];
													$grand_total_trim += $val["trim"];
													$grand_total_subcon+= $tot_buyer_match_not_shade_subcon;
													$grand_total_buyer += $tot_buyer_match_not_shade;
												}
												?>
											</tbody>
											<tfoot>
												<tr bgcolor="#EBEBEB">
													<th align="center">Total</th>
													<th align="right"><? echo number_format($total_self_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_sm_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_smn_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_trim_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_subcon_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_tot_buyer_match_shade_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_summury_shade_matched_not_matched,2);?></th>
												</tr>
												<tr bgcolor="#EBEBEB">
													<th align="center">Grand Total</th>
													<th align="right"><? echo number_format($grand_total_self,2);?></th>
													<th align="right"><? echo number_format($grand_total_sm,2);?></th>
													<th align="right"><? echo number_format($grand_total_smn,2);?></th>
													<th align="right"><? echo number_format($grand_total_trim,2);?></th>
													<th align="right"><? echo number_format($grand_total_subcon,2);?></th>
													<th align="right"><? echo number_format($grand_total_buyer,2);?></th>
													<th align="right"><? ?></th>
												</tr>
											</tfoot>
											
											
										</table>

										
										<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
											<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
											
										</table>
										

										<table cellpadding="0"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
											<thead>
												<tr>
													<th colspan="3"></th>
												</tr>
												<tr>
													<th width="130">Re-Processed</th> 
													<th width="100">Batch Qnty</th>
													<th width="">%</th>
												</tr>
											</thead>
											<tbody>
												<? 
												$sl=1;
												foreach($dyeing_process_wise_batch_qnty_arr as $processId=> $val)
												{
													if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor_dyeing;?>">
														<td align="center"><? echo $conversion_cost_head_array[$processId];?></td>
														<td align="right"><? echo number_format($val,2);?></td>
														<td align="right"><? echo number_format(($val/$dyeing_process_wise_batch_qnty_total)*100,2);?></td>
													</tr>
													<?
													$sl++;
												}
												?>
											</tbody>
											<tfoot>
												<tr>
													<th>Total </th>
													<th><? echo number_format($dyeing_process_wise_batch_qnty_total,2);?></th>
													<th>100.00</th>
												</tr>
											</tfoot>
										</table>

										<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
											<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
										</table>

										<table cellpadding="0"  width="400" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
											<thead>
												<tr>
													<th colspan="4"></th>
												</tr>
												<tr>
													<th width="130">RFT + Adding </th> 
													<th width="100">No Of Batch </th>
													<th width="100">Shade Matched</th>
													<th width="">%</th>
												</tr>
											</thead>
											<tbody>
												<? 
												$sl=1;
												foreach($btb_shade_matched as $BtbLtb=> $val)
												{
													if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor_dyeing;?>">
														<td align="center"><? if($BtbLtb==1) echo "Bulk To Bulk"; else if($BtbLtb==2) echo "Lab To Bulk"?></td>
														<td align="right"><? echo $val["total_batch"];?></td>
														<td align="right"><? echo $val["shade_matched"];?></td>
														<td align="right"><? echo number_format((($val["shade_matched"])/$val["total_batch"])*100,2);?></td>
													</tr>
													<?
													$sl++;
													$tot_total_batch += $val["total_batch"];
													$tot_shade_matched += $val["shade_matched"];
												}
												?>
											</tbody>
											<tfoot>
												<tr>
													<th align="left">Total =</th>
													<th><? echo $tot_total_batch;?></th>
													<th><? echo $tot_shade_matched;?></th>
													<th><? echo number_format(($tot_shade_matched/$tot_total_batch)*100,2);?></th>
												</tr>
											</tfoot>
										</table>
										<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
											<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
										</table>
										<table cellpadding="0" style="margin-bottom:-5px;"  width="160" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
											
											<tbody bgcolor="#FFFFFF">
											<?
												$total_machine_capacity=0;
												foreach($total_running_machine_arr as $mid=>$val)
												{
													$total_machine_capacity+= $machine_capacity_arr[$mid];
													$mids.=$mid.',';
												}
												//echo $mids;
												$tot_machine=count($total_machine_arr);
												//echo $tot_machine.'XXd';
												$running_machine=count($total_running_machine_arr);
												$stop_machine=$tot_machine-$running_machine;
												$running_machine_percent=(($running_machine/$tot_machine)*100);
												$stop_machine_percent=(($stop_machine/$tot_machine)*100);
													$total_batch_weight=($ttl_batch_qty+$sub_ttl_batch_prod)*100;
													//$total_machine_capacity=($tot_machine_capacity+$sub_tot_machine_capacity)*1;
													$avg_load_percent=($total_batch_weight/($total_machine_capacity*$all_days_total_date));
											?>
												<tr>
													<td width="90" title="Total batch Weight(<? echo number_format($ttl_batch_qty,2).' + '.number_format($sub_ttl_batch_prod,2);?>)*100/(Total Running Machine Capacity(<? echo $total_machine_capacity;?>)*Working Day(<? echo $all_days_total_date;?>))">Avg. Load% :</td>
													<td align="right"><? 
													echo number_format($avg_load_percent,2);
													
													?></td>
													
												</tr>
												<tr>
												<td width="90" title="Tot Machine=<? echo $tot_machine;?>">Avg.Batch/Machine: </td>
												<td title="Total No Of Batch/Running Machine(<? echo $running_machine;?>)" align="right">
												<? $avg_batch_machine=$tot_total_batch/$running_machine; echo number_format($avg_batch_machine,2); ?>
												</td>
													
												</tr>
											</tbody>
										</table>
										<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
											<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
										</table>
										<table cellpadding="0"  width="350" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<tr bgcolor="#E9F3FF">
											<td width="150">Total number of M/C running </td>
											<td width="80" align="right"><? echo $running_machine; ?></td>
											<td align="right" width="80"><? if( $running_machine_percent>0) echo number_format($running_machine_percent,2,'.','')." % "; ?></td>
										</tr>
										<tr bgcolor="#FFFFFF">
											<td width="150">Total number of M/C stop</td>
											<td align="right" width="80"><? echo $stop_machine; ?></td>
											<td align="right" width="80"><? if( $running_machine_percent>0) echo number_format($stop_machine_percent,2,'.','')." % "; ?></td>
										</tr>
										
									</table>
										
										
										<?
										$group_by=str_replace("'",'',$cbo_group_by);
										?>
										<div align="left">

											<table class="rpt_table" width="3070" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
												<caption> <b>Self batch  </b></caption>
												<thead>
													<tr>
														<th width="30">SL</th>
														<th width="80"><? echo $date_type_msg;?></th>
														<? if($group_by==3 || $group_by==0){ ?>
															<th width="80">M/C No</th>
														<? } if($group_by==2 || $group_by==0){ ?>
															<th width="80">Floor</th>  
														<? } if($group_by==1 || $group_by==0){ ?> 
															<th width="80">Shift Name</th>
														<? } ?>
														<th width="100">Buyer</th>
														<th width="100">Style Ref.</th>
														<th width="100">Season</th>
														<th width="110">Fso No</th>
														<th width="90">Fabric Booking No</th>
														<th width="70">Booking Type</th>
														<th width="100">Body Part</th>
														<th width="100">Fabrics Desc</th>
														<th width="70">Dia/Width Type</th>
														<th width="50">Lot No</th> 
														<th width="100">Yarn Information</th> 
														<th width="80">Color Name</th>
														<th width="80">Color Range</th>
														<th width="90">Batch No</th>
														<th width="40">Extn. No</th>
														<th width="70">Fabric Wgt.</th>
														<th width="70">Trims Wgt.</th>
														<th width="70">Batch Wgt.</th>
														<th width="70">M/C Capacity.</th>
														<th width="70">Loading %.</th>
														<th width="75">Load Date & Time</th>
														<th width="75">UnLoad Date Time</th>
														<th width="60">Time Used</th>
														<th width="60">BTB/LTB</th>
														<th width="100">Dyeing Fab. Type</th>
														<th width="100">Result</th>
														<th width="80">Dyeing Process</th>
														<th width="80"><p>Dyeing Re Process Name</p></th>

														<th width="60">Hour Load Meter</th>
														<th width="60">Hour unLoad Meter</th>
														<th width="60">Total Time</th>

														<th width="60">Water Loading Flow</th> 
														<th width="60">Water UnLoading Flow</th>
														<th width="70">Water Cons.</th>

														<th width="">Remark</th>
													</tr>
												</thead>
											</table>
											<div style=" max-height:350px; width:3090px; overflow-y:scroll;;" id="scroll_body">
												<table class="rpt_table" id="table_body" width="3070" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
													<tbody>
														<? 
														$i=1; $total_water_cons_load=0;$total_water_cons_unload=$tot_machine_capacity=0;$grand_total_production_qnty=$sub_grand_tot_trims_qnty=0;
														$batch_chk_arr=array(); $group_by_arr=array();$tot_trims_qnty=0;$trims_check_array=array();
														foreach($data_array_batch as $batch_id=>$batch_data_arr)
														{
															$b=1;
															foreach ($batch_data_arr as $prod_id => $row) 
															{

																
																$batch_td_span = $batch_row_span_arr[$batch_id];
																//echo $batch_td_span.',';
																if ($i%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";

																if($group_by!=0)
																{
																	if($group_by==1)
																	{
																		$group_value=$row['floor_id'];
																		$group_name="Floor";
																		$group_dtls_value=$floor_arr[$row['floor_id']];
																	}
																	else if($group_by==2)
																	{
																		$group_value=$row['shift_name'];
																		$group_name="Shift";
																		$group_dtls_value=$shift_name[$row['shift_name']];
																	}
																	else if($group_by==3)
																	{
																		$group_value=$row['machine_id'];
																		$group_name="machine";
																		$group_dtls_value=$machine_arr[$row['machine_id']];
																	}
																	if (!in_array($group_value,$group_by_arr) )
																	{
																		if($k>1)
																		{ 	
																			?>
																			<tr class="tbl_bottom">
																				<td width="30">&nbsp;</td>
																				<td width="80">&nbsp;</td>

																				<? if($group_by==3 || $group_by==0){ ?>
																					<td width="80">&nbsp;</td> 
																				<? } ?>
																				<? if($group_by==2 || $group_by==0){ ?>
																					<td width="80">&nbsp;</td> 
																				<? } if($group_by==1 || $group_by==0){ ?>  
																					<td width="80">&nbsp;</td>
																				<? } ?>
																				<td width="100" align="center"><p></p></td>
																				<td width="100" align="center"><p></p></td>
																				<td width="100" align="center"><p></p></td>
																				<td width="80" align="center"><p></p></td>
																				<td width="90"></td>
																				<td width="70" align="center"></td>
																				<td width="100"></td>
																				<td width="100"></td>
																				<td width="70"><p></p></td>
																				<td width="50"><p></p></td>
																				<td width="100"><p></p></td>
																				<td width="80"><p></p></td>
																				<td width="80"><p></p></td>
																				<td width="90"><p></p></td>
																				<td width="40"><p></p></td>
																				<td align="right" width="70"><? echo  number_format($sub_production_qnty,2);?></td>
																				<td align="right" width="70"></td>
																				<td align="right" width="70"><? echo number_format($batch_qnty_tot,2);?></td>
																				
																				<td align="right" width="70"></td>
																				<td align="right" width="70"></td>
																				
																				<td width="75"><p></p></td>
																				<td width="75"><p></p></td>
																				<td align="center" width="60"> </td>
																				<td align="center" width="60"></td>
																				<td align="center" width="100"></td>
																				<td align="center" width="100"></td>
																				<td align="center" width="80"></td>
																				<td align="center" width="80"> </td>
																				<td width="60" align="right"><p></p></td>
																				<td width="60" align="right"><p></p></td>
																				<td width="60" align="right" ><p></p></td>
																				<td width="60" align="right"><p></p></td>
																				<td width="60" align="right"><p></p></td>
																				<td align="right" width="70" ></td>
																				<td align="center"></td>
																			</tr>                                
																			<?
																			$batch_qnty_tot=0; $trims_btq=0;$sub_production_qnty=0;
																		}
																		?>
																		<tr bgcolor="#EFEFEF">
																			<td colspan="39" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
																		</tr>
																		<?
																		$group_by_arr[]=$group_value;            
																		$k++;
																	}					
																}

																$batch_qnty = $row["prod_qnty_tot"]+$row["total_trims_weight"];
																$batch_weight=$row['batch_weight'];
																$water_cons_unload=$row['water_flow_meter'];
																$water_cons_load=$water_flow_arr[$batch_id];
																$load_hour_meter=$load_hour_meter_arr[$batch_id];
																$water_cons_diff=($water_cons_unload-$water_cons_load)/$batch_weight*1000;

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
																?>
																<tr bgcolor="<? echo $bgcolor_dyeing; ?>"  id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor_dyeing; ?>')">
																	<td width="30"><? echo $i; ?></td>
																	<td width="80"><p><? echo change_date_format($date_type_cond); $unload_date=$row['process_end_date']; ?></p></td>
																	<? if($group_by==3 || $group_by==0){ ?>
																		<td align="center" width="80"><p><? echo $machine_arr[$row['machine_id']]; ?></p></td>
																	<? } if($group_by==2 || $group_by==0){ ?>
																		<td width="80"><p><? echo $floor_arr[$row['floor_id']]; ?></p></td>
																	<? } if($group_by==1 || $group_by==0){ ?>
																		<td width="80" align="center"><p><? echo $shift_name[$row['shift_name']]; ?></p></td>
																	<? } ?>

																	<td width="100" align="center">
																		<p>
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
																		</p>
																	</td>
																	<td width="100" align="center"><p><? echo $row['style_ref_no']; ?></p></td>
																	<td width="100" align="center"><p><? echo $row['season']; ?></p></td>
																	<td width="110" align="center"><p><? echo $row['sales_no']; ?></p></td>
																	<td width="90"><p><? echo $row['booking_no']; ?></p></td>
																	<td width="70" align="center"><p><? echo $booking_type_arr[$row['booking_no']]; ?></p></td>
																	<td width="100" style="width:100px; word-wrap:break-word;"><p><?  echo $row['body_part']; ?></p></td>
																	<td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $row['item_description']; ?></div></td>
																	<td width="70" style="width:70px; word-wrap:break-word;"><p><? echo $fabric_typee[$row['width_dia_type']]; ?></p></td>																<td width="50" style="width:50px; word-wrap:break-word;" title="<? echo implode(",",array_filter(array_unique($row["barcode_no"])));?>">
																		<p>
																			<? $bar_lot_arr= array();$yarnlots="";$yarn_types="";$yarn_count_name="";$yarn_comp_type1st="";
																			$bar_lot_arr =array_filter(array_unique($row["barcode_no"]));
																			if(count($bar_lot_arr)>0){
																				foreach ($bar_lot_arr as $bcode) 
																				{
																					$yarnlots .= chop($yarn_lot_arr[$bcode],",").",";
																					
																					$yarn_types .= chop($yarn_data_arr[$bcode]['yarn_type'],",").",";
																					$yarn_comp_type1st .= chop($yarn_data_arr[$bcode]['yarn_comp_type1st'],",").",";
																					$yarn_count_name .= chop($yarn_data_arr[$bcode]['yarn_count_id'],",").",";
																					$colors .= chop($yarn_data_arr[$bcode]['color'],",").",";																				}
																				echo implode(",",array_filter(array_unique(explode(",", $yarnlots))));  
																			}
																			$yarn_types=implode(",",array_filter(array_unique(explode(",", $yarn_types))));
																			$yarn_count_name=implode(",",array_filter(array_unique(explode(",", $yarn_count_name))));
																			$yarn_comp_type1st=implode(",",array_filter(array_unique(explode(",", $yarn_comp_type1st))));
																			$colors=implode(",",array_filter(array_unique(explode(",", $colors))));
																			?>
																		</p>
																	</td>

																	<td width="100" style="width:100px; word-wrap:break-word;"><p><? echo $yarn_count_name.','.$yarn_comp_type1st.','.$yarn_types; ?></p></td>
																	<td width="80" style="width:80px; word-wrap:break-word;"><p><? echo $color_library[$row['color_id']]; ?></p></td>
																	<td width="80" style="width:80px; word-wrap:break-word;"><p><? echo $color_range[$row['color_range_id']]; ?></p></td>
																	<? if($b==1){ ?>
																		<td width="90" rowspan="<? echo $batch_td_span?>"><p><? echo $row['batch_no']; ?></p></td>
																		<td width="40" rowspan="<? echo $batch_td_span?>"><p><? echo $row['extention_no']; ?></p></td>
																		<? } ?>
																		<td align="right" width="70"><? echo number_format($row['production_qnty'],2); $grand_total_production_qnty += $row['production_qnty']; $sub_production_qnty += $row['production_qnty'];?></td>

																		<? if($b==1){?>
																			<td align="right" width="70" rowspan="<? echo $batch_td_span?>"><? echo number_format($row["total_trims_weight"],2);  ?></td>
																			<td align="right" width="70" rowspan="<? echo $batch_td_span?>"><? echo number_format($batch_qnty,2);  ?></td>
																			<?
																			$batch_qnty_tot+=$batch_qnty;
																			$grand_total_batch_qty+=$batch_qnty;
																			$trims_btq+=$row["total_trims_weight"];
																			$sub_grand_tot_trims_qnty+=$row["total_trims_weight"];
																			$tot_machine_capacity+=$machine_capacity_arr[$row['machine_id']];

																			?>
																			<td align="right" width="70" rowspan="<? echo $batch_td_span?>"><? echo number_format($machine_capacity_arr[$row['machine_id']],2);  ?>
																			</td>
																			<td align="right" width="70" title="Batch Weight/Machine Capacity*100" rowspan="<? echo $batch_td_span?>"><? echo number_format(($batch_qnty/$machine_capacity_arr[$row['machine_id']]*100),2);  ?>
																			</td>
																			<td width="75" rowspan="<? echo $batch_td_span?>" title="<? echo $load_date[$batch_id];?>"><p><? $load_t=$load_hr[$batch_id].':'.$load_min[$batch_id]; 
																			echo  ($load_date[$batch_id] == '0000-00-00' || $load_date[$batch_id] == '' ? '' : change_date_format($load_date[$batch_id])).' <br> '.$load_t;

																			?></p></td>
																			<td width="75" rowspan="<? echo $batch_td_span?>"><p><? $hr=strtotime($unload_date,$load_t); $min=($row['end_minutes'])-($load_min[$batch_id]);
																			echo  ($row['process_end_date'] == '0000-00-00' || $row['process_end_date'] == '' ? '' : change_date_format($row['process_end_date'])).'<br>'.$unload_time=$row['end_hours'].':'.$row['end_minutes']; ?></p>
																		</td>
																		<td align="center" width="60" rowspan="<? echo $batch_td_span?>">
																			<?     
																			$new_date_time_unload=($unload_date.' '.$unload_time.':'.'00');
																			$new_date_time_load=($load_date[$batch_id].' '.$load_t.':'.'00');
																			$total_time=datediff(n,$new_date_time_load,$new_date_time_unload);
																			echo floor($total_time/60).":".$total_time%60;
																			?>
																		</td>
																		<td align="center" width="60" rowspan="<? echo $batch_td_span?>"><? echo $ltb_btb[$row["ltb_btb_id"]];?></td>

																		<td align="center" width="100" rowspan="<? echo $batch_td_span?>"><p><? 
																		echo $fabric_type_for_dyeing[$row['fabric_type']];?></p> </td>
																		<td align="center" width="100" style="width:100px; word-wrap:break-word;" rowspan="<? echo $batch_td_span?>"><p><? echo $dyeing_result[$row['result']]; ?></p> </td>
																		<td align="center" width="80" style="width:80px; word-wrap:break-word;" rowspan="<? echo $batch_td_span?>"><? echo $conversion_cost_head_array[$row["process_id"]]?></td>
																		<td align="center" width="80" rowspan="<? echo $batch_td_span?>" title="<? echo $row["add_tp_strip"];?>">
																			<? 
																			if($row["process_id"]==148 && $row["add_tp_strip"] ==1)
																			{
																				echo "Re-Wash";
																			}
																			else{
																				echo $dyeing_re_process[$row["add_tp_strip"]];
																			} 
																			?>
																		</td>
																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo number_format($load_hour_meter,2);  ?></p></td>
																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo number_format($row['hour_unload_meter'],2);  ?></p></td>
																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo number_format($row['hour_unload_meter']-$load_hour_meter,2);  ?></p></td>

																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo $water_cons_load;  ?></p></td>
																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo $water_cons_unload;  ?></p></td>
																		<td align="right" width="70" rowspan="<? echo $batch_td_span?>" title="<? echo "Water Cons Unload-Water Cons Load*1000/Batch Qnty"?>" ><? echo number_format((($water_cons_unload-$water_cons_load)*1000)/$row['batch_qnty'],2);  ?></td>

																		<td align="center" rowspan="<? echo $batch_td_span?>"><p><? echo $row['remarks']; ?></p> </td>
																		<? } ?>
																	</tr>
																	<? 
																	$i++;$b++;


																	$total_water_cons_load+=$water_cons_load;
																	$total_water_cons_unload+=$water_cons_unload;


																}
															}
															if($group_by!=0)
															{
																?>
																<tr class="tbl_bottom">
																	<td width="30">&nbsp;</td>
																	<td width="80">&nbsp;</td>

																	<? if($group_by==3 || $group_by==0){ ?>
																		<td width="80">&nbsp;</td> 
																	<? } ?>
																	<? if($group_by==2 || $group_by==0){ ?>
																		<td width="80">&nbsp;</td> 
																	<? } if($group_by==1 || $group_by==0){ ?>  
																		<td width="80">&nbsp;</td>
																	<? } ?>
																	<td width="100" align="center"><p></p></td>
																	<td width="100" align="center"><p></p></td>
																	<td width="100" align="center"><p></p></td>
																	<td width="110" align="center"><p></p></td>
																	<td width="90"></td>
																	<td width="70" align="center"></td>
																	<td width="100"></td>
																	<td width="100"></td>
																	<td width="70"><p></p></td>
																	<td width="50"><p></p></td>
																	<td width="100"><p></p></td>
																	<td width="80"><p></p></td>
																	
																	<td width="80"><p></p></td>
																	<td width="90"><p></p></td>
																	<td width="40"><p></p></td>
																	<td align="right" width="70"><? echo  number_format($sub_production_qnty,2);?></td>
																	<td align="right" width="70"></td>
																	<td align="right" width="70"><? echo number_format($batch_qnty_tot,2);?></td>
																	<td width="70"><p></p></td>
																	<td width="70"><p></p></td>
																	<td width="75"><p></p></td>
																	<td width="75"><p></p></td>
																	<td align="center" width="60"> </td>
																	<td align="center" width="60"></td>
																	<td align="center" width="100"></td>
																	<td align="center" width="100"></td>
																	<td align="center" width="80"></td>
																	<td align="center" width="80"> </td>
																	<td width="60" align="right"><p></p></td>
																	<td width="60" align="right"><p></p></td>
																	<td width="60" align="right" ><p></p></td>
																	<td width="60" align="right"><p></p></td>
																	<td width="60" align="right"><p></p></td>
																	<td align="right" width="70" ></td>
																	<td align="center"></td>
																</tr> 
																<? 
															}

															?>            
														</tbody>
														<tfoot>
															<tr class="tbl_bottom">
																<td width="30">&nbsp;</td>
																<td width="80">&nbsp;</td>

																<? if($group_by==3 || $group_by==0){ ?>
																	<td width="80">&nbsp;</td> 
																<? } ?>
																<? if($group_by==2 || $group_by==0){ ?>
																	<td width="80">&nbsp;</td> 
																<? } if($group_by==1 || $group_by==0){ ?>  
																	<td width="80">&nbsp;</td>
																<? } ?>
																<td width="100" align="center"><p></p></td>
																<td width="100" align="center"><p></p></td>
																<td width="100" align="center"><p></p></td>
																<td width="110" align="center"><p></p></td>
																<td width="90"></td>
																<td width="70" align="center"></td>
																<td width="100"></td>
																<td width="100"></td>
																<td width="70"><p></p></td>
																<td width="50"><p></p></td>
																<td width="100"><p></p></td>
																<td width="80"><p></p></td>
																
																<td width="80"><p></p></td>
																<td width="90"><p></p></td>
																<td width="40"><p></p></td>
																<td align="right" width="70"><? echo number_format($grand_total_production_qnty,2);?></td>
																<td align="right" width="70"><? echo number_format($sub_grand_tot_trims_qnty,2);?></td>
																<td align="right" width="70"><? echo number_format($grand_total_batch_qty,2);?></td>
																<td width="70"><p><? echo number_format($tot_machine_capacity,2);?></p></td>
																<td width="70"><p></p></td>
																<td width="75"><p></p></td>
																<td width="75"><p></p></td>
																<td align="center" width="60"> </td>
																<td align="center" width="60"></td>
																<td align="center" width="100"></td>
																<td align="center" width="100"></td>
																<td align="center" width="80"></td>
																<td align="center" width="80"> </td>
																<td width="60" align="right"><p></p></td>
																<td width="60" align="right"><p></p></td>
																<td width="60" align="right" ><p></p></td>
																<td width="60" align="right"><p></p></td>
																<td width="60" align="right"><p></p></td>
																<td align="right" width="70" ></td>
																<td align="center"></td>
															</tr> 
														</tfoot>
													</table>
												</div>
											</div>
											<br/>
											<? 
										}
									}
						if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
						{
							if (count($subbatchdata)>0)
							{
								$group_by=str_replace("'",'',$cbo_group_by);
								if(str_replace("'",'',$cbo_batch_type)==2)
								{
								?>
								<table cellpadding="0"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
									<thead>
										<tr>
											<th colspan="3">Production Summary</th>
										</tr>
										<tr>
											<th width="130">Details </th> 
											<th width="120">Prod. Qty. </th>
											<th>%</th>
										</tr>
									</thead>
									<tbody>
										<tr bgcolor="#E9F3FF">
											<td>Total RFT</td>
											<td align="right"><? echo number_format($total_rft_qnty,2);?></td>
											<td align="right"><? echo number_format(($total_rft_qnty*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty),2);?></td>
										</tr>
										<tr bgcolor="#FFFFFF">
											<td>Total Adding</td> 
											<td align="right"><? echo number_format($total_adding_qnty,2);?></td>
											<td align="right"><? echo number_format(($total_adding_qnty*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty),2); ?></td>
										</tr>
                                        <tr bgcolor="#FFFF00">
											<td>Total:</td> 
											<td align="right"><? 
											$total_rft_add=0; $total_rft_add_per=0;
											$total_rft_add=$total_rft_qnty+$total_adding_qnty;
											$total_rft_add_per=(($total_rft_qnty*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty))+(($total_adding_qnty*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty));
											echo number_format($total_rft_add,2);?></td>
											<td align="right"><? echo number_format($total_rft_add_per,2);?></td>
										</tr>
										<tr bgcolor="#E9F3FF">
											<td>Total Re-Process</td> 
											<td align="right"><? echo number_format($total_reprocess_qty,2);?></td> 
											<td align="right"><? echo number_format(($total_reprocess_qty*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty),2);?></td> 
										</tr>
										<tr bgcolor="#EBEBEB">
											<td align="right"><b>Grand Total:</b></td> 
											<td align="right">
												<b><? echo number_format($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty,2); ?></b>
											</td> 
											<td align="right"><b><? echo number_format((($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty)*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty),2);?></b></td>  
											</tr>
											<tr bgcolor="#FFFFFF">
												<td><b>Avg. Prod. Per Day</b></td> 
												<td align="right" title="<? echo "days=".implode(",",array_filter(array_unique($production_date_count)));?>">
													<b><? 
													echo number_format(($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty)/count(array_filter(array_unique($production_date_count))),2);
													?></b>	
												</td> 
												<td></td> 
											</tr>
											<? 
											$ftsl=2;
											foreach($fabric_type_production_arr as $fabType=>$val)
											{ 
												if ($ftsl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													<td><? echo $fabric_type_for_dyeing[$fabType];?></td> 
													<td align="right"><? echo number_format($val,2);?></td> 
													<td align="right"><? echo number_format(($val/$fabric_type_production_total)*100,2);?></td> 
												</tr>
												<?
												$ftsl++;
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th>Total</th> 
												<th align="right"><? echo number_format($fabric_type_production_total,2);?></th> 
												<th align="right"><? echo number_format(100,2);?></th> 
											</tr>
										</tfoot>
									</table>

									<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
										<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
									</table>

									<table cellpadding="0"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<thead>
											<tr>
												<th colspan="4">Re-Process Summary (Buyer Wise)</th>
											</tr>
											<tr>
												<th width="40">Sl </th> 
												<th width="100">Buyer</th>
												<th width="100">Batch Total</th>
												<th>%</th>
											</tr>
										</thead>
										<tbody>
											<? 
											$sl=1;
											foreach($buyer_wise_summary as $buyer=> $bs)
											{
												if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													<td align="center"><? echo $sl;?></td>
													<td align="center"><? echo $buyer_arr[$buyer];?></td>
													<td align="right"><? echo number_format($bs,2);?></td>
													<td align="right"><? echo number_format(($bs/$buyer_wise_summary_batch_total)*100,2);?></td>
												</tr>
												<?
												$sl++;
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="2">Total </th>
												<th><? echo number_format($buyer_wise_summary_batch_total,2);?></th>
												<th><? if ($buyer_wise_summary_batch_total>0) echo "100.00"; else echo "0.00";?></th>
											</tr>
										</tfoot>
									</table>
									<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
										<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
									</table>
									<table cellpadding="0"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<thead>
											<tr>
												<th colspan="5">Color Range wise Summary</th>
											</tr>
											<tr>
												<th width="40">Sl </th> 
												<th width="100">Color Range</th>
												<th width="100">Quantity (Kgs)</th>
												<th  width="50">Percentage%</th>
												<th>No. Of Batches</th>
											</tr>
										</thead>
										<tbody>
											<? 
											$sl=1;$tot_batch_qty=$tot_no_of_batch=$tot_total_trims_weight=0;
											foreach($color_wise_batch_arr as $color_rang_id=> $val)
											{
												$total_color_qty+=$val['batch_qnty'];
											}
											//print_r($color_wise_batch_arr);
											foreach($color_wise_batch_arr as $color_rang_id=> $val)
											{
												if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												$batch_ids=rtrim($val[batch_ids],',');
												$batch_ids_arr=array_unique(explode(",",$batch_ids));
												$tot_total_trims_weight+=$val['total_trims_weight'];
												//echo count($batch_ids_arr).',';
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													<td align="center"><? echo $sl;?></td>
													<td align="center"><? echo $color_range[$color_rang_id];?></td>
													<td align="right"><? echo number_format($val['batch_qnty'],2);?></td>
													<td align="right" title="Tot batch Qty=<? echo $total_color_qty;?>">
													<? echo number_format(($val['batch_qnty']/($total_color_qty))*100,2);?></td>
													<td align="right"><? echo count($batch_ids_arr);?></td>
												</tr>
												<?
												$sl++;
												$tot_batch_qty+=$val['batch_qnty'];
												$tot_no_of_batch+=count($batch_ids_arr);
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="2">Total </th>
												<th><? echo number_format($tot_batch_qty,2);?></th>
												<th><? echo number_format(($tot_batch_qty/($total_color_qty))*100,2);?></th>
												<th><? echo number_format($tot_no_of_batch,0);?></th>
											</tr>
										</tfoot>
									</table>
									
									

									<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
										<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
									</table>

									<table cellpadding="0"  width="640" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<thead>
											<tr>
												<th colspan="8">Summary Total (Shade Match)</th>
											</tr>
											<tr>
												<th width="100">Buyer</th>
												<th width="100">Self batch</th> 
												<th width="100">Smpl. Batch With Order</th>
												<th width="60">Smpl. Batch Without Order</th>
												<th width="60">Trims Weight</th>
												<th width="60">Inbound subcontract</th>
												<th width="100">Buyer Total</th>
												<th>%</th>
											</tr>
										</thead>
										<tbody>
											<? 
											$sl=1;
											foreach($shade_matched_wise_summary as $buyer=> $val)
											{
												if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												$tot_buyer_match_shade_subcon = $val["subcon"];
												$tot_buyer_match_shade = ($val["self"]+$val["sm"]+$val["smn"]+$val["trim"])+$tot_buyer_match_shade_subcon;
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													<td align="center"><? echo $buyer_arr[$buyer];?></td>
													<td align="right"><? echo number_format($val["self"],2); ?></td>
													<td align="right"><? echo number_format($val["sm"],2); ?></td>
													<td align="right"><? echo number_format($val["smn"],2); ?></td>
													<td align="right"><? echo number_format($val["trim"],2); ?></td>
													<td align="right"><? echo number_format($tot_buyer_match_shade_subcon,2); ?></td>
													<td align="right"><? echo number_format($tot_buyer_match_shade,2);?></td>
													<td align="right"><? echo number_format(($tot_buyer_match_shade/$shade_matched_buyer_total)*100,2);?></td>
												</tr>
												<?
												$sl++;
												$total_self += $val["self"];
												$total_sm += $val["sm"];
												$total_smn += $val["smn"];
												$total_trim += $val["trim"];
												$total_subcon += $tot_buyer_match_shade_subcon;
												$total_tot_buyer_match_shade += $tot_buyer_match_shade;
												$total_summury_shade_matched +=($tot_buyer_match_shade/$shade_matched_buyer_total)*100;

												$grand_total_self += $val["self"];
												$grand_total_sm += $val["sm"];
												$grand_total_smn += $val["smn"];
												$grand_total_trim += $val["trim"];
												$grand_total_subcon += $tot_buyer_match_shade_subcon;
												$grand_total_buyer += $tot_buyer_match_shade;

											}
											unset($shade_matched_wise_summary);
											?>
											<tr style="background-color: #eee;font-weight: bold;">
												<td width="100" align="right">Total</th>
													<td width="100" align="right"><? echo number_format($total_self,2);?></td> 
													<td width="100" align="right"><? echo number_format($total_sm,2);?></td>
													<td width="60" align="right"><? echo number_format($total_smn,2);?></td>
													<td width="60" align="right"><? echo number_format($total_trim,2);?></td>
													<td width="60"><? echo number_format($grand_total_subcon,2);?></td>
													<td width="100" align="right"><? echo number_format($total_tot_buyer_match_shade,2);?></td>
													<td align="right"><? echo number_format($total_summury_shade_matched,2);?></td>
												</tr>
											</tbody>
											<thead>
												<tr>
													<th colspan="8">Summary Total (Shade Not Match)</th>
												</tr>
											</thead>
											<tbody>
												<?
												foreach($shade_not_matched_wise_summary as $buyer=> $val)
												{
													if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
													$tot_buyer_match_not_shade_subcon = $val["subcon"];
													$tot_buyer_match_not_shade = ($val["self"]+$val["sm"]+$val["smn"]+$val["trim"])+$tot_buyer_match_not_shade_subcon;
													?>
													<tr bgcolor="<? echo $bgcolor_dyeing;?>">
														<td align="center"><? echo $buyer_arr[$buyer];?></td>
														<td align="right"><? echo number_format($val["self"],2);?></td>
														<td align="right"><? echo number_format($val["sm"],2);?></td>
														<td align="right"><? echo number_format($val["smn"],2);?></td>
														<td align="right"><? echo number_format($val["trim"],2);?></td>
														<td align="right"><? echo number_format($tot_buyer_match_not_shade_subcon,2); ?></td>
														<td align="right"><? echo number_format($tot_buyer_match_not_shade,2);?></td>
														<td align="right"><? echo number_format(($tot_buyer_match_not_shade/$shade_not_matched_buyer_total)*100,2);?></td>
													</tr>
													<?
													$sl++;

													$total_self_not_matched += $val["self"];
													$total_sm_not_matched += $val["sm"];
													$total_smn_not_matched += $val["smn"];
													$total_trim_not_matched += $val["trim"];
													$total_subcon_not_matched += $tot_buyer_match_not_shade_subcon;
													$total_tot_buyer_match_shade_not_matched += $tot_buyer_match_not_shade;
													$total_summury_shade_matched_not_matched +=($tot_buyer_match_not_shade/$shade_not_matched_buyer_total)*100;

													$grand_total_self += $val["self"];
													$grand_total_sm += $val["sm"];
													$grand_total_smn += $val["smn"];
													$grand_total_trim += $val["trim"];
													$grand_total_subcon+= $tot_buyer_match_not_shade_subcon;
													$grand_total_buyer += $tot_buyer_match_not_shade;
												}
												?>
											</tbody>
											<tfoot>
												<tr bgcolor="#EBEBEB">
													<th align="center">Total</th>
													<th align="right"><? echo number_format($total_self_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_sm_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_smn_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_trim_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_subcon_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_tot_buyer_match_shade_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_summury_shade_matched_not_matched,2);?></th>
												</tr>
												<tr bgcolor="#EBEBEB">
													<th align="center">Grand Total</th>
													<th align="right"><? echo number_format($grand_total_self,2);?></th>
													<th align="right"><? echo number_format($grand_total_sm,2);?></th>
													<th align="right"><? echo number_format($grand_total_smn,2);?></th>
													<th align="right"><? echo number_format($grand_total_trim,2);?></th>
													<th align="right"><? echo number_format($grand_total_subcon,2);?></th>
													<th align="right"><? echo number_format($grand_total_buyer,2);?></th>
													<th align="right"><? ?></th>
												</tr>
											</tfoot>
										</table>

										<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
											<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
										</table>

										<table cellpadding="0"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
											<thead>
												<tr>
													<th colspan="3"></th>
												</tr>
												<tr>
													<th width="130">Re-Processed</th> 
													<th width="100">Batch Qnty</th>
													<th width="">%</th>
												</tr>
											</thead>
											<tbody>
												<? 
												$sl=1;
												foreach($dyeing_process_wise_batch_qnty_arr as $processId=> $val)
												{
													if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor_dyeing;?>">
														<td align="center"><? echo $conversion_cost_head_array[$processId];?></td>
														<td align="right"><? echo number_format($val,2);?></td>
														<td align="right"><? echo number_format(($val/$dyeing_process_wise_batch_qnty_total)*100,2);?></td>
													</tr>
													<?
													$sl++;
												}
												?>
											</tbody>
											<tfoot>
												<tr>
													<th>Total </th>
													<th><? echo number_format($dyeing_process_wise_batch_qnty_total,2);?></th>
													<th>100.00</th>
												</tr>
											</tfoot>
										</table>

										<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
											<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
										</table>

										<table cellpadding="0"  width="400" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
											<thead>
												<tr>
													<th colspan="4"></th>
												</tr>
												<tr>
													<th width="130">RFT + Adding </th> 
													<th width="100">No Of Batch </th>
													<th width="100">Shade Matched</th>
													<th width="">%</th>
												</tr>
											</thead>
											<tbody>
												<? 
												$sl=1;
												foreach($btb_shade_matched as $BtbLtb=> $val)
												{
													if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor_dyeing;?>">
														<td align="center"><? if($BtbLtb==1) echo "Bulk To Bulk"; else if($BtbLtb==2) echo "Lab To Bulk"?></td>
														<td align="right"><? echo $val["total_batch"];?></td>
														<td align="right"><? echo $val["shade_matched"];?></td>
														<td align="right"><? echo number_format((($val["shade_matched"])/$val["total_batch"])*100,2);?></td>
													</tr>
													<?
													$sl++;
													$tot_total_batch += $val["total_batch"];
													$tot_shade_matched += $val["shade_matched"];
												}
												?>
											</tbody>
											<tfoot>
												<tr>
													<th align="left">Total =</th>
													<th><? echo $tot_total_batch;?></th>
													<th><? echo $tot_shade_matched;?></th>
													<th><? echo number_format(($tot_shade_matched/$tot_total_batch)*100,2);?></th>
												</tr>
											</tfoot>
										</table>
										<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
											<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
										</table>
										<table cellpadding="0" style=""  width="160" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
											
											<tbody bgcolor="#FFFFFF">
											<?
												$tot_machine=count($total_machine_arr);
												//echo $tot_machine.'XXd';
												$running_machine=count($total_running_machine_arr);
												$stop_machine=$tot_machine-$running_machine;
												$running_machine_percent=(($running_machine/$tot_machine)*100);
												$stop_machine_percent=(($stop_machine/$tot_machine)*100);
													$total_batch_weight=($total_color_wise_batch_qty+$sub_total_color_wise_batch_qty)*100;
													$total_machine_capacity=($tot_machine_capacity+$sub_tot_machine_capacity)*1;
													$avg_load_percent=($total_batch_weight/($total_machine_capacity*$all_days_total_date));
											?>
												<tr>
													<td width="90" title="Total batch Weight(<? echo $total_color_wise_batch_qty.'+ '.$sub_total_color_wise_batch_qty;?>)*100/(Total Running Machine Capacity(<? echo $total_machine_capacity;?>)*Working Day(<? echo $all_days_total_date;?>))">Avg. Load% :</td>
													<td align="right"><? 
													echo number_format($avg_load_percent,2);
													
													?></td>
													
												</tr>
												<tr>
												<td width="90" title="Tot Machine=<? echo $tot_machine;?>">Avg.Batch/Machine: </td>
												<td title="Total No Of Batch/Running Machine(<? echo $running_machine;?>)" align="right">
												<? $avg_batch_machine=$tot_total_batch/$running_machine; echo number_format($avg_batch_machine,2); ?>
												</td>
													
												</tr>
											</tbody>
										</table>
										<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
											<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
										</table>
										<table cellpadding="0"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<tr bgcolor="#E9F3FF">
											<td>Total number of M/C running </td>
											<td width="70" align="right"><? echo $running_machine; ?></td>
											<td align="right" width="70"><? if( $running_machine_percent>0) echo number_format($running_machine_percent,2,'.','')." % "; ?></td>
										</tr>
										<tr bgcolor="#FFFFFF">
											<td>Total number of M/C stop</td>
											<td width="70" align="right"><? echo $stop_machine; ?></td>
											<td width="70" align="right"><? if( $running_machine_percent>0) echo number_format($stop_machine_percent,2,'.','')." % "; ?></td>
										</tr>
										
									</table>
									
                                        
                                        <? } ?>
										<div align="left">

											<table class="rpt_table" width="2840" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
												<caption> <b>Inbound subcontract</b></caption>
												<thead>
													<tr>
														<th width="30">SL</th>
														<th width="80"><? echo $date_type_msg;?></th>
														<? if($group_by==3 || $group_by==0){ ?>
															<th width="80">M/C No</th>
														<? } if($group_by==2 || $group_by==0){ ?>
															<th width="80">Floor</th>  
														<? } if($group_by==1 || $group_by==0){ ?> 
															<th width="80">Shift Name</th>
														<? } ?>
														<th width="100">Buyer</th>
														<th width="100">Style Ref.</th>
														<th width="100">Season</th>
														<th width="80">Fso No</th>
														<th width="90">Fabric Booking No</th>
														<th width="70">Booking Type</th>
														<th width="100">Fabrics Desc</th>
														<th width="70">Dia/Width Type</th>
														<th width="50">Lot No</th> 
														<th width="80">Color Name</th>
														<th width="80">Color Range</th>
														<th width="90">Batch No</th>
														<th width="40">Extn. No</th>
														<th width="70">Fabric Wgt.</th>
														<th width="70">Trims Wgt.</th>
														<th width="70">Batch Wgt.</th>
														<th width="70">M/C Capacity</th>
														<th width="70">Loading %</th>
														<th width="75">Load Date & Time</th>
														<th width="75">UnLoad Date Time</th>
														<th width="60">Time Used</th>
														<th width="60">BTB/LTB</th>
														<th width="100">Dyeing Fab. Type</th>
														<th width="100">Result</th>
														<th width="80">Dyeing Process</th>
														<th width="80"><p>Dyeing Re Process Name</p></th>

														<th width="60">Hour Load Meter</th>
														<th width="60">Hour unLoad Meter</th>
														<th width="60">Total Time</th>

														<th width="60">Water Loading Flow</th> 
														<th width="60">Water UnLoading Flow</th>
														<th width="70">Water Cons.</th>

														<th width="">Remark</th>
													</tr>
												</thead>
											</table>
											<div style=" max-height:350px; width:2860px; overflow-y:scroll;;" id="scroll_body">
												<table class="rpt_table" id="table_body" width="2840" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
													<tbody>
														<? 
														$total_water_cons_load=0;$total_water_cons_unload=$grand_sub_tot_machine_capacity=0;$grand_total_production_qnty=$grand_subcon_total_batch_qty=0;
														$batch_chk_arr=array(); $group_by_arr=array(); $tot_trims_qnty=0; $trims_check_array=array();
														foreach($subbatch_data_arr as $batch_id=>$batch_data_arr)
														{
															$b=1;
															foreach ($batch_data_arr as $prod_id => $row) 
															{

																$batch_td_span = $sub_batch_row_span_arr[$batch_id];
																if ($i%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";

																if($group_by!=0)
																{
																	if($group_by==1)
																	{
																		$group_value=$row['floor_id'];
																		$group_name="Floor";
																		$group_dtls_value=$floor_arr[$row['floor_id']];
																	}
																	else if($group_by==2)
																	{
																		$group_value=$row['shift_name'];
																		$group_name="Shift";
																		$group_dtls_value=$shift_name[$row['shift_name']];
																	}
																	else if($group_by==3)
																	{
																		$group_value=$row['machine_id'];
																		$group_name="machine";
																		$group_dtls_value=$machine_arr[$row['machine_id']];
																	}
																	if (!in_array($group_value,$group_by_arr) )
																	{
																		if($k>1)
																		{ 	
																			?>
																			<tr class="tbl_bottom">
																				<td width="30">&nbsp;</td>
																				<td width="80">&nbsp;</td>

																				<? if($group_by==3 || $group_by==0){ ?>
																					<td width="80">&nbsp;</td> 
																				<? } ?>
																				<? if($group_by==2 || $group_by==0){ ?>
																					<td width="80">&nbsp;</td> 
																				<? } if($group_by==1 || $group_by==0){ ?>  
																					<td width="80">&nbsp;</td>
																				<? } ?>
																				<td width="100" align="center"><p></p></td>
																				<td width="100" align="center"><p></p></td>
																				<td width="100" align="center"><p></p></td>
																				<td width="80" align="center"><p></p></td>
																				<td width="90"></td>
																				<td width="70" align="center"></td>
																				<td width="100"></td>
																				<td width="70"><p></p></td>
																				<td width="50"><p></p></td>
																				<td width="80"><p></p></td>
																				<td width="80"><p></p></td>
																				<td width="90"><p></p></td>
																				<td width="40"><p></p></td>
																				<td align="right" width="70"><? echo  number_format($sub_production_qnty,2);?></td>
																				<td align="right" width="70"></td>
																				<td align="right" width="70"><? echo number_format($batch_qnty_tot,2);?></td>
																				<td width="70"><p></p></td>
																				<td width="70"><p></p></td>
																				<td width="75"><p></p></td>
																				<td width="75"><p></p></td>
																				<td align="center" width="60"> </td>
																				<td align="center" width="60"></td>
																				<td align="center" width="100"></td>
																				<td align="center" width="100"></td>
																				<td align="center" width="80"></td>
																				<td align="center" width="80"> </td>
																				<td width="60" align="right"><p></p></td>
																				<td width="60" align="right"><p></p></td>
																				<td width="60" align="right" ><p></p></td>
																				<td width="60" align="right"><p></p></td>
																				<td width="60" align="right"><p></p></td>
																				<td align="right" width="70" ></td>
																				<td align="center"></td>
																			</tr>                                
																			<?
																			$batch_qnty_tot=0; $trims_btq=0;$sub_production_qnty=0;
																		}
																		?>
																		<tr bgcolor="#EFEFEF">
																			<td colspan="37" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
																		</tr>
																		<?
																		$group_by_arr[]=$group_value;            
																		$k++;
																	}					
																}

																
																$batch_weight=$row['batch_weight'];
																$water_cons_unload=$row['water_flow_meter'];
																$water_cons_load=$sub_water_flow_arr[$batch_id];
																$load_hour_meter=$sub_load_hour_meter_arr[$batch_id];
																$water_cons_diff=($water_cons_unload-$water_cons_load)/$batch_weight*1000;

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
																?>
																<tr bgcolor="<? echo $bgcolor_dyeing; ?>"  id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor_dyeing; ?>')">
																	<td width="30"><? echo $i; ?></td>
																	<td width="80"><p><? echo change_date_format($date_type_cond); $unload_date=$row['process_end_date']; ?></p></td>
																	<? if($group_by==3 || $group_by==0){ ?>

																		<td align="center" width="80"><p><? echo $machine_arr[$row['machine_id']]; ?></p></td>
																	<? } if($group_by==2 || $group_by==0){ ?>
																		<td width="80"><p><? echo $floor_arr[$row['floor_id']]; ?></p></td>
																	<? } if($group_by==1 || $group_by==0){ ?>
																		<td width="80" align="center"><p><? echo $shift_name[$row['shift_name']]; ?></p></td>
																	<? } ?>

																	<td width="100" align="center">
																		<p>
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
																		</p>
																	</td>
																	<td width="100" align="center"><p><? echo $row['style_ref_no']; ?></p></td>
																	<td width="100" align="center"><p><? echo $row['season']; ?></p></td>
																	<td width="80" align="center"><p><? echo $row['job_no_prefix_num']; ?></p></td>
																	<td width="90"><p><? echo $row['booking_no']; ?></p></td>
																	<td width="70" align="center"><p><? echo $booking_type_arr[$row['booking_no']]; ?></p></td>
																	<td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $row['item_description']; ?></div></td>
																	<td width="70"><p><? echo $fabric_typee[$row['width_dia_type']]; ?></p></td>

																	<td width="50" title="<? echo implode(",",array_filter(array_unique($row["barcode_no"])));?>">
																		<p>
																			<? $bar_lot_arr= array();$yarnlots="";
																			$bar_lot_arr =array_filter(array_unique($row["barcode_no"]));
																			if(count($bar_lot_arr)>0){
																				foreach ($bar_lot_arr as $bcode) 
																				{
																					$yarnlots .= chop($yarn_lot_arr[$bcode],",").",";
																				}
																				echo implode(",",array_filter(array_unique(explode(",", $yarnlots))));  
																			}
																			?>
																		</p>
																	</td>

																	<td width="80"><p><? echo $color_library[$row['color_id']]; ?></p></td>
																	<td width="80"><p><? echo $color_range[$row['color_range_id']]; ?></p></td>
																	<? if($b==1){
																	$batch_qnty=$sub_batch_qnty_arr[$batch_id]+$row["total_trims_weight"];
																	 ?>
																		<td width="90" rowspan="<? echo $batch_td_span?>"><p><? echo $row['batch_no']; ?></p></td>
																		<td width="40" rowspan="<? echo $batch_td_span?>"><p><? echo $row['extention_no']; ?></p></td>
																		<? } ?>
																		<td align="right" width="70"><? echo number_format($row['production_qnty'],2); $grand_total_production_qnty += $row['production_qnty']; $sub_production_qnty += $row['production_qnty'];?></td>

																		<? if($b==1){?>
																			<td align="right" width="70" rowspan="<? echo $batch_td_span?>"><? echo number_format($row["total_trims_weight"],2);  ?></td>
																			<td align="right" width="70" rowspan="<? echo $batch_td_span?>"><? echo number_format($batch_qnty,2);  ?></td>
																			<?
																			$batch_qnty_tot+=$batch_qnty;
																			$grand_subcon_total_batch_qty+=$batch_qnty;
																			$trims_btq+=$row["total_trims_weight"];
																			$grand_tot_trims_qnty+=$row["total_trims_weight"];
																			$grand_sub_tot_machine_capacity+=$machine_capacity_arr[$row['machine_id']];

																			?>
																			<td align="right" width="70" rowspan="<? echo $batch_td_span?>"><? echo number_format($machine_capacity_arr[$row['machine_id']],2);  ?>
																			</td>
																			<td align="right" width="70" title="Batch Weight/Machine Capacity*100" rowspan="<? echo $batch_td_span?>"><? echo number_format(($batch_qnty/$machine_capacity_arr[$row['machine_id']]*100),2);  ?>
																			</td>
																			<td width="75" rowspan="<? echo $batch_td_span?>" title="<? echo $sub_load_date[$batch_id];?>"><p><? $load_t=$sub_load_hr[$batch_id].':'.$sub_load_min[$batch_id]; 
																			echo  ($sub_load_date[$batch_id] == '0000-00-00' || $sub_load_date[$batch_id] == '' ? '' : change_date_format($sub_load_date[$batch_id])).' <br> '.$load_t;

																			?></p></td>
																			<td width="75" rowspan="<? echo $batch_td_span?>"><p><? $hr=strtotime($unload_date,$load_t); $min=($row['end_minutes'])-($sub_load_min[$batch_id]);
																			echo  ($row['process_end_date'] == '0000-00-00' || $row['process_end_date'] == '' ? '' : change_date_format($row['process_end_date'])).'<br>'.$unload_time=$row['end_hours'].':'.$row['end_minutes']; ?></p>
																		</td>
																		<td align="center" width="60" rowspan="<? echo $batch_td_span?>">
																			<?     
																			$new_date_time_unload=($unload_date.' '.$unload_time.':'.'00');
																			$new_date_time_load=($sub_load_date[$batch_id].' '.$load_t.':'.'00');
																			$total_time=datediff(n,$new_date_time_load,$new_date_time_unload);
																			echo floor($total_time/60).":".$total_time%60;
																			?>
																		</td>
																		<td align="center" width="60" rowspan="<? echo $batch_td_span?>"><? echo $ltb_btb[$row["ltb_btb_id"]];?></td>

																		<td align="center" width="100" rowspan="<? echo $batch_td_span?>"><p><? 
																		echo $fabric_type_for_dyeing[$row['fabric_type']];?></p> </td>
																		<td align="center" width="100" rowspan="<? echo $batch_td_span?>"><p><? echo $dyeing_result[$row['result']]; ?></p> </td>
																		<td align="center" width="80" rowspan="<? echo $batch_td_span?>"><? echo $conversion_cost_head_array[$row["process_id"]]?></td>
																		<td align="center" width="80" rowspan="<? echo $batch_td_span?>" title="<? echo $row["add_tp_strip"];?>">
																			<? 
																			if($row["process_id"]==148 && $row["add_tp_strip"] ==1)
																			{
																				echo "Re-Wash";
																			}
																			else{
																				echo $dyeing_re_process[$row["add_tp_strip"]];
																			} 
																			?>
																		</td>
																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo number_format($load_hour_meter,2);  ?></p></td>
																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo number_format($row['hour_unload_meter'],2);  ?></p></td>
																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo number_format($row['hour_unload_meter']-$load_hour_meter,2);  ?></p></td>

																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo $water_cons_load;  ?></p></td>
																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo $water_cons_unload;  ?></p></td>
																		<td align="right" width="70" rowspan="<? echo $batch_td_span?>" title="<? echo "Water Cons Unload-Water Cons Load*1000/Batch Qnty"?>" ><? echo number_format((($water_cons_unload-$water_cons_load)*1000)/$row['batch_qnty'],2);  ?></td>

																		<td align="center" rowspan="<? echo $batch_td_span?>"><p><? echo $row['remarks']; ?></p> </td>
																		<?}?>
																	</tr>
																	<? 
																	$i++;$b++;


																	$total_water_cons_load+=$water_cons_load;
																	$total_water_cons_unload+=$water_cons_unload;


																}
															}
															if($group_by!=0)
															{
																?>
																<tr class="tbl_bottom">
																	<td width="30">&nbsp;</td>
																	<td width="80">&nbsp;</td>

																	<? if($group_by==3 || $group_by==0){ ?>
																		<td width="80">&nbsp;</td> 
																	<? } ?>
																	<? if($group_by==2 || $group_by==0){ ?>
																		<td width="80">&nbsp;</td> 
																	<? } if($group_by==1 || $group_by==0){ ?>  
																		<td width="80">&nbsp;</td>
																	<? } ?>
																	<td width="100" align="center"><p></p></td>
																	<td width="100" align="center"><p></p></td>
																	<td width="100" align="center"><p></p></td>
																	<td width="80" align="center"><p></p></td>
																	<td width="90"></td>
																	<td width="70" align="center"></td>
																	<td width="100"></td>
																	<td width="70"><p></p></td>
																	<td width="50"><p></p></td>
																	<td width="80"><p></p></td>
																	<td width="80"><p></p></td>
																	<td width="90"><p></p></td>
																	<td width="40"><p></p></td>
																	<td align="right" width="70"><? echo  number_format($sub_production_qnty,2);?></td>
																	<td align="right" width="70"></td>
																	<td align="right" width="70"><? echo number_format($batch_qnty_tot,2);?></td>
																	<td width="70"><p></p></td>
																	<td width="70"><p></p></td>
																	<td width="75"><p></p></td>
																	<td width="75"><p></p></td>
																	<td align="center" width="60"> </td>
																	<td align="center" width="60"></td>
																	<td align="center" width="100"></td>
																	<td align="center" width="100"></td>
																	<td align="center" width="80"></td>
																	<td align="center" width="80"> </td>
																	<td width="60" align="right"><p></p></td>
																	<td width="60" align="right"><p></p></td>
																	<td width="60" align="right" ><p></p></td>
																	<td width="60" align="right"><p></p></td>
																	<td width="60" align="right"><p></p></td>
																	<td align="right" width="70" ></td>
																	<td align="center"></td>
																</tr> 
																<? 
															}

															?>            
														</tbody>
														<tfoot>
															<tr class="tbl_bottom">
																<td width="30">&nbsp;</td>
																<td width="80">&nbsp;</td>

																<? if($group_by==3 || $group_by==0){ ?>
																	<td width="80">&nbsp;</td> 
																<? } ?>
																<? if($group_by==2 || $group_by==0){ ?>
																	<td width="80">&nbsp;</td> 
																<? } if($group_by==1 || $group_by==0){ ?>  
																	<td width="80">&nbsp;</td>
																<? } ?>
																<td width="100" align="center"><p></p></td>
																<td width="100" align="center"><p></p></td>
																<td width="100" align="center"><p></p></td>
																<td width="80" align="center"><p></p></td>
																<td width="90"></td>
																<td width="70" align="center"></td>
																<td width="100"></td>
																<td width="70"><p></p></td>
																<td width="50"><p></p></td>
																<td width="80"><p></p></td>
																<td width="80"><p></p></td>
																<td width="90"><p></p></td>
																<td width="40"><p></p></td>
																<td align="right" width="70"><? echo number_format($grand_total_production_qnty,2);?></td>
																<td align="right" width="70"><? echo number_format($grand_tot_trims_qnty,2);?></td>
																<td align="right" width="70"><? echo number_format($grand_subcon_total_batch_qty,2);?></td>
																<td align="right" width="70"><? echo number_format($grand_sub_tot_machine_capacity,2);?></td>
																
																<td width="70"><p></p></td>
																<td width="75"><p></p></td>
																<td width="75"><p></p></td>
																<td align="center" width="60"> </td>
																<td align="center" width="60"></td>
																<td align="center" width="100"></td>
																<td align="center" width="100"></td>
																<td align="center" width="80"></td>
																<td align="center" width="80"> </td>
																<td width="60" align="right"><p></p></td>
																<td width="60" align="right"><p></p></td>
																<td width="60" align="right" ><p></p></td>
																<td width="60" align="right"><p></p></td>
																<td width="60" align="right"><p></p></td>
																<td align="right" width="70" ></td>
																<td align="center"></td>
															</tr> 
														</tfoot>
													</table>
												</div>
											</div>
											<br/>
											<? 
										}
									}
									?>
								</fieldset>
							</div>
							<?
						} 
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
	//$filename=$user_id."_".$name.".xls";
						echo "$total_data****$filename";

						exit();
						
}
if($action=="generate_report2") //Show 2
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if($db_type==0) $year_field_by="and YEAR(a.insert_date)"; 
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	if($db_type==0) $field_concat="concat(machine_no,'-',brand) as machine_name"; 
	else if($db_type==2) $field_concat="machine_no || '-' || brand as machine_name";
	$company = str_replace("'","",$cbo_company_name);
	$working_company = str_replace("'","",$cbo_working_company_name);
	$buyer = str_replace("'","",$cbo_buyer_name);
	$cbo_prod_type = str_replace("'","",$cbo_prod_type);
	$cbo_party_id = str_replace("'","",$cbo_party_id);

	$date_search_type = str_replace("'","",$search_type);
	$job_number = str_replace("'","",$job_number);
	$job_number_id = str_replace("'","",$job_number_show);
	$batch_no = str_replace("'","",$batch_number_show);

	$machine=str_replace("'","",$txt_machine_id);
	$floor_name=str_replace("'","",$cbo_floor_id);

	$batch_number_hidden = str_replace("'","",$batch_number);
	$ext_num = str_replace("'","",$txt_ext_no);
	$hidden_ext = str_replace("'","",$hidden_ext_no);

	$fso_no = trim(str_replace("'","",$fso_no));
	$hidden_fso_no = str_replace("'","",$hidden_fso_no);
	$hidden_booking_no = str_replace("'","",$hidden_booking_no);
	$booking_no = str_replace("'","",$booking_no);

	$cbo_type = str_replace("'","",$cbo_type);
	$cbo_result_name = str_replace("'","",$cbo_result_name);
	$year = str_replace("'","",$cbo_year);
	$shift = str_replace("'","",$cbo_shift_name);

	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	if ($buyer==0) $sub_buyer_cond=""; else $sub_buyer_cond="and d.party_id='".$buyer."' ";
	if ($cbo_prod_type>0 && $cbo_party_id>0) $cbo_prod_type_cond="and f.service_company in($cbo_party_id) "; else $cbo_prod_type_cond="";
	if ($cbo_prod_type>0) $cbo_prod_source_cond="and f.service_source in($cbo_prod_type) "; else $cbo_prod_source_cond="";

	if ($cbo_result_name==0) $result_name_cond=""; else $result_name_cond="  and f.result='".$cbo_result_name."' ";
	if ($shift==0) $shift_name_cond=""; else $shift_name_cond="  and f.shift_name='".$shift."' ";
	if ($machine=="") $machine_cond=""; else $machine_cond =" and f.machine_id in ( $machine ) ";
	if ($floor_name==0 || $floor_name=='')
	{
		$floor_id_cond="";$floor_id_cond2="";
	}
	 else {
		 $floor_id_cond=" and f.floor_id=$floor_name";
		  $floor_id_cond2=" and floor_id=$floor_name";
	 }
	
	$hidden_booking_cond="";
	if($booking_no!="")
	{
		$hidden_booking_cond="and a.booking_no like '%$booking_no%' ";
	}
	//echo $hidden_booking_cond.'='.$booking_no;

	if ($buyer==0) $buyerdata=""; else $buyerdata=" and ((d.buyer_id = $buyer and d.within_group =2 ) or (d.po_buyer = $buyer and d.within_group =1))";


	if ($batch_no=="") $batch_num=""; else $batch_num="  and a.batch_no='".trim($batch_no)."' ";

	if ($batch_no=="") $unload_batch_cond=""; else $unload_batch_cond="  and batch_no='".trim($batch_no)."' ";
	if ($batch_no=="") $unload_batch_cond2=""; else $unload_batch_cond2="  and f.batch_no='".trim($batch_no)."' ";
	//if ($batch_no=="") $$sub_job_cond=""; else $$sub_job_cond="  and f.batch_no='".trim($batch_no)."' ";
	if ($working_company==0) $workingCompany_name_cond2=""; else $workingCompany_name_cond2="  and a.working_company_id='".$working_company."' ";
	if ($company==0) $companyCond=""; else $companyCond="  and a.company_id=$company";
	
	if ($working_company==0) $subcon_working_company_cond=""; else $subcon_working_company_cond="  and a.company_id='".$working_company."' ";
	if ($company==0) $subcon_companyCond=""; else $subcon_companyCond="  and a.company_id=$company"; 

	if(trim($ext_no)!="") $ext_no_search="%".trim($ext_no)."%"; else $ext_no_search="%%";

	$fsodata=($hidden_fso_no)? " and d.id in (".$hidden_fso_no .")" : '';
	if($hidden_fso_no=="")
	{
		$fsodata =($fso_no)? " and d.job_no like '%".str_pad($fso_no,5,'0',STR_PAD_LEFT)."%'" : ''; 
		if($year!=0)
		{
			$fsodata.=($year)? " and d.job_no like '%-".substr( $year, -2)."-%'" : ''; 
		}    
	}

	if($job_number!=""){
		$po_job_cond = " d.po_job_no ";
		$job_ref_arr = explode(",", $job_number);
		foreach ($job_ref_arr as $val) 
		{
			$po_job_cond .=" like '%".$val."%' or ";
		}
		$po_job_cond = " and ".chop($po_job_cond," or");
		
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
		{
			//if ($txt_order!='') $suborder_no="and c.order_no='$txt_order'"; else $suborder_no="";
			if ($job_number_id!='') $sub_job_cond="  and d.subcon_job like '%".$job_number_id."%' "; else $sub_job_cond="";
		}
	
	}

	$yarncount_arr = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');


	if ($ext_num=="") $ext_no=""; else $ext_no="  and a.extention_no=$ext_num ";
	if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
	if($txt_date_from && $txt_date_to)
	{
		if($db_type==0)
		{
			$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');

			$start_dd= strtotime(change_date_format($date_from,"yyyy-mm-dd","-"));
			$end_dd= strtotime(change_date_format($date_from,"yyyy-mm-dd","-"));
			$start_d=date("Y-m-d",$start_dd);
			$end_d=date("Y-m-d",$end_dd);
			$date_from2 = strtotime($start_d);
			$date_to2 = strtotime($end_d);
			$date_start = date("Y-m-d",strtotime("-5 day", $date_from2));
			$date_end = date("Y-m-d",strtotime("+5 day", $date_to2));
			$date_start= change_date_format($date_start,"yyyy-mm-dd","-",1);
			$date_end= change_date_format($date_end,"yyyy-mm-dd","-",1);

			$second_month_ldate=date("Y-m-t",strtotime($date_to));
			$dateFrom= explode("-",$date_from);
			$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
			$prod_last_day=change_date_format($second_month_ldate,'yyyy-mm-dd');
			$prod_date_upto=" and  f.process_end_date BETWEEN '$fromdate' AND '$prod_last_day' ";
			if($date_search_type==1)
			{
				$dates_com=" and  f.process_end_date BETWEEN '$date_from' AND '$date_to' ";
				$dates_com2=" and  f.process_end_date BETWEEN '$date_start' AND '$date_end' ";
			}
			else
			{
				$dates_com=" and f.insert_date between '".$date_from."' and '".$date_to." 23:59:59' ";
				$dates_com2=" and f.insert_date between '".$date_start."' and '".$date_end." 23:59:59' ";
			}
		}
		elseif($db_type==2)
		{
			$date_from=change_date_format($txt_date_from,'','',1);
			$date_to=change_date_format($txt_date_to,'','',1);

			$start_dd= strtotime(change_date_format($date_from,"dd-mm-yyyy","-"));
			$end_dd= strtotime(change_date_format($date_from,"dd-mm-yyyy","-"));
			$start_d=date("Y-m-d",$start_dd);
			$end_d=date("Y-m-d",$end_dd);
			$date_from2 = strtotime($start_d);
			$date_to2 = strtotime($end_d);
			$date_start = date("Y-m-d",strtotime("-5 day", $date_from2));
			$date_end = date("Y-m-d",strtotime("+5 day", $date_to2));
			$date_start= change_date_format($date_start,"dd-mm-yyyy","-",1);
			$date_end= change_date_format($date_end,"dd-mm-yyyy","-",1);

			$dateFrom= explode("-",$date_from);
			$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
			$second_month_ldate=date("Y-M-t",strtotime($date_to));
			$prod_last_day=change_date_format($second_month_ldate,'','',1);
			$prod_date_upto="and  f.process_end_date BETWEEN '$fromdate' AND '$prod_last_day'";
			if($date_search_type==1)
			{
				$dates_com="and  f.process_end_date BETWEEN '$date_from' AND '$date_to'";
				$dates_com2="and  f.process_end_date BETWEEN '$date_start' AND '$date_end'";
			}
			else
			{
				$dates_com=" and f.insert_date between '".$date_from."' and '".$date_to." 11:59:59 PM' ";
				$dates_com2=" and f.insert_date between '".$date_start."' and '".$date_end." 11:59:59 PM' ";
			}
		}
	}

	$group_by=str_replace("'",'',$cbo_group_by);
	if($group_by==1)
	{
		$order_by="order by f.floor_id";
		$order_by2="order by floor_id";
	}
	else if($group_by==2)
	{
		$order_by="order by f.shift_name";
		$order_b2y="order by shift_name";
	}
	else if($group_by==3)
	{
		$order_by="order by f.machine_id";
		$order_by2="order by machine_id";
	}
	else
	{
		$order_by="order by f.process_end_date,f.machine_id";
		$order_by2="order by process_end_date,machine_id";
	}

		$machine_result=sql_select("select id,prod_capacity,$field_concat from  lib_machine_name where status_active=1 and  category_id = 2 $floor_id_cond2 order by seq_no ");
		//echo "select id,prod_capacity,$field_concat from  lib_machine_name where status_active=1 and  category_id = 2 $floor_id_cond2 order by seq_no ";
		foreach($machine_result as $row)
		{
			$machine_arr[$row[csf('id')]]=$row[csf('machine_name')];
			$machine_capacity_arr[$row[csf('id')]]=$row[csf('prod_capacity')];
			$total_machine_arr[$row[csf('id')]]=$row[csf('id')];
		}
		
	if($cbo_type==1)
	{
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
		{

			 $sql="select a.id,a.sales_order_id,a.company_id,a.batch_no,a.batch_date,a.batch_weight,a.color_id,null as color_range_id, a.booking_no_id, a.extention_no,a.batch_against,a.total_trims_weight, SUM(b.batch_qnty) AS batch_qnty, b.program_no,b.item_description, b.po_id,b.prod_id,b.body_part_id,b.width_dia_type, d.job_no_prefix_num as fso_no, d.job_no  as sales_no, d.po_job_no,d.sales_booking_no, d.style_ref_no, d.within_group, d.buyer_id,d.po_buyer, f.process_end_date, f.process_end_date as production_date, f.end_hours,f.end_minutes,f.machine_id,f.shift_name,f.responsibility_id, f.floor_id,f.multi_batch_load_id,f.system_no, f.remarks,f.process_id from pro_batch_create_mst a,pro_batch_create_dtls b,fabric_sales_order_mst d, pro_fab_subprocess f where  a.id=b.mst_id and f.batch_id=a.id and f.batch_id=b.mst_id and a.is_sales=1 and b.is_sales=1 and b.po_id=d.id $companyCond $workingCompany_name_cond2 $dates_com $po_job_cond $fsodata $batch_num $buyerdata $year_cond  $machine_cond $floor_id_cond $cbo_prod_type_cond and a.entry_form=0  and f.entry_form=35 and f.load_unload_id=1 and a.batch_against in(1,2,3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $batchIds_cond $cbo_prod_type_cond $cbo_prod_source_cond $hidden_booking_cond GROUP BY  a.id,f.shift_name,f.floor_id,f.multi_batch_load_id,a.sales_order_id,a.company_id,a.batch_no,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id, a.booking_no_id, a.extention_no, a.batch_against,b.program_no,b.item_description, b.po_id,b.body_part_id,b.prod_id, b.width_dia_type ,d.po_job_no,d.sales_booking_no, d.job_no, d.style_ref_no, d.job_no_prefix_num, d.within_group, d.buyer_id, d.po_buyer, f.process_end_date,f.system_no,f.responsibility_id, f.end_hours, f.end_minutes, f.machine_id, f.remarks,f.process_id $order_by";

		}
	}
	else if($cbo_type==2)
	{
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
		{

		 $sql = "select f.insert_date, a.batch_no, a.batch_weight, a.id as batch_id,a.sales_order_id, a.batch_against,a.color_id, a.color_range_id, a.extention_no, a.total_trims_weight, (b.batch_qnty) as batch_qnty, b.program_no,b.item_description, b.po_id, b.prod_id,b.body_part_id, b.width_dia_type, d.job_no_prefix_num, d.buyer_id, d.po_buyer, f.remarks, f.shift_name, f.production_date as process_end_date, f.process_end_date as production_date, f.end_hours,f.system_no, f.floor_id,f.multi_batch_load_id, f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id, f.fabric_type,f.responsibility_id, f.result, a.booking_no, d.booking_without_order, d.season, d.job_no  as sales_no,d.style_ref_no , b.barcode_no, f.process_id, f.ltb_btb_id, d.within_group, d.booking_id 

		
			from pro_batch_create_dtls b, fabric_sales_order_mst d, pro_fab_subprocess f, pro_batch_create_mst a 
			where f.batch_id=a.id 
			$companyCond $workingCompany_name_cond2  $dates_com $po_job_cond $fsodata $buyerdata $batch_num $year_cond $result_name_cond $shift_name_cond $machine_cond $floor_id_cond $cbo_prod_type_cond $cbo_prod_source_cond $hidden_booking_cond

			and a.entry_form=0  and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,11,2,3) and b.po_id=d.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.is_sales=1 and b.is_sales=1
			$order_by";
		}
		
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
		{
			 $sql_subcon="select f.insert_date, a.batch_no, a.batch_weight,a.batch_against, a.id as batch_id, a.color_id, a.color_range_id, a.extention_no, a.total_trims_weight, b.batch_qnty AS sub_batch_qnty, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.subcon_job as job_no_prefix_num, d.party_id as buyer_id, d.party_id as po_buyer, 

			 f.remarks, f.shift_name, f.production_date as process_end_date,f.system_no,f.responsibility_id, f.process_end_date as production_date, f.end_hours, f.floor_id,f.multi_batch_load_id, f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id, f.fabric_type, f.result, 0 as season, c.cust_style_ref as style_ref_no, 0 as barcode_no, f.process_id, f.ltb_btb_id, 0 as within_group, 0 as booking_id 

			
			
			from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a, pro_fab_subprocess f
			
			where f.batch_id=a.id and f.batch_id=b.mst_id $subcon_working_company_cond $subcon_companyCond and a.entry_form=36 and a.id=b.mst_id and f.entry_form=38 and f.load_unload_id=2 and a.batch_against in(1,11,2,3) and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $dates_com $sub_job_cond $batch_num $fsodata $sub_buyer_cond $suborder_no $result_name_cond $year_cond $shift_name_cond $machine_cond $floor_id_cond $cbo_prod_type_cond $cbo_prod_source_cond $hidden_booking_cond $order_by";
		}
	}	

	//echo $sql; 

	if($cbo_type==2) 
	{
		/*$sql_booking_type="SELECT booking_no, booking_type, is_short,1 as with_order_status from wo_booking_mst where status_active=1 and is_deleted=0
		union all select booking_no, booking_type, is_short, 2 as with_order_status from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0";
		$sql_booking_type_data=sql_select($sql_booking_type);
		foreach ($sql_booking_type_data as $value) 
		{
			if($value[csf("booking_type")]==1 && $value[csf("is_short")]==2) $booking_type_arr[$value[csf("booking_no")]]="Main";
			else if($value[csf("booking_type")]==1 && $value[csf("is_short")]==1) $booking_type_arr[$value[csf("booking_no")]]="Short";
			else if($value[csf("booking_type")]==4) $booking_type_arr[$value[csf("booking_no")]]="Sample";
		}*/

		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
		{
			//echo $sql;
			$batchdata=sql_select($sql);
			foreach($batchdata as $row)
			{
				$all_prog_idArr[$row[csf("program_no")]]=$row[csf("program_no")];
				$all_batch[$row[csf("batch_id")]]=$row[csf("batch_id")];
				$all_barcode[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
				$sales_order_idArr[$row[csf("sales_order_id")]]=$row[csf("sales_order_id")];
				if($row[csf("booking_id")]!="") $all_booking_id[$row[csf("booking_id")]]=$row[csf("booking_id")];
			}
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

			$batchCond = $dyeing_batch_id_cond = "";  $batchCond2 = $all_batch_no_cond2 = ""; 
			$all_batch_arr=explode(",",$all_batch_ids);
			if($db_type==2 && count($all_batch_arr)>999)
			{
				$all_batch_chunk=array_chunk($all_batch_arr,999) ;
				foreach($all_batch_chunk as $chunk_arr)
				{
					$batchCond.=" f.batch_id in(".implode(",",$chunk_arr).") or ";	
					$batchCond2.=" a.batch_id in(".implode(",",$chunk_arr).") or ";	
				}
				$dyeing_batch_id_cond.=" and (".chop($batchCond,'or ').")";			
				$all_batch_no_cond2.=" and (".chop($batchCond2,'or ').")";			
			}
			else
			{ 	
				$dyeing_batch_id_cond=" and f.batch_id in($all_batch_ids)";
				$all_batch_no_cond2=" and a.batch_id in($all_batch_ids)";
			}
			//echo $dyeing_batch_id_cond.'DD';
			//=======================
			//if($all_batch_ids!="") $dyeing_batch_id_cond= "and f.batch_id in($all_batch_ids)"; else $dyeing_batch_id_cond="";
			
			$sales_cond_for_in=where_con_using_array($sales_order_idArr,0,"a.id");
			$req_batch_cond_for_in=where_con_using_array($all_batch_arr,0,"a.batch_id");
			$progId_batch_cond_for_in=where_con_using_array($all_prog_idArr,0,"b.id");
			
			//$dia_type_arr = return_library_array("select a.id, a.width_dia_type from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and b.po_id in($po_ids)", 'id', 'width_dia_type');
			$sql_prog="SELECT b.id as program_id, b.id as program_no,a.color_type_id FROM ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b WHERE a.dtls_id=b.id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.is_sales=1 $progId_batch_cond_for_in";
			$result_prog=sql_select($sql_prog);
			foreach ($result_prog as $row) 
			{
				$prog_sales_arr[$row[csf("program_id")]]= $row[csf("color_type_id")];
			}
			//print_r($prog_sales_arr);
			
			
			$sql_sales= sql_select("select a.id as sales_id, c.id as batch_id, b.body_part_id,b.color_type_id, c.sales_order_no,c.sales_order_id
				from  fabric_sales_order_mst a, fabric_sales_order_dtls b,pro_batch_create_mst c
				where a.id = b.mst_id and c.sales_order_id = a.id  and a.status_active = 1 and b.status_active=1 $sales_cond_for_in"); // and a.batch_id in ($all_batch_ids)
			foreach ($sql_sales as $row) 
			{
				$sales_batch_arr[$row[csf("batch_id")]][$row[csf("sales_order_no")]][$row[csf("body_part_id")]]= $row[csf("color_type_id")];
				$sales_batch_arr2[$row[csf("batch_id")]][$row[csf("sales_id")]][$row[csf("body_part_id")]]= $row[csf("color_type_id")];
				//$batch_product_arr2[$val[csf("batch_id")]]["batch_prod_tot"] += $val[csf("production_qty")];
			//	$batch_product_Addingarr[$val[csf("batch_id")]][$val[csf("prod_id")]]["batch_prod_tot"] += $val[csf("production_qty")];
			}
			$sql_prod_sales= sql_select("select b.id as dtls_id,a.id,a.batch_id,a.multi_batch_load_id,a.load_unload_id, b.prod_id, b.const_composition, (b.batch_qty) as batch_qty, (b.production_qty) as production_qty,c.body_part_id,c.po_id as sales_id,c.program_no
				from  pro_fab_subprocess a, pro_fab_subprocess_dtls b,pro_batch_create_dtls c
				where a.id = b.mst_id and c.mst_id=a.batch_id and b.prod_id=c.prod_id and a.load_unload_id in(2) and b.load_unload_id in(2) and b.entry_page=35 and a.status_active = 1 and b.status_active=1 $all_batch_no_cond2");
				 
				
			foreach ($sql_prod_sales as $row) 
			{
				 $program_no=$row[csf("program_no")];
			if($dtlsChkArr[$row[csf("dtls_id")]]=='')
			{
			$color_typeId=$prog_sales_arr[$program_no];
			//$color_typeId=$sales_batch_arr2[$row[csf("batch_id")]][$row[csf("sales_id")]][$row[csf("body_part_id")]];
			//$sales_batch_colortype_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]= $color_typeId;
			$dtlsChkArr[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
			}
			$sales_batch_product_arr[$row[csf("batch_id")]][$color_typeId] += $row[csf("production_qty")];
			$prog_colorType_arr[$row[csf("batch_id")]][$row[csf("prod_id")]] = $program_no;
			}
			//print_r($sales_batch_product_arr);
			unset($sql_prod_sales);
			

			$sql_prod_ref= sql_select("select a.id,a.batch_id,a.multi_batch_load_id,a.load_unload_id, b.prod_id, b.const_composition, (b.batch_qty) as batch_qty, (b.production_qty) as production_qty
				from  pro_fab_subprocess a, pro_fab_subprocess_dtls b
				where a.id = b.mst_id and a.load_unload_id in(1,2) and b.load_unload_id in(1,2) and b.entry_page=35 and a.status_active = 1 and b.status_active=1 $all_batch_no_cond2"); // and a.batch_id in ($all_batch_ids)
				//echo "select a.id,a.batch_id, b.prod_id, b.const_composition, (b.batch_qty) as batch_qty, (b.production_qty) as production_qty
			//	from  pro_fab_subprocess a, pro_fab_subprocess_dtls b
				//where a.id = b.mst_id and a.load_unload_id = 2 and b.load_unload_id=2 and b.entry_page=35 and a.status_active = 1 and b.status_active=1 $all_batch_no_cond2";

			foreach ($sql_prod_ref as $val) 
			{
				if($val[csf("load_unload_id")]==2)
				{
					$program_no=$prog_colorType_arr[$val[csf("batch_id")]][$val[csf("prod_id")]];
					$colortype=$prog_sales_arr[$program_no];
					//$colortype=$sales_batch_colortype_arr[$val[csf("batch_id")]][$val[csf("prod_id")]];
					//echo $colortype.'X,';
					$batch_product_arr[$val[csf("batch_id")]][$val[csf("prod_id")]] += $val[csf("production_qty")];
					$batch_product_arr2[$val[csf("batch_id")]]["batch_prod_tot"] += $val[csf("production_qty")];
					$batch_product_arr3[$val[csf("batch_id")]][$val[csf("prod_id")]][$colortype] += $val[csf("production_qty")];
				}
				else
				{
					$multi_batch_arr[$val[csf("batch_id")]]= $val[csf("multi_batch_load_id")];
				}
			//	$batch_product_Addingarr[$val[csf("batch_id")]][$val[csf("prod_id")]]["batch_prod_tot"] += $val[csf("production_qty")];
			}
		//print_r($batch_product_arr3);
			//sales_order_idArr
				
			
				
			
			//print_r($sales_batch_product_arr);
			//print_r($sales_batch_arr);
		 $sql_dyes = "SELECT a.id as recipe_id,a.batch_id,d.item_category_id,c.ratio from pro_recipe_entry_mst a, dyes_chem_issue_requ_dtls c,dyes_chem_requ_recipe_att b,product_details_master d where b.recipe_id=a.id and c.mst_id=b.mst_id and d.id=c.product_id and d.item_category_id in(6)  and a.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.status_active=1 $req_batch_cond_for_in";
			// echo $sql;die();
			$dyes_res = sql_select($sql_dyes);
			 
			foreach ($dyes_res as $row) 
			{
				////if($req_batch_ckhArr[$row[csf('batch_id')]][$row[csf('recipe_id')]]=='')
				//{
				$batch_item_ratio_dyes_arr[$row[csf('batch_id')]]['ratio_total']+= $row[csf('ratio')];
				//$req_batch_ckhArr[$row[csf('batch_id')]][$row[csf('recipe_id')]]=$row[csf('batch_id')];
				//}
			}
			unset($dyes_res);
		//	print_r($batch_item_ratio_dyes_arr);
		
			

			$add_tp_stri_batch_sql=sql_select("select  a.entry_form,a.batch_id, a.dyeing_re_process,b.prod_id,b.ratio,c.item_category_id from pro_recipe_entry_mst a,pro_recipe_entry_dtls b,product_details_master c where a.id=b.mst_id and c.id=b.prod_id   and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.item_category_id in(5,7,6) $all_batch_no_cond2 ");
			 
			//echo "select  a.entry_form,a.batch_id, a.dyeing_re_process,b.prod_id,b.ratio,c.item_category_id from pro_recipe_entry_mst a,pro_recipe_entry_dtls b,product_details_master c where a.id=b.mst_id and c.id=b.prod_id   and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.item_category_id in(5,7,6) $all_batch_no_cond2 ";
			 
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
				$batch_item_ratio_total_arr[$val[csf('batch_id')]][$val[csf('item_category_id')]]['ratio_total'] += $val[csf('ratio')];
				$batch_item_ratio_total_arr[$val[csf('batch_id')]][$val[csf('item_category_id')]]['ratio_count'] += 1;
				$batchTotal_arr[$val[csf('batch_id')]]['batch_ratio_total']  += $val[csf('ratio')];
			}
			//print_r($category_add_tp_stri_batch_arr);
			unset($add_tp_stri_batch_sql);

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
				$yarn_lot_data=sql_select("select b.barcode_no, a.prod_id,c.
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
			$total_color_wise_batch_qty=$tot_machine_capacity=0;$total_stripping_qnty=0;$total_color_batch_qty=$total_others_color_batch_qty=0;
			$total_dyes_chemical_adding_qnty=$total_chemical_adding_qnty=$total_dyes_adding_qnty=0;$total_redying_batch_qty=$total_rewash_batch_qty=0;
			$shade_total_color_batch_qty=$shade_total_others_color_batch_qty=0;
			$dyes_cat_arr=6;$chemical_cat_arr=array(5,7);
			foreach($batchdata as $row)
			{
				$program_no=$row[csf("program_no")];
				//$color_typeId=$sales_batch_arr[$row[csf("batch_id")]][$row[csf("sales_no")]][$row[csf("body_part_id")]];
				$color_typeId=$prog_sales_arr[$program_no];
				//echo 	$color_typeId.'X';
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["insert_date"] = $row[csf("insert_date")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["batch_no"] = $row[csf("batch_no")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["system_no"] = $row[csf("system_no")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["batch_weight"] = $row[csf("batch_weight")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["color_id"] = $row[csf("color_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["color_type"] = $color_typeId;
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["color_range_id"] = $row[csf("color_range_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["extention_no"] = $row[csf("extention_no")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["total_trims_weight"] = $row[csf("total_trims_weight")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["batch_qnty"] += $row[csf("batch_qnty")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["item_description"] = $row[csf("item_description")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["po_id"] = $row[csf("po_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["body_part"] = $body_part[$row[csf("body_part_id")]];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["width_dia_type"] = $row[csf("width_dia_type")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["job_no_prefix_num"] = $row[csf("job_no_prefix_num")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["remarks"] = $row[csf("remarks")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["shift_name"] = $row[csf("shift_name")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["within_group"] = $row[csf("within_group")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["po_buyer"] = $row[csf("po_buyer")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["buyer_id"] = $row[csf("buyer_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["process_end_date"] = $row[csf("process_end_date")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["production_date"] = $row[csf("production_date")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["production_qnty"] = $batch_product_arr[$row[csf("batch_id")]][$row[csf("prod_id")]];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["prod_qnty_tot"] = $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["end_hours"] = $row[csf("end_hours")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["floor_id"] = $row[csf("floor_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["hour_unload_meter"] = $row[csf("hour_unload_meter")]; 
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["water_flow_meter"] = $row[csf("water_flow_meter")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["end_minutes"] = $row[csf("end_minutes")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["machine_id"] = $row[csf("machine_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["load_unload_id"] = $row[csf("load_unload_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["fabric_type"] = $row[csf("fabric_type")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["result"] = $row[csf("result")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["booking_no"] = $row[csf("booking_no")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["sales_no"] = $row[csf("sales_no")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["booking_without_order"] = $row[csf("booking_without_order")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["season"] = $row[csf("season")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["style_ref_no"] = $row[csf("style_ref_no")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["barcode_no"][] = $row[csf("barcode_no")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["process_id"] = $row[csf("process_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["ltb_btb_id"] = $row[csf("ltb_btb_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["multi_batch_load_id"] = $row[csf("multi_batch_load_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["batch_againstId"] = $row[csf("batch_against")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["add_tp_strip"] = $add_tp_stri_batch_arr[$row[csf("batch_id")]];
				
				$ttl_batch_prod_tot_arr[$row[csf("batch_id")]]['prod_qty']=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
				$ttl_batch_prod_tot_arr[$row[csf("batch_id")]]['trims']=$row[csf("total_trims_weight")];
				
				$machine_id_arr[$row[csf("batch_id")]]['machine_id']=$row[csf("machine_id")];
				$multi_batch_load_id=$multi_batch_arr[$row[csf("batch_id")]];
				//echo $row[csf("batch_against")].'dsd';
				if($row[csf("extention_no")]=='') $row[csf("extention_no")]=0;
				
				$batch_againstId=$row[csf("batch_against")];
				if($row[csf("process_id")]==147) //Re dyeing
				{
					if($dyingchkBatch[$row[csf("batch_id")]] =="")
						{
							$total_redying_batch_qty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
							$dyingchkBatch[$row[csf("batch_id")]] =$row[csf("batch_id")];
						}
				}
				if($row[csf("process_id")]==148) //Re Wash...
				{
					if($dyinWgchkBatch[$row[csf("batch_id")]] =="")
						{
							$total_rewash_batch_qty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
							$dyinWgchkBatch[$row[csf("batch_id")]] =$row[csf("batch_id")];
						}
				}
				if($row[csf("process_id")]==294) //Stripping...
				{
					if($dyinStripchkBatch[$row[csf("batch_id")]] =="")
						{
							$total_stripping_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
							$dyinStripchkBatch[$row[csf("batch_id")]] =$row[csf("batch_id")];
						}
				}
				//294
				if($row[csf('machine_id')] != 0) //$multi_batch_load_id==1 &&
				{
						if($multichkBatch[$row[csf("system_no")]] =="")
						{
						$tot_process_end_date_arr[$row[csf('system_no')]]= $row[csf('system_no')];
						$multichkBatch[$row[csf("system_no")]]=$row[csf("system_no")];
						}
					//$tot_process_end_date_arr[$row[csf('system_no')]]= $row[csf('system_no')];
					$total_running_machine_arr[$row[csf('machine_id')]]=$row[csf('machine_id')];
					//echo $machine_capacity_arr[$row[csf('machine_id')]].'d';
					$tot_machine_capacity_arr[$row[csf('batch_id')]]= $machine_capacity_arr[$row[csf('machine_id')]];
				}
				
				//if($row[csf('result')]!= 4 && $row[csf('extention_no')]=="")
				//{
				/*$color_wise_batch_arr[$row[csf("color_range_id")]]["batch_qnty"]= $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"]+$row[csf("total_trims_weight")];
				$color_wise_batch_arr[$row[csf("color_range_id")]]["total_trims_weight"]= $row[csf("total_trims_weight")];
				$total_color_wise_batch_qty+= $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"]+$row[csf("total_trims_weight")];;
				$color_wise_batch_arr[$row[csf("color_range_id")]]["batch_ids"].=$row[csf("batch_id")].',';*/
				//}

				//==========Section wise =============
				if($row[csf("extention_no")]>0 && $row[csf("responsibility_id")]>0)
				{
					if($sectionBatchChkArr[$row[csf("batch_id")]] =="")
					{
					
					$section_wise_batch_qnty_arr[$row[csf("responsibility_id")]]=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$sectionBatchChkArr[$row[csf("batch_id")]]=$row[csf("batch_id")];
					$section_wise_batch_qnty_total+=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];;
					}
				}
				
				

				//Color Batch qty
				if($row[csf("extention_no")]=='') $row[csf("extention_no")]=0;
				
				//echo $row[csf("extention_no")].'='.$row[csf("result")].'<br>';
				if($row[csf("result")]==1)
				{
					if($color_typeId==1 || $color_typeId==5) //Solid ,AOP
					{
						
						if($colorchkBatch[$row[csf("batch_id")]][$color_typeId] =="")
						{
							
							if($colorchkBatchTrim[$row[csf("batch_id")]] =="")
							{
								$total_trims_weight==0;
								$total_trims_weight=$row[csf("total_trims_weight")];
								$colorchkBatchTrim[$row[csf("batch_id")]] =$row[csf("batch_id")];
								//$total_color_batch_qty+=$total_trims_weight;
								
							}
							//echo $total_trims_weight.'T,';
							$batch_prod_tot=$batch_product_arr3[$row[csf("batch_id")]][$row[csf("prod_id")]][$color_typeId];
							//echo $batch_prod_tot.'D,';
							$total_color_batch_qty += $batch_prod_tot ;
							//$total_color_batch_qty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
							$colorchkBatch[$row[csf("batch_id")]][$color_typeId] =$row[csf("batch_id")];
						}
						
					}
					else
					{
						if($colorchkBatch2[$row[csf("batch_id")]][$color_typeId] =="")
						{
							if($colorchkBatchTrim2[$row[csf("batch_id")]] =="")
							{
								$trims_weight==0;
								$trims_weight=$row[csf("total_trims_weight")];
								$colorchkBatchTrim2[$row[csf("batch_id")]] =$row[csf("batch_id")];
								//$total_others_color_batch_qty+=$trims_weight;
								
							}
							
							$batch_prod_tot=$batch_product_arr3[$row[csf("batch_id")]][$row[csf("prod_id")]][$color_typeId];
							$total_others_color_batch_qty += $batch_prod_tot;
							
							$colorchkBatch2[$row[csf("batch_id")]][$color_typeId] =$row[csf("batch_id")];
						}
						//if($colorchkBatch2[$row[csf("batch_id")]] =="")
						//{
							//$batch_prod_tot=$sales_batch_product_arr[$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("body_part_id")]][$color_typeId];
							//$total_others_color_batch_qty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
							
							//$colorchkBatch2[$row[csf("batch_id")]] =$row[csf("batch_id")];
						//}
					}
				}
				//Color without Reprocess Batch qty
				//echo $row[csf("extention_no")].'='.$row[csf("result")].',';
				if($row[csf("result")]==1)
				{
					if($color_typeId==1 || $color_typeId==5)
					{
						//$batch_prod_tot=$sales_batch_product_arr[$row[csf("batch_id")]][$color_typeId];
						$batch_prod_tot=$batch_product_arr3[$row[csf("batch_id")]][$row[csf("prod_id")]][$color_typeId];
						//echo $batch_prod_tot.'d'.$color_typeId.', ';
						if($colorchkBatchColorType[$row[csf("batch_id")]][$color_typeId] =="")
						{
							//$shade_total_color_batch_qty+= $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
							$shade_total_color_batch_qty+= $batch_prod_tot;
							$colorchkBatchColorType[$row[csf("batch_id")]][$color_typeId] =$row[csf("batch_id")];
						}
						
					}
					else
					{
						if($colorchkBatchColorType2[$row[csf("batch_id")]][$color_typeId] =="")
						{
							//$batch_prod_tot=$sales_batch_product_arr[$row[csf("batch_id")]][$color_typeId];
							$batch_prod_tot=$batch_product_arr3[$row[csf("batch_id")]][$row[csf("prod_id")]][$color_typeId];
							//$shade_total_others_color_batch_qty+= $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
							$shade_total_others_color_batch_qty+= $batch_prod_tot;
							$colorchkBatchColorType2[$row[csf("batch_id")]][$color_typeId] =$row[csf("batch_id")];
						}
					}
				}
				
		
				if($batch_product_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]>0)
				{
					$production_date_count[$row[csf("production_date")]] = $row[csf("production_date")];
				}
				
				//re-process
				if($row[csf("extention_no")]>0 && $row[csf("result")]==1)
				{
					if($chkBatch[$row[csf("batch_id")]] =="")
					{
							//$total_reprocess_qty += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
						$total_reprocess_qty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch[$row[csf("batch_id")]] =$row[csf("batch_id")];
					}
				}

				//adding
				if($add_tp_stri_batch_arr[$row[csf("batch_id")]]==2)
				{
					$all_category_id=rtrim($all_categry_add_tp_stri_batch_arr[$row[csf("batch_id")]],',');
					$all_category_Arr=array_unique(explode(",",$all_category_id));
					//print_r($all_category_Arr);
				//	$all_categry_add_tp_stri_batch_arr[$val[csf("batch_id")]]
					
					$dyes_category_id=$dyes_add_tp_stri_batch_arr[$row[csf("batch_id")]];
					$chemical_category_id=$chemical_add_tp_stri_batch_arr[$row[csf("batch_id")]];
					$dyes_chemical_category_id=$dyes_chemical_add_tp_stri_batch_arr[$row[csf("batch_id")]];
					//echo $all_category_id.'<br>';
					if($chkBatch_1[$row[csf("batch_id")]] =="")
					{
						//$total_adding_qnty += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
						//$dyes_cat_arr=array(6);$chemical_cat_arr=array(5,7);
						$chemical_arr_diff=array_diff($all_category_Arr,$chemical_cat_arr);
						//print_r($arr_diff);
						if(count($all_category_Arr)>=2 && ($chemical_arr_diff))
						{
							//echo $all_category_id.'k';
							$total_dyes_chemical_adding_qnty +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						}
						else if(in_array(6,$all_category_Arr)) //====Dyes =6
						{
							$total_dyes_adding_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];//$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						}
						else if(in_array(5,$all_category_Arr) || in_array(7,$all_category_Arr)) //Dyes ,//Chemical =5,7 
						{
							$total_chemical_adding_qnty +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];//$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						}
						
						$total_adding_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch_1[$row[csf("batch_id")]] =$row[csf("batch_id")];
						//echo 'X,';
						 
					}
				}
				/*if($row[csf("extention_no")]>0 && $add_tp_stri_batch_arr[$row[csf("batch_id")]]==3) //Stripping
				{
					if($chkBatch_11[$row[csf("batch_id")]] =="")
					{
						
						$total_stripping_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch_11[$row[csf("batch_id")]] =$row[csf("batch_id")];
					}
				}*/
				if($row[csf("extention_no")]>0 && $add_tp_stri_batch_arr[$row[csf("batch_id")]]==1) //Topping
				{
					if($chkBatch_12[$row[csf("batch_id")]] =="")
					{
						
						$total_topping_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch_12[$row[csf("batch_id")]] =$row[csf("batch_id")];
					}
				}

				//rft
				if($row[csf("extention_no")]==0 && $row[csf("result")]==1 && $add_tp_stri_batch_arr[$row[csf("batch_id")]]=="")
				{
					if($chkBatch_2[$row[csf("batch_id")]] =="")
					{
					//echo 'B,';
						//$total_rft_qnty += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
						//$total_rft_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						//$chkBatch_2[$row[csf("batch_id")]] =$row[csf("batch_id")];
					}
				}
				//echo  $batch_againstId.'='.$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")].'<br>';
				if($chkBatch_444[$row[csf("batch_id")]] =="" && $batch_againstId!=2)
				{
					$without_fabric_type_production_arr[$row[csf("fabric_type")]] += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					
					$without_fabric_type_production_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$chkBatch_444[$row[csf("batch_id")]] =$row[csf("batch_id")];
				}
				//if($chkBatch_3[$row[csf("batch_id")]] =="")
				if($chkBatch_3[$row[csf("batch_id")]] =="" && $row[csf("result")]==1 && $row[csf("extention_no")]>0) 
				{
					//$fabric_type_production_arr[$row[csf("fabric_type")]] += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
					//$fabric_type_production_total +=$batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
					$fabric_type_production_arr[$row[csf("fabric_type")]] += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$fabric_type_production_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$chkBatch_3[$row[csf("batch_id")]] =$row[csf("batch_id")];
				}

				if($row[csf("extention_no")]>0 && $row[csf("result")]==1 && $chkBatch_6[$row[csf("batch_id")]] =="")
				{
					$chkBatch_6[$row[csf("batch_id")]] =$row[csf("batch_id")];
					if($row[csf("within_group")]==1)
					{
						$buyer_wise_summary[$row[csf("po_buyer")]] += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					}
					else{
						$buyer_wise_summary[$row[csf("buyer_id")]] += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					}
					$buyer_wise_summary_batch_total += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];

					$dyeing_process_wise_batch_qnty_arr[$row[csf("process_id")]] +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$dyeing_process_wise_batch_qnty_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
				}

				//shade Matched
				if($row[csf("result")]==1) 
				{
					if($row[csf("within_group")]==1)
					{
						if($booking_type_arr[$row[csf("booking_no")]] == "Main" || $booking_type_arr[$row[csf("booking_no")]] == "Short")
						{
							//$shade_matched_wise_summary[$row[csf("po_buyer")]]["self"]+=$row[csf("batch_qnty")];
							//$shade_matched_buyer_total +=$row[csf("batch_qnty")];
							//echo $row[csf("extention_no")].'DDD';
							if($row[csf("extention_no")]<1){
								//echo $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"].'AA';
								if($chkBatch_41[$row[csf("batch_id")]] == ""){
									$chkBatch_41[$row[csf("batch_id")]] =$row[csf("batch_id")];
										
									$shade_matched_wise_summary[$row[csf("po_buyer")]]["self"]+=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
									$shade_matched_buyer_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];

									$shade_matched_wise_summary[$row[csf("po_buyer")]]["trim"]+=$row[csf("total_trims_weight")];
									$shade_matched_buyer_total +=$row[csf("total_trims_weight")];
								}
							}
						}
						else if($booking_type_arr[$row[csf("booking_no")]]=="Sample")
						{
							if($row[csf("booking_without_order")]==1)
							{
								//$shade_matched_wise_summary[$row[csf("po_buyer")]]["smn"]+=$row[csf("batch_qnty")];
								//$shade_matched_buyer_total +=$row[csf("batch_qnty")];
								if($row[csf("extention_no")]<1){
									if($chkBatch_42[$row[csf("batch_id")]] == ""){
										$chkBatch_42[$row[csf("batch_id")]] =$row[csf("batch_id")];

										$shade_matched_wise_summary[$row[csf("po_buyer")]]["smn"]+=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
										$shade_matched_buyer_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];

										$shade_matched_wise_summary[$row[csf("po_buyer")]]["trim"]+=$row[csf("total_trims_weight")];
										$shade_matched_buyer_total +=$row[csf("total_trims_weight")];
									}
								}
							}
							else
							{
								//$shade_matched_wise_summary[$row[csf("po_buyer")]]["sm"]+=$row[csf("batch_qnty")];
								//$shade_matched_buyer_total +=$row[csf("batch_qnty")];
								if($row[csf("extention_no")]<1){
									if($chkBatch_43[$row[csf("batch_id")]] == ""){
										$chkBatch_43[$row[csf("batch_id")]] =$row[csf("batch_id")];

										$shade_matched_wise_summary[$row[csf("po_buyer")]]["sm"]+=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
										$shade_matched_buyer_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];

										$shade_matched_wise_summary[$row[csf("po_buyer")]]["trim"]+=$row[csf("total_trims_weight")];
										$shade_matched_buyer_total +=$row[csf("total_trims_weight")];
									}
								}
							}
						}
					}
					else
					{
						//$shade_matched_wise_summary[$row[csf("buyer_id")]]["self"]+=$row[csf("batch_qnty")];
						//$shade_matched_buyer_total +=$row[csf("batch_qnty")];
						if($row[csf("extention_no")]<1){
							if($chkBatch_44[$row[csf("batch_id")]] == ""){
								$chkBatch_44[$row[csf("batch_id")]] =$row[csf("batch_id")];

								$shade_matched_wise_summary[$row[csf("buyer_id")]]["self"]+=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
								$shade_matched_buyer_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];

								$shade_matched_wise_summary[$row[csf("buyer_id")]]["trim"]+=$row[csf("total_trims_weight")];
								$shade_matched_buyer_total +=$row[csf("total_trims_weight")];
							}
						}
					}
				}
				else
				{
					if($row[csf("within_group")]==1)
					{
						if($booking_type_arr[$row[csf("booking_no")]] == "Main" || $booking_type_arr[$row[csf("booking_no")]] == "Short")
						{
							//$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["self"]+=$row[csf("batch_qnty")];
							//$shade_not_matched_buyer_total +=$row[csf("batch_qnty")];
							if($chkBatch_4[$row[csf("batch_id")]] == ""){
								$chkBatch_4[$row[csf("batch_id")]] =$row[csf("batch_id")];

								$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["self"]+=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
								$shade_not_matched_buyer_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];

								$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["trim"]+=$row[csf("total_trims_weight")];
								$shade_not_matched_buyer_total +=$row[csf("total_trims_weight")];
							}
						}
						else if($booking_type_arr[$row[csf("booking_no")]]=="Sample")
						{
							if($row[csf("booking_without_order")]==1)
							{
								//$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["smn"]+=$row[csf("batch_qnty")];
								//$shade_not_matched_buyer_total +=$row[csf("batch_qnty")];
								if($chkBatch_4[$row[csf("batch_id")]] == ""){
									$chkBatch_4[$row[csf("batch_id")]] =$row[csf("batch_id")];

									$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["smn"]+=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
									$shade_not_matched_buyer_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];

									$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["trim"]+=$row[csf("total_trims_weight")];
									$shade_not_matched_buyer_total +=$row[csf("total_trims_weight")];
								}
							}
							else
							{
								//$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["sm"]+=$row[csf("batch_qnty")];
								//$shade_not_matched_buyer_total +=$row[csf("batch_qnty")];
								if($chkBatch_4[$row[csf("batch_id")]] == ""){
									$chkBatch_4[$row[csf("batch_id")]] =$row[csf("batch_id")];

									$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["sm"]+=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
									$shade_not_matched_buyer_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];

									$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["trim"]+=$row[csf("total_trims_weight")];
									$shade_not_matched_buyer_total +=$row[csf("total_trims_weight")];
								}
							}
						}
					}
					else
					{
						//$shade_not_matched_wise_summary[$row[csf("buyer_id")]]["self"]+=$row[csf("batch_qnty")];
						//$shade_not_matched_buyer_total +=$row[csf("batch_qnty")];
						if($chkBatch_4[$row[csf("batch_id")]] == ""){
							$chkBatch_4[$row[csf("batch_id")]] =$row[csf("batch_id")];

							$shade_not_matched_wise_summary[$row[csf("buyer_id")]]["self"]+=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
							$shade_not_matched_buyer_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];

							$shade_not_matched_wise_summary[$row[csf("buyer_id")]]["trim"]+=$row[csf("total_trims_weight")];
							$shade_not_matched_buyer_total +=$row[csf("total_trims_weight")];
						}

					}
				}
				
				//$dyeing_process_wise_batch_qnty_arr[$row[csf("process_id")]] +=$row[csf("batch_qnty")];
				//$dyeing_process_wise_batch_qnty_total +=$row[csf("batch_qnty")];

				//$btb_ltb_count=1;
				 if($chkBatch_5[$row[csf("batch_id")]] == "")
				{
					$chkBatch_5[$row[csf("batch_id")]] =$row[csf("batch_id")];
					if($row[csf("extention_no")]==0 && $add_tp_stri_batch_arr[$row[csf("batch_id")]]=="" && $row[csf("result")] ==1){
						$btb_shade_matched[$row[csf("ltb_btb_id")]]["shade_matched"].=$row[csf("system_no")].',';//+=$btb_ltb_count;
						//echo "Z".$row[csf("ltb_btb_id")];
					}
				//	if($multi_batch_load_id==1)
					//{
					$btb_shade_matched[$row[csf("ltb_btb_id")]]["total_batch"].=$row[csf("system_no")].',';
					//}
				}
			}
			
			//print_r($tot_process_end_date_arr);
			$batch_row_span_arr =array();$ttl_batch_qty=0;$machine_check_array=array();
			$x=1;
			foreach ($data_array_batch as $batch_id => $batch_data_arr) 
			{
				$batch_row_span = 0;
				foreach ($batch_data_arr as $prod_id => $val) 
				{
					$batch_row_span++;
					//$val["prod_qnty_tot"];
					//$ttl_batch_qty+=$val["prod_qnty_tot"];
					 $batch_againstId=$val[("batch_againstId")];
					
					if($btchChkkArr[$batch_id]=='')
					{
						if($batch_againstId==2) //Color Range wise Reprocess 
						{
							$color_wise_batch_arr[$val[("color_range_id")]]["summary_batch_qnty"]+=$ttl_batch_prod_tot_arr[$batch_id]['prod_qty'];
							if($ttl_batch_prod_tot_arr[$batch_id]['trims']>0)
							{
							$color_wise_batch_arr[$val[("color_range_id")]]["total_trims_weight"]+=$ttl_batch_prod_tot_arr[$batch_id]['trims'];
							}
							$color_wise_batch_arr[$val[("color_range_id")]]["batch_ids"].=$batch_id.',';
						}
						else
						{
							$without_reprocess_color_wise_batch_arr[$val[("color_range_id")]]["summary_batch_qnty"]+=$ttl_batch_prod_tot_arr[$batch_id]['prod_qty'];
							if($ttl_batch_prod_tot_arr[$batch_id]['trims']>0)
							{
							$without_reprocess_color_wise_batch_arr[$val[("color_range_id")]]["total_trims_weight"]+=$ttl_batch_prod_tot_arr[$batch_id]['trims'];
							}
							$without_reprocess_color_wise_batch_arr[$val[("color_range_id")]]["batch_ids"].=$batch_id.',';
						}
						$btchChkkArr[$batch_id]=$batch_id;
					}
				
					
				}
				 $multi_batch_load_id=$multi_batch_arr[$batch_id];
				
				//echo $val["total_trims_weight"].'FD';   
				
				
				$total_color_wise_batch_qty+= $ttl_batch_prod_tot_arr[$batch_id]['prod_qty']+$ttl_batch_prod_tot_arr[$batch_id]['trims'];
				
				
				$ttl_batch_qty+=$ttl_batch_prod_tot_arr[$batch_id]['prod_qty']+$ttl_batch_prod_tot_arr[$batch_id]['trims'];
				$batch_row_span_arr[$batch_id] = $batch_row_span;
				
				$machine_id=$machine_id_arr[$batch_id]['machine_id'];
				if (!in_array($machine_id,$machine_check_array))
					{ $x++;
						
						
						 $machine_check_array[]=$machine_id;
						 $machine_capacity=$tot_machine_capacity_arr[$batch_id];
					}
					else
					{
						 $machine_capacity=0;
					}
					
				$tot_machine_capacity+=$machine_capacity;
			}
			//echo $ttl_batch_qty.'SS';	
			//print_r($color_wise_batch_arr);			
		}
		//echo $tot_machine_capacity.'DD';
		
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2 )
		{
				$subbatchdata=sql_select($sql_subcon); $all_sub_batch=array();
				foreach($subbatchdata as $row) 
				{
					$all_sub_batch[$row[csf("batch_id")]]=$row[csf("batch_id")];
				}
			//print_r($all_sub_batch);
			//$all_sub_batch_id= array_unique($all_sub_batch);
			$all_sub_batch_ids=implode(",",$all_sub_batch);
			//if($all_sub_batch_ids=="") { echo "Data Not Found"; die; }
			//====================

			$batchCond =$sub_dyeing_batch_id_cond= "";  $subbatchCond2 = $all_sub_batch_no_cond2 = ""; 
			$all_sub_batch_arr=explode(",",$all_sub_batch_ids);
			//print_r($all_sub_batch_arr);
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
			//if($all_batch_ids!="") $dyeing_batch_id_cond= "and f.batch_id in($all_batch_ids)"; else $dyeing_batch_id_cond="";
			 
			
			 $sub_sql_prod_ref= sql_select("select a.batch_id,a.multi_batch_load_id,a.load_unload_id,b.prod_id,(b.batch_qnty) as production_qty
				from  pro_fab_subprocess a,pro_batch_create_dtls b
				where a.batch_id =b.mst_id and a.load_unload_id in(1,2) and a.load_unload_id in(1,2) and a.entry_form=38 and a.status_active=1 and b.status_active=1 $all_sub_batch_no_cond2 "); // and a.batch_id in ($all_batch_ids)
				
				
			foreach ($sub_sql_prod_ref as $val) 
			{
				if($val[csf("load_unload_id")]==2)
				{
				$batch_product_arr[$val[csf("batch_id")]][$val[csf("prod_id")]] = $val[csf("production_qty")];
				$batch_product_arr2[$val[csf("batch_id")]]["batch_prod_tot"] += $val[csf("production_qty")];
				}
				else
				{
					$multi_batch_arr[$val[csf("batch_id")]] = $val[csf("multi_batch_load_id")];
				}
			}
			

			/*$add_tp_stri_batch_sql=sql_select("select  a.batch_id, a.dyeing_re_process from pro_recipe_entry_mst a where a.entry_form = 60 and a.status_active = 1 and a.is_deleted = 0 $all_batch_no_cond2 group by a.batch_id, a.dyeing_re_process");
			foreach ($add_tp_stri_batch_sql as $val) 
			{
				$add_tp_stri_batch_arr[$val[csf("batch_id")]] = $val[csf("dyeing_re_process")];
			}
			unset($add_tp_stri_batch_sql);*/
			$add_tp_stri_batch_sql=sql_select("select  a.entry_form,a.batch_id, a.dyeing_re_process,b.prod_id,b.ratio,c.item_category_id from pro_recipe_entry_mst a,pro_recipe_entry_dtls b,product_details_master c where a.id=b.mst_id and c.id=b.prod_id   and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.item_category_id in(5,6,7) $all_sub_batch_no_cond2 ");
			 
			 
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
				$batch_item_ratio_total_arr[$val[csf('batch_id')]][$val[csf('item_category_id')]]['ratio_total'] += $val[csf('ratio')];
				$batch_item_ratio_total_arr[$val[csf('batch_id')]][$val[csf('item_category_id')]]['ratio_count'] += 1;
				$batchTotal_arr[$val[csf('batch_id')]]['batch_ratio_total']  += $val[csf('ratio')];
			}
			//print_r($category_add_tp_stri_batch_arr);
			unset($add_tp_stri_batch_sql);
			

			//$yarn_lot_arr=array();
		//	print_r($subbatchdata);
			$subbatch_data_arr=array();
			$sub_total_color_wise_batch_qty=$sub_tot_machine_capacity=0;
			$dyes_cat_arr=6;$chemical_cat_arr=array(5,7);
			foreach($subbatchdata as $row)
			{
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["insert_date"] = $row[csf("insert_date")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["batch_no"] = $row[csf("batch_no")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["system_no"] = $row[csf("system_no")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["batch_weight"] = $row[csf("batch_weight")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["color_id"] = $row[csf("color_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["color_range_id"] = $row[csf("color_range_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["extention_no"] = $row[csf("extention_no")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["total_trims_weight"] = $row[csf("total_trims_weight")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["batch_qnty"] += $row[csf("sub_batch_qnty")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["item_description"] = $row[csf("item_description")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["po_id"] = $row[csf("po_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["width_dia_type"] = $row[csf("width_dia_type")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["job_no_prefix_num"] = $row[csf("job_no_prefix_num")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["remarks"] = $row[csf("remarks")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["shift_name"] = $row[csf("shift_name")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["within_group"] = $row[csf("within_group")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["po_buyer"] = $row[csf("po_buyer")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["buyer_id"] = $row[csf("buyer_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["process_end_date"] = $row[csf("process_end_date")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["production_date"] = $row[csf("production_date")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["production_qnty"] = $row[csf("sub_batch_qnty")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["prod_qnty_tot"] = $sub_batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
				//echo $sub_batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"].'jj';
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["end_hours"] = $row[csf("end_hours")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["floor_id"] = $row[csf("floor_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["hour_unload_meter"] = $row[csf("hour_unload_meter")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["water_flow_meter"] = $row[csf("water_flow_meter")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["end_minutes"] = $row[csf("end_minutes")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["machine_id"] = $row[csf("machine_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["load_unload_id"] = $row[csf("load_unload_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["fabric_type"] = $row[csf("fabric_type")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["result"] = $row[csf("result")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["booking_no"] = $row[csf("booking_no")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["booking_without_order"] = $row[csf("booking_without_order")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["season"] = $row[csf("season")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["style_ref_no"] = $row[csf("style_ref_no")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["barcode_no"][] = $row[csf("barcode_no")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["process_id"] = $row[csf("process_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["ltb_btb_id"] = $row[csf("ltb_btb_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["multi_batch_load_id"] = $row[csf("multi_batch_load_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["batch_againstId"] = $row[csf("batch_against")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["add_tp_strip"] = $add_tp_stri_batch_arr[$row[csf("batch_id")]];
				//echo  $sub_batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"].'BB';
				//echo  $row[csf("sub_batch_qnty")].'X';
				 $multi_batch_load_id=$multi_batch_arr[$row[csf("batch_id")]];
				 $batch_againstId=$row[csf("batch_againstId")];
				$sub_ttl_batch_prod_tot_arr[$row[csf("batch_id")]]["prod_qty"]= $row[csf("sub_batch_qnty")];
				$sub_ttl_batch_prod_tot_arr[$row[csf("batch_id")]]["trims"]=$row[csf("total_trims_weight")];
				
				
				
				//$sub_tot_day_process_end_date.= $row[csf('process_end_date')].',';
				//if($multi_batch_load_id==1)
				//{
						if($multichkBatch[$row[csf("system_no")]] =="")
						{
						$tot_process_end_date_arr[$row[csf('system_no')]]= $row[csf('system_no')];
						$multichkBatch[$row[csf("system_no")]]=$row[csf("system_no")];
						}
				//}
				
				if($row[csf("process_id")]==147) //Re dyeing
				{
						if($dyingchkBatch[$row[csf("batch_id")]] =="")
						{
							$total_redying_batch_qty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
							$dyingchkBatch[$row[csf("batch_id")]] =$row[csf("batch_id")];
						}
				}
				if($row[csf("process_id")]==148) //Re Wash...
				{
					if($dyinWgchkBatch[$row[csf("batch_id")]] =="")
						{
							$total_rewash_batch_qty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
							$dyinWgchkBatch[$row[csf("batch_id")]] =$row[csf("batch_id")];
						}
				}
				if($row[csf("process_id")]==294) //Stripping...
				{
					if($dyinStripchkBatch[$row[csf("batch_id")]] =="")
						{
							$total_stripping_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
							$dyinStripchkBatch[$row[csf("batch_id")]] =$row[csf("batch_id")];
						}
				}
				
				/*if($row[csf('result')]!= 4 && $row[csf('extention_no')]==0)
				{
				$color_wise_batch_arr[$row[csf("color_range_id")]]["batch_qnty"]= $row[csf("sub_batch_qnty")]+$row[csf("total_trims_weight")];
				$color_wise_batch_arr[$row[csf("color_range_id")]]["total_trims_weight"]+= $row[csf("total_trims_weight")];
				$color_wise_batch_arr[$row[csf("color_range_id")]]["batch_ids"].=$row[csf("batch_id")].',';
				$sub_total_color_wise_batch_qty+= $row[csf("sub_batch_qnty")]+$row[csf("total_trims_weight")];
				}
				*/
				if($row[csf('machine_id')] != 0)
				{
					$sub_machine_id_arr[$row[csf("batch_id")]]['machine_id']=$row[csf('machine_id')];
					
					$total_running_machine_arr[$row[csf('machine_id')]]=$row[csf('machine_id')];
					$sub_tot_machine_capacity_arr[$row[csf("batch_id")]]= $machine_capacity_arr[$row[csf('machine_id')]];
				}

				if($batch_product_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]>0)
				{
					$production_date_count[$row[csf("production_date")]] = $row[csf("production_date")];
				}

				//re-process
				if($row[csf("extention_no")]>0 && $row[csf("result")]==1)
				{
					if($chkBatch[$row[csf("batch_id")]] =="")
					{
						//$total_reprocess_qty += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
						$total_reprocess_qty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch[$row[csf("batch_id")]] =$row[csf("batch_id")];
						
					//	echo $row[csf("buyer_id")].'D';
						
						$buyer_wise_summary[$row[csf("buyer_id")]] += $sub_batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						
						$buyer_wise_summary_batch_total += $sub_batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
	
						$dyeing_process_wise_batch_qnty_arr[$row[csf("process_id")]] +=$sub_batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$dyeing_process_wise_batch_qnty_total +=$sub_batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						}
				}
 				
				//adding
				//echo "ASSSS".$add_tp_stri_batch_arr[$row[csf("batch_id")]];
				if($add_tp_stri_batch_arr[$row[csf("batch_id")]]==2)
				{
					
					$all_category_id=$all_categry_add_tp_stri_batch_arr[$row[csf("batch_id")]];
					$all_category_Arr=array_unique(explode(",",$all_category_id));
					
					//$dyes_category_id=$dyes_add_tp_stri_batch_arr[$row[csf("batch_id")]];
				//	$chemical_category_id=$chemical_add_tp_stri_batch_arr[$row[csf("batch_id")]];
					//$dyes_chemical_category_id=$dyes_chemical_add_tp_stri_batch_arr[$row[csf("batch_id")]];
					
					if($chkBatch_1[$row[csf("batch_id")]] =="")
					{
						  $chemical_arr_diff=array_diff($all_category_Arr,$chemical_cat_arr);
							
						/*if($dyes_category_id==6) //Dyes
						{
							$total_dyes_adding_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];//$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						}
						if($chemical_category_id==5 || $chemical_category_id==7) //Chemical
						{
							$total_chemical_adding_qnty +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];//$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						}
						if($dyes_chemical_category_id==23) //Dyes+Chemical
						{
							$total_dyes_chemical_adding_qnty +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];//$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						}*/
						
						if(count($all_category_Arr)>=2 && ($chemical_arr_diff))
						{
							//echo $all_category_id.'k';
							$total_dyes_chemical_adding_qnty +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						}
						else if(in_array(6,$all_category_Arr)) //====Dyes =6
						{
							$total_dyes_adding_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];//$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						}
						else if(in_array(5,$all_category_Arr) || in_array(7,$all_category_Arr)) //Dyes ,//Chemical =5,7 
						{
							$total_chemical_adding_qnty +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];//$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						}
						
						
						$total_adding_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch_1[$row[csf("batch_id")]] =$row[csf("batch_id")];
						//$btb_shade_matched[$row[csf("ltb_btb_id")]]["shade_matched"] ++;
					}
				}
				/*if($row[csf("extention_no")]>0 && $add_tp_stri_batch_arr[$row[csf("batch_id")]]==3) //Stripping
				{
					if($chkBatch_11[$row[csf("batch_id")]] =="")
					{
						
						$total_stripping_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch_11[$row[csf("batch_id")]] =$row[csf("batch_id")];
					}
				}*/
				if($row[csf("extention_no")]>0 && $add_tp_stri_batch_arr[$row[csf("batch_id")]]==1) //Topping
				{
					if($chkBatch_12[$row[csf("batch_id")]] =="")
					{
						
						$total_topping_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch_12[$row[csf("batch_id")]] =$row[csf("batch_id")];
					}
				}


				//rft
				if($row[csf("extention_no")]==0 && $row[csf("result")]==1 && $add_tp_stri_batch_arr[$row[csf("batch_id")]]=="")
				{
					if($chkBatch_2[$row[csf("batch_id")]] =="")
					{
						//$total_rft_qnty += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
						$total_rft_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch_2[$row[csf("batch_id")]] =$row[csf("batch_id")];
					}
				}
				$batch_againstId=$row[csf("batch_against")];
				if($chkBatch_4[$row[csf("batch_id")]] =="" && $batch_againstId!=2)
				{
					$without_fabric_type_production_arr[$row[csf("fabric_type")]] += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$without_fabric_type_production_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$chkBatch_4[$row[csf("batch_id")]] =$row[csf("batch_id")];
				}
				
				//if($chkBatch_3[$row[csf("batch_id")]] =="")
				if($chkBatch_3[$row[csf("batch_id")]] =="" && $row[csf("result")]==1 && $row[csf("extention_no")]>0)
				{
					//$fabric_type_production_arr[$row[csf("fabric_type")]] += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
					//$fabric_type_production_total +=$batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
					$fabric_type_production_arr[$row[csf("fabric_type")]] += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$fabric_type_production_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$chkBatch_3[$row[csf("batch_id")]] =$row[csf("batch_id")];
				}

				if($row[csf("extention_no")]>0 && $row[csf("result")]==1 && $chkBatch_6[$row[csf("batch_id")]] =="")
				{
					$chkBatch_6[$row[csf("batch_id")]] =$row[csf("batch_id")];
					
					$buyer_wise_summary[$row[csf("buyer_id")]] += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					
					$buyer_wise_summary_batch_total += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$dyeing_process_wise_batch_qnty_arr[$row[csf("process_id")]] +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$dyeing_process_wise_batch_qnty_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
				}

				//shade Matched
				if($row[csf("result")]==1) 
				{
					//if($row[csf("extention_no")]<1){
						//if($chkBatch_45[$row[csf("batch_id")]] == ""){
							$chkBatch_45[$row[csf("batch_id")]] =$row[csf("batch_id")];

							$shade_matched_wise_summary[$row[csf("buyer_id")]]["subcon"]+=$row[csf("sub_batch_qnty")];
							$shade_matched_buyer_total +=$row[csf("sub_batch_qnty")];

							$shade_matched_wise_summary[$row[csf("buyer_id")]]["trim"]+=$row[csf("total_trims_weight")];
							$shade_matched_buyer_total +=$row[csf("total_trims_weight")];
						//}
					//}
				}
				else
				{
					//$shade_not_matched_wise_summary[$row[csf("buyer_id")]]["self"]+=$row[csf("batch_qnty")];
					//$shade_not_matched_buyer_total +=$row[csf("batch_qnty")];
					//if($chkBatch_4[$row[csf("batch_id")]] == ""){
						$chkBatch_4[$row[csf("batch_id")]] =$row[csf("batch_id")];

						$shade_not_matched_wise_summary[$row[csf("buyer_id")]]["subcon"]+=$row[csf("sub_batch_qnty")];
						$shade_not_matched_buyer_total +=$row[csf("sub_batch_qnty")];

						$shade_not_matched_wise_summary[$row[csf("buyer_id")]]["trim"]+=$row[csf("total_trims_weight")];
						$shade_not_matched_buyer_total +=$row[csf("total_trims_weight")];
					//}
				}

				//$dyeing_process_wise_batch_qnty_arr[$row[csf("process_id")]] +=$row[csf("batch_qnty")];
				//$dyeing_process_wise_batch_qnty_total +=$row[csf("batch_qnty")];

				//$btb_ltb_count=1;
				if($chkBatch_5[$row[csf("batch_id")]] == "")
				{
					$chkBatch_5[$row[csf("batch_id")]] =$row[csf("batch_id")];
					if($row[csf("extention_no")]==0 && $add_tp_stri_batch_arr[$row[csf("batch_id")]]=="" && $row[csf("result")] ==1){
					//$btb_shade_matched[$row[csf("ltb_btb_id")]]["shade_matched"];
						$btb_shade_matched[$row[csf("ltb_btb_id")]]["shade_matched"].=$row[csf("system_no")].',';//+=$btb_ltb_count;
						//echo "T".$row[csf("ltb_btb_id")];
					}
				//	if($multi_batch_load_id==1)
					//{
					$btb_shade_matched[$row[csf("ltb_btb_id")]]["total_batch"].=$row[csf("system_no")].',';
					//}
				}
			}
			//print_r($buyer_wise_summary);
			$x=1;
			$sub_batch_row_span_arr =array();$sub_ttl_batch_prod=0;$sub_machine_check_array=array();
			foreach ($subbatch_data_arr as $batch_id => $batch_data_arr) 
			{
				$sub_batch_row_span = 0;
				foreach ($batch_data_arr as $prod_id=>$row) 
				{
					$sub_batch_row_span++;
					$sub_batch_qnty_arr[$batch_id]+= $row["production_qnty"]; 
					
					//$sub_ttl_batch_prod+=$row["production_qnty"]+$row["total_trims_weight"];//$sub_ttl_batch_prod_tot_arr[$batch_id]["trims"];
					$sub_buyer_batch_qnty_arr[$row["buyer_id"]]+= $row["production_qnty"]+$sub_ttl_batch_prod_tot_arr[$batch_id]["trims"];
				}
				 $multi_batch_load_id=$multi_batch_arr[$batch_id];;
				 $batch_againstId=$row[("batch_againstId")];
				 
			//	$sub_buyer_batch_qnty_arr[$row[csf("batch_id")]]["prod_qty"]= $sub_batch_qnty_arr[$batch_id]+$sub_ttl_batch_prod_tot_arr[$batch_id]["trims"];
				//$sub_ttl_batch_prod_tot_arr[$row[csf("batch_id")]]["trims"]
				//prod_qnty_tot
				//echo $sub_ttl_batch_prod_tot_arr[$batch_id]["prod_qty"].'FD';
				$sub_ttl_batch_prod+=$sub_batch_qnty_arr[$batch_id]+$sub_ttl_batch_prod_tot_arr[$batch_id]["trims"];
				if($batch_againstId==2)  //Reprocess
				{
					$color_wise_batch_arr[$row[("color_range_id")]]["summary_batch_qnty"]+=$sub_batch_qnty_arr[$batch_id];//$sub_ttl_batch_prod_tot_arr[$batch_id]["prod_qty"];
					if($sub_ttl_batch_prod_tot_arr[$batch_id]["trims"]>0)
					{
					$color_wise_batch_arr[$row[("color_range_id")]]["total_trims_weight"]+= $sub_ttl_batch_prod_tot_arr[$batch_id]["trims"];
					}
					$color_wise_batch_arr[$row[("color_range_id")]]["batch_ids"].=$batch_id.',';
					}
				else
				{
					if($batch_againstId==2)  //Reprocess
					{
					$without_reprocess_color_wise_batch_arr[$row[("color_range_id")]]["summary_batch_qnty"]+=$sub_batch_qnty_arr[$batch_id];//$sub_ttl_batch_prod_tot_arr[$batch_id]["prod_qty"];
					if($sub_ttl_batch_prod_tot_arr[$batch_id]["trims"]>0)
					{
					$without_reprocess_color_wise_batch_arr[$row[("color_range_id")]]["total_trims_weight"]+= $sub_ttl_batch_prod_tot_arr[$batch_id]["trims"];
					}
					$without_reprocess_color_wise_batch_arr[$row[("color_range_id")]]["batch_ids"].=$batch_id.',';
					}	
				}
				
				$sub_total_color_wise_batch_qty+= $sub_batch_qnty_arr[$batch_id]+$sub_ttl_batch_prod_tot_arr[$batch_id]["trims"];
				
				//echo $sub_ttl_batch_prod_tot_arr[$batch_id]["prod_qty"].'DD';
				//$sub_ttl_batch_prod+=$sub_ttl_batch_prod_tot_arr[$batch_id]["prod_qty"]+$sub_ttl_batch_prod_tot_arr[$batch_id]["trims"];
				$sub_batch_row_span_arr[$batch_id] = $sub_batch_row_span;
				
				$machine_id=$sub_machine_id_arr[$batch_id]['machine_id'];
				if (!in_array($machine_id,$sub_machine_check_array))
					{ $x++;
						
						
						 $sub_machine_check_array[]=$machine_id;
						 $sub_machine_capacity=$sub_tot_machine_capacity_arr[$batch_id];
					}
					else
					{
						 $sub_machine_capacity=0;
					}
					
				$sub_tot_machine_capacity+=$sub_machine_capacity;
			}	
			//echo $sub_ttl_batch_prod.'XXX';		
		}			
	}
	$all_days_total_date=count($tot_process_end_date_arr);
		
		if($date_search_type==1) $date_type_msg="Dyeing Date"; else $date_type_msg="Insert Date";
		//echo $date_type_msg;
		$load_hr=array();
		$load_min=array();
		$load_date=array();
		$water_flow_arr=array(); $load_hour_meter_arr=array();
		if ($working_company==0) $workingCompany_name_cond1=""; else $workingCompany_name_cond1="  and service_company='".$working_company."' ";
		if ($working_company==0) $workingCompany_name_cond13=""; else $workingCompany_name_cond13="  and f.service_company='".$working_company."' ";
		if ($company==0) $companyCond1=""; else $companyCond1="  and f.company_id=$company";
	
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
		$sub_load_time_data=sql_select("select f.batch_id, f.water_flow_meter, f.batch_no, f.load_unload_id, f.process_end_date, f.end_hours, f.hour_load_meter, f.end_minutes from pro_fab_subprocess f where f.load_unload_id=1 and f.entry_form=38 $companyCond1 and f.status_active=1  and f.is_deleted=0 $workingCompany_name_cond13 $sub_dyeing_batch_id_cond");
		//echo "select f.batch_id, f.water_flow_meter, f.batch_no, f.load_unload_id, f.process_end_date, f.end_hours, f.hour_load_meter, f.end_minutes from pro_fab_subprocess f where f.load_unload_id=1 and f.entry_form=38 $companyCond1 and f.status_active=1  and f.is_deleted=0 $workingCompany_name_cond13 $sub_dyeing_batch_id_cond";
		
		foreach($sub_load_time_data as $row_time)// for Loading time
		{
			$sub_load_hr[$row_time[csf('batch_id')]]=$row_time[csf('end_hours')];
			$sub_load_min[$row_time[csf('batch_id')]]=$row_time[csf('end_minutes')];
			$sub_load_date[$row_time[csf('batch_id')]]=$row_time[csf('process_end_date')];
			$sub_water_flow_arr[$row_time[csf('batch_id')]]=$row_time[csf('water_flow_meter')];
			$sub_load_hour_meter_arr[$row_time[csf('batch_id')]]=$row_time[csf('hour_load_meter')];
		}
		unset($sub_load_time_data);
		
		
	
			ob_start();
			if($cbo_type==1)
			{
				?>
				<div>
					<fieldset style="width:1450px;">
						<div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong><br><strong> Dyeing WIP </strong> </div>
						<div>
							<? 
							//$batchdata = sql_select($sql);
							$batchdata = sql_select($sql);
							foreach($batchdata as $row)
							{
								$batch_id.=$row[csf('id')].",";
							}
							$batch_ids=rtrim($batch_id,',');
							$baIds=chop($batch_id,','); $ba_cond_in="";
							$ba_ids=count(array_unique(explode(",",$batch_ids)));
							if($ba_ids>1000 && $db_type==2)
							{
							$ba_cond_in=" and (";
							$baIdsArr=array_chunk(explode(",",$baIds),999);
							foreach($baIdsArr as $ids)
							{
							$ids=implode(",",$ids);
							$ba_cond_in.=" f.batch_id in($ids) or"; 
							}
							$ba_cond_in=chop($ba_cond_in,'or ');
							$ba_cond_in.=")";
							}
							else
							{
							$ba_cond_in=" and f.batch_id in($baIds)";
							}
				
							$sql_batch_id=sql_select("select f.batch_id from  pro_fab_subprocess f where f.entry_form =35 $companyCond1 and f.load_unload_id=2 and f.status_active=1 and f.is_deleted=0  $unload_batch_cond2  $cbo_prod_source_cond $machine_cond $floor_id_cond $cbo_prod_type_cond $dyeing_batch_id_cond $ba_cond_in");
							//echo "select f.batch_id from  pro_fab_subprocess f where f.entry_form =35 $companyCond1 and f.load_unload_id=2 and f.status_active=1 and f.is_deleted=0  $unload_batch_cond2  $cbo_prod_source_cond $machine_cond $floor_id_cond $cbo_prod_type_cond $dyeing_batch_id_cond $ba_cond_in";
						
							//$tot_row=1;$batch_id='';
							foreach($sql_batch_id as $row_batch)
							{
							//if($batch_id=='') $batch_id=$row_batch[csf('batch_id')];else $batch_id.=",".$row_batch[csf('batch_id')];
							$batch_unload_check[$row_batch[csf('batch_id')]]=$row_batch[csf('batch_id')];
							}
							unset($sql_batch_id);
							
							if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
							{

								if (count($batchdata)>0)
								{
									?>
									<div align="left"> <b>Self batch</b></div>
									<table class="rpt_table" width="1450" cellpadding="0" cellspacing="0" border="1" rules="all" align="left" id="table_header_1">
										<thead>
											<tr>
												<th width="30">SL</th>
												<? if($group_by==3 || $group_by==0){ ?>
													<th width="80">M/C No</th>
												<? } ?>
												<? if($group_by==2 || $group_by==0){ ?>
													<th width="80">Floor</th>  
												<? } if($group_by==1 || $group_by==0){ ?> 
													<th width="80">Shift</th>
												<? } ?>
												<th width="100">Booking No</th>
												<th width="100">Buyer</th>
												<th width="80">Style</th>
												<th width="90">FSO No</th>
												<th width="100">Fabrics Desc</th>
												<th width="70">Dia/Width Type</th>
												<th width="80">Color Name</th>
												<th width="90">Batch No</th>
												<th width="40">Ext. No</th>
												<th width="80">Fabric Weight</th>
												<th width="80">Trims Weight</th>
												<th width="80">Total Batch Weight</th>
												<th width="100">Loading Date & Time</th>
												<th>process</th>
											</tr>
										</thead>
									</table>
									<div style=" max-height:360px; width:1470px; overflow-y:scroll;" id="scroll_body" align="left">
										<table class="rpt_table" id="table_body" width="1450" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
											<tbody>
												<? 
												$i=1;$k=1;
												$f=0;
												$btq=0;$grand_total_batch_qty=0; $sub_trim_tot=0;$sub_batch_wgt=0;
												$batch_chk_arr=array();$group_by_arr=array();
												foreach($batchdata as $batch)
												{ 
													if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													if($batch_unload_check[$batch[csf('id')]]=='')
													{
														if($group_by!=0)
														{
															if($group_by==1)
															{
																$group_value=$batch[csf('floor_id')];
																$group_name="Floor";
																$group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
															}
															else if($group_by==2)
															{
																$group_value=$batch[csf('shift_name')];
																$group_name="Shift";
																$group_dtls_value=$shift_name[$batch[csf('shift_name')]];
															}
															else if($group_by==3)
															{
																$group_value=$batch[csf('machine_id')];
																$group_name="machine";
																$group_dtls_value=$machine_arr[$batch[csf('machine_id')]];
															}
															if (!in_array($group_value,$group_by_arr) )
															{
																if($k!=1)
																{ 	
																	?>
																	<tr class="tbl_bottom">
																		<td width="30">&nbsp;</td>
																		<? if($group_by==3 || $group_by==0){ ?>
																			<td width="80">&nbsp;</td> 
																		<? } ?>
																		<? if($group_by==2 || $group_by==0){ ?>
																			<td width="80">&nbsp;</td> 
																		<? } if($group_by==1 || $group_by==0){ ?>  
																			<td width="80">&nbsp;</td>
																		<? } ?>
																		<td width="100">&nbsp;</td>
																		<td width="100">&nbsp;</td>
																		<td width="80">&nbsp;</td>
																		<td width="90">&nbsp;</td>
																		<td width="100">&nbsp;</td>
																		<td width="70">&nbsp;</td>
																		<td width="80">&nbsp;</td>

																		<td width="90" >Sub.Total: </td>
																		<td width="40"></td>
																		<td width="80"><? echo number_format($btq,2); ?></td>
																		<td width="80"><? echo number_format($sub_trim_tot,2); ?></td>
																		<td width="80"><? echo number_format($sub_batch_wgt,2); ?></td>
																		<td width="100"></td>

																		<td>&nbsp;</td>
																	</tr>                                
																	<?
																	unset($btq);unset($sub_trim_tot);unset($sub_batch_wgt);
																}
																?>
																<tr bgcolor="#EFEFEF">
																	<td colspan="16" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
																</tr>
																<?
																$group_by_arr[]=$group_value;            
																$k++;
															}					
														}

														$order_id=$batch[csf('po_id')];
														$color_id=$batch[csf('color_id')];
														$desc=explode(",",$batch[csf('item_description')]); 
														$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')]))); 
														?>
														<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">

															<td width="30"><? echo $i; ?></td>

															<? if($group_by==3 || $group_by==0){ ?>
																<td  align="center" width="80" title="<? echo $machine_arr[$batch[csf('machine_id')]]; ?>"><p><? echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
																<?
															}
															if($group_by==2 || $group_by==0){ ?>
																<td width="80"><p><? echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
															<? } if($group_by==1 || $group_by==0){ ?>
																<td width="80"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
															<? } ?>
															<td  width="100" align="center"><p><? echo $batch[csf('sales_booking_no')]; ?></p></td>
															<td width="100" align="center" title="<? echo $batch[csf('po_buyer')];?>"><p><? if($batch[csf('within_group')]==2) echo $buyer_arr[$batch[csf('buyer_id')]]; else echo $buyer_arr[$batch[csf('po_buyer')]];?></p></td>

															<td  width="80" align="center"><p style="word-break: break-all;"><? echo $batch[csf('style_ref_no')]; ?></p></td>
															<td width="90" align="center"><p><? echo $batch[csf('fso_no')]; ?></p></td>

															<td  width="100"><p style="word-break: break-all;"><? echo $batch[csf('item_description')]; ?></p></td>
															<td  width="70" title="<? echo $fabric_typee[$batch[csf('width_dia_type')]]; ?>"><p><? echo $fabric_typee[$batch[csf('width_dia_type')]]; ?></p></td>
															<td  width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
															<td  align="center" width="90" style="word-break: break-all;"><p><? echo $batch[csf('batch_no')]; ?></p></td>
															<td  align="center" width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
															<td align="right" width="80"   title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
															<? 
															$total_batch_wgt=0;
															if($batchTrimChk[$batch[csf('id')]]=="")
															{
																$total_batch_wgt = $batch[csf('batch_qnty')]+ $batch[csf('total_trims_weight')];
																?>
																<td align="right" width="80" ><? echo number_format($batch[csf('total_trims_weight')],2);  ?></td>
																<td align="right" width="80" ><? echo number_format($total_batch_wgt,2);  ?></td>
																<?
																$batchTrimChk[$batch[csf('id')]] = $batch[csf('id')]; 
																$sub_trim_tot += $batch[csf('total_trims_weight')];
																$grand_trim_tot += $batch[csf('total_trims_weight')];
															}
															else
															{
																$total_batch_wgt = $batch[csf('batch_qnty')];
																?>
																<td align="right" width="80" ><?  ?></td>
																<td align="right" width="80" ><? echo number_format($total_batch_wgt,2);  ?></td>
																<?
															}
															?>

															<td width="100" align="center"><p><?  echo  ($batch[csf('process_end_date')] == '0000-00-00' || $batch[csf('process_end_date')] == '' ? '' : change_date_format($batch[csf('process_end_date')])).'<br>'.$batch[csf('end_hours')].':'.$batch[csf('end_minutes')]; ?></p></td>

															<td align="center"><? echo $conversion_cost_head_array[$batch_against[$batch[csf('process_id')]]];?> &nbsp;</td>
														</tr>
														<? 
														$i++;
														$btq+=$batch[csf('batch_qnty')];
														$grand_total_batch_qty+=$batch[csf('batch_qnty')];

														$sub_batch_wgt += $total_batch_wgt;
														$grand_batch_wgt += $total_batch_wgt;
													}
												} 
												?>
											</tbody>
										</table>
										<table class="rpt_table" width="1450" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
											<tfoot>
												<?
												if($group_by!=0)
												{
													?>
													<tr>
														<th width="30">&nbsp;</th>
														<? if($group_by==3 || $group_by==0){ ?>
															<th width="80">&nbsp;</th>
														<? } ?>
														<? if($group_by==2 || $group_by==0){ ?>
															<th width="80">&nbsp;</th>  
														<? } if($group_by==1 || $group_by==0){ ?> 
															<th width="80">&nbsp;</th>
														<? } ?>
														<th width="100">&nbsp;</th>
														<th width="100">&nbsp;</th>
														<th width="80">&nbsp;</th>
														<th width="90">&nbsp;</th>
														<th width="100">&nbsp;</th>
														<th width="70">&nbsp;</th>
														<th width="80">&nbsp;</th>
														<th width="130" colspan="2">Total</th>
														<th width="80" align="right"><? echo number_format($btq,2); ?></th>
														<th width="80" align="right"><? echo number_format($sub_trim_tot,2);?></th>
														<th width="80" align="right"><? echo number_format($sub_batch_wgt,2);?></th>
														<th width="100">&nbsp;</th>
														<th>&nbsp;</th>
													</tr>
													<?
												}
												?>
												<tr>
													<th width="30">&nbsp;</th>
													<? if($group_by==3 || $group_by==0){ ?>
														<th width="80">&nbsp;</th>
													<? } ?>
													<? if($group_by==2 || $group_by==0){ ?>
														<th width="80">&nbsp;</th>  
													<? } if($group_by==1 || $group_by==0){ ?> 
														<th width="80">&nbsp;</th>
													<? } ?>
													<th width="100">&nbsp;</th>
													<th width="100">&nbsp;</th>
													<th width="80">&nbsp;</th>
													<th width="90">&nbsp;</th>
													<th width="100">&nbsp;</th>
													<th width="70">&nbsp;</th>
													<th width="80">&nbsp;</th>

													<th width="90">GrandTotal</th>
													<th width="40"></th>
													<th width="80" align="right" id="value_grand_batch_qnty"><? echo number_format($grand_total_batch_qty,2); ?></th>
													<th width="80" align="right" id="value_grand_trims_qnty"><? echo number_format($grand_trim_tot,2); ?></th>
													<th width="80" align="right" id="value_grand_tot_batch_wgt"><? echo number_format($grand_batch_wgt,2); ?></th>
													<th width="100">&nbsp;</th>
													<th>&nbsp;</th>
												</tr>
											</tfoot>
										</table>
									</div>
									<br/>
									<? 
								}
							}

							?>
						</div>
					</fieldset>
					<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
				</div>
				<?
			}
			else if($cbo_type==2) 
			{
				?>
				<div>
					<fieldset style="width:1350px;">
						<div align="center"><strong> <? echo $company_library[$company]; ?> </strong><br> <strong> Daily Dyeing Production </strong><br>
							<?

							echo  ($date_from == '0000-00-00' || $date_from == '' ? '' : change_date_format($date_from)).' To ';echo  ($date_to == '0000-00-00' || $date_to == '' ? '' : change_date_format($date_to));
							?>
						</div>
						<?  

						if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
						{
							if (count($batchdata)>0)
							{
								?>
								<table cellpadding="0"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
									<thead>
										<tr>
											<th colspan="3">Production Summary</th>
										</tr>
										<tr>
											<th width="130">Details </th> 
											<th width="120">Prod. Qty. </th>
											<th>%</th>
										</tr>
									</thead>
									<tbody>
										<tr bgcolor="#E9F3FF">
											<td title="Solid and AOP">Color Batch</td>
											<td align="right" title=""><? echo number_format($total_color_batch_qty,2);?></td>
											<td align="right"><? echo number_format(($total_color_batch_qty*100)/($total_others_color_batch_qty + $total_color_batch_qty),2);?></td>
										</tr>
                                        <tr bgcolor="#FFFFFF">
											<td>Others Batch</td>
											<td align="right"><? echo number_format($total_others_color_batch_qty,2);?></td>
											<td align="right"><? echo number_format(($total_others_color_batch_qty*100)/($total_others_color_batch_qty + $total_color_batch_qty),2);?></td>
										</tr>
                                        <?
										$total_rft_qnty=0;
										 $total_rft_qnty=$total_color_batch_qty+$total_others_color_batch_qty;?>
                                        <tr bgcolor="#E9F3FF">
											<td><b>Total RFT</b></td>
											<td align="right"><b><? echo number_format($total_rft_qnty,2);?></b></td>
											<td align="right"><b><?  if($total_rft_qnty) echo  number_format(($total_rft_qnty*100)/$total_rft_qnty,2);else echo "0";?></b></td>
										</tr>
                                        
										<tr bgcolor="#FFFFFF">
										<td>Dyes Adding</td> 
										<td align="right"><? echo number_format($total_dyes_adding_qnty,2);?></td>
										<td align="right"><? if($total_dyes_adding_qnty) echo number_format(($total_dyes_adding_qnty*100)/($total_dyes_adding_qnty + $total_chemical_adding_qnty + $total_dyes_chemical_adding_qnty),2);else echo "0"; ?></td>
										</tr>
                                        <tr bgcolor="#E9F3FF">
										<td>Chemical Adding</td> 
										<td align="right"><? echo number_format($total_chemical_adding_qnty,2);?></td>
										<td align="right"><? if($total_chemical_adding_qnty) echo number_format(($total_chemical_adding_qnty*100)/($total_dyes_adding_qnty + $total_chemical_adding_qnty + $total_dyes_chemical_adding_qnty),2);else echo "0"; ?></td>
										</tr>
                                        <tr bgcolor="#FFFFFF">
										<td>Dyes + Chemical Adding</td> 
										<td align="right"><? echo number_format($total_dyes_chemical_adding_qnty,2);?></td>
										<td align="right"><? if($total_dyes_chemical_adding_qnty) echo number_format(($total_dyes_chemical_adding_qnty*100)/($total_dyes_chemical_adding_qnty + $total_dyes_adding_qnty + $total_chemical_adding_qnty),2);else echo "0"; ?></td>
										</tr>
                                        <tr bgcolor="#E9F3FF">
										<td><b>Total Adding</b></td> 
                                        <?
										
                                        $total_adding=$total_dyes_adding_qnty+$total_chemical_adding_qnty+$total_dyes_chemical_adding_qnty;
										?>
										<td align="right"><b><? echo number_format($total_adding,2);?></b></td>
										<td align="right"><b><? if($total_adding) echo number_format(($total_adding*100)/$total_adding,2);else echo "0"; ?></b></td>
										</tr>
                                         <tr bgcolor="#FFFFFF">
                                         <?
                                         $total_rft_add=0; $total_rft_add_per=0;
										$total_rft_add=$total_rft_qnty+$total_adding+$total_topping_qnty;
										 ?>
										<td>Topping</td> 
										<td align="right"><? echo number_format($total_topping_qnty,2);?></td>
										<td align="right" title="Topping/Total*100"><? 
										if($total_topping_qnty) echo number_format($total_topping_qnty*100/$total_rft_add,2);
										else echo '0'; ?></td>
										</tr>
                                        
                                        <tr bgcolor="#FFFF00">
											<td><b>Total:</b></td> 
											<td align="right"><b><? 
											
											//$total_rft_add_per=(($total_rft_qnty*100)/($total_rft_qnty + $total_adding+$total_topping_qnty))+(($total_adding*100)/($total_rft_qnty + $total_adding));
											echo number_format($total_rft_add,2);?></b></td>
											<td align="right"><b><? echo number_format($total_rft_add/$total_rft_add*100,2);?></b></td>
										</tr>
                                        <tr bgcolor="#FFFFFF">
										<td>Re Wash</td> 
										<td align="right"><? echo number_format($total_rewash_batch_qty,2);?></td>
										<td align="right"><? //echo number_format(($total_rewash_batch_qty*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty),2); ?></td>
										</tr>
                                         <tr bgcolor="#FFFFFF">
										<td>Re Dyeing</td> 
										<td align="right"><? echo number_format($total_redying_batch_qty,2);?></td>
										<td align="right"><? //echo number_format(($total_adding_qnty*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty),2); ?></td>
										</tr>
                                         <tr bgcolor="#E9F3FF">
										<td>Stripping</td> 
										<td align="right"><? echo number_format($total_stripping_qnty,2);?></td>
										<td align="right"><? echo number_format(($total_stripping_qnty*100)/($total_rft_qnty + $total_stripping_qnty + $total_reprocess_qty),2); ?></td>
										</tr>
                                        <?
                                        $total_reprocess_qty=0;
										$total_reprocess_qty=$total_redying_batch_qty+$total_rewash_batch_qty+$total_stripping_qnty;
										?>
										<tr bgcolor="#E9F3FF">
											<td><b>Total Re-Process</b></td> 
											<td align="right"><b><? echo number_format($total_reprocess_qty,2);?></b></td>  
											<td align="right"><? //echo number_format(($total_reprocess_qty*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty),2);?></td> 
										</tr>
										<tr bgcolor="#EBEBEB">
											<td align="right"><b>Grand Total:</b></td> 
											<td align="right">
												<b><? echo number_format($total_reprocess_qty + $total_rft_add,2); ?></b>
											</td> 
											<td align="right"><b><? echo number_format((($total_rft_add + $total_reprocess_qty)*100)/($total_rft_add + $total_reprocess_qty),2);?></b></td>  
											</tr>
											<tr bgcolor="#FFFFFF">
												<td><b>Avg. Prod. Per Day</b></td> 
												<td align="right" title="<? echo "days=".implode(",",array_filter(array_unique($production_date_count)));?>">
													<b><? 
													echo number_format(($total_reprocess_qty + $total_rft_add)/count(array_filter(array_unique($production_date_count))),2);
													?></b>	
												</td> 
												<td></td> 
											</tr>
                                            <tr>
                                            <td colspan="3"><b> Fabric type wise breakdown with Reprocess</b></td>
                                            </tr>
											<? 
											$ftsl=2;
											foreach($fabric_type_production_arr as $fabType=>$val)
											{ 
												if ($ftsl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													<td><? echo $fabric_type_for_dyeing[$fabType];?></td> 
													<td align="right"><? echo number_format($val,2);?></td> 
													<td align="right"><? echo number_format(($val/$fabric_type_production_total)*100,2);?></td> 
												</tr>
												<?
												$ftsl++;
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th>Total</th> 
												<th align="right"><? echo number_format($fabric_type_production_total,2);?></th> 
												<th align="right"><? echo number_format(100,2);?></th> 
											</tr>
										</tfoot>
									</table>

									<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
										<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
									</table>
                                    <table cellpadding="0"   style="margin:5px;" width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<thead>
											<tr>
												<th colspan="3">Fabric type wise breakdown without Reprocess</th>
											</tr>
											<!--<tr>
												<th width="40">Sl </th> 
												<th width="100">Buyer</th>
												<th width="100">Batch Total</th>
												<th>%</th>
											</tr>-->
										</thead>
										<tbody>
											<? 
											$sl=1;
											foreach($without_fabric_type_production_arr as $fab_type=> $qty)
											{
												if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													 
														<td><? echo $fabric_type_for_dyeing[$fab_type];?></td> 
													<td align="right"><? echo number_format($qty,2);?></td>
													<td align="right"><? echo number_format(($qty/$without_fabric_type_production_total)*100,2);?></td>
												</tr>
												<?
												$sl++;
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th>Total </th>
												<th><? echo number_format($without_fabric_type_production_total,2);?></th>
												<th><? if ($without_fabric_type_production_total>0) echo "100.00"; else echo "0.00";?></th>
											</tr>
										</tfoot>
									</table>
                                    

									<table cellpadding="0"   style="margin:5px;" width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<thead>
											<tr>
												<th colspan="4">Re-Process Summary (Buyer Wise)</th>
											</tr>
											<tr>
												<th width="40">Sl </th> 
												<th width="100">Buyer</th>
												<th width="100">Batch Total</th>
												<th>%</th>
											</tr>
										</thead>
										<tbody>
											<? 
											$sl=1;
											foreach($buyer_wise_summary as $buyer=> $bs)
											{
												if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													<td align="center"><? echo $sl;?></td>
													<td align="center"><? echo $buyer_arr[$buyer];?></td>
													<td align="right"><? echo number_format($bs,2);?></td>
													<td align="right"><? echo number_format(($bs/$buyer_wise_summary_batch_total)*100,2);?></td>
												</tr>
												<?
												$sl++;
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="2">Total </th>
												<th><? echo number_format($buyer_wise_summary_batch_total,2);?></th>
												<th><? if ($buyer_wise_summary_batch_total>0) echo "100.00"; else echo "0.00";?></th>
											</tr>
										</tfoot>
									</table>
									<table cellpadding="0" style="margin:5px;"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<thead>
											<tr>
												<th colspan="5">Color Range wise Summary With Reprocess</th>
											</tr>
											<tr>
												<th width="40">Sl </th> 
												<th width="100">Color Range</th>
												<th width="100">Quantity (Kgs)</th>
												<th  width="50">Percentage%</th>
												<th>No. Of Batches</th>
											</tr>
										</thead>
										<tbody>
											<? 
											$sl=1;$tot_batch_qty=$tot_no_of_batch=$tot_total_trims_weight=0;
											//print_r($color_wise_batch_arr);ttl_batch_prod_tot
											foreach($color_wise_batch_arr as $color_rang_id=> $val)
											{
												$total_color_qty+=$val['summary_batch_qnty']+$val['total_trims_weight'];
											}
											foreach($color_wise_batch_arr as $color_rang_id=> $summ)
											{
												if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												$batch_ids=rtrim($summ[batch_ids],',');
												$batch_ids_arr=array_unique(explode(",",$batch_ids));
												$tot_total_trims_weight+=$summ['total_trims_weight'];
												//echo count($batch_ids_arr).',';
												//echo $val['total_trims_weight'].'D';
												$tot_prod_qty_summ=$summ['summary_batch_qnty']+$summ['total_trims_weight'];
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													<td align="center"><? echo $sl;?></td>
													<td align="center"><? echo $color_range[$color_rang_id];?></td>
													<td align="right"><? echo number_format($tot_prod_qty_summ,2);?></td>
													<td align="right" title="Tot batch Qty=<? echo $total_color_qty;?>">
													<? echo number_format(($tot_prod_qty_summ/($total_color_qty))*100,2);?></td>
													<td align="right"><? echo count($batch_ids_arr);?></td>
												</tr>
												<?
												$sl++;
												$tot_batch_qty+=$tot_prod_qty_summ;
												$tot_no_of_batch+=count($batch_ids_arr);
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="2">Total </th>
												<th><? echo number_format($tot_batch_qty,2);?></th>
												<th><? echo number_format(($tot_batch_qty/($total_color_qty))*100,2);?></th>
												<th><? echo number_format($tot_no_of_batch,0);?></th>
											</tr>
										</tfoot>
									</table>
                                   
                                    <table cellpadding="0"   style="margin:5px;" width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<thead>
											<tr>
												<th colspan="5">Color Range wise Summary Without Reprocess</th>
											</tr>
											<tr>
												<th width="40">Sl </th> 
												<th width="100">Color Range</th>
												<th width="100">Quantity (Kgs)</th>
												<th  width="50">Percentage%</th>
												<th>No. Of Batches</th>
											</tr>
										</thead>
										<tbody>
											<? 
											$sl=1;$tot_batch_qty=$tot_no_of_batch=$tot_total_trims_weight=0;
											//print_r($color_wise_batch_arr);ttl_batch_prod_tot
											foreach($without_reprocess_color_wise_batch_arr as $color_rang_id=> $val)
											{
												$total_color_qty+=$val['summary_batch_qnty']+$val['total_trims_weight'];
											}
											foreach($without_reprocess_color_wise_batch_arr as $color_rang_id=> $summ)
											{
												if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												$batch_ids=rtrim($summ[batch_ids],',');
												$batch_ids_arr=array_unique(explode(",",$batch_ids));
												$tot_total_trims_weight+=$summ['total_trims_weight'];
												//echo count($batch_ids_arr).',';
												//echo $val['total_trims_weight'].'D';
												$tot_prod_qty_summ=$summ['summary_batch_qnty']+$summ['total_trims_weight'];
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													<td align="center"><? echo $sl;?></td>
													<td align="center"><? echo $color_range[$color_rang_id];?></td>
													<td align="right"><? echo number_format($tot_prod_qty_summ,2);?></td>
													<td align="right" title="Tot batch Qty=<? echo $total_color_qty;?>">
													<? echo number_format(($tot_prod_qty_summ/($total_color_qty))*100,2);?></td>
													<td align="right"><? echo count($batch_ids_arr);?></td>
												</tr>
												<?
												$sl++;
												$tot_batch_qty+=$tot_prod_qty_summ;
												$tot_no_of_batch+=count($batch_ids_arr);
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="2">Total </th>
												<th><? echo number_format($tot_batch_qty,2);?></th>
												<th><? echo number_format(($tot_batch_qty/($total_color_qty))*100,2);?></th>
												<th><? echo number_format($tot_no_of_batch,0);?></th>
											</tr>
										</tfoot>
									</table>
									<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
										<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
									</table>

									<table cellpadding="0"  width="640" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<thead>
											<tr>
												<th colspan="8">Summary Total (Shade Match)</th>
											</tr>
											<tr>
												<th width="100">Buyer</th>
												<th width="100">Self batch</th> 
												<th width="100">Smpl. Batch With Order</th>
												<th width="60">Smpl. Batch Without Order</th>
												<th width="60">Trims Weight</th>
												<th width="60">Inbound subcontract</th>
												<th width="100">Buyer Total</th>
												<th>%</th>
											</tr>
										</thead>
										<tbody>
											<? 
											$sl=1;
											foreach($shade_matched_wise_summary as $buyer=> $val)
											{
												if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												$tot_buyer_match_shade_subcon = $sub_buyer_batch_qnty_arr[$buyer];//$val["subcon"];
												$tot_buyer_match_shade = ($val["self"]+$val["sm"]+$val["smn"]+$val["trim"])+$tot_buyer_match_shade_subcon;
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													<td align="center"><? echo $buyer_arr[$buyer];?></td>
													<td align="right"><? echo number_format($val["self"],2); ?></td>
													<td align="right"><? echo number_format($val["sm"],2); ?></td>
													<td align="right"><? echo number_format($val["smn"],2); ?></td>
													<td align="right"><? echo number_format($val["trim"],2); ?></td>
													<td align="right"><? echo number_format($tot_buyer_match_shade_subcon,2); ?></td>
													<td align="right"><? echo number_format($tot_buyer_match_shade,2);?></td>
													<td align="right"><? echo number_format(($tot_buyer_match_shade/$shade_matched_buyer_total)*100,2);?></td>
												</tr>
												<?
												$sl++;
												$total_self += $val["self"];
												$total_sm += $val["sm"];
												$total_smn += $val["smn"];
												$total_trim += $val["trim"];
												$total_subcon += $tot_buyer_match_shade_subcon;
												$total_tot_buyer_match_shade += $tot_buyer_match_shade;
												$total_summury_shade_matched +=($tot_buyer_match_shade/$shade_matched_buyer_total)*100;

												$grand_total_self += $val["self"];
												$grand_total_sm += $val["sm"];
												$grand_total_smn += $val["smn"];
												$grand_total_trim += $val["trim"];
												$grand_total_subcon += $tot_buyer_match_shade_subcon;
												$grand_total_buyer += $tot_buyer_match_shade;
											}
											unset($shade_matched_wise_summary);
											?>
											<tr style="background-color: #eee;font-weight: bold;">
												<td width="100" align="right">Total</th>
													<td width="100" align="right"><? echo number_format($total_self,2);?></td> 
													<td width="100" align="right"><? echo number_format($total_sm,2);?></td>
													<td width="60" align="right"><? echo number_format($total_smn,2);?></td>
													<td width="60" align="right"><? echo number_format($total_trim,2);?></td>
													<td width="60"><? echo number_format($grand_total_subcon,2);?></td>
													<td width="100" align="right"><? echo number_format($total_tot_buyer_match_shade,2);?></td>
													<td align="right"><? echo number_format($total_summury_shade_matched,2);?></td>
												</tr>
											</tbody>
											<thead>
												<tr>
													<th colspan="8">Summary Total (Shade Not Match)</th>
												</tr>
											</thead>
											<tbody>
												<?
												foreach($shade_not_matched_wise_summary as $buyer=> $val)
												{
													if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
													$tot_buyer_match_not_shade_subcon = $val["subcon"];
													$tot_buyer_match_not_shade = ($val["self"]+$val["sm"]+$val["smn"]+$val["trim"])+$tot_buyer_match_not_shade_subcon;
													?>
													<tr bgcolor="<? echo $bgcolor_dyeing;?>">
														<td align="center"><? echo $buyer_arr[$buyer];?></td>
														<td align="right"><? echo number_format($val["self"],2);?></td>
														<td align="right"><? echo number_format($val["sm"],2);?></td>
														<td align="right"><? echo number_format($val["smn"],2);?></td>
														<td align="right"><? echo number_format($val["trim"],2);?></td>
														<td align="right"><? echo number_format($tot_buyer_match_not_shade_subcon,2); ?></td>
														<td align="right"><? echo number_format($tot_buyer_match_not_shade,2);?></td>
														<td align="right"><? echo number_format(($tot_buyer_match_not_shade/$shade_not_matched_buyer_total)*100,2);?></td>
													</tr>
													<?
													$sl++;

													$total_self_not_matched += $val["self"];
													$total_sm_not_matched += $val["sm"];
													$total_smn_not_matched += $val["smn"];
													$total_trim_not_matched += $val["trim"];
													$total_subcon_not_matched += $tot_buyer_match_not_shade_subcon;
													$total_tot_buyer_match_shade_not_matched += $tot_buyer_match_not_shade;
													$total_summury_shade_matched_not_matched +=($tot_buyer_match_not_shade/$shade_not_matched_buyer_total)*100;

													$grand_total_self += $val["self"];
													$grand_total_sm += $val["sm"];
													$grand_total_smn += $val["smn"];
													$grand_total_trim += $val["trim"];
													$grand_total_subcon+= $tot_buyer_match_not_shade_subcon;
													$grand_total_buyer += $tot_buyer_match_not_shade;
												}
												?>
											</tbody>
											<tfoot>
												<tr bgcolor="#EBEBEB">
													<th align="center">Total</th>
													<th align="right"><? echo number_format($total_self_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_sm_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_smn_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_trim_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_subcon_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_tot_buyer_match_shade_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_summury_shade_matched_not_matched,2);?></th>
												</tr>
												<tr bgcolor="#EBEBEB">
													<th align="center">Grand Total</th>
													<th align="right"><? echo number_format($grand_total_self,2);?></th>
													<th align="right"><? echo number_format($grand_total_sm,2);?></th>
													<th align="right"><? echo number_format($grand_total_smn,2);?></th>
													<th align="right"><? echo number_format($grand_total_trim,2);?></th>
													<th align="right"><? echo number_format($grand_total_subcon,2);?></th>
													<th align="right"><? echo number_format($grand_total_buyer,2);?></th>
													<th align="right"><? ?></th>
												</tr>
											</tfoot>
											
											
										</table>

										
										<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
											<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
											
										</table>
										

										<table cellpadding="0"  width="300" style="margin:5px;" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
											<thead>
												<tr>
													<th colspan="3"></th>
												</tr>
												<tr>
													<th width="130">Re-Processed</th> 
													<th width="100">Batch Qnty</th>
													<th width="">%</th>
												</tr>
											</thead>
											<tbody>
												<? 
												$sl=1;
												foreach($dyeing_process_wise_batch_qnty_arr as $processId=> $val)
												{
													if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor_dyeing;?>">
														<td align="center"><? echo $conversion_cost_head_array[$processId];?></td>
														<td align="right"><? echo number_format($val,2);?></td>
														<td align="right"><? echo number_format(($val/$dyeing_process_wise_batch_qnty_total)*100,2);?></td>
													</tr>
													<?
													$sl++;
												}
												?>
											</tbody>
											<tfoot>
												<tr>
													<th>Total </th>
													<th><? echo number_format($dyeing_process_wise_batch_qnty_total,2);?></th>
													<th>100.00</th>
												</tr>
											</tfoot>
										</table>
                                        <table cellpadding="0" style="margin:5px;"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
                                        
											<thead>
												<tr>
													<th colspan="3">Shade wise Dyeing Category without Reprocess</th>
												</tr>
												<tr>
													<th width="130">Details</th> 
													<th width="100">Quantity</th>
													<th width="">%</th>
												</tr>
											</thead>
											<tbody>
												<? //total_color_batch_arr;;
												 //shade_total_color_batch_qty=$shade_total_others_color_batch_qty
												 
													  $bgcolor_dyeing="#E9F3FF";  $bgcolor_dyeing2="#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor_dyeing;?>">
														 
														<td>Color Batch </td>
                                                        <td align="right"><? echo number_format($shade_total_color_batch_qty,2);?></td>
														<td align="right" title="Shade Color/Total rft*100"> <? echo number_format(($shade_total_color_batch_qty/$total_rft_add)*100,2);?></td>
													</tr>
                                                    <tr bgcolor="<? echo $bgcolor_dyeing2;?>">
														 
														<td>Others Batch </td>
                                                        <td align="right"><? echo number_format($shade_total_others_color_batch_qty,2);?></td>
														<td align="right" title="Shade Other/Total rft*100"><? echo number_format(($shade_total_others_color_batch_qty/$total_rft_add)*100,2);?></td>
													</tr>
													<?
													 
												 
												?>
											</tbody>
											<tfoot>
												<tr>
													<th>Total </th>
													<th><? echo number_format($shade_total_color_batch_qty+$shade_total_others_color_batch_qty,2);?></th>
													<th>100.00</th>
												</tr>
											</tfoot>
										</table>

                                        <table cellpadding="0" style="margin:5px;"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
                                        
											<thead>
												<tr>
													<th colspan="3">Section wise Reprocess</th>
												</tr>
												<tr>
													<th width="130">Details</th> 
													<th width="100">Quantity</th>
													<th width="">%</th>
												</tr>
											</thead>
											<tbody>
												<? 
												$sl=1;
												foreach($section_wise_batch_qnty_arr as $sectionId=> $val)
												{
													if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor_dyeing;?>">
														<td align="center"><? echo $responsibility_dept_arr[$sectionId];?></td>
														<td align="right"><? echo number_format($val,2);?></td>
														<td align="right"><? echo number_format(($val/$section_wise_batch_qnty_total)*100,2);?></td>
													</tr>
													<?
													$sl++;
												}
												 
													 
													 
												 
												?>
											</tbody>
											<tfoot>
												<tr>
													<th>Total </th>
													<th><? echo number_format($section_wise_batch_qnty_total,2);?></th>
													<th>100.00</th>
												</tr>
											</tfoot>
										</table>
                                        


										<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
											<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
										</table>

										<table cellpadding="0" style="margin:5px;" width="400" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
											<thead>
												<tr>
													<th colspan="4"></th>
												</tr>
												<tr>
													<th width="130">RFT + Adding </th> 
													<th width="100" title="Multi Batch Yes">No Of Batch </th>
													<th width="100" title="Multi Batch Yes">Shade Matched</th>
													<th width="">%</th>
												</tr>
											</thead>
											<tbody>
												<? 
												$sl=1;
												foreach($btb_shade_matched as $BtbLtb=> $val) 
												{
													if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
													$total_batch=rtrim($val["total_batch"],',');
													$shade_matched=rtrim($val["shade_matched"],',');
													
													$total_batchArr=array_unique(explode(",",$total_batch));
													$total_batch_no=count($total_batchArr);
													$shade_matchedArr=array_unique(explode(",",$shade_matched));
													$shade_matched_no=count($shade_matchedArr);
													
													?>
													<tr bgcolor="<? echo $bgcolor_dyeing;?>">
														<td align="center"><? if($BtbLtb==1) echo "Bulk To Bulk"; else if($BtbLtb==2) echo "Lab To Bulk"?></td>
														<td align="right"><? echo $total_batch_no;?></td>
														<td align="right"><? echo $shade_matched_no;?></td>
														<td align="right"><? echo number_format((($shade_matched_no)/$total_batch_no)*100,2);?></td>
													</tr>
													<?
													$sl++;
													$tot_total_batch += $total_batch_no;
													$tot_shade_matched += $shade_matched_no;
												}
												?>
											</tbody>
											<tfoot>
												<tr>
													<th align="left">Total =</th>
													<th><? echo $tot_total_batch;?></th>
													<th><? echo $tot_shade_matched;?></th>
													<th><? echo number_format(($tot_shade_matched/$tot_total_batch)*100,2);?></th>
												</tr>
											</tfoot>
										</table>
										<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
											<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
										</table>
										<table cellpadding="0" style="margin:5px;"  width="160" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
											
											<tbody bgcolor="#FFFFFF">
											<?
												$total_machine_capacity=0;
												foreach($total_running_machine_arr as $mid=>$val)
												{
													$total_machine_capacity+= $machine_capacity_arr[$mid];
													$mids.=$mid.',';
												}
												//echo $mids;
												$tot_machine=count($total_machine_arr);
												//echo $tot_machine.'XXd';
												$running_machine=count($total_running_machine_arr);
												$stop_machine=$tot_machine-$running_machine;
												$running_machine_percent=(($running_machine/$tot_machine)*100);
												$stop_machine_percent=(($stop_machine/$tot_machine)*100);
													$total_batch_weight=($ttl_batch_qty+$sub_ttl_batch_prod)*100;
													//$total_machine_capacity=($tot_machine_capacity+$sub_tot_machine_capacity)*1;
													$avg_load_percent=($total_batch_weight/($total_machine_capacity*$all_days_total_date));
											?>
												<tr>
													<td width="90" title="Total batch Weight(<? echo number_format($ttl_batch_qty,2).' + '.number_format($sub_ttl_batch_prod,2);?>)*100/(Total Running Machine Capacity(<? echo $total_machine_capacity;?>)*Working Day(<? echo $all_days_total_date;?>))">Avg. Load% :</td>
													<td align="right"><? 
													echo number_format($avg_load_percent,2);
													
													?></td>
													
												</tr>
												<tr>
												<td width="90" title="Tot Machine=<? echo $tot_machine;?>">Avg.Batch/Machine: </td>
												<td title="Total No Of Batch/Running Machine(<? echo $running_machine;?>)" align="right">
												<? $avg_batch_machine=$tot_total_batch/$running_machine; echo number_format($avg_batch_machine,2); ?>
												</td>
													
												</tr>
											</tbody>
										</table>
										<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
											<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
										</table>
										<table cellpadding="0"   style="margin:5px;" width="350" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<tr bgcolor="#E9F3FF">
											<td width="150">Total number of M/C running </td>
											<td width="80" align="right"><? echo $running_machine; ?></td>
											<td align="right" width="80"><? if( $running_machine_percent>0) echo number_format($running_machine_percent,2,'.','')." % "; ?></td>
										</tr>
										<tr bgcolor="#FFFFFF">
											<td width="150">Total number of M/C stop</td>
											<td align="right" width="80"><? echo $stop_machine; ?></td>
											<td align="right" width="80"><? if( $running_machine_percent>0) echo number_format($stop_machine_percent,2,'.','')." % "; ?></td>
										</tr>
										
									</table>
										
										
										<?
										$group_by=str_replace("'",'',$cbo_group_by);
										?>
										<div align="left">

											<table class="rpt_table" width="3320" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
												<caption> <b>Self batch  </b></caption>
												<thead>
													<tr>
														<th width="30">SL</th>
														<th width="80"><? echo $date_type_msg;?></th>
														<? if($group_by==3 || $group_by==0){ ?>
															<th width="80">M/C No</th>
														<? } if($group_by==2 || $group_by==0){ ?>
															<th width="80">Floor</th>  
														<? } if($group_by==1 || $group_by==0){ ?> 
															<th width="80">Shift Name</th>
														<? } ?>
														<th width="100">Buyer</th>
														<th width="100">Style Ref.</th>
														<th width="100">Season</th>
														<th width="120">Fso No</th>
														<th width="90">Fab.Booking No</th>
														<th width="70">Booking Type</th>
														<th width="100">Body Part</th>
														<th width="100">Fabrics Desc</th>
														<th width="70">Dia/ Width<br> Type</th>
														<th width="50">Lot No</th> 
														<th width="100">Yarn Info.</th> 
                                                        <th width="80">Color Type</th>
														<th width="80">Color Name</th>
														<th width="80">Color Range</th>
                                                        <th width="80">Dyes percent</th>
                                                        <th width="80">Shade%</th>
														<th width="90">Batch No</th>
														<th width="40">Extn. No</th>
														<th width="70">Fabric Wgt.</th>
														<th width="70">Trims Wgt.</th>
														<th width="70">Batch Wgt.</th>
														<th width="70">M/C <br>Capacity.</th>
														<th width="70">Loading %.</th>
														<th width="75">Load Date<br> Time</th>
														<th width="75">UnLoad <br>Date Time</th>
														<th width="60">Time Used</th>
														<th width="60">BTB<br>LTB</th>
														<th width="100">Dyeing <br>Fab. Type</th>
														<th width="100">Result</th>
														<th width="80">Dyeing Process</th>
														<th width="80"><p>Dyeing Re <br>Process Name</p></th>

														<th width="60">Hour <br>Load Meter</th>
														<th width="60">Hour <br>unLoad Meter</th>
														<th width="60">Total Time</th>

														<th width="60">Water <br>Loading Flow</th> 
														<th width="60">Water <br>UnLoading Flow</th>
														<th width="70">Water Cons.</th>

														<th width="">Remark</th>
													</tr>
												</thead>
											</table>
											<div style=" max-height:350px; width:3340px; overflow-y:scroll;;" id="scroll_body">
												<table class="rpt_table" id="table_body" width="3320" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
													<tbody>
														<? 
														$i=1; $total_water_cons_load=0;$total_water_cons_unload=$tot_machine_capacity=0;$grand_total_production_qnty=$sub_grand_tot_trims_qnty=0;
														$batch_chk_arr=array(); $group_by_arr=array();$tot_trims_qnty=0;$trims_check_array=array();
														foreach($data_array_batch as $batch_id=>$batch_data_arr)
														{
															$b=1;
															foreach ($batch_data_arr as $prod_id => $row) 
															{

																
																$batch_td_span = $batch_row_span_arr[$batch_id];
																//echo $batch_td_span.',';
															 //echo $row['color_type'].'ff';;
																
																if ($i%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";

																if($group_by!=0)
																{
																	if($group_by==1)
																	{
																		$group_value=$row['floor_id'];
																		$group_name="Floor";
																		$group_dtls_value=$floor_arr[$row['floor_id']];
																	}
																	else if($group_by==2)
																	{
																		$group_value=$row['shift_name'];
																		$group_name="Shift";
																		$group_dtls_value=$shift_name[$row['shift_name']];
																	}
																	else if($group_by==3)
																	{
																		$group_value=$row['machine_id'];
																		$group_name="machine";
																		$group_dtls_value=$machine_arr[$row['machine_id']];
																	}
																	if (!in_array($group_value,$group_by_arr) )
																	{
																		if($k>1)
																		{ 	
																			?>
																			<tr class="tbl_bottom">
																				<td width="30">&nbsp;</td>
																				<td width="80">&nbsp;</td>

																				<? if($group_by==3 || $group_by==0){ ?>
																					<td width="80">&nbsp;</td> 
																				<? } ?>
																				<? if($group_by==2 || $group_by==0){ ?>
																					<td width="80">&nbsp;</td> 
																				<? } if($group_by==1 || $group_by==0){ ?>  
																					<td width="80">&nbsp;</td>
																				<? } ?>
																				<td width="100" align="center"><p></p></td>
																				<td width="100" align="center"><p></p></td>
																				<td width="100" align="center"><p></p></td>
																				<td width="120" align="center"><p></p></td>
																				<td width="90"></td>
																				<td width="70" align="center"></td>
																				<td width="100"></td>
																				<td width="100"></td>
																				<td width="70"><p></p></td>
																				<td width="50"><p></p></td>
																				<td width="100"><p></p></td>
                                                                                <td width="80"><p></p></td>
																				<td width="80"><p></p></td>
																				<td width="80"><p></p></td>
                                                                                <td width="80"><p></p></td>
                                                                                <td width="80"><p></p></td>
																				<td width="90"><p></p></td>
																				<td width="40"><p></p></td>
																				<td align="right" width="70"><? echo  number_format($sub_production_qnty,2);?></td>
																				<td align="right" width="70"></td>
																				<td align="right" width="70"><? echo number_format($batch_qnty_tot,2);?></td>
																				
																				<td align="right" width="70"></td>
																				<td align="right" width="70"></td>
																				
																				<td width="75"><p></p></td>
																				<td width="75"><p></p></td>
																				<td align="center" width="60"> </td>
																				<td align="center" width="60"></td>
																				<td align="center" width="100"></td>
																				<td align="center" width="100"></td>
																				<td align="center" width="80"></td>
																				<td align="center" width="80"> </td>
																				<td width="60" align="right"><p></p></td>
																				<td width="60" align="right"><p></p></td>
																				<td width="60" align="right" ><p></p></td>
																				<td width="60" align="right"><p></p></td>
																				<td width="60" align="right"><p></p></td>
																				<td align="right" width="70" ></td>
																				<td align="center"></td>
																			</tr>                                
																			<?
																			$batch_qnty_tot=0; $trims_btq=0;$sub_production_qnty=0;
																		}
																		?>
																		<tr bgcolor="#EFEFEF">
																			<td colspan="41" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
																		</tr>
																		<?
																		$group_by_arr[]=$group_value;            
																		$k++;
																	}					
																}

																$batch_qnty = $row["prod_qnty_tot"]+$row["total_trims_weight"];
																$batch_weight=$row['batch_weight'];
																$water_cons_unload=$row['water_flow_meter'];
																$water_cons_load=$water_flow_arr[$batch_id];
																$load_hour_meter=$load_hour_meter_arr[$batch_id];
																$water_cons_diff=($water_cons_unload-$water_cons_load)/$batch_weight*1000;

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
																$BatchTotal 				= $batchTotal_arr[$batch_id]['batch_ratio_total'];
																//$chemicalsTotal 			= $batch_item_ratio_total_arr[$batch_id][5]['ratio_total'];
																$dyesTotal 					= $batch_item_ratio_total_arr[$batch_id][6]['ratio_total'];
																$dyesReqTotal 					= $batch_item_ratio_dyes_arr[$batch_id]['ratio_total'];
																//$auxiChemicalsTotal			= $batch_item_ratio_total_arr[$batch_id][7]['ratio_total'];
																
																$shade_percentageOfColor = 0;
																if($dyesTotal>0 && $BatchTotal>0 )
																{
																//	echo $row[csf('batch_id')].'=='.$dyesTotal.'TTTTTTTT'.$BatchTotal;
																 $shade_percentageOfColor=($dyesTotal/$BatchTotal)*100;
																}
					
																?>
																<tr bgcolor="<? echo $bgcolor_dyeing; ?>"  id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor_dyeing; ?>')">
																	<td width="30"><? echo $i; ?></td>
																	<td width="80"><p><? echo change_date_format($date_type_cond); $unload_date=$row['process_end_date']; ?></p></td>
																	<? if($group_by==3 || $group_by==0){ ?>
																		<td align="center" width="80"><p style="word-break:break-all;width:80px"><? echo $machine_arr[$row['machine_id']]; ?></p></td>
																	<? } if($group_by==2 || $group_by==0){ ?>
																		<td width="80"><p style="word-break:break-all"><? echo $floor_arr[$row['floor_id']]; ?></p></td>
																	<? } if($group_by==1 || $group_by==0){ ?>
																		<td width="80" align="center"><p style="word-break:break-all"><? echo $shift_name[$row['shift_name']]; ?></p></td>
																	<? } ?>

																	<td width="100" align="center">
																		<p style="word-break:break-all">
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
																		</p>
																	</td>
																	<td width="100" align="center"><p style="width:100px; word-wrap:break-word;"><? echo $row['style_ref_no']; ?></p></td>
																	<td width="100" align="center"><p style="word-break:break-all"><? echo $row['season']; ?></p></td>
																	<td width="120" align="center"><p style="width:118px; word-wrap:break-word;"><? echo $row['sales_no']; ?></p></td>
																	<td width="90"><p style="width:70px; word-wrap:break-word;"><? echo $row['booking_no']; ?></p></td>
																	<td width="70" align="center"><p style="width:70px; word-wrap:break-word;"><? echo $booking_type_arr[$row['booking_no']]; ?></p></td>
																	<td width="100" style="width:100px;"><p style="width:100px; word-wrap:break-word;"><?  echo $row['body_part']; ?></p></td>
																	<td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $row['item_description']; ?></div></td>
																	<td width="70" style="width:70px; word-wrap:break-word;"><p style="width:70px; word-wrap:break-word;"> <? echo $fabric_typee[$row['width_dia_type']]; ?></p></td>								<td width="50" style="width:50px; " title="<? echo implode(", ",array_filter(array_unique($row["barcode_no"])));?>">
																		<p style="width:50px; word-wrap:break-word;">
																			<? $bar_lot_arr= array();$yarnlots="";$yarn_types="";$yarn_count_name="";$yarn_comp_type1st="";
																			$bar_lot_arr =array_filter(array_unique($row["barcode_no"]));
																			if(count($bar_lot_arr)>0){
																				foreach ($bar_lot_arr as $bcode) 
																				{
																					$yarnlots .= chop($yarn_lot_arr[$bcode],",").",";
																					
																					$yarn_types .= chop($yarn_data_arr[$bcode]['yarn_type'],",").",";
																					$yarn_comp_type1st .= chop($yarn_data_arr[$bcode]['yarn_comp_type1st'],",").",";
																					$yarn_count_name .= chop($yarn_data_arr[$bcode]['yarn_count_id'],",").",";
																					$colors .= chop($yarn_data_arr[$bcode]['color'],",").",";																				}
																				echo implode(", ",array_filter(array_unique(explode(",", $yarnlots))));  
																			}
																			$yarn_types=implode(", ",array_filter(array_unique(explode(",", $yarn_types))));
																			$yarn_count_name=implode(", ",array_filter(array_unique(explode(",", $yarn_count_name))));
																			$yarn_comp_type1st=implode(", ",array_filter(array_unique(explode(",", $yarn_comp_type1st))));
																			$colors=implode(", ",array_filter(array_unique(explode(",", $colors))));
																			?>
																		</p>
																	</td>

																	<td width="100" style="width:100px; "><p style="width:100px; word-wrap:break-word;"><? echo $yarn_count_name.', '.$yarn_comp_type1st.', '.$yarn_types; ?></p></td>
																<td width="80" style="width:80px; word-wrap:break-word;"><p style="word-break:break-all"><? echo $color_type[$row['color_type']];; ?></p></td>
                                                                 <td width="80" style="width:80px; word-wrap:break-word;"><p style="width:80px; word-wrap:break-word;"><? echo $color_library[$row['color_id']]; ?></p></td>
															<td width="80" style="width:80px; word-wrap:break-word;"><p style="width:80px; word-wrap:break-word;"><? echo $color_range[$row['color_range_id']]; ?></p></td>
                                                           
                                                           
                                                            
																	<? if($b==1){ ?>
                                                                     <td width="80" rowspan="<? echo $batch_td_span?>" style="width:80px; word-wrap:break-word;"><p><? echo number_format($dyesReqTotal,2); ?></p></td>
																		 <td width="80" rowspan="<? echo $batch_td_span?>" title="(dyesTotalRatio(<? echo $dyesTotal;?>)/BatchTotalRatio(<? echo $BatchTotal;?>))*100;" style="width:80px; word-wrap:break-word;"><p><? echo number_format($shade_percentageOfColor,2); ?></p></td>
                                                                        <td width="90" rowspan="<? echo $batch_td_span?>"><p style="width:90px; word-wrap:break-word;"><? echo $row['batch_no']; ?></p></td>
																		<td width="40" rowspan="<? echo $batch_td_span?>"><p><? echo $row['extention_no']; ?></p></td>
																		<? } ?>
																		<td align="right" width="70"><? echo number_format($row['production_qnty'],2); $grand_total_production_qnty += $row['production_qnty']; $sub_production_qnty += $row['production_qnty'];?></td>

																		<? if($b==1){?>
																			<td align="right" width="70" rowspan="<? echo $batch_td_span?>"><? echo number_format($row["total_trims_weight"],2);  ?></td>
																			<td align="right" width="70" rowspan="<? echo $batch_td_span?>"><? echo number_format($batch_qnty,2);  ?></td>
																			<?
																			$batch_qnty_tot+=$batch_qnty;
																			$grand_total_batch_qty+=$batch_qnty;
																			$trims_btq+=$row["total_trims_weight"];
																			$sub_grand_tot_trims_qnty+=$row["total_trims_weight"];
																			$tot_machine_capacity+=$machine_capacity_arr[$row['machine_id']];

																			?>
																			<td align="right" width="70" rowspan="<? echo $batch_td_span?>"><? echo number_format($machine_capacity_arr[$row['machine_id']],2);  ?>
																			</td>
																			<td align="right" width="70" title="Batch Weight/Machine Capacity*100" rowspan="<? echo $batch_td_span?>"><? echo number_format(($batch_qnty/$machine_capacity_arr[$row['machine_id']]*100),2);  ?>
																			</td>
																			<td width="75" rowspan="<? echo $batch_td_span?>" title="<? echo $load_date[$batch_id];?>"><p  style="width:75px; word-wrap:break-word;"><? $load_t=$load_hr[$batch_id].':'.$load_min[$batch_id]; 
																			echo  ($load_date[$batch_id] == '0000-00-00' || $load_date[$batch_id] == '' ? '' : change_date_format($load_date[$batch_id])).' <br> '.$load_t;

																			?></p></td>
																			<td width="75" rowspan="<? echo $batch_td_span?>"><p  style="width:75px; word-wrap:break-word;"><? $hr=strtotime($unload_date,$load_t); $min=($row['end_minutes'])-($load_min[$batch_id]);
																			echo  ($row['process_end_date'] == '0000-00-00' || $row['process_end_date'] == '' ? '' : change_date_format($row['process_end_date'])).'<br>'.$unload_time=$row['end_hours'].':'.$row['end_minutes']; ?></p>
																		</td>
																		<td align="center" width="60" rowspan="<? echo $batch_td_span?>">
																			<?     
																			$new_date_time_unload=($unload_date.' '.$unload_time.':'.'00');
																			$new_date_time_load=($load_date[$batch_id].' '.$load_t.':'.'00');
																			$total_time=datediff(n,$new_date_time_load,$new_date_time_unload);
																			echo floor($total_time/60).":".$total_time%60;
																			?>
																		</td>
																		<td align="center" width="60" rowspan="<? echo $batch_td_span?>"><? echo $ltb_btb[$row["ltb_btb_id"]];?></td>

																		<td align="center" width="100" rowspan="<? echo $batch_td_span?>"><p width:100px; word-wrap:break-word;><? 
																		echo $fabric_type_for_dyeing[$row['fabric_type']];?></p> </td>
																		<td align="center" width="100" style="width:100px; word-wrap:break-word;" rowspan="<? echo $batch_td_span?>"><p><? echo $dyeing_result[$row['result']]; ?></p> </td>
																		<td align="center" width="80" style="width:80px; word-wrap:break-word;" rowspan="<? echo $batch_td_span?>"><? echo $conversion_cost_head_array[$row["process_id"]]?></td>
																		<td align="center" width="80" rowspan="<? echo $batch_td_span?>" title="<? echo $row["add_tp_strip"];?>">
																			<? 
																			if($row["process_id"]==148 && $row["add_tp_strip"] ==1)
																			{
																				echo "Re-Wash";
																			}
																			else{
																				echo $dyeing_re_process[$row["add_tp_strip"]];
																			} 
																			?>
																		</td>
																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo number_format($load_hour_meter,2);  ?></p></td>
																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo number_format($row['hour_unload_meter'],2);  ?></p></td>
																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo number_format($row['hour_unload_meter']-$load_hour_meter,2);  ?></p></td>

																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo $water_cons_load;  ?></p></td>
																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo $water_cons_unload;  ?></p></td>
																		<td align="right" width="70" rowspan="<? echo $batch_td_span?>" title="<? echo "Water Cons Unload-Water Cons Load*1000/Batch Qnty"?>" ><? echo number_format((($water_cons_unload-$water_cons_load)*1000)/$row['batch_qnty'],2);  ?></td>

																		<td align="center" rowspan="<? echo $batch_td_span?>"><p><? echo $row['remarks']; ?></p> </td>
																		<? } ?>
																	</tr>
																	<? 
																	$i++;$b++;


																	$total_water_cons_load+=$water_cons_load;
																	$total_water_cons_unload+=$water_cons_unload;


																}
															}
															if($group_by!=0)
															{
																?>
																<tr class="tbl_bottom">
																	<td width="30">&nbsp;</td>
																	<td width="80">&nbsp;</td>

																	<? if($group_by==3 || $group_by==0){ ?>
																		<td width="80">&nbsp;</td> 
																	<? } ?>
																	<? if($group_by==2 || $group_by==0){ ?>
																		<td width="80">&nbsp;</td> 
																	<? } if($group_by==1 || $group_by==0){ ?>  
																		<td width="80">&nbsp;</td>
																	<? } ?>
																	<td width="100" align="center"><p></p></td>
																	<td width="100" align="center"><p></p></td>
																	<td width="100" align="center"><p></p></td>
																	<td width="120" align="center"><p></p></td>
																	<td width="90"></td>
																	<td width="70" align="center"></td>
																	<td width="100"></td>
																	<td width="100"></td>
																	<td width="70"><p></p></td>
																	<td width="50"><p></p></td>
																	<td width="100"><p></p></td>
																	<td width="80"><p></p></td>
																	<td width="80"><p></p></td>
																	<td width="80"><p></p></td>
                                                                    <td width="80"><p></p></td>
                                                                    <td width="80"><p></p></td>
																	<td width="90"><p></p></td>
																	<td width="40"><p></p></td>
																	<td align="right" width="70"><? echo  number_format($sub_production_qnty,2);?></td>
																	<td align="right" width="70"></td>
																	<td align="right" width="70"><? echo number_format($batch_qnty_tot,2);?></td>
																	<td width="70"><p></p></td>
																	<td width="70"><p></p></td>
																	<td width="75"><p></p></td>
																	<td width="75"><p></p></td>
																	<td align="center" width="60"> </td>
																	<td align="center" width="60"></td>
																	<td align="center" width="100"></td>
																	<td align="center" width="100"></td>
																	<td align="center" width="80"></td>
																	<td align="center" width="80"> </td>
																	<td width="60" align="right"><p></p></td>
																	<td width="60" align="right"><p></p></td>
																	<td width="60" align="right" ><p></p></td>
																	<td width="60" align="right"><p></p></td>
																	<td width="60" align="right"><p></p></td>
																	<td align="right" width="70" ></td>
																	<td align="center"></td>
																</tr> 
																<? 
															}

															?>            
														</tbody>
														<tfoot>
															<tr class="tbl_bottom">
																<td width="30">&nbsp;</td>
																<td width="80">&nbsp;</td>

																<? if($group_by==3 || $group_by==0){ ?>
																	<td width="80">&nbsp;</td> 
																<? } ?>
																<? if($group_by==2 || $group_by==0){ ?>
																	<td width="80">&nbsp;</td> 
																<? } if($group_by==1 || $group_by==0){ ?>  
																	<td width="80">&nbsp;</td>
																<? } ?>
																<td width="100" align="center"><p></p></td>
																<td width="100" align="center"><p></p></td>
																<td width="100" align="center"><p></p></td>
																<td width="120" align="center"><p></p></td>
																<td width="90"></td>
																<td width="70" align="center"></td>
																<td width="100"></td>
																<td width="100"></td>
																<td width="70"><p></p></td>
																<td width="50"><p></p></td>
																<td width="100"><p></p></td>
																<td width="80"><p></p></td>
																<td width="80"><p></p></td>
																<td width="80"><p></p></td>
                                                                <td width="80"><p></p></td>
                                                                <td width="80"><p></p></td>
																<td width="90"><p></p></td>
																<td width="40"><p></p></td>
																<td align="right" width="70"><? echo number_format($grand_total_production_qnty,2);?></td>
																<td align="right" width="70"><? echo number_format($sub_grand_tot_trims_qnty,2);?></td>
																<td align="right" width="70"><? echo number_format($grand_total_batch_qty,2);?></td>
																<td width="70"><p><? echo number_format($tot_machine_capacity,2);?></p></td>
																<td width="70"><p></p></td>
																<td width="75"><p></p></td>
																<td width="75"><p></p></td>
																<td align="center" width="60"> </td>
																<td align="center" width="60"></td>
																<td align="center" width="100"></td>
																<td align="center" width="100"></td>
																<td align="center" width="80"></td>
																<td align="center" width="80"> </td>
																<td width="60" align="right"><p></p></td>
																<td width="60" align="right"><p></p></td>
																<td width="60" align="right" ><p></p></td>
																<td width="60" align="right"><p></p></td>
																<td width="60" align="right"><p></p></td>
																<td align="right" width="70" ></td>
																<td align="center"></td>
															</tr> 
														</tfoot>
													</table>
												</div>
											</div>
											<br/>
											<? 
										}
									}
						if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
						{
							if (count($subbatchdata)>0)
							{
								$group_by=str_replace("'",'',$cbo_group_by);
								if(str_replace("'",'',$cbo_batch_type)==2)
								{
								?>
								<table cellpadding="0"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
									<thead>
										<tr>
											<th colspan="3">Production Summary</th>
										</tr>
										<tr>
											<th width="130">Details </th> 
											<th width="120">Prod. Qty. </th>
											<th>%</th>
										</tr>
									</thead>
									<tbody>
										<tr bgcolor="#E9F3FF">
											<td>Total RFT</td>
											<td align="right"><? echo number_format($total_rft_qnty,2);?></td>
											<td align="right"><? echo number_format(($total_rft_qnty*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty),2);?></td>
										</tr>
										<tr bgcolor="#FFFFFF">
											<td>Total Adding</td> 
											<td align="right"><? echo number_format($total_adding_qnty,2);?></td>
											<td align="right"><? echo number_format(($total_adding_qnty*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty),2); ?></td>
										</tr>
                                        <tr bgcolor="#FFFF00">
											<td>Total:</td> 
											<td align="right"><? 
											$total_rft_add=0; $total_rft_add_per=0;
											$total_rft_add=$total_rft_qnty+$total_adding_qnty;
											$total_rft_add_per=(($total_rft_qnty*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty))+(($total_adding_qnty*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty));
											echo number_format($total_rft_add,2);?></td>
											<td align="right"><? echo number_format($total_rft_add_per,2);?></td>
										</tr>
										<tr bgcolor="#E9F3FF">
											<td>Total Re-Process</td> 
											<td align="right"><? echo number_format($total_reprocess_qty,2);?></td> 
											<td align="right"><? echo number_format(($total_reprocess_qty*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty),2);?></td> 
										</tr>
										<tr bgcolor="#EBEBEB">
											<td align="right"><b>Grand Total:</b></td> 
											<td align="right">
												<b><? echo number_format($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty,2); ?></b>
											</td> 
											<td align="right"><b><? echo number_format((($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty)*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty),2);?></b></td>  
											</tr>
											<tr bgcolor="#FFFFFF">
												<td><b>Avg. Prod. Per Day</b></td> 
												<td align="right" title="<? echo "days=".implode(",",array_filter(array_unique($production_date_count)));?>">
													<b><? 
													echo number_format(($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty)/count(array_filter(array_unique($production_date_count))),2);
													?></b>	
												</td> 
												<td></td> 
											</tr>
											<? 
											$ftsl=2;
											foreach($fabric_type_production_arr as $fabType=>$val)
											{ 
												if ($ftsl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													<td><? echo $fabric_type_for_dyeing[$fabType];?></td> 
													<td align="right"><? echo number_format($val,2);?></td> 
													<td align="right"><? echo number_format(($val/$fabric_type_production_total)*100,2);?></td> 
												</tr>
												<?
												$ftsl++;
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th>Total</th> 
												<th align="right"><? echo number_format($fabric_type_production_total,2);?></th> 
												<th align="right"><? echo number_format(100,2);?></th> 
											</tr>
										</tfoot>
									</table>

									<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
										<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
									</table>

									<table cellpadding="0"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<thead>
											<tr>
												<th colspan="4">Re-Process Summary (Buyer Wise)</th>
											</tr>
											<tr>
												<th width="40">Sl </th> 
												<th width="100">Buyer</th>
												<th width="100">Batch Total</th>
												<th>%</th>
											</tr>
										</thead>
										<tbody>
											<? 
											$sl=1;
											foreach($buyer_wise_summary as $buyer=> $bs)
											{
												if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													<td align="center"><? echo $sl;?></td>
													<td align="center"><? echo $buyer_arr[$buyer];?></td>
													<td align="right"><? echo number_format($bs,2);?></td>
													<td align="right"><? echo number_format(($bs/$buyer_wise_summary_batch_total)*100,2);?></td>
												</tr>
												<?
												$sl++;
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="2">Total </th>
												<th><? echo number_format($buyer_wise_summary_batch_total,2);?></th>
												<th><? if ($buyer_wise_summary_batch_total>0) echo "100.00"; else echo "0.00";?></th>
											</tr>
										</tfoot>
									</table>
									<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
										<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
									</table>
									<table cellpadding="0"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<thead>
											<tr>
												<th colspan="5">Color Range wise Summary</th>
											</tr>
											<tr>
												<th width="40">Sl </th> 
												<th width="100">Color Range</th>
												<th width="100">Quantity (Kgs)</th>
												<th  width="50">Percentage%</th>
												<th>No. Of Batches</th>
											</tr>
										</thead>
										<tbody>
											<? 
											$sl=1;$tot_batch_qty=$tot_no_of_batch=$tot_total_trims_weight=0;
											foreach($color_wise_batch_arr as $color_rang_id=> $val)
											{
												$total_color_qty+=$val['batch_qnty'];
											}
											//print_r($color_wise_batch_arr);
											foreach($color_wise_batch_arr as $color_rang_id=> $val)
											{
												if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												$batch_ids=rtrim($val[batch_ids],',');
												$batch_ids_arr=array_unique(explode(",",$batch_ids));
												$tot_total_trims_weight+=$val['total_trims_weight'];
												//echo count($batch_ids_arr).',';
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													<td align="center"><? echo $sl;?></td>
													<td align="center"><? echo $color_range[$color_rang_id];?></td>
													<td align="right"><? echo number_format($val['batch_qnty'],2);?></td>
													<td align="right" title="Tot batch Qty=<? echo $total_color_qty;?>">
													<? echo number_format(($val['batch_qnty']/($total_color_qty))*100,2);?></td>
													<td align="right"><? echo count($batch_ids_arr);?></td>
												</tr>
												<?
												$sl++;
												$tot_batch_qty+=$val['batch_qnty'];
												$tot_no_of_batch+=count($batch_ids_arr);
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="2">Total </th>
												<th><? echo number_format($tot_batch_qty,2);?></th>
												<th><? echo number_format(($tot_batch_qty/($total_color_qty))*100,2);?></th>
												<th><? echo number_format($tot_no_of_batch,0);?></th>
											</tr>
										</tfoot>
									</table>
									
									

									<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
										<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
									</table>

									<table cellpadding="0"  width="640" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<thead>
											<tr>
												<th colspan="8">Summary Total (Shade Match)</th>
											</tr>
											<tr>
												<th width="100">Buyer</th>
												<th width="100">Self batch</th> 
												<th width="100">Smpl. Batch With Order</th>
												<th width="60">Smpl. Batch Without Order</th>
												<th width="60">Trims Weight</th>
												<th width="60">Inbound subcontract</th>
												<th width="100">Buyer Total</th>
												<th>%</th>
											</tr>
										</thead>
										<tbody>
											<? 
											$sl=1;
											foreach($shade_matched_wise_summary as $buyer=> $val)
											{
												if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												$tot_buyer_match_shade_subcon = $val["subcon"];
												$tot_buyer_match_shade = ($val["self"]+$val["sm"]+$val["smn"]+$val["trim"])+$tot_buyer_match_shade_subcon;
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													<td align="center"><? echo $buyer_arr[$buyer];?></td>
													<td align="right"><? echo number_format($val["self"],2); ?></td>
													<td align="right"><? echo number_format($val["sm"],2); ?></td>
													<td align="right"><? echo number_format($val["smn"],2); ?></td>
													<td align="right"><? echo number_format($val["trim"],2); ?></td>
													<td align="right"><? echo number_format($tot_buyer_match_shade_subcon,2); ?></td>
													<td align="right"><? echo number_format($tot_buyer_match_shade,2);?></td>
													<td align="right"><? echo number_format(($tot_buyer_match_shade/$shade_matched_buyer_total)*100,2);?></td>
												</tr>
												<?
												$sl++;
												$total_self += $val["self"];
												$total_sm += $val["sm"];
												$total_smn += $val["smn"];
												$total_trim += $val["trim"];
												$total_subcon += $tot_buyer_match_shade_subcon;
												$total_tot_buyer_match_shade += $tot_buyer_match_shade;
												$total_summury_shade_matched +=($tot_buyer_match_shade/$shade_matched_buyer_total)*100;

												$grand_total_self += $val["self"];
												$grand_total_sm += $val["sm"];
												$grand_total_smn += $val["smn"];
												$grand_total_trim += $val["trim"];
												$grand_total_subcon += $tot_buyer_match_shade_subcon;
												$grand_total_buyer += $tot_buyer_match_shade;

											}
											unset($shade_matched_wise_summary);
											?>
											<tr style="background-color: #eee;font-weight: bold;">
												<td width="100" align="right">Total</th>
													<td width="100" align="right"><? echo number_format($total_self,2);?></td> 
													<td width="100" align="right"><? echo number_format($total_sm,2);?></td>
													<td width="60" align="right"><? echo number_format($total_smn,2);?></td>
													<td width="60" align="right"><? echo number_format($total_trim,2);?></td>
													<td width="60"><? echo number_format($grand_total_subcon,2);?></td>
													<td width="100" align="right"><? echo number_format($total_tot_buyer_match_shade,2);?></td>
													<td align="right"><? echo number_format($total_summury_shade_matched,2);?></td>
												</tr>
											</tbody>
											<thead>
												<tr>
													<th colspan="8">Summary Total (Shade Not Match)</th>
												</tr>
											</thead>
											<tbody>
												<?
												foreach($shade_not_matched_wise_summary as $buyer=> $val)
												{
													if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
													$tot_buyer_match_not_shade_subcon = $val["subcon"];
													$tot_buyer_match_not_shade = ($val["self"]+$val["sm"]+$val["smn"]+$val["trim"])+$tot_buyer_match_not_shade_subcon;
													?>
													<tr bgcolor="<? echo $bgcolor_dyeing;?>">
														<td align="center"><? echo $buyer_arr[$buyer];?></td>
														<td align="right"><? echo number_format($val["self"],2);?></td>
														<td align="right"><? echo number_format($val["sm"],2);?></td>
														<td align="right"><? echo number_format($val["smn"],2);?></td>
														<td align="right"><? echo number_format($val["trim"],2);?></td>
														<td align="right"><? echo number_format($tot_buyer_match_not_shade_subcon,2); ?></td>
														<td align="right"><? echo number_format($tot_buyer_match_not_shade,2);?></td>
														<td align="right"><? echo number_format(($tot_buyer_match_not_shade/$shade_not_matched_buyer_total)*100,2);?></td>
													</tr>
													<?
													$sl++;

													$total_self_not_matched += $val["self"];
													$total_sm_not_matched += $val["sm"];
													$total_smn_not_matched += $val["smn"];
													$total_trim_not_matched += $val["trim"];
													$total_subcon_not_matched += $tot_buyer_match_not_shade_subcon;
													$total_tot_buyer_match_shade_not_matched += $tot_buyer_match_not_shade;
													$total_summury_shade_matched_not_matched +=($tot_buyer_match_not_shade/$shade_not_matched_buyer_total)*100;

													$grand_total_self += $val["self"];
													$grand_total_sm += $val["sm"];
													$grand_total_smn += $val["smn"];
													$grand_total_trim += $val["trim"];
													$grand_total_subcon+= $tot_buyer_match_not_shade_subcon;
													$grand_total_buyer += $tot_buyer_match_not_shade;
												}
												?>
											</tbody>
											<tfoot>
												<tr bgcolor="#EBEBEB">
													<th align="center">Total</th>
													<th align="right"><? echo number_format($total_self_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_sm_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_smn_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_trim_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_subcon_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_tot_buyer_match_shade_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_summury_shade_matched_not_matched,2);?></th>
												</tr>
												<tr bgcolor="#EBEBEB">
													<th align="center">Grand Total</th>
													<th align="right"><? echo number_format($grand_total_self,2);?></th>
													<th align="right"><? echo number_format($grand_total_sm,2);?></th>
													<th align="right"><? echo number_format($grand_total_smn,2);?></th>
													<th align="right"><? echo number_format($grand_total_trim,2);?></th>
													<th align="right"><? echo number_format($grand_total_subcon,2);?></th>
													<th align="right"><? echo number_format($grand_total_buyer,2);?></th>
													<th align="right"><? ?></th>
												</tr>
											</tfoot>
										</table>

										<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
											<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
										</table>

										<table cellpadding="0"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
											<thead>
												<tr>
													<th colspan="3"></th>
												</tr>
												<tr>
													<th width="130">Re-Processed</th> 
													<th width="100">Batch Qnty</th>
													<th width="">%</th>
												</tr>
											</thead>
											<tbody>
												<? 
												$sl=1;
												foreach($dyeing_process_wise_batch_qnty_arr as $processId=> $val)
												{
													if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor_dyeing;?>">
														<td align="center"><? echo $conversion_cost_head_array[$processId];?></td>
														<td align="right"><? echo number_format($val,2);?></td>
														<td align="right"><? echo number_format(($val/$dyeing_process_wise_batch_qnty_total)*100,2);?></td>
													</tr>
													<?
													$sl++;
												}
												?>
											</tbody>
											<tfoot>
												<tr>
													<th>Total </th>
													<th><? echo number_format($dyeing_process_wise_batch_qnty_total,2);?></th>
													<th>100.00</th>
												</tr>
											</tfoot>
										</table>

										<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
											<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
										</table>

										<table cellpadding="0"  width="400" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
											<thead>
												<tr>
													<th colspan="4"></th>
												</tr>
												<tr>
													<th width="130">RFT + Adding </th> 
													<th width="100">No Of Batch </th>
													<th width="100">Shade Matched</th>
													<th width="">%</th>
												</tr>
											</thead>
											<tbody>
												<? 
												$sl=1;
												foreach($btb_shade_matched as $BtbLtb=> $val)
												{
													if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor_dyeing;?>">
														<td align="center"><? if($BtbLtb==1) echo "Bulk To Bulk"; else if($BtbLtb==2) echo "Lab To Bulk"?></td>
														<td align="right"><? echo $val["total_batch"];?></td>
														<td align="right"><? echo $val["shade_matched"];?></td>
														<td align="right"><? echo number_format((($val["shade_matched"])/$val["total_batch"])*100,2);?></td>
													</tr>
													<?
													$sl++;
													$tot_total_batch += $val["total_batch"];
													$tot_shade_matched += $val["shade_matched"];
												}
												?>
											</tbody>
											<tfoot>
												<tr>
													<th align="left">Total =</th>
													<th><? echo $tot_total_batch;?></th>
													<th><? echo $tot_shade_matched;?></th>
													<th><? echo number_format(($tot_shade_matched/$tot_total_batch)*100,2);?></th>
												</tr>
											</tfoot>
										</table>
										<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
											<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
										</table>
										<table cellpadding="0" style=""  width="160" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
											
											<tbody bgcolor="#FFFFFF">
											<?
												$tot_machine=count($total_machine_arr);
												//echo $tot_machine.'XXd';
												$running_machine=count($total_running_machine_arr);
												$stop_machine=$tot_machine-$running_machine;
												$running_machine_percent=(($running_machine/$tot_machine)*100);
												$stop_machine_percent=(($stop_machine/$tot_machine)*100);
													$total_batch_weight=($total_color_wise_batch_qty+$sub_total_color_wise_batch_qty)*100;
													$total_machine_capacity=($tot_machine_capacity+$sub_tot_machine_capacity)*1;
													$avg_load_percent=($total_batch_weight/($total_machine_capacity*$all_days_total_date));
											?>
												<tr>
													<td width="90" title="Total batch Weight(<? echo $total_color_wise_batch_qty.'+ '.$sub_total_color_wise_batch_qty;?>)*100/(Total Running Machine Capacity(<? echo $total_machine_capacity;?>)*Working Day(<? echo $all_days_total_date;?>))">Avg. Load% :</td>
													<td align="right"><? 
													echo number_format($avg_load_percent,2);
													
													?></td>
													
												</tr>
												<tr>
												<td width="90" title="Tot Machine=<? echo $tot_machine;?>">Avg.Batch/Machine: </td>
												<td title="Total No Of Batch/Running Machine(<? echo $running_machine;?>)" align="right">
												<? $avg_batch_machine=$tot_total_batch/$running_machine; echo number_format($avg_batch_machine,2); ?>
												</td>
													
												</tr>
											</tbody>
										</table>
										<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
											<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
										</table>
										<table cellpadding="0"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<tr bgcolor="#E9F3FF">
											<td>Total number of M/C running </td>
											<td width="70" align="right"><? echo $running_machine; ?></td>
											<td align="right" width="70"><? if( $running_machine_percent>0) echo number_format($running_machine_percent,2,'.','')." % "; ?></td>
										</tr>
										<tr bgcolor="#FFFFFF">
											<td>Total number of M/C stop</td>
											<td width="70" align="right"><? echo $stop_machine; ?></td>
											<td width="70" align="right"><? if( $running_machine_percent>0) echo number_format($stop_machine_percent,2,'.','')." % "; ?></td>
										</tr>
										
									</table>
									
                                        
                                        <? } ?>
										<div align="left">

											<table class="rpt_table" width="2840" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
												<caption> <b>Inbound subcontract</b></caption>
												<thead>
													<tr>
														<th width="30">SL</th>
														<th width="80"><? echo $date_type_msg;?></th>
														<? if($group_by==3 || $group_by==0){ ?>
															<th width="80">M/C No</th>
														<? } if($group_by==2 || $group_by==0){ ?>
															<th width="80">Floor</th>  
														<? } if($group_by==1 || $group_by==0){ ?> 
															<th width="80">Shift Name</th>
														<? } ?>
														<th width="100">Buyer</th>
														<th width="100">Style Ref.</th>
														<th width="100">Season</th>
														<th width="80">Fso No</th>
														<th width="90">Fabric Booking No</th>
														<th width="70">Booking Type</th>
														<th width="100">Fabrics Desc</th>
														<th width="70">Dia/Width Type</th>
														<th width="50">Lot No</th> 
														<th width="80">Color Name</th>
														<th width="80">Color Range</th>
														<th width="90">Batch No</th>
														<th width="40">Extn. No</th>
														<th width="70">Fabric Wgt.</th>
														<th width="70">Trims Wgt.</th>
														<th width="70">Batch Wgt.</th>
														<th width="70">M/C Capacity</th>
														<th width="70">Loading %</th>
														<th width="75">Load Date & Time</th>
														<th width="75">UnLoad Date Time</th>
														<th width="60">Time Used</th>
														<th width="60">BTB/LTB</th>
														<th width="100">Dyeing Fab. Type</th>
														<th width="100">Result</th>
														<th width="80">Dyeing Process</th>
														<th width="80"><p>Dyeing Re Process Name</p></th>

														<th width="60">Hour Load Meter</th>
														<th width="60">Hour unLoad Meter</th>
														<th width="60">Total Time</th>

														<th width="60">Water Loading Flow</th> 
														<th width="60">Water UnLoading Flow</th>
														<th width="70">Water Cons.</th>

														<th width="">Remark</th>
													</tr>
												</thead>
											</table>
											<div style=" max-height:350px; width:2860px; overflow-y:scroll;;" id="scroll_body">
												<table class="rpt_table" id="table_body" width="2840" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
													<tbody>
														<? 
														$total_water_cons_load=0;$total_water_cons_unload=$grand_sub_tot_machine_capacity=0;$grand_total_production_qnty=$grand_subcon_total_batch_qty=0;
														$batch_chk_arr=array(); $group_by_arr=array(); $tot_trims_qnty=0; $trims_check_array=array();
														foreach($subbatch_data_arr as $batch_id=>$batch_data_arr)
														{
															$b=1;
															foreach ($batch_data_arr as $prod_id => $row) 
															{

																$batch_td_span = $sub_batch_row_span_arr[$batch_id];
																if ($i%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";

																if($group_by!=0)
																{
																	if($group_by==1)
																	{
																		$group_value=$row['floor_id'];
																		$group_name="Floor";
																		$group_dtls_value=$floor_arr[$row['floor_id']];
																	}
																	else if($group_by==2)
																	{
																		$group_value=$row['shift_name'];
																		$group_name="Shift";
																		$group_dtls_value=$shift_name[$row['shift_name']];
																	}
																	else if($group_by==3)
																	{
																		$group_value=$row['machine_id'];
																		$group_name="machine";
																		$group_dtls_value=$machine_arr[$row['machine_id']];
																	}
																	if (!in_array($group_value,$group_by_arr) )
																	{
																		if($k>1)
																		{ 	
																			?>
																			<tr class="tbl_bottom">
																				<td width="30">&nbsp;</td>
																				<td width="80">&nbsp;</td>

																				<? if($group_by==3 || $group_by==0){ ?>
																					<td width="80">&nbsp;</td> 
																				<? } ?>
																				<? if($group_by==2 || $group_by==0){ ?>
																					<td width="80">&nbsp;</td> 
																				<? } if($group_by==1 || $group_by==0){ ?>  
																					<td width="80">&nbsp;</td>
																				<? } ?>
																				<td width="100" align="center"><p></p></td>
																				<td width="100" align="center"><p></p></td>
																				<td width="100" align="center"><p></p></td>
																				<td width="80" align="center"><p></p></td>
																				<td width="90"></td>
																				<td width="70" align="center"></td>
																				<td width="100"></td>
																				<td width="70"><p></p></td>
																				<td width="50"><p></p></td>
																				<td width="80"><p></p></td>
																				<td width="80"><p></p></td>
																				<td width="90"><p></p></td>
																				<td width="40"><p></p></td>
																				<td align="right" width="70"><? echo  number_format($sub_production_qnty,2);?></td>
																				<td align="right" width="70"></td>
																				<td align="right" width="70"><? echo number_format($batch_qnty_tot,2);?></td>
																				<td width="70"><p></p></td>
																				<td width="70"><p></p></td>
																				<td width="75"><p></p></td>
																				<td width="75"><p></p></td>
																				<td align="center" width="60"> </td>
																				<td align="center" width="60"></td>
																				<td align="center" width="100"></td>
																				<td align="center" width="100"></td>
																				<td align="center" width="80"></td>
																				<td align="center" width="80"> </td>
																				<td width="60" align="right"><p></p></td>
																				<td width="60" align="right"><p></p></td>
																				<td width="60" align="right" ><p></p></td>
																				<td width="60" align="right"><p></p></td>
																				<td width="60" align="right"><p></p></td>
																				<td align="right" width="70" ></td>
																				<td align="center"></td>
																			</tr>                                
																			<?
																			$batch_qnty_tot=0; $trims_btq=0;$sub_production_qnty=0;
																		}
																		?>
																		<tr bgcolor="#EFEFEF">
																			<td colspan="37" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
																		</tr>
																		<?
																		$group_by_arr[]=$group_value;            
																		$k++;
																	}					
																}

																
																$batch_weight=$row['batch_weight'];
																$water_cons_unload=$row['water_flow_meter'];
																$water_cons_load=$sub_water_flow_arr[$batch_id];
																$load_hour_meter=$sub_load_hour_meter_arr[$batch_id];
																$water_cons_diff=($water_cons_unload-$water_cons_load)/$batch_weight*1000;

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
																?>
																<tr bgcolor="<? echo $bgcolor_dyeing; ?>"  id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor_dyeing; ?>')">
																	<td width="30"><? echo $i; ?></td>
																	<td width="80"><p><? echo change_date_format($date_type_cond); $unload_date=$row['process_end_date']; ?></p></td>
																	<? if($group_by==3 || $group_by==0){ ?>

																		<td align="center" width="80"><p><? echo $machine_arr[$row['machine_id']]; ?></p></td>
																	<? } if($group_by==2 || $group_by==0){ ?>
																		<td width="80"><p><? echo $floor_arr[$row['floor_id']]; ?></p></td>
																	<? } if($group_by==1 || $group_by==0){ ?>
																		<td width="80" align="center"><p><? echo $shift_name[$row['shift_name']]; ?></p></td>
																	<? } ?>

																	<td width="100" align="center">
																		<p>
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
																		</p>
																	</td>
																	<td width="100" align="center"><p><? echo $row['style_ref_no']; ?></p></td>
																	<td width="100" align="center"><p><? echo $row['season']; ?></p></td>
																	<td width="80" align="center"><p><? echo $row['job_no_prefix_num']; ?></p></td>
																	<td width="90"><p><? echo $row['booking_no']; ?></p></td>
																	<td width="70" align="center"><p><? echo $booking_type_arr[$row['booking_no']]; ?></p></td>
																	<td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $row['item_description']; ?></div></td>
																	<td width="70"><p><? echo $fabric_typee[$row['width_dia_type']]; ?></p></td>

																	<td width="50" title="<? echo implode(",",array_filter(array_unique($row["barcode_no"])));?>">
																		<p>
																			<? $bar_lot_arr= array();$yarnlots="";
																			$bar_lot_arr =array_filter(array_unique($row["barcode_no"]));
																			if(count($bar_lot_arr)>0){
																				foreach ($bar_lot_arr as $bcode) 
																				{
																					$yarnlots .= chop($yarn_lot_arr[$bcode],",").",";
																				}
																				echo implode(",",array_filter(array_unique(explode(",", $yarnlots))));  
																			}
																			?>
																		</p>
																	</td>

																	<td width="80"><p><? echo $color_library[$row['color_id']]; ?></p></td>
																	<td width="80"><p><? echo $color_range[$row['color_range_id']]; ?></p></td>
																	<? if($b==1){
																	$batch_qnty=$sub_batch_qnty_arr[$batch_id]+$row["total_trims_weight"];
																	 ?>
																		<td width="90" rowspan="<? echo $batch_td_span?>"><p><? echo $row['batch_no']; ?></p></td>
																		<td width="40" rowspan="<? echo $batch_td_span?>"><p><? echo $row['extention_no']; ?></p></td>
																		<? } ?>
																		<td align="right" width="70"><? echo number_format($row['production_qnty'],2); $grand_total_production_qnty += $row['production_qnty']; $sub_production_qnty += $row['production_qnty'];?></td>

																		<? if($b==1){?>
																			<td align="right" width="70" rowspan="<? echo $batch_td_span?>"><? echo number_format($row["total_trims_weight"],2);  ?></td>
																			<td align="right" width="70" rowspan="<? echo $batch_td_span?>"><? echo number_format($batch_qnty,2);  ?></td>
																			<?
																			$batch_qnty_tot+=$batch_qnty;
																			$grand_subcon_total_batch_qty+=$batch_qnty;
																			$trims_btq+=$row["total_trims_weight"];
																			$grand_tot_trims_qnty+=$row["total_trims_weight"];
																			$grand_sub_tot_machine_capacity+=$machine_capacity_arr[$row['machine_id']];

																			?>
																			<td align="right" width="70" rowspan="<? echo $batch_td_span?>"><? echo number_format($machine_capacity_arr[$row['machine_id']],2);  ?>
																			</td>
																			<td align="right" width="70" title="Batch Weight/Machine Capacity*100" rowspan="<? echo $batch_td_span?>"><? echo number_format(($batch_qnty/$machine_capacity_arr[$row['machine_id']]*100),2);  ?>
																			</td>
																			<td width="75" rowspan="<? echo $batch_td_span?>" title="<? echo $sub_load_date[$batch_id];?>"><p><? $load_t=$sub_load_hr[$batch_id].':'.$sub_load_min[$batch_id]; 
																			echo  ($sub_load_date[$batch_id] == '0000-00-00' || $sub_load_date[$batch_id] == '' ? '' : change_date_format($sub_load_date[$batch_id])).' <br> '.$load_t;

																			?></p></td>
																			<td width="75" rowspan="<? echo $batch_td_span?>"><p><? $hr=strtotime($unload_date,$load_t); $min=($row['end_minutes'])-($sub_load_min[$batch_id]);
																			echo  ($row['process_end_date'] == '0000-00-00' || $row['process_end_date'] == '' ? '' : change_date_format($row['process_end_date'])).'<br>'.$unload_time=$row['end_hours'].':'.$row['end_minutes']; ?></p>
																		</td>
																		<td align="center" width="60" rowspan="<? echo $batch_td_span?>">
																			<?     
																			$new_date_time_unload=($unload_date.' '.$unload_time.':'.'00');
																			$new_date_time_load=($sub_load_date[$batch_id].' '.$load_t.':'.'00');
																			$total_time=datediff(n,$new_date_time_load,$new_date_time_unload);
																			echo floor($total_time/60).":".$total_time%60;
																			?>
																		</td>
																		<td align="center" width="60" rowspan="<? echo $batch_td_span?>"><? echo $ltb_btb[$row["ltb_btb_id"]];?></td>

																		<td align="center" width="100" rowspan="<? echo $batch_td_span?>"><p><? 
																		echo $fabric_type_for_dyeing[$row['fabric_type']];?></p> </td>
																		<td align="center" width="100" rowspan="<? echo $batch_td_span?>"><p><? echo $dyeing_result[$row['result']]; ?></p> </td>
																		<td align="center" width="80" rowspan="<? echo $batch_td_span?>"><? echo $conversion_cost_head_array[$row["process_id"]]?></td>
																		<td align="center" width="80" rowspan="<? echo $batch_td_span?>" title="<? echo $row["add_tp_strip"];?>">
																			<? 
																			if($row["process_id"]==148 && $row["add_tp_strip"] ==1)
																			{
																				echo "Re-Wash";
																			}
																			else{
																				echo $dyeing_re_process[$row["add_tp_strip"]];
																			} 
																			?>
																		</td>
																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo number_format($load_hour_meter,2);  ?></p></td>
																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo number_format($row['hour_unload_meter'],2);  ?></p></td>
																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo number_format($row['hour_unload_meter']-$load_hour_meter,2);  ?></p></td>

																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo $water_cons_load;  ?></p></td>
																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo $water_cons_unload;  ?></p></td>
																		<td align="right" width="70" rowspan="<? echo $batch_td_span?>" title="<? echo "Water Cons Unload-Water Cons Load*1000/Batch Qnty"?>" ><? echo number_format((($water_cons_unload-$water_cons_load)*1000)/$row['batch_qnty'],2);  ?></td>

																		<td align="center" rowspan="<? echo $batch_td_span?>"><p><? echo $row['remarks']; ?></p> </td>
																		<?}?>
																	</tr>
																	<? 
																	$i++;$b++;


																	$total_water_cons_load+=$water_cons_load;
																	$total_water_cons_unload+=$water_cons_unload;


																}
															}
															if($group_by!=0)
															{
																?>
																<tr class="tbl_bottom">
																	<td width="30">&nbsp;</td>
																	<td width="80">&nbsp;</td>

																	<? if($group_by==3 || $group_by==0){ ?>
																		<td width="80">&nbsp;</td> 
																	<? } ?>
																	<? if($group_by==2 || $group_by==0){ ?>
																		<td width="80">&nbsp;</td> 
																	<? } if($group_by==1 || $group_by==0){ ?>  
																		<td width="80">&nbsp;</td>
																	<? } ?>
																	<td width="100" align="center"><p></p></td>
																	<td width="100" align="center"><p></p></td>
																	<td width="100" align="center"><p></p></td>
																	<td width="80" align="center"><p></p></td>
																	<td width="90"></td>
																	<td width="70" align="center"></td>
																	<td width="100"></td>
																	<td width="70"><p></p></td>
																	<td width="50"><p></p></td>
																	<td width="80"><p></p></td>
																	<td width="80"><p></p></td>
																	<td width="90"><p></p></td>
																	<td width="40"><p></p></td>
																	<td align="right" width="70"><? echo  number_format($sub_production_qnty,2);?></td>
																	<td align="right" width="70"></td>
																	<td align="right" width="70"><? echo number_format($batch_qnty_tot,2);?></td>
																	<td width="70"><p></p></td>
																	<td width="70"><p></p></td>
																	<td width="75"><p></p></td>
																	<td width="75"><p></p></td>
																	<td align="center" width="60"> </td>
																	<td align="center" width="60"></td>
																	<td align="center" width="100"></td>
																	<td align="center" width="100"></td>
																	<td align="center" width="80"></td>
																	<td align="center" width="80"> </td>
																	<td width="60" align="right"><p></p></td>
																	<td width="60" align="right"><p></p></td>
																	<td width="60" align="right" ><p></p></td>
																	<td width="60" align="right"><p></p></td>
																	<td width="60" align="right"><p></p></td>
																	<td align="right" width="70" ></td>
																	<td align="center"></td>
																</tr> 
																<? 
															}

															?>            
														</tbody>
														<tfoot>
															<tr class="tbl_bottom">
																<td width="30">&nbsp;</td>
																<td width="80">&nbsp;</td>

																<? if($group_by==3 || $group_by==0){ ?>
																	<td width="80">&nbsp;</td> 
																<? } ?>
																<? if($group_by==2 || $group_by==0){ ?>
																	<td width="80">&nbsp;</td> 
																<? } if($group_by==1 || $group_by==0){ ?>  
																	<td width="80">&nbsp;</td>
																<? } ?>
																<td width="100" align="center"><p></p></td>
																<td width="100" align="center"><p></p></td>
																<td width="100" align="center"><p></p></td>
																<td width="80" align="center"><p></p></td>
																<td width="90"></td>
																<td width="70" align="center"></td>
																<td width="100"></td>
																<td width="70"><p></p></td>
																<td width="50"><p></p></td>
																<td width="80"><p></p></td>
																<td width="80"><p></p></td>
																<td width="90"><p></p></td>
																<td width="40"><p></p></td>
																<td align="right" width="70"><? echo number_format($grand_total_production_qnty,2);?></td>
																<td align="right" width="70"><? echo number_format($grand_tot_trims_qnty,2);?></td>
																<td align="right" width="70"><? echo number_format($grand_subcon_total_batch_qty,2);?></td>
																<td align="right" width="70"><? echo number_format($grand_sub_tot_machine_capacity,2);?></td>
																
																<td width="70"><p></p></td>
																<td width="75"><p></p></td>
																<td width="75"><p></p></td>
																<td align="center" width="60"> </td>
																<td align="center" width="60"></td>
																<td align="center" width="100"></td>
																<td align="center" width="100"></td>
																<td align="center" width="80"></td>
																<td align="center" width="80"> </td>
																<td width="60" align="right"><p></p></td>
																<td width="60" align="right"><p></p></td>
																<td width="60" align="right" ><p></p></td>
																<td width="60" align="right"><p></p></td>
																<td width="60" align="right"><p></p></td>
																<td align="right" width="70" ></td>
																<td align="center"></td>
															</tr> 
														</tfoot>
													</table>
												</div>
											</div>
											<br/>
											<? 
										}
									}
									?>
								</fieldset>
							</div>
							<?
						} 
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
	//$filename=$user_id."_".$name.".xls";
						echo "$total_data****$filename";

						exit();
						exit();
					}

if($action=="generate_report2_old") //Show 2
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if($db_type==0) $year_field_by="and YEAR(a.insert_date)"; 
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	if($db_type==0) $field_concat="concat(machine_no,'-',brand) as machine_name"; 
	else if($db_type==2) $field_concat="machine_no || '-' || brand as machine_name";
	$company = str_replace("'","",$cbo_company_name);
	$working_company = str_replace("'","",$cbo_working_company_name);
	$buyer = str_replace("'","",$cbo_buyer_name);
	$cbo_prod_type = str_replace("'","",$cbo_prod_type);
	$cbo_party_id = str_replace("'","",$cbo_party_id);

	$date_search_type = str_replace("'","",$search_type);
	$job_number = str_replace("'","",$job_number);
	$job_number_id = str_replace("'","",$job_number_show);
	$batch_no = str_replace("'","",$batch_number_show);

	$machine=str_replace("'","",$txt_machine_id);
	$floor_name=str_replace("'","",$cbo_floor_id);

	$batch_number_hidden = str_replace("'","",$batch_number);
	$ext_num = str_replace("'","",$txt_ext_no);
	$hidden_ext = str_replace("'","",$hidden_ext_no);

	$fso_no = trim(str_replace("'","",$fso_no));
	$hidden_fso_no = str_replace("'","",$hidden_fso_no);
	$hidden_booking_no = str_replace("'","",$hidden_booking_no);
	$booking_no = str_replace("'","",$booking_no);

	$cbo_type = str_replace("'","",$cbo_type);
	$cbo_result_name = str_replace("'","",$cbo_result_name);
	$year = str_replace("'","",$cbo_year);
	$shift = str_replace("'","",$cbo_shift_name);

	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	if ($buyer==0) $sub_buyer_cond=""; else $sub_buyer_cond="and d.party_id='".$buyer."' ";
	if ($cbo_prod_type>0 && $cbo_party_id>0) $cbo_prod_type_cond="and f.service_company in($cbo_party_id) "; else $cbo_prod_type_cond="";
	if ($cbo_prod_type>0) $cbo_prod_source_cond="and f.service_source in($cbo_prod_type) "; else $cbo_prod_source_cond="";

	if ($cbo_result_name==0) $result_name_cond=""; else $result_name_cond="  and f.result='".$cbo_result_name."' ";
	if ($shift==0) $shift_name_cond=""; else $shift_name_cond="  and f.shift_name='".$shift."' ";
	if ($machine=="") $machine_cond=""; else $machine_cond =" and f.machine_id in ( $machine ) ";
	if ($floor_name==0 || $floor_name=='')
	{
		$floor_id_cond="";$floor_id_cond2="";
	}
	 else {
		 $floor_id_cond=" and f.floor_id=$floor_name";
		  $floor_id_cond2=" and floor_id=$floor_name";
	 }
	
	$hidden_booking_cond="";
	if($booking_no!="")
	{
		$hidden_booking_cond="and a.booking_no like '%$booking_no%' ";
	}
	//echo $hidden_booking_cond.'='.$booking_no;

	if ($buyer==0) $buyerdata=""; else $buyerdata=" and ((d.buyer_id = $buyer and d.within_group =2 ) or (d.po_buyer = $buyer and d.within_group =1))";


	if ($batch_no=="") $batch_num=""; else $batch_num="  and a.batch_no='".trim($batch_no)."' ";

	if ($batch_no=="") $unload_batch_cond=""; else $unload_batch_cond="  and batch_no='".trim($batch_no)."' ";
	if ($batch_no=="") $unload_batch_cond2=""; else $unload_batch_cond2="  and f.batch_no='".trim($batch_no)."' ";
	//if ($batch_no=="") $$sub_job_cond=""; else $$sub_job_cond="  and f.batch_no='".trim($batch_no)."' ";
	if ($working_company==0) $workingCompany_name_cond2=""; else $workingCompany_name_cond2="  and a.working_company_id='".$working_company."' ";
	if ($company==0) $companyCond=""; else $companyCond="  and a.company_id=$company";
	
	if ($working_company==0) $subcon_working_company_cond=""; else $subcon_working_company_cond="  and a.company_id='".$working_company."' ";
	if ($company==0) $subcon_companyCond=""; else $subcon_companyCond="  and a.company_id=$company"; 

	if(trim($ext_no)!="") $ext_no_search="%".trim($ext_no)."%"; else $ext_no_search="%%";

	$fsodata=($hidden_fso_no)? " and d.id in (".$hidden_fso_no .")" : '';
	if($hidden_fso_no=="")
	{
		$fsodata =($fso_no)? " and d.job_no like '%".str_pad($fso_no,5,'0',STR_PAD_LEFT)."%'" : ''; 
		if($year!=0)
		{
			$fsodata.=($year)? " and d.job_no like '%-".substr( $year, -2)."-%'" : ''; 
		}    
	}

	if($job_number!=""){
		$po_job_cond = " d.po_job_no ";
		$job_ref_arr = explode(",", $job_number);
		foreach ($job_ref_arr as $val) 
		{
			$po_job_cond .=" like '%".$val."%' or ";
		}
		$po_job_cond = " and ".chop($po_job_cond," or");
		
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
		{
			//if ($txt_order!='') $suborder_no="and c.order_no='$txt_order'"; else $suborder_no="";
			if ($job_number_id!='') $sub_job_cond="  and d.subcon_job like '%".$job_number_id."%' "; else $sub_job_cond="";
		}
	
	}

	$yarncount_arr = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');


	if ($ext_num=="") $ext_no=""; else $ext_no="  and a.extention_no=$ext_num ";
	if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
	if($txt_date_from && $txt_date_to)
	{
		if($db_type==0)
		{
			$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');

			$start_dd= strtotime(change_date_format($date_from,"yyyy-mm-dd","-"));
			$end_dd= strtotime(change_date_format($date_from,"yyyy-mm-dd","-"));
			$start_d=date("Y-m-d",$start_dd);
			$end_d=date("Y-m-d",$end_dd);
			$date_from2 = strtotime($start_d);
			$date_to2 = strtotime($end_d);
			$date_start = date("Y-m-d",strtotime("-5 day", $date_from2));
			$date_end = date("Y-m-d",strtotime("+5 day", $date_to2));
			$date_start= change_date_format($date_start,"yyyy-mm-dd","-",1);
			$date_end= change_date_format($date_end,"yyyy-mm-dd","-",1);

			$second_month_ldate=date("Y-m-t",strtotime($date_to));
			$dateFrom= explode("-",$date_from);
			$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
			$prod_last_day=change_date_format($second_month_ldate,'yyyy-mm-dd');
			$prod_date_upto=" and  f.process_end_date BETWEEN '$fromdate' AND '$prod_last_day' ";
			if($date_search_type==1)
			{
				$dates_com=" and  f.process_end_date BETWEEN '$date_from' AND '$date_to' ";
				$dates_com2=" and  f.process_end_date BETWEEN '$date_start' AND '$date_end' ";
			}
			else
			{
				$dates_com=" and f.insert_date between '".$date_from."' and '".$date_to." 23:59:59' ";
				$dates_com2=" and f.insert_date between '".$date_start."' and '".$date_end." 23:59:59' ";
			}
		}
		elseif($db_type==2)
		{
			$date_from=change_date_format($txt_date_from,'','',1);
			$date_to=change_date_format($txt_date_to,'','',1);

			$start_dd= strtotime(change_date_format($date_from,"dd-mm-yyyy","-"));
			$end_dd= strtotime(change_date_format($date_from,"dd-mm-yyyy","-"));
			$start_d=date("Y-m-d",$start_dd);
			$end_d=date("Y-m-d",$end_dd);
			$date_from2 = strtotime($start_d);
			$date_to2 = strtotime($end_d);
			$date_start = date("Y-m-d",strtotime("-5 day", $date_from2));
			$date_end = date("Y-m-d",strtotime("+5 day", $date_to2));
			$date_start= change_date_format($date_start,"dd-mm-yyyy","-",1);
			$date_end= change_date_format($date_end,"dd-mm-yyyy","-",1);

			$dateFrom= explode("-",$date_from);
			$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
			$second_month_ldate=date("Y-M-t",strtotime($date_to));
			$prod_last_day=change_date_format($second_month_ldate,'','',1);
			$prod_date_upto="and  f.process_end_date BETWEEN '$fromdate' AND '$prod_last_day'";
			if($date_search_type==1)
			{
				$dates_com="and  f.process_end_date BETWEEN '$date_from' AND '$date_to'";
				$dates_com2="and  f.process_end_date BETWEEN '$date_start' AND '$date_end'";
			}
			else
			{
				$dates_com=" and f.insert_date between '".$date_from."' and '".$date_to." 11:59:59 PM' ";
				$dates_com2=" and f.insert_date between '".$date_start."' and '".$date_end." 11:59:59 PM' ";
			}
		}
	}

	$group_by=str_replace("'",'',$cbo_group_by);
	if($group_by==1)
	{
		$order_by="order by f.floor_id";
		$order_by2="order by floor_id";
	}
	else if($group_by==2)
	{
		$order_by="order by f.shift_name";
		$order_b2y="order by shift_name";
	}
	else if($group_by==3)
	{
		$order_by="order by f.machine_id";
		$order_by2="order by machine_id";
	}
	else
	{
		$order_by="order by f.process_end_date,f.machine_id";
		$order_by2="order by process_end_date,machine_id";
	}

		$machine_result=sql_select("select id,prod_capacity,$field_concat from  lib_machine_name where status_active=1 and  category_id = 2 $floor_id_cond2 order by seq_no ");
		//echo "select id,prod_capacity,$field_concat from  lib_machine_name where status_active=1 and  category_id = 2 $floor_id_cond2 order by seq_no ";
		foreach($machine_result as $row)
		{
			$machine_arr[$row[csf('id')]]=$row[csf('machine_name')];
			$machine_capacity_arr[$row[csf('id')]]=$row[csf('prod_capacity')];
			$total_machine_arr[$row[csf('id')]]=$row[csf('id')];
		}
		
	if($cbo_type==1)
	{
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
		{
			 $sql="select a.id,a.sales_order_id,a.company_id,a.batch_no,a.batch_date,a.batch_weight,a.color_id,null as color_range_id, a.booking_no_id, a.extention_no,a.batch_against,a.total_trims_weight, SUM(b.batch_qnty) AS batch_qnty, b.item_description, b.po_id,b.prod_id,b.body_part_id,b.width_dia_type, d.job_no_prefix_num as fso_no, d.job_no  as sales_no, d.po_job_no,d.sales_booking_no, d.style_ref_no, d.within_group, d.buyer_id,d.po_buyer, f.process_end_date, f.process_end_date as production_date, f.end_hours,f.end_minutes,f.machine_id,f.shift_name, f.floor_id,f.multi_batch_load_id,f.system_no, f.remarks,f.process_id from pro_batch_create_mst a,pro_batch_create_dtls b,fabric_sales_order_mst d, pro_fab_subprocess f where  a.id=b.mst_id and f.batch_id=a.id and f.batch_id=b.mst_id and a.is_sales=1 and b.is_sales=1 and b.po_id=d.id $companyCond $workingCompany_name_cond2 $dates_com $po_job_cond $fsodata $batch_num $buyerdata $year_cond  $machine_cond $floor_id_cond $cbo_prod_type_cond and a.entry_form=0  and f.entry_form=35 and f.load_unload_id=1 and a.batch_against in(1,2,3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $batchIds_cond $cbo_prod_type_cond $cbo_prod_source_cond $hidden_booking_cond GROUP BY  a.id,f.shift_name,f.floor_id,f.multi_batch_load_id,a.sales_order_id,a.company_id,a.batch_no,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id, a.booking_no_id, a.extention_no, a.batch_against,b.item_description, b.po_id,b.body_part_id,b.prod_id, b.width_dia_type ,d.po_job_no,d.sales_booking_no, d.job_no, d.style_ref_no, d.job_no_prefix_num, d.within_group, d.buyer_id, d.po_buyer, f.process_end_date,f.system_no, f.end_hours, f.end_minutes, f.machine_id, f.remarks,f.process_id $order_by";
		}
	}
	else if($cbo_type==2)
	{
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
		{
		 $sql = "select f.insert_date, a.batch_no, a.batch_weight, a.id as batch_id,a.sales_order_id, a.color_id, a.color_range_id, a.extention_no, a.total_trims_weight, (b.batch_qnty) as batch_qnty, b.item_description, b.po_id, b.prod_id,b.body_part_id, b.width_dia_type, d.job_no_prefix_num, d.buyer_id, d.po_buyer, f.remarks, f.shift_name, f.production_date as process_end_date, f.process_end_date as production_date, f.end_hours,f.system_no, f.floor_id,f.multi_batch_load_id, f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id, f.fabric_type, f.result, a.booking_no, d.booking_without_order, d.season, d.job_no  as sales_no,d.style_ref_no , b.barcode_no, f.process_id, f.ltb_btb_id, d.within_group, d.booking_id 
			from pro_batch_create_dtls b, fabric_sales_order_mst d, pro_fab_subprocess f, pro_batch_create_mst a 
			where f.batch_id=a.id 
			$companyCond $workingCompany_name_cond2  $dates_com $po_job_cond $fsodata $buyerdata $batch_num $year_cond $result_name_cond $shift_name_cond $machine_cond $floor_id_cond $cbo_prod_type_cond $cbo_prod_source_cond $hidden_booking_cond

			and a.entry_form=0  and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,11,2,3) and b.po_id=d.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.is_sales=1 and b.is_sales=1
			$order_by";
		}
		
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
		{
			 $sql_subcon="select f.insert_date, a.batch_no, a.batch_weight, a.id as batch_id, a.color_id, a.color_range_id, a.extention_no, a.total_trims_weight, b.batch_qnty AS sub_batch_qnty, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.subcon_job as job_no_prefix_num, d.party_id as buyer_id, d.party_id as po_buyer, 
			 f.remarks, f.shift_name, f.production_date as process_end_date,f.system_no, f.process_end_date as production_date, f.end_hours, f.floor_id,f.multi_batch_load_id, f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id, f.fabric_type, f.result, 0 as season, c.cust_style_ref as style_ref_no, 0 as barcode_no, f.process_id, f.ltb_btb_id, 0 as within_group, 0 as booking_id 
			
			from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a, pro_fab_subprocess f
			
			where f.batch_id=a.id and f.batch_id=b.mst_id $subcon_working_company_cond $subcon_companyCond and a.entry_form=36 and a.id=b.mst_id and f.entry_form=38 and f.load_unload_id=2 and a.batch_against in(1,11,2,3) and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $dates_com $sub_job_cond $batch_num $fsodata $sub_buyer_cond $suborder_no $result_name_cond $year_cond $shift_name_cond $machine_cond $floor_id_cond $cbo_prod_type_cond $cbo_prod_source_cond $hidden_booking_cond $order_by";
		}
	}	

	//echo $sql; 

	if($cbo_type==2) 
	{
		/*$sql_booking_type="SELECT booking_no, booking_type, is_short,1 as with_order_status from wo_booking_mst where status_active=1 and is_deleted=0
		union all select booking_no, booking_type, is_short, 2 as with_order_status from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0";
		$sql_booking_type_data=sql_select($sql_booking_type);
		foreach ($sql_booking_type_data as $value) 
		{
			if($value[csf("booking_type")]==1 && $value[csf("is_short")]==2) $booking_type_arr[$value[csf("booking_no")]]="Main";
			else if($value[csf("booking_type")]==1 && $value[csf("is_short")]==1) $booking_type_arr[$value[csf("booking_no")]]="Short";
			else if($value[csf("booking_type")]==4) $booking_type_arr[$value[csf("booking_no")]]="Sample";
		}*/

		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
		{
			//echo $sql;
			$batchdata=sql_select($sql);
			foreach($batchdata as $row)
			{
				$all_batch[$row[csf("batch_id")]]=$row[csf("batch_id")];
				$all_barcode[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
				$sales_order_idArr[$row[csf("sales_order_id")]]=$row[csf("sales_order_id")];
				if($row[csf("booking_id")]!="") $all_booking_id[$row[csf("booking_id")]]=$row[csf("booking_id")];
			}
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

			$batchCond = $dyeing_batch_id_cond = "";  $batchCond2 = $all_batch_no_cond2 = ""; 
			$all_batch_arr=explode(",",$all_batch_ids);
			if($db_type==2 && count($all_batch_arr)>999)
			{
				$all_batch_chunk=array_chunk($all_batch_arr,999) ;
				foreach($all_batch_chunk as $chunk_arr)
				{
					$batchCond.=" f.batch_id in(".implode(",",$chunk_arr).") or ";	
					$batchCond2.=" a.batch_id in(".implode(",",$chunk_arr).") or ";	
				}
				$dyeing_batch_id_cond.=" and (".chop($batchCond,'or ').")";			
				$all_batch_no_cond2.=" and (".chop($batchCond2,'or ').")";			
			}
			else
			{ 	
				$dyeing_batch_id_cond=" and f.batch_id in($all_batch_ids)";
				$all_batch_no_cond2=" and a.batch_id in($all_batch_ids)";
			}
			//echo $dyeing_batch_id_cond.'DD';
			//=======================
			//if($all_batch_ids!="") $dyeing_batch_id_cond= "and f.batch_id in($all_batch_ids)"; else $dyeing_batch_id_cond="";
			
			$sales_cond_for_in=where_con_using_array($sales_order_idArr,0,"a.id");
			$req_batch_cond_for_in=where_con_using_array($all_batch_arr,0,"a.batch_id");
			$sql_sales= sql_select("select a.id as sales_id, c.id as batch_id, b.body_part_id,b.color_type_id, c.sales_order_no,c.sales_order_id
				from  fabric_sales_order_mst a, fabric_sales_order_dtls b,pro_batch_create_mst c
				where a.id = b.mst_id and c.sales_order_id = a.id  and a.status_active = 1 and b.status_active=1 $sales_cond_for_in"); // and a.batch_id in ($all_batch_ids)
			foreach ($sql_sales as $row) 
			{
				$sales_batch_arr[$row[csf("batch_id")]][$row[csf("sales_order_no")]][$row[csf("body_part_id")]]= $row[csf("color_type_id")];
				$sales_batch_arr2[$row[csf("batch_id")]][$row[csf("sales_id")]][$row[csf("body_part_id")]]= $row[csf("color_type_id")];
				//$batch_product_arr2[$val[csf("batch_id")]]["batch_prod_tot"] += $val[csf("production_qty")];
			//	$batch_product_Addingarr[$val[csf("batch_id")]][$val[csf("prod_id")]]["batch_prod_tot"] += $val[csf("production_qty")];
			}
			$sql_prod_sales= sql_select("select b.id as dtls_id,a.id,a.batch_id,a.multi_batch_load_id,a.load_unload_id, b.prod_id, b.const_composition, (b.batch_qty) as batch_qty, (b.production_qty) as production_qty,c.body_part_id,c.po_id as sales_id
				from  pro_fab_subprocess a, pro_fab_subprocess_dtls b,pro_batch_create_dtls c
				where a.id = b.mst_id and c.mst_id=a.batch_id and b.prod_id=c.prod_id and a.load_unload_id in(2) and b.load_unload_id in(2) and b.entry_page=35 and a.status_active = 1 and b.status_active=1 $all_batch_no_cond2");
				 
				
			foreach ($sql_prod_sales as $row) 
			{
				 
			if($dtlsChkArr[$row[csf("dtls_id")]]=='')
			{
			$color_typeId=$sales_batch_arr2[$row[csf("batch_id")]][$row[csf("sales_id")]][$row[csf("body_part_id")]];
			$sales_batch_colortype_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]= $color_typeId;
			$dtlsChkArr[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
			}
			//$sales_batch_product_arr[$row[csf("batch_id")]][$color_typeId] += $row[csf("production_qty")];
			}
			//print_r($sales_batch_colortype_arr);
			unset($sql_prod_sales);
			

			$sql_prod_ref= sql_select("select a.id,a.batch_id,a.multi_batch_load_id,a.load_unload_id, b.prod_id, b.const_composition, (b.batch_qty) as batch_qty, (b.production_qty) as production_qty
				from  pro_fab_subprocess a, pro_fab_subprocess_dtls b
				where a.id = b.mst_id and a.load_unload_id in(1,2) and b.load_unload_id in(1,2) and b.entry_page=35 and a.status_active = 1 and b.status_active=1 $all_batch_no_cond2"); // and a.batch_id in ($all_batch_ids)
				//echo "select a.id,a.batch_id, b.prod_id, b.const_composition, (b.batch_qty) as batch_qty, (b.production_qty) as production_qty
			//	from  pro_fab_subprocess a, pro_fab_subprocess_dtls b
				//where a.id = b.mst_id and a.load_unload_id = 2 and b.load_unload_id=2 and b.entry_page=35 and a.status_active = 1 and b.status_active=1 $all_batch_no_cond2";

			foreach ($sql_prod_ref as $val) 
			{
				if($val[csf("load_unload_id")]==2)
				{
					$colortype=$sales_batch_colortype_arr[$val[csf("batch_id")]][$val[csf("prod_id")]];
					//echo $colortype.'X,';
					$batch_product_arr[$val[csf("batch_id")]][$val[csf("prod_id")]] += $val[csf("production_qty")];
					$batch_product_arr2[$val[csf("batch_id")]]["batch_prod_tot"] += $val[csf("production_qty")];
					$batch_product_arr3[$val[csf("batch_id")]][$val[csf("prod_id")]][$colortype] += $val[csf("production_qty")];
				}
				else
				{
					$multi_batch_arr[$val[csf("batch_id")]]= $val[csf("multi_batch_load_id")];
				}
			//	$batch_product_Addingarr[$val[csf("batch_id")]][$val[csf("prod_id")]]["batch_prod_tot"] += $val[csf("production_qty")];
			}
		//print_r($batch_product_arr3);
			//sales_order_idArr
				
			
				
			
			//print_r($sales_batch_product_arr);
			//print_r($sales_batch_arr);
		  $sql_dyes = "SELECT a.id as recipe_id,a.batch_id,d.item_category_id,c.ratio from pro_recipe_entry_mst a, dyes_chem_issue_requ_dtls c,dyes_chem_requ_recipe_att b,product_details_master d where b.recipe_id=a.id and c.mst_id=b.mst_id and d.id=c.product_id and d.item_category_id in(6)  and a.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.status_active=1 $req_batch_cond_for_in";
			// echo $sql;die();
			$dyes_res = sql_select($sql_dyes);
			 
			foreach ($dyes_res as $row) 
			{
				////if($req_batch_ckhArr[$row[csf('batch_id')]][$row[csf('recipe_id')]]=='')
				//{
				$batch_item_ratio_dyes_arr[$row[csf('batch_id')]]['ratio_total']+= $row[csf('ratio')];
				$req_batch_ckhArr[$row[csf('batch_id')]][$row[csf('recipe_id')]]=$row[csf('batch_id')];
				//}
			}
			unset($dyes_res);
		//	print_r($batch_item_ratio_dyes_arr);
		
			if(count($req_id_array))
			{
				//$requisition_id_cond=where_con_using_array($req_id_array,0,"a.requisition_no");
			}

			$add_tp_stri_batch_sql=sql_select("select  a.entry_form,a.batch_id, a.dyeing_re_process,b.prod_id,b.ratio,c.item_category_id from pro_recipe_entry_mst a,pro_recipe_entry_dtls b,product_details_master c where a.id=b.mst_id and c.id=b.prod_id   and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.item_category_id in(5,7,6) $all_batch_no_cond2 ");
			//echo "select  a.entry_form,a.batch_id, a.dyeing_re_process,b.prod_id,b.ratio,c.item_category_id from pro_recipe_entry_mst a,pro_recipe_entry_dtls b,product_details_master c where a.id=b.mst_id and c.id=b.prod_id   and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.item_category_id in(5,7,6) $all_batch_no_cond2 ";
			 
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
				$batch_item_ratio_total_arr[$val[csf('batch_id')]][$val[csf('item_category_id')]]['ratio_total'] += $val[csf('ratio')];
				$batch_item_ratio_total_arr[$val[csf('batch_id')]][$val[csf('item_category_id')]]['ratio_count'] += 1;
				$batchTotal_arr[$val[csf('batch_id')]]['batch_ratio_total']  += $val[csf('ratio')];
			}
			//print_r($category_add_tp_stri_batch_arr);
			unset($add_tp_stri_batch_sql);

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
				$yarn_lot_data=sql_select("select b.barcode_no, a.prod_id,c.
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
			$total_color_wise_batch_qty=$tot_machine_capacity=0;$total_stripping_qnty=0;$total_color_batch_qty=$total_others_color_batch_qty=0;
			$total_dyes_chemical_adding_qnty=$total_chemical_adding_qnty=$total_dyes_adding_qnty=0;$total_redying_batch_qty=$total_rewash_batch_qty=0;
			$shade_total_color_batch_qty=$shade_total_others_color_batch_qty=0;
			$dyes_cat_arr=6;$chemical_cat_arr=array(5,7);
			foreach($batchdata as $row)
			{
				$color_typeId=$sales_batch_arr[$row[csf("batch_id")]][$row[csf("sales_no")]][$row[csf("body_part_id")]];
			//	echo 	$color_typeId.'DD';
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["insert_date"] = $row[csf("insert_date")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["batch_no"] = $row[csf("batch_no")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["system_no"] = $row[csf("system_no")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["batch_weight"] = $row[csf("batch_weight")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["color_id"] = $row[csf("color_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["color_type"] = $color_typeId;
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["color_range_id"] = $row[csf("color_range_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["extention_no"] = $row[csf("extention_no")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["total_trims_weight"] = $row[csf("total_trims_weight")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["batch_qnty"] += $row[csf("batch_qnty")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["item_description"] = $row[csf("item_description")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["po_id"] = $row[csf("po_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["body_part"] = $body_part[$row[csf("body_part_id")]];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["width_dia_type"] = $row[csf("width_dia_type")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["job_no_prefix_num"] = $row[csf("job_no_prefix_num")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["remarks"] = $row[csf("remarks")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["shift_name"] = $row[csf("shift_name")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["within_group"] = $row[csf("within_group")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["po_buyer"] = $row[csf("po_buyer")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["buyer_id"] = $row[csf("buyer_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["process_end_date"] = $row[csf("process_end_date")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["production_date"] = $row[csf("production_date")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["production_qnty"] = $batch_product_arr[$row[csf("batch_id")]][$row[csf("prod_id")]];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["prod_qnty_tot"] = $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["end_hours"] = $row[csf("end_hours")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["floor_id"] = $row[csf("floor_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["hour_unload_meter"] = $row[csf("hour_unload_meter")]; 
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["water_flow_meter"] = $row[csf("water_flow_meter")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["end_minutes"] = $row[csf("end_minutes")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["machine_id"] = $row[csf("machine_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["load_unload_id"] = $row[csf("load_unload_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["fabric_type"] = $row[csf("fabric_type")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["result"] = $row[csf("result")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["booking_no"] = $row[csf("booking_no")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["sales_no"] = $row[csf("sales_no")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["booking_without_order"] = $row[csf("booking_without_order")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["season"] = $row[csf("season")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["style_ref_no"] = $row[csf("style_ref_no")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["barcode_no"][] = $row[csf("barcode_no")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["process_id"] = $row[csf("process_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["ltb_btb_id"] = $row[csf("ltb_btb_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["multi_batch_load_id"] = $row[csf("multi_batch_load_id")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["batch_againstId"] = $row[csf("batch_against")];
				$data_array_batch[$row[csf("batch_id")]][$row[csf("prod_id")]]["add_tp_strip"] = $add_tp_stri_batch_arr[$row[csf("batch_id")]];
				
				$ttl_batch_prod_tot_arr[$row[csf("batch_id")]]['prod_qty']=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
				$ttl_batch_prod_tot_arr[$row[csf("batch_id")]]['trims']=$row[csf("total_trims_weight")];
				
				$machine_id_arr[$row[csf("batch_id")]]['machine_id']=$row[csf("machine_id")];
				$multi_batch_load_id=$multi_batch_arr[$row[csf("batch_id")]];
				//echo $multi_batch_load_id.'dsd';
				if($row[csf("extention_no")]=='') $row[csf("extention_no")]=0;
				
				$batch_againstId=$row[csf("batch_against")];
				if($row[csf("process_id")]==147) //Re dyeing
				{
					if($dyingchkBatch[$row[csf("batch_id")]] =="")
						{
							$total_redying_batch_qty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
							$dyingchkBatch[$row[csf("batch_id")]] =$row[csf("batch_id")];
						}
				}
				if($row[csf("process_id")]==148) //Re Wash...
				{
					if($dyinWgchkBatch[$row[csf("batch_id")]] =="")
						{
							$total_rewash_batch_qty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
							$dyinWgchkBatch[$row[csf("batch_id")]] =$row[csf("batch_id")];
						}
				}
				if($row[csf("process_id")]==294) //Stripping...
				{
					if($dyinStripchkBatch[$row[csf("batch_id")]] =="")
						{
							$total_stripping_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
							$dyinStripchkBatch[$row[csf("batch_id")]] =$row[csf("batch_id")];
						}
				}
				//294
				if($multi_batch_load_id==1 && $row[csf('machine_id')] != 0)
				{
						if($multichkBatch[$row[csf("system_no")]] =="")
						{
						$tot_process_end_date_arr[$row[csf('production_date')]]= $row[csf('production_date')];
						$multichkBatch[$row[csf("system_no")]]=$row[csf("system_no")];
						}
					//$tot_process_end_date_arr[$row[csf('system_no')]]= $row[csf('system_no')];
					$total_running_machine_arr[$row[csf('machine_id')]]=$row[csf('machine_id')];
					$tot_machine_capacity_arr[$row[csf('batch_id')]]= $machine_capacity_arr[$row[csf('machine_id')]];
				}
				
				//if($row[csf('result')]!= 4 && $row[csf('extention_no')]=="")
				//{
				/*$color_wise_batch_arr[$row[csf("color_range_id")]]["batch_qnty"]= $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"]+$row[csf("total_trims_weight")];
				$color_wise_batch_arr[$row[csf("color_range_id")]]["total_trims_weight"]= $row[csf("total_trims_weight")];
				$total_color_wise_batch_qty+= $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"]+$row[csf("total_trims_weight")];;
				$color_wise_batch_arr[$row[csf("color_range_id")]]["batch_ids"].=$row[csf("batch_id")].',';*/
				//}
				//Color Batch qty
				if($row[csf("extention_no")]=='') $row[csf("extention_no")]=0;
				
				//echo $row[csf("extention_no")].'='.$row[csf("result")].'<br>';
				if($row[csf("extention_no")]==0 && $row[csf("result")]==1)
				{
					if($color_typeId==1 || $color_typeId==5) //Solid ,AOP
					{
						
						if($colorchkBatch[$row[csf("batch_id")]][$color_typeId] =="")
						{
							
							if($colorchkBatchTrim[$row[csf("batch_id")]] =="")
							{
								$total_trims_weight==0;
								$total_trims_weight=$row[csf("total_trims_weight")];
								$colorchkBatchTrim[$row[csf("batch_id")]] =$row[csf("batch_id")];
								$total_color_batch_qty+=$total_trims_weight;
								
							}
							//echo $total_trims_weight.'T,';
							$batch_prod_tot=$batch_product_arr3[$row[csf("batch_id")]][$row[csf("prod_id")]][$color_typeId];
							//echo $batch_prod_tot.'D,';
							$total_color_batch_qty += $batch_prod_tot ;
							//$total_color_batch_qty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
							$colorchkBatch[$row[csf("batch_id")]][$color_typeId] =$row[csf("batch_id")];
						}
						
					}
					else
					{
						if($colorchkBatch2[$row[csf("batch_id")]][$color_typeId] =="")
						{
							if($colorchkBatchTrim2[$row[csf("batch_id")]] =="")
							{
								$trims_weight==0;
								$trims_weight=$row[csf("total_trims_weight")];
								$colorchkBatchTrim2[$row[csf("batch_id")]] =$row[csf("batch_id")];
								$total_others_color_batch_qty+=$trims_weight;
								
							}
							
							$batch_prod_tot=$batch_product_arr3[$row[csf("batch_id")]][$row[csf("prod_id")]][$color_typeId];
							$total_others_color_batch_qty += $batch_prod_tot;
							
							$colorchkBatch2[$row[csf("batch_id")]][$color_typeId] =$row[csf("batch_id")];
						}
						//if($colorchkBatch2[$row[csf("batch_id")]] =="")
						//{
							//$batch_prod_tot=$sales_batch_product_arr[$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("body_part_id")]][$color_typeId];
							//$total_others_color_batch_qty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
							
							//$colorchkBatch2[$row[csf("batch_id")]] =$row[csf("batch_id")];
						//}
					}
				}
				//Color without Reprocess Batch qty
				if($row[csf("extention_no")]==0 && $row[csf("result")]==1)
				{
					if($color_typeId==1 || $color_typeId==5)
					{
						$batch_prod_tot=$sales_batch_product_arr[$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("body_part_id")]][$color_typeId];
						//if($shadechkBatch[$row[csf("batch_id")]] =="")
						//{
							//$shade_total_color_batch_qty+= $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
							$shade_total_color_batch_qty+= $batch_prod_tot + $row[csf("total_trims_weight")];
							$shadechkBatch[$row[csf("batch_id")]] =$row[csf("batch_id")];
						//}
						
					}
					else
					{
						//if($other_shadechkBatch[$row[csf("batch_id")]] =="")
						//{
							$batch_prod_tot=$sales_batch_product_arr[$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("body_part_id")]][$color_typeId];
							//$shade_total_others_color_batch_qty+= $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
							$shade_total_others_color_batch_qty+= $batch_prod_tot + $row[csf("total_trims_weight")];
							$other_shadechkBatch[$row[csf("batch_id")]] =$row[csf("batch_id")];
						//}
					}
				}
				
		
				if($batch_product_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]>0)
				{
					$production_date_count[$row[csf("production_date")]] = $row[csf("production_date")];
				}
				
				//re-process
				if($row[csf("extention_no")]>0 && $row[csf("result")]==1)
				{
					if($chkBatch[$row[csf("batch_id")]] =="")
					{
							//$total_reprocess_qty += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
						$total_reprocess_qty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch[$row[csf("batch_id")]] =$row[csf("batch_id")];
					}
				}

				//adding
				if($add_tp_stri_batch_arr[$row[csf("batch_id")]]==2)
				{
					$all_category_id=rtrim($all_categry_add_tp_stri_batch_arr[$row[csf("batch_id")]],',');
					$all_category_Arr=array_unique(explode(",",$all_category_id));
					//print_r($all_category_Arr);
				//	$all_categry_add_tp_stri_batch_arr[$val[csf("batch_id")]]
					
					$dyes_category_id=$dyes_add_tp_stri_batch_arr[$row[csf("batch_id")]];
					$chemical_category_id=$chemical_add_tp_stri_batch_arr[$row[csf("batch_id")]];
					$dyes_chemical_category_id=$dyes_chemical_add_tp_stri_batch_arr[$row[csf("batch_id")]];
					//echo $all_category_id.'<br>';
					if($chkBatch_1[$row[csf("batch_id")]] =="")
					{
						//$total_adding_qnty += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
						//$dyes_cat_arr=array(6);$chemical_cat_arr=array(5,7);
						$chemical_arr_diff=array_diff($all_category_Arr,$chemical_cat_arr);
						//print_r($arr_diff);
						if(count($all_category_Arr)>=2 && ($chemical_arr_diff))
						{
							//echo $all_category_id.'k';
							$total_dyes_chemical_adding_qnty +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						}
						else if(in_array(6,$all_category_Arr)) //====Dyes =6
						{
							$total_dyes_adding_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];//$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						}
						else if(in_array(5,$all_category_Arr) || in_array(7,$all_category_Arr)) //Dyes ,//Chemical =5,7 
						{
							$total_chemical_adding_qnty +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];//$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						}
						
						$total_adding_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch_1[$row[csf("batch_id")]] =$row[csf("batch_id")];
						//echo 'X,';
						 
					}
				}
				/*if($row[csf("extention_no")]>0 && $add_tp_stri_batch_arr[$row[csf("batch_id")]]==3) //Stripping
				{
					if($chkBatch_11[$row[csf("batch_id")]] =="")
					{
						
						$total_stripping_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch_11[$row[csf("batch_id")]] =$row[csf("batch_id")];
					}
				}*/
				if($row[csf("extention_no")]>0 && $add_tp_stri_batch_arr[$row[csf("batch_id")]]==1) //Topping
				{
					if($chkBatch_12[$row[csf("batch_id")]] =="")
					{
						
						$total_topping_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch_12[$row[csf("batch_id")]] =$row[csf("batch_id")];
					}
				}

				//rft
				if($row[csf("extention_no")]==0 && $row[csf("result")]==1 && $add_tp_stri_batch_arr[$row[csf("batch_id")]]=="")
				{
					if($chkBatch_2[$row[csf("batch_id")]] =="")
					{
					//echo 'B,';
						//$total_rft_qnty += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
						//$total_rft_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						//$chkBatch_2[$row[csf("batch_id")]] =$row[csf("batch_id")];
					}
				}
				
				if($chkBatch_4[$row[csf("batch_id")]] =="" && $batch_againstId!=2)
				{
					$without_fabric_type_production_arr[$row[csf("fabric_type")]] += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$without_fabric_type_production_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$chkBatch_4[$row[csf("batch_id")]] =$row[csf("batch_id")];
				}
				//if($chkBatch_3[$row[csf("batch_id")]] =="")
				if($chkBatch_3[$row[csf("batch_id")]] =="" && $row[csf("result")]==1 && $row[csf("extention_no")]>0) 
				{
					//$fabric_type_production_arr[$row[csf("fabric_type")]] += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
					//$fabric_type_production_total +=$batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
					$fabric_type_production_arr[$row[csf("fabric_type")]] += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$fabric_type_production_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$chkBatch_3[$row[csf("batch_id")]] =$row[csf("batch_id")];
				}

				if($row[csf("extention_no")]>0 && $row[csf("result")]==1 && $chkBatch_6[$row[csf("batch_id")]] =="")
				{
					$chkBatch_6[$row[csf("batch_id")]] =$row[csf("batch_id")];
					if($row[csf("within_group")]==1)
					{
						$buyer_wise_summary[$row[csf("po_buyer")]] += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					}
					else{
						$buyer_wise_summary[$row[csf("buyer_id")]] += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					}
					$buyer_wise_summary_batch_total += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];

					$dyeing_process_wise_batch_qnty_arr[$row[csf("process_id")]] +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$dyeing_process_wise_batch_qnty_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
				}

				//shade Matched
				if($row[csf("result")]==1) 
				{
					if($row[csf("within_group")]==1)
					{
						if($booking_type_arr[$row[csf("booking_no")]] == "Main" || $booking_type_arr[$row[csf("booking_no")]] == "Short")
						{
							//$shade_matched_wise_summary[$row[csf("po_buyer")]]["self"]+=$row[csf("batch_qnty")];
							//$shade_matched_buyer_total +=$row[csf("batch_qnty")];
							//echo $row[csf("extention_no")].'DDD';
							if($row[csf("extention_no")]<1){
								//echo $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"].'AA';
								if($chkBatch_41[$row[csf("batch_id")]] == ""){
									$chkBatch_41[$row[csf("batch_id")]] =$row[csf("batch_id")];
										
									$shade_matched_wise_summary[$row[csf("po_buyer")]]["self"]+=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
									$shade_matched_buyer_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];

									$shade_matched_wise_summary[$row[csf("po_buyer")]]["trim"]+=$row[csf("total_trims_weight")];
									$shade_matched_buyer_total +=$row[csf("total_trims_weight")];
								}
							}
						}
						else if($booking_type_arr[$row[csf("booking_no")]]=="Sample")
						{
							if($row[csf("booking_without_order")]==1)
							{
								//$shade_matched_wise_summary[$row[csf("po_buyer")]]["smn"]+=$row[csf("batch_qnty")];
								//$shade_matched_buyer_total +=$row[csf("batch_qnty")];
								if($row[csf("extention_no")]<1){
									if($chkBatch_42[$row[csf("batch_id")]] == ""){
										$chkBatch_42[$row[csf("batch_id")]] =$row[csf("batch_id")];

										$shade_matched_wise_summary[$row[csf("po_buyer")]]["smn"]+=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
										$shade_matched_buyer_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];

										$shade_matched_wise_summary[$row[csf("po_buyer")]]["trim"]+=$row[csf("total_trims_weight")];
										$shade_matched_buyer_total +=$row[csf("total_trims_weight")];
									}
								}
							}
							else
							{
								//$shade_matched_wise_summary[$row[csf("po_buyer")]]["sm"]+=$row[csf("batch_qnty")];
								//$shade_matched_buyer_total +=$row[csf("batch_qnty")];
								if($row[csf("extention_no")]<1){
									if($chkBatch_43[$row[csf("batch_id")]] == ""){
										$chkBatch_43[$row[csf("batch_id")]] =$row[csf("batch_id")];

										$shade_matched_wise_summary[$row[csf("po_buyer")]]["sm"]+=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
										$shade_matched_buyer_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];

										$shade_matched_wise_summary[$row[csf("po_buyer")]]["trim"]+=$row[csf("total_trims_weight")];
										$shade_matched_buyer_total +=$row[csf("total_trims_weight")];
									}
								}
							}
						}
					}
					else
					{
						//$shade_matched_wise_summary[$row[csf("buyer_id")]]["self"]+=$row[csf("batch_qnty")];
						//$shade_matched_buyer_total +=$row[csf("batch_qnty")];
						if($row[csf("extention_no")]<1){
							if($chkBatch_44[$row[csf("batch_id")]] == ""){
								$chkBatch_44[$row[csf("batch_id")]] =$row[csf("batch_id")];

								$shade_matched_wise_summary[$row[csf("buyer_id")]]["self"]+=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
								$shade_matched_buyer_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];

								$shade_matched_wise_summary[$row[csf("buyer_id")]]["trim"]+=$row[csf("total_trims_weight")];
								$shade_matched_buyer_total +=$row[csf("total_trims_weight")];
							}
						}
					}
				}
				else
				{
					if($row[csf("within_group")]==1)
					{
						if($booking_type_arr[$row[csf("booking_no")]] == "Main" || $booking_type_arr[$row[csf("booking_no")]] == "Short")
						{
							//$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["self"]+=$row[csf("batch_qnty")];
							//$shade_not_matched_buyer_total +=$row[csf("batch_qnty")];
							if($chkBatch_4[$row[csf("batch_id")]] == ""){
								$chkBatch_4[$row[csf("batch_id")]] =$row[csf("batch_id")];

								$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["self"]+=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
								$shade_not_matched_buyer_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];

								$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["trim"]+=$row[csf("total_trims_weight")];
								$shade_not_matched_buyer_total +=$row[csf("total_trims_weight")];
							}
						}
						else if($booking_type_arr[$row[csf("booking_no")]]=="Sample")
						{
							if($row[csf("booking_without_order")]==1)
							{
								//$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["smn"]+=$row[csf("batch_qnty")];
								//$shade_not_matched_buyer_total +=$row[csf("batch_qnty")];
								if($chkBatch_4[$row[csf("batch_id")]] == ""){
									$chkBatch_4[$row[csf("batch_id")]] =$row[csf("batch_id")];

									$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["smn"]+=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
									$shade_not_matched_buyer_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];

									$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["trim"]+=$row[csf("total_trims_weight")];
									$shade_not_matched_buyer_total +=$row[csf("total_trims_weight")];
								}
							}
							else
							{
								//$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["sm"]+=$row[csf("batch_qnty")];
								//$shade_not_matched_buyer_total +=$row[csf("batch_qnty")];
								if($chkBatch_4[$row[csf("batch_id")]] == ""){
									$chkBatch_4[$row[csf("batch_id")]] =$row[csf("batch_id")];

									$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["sm"]+=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
									$shade_not_matched_buyer_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];

									$shade_not_matched_wise_summary[$row[csf("po_buyer")]]["trim"]+=$row[csf("total_trims_weight")];
									$shade_not_matched_buyer_total +=$row[csf("total_trims_weight")];
								}
							}
						}
					}
					else
					{
						//$shade_not_matched_wise_summary[$row[csf("buyer_id")]]["self"]+=$row[csf("batch_qnty")];
						//$shade_not_matched_buyer_total +=$row[csf("batch_qnty")];
						if($chkBatch_4[$row[csf("batch_id")]] == ""){
							$chkBatch_4[$row[csf("batch_id")]] =$row[csf("batch_id")];

							$shade_not_matched_wise_summary[$row[csf("buyer_id")]]["self"]+=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
							$shade_not_matched_buyer_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];

							$shade_not_matched_wise_summary[$row[csf("buyer_id")]]["trim"]+=$row[csf("total_trims_weight")];
							$shade_not_matched_buyer_total +=$row[csf("total_trims_weight")];
						}

					}
				}
				
				//$dyeing_process_wise_batch_qnty_arr[$row[csf("process_id")]] +=$row[csf("batch_qnty")];
				//$dyeing_process_wise_batch_qnty_total +=$row[csf("batch_qnty")];

				//$btb_ltb_count=1;
				if($chkBatch_5[$row[csf("batch_id")]] == "")
				{
					$chkBatch_5[$row[csf("batch_id")]] =$row[csf("batch_id")];
					if($multi_batch_load_id==1 && $row[csf("extention_no")]==0 && $add_tp_stri_batch_arr[$row[csf("batch_id")]]=="" && $row[csf("result")] ==1){
						$btb_shade_matched[$row[csf("ltb_btb_id")]]["shade_matched"].=$row[csf("system_no")].',';//+=$btb_ltb_count;
						//echo "Z".$row[csf("ltb_btb_id")];
					}
					if($multi_batch_load_id==1)
					{
					$btb_shade_matched[$row[csf("ltb_btb_id")]]["total_batch"].=$row[csf("system_no")].',';
					}
				}
			}
			
			//print_r($tot_process_end_date_arr);
			$batch_row_span_arr =array();$ttl_batch_qty=0;$machine_check_array=array();
			$x=1;
			foreach ($data_array_batch as $batch_id => $batch_data_arr) 
			{
				$batch_row_span = 0;
				foreach ($batch_data_arr as $prod_id => $val) 
				{
					$batch_row_span++;
					//$val["prod_qnty_tot"];
					//$ttl_batch_qty+=$val["prod_qnty_tot"];
				
					
				}
				 $multi_batch_load_id=$multi_batch_arr[$batch_id];
				 $batch_againstId=$row[csf("batch_againstId")];
				//echo $val["total_trims_weight"].'FD';   
				if($batch_againstId==2) //Color Range wise Reprocess
				{
					$color_wise_batch_arr[$val[("color_range_id")]]["summary_batch_qnty"]+=$ttl_batch_prod_tot_arr[$batch_id]['prod_qty'];
					if($ttl_batch_prod_tot_arr[$batch_id]['trims']>0)
					{
					$color_wise_batch_arr[$val[("color_range_id")]]["total_trims_weight"]+=$ttl_batch_prod_tot_arr[$batch_id]['trims'];
					}
					$color_wise_batch_arr[$val[("color_range_id")]]["batch_ids"].=$batch_id.',';
				}
				else
				{
					$without_reprocess_color_wise_batch_arr[$val[("color_range_id")]]["summary_batch_qnty"]+=$ttl_batch_prod_tot_arr[$batch_id]['prod_qty'];
					if($ttl_batch_prod_tot_arr[$batch_id]['trims']>0)
					{
					$without_reprocess_color_wise_batch_arr[$val[("color_range_id")]]["total_trims_weight"]+=$ttl_batch_prod_tot_arr[$batch_id]['trims'];
					}
					$without_reprocess_color_wise_batch_arr[$val[("color_range_id")]]["batch_ids"].=$batch_id.',';
				}
				
				$total_color_wise_batch_qty+= $ttl_batch_prod_tot_arr[$batch_id]['prod_qty']+$ttl_batch_prod_tot_arr[$batch_id]['trims'];
				
				
				$ttl_batch_qty+=$ttl_batch_prod_tot_arr[$batch_id]['prod_qty']+$ttl_batch_prod_tot_arr[$batch_id]['trims'];
				$batch_row_span_arr[$batch_id] = $batch_row_span;
				
				$machine_id=$machine_id_arr[$batch_id]['machine_id'];
				if (!in_array($machine_id,$machine_check_array))
					{ $x++;
						
						
						 $machine_check_array[]=$machine_id;
						 $machine_capacity=$tot_machine_capacity_arr[$batch_id];
					}
					else
					{
						 $machine_capacity=0;
					}
					
				$tot_machine_capacity+=$machine_capacity;
			}
			//echo $ttl_batch_qty.'SS';	
			//print_r($color_wise_batch_arr);			
		}
		//echo $tot_machine_capacity.'DD';
		
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2 )
		{
				$subbatchdata=sql_select($sql_subcon); $all_sub_batch=array();
				foreach($subbatchdata as $row) 
				{
					$all_sub_batch[$row[csf("batch_id")]]=$row[csf("batch_id")];
				}
			//print_r($all_sub_batch);
			//$all_sub_batch_id= array_unique($all_sub_batch);
			$all_sub_batch_ids=implode(",",$all_sub_batch);
			//if($all_sub_batch_ids=="") { echo "Data Not Found"; die; }
			//====================

			$batchCond =$sub_dyeing_batch_id_cond= "";  $subbatchCond2 = $all_sub_batch_no_cond2 = ""; 
			$all_sub_batch_arr=explode(",",$all_sub_batch_ids);
			//print_r($all_sub_batch_arr);
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
			//if($all_batch_ids!="") $dyeing_batch_id_cond= "and f.batch_id in($all_batch_ids)"; else $dyeing_batch_id_cond="";
			
			
			 $sub_sql_prod_ref= sql_select("select a.batch_id,a.multi_batch_load_id,a.load_unload_id,b.prod_id,(b.batch_qnty) as production_qty
				from  pro_fab_subprocess a,pro_batch_create_dtls b
				where a.batch_id =b.mst_id and a.load_unload_id in(1,2) and a.load_unload_id in(1,2) and a.entry_form=38 and a.status_active=1 and b.status_active=1 $all_sub_batch_no_cond2 "); // and a.batch_id in ($all_batch_ids)
				
				
			foreach ($sub_sql_prod_ref as $val) 
			{
				if($val[csf("load_unload_id")]==2)
				{
				$batch_product_arr[$val[csf("batch_id")]][$val[csf("prod_id")]] = $val[csf("production_qty")];
				$batch_product_arr2[$val[csf("batch_id")]]["batch_prod_tot"] += $val[csf("production_qty")];
				}
				else
				{
					$multi_batch_arr[$val[csf("batch_id")]] = $val[csf("multi_batch_load_id")];
				}
			}
			

			/*$add_tp_stri_batch_sql=sql_select("select  a.batch_id, a.dyeing_re_process from pro_recipe_entry_mst a where a.entry_form = 60 and a.status_active = 1 and a.is_deleted = 0 $all_batch_no_cond2 group by a.batch_id, a.dyeing_re_process");
			foreach ($add_tp_stri_batch_sql as $val) 
			{
				$add_tp_stri_batch_arr[$val[csf("batch_id")]] = $val[csf("dyeing_re_process")];
			}
			unset($add_tp_stri_batch_sql);*/
			$add_tp_stri_batch_sql=sql_select("select  a.entry_form,a.batch_id, a.dyeing_re_process,b.prod_id,b.ratio,c.item_category_id from pro_recipe_entry_mst a,pro_recipe_entry_dtls b,product_details_master c where a.id=b.mst_id and c.id=b.prod_id   and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.item_category_id in(5,6,7) $all_batch_no_cond2 ");
			 
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
				$batch_item_ratio_total_arr[$val[csf('batch_id')]][$val[csf('item_category_id')]]['ratio_total'] += $val[csf('ratio')];
				$batch_item_ratio_total_arr[$val[csf('batch_id')]][$val[csf('item_category_id')]]['ratio_count'] += 1;
				$batchTotal_arr[$val[csf('batch_id')]]['batch_ratio_total']  += $val[csf('ratio')];
			}
			//print_r($category_add_tp_stri_batch_arr);
			unset($add_tp_stri_batch_sql);
			

			//$yarn_lot_arr=array();
		//	print_r($subbatchdata);
			$subbatch_data_arr=array();
			$sub_total_color_wise_batch_qty=$sub_tot_machine_capacity=0;
			$dyes_cat_arr=6;$chemical_cat_arr=array(5,7);
			foreach($subbatchdata as $row)
			{
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["insert_date"] = $row[csf("insert_date")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["batch_no"] = $row[csf("batch_no")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["system_no"] = $row[csf("system_no")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["batch_weight"] = $row[csf("batch_weight")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["color_id"] = $row[csf("color_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["color_range_id"] = $row[csf("color_range_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["extention_no"] = $row[csf("extention_no")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["total_trims_weight"] = $row[csf("total_trims_weight")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["batch_qnty"] += $row[csf("sub_batch_qnty")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["item_description"] = $row[csf("item_description")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["po_id"] = $row[csf("po_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["width_dia_type"] = $row[csf("width_dia_type")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["job_no_prefix_num"] = $row[csf("job_no_prefix_num")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["remarks"] = $row[csf("remarks")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["shift_name"] = $row[csf("shift_name")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["within_group"] = $row[csf("within_group")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["po_buyer"] = $row[csf("po_buyer")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["buyer_id"] = $row[csf("buyer_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["process_end_date"] = $row[csf("process_end_date")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["production_date"] = $row[csf("production_date")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["production_qnty"] = $row[csf("sub_batch_qnty")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["prod_qnty_tot"] = $sub_batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"];
				//echo $sub_batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"].'jj';
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["end_hours"] = $row[csf("end_hours")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["floor_id"] = $row[csf("floor_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["hour_unload_meter"] = $row[csf("hour_unload_meter")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["water_flow_meter"] = $row[csf("water_flow_meter")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["end_minutes"] = $row[csf("end_minutes")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["machine_id"] = $row[csf("machine_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["load_unload_id"] = $row[csf("load_unload_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["fabric_type"] = $row[csf("fabric_type")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["result"] = $row[csf("result")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["booking_no"] = $row[csf("booking_no")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["booking_without_order"] = $row[csf("booking_without_order")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["season"] = $row[csf("season")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["style_ref_no"] = $row[csf("style_ref_no")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["barcode_no"][] = $row[csf("barcode_no")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["process_id"] = $row[csf("process_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["ltb_btb_id"] = $row[csf("ltb_btb_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["multi_batch_load_id"] = $row[csf("multi_batch_load_id")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["batch_againstId"] = $row[csf("batch_against")];
				$subbatch_data_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["add_tp_strip"] = $add_tp_stri_batch_arr[$row[csf("batch_id")]];
				//echo  $sub_batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"].'BB';
				//echo  $row[csf("sub_batch_qnty")].'X';
				 $multi_batch_load_id=$multi_batch_arr[$row[csf("batch_id")]];
				 $batch_againstId=$row[csf("batch_againstId")];
				$sub_ttl_batch_prod_tot_arr[$row[csf("batch_id")]]["prod_qty"]= $row[csf("sub_batch_qnty")];
				$sub_ttl_batch_prod_tot_arr[$row[csf("batch_id")]]["trims"]=$row[csf("total_trims_weight")];
				
				
				
				//$sub_tot_day_process_end_date.= $row[csf('process_end_date')].',';
				if($multi_batch_load_id==1)
				{
						if($multichkBatch[$row[csf("system_no")]] =="")
						{
						$tot_process_end_date_arr[$row[csf('production_date')]]= $row[csf('production_date')];
						$multichkBatch[$row[csf("system_no")]]=$row[csf("system_no")];
						}
				}
				
				if($row[csf("process_id")]==147) //Re dyeing
				{
						if($dyingchkBatch[$row[csf("batch_id")]] =="")
						{
							$total_redying_batch_qty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
							$dyingchkBatch[$row[csf("batch_id")]] =$row[csf("batch_id")];
						}
				}
				if($row[csf("process_id")]==148) //Re Wash...
				{
					if($dyinWgchkBatch[$row[csf("batch_id")]] =="")
						{
							$total_rewash_batch_qty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
							$dyinWgchkBatch[$row[csf("batch_id")]] =$row[csf("batch_id")];
						}
				}
				if($row[csf("process_id")]==294) //Stripping...
				{
					if($dyinStripchkBatch[$row[csf("batch_id")]] =="")
						{
							$total_stripping_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
							$dyinStripchkBatch[$row[csf("batch_id")]] =$row[csf("batch_id")];
						}
				}
				
				/*if($row[csf('result')]!= 4 && $row[csf('extention_no')]==0)
				{
				$color_wise_batch_arr[$row[csf("color_range_id")]]["batch_qnty"]= $row[csf("sub_batch_qnty")]+$row[csf("total_trims_weight")];
				$color_wise_batch_arr[$row[csf("color_range_id")]]["total_trims_weight"]+= $row[csf("total_trims_weight")];
				$color_wise_batch_arr[$row[csf("color_range_id")]]["batch_ids"].=$row[csf("batch_id")].',';
				$sub_total_color_wise_batch_qty+= $row[csf("sub_batch_qnty")]+$row[csf("total_trims_weight")];
				}
				*/
				if($multi_batch_load_id==1 && $row[csf('machine_id')] != 0)
				{
					$sub_machine_id_arr[$row[csf("batch_id")]]['machine_id']=$row[csf('machine_id')];
					
					$total_running_machine_arr[$row[csf('machine_id')]]=$row[csf('machine_id')];
					$sub_tot_machine_capacity_arr[$row[csf("batch_id")]]= $machine_capacity_arr[$row[csf('machine_id')]];
				}

				if($batch_product_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]>0)
				{
					$production_date_count[$row[csf("production_date")]] = $row[csf("production_date")];
				}

				//re-process
				if($row[csf("extention_no")]>0 && $row[csf("result")]==1)
				{
					if($chkBatch[$row[csf("batch_id")]] =="")
					{
						//$total_reprocess_qty += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
						$total_reprocess_qty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch[$row[csf("batch_id")]] =$row[csf("batch_id")];
						
					//	echo $row[csf("buyer_id")].'D';
						
						$buyer_wise_summary[$row[csf("buyer_id")]] += $sub_batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						
						$buyer_wise_summary_batch_total += $sub_batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
	
						$dyeing_process_wise_batch_qnty_arr[$row[csf("process_id")]] +=$sub_batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$dyeing_process_wise_batch_qnty_total +=$sub_batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						}
				}
 				
				//adding
				//echo "ASSSS".$add_tp_stri_batch_arr[$row[csf("batch_id")]];
				if($add_tp_stri_batch_arr[$row[csf("batch_id")]]==2)
				{
					
					$all_category_id=$all_categry_add_tp_stri_batch_arr[$row[csf("batch_id")]];
					$all_category_Arr=array_unique(explode(",",$all_category_id));
					
					//$dyes_category_id=$dyes_add_tp_stri_batch_arr[$row[csf("batch_id")]];
				//	$chemical_category_id=$chemical_add_tp_stri_batch_arr[$row[csf("batch_id")]];
					//$dyes_chemical_category_id=$dyes_chemical_add_tp_stri_batch_arr[$row[csf("batch_id")]];
					
					if($chkBatch_1[$row[csf("batch_id")]] =="")
					{
						  $chemical_arr_diff=array_diff($all_category_Arr,$chemical_cat_arr);
							
						/*if($dyes_category_id==6) //Dyes
						{
							$total_dyes_adding_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];//$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						}
						if($chemical_category_id==5 || $chemical_category_id==7) //Chemical
						{
							$total_chemical_adding_qnty +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];//$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						}
						if($dyes_chemical_category_id==23) //Dyes+Chemical
						{
							$total_dyes_chemical_adding_qnty +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];//$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						}*/
						
						if(count($all_category_Arr)>=2 && ($chemical_arr_diff))
						{
							//echo $all_category_id.'k';
							$total_dyes_chemical_adding_qnty +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						}
						else if(in_array(6,$all_category_Arr)) //====Dyes =6
						{
							$total_dyes_adding_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];//$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						}
						else if(in_array(5,$all_category_Arr) || in_array(7,$all_category_Arr)) //Dyes ,//Chemical =5,7 
						{
							$total_chemical_adding_qnty +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];//$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						}
						
						
						$total_adding_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch_1[$row[csf("batch_id")]] =$row[csf("batch_id")];
						//$btb_shade_matched[$row[csf("ltb_btb_id")]]["shade_matched"] ++;
					}
				}
				/*if($row[csf("extention_no")]>0 && $add_tp_stri_batch_arr[$row[csf("batch_id")]]==3) //Stripping
				{
					if($chkBatch_11[$row[csf("batch_id")]] =="")
					{
						
						$total_stripping_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch_11[$row[csf("batch_id")]] =$row[csf("batch_id")];
					}
				}*/
				if($row[csf("extention_no")]>0 && $add_tp_stri_batch_arr[$row[csf("batch_id")]]==1) //Topping
				{
					if($chkBatch_12[$row[csf("batch_id")]] =="")
					{
						
						$total_topping_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch_12[$row[csf("batch_id")]] =$row[csf("batch_id")];
					}
				}


				//rft
				if($row[csf("extention_no")]==0 && $row[csf("result")]==1 && $add_tp_stri_batch_arr[$row[csf("batch_id")]]=="")
				{
					if($chkBatch_2[$row[csf("batch_id")]] =="")
					{
						//$total_rft_qnty += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
						$total_rft_qnty += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch_2[$row[csf("batch_id")]] =$row[csf("batch_id")];
					}
				}
				$batch_againstId=$row[csf("batch_against")];
				if($chkBatch_4[$row[csf("batch_id")]] =="" && $batch_againstId!=2)
				{
					$without_fabric_type_production_arr[$row[csf("fabric_type")]] += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$without_fabric_type_production_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$chkBatch_4[$row[csf("batch_id")]] =$row[csf("batch_id")];
				}
				
				//if($chkBatch_3[$row[csf("batch_id")]] =="")
				if($chkBatch_3[$row[csf("batch_id")]] =="" && $row[csf("result")]==1 && $row[csf("extention_no")]>0)
				{
					//$fabric_type_production_arr[$row[csf("fabric_type")]] += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
					//$fabric_type_production_total +=$batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"];
					$fabric_type_production_arr[$row[csf("fabric_type")]] += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$fabric_type_production_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$chkBatch_3[$row[csf("batch_id")]] =$row[csf("batch_id")];
				}

				if($row[csf("extention_no")]>0 && $row[csf("result")]==1 && $chkBatch_6[$row[csf("batch_id")]] =="")
				{
					$chkBatch_6[$row[csf("batch_id")]] =$row[csf("batch_id")];
					
					$buyer_wise_summary[$row[csf("buyer_id")]] += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					
					$buyer_wise_summary_batch_total += $batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$dyeing_process_wise_batch_qnty_arr[$row[csf("process_id")]] +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
					$dyeing_process_wise_batch_qnty_total +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
				}

				//shade Matched
				if($row[csf("result")]==1) 
				{
					//if($row[csf("extention_no")]<1){
						//if($chkBatch_45[$row[csf("batch_id")]] == ""){
							$chkBatch_45[$row[csf("batch_id")]] =$row[csf("batch_id")];

							$shade_matched_wise_summary[$row[csf("buyer_id")]]["subcon"]+=$row[csf("sub_batch_qnty")];
							$shade_matched_buyer_total +=$row[csf("sub_batch_qnty")];

							$shade_matched_wise_summary[$row[csf("buyer_id")]]["trim"]+=$row[csf("total_trims_weight")];
							$shade_matched_buyer_total +=$row[csf("total_trims_weight")];
						//}
					//}
				}
				else
				{
					//$shade_not_matched_wise_summary[$row[csf("buyer_id")]]["self"]+=$row[csf("batch_qnty")];
					//$shade_not_matched_buyer_total +=$row[csf("batch_qnty")];
					//if($chkBatch_4[$row[csf("batch_id")]] == ""){
						$chkBatch_4[$row[csf("batch_id")]] =$row[csf("batch_id")];

						$shade_not_matched_wise_summary[$row[csf("buyer_id")]]["subcon"]+=$row[csf("sub_batch_qnty")];
						$shade_not_matched_buyer_total +=$row[csf("sub_batch_qnty")];

						$shade_not_matched_wise_summary[$row[csf("buyer_id")]]["trim"]+=$row[csf("total_trims_weight")];
						$shade_not_matched_buyer_total +=$row[csf("total_trims_weight")];
					//}
				}

				//$dyeing_process_wise_batch_qnty_arr[$row[csf("process_id")]] +=$row[csf("batch_qnty")];
				//$dyeing_process_wise_batch_qnty_total +=$row[csf("batch_qnty")];

				//$btb_ltb_count=1;
				if($chkBatch_5[$row[csf("batch_id")]] == "")
				{
					$chkBatch_5[$row[csf("batch_id")]] =$row[csf("batch_id")];
					if($multi_batch_load_id==1 && $row[csf("extention_no")]==0 && $add_tp_stri_batch_arr[$row[csf("batch_id")]]=="" && $row[csf("result")] ==1){
					//$btb_shade_matched[$row[csf("ltb_btb_id")]]["shade_matched"];
						$btb_shade_matched[$row[csf("ltb_btb_id")]]["shade_matched"].=$row[csf("system_no")].',';//+=$btb_ltb_count;
						//echo "T".$row[csf("ltb_btb_id")];
					}
					if($multi_batch_load_id==1)
					{
					$btb_shade_matched[$row[csf("ltb_btb_id")]]["total_batch"].=$row[csf("system_no")].',';
					}
				}
			}
			//print_r($buyer_wise_summary);
			$x=1;
			$sub_batch_row_span_arr =array();$sub_ttl_batch_prod=0;$sub_machine_check_array=array();
			foreach ($subbatch_data_arr as $batch_id => $batch_data_arr) 
			{
				$sub_batch_row_span = 0;
				foreach ($batch_data_arr as $prod_id=>$row) 
				{
					$sub_batch_row_span++;
					$sub_batch_qnty_arr[$batch_id]+= $row["production_qnty"]; 
					
					//$sub_ttl_batch_prod+=$row["production_qnty"]+$row["total_trims_weight"];//$sub_ttl_batch_prod_tot_arr[$batch_id]["trims"];
					$sub_buyer_batch_qnty_arr[$row["buyer_id"]]+= $row["production_qnty"]+$sub_ttl_batch_prod_tot_arr[$batch_id]["trims"];
				}
				 $multi_batch_load_id=$multi_batch_arr[$batch_id];;
				 $batch_againstId=$row[("batch_againstId")];
				 
			//	$sub_buyer_batch_qnty_arr[$row[csf("batch_id")]]["prod_qty"]= $sub_batch_qnty_arr[$batch_id]+$sub_ttl_batch_prod_tot_arr[$batch_id]["trims"];
				//$sub_ttl_batch_prod_tot_arr[$row[csf("batch_id")]]["trims"]
				//prod_qnty_tot
				//echo $sub_ttl_batch_prod_tot_arr[$batch_id]["prod_qty"].'FD';
				$sub_ttl_batch_prod+=$sub_batch_qnty_arr[$batch_id]+$sub_ttl_batch_prod_tot_arr[$batch_id]["trims"];
				if($batch_againstId==2)  //Reprocess
				{
					$color_wise_batch_arr[$row[("color_range_id")]]["summary_batch_qnty"]+=$sub_batch_qnty_arr[$batch_id];//$sub_ttl_batch_prod_tot_arr[$batch_id]["prod_qty"];
					if($sub_ttl_batch_prod_tot_arr[$batch_id]["trims"]>0)
					{
					$color_wise_batch_arr[$row[("color_range_id")]]["total_trims_weight"]+= $sub_ttl_batch_prod_tot_arr[$batch_id]["trims"];
					}
					$color_wise_batch_arr[$row[("color_range_id")]]["batch_ids"].=$batch_id.',';
					}
				else
				{
					if($batch_againstId==2)  //Reprocess
					{
					$without_reprocess_color_wise_batch_arr[$row[("color_range_id")]]["summary_batch_qnty"]+=$sub_batch_qnty_arr[$batch_id];//$sub_ttl_batch_prod_tot_arr[$batch_id]["prod_qty"];
					if($sub_ttl_batch_prod_tot_arr[$batch_id]["trims"]>0)
					{
					$without_reprocess_color_wise_batch_arr[$row[("color_range_id")]]["total_trims_weight"]+= $sub_ttl_batch_prod_tot_arr[$batch_id]["trims"];
					}
					$without_reprocess_color_wise_batch_arr[$row[("color_range_id")]]["batch_ids"].=$batch_id.',';
					}	
				}
				
				$sub_total_color_wise_batch_qty+= $sub_batch_qnty_arr[$batch_id]+$sub_ttl_batch_prod_tot_arr[$batch_id]["trims"];
				
				//echo $sub_ttl_batch_prod_tot_arr[$batch_id]["prod_qty"].'DD';
				//$sub_ttl_batch_prod+=$sub_ttl_batch_prod_tot_arr[$batch_id]["prod_qty"]+$sub_ttl_batch_prod_tot_arr[$batch_id]["trims"];
				$sub_batch_row_span_arr[$batch_id] = $sub_batch_row_span;
				
				$machine_id=$sub_machine_id_arr[$batch_id]['machine_id'];
				if (!in_array($machine_id,$sub_machine_check_array))
					{ $x++;
						
						
						 $sub_machine_check_array[]=$machine_id;
						 $sub_machine_capacity=$sub_tot_machine_capacity_arr[$batch_id];
					}
					else
					{
						 $sub_machine_capacity=0;
					}
					
				$sub_tot_machine_capacity+=$sub_machine_capacity;
			}	
			//echo $sub_ttl_batch_prod.'XXX';		
		}			
	}
	$all_days_total_date=count($tot_process_end_date_arr);
		
		if($date_search_type==1) $date_type_msg="Dyeing Date"; else $date_type_msg="Insert Date";
		//echo $date_type_msg;
		$load_hr=array();
		$load_min=array();
		$load_date=array();
		$water_flow_arr=array(); $load_hour_meter_arr=array();
		if ($working_company==0) $workingCompany_name_cond1=""; else $workingCompany_name_cond1="  and service_company='".$working_company."' ";
		if ($working_company==0) $workingCompany_name_cond13=""; else $workingCompany_name_cond13="  and f.service_company='".$working_company."' ";
		if ($company==0) $companyCond1=""; else $companyCond1="  and f.company_id=$company";
	
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
		$sub_load_time_data=sql_select("select f.batch_id, f.water_flow_meter, f.batch_no, f.load_unload_id, f.process_end_date, f.end_hours, f.hour_load_meter, f.end_minutes from pro_fab_subprocess f where f.load_unload_id=1 and f.entry_form=38 $companyCond1 and f.status_active=1  and f.is_deleted=0 $workingCompany_name_cond13 $sub_dyeing_batch_id_cond");
		//echo "select f.batch_id, f.water_flow_meter, f.batch_no, f.load_unload_id, f.process_end_date, f.end_hours, f.hour_load_meter, f.end_minutes from pro_fab_subprocess f where f.load_unload_id=1 and f.entry_form=38 $companyCond1 and f.status_active=1  and f.is_deleted=0 $workingCompany_name_cond13 $sub_dyeing_batch_id_cond";
		
		foreach($sub_load_time_data as $row_time)// for Loading time
		{
			$sub_load_hr[$row_time[csf('batch_id')]]=$row_time[csf('end_hours')];
			$sub_load_min[$row_time[csf('batch_id')]]=$row_time[csf('end_minutes')];
			$sub_load_date[$row_time[csf('batch_id')]]=$row_time[csf('process_end_date')];
			$sub_water_flow_arr[$row_time[csf('batch_id')]]=$row_time[csf('water_flow_meter')];
			$sub_load_hour_meter_arr[$row_time[csf('batch_id')]]=$row_time[csf('hour_load_meter')];
		}
		unset($sub_load_time_data);
		
		
	
			ob_start();
			if($cbo_type==1)
			{
				?>
				<div>
					<fieldset style="width:1450px;">
						<div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong><br><strong> Dyeing WIP </strong> </div>
						<div>
							<? 
							//$batchdata = sql_select($sql);
							$batchdata = sql_select($sql);
							foreach($batchdata as $row)
							{
								$batch_id.=$row[csf('id')].",";
							}
							$batch_ids=rtrim($batch_id,',');
							$baIds=chop($batch_id,','); $ba_cond_in="";
							$ba_ids=count(array_unique(explode(",",$batch_ids)));
							if($ba_ids>1000 && $db_type==2)
							{
							$ba_cond_in=" and (";
							$baIdsArr=array_chunk(explode(",",$baIds),999);
							foreach($baIdsArr as $ids)
							{
							$ids=implode(",",$ids);
							$ba_cond_in.=" f.batch_id in($ids) or"; 
							}
							$ba_cond_in=chop($ba_cond_in,'or ');
							$ba_cond_in.=")";
							}
							else
							{
							$ba_cond_in=" and f.batch_id in($baIds)";
							}
				
							$sql_batch_id=sql_select("select f.batch_id from  pro_fab_subprocess f where f.entry_form =35 $companyCond1 and f.load_unload_id=2 and f.status_active=1 and f.is_deleted=0  $unload_batch_cond2  $cbo_prod_source_cond $machine_cond $floor_id_cond $cbo_prod_type_cond $dyeing_batch_id_cond $ba_cond_in");
							//echo "select f.batch_id from  pro_fab_subprocess f where f.entry_form =35 $companyCond1 and f.load_unload_id=2 and f.status_active=1 and f.is_deleted=0  $unload_batch_cond2  $cbo_prod_source_cond $machine_cond $floor_id_cond $cbo_prod_type_cond $dyeing_batch_id_cond $ba_cond_in";
						
							//$tot_row=1;$batch_id='';
							foreach($sql_batch_id as $row_batch)
							{
							//if($batch_id=='') $batch_id=$row_batch[csf('batch_id')];else $batch_id.=",".$row_batch[csf('batch_id')];
							$batch_unload_check[$row_batch[csf('batch_id')]]=$row_batch[csf('batch_id')];
							}
							unset($sql_batch_id);
							
							if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
							{

								if (count($batchdata)>0)
								{
									?>
									<div align="left"> <b>Self batch</b></div>
									<table class="rpt_table" width="1450" cellpadding="0" cellspacing="0" border="1" rules="all" align="left" id="table_header_1">
										<thead>
											<tr>
												<th width="30">SL</th>
												<? if($group_by==3 || $group_by==0){ ?>
													<th width="80">M/C No</th>
												<? } ?>
												<? if($group_by==2 || $group_by==0){ ?>
													<th width="80">Floor</th>  
												<? } if($group_by==1 || $group_by==0){ ?> 
													<th width="80">Shift</th>
												<? } ?>
												<th width="100">Booking No</th>
												<th width="100">Buyer</th>
												<th width="80">Style</th>
												<th width="90">FSO No</th>
												<th width="100">Fabrics Desc</th>
												<th width="70">Dia/Width Type</th>
												<th width="80">Color Name</th>
												<th width="90">Batch No</th>
												<th width="40">Ext. No</th>
												<th width="80">Fabric Weight</th>
												<th width="80">Trims Weight</th>
												<th width="80">Total Batch Weight</th>
												<th width="100">Loading Date & Time</th>
												<th>process</th>
											</tr>
										</thead>
									</table>
									<div style=" max-height:360px; width:1470px; overflow-y:scroll;" id="scroll_body" align="left">
										<table class="rpt_table" id="table_body" width="1450" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
											<tbody>
												<? 
												$i=1;$k=1;
												$f=0;
												$btq=0;$grand_total_batch_qty=0; $sub_trim_tot=0;$sub_batch_wgt=0;
												$batch_chk_arr=array();$group_by_arr=array();
												foreach($batchdata as $batch)
												{ 
													if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													if($batch_unload_check[$batch[csf('id')]]=='')
													{
														if($group_by!=0)
														{
															if($group_by==1)
															{
																$group_value=$batch[csf('floor_id')];
																$group_name="Floor";
																$group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
															}
															else if($group_by==2)
															{
																$group_value=$batch[csf('shift_name')];
																$group_name="Shift";
																$group_dtls_value=$shift_name[$batch[csf('shift_name')]];
															}
															else if($group_by==3)
															{
																$group_value=$batch[csf('machine_id')];
																$group_name="machine";
																$group_dtls_value=$machine_arr[$batch[csf('machine_id')]];
															}
															if (!in_array($group_value,$group_by_arr) )
															{
																if($k!=1)
																{ 	
																	?>
																	<tr class="tbl_bottom">
																		<td width="30">&nbsp;</td>
																		<? if($group_by==3 || $group_by==0){ ?>
																			<td width="80">&nbsp;</td> 
																		<? } ?>
																		<? if($group_by==2 || $group_by==0){ ?>
																			<td width="80">&nbsp;</td> 
																		<? } if($group_by==1 || $group_by==0){ ?>  
																			<td width="80">&nbsp;</td>
																		<? } ?>
																		<td width="100">&nbsp;</td>
																		<td width="100">&nbsp;</td>
																		<td width="80">&nbsp;</td>
																		<td width="90">&nbsp;</td>
																		<td width="100">&nbsp;</td>
																		<td width="70">&nbsp;</td>
																		<td width="80">&nbsp;</td>

																		<td width="90" >Sub.Total: </td>
																		<td width="40"></td>
																		<td width="80"><? echo number_format($btq,2); ?></td>
																		<td width="80"><? echo number_format($sub_trim_tot,2); ?></td>
																		<td width="80"><? echo number_format($sub_batch_wgt,2); ?></td>
																		<td width="100"></td>

																		<td>&nbsp;</td>
																	</tr>                                
																	<?
																	unset($btq);unset($sub_trim_tot);unset($sub_batch_wgt);
																}
																?>
																<tr bgcolor="#EFEFEF">
																	<td colspan="16" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
																</tr>
																<?
																$group_by_arr[]=$group_value;            
																$k++;
															}					
														}

														$order_id=$batch[csf('po_id')];
														$color_id=$batch[csf('color_id')];
														$desc=explode(",",$batch[csf('item_description')]); 
														$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')]))); 
														?>
														<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">

															<td width="30"><? echo $i; ?></td>

															<? if($group_by==3 || $group_by==0){ ?>
																<td  align="center" width="80" title="<? echo $machine_arr[$batch[csf('machine_id')]]; ?>"><p><? echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
																<?
															}
															if($group_by==2 || $group_by==0){ ?>
																<td width="80"><p><? echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
															<? } if($group_by==1 || $group_by==0){ ?>
																<td width="80"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
															<? } ?>
															<td  width="100" align="center"><p><? echo $batch[csf('sales_booking_no')]; ?></p></td>
															<td width="100" align="center" title="<? echo $batch[csf('po_buyer')];?>"><p><? if($batch[csf('within_group')]==2) echo $buyer_arr[$batch[csf('buyer_id')]]; else echo $buyer_arr[$batch[csf('po_buyer')]];?></p></td>

															<td  width="80" align="center"><p style="word-break: break-all;"><? echo $batch[csf('style_ref_no')]; ?></p></td>
															<td width="90" align="center"><p><? echo $batch[csf('fso_no')]; ?></p></td>

															<td  width="100"><p style="word-break: break-all;"><? echo $batch[csf('item_description')]; ?></p></td>
															<td  width="70" title="<? echo $fabric_typee[$batch[csf('width_dia_type')]]; ?>"><p><? echo $fabric_typee[$batch[csf('width_dia_type')]]; ?></p></td>
															<td  width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
															<td  align="center" width="90" style="word-break: break-all;"><p><? echo $batch[csf('batch_no')]; ?></p></td>
															<td  align="center" width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
															<td align="right" width="80"   title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
															<? 
															$total_batch_wgt=0;
															if($batchTrimChk[$batch[csf('id')]]=="")
															{
																$total_batch_wgt = $batch[csf('batch_qnty')]+ $batch[csf('total_trims_weight')];
																?>
																<td align="right" width="80" ><? echo number_format($batch[csf('total_trims_weight')],2);  ?></td>
																<td align="right" width="80" ><? echo number_format($total_batch_wgt,2);  ?></td>
																<?
																$batchTrimChk[$batch[csf('id')]] = $batch[csf('id')]; 
																$sub_trim_tot += $batch[csf('total_trims_weight')];
																$grand_trim_tot += $batch[csf('total_trims_weight')];
															}
															else
															{
																$total_batch_wgt = $batch[csf('batch_qnty')];
																?>
																<td align="right" width="80" ><?  ?></td>
																<td align="right" width="80" ><? echo number_format($total_batch_wgt,2);  ?></td>
																<?
															}
															?>

															<td width="100" align="center"><p><?  echo  ($batch[csf('process_end_date')] == '0000-00-00' || $batch[csf('process_end_date')] == '' ? '' : change_date_format($batch[csf('process_end_date')])).'<br>'.$batch[csf('end_hours')].':'.$batch[csf('end_minutes')]; ?></p></td>

															<td align="center"><? echo $conversion_cost_head_array[$batch_against[$batch[csf('process_id')]]];?> &nbsp;</td>
														</tr>
														<? 
														$i++;
														$btq+=$batch[csf('batch_qnty')];
														$grand_total_batch_qty+=$batch[csf('batch_qnty')];

														$sub_batch_wgt += $total_batch_wgt;
														$grand_batch_wgt += $total_batch_wgt;
													}
												} 
												?>
											</tbody>
										</table>
										<table class="rpt_table" width="1450" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
											<tfoot>
												<?
												if($group_by!=0)
												{
													?>
													<tr>
														<th width="30">&nbsp;</th>
														<? if($group_by==3 || $group_by==0){ ?>
															<th width="80">&nbsp;</th>
														<? } ?>
														<? if($group_by==2 || $group_by==0){ ?>
															<th width="80">&nbsp;</th>  
														<? } if($group_by==1 || $group_by==0){ ?> 
															<th width="80">&nbsp;</th>
														<? } ?>
														<th width="100">&nbsp;</th>
														<th width="100">&nbsp;</th>
														<th width="80">&nbsp;</th>
														<th width="90">&nbsp;</th>
														<th width="100">&nbsp;</th>
														<th width="70">&nbsp;</th>
														<th width="80">&nbsp;</th>
														<th width="130" colspan="2">Total</th>
														<th width="80" align="right"><? echo number_format($btq,2); ?></th>
														<th width="80" align="right"><? echo number_format($sub_trim_tot,2);?></th>
														<th width="80" align="right"><? echo number_format($sub_batch_wgt,2);?></th>
														<th width="100">&nbsp;</th>
														<th>&nbsp;</th>
													</tr>
													<?
												}
												?>
												<tr>
													<th width="30">&nbsp;</th>
													<? if($group_by==3 || $group_by==0){ ?>
														<th width="80">&nbsp;</th>
													<? } ?>
													<? if($group_by==2 || $group_by==0){ ?>
														<th width="80">&nbsp;</th>  
													<? } if($group_by==1 || $group_by==0){ ?> 
														<th width="80">&nbsp;</th>
													<? } ?>
													<th width="100">&nbsp;</th>
													<th width="100">&nbsp;</th>
													<th width="80">&nbsp;</th>
													<th width="90">&nbsp;</th>
													<th width="100">&nbsp;</th>
													<th width="70">&nbsp;</th>
													<th width="80">&nbsp;</th>

													<th width="90">GrandTotal</th>
													<th width="40"></th>
													<th width="80" align="right" id="value_grand_batch_qnty"><? echo number_format($grand_total_batch_qty,2); ?></th>
													<th width="80" align="right" id="value_grand_trims_qnty"><? echo number_format($grand_trim_tot,2); ?></th>
													<th width="80" align="right" id="value_grand_tot_batch_wgt"><? echo number_format($grand_batch_wgt,2); ?></th>
													<th width="100">&nbsp;</th>
													<th>&nbsp;</th>
												</tr>
											</tfoot>
										</table>
									</div>
									<br/>
									<? 
								}
							}

							?>
						</div>
					</fieldset>
					<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
				</div>
				<?
			}
			else if($cbo_type==2) 
			{
				?>
				<div>
					<fieldset style="width:1350px;">
						<div align="center"><strong> <? echo $company_library[$company]; ?> </strong><br> <strong> Daily Dyeing Production </strong><br>
							<?

							echo  ($date_from == '0000-00-00' || $date_from == '' ? '' : change_date_format($date_from)).' To ';echo  ($date_to == '0000-00-00' || $date_to == '' ? '' : change_date_format($date_to));
							?>
						</div>
						<?  

						if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
						{
							if (count($batchdata)>0)
							{
								?>
								<table cellpadding="0"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
									<thead>
										<tr>
											<th colspan="3">Production Summary</th>
										</tr>
										<tr>
											<th width="130">Details </th> 
											<th width="120">Prod. Qty. </th>
											<th>%</th>
										</tr>
									</thead>
									<tbody>
										<tr bgcolor="#E9F3FF">
											<td title="Solid and AOP">Color Batch</td>
											<td align="right" title=""><? echo number_format($total_color_batch_qty,2);?></td>
											<td align="right"><? echo number_format(($total_color_batch_qty*100)/($total_others_color_batch_qty + $total_color_batch_qty),2);?></td>
										</tr>
                                        <tr bgcolor="#FFFFFF">
											<td>Others Batch</td>
											<td align="right"><? echo number_format($total_others_color_batch_qty,2);?></td>
											<td align="right"><? echo number_format(($total_others_color_batch_qty*100)/($total_others_color_batch_qty + $total_color_batch_qty),2);?></td>
										</tr>
                                        <?
										$total_rft_qnty=0;
										 $total_rft_qnty=$total_color_batch_qty+$total_others_color_batch_qty;?>
                                        <tr bgcolor="#E9F3FF">
											<td><b>Total RFT</b></td>
											<td align="right"><b><? echo number_format($total_rft_qnty,2);?></b></td>
											<td align="right"><b><?  if($total_rft_qnty) echo  number_format(($total_rft_qnty*100)/$total_rft_qnty,2);else echo "0";?></b></td>
										</tr>
                                        
										<tr bgcolor="#FFFFFF">
										<td>Dyes Adding</td> 
										<td align="right"><? echo number_format($total_dyes_adding_qnty,2);?></td>
										<td align="right"><? if($total_dyes_adding_qnty) echo number_format(($total_dyes_adding_qnty*100)/($total_dyes_adding_qnty + $total_chemical_adding_qnty + $total_dyes_chemical_adding_qnty),2);else echo "0"; ?></td>
										</tr>
                                        <tr bgcolor="#E9F3FF">
										<td>Chemical Adding</td> 
										<td align="right"><? echo number_format($total_chemical_adding_qnty,2);?></td>
										<td align="right"><? if($total_chemical_adding_qnty) echo number_format(($total_chemical_adding_qnty*100)/($total_dyes_adding_qnty + $total_chemical_adding_qnty + $total_dyes_chemical_adding_qnty),2);else echo "0"; ?></td>
										</tr>
                                        <tr bgcolor="#FFFFFF">
										<td>Dyes + Chemical Adding</td> 
										<td align="right"><? echo number_format($total_dyes_chemical_adding_qnty,2);?></td>
										<td align="right"><? if($total_dyes_chemical_adding_qnty) echo number_format(($total_dyes_chemical_adding_qnty*100)/($total_dyes_chemical_adding_qnty + $total_dyes_adding_qnty + $total_chemical_adding_qnty),2);else echo "0"; ?></td>
										</tr>
                                        <tr bgcolor="#E9F3FF">
										<td><b>Total Adding</b></td> 
                                        <?
										
                                        $total_adding=$total_dyes_adding_qnty+$total_chemical_adding_qnty+$total_dyes_chemical_adding_qnty;
										?>
										<td align="right"><b><? echo number_format($total_adding,2);?></b></td>
										<td align="right"><b><? if($total_adding) echo number_format(($total_adding*100)/$total_adding,2);else echo "0"; ?></b></td>
										</tr>
                                         <tr bgcolor="#FFFFFF">
                                         <?
                                         $total_rft_add=0; $total_rft_add_per=0;
										$total_rft_add=$total_rft_qnty+$total_adding+$total_topping_qnty;
										 ?>
										<td>Topping</td> 
										<td align="right"><? echo number_format($total_topping_qnty,2);?></td>
										<td align="right" title="Topping/Total*100"><? 
										if($total_topping_qnty) echo number_format($total_topping_qnty*100/$total_rft_add,2);
										else echo '0'; ?></td>
										</tr>
                                        
                                        <tr bgcolor="#FFFF00">
											<td><b>Total:</b></td> 
											<td align="right"><b><? 
											
											//$total_rft_add_per=(($total_rft_qnty*100)/($total_rft_qnty + $total_adding+$total_topping_qnty))+(($total_adding*100)/($total_rft_qnty + $total_adding));
											echo number_format($total_rft_add,2);?></b></td>
											<td align="right"><b><? echo number_format($total_rft_add/$total_rft_add*100,2);?></b></td>
										</tr>
                                        <tr bgcolor="#FFFFFF">
										<td>Re Wash</td> 
										<td align="right"><? echo number_format($total_rewash_batch_qty,2);?></td>
										<td align="right"><? //echo number_format(($total_rewash_batch_qty*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty),2); ?></td>
										</tr>
                                         <tr bgcolor="#FFFFFF">
										<td>Re Dyeing</td> 
										<td align="right"><? echo number_format($total_redying_batch_qty,2);?></td>
										<td align="right"><? //echo number_format(($total_adding_qnty*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty),2); ?></td>
										</tr>
                                         <tr bgcolor="#E9F3FF">
										<td>Stripping</td> 
										<td align="right"><? echo number_format($total_stripping_qnty,2);?></td>
										<td align="right"><? echo number_format(($total_stripping_qnty*100)/($total_rft_qnty + $total_stripping_qnty + $total_reprocess_qty),2); ?></td>
										</tr>
                                        <?
                                        $total_reprocess_qty=0;
										$total_reprocess_qty=$total_redying_batch_qty+$total_rewash_batch_qty+$total_stripping_qnty;
										?>
										<tr bgcolor="#E9F3FF">
											<td><b>Total Re-Process</b></td> 
											<td align="right"><b><? echo number_format($total_reprocess_qty,2);?></b></td>  
											<td align="right"><? //echo number_format(($total_reprocess_qty*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty),2);?></td> 
										</tr>
										<tr bgcolor="#EBEBEB">
											<td align="right"><b>Grand Total:</b></td> 
											<td align="right">
												<b><? echo number_format($total_reprocess_qty + $total_rft_add,2); ?></b>
											</td> 
											<td align="right"><b><? echo number_format((($total_rft_add + $total_reprocess_qty)*100)/($total_rft_add + $total_reprocess_qty),2);?></b></td>  
											</tr>
											<tr bgcolor="#FFFFFF">
												<td><b>Avg. Prod. Per Day</b></td> 
												<td align="right" title="<? echo "days=".implode(",",array_filter(array_unique($production_date_count)));?>">
													<b><? 
													echo number_format(($total_reprocess_qty + $total_rft_add)/count(array_filter(array_unique($production_date_count))),2);
													?></b>	
												</td> 
												<td></td> 
											</tr>
                                            <tr>
                                            <td colspan="3"><b> Fabric type wise breakdown with Reprocess</b></td>
                                            </tr>
											<? 
											$ftsl=2;
											foreach($fabric_type_production_arr as $fabType=>$val)
											{ 
												if ($ftsl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													<td><? echo $fabric_type_for_dyeing[$fabType];?></td> 
													<td align="right"><? echo number_format($val,2);?></td> 
													<td align="right"><? echo number_format(($val/$fabric_type_production_total)*100,2);?></td> 
												</tr>
												<?
												$ftsl++;
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th>Total</th> 
												<th align="right"><? echo number_format($fabric_type_production_total,2);?></th> 
												<th align="right"><? echo number_format(100,2);?></th> 
											</tr>
										</tfoot>
									</table>

									<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
										<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
									</table>
                                    <table cellpadding="0"   style="margin:5px;" width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<thead>
											<tr>
												<th colspan="3">Fabric type wise breakdown without Reprocess</th>
											</tr>
											<!--<tr>
												<th width="40">Sl </th> 
												<th width="100">Buyer</th>
												<th width="100">Batch Total</th>
												<th>%</th>
											</tr>-->
										</thead>
										<tbody>
											<? 
											$sl=1;
											foreach($without_fabric_type_production_arr as $fab_type=> $qty)
											{
												if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													 
														<td><? echo $fabric_type_for_dyeing[$fab_type];?></td> 
													<td align="right"><? echo number_format($qty,2);?></td>
													<td align="right"><? echo number_format(($qty/$without_fabric_type_production_total)*100,2);?></td>
												</tr>
												<?
												$sl++;
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th>Total </th>
												<th><? echo number_format($without_fabric_type_production_total,2);?></th>
												<th><? if ($without_fabric_type_production_total>0) echo "100.00"; else echo "0.00";?></th>
											</tr>
										</tfoot>
									</table>
                                    

									<table cellpadding="0"   style="margin:5px;" width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<thead>
											<tr>
												<th colspan="4">Re-Process Summary (Buyer Wise)</th>
											</tr>
											<tr>
												<th width="40">Sl </th> 
												<th width="100">Buyer</th>
												<th width="100">Batch Total</th>
												<th>%</th>
											</tr>
										</thead>
										<tbody>
											<? 
											$sl=1;
											foreach($buyer_wise_summary as $buyer=> $bs)
											{
												if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													<td align="center"><? echo $sl;?></td>
													<td align="center"><? echo $buyer_arr[$buyer];?></td>
													<td align="right"><? echo number_format($bs,2);?></td>
													<td align="right"><? echo number_format(($bs/$buyer_wise_summary_batch_total)*100,2);?></td>
												</tr>
												<?
												$sl++;
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="2">Total </th>
												<th><? echo number_format($buyer_wise_summary_batch_total,2);?></th>
												<th><? if ($buyer_wise_summary_batch_total>0) echo "100.00"; else echo "0.00";?></th>
											</tr>
										</tfoot>
									</table>
									<table cellpadding="0" style="margin:5px;"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<thead>
											<tr>
												<th colspan="5">Color Range wise Summary With Reprocess</th>
											</tr>
											<tr>
												<th width="40">Sl </th> 
												<th width="100">Color Range</th>
												<th width="100">Quantity (Kgs)</th>
												<th  width="50">Percentage%</th>
												<th>No. Of Batches</th>
											</tr>
										</thead>
										<tbody>
											<? 
											$sl=1;$tot_batch_qty=$tot_no_of_batch=$tot_total_trims_weight=0;
											//print_r($color_wise_batch_arr);ttl_batch_prod_tot
											foreach($color_wise_batch_arr as $color_rang_id=> $val)
											{
												$total_color_qty+=$val['summary_batch_qnty']+$val['total_trims_weight'];
											}
											foreach($color_wise_batch_arr as $color_rang_id=> $summ)
											{
												if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												$batch_ids=rtrim($summ[batch_ids],',');
												$batch_ids_arr=array_unique(explode(",",$batch_ids));
												$tot_total_trims_weight+=$summ['total_trims_weight'];
												//echo count($batch_ids_arr).',';
												//echo $val['total_trims_weight'].'D';
												$tot_prod_qty_summ=$summ['summary_batch_qnty']+$summ['total_trims_weight'];
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													<td align="center"><? echo $sl;?></td>
													<td align="center"><? echo $color_range[$color_rang_id];?></td>
													<td align="right"><? echo number_format($tot_prod_qty_summ,2);?></td>
													<td align="right" title="Tot batch Qty=<? echo $total_color_qty;?>">
													<? echo number_format(($tot_prod_qty_summ/($total_color_qty))*100,2);?></td>
													<td align="right"><? echo count($batch_ids_arr);?></td>
												</tr>
												<?
												$sl++;
												$tot_batch_qty+=$tot_prod_qty_summ;
												$tot_no_of_batch+=count($batch_ids_arr);
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="2">Total </th>
												<th><? echo number_format($tot_batch_qty,2);?></th>
												<th><? echo number_format(($tot_batch_qty/($total_color_qty))*100,2);?></th>
												<th><? echo number_format($tot_no_of_batch,0);?></th>
											</tr>
										</tfoot>
									</table>
                                   
                                    <table cellpadding="0"   style="margin:5px;" width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<thead>
											<tr>
												<th colspan="5">Color Range wise Summary Without Reprocess</th>
											</tr>
											<tr>
												<th width="40">Sl </th> 
												<th width="100">Color Range</th>
												<th width="100">Quantity (Kgs)</th>
												<th  width="50">Percentage%</th>
												<th>No. Of Batches</th>
											</tr>
										</thead>
										<tbody>
											<? 
											$sl=1;$tot_batch_qty=$tot_no_of_batch=$tot_total_trims_weight=0;
											//print_r($color_wise_batch_arr);ttl_batch_prod_tot
											foreach($without_reprocess_color_wise_batch_arr as $color_rang_id=> $val)
											{
												$total_color_qty+=$val['summary_batch_qnty']+$val['total_trims_weight'];
											}
											foreach($without_reprocess_color_wise_batch_arr as $color_rang_id=> $summ)
											{
												if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												$batch_ids=rtrim($summ[batch_ids],',');
												$batch_ids_arr=array_unique(explode(",",$batch_ids));
												$tot_total_trims_weight+=$summ['total_trims_weight'];
												//echo count($batch_ids_arr).',';
												//echo $val['total_trims_weight'].'D';
												$tot_prod_qty_summ=$summ['summary_batch_qnty']+$summ['total_trims_weight'];
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													<td align="center"><? echo $sl;?></td>
													<td align="center"><? echo $color_range[$color_rang_id];?></td>
													<td align="right"><? echo number_format($tot_prod_qty_summ,2);?></td>
													<td align="right" title="Tot batch Qty=<? echo $total_color_qty;?>">
													<? echo number_format(($tot_prod_qty_summ/($total_color_qty))*100,2);?></td>
													<td align="right"><? echo count($batch_ids_arr);?></td>
												</tr>
												<?
												$sl++;
												$tot_batch_qty+=$tot_prod_qty_summ;
												$tot_no_of_batch+=count($batch_ids_arr);
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="2">Total </th>
												<th><? echo number_format($tot_batch_qty,2);?></th>
												<th><? echo number_format(($tot_batch_qty/($total_color_qty))*100,2);?></th>
												<th><? echo number_format($tot_no_of_batch,0);?></th>
											</tr>
										</tfoot>
									</table>
									<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
										<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
									</table>

									<table cellpadding="0"  width="640" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<thead>
											<tr>
												<th colspan="8">Summary Total (Shade Match)</th>
											</tr>
											<tr>
												<th width="100">Buyer</th>
												<th width="100">Self batch</th> 
												<th width="100">Smpl. Batch With Order</th>
												<th width="60">Smpl. Batch Without Order</th>
												<th width="60">Trims Weight</th>
												<th width="60">Inbound subcontract</th>
												<th width="100">Buyer Total</th>
												<th>%</th>
											</tr>
										</thead>
										<tbody>
											<? 
											$sl=1;
											foreach($shade_matched_wise_summary as $buyer=> $val)
											{
												if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												$tot_buyer_match_shade_subcon = $sub_buyer_batch_qnty_arr[$buyer];//$val["subcon"];
												$tot_buyer_match_shade = ($val["self"]+$val["sm"]+$val["smn"]+$val["trim"])+$tot_buyer_match_shade_subcon;
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													<td align="center"><? echo $buyer_arr[$buyer];?></td>
													<td align="right"><? echo number_format($val["self"],2); ?></td>
													<td align="right"><? echo number_format($val["sm"],2); ?></td>
													<td align="right"><? echo number_format($val["smn"],2); ?></td>
													<td align="right"><? echo number_format($val["trim"],2); ?></td>
													<td align="right"><? echo number_format($tot_buyer_match_shade_subcon,2); ?></td>
													<td align="right"><? echo number_format($tot_buyer_match_shade,2);?></td>
													<td align="right"><? echo number_format(($tot_buyer_match_shade/$shade_matched_buyer_total)*100,2);?></td>
												</tr>
												<?
												$sl++;
												$total_self += $val["self"];
												$total_sm += $val["sm"];
												$total_smn += $val["smn"];
												$total_trim += $val["trim"];
												$total_subcon += $tot_buyer_match_shade_subcon;
												$total_tot_buyer_match_shade += $tot_buyer_match_shade;
												$total_summury_shade_matched +=($tot_buyer_match_shade/$shade_matched_buyer_total)*100;

												$grand_total_self += $val["self"];
												$grand_total_sm += $val["sm"];
												$grand_total_smn += $val["smn"];
												$grand_total_trim += $val["trim"];
												$grand_total_subcon += $tot_buyer_match_shade_subcon;
												$grand_total_buyer += $tot_buyer_match_shade;
											}
											unset($shade_matched_wise_summary);
											?>
											<tr style="background-color: #eee;font-weight: bold;">
												<td width="100" align="right">Total</th>
													<td width="100" align="right"><? echo number_format($total_self,2);?></td> 
													<td width="100" align="right"><? echo number_format($total_sm,2);?></td>
													<td width="60" align="right"><? echo number_format($total_smn,2);?></td>
													<td width="60" align="right"><? echo number_format($total_trim,2);?></td>
													<td width="60"><? echo number_format($grand_total_subcon,2);?></td>
													<td width="100" align="right"><? echo number_format($total_tot_buyer_match_shade,2);?></td>
													<td align="right"><? echo number_format($total_summury_shade_matched,2);?></td>
												</tr>
											</tbody>
											<thead>
												<tr>
													<th colspan="8">Summary Total (Shade Not Match)</th>
												</tr>
											</thead>
											<tbody>
												<?
												foreach($shade_not_matched_wise_summary as $buyer=> $val)
												{
													if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
													$tot_buyer_match_not_shade_subcon = $val["subcon"];
													$tot_buyer_match_not_shade = ($val["self"]+$val["sm"]+$val["smn"]+$val["trim"])+$tot_buyer_match_not_shade_subcon;
													?>
													<tr bgcolor="<? echo $bgcolor_dyeing;?>">
														<td align="center"><? echo $buyer_arr[$buyer];?></td>
														<td align="right"><? echo number_format($val["self"],2);?></td>
														<td align="right"><? echo number_format($val["sm"],2);?></td>
														<td align="right"><? echo number_format($val["smn"],2);?></td>
														<td align="right"><? echo number_format($val["trim"],2);?></td>
														<td align="right"><? echo number_format($tot_buyer_match_not_shade_subcon,2); ?></td>
														<td align="right"><? echo number_format($tot_buyer_match_not_shade,2);?></td>
														<td align="right"><? echo number_format(($tot_buyer_match_not_shade/$shade_not_matched_buyer_total)*100,2);?></td>
													</tr>
													<?
													$sl++;

													$total_self_not_matched += $val["self"];
													$total_sm_not_matched += $val["sm"];
													$total_smn_not_matched += $val["smn"];
													$total_trim_not_matched += $val["trim"];
													$total_subcon_not_matched += $tot_buyer_match_not_shade_subcon;
													$total_tot_buyer_match_shade_not_matched += $tot_buyer_match_not_shade;
													$total_summury_shade_matched_not_matched +=($tot_buyer_match_not_shade/$shade_not_matched_buyer_total)*100;

													$grand_total_self += $val["self"];
													$grand_total_sm += $val["sm"];
													$grand_total_smn += $val["smn"];
													$grand_total_trim += $val["trim"];
													$grand_total_subcon+= $tot_buyer_match_not_shade_subcon;
													$grand_total_buyer += $tot_buyer_match_not_shade;
												}
												?>
											</tbody>
											<tfoot>
												<tr bgcolor="#EBEBEB">
													<th align="center">Total</th>
													<th align="right"><? echo number_format($total_self_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_sm_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_smn_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_trim_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_subcon_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_tot_buyer_match_shade_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_summury_shade_matched_not_matched,2);?></th>
												</tr>
												<tr bgcolor="#EBEBEB">
													<th align="center">Grand Total</th>
													<th align="right"><? echo number_format($grand_total_self,2);?></th>
													<th align="right"><? echo number_format($grand_total_sm,2);?></th>
													<th align="right"><? echo number_format($grand_total_smn,2);?></th>
													<th align="right"><? echo number_format($grand_total_trim,2);?></th>
													<th align="right"><? echo number_format($grand_total_subcon,2);?></th>
													<th align="right"><? echo number_format($grand_total_buyer,2);?></th>
													<th align="right"><? ?></th>
												</tr>
											</tfoot>
											
											
										</table>

										
										<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
											<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
											
										</table>
										

										<table cellpadding="0"  width="300" style="margin:5px;" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
											<thead>
												<tr>
													<th colspan="3"></th>
												</tr>
												<tr>
													<th width="130">Re-Processed</th> 
													<th width="100">Batch Qnty</th>
													<th width="">%</th>
												</tr>
											</thead>
											<tbody>
												<? 
												$sl=1;
												foreach($dyeing_process_wise_batch_qnty_arr as $processId=> $val)
												{
													if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor_dyeing;?>">
														<td align="center"><? echo $conversion_cost_head_array[$processId];?></td>
														<td align="right"><? echo number_format($val,2);?></td>
														<td align="right"><? echo number_format(($val/$dyeing_process_wise_batch_qnty_total)*100,2);?></td>
													</tr>
													<?
													$sl++;
												}
												?>
											</tbody>
											<tfoot>
												<tr>
													<th>Total </th>
													<th><? echo number_format($dyeing_process_wise_batch_qnty_total,2);?></th>
													<th>100.00</th>
												</tr>
											</tfoot>
										</table>
                                        <table cellpadding="0" style="margin:5px;"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
                                        
											<thead>
												<tr>
													<th colspan="3">Shade wise Dyeing Category without Reprocess</th>
												</tr>
												<tr>
													<th width="130">Details</th> 
													<th width="100">Quantity</th>
													<th width="">%</th>
												</tr>
											</thead>
											<tbody>
												<? //total_color_batch_arr;;
												 //shade_total_color_batch_qty=$shade_total_others_color_batch_qty
												 
													  $bgcolor_dyeing="#E9F3FF";  $bgcolor_dyeing2="#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor_dyeing;?>">
														 
														<td>Color Batch </td>
                                                        <td align="right"><? echo number_format($shade_total_color_batch_qty,2);?></td>
														<td align="right" title="Shade Color/Total rft*100"> <? echo number_format(($shade_total_color_batch_qty/$total_rft_add)*100,2);?></td>
													</tr>
                                                    <tr bgcolor="<? echo $bgcolor_dyeing2;?>">
														 
														<td>Others Batch </td>
                                                        <td align="right"><? echo number_format($shade_total_others_color_batch_qty,2);?></td>
														<td align="right" title="Shade Other/Total rft*100"><? echo number_format(($shade_total_others_color_batch_qty/$total_rft_add)*100,2);?></td>
													</tr>
													<?
													 
												 
												?>
											</tbody>
											<tfoot>
												<tr>
													<th>Total </th>
													<th><? echo number_format($shade_total_color_batch_qty+$shade_total_others_color_batch_qty,2);?></th>
													<th>100.00</th>
												</tr>
											</tfoot>
										</table>

										<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
											<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
										</table>

										<table cellpadding="0" style="margin:5px;" width="400" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
											<thead>
												<tr>
													<th colspan="4"></th>
												</tr>
												<tr>
													<th width="130">RFT + Adding </th> 
													<th width="100" title="Multi Batch Yes">No Of Batch </th>
													<th width="100" title="Multi Batch Yes">Shade Matched</th>
													<th width="">%</th>
												</tr>
											</thead>
											<tbody>
												<? 
												$sl=1;
												foreach($btb_shade_matched as $BtbLtb=> $val) 
												{
													if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
													$total_batch=rtrim($val["total_batch"],',');
													$shade_matched=rtrim($val["shade_matched"],',');
													
													$total_batchArr=array_unique(explode(",",$total_batch));
													$total_batch_no=count($total_batchArr);
													$shade_matchedArr=array_unique(explode(",",$shade_matched));
													$shade_matched_no=count($shade_matchedArr);
													
													?>
													<tr bgcolor="<? echo $bgcolor_dyeing;?>">
														<td align="center"><? if($BtbLtb==1) echo "Bulk To Bulk"; else if($BtbLtb==2) echo "Lab To Bulk"?></td>
														<td align="right"><? echo $total_batch_no;?></td>
														<td align="right"><? echo $shade_matched_no;?></td>
														<td align="right"><? echo number_format((($shade_matched_no)/$total_batch_no)*100,2);?></td>
													</tr>
													<?
													$sl++;
													$tot_total_batch += $total_batch_no;
													$tot_shade_matched += $shade_matched_no;
												}
												?>
											</tbody>
											<tfoot>
												<tr>
													<th align="left">Total =</th>
													<th><? echo $tot_total_batch;?></th>
													<th><? echo $tot_shade_matched;?></th>
													<th><? echo number_format(($tot_shade_matched/$tot_total_batch)*100,2);?></th>
												</tr>
											</tfoot>
										</table>
										<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
											<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
										</table>
										<table cellpadding="0" style="margin:5px;"  width="160" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
											
											<tbody bgcolor="#FFFFFF">
											<?
												$total_machine_capacity=0;
												foreach($total_running_machine_arr as $mid=>$val)
												{
													$total_machine_capacity+= $machine_capacity_arr[$mid];
													$mids.=$mid.',';
												}
												//echo $mids;
												$tot_machine=count($total_machine_arr);
												//echo $tot_machine.'XXd';
												$running_machine=count($total_running_machine_arr);
												$stop_machine=$tot_machine-$running_machine;
												$running_machine_percent=(($running_machine/$tot_machine)*100);
												$stop_machine_percent=(($stop_machine/$tot_machine)*100);
													$total_batch_weight=($ttl_batch_qty+$sub_ttl_batch_prod)*100;
													//$total_machine_capacity=($tot_machine_capacity+$sub_tot_machine_capacity)*1;
													$avg_load_percent=($total_batch_weight/($total_machine_capacity*$all_days_total_date));
											?>
												<tr>
													<td width="90" title="Total batch Weight(<? echo number_format($ttl_batch_qty,2).' + '.number_format($sub_ttl_batch_prod,2);?>)*100/(Total Running Machine Capacity(<? echo $total_machine_capacity;?>)*Working Day(<? echo $all_days_total_date;?>))">Avg. Load% :</td>
													<td align="right"><? 
													echo number_format($avg_load_percent,2);
													
													?></td>
													
												</tr>
												<tr>
												<td width="90" title="Tot Machine=<? echo $tot_machine;?>">Avg.Batch/Machine: </td>
												<td title="Total No Of Batch/Running Machine(<? echo $running_machine;?>)" align="right">
												<? $avg_batch_machine=$tot_total_batch/$running_machine; echo number_format($avg_batch_machine,2); ?>
												</td>
													
												</tr>
											</tbody>
										</table>
										<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
											<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
										</table>
										<table cellpadding="0"   style="margin:5px;" width="350" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<tr bgcolor="#E9F3FF">
											<td width="150">Total number of M/C running </td>
											<td width="80" align="right"><? echo $running_machine; ?></td>
											<td align="right" width="80"><? if( $running_machine_percent>0) echo number_format($running_machine_percent,2,'.','')." % "; ?></td>
										</tr>
										<tr bgcolor="#FFFFFF">
											<td width="150">Total number of M/C stop</td>
											<td align="right" width="80"><? echo $stop_machine; ?></td>
											<td align="right" width="80"><? if( $running_machine_percent>0) echo number_format($stop_machine_percent,2,'.','')." % "; ?></td>
										</tr>
										
									</table>
										
										
										<?
										$group_by=str_replace("'",'',$cbo_group_by);
										?>
										<div align="left">

											<table class="rpt_table" width="3320" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
												<caption> <b>Self batch  </b></caption>
												<thead>
													<tr>
														<th width="30">SL</th>
														<th width="80"><? echo $date_type_msg;?></th>
														<? if($group_by==3 || $group_by==0){ ?>
															<th width="80">M/C No</th>
														<? } if($group_by==2 || $group_by==0){ ?>
															<th width="80">Floor</th>  
														<? } if($group_by==1 || $group_by==0){ ?> 
															<th width="80">Shift Name</th>
														<? } ?>
														<th width="100">Buyer</th>
														<th width="100">Style Ref.</th>
														<th width="100">Season</th>
														<th width="120">Fso No</th>
														<th width="90">Fab.Booking No</th>
														<th width="70">Booking Type</th>
														<th width="100">Body Part</th>
														<th width="100">Fabrics Desc</th>
														<th width="70">Dia/ Width<br> Type</th>
														<th width="50">Lot No</th> 
														<th width="100">Yarn Info.</th> 
                                                        <th width="80">Color Type</th>
														<th width="80">Color Name</th>
														<th width="80">Color Range</th>
                                                        <th width="80">Dyes percent</th>
                                                        <th width="80">Shade%</th>
														<th width="90">Batch No</th>
														<th width="40">Extn. No</th>
														<th width="70">Fabric Wgt.</th>
														<th width="70">Trims Wgt.</th>
														<th width="70">Batch Wgt.</th>
														<th width="70">M/C <br>Capacity.</th>
														<th width="70">Loading %.</th>
														<th width="75">Load Date<br> Time</th>
														<th width="75">UnLoad <br>Date Time</th>
														<th width="60">Time Used</th>
														<th width="60">BTB<br>LTB</th>
														<th width="100">Dyeing <br>Fab. Type</th>
														<th width="100">Result</th>
														<th width="80">Dyeing Process</th>
														<th width="80"><p>Dyeing Re <br>Process Name</p></th>

														<th width="60">Hour <br>Load Meter</th>
														<th width="60">Hour <br>unLoad Meter</th>
														<th width="60">Total Time</th>

														<th width="60">Water <br>Loading Flow</th> 
														<th width="60">Water <br>UnLoading Flow</th>
														<th width="70">Water Cons.</th>

														<th width="">Remark</th>
													</tr>
												</thead>
											</table>
											<div style=" max-height:350px; width:3340px; overflow-y:scroll;;" id="scroll_body">
												<table class="rpt_table" id="table_body" width="3320" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
													<tbody>
														<? 
														$i=1; $total_water_cons_load=0;$total_water_cons_unload=$tot_machine_capacity=0;$grand_total_production_qnty=$sub_grand_tot_trims_qnty=0;
														$batch_chk_arr=array(); $group_by_arr=array();$tot_trims_qnty=0;$trims_check_array=array();
														foreach($data_array_batch as $batch_id=>$batch_data_arr)
														{
															$b=1;
															foreach ($batch_data_arr as $prod_id => $row) 
															{

																
																$batch_td_span = $batch_row_span_arr[$batch_id];
																//echo $batch_td_span.',';
															 //echo $row['color_type'].'ff';;
																
																if ($i%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";

																if($group_by!=0)
																{
																	if($group_by==1)
																	{
																		$group_value=$row['floor_id'];
																		$group_name="Floor";
																		$group_dtls_value=$floor_arr[$row['floor_id']];
																	}
																	else if($group_by==2)
																	{
																		$group_value=$row['shift_name'];
																		$group_name="Shift";
																		$group_dtls_value=$shift_name[$row['shift_name']];
																	}
																	else if($group_by==3)
																	{
																		$group_value=$row['machine_id'];
																		$group_name="machine";
																		$group_dtls_value=$machine_arr[$row['machine_id']];
																	}
																	if (!in_array($group_value,$group_by_arr) )
																	{
																		if($k>1)
																		{ 	
																			?>
																			<tr class="tbl_bottom">
																				<td width="30">&nbsp;</td>
																				<td width="80">&nbsp;</td>

																				<? if($group_by==3 || $group_by==0){ ?>
																					<td width="80">&nbsp;</td> 
																				<? } ?>
																				<? if($group_by==2 || $group_by==0){ ?>
																					<td width="80">&nbsp;</td> 
																				<? } if($group_by==1 || $group_by==0){ ?>  
																					<td width="80">&nbsp;</td>
																				<? } ?>
																				<td width="100" align="center"><p></p></td>
																				<td width="100" align="center"><p></p></td>
																				<td width="100" align="center"><p></p></td>
																				<td width="120" align="center"><p></p></td>
																				<td width="90"></td>
																				<td width="70" align="center"></td>
																				<td width="100"></td>
																				<td width="100"></td>
																				<td width="70"><p></p></td>
																				<td width="50"><p></p></td>
																				<td width="100"><p></p></td>
                                                                                <td width="80"><p></p></td>
																				<td width="80"><p></p></td>
																				<td width="80"><p></p></td>
                                                                                <td width="80"><p></p></td>
                                                                                <td width="80"><p></p></td>
																				<td width="90"><p></p></td>
																				<td width="40"><p></p></td>
																				<td align="right" width="70"><? echo  number_format($sub_production_qnty,2);?></td>
																				<td align="right" width="70"></td>
																				<td align="right" width="70"><? echo number_format($batch_qnty_tot,2);?></td>
																				
																				<td align="right" width="70"></td>
																				<td align="right" width="70"></td>
																				
																				<td width="75"><p></p></td>
																				<td width="75"><p></p></td>
																				<td align="center" width="60"> </td>
																				<td align="center" width="60"></td>
																				<td align="center" width="100"></td>
																				<td align="center" width="100"></td>
																				<td align="center" width="80"></td>
																				<td align="center" width="80"> </td>
																				<td width="60" align="right"><p></p></td>
																				<td width="60" align="right"><p></p></td>
																				<td width="60" align="right" ><p></p></td>
																				<td width="60" align="right"><p></p></td>
																				<td width="60" align="right"><p></p></td>
																				<td align="right" width="70" ></td>
																				<td align="center"></td>
																			</tr>                                
																			<?
																			$batch_qnty_tot=0; $trims_btq=0;$sub_production_qnty=0;
																		}
																		?>
																		<tr bgcolor="#EFEFEF">
																			<td colspan="41" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
																		</tr>
																		<?
																		$group_by_arr[]=$group_value;            
																		$k++;
																	}					
																}

																$batch_qnty = $row["prod_qnty_tot"]+$row["total_trims_weight"];
																$batch_weight=$row['batch_weight'];
																$water_cons_unload=$row['water_flow_meter'];
																$water_cons_load=$water_flow_arr[$batch_id];
																$load_hour_meter=$load_hour_meter_arr[$batch_id];
																$water_cons_diff=($water_cons_unload-$water_cons_load)/$batch_weight*1000;

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
																$BatchTotal 				= $batchTotal_arr[$batch_id]['batch_ratio_total'];
																//$chemicalsTotal 			= $batch_item_ratio_total_arr[$batch_id][5]['ratio_total'];
																$dyesTotal 					= $batch_item_ratio_total_arr[$batch_id][6]['ratio_total'];
																$dyesReqTotal 					= $batch_item_ratio_dyes_arr[$batch_id]['ratio_total'];
																//$auxiChemicalsTotal			= $batch_item_ratio_total_arr[$batch_id][7]['ratio_total'];
																
																$shade_percentageOfColor = 0;
																if($dyesTotal>0 && $BatchTotal>0 )
																{
																//	echo $row[csf('batch_id')].'=='.$dyesTotal.'TTTTTTTT'.$BatchTotal;
																 $shade_percentageOfColor=($dyesTotal/$BatchTotal)*100;
																}
					
																?>
																<tr bgcolor="<? echo $bgcolor_dyeing; ?>"  id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor_dyeing; ?>')">
																	<td width="30"><? echo $i; ?></td>
																	<td width="80"><p><? echo change_date_format($date_type_cond); $unload_date=$row['process_end_date']; ?></p></td>
																	<? if($group_by==3 || $group_by==0){ ?>
																		<td align="center" width="80"><p style="word-break:break-all;width:80px"><? echo $machine_arr[$row['machine_id']]; ?></p></td>
																	<? } if($group_by==2 || $group_by==0){ ?>
																		<td width="80"><p style="word-break:break-all"><? echo $floor_arr[$row['floor_id']]; ?></p></td>
																	<? } if($group_by==1 || $group_by==0){ ?>
																		<td width="80" align="center"><p style="word-break:break-all"><? echo $shift_name[$row['shift_name']]; ?></p></td>
																	<? } ?>

																	<td width="100" align="center">
																		<p style="word-break:break-all">
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
																		</p>
																	</td>
																	<td width="100" align="center"><p style="width:100px; word-wrap:break-word;"><? echo $row['style_ref_no']; ?></p></td>
																	<td width="100" align="center"><p style="word-break:break-all"><? echo $row['season']; ?></p></td>
																	<td width="120" align="center"><p style="width:118px; word-wrap:break-word;"><? echo $row['sales_no']; ?></p></td>
																	<td width="90"><p style="width:70px; word-wrap:break-word;"><? echo $row['booking_no']; ?></p></td>
																	<td width="70" align="center"><p style="width:70px; word-wrap:break-word;"><? echo $booking_type_arr[$row['booking_no']]; ?></p></td>
																	<td width="100" style="width:100px;"><p style="width:100px; word-wrap:break-word;"><?  echo $row['body_part']; ?></p></td>
																	<td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $row['item_description']; ?></div></td>
																	<td width="70" style="width:70px; word-wrap:break-word;"><p style="width:70px; word-wrap:break-word;"> <? echo $fabric_typee[$row['width_dia_type']]; ?></p></td>								<td width="50" style="width:50px; " title="<? echo implode(", ",array_filter(array_unique($row["barcode_no"])));?>">
																		<p style="width:50px; word-wrap:break-word;">
																			<? $bar_lot_arr= array();$yarnlots="";$yarn_types="";$yarn_count_name="";$yarn_comp_type1st="";
																			$bar_lot_arr =array_filter(array_unique($row["barcode_no"]));
																			if(count($bar_lot_arr)>0){
																				foreach ($bar_lot_arr as $bcode) 
																				{
																					$yarnlots .= chop($yarn_lot_arr[$bcode],",").",";
																					
																					$yarn_types .= chop($yarn_data_arr[$bcode]['yarn_type'],",").",";
																					$yarn_comp_type1st .= chop($yarn_data_arr[$bcode]['yarn_comp_type1st'],",").",";
																					$yarn_count_name .= chop($yarn_data_arr[$bcode]['yarn_count_id'],",").",";
																					$colors .= chop($yarn_data_arr[$bcode]['color'],",").",";																				}
																				echo implode(", ",array_filter(array_unique(explode(",", $yarnlots))));  
																			}
																			$yarn_types=implode(", ",array_filter(array_unique(explode(",", $yarn_types))));
																			$yarn_count_name=implode(", ",array_filter(array_unique(explode(",", $yarn_count_name))));
																			$yarn_comp_type1st=implode(", ",array_filter(array_unique(explode(",", $yarn_comp_type1st))));
																			$colors=implode(", ",array_filter(array_unique(explode(",", $colors))));
																			?>
																		</p>
																	</td>

																	<td width="100" style="width:100px; "><p style="width:100px; word-wrap:break-word;"><? echo $yarn_count_name.', '.$yarn_comp_type1st.', '.$yarn_types; ?></p></td>
																<td width="80" style="width:80px; word-wrap:break-word;"><p style="word-break:break-all"><? echo $color_type[$row['color_type']];; ?></p></td>
                                                                 <td width="80" style="width:80px; word-wrap:break-word;"><p style="width:80px; word-wrap:break-word;"><? echo $color_library[$row['color_id']]; ?></p></td>
															<td width="80" style="width:80px; word-wrap:break-word;"><p style="width:80px; word-wrap:break-word;"><? echo $color_range[$row['color_range_id']]; ?></p></td>
                                                           
                                                           
                                                            
																	<? if($b==1){ ?>
                                                                     <td width="80" rowspan="<? echo $batch_td_span?>" style="width:80px; word-wrap:break-word;"><p><? echo number_format($dyesReqTotal,2); ?></p></td>
																		 <td width="80" rowspan="<? echo $batch_td_span?>" title="(dyesTotalRatio(<? echo $dyesTotal;?>)/BatchTotalRatio)*100;" style="width:80px; word-wrap:break-word;"><p><? echo number_format($shade_percentageOfColor,2); ?></p></td>
                                                                        <td width="90" rowspan="<? echo $batch_td_span?>"><p style="width:90px; word-wrap:break-word;"><? echo $row['batch_no']; ?></p></td>
																		<td width="40" rowspan="<? echo $batch_td_span?>"><p><? echo $row['extention_no']; ?></p></td>
																		<? } ?>
																		<td align="right" width="70"><? echo number_format($row['production_qnty'],2); $grand_total_production_qnty += $row['production_qnty']; $sub_production_qnty += $row['production_qnty'];?></td>

																		<? if($b==1){?>
																			<td align="right" width="70" rowspan="<? echo $batch_td_span?>"><? echo number_format($row["total_trims_weight"],2);  ?></td>
																			<td align="right" width="70" rowspan="<? echo $batch_td_span?>"><? echo number_format($batch_qnty,2);  ?></td>
																			<?
																			$batch_qnty_tot+=$batch_qnty;
																			$grand_total_batch_qty+=$batch_qnty;
																			$trims_btq+=$row["total_trims_weight"];
																			$sub_grand_tot_trims_qnty+=$row["total_trims_weight"];
																			$tot_machine_capacity+=$machine_capacity_arr[$row['machine_id']];

																			?>
																			<td align="right" width="70" rowspan="<? echo $batch_td_span?>"><? echo number_format($machine_capacity_arr[$row['machine_id']],2);  ?>
																			</td>
																			<td align="right" width="70" title="Batch Weight/Machine Capacity*100" rowspan="<? echo $batch_td_span?>"><? echo number_format(($batch_qnty/$machine_capacity_arr[$row['machine_id']]*100),2);  ?>
																			</td>
																			<td width="75" rowspan="<? echo $batch_td_span?>" title="<? echo $load_date[$batch_id];?>"><p  style="width:75px; word-wrap:break-word;"><? $load_t=$load_hr[$batch_id].':'.$load_min[$batch_id]; 
																			echo  ($load_date[$batch_id] == '0000-00-00' || $load_date[$batch_id] == '' ? '' : change_date_format($load_date[$batch_id])).' <br> '.$load_t;

																			?></p></td>
																			<td width="75" rowspan="<? echo $batch_td_span?>"><p  style="width:75px; word-wrap:break-word;"><? $hr=strtotime($unload_date,$load_t); $min=($row['end_minutes'])-($load_min[$batch_id]);
																			echo  ($row['process_end_date'] == '0000-00-00' || $row['process_end_date'] == '' ? '' : change_date_format($row['process_end_date'])).'<br>'.$unload_time=$row['end_hours'].':'.$row['end_minutes']; ?></p>
																		</td>
																		<td align="center" width="60" rowspan="<? echo $batch_td_span?>">
																			<?     
																			$new_date_time_unload=($unload_date.' '.$unload_time.':'.'00');
																			$new_date_time_load=($load_date[$batch_id].' '.$load_t.':'.'00');
																			$total_time=datediff(n,$new_date_time_load,$new_date_time_unload);
																			echo floor($total_time/60).":".$total_time%60;
																			?>
																		</td>
																		<td align="center" width="60" rowspan="<? echo $batch_td_span?>"><? echo $ltb_btb[$row["ltb_btb_id"]];?></td>

																		<td align="center" width="100" rowspan="<? echo $batch_td_span?>"><p width:100px; word-wrap:break-word;><? 
																		echo $fabric_type_for_dyeing[$row['fabric_type']];?></p> </td>
																		<td align="center" width="100" style="width:100px; word-wrap:break-word;" rowspan="<? echo $batch_td_span?>"><p><? echo $dyeing_result[$row['result']]; ?></p> </td>
																		<td align="center" width="80" style="width:80px; word-wrap:break-word;" rowspan="<? echo $batch_td_span?>"><? echo $conversion_cost_head_array[$row["process_id"]]?></td>
																		<td align="center" width="80" rowspan="<? echo $batch_td_span?>" title="<? echo $row["add_tp_strip"];?>">
																			<? 
																			if($row["process_id"]==148 && $row["add_tp_strip"] ==1)
																			{
																				echo "Re-Wash";
																			}
																			else{
																				echo $dyeing_re_process[$row["add_tp_strip"]];
																			} 
																			?>
																		</td>
																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo number_format($load_hour_meter,2);  ?></p></td>
																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo number_format($row['hour_unload_meter'],2);  ?></p></td>
																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo number_format($row['hour_unload_meter']-$load_hour_meter,2);  ?></p></td>

																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo $water_cons_load;  ?></p></td>
																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo $water_cons_unload;  ?></p></td>
																		<td align="right" width="70" rowspan="<? echo $batch_td_span?>" title="<? echo "Water Cons Unload-Water Cons Load*1000/Batch Qnty"?>" ><? echo number_format((($water_cons_unload-$water_cons_load)*1000)/$row['batch_qnty'],2);  ?></td>

																		<td align="center" rowspan="<? echo $batch_td_span?>"><p><? echo $row['remarks']; ?></p> </td>
																		<? } ?>
																	</tr>
																	<? 
																	$i++;$b++;


																	$total_water_cons_load+=$water_cons_load;
																	$total_water_cons_unload+=$water_cons_unload;


																}
															}
															if($group_by!=0)
															{
																?>
																<tr class="tbl_bottom">
																	<td width="30">&nbsp;</td>
																	<td width="80">&nbsp;</td>

																	<? if($group_by==3 || $group_by==0){ ?>
																		<td width="80">&nbsp;</td> 
																	<? } ?>
																	<? if($group_by==2 || $group_by==0){ ?>
																		<td width="80">&nbsp;</td> 
																	<? } if($group_by==1 || $group_by==0){ ?>  
																		<td width="80">&nbsp;</td>
																	<? } ?>
																	<td width="100" align="center"><p></p></td>
																	<td width="100" align="center"><p></p></td>
																	<td width="100" align="center"><p></p></td>
																	<td width="120" align="center"><p></p></td>
																	<td width="90"></td>
																	<td width="70" align="center"></td>
																	<td width="100"></td>
																	<td width="100"></td>
																	<td width="70"><p></p></td>
																	<td width="50"><p></p></td>
																	<td width="100"><p></p></td>
																	<td width="80"><p></p></td>
																	<td width="80"><p></p></td>
																	<td width="80"><p></p></td>
                                                                    <td width="80"><p></p></td>
                                                                    <td width="80"><p></p></td>
																	<td width="90"><p></p></td>
																	<td width="40"><p></p></td>
																	<td align="right" width="70"><? echo  number_format($sub_production_qnty,2);?></td>
																	<td align="right" width="70"></td>
																	<td align="right" width="70"><? echo number_format($batch_qnty_tot,2);?></td>
																	<td width="70"><p></p></td>
																	<td width="70"><p></p></td>
																	<td width="75"><p></p></td>
																	<td width="75"><p></p></td>
																	<td align="center" width="60"> </td>
																	<td align="center" width="60"></td>
																	<td align="center" width="100"></td>
																	<td align="center" width="100"></td>
																	<td align="center" width="80"></td>
																	<td align="center" width="80"> </td>
																	<td width="60" align="right"><p></p></td>
																	<td width="60" align="right"><p></p></td>
																	<td width="60" align="right" ><p></p></td>
																	<td width="60" align="right"><p></p></td>
																	<td width="60" align="right"><p></p></td>
																	<td align="right" width="70" ></td>
																	<td align="center"></td>
																</tr> 
																<? 
															}

															?>            
														</tbody>
														<tfoot>
															<tr class="tbl_bottom">
																<td width="30">&nbsp;</td>
																<td width="80">&nbsp;</td>

																<? if($group_by==3 || $group_by==0){ ?>
																	<td width="80">&nbsp;</td> 
																<? } ?>
																<? if($group_by==2 || $group_by==0){ ?>
																	<td width="80">&nbsp;</td> 
																<? } if($group_by==1 || $group_by==0){ ?>  
																	<td width="80">&nbsp;</td>
																<? } ?>
																<td width="100" align="center"><p></p></td>
																<td width="100" align="center"><p></p></td>
																<td width="100" align="center"><p></p></td>
																<td width="120" align="center"><p></p></td>
																<td width="90"></td>
																<td width="70" align="center"></td>
																<td width="100"></td>
																<td width="100"></td>
																<td width="70"><p></p></td>
																<td width="50"><p></p></td>
																<td width="100"><p></p></td>
																<td width="80"><p></p></td>
																<td width="80"><p></p></td>
																<td width="80"><p></p></td>
                                                                <td width="80"><p></p></td>
                                                                <td width="80"><p></p></td>
																<td width="90"><p></p></td>
																<td width="40"><p></p></td>
																<td align="right" width="70"><? echo number_format($grand_total_production_qnty,2);?></td>
																<td align="right" width="70"><? echo number_format($sub_grand_tot_trims_qnty,2);?></td>
																<td align="right" width="70"><? echo number_format($grand_total_batch_qty,2);?></td>
																<td width="70"><p><? echo number_format($tot_machine_capacity,2);?></p></td>
																<td width="70"><p></p></td>
																<td width="75"><p></p></td>
																<td width="75"><p></p></td>
																<td align="center" width="60"> </td>
																<td align="center" width="60"></td>
																<td align="center" width="100"></td>
																<td align="center" width="100"></td>
																<td align="center" width="80"></td>
																<td align="center" width="80"> </td>
																<td width="60" align="right"><p></p></td>
																<td width="60" align="right"><p></p></td>
																<td width="60" align="right" ><p></p></td>
																<td width="60" align="right"><p></p></td>
																<td width="60" align="right"><p></p></td>
																<td align="right" width="70" ></td>
																<td align="center"></td>
															</tr> 
														</tfoot>
													</table>
												</div>
											</div>
											<br/>
											<? 
										}
									}
						if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
						{
							if (count($subbatchdata)>0)
							{
								$group_by=str_replace("'",'',$cbo_group_by);
								if(str_replace("'",'',$cbo_batch_type)==2)
								{
								?>
								<table cellpadding="0"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
									<thead>
										<tr>
											<th colspan="3">Production Summary</th>
										</tr>
										<tr>
											<th width="130">Details </th> 
											<th width="120">Prod. Qty. </th>
											<th>%</th>
										</tr>
									</thead>
									<tbody>
										<tr bgcolor="#E9F3FF">
											<td>Total RFT</td>
											<td align="right"><? echo number_format($total_rft_qnty,2);?></td>
											<td align="right"><? echo number_format(($total_rft_qnty*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty),2);?></td>
										</tr>
										<tr bgcolor="#FFFFFF">
											<td>Total Adding</td> 
											<td align="right"><? echo number_format($total_adding_qnty,2);?></td>
											<td align="right"><? echo number_format(($total_adding_qnty*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty),2); ?></td>
										</tr>
                                        <tr bgcolor="#FFFF00">
											<td>Total:</td> 
											<td align="right"><? 
											$total_rft_add=0; $total_rft_add_per=0;
											$total_rft_add=$total_rft_qnty+$total_adding_qnty;
											$total_rft_add_per=(($total_rft_qnty*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty))+(($total_adding_qnty*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty));
											echo number_format($total_rft_add,2);?></td>
											<td align="right"><? echo number_format($total_rft_add_per,2);?></td>
										</tr>
										<tr bgcolor="#E9F3FF">
											<td>Total Re-Process</td> 
											<td align="right"><? echo number_format($total_reprocess_qty,2);?></td> 
											<td align="right"><? echo number_format(($total_reprocess_qty*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty),2);?></td> 
										</tr>
										<tr bgcolor="#EBEBEB">
											<td align="right"><b>Grand Total:</b></td> 
											<td align="right">
												<b><? echo number_format($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty,2); ?></b>
											</td> 
											<td align="right"><b><? echo number_format((($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty)*100)/($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty),2);?></b></td>  
											</tr>
											<tr bgcolor="#FFFFFF">
												<td><b>Avg. Prod. Per Day</b></td> 
												<td align="right" title="<? echo "days=".implode(",",array_filter(array_unique($production_date_count)));?>">
													<b><? 
													echo number_format(($total_rft_qnty + $total_adding_qnty + $total_reprocess_qty)/count(array_filter(array_unique($production_date_count))),2);
													?></b>	
												</td> 
												<td></td> 
											</tr>
											<? 
											$ftsl=2;
											foreach($fabric_type_production_arr as $fabType=>$val)
											{ 
												if ($ftsl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													<td><? echo $fabric_type_for_dyeing[$fabType];?></td> 
													<td align="right"><? echo number_format($val,2);?></td> 
													<td align="right"><? echo number_format(($val/$fabric_type_production_total)*100,2);?></td> 
												</tr>
												<?
												$ftsl++;
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th>Total</th> 
												<th align="right"><? echo number_format($fabric_type_production_total,2);?></th> 
												<th align="right"><? echo number_format(100,2);?></th> 
											</tr>
										</tfoot>
									</table>

									<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
										<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
									</table>

									<table cellpadding="0"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<thead>
											<tr>
												<th colspan="4">Re-Process Summary (Buyer Wise)</th>
											</tr>
											<tr>
												<th width="40">Sl </th> 
												<th width="100">Buyer</th>
												<th width="100">Batch Total</th>
												<th>%</th>
											</tr>
										</thead>
										<tbody>
											<? 
											$sl=1;
											foreach($buyer_wise_summary as $buyer=> $bs)
											{
												if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													<td align="center"><? echo $sl;?></td>
													<td align="center"><? echo $buyer_arr[$buyer];?></td>
													<td align="right"><? echo number_format($bs,2);?></td>
													<td align="right"><? echo number_format(($bs/$buyer_wise_summary_batch_total)*100,2);?></td>
												</tr>
												<?
												$sl++;
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="2">Total </th>
												<th><? echo number_format($buyer_wise_summary_batch_total,2);?></th>
												<th><? if ($buyer_wise_summary_batch_total>0) echo "100.00"; else echo "0.00";?></th>
											</tr>
										</tfoot>
									</table>
									<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
										<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
									</table>
									<table cellpadding="0"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<thead>
											<tr>
												<th colspan="5">Color Range wise Summary</th>
											</tr>
											<tr>
												<th width="40">Sl </th> 
												<th width="100">Color Range</th>
												<th width="100">Quantity (Kgs)</th>
												<th  width="50">Percentage%</th>
												<th>No. Of Batches</th>
											</tr>
										</thead>
										<tbody>
											<? 
											$sl=1;$tot_batch_qty=$tot_no_of_batch=$tot_total_trims_weight=0;
											foreach($color_wise_batch_arr as $color_rang_id=> $val)
											{
												$total_color_qty+=$val['batch_qnty'];
											}
											//print_r($color_wise_batch_arr);
											foreach($color_wise_batch_arr as $color_rang_id=> $val)
											{
												if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												$batch_ids=rtrim($val[batch_ids],',');
												$batch_ids_arr=array_unique(explode(",",$batch_ids));
												$tot_total_trims_weight+=$val['total_trims_weight'];
												//echo count($batch_ids_arr).',';
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													<td align="center"><? echo $sl;?></td>
													<td align="center"><? echo $color_range[$color_rang_id];?></td>
													<td align="right"><? echo number_format($val['batch_qnty'],2);?></td>
													<td align="right" title="Tot batch Qty=<? echo $total_color_qty;?>">
													<? echo number_format(($val['batch_qnty']/($total_color_qty))*100,2);?></td>
													<td align="right"><? echo count($batch_ids_arr);?></td>
												</tr>
												<?
												$sl++;
												$tot_batch_qty+=$val['batch_qnty'];
												$tot_no_of_batch+=count($batch_ids_arr);
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="2">Total </th>
												<th><? echo number_format($tot_batch_qty,2);?></th>
												<th><? echo number_format(($tot_batch_qty/($total_color_qty))*100,2);?></th>
												<th><? echo number_format($tot_no_of_batch,0);?></th>
											</tr>
										</tfoot>
									</table>
									
									

									<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
										<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
									</table>

									<table cellpadding="0"  width="640" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<thead>
											<tr>
												<th colspan="8">Summary Total (Shade Match)</th>
											</tr>
											<tr>
												<th width="100">Buyer</th>
												<th width="100">Self batch</th> 
												<th width="100">Smpl. Batch With Order</th>
												<th width="60">Smpl. Batch Without Order</th>
												<th width="60">Trims Weight</th>
												<th width="60">Inbound subcontract</th>
												<th width="100">Buyer Total</th>
												<th>%</th>
											</tr>
										</thead>
										<tbody>
											<? 
											$sl=1;
											foreach($shade_matched_wise_summary as $buyer=> $val)
											{
												if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
												$tot_buyer_match_shade_subcon = $val["subcon"];
												$tot_buyer_match_shade = ($val["self"]+$val["sm"]+$val["smn"]+$val["trim"])+$tot_buyer_match_shade_subcon;
												?>
												<tr bgcolor="<? echo $bgcolor_dyeing;?>">
													<td align="center"><? echo $buyer_arr[$buyer];?></td>
													<td align="right"><? echo number_format($val["self"],2); ?></td>
													<td align="right"><? echo number_format($val["sm"],2); ?></td>
													<td align="right"><? echo number_format($val["smn"],2); ?></td>
													<td align="right"><? echo number_format($val["trim"],2); ?></td>
													<td align="right"><? echo number_format($tot_buyer_match_shade_subcon,2); ?></td>
													<td align="right"><? echo number_format($tot_buyer_match_shade,2);?></td>
													<td align="right"><? echo number_format(($tot_buyer_match_shade/$shade_matched_buyer_total)*100,2);?></td>
												</tr>
												<?
												$sl++;
												$total_self += $val["self"];
												$total_sm += $val["sm"];
												$total_smn += $val["smn"];
												$total_trim += $val["trim"];
												$total_subcon += $tot_buyer_match_shade_subcon;
												$total_tot_buyer_match_shade += $tot_buyer_match_shade;
												$total_summury_shade_matched +=($tot_buyer_match_shade/$shade_matched_buyer_total)*100;

												$grand_total_self += $val["self"];
												$grand_total_sm += $val["sm"];
												$grand_total_smn += $val["smn"];
												$grand_total_trim += $val["trim"];
												$grand_total_subcon += $tot_buyer_match_shade_subcon;
												$grand_total_buyer += $tot_buyer_match_shade;

											}
											unset($shade_matched_wise_summary);
											?>
											<tr style="background-color: #eee;font-weight: bold;">
												<td width="100" align="right">Total</th>
													<td width="100" align="right"><? echo number_format($total_self,2);?></td> 
													<td width="100" align="right"><? echo number_format($total_sm,2);?></td>
													<td width="60" align="right"><? echo number_format($total_smn,2);?></td>
													<td width="60" align="right"><? echo number_format($total_trim,2);?></td>
													<td width="60"><? echo number_format($grand_total_subcon,2);?></td>
													<td width="100" align="right"><? echo number_format($total_tot_buyer_match_shade,2);?></td>
													<td align="right"><? echo number_format($total_summury_shade_matched,2);?></td>
												</tr>
											</tbody>
											<thead>
												<tr>
													<th colspan="8">Summary Total (Shade Not Match)</th>
												</tr>
											</thead>
											<tbody>
												<?
												foreach($shade_not_matched_wise_summary as $buyer=> $val)
												{
													if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
													$tot_buyer_match_not_shade_subcon = $val["subcon"];
													$tot_buyer_match_not_shade = ($val["self"]+$val["sm"]+$val["smn"]+$val["trim"])+$tot_buyer_match_not_shade_subcon;
													?>
													<tr bgcolor="<? echo $bgcolor_dyeing;?>">
														<td align="center"><? echo $buyer_arr[$buyer];?></td>
														<td align="right"><? echo number_format($val["self"],2);?></td>
														<td align="right"><? echo number_format($val["sm"],2);?></td>
														<td align="right"><? echo number_format($val["smn"],2);?></td>
														<td align="right"><? echo number_format($val["trim"],2);?></td>
														<td align="right"><? echo number_format($tot_buyer_match_not_shade_subcon,2); ?></td>
														<td align="right"><? echo number_format($tot_buyer_match_not_shade,2);?></td>
														<td align="right"><? echo number_format(($tot_buyer_match_not_shade/$shade_not_matched_buyer_total)*100,2);?></td>
													</tr>
													<?
													$sl++;

													$total_self_not_matched += $val["self"];
													$total_sm_not_matched += $val["sm"];
													$total_smn_not_matched += $val["smn"];
													$total_trim_not_matched += $val["trim"];
													$total_subcon_not_matched += $tot_buyer_match_not_shade_subcon;
													$total_tot_buyer_match_shade_not_matched += $tot_buyer_match_not_shade;
													$total_summury_shade_matched_not_matched +=($tot_buyer_match_not_shade/$shade_not_matched_buyer_total)*100;

													$grand_total_self += $val["self"];
													$grand_total_sm += $val["sm"];
													$grand_total_smn += $val["smn"];
													$grand_total_trim += $val["trim"];
													$grand_total_subcon+= $tot_buyer_match_not_shade_subcon;
													$grand_total_buyer += $tot_buyer_match_not_shade;
												}
												?>
											</tbody>
											<tfoot>
												<tr bgcolor="#EBEBEB">
													<th align="center">Total</th>
													<th align="right"><? echo number_format($total_self_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_sm_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_smn_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_trim_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_subcon_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_tot_buyer_match_shade_not_matched,2);?></th>
													<th align="right"><? echo number_format($total_summury_shade_matched_not_matched,2);?></th>
												</tr>
												<tr bgcolor="#EBEBEB">
													<th align="center">Grand Total</th>
													<th align="right"><? echo number_format($grand_total_self,2);?></th>
													<th align="right"><? echo number_format($grand_total_sm,2);?></th>
													<th align="right"><? echo number_format($grand_total_smn,2);?></th>
													<th align="right"><? echo number_format($grand_total_trim,2);?></th>
													<th align="right"><? echo number_format($grand_total_subcon,2);?></th>
													<th align="right"><? echo number_format($grand_total_buyer,2);?></th>
													<th align="right"><? ?></th>
												</tr>
											</tfoot>
										</table>

										<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
											<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
										</table>

										<table cellpadding="0"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
											<thead>
												<tr>
													<th colspan="3"></th>
												</tr>
												<tr>
													<th width="130">Re-Processed</th> 
													<th width="100">Batch Qnty</th>
													<th width="">%</th>
												</tr>
											</thead>
											<tbody>
												<? 
												$sl=1;
												foreach($dyeing_process_wise_batch_qnty_arr as $processId=> $val)
												{
													if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor_dyeing;?>">
														<td align="center"><? echo $conversion_cost_head_array[$processId];?></td>
														<td align="right"><? echo number_format($val,2);?></td>
														<td align="right"><? echo number_format(($val/$dyeing_process_wise_batch_qnty_total)*100,2);?></td>
													</tr>
													<?
													$sl++;
												}
												?>
											</tbody>
											<tfoot>
												<tr>
													<th>Total </th>
													<th><? echo number_format($dyeing_process_wise_batch_qnty_total,2);?></th>
													<th>100.00</th>
												</tr>
											</tfoot>
										</table>

										<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
											<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
										</table>

										<table cellpadding="0"  width="400" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
											<thead>
												<tr>
													<th colspan="4"></th>
												</tr>
												<tr>
													<th width="130">RFT + Adding </th> 
													<th width="100">No Of Batch </th>
													<th width="100">Shade Matched</th>
													<th width="">%</th>
												</tr>
											</thead>
											<tbody>
												<? 
												$sl=1;
												foreach($btb_shade_matched as $BtbLtb=> $val)
												{
													if ($sl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor_dyeing;?>">
														<td align="center"><? if($BtbLtb==1) echo "Bulk To Bulk"; else if($BtbLtb==2) echo "Lab To Bulk"?></td>
														<td align="right"><? echo $val["total_batch"];?></td>
														<td align="right"><? echo $val["shade_matched"];?></td>
														<td align="right"><? echo number_format((($val["shade_matched"])/$val["total_batch"])*100,2);?></td>
													</tr>
													<?
													$sl++;
													$tot_total_batch += $val["total_batch"];
													$tot_shade_matched += $val["shade_matched"];
												}
												?>
											</tbody>
											<tfoot>
												<tr>
													<th align="left">Total =</th>
													<th><? echo $tot_total_batch;?></th>
													<th><? echo $tot_shade_matched;?></th>
													<th><? echo number_format(($tot_shade_matched/$tot_total_batch)*100,2);?></th>
												</tr>
											</tfoot>
										</table>
										<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
											<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
										</table>
										<table cellpadding="0" style=""  width="160" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
											
											<tbody bgcolor="#FFFFFF">
											<?
												$tot_machine=count($total_machine_arr);
												//echo $tot_machine.'XXd';
												$running_machine=count($total_running_machine_arr);
												$stop_machine=$tot_machine-$running_machine;
												$running_machine_percent=(($running_machine/$tot_machine)*100);
												$stop_machine_percent=(($stop_machine/$tot_machine)*100);
													$total_batch_weight=($total_color_wise_batch_qty+$sub_total_color_wise_batch_qty)*100;
													$total_machine_capacity=($tot_machine_capacity+$sub_tot_machine_capacity)*1;
													$avg_load_percent=($total_batch_weight/($total_machine_capacity*$all_days_total_date));
											?>
												<tr>
													<td width="90" title="Total batch Weight(<? echo $total_color_wise_batch_qty.'+ '.$sub_total_color_wise_batch_qty;?>)*100/(Total Running Machine Capacity(<? echo $total_machine_capacity;?>)*Working Day(<? echo $all_days_total_date;?>))">Avg. Load% :</td>
													<td align="right"><? 
													echo number_format($avg_load_percent,2);
													
													?></td>
													
												</tr>
												<tr>
												<td width="90" title="Tot Machine=<? echo $tot_machine;?>">Avg.Batch/Machine: </td>
												<td title="Total No Of Batch/Running Machine(<? echo $running_machine;?>)" align="right">
												<? $avg_batch_machine=$tot_total_batch/$running_machine; echo number_format($avg_batch_machine,2); ?>
												</td>
													
												</tr>
											</tbody>
										</table>
										<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
											<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
										</table>
										<table cellpadding="0"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
										<tr bgcolor="#E9F3FF">
											<td>Total number of M/C running </td>
											<td width="70" align="right"><? echo $running_machine; ?></td>
											<td align="right" width="70"><? if( $running_machine_percent>0) echo number_format($running_machine_percent,2,'.','')." % "; ?></td>
										</tr>
										<tr bgcolor="#FFFFFF">
											<td>Total number of M/C stop</td>
											<td width="70" align="right"><? echo $stop_machine; ?></td>
											<td width="70" align="right"><? if( $running_machine_percent>0) echo number_format($stop_machine_percent,2,'.','')." % "; ?></td>
										</tr>
										
									</table>
									
                                        
                                        <? } ?>
										<div align="left">

											<table class="rpt_table" width="2840" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
												<caption> <b>Inbound subcontract</b></caption>
												<thead>
													<tr>
														<th width="30">SL</th>
														<th width="80"><? echo $date_type_msg;?></th>
														<? if($group_by==3 || $group_by==0){ ?>
															<th width="80">M/C No</th>
														<? } if($group_by==2 || $group_by==0){ ?>
															<th width="80">Floor</th>  
														<? } if($group_by==1 || $group_by==0){ ?> 
															<th width="80">Shift Name</th>
														<? } ?>
														<th width="100">Buyer</th>
														<th width="100">Style Ref.</th>
														<th width="100">Season</th>
														<th width="80">Fso No</th>
														<th width="90">Fabric Booking No</th>
														<th width="70">Booking Type</th>
														<th width="100">Fabrics Desc</th>
														<th width="70">Dia/Width Type</th>
														<th width="50">Lot No</th> 
														<th width="80">Color Name</th>
														<th width="80">Color Range</th>
														<th width="90">Batch No</th>
														<th width="40">Extn. No</th>
														<th width="70">Fabric Wgt.</th>
														<th width="70">Trims Wgt.</th>
														<th width="70">Batch Wgt.</th>
														<th width="70">M/C Capacity</th>
														<th width="70">Loading %</th>
														<th width="75">Load Date & Time</th>
														<th width="75">UnLoad Date Time</th>
														<th width="60">Time Used</th>
														<th width="60">BTB/LTB</th>
														<th width="100">Dyeing Fab. Type</th>
														<th width="100">Result</th>
														<th width="80">Dyeing Process</th>
														<th width="80"><p>Dyeing Re Process Name</p></th>

														<th width="60">Hour Load Meter</th>
														<th width="60">Hour unLoad Meter</th>
														<th width="60">Total Time</th>

														<th width="60">Water Loading Flow</th> 
														<th width="60">Water UnLoading Flow</th>
														<th width="70">Water Cons.</th>

														<th width="">Remark</th>
													</tr>
												</thead>
											</table>
											<div style=" max-height:350px; width:2860px; overflow-y:scroll;;" id="scroll_body">
												<table class="rpt_table" id="table_body" width="2840" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
													<tbody>
														<? 
														$total_water_cons_load=0;$total_water_cons_unload=$grand_sub_tot_machine_capacity=0;$grand_total_production_qnty=$grand_subcon_total_batch_qty=0;
														$batch_chk_arr=array(); $group_by_arr=array(); $tot_trims_qnty=0; $trims_check_array=array();
														foreach($subbatch_data_arr as $batch_id=>$batch_data_arr)
														{
															$b=1;
															foreach ($batch_data_arr as $prod_id => $row) 
															{

																$batch_td_span = $sub_batch_row_span_arr[$batch_id];
																if ($i%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";

																if($group_by!=0)
																{
																	if($group_by==1)
																	{
																		$group_value=$row['floor_id'];
																		$group_name="Floor";
																		$group_dtls_value=$floor_arr[$row['floor_id']];
																	}
																	else if($group_by==2)
																	{
																		$group_value=$row['shift_name'];
																		$group_name="Shift";
																		$group_dtls_value=$shift_name[$row['shift_name']];
																	}
																	else if($group_by==3)
																	{
																		$group_value=$row['machine_id'];
																		$group_name="machine";
																		$group_dtls_value=$machine_arr[$row['machine_id']];
																	}
																	if (!in_array($group_value,$group_by_arr) )
																	{
																		if($k>1)
																		{ 	
																			?>
																			<tr class="tbl_bottom">
																				<td width="30">&nbsp;</td>
																				<td width="80">&nbsp;</td>

																				<? if($group_by==3 || $group_by==0){ ?>
																					<td width="80">&nbsp;</td> 
																				<? } ?>
																				<? if($group_by==2 || $group_by==0){ ?>
																					<td width="80">&nbsp;</td> 
																				<? } if($group_by==1 || $group_by==0){ ?>  
																					<td width="80">&nbsp;</td>
																				<? } ?>
																				<td width="100" align="center"><p></p></td>
																				<td width="100" align="center"><p></p></td>
																				<td width="100" align="center"><p></p></td>
																				<td width="80" align="center"><p></p></td>
																				<td width="90"></td>
																				<td width="70" align="center"></td>
																				<td width="100"></td>
																				<td width="70"><p></p></td>
																				<td width="50"><p></p></td>
																				<td width="80"><p></p></td>
																				<td width="80"><p></p></td>
																				<td width="90"><p></p></td>
																				<td width="40"><p></p></td>
																				<td align="right" width="70"><? echo  number_format($sub_production_qnty,2);?></td>
																				<td align="right" width="70"></td>
																				<td align="right" width="70"><? echo number_format($batch_qnty_tot,2);?></td>
																				<td width="70"><p></p></td>
																				<td width="70"><p></p></td>
																				<td width="75"><p></p></td>
																				<td width="75"><p></p></td>
																				<td align="center" width="60"> </td>
																				<td align="center" width="60"></td>
																				<td align="center" width="100"></td>
																				<td align="center" width="100"></td>
																				<td align="center" width="80"></td>
																				<td align="center" width="80"> </td>
																				<td width="60" align="right"><p></p></td>
																				<td width="60" align="right"><p></p></td>
																				<td width="60" align="right" ><p></p></td>
																				<td width="60" align="right"><p></p></td>
																				<td width="60" align="right"><p></p></td>
																				<td align="right" width="70" ></td>
																				<td align="center"></td>
																			</tr>                                
																			<?
																			$batch_qnty_tot=0; $trims_btq=0;$sub_production_qnty=0;
																		}
																		?>
																		<tr bgcolor="#EFEFEF">
																			<td colspan="37" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
																		</tr>
																		<?
																		$group_by_arr[]=$group_value;            
																		$k++;
																	}					
																}

																
																$batch_weight=$row['batch_weight'];
																$water_cons_unload=$row['water_flow_meter'];
																$water_cons_load=$sub_water_flow_arr[$batch_id];
																$load_hour_meter=$sub_load_hour_meter_arr[$batch_id];
																$water_cons_diff=($water_cons_unload-$water_cons_load)/$batch_weight*1000;

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
																?>
																<tr bgcolor="<? echo $bgcolor_dyeing; ?>"  id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor_dyeing; ?>')">
																	<td width="30"><? echo $i; ?></td>
																	<td width="80"><p><? echo change_date_format($date_type_cond); $unload_date=$row['process_end_date']; ?></p></td>
																	<? if($group_by==3 || $group_by==0){ ?>

																		<td align="center" width="80"><p><? echo $machine_arr[$row['machine_id']]; ?></p></td>
																	<? } if($group_by==2 || $group_by==0){ ?>
																		<td width="80"><p><? echo $floor_arr[$row['floor_id']]; ?></p></td>
																	<? } if($group_by==1 || $group_by==0){ ?>
																		<td width="80" align="center"><p><? echo $shift_name[$row['shift_name']]; ?></p></td>
																	<? } ?>

																	<td width="100" align="center">
																		<p>
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
																		</p>
																	</td>
																	<td width="100" align="center"><p><? echo $row['style_ref_no']; ?></p></td>
																	<td width="100" align="center"><p><? echo $row['season']; ?></p></td>
																	<td width="80" align="center"><p><? echo $row['job_no_prefix_num']; ?></p></td>
																	<td width="90"><p><? echo $row['booking_no']; ?></p></td>
																	<td width="70" align="center"><p><? echo $booking_type_arr[$row['booking_no']]; ?></p></td>
																	<td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $row['item_description']; ?></div></td>
																	<td width="70"><p><? echo $fabric_typee[$row['width_dia_type']]; ?></p></td>

																	<td width="50" title="<? echo implode(",",array_filter(array_unique($row["barcode_no"])));?>">
																		<p>
																			<? $bar_lot_arr= array();$yarnlots="";
																			$bar_lot_arr =array_filter(array_unique($row["barcode_no"]));
																			if(count($bar_lot_arr)>0){
																				foreach ($bar_lot_arr as $bcode) 
																				{
																					$yarnlots .= chop($yarn_lot_arr[$bcode],",").",";
																				}
																				echo implode(",",array_filter(array_unique(explode(",", $yarnlots))));  
																			}
																			?>
																		</p>
																	</td>

																	<td width="80"><p><? echo $color_library[$row['color_id']]; ?></p></td>
																	<td width="80"><p><? echo $color_range[$row['color_range_id']]; ?></p></td>
																	<? if($b==1){
																	$batch_qnty=$sub_batch_qnty_arr[$batch_id]+$row["total_trims_weight"];
																	 ?>
																		<td width="90" rowspan="<? echo $batch_td_span?>"><p><? echo $row['batch_no']; ?></p></td>
																		<td width="40" rowspan="<? echo $batch_td_span?>"><p><? echo $row['extention_no']; ?></p></td>
																		<? } ?>
																		<td align="right" width="70"><? echo number_format($row['production_qnty'],2); $grand_total_production_qnty += $row['production_qnty']; $sub_production_qnty += $row['production_qnty'];?></td>

																		<? if($b==1){?>
																			<td align="right" width="70" rowspan="<? echo $batch_td_span?>"><? echo number_format($row["total_trims_weight"],2);  ?></td>
																			<td align="right" width="70" rowspan="<? echo $batch_td_span?>"><? echo number_format($batch_qnty,2);  ?></td>
																			<?
																			$batch_qnty_tot+=$batch_qnty;
																			$grand_subcon_total_batch_qty+=$batch_qnty;
																			$trims_btq+=$row["total_trims_weight"];
																			$grand_tot_trims_qnty+=$row["total_trims_weight"];
																			$grand_sub_tot_machine_capacity+=$machine_capacity_arr[$row['machine_id']];

																			?>
																			<td align="right" width="70" rowspan="<? echo $batch_td_span?>"><? echo number_format($machine_capacity_arr[$row['machine_id']],2);  ?>
																			</td>
																			<td align="right" width="70" title="Batch Weight/Machine Capacity*100" rowspan="<? echo $batch_td_span?>"><? echo number_format(($batch_qnty/$machine_capacity_arr[$row['machine_id']]*100),2);  ?>
																			</td>
																			<td width="75" rowspan="<? echo $batch_td_span?>" title="<? echo $sub_load_date[$batch_id];?>"><p><? $load_t=$sub_load_hr[$batch_id].':'.$sub_load_min[$batch_id]; 
																			echo  ($sub_load_date[$batch_id] == '0000-00-00' || $sub_load_date[$batch_id] == '' ? '' : change_date_format($sub_load_date[$batch_id])).' <br> '.$load_t;

																			?></p></td>
																			<td width="75" rowspan="<? echo $batch_td_span?>"><p><? $hr=strtotime($unload_date,$load_t); $min=($row['end_minutes'])-($sub_load_min[$batch_id]);
																			echo  ($row['process_end_date'] == '0000-00-00' || $row['process_end_date'] == '' ? '' : change_date_format($row['process_end_date'])).'<br>'.$unload_time=$row['end_hours'].':'.$row['end_minutes']; ?></p>
																		</td>
																		<td align="center" width="60" rowspan="<? echo $batch_td_span?>">
																			<?     
																			$new_date_time_unload=($unload_date.' '.$unload_time.':'.'00');
																			$new_date_time_load=($sub_load_date[$batch_id].' '.$load_t.':'.'00');
																			$total_time=datediff(n,$new_date_time_load,$new_date_time_unload);
																			echo floor($total_time/60).":".$total_time%60;
																			?>
																		</td>
																		<td align="center" width="60" rowspan="<? echo $batch_td_span?>"><? echo $ltb_btb[$row["ltb_btb_id"]];?></td>

																		<td align="center" width="100" rowspan="<? echo $batch_td_span?>"><p><? 
																		echo $fabric_type_for_dyeing[$row['fabric_type']];?></p> </td>
																		<td align="center" width="100" rowspan="<? echo $batch_td_span?>"><p><? echo $dyeing_result[$row['result']]; ?></p> </td>
																		<td align="center" width="80" rowspan="<? echo $batch_td_span?>"><? echo $conversion_cost_head_array[$row["process_id"]]?></td>
																		<td align="center" width="80" rowspan="<? echo $batch_td_span?>" title="<? echo $row["add_tp_strip"];?>">
																			<? 
																			if($row["process_id"]==148 && $row["add_tp_strip"] ==1)
																			{
																				echo "Re-Wash";
																			}
																			else{
																				echo $dyeing_re_process[$row["add_tp_strip"]];
																			} 
																			?>
																		</td>
																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo number_format($load_hour_meter,2);  ?></p></td>
																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo number_format($row['hour_unload_meter'],2);  ?></p></td>
																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo number_format($row['hour_unload_meter']-$load_hour_meter,2);  ?></p></td>

																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo $water_cons_load;  ?></p></td>
																		<td width="60" align="right" rowspan="<? echo $batch_td_span?>"><p><? echo $water_cons_unload;  ?></p></td>
																		<td align="right" width="70" rowspan="<? echo $batch_td_span?>" title="<? echo "Water Cons Unload-Water Cons Load*1000/Batch Qnty"?>" ><? echo number_format((($water_cons_unload-$water_cons_load)*1000)/$row['batch_qnty'],2);  ?></td>

																		<td align="center" rowspan="<? echo $batch_td_span?>"><p><? echo $row['remarks']; ?></p> </td>
																		<?}?>
																	</tr>
																	<? 
																	$i++;$b++;


																	$total_water_cons_load+=$water_cons_load;
																	$total_water_cons_unload+=$water_cons_unload;


																}
															}
															if($group_by!=0)
															{
																?>
																<tr class="tbl_bottom">
																	<td width="30">&nbsp;</td>
																	<td width="80">&nbsp;</td>

																	<? if($group_by==3 || $group_by==0){ ?>
																		<td width="80">&nbsp;</td> 
																	<? } ?>
																	<? if($group_by==2 || $group_by==0){ ?>
																		<td width="80">&nbsp;</td> 
																	<? } if($group_by==1 || $group_by==0){ ?>  
																		<td width="80">&nbsp;</td>
																	<? } ?>
																	<td width="100" align="center"><p></p></td>
																	<td width="100" align="center"><p></p></td>
																	<td width="100" align="center"><p></p></td>
																	<td width="80" align="center"><p></p></td>
																	<td width="90"></td>
																	<td width="70" align="center"></td>
																	<td width="100"></td>
																	<td width="70"><p></p></td>
																	<td width="50"><p></p></td>
																	<td width="80"><p></p></td>
																	<td width="80"><p></p></td>
																	<td width="90"><p></p></td>
																	<td width="40"><p></p></td>
																	<td align="right" width="70"><? echo  number_format($sub_production_qnty,2);?></td>
																	<td align="right" width="70"></td>
																	<td align="right" width="70"><? echo number_format($batch_qnty_tot,2);?></td>
																	<td width="70"><p></p></td>
																	<td width="70"><p></p></td>
																	<td width="75"><p></p></td>
																	<td width="75"><p></p></td>
																	<td align="center" width="60"> </td>
																	<td align="center" width="60"></td>
																	<td align="center" width="100"></td>
																	<td align="center" width="100"></td>
																	<td align="center" width="80"></td>
																	<td align="center" width="80"> </td>
																	<td width="60" align="right"><p></p></td>
																	<td width="60" align="right"><p></p></td>
																	<td width="60" align="right" ><p></p></td>
																	<td width="60" align="right"><p></p></td>
																	<td width="60" align="right"><p></p></td>
																	<td align="right" width="70" ></td>
																	<td align="center"></td>
																</tr> 
																<? 
															}

															?>            
														</tbody>
														<tfoot>
															<tr class="tbl_bottom">
																<td width="30">&nbsp;</td>
																<td width="80">&nbsp;</td>

																<? if($group_by==3 || $group_by==0){ ?>
																	<td width="80">&nbsp;</td> 
																<? } ?>
																<? if($group_by==2 || $group_by==0){ ?>
																	<td width="80">&nbsp;</td> 
																<? } if($group_by==1 || $group_by==0){ ?>  
																	<td width="80">&nbsp;</td>
																<? } ?>
																<td width="100" align="center"><p></p></td>
																<td width="100" align="center"><p></p></td>
																<td width="100" align="center"><p></p></td>
																<td width="80" align="center"><p></p></td>
																<td width="90"></td>
																<td width="70" align="center"></td>
																<td width="100"></td>
																<td width="70"><p></p></td>
																<td width="50"><p></p></td>
																<td width="80"><p></p></td>
																<td width="80"><p></p></td>
																<td width="90"><p></p></td>
																<td width="40"><p></p></td>
																<td align="right" width="70"><? echo number_format($grand_total_production_qnty,2);?></td>
																<td align="right" width="70"><? echo number_format($grand_tot_trims_qnty,2);?></td>
																<td align="right" width="70"><? echo number_format($grand_subcon_total_batch_qty,2);?></td>
																<td align="right" width="70"><? echo number_format($grand_sub_tot_machine_capacity,2);?></td>
																
																<td width="70"><p></p></td>
																<td width="75"><p></p></td>
																<td width="75"><p></p></td>
																<td align="center" width="60"> </td>
																<td align="center" width="60"></td>
																<td align="center" width="100"></td>
																<td align="center" width="100"></td>
																<td align="center" width="80"></td>
																<td align="center" width="80"> </td>
																<td width="60" align="right"><p></p></td>
																<td width="60" align="right"><p></p></td>
																<td width="60" align="right" ><p></p></td>
																<td width="60" align="right"><p></p></td>
																<td width="60" align="right"><p></p></td>
																<td align="right" width="70" ></td>
																<td align="center"></td>
															</tr> 
														</tfoot>
													</table>
												</div>
											</div>
											<br/>
											<? 
										}
									}
									?>
								</fieldset>
							</div>
							<?
						} 
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
	//$filename=$user_id."_".$name.".xls";
						echo "$total_data****$filename";

						exit();
						exit();
					}

					?>