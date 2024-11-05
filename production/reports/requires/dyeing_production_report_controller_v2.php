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


	$sql="select id,batch_no,batch_for,booking_no,color_id,batch_weight from pro_batch_create_mst where company_id=$company_name and entry_form in(0) and is_deleted = 0 $field_grpby ";	


$arr=array(1=>$color_library);
	echo  create_list_view("list_view", "Batch no,Color,Booking no, Batch for,Batch weight ", "100,100,100,100,170","620","350",0, $sql, "js_set_value", "id,batch_no", "", 1, "0,color_id,0,0,0", $arr , "batch_no,color_id,booking_no,batch_for,batch_weight", "employee_info_controller",'setFilterGrid("list_view",-1);','0') ;
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
	
    echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,2,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
	 //echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"","","","");	
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

$sql="select a.id,a.buyer_name,a.style_ref_no,a.gmts_item_id,b.po_number,a.job_no_prefix_num as job_prefix,$year_field from wo_po_details_master a,wo_po_break_down b where b.job_no_mst=a.job_no and a.company_name=$company_id  $buyer_name_cond $year_cond and a.is_deleted = 0 $field_grpby";

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
	<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $data[csf('job_prefix')]; ?>')" style="cursor:pointer;">
		<td width="35"><? echo $i; ?></td>
		<td width="100"><p><? echo $data[csf('po_number')]; ?></p></td>
		<td width="100"><p><? echo $data[csf('job_prefix')]; ?></p></td>
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
}//JobNumberShow

if($action=="order_number_popup")
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
	$buyer = str_replace("'","",$buyer_name);
	$year = str_replace("'","",$year);
	$year_job = str_replace("'","",$year);
	if($db_type==0)
	{
		$year_field_by=" and YEAR(b.insert_date)"; 
		$year_field="SUBSTRING_INDEX(b.insert_date, '-', 1) as year "; 
	}
	else if($db_type==2)
	{
	$year_field_by=" and to_char(b.insert_date,'YYYY')";
	$year_field="to_char(b.insert_date,'YYYY') as year";	
	}
	if ($company_name==0) $company=""; else $company=" and b.company_name=$company_name";
	if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
	//echo $buyer;die;
	if ($buyer==0) $buyername=""; else $buyername=" and b.buyer_name=$buyer";//$cbo_buyer_name=($cbo_buyer_name==0)?"%%" : "%$cbo_buyer_name%";
	if(trim($buyer)==0) $sub_buyer_name_cond=""; else $sub_buyer_name_cond=" and a.party_id=$buyer";
	
	$sql = "select distinct a.id,b.job_no,a.po_number,b.company_name,b.buyer_name,b.job_no_prefix_num as job_prefix,$year_field from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $company $buyername $year_cond order by a.id asc"; 
	
	
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	?>
	<table width="490" border="1" rules="all" class="rpt_table">
		<thead>
		 <tr><th colspan="5"><? if($batch_type==0 || $batch_type==1) echo "Self Batch Order"; else echo "SubCon Batch Order";?>  </th></tr>
			<tr>
			<th width="30">SL</th>
			<th width="80">Order Number</th>
			<th width="50">Job no</th>
			<th width="80">Buyer</th>
			<th width="40">Year</th>
			</tr>
	   </thead>
	</table>
	<div style="max-height:300px; overflow:auto;">
	<table id="table_body2" width="490" border="1" rules="all" class="rpt_table">
	 <? $rows=sql_select($sql);
		 $i=1;
	 foreach($rows as $data)
	 {
		  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	  ?>
		<tr bgcolor="<? echo $bgcolor;?>" onClick="js_set_value('<? echo $data[csf('po_number')]; ?>')" style="cursor:pointer;">
			<td width="30"><? echo $i; ?></td>
			<td width="80"><p><? echo $data[csf('po_number')]; ?></p></td>
			<td width="50"><p><? echo $data[csf('job_prefix')]; ?></p></td>
			<td width="80"><p><? echo $buyer[$data[csf('buyer_name')]]; ?></p></td>
			<td width="40" align="center"><? echo $data[csf('year')]; ?></p></td>
		</tr>
		<? $i++; } ?>
	</table>
	</div>
	<script> setFilterGrid("table_body2",-1); </script>
	<?
	exit();
}


if($action=="generate_report_machine_wise_new") //AKH
{
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if($db_type==0) $year_field_by="and YEAR(a.insert_date)"; 
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	if($db_type==0) $field_concat="concat(machine_no,'-',brand) as machine_name"; 
	else if($db_type==2) $field_concat="machine_no || '-' || brand as machine_name";
	// machine_no || '-' || brand as machine_name
	$cbo_prod_type = str_replace("'","",$cbo_prod_type);
	$cbo_party_id = str_replace("'","",$cbo_party_id);

	$company = str_replace("'","",$cbo_company_name);
	$working_company = str_replace("'","",$cbo_working_company_name);

	$buyer = str_replace("'","",$cbo_buyer_name);
	$date_search_type = str_replace("'","",$search_type);
	$job_number = str_replace("'","",$job_number);
	$job_number_id = str_replace("'","",$job_number_show);
	$batch_no = str_replace("'","",$batch_number_show);
	
	$machine=str_replace("'","",$txt_machine_id);

	//echo $company;die;
	$batch_number_hidden = str_replace("'","",$batch_number);
	$ext_num = str_replace("'","",$txt_ext_no);
	$hidden_ext = str_replace("'","",$hidden_ext_no);
	$txt_order = str_replace("'","",$order_no);
	$file_no = str_replace("'","",$file_no);
	$ref_no = str_replace("'","",$ref_no);
	$hidden_order = str_replace("'","",$hidden_order_no);
	$cbo_type = str_replace("'","",$cbo_type);
	$cbo_result_name = str_replace("'","",$cbo_result_name);
	$year = str_replace("'","",$cbo_year);
	//echo $cbo_type;die;
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);

	if ($cbo_prod_type>0 && $cbo_party_id>0) $cbo_prod_type_cond="and f.service_company in($cbo_party_id) "; else $cbo_prod_type_cond="";
	if ($cbo_prod_type>0) $cbo_prod_source_cond="and f.service_source in($cbo_prod_type) "; else $cbo_prod_source_cond="";

	if ($buyer==0) $sub_buyer_cond=""; else $sub_buyer_cond="  and d.party_id='".$buyer."' ";
	if ($cbo_result_name==0) $result_name_cond=""; else $result_name_cond="  and f.result='".$cbo_result_name."' ";
	if ($machine=="") $machine_cond=""; else $machine_cond =" and f.machine_id in ( $machine ) ";
	
	if ($buyer==0) $buyerdata=""; else $buyerdata="  and d.buyer_name='".$buyer."' ";
	if ($buyer==0) $buyerdata2=""; else $buyerdata2="  and h.buyer_id='".$buyer."' ";
	if ($batch_no=="") $batch_num=""; else $batch_num="  and a.batch_no='".trim($batch_no)."' ";
	
	if ($batch_no=="") $unload_batch_cond=""; else $unload_batch_cond="  and batch_no='".trim($batch_no)."' ";
	if ($batch_no=="") $unload_batch_cond2=""; else $unload_batch_cond2="  and f.batch_no='".trim($batch_no)."' ";
	if ($company==0) $companyCond=""; else $companyCond="  and a.company_id=$company";
	if ($company==0) $companyCond2=""; else $companyCond2="  and f.company_id=$company";


	//$buyerdata=($buyer)?' and d.buyer_name='.$buyer : '';
	//$batch_num=($batch_no)?" and a.batch_no='".$batch_no."'" : '';
	
	//echo $cbo_batch_type;
	if(trim($ext_no)!="") $ext_no_search="%".trim($ext_no)."%"; else $ext_no_search="%%";
	if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
	{
		if ($txt_order!='') $order_no="  and c.po_number='$txt_order'"; else $order_no="";
		if ($file_no!='') $file_cond="  and c.file_no=$file_no"; else $file_cond="";
		if ($ref_no!='') $ref_cond="  and c.grouping='$ref_no'"; else $ref_cond="";
		$jobdata=($job_number_id )? " and d.job_no_prefix_num='".$job_number_id ."'" : '';
	}
	
	if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
	{
		if ($txt_order!='') $suborder_no="and c.order_no='$txt_order'"; else $suborder_no="";
		if ($job_number_id!='') $sub_job_cond="  and d.job_no_prefix_num='".$job_number_id."' "; else $sub_job_cond="";
	}
	if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==3)
	{
	if ($txt_order!='') $order_no="  and c.po_number='$txt_order'"; else $order_no="";
	if ($file_no!='') $file_cond="  and c.file_no=$file_no"; else $file_cond="";
	if ($ref_no!='') $ref_cond="  and c.grouping='$ref_no'"; else $ref_cond="";
		$jobdata=($job_number_id )? " and d.job_no_prefix_num='".$job_number_id ."'" : '';
	}
	
	//echo $order_no;die;
	if ($ext_num=="") $ext_no=""; else $ext_no="  and a.extention_no=$ext_num ";
	if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
	//echo date("Y-n-j", strtotime("first day of previous month"));
	//echo date("Y-n-j", strtotime("last day of previous month"));
	if($txt_date_from && $txt_date_to)
	{
		if($db_type==0)
		{
			
			$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
			$second_month_ldate=date("Y-m-t",strtotime($date_to));
			$dateFrom= explode("-",$date_from);
			$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
			//$last_day= date('d',strtotime($date_to));
			//$last_date=date('Y-m',strtotime($date_to));
			$prod_last_day=change_date_format($second_month_ldate,'yyyy-mm-dd');
			//$prod_date_upto=" and  f.process_end_date BETWEEN '$fromdate' AND '$prod_last_day' ";
			$current_month = date('F');
			$current_month_from_date = date('F',strtotime($date_from));
			if ($current_month_from_date != $current_month ) 
			{
				$prod_date_upto="and  f.process_end_date BETWEEN '$fromdate' AND '$prod_last_day'";
			}
			else
			{
				$prod_last_day=date('yyyy-mm-dd',strtotime("-1 days"));
				$prod_date_upto="and  f.process_end_date BETWEEN '$fromdate' AND '$prod_last_day'";
			}
			// echo $prod_date_upto;die;
			if($date_search_type==1)
			{
				$dates_com=" and  f.process_end_date BETWEEN '$date_from' AND '$date_to' ";
			}
			else
			{
				$dates_com=" and f.insert_date between '".$date_from."' and '".$date_to." 23:59:59' ";
			}
		}
		elseif($db_type==2)
		{
			
			$date_from=change_date_format($txt_date_from,'','',1);
			$date_to=change_date_format($txt_date_to,'','',1);
			$dateFrom= explode("-",$date_from);
			//echo $dateto[1];
			$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
			//$prod_date_to=change_date_format($today_date,'','',1);
			$second_month_ldate=date("Y-M-t",strtotime($date_to));
			//$last_day= date("t", strtotime($date_to));
			//$last_day= date('d',strtotime($date_to));
			//$last_date=date('Y-M',strtotime($date_to));
		    $prod_last_day=change_date_format($second_month_ldate,'','',1);
			//$dates_com="and  f.process_end_date BETWEEN '$date_from' AND '$date_to'";
			
			$current_month = date('F');
			$current_month_from_date = date('F',strtotime($date_from));
			if ($current_month_from_date != $current_month ) 
			{
				$prod_date_upto="and  f.process_end_date BETWEEN '$fromdate' AND '$prod_last_day'";
			}
			else
			{
				$prod_last_day=date('d-M-Y',strtotime("-1 days"));
				$prod_date_upto="and  f.process_end_date BETWEEN '$fromdate' AND '$prod_last_day'";
			}
			// echo $prod_date_upto;die;

			if($date_search_type==1)
			{
				$dates_com="and  f.process_end_date BETWEEN '$date_from' AND '$date_to'";
			}
			else
			{
				$dates_com=" and f.insert_date between '".$date_from."' and '".$date_to." 11:59:59 PM' ";
			}
		}

		function dateDifference($start_date, $end_date)
		{
		    // calulating the difference in timestamps 
		    $diff = strtotime($start_date) - strtotime($end_date);
		     
		    // 1 day = 24 hours 
		    // 24 * 60 * 60 = 86400 seconds
		    return ceil(abs($diff / 86400))+1;
		}			 
		// call dateDifference() function to find the number of days between two dates
		$dateDiff = dateDifference($fromdate, $prod_last_day);			 
		//echo "Difference between two dates: " . $dateDiff . " Days ";die;
	}
	if($date_search_type==1)
	{
		$date_type_msg="Dyeing Date";
	}
	else
	{
		$date_type_msg="Insert Date";
	}	

	//print_r($yarn_lot_arr);
	$load_hr=array();
	$load_min=array();
	$load_date=array();
	$water_flow_arr=array();$load_hour_meter_arr=array();
	if ($company==0) $companyCond1=""; else $companyCond1="  and company_id=$company ";
	$load_time_data=sql_select("select batch_id,water_flow_meter,batch_no,load_unload_id,process_end_date,end_hours,hour_load_meter,end_minutes from pro_fab_subprocess where load_unload_id=1 and entry_form=35 $companyCond1 $workingCompany_name_cond1 $unload_batch_cond and status_active=1  and is_deleted=0 ");
	foreach($load_time_data as $row_time)// for Loading time
	{
		$load_hr[$row_time[csf('batch_id')]]=$row_time[csf('end_hours')];
		$load_min[$row_time[csf('batch_id')]]=$row_time[csf('end_minutes')];
		$load_date[$row_time[csf('batch_id')]]=$row_time[csf('process_end_date')];
		$water_flow_arr[$row_time[csf('batch_id')]]=$row_time[csf('water_flow_meter')];
		$load_hour_meter_arr[$row_time[csf('batch_id')]]=$row_time[csf('hour_load_meter')];
	}
	$subcon_load_hr=array();
	$subcon_load_min=array();
	$subcon_load_date=array();$subcon_load_hour_meter_arr=array();
	$subcon_water_flow_arr=array();
	$subcon_load_time_data=sql_select("select batch_id,water_flow_meter,batch_no,load_unload_id,process_end_date,end_hours,hour_load_meter,end_minutes from pro_fab_subprocess where load_unload_id=1 and entry_form=38 $companyCond1 $workingCompany_name_cond1  and status_active=1  and is_deleted=0 ");
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
	$subcon_unload_time_data=sql_select("select f.batch_id,f.batch_no,f.load_unload_id,f.production_date,f.end_hours,f.end_minutes from pro_fab_subprocess f  where f.load_unload_id=2 and f.entry_form=38 $companyCond1 $workingCompany_name_cond1 and f.status_active=1  and f.is_deleted=0 $cbo_prod_type_cond $result_name_cond $shift_name_cond machine_cond $floor_id_cond");
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
	
	//$color_id = return_field_value("distinct(a.id) as id", "lib_color a ", "a.color_name='$color'", "id");
		//echo $color_id;
		//if($color_id!='') $color=$color_id;else $color="";
		
		//if ($color=="") $color_name=""; else $color_name="  and a.color_id=$color";

	foreach($machine_capacity_data as $capacity)// for Un-Loading time
	{
		$m_capacity[$capacity[csf('id')]]=$capacity[csf('m_capacity')];
	}
	
	$sql_batch_id=sql_select("select f.batch_id from  pro_fab_subprocess f where f.entry_form=35 and f.load_unload_id=2 and f.status_active=1 and f.is_deleted=0  $unload_batch_cond2 $dates_com $cbo_prod_type_cond $result_name_cond $companyCond2  $machine_cond ");
	
	$tot_row=1;
	foreach($sql_batch_id as $row_batch)
	{
		if($tot_row!=1) $batch_id.=",";	
		$batch_id.=$row_batch[csf('batch_id')];	
		
		$tot_row++;
	}
	//echo $batch_id;die;
	unset($sql_batch_id);
	if ($batch_id!="") 
	{
		$batchIds=chop($batch_id,','); $batchIds_cond="";
		if($db_type==2 && count($tot_row)>990)
		{
			$batchIds_cond=" and (";
			$batchIdsArr=array_chunk(explode(",",$batchIds),990);
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
			$batchIds_cond=" and a.id not in($batchIds)";
		}
	}	
	// echo $batchIds_cond;die;
	$sub_sql_batch_id=sql_select("select f.batch_id from  pro_fab_subprocess f where f.entry_form=38 and f.load_unload_id=2 and f.status_active=1 and f.is_deleted=0 $cbo_prod_type_cond $result_name_cond machine_cond ");
	$k=1;
	foreach($sub_sql_batch_id as $row_batch)
	{
		if($k!=1) $sub_batch_id.=",";	
		$sub_batch_id.=$row_batch[csf('batch_id')];	
		
		$k++;
	}
	if($batch_id=="") $batch_id=0;
	if($sub_batch_id=="") $sub_batch_id=0;
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
		$order_by="order by f.seq_no,f.process_end_date,f.end_hours";
		$order_by2="order by seq_no,process_end_date,end_hours";
	}
	else
	{
		$order_by="order by f.process_end_date,f.machine_id";
		$order_by2="order by process_end_date,end_hours,machine_id";
	}
	
	if($db_type==2)
	{
		$grp_con="LISTAGG(CAST(c.po_number AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS po_number,LISTAGG(CAST(b.po_id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS po_id,LISTAGG(CAST(c.grouping AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS grouping,LISTAGG(CAST(c.file_no AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS file_no";
		$trims_grp_con=" LISTAGG(CAST(c.po_number AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS po_number, LISTAGG(CAST(c.id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS po_id,
		LISTAGG(CAST(c.grouping AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS grouping,LISTAGG(CAST(c.file_no AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS file_no ";
		$grp_sub_con="LISTAGG(CAST(c.order_no AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS po_number,LISTAGG(CAST(b.po_id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS po_id";
	}
	else if($db_type==0)
	{
		$grp_con="group_concat(distinct c.po_number) AS po_number,group_concat(distinct b.po_id) AS po_id,group_concat(distinct c.grouping) AS grouping,group_concat(distinct c.file_no) AS file_no";
		$trims_grp_con="group_concat(distinct c.po_number) AS po_number,group_concat(distinct c.id) AS po_id,group_concat(distinct c.grouping) AS grouping,group_concat(distinct c.file_no) AS file_no";
		$grp_sub_con="group_concat(distinct c.order_no) AS po_number,group_concat(distinct b.po_id) AS po_id";
	}
	/*	$sql_batch_id=("select f.batch_id from  pro_fab_subprocess f where f.entry_form=35 $companyCond1 and f.load_unload_id=2 and f.status_active=1 and f.is_deleted=0  $unload_batch_cond2 $dates_com $cbo_prod_source_cond $machine_cond  $cbo_prod_type_cond $dyeing_batch_id_cond");
	$tot_row=1;$batch_id='';
	foreach($sql_batch_id as $row_batch)
	{
	if($batch_id=='') $batch_id=$row_batch[csf('batch_id')];else $batch_id.=",".$row_batch[csf('batch_id')];
	$batch_unload_check[$row_batch[csf('batch_id')]]=$row_batch[csf('batch_id')];

	}//echo $batch_id;die;
	unset($sql_batch_id);
	
	if($batch_id!='')
	{
		$batchIds=chop($batch_id,','); $batchIds_cond="";
		$tot_ids=count(array_unique(explode(",",$batch_id)));
		//echo $tot_ids.'d';
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
	}*/
		
	if($cbo_type==1)//   For WiP
	{
		$sql="(select a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight,SUM(b.batch_qnty) AS batch_qnty, b.item_description, b.prod_id, b.width_dia_type, $grp_con, d.job_no as job_no_prefix_num, d.buyer_name,f.remarks, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,a.booking_no,a.booking_without_order,g.seq_no, a.entry_form 
		from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g, pro_batch_create_mst a 
		where f.batch_id=a.id and f.batch_id=b.mst_id $companyCond  $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond   $result_name_cond  $machine_cond  $file_cond $ref_cond $cbo_prod_source_cond $cbo_prod_type_cond $batchIds_cond and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=1 and a.batch_against in(1,2,3) and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0 
		GROUP BY a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id, a.color_id,a.total_trims_weight, a.extention_no, b.item_description, b.prod_id, b.width_dia_type,d.job_no, d.buyer_name, f.shift_name, f.production_date,f.process_end_date, f.end_hours, f.floor_id,f.remarks,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id,g.seq_no, f.load_unload_id,f.fabric_type,f.result,a.booking_no,a.booking_without_order, a.entry_form)
		union
		(select a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight, SUM(b.batch_qnty) AS batch_qnty,b.item_description, b.prod_id, b.width_dia_type,null as po_number ,null as po_id,null as grouping, null as file_no, null as job_no_prefix_num,h.buyer_id as buyer_name,f.remarks,f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id,f.load_unload_id, f.fabric_type,f.result,a.booking_no,a.booking_without_order,g.seq_no, a.entry_form 
		from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h 
		where h.booking_no=a.booking_no $companyCond   and f.batch_id=a.id and f.batch_id=b.mst_id  and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=1 and a.batch_against in(1,2,3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  f.status_active=1 and f.is_deleted=0 $dates_com  $batch_num  $buyerdata2 $result_name_cond  $machine_cond $cbo_prod_type_cond $cbo_prod_source_cond $batchIds_cond 
		GROUP BY a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id,h.buyer_id, a.color_id, a.extention_no, b.item_description, b.prod_id, b.width_dia_type,g.seq_no, f.shift_name,a.total_trims_weight, f.production_date,f.process_end_date, f.end_hours, f.floor_id,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id,g.seq_no, f.load_unload_id,f.fabric_type,a.booking_without_order,a.booking_no, f.result,f.remarks, a.entry_form) 
		union
		(select a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight,sum(b.trims_wgt_qnty) as batch_qnty, b.item_description, null as prod_id, null as width_dia_type, null as po_number, null as po_id, null as grouping,null as file_no, d.job_no as job_no_prefix_num, d.buyer_name,f.remarks, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,
		f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,a.booking_no,a.booking_without_order,g.seq_no, a.entry_form 
		from pro_batch_trims_dtls b, wo_po_details_master d, pro_fab_subprocess f, lib_machine_name g, pro_batch_create_mst a 
		where f.batch_id=a.id and f.batch_id=b.mst_id $companyCond  $dates_com $jobdata $batch_num $buyerdata $year_cond   $result_name_cond  $machine_cond $cbo_prod_source_cond $cbo_prod_type_cond $batchIds_cond and a.entry_form=136 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=1 and a.batch_against in(1,2,3) and A.JOB_NO=d.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 
		GROUP BY a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id, a.color_id,a.total_trims_weight, a.extention_no, b.item_description, d.job_no, d.buyer_name, f.shift_name, f.production_date,f.process_end_date, f.end_hours, f.floor_id,f.remarks,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id,g.seq_no, f.load_unload_id,f.fabric_type,f.result,a.booking_no, a.booking_without_order, a.entry_form)
		$order_by2";
		// echo $sql;
	}
	else if($cbo_type==2)//   For Order Wise Dyeing Production
	{
		$sql="(select a.batch_against,a.company_id,a.batch_no,a.entry_form, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight,SUM(b.batch_qnty) AS batch_qnty, b.item_description, b.prod_id, b.width_dia_type, $grp_con, d.job_no as job_no_prefix_num, d.buyer_name,f.remarks, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,a.booking_no,a.booking_without_order,g.seq_no from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g, pro_batch_create_mst a where f.batch_id=a.id and f.batch_id=b.mst_id $companyCond  $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond   $result_name_cond  $machine_cond  $file_cond $ref_cond $cbo_prod_source_cond $cbo_prod_type_cond and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,2,3) and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0 
		GROUP BY a.batch_against,a.company_id,a.batch_no,a.entry_form, a.batch_weight,a.id, a.color_id,a.total_trims_weight, a.extention_no, b.item_description, b.prod_id, b.width_dia_type,d.job_no, d.buyer_name, f.shift_name, f.production_date,f.process_end_date, f.end_hours, f.floor_id,f.remarks,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id,g.seq_no, f.load_unload_id,f.fabric_type,f.result,a.booking_no,a.booking_without_order)
		union
		(
		select a.batch_against,a.company_id,a.batch_no,a.entry_form, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight, SUM(b.batch_qnty) AS batch_qnty,b.item_description, b.prod_id, b.width_dia_type,null as po_number ,null as po_id,null as grouping, null as file_no, null as job_no_prefix_num,h.buyer_id as buyer_name,f.remarks,f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date,
		f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id,f.load_unload_id, f.fabric_type,f.result,a.booking_no,a.booking_without_order,g.seq_no from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h where h.booking_no=a.booking_no $companyCond   and f.batch_id=a.id and f.batch_id=b.mst_id  and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,2,3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  f.status_active=1 and f.is_deleted=0 $dates_com  $batch_num  $color_name  $buyerdata2 $result_name_cond  $machine_cond $cbo_prod_type_cond $cbo_prod_source_cond GROUP BY a.batch_against,a.company_id,a.batch_no,a.entry_form, a.batch_weight,a.id,h.buyer_id, a.color_id, a.extention_no, b.item_description, b.prod_id, b.width_dia_type,g.seq_no,
		f.shift_name,a.total_trims_weight, f.production_date,f.process_end_date, f.end_hours, f.floor_id,f.hour_unload_meter, f.water_flow_meter,
		f.end_minutes, f.machine_id,g.seq_no, f.load_unload_id,f.fabric_type,a.booking_without_order,a.booking_no, f.result,f.remarks
		) 
		union
		(
		select a.batch_against,a.company_id,a.batch_no,a.entry_form, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight,SUM(b.trims_wgt_qnty) AS batch_qnty, null as item_description, null as prod_id, null as width_dia_type, null as po_number ,null as po_id,null as grouping, null as file_no, d.job_no as job_no_prefix_num, d.buyer_name,f.remarks, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,a.booking_no,a.booking_without_order,g.seq_no from pro_batch_create_mst a,pro_batch_trims_dtls b, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g where a.id=b.mst_id and f.batch_id=a.id and f.batch_id=b.mst_id and a.entry_form=136 and g.id=f.machine_id and  f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,2,3) and d.job_no=a.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  $companyCond  $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond   $result_name_cond  $machine_cond   $cbo_prod_source_cond $cbo_prod_type_cond
		GROUP BY a.batch_against,a.company_id,a.batch_no,a.entry_form, a.batch_weight,a.id, a.color_id,a.total_trims_weight, a.extention_no, b.item_description,d.job_no, d.buyer_name, f.shift_name, f.production_date,f.process_end_date, f.end_hours, f.floor_id,f.remarks,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id,g.seq_no, f.load_unload_id,f.fabric_type,f.result,a.booking_no,a.booking_without_order
		)
		$order_by2";
		//echo $sql;die;
	}
	else if($cbo_type==3)//   LTB-BTB
	{	
		$sql="(select a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight,SUM(b.batch_qnty) AS batch_qnty, b.item_description, b.prod_id, b.width_dia_type, $grp_con, d.job_no as job_no_prefix_num, d.buyer_name,f.remarks, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,a.booking_no,a.booking_without_order,g.seq_no from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g, pro_batch_create_mst a where f.batch_id=a.id and f.batch_id=b.mst_id $companyCond  $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond   $result_name_cond  $machine_cond  $file_cond $ref_cond $cbo_prod_source_cond $cbo_prod_type_cond and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1) and f.result in(1,2,3,4,5) and f.ltb_btb_id=1 and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0 
		GROUP BY a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id, a.color_id,a.total_trims_weight, a.extention_no, b.item_description, b.prod_id, b.width_dia_type,d.job_no, d.buyer_name, f.shift_name, f.production_date,f.process_end_date, f.end_hours, f.floor_id,f.remarks,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id,g.seq_no, f.load_unload_id,f.fabric_type,f.result,a.booking_no,a.booking_without_order)
		union
		(
		select a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight, SUM(b.batch_qnty) AS batch_qnty,b.item_description, b.prod_id, b.width_dia_type,null as po_number ,null as po_id,null as grouping, null as file_no, null as job_no_prefix_num,h.buyer_id as buyer_name,f.remarks,f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date,
		f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id,f.load_unload_id, f.fabric_type,f.result,a.booking_no,a.booking_without_order,g.seq_no from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h where h.booking_no=a.booking_no $companyCond   and f.batch_id=a.id and f.batch_id=b.mst_id  and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1) and f.result in(1,2,3,4,5) and f.ltb_btb_id=1 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  f.status_active=1 and f.is_deleted=0 $dates_com  $batch_num  $color_name  $buyerdata2 $result_name_cond  $machine_cond $cbo_prod_type_cond $cbo_prod_source_cond GROUP BY a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id,h.buyer_id, a.color_id, a.extention_no, b.item_description, b.prod_id, b.width_dia_type,g.seq_no,
		f.shift_name,a.total_trims_weight, f.production_date,f.process_end_date, f.end_hours, f.floor_id,f.hour_unload_meter, f.water_flow_meter,
		f.end_minutes, f.machine_id,g.seq_no, f.load_unload_id,f.fabric_type,a.booking_without_order,a.booking_no, f.result,f.remarks
		) $order_by2";
		$sql_ltb="(select a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight,SUM(b.batch_qnty) AS batch_qnty, b.item_description, b.prod_id, b.width_dia_type, $grp_con, d.job_no as job_no_prefix_num, d.buyer_name,f.remarks, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,a.booking_no,a.booking_without_order,g.seq_no from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g, pro_batch_create_mst a where f.batch_id=a.id and f.batch_id=b.mst_id $companyCond  $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond   $result_name_cond  $machine_cond  $file_cond $ref_cond $cbo_prod_source_cond $cbo_prod_type_cond and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1) and f.result in(1,2,3,4,5) and f.ltb_btb_id=2 and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0 
		GROUP BY a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id, a.color_id,a.total_trims_weight, a.extention_no, b.item_description, b.prod_id, b.width_dia_type,d.job_no, d.buyer_name, f.shift_name, f.production_date,f.process_end_date, f.end_hours, f.floor_id,f.remarks,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id,g.seq_no, f.load_unload_id,f.fabric_type,f.result,a.booking_no,a.booking_without_order)
		union
		(
		select a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight, SUM(b.batch_qnty) AS batch_qnty,b.item_description, b.prod_id, b.width_dia_type,null as po_number ,null as po_id,null as grouping, null as file_no, null as job_no_prefix_num,h.buyer_id as buyer_name,f.remarks,f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date,
		f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id,f.load_unload_id, f.fabric_type,f.result,a.booking_no,a.booking_without_order,g.seq_no from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h where h.booking_no=a.booking_no $companyCond   and f.batch_id=a.id and f.batch_id=b.mst_id  and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1) and f.result in(1,2,3,4,5) and f.ltb_btb_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  f.status_active=1 and f.is_deleted=0 $dates_com  $batch_num  $color_name  $buyerdata2 $result_name_cond  $machine_cond $cbo_prod_type_cond $cbo_prod_source_cond GROUP BY a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id,h.buyer_id, a.color_id, a.extention_no, b.item_description, b.prod_id, b.width_dia_type,g.seq_no,
		f.shift_name,a.total_trims_weight, f.production_date,f.process_end_date, f.end_hours, f.floor_id,f.hour_unload_meter, f.water_flow_meter,
		f.end_minutes, f.machine_id,g.seq_no, f.load_unload_id,f.fabric_type,a.booking_without_order,a.booking_no, f.result,f.remarks
		) $order_by2";
		//echo $sql;die;
	}
	else if($cbo_type==4)//   For Re Process
	{	
		$sql="(select a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight,SUM(b.batch_qnty) AS batch_qnty, b.item_description, b.prod_id, b.width_dia_type, $grp_con, d.job_no as job_no_prefix_num, d.buyer_name,f.remarks, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,a.booking_no,a.booking_without_order,g.seq_no from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g, pro_batch_create_mst a where f.batch_id=a.id and f.batch_id=b.mst_id $companyCond  $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond   $result_name_cond  $machine_cond  $file_cond $ref_cond $cbo_prod_source_cond $cbo_prod_type_cond and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(2) and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0 
		GROUP BY a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id, a.color_id,a.total_trims_weight, a.extention_no, b.item_description, b.prod_id, b.width_dia_type,d.job_no, d.buyer_name, f.shift_name, f.production_date,f.process_end_date, f.end_hours, f.floor_id,f.remarks,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id,g.seq_no, f.load_unload_id,f.fabric_type,f.result,a.booking_no,a.booking_without_order)
		union
		(
		select a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight, SUM(b.batch_qnty) AS batch_qnty,b.item_description, b.prod_id, b.width_dia_type,null as po_number ,null as po_id,null as grouping, null as file_no, null as job_no_prefix_num,h.buyer_id as buyer_name,f.remarks,f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date,
		f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id,f.load_unload_id, f.fabric_type,f.result,a.booking_no,a.booking_without_order,g.seq_no from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h where h.booking_no=a.booking_no $companyCond   and f.batch_id=a.id and f.batch_id=b.mst_id  and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(2) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  f.status_active=1 and f.is_deleted=0 $dates_com  $batch_num  $color_name  $buyerdata2 $result_name_cond  $machine_cond $cbo_prod_type_cond $cbo_prod_source_cond GROUP BY a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id,h.buyer_id, a.color_id, a.extention_no, b.item_description, b.prod_id, b.width_dia_type,g.seq_no,
		f.shift_name,a.total_trims_weight, f.production_date,f.process_end_date, f.end_hours, f.floor_id,f.hour_unload_meter, f.water_flow_meter,
		f.end_minutes, f.machine_id,g.seq_no, f.load_unload_id,f.fabric_type,a.booking_without_order,a.booking_no, f.result,f.remarks
		) 
		union


		$order_by2";
		//echo $sql;die;
	}
	
	// echo $sql;
	if($cbo_type==2 || $cbo_type==1 || $cbo_type==4 || $cbo_type==3)
	{
		$batchdata=sql_select($sql);$total_batch_qty_re_dyeing=0;$sum_trims_check_array=array();$m=1;
		foreach($batchdata as $row)
		{
			if($row[csf('batch_against')]==2)//Re-Dyeing
			{
				$buyer_re_process_arr[$row[csf('buyer_name')]]['re_qty']+=$row[csf('batch_qnty')];
				$total_batch_qty_re_dyeing+=$row[csf('batch_qnty')];
				 
				  $batch_no=$row[csf('id')];
				if (!in_array($batch_no,$sum_trims_check_array))
					{ $m++;
						
						
						 $sum_trims_check_array[]=$batch_no;
						  $sumtot_trim_qty=$row[csf('total_trims_weight')];
					}
					else
					{
						 $sumtot_trim_qty=0;
					}
				  // $trims_total_batch_qty+=$sumtot_trim_qty;
				   $buyer_re_process_arr[$row[csf('buyer_name')]]['trims']+=$sumtot_trim_qty;
			}
		}
		//echo  $buyer_re_process_arr[1]['re_qty'].'dsd';
		if($cbo_type==3)
		{
			$batchdata_ltb=sql_select($sql_ltb);
			//$matched_btb=count($sql_btb_data);
			$tot_btb_matched=0;$tot_btb_not_matched=0;
			foreach($batchdata as $btb)
			{
				if($btb[csf('result')]==1)//Shade Matched
				{
					$tot_btb_matched+=1;
				}
				else if($btb[csf('result')]!=1)
				{
					$tot_btb_not_matched+=1;
					//echo "not,";
				}
			}
			//echo $tot_btb_not_matched;
			$tot_ltb_matched=0;$tot_ltb_not_matched=0;
			foreach($batchdata_ltb as $ltb)
			{
				if($ltb[csf('result')]==1)//Shade Matched
				{
					$tot_ltb_matched+=1;
				}
				else
				{
					$tot_ltb_not_matched+=1;
				}
			}
			//echo $tot_btb_not_matched;
		}
		$batch_ids='';$all_po_id='';
		foreach($batchdata as $row)
		{
			if($batch_ids=='') $batch_ids=$row[csf('id')]; else $batch_ids.=",".$row[csf('id')];
			if($all_po_id=='') $all_po_id=$row[csf('po_id')]; else $all_po_id.=",".$row[csf('po_id')];
		}
		$po_idsid=implode(",",(array_unique(explode(",",$all_po_id))));
		$batch_idss=implode(",",(array_unique(explode(",",$batch_ids))));
		
		$poIds=chop($po_idsid,','); $po_cond_for_in=""; 
		$po_ids=count(array_unique(explode(",",$po_idsid)));
		if($db_type==2 && $po_ids>999)
		{
			$po_cond_for_in=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
			$ids=implode(",",$ids);
			$po_cond_for_in.="b.po_breakdown_id in($ids) or"; 
			}
			$po_cond_for_in=chop($po_cond_for_in,'or ');
			$po_cond_for_in.=")";
		}
		else
		{
		$po_cond_for_in=" and b.po_breakdown_id in($poIds)";
		}
		
		$batchIds=chop($batch_idss,','); $batch_cond_for_in=""; 
		$batch_ids=count(array_unique(explode(",",$batchIds)));
		if($db_type==2 && $batch_ids>999)
		{
			$batch_cond_for_in=" and (";
			$batchIdsArr=array_chunk(explode(",",$batchIds),999);
			foreach($batchIdsArr as $ids)
			{
			$ids=implode(",",$ids);
			$batch_cond_for_in.="a.id in($ids) or"; 
			}
			$batch_cond_for_in=chop($batch_cond_for_in,'or ');
			$batch_cond_for_in.=")";
		}
		else
		{
			$batch_cond_for_in=" and a.id in($batchIds)";
		}
			
		//}
		
	
		//print_r($sql_subcon_data);
		$yarn_lot_arr=array();
		if($db_type==0)
		{
			$yarn_lot_data=sql_select("select b.po_breakdown_id, a.prod_id, a.yarn_lot as yarn_lot from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yarn_lot!=''  $po_cond_for_in  group by a.prod_id, b.po_breakdown_id");
		}
		else if($db_type==2)
		{
			$yarn_lot_data=sql_select("select b.po_breakdown_id, a.prod_id,  a.yarn_lot as yarn_lot from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yarn_lot!='0' $po_cond_for_in group by a.prod_id, b.po_breakdown_id,a.yarn_lot");
		}
		foreach($yarn_lot_data as $rows)
		{
			//$yarn_lot=explode(",",$rows[csf('yarn_lot')]);
			$yarn_lot_arr[$rows['prod_id']][$rows['po_breakdown_id']].=$rows[csf('yarn_lot')].',';
		}
	}
	
	ob_start();
	if($cbo_type==2 || $cbo_type==1 || $cbo_type==4) //  Dyeing Production Show data
	{
		?>
		<fieldset style="width:1350px;">
		<div align="center"><strong> <? echo $company_library[$company]; ?> </strong><br> <strong> Daily Dyeing Production </strong><br>
		<?
			echo  ($date_from == '0000-00-00' || $date_from == '' ? '' : change_date_format($date_from)).' To ';echo  ($date_to == '0000-00-00' || $date_to == '' ? '' : change_date_format($date_to));
			?>
		</div>
		<? 
		if($cbo_type!=1) // without wip
		{ 
			if ($cbo_result_name==1 || $cbo_result_name==0) // All summary Start
			{ 
			 	?>
			 	<div>                        
				<table cellpadding="0"  width="620" cellspacing="0" align="left" style="margin-left:20px;">
					<tr>
						<!-- Production Summary Start-->
						 <!-- Trims batch already added in Production Summary part-->
					 	<td>
	                 		<table cellpadding="0"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
								<thead>
									<tr>
										<th colspan="3">Production Summary</th>
									</tr>
									<tr>
										<th>Details </th> 
										<th>Prod. Qty. </th>
										<th>%</th>
									</tr>
								</thead>
								<tbody>
								<?
								if ($cbo_result_name==0 || $cbo_result_name==1) $result_name_cond_summary=" and f.result=1"; else $result_name_cond_summary=" ";
								
								$sql="(SELECT f.load_unload_id,f.process_end_date,count(f.process_end_date) as row_count,
								sum(CASE WHEN a.batch_against in(1,3) THEN b.batch_qnty ELSE 0 END) AS batch_qnty,
								sum(distinct CASE WHEN a.batch_against in(1,3) THEN a.total_trims_weight ELSE 0 END) AS total_trims_weight,
								sum(CASE WHEN a.batch_against in(2) THEN b.batch_qnty ELSE 0 END) AS re_batch_qnty 

								from pro_batch_create_mst a,pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g 
								where f.batch_id=a.id  and g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and a.entry_form=0 and  f.entry_form=35 and f.load_unload_id=2 and f.result=1 and a.batch_against in(1,2)  and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  $prod_date_upto $jobdata $batch_num $buyerdata $order_no $year_cond $color_name $companyCond $workingCompany_name_cond2 $shift_name_cond $machine_cond $floor_id_cond  $result_name_cond_summary $cbo_prod_type_cond $cbo_prod_source_cond 
								GROUP BY f.load_unload_id,f.process_end_date)
								union
								(
								SELECT f.load_unload_id,f.process_end_date,count(f.process_end_date) as row_count,
								sum(CASE WHEN a.batch_against in(1,3) THEN b.trims_wgt_qnty ELSE 0 END) AS batch_qnty,
								sum(distinct CASE WHEN a.batch_against in(1,3) THEN a.total_trims_weight ELSE 0 END) AS total_trims_weight,
								sum(CASE WHEN a.batch_against in(2) THEN b.trims_wgt_qnty ELSE 0 END) AS re_batch_qnty 

								from pro_batch_create_mst a,pro_batch_trims_dtls b, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g 
								where f.batch_id=a.id  and g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and a.entry_form=136 and  f.entry_form=35 and f.load_unload_id=2 and f.result=1 and a.batch_against in(1,2) and d.job_no=a.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  $prod_date_upto $jobdata $batch_num $buyerdata $order_no $year_cond $color_name $companyCond $workingCompany_name_cond2 $shift_name_cond $machine_cond $floor_id_cond  $result_name_cond_summary $cbo_prod_type_cond $cbo_prod_source_cond 
								GROUP BY f.load_unload_id,f.process_end_date 
								)
								";
								// echo $sql;
								$sql_datas=sql_select($sql);
								
								$unload_qty_arr=array();$plan_cycle_time=0;$reprocess_qty_arr=array();$tot_reprocess_qty=0;
								foreach($sql_datas as $row)
								{
									$tot_row=count($row[csf('process_end_date')]);
									$unload_qty_arr[$row[csf('load_unload_id')]]['qty']+=$row[csf('batch_qnty')]+$row[csf('total_trims_weight')];
									//$reprocess_qty_arr[2]['bqty']+=$row[csf('batch_qnty')];
									 $tot_reprocess_qty+=$row[csf('re_batch_qnty')];
									$unload_qty_arr[$row[csf('load_unload_id')]]['count']+=$tot_row;
									$unload_qty_arr[$row[csf('load_unload_id')]]['process_end_date'].=$row[csf('process_end_date')].',';		
								}
								unset($sql_datas);
								
								//print_r($unload_qty_arr);
								$total_current_mon_qty1=$unload_qty_arr[2]['qty'];
								$total_count1=$unload_qty_arr[2]['count'];
								$total_reprocess_qty1=$tot_reprocess_qty;
								 
								$sql_sample_currMon="SELECT f.load_unload_id,f.process_end_date,count(f.process_end_date) as row_count,
								sum(CASE WHEN a.batch_against in(3) THEN b.batch_qnty ELSE 0 END) AS batch_qnty,
								sum(distinct CASE WHEN a.batch_against in(1) THEN a.total_trims_weight ELSE 0 END) AS total_trims_weight,
								sum(CASE WHEN a.batch_against in(2) THEN b.batch_qnty ELSE 0 END) AS re_batch_qnty 
								from pro_batch_create_dtls b, wo_non_ord_samp_booking_mst h, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where  f.batch_id=a.id $companyCond   and a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and f.result=1  and a.batch_against in(2,3)  and h.booking_no=a.booking_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  $prod_date_upto  $batch_num $buyerdata2  $machine_cond $floor_id_cond  $result_name_cond_summary $cbo_prod_type_cond $cbo_prod_source_cond 
								GROUP BY  f.load_unload_id,f.process_end_date  ";
								$sql_result_samp_currMon=sql_select($sql_sample_currMon);
								$tot_reprocess_qty2=0;
								$process_enddate=rtrim($unload_qty_arr[2]['process_end_date'],',');
								$process_enddates=array_unique(explode(",",$process_enddate));
								foreach($sql_result_samp_currMon as $row)
								{
									$tot_row=count($row[csf('process_end_date')]);
									$unload_qty_arr2[$row[csf('load_unload_id')]]['qty']+=$row[csf('batch_qnty')]+$row[csf('total_trims_weight')];
									//$reprocess_qty_arr[2]['bqty']+=$row[csf('batch_qnty')];
									 $tot_reprocess_qty2+=$row[csf('re_batch_qnty')];
									 
									  $isval=array_diff($row[csf('process_end_date')],$edate);
									  $tot_rows=0;
									 foreach($process_enddates as $edate)
									 {
										 
										  $tot_rows=count($row[csf('process_end_date')]);
										  $isval=array_diff($row[csf('process_end_date')],$edate);
										 if($isval)
										 {
											$unload_qty_arr2[$row[csf('load_unload_id')]]['count']+=$tot_rows;	 	
										 }
										
									}
									  
									//$unload_qty_arr2[$row[csf('load_unload_id')]]['count']+=$tot_row;		
								}
								unset($sql_result_samp_currMon);
								$total_current_mon_qty=$unload_qty_arr2[2]['qty']+$total_current_mon_qty1;
								$total_count=$total_count1+$unload_qty_arr2[2]['count'];
								$total_reprocess_qty=$total_reprocess_qty1+$tot_reprocess_qty2;
								
								$sql_result_sample="SELECT a.id,f.fabric_type,f.process_end_date,
								SUM(b.batch_qnty) AS batch_qnty,sum(a.batch_weight) as batch_weight,sum(distinct a.total_trims_weight) as total_trims_weight 
								from pro_batch_create_dtls b, wo_non_ord_samp_booking_mst h, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g 
								where f.batch_id=a.id $companyCond $workingCompany_name_cond2 and  a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2  and a.batch_against in(1,3)  and h.booking_no=a.booking_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and f.fabric_type in(1,2,4,5,6) and  f.status_active=1 and f.is_deleted=0  $dates_com  $batch_num $buyerdata2   $machine_cond   $result_name_cond_summary $cbo_prod_type_cond $cbo_prod_source_cond
								GROUP BY  a.id,f.fabric_type,f.process_end_date  ";
								$sql_result_sam=sql_select($sql_result_sample);
								$fabric_batch_arr=array();$tot_batch_qty_type=array();//$trims_wgt_check_array=array();
								$zz=1;
								foreach($sql_result_sam as $row)
								{
											
									$tot_trim_qty=$row[csf('total_trims_weight')];
									$fabric_batch_arr[$row[csf('fabric_type')]]['qty']+=$row[csf('batch_qnty')]+$tot_trim_qty;
									$fabric_batch_arr[$row[csf('fabric_type')]]['weight']+=$row[csf('batch_weight')];
									$tot_batch_qty_type[1]+=$row[csf('batch_qnty')]+$tot_trim_qty;
								}
								unset($sql_result_sam);
								
								$sql_result="(select a.id,f.fabric_type,f.process_end_date,
								SUM(b.batch_qnty) AS batch_qnty,sum(a.batch_weight) as batch_weight,sum(distinct a.total_trims_weight) as total_trims_weight 
								from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g 
								where f.batch_id=a.id $companyCond   and a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2  and a.batch_against in(1,3)  and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and f.fabric_type in(1,2,4,5,6) and  f.status_active=1 and f.is_deleted=0 $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond  $machine_cond  $result_name_cond_summary $cbo_prod_type_cond $cbo_prod_source_cond 
								GROUP BY a.id, f.fabric_type,f.process_end_date)
								union
								(
								select a.id,f.fabric_type,f.process_end_date,
								SUM(b.trims_wgt_qnty) AS batch_qnty,sum(a.batch_weight) as batch_weight,
								sum(distinct a.total_trims_weight) as total_trims_weight 
								from pro_batch_trims_dtls b, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g 
								where f.batch_id=a.id $companyCond   and a.entry_form=136 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2  and a.batch_against in(1,3) and d.job_no=a.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  f.status_active=1 and f.is_deleted=0 and f.fabric_type in(1,2,4,5,6) $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond  $machine_cond  $result_name_cond_summary $cbo_prod_type_cond $cbo_prod_source_cond 
								GROUP BY a.id, f.fabric_type,f.process_end_date
								)
								";
								$sql_result=sql_select($sql_result);
								foreach($sql_result as $row)
								{
									$tot_trim_qty=$row[csf('total_trims_weight')];
									$fabric_batch_arr[$row[csf('fabric_type')]]['qty']+=$row[csf('batch_qnty')]+$tot_trim_qty;
									$fabric_batch_arr[$row[csf('fabric_type')]]['weight']+=$row[csf('batch_weight')];
									$tot_batch_qty_type[1]+=$row[csf('batch_qnty')]+$tot_trim_qty;
								}
								unset($sql_result);
								//print_r($fabric_batch_arr);
								?>
							  	<tr bgcolor="#E9F3FF" style="cursor:pointer;">
								  <td>Current Month</td>
	                               <td align="right"><? echo number_format($total_current_mon_qty,2);?></td> 
								  <td align="right"><?  ?></td>  
							  	</tr>
	                          	<tr bgcolor="#D8D8D8" style="cursor:pointer;">
								 
								  <td>Avg. Prod. Per Day</td>
	                               <td align="right" title="<? echo 'Total Day: '.$dateDiff;?>"><? echo number_format($total_current_mon_qty/$dateDiff,2); ?></td> 
								  <td align="right"><? //echo $total_current_mon_qty/$total_count; ?></td>  
							  	</tr>
	                           	<tr bgcolor="#D8D8D8" style="cursor:pointer; ">
								 
								  <td>ReProcess Current Month</td>
	                               <td align="right"><?   echo number_format($total_reprocess_qty,2);//round( 1040.56789, 4, PHP_ROUND_HALF_EVEN) ?></td> 
								  <td align="right"><? //echo $total_current_mon_qty/$total_count; ?></td>  
							  	</tr>
	                            <? $k=1;	$tot_batch_qty=0;
								//$fabric_type_for_dyeing2=array(1=>'Cotton',2=>'Polyster',3=>'Lycra',4=>'Both Part',5=>'White',6=>'Wash');
	                             
								//print_r($fabric_type_for_dyeingnn);
								foreach($fabric_batch_arr as $typekey=>$val)
								{
									if ($k%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";
									if($typekey==1)
									{
										$fab_type="Cotton";
									}
									else if($typekey==2)
									{
										$fab_type="Polyster";
									}
									else if($typekey==3)
									{
										$fab_type="Lycra";
									}
									else if($typekey==4)
									{
										$fab_type="Both Part";
									}
									else if($typekey==5)
									{
										$fab_type="White";
									}
									else if($typekey==6)
									{
										$fab_type="Wash";
									}
									$total_reporcess=$tot_batch_qty_type[1];
									?>
	                               	<tr bgcolor="<? echo $bgcolor;?>">
									 <? //print_r($fabric_type_for_dyeing);?>
									  <td><?php echo $fab_type;//$fabric_type_for_dyeing[$typekey];?></td>
	                                   <td align="right" title="<? echo $val['weight']?>"><? echo number_format($val['qty'],2); ?></td> 
									  <td align="right"><? echo number_format(($val['qty']/$total_reporcess)*100,2); ?></td>  
								  	</tr>
									<? 		
									$tot_batch_qty+=$val['qty'];
								  	$k++;
								}
								
								?>
								</tbody>
								<tfoot>
									<tr> 
										<th align="right">Total </th>
										<th align="right"><b><? echo number_format($tot_batch_qty,2,'.','');?></b> </th>
										<th align="right"><? echo number_format(($tot_batch_qty/$total_reporcess*100),2,'.','').'%'; ?></th>
									</tr>
								</tfoot>
							</table>
	                    </td>
	                    <!-- Production Summary End-->

	                    <!-- Re Process Summary Start-->
	                 	<?
						if ($cbo_result_name==0 || $cbo_result_name==1) $result_name_cond_summary=" and f.result=1"; else $result_name_cond_summary=" ";

						$sql_dt="(select a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight,SUM(b.batch_qnty) AS batch_qnty,0 AS trim_batch_qnty, b.item_description, b.prod_id, b.width_dia_type, $grp_con, d.job_no_prefix_num, d.buyer_name,f.remarks, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,a.booking_no,a.booking_without_order from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g, pro_batch_create_mst a where f.batch_id=a.id and f.batch_id=b.mst_id and g.id=f.machine_id $companyCond   $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond   $result_name_cond $machine_cond $file_cond $ref_cond $cbo_prod_source_cond $cbo_prod_type_cond and a.entry_form=0  and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1) and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and f.result=1 and  f.status_active=1 and f.is_deleted=0 
						GROUP BY a.batch_no, a.batch_weight,a.id, a.color_id,a.total_trims_weight, a.extention_no, b.item_description, b.prod_id, b.width_dia_type,d.job_no_prefix_num, d.buyer_name, f.shift_name, f.production_date,f.process_end_date, f.end_hours, f.floor_id,f.remarks,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type,f.result,a.booking_no,a.booking_without_order)
						union
						(
						select a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight,0 AS batch_qnty,SUM(b.trims_wgt_qnty) AS trim_batch_qnty, b.item_description, null as prod_id, null as width_dia_type,null as po_number ,null as po_id,null as grouping, null as file_no, d.job_no_prefix_num, d.buyer_name,f.remarks, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,a.booking_no,a.booking_without_order from  pro_batch_create_mst a,pro_batch_trims_dtls b, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g where  f.batch_id=a.id and a.id=b.mst_id  and f.batch_id=b.mst_id and g.id=f.machine_id  and d.job_no=a.job_no  and a.entry_form=136  and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1)  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and f.result=1 and  f.status_active=1 and f.is_deleted=0 $companyCond   $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond   $result_name_cond $machine_cond $file_cond $ref_cond $cbo_prod_source_cond $cbo_prod_type_cond 
						GROUP BY a.batch_no, a.batch_weight,a.id, a.color_id,a.total_trims_weight, a.extention_no,d.job_no_prefix_num, d.buyer_name, f.shift_name, f.production_date,f.process_end_date, f.end_hours, f.floor_id,f.remarks,f.hour_unload_meter, b.item_description,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type,f.result,a.booking_no,a.booking_without_order
						)
						union
						(
						select a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight, SUM(b.batch_qnty) AS batch_qnty,0 AS trim_batch_qnty,b.item_description, b.prod_id, b.width_dia_type,null as po_number ,null as po_id,null as grouping, null as file_no, null as job_no_prefix_num,h.buyer_id as buyer_name,f.remarks,f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date,
						f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id, f.fabric_type,f.result,a.booking_no,a.booking_without_order from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h where h.booking_no=a.booking_no $companyCond $workingCompany_name_cond2 and f.batch_id=a.id  and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and f.result=1 and  f.status_active=1 and f.is_deleted=0  $dates_com  $batch_num   $buyerdata2 $result_name_cond  $machine_cond $cbo_prod_type_cond $cbo_prod_source_cond GROUP BY a.batch_no, a.batch_weight,a.id,h.buyer_id, a.color_id, a.extention_no, b.item_description, b.prod_id, b.width_dia_type,
						f.shift_name,a.total_trims_weight, f.production_date,f.process_end_date, f.end_hours, f.floor_id,f.hour_unload_meter, f.water_flow_meter,
						f.end_minutes, f.machine_id, f.load_unload_id,fabric_type,a.booking_without_order,a.booking_no, f.result,f.remarks
						)
						";
						// echo  $sql_dt;
						$tot_trim_batch_qnty=0;
						$sql_d=sql_select($sql_dt);$total_batch_qty2=0;$trims_total_batch_qty=0;$sum_trims_check_array=array();
						$buyer_trims_self_arr=array();$z=1;
						foreach($sql_d as $row)
						{
							  $buyer_trims_self_arr[$row[csf('buyer_name')]]['trims']+=$row[csf('total_trims_weight')];
							  $buyer_trims_self_arr[$row[csf('buyer_name')]]['qty']+=$row[csf('batch_qnty')];
							  if($row[csf('batch_qnty')]>0)
							  {
							 	// echo $row[csf('batch_qnty')].'ddd';
							  	$total_batch_qty2+=$row[csf('batch_qnty')];
							  }
							  if($row[csf('trim_batch_qnty')]>0)
							  {
							  $tot_trim_batch_qnty+=$row[csf('trim_batch_qnty')];
							  }
							  $batch_no=$row[csf('id')];
								if (!in_array($batch_no,$sum_trims_check_array))
								{ $z++;
									
									
									 $sum_trims_check_array[]=$batch_no;
									  $sumtot_trim_qty=$row[csf('total_trims_weight')];
								}
								else
								{
									 $sumtot_trim_qty=0;
								}
							   $trims_total_batch_qty+=$sumtot_trim_qty;
						}
						unset($sql_d);
							 
						$k=1;$total_batch_qty=0;$tot_batch_per=0;
						foreach($buyer_trims_self_arr as $key=>$val)
						{
							
							//$trims_qty=$party_batch_arr[$key]['trims_weight'];
							$trims_qty_sum=$val['trims'];
							
							    
							$k; 
							$buyer_arr[$key];
							number_format($val['qty']+$trims_qty_sum,2,'.',''); //$total_batch_qty+=$val['qty']+$trims_qty_sum; 
							$batch_per=(($val['qty']+$trims_qty_sum)/$total_batch_qty2)*100;  number_format($batch_per,2,'.','').'%'; 
							$tot_batch_per+=$batch_per;
							$k++;
						}
						?>
						<!-- kaiyum subcontact batch (shade match)-->
	                    <td valign="top">
	                        <table style="width:300px;border:1px solid #000;margin-left:40px;" align="center"  class="rpt_table" rules="all" >
	                           	<thead>
	                            	<tr>
	                               		<th colspan="5">Re Process Summary</th>
	                             	</tr>
	                             	<tr>
		                               <th>SL</th>
		                               <th>Buyer</th>
		                               <th>Batch Total</th>
		                               <th>%</th>
	                             	</tr>
	                           	</thead>
	                           	<tbody>
		                            <?
		                            $k=1;$total_batch_qty_reproc=0;$tot_batch_per_re=0;
		                            foreach($buyer_re_process_arr as $key=>$val)
									{
										$trims_qty_sum=$val['trims'];
										$total_batch_qty_reproc+=$val['re_qty']+$trims_qty_sum;
									}
									foreach($buyer_re_process_arr as $key=>$val)
									{
									
										if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										//$trims_qty=$party_batch_arr[$key]['trims_weight'];
										$trims_qty_sum=$val['trims']; //total_batch_qty_re
										$total_batch_qty_reprocess=$total_batch_qty_re_without+$total_batch_qty_re_dyeing;
										?>
			                            <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;">
			                               <td><? echo $k; ?></td>
			                               <td><? echo $buyer_arr[$key] ?></td>
			                               <td align="right"><? echo number_format($val['re_qty']+$trims_qty_sum,2,'.','');  ?></td>
			                               <td align="right"><? $batch_per=(($val['re_qty']+$trims_qty_sum)/$total_batch_qty_reproc)*100; echo number_format($batch_per,2,'.','').'%'; ?></td>
			                            </tr>
		                             	<?
										$tot_batch_per_re+=$batch_per;
										$k++;  
									}
									?>
	                           	</tbody>
	                           	<tfoot>
	                            	<tr>
		                               <th colspan="2" align="right">Total </th>
		                               <th align="left"><b><? echo number_format($total_batch_qty_reproc,2,'.','');?></b></th>
		                               <th align="right"><? echo number_format($tot_batch_per_re,2,'.','').'%'; ?></th>
	                            	</tr>
	                           </tfoot>
	                        </table>
	                    </td>
	                    <!-- Re Process Summary End-->

	                    <!-- Summary Total(Shade Match) Start-->
					 	<?
					 	// echo "string".$cbo_type;die;
					 	
						if($subcn_batch_ids!='')
						{
							$sql_sub="select d.party_id, SUM(b.batch_qnty) AS sub_batch_qnty from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where f.batch_id=a.id $companyCond $workingCompany_name_cond2 and  a.entry_form=36 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=38 and f.batch_id=b.mst_id and f.load_unload_id=2 and f.result=1  and a.batch_against in(1) and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  $dates_com $sub_job_cond $batch_num $sub_buyer_cond $suborder_no $year_cond $machine_cond  $result_name_cond_summary $cbo_prod_type_cond $cbo_prod_source_cond GROUP BY d.party_id ";
						   $sql_data_sub=sql_select($sql_sub);
						}
						
						$sub_party_batch_arr=array();$tot_sub_batch_qty=0; 
						foreach($sql_data_sub as $row)
						{
							$sub_party_batch_arr[$row[csf('party_id')]]['qty']=$row[csf('sub_batch_qnty')];
							$sub_party_batch_arr[$row[csf('party_id')]]['buyer']=$row[csf('party_id')];
							$tot_sub_batch_qty+=$row[csf('sub_batch_qnty')];
						}
						$p=1;$total_sub_batch_qty=0;$total_batch_per_sub=0;
						foreach($sub_party_batch_arr as $id=>$sval)
						{
							$p;
							$buyer_arr[$id];
							number_format($sub_party_batch_arr[$id]['qty'],2,'.',''); $total_sub_batch_qty+=$sub_party_batch_arr[$id]['qty'];
							$batch_per_sub=($sub_party_batch_arr[$id]['qty']/$tot_sub_batch_qty)*100; number_format($batch_per_sub,2,'.','').'%';
							$total_batch_per_sub+=$batch_per_sub;
							$p++;
						}
					 	// for sample
						if ($cbo_result_name==0 || $cbo_result_name==1) $result_name_cond_summary=" and f.result=1"; else $result_name_cond_summary=" ";
							
						$sql_sam="(select d.buyer_name,a.batch_no,a.total_trims_weight as trims_weight,SUM(b.batch_qnty) AS batch_qnty  from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where f.batch_id=a.id $companyCond $workingCompany_name_cond2 and   a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and f.result=1 and a.batch_against in(3) and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0 $sam_batch_cond_for_in $dates_com $jobdata $batch_num $buyerdata $order_no $file_cond $ref_cond $year_cond  $machine_cond  $result_name_cond_summary $cbo_prod_type_cond $cbo_prod_source_cond group by d.buyer_name,a.batch_no,a.total_trims_weight )
						union
						(
						select h.buyer_id as buyer_name,a.batch_no,a.total_trims_weight as trims_weight,SUM(b.batch_qnty) AS batch_qnty from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h where f.batch_id=a.id $companyCond $workingCompany_name_cond2  and h.booking_no=a.booking_no  and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and f.result=1 and a.batch_against in(3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  f.status_active=1 and f.is_deleted=0 $sam_batch_cond_for_in $dates_com  $batch_num  $year_cond    $result_name_cond_summary  $machine_cond $cbo_prod_type_cond $buyerdata2 $cbo_prod_source_cond
						GROUP BY h.buyer_id,a.batch_no,a.total_trims_weight)
						union
						(
						select d.buyer_name,a.batch_no,SUM(b.trims_wgt_qnty) AS trims_weight, 0 as batch_qnty from pro_batch_create_mst a,pro_batch_trims_dtls b, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g where a.id=b.mst_id and f.batch_id=a.id and f.batch_id=b.mst_id and a.entry_form=136 and g.id=f.machine_id and  f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(3) and d.job_no=a.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  $companyCond  $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond   $result_name_cond  $machine_cond   $cbo_prod_source_cond $cbo_prod_type_cond  GROUP BY d.buyer_name,a.batch_no )
						";
						// echo $sql_sam;
						$sql_data_sam=sql_select($sql_sam);
								
						$samp_party_batch_arr=array(); $tot_batch_qty_sam=0;$tot_batch_trims_qty_sam=0;$tot_batch_qty_sam_summary=0;
						foreach($sql_data_sam as $row)
						{
							$samp_party_batch_arr[$row[csf('buyer_name')]]['qty']+=$row[csf('batch_qnty')];
							$samp_party_batch_arr[$row[csf('buyer_name')]]['trims_qty']+=$row[csf('trims_weight')];
							$samp_party_batch_arr[$row[csf('buyer_name')]]['buyer']=$row[csf('buyer_name')];
							$tot_batch_qty_sam+=$row[csf('batch_qnty')]+$row[csf('trims_weight')];
							$tot_batch_qty_sam_summary+=$row[csf('trims_weight')];
							//$tot_batch_trims_qty_sam+=$row[csf('trims_weight')];
						}
						$k=1;$total_batch_qty_sam=0;$tot_batch_per_sam=0;
						foreach($samp_party_batch_arr as $key=>$val)
						{
							$smp_trims=$samp_party_batch_arr[$key]['trims_qty'];
							$k;
							$buyer_arr[$key]; 
							number_format($samp_party_batch_arr[$key]['qty']+$smp_trims,2,'.',''); $total_batch_qty_sam+=$samp_party_batch_arr[$key]['qty'];//+$smp_trims;
							$batch_per_sam=(($samp_party_batch_arr[$key]['qty']+$smp_trims)/$tot_batch_qty_sam)*100; number_format($batch_per_sam,2,'.','').'%';
						
							$tot_batch_per_sam+=$batch_per_sam;
							$k++;
						}
						
						if ($cbo_result_name==0 || $cbo_result_name==1) $result_name_cond_summary=" and f.result=1"; else $result_name_cond_summary=" ";
							if($batch_idss!='')
						{
						  	$sql_result_re="select d.buyer_name,SUM(b.batch_qnty) AS batch_qnty,a.total_trims_weight from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where f.batch_id=a.id $companyCond $workingCompany_name_cond2 and  a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2  and a.batch_against in(2) and f.result in(1)  and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  f.status_active=1 and f.is_deleted=0 $batch_cond_for_in $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond  $machine_cond  $result_name_cond_summary $cbo_prod_type_cond  $cbo_prod_source_cond GROUP BY d.buyer_name ,a.total_trims_weight ";
							$sql_re=sql_select($sql_result_re);$total_batch_qty_re=0;
								
							$buyer_re_process_arr=array();
							foreach($sql_re as $row)
							{
								$buyer_re_process_arr[$row[csf('buyer_name')]]['re_qty']+=$row[csf('batch_qnty')];
								$buyer_re_process_arr[$row[csf('buyer_name')]]['trims']+=$row[csf('total_trims_weight')];
								$total_batch_qty_re+=$row[csf('batch_qnty')]+$row[csf('total_trims_weight')];
							}
							unset($sql_re);
						}
						?>
						<td valign="top">
							<table cellpadding="0"  width="300" cellspacing="0" align="center"  class="rpt_table" rules="all" border="1" style="margin-left:40px;">
								<thead>
									<tr>
										<th colspan="6">Summary Total(Shade Match)</th>
									</tr>
									<tr>
										<th>Self Batch</th>
										<th>Sample Batch</th>
	                                    <th>Trims Weight</th>
	                                    <th>SubCon Batch</th>
										<th>Total</th>
									</tr>
								</thead>
								<tbody>
									<tr bgcolor="#FFFFFF"  style="cursor:pointer;">
										<td width="30"><? echo number_format($total_batch_qty2,2); ?></td>
										<td width="30"><? echo number_format($total_batch_qty_sam,2); ?></td>

										<!-- trims weight --> 
										<td width="30" title="trims weight:<? echo $tot_batch_qty_sam_summary; ?>"><? 
										$totaltrimswt=($trims_total_batch_qty+$tot_batch_qty_sam_summary+$tot_trim_batch_qnty);
										echo number_format($totaltrimswt,2);

										?></td>

										<td width="30"><? echo number_format($total_sub_batch_qty,2); ?></td>
										<td width="30"><?  echo number_format($grand_total=$total_batch_qty2+$total_sub_batch_qty+$total_batch_qty_sam+$totaltrimswt,2); ?></td>
								  	</tr>
									<tr bgcolor="#E9F3FF"> 
	  								   	<td><?  echo number_format(($total_batch_qty2/$grand_total)*100,2).'%'; ?></td>
	                                    <td><?  echo number_format(($total_batch_qty_sam/$grand_total)*100,2).'%'; ?></td>
	                                     
	                                     <!-- trims weight %--> 
	                                    <td title="<? echo $totaltrimswt.'/'.$grand_total; ?>"><?  echo number_format(($totaltrimswt/$grand_total)*100,2).'%';
	                                    // echo number_format(($trims_total_batch_qty/$grand_total)*100,2).'%'; ?></td>
	                                   
										<td><?  echo number_format(($total_sub_batch_qty/$grand_total)*100,2).'%'; ?></td>
										<td><?  echo '100%'; ?></td>
									</tr>
								</tbody>
							</table>
					   	</td>
	                   	<!-- Summary Total(Shade Match) End -->
	                   	<td valign="top">
							 
					   	</td>
	                   	<!-- END Total Dyeing Production Summary -->
				  	</tr>
				</table>
			 	</div>
			 	<br />
			 	<? 
			}
		}
		// echo "All Summary End";die;

		// ============Details Part data show Start================
		if (count($batchdata)>0)
		{
			$group_by=str_replace("'",'',$cbo_group_by);

			?>
			<div align="left" style="float:left; clear:both;">
         	
			<table class="rpt_table" width="1420" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
             	<caption> <b>Batch Detail  </b></caption>
                <thead>
                    <tr>
                        <th width="30">SL</th>
                       <? if($group_by==3 || $group_by==0){ ?>
                    	 <th width="80">M/C No</th>
                   		 <? } ?>
                       
                        <th width="60">File No</th>
                        <th width="70">Ref. No</th>
                        <th width="100">Buyer</th>
                       
                        <th width="100">Fabrics Desc</th>
                        <th width="70">Dia/Width Type</th>
                        <th width="80">Color Name</th>
                        <th width="90">Batch No</th>
                        <th width="40">Extn. No</th>
                        <th width="70">Dyeing Qty.</th>
                        <th width="70">Trims Wgt.</th>
                       
                        <th width="75">Load Date & Time</th>
                        <th width="75">UnLoad Date Time</th>
                        <th width="60">Time Used</th>
                        <th width="100">Dyeing Fab. Type</th>
                        <th width="100">Result</th>
                        <th width="">Remark</th>
                    </tr>
                </thead>
			</table>
			<div style=" max-height:350px; width:1440px; overflow-y:scroll; float:left; clear:both;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1420" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                <tbody>
	                <? 
					$tot_sum_trims_qnty=0;
	                $i=1; $btq=0; $k=1;$z=1;$total_water_cons_load=0;$total_water_cons_unload=0;
	                $batch_chk_arr=array(); $group_by_arr=array();$tot_trims_qnty=0;$trims_check_array=array();
	                // echo "<pre>";print_r($batchdata);die;
	                foreach($batchdata as $batch)
					{ 
						if ($i%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
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
	                                    <td width="130" colspan="6" style="text-align:right;"><strong>Sub. Total : </strong></td>
	                                    <td width="70"><? //echo number_format($batch_qnty,2); ?></td>
	                                    <td width="40"><? //echo number_format($batch_qnty_trims,2); ?></td>
										
										<td width="70"><? echo number_format($batch_qnty,2); ?></td>
	                                    <td width="70"><? echo number_format($batch_qnty_trims,2); ?></td>
	                                    <td width="75" colspan="6">&nbsp;</td>
	                                    
	                                </tr>                                
									<?
									unset($batch_qnty);unset($batch_qnty_trims);
								}
								?>
								<tr bgcolor="#EFEFEF">
									<td colspan="18"><b style="margin-left:0px"><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
								</tr>
								<?
								$group_by_arr[]=$group_value;            
								$k++;
							}					
						}

						$order_id=$batch[csf('po_id')];
						
						$entry_form=$batch[csf('entry_form')];
						$load_id=$batch[csf('load_unload_id')];
						$batch_weight=$batch[csf('batch_weight')];
						$water_cons_unload=$batch[csf('water_flow_meter')];
						$water_cons_load=$water_flow_arr[$batch[csf('id')]];
						$load_hour_meter=$load_hour_meter_arr[$batch[csf('id')]];
						//echo $water_cons_load.'=='.$water_cons_unload;
						$water_cons_diff=($water_cons_unload-$water_cons_load)/$batch_weight*1000;
						
						$desc=explode(",",$batch[csf('item_description')]); 
						$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
						$file_no=implode(",",array_unique(explode(",",$batch[csf('file_no')]))); 
						$ref_no=implode(",",array_unique(explode(",",$batch[csf('grouping')]))); 

						$batch_no=$batch[csf('id')];
						if (!in_array($batch_no,$trims_check_array))
						{ 
							$z++;
							$trims_check_array[]=$batch_no;
							$tot_trim_qty=$batch[csf('total_trims_weight')];
						}
						else
						{
							$tot_trim_qty=0;
						}
						$batch_against=$batch[csf('batch_against')];
						if($batch_against==2) $bg_color_td="bolder";else $bg_color_td="";
						if($entry_form==136)
						{
							$tot_trim_qty=$tot_trim_qty+$batch[csf('batch_qnty')];
						}
						else $tot_trim_qty=$tot_trim_qty;
						?>
						<tr style="font-weight:<? echo $bg_color_td; ?>"   bgcolor="<? echo $bgcolor_dyeing; ?>"  id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor_dyeing; ?>')">
	                        <td  width="30"><? echo $i; ?></td>
	                        
	                         <? if($group_by==3 || $group_by==0){ ?>
	                        <td align="center" width="80"><p><? echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
	                        <?
							 }
							 if($group_by==2 || $group_by==0){ ?>
	                       
	                        <? } if($group_by==1 || $group_by==0){ ?>
	                    
	                        <? } ?>
	                        <td align="center" width="60"><p><? echo $file_no; ?></p></td>
	                        <td align="center" width="70"><p><? echo $ref_no; ?></p></td>
	                        <td width="100"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
	                        
	                        <td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $batch[csf('item_description')]; ?></div></td>
	                        <td width="70"><p><? echo $fabric_typee[$batch[csf('width_dia_type')]]; ?></p></td>
	                        <td width="80"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
	                        <td width="90"><p><? echo $batch[csf('batch_no')]; ?></p></td>
	                        <td width="40"><p><? echo $batch[csf('extention_no')]; ?></p></td>
	                        <td title="<?  if($batch_against==3) echo "Sample";?>" align="right" width="70"><? 
							if($entry_form!=136) 
							{
								$batch_qty=$batch[csf('batch_qnty')];
							}
							else {
								$batch_qty=0;
							}
							echo number_format($batch_qty,2); ?></td>
	                       	<td align="right" width="70"><? echo number_format($tot_trim_qty,2);  ?></td>
	                        <td width="75"><p><? $load_t=$load_hr[$batch[csf('id')]].':'.$load_min[$batch[csf('id')]]; echo  ($load_date[$batch[csf('id')]] == '0000-00-00' || $load_date[$batch[csf('id')]] == '' ? '' : change_date_format($load_date[$batch[csf('id')]])).' <br> '.$load_t;
							//echo $batch[csf('id')];
	                        ?></p></td>
	                        <td width="75"><p><? $hr=strtotime($unload_date,$load_t); $min=($batch[csf('end_minutes')])-($load_min[$batch[csf('id')]]);
							if($load_id==2) echo  ($batch[csf('process_end_date')] == '0000-00-00' || $batch[csf('process_end_date')] == '' ? '' : change_date_format($batch[csf('process_end_date')])).'<br>'.$unload_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];
							$unloaded_date=change_date_format($batch[csf('process_end_date')]);
							 //$unloadd=change_date_format($batch[csf('process_end_date')]).'<br>'.$unload_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];?></p></td>
	                        <td align="center" width="60">
								<?  
	                            $new_date_time_unload=($unloaded_date.' '.$unload_time.':'.'00');
	                            $new_date_time_load=($load_date[$batch[csf('id')]].' '.$load_t.':'.'00');
	                            $total_time=datediff(n,$new_date_time_load,$new_date_time_unload);
								//echo $new_date_time_unload.'=='.$new_date_time_load;
	                            if($load_id==2) echo floor($total_time/60).":".$total_time%60;
	                            //echo ($total_time/60 - $total_time%3600/60)/60 .':'.$total_time%3600/60;
	                            ?>
	                        </td>
	                        <td align="center" width="100" title="fabric_type:<? echo $batch[csf('fabric_type')]; ?>"><p><? 
	                        $fab_type="";
							if($batch[csf('fabric_type')]==1)
							{
								$fab_type="Cotton";
							}
							else if($batch[csf('fabric_type')]==2)
							{
								$fab_type="Polyster";
							}
							else if($batch[csf('fabric_type')]==3)
							{
								$fab_type="Lycra";
							}
							else if($batch[csf('fabric_type')]==4)
							{
								$fab_type="Both Part";
							}
							else if($batch[csf('fabric_type')]==5)
							{

								$fab_type="White";
							}
							else if($batch[csf('fabric_type')]==6)
							{
								$fab_type="Wash";
							}
							echo $fab_type;//$fabric_type_for_dyeing[$batch[csf('fabric_type')]]; ?></p> </td>
	                        <td align="center" width="100"><p><? echo $dyeing_result[$batch[csf('result')]]; ?></p> </td>
	                         <td align="center"><p><? echo $batch[csf('remarks')]; ?></p> </td>
						</tr>
						<? 
						if($batch[csf('result')]==1)
						{
							$tot_sum_trims_qnty+=$tot_trim_qty;
						}
						else 
						{ 
							$tot_trims_qnty+=0;
						}
						$i++;
						
						$batch_qnty+=$batch_qty;
						$batch_qnty_trims+=$tot_trim_qty;
						$total_water_cons_load+=$water_cons_load;
						$total_water_cons_unload+=$water_cons_unload;
						$tot_trims_qnty+=$tot_trim_qty;
						$trims_summary+=$tot_trim_qty;
						$grand_total_batch_qty+=$batch_qty;
					} //batchdata froeach
					if($group_by!=0)
					{
						?>
	                 	<tr class="tbl_bottom">
	                    <td width="30">&nbsp;</td>
	                    <? if($group_by==3 || $group_by==0){ ?>
	                    <td width="80">&nbsp;</td> 
	                     <? } ?>
	                    <td width="130" colspan="8" style="text-align:right;"><strong>Sub. Total : </strong></td>
	                    <td width="70"><? echo number_format($batch_qnty,2); ?></td>
	                    <td width="40"><? echo number_format($batch_qnty_trims,2); ?></td>
						<td width="70"><? //echo number_format($batch_qnty,2); ?></td>
	                    <td width="70"><? //echo number_format($batch_qnty_trims,2); ?></td>
	                    <td width="75" colspan="4">&nbsp;</td>
	                	</tr> 
						<? 
					} ?>            
				</tbody>
            	<tfoot>
	                <tr>
	                    <th width="30">&nbsp;</th>
	                    <? if($group_by==3 || $group_by==0){ ?>

	                    <th width="80">&nbsp;</th>
	                    
	                    <? } ?>
	                   
	                    <th width="130" colspan="8" style="text-align:right;"><strong>Trims Total : </strong></th>
	                    <th width="70"></th>
	                    <th width="70"><? echo number_format($tot_trims_qnty,2); ?></th>
	                    <th colspan="6">  </th>
	                   
	                </tr>
	                
	                <tr>
	                    <th width="30">&nbsp;</th>
	                    <? if($group_by==3 || $group_by==0){ ?>
	                    <th width="80">&nbsp;</th>
	                    <? } ?>
	                    
	                    <th width="130" colspan="8" style="text-align:right;"><strong>Grand Total : </strong></th>
	                    <th width="70"><? echo number_format($grand_total_batch_qty+$tot_trims_qnty,2); ?></th>
	                   
	                    <th colspan="7">&nbsp;  </th>
	                   
	                </tr>
                </tfoot>
        	</table>
			</div>
			</div>
			<? 
		}
		// ============Details Part data show End==================
		?>
		</fieldset>
       
		<?
		//======================================end===========================
	} //Dyeing Production End
	else if($cbo_type==3) // Daily Right First Time Show data
	{
	
		?>
		<div>
		<fieldset style="width:1505px;">
		<div align="center"><strong> <? echo $company_library[$company]; ?> </strong><br> <strong> Daily Right First Time </strong><br>
		<?
			echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
		?>
		</div>
	    <div>
	    <table cellpadding="0"  width="400" cellspacing="0" align="center" >
	        <tr>
	                 <td width="400">
	                     <table cellpadding="0"  width="400" cellspacing="0" align="center"  class="rpt_table" rules="all" border="1">
	                            <thead>
	                        	<tr>
	                            	<th colspan="4"> <p>&nbsp;</p>
	                           	    <p>&nbsp;</p>
	                           	    <p>&nbsp;</p>
	                           	    <p>&nbsp;</p>
	                           	    <p>&nbsp;</p>
	                           	    <p>&nbsp;</p>
	                           	    <p>&nbsp;</p>
	                           	    <p>&nbsp;</p>
									<p>RFT Summary </p>
									</th>
	                            </tr>
	                             <tr><th colspan="3">RFT </th> <th>OK %</th></tr>
	                            </thead>
	                            <?
								
					
						$tot_ltb=$tot_ltb_matched+$tot_ltb_not_matched;
						$ltb_percent=$matched_ltb/$tot_ltb*100;
						$tot_btb=$tot_btb_matched+$tot_btb_not_matched;
						$btb_percent=$tot_btb_matched/$tot_btb*100;
						$tot_rp=$tot_btb_matched+$tot_btb_not_matched+$tot_ltb_matched+$tot_ltb_not_matched;
						$tot_ok=$tot_btb_matched+$tot_ltb_matched;
						$tot_rp_percent=$tot_ok/$tot_rp*100;
						?>
	                            <tr bgcolor="#E9F3FF">
	                            	<td rowspan="2">LTB </td>
	                                <td width="100">L OK </td>
	                                <td align="right"> <? echo number_format($tot_ltb_matched); ?></td>
	                                <td rowspan="2" align="right"><? echo number_format($ltb_percent,2).'%'; ?> </td>
	                            </tr>
	                            <tr>
	                                <td width="100">L Not OK </td>
	                                <td width="60" align="right"><? echo number_format($tot_ltb_not_matched); ?></td>
	                            </tr>
	                             <tr bgcolor="#FFFFFF">
	                            	<td rowspan="2">BTB </td>
	                                <td width="100">B OK </td>
	                                <td align="right"> <? echo $tot_btb_matched; ?></td>
	                                <td rowspan="2" align="right"><? echo number_format($btb_percent,2).'%'  ?></td>
	                            </tr>
	                            <tr>
	                                <td width="100">B Not OK </td>
	                                <td width="60" align="right"><? echo number_format($tot_btb_not_matched); ?></td>
	                            </tr>
	                             <tfoot>
	                             <tr>
	                            	<th> </th>
	                                <th colspan="2">R/P</th>
	                                <th align="right"> <? echo number_format($tot_rp_percent,2).'%'; ?></th>
	                             </tr>
	                            </tfoot>
	                            </table>
	                     </td>
	                     </tr>  
	                    </table>
	    </div>
	    <br>
	    <div>
	    <div align="left"> <b>Self batch </b><br>
	    <Strong>Bulk To Bulk</Strong>
	    </div>
		<table class="rpt_table" width="1510" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="80">Prod. Date</th>
					<th width="80">M/C No</th>
					<th width="100">Buyer</th>
					<th width="80">Job</th>
					<th width="90">PO No</th>
					<th width="100">Fabrics Desc</th>
					<th width="80">Dia/Width Type</th>
					<th width="80">Color Name</th>
					<th width="90">Batch No</th>
					<th width="70">Dyeing Qty.</th>
					<th width="50">LTB/BTB</th>
					<th width="100">Unload Date & Time</th>
					<th width="60">Time Used</th>
					<th width="100">RFT</th>
					<th width="100">Remark</th>
					<th width="100">Machine Utilization</th>
					<th>Lot No</th>
				</tr>
			</thead>
		</table>
		<div style=" max-height:350px; width:1510px; overflow-y:scroll;" id="scroll_body">
		<table class="rpt_table" id="table_body" width="1490" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
		<tbody>
			<? 
			$i=1;
			$f=0;
			$btq=0;
			$batch_chk_arr=array();
			if (count($batchdata)>0)
			{
				foreach($batchdata as $batch)
				{ 
					if ($i%2==0)  
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					$order_id=$batch[csf('po_id')];
					$color_id=$batch[csf('color_id')];
					if($batch[csf('result')]==1)
					{
					$shade="Shade Matched";	
					}
					else
					{
					$shade="Shade Not Matched";		
					}
					$desc=explode(",",$batch[csf('item_description')]); 
					$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')]))); 
					?>	
					<tr bgcolor="<? echo $bgcolor; ?>"  id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
						<td width="30"><? echo $i; ?></td>
						<td width="80" title="<? echo change_date_format($batch[csf('production_date')]); ?>"><p><? echo change_date_format($batch[csf('production_date')]); $unload_date=$batch[csf('process_end_date')]; ?></p></td>
						<td  align="center" width="80" title="<? echo $machine_arr[$batch[csf('machine_id')]]; ?>"><p><? echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
						<td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
						<td  width="80" title="<? echo $batch[csf('job_no_prefix_num')]; ?>"><p><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
						<td width="90" title="<? echo $po_number; ?>"><p><? echo $po_number; ?></p></td>
						<td  width="100" title="<? echo $batch[csf('item_description')];?>"><p><? echo $batch[csf('item_description')]; ?></p></td>
						<td  width="80" title="<? echo $fabric_typee[$batch[csf('width_dia_type')]]; ?>"><p><? echo $fabric_typee[$batch[csf('width_dia_type')]]; ?></p></td>
						<td  width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
						<td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
						<td  align="right" width="70" title="<? echo $batch[csf('batch_qnty')]; ?>"><p><? echo $batch[csf('batch_qnty')]; ?></p></td>
						<td align="center" width="50" title="<? echo $ltb_btb[$batch[csf('ltb_btb_id')]]; ?>"><? echo $ltb_btb[$batch[csf('ltb_btb_id')]]; ?></td>
						<td width="100" align="center" title="<? //echo $load_hr[$batch[csf('id')]].':'.$load_min[$batch[csf('id')]]; ?>"><p><?  echo ($batch[csf('process_end_date')] == '0000-00-00' || $batch[csf('process_end_date')]== '' ? '' : change_date_format($batch[csf('process_end_date')])).' <br> '.$batch[csf('end_hours')].':'.$batch[csf('end_minutes')]; ?></p></td>
						  <td align="center" width="60"><? $hr=$batch[csf('end_hours')]-$load_hr[$batch[csf('id')]]; $min=$batch[csf('end_minutes')]-$load_min[$batch[csf('id')]]; //echo  $hr.':'.$min;
						$load_t=$load_hr[$batch[csf('id')]].':'.$load_min[$batch[csf('id')]];
						$unload_t=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];
						$new_date_time_unload=($unload_date.' '.$unload_t.':'.'00');
						$new_date_time_load=($load_date[$batch[csf('id')]].' '.$load_t.':'.'00');
					   //$unload_time=strtotime($unload_date,$unload_time);
						//$load_time=strtotime($load_date,$load_t);
						$total_time=datediff(n,$new_date_time_load,$new_date_time_unload);
						echo floor($total_time/60).":".$total_time%60;
						//echo ($total_time/60 - $total_time%3600/60)/60 .':'.$total_time%3600/60;
							?></td>
						<td width="100" title="<? echo $shade;  ?>"><p><?  echo $shade; ?></p></td>
						<td width="100" title="<? ?>"><p><? echo  $batch[csf('remarks')]; ?></p></td>
					  
						<td align="center" width="100" title="<?  $utiliz=$batch[csf('batch_qnty')]/$m_capacity[$batch[csf('machine_id')]]*100; echo number_format($utiliz,2).'%'; ?>"><?  $utiliz=$batch[csf('batch_qnty')]/$m_capacity[$batch[csf('machine_id')]]*100; echo number_format($utiliz,2).'%'; ?> </td>
						<td align="left" title="<? echo  $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]];   ?>"><p><? echo  $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?></p> </td>
					</tr>
					<? 
					$i++;
					$btq+=$batch[csf('batch_qnty')];
				} //batchdata froeach
	 			?>
				<tr bgcolor="#CCCCCC">
					<td colspan="10" align="right"><Strong>BTB Total:</Strong> <? //echo $b_qty; ?> </td>
					<td align="right"><? echo number_format($btq,2); ?>&nbsp;</td>
					<td colspan="7">&nbsp;</td>
				</tr>
			 	<tr bgcolor="#C2DCFF">
					<td colspan="10"><strong>Lab To Bulk</strong></td><td colspan="8"> </td>
			 	</tr>
		 		<?
			}
			?>
	        <?
			if (count($batchdata_ltb)>0)
			{
				$d=1;
				foreach($batchdata_ltb as $batch_ltb)
				{ 
					if ($d%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$order_id=$batch_ltb[csf('po_id')];
					$color_id=$batch_ltb[csf('color_id')];
					if($batch_ltb[csf('result')]==1)
					{
						$shade="Shade Matched";	
					}
					else
					{
						$shade="Shade Not Matched";
					}
					$desc=explode(",",$batch_ltb[csf('item_description')]); 
					$po_number=implode(",",array_unique(explode(",",$batch_ltb[csf('po_number')]))); 
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" id="trbluk_<? echo $d; ?>"  onclick="change_color('trbluk_<? echo $d; ?>','<? echo $bgcolor; ?>')">
						<td width="30"><? echo $d; ?></td>
						<td width="80" title="<? echo change_date_format($batch_ltb[csf('production_date')]); ?>"><p><? echo change_date_format($batch_ltb[csf('production_date')]);$unload_date1=$batch_ltb[csf('process_end_date')]; ?></p></td>
						<td  align="center" width="80" title="<? echo $machine_arr[$batch_ltb[csf('machine_id')]]; ?>"><p><? echo $machine_arr[$batch_ltb[csf('machine_id')]]; ?></p></td>
						<td  width="100" title="<? echo $buyer_arr[$batch_ltb[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch_ltb[csf('buyer_name')]]; ?></p></td>
						<td  width="80" title="<? echo $batch[csf('job_no_prefix_num')]; ?>"><p><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
						<td width="90" title="<? echo $po_number; ?>"><p><? echo $po_number; ?></p></td>
						<td  width="100" title="<? echo $batch[csf('item_description')];?>"><p><? echo $batch[csf('item_description')]; ?></p></td>
						<td  width="80" title="<? echo $fabric_typee[$batch[csf('width_dia_type')]]; ?>"><p><? echo $fabric_typee[$batch[csf('width_dia_type')]]; ?></p></td>
						<td  width="80" title="<? echo $color_library[$batch_ltb[csf('color_id')]]; ?>"><p><? echo $color_library[$batch_ltb[csf('color_id')]]; ?></p></td>
						<td  align="center" width="90" title="<? echo $batch_ltb[csf('batch_no')]; ?>"><p><? echo $batch_ltb[csf('batch_no')]; ?></p></td>
						<td  align="right" width="70" title="<? echo $batch_ltb[csf('batch_qnty')]; ?>"><p><? echo $batch_ltb[csf('batch_qnty')]; ?></p></td>
						<td align="center" width="50" title="<? echo $ltb_btb[$batch_ltb[csf('ltb_btb_id')]]; ?>"><? echo $ltb_btb[$batch_ltb[csf('ltb_btb_id')]]; ?></td>
						<td width="100" align="center" title="<? //echo $load_hr[$batch[csf('id')]].':'.$load_min[$batch[csf('id')]]; ?>"><p><?   echo ($batch_ltb[csf('process_end_date')] == '0000-00-00' || $batch_ltb[csf('process_end_date')]== '' ? '' : change_date_format($batch_ltb[csf('process_end_date')])).'<br>'.$batch_ltb[csf('end_hours')].':'.$batch_ltb[csf('end_minutes')]; ?></p></td>
						   <td align="center" width="60" title="<? echo  $hr.':'.$min;  ?>"><? $hr=$batch_ltb[csf('end_hours')]-$load_hr[$batch_ltb[csf('id')]]; $min=$batch_ltb[csf('end_minutes')]-$load_min[$batch_ltb[csf('id')]]; //echo  $hr.':'.$min; 
						$load_time=$load_hr[$batch_ltb[csf('id')]].':'.$load_min[$batch_ltb[csf('id')]];
						$unload_time=$batch_ltb[csf('end_hours')].':'.$batch_ltb[csf('end_minutes')];
						$new_date_time_unload1=($unload_date1.' '.$unload_time.':'.'00');
						$new_date_time_load1=($load_date[$batch_ltb[csf('id')]].' '.$load_time.':'.'00');
						$total_time1=datediff(n,$new_date_time_load1,$new_date_time_unload1);
						echo floor($total_time1/60).":".$total_time1%60;
						//echo ($total_time/60 - $total_time%3600/60)/60 .':'.$total_time%3600/60;
						?></td>
						<td width="100" title="<? echo $shade ?>"><p><?  echo $shade; ?></p></td>
						<td width="100" title="<? ?>"><p><? echo  $batch_ltb[csf('remarks')];  ?></p></td>
						<td align="center" width="100" title="<? //echo $hr.':'.$min;   ?>"><?  $utiliz=$batch_ltb[csf('batch_qnty')]/$m_capacity[$batch_ltb[csf('machine_id')]]*100; 		
						echo number_format($utiliz,2).'%'; ?> </td>
						<td align="left" title="<? echo  $yarn_lot_arr[$batch_ltb[csf('prod_id')]][$batch_ltb[csf('po_id')]];   ?>"><p><? echo  $yarn_lot_arr[$batch_ltb[csf('prod_id')]][$batch_ltb[csf('po_id')]]; ?></p> </td>
					</tr>
					<?			
					$d++;
					$total_ltb+=$batch_ltb[csf('batch_qnty')];
				}
				?>
	            </table>
				<table class="rpt_table" width="1490" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
				<tfoot>
					<tr>
						<th width="30">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="70"><? echo number_format($total_ltb,2); ?></th>
						<th width="50">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th>&nbsp;</th>
					</tr>
				</tfoot>
				</table>
				<?
			}
			?>    
		</tbody>
		</table>
		</div>
	    </div>
	    <br>
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

if($action=="generate_report_machine_wise_akh2") //AKH 2
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if($db_type==0) $year_field_by="and YEAR(a.insert_date)"; 
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	if($db_type==0) $field_concat="concat(machine_no,'-',brand) as machine_name"; 
	else if($db_type==2) $field_concat="machine_no || '-' || brand as machine_name";
	// machine_no || '-' || brand as machine_name
	$cbo_prod_type = str_replace("'","",$cbo_prod_type);
	$cbo_party_id = str_replace("'","",$cbo_party_id);

	$company = str_replace("'","",$cbo_company_name);
	$working_company = str_replace("'","",$cbo_working_company_name);

	$buyer = str_replace("'","",$cbo_buyer_name);
	$date_search_type = str_replace("'","",$search_type);
	$job_number = str_replace("'","",$job_number);
	$job_number_id = str_replace("'","",$job_number_show);
	$batch_no = str_replace("'","",$batch_number_show);
	
	$machine=str_replace("'","",$txt_machine_id);

	//echo $company;die;
	$batch_number_hidden = str_replace("'","",$batch_number);
	$ext_num = str_replace("'","",$txt_ext_no);
	$hidden_ext = str_replace("'","",$hidden_ext_no);
	$txt_order = str_replace("'","",$order_no);
	$file_no = str_replace("'","",$file_no);
	$ref_no = str_replace("'","",$ref_no);
	$hidden_order = str_replace("'","",$hidden_order_no);
	$cbo_type = str_replace("'","",$cbo_type);
	$cbo_result_name = str_replace("'","",$cbo_result_name);
	$year = str_replace("'","",$cbo_year);
	//echo $cbo_type;die;
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);

	if ($cbo_prod_type>0 && $cbo_party_id>0) $cbo_prod_type_cond="and f.service_company in($cbo_party_id) "; else $cbo_prod_type_cond="";
	if ($cbo_prod_type>0) $cbo_prod_source_cond="and f.service_source in($cbo_prod_type) "; else $cbo_prod_source_cond="";

	if ($buyer==0) $sub_buyer_cond=""; else $sub_buyer_cond="  and d.party_id='".$buyer."' ";
	if ($cbo_result_name==0) $result_name_cond=""; else $result_name_cond="  and f.result='".$cbo_result_name."' ";
	if ($machine=="") $machine_cond=""; else $machine_cond =" and f.machine_id in ( $machine ) ";
	
	if ($buyer==0) $buyerdata=""; else $buyerdata="  and d.buyer_name='".$buyer."' ";
	if ($buyer==0) $buyerdata2=""; else $buyerdata2="  and h.buyer_id='".$buyer."' ";
	if ($batch_no=="") $batch_num=""; else $batch_num="  and a.batch_no='".trim($batch_no)."' ";
	
	if ($batch_no=="") $unload_batch_cond=""; else $unload_batch_cond="  and batch_no='".trim($batch_no)."' ";
	if ($batch_no=="") $unload_batch_cond2=""; else $unload_batch_cond2="  and f.batch_no='".trim($batch_no)."' ";
	if ($company==0) $companyCond=""; else $companyCond="  and a.company_id=$company";
	if ($company==0) $companyCond2=""; else $companyCond2="  and f.company_id=$company";


	//$buyerdata=($buyer)?' and d.buyer_name='.$buyer : '';
	//$batch_num=($batch_no)?" and a.batch_no='".$batch_no."'" : '';
	
	//echo $cbo_batch_type;
	if(trim($ext_no)!="") $ext_no_search="%".trim($ext_no)."%"; else $ext_no_search="%%";
	if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
	{
		if ($txt_order!='') $order_no="  and c.po_number='$txt_order'"; else $order_no="";
		if ($file_no!='') $file_cond="  and c.file_no=$file_no"; else $file_cond="";
		if ($ref_no!='') $ref_cond="  and c.grouping='$ref_no'"; else $ref_cond="";
		$jobdata=($job_number_id )? " and d.job_no_prefix_num='".$job_number_id ."'" : '';
	}
	
	if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
	{
		if ($txt_order!='') $suborder_no="and c.order_no='$txt_order'"; else $suborder_no="";
		if ($job_number_id!='') $sub_job_cond="  and d.job_no_prefix_num='".$job_number_id."' "; else $sub_job_cond="";
	}
	if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==3)
	{
	if ($txt_order!='') $order_no="  and c.po_number='$txt_order'"; else $order_no="";
	if ($file_no!='') $file_cond="  and c.file_no=$file_no"; else $file_cond="";
	if ($ref_no!='') $ref_cond="  and c.grouping='$ref_no'"; else $ref_cond="";
		$jobdata=($job_number_id )? " and d.job_no_prefix_num='".$job_number_id ."'" : '';
	}
	
	//echo $order_no;die;
	if ($ext_num=="") $ext_no=""; else $ext_no="  and a.extention_no=$ext_num ";
	if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
	//echo date("Y-n-j", strtotime("first day of previous month"));
	//echo date("Y-n-j", strtotime("last day of previous month"));
	if($txt_date_from && $txt_date_to)
	{
		if($db_type==0)
		{
			
			$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
			$second_month_ldate=date("Y-m-t",strtotime($date_to));
			$dateFrom= explode("-",$date_from);
			$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
			//$last_day= date('d',strtotime($date_to));
			//$last_date=date('Y-m',strtotime($date_to));
			$prod_last_day=change_date_format($second_month_ldate,'yyyy-mm-dd');
			// $prod_date_upto=" and  f.process_end_date BETWEEN '$fromdate' AND '$prod_last_day' ";
			$current_month = date('F');
			$current_month_from_date = date('F',strtotime($date_from));
			if ($current_month_from_date != $current_month ) 
			{
				$prod_date_upto="and  f.process_end_date BETWEEN '$fromdate' AND '$prod_last_day'";
			}
			else
			{
				$prod_last_day=date('yyyy-mm-dd',strtotime("-1 days"));
				$prod_date_upto="and  f.process_end_date BETWEEN '$fromdate' AND '$prod_last_day'";
			}
			// echo $prod_date_upto;die;
			if($date_search_type==1)
			{
				$dates_com=" and  f.process_end_date BETWEEN '$date_from' AND '$date_to' ";
			}
			else
			{
				$dates_com=" and f.insert_date between '".$date_from."' and '".$date_to." 23:59:59' ";
			}
		}
		elseif($db_type==2)
		{
			
			$date_from=change_date_format($txt_date_from,'','',1);
			$date_to=change_date_format($txt_date_to,'','',1);
			$dateFrom= explode("-",$date_from);
			//echo $dateto[1];
			$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
			//$prod_date_to=change_date_format($today_date,'','',1);
			$second_month_ldate=date("Y-M-t",strtotime($date_to));
			//$last_day= date("t", strtotime($date_to));
			//$last_day= date('d',strtotime($date_to));
			//$last_date=date('Y-M',strtotime($date_to));
		    $prod_last_day=change_date_format($second_month_ldate,'','',1);
			//$dates_com="and  f.process_end_date BETWEEN '$date_from' AND '$date_to'";
			// $prod_date_upto="and  f.process_end_date BETWEEN '$fromdate' AND '$prod_last_day'";

			$current_month = date('F');
			$current_month_from_date = date('F',strtotime($date_from));
			if ($current_month_from_date != $current_month ) 
			{
				$prod_date_upto="and  f.process_end_date BETWEEN '$fromdate' AND '$prod_last_day'";
			}
			else
			{
				$prod_last_day=date('d-M-Y',strtotime("-1 days"));
				$prod_date_upto="and  f.process_end_date BETWEEN '$fromdate' AND '$prod_last_day'";
			}
			// echo $prod_date_upto;die;

			if($date_search_type==1)
			{
				$dates_com="and  f.process_end_date BETWEEN '$date_from' AND '$date_to'";
			}
			else
			{
				$dates_com=" and f.insert_date between '".$date_from."' and '".$date_to." 11:59:59 PM' ";
			}
		}

		function dateDifference($start_date, $end_date)
		{
		    // calulating the difference in timestamps 
		    $diff = strtotime($start_date) - strtotime($end_date);
		     
		    // 1 day = 24 hours 
		    // 24 * 60 * 60 = 86400 seconds
		    return ceil(abs($diff / 86400))+1;
		}			 
		// call dateDifference() function to find the number of days between two dates
		$dateDiff = dateDifference($fromdate, $prod_last_day);			 
		//echo "Difference between two dates: " . $dateDiff . " Days ";die;

	}
	if($date_search_type==1)
	{
		$date_type_msg="Dyeing Date";
	}
	else
	{
		$date_type_msg="Insert Date";
	}	

	//print_r($yarn_lot_arr);
	$load_hr=array();
	$load_min=array();
	$load_date=array();
	$water_flow_arr=array();$load_hour_meter_arr=array();
	if ($company==0) $companyCond1=""; else $companyCond1="  and company_id=$company ";
	$load_time_data=sql_select("select batch_id,water_flow_meter,batch_no,load_unload_id,process_end_date,end_hours,hour_load_meter,end_minutes from pro_fab_subprocess where load_unload_id=1 and entry_form=35 $companyCond1 $workingCompany_name_cond1 $unload_batch_cond and status_active=1  and is_deleted=0 ");
	foreach($load_time_data as $row_time)// for Loading time
	{
		$load_hr[$row_time[csf('batch_id')]]=$row_time[csf('end_hours')];
		$load_min[$row_time[csf('batch_id')]]=$row_time[csf('end_minutes')];
		$load_date[$row_time[csf('batch_id')]]=$row_time[csf('process_end_date')];
		$water_flow_arr[$row_time[csf('batch_id')]]=$row_time[csf('water_flow_meter')];
		$load_hour_meter_arr[$row_time[csf('batch_id')]]=$row_time[csf('hour_load_meter')];
	}
	$subcon_load_hr=array();
	$subcon_load_min=array();
	$subcon_load_date=array();$subcon_load_hour_meter_arr=array();
	$subcon_water_flow_arr=array();
	$subcon_load_time_data=sql_select("select batch_id,water_flow_meter,batch_no,load_unload_id,process_end_date,end_hours,hour_load_meter,end_minutes from pro_fab_subprocess where load_unload_id=1 and entry_form=38 $companyCond1 $workingCompany_name_cond1  and status_active=1  and is_deleted=0 ");
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
	$subcon_unload_time_data=sql_select("select f.batch_id,f.batch_no,f.load_unload_id,f.production_date,f.end_hours,f.end_minutes from pro_fab_subprocess f  where f.load_unload_id=2 and f.entry_form=38 $companyCond1 $workingCompany_name_cond1 and f.status_active=1  and f.is_deleted=0 $cbo_prod_type_cond $result_name_cond $shift_name_cond machine_cond $floor_id_cond");
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
	
	//$color_id = return_field_value("distinct(a.id) as id", "lib_color a ", "a.color_name='$color'", "id");
		//echo $color_id;
		//if($color_id!='') $color=$color_id;else $color="";
		
		//if ($color=="") $color_name=""; else $color_name="  and a.color_id=$color";

	foreach($machine_capacity_data as $capacity)// for Un-Loading time
	{
		$m_capacity[$capacity[csf('id')]]=$capacity[csf('m_capacity')];
	}
	
	$sql_batch_id=sql_select("select f.batch_id from  pro_fab_subprocess f where f.entry_form=35 and f.load_unload_id=2 and f.status_active=1 and f.is_deleted=0  $unload_batch_cond2 $dates_com $cbo_prod_type_cond $result_name_cond $companyCond2  $machine_cond ");
	
	$tot_row=1;
	foreach($sql_batch_id as $row_batch)
	{
		if($tot_row!=1) $batch_id.=",";	
		$batch_id.=$row_batch[csf('batch_id')];	
		
		$tot_row++;
	}
	//echo $batch_id;die;
	unset($sql_batch_id);
	if ($batch_id!="")
	{
		$batchIds=chop($batch_id,','); $batchIds_cond="";
		if($db_type==2 && count($tot_row)>990)
		{
			$batchIds_cond=" and (";
			$batchIdsArr=array_chunk(explode(",",$batchIds),990);
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
			$batchIds_cond=" and a.id not in($batchIds)";
		}
	}
	//echo $batchIds_cond;
	$sub_sql_batch_id=sql_select("select f.batch_id from  pro_fab_subprocess f where f.entry_form=38 and f.load_unload_id=2 and f.status_active=1 and f.is_deleted=0 $cbo_prod_type_cond $result_name_cond machine_cond ");
	$k=1;
	foreach($sub_sql_batch_id as $row_batch)
	{
		if($k!=1) $sub_batch_id.=",";	
		$sub_batch_id.=$row_batch[csf('batch_id')];	
		
		$k++;
	}
	if($batch_id=="") $batch_id=0;
	if($sub_batch_id=="") $sub_batch_id=0;
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
		$order_by="order by f.seq_no,f.process_end_date,f.end_hours";
		$order_by2="order by seq_no,process_end_date,end_hours";
	}
	else
	{
		$order_by="order by f.process_end_date,f.machine_id";
		$order_by2="order by process_end_date,end_hours,machine_id";
	}
	
	if($db_type==2)
	{
		$grp_con="LISTAGG(CAST(c.po_number AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS po_number,LISTAGG(CAST(b.po_id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS po_id,LISTAGG(CAST(c.grouping AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS grouping,LISTAGG(CAST(c.file_no AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS file_no";
	  	$grp_sub_con="LISTAGG(CAST(c.order_no AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS po_number,LISTAGG(CAST(b.po_id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS po_id";
	  	$group_cond="LISTAGG(CAST(b.item_description AS VARCHAR2(4000)),'**') WITHIN GROUP ( ORDER BY a.id) AS item_description,LISTAGG(CAST(b.width_dia_type AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS width_dia_type,LISTAGG(CAST(b.prod_id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS prod_id";
	  	//, b.item_description, b.width_dia_type, b.prod_id
	}
	else if($db_type==0)
	{
		$grp_con="group_concat(distinct c.po_number) AS po_number,group_concat(distinct b.po_id) AS po_id,group_concat(distinct c.grouping) AS grouping,group_concat(distinct c.file_no) AS file_no";
		$grp_sub_con="group_concat(distinct c.order_no) AS po_number,group_concat(distinct b.po_id) AS po_id";
		$group_cond="group_concat(distinct b.item_description) AS item_description,group_concat(distinct b.width_dia_type) AS width_dia_type,group_concat(distinct b.prod_id) AS prod_id";
	}
		
	if($cbo_type==1)//   For WiP
	{
		$sql="(select a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight,b.batch_qnty AS batch_qnty, c.po_number, b.po_id, c.grouping, c.file_no, b.item_description, b.width_dia_type, b.prod_id, d.job_no as job_no_prefix_num, d.buyer_name,f.remarks, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,a.booking_no,a.booking_without_order,g.seq_no, a.entry_form 
		from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g, pro_batch_create_mst a 
		where f.batch_id=a.id and f.batch_id=b.mst_id $companyCond  $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond   $result_name_cond  $machine_cond  $file_cond $ref_cond $cbo_prod_source_cond $cbo_prod_type_cond $batchIds_cond and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=1 and a.batch_against in(1,2,3) and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0
		)
		UNION ALL
		(
		select a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight, b.batch_qnty AS batch_qnty, null as po_number ,null as po_id,null as grouping, null as file_no, b.item_description, b.width_dia_type, b.prod_id, null as job_no_prefix_num,h.buyer_id as buyer_name,f.remarks,f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date,
		f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id,f.load_unload_id, f.fabric_type,f.result,a.booking_no,a.booking_without_order,g.seq_no, a.entry_form 
		from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h 
		where h.booking_no=a.booking_no $companyCond   and f.batch_id=a.id and f.batch_id=b.mst_id  and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=1 and a.batch_against in(1,2,3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  f.status_active=1 and f.is_deleted=0 $dates_com  $batch_num  $buyerdata2 $result_name_cond  $machine_cond $cbo_prod_type_cond $cbo_prod_source_cond $batchIds_cond
		) 
		UNION ALL
		(select a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight,b.trims_wgt_qnty as batch_qnty, null as po_number, null as po_id, null as grouping, null as file_no, b.item_description, null as width_dia_type, null as prod_id, d.job_no as job_no_prefix_num, d.buyer_name,f.remarks, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,a.booking_no,a.booking_without_order,g.seq_no, a.entry_form 
		from pro_batch_create_mst a, pro_batch_trims_dtls b, wo_po_details_master d, pro_fab_subprocess f, lib_machine_name g 
		where f.batch_id=a.id and f.batch_id=b.mst_id $companyCond $dates_com  $batch_num  $buyerdata2 $result_name_cond  $machine_cond $cbo_prod_type_cond $cbo_prod_source_cond $batchIds_cond and a.entry_form=136 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=1 and a.batch_against in(1,2,3) and a.job_no=d.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0)
		$order_by2";
		// echo $sql;
	}
	else if($cbo_type==2)//   For Order Wise Dyeing Production
	{	
		$sql="(SELECT a.batch_against,a.company_id,a.batch_no,a.entry_form, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight,b.batch_qnty AS batch_qnty, c.po_number, b.po_id, c.grouping, c.file_no, b.item_description, b.width_dia_type, b.prod_id, d.job_no as job_no_prefix_num, d.buyer_name,f.remarks, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,a.booking_no,a.booking_without_order,g.seq_no 
		FROM pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g, pro_batch_create_mst a 
		WHERE f.batch_id=a.id and f.batch_id=b.mst_id $companyCond  $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond   $result_name_cond  $machine_cond  $file_cond $ref_cond $cbo_prod_source_cond $cbo_prod_type_cond and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,2,3) and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0 
		)
		UNION ALL
		(
		SELECT a.batch_against,a.company_id,a.batch_no,a.entry_form, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight, b.batch_qnty AS batch_qnty, null as po_number ,null as po_id,null as grouping, null as file_no,b.item_description, b.width_dia_type, b.prod_id, null as job_no_prefix_num,h.buyer_id as buyer_name,f.remarks,f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id,f.load_unload_id, f.fabric_type,f.result,a.booking_no,a.booking_without_order,g.seq_no 
		FROM pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h 
		WHERE h.booking_no=a.booking_no $companyCond   and f.batch_id=a.id and f.batch_id=b.mst_id  and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,2,3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  f.status_active=1 and f.is_deleted=0 $dates_com  $batch_num  $color_name  $buyerdata2 $result_name_cond  $machine_cond $cbo_prod_type_cond $cbo_prod_source_cond
		) 
		UNION ALL
		(
		SELECT a.batch_against,a.company_id,a.batch_no,a.entry_form, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight,b.trims_wgt_qnty AS batch_qnty, null as po_number ,null as po_id,null as grouping, null as file_no, null as item_description, null as width_dia_type, null as prod_id, d.job_no as job_no_prefix_num, d.buyer_name,f.remarks, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,a.booking_no,a.booking_without_order,g.seq_no 
		FROM pro_batch_create_mst a,pro_batch_trims_dtls b, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g 
		WHERE a.id=b.mst_id and f.batch_id=a.id and f.batch_id=b.mst_id and a.entry_form=136 and g.id=f.machine_id and  f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,2,3) and d.job_no=a.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  $companyCond  $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond   $result_name_cond  $machine_cond   $cbo_prod_source_cond $cbo_prod_type_cond
		) $order_by2";
		//echo $sql;die;
	}
	else if($cbo_type==3)//   LTB-BTB
	{
		$sql="(select a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight,b.batch_qnty AS batch_qnty, c.po_number, b.po_id, c.grouping, c.file_no, b.item_description, b.width_dia_type, b.prod_id, d.job_no as job_no_prefix_num, d.buyer_name,f.remarks, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,a.booking_no,a.booking_without_order,g.seq_no 
		from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g, pro_batch_create_mst a 
		where f.batch_id=a.id and f.batch_id=b.mst_id $companyCond  $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond   $result_name_cond  $machine_cond  $file_cond $ref_cond $cbo_prod_source_cond $cbo_prod_type_cond and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1) and f.result in(1,2,3,4,5) and f.ltb_btb_id=1 and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0
		)
		UNION ALL
		(
		select a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight, b.batch_qnty AS batch_qnty, null as po_number ,null as po_id,null as grouping, null as file_no, b.item_description, b.width_dia_type, b.prod_id, null as job_no_prefix_num,h.buyer_id as buyer_name,f.remarks,f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id,f.load_unload_id, f.fabric_type,f.result,a.booking_no,a.booking_without_order,g.seq_no 
		from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h 
		where h.booking_no=a.booking_no $companyCond   and f.batch_id=a.id and f.batch_id=b.mst_id  and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1) and f.result in(1,2,3,4,5) and f.ltb_btb_id=1 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  f.status_active=1 and f.is_deleted=0 $dates_com  $batch_num  $color_name  $buyerdata2 $result_name_cond  $machine_cond $cbo_prod_type_cond $cbo_prod_source_cond
		) $order_by2";

		$sql_ltb="(select a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight,b.batch_qnty AS batch_qnty, c.po_number, b.po_id, c.grouping, c.file_no, b.item_description, b.width_dia_type, b.prod_id, d.job_no as job_no_prefix_num, d.buyer_name,f.remarks, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,a.booking_no,a.booking_without_order,g.seq_no 
		from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g, pro_batch_create_mst a 
		where f.batch_id=a.id and f.batch_id=b.mst_id $companyCond  $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond   $result_name_cond  $machine_cond  $file_cond $ref_cond $cbo_prod_source_cond $cbo_prod_type_cond and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1) and f.result in(1,2,3,4,5) and f.ltb_btb_id=2 and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0
		)
		UNION ALL
		(
		select a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight, b.batch_qnty AS batch_qnty, null as po_number ,null as po_id,null as grouping, null as file_no, b.item_description, b.width_dia_type, b.prod_id, null as job_no_prefix_num,h.buyer_id as buyer_name,f.remarks,f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id,f.load_unload_id, f.fabric_type,f.result,a.booking_no,a.booking_without_order,g.seq_no 
		from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h 
		where h.booking_no=a.booking_no $companyCond   and f.batch_id=a.id and f.batch_id=b.mst_id  and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1) and f.result in(1,2,3,4,5) and f.ltb_btb_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  f.status_active=1 and f.is_deleted=0 $dates_com  $batch_num  $color_name  $buyerdata2 $result_name_cond  $machine_cond $cbo_prod_type_cond $cbo_prod_source_cond
		) $order_by2";
		// echo $sql;die;
		// echo $sql_ltb;die;
	}
	else if($cbo_type==4)//   For Re Process
	{
		$sql="(select a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight,b.batch_qnty AS batch_qnty, c.po_number, b.po_id, c.grouping, c.file_no, b.item_description, b.width_dia_type, b.prod_id, d.job_no as job_no_prefix_num, d.buyer_name,f.remarks, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,a.booking_no,a.booking_without_order,g.seq_no 
		from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g, pro_batch_create_mst a 
		where f.batch_id=a.id and f.batch_id=b.mst_id $companyCond  $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond   $result_name_cond  $machine_cond  $file_cond $ref_cond $cbo_prod_source_cond $cbo_prod_type_cond and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(2) and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0)
		UNION ALL
		(
		select a.batch_against,a.company_id,a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight, b.batch_qnty AS batch_qnty, null as po_number ,null as po_id,null as grouping, null as file_no, b.item_description, b.width_dia_type, b.prod_id, null as job_no_prefix_num,h.buyer_id as buyer_name,f.remarks,f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date,f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id,f.load_unload_id, f.fabric_type,f.result,a.booking_no,a.booking_without_order,g.seq_no 
		from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h 
		where h.booking_no=a.booking_no $companyCond   and f.batch_id=a.id and f.batch_id=b.mst_id  and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(2) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  f.status_active=1 and f.is_deleted=0 $dates_com  $batch_num  $color_name  $buyerdata2 $result_name_cond  $machine_cond $cbo_prod_type_cond $cbo_prod_source_cond
		) $order_by2";
		//echo $sql;die;
	}
	// echo $sql;die;
	//$batchdata=sql_select($sql);
	//echo $sql_subcon; die;sql_subcon_ltb

	

	if($cbo_type==2 || $cbo_type==1 || $cbo_type==4 || $cbo_type==3)
	{
		// echo $sql;
		$batchdata=sql_select($sql);
		$batch_wise_arr=array();
		foreach($batchdata as $row) // Main array
		{
			$batch_wise_arr[$row[csf('id')]]['id']=$row[csf('id')];
			$batch_wise_arr[$row[csf('id')]]['batch_against']=$row[csf('batch_against')];
			$batch_wise_arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
			$batch_wise_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$batch_wise_arr[$row[csf('id')]]['entry_form']=$row[csf('entry_form')];
			$batch_wise_arr[$row[csf('id')]]['batchWgt']=$row[csf('batch_weight')];
			$batch_wise_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
			$batch_wise_arr[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
			$batch_wise_arr[$row[csf('id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
			$batch_wise_arr[$row[csf('id')]]['batch_qnty']+=$row[csf('batch_qnty')];
			$batch_wise_arr[$row[csf('id')]]['item_description'].=$row[csf('item_description')].'**';
			$batch_wise_arr[$row[csf('id')]]['width_dia_type'].=$row[csf('width_dia_type')].',';
			$batch_wise_arr[$row[csf('id')]]['prod_id'].=$row[csf('prod_id')].',';
			$batch_wise_arr[$row[csf('id')]]['po_number'].=$row[csf('po_number')].',';
			$batch_wise_arr[$row[csf('id')]]['po_id'].=$row[csf('po_id')].',';
			$batch_wise_arr[$row[csf('id')]]['grouping'].=$row[csf('grouping')].',';
			$batch_wise_arr[$row[csf('id')]]['file_no'].=$row[csf('file_no')].',';
			$batch_wise_arr[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
			$batch_wise_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
			$batch_wise_arr[$row[csf('id')]]['remarks']=$row[csf('remarks')]; 
			$batch_wise_arr[$row[csf('id')]]['shift_name']=$row[csf('shift_name')];
			$batch_wise_arr[$row[csf('id')]]['process_end_date']=$row[csf('process_end_date')];
			$batch_wise_arr[$row[csf('id')]]['production_date']=$row[csf('production_date')];
			$batch_wise_arr[$row[csf('id')]]['end_hours']=$row[csf('end_hours')];
			$batch_wise_arr[$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
			$batch_wise_arr[$row[csf('id')]]['hour_unload_meter']=$row[csf('hour_unload_meter')];
			$batch_wise_arr[$row[csf('id')]]['water_flow_meter']=$row[csf('water_flow_meter')];
			$batch_wise_arr[$row[csf('id')]]['end_minutes']=$row[csf('end_minutes')];
			$batch_wise_arr[$row[csf('id')]]['machine_id']=$row[csf('machine_id')];
			$batch_wise_arr[$row[csf('id')]]['load_unload_id']=$row[csf('load_unload_id')];
			$batch_wise_arr[$row[csf('id')]]['fabric_type']=$row[csf('fabric_type')];
			$batch_wise_arr[$row[csf('id')]]['result']=$row[csf('result')];
			$batch_wise_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
			$batch_wise_arr[$row[csf('id')]]['booking_without_order']=$row[csf('booking_without_order')];
			$batch_wise_arr[$row[csf('id')]]['seq_no']=$row[csf('seq_no')];
		}
		//echo "<pre>";print_r($batch_wise_arr);die;

		$total_batch_qty_re_dyeing=0;$sum_trims_check_array=array();$m=1;
		foreach($batch_wise_arr as $row)
		{
			if($row['batch_against']==2)//Re-Dyeing
			{
				$buyer_re_process_arr[$row['buyer_name']]['re_qty']+=$row['batch_qnty'];
				$total_batch_qty_re_dyeing+=$row['batch_qnty'];
				 
				$batch_no=$row['id'];
				if (!in_array($batch_no,$sum_trims_check_array))
				{ $m++;
					
					
					$sum_trims_check_array[]=$batch_no;
					$sumtot_trim_qty=$row['total_trims_weight'];
				}
				else
				{
					$sumtot_trim_qty=0;
				}
				// $trims_total_batch_qty+=$sumtot_trim_qty;
				$buyer_re_process_arr[$row['buyer_name']]['trims']+=$sumtot_trim_qty;
			}
		}
		//echo  $buyer_re_process_arr[1]['re_qty'].'dsd';
		if($cbo_type==3)
		{
			$batchdata_ltb=sql_select($sql_ltb);
			$ltb_batch_wise_arr=array();
			foreach($batchdata_ltb as $row) // Main array
			{
				$ltb_batch_wise_arr[$row[csf('id')]]['id']=$row[csf('id')];
				$ltb_batch_wise_arr[$row[csf('id')]]['batch_against']=$row[csf('batch_against')];
				$ltb_batch_wise_arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
				$ltb_batch_wise_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
				$ltb_batch_wise_arr[$row[csf('id')]]['entry_form']=$row[csf('entry_form')];
				$ltb_batch_wise_arr[$row[csf('id')]]['batchWgt']=$row[csf('batch_weight')];
				$ltb_batch_wise_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
				$ltb_batch_wise_arr[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
				$ltb_batch_wise_arr[$row[csf('id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
				$ltb_batch_wise_arr[$row[csf('id')]]['batch_qnty']+=$row[csf('batch_qnty')];
				$ltb_batch_wise_arr[$row[csf('id')]]['item_description'].=$row[csf('item_description')].'**';
				$ltb_batch_wise_arr[$row[csf('id')]]['width_dia_type'].=$row[csf('width_dia_type')].',';
				$ltb_batch_wise_arr[$row[csf('id')]]['prod_id'].=$row[csf('prod_id')].',';
				$ltb_batch_wise_arr[$row[csf('id')]]['po_number'].=$row[csf('po_number')].',';
				$ltb_batch_wise_arr[$row[csf('id')]]['po_id'].=$row[csf('po_id')].',';
				$ltb_batch_wise_arr[$row[csf('id')]]['grouping'].=$row[csf('grouping')].',';
				$ltb_batch_wise_arr[$row[csf('id')]]['file_no'].=$row[csf('file_no')].',';
				$ltb_batch_wise_arr[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
				$ltb_batch_wise_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
				$ltb_batch_wise_arr[$row[csf('id')]]['remarks']=$row[csf('remarks')]; 
				$ltb_batch_wise_arr[$row[csf('id')]]['shift_name']=$row[csf('shift_name')];
				$ltb_batch_wise_arr[$row[csf('id')]]['process_end_date']=$row[csf('process_end_date')];
				$ltb_batch_wise_arr[$row[csf('id')]]['production_date']=$row[csf('production_date')];
				$ltb_batch_wise_arr[$row[csf('id')]]['end_hours']=$row[csf('end_hours')];
				$ltb_batch_wise_arr[$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
				$ltb_batch_wise_arr[$row[csf('id')]]['hour_unload_meter']=$row[csf('hour_unload_meter')];
				$ltb_batch_wise_arr[$row[csf('id')]]['water_flow_meter']=$row[csf('water_flow_meter')];
				$ltb_batch_wise_arr[$row[csf('id')]]['end_minutes']=$row[csf('end_minutes')];
				$ltb_batch_wise_arr[$row[csf('id')]]['machine_id']=$row[csf('machine_id')];
				$ltb_batch_wise_arr[$row[csf('id')]]['load_unload_id']=$row[csf('load_unload_id')];
				$ltb_batch_wise_arr[$row[csf('id')]]['fabric_type']=$row[csf('fabric_type')];
				$ltb_batch_wise_arr[$row[csf('id')]]['result']=$row[csf('result')];
				$ltb_batch_wise_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
				$ltb_batch_wise_arr[$row[csf('id')]]['booking_without_order']=$row[csf('booking_without_order')];
				$ltb_batch_wise_arr[$row[csf('id')]]['seq_no']=$row[csf('seq_no')];
			}

			//$matched_btb=count($sql_btb_data);
			$tot_btb_matched=0;$tot_btb_not_matched=0;
			foreach($batch_wise_arr as $btb)
			{
				if($btb['result']==1)//Shade Matched
				{
					$tot_btb_matched+=1;
				}
				else if($btb['result']!=1)
				{
					$tot_btb_not_matched+=1;
					//echo "not,";
				}
			}
			// echo $tot_btb_matched;die;
			$tot_ltb_matched=0;$tot_ltb_not_matched=0;
			foreach($ltb_batch_wise_arr as $ltb)
			{
				if($ltb['result']==1)//Shade Matched
				{
					$tot_ltb_matched+=1;
				}
				else
				{
					$tot_ltb_not_matched+=1;
				}
			}
			//echo $tot_btb_not_matched;
		}
		$batch_ids='';$all_po_id='';
		foreach($batch_wise_arr as $row)
		{
			if($batch_ids=='') $batch_ids=$row['id']; else $batch_ids.=",".$row['id'];
			if($all_po_id=='') $all_po_id=$row['po_id']; else $all_po_id.=",".$row['po_id'];
		}
		$po_idsid=implode(",",(array_unique(explode(",",$all_po_id))));
		$batch_idss=implode(",",(array_unique(explode(",",$batch_ids))));
		
		$poIds=chop($po_idsid,','); $po_cond_for_in=""; 
		$po_ids=count(array_unique(explode(",",$po_idsid)));
		if($db_type==2 && $po_ids>999)
		{
			$po_cond_for_in=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$po_cond_for_in.="b.po_breakdown_id in($ids) or"; 
			}
			$po_cond_for_in=chop($po_cond_for_in,'or ');
			$po_cond_for_in.=")";
		}
		else
		{
			$po_cond_for_in=" and b.po_breakdown_id in($poIds)";
		}
		
		$batchIds=chop($batch_idss,','); $batch_cond_for_in=""; 
		$batch_ids=count(array_unique(explode(",",$batchIds)));
		if($db_type==2 && $batch_ids>999)
		{
			$batch_cond_for_in=" and (";
			$batchIdsArr=array_chunk(explode(",",$batchIds),999);
			foreach($batchIdsArr as $ids)
			{
			$ids=implode(",",$ids);
			$batch_cond_for_in.="a.id in($ids) or"; 
			}
			$batch_cond_for_in=chop($batch_cond_for_in,'or ');
			$batch_cond_for_in.=")";
		}
		else
		{
			$batch_cond_for_in=" and a.id in($batchIds)";
		}
			
		//}
		
	
		//print_r($sql_subcon_data);
		$yarn_lot_arr=array();
		if($db_type==0)
		{
			$yarn_lot_data=sql_select("select b.po_breakdown_id, a.prod_id, a.yarn_lot as yarn_lot from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yarn_lot!=''  $po_cond_for_in  group by a.prod_id, b.po_breakdown_id");
		}
		else if($db_type==2)
		{
			$yarn_lot_data=sql_select("select b.po_breakdown_id, a.prod_id,  a.yarn_lot as yarn_lot from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yarn_lot!='0' $po_cond_for_in group by a.prod_id, b.po_breakdown_id,a.yarn_lot");
		}
		foreach($yarn_lot_data as $rows)
		{
			//$yarn_lot=explode(",",$rows[csf('yarn_lot')]);
			$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]].=$rows[csf('yarn_lot')].',';
		}
	}
	// echo "<pre>"; print_r($yarn_lot_arr);die;
	
	ob_start();
	if($cbo_type==2 || $cbo_type==1 || $cbo_type==4) //  Dyeing Production
	{
		?>
		<fieldset style="width:1350px;">
		<div align="center"><strong> <? echo $company_library[$company]; ?> </strong><br> <strong> Daily Dyeing Production </strong><br>
			<?
			echo  ($date_from == '0000-00-00' || $date_from == '' ? '' : change_date_format($date_from)).' To ';echo  ($date_to == '0000-00-00' || $date_to == '' ? '' : change_date_format($date_to));
			?>
		</div>
		<?
		if($cbo_type!=1) // without wip
		{  
			if ($cbo_result_name==1 || $cbo_result_name==0) // AKH-2 All summary Start
			{
			 	?>
			 	<div>
	                        
				<table cellpadding="0"  width="620" cellspacing="0" align="left" style="margin-left:20px;">
					<tr>
						<!-- AKH-2 Production Summary Start-->
						<td>
		                	<table cellpadding="0"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
								<thead>
									<tr>
										<th colspan="3">Production Summary</th>
									</tr>
									<tr>
										<th>Details </th> 
										<th>Prod. Qty. </th>
										<th>%</th>
									</tr>
								</thead>
								<tbody>
									<?
									if ($cbo_result_name==0 || $cbo_result_name==1) $result_name_cond_summary=" and f.result=1"; else $result_name_cond_summary=" ";
									
									$sql="(select f.load_unload_id,f.process_end_date,count(f.process_end_date) as row_count,
									sum(CASE WHEN a.batch_against in(1,3) THEN b.batch_qnty ELSE 0 END) AS batch_qnty,
									sum(distinct CASE WHEN a.batch_against in(1,3) THEN a.total_trims_weight ELSE 0 END) AS total_trims_weight,
									sum(CASE WHEN a.batch_against in(2) THEN b.batch_qnty ELSE 0 END) AS re_batch_qnty 

									from pro_batch_create_mst a,pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g where f.batch_id=a.id  and g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and a.entry_form=0 and  f.entry_form=35 and f.load_unload_id=2 and f.result=1 and a.batch_against in(1,2)  and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  $prod_date_upto $jobdata $batch_num $buyerdata $order_no $year_cond $color_name $companyCond $workingCompany_name_cond2 $shift_name_cond $machine_cond $floor_id_cond  $result_name_cond_summary $cbo_prod_type_cond $cbo_prod_source_cond GROUP BY f.load_unload_id,f.process_end_date)
									union
									(
									select f.load_unload_id,f.process_end_date,count(f.process_end_date) as row_count,
									sum(CASE WHEN a.batch_against in(1,3) THEN b.trims_wgt_qnty ELSE 0 END) AS batch_qnty,
									sum(distinct CASE WHEN a.batch_against in(1,3) THEN a.total_trims_weight ELSE 0 END) AS total_trims_weight,
									sum(CASE WHEN a.batch_against in(2) THEN b.trims_wgt_qnty ELSE 0 END) AS re_batch_qnty 

									from pro_batch_create_mst a,pro_batch_trims_dtls b, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g where f.batch_id=a.id  and g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and a.entry_form=136 and  f.entry_form=35 and f.load_unload_id=2 and f.result=1 and a.batch_against in(1,2) and d.job_no=a.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  $prod_date_upto $jobdata $batch_num $buyerdata $order_no $year_cond $color_name $companyCond $workingCompany_name_cond2 $shift_name_cond $machine_cond $floor_id_cond  $result_name_cond_summary $cbo_prod_type_cond $cbo_prod_source_cond GROUP BY f.load_unload_id,f.process_end_date 
									)
									";
									// echo $sql;
									$sql_datas=sql_select($sql);
									
									$unload_qty_arr=array();$plan_cycle_time=0;$reprocess_qty_arr=array();$tot_reprocess_qty=0;
									foreach($sql_datas as $row)
									{
										$tot_row=count($row[csf('process_end_date')]);
										$unload_qty_arr[$row[csf('load_unload_id')]]['qty']+=$row[csf('batch_qnty')]+$row[csf('total_trims_weight')];
										//$reprocess_qty_arr[2]['bqty']+=$row[csf('batch_qnty')];
										 $tot_reprocess_qty+=$row[csf('re_batch_qnty')];
										$unload_qty_arr[$row[csf('load_unload_id')]]['count']+=$tot_row;
										$unload_qty_arr[$row[csf('load_unload_id')]]['process_end_date'].=$row[csf('process_end_date')].',';		
									}
									unset($sql_datas);
									
									//print_r($unload_qty_arr);
									$total_current_mon_qty1=$unload_qty_arr[2]['qty'];
									$total_count1=$unload_qty_arr[2]['count'];
									$total_reprocess_qty1=$tot_reprocess_qty;

									$sql_sample_currMon="select f.load_unload_id,f.process_end_date,count(f.process_end_date) as row_count,
									sum(CASE WHEN a.batch_against in(3) THEN b.batch_qnty ELSE 0 END) AS batch_qnty,
									sum(distinct CASE WHEN a.batch_against in(1) THEN a.total_trims_weight ELSE 0 END) AS total_trims_weight,
									sum(CASE WHEN a.batch_against in(2) THEN b.batch_qnty ELSE 0 END) AS re_batch_qnty 
									from pro_batch_create_dtls b, wo_non_ord_samp_booking_mst h, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where  f.batch_id=a.id $companyCond   and a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and f.result=1  and a.batch_against in(2,3)  and h.booking_no=a.booking_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  $prod_date_upto  $batch_num $buyerdata2  $machine_cond $floor_id_cond  $result_name_cond_summary $cbo_prod_type_cond $cbo_prod_source_cond GROUP BY  f.load_unload_id,f.process_end_date  ";
									$sql_result_samp_currMon=sql_select($sql_sample_currMon);
									$tot_reprocess_qty2=0;
									$process_enddate=rtrim($unload_qty_arr[2]['process_end_date'],',');
									$process_enddates=array_unique(explode(",",$process_enddate));
									foreach($sql_result_samp_currMon as $row)
									{
										$tot_row=count($row[csf('process_end_date')]);
										$unload_qty_arr2[$row[csf('load_unload_id')]]['qty']+=$row[csf('batch_qnty')]+$row[csf('total_trims_weight')];
										//$reprocess_qty_arr[2]['bqty']+=$row[csf('batch_qnty')];
										 $tot_reprocess_qty2+=$row[csf('re_batch_qnty')];
										 
										  $isval=array_diff($row[csf('process_end_date')],$edate);
										  $tot_rows=0;
										 foreach($process_enddates as $edate)
										 {
											 
											  $tot_rows=count($row[csf('process_end_date')]);
											  $isval=array_diff($row[csf('process_end_date')],$edate);
											 if($isval)
											 {
												$unload_qty_arr2[$row[csf('load_unload_id')]]['count']+=$tot_rows;	 	
											 }
											
										}
										  
										//$unload_qty_arr2[$row[csf('load_unload_id')]]['count']+=$tot_row;		
									}
									unset($sql_result_samp_currMon);
									 $total_current_mon_qty=$unload_qty_arr2[2]['qty']+$total_current_mon_qty1;
									$total_count=$total_count1+$unload_qty_arr2[2]['count'];
									$total_reprocess_qty=$total_reprocess_qty1+$tot_reprocess_qty2;
									
									$sql_result_sample="select a.id,f.fabric_type,f.process_end_date,
									SUM(b.batch_qnty) AS batch_qnty,sum(a.batch_weight) as batch_weight,sum(distinct a.total_trims_weight) as total_trims_weight 
									from pro_batch_create_dtls b, wo_non_ord_samp_booking_mst h, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where f.batch_id=a.id $companyCond $workingCompany_name_cond2 and  a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2  and a.batch_against in(1,3)  and h.booking_no=a.booking_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and f.fabric_type in(1,2,4,5,6) and  f.status_active=1 and f.is_deleted=0  $dates_com  $batch_num $buyerdata2   $machine_cond   $result_name_cond_summary $cbo_prod_type_cond $cbo_prod_source_cond 
									GROUP BY  a.id,f.fabric_type,f.process_end_date  ";
									$sql_result_sam=sql_select($sql_result_sample);
									$fabric_batch_arr=array();$tot_batch_qty_type=array();//$trims_wgt_check_array=array();
									$zz=1;
									foreach($sql_result_sam as $row)
									{
												
										$tot_trim_qty=$row[csf('total_trims_weight')];
										$fabric_batch_arr[$row[csf('fabric_type')]]['qty']+=$row[csf('batch_qnty')]+$tot_trim_qty;
										$fabric_batch_arr[$row[csf('fabric_type')]]['weight']+=$row[csf('batch_weight')];
										$tot_batch_qty_type[1]+=$row[csf('batch_qnty')]+$tot_trim_qty;
									}
									unset($sql_result_sam);
									
									$sql_result="(select a.id,f.fabric_type,f.process_end_date,
									SUM(b.batch_qnty) AS batch_qnty,sum(a.batch_weight) as batch_weight,sum(distinct a.total_trims_weight) as total_trims_weight from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where f.batch_id=a.id $companyCond   and a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2  and a.batch_against in(1,3)  and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and f.fabric_type in(1,2,4,5,6) and  f.status_active=1 and f.is_deleted=0 $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond  $machine_cond  $result_name_cond_summary $cbo_prod_type_cond $cbo_prod_source_cond GROUP BY a.id, f.fabric_type,f.process_end_date)
									union
									(
									select a.id,f.fabric_type,f.process_end_date,
									SUM(b.trims_wgt_qnty) AS batch_qnty,sum(a.batch_weight) as batch_weight,sum(distinct a.total_trims_weight) as total_trims_weight from pro_batch_trims_dtls b, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where f.batch_id=a.id $companyCond   and a.entry_form=136 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2  and a.batch_against in(1,3) and d.job_no=a.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  f.status_active=1 and f.is_deleted=0 and f.fabric_type in(1,2,4,5,6) $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond  $machine_cond  $result_name_cond_summary $cbo_prod_type_cond $cbo_prod_source_cond GROUP BY a.id, f.fabric_type,f.process_end_date
									)
									";
									$sql_result=sql_select($sql_result);
									foreach($sql_result as $row)
									{
										$tot_trim_qty=$row[csf('total_trims_weight')];
										$fabric_batch_arr[$row[csf('fabric_type')]]['qty']+=$row[csf('batch_qnty')]+$tot_trim_qty;
										$fabric_batch_arr[$row[csf('fabric_type')]]['weight']+=$row[csf('batch_weight')];
										$tot_batch_qty_type[1]+=$row[csf('batch_qnty')]+$tot_trim_qty;
									}
									unset($sql_result);
									//print_r($fabric_batch_arr);
								
									
									?>
									<tr bgcolor="#E9F3FF" style="cursor:pointer;">
										 
										  <td>Current Month</td>
		                                   <td align="right"><? echo number_format($total_current_mon_qty,2);?></td> 
										  <td align="right"><?  ?></td>  
									</tr>
		                            <tr bgcolor="#D8D8D8" style="cursor:pointer;">
										<td>Avg. Prod. Per Day</td>
										<td align="right" title="<? echo 'Total Day: '.$dateDiff;?>"><? echo number_format($total_current_mon_qty/$dateDiff,2); ?></td> 
										<td align="right"><? //echo $total_current_mon_qty/$total_count; ?></td>  
									</tr>
		                            <tr bgcolor="#D8D8D8" style="cursor:pointer; ">
										<td>ReProcess Current Month</td>
										<td align="right"><?   echo number_format($total_reprocess_qty,2);//round( 1040.56789, 4, PHP_ROUND_HALF_EVEN) ?></td> 
										<td align="right"><? //echo $total_current_mon_qty/$total_count; ?></td>  
									</tr>
		                            <? $k=1;	$tot_batch_qty=0;
									//$fabric_type_for_dyeing2=array(1=>'Cotton',2=>'Polyster',3=>'Lycra',4=>'Both Part',5=>'White',6=>'Wash');
		                             
									//print_r($fabric_type_for_dyeingnn);
									foreach($fabric_batch_arr as $typekey=>$val)
									{
										if ($k%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";
										if($typekey==1)
										{
											$fab_type="Cotton";
										}
										else if($typekey==2)
										{
											$fab_type="Polyster";
										}
										else if($typekey==3)
										{
											$fab_type="Lycra";
										}
										else if($typekey==4)
										{
											$fab_type="Both Part";
										}
										else if($typekey==5)
										{
											$fab_type="White";
										}
										else if($typekey==6)
										{
											$fab_type="Wash";
										}
										$total_reporcess=$tot_batch_qty_type[1];
											  ?>
		                               	<tr bgcolor="<? echo $bgcolor;?>">
										 	<? //print_r($fabric_type_for_dyeing);?>
										  	<td><?php echo $fab_type;//$fabric_type_for_dyeing[$typekey];?></td>
		                                   	<td align="right" title="<? echo $val['weight']?>"><? echo number_format($val['qty'],2); ?></td> 
										  	<td align="right"><? echo number_format(($val['qty']/$total_reporcess)*100,2); ?></td>  
									  	</tr>
										<? 
										$tot_batch_qty+=$val['qty'];
									  	$k++;
									}
									
									?>
								</tbody>
								<tfoot>
									<tr> 
										<th align="right">Total </th>
										<th align="right"><b><? echo number_format($tot_batch_qty,2,'.','');?></b> </th>
										<th align="right"><? echo number_format(($tot_batch_qty/$total_reporcess*100),2,'.','').'%'; ?></th>
									</tr>
								</tfoot>
							</table>
	                    </td>
	                    <!-- AKH-2 Production Summary End-->

	                    <!-- AKH-2 Re Process Summary Start-->
                     	<?
						if ($cbo_result_name==0 || $cbo_result_name==1) $result_name_cond_summary=" and f.result=1"; else $result_name_cond_summary=" ";
						
						
						$sql_dt="(select a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight,SUM(b.batch_qnty) AS batch_qnty,0 AS trim_batch_qnty, b.item_description, b.prod_id, b.width_dia_type, $grp_con, d.job_no_prefix_num, d.buyer_name,f.remarks, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,a.booking_no,a.booking_without_order from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g, pro_batch_create_mst a where f.batch_id=a.id and f.batch_id=b.mst_id and g.id=f.machine_id $companyCond   $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond   $result_name_cond $machine_cond $file_cond $ref_cond $cbo_prod_source_cond $cbo_prod_type_cond and a.entry_form=0  and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1) and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and f.result=1 and  f.status_active=1 and f.is_deleted=0 
						GROUP BY a.batch_no, a.batch_weight,a.id, a.color_id,a.total_trims_weight, a.extention_no, b.item_description, b.prod_id, b.width_dia_type,d.job_no_prefix_num, d.buyer_name, f.shift_name, f.production_date,f.process_end_date, f.end_hours, f.floor_id,f.remarks,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type,f.result,a.booking_no,a.booking_without_order)
						union
						(
						select a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight,0 AS batch_qnty,SUM(b.trims_wgt_qnty) AS trim_batch_qnty, b.item_description, null as prod_id, null as width_dia_type,null as po_number ,null as po_id,null as grouping, null as file_no, d.job_no_prefix_num, d.buyer_name,f.remarks, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,a.booking_no,a.booking_without_order from  pro_batch_create_mst a,pro_batch_trims_dtls b, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g where  f.batch_id=a.id and a.id=b.mst_id  and f.batch_id=b.mst_id and g.id=f.machine_id  and d.job_no=a.job_no  and a.entry_form=136  and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1)  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and f.result=1 and  f.status_active=1 and f.is_deleted=0 $companyCond   $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond   $result_name_cond $machine_cond $file_cond $ref_cond $cbo_prod_source_cond $cbo_prod_type_cond 
						GROUP BY a.batch_no, a.batch_weight,a.id, a.color_id,a.total_trims_weight, a.extention_no,d.job_no_prefix_num, d.buyer_name, f.shift_name, f.production_date,f.process_end_date, f.end_hours, f.floor_id,f.remarks,f.hour_unload_meter, b.item_description,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type,f.result,a.booking_no,a.booking_without_order
						)
						union
						(
						select a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight, SUM(b.batch_qnty) AS batch_qnty,0 AS trim_batch_qnty,b.item_description, b.prod_id, b.width_dia_type,null as po_number ,null as po_id,null as grouping, null as file_no, null as job_no_prefix_num,h.buyer_id as buyer_name,f.remarks,f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date,
						f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id, f.fabric_type,f.result,a.booking_no,a.booking_without_order from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h where h.booking_no=a.booking_no $companyCond $workingCompany_name_cond2 and f.batch_id=a.id  and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and f.result=1 and  f.status_active=1 and f.is_deleted=0  $dates_com  $batch_num   $buyerdata2 $result_name_cond  $machine_cond $cbo_prod_type_cond $cbo_prod_source_cond GROUP BY a.batch_no, a.batch_weight,a.id,h.buyer_id, a.color_id, a.extention_no, b.item_description, b.prod_id, b.width_dia_type,
						f.shift_name,a.total_trims_weight, f.production_date,f.process_end_date, f.end_hours, f.floor_id,f.hour_unload_meter, f.water_flow_meter,
						f.end_minutes, f.machine_id, f.load_unload_id,fabric_type,a.booking_without_order,a.booking_no, f.result,f.remarks
						)
						";
						// echo  $sql_dt;
						$tot_trim_batch_qnty=0;
						$sql_d=sql_select($sql_dt);$total_batch_qty2=0;$trims_total_batch_qty=0;$sum_trims_check_array=array();
						$buyer_trims_self_arr=array();$z=1;
						foreach($sql_d as $row)
						{
							$buyer_trims_self_arr[$row[csf('buyer_name')]]['trims']+=$row[csf('total_trims_weight')];
							$buyer_trims_self_arr[$row[csf('buyer_name')]]['qty']+=$row[csf('batch_qnty')];
							if($row[csf('batch_qnty')]>0)
							{
								// echo $row[csf('batch_qnty')].'ddd';
								$total_batch_qty2+=$row[csf('batch_qnty')];
							}
							if($row[csf('trim_batch_qnty')]>0)
							{
								$tot_trim_batch_qnty+=$row[csf('trim_batch_qnty')];
							}
							$batch_no=$row[csf('id')];
							if (!in_array($batch_no,$sum_trims_check_array))
							{ 
								$z++;
								$sum_trims_check_array[]=$batch_no;
								$sumtot_trim_qty=$row[csf('total_trims_weight')];
							}
							else
							{
								$sumtot_trim_qty=0;
							}
							$trims_total_batch_qty+=$sumtot_trim_qty;
						}
						unset($sql_d);
							 
						$k=1;$total_batch_qty=0;$tot_batch_per=0;
						foreach($buyer_trims_self_arr as $key=>$val)
						{
							//$trims_qty=$party_batch_arr[$key]['trims_weight'];
							$trims_qty_sum=$val['trims'];


							$k; 
							$buyer_arr[$key];
							number_format($val['qty']+$trims_qty_sum,2,'.',''); //$total_batch_qty+=$val['qty']+$trims_qty_sum; 
							$batch_per=(($val['qty']+$trims_qty_sum)/$total_batch_qty2)*100;  number_format($batch_per,2,'.','').'%'; 
							$tot_batch_per+=$batch_per;
							$k++;
						}
						?>
						<!-- kaiyum subcontact batch (shade match)-->
                        <td valign="top">
                        	<table style="width:300px;border:1px solid #000;margin-left:40px;" align="center"  class="rpt_table" rules="all" >
	                           	<thead>
	                             	<tr>
	                               		<th colspan="5">Re Process Summary</th>
	                             	</tr>
	                             	<tr>
	                               		<th>SL</th>
	                               		<th>Buyer</th>
	                               		<th>Batch Total</th>
	                               		<th>%</th>
	                             	</tr>
	                           	</thead>
	                           	<tbody>
	                            <?
	                            $k=1;$total_batch_qty_reproc=0;$tot_batch_per_re=0;
	                            foreach($buyer_re_process_arr as $key=>$val)
								{
									$trims_qty_sum=$val['trims']; 
									$total_batch_qty_reproc+=$val['re_qty']+$trims_qty_sum;
								}
								foreach($buyer_re_process_arr as $key=>$val)
								{
									if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									//$trims_qty=$party_batch_arr[$key]['trims_weight'];
									$trims_qty_sum=$val['trims']; //total_batch_qty_re
									$total_batch_qty_reprocess=$total_batch_qty_re_without+$total_batch_qty_re_dyeing;
									?>
		                            <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;">
		                               <td><? echo $k; ?></td>
		                               <td><? echo $buyer_arr[$key] ?></td>
		                               <td align="right"><? echo number_format($val['re_qty']+$trims_qty_sum,2,'.','');  ?></td>
		                               <td align="right"><? $batch_per=(($val['re_qty']+$trims_qty_sum)/$total_batch_qty_reproc)*100; echo number_format($batch_per,2,'.','').'%'; ?></td>
		                            </tr>
		                             <?
									$tot_batch_per_re+=$batch_per;
									$k++;
								}
								?>
	                           </tbody>
	                           	<tfoot>
	                             	<tr>
	                               		<th colspan="2" align="right">Total </th>
	                               		<th align="left"><b><? echo number_format($total_batch_qty_reproc,2,'.','');?></b></th>
	                               		<th align="right"><? echo number_format($tot_batch_per_re,2,'.','').'%'; ?></th>
	                             	</tr>
	                           	</tfoot>
                         	</table>
                        </td>
                        <!-- AKH 2 Re Process Summary End-->

                        <!-- AKH 2 Summary Total(Shade Match) Start-->
			 			<?
						if($subcn_batch_ids!='')
						{
							$sql_sub="select d.party_id, SUM(b.batch_qnty) AS sub_batch_qnty from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where f.batch_id=a.id $companyCond $workingCompany_name_cond2 and  a.entry_form=36 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=38 and f.batch_id=b.mst_id and f.load_unload_id=2 and f.result=1  and a.batch_against in(1) and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  $dates_com $sub_job_cond $batch_num $sub_buyer_cond $suborder_no $year_cond $machine_cond  $result_name_cond_summary $cbo_prod_type_cond $cbo_prod_source_cond GROUP BY d.party_id ";
					   		$sql_data_sub=sql_select($sql_sub);
					   	}
								
						$sub_party_batch_arr=array();$tot_sub_batch_qty=0; 
						foreach($sql_data_sub as $row)
						{
							$sub_party_batch_arr[$row[csf('party_id')]]['qty']=$row[csf('sub_batch_qnty')];
							$sub_party_batch_arr[$row[csf('party_id')]]['buyer']=$row[csf('party_id')];
							$tot_sub_batch_qty+=$row[csf('sub_batch_qnty')];
						}
						$p=1;$total_sub_batch_qty=0;$total_batch_per_sub=0;
						foreach($sub_party_batch_arr as $id=>$sval)
						{
							$p;
							$buyer_arr[$id];
							number_format($sub_party_batch_arr[$id]['qty'],2,'.',''); $total_sub_batch_qty+=$sub_party_batch_arr[$id]['qty'];
							$batch_per_sub=($sub_party_batch_arr[$id]['qty']/$tot_sub_batch_qty)*100; number_format($batch_per_sub,2,'.','').'%';
							$total_batch_per_sub+=$batch_per_sub;
							$p++;
						}
					 	// for sample
						if ($cbo_result_name==0 || $cbo_result_name==1) $result_name_cond_summary=" and f.result=1"; else $result_name_cond_summary=" ";
							
						$sql_sam="(select d.buyer_name,a.total_trims_weight as trims_weight,SUM(b.batch_qnty) AS batch_qnty  from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where f.batch_id=a.id $companyCond $workingCompany_name_cond2 and   a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and f.result=1 and a.batch_against in(3) and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0 $sam_batch_cond_for_in $dates_com $jobdata $batch_num $buyerdata $order_no $file_cond $ref_cond $year_cond  $machine_cond  $result_name_cond_summary $cbo_prod_type_cond $cbo_prod_source_cond group by d.buyer_name,a.total_trims_weight )
						union
						(
						select h.buyer_id as buyer_name,a.total_trims_weight as trims_weight,SUM(b.batch_qnty) AS batch_qnty from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h where f.batch_id=a.id $companyCond $workingCompany_name_cond2  and h.booking_no=a.booking_no  and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and f.result=1 and a.batch_against in(3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  f.status_active=1 and f.is_deleted=0 $sam_batch_cond_for_in $dates_com  $batch_num  $year_cond    $result_name_cond_summary  $machine_cond $cbo_prod_type_cond $buyerdata2 $cbo_prod_source_cond
						GROUP BY h.buyer_id,a.total_trims_weight)
						union
						(
						select d.buyer_name,SUM(b.trims_wgt_qnty) AS trims_weight, 0 as batch_qnty from pro_batch_create_mst a,pro_batch_trims_dtls b, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g where a.id=b.mst_id and f.batch_id=a.id and f.batch_id=b.mst_id and a.entry_form=136 and g.id=f.machine_id and  f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(3) and d.job_no=a.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  $companyCond  $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond   $result_name_cond  $machine_cond   $cbo_prod_source_cond $cbo_prod_type_cond  GROUP BY d.buyer_name )
						";
						$sql_data_sam=sql_select($sql_sam);
								
						$samp_party_batch_arr=array(); $tot_batch_qty_sam=0;$tot_batch_trims_qty_sam=0;$tot_batch_qty_sam_summary=0;
						foreach($sql_data_sam as $row)
						{
							$samp_party_batch_arr[$row[csf('buyer_name')]]['qty']+=$row[csf('batch_qnty')];
							$samp_party_batch_arr[$row[csf('buyer_name')]]['trims_qty']+=$row[csf('trims_weight')];
							$samp_party_batch_arr[$row[csf('buyer_name')]]['buyer']=$row[csf('buyer_name')];
							$tot_batch_qty_sam+=$row[csf('batch_qnty')]+$row[csf('trims_weight')];
							$tot_batch_qty_sam_summary+=$row[csf('trims_weight')];
							//$tot_batch_trims_qty_sam+=$row[csf('trims_weight')];
						}
						$k=1;$total_batch_qty_sam=0;$tot_batch_per_sam=0;
						foreach($samp_party_batch_arr as $key=>$val)
						{
							$smp_trims=$samp_party_batch_arr[$key]['trims_qty'];
							$k;
							$buyer_arr[$key]; 
							number_format($samp_party_batch_arr[$key]['qty']+$smp_trims,2,'.',''); $total_batch_qty_sam+=$samp_party_batch_arr[$key]['qty'];//+$smp_trims;
							$batch_per_sam=(($samp_party_batch_arr[$key]['qty']+$smp_trims)/$tot_batch_qty_sam)*100; number_format($batch_per_sam,2,'.','').'%';

							$tot_batch_per_sam+=$batch_per_sam;
							$k++;
						}
								
						if ($cbo_result_name==0 || $cbo_result_name==1) $result_name_cond_summary=" and f.result=1"; else $result_name_cond_summary=" ";
						if($batch_idss!='')
						{
							$sql_result_re="select d.buyer_name,SUM(b.batch_qnty) AS batch_qnty,a.total_trims_weight from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g 
							where f.batch_id=a.id $companyCond $workingCompany_name_cond2 and  a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2  and a.batch_against in(2) and f.result in(1)  and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  f.status_active=1 and f.is_deleted=0 $batch_cond_for_in $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond  $machine_cond  $result_name_cond_summary $cbo_prod_type_cond  $cbo_prod_source_cond 
							GROUP BY d.buyer_name ,a.total_trims_weight ";
								$sql_re=sql_select($sql_result_re);$total_batch_qty_re=0;
									
							$buyer_re_process_arr=array();
							foreach($sql_re as $row)
							{
								$buyer_re_process_arr[$row[csf('buyer_name')]]['re_qty']+=$row[csf('batch_qnty')];
								$buyer_re_process_arr[$row[csf('buyer_name')]]['trims']+=$row[csf('total_trims_weight')];
								$total_batch_qty_re+=$row[csf('batch_qnty')]+$row[csf('total_trims_weight')];
							}
							unset($sql_re);
						}
						?>
						<td valign="top">
							<table cellpadding="0"  width="300" cellspacing="0" align="center"  class="rpt_table" rules="all" border="1" style="margin-left:40px;">
								<thead>
									<tr>
										<th colspan="6">Summary Total(Shade Match)</th>
									</tr>
									<tr>
										<th>Self Batch</th>
										<th>Sample Batch</th>
	                                    <th>Trims Weight</th>
	                                    <th>SubCon Batch</th>
										<th>Total</th>
									</tr>
								</thead>
								<tbody>
								 	<tr bgcolor="#FFFFFF"  style="cursor:pointer;">
									  	<td width="30"><? echo number_format($total_batch_qty2,2); ?></td>
	                                  	<td width="30"><? echo number_format($total_batch_qty_sam,2); ?></td>
	                                  
	                                 	<!-- trims weight --> 
	                                   	<td width="30"><? 
									   	$totaltrimswt=($trims_total_batch_qty+$tot_batch_qty_sam_summary+$tot_trim_batch_qnty);
									   	echo number_format($totaltrimswt,2);
									   
									    ?></td>
	                                  
									  	<td width="30"><? echo number_format($total_sub_batch_qty,2); ?></td>
									  	<td width="30"><?  echo number_format($grand_total=$total_batch_qty2+$total_sub_batch_qty+$total_batch_qty_sam+$totaltrimswt,2); ?></td>
							  		</tr>
								  	<tr bgcolor="#E9F3FF"> 
										<td><?  echo number_format(($total_batch_qty2/$grand_total)*100,2).'%'; ?></td>
										<td><?  echo number_format(($total_batch_qty_sam/$grand_total)*100,2).'%'; ?></td>

										<!-- trims weight --> 
										<td title="AKH2:<? echo $totaltrimswt.'/'.$grand_total; ?>"><?  echo number_format(($totaltrimswt/$grand_total)*100,2).'%';
										//echo number_format(($trims_total_batch_qty/$grand_total)*100,2).'%'; ?></td>

										<td><?  echo number_format(($total_sub_batch_qty/$grand_total)*100,2).'%'; ?></td>
										<td><?  echo '100%'; ?></td>
								  	</tr>
								</tbody>
							</table>
					   	</td>
					   	<!-- AKH-2 Summary Total(Shade Match) End -->

	                   <!-- START Total Dyeing Production Summary -->
	                   <td valign="top">
							 
					   </td>
	                   <!-- END Total Dyeing Production Summary -->
				  </tr>
				</table>
			 	</div>
			 	<br />
			 	<? 
			}
		}
		// echo " AKH-2 All Summary End";die;
		 
		// ============ AKH-2 Details Part data show Start ================
		if (count($batchdata)>0) // Batch Detail
		{
			$group_by=str_replace("'",'',$cbo_group_by);

			?>
			<div align="left" style="float:left; clear:both;">
             	
			<table class="rpt_table" width="1420" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
              <caption> <b>Batch Detail  </b></caption>
                <thead>
                    <tr>
                        <th width="30">SL</th>
                       <? if($group_by==3 || $group_by==0){ ?>
                    	 <th width="80">M/C No</th>
                   		 <? } ?>
                       
                        <th width="60">File No</th>
                        <th width="70">Ref. No</th>
                        <th width="100">Buyer</th>
                       
                        <th width="100">Construction</th>
                        <th width="70">Dia/Width Type</th>
                        <th width="80">Color Name</th>
                        <th width="90">Batch No</th>
                        <th width="40">Extn. No</th>
                        <th width="70">Dyeing Qty.</th>
                        <th width="70">Trims Wgt.</th>
                       
                        <th width="75">Load Date & Time</th>
                        <th width="75">UnLoad Date Time</th>
                        <th width="60">Time Used</th>
                        <th width="100">Dyeing Fab. Type</th>
                        <th width="100">Result</th>
                        <th width="">Remark</th>
                    </tr>
                </thead>
			</table>
			<div style=" max-height:350px; width:1440px; overflow-y:scroll; float:left; clear:both;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1420" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                <tbody>
	                <? 
					$tot_sum_trims_qnty=0;
	                $i=1; $btq=0; $k=1;$z=1;$total_water_cons_load=0;$total_water_cons_unload=0;
	                $batch_chk_arr=array(); $group_by_arr=array();$tot_trims_qnty=0;$trims_check_array=array();

	                foreach($batch_wise_arr as $batch)
					{
						if ($i%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
						if($group_by!=0)
						{
							if($group_by==1)
							{
								$group_value=$batch['floor_id'];
								$group_name="Floor";
								$group_dtls_value=$floor_arr[$batch['floor_id']];
							}
							else if($group_by==2)
							{
								$group_value=$batch['shift_name'];
								$group_name="Shift";
								$group_dtls_value=$shift_name[$batch['shift_name']];
							}
							else if($group_by==3)
							{
								$group_value=$batch['machine_id'];
								$group_name="machine";
								$group_dtls_value=$machine_arr[$batch['machine_id']];
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
	                                    <td width="130" colspan="6" style="text-align:right;"><strong>Sub. Total : </strong></td>
	                                    <td width="70"><? //echo number_format($batch_qnty,2); ?></td>
	                                    <td width="40"><? //echo number_format($batch_qnty_trims,2); ?></td>
										
										<td width="70" title="This sub total only for shade matched(without extention)"><? echo number_format($batch_qnty,2); ?></td>
	                                    <td width="70" title="This sub total only for shade matched(without extention)"><? echo number_format($batch_qnty_trims,2); ?></td>
	                                    <td width="75" colspan="6">&nbsp;</td>
	                                    
	                                </tr>                                
									<?
									unset($batch_qnty);unset($batch_qnty_trims);
								}
								?>
								<tr bgcolor="#EFEFEF">
									<td colspan="18"><b style="margin-left:0px"><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
								</tr>
								<?
								$group_by_arr[]=$group_value;            
								$k++;
							}					
						}

						$order_id=$batch['po_id'];
						$load_unid=$batch['load_unload_id'];
						$entry_form=$batch['entry_form'];
						$batch_weight=$batch['batch_weight'];
						$water_cons_unload=$batch['water_flow_meter'];
						$water_cons_load=$water_flow_arr[$batch['id']];
						$load_hour_meter=$load_hour_meter_arr[$batch['id']];
						//echo $water_cons_load.'=='.$water_cons_unload;
						$water_cons_diff=($water_cons_unload-$water_cons_load)/$batch_weight*1000;
						$batch['item_description']=rtrim($batch['item_description'],"**");
						//$desc=explode(",",$batch['item_description']); 
						//$item_D="Lycra 1, Cotton 95% Spandex 5%, 180, 72**Jersey 2, Cotton 95% Spandex 5%, 180, 72";
						$item_desc_arr=array_unique(explode("**",$batch['item_description']));
						$item_desc="";
						foreach ($item_desc_arr as $key => $value) 
						{
							$item_desc2=array_unique(explode(",",$value));
							//echo $item_desc2[0].'<br>';
							//echo "<pre>";print_r($item_desc2);
							$item_desc.=($item_desc=="") ? $item_desc2[0] : ",".$item_desc2[0] ;
						}
						//$item_desc=implode(",",array_unique(explode(",",$item_desc)));
						//echo $item_desc.'<br>';

						// $width_dia="1**2**1";
						$width_dia_type_arr=array_unique(explode(",",$batch['width_dia_type']));
						$width_dia_type="";
						foreach ($width_dia_type_arr as $key => $width_dia_type_id) 
						{
							$width_dia_type.=($width_dia_type=="") ? $fabric_typee[$width_dia_type_id] : ",".$fabric_typee[$width_dia_type_id] ;
						}
						//echo $width_dia_type.'<br>';

						$po_number=implode(",",array_unique(explode(",",$batch['po_number'])));
						$file_no=implode(",",array_unique(explode(",",$batch['file_no']))); 
						$ref_no=implode(",",array_unique(explode(",",$batch['grouping']))); 

						$batch_no=$batch['id'];
						if (!in_array($batch_no,$trims_check_array))
						{ 
							$z++;
							$trims_check_array[]=$batch_no;
							$tot_trim_qty=$batch['total_trims_weight'];
						}
						else
						{
							$tot_trim_qty=0;
						}
						$batch_against=$batch['batch_against'];
						if($batch_against==2) $bg_color_td="bolder";else $bg_color_td="";
						if($entry_form==136)
						{
						 	$tot_trim_qty=$tot_trim_qty+$batch['batch_qnty'];
						}
						else $tot_trim_qty=$tot_trim_qty;
						?>
						<tr style="font-weight:<? echo $bg_color_td; ?>"   bgcolor="<? echo $bgcolor_dyeing; ?>"  id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor_dyeing; ?>')">
	                        <td  width="30"><? echo $i; ?></td>
	                        
	                         <? if($group_by==3 || $group_by==0){ ?>
	                        <td align="center" width="80"><p><? echo $machine_arr[$batch['machine_id']]; ?></p></td>
	                        <?
							 }
							 if($group_by==2 || $group_by==0){ ?>
	                       
	                        <? } if($group_by==1 || $group_by==0){ ?>
	                    
	                        <? } ?>
	                        <td align="center" width="60"><p><? echo chop($file_no,','); ?></p></td>
	                        <td align="center" width="70"><p><? echo chop($ref_no,','); ?></p></td>
	                        <td width="100"><p><? echo $buyer_arr[$batch['buyer_name']]; ?></p></td>
	                        
	                        <td width="100"><div style="width:100px; word-wrap:break-word;" title="ItemDesc=<?  echo implode(",",$item_desc_arr);?>"><? echo chop($item_desc,','); ?></div></td>
	                        <td width="70"><p><? echo chop($width_dia_type,','); ?></p></td>
	                        <td width="80"><p><? echo $color_library[$batch['color_id']]; ?></p></td>
	                        <td width="90" title="<? echo $batch['id']; ?>"><p><? echo $batch['batch_no']; ?></p></td>
	                        <td width="40"><p><? echo $batch['extention_no']; ?></p></td>
	                        <td title="<?  if($batch_against==3) echo "Sample";?>" align="right" width="70"><? 
	                        if($entry_form!=136) 
							{
								$batch_qty=$batch['batch_qnty'];
							}
							else {
								$batch_qty=0;
							}
							echo number_format($batch_qty,2); ?></td>
	                       	<td align="right" width="70"><? echo number_format($tot_trim_qty,2);  ?></td>
	                        <td width="75"><p><? $load_t=$load_hr[$batch['id']].':'.$load_min[$batch['id']]; echo  ($load_date[$batch['id']] == '0000-00-00' || $load_date[$batch['id']] == '' ? '' : change_date_format($load_date[$batch['id']])).' <br> '.$load_t;
							//echo $batch['id'];
	                        ?></p></td>
	                        <td width="75"><p><? $hr=strtotime($unload_date,$load_t); $min=($batch['end_minutes'])-($load_min[$batch['id']]);
							if($load_unid==2) echo  ($batch['process_end_date'] == '0000-00-00' || $batch['process_end_date'] == '' ? '' : change_date_format($batch['process_end_date'])).'<br>'.$unload_time=$batch['end_hours'].':'.$batch['end_minutes'];
							$unloaded_date=change_date_format($batch['process_end_date']);
							 //$unloadd=change_date_format($batch[csf('process_end_date')]).'<br>'.$unload_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];?></p></td>
	                        <td align="center" width="60">
								<?  
	                            $new_date_time_unload=($unloaded_date.' '.$unload_time.':'.'00');
	                            $new_date_time_load=($load_date[$batch['id']].' '.$load_t.':'.'00');
	                            $total_time=datediff(n,$new_date_time_load,$new_date_time_unload);
								//echo $new_date_time_unload.'=='.$new_date_time_load;
	                            if($load_unid==2) echo floor($total_time/60).":".$total_time%60;
	                            //echo ($total_time/60 - $total_time%3600/60)/60 .':'.$total_time%3600/60;
	                            ?>
	                        </td>
	                        <td align="center" width="100"><p><? 
	                        $fab_type="";
							if($batch['fabric_type']==1)
							{
								$fab_type="Cotton";
							}
							else if($batch['fabric_type']==2)
							{
								$fab_type="Polyster";
							}
							else if($batch['fabric_type']==3)
							{
								$fab_type="Lycra";
							}
							else if($batch['fabric_type']==4)
							{
								$fab_type="Both Part";
							}
							else if($batch['fabric_type']==5)
							{

								$fab_type="White";
							}
							else if($batch['fabric_type']==6)
							{
								$fab_type="Wash";
							}
							echo $fab_type;//$fabric_type_for_dyeing[$batch[csf('fabric_type')]]; ?></p> </td>
	                        <td align="center" width="100"><p><? echo $dyeing_result[$batch['result']]; ?></p> </td>
	                        <td align="center"><p><? echo $batch['remarks']; ?></p> </td>
						</tr>
						<? 
						if($batch['result']==1)
						{
							$tot_sum_trims_qnty+=$tot_trim_qty;
						}
						else
						{ 
							$tot_trims_qnty+=0;
						}
						$i++;
						if ($cbo_type==1) 
						{
							$batch_qnty+=$batch_qty;
							$grand_total_batch_qty+=$batch_qty;
							$batch_qnty_trims+=$tot_trim_qty;
						}
						else
						{
							if($batch['result']==1 && empty($batch['extention_no']))
							{
								$batch_qnty+=$batch_qty;
								$grand_total_batch_qty+=$batch_qty;
								$batch_qnty_trims+=$tot_trim_qty;
							}
						}
						
						
						
						
						
						$total_water_cons_load+=$water_cons_load;
						$total_water_cons_unload+=$water_cons_unload;
						//$tot_trims_qnty+=$tot_trim_qty;
						if($batch['result']==1 && empty($batch['extention_no']))
						{
							$tot_trims_qnty+=$tot_trim_qty;
						}
						$trims_summary+=$tot_trim_qty;
						
					} //batchdata froeach end
					if($group_by!=0)
					{
						?>
	                 	<tr class="tbl_bottom">
	                    <td width="30">&nbsp;</td>
	                    <? if($group_by==3 || $group_by==0){ ?>
	                    <td width="80">&nbsp;</td> 
	                     <? } ?>
	                    <td width="130" colspan="8" style="text-align:right;"><strong>Sub. Total : </strong></td>
	                    <td width="70" title="This sub total only for shade matched(without extention)"><? echo number_format($batch_qnty,2); ?></td>
	                    <td width="40" title="This sub total only for shade matched(without extention)"><? echo number_format($batch_qnty_trims,2); ?></td>
						<td width="70"><? //echo number_format($batch_qnty,2); ?></td>
	                    <td width="70"><? //echo number_format($batch_qnty_trims,2); ?></td>
	                    <td width="75" colspan="4">&nbsp;</td>
	                	</tr> 
						<? 
					} ?>            
				</tbody>
	            <tfoot>
	                <tr>
	                    <th width="30">&nbsp;</th>
	                    <? if($group_by==3 || $group_by==0){ ?>

	                    <th width="80">&nbsp;</th>
	                    
	                    <? } ?>
	                   
	                    <th width="130" colspan="8" style="text-align:right;"><strong>Trims Total : </strong></th>
	                    <th width="70"></th>
	                    <th width="70"><? echo number_format($tot_trims_qnty,2); ?></th>
	                    <th colspan="6">  </th>
	                </tr>
	                
	                <tr>
	                    <th width="30">&nbsp;</th>
	                    <? if($group_by==3 || $group_by==0){ ?>
	                    <th width="80">&nbsp;</th>
	                    <? } ?>
	                    
	                    <th width="130" colspan="8" style="text-align:right;"><strong>Grand Total : </strong></th>
	                    <th width="70" title="This Grand total only for shade matched(without extention) + trim total(without extention)"><? echo number_format($grand_total_batch_qty+$tot_trims_qnty,2); ?></th>
	                   
	                    <th colspan="7">&nbsp;  </th>
	                </tr>
	            </tfoot>
        	</table>
			</div>
			</div>
			<? 
		}
		// ============ AKH-2 Details Part data show End ==================
		
		?>
		</fieldset>
       
		<?
	} //Dyeing Production End=============================================
	else if($cbo_type==3) // Daily Right First Time Start
	{
		
		?>
		<div>
		<fieldset style="width:1505px;">
		<div align="center"><strong> <? echo $company_library[$company]; ?> </strong><br> <strong> Daily Right First Time </strong><br>
		<?
			echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
		?>
		</div>
	    <div>
	 		<table cellpadding="0"  width="400" cellspacing="0" align="center" >
	         	<tr>
	             	<td width="400">
	                 	<table cellpadding="0"  width="400" cellspacing="0" align="center"  class="rpt_table" rules="all" border="1">
	                        <thead>
		                    	<tr>
		                        	<th colspan="4"> <p>&nbsp;</p>
		                       	    <p>&nbsp;</p>
		                       	    <p>&nbsp;</p>
		                       	    <p>&nbsp;</p>
		                       	    <p>&nbsp;</p>
		                       	    <p>&nbsp;</p>
		                       	    <p>&nbsp;</p>
		                       	    <p>&nbsp;</p>
									<p>RFT Summary </p>
									</th>
		                        </tr>
		                        <tr><th colspan="3">RFT </th> <th>OK %</th></tr>
	                        </thead>
	                        <?
							
				
							$tot_ltb=$tot_ltb_matched+$tot_ltb_not_matched;
							$ltb_percent=$matched_ltb/$tot_ltb*100;
							$tot_btb=$tot_btb_matched+$tot_btb_not_matched;
							$btb_percent=$tot_btb_matched/$tot_btb*100;
							$tot_rp=$tot_btb_matched+$tot_btb_not_matched+$tot_ltb_matched+$tot_ltb_not_matched;
							$tot_ok=$tot_btb_matched+$tot_ltb_matched;
							$tot_rp_percent=$tot_ok/$tot_rp*100;
							?>
	                        <tr bgcolor="#E9F3FF">
	                        	<td rowspan="2">LTB </td>
	                            <td width="100">L OK </td>
	                            <td align="right"> <? echo number_format($tot_ltb_matched); ?></td>
	                            <td rowspan="2" align="right"><? echo number_format($ltb_percent,2).'%'; ?> </td>
	                        </tr>
	                        <tr>
	                            <td width="100">L Not OK </td>
	                            <td width="60" align="right"><? echo number_format($tot_ltb_not_matched); ?></td>
	                        </tr>
	                         <tr bgcolor="#FFFFFF">
	                        	<td rowspan="2">BTB </td>
	                            <td width="100">B OK </td>
	                            <td align="right"> <? echo $tot_btb_matched; ?></td>
	                            <td rowspan="2" align="right"><? echo number_format($btb_percent,2).'%'  ?></td>
	                        </tr>
	                        <tr>
	                            <td width="100">B Not OK </td>
	                            <td width="60" align="right"><? echo number_format($tot_btb_not_matched); ?></td>
	                        </tr>
	                         <tfoot>
	                         <tr>
	                        	<th> </th>
	                            <th colspan="2">R/P</th>
	                            <th align="right"> <? echo number_format($tot_rp_percent,2).'%'; ?></th>
	                         </tr>
	                        </tfoot>
	                        </table>
	                </td>
	            </tr>  
	        </table>
	    </div>
	    <br>
	    <div>
	    <div align="left"> <b>Self batch </b><br>
	    <Strong>Bulk To Bulk</Strong>
	    </div>
		<table class="rpt_table" width="1510" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="80">Prod. Date</th>
					<th width="80">M/C No</th>
					<th width="100">Buyer</th>
					<th width="80">Job</th>
					<th width="90">PO No</th>
					<th width="100">Construction</th>
					<th width="80">Dia/Width Type</th>
					<th width="80">Color Name</th>
					<th width="90">Batch No</th>
					<th width="70">Dyeing Qty.</th>
					<th width="50">LTB/BTB</th>
					<th width="100">Unload Date & Time</th>
					<th width="60">Time Used</th>
					<th width="100">RFT</th>
					<th width="100">Remark</th>
					<th width="100">Machine Utilization</th>
					<th>Lot No</th>
				</tr>
			</thead>
		</table>
		<div style=" max-height:350px; width:1510px; overflow-y:scroll;" id="scroll_body">
		<table class="rpt_table" id="table_body" width="1490" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
			<tbody>
				<? 
				$i=1;
				$f=0;
				$btq=0;
				$batch_chk_arr=array();
				if (count($batchdata)>0)
			 	{
					foreach($batch_wise_arr as $batch)
					{
						if ($i%2==0)  
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						$order_id=$batch['po_id'];
						$color_id=$batch['color_id'];
						if($batch['result']==1)
						{
							$shade="Shade Matched";	
						}
						else
						{
						$shade="Shade Not Matched";		
						}
						$desc=explode(",",$batch['item_description']); 
						$po_number=implode(",",array_unique(explode(",",$batch['po_number'])));
						

						$item_desc_arr=array_unique(explode("**",$batch['item_description']));
						$item_desc="";
						foreach ($item_desc_arr as $key => $value) 
						{
							$item_desc2=array_unique(explode(",",$value));
							//echo $item_desc2[0].'<br>';
							//echo "<pre>";print_r($item_desc2);
							$item_desc.=($item_desc=="") ? $item_desc2[0] : ",".$item_desc2[0] ;
						}
						//$item_desc=implode(",",array_unique(explode(",",$item_desc)));
						//echo $item_desc.'<br>';

						// $width_dia="1**2**1";
						$width_dia_type_arr=array_unique(explode(",",$batch['width_dia_type']));
						$width_dia_type="";
						foreach ($width_dia_type_arr as $key => $width_dia_type_id) 
						{
							$width_dia_type.=($width_dia_type=="") ? $fabric_typee[$width_dia_type_id] : ",".$fabric_typee[$width_dia_type_id] ;
						}
						//echo $width_dia_type.'<br>';
						$po_id_arr=array_unique(explode(",",$batch['po_id']));

						$prod_id_arr=array_unique(explode(",",$batch['prod_id']));
						$yarn_lot="";
						foreach ($prod_id_arr as $key => $prod_Ids) 
						{
							//print_r($key).'<br>';
							//echo $prod_Ids.'<br>';
							foreach ($po_id_arr as $po_id => $po_ids)
							{
								//print_r($po_id).'<br>';
								//echo $po_ids.'<br>';
								$yarn_lot.=($yarn_lot=="") ? $yarn_lot_arr[$prod_Ids][$po_ids] : ",".$yarn_lot_arr[$prod_Ids][$po_ids];
							}
						}
						//echo chop($yarn_lot,',').'<br>';

						?>
						<tr bgcolor="<? echo $bgcolor; ?>"  id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
							<td width="30"><? echo $i; ?></td>
							<td width="80" title="<? echo change_date_format($batch['production_date']); ?>"><p><? echo change_date_format($batch['production_date']); $unload_date=$batch['process_end_date']; ?></p></td>
							<td  align="center" width="80" title="<? echo $machine_arr[$batch['machine_id']]; ?>"><p><? echo $machine_arr[$batch['machine_id']]; ?></p></td>
							<td  width="100" title="<? echo $buyer_arr[$batch['buyer_name']]; ?>"><p><? echo $buyer_arr[$batch['buyer_name']]; ?></p></td>
							<td  width="80" title="<? echo $batch['job_no_prefix_num']; ?>"><p><? echo $batch['job_no_prefix_num']; ?></p></td>
							<td width="90" title="<? echo $po_number; ?>"><p><? echo chop($po_number,','); ?></p></td>
							<td  width="100" title="<? echo $batch['item_description'];?>"><p><? echo chop($item_desc,','); ?></p></td>
							<td  width="80" title="<? echo $width_dia_type; ?>"><p><? echo chop($width_dia_type,','); ?></p></td>
							<td  width="80" title="<? echo $color_library[$batch['color_id']]; ?>"><p><? echo $color_library[$batch['color_id']]; ?></p></td>
							<td  align="center" width="90" title="<? echo $batch['batch_no']; ?>"><p><? echo $batch['batch_no']; ?></p></td>
							<td  align="right" width="70" title="<? echo $batch['batch_qnty']; ?>"><p><? echo $batch['batch_qnty']; ?></p></td>
							<td align="center" width="50" title="<? echo $ltb_btb[$batch['ltb_btb_id']]; ?>"><? echo $ltb_btb[$batch['ltb_btb_id']]; ?></td>
							<td width="100" align="center" title="<? //echo $load_hr[$batch[csf('id')]].':'.$load_min[$batch[csf('id')]]; ?>"><p><?  echo ($batch['process_end_date'] == '0000-00-00' || $batch['process_end_date']== '' ? '' : change_date_format($batch['process_end_date'])).' <br> '.$batch['end_hours'].':'.$batch['end_minutes']; ?></p></td>
							  <td align="center" width="60"><? $hr=$batch['end_hours']-$load_hr[$batch['id']]; $min=$batch['end_minutes']-$load_min[$batch['id']]; //echo  $hr.':'.$min;
							$load_t=$load_hr[$batch['id']].':'.$load_min[$batch['id']];
							$unload_t=$batch['end_hours'].':'.$batch['end_minutes'];
							$new_date_time_unload=($unload_date.' '.$unload_t.':'.'00');
							$new_date_time_load=($load_date[$batch['id']].' '.$load_t.':'.'00');
						   //$unload_time=strtotime($unload_date,$unload_time);
							//$load_time=strtotime($load_date,$load_t);
							$total_time=datediff(n,$new_date_time_load,$new_date_time_unload);
							echo floor($total_time/60).":".$total_time%60;
							//echo ($total_time/60 - $total_time%3600/60)/60 .':'.$total_time%3600/60;
								?></td>
							<td width="100" title="<? echo $shade;  ?>"><p><?  echo $shade; ?></p></td>
							<td width="100" title="<? ?>"><p><? echo  $batch['remarks']; ?></p></td>
						  
							<td align="center" width="100" title="<?  $utiliz=$batch['batch_qnty']/$m_capacity[$batch['machine_id']]*100; echo number_format($utiliz,2).'%'; ?>"><?  $utiliz=$batch['batch_qnty']/$m_capacity[$batch['machine_id']]*100; echo number_format($utiliz,2).'%'; ?> </td>
							<td align="left" title="<? echo $batch['prod_id'].'='.$batch['po_id'];   ?>"><p><? //echo chop($yarn_lot,','); 
							echo $yarn_lot_arr[$batch['prod_id']][$batch['po_id']]; ?></p></td>
						</tr>
						<? 
						$i++;
						$btq+=$batch['batch_qnty'];
					} //batchdata froeach
	 				?>
					<tr bgcolor="#CCCCCC">
						<td colspan="10" align="right"><Strong>BTB Total:</Strong> <? //echo $b_qty; ?> </td>
						<td align="right"><? echo number_format($btq,2); ?>&nbsp;</td>
						<td colspan="7">&nbsp;</td>
					</tr>
					<tr bgcolor="#C2DCFF">
						<td colspan="10"><strong>Lab To Bulk</strong></td><td colspan="8"> </td>
					</tr>
					<?
				}
				?>
	              
	            <?
				if (count($batchdata_ltb)>0)
				{
					$d=1;
					foreach($ltb_batch_wise_arr as $batch_ltb)
					{
						if ($d%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$order_id=$batch_ltb['po_id'];
						$color_id=$batch_ltb['color_id'];
						if($batch_ltb['result']==1)
						{
							$shade="Shade Matched";	
						}
						else
						{
							$shade="Shade Not Matched";
						}
						$desc=explode(",",$batch_ltb['item_description']); 
						$po_number=implode(",",array_unique(explode(",",$batch_ltb['po_number']))); 

						$item_desc_arr=array_unique(explode("**",$batch['item_description']));
						$item_desc="";
						foreach ($item_desc_arr as $key => $value) 
						{
							$item_desc2=array_unique(explode(",",$value));
							//echo $item_desc2[0].'<br>';
							//echo "<pre>";print_r($item_desc2);
							$item_desc.=($item_desc=="") ? $item_desc2[0] : ",".$item_desc2[0] ;
						}
						//$item_desc=implode(",",array_unique(explode(",",$item_desc)));
						//echo $item_desc.'<br>';

						// $width_dia="1**2**1";
						$width_dia_type_arr=array_unique(explode(",",$batch['width_dia_type']));
						$width_dia_type="";
						foreach ($width_dia_type_arr as $key => $width_dia_type_id) 
						{
							$width_dia_type.=($width_dia_type=="") ? $fabric_typee[$width_dia_type_id] : ",".$fabric_typee[$width_dia_type_id] ;
						}
						//echo $width_dia_type.'<br>';
						$po_id_arr=array_unique(explode(",",$batch['po_id']));

						$prod_id_arr=array_unique(explode(",",$batch['prod_id']));
						$yarn_lot="";
						foreach ($prod_id_arr as $key => $prod_Ids) 
						{
							//print_r($key).'<br>';
							//echo $prod_Ids.'<br>';
							foreach ($po_id_arr as $po_id => $po_ids)
							{
								//print_r($po_id).'<br>';
								//echo $po_ids.'<br>';
								$yarn_lot.=($yarn_lot=="") ? $yarn_lot_arr[$prod_Ids][$po_ids] : ",".$yarn_lot_arr[$prod_Ids][$po_ids];
							}
						}
						//echo chop($yarn_lot,',').'<br>';

						?>
						<tr bgcolor="<? echo $bgcolor; ?>" id="trbluk_<? echo $d; ?>"  onclick="change_color('trbluk_<? echo $d; ?>','<? echo $bgcolor; ?>')">
							<td width="30"><? echo $d; ?></td>
							<td width="80" title="<? echo change_date_format($batch_ltb['production_date']); ?>"><p><? echo change_date_format($batch_ltb['production_date']);$unload_date1=$batch_ltb['process_end_date']; ?></p></td>
							<td  align="center" width="80" title="<? echo $machine_arr[$batch_ltb['machine_id']]; ?>"><p><? echo $machine_arr[$batch_ltb['machine_id']]; ?></p></td>
							<td  width="100" title="<? echo $buyer_arr[$batch_ltb['buyer_name']]; ?>"><p><? echo $buyer_arr[$batch_ltb['buyer_name']]; ?></p></td>
							<td  width="80" title="<? echo $batch['job_no_prefix_num']; ?>"><p><? echo $batch['job_no_prefix_num']; ?></p></td>
							<td width="90" title="<? echo $po_number; ?>"><p><? echo $po_number; ?></p></td>
							<td  width="100" title="<? echo $batch['item_description'];?>"><p><? echo $item_desc; ?></p></td>
							<td  width="80" title="<? echo $fabric_typee[$batch['width_dia_type']]; ?>"><p><? echo $width_dia_type; ?></p></td>
							<td  width="80" title="<? echo $color_library[$batch_ltb['color_id']]; ?>"><p><? echo $color_library[$batch_ltb['color_id']]; ?></p></td>
							<td  align="center" width="90" title="<? echo $batch_ltb['batch_no']; ?>"><p><? echo $batch_ltb['batch_no']; ?></p></td>
							<td  align="right" width="70" title="<? echo $batch_ltb['batch_qnty']; ?>"><p><? echo $batch_ltb['batch_qnty']; ?></p></td>
							<td align="center" width="50" title="<? echo $ltb_btb[$batch_ltb['ltb_btb_id']]; ?>"><? echo $ltb_btb[$batch_ltb['ltb_btb_id']]; ?></td>
							<td width="100" align="center" title="<? //echo $load_hr[$batch[csf('id')]].':'.$load_min[$batch[csf('id')]]; ?>"><p><?   echo ($batch_ltb['process_end_date'] == '0000-00-00' || $batch_ltb['process_end_date']== '' ? '' : change_date_format($batch_ltb['process_end_date'])).'<br>'.$batch_ltb['end_hours'].':'.$batch_ltb['end_minutes']; ?></p></td>
							   <td align="center" width="60" title="<? echo  $hr.':'.$min;  ?>"><? $hr=$batch_ltb['end_hours']-$load_hr[$batch_ltb['id']]; $min=$batch_ltb['end_minutes']-$load_min[$batch_ltb['id']]; //echo  $hr.':'.$min; 
							$load_time=$load_hr[$batch_ltb['id']].':'.$load_min[$batch_ltb['id']];
							$unload_time=$batch_ltb['end_hours'].':'.$batch_ltb['end_minutes'];
							$new_date_time_unload1=($unload_date1.' '.$unload_time.':'.'00');
							$new_date_time_load1=($load_date[$batch_ltb['id']].' '.$load_time.':'.'00');
							$total_time1=datediff(n,$new_date_time_load1,$new_date_time_unload1);
							echo floor($total_time1/60).":".$total_time1%60;
							//echo ($total_time/60 - $total_time%3600/60)/60 .':'.$total_time%3600/60;
								?></td>
							<td width="100" title="<? echo $shade ?>"><p><?  echo $shade; ?></p></td>
							<td width="100" title="<? ?>"><p><? echo  $batch_ltb['remarks'];  ?></p></td>
							<td align="center" width="100" title="<? //echo $hr.':'.$min;   ?>"><?  $utiliz=$batch_ltb['batch_qnty']/$m_capacity[$batch_ltb['machine_id']]*100; 		
							echo number_format($utiliz,2).'%'; ?> </td>
							<td align="left" title="<? echo $batch_ltb['prod_id'].'*'.$batch_ltb['po_id']; ?>"><p><? //echo chop($yarn_lot,','); 
							echo $yarn_lot_arr[$batch_ltb['prod_id']][$batch_ltb['po_id']]; ?></p> </td>
						</tr>
						<?			
						$d++;
						$total_ltb+=$batch_ltb['batch_qnty'];
					}
					?>
	                </table>
					<table class="rpt_table" width="1490" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
						<tfoot>
							<tr>
								<th width="30">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<th width="90">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<th width="90">&nbsp;</th>
								<th width="70"><? echo number_format($total_ltb,2); ?></th>
								<th width="50">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="60">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th>&nbsp;</th>
							</tr>
						</tfoot>
					</table>
					<?
				}
			 	?>    
			</tbody>
		</table>
		</div>
	    </div>
	    <br>
	    <?	
	} // Daily Right First Time End===================================
		
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

?>