<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 130, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}

if ($action=="load_drop_down_season_conf")
{
	echo create_drop_down( "cbo_season_id", 100, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}

if ($action=="load_drop_down_sub_dep")
{
	echo create_drop_down( "cbo_subDept_id", 130, "select id, sub_department_name from lib_pro_sub_deparatment where buyer_id=$data and status_active=1 and is_deleted=0 order by sub_department_name","id,sub_department_name", 1, "-- Select Dept.--", $selected, "" );
	exit();
}

if ($action=="load_drop_down_sub_depConf")
{
	echo create_drop_down( "cbo_subDept_id", 100, "select id, sub_department_name from lib_pro_sub_deparatment where buyer_id=$data and status_active=1 and is_deleted=0 order by sub_department_name","id,sub_department_name", 1, "-- Select Dept.--", $selected, "" );
	exit();
}

if ($action=="load_drop_down_revise_no")
{
	$ex_data=explode('__',$data);
	if($ex_data[0]!='')
		$select=$ex_data[2];
	else
		$select=$selected;
	$sql=sql_select("select revise_no from qc_mst where cost_sheet_no='$ex_data[0]' and option_id='$ex_data[1]' order by revise_no Desc");
	$rvs=array();
	foreach($sql as $rows)
	{
		if($max=="") $max=$rows[csf("revise_no")];
		$rvs[$rows[csf("revise_no")]]=$rows[csf("revise_no")];
	}
	//echo "select revise_no from qc_mst where cost_sheet_no='$ex_data[0]' and option_id='$ex_data[1]' order by revise_no Desc";	
	echo create_drop_down( "cbo_revise_no", 45, $rvs ,"", 0, "-0-", $max, "fnc_option_rev( this.value+'***'+document.getElementById('cbo_option_id').value );" );
	exit();
}

if ($action=="load_drop_down_option_id")
{
	$ex_data=explode('__',$data);
	if($ex_data[0]!='')
		$selectopt=$ex_data[1];
	else
		$selectopt=$selected;
		//load_drop_down('requires/quick_costing_controller', '".$row[csf("cost_sheet_no")].'__'.$row[csf("option_id")].'__'.$row[csf("revise_no")]."', 'load_drop_down_revise_no', 'revise_td');
	if ($db_type==0) $option_name_cond="concat(option_id,'-',option_remarks)";
	else if ($db_type==2) $option_name_cond="option_id || '-' || option_remarks";
	
	$sql="select option_id, $option_name_cond as option_name from qc_mst where cost_sheet_no='$ex_data[0]' group by option_id, option_remarks order by option_id Desc";
	//echo $sql;
	echo create_drop_down( "cbo_option_id", 45, $sql,"option_id,option_name", 0, "-0-", $selectopt, "load_drop_down( 'requires/quick_costing_controller', document.getElementById('txt_costSheetNo').value+'__'+this.value+'__'+0, 'load_drop_down_revise_no', 'revise_td'); fnc_option_rev( document.getElementById('cbo_revise_no').value+'***'+this.value );" );//
	exit();
}

if($action=="template_popup")
{
	echo load_html_head_contents("Template Info","../../../", 1, 1, '','1','');
	extract($_REQUEST); 
	$permission=$_SESSION['page_permission'];
	?>
    <script>
	var permission='<? echo $permission; ?>';
	function add_break_down_tr( i )
	{
		var row_num=$('#tbl_tempCreat tr').length;
		//alert(row_num)
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			$("#tbl_tempCreat tbody tr:last").clone().find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { return name },
					'value': function(_, value) { return value }              
				});
			}).end().appendTo("#tbl_tempCreat tbody");
			
			$("#tbl_tempCreat tbody tr:last").removeAttr('id').attr('id','tr_'+i);
			$('#tr_'+i).find("td:eq(0)").text( i );
			
			$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+")");
			$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_delete_tr("+i+",'tbl_tempCreat')");
			$('#txtBtsRatio_'+i).val('');
			$('#txtBtmRatio_'+i).val(''); 
			$('#txtrowid_'+i).val('');
			set_all_onclick(); 
		}
	}

	function fn_delete_tr(rowNo,table_id) 
	{   
		if(table_id=='tbl_tempCreat')
		{
			var numRow = $('table#tbl_tempCreat tbody tr').length; 
			if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_tempCreat tbody tr:last').remove();
			}
		}
	}

	function js_set_value()
	{
		parent.emailwindow.hide();
	}
	
	function fnc_template_entry( operation )
	{
		/*if( form_validation('cbo_company_id*cbo_location_name*txt_bill_date*cbo_supplier_company*cbo_bill_for*txt_receive_date_1*txt_challenno_1','Company Name*Location*Bill Date*supplier company*bill for*receive date*challen no')==false)
		{ 
			return;
		}
		else
		{*/
		var tot_row=$('#tbl_tempCreat tr').length;
		var all_data='';
		for(var i=1; i<=tot_row; i++)
		{
			all_data+=get_submitted_data_string('cboItemBts_'+i+'*txtBtsRatio_'+i+'*cboItemBtm_'+i+'*txtBtmRatio_'+i+'*cboItemBts_'+i+'*txtrowid_'+i+'*txttmpid_'+i,"../../../",i);
		}
		//alert(all_data);
		var data="action=save_update_delete_tamplete&operation="+operation+'&tot_row='+tot_row+all_data;
		freeze_window(operation);
		http.open("POST","quick_costing_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_template_entry_reponse;
/*		}
*/	}

	function fnc_template_entry_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			release_freezing();
			if(reponse[0]==0 || reponse[0]==1)
			{
				show_msg(trim(reponse[0]));
				$('#tbl_tempCreat tr:not(:first)').remove();
				//parent.emailwindow.hide();
				set_button_status(0, permission, 'fnc_template_entry',1);
				show_list_view( '','template_list_view','save_up_list_view','quick_costing_controller','setFilterGrid(\'tbl_upListView\',-1)');
			}
		}
	}
	
	function get_temp_data(temp_id)
	{
		var list_view_grid = return_global_ajax_value( temp_id, 'load_php_dtls_form', '', 'quick_costing_controller');
		if(list_view_grid!='')
		{
			$("#tbl_tempCreat tbody tr").remove();
			$("#tbl_tempCreat tbody").append( list_view_grid );
			set_button_status(1, permission, 'fnc_template_entry',1);
		}
	}
    </script>
    <body onLoad="set_hotkey();">
    <div style="display:none"><? echo load_freeze_divs ("../../../",$permission); ?></div>
    <form name="tmpcreatfrm_1"  id="tmpcreatfrm_1" autocomplete="off">
        <table cellspacing="0" cellpadding="0" width="720" >
        <tr><td width="370" valign="top">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="352" class="rpt_table">
                <thead>
                    <tr>
                        <th colspan="6" align="center">Create Template</th>
                    </tr>
                    <tr>
                        <th width="20">SL</th>
                        <th width="70">Item</th>
                        <th width="53">Ratio</th>
                        <th width="70">Item</th>
                        <th width="53">Ratio</th>
                        <th><input type="hidden" name="template_break_data" id="template_break_data" value="" /></th>
                    </tr>
                </thead>
              </table>
              <div style="width:370px; overflow-y:scroll; max-height:220px;" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="352" class="rpt_table" id="tbl_tempCreat" >
                	<tbody id="tbd_temp">
                        <tr id="tr_1">
                            <td width="20">1</td>
                            <td width="70"><input style="width:40px;" type="hidden" id="txtrowid_1" /><input style="width:40px;" type="hidden" id="txttmpid_1" />
                            <? echo create_drop_down( "cboItemBts_1", 70, $qc_template_item_arr,"", 0, "-Select-", 1, "", "", "", "", "", "", "", "", "cboItemBts[]" ); ?></td>
                            <td width="53"><input style="width:40px;" type="text" class="text_boxes_numeric" name="txtBtsRatio[]" id="txtBtsRatio_1" /></td>
                            <td width="70"><? echo create_drop_down( "cboItemBtm_1", 70, $qc_template_item_arr,"", 0, "-Select-", 2, "", "", "", "", "", "", "", "", "cboItemBtm[]" ); ?></td>
                            <td width="53"><input style="width:40px;" type="text" class="text_boxes_numeric" name="txtBtmRatio[]" id="txtBtmRatio_1" /></td>
                            <td><input type="button" id="increaseset_1" style="width:23px" class="formbutton" value="+" onClick="add_break_down_tr(1)" />
                                <input type="button" id="decreaseset_1" style="width:23px" class="formbutton" value="-" onClick="javascript:fn_delete_tr(1 ,'tbl_tempCreat');"/></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <table width="370" cellspacing="0" border="0" height="40">
                <tr>
                    <td align="center" class="button_container">
						<? echo load_submit_buttons($permission,"fnc_template_entry",0,0,"reset_form('tmpcreatfrm_1','save_up_list_view','','','$(\'#tbl_tempCreat tr:not(:first)\').remove();')",1); ?> </td> 
                </tr>
                <tr><td align="center"><input type="button" id="btn" style="width:70px" class="formbutton" value="Close" onClick="js_set_value();" /></td></tr>
            </table>
        </td><td align="center" valign="top"><div id="save_up_list_view"></div></td></tr>
        </table>
    </form>
    </body>
    <script>
	show_list_view( '','template_list_view','save_up_list_view','quick_costing_controller','setFilterGrid(\'tbl_upListView\',-1)');
	</script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
exit();
}

if($action=='save_update_delete_tamplete')
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
		$template_item_library=return_library_array( "select id, item_name from lib_qc_template", "id", "item_name" );
		//echo "0**".$tot_row;
		$rowid=return_next_id( "id", "qc_template", 1);
		$tempid=return_next_id( "temp_id", "qc_template", 1);
		$data_arr="";
		$field_arr="id, temp_id, item_id1, ratio1, item_id2, ratio2, lib_item_id, inserted_by, insert_date";
		$m=1; $n=1; 
		for ($i=1;$i<=$tot_row;$i++)
		{
			$itemBts="cboItemBts_".$i;
			$btsRatio="txtBtsRatio_".$i;
			$itemBtm="cboItemBtm_".$i;
			$btmRatio="txtBtmRatio_".$i;
			$update_id="txtrowid_".$i;
			$bts_ratio=''; $btm_ratio='';
			
			for($j=1; $j<=str_replace("'","",$$btsRatio); $j++)
			{
				if($bts_ratio=='') $bts_ratio=$qc_template_item_arr[str_replace("'","",$$itemBts)].$m; else $bts_ratio.=','.$qc_template_item_arr[str_replace("'","",$$itemBts)].$m;
				$m++;
			}
			
			for($k=1; $k<=str_replace("'","",$$btmRatio); $k++)
			{
				if($btm_ratio=='') $btm_ratio=$qc_template_item_arr[str_replace("'","",$$itemBtm)].$n; else $btm_ratio.=','.$qc_template_item_arr[str_replace("'","",$$itemBtm)].$n;
				$n++;
			}
			$item_data='';
			if($bts_ratio!='' && $btm_ratio=='') $item_data=$bts_ratio;
			else if($bts_ratio=='' && $btm_ratio!='') $item_data=$btm_ratio;
			else $item_data=$bts_ratio.','.$btm_ratio;
			
			$ex_item_data='';
			$ex_item_data=explode(',',$item_data); 
			$s=0; $libTemp_id=''; $add_comma=0;
			for ($l=1; $l<=count($ex_item_data); $l++)
			{
				if($ex_item_data[$s]!="")
				{
					if (!in_array( $ex_item_data[$s], $new_array_tmpItem))
					{
						if ($add_comma!=0) $libTemp_id .=",";
						$libTemp_id.=return_id( $ex_item_data[$s], $template_item_library, "lib_qc_template", "id,item_name");  
						$new_array_tmpItem[$libTemp_id]=$ex_item_data[$s];
					}
					else
					{
						if ($add_comma!=0) $libTemp_id .=",";
						$libTemp_id.=array_search( $ex_item_data[$s], $new_array_tmpItem);
					}
					$add_comma++;
				}
				else
				{
					$libTemp_id=0;
				}
				$s++;
				
			}
			if ($i!=1) $data_arr .=",";
			$data_arr .="(".$rowid.",".$tempid.",".$$itemBts.",".$$btsRatio.",".$$itemBtm.",".$$btmRatio.",'".$libTemp_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$rowid=$rowid+1;
		}//die;
		$rID=sql_insert("qc_template",$field_arr,$data_arr,1);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**";
			}
			else{
				oci_rollback($con);
				echo "10**";
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
		$template_item_library=return_library_array( "select id, item_name from lib_qc_template", "id", "item_name" );
		//echo "select temp_id from qc_template where id=".$txtrowid_1.""; die;
		$previous_temp_id=return_field_value("temp_id","qc_template","id=".$txtrowid_1."","temp_id");
		$sql_id=sql_select("Select id from qc_template where temp_id='$previous_temp_id'");
		$row_id_arr=array();
		foreach( $sql_id as $row )
		{
			$row_id_arr[]=$row[csf('id')];
		}
		//print_r($row_id_arr); die;
		$rowid=return_next_id( "id", "qc_template", 1);
		//$tempid=return_next_id( "temp_id", "qc_template", 1);
		$data_arr="";
		$field_arr="id, temp_id, item_id1, ratio1, item_id2, ratio2, lib_item_id, inserted_by, insert_date";
		$field_arr_up="item_id1*ratio1*item_id2*ratio2*lib_item_id*updated_by*update_date";
		$m=1; $n=1;
		for ($i=1;$i<=$tot_row;$i++)
		{
			$itemBts="cboItemBts_".$i;
			$btsRatio="txtBtsRatio_".$i;
			$itemBtm="cboItemBtm_".$i;
			$btmRatio="txtBtmRatio_".$i;
			$update_id="txtrowid_".$i;
			$tempid="txttmpid_".$i;
			
			$bts_ratio=''; $btm_ratio='';
			
			for($j=1; $j<=str_replace("'","",$$btsRatio); $j++)
			{
				if($bts_ratio=='') $bts_ratio=$qc_template_item_arr[str_replace("'","",$$itemBts)].$m; else $bts_ratio.=','.$qc_template_item_arr[str_replace("'","",$$itemBts)].$m;
				$m++;
			}
			
			for($k=1; $k<=str_replace("'","",$$btmRatio); $k++)
			{
				if($btm_ratio=='') $btm_ratio=$qc_template_item_arr[str_replace("'","",$$itemBtm)].$n; else $btm_ratio.=','.$qc_template_item_arr[str_replace("'","",$$itemBtm)].$n;
				$n++;
			}
			$item_data='';
			if($bts_ratio!='' && $btm_ratio=='') $item_data=$bts_ratio;
			else if($bts_ratio=='' && $btm_ratio!='') $item_data=$btm_ratio;
			else $item_data=$bts_ratio.','.$btm_ratio;
			
			$ex_item_data='';
			$ex_item_data=explode(',',$item_data); 
			$s=0; $libTemp_id=''; $add_comma=0;
			for ($l=1; $l<=count($ex_item_data); $l++)
			{
				if($ex_item_data[$s]!="")
				{
					if (!in_array( $ex_item_data[$s], $new_array_tmpItem))
					{
						if ($add_comma!=0) $libTemp_id .=",";
						$libTemp_id.=return_id( $ex_item_data[$s], $template_item_library, "lib_qc_template", "id,item_name");  
						$new_array_tmpItem[$libTemp_id]=$ex_item_data[$s];
					}
					else
					{
						if ($add_comma!=0) $libTemp_id .=",";
						$libTemp_id.=array_search( $ex_item_data[$s], $new_array_tmpItem);
					}
					$add_comma++;
				}
				else
				{
					$libTemp_id=0;
				}
				$s++;
			}
			
			if(str_replace("'",'',$$update_id)=="")
			{
				if ($i!=1) $data_arr .=",";
				$data_arr .="(".$rowid.",".$$tempid.",".$$itemBts.",".$$btsRatio.",".$$itemBtm.",".$$btmRatio.",'".$libTemp_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$rowid=$rowid+1;
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$update_id);
				$data_arr_up[str_replace("'",'',$$update_id)] =explode("*",("".$$itemBts."*".$$btsRatio."*".$$itemBtm."*".$$btmRatio."*'".$libTemp_id."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
			}
		}
		//echo "0**".$data_arr; die;
		$flag=1;
		if($data_arr!="")
		{
			$rID=sql_insert("qc_template",$field_arr,$data_arr,1);
			if($rID) $flag=1; else $flag=0;
		}
		//echo bulk_update_sql_statement("qc_template", "id",$field_arr_up,$data_arr_up,$id_arr ); die;
		if($data_arr_up!="")
		{
			//echo bulk_update_sql_statement("qc_template", "id",$field_arr_up,$data_arr_up,$id_arr );
			$rID1=execute_query(bulk_update_sql_statement("qc_template", "id",$field_arr_up,$data_arr_up,$id_arr ));
			if($rID1) $flag=1; else $flag=0;
		}
		
		if(implode(',',$id_arr)!="")
		{
			$distance_delete_id=array_diff($row_id_arr,$id_arr);
		}
		else
		{
			$distance_delete_id=$row_id_arr;
		}
		$field_array_del="status_active*is_deleted*updated_by*update_date";
		$data_array_del="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//print_r ($distance_delete_id);
		if(implode(',',$distance_delete_id)!="")
		{
			foreach($distance_delete_id as $id_val)
			{
				$rID5=sql_update("qc_template",$field_array_del,$data_array_del,"id","".$id_val."",1);
				if($rID5) $flag=1; else $flag=0;
			}
		}
		
		if($db_type==0)
		{
			if($flag==1 ){
				mysql_query("COMMIT");  
				echo "0**";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1 ){
				oci_commit($con);
				echo "0**";
			}
			else{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}  // Update End
}

if($action=="template_list_view")
{
	?>
    <div style="width:370px;" align="center">
    <legend>Update Template List</legend>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="370" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="50">Temp ID</th>
                <th>Template Name</th>
            </thead>
     	</table>
        <div style="width:370px; overflow-y:scroll; max-height:220px;" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="352" class="rpt_table" id="tbl_upListView" >
            
            <?
				$sql_tmp="select temp_id, item_id1, ratio1, item_id2, ratio2, status_active from qc_template where status_active=1 and is_deleted=0 order by id ASC";
				$sql_tmp_res=sql_select($sql_tmp);
				$mst_temp_arr=array();
				foreach($sql_tmp_res as $row)
				{
					$mst_temp_arr[$row[csf('temp_id')]].=$row[csf('item_id1')].'**'.$row[csf('ratio1')].'**'.$row[csf('item_id2')].'**'.$row[csf('ratio2')].'**'.$row[csf('status_active')].'__';
				}
				//print_r($mst_temp_arr);
				$i=1; 
				foreach($mst_temp_arr as $temp_id=>$tmp_data)
				{
					$m=1; $n=1;
					$template_data=array_filter(explode('__',$tmp_data));
					$template_name='';
					foreach($template_data as $temp_val)
					{
						$ex_tmp_val=explode('**',$temp_val);
						$item_id1=''; $ratio1=0; $item_id2=''; $ratio2=0;
						
						$item_id1=$ex_tmp_val[0]; 
						$ratio1=$ex_tmp_val[1]; 
						$item_id2=$ex_tmp_val[2]; 
						$ratio2=$ex_tmp_val[3];
						
						$bts_ratio='';
						for($j=1; $j<=$ratio1; $j++)
						{
							if($bts_ratio=='') $bts_ratio=$qc_template_item_arr[$item_id1].$m; else $bts_ratio.=','.$qc_template_item_arr[$item_id1].$m;
							$m++;
						}
						$btm_ratio='';
						for($k=1; $k<=$ratio2; $k++)
						{
							if($btm_ratio=='') $btm_ratio=$qc_template_item_arr[$item_id2].$n; else $btm_ratio.=','.$qc_template_item_arr[$item_id2].$n;
							$n++;
						}
						if($template_name=='')
						{
							if( $ratio1!='' && $ratio2=='' )
							{
								$template_name=$bts_ratio;
							}
							else if( $ratio2!='' && $ratio1=='' )
							{
								$template_name=$btm_ratio;
							}
							else if( $ratio2!='' && $ratio1!='' )
							{
								$template_name=$bts_ratio.','.$btm_ratio;
							}
						}
						else
						{
							if( $ratio1!='' && $ratio2=='' )
							{
								$template_name.=','.$bts_ratio;
							}
							else if( $ratio2!='' && $ratio1=='' )
							{
								$template_name.=','.$btm_ratio;
							}
							else if( $ratio2!='' && $ratio1!='' )
							{
								$template_name.=','.$bts_ratio.','.$btm_ratio;
							}
						}
					}
					if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="get_temp_data('<? echo $temp_id; ?>');"> 
                        <td width="30" align="center"><? echo $i; ?></td>
                        <td width="50" align="center"><? echo $temp_id; ?></td>
                        <td><div style="word-wrap:break-word; width:250px"><? echo $template_name; ?></div></td>
                    </tr>
                    <?
					$i++;
				}
			?>
            </table>
        </div>
     </div>
    <?
	exit();
}

if($action=="load_drop_template_name")
{
	$lib_temp_arr=return_library_array("select id, item_name from lib_qc_template","id","item_name");
	if($db_type==0) $concat_cond="group_concat(lib_item_id)";
	else if($db_type==2) $concat_cond="listagg(cast(lib_item_id as varchar2(4000)),',') within group (order by lib_item_id)";
	else $concat_cond="";
	$sql_tmp="select temp_id, $concat_cond as lib_item_id from qc_template where status_active=1 and is_deleted=0 group by temp_id order by temp_id ASC";
	$sql_tmp_res=sql_select($sql_tmp);
	//print_r($sql_tmp_res);die;
	$template_name_arr=array();
	foreach($sql_tmp_res as $row)
	{
		$lib_temp_id='';
		
		$ex_temp_id=explode(',',$row[csf('lib_item_id')]);
		foreach($ex_temp_id as $lib_id)
		{
			if($lib_temp_id=="") $lib_temp_id=$lib_temp_arr[$lib_id]; else $lib_temp_id.=','.$lib_temp_arr[$lib_id];
		}
		
		$template_name_arr[$row[csf('temp_id')]]=$lib_temp_id;
	}
	
	echo create_drop_down( "cbo_temp_id", 130, $template_name_arr,'', 1, "-Select Template-",$selected, "fnc_template_view();" ); 
	exit();
}

if($action=="load_php_dtls_form")
{
	$sql_tmp="select id, temp_id, item_id1, ratio1, item_id2, ratio2, status_active from qc_template where temp_id='$data' and status_active=1 and is_deleted=0 order by id ASC";
	$sql_tmp_res=sql_select($sql_tmp); $i=1; $j=1;
	foreach($sql_tmp_res as $row)
	{
		?>
        <tr id="tr_<? echo $i; ?>">
            <td width="20"><? echo $i; ?></td>
            <td width="70"><input style="width:40px;" type="hidden" id="txtrowid_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>" />
            <input style="width:40px;" type="hidden" id="txttmpid_<? echo $i; ?>" value="<? echo $row[csf('temp_id')]; ?>" />
                <? echo create_drop_down( "cboItemBts_$i", 70, $qc_template_item_arr,"", 0, "-Select-", $row[csf('item_id1')], "", "", "", "", "", "", "", "", "cboItemBts[]" ); ?></td>
            <td width="53"><input style="width:40px;" type="text" class="text_boxes_numeric" name="txtBtsRatio[]" id="txtBtsRatio_<? echo $i; ?>" value="<? echo $row[csf('ratio1')]; ?>" /></td>
            <td width="70"><? echo create_drop_down( "cboItemBtm_$i", 70, $qc_template_item_arr,"", 0, "-Select-", $row[csf('item_id2')], "", "", "", "", "", "", "", "", "cboItemBtm[]" ); ?></td>
            <td width="53"><input style="width:40px;" type="text" class="text_boxes_numeric" name="txtBtmRatio[]" id="txtBtmRatio_<? echo $i; ?>" value="<? echo $row[csf('ratio2')]; ?>" /></td>
            <td><input type="button" id="increaseset_<? echo $i; ?>" style="width:23px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?>)" />
                <input type="button" id="decreaseset_<? echo $i; ?>" style="width:23px" class="formbutton" value="-" onClick="javascript:fn_delete_tr(<? echo $i; ?> ,'tbl_tempCreat');"/></td>
        </tr>
        <?
		$i++;
	}
	exit();
}

if($action=='formulaBilder_popup')
{
	
}

if($action=="save_update_delete_meeting_minutes")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//echo $operation;
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$meeting_mst_id=return_next_id("id", "qc_meeting_mst", 1);
		
		$mst_field_arr="id, mst_id, style_ref, meeting_date, meeting_time, venue, inserted_by, insert_date, status_active, is_deleted";
		$mst_data_arr="(".$meeting_mst_id.",".$txt_costing_id.",".$txt_style_ref.",".$txt_meeting_date.",".$txt_meeting_time.",".$txt_meeting_venue.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		
		$meeting_person_id=return_next_id("id", "qc_meeting_person", 1);
		$person_field_arr="id, mst_id, dtls_id, name, organization, designation, inserted_by, insert_date, status_active, is_deleted";
		$add_comma_person=0; $person_data_arr=""; 
		for($i=1; $i<=$tot_participants; $i++)
		{
			$txtName='txtName_'.$i;
			$txtOrg='txtOrg_'.$i;
			$txtDesig='txtDesig_'.$i;
			
			if ($add_comma_person!=0) $person_data_arr .=",";
			$person_data_arr .="(".$meeting_person_id.",".$txt_costing_id.",".$meeting_mst_id.",".$$txtName.",".$$txtOrg.",".$$txtDesig.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			
			$meeting_person_id=$meeting_person_id+1;
			$add_comma_person++;
		}
		
		$meeting_dtls_id=return_next_id("id", "qc_meeting_dtls", 1);
		$dtls_field_arr="id, mst_id, dtls_id, particulars, inserted_by, insert_date, status_active, is_deleted";
		$add_comma=0; $dtls_data_arr=""; 
		for($j=1; $j<=$tot_particulars; $j++)
		{
			$txtParticulars='txtParticulars_'.$j;
			
			if ($add_comma!=0) $dtls_data_arr .=",";
			$dtls_data_arr .="(".$meeting_dtls_id.",".$txt_costing_id.",".$meeting_mst_id.",".$$txtParticulars.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			
			$meeting_dtls_id=$meeting_dtls_id+1;
			$add_comma++;
		}
		
		//echo "10**INSERT INTO qc_meeting_person (".$person_field_arr.") VALUES ".$person_data_arr; 
		//die;
		$flag=1;
		
		$rID=sql_insert("qc_meeting_mst",$mst_field_arr,$mst_data_arr,1);
		if($rID) $flag=1; else $flag=0;
		
		$rID1=sql_insert("qc_meeting_person",$person_field_arr,$person_data_arr,1);
		if($rID1) $flag=1; else $flag=0;
		
		$rID2=sql_insert("qc_meeting_dtls",$dtls_field_arr,$dtls_data_arr,1);
		if($rID2) $flag=1; else $flag=0;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$meeting_mst_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "0**".str_replace("'",'',$meeting_mst_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$mst_field_arr="style_ref*meeting_date*meeting_time*venue*updated_by*update_date";
		$mst_data_arr="".$txt_style_ref."*".$txt_meeting_date."*".$txt_meeting_time."*".$txt_meeting_venue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		execute_query( "delete from qc_meeting_person where  dtls_id =".$txt_up_id."",1);
		
		$meeting_person_id=return_next_id("id", "qc_meeting_person", 1);
		$person_field_arr="id, mst_id, dtls_id, name, organization, designation, inserted_by, insert_date, status_active, is_deleted";
		$add_comma_person=0; $person_data_arr=""; 
		for($i=1; $i<=$tot_participants; $i++)
		{
			$txtName='txtName_'.$i;
			$txtOrg='txtOrg_'.$i;
			$txtDesig='txtDesig_'.$i;
			
			if ($add_comma_person!=0) $person_data_arr .=",";
			$person_data_arr .="(".$meeting_person_id.",".$txt_costing_id.",".$meeting_mst_id.",".$$txtName.",".$$txtOrg.",".$$txtDesig.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			
			$meeting_person_id=$meeting_person_id+1;
			$add_comma_person++;
		}
		execute_query( "delete from qc_meeting_dtls where  dtls_id =".$txt_up_id."",1);
		$meeting_dtls_id=return_next_id("id", "qc_meeting_dtls", 1);
		$dtls_field_arr="id, mst_id, dtls_id, particulars, inserted_by, insert_date, status_active, is_deleted";
		$add_comma=0; $dtls_data_arr=""; 
		for($j=1; $j<=$tot_particulars; $j++)
		{
			$txtParticulars='txtParticulars_'.$j;
			
			if ($add_comma!=0) $dtls_data_arr .=",";
			$dtls_data_arr .="(".$meeting_dtls_id.",".$txt_costing_id.",".$meeting_mst_id.",".$$txtParticulars.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			
			$meeting_dtls_id=$meeting_dtls_id+1;
			$add_comma++;
		}
		
		$flag=1;
		
		$rID=sql_update("qc_meeting_mst",$mst_field_arr,$mst_data_arr,"id","".$txt_up_id."",1);
		if($rID) $flag=1; else $flag=0;
		
		$rID1=sql_insert("qc_meeting_person",$person_field_arr,$person_data_arr,1);
		if($rID1) $flag=1; else $flag=0;
		
		$rID2=sql_insert("qc_meeting_dtls",$dtls_field_arr,$dtls_data_arr,1);
		if($rID2) $flag=1; else $flag=0;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_up_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'",'',$txt_up_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="rate_details_popup")
{
	echo load_html_head_contents("Rate Details Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
    <script>
		function fnc_yarn_cost_percent_check(id)
		{
			var yarn_costper1=$('#txt_yarn_costPer1').val()*1;
			var yarn_costper2=$('#txt_yarn_costPer2').val()*1;
			var yarn_costper3=$('#txt_yarn_costPer3').val()*1;
			var tot_yarn_cost_per=yarn_costper1+yarn_costper2+yarn_costper3;
			var balance_cost=0;
			if(tot_yarn_cost_per>100)
			{
				alert('Percentage grater then 100.');
				$('#txt_yarn_costPer'+id).val('');
			}
			/*else if(tot_yarn_cost_per<100)
			{
				if(yarn_costper1==0)
				alert('Percentage Must total 100.');
				$('#txt_yarn_costPer'+id).val('');
			}*/
			else
			{
				balance_cost=100-tot_yarn_cost_per;
				if(yarn_costper1==0)
				{
					$('#txt_yarn_costPer1').val( balance_cost );
				}
				else if(yarn_costper2==0)
				{
					$('#txt_yarn_costPer2').val( balance_cost );
				}
				else if(yarn_costper3==0)
				{
					$('#txt_yarn_costPer3').val( balance_cost );
				}
			}
			var ratePerKg=0; var yarn_percent=0; var yarn_cost=0;
			ratePerKg=($('#txt_ratePerKg'+id).val()*1);
			yarn_percent=($('#txt_yarn_costPer'+id).val()*1)/100;
			yarn_cost=ratePerKg*yarn_percent;
			$('#txt_yarn_cost'+id).val( number_format(yarn_cost,4) );
			fnc_total_rate();
		}
		
		function fnc_total_rate()
		{
			var yarn_cost1=$('#txt_yarn_cost1').val()*1;
			var yarn_cost2=$('#txt_yarn_cost2').val()*1;
			var yarn_cost3=$('#txt_yarn_cost3').val()*1;
			
			var tot_yarn_cost=yarn_cost1+yarn_cost2+yarn_cost3;
			
			$('#txt_tot_yarn_cost').val( number_format(tot_yarn_cost,4) );
			
			var knit_cost=$('#txt_knit_charge').val()*1;
			var dye_cost=$('#txt_dyeing_charge').val()*1;
			var aop_cost=$('#txt_aop_charge').val()*1;
			var fini_cost=$('#txt_fin_charge').val()*1;
			var other_cost=$('#txt_other_cost').val()*1;
			
			var tot_cost=knit_cost+dye_cost+aop_cost+fini_cost+tot_yarn_cost+other_cost;
			$('#txt_tot_cost').val( number_format(tot_cost,4));
			
			var process_lossPer=($('#txt_process_loss_per').val()*1)/100;
			processLoss_cost=((tot_cost/(1-process_lossPer))-tot_cost);
			$('#txt_process_loss').val( number_format(processLoss_cost,4) );
			
			var process_loss=$('#txt_process_loss').val()*1;
			
			var tot_rate=tot_cost+process_loss;
			
			$('#txt_tot_rate').val( number_format(tot_rate,4));
			
			var all_data="";
			
			all_data=$('#txt_yarn_dtls1').val()+'~~'+$('#txt_ratePerKg1').val()+'~~'+$('#txt_yarn_costPer1').val()+'~~'+$('#txt_yarn_cost1').val()+'~~'+$('#txt_yarn_dtls2').val()+'~~'+$('#txt_ratePerKg2').val()+'~~'+$('#txt_yarn_costPer2').val()+'~~'+$('#txt_yarn_cost2').val()+'~~'+$('#txt_yarn_dtls3').val()+'~~'+$('#txt_ratePerKg3').val()+'~~'+$('#txt_yarn_costPer3').val()+'~~'+$('#txt_yarn_cost3').val()+'~~'+$('#txt_tot_yarn_costPer').val()+'~~'+$('#txt_tot_yarn_cost').val()+'~~'+$('#txt_knit_charge').val()+'~~'+$('#txt_dyeing_charge').val()+'~~'+$('#txt_aop_charge').val()+'~~'+$('#txt_fin_charge').val()+'~~'+$('#txt_other_cost').val()+'~~'+$('#txt_tot_cost').val()+'~~'+$('#txt_process_loss_per').val()+'~~'+$('#txt_process_loss').val()+'~~'+$('#txt_tot_rate').val();
			
			$('#hidden_all_data').val( all_data );
		}
		
		function js_set_value()
		{
			fnc_total_rate();
			document.getElementById('txt_tot_rate').value;
			document.getElementById('hidden_all_data').value;
			parent.emailwindow.hide();
		}
		
		
		function fnc_assign_all_data()
		{
			var all_data=$('#hidden_all_data').val();
			var exData=all_data.split("~~");
			$('#txt_yarn_dtls1').val(exData[0]);
			$('#txt_ratePerKg1').val(exData[1]);
			$('#txt_yarn_costPer1').val(exData[2]);
			$('#txt_yarn_cost1').val(exData[3]);
			$('#txt_yarn_dtls2').val(exData[4]);
			$('#txt_ratePerKg2').val(exData[5]);
			$('#txt_yarn_costPer2').val(exData[6]);
			$('#txt_yarn_cost2').val(exData[7]);
			$('#txt_yarn_dtls3').val(exData[8]);
			$('#txt_ratePerKg3').val(exData[9]);
			$('#txt_yarn_costPer3').val(exData[10]);
			$('#txt_yarn_cost3').val(exData[11]);
			$('#txt_tot_yarn_costPer').val(exData[12]);
			$('#txt_tot_yarn_cost').val(exData[13]);
			$('#txt_knit_charge').val(exData[14]);
			$('#txt_dyeing_charge').val(exData[15]);
			$('#txt_aop_charge').val(exData[16]);
			$('#txt_fin_charge').val(exData[17]);
			$('#txt_other_cost').val(exData[18]);
			$('#txt_tot_cost').val(exData[19]);
			$('#txt_process_loss_per').val(exData[20]);
			$('#txt_process_loss').val(exData[21]);
			$('#txt_tot_rate').val(exData[22]);
		}
		
	</script>
	</head>
	<body>
    <div id="rate_details"  align="center">            
        <form name="rateDetails_1" id="rateDetails_1" autocomplete="off">
            <table width="360" cellspacing="0" border="1" class="rpt_table" id="tbl_rateDetails" rules="all">
            	<thead>
                	<th width="160">Details</th>
                    <th width="70">Rate Per KG</th>
                	<th width="65"> % </th>
                    <th>Cost</th>
                </thead>
                <tr>
                    <td><input style="width:147px;" type="text" class="text_boxes" name="txt_yarn_dtls1" id="txt_yarn_dtls1" placeholder="Yarn Cost 1" onBlur="fnc_total_rate();" /><input type="hidden" class="text_boxes" name="hidden_all_data" id="hidden_all_data" value="<? echo $rateData; ?>"/></td>
                    <td><input style="width:57px;" type="text" class="text_boxes_numeric" name="txt_ratePerKg1" id="txt_ratePerKg1" placeholder="Rate 1" onBlur="fnc_yarn_cost_percent_check(1);" /></td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_yarn_costPer1" id="txt_yarn_costPer1" onBlur="fnc_yarn_cost_percent_check(1);" /></td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_yarn_cost1" id="txt_yarn_cost1" onBlur="fnc_total_rate();" /></td>
                </tr>
                <tr>
                    <td><input style="width:147px;" type="text" class="text_boxes" name="txt_yarn_dtls2" id="txt_yarn_dtls2" placeholder="Yarn Cost 2" onBlur="fnc_total_rate();" /></td>
                    <td><input style="width:57px;" type="text" class="text_boxes_numeric" name="txt_ratePerKg2" id="txt_ratePerKg2" placeholder="Rate 2" onBlur="fnc_yarn_cost_percent_check(2);" /></td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_yarn_costPer2" id="txt_yarn_costPer2" onBlur="fnc_yarn_cost_percent_check(2);" /></td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_yarn_cost2" id="txt_yarn_cost2" onBlur="fnc_total_rate();" /></td>
                </tr>
                <tr>
                    <td><input style="width:147px;" type="text" class="text_boxes" name="txt_yarn_dtls3" id="txt_yarn_dtls3" placeholder="Yarn Cost 3" /></td>
                    <td><input style="width:57px;" type="text" class="text_boxes_numeric" name="txt_ratePerKg3" id="txt_ratePerKg3" placeholder="Rate 3" onBlur="fnc_yarn_cost_percent_check(3);" /></td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_yarn_costPer3" id="txt_yarn_costPer3" onBlur="fnc_yarn_cost_percent_check(3);" /></td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_yarn_cost3" id="txt_yarn_cost3" onBlur="fnc_total_rate();" /></td>
                </tr>
                <tr bgcolor="#CCCCAA">
                    <td colspan="2">Total Yarn Cost</td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_tot_yarn_costPer" id="txt_tot_yarn_costPer" value="100" disabled /></td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_tot_yarn_cost" id="txt_tot_yarn_cost" disabled /></td>
                </tr>
                <tr>
                    <td colspan="3">Knitting Charge</td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_knit_charge" id="txt_knit_charge" onBlur="fnc_total_rate();" /></td>
                </tr>
                <tr>
                    <td colspan="3">Dyeing Charge</td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_dyeing_charge" id="txt_dyeing_charge" onBlur="fnc_total_rate();" /></td>
                </tr>
                <tr>
                    <td colspan="3">AOP Charge</td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_aop_charge" id="txt_aop_charge" onBlur="fnc_total_rate();" /></td>
                </tr>
                <tr>
                    <td colspan="3">Finishing Charge</td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_fin_charge" id="txt_fin_charge" onBlur="fnc_total_rate();" /></td>
                </tr>
                <tr>
                    <td colspan="3">Other Cost</td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_other_cost" id="txt_other_cost" onBlur="fnc_total_rate();" /></td>
                </tr>
                <tr bgcolor="#CCCCAA">
                    <td colspan="3">Total Cost</td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_tot_cost" id="txt_tot_cost" disabled /></td>
                </tr>
                <tr>
                    <td  colspan="2">Process Loss</td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_process_loss_per" id="txt_process_loss_per" onBlur="fnc_total_rate();" /></td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_process_loss" id="txt_process_loss" onBlur="fnc_total_rate();" /></td>
                </tr>
                <tr bgcolor="#FF66FF">
                    <td colspan="3">Total Rate</td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_tot_rate" id="txt_tot_rate" disabled /></td>
                </tr>
            </table>
            <table width="360" cellspacing="0" class="" border="0">
                <tr>
                    <td align="center" width="100%" class="button_container"><input type="button" class="formbutton" value="Close" onClick="js_set_value()"/></td> 
                </tr>
            </table>
        </form>
    </div>
    </body>
    <script>fnc_assign_all_data();</script>      
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();	
}

if($action=="stage_saveUpdate_popup")
{
	echo load_html_head_contents("Stage Entry/Update PopUp","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
    <script>
		
		var permission='<? echo $permission; ?>'; 
		
		function fnc_stage_entry( operation )
		{
			if(form_validation('txt_stage_name','Stage Name')==false)
			{
				return;   
			}
			else
			{
				var data="action=save_update_delete_stage&operation="+operation+get_submitted_data_string('update_id*txt_stage_name',"../../",2);
				freeze_window(operation);
				http.open("POST","quick_costing_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_stage_entry_reponse;
			}
		}
		 
		function fnc_stage_entry_reponse()
		{
			if(http.readyState == 4) 
			{
				var reponse=http.responseText.split('**');
				
				if(reponse[0]==0 || reponse[0]==1)
				{
					$('#update_id').val(reponse[0]);
					set_button_status(1, permission, 'fnc_stage_entry',1);
					show_list_view('','stage_data_list','stage_data_div','quick_costing_controller','');
				}
			}
			release_freezing();
		}
		
		function js_set_value( )
		{
			parent.emailwindow.hide();
		}
		
	</script>
	</head>
	<body>
    <div id="stage_details" align="center">  
    <? echo load_freeze_divs ("../../../",$permission);  ?>          
        <form name="stageDetails_1" id="stageDetails_1" autocomplete="off">
            <table width="260" cellspacing="0" border="1" class="rpt_table" id="tbl_stageDetails" rules="all">
            	<thead>
                	<th class="must_entry_caption">Stage Name</th>
                </thead>
                <tbody>
                	<tr>
                    	<td><input style="width:240px;" type="text" class="text_boxes" name="txt_stage_name" id="txt_stage_name" />
                        	<input style="width:40px;" type="hidden" class="text_boxes" name="update_id" id="update_id" />
                        </td>
                    </tr>
                </tbody>
			</table>
            <table width="260" cellspacing="0" class="" border="0">
                <tr>
                    <td align="center" height="15" width="100%"></td>
                </tr>
                <tr>
                    <td align="center" width="100%" class="button_container">
						<?
                           echo load_submit_buttons( $permission, "fnc_stage_entry", 0,0 ,"reset_form('stageDetails_1','','','','')",1); 
                        ?><input type="button" class="formbutton" value="Close" onClick="js_set_value()"/>
                    </td> 
                </tr>
            </table>
            <div id="stage_data_div"></div>
        </form>
	</div>
    </body> 
    <script>show_list_view('','stage_data_list','stage_data_div','quick_costing_controller','');</script>          
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();	
}

if($action=="save_update_delete_stage")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=return_next_id("id", "lib_stage_name", 1);
		
		$field_array="id, stage_name, inserted_by, insert_date";
		$data_array="(".$id.",'".strtoupper(str_replace("'","",$txt_stage_name))."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$rID=sql_insert("lib_stage_name",$field_array,$data_array,1);
		
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
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);  
				echo "0**".$id;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="stage_name*updated_by*update_date";
		$data_array="'".strtoupper(str_replace("'","",$txt_stage_name))."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_update("lib_stage_name",$field_array,$data_array,"id","".$update_id."",1);
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "1**".$update_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);  
				echo "1**".$update_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="stage_data_list")
{
	echo  create_list_view ( "list_view", "Stage Name", "200","200","150",1, "select id, stage_name from lib_stage_name where status_active=1 and is_deleted=0 order by id desc", "get_php_form_data", "id","'load_php_data_to_form_satge'", 1, "0", $arr, "stage_name","quick_costing_controller", 'setFilterGrid("list_view",-1);','0' );
	exit();
}

if($action=="load_php_data_to_form_satge")
{
	$sql_arr=sql_select( "select id, stage_name from lib_stage_name where status_active=1 and is_deleted=0 and id='$data'" );
	foreach ($sql_arr as $inf)
	{
		echo "document.getElementById('txt_stage_name').value  			= '".$inf[csf("stage_name")]."';\n"; 
		echo "document.getElementById('update_id').value  				= '".$inf[csf("id")]."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_stage_entry',1);\n";  
	}
	exit();
}

if($action=="load_drop_stage_name")
{
	echo create_drop_down( "cbo_stage_id", 130,"select id, stage_name from lib_stage_name where status_active=1 and is_deleted=0","id,stage_name", 1, "--Select--", $selected, "" );
	exit();
}

if($action=="cpm_check_load")
{
	if($db_type==0) $date_change=change_date_format($data,"yyyy-mm-dd");
	else if($db_type==2) $date_change=change_date_format($data, "", "",1);
	//echo "select cost_per_minute from lib_standard_cm_entry where '$date_change' between applying_period_date and applying_period_to_date";
	
	$sql_arr=sql_select( "select cost_per_minute from lib_standard_cm_entry where '$date_change' between applying_period_date and applying_period_to_date" );
	
	if(count($sql_arr)>0)
		echo $sql_arr[0][csf('cost_per_minute')];
	else
		echo '0';	
	exit();
}

if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$flag=1; 
	if ($operation==0)  // Insert Here
	{	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//$update_id=str_replace("'","",$txt_update_id); //die;
		$dup_styleRef=str_replace("'","",$txt_styleRef);
		$dup_season_id=str_replace("'","",$cbo_season_id);
		$dup_buyer_id=str_replace("'","",$cbo_buyer_id);
		$msg="Duplicate Styel Ref, Buyer, Season.";
		if($type==1)
		{
			if (is_duplicate_field( "cost_sheet_no", "qc_mst", "style_ref='$dup_styleRef' and season_id='$dup_season_id' and buyer_id='$dup_buyer_id'" ) == 1)
			{
				echo "11**".$msg; 
				die;
			}
			$insert_user_id=return_field_value("inserted_by","qc_mst","id=".$txt_update_id."","inserted_by");
			$updated_by_user=return_field_value("updated_by","qc_mst","id=".$txt_update_id."","updated_by");
			$team_dtls_arr=array();
			//$team_dtls_sql=sql_select("Select a.user_tag_id as team_leader, b.user_tag_id as team_member from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and a.user_tag_id='$user_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			if( ($updated_by_user*1)==0 ) $updated_by_user=$user_id;
			
			/*$team_dtls_sql=sql_select("select b.user_tag_id as team_member from  lib_mkt_team_member_info b 
	where  b.team_id in (select id from lib_marketing_team a where user_tag_id='$user_id' and a.status_active=1 and a.is_deleted=0 ) and b.status_active=1 and b.is_deleted=0");
			if(count($team_dtls_sql)>1)
			{
				foreach($team_dtls_sql as $row)
				{
					$team_dtls_arr[$row[csf("team_member")]]=$user_id;
				}
			}
			else
			{
				if( $updated_by_user==$user_id )
					$team_dtls_arr[$user_id]=$user_id;
			}
			//print_r($team_dtls_arr);
			if($team_dtls_arr[$insert_user_id]=='')
			{
				echo "6**"."Update restricted, This Information is Update Only team Leader or member.";
				die;
			}*/
		}
		
		$mst_id=return_next_id("id", "qc_mst", 1);
		$revise_no=0; $update_id=0; $option_id=0;
		//echo "0**2**";
		if($type==6)
		{
			$option_id=str_replace("'","",$cbo_option_id);
			/*if($option_id==0) $option_id=1;
			else $option_id=$option_id;*/
			
			if(str_replace("'","",$txt_costSheetNo)!='' && str_replace("'","",$txt_update_id)!='')
			{
				$costSheetNo=str_replace("'","",$txt_costSheetNo);
				$sql_revise_no=sql_select("select max(revise_no) as revise_no from qc_mst where cost_sheet_no='$costSheetNo' and option_id='$option_id'");
				if($sql_revise_no[0][csf('revise_no')]=="") $revise_no=0;
				else $revise_no=$sql_revise_no[0][csf('revise_no')]+1;
			}
			else
			{
				$revise_no=0;
			}
			$autoCostSheetNo=str_replace("'","",$txt_costSheetNo);
			$update_id=str_replace("'","",$txt_update_id);
		}
		else if($type==7)
		{
			$option_id=str_replace("'","",$cbo_option_id);
			if(str_replace("'","",$txt_costSheetNo)!='' && str_replace("'","",$txt_update_id)!='')
			{
				$revise_no=0;
				$costSheetNo=str_replace("'","",$txt_costSheetNo);
				$sql_option_no=sql_select("select max(option_id) as option_id from qc_mst where cost_sheet_no='$costSheetNo'");
				if($sql_option_no[0][csf('option_id')]=="") $option_id=0;
				else $option_id=$sql_option_no[0][csf('option_id')]+1;
			}
			else
			{
				$revise_no=0;
				$option_id=0;
			}
			$autoCostSheetNo=str_replace("'","",$txt_costSheetNo);
			$update_id=str_replace("'","",$txt_update_id);
		}
		else
		{
			$option_id=0;
			$revise_no=0;
			$autoCostSheetNo=$_SESSION['logic_erp']['user_id'].str_pad($mst_id,8,'0',STR_PAD_LEFT); 
			$update_id=0;
		}
		$qcno=$_SESSION['logic_erp']['user_id'].$mst_id;
		//echo $revise_no."=select max(revise_no) as revise_no from qc_mst where cost_sheet_no='$txt_costSheetNo'"; die;
		$field_array_mst=" id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, style_ref, buyer_id, cons_basis, season_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, pre_cost_sheet_id, revise_no, option_id, buyer_remarks, option_remarks, qc_no, inserted_by, insert_date";
		
		$data_array_mst="(".$mst_id.",".$mst_id.",'".$autoCostSheetNo."',".$cbo_temp_id.",".$txt_temp_id.",'".strtoupper(str_replace("'","",$txt_styleRef))."',".$cbo_buyer_id.",".$cbo_cons_basis_id.",".$cbo_season_id.",'".strtoupper(str_replace("'","",$txt_styleDesc))."',".$cbo_subDept_id.",".$txt_delivery_date.",".$txt_exchangeRate.",".$txt_offerQty.",".$txt_quotedPrice.",".$txt_tgtPrice.",".$cbo_stage_id.",".$txt_costingDate.",'".$update_id."','".$revise_no."','".$option_id."','".strtoupper(str_replace("'","",$txt_costing_remarks))."','".strtoupper(str_replace("'","",$txt_option_remarks))."','".$qcno."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//echo $data_array; die;
		//$template_id=return_next_id("id", "qc_template", 1);
		//$template_id = return_id( $txt_temp_id, $template_library, "qc_template", "id,item_id");
		$dtls_id=return_next_id("id", "qc_fabric_dtls", 1);
		$temp_id=explode(",",str_replace("'","",$txt_temp_id));
		
		$field_array_fab="id, mst_id, item_id, uniq_id, body_part, des, value, alw, inserted_by, insert_date";
   		//$fstr_id=return_next_id("id", "qc_fabric_string_data", 1);
		$cons_rate_id=return_next_id("id", "qc_cons_rate_dtls", 1);
		$field_array_consrate="id, mst_id, item_id, type, particular_type_id, formula, consumption, ex_percent, is_calculation, rate, rate_data, value, inserted_by, insert_date";
		
		$add_comma_fab=0; $data_array_fab=""; $add_comma_cons_rate=0; $data_array_cons_rate="";
		foreach($temp_id as $item_id)
		{
			$item_id=trim($item_id);
			$itemBodyData='txt_itemBodyData_'.$item_id;
			$itemBodyData=explode('_',str_replace("'","",$$itemBodyData));
			
			if( $itemBodyData[0]=='' ) $itemBodyData=array(1,4,9,998,999);
			$m=1;
			//echo "10**<pre>";
			////print_r($itemBodyData);
			foreach($itemBodyData as $body_part_id)
			{
				$trLen=5;
				 
				for($j=1; $j<=$trLen; $j++)
				{
					$txtItemDes='txtItemDes_'.$item_id.'_'.$j.'_'.$m;
					$txtVal='txtVal_'.$item_id.'_'.$j.'_'.$m;
					$txtAw='txtAw_'.$item_id.'_'.$j.'_'.$m;
					$uniq_id=$item_id.'_'.$j.'_'.$m;
					if ($add_comma_fab!=0) $data_array_fab .=",";
					$data_array_fab .="(".$dtls_id.",".$mst_id.",'".$item_id."','".$uniq_id."','".$body_part_id."','".strtoupper(str_replace("'","",$$txtItemDes))."',".$$txtVal.",".$$txtAw.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					
					$dtls_id=$dtls_id+1;
					$add_comma_fab++;
				}
				$m++;
			}
			
			$itemConsRateData='txt_itemConsRateData_'.$item_id;
			$ex_itemConsRateData=array_filter(explode('##',str_replace("'","",$$itemConsRateData)));
			foreach($ex_itemConsRateData as $item_wise_consRateData)
			{
				if($item_wise_consRateData!="")
				{
					//echo $item_wise_consRateData.'==';
					$ex_itemConsRate=explode('_',$item_wise_consRateData);
					$head_id=$ex_itemConsRate[0];
					$cons=$ex_itemConsRate[1];
					$is_rate_cal=$ex_itemConsRate[2];
					$rate=$ex_itemConsRate[3];
					$rateData=$ex_itemConsRate[4];
					$exPer=$ex_itemConsRate[5];
					
					$tot_val=$cons*$rate;
					
					if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
					$data_array_cons_rate .="(".$cons_rate_id.",".$mst_id.",'".$item_id."',1,'".$head_id."',0,'".$cons."','".$exPer."','".$is_rate_cal."','".$rate."','".$rateData."','".$tot_val."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					
					$cons_rate_id=$cons_rate_id+1;
					$add_comma_cons_rate++;
				}
			}
			
			$specialData='txt_specialData_'.$item_id;
			$ex_specialData=array_filter(explode('##',str_replace("'","",$$specialData)));
			foreach($ex_specialData as $item_wise_specialData)
			{
				$ex_itemSpConsRate=explode('_',$item_wise_specialData);
				$speciaOperation_id=$ex_itemSpConsRate[0];
				$spConsumtion=$ex_itemSpConsRate[1];
				$spExper=$ex_itemSpConsRate[2];
				$spRate=$ex_itemSpConsRate[3];
				
				$tot_spVal=$spConsumtion*$spRate;
				
				if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
				$data_array_cons_rate .="(".$cons_rate_id.",".$mst_id.",'".$item_id."',2,'".$speciaOperation_id."',0,'".$spConsumtion."','".$spExper."','','".$spRate."','','".$tot_spVal."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$cons_rate_id=$cons_rate_id+1;
				$add_comma_cons_rate++;
			}
			
			$itemAccData='txt_itemAccData_'.$item_id;
			$ex_itemAccData=array_filter(explode('##',str_replace("'","",$$itemAccData)));
			foreach($ex_itemAccData as $item_wise_itemAccData)
			{
				$ex_itemAcConsRate=explode('_',$item_wise_itemAccData);
				$accessories_id=$ex_itemAcConsRate[0];
				$acConsumtion=$ex_itemAcConsRate[1];
				$acExPer=$ex_itemAcConsRate[2];
				$acRate=$ex_itemAcConsRate[3];
				
				$tot_acVal=$acConsumtion*$acRate;
				
				if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
				$data_array_cons_rate .="(".$cons_rate_id.",".$mst_id.",'".$item_id."',3,'".$accessories_id."',0,'".$acConsumtion."','".$acExPer."',0,'".$acRate."','','".$tot_acVal."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$cons_rate_id=$cons_rate_id+1;
				$add_comma_cons_rate++;
			}
		}
		//echo "10**";
		//echo $data_array_fab; 
		//die;
		$tc_sum_id=return_next_id("id", "qc_tot_cost_summary", 1);
		$field_array_tot_cost="id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, tot_rmg_ratio, inserted_by, insert_date";
		
		$ex_tot_cost_data=explode('_',$data_tot_cost_summ);
		
		$cbo_buyer_agent=trim($ex_tot_cost_data[0]);
		$cbo_agent_location=trim($ex_tot_cost_data[1]);
		$txt_noOfPack=trim($ex_tot_cost_data[2]);
		$cmPop=trim($ex_tot_cost_data[3]);
		//$txt_smv=trim($ex_tot_cost_data[4]);
		//$txt_eff=trim($ex_tot_cost_data[5]);
		$txt_lumSum_cost=trim($ex_tot_cost_data[4]);
		$txt_commPer=trim($ex_tot_cost_data[5]);
		$totFab_td=trim($ex_tot_cost_data[6]);
		$totSpc_td=trim($ex_tot_cost_data[7]);
		$totAcc_td=trim($ex_tot_cost_data[8]);
		$totCm_td=trim($ex_tot_cost_data[9]);
		$totFriCst_td=trim($ex_tot_cost_data[10]);
		$totLbTstCst_td=trim($ex_tot_cost_data[11]);
		$totMissCst_td=trim($ex_tot_cost_data[12]);
		$totOtherCst_td=trim($ex_tot_cost_data[13]);
		$totCommCst_td=trim($ex_tot_cost_data[14]);
		$totCost_td=trim($ex_tot_cost_data[15]);
		$totalFob_td=trim($ex_tot_cost_data[16]);
		$totRmgQty_td=trim($ex_tot_cost_data[17]);
		
		$data_array_tot_cost="(".$tc_sum_id.",".$mst_id.",'".$cbo_buyer_agent."','".$cbo_agent_location."','".$txt_noOfPack."',0,'".$cmPop."','".$txt_lumSum_cost."','".$txt_commPer."','".$totFab_td."','".$totSpc_td."','".$totAcc_td."','".$totCm_td."','".$totFriCst_td."','".$totLbTstCst_td."','".$totMissCst_td."','".$totOtherCst_td."','".$totCommCst_td."','".$totCost_td."','".$totalFob_td."','".$totRmgQty_td."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//echo $data_array_tot_cost; die;
		$field_array_item_tot=" id, mst_id, item_id, fabric_cost, sp_operation_cost, accessories_cost, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs, rmg_ratio, inserted_by, insert_date";
		$item_cost_id=return_next_id("id", "qc_item_cost_summary", 1);
		$ex_item_wise_tot_data=array_filter(explode('__',str_replace("'","",$item_wise_tot_data)));
		$add_comma_item_tot=0; $data_array_item_tot="";
		foreach($ex_item_wise_tot_data as $item_wise_tot)
		{
			$fab_td=$spOpe_td=$acc_td=$txtSmv=$txtEff=$txtCmCost=$txtFriCost=$txtLtstCost=$txtMissCost=$txtOtherCost=$txtCommCost=$fobT_td=$tot_item_id=0;
			
			$ex_item_tot=explode('_',$item_wise_tot);
			$fab_td=trim($ex_item_tot[0]);
			$spOpe_td=trim($ex_item_tot[1]);
			$acc_td=trim($ex_item_tot[2]);
			$txtSmv=trim($ex_item_tot[3]);
			$txtEff=trim($ex_item_tot[4]);
			$txtCmCost=trim($ex_item_tot[5]);
			$txtFriCost=trim($ex_item_tot[6]);
			$txtLtstCost=trim($ex_item_tot[7]);
			$txtMissCost=trim($ex_item_tot[8]);
			$txtOtherCost=trim($ex_item_tot[9]);
			$txtCommCost=trim($ex_item_tot[10]);
			$fobT_td=trim($ex_item_tot[11]);
			$txtRmgQty=trim($ex_item_tot[12]);
			$tot_item_id=trim($ex_item_tot[13]);
			
			if ($add_comma_item_tot!=0) $data_array_item_tot .=",";
			$data_array_item_tot .="(".$item_cost_id.",".$mst_id.",'".$tot_item_id."','".$fab_td."','".$spOpe_td."','".$acc_td."','".$txtSmv."','".$txtEff."','".$txtCmCost."','".$txtFriCost."','".$txtLtstCost."','".$txtMissCost."','".$txtOtherCost."','".$txtCommCost."','".$fobT_td."','".$txtRmgQty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$item_cost_id=$item_cost_id+1;
			$add_comma_item_tot++;
		}
		
		/*if(str_replace("'","",$chk_is_new_meeting)==1)
		{*/
			$buyer_meeting_id=return_next_id("id", "qc_meeting_mst", 1);
			$field_arr_buyer_meeting="id, mst_id, meeting_no, buyer_agent_id, location_id, meeting_date, meeting_time, remarks, inserted_by, insert_date, status_active, is_deleted";
		if(str_replace("'","",$txt_meeting_remarks)!="")
		{
			if($db_type==2)
			{
				$txt_meeting_hour=str_replace("'","",$txt_meeting_date)." ".str_replace("'","",$txt_meeting_time);
				$txt_meeting_hour="to_date('".$txt_meeting_hour."','DD MONTH YYYY HH24:MI:SS')";
				
				$data_arr_buyer_meeting="INSERT INTO qc_meeting_mst (".$field_arr_buyer_meeting.") VALUES (".$buyer_meeting_id.",".$mst_id.",".$txt_meeting_no.",'".$cbo_buyer_agent."','".$cbo_agent_location."',".$txt_meeting_date.",".$txt_meeting_hour.",".$txt_meeting_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			}
			else
			{
				$data_arr_buyer_meeting="(".$buyer_meeting_id.",".$mst_id.",".$txt_meeting_no.",'".$cbo_buyer_agent."','".$cbo_agent_location."',".$txt_meeting_date.",".$txt_meeting_time.",".$txt_meeting_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			}			
			$field_arr_mst_up="meeting_no";
			$data_arr_mst_up="".$buyer_meeting_id."";
		}
		/*}
		else
		{
			if(str_replace("'","",$txt_meeting_no)!='')
			{
				$field_arr_mst_up="meeting_no";
				$data_arr_mst_up="".$buyer_meeting_id."";
			}
		}*/
		
		//echo $data_arr_buyer_meeting; die;
		echo "10**insert into qc_mst (".$field_array_mst.") values ".$data_array_mst;die;
		/*$meetMst_id=return_next_id("id", "qc_meeting_mst", 1);
		$meetPer_id=return_next_id("id", "qc_meeting_mst", 1);
		$meetDtls_id=return_next_id("id", "qc_meeting_mst", 1);*/
		
		$rID=sql_insert("qc_mst",$field_array_mst,$data_array_mst,0);	
		if($rID) $flag=1; else $flag=0;
		
		$rID_fab=sql_insert("qc_fabric_dtls",$field_array_fab,$data_array_fab,0);	
		if($rID_fab) $flag=1; else $flag=0;
		
		$rID_consRate=sql_insert("qc_cons_rate_dtls",$field_array_consrate,$data_array_cons_rate,0);	
		if($rID_consRate) $flag=1; else $flag=0;
		
		$rID_tot_cost=sql_insert("qc_tot_cost_summary",$field_array_tot_cost,$data_array_tot_cost,0);	
		if($rID_tot_cost) $flag=1; else $flag=0;
		
		$rID_item_tot=sql_insert("qc_item_cost_summary",$field_array_item_tot,$data_array_item_tot,0);	
		if($rID_item_tot) $flag=1; else $flag=0;
		
		/*if(str_replace("'","",$chk_is_new_meeting)==1)
		{*/
		if(str_replace("'","",$txt_meeting_remarks)!="")
		{
			if($db_type==2)
			{
				$rID_buyer_meeting=execute_query($data_arr_buyer_meeting);
				if($rID_buyer_meeting) $flag=1; else $flag=0;
			}
			else
			{
				$rID_buyer_meeting=sql_insert("qc_meeting_mst", $field_arr_buyer_meeting, $data_arr_buyer_meeting,1);	
				if($rID_buyer_meeting) $flag=1; else $flag=0;
			}
			
			$rID_up=sql_update("qc_mst",$field_arr_mst_up,$data_arr_mst_up,"id","".$mst_id."",1);
			if($rID_up) $flag=1; else $flag=0;
		}
		/*}
		else
		{
			if(str_replace("'","",$txt_meeting_no)!='')
			{
				$rID_up=sql_update("qc_mst",$field_arr_mst_up,$data_arr_mst_up,"id","".$mst_id."",1);
				if($rID_up) $flag=1; else $flag=0;
			}
		}*/
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$mst_id).'**'.str_replace("'",'',$autoCostSheetNo).'**'.str_replace("'",'',$revise_no).'**'.str_replace("'",'',$option_id).'**'.str_replace("'",'',$type).'**'.str_replace("'",'',$buyer_meeting_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "0**".str_replace("'",'',$mst_id).'**'.str_replace("'",'',$autoCostSheetNo).'**'.str_replace("'",'',$revise_no).'**'.str_replace("'",'',$option_id).'**'.str_replace("'",'',$type).'**'.str_replace("'",'',$buyer_meeting_id);
			}
			else
			{
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
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$msg_confirm="This option is already confirm. So You can't Change, Edit or Delete it.";
		$isconfirm_id=str_replace("'","",$txt_update_id);
		if (is_duplicate_field( "cost_sheet_id", "qc_confirm_mst", "cost_sheet_id='$isconfirm_id' and status_active=1 and is_deleted=0") == 1)
		{
			echo "11**".$msg_confirm; 
			die;
		}
		
		$dup_styleRef=str_replace("'","",$txt_styleRef);
		$dup_season_id=str_replace("'","",$cbo_season_id);
		$dup_buyer_id=str_replace("'","",$cbo_buyer_id);
		$msg="Duplicate Styel Ref, Buyer, Season.";
		
		if (is_duplicate_field( "cost_sheet_no", "qc_mst", "style_ref='$dup_styleRef' and season_id='$dup_season_id' and buyer_id='$dup_buyer_id' and cost_sheet_no!=$txt_costSheetNo") == 1)
		{
			echo "11**".$msg; 
			die;
		}
		
		$insert_user_id=return_field_value("inserted_by","qc_mst","id=".$txt_update_id."","inserted_by");
		$updated_by_user=return_field_value("updated_by","qc_mst","id=".$txt_update_id."","updated_by");
		$team_dtls_arr=array();
		//$team_dtls_sql=sql_select("Select a.user_tag_id as team_leader, b.user_tag_id as team_member from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and a.user_tag_id='$user_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		if( ($updated_by_user*1)==0 ) $updated_by_user=$user_id;
		
		$team_dtls_sql=sql_select("select b.user_tag_id as team_member from  lib_mkt_team_member_info b 
where  b.team_id in (select id from lib_marketing_team a where user_tag_id='$user_id' and a.status_active=1 and a.is_deleted=0 ) and b.status_active=1 and b.is_deleted=0");
		if(count($team_dtls_sql)>1)
		{
			foreach($team_dtls_sql as $row)
			{
				$team_dtls_arr[$row[csf("team_member")]]=$user_id;
			}
		}
		else
		{
			if( $updated_by_user==$user_id )
				$team_dtls_arr[$user_id]=$user_id;
		}
		//print_r($team_dtls_arr);
		if($team_dtls_arr[$insert_user_id]=='')
		{
			echo "6**"."Update restricted, This Information is Update Only team Leader or member.";
			die;
		}
		
		$field_array_mst="temp_id*lib_item_id*style_ref*buyer_id*cons_basis*season_id*style_des*department_id*delivery_date*exchange_rate*offer_qty*quoted_price*tgt_price*stage_id*costing_date*option_id*buyer_remarks*option_remarks*updated_by*update_date";
		
		//$txt_temp_id=explode(",",str_replace("'","",$txt_temp_id));
		//asort($txt_temp_id);
		//$txt_temp_id="'".implode(",",$txt_temp_id)."'";
		
		$data_array_mst="".$cbo_temp_id."*".$txt_temp_id."*'".strtoupper(str_replace("'","",$txt_styleRef))."'*".$cbo_buyer_id."*".$cbo_cons_basis_id."*".$cbo_season_id."*'".strtoupper(str_replace("'","",$txt_styleDesc))."'*".$cbo_subDept_id."*".$txt_delivery_date."*".$txt_exchangeRate."*".$txt_offerQty."*".$txt_quotedPrice."*".$txt_tgtPrice."*".$cbo_stage_id."*".$txt_costingDate."*".$cbo_option_id."*'".strtoupper(str_replace("'","",$txt_costing_remarks))."'*'".strtoupper(str_replace("'","",$txt_option_remarks))."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		//$template_id = return_id( $txt_temp_id, $template_library, "qc_template", "id,item_id");
		
		$temp_id=explode(",",str_replace("'","",$txt_temp_id));
		
		$field_array_fab="item_id*uniq_id*body_part*des*value*alw*updated_by*update_date";
		execute_query( "delete from qc_cons_rate_dtls where  mst_id =".$txt_update_id."",1);
		$cons_rate_id=return_next_id("id", "qc_cons_rate_dtls", 1);
		$field_array_consrate="id, mst_id, item_id, type, particular_type_id, formula, consumption, ex_percent, is_calculation, rate, rate_data, value, inserted_by, insert_date";
		
		$add_comma_fab=0; $data_array_fab=""; $add_comma_cons_rate=0; $data_array_cons_rate="";
		foreach($temp_id as $item_id)
		{
			$itemBodyData='txt_itemBodyData_'.$item_id;
			$itemBodyData=explode('_',str_replace("'","",$$itemBodyData));
			if( $itemBodyData[0]=='' ) $itemBodyData=array(1,4,9,998,999);
			$m=1;
			foreach($itemBodyData as $body_part_id)
			{
				$trLen=5;
				for($j=1; $j<=$trLen; $j++)
				{
					$txtItemDes='txtItemDes_'.$item_id.'_'.$j.'_'.$m;
					$txtVal='txtVal_'.$item_id.'_'.$j.'_'.$m;
					$txtAw='txtAw_'.$item_id.'_'.$j.'_'.$m;
					$txtdvaId='txtdvaId_'.$item_id.'_'.$j.'_'.$m;
					$uniq_id=$item_id.'_'.$j.'_'.$m;
					$id_arr[]=str_replace("'",'',$$txtdvaId);
					$data_array_fab[str_replace("'",'',$$txtdvaId)]=explode("*",("'".$item_id."'*'".$uniq_id."'*'".$body_part_id."'*'".strtoupper(str_replace("'","",$$txtItemDes))."'*".$$txtVal."*".$$txtAw."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					
				}
				$m++;
			}
			
			$itemConsRateData='txt_itemConsRateData_'.$item_id;
			$ex_itemConsRateData=array_filter(explode('##',str_replace("'","",$$itemConsRateData)));
			foreach($ex_itemConsRateData as $item_wise_consRateData)
			{
				if($item_wise_consRateData!="")
				{
					//echo $item_wise_consRateData.'==';
					$ex_itemConsRate=explode('_',$item_wise_consRateData);
					$head_id=$ex_itemConsRate[0];
					$cons=$ex_itemConsRate[1];
					$is_rate_cal=$ex_itemConsRate[2];
					$rate=$ex_itemConsRate[3];
					$rateData=$ex_itemConsRate[4];
					$exPer=$ex_itemConsRate[5];
					
					$tot_val=$cons*$rate;
					
					if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
					$data_array_cons_rate .="(".$cons_rate_id.",".$txt_update_id.",'".$item_id."',1,'".$head_id."',0,'".$cons."','".$exPer."','".$is_rate_cal."','".$rate."','".$rateData."','".$tot_val."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					
					$cons_rate_id=$cons_rate_id+1;
					$add_comma_cons_rate++;
				}
			}
			
			$specialData='txt_specialData_'.$item_id;
			$ex_specialData=array_filter(explode('##',str_replace("'","",$$specialData)));
			foreach($ex_specialData as $item_wise_specialData)
			{
				$ex_itemSpConsRate=explode('_',$item_wise_specialData);
				$speciaOperation_id=$ex_itemSpConsRate[0];
				$spConsumtion=$ex_itemSpConsRate[1];
				$spExper=$ex_itemSpConsRate[2];
				$spRate=$ex_itemSpConsRate[3];
				
				$tot_spVal=$spConsumtion*$spRate;
				
				if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
				$data_array_cons_rate .="(".$cons_rate_id.",".$txt_update_id.",'".$item_id."',2,'".$speciaOperation_id."',0,'".$spConsumtion."','".$spExper."','','".$spRate."','','".$tot_spVal."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$cons_rate_id=$cons_rate_id+1;
				$add_comma_cons_rate++;
			}
			
			$itemAccData='txt_itemAccData_'.$item_id;
			$ex_itemAccData=array_filter(explode('##',str_replace("'","",$$itemAccData)));
			foreach($ex_itemAccData as $item_wise_itemAccData)
			{
				$ex_itemAcConsRate=explode('_',$item_wise_itemAccData);
				$accessories_id=$ex_itemAcConsRate[0];
				$acConsumtion=$ex_itemAcConsRate[1];
				$acExPer=$ex_itemAcConsRate[2];
				$acRate=$ex_itemAcConsRate[3];
				
				$tot_acVal=$acConsumtion*$acRate;
				
				if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
				$data_array_cons_rate .="(".$cons_rate_id.",".$txt_update_id.",'".$item_id."',3,'".$accessories_id."',0,'".$acConsumtion."','".$acExPer."',0,'".$acRate."','','".$tot_acVal."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$cons_rate_id=$cons_rate_id+1;
				$add_comma_cons_rate++;
			}
		}
		
		// echo "10**";
		// print_r ($data_array_fab); 
		// echo bulk_update_sql_statement("qc_fabric_dtls", "id",$field_array_fab,$data_array_fab,$id_arr ); die;
		// die;
		//echo $data_array_cons_rate; 
		execute_query( "delete from qc_tot_cost_summary where mst_id =".$txt_update_id."",1);
		$tc_sum_id=return_next_id("id", "qc_tot_cost_summary", 1);
		$field_array_tot_cost="id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, tot_rmg_ratio, inserted_by, insert_date";
		
		$ex_tot_cost_data=explode('_',$data_tot_cost_summ);
		
		$cbo_buyer_agent=trim($ex_tot_cost_data[0]);
		$cbo_agent_location=trim($ex_tot_cost_data[1]);
		$txt_noOfPack=trim($ex_tot_cost_data[2]);
		$cmPop=trim($ex_tot_cost_data[3]);
		$txt_lumSum_cost=trim($ex_tot_cost_data[4]);
		$txt_commPer=trim($ex_tot_cost_data[5]);
		$totFab_td=trim($ex_tot_cost_data[6]);
		$totSpc_td=trim($ex_tot_cost_data[7]);
		$totAcc_td=trim($ex_tot_cost_data[8]);
		$totCm_td=trim($ex_tot_cost_data[9]);
		$totFriCst_td=trim($ex_tot_cost_data[10]);
		$totLbTstCst_td=trim($ex_tot_cost_data[11]);
		$totMissCst_td=trim($ex_tot_cost_data[12]);
		$totOtherCst_td=trim($ex_tot_cost_data[13]);
		$totCommCst_td=trim($ex_tot_cost_data[14]);
		$totCost_td=trim($ex_tot_cost_data[15]);
		$totalFob_td=trim($ex_tot_cost_data[16]);
		$totRmgQty_td=trim($ex_tot_cost_data[17]);
		
		$data_array_tot_cost="(".$tc_sum_id.",".$txt_update_id.",'".$cbo_buyer_agent."','".$cbo_agent_location."','".$txt_noOfPack."',0,'".$cmPop."','".$txt_lumSum_cost."','".$txt_commPer."','".$totFab_td."','".$totSpc_td."','".$totAcc_td."','".$totCm_td."','".$totFriCst_td."','".$totLbTstCst_td."','".$totMissCst_td."','".$totOtherCst_td."','".$totCommCst_td."','".$totCost_td."','".$totalFob_td."','".$totRmgQty_td."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//echo $data_array_tot_cost; die;
		$field_array_item_tot=" id, mst_id, item_id, fabric_cost, sp_operation_cost, accessories_cost, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs, rmg_ratio, inserted_by, insert_date";
		execute_query( "delete from qc_item_cost_summary where  mst_id =".$txt_update_id."",1);
		$item_cost_id=return_next_id("id", "qc_item_cost_summary", 1);
		$ex_item_wise_tot_data=array_filter(explode('__',str_replace("'","",$item_wise_tot_data)));
		$add_comma_item_tot=0; $data_array_item_tot="";
		foreach($ex_item_wise_tot_data as $item_wise_tot)
		{
			$fab_td=$spOpe_td=$acc_td=$txtSmv=$txtEff=$txtCmCost=$txtFriCost=$txtLtstCost=$txtMissCost=$txtOtherCost=$txtCommCost=$fobT_td=$tot_item_id=0;
			
			$ex_item_tot=explode('_',$item_wise_tot);
			$fab_td=trim($ex_item_tot[0]);
			$spOpe_td=trim($ex_item_tot[1]);
			$acc_td=trim($ex_item_tot[2]);
			$txtSmv=trim($ex_tot_cost_data[3]);
			$txtEff=trim($ex_tot_cost_data[4]);
			$txtCmCost=trim($ex_item_tot[5]);
			$txtFriCost=trim($ex_item_tot[6]);
			$txtLtstCost=trim($ex_item_tot[7]);
			$txtMissCost=trim($ex_item_tot[8]);
			$txtOtherCost=trim($ex_item_tot[9]);
			$txtCommCost=trim($ex_item_tot[10]);
			$fobT_td=trim($ex_item_tot[11]);
			$txtRmgQty=trim($ex_item_tot[12]);
			$tot_item_id=trim($ex_item_tot[13]);
			
			if ($add_comma_item_tot!=0) $data_array_item_tot .=",";
			$data_array_item_tot .="(".$item_cost_id.",".$txt_update_id.",'".$tot_item_id."','".$fab_td."','".$spOpe_td."','".$acc_td."','".$txtSmv."','".$txtEff."','".$txtCmCost."','".$txtFriCost."','".$txtLtstCost."','".$txtMissCost."','".$txtOtherCost."','".$txtCommCost."','".$fobT_td."','".$txtRmgQty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$item_cost_id=$item_cost_id+1;
			$add_comma_item_tot++;
		}
		
		/*if(str_replace("'","",$chk_is_new_meeting)==1)
		{*/
		if(str_replace("'","",$txt_meeting_remarks)!="")
		{
			$buyer_meeting_id=return_next_id("id", "qc_meeting_mst", 1);
			$field_arr_buyer_meeting="id, mst_id, meeting_no, buyer_agent_id, location_id, meeting_date, meeting_time, remarks, inserted_by, insert_date, status_active, is_deleted";
			if($db_type==2)
			{
				$txt_meeting_hour=str_replace("'","",$txt_meeting_date)." ".str_replace("'","",$txt_meeting_time);
				$txt_meeting_hour="to_date('".$txt_meeting_hour."','DD MONTH YYYY HH24:MI:SS')";
				
				$data_arr_buyer_meeting="INSERT INTO qc_meeting_mst (".$field_arr_buyer_meeting.") VALUES (".$buyer_meeting_id.",".$txt_update_id.",".$txt_meeting_no.",'".$cbo_buyer_agent."','".$cbo_agent_location."',".$txt_meeting_date.",".$txt_meeting_hour.",".$txt_meeting_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			}
			else
			{
				$data_arr_buyer_meeting="(".$buyer_meeting_id.",".$txt_update_id.",".$txt_meeting_no.",'".$cbo_buyer_agent."','".$cbo_agent_location."',".$txt_meeting_date.",".$txt_meeting_time.",".$txt_meeting_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			}
			
			$field_arr_mst_up="meeting_no";
			$data_arr_mst_up="".$buyer_meeting_id."";
			//echo "10**".$data_arr_buyer_meeting;die;
		}
		/*}
		else
		{
			if(str_replace("'","",$txt_meeting_no)!="")
			{
				if($db_type==2)
				{
					$txt_meeting_hour=str_replace("'","",$txt_meeting_date)." ".str_replace("'","",$txt_meeting_time);
					$txt_meeting_hour="to_date('".$txt_meeting_hour."','DD MONTH YYYY HH24:MI:SS')";
				}
				else
				{
					$txt_meeting_hour=str_replace("'","",$txt_meeting_time);
				}
				$field_arr_buyer_meeting_up="meeting_date*meeting_time*remarks*updated_by*update_date";
				$data_arr_buyer_meeting_up="".$txt_meeting_date."*".$txt_meeting_hour."*".$txt_meeting_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				
				$field_arr_mst_up="meeting_no";
				$data_arr_mst_up="".$buyer_meeting_id."";
			}
		}*/
		//echo "10**insert into qc_meeting_mst (".$field_arr_buyer_meeting.") values ".$data_arr_buyer_meeting;die;
		/*$meetMst_id=return_next_id("id", "qc_meeting_mst", 1);
		$meetPer_id=return_next_id("id", "qc_meeting_mst", 1);
		$meetDtls_id=return_next_id("id", "qc_meeting_mst", 1);*/
		$rID=sql_update("qc_mst",$field_array_mst,$data_array_mst,"id","".$txt_update_id."",1);
		if($rID) $flag=1; else $flag=0;
		//echo bulk_update_sql_statement("qc_fabric_dtls", "id",$field_array_fab,$data_array_fab,$id_arr ); die;
		$rID_fab=execute_query(bulk_update_sql_statement("qc_fabric_dtls", "id",$field_array_fab,$data_array_fab,$id_arr ));
		//$rID_fab=sql_insert("qc_fabric_dtls",$field_array_fab,$data_array_fab,0);	
		if($rID_fab) $flag=1; else $flag=0;
		
		$rID_consRate=sql_insert("qc_cons_rate_dtls",$field_array_consrate,$data_array_cons_rate,0);	
		if($rID_consRate) $flag=1; else $flag=0;
		
		$rID_tot_cost=sql_insert("qc_tot_cost_summary",$field_array_tot_cost,$data_array_tot_cost,0);	
		if($rID_tot_cost) $flag=1; else $flag=0;
		
		$rID_item_tot=sql_insert("qc_item_cost_summary",$field_array_item_tot,$data_array_item_tot,0);	
		if($rID_item_tot) $flag=1; else $flag=0;
		
		/*if(str_replace("'","",$chk_is_new_meeting)==1)
		{*/
		if(str_replace("'","",$txt_meeting_remarks)!="")
		{
			if($db_type==2)
			{
				$rID_buyer_meeting=execute_query($data_arr_buyer_meeting);
				if($rID_buyer_meeting) $flag=1; else $flag=0;
			}
			else
			{
				$rID_buyer_meeting=sql_insert("qc_meeting_mst", $field_arr_buyer_meeting, $data_arr_buyer_meeting,1);	
				if($rID_buyer_meeting) $flag=1; else $flag=0;
			}
			//echo $rID_buyer_meeting;
			$rID_up=sql_update("qc_mst",$field_arr_mst_up,$data_arr_mst_up,"id","".$txt_update_id."",1);
			if($rID_up) $flag=1; else $flag=0;
		}
		/*}
		else
		{
			if(str_replace("'","",$txt_meeting_no)!="")
			{
				$rID_buyer_meeting_up=sql_update("qc_meeting_mst",$field_arr_buyer_meeting_up,$data_arr_buyer_meeting_up,"id","".$txt_meeting_no."",1);
				if($rID_buyer_meeting_up) $flag=1; else $flag=0;
				
				$rID_upmst=sql_update("qc_mst",$field_arr_mst_up,$data_arr_mst_up,"id","".$txt_update_id."",1);
				if($rID_upmst) $flag=1; else $flag=0;
			}
		}*/
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_update_id).'**'.str_replace("'",'',$txt_costSheetNo).'**'.str_replace("'",'',$cbo_revise_no).'**'.str_replace("'",'',$cbo_option_id).'**'.str_replace("'",'',$type);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'",'',$txt_update_id).'**'.str_replace("'",'',$txt_costSheetNo).'**'.str_replace("'",'',$cbo_revise_no).'**'.str_replace("'",'',$cbo_option_id).'**'.str_replace("'",'',$type);
			}
			else
			{
				oci_rollback($con); 
				echo "10**";
			}
		}
		disconnect($con);
		die;	
	}
	else
	{
		echo "11**"."Delete Restricted";
	}
	//exit();
}

if($action=="style_popup")
{
	echo load_html_head_contents("Style Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
    <script>
	function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
			
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
			}
		}
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	 
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
	function js_set_value(id)
	{
		var str=id.split("_");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		var strdt=str[2];
		str=str[1];
	
		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
			selected_name.push( strdt );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i,1 );
		}
		var id = '';
		var ddd='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			ddd += selected_name[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		ddd = ddd.substr( 0, ddd.length - 1 );
		$('#hide_style_id').val( id );
		$('#hide_style_no').val( ddd );
	} 
		  
	</script>
     <input type="hidden" name="hide_style_id" id="hide_style_id" value="" />
     <input type="hidden" name="hide_style_no" id="hide_style_no" value="" />
 
    </head>
    <body>
    <div align="center">
        <form name="styleRefform_1" id="styleRefform_1">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Season</th>
                    <th>Department</th>
                    <th>Style</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('styleRefform_1','search_div','','','','');"></th> 					
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name",1, "-- All Buyer--",$selected,"load_drop_down( 'quick_costing_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'quick_costing_controller',this.value, 'load_drop_down_sub_dep', 'sub_td' );",0 ); ?>
                        </td>                 
                        <td id="season_td"><? echo create_drop_down( "cbo_season_id", 130, $blank_array,'', 1, "-- Select Season--",$selected, "" ); ?></td>
                        <td id="sub_td"><? echo create_drop_down( "cbo_subDept_id", 130, $blank_array,'', 1, "-- Select Dept--",$selected, "" ); ?></td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_id').value+'**'+document.getElementById('cbo_season_id').value+'**'+document.getElementById('cbo_subDept_id').value+'**'+document.getElementById('txt_search_common').value, 'style_ref_search_list_view', 'search_div', 'quick_costing_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if($action=="style_ref_search_list_view")
{
	extract($_REQUEST);
	$buyer_arr=return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");	
	$season_arr=return_library_array("select id, season_name from lib_buyer_season","id","season_name");
	$department_arr=return_library_array("select id, sub_department_name from lib_pro_sub_deparatment","id","sub_department_name");
	$stage_arr=return_library_array("select id, stage_name from lib_stage_name","id","stage_name");
	$user_arr=return_library_array("select id, user_name from user_passwd","id","user_name");
	
	//echo $data;
	$ex_data=explode('**',$data);

	$buyer_id=str_replace("'","",$ex_data[0]);
	$season_id=str_replace("'","",$ex_data[1]);
	$department_id=str_replace("'","",$ex_data[2]);
	$style_ref=strtoupper(str_replace("'","",$ex_data[3]));
	
	if($buyer_id!=0) $buyer_id_cond="and a.buyer_id=$buyer_id"; else $buyer_id_cond="";
	if($season_id!=0) $season_id_cond="and a.season_id=$season_id"; else $season_id_cond="";
	if($department_id!=0) $department_id_cond="and a.department_id=$department_id"; else $department_id_cond="";
	if($style_ref!='') $style_ref_cond=" and a.style_ref like '%$style_ref%'"; else $style_ref_cond="";
	
	$lib_temp_arr=return_library_array("select id, item_name from lib_qc_template","id","item_name");
	if($db_type==0) $concat_cond="group_concat(lib_item_id)";
	else if($db_type==2) $concat_cond="listagg(cast(lib_item_id as varchar2(4000)),',') within group (order by lib_item_id)";
	else $concat_cond="";
	$sql_tmp="select temp_id, $concat_cond as lib_item_id from qc_template where status_active=1 and is_deleted=0 group by temp_id order by temp_id ASC";
	$sql_tmp_res=sql_select($sql_tmp);
	//print_r($sql_tmp_res);die;
	$template_name_arr=array();
	foreach($sql_tmp_res as $row)
	{
		$lib_temp_id='';
		$ex_temp_id=explode(',',$row[csf('lib_item_id')]);
		foreach($ex_temp_id as $lib_id)
		{
			if($lib_temp_id=="") $lib_temp_id=$lib_temp_arr[$lib_id]; else $lib_temp_id.=','.$lib_temp_arr[$lib_id];
		}
		$template_name_arr[$row[csf('temp_id')]]=$lib_temp_id;
	}
	?>
    <div>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1040" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="80">Buyer Name</th>
            <th width="60">Season</th>
            <th width="70">Department</th>
            <th width="90">Style Ref.</th>
            <th width="130">Template</th>
            <th width="60">Offer Qty.</th>
            <th width="60">FOB ($)</th>
            <th width="50">TGT Price</th>
            <th width="50">Quoted Price ($)</th>
            <th width="70">Stage</th>
            <th width="40">Confirm</th>
            <th width="65">Cost Sheet No</th>
            <th width="60">Insert By</th>
            <th width="60">Update By</th>
            <th>Revise No</th>
        </thead>
    </table>
    <div style="width:1040px; overflow-y:scroll; max-height:260px;" align="center">
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1020" class="rpt_table" id="tbl_list_search" >
    <?
	$sql_style = "select a.id, a.cost_sheet_no, a.revise_no, a.buyer_id, a.season_id, a.style_ref, a.department_id, a.temp_id, a.lib_item_id, a.offer_qty, a.tgt_price, a.quoted_price, a.stage_id, a.inserted_by, a.updated_by, b.tot_fob_cost, b.is_confirm from qc_mst a, qc_tot_cost_summary b where a.id=b.mst_id and a.pre_cost_sheet_id=0 and a.id not in ( select c.mst_id from qc_style_temp_data c where inserted_by='$user_id' ) $buyer_id_cond $style_ref_cond $season_id_cond $department_id_cond and a.status_active=1 and a.is_deleted=0 order by a.id Desc"; // 
	//echo $sql_style;
	$sql_style_result=sql_select($sql_style); $i=1;
	foreach($sql_style_result as $row)
	{
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		$ex_tmpelate_id=explode(',',$row[csf("temp_id")]);
		$count_temp=count(explode(',',$row[csf("lib_item_id")]));
		$template_name='';
		foreach($ex_tmpelate_id as $tmpelate_id)
		{
			if($template_name=="") $template_name=$template_name_arr[$tmpelate_id]; else $template_name.=','.$template_name_arr[$tmpelate_id];
		}
		
		$confirm='';
		if($row[csf("is_confirm")]==1) $confirm='Yes';
		else $confirm='No';
		?>
        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i; ?>" onClick="js_set_value('<? echo $i.'_'.$row[csf("id")].'_'.$row[csf("style_ref")].'_'.$row[csf("cost_sheet_no")]; ?>')"> 
            <td width="30" align="center"><?php echo $i; ?></td>
            <td width="80"><div style="word-wrap:break-word; width:80px"><?php echo $buyer_arr[$row[csf("buyer_id")]]; ?></div></td>
            <td width="60"><?php echo $season_arr[$row[csf("season_id")]]; ?></td>
            <td width="70"><?php echo $department_arr[$row[csf("department_id")]]; ?>&nbsp;</td>
            <td width="90"><?php echo $row[csf("style_ref")]; ?></td>
            <td width="130"><div style="word-wrap:break-word; width:130px"><?php echo $template_name; ?></div></td>
            <td width="60" align="right"><?php echo number_format($row[csf("offer_qty")],2); ?>&nbsp;</td>
            <td width="60" align="right"><?php echo number_format($row[csf("tot_fob_cost")],2); ?>&nbsp;</td>
            <td width="50" align="right"><?php echo number_format($row[csf("tgt_price")],2); ?>&nbsp;</td>
            <td width="50" align="right"><?php echo number_format($row[csf("quoted_price")],2); ?>&nbsp;</td>
            <td width="70"><?php echo $stage_arr[$row[csf("stage_id")]]; ?>&nbsp;</td>	
            <td width="40"><? echo $confirm; ?>&nbsp;</td>
            <td width="65"><?php echo $row[csf("cost_sheet_no")]; ?></td>
            <td width="60"><?php echo $user_arr[$row[csf("inserted_by")]]; ?></td>
            <td width="60"><?php echo $user_arr[$row[csf("updated_by")]]; ?></td>
            <td align="center"><?php echo $row[csf("revise_no")]; ?></td>
        </tr>
		<?
		$i++;
	}
	?>
    </table>
        
    </div>
    </div>
    <div class="check_all_container"><div style="width:100%"> 
        <div style="width:50%; float:left" align="left">
            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
        </div>
        <div style="width:50%; float:left" align="left">
        <input type="button" name="close" id="close"  onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
        </div>
    </div></div>
	<?
	exit();
}

if($action=="temp_style_list_view")
{
	//echo $data.'==';
	$ex_data=explode('__',$data);
	$user_id=$_SESSION['logic_erp']['user_id'];
	$season_arr=return_library_array("select id, season_name from lib_buyer_season","id","season_name");
	$user_arr=return_library_array("select id, user_name from user_passwd","id","user_name");
	
	if($ex_data[1]==1)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		/*if (is_duplicate_field( "cost_sheet_no", "qc_style_temp_data", "mst_id in ($ex_data[0]) and inserted_by='$user_id'" ) == 1)
		{
			echo "11**0"; 
			die;
		}*/
		
		$sql_style= "select id, cost_sheet_no, style_ref, season_id, inserted_by from qc_mst where id in ($ex_data[0]) and status_active=1 and is_deleted=0";
		//echo  $sql_style;
		$sql_data_arr=sql_select($sql_style);
		
		$field_array="id, mst_id, cost_sheet_no, style_user, inserted_by, insert_date";
		$qc_style_list_id=return_next_id("id", "qc_style_temp_data", 1);
		$add_comma=0; $data_array="";
		foreach($sql_data_arr as $row)
		{ 
			$mst_id=$row[csf('id')];
			$cost_sheet_no=$row[csf('cost_sheet_no')];
			$style_user=$row[csf('inserted_by')];
			if ($add_comma!=0) $data_array .=",";
			$data_array .="(".$qc_style_list_id.",'".$mst_id."','".$cost_sheet_no."','".$style_user."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$add_comma++;
			$qc_style_list_id=$qc_style_list_id+1;
		}
		unset($sql_data_arr);
		//echo "insert into qc_style_temp_data ($field_array) values".$data_array;
		$rID=sql_insert("qc_style_temp_data",$field_array,$data_array,0);
		if($rID) $flag=1; else $flag=0;
	}
	else if($ex_data[1]==2)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//echo "delete from qc_style_temp_data where style_user ='$user_id'";
		$rID=execute_query( "delete from qc_style_temp_data where inserted_by ='$user_id'",0);
		if($rID) $flag=1; else $flag=0;
	}
	else if($ex_data[1]==3)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//echo "delete from qc_style_temp_data where inserted_by='$user_id' and mst_id='$ex_data[0]'";
		$rID=execute_query( "delete from qc_style_temp_data where inserted_by='$user_id' and mst_id='$ex_data[0]'",0);
		if($rID) $flag=1; else $flag=0;
	}
	
	if($db_type==0)
	{
		if($flag==1)
		{
			mysql_query("COMMIT");  
			//echo "1**";
		}
		else
		{
			mysql_query("ROLLBACK"); 
			//echo "10**";
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($flag==1)
		{
			oci_commit($con);  
			//echo "1**";
		}
		else
		{
			oci_rollback($con); 
			//echo "10**";
		}
	}
	
	$sql_temp_style=sql_select("select a.id, a.cost_sheet_no, a.style_ref, a.season_id, a.inserted_by, a.updated_by from qc_mst a, qc_style_temp_data b where a.id=b.mst_id and b.inserted_by='$user_id' order by a.style_ref ASC");
	
	$i=1;
	foreach($sql_temp_style as $row)
	{  
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		$user_name="";
		if($row[csf('updated_by')]==0 || $row[csf('updated_by')]=="") $user_name=$user_arr[$row[csf('inserted_by')]]; else $user_name=$user_arr[$row[csf('updated_by')]];
		
		?>
        <tr id="tr_<? echo $row[csf('id')]; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="change_color_tr('<? echo $row[csf('id')]; ?>','<? echo $bgcolor; ?>'); set_onclick_style_list('<? echo $row[csf('id')].'__'.$row[csf('inserted_by')].'__'.$row[csf('cost_sheet_no')]; ?>')"> <!--change_color('tr_<?// echo $i; ?>','<?// echo $bgcolor;?>');-->
        	<td width="90"><div style="word-wrap:break-word; width:90px"><? echo $row[csf('style_ref')]; ?>&nbsp;</div></td>
            <td width="70"><div style="word-wrap:break-word; width:70px"><? echo $season_arr[$row[csf('season_id')]]; ?>&nbsp;</div></td>
            <td title="<? //echo $user_arr[$row[csf('updated_by')]]; ?>"><div style="word-wrap:break-word; width:60px"><? echo $user_name; //$user_arr[$row[csf('inserted_by')]].'-'.$user_arr[$row[csf('updated_by')]]; ?>&nbsp;</div></td>
        <?
		$i++;
	}
//	if($ex_data[1]==1) echo "change_color_tr('".$i."','".$bgcolor."');\n"; 
	unset($sql_temp_style);
	exit();
}

if($action=="populate_style_details_data")
{
	//echo $data;
	$max_meeting=return_field_value("max(meeting_no) as max_meeting_no","qc_meeting_mst","is_deleted=0 and status_active=1","max_meeting_no");
	if($max_meeting=="") $max_meeting=0;
	
	$lib_temp_arr=return_library_array("select id, item_name from lib_qc_template","id","item_name");
	if($db_type==0) $concat_cond="group_concat(lib_item_id)";
	else if($db_type==2) $concat_cond="listagg(cast(lib_item_id as varchar2(4000)),',') within group (order by lib_item_id)";
	else $concat_cond="";
	$sql_tmp="select temp_id, $concat_cond as lib_item_id from qc_template where status_active=1 and is_deleted=0 group by temp_id order by temp_id ASC";
	$sql_tmp_res=sql_select($sql_tmp);
	
	//print_r($sql_tmp_res);die;
	$template_name_arr=array();
	foreach($sql_tmp_res as $row)
	{
		$lib_temp_id='';
		
		$ex_temp_id=explode(',',$row[csf('lib_item_id')]);
		foreach($ex_temp_id as $lib_id)
		{
			if($lib_temp_id=="") $lib_temp_id=$lib_temp_arr[$lib_id]; else $lib_temp_id.=','.$lib_temp_arr[$lib_id];
		}
		
		$template_name_arr[$row[csf('temp_id')]]=$lib_temp_id;
	}
	
	$buyer_remark_arr=array(); $pre_mee_arr=array();
	if($db_type==0) $meeting_time="TIME_FORMAT(meeting_time, '%H:%i')";
	else if ($db_type==2) $meeting_time="TO_CHAR(meeting_time,'HH24:MI')";
	
	
	$sql_buyer_remark="select id, mst_id, meeting_no, buyer_agent_id, location_id, meeting_date, $meeting_time as meeting_time, remarks from qc_meeting_mst order by id ASC";
	//echo $sql_buyer_remark;
	$sql_buyer_remark_res=sql_select($sql_buyer_remark);
	
	foreach($sql_buyer_remark_res as $row)
	{
		$buyer_remark_arr[$row[csf('id')]]['date']=$row[csf('meeting_date')];
		$buyer_remark_arr[$row[csf('id')]]['time']=$row[csf('meeting_time')];
		$buyer_remark_arr[$row[csf('id')]]['remarks']=$row[csf('remarks')];
		$buyer_remark_arr[$row[csf('id')]]['meeting_no']=$row[csf('meeting_no')];
		$buyer_remark_arr[$row[csf('id')]]['agent_id']=$row[csf('buyer_agent_id')];
		$buyer_remark_arr[$row[csf('id')]]['location_id']=$row[csf('location_id')];
		$buyer_remark_arr[$row[csf('id')]]['id']=$row[csf('id')];
		
		$pre_mee_arr[$row[csf('meeting_no')]]['agent_id']=$row[csf('buyer_agent_id')];
		$pre_mee_arr[$row[csf('meeting_no')]]['location_id']=$row[csf('location_id')];
	}
	
	$ex_data=explode('__',$data);
	$cost_sheet_id=$ex_data[0];
	$cond_user=$ex_data[1];
	$cost_sheet_no=$ex_data[2];
	$other_val=$ex_data[3];
	//if ($other_val!=0)
	$ex_other_val=explode('***',$other_val);
	
	$rev_no=0; $option_no=0; $starting_row_cond='';	
	if($ex_other_val[0]==0)
	{
		$rev_no=0;
		$option_no=0;
		$starting_row_cond=" and pre_cost_sheet_id=0";
	}
	else
	{
		$rev_no=$ex_other_val[0];
		$option_no=$ex_other_val[1];
		$starting_row_cond='';	
	}
	$rev_no_cond=''; $option_id_cond='';
	if($ex_other_val[0]!="")
		$rev_no_cond="and revise_no='$ex_other_val[0]'";
	else
		$rev_no_cond='';
		
	if($ex_other_val[1]!="")
		$option_id_cond="and option_id='$ex_other_val[1]'";
	else
		$option_id_cond='';
	
	//echo "select id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, style_ref, buyer_id, cons_basis, season_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, revise_no, option_id, buyer_remarks, option_remarks, inserted_by from qc_mst where status_active=1 and is_deleted=0 and cost_sheet_no='$cost_sheet_no' $rev_no_cond $option_id_cond"; die;
	$data_array=sql_select("select id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, style_ref, buyer_id, cons_basis, season_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, revise_no, option_id, buyer_remarks, option_remarks, inserted_by, meeting_no from qc_mst where status_active=1 and is_deleted=0 and cost_sheet_no='$cost_sheet_no' $rev_no_cond $option_id_cond ");//$starting_row_cond and inserted_by='$cond_user'
	$cond_data=$data_array[0][csf('id')];
	$cost_sheet_id=$data_array[0][csf('id')];
	foreach ($data_array as $row)
	{
		$ex_tmpelate_id=explode(',',$row[csf("temp_id")]);
		$count_temp=count(explode(',',$row[csf("lib_item_id")]));
		$template_name='';
		foreach($ex_tmpelate_id as $tmpelate_id)
		{
			if($template_name=="") $template_name=$template_name_arr[$tmpelate_id]; else $template_name.=','.$template_name_arr[$tmpelate_id];
		}
		echo "fnc_item_list('".trim($row[csf("lib_item_id")])."__".$template_name."');\n"; 
		echo "fnc_summary_dtls('".trim($row[csf("lib_item_id")])."__".$template_name."');\n"; 
		//echo "fnc_dtls_ganerate('".$row[csf("temp_id")].'__'.$template_name."');\n"; 
		echo "navigate_arrow_key();\n";
		
		echo "$('#cbo_temp_id').val('".$row[csf("temp_id")]."');\n";
		echo "$('#txt_temp_id').val('".trim($row[csf("lib_item_id")])."');\n";
		//echo "$('#txt_tmp_name').val('".$template_name."');\n";
		echo "$('#txt_styleRef').val('".$row[csf("style_ref")]."');\n"; 
		echo "$('#txt_update_id').val('".$row[csf("id")]."');\n";
		//echo "$('#txt_costSheet_id').val('".$row[csf("cost_sheet_id")]."');\n";
		echo "$('#txt_costSheetNo').val('".$row[csf("cost_sheet_no")]."');\n";
		echo "$('#cbo_buyer_id').val('".$row[csf("buyer_id")]."');\n"; 
		echo "$('#cbo_cons_basis_id').val('".$row[csf("cons_basis")]."');\n";
		//echo "fnc_consumption_write_disable('".$row[csf("cons_basis")]."');\n"; 
		echo "load_drop_down('requires/quick_costing_controller', '".$row[csf("buyer_id")]."', 'load_drop_down_season', 'season_td');\n"; 
		echo "load_drop_down('requires/quick_costing_controller', '".$row[csf("buyer_id")]."', 'load_drop_down_sub_dep', 'sub_td');\n"; 
		echo "$('#cbo_season_id').val('".$row[csf("season_id")]."');\n";
		echo "$('#txt_styleDesc').val('".$row[csf("style_des")]."');\n";
		echo "$('#cbo_subDept_id').val('".$row[csf("department_id")]."');\n";
		echo "$('#txt_delivery_date').val('".change_date_format($row[csf("delivery_date")])."');\n";
		echo "$('#txt_exchangeRate').val('".$row[csf("exchange_rate")]."');\n";
		echo "$('#txt_offerQty').val('".$row[csf("offer_qty")]."');\n";
		echo "$('#txt_quotedPrice').val('".$row[csf("quoted_price")]."');\n"; 
		echo "$('#txt_tgtPrice').val('".$row[csf("tgt_price")]."');\n";
		echo "$('#cbo_stage_id').val('".$row[csf("stage_id")]."');\n";
		echo "$('#txt_costingDate').val('".change_date_format($row[csf("costing_date")])."');\n"; 
		$buyer_remarks = str_replace("\n", "\\n", $row[csf("buyer_remarks")]);
		echo "$('#txt_costing_remarks').val('".$buyer_remarks."');\n"; 
		echo "$('#txt_option_remarks').val('".$row[csf("option_remarks")]."');\n"; 
		echo "$('#txt_isUpdate').val(1);\n"; 
		echo "disable_enable_fields('cbo_temp_id*cbo_cons_basis_id',1);\n";
		if($ex_data[4]!="from_option")
			echo "load_drop_down('requires/quick_costing_controller', '".$row[csf("cost_sheet_no")].'__'.$row[csf("option_id")].'__'.$row[csf("revise_no")]."', 'load_drop_down_revise_no', 'revise_td');\n"; 
		echo "load_drop_down('requires/quick_costing_controller', '".$row[csf("cost_sheet_no")].'__'.$row[csf("option_id")]."', 'load_drop_down_option_id', 'option_td');\n"; 
		echo "$('#cbo_option_id').val('".$row[csf("option_id")]."');\n";
		if($count_temp==1) $set_pcs='Pcs';
		else if($count_temp>1) $set_pcs='Set';
		else $set_pcs='';
		
		echo "$('#uom_td').text('".$set_pcs."');\n";
		if($max_meeting==$buyer_remark_arr[$row[csf('meeting_no')]]['meeting_no'])
		{
			echo "$('#txt_meeting_no').val('".$buyer_remark_arr[$row[csf('meeting_no')]]['meeting_no']."');\n";
			echo "$('#cbo_buyer_agent').val('".$buyer_remark_arr[$row[csf('meeting_no')]]['agent_id']."');\n";
			echo "$('#cbo_agent_location').val('".$buyer_remark_arr[$row[csf('meeting_no')]]['location_id']."');\n";
			echo "$('#txt_meeting_date').val('".change_date_format($buyer_remark_arr[$row[csf('meeting_no')]]['date'])."');\n";
			echo "$('#txt_meeting_time').val('".$buyer_remark_arr[$row[csf('meeting_no')]]['time']."');\n";
			echo "$('#txt_meeting_remarks').val('".$buyer_remark_arr[$row[csf('meeting_no')]]['remarks']."');\n";
		}
		else
		{
			echo "$('#txt_meeting_no').val('".$max_meeting."');\n";
			//echo "$('#cbo_buyer_agent').val('".$buyer_remark_arr[$max_meeting]['agent_id']."');\n";
			//echo "$('#cbo_agent_location').val('".$buyer_remark_arr[$max_meeting]['location_id']."');\n";
			echo "$('#txt_meeting_date').val('".date('d-m-Y')."');\n";
			echo "$('#txt_meeting_time').val('".date('H:i', time())."');\n";
			echo "$('#txt_meeting_remarks').val('');\n";
		}
		//echo "$('#txt_meeting_no').val('".$buyer_remark_arr[$row[csf('meeting_no')]]['id']."');\n";
	}
	
	$sql_fab_dtls="Select id, item_id, uniq_id, body_part, des, value, alw from qc_fabric_dtls where status_active=1 and is_deleted=0 and mst_id='$cond_data'";
	$sql_result_fab_dtls=sql_select($sql_fab_dtls); $item_body_array=array(); $itemDVAData_array=array(); 
	foreach ($sql_result_fab_dtls as $rowFbDt)
	{
		$item_body_array[$rowFbDt[csf("item_id")]][$rowFbDt[csf("body_part")]]=1;
		$itemDVAData_array[$rowFbDt[csf("item_id")]].=$rowFbDt[csf("des")].'_'.($rowFbDt[csf("value")]*1).'_'.($rowFbDt[csf("alw")]*1).'##';
		
		echo "$('#txtItemDes_".$rowFbDt[csf("uniq_id")]."').val('".$rowFbDt[csf("des")]."');\n";
		echo "$('#txtVal_".$rowFbDt[csf("uniq_id")]."').val('".$rowFbDt[csf("value")]."');\n";
		echo "$('#txtAw_".$rowFbDt[csf("uniq_id")]."').val('".$rowFbDt[csf("alw")]."');\n";
		echo "$('#txtdvaId_".$rowFbDt[csf("uniq_id")]."').val('".$rowFbDt[csf("id")]."');\n";
	}
	
	$sql_cons_rate="select id, item_id, type, particular_type_id, consumption, ex_percent, is_calculation, rate, rate_data, value from qc_cons_rate_dtls where mst_id='$cond_data' and status_active=1 and is_deleted=0  order by id asc";
	$sql_result_cons_rate=sql_select($sql_cons_rate); $cons_rate_fab_arr=array(); $cons_rate_sp_arr=array(); $cons_rate_ac_arr=array();
	foreach ($sql_result_cons_rate as $rowConsRate)
	{
		if($rowConsRate[csf("type")]==1)
		{
			$cons_rate_fab_arr[$rowConsRate[csf("item_id")]].=$rowConsRate[csf("particular_type_id")].'_'.$rowConsRate[csf("consumption")].'_'.$rowConsRate[csf("is_calculation")].'_'.$rowConsRate[csf("rate")].'_'.$rowConsRate[csf("rate_data")].'_'.$rowConsRate[csf("ex_percent")].'##';
		}
		
		if($rowConsRate[csf("type")]==2)
		{
			$cons_rate_sp_arr[$rowConsRate[csf("item_id")]].=$rowConsRate[csf("particular_type_id")].'_'.$rowConsRate[csf("consumption")].'_'.$rowConsRate[csf("ex_percent")].'_'.$rowConsRate[csf("rate")].'##';
		}
		
		if($rowConsRate[csf("type")]==3)
		{
			$cons_rate_ac_arr[$rowConsRate[csf("item_id")]].=$rowConsRate[csf("particular_type_id")].'_'.$rowConsRate[csf("consumption")].'_'.$rowConsRate[csf("ex_percent")].'_'.$rowConsRate[csf("rate")].'##';
		}
	}
	
	$active_item=1;
	foreach($item_body_array as $item_id=>$body_id)
	{
		$item_body_data='';
		foreach($body_id as $body_part_id=>$data)
		{
			if($item_body_data=='') $item_body_data=$body_part_id; else $item_body_data.='_'.$body_part_id; 
		}
		
		echo "$('#txt_itemBodyData_$item_id').val('".$item_body_data."');\n";
		echo "$('#txt_itemDVAData_$item_id').val('".$itemDVAData_array[$item_id]."');\n";
		echo "$('#txt_itemConsRateData_$item_id').val('".$cons_rate_fab_arr[$item_id]."');\n";
		echo "$('#txt_specialData_$item_id').val('".$cons_rate_sp_arr[$item_id]."');\n";
		echo "$('#txt_itemAccData_$item_id').val('".$cons_rate_ac_arr[$item_id]."');\n";
		if($active_item==1)
		{
			echo "fnc_dtls_ganerate('".$item_id."__".$lib_temp_arr[$item_id]."');\n";
			$active_item++;
		}
	}
	//print_r($item_body_array); 
	//die;
	$sql_summ="select buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, tot_rmg_ratio from qc_tot_cost_summary where mst_id='$cond_data' and status_active=1 and is_deleted=0";
	$sql_result_summ=sql_select($sql_summ);
	
	foreach($sql_result_summ as $rowSumm)
	{
		echo "$('#cbo_buyer_agent').val('".$rowSumm[csf("buyer_agent_id")]."');\n";
		echo "$('#cbo_agent_location').val('".$rowSumm[csf("location_id")]."');\n";
		echo "$('#txt_noOfPack').val('".$rowSumm[csf("no_of_pack")]."');\n";
		//echo "$('#txt_noOfPack').val('".$rowSumm[csf("is_confirm")]."');\n";
		if($rowSumm[csf("is_cm_calculative")]==1)
		{
			echo "$('#cmPop').prop('checked', true);\n";
			echo "fnc_rate_write_popup('cm');\n"; 
		}
		else
		{
			echo "$('#cmPop').prop('checked', false);\n";
			echo "fnc_rate_write_popup('cm');\n"; 
		}
		echo "$('#cmPop').val('".$rowSumm[csf("is_cm_calculative")]."');\n";
		echo "$('#txt_lumSum_cost').val('".$rowSumm[csf("mis_lumsum_cost")]."');\n";
		echo "$('#txt_commPer').val('".$rowSumm[csf("commision_per")]."');\n";
		echo "$('#totFab_td').text('".$rowSumm[csf("tot_fab_cost")]."');\n";
		echo "$('#totSpc_td').text('".$rowSumm[csf("tot_sp_operation_cost")]."');\n";
		echo "$('#totAcc_td').text('".$rowSumm[csf("tot_accessories_cost")]."');\n";
		echo "$('#totCm_td').text('".$rowSumm[csf("tot_cm_cost")]."');\n";
		echo "$('#totFriCst_td').text('".$rowSumm[csf("tot_fright_cost")]."');\n";
		echo "$('#totLbTstCst_td').text('".$rowSumm[csf("tot_lab_test_cost")]."');\n";
		echo "$('#totMissCst_td').text('".$rowSumm[csf("tot_miscellaneous_cost")]."');\n";
		echo "$('#totOtherCst_td').text('".$rowSumm[csf("tot_other_cost")]."');\n";
		echo "$('#totCommCst_td').text('".$rowSumm[csf("tot_commission_cost")]."');\n";
		echo "$('#totCost_td').text('".$rowSumm[csf("tot_cost")]."');\n";
		echo "$('#totalFob_td').text('".$rowSumm[csf("tot_fob_cost")]."');\n";
		echo "$('#totRmgQty_td').text('".$rowSumm[csf("tot_rmg_ratio")]."');\n";
		echo "$('#totFOBCost_td').text('".number_format(($rowSumm[csf("tot_cost")]/12),4)."');\n";
	}
	
	$sql_item_summ="select item_id, fabric_cost, sp_operation_cost, accessories_cost, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs, rmg_ratio from qc_item_cost_summary where mst_id='$cond_data' and status_active=1 and is_deleted=0";
	$sql_result_item_summ=sql_select($sql_item_summ);
	foreach($sql_result_item_summ as $rowItemSumm)
	{
		echo "$('#fab_td".$rowItemSumm[csf("item_id")]."').text('".$rowItemSumm[csf("fabric_cost")]."');\n";
		echo "$('#spOpe_td".$rowItemSumm[csf("item_id")]."').text('".$rowItemSumm[csf("sp_operation_cost")]."');\n";
		echo "$('#acc_td".$rowItemSumm[csf("item_id")]."').text('".$rowItemSumm[csf("accessories_cost")]."');\n";
		echo "$('#txt_smv_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("smv")]."');\n";
		echo "$('#txt_eff_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("efficiency")]."');\n";
		echo "$('#txtCmCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("cm_cost")]."');\n";
		echo "$('#txtFriCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("frieght_cost")]."');\n";
		echo "$('#txtLtstCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("lab_test_cost")]."');\n";
		echo "$('#txtMissCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("miscellaneous_cost")]."');\n";
		echo "$('#txtOtherCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("other_cost")]."');\n";
		echo "$('#txtCommCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("commission_cost")]."');\n";
		
		echo "$('#fobT_td".$rowItemSumm[csf("item_id")]."').text('".$rowItemSumm[csf("fob_pcs")]."');\n";
		echo "$('#txtRmgQty_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("rmg_ratio")]."');\n";
		echo "$('#fobPcsT_td".$rowItemSumm[csf("item_id")]."').text('".number_format(($rowItemSumm[csf("fob_pcs")]/12),4)."');\n";
	}
	if($cond_data!='' || $cond_data!=0)
	{
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_qcosting_entry',1);\n";
	}
	else
	{
		echo "$('#txt_costSheetNo').val('');\n";
		//echo "$('#txt_costSheetNo').focus();\n";
	}
	exit();
}

if($action=="load_lib_item_id")
{
	$sql_item_id="Select lib_item_id from qc_template where temp_id='$data' and status_active=1 and is_deleted=0";
	$lib_item_id="";
	$sql_item_id_res=sql_select($sql_item_id);
	foreach($sql_item_id_res as $row)
	{
		if($lib_item_id=='') $lib_item_id=trim($row[csf("lib_item_id")]); else $lib_item_id.=','.trim($row[csf("lib_item_id")]);
	}
	echo $lib_item_id;
	exit();
}

if($action=="quick_costing_print")
{
	extract($_REQUEST);
	echo load_html_head_contents("Quick Costing Print", "../../../", 1, 1,'','','');
	$data=explode('*',$data);
	//print_r( $data);
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$department_arr=return_library_array( "select id, sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	$season_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
	$lib_temp_arr=return_library_array("select id, item_name from lib_qc_template","id","item_name");
	
	$accessories_arr=return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active =1 and is_deleted=0","id","item_name");
	
	$sql_mst="select id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, style_ref, buyer_id, cons_basis, season_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, revise_no, option_id, option_remarks, buyer_remarks from qc_mst where status_active=1 and is_deleted=0 and id='$data[0]'";
	$sqlMst_res=sql_select($sql_mst);
	
	if($db_type==0) $concat_cond="group_concat(lib_item_id)";
	else if($db_type==2) $concat_cond="listagg(cast(lib_item_id as varchar2(4000)),',') within group (order by lib_item_id)";
	else $concat_cond="";
	$sql_tmp="select temp_id, $concat_cond as lib_item_id from qc_template where status_active=1 and is_deleted=0 group by temp_id order by temp_id ASC";
	$sql_tmp_res=sql_select($sql_tmp);
	//print_r($sql_tmp_res);die;
	$template_name_arr=array();
	foreach($sql_tmp_res as $row)
	{
		$lib_temp_id='';
		
		$ex_temp_id=explode(',',$sqlMst_res[0][csf('lib_item_id')]);
		foreach($ex_temp_id as $lib_id)
		{
			if($lib_temp_id=="") $lib_temp_id=$lib_temp_arr[$lib_id]; else $lib_temp_id.=','.$lib_temp_arr[$lib_id];
		}
		$template_name_arr[$sqlMst_res[0][csf('temp_id')]]=$lib_temp_id;
	}
	?>
    <div style="width:1000px;">
        <table width="1000" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
            <tr>
                <td colspan="8" align="center" style="font-size:18px"><strong><? //echo $company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr class="form_caption">
            	<td colspan="8" align="center" style="font-size:14px"><strong>ESTIMATE COST SHEET</strong></td>
            </tr>
            <tr>
                <td width="100"><strong>Buyer:</strong></td> <td width="145px"><? echo $buyer_arr[$sqlMst_res[0][csf('buyer_id')]]; ?></td>
                <td width="100"><strong>Department:</strong></td><td width="145px"><? echo $department_arr[$sqlMst_res[0][csf('department_id')]]; ?></td>
                <td width="100"><strong>Template Name:</strong></td><td width="145px"><? echo $template_name_arr[$sqlMst_res[0][csf('temp_id')]]; ?></td>
                <td width="100"><strong>Cons. Basis:</strong></td><td width="145px"><? echo $qc_consumption_basis[$sqlMst_res[0][csf('cons_basis')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Cost Sheet No:</strong></td><td><? echo $sqlMst_res[0][csf('cost_sheet_no')]; ?></td>
                <td><strong>Season:</strong></td><td><? echo $season_arr[$sqlMst_res[0][csf('season_id')]]; ?></td>
                <td><strong>Style Ref.:</strong></td><td><? echo $sqlMst_res[0][csf('style_ref')]; ?></td>
                <td><strong>Costing Date:</strong></td><td><? echo change_date_format($sqlMst_res[0][csf('costing_date')]); ?></td>
            </tr>
            <tr style="border-bottom:1px solid black;">
                <td><strong>Offer Qty.:</strong></td><td><? echo number_format($sqlMst_res[0][csf('offer_qty')],2,'.',''); ?></td>
                <td><strong>Target Price($):</strong></td><td><? echo number_format($sqlMst_res[0][csf('tgt_price')],3,'.',''); ?></td>
                <td><strong>Quoted Price($):</strong></td><td><? echo number_format($sqlMst_res[0][csf('quoted_price')],3,'.',''); ?></td>
                <td><strong>Exchange Rate:</strong></td><td><? echo number_format($sqlMst_res[0][csf('exchange_rate')],3,'.',''); ?></td>
            </tr>
            <tr style="border-bottom:1px solid black;">
                <td><strong>Style Description:</strong></td><td><? echo $sqlMst_res[0][csf('style_des')]; ?></td>
                <td><strong>Revise/Option No:</strong></td><td><? echo 'R:- '.$sqlMst_res[0][csf('revise_no')]."/ O:- ".$sqlMst_res[0][csf('option_id')]; ?></td>
                <td><strong>&nbsp;</strong></td><td>&nbsp;</td>
                <td><strong>&nbsp;</strong></td><td>&nbsp;</td>
            </tr>
        </table>
        <table width="1000" cellspacing="0" align="right" border="1" rules="all" class="rpt_table">
        	<?
			$sql_fab_dtls="Select id, item_id, uniq_id, body_part, des, value, alw from qc_fabric_dtls where status_active=1 and is_deleted=0 and mst_id='$data[0]'";
			$sql_result_fab_dtls=sql_select($sql_fab_dtls);
			$fabric_details_arr=array(); $dtls_arr=array(); $templateItem_arr=array(); $i=1; $k=1; $n=1;
			foreach($sql_result_fab_dtls as $rowFab)
			{
				$fabric_details_arr[$rowFab[csf('item_id')]].=$rowFab[csf('item_id')].'##'.$rowFab[csf('body_part')].'##'.$rowFab[csf('des')].'##'.$rowFab[csf('value')].'##'.$rowFab[csf('alw')].'##'.$rowFab[csf('uniq_id')].'__';
				$dtls_arr[$rowFab[csf('uniq_id')]]['des']=$rowFab[csf('des')];
				$dtls_arr[$rowFab[csf('uniq_id')]]['val']=$rowFab[csf('value')];
				$dtls_arr[$rowFab[csf('uniq_id')]]['alw']=$rowFab[csf('alw')];
				//$fabric_details_arr[$rowFab[csf('item_id')]]['body']=$rowFab[csf('body_part')];
				$templateItem_arr[$rowFab[csf('item_id')]].=$rowFab[csf('body_part')];
				
			}
				
			unset($sql_result_fab_dtls); $i=1; $k=1; $n=1;
			$itemIndex_arr=array();
			$mk=0;
			$nk=11;
			foreach($templateItem_arr as $item_id=>$val)
			{
				$item_name=$lib_temp_arr[$item_id];
				if(strpos($item_name,'BTS') !== false)
				{
					$itemIndex_arr[$mk]=$item_id;
					$mk++;
				}
				else
				{
					$itemIndex_arr[$nk]=$item_id;
					$nk++;
				}
			}
			ksort($itemIndex_arr);
			//print_r($narr); die;
			foreach($itemIndex_arr as $ind=>$item_id)
			{
				//$item_id;
				$item_name='';
				$val=explode('__',$fabric_details_arr[$item_id]);
				//print_r($val);
				//die;
				
				$item_name=$lib_temp_arr[$item_id];
				if(strpos($item_name,'BTS') !== false)
				{
					if($k==1)
					{
						?>
						<thead>
							<th width="70">Item Name</th>
							<th width="90">Main Fabric Top</th>
                            <th width="50">VLU</th>
                            <th width="50">ALW</th>
                            <th width="80">Rib</th>
                            <th width="50">VLU</th>
                            <th width="50">ALW</th>
                            <th width="80">HOOD</th>
                            <th width="50">VLU</th>
                            <th width="50">ALW</th>
                            <th width="80">Others</th>
                            <th width="50">VLU</th>
                            <th width="50">ALW</th>
                            <th width="80">Yds</th>
                            <th width="50">VLU</th>
                            <th>ALW</th>
						<thead>
							<?
						$k++;
					}
					?>
					<tr>
                    	<td rowspan="5" align="center"><? echo $item_name; ?></td>
                        <? $u=5; 
						for($j=1; $j<=$u; $j++) {?>
                        <td><? echo $dtls_arr[$item_id.'_'.'1'.'_'.$j]['des']; ?></td>
                        <td align="right"><? echo $dtls_arr[$item_id.'_'.'1'.'_'.$j]['val']; ?>&nbsp;</td>
                        <td align="right"><? echo $dtls_arr[$item_id.'_'.'1'.'_'.$j]['alw']; ?>&nbsp;</td>
                        <? } ?>
                    </tr>
                    <? $z=4; $x=5; $y=2;
					for($j=2; $j<=$x; $j++) {?>
                    <tr>
                    	<? for($a=1; $a<=$x; $a++) {?>
                        <td><? echo $dtls_arr[$item_id.'_'.$j.'_'.$a]['des']; ?></td>
                        <td align="right"><? echo $dtls_arr[$item_id.'_'.$j.'_'.$a]['val']; ?>&nbsp;</td>
                        <td align="right"><? echo $dtls_arr[$item_id.'_'.$j.'_'.$a]['alw']; ?>&nbsp;</td>
                        <? } ?>
                    </tr>
                    <? $z++; } ?>
                    
				<?
				}
				else if(strpos($item_name,'BTM') !== false)
				{
					if($n==1)
					{
					?>
					<thead>
						<th width="70">Item Name</th>
						<th width="90">Main Fabric Bottom</th>
						<th width="50">VLU</th>
						<th width="50">ALW</th>
						<th width="80">Rib</th>
						<th width="50">VLU</th>
						<th width="50">ALW</th>
						<th width="80">Pocketing</th>
						<th width="50">VLU</th>
						<th width="50">ALW</th>
						<th width="80">Others</th>
						<th width="50">VLU</th>
						<th width="50">ALW</th>
						<th width="80">Yds</th>
						<th width="50">VLU</th>
						<th>ALW</th>
					<thead>
					<?
					$n++;
					}
					
					?>
					<tr>
						<td rowspan="5" align="center"><? echo $item_name; ?></td>
						<? $u=5; 
						for($j=1; $j<=$u; $j++) {?>
						<td><? echo $dtls_arr[$item_id.'_'.'1'.'_'.$j]['des']; ?></td>
						<td align="right"><? echo $dtls_arr[$item_id.'_'.'1'.'_'.$j]['val']; ?>&nbsp;</td>
						<td align="right"><? echo $dtls_arr[$item_id.'_'.'1'.'_'.$j]['alw']; ?>&nbsp;</td>
						<? } ?>
					</tr>
					<? $z=4; $x=5; $y=2;
					for($j=2; $j<=$x; $j++) {?>
					<tr>
						<? for($a=1; $a<=$x; $a++) {?>
						<td><? echo $dtls_arr[$item_id.'_'.$j.'_'.$a]['des']; ?></td>
						<td align="right"><? echo $dtls_arr[$item_id.'_'.$j.'_'.$a]['val']; ?>&nbsp;</td>
						<td align="right"><? echo $dtls_arr[$item_id.'_'.$j.'_'.$a]['alw']; ?>&nbsp;</td>
						<? } ?>
					</tr>
					<? $z++; } 
				}
				$i++;
			}
			?>
        </table>
        <div><br><br>
        <table width="1000" cellspacing="0" align="right" >
        	<tr>
            	<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
            </tr>
        	<tr>
            <?
			$sql_cons_rate="select id, item_id, type, particular_type_id, consumption, unit, is_calculation, rate, rate_data, value from qc_cons_rate_dtls where mst_id='$data[0]' and status_active=1 and is_deleted=0 order by id asc";
			$sql_result_cons_rate=sql_select($sql_cons_rate); $item_arr=array(); $cons_rate_fab_arr=array(); $cons_rate_sp_arr=array(); $cons_rate_ac_arr=array();
			foreach ($sql_result_cons_rate as $rowConsRate)
			{
				$item_arr[$rowConsRate[csf("item_id")]]=$rowConsRate[csf("item_id")];
				if($rowConsRate[csf("type")]==1)
				{
					$cons_rate_fab_arr[$rowConsRate[csf("item_id")]].=$rowConsRate[csf("particular_type_id")].'_'.$rowConsRate[csf("consumption")].'_'.$rowConsRate[csf("unit")].'_'.$rowConsRate[csf("rate")].'_'.$rowConsRate[csf("rate_data")].'##';
				}
				
				if($rowConsRate[csf("particular_type_id")]!='')
				{
					if($rowConsRate[csf("particular_type_id")]!=0)
					{
						if($rowConsRate[csf("type")]==2)
						{
							$cons_rate_sp_arr[$rowConsRate[csf("item_id")]].=$rowConsRate[csf("particular_type_id")].'_'.$rowConsRate[csf("consumption")].'_'.$rowConsRate[csf("unit")].'_'.$rowConsRate[csf("rate")].'##';
						}
						
						if($rowConsRate[csf("type")]==3)
						{
							$cons_rate_ac_arr[$rowConsRate[csf("item_id")]].=$rowConsRate[csf("particular_type_id")].'_'.$rowConsRate[csf("consumption")].'_'.$rowConsRate[csf("unit")].'_'.$rowConsRate[csf("rate")].'##';
						}
					}
				}
			}
			?>
            	<td colspan="3">
                <? foreach($itemIndex_arr as $ind=>$itemId) { ?>
                	<table width="650" cellspacing="0" border="1" rules="all" class="rpt_table">
                    	<thead>
                        	<th width="80">Item</th>
                            <th width="180">Item Description</th>
                            <th width="70">Unit</th>
                            <th width="80">Cons.</th>
                            <th width="70">U.Price</th>
                            <th>Value($)</th>
                        </thead>
                        <? $fabric_data=''; $special_data=''; $accessories_data='';
						$count_fab=0; $count_sp=0; $count_acc=0;
						$fabric_data=array_filter(explode('##',$cons_rate_fab_arr[$itemId]));
						$special_data=array_filter(explode('##',$cons_rate_sp_arr[$itemId]));
						$accessories_data=array_filter(explode('##',$cons_rate_ac_arr[$itemId]));
						$count_fab=count($fabric_data); $count_sp=count($special_data); $count_acc=count($accessories_data);
						$colspan=$count_fab+$count_sp+$count_acc;
						$item_name=$lib_temp_arr[$itemId]; $q=1; $item_wise_value_total=0;
						foreach($fabric_data as $fabData)
						{
							//echo $fabData.'===';
							$exFab_data=explode('_',$fabData);
							$bodyName='';
							if($exFab_data[0]!=0)
							{
								$bodyName=$body_part[$exFab_data[0]];
							}
							
							if(trim($exFab_data[0])==998)
							{
								$bodyName='Others';
							}
							else if(trim($exFab_data[0])==999)
							{
								$bodyName='Yds';
							}
							$row_value=$exFab_data[1]*$exFab_data[3];
							$item_wise_value_total+=$row_value;
							?>
							<tr>
                            	<? if($q==1)
								{ ?>
								<td rowspan="<? echo $colspan; ?>" align="center"><? echo $item_name; ?></td>
                                <? } ?>
                                <td><? echo $bodyName; ?></td>
                                <td><? echo $unit_of_measurement[$exFab_data[2]]; ?></td>
                                <td align="right"><? echo number_format($exFab_data[1],4); ?></td>
                                <td align="right"><? echo number_format($exFab_data[3],2); ?></td>
                                <td align="right"><? echo number_format($row_value,4); ?></td>
							</tr>
                        <?  $q++; } 
						foreach($special_data as $spData)
						{
							$exSp_data=explode('_',$spData);
							$rowSp_value=$exSp_data[1]*$exSp_data[3];
							$item_wise_value_total+=$rowSp_value;
							?>
							<tr>
                                <td><? echo $emblishment_name_array[$exSp_data[0]]; ?></td>
                                <td><? echo $unit_of_measurement[$exSp_data[2]]; ?></td>
                                <td align="right"><? echo number_format($exSp_data[1],4); ?></td>
                                <td align="right"><? echo number_format($exSp_data[3],2); ?></td>
                                <td align="right"><? echo number_format($rowSp_value,4); ?></td>
							</tr>
                        <?  $q++; } 
						foreach($accessories_data as $acData)
						{
							$exAc_data=explode('_',$acData);
							$rowAc_value=$exAc_data[1]*$exAc_data[3];
							$item_wise_value_total+=$rowAc_value;
							?>
							<tr>
                                <td><? echo $accessories_arr[$exAc_data[0]]; ?></td>
                                <td><? echo $unit_of_measurement[$exAc_data[2]]; ?></td>
                                <td align="right"><? echo number_format($exAc_data[1],4); ?></td>
                                <td align="right"><? echo number_format($exAc_data[3],2); ?></td>
                                <td align="right"><? echo number_format($rowAc_value,4); ?></td>
							</tr>
                        <?  $q++; } ?>
                        
                        <tr>
                        	<td colspan="5" align="right"><strong>Total</strong></td>
                            <td align="right"><? echo number_format($item_wise_value_total,4); ?>&nbsp;</td>
                        </tr>
                    </table><br>
                    <table width="850" cellspacing="0" border="1" rules="all" class="rpt_table">
                    	<tr>
                        	<td width="80"><strong>Agent</strong></td>
                            <td width="120">&nbsp;</td>
                            <td rowspan="3">&nbsp;</td>
                        </tr>
                        <tr>
                        	<td><strong>Location</strong></td>
                            <td width="120">&nbsp;</td>
                        </tr>
                        <tr>
                        	<td><strong>Date</strong></td>
                            <td  width="120">&nbsp;</td>
                        </tr>
                    </table><br>
                    <? } ?>
                </td>
            </tr>
        </table>
        </div><br>
        <table width="850" cellspacing="0" border="1" rules="all" class="rpt_table">
        	<thead>
            	<th width="70">Item</th>
                <th width="60">Fabric</th>
                <th width="60">Special Operation</th>
                <th width="60">Accessories</th>
                <th width="60">CM</th>
                <th width="60">Frieght Cost</th>
                <th width="60">Lab - Test</th>
                <th width="60">Miscellaneous</th>
                <th width="60">Other Cost</th>
                <th width="60">Commission</th>
                <th width="60">F.O.B($/DZN)</th>
                <th width="60">F.O.B($/PCS)</th>
                <th>RMG Qty(Pcs)</th>
            </thead>
			<?
            $sql_item_summ="select item_id, fabric_cost, sp_operation_cost, accessories_cost, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs from qc_item_cost_summary where mst_id='$data[0]' and status_active=1 and is_deleted=0";
            $sql_result_item_summ=sql_select($sql_item_summ);
            foreach($sql_result_item_summ as $rowItemSumm)
            {
                ?>
                <tr>
                    <td><? echo $lib_temp_arr[$rowItemSumm[csf("item_id")]]; ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("fabric_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("sp_operation_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("accessories_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("cm_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("frieght_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("lab_test_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("miscellaneous_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("other_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("commission_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("fob_pcs")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("fob_pcs")]/12,4); ?></td>
                    <td align="right"><? //echo number_format($rowItemSumm[csf("sp_operation_cost")],4); ?></td>
                </tr>
                <?
				$tot_fabric+=$rowItemSumm[csf("fabric_cost")];
				$tot_spOPar+=$rowItemSumm[csf("sp_operation_cost")];
				$tot_acc+=$rowItemSumm[csf("accessories_cost")];
				$tot_cm+=$rowItemSumm[csf("cm_cost")];
				$tot_fright+=$rowItemSumm[csf("frieght_cost")];
				$tot_lab+=$rowItemSumm[csf("lab_test_cost")];
				$tot_miss+=$rowItemSumm[csf("miscellaneous_cost")];
				$tot_other+=$rowItemSumm[csf("other_cost")];
				$tot_comm+=$rowItemSumm[csf("commission_cost")];
				$tot_fob_dzn+=$rowItemSumm[csf("fob_pcs")];
            }
            ?>
            <tr>
            	<td><strong>Total</strong></td>
                <td align="right"><? echo number_format($tot_fabric,4); ?></td>
                <td align="right"><? echo number_format($tot_spOPar,4); ?></td>
                <td align="right"><? echo number_format($tot_acc,4); ?></td>
                <td align="right"><? echo number_format($tot_cm,4); ?></td>
                <td align="right"><? echo number_format($tot_fright,4); ?></td>
                <td align="right"><? echo number_format($tot_lab,4); ?></td>
                <td align="right"><? echo number_format($tot_miss,4); ?></td>
                <td align="right"><? echo number_format($tot_other,4); ?></td>
                <td align="right"><? echo number_format($tot_comm,4); ?></td>
                <td align="right"><? echo number_format($tot_fob_dzn,4); ?></td>
                <td align="right"><? echo number_format($tot_fob_dzn/12,4); ?></td>
                <td align="right"><? //echo number_format($rowItemSumm[csf("fob_pcs")],4); ?>&nbsp;</td>
            </tr>
        </table>
        <br>
        <table width="400" cellspacing="0" border="1" rules="all" class="rpt_table">
        	<? 
			$sql_summ="select no_of_pack, tot_fob_cost from qc_tot_cost_summary where mst_id='$data[0]' and status_active=1 and is_deleted=0";
			$sql_result_summ=sql_select($sql_summ);
			?>
            <tr>
            	<td width="120">No Of Pack:</td><td width="80" align="center"><? echo $sql_result_summ[0][csf('no_of_pack')]; ?></td>
                <td width="120">Unit Price:</td><td align="center"><? echo number_format($sql_result_summ[0][csf('tot_fob_cost')],4); ?></td>
            </tr>
        </table>
        
        <br>
        <table width="400" cellspacing="0" border="1" rules="all" class="rpt_table">
        	<? 
			$buyer_remarks = str_replace("\n", "\\n", $sqlMst_res[0][csf('buyer_remarks')]);
			?>
            <thead>
            	<th>Merchandiser Remarks</th>
            </thead>
            <tr>
                <td><? echo $buyer_remarks; ?></td>
            </tr>
        </table>
        <table width="400" cellspacing="0" border="1" rules="all" class="rpt_table">
        	<? 
			$option_remarks = $sqlMst_res[0][csf('option_remarks')];
			?>
            <thead>
            	<th>Option Remarks</th>
            </thead>
            <tr>
                <td><? echo $option_remarks; ?></td>
            </tr>
        </table>
        </div>
		<?
	exit();
}

if($action=="copy_cost_sheet")
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
		//echo "0**";
		$update_id=str_replace("'","",$txt_update_id); //die;
		$dup_styleRef=str_replace("'","",$txt_styleRef);
		$dup_season_id=str_replace("'","",$cbo_season_id);
		$dup_buyer_id=str_replace("'","",$cbo_buyer_id);
		$msg="Duplicate Styel Ref, Buyer, Season.";
		
		if (is_duplicate_field( "cost_sheet_no", "qc_mst", "style_ref='$dup_styleRef' and season_id='$dup_season_id' and buyer_id='$dup_buyer_id'" ) == 1)
		{
			echo "11**".$msg; 
			die;
		}
		//all_table qc_template, qc_mst, qc_style_temp_data, qc_fabric_dtls, qc_fabric_string_data, qc_cons_rate_dtls, qc_tot_cost_summary, qc_item_cost_summary, qc_meeting_mst, qc_meeting_person, qc_meeting_dtls
		//echo $txtItemDes_1_1_1; die;
		
		$mst_id=return_next_id("id", "qc_mst", 1);
		$sql_mst=sql_select("select id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, style_ref, buyer_id, cons_basis, season_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, buyer_remarks from qc_mst where id='$update_id'");
		//echo "select id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, style_ref, buyer_id, cons_basis, season_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date from qc_mst where id='$update_id'";
		
		$cbo_temp_id=$sql_mst[0][csf('temp_id')];
		$txt_temp_id=$sql_mst[0][csf('lib_item_id')];
		//$txt_styleRef=$sql_mst[0][csf('style_ref')];
		$cbo_buyer_id=$sql_mst[0][csf('buyer_id')];
		$cbo_cons_basis_id=$sql_mst[0][csf('cons_basis')];
		//$cbo_season_id=$sql_mst[0][csf('season_id')];
		$txt_styleDesc=$sql_mst[0][csf('style_des')];
		$cbo_subDept_id=$sql_mst[0][csf('department_id')];
		$txt_delivery_date=$sql_mst[0][csf('delivery_date')];
		$txt_exchangeRate=$sql_mst[0][csf('exchange_rate')];
		$txt_offerQty=$sql_mst[0][csf('offer_qty')];
		$txt_quotedPrice=$sql_mst[0][csf('quoted_price')];
		$txt_tgtPrice=$sql_mst[0][csf('tgt_price')];
		$cbo_stage_id=$sql_mst[0][csf('stage_id')];
		$txt_costingDate=$sql_mst[0][csf('costing_date')];
		$txt_costing_remarks=$sql_mst[0][csf('buyer_remarks')];
		$old_id=$sql_mst[0][csf('id')];
		
		$field_array_mst=" id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, style_ref, buyer_id, cons_basis, season_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, buyer_remarks, revise_no, option_id, inserted_by, insert_date";
		
		//$txt_temp_id=explode(",",str_replace("'","",$txt_temp_id));
		//asort($txt_temp_id);
		//$txt_temp_id="'".implode(",",$txt_temp_id)."'";
		$option_id=0;
		$revise_no=0;
		$autoCostSheetNo=$_SESSION['logic_erp']['user_id'].str_pad($mst_id,8,'0',STR_PAD_LEFT);
		
		$data_array_mst="(".$mst_id.",".$mst_id.",'".$autoCostSheetNo."','".$cbo_temp_id."','".$txt_temp_id."',".$txt_styleRef.",'".$cbo_buyer_id."','".$cbo_cons_basis_id."',".$cbo_season_id.",'".$txt_styleDesc."','".$cbo_subDept_id."','".$txt_delivery_date."','".$txt_exchangeRate."','".$txt_offerQty."','".$txt_quotedPrice."','".$txt_tgtPrice."','".$cbo_stage_id."','".$txt_costingDate."','".$txt_costing_remarks."','".$revise_no."','".$option_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//echo $data_array; die;
		//$template_id=return_next_id("id", "qc_template", 1);
		//$template_id = return_id( $txt_temp_id, $template_library, "qc_template", "id,item_id");
		$dtls_id=return_next_id("id", "qc_fabric_dtls", 1);
		$temp_id=explode(",",str_replace("'","",$txt_temp_id));
		$sql_fab=sql_select("select id, mst_id, item_id, uniq_id, body_part, des, value, alw from qc_fabric_dtls where mst_id='$update_id' order by id ASC");
		//echo "select id, mst_id, item_id, uniq_id, body_part, des, value, alw from qc_fabric_dtls where mst_id='$update_id' order by id ASC"; die;
		$field_array_fab="id, mst_id, item_id, uniq_id, body_part, des, value, alw, inserted_by, insert_date";
   		//$fstr_id=return_next_id("id", "qc_fabric_string_data", 1); 
		$add_comma_fab=0; $data_array_fab="";
		foreach( $sql_fab as $rowFab )
		{
			$old_fab_id=$rowFab[csf('id')];
			$item_id=$rowFab[csf('item_id')];
			$uniq_id=$rowFab[csf('uniq_id')];
			$body_part_id=$rowFab[csf('body_part')];
			$txtItemDes=$rowFab[csf('des')];
			$txtVal=$rowFab[csf('value')];
			$txtAw=$rowFab[csf('alw')];
			
			if ($add_comma_fab!=0) $data_array_fab .=",";
			$data_array_fab .="(".$dtls_id.",".$mst_id.",'".$item_id."','".$uniq_id."','".$body_part_id."','".$txtItemDes."','".$txtVal."','".$txtAw."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$add_comma_fab++;
			$dtls_id=$dtls_id+1;
		}
		
		$cons_rate_id=return_next_id("id", "qc_cons_rate_dtls", 1);
		$sql_consrate=sql_select("select id, mst_id, item_id, type, particular_type_id, formula, consumption, unit, is_calculation, rate, rate_data, value from qc_cons_rate_dtls where mst_id='$update_id' order by id ASC");
		$field_array_consrate="id, mst_id, item_id, type, particular_type_id, formula, consumption, unit, is_calculation, rate, rate_data, value, inserted_by, insert_date";
		$add_comma_cons_rate=0; $data_array_cons_rate="";
		foreach( $sql_consrate as $rowConsrate )
		{
			$old_consrate_id=$rowConsrate[csf('id')];
			$item_id=$rowConsrate[csf('item_id')];
			$type=$rowConsrate[csf('type')];
			$head_id=$rowConsrate[csf('particular_type_id')];
			$formula=$rowConsrate[csf('formula')];
			$cons=$rowConsrate[csf('consumption')];
			$unit_id=$rowConsrate[csf('unit')];
			$is_rate_cal=$rowConsrate[csf('is_calculation')];
			$rate=$rowConsrate[csf('rate')];
			$rateData=$rowConsrate[csf('rate_data')];
			$tot_val=$rowConsrate[csf('value')];
			
			if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
			$data_array_cons_rate .="(".$cons_rate_id.",".$mst_id.",'".$item_id."','".$type."','".$head_id."','".$formula."','".$cons."','".$unit_id."','".$is_rate_cal."','".$rate."','".$rateData."','".$tot_val."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$cons_rate_id=$cons_rate_id+1;
			$add_comma_cons_rate++;
		}
		
		
		//echo $data_array_cons_rate; 
		//die;
		$tc_sum_id=return_next_id("id", "qc_tot_cost_summary", 1);
		$sql_tot_cost=sql_select("select id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost from qc_tot_cost_summary where mst_id='$update_id'");
		
		$field_array_tot_cost="id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, inserted_by, insert_date";
		
		//$ex_tot_cost_data=explode('_',$data_tot_cost_summ);
		
		$cbo_buyer_agent=$sql_tot_cost[0][csf('buyer_agent_id')];
		$cbo_agent_location=$sql_tot_cost[0][csf('location_id')];
		$txt_noOfPack=$sql_tot_cost[0][csf('no_of_pack')];
		$cmPop=$sql_tot_cost[0][csf('is_cm_calculative')];
		//$txt_smv=trim($ex_tot_cost_data[4]);
		//$txt_eff=trim($ex_tot_cost_data[5]);
		$txt_lumSum_cost=$sql_tot_cost[0][csf('mis_lumsum_cost')];
		$txt_commPer=$sql_tot_cost[0][csf('commision_per')];
		$totFab_td=$sql_tot_cost[0][csf('tot_fab_cost')];
		$totSpc_td=$sql_tot_cost[0][csf('tot_sp_operation_cost')];
		$totAcc_td=$sql_tot_cost[0][csf('tot_accessories_cost')];
		$totCm_td=$sql_tot_cost[0][csf('tot_cm_cost')];
		$totFriCst_td=$sql_tot_cost[0][csf('tot_fright_cost')];
		$totLbTstCst_td=$sql_tot_cost[0][csf('tot_lab_test_cost')];
		$totMissCst_td=$sql_tot_cost[0][csf('tot_miscellaneous_cost')];
		$totOtherCst_td=$sql_tot_cost[0][csf('tot_other_cost')];
		$totCommCst_td=$sql_tot_cost[0][csf('tot_commission_cost')];
		$totCost_td=$sql_tot_cost[0][csf('tot_cost')];
		$totalFob_td=$sql_tot_cost[0][csf('tot_fob_cost')];
		
		$data_array_tot_cost="(".$tc_sum_id.",".$mst_id.",'".$cbo_buyer_agent."','".$cbo_agent_location."','".$txt_noOfPack."',0,'".$cmPop."','".$txt_lumSum_cost."','".$txt_commPer."','".$totFab_td."','".$totSpc_td."','".$totAcc_td."','".$totCm_td."','".$totFriCst_td."','".$totLbTstCst_td."','".$totMissCst_td."','".$totOtherCst_td."','".$totCommCst_td."','".$totCost_td."','".$totalFob_td."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//echo $data_array_tot_cost; die;
		$sql_item_tot=sql_select("select id, mst_id, item_id, fabric_cost, sp_operation_cost, accessories_cost, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs from qc_item_cost_summary where mst_id='$update_id' order by id ASC");
		$field_array_item_tot=" id, mst_id, item_id, fabric_cost, sp_operation_cost, accessories_cost, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs, inserted_by, insert_date";
		$item_cost_id=return_next_id("id", "qc_item_cost_summary", 1);
		$add_comma_item_tot=0; $data_array_item_tot="";
		foreach($sql_item_tot as $rowItem_tot)
		{
			$fab_td=$spOpe_td=$acc_td=$txtSmv=$txtEff=$txtCmCost=$txtFriCost=$txtLtstCost=$txtMissCost=$txtOtherCost=$txtCommCost=$fobT_td=$tot_item_id=0;
			$fab_td=$rowItem_tot[csf('fabric_cost')];
			$spOpe_td=$rowItem_tot[csf('sp_operation_cost')];
			$acc_td=$rowItem_tot[csf('accessories_cost')];
			$txtSmv=$rowItem_tot[csf('smv')];
			$txtEff=$rowItem_tot[csf('efficiency')];
			$txtCmCost=$rowItem_tot[csf('cm_cost')];
			$txtFriCost=$rowItem_tot[csf('frieght_cost')];
			$txtLtstCost=$rowItem_tot[csf('lab_test_cost')];
			$txtMissCost=$rowItem_tot[csf('miscellaneous_cost')];
			$txtOtherCost=$rowItem_tot[csf('other_cost')];
			$txtCommCost=$rowItem_tot[csf('commission_cost')];
			$fobT_td=$rowItem_tot[csf('fob_pcs')];
			$tot_item_id=$rowItem_tot[csf('item_id')];
			
			if ($add_comma_item_tot!=0) $data_array_item_tot .=",";
			$data_array_item_tot .="(".$item_cost_id.",".$mst_id.",'".$tot_item_id."','".$fab_td."','".$spOpe_td."','".$acc_td."','".$txtSmv."','".$txtEff."','".$txtCmCost."','".$txtFriCost."','".$txtLtstCost."','".$txtMissCost."','".$txtOtherCost."','".$txtCommCost."','".$fobT_td."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$item_cost_id=$item_cost_id+1;
			$add_comma_item_tot++;
		}
		
		
		
		//echo "insert into qc_tot_cost_summary (".$field_array_tot_cost.") values ".$data_array_tot_cost;die;
		/*$meetMst_id=return_next_id("id", "qc_meeting_mst", 1);
		$meetPer_id=return_next_id("id", "qc_meeting_mst", 1);
		$meetDtls_id=return_next_id("id", "qc_meeting_mst", 1);*/
		
		//echo "0**"."insert into qc_item_cost_summary (".$field_array_item_tot.") values ".$data_array_item_tot; die;
		
		$rID=sql_insert("qc_mst",$field_array_mst,$data_array_mst,0);	
		if($rID) $flag=1; else $flag=0;
		
		$rID_fab=sql_insert("qc_fabric_dtls",$field_array_fab,$data_array_fab,0);	
		//if($rID_fab) $flag=1; else $flag=0;
		$rID_consRate=sql_insert("qc_cons_rate_dtls",$field_array_consrate,$data_array_cons_rate,0);	
		if($rID_consRate) $flag=1; else $flag=0;
		
		$rID_tot_cost=sql_insert("qc_tot_cost_summary",$field_array_tot_cost,$data_array_tot_cost,0);	
		if($rID_tot_cost) $flag=1; else $flag=0;
		
		$rID_item_tot=sql_insert("qc_item_cost_summary",$field_array_item_tot,$data_array_item_tot,0);	
		if($rID_item_tot) $flag=1; else $flag=0;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				//echo "0**".str_replace("'",'',$mst_id).'**'.str_replace("'",'',$autoCostSheetNo);
				echo "0**".str_replace("'",'',$mst_id).'**'.str_replace("'",'',$autoCostSheetNo).'**'.str_replace("'",'',$revise_no).'**'.str_replace("'",'',$option_id).'**1';
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				//echo "0**".str_replace("'",'',$mst_id).'**'.str_replace("'",'',$autoCostSheetNo);
				echo "0**".str_replace("'",'',$mst_id).'**'.str_replace("'",'',$autoCostSheetNo).'**'.str_replace("'",'',$revise_no).'**'.str_replace("'",'',$option_id).'**1';
			}
			else
			{
				oci_rollback($con); 
				echo "10**";
			}
		}
		disconnect($con);
		die;	
	}
}

if($action=="confirmStyle_popup")
{
	echo load_html_head_contents("Confirm Style PopUp","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	$permission=$_SESSION['page_permission'];
	//echo $data;
	$sql_data=sql_select("Select cost_sheet_no, buyer_id, season_id, department_id, temp_id, lib_item_id, style_ref, offer_qty, revise_no, option_id, delivery_date from qc_mst where id='$data' ");
	
	$lib_temp_arr=return_library_array("select id, item_name from lib_qc_template","id","item_name");
	if($db_type==0) $concat_cond="group_concat(lib_item_id)";
	else if($db_type==2) $concat_cond="listagg(cast(lib_item_id as varchar2(4000)),',') within group (order by lib_item_id)";
	else $concat_cond="";
	$sql_tmp="select temp_id, $concat_cond as lib_item_id from qc_template where status_active=1 and is_deleted=0 group by temp_id order by temp_id ASC";
	$sql_tmp_res=sql_select($sql_tmp);
	//print_r($sql_tmp_res);die;
	$template_name_arr=array();
	foreach($sql_tmp_res as $row)
	{
		$lib_temp_id='';
		
		$ex_temp_id=explode(',',$sql_data[0][csf('lib_item_id')]);
		foreach($ex_temp_id as $lib_id)
		{
			if($lib_temp_id=="") $lib_temp_id=$lib_temp_arr[$lib_id]; else $lib_temp_id.=','.$lib_temp_arr[$lib_id];
		}
		$template_name_arr[$sql_data[0][csf('temp_id')]]=$lib_temp_id;
	}
	$gmt_type_arr=array(1=>'Pcs',2=>'Set');
	$gmt_itm_count=count(explode(',',$template_name_arr[$sql_data[0][csf('temp_id')]]));
	$selected_gmt_type=0;
	if($gmt_itm_count>1)
		$selected_gmt_type=2;
	else
		$selected_gmt_type=1;
	
	$sql_summ=sql_select("select no_of_pack, tot_fob_cost from qc_tot_cost_summary where mst_id='$data' and status_active=1 and is_deleted=0");
	//$sql_cons_rate="select id, item_id, type, particular_type_id, consumption, unit, is_calculation, rate, rate_data, value from qc_cons_rate_dtls where mst_id='$data[0]' and status_active=1 and is_deleted=0 order by id asc";
	$sql_cons=sql_select("select item_id, sum(CASE WHEN particular_type_id in (1,20,4,6,7,998) THEN consumption ELSE 0 END) as qty_kg, sum(CASE WHEN particular_type_id=999 THEN consumption ELSE 0 END) as qty_yds from qc_cons_rate_dtls where mst_id='$data' and type=1 group by item_id");//type ='1' and
	$item_wise_cons_arr=array();
	foreach($sql_cons as $cRow)
	{
		$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_kg']=$cRow[csf("qty_kg")];
		$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_yds']=$cRow[csf("qty_yds")];
	}
	//$sql_result_summ=sql_select($sql_summ);
	//print_r($item_wise_cons_arr);
	?>
    <script>
		var permission='<? echo $permission; ?>'; 
		function fnc_confirm_entry( operation )
		{
/*			if(form_validation('txt_stage_name','Stage Name')==false)
			{
				return;   
			}
			else
			{
*/				
				var temp_id=$('#txtItem_id').val();
				var split_tmep_id=temp_id.split(',');
				var data_mst=''; var data_dtls=''; var k=0;
				for(i=1; i<=split_tmep_id.length; i++)
				{
					var itm_id=trim(split_tmep_id[k]);
					data_dtls+=get_submitted_data_string('txtitemid_'+itm_id+'*txtdtlsupid_'+itm_id+'*txtFabConkg_'+itm_id+'*txtFabConyds_'+itm_id+'*txtFabCst_'+itm_id+'*txtSpOpa_'+itm_id+'*txtAcc_'+itm_id+'*txtFrightCst_'+itm_id+'*txtLabCst_'+itm_id+'*txtMiscCst_'+itm_id+'*txtOtherCst_'+itm_id+'*txtCommCst_'+itm_id+'*txtFobDzn_'+itm_id+'*txtCmCst_'+itm_id+'*txtPack_'+itm_id,"../../",2);
					k++;
				}
				var data_mst="action=save_update_delete_confirm_style&operation="+operation+get_submitted_data_string('txt_costSheet_id*txtConfirm_id*txtItem_id*txt_confirm_style*txt_order_qty*txt_confirm_fob*txt_ship_date*txt_job_id',"../../",2);
				
				var data=data_mst+data_dtls;
				//alert(data); //return;
				freeze_window(operation);
				http.open("POST","quick_costing_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_stage_entry_reponse;
			//}
		}
		 
		function fnc_stage_entry_reponse()
		{
			if(http.readyState == 4) 
			{
				var reponse=http.responseText.split('**');
				
				if(reponse[0]==0 || reponse[0]==1)
				{
					$('#txtConfirm_id').val(reponse[1]);
					set_button_status(1, permission, 'fnc_confirm_entry',1);
					get_php_form_data($('#txt_costSheet_id').val()+'__'+$('#txtItem_id').val(),'populate_confirm_style_form_data','quick_costing_controller');
					//show_list_view('','stage_data_list','stage_data_div','quick_costing_controller','');
				}
			}
			release_freezing();
		}
		
		function js_set_value( )
		{
			parent.emailwindow.hide();
		}
		
		function fnc_openJobPopup()
		{
			var data=document.getElementById('cbo_buyer_id').value;
			page_link='quick_costing_controller.php?action=style_tag_popup&data='+data;
			emailwindow=dhtmlmodal.open('EmailBox','iframe',page_link,'Job and Style Popup', 'width=700px, height=350px, center=1, resize=0, scrolling=0','../../');
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("hidd_job_data");  
				//alert (theemail.value);return;
				var job_val=theemail.value.split("_");
				if (theemail.value!="")
				{
					$("#txt_job_id").val(job_val[0]);
					$("#txt_job_style").val(job_val[1]);
					$("#txt_style_job").val(job_val[2]);
				}
			}
		}
	</script>
	</head>
	<body>
    <div id="confirm_style_details" align="center">  
    <div style="display:none"><? echo load_freeze_divs ("../../../",$permission);  ?></div>       
        <form name="confirmStyle_1" id="confirmStyle_1" autocomplete="off">
        	<table width="850">
                <tr>
                    <td width="90"><strong>Buyer</strong><input style="width:40px;" type="hidden" class="text_boxes" name="txt_costSheet_id" id="txt_costSheet_id" value="<? echo $data; ?>" /><input style="width:40px;" type="hidden" class="text_boxes" name="txtConfirm_id" id="txtConfirm_id" value="" /><input style="width:40px;" type="hidden" class="text_boxes" name="txtItem_id" id="txtItem_id" value="<? echo $sql_data[0][csf('lib_item_id')]; ?>" /></td>
                    <td width="120"><? echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-Select Buyer-", $sql_data[0][csf('buyer_id')], "load_drop_down( 'quick_costing_controller', this.value, 'load_drop_down_season_conf', 'season_conf_td'); load_drop_down( 'quick_costing_controller',this.value, 'load_drop_down_sub_depConf', 'subConf_td' );",1 ); ?></td>
                    <td width="90">&nbsp;&nbsp;<strong>Season</strong></td>
                    <td width="100" id="season_conf_td"><? echo create_drop_down( "cbo_season_id", 100, "select id, season_name from lib_buyer_season where buyer_id='".$sql_data[0][csf('buyer_id')]."' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-Select Season-",$sql_data[0][csf('season_id')], "",1 ); ?></td>
                    <td width="90">&nbsp;&nbsp;<strong>Department</strong></td>
                    <td width="100" id="subConf_td"><? echo create_drop_down( "cbo_subDept_id", 100, "select id, sub_department_name from lib_pro_sub_deparatment where buyer_id='".$sql_data[0][csf('buyer_id')]."' and status_active=1 and is_deleted=0 order by sub_department_name","id,sub_department_name", 1, "-- Select Dept--",$sql_data[0][csf('department_id')], "",1 ); ?></td>
                    <td colspan="3" align="center">&nbsp;</td>
                </tr>
                <tr>
                    <td><strong>Style Type</strong></td>
                    <td><? echo create_drop_down( "cbo_style_type", 120, $template_name_arr,"", 1, "-Select-", $selected, "",1 ); ?> </td>
                    <td>&nbsp;&nbsp;<strong>Gmts Type</strong></td>
                    <td><? echo create_drop_down( "cbo_gmts_type", 100, $gmt_type_arr,'', 1, "-Gmts Type-",$selected_gmt_type, "" ,1); ?></td>
                    
                    <td>&nbsp;&nbsp;<strong>Revise No</strong></td>
                    <td><? echo create_drop_down( "cbo_revise", 100, "select revise_no from qc_mst where cost_sheet_no='".$sql_data[0][csf('cost_sheet_no')]."' and status_active=1 and is_deleted=0 order by cost_sheet_no Desc","revise_no,revise_no", 0, "-Select-", $sql_data[0][csf('revise_no')], "",1 ); ?> </td>
                    <td width="90">&nbsp;&nbsp;<strong>Option</strong></td>
                    <td width="100"><? echo create_drop_down( "cbo_option", 100, "select option_id from qc_mst where cost_sheet_no='".$sql_data[0][csf('cost_sheet_no')]."' and status_active=1 and is_deleted=0 order by cost_sheet_no Desc","option_id,option_id", 0, "-Select-",$sql_data[0][csf('option_id')], "" ,1); ?></td>
                </tr>
                <tr>
                    <td><strong>Estimate Style</strong></td>
                    <td><input style="width:110px;" type="text" class="text_boxes" name="txt_style_ref" id="txt_style_ref" value="<? echo $sql_data[0][csf('style_ref')]; ?>" disabled /></td>
                    <td>&nbsp;&nbsp;<strong>Cofirm Style</strong></td>
                    <td><input style="width:90px;" type="text" class="text_boxes" name="txt_confirm_style" id="txt_confirm_style" value="<? echo $sql_data[0][csf('style_ref')]; ?>" /></td>
                    <td>&nbsp;&nbsp;<strong>Order Qty.</strong></td>
                    <td><input style="width:90px;" type="text" class="text_boxes_numeric" name="txt_order_qty" id="txt_order_qty" value="<? echo $sql_data[0][csf('offer_qty')]; ?>" /></td>
                    <td>&nbsp;&nbsp;<strong>Cofirm FOB</strong></td>
                    <td><input style="width:90px;" type="text" class="text_boxes_numeric" name="txt_confirm_fob" id="txt_confirm_fob" value="<? echo $sql_summ[0][csf('tot_fob_cost')]; ?>" /></td>
                </tr>
                <tr>
                	<td><strong>Ship Date</strong></td>
                    <td><input style="width:110px;" type="text" class="datepicker" name="txt_ship_date" id="txt_ship_date" value="<? echo change_date_format($sql_data[0][csf('delivery_date')]); ?>" readonly /></td>
                    <td>&nbsp;&nbsp;<strong>Job No</strong></td>
                    <td><input style="width:90px;" type="text" class="text_boxes" name="txt_job_style" id="txt_job_style" placeholder="Browse Job" onDblClick="fnc_openJobPopup();" readonly /><input style="width:40px;" type="hidden" class="text_boxes" name="txt_job_id" id="txt_job_id" /></td>
                    <td>&nbsp;&nbsp;<strong>Style Ref.</strong></td>
                    <td><input style="width:90px;" type="text" class="text_boxes" name="txt_style_job" id="txt_style_job" disabled /></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
            <table width="400" cellspacing="0" class="" border="0">
                <tr>
                    <td align="center" width="100%" class="button_container">
						<? echo load_submit_buttons( $permission, "fnc_confirm_entry", 0,0 ,"reset_form('confirmStyle_1','','','','')",1); ?><input type="button" class="formbutton" value="Close" style="width:80px" onClick="js_set_value()"/>
                    </td> 
                </tr>
                <!--<tr>
                    <td align="center" height="15" width="100%"></td>
                </tr>-->
            </table>
            <div id="confirm_data_div">
            <table width="865" cellspacing="0" border="1" rules="all" class="rpt_table">
        	<thead>
            	<th width="70">Item</th>
                <th width="60">Fab. Cons. Kg</th>
                <th width="60">Fab. Cons. Yds</th>
                <th width="60">Fab. Amount</th>
                <th width="60">Special Opera.</th>
                <th width="60">Access.</th>
                <th width="60">Frieght Cost</th>
                <th width="60">Lab - Test</th>
                <th width="60">Misce.</th>
                <th width="60">Other Cost</th>
                <th width="60">Commis.</th>
                <th width="60">FOB ($/DZN)</th>
                <th width="60">CM</th>
                <th>RMG Qty(Pcs)</th>
            </thead>
			<?
            $sql_item_summ="select item_id, fabric_cost, sp_operation_cost, accessories_cost, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs from qc_item_cost_summary where mst_id='$data' and status_active=1 and is_deleted=0";
            $sql_result_item_summ=sql_select($sql_item_summ); $z=1;
            foreach($sql_result_item_summ as $rowItemSumm)
            {
				if ($z%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if ($z%2==0) $bgcolorN="#E9F50F"; else $bgcolorN="#D078F6";
                ?>
                <tr id="trVal_<? echo $z; ?>" bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $lib_temp_arr[$rowItemSumm[csf("item_id")]]; ?></td>
                    <td align="right"><? echo number_format($item_wise_cons_arr[$rowItemSumm[csf("item_id")]]['qty_kg'],4); ?></td>
                    <td align="right"><? echo number_format($item_wise_cons_arr[$rowItemSumm[csf("item_id")]]['qty_yds'],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("fabric_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("sp_operation_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("accessories_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("frieght_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("lab_test_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("miscellaneous_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("other_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("commission_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("fob_pcs")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("cm_cost")],4); ?></td>
                    <td align="right">&nbsp;<? //echo number_format($rowItemSumm[csf("sp_operation_cost")],4); ?></td>
                </tr>
                <tr id="tr_<? echo $z; ?>" bgcolor="<? echo $bgcolorN; ?>">
                    <td>BOM Limit<input style="width:40px;" type="hidden" name="txtitemid_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtitemid_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo $rowItemSumm[csf("item_id")]; ?>" /><input style="width:40px;" type="hidden" name="txtdtlsupid_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtdtlsupid_<? echo $rowItemSumm[csf("item_id")]; ?>" value="" /></td>
                    <td>
                    <input style="width:45px;" type="text" class="text_boxes_numeric" name="txtFabConkg_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtFabConkg_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($item_wise_cons_arr[$rowItemSumm[csf("item_id")]]['qty_kg'],4); ?>" /></td>
                    <td><input style="width:45px;" type="text" class="text_boxes_numeric" name="txtFabConyds_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtFabConyds_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($item_wise_cons_arr[$rowItemSumm[csf("item_id")]]['qty_yds'],4); ?>" /></td>
                    <td><input style="width:45px;" type="text" class="text_boxes_numeric" name="txtFabCst_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtFabCst_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("fabric_cost")],4); ?>" /></td>
                    <td><input style="width:45px;" type="text" class="text_boxes_numeric" name="txtSpOpa_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtSpOpa_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("sp_operation_cost")],4); ?>" /></td>
                    <td><input style="width:45px;" type="text" class="text_boxes_numeric" name="txtAcc_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtAcc_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("accessories_cost")],4); ?>" /></td>
                    <td><input style="width:45px;" type="text" class="text_boxes_numeric" name="txtFrightCst_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtFrightCst_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("frieght_cost")],4); ?>" /></td>
                    <td><input style="width:45px;" type="text" class="text_boxes_numeric" name="txtLabCst_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtLabCst_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("lab_test_cost")],4); ?>" /></td>
                    <td><input style="width:45px;" type="text" class="text_boxes_numeric" name="txtMiscCst_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtMiscCst_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("miscellaneous_cost")],4); ?>" /></td>
                    <td><input style="width:45px;" type="text" class="text_boxes_numeric" name="txtOtherCst_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtOtherCst_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("other_cost")],4); ?>" /></td>
                    <td><input style="width:45px;" type="text" class="text_boxes_numeric" name="txtCommCst_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtCommCst_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("commission_cost")],4); ?>" /></td>
                    <td><input style="width:45px;" type="text" class="text_boxes_numeric" name="txtFobDzn_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtFobDzn_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("fob_pcs")],4); ?>" /></td>
                    <td><input style="width:45px;" type="text" class="text_boxes_numeric" name="txtCmCst_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtCmCst_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("cm_cost")],4); ?>" /></td>
                    <td><input style="width:42px;" type="text" class="text_boxes_numeric" name="txtPack_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtPack_<? echo $rowItemSumm[csf("item_id")]; ?>" value="" />&nbsp;</td>
                </tr>
                <?
				$z++;
            }
            ?>
        </table>
            </div>
        </form>
	</div>
    </body> 
    <script>get_php_form_data($('#txt_costSheet_id').val()+'__'+$('#txtItem_id').val(),'populate_confirm_style_form_data','quick_costing_controller');</script>          
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();	
}

if($action=="save_update_delete_confirm_style")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$confirm_mst_id=return_next_id("id", "qc_confirm_mst", 1);
		
		$mst_field_arr="id, cost_sheet_id, lib_item_id, confirm_style, confirm_order_qty, confirm_fob, ship_date, job_id, inserted_by, insert_date";
		$mst_data_arr="(".$confirm_mst_id.",".$txt_costSheet_id.",".$txtItem_id.",".$txt_confirm_style.",".$txt_order_qty.",".$txt_confirm_fob.",".$txt_ship_date.",".$txt_job_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$confirm_dtls_id=return_next_id("id", "qc_confirm_dtls", 1);
		
		$dtls_field_arr="id, mst_id, cost_sheet_id, item_id, fab_cons_kg, fab_cons_yds, fab_amount, sp_oparation_amount, acc_amount, fright_amount, lab_amount, misce_amount, other_amount, comm_amount, fob_amount, cm_amount, rmg_ratio, inserted_by, insert_date";
		//echo "10**";
		$itm_id=explode(",",str_replace("'","",$txtItem_id));
		
		$up_job_field="quotation_id*style_ref_no";
		$up_job_data="".$txt_costSheet_id."*".$confirm_style."";
		
		$k=0; $add_comma=0; $dtls_data_arr=""; 
		for($i=1; $i<=count($itm_id); $i++)
		{
			$item_id=0;
			$item_id=trim($itm_id[$k]);
			//'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id,
			$txtitemid='txtitemid_'.$item_id;
			$txtdtlsupid='txtdtlsupid_'.$item_id;
			$txtFabConkg='txtFabConkg_'.$item_id;
			$txtFabConyds='txtFabConyds_'.$item_id;
			$txtFabCst='txtFabCst_'.$item_id;
			$txtSpOpa='txtSpOpa_'.$item_id;
			$txtAcc='txtAcc_'.$item_id;
			$txtFrightCst='txtFrightCst_'.$item_id;
			$txtLabCst='txtLabCst_'.$item_id;
			$txtMiscCst='txtMiscCst_'.$item_id;
			$txtOtherCst='txtOtherCst_'.$item_id;
			$txtCommCst='txtCommCst_'.$item_id;
			$txtFobDzn='txtFobDzn_'.$item_id;
			$txtCmCst='txtCmCst_'.$item_id;
			$txtPack='txtPack_'.$item_id;
			
			if ($add_comma!=0) $dtls_data_arr .=",";
			$dtls_data_arr .="(".$confirm_dtls_id.",".$confirm_mst_id.",".$txt_costSheet_id.",".$$txtitemid.",".$$txtFabConkg.",".$$txtFabConyds.",".$$txtFabCst.",".$$txtSpOpa.",".$$txtAcc.",".$$txtFrightCst.",".$$txtLabCst.", ".$$txtMiscCst.", ".$$txtOtherCst.", ".$$txtCommCst.", ".trim($$txtFobDzn).",".$$txtCmCst.",".$$txtPack.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$confirm_dtls_id=$confirm_dtls_id+1;
			$add_comma++;
			$k++;
		}
		/*echo "10**INSERT INTO qc_confirm_mst (".$mst_field_arr.") VALUES ".$mst_data_arr; 
		
		echo "10**INSERT INTO qc_confirm_dtls (".$dtls_field_arr.") VALUES ".$dtls_data_arr; 
		die;*/
		$flag=1;
		
		$rID=sql_insert("qc_confirm_mst",$mst_field_arr,$mst_data_arr,1);
		if($rID) $flag=1; else $flag=0;
		
		$rID1=sql_insert("qc_confirm_dtls",$dtls_field_arr,$dtls_data_arr,1);
		if($rID1) $flag=1; else $flag=0;
		
		if(str_replace("'",'',$txt_job_id)!='')
		{
			$rIDJob=sql_update("wo_po_details_master",$up_job_field,$up_job_data,"id","".$txt_job_id."",1);
			if($rIDJob) $flag=1; else $flag=0;
		}
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$confirm_mst_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "0**".str_replace("'",'',$confirm_mst_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$mst_field_arr="confirm_style*confirm_order_qty*confirm_fob*ship_date*job_id*updated_by*update_date";
		$mst_data_arr="".$txt_confirm_style."*".$txt_order_qty."*".$txt_confirm_fob."*".$txt_ship_date."*".$txt_job_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$up_job_field="quotation_id*style_ref_no";
		$up_job_data="".$txt_costSheet_id."*".$confirm_style."";
		
		$dtls_field_arr="fab_cons_kg*fab_cons_yds*fab_amount*sp_oparation_amount*acc_amount*fright_amount*lab_amount*misce_amount*other_amount*comm_amount*fob_amount*cm_amount*rmg_ratio*updated_by*update_date";
		//echo "10**";
		$itm_id=explode(",",str_replace("'","",$txtItem_id));
		//print_r($itm_id);
		//echo count($itm_id);
		$k=0; $add_comma=0; $dtls_data_arr=""; 
		for($i=1; $i<=count($itm_id); $i++)
		{
			$item_id=0;
			$item_id=trim($itm_id[$k]);
			//'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id, 
			$txtitemid='txtitemid_'.$item_id;
			$txtdtlsupid='txtdtlsupid_'.$item_id;
			$txtFabConkg='txtFabConkg_'.$item_id;
			$txtFabConyds='txtFabConyds_'.$item_id;
			$txtFabCst='txtFabCst_'.$item_id;
			$txtSpOpa='txtSpOpa_'.$item_id;
			$txtAcc='txtAcc_'.$item_id;
			$txtFrightCst='txtFrightCst_'.$item_id;
			$txtLabCst='txtLabCst_'.$item_id;
			$txtMiscCst='txtMiscCst_'.$item_id;
			$txtOtherCst='txtOtherCst_'.$item_id;
			$txtCommCst='txtCommCst_'.$item_id;
			$txtFobDzn='txtFobDzn_'.$item_id;
			$txtCmCst='txtCmCst_'.$item_id;
			$txtPack='txtPack_'.$item_id;
			
			$id_arr[]=str_replace("'",'',$$txtdtlsupid);
							
			$dtls_data_arr[str_replace("'",'',$$txtdtlsupid)] =explode("*",("".$$txtFabConkg."*".$$txtFabConyds."*".$$txtFabCst."*".$$txtSpOpa."*".$$txtAcc."*".$$txtFrightCst."*".$$txtLabCst."*".$$txtMiscCst."*".$$txtOtherCst."*".$$txtCommCst."*".$$txtFobDzn."*".$$txtCmCst."*".$$txt_colorSizeExCut."*".$$txt_colorSizeArticleNo."*".$order_total_amt."*'".$color_size_planCutQty."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
			$k++;
		}
		$flag=1;
		
		$rID=sql_update("qc_confirm_mst",$mst_field_arr,$mst_data_arr,"id","".$txtConfirm_id."",1);
		if($rID) $flag=1; else $flag=0;
		
		$rID1=execute_query(bulk_update_sql_statement("qc_confirm_dtls", "id",$dtls_field_arr,$dtls_data_arr,$id_arr ));
		if($rID1) $flag=1; else $flag=0;
		
		if(str_replace("'",'',$txt_job_id)!='')
		{
			$rIDJob=sql_update("wo_po_details_master",$up_job_field,$up_job_data,"id","".$txt_job_id."",1);
			if($rIDJob) $flag=1; else $flag=0;
		}
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txtConfirm_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'",'',$txtConfirm_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="populate_confirm_style_form_data")
{
	//echo $data;
	$ex_data=explode('__',$data);
	$job_arr=array();
	$job_sql=sql_select("Select id, job_no, style_ref_no from wo_po_details_master where status_active=1 and is_deleted=0");
	foreach($job_sql as $jrow)
	{
		$job_arr[$jrow[csf("id")]]['id']=$jrow[csf("id")];
		$job_arr[$jrow[csf("id")]]['job']=$jrow[csf("job_no")];
		$job_arr[$jrow[csf("id")]]['style']=$jrow[csf("style_ref_no")];
	}
	//echo "id, cost_sheet_id, lib_item_id, confirm_style, confirm_order_qty, confirm_fob, deal_merchant, ship_date from qc_confirm_mst where cost_sheet_id ='$ex_data[0]'";
	$sql_confirm_mst=sql_select("select id, cost_sheet_id, lib_item_id, confirm_style, confirm_order_qty, confirm_fob, deal_merchant, ship_date, job_id from qc_confirm_mst where cost_sheet_id ='$ex_data[0]'") ;
	foreach($sql_confirm_mst as $row_mst)
	{
		echo "$('#txt_confirm_style').val('".$row_mst[csf("confirm_style")]."');\n";
		echo "$('#txt_order_qty').val('".$row_mst[csf("confirm_order_qty")]."');\n";
		echo "$('#txt_confirm_fob').val('".$row_mst[csf("confirm_fob")]."');\n";
		echo "$('#cbo_dealing_merchant').val('".$row_mst[csf("deal_merchant")]."');\n";
		echo "$('#txt_ship_date').val('".change_date_format($row_mst[csf("ship_date")])."');\n";
		echo "$('#txtConfirm_id').val('".$row_mst[csf("id")]."');\n";    
		
		echo "$('#txt_job_id').val('".$job_arr[$row_mst[csf("job_id")]]['id']."');\n";
		echo "$('#txt_job_style').val('".$job_arr[$row_mst[csf("job_id")]]['job']."');\n";
		echo "$('#txt_style_job').val('".$job_arr[$row_mst[csf("job_id")]]['style']."');\n";
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_confirm_entry',1);\n"; 
	}
	
	$sql_confirm_dtls=sql_select("select id, mst_id, cost_sheet_id, item_id, fab_cons_kg, fab_cons_yds, fab_amount, sp_oparation_amount, acc_amount, fright_amount, lab_amount, misce_amount, other_amount, comm_amount, fob_amount, cm_amount, rmg_ratio from qc_confirm_dtls where cost_sheet_id='$ex_data[0]'");
	foreach($sql_confirm_dtls as $row_dtls)
	{
		echo "$('#txtitemid_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("item_id")]."');\n";
		echo "$('#txtFabConkg_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("fab_cons_kg")]."');\n";
		echo "$('#txtFabConyds_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("fab_cons_yds")]."');\n";
		echo "$('#txtFabCst_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("fab_amount")]."');\n";
		echo "$('#txtSpOpa_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("sp_oparation_amount")]."');\n";
		echo "$('#txtAcc_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("acc_amount")]."');\n";
		echo "$('#txtFrightCst_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("fright_amount")]."');\n";
		echo "$('#txtLabCst_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("lab_amount")]."');\n";
		echo "$('#txtMiscCst_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("misce_amount")]."');\n";
		echo "$('#txtOtherCst_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("other_amount")]."');\n";
		echo "$('#txtCommCst_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("comm_amount")]."');\n";
		echo "$('#txtFobDzn_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("fob_amount")]."');\n";
		echo "$('#txtCmCst_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("cm_amount")]."');\n";
		echo "$('#txtPack_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("rmg_ratio")]."');\n";
		
		echo "$('#txtdtlsupid_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("id")]."');\n";
	}
	exit();
}

if($action=="style_tag_popup")
{
	echo load_html_head_contents("Style Browse Pop-Up", "../../../", 1, 1,'',1,'');
	extract($_REQUEST);
	//$ex_data=explode('_',$data);
		?>
		<script>
			function js_set_value(all_data)
			{ 
				document.getElementById('hidd_job_data').value=all_data;
				parent.emailwindow.hide();
			}
			
			$(document).ready(function(e) {
            setFilterGrid('list_view',-1);
        }); 
		
		</script>
		</head>
		<body>
            <div align="center" style="width:100%;" >
                <form name="searchpofrm"  id="searchpofrm">
                    <fieldset style="width:500px">
                    <input type="hidden" id="hidd_job_data" />
						<?
							$buyer_arr=return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name"); 
							$season_arr=return_library_array("select id, season_name from lib_buyer_season", "id", "season_name"); 
							
							if($db_type==0) $year_cond="YEAR(insert_date)";
							else if ($db_type==2) $year_cond="TO_CHAR(insert_date,'YYYY')";
							
							$sql ="select id, job_no_prefix_num, $year_cond as year, job_no, buyer_name, style_ref_no, season_matrix, job_quantity from wo_po_details_master where buyer_name='$data' and status_active=1 and is_deleted=0 order by id DESC";
							//echo $sql;die;
							$arr=array(3=>$buyer_arr,5=>$season_arr);
							echo  create_list_view("list_view", "Job No, Job Pre., Year, Buyer, Style, Season, Job Qty.", "100,60,60,110,100,80,90","650","250",0, $sql, "js_set_value", "id,job_no,style_ref_no", "", 1, "0,0,0,buyer_name,season_matrix,0", $arr ,"job_no,job_no_prefix_num,year,buyer_name,style_ref_no,season_matrix,job_quantity", "","","0,0,0,0,0,0,2") ;			
                        ?>
                    </fieldset>
                </form>
            </div>
		</body>           
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
		</html>
	<?	
	exit();
}

if($action=='meeting_remarks_popup')
{
	echo load_html_head_contents("Meeting Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//$costSheetId; $styleRef
	$permission=$_SESSION['page_permission'];
	
	?>
    <script>
	var permission = '<? echo $permission; ?>';
	//if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	function jsset_value(val_re)
	{
		//alert(val_re);
		document.getElementById('hide_meeting_data').value=val_re;
		parent.emailwindow.hide();
	} 
	
	function fn_report_generated(action)
	{
		//var report_title=$( "div.form_caption" ).html();
		var data="action="+action+get_submitted_data_string('cbo_buyer_id*cbo_season_id*cbo_subDept_id*txt_search_common*hide_curr_cost_id' ,"../../",4);
		//alert(data);//+"&report_title="+report_title
		//freeze_window(3);
		//alert(data);
		http.open("POST","quick_costing_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**");
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
			setFilterGrid("table_body",-1);
			
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="270px";
		$("#table_body tr:first").show();
	}	
		  
	</script>
     <input type="hidden" name="hide_meeting_data" id="hide_meeting_data" value="" />
 
    </head>
    <body>
    <div align="center">
        <form name="remarkform_1" id="remarkform_1">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Season</th>
                    <th>Department</th>
                    <th>Style</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:70px;" onClick="reset_form('remarkform_1','','','','','');"></th> 					
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name",1, "-- All Buyer--",$selected,"load_drop_down( 'quick_costing_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'quick_costing_controller',this.value, 'load_drop_down_sub_dep', 'sub_td' );",0 ); ?>
                        </td>                 
                        <td id="season_td"><? echo create_drop_down( "cbo_season_id", 130, $blank_array,'', 1, "-- Select Season--",$selected, "" ); ?></td>
                        <td id="sub_td"><? echo create_drop_down( "cbo_subDept_id", 130, $blank_array,'', 1, "-- Select Dept--",$selected, "" ); ?></td>     
                        <td align="center" id="search_by_td"><input type="hidden" name="hide_curr_cost_id" id="hide_curr_cost_id" value="<? echo $costSheetId; ?>" />				
                            <input type="text" style="width:70px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center"><input type="button" name="button" class="formbutton" value="Show" onClick="fn_report_generated('report_generate');" style="width:70px;" /></td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body> 
    <div id="report_container" align="center"></div>
    <div id="report_container2" ></div>          
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	//echo $cbo_buyer_id;//*cbo_season_id*cbo_subDept_id*txt_search_common
	$lib_temp_arr=return_library_array("select id, item_name from lib_qc_template","id","item_name");
	if($db_type==0) $concat_cond="group_concat(lib_item_id)";
	else if($db_type==2) $concat_cond="listagg(cast(lib_item_id as varchar2(4000)),',') within group (order by lib_item_id)";
	else $concat_cond="";
	$sql_tmp="select temp_id, $concat_cond as lib_item_id from qc_template where status_active=1 and is_deleted=0 group by temp_id order by temp_id ASC";
	$sql_tmp_res=sql_select($sql_tmp);
	//print_r($sql_tmp_res);die;
	$template_name_arr=array();
	foreach($sql_tmp_res as $row)
	{
		$lib_temp_id='';
		$ex_temp_id=explode(',',$row[csf('lib_item_id')]);
		foreach($ex_temp_id as $lib_id)
		{
			if($lib_temp_id=="") $lib_temp_id=$lib_temp_arr[$lib_id]; else $lib_temp_id.=','.$lib_temp_arr[$lib_id];
		}
		$template_name_arr[$row[csf('temp_id')]]=$lib_temp_id;
	}
	
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");
	$department_arr=return_library_array( "select id, sub_department_name from lib_pro_sub_deparatment", "id", "sub_department_name");
	$stage_arr=return_library_array( "select id, stage_name from lib_stage_name", "id", "stage_name");
	$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name");
	$fob_arr=return_library_array( "select mst_id, tot_fob_cost from qc_tot_cost_summary", "mst_id", "tot_fob_cost");
	$agent_location_arr=return_library_array( "select id, agent_location from lib_agent_location", "id", "agent_location");
	
	$buyer_id=str_replace("'","",$cbo_buyer_id);
	$season_id=str_replace("'","",$cbo_season_id);
	$subDept_id=str_replace("'","",$cbo_subDept_id);
	$search_common=str_replace("'","",$txt_search_common);
	$curr_cost_id=str_replace("'","",$hide_curr_cost_id);
	
	if($buyer_id!=0) $buyer_id_cond="and a.buyer_id='$buyer_id'"; else $buyer_id_cond="";
	if($season_id!=0) $season_id_cond="and a.season_id='$season_id'"; else $season_id_cond="";
	if($subDept_id!=0) $subDept_id_cond="and a.department_id='$subDept_id'"; else $subDept_id_cond="";
	if($search_common!="") $style_ref_cond="and a.style_ref='$search_common'"; else $style_ref_cond="";
	
	$confirm_arr=array();
	$sql_con=sql_select("select id, cost_sheet_id from qc_confirm_mst where status_active=1 and is_deleted=0");
	
	foreach($sql_con as $crow)
	{
		$confirm_arr[$crow[csf("cost_sheet_id")]]=$crow[csf("id")];
	}
	unset($sql_con);
	if($db_type==0) $meeting_time="TIME_FORMAT( b.meeting_time, '%H:%i')";
	else if ($db_type==2) $meeting_time="TO_CHAR(b.meeting_time,'HH24:MI')";
	
	$sql_mst="select a.id as mst_id, a.buyer_id, a.season_id, a.department_id, a.style_ref, a.temp_id, a.lib_item_id, a.offer_qty, a.tgt_price, a.quoted_price, a.stage_id, a.option_id, a.revise_no, a.inserted_by, a.updated_by, b.id, b.meeting_no, b.buyer_agent_id, b.location_id, b.meeting_date, $meeting_time as meeting_time, b.remarks from qc_mst a, qc_meeting_mst b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.remarks<>' ' $buyer_id_cond $season_id_cond $subDept_id_cond $style_ref_cond order by a.id DESC";
	//echo $sql_mst;
	$sql_mst_arr=sql_select($sql_mst);
	
	ob_start();
	?>
    <div style="width:1830px">
    <table class="rpt_table" width="1830" cellpadding="0" cellspacing="0" border="1" rules="all">
        <thead>
            <th width="30">SL</th>
            <th width="100">Buyer</th>
            <th width="80">Season</th>
            <th width="80">Department</th>
            <th width="100">Style Ref.</th>
            <th width="130">Template</th>
            <th width="80">Order Qty</th>
            <th width="70">FOB ($)</th>
            <th width="60">TGT Price</th>
            <th width="70">Quoted Price ($)</th>
            <th width="80">Stage</th>
            <th width="100">Remarks</th>
            <th width="60">Is Conf.</th>
            <th width="120">Meeting Comments</th>
            <th width="70">Meeting Date</th>
            <th width="60">Meeting No</th>
            <th width="60">Option No</th>
            <th width="60">Revise No</th>
            <th width="90">Buyer Agent</th>
            <th width="90">Location</th>
            <th width="100">Cr User</th>
            <th>Up User</th>
        </thead>
    </table>
    <div style="width:1830px; max-height:270px; overflow-y:scroll" id="scroll_body">
    <table class="rpt_table" width="1810" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
    <?
	$i=1;
	foreach($sql_mst_arr as $row)
	{
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";  
		
		$ex_tmpelate_id=explode(',',$row[csf("temp_id")]);
		$count_temp=count(explode(',',$row[csf("lib_item_id")]));
		$template_name='';
		foreach($ex_tmpelate_id as $tmpelate_id)
		{
			if($template_name=="") $template_name=$template_name_arr[$tmpelate_id]; else $template_name.=','.$template_name_arr[$tmpelate_id];
		}
		$meeting_data="";
		//if($row[csf('mst_id')]==$curr_cost_id) 
		$meeting_data=$row[csf('meeting_no')].'__'.$row[csf('buyer_agent_id')].'__'.$row[csf('location_id')].'__'.change_date_format($row[csf('meeting_date')]).'__'.$row[csf('meeting_time')].'__'.$row[csf('remarks')];
		//else $meeting_data=$row[csf('meeting_no')].'__'.$row[csf('buyer_agent_id')].'__'.$row[csf('location_id')].'__'.change_date_format($row[csf('meeting_date')]).'__'.$row[csf('meeting_time')];
		
		$confirm='';
		if($confirm_arr[$row[csf('id')]]!="") $confirm='Yes';
		else $confirm='No';   
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" onClick="jsset_value('<? echo $meeting_data; ?>'); change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>');" id="tr_<? echo $i; ?>" > 
			<td width="30"><? echo $i; ?></td>
			<td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
			<td width="80"><? echo $season_arr[$row[csf('season_id')]]; ?></td>
			<td width="80"><? echo $department_arr[$row[csf('department_id')]]; ?>&nbsp;</td>
			<td width="100" style="word-break: break-all;"><? echo $row[csf('style_ref')]; ?></td>
			<td width="130" style="word-break: break-all;"><? echo $template_name; ?></td>
			<td width="80" align="right"><? echo number_format($row[csf('offer_qty')],0,'.',''); ?></td>
			<td width="70" align="right"><? echo number_format($fob_arr[$row[csf('id')]],2,'.',''); ?>&nbsp;</td>
			<td width="60" align="right"><? echo number_format($row[csf('tgt_price')],2,'.',''); ?>&nbsp;</td>
			<td width="70" align="right"><? echo number_format($row[csf('quoted_price')],2,'.',''); ?>&nbsp;</td>
			<td width="80"><? echo $stage_arr[$row[csf('stage_id')]]; ?>&nbsp;</td>
			<td width="100"style="word-break: break-all;">&nbsp;</td>
			<td width="60"><? echo $confirm; ?></td>
			<td width="120" style="word-break: break-all;"><? echo $row[csf('remarks')]; ?>&nbsp;</td>
			<td width="70"><? echo change_date_format($row[csf('meeting_date')]); ?>&nbsp;</td>
			<td width="60" align="center"><? echo $row[csf('meeting_no')]; ?>&nbsp;</td>
			<td width="60" align="center"><? echo $row[csf('option_id')]; ?></td>
			<td width="60" align="center"><? echo $row[csf('revise_no')]; ?></td>
			<td width="90" style="word-break: break-all;"><? echo $agent_location_arr[$row[csf('buyer_agent_id')]]; ?>&nbsp;</td>
			<td width="90" style="word-break: break-all;"><? echo $agent_location_arr[$row[csf('location_id')]]; ?>&nbsp;</td>
			<td width="100" style="word-break: break-all;"><? echo $user_arr[$row[csf('inserted_by')]]; ?></td>
			<td style="word-break: break-all;"><? echo $user_arr[$row[csf('updated_by')]]; ?></td>
		</tr>
		<?
		$i++;
	}
	?>
    </table>
    </div>
    </div>
    <?
	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename"; 
    exit();
}

if($action=="agent_location_popup")
{
	echo load_html_head_contents("Agent Location Entry/Update PopUp","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	//echo $type;
	?>
    <script>
		var permission='<? echo $permission; ?>';
		var type='<? echo $type; ?>'; 
		
		function fnc_agent_location_entry( operation )
		{
			if(form_validation('txt_agent_location','Agent Location Name')==false)
			{
				return;   
			}
			else
			{
				var data="action=save_update_delete_agent_location&operation="+operation+get_submitted_data_string('update_id*txt_agent_location',"../../",5)+"&type="+type;
				//freeze_window(operation);
				http.open("POST","quick_costing_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_agent_location_reponse;
			}
		}
		 
		function fnc_agent_location_reponse()
		{
			if(http.readyState == 4) 
			{
				var reponse=http.responseText.split('**');
				
				if(reponse[0]==0 || reponse[0]==1)
				{
					$('#update_id').val(reponse[0]);
					set_button_status(1, permission, 'fnc_agent_location_entry',1);
					show_list_view(type,'agent_location_data_list','agent_location_data_div','quick_costing_controller','');
				}
			}
			release_freezing();
		}
		
		function js_set_value( )
		{
			parent.emailwindow.hide();
		}
		
	</script>
	</head>
	<body>
    <div id="agent_location_details" align="center">  
    <div style="display:none"><? echo load_freeze_divs ("../../../",$permission); ?>  </div>        
        <form name="agentLocationDetails_1" id="agentLocationDetails_1" autocomplete="off">
            <table width="260" cellspacing="0" border="1" class="rpt_table" id="tbl_agentLocationDetails" rules="all">
            	<thead>
                	<th class="must_entry_caption">Agent/ Location Name</th>
                </thead>
                <tbody>
                	<tr>
                    	<td><input style="width:240px;" type="text" class="text_boxes" name="txt_agent_location" id="txt_agent_location" />
                        	<input style="width:40px;" type="hidden" class="text_boxes" name="update_id" id="update_id" />
                        </td>
                    </tr>
                </tbody>
			</table>
            <table width="260" cellspacing="0" class="" border="0">
                <tr>
                    <td align="center" height="15" width="100%"></td>
                </tr>
                <tr>
                    <td align="center" width="100%" class="button_container">
						<?
                           echo load_submit_buttons( $permission, "fnc_agent_location_entry", 0,0 ,"reset_form('agentLocationDetails_1','','','','')",1); 
                        ?><input type="button" class="formbutton" value="Close" onClick="js_set_value()"/>
                    </td> 
                </tr>
            </table>
            <div id="agent_location_data_div"></div>
        </form>
	</div>
    </body> 
    <script>show_list_view(type,'agent_location_data_list','agent_location_data_div','quick_costing_controller','');</script>          
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();	
}

if($action=="save_update_delete_agent_location")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=return_next_id("id", "lib_agent_location", 1);
		//echo "10**".$type; //die;
		$field_array="id, type, agent_location, inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$id.",'".$type."','".strtoupper(str_replace("'","",$txt_agent_location))."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		//echo $data_array; die;
		$rID=sql_insert("lib_agent_location",$field_array,$data_array,1);
		
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
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);  
				echo "0**".$id;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="agent_location*updated_by*update_date";
		$data_array="'".strtoupper(str_replace("'","",$txt_agent_location))."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_update("lib_agent_location",$field_array,$data_array,"id","".$update_id."",1);
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "1**".$update_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);  
				echo "1**".$update_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="agent_location_data_list")
{
	//echo $data;
	echo  create_list_view ( "list_view", "Agent/Location Name", "200","200","150",1, "select id, agent_location from lib_agent_location where type='$data' and status_active=1 and is_deleted=0 order by id desc", "get_php_form_data", "id","'load_php_data_to_form_agent_location'", 1, "0", $arr, "agent_location","quick_costing_controller", 'setFilterGrid("list_view",-1);','0' );
	exit();
}

if($action=="load_php_data_to_form_agent_location")
{
	$sql_arr=sql_select( "select id, agent_location from lib_agent_location where status_active=1 and is_deleted=0 and id='$data'" );
	foreach ($sql_arr as $inf)
	{
		echo "document.getElementById('txt_agent_location').value  			= '".$inf[csf("agent_location")]."';\n"; 
		echo "document.getElementById('update_id').value  				= '".$inf[csf("id")]."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_agent_location_entry',1);\n";  
	}
	exit();
}

if($action=="load_drop_agent_location_name")
{
	if($data==1) echo create_drop_down( "cbo_buyer_agent", 80,"select id, agent_location from lib_agent_location where type='$data' and status_active=1 and is_deleted=0","id,agent_location", 1, "-Agent-", $selected, "" );
	else if ($data==2)  echo create_drop_down( "cbo_agent_location", 80,"select id, agent_location from lib_agent_location where type='$data' and status_active=1 and is_deleted=0","id,agent_location", 1, "-Location-", $selected, "" );
	
	exit();
}

if($action=="max_meeting_no")
{
	$max_meeting=return_field_value("max(meeting_no) as max_meeting_no","qc_meeting_mst","is_deleted=0 and status_active=1","max_meeting_no");
	if($max_meeting=="" || $max_meeting==0) $max_meeting=0; else $max_meeting=$max_meeting;
	echo $max_meeting;
	exit();	
}