<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 140, "select location_name,id from lib_location where company_id='$data' and is_deleted=0  and status_active=1  order by location_name",'id,location_name', 1, '--- Select Location ---', 0, "load_drop_down( 'requires/machine_name_entry_controller', this.value, 'load_drop_down_floor', 'floor' )"  );
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_name", 140, "select floor_name,id from  lib_prod_floor where location_id='$data' and is_deleted=0  and status_active=1  order by floor_name",'id,floor_name', 1, '--- Select Floor ---', 0, '' );
	exit();
}

if ($action=="machine_entry_list_view")
{
	$location_name=return_library_array( "select location_name,id from  lib_location where is_deleted=0", "id", "location_name"  );
	$floor=return_library_array( "select floor_name,id from  lib_prod_floor where is_deleted=0", "id", "floor_name"  );
	$lib_supplier_name=return_library_array( "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 and a.is_deleted=0 group by a.id, a.supplier_name order by a.supplier_name", "id", "supplier_name"  );

	if ($data=="" || $data==0) $com=""; else $com=" and company_id='$data'";
	$sql="SELECT location_id, floor_id, machine_no, category_id, machine_group, dia_width, gauge, id, seq_no, is_subcon, prod_capacity, efficiency, no_of_feeder,dyeing_mc_type,party_id from lib_machine_name where is_deleted=0 $com order by seq_no desc";
	$arr=array(0=>$location_name,1=>$floor, 3=>$machine_category, 8=>$yes_no, 12=>$dyeing_mcTypeArr, 13=>$lib_supplier_name);  
	
	echo  create_list_view ( "list_view", "Location Name,Floor Name,Machine No,Category,Machine Group,Dia Width,Gauge,Seq. No,SubCon,Prod. Capacity,Effi%,No of feeder/Tube,Dyeing M/C Type,Party", "100,100,100,100,80,60,60,60,50,50,50,100,100","1180","220",1, $sql, "get_php_form_data", "id","'load_php_data_to_form'", 1, "location_id,floor_id,0,category_id,0,0,0,0,is_subcon,0,0,0,dyeing_mc_type,party_id", $arr , "location_id,floor_id,machine_no,category_id,machine_group,dia_width,gauge,seq_no,is_subcon,prod_capacity,efficiency,no_of_feeder,dyeing_mc_type,party_id", "requires/machine_name_entry_controller", 'setFilterGrid("list_view",-1);','') ;
	exit();
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select company_id, location_id, floor_id, machine_no, category_id, machine_group,machine_group_id,fabric_machine_group, dia_width, gauge, extra_cylinder, no_of_feeder, attachment, prod_capacity, capacity_uom_id, machine_capacity, brand, origin, purchase_date, purchase_cost, over_head_cost, accumulated_dep, depreciation_rate, depreciation_method_id, remark, seq_no, machine_type, is_subcon, efficiency, status_active, id, norsel_weight_api, norsel_printer, norsel_printer_api,pipe_weight,cycle_time,dyeing_mc_type,party_id from lib_machine_name where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "load_drop_down( 'requires/machine_name_entry_controller', '".($inf[csf("company_id")])."', 'load_drop_down_location', 'location' );\n";
		echo "load_drop_down( 'requires/machine_name_entry_controller', '".($inf[csf("location_id")])."', 'load_drop_down_floor', 'floor' );\n";
		
		echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_id")])."';\n";    
		echo "document.getElementById('cbo_location_name').value  = '".($inf[csf("location_id")])."';\n"; 
		echo "document.getElementById('cbo_floor_name').value  = '".($inf[csf("floor_id")])."';\n";
		echo "document.getElementById('txt_machine_no').value  = '".($inf[csf("machine_no")])."';\n";
		echo "document.getElementById('cbo_catagory').value  = '".($inf[csf("category_id")])."';\n";
		echo "document.getElementById('txt_group').value  = '".($inf[csf("machine_group")])."';\n";
		echo "document.getElementById('txt_fab_group').value  = '".($inf[csf("fabric_machine_group")])."';\n";
		echo "document.getElementById('fab_group_hid_id').value  = '".($inf[csf("machine_group_id")])."';\n";
		echo "document.getElementById('txt_dia_width').value  = '".($inf[csf("dia_width")])."';\n";
		echo "document.getElementById('txt_gauge').value  = '".($inf[csf("gauge")])."';\n";
		echo "document.getElementById('txt_extra_cylinder').value  = '".($inf[csf("extra_cylinder")])."';\n"; 
		echo "document.getElementById('txt_no_of_feeder').value  = '".($inf[csf("no_of_feeder")])."';\n";
		echo "document.getElementById('txt_attachment').value  = '".($inf[csf("attachment")])."';\n";
		echo "document.getElementById('txt_prod_capacity').value  = '".($inf[csf("prod_capacity")])."';\n";
		echo "document.getElementById('txt_efficiency').value  = '".($inf[csf("efficiency")])."';\n";
		echo "document.getElementById('cbo_capacity_uom').value  = '".($inf[csf("capacity_uom_id")])."';\n";
		echo "document.getElementById('txt_machine_capacity').value  = '".($inf[csf("machine_capacity")])."';\n";
		echo "document.getElementById('txt_brand').value  = '".($inf[csf("brand")])."';\n";
		echo "document.getElementById('txt_origin').value  = '".($inf[csf("origin")])."';\n";
		echo "document.getElementById('txt_purchase_date').value  = '".(change_date_format($inf[csf("purchase_date")]))."';\n";
		echo "document.getElementById('txt_purchase_cost').value  = '".($inf[csf("purchase_cost")])."';\n";
		//echo "document.getElementById('txt_over_head_cost').value  = '".($inf[csf("over_head_cost")])."';\n";
		echo "document.getElementById('txt_accumulated_dep').value  = '".($inf[csf("accumulated_dep")])."';\n";
		echo "document.getElementById('txt_depreciation_rate').value  = '".($inf[csf("depreciation_rate")])."';\n";
		echo "document.getElementById('cbo_depreciation_method').value  = '".($inf[csf("depreciation_method_id")])."';\n";
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('txt_remarks').value  = '".($inf[csf("remark")])."';\n";
		echo "document.getElementById('cbo_mc_type').value  = '".($inf[csf("dyeing_mc_type")])."';\n";
		echo "document.getElementById('txt_seq_no').value  = '".($inf[csf("seq_no")])."';\n";
		echo "document.getElementById('cbo_machinetype').value  = '".($inf[csf("machine_type")])."';\n";
		echo "document.getElementById('cbo_isSubcon').value  = '".($inf[csf("is_subcon")])."';\n";
		echo "document.getElementById('txt_norsel_weight_api').value  = '".($inf[csf("norsel_weight_api")])."';\n";
		echo "document.getElementById('txt_norsel_printer').value  = '".($inf[csf("norsel_printer")])."';\n";
		echo "document.getElementById('txt_norsel_printer_api').value  = '".($inf[csf("norsel_printer_api")])."';\n";
		echo "document.getElementById('txt_pipe_weight').value  = '".($inf[csf("pipe_weight")])."';\n";
		echo "document.getElementById('txt_cycle_time').value  	= '".($inf[csf("cycle_time")])."';\n";
		echo "document.getElementById('cbo_supplier').value  	= '".($inf[csf("party_id")])."';\n";
		if($inf[csf("is_subcon")]==1)
		{
			echo "document.getElementById('cbo_supplier').disabled=false;\n";
		}
		else
		{
			echo "document.getElementById('cbo_supplier').disabled=true;\n";	
		}
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n";  
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_machine_name_entry',1);\n";
	}
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{

		if($cbo_isSubcon==1)
		{
			if (is_duplicate_field( "machine_no", "lib_machine_name", " machine_no=$txt_machine_no and company_id=$cbo_company_name and location_id=$cbo_location_name and party_id=$cbo_supplier and is_deleted=0" ) == 1)
			{
				echo "11**0"; die;
			}
		}

		if (is_duplicate_field( "machine_no", "lib_machine_name", " machine_no=$txt_machine_no and company_id=$cbo_company_name and location_id=$cbo_location_name and floor_id=$cbo_floor_name and is_deleted=0" ) == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			     
			$id=return_next_id( "id", " lib_machine_name", 1 ) ;
			$field_array="id, company_id, location_id, floor_id, machine_no, category_id, machine_group,fabric_machine_group,machine_group_id, dia_width, pipe_weight, gauge, extra_cylinder, no_of_feeder, attachment, prod_capacity, capacity_uom_id, brand, origin, purchase_date, purchase_cost, accumulated_dep, depreciation_rate, depreciation_method_id, remark,dyeing_mc_type, seq_no, machine_type, is_subcon, efficiency, norsel_weight_api, norsel_printer, norsel_printer_api, cycle_time, machine_capacity,party_id, inserted_by, insert_date, status_active, is_deleted";
			$data_array="(".$id.",".$cbo_company_name.",".$cbo_location_name.",".$cbo_floor_name.",".$txt_machine_no.",".$cbo_catagory.",".$txt_group.",".$txt_fab_group.",".$fab_group_hid_id.",".$txt_dia_width.",".$txt_pipe_weight.",".$txt_gauge.",".$txt_extra_cylinder.",".$txt_no_of_feeder.",".$txt_attachment.",".$txt_prod_capacity.",".$cbo_capacity_uom.",".$txt_brand.",".$txt_origin.",".$txt_purchase_date.",".$txt_purchase_cost.",".$txt_accumulated_dep.",".$txt_depreciation_rate.",".$cbo_depreciation_method.",".$txt_remarks.",".$cbo_mc_type.",".$txt_seq_no.",".$cbo_machinetype.",".$cbo_isSubcon.",".$txt_efficiency.",".$txt_norsel_weight_api.",".$txt_norsel_printer.",".$txt_norsel_printer_api.",".$txt_cycle_time.",".$txt_machine_capacity.",".$cbo_supplier.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";
			//echo "10**0**insert into lib_machine_name (".$field_array.") values ".$data_array;die;
			$rID=sql_insert("lib_machine_name",$field_array,$data_array,1);
			if($db_type==0)
			{
				if($rID ){
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
				if($rID )
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
	}
	else if ($operation==1)   // Update Here
	{

		if($cbo_isSubcon==1)
		{
			if (is_duplicate_field( "machine_no", "lib_machine_name", " machine_no=$txt_machine_no and company_id=$cbo_company_name and id!=$update_id and location_id=$cbo_location_name and party_id=$cbo_supplier and is_deleted=0" ) == 1)
			{
				echo "11**0"; die;
			}
		}


		if (is_duplicate_field( "machine_no", "lib_machine_name", " machine_no=$txt_machine_no and company_id=$cbo_company_name and id!=$update_id and location_id=$cbo_location_name and floor_id=$cbo_floor_name and is_deleted=0" ) == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			$field_array="company_id*location_id*floor_id*machine_no*category_id*machine_group*fabric_machine_group*machine_group_id*dia_width*pipe_weight*gauge*extra_cylinder*no_of_feeder*attachment*prod_capacity*capacity_uom_id*brand*origin*purchase_date*purchase_cost*accumulated_dep*depreciation_rate*depreciation_method_id*remark*dyeing_mc_type*seq_no*machine_type*is_subcon*efficiency*norsel_weight_api*norsel_printer*norsel_printer_api*cycle_time* machine_capacity*party_id*updated_by*update_date*status_active*is_deleted";
			$data_array="".$cbo_company_name."*".$cbo_location_name."*".$cbo_floor_name."*".$txt_machine_no."*".$cbo_catagory."*".$txt_group."*".$txt_fab_group."*".$fab_group_hid_id."*".$txt_dia_width."*".$txt_pipe_weight."*".$txt_gauge."*".$txt_extra_cylinder."*".$txt_no_of_feeder."*".$txt_attachment."*".$txt_prod_capacity."*".$cbo_capacity_uom."*".$txt_brand."*".$txt_origin."*".$txt_purchase_date."*".$txt_purchase_cost."*".$txt_accumulated_dep."*".$txt_depreciation_rate."*".$cbo_depreciation_method."*".$txt_remarks."*".$cbo_mc_type."*".$txt_seq_no."*".$cbo_machinetype."*".$cbo_isSubcon."*".$txt_efficiency."*".$txt_norsel_weight_api."*".$txt_norsel_printer."*".$txt_norsel_printer_api."*".$txt_cycle_time."*".$txt_machine_capacity."*".$cbo_supplier."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0";
			 
			$rID=sql_update("lib_machine_name",$field_array,$data_array,"id","".$update_id."",1);
			 
			if($db_type==0)
			{
				if($rID ){
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
			    if($rID )
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
		}
	}
	else if ($operation==2)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("lib_machine_name",$field_array,$data_array,"id","".$update_id."",1);
		
		if($db_type==0)
		{
			if($rID ){
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
			if($rID )
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

if ($action=="mccpm_popup")
{
	echo load_html_head_contents("M/C CPM Entry Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//$permission=$_SESSION['page_permission'];
	?> 
	<script>
		var permission='<? echo $permission; ?>';
			
		function add_break_down_tr(i) 
		{
			var row_num=$('#tbl_list_search tbody tr').length;
			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;
				$("#tbl_list_search tr:last").clone().find("input,select").each(function() {
					$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { return name + i },
					'value': function(_, value) { return value }              
					});  
				}).end().appendTo("#tbl_list_search");
				$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
				$('#txtapplicabledate_'+i).removeAttr("class").attr("class","datepicker");
				
				$('#txtapplicabledate_'+i).val("");
				set_all_onclick();
				$('#txtCpm_'+i).val("");
				$('#rowid_'+i).val("");
			}
		}
		
		function fn_deletebreak_down_tr(rowNo) 
		{   
			var numRow = $('table#tbl_list_search tbody tr').length; 
			if(rowNo!=1)
			{
				var permission_array=permission.split("_");
				var rowid=$('#rowid_'+rowNo).val();
				if(rowid !="" && permission_array[2]==1)
				{
					var booking=return_global_ajax_value(rowid, 'delete_row', '', 'machine_name_entry_controller');
				}
				var index=rowNo-1
				$('#tbl_list_search tbody tr:eq('+index+')').remove();
				var numRow = $('table#tbl_list_search tbody tr').length; 
				for(i = rowNo;i <= numRow;i++)
				{
					$("#size_color_break_down_list tr:eq("+i+")").find("input,select").each(function() {
						$(this).attr({
							'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
							//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
							'value': function(_, value) { return value }             
						}); 
					})
				}
			}
		}
			
		function fnc_mccpm_entry( operation )
		{
			var row_num = $('table#tbl_list_search tbody tr').length; 
			var data_all='&hid_mc_id='+document.getElementById('hid_mc_id').value;
			for (var i=1; i<=row_num; i++)
			{
				if (form_validation('txtapplicabledate_'+i+'*txtCpm_'+i,'Applicable Date*CPM')==false)
				{
					return;
				}
				data_all=data_all+get_submitted_data_string('txtapplicabledate_'+i+'*txtCpm_'+i+'*rowid_'+i,"../../../",i);
			}
			
			var data="action=save_update_delete_mccpm&operation="+operation+'&total_row='+row_num+data_all;
			//alert(data);
			//return;
			freeze_window(operation);
			http.open("POST","machine_name_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_mccpm_entry_reponse;
		}

		function fnc_mccpm_entry_reponse()
		{
			if(http.readyState == 4) 
			{
				var reponse=trim(http.responseText).split('**');
				if (reponse[0].length>2) reponse[0]=10;
				release_freezing();
				if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
				{
					parent.emailwindow.hide();
				}
			}
		}
    </script>
	</head>
	<body>
    <div align="center">
    <? echo load_freeze_divs ("../../../",$permission); ?>
	<fieldset style="width:300px">
		<form id="mccpminfo_1" autocomplete="off">
			<table width="300" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
				<thead>
					<th width="100" class="must_entry_caption">Applicable Date</th>
					<th width="70" class="must_entry_caption">CPM</th>
					<th>&nbsp;</th>
				</thead>
				<tbody>
				<?
				$data_array=sql_select("select id, applicable_date, cpm from lib_machine_cpm where mst_id=$update_id and status_active=1 and is_deleted=0");
				if(count($data_array)>0)
				{
					$i=1;
					foreach( $data_array as $row)
					{
				?>
					<tr class="general" id="tr_<?=$i; ?>">
						<td>
							<input type="hidden" id="rowid_<?=$i;?>" name="rowid_<?=$i;?>" class="text_boxes" style="width:40px" value="<?=$row[csf('id')] ; ?>" />
							<input type="text" id="txtapplicabledate_<?=$i; ?>" name="txtapplicabledate_<?=$i; ?>" class="datepicker" style="width:90px" value="<?=change_date_format($row[csf('applicable_date')]); ?>" />
						</td>
						<td><input type="text" id="txtCpm_<?=$i;?>" name="txtCpm_<?=$i;?>" class="text_boxes_numeric" style="width:70px" value="<?=$row[csf('cpm')] ; ?>"/></td>
						<td>&nbsp;
							<input type="button" id="increase_<?=$i;?>" name="increase_<?=$i;?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<?=$i;?>)" /> &nbsp;&nbsp;
							<input type="button" id="decrease_<?=$i;?>" name="decrease_<?=$i;?>" style="width:30px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(<?=$i;?>);" />
						</td>
					</tr>
					<?
					$i++;
					}
				}
				else
				{
					?>
					<tr class="general" id="tr_1">
						<td>
							<input type="hidden" id="rowid_1" name="rowid_1" class="text_boxes" style="width:40px" value="" />
							<input type="text" id="txtapplicabledate_1" name="txtapplicabledate_1" class="datepicker" style="width:90px" value="" />
						</td>
						<td><input type="text" id="txtCpm_1" name="txtCpm_1" class="text_boxes_numeric" style="width:70px" value=""/></td>
						<td>&nbsp;
							<input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(1);" />&nbsp;&nbsp;
							<input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(1);;" />
						</td>
					</tr>
					<?
				}
				?>
				</tbody>
			</table>
			<div align="center" style="margin-top:10px">
			<?
			if(count($data_array)>0)
				{
					echo load_submit_buttons( $permission, "fnc_mccpm_entry", 1,0 ,"reset_form('mccpminfo_1','','','','')",1) ; 
				}
				else
				{
					echo load_submit_buttons( $permission, "fnc_mccpm_entry", 0,0 ,"reset_form('mccpminfo_1','','','','')",1) ; 
				}
			?>
				<input type="hidden" id="hid_mc_id" value="<?=$update_id; ?>" />
			</div>
		</form>
	</fieldset>
    </div>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </body>           
    </html>
    <?
    exit();
}
if($action=="save_update_delete_mccpm")
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
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die;}		
		$id=return_next_id( "id", "lib_machine_cpm", 1 ) ;
		$field_array="id,mst_id,applicable_date,cpm,inserted_by,insert_date,status_active,is_deleted";
		for ($i=1;$i<=$total_row;$i++)
		{
			$txtapplicabledate="txtapplicabledate_".$i;
			$txtCpm="txtCpm_".$i;
			$rowid="rowid_".$i;
			
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",'".$hid_mc_id."',".$$txtapplicabledate.",".$$txtCpm.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$id=$id+1;
		}
		$rID=sql_insert("lib_machine_cpm",$field_array,$data_array,1);
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0";
			}
			else{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die;}

		 $add_comma=0;
		 $id=return_next_id( "id", "lib_machine_cpm", 1 ) ;
		 $field_array="id,mst_id,applicable_date,cpm,inserted_by,insert_date,status_active,is_deleted";
		 $field_array_up="applicable_date*cpm*updated_by*update_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $txtapplicabledate="txtapplicabledate_".$i;
			 $txtCpm="txtCpm_".$i;
			 $rowid="rowid_".$i;
			
			 if(str_replace("'",'',$$rowid)!="")
			 {
				 $id_arr[]=str_replace("'",'',$$rowid);
				 $data_array_up[str_replace("'",'',$$rowid)] =explode("*",("".$$txtapplicabledate."*".$$txtCpm."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			 }
             else if(str_replace("'",'',$$rowid)=="")
			 {
				if ($add_comma!=0) $data_array .=",";
				$data_array .="(".$id.",'".$hid_mc_id."',".$$txtapplicabledate.",".$$txtCpm.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$add_comma++;
				$id=$id+1;
			 }
		 }
		// echo bulk_update_sql_statement( "wo_po_acc_po_info", "id", $field_array_up, $data_array_up, $id_arr );
		 $rID=execute_query(bulk_update_sql_statement( "lib_machine_cpm", "id", $field_array_up, $data_array_up, $id_arr ));
		 if($data_array !="")
		 {
			$rID=sql_insert("lib_machine_cpm",$field_array,$data_array,1);
		 }
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "1";
			}
			else{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die;}

		 $field_array_up="status_active*is_deleted*updated_by*update_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $txtapplicabledate="txtapplicabledate_".$i;
			 $txtCpm="txtCpm_".$i;
			 $rowid="rowid_".$i;
			 if(str_replace("'",'',$$rowid)!="")
			 {
				 $id_arr[]=str_replace("'",'',$$rowid);
				 $data_array_up[str_replace("'",'',$$rowid)] =explode("*",("0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			 }
             
		 }
		// echo bulk_update_sql_statement( "wo_po_acc_po_info", "id", $field_array_up, $data_array_up, $id_arr );
		$rID=execute_query(bulk_update_sql_statement( "lib_machine_cpm", "id", $field_array_up, $data_array_up, $id_arr ));
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "2";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			
			if($rID ){
				oci_commit($con);
				echo "2";
			}
			else{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="delete_row")
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	$rID=execute_query("update lib_machine_cpm set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where id=$data");
	if($db_type==0)
	{
		if($rID ){
			mysql_query("COMMIT");
			echo "2";
		}
		else{
			mysql_query("ROLLBACK");
			echo "10";
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID ){
			oci_commit($con);
			echo "2";
		}
		else{
			oci_rollback($con);
			echo "10";
		}
	}
	disconnect($con);
	die;
}
if($action=="fabric_group_popup")
{
	echo load_html_head_contents("Fabric Group Info","../../../", 1, 1, '','1','');
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
?>