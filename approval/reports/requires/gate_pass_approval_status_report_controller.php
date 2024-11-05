<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_ip=$_SESSION['logic_erp']['user_ip'];

extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../../includes/common.php');
 	
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	exit();
}




if($action=="report_generate")
{  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$cbo_buyer_id = str_replace("'","",$cbo_buyer_id);
	$cbo_basis = str_replace("'","",$cbo_basis);
	$txt_gate_pass_id = str_replace("'","",$txt_gate_pass_id);
	$txt_job_no = str_replace("'","",$txt_job_no);
	$txt_style_number = str_replace("'","",$txt_style_number);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$approval_type = str_replace("'","",$cbo_approval_type);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;

	
	$gate_pass_print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$cbo_company_id."' and module_id=6 and report_id=38 and is_deleted=0 and status_active=1");	
    $gate_format_ids=explode(",",$gate_pass_print_report_format);
    $print_btn=$gate_format_ids[0];
	//print_r($print_btn);

     

	if($cbo_buyer_id){$where_con .= " and b.BUYER_ID =".$cbo_buyer_id.""; }
	if($txt_date_from && $txt_date_to){
		$where_con .= " and a.WO_DATE BETWEEN '".$txt_date_from."' AND '".$txt_date_to."'";	
	}
	if($cbo_search_type==1 && $txt_search_data!=''){
		$where_con .= " and a.SYS_NUMBER like('%".$txt_search_data."')"; 
	}
	else if($cbo_search_type==2 && $txt_search_data!=''){
		$where_con .= " and c.JOB_NO_MST like('%".$txt_search_data."')"; 
	}
	else if($cbo_search_type==3 && $txt_search_data!=''){
		$where_con .= " and c.PO_NUMBER like('%".$txt_search_data."')"; 
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$search_by_arr=array(2=>"Pending",3=>"Partial Approved",1=>"Full Approved");
	$precostArr=return_library_array( "select job_no, cm_cost from wo_pre_cost_dtls", "job_no", "cm_cost"  );
	$designation_arr = return_library_array( "SELECT id, custom_designation from lib_designation", "id", "custom_designation" );
	 //echo $sql;
	//............................................................................
	
   
	
	//var_dump($electronicDataArr);die;
	
 
	 
	
	if($approval_type==0) // Un-Approve/pending
	{   $approved_cond=" and a.APPROVED in(0,2)";  
		$sql .= "SELECT A.ID,a.DEPARTMENT_ID, A.SYS_NUMBER_PREFIX_NUM, A.SYS_NUMBER, A.BASIS, A.COMPANY_ID , A.OUT_DATE, 0 AS APPROVAL_ID, A.APPROVED , A.COM_LOCATION_ID ,A.CHALLAN_NO,A.RETURNABLE
		from inv_gate_pass_mst a
		where a.company_id=$cbo_company_id and a.is_deleted=0 and a.status_active=1 and a.approved<>1 and a.ready_to_approved=1 $basis_cond $system_id_cond $date_cond $approved_cond 
		group by a.id,a.DEPARTMENT_ID, a.sys_number_prefix_num, a.sys_number, a.basis, a.company_id, a.insert_date, a.out_date,  a.approved,a.com_location_id ,a.challan_no,a.returnable";

			
		
	}
	else if($approval_type==2){ //Full_approved)
	    $approved_cond=" and a.APPROVED=1";  
		$sql .= "SELECT A.ID,a.DEPARTMENT_ID, A.SYS_NUMBER_PREFIX_NUM, A.SYS_NUMBER, A.BASIS, A.COMPANY_ID , A.OUT_DATE, 0 AS APPROVAL_ID, A.APPROVED , A.COM_LOCATION_ID ,A.CHALLAN_NO,A.RETURNABLE
		from inv_gate_pass_mst a
		where a.company_id=$cbo_company_id and a.is_deleted=0 and a.status_active=1 and a.approved<>0 and a.ready_to_approved=1 $basis_cond $system_id_cond $date_cond $approved_cond
		group by a.id,a.DEPARTMENT_ID, a.sys_number_prefix_num, a.sys_number, a.basis, a.company_id, a.insert_date, a.out_date,  a.approved,a.com_location_id ,a.challan_no,a.returnable";

    }
	else
	{
		$approved_cond=" and a.APPROVED=3";   //Full_approved
		$sql .= "SELECT A.ID,a.DEPARTMENT_ID, A.SYS_NUMBER_PREFIX_NUM, A.SYS_NUMBER, A.BASIS, A.COMPANY_ID , A.OUT_DATE, 0 AS APPROVAL_ID, A.APPROVED , A.COM_LOCATION_ID ,A.CHALLAN_NO,A.RETURNABLE
		from inv_gate_pass_mst a
		where a.company_id=$cbo_company_id and a.is_deleted=0 and a.status_active=1 and a.approved<>1 and a.ready_to_approved=1 $basis_cond $system_id_cond $date_cond $approved_cond 
		group by a.id,a.DEPARTMENT_ID, a.sys_number_prefix_num, a.sys_number, a.basis, a.company_id, a.insert_date, a.out_date,  a.approved,a.com_location_id ,a.challan_no,a.returnable";
	}
	  //echo $sql;die();
     
	 $nameArray=sql_select( $sql );
	 $sys_id_arr=array();
	 foreach ($nameArray as $row)
	 {
		$office_note_id.=$row['ID'].',';
	 }
	
	 $office_note_ids=rtrim($office_note_id,',');
	

	 $signatory_data = sql_select("SELECT company_id as COMPANY_ID, user_id as USER_ID, sequence_no as SEQUENCE_NO, bypass as BYPASS from electronic_approval_setup where company_id in($cbo_company_id) and is_deleted=0 and entry_form=59 order by sequence_no");
	 
	$signatory_data_arr=array();
	foreach ($signatory_data as $row) {
		$signatory_data_arr[$row['COMPANY_ID']][$row['USER_ID']]['USER_ID']=$row['USER_ID'];
		$signatory_data_arr[$row['COMPANY_ID']][$row['USER_ID']]['SEQUENCE_NO']=$row['SEQUENCE_NO'];
		$signatory_data_arr[$row['COMPANY_ID']][$row['USER_ID']]['BYPASS']=$row['BYPASS'];
		$rowspan_arr[$row['COMPANY_ID']]++;
	}
// 	echo "<pre>";
// print_r($signatory_data_arr); 
//   echo "</pre>";die();
    $user_name_array = array();
	$userData = sql_select( "SELECT id as ID, user_name as USER_NAME, user_full_name as USER_FULL_NAME, designation as DESIGNATION from user_passwd where valid=1");
	foreach($userData as $user_row)
	{
		$user_name_array[$user_row['ID']]['NAME']=$user_row['USER_NAME'];
		$user_name_array[$user_row['ID']]['FULL_NAME']=$user_row['USER_FULL_NAME'];
		$user_name_array[$user_row['ID']]['DESIGNATION']=$designation_arr[$user_row['DESIGNATION']];	
	}

	$sql_approved="SELECT mst_id as MST_ID, max(approved_no) as APPROVED_NO, approved_by as APPROVED_BY, max(approved_date) as APPROVED_DATE from approval_history where entry_form=59 and un_approved_by=0 and mst_id in($office_note_ids) group by mst_id, approved_by";
	//echo $sql_approved;die();
	$sql_approved_res=sql_select($sql_approved);
	foreach ($sql_approved_res as $row) {
		$approval_arr[$row['MST_ID']][$row['APPROVED_BY']]['APPROVED_DATE']=$row['APPROVED_DATE'];
	}

//      	echo "<pre>";
// print_r($user_name_array); 
//   echo "</pre>";die();

    ob_start();
	$width=950;
    ?> 


    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<? echo $width+25; ?>px;">
        <legend>Garments Service Work Order Report</legend>
        <div style="width:<? echo $width+25; ?>px; margin:0 auto;">
        	
            <table width="<? echo $width+25; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="left" >
                <thead>
					<!-- <th width="25"></th>                    -->
                    <th width="35">SL</th>                   
                    <th width="150"> Gate Pass ID</th>
                    <th width="150"> Basis </th>
                    <th width="100">Signatory</th>
                    <th width="100">Disgination</th>                   
                    <th width="100">Approval Date & Time</th>
					                 
                    <th width="100">App Status</th>                   
                </thead>
            </table>            
            <div style="min-width:<? echo $width+25; ?>px; float:left; overflow-y:auto; max-height:330px;">
                <table width="<? echo $width+25; ?>" cellspacing="0" cellpadding="0" border="1" rules="all"  class="rpt_table" id="tbl_list_search" align="left">
                    <tbody> 
                     <?
						$i=1; 
						foreach($nameArray as $office_id => $row)
						{ 
						
						 ?>
                        
                          <tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_<?= $i; ?>"> 
                                    <td width="35" align="center" rowspan="<?= $rowspan_arr[$row['COMPANY_ID']]; ?>"><?= $i;?></td>
									<td width="150" align="center" rowspan="<?= $rowspan_arr[$row['COMPANY_ID']]; ?>"> <a href="##" onClick="generate_trims_print_report('<?= $row['COMPANY_ID']?>','<?= $row['SYS_NUMBER']?>','<?= $print_btn ?>','<?= $row['COM_LOCATION_ID']?>','<?= $row['CHALLAN_NO']?>','<?= $row['BASIS']?>','<?= $row['RETURNABLE']?>')"><?= $row['SYS_NUMBER']; ?></a></td>   
                                    <td width="150" rowspan="<?= $rowspan_arr[$row['COMPANY_ID']]; ?>"><?= $get_pass_basis[$row['BASIS']]; ?></td>
									 <?
							if (!empty($signatory_data_arr))
							{
								foreach ($signatory_data_arr as $company_id => $user_data) 
								{
									$m=1;
									if ($company_id==$row['COMPANY_ID'])
									{						
										foreach ($user_data as $userid => $val) 
										{
											if ($m!=1)
											{
												?>
												<tr bgcolor="<? echo $bgcolor; ?>">
												<?
											}
													?>                                   
											<td width="100" ><? echo $user_name_array[$userid]['NAME']; ?></td>
											<td width="100"><? echo $user_name_array[$userid]['DESIGNATION']; ?> </td>				
											<td width="100" align="center"><? echo $approval_arr[$office_id][$userid]['APPROVED_DATE']; ?></td>
										
											<td width="100"><? if($row[csf('APPROVED')]==1){
									       {echo " Approved";}
									     }else if($row[csf('APPROVED')]==3){echo "Partial Approved";}
									       else {echo "Pending";}
									?></td>		 
																			
											</tr> 
											<?
											$m++;
										
									    }
									}else
									{
										?>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										
										<td width="100">&nbsp;</td>
										</tr>
										<?
									}							
							    }
					       }
							else
							{ 
								?>								
								<td width="100">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="100">&nbsp;</td>
								
								<td width="100">&nbsp;</td>
								</tr>
								<?
							}				
                        	$i++; 
                        } 
                        ?>                  
                    </tbody>
                </table>
            </div>
            
        </div>
        </fieldset>
        
    </form>
    
	<?
    foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}

	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename="requires/".$user_name."_".$name.".xls";
	echo "$total_data####$filename";
	exit(); 
}

if($action=="full_approved_popup")
{   extract($_REQUEST);
	list($company_id,$sys_id,$approved_no)=explode('_',$data);
   // print_r($approved_no);

	echo load_html_head_contents($tittle." Info", "../../../", 1, 1,'','','');

	$designation_a=return_library_array("select id,custom_designation from lib_designation","id","custom_designation");
	$department_arr=return_library_array( "select id,DEPARTMENT_NAME from LIB_DEPARTMENT comp where status_active =1 and is_deleted=0 order by DEPARTMENT_NAME",'id','DEPARTMENT_NAME');

	$hisSql =  "select MST_ID,APPROVED_BY,APPROVED_DATE, SEQUENCE_NO from APPROVAL_HISTORY where ENTRY_FORM=68 and MST_ID in(".$sys_id.") order by sequence_no";
	//echo $hisSql;die();
	$hisSqlRes=sql_select($hisSql);
	foreach($hisSqlRes as $row){
		$row['APPROVED_DATE']=strtotime($row['APPROVED_DATE']);
		
		$sys_id_arr[$row['APPROVED_BY']]=array(
		'APPROVED_BY'=>$row['APPROVED_BY'],
		'SEQUENCE_NO'=>$row['SEQUENCE_NO'],
		'APPROVED_DATE'=>date('d-m-Y h:i:s A',$row['APPROVED_DATE']),
	  );
	  $userIdArr[$row['APPROVED_BY']]=$row['APPROVED_BY'];
	}

// 		echo "<pre>";
// print_r($sys_id_arr); 
//   echo "</pre>";die();

    //$users = implode(',',$dataArr[$erosion_id]);
	$sql="select a.USER_ID,a.DEPARTMENT,b.DESIGNATION,b.USER_NAME from electronic_approval_setup a,user_passwd b,EROSION_ENTRY c where b.id=a.USER_ID ".where_con_using_array($userIdArr,0,'a.USER_ID')."";
     //echo $sql;die();
    $sql_res=sql_select($sql);
	foreach($sql_res as $row){
		$userName[$row['USER_ID']]=$row['USER_NAME'];
		$userDeg[$row['USER_ID']]=$row['DESIGNATION'];
		if($department_arr[$row['DEPARTMENT']]!=''){$userDep[$row['USER_ID']][$row['DEPARTMENT']]=$department_arr[$row['DEPARTMENT']];}
	}

	$hisSql ="select MST_ID,REFUSING_REASON from REFUSING_CAUSE_HISTORY where ENTRY_FORM=68 and MST_ID in(".$sys_id.")";
   // echo $hisSql;die();
	$hisSqlRes=sql_select($hisSql);
	$refusing_res_arr = [];
	foreach($hisSqlRes as $key => $row){
		$refusing_res_arr[$key+2] = $row['REFUSING_REASON'];

	}

  ?> 

  <div  id="data_panel" align="center" style="width:99%">
    <fieldset style="width: 100%">
       <table width="100%" cellspacing="0" class="rpt_table" border="0" id="tbl_returnable_details" rules="all">
         <thead>
			<tr>
				<th width="80">Department</th>
				<th width="80">Name</th>
				<th width="100">Designation</th>
				<th width="80" >Comment</th>
				<th width="80">Approve/Reject</th>
				<th width="80">Time/Date</th>
			</tr>
           
        </thead> 
		<?
	
	foreach ($sys_id_arr as $row) 
	{  
	  //$row[csf('APPROVED_DATE')] = strtotime($row[csf('APPROVED_DATE')]);
	?>  
		 

			<tr  bgColor="<?= $bgcolor;?>">
				<td><p><?=implode(', ',$userDep[$row['APPROVED_BY']]);?></p></td> 
				<td><?=$userName[$row['APPROVED_BY']];?></td>
				<td align="center"><?=$designation_a[$userDeg[$row['APPROVED_BY']]];?></td>
				<td align="center"><?=$refusing_res_arr[$row['SEQUENCE_NO']];?></td> 
				<td align="center"><? echo "Yes";?></td> 
				<td align="center"><?=$row['APPROVED_DATE'];?></td> 
				
		    </tr>
		<?
			
		}
		?>

      </table>
    </fieldset>

     
  </div>
    <?
exit(); 
 
}


if($action=='garments_service_work_order'){
	$file_arr=return_library_array( "select ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where MASTER_TBLE_ID='".$id."' and FORM_NAME='garments_service_work_order'", "ID", "IMAGE_LOCATION"  );
	foreach($file_arr as $file){
		echo "<a target='_blank' href='../../../".$file."'>Download</a> ";
	}
}


if($action=='user_popup')
{
    echo load_html_head_contents("Popup Info","../../../",1, 1,'',1,'');
    ?>  
    <script>
      function js_set_value(id)
      { 
        document.getElementById('selected_id').value=id;
      	parent.emailwindow.hide();
      }
    </script>

    <form>
            <input type="hidden" id="selected_id" name="selected_id" /> 
           <?php
            $custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');  
             $Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');   ;
             $sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.SEQUENCE_NO from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_id and b.entry_form=68 and valid=1 and a.id!=$user_id  and b.is_deleted=0 order by b.SEQUENCE_NO";
                //echo $sql;
             $arr=array (2=>$custom_designation,3=>$Department);
             echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department,Seq. No", "100,120,130,120,50,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id,0", $arr , "user_name,user_full_name,designation,department_id,SEQUENCE_NO", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
            ?>
    </form>
    <script language="javascript" type="text/javascript">
      setFilterGrid("tbl_style_ref");
    </script>
	<?
	exit();
}

 
if($action=="refusing_cause_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Comments Info","../../../", 1, 1, $unicode);
	$permission="1_1_1_1";
	$app_user_id = ($txt_alter_user_id)?$txt_alter_user_id:$user_id;
	$sql_cause="select MST_ID,REFUSING_REASON from refusing_cause_history where entry_form=68 and mst_id='$quo_id' and INSERTED_BY=$app_user_id order by id asc";	
	$nameArray_cause=sql_select($sql_cause);
	$app_cause_arr=array();
	foreach($nameArray_cause as $row)
	{
		$app_cause_arr[$row['MST_ID']]=$row['REFUSING_REASON'];
	}
	$btn_status=(count($app_cause_arr)==0)?0:1;
 
	?>
    <script>
 	var permission='<?=$permission; ?>';

	function set_values( cause )
	{
		var refusing_cause = document.getElementById('txt_refusing_cause').value;
		if(refusing_cause == '')
		{
			document.getElementById('txt_refusing_cause').value =refusing_cause;
			parent.emailwindow.hide();
		}
		else
		{
			alert("Please save Comments first or empty");
			return;
		}
	}

	function fnc_cause_info( operation )
	{
		var refusing_cause=$("#txt_refusing_cause").val();
		var quo_id=$("#hidden_quo_id").val();
  		if (form_validation('txt_refusing_cause','Comments')==false)
		{
			return;
		}
		else
		{
			var data="action=save_update_delete_refusing_cause&operation="+operation+"&refusing_cause="+refusing_cause+"&quo_id="+quo_id+"&app_user_id="+<?=$app_user_id;?>;
			http.open("POST","gate_pass_approval_status_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_cause_info_reponse;
		}
	}
	function fnc_cause_info_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split('**');
			if(response[0]==0)
			{
				alert("Data saved successfully");
				parent.emailwindow.hide();
			}
			else if(response[0]==1)
			{
				alert("Data update successfully");
				parent.emailwindow.hide();
			}
			else
			{
				alert("Data not saved");
				return;
			}
		}
	}

    </script>
    <body  onload="set_hotkey();">
    <div align="center" style="width:100%;">
	<fieldset style="width:470px;">
		<legend>Comments</legend>
		<form name="causeinfo_1" id="causeinfo_1"  autocomplete="off">
			<table cellpadding="0" cellspacing="2" width="470px">
			 	<tr>
					<td width="100" class="must_entry_caption">Comments</td>
					<td >
						<textarea name="txt_refusing_cause" id="txt_refusing_cause" class="text_boxes" style="width:320px;height: 100px;"><?=$app_cause_arr[$quo_id];?></textarea>
						<input type="hidden" name="hidden_quo_id" id="hidden_quo_id" value="<?= $quo_id;?>">
					</td>
                  </tr>
                  <tr>
					<td colspan="4" align="center" class="button_container">
						<?
						 
							echo load_submit_buttons( $permission, "fnc_cause_info", $btn_status,0 ,"reset_form('causeinfo_1','','')",1);
						 
				        ?> </br>
				        <input type="button" class="formbutton" value="Close" name="close_buttons" id="close_buttons" onClick="set_values();" style="width:50px;">
 					</td>
				</tr>
				<tr>
					<td colspan="4" align="center"></td>
				</tr>
		   </table>
			</form>
		</fieldset>
         <?
		$sqlHis="select approval_cause from approval_cause_refusing_his where entry_form=15 and approval_type=1 and booking_id='$quo_id' and status_active=1 and is_deleted=0 order by id Desc";
		$sqlHisRes=sql_select($sqlHis);
		?>
		<table align="center" cellspacing="0" width="420" class="rpt_table" border="1" rules="all">
			<thead>
				<th width="30">SL</th>
				<th>Refusing History</th>
			</thead>
		</table>
		<div style="width:420px; overflow-y:scroll; max-height:260px;" align="center">
			<table align="center" cellspacing="0" width="403" class="rpt_table" border="1" rules="all">
			<?
			$i=1;
			foreach($sqlHisRes as $hrow)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');">
					<td width="30"><?=$i; ?></td>
					<td style="word-break:break-all"><?=$hrow[csf('approval_cause')]; ?></td>
				</tr>
				<?
				$i++;
			}
			?>
			 </table>
		 </div>
	</div>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</body>
    <?
	exit();
}



if($action=="save_update_delete_refusing_cause")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $_REQUEST ));
	$flag=1;
	if(is_duplicate_field( "approval_cause", "approval_cause_refusing_his", "approval_cause='".$refusing_cause."' and entry_form=68 and booking_id='".str_replace("'", "", $quo_id)."' and approval_type=1 and status_active=1 and is_deleted=0" )==1)
	{
		//
	}
	else
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//$id_his=return_next_id( "id", "approval_cause_refusing_his", 1);
		$idpre=return_field_value("max(id) as id", "refusing_cause_history", "mst_id=".$quo_id." and entry_form=68 group by mst_id","id");
		$sqlHis="insert into approval_cause_refusing_his( id, cause_id, entry_form, booking_id, approval_type, approval_cause, inserted_by, insert_date, updated_by, update_date)
				select '', id, entry_form, mst_id, 1, refusing_reason, inserted_by, insert_date, updated_by, update_date from refusing_cause_history where mst_id=".$quo_id." and entry_form=68 and id=$idpre"; //die;
		
		if(count($sqlHis)>0)
		{
			$rID3=execute_query($sqlHis,0);
			if($flag==1)
			{
				if($rID3==1) $flag=1; else $flag=0;
			}
		}
	}
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}		
		
		$id=return_next_id( "id", "refusing_cause_history", 1);
		$field_array = "id,entry_form,mst_id,refusing_reason,inserted_by,insert_date";
		$data_array = "(".$id.",68,".$quo_id.",'".$refusing_cause."',".$app_user_id.",'".$pc_date_time."')";
		$rID=sql_insert("refusing_cause_history",$field_array,$data_array,1);
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "0**$refusing_cause";
			}
			else{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();

		$idpre=return_field_value("max(id) as id", "refusing_cause_history", "mst_id=".$quo_id." and entry_form=68 group by mst_id","id");
		$field_array="refusing_reason*updated_by*update_date";
		$data_array="'".$refusing_cause."'*".$app_user_id."*'".$pc_date_time."'";
		$rID=sql_update("refusing_cause_history",$field_array,$data_array,"id",$idpre,0);
		
		if($rID==1) $flag=1; else $flag=0;
		
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**$refusing_cause";
			}
			else{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
}


if($action=='app_mail_notification'){
	//http://localhost/platform-v3.5/approval/requires/gate_pass_approval_status_report_controller.php?data=20__reza@logicsoftbd.com__3&action=app_mail_notification

	require_once('../../../mailer/class.phpmailer.php');
	require_once('../../../auto_mail/setting/mail_setting.php');
	

	list($sys_id,$email,$alter_user_id,$company_name,$type)=explode('__',$data);
	$approved_user_id=($alter_user_id!='')?$alter_user_id:$user_id;

		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$sql = "select a.ID,a.SYS_NUMBER,a.COMPANY_ID,a.WO_DATE,c.JOB_NO_MST,c.PO_NUMBER,b.JOB_ID,b.PO_ID,b.BUYER_ID,b.AMOUNT from garments_service_wo_mst a,garments_service_wo_dtls b,WO_PO_BREAK_DOWN c where a.id=b.mst_id and b.po_id=c.id and b.job_id=c.job_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0  and a.id in($sys_id)";
		$dataArr=sql_select($sql);

	 

		ob_start();
	?>
	

 
		<table border="1" rules="all">
			<tr>
				<th>To</th>
				<td>value</td>
			</tr>
			<tr>
				<th>From</th>
				<td>..</td>
			</tr>
			<tr>
				<th>Subject</th>
				<td>...</td>
			</tr>
		</table>
		  <br>
		  <p>
			<?
			if($type==5){
				echo "Dear Sir, <br>
				Your request has been rejected.  <br>
				Check the below comments and take action for approval";
			}
			else{
				echo "Dear Sir, <br>
				Please check below Garments Service Workorder Approval Request for your electronic approval <br>
				Click the below link to approve or reject with comments";
			}

			?>

		  </p>
	
		  <table border="1" rules="all">
		  <?
		//$grandtotal_amount=0;
				  
			foreach ($dataArr as $row) 
			{  

			?>
				<tr>
					<th>Company Name</th>
					<td><? echo $company_arr[$row[csf("COMPANY_ID")]]; ?></td>
				</tr>
				<tr>
					<th>Buyer Name</th>
					<td><? echo $buyer_library_arr[$row[csf("BUYER_ID")]]; ?></td>
				</tr>
				<tr>
					<th>Work order Date</th>
					<td><? echo $row[csf('WO_DATE')];?></td>
				</tr>
				<tr>
					<th>Workorder No.</th>
					<td><? echo $row[csf('SYS_NUMBER')];?></td>
				</tr>
				<tr>
					<th>Reason for Subcontract</th>
					<td></td>
				</tr>
				<tr>
					<th>Subcontract Value</th>
					<td><? echo $row[csf("AMOUNT")]; ?></td>
				</tr>
				<tr>
					<th>Job No.</th>
					<td><? echo $row[csf("JOB_NO_MST")]; ?></td>
				</tr>
				
			<?
			}
	
	  ?>
	</table>

	
<?
 

	$message=ob_get_contents();
	ob_clean();

	echo $message;die;
	
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_name and entry_form=68 and user_id=$approved_user_id and is_deleted=0");

	$mailToArr=array($email);
		
	$elcetronicSql = "SELECT a.BUYER_ID,a.SEQUENCE_NO,a.BYPASS,b.USER_EMAIL  from electronic_approval_setup a join user_passwd b on a.user_id=b.id where b.valid=1 AND a.IS_DELETED=0 and a.entry_form=68 and a.company_id=$company_name and a.SEQUENCE_NO > $user_sequence_no order by a.SEQUENCE_NO"; //and a.page_id=2150 and a.entry_form=46
		// echo $elcetronicSql;die;
	
	$elcetronicSqlRes=sql_select($elcetronicSql);
	foreach($elcetronicSqlRes as $rows){
			if($rows['USER_EMAIL']){$mailToArr[]=$rows['USER_EMAIL'];}
			if($rows['BYPASS']==2){break;}
	}

	$to=implode(',',$mailToArr);
	$subject = "Erosion Approved Notification";				
	$header=mailHeader();
	
	if($to!="")echo sendMailMailer( $to, $subject, $messageTitle.$message, $from_mail);


}




?>
 
