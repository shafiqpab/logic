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
	$lib_fabric_composition=return_library_array( "select id,fabric_composition_name from lib_fabric_composition where status_active=1", "id", "fabric_composition_name");
	$lib_fabric_construction=return_library_array( "select id,fabric_construction_name from lib_fabric_construction where status_active=1", "id", "fabric_construction_name");
	$sql_q="select mst_id, copmposition_id, percent, count_id, type_id from lib_yarn_count_determina_dtls where is_deleted=0 order by id";
	$lib_group_short=return_library_array( "select id,group_short_name from lib_group where id=1 and status_active=1", "id", "group_short_name");
	$group_short_name=$lib_group_short[1];

						
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
			$sys_code=$group_short_name.'-'.$row[csf('mst_id')];
			$sysCodeArr[$row[csf('mst_id')]]=$sys_code;
		}
	}
	unset($data_array);
	//print_r($composition_arr);
	$user_info_arr=return_library_array( "select user_full_name,id from user_passwd", "id", "user_full_name");
	
	$sql="select id, fab_nature_id, construction, gsm_weight, status_active, color_range_id, stich_length, process_loss, sequence_no,fabric_composition_id,rd_no,supplier_reference, inserted_by from  lib_yarn_count_determina_mst where is_deleted=0 and entry_form=184 order by id";

	$arr=array (0=>$item_category,2=>$sysCodeArr, 4=>$color_range,7=>$composition_arr,8=>$lib_fabric_composition, 12=>$user_info_arr,13=>$row_status);

	echo  create_list_view ( "list_view", "Fab Nature,Construction,Sys No,GSM/Weight,Color Range,Stich Length,Process Loss,Composition,Fabric Composition,Seq. No,RD Number,Supplier Reference,Insert by,Status", "100,100,80,100,100,90,50,300,200,50,100,100,100,50","1600","350",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'",1, "fab_nature_id,0,id,0,color_range_id,0,0,id,fabric_composition_id,0,0,0,inserted_by,status_active", $arr , "fab_nature_id,construction,id,gsm_weight,color_range_id,stich_length,process_loss,id,fabric_composition_id,sequence_no,rd_no,supplier_reference,inserted_by,status_active", "requires/yarn_count_determination_controller",'setFilterGrid("list_view",-1);','0,0,0,1,0,1,1,0,0,0,0,0,0,0') ;
	exit();
}
    
if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select("select fab_nature_id, construction, gsm_weight, fab_composition, color_range_id, fabric_composition_id, status_active, stich_length, process_loss, sequence_no, id, supplier_reference, rd_no, hs_code, count_range,from_entry_form, fabric_construction_id, gauge from lib_yarn_count_determina_mst where id='$data'");
	$lib_group_short=return_library_array( "select id,group_short_name from lib_group where id=1 and status_active=1", "id", "group_short_name");
	$lib_fabric_composition=return_library_array( "select id,fabric_composition_name from lib_fabric_composition where status_active=1", "id", "fabric_composition_name");
	$lib_compositionArr=return_library_array( "select id,composition_name from lib_composition_array where status_active=1", "id", "composition_name");
	$lib_fabric_construction=return_library_array( "select id,fabric_construction_name from lib_fabric_construction where status_active=1", "id", "fabric_construction_name");
	$group_short_name=$lib_group_short[1];
	
	foreach ($nameArray as $inf)
	{
		$group_short_name=$lib_group_short[1];
		$sys_code=$group_short_name.'-'.$inf[csf("id")];//
		$fabric_composition_name=$lib_fabric_composition[$inf[csf("fabric_composition_id")]];
		$from_entry_form=$inf[csf("from_entry_form")];
		if($from_entry_form==708 || $from_entry_form==709)
		{
			$fabric_composition_name=$lib_compositionArr[$inf[csf("fabric_composition_id")]];
		}
		
		echo "document.getElementById('cbo_fabric_nature').value  = '".($inf[csf("fab_nature_id")])."';\n";
		echo "document.getElementById('txt_sys_code').value  	= '".($sys_code)."';\n";
		echo "document.getElementById('txtconstruction').value = '".($inf[csf("construction")])."';\n";    
		echo "document.getElementById('txtgsmweight').value = '".($inf[csf("gsm_weight")])."';\n";
		echo "document.getElementById('cbocolortype').value = '".($inf[csf("color_range_id")])."';\n";
		echo "document.getElementById('cbo_status').value = '".($inf[csf("status_active")])."';\n";    
		echo "document.getElementById('stichlength').value  = '".($inf[csf("stich_length")])."';\n";
		echo "document.getElementById('processloss').value  = '".($inf[csf("process_loss")])."';\n";
		echo "document.getElementById('txt_sequence').value  = '".($inf[csf("sequence_no")])."';\n";
		echo "document.getElementById('txt_fab_composition').value  = '".($fabric_composition_name)."';\n";
		echo "document.getElementById('fab_composition_id').value  = '".($inf[csf("fabric_composition_id")])."';\n";
		echo "document.getElementById('fab_construction_id').value  = '".($inf[csf("fabric_construction_id")])."';\n";
		echo "document.getElementById('fab_from_entry_form').value  = '".($inf[csf("from_entry_form")])."';\n";
		echo "document.getElementById('update_mst_id').value  = '".($inf[csf("id")])."';\n";
	    echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_count_determination',1);\n";  
		echo "document.getElementById('txt_rd_number').value  = '".($inf[csf("rd_no")])."';\n";
		echo "document.getElementById('txt_hs_code').value  = '".($inf[csf("hs_code")])."';\n";
		echo "document.getElementById('cbo_count_range').value  = '".($inf[csf("count_range")])."';\n";
		echo "document.getElementById('txt_supplier_reference').value  = '".($inf[csf("supplier_reference")])."';\n";
		echo "document.getElementById('cbo_gauge').value  = '".($inf[csf("gauge")])."';\n";
		echo "show_detail_form('".$inf[csf("id")]."');\n"; 
	}
	exit();
}

if($action =="show_detail_form")
{
	?>
	<table width="100%" border="0" id="tbl_yarn_count" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" >
        <thead>
            <tr>
            	<th width="150" class="must_entry_caption"><font color="blue">Composition</font></th>
                <th width="50" class="must_entry_caption"><font color="blue">%</font></th>
                <th width="150">Count</th>
                <th width="150">Type</th>
                <th width="70">Yarn Rate</th>
                <th>&nbsp;</th> 
            </tr>
        </thead>
        <tbody>
			<?
				$is_used=0;
				$price_sql="select lib_yarn_count_deter_id as detarmination_id from wo_pri_quo_fabric_cost_dtls a where  a.lib_yarn_count_deter_id=$data";
				$price_data_array=sql_select($price_sql);

				$pre_sql="select lib_yarn_count_deter_id as detarmination_id from wo_pre_cost_fabric_cost_dtls a where  a.lib_yarn_count_deter_id=$data";
				$pre_data_array=sql_select($pre_sql);
				 
				$sql="select a.detarmination_id as detarmination_id from product_details_master a where   a.detarmination_id=$data and a.is_deleted=0 and a.status_active=1"; 
				
				$data_array=sql_select($sql);
				if(count($data_array)>0 || count($pre_data_array)>0 || count($price_data_array)>0)
				{
					$is_used=1;
				}
				$disabled='';
				if($is_used==1)
				{
					$disabled="disabled";
				}

				$data_array=sql_select("SELECT id, copmposition_id, percent,count_id, type_id, yarn_rate from lib_yarn_count_determina_dtls where mst_id='$data' and status_active=1 and is_deleted=0 order by id asc");
				if(count($data_array)>0)
				{
					$i=0;
					foreach($data_array as $row)
					{
						$i++;
						?>
						<tr id="yarncost_<?=$i; ?>" align="center">
                            <td>
                            	<input type="text" id="txtcompone_<?=$i; ?>" name="txtcompone_<?$i; ?>"  class="text_boxes" style="width:140px" readonly placeholder="Browse" onDblClick="openmypage_comp(<?=$i; ?>);" value="<?= $composition[$row[csf("copmposition_id")]]; ?>" <? echo $disabled ?> />
                                <input type="hidden" id="cbocompone_<?=$i; ?>" name="cbocompone_<?=$i; ?>" class="text_boxes" style="width:50px" value="<?=$row[csf("copmposition_id")]; ?>" />
                            
                            </td>
                            <td><input type="text" id="percentone_<?=$i; ?>" name="percentone_<?=$i; ?>" class="text_boxes_numeric" style="width:50px" onChange="sum_percent();" value="<?=$row[csf("percent")]; ?>" <? echo $disabled ?> /></td>
                            <td><?=create_drop_down( "cbocountcotton_".$i, 150, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1," -- Select Count --",  $row[csf("count_id")], "check_duplicate(".$i.",this.id )",$is_used,'' ); 
                            ?>
                            </td>
                            <td><?=create_drop_down( "cbotypecotton_".$i, 150, $yarn_type,"", 1, "-- Select --", $row[csf("type_id")], "check_duplicate(".$i.",this.id )",$is_used,'','','',$ommitYarnType); ?></td>
                            <td><input type="text" id="txtyarnrate_<?=$i; ?>" name="txtyarnrate_<?=$i; ?>" class="text_boxes_numeric" style="width:50px" value="<?=$row[csf("yarn_rate")]; ?>" />
                            <input type="hidden" id="updateid_<?=$i; ?>" name="updateid_<?=$i; ?>" class="text_boxes" style="width:20px" value="<?=$row[csf("id")]; ?>" /> 
                            </td>
                            <? if($is_used!=1){ ?>
                            <td> 
                                <input type="button" id="increase_<?=$i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<?=$i; ?>);" />
                                <input type="button" id="decrease_<?=$i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<?=$i; ?>);" />
                                 
                            </td>
                            <? } else { ?> 
                            	<td></td>
                            <? } ?>
						</tr>
						<?
					}
				}
	            ?>
        </tbody>
	</table>
	<tr><td><input type="hidden" name="total_percent" id="total_percent" value=""></td></tr>
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
			    data_all+=get_submitted_data_string('processid_'+i+'*processloss_'+i+'*effectivedate_'+i+'*txtrate_'+i,"../../../",i);
				
			}
			if(data_all=='')
			{
				alert("No Data Select");	
				return;
			}
			var data="action=save_update_delete_process_loss&operation="+operation+data_all+'&total_row='+tot_row+'&mst_id='+mst_id+'&tot_process_loss='+tot_process_loss_hidden;
			//alert(data);
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
 $sql_up_data=sql_select("select id, mst_id,rate, process_id, process_loss, effective_date from  conversion_process_loss where mst_id='$mst_id'");
 
 //var_dump($sql_up_data);
 ?>   
    
     <form name="rate_1" id="rate_1">
      <? echo load_freeze_divs ("../../../",$permission);  ?>
    <table width="520" cellspacing="0" class="rpt_table" border="0" rules="all">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="200">Process</th>
                <th width="100">Process Loss</th>
                <th width="100">Effective Date</th>
                <th  width="80">Taka</th>
            </tr>
        </thead>
    </table>
    
    <table width="520" cellspacing="0" class="rpt_table" border="0" id="tbl_process_loss_details" rules="all">
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
			<p><? echo $process_val;?></p>
            <input type="hidden" id="processid_<? echo $i; ?>" name="processid_<? echo $i; ?>" value="<? echo $process_id;?>"/>
        </td>
        <td width="100" align="center">
            <input type="text" id="processloss_<? echo $i; ?>" onKeyUp="fn_sum()" name="processloss_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px" />
        </td>
        <td align="center" width="100">
            <input type="text" id="effectivedate_<? echo $i; ?>" name="effectivedate_<? echo $i; ?>" class="datepicker" />
        </td>
         <td align="center"  width="80">
            <input type="text" id="txtrate_<? echo $i; ?>" style="width:80px" name="txtrate_<? echo $i; ?>" class="text_boxes_numeric" />
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
        <td width="100" align="center">
            <input type="text" id="processloss_<? echo $i; ?>" name="processloss_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px" onKeyUp="fn_sum()" value="<? echo $rows[csf("process_loss")];?>" />
        </td>
        <td align="center" width="100">
            <input type="text" id="effectivedate_<? echo $i; ?>" name="effectivedate_<? echo $i; ?>" class="datepicker" value="<? echo change_date_format($rows[csf("effective_date")]);?>" />
        </td>
        <td align="center"  width="80">
            <input type="text"  style="width:80px" id="txtrate_<? echo $i; ?>" name="txtrate_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $rows[csf("rate")];?>" />
        </td>
    </tr>
    <?
	$i++;
    }
	
}
	?>
    </table>
    <table width="510" cellspacing="0" class="rpt_table" border="0"rules="all">
    <thead>
        <td colspan="4">Total <span style="float:right;" id="tot_process_loss">&nbsp;</span></td>
        
        <td width="195">&nbsp;</td>
    </thead>
    <tr>
    <td colspan="5" align="center">
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
					col_4: "none",
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
		$field_array= "id,mst_id,process_id,process_loss, effective_date,rate,insert_by,insert_date,status_active,is_deleted";
		
		for ($i=1;$i<=$total_row;$i++)
		{
			$processid="processid_".$i;
			$processloss="processloss_".$i;
			$effectivedate="effectivedate_".$i;
			$txtrate="txtrate_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$mst_id.",".$$processid.",".$$processloss.",".$$effectivedate.",".$$txtrate.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
			$id++;
		}
		$tot_process_loss=str_replace("'","",$tot_process_loss);
		if($tot_process_loss) $tot_process_loss=$tot_process_loss;else $tot_process_loss=0;
		
		$field_array_mst= "process_loss*updated_by*update_date*status_active*is_deleted";
		$data_array_mst ="".$tot_process_loss."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'1'*'0'";
		$rID_mst=sql_update("lib_yarn_count_determina_mst",$field_array_mst,$data_array_mst,"id","".$mst_id."",0);
		$rID=sql_insert("conversion_process_loss",$field_array,$data_array,1);
	//	echo "10*".$rID_mst.'='.$rID;die;
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
		$field_array= "id,mst_id,process_id,process_loss, effective_date,rate,insert_by,insert_date,status_active,is_deleted";
		
		for ($i=1;$i<=$total_row;$i++)
		{
			$processid="processid_".$i;
			$processloss="processloss_".$i;
			$effectivedate="effectivedate_".$i;
			$txtrate="txtrate_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$mst_id.",".$$processid.",".$$processloss.",".$$effectivedate.",".$$txtrate.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
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
	$componearr=array();
	$current_yarn_key="";
	for ($i=1;$i<=$total_row;$i++)
	{
		$cbocompone="cbocompone_".$i;
		$percentone="percentone_".$i;
		$cbocountcotton="cbocountcotton_".$i;
		$cbotypecotton="cbotypecotton_".$i;

		$composition_id=str_replace("'",'',$$cbocompone);
		$percentone_id=str_replace("'",'',$$percentone);
		$countcotton_id=str_replace("'",'',$$cbocountcotton);
		$typecotton_id=str_replace("'",'',$$cbotypecotton);

		$current_yarn_str=$composition_id.'*'.$percentone_id.'*'.$countcotton_id.'*'.$typecotton_id;

		if($current_yarn_key != ''){
			$current_yarn_key.='*'.$current_yarn_str;
		}
		else{
			$current_yarn_key.=str_replace("'",'',$fab_construction_id).'*'.$current_yarn_str;
		}

		$componearr[$composition_id]=$composition_id;
		
	}
	//echo "10**".$current_yarn_key; die;
	if(count($componearr)>0){
		$comp_str=implode(",",$componearr);
		$comp_cond="and b.copmposition_id in ($comp_str)";
	}
	$updateid_con="";
	if($operation==1){
		$updateid_cond="and a.id != $update_mst_id";
	}
	$duplicate_sql=sql_select("SELECT a.id, b.id as dtls_id,  a.fabric_construction_id, b.copmposition_id, b.percent, b.count_id, b.type_id from lib_yarn_count_determina_mst a join lib_yarn_count_determina_dtls b on a.id=b.mst_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=184 and a.fabric_construction_id=$fab_construction_id $comp_cond $updateid_cond");

	if(count($duplicate_sql)>0){
		foreach($duplicate_sql as $row){
			$dtls_str=$row[csf('copmposition_id')].'*'.$row[csf('percent')].'*'.$row[csf('count_id')].'*'.$row[csf('type_id')];
			$yarn_duplicate_data[$row[csf('id')]][$row[csf('dtls_id')]]=$dtls_str;
			$construction_id_arr[$row[csf('id')]]=$row[csf('fabric_construction_id')];
		}
		
	}
	foreach($yarn_duplicate_data as $yarnid=>$yarn_data){
		$all_str=$construction_id_arr[$yarnid].'*'.implode("*",$yarn_data);
		$chk_yarn_arr[$all_str]=$all_str;
	}

	if(array_key_exists($current_yarn_key,$chk_yarn_arr)){
		$msg="Duplicate Found.";
		echo "11**".$msg; disconnect($con); die;
	}
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		
		$str_rep=array("&", "*", "(", ")", "=","'","_",",","\r", "\n",'"','#');
		$construction=str_replace("'",'',$txtconstruction);
		
		$txtconstruction=str_replace($str_rep,' ',$construction);
		//$txt_fab_composition=str_replace("'",'',$txt_fab_composition);
		//$txtfab_composition=str_replace($str_rep,' ',$txt_fab_composition);
		//hidfabconspid hidfabconsname fab_group_hid_id  txt_fab_group_name
		$id=return_next_id( "id", "lib_yarn_count_determina_mst", 1 ) ;
		$field_array1= "id, fab_nature_id,  construction, gsm_weight, color_range_id, stich_length, process_loss, sequence_no, rd_no, supplier_reference, fabric_composition_id, fabric_construction_id, entry_form, hs_code, count_range, gauge,fabric_group_name,fabric_group_entry_id,from_entry_form, inserted_by, insert_date, status_active, is_deleted";
		$data_array1="(".$id.",".$cbo_fabric_nature.",'".$txtconstruction."',".$txtgsmweight.",".$cbocolortype.",".$stichlength.",".$processloss.",".$txt_sequence.",".$txt_rd_number.",".$txt_supplier_reference.",".$fab_composition_id.",".$fab_construction_id.",184,$txt_hs_code,".$cbo_count_range.",".$cbo_gauge.",".$txt_fab_group_name.",".$fab_group_hid_id.",".$fab_from_entry_form.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",'0')";
		$id_dtls=return_next_id( "id", "lib_yarn_count_determina_dtls", 1 ) ;
		$field_array2= "id,mst_id, copmposition_id, percent, count_id, type_id, yarn_rate, inserted_by, insert_date, status_active, is_deleted";
		for ($i=1;$i<=$total_row;$i++)
		{
			$cbocompone="cbocompone_".$i;
			$percentone="percentone_".$i;
			$cbocountcotton="cbocountcotton_".$i;
			$cbotypecotton="cbotypecotton_".$i;
			$txtyarnrate="txtyarnrate_".$i;
			
			 
			$composition_chk=str_replace("'",'',$$cbocompone);
			$percentone_chk=str_replace("'",'',$$percentone);
			$countcotton_chk=str_replace("'",'',$$cbocountcotton);
			$typecotton_chk=str_replace("'",'',$$cbotypecotton);
			
			$updateid="updateid_".$i;
			if ($i!=1) $data_array2 .=",";
			$data_array2 .="(".$id_dtls.",".$id.",".$$cbocompone.",".$$percentone.",".$$cbocountcotton.",".$$cbotypecotton.",".$$txtyarnrate.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
			$id_dtls=$id_dtls+1;
		}
		
		/*echo "10**INSERT INTO lib_yarn_count_determina_mst(".$field_array1.") VALUES ".$data_array1;
		echo "10**INSERT INTO lib_yarn_count_determina_dtls(".$field_array2.") VALUES ".$data_array2;die;*/
		//echo "10**INSERT INTO lib_yarn_count_determina_mst(".$field_array1.") VALUES ".$data_array1;die;
		
		$rID=sql_insert("lib_yarn_count_determina_mst",$field_array1,$data_array1,0);
		$rID_1=sql_insert("lib_yarn_count_determina_dtls",$field_array2,$data_array2,1);
		//echo "10**=A".$rID.'='.$rID_1;die;
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
		
		/*if(str_replace("'","",$cbo_status)==2)
		{
			$rID=execute_query( "update lib_yarn_count_determina_dtls set status_active=$cbo_status where  mst_id =$update_mst_id",0);
			$rID_1=execute_query( "update lib_yarn_count_determina_mst set status_active=$cbo_status where  id =$update_mst_id",0);
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
			$flag = 0;
			if(count($data_array)>0 || count($pre_data_array)>0)
			{
				$rID=execute_query( "update  lib_yarn_count_determina_dtls set status_active=$cbo_status where  mst_id =$update_mst_id",0);
				$rID_1=execute_query( "update  lib_yarn_count_determina_mst set status_active=$cbo_status where  id =$update_mst_id",0);
				if($db_type==0)
				{
					if($rID && $rID_1){
						mysql_query("COMMIT");  
						echo "1**".$rID;
						//$flag = 1;
					}
					else{
						mysql_query("ROLLBACK"); 
						echo "10**".$rID;
						//$flag = 0;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID && $rID_1)
					{
						oci_commit($con);  
						echo "1**".$rID;
						//$flag = 1;
					}
					else{
						oci_rollback($con); 
						echo "10**".$rID;
						//$flag = 0;
					}
				}
				//die;
			}
			 
		}*/
		
		$str_rep=array("&", "*", "(", ")", "=","'","_",",","\r", "\n",'"','#');
		$construction=str_replace("'",'',$txtconstruction);
		$txtconstruction=str_replace($str_rep,' ',$construction);
		$field_array1= "fab_nature_id*construction*gsm_weight*color_range_id*stich_length*process_loss*sequence_no*rd_no*supplier_reference*fabric_composition_id*fabric_construction_id*hs_code*count_range*gauge*fabric_group_name*fabric_group_entry_id*from_entry_form*updated_by*update_date*status_active*is_deleted*is_posted_sql"; 
		$data_array1="".$cbo_fabric_nature."*'".$txtconstruction."'*".$txtgsmweight."*".$cbocolortype."*".$stichlength."*".$processloss."*".$txt_sequence."*".$txt_rd_number."*".$txt_supplier_reference."*".$fab_composition_id."*".$fab_construction_id."*".$txt_hs_code."*".$cbo_count_range."*".$cbo_gauge."*".$txt_fab_group_name."*".$fab_group_hid_id."*".$fab_from_entry_form."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*'0'*'2'";
														//.$pc_date_time."'*".$cbo_status."*0";
			
			$is_used=0;
			$price_sql="select lib_yarn_count_deter_id as detarmination_id from wo_pri_quo_fabric_cost_dtls a where  a.lib_yarn_count_deter_id=$update_mst_id";
			$price_data_array=sql_select($price_sql);

			$pre_sql="select lib_yarn_count_deter_id as detarmination_id from wo_pre_cost_fabric_cost_dtls a where  a.lib_yarn_count_deter_id=$update_mst_id";
			$pre_data_array=sql_select($pre_sql);
			 
			$sql="select a.detarmination_id as detarmination_id from product_details_master a where   a.detarmination_id=$update_mst_id and a.is_deleted=0 and a.status_active=1"; 
			
			$data_array=sql_select($sql);
			if(count($data_array)>0 || count($pre_data_array)>0 || count($price_data_array)>0)
			{
				$is_used=1;
			}
			
			//$rID_de1=execute_query( "delete from lib_yarn_count_determina_dtls where  mst_id =".$update_mst_id."",0);
			$field_array13= "updated_by*update_date*status_active*is_deleted";
			$data_array13="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			//$rID_de1=execute_query( "delete from lib_yarn_count_determina_dtls where  mst_id =".$update_mst_id."",0);
			$rID_de1=sql_update("lib_yarn_count_determina_dtls",$field_array13,$data_array13,"mst_id","".$update_mst_id."",0);
			
			$id_dtls=return_next_id( "id", "lib_yarn_count_determina_dtls", 1 ) ;
			
			//echo "shajjad";die;
			
			
			$field_array2= "id, mst_id, copmposition_id, percent, count_id, type_id, yarn_rate, inserted_by, insert_date, status_active, is_deleted";
			for ($i=1;$i<=$total_row;$i++)
			{
				$cbocompone="cbocompone_".$i;
				$percentone="percentone_".$i;
				$cbocountcotton="cbocountcotton_".$i;
				$cbotypecotton="cbotypecotton_".$i;
				$txtyarnrate="txtyarnrate_".$i;
				$updateid="updateid_".$i;
				
				$composition_chk=str_replace("'",'',$$cbocompone);
				$percentone_chk=str_replace("'",'',$$percentone);
				$countcotton_chk=str_replace("'",'',$$cbocountcotton);
				$typecotton_chk=str_replace("'",'',$$cbotypecotton);			
	
				if ($i!=1) $data_array2 .=",";
				$data_array2 .="(".$id_dtls.",".$update_mst_id.",".$$cbocompone.",".$$percentone.",".$$cbocountcotton.",".$$cbotypecotton.",".$$txtyarnrate.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
				$id_dtls=$id_dtls+1;
			}
			
			//echo "INSERT INTO lib_yarn_count_determina_mst(".$field_array1.") VALUES ".$data_array1;die;
			//echo $is_used.'DDD';
			$rID=1;
			if($is_used!=1)
			{
				$rID=sql_update("lib_yarn_count_determina_mst",$field_array1,$data_array1,"id","".$update_mst_id."",0);
			}			
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

if($action=="fabric_composition_popup")
{
	echo load_html_head_contents("Fabric Composition Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);	
	
	?>
	<script>
		function js_set_value(id,name,entry_form)
		{
			document.getElementById('hidfabcompid').value=id;
			document.getElementById('hidfabcompname').value=name;
			document.getElementById('entry_form').value=entry_form;
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
	                    <th>Fabric Composition
                        	<input type="hidden" name="hidfabcompid" id="hidfabcompid" value="" style="width:50px">
                            <input type="hidden" name="hidfabcompname" id="hidfabcompname" value="" style="width:50px">
							<input type="hidden" name="entry_form" id="entry_form" value="" style="width:50px">
                        </th>
	                </thead>
                    </table>
                    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="430" class="rpt_table" id="fab_comp_tbl">
                    <tbody>

                    <? 
					   $sql_fab_com="(select id, fabric_composition_name as composition_name,entry_form from  lib_fabric_composition where entry_form=710 and status_active=1 and is_deleted=0 
					 union all  select id, composition_name as composition_name,entry_form from  lib_composition_array where entry_form in(708,709) and is_fabric=1 and status_active=1 and is_deleted=0 ) order by composition_name
					  ";
					$sql_fab_com_res=sql_select($sql_fab_com);
					 

                    // $fabric_composition = return_library_array("select id, fabric_composition_name from  lib_fabric_composition where status_active=1 and is_deleted=0 order by fabric_composition_name", "id", "fabric_composition_name");
					 
                    $i=1; 
                    foreach($sql_fab_com_res as $row) 
                    { 
                    	if($i%2==0) $bgcolor="#E9F3FF"; 
                    	else $bgcolor="#FFFFFF"; 
                    	?>
                    	<tr title="EntryFormID=<?=$row[csf('entry_form')];?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('composition_name')]; ?>','<? echo $row[csf('entry_form')]; ?>')">
                            <td width="30"><? echo $i; ?></td>
                            <td><? echo $row[csf('composition_name')]; ?> </td> 						
                        </tr>
                    	<? 
                    	$i++; 
                	} 
                	?>
                    </tbody>
	            </table>
	            <div id="search_div" style="margin-top:5px"></div>   
	        </form>
	    </fieldset>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>setFilterGrid('fab_comp_tbl',-1);</script>
	</html>
	<?
	exit();
}

if($action=="fabric_construction_popup")
{
	echo load_html_head_contents("Fabric Construction Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);	
	
	?>
	<script>
		function js_set_value(id,name)
		{
			document.getElementById('hidfabconspid').value=id;
			document.getElementById('hidfabconsname').value=name;
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
	                    <th>Fabric Construction
                        	<input type="hidden" name="hidfabconspid" id="hidfabconspid" value="" style="width:50px">
                            <input type="hidden" name="hidfabconsname" id="hidfabconsname" value="" style="width:50px">
                        </th>
	                </thead>
                    </table>
                    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="430" class="rpt_table" id="fab_cons_tbl">
                    <tbody>

                    <? 
                    $fabric_construction = return_library_array("select id, fabric_construction_name from  lib_fabric_construction where status_active=1 and is_deleted=0 order by fabric_construction_name", "id", "fabric_construction_name");
                    $i=1; 
                    foreach($fabric_construction as $id=>$fab_cons_name) 
                    { 
                    	if($i%2==0) $bgcolor="#E9F3FF"; 
                    	else $bgcolor="#FFFFFF"; 
                    	?>
                    	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $id; ?>,'<? echo $fab_cons_name; ?>')">
                            <td width="30"><? echo $i; ?></td>
                            <td><? echo $fab_cons_name; ?> </td> 						
                        </tr>
                    	<? 
                    	$i++; 
                	} 
                	?>
                    </tbody>
	            </table>
	            <div id="search_div" style="margin-top:5px"></div>   
	        </form>
	    </fieldset>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>setFilterGrid('fab_cons_tbl',-1);</script>
	</html>
	<?
	exit();
}
if($action=="fabric_group_popup")
{
	echo load_html_head_contents("Fabric Construction Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);	
	
	?>
	<script>
		function js_set_value(id,name)
		{
			document.getElementById('hidfabgroupid').value=id;
			document.getElementById('hidfabgroupname').value=name;
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
	                    <th>Fabric Group Name
                        	<input type="hidden" name="hidfabgroupid" id="hidfabgroupid" value="" style="width:50px">
                            <input type="hidden" name="hidfabgroupname" id="hidfabgroupname" value="" style="width:50px">
                        </th>
	                </thead>
                    </table>
                    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="430" class="rpt_table" id="fab_cons_tbl">
                    <tbody>

                    <? 
                    $fabric_group_query = return_library_array("select id, fabric_group_name from  lib_fabric_group_entry where status_active=1 and is_deleted=0 order by fabric_group_name", "id", "fabric_group_name");
                    $i=1; 
                    foreach($fabric_group_query as $id=>$fab_group_name) 
                    { 
                    	if($i%2==0) $bgcolor="#E9F3FF"; 
                    	else $bgcolor="#FFFFFF"; 
                    	?>
                    	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $id; ?>,'<? echo $fab_group_name; ?>')">
                            <td width="30"><? echo $i; ?></td>
                            <td><? echo $fab_group_name; ?> </td> 						
                        </tr>
                    	<? 
                    	$i++; 
                	} 
                	?>
                    </tbody>
	            </table>
	            <div id="search_div" style="margin-top:5px"></div>   
	        </form>
	    </fieldset>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>setFilterGrid('fab_cons_tbl',-1);</script>
	</html>
	<?
	exit();
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

                    <? 
                    $i=1; foreach($composition as $id=>$comp_name) { if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; ?>
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
	$price_sql="select lib_yarn_count_deter_id as detarmination_id from wo_pri_quo_fabric_cost_dtls a where  a.lib_yarn_count_deter_id=$data";
	$price_data_array=sql_select($price_sql);

	$pre_sql="select lib_yarn_count_deter_id as detarmination_id from wo_pre_cost_fabric_cost_dtls a where  a.lib_yarn_count_deter_id=$data";
	$pre_data_array=sql_select($pre_sql);
	 
	$sql="select a.detarmination_id as detarmination_id from product_details_master a where   a.detarmination_id=$data and a.is_deleted=0 and a.status_active=1"; 
	
	$data_array=sql_select($sql);
	if(count($data_array)>0 || count($pre_data_array)>0 || count($price_data_array)>0)
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