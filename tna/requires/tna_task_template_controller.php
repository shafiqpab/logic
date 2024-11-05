<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($_SESSION['logic_erp']["data_level_secured"]==1)
{
	if($_SESSION['logic_erp']["buyer_id"]!=0) $buyer_cond=" and buy.id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
	if($_SESSION['logic_erp']["company_id"]!=0) $company_cond=" and id in (".$_SESSION['logic_erp']["company_id"].")"; else $company_cond="";
}
else
{
	$buyer_cond="";	$company_cond="";
}

 //$task_short_arr = return_library_array("select task_name,task_short_name from  lib_tna_task where STATUS_ACTIVE=1 and IS_DELETED=0","task_name","task_short_name"); //where sample_type='$key'
//---------------------------------------------------- Start


if($action=="task_name_search")
{
	echo load_html_head_contents("TNA Task Template","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	var is_single=<? echo $is_single; ?>;
	var selected_id = new Array, selected_name = new Array();
		
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_task_list' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
				//selected_job.push( $('#txt_individual_job' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				//selected_job.splice( i,1);
			}
			var id =''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
				//job += selected_job[i] + '*';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			//job = job.substr( 0, job.length - 1 );
			
			$('#task_short_name_id').val( id );
			$('#task_short_name').val( name );
			//$('#txt_selected_job').val( job );
		}

    </script>
    
    <? 
   // $task_id_arr=return_library_array("select tna_task_id,tna_task_id from  tna_task_template_details where task_template_id =$system_id and status_active=1 and is_deleted = 0",'tna_task_id','tna_task_id');
	 
	if ($selected_ids=="") $selected_ids=''; else $selected_ids=" and task_name not in ( $selected_ids ) "; 
	if($is_single==1){$selected_ids=" and task_name <> $task_id"; }
	
	if($is_single==1){$where_con=" and task_name in(".$selected_task_id_str.")";}
	
	$sql="select id, task_name, task_short_name , task_catagory, module_name, link_page,penalty,row_status,task_type from lib_tna_task where is_deleted=0 and row_status=1 and task_type=$task_type $selected_ids $where_con order by task_sequence_no";
	
	 //echo $sql;
	

	?>
    	<div style="width:auto;" align="center" id="tna_task_list">
    	<fieldset>
    	<div style="width:620px;">
        <table align="center" width="620" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
    		<thead>
            	<th width="50">SL</th>
         		 
            	<th width="150">Task Name</th>
            	<th width="130">Task Short Name</th>
                <th width="130">Task Type</th>
            	<th width="80">Penalty</th>
            	<th>Status
                <input type="hidden" name="task_short_name_id" id="task_short_name_id">
                <input type="hidden" name="task_short_name" id="task_short_name">
                </th>
        	</thead>
        </table> 
        </div>
        
   		<div style="overflow-y:scroll; max-height:200px; width:620px;"  align="center">
     	<table align="center" id="tbl_task_list" width="600" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">  
    	<?
		$i=0;
		$data_array=sql_select($sql);
		foreach($data_array as $data)
		{
			$i++;
		?>
        	 
         		<tr style="cursor:pointer" onClick="js_set_value(<? echo $i; ?>);<? if ( $is_single==1 ) { echo "parent.emailwindow.hide();"; } ?>" id="search<? echo $i;?>">
            		<td width="50"><? echo $i;//$data[csf('id')]; ?>
                     <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<?php echo $data[csf('task_short_name')]; ?>"/>
                 	 <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<?php echo $data[csf('task_name')]; ?>"/>	
					
                    </td>
         			
            		<td width="150"><?  echo $tna_task_name[$data[csf('task_name')]] ?></td>
            		<td width="130"><? echo $data[csf('task_short_name')]; ?></td>
                    <td width="130"><? echo $template_type_arr[$data[csf('task_type')]]; ?></td>
            		<td width="80" align="right"><? echo number_format($data[csf('penalty')],2); ?></td>
            		<td ><? echo $row_status[$data[csf('row_status')]]; ?></td>
         		</tr>
    	<?
		}
		?>
    
   		</table>
   		</div>
        
        <? if ($is_single!=1) { ?>
		 			<div style="width:100%">
							<div style="width:50%; float:left" align="left">
								<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
							</div>
							<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
							</div>
					</div>
                    
                    <? } ?>
    	</fieldset>
    	</div>
      
    	
    	 
        <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
     	<script>
 			var tableFilters = 
				 {
					col_0: "none",			
					//col_1: "select",
 					display_all_text: " -- All --",
 				}							
				setFilterGrid("tbl_task_list",-1,tableFilters);	
         </script>
        
	<?
	
}

if ($action=="show_list_view_template")
{
	 
	$data_array= sql_select("select TASK_TYPE,id,task_template_id,lead_time,material_source,total_task,tna_task_id,deadline,execution_days,notice_before,sequence_no,for_specific,dependant_task,status_active from  tna_task_template_details where task_template_id in (".$data.") and status_active > 0 and is_deleted=0 order by sequence_no"); 

 
	$task_short_arr = return_library_array("select task_name,task_short_name from  lib_tna_task where STATUS_ACTIVE=1 and IS_DELETED=0 and task_type={$data_array[0]['TASK_TYPE']}","task_name","task_short_name"); 

	//,group_concat(tna_task_id) as task_group 
	 
	?>
        <table id="tbl_task_template" class="rpt_table" rules="all" border="1">
            <thead>
            <tr>
                <th width="200" style="color:#0000FF;">Task Short Name </th>
                <th width="100" style="color:#0000FF;">Deadline</th>
                <th width="100" style="color:#0000FF;">Execution Days</th>
                <th width="100" style="color:#0000FF;">Notice Before </th>	
                <th width="10" style="display:none"></th>				     
                <th width="100">Sequence No</th>
                <th width="100">Dependant Task</th>
                
                <th>Status
                <input type="hidden" name="selected_task_id" id="selected_task_id" value="">
                </th>
                <th></th>
            </tr>
            </thead>
            <tbody>
		<?
		 $i=0;
		 if (count($data_array)>0)
		 {
			foreach ( $data_array as $row )
			{
				$i++;
		?>
				<tr>
                    <td>
                        <input type="text" name="txttaskshortname_<? echo $i; ?>" id="txttaskshortname_<? echo $i; ?>" value="<? echo $task_short_arr[$row[csf("tna_task_id")]]; ?>" style="width:190px" class="text_boxes" placeholder="Double Click To Search" onDblClick="openmypage_task($(this).attr('id'),'Task Name Search')" readonly />
                        <input type="hidden" id="hiddentaskid_<? echo $i; ?>" name="hiddentaskid_<? echo $i; ?>" value="<? echo $row[csf("tna_task_id")]; ?>" />
                    </td>
                    <td>
                        <input name="txtdeadline_<? echo $i; ?>" onFocus="set_all_onclick();" value="<? echo $row[csf("deadline")]; ?>" type="number" id="txtdeadline_<? echo $i; ?>" style="width:90px" class="text_boxes_numeric"/>
                    </td>
                    <td>
                        <input name="txtexecutiondays_<? echo $i; ?>"  onFocus="set_all_onclick();" value="<? echo $row[csf("execution_days")]; ?>" type="text" id="txtexecutiondays_<? echo $i; ?>" style="width:90px" class="text_boxes_numeric"/>
                    </td>
                    <td>
                        <input name="txtnoticebefore_<? echo $i; ?>" onFocus="set_all_onclick();" value="<? echo $row[csf("notice_before")]; ?>" type="text" id="txtnoticebefore_<? echo $i; ?>" style="width:90px" class="text_boxes_numeric"/>
                            
                    </td>
                     <td width="10" id="seq_td_<? echo $i; ?>" style="display:none"><? echo $row[csf("sequence_no")]; ?></td>
                    <td>
                         <input name="txtsequenceno_<? echo $i; ?>"  onBlur="set_seq(<? echo $i; ?>, this.value )"  value="<? echo $row[csf("sequence_no")]; ?>" type="text" id="txtsequenceno_<? echo $i; ?>" style="width:90px" class="text_boxes_numeric"/>
                    </td>
                     <td>
                         <input name="txtdependanttask_<? echo $i; ?>"  onBlur="set_seq(<? echo $i; ?>, this.value)"  value="<? echo $task_short_arr[$row[csf("dependant_task")]]; ?>" type="text" id="txtdependanttask_<? echo $i; ?>" style="width:90px" class="text_boxes" onDblClick="openmypage_task($(this).attr('id'),'Task Name Search',1)"/>
                         <input type="hidden" id="hiddendependtaskid_<? echo $i; ?>" name="hiddendependtaskid_<? echo $i; ?>" value="<? echo $row[csf("dependant_task")]; ?>" />
                    </td>
                     
                    <td>
                        <? 
                            echo create_drop_down( "cbostatus_".$i, 80, $row_status,"", '', "", $row[csf("status_active")], "" );
                        ?>
                    <input type="hidden" id="updateid_<? echo $i; ?>" value="<? echo $row[csf("id")]; ?>">
                    </td>
                    <td width="65">
                    <? if (count($data_array)==$i) $symb="+"; else $symb="-";?>
                        <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="<? echo $symb; ?>" onClick="add_new_tr(<? echo $i; ?>)" />&nbsp;<input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deleteRow(<? echo $i; ?>);" />
                   </td>
                </tr>
				<?
			}
		 }
		 else
		 {
			  
		?>
        <tr>
            <td>
                <input type="text" name="txttaskshortname_1" id="txttaskshortname_1" style="width:90px" class="text_boxes" placeholder="Double Click To Search" onDblClick="openmypage_task($(this).attr('id'),'Task Name Search')" readonly />
                <input type="hidden" id="hiddentaskid_1" name="hiddentaskid_1" value="" />
            </td>
            <td>
                <input name="txtdeadline_1" onFocus="set_all_onclick();" type="text" id="txtdeadline_1" style="width:90px" class="text_boxes_numeric"/>
            </td>
            <td>
                <input name="txtexecutiondays_1"  onFocus="set_all_onclick();" type="text" id="txtexecutiondays_1" style="width:90px" class="text_boxes_numeric"/>
            </td>
            <td>
                <input name="txtnoticebefore_1" onFocus="set_all_onclick();" type="text" id="txtnoticebefore_1" style="width:90px" class="text_boxes_numeric"/>
                    
            </td>
            <td width="10" id="seq_td_1" style="display:none"></td>	
            <td>
                 <input name="txtsequenceno_1" onBlur="set_seq( 1, this.value )" type="text" id="txtsequenceno_1" style="width:90px" class="text_boxes_numeric"/>
            </td>
             <td>
                         <input name="txtdependanttask_1"  onBlur="set_seq(1, this.value )"  type="text" id="txtdependanttask_1" style="width:90px" class="text_boxes_numeric"/>
                         <input type="hidden" id="hiddendependtaskid_1" name="hiddendependtaskid_1" value="" />
                    </td>
            <td>
                <?
                    echo create_drop_down( "cbostatus_1", 80, $row_status,"", '', "", $selected, "" );
                ?>
            <input type="hidden" id="updateid_1">
            </td>
            <td width="65">
<input type="button" id="increase_1" style="width:30px" class="formbutton" value="+" onClick="add_new_tr(1)" />&nbsp;<input type="button" id="decrease_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deleteRow(1);" />
</td>
        </tr>
        <? } ?>
        </tbody>
    </table>
    <? 
}
 
/*if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
} 
*/

if ($action=="load_drop_down_buyer")
{
	if($data!=0){$comCon=" and b.TAG_COMPANY=$data";}
	
	if($db_type==0)
	{
		echo create_drop_down( "cbo_buyer_specific", 150, "select buy.id, buy.buyer_name from lib_buyer buy  where status_active =1 and is_deleted=0 $buyer_cond  and (FIND_IN_SET(1, party_type) or FIND_IN_SET(3, party_type) or FIND_IN_SET(21, party_type) or FIND_IN_SET(90, party_type)) $comCon order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	}
	else
	{
		
		echo create_drop_down( "cbo_buyer_specific", 150, "select distinct(buy.id), buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $comCon order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );
	}

} 




if ($action=="save_update_delete")
{
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if (is_duplicate_field( "id", "tna_task_template_details", "company_id=$cbo_company_id and for_specific=$cbo_buyer_specific and lead_time=$txt_lead_time and task_type=$cbo_task_type and IS_DELETED=0 and STATUS_ACTIVE=1 and task_template_id>0" ) == true)
		{
			echo "11**".$temp_id."**".$rID;
			die;
		}
		$id=return_next_id( "id", "tna_task_template_details", 1 ) ;
		$temp_id=return_next_id( "task_template_id", "tna_task_template_details", 1 ) ;
		$field_array="id,company_id,task_template_id,lead_time,material_source,task_type,total_task,dependant_task,tna_task_id,deadline,execution_days,notice_before,sequence_no,for_specific,is_deleted,status_active,inserted_by,insert_date";
		
		$k=0;
		 for ($i=1;$i<=str_replace("'","",$txt_total_task); $i++)
		 {
			 $cbotaskshortname="hiddentaskid_".$i; // hiddentaskid_ is the tna task id
			 $txtdeadline="txtdeadline_".$i;
			 $txtexecutiondays="txtexecutiondays_".$i;
			 $txtnoticebefore="txtnoticebefore_".$i;
			 $txtsequenceno="txtsequenceno_".$i;
			 $hiddendependtaskid="hiddendependtaskid_".$i;
			 
			 $cbostatus="cbostatus_".$i;
			 $updateid="updateid_".$i;
			 //echo "0**".$$cbotaskshortname."=";
			  if (str_replace("'","",$$cbotaskshortname)!="" && str_replace("'","",$$txtdeadline)!="" && str_replace("'","",$$txtexecutiondays)!="")
			  {
				  $k++;
				if ($k!=1) $data_array .=",";
				
				$data_array .="(".$id.",".$cbo_company_id.",".$temp_id.",".$txt_lead_time.",".$cbo_material_source.",".$cbo_task_type.",".$txt_total_task.",'".str_replace("'","",$$hiddendependtaskid)."',".$$cbotaskshortname.",".$$txtdeadline.",".$$txtexecutiondays.",".$$txtnoticebefore.",".$$txtsequenceno.",".$cbo_buyer_specific.",0,".$$cbostatus.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				 
				$id=$id+1;
			  }
		 }
		// echo $data_array;die;
		 /* echo "10**"."**insert into tna_task_template_details (".$field_array.") values ".$data_array; oci_rollback($con);die; */
		 $rID=sql_insert("tna_task_template_details",$field_array,$data_array,1);
 		// echo "0**"."**".$rID;die;
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$temp_id."**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$temp_id."**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$temp_id."**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$temp_id."**".$rID;
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
	 	/*if (is_duplicate_field( "id", "tna_task_template_details", "for_specific=$cbo_buyer_specific and lead_time=$txt_lead_time and id!=" ) == true)
		{
			echo "11**".$temp_id."**".$rID;
			die;
		}
		*/
		$field_array_save="id,company_id,task_template_id,lead_time,material_source,task_type,total_task,dependant_task,tna_task_id,deadline,execution_days,notice_before,sequence_no,for_specific,is_deleted,status_active,inserted_by,insert_date";
		$field_array_upd="company_id*lead_time*material_source*task_type*total_task*dependant_task*tna_task_id*deadline*execution_days*notice_before*sequence_no*for_specific*is_deleted*status_active*updated_by*update_date";
		$k=0;
		$data_array_save="";
		$id=return_next_id( "id", " tna_task_template_details", 1 ) ;
		for ($i=1;$i<=str_replace("'","",$txt_total_task);$i++)
		 {
			 $cbotaskshortname="hiddentaskid_".$i; // hiddentaskid_ is the tna task id
			 $txtdeadline="txtdeadline_".$i;
			 $txtexecutiondays="txtexecutiondays_".$i;
			 $txtnoticebefore="txtnoticebefore_".$i;
			 $txtsequenceno="txtsequenceno_".$i;
			 $hiddendependtaskid="hiddendependtaskid_".$i;
			
			 $cbostatus="cbostatus_".$i;
			 $updateid="updateid_".$i;
			 if (str_replace("'","",$$cbotaskshortname)!="" && str_replace("'","",$$cbotaskshortname)!=0 && str_replace("'","",$$txtdeadline)!="" && str_replace("'","",$$txtexecutiondays)!="")
			 {
				 if (str_replace("'","",$$updateid)=="")
				 {	
					 $k++;
					 if ($k!=1) $data_array_save .=",";
					
					 $not_delete_id[$id]=$id;
					 $data_array_save .="(".$id.",".$cbo_company_id.",".$txt_system_id.",".$txt_lead_time.",".$cbo_material_source.",".$cbo_task_type.",".$txt_total_task.",'".str_replace("'","",$$hiddendependtaskid)."',".$$cbotaskshortname.",".$$txtdeadline.",".$$txtexecutiondays.",".$$txtnoticebefore.",".$$txtsequenceno.",".$cbo_buyer_specific.",0,".$$cbostatus.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					
					$id=$id+1;
				 }
				 else
				 {
					 $id_arr[]=str_replace("'",'',$$updateid);
					 $not_delete_id[str_replace("'",'',$$updateid)]=str_replace("'",'',$$updateid);
					 
					 $data_array_upd[str_replace("'",'',$$updateid)] =explode("*",("".$cbo_company_id."*".$txt_lead_time."*".$cbo_material_source."*".$cbo_task_type."*".$txt_total_task."*'".str_replace("'","",$$hiddendependtaskid)."'*".$$cbotaskshortname."*".$$txtdeadline."*".$$txtexecutiondays."*".$$txtnoticebefore."*".$$txtsequenceno."*".$cbo_buyer_specific."*0*".$$cbostatus."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					 
					 
				 }
			 }
		 }
		 
		
		   //echo "10**";print_r($not_delete_id);die;
		 
		 
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$not_delete_id_str = implode(',',$not_delete_id);
		
		$rID_del=sql_delete(" tna_task_template_details",$field_array,$data_array,"id not in(".str_replace("'","",$not_delete_id_str).") and task_template_id","".str_replace("'","",$txt_system_id)."",1);
		
		
		 if (count($id_arr)>0) $rID_up=execute_query(bulk_update_sql_statement( "tna_task_template_details", "id", $field_array_upd, $data_array_upd, $id_arr ));
		 
		 if ($data_array_save!="") $rID=sql_insert("tna_task_template_details",$field_array_save,$data_array_save,1);
		 
		
		
		 $txt_system_id =str_replace("'",'',$txt_system_id);
		if($db_type==0)
		{
			if($rID_up || $rID ){
				mysql_query("COMMIT");  
				//echo "1**"."**".$rID;
				echo "1**".$txt_system_id."**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				//echo "10**"."**".$rID;
				echo "10**".$txt_system_id."**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID_up || $rID )
			{
				oci_commit($con); 
				//echo "1**"."**".$rID;
				echo "1**".$txt_system_id."**".$rID;
			}
			else
			{
				oci_rollback($con);
				//echo "10**"."**".$rID;
				echo "10**".$txt_system_id."**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'2'*'1'";
		$rID=sql_delete(" tna_task_template_details",$field_array,$data_array,"task_template_id","".str_replace("'","",$txt_system_id)."",1);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "2**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con); 
				echo "2**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
		 
		
	}
	else if ($operation==5)   // Copy Here
	{
		$con = connect();
		
 

		if (is_duplicate_field( "id", "tna_task_template_details", "company_id=$cbo_company_id and for_specific=$cbo_buyer_specific and lead_time=$txt_lead_time and task_type=$cbo_task_type and IS_DELETED=0 and STATUS_ACTIVE=1 and task_template_id>0" ) == true)
		{
			echo "11**".$temp_id."**".$rID;
			die;
		}
		$id=return_next_id( "id", "tna_task_template_details", 1 ) ;
		$temp_id=return_next_id( "task_template_id", "tna_task_template_details", 1 ) ;
		
		$sql_insert="insert into tna_task_template_details(id,company_id,task_template_id,lead_time,material_source,task_type,total_task,dependant_task,tna_task_id,deadline,execution_days,notice_before,sequence_no,for_specific,is_deleted,status_active,inserted_by,insert_date) 
				select	
				'', company_id,$temp_id,$txt_lead_time,material_source,task_type,$txt_total_task,dependant_task,tna_task_id,deadline,execution_days,notice_before,sequence_no,$cbo_buyer_specific,is_deleted,status_active,inserted_by,insert_date from tna_task_template_details where TASK_TEMPLATE_ID in ($txt_system_id)";		
		
		
		
		$rID=execute_query($sql_insert,1);

		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con); 
				echo "0**".$temp_id."**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$temp_id."**".$rID;
			}
		}
		
		
		
		disconnect($con);
		die;
		 
		
	}
	
	
	
	
	
}



if($action=="task_template")
{
	echo load_html_head_contents("Task Template", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
	function js_set_value( job_no )
	{
		document.getElementById('txt_selected_id').value=job_no;
		parent.emailwindow.hide();
	}
	
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Type</th>
                    <th>Material Source</th>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter System ID</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                </thead>
                <tbody>
                	<tr>
                        <td>
                        <?
							echo create_drop_down( "cbo_task_type", 90, $template_type_arr,"", 1, "-- Select --",$cbo_task_type, "",'' );		
						?>
                        </td>  
                          <td width="90">
                          <? 
                            echo create_drop_down( "cbo_material_source", 100, $material_source,"", 1, "-- Select --", $selected, "",'' );		
                            ?>	
                          </td>
                        
                        <td align="center">
                        	 <? 
								//echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--","","",0 );
								if($company_id!=0){$comCon=" and b.TAG_COMPANY=$company_id";}
								
								if($db_type==0)
								{
									echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy  where status_active =1 and is_deleted=0 $buyer_cond  and (FIND_IN_SET(1, party_type) or FIND_IN_SET(3, party_type) or FIND_IN_SET(21, party_type) or FIND_IN_SET(90, party_type)) $comCon order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
								}
								else
								{
									echo create_drop_down( "cbo_buyer_name", 150, "select distinct(buy.id), buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $comCon order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $cbo_buyer_specific, "" );
								}
							
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"System ID",2=>"Lead Time");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_task_type').value+'**'+document.getElementById('cbo_material_source').value+'**'+'<?=$company_id;?>', 'task_template_list_view', 'search_div', 'tna_task_template_controller', 'setFilterGrid(\'tbl_sid_list_view\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}


if ($action=="task_template_list_view")
{
  	//echo load_html_head_contents("Task Template","../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($buyer_id,$search_type,$search_value,$type,$material_source,$company_id)=explode('**',$data);
	
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
	
	if($company_id>0){
		$search_con.=" and COMPANY_ID = $company_id";	
	}
	
	

?>

</head>

<body>
    <div align="center" style="width:100%;" >
    	<input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
     <? 
	 		if($db_type==0)
			{
				$sql= "select task_template_id,lead_time,material_source,total_task,for_specific,task_type from tna_task_template_details where status_active=1 and is_deleted=0 $search_con  group by task_template_id  order by task_template_id desc";
			}
			else
			{
				$sql= "select task_template_id,lead_time,material_source,total_task,for_specific,task_type from tna_task_template_details where status_active=1 and is_deleted=0 $search_con  group by task_template_id,lead_time,material_source,total_task,for_specific,task_type order by task_template_id desc";
			}
			  //echo $sql;
			$task_buyer = return_library_array("select id,buyer_name from  lib_buyer ","id","buyer_name");
			$arr=array(2=>$material_source,4=>$task_buyer,5=>$template_type_arr);
			echo  create_list_view("tbl_sid_list_view", "System ID,Lead Time,Material Source,Total Task,Buyer,Type", "90,50,60,120,220,100","700","190",0, $sql , "js_set_value", "task_template_id", "", 1, "0,0,material_source,0,for_specific,task_type", $arr , "task_template_id,lead_time,material_source,total_task,for_specific,task_type", "",'','1,1,0,1,0') ;
        
     ?>
    </div>
    
</body>           
 
</html>
<?
}
 
if ($action=="populate_data_from_search_popup")
{
	$data_array=sql_select("select company_id,task_template_id,lead_time,material_source,total_task,for_specific,task_type from  tna_task_template_details where task_template_id='$data' and is_deleted=0");
	foreach ($data_array as $row)
	{
		echo "load_drop_down( 'requires/tna_task_template_controller', '".$row[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' );";
		
		echo "document.getElementById('txt_system_id').value = '".$row[csf("task_template_id")]."';\n";  
		echo "document.getElementById('cbo_company_id').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('txt_lead_time').value = '".$row[csf("lead_time")]."';\n";  
		echo "document.getElementById('cbo_material_source').value = '".$row[csf("material_source")]."';\n";  
		echo "document.getElementById('txt_total_task').value = '".$row[csf("total_task")]."';\n";  
		echo "document.getElementById('cbo_buyer_specific').value = '".$row[csf("for_specific")]."';\n";  
		echo "document.getElementById('cbo_task_type').value = '".$row[csf("task_type")]."';\n";
		  
		die; 
	}
}
 
?>


 