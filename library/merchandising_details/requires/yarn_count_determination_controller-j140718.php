<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$permission=$_SESSION['page_permission'];

if ($action=="search_list_view")
{
	$composition_arr=array();
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	//$arr=array (0=>$item_category, 3=>$color_range,6=>$composition,8=>$lib_yarn_count,9=>$yarn_type);
	$sql_q="select mst_id, copmposition_id, percent, count_id, type_id from lib_yarn_count_determina_dtls where is_deleted=0 order by id";
						
	$data_array=sql_select($sql_q);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('mst_id')],$composition_arr))
			{
				$composition_arr[$row[csf('mst_id')]]=$composition_arr[$row[csf('mst_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('mst_id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
		}
	}
	unset($data_array);
	//print_r($composition_arr);
	
	$sql="select id, fab_nature_id, construction, gsm_weight, status_active, color_range_id, stich_length, process_loss, sequence_no from  lib_yarn_count_determina_mst where is_deleted=0 order by id";

	$arr=array (0=>$item_category, 3=>$color_range,6=>$composition_arr,8=>$row_status);

	echo  create_list_view ( "list_view", "Fab Nature,Construction,GSM/Weight,Color Range,Stich Length,Process Loss,Composition,Sequence No,Status", "100,100,100,100,90,50,300,50,50","1000","350",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'",1, "fab_nature_id,0,0,color_range_id,0,0,id,0,status_active", $arr , "fab_nature_id,construction,gsm_weight,color_range_id,stich_length,process_loss,id,sequence_no,status_active", "requires/yarn_count_determination_controller",'setFilterGrid("list_view",-1);','0,0,1,0,1,1,0,0,0') ;
	exit();
}
    
if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select("select fab_nature_id,construction,gsm_weight,color_range_id,status_active,stich_length,process_loss,sequence_no,id from lib_yarn_count_determina_mst where id='$data'");
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('cbo_fabric_nature').value  = '".($inf[csf("fab_nature_id")])."';\n";
		echo "document.getElementById('txtconstruction').value = '".($inf[csf("construction")])."';\n";    
		echo "document.getElementById('txtgsmweight').value = '".($inf[csf("gsm_weight")])."';\n";
		echo "document.getElementById('cbocolortype').value = '".($inf[csf("color_range_id")])."';\n";
		echo "document.getElementById('cbo_status').value = '".($inf[csf("status_active")])."';\n";    
		echo "document.getElementById('stichlength').value  = '".($inf[csf("stich_length")])."';\n";
		echo "document.getElementById('processloss').value  = '".($inf[csf("process_loss")])."';\n";
		echo "document.getElementById('txt_sequence').value  = '".($inf[csf("sequence_no")])."';\n";
		echo "document.getElementById('update_mst_id').value  = '".($inf[csf("id")])."';\n";
	    echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_count_determination',1);\n";  
	    echo "show_detail_form('".$inf[csf("id")]."');\n"; 
	}
	exit();
}

if($action =="show_detail_form")
{
	?>
	<table width="100%" border="0" id="tbl_yarn_count" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" >
        <thead>
            <tr><th width="150">Composition</th> <th width="50">%</th> <th width="150">Count</th><th width="150">Type</th> <th width="">  </th> 
            </tr>
        </thead>
        <tbody>
			<?
				$data_array=sql_select("select id, copmposition_id, percent,count_id,type_id from  lib_yarn_count_determina_dtls where mst_id='$data' order by id asc");
				if ( count($data_array)>0)
				{
					$i=0;
					foreach( $data_array as $row )
					{
						$i++;
						?>
						<tr id="yarncost_1" align="center">
                            <td width="150"><? //echo create_drop_down( "cbocompone_".$i, 150, $composition,"", 1, "-- Select --", $row[csf("copmposition_id")], "check_duplicate(".$i.",this.id )",'','' ); ?>
                            	<input type="text" id="txtcompone_<? echo $i; ?>"  name="txtcompone_<? echo $i; ?>"  class="text_boxes" style="width:140px" readonly placeholder="Browse" onDblClick="openmypage_comp(<? echo $i; ?>);" value="<? echo $composition[$row[csf("copmposition_id")]]; ?>" />
                                <input type="hidden" id="cbocompone_<? echo $i; ?>"  name="cbocompone_<? echo $i; ?>" class="text_boxes" style="width:50px" value="<? echo $row[csf("copmposition_id")]; ?>" />
                            
                            </td>
                            <td width="50"><input type="text" id="percentone_<? echo $i; ?>"  name="percentone_<? echo $i; ?>" class="text_boxes" style="width:50px" onChange="sum_percent()"  value="<? echo  $row[csf("percent")]; ?>" /></td>
                            <td width="70"><? echo create_drop_down( "cbocountcotton_".$i, 150, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1," -- Select Count --",  $row[csf("count_id")], "check_duplicate(".$i.",this.id )",'','' ); 
                            ?>
                            </td>
                            <td width="100"><? echo create_drop_down( "cbotypecotton_".$i, 150, $yarn_type,"", 1, "-- Select --", $row[csf("type_id")], "check_duplicate(".$i.",this.id )",'','' ); ?>
                            </td>
                            <td> 
                                <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?>)" />
                                <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
                                <input type="hidden" id="updateid_<? echo $i; ?>" name="updateid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value=" <? echo $row[csf("id")]; ?>"  />  
                            </td>  
						</tr>
						<?
					}
				}
	            ?>
        </tbody>
	</table>
	<?
	exit();
}

if ($action=="open_process_loss_popup_view")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>
	var permission='<? echo $permission; ?>';
	function fnc_process_loss_entry(operation)
		{
			var tot_row=$('#tbl_process_loss_details tr').length-1;
			var mst_id=document.getElementById('mst_id').value;
			var tot_process_loss_hidden=document.getElementById('tot_process_loss_hidden').value;
			var data_all='';
			
			for(i=1; i<=tot_row; i++)
			{
			    data_all+=get_submitted_data_string('processid_'+i+'*processloss_'+i+'*effectivedate_'+i,"../../../",i);
				
			}
			if(data_all=='')
			{
				alert("No Data Select");	
				return;
			}
			var data="action=save_update_delete_process_loss&operation="+operation+data_all+'&total_row='+tot_row+'&mst_id='+mst_id+'&tot_process_loss='+tot_process_loss_hidden;
			freeze_window(operation);
			http.open("POST","yarn_count_determination_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fnc_process_loss_entry_response;
		}
		
		function fnc_process_loss_entry_response()
		{
			if(http.readyState == 4) 
			{
				var reponse=trim(http.responseText).split('**');	
				show_msg(reponse[0]);
				$("#mst_id").val(reponse[1]);
				set_button_status(1, permission, 'fnc_process_loss_entry',1);
				release_freezing();	
				fn_close();
			}
		}
		
		
		function fn_sum()
		{
		var tot_processloss=0;
		var tot_row=$('#tbl_process_loss_details tr').length-1;
			for(i=1; i<=tot_row; i++){
			var processloss=$("#processloss_"+i).val()*1;
			tot_processloss+=processloss;
			}
			$("#tot_process_loss").html(tot_processloss);
			$("#tot_process_loss_hidden").val(tot_processloss);
		}
		
		function fn_close(str)
		{
			parent.emailwindow.hide(); 
		}
		
	</script>
    </head>

    <body>
    
 <?
 $sql_up_data=sql_select("select id, mst_id, process_id, process_loss, effective_date from  conversion_process_loss where mst_id='$mst_id'");
 
 //var_dump($sql_up_data);
 ?>   
    
     <form name="rate_1" id="rate_1">
      <? echo load_freeze_divs ("../../../",$permission);  ?>
    <table width="455" cellspacing="0" class="rpt_table" border="0" rules="all">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="200">Process</th>
                <th width="80">Process Loss</th>
                <th>Effective Date</th>
            </tr>
        </thead>
    </table>
    
    <table width="455" cellspacing="0" class="rpt_table" border="0" id="tbl_process_loss_details" rules="all">
    <?
	$i=1;
	
if(count($sql_up_data)==0)
{
	foreach($conversion_cost_head_array as $process_id=>$process_val)
	{
	?>
    <tr>
        <td width="30" align="center"><? echo $i;?></td>
        <td width="200">
			<? echo $process_val;?>
            <input type="hidden" id="processid_<? echo $i; ?>" name="processid_<? echo $i; ?>" value="<? echo $process_id;?>"/>
        </td>
        <td width="80" align="center">
            <input type="text" id="processloss_<? echo $i; ?>" onKeyUp="fn_sum()" name="processloss_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" />
        </td>
        <td align="center">
            <input type="text" id="effectivedate_<? echo $i; ?>" name="effectivedate_<? echo $i; ?>" class="datepicker" />
        </td>
    </tr>
    <?
	$i++;
    }
}
else
{
	foreach($sql_up_data as $rows)
	{
	?>
    <tr>
        <td width="30" align="center"><? echo $i;?></td>
        <td width="200">
			<? echo $conversion_cost_head_array[$rows[csf("process_id")]];?>
            <input type="hidden" id="processid_<? echo $i; ?>" name="processid_<? echo $i; ?>" value="<? echo $rows[csf("process_id")];?>"/>
        </td>
        <td width="80" align="center">
            <input type="text" id="processloss_<? echo $i; ?>" name="processloss_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" onKeyUp="fn_sum()" value="<? echo $rows[csf("process_loss")];?>" />
        </td>
        <td align="center">
            <input type="text" id="effectivedate_<? echo $i; ?>" name="effectivedate_<? echo $i; ?>" class="datepicker" value="<? echo change_date_format($rows[csf("effective_date")]);?>" />
        </td>
    </tr>
    <?
	$i++;
    }
	
}
	?>
    </table>
    <table width="455" cellspacing="0" class="rpt_table" border="0"rules="all">
    <thead>
        <td colspan="3">Total <span style="float:right;" id="tot_process_loss">&nbsp;</span></td>
        
        <td width="140">&nbsp;</td>
    </thead>
    <tr>
    <td colspan="4" align="center">
    <input type="hidden" id="mst_id" name="mst_id" value="<? echo $mst_id; ?>" />
    <input type="hidden" id="tot_process_loss_hidden" name="tot_process_loss_hidden" />
	<?
	if(count($sql_up_data)==0)
	{
		echo load_submit_buttons($permission, "fnc_process_loss_entry", 0,0,"reset_form('rate_1','','','','','');",1);
	}
	else
	{
		echo load_submit_buttons($permission, "fnc_process_loss_entry", 1,0,"reset_form('rate_1','','','','','');",1);
	}
	?>
    </td>
    </tr>
    </table>
    </form>
    </body>  
<script>
var tableFilters = 	{					
					col_0: "none",
					col_2: "none",
					col_3: "none",
				};
setFilterGrid("tbl_process_loss_details",tableFilters,-1)
</script>         
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
    
    
    <?
	exit();

}

if ($action=="save_update_delete_process_loss")
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

		$id=return_next_id( "id", "conversion_process_loss", 1 ) ;
		$field_array= "id,mst_id,process_id,process_loss, effective_date,insert_by,insert_date,status_active,is_deleted";
		
		for ($i=1;$i<=$total_row;$i++)
		{
			$processid="processid_".$i;
			$processloss="processloss_".$i;
			$effectivedate="effectivedate_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$mst_id.",".$$processid.",".$$processloss.",".$$effectivedate.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
			$id++;
		}
		
		$field_array_mst= "process_loss*updated_by*update_date*status_active*is_deleted";
		$data_array_mst ="".$tot_process_loss."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'1'*'0'";
		$rID_mst=sql_update("lib_yarn_count_determina_mst",$field_array_mst,$data_array_mst,"id","".$mst_id."",0);
		$rID=sql_insert("conversion_process_loss",$field_array,$data_array,1);
		if($db_type==0)
		{
			if($rID && $rID_mst){
				mysql_query("COMMIT");  
				echo "0**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_mst)
			{
				oci_commit($con);  
				echo "0**".$rID;
			}
		else{
				oci_rollback($con); 
				echo "10**".$rID;
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
		//echo "DELETE  from conversion_process_loss WHERE mst_id=".$mst_id."";die;
		$rID=execute_query("DELETE  from conversion_process_loss WHERE mst_id=".$mst_id."");

		$id=return_next_id( "id", "conversion_process_loss", 1 ) ;
		$field_array= "id,mst_id,process_id,process_loss, effective_date,insert_by,insert_date,status_active,is_deleted";
		
		for ($i=1;$i<=$total_row;$i++)
		{
			$processid="processid_".$i;
			$processloss="processloss_".$i;
			$effectivedate="effectivedate_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$mst_id.",".$$processid.",".$$processloss.",".$$effectivedate.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
			$id++;
		}
		
		$field_array_mst= "process_loss*updated_by*update_date*status_active*is_deleted";
		$data_array_mst ="".$tot_process_loss."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'1'*'0'";
		$rID_mst=sql_update("lib_yarn_count_determina_mst",$field_array_mst,$data_array_mst,"id","".$mst_id."",0);

		$rID=sql_insert("conversion_process_loss",$field_array,$data_array,1);
		if($db_type==0)
		{
			if($rID && $rID_mst){
				mysql_query("COMMIT");  
				echo "0**".$mst_id;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$mst_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_mst)
			{
				oci_commit($con);  
				echo "0**".$mst_id;
			}
		else{
				oci_rollback($con); 
				echo "10**".$mst_id;
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="openpage_mapping_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>
		var permission='<? echo $permission; ?>';
		
		function fnc_mapping_entry(operation)
		{
			if(form_validation('txt_machine_dia','Machine Dia')==false )
			{
				return;
			}
			
			var data="action=save_update_delete_mapping&operation="+operation+get_submitted_data_string('txt_machine_dia*txt_machine_gg*txt_stitch_length*txt_fabric_dia*update_dtls_id',"../../../")+'&mst_id='+<? echo $mst_id; ?>;
			
			freeze_window(operation);
			
			http.open("POST","yarn_count_determination_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fnc_mapping_entry_Reply_info;
		}
	
		function fnc_mapping_entry_Reply_info()
		{
			if(http.readyState == 4) 
			{
				//release_freezing();return;//alert(http.responseText);
				var response=trim(http.responseText).split('**');	
					
				show_msg(response[0]);
				
				if(response[0]==0 || response[0]==1)
				{
					$('#update_dtls_id').val(response[1]);
					set_button_status(1, permission, 'fnc_mapping_entry',1);	
				}
				else if(response[0]==2)
				{
					reset_form('mapping_1','','','','','');
				}
				release_freezing();	
			}
		}
		
	</script>
</head>

<body>
<div align="center">
	<? 
		echo load_freeze_divs ("../../../",$permission); 
		
		$dataArray=sql_select("select id, machine_dia, machine_gg, fabric_dia, stitch_length from fabric_mapping where mst_id=$mst_id and status_active=1 and is_deleted=0");
	?>
	<form name="mapping_1" id="mapping_1">
    	<fieldset style="width:450px;">
            <legend>New Entry</legend>
            <table width="450" align="center" border="0">
                <tr>
                    <td class="must_entry_caption">Machine Dia</td>
                    <td>
                        <input type="text" name="txt_machine_dia" id="txt_machine_dia" class="text_boxes_numeric" style="width:100px;" value="<? echo $dataArray[0][csf('machine_dia')]; ?>"/>
                    </td>
                    <td>Machine GG</td>
                    <td>
                        <input type="text" name="txt_machine_gg" id="txt_machine_gg" class="text_boxes" style="width:100px;" value="<? echo $dataArray[0][csf('machine_gg')]; ?>"/>
                    </td>
                </tr>
                <tr>
                	<td>Finish Fabric Dia</td>
                    <td>
                        <input type="text" name="txt_fabric_dia" id="txt_fabric_dia" class="text_boxes" style="width:100px;" value="<? echo $dataArray[0][csf('fabric_dia')]; ?>"/>
                    </td>
                	<td>Stitch Length</td>
                    <td>
                        <input type="text" name="txt_stitch_length" id="txt_stitch_length" class="text_boxes" style="width:100px;" value="<? echo $dataArray[0][csf('stitch_length')]; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" align="center" class="button_container">
						<? 
							if(count($dataArray)>0)
							{
								echo load_submit_buttons($permission, "fnc_mapping_entry", 1,0,"reset_form('','','txt_machine_dia*txt_machine_gg*txt_stitch_length*txt_fabric_dia','','','');",1); 	
							}
							else
							{
								echo load_submit_buttons($permission, "fnc_mapping_entry", 0,0,"reset_form('','','txt_machine_dia*txt_machine_gg*txt_stitch_length*txt_fabric_dia','','','');",1); 
							}
						?>
                        <input type="hidden" name="update_dtls_id" id="update_dtls_id" class="text_boxes" value="<? echo $dataArray[0][csf('id')]; ?>">
                     </td>
                </tr>
             </table>
		</fieldset>
    </form>
</div>
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action=="save_update_delete_mapping")
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
		
		$id=return_next_id( "id", "fabric_mapping", 1 ) ;
		$field_array= "id,mst_id,machine_dia,machine_gg,fabric_dia,stitch_length,inserted_by,insert_date";
		$data_array="(".$id.",".$mst_id.",".$txt_machine_dia.",".$txt_machine_gg.",".$txt_fabric_dia.",".$txt_stitch_length.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//echo "insert into fabric_mapping (".$field_array.") values ".$data_array;die;
		$rID=sql_insert("fabric_mapping",$field_array,$data_array,1);
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "0**".$id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0";
			}
		}
		else
		{
			if($rID)
			{
				oci_commit($con);  
				echo "0**".$id;
			}
			else
			{
				oci_rollback($con); 
				echo "5**0";
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
		
		$field_array="machine_dia*machine_gg*fabric_dia*stitch_length*updated_by*update_date";
		$data_array=$txt_machine_dia."*".$txt_machine_gg."*".$txt_fabric_dia."*".$txt_stitch_length."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$rID=sql_update("fabric_mapping",$field_array,$data_array,"id",$update_dtls_id,0);
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_dtls_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0";
			}
		}
		else
		{
			if($rID)
			{
				oci_commit($con);  
				echo "1**".str_replace("'","",$update_dtls_id);
			}
			else
			{
				oci_rollback($con); 
				echo "6**0";
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
		$data_array=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";

		$rID=sql_update("fabric_mapping",$field_array,$data_array,"id",$update_dtls_id,0);
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$update_dtls_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "7**0";
			}
		}
		else
		{
			if($rID)
			{
				oci_commit($con);  
				echo "2**".str_replace("'","",$update_dtls_id);
			}
			else
			{
				oci_rollback($con); 
				echo "7**0";
			}
		}
		disconnect($con);
		die;
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
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		
		$str_rep=array("/", "&", "*", "(", ")", "=","'",",","\r", "\n",'"','#');
		$construction=str_replace("'",'',$txtconstruction);
		$txtconstruction=str_replace($str_rep,' ',$construction);
		$id=return_next_id( "id", "lib_yarn_count_determina_mst", 1 ) ;
		$field_array1= "id,fab_nature_id, construction,gsm_weight,color_range_id,stich_length,process_loss,sequence_no,inserted_by,insert_date,status_active,is_deleted";
		$data_array1="(".$id.",".$cbo_fabric_nature.",'".$txtconstruction."',".$txtgsmweight.",".$cbocolortype.",".$stichlength.",".$processloss.",".$txt_sequence.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",'0')";
		$id_dtls=return_next_id( "id", "lib_yarn_count_determina_dtls", 1 ) ;
		$field_array2= "id,mst_id, copmposition_id,percent,count_id,type_id,inserted_by,insert_date,status_active,is_deleted";
		for ($i=1;$i<=$total_row;$i++)
		{
			$cbocompone="cbocompone_".$i;
			$percentone="percentone_".$i;
			$cbocountcotton="cbocountcotton_".$i;
			$cbotypecotton="cbotypecotton_".$i;
			$updateid="updateid_".$i;
			if ($i!=1) $data_array2 .=",";
			$data_array2 .="(".$id_dtls.",".$id.",".$$cbocompone.",".$$percentone.",".$$cbocountcotton.",".$$cbotypecotton.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
			$id_dtls=$id_dtls+1;
		}
		
		//echo "INSERT INTO lib_yarn_count_determina_mst(".$field_array1.") VALUES ".$data_array1;die;
		
		
		$rID=sql_insert("lib_yarn_count_determina_mst",$field_array1,$data_array1,0);
		$rID_1=sql_insert("lib_yarn_count_determina_dtls",$field_array2,$data_array2,1);
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $rID_1){
				mysql_query("COMMIT");  
				echo "0**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_1)
			{
				oci_commit($con);  
				echo "0**".$rID;
			}
		else{
				oci_rollback($con); 
				echo "10**".$rID;
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
		
		if(str_replace("'","",$cbo_status)==2)
		{
			$rID=execute_query( "update  lib_yarn_count_determina_dtls set status_active=$cbo_status where  mst_id =$update_mst_id",0);
			$rID_1=execute_query( "update  lib_yarn_count_determina_mst set status_active=$cbo_status where  id =$update_mst_id",0);
			if($db_type==0)
				{
					if($rID && $rID_1){
						mysql_query("COMMIT");  
						echo "1**".$rID;
					}
					else{
						mysql_query("ROLLBACK"); 
						echo "10**".$rID;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID && $rID_1)
					{
						oci_commit($con);  
						echo "1**".$rID;
					}
					else{
						oci_rollback($con); 
						echo "10**".$rID;
					}
				}
			die;
		}
		else
		{
			$pre_sql="select lib_yarn_count_deter_id as detarmination_id from wo_pre_cost_fabric_cost_dtls a where  a.lib_yarn_count_deter_id=$update_mst_id";
			$pre_data_array=sql_select($pre_sql);
			 
			 $sql="select a.detarmination_id as detarmination_id from product_details_master a where   a.detarmination_id=$update_mst_id and a.is_deleted=0 and a.status_active=1"; 
			
			$data_array=sql_select($sql);
			if(count($data_array)>0 || count($pre_data_array)>0)
			{
				$rID=execute_query( "update  lib_yarn_count_determina_dtls set status_active=$cbo_status where  mst_id =$update_mst_id",0);
				$rID_1=execute_query( "update  lib_yarn_count_determina_mst set status_active=$cbo_status where  id =$update_mst_id",0);
				if($db_type==0)
				{
					if($rID && $rID_1){
						mysql_query("COMMIT");  
						echo "1**".$rID;
					}
					else{
						mysql_query("ROLLBACK"); 
						echo "10**".$rID;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID && $rID_1)
					{
						oci_commit($con);  
						echo "1**".$rID;
					}
					else{
						oci_rollback($con); 
						echo "10**".$rID;
					}
				}
				die;
			}
			 
		}
		
		$str_rep=array("/", "&", "*", "(", ")", "=","'",",","\r", "\n",'"','#');
		$construction=str_replace("'",'',$txtconstruction);
		$txtconstruction=str_replace($str_rep,' ',$construction);
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		$field_array1= "fab_nature_id*construction*gsm_weight*color_range_id*stich_length*process_loss*sequence_no*updated_by*update_date*status_active*is_deleted";
		$data_array1="".$cbo_fabric_nature."*'".$txtconstruction."'*".$txtgsmweight."*".$cbocolortype."*".$stichlength."*".$processloss."*".$txt_sequence."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*'0'";
													//.$pc_date_time."'*".$cbo_status."*0";
		
		
		
		$rID_de1=execute_query( "delete from lib_yarn_count_determina_dtls where  mst_id =".$update_mst_id."",0);
		
		$id_dtls=return_next_id( "id", "lib_yarn_count_determina_dtls", 1 ) ;
		
		//echo "shajjad";die;
		
		$field_array2= "id,mst_id, copmposition_id,percent,count_id,type_id,inserted_by,insert_date,status_active,is_deleted";
		for ($i=1;$i<=$total_row;$i++)
		{
			$cbocompone="cbocompone_".$i;
			$percentone="percentone_".$i;
			$cbocountcotton="cbocountcotton_".$i;
			$cbotypecotton="cbotypecotton_".$i;
			$updateid="updateid_".$i;
			if ($i!=1) $data_array2 .=",";
			$data_array2 .="(".$id_dtls.",".$update_mst_id.",".$$cbocompone.",".$$percentone.",".$$cbocountcotton.",".$$cbotypecotton.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
			$id_dtls=$id_dtls+1;
		}
		
		//echo "INSERT INTO lib_yarn_count_determina_mst(".$field_array1.") VALUES ".$data_array1;die;
		
		$rID=sql_update("lib_yarn_count_determina_mst",$field_array1,$data_array1,"id","".$update_mst_id."",0);
		$rID_1=sql_insert("lib_yarn_count_determina_dtls",$field_array2,$data_array2,1);
		
		
		
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $rID_1){
				mysql_query("COMMIT");  
				echo "1**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			
			if($rID && $rID_1)
			{
				oci_commit($con);  
				echo "1**".$rID;
			}
			else{
				oci_rollback($con); 
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
		//}
		
	}
	else if ($operation==2) // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array1="updated_by*update_date*status_active*is_deleted";
		$data_array1="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("lib_yarn_count_determina_mst",$field_array1,$data_array1,"id","".$update_mst_id."",1);
		$field_array2="updated_by*update_date*status_active*is_deleted";
		$data_array2="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID1=sql_delete("lib_yarn_count_determina_dtls",$field_array2,$data_array2,"mst_id","".$update_mst_id."",1);
		
		if($db_type==0)
		{
			if($rID && $rID1 ){
				mysql_query("COMMIT");  
				echo "2**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
		 if($rID && $rID1 )
			{
				oci_commit($con);   
				echo "2**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="composition_popup")
{
	echo load_html_head_contents("Order Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);	
	
	?>
	<script>
		function js_set_value(id,name)
		{
			document.getElementById('hidcompid').value=id;
			document.getElementById('hidcompname').value=name;
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
		<fieldset style="width:430px;margin-left:10px">
	        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="430" class="rpt_table">
	                <thead>
	                    <th width="30">SL</th>
	                    <th>Composition
                        	<input type="hidden" name="hidcompid" id="hidcompid" value="" style="width:50px">
                            <input type="hidden" name="hidcompname" id="hidcompname" value="" style="width:50px">
                        </th>
	                </thead>
                    </table>
                    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="430" class="rpt_table" id="comp_tbl">
                    <tbody>
                    <? $i=1; foreach($composition as $id=>$comp_name) { if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; ?>
                    	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $id; ?>,'<? echo $comp_name; ?>')">
                            <td width="30"><? echo $i; ?></td>
                            <td><? echo $comp_name; ?> </td> 						
                        </tr>
                    <? $i++; } ?>
                    </tbody>
	            </table>
	            <div id="search_div" style="margin-top:5px"></div>   
	        </form>
	    </fieldset>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>setFilterGrid('comp_tbl',-1);</script>
	</html>
	<?
	exit();
}
	
if($action=="check_yarn_count_determination")
{
	//$data=explode("**",$data);
	$pre_sql="select lib_yarn_count_deter_id as detarmination_id from wo_pre_cost_fabric_cost_dtls a where  a.lib_yarn_count_deter_id=$data";
	$pre_data_array=sql_select($pre_sql);
	 
	 $sql="select a.detarmination_id as detarmination_id from product_details_master a where   a.detarmination_id=$data and a.is_deleted=0 and a.status_active=1"; 
	
	$data_array=sql_select($sql);
	if(count($data_array)>0 || count($pre_data_array)>0)
	{
		echo "1_";
	}
	else
	{
		echo "0_";
	}
	exit();	
}
?>