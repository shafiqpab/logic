<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');


extract($_REQUEST);
$user_id=$_SESSION['logic_erp']['user_id'];
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];



if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
	exit();	 
}


if($action=='task_name_list')
{
	extract($_REQUEST);
	echo load_html_head_contents("TNA Task Info","../../../", 1, 1, $unicode);
	?>
    <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
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
	$company=str_replace("'","",$company);
	
	$sql =sql_select("select id, task_name, task_short_name, task_sequence_no from  lib_tna_task where status_active=1 and is_deleted=0 order by task_sequence_no"); 

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
	setFilterGrid('list_view',-1);
	</script>
    
    <?
	
	exit();
}




if($action=="generate_report")
{
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$tna_task_id=str_replace("'","",$tna_task_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	
	$buyer_arr=return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
	
	
	if($db_type==0)
	{
		$txt_date_from = date("Y-m-d",strtotime($txt_date_from));
		$txt_date_to = date("Y-m-d",strtotime($txt_date_to));
	}
	else
	{
		$txt_date_from = date("d-M-Y",strtotime($txt_date_from));
		$txt_date_to = date("d-M-Y",strtotime($txt_date_to));
	}

	
	//echo datediff( 'd', $txt_date_from, $txt_date_to).'<br>';die;
	
	
	if($cbo_company_name>0){$sql_cond.=" and c.COMPANY_NAME=$cbo_company_name";}
	if($tna_task_id != ''){$sql_cond.=" and a.TASK_NUMBER in($tna_task_id)";}
	if($cbo_buyer_name>0){$sql_cond.=" and c.BUYER_NAME=$cbo_buyer_name";}
	if($txt_job_no!=""){ $sql_cond.=" and a.JOB_NO like('%$txt_job_no')";}
	if($txt_date_from!="" && $txt_date_to!=""){ $sql_cond.=" and a.task_start_date between '$txt_date_from' and '$txt_date_to'";}
	
	 //echo $tna_task_id;die;

	$tna_sql="select c.BUYER_NAME, a.JOB_NO,a.PO_NUMBER_ID,a.TASK_NUMBER,a.TASK_START_DATE,a.TASK_FINISH_DATE,a.ACTUAL_START_DATE,a.ACTUAL_FINISH_DATE
	
	from  tna_process_mst a, wo_po_break_down b, wo_po_details_master c 
	where a.po_number_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1  and a.is_deleted=0 and a.TASK_TYPE=1 and b.IS_CONFIRMED=1 $sql_cond";
	
   //echo $tna_sql;die;
	
	$tna_sql_result=sql_select($tna_sql);
	$dataArr=array();
	
	foreach($tna_sql_result as $row)
	{
		
			//$po_task_wise_plan_finish_date[$row[PO_NUMBER_ID]][$row[TASK_NUMBER]]=$row[TASK_FINISH_DATE];
			$po_wise_buyer[$row[PO_NUMBER_ID]]=$row[BUYER_NAME];
			$all_po_id_arr[$row[PO_NUMBER_ID]]=$row[PO_NUMBER_ID];
			$buyer_wise_po_arr[$row[BUYER_NAME]][$row[PO_NUMBER_ID]]=$row[PO_NUMBER_ID];
			
			if($row[ACTUAL_FINISH_DATE]!=''){
				$finish_day_diff=datediff( 'd', $row[TASK_FINISH_DATE], $row[ACTUAL_FINISH_DATE]);
			}
			
			if($finish_day_diff<=1 && $row[ACTUAL_FINISH_DATE]!=''){
				$buyer_wise_on_time_pass_po_arr[$row[BUYER_NAME]][$row[PO_NUMBER_ID]]=$row[PO_NUMBER_ID];
				$buyer_wise_on_time_pass_task_arr[$row[BUYER_NAME]][$row[TASK_NUMBER]]=$row[TASK_NUMBER];
			}
			else{
				$buyer_wise_on_time_fail_po_arr[$row[BUYER_NAME]][$row[PO_NUMBER_ID]]=$row[PO_NUMBER_ID];
				$buyer_wise_on_time_fail_task_arr[$row[BUYER_NAME]][$row[TASK_NUMBER]]=$row[TASK_NUMBER];
			}


			$dataArr[$row[BUYER_NAME]]=array(
			
			);
	}
	
	
	//echo count($buyer_wise_on_time_fail_task_arr[311]);
	
	if($tna_task_id != ''){$sql_cond2=" and a.TASK_NUMBER in($tna_task_id)";}
	if($txt_date_from!="" && $txt_date_to!=""){ $sql_cond2.=" and a.task_start_date between '$txt_date_from' and '$txt_date_to'";}
	
	
	$tna_history_sql="select  a.JOB_NO,a.PO_NUMBER_ID,a.TASK_NUMBER,a.TASK_START_DATE,a.TASK_FINISH_DATE,a.ACTUAL_START_DATE,a.ACTUAL_FINISH_DATE
	from  TNA_PLAN_ACTUAL_HISTORY a 
	where a.status_active=1 $sql_cond2 and a.is_deleted=0 and a.TASK_TYPE=1 ".where_con_using_array($all_po_id_arr,0,'a.PO_NUMBER_ID')."";
 	  //echo $tna_history_sql;die;
	
	$tna_history_sql_result=sql_select($tna_history_sql);
	
	foreach($tna_history_sql_result as $row)
	{
			//$row[TASK_FINISH_DATE]=$po_task_wise_plan_finish_date[$row[PO_NUMBER_ID]][$row[TASK_NUMBER]];
			$row[BUYER_NAME]=$po_wise_buyer[$row[PO_NUMBER_ID]];
			
			if($row[ACTUAL_FINISH_DATE]!=''){
				$buyer_wise_revise_pass_po_arr[$row[BUYER_NAME]][$row[PO_NUMBER_ID]]=$row[PO_NUMBER_ID];
				$buyer_wise_revise_pass_task_arr[$row[BUYER_NAME]][$row[TASK_NUMBER]]=$row[TASK_NUMBER];
			}
			else{
				$buyer_wise_revise_fail_po_arr[$row[BUYER_NAME]][$row[PO_NUMBER_ID]]=$row[PO_NUMBER_ID];
				$buyer_wise_revise_fail_task_arr[$row[BUYER_NAME]][$row[TASK_NUMBER]]=$row[TASK_NUMBER];
			}
			
	}
	
	

 	$width=700;
	ob_start();
	
	?>
    
 
    <div style="overflow-y:scroll; max-height:360px; width:<?= $width+20;?>px;" align="left" id="scroll_body">
        <table width="<?= $width;?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
            <thead>
                <th width="30">Sl</th>
                <th width="150">Buyer</th>
                <th width="80">No. of Order</th>
                <th width="80">On Time  Passed</th>
                <th width="80">As per  TNA No.of  Order Failed</th>
                <th width="80">As per Revised TNA No.of Pass</th>
                <th width="80">As per Revised TNA No.of Fail</th>
                <th>Passed  %</th>
            </thead>
            <tbody>
			<?
            $i=1;
            
			
			$NoofOrder=0;
			$OnTimePassed=0;
			$AsperTNANoofOrderFailed=0;
			$AsperRevisedTNANoofPass=0;
			$AsperRevisedTNANoofFail=0;
			$Passed_per=0;
				
			foreach ($dataArr as $buyer_id=>$row)
            {
				
				$NoofOrder+=count($buyer_wise_po_arr[$buyer_id]);
				$OnTimePassed+=count($buyer_wise_on_time_pass_po_arr[$buyer_id]);
				$AsperTNANoofOrderFailed+=count($buyer_wise_on_time_fail_po_arr[$buyer_id]);
				$AsperRevisedTNANoofPass+=count($buyer_wise_revise_pass_po_arr[$buyer_id]);
				$AsperRevisedTNANoofFail+=count($buyer_wise_revise_fail_po_arr[$buyer_id]);
				
				
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                    <td width="30"  align="center"><?= $i; ?></td>
                    <td><?= $buyer_arr[$buyer_id];?></td>
                    
                    <td align="right"><a href="javascript:fn_tna_popup('<?= implode(',',$buyer_wise_po_arr[$buyer_id]);?>','order_dtls',300,'Po Dtls','<?= $txt_date_from .'__'. $txt_date_to;?>','')"><?= count($buyer_wise_po_arr[$buyer_id]);?></a></td>
                    
                    <td align="right"><a href="javascript:fn_tna_popup('<?= implode(',',$buyer_wise_on_time_pass_po_arr[$buyer_id]);?>','on_time_tna_pass_dtls',<?= (count($buyer_wise_on_time_pass_task_arr[$buyer_id])*140)+400;?>,'On Time TNA Pass Dtls','<?= $txt_date_from .'__'. $txt_date_to;?>','<?= implode('_',$buyer_wise_on_time_pass_task_arr[$buyer_id]);?>')"><?= count($buyer_wise_on_time_pass_po_arr[$buyer_id]);?></a></td>
                    
                    <td align="right"><a href="javascript:fn_tna_popup('<?= implode(',',$buyer_wise_on_time_fail_po_arr[$buyer_id]);?>','on_time_tna_fail_dtls',<?= (count($buyer_wise_on_time_fail_task_arr[$buyer_id])*140)+400;?>,'On Time TNA Fail Dtls','<?= $txt_date_from .'__'. $txt_date_to;?>','<?= implode('_',$buyer_wise_on_time_fail_task_arr[$buyer_id]);?>')"><?= count($buyer_wise_on_time_fail_po_arr[$buyer_id]);?></a></td>
                    
                    <td align="right"><a href="javascript:fn_tna_popup('<?= implode(',',$buyer_wise_revise_pass_po_arr[$buyer_id]);?>','revise_tna_pass_dtls',<?= (count($buyer_wise_revise_pass_task_arr[$buyer_id])*140)+400;?>,'Revise TNA Pass Dtls','<?= $txt_date_from .'__'. $txt_date_to;?>','<?= implode('_',$buyer_wise_revise_pass_task_arr[$buyer_id]);?>')"><?= count($buyer_wise_revise_pass_po_arr[$buyer_id]);?></a></td>
                    
                    <td align="right"><a href="javascript:fn_tna_popup('<?= implode(',',$buyer_wise_revise_fail_po_arr[$buyer_id]);?>','revise_tna_fail_dtls',<?= (count($buyer_wise_revise_fail_task_arr[$buyer_id])*140)+400;?>,'Revise TNA Fail Dtls','<?= $txt_date_from .'__'. $txt_date_to;?>','<?= implode('_',$buyer_wise_revise_fail_task_arr[$buyer_id]);?>')"><?= count($buyer_wise_revise_fail_po_arr[$buyer_id]);?></a></td>
                    
                    <td align="right" title="((On Time Passed+As per Revised TNA No.of Pass)/No. of Order)*100">
                    <?
						echo number_format((count($buyer_wise_on_time_pass_po_arr[$buyer_id])+count($buyer_wise_revise_pass_po_arr[$buyer_id]))/count($buyer_wise_po_arr[$buyer_id])*100,2);
					?>
                    </td>
                </tr>
             <?
				$i++;
			}
			?>
       
        	</tbody>
            <tfoot>
                <th></th>
                <th></th>
                <th><?= $NoofOrder;?></th>
                <th><?= $OnTimePassed;?></th>
                <th><?= $AsperTNANoofOrderFailed;?></th>
                <th><?= $AsperRevisedTNANoofPass;?></th>
                <th><?= $AsperRevisedTNANoofFail;?></th>
                <th><?= number_format($Passed_per=($OnTimePassed+$AsperRevisedTNANoofPass)/$NoofOrder*100,2);?></th>
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

if($action=="generate_report_2")
{
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$tna_task_id=str_replace("'","",$tna_task_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$cbo_date_type=str_replace("'","",$cbo_date_type);
    // print_r($cbo_date_type);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	
	$buyer_arr=return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
	
	
	if($db_type==0)
	{
		$txt_date_from = date("Y-m-d",strtotime($txt_date_from));
		$txt_date_to = date("Y-m-d",strtotime($txt_date_to));
	}
	else
	{
		$txt_date_from = date("d-M-Y",strtotime($txt_date_from));
		$txt_date_to = date("d-M-Y",strtotime($txt_date_to));
	}

	
	//echo datediff( 'd', $txt_date_from, $txt_date_to).'<br>';die;
	
	
	if($cbo_company_name>0){$sql_cond.=" and c.COMPANY_NAME=$cbo_company_name";}
	if($tna_task_id != ''){$sql_cond.=" and a.TASK_NUMBER in($tna_task_id)";}
	if($cbo_buyer_name>0){$sql_cond.=" and c.BUYER_NAME=$cbo_buyer_name";}
	if($txt_job_no!=""){ $sql_cond.=" and a.JOB_NO like('%$txt_job_no')";}
	// if($txt_date_from!="" && $txt_date_to!=""){ $sql_cond.=" and a.task_start_date between '$txt_date_from' and '$txt_date_to'";}

	if($txt_date_from != "" && $txt_date_to != ""){
		if($cbo_date_type == 1){$sql_cond .= " and a.task_start_date between '$txt_date_from' and '$txt_date_to'";}
	
		else if($cbo_date_type == 2){$sql_cond .= " and b.SHIPMENT_DATE  between '$txt_date_from' and '$txt_date_to'";}
	}
	
	 //echo $tna_task_id;die;

	$tna_sql="select c.BUYER_NAME, a.JOB_NO,a.PO_NUMBER_ID,a.TASK_NUMBER,a.TASK_START_DATE,a.TASK_FINISH_DATE,a.ACTUAL_START_DATE,a.ACTUAL_FINISH_DATE,b.SHIPMENT_DATE
	
	from  tna_process_mst a, wo_po_break_down b, wo_po_details_master c 
	where a.po_number_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1  and a.is_deleted=0 and a.TASK_TYPE=1 and b.IS_CONFIRMED=1 $sql_cond ";
	
   //echo $tna_sql;die;
	
	$tna_sql_result=sql_select($tna_sql);
	$dataArr=array();
	
	foreach($tna_sql_result as $row)
	{
		
			//$po_task_wise_plan_finish_date[$row[PO_NUMBER_ID]][$row[TASK_NUMBER]]=$row[TASK_FINISH_DATE];
			$po_wise_buyer[$row['PO_NUMBER_ID']]=$row['BUYER_NAME'];
			$all_po_id_arr[$row['PO_NUMBER_ID']]=$row['PO_NUMBER_ID'];
			$buyer_wise_po_arr[$row['BUYER_NAME']][$row['PO_NUMBER_ID']]=$row['PO_NUMBER_ID'];

			if($row['TASK_FINISH_DATE']!=''){
				$po_wise_plan_finish_task_arr[$row['BUYER_NAME']][$row['PO_NUMBER_ID']][$row['TASK_NUMBER']]=$row['TASK_NUMBER'];
			}

			
			if($row['ACTUAL_FINISH_DATE']!=''){
				$finish_day_diff=datediff( 'd', $row['TASK_FINISH_DATE'], $row['ACTUAL_FINISH_DATE']);
			}
			
			if($finish_day_diff<=1 && $row['ACTUAL_FINISH_DATE']!=''){
				$po_wise_actual_finish_task_arr[$row['BUYER_NAME']][$row['PO_NUMBER_ID']][$row['TASK_NUMBER']]=$row['TASK_NUMBER'];
				$buyer_wise_on_time_pass_po_arr[$row['BUYER_NAME']][$row['PO_NUMBER_ID']]=$row['PO_NUMBER_ID'];
				$buyer_wise_on_time_pass_task_arr[$row['BUYER_NAME']][$row['TASK_NUMBER']]=$row['TASK_NUMBER'];
			}
			else{
				$buyer_wise_on_time_fail_po_arr[$row['BUYER_NAME']][$row['PO_NUMBER_ID']]=$row['PO_NUMBER_ID'];
				$buyer_wise_on_time_fail_task_arr[$row['BUYER_NAME']][$row['TASK_NUMBER']]=$row['TASK_NUMBER'];
			}


			$dataArr[$row['BUYER_NAME']]=array();
	}


	foreach($po_wise_plan_finish_task_arr as $buyer_id => $poRows){
		foreach($poRows as $po_id => $rowArr){
			if(count($po_wise_plan_finish_task_arr[$buyer_id][$po_id]) != count($po_wise_actual_finish_task_arr[$buyer_id][$po_id])){
				unset($buyer_wise_on_time_pass_po_arr[$buyer_id][$po_id]);
				foreach($rowArr as $task_id){
					unset($buyer_wise_on_time_pass_task_arr[$buyer_id][$task_id]);
				}

				$buyer_wise_on_time_fail_po_arr[$buyer_id][$po_id]=$po_id;
				foreach($rowArr as $task_id){
					$buyer_wise_on_time_fail_task_arr[$buyer_id][$task_id]=$task_id;
				}


			}
		 
		}
	}

	
	// echo "<pre>";
	// print_r($buyer_wise_on_time_fail_task_arr[445]); 
	//   echo "</pre>";die();
	// echo count($buyer_wise_on_time_fail_task_arr[445]);
	
	if($tna_task_id != ''){$sql_cond2=" and a.TASK_NUMBER in($tna_task_id)";}
	if($txt_date_from != "" && $txt_date_to != ""){
		if($cbo_date_type == 1){$sql_cond2 .= " and a.task_start_date between '$txt_date_from' and '$txt_date_to'";}
	
		else if($cbo_date_type == 2){$sql_cond2 .= " and b.SHIPMENT_DATE  between '$txt_date_from' and '$txt_date_to'";}
	}
	
	$tna_history_sql="select  a.JOB_NO,a.PO_NUMBER_ID,a.TASK_NUMBER,a.TASK_START_DATE,a.TASK_FINISH_DATE,a.ACTUAL_START_DATE,a.ACTUAL_FINISH_DATE,b.SHIPMENT_DATE
	from  TNA_PLAN_ACTUAL_HISTORY a,wo_po_break_down b
	 where a.po_number_id=b.id and a.status_active=1 $sql_cond2 and a.is_deleted=0 and  b.status_active=1 and  b.is_deleted=0 and  a.TASK_TYPE=1 ".where_con_using_array($all_po_id_arr,0,'a.PO_NUMBER_ID')."";
 	 // echo $tna_history_sql;die;
	
	$tna_history_sql_result=sql_select($tna_history_sql);
	
	foreach($tna_history_sql_result as $row)
	{
			//$row[TASK_FINISH_DATE]=$po_task_wise_plan_finish_date[$row[PO_NUMBER_ID]][$row[TASK_NUMBER]];
			$row['BUYER_NAME']=$po_wise_buyer[$row['PO_NUMBER_ID']];
			
			if($row['ACTUAL_FINISH_DATE']!=''){
				$buyer_wise_revise_pass_po_arr[$row['BUYER_NAME']][$row['PO_NUMBER_ID']]=$row['PO_NUMBER_ID'];
				$buyer_wise_revise_pass_task_arr[$row['BUYER_NAME']][$row['TASK_NUMBER']]=$row['TASK_NUMBER'];
			}
			else{
				$buyer_wise_revise_fail_po_arr[$row['BUYER_NAME']][$row['PO_NUMBER_ID']]=$row['PO_NUMBER_ID'];
				$buyer_wise_revise_fail_task_arr[$row['BUYER_NAME']][$row['TASK_NUMBER']]=$row['TASK_NUMBER'];
			}
			
	}
	
	

 	$width=500;
	ob_start();
	
	?>
    
 
    <div style="overflow-y:scroll; max-height:360px; width:<?= $width+20;?>px;" align="left" id="scroll_body">
        <table width="<?= $width;?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
            <thead>
                <th width="30">Sl</th>
                <th width="150">Buyer</th>
                <th width="80">No. of Order</th>
                <th width="80">On Time  Passed</th>
                <th width="80">As per  TNA No.of  Order Failed</th>
                <th>Passed  %</th>
            </thead>
            <tbody>
			<?
            $i=1;
            
			
			$NoofOrder=0;
			$OnTimePassed=0;
			$AsperTNANoofOrderFailed=0;
			$AsperRevisedTNANoofPass=0;
			$AsperRevisedTNANoofFail=0;
			$Passed_per=0;
				
			foreach ($dataArr as $buyer_id=>$row)
            {
				
				$NoofOrder+=count($buyer_wise_po_arr[$buyer_id]);
				$OnTimePassed+=count($buyer_wise_on_time_pass_po_arr[$buyer_id]);
				$AsperTNANoofOrderFailed+=count($buyer_wise_on_time_fail_po_arr[$buyer_id]);
				$AsperRevisedTNANoofPass+=count($buyer_wise_revise_pass_po_arr[$buyer_id]);
				$AsperRevisedTNANoofFail+=count($buyer_wise_revise_fail_po_arr[$buyer_id]);
				
				
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                    <td width="30"  align="center"><?= $i; ?></td>
                    <td><?= $buyer_arr[$buyer_id];?></td>
                    
                    <td align="right"><a href="javascript:fn_tna_popup('<?= implode(',',$buyer_wise_po_arr[$buyer_id]);?>','order_dtls',300,'Po Dtls','<?= $txt_date_from .'__'. $txt_date_to;?>','')"><?= count($buyer_wise_po_arr[$buyer_id]);?></a></td>
                    
                    <td align="right"><a href="javascript:fn_tna_popup('<?= implode(',',$buyer_wise_on_time_pass_po_arr[$buyer_id]);?>','on_time_tna_pass_dtls',<?= (count($buyer_wise_on_time_pass_task_arr[$buyer_id])*140)+400;?>,'On Time TNA Pass Dtls','<?= $txt_date_from .'__'. $txt_date_to;?>','<?= implode('_',$buyer_wise_on_time_pass_task_arr[$buyer_id]);?>')"><?= count($buyer_wise_on_time_pass_po_arr[$buyer_id]);?></a></td>
                    
                    <td align="right" title="<?= count($buyer_wise_on_time_fail_task_arr[$buyer_id]);?>">
					<a href="javascript:fn_tna_popup('<?= implode(',',$buyer_wise_on_time_fail_po_arr[$buyer_id]);?>','on_time_tna_fail_dtls_2',<?= (count($buyer_wise_on_time_fail_task_arr[$buyer_id])*140)+400;?>,'On Time TNA Fail Dtls','<?= $txt_date_from .'__'. $txt_date_to;?>','<?= implode('_',$buyer_wise_on_time_fail_task_arr[$buyer_id]);?>')"><?= count($buyer_wise_on_time_fail_po_arr[$buyer_id]);?></a></td>
                    

                    <td align="right" title="((On Time Passed+As per Revised TNA No.of Pass)/No. of Order)*100">
                    <?
						echo number_format(count($buyer_wise_on_time_pass_po_arr[$buyer_id])/count($buyer_wise_po_arr[$buyer_id])*100,2);
					?>
                    </td>
                </tr>
             <?
				$i++;
			}
			?>
       
        	</tbody>
            <tfoot>
                <th></th>
                <th></th>
                <th><?= $NoofOrder;?></th>
                <th><?= $OnTimePassed;?></th>
                <th><?= $AsperTNANoofOrderFailed;?></th>
                <th><?= number_format($Passed_per=$OnTimePassed/$NoofOrder*100,2);?></th>
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


if($action=="order_dtls")
{
	echo load_html_head_contents("Set Entry","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	
	$sql="select  a.JOB_NO,b.PO_NUMBER
	from  tna_process_mst a,wo_po_break_down b
	where a.PO_NUMBER_ID=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.TASK_TYPE=1 and a.PO_NUMBER_ID in($po_id) group by a.JOB_NO,b.PO_NUMBER";
	//echo $sql;
	$data_array=sql_select($sql);
/*	foreach ($data_array as $row)
	{
		$jobPoArr[$row[JOB_NO].'**'.$row[JOB_NO]]=$row;
		
	}
*/
	
	
	
	?>
    <table width="100%" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
    	<thead>
        	<th width="30">Sl</th>
            <th width="100">Job No</th>
            <th>Po No</th>
        </thead>
        <tbody>
        
        <?
		$i=1;
        foreach ($data_array as $row)
        { 
            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
		?>
		  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
            <td><?= $i;?></td>
            <td><?= $row[JOB_NO];?></td>
            <td><?= $row[PO_NUMBER];?></td>
          </tr>
            <?
			$i++;
        }
        ?>
        
        </tbody>
    </table>
    <?
}


if($action=="on_time_tna_pass_dtls")
{
	echo load_html_head_contents("Set Entry","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$task_id=str_replace('_',',',$task_id);
	$tna_task_name=return_library_array("select TASK_NAME,TASK_SHORT_NAME  from LIB_TNA_TASK where STATUS_ACTIVE=1 and  IS_DELETED=0","TASK_NAME","TASK_SHORT_NAME");

	$sql="select  a.JOB_NO,a.PO_NUMBER_ID,a.TASK_NUMBER,b.PO_NUMBER,a.TASK_START_DATE,a.TASK_FINISH_DATE,a.ACTUAL_START_DATE,a.ACTUAL_FINISH_DATE,b.PO_NUMBER
	from  tna_process_mst a,wo_po_break_down b
	where a.PO_NUMBER_ID=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.TASK_TYPE=1 and a.PO_NUMBER_ID in($po_id) and a.TASK_NUMBER in($task_id)";
	//echo $sql;
	$data_array=sql_select($sql);
	$allTaskARr=array();
	$jobPoArr=array();
	foreach ($data_array as $row)
	{
		
		
			if($row[ACTUAL_FINISH_DATE]!=''){
				$finish_day_diff=datediff( 'd', $row[TASK_FINISH_DATE], $row[ACTUAL_FINISH_DATE]);
			}
			
			if($finish_day_diff<=1 && $row[ACTUAL_FINISH_DATE]!=''){
				$allTaskARr[$row[TASK_NUMBER]]=$tna_task_name[$row[TASK_NUMBER]];
				$jobPoArr[$row[JOB_NO].'**'.$row[PO_NUMBER_ID].'**'.$row[PO_NUMBER]][$row[TASK_NUMBER]]=array(
					JOB_NO=>$row[JOB_NO],
					PO_NUMBER=>$row[PO_NUMBER],
					TASK_START_DATE=>$row[TASK_START_DATE],
					TASK_FINISH_DATE=>$row[TASK_FINISH_DATE],
					ACTUAL_START_DATE=>$row[ACTUAL_START_DATE],
					ACTUAL_FINISH_DATE=>$row[ACTUAL_FINISH_DATE],
				);
			}
		
		
	}

	
	
	?>
    <table width="100%" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
    	<thead>
        	<tr>
                <th width="30" rowspan="2">Sl</th>
                <th width="80" rowspan="2">Job No</th>
                <th rowspan="2">Po No</th>
                <th width="70" rowspan="2">Status</th>
                <?
                    foreach($allTaskARr as $task_id=>$task_name){
                        echo "<th colspan='2'>".$task_name."</th>";
                    }
                ?>
            </tr>
        	<tr>
                <?
                    foreach($allTaskARr as $task_id=>$task_name){
                        echo "<th width='70'>Start</th>";
                        echo "<th width='70'>Finish</th>";
                    }
                ?>
            </tr>
        </thead>
        <tbody>
        
        <?
		$i=1;
        foreach ($jobPoArr as $job_po=>$dataRow)
        { 
            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			list($job_no,$po_id,$po_no)=explode('**',$job_po);
		?>
		  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
            <td rowspan="2" align="center"><?= $i;?></td>
            <td rowspan="2" align="center"><?= $job_no;?></td>
            <td rowspan="2"><?= $po_no;?></td>
            <td>Plan Date</td>
            <?
				foreach($allTaskARr as $task_id=>$task_name){
					echo "<td align='center'>".$dataRow[$task_id][TASK_START_DATE]."</td>";
					echo "<td align='center'>".$dataRow[$task_id][TASK_FINISH_DATE]."</td>";
				}
			?>
          </tr>
          <tr>
            <td>Actual Date</td>
            <?
				foreach($allTaskARr as $task_id=>$task_name){
					echo "<td align='center'>".$dataRow[$task_id][ACTUAL_START_DATE]."</td>";
					echo "<td align='center'>".$dataRow[$task_id][ACTUAL_FINISH_DATE]."</td>";
				}
			?>
          </tr>
          
          
            <?
			$i++;
        }
        ?>
        
        </tbody>
    </table>
    <?
}


if($action=="on_time_tna_fail_dtls")
{
	echo load_html_head_contents("TNA Summary Report","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	
	list($txt_date_from,$txt_date_to)=explode('__',$date);
	
		if($txt_date_from!="" && $txt_date_to!=""){ $sql_cond=" and a.task_start_date between '$txt_date_from' and '$txt_date_to'";}
		$task_id=str_replace('_',',',$task_id);

	$tna_task_name=return_library_array("select TASK_NAME,TASK_SHORT_NAME  from LIB_TNA_TASK where STATUS_ACTIVE=1 and  IS_DELETED=0","TASK_NAME","TASK_SHORT_NAME");
	
	$sql="select  a.JOB_NO,a.PO_NUMBER_ID,b.PO_NUMBER,a.TASK_NUMBER,a.TASK_START_DATE,a.TASK_FINISH_DATE,a.ACTUAL_START_DATE,a.ACTUAL_FINISH_DATE,b.PO_NUMBER
	from  tna_process_mst a,wo_po_break_down b
	where a.PO_NUMBER_ID=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.TASK_TYPE=1 and a.PO_NUMBER_ID in($po_id) and a.TASK_NUMBER in($task_id) $sql_cond";
	 // echo $sql;die;
	$data_array=sql_select($sql);
	$allTaskARr=array();
	$jobPoArr=array();
	foreach ($data_array as $row)
	{
		
		
			if($row[ACTUAL_FINISH_DATE]!=''){
				$finish_day_diff=datediff( 'd', $row[TASK_FINISH_DATE], $row[ACTUAL_FINISH_DATE]);
			}
			
			if($finish_day_diff > 1 || $row[ACTUAL_FINISH_DATE]==''){
 				
				$allTaskARr[$row[TASK_NUMBER]]=$tna_task_name[$row[TASK_NUMBER]];
				$jobPoArr[$row[JOB_NO].'**'.$row[PO_NUMBER_ID].'**'.$row[PO_NUMBER]][$row[TASK_NUMBER]]=array(
					JOB_NO=>$row[JOB_NO],
					PO_NUMBER=>$row[PO_NUMBER],
					TASK_START_DATE=>$row[TASK_START_DATE],
					TASK_FINISH_DATE=>$row[TASK_FINISH_DATE],
					ACTUAL_START_DATE=>$row[ACTUAL_START_DATE],
					ACTUAL_FINISH_DATE=>$row[ACTUAL_FINISH_DATE],
				);
				
			}
		
		
	}

	
	
	?>
    <table width="100%" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
    	<thead>
        	<tr>
                <th width="30" rowspan="2">Sl</th>
                <th width="80" rowspan="2">Job No</th>
                <th rowspan="2">Po No</th>
                <th width="70" rowspan="2">Status</th>
                <?
                    foreach($allTaskARr as $task_id=>$task_name){
                        echo "<th colspan='2'>".$task_name."</th>";
                    }
                ?>
            </tr>
        	<tr>
                <?
                    foreach($allTaskARr as $task_id=>$task_name){
                        echo "<th width='70'>Start</th>";
                        echo "<th width='70'>Finish</th>";
                    }
                ?>
            </tr>
        </thead>
        <tbody>
        
        <?
		$i=1;
        foreach ($jobPoArr as $job_po=>$dataRow)
        { 
            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			list($job_no,$po_id,$po_no)=explode('**',$job_po);
		?>
		  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
            <td rowspan="2" align="center"><?= $i;?></td>
            <td rowspan="2" align="center"><?= $job_no;?></td>
            <td rowspan="2"><?= $po_no;?></td>
            <td>Plan Date</td>
            <?
				foreach($allTaskARr as $task_id=>$task_name){
					echo "<td align='center'>".$dataRow[$task_id][TASK_START_DATE]."</td>";
					echo "<td align='center'>".$dataRow[$task_id][TASK_FINISH_DATE]."</td>";
				}
			?>
          </tr>
          <tr>
            <td>Actual Date</td>
            <?
				foreach($allTaskARr as $task_id=>$task_name){
					echo "<td align='center'>".$dataRow[$task_id][ACTUAL_START_DATE]."</td>";
					echo "<td align='center'>".$dataRow[$task_id][ACTUAL_FINISH_DATE]."</td>";
				}
			?>
          </tr>
          
          
            <?
			$i++;
        }
        ?>
        
        </tbody>
    </table>
    <?
}




if($action=="revise_tna_pass_dtls")
{
	echo load_html_head_contents("Set Entry","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$task_id=str_replace('_',',',$task_id);
	$tna_task_name=return_library_array("select TASK_NAME,TASK_SHORT_NAME  from LIB_TNA_TASK where STATUS_ACTIVE=1 and  IS_DELETED=0","TASK_NAME","TASK_SHORT_NAME");
	
	$sql="select  a.JOB_NO,a.PO_NUMBER_ID,b.PO_NUMBER,a.TASK_NUMBER,a.TASK_START_DATE,a.TASK_FINISH_DATE,a.ACTUAL_START_DATE,a.ACTUAL_FINISH_DATE,b.PO_NUMBER
	from  TNA_PLAN_ACTUAL_HISTORY a,wo_po_break_down b
	where a.PO_NUMBER_ID=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.TASK_TYPE=1 and a.PO_NUMBER_ID in($po_id) and a.TASK_NUMBER in($task_id)";
	  //echo $sql;
	$data_array=sql_select($sql);
	$allTaskARr=array();
	$jobPoArr=array();
	foreach ($data_array as $row)
	{
		
		
			//echo $finish_day_diff;
			if($row[ACTUAL_FINISH_DATE]!=''){
				$allTaskARr[$row[TASK_NUMBER]]=$tna_task_name[$row[TASK_NUMBER]];
				$jobPoArr[$row[JOB_NO].'**'.$row[PO_NUMBER_ID].'**'.$row[PO_NUMBER]][$row[TASK_NUMBER]]=array(
					JOB_NO=>$row[JOB_NO],
					PO_NUMBER=>$row[PO_NUMBER],
					TASK_START_DATE=>$row[TASK_START_DATE],
					TASK_FINISH_DATE=>$row[TASK_FINISH_DATE],
					ACTUAL_START_DATE=>$row[ACTUAL_START_DATE],
					ACTUAL_FINISH_DATE=>$row[ACTUAL_FINISH_DATE],
				);
			}
		
		
	}

	
	
	
	?>
    <table width="100%" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
    	<thead>
        	<tr>
                <th width="30" rowspan="2">Sl</th>
                <th width="80" rowspan="2">Job No</th>
                <th rowspan="2">Po No</th>
                <th width="70" rowspan="2">Status</th>
                <?
                    foreach($allTaskARr as $task_id=>$task_name){
                        echo "<th colspan='2'>".$task_name."</th>";
                    }
                ?>
            </tr>
        	<tr>
                <?
                    foreach($allTaskARr as $task_id=>$task_name){
                        echo "<th width='70'>Start</th>";
                        echo "<th width='70'>Finish</th>";
                    }
                ?>
            </tr>
        </thead>
        <tbody>
        
        <?
		$i=1;
        foreach ($jobPoArr as $job_po=>$dataRow)
        { 
            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			list($job_no,$po_id,$po_no)=explode('**',$job_po);
		?>
		  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
            <td rowspan="2" align="center"><?= $i;?></td>
            <td rowspan="2" align="center"><?= $job_no;?></td>
            <td rowspan="2"><?= $po_no;?></td>
            <td>Plan Date</td>
            <?
				foreach($allTaskARr as $task_id=>$task_name){
					echo "<td align='center'>".$dataRow[$task_id][TASK_START_DATE]."</td>";
					echo "<td align='center'>".$dataRow[$task_id][TASK_FINISH_DATE]."</td>";
				}
			?>
          </tr>
          <tr>
            <td>Actual Date</td>
            <?
				foreach($allTaskARr as $task_id=>$task_name){
					echo "<td align='center'>".$dataRow[$task_id][ACTUAL_START_DATE]."</td>";
					echo "<td align='center'>".$dataRow[$task_id][ACTUAL_FINISH_DATE]."</td>";
				}
			?>
          </tr>
          
          
            <?
			$i++;
        }
        ?>
        
        </tbody>
    </table>
    <?
}


if($action=="revise_tna_fail_dtls")
{
	echo load_html_head_contents("TNA Summary Report","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$tna_task_name=return_library_array("select TASK_NAME,TASK_SHORT_NAME  from LIB_TNA_TASK where STATUS_ACTIVE=1 and  IS_DELETED=0","TASK_NAME","TASK_SHORT_NAME");
	list($txt_date_from,$txt_date_to)=explode('__',$date);
	
	if($txt_date_from!="" && $txt_date_to!=""){ $sql_cond=" and a.task_start_date between '$txt_date_from' and '$txt_date_to'";}
	
	$task_id=str_replace('_',',',$task_id);
	
	
	$sql="select  a.JOB_NO,a.PO_NUMBER_ID,b.PO_NUMBER,a.TASK_NUMBER,a.TASK_START_DATE,a.TASK_FINISH_DATE,a.ACTUAL_START_DATE,a.ACTUAL_FINISH_DATE,b.PO_NUMBER
	from  TNA_PLAN_ACTUAL_HISTORY a,wo_po_break_down b
	where a.PO_NUMBER_ID=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.TASK_TYPE=1 and a.PO_NUMBER_ID in($po_id) and a.TASK_NUMBER in($task_id)";
	 // echo $sql; 
	$data_array=sql_select($sql);
	$allTaskARr=array();
	$jobPoArr=array();
	foreach ($data_array as $row)
	{
		
			if($row[ACTUAL_FINISH_DATE]==''){
 				
				$allTaskARr[$row[TASK_NUMBER]]=$tna_task_name[$row[TASK_NUMBER]];
				$jobPoArr[$row[JOB_NO].'**'.$row[PO_NUMBER_ID].'**'.$row[PO_NUMBER]][$row[TASK_NUMBER]]=array(
					JOB_NO=>$row[JOB_NO],
					PO_NUMBER=>$row[PO_NUMBER],
					TASK_START_DATE=>$row[TASK_START_DATE],
					TASK_FINISH_DATE=>$row[TASK_FINISH_DATE],
					ACTUAL_START_DATE=>$row[ACTUAL_START_DATE],
					ACTUAL_FINISH_DATE=>$row[ACTUAL_FINISH_DATE],
				);
				
			}
	}

	
	
	?>
    <table width="100%" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
    	<thead>
        	<tr>
                <th width="30" rowspan="2">Sl</th>
                <th width="80" rowspan="2">Job No</th>
                <th rowspan="2">Po No</th>
                <th width="70" rowspan="2">Status</th>
                <?
                    foreach($allTaskARr as $task_id=>$task_name){
                        echo "<th colspan='2'>".$task_name."</th>";
                    }
                ?>
            </tr>
        	<tr>
                <?
                    foreach($allTaskARr as $task_id=>$task_name){
                        echo "<th width='70'>Start</th>";
                        echo "<th width='70'>Finish</th>";
                    }
                ?>
            </tr>
        </thead>
        <tbody>
        
        <?
		$i=1;
        foreach ($jobPoArr as $job_po=>$dataRow)
        { 
            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			list($job_no,$po_id,$po_no)=explode('**',$job_po);
		?>
		  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
            <td rowspan="2" align="center"><?= $i;?></td>
            <td rowspan="2" align="center"><?= $job_no;?></td>
            <td rowspan="2"><?= $po_no;?></td>
            <td>Plan Date</td>
            <?
				foreach($allTaskARr as $task_id=>$task_name){
					echo "<td align='center'>".$dataRow[$task_id][TASK_START_DATE]."</td>";
					echo "<td align='center'>".$dataRow[$task_id][TASK_FINISH_DATE]."</td>";
				}
			?>
          </tr>
          <tr>
            <td>Actual Date</td>
            <?
				foreach($allTaskARr as $task_id=>$task_name){
					echo "<td align='center'>".$dataRow[$task_id][ACTUAL_START_DATE]."</td>";
					echo "<td align='center'>".$dataRow[$task_id][ACTUAL_FINISH_DATE]."</td>";
				}
			?>
          </tr>
          
          
            <?
			$i++;
        }
        ?>
        
        </tbody>
    </table>
    <?
}

if($action=="on_time_tna_fail_dtls_2")
{
	echo load_html_head_contents("TNA Summary Report","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	
	list($txt_date_from,$txt_date_to)=explode('__',$date);
	
		if($txt_date_from!="" && $txt_date_to!=""){ $sql_cond=" and b.PUB_SHIPMENT_DATE between '$txt_date_from' and '$txt_date_to'";}
		$task_id=str_replace('_',',',$task_id);

	$tna_task_name=return_library_array("select TASK_NAME,TASK_SHORT_NAME  from LIB_TNA_TASK where STATUS_ACTIVE=1 and  IS_DELETED=0","TASK_NAME","TASK_SHORT_NAME");
	
	$sql="select  a.JOB_NO,a.PO_NUMBER_ID,b.PO_NUMBER,a.TASK_NUMBER,a.TASK_START_DATE,a.TASK_FINISH_DATE,a.ACTUAL_START_DATE,a.ACTUAL_FINISH_DATE,b.PO_NUMBER
	from  tna_process_mst a,wo_po_break_down b
	where a.PO_NUMBER_ID=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.TASK_TYPE=1 and a.PO_NUMBER_ID in($po_id) and a.TASK_NUMBER in($task_id) $sql_cond";
	 // echo $sql;die;
	$data_array=sql_select($sql);
	$allTaskARr=array();
	$jobPoArr=array();
	foreach ($data_array as $row)
	{
		
		
			if($row['ACTUAL_FINISH_DATE']!=''){
				$finish_day_diff=datediff( 'd', $row['TASK_FINISH_DATE'], $row['ACTUAL_FINISH_DATE']);
			}
			
			if($finish_day_diff > 1 || $row['ACTUAL_FINISH_DATE']==''){
 				
				$allTaskARr[$row['TASK_NUMBER']]=$tna_task_name[$row['TASK_NUMBER']];
				$jobPoArr[$row['JOB_NO'].'**'.$row['PO_NUMBER_ID'].'**'.$row['PO_NUMBER']][$row['TASK_NUMBER']]=array(
					'JOB_NO'=>$row['JOB_NO'],
					'PO_NUMBER'=>$row['PO_NUMBER'],
					'TASK_START_DATE'=>$row['TASK_START_DATE'],
					'TASK_FINISH_DATE'=>$row['TASK_FINISH_DATE'],
					'ACTUAL_START_DATE'=>$row['ACTUAL_START_DATE'],
					'ACTUAL_FINISH_DATE'=>$row['ACTUAL_FINISH_DATE'],
				);
				
			}
		
		
	}

	
	
	?>
    <table width="100%" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
    	<thead>
        	<tr>
                <th width="30" rowspan="2">Sl</th>
                <th width="80" rowspan="2">Job No</th>
                <th rowspan="2">Po No</th>
                <th width="70" rowspan="2">Status</th>
                <?
                    foreach($allTaskARr as $task_id=>$task_name){
                        echo "<th colspan='2'>".$task_name."</th>";
                    }
                ?>
            </tr>
        	<tr>
                <?
                    foreach($allTaskARr as $task_id=>$task_name){
                        echo "<th width='70'>Start</th>";
                        echo "<th width='70'>Finish</th>";
                    }
                ?>
            </tr>
        </thead>
        <tbody>
        
        <?
		$i=1;
        foreach ($jobPoArr as $job_po=>$dataRow)
        { 
            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			list($job_no,$po_id,$po_no)=explode('**',$job_po);
		?>
		  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
            <td rowspan="2" align="center"><?= $i;?></td>
            <td rowspan="2" align="center"><?= $job_no;?></td>
            <td rowspan="2"><?= $po_no;?></td>
            <td>Plan Date</td>
            <?
				foreach($allTaskARr as $task_id=>$task_name){
					echo "<td align='center'>".$dataRow[$task_id]['TASK_START_DATE']."</td>";
					echo "<td align='center'>".$dataRow[$task_id]['TASK_FINISH_DATE']."</td>";
				}
			?>
          </tr>
          <tr>
            <td>Actual Date</td>
            <?
				foreach($allTaskARr as $task_id=>$task_name){
					echo "<td align='center'>".$dataRow[$task_id][ACTUAL_START_DATE]."</td>";
					echo "<td align='center'>".$dataRow[$task_id][ACTUAL_FINISH_DATE]."</td>";
				}
			?>
          </tr>
          
          
            <?
			$i++;
        }
        ?>
        
        </tbody>
    </table>
    <?
}


?>

