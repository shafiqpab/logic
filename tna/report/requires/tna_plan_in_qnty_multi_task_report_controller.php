﻿<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
require_once('../../../includes/class3/class.conditions.php');
require_once('../../../includes/class3/class.reports.php');
require_once('../../../includes/class3/class.yarns.php');
require_once('../../../includes/class3/class.fabrics.php');


extract($_REQUEST);
$user_id=$_SESSION['logic_erp']['user_id'];
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


$tna_process_start_date="2014-12-01";
if($db_type==0) $blank_date="0000-00-00"; else $blank_date=""; 

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
	exit();	 
}


if($action=='task_surch')
{
	
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
    <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			var tbl_row_count = document.getElementById('list_view').rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
				var splitSTR = strCon.split("_");
				var str_or = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				
				toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );
				
				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
					selected_no.push( str_or );				
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
					selected_no.splice( i, 1 ); 
				}
				var id = ''; var name = ''; var job = ''; var num='';
				for( var i = 0; i < selected_id.length; i++ ) {
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
		
		function window_close()
		{
			parent.emailwindow.hide();
		}
    </script>
    <?
	$company=str_replace("'","",$company);
	$cbo_task_group=str_replace("'","",$cbo_task_group);
	
	
		
	if($db_type==0){$task_group_con=" and task_group!=''";}else{$task_group_con=" and task_group is not null";}
	if($cbo_task_group){$task_group_con.=" and task_group in('$cbo_task_group')";}


	$sql =sql_select("select id,task_name,task_short_name from lib_tna_task where status_active=1 and is_deleted=0 and task_type=1 $task_group_con  and  task_name in(48,50,60,61,64,73,84,86,87,88,101,110) order by task_sequence_no"); 
	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	?>
    <div style="width:400px" align="left"> 
    <table width="382" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
    	<thead>
        	<th width="50">SL</th>
            <th width="200">Task Name</th>
            <th>Short Name</th>
        </thead>
    </table>
    </div>
    <div style="width:400px; overflow-y: scroll; max-height:300px;" id="scroll_body" align="left">
    <table width="382" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="list_view">
    	<tbody>
        <?
		$i=1;
		foreach($sql as $row)
		{
			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $i; ?>_<? echo $row[csf("task_name")]; ?>_<? echo $tna_task_name[$row[csf("task_name")]]; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer;">
                <td width="50" align="center"><? echo $i; ?></td>
                <td width="200"><p><? echo $tna_task_name[$row[csf("task_name")]]; ?></p></td>
                <td><p><? echo $row[csf("task_short_name")]; ?></p></td>
            </tr>
            <?
			$i++;
		}
		?>

        </tbody>
    </table>
    </div>
    <div style="width:400px" align="left"> 
    <table width="382" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
    	<tbody>
        	<td width="50" align="center"><input type="checkbox" id="chk_all" onClick="check_all_data()" ></th>
            <td align="center"><input type="button" id="btn_close" value="Close" class="formbutton" style="width:100px;" onClick="window_close()" align="middle">
</th>
        </tbody>
    </table>
    </div>
    <?
	
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $tna_task_id_no;?>';
	var style_id='<? echo $tna_task_id;?>';
	var style_des='<? echo $tna_task;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
			js_set_value(str_ref);
		}
	}
	</script>
    
    <?
	
	exit();
}



if($action=="generate_report_summary")
{
	
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$tna_task_id=str_replace("'","",$tna_task_id);
	$task_id_arr=explode(',',$tna_task_id);
	
	$date = date_create($txt_date_from);
	$end = date_create($txt_date_to);
	
	
	$weekly_date_arr = array();
	$l=0;
	while($date < $end){
	  $l++;
	  $key = $date->format('M-d');
	  $weekly_date_arr[$l] = array(
		'start' => $date->format('Y-m-d'),
		'end'   => $date->modify('next Friday')->format('Y-m-d')
	  );
	  $date->modify('next Saturday'); 
	}

//test output

 
 	
	
	$task_data_Arr=return_library_array("select task_name,task_short_name from lib_tna_task where status_active=1 and is_deleted=0 and task_type=1 and task_name in($tna_task_id) order by task_sequence_no","task_name","task_short_name");
	
 	
?>
    <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <thead>
            <tr>
                <th>Task Name</th>
                <th>Qty Source</th>
                <?php foreach($weekly_date_arr as $week=>$dateArr){ 
					echo "<th title=' Start: ".$dateArr['start']."\n End: ".$dateArr['end']."'>Week ".$week."</th>"; 
				} ?>
            </tr>
         </thead>
        <tbody>
            <? foreach($task_id_arr as $task_id){ ?>
            <tr>
                <td rowspan="3" valign="middle"><?=$task_data_Arr[$task_id];?></td>
                <td>Req. Qty</td>
                <?php foreach($weekly_date_arr as $week=>$dateArr){ echo '<td><a href="javascript:generate_task_wise('."'".$task_id.'__'.$dateArr['start'].'__'.$dateArr['end']."'".')">vvvvv</a></td>'; } ?>
            </tr>
            <tr>
                <td>Acct. Qty</td>
                <?php foreach($weekly_date_arr as $week=>$dateArr){ echo '<td>Week '.$week.'</td>'; } ?>
            </tr>
            <tr>
                <td>Blance</td>
                <?php foreach($weekly_date_arr as $week=>$dateArr){ echo '<td>Week '.$week.'</td>'; } ?>
            </tr>
            <? } ?>
            
        </tbody>
    </table>


<?
}

if($action=="generate_report")
{
	
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_task_name=str_replace("'","",$task_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_job_year=str_replace("'","",$cbo_job_year);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$cbo_shipment_status=str_replace("'","",$cbo_shipment_status);
	$cbo_order_status=str_replace("'","",$cbo_order_status);
	$txt_date_from=str_replace("'","",$start_date);
	$txt_date_to=str_replace("'","",$end_date);
	
	$txt_date_from= date('d-M-Y',strtotime($txt_date_from));
	$txt_date_to= date('d-M-Y',strtotime($txt_date_from));
	//echo $txt_date_from;die;
	$firstDate_prev=add_date($txt_date_from,-30);
	if($db_type==0){$firstDate_prev=change_date_format($firstDate_prev,'yyyy-mm-dd');}
	else {$firstDate_prev=change_date_format($firstDate_prev,'','',1);}
	
	$sql_cond="";
	if($cbo_buyer_name>0) $sql_cond=" and c.buyer_name=$cbo_buyer_name";
	if($cbo_job_year>0)
	{
		if($db_type==0)
			$sql_cond.=" and year(c.insert_date)=$cbo_job_year";
		else
			$sql_cond.=" and to_char(c.insert_date,'YYYY')=$cbo_job_year";
	}
	if($txt_job_no!="") $sql_cond.=" and c.job_no_prefix_num=$txt_job_no";
	if($txt_order_no!="") $sql_cond.=" and b.po_number='$txt_order_no'";
	if($cbo_shipment_status>0)
	{
		if($cbo_shipment_status==3) 
			$sql_cond.=" and b.shiping_status=3";
		else 
			$sql_cond.=" and b.shiping_status<>3";
	}
	if($cbo_order_status>0) $sql_cond.=" and b.is_confirmed='$cbo_order_status'";
	if($txt_date_from!="" && $txt_date_to!="") $sql_cond.=" and a.task_start_date between '$firstDate_prev' and '$txt_date_to'";
	
	if($db_type==0) $select_job_year="year(c.insert_date) as job_year"; else $select_job_year="to_char(c.insert_date,'YYYY') as job_year";

	if($db_type==0){$date_diff_1="(b.shipment_date - DATE_FORMAT('$pc_date','dd-mm-yyyy')+1) date_diff_1";}
	else{$date_diff_1="(b.shipment_date - to_date('$pc_date','dd-mm-yyyy')+1) date_diff_1";}


	$tna_task_sql="select c.id as job_id, c.job_no_prefix_num, c.job_no, $select_job_year, c.buyer_name, c.style_ref_no, c.set_smv, b.id as po_id, b.po_number, b.po_quantity, b.plan_cut, b.shipment_date, b.po_received_date, ((b.shipment_date-b.po_received_date)+1) as lead_time, a.po_number_id, a.task_number, a.task_start_date, a.task_finish_date, a.actual_start_date, a.actual_finish_date,  ((a.task_finish_date-a.task_start_date)+1) as task_lead_time, $date_diff_1 , b.shiping_status
	from  tna_process_mst a, wo_po_break_down b, wo_po_details_master c 
	where a.po_number_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.company_name =$cbo_company_name and a.is_deleted=0 and a.task_number=$cbo_task_name $sql_cond";
	
	  //echo $tna_task_sql;die;
	
	$tna_task_result=sql_select($tna_task_sql);
	$booking_data=array();
	$details_data=array();
	
	foreach($tna_task_result as $row)
	{
		
		$daily_book=0;$po_day_found=0;
		$book_day_diff=datediff( 'd', $row[csf("task_start_date")], $row[csf("task_finish_date")]);
		if($book_day_diff>1)
		{
			for( $i=1; $i<=$book_day_diff;$i++ )
			{
				$book_date=add_date($row[csf("task_start_date")],$i-1);
				if((strtotime($book_date) <= strtotime($txt_date_to)) && (strtotime($book_date) >= strtotime($txt_date_from)))
				{
					$po_day_found++;
				}
			}
			
		}
		else
		{
			if(strtotime($row[csf("task_start_date")]) == strtotime($txt_date_from))
			{
				$po_day_found++;
			}
		}
		
		
		$all_order.=$row[csf("po_id")].",";
		
		
		
		if($po_day_found>0)
		{
			$details_data[$row[csf("buyer_name")]][$row[csf("po_id")]]["job_id"]=$row[csf("job_id")];
			$details_data[$row[csf("buyer_name")]][$row[csf("po_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$details_data[$row[csf("buyer_name")]][$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
			$details_data[$row[csf("buyer_name")]][$row[csf("po_id")]]["job_year"]=$row[csf("job_year")];
			$details_data[$row[csf("buyer_name")]][$row[csf("po_id")]]["buyer_name"]=$row[csf("buyer_name")];
			$details_data[$row[csf("buyer_name")]][$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
			
			$details_data[$row[csf("buyer_name")]][$row[csf("po_id")]]["set_smv"]=$row[csf("set_smv")];
			$details_data[$row[csf("buyer_name")]][$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
			$details_data[$row[csf("buyer_name")]][$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
			$details_data[$row[csf("buyer_name")]][$row[csf("po_id")]]["po_quantity"]=$row[csf("po_quantity")];
			$details_data[$row[csf("buyer_name")]][$row[csf("po_id")]]["plan_cut"]=$row[csf("plan_cut")];
			$details_data[$row[csf("buyer_name")]][$row[csf("po_id")]]["shipment_date"]=$row[csf("shipment_date")];
			
			$details_data[$row[csf("buyer_name")]][$row[csf("po_id")]]["po_received_date"]=$row[csf("po_received_date")];
			$details_data[$row[csf("buyer_name")]][$row[csf("po_id")]]["lead_time"]=$row[csf("lead_time")];
			$details_data[$row[csf("buyer_name")]][$row[csf("po_id")]]["po_number_id"]=$row[csf("po_number_id")];
			$details_data[$row[csf("buyer_name")]][$row[csf("po_id")]]["task_number"]=$row[csf("task_number")];
			$details_data[$row[csf("buyer_name")]][$row[csf("po_id")]]["task_start_date"]=$row[csf("task_start_date")];
			$details_data[$row[csf("buyer_name")]][$row[csf("po_id")]]["task_finish_date"]=$row[csf("task_finish_date")];
			$details_data[$row[csf("buyer_name")]][$row[csf("po_id")]]["actual_start_date"]=$row[csf("actual_start_date")];
			$details_data[$row[csf("buyer_name")]][$row[csf("po_id")]]["actual_finish_date"]=$row[csf("actual_finish_date")];
			$details_data[$row[csf("buyer_name")]][$row[csf("po_id")]]["task_lead_time"]=$row[csf("task_lead_time")];
			$details_data[$row[csf("buyer_name")]][$row[csf("po_id")]]["po_day_found"]=$po_day_found;
			$details_data[$row[csf("buyer_name")]][$row[csf("po_id")]]["date_diff_1"]=$row[csf("date_diff_1")];
			$details_data[$row[csf("buyer_name")]][$row[csf("po_id")]]["shiping_status"]=$row[csf("shiping_status")];

			
		}
	}
	$all_order_arr=array_chunk(array_unique(explode(",",chop($all_order,","))),999);
	$produce_data=$produce_date_data=array();
	if($cbo_task_name==84 || $cbo_task_name==86 || $cbo_task_name==87 || $cbo_task_name==88)
	{
		if($cbo_task_name==84) $production_type=1;
		else if($cbo_task_name==86) $production_type=5;
		else if($cbo_task_name==87) $production_type=7;
		else if($cbo_task_name==88) $production_type=8;
		
		$produce_sql=sql_select("select a.po_break_down_id, a.production_date, a.production_quantity from  pro_garments_production_mst a where a.status_active=1 and a.production_type=$production_type");
		foreach($produce_sql as $row)
		{
			$produce_data[$row[csf("po_break_down_id")]][$row[csf("production_date")]]["production_quantity"]+=$row[csf("production_quantity")];
			$produce_date_data[$row[csf("po_break_down_id")]]["production_date"].=$row[csf("production_date")].",";
		}
		
	}
	else if($cbo_task_name==48)
	{
		if($db_type==0) $date_cond=" and a.allocation_date<>'0000-00-00'"; else $date_cond=" and a.allocation_date is not null";
		$produce_sql=sql_select("select a.po_break_down_id, a.allocation_date, a.qnty from inv_material_allocation_dtls a where a.status_active=1 $date_cond");
		foreach($produce_sql as $row)
		{
			$produce_data[$row[csf("po_break_down_id")]][$row[csf("allocation_date")]]["production_quantity"]+=$row[csf("qnty")];
			$produce_date_data[$row[csf("po_break_down_id")]]["production_date"].=$row[csf("allocation_date")].",";
		}
	}
	else if($cbo_task_name==50)
	{
		$produce_sql=sql_select("select c.po_breakdown_id, a.issue_date, c.quantity from inv_issue_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=3 and c.entry_form=3 and a.issue_purpose=1 and a.status_active=1 and b.status_active=1 and c.status_active=1");
		foreach($produce_sql as $row)
		{
			$produce_data[$row[csf("po_breakdown_id")]][$row[csf("issue_date")]]["production_quantity"]+=$row[csf("quantity")];
			$produce_date_data[$row[csf("po_breakdown_id")]]["production_date"].=$row[csf("issue_date")].",";
		}
	}
	else if($cbo_task_name==60) 
	{
		$produce_sql=sql_select("select c.po_breakdown_id, a.receive_date, c.quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and a.status_active=1 and b.status_active=1 and c.status_active=1");
		foreach($produce_sql as $row)
		{
			$produce_data[$row[csf("po_breakdown_id")]][$row[csf("receive_date")]]["production_quantity"]+=$row[csf("quantity")];
			$produce_date_data[$row[csf("po_breakdown_id")]]["production_date"].=$row[csf("receive_date")].",";
		}
	}
	else if($cbo_task_name==61)
	{
		$produce_sql=sql_select("select b.po_id, a.process_end_date, b.batch_qnty  from pro_fab_subprocess a, pro_batch_create_dtls b where a.batch_id=b.mst_id and a.entry_form=35 and a.load_unload_id=2 and a.result=1 and a.status_active=1 and a.is_deleted=0 and a.service_source=1");
		foreach($produce_sql as $row)
		{
			$produce_data[$row[csf("po_id")]][$row[csf("process_end_date")]]["production_quantity"]+=$row[csf("batch_qnty")];
			$produce_date_data[$row[csf("po_id")]]["production_date"].=$row[csf("process_end_date")].",";
		}
	}
	else if($cbo_task_name==64)
	{
		$produce_sql=sql_select("select c.po_breakdown_id, a.receive_date, c.quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=7 and c.entry_form=7 and a.status_active=1 and b.status_active=1 and c.status_active=1");
		foreach($produce_sql as $row)
		{
			$produce_data[$row[csf("po_breakdown_id")]][$row[csf("receive_date")]]["production_quantity"]+=$row[csf("quantity")];
			$produce_date_data[$row[csf("po_breakdown_id")]]["production_date"].=$row[csf("receive_date")].",";
		}
	}
	else if($cbo_task_name==73)
	{
		$produce_sql=sql_select("select c.po_breakdown_id, a.receive_date, c.quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=37 and c.entry_form=37 and a.knitting_source=1 and a.status_active=1 and b.status_active=1 and c.status_active=1");
		foreach($produce_sql as $row)
		{
			$produce_data[$row[csf("po_breakdown_id")]][$row[csf("receive_date")]]["production_quantity"]+=$row[csf("quantity")];
			$produce_date_data[$row[csf("po_breakdown_id")]]["production_date"].=$row[csf("receive_date")].",";
		}
	}
	else if($cbo_task_name==101)
	{
		$produce_sql=sql_select("select a.po_break_down_id, a.inspection_date, a.inspection_qnty from pro_buyer_inspection a where a.status_active=1");
		foreach($produce_sql as $row)
		{
			$produce_data[$row[csf("po_break_down_id")]][$row[csf("inspection_date")]]["production_quantity"]+=$row[csf("inspection_qnty")];
			$produce_date_data[$row[csf("po_break_down_id")]]["production_date"].=$row[csf("inspection_date")].",";
		}
	}
	else if($cbo_task_name==110)
	{
		$produce_sql=sql_select("select a.po_break_down_id, a.ex_factory_date, a.ex_factory_qnty from pro_ex_factory_mst a where a.status_active=1");
		foreach($produce_sql as $row)
		{
			$produce_data[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]]["production_quantity"]+=$row[csf("ex_factory_qnty")];
			$produce_date_data[$row[csf("po_break_down_id")]]["production_date"].=$row[csf("ex_factory_date")].",";
			if($order_check[$row[csf("po_break_down_id")]]=="")
			{
				$order_check[$row[csf("po_break_down_id")]]=$row[csf("po_break_down_id")];
				$exfact_data[$row[csf("po_break_down_id")]]=$row[csf("ex_factory_date")];
				$ex_fac_date=$row[csf("ex_factory_date")];
			}
			else
			{
				if(strtotime($row[csf("ex_factory_date")])>=strtotime($ex_fac_date))
				{
					$exfact_data[$row[csf("po_break_down_id")]]=$row[csf("ex_factory_date")];
					$ex_fac_date=$row[csf("ex_factory_date")];
				}
			}
			
		}
	}
	
	
	
	
	
	$condition= new condition();
	$condition->company_name("=$cbo_company_name");
	if($cbo_buyer_name>0)
	{
	  $condition->buyer_name("=$cbo_buyer_name");
	}
	if($txt_job_no !='')
	{
	  $condition->job_no_prefix_num("=$txt_job_no");
	}
	if($txt_order_no !='')
	{
	  $condition->po_number("='$txt_order_no'");
	}
	if($cbo_order_status>0)
	{
	  $condition->is_confirmed("=$cbo_order_status");
	}
	$condition->init();
	$yarn= new yarn($condition);
	$yarn_req_qnty_arr=$yarn->getOrderWiseYarnQtyArray();
	
	$fabric= new fabric($condition);
	$fabric_req_qnty_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
	$buyer_name_arr=return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where file_type=1 and FORM_NAME = 'knit_order_entry'",'master_tble_id','image_location');

	
	ob_start();
	
	?>
    <div style="width:1820px" align="left">
    <table width="1800" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <thead>
            <tr>
                <th width="30" rowspan="2">SL</th>
                <th width="60" rowspan="2">Job No</th>
                <th width="110" rowspan="2">Order No</th>
                <th width="100" rowspan="2">Order Qty.</th>
                <th width="40" rowspan="2">Image</th>
                <th width="110" rowspan="2">Style Ref.</th>
                <th width="50" rowspan="2">SMV</th> 
                <th width="60" rowspan="2">PO Lead Time</th>
                <th width="75" rowspan="2">PO Rcv. Date</th>
                <th width="75" rowspan="2">Shipment Date</th>
                <th width="60" rowspan="2">Days In Hand</th>
                <th colspan="2">TNA Plan</th>
                <th colspan="2">Actual</th>
                <th width="60" rowspan="2">Task Lead Time</th>
                <th width="60" rowspan="2">Days Found</th>
                <th width="80" rowspan="2">Total Req. Qty</th>
                <th width="80" rowspan="2">Done On Time</th>
                <th width="80" rowspan="2">Done Later</th>
                <th width="80" rowspan="2">Total Done</th>
                <th width="80" rowspan="2">Current Balance</th>
                <th width="80" rowspan="2">To Be Done As TNA</th>
                <th rowspan="2">Balance To Next</th>
            </tr>
            <tr>
            	<th width="75">Start Date</th>
                <th width="75">End Date</th>
                <th width="75">Start Date</th>
                <th width="75">End Date</th>
            </tr>
        </thead>
    </table>
    </div>
    <div style="overflow-y:scroll; max-height:360px; width:1820px;" align="left" id="scroll_body">
    <table width="1800" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
    	<tbody>
        <?
        $i=1;
        foreach ($details_data as $buyer_id=>$buy_val)
        {
			?>
            <tr bgcolor="#F4F4F4">
                <td colspan="24" style="font-size:16px; font-weight:bold">Buyer Name:- <? echo $buyer_name_arr[$buyer_id];?></td>
            </tr>
            <?
			foreach($buy_val as $po_id=>$value)
			{
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					
				if($value[('shiping_status')]==3) $date_diff_3=datediff( "d", $exfact_data[$po_id] , $value["shipment_date"]);
				
				if($value[('shiping_status')]==1 && $value[('date_diff_1')]>10 ) $color="";	
				if($value[('shiping_status')]==1 && ($value[('date_diff_1')]<=10 && $value[('date_diff_1')]>=0)) $color="orange";
				if($value[('shiping_status')]==1 &&  $value[('date_diff_1')]<0) $color="red";
				if($value[('shiping_status')]==2 && $value[('date_diff_1')]>10 ) $color="";	
				if($value[('shiping_status')]==2 && ($value[('date_diff_1')]<=10 && $value[('date_diff_1')]>=0)) $color="orange";	
				if($value[('shiping_status')]==2 &&  $value[('date_diff_1')]<0) $color="red";	
				if($row[('shiping_status')]==3 && $date_diff_3 >=0 ) $color="green";	
				if($row[('shiping_status')]==3 &&  $date_diff_3<0) $color="#2A9FFF";
				
				if($cbo_task_name==84)
				{
					$po_req_qnty=$value["plan_cut"];
				}
				elseif($cbo_task_name==86 || $cbo_task_name==87 || $cbo_task_name==88 || $cbo_task_name==101 || $cbo_task_name==110)
				{
					$po_req_qnty=$value["po_quantity"];
				}
				elseif($cbo_task_name==48 || $cbo_task_name==50)
				{
					$po_req_qnty=$yarn_req_qnty_arr[$po_id];
				}
				elseif($cbo_task_name==60 || $cbo_task_name==61)
				{
					$po_req_qnty=$fabric_req_qnty_arr['knit']['grey'][$po_id];
				}
				elseif($cbo_task_name==64 || $cbo_task_name==73)
				{
					$po_req_qnty=$fabric_req_qnty_arr['knit']['finish'][$po_id];
				}
				
				
				$produce_leter = $produce_onTime = $tot_produce = $done_as_tna = $current_balance = $balance_to_next= 0;
				$prod_date=chop($produce_date_data[$po_id]["production_date"],",");
				if($prod_date!="")
				{
					$prod_date_arr=array_unique(explode(",",$prod_date));
					foreach($prod_date_arr as $production_date)
					{
						if(strtotime($production_date)>strtotime($value["task_finish_date"]))
						{
							$produce_leter+=$produce_data[$po_id][$production_date]["production_quantity"];
						}
						else
						{
							$produce_onTime+=$produce_data[$po_id][$production_date]["production_quantity"];
						}
					}
				}
				
				$tot_produce=$produce_leter+$produce_onTime;
				$current_balance=$po_req_qnty-$tot_produce;
				
				$req_qnty=(($po_req_qnty/$value["task_lead_time"])* $value["po_day_found"]);
				
				if($req_qnty>$current_balance)
				{
					$done_as_tna=$current_balance;
				}
				else
				{
					$done_as_tna=$req_qnty;
				}
				
				$balance_to_next=$current_balance-$done_as_tna;
				
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                    <td width="30"  align="center"><? echo $i; ?></td>
                    <td width="60"  align="center"><? echo $value["job_no_prefix_num"]; ?></td>
                    <td width="110"><p><? echo $value["po_number"]; ?>&nbsp;</p></td>
                    <td width="100"  align="right"><? echo number_format($value["po_quantity"],0); ?></td>
                    <td width="40" align="center" onClick="openmypage_image('requires/tna_plan_in_qnty_multi_task_report_controller.php?action=show_image&job_no=<? echo $value[('job_no')]; ?>','Image View')" style="cursor:pointer;"><img  src='../<? echo $imge_arr[$value[('job_no')]]; ?>' height='25' width='30' /></td>
                    <td width="110"><p><? echo $value["style_ref_no"]; ?>&nbsp;</p></td>
                    <td width="50" align="center"><p><? echo $value["set_smv"]; ?>&nbsp;</p></td> 
                    <td width="60" align="center"><p><? echo $value["lead_time"]; ?>&nbsp;</p></td>
                    <td width="75" align="center"><p><? if($value["po_received_date"]!="" && $value["po_received_date"]!="0000-00-00") echo change_date_format($value["po_received_date"]); ?>&nbsp;</p></td>
                    <td width="75" align="center"><p><? if($value["shipment_date"]!="" && $value["shipment_date"]!="0000-00-00") echo change_date_format($value["shipment_date"]); ?>&nbsp;</p></td>
                    <td width="60" align="center" bgcolor="<? echo $color; ?>">
					<?
					if($value[('shiping_status')]==1 || $value[('shiping_status')]==2)
					{
						echo $value[('date_diff_1')];
					}
					if($value[('shiping_status')]==3)
					{
						echo $date_diff_3;
					}
					?>
                    </td>
                    <td width="75" align="center"><p><? if($value["task_start_date"]!="" && $value["task_start_date"]!="0000-00-00") echo change_date_format($value["task_start_date"]); ?>&nbsp;</p></td>
                    <td width="75" align="center"><p><? if($value["task_finish_date"]!="" && $value["task_finish_date"]!="0000-00-00") echo change_date_format($value["task_finish_date"]); ?>&nbsp;</p></td>
                    <td width="75" align="center"><p><? if($value["actual_start_date"]!="" && $value["actual_start_date"]!="0000-00-00") echo change_date_format($value["actual_start_date"]); ?>&nbsp;</p></td>
                    <td width="75" align="center"><p><? if($value["actual_finish_date"]!="" && $value["actual_finish_date"]!="0000-00-00") echo change_date_format($value["actual_finish_date"]); ?>&nbsp;</p></td>
                    <td width="60" align="center"><p><? echo $value["task_lead_time"]; ?>&nbsp;</p></td>
                    <td width="60" align="center"><p><? echo $value["po_day_found"]; ?>&nbsp;</p></td>
                    <td width="80" align="right"><? echo number_format($po_req_qnty,2); $buyer_po_req_qnty+=$po_req_qnty; $total_po_req_qnty+=$po_req_qnty; ?></td>
                    <td width="80" align="right"><? echo number_format($produce_onTime,2); $buyer_produce_onTime+=$produce_onTime; $total_produce_onTime+=$produce_onTime; ?></td>
                    <td width="80" align="right"><? echo number_format($produce_leter,2); $buyer_produce_leter+=$produce_leter; $total_produce_leter+=$produce_leter; ?></td>
                    <td width="80" align="right"><? echo number_format($tot_produce,2); $buyer_tot_produce+=$tot_produce; $total_tot_produce+=$tot_produce; ?></td>
                    <td width="80" align="right"><? echo number_format($current_balance,2);$buyer_current_balance+=$current_balance; $total_current_balance+=$current_balance;?></td>
                    <td align="right" width="80" ><? echo number_format($done_as_tna,2); $buyer_done_as_tna+=$done_as_tna; $total_done_as_tna+=$done_as_tna; ?></td>
                    <td align="right"><? echo number_format($balance_to_next,2); $buyer_balance_to_next+=$balance_to_next; $total_balance_to_next+=$balance_to_next; ?></td>
                </tr>
                <?
				$i++;
			}
			
			?>
            <tr bgcolor="#CCCCCC">
                <td colspan="17" align="right">Buyer Total : </td>
                <td align="right"><? echo number_format($buyer_po_req_qnty,2) ?></td>
                <td align="right"><? echo number_format($buyer_produce_onTime,2) ?></td>
                <td align="right"><? echo number_format($buyer_produce_leter,2) ?></td>
                <td align="right"><? echo number_format($buyer_tot_produce,2) ?></td>
                <td align="right"><? echo number_format($buyer_current_balance,2) ?></td>
                <td align="right"><? echo number_format($buyer_done_as_tna,2) ?></td>
                <td align="right"><? echo number_format($buyer_balance_to_next,2) ?></td>
            </tr>
            <?
			$buyer_po_req_qnty=$buyer_produce_onTime=$buyer_produce_leter=$buyer_tot_produce=$buyer_current_balance=$buyer_done_as_tna=$buyer_balance_to_next=0;
        }
        ?>
        </tbody>
    </table>
    </div>
    <div style="width:1800px;" align="left">
    <table width="1800" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <tfoot>
            <th colspan="21" align="right">Grand Total : </th>
            <th align="right" width="80"><? echo number_format($total_po_req_qnty,2) ?></th>
            <th align="right" width="80"><? echo number_format($total_produce_onTime,2) ?></th>
            <th align="right" width="80"><? echo number_format($total_produce_leter,2) ?></th>
            <th align="right" width="80"><? echo number_format($total_tot_produce,2) ?></th>
            <th align="right" width="80"><? echo number_format($total_current_balance,2) ?></th>
            <th align="right" width="80"><? echo number_format($total_done_as_tna,2) ?></th>
            <th align="right" width="105"><? echo number_format($total_balance_to_next,2) ?></th>
        </tfoot>
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
 	echo "$total_datass****$filename";
	exit();
}


if($action=="show_image")
{
	//echo load_html_head_contents("Set Entry","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
	?>
    <table>
        <tr>
        <?
        foreach ($data_array as $row)
        { 
            ?>
            <td><img src='../../../<? echo $row[csf('image_location')]; ?>' height='250' width='300' /></td>
            <?
        }
        ?>
        </tr>
    </table>
    <?
}
?>
