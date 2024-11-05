<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']['user_id'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$menu_id = $_SESSION['menu_id'];

$permissionSql = "SELECT approve_priv FROM user_priv_mst where user_id = $user_id AND main_menu_id = $menu_id";
$permissionCheck = sql_select( $permissionSql ); 
$approvePermission = $permissionCheck[0][csf('approve_priv')];

if($db_type == 0) $year_cond = "SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
else if($db_type == 2) $year_cond = "to_char(a.insert_date,'YYYY') as year";

if($action == "load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	exit();	
}

$company_arr = return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

//$cost_components = array('Fabric Cost','Trims Cost','Embell.Cost','Gmts.Wash','Commission','Commercial Cost','Lab Test','Inspection Cost','Gmts Freight Cost','Currier Cost','Certificate Cost','Depreciation & Amortization','Interest','Income Tax','Others Cost'); 

if($action == "report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
    
	$company_name = str_replace("'","",$cbo_company_name);
	
	if(str_replace("'","",$cbo_buyer_name) == 0)
	{ 
		if ($_SESSION['logic_erp']["data_level_secured"] == 1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}
	
	$txt_job_no = str_replace("'","",$txt_job_no);	
	if($txt_job_no!=''){$job_con=" and a.job_no_prefix_num=".$txt_job_no;}
	else{$job_con="";}
	
	
		
	$txt_date = str_replace("'","",$txt_date);	
    
	$date_cond = '';
	if($txt_date!="")
	{
		if($db_type==0)  $txt_date=change_date_format($txt_date,"yyyy-mm-dd");
		else   			 $txt_date=change_date_format($txt_date,"yyyy-mm-dd","-",1);

		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and b.costing_date>'".$txt_date."'";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and b.costing_date<='".$txt_date."'";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and b.costing_date='".$txt_date."'";
		else $date_cond = '';
	}
	
	$approval_type = str_replace("'","",$cbo_approval_type);				
	
    
	$max_sequence_no = return_field_value("max(sequence_no)","electronic_approval_setup","company_id = $cbo_company_name and entry_form=15 and is_deleted=0");
   
	if($approval_type==1)
	{      
		$sql="select b.id,a.job_no_prefix_num,$year_cond,a.job_no,a.buyer_name,a.style_ref_no,a.insert_date,a.updated_by,a.update_date,b.costing_date, b.approved,b.inserted_by,  c.id as approval_id,c.approved_by,c.id as approval_id ,a.id as job_id  from wo_pre_cost_mst b,  wo_po_details_master a,approval_history c  where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=$company_name  and a.is_deleted=0 and  a.status_active=1 and b.status_active=1  and c.sequence_no='".$max_sequence_no."'   and  b.is_deleted=0  and  b.partial_approved=1 and b.ready_to_approved=1 and b.approved=1 and b.higher_othorized_approved not in (1,2,3) $buyer_id_cond  $date_cond $job_con
		union all 
				  
		select b.id,a.job_no_prefix_num,$year_cond,a.job_no,a.buyer_name,a.style_ref_no,a.insert_date,a.updated_by,a.update_date,b.costing_date, b.approved,b.inserted_by,  c.id as approval_id,c.approved_by,c.id as approval_id,a.id as job_id   from wo_pre_cost_mst b,  wo_po_details_master a,approval_history c  where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=$company_name  and a.is_deleted=0 and  a.status_active=1 and b.status_active=1  and c.sequence_no='".$max_sequence_no."'   and  b.is_deleted=0  and b.higher_othorized_approved in (1,3) $buyer_id_cond  $date_cond $job_con";
				  //(b.higher_othorized_approved in (1,3) or and c.current_approval_status=1 and c.current_approval_status=1
				  
		$sql1 = "SELECT id,job_no, cost_component_id,approved_by,current_approval_status from co_com_pre_costing_approval where current_approval_status=1";   	
		$costCompArray = sql_select( $sql1 );         
        
        $costCompStatus = array();
        $dbjobNo = array();
        $dbjobStatus = array();
        foreach ($costCompArray as $costComponetntRow)
		{
            $costCompStatus[$costComponetntRow[csf('job_no')]][$costComponetntRow[csf('cost_component_id')]] = $costComponetntRow[csf('current_approval_status')];                  
        }		 
    }
	else
	{ 
		$sql="select b.id,a.job_no_prefix_num,$year_cond,a.job_no,a.buyer_name,a.style_ref_no,a.insert_date,a.updated_by,a.update_date,b.costing_date, b.approved,b.inserted_by,
			      c.id as approval_id,c.approved_by,c.id as approval_id ,a.id as job_id
			      from wo_pre_cost_mst b,  wo_po_details_master a,approval_history c
				  where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=$company_name  and a.is_deleted=0 and
				  a.status_active=1 and b.status_active=1 and b.ready_to_approved=1 and c.sequence_no='".$max_sequence_no."' 
				  and  b.is_deleted=0  and b.higher_othorized_approved in (2,3) $buyer_id_cond  $date_cond  $job_con"; //and b.approved=1 and c.current_approval_status=1
				  
		
		
		$sql1 = "SELECT id,job_no, cost_component_id,approved_by,current_approval_status from co_com_pre_costing_approval"; 
		$costCompArray = sql_select( $sql1 );         
        
        $costCompStatus = array();
        $dbjobNo = array();
        $dbjobStatus = array();
        foreach ($costCompArray as $costComponetntRow)
		{
            $costCompStatus[$costComponetntRow[csf('job_no')]][$costComponetntRow[csf('cost_component_id')]] = $costComponetntRow[csf('current_approval_status')];                  
        }
	}
	
	   //echo $sql;
	
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:1315px; margin-top:10px">
            <legend>Pre-Costing Approval</legend>	
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="1280" class="rpt_table" >
                <thead>
                    <th width="50">SL</th>
                    <th width="80">Job No</th>
                    <th width="170">Cost Components</th>
                    <th width="50">Year</th>
                    <th width="100">Buyer</th>
                    <th width="100">Style Ref.</th>
                    <th width="100">Costing Date</th>
                    <th width="100">Est. Ship Date</th>
                    <th width="50">Image</th>
					<th width="50">file</th>
                    <th width="100">Insert By</th>
                    <th width="80">Insert Date</th>
                    <th width="80">Last Update By</th>
                    <th width="80">Last Update Date</th>
                    <th width="">Un-Approved Request</th>                                    
                </thead>
            </table>
            <div style="width:1280px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="0" rules="all" width="1261" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
                        $i = 1;
                        $nameArray=sql_select( $sql );
                       // print_r($costCompStatus);die;
                        foreach ($nameArray as $row)
                        {

                            if ($i%2==0)  
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                            
                            $value=$row[csf('id')];
                        
                            if($row[csf('approval_id')]==0)
                            {
                                $print_cond = 1;
                            }
                            else
                            {
                                if($duplicate_array[$row[csf('entry_form')]][$row[csf('id')]][$row[csf('approved_by')]]=="")
                                {
                                    $duplicate_array[$row[csf('entry_form')]][$row[csf('id')]][$row[csf('approved_by')]]=$row[csf('id')];
                                    $print_cond=1;
                                }
                                else
                                {
                                    if($all_approval_id == "") $all_approval_id = $row[csf('approval_id')]; else $all_approval_id.=",".$row[csf('approval_id')];
                                    $print_cond=0;
                                }
                            }
							if($print_cond==1)
							{	                           
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" align="center"> 
									<td width="50" align="center">
                                        <? echo  $i; ?>
                                    </td>
									<td width="80" id ="job_<? echo  $i.$row[csf('job_no_prefix_num')]; ?>">
                                    	<input type="checkbox" id="check<? echo $i ?>" onclick="check_all_cost_component(<? echo $i; ?>,<? echo $row[csf('job_no_prefix_num')]; ?>)" value="<?php echo $i; ?>" <? if($dbjobNo[$row[csf('job_no')]]==$row[csf('job_no')] || $row[csf('higher_othorized_approved')]==1) { ?> checked <? } ?>/>
                                        <a href='##'  onclick="report_part('<? echo "preCostRpt3"; ?>','<? echo $row[csf('job_no')]; ?>',<? echo $cbo_company_name; ?>,<? echo $row[csf('buyer_name')]; ?>,'<? echo $row[csf('style_ref_no')];?>','<? echo $row[csf('costing_date')]; ?>','')"><? echo $row[csf('job_no_prefix_num')]; ?></a>
                                        
                                      <br />
                                      
                                        <a href='##' onclick="generate_report('bomRpt3','<? echo $row[csf('job_no')]; ?>',<? echo $cbo_company_name; ?>,<? echo $row[csf('buyer_name')]; ?>,'<? echo $row[csf('style_ref_no')];?>','<? echo $row[csf('costing_date')]; ?>','')">BOM 3</a><br/>
										 <a href='##' onclick="generate_report('fabric_cost_detail','<? echo $row[csf('job_no')]; ?>',<? echo $cbo_company_name; ?>,<? echo $row[csf('buyer_name')]; ?>,'<? echo $row[csf('style_ref_no')];?>','<? echo $row[csf('costing_date')]; ?>','')">Fab Pre Cost</a>
                                      
                                      
                                         
                                    </td>
                                    <td width="170" id="cost_component_<?php echo $row[csf('job_no_prefix_num')];?>">
                                        <table cellspacing="0" cellpadding="0" border="0" rules="all" id="cost_component_tbl_<?php echo $i;?>">                                           <tbody>                                         
                                            <?php                                            
                                            $k = 0;                
                                            foreach ($cost_components as $key=>$val) { 
                                                $k++;
												$checked='';
												$disabled=''; $disabled_value=0;
												if($costCompStatus[$row[csf('job_no')]][$key]==1) $checked="checked";
												if($costCompStatus[$row[csf('job_no')]][$key]==0 && $approval_type==1){ $disabled="disabled"; $disabled_value=1;}
												if($costCompStatus[$row[csf('job_no')]][$key]==1 && $approval_type==2){ $disabled="disabled";$disabled_value=1;}                                                 
                                                ?>
                                                <tr>      
                                                    <td width="170">                                                                                                             
                                                        <input type="checkbox" id="cost_com_<?php echo $i.'_'.$k;?>" value="<?php echo $disabled_value;?>" class="custom" <?php if($costCompStatus[$row[csf('job_no')]][$key]==1) {?> checked <?php } ?> onclick="specificJobCheck(<? echo $i; ?>)"  <?php echo $disabled; ?>/>
                                                        <?php echo $val;?>
                                                        <input type="hidden" id="cost_com_hidden_<?php echo $i.'_'.$k;?>" value="<?php  echo $row[csf('job_no')].'*'.$key.'*'.$row[csf('id')];?>">
                                                        <input type="hidden" id="costComDisable_<?php echo $i.'_'.$k;?>" name="costComDisable[]"  value="<?php  echo $disabled_value;?>">
                                                        
                                                    </td>
                                                </tr>
                                                <?                                                 
                                            }
                                            ?> 
                                            </tbody>                                           
                                        </table>
                                    </td>
                                    <td width="50"><p><? echo $row[csf('year')]; ?>&nbsp;</p></td>
                                    <td width="100"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
									<td width="100" align="center"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
                                    <td width="100" align="center"><? if($row[csf('costing_date')]!="0000-00-00") echo change_date_format($row[csf('costing_date')]); ?>&nbsp;</td>
									<td width="100" align="center" ><? if($row[csf('est_ship_date')]!="0000-00-00") echo change_date_format($row[csf('est_ship_date')]); ?>&nbsp;</td>
                                    <td width="50" align="center"><a href="##" onClick="openImgFile('<? echo $row[csf('job_no')];?>','img');">View</a></td>
									<td width="50" align="center"><a href="javascript:void()" onClick="openPopup('<? echo $row[csf('job_id')];?>','Job File Pop up','job_file_popup')">File</a></td>
                                    <td width="100"><p><? echo ucfirst ($user_arr[$row[csf('inserted_by')]]); ?>&nbsp;</p></td>
                                    <td width="80"><? $insertDate =  explode(" ",$row[csf('insert_date')]); echo change_date_format($insertDate[0]); ?></td>
                                    <td width="80"><p><? echo ucfirst ($user_arr[$row[csf('updated_by')]]); ?>&nbsp;</p></td>
                                    <td width="80"><? $upDate =  explode(" ",$row[csf('update_date')]); echo change_date_format($upDate[0]); ?></td>
                                    <td width="">&nbsp;</td>
                                </tr>
								<?
								$i++;
							}
						}
                       
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="1280" class="rpt_table">
				<tfoot>
                    <td width="50">&nbsp;</td>
                    <td colspan="" width="88" align="center" ><input type="checkbox" id="all_check" onclick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onclick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>,<? echo $approvePermission;?>)"/></td>
				</tfoot>
			</table>
        </fieldset>
    </form>         
<?
	exit();	
}


if($action=="job_file_popup")
{
	echo load_html_head_contents("Job Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$job_file=sql_select("SELECT id, master_tble_id, image_location, real_file_name from common_photo_library where is_deleted=0 and form_name = 'pre_cost_v2'	and file_type = 2 and master_tble_id='$data'");
	?>
	<fieldset style="width:670px; margin-left:3px">
		<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
			<thead>
				<tr>
					<th>SL</th>
					<th>File Name</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
			<?
				$i=1;
				foreach($job_file as $row){
					$filename_arr=explode(".", $row[csf('real_file_name')]);
				?>
					<tr>
						<td><?= $i ?></td>
						<td><?= $filename_arr[0]; ?></td>
						<td><a href="../../<?= $row[csf('image_location')];  ?>" download>download</a></td>
					</tr>
				<?
				$i++;
				}
			?>
			</tbody>
		</table>
	</fieldset>
	<?
}




if ($action=="approve")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$msg=''; $flag=''; $response='';
	 

	if($approval_type==2)
	{				      
        $costComponentsArr = explode(",",$costComponents); 
		$id = return_next_id( "id","co_com_pre_costing_approval", 1 );
		$id_his= return_next_id( "id","co_com_pre_costing_app_his", 1 );
        foreach ($costComponentsArr as $costComponent){
			list($job,$cost_id,$mst_id,$val)=explode("*",$costComponent);
			$deletedJob[$job]=$job;			
        } 

		$field_array_cost="id, entry_form, mst_id, job_no,cost_component_id, current_approval_status,higher_othorized_approved, approved_by, approved_date"; 
		$field_array_cost_his="id, entry_form, mst_id, job_no,cost_component_id, current_approval_status,higher_othorized_approved, approved_by, approved_date"; 
        
        $data_array_cost=""; $mstIds="";
        foreach ($costComponentsArr as $costComponent) {
            if($data_array_cost!="") $data_array_cost.=","; 
            if($data_array_cost_his!="") $data_array_cost_his.=",";
			list($job,$cost_id,$mst_id,$val)=explode("*",$costComponent);
			
			if($val==1)
            {
				$check_jobList[$mst_id]+=1; 
            } 
			$data_array_cost.="(".$id.",15,".$mst_id.",'".$job."',".$cost_id.",".$val.",1,".$user_id.",'".$pc_date_time."')";
			$data_array_cost_his.="(".$id_his.",15,".$mst_id.",'".$job."',".$cost_id.",".$val.",1,".$user_id.",'".$pc_date_time."')"; 
			
			$id++;
			$id_his++; 
			
            if($mstIds=="") $mstIds= $mst_id; else $mstIds .=','.$mst_id;  
        } 
        
		
		$ApprovedJobNolist=array(); $PartialUnApprrovedJobList=array();
		foreach($check_jobList as $mst_id=>$job_data)
		{
			if($job_data==12){  $ApprovedJobNolist[$mst_id]=$mst_id;}
			elseif ($job_data<12){  $PartialUnApprrovedJobList[]=$mst_id;}
		}
		  
		$sqlCheckBack=sql_select("select id, job_no from wo_pre_cost_mst where status_active=1 and is_deleted=0 and id in ($mstIds) and ready_to_approved!=1");
		$readyToApproveNoJobLIst="";
		if(count($sqlCheckBack)>0)
		{
			foreach($sqlCheckBack as $jmrow)
			{
				if($readyToApproveNoJobLIst=="") $readyToApproveNoJobLIst=$jmrow[csf('job_no')]; else $readyToApproveNoJobLIst.=",".$jmrow[csf('job_no')];
			}
			if($readyToApproveNoJobLIst!="")
			{
				echo "40**".$readyToApproveNoJobLIst; disconnect($con); die;
			}
		}
		
		if(count($deletedJob)>0){
            $deleteSuccess = execute_query("DELETE FROM co_com_pre_costing_approval WHERE job_no in('".implode("','",$deletedJob)."')",1);
		}
		
		$rID = sql_insert("co_com_pre_costing_approval",$field_array_cost,$data_array_cost,0);
		$rID_his = sql_insert("co_com_pre_costing_app_his",$field_array_cost_his,$data_array_cost_his,0);        
        
		$rID1=$rID2=true;
        if (!empty($ApprovedJobNolist)) 
        {        
            $rID1 = sql_multirow_update("wo_pre_cost_mst","higher_othorized_approved*approved*partial_approved*ready_to_approved",'1*1*1*1',"id",implode(",",$ApprovedJobNolist),1);  

			//new................
			$history_up_id_sql_res = sql_select("select max(id) as ID,MST_ID from APPROVAL_HISTORY where ENTRY_FORM=15 and mst_id in(".implode(",",$ApprovedJobNolist).") group by mst_id");
			foreach ($history_up_id_sql_res as $hisRowId)
			{
				$up_his_id[$hisRowId[MST_ID]]=$hisRowId[ID];                  
			}
			
			sql_multirow_update("approval_history","current_approval_status*APPROVED_BY*APPROVED_DATE*FULL_APPROVED","1*$user_id*'$pc_date_time'*1","id",implode(",",$up_his_id),1); 
			//..................add 
		
		
		}
		
		 //print_r($ApprovedJobNolist);oci_rollback($con); die;
		
		
		$rID4=true;
		if (!empty($PartialUnApprrovedJobList)) 
        {  
            $rID2 = sql_multirow_update("wo_pre_cost_mst","higher_othorized_approved*approved*partial_approved",'3*2*2',"id",implode(",",$PartialUnApprrovedJobList),1);  
        }
        if ($rID && $rID1 && $rID2 && $deleteSuccess){$flag=1;$msg='19';} else {$flag=0;$msg='21';}
	}
	else
	{
		$id = return_next_id( "id","co_com_pre_costing_approval", 1 ); 
		$id_his= return_next_id( "id","co_com_pre_costing_app_his", 1 ); 				
        $costComponentsArr = explode(",",$costComponents); 

        $updateCostId = array(); $unApprovedJoblist_arr=array();
        foreach ($costComponentsArr as $costComponent) {
            if($data!="") $data.=","; 
			list($job,$cost_id,$mst_id,$val)=explode("*",$costComponent);           
            if($updateCostId[$job][$cost_id] !=1) {
                $deletedJob[$job] = $job;   
            }
			if($val==0)
            {
                $unApprovedJoblist_arr[$job] = $mst_id; 
            }			
        }
		
		//print_r($unApprovedJobNolist); die;
		if(count($deletedJob)>0){
            $deleteSuccess = execute_query("DELETE FROM co_com_pre_costing_approval WHERE job_no in('".implode("','",$deletedJob)."') and current_approval_status =1",1);
		}
        
		
        $field_array_cost="id, entry_form, mst_id, job_no,cost_component_id, current_approval_status,higher_othorized_approved, approved_by, approved_date"; 
        $field_array_cost_his="id, entry_form, mst_id, job_no,cost_component_id, current_approval_status,higher_othorized_approved, un_approved_by, un_approved_date";
        $data_array_cost="";
        
        $unApprovedJobNolist = array(); 
		$check_jobList=array();
        foreach ($costComponentsArr as $costComponent) {     
            if($data_array_cost!="") $data_array_cost.=","; 
            if($data_array_cost_his!="") $data_array_cost_his.=","; 
			list($job,$cost_id,$mst_id,$val)= explode("*",$costComponent);  
            if($val==0)
            {
				$check_jobList[$mst_id]+=1; 
            } 
			           
            $data_array_cost.="(".$id.",15,".$mst_id.",'".$job."',".$cost_id.",".$val.",2,".$user_id.",'".$pc_date_time."')"; 
            $data_array_cost_his.="(".$id.",15,".$mst_id.",'".$job."',".$cost_id.",".$val.",2,".$user_id.",'".$pc_date_time."')";
            $id++; 
            $id_his++;           
        }       
      
		$unApprovedJobNolist=array(); $PartialUnApprrovedJobList=array();
		foreach($check_jobList as $mst_id=>$job_data)
		{
			if($job_data==12){  $unApprovedJobNolist[$mst_id]=$mst_id;}
			elseif ($job_data<12){  $PartialUnApprrovedJobList[]=$mst_id;}
		}
		//echo "21**insert into co_com_pre_costing_app_his($field_array_cost_his)values".$data_array_cost_his;die;
        $rID = sql_insert("co_com_pre_costing_approval",$field_array_cost,$data_array_cost,0);
        $rID_his = sql_insert("co_com_pre_costing_app_his",$field_array_cost_his,$data_array_cost_his,0);  
  
   //echo "21**insert into co_com_pre_costing_approval($field_array_cost)values".$data_array_cost; ;oci_rollback($con); die;
  
  
  		$rID1=$rID2= $rID3=true;
        if (!empty($unApprovedJobNolist)) 
        {        
            $rID1 = sql_multirow_update("wo_pre_cost_mst","higher_othorized_approved*approved*partial_approved*ready_to_approved",'2*2*2*0',"id",implode(",",$unApprovedJobNolist),1);  
           //new................
		   $query = "UPDATE approval_history SET current_approval_status=0,un_approved_by=$user_id,un_approved_date='$pc_date_time',FULL_APPROVED=0 WHERE entry_form=15 and mst_id in(".implode(",",$unApprovedJobNolist).")"; 
           execute_query($query,1);
		   //......................add; 
		}
		
		 //print_r($ApprovedJobNolist);oci_rollback($con); die;
		
		
		$rID4=true;
		if (!empty($PartialUnApprrovedJobList)) 
        {  
            $rID2 = sql_multirow_update("wo_pre_cost_mst","higher_othorized_approved*approved*partial_approved*ready_to_approved",'3*2*2*0',"id",implode(",",$PartialUnApprrovedJobList),1);  
        
           //new................
		   $query = "UPDATE approval_history SET current_approval_status=0,un_approved_by=$user_id,un_approved_date='$pc_date_time',FULL_APPROVED=0 WHERE entry_form=15 and mst_id in(".implode(",",$PartialUnApprrovedJobList).")"; 
           execute_query($query,1);
		   //......................add; 
		
		
		}
      //  echo $rID.'='.$rID1.'='.$rID2.'='.$deleteSuccess.'='.$rID_his; die;
        if ($rID && $rID1 && $rID2 && $deleteSuccess && $rID_his){$flag=1;$msg='20';} else {$flag=0;$msg='21';}
		
		if($flag==1)
		{
			$unapproved_no_array=array(); $unapproved_job_array=array();
			if (count($unApprovedJoblist_arr)>0) 
			{        
				foreach ($unApprovedJoblist_arr as $jobNo=>$mst_id) {               
					$unapproved_no_array[$mst_id]=$mst_id;
					$unapproved_job_array[$mst_id]=$jobNo;		                  
				} 
				//echo "10**";
				//$max_approved_sql = sql_select("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in(".implode(",",$unapproved_no_array).") and entry_form=15 group by mst_id");
				$max_approved_sql = sql_select("select PRE_COST_MST_ID as mst_id, max(APPROVED_NO) as approved_no from wo_pre_cost_mst_histry where PRE_COST_MST_ID in(".implode(",",$unapproved_no_array).")   group by PRE_COST_MST_ID");
				$approvedNoArr= array();
				foreach ($max_approved_sql as $approvedRow) {          
					$approvedNoArr[$approvedRow[csf('mst_id')]] = $approvedRow[csf('approved_no')];           
				}
				
				foreach($unapproved_job_array as $mst_id=>$jobNo)
				{
					$job="'".$jobNo."'";
					//echo $job; die;
					$app_no=$approvedNoArr[$mst_id]+1;
					//============================wo_pre_cost_mst_histry============
					$sql_insert="insert into wo_pre_cost_mst_histry(id, approved_no, pre_cost_mst_id, garments_nature, job_no, costing_date, incoterm, incoterm_place, machine_line, prod_line_hr, costing_per, remarks, copy_quatation, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, efficiency_wastage_percent, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted,history_insert_date) 
							select	
							'', $app_no, id, garments_nature, job_no, costing_date, incoterm, incoterm_place, machine_line, prod_line_hr, costing_per, remarks, copy_quatation, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, efficiency_wastage_percent, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted,'$pc_date_time'
					from wo_pre_cost_mst where job_no in ($job)";
					//echo $sql_insert; die;
					//============================wo_pre_cost_dtls_histry============
					$sql_precost_dtls="insert into wo_pre_cost_dtls_histry (id, approved_no, pre_cost_dtls_id, job_no, costing_per_id, order_uom_id, fabric_cost,  fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, commission, commission_percent,	lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, cost_pcs_set, cost_pcs_set_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted)
					select	
					'', $app_no, id, job_no, costing_per_id, order_uom_id, fabric_cost,  fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, commission, commission_percent,	lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, cost_pcs_set, cost_pcs_set_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_pre_cost_dtls  where job_no in ($job)";
					
					
						
					//--------------------------------------wo_pre_cost_fabric_cost_dtls_h---------------------------------------------------------------------------
					$sql_precost_fabric_cost_dtls="insert into wo_pre_cost_fabric_cost_dtls_h( id, approved_no, pre_cost_fabric_cost_dtls_id, job_no, item_number_id, body_part_id, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, color_size_sensitive, color, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, company_id, costing_per, consumption_basis, process_loss_method, cons_breack_down, msmnt_break_down, color_break_down, yarn_breack_down, marker_break_down, width_dia_type)
					select	
					'', $app_no, id,job_no, item_number_id, body_part_id, fab_nature_id, color_type_id,lib_yarn_count_deter_id,construction,composition, fabric_description, gsm_weight,color_size_sensitive,	color, avg_cons, fabric_source, rate,amount,avg_finish_cons,avg_process_loss, inserted_by, insert_date,updated_by,update_date, status_active, is_deleted, company_id, costing_per,consumption_basis,process_loss_method,cons_breack_down,msmnt_break_down,color_break_down,yarn_breack_down,marker_break_down,width_dia_type from wo_pre_cost_fabric_cost_dtls where  job_no in ($job)";
					//echo $sql_precost_fabric_cost_dtls;die;
					
					//--------------------------------------wo_pre_cost_fab_yarn_cst_dtl_h---------------------------------------------------------------------------
					$sql_precost_fab_yarn_cst="insert into wo_pre_cost_fab_yarn_cst_dtl_h(id,approved_no,pre_cost_fab_yarn_cost_dtls_id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
					select	
					'', $app_no, id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,
					inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_fab_yarn_cost_dtls  where  job_no in ($job)";
					//echo $sql_precost_fab_yarn_cst;die;
					
					//--------------------------------------wo_pre_cost_comarc_cost_dtls_h---------------------------------------------------------------------------
					$sql_precost_fcomarc_cost_dtls="insert into wo_pre_cost_comarc_cost_dtls_h(id,approved_no,pre_cost_comarci_cost_dtls_id,job_no,item_id,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
					select	
					'', $app_no,id,job_no,item_id,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,
					is_deleted from wo_pre_cost_comarci_cost_dtls where  job_no in ($job)";
					//echo $sql_precost_fcomarc_cost_dtls;die;
					
					
					//--------------------------------------  wo_pre_cost_commis_cost_dtls_h---------------------------------------------------------------------------
					$sql_precost_commis_cost_dtls="insert into wo_pre_cost_commis_cost_dtls_h(id,approved_no,pre_cost_commiss_cost_dtls_id,job_no,particulars_id,
					commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
					select	
					'', $app_no, id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_commiss_cost_dtls where  job_no in ($job)";
					//	echo $sql_precost_commis_cost_dtls;die;
					
					//--------------------------------------   wo_pre_cost_embe_cost_dtls_his---------------------------------------------------------------------------
					$sql_precost_embe_cost_dtls="insert into  wo_pre_cost_embe_cost_dtls_his(id,approved_no,pre_cost_embe_cost_dtls_id,job_no,emb_name,
					emb_type,cons_dzn_gmts,rate,amount,charge_lib_id,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
					select	
					'', $app_no, id,job_no,emb_name,
					emb_type,cons_dzn_gmts,rate,amount,charge_lib_id,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_embe_cost_dtls  where  job_no in ($job)";
					//echo $sql_precost_commis_cost_dtls;die;
					
					//----------------------------------------------------wo_pre_cost_fab_yarnbkdown_his------------------------------------------------------------------------
					
					$sql_precost_fab_yarnbkdown_his="insert into  wo_pre_cost_fab_yarnbkdown_his(id,approved_no,pre_cost_fab_yarnbreakdown_id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
					select	
					'', $app_no, id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_fab_yarnbreakdown  where  job_no in ($job)";
					//echo $sql_precost_fab_yarnbkdown_his;die;
					
					//----------------------------------------------------wo_pre_cost_sum_dtls_histroy------------------------------------------------------------------------
					
					$sql_precost_fab_sum_dtls="insert into  wo_pre_cost_sum_dtls_histroy(id,approved_no,pre_cost_sum_dtls_id,job_no,fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_woven_fin_req_yds,fab_knit_fin_req_kg,fab_amount,avg,yarn_cons_qnty,yarn_amount,conv_req_qnty,conv_charge_unit,conv_amount,trim_cons,trim_rate,trim_amount,emb_amount,comar_rate,
					comar_amount,commis_rate,commis_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
					select	
					'', $app_no, id,job_no,fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_woven_fin_req_yds,fab_knit_fin_req_kg,fab_amount,avg,yarn_cons_qnty,yarn_amount,conv_req_qnty,conv_charge_unit,conv_amount,trim_cons,trim_rate,trim_amount,emb_amount,comar_rate,
					comar_amount,commis_rate,commis_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_sum_dtls  where  job_no in ($job)";
					//echo $sql_precost_fab_sum_dtls;die;
					//----------------------------------------------------wo_pre_cost_trim_cost_dtls_his------------------------------------------------------------------------
					
					$sql_precost_trim_cost_dtls="insert into  wo_pre_cost_trim_cost_dtls_his(id,approved_no,pre_cost_trim_cost_dtls_id,job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,cons_breack_down,inserted_by, insert_date,updated_by,update_date, status_active,is_deleted)
					select	
					'', $app_no, id,job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,cons_breack_down,inserted_by, insert_date,updated_by,update_date, status_active,is_deleted from wo_pre_cost_trim_cost_dtls  where  job_no in ($job)";
					//echo $sql_precost_trim_cost_dtls;die;
					
					
					//----------------------------------------------------wo_pre_cost_trim_co_cons_dtl_h------------------------------------------------------------------------
					
					$sql_precost_trim_co_cons_dtl="insert into   wo_pre_cost_trim_co_cons_dtl_h(id,approved_no,pre_cost_trim_co_cons_dtls_id,wo_pre_cost_trim_cost_dtls_id,job_no, po_break_down_id,item_size, cons, place, pcs,country_id)
					select	
					'', $app_no, id,wo_pre_cost_trim_cost_dtls_id,job_no,po_break_down_id,item_size, cons,place, pcs,country_id from wo_pre_cost_trim_co_cons_dtls  where  job_no in ($job)";
					//----------------------------------------------------wo_pre_cost_fab_con_cst_dtls_h------------------------------------------------------------------------
					
					$sql_precost_fab_con_cst_dtls="insert into   wo_pre_cost_fab_con_cst_dtls_h(id,approved_no,pre_cost_fab_conv_cst_dtls_id,job_no, fabric_description,cons_process,req_qnty,charge_unit,amount,color_break_down,charge_lib_id,inserted_by,insert_date,updated_by,update_date, status_active,is_deleted)
					select	
					'', $app_no, id,job_no, fabric_description,cons_process,req_qnty,charge_unit,amount,color_break_down,charge_lib_id,inserted_by,insert_date,updated_by,update_date, status_active,is_deleted from wo_pre_cost_fab_conv_cost_dtls  where  job_no in ($job)";
					if(count($sql_precost_trim_cost_dtls)>0)
					{
						$rID12=execute_query($sql_precost_trim_cost_dtls,1);
						if($flag==1) 
						{
							if($rID12) $flag=1; else $flag=0; 
						} 
					}
					
					if(count($sql_precost_trim_cost_dtls)>0)
					{
						$rID13=execute_query($sql_precost_trim_co_cons_dtl,1);
						if($flag==1) 
						{
							if($rID13) $flag=1; else $flag=0; 
						}
					}
					
					//echo $sql_precost_fab_con_cst_dtls;die;
					$rID13=execute_query($sql_precost_fab_con_cst_dtls,1);
					if($flag==1) 
					{
						if($rID13) $flag=1; else $flag=0; 
					}
					
					if(count($sql_insert)>0)
					{
						$rID3=execute_query($sql_insert,0);
						if($flag==1) 
						{
							if($rID3) $flag=1; else $flag=0; 
						} 
					}
					if(count($sql_precost_dtls)>0)
					{
						$rID4=execute_query($sql_precost_dtls,1);
						if($flag==1) 
						{
							if($rID4) $flag=1; else $flag=0; 
						}
					}
					if(count($sql_precost_fabric_cost_dtls)>0)
					{
						$rID5=execute_query($sql_precost_fabric_cost_dtls,1);
						if($flag==1) 
						{
							if($rID5) $flag=1; else $flag=0; 
						} 
					}
					if(count($sql_precost_fab_yarn_cst)>0)
					{
						$rID6=execute_query($sql_precost_fab_yarn_cst,1);
						if($flag==1) 
						{
							if($rID6) $flag=1; else $flag=0; 
						} 
					}
					if(count($sql_precost_fcomarc_cost_dtls)>0)
					{	
						$rID7=execute_query($sql_precost_fcomarc_cost_dtls,1);
						if($flag==1) 
						{
							if($rID7) $flag=1; else $flag=0; 
						} 			
					}
					if(count($sql_precost_commis_cost_dtls)>0)
					{
						$rID8=execute_query($sql_precost_commis_cost_dtls,1);
						if($flag==1) 
						{
							if($rID8) $flag=1; else $flag=0; 
						}
					}
					if(count($sql_precost_embe_cost_dtls)>0)
					{
						$rID9=execute_query($sql_precost_embe_cost_dtls,1);
						if($flag==1) 
						{
							if($rID9) $flag=1; else $flag=0; 
						} 
					}
					if(count($sql_precost_fab_yarnbkdown_his)>0)
					{
						$rID10=execute_query($sql_precost_fab_yarnbkdown_his,1);
						if($flag==1) 
						{
							if($rID10) $flag=1; else $flag=0; 
						} 
					}
					if(count($sql_precost_fab_sum_dtls)>0)
					{
						$rID11=execute_query($sql_precost_fab_sum_dtls,1);
						if($flag==1) 
						{
							if($rID11) $flag=1; else $flag=0; 
						} 
					}
				}
			}
		}
        
	}
    
	if($db_type==0)
		{ 
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo $msg."**".$response;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo $msg."**".$response;
			}
		}
	
	if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo $msg."**".$response;
			}
			else
			{
				oci_rollback($con); 
				echo $msg."**".$response;
			}
		}
	disconnect($con);
	die;
	
}

if($action=="img")
{
	echo load_html_head_contents("Image View", "../../", 1, 1,'','','');
	extract($_REQUEST);
?>
	<fieldset style="width:600px; margin-left:5px">
		<div style="width:100%; word-wrap:break-word" id="scroll_body">
             <table border="0" rules="all" width="100%" cellpadding="2" cellspacing="2">
             	<tr>
					<?
					$i=0;
                    $sql="select image_location from common_photo_library where master_tble_id='$id' and form_name='knit_order_entry' and file_type=1";
					
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
						$i++;
                    ?>
                    <!--<td align="center"><? echo $row[csf('image_location')];?></td>-->
                    	<td align="center"><img width="300px" height="180px" src="../../<? echo $row[csf('image_location')];?>" /></td>
                    <?
						if($i%2==0) echo "</tr><tr>";
                    }
                    ?>
                </tr>
            </table>
        </div>	
	</fieldset>     
<?
exit();
}
?>