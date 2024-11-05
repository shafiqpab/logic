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






if($action=="generate_report")
{

	$buyer_id=str_replace("'","",$cbo_buyer_name);
	$search_type=str_replace("'","",$cbo_search_by);
	$search_value=str_replace("'","",$txt_search_common);
	$type=str_replace("'","",$cbo_task_type);
	$material_source=str_replace("'","",$cbo_material_source);
	
	if($search_type==1 && $search_value!=''){
		$search_con=" and task_template_id = $search_value";	
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and  lead_time = $search_value";	
	}
	if($buyer_id){
		$search_con.=" and for_specific = $buyer_id";	
	}
	
	if($type){
		$search_con.=" and task_type = $type";	
	}
	if($material_source){
		$search_con.=" and material_source = $material_source";	
	}
	
	
	$buyer_arr = return_library_array("SELECT buyer_name,id FROM  lib_buyer WHERE is_deleted = 0 and status_active=1 order by id asc","id","buyer_name");
	
	
	$task_short_name_arr = return_library_array("SELECT TASK_NAME,TASK_SHORT_NAME FROM  LIB_TNA_TASK WHERE is_deleted = 0 and status_active=1","TASK_NAME","TASK_SHORT_NAME");
	
 	
	$sql= "SELECT TASK_TEMPLATE_ID,TNA_TASK_ID, LEAD_TIME,MATERIAL_SOURCE,DEADLINE,FOR_SPECIFIC,TASK_TYPE,EXECUTION_DAYS,NOTICE_BEFORE,SEQUENCE_NO FROM TNA_TASK_TEMPLATE_DETAILS WHERE STATUS_ACTIVE=1 AND IS_DELETED=0  $search_con   ORDER BY TASK_TEMPLATE_ID,SEQUENCE_NO ASC ";

	//echo $sql;
	
	$result = sql_select( $sql ) ;
	foreach( $result as  $row ) 
	{	
		$dataArr[$row[TASK_TEMPLATE_ID]][$row[TNA_TASK_ID]]=$row;
	}
	
	
	
	
	$width=920;
	
	
	ob_start();
	
	?>
    
        <div style="width:<? echo $width+20; ?>px" align="left">
            <table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                <thead>
                    <th width="35">SL</th>
                    <th width="70">SYS Id</th>
                    <th width="100">Buyer</th>
                    <th width="70">Lead Time</th>
                    <th width="170">Task Name </th>
                    <th width="170">Task Short Name </th>
                    <th width="70">Deadline</th>
                    <th width="70">Execution Days</th>
                    <th width="70">Notice Before </th> 
                    <th>Sequence No</th>
                </thead>
            </table>
        </div>
        <div style="overflow-y:scroll; max-height:360px; width:<? echo $width+20; ?>px;" align="left" id="scroll_body">
          	<table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
       
    	<?
		$i=1;
		foreach ($dataArr as $sys_id=>$dataRow)
		{
			foreach ($dataRow as $row)
			{
				
				 $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
							
		?>
        		<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle; cursor:pointer;" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                    <td width="35" align="center"><? echo $i;?></td>
                    <td align="center" width="70"><?= $sys_id;?></td>
                    <td width="100"><?= $buyer_arr[$row[FOR_SPECIFIC]];?></td>
                    <td align="center" width="70"><?= $row[LEAD_TIME];?></td>
                    <td width="170"><?= $tna_task_name[$row[TNA_TASK_ID]];?></td>
                    <td width="170" title="Task ID:<?= $row[TNA_TASK_ID];?>"><?= $task_short_name_arr[$row[TNA_TASK_ID]];?></td>
                    <td align="center" width="70"><?= $row[DEADLINE];?></td>
                    <td align="center" width="70"><?= $row[EXECUTION_DAYS];?></td>
                    <td align="center" width="70"><?= $row[NOTICE_BEFORE];?></td>
                    <td align="center"><?= $row[SEQUENCE_NO];?></td>
           <?
		    	$i++;
		 	}
		 }
		
		?>
     
     
    </table>
    </div>
    <div style="width:<? echo $width+20; ?>px;" align="left">
         <table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <tfoot>
                <th width="35">&nbsp;</th>
                <th width="70"></th>
                <th width="100"></th>
                <th width="70"></th>
                <th width="170"></th>
                <th width="170"></th>
                <th width="70"></th>
                <th width="70"></th>
                <th width="70"> </th> 
                <th></th>
            </tfoot>
        </table>
    </div>
    
    
          <?
	$html=ob_get_contents();
	ob_clean();	 

	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
	}
	//---------end------------//
	$filename=$user_id."_".time().".xls";
	$create_new_doc = fopen($filename, 'w');
	fwrite($create_new_doc,$html);

 	echo "$html****$filename";
	exit();
}



?>

