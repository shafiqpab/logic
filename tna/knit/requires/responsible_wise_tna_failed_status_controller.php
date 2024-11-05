<!-- // TNA_PROCESS_MST WO_PO_DETAILS_MASTER WO_PO_BREAK_DOWN -->
<?php

header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$type=$_REQUEST['type'];


//Get Buyers List Show
if($action=="load_drop_down_buyer")
{
    echo create_drop_down("cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($data)  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 0, "-- Select Buyer --", $selected, "");
    exit();
}

if($action=='task_popup')
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
			//alert(strCon);
				var splitSTR = strCon.split("_");
				var str_or = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
				
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
				//alert(num);
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
	$company=str_replace("'","", $company); 
	if($db_type==0){$task_group_con=" and task_group!=''";}else{$task_group_con=" and task_group is not null";}
 
 
	$sql =sql_select("SELECT id,task_name,task_short_name FROM lib_tna_task WHERE status_active=1 and is_deleted=0  AND task_type = 1 $task_group_con order by task_sequence_no"); 
	
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
			if ($i%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $i; ?>_<? echo $row[csf("task_name")]; ?>_<? echo $tna_task_name[$row[csf("task_name")]]; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer;">
                <td width="50" align="center"><? echo $i; ?></td>
                <td width="200"><p><? echo $tna_task_name[$row[csf("task_name")]]; ?>&nbsp;</p></td>
                <td><p><? echo $row[csf("task_short_name")]; ?>&nbsp;</p></td>
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

if($action=='team_leader_popup')
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
            
            //$('#txt_individual_id' + str).val(splitSTR[1]);
            //$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
            
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
            //alert(num);
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
    if($txt_team_leader_id){$task_id_con = " and id in($txt_team_leader_id)";}
	else{$task_id_con="";}  

	$sql =sql_select("SELECT id, user_full_name FROM user_passwd WHERE status_active=1 and is_deleted=0"); 
	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	?>
    <div style="width:300px" align="left"> 
        <table width="300" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
            <thead>
                <th width="50">SL</th>
                <th width="150">Name</th>
            </thead>
        </table>
    </div>
    <div style="width:300px; overflow-y: scroll; max-height:300px;" id="scroll_body" align="left">
        <table width="300" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="list_view">
            <tbody>
            <?
            $i=1;
            foreach($sql as $row)
            {
                if ($i%2==0)
                $bgcolor="#E9F3FF";
                else
                $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $i; ?>_<? echo $row["ID"]; ?>_<? echo $row[("USER_FULL_NAME")]; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer;">
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="150"><p><? echo $row["USER_FULL_NAME"]; ?>&nbsp;</p></td>
                </tr>
                <?
                $i++;
            }
            ?>
            </tbody>
        </table>
    </div>
    <div style="width:300px" align="left"> 
    <table width="382" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
    	<tbody>
        	<td width="50" align="center"><input type="checkbox" id="chk_all" onClick="check_all_data()" ></td>
            <td align="center"><input type="button" id="btn_close" value="Close" class="formbutton" style="width:100px;" onClick="window_close()" align="middle"></td>
	    </tbody>
    </table>
    </div> 
    <script language="javascript" type="text/javascript">
	var team_leader_no='<? echo $txt_team_leader_no;?>';
	var team_leader_id='<? echo $txt_team_leader_id;?>';
	var team_leader='<? echo $txt_team_leader;?>';
	if(team_leader_no!="")
	{
		team_leader_no_arr=team_leader_no.split(",");
		team_leader_id_arr=team_leader_id.split(",");
		team_leader_arr=team_leader.split(",");
		var str_ref="";
		for(var k=0;k<team_leader_no_arr.length; k++)
		{
			str_ref=team_leader_no_arr[k]+'_'+team_leader_id_arr[k]+'_'+team_leader_arr[k];
			js_set_value(str_ref);
		}
	}
	</script>
    <?
	exit();
}

// report generate
if($action=="report_generate")
{  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_id = str_replace("'","",$cbo_company_id);
	$cbo_buyer_id = str_replace("'","",$cbo_buyer_id);
    $tna_task_id = str_replace("'","",$tna_task_id);
	$team_leader = str_replace("'","",$txt_team_leader_id);
    $cbo_shipment_status = str_replace("'","",$cbo_shipment_status);
    $cbo_date_category = str_replace("'","",$cbo_date_category);
	$date_from = str_replace("'","",$txt_date_from);
	$date_to = str_replace("'","",$txt_date_to);
    
    $company_arr = return_library_array("select id,company_short_name from lib_company","id","company_short_name");
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
    $task_arr = return_library_array("select task_name, task_short_name from lib_tna_task where task_type = 1", "task_name", "task_short_name");
    $team_arr = return_library_array("select id, mobile_number from user_passwd", "id", "mobile_number");
    $user_arr = return_library_array("select id, user_name from user_passwd", "id", "user_name");
    $ship_status_arr=array(1=>'ALL (Pending+Partial)', 3=>'Full Shipment/Closed');

	$company_id = str_replace('"', '', $company_id);

    if($cbo_company_id){$company_con = " and b.company_name = $company_id";}
	else{$company_con="";}

    if($cbo_buyer_id){$buyer_con = " and b.buyer_name in($cbo_buyer_id)";}
	else{$buyer_con="";}

    if($tna_task_id){$task_con = " and a.task_number in($tna_task_id)";}
	else{$task_con="";}

    if($team_leader){$team_leader_con = " and b.team_leader in($team_leader)";}
	else{$team_leader_con="";}
    
     
    if($cbo_order_status){$order_status = " and c.is_confirmed = $cbo_order_status";}
	else{$order_status="";} 

    if(str_replace("'","",$cbo_shipment_status)==4){$shipment_status_con=" and c.shiping_status=3";}
	else if(str_replace("'","",$cbo_shipment_status)==1){$shipment_status_con=" and c.shiping_status !=3";}
 
    // txt_team_leader 
    // Plan Start Date
    // Plan Finish Date

	//echo $task_con;die;
	  
	$date_category_status_date = '';
	if($date_from && $date_to){
		// date category
		if($cbo_date_category==1) // Plan Start Date
		{
			$date_category = " AND a.task_start_date between '$date_from' and '$date_to' AND a.actual_start_date between '$date_from' and '$date_to'";
			$plan_date_fild = " a.task_start_date";
			$actual_date_fild = " a.actual_start_date";
		}
		else if($cbo_date_category == 2) // Plan Finish Date
		{
			$date_category = " AND a.task_finish_date between '$date_from' and '$date_to' AND a.actual_finish_date between '$date_from' and '$date_to'";
			$plan_date_fild = " a.task_finish_date";
			$actual_date_fild = " a.actual_finish_date";
		}
    }

	//$task_start_date = strtotime('01/17/2024');
	//$task_finish_date = strtotime('01/18/2024');
	//echo $task_start_date.'='.$task_finish_date;die;
	// if($task_start_date < $task_finish_date){
	// 	echo "Expire";die;
	// }
	// else{
	// 	echo "Valid";die;
	// }
	// if(1705428000 < 1705514400){

	// }

	// $diff = $task_start_date - $task_finish_date;
	// $days = floor($diff / (60 * 60 * 24));
	// echo $days;die;

    // NA_PROCESS_MST WO_PO_DETAILS_MASTER WO_PO_BREAK_DOWN

    $sql = "SELECT a.id as tna_mst_id, a.job_no as tna_job_no, a.po_number_id as tna_po_number_id, a.shipment_date as tna_shipment_date, a.task_number,$plan_date_fild as plan_data,$actual_date_fild as actual_date, b.id as po_dlts_mst_id, b.job_no, b.company_name, b.buyer_name, b.style_ref_no, b.team_leader, c.id as po_break_mst_id, c.is_confirmed, c.shiping_status FROM tna_process_mst a, wo_po_details_master b, wo_po_break_down c WHERE a.job_no=b.job_no AND b.id=c.job_id $company_con $buyer_con $task_con $team_leader_con $order_status $date_category $shipment_status_con";

    // echo $sql;die;
    $sql_data = sql_select($sql);
    $order_wise_array = array();
    foreach( $sql_data as $row)
	{
		$plan_data = strtotime($row['PLAN_DATA']);  
		$actual_date = strtotime($row['ACTUAL_DATE']);
		if($plan_data < $actual_date){
			 
			$order_wise_array[$row[csf('task_number')]][$row[csf('tna_po_number_id')]] = $row;
		}
        
    } 

	//echo "<pre>";
    //print_r($order_wise_array);
    // die; 
	ob_start();
    ?>
    <br>
    <br>
    <div style="width:1020px;">
		<div>
			<table width="1000" border="0" cellpadding="2" cellspacing="0"> 
				<thead>
					<tr class="form_caption" align="center">
						<td colspan="9" style="font-size:16px; font-weight:bold">TNA failed status Report</td> 
					</tr> 
					<tr class="form_caption">
						<td colspan="9" align="center"><?= $date_category_arr[$cbo_date_cat_id].' ('. change_date_format($date_from).' To '.change_date_format($date_to); ?>)</td> 
					</tr>
				</thead>
			</table>
		<div>
		<table width="1020" id="table_header_1" border="1" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th width="100">Task Name</th>
					<th width="100">Buyer</th>
					<th width="100">Job No</th>
					<th width="100">Style Ref.</th>
					<th width="100">PO No.</th>
					<th width="100">Ship Date</th>
					<th width="100">Status</th>
					<th width="100">Responsible</th>
					<th width="100">Contact No.</th>
				</tr>
			</thead>
		</table>	
        <div style="max-height:400px; overflow-y:scroll; width:1020px" id="scroll_body">
			<table width="1020" border="1" class="rpt_table" rules="all" id="table-body">
				<?php
                
                foreach($order_wise_array as $po_id){
                    $i = 1;
                    foreach($po_id as $row){ 
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor;?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<?= $i; ?>','<?= $bgcolor;?>')" id="tr_<?= $i;?>">
				 
					<td width="100" align="left"><?= $task_arr[$row['TASK_NUMBER']]; ?></td>
					<td width="100" align="left"><?= $buyer_arr[$row['BUYER_NAME']]; ?></td>
					<td width="100" align="left"><?= $row['TNA_JOB_NO']; ?></td>
					<td width="100" align="left"><?= $row['STYLE_REF_NO']; ?></td>
					<td width="100" align="left"><?= $row['TNA_PO_NUMBER_ID']; ?></td>
					<td width="100" align="left"><?= $row['TNA_SHIPMENT_DATE']; ?></td>
					<td width="100" align="left"><?= $ship_status_arr[$row['SHIPING_STATUS']]; ?></td>
					<td width="100" align="left"><?= $user_arr[$row['TEAM_LEADER']]; ?></td>
					<td width="100" align="left"><?= $team_arr[$row['TEAM_LEADER']]; ?></td>
				</tr>
                <?php  ++$i;} } ?>
                
			</table>
		</div>		
	</div>
	<br>
	<br>
    <?php
    $user_id = $_SESSION['logic_erp']['user_id'];
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
        @unlink($filename);
    }
    $name = time();
    $filename = $user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****$report_type";
    exit();
}
?>