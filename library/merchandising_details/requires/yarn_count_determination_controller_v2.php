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
	
	$sql="select id, fab_nature_id, construction, gsm_weight, status_active, color_range_id, stich_length, process_loss, sequence_no,fabric_composition_id,rd_no,supplier_reference from  lib_yarn_count_determina_mst where is_deleted=0 and entry_form=184 order by id";

	$arr=array (0=>$item_category,2=>$sysCodeArr, 4=>$color_range,6=>$composition_arr,7=>$lib_fabric_composition,11=>$row_status);

	echo  create_list_view ( "list_view", "Fab Nature,Construction,Sys No,GSM/Weight,Color Range,Process Loss,Composition,Fabric Composition,Sequence No,RD Number,Supplier Reference,Status", "100,100,80,100,90,50,300,200,50,100,100,50","1500","350",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'",1, "fab_nature_id,0,id,0,color_range_id,0,id,fabric_composition_id,0,0,0,status_active", $arr , "fab_nature_id,construction,id,gsm_weight,color_range_id,process_loss,id,fabric_composition_id,sequence_no,rd_no,supplier_reference,status_active", "requires/yarn_count_determination_controller_v2",'setFilterGrid("list_view",-1);','0,0,0,1,0,1,1,0,0,0,0,0,0') ;
	exit();
}
    
if ($action=="load_php_data_to_form")
{
	$sql="select fab_nature_id, construction, gsm_weight, fab_composition, color_range_id, fabric_composition_id, status_active, stich_length, process_loss, sequence_no, id, supplier_reference,buyer_id, rd_no,construction_short,construction_type,brand_id,fabric_construction_id,fabric_yarn_finish from lib_yarn_count_determina_mst where id='$data'";
	//echo $sql;
	$nameArray=sql_select($sql);
	$lib_group_short=return_library_array( "select id,group_short_name from lib_group where id=1 and status_active=1", "id", "group_short_name");
	$lib_fabric_composition=return_library_array( "select id,fabric_composition_name from lib_fabric_composition where status_active=1", "id", "fabric_composition_name");
	$fabric_type_arr=return_library_array( "select id,fabric_type_name from lib_fabric_type where status_active=1", "id", "fabric_type_name");
	$group_short_name=$lib_group_short[1];
	
	foreach ($nameArray as $inf)
	{
		$group_short_name=$lib_group_short[1];
		$sys_code=$group_short_name.'-'.$inf[csf("id")];
		$fabric_composition_name=$lib_fabric_composition[$inf[csf("fabric_composition_id")]];
		
		
		echo "document.getElementById('cbo_fabric_nature').value  = '".($inf[csf("fab_nature_id")])."';\n";
		echo "document.getElementById('txt_sys_code').value  	= '".($sys_code)."';\n";
		echo "document.getElementById('txtconstruction').value = '".($inf[csf("construction_short")])."';\n";    
		echo "document.getElementById('fab_construction_id').value = '".($inf[csf("FABRIC_CONSTRUCTION_ID")])."';\n";    
		echo "document.getElementById('txtgsmweight').value = '".($inf[csf("gsm_weight")])."';\n";
		echo "document.getElementById('cbocolortype').value = '".($inf[csf("color_range_id")])."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".($inf[csf("buyer_id")])."';\n";
		echo "get_buyer_config('".($inf[csf("buyer_id")])."');\n";
		echo "document.getElementById('cbo_brand_id').value = '".($inf[csf("brand_id")])."';\n";    
		echo "document.getElementById('cbo_status').value = '".($inf[csf("status_active")])."';\n";    
		echo "document.getElementById('stichlength').value  = '".($inf[csf("stich_length")])."';\n";
		echo "document.getElementById('processloss').value  = '".($inf[csf("process_loss")])."';\n";
		echo "document.getElementById('txt_sequence').value  = '".($inf[csf("sequence_no")])."';\n";
		echo "document.getElementById('cbo_fabric_yarn_finish').value  = '".($inf[csf("fabric_yarn_finish")])."';\n";
		echo "document.getElementById('txt_fab_composition').value  = '".($fabric_composition_name)."';\n";
		echo "document.getElementById('cbo_construction_type').value  = '".($fabric_type_arr[$inf[csf("construction_type")]])."';\n";
		echo "document.getElementById('fab_type_id').value  = '".($inf[csf("construction_type")])."';\n";
		echo "document.getElementById('fab_composition_id').value  = '".($inf[csf("fabric_composition_id")])."';\n";
		echo "document.getElementById('update_mst_id').value  = '".($inf[csf("id")])."';\n";
	    echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_count_determination',1);\n";  
		echo "document.getElementById('txt_rd_number').value  = '".($inf[csf("rd_no")])."';\n";
		echo "document.getElementById('txt_supplier_reference').value  = '".($inf[csf("supplier_reference")])."';\n";
		echo "show_detail_form('".$inf[csf("id")]."');\n"; 
	}
	exit();
}

if($action =="show_detail_form")
{
	
	$composition_name=return_library_array( "select id,composition_name from lib_composition_array", "id", "composition_name");
	?>
	<table width="100%" border="0" id="tbl_yarn_count" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" >
        <thead>
            <tr>
            	<th width="150" class="must_entry_caption"><font color="blue">Composition</font></th>
                <th width="50" class="must_entry_caption"><font color="blue">%</font></th>
                <th width="150">Count</th>
                <th width="150">Type</th>
                <th width="70">Yarn Rate</th>
                <th width="110">Yarn Finish</th>
                <th width="110">Yarn Spinning System</th>
                <th width="80">Certification</th>
                <th width="80">Yarn Color</th>
                <th width="120">Yarn Code</th>
                <th>&nbsp;</th> 
            </tr>
        </thead>
        <tbody>
			<?
				$data_array=sql_select("select id, copmposition_id, percent,count_id, type_id, yarn_rate,yarn_finish,yarn_spinning_system,certification,yarn_color_id,yarn_code from lib_yarn_count_determina_dtls where mst_id='$data' order by id asc");
				if(count($data_array)>0)
				{
					$i=0;
					$lib_yarn_count=return_library_array( "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id", "yarn_count");
					foreach($data_array as $row)
					{
						$txt_count=$lib_yarn_count[$row[csf('count_id')]];
						$i++;
						?>
						<tr id="yarncost_<?=$i; ?>" align="center">
                            <td>
                            	<input type="text" id="txtcompone_<?=$i; ?>" name="txtcompone_<?$i; ?>"  class="text_boxes" style="width:140px" readonly placeholder="Browse" onDblClick="openmypage_comp(<?=$i; ?>);" value="<?= $composition_name[$row[csf("copmposition_id")]]; ?>" />
                                <input type="hidden" id="cbocompone_<?=$i; ?>" name="cbocompone_<?=$i; ?>" class="text_boxes" style="width:50px" value="<?=$row[csf("copmposition_id")]; ?>" />
                                
                            
                            </td>
                            <td>
                            	<input type="text" id="percentone_<?=$i; ?>" name="percentone_<?=$i; ?>" class="text_boxes_numeric" style="width:50px" onChange="sum_percent();" value="<?=$row[csf("percent")]; ?>" />
                            </td>
                            <td>
                            	<input type="hidden" id="cbocountcotton_<?=$i; ?>"  name="cbocountcotton_<?=$i; ?>" class="text_boxes"  value="<?php echo $row[csf('count_id')]; ?>" />

                            	<input type="text" id="txtcountcotton_<?=$i; ?>"  name="txtcountcotton_<?=$i; ?>"  class="text_boxes" style="width:140px" value="<?=$txt_count;?>" readonly placeholder="Browse" onDblClick="openmypage_count(<?=$i; ?>);" />
                                    
                            	<? /*create_drop_down( "cbocountcotton_".$i, 150, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1," -- Select Count --",  $row[csf("count_id")], "check_duplicate(".$i.",this.id )",'','' ); */
                            	?>
                            </td>
                            <td><?=create_drop_down( "cbotypecotton_".$i, 150, $yarn_type,"", 1, "-- Select --", $row[csf("type_id")], "check_duplicate(".$i.",this.id )",'','','','',$ommitYarnType); ?></td>
                            <td><input type="text" id="txtyarnrate_<?=$i; ?>" name="txtyarnrate_<?=$i; ?>" class="text_boxes_numeric" style="width:50px" value="<?=$row[csf("yarn_rate")]; ?>" /></td>
                            <td><?=create_drop_down( "cboyarnfinish_".$i, 110, $yarn_finish_arr,"", 1, "-- Select --", $row[csf("yarn_finish")], '','','','','','' ); ?></td>
                            <td><?=create_drop_down( "cboyarnspinningsystem_".$i, 110, $yarn_spinning_system_arr,"", 1, "-- Select --", $row[csf("yarn_spinning_system")], '','','','','','' ); ?></td>
                             <td><? echo create_drop_down( "cbocertification_".$i, 80, $certification_arr,"", 1, "-- Select --", $row[csf("certification")], '','','','','' ); ?></td>
                              <td><? echo create_drop_down( "cboyarncolor_".$i, 80, "Select id,color_name from lib_color where status_active=1 and is_deleted=0 group by id,color_name","id,color_name", 1, "-- Select --", $row[csf("yarn_color_id")], '','','','','' ); ?></td>
                             <td>
                             	<input type="text" id="txtyarncode_<?=$i;?>"  name="txtyarncode_<?=$i;?>"  class="text_boxes" style="width:120px" value="<?=$row[csf('yarn_code')]?>" placeholder="Yarn Code" />
                             </td>
                            <td> 
                                <input type="button" id="increase_<?=$i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<?=$i; ?>);" />
                                <input type="button" id="decrease_<?=$i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<?=$i; ?>);" />
                                <input type="hidden" id="updateid_<?=$i; ?>" name="updateid_<?=$i; ?>" class="text_boxes" style="width:20px" value="<?=$row[csf("id")]; ?>" />  
                            </td>  
						</tr>
						<?
					}
				}
				else
				{
					?>
                        <tr id="yarncost_1" align="center">
                            <td>
                            	<input type="text" id="txtcompone_1"  name="txtcompone_1"  class="text_boxes" style="width:140px" value="" readonly placeholder="Browse" onDblClick="openmypage_comp(1);" />
                                <input type="hidden" id="cbocompone_1"  name="cbocompone_1" class="text_boxes" style="width:50px" value="" />
                            </td>
                            <td><input type="text" id="percentone_1"  name="percentone_1" onChange="sum_percent();" class="text_boxes_numeric" style="width:50px" value="" /></td>
                            <td>
                            	<? /* echo create_drop_down( "cbocountcotton_1", 150, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1," -- Select Count --", '', 'check_duplicate(1,this.id);','','' ); 
									*/
                            	?>

                            	<input type="text" id="txtcountcotton_1"  name="txtcountcotton_1"  class="text_boxes" style="width:140px" value="" readonly placeholder="Browse" onDblClick="openmypage_count(1);" />
                                <input type="hidden" id="cbocountcotton_1"  name="cbocountcotton_1" class="text_boxes"  value="" />
                            </td>
                            <td><?=create_drop_down( "cbotypecotton_1", 150, $yarn_type,"", 1, "-- Select --", '', 'check_duplicate(1,this.id)','','','','',$ommitYarnType ); ?></td>

                            <td><input type="text" id="txtyarnrate_1"  name="txtyarnrate_1" class="text_boxes_numeric" style="width:50px" value="" /></td>

                             <td><?=create_drop_down( "cboyarnfinish_1", 110, $yarn_finish_arr,"", 1, "-- Select --", '', '','','','','','' ); ?></td>
                             <td><?=create_drop_down( "cboyarnspinningsystem_1", 110, $yarn_spinning_system_arr,"", 1, "-- Select --", '', '','','','','','' ); ?></td>
                              <td><? echo create_drop_down( "cbocertification_1", 80, $certification_arr,"", 1, "-- Select --", '', '','','','','' ); ?></td>

                             <td><? echo create_drop_down( "cboyarncolor_1", 80, "Select id,color_name from lib_color where status_active=1 and is_deleted=0 group by id,color_name","id,color_name", 1, "-- Select --", '', '','','','','' ); ?></td>
                             <td>
                             	<input type="text" id="txtyarncode_1"  name="txtyarncode_1"  class="text_boxes" style="width:120px" value="" placeholder="Yarn Code" />
                             </td>
                            <td> 
                                <input type="button" id="increase_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(1)" />
                                <input type="button" id="decrease_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1);" />
                                <input type="hidden" id="updateid_1" name="updateid_1"  class="text_boxes" style="width:20px" value=""  />                                     
                            </td>  
                        </tr>
					<?
				}
	            ?>
        </tbody>
	</table>
	<tr><td><input type="hidden" name="total_percent" id="total_percent" value=""></td></tr>
	<?
	exit();
}

if ($action=="load_drop_down_brand")
{
	$data_arr = explode("*", $data);
	$width=190;
	echo create_drop_down( "cbo_brand_id", $width, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
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
			http.open("POST","yarn_count_determination_controller_v2.php",true);
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
                <th  width="80">Rate</th>
            </tr>
        </thead>
    </table>
    
    <table width="500" cellspacing="0" class="rpt_table" border="0" id="tbl_process_loss_details" rules="all">
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
         <td align="center"  width="55">
            <input type="text" id="txtrate_<? echo $i; ?>" style="width:50px" name="txtrate_<? echo $i; ?>" class="text_boxes_numeric" />
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
        <td align="center"  width="55">
            <input type="text"  style="width:50px" id="txtrate_<? echo $i; ?>" name="txtrate_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $rows[csf("rate")];?>" />
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
			
			http.open("POST","yarn_count_determination_controller_v2.php",true);
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
                	<td><!-- Stitch Length --></td>
                    <td>
                        <input type="hidden" name="txt_stitch_length" id="txt_stitch_length" class="text_boxes" style="width:100px;" value="<? echo $dataArray[0][csf('stitch_length')]; ?>"/>
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
	$cbo_buyer_name=str_replace("'", "", $cbo_buyer_name);
	$cbo_construction_type=str_replace("'",'',$cbo_construction_type);
	$fab_type_id=str_replace("'",'',$fab_type_id);
	$fab_construction_id=str_replace("'",'',$fab_construction_id);
	$cbo_brand_id=str_replace("'",'',$cbo_brand_id);
	$cbo_fabric_yarn_finish=str_replace("'",'',$cbo_fabric_yarn_finish);
	
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

		
		$id=return_next_id( "id", "lib_yarn_count_determina_mst", 1 ) ;
	$field_array1= "id,fab_nature_id, construction,construction_short,construction_type,gsm_weight,color_range_id,stich_length,buyer_id,process_loss,sequence_no,rd_no,supplier_reference,fabric_composition_id,fabric_construction_id,brand_id,fabric_yarn_finish,entry_form,inserted_by,insert_date,status_active,is_deleted";
		$data_array1="(".$id.",".$cbo_fabric_nature.",'".$txtconstruction." ".$cbo_construction_type."','".$txtconstruction."','".$fab_type_id."',".$txtgsmweight.",".$cbocolortype.",".$stichlength.",'".$cbo_buyer_name."',".$processloss.",".$txt_sequence.",".$txt_rd_number.",".$txt_supplier_reference.",".$fab_composition_id.",'".$fab_construction_id."','".$cbo_brand_id."','".$cbo_fabric_yarn_finish."',184,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",'0')";
		$id_dtls=return_next_id( "id", "lib_yarn_count_determina_dtls", 1 ) ;
		$field_array2= "id,mst_id, copmposition_id, percent, count_id, type_id, yarn_rate,yarn_finish,yarn_spinning_system,certification,yarn_color_id,yarn_code, inserted_by, insert_date, status_active, is_deleted";
		for ($i=1;$i<=$total_row;$i++)
		{
			$cbocompone="cbocompone_".$i;
			$percentone="percentone_".$i;
			$cbocountcotton="cbocountcotton_".$i;
			$cbotypecotton="cbotypecotton_".$i;
			$txtyarnrate="txtyarnrate_".$i;
			$updateid="updateid_".$i;
			$cbo_yarn_finish="cboyarnfinish_".$i;
			$cbo_yarn_spinning_system="cboyarnspinningsystem_".$i;
			$cbocertification="cbocertification_".$i;
			$cboyarncolor="cboyarncolor_".$i;
			$txtyarncode="txtyarncode_".$i;
			if ($i!=1) $data_array2 .=",";
			$data_array2 .="(".$id_dtls.",".$id.",".$$cbocompone.",".$$percentone.",".$$cbocountcotton.",".$$cbotypecotton.",".$$txtyarnrate.",'".str_replace("'", "", $$cbo_yarn_finish)."','".str_replace("'", "", $$cbo_yarn_spinning_system)."','".str_replace("'", "", $$cbocertification)."','".str_replace("'", "", $$cboyarncolor)."','".str_replace("'", "", $$txtyarncode)."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
			$id_dtls=$id_dtls+1;
		}
		
		/*echo "10**INSERT INTO lib_yarn_count_determina_mst(".$field_array1.") VALUES ".$data_array1;
		echo "10**INSERT INTO lib_yarn_count_determina_dtls(".$field_array2.") VALUES ".$data_array2;die;*/
		
		
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
				echo "10**INSERT INTO lib_yarn_count_determina_mst(".$field_array1.") VALUES ".$data_array1;
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
				echo "10**INSERT INTO lib_yarn_count_determina_mst(".$field_array1.") VALUES ".$data_array1;
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
			 
		}
		
			$str_rep=array("&", "*", "(", ")", "=","'","_",",","\r", "\n",'"','#');
			$construction=str_replace("'",'',$txtconstruction);
			$txtconstruction=str_replace($str_rep,' ',$construction);
			//$txt_fab_composition=str_replace("'",'',$txt_fab_composition);
			//$txtfab_composition=str_replace($str_rep,' ',$txt_fab_composition);
			//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		$field_array1= "fab_nature_id*construction*construction_short*construction_type*gsm_weight*color_range_id*stich_length*buyer_id*process_loss*sequence_no*rd_no*supplier_reference*fabric_composition_id*fabric_construction_id*brand_id*fabric_yarn_finish*updated_by*update_date*status_active*is_deleted";
			$data_array1="".$cbo_fabric_nature."*'".$txtconstruction." ".$cbo_construction_type."'*'".$txtconstruction."'*'".$fab_type_id."'*".$txtgsmweight."*".$cbocolortype."*".$stichlength."*'".$cbo_buyer_name."'*".$processloss."*".$txt_sequence."*".$txt_rd_number."*".$txt_supplier_reference."*".$fab_composition_id."*'".$fab_construction_id."'*'".$cbo_brand_id."'*'".$cbo_fabric_yarn_finish."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*'0'";
														//.$pc_date_time."'*".$cbo_status."*0";
			
			
			
			$rID_de1=execute_query( "delete from lib_yarn_count_determina_dtls where  mst_id =".$update_mst_id."",0);
			
			$id_dtls=return_next_id( "id", "lib_yarn_count_determina_dtls", 1 ) ;
			
			//echo "shajjad";die;
			
			$field_array2= "id, mst_id, copmposition_id, percent, count_id, type_id, yarn_rate,yarn_finish,yarn_spinning_system,certification,yarn_color_id,yarn_code, inserted_by, insert_date, status_active, is_deleted";
			for ($i=1;$i<=$total_row;$i++)
			{
				$cbocompone="cbocompone_".$i;
				$percentone="percentone_".$i;
				$cbocountcotton="cbocountcotton_".$i;
				$cbotypecotton="cbotypecotton_".$i;
				$txtyarnrate="txtyarnrate_".$i;
				$updateid="updateid_".$i;
				$cbo_yarn_finish="cboyarnfinish_".$i;
				$cbo_yarn_spinning_system="cboyarnspinningsystem_".$i;
				$cbocertification="cbocertification_".$i;
				$cboyarncolor="cboyarncolor_".$i;
				$txtyarncode="txtyarncode_".$i;
				if ($i!=1) $data_array2 .=",";
				$data_array2 .="(".$id_dtls.",".$update_mst_id.",".$$cbocompone.",".$$percentone.",".$$cbocountcotton.",".$$cbotypecotton.",".$$txtyarnrate.",'".str_replace("'", "", $$cbo_yarn_finish)."','".str_replace("'", "", $$cbo_yarn_spinning_system)."','".str_replace("'", "", $$cbocertification)."','".str_replace("'", "", $$cboyarncolor)."','".str_replace("'", "", $$txtyarncode)."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
				$id_dtls=$id_dtls+1;
			}
			
			//echo "INSERT INTO lib_yarn_count_determina_mst(".$field_array1.") VALUES ".$data_array1;die;
			
			$rID=sql_update("lib_yarn_count_determina_mst",$field_array1,$data_array1,"id","".$update_mst_id."",0);
			$rID_1=sql_insert("lib_yarn_count_determina_dtls",$field_array2,$data_array2,1);
			
			
			
			//check_table_status( $_SESSION['menu_id'],0);
			if($db_type==0)
			{
				if($rID && $rID_1 && $rID_de1){
					mysql_query("COMMIT");  
					echo "1**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID."**".$data_array1;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				
				if($rID && $rID_1 && $rID_de1)
				{
					oci_commit($con);  
					echo "1**".$rID;
				}
				else{
					oci_rollback($con); 
					echo "10**".$rID."**".$data_array1."**test";
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
		function js_set_value(id,name)
		{
			document.getElementById('hidfabcompid').value=id;
			document.getElementById('hidfabcompname').value=name;
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
                        </th>
	                </thead>
                    </table>
                    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="430" class="rpt_table" id="fab_comp_tbl">
                    <tbody>

                    <? 
                    $fabric_composition = return_library_array("select id, fabric_composition_name from  lib_fabric_composition where status_active=1 and is_deleted=0 order by fabric_composition_name", "id", "fabric_composition_name");
                    $i=1; 
                    foreach($fabric_composition as $id=>$fab_comp_name) 
                    { 
                    	if($i%2==0) $bgcolor="#E9F3FF"; 
                    	else $bgcolor="#FFFFFF"; 
                    	?>
                    	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $id; ?>,'<? echo $fab_comp_name; ?>')">
                            <td width="30"><? echo $i; ?></td>
                            <td><? echo $fab_comp_name; ?> </td> 						
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

if($action=="composition_popup")
{
	echo load_html_head_contents("Order Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);	
	
	?>
	<script>
		function js_set_value(id,name,type)
		{
			document.getElementById('hidcompid').value=id;
			document.getElementById('hidcompname').value=name;
			document.getElementById('count_type').value=type;
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
		<fieldset style="width:630px;margin-left:10px">
	        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="630" class="rpt_table">
	                <thead>
	                    <th width="30">SL</th>
	                    <th width="160">Composition
                        	<input type="hidden" name="hidcompid" id="hidcompid" value="" >
                            <input type="hidden" name="hidcompname" id="hidcompname" value="" >
                            <input type="hidden" name="count_type" id="count_type" value="" >
                        </th>
                        <th width="100">Yarn Fibre Type</th>
                        <th width="100">Yarn Fibre </th>
                        <th width="100">Yarn Type</th>
                        <th >Yarn Spinning System </th>
                        
	                </thead>
                    </table>
                    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="630" class="rpt_table" id="comp_tbl">
                    <tbody>

                    <? 
                    $sql="SELECT id,composition_name,yarn_spinning_system ,yarn_type, yarn_fibre,yarn_fibre_type from lib_composition_array where status_active=1";
                    $result=sql_select($sql);
                    $i=1; 
                    foreach($result as $row) 
                    { 
                    	$id=$row[csf('id')];
                    	$comp_name=$row[csf('composition_name')];
                    	$yarn_type=$row[csf('yarn_type')];
                    	if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; ?>
                    	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $id; ?>,'<? echo $comp_name; ?>',<? echo $yarn_type; ?>)">
                            <td width="30"><? echo $i; ?></td>
                            <td width="160"><p><? echo $comp_name; ?></p> </td> 						
                            <td width="100"><p><? echo $yarn_fibre_type_arr[$row[csf('yarn_fibre_type')]]; ?></p> </td> 						
                            <td width="100"><p><? echo $yarn_fibre_arr[$row[csf('yarn_fibre')]]; ?></p> </td> 						
                            <td width="100"><p><? echo $yarn_type_for_entry[$row[csf('yarn_type')]]; ?></p> </td> 						
                            <td><p><? echo $yarn_spinning_system_arr[$row[csf('yarn_spinning_system')]]; ?></p> </td> 						
                           			
                        </tr>
	                    <? $i++; 
	                } ?>
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

if($action=="count_popup")
{
	echo load_html_head_contents("Order Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);	
	
	?>
	<script>
		function js_set_value(id,name,type)
		{
			document.getElementById('count_id').value=id;
			document.getElementById('count_name').value=name;
			document.getElementById('count_type').value=type;
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
		<fieldset style="width:450px;margin-left:10px">
	        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="440" class="rpt_table">
	                <thead>
	                    <th width="30">SL</th>
	                    <th width="110">Yarn Count
                        	<input type="hidden" name="count_id" id="count_id" value="" >
                            <input type="hidden" name="count_name" id="count_name" value="" >
                            <input type="hidden" name="count_type" id="count_type" value="" >
                        </th>
                       
                        
                        <th width="70">Count System</th>
                        <th width="80">Number Of Filament</th>
                        <th >Sequence No</th>

	                </thead>
                    </table>
                    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="440" class="rpt_table" id="comp_tbl">
                    <tbody>

                    <? 

                    	//$arr=array (1=>$yarn_color_arr,2=>$yarn_fibre_type_arr,3=>$yarn_fibre_arr,4=>$count_system_arr,5=>$number_of_filament_arr,6=>$yarn_type_for_entry,8=>$yarn_finish_arr,9=>$yarn_spinning_system_arr,10=>$row_status);

                    $sql="select id,yarn_count,sequence_no,status_active,yarn_spinning_system,yarn_finish,yarn_color_code,yarn_type,number_of_filament,count_system,yarn_fibre,yarn_fibre_type,yarn_color from lib_yarn_count where is_deleted=0";
                    $count_res=sql_select($sql);
                    $i=1; 
                    foreach($count_res as $row) 
                    { 
                    	$yarn_count=$row[csf('yarn_count')];
                    	$yarn_type_id=$row[csf('yarn_type')];
                    	$id=$row[csf('id')];
                    	if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
                    	if($id==$pre_count_id) $bgcolor="#FFFF00";
                    	?>

                    	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $id; ?>,'<? echo $yarn_count; ?>','<? echo $yarn_type_id; ?>')">
                            <td width="30"><? echo $i; ?></td>
                            <td width="110"><? echo $yarn_count; ?> </td> 						
                            						
                           	
                            <td width="70"><? echo $count_system_arr[$row[csf('count_system')]]; ?> </td> 						
                            <td width="80"><? echo $number_of_filament_arr[$row[csf('number_of_filament')]]; ?> </td> 						
                            <td ><? echo$row[csf('sequence_no')]; ?> </td> 						
                        </tr>
                    	<? $i++; 
               		} 
                	?>
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

if($action=="fabric_type_popup")
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
	                    <th>Fabric Type
                        	<input type="hidden" name="hidfabconspid" id="hidfabconspid" value="" style="width:50px">
                            <input type="hidden" name="hidfabconsname" id="hidfabconsname" value="" style="width:50px">
                        </th>
	                </thead>
                    </table>
                    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="430" class="rpt_table" id="fab_cons_tbl">
                    <tbody>

                    <? 
                    $fabric_construction = return_library_array("select id, fabric_type_name from  lib_fabric_type where status_active=1 and is_deleted=0 order by fabric_type_name", "id", "fabric_type_name");
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
?>