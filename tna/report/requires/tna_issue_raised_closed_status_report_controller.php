<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
extract($_REQUEST);
$user_id=$_SESSION['logic_erp']['user_id'];
$permission=$_SESSION['page_permission'];


if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
	exit(); 	 
}



if($action=="generate_tna_report")
{

	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$tna_task_id = str_replace("'","",$tna_task_id);
	$cbo_buyer_id = str_replace("'","",$cbo_buyer_id);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$txt_job_no = str_replace("'","",$txt_job_no);
	$txt_order_no = str_replace("'","",$txt_order_no);
	$cbo_date_type = str_replace("'","",$cbo_date_type);
	$cbo_raised_closed_id = str_replace("'","",$cbo_raised_closed_id);
	
 



	if($cbo_company_id){$whereCon = " and c.COMPANY_NAME in($cbo_company_id)";}
	if($tna_task_id){$whereCon .= " and a.TASK_ID in($tna_task_id)";}
	if($cbo_buyer_id){$whereCon .= " and c.BUYER_NAME in($cbo_buyer_id)";}
	if($txt_job_no){$whereCon .= " and b.JOB_NO_MST like('%$txt_job_no')";}
	if($txt_order_no){$whereCon .= " and b.PO_NUMBER like('%$txt_order_no')";}
	if($cbo_raised_closed_id){$whereCon .= " and a.ISSUE_STATUS=$cbo_raised_closed_id";}


	if($txt_date_from != "" && $txt_date_to != ""){
		if($cbo_date_type == 1){$whereCon .= " and a.INSERT_DATE between '$txt_date_from' and '$txt_date_to 11:59:59 pm'";}
		else if($cbo_date_type == 2){$whereCon .= " and a.UPDATE_DATE  between '$txt_date_from' and '$txt_date_to 11:59:59 pm'";}
		else if($cbo_date_type == 3){$whereCon .= " and b.PUB_SHIPMENT_DATE  between '$txt_date_from' and '$txt_date_to'";}
	}

 	//echo $whereCon;die;
	
	
	
	$issue_status_arr = [1 => "Raised",2 => "Closed"];

	$lib_buyer = return_library_array("SELECT buyer_name,id FROM  lib_buyer WHERE is_deleted = 0 and status_active=1 order by id asc","id","buyer_name");
	$lib_company = return_library_array( "select id, company_name from lib_company", "id", "company_name"  );	
	$lib_user = return_library_array( "select id, USER_FULL_NAME from USER_PASSWD",'id','USER_FULL_NAME');
	$tna_task_arr=return_library_array( "select task_name, task_short_name from lib_tna_task where  task_type=1",'task_name','task_short_name');
	
	
	
	
	$sql = "select c.COMPANY_NAME,b.JOB_NO_MST,c.BUYER_NAME,b.PO_NUMBER,b.PUB_SHIPMENT_DATE,b.PO_QUANTITY,a.TASK_ID,a.JOB_ID,a.ORDER_ID,a.TASK_TYPE,a.ISSUE_STATUS,a.ISSUE_RAISED,a.ISSUE_CLOSED,a.INSERTED_BY,a.INSERT_DATE,a.UPDATED_BY,a.UPDATE_DATE from TNA_TASK_ISSUE_RAISED_CLOSED a,WO_PO_BREAK_DOWN b,WO_PO_DETAILS_MASTER c where b.id=a.ORDER_ID and c.id=b.job_id  and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 $whereCon order by a.INSERT_DATE desc";
	 // echo $sql;
	$sql_res = sql_select($sql);
	$po_id_arr=array();
	foreach($sql_res as $row){
		$po_id_arr[$row['ORDER_ID']] = $row['ORDER_ID'];
	}

	$bookingSql = "select b.PO_BREAK_DOWN_ID,b.BOOKING_NO from WO_BOOKING_DTLS b where b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and b.IS_SHORT<>1 and b.BOOKING_TYPE=1 ".where_con_using_array($po_id_arr,0,'b.PO_BREAK_DOWN_ID')."";
	//echo $bookingSql;
	$bookingSqlRes = sql_select($bookingSql);
	$booking_no_arr=array();
	foreach($bookingSqlRes as $row){
		$booking_no_arr[$row['PO_BREAK_DOWN_ID']][$row['BOOKING_NO']] = $row['BOOKING_NO'];
	}

	



	$width = 2260;
	ob_start();
	
	?>


<div style="width:<?= $width+20; ?>px" align="left">
    <table width="<?= $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <thead>
            <tr>
                <th width="35">SL</th>
                <th width="120">Company Name</th>
                <th width="120">Buyer Name</th>
                <th width="80">Job No</th>
                <th width="120">Order No</th>
                <th width="80">Ship Date</th>
                <th width="60">Order Qty</th>
                <th>FB. Booking</th>
                <th width="120">Task Name</th>
                <th width="60">Status</th>


                <th width="350">Raised Comments</th>
                <th width="100">Raised By</th>
                <th width="80">Raised Date</th>
                <th width="80">Raised Time</th>

                <th width="350">Closed Comments</th>
                <th width="100">Closed By</th>
                <th width="80">Closed Date</th>
                <th width="80">Closed Time</th>
            </tr>
        </thead>
    </table>
    <div style="overflow-y:scroll; max-height:360px; width:<? echo $width+20; ?>px;" align="left" id="scroll_body">
        <table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"
            id="table_body">
            <tbody>
                <?php
				
			 $i=1;
			 foreach($sql_res as $row){
				$bgcolor = ($i%2==0) ? "#E9F3FF" : "#FFFFFF";	
				$row['INSERT_TIME'] = date('h:i:s a',strtotime($row['INSERT_DATE']));
				$row['INSERT_DATE'] = date('d-m-Y',strtotime($row['INSERT_DATE']));
				
				if($row['UPDATE_DATE'] && $row['ISSUE_CLOSED']){
					$row['UPDATE_TIME'] = date('h:i:s a',strtotime($row['UPDATE_DATE']));
					$row['UPDATE_DATE'] = date('d-m-Y',strtotime($row['UPDATE_DATE']));
				}
				else{$row['UPDATED_BY'] = '';$row['UPDATE_TIME'] = '';}

							
			?>
                <tr bgcolor="<?= $bgcolor; ?>" style="cursor:pointer;"
                    onClick="change_color('tr_<?= $i;?>','<?= $bgcolor;?>')" id="tr_<?= $i; ?>">
                    <td width="35" align="center"><?= $i;?></td>
                    <td width="120"><?= $lib_company[$row['COMPANY_NAME']];?></td>
                    <td width="120"><?= $lib_buyer[$row['BUYER_NAME']];?></td>
                    <td width="80" align="center"><?=$row['JOB_NO_MST'];?></td>
                    <td width="120"><?=$row['PO_NUMBER'];?></td>
                    <td width="80" align="center"><?= change_date_format($row['PUB_SHIPMENT_DATE']);?></td>
                    <td width="60" align="right"><?=$row['PO_QUANTITY'];?></td>
                    <td><p><?= implode(', ',$booking_no_arr[$row['ORDER_ID']]);?></p></td>
                    <td width="120" title="<?=$row['TASK_ID'];?>"><?= $tna_task_arr[$row['TASK_ID']];?></td>
                    <td width="60" align="center"><?= $issue_status_arr[$row['ISSUE_STATUS']];?></td>

                    <td width="350"><?=$row['ISSUE_RAISED'];?></td>
                    <td width="100"><?= $lib_user[$row['INSERTED_BY']];?></td>
                    <td width="80" align="center"><?=$row['INSERT_DATE'];?></td>
                    <td width="80" align="center"><?=$row['INSERT_TIME'];?></td>

                    <td width="350"><?=$row['ISSUE_CLOSED'];?></td>
                    <td width="100"><?= $lib_user[$row['UPDATED_BY']];?></td>
                    <td width="80" align="center"><?=$row['UPDATE_DATE'];?></td>
                    <td width="80" align="center"><?=$row['UPDATE_TIME'];?></td>
                </tr>
                <?php
				$i++;	 
			}
		?>

            </tbody>
        </table>
    </div>
    <!-- <table width="< ?= $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <tfoot>
            <th width="40"></th>
        </tfoot>
    </table> -->
</div>


<?
		  
	$html = ob_get_contents();
	ob_clean();	 



	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);

	

 	echo "$html###$filename";
	exit();
}


if($action == 'tna_task_list_popup'){

	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
<script>
	var selected_id = Array();
	var selected_name = Array();

	function check_all_data(str) {
		selectByid(str);
	}

	function toggle(x, origColor) {
		var newColor = 'yellow';
		if (x.style) {
			x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
		}
	}

	function js_set_value(str) {
		[task_id, task_name] = str.split("**");

		toggle(document.getElementById('tr_' + task_id), '#FFFFCC');

		if (jQuery.inArray(task_id, selected_id) == -1) {
			selected_id.push(task_id);
			selected_name.push(task_name);
		} else {
			for (var i = 0; i < selected_id.length; i++) {
				if (selected_id[i] == task_id) break;
			}
			selected_id.splice(i, 1);
			selected_name.splice(i, 1);
		}
		var id = ''; var name = '';
		for (var i = 0; i < selected_id.length; i++) {
			id += selected_id[i] + ',';
			name += selected_name[i] + ',';
		}
		id = id.substr(0, id.length - 1);
		name = name.substr(0, name.length - 1);
		//alert(num);
		$('#txt_selected_task_id').val(id);
		$('#txt_selected_task_name').val(name);
	}

	function window_close() {
		parent.emailwindow.hide();
	}


	let selectByid = (tna_task_id) => {
		var task_id_arr = tna_task_id.split(',');
		for (var k = 0; k < task_id_arr.length; k++) {
			var taskIdName = task_id_arr[k] + '**' + '_' + trim($('#task_name_' + task_id_arr[k]).text());
			//alert($('#tr_'+task_id_arr[k]).css("background-color"));
			js_set_value(taskIdName);
		}
	}

</script>
<?
	$company_id = str_replace("'","",$company_id);
	
	$sql = sql_select("select ID,TASK_NAME,TASK_SHORT_NAME from lib_tna_task where status_active=1 and is_deleted=0 and task_type=1 order by task_sequence_no"); 
	//print_r($sql);
	

	?>
<div style="width:400px" align="left">
    <input type='hidden' id='txt_selected_task_id' />
    <input type='hidden' id='txt_selected_task_name' />

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
		$task_id_arr = array();
		foreach($sql as $row)
		{
			$bgcolor = ($i%2==0) ? "#E9F3FF" : "#FFFFFF";
			$task_id_arr[$row["TASK_NAME"]] = $row["TASK_NAME"];
			?>
            <tr bgcolor="<?= $bgcolor; ?>"
                onClick="js_set_value('<?= $row["TASK_NAME"] . '**' . $tna_task_name[$row["TASK_NAME"]]; ?>')"
                id="tr_<?= $row["TASK_NAME"]; ?>" style="cursor:pointer;">
                <td width="50" align="center"><?= $i; ?></td>
                <td width="200" id="task_name_<?= $row["TASK_NAME"]; ?>">
                    <p>
                        <? echo $tna_task_name[$row["TASK_NAME"]]; ?>
                    </p>
                </td>
                <td>
                    <p>
                        <? echo $row["TASK_SHORT_NAME"]; ?>
                    </p>
                </td>
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
            <td width="50" align="center"><input type="checkbox" id="chk_all" onClick="check_all_data('<?= implode(',',$task_id_arr);?>')"></th>
            <td align="center"><input type="button" id="btn_close" value="Close" class="formbutton" style="width:100px;"  onClick="window_close()" align="middle"> </th>
        </tbody>
    </table>
</div>

 
<script language="javascript" type="text/javascript">
var tna_task_id = '<? echo $tna_task_id;?>';
selectByid(tna_task_id);




</script>

<?
	exit();
}


?>