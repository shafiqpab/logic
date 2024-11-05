<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];

$userCredential = sql_select("SELECT brand_id, single_user_id FROM user_passwd where id=$user_id");
$userbrand_id = $userCredential[0][csf('brand_id')];
$single_user_id = $userCredential[0][csf('single_user_id')];
if($userbrand_id==0) $userbrand_id="";
$userbrand_idCond = ""; $filterBrandId = "";
if ($userbrand_id !='') { //&& $single_user_id==1
    $userbrand_idCond = "and id in ($userbrand_id)";
	$filterBrandId=$userbrand_id;
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 130, "select id, location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 $location_credential_cond order by location_name","id,location_name", 1, "--Location--", $selected, "" );
	exit();
}

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

if ($action=="load_drop_down_buyer_pop")
{
	echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   
	exit();	 
}

if ($action=="load_drop_down_brand")
{
	echo create_drop_down( "cbo_brand", 130, "select id, brand_name from lib_buyer_brand where buyer_id='$data' and status_active =1 and is_deleted=0 $userbrand_idCond order by brand_name ASC","id,brand_name", 1, "-Brand-", $selected, "" );
	exit();
}

if ($action=="load_drop_down_revise_no")
{
	$ex_data=explode('__',$data);
	$user_arr=return_library_array("select id, user_name from user_passwd","id","user_name");
	if($ex_data[1]=="") $option_cond=""; else $option_cond="and option_id='$ex_data[1]'";
	//echo "select revise_no from qc_mst where cost_sheet_no='$ex_data[0]' $option_cond order by revise_no Desc";
	$sql=sql_select("select inserted_by, revise_no from qc_mst where cost_sheet_no='$ex_data[0]' and status_active=1 and is_deleted=0 $option_cond order by revise_no Desc");
	$rvs=array();
	foreach($sql as $rows)
	{
		if($max=="") $max=$rows[csf("revise_no")];
		$rvs[$rows[csf("revise_no")]]=$rows[csf("revise_no")].'-'.$user_arr[$rows[csf("inserted_by")]];
	}
	
	if($ex_data[2]!=0)
		$select=$ex_data[2];
	else
		$select=$max;
	//echo "select revise_no from qc_mst where cost_sheet_no='$ex_data[0]' and option_id='$ex_data[1]' order by revise_no Desc";	
	echo create_drop_down( "cbo_revise_no", 45, $rvs ,"", 0, "-0-", $select, "fnc_option_rev( this.value+'***'+document.getElementById('cbo_option_id').value+'***2');" );
	exit();
}

if($action=="check_conversion_rate_variable")
{
	$data=explode("**",$data);
	$fabprocess_loss_method=0; $accprocess_loss_method=0;
	/*$process_loss_sql=sql_select("SELECT item_category_id, process_loss_method from variable_order_tracking where company_name='$data[2]' and variable_list=18 and item_category_id in (2,4) and status_active=1 and is_deleted=0");
	
	foreach($process_loss_sql as $row)
	{
		if($row[csf("item_category_id")]==2)//Knit finish Fabric
		{
			$fabprocess_loss_method=$row[csf("process_loss_method")];
		}
		if($row[csf("item_category_id")]==4)//Accessories
		{
			$accprocess_loss_method=$row[csf("process_loss_method")];
		}
	}*/
	$commSettings=sql_select("SELECT id, commercial_cost_method, commercial_cost_percent, editable from variable_order_tracking where company_name='$data[2]' and variable_list=84 and status_active=1 and is_deleted=0");
	$commercial_cost_method=$commercial_cost_percent=$editable=0;
	foreach($commSettings as $row)
	{
		$commercial_cost_method=$row[csf("commercial_cost_method")];
		$commercial_cost_percent=$row[csf("commercial_cost_percent")];
		$editable=$row[csf("editable")];
	}
	if($db_type==0) $conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	else $conversion_date=change_date_format($data[1], "d-M-y", "-",1);

	$currency_rate=set_conversion_rate( $data[0], $conversion_date,$data[2]);
	echo "1_".$currency_rate.'_'.$fabprocess_loss_method.'_'.$accprocess_loss_method.'_'.$commercial_cost_method.'_'.$commercial_cost_percent.'_'.$editable;
	exit();
}

if ($action=="load_drop_down_embtype")
{
	$data=explode('_',$data);

	$emb_typearr="";
	if($data[0]==1) $emb_typearr=$emblishment_print_type;
	else if($data[0]==2) $emb_typearr=$emblishment_embroy_type;
	else if($data[0]==3) $emb_typearr=$emblishment_wash_type;
	else if($data[0]==4) $emb_typearr=$emblishment_spwork_type;
	else if($data[0]==5) $emb_typearr=$emblishment_gmts_type;
	else if($data[0]==99) $emb_typearr=$blank_array;
	else $emb_typearr=$blank_array;

	echo create_drop_down( "cboSpeciaTypeId_".$data[1], 70,$emb_typearr,"", 1, "--Select--", "", "","","" );
	exit();
}

if ($action=="load_drop_down_option_id")
{
	$ex_data=explode('__',$data);
	/*if($ex_data[1]!=0)
		$selectopt=$ex_data[1];
	else
		$selectopt=$selected;*/
		//load_drop_down('requires/quick_costing_woven_controller', '".$row[csf("cost_sheet_no")].'__'.$row[csf("option_id")].'__'.$row[csf("revise_no")]."', 'load_drop_down_revise_no', 'revise_td');
	$max_option=return_field_value("max(option_id) as option_id","qc_mst","cost_sheet_no='$ex_data[0]' and is_deleted=0 and status_active=1","option_id");
	$user_arr=return_library_array("select id, user_name from user_passwd","id","user_name");
	if ($db_type==0) 
		$sql_op=sql_select("select option_id, inserted_by, concat(option_id,'-',option_remarks) as option_name from qc_mst where cost_sheet_no='$ex_data[0]' and status_active=1 and is_deleted=0 order by option_id Desc");
	else if ($db_type==2)
		$sql_op=sql_select("select option_id, inserted_by, option_id || '-' || option_remarks as option_name from qc_mst where cost_sheet_no='$ex_data[0]' and status_active=1 and is_deleted=0 order by option_id Desc");
	
	$rvs=array();
	foreach($sql_op as $rows)
	{
		if($max=="") $max=$rows[csf("option_id")];
		$rvs[$rows[csf("option_id")]]=$rows[csf("option_name")].'='.$user_arr[$rows[csf("inserted_by")]];
	}
	
	if($ex_data[1]!=0)
		$selectopt=$ex_data[1];
	else
		$selectopt=$max;
	//echo $sql;
	echo create_drop_down( "cbo_option_id", 45, $rvs,"", 0, "-0-", $selectopt, "load_drop_down( 'requires/quick_costing_woven_controller', document.getElementById('txt_costSheetNo').value+'__'+this.value+'__'+0, 'load_drop_down_revise_no', 'revise_td'); fnc_option_rev( document.getElementById('cbo_revise_no').value+'***'+this.value+'***2' );" );//
	exit();
}

if($action=="template_popup")
{
	echo load_html_head_contents("Template Info","../../../", 1, 1, '','1','');
	extract($_REQUEST); 
	$permission=$_SESSION['page_permission'];
	$user_level=$_SESSION['logic_erp']["user_level"];
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
		freeze_window(operation);
		
		var tot_row=$('#tbl_tempCreat tr').length;
		var all_data='';
		for(var i=1; i<=tot_row; i++)
		{
			all_data+=get_submitted_data_string('cboItemBts_'+i+'*txtBtsRatio_'+i+'*cboItemBtm_'+i+'*txtBtmRatio_'+i+'*cboItemBts_'+i+'*txtrowid_'+i+'*txttmpid_'+i,"../../../",i);
		}
		//alert(all_data);
		var data="action=save_update_delete_tamplete&operation="+operation+'&tot_row='+tot_row+all_data;
		
		http.open("POST","quick_costing_woven_controller.php",true);
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
				show_list_view( '','template_list_view','save_up_list_view','quick_costing_woven_controller','setFilterGrid(\'tbl_upListView\',-1)');
			}
		}
	}
	
	function get_temp_data(temp_id)
	{
		var list_view_grid = return_global_ajax_value( temp_id, 'load_php_dtls_form', '', 'quick_costing_woven_controller');
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
                            <? echo create_drop_down( "cboItemBts_1", 70, $qc_template_wovenItem_arr,"", 0, "-Select-", 1, "", "", "", "", "", "", "", "", "cboItemBts[]" ); ?></td>
                            <td width="53"><input style="width:40px;" type="text" class="text_boxes_numeric" name="txtBtsRatio[]" id="txtBtsRatio_1" /></td>
                            <td width="70"><? echo create_drop_down( "cboItemBtm_1", 70, $qc_template_wovenItem_arr,"", 0, "-Select-", 2, "", "", "", "", "", "", "", "", "cboItemBtm[]" ); ?></td>
                            <td width="53"><input style="width:40px;" type="text" class="text_boxes_numeric" name="txtBtmRatio[]" id="txtBtmRatio_1" /></td>
                            <td><input type="button" id="increaseset_1" style="width:23px" class="formbutton" value="+" onClick="add_break_down_tr(1)" />
                                <input type="button" id="decreaseset_1" style="width:23px" class="formbutton" value="-" onClick="javascript:fn_delete_tr(1 ,'tbl_tempCreat');"/></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <table width="370" cellspacing="0" border="0" height="40">
                <tr>
                	<? 
						$isteam_leader=return_field_value("user_tag_id","lib_marketing_team","user_tag_id='$user_id' and is_deleted=0 and status_active=1","user_tag_id");
						//echo $user_level.'-'.$isteam_leader;
						if($user_level==2 || $isteam_leader!='') $admin_or_leader="";  else $admin_or_leader="display:none";
					
					?>
                    <td align="center" class="button_container" style=" <? echo $admin_or_leader; ?>">
						<? echo load_submit_buttons($permission,"fnc_template_entry",0,0,"reset_form('tmpcreatfrm_1','save_up_list_view','','','$(\'#tbl_tempCreat tr:not(:first)\').remove();')",1); ?> </td> 
                </tr>
                <tr><td align="center"><input type="button" id="btn" style="width:70px" class="formbutton" value="Close" onClick="js_set_value();" /></td></tr>
            </table>
        </td><td align="center" valign="top"><div id="save_up_list_view"></div></td></tr>
        </table>
    </form>
    </body>
    <script>
	show_list_view( '','template_list_view','save_up_list_view','quick_costing_woven_controller','setFilterGrid(\'tbl_upListView\',-1)');
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
		$field_arr="id, temp_id, item_id1, ratio1, item_id2, ratio2, lib_item_id, inserted_by, insert_date, tuid";
		$m=1; $n=1; $tuid=0;
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
				if($bts_ratio=='') $bts_ratio=$qc_template_wovenItem_arr[str_replace("'","",$$itemBts)].$m; else $bts_ratio.=','.$qc_template_wovenItem_arr[str_replace("'","",$$itemBts)].$m;
				$m++;
			}
			
			for($k=1; $k<=str_replace("'","",$$btmRatio); $k++)
			{
				if($btm_ratio=='') $btm_ratio=$qc_template_wovenItem_arr[str_replace("'","",$$itemBtm)].$n; else $btm_ratio.=','.$qc_template_wovenItem_arr[str_replace("'","",$$itemBtm)].$n;
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
			$tuid=$_SESSION['logic_erp']['user_id'].$tempid;
			if ($i!=1) $data_arr .=",";
			$data_arr .="(".$rowid.",".$tempid.",".$$itemBts.",".$$btsRatio.",".$$itemBtm.",".$$btmRatio.",'".$libTemp_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$tuid."')";
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
		$field_arr="id, temp_id, item_id1, ratio1, item_id2, ratio2, lib_item_id, inserted_by, insert_date, tuid";
		$field_arr_up="item_id1*ratio1*item_id2*ratio2*lib_item_id*updated_by*update_date";
		$m=1; $n=1; $tuid=0;
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
				if($bts_ratio=='') $bts_ratio=$qc_template_wovenItem_arr[str_replace("'","",$$itemBts)].$m; else $bts_ratio.=','.$qc_template_wovenItem_arr[str_replace("'","",$$itemBts)].$m;
				$m++;
			}
			
			for($k=1; $k<=str_replace("'","",$$btmRatio); $k++)
			{
				if($btm_ratio=='') $btm_ratio=$qc_template_wovenItem_arr[str_replace("'","",$$itemBtm)].$n; else $btm_ratio.=','.$qc_template_wovenItem_arr[str_replace("'","",$$itemBtm)].$n;
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
				$tuid=$_SESSION['logic_erp']['user_id'].$tempid;
				if ($i!=1) $data_arr .=",";
				$data_arr .="(".$rowid.",".$$tempid.",".$$itemBts.",".$$btsRatio.",".$$itemBtm.",".$$btmRatio.",'".$libTemp_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$tuid."')";
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
				$sql_tmp="select tuid, temp_id, item_id1, ratio1, item_id2, ratio2, status_active from qc_template where status_active=1 and is_deleted=0 order by id ASC";
				$sql_tmp_res=sql_select($sql_tmp);
				$mst_temp_arr=array();
				foreach($sql_tmp_res as $row)
				{
					$mst_temp_arr[$row[csf('tuid')]].=$row[csf('item_id1')].'**'.$row[csf('ratio1')].'**'.$row[csf('item_id2')].'**'.$row[csf('ratio2')].'**'.$row[csf('status_active')].'__';
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
							if($bts_ratio=='') $bts_ratio=$qc_template_wovenItem_arr[$item_id1].$m; else $bts_ratio.=','.$qc_template_wovenItem_arr[$item_id1].$m;
							$m++;
						}
						$btm_ratio='';
						for($k=1; $k<=$ratio2; $k++)
						{
							if($btm_ratio=='') $btm_ratio=$qc_template_wovenItem_arr[$item_id2].$n; else $btm_ratio.=','.$qc_template_wovenItem_arr[$item_id2].$n;
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
	$sql_tmp="select tuid, temp_id, $concat_cond as lib_item_id from qc_template where status_active=1 and is_deleted=0 group by tuid, temp_id order by temp_id ASC";
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
		
		$template_name_arr[$row[csf('tuid')]]=$lib_temp_id;
	}
	
	echo create_drop_down( "cbo_temp_id", 130, $template_name_arr,'', 1, "-Select Template-",$selected, "fnc_template_view();" ); 
	exit();
}

if($action=="load_drop_down_tempItem")
{
	$sql_tmp="select id, item_name from lib_qc_template where status_active=1 and is_deleted=0 and id in ($data)";
	echo create_drop_down( "cboItemId", 90, $sql_tmp,"id,item_name", 0, "-Select-", $selected, "" );
	exit();
}

if($action=="load_php_dtls_form")
{
	$sql_tmp="select id, tuid, temp_id, item_id1, ratio1, item_id2, ratio2, status_active from qc_template where tuid='$data' and status_active=1 and is_deleted=0 order by id ASC";
	$sql_tmp_res=sql_select($sql_tmp); $i=1; $j=1;
	foreach($sql_tmp_res as $row)
	{
		?>
        <tr id="tr_<? echo $i; ?>">
            <td width="20"><? echo $i; ?></td>
            <td width="70"><input style="width:40px;" type="hidden" id="txtrowid_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>" />
            <input style="width:40px;" type="hidden" id="txttmpid_<? echo $i; ?>" value="<? echo $row[csf('temp_id')]; ?>" />
                <? echo create_drop_down( "cboItemBts_$i", 70, $qc_template_wovenItem_arr,"", 0, "-Select-", $row[csf('item_id1')], "", "", "", "", "", "", "", "", "cboItemBts[]" ); ?></td>
            <td width="53"><input style="width:40px;" type="text" class="text_boxes_numeric" name="txtBtsRatio[]" id="txtBtsRatio_<? echo $i; ?>" value="<? echo $row[csf('ratio1')]; ?>" /></td>
            <td width="70"><? echo create_drop_down( "cboItemBtm_$i", 70, $qc_template_wovenItem_arr,"", 0, "-Select-", $row[csf('item_id2')], "", "", "", "", "", "", "", "", "cboItemBtm[]" ); ?></td>
            <td width="53"><input style="width:40px;" type="text" class="text_boxes_numeric" name="txtBtmRatio[]" id="txtBtmRatio_<? echo $i; ?>" value="<? echo $row[csf('ratio2')]; ?>" /></td>
            <td><input type="button" id="increaseset_<? echo $i; ?>" style="width:23px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?>)" />
                <input type="button" id="decreaseset_<? echo $i; ?>" style="width:23px" class="formbutton" value="-" onClick="javascript:fn_delete_tr(<? echo $i; ?> ,'tbl_tempCreat');"/></td>
        </tr>
        <?
		$i++;
	}
	exit();
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
			//processLoss_cost=((tot_cost/(1-process_lossPer))-tot_cost);
			processLoss_cost=((tot_cost*process_lossPer));
			//alert(process_lossPer)
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
                    <td><input style="width:147px;" type="text" class="text_boxes" name="txt_yarn_dtls1" id="txt_yarn_dtls1" placeholder="Yarn Cost 1" onChange="fnc_total_rate();" /><input type="hidden" class="text_boxes" name="hidden_all_data" id="hidden_all_data" value="<? echo $rateData; ?>"/></td>
                    <td><input style="width:57px;" type="text" class="text_boxes_numeric" name="txt_ratePerKg1" id="txt_ratePerKg1" placeholder="Rate 1" onChange="fnc_yarn_cost_percent_check(1);" /></td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_yarn_costPer1" id="txt_yarn_costPer1" onChange="fnc_yarn_cost_percent_check(1);" /></td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_yarn_cost1" id="txt_yarn_cost1" onChange="fnc_total_rate();" /></td>
                </tr>
                <tr>
                    <td><input style="width:147px;" type="text" class="text_boxes" name="txt_yarn_dtls2" id="txt_yarn_dtls2" placeholder="Yarn Cost 2" onChange="fnc_total_rate();" /></td>
                    <td><input style="width:57px;" type="text" class="text_boxes_numeric" name="txt_ratePerKg2" id="txt_ratePerKg2" placeholder="Rate 2" onChange="fnc_yarn_cost_percent_check(2);" /></td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_yarn_costPer2" id="txt_yarn_costPer2" onChange="fnc_yarn_cost_percent_check(2);" /></td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_yarn_cost2" id="txt_yarn_cost2" onChange="fnc_total_rate();" /></td>
                </tr>
                <tr>
                    <td><input style="width:147px;" type="text" class="text_boxes" name="txt_yarn_dtls3" id="txt_yarn_dtls3" placeholder="Yarn Cost 3" /></td>
                    <td><input style="width:57px;" type="text" class="text_boxes_numeric" name="txt_ratePerKg3" id="txt_ratePerKg3" placeholder="Rate 3" onChange="fnc_yarn_cost_percent_check(3);" /></td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_yarn_costPer3" id="txt_yarn_costPer3" onChange="fnc_yarn_cost_percent_check(3);" /></td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_yarn_cost3" id="txt_yarn_cost3" onChange="fnc_total_rate();" /></td>
                </tr>
                <tr bgcolor="#CCCCAA">
                    <td colspan="2">Total Yarn Cost</td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_tot_yarn_costPer" id="txt_tot_yarn_costPer" value="100" disabled /></td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_tot_yarn_cost" id="txt_tot_yarn_cost" disabled /></td>
                </tr>
                <tr>
                    <td colspan="3">Knitting Charge</td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_knit_charge" id="txt_knit_charge" onChange="fnc_total_rate();" /></td>
                </tr>
                <tr>
                    <td colspan="3">Dyeing Charge</td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_dyeing_charge" id="txt_dyeing_charge" onChange="fnc_total_rate();" /></td>
                </tr>
                <tr>
                    <td colspan="3">AOP Charge</td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_aop_charge" id="txt_aop_charge" onChange="fnc_total_rate();" /></td>
                </tr>
                <tr>
                    <td colspan="3">Finishing Charge</td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_fin_charge" id="txt_fin_charge" onChange="fnc_total_rate();" /></td>
                </tr>
                <tr>
                    <td colspan="3">Other Cost</td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_other_cost" id="txt_other_cost" onChange="fnc_total_rate();" /></td>
                </tr>
                <tr bgcolor="#CCCCAA">
                    <td colspan="3">Total Cost</td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_tot_cost" id="txt_tot_cost" disabled /></td>
                </tr>
                <tr>
                    <td  colspan="2">Process Loss</td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_process_loss_per" id="txt_process_loss_per" onChange="fnc_total_rate();" /></td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_process_loss" id="txt_process_loss" onChange="fnc_total_rate();" disabled /></td>
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
				http.open("POST","quick_costing_woven_controller.php",true);
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
					$('#update_id').val(reponse[1]);
					set_button_status(1, permission, 'fnc_stage_entry',1);
					show_list_view('','stage_data_list','stage_data_div','quick_costing_woven_controller','');
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
    <script>show_list_view('','stage_data_list','stage_data_div','quick_costing_woven_controller','');</script>          
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
		$tuid=$_SESSION['logic_erp']['user_id'].$id;
		$field_array="id, stage_name, inserted_by, insert_date, tuid";
		$data_array="(".$id.",'".strtoupper(str_replace("'","",$txt_stage_name))."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$tuid."')";
		
		$rID=sql_insert("lib_stage_name",$field_array,$data_array,1);
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "0**".$tuid;
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
				echo "0**".$tuid;
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
		
		$rID=sql_update("lib_stage_name",$field_array,$data_array,"tuid","".$update_id."",1);
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id);
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
				echo "1**".str_replace("'","",$update_id);
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
	echo  create_list_view ( "list_view", "Stage Name", "200","200","150",1, "select tuid, stage_name from lib_stage_name where status_active=1 and is_deleted=0 order by tuid desc", "get_php_form_data", "tuid","'load_php_data_to_form_satge'", 1, "0", $arr, "stage_name","quick_costing_woven_controller", 'setFilterGrid("list_view",-1);','0' );
	exit();
}

if($action=="load_php_data_to_form_satge")
{
	$sql_arr=sql_select( "select tuid, stage_name from lib_stage_name where status_active=1 and is_deleted=0 and tuid='$data'" );
	foreach ($sql_arr as $inf)
	{
		echo "document.getElementById('txt_stage_name').value  			= '".$inf[csf("stage_name")]."';\n"; 
		echo "document.getElementById('update_id').value  				= '".$inf[csf("tuid")]."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_stage_entry',1);\n";  
	}
	exit();
}

if($action=="load_drop_stage_name")
{
	echo create_drop_down( "cbo_stage_id", 130,"select tuid, stage_name from lib_stage_name where status_active=1 and is_deleted=0","tuid,stage_name", 1, "--Select--", $selected, "" );
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
	$color_arr=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name"); 
	
	if ($operation!=0)
	{
		$approved=0;
		$sql=sql_select("select approved from qc_mst where id=$txt_update_id");
		foreach($sql as $row){
			$approved=$row[csf('approved')];
		}
		if($approved==3) $approved=1; else $approved=$approved;
		
		if($approved==1){
			echo "approvedQc**".str_replace("'","",$txt_costSheet_id);
			disconnect($con);die;
		}
	}
	
	$str_rep=array("/", "&", "*", "(", ")", "=","'",",",'"','#');
	if ($operation==0)  // Insert Here
	{	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}	
		//$update_id=str_replace("'","",$txt_update_id); //die;
		$dup_styleRef=str_replace("'","",$txt_styleRef);
		$dup_season_id=str_replace("'","",$cbo_season_id);
		$dup_buyer_id=str_replace("'","",$cbo_buyer_id);
		$msg="Duplicate Inquiry ID, Styel Ref, Buyer And Season.";
		if($type==1)
		{
			if (is_duplicate_field( "cost_sheet_no", "qc_mst", "style_ref='$dup_styleRef' and inquery_id=$txt_inquery_id and season_id='$dup_season_id' and buyer_id='$dup_buyer_id' and status_active=1 and is_deleted=0" ) == 1)
			{
				echo "11**".$msg; 
				disconnect($con);die;
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
		$txt_option_remarks=str_replace($str_rep,' ',str_replace("'","",$txt_option_remarks));
		$txt_costing_remarks=str_replace($str_rep,' ',str_replace("'","",$txt_costing_remarks));
		$txt_meeting_remarks=str_replace($str_rep,' ',str_replace("'","",$txt_meeting_remarks));
		
		if(str_replace("'","",$txt_bodywashcolor)!="")
		{
			if (!in_array(str_replace("'","",$txt_bodywashcolor),$new_array_color))
			{
				$color_id = return_id( str_replace("'","",$txt_bodywashcolor), $color_arr, "lib_color", "id,color_name","430");
				$new_array_color[$color_id]=str_replace("'","",$txt_bodywashcolor);
			}
			else $color_id =  array_search(str_replace("'","",$txt_bodywashcolor), $new_array_color);
		}
		else $color_id=0;
		
		//echo $revise_no."=select max(revise_no) as revise_no from qc_mst where cost_sheet_no='$txt_costSheetNo'"; die;
		$field_array_mst=" id, company_id, location_id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, inquery_id, style_ref, buyer_id, uom, cons_basis, season_id, season_year, brand_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, pre_cost_sheet_id, revise_no, option_id, buyer_remarks, option_remarks, body_color_id, qc_no, commercial_cost_method, entry_form, inserted_by, insert_date, status_active, is_deleted";
		
		$data_array_mst="(".$mst_id.",".$cbo_company_id.",".$cbo_location_id.",".$mst_id.",'".$autoCostSheetNo."',".$cbo_temp_id.",".$txt_temp_id.",".$txt_inquery_id.",'".strtoupper(str_replace("'","",$txt_styleRef))."',".$cbo_buyer_id.",".$cbouom.",".$cbo_cons_basis_id.",".$cbo_season_id.",".$cbo_season_year.",".$cbo_brand.",'".strtoupper(str_replace("'","",$txt_styleDesc))."',".$cbo_subDept_id.",".$txt_delivery_date.",".$txt_exchangeRate.",".$txt_offerQty.",".$txt_quotedPrice.",".$txt_tgtPrice.",".$cbo_stage_id.",".$txt_costingDate.",'".$update_id."','".$revise_no."','".$option_id."','".strtoupper($txt_costing_remarks)."','".strtoupper($txt_option_remarks)."','".$color_id."','".$qcno."',".$txt_commercial_cost_method.",430,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		
		//echo $data_array; die;
		$temp_id=explode(",",str_replace("'","",$txt_temp_id));
		
		$cons_rate_id=return_next_id("id", "qc_cons_rate_dtls", 1);
		$field_array_consrate="id, mst_id, item_id, type, particular_type_id, consumption, ex_percent, tot_cons, uom, rate, value, fab_id, description, use_for, fc_per, body_part_id, supp_ref, rate_data, nominated_supp_multi, inserted_by, insert_date, status_active, is_deleted";
		
		//fab_id, description, use_for, fc_per, body_part_id, supp_ref
		//type=1 fab; particular_type_id= bodyPartid
		//type=2 sp; particular_type_id=Special Operation Name
		//type=3 wash; particular_type_id=wash type
		//type=4 acc;  particular_type_id=item_group
		
		$add_comma_cons_rate=0; $data_array_cons_rate="";
		foreach($temp_id as $item_id)
		{
			$item_id=trim($item_id);
			
			$itemConsRateData='txtfabricData_'.$item_id;
			$ex_itemConsRateData=array_filter(explode('##',str_replace("'","",$$itemConsRateData)));
			foreach($ex_itemConsRateData as $item_wise_consRateData)
			{
				if($item_wise_consRateData!="")
				{
					//echo $item_wise_consRateData.'==';
					$ex_itemConsRate=explode('_',$item_wise_consRateData);
					$bodypartid=$fabDesc=$fabid=$usefor=$cons=$exPer=$totCons=$rate=$fcPer=$fabAmt=$fabNSupp=0;
					
					$bodypartid=$ex_itemConsRate[1];
					$fabDesc=$ex_itemConsRate[2];
					$fabid=$ex_itemConsRate[3];
					$usefor=$ex_itemConsRate[4];
					$usefor=str_replace($str_rep,' ',$usefor);
					$cons=$ex_itemConsRate[5];
					$exPer=$ex_itemConsRate[6];
					$totCons=$ex_itemConsRate[7];
					$rate=$ex_itemConsRate[8];
					$fcPer=$ex_itemConsRate[9];
					$fabAmt=$ex_itemConsRate[10];
					$fabNSupp=$ex_itemConsRate[11];
					
					if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
					$data_array_cons_rate .="(".$cons_rate_id.",".$qcno.",'".$item_id."',1,0,'".$cons."','".$exPer."','".$totCons."',0,'".$rate."','".$fabAmt."','".$fabid."','".$fabDesc."','".$usefor."','".$fcPer."','".$bodypartid."','0','0','".$fabNSupp."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					
					$cons_rate_id=$cons_rate_id+1;
					$add_comma_cons_rate++;
				}
			}
			
			$specialData='txtspData_'.$item_id;
			$ex_specialData=array_filter(explode('##',str_replace("'","",$$specialData)));
			foreach($ex_specialData as $item_wise_specialData)
			{
				$ex_itemSpConsRate=explode('_',$item_wise_specialData);
				
				$speciaOperation_id=$speciaTypeId=$spbodypartid=$spConsumtion=$spExper=$totSpCons=$spRate=$spValue=$spNSupp=0;
				$speciaOperation_id=$ex_itemSpConsRate[0];
				$speciaTypeId=$ex_itemSpConsRate[1];
				$spbodypartid=$ex_itemSpConsRate[3];
				
				$spConsumtion=$ex_itemSpConsRate[4];
				$spExper=$ex_itemSpConsRate[5];
				$totSpCons=$ex_itemSpConsRate[6];
				$spRate=$ex_itemSpConsRate[7];
				$spValue=$ex_itemSpConsRate[8];
				$spNSupp=$ex_itemSpConsRate[9];
				
				if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
				$data_array_cons_rate .="(".$cons_rate_id.",".$qcno.",'".$item_id."',2,'".$speciaOperation_id."','".$spConsumtion."','".$spExper."','".$totSpCons."',0,'".$spRate."','".$spValue."','".$speciaTypeId."','','',0,'".$spbodypartid."','','','".$spNSupp."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				
				$cons_rate_id=$cons_rate_id+1;
				$add_comma_cons_rate++;
			}
			
			$washData='txtwashData_'.$item_id;
			$ex_washData=array_filter(explode('##',str_replace("'","",$$washData)));
			foreach($ex_washData as $item_wise_washData)
			{
				$ex_itemwashConsRate=explode('_',$item_wise_washData);
				$washTypeId=$wbodypartid=$wConsumtion=$wExper=$totwCons=$wRate=$wValue=$wNsupp=0;
				
				$washTypeId=$ex_itemwashConsRate[1];
				$wbodypartid=$ex_itemwashConsRate[3];
				$wConsumtion=$ex_itemwashConsRate[4];
				
				$wExper=$ex_itemwashConsRate[5];
				$totwCons=$ex_itemwashConsRate[6];
				$wRate=$ex_itemwashConsRate[7];
				$wValue=$ex_itemwashConsRate[8];
				$wNsupp=$ex_itemwashConsRate[9];
				
				if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
				$data_array_cons_rate .="(".$cons_rate_id.",".$qcno.",'".$item_id."',3,'".$washTypeId."','".$wConsumtion."','".$wExper."','".$totwCons."',0,'".$wRate."','".$wValue."',0,'','',0,'".$wbodypartid."','','','".$wNsupp."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				
				$cons_rate_id=$cons_rate_id+1;
				$add_comma_cons_rate++;
			}
			
			$itemAccData='txtaccData_'.$item_id;
			$ex_itemAccData=array_filter(explode('##',str_replace("'","",$$itemAccData)));
			foreach($ex_itemAccData as $item_wise_itemAccData)
			{
				$ex_itemAcConsRate=explode('_',$item_wise_itemAccData);
				
				$accessories_id=$accDescription=$accBandRef=$acConsumtion=$acExPer=$totAccCons=$acRate=$acVal=$acUom=$accCalParaMeter=$accConsCalData=$accNSupp=0;
				
				$accessories_id=$ex_itemAcConsRate[1];
				$accDescription=$ex_itemAcConsRate[2];
				$accBandRef=$ex_itemAcConsRate[3];
				$accDescription=str_replace($str_rep,' ',$accDescription);
				$accBandRef=str_replace($str_rep,' ',$accBandRef);
				$acConsumtion=$ex_itemAcConsRate[4];
				$acExPer=$ex_itemAcConsRate[5];
				$totAccCons=$ex_itemAcConsRate[6];
				$acRate=$ex_itemAcConsRate[7];
				$acVal=$ex_itemAcConsRate[8];
				$acUom=$ex_itemAcConsRate[9];
				$accCalParaMeter=$ex_itemAcConsRate[10];
				$accConsCalData=$ex_itemAcConsRate[11];
				$accNSupp=$ex_itemAcConsRate[12];
				
				if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
				$data_array_cons_rate .="(".$cons_rate_id.",".$qcno.",'".$item_id."',4,'".$accessories_id."','".$acConsumtion."','".$acExPer."','".$totAccCons."','".$acUom."','".$acRate."','".$acVal."',0,'".$accDescription."','',0,0,'".$accBandRef."','".$accConsCalData."','".$accNSupp."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				
				$cons_rate_id=$cons_rate_id+1;
				$add_comma_cons_rate++;
			}
		}
		//echo "10**";
		//echo $data_array_fab; 
		//die;
		$tc_sum_id=return_next_id("id", "qc_tot_cost_summary", 1);
		$field_array_tot_cost="id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_wash_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_courier_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, tot_rmg_ratio, commercial_per, commercial_cost, inspection_cost, lc_per, lc_cost, inserted_by, insert_date, status_active, is_deleted";
		
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
		$totWash_td=trim($ex_tot_cost_data[8]);
		$totAcc_td=trim($ex_tot_cost_data[9]);
		$totCm_td=trim($ex_tot_cost_data[10]);
		$totFriCst_td=trim($ex_tot_cost_data[11]);
		$totCourierCst_td=trim($ex_tot_cost_data[12]);
		$totLbTstCst_td=trim($ex_tot_cost_data[13]);
		$totMissCst_td=trim($ex_tot_cost_data[14]);
		$totOtherCst_td=trim($ex_tot_cost_data[15]);
		$totCommCst_td=trim($ex_tot_cost_data[16]);
		$totCost_td=trim($ex_tot_cost_data[17]);
		$totalFob_td=trim($ex_tot_cost_data[18]);
		$totRmgQty_td=trim($ex_tot_cost_data[19]);
		$totCommlCst_td=trim($ex_tot_cost_data[20]);
		$txt_commlPer=trim($ex_tot_cost_data[21]);
		$txtInspCost=trim($ex_tot_cost_data[22]);
		$txt_lcPer=trim($ex_tot_cost_data[23]);
		$totDlcCst_td=trim($ex_tot_cost_data[24]);
		
		$data_array_tot_cost="(".$tc_sum_id.",".$qcno.",'".$cbo_buyer_agent."','".$cbo_agent_location."','".$txt_noOfPack."',0,'".$cmPop."','".$txt_lumSum_cost."','".$txt_commPer."','".$totFab_td."','".$totSpc_td."','".$totWash_td."','".$totAcc_td."','".$totCm_td."','".$totFriCst_td."','".$totCourierCst_td."','".$totLbTstCst_td."','".$totMissCst_td."','".$totOtherCst_td."','".$totCommCst_td."','".$totCost_td."','".$totalFob_td."','".$totRmgQty_td."','".$txt_commlPer."','".$totCommlCst_td."','".$txtInspCost."','".$txt_lcPer."','".$totDlcCst_td."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		//echo $data_array_tot_cost; die;
		$field_array_item_tot=" id, mst_id, item_id, fabric_cost, sp_operation_cost, wash_cost, accessories_cost, cpm, smv, efficiency, cm_cost, frieght_cost, courier_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs, rmg_ratio, commercial_cost, inspection_cost, lc_cost, inserted_by, insert_date, status_active, is_deleted";
		$item_cost_id=return_next_id("id", "qc_item_cost_summary", 1);
		$ex_item_wise_tot_data=array_filter(explode('__',str_replace("'","",$item_wise_tot_data)));
		$add_comma_item_tot=0; $data_array_item_tot="";
		foreach($ex_item_wise_tot_data as $item_wise_tot)
		{
			$fab_td=$spOpe_td=$wash_td=$acc_td=$txtCpm=$txtSmv=$txtEff=$txtCmCost=$txtFriCost=$txtCourierCost=$txtLtstCost=$txtMissCost=$txtOtherCost=$txtCommCost=$fobT_td=$fobDzn_td=$txtCommlCost=$txtInspCost=$txtdlcCost=$tot_item_id=0;
			
			$ex_item_tot=explode('_',$item_wise_tot);
			$fab_td=trim($ex_item_tot[0]);
			$spOpe_td=trim($ex_item_tot[1]);
			$wash_td=trim($ex_item_tot[2]);
			$acc_td=trim($ex_item_tot[3]);
			$txtCpm=trim($ex_item_tot[4]);
			$txtSmv=trim($ex_item_tot[5]);
			$txtEff=trim($ex_item_tot[6]);
			$txtCmCost=trim($ex_item_tot[7]);
			$txtFriCost=trim($ex_item_tot[8]);
			$txtCourierCost=trim($ex_item_tot[9]);
			$txtLtstCost=trim($ex_item_tot[10]);
			$txtMissCost=trim($ex_item_tot[11]);
			$txtOtherCost=trim($ex_item_tot[12]);
			$txtCommCost=trim($ex_item_tot[13]);
			$fobT_td=trim($ex_item_tot[14]);
			$fobDzn_td=trim($ex_item_tot[15]);
			$txtRmgQty=trim($ex_item_tot[16]);
			$txtCommlCost=trim($ex_item_tot[17]);
			$txtInspCost=trim($ex_item_tot[18]);
			$txtdlcCost=trim($ex_item_tot[19]);
			$tot_item_id=trim($ex_item_tot[20]);
			
			if ($add_comma_item_tot!=0) $data_array_item_tot .=",";
			$data_array_item_tot .="(".$item_cost_id.",".$qcno.",'".$tot_item_id."','".$fab_td."','".$spOpe_td."','".$wash_td."','".$acc_td."','".$txtCpm."','".$txtSmv."','".$txtEff."','".$txtCmCost."','".$txtFriCost."','".$txtCourierCost."','".$txtLtstCost."','".$txtMissCost."','".$txtOtherCost."','".$txtCommCost."','".$fobT_td."','".$txtRmgQty."','".$txtCommlCost."','".$txtInspCost."', '".$txtdlcCost."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			
			$item_cost_id=$item_cost_id+1;
			$add_comma_item_tot++;
		}
		
		/*if(str_replace("'","",$chk_is_new_meeting)==1)
		{*/
			$buyer_meeting_id=return_next_id("id", "qc_meeting_mst", 1);
			$field_arr_buyer_meeting="id, mst_id, meeting_no, buyer_agent_id, location_id, meeting_date, meeting_time, remarks, inserted_by, insert_date, status_active, is_deleted";
		if(str_replace("'","",$txt_meeting_remarks)!="" && str_replace("'","",$txt_meeting_remarks)!="1. ")
		{
			if($db_type==2)
			{
				$txt_meeting_hour=str_replace("'","",$txt_meeting_date)." ".str_replace("'","",$txt_meeting_time);
				$txt_meeting_hour="to_date('".$txt_meeting_hour."','DD MONTH YYYY HH24:MI:SS')";
				
				$data_arr_buyer_meeting="INSERT INTO qc_meeting_mst (".$field_arr_buyer_meeting.") VALUES (".$buyer_meeting_id.",".$qcno.",".$txt_meeting_no.",'".$cbo_buyer_agent."','".$cbo_agent_location."',".$txt_meeting_date.",".$txt_meeting_hour.",'".$txt_meeting_remarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			}
			else
			{
				$data_arr_buyer_meeting="(".$buyer_meeting_id.",".$qcno.",".$txt_meeting_no.",'".$cbo_buyer_agent."','".$cbo_agent_location."',".$txt_meeting_date.",".$txt_meeting_time.",'".$txt_meeting_remarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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
		//echo "10**insert into qc_cons_rate_dtls (".$field_array_consrate.") values ".$data_array_cons_rate;die;
		/*$meetMst_id=return_next_id("id", "qc_meeting_mst", 1);
		$meetPer_id=return_next_id("id", "qc_meeting_mst", 1);
		$meetDtls_id=return_next_id("id", "qc_meeting_mst", 1);*/
		
		$flag=1;
		
		$rID=sql_insert("qc_mst",$field_array_mst,$data_array_mst,0);	
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID_consRate=sql_insert("qc_cons_rate_dtls",$field_array_consrate,$data_array_cons_rate,0);	
		if($rID_consRate==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID_tot_cost=sql_insert("qc_tot_cost_summary",$field_array_tot_cost,$data_array_tot_cost,0);	
		if($rID_tot_cost==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID_item_tot=sql_insert("qc_item_cost_summary",$field_array_item_tot,$data_array_item_tot,0);	
		if($rID_item_tot==1 && $flag==1) $flag=1; else $flag=0;
		
		//echo '10**'.$flag.'='.$rID_consRate.'='.$rID_tot_cost.'='.$rID_item_tot.'='.$rID; die;
		/*if(str_replace("'","",$chk_is_new_meeting)==1)
		{*/
		if(str_replace("'","",$txt_meeting_remarks)!="" && str_replace("'","",$txt_meeting_remarks)!="1. ")
		{
			if($db_type==2)
			{
				$rID_buyer_meeting=execute_query($data_arr_buyer_meeting);
				if($rID_buyer_meeting==1 && $flag==1) $flag=1; else $flag=0;
			}
			else
			{
				$rID_buyer_meeting=sql_insert("qc_meeting_mst", $field_arr_buyer_meeting, $data_arr_buyer_meeting,1);	
				if($rID_buyer_meeting==1 && $flag==1) $flag=1; else $flag=0;
			}
			
			$rID_up=sql_update("qc_mst",$field_arr_mst_up,$data_arr_mst_up,"id","".$mst_id."",1);
			if($rID_up==1 && $flag==1) $flag=1; else $flag=0;
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
				echo "0**".str_replace("'",'',$mst_id).'**'.str_replace("'",'',$autoCostSheetNo).'**'.str_replace("'",'',$revise_no).'**'.str_replace("'",'',$option_id).'**'.str_replace("'",'',$type).'**'.str_replace("'",'',$buyer_meeting_id).'**'.str_replace("'",'',$qcno);
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
				echo "0**".str_replace("'",'',$mst_id).'**'.str_replace("'",'',$autoCostSheetNo).'**'.str_replace("'",'',$revise_no).'**'.str_replace("'",'',$option_id).'**'.str_replace("'",'',$type).'**'.str_replace("'",'',$buyer_meeting_id).'**'.str_replace("'",'',$qcno);
			}
			else
			{
				oci_rollback($con); 
				echo "10**";
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
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
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		
		$msg_confirm="This option is already confirm. So You can't Change, Edit or Delete it.";
		$isconfirm_id=str_replace("'","",$hid_qc_no);
		if (is_duplicate_field( "cost_sheet_id", "qc_confirm_mst", "cost_sheet_id='$isconfirm_id' and status_active=1 and is_deleted=0") == 1)
		{
			echo "11**".$msg_confirm; 
			disconnect($con);
			die;
		}
		
		$dup_styleRef=str_replace("'","",$txt_styleRef);
		$dup_season_id=str_replace("'","",$cbo_season_id);
		$dup_buyer_id=str_replace("'","",$cbo_buyer_id);
		$msg="Duplicate Inquiry ID, Styel Ref, Buyer And Season.";
		
		if (is_duplicate_field( "cost_sheet_no", "qc_mst", "style_ref='$dup_styleRef' and inquery_id=$txt_inquery_id and season_id='$dup_season_id' and buyer_id='$dup_buyer_id' and cost_sheet_no!=$txt_costSheetNo and status_active=1 and is_deleted=0") == 1)
		{
			echo "11**".$msg; 
			disconnect($con);
			die;
		}
		
		$insert_user_id=return_field_value("inserted_by","qc_mst","id=".$txt_update_id."","inserted_by");
		$updated_by_user=return_field_value("updated_by","qc_mst","id=".$txt_update_id."","updated_by");
		$team_dtls_arr=array();
		//$team_dtls_sql=sql_select("Select a.user_tag_id as team_leader, b.user_tag_id as team_member from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and a.user_tag_id='$user_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		if( ($updated_by_user*1)==0 ) $updated_by_user=$user_id;
		
		$team_dtls_sql=sql_select("select b.user_tag_id as team_member from  lib_mkt_team_member_info b 
where b.team_id in (select id from lib_marketing_team a where user_tag_id='$user_id' and a.status_active=1 and a.is_deleted=0 ) and b.status_active=1 and b.is_deleted=0");
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
		if($team_dtls_arr[$insert_user_id]=='' && $_SESSION['logic_erp']["user_level"]!=2)
		{
			echo "6**"."Update restricted, This Information is Update Only team Leader or member.";
			disconnect($con);die;
		}
		$txt_option_remarks=str_replace($str_rep,' ',str_replace("'","",$txt_option_remarks));
		$txt_costing_remarks=str_replace($str_rep,' ',str_replace("'","",$txt_costing_remarks));
		$txt_meeting_remarks=str_replace($str_rep,' ',str_replace("'","",$txt_meeting_remarks));
		$qcno=str_replace("'","",$hid_qc_no);
		
		if(str_replace("'","",$txt_bodywashcolor)!="")
		{
			if (!in_array(str_replace("'","",$txt_bodywashcolor),$new_array_color))
			{
				$color_id = return_id( str_replace("'","",$txt_bodywashcolor), $color_arr, "lib_color", "id,color_name","430");
				$new_array_color[$color_id]=str_replace("'","",$txt_bodywashcolor);
			}
			else $color_id =  array_search(str_replace("'","",$txt_bodywashcolor), $new_array_color);
		}
		else $color_id=0;
		
		$field_array_mst="location_id*temp_id*lib_item_id*inquery_id*style_ref*buyer_id*uom*cons_basis*season_id*season_year*brand_id*style_des*department_id*delivery_date*exchange_rate*offer_qty*quoted_price*tgt_price*stage_id*costing_date*buyer_remarks*option_remarks*body_color_id*updated_by*update_date";
		
		//$txt_temp_id=explode(",",str_replace("'","",$txt_temp_id));
		//asort($txt_temp_id);
		//$txt_temp_id="'".implode(",",$txt_temp_id)."'";
		
		$data_array_mst="".$cbo_location_id."*".$cbo_temp_id."*".$txt_temp_id."*".$txt_inquery_id."*'".strtoupper(str_replace("'","",$txt_styleRef))."'*".$cbo_buyer_id."*".$cbouom."*".$cbo_cons_basis_id."*".$cbo_season_id."*".$cbo_season_year."*".$cbo_brand."*'".strtoupper(str_replace("'","",$txt_styleDesc))."'*".$cbo_subDept_id."*".$txt_delivery_date."*".$txt_exchangeRate."*".$txt_offerQty."*".$txt_quotedPrice."*".$txt_tgtPrice."*".$cbo_stage_id."*".$txt_costingDate."*'".strtoupper($txt_costing_remarks)."'*'".strtoupper($txt_option_remarks)."'*'".$color_id."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		//$template_id = return_id( $txt_temp_id, $template_library, "qc_template", "id,item_id");
		
		$temp_id=explode(",",str_replace("'","",$txt_temp_id));
		$cons_rate_id=return_next_id("id", "qc_cons_rate_dtls", 1);
		$field_array_consrate="id, mst_id, item_id, type, particular_type_id, consumption, ex_percent, tot_cons, uom, rate, value, fab_id, description, use_for, fc_per, body_part_id, supp_ref, rate_data, nominated_supp_multi, inserted_by, insert_date, status_active, is_deleted";
		
		//fab_id, description, use_for, fc_per, body_part_id, supp_ref
		//type=1 fab; particular_type_id= bodyPartid
		//type=2 sp; particular_type_id=Special Operation Name
		//type=3 wash; particular_type_id=wash type
		//type=4 acc;  particular_type_id=item_group
		
		$add_comma_cons_rate=0; $data_array_cons_rate="";
		foreach($temp_id as $item_id)
		{
			$item_id=trim($item_id);
			
			$itemConsRateData='txtfabricData_'.$item_id;
			$ex_itemConsRateData=array_filter(explode('##',str_replace("'","",$$itemConsRateData)));
			foreach($ex_itemConsRateData as $item_wise_consRateData)
			{
				if($item_wise_consRateData!="")
				{
					//echo $item_wise_consRateData.'==';
					$ex_itemConsRate=explode('_',$item_wise_consRateData);
					$bodypartid=$fabDesc=$fabid=$usefor=$cons=$exPer=$totCons=$rate=$fcPer=$fabAmt=$fabNSupp=0;
					
					$bodypartid=$ex_itemConsRate[1];
					$fabDesc=$ex_itemConsRate[2];
					$fabid=$ex_itemConsRate[3];
					$usefor=$ex_itemConsRate[4];
					$usefor=str_replace($str_rep,' ',$usefor);
					$cons=$ex_itemConsRate[5];
					$exPer=$ex_itemConsRate[6];
					$totCons=$ex_itemConsRate[7];
					$rate=$ex_itemConsRate[8];
					$fcPer=$ex_itemConsRate[9];
					$fabAmt=$ex_itemConsRate[10];
					$fabNSupp=$ex_itemConsRate[11];
					
					if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
					$data_array_cons_rate .="(".$cons_rate_id.",".$qcno.",'".$item_id."',1,0,'".$cons."','".$exPer."','".$totCons."',0,'".$rate."','".$fabAmt."','".$fabid."','".$fabDesc."','".$usefor."','".$fcPer."','".$bodypartid."','0','0','".$fabNSupp."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					
					$cons_rate_id=$cons_rate_id+1;
					$add_comma_cons_rate++;
				}
			}
			
			$specialData='txtspData_'.$item_id;
			$ex_specialData=array_filter(explode('##',str_replace("'","",$$specialData)));
			foreach($ex_specialData as $item_wise_specialData)
			{
				$ex_itemSpConsRate=explode('_',$item_wise_specialData);
				
				$speciaOperation_id=$speciaTypeId=$spbodypartid=$spConsumtion=$spExper=$totSpCons=$spRate=$spValue=$spNSupp=0;
				$speciaOperation_id=$ex_itemSpConsRate[0];
				$speciaTypeId=$ex_itemSpConsRate[1];
				$spbodypartid=$ex_itemSpConsRate[3];
				
				$spConsumtion=$ex_itemSpConsRate[4];
				$spExper=$ex_itemSpConsRate[5];
				$totSpCons=$ex_itemSpConsRate[6];
				$spRate=$ex_itemSpConsRate[7];
				$spValue=$ex_itemSpConsRate[8];
				$spNSupp=$ex_itemSpConsRate[9];
				
				if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
				$data_array_cons_rate .="(".$cons_rate_id.",".$qcno.",'".$item_id."',2,'".$speciaOperation_id."','".$spConsumtion."','".$spExper."','".$totSpCons."',0,'".$spRate."','".$spValue."','".$speciaTypeId."','','',0,'".$spbodypartid."','','','".$spNSupp."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				
				$cons_rate_id=$cons_rate_id+1;
				$add_comma_cons_rate++;
			}
			
			$washData='txtwashData_'.$item_id;
			$ex_washData=array_filter(explode('##',str_replace("'","",$$washData)));
			foreach($ex_washData as $item_wise_washData)
			{
				$ex_itemwashConsRate=explode('_',$item_wise_washData);
				$washTypeId=$wbodypartid=$wConsumtion=$wExper=$totwCons=$wRate=$wValue=$wNsupp=0;
				
				$washTypeId=$ex_itemwashConsRate[1];
				$wbodypartid=$ex_itemwashConsRate[3];
				$wConsumtion=$ex_itemwashConsRate[4];
				
				$wExper=$ex_itemwashConsRate[5];
				$totwCons=$ex_itemwashConsRate[6];
				$wRate=$ex_itemwashConsRate[7];
				$wValue=$ex_itemwashConsRate[8];
				$wNsupp=$ex_itemwashConsRate[9];
				
				if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
				$data_array_cons_rate .="(".$cons_rate_id.",".$qcno.",'".$item_id."',3,'".$washTypeId."','".$wConsumtion."','".$wExper."','".$totwCons."',0,'".$wRate."','".$wValue."',0,'','',0,'".$wbodypartid."','','','".$wNsupp."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				
				$cons_rate_id=$cons_rate_id+1;
				$add_comma_cons_rate++;
			}

			$itemAccData='txtaccData_'.$item_id;
			$ex_itemAccData=array_filter(explode('##',str_replace("'","",$$itemAccData)));
			foreach($ex_itemAccData as $item_wise_itemAccData)
			{
				$ex_itemAcConsRate=explode('_',$item_wise_itemAccData);
				
				$accessories_id=$accDescription=$accBandRef=$acConsumtion=$acExPer=$totAccCons=$acRate=$acVal=$acUom=$accCalParaMeter=$accConsCalData=0;
				
				$accessories_id=$ex_itemAcConsRate[1];
				$accDescription=$ex_itemAcConsRate[2];
				$accDescription=str_replace($str_rep,' ',$accDescription);
				$accBandRef=$ex_itemAcConsRate[3];
				$accBandRef=str_replace($str_rep,' ',$accBandRef);
				$acConsumtion=$ex_itemAcConsRate[4];
				$acExPer=$ex_itemAcConsRate[5];
				$totAccCons=$ex_itemAcConsRate[6];
				$acRate=$ex_itemAcConsRate[7];
				$acVal=$ex_itemAcConsRate[8];
				$acUom=$ex_itemAcConsRate[9];
				$accCalParaMeter=$ex_itemAcConsRate[10];
				$accConsCalData=$ex_itemAcConsRate[11];
				$accNSupp=$ex_itemAcConsRate[12];
				
				if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
				$data_array_cons_rate .="(".$cons_rate_id.",".$qcno.",'".$item_id."',4,'".$accessories_id."','".$acConsumtion."','".$acExPer."','".$totAccCons."','".$acUom."','".$acRate."','".$acVal."',0,'".$accDescription."','',0,0,'".$accBandRef."','".$accConsCalData."','".$accNSupp."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				
				$cons_rate_id=$cons_rate_id+1;
				$add_comma_cons_rate++;
			}
		}
		
		//echo "10**";
		//print_r ($data_array_fab); 
		//echo bulk_update_sql_statement("qc_fabric_dtls", "id",$field_array_fab,$data_array_fab,$id_arr ); die;
		//die;
		//echo $data_array_cons_rate; 
		
		$tc_sum_id=return_next_id("id", "qc_tot_cost_summary", 1);
		$field_array_tot_cost="id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_wash_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_courier_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, tot_rmg_ratio, commercial_per, commercial_cost, inspection_cost, lc_per, lc_cost, inserted_by, insert_date, status_active, is_deleted";
		
		$ex_tot_cost_data=explode('_',$data_tot_cost_summ);
		
		$cbo_buyer_agent=trim($ex_tot_cost_data[0]);
		$cbo_agent_location=trim($ex_tot_cost_data[1]);
		$txt_noOfPack=trim($ex_tot_cost_data[2]);
		$cmPop=trim($ex_tot_cost_data[3]);
		$txt_lumSum_cost=trim($ex_tot_cost_data[4]);
		$txt_commPer=trim($ex_tot_cost_data[5]);
		$totFab_td=trim($ex_tot_cost_data[6]);
		$totSpc_td=trim($ex_tot_cost_data[7]);
		$totWash_td=trim($ex_tot_cost_data[8]);
		$totAcc_td=trim($ex_tot_cost_data[9]);
		$totCm_td=trim($ex_tot_cost_data[10]);
		$totFriCst_td=trim($ex_tot_cost_data[11]);
		$totCourierCst_td=trim($ex_tot_cost_data[12]);
		$totLbTstCst_td=trim($ex_tot_cost_data[13]);
		$totMissCst_td=trim($ex_tot_cost_data[14]);
		$totOtherCst_td=trim($ex_tot_cost_data[15]);
		$totCommCst_td=trim($ex_tot_cost_data[16]);
		$totCost_td=trim($ex_tot_cost_data[17]);
		$totalFob_td=trim($ex_tot_cost_data[18]);
		$totRmgQty_td=trim($ex_tot_cost_data[19]);
		$totCommlCst_td=trim($ex_tot_cost_data[20]);
		$txt_commlPer=trim($ex_tot_cost_data[21]);
		$txtInspCost=trim($ex_tot_cost_data[22]);
		$txt_lcPer=trim($ex_tot_cost_data[23]);
		$totDlcCst_td=trim($ex_tot_cost_data[24]);
		
		$data_array_tot_cost="(".$tc_sum_id.",".$qcno.",'".$cbo_buyer_agent."','".$cbo_agent_location."','".$txt_noOfPack."',0,'".$cmPop."','".$txt_lumSum_cost."','".$txt_commPer."','".$totFab_td."','".$totSpc_td."','".$totWash_td."','".$totAcc_td."','".$totCm_td."','".$totFriCst_td."','".$totCourierCst_td."','".$totLbTstCst_td."','".$totMissCst_td."','".$totOtherCst_td."','".$totCommCst_td."','".$totCost_td."','".$totalFob_td."','".$totRmgQty_td."','".$txt_commlPer."','".$totCommlCst_td."','".$txtInspCost."','".$txt_lcPer."','".$totDlcCst_td."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		//echo $data_array_tot_cost; die;
		$field_array_item_tot=" id, mst_id, item_id, fabric_cost, sp_operation_cost, wash_cost, accessories_cost, cpm, smv, efficiency, cm_cost, frieght_cost, courier_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs, rmg_ratio, commercial_cost, inspection_cost, lc_cost, inserted_by, insert_date, status_active, is_deleted";
		$item_cost_id=return_next_id("id", "qc_item_cost_summary", 1);
		$ex_item_wise_tot_data=array_filter(explode('__',str_replace("'","",$item_wise_tot_data)));
		$add_comma_item_tot=0; $data_array_item_tot="";
		foreach($ex_item_wise_tot_data as $item_wise_tot)
		{
			$fab_td=$spOpe_td=$wash_td=$acc_td=$txtCpm=$txtSmv=$txtEff=$txtCmCost=$txtFriCost=$txtCourierCost=$txtLtstCost=$txtMissCost=$txtOtherCost=$txtCommCost=$fobT_td=$fobDzn_td=$txtCommlCost=$txtInspCost=$txtdlcCost=$tot_item_id=0;
			
			$ex_item_tot=explode('_',$item_wise_tot);
			$fab_td=trim($ex_item_tot[0]);
			$spOpe_td=trim($ex_item_tot[1]);
			$wash_td=trim($ex_item_tot[2]);
			$acc_td=trim($ex_item_tot[3]);
			$txtCpm=trim($ex_item_tot[4]);
			$txtSmv=trim($ex_item_tot[5]);
			$txtEff=trim($ex_item_tot[6]);
			$txtCmCost=trim($ex_item_tot[7]);
			$txtFriCost=trim($ex_item_tot[8]);
			$txtCourierCost=trim($ex_item_tot[9]);
			$txtLtstCost=trim($ex_item_tot[10]);
			$txtMissCost=trim($ex_item_tot[11]);
			$txtOtherCost=trim($ex_item_tot[12]);
			$txtCommCost=trim($ex_item_tot[13]);
			$fobT_td=trim($ex_item_tot[14]);
			$fobDzn_td=trim($ex_item_tot[15]);
			$txtRmgQty=trim($ex_item_tot[16]);
			$txtCommlCost=trim($ex_item_tot[17]);
			$txtInspCost=trim($ex_item_tot[18]);
			$txtdlcCost=trim($ex_item_tot[19]);
			$tot_item_id=trim($ex_item_tot[20]);
			
			if ($add_comma_item_tot!=0) $data_array_item_tot .=",";
			$data_array_item_tot .="(".$item_cost_id.",".$qcno.",'".$tot_item_id."','".$fab_td."','".$spOpe_td."','".$wash_td."','".$acc_td."','".$txtCpm."','".$txtSmv."','".$txtEff."','".$txtCmCost."','".$txtFriCost."','".$txtCourierCost."','".$txtLtstCost."','".$txtMissCost."','".$txtOtherCost."','".$txtCommCost."','".$fobT_td."','".$txtRmgQty."','".$txtCommlCost."','".$txtInspCost."','".$txtdlcCost."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			
			$item_cost_id=$item_cost_id+1;
			$add_comma_item_tot++;
		}
		
		$meeting_update_id=return_field_value("meeting_no","qc_mst","qc_no=$qcno and is_deleted=0 and status_active=1","meeting_no");
		$max_meeting=return_field_value("max(meeting_no) as max_meeting_no","qc_meeting_mst"," mst_id =$qcno and is_deleted=0 and status_active=1","max_meeting_no");
		$meeting_qc_no=return_field_value("qc_no","qc_mst","meeting_no='$meeting_update_id' and is_deleted=0 and status_active=1","qc_no");
		$meeting_mst_qc_no=return_field_value("id","qc_meeting_mst","mst_id =$qcno and is_deleted=0 and status_active=1","id");
		if($max_meeting=="") $max_meeting=0;
		
		if(str_replace("'","",$txt_meeting_no)!=$max_meeting || $qcno!=$meeting_qc_no || $meeting_mst_qc_no=='')
		{
			if(str_replace("'","",$txt_meeting_remarks)!="" && str_replace("'","",$txt_meeting_remarks)!="1. ")
			{
				$buyer_meeting_id=return_next_id("id", "qc_meeting_mst", 1);
				$field_arr_buyer_meeting="id, mst_id, meeting_no, buyer_agent_id, location_id, meeting_date, meeting_time, remarks, inserted_by, insert_date, status_active, is_deleted";
				if($db_type==2)
				{
					$txt_meeting_hour=str_replace("'","",$txt_meeting_date)." ".str_replace("'","",$txt_meeting_time);
					$txt_meeting_hour="to_date('".$txt_meeting_hour."','DD MONTH YYYY HH24:MI:SS')";
					
					$data_arr_buyer_meeting="INSERT INTO qc_meeting_mst (".$field_arr_buyer_meeting.") VALUES (".$buyer_meeting_id.",".$qcno.",".$txt_meeting_no.",'".$cbo_buyer_agent."','".$cbo_agent_location."',".$txt_meeting_date.",".$txt_meeting_hour.",'".$txt_meeting_remarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				}
				else
				{
					$data_arr_buyer_meeting="(".$buyer_meeting_id.",".$qcno.",".$txt_meeting_no.",'".$cbo_buyer_agent."','".$cbo_agent_location."',".$txt_meeting_date.",".$txt_meeting_time.",'".$txt_meeting_remarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				}
				// echo "10**".str_replace("'","",$txt_meeting_no).'='.$max_meeting.'='.$qcno.'='.$data_arr_buyer_meeting; die;
				$field_arr_mst_up="meeting_no";
				$data_arr_mst_up="".$buyer_meeting_id."";
				//echo "10**".$data_arr_buyer_meeting;die;
			}
		}
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
					$txt_meeting_hour=$txt_meeting_time;
				}
				$field_arr_buyer_meeting_up="buyer_agent_id*location_id*meeting_date*meeting_time*remarks*updated_by*update_date";
				$data_arr_buyer_meeting_up="'".$cbo_buyer_agent."'*'".$cbo_agent_location."'*".$txt_meeting_date."*".$txt_meeting_hour."*'".$txt_meeting_remarks."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				
				$field_arr_mst_up="meeting_no";
				$data_arr_mst_up="".$meeting_update_id."";
			}
		}
		
		//echo "10**"."insert into qc_cons_rate_dtls (".$field_array_consrate.") values ".$data_array_cons_rate; die;
		/*$meetMst_id=return_next_id("id", "qc_meeting_mst", 1);
		$meetPer_id=return_next_id("id", "qc_meeting_mst", 1);
		$meetDtls_id=return_next_id("id", "qc_meeting_mst", 1);*/
		
		
		$rID=sql_update("qc_mst",$field_array_mst,$data_array_mst,"id","".$txt_update_id."",1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		//echo bulk_update_sql_statement("qc_fabric_dtls", "id",$field_array_fab,$data_array_fab,$id_arr ); die;
		//$rID_fab=execute_query(bulk_update_sql_statement("qc_fabric_dtls", "id",$field_array_fab,$data_array_fab,$id_arr ));
		//$rID_fab=sql_insert("qc_fabric_dtls",$field_array_fab,$data_array_fab,0);	
		//if($rID_fab==1 && $flag==1) $flag=1; else $flag=0;
		
		$d_cons_rate=execute_query( "delete from qc_cons_rate_dtls where  mst_id =".$hid_qc_no."",1);
		if($d_cons_rate==1 && $flag==1) $flag=1; else  $flag=0;
		
		$rID_consRate=sql_insert("qc_cons_rate_dtls",$field_array_consrate,$data_array_cons_rate,0);	
		if($rID_consRate==1 && $flag==1) $flag=1; else $flag=0;
		
		$d_tot_cost=execute_query( "delete from qc_tot_cost_summary where mst_id =".$hid_qc_no."",1);
		if($d_tot_cost==1 && $flag==1) $flag=1; else  $flag=0;
		
		$rID_tot_cost=sql_insert("qc_tot_cost_summary",$field_array_tot_cost,$data_array_tot_cost,0);	
		if($rID_tot_cost==1 && $flag==1) $flag=1; else $flag=0;
		
		$d_item_cost=execute_query( "delete from qc_item_cost_summary where  mst_id =".$hid_qc_no."",1);
		if($d_item_cost==1 && $flag==1) $flag=1; else  $flag=0;
		
		$rID_item_tot=sql_insert("qc_item_cost_summary",$field_array_item_tot,$data_array_item_tot,0);	
		if($rID_item_tot==1 && $flag==1) $flag=1; else $flag=0;
		 
		if(str_replace("'","",$txt_meeting_no)!=$max_meeting || $qcno!=$meeting_qc_no || $meeting_mst_qc_no=='')
		{
			if(str_replace("'","",$txt_meeting_remarks)!="" && str_replace("'","",$txt_meeting_remarks)!="1. ")
			{
				if($db_type==2)
				{
					$rID_buyer_meeting=execute_query($data_arr_buyer_meeting);
					if($rID_buyer_meeting==1 && $flag==1) $flag=1; else $flag=0;
				}
				else
				{
					$rID_buyer_meeting=sql_insert("qc_meeting_mst", $field_arr_buyer_meeting, $data_arr_buyer_meeting,1);	
					if($rID_buyer_meeting==1 && $flag==1) $flag=1; else $flag=0;
				}
				//echo $rID_buyer_meeting;
				$rID_up=sql_update("qc_mst",$field_arr_mst_up,$data_arr_mst_up,"id","".$txt_update_id."",1);
				if($rID_up==1 && $flag==1) $flag=1; else $flag=0;
			}
			//echo "10**2222**".$txt_update_id; 
			//echo "insert into qc_meeting_mst (".$field_arr_buyer_meeting.") values ".$data_arr_buyer_meeting; die;
		}
		else
		{
			/* echo "10**".$meeting_update_id;
			//echo sql_update2("qc_mst",$field_arr_mst_up,$data_arr_mst_up,"id","".$txt_update_id."",1);
			echo sql_update2("qc_meeting_mst",$field_arr_buyer_meeting_up,$data_arr_buyer_meeting_up,"id","'".$meeting_update_id."'",1); 
		die;
			echo "insert into qc_meeting_mst (".$field_arr_buyer_meeting_up.") values ".$data_arr_buyer_meeting_up; die;*/
			if(str_replace("'","",$txt_meeting_no)!="")
			{
				$rID_buyer_meeting_up=sql_update("qc_meeting_mst",$field_arr_buyer_meeting_up,$data_arr_buyer_meeting_up,"id","'".$meeting_update_id."'",1);
				if($rID_buyer_meeting_up==1 && $flag==1) $flag=1; else $flag=0;
				
				$rID_upmst=sql_update("qc_mst",$field_arr_mst_up,$data_arr_mst_up,"id","".$txt_update_id."",1);
				if($rID_upmst==1 && $flag==1) $flag=1; else $flag=0;
			} 
		}
		
		//echo "10**".$flag."=".$rID."=".$rID_consRate."=".$rID_tot_cost."=".$rID_item_tot."=".$rID_buyer_meeting_up."=".$rID_upmst; die;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_update_id).'**'.str_replace("'",'',$txt_costSheetNo).'**'.str_replace("'",'',$cbo_revise_no).'**'.str_replace("'",'',$cbo_option_id).'**'.str_replace("'",'',$type).'**'.str_replace("'",'',$buyer_meeting_id).'**'.str_replace("'",'',$qcno);
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
				echo "1**".str_replace("'",'',$txt_update_id).'**'.str_replace("'",'',$txt_costSheetNo).'**'.str_replace("'",'',$cbo_revise_no).'**'.str_replace("'",'',$cbo_option_id).'**'.str_replace("'",'',$type).'**'.str_replace("'",'',$buyer_meeting_id).'**'.str_replace("'",'',$qcno);
			}
			else
			{
				oci_rollback($con); 
				echo "10**";
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;	
	}
	else if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$insert_user_id=return_field_value("inserted_by","qc_mst","id=".$txt_update_id."","inserted_by");
		$user_wise_msg="You have no right for delete. If you need delete, Please contract with MIS Department.";
		if($insert_user_id!=$user_id  && $_SESSION['logic_erp']["user_level"]!=2)
		{
			echo "11**".$user_wise_msg; 
			disconnect($con);die;
		}
		
		$msg_confirm="This option is already confirm. So You can't Change, Edit or Delete it.";
		$isconfirm_id=str_replace("'","",$hid_qc_no);
		if (is_duplicate_field( "cost_sheet_id", "qc_confirm_mst", "cost_sheet_id='$isconfirm_id' and status_active=1 and is_deleted=0") == 1)
		{
			echo "11**".$msg_confirm; 
			disconnect($con);
			die;
		}
		
		$joborbom=sql_select("select a.job_no from wo_po_details_master a, qc_mst b, wo_pre_cost_mst c where a.inquiry_id=b.inquery_id and b.qc_no=$hid_qc_no and a.id=c.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
		$jobNoinBom=$joborbom[0][csf('job_no')];
		if($jobNoinBom!="")
		{
			echo "13**".$jobNoinBom;
			disconnect($con);
			die;
		}
		//if (is_duplicate_field( "cost_sheet_no", "qc_mst", "style_ref='$dup_styleRef' and season_id='$dup_season_id' and buyer_id='$dup_buyer_id' and cost_sheet_no!=$txt_costSheetNo and status_active=1 and is_deleted=0") == 1)
		$sql_cost_sheet=sql_select("select id from qc_mst where cost_sheet_no=$txt_costSheetNo and status_active=1 and is_deleted=0");
		$flag=1;
		$field_arraymst="inquery_id*status_active*is_deleted*updated_by*update_date";
		$data_arraymst="'0'*'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//echo "11**"."Delete Restricted";
		
		$rID=sql_update("qc_mst",$field_arraymst,$data_arraymst,"id","".$txt_update_id."",1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID_fab=sql_update("qc_fabric_dtls",$field_array,$data_array,"mst_id","".$hid_qc_no."",1);
		if($rID_fab==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID_consRate=sql_update("qc_cons_rate_dtls",$field_array,$data_array,"mst_id","".$hid_qc_no."",1);
		if($rID_consRate==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID_tot_cost=sql_update("qc_tot_cost_summary",$field_array,$data_array,"mst_id","".$hid_qc_no."",1);
		if($rID_tot_cost==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID_item_tot=sql_update("qc_item_cost_summary",$field_array,$data_array,"mst_id","".$hid_qc_no."",1);
		if($rID_item_tot==1 && $flag==1) $flag=1; else $flag=0;
		
		//$rID_temp_style=sql_update("qc_style_temp_data",$field_array,$data_array,"qc_no","".$hid_qc_no."",1);
		if(count($sql_cost_sheet)==1)
		{
			$rID_temp_style=execute_query( "update qc_style_temp_data set status_active='0', is_deleted='1', updated_by='$user_id', update_date='$pc_date_time' where cost_sheet_no=$txt_costSheetNo and inserted_by=".$user_id."",0);
			if($rID_temp_style==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");  
				echo "2**";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con); 
				echo "2**";
			}
			else{
				oci_rollback($con); 
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	//exit();
}

if($action=="style_popup")
{
	echo load_html_head_contents("Style Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$ex_data=explode("__",$data);
	if($ex_data[0]!=0) $companyId=$ex_data[0]; else $companyId=$selected;
	$buyerId=$ex_data[1];
	$type=$ex_data[2];
	?>
    <script>
	var companyId='<?=$ex_data[0]; ?>';
	function set_all()
	{
		var old=document.getElementById('txt_select_row_id').value; 
		if(old!="")
		{   
			old=old.split(",");
			for(var k=0; k<old.length; k++)
			{   
				js_set_value( old[k] );
			} 
		}
	}
	
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
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array(); selected_cost_no = new Array();
	 
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
		var strcst=str[3];
		str=str[1];
	
		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str ); 
			selected_name.push( strdt );
			selected_cost_no.push( strcst );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i,1 );
			selected_cost_no.splice( i,1 );
		}
		var id = '';
		var ddd='';
		var cst_no='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			ddd += selected_name[i] + ',';
			cst_no += selected_cost_no[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		ddd = ddd.substr( 0, ddd.length - 1 );
		cst_no = cst_no.substr( 0, cst_no.length - 1 );
		//alert(id)
		$('#hide_style_id').val( id );
		$('#hide_style_no').val( ddd );
		$('#hide_cost_no').val( cst_no );
	} 
	
	function show_inner_filter(e)
	{
		if (e!=13) {var unicode=e.keyCode? e.keyCode : e.charCode } else {unicode=13;}
		if (unicode==13 )
		{
			fnc_listview();
		}
	}
	
	function fnc_listview()
	{
		show_list_view (document.getElementById('cbo_buyer_id').value+'**'+document.getElementById('cbo_season_id').value+'**'+document.getElementById('cbo_subDept_id').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_cost_sheet_no').value+'**'+document.getElementById('hide_dataType').value+'**'+document.getElementById('cbo_approval_type').value+'**'+document.getElementById('cbo_season_year').value+'**'+document.getElementById('cbo_brand').value+'**'+document.getElementById('cbo_company_id').value, 'style_ref_search_list_view', 'search_div', 'quick_costing_woven_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
		set_all();
	}
	 
	function fnc_select()
	{
		$(document).ready(function() {
			$("input:text").focus(function() { $(this).select(); } );
		});
	}
	
	function fnc_confirm_style(qcno,upid)
	{
		if(upid!="")
		{
			var data=qcno+'__'+upid;
			var page_link='quick_costing_woven_controller.php?action=confirmStyle_popup';
			var title="Confirm Style Popup";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&data='+data, title, 'width=1000px,height=350px,center=1,resize=0,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				fnc_listview();
			}
		}
		else
		{
			alert("Please Cost Sheet Save First.");
			return;
		}
	}
		  
	</script>
     <input type="hidden" name="hide_style_id" id="hide_style_id" value="" />
     <input type="hidden" name="hide_style_no" id="hide_style_no" value="" />
     <input type="hidden" name="hide_cost_no" id="hide_cost_no" value="" />
     <input type="hidden" name="hide_dataType" id="hide_dataType" value="<?php echo $type; ?>" />
    </head>
    <body>
    <div align="center">
        <form name="styleRefform_1" id="styleRefform_1">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                	<th width="130">Company</th>
                    <th width="130">Buyer</th>
                    <th width="130">Season</th>
                    <th width="60">Season Year</th>
                    <th width="130">Brand</th>
                    <th width="130">Department</th>
                    <th width="90">M.Style Ref/Name</th>
                    <th width="90">Cost Sheet No</th>
                    <th width="90">Approval Type</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('styleRefform_1','search_div','','','','');"></th> 					
                </thead>
                <tbody>
                	<tr class="general">
                    	<td><?=create_drop_down( "cbo_company_id", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "--Company--", $companyId,"load_drop_down( 'quick_costing_woven_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_td'); ",'' ); ?></td>
                        <td id="buyer_td"><?=create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name",1, "-- Select Buyer --",$buyerId,"load_drop_down( 'quick_costing_woven_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'quick_costing_woven_controller',this.value, 'load_drop_down_sub_dep', 'sub_td' ); load_drop_down( 'quick_costing_woven_controller', this.value, 'load_drop_down_brand', 'brand_td');",0 ); ?></td>   
                        <td id="season_td"><? echo create_drop_down( "cbo_season_id", 130, $blank_array,'', 1, "-- Select Season--",$selected, "" ); ?></td>
                        <td><? echo create_drop_down( "cbo_season_year", 60, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
                        <td id="brand_td"><? echo create_drop_down("cbo_brand", 130, $blank_array,"",1, "-Brand-", $selected,""); ?></td>
                        <td id="sub_td"><? echo create_drop_down( "cbo_subDept_id", 130, $blank_array,'', 1, "-- Select Dept--",$selected, "" ); ?></td>     
                        <td id="search_by_td"><input type="text" style="width:80px" class="text_boxes" name="txt_search_common" id="txt_search_common" onKeyUp="show_inner_filter(event);" /></td>
                        <td><input type="text" style="width:80px" class="text_boxes" name="txt_cost_sheet_no" id="txt_cost_sheet_no" onKeyUp="show_inner_filter(event);" /></td>
                        <td><? 
						$approvalType_arr = array(1 => "Un-Approved", 2 => "Approved");
						echo create_drop_down( "cbo_approval_type", 90, $approvalType_arr,'', 0, "All",$type, "" ); ?></td>	
                        <td><input type="button" name="button" class="formbutton" value="Show" onClick="fnc_listview();" style="width:80px;" /></td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
		$( "#txt_search_common" ).focus();
		fnc_select();
		if(companyId!=0) $('#cbo_company_id').attr('disabled',true); else $('#cbo_company_id').attr('disabled',false);
		if(companyId!=0) load_drop_down( 'quick_costing_woven_controller', companyId, 'load_drop_down_buyer_pop', 'buyer_td');
	</script>
    </html>
    <?
	exit(); 
}

if($action=="style_ref_search_list_view")
{
	extract($_REQUEST);
	$buyer_arr=return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");	
	$season_arr=return_library_array("select id, season_name from lib_buyer_season","id","season_name");
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand where status_active=1 and is_deleted=0",'id','brand_name');
	$department_arr=return_library_array("select id, sub_department_name from lib_pro_sub_deparatment","id","sub_department_name");
	$stage_arr=return_library_array("select tuid, stage_name from lib_stage_name","tuid","stage_name");
	$user_arr=return_library_array("select id, user_name from user_passwd","id","user_name");
	$color_arr=return_library_array("select id, color_name from lib_color","id","color_name");
	
	//echo $data;
	$ex_data=explode('**',$data);

	$buyer_id=str_replace("'","",$ex_data[0]);
	$season_id=str_replace("'","",$ex_data[1]);
	$department_id=str_replace("'","",$ex_data[2]);
	$style_ref=strtoupper(str_replace("'","",$ex_data[3]));
	$cost_sheet_no=str_replace("'","",$ex_data[4]);
	$type=str_replace("'","",$ex_data[5]);
	$approvaltype=str_replace("'","",$ex_data[6]);
	$seasonyear=str_replace("'","",$ex_data[7]);
	$brandid=str_replace("'","",$ex_data[8]);
	$companyid=str_replace("'","",$ex_data[9]);
	$unappreq="";
	
	if($type==1)
	{
		if($buyer_id==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond=" and a.buyer_id=$buyer_id";
		
		if($companyid!=0) $companyid_cond="and a.company_id=$companyid"; else $companyid_cond="";
		if($season_id!=0) $season_id_cond="and a.season_id=$season_id"; else $season_id_cond="";
		if($department_id!=0) $department_id_cond="and a.department_id=$department_id"; else $department_id_cond="";
		if($seasonyear!=0) $seasonYearCond="and a.season_year=$seasonyear"; else $seasonYearCond="";
		if($brandid!=0) $brandCond="and a.brand_id=$brandid"; else $brandCond="";
		
		if($style_ref!='') $style_ref_cond=" and a.style_ref like '%$style_ref%'"; else $style_ref_cond="";
		if($cost_sheet_no!='') $cost_sheet_no_cond=" and a.cost_sheet_no like '%$cost_sheet_no%'"; else $cost_sheet_no_cond="";
		if($approvaltype==2)
		{
			$approvaltype_cond="and a.approved in (1,3)";
			$unappreq="and c.approval_cause is not null";
		}
		else if($approvaltype==1)
		{
			$approvaltype_cond="and a.approved in (0,2)";
			$unappreq="";
		}
		else $approvaltype_cond="";
	}
	else
	{
		if($buyer_id==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond=" and buyer_id=$buyer_id";
		//if($buyer_id!=0) $buyer_id_cond="and buyer_id=$buyer_id"; else $buyer_id_cond="";
		if($season_id!=0) $season_id_cond="and season_id=$season_id"; else $season_id_cond="";
		if($department_id!=0) $department_id_cond="and department_id=$department_id"; else $department_id_cond="";
		if($seasonyear!=0) $seasonYearCond="and season_year=$seasonyear"; else $seasonYearCond="";
		if($brandid!=0) $brandCond="and brand_id=$brandid"; else $brandCond="";
		if($style_ref!='') $style_ref_cond=" and style_ref like '%$style_ref%'"; else $style_ref_cond="";
		if($cost_sheet_no!='') $cost_sheet_no_cond=" and cost_sheet_no like '%$cost_sheet_no%'"; else $cost_sheet_no_cond="";
		
		if($approvaltype==2)
		{
			$approvaltype_cond="and approved in (1,3)";
			$unappreq="and c.approval_cause is not null";
		}
		else if($approvaltype==1) 
		{
			$approvaltype_cond="and approved in (0,2)";
			$unappreq="";
		}
		else $approvaltype_cond="";
	}
	
	$lib_temp_arr=return_library_array("select id, item_name from lib_qc_template","id","item_name");
	if($db_type==0) $concat_cond="group_concat(lib_item_id)";
	else if($db_type==2) $concat_cond="listagg(cast(lib_item_id as varchar2(4000)),',') within group (order by lib_item_id)";
	else $concat_cond="";
	$sql_tmp="select tuid, $concat_cond as lib_item_id from qc_template where status_active=1 and is_deleted=0 group by tuid order by tuid ASC";
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
		$template_name_arr[$row[csf('tuid')]]=$lib_temp_id;
	}
	//print_r($template_name_arr[19]);
	?>
    <div>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1345" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="100">Buyer Name</th>
            <th width="70">Season</th>.
            <th width="60">Season Year</th>
            <th width="70">Brand</th>
            <th width="80">Body/Wash Color</th>
            <th width="70">Depar tment</th>
            <th width="100">M.Style Ref/Name</th>
            <th width="120">Template</th>
            <th width="60">FOB ($)</th>
            <th width="70">Stage</th>
            <th width="40">Conf irm</th>
            <th width="65">Cost Sheet No</th>
            <th width="60">Costing Date</th>
            <?
			if($type==1)
			{
				?>
                <th width="20">Op.</th>
                <th width="20">Rv.</th>
                <?
			}
			?>
            <th width="70">Insert By</th>
            <th>Update By</th>
            <? if($approvaltype==2) 
			{
				?>
            	<th width="120">Un-approved Request</th>
             	<?
			}
			?>
        </thead>
    </table>
    <div style="width:1345px; overflow-y:scroll; max-height:260px;" align="center">
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1325" class="rpt_table" id="tbl_list_search" >
    <?
	/*$sql_request="select booking_id as qc_no, approval_cause from fabric_booking_approval_cause where entry_form=28 and approval_type=2 and status_active=1 and is_deleted=0";
	$unapp_request_arr=array();
	$nameArray_request=sql_select($sql_request);
	foreach($nameArray_request as $row)
	{
		$unapp_request_arr[$row[csf("qc_no")]]=$row[csf("approval_cause")];
	}
	unset($nameArray_request);*/
	
	$style_temp_arr=array();
	$sql_style_temp=sql_select("select qc_no, cost_sheet_no from qc_style_temp_data where inserted_by='$user_id' and status_active=1 and is_deleted=0");
	foreach($sql_style_temp as $qcrow)
	{
		$style_temp_arr[$qcrow[csf("qc_no")]]=$qcrow[csf("qc_no")];
	}
	unset($sql_style_temp);
	
	$cost_summ_arr=array();
	$sql_tot_summ=sql_select("select mst_id, tot_fob_cost, is_confirm from qc_tot_cost_summary where status_active=1 and is_deleted=0");
	foreach($sql_tot_summ as $row)
	{
		$cost_summ_arr[$row[csf("mst_id")]]['fob']=$row[csf("tot_fob_cost")];
		$cost_summ_arr[$row[csf("mst_id")]]['is_confirm']=$row[csf("is_confirm")];
	}
	unset($sql_tot_summ);
	if($type==1)
	{
		if($approvaltype==2)
		{
			$sql_style = "select a.id, a.cost_sheet_no, a.costing_date, a.buyer_id, a.season_id, a.season_year, a.brand_id, a.body_color_id, a.style_ref, a.department_id, a.temp_id, a.lib_item_id, a.stage_id, a.inserted_by, a.updated_by, a.qc_no, a.revise_no, a.option_id, a.approved, c.approval_cause from qc_mst a, qc_confirm_mst b, fabric_booking_approval_cause c where a.qc_no=b.cost_sheet_id and c.booking_id=a.qc_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=28 and c.approval_type=2 and c.status_active=1 and c.is_deleted=0 and a.entry_form=430
			$buyer_id_cond $style_ref_cond $cost_sheet_no_cond $season_id_cond $department_id_cond $approvaltype_cond $unappreq $seasonYearCond $brandCond $companyid_cond
			group by a.id, a.cost_sheet_no, a.costing_date, a.buyer_id, a.season_id, a.season_year, a.brand_id, a.body_color_id, a.style_ref, a.department_id, a.temp_id, a.lib_item_id, a.stage_id, a.inserted_by, a.updated_by, a.qc_no, a.revise_no, a.option_id, a.approved, c.approval_cause order by a.id Desc";
		}
		else
		{
			$sql_style = "select a.id, a.cost_sheet_no, a.costing_date, a.buyer_id, a.season_id, a.season_year, a.brand_id, a.body_color_id, a.style_ref, a.department_id, a.temp_id, a.lib_item_id, a.stage_id, a.inserted_by, a.updated_by, a.qc_no, a.revise_no, a.option_id, a.approved from qc_mst a, qc_confirm_mst b where a.qc_no=b.cost_sheet_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=430
			$buyer_id_cond $style_ref_cond $cost_sheet_no_cond $season_id_cond $department_id_cond $approvaltype_cond $seasonYearCond $brandCond $companyid_cond
			group by a.id, a.cost_sheet_no, a.costing_date, a.buyer_id, a.season_id, a.season_year, a.brand_id, a.body_color_id, a.style_ref, a.department_id, a.temp_id, a.lib_item_id, a.stage_id, a.inserted_by, a.updated_by, a.qc_no, a.revise_no, a.option_id, a.approved order by a.id Desc";
		}
	}
	else
	{
		$sql_style = "select min(id) as id, cost_sheet_no, max(costing_date) as costing_date, buyer_id, season_id, season_year, brand_id, body_color_id, style_ref, department_id, temp_id, lib_item_id, stage_id, max(inserted_by) as inserted_by, max(updated_by) as updated_by, approved, min(qc_no) as qc_no from qc_mst where status_active=1 and is_deleted=0 and entry_form=430 $buyer_id_cond $style_ref_cond $cost_sheet_no_cond $season_id_cond $department_id_cond $approvaltype_cond $seasonYearCond $brandCond $companyid_cond group by cost_sheet_no, buyer_id, season_id, season_year, brand_id, body_color_id, style_ref, department_id, temp_id, lib_item_id, stage_id, approved order by id Desc";
	}//and a.qc_no not in ( select c.qc_no from qc_style_temp_data c where inserted_by='$user_id' ) 
	//echo $sql_style;
	$style_temp_id='';
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
		$row_select=''; $unapp_stlink=""; $unapp_endlink=""; $jsFunction="";
		if($type==1)
		{
			$style_temp_id='';
			$unapp_link='<a href="#report_details" onClick="fnc_confirm_style('.$row[csf('qc_no')].','.$row[csf('id')].');">';
			$unapp_endlink='</a>';
			$jsFunction="";
		}
		else
		{
			$row_select=$i.'_'.$row[csf("qc_no")].'_'.$row[csf("style_ref")].'_'.$row[csf("cost_sheet_no")];
			if(in_array($row[csf("qc_no")],$style_temp_arr)) 
			{ 
				if($style_temp_id=="") $style_temp_id=$row_select; else $style_temp_id.=",".$row_select;
			}
			$unapp_link="";
			$unapp_endlink="";
			$jsFunction="js_set_value('".$row_select."')";
		}
		$apptdcolor="";
		if($row[csf("approved")]==1 || $row[csf("approved")]==3 ) $apptdcolor="#99FFCC";
		
		$confirm='';
		if($cost_summ_arr[$row[csf("qc_no")]]['is_confirm']==1) $confirm='Yes'; else $confirm='No';
		?>
        <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<?=$i; ?>" onClick="<?=$jsFunction; ?>"> 
            <td width="30" align="center"><?=$i; ?></td>
            <td width="100" style="word-break:break-all"><?=$buyer_arr[$row[csf("buyer_id")]]; ?></td>
            <td width="70" style="word-break:break-all"><?=$season_arr[$row[csf("season_id")]]; ?></td>
            
            <td width="60" style="word-break:break-all"><?=$row[csf("season_year")]; ?></td>
            <td width="70" style="word-break:break-all"><?=$brand_arr[$row[csf("brand_id")]]; ?></td>
            <td width="80" style="word-break:break-all"><?=$color_arr[$row[csf("body_color_id")]]; ?></td>
            
            <td width="70" style="word-break:break-all"><?=$department_arr[$row[csf("department_id")]]; ?>&nbsp;</td>
            <td width="100" style="word-break:break-all" bgcolor="<?=$apptdcolor; ?>"><?=$unapp_link; ?> <?=$row[csf("style_ref")]; ?><?=$unapp_endlink; ?></td>
            <td width="120" style="word-break:break-all"><?=$template_name; ?></td>
            <td width="60" align="right"><?=number_format($cost_summ_arr[$row[csf("qc_no")]]['fob'],4); ?>&nbsp;</td>
            <td width="70" style="word-break:break-all"><?=$stage_arr[$row[csf("stage_id")]]; ?>&nbsp;</td>	
            <td width="40"><?=$confirm; ?>&nbsp;</td>
            <td width="65" style="word-break:break-all" bgcolor="<?=$apptdcolor; ?>"><?=$row[csf("cost_sheet_no")]; ?></td>
            <td width="60" style="word-break:break-all"><?=change_date_format($row[csf("costing_date")]); ?></td>
            <?
			if($type==1)
			{
				?>
                <td width="20" align="center"><?=$row[csf("option_id")]; ?></td>
                <td width="20" align="center"><?=$row[csf("revise_no")]; ?></td>
                <?
			}
			?>
            <td width="70" style="word-break:break-all"><?=$user_arr[$row[csf("inserted_by")]]; ?></td>
            <td style="word-break:break-all"><?=$user_arr[$row[csf("updated_by")]]; ?></td>
            <? if($approvaltype==2) 
			{
				?>
                <td width="100" style="word-break:break-all"><?=$row[csf("approval_cause")]; ?></td>
                <?
			}
			?>
        </tr>
		<?
		$i++;
	}
	?>
    </table>
        
    </div>
    </div>
    <? if($type!=1) { ?>
    <div class="check_all_container"><div style="width:100%"> 
        <div style="width:50%; float:left" align="left">
        	<input type="hidden" name="txt_select_row_id" id="txt_select_row_id" value="<?=$style_temp_id; ?>"/>
            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
        </div>
        <div style="width:50%; float:left" align="left">
        <input type="button" name="close" id="close"  onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
        </div>
    </div>
    <? } ?>
    </div>
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
		
		$sql_style= "select min(id) as id, cost_sheet_no, style_ref, min(qc_no) as qc_no, season_id, inserted_by from qc_mst where entry_form=430 and cost_sheet_no in ($ex_data[0]) and cost_sheet_no not in (select cost_sheet_no from qc_style_temp_data where cost_sheet_no in ($ex_data[0]) and inserted_by='$user_id' and status_active=1 and is_deleted=0 ) and revise_no=0 and option_id=0 and status_active=1 and is_deleted=0 group by cost_sheet_no, style_ref, season_id, inserted_by";
		//echo  $sql_style;
		$sql_data_arr=sql_select($sql_style);
		
		$field_array="id, mst_id, cost_sheet_no, style_user, qc_no, inserted_by, insert_date";
		$qc_style_list_id=return_next_id("id", "qc_style_temp_data", 1);
		$add_comma=0; $data_array="";
		foreach($sql_data_arr as $row)
		{ 
			$mst_id=$row[csf('id')];
			$cost_sheet_no=$row[csf('cost_sheet_no')];
			$style_user=$row[csf('inserted_by')];
			$qc_no=$row[csf('qc_no')];
			if ($add_comma!=0) $data_array .=",";
			$data_array .="(".$qc_style_list_id.",'".$mst_id."','".$cost_sheet_no."','".$style_user."','".$qc_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
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
		$rID=execute_query( "delete from qc_style_temp_data where inserted_by ='$user_id' and status_active=1 and is_deleted=0",0);
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
		$rID=execute_query( "delete from qc_style_temp_data where inserted_by='$user_id' and qc_no='$ex_data[0]' and status_active=1 and is_deleted=0",0);
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
	$colorArr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$sql_temp_style=sql_select("select a.id, a.cost_sheet_no, a.style_ref, a.body_color_id, a.season_id, a.inserted_by, a.updated_by, a.qc_no, a.approved from qc_mst a, qc_style_temp_data b where a.id=b.mst_id and b.inserted_by='$user_id' and a.entry_form=430 and b.status_active=1 and b.is_deleted=0 order by a.style_ref ASC");
	
	$i=1;
	foreach($sql_temp_style as $row)
	{  
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		if($ex_data[0]==$row[csf('cost_sheet_no')]) $bgcolor="#33CC00"; else $bgcolor;
		$user_name="";
		if($row[csf('updated_by')]==0 || $row[csf('updated_by')]=="") $user_name=$user_arr[$row[csf('inserted_by')]]; else $user_name=$user_arr[$row[csf('updated_by')]];
		$apptdcolor="";
		if($row[csf("approved")]==1 || $row[csf("approved")]==3 ) $apptdcolor="#99FFCC";
		
		?>
        <tr id="tr_<?=$row[csf('qc_no')]; ?>" bgcolor="<?=$bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="change_color_tr('<?=$row[csf('qc_no')]; ?>','<?=$bgcolor; ?>'); set_onclick_style_list('<?=$row[csf('qc_no')].'__'.$row[csf('inserted_by')].'__'.$row[csf('cost_sheet_no')].'__25'; ?>')">
        	<td width="90" style="word-break:break-all" bgcolor="<?=$apptdcolor; ?>"><?=$row[csf('style_ref')]; ?>&nbsp;</td>
            <td width="70" style="word-break:break-all"><?=$season_arr[$row[csf('season_id')]]; ?>&nbsp;</td>
            <td width="80" style="word-break:break-all"><?=$colorArr[$row[csf('body_color_id')]]; ?>&nbsp;</td>
            <td style="word-break:break-all"><?=$user_name; ?>&nbsp;</td>
        <?
		$i++;
	}
	//if($ex_data[1]==1) echo "change_color_tr('".$i."','".$bgcolor."');\n"; 
	unset($sql_temp_style);
	exit();
}

if($action=="populate_style_details_data")
{
	//echo $data;
	$color_arr=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name"); 
	$supplierArr=return_library_array("select id, supplier_name from  lib_supplier", "id", "supplier_name");
	$max_meeting=return_field_value("max(meeting_no) as max_meeting_no","qc_meeting_mst","is_deleted=0 and status_active=1","max_meeting_no");
	if($max_meeting=="") $max_meeting=0;
	
	$lib_temp_arr=return_library_array("select id, item_name from lib_qc_template","id","item_name");
	if($db_type==0) $concat_cond="group_concat(lib_item_id)";
	else if($db_type==2) $concat_cond="listagg(cast(lib_item_id as varchar2(4000)),',') within group (order by lib_item_id)";
	else $concat_cond="";
	$sql_tmp="select tuid, temp_id, $concat_cond as lib_item_id from qc_template where status_active=1 and is_deleted=0 group by tuid, temp_id order by temp_id ASC";
	$sql_tmp_res=sql_select($sql_tmp);
	
	$inqueryNoArr=return_library_array("select id, system_number_prefix_num from wo_quotation_inquery","id","system_number_prefix_num");
	$itemGroupArr=array(); $itemGroupCalParaArr=array();
	$itemGroupSql=sql_select("select id, item_name, cal_parameter from lib_item_group where status_active=1 and is_deleted=0 and item_category=4");
	foreach($itemGroupSql as $row)
	{
		$itemGroupArr[$row[csf('id')]]=strtoupper($row[csf('item_name')]);
		$itemGroupCalParaArr[$row[csf('id')]]=strtoupper($row[csf('cal_parameter')]);
	}
	unset($itemGroupSql);
	
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
		$template_name_arr[$row[csf('tuid')]]=$lib_temp_id;
	}
	unset($sql_tmp_res);
	
	$buyer_remark_arr=array(); $pre_mee_arr=array();
	if($db_type==0) $meeting_time="TIME_FORMAT(meeting_time, '%H:%i')";
	else if ($db_type==2) $meeting_time="TO_CHAR(meeting_time,'HH24:MI')";
	
	$sql_buyer_remark="select id, mst_id, meeting_no, buyer_agent_id, location_id, meeting_date, $meeting_time as meeting_time, remarks from qc_meeting_mst order by id ASC";
	//echo $sql_buyer_remark;
	$sql_buyer_remark_res=sql_select($sql_buyer_remark);
	//print_r($sql_tmp_res);die;
	foreach($sql_buyer_remark_res as $row)
	{
		$meeting_remarks = str_replace("\n", "\\n", $row[csf("remarks")]);
		if($meeting_remarks=="") $meeting_remarks="1. "; 
		$buyer_remark_arr[$row[csf('id')]]['date']=$row[csf('meeting_date')];
		$buyer_remark_arr[$row[csf('id')]]['time']=$row[csf('meeting_time')];
		$buyer_remark_arr[$row[csf('id')]]['remarks']=$meeting_remarks;
		$buyer_remark_arr[$row[csf('id')]]['meeting_no']=$row[csf('meeting_no')];
		$buyer_remark_arr[$row[csf('id')]]['agent_id']=$row[csf('buyer_agent_id')];
		$buyer_remark_arr[$row[csf('id')]]['location_id']=$row[csf('location_id')];
		$buyer_remark_arr[$row[csf('id')]]['id']=$row[csf('id')];
		
		$pre_mee_arr[$row[csf('meeting_no')]]['agent_id']=$row[csf('buyer_agent_id')];
		$pre_mee_arr[$row[csf('meeting_no')]]['location_id']=$row[csf('location_id')];
	} 
	unset($sql_buyer_remark_res);
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
		
	$chk_new_meeting=$ex_other_val[2];
	
	
	//if ($chk_new_meeting==1) $max_meeting=$max_meeting+1;
	//echo "select id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, style_ref, buyer_id, cons_basis, season_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, revise_no, option_id, buyer_remarks, option_remarks, inserted_by, meeting_no, qc_no from qc_mst where status_active=1 and is_deleted=0 and qc_no='$cost_sheet_id'"; die;
	$data_array=sql_select("select id, company_id, location_id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, inquery_id, style_ref, buyer_id, uom, cons_basis, season_id, season_year, brand_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, revise_no, option_id, buyer_remarks, option_remarks, body_color_id, inserted_by, meeting_no, qc_no, commercial_cost_method from qc_mst where status_active=1 and is_deleted=0 and cost_sheet_no='$cost_sheet_no' $rev_no_cond $option_id_cond");//$starting_row_cond and inserted_by='$cond_user'  qc_no='$cost_sheet_id'   
	$cond_data=$data_array[0][csf('qc_no')];
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
		echo "$('#cbo_company_id').val('".$row[csf("company_id")]."');\n";
		echo "load_drop_down('requires/quick_costing_woven_controller', '".trim($row[csf("company_id")])."', 'load_drop_down_location', 'location_td'); \n";
		echo "$('#cbo_location_id').val('".$row[csf("location_id")]."');\n";
		echo "$('#cbo_temp_id').val('".$row[csf("temp_id")]."');\n";
		echo "$('#txt_temp_id').val('".trim($row[csf("lib_item_id")])."');\n";
		echo "load_drop_down('requires/quick_costing_woven_controller', '".trim($row[csf("lib_item_id")])."', 'load_drop_down_tempItem', 'item_td'); \n";
		echo "fnc_item_list('".trim($row[csf("lib_item_id")])."__".$template_name."');\n"; 
		echo "fnc_summary_dtls('".trim($row[csf("lib_item_id")])."__".$template_name."');\n"; 
		//echo "fnc_dtls_ganerate('".$row[csf("temp_id")].'__'.$template_name."');\n"; 
		echo "navigate_arrow_key();\n";
		
		
		echo "$('#txt_inquery_id').val('".$row[csf("inquery_id")]."');\n";
		echo "$('#txt_inquiry_no').val('".$inqueryNoArr[$row[csf("inquery_id")]]."');\n";
		echo "$('#txt_styleRef').val('".$row[csf("style_ref")]."');\n"; 
		echo "$('#txt_update_id').val('".$row[csf("id")]."');\n";
		//echo "$('#txt_costSheet_id').val('".$row[csf("cost_sheet_id")]."');\n";
		echo "$('#txt_costSheetNo').val('".$row[csf("cost_sheet_no")]."');\n";
		echo "$('#cbo_buyer_id').val('".$row[csf("buyer_id")]."');\n";
		echo "$('#cbouom').val('".$row[csf("uom")]."');\n";
		echo "$('#cbo_cons_basis_id').val('".$row[csf("cons_basis")]."');\n";
		//echo "fnc_consumption_write_disable('".$row[csf("cons_basis")]."');\n"; 
		echo "load_drop_down('requires/quick_costing_woven_controller', '".$row[csf("buyer_id")]."', 'load_drop_down_season', 'season_td');\n"; 
		echo "load_drop_down('requires/quick_costing_woven_controller', '".$row[csf("buyer_id")]."', 'load_drop_down_brand', 'brand_td');\n";
		echo "load_drop_down('requires/quick_costing_woven_controller', '".$row[csf("buyer_id")]."', 'load_drop_down_sub_dep', 'sub_td');\n";
		
		echo "$('#cbo_season_id').val('".$row[csf("season_id")]."');\n";
		echo "$('#cbo_season_year').val('".$row[csf("season_year")]."');\n";
		echo "$('#cbo_brand').val('".$row[csf("brand_id")]."');\n";
		
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
		echo "$('#txt_costing_remarks').attr('pre_costing_remarks','".$buyer_remarks."');\n"; 
		echo "$('#txt_option_remarks').val('".$row[csf("option_remarks")]."');\n";
		echo "$('#txt_option_remarks').attr('pre_opt_remarks','".$row[csf("option_remarks")]."');\n";
		echo "$('#txt_bodywashcolor').val('".$color_arr[$row[csf("body_color_id")]]."');\n";
		echo "$('#txt_commercial_cost_method').val('".$row[csf("commercial_cost_method")]."');\n"; 
		echo "$('#hid_qc_no').val('".$row[csf("qc_no")]."');\n"; 
		echo "$('#txt_isUpdate').val(1);\n"; 
		echo "disable_enable_fields('cbo_temp_id*cbouom*cbo_cons_basis_id',1);\n";
		if($row[csf("inquery_id")]>0)
		{
			echo "disable_enable_fields('cbo_buyer_id*cbo_brand*cbo_season_year*cbo_season_id*txt_bodywashcolor*txt_styleDesc',1);\n";
		}
		if($ex_data[4]!="from_option")
		{
			//echo "load_drop_down('requires/quick_costing_woven_controller', '".$row[csf("cost_sheet_no")].'__'.$row[csf("option_id")].'__'.$row[csf("revise_no")]."', 'load_drop_down_revise_no', 'revise_td');\n"; //.'__'.$row[csf("revise_no")]
			//echo "load_drop_down('requires/quick_costing_woven_controller', '".$row[csf("cost_sheet_no")].'__'.$row[csf("option_id")]."', 'load_drop_down_option_id', 'option_td');\n"; 
		}
		//echo "$('#cbo_option_id').val('".$row[csf("option_id")]."');\n";
		if($count_temp==1) $set_pcs='Pcs';
		else if($count_temp>1) $set_pcs='Set';
		else $set_pcs='';
		
		echo "$('#uom_td').text('".$set_pcs."');\n";
		if($chk_new_meeting==2)
		{
			if($max_meeting==$buyer_remark_arr[$row[csf('meeting_no')]]['meeting_no'])
			{
				echo "$('#txt_meeting_no').val('".$buyer_remark_arr[$row[csf('meeting_no')]]['meeting_no']."');\n";
				//echo "$('#cbo_buyer_agent').val('".$buyer_remark_arr[$row[csf('meeting_no')]]['agent_id']."');\n";
				//echo "$('#cbo_agent_location').val('".$buyer_remark_arr[$row[csf('meeting_no')]]['location_id']."');\n";
				echo "$('#txt_meeting_date').val('".change_date_format($buyer_remark_arr[$row[csf('meeting_no')]]['date'])."');\n";
				echo "$('#txt_meeting_time').val('".$buyer_remark_arr[$row[csf('meeting_no')]]['time']."');\n";
				echo "$('#txt_meeting_remarks').val('".$buyer_remark_arr[$row[csf('meeting_no')]]['remarks']."');\n";
				echo "$('#txt_meeting_remarks').attr('pre_meeting_remark','".$buyer_remark_arr[$row[csf('meeting_no')]]['remarks']."');\n";
			}
			else
			{
				echo "$('#txt_meeting_no').val('".$max_meeting."');\n";
				//echo "$('#cbo_buyer_agent').val('".$buyer_remark_arr[$max_meeting]['agent_id']."');\n";
				//echo "$('#cbo_agent_location').val('".$buyer_remark_arr[$max_meeting]['location_id']."');\n";
				echo "$('#txt_meeting_date').val('".date('d-m-Y')."');\n";
				echo "$('#txt_meeting_time').val('".date('H:i', time())."');\n";
				echo "$('#txt_meeting_remarks').attr('pre_meeting_remark','1. ');\n";
				echo "$('#txt_meeting_remarks').val('1. ');\n";
			}
		}
		else if($chk_new_meeting==1)
		{
			//echo "$('#txt_meeting_no').val('".($max_meeting+1)."');\n";
			//echo "$('#cbo_buyer_agent').val('".$buyer_remark_arr[$max_meeting]['agent_id']."');\n";
			//echo "$('#cbo_agent_location').val('".$buyer_remark_arr[$max_meeting]['location_id']."');\n";
			echo "$('#txt_meeting_date').val('".date('d-m-Y')."');\n";
			echo "$('#txt_meeting_time').val('".date('H:i', time())."');\n";
			if($max_meeting==$buyer_remark_arr[$row[csf('meeting_no')]]['meeting_no'])
			{
				echo "$('#txt_meeting_remarks').val('".$buyer_remark_arr[$row[csf('meeting_no')]]['remarks']."');\n";
				echo "$('#txt_meeting_remarks').attr('pre_meeting_remark','".$buyer_remark_arr[$row[csf('meeting_no')]]['remarks']."');\n";
			}
			else
			{
				echo "$('#txt_meeting_remarks').attr('pre_meeting_remark','1. ');\n";
				echo "$('#txt_meeting_remarks').val('1. ');\n";
			}
		}
		//echo "$('#txt_meeting_no').val('".$buyer_remark_arr[$row[csf('meeting_no')]]['id']."');\n";
	}
	unset($data_array);
	
	$sql_cons_rate="select id, mst_id, item_id, type, particular_type_id, consumption, ex_percent, tot_cons, uom, rate, value, fab_id, description, use_for, fc_per, body_part_id, supp_ref, rate_data, nominated_supp_multi from qc_cons_rate_dtls where mst_id=$cond_data and status_active=1 and is_deleted=0 order by id asc";
	$sql_result_cons_rate=sql_select($sql_cons_rate);
	$cons_rate_fab_arr=array(); $cons_rate_sp_arr=array(); $cons_rate_wash_arr=array(); $cons_rate_ac_arr=array(); $itemArray=array();
	$yarn_count_determin_id_arr=array();
	foreach ($sql_result_cons_rate as $rowConsRate)
	{
		array_push($yarn_count_determin_id_arr, $rowConsRate[csf("fab_id")]);
	}
	$determina_cond=where_con_using_array($yarn_count_determin_id_arr,0,"id");
	$yarn_count_determin_arr=return_library_array("select id, shrinkage_l || ',' || shrinkage_w as shrinkage from lib_yarn_count_determina_mst where status_active=1 and is_deleted=0 $determina_cond","id","shrinkage");
	foreach ($sql_result_cons_rate as $rowConsRate)
	{
		$nominated_supp_multi="";
		if($rowConsRate[csf("nominated_supp_multi")]!="")
		{
			$exsupp=explode(",",$rowConsRate[csf("nominated_supp_multi")]);
			foreach($exsupp as $supid)
			{
				if($nominated_supp_multi=="") $nominated_supp_multi=$supplierArr[$supid]; else $nominated_supp_multi.=','.$supplierArr[$supid];
			}
		}
		if($rowConsRate[csf("type")]==1)
		{
			$cons_rate_fab_arr[$rowConsRate[csf("item_id")]].=$body_part[$rowConsRate[csf("body_part_id")]].'_'.$rowConsRate[csf("body_part_id")].'_'.$rowConsRate[csf("description")].'_'.$rowConsRate[csf("fab_id")].'_'.$rowConsRate[csf("use_for")].'_'.$rowConsRate[csf("consumption")].'_'.$rowConsRate[csf("ex_percent")].'_'.$rowConsRate[csf("tot_cons")].'_'.$rowConsRate[csf("rate")].'_'.$rowConsRate[csf("fc_per")].'_'.$rowConsRate[csf("value")].'_'.$yarn_count_determin_arr[$rowConsRate[csf("fab_id")]].'_'.$rowConsRate[csf("nominated_supp_multi")].'_'.$nominated_supp_multi.'##';
		}
		if($rowConsRate[csf("type")]==2)
		{
			$cons_rate_sp_arr[$rowConsRate[csf("item_id")]].=$rowConsRate[csf("particular_type_id")].'_'.$rowConsRate[csf("fab_id")].'_'.$body_part[$rowConsRate[csf("body_part_id")]].'_'.$rowConsRate[csf("body_part_id")].'_'.$rowConsRate[csf("consumption")].'_'.$rowConsRate[csf("ex_percent")].'_'.$rowConsRate[csf("tot_cons")].'_'.$rowConsRate[csf("rate")].'_'.$rowConsRate[csf("value")].'_'.$yarn_count_determin_arr[$rowConsRate[csf("fab_id")]].'_'.$rowConsRate[csf("nominated_supp_multi")].'_'.$nominated_supp_multi.'##';
		}
		if($rowConsRate[csf("type")]==3)
		{
			$cons_rate_wash_arr[$rowConsRate[csf("item_id")]].=$emblishment_wash_type[$rowConsRate[csf("particular_type_id")]].'_'.$rowConsRate[csf("particular_type_id")].'_'.$body_part[$rowConsRate[csf("body_part_id")]].'_'.$rowConsRate[csf("body_part_id")].'_'.$rowConsRate[csf("consumption")].'_'.$rowConsRate[csf("ex_percent")].'_'.$rowConsRate[csf("tot_cons")].'_'.$rowConsRate[csf("rate")].'_'.$rowConsRate[csf("value")].'_'.$yarn_count_determin_arr[$rowConsRate[csf("fab_id")]].'_'.$rowConsRate[csf("nominated_supp_multi")].'_'.$nominated_supp_multi.'##';
		}
		if($rowConsRate[csf("type")]==4)
		{
			$cons_rate_ac_arr[$rowConsRate[csf("item_id")]].=$itemGroupArr[$rowConsRate[csf("particular_type_id")]].'_'.$rowConsRate[csf("particular_type_id")].'_'.$rowConsRate[csf("description")].'_'.$rowConsRate[csf("supp_ref")].'_'.$rowConsRate[csf("consumption")].'_'.$rowConsRate[csf("ex_percent")].'_'.$rowConsRate[csf("tot_cons")].'_'.$rowConsRate[csf("rate")].'_'.$rowConsRate[csf("value")].'_'.$rowConsRate[csf("uom")].'_'.$itemGroupCalParaArr[$rowConsRate[csf("particular_type_id")]].'_'.$rowConsRate[csf("rate_data")].'_0_'.$rowConsRate[csf("nominated_supp_multi")].'_'.$nominated_supp_multi.'##';
			
			//accessoriesData+= ($('#txtAcctext_'+q).val())+'_'+($('#txtAccId_'+q).val()*1)+'_'+($('#txtAccDescription_'+q).val())+'_'+($('#txtAccBandRef_'+q).val())+'_'+($('#txtaccConsumtion_'+q).val()*1)+'_'+($('#txtacexper_'+q).val()*1)+'_'+($('#txttotAccConsumtion_'+q).val()*1)+'_'+($('#txtacRate_'+q).val()*1)+'_'+($('#txtacValue_'+q).val()*1)+'_'+($('#cboconsuom_'+q).val()*1)+'_'+($('#hiddencalparameter_'+q).val()*1)+'_'+$('#txtAccConsCalData_'+q).val()+'_'+($('#txtaccsupplierid_'+q).val()*1)+'##';
		}
		$itemArray[$rowConsRate[csf("item_id")]]=$rowConsRate[csf("item_id")];
	}
	unset($sql_result_cons_rate);
	$active_item=1;
	foreach($itemArray as $item_id)
	{
		echo "$('#txtfabricData_$item_id').val('".$cons_rate_fab_arr[$item_id]."');\n";
		echo "$('#txtspData_$item_id').val('".$cons_rate_sp_arr[$item_id]."');\n";
		echo "$('#txtwashData_$item_id').val('".$cons_rate_wash_arr[$item_id]."');\n";
		echo "$('#txtaccData_$item_id').val('".$cons_rate_ac_arr[$item_id]."');\n";
		if($active_item==1)
		{
			echo "fnc_dtls_ganerate('".$item_id."__".$lib_temp_arr[$item_id]."');\n";
			$active_item++;
		}
	}
	
	$sql_summ="select buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_wash_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_courier_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, tot_rmg_ratio, commercial_per, commercial_cost, inspection_cost, lc_per, lc_cost from qc_tot_cost_summary where mst_id='$cond_data' and status_active=1 and is_deleted=0";
	$sql_result_summ=sql_select($sql_summ);
	
	foreach($sql_result_summ as $rowSumm)
	{
		//echo "$('#cbo_buyer_agent').val('".$rowSumm[csf("buyer_agent_id")]."');\n";
		//echo "$('#cbo_agent_location').val('".$rowSumm[csf("location_id")]."');\n";
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
		echo "$('#txt_lcPer').val('".$rowSumm[csf("lc_per")]."');\n";
		echo "$('#txt_commlPer').val('".$rowSumm[csf("commercial_per")]."');\n";
		echo "$('#txt_commPer').val('".$rowSumm[csf("commision_per")]."');\n";
		echo "$('#totFab_td').text('".$rowSumm[csf("tot_fab_cost")]."');\n";
		echo "$('#totSpc_td').text('".$rowSumm[csf("tot_sp_operation_cost")]."');\n";
		echo "$('#totWash_td').text('".$rowSumm[csf("tot_wash_cost")]."');\n";
		echo "$('#totAcc_td').text('".$rowSumm[csf("tot_accessories_cost")]."');\n";
		echo "$('#totCm_td').text('".$rowSumm[csf("tot_cm_cost")]."');\n";
		echo "$('#totFriCst_td').text('".$rowSumm[csf("tot_fright_cost")]."');\n";
		echo "$('#totCourierCst_td').text('".$rowSumm[csf("tot_courier_cost")]."');\n";
		echo "$('#totLbTstCst_td').text('".$rowSumm[csf("tot_lab_test_cost")]."');\n";
		echo "$('#totInspCst_td').text('".$rowSumm[csf("inspection_cost")]."');\n";
		echo "$('#totMissCst_td').text('".$rowSumm[csf("tot_miscellaneous_cost")]."');\n";
		echo "$('#totOtherCst_td').text('".$rowSumm[csf("tot_other_cost")]."');\n";
		echo "$('#totDlcCst_td').text('".$rowSumm[csf("lc_cost")]."');\n";
		echo "$('#totCommlCst_td').text('".$rowSumm[csf("commercial_cost")]."');\n";
		echo "$('#totCommCst_td').text('".$rowSumm[csf("tot_commission_cost")]."');\n";
		echo "$('#totCost_td').text('".$rowSumm[csf("tot_cost")]."');\n";
		echo "$('#totalFob_td').text('".$rowSumm[csf("tot_fob_cost")]."');\n";
		echo "$('#totalFob_td').attr('prev_fob','".$rowSumm[csf("tot_fob_cost")]."');\n";
		
		echo "$('#totRmgQty_td').text('".$rowSumm[csf("tot_rmg_ratio")]."');\n";
		echo "$('#totFOBCost_td').text('".number_format(($rowSumm[csf("tot_cost")]*12),4)."');\n";
	}
	unset($sql_result_summ);
	$sql_item_summ="select item_id, fabric_cost, sp_operation_cost, wash_cost, accessories_cost, cpm, smv, efficiency, cm_cost, frieght_cost, courier_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, commercial_cost, inspection_cost, lc_cost, fob_pcs, rmg_ratio from qc_item_cost_summary where mst_id='$cond_data' and status_active=1 and is_deleted=0";
	$sql_result_item_summ=sql_select($sql_item_summ);
	foreach($sql_result_item_summ as $rowItemSumm)
	{
		echo "$('#fab_td".$rowItemSumm[csf("item_id")]."').text('".$rowItemSumm[csf("fabric_cost")]."');\n";
		echo "$('#spOpe_td".$rowItemSumm[csf("item_id")]."').text('".$rowItemSumm[csf("sp_operation_cost")]."');\n";
		echo "$('#wash_td".$rowItemSumm[csf("item_id")]."').text('".$rowItemSumm[csf("wash_cost")]."');\n";
		echo "$('#acc_td".$rowItemSumm[csf("item_id")]."').text('".$rowItemSumm[csf("accessories_cost")]."');\n";
		echo "$('#txt_cpm_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("cpm")]."');\n";
		echo "$('#txt_smv_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("smv")]."');\n";
		echo "$('#txt_eff_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("efficiency")]."');\n";
		echo "$('#txtCmCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("cm_cost")]."');\n";
		echo "$('#txtFriCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("frieght_cost")]."');\n";
		echo "$('#txtCourierCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("courier_cost")]."');\n";
		echo "$('#txtLtstCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("lab_test_cost")]."');\n";
		echo "$('#txtInspCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("inspection_cost")]."');\n";
		echo "$('#txtMissCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("miscellaneous_cost")]."');\n";
		echo "$('#txtOtherCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("other_cost")]."');\n";
		echo "$('#txtdlcCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("lc_cost")]."');\n";
		echo "$('#txtCommlCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("commercial_cost")]."');\n";
		echo "$('#txtCommCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("commission_cost")]."');\n";
		
		echo "$('#fobT_td".$rowItemSumm[csf("item_id")]."').text('".$rowItemSumm[csf("fob_pcs")]."');\n";
		echo "$('#txtRmgQty_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("rmg_ratio")]."');\n";
		echo "$('#fobPcsT_td".$rowItemSumm[csf("item_id")]."').text('".number_format(($rowItemSumm[csf("fob_pcs")]*12),4)."');\n";
	}
	unset($sql_result_item_summ);
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
	$sql_item_id="Select lib_item_id from qc_template where tuid='$data' and status_active=1 and is_deleted=0";
	$lib_item_id="";
	$sql_item_id_res=sql_select($sql_item_id);
	foreach($sql_item_id_res as $row)
	{
		if($lib_item_id=='') $lib_item_id=trim($row[csf("lib_item_id")]); else $lib_item_id.=','.trim($row[csf("lib_item_id")]);
	}
	echo $lib_item_id;
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
		$hid_qc_no=str_replace("'","",$hid_qc_no);
		$dup_styleRef=str_replace("'","",$txt_styleRef);
		$dup_season_id=str_replace("'","",$cbo_season_id);
		$dup_buyer_id=str_replace("'","",$cbo_buyer_id);
		$msg="Duplicate Inquiry ID, Styel Ref, Buyer And Season.";
		
		//all_table qc_template, qc_mst, qc_style_temp_data, qc_fabric_dtls, qc_fabric_string_data, qc_cons_rate_dtls, qc_tot_cost_summary, qc_item_cost_summary, qc_meeting_mst, qc_meeting_person, qc_meeting_dtls
		//echo $txtItemDes_1_1_1; die;
		
		$mst_id=return_next_id("id", "qc_mst", 1);
		
		$qcno=$_SESSION['logic_erp']['user_id'].$mst_id;
		$sql_mst=sql_select("select id, company_id, location_id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, inquery_id, style_ref, buyer_id, uom, cons_basis, season_id, season_year, brand_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, buyer_remarks, option_remarks, commercial_cost_method from qc_mst where qc_no='$hid_qc_no'");
		
		//echo "select id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, style_ref, buyer_id, cons_basis, season_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date from qc_mst where id='$update_id'";
		
		$cbo_company_id=$sql_mst[0][csf('company_id')];
		$cbo_location_id=$sql_mst[0][csf('location_id')];
		$cbo_temp_id=$sql_mst[0][csf('temp_id')];
		$txt_temp_id=$sql_mst[0][csf('lib_item_id')];
		//$txt_styleRef=$sql_mst[0][csf('style_ref')];
		$cbo_buyer_id=$sql_mst[0][csf('buyer_id')];
		$cbouom=$sql_mst[0][csf('uom')];
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
		$txt_option_remarks=$sql_mst[0][csf('option_remarks')];
		$txtcommercial_cost_method=$sql_mst[0][csf('commercial_cost_method')];
		
		$txt_inquery_id='';//$sql_mst[0][csf('inquery_id')];
		$cbo_season_year=$sql_mst[0][csf('season_year')];
		$cbo_brand=$sql_mst[0][csf('brand_id')];
		$old_id=$sql_mst[0][csf('id')];
		
		if (is_duplicate_field( "cost_sheet_no", "qc_mst", "style_ref='$dup_styleRef' and inquery_id='$txt_inquery_id' and season_id='$dup_season_id' and buyer_id='$dup_buyer_id' and entry_form=430 and status_active=1 and is_deleted=0")==1)
		{
			echo "11**".$msg;
			die;
		}
		
		$field_array_mst="id, company_id, location_id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, inquery_id, style_ref, buyer_id, uom, cons_basis, season_id, season_year, brand_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, pre_cost_sheet_id, revise_no, option_id, buyer_remarks, option_remarks, commercial_cost_method, qc_no, entry_form, inserted_by, insert_date, status_active, is_deleted";
		
		
		//$txt_temp_id=explode(",",str_replace("'","",$txt_temp_id));
		//asort($txt_temp_id);
		//$txt_temp_id="'".implode(",",$txt_temp_id)."'";
		$option_id=0;
		$revise_no=0;
		$autoCostSheetNo=$_SESSION['logic_erp']['user_id'].str_pad($mst_id,8,'0',STR_PAD_LEFT);
		
		$data_array_mst="(".$mst_id.",'".$cbo_company_id."','".$cbo_location_id."',".$mst_id.",'".$autoCostSheetNo."','".$cbo_temp_id."','".$txt_temp_id."','".$txt_inquery_id."',".$txt_styleRef.",'".$cbo_buyer_id."','".$cbouom."','".$cbo_cons_basis_id."',".$cbo_season_id.",'".$cbo_season_year."','".$cbo_brand."','".$txt_styleDesc."','".$cbo_subDept_id."','".$txt_delivery_date."','".$txt_exchangeRate."','".$txt_offerQty."','".$txt_quotedPrice."','".$txt_tgtPrice."','".$cbo_stage_id."','".$txt_costingDate."','".$hid_qc_no."','".$revise_no."','".$option_id."','".$txt_costing_remarks."','".$txt_option_remarks."','".$txtcommercial_cost_method."','".$qcno."',430,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		
		//echo $data_array; die;
		//$template_id=return_next_id("id", "qc_template", 1);
		//$template_id = return_id( $txt_temp_id, $template_library, "qc_template", "id,item_id");
		//$dtls_id=return_next_id("id", "qc_fabric_dtls", 1);
		$temp_id=explode(",",str_replace("'","",$txt_temp_id));
		//$sql_fab=sql_select("select id, mst_id, item_id, uniq_id, body_part, des, value, alw from qc_fabric_dtls where mst_id='$hid_qc_no' order by id ASC");
		//echo "select id, mst_id, item_id, uniq_id, body_part, des, value, alw from qc_fabric_dtls where mst_id='$update_id' order by id ASC"; die;
		//$field_array_fab="id, mst_id, item_id, uniq_id, body_part, des, value, alw, inserted_by, insert_date";
   		//$fstr_id=return_next_id("id", "qc_fabric_string_data", 1); 
		/*$add_comma_fab=0; $data_array_fab="";
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
			$data_array_fab .="(".$dtls_id.",".$qcno.",'".$item_id."','".$uniq_id."','".$body_part_id."','".$txtItemDes."','".$txtVal."','".$txtAw."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$add_comma_fab++;
			$dtls_id=$dtls_id+1;
		}*/
		
		$cons_rate_id=return_next_id("id", "qc_cons_rate_dtls", 1);
		$sql_consrate=sql_select("select id, mst_id, item_id, type, particular_type_id, consumption, ex_percent, tot_cons, uom, rate, value, fab_id, description, use_for, fc_per, body_part_id, supp_ref, rate_data, nominated_supp_multi from qc_cons_rate_dtls where mst_id='$hid_qc_no' order by id ASC");
		
		//$field_array_consrate="id, mst_id, item_id, type, particular_type_id, consumption, ex_percent, tot_cons, rate, value, fab_id, description, use_for, fc_per, body_part_id, supp_ref, inserted_by, insert_date, status_active, is_deleted";
		
		
		$field_array_consrate="id, mst_id, item_id, type, particular_type_id, consumption, ex_percent, tot_cons, uom, rate, value, fab_id, description, use_for, fc_per, body_part_id, supp_ref, rate_data, nominated_supp_multi, inserted_by, insert_date, status_active, is_deleted";
		$add_comma_cons_rate=0; $data_array_cons_rate="";
		foreach( $sql_consrate as $rowConsrate )
		{
			$old_consrate_id=$rowConsrate[csf('id')];
			$item_id=$rowConsrate[csf('item_id')];
			$type=$rowConsrate[csf('type')];
			$head_id=$rowConsrate[csf('particular_type_id')];
			$cons=$rowConsrate[csf('consumption')];
			$ex_percent=$rowConsrate[csf('ex_percent')];
			$tot_cons=$rowConsrate[csf('tot_cons')];
			$consuom=$rowConsrate[csf('uom')];
			$rate=$rowConsrate[csf('rate')];
			$tot_val=$rowConsrate[csf('value')];
			$fab_id=$rowConsrate[csf('fab_id')];
			$description=$rowConsrate[csf('description')];
			$use_for=$rowConsrate[csf('use_for')];
			$fc_per=$rowConsrate[csf('fc_per')];
			$body_part_id=$rowConsrate[csf('body_part_id')];
			$supp_ref=$rowConsrate[csf('supp_ref')];
			$rate_data=$rowConsrate[csf('rate_data')];
			$nominated_supp_multi=$rowConsrate[csf('nominated_supp_multi')];
			
			if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
			$data_array_cons_rate .="(".$cons_rate_id.",".$qcno.",'".$item_id."','".$type."','".$head_id."','".$cons."','".$ex_percent."','".$tot_cons."','".$consuom."','".$rate."','".$tot_val."','".$fab_id."','".$description."','".$use_for."','".$fc_per."','".$body_part_id."','".$supp_ref."','".$rate_data."','".$nominated_supp_multi."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			
			$cons_rate_id=$cons_rate_id+1;
			$add_comma_cons_rate++;
		}
		
		//echo $data_array_cons_rate; 
		//die;
		$tc_sum_id=return_next_id("id", "qc_tot_cost_summary", 1);
		$sql_tot_cost=sql_select("select id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_wash_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_courier_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, commercial_per, commercial_cost, inspection_cost, lc_per, lc_cost, tot_cost, tot_fob_cost, tot_rmg_ratio from qc_tot_cost_summary where mst_id='$hid_qc_no'");
		
		$field_array_tot_cost="id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_wash_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_courier_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, commercial_per, commercial_cost, inspection_cost, lc_per, lc_cost, tot_cost, tot_fob_cost, tot_rmg_ratio, inserted_by, insert_date";
		
		//$ex_tot_cost_data=explode('_',$data_tot_cost_summ);
		
		$cbo_buyer_agent=$sql_tot_cost[0][csf('buyer_agent_id')];
		$cbo_agent_location=$sql_tot_cost[0][csf('location_id')];
		$txt_noOfPack=$sql_tot_cost[0][csf('no_of_pack')];
		$cmPop=$sql_tot_cost[0][csf('is_cm_calculative')];
		//$txt_smv=trim($ex_tot_cost_data[4]);
		//$txt_eff=trim($ex_tot_cost_data[5]);
		$txt_lumSum_cost=$sql_tot_cost[0][csf('mis_lumsum_cost')];
		$txt_commlPer=$sql_tot_cost[0][csf('commercial_per')];
		$txt_commPer=$sql_tot_cost[0][csf('commision_per')];
		$totFab_td=$sql_tot_cost[0][csf('tot_fab_cost')];
		$totSpc_td=$sql_tot_cost[0][csf('tot_sp_operation_cost')];
		$totWash_td=$sql_tot_cost[0][csf('tot_wash_cost')];
		$totAcc_td=$sql_tot_cost[0][csf('tot_accessories_cost')];
		$totCm_td=$sql_tot_cost[0][csf('tot_cm_cost')];
		$totFriCst_td=$sql_tot_cost[0][csf('tot_fright_cost')];
		$totCourierCst_td=$sql_tot_cost[0][csf('tot_courier_cost')];
		$totLbTstCst_td=$sql_tot_cost[0][csf('tot_lab_test_cost')];
		$totMissCst_td=$sql_tot_cost[0][csf('tot_miscellaneous_cost')];
		$totOtherCst_td=$sql_tot_cost[0][csf('tot_other_cost')];
		$totCommlCst_td=$sql_tot_cost[0][csf('commercial_cost')];
		$totCommCst_td=$sql_tot_cost[0][csf('tot_commission_cost')];
		$totCost_td=$sql_tot_cost[0][csf('tot_cost')];
		$totalFob_td=$sql_tot_cost[0][csf('tot_fob_cost')];
		$totalRmg_td=$sql_tot_cost[0][csf('tot_rmg_ratio')];
		$totInspCst_td=$sql_tot_cost[0][csf('inspection_cost')];
		$txt_lcPer=$sql_tot_cost[0][csf('lc_per')];
		$totDlcCst_td=$sql_tot_cost[0][csf('lc_cost')];
		
		$data_array_tot_cost="(".$tc_sum_id.",".$qcno.",'".$cbo_buyer_agent."','".$cbo_agent_location."','".$txt_noOfPack."',0,'".$cmPop."','".$txt_lumSum_cost."','".$txt_commPer."','".$totFab_td."','".$totSpc_td."','".$totWash_td."','".$totAcc_td."','".$totCm_td."','".$totFriCst_td."','".$totCourierCst_td."','".$totLbTstCst_td."','".$totMissCst_td."','".$totOtherCst_td."','".$totCommCst_td."','".$txt_commlPer."','".$totCommlCst_td."','".$totInspCst_td."','".$txt_lcPer."','".$totDlcCst_td."','".$totCost_td."','".$totalFob_td."','".$totalRmg_td."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//echo $data_array_tot_cost; die;
		$sql_item_tot=sql_select("select id, mst_id, item_id, fabric_cost, sp_operation_cost, wash_cost, accessories_cost, cpm, smv, efficiency, cm_cost, frieght_cost, courier_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, commercial_cost, inspection_cost, lc_cost, fob_pcs, rmg_ratio from qc_item_cost_summary where mst_id='$hid_qc_no' order by id ASC");
		
		//$field_array_item_tot="id, mst_id, item_id, fabric_cost, sp_operation_cost, wash_cost, accessories_cost, cpm, smv, efficiency, cm_cost, frieght_cost, courier_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs, rmg_ratio, inserted_by, insert_date";
		
		$field_array_item_tot="id, mst_id, item_id, fabric_cost, sp_operation_cost, wash_cost, accessories_cost, cpm, smv, efficiency, cm_cost, frieght_cost, courier_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, commercial_cost, inspection_cost, lc_cost, fob_pcs, rmg_ratio, inserted_by, insert_date";
		$item_cost_id=return_next_id("id", "qc_item_cost_summary", 1);
		$add_comma_item_tot=0; $data_array_item_tot="";
		foreach($sql_item_tot as $rowItem_tot)
		{
			$fab_td=$spOpe_td=$wash_td=$acc_td=$txtCpm=$txtSmv=$txtEff=$txtCmCost=$txtFriCost=$txtCourierCost=$txtLtstCost=$txtMissCost=$txtOtherCost=$txtCommCost=$fobT_td=$txtInspCost=$txtdlcCost=$tot_item_id=$rmg_ratio=0;
			$fab_td=$rowItem_tot[csf('fabric_cost')];
			$spOpe_td=$rowItem_tot[csf('sp_operation_cost')];
			$wash_td=$rowItem_tot[csf('wash_cost')];
			$acc_td=$rowItem_tot[csf('accessories_cost')];
			$txtCpm=$rowItem_tot[csf('cpm')];
			$txtSmv=$rowItem_tot[csf('smv')];
			$txtEff=$rowItem_tot[csf('efficiency')];
			$txtCmCost=$rowItem_tot[csf('cm_cost')];
			$txtFriCost=$rowItem_tot[csf('frieght_cost')];
			$txtCourierCost=$rowItem_tot[csf('courier_cost')];
			$txtLtstCost=$rowItem_tot[csf('lab_test_cost')];
			$txtMissCost=$rowItem_tot[csf('miscellaneous_cost')];
			$txtOtherCost=$rowItem_tot[csf('other_cost')];
			$txtCommlCost=$rowItem_tot[csf('commercial_cost')];
			$txtCommCost=$rowItem_tot[csf('commission_cost')];
			$fobT_td=$rowItem_tot[csf('fob_pcs')];
			$rmg_ratio=$rowItem_tot[csf('rmg_ratio')];
			$txtInspCost=$rowItem_tot[csf('inspection_cost')];
			$txtdlcCost=$rowItem_tot[csf('lc_cost')];
			$tot_item_id=$rowItem_tot[csf('item_id')];
			
			if ($add_comma_item_tot!=0) $data_array_item_tot .=",";
			$data_array_item_tot .="(".$item_cost_id.",".$qcno.",'".$tot_item_id."','".$fab_td."','".$spOpe_td."','".$wash_td."','".$acc_td."','".$txtCpm."','".$txtSmv."','".$txtEff."','".$txtCmCost."','".$txtFriCost."','".$txtCourierCost."','".$txtLtstCost."','".$txtMissCost."','".$txtOtherCost."','".$txtCommCost."','".$txtCommlCost."','".$txtInspCost."','".$txtdlcCost."','".$fobT_td."','".$rmg_ratio."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$item_cost_id=$item_cost_id+1;
			$add_comma_item_tot++;
		}
		
		//echo "insert into qc_tot_cost_summary (".$field_array_tot_cost.") values ".$data_array_tot_cost;die;
		/*$meetMst_id=return_next_id("id", "qc_meeting_mst", 1);
		$meetPer_id=return_next_id("id", "qc_meeting_mst", 1);
		$meetDtls_id=return_next_id("id", "qc_meeting_mst", 1);*/
		
		//echo "0**"."insert into qc_item_cost_summary (".$field_array_item_tot.") values ".$data_array_item_tot; die;
		$flag=1;
		$rID=sql_insert("qc_mst",$field_array_mst,$data_array_mst,0);	
		if($rID) $flag=1; else $flag=0;
		
		//$rID_fab=sql_insert("qc_fabric_dtls",$field_array_fab,$data_array_fab,0);	
		//if($rID_fab) $flag=1; else $flag=0;
		$rID_consRate=sql_insert("qc_cons_rate_dtls",$field_array_consrate,$data_array_cons_rate,0);	
		if($rID_consRate==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID_tot_cost=sql_insert("qc_tot_cost_summary",$field_array_tot_cost,$data_array_tot_cost,0);	
		if($rID_tot_cost==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID_item_tot=sql_insert("qc_item_cost_summary",$field_array_item_tot,$data_array_item_tot,0);	
		if($rID_item_tot==1 && $flag==1) $flag=1; else $flag=0;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				//echo "0**".str_replace("'",'',$mst_id).'**'.str_replace("'",'',$autoCostSheetNo);
				echo "0**".str_replace("'",'',$mst_id).'**'.str_replace("'",'',$autoCostSheetNo).'**'.str_replace("'",'',$revise_no).'**'.str_replace("'",'',$option_id).'**1'.'**0'.'**'.str_replace("'",'',$qcno);
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
				echo "0**".str_replace("'",'',$mst_id).'**'.str_replace("'",'',$autoCostSheetNo).'**'.str_replace("'",'',$revise_no).'**'.str_replace("'",'',$option_id).'**1'.'**0'.'**'.str_replace("'",'',$qcno);
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
	$exdata=explode('__',$data);
	$qc_no=$exdata[0];
	$updateid=$exdata[1];
	$cost_sheet=$exdata[2];
	$user_id=$_SESSION['logic_erp']['user_id'];
	$user_level=$_SESSION['logic_erp']["user_level"];
	$sql_data=sql_select("Select cost_sheet_no, buyer_id, season_id, department_id, temp_id, lib_item_id, style_ref, offer_qty, revise_no, option_id, delivery_date, uom from qc_mst where qc_no='$qc_no' ");
	
	$uom=$sql_data[0][csf('uom')];
	
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
	if($gmt_itm_count>1) $selected_gmt_type=2; else $selected_gmt_type=1;
	
	$qcCons_from=return_field_value("excut_source","variable_order_tracking","variable_list=68 and is_deleted=0 and status_active=1 order by id","excut_source");
	//$qcCons_from=2;excut_source=2 and 
	//echo $qcCons_from;
	if($qcCons_from==2) $cons_cond="tot_cons";//Tot Cons
	else $cons_cond="consumption";//Tot Cons
	
	
	$sql_summ=sql_select("select no_of_pack, tot_fob_cost from qc_tot_cost_summary where mst_id='$qc_no' and status_active=1 and is_deleted=0");
	//$sql_cons_rate="select id, item_id, type, particular_type_id, consumption, unit, is_calculation, rate, rate_data, value from qc_cons_rate_dtls where mst_id='$data[0]' and status_active=1 and is_deleted=0 order by id asc";
	$sql_cons=sql_select("select item_id, sum(tot_cons) as tot_cons from qc_cons_rate_dtls where mst_id='$qc_no' and type=1 group by item_id");//type ='1' and
	$item_wise_cons_arr=array();
	foreach($sql_cons as $cRow)
	{
		if($uom==12)
		{
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_kg']=$cRow[csf("qty_kg")];
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_mtr']=0;
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_yds']=$cRow[csf("qty_yds")];
		}
		else if($uom==23)
		{
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_kg']=0;
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_mtr']=$cRow[csf("qty_kg")];
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_yds']=$cRow[csf("qty_yds")];
		}
		else if($uom==27)
		{
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_kg']=0;
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_mtr']=0;
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_yds']=$cRow[csf("tot_cons")];
		}
	}
	//$sql_result_summ=sql_select($sql_summ);
	//print_r($item_wise_cons_arr);
	$sql_approved="select b.approval_need from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and b.status_active=1 and page_id=28 and b.approval_need=1 ";
	$result_nasscity = sql_select($sql_approved); $approved_need=2;
	foreach($result_nasscity as $row)
	{
		$approved_need=$row[csf("approval_need")];
	}
	unset($result_nasscity);

	$team_dtls_sql=sql_select("select a.user_tag_id from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and b.user_tag_id='$user_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.user_tag_id");
	if(count($team_dtls_sql)>0) $team_dtls_arr[$user_id]=$team_dtls_sql[0][csf('user_tag_id')];
	else $team_dtls_arr[$user_id]='';
	//print_r($team_dtls_arr);
	$disable="";
	if($user_level==2 || $team_dtls_arr[$user_id]!="") $disable=""; else $disable="disabled";
	
	$isteam_leader=return_field_value("user_tag_id","lib_marketing_team","user_tag_id='$user_id' and is_deleted=0 and status_active=1","user_tag_id");
	//echo $user_level.'-'.$isteam_leader.'-'.$approved_need;
	if($user_level==2 || $isteam_leader!='') $admin_or_leader="";  else $admin_or_leader="none";
	
	if($approved_need==1) $admin_or_leader="none";
	//echo $admin_or_leader.'===';
	
	$sql_cause="select MAX(id) as id from fabric_booking_approval_cause where entry_form=28 and user_id='$user_id' and booking_id='$qc_no' and status_active=1 and is_deleted=0";
	//echo $sql_cause; die;page_id='$menu_id' and 
	$nameArray_cause=sql_select($sql_cause);
	if(count($nameArray_cause)>0){
		foreach($nameArray_cause as $row)
		{
			$unapp_cause1=return_field_value("NOT_APPROVAL_CAUSE", "fabric_booking_approval_cause", "id='".$row[csf("id")]."' and status_active=1 and is_deleted=0");
			$unapp_cause = str_replace(array("\r", "\n"), ' ', $unapp_cause1);
		}
	}
	else $unapp_cause = '';
	?>
    <script>
		var permission='<? echo $permission; ?>'; 
		var qcCons_from='<? echo $qcCons_from; ?>'; 
		function fnc_confirm_entry( operation )
		{
			freeze_window(operation);
			if(operation==3)
			{
				//var fab_cons_kg=$("#txtFabConkg_bom").val();
				//var fab_cons_mtr=$("#txtFabConmtr_bom").val();
				var fab_cons_yds=$("#txtFabConyds_bom").val();
				var fab_amount=$("#txtFabCst_bom").val();
				var sp_oparation_amount=$("#txtSpOpa_bom").val();
				var wash_amount=$("#txtWash_bom").val();
				var acc_amount=$("#txtAcc_bom").val();
				var fright_amount=$("#txtFrightCst_bom").val();
				var lab_amount=$("#txtLabCst_bom").val();
				var insp_amount=$("#txtInspCst_bom").val();
				var misce_amount=$("#txtMiscCst_bom").val();
				var courier_amount=$("#txtCourierCst_bom").val();
				var other_amount=$("#txtOtherCst_bom").val();
				var comml_amount=$("#txtCommlCst_bom").val();
				var comm_amount=$("#txtCommCst_bom").val();
				var fob_amount=$("#txtFobDzn_bom").val();
				var cm_amount=$("#txtCmCst_bom").val();
				var rmg_ratio=$("#txtPack_bom").val();
							
				var temp_id=$('#txtItem_id').val();
				var split_tmep_id=temp_id.split(','); var ab=0;
				var qc_fab_kg=0; var qc_fab_mtr=0; var qc_fab_yds=0; var qc_fab_amt=0; var qc_sp_amt=0; var qc_wash_amt=0; var qc_acc_amt=0; var qc_fri_amt=0; var qc_lab_amt=0; var qc_insp_amt=0; var qc_misce_amt=0; var qc_courier_amt=0; var qc_other_amt=0; var qc_comml_amt=0; var qc_comm_amt=0; var qc_fob_amt=0; var qc_cm_amt=0; var qc_rmg_amt=0;
				for(j=1; j<=split_tmep_id.length; j++)
				{
					var itm_id=trim(split_tmep_id[ab]);
					
					//qc_fab_kg+=$("#txtFabConkg_"+itm_id).val()*1;
					//qc_fab_mtr+=$("#txtFabConmtr_"+itm_id).val()*1;
					qc_fab_yds+=$("#txtFabConyds_"+itm_id).val()*1;
					qc_fab_amt+=$("#txtFabCst_"+itm_id).val()*1;
					qc_sp_amt+=$("#txtSpOpa_"+itm_id).val()*1;
					qc_wash_amt+=$("#txtWash_"+itm_id).val()*1;
					qc_acc_amt+=$("#txtAcc_"+itm_id).val()*1;
					qc_fri_amt+=$("#txtFrightCst_"+itm_id).val()*1;
					qc_lab_amt+=$("#txtLabCst_"+itm_id).val()*1;
					qc_insp_amt+=$("#txtInspCst_"+itm_id).val()*1;
					qc_misce_amt+=$("#txtMiscCst_"+itm_id).val()*1;
					qc_courier_amt+=$("#txtCourierCst_"+itm_id).val()*1;
					qc_other_amt+=$("#txtOtherCst_"+itm_id).val()*1;
					qc_comml_amt+=$("#txtCommlCst_"+itm_id).val()*1;
					qc_comm_amt+=$("#txtCommCst_"+itm_id).val()*1;
					qc_fob_amt+=$("#txtFobDzn_"+itm_id).val()*1;
					qc_cm_amt+=$("#txtCmCst_"+itm_id).val()*1;
					qc_rmg_amt+=$("#txtPack_"+itm_id).val()*1;
					
					ab++;
				}
				//alert(qc_fab_amt); release_freezing();return;
				var job_no=$("#txt_job_style").val();
				if(job_no!="")
				{
					/*if(qc_fab_kg<fab_cons_kg)
					{
						alert("BOM Fab. Cons. Kg is Greater Than QC.");
						release_freezing();
						return;
					}
					else if(qc_fab_mtr<fab_cons_mtr)
					{
						alert("BOM Fab. Cons. Mtr is Greater Than QC.");
						release_freezing();
						return;
					}
					else */
					if(qc_fab_yds<fab_cons_yds)
					{
						alert("BOM Fab. Cons. Yds is Greater Than QC.");
						release_freezing();
						return;
					}
					else if(qc_fab_amt<fab_amount)
					{
						alert("BOM Fab. Amount is Greater Than QC.");
						release_freezing();
						return;
					}
					else if( qc_sp_amt<sp_oparation_amount )
					{
						alert("BOM Special Opera. Amount is Greater Than QC.");
						release_freezing();
						return;
					}
					else if(qc_wash_amt<wash_amount)
					{
						alert("BOM Wash Amount is Greater Than QC.");
						release_freezing();
						return;
					}
					else if(qc_acc_amt<acc_amount)
					{
						alert("BOM Accessories Amount is Greater Than QC.");
						release_freezing();
						return;
					}
					else if(qc_fri_amt<fright_amount)
					{
						alert("BOM Frieght Amount is Greater Than QC.");
						release_freezing();
						return;
					}
					else if(qc_lab_amt<lab_amount)
					{
						alert("BOM Lab - Test Amount is Greater Than QC.");
						release_freezing();
						return;
					}
					else if(qc_insp_amt<insp_amount)
					{
						alert("BOM Inspection Amount is Greater Than QC.");
						release_freezing();
						return;
					}
					else if(qc_courier_amt<courier_amount)
					{
						alert("BOM Courier Amount is Greater Than QC.");
						release_freezing();
						return;
					}
					/*else if(qc_misce_amt<misce_amount)
					{
						alert("BOM Misce. Amount is Greater Than QC.");
						release_freezing();
						return;
					}
					else if(qc_other_amt<other_amount)
					{
						alert("BOM Other Amount is Greater Than QC.");
						release_freezing();
						return;
					}*/
					else if(qc_comml_amt<comml_amount)
					{
						alert("BOM Commercial Cost is Greater Than QC.");
						release_freezing();
						return;
					}
					else if(qc_comm_amt<comm_amount)
					{
						alert("BOM Commis. Amount is Greater Than QC.");
						release_freezing();
						return;
					}
					/*else if(qc_fob_amt<fob_amount)
					{
						alert("BOM FOB/DZN Amount is Greater Than QC.");
						release_freezing();
						return;
					}
					else if(qc_cm_amt<cm_amount)
					{
						alert("BOM CM Amount is Greater Than QC.");
						release_freezing();
						return;
					}*/
					
					var confirm_style=$("#txt_confirm_style").val();
					var style_ref=$("#txt_style_job").val();
					
					if(trim(confirm_style)!=trim(style_ref))
					{
						var rr=confirm("Both Style are not same.\n Are you sure?");
						if(rr==true)
						{
							 //delete_country=1;
						}
						else
						{
							//delete_country=0;
							release_freezing();	
							return;
						}
					}
				}
			}
			var temp_id=$('#txtItem_id').val();
			var split_tmep_id=temp_id.split(',');	
			var data_mst=''; var data_dtls=''; var k=0;
			for(i=1; i<=split_tmep_id.length; i++)
			{
				var itm_id=trim(split_tmep_id[k]);
				data_dtls+="&txtitemid_" + itm_id + "='" + $('#txtitemid_'+itm_id).val()+"'"+"&txtdtlsupid_" + itm_id + "='" + $('#txtdtlsupid_'+itm_id).val()+"'"+"&txtFabConyds_" + itm_id + "='" + $('#txtFabConyds_'+itm_id).val()+"'"+"&txtFabCst_" + itm_id + "='" + $('#txtFabCst_'+itm_id).val()+"'"+"&txtSpOpa_" + itm_id + "='" + $('#txtSpOpa_'+itm_id).val()+"'"+"&txtWash_" + itm_id + "='" + $('#txtWash_'+itm_id).val()+"'"+"&txtAcc_" + itm_id + "='" + $('#txtAcc_'+itm_id).val()+"'"+"&txtFrightCst_" + itm_id + "='" + $('#txtFrightCst_'+itm_id).val()+"'"+"&txtLabCst_" + itm_id + "='" + $('#txtLabCst_'+itm_id).val()+"'"+"&txtInspCst_" + itm_id + "='" + $('#txtInspCst_'+itm_id).val()+"'"+"&txtMiscCst_" + itm_id + "='" + $('#txtMiscCst_'+itm_id).val()+"'"+"&txtCourierCst_" + itm_id + "='" + $('#txtCourierCst_'+itm_id).val()+"'"+"&txtOtherCst_" + itm_id + "='" + $('#txtOtherCst_'+itm_id).val()+"'"+"&txtCommlCst_" + itm_id + "='" + $('#txtCommlCst_'+itm_id).val()+"'"+"&txtCommCst_" + itm_id + "='" + $('#txtCommCst_'+itm_id).val()+"'"+"&txtFobDzn_" + itm_id + "='" + $('#txtFobDzn_'+itm_id).val()+"'"+"&txtCpm_" + itm_id + "='" + $('#txtCpm_'+itm_id).val()+"'"+"&txtSmv_" + itm_id + "='" + $('#txtSmv_'+itm_id).val()+"'"+"&txtCmCst_" + itm_id + "='" + $('#txtCmCst_'+itm_id).val()+"'"+"&txtPack_" + itm_id + "='" + $('#txtPack_'+itm_id).val()+"'"+"&txtFabConydsPer_" + itm_id + "='" + $('#txtFabConydsPer_'+itm_id).val()+"'"+"&txtFabCstPer_" + itm_id + "='" + $('#txtFabCstPer_'+itm_id).val()+"'"+"&txtSpOpaPer_" + itm_id + "='" + $('#txtSpOpaPer_'+itm_id).val()+"'"+"&txtWashPer_" + itm_id + "='" + $('#txtWashPer_'+itm_id).val()+"'"+"&txtAccPer_" + itm_id + "='" + $('#txtAccPer_'+itm_id).val()+"'"+"&txtFrightCstPer_" + itm_id + "='" + $('#txtFrightCstPer_'+itm_id).val()+"'"+"&txtLabCstPer_" + itm_id + "='" + $('#txtLabCstPer_'+itm_id).val()+"'"+"&txtInspCstPer_" + itm_id + "='" + $('#txtInspCstPer_'+itm_id).val()+"'"+"&txtMiscCstPer_" + itm_id + "='" + $('#txtMiscCstPer_'+itm_id).val()+"'"+"&txtCourierCstPer_" + itm_id + "='" + $('#txtCourierCstPer_'+itm_id).val()+"'"+"&txtOtherCstPer_" + itm_id + "='" + $('#txtOtherCstPer_'+itm_id).val()+"'"+"&txtCommlCstPer_" + itm_id + "='" + $('#txtCommlCstPer_'+itm_id).val()+"'"+"&txtCommCstPer_" + itm_id + "='" + $('#txtCommCstPer_'+itm_id).val()+"'"+"&txtFobDznPer_" + itm_id + "='" + $('#txtFobDznPer_'+itm_id).val()+"'"+"&txtCpmPer_" + itm_id + "='" + $('#txtCpmPer_'+itm_id).val()+"'"+"&txtSmvPer_" + itm_id + "='" + $('#txtSmvPer_'+itm_id).val()+"'"+"&txtCmCstPer_" + itm_id + "='" + $('#txtCmCstPer_'+itm_id).val()+"'"+"&txtPackPer_" + itm_id + "='" + $('#txtPackPer_'+itm_id).val()+"'";//+"'"+"&txtFabConkg_" + itm_id + "='" + $('#txtFabConkg_'+itm_id).val()+"'"+"&txtFabConmtr_" + itm_id + "='" + $('#txtFabConmtr_'+itm_id).val()
				k++;
			}
			var data="action=save_update_delete_confirm_style&operation="+operation+get_submitted_data_string('txt_costSheet_id*txtConfirm_id*txtItem_id*txt_confirm_style*txt_order_qty*txt_confirm_fob*txt_ship_date*txt_job_id*cbo_approved_status*cbo_ready_approve',"../../../",2)+data_dtls;
			
			//var data=data_mst+data_dtls;
			//alert(data); release_freezing();//return;
			http.open("POST","quick_costing_woven_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_stage_entry_reponse;
		}
		 
		function fnc_stage_entry_reponse()
		{
			if(http.readyState == 4) 
			{
				var reponse=http.responseText.split('**');
				//alert(reponse[0])
				if (trim(reponse[0])=="approvedQc")
				{
					alert("This Option (QC) is Approved.");
					release_freezing();
					return;
				}
				
				if (trim(reponse[0])==13)
				{
					var altbom_msg="Delete Restricted, BOM Found, Job No: "+trim(reponse[1]);
					alert(altbom_msg);
					release_freezing();
					return;
					//document.getElementById('approve1').value = 'Un-Approved';
				}
				
				if (trim(reponse[0])==6) alert(trim(reponse[1]));
				if(trim(reponse[0])==0 || trim(reponse[0])==1 || trim(reponse[0])==2 || trim(reponse[0])==3)
				{
					if (trim(reponse[0])==0)
					{
						alert("Data is Save Successfully");
						//document.getElementById('approve1').value = 'Approved';
					}
					else if (trim(reponse[0])==1)
					{
						alert("Data is Update Successfully");
						//document.getElementById('approve1').value = 'Approved';
					}
					else if (trim(reponse[0])==2)
					{
						alert("Data is Deleted Successfully");
						//document.getElementById('approve1').value = 'Un-Approved';
					}
					else if (trim(reponse[0])==3)
					{
						var cbo_approved_status=$('#cbo_approved_status').val();
						if(cbo_approved_status==2)
						{
							alert("Data is Approve Successfully");
							$('#cbo_approved_status').val(1);
							document.getElementById('approve1').value = 'Un-Approved';
						}
						else
						{
							alert("Data is Un-Approved Successfully");
							$('#cbo_approved_status').val(2);
							document.getElementById('approve1').value = 'Approve';
						}
					}
					
					$('#txtConfirm_id').val(trim(reponse[1]));
						set_button_status(1, permission, 'fnc_confirm_entry',1);
						//get_php_form_data($('#txt_costSheet_id').val()+'__'+$('#txtItem_id').val(),'populate_confirm_style_form_data','quick_costing_woven_controller');
					//show_list_view('','stage_data_list','stage_data_div','quick_costing_woven_controller','');
					js_set_value();
				}
			}
			release_freezing();
		}
		
		function js_set_value()
		{
			parent.emailwindow.hide();
		}
		
		function fnc_openJobPopup()
		{
			var cbo_approved_status=$('#cbo_approved_status').val();
			if(cbo_approved_status==1)
			{
				alert("This Option (QC) is Approved.");
				release_freezing();
				return;
			}
			var data=document.getElementById('cbo_buyer_id').value;
			page_link='quick_costing_woven_controller.php?action=style_tag_popup&data='+data;
			emailwindow=dhtmlmodal.open('EmailBox','iframe',page_link,'Job and Style Popup', 'width=780px, height=380px, center=1, resize=0, scrolling=0','../../');
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
					fnc_bom_data_load();
				}
			}
		}
		
		function fnc_bom_data_load()
		{
			var job_no=$("#txt_job_style").val();
			if(job_no!="")
			{
				var str_data=return_global_ajax_value( job_no, 'budgete_cost_validate', '', 'quick_costing_woven_controller');
				
				var spdata=str_data.split("##");
				var fab_cons_kg=spdata[0]; var fab_cons_mtr=spdata[1]; var fab_cons_yds=spdata[2]; var fab_amount=spdata[3]; var sp_oparation_amount=spdata[4]; var wash_amount=spdata[14]; var acc_amount=spdata[5]; var fright_amount=spdata[6]; var lab_amount=spdata[7]; var misce_amount=spdata[8]; var other_amount=spdata[9]; var comm_amount=spdata[10]; var fob_amount=spdata[11]; var cm_amount=spdata[12]; var rmg_ratio=spdata[13]; var courier_amount=spdata[15]; var comml_amount=spdata[16]; var insp_amount=spdata[17];
				
				if(qcCons_from==1)
				{
					var bomTotCost=(number_format(fab_amount,4)*1)+(number_format(sp_oparation_amount,4)*1)+(number_format(wash_amount,4)*1)+(number_format(acc_amount,4)*1)+(number_format(fright_amount,4)*1)+(number_format(lab_amount,4)*1)+(number_format(insp_amount,4)*1)+(number_format(misce_amount,4)*1)+(number_format(other_amount,4)*1)+(number_format(comm_amount,4)*1)+(number_format(courier_amount,4)*1)+(number_format(comml_amount,2)*1);
					
					var bomCost='Fab='+fab_amount+'+ SP='+sp_oparation_amount+'+ Wash='+wash_amount+'+ ACC='+acc_amount+'+ FR='+fright_amount+'+ Lab='+lab_amount+'+ Insp='+insp_amount+'+ Mis='+misce_amount+'+ Courier='+courier_amount+'+ Others='+other_amount+'+ Comml. Amt.='+comml_amount+'+ Comm='+comm_amount+'= Total='+bomTotCost;
					
					$("#txtFobDzn_bom").attr('title',bomCost);
					
					//var bom_tot_cm=(number_format(fob_amount,4)*1)-(number_format(bomTotCost,4)*1);
					var bom_tot_cm=cm_amount;
				}
				else
				{
					var bom_tot_cm=cm_amount;
				}
				
				//$("#txtFabConkg_bom").val(fab_cons_kg);
				//$("#txtFabConmtr_bom").val(fab_cons_mtr);
				$("#txtFabConyds_bom").val(fab_cons_yds);
				$("#txtFabCst_bom").val(fab_amount);
				$("#txtSpOpa_bom").val(sp_oparation_amount);
				$("#txtWash_bom").val(wash_amount);
				$("#txtAcc_bom").val(acc_amount);
				$("#txtFrightCst_bom").val(fright_amount);
				$("#txtLabCst_bom").val(lab_amount);
				$("#txtInspCst_bom").val(insp_amount);
				$("#txtMiscCst_bom").val(misce_amount);
				$("#txtCourierCst_bom").val(courier_amount);
				$("#txtOtherCst_bom").val(other_amount);
				$("#txtCommlCst_bom").val(comml_amount);
				$("#txtCommCst_bom").val(comm_amount);
				$("#txtFobDzn_bom").val(fob_amount);
				$("#txtCmCst_bom").val(bom_tot_cm);
				$("#txtPack_bom").val(rmg_ratio);
			}
		}
		
		function fnc_consamt_calculate()
		{
			var temp_id=$('#txtItem_id').val();
			var split_tmep_id=temp_id.split(','); var gitem=0;
			for(j=1; j<=split_tmep_id.length; j++)
			{
				var item_tot_amount=0; var item_tot_cm=0;
				var itm_id=trim(split_tmep_id[gitem]);
				
				var qcfabydsper=($("#tdfabyds"+itm_id).text()*1)-( ($("#tdfabyds"+itm_id).text()*1)*(($("#txtFabConydsPer_"+itm_id).val()*1)/100) );
				var qcfabcostper=($("#tdfabamt"+itm_id).text()*1)-( ($("#tdfabamt"+itm_id).text()*1)*(($("#txtFabCstPer_"+itm_id).val()*1)/100) );
				var qcspcostper=($("#tdspamt"+itm_id).text()*1)-( ($("#tdspamt"+itm_id).text()*1)*(($("#txtSpOpaPer_"+itm_id).val()*1)/100) );
				var qcwashcostper=($("#tdwamt"+itm_id).text()*1)-( ($("#tdwamt"+itm_id).text()*1)*(($("#txtWashPer_"+itm_id).val()*1)/100) );
				var qcacccostper=($("#tdaccamt"+itm_id).text()*1)-( ($("#tdaccamt"+itm_id).text()*1)*(($("#txtAccPer_"+itm_id).val()*1)/100) );
				var qcfricostper=($("#tdfriamt"+itm_id).text()*1)-( ($("#tdfriamt"+itm_id).text()*1)*(($("#txtFrightCstPer_"+itm_id).val()*1)/100) );
				var qclabcostper=($("#tdlabamt"+itm_id).text()*1)-( ($("#tdlabamt"+itm_id).text()*1)*(($("#txtLabCstPer_"+itm_id).val()*1)/100) );
				var qcinspcostper=($("#tdinspamt"+itm_id).text()*1)-( ($("#tdinspamt"+itm_id).text()*1)*(($("#txtInspCstPer_"+itm_id).val()*1)/100) );
				var qcmiscostper=($("#tdmisamt"+itm_id).text()*1)-( ($("#tdmisamt"+itm_id).text()*1)*(($("#txtMiscCstPer_"+itm_id).val()*1)/100) );
				var qccouriercostper=($("#tdcourieramt"+itm_id).text()*1)-( ($("#tdcourieramt"+itm_id).text()*1)*(($("#txtCourierCstPer_"+itm_id).val()*1)/100) );
				var qcothercostper=($("#tdothamt"+itm_id).text()*1)-( ($("#tdothamt"+itm_id).text()*1)*(($("#txtOtherCstPer_"+itm_id).val()*1)/100) );
				var qccommlcostper=($("#tdcommlamt"+itm_id).text()*1)-( ($("#tdcommlamt"+itm_id).text()*1)*(($("#txtCommlCstPer_"+itm_id).val()*1)/100) );
				var qccommcostper=($("#tdcomamt"+itm_id).text()*1)-( ($("#tdcomamt"+itm_id).text()*1)*(($("#txtCommCstPer_"+itm_id).val()*1)/100) );
				var qcfobcostper=($("#tdfobamt"+itm_id).text()*1)-( ($("#tdfobamt"+itm_id).text()*1)*(($("#txtFobDznPer_"+itm_id).val()*1)/100) );
				var qccppmper=($("#tdcppmamt"+itm_id).text()*1)-( ($("#tdcppmamt"+itm_id).text()*1)*(($("#txtCpmPer_"+itm_id).val()*1)/100) );
				var qcsmvper=($("#tdsmvamt"+itm_id).text()*1)-( ($("#tdsmvamt"+itm_id).text()*1)*(($("#txtSmvPer_"+itm_id).val()*1)/100) );
				var qccmper=($("#tdcmamt"+itm_id).text()*1)-( ($("#tdcmamt"+itm_id).text()*1)*(($("#txtCmCstPer_"+itm_id).val()*1)/100) );
				var qcpackper=($("#tdrmgpcs"+itm_id).text()*1)-( ($("#tdrmgpcs"+itm_id).text()*1)*(($("#txtPackPer_"+itm_id).val()*1)/100) );
				
				//alert(qcfabydsper)
				$("#txtFabConyds_"+itm_id).val( number_format(qcfabydsper,4,'.','') );
				$("#txtFabCst_"+itm_id).val( number_format(qcfabcostper,4,'.','') );
				$("#txtSpOpa_"+itm_id).val( number_format(qcspcostper,4,'.','') );
				$("#txtWash_"+itm_id).val( number_format(qcwashcostper,4,'.','') );
				$("#txtAcc_"+itm_id).val( number_format(qcacccostper,4,'.','') );
				$("#txtFrightCst_"+itm_id).val( number_format(qcfricostper,4,'.','') );
				$("#txtLabCst_"+itm_id).val( number_format(qclabcostper,4,'.','') );
				$("#txtInspCst_"+itm_id).val( number_format(qcinspcostper,4,'.','') );
				$("#txtMiscCst_"+itm_id).val( number_format(qcmiscostper,4,'.','') );
				$("#txtCourierCst_"+itm_id).val( number_format(qccouriercostper,4,'.','') );
				$("#txtOtherCst_"+itm_id).val( number_format(qcothercostper,4,'.','') );
				$("#txtCommlCst_"+itm_id).val( number_format(qccommlcostper,4,'.','') );
				$("#txtCommCst_"+itm_id).val( number_format(qccommcostper,4,'.','') );
				$("#txtFobDzn_"+itm_id).val( number_format(qcfobcostper,4,'.','') );
				$("#txtCpm_"+itm_id).val( number_format(qccppmper,4,'.','') );
				
				$("#txtSmv_"+itm_id).val( number_format(qcsmvper,4,'.','') );
				$("#txtCmCst_"+itm_id).val( number_format(qccmper,4,'.','') );
				$("#txtPack_"+itm_id).val( number_format(qcpackper,4,'.','') );
				
				gitem++;
			}
			fnc_total_calculate();
		}
		
		function fnc_total_calculate()
		{
			var temp_id=$('#txtItem_id').val();
			var split_tmep_id=temp_id.split(',');
			var ab=0;
			var qc_fab_kg=0; var qc_fab_mtr=0; var qc_fab_yds=0; var qc_fab_amt=0; var qc_sp_amt=0; var qc_wash_amt=0; var qc_acc_amt=0; var qc_fri_amt=0; var qc_lab_amt=0; var qc_insp_amt=0; var qc_misce_amt=0; var qc_courier_amt=0; var qc_other_amt=0; var qc_comml_amt=0; var qc_comm_amt=0; var qc_fob_amt=0; var qc_cpm_amt=0; var qc_smv_amt=0; var qc_cm_amt=0; var qc_rmg_amt=0;
			for(j=1; j<=split_tmep_id.length; j++)
			{
				var item_tot_amount=0; var item_tot_cm=0;
				var itm_id=trim(split_tmep_id[ab]);
				
				//qc_fab_kg+=$("#txtFabConkg_"+itm_id).val()*1;
				//qc_fab_mtr+=$("#txtFabConmtr_"+itm_id).val()*1;
				qc_fab_yds+=$("#txtFabConyds_"+itm_id).val()*1;
				qc_fab_amt+=$("#txtFabCst_"+itm_id).val()*1;
				qc_sp_amt+=$("#txtSpOpa_"+itm_id).val()*1;
				qc_wash_amt+=$("#txtWash_"+itm_id).val()*1;
				qc_acc_amt+=$("#txtAcc_"+itm_id).val()*1;
				qc_fri_amt+=$("#txtFrightCst_"+itm_id).val()*1;
				qc_lab_amt+=$("#txtLabCst_"+itm_id).val()*1;
				qc_insp_amt+=$("#txtInspCst_"+itm_id).val()*1;
				qc_misce_amt+=$("#txtMiscCst_"+itm_id).val()*1;
				qc_courier_amt+=$("#txtCourierCst_"+itm_id).val()*1;
				qc_other_amt+=$("#txtOtherCst_"+itm_id).val()*1;
				qc_comml_amt+=$("#txtCommlCst_"+itm_id).val()*1;
				qc_comm_amt+=$("#txtCommCst_"+itm_id).val()*1;
				qc_fob_amt+=$("#txtFobDzn_"+itm_id).val()*1;
				
				qc_cpm_amt+=$("#txtCpm_"+itm_id).val()*1;
				qc_smv_amt+=$("#txtSmv_"+itm_id).val()*1;
				
				qc_cm_amt+=$("#txtCmCst_"+itm_id).val()*1;
				qc_rmg_amt+=$("#txtPack_"+itm_id).val()*1;
				
				item_tot_amount=($("#txtFabCst_"+itm_id).val()*1)+($("#txtSpOpa_"+itm_id).val()*1)+($("#txtWash_"+itm_id).val()*1)+($("#txtAcc_"+itm_id).val()*1)+($("#txtFrightCst_"+itm_id).val()*1)+($("#txtLabCst_"+itm_id).val()*1)+($("#txtInspCst_"+itm_id).val()*1)+($("#txtMiscCst_"+itm_id).val()*1)+($("#txtCourierCst_"+itm_id).val()*1)+($("#txtOtherCst_"+itm_id).val()*1)+($("#txtCommlCst_"+itm_id).val()*1)+($("#txtCommCst_"+itm_id).val()*1);
				
				var itemCost='Fab='+$("#txtFabCst_"+itm_id).val()+'+ SP='+$("#txtSpOpa_"+itm_id).val()+'+ Wash='+$("#txtWash_"+itm_id).val()+'+ ACC='+$("#txtAcc_"+itm_id).val()+'+ FR='+$("#txtFrightCst_"+itm_id).val()+'+ Lab='+$("#txtLabCst_"+itm_id).val()+'+ Insp='+$("#txtInspCst_"+itm_id).val()+'+ Mis='+$("#txtMiscCst_"+itm_id).val()+'+ Courier='+$("#txtCourierCst_"+itm_id).val()+'+ Others='+$("#txtOtherCst_"+itm_id).val()+'+ Comml='+$("#txtCommlCst_"+itm_id).val()+'+ Comm='+$("#txtCommCst_"+itm_id).val()+'= Total='+item_tot_amount;
				
				item_tot_cm=($("#txtFobDzn_"+itm_id).val()*1)-item_tot_amount;
				
				$('#txtFobDzn_'+itm_id).attr('title',itemCost);
				
				//$("#txtCmCst_"+itm_id).val( number_format(item_tot_cm,4,'.',''))
				
				ab++;
			}
			
			//$("#txtFabConkg_qc").val( number_format(qc_fab_kg,4,'.','') );
			//$("#txtFabConmtr_qc").val( number_format(qc_fab_mtr,4,'.','') );
			$("#txtFabConyds_qc").val( number_format(qc_fab_yds,4,'.','') );
			$("#txtFabCst_qc").val( number_format(qc_fab_amt,4,'.','') );
			$("#txtSpOpa_qc").val( number_format(qc_sp_amt,4,'.','') );
			$("#txtWash_qc").val( number_format(qc_wash_amt,4,'.','') );
			$("#txtAcc_qc").val( number_format(qc_acc_amt,4,'.','') );
			$("#txtFrightCst_qc").val( number_format(qc_fri_amt,4,'.','') );
			$("#txtLabCst_qc").val( number_format(qc_lab_amt,4,'.','') );
			$("#txtInspCst_qc").val( number_format(qc_insp_amt,4,'.','') );
			$("#txtMiscCst_qc").val( number_format(qc_misce_amt,4,'.','') );
			$("#txtCourierCst_qc").val( number_format(qc_courier_amt,4,'.','') );
			$("#txtOtherCst_qc").val( number_format(qc_other_amt,4,'.','') );
			$("#txtCommlCst_qc").val( number_format(qc_comml_amt,2,'.','') );
			$("#txtCommCst_qc").val( number_format(qc_comm_amt,4,'.','') );
			$("#txtFobDzn_qc").val( number_format(qc_fob_amt,4,'.','') );
			
			$("#txtCpm_qc").val( number_format(qc_cpm_amt,4,'.','') );
			$("#txtSmv_qc").val( number_format(qc_smv_amt,4,'.','') );
			
			$("#txtPack_qc").val( number_format(qc_rmg_amt,4,'.','') );
			
			//var total_amount=qc_fab_amt+qc_sp_amt+qc_acc_amt+qc_fri_amt+qc_lab_amt+qc_misce_amt+qc_other_amt+qc_comm_amt;
			//var cal_cm=qc_fob_amt-total_amount;
			$("#txtCmCst_qc").val( number_format(qc_cm_amt,4,'.','') );
		}
		
		function fnc_select()
		{
			$(document).ready(function() {
				$("input:text").focus(function() { $(this).select(); } );
			});
		}
		
		function fnc_confirm()
		{
			var job_no=$('#txt_job_style').val();
			
			if(job_no=="")
			{
				alert("Please Add Job no with this option.");
				return;
			}
			else
			{
				fnc_confirm_entry(3);
			}
		}
		
		function fnc_cppm_cal(item_id)
		{
			var txtSmv=$("#txtSmv_"+item_id).val()*1;
			var txtCm=$("#txtCmCst_"+item_id).val()*1;
			
			var cppm=( txtCm/txtSmv);
			var cppm_nf=number_format((cppm/12),4,'.','');
			if(cppm_nf=="nan") cppm_nf=0;
			$("#txtCpm_"+item_id).val( cppm_nf );
			
			fnc_total_calculate();
		}
		
		function openmypage_unapprove_request()
		{
			var costSheet_id=document.getElementById('txt_costSheet_id').value;
			var txt_un_appv_request=document.getElementById('txt_un_appv_request').value;
			var data=costSheet_id+"_"+txt_un_appv_request;
			var title = 'Un Approval Request';
			var page_link = 'quick_costing_woven_controller.php?data='+data+'&action=unapp_request_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','../../');
			emailwindow.onclose=function()
			{
				var unappv_request=this.contentDoc.getElementById("hidden_appv_cause");
				$('#txt_un_appv_request').val(unappv_request.value);
			}
		}
		
		function fncqclimit(str)
		{
			//alert(str)
			var strdata=str.split("_");
			var item_id=strdata[0];
			var validatetype=strdata[1];
			
			//var tdfabkg=$('#tdfabkg'+item_id).text()*1;
			//var tdfabmtr=$('#tdfabmtr'+item_id).text()*1;
			var tdfabyds=$('#tdfabyds'+item_id).text()*1;
			var tdfabamt=$('#tdfabamt'+item_id).text()*1;
			var tdspamt=$('#tdspamt'+item_id).text()*1;
			var tdwamt=$('#tdwamt'+item_id).text()*1;
			var tdaccamt=$('#tdaccamt'+item_id).text()*1;
			var tdfriamt=$('#tdfriamt'+item_id).text()*1;
			var tdlabamt=$('#tdlabamt'+item_id).text()*1;
			var tdinspamt=$('#tdinspamt'+item_id).text()*1;
			var tdmisamt=$('#tdmisamt'+item_id).text()*1;
			var tdcourieramt=$('#tdcourieramt'+item_id).text()*1;
			var tdothamt=$('#tdothamt'+item_id).text()*1;
			var tdcommlamt=$('#tdcommlamt'+item_id).text()*1;
			var tdcomamt=$('#tdcomamt'+item_id).text()*1;
			var tdfobamt=$('#tdfobamt'+item_id).text()*1;
			var tdcppmamt=$('#tdcppmamt'+item_id).text()*1;
			var tdsmvamt=$('#tdsmvamt'+item_id).text()*1;
			var tdcmamt=$('#tdcmamt'+item_id).text()*1;
			var tdrmgpcs=$('#tdrmgpcs'+item_id).text()*1;
			
			if(qcCons_from==2)
			{
				/*if( validatetype==1 && tdfabkg < ($("#txtFabConkg_"+item_id).val()*1) )
				{
					alert("QC BOM Limit (Fab. Cons. Kg) is Greater Than QC!!!");
					$("#txtFabConkg_"+item_id).val(tdfabkg);
					//return;
				}
				if( validatetype==2 && tdfabmtr < ($("#txtFabConmtr_"+item_id).val()*1) )
				{
					alert("QC BOM Limit (Fab. Cons. Mtr) is Greater Than QC!!!");
					$("#txtFabConmtr_"+item_id).val(tdfabmtr);
					//return;
				}*/
				if( validatetype==3 && tdfabyds < ($("#txtFabConyds_"+item_id).val()*1) )
				{
					alert("QC BOM Limit (Fab. Cons. Yds) is Greater Than QC!!!");
					$("#txtFabConyds_"+item_id).val(tdfabyds);
					//return;
				}
				if( validatetype==4 && tdfabamt < ($("#txtFabCst_"+item_id).val()*1) )
				{
					alert("QC BOM Limit (Fab. Amt.) is Greater Than QC!!!");
					$("#txtFabCst_"+item_id).val(tdfabamt);
					//return;
				}
				if( validatetype==5 && tdspamt < ($("#txtSpOpa_"+item_id).val()*1) )
				{
					alert("QC BOM Limit (Special Opera.) is Greater Than QC!!!");
					$("#txtSpOpa_"+item_id).val(tdspamt);
					//return;
				}
				if( validatetype==6 && tdaccamt < ($("#txtAcc_"+item_id).val()*1) )
				{
					alert("QC BOM Limit (Access.) is Greater Than QC!!!");
					$("#txtAcc_"+item_id).val(tdaccamt);
					//return;
				}
				if( validatetype==7 && tdfriamt < ($("#txtFrightCst_"+item_id).val()*1) )
				{
					alert("QC BOM Limit (Frieght Cost) is Greater Than QC!!!");
					$("#txtFrightCst_"+item_id).val(tdfriamt);
					//return;
				}
				if( validatetype==8 && tdlabamt < ($("#txtLabCst_"+item_id).val()*1) )
				{
					alert("QC BOM Limit (Lab - Test) is Greater Than QC!!!");
					$("#txtLabCst_"+item_id).val(tdlabamt);
					//return;
				}
				if( validatetype==9 && tdmisamt < ($("#txtMiscCst_"+item_id).val()*1) )
				{
					alert("QC BOM Limit (Misce.) is Greater Than QC!!!");
					$("#txtMiscCst_"+item_id).val(tdmisamt);
					//return;
				}
				if( validatetype==10 && tdothamt < ($("#txtOtherCst_"+item_id).val()*1) )
				{
					alert("QC BOM Limit (Other Cost) is Greater Than QC!!!");
					$("#txtOtherCst_"+item_id).val(tdothamt);
					//return;
				}
				if( validatetype==11 && tdcomamt < ($("#txtCommCst_"+item_id).val()*1) )
				{
					alert("QC BOM Limit (Commis.) is Greater Than QC!!!");
					$("#txtCommCst_"+item_id).val(tdcomamt);
					//return;
				}
				if( validatetype==12 && tdfobamt < ($("#txtFobDzn_"+item_id).val()*1) )
				{
					alert("QC BOM Limit (FOB ($/DZN)) is Greater Than QC!!!");
					$("#txtFobDzn_"+item_id).val(tdfobamt);
					//return;
				}
				if( validatetype==13 && tdcppmamt < ($("#txtCpm_"+item_id).val()*1) )
				{
					alert("QC BOM Limit (CPPM) is Greater Than QC!!!");
					$("#txtCpm_"+item_id).val(tdcppmamt);
					//return;
				}
				if( validatetype==14 && tdsmvamt < ($("#txtSmv_"+item_id).val()*1) )
				{
					alert("QC BOM Limit (SMV) is Greater Than QC!!!");
					$("#txtSmv_"+item_id).val(tdsmvamt);
					//return;
				}
				if( validatetype==15 && tdcmamt < ($("#txtCmCst_"+item_id).val()*1) )
				{
					alert("QC BOM Limit (CM) is Greater Than QC!!!");
					$("#txtCmCst_"+item_id).val(tdcmamt);
					//return;
				}
				if( validatetype==16 && tdrmgpcs < ($("#txtPack_"+item_id).val()*1) )
				{
					alert("QC BOM Limit (RMG Qty(Pcs)) is Greater Than QC!!!");
					$("#txtPack_"+item_id).val(tdrmgpcs);
					//return;
				}
				if( validatetype==17 && tdwamt < ($("#txtWash_"+item_id).val()*1) )
				{
					alert("QC BOM Limit (Wash) is Greater Than QC!!!");
					$("#txtWash_"+item_id).val(tdwamt);
					//return;
				}
				if( validatetype==18 && tdcourieramt < ($("#txtCourierCst_"+item_id).val()*1) )
				{
					alert("QC BOM Limit (Courier Cost) is Greater Than QC!!!");
					$("#txtCourierCst_"+item_id).val(tdcourieramt);
					//return;
				}
				//alert(validatetype+'='+tdcommlamt+'='+($("#txtCommlCst_"+item_id).val()*1));
				if( validatetype==19 && tdcommlamt < ($("#txtCommlCst_"+item_id).val()*1) )
				{
					alert("QC BOM Limit (Commercial Cost) is Greater Than QC!!!");
					$("#txtCommlCst_"+item_id).val(tdcommlamt);
					//return;
				}
				if( validatetype==20 && tdinspamt < ($("#txtInspCst_"+item_id).val()*1) )
				{
					alert("QC BOM Limit (Inspection Cost) is Greater Than QC!!!");
					$("#txtInspCst_"+item_id).val(tdinspamt);
					//return;
				}
				fnc_total_calculate();
				return;
			}
		}
		
		function mail_send(){
	    if (form_validation('cbo_ready_approve','Ready App Yes')==false)
		{
			return;
		}
		else
		{
			var report_title="";
			var data=<?=$qc_no;?>+'*'+<?=$cost_sheet;?>+'*'+report_title;
			window.open("quick_costing_woven_controller.php?data=" + data+'&action=quick_costing_print'+'&is_mail_send=1', true );
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
                    <td width="90"><strong>Buyer</strong><input style="width:40px;" type="hidden" class="text_boxes" name="txt_costSheet_id" id="txt_costSheet_id" value="<? echo $qc_no; ?>" /><input style="width:40px;" type="hidden" class="text_boxes" name="txtConfirm_id" id="txtConfirm_id" value="" /><input style="width:40px;" type="hidden" class="text_boxes" name="txtItem_id" id="txtItem_id" value="<? echo $sql_data[0][csf('lib_item_id')]; ?>" /></td>
                    <td width="120"><? echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-Select Buyer-", $sql_data[0][csf('buyer_id')], "load_drop_down( 'quick_costing_woven_controller', this.value, 'load_drop_down_season_conf', 'season_conf_td'); load_drop_down( 'quick_costing_woven_controller',this.value, 'load_drop_down_sub_depConf', 'subConf_td' );",1 ); ?></td>
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
                    <td>&nbsp;&nbsp;<strong>Confirm Style</strong></td>
                    <td><input style="width:90px;" type="text" class="text_boxes" name="txt_confirm_style" id="txt_confirm_style" value="<? echo $sql_data[0][csf('style_ref')]; ?>" <? echo $disable; ?> /></td>
                    <td>&nbsp;&nbsp;<strong>Order Qty.</strong></td>
                    <td><input style="width:90px;" type="text" class="text_boxes_numeric" name="txt_order_qty" id="txt_order_qty" value="<? echo $sql_data[0][csf('offer_qty')]; ?>" <? echo $disable; ?> /></td>
                    <td>&nbsp;&nbsp;<strong>Confirm FOB</strong></td>
                    <td><input style="width:90px;" type="text" class="text_boxes_numeric" name="txt_confirm_fob" id="txt_confirm_fob" value="<? echo $sql_summ[0][csf('tot_fob_cost')]; ?>" <? echo $disable; ?> /></td>
                </tr>
                <tr>
                	<td><strong>Ship Date</strong></td>
                    <td><input style="width:110px;" type="text" class="datepicker" name="txt_ship_date" id="txt_ship_date" value="<? echo change_date_format($sql_data[0][csf('delivery_date')]); ?>" readonly <? echo $disable; ?>/></td>
                    <td>&nbsp;&nbsp;<strong>Job No</strong></td>
                    <td><input style="width:90px;" type="text" class="text_boxes" name="txt_job_style" id="txt_job_style" placeholder="Browse Job" onDblClick="fnc_openJobPopup();" readonly /><input style="width:40px;" type="hidden" class="text_boxes" name="txt_job_id" id="txt_job_id" /></td>
                    <td>&nbsp;&nbsp;<strong>Style Ref.</strong></td>
                    <td><input style="width:90px;" type="text" class="text_boxes" name="txt_style_job" id="txt_style_job" disabled /></td>
                    <td>&nbsp;&nbsp;<strong>Approved</strong></td>
                	<td><? echo create_drop_down( "cbo_approved_status", 100, $yes_no,"", 0, "", 2, "",1,"" ); ?></td> 
                    <td>&nbsp;</td>
                </tr>
                <tr>
                	<td><strong>Ready To App.</strong></td>
                    <td><? echo create_drop_down( "cbo_ready_approve", 100, $yes_no,"", 1, "--", 0, "","","" ); ?></td>
                	<td colspan="3"><strong>Un-approve Request</strong>&nbsp;&nbsp;
                        <Input name="txt_un_appv_request" class="text_boxes" placeholder="Double Click" ID="txt_un_appv_request" style="width:110px;" onClick="openmypage_unapprove_request();" readonly disabled>
                    </td>
                    <td colspan="3"><strong>Refusing Cause</strong>&nbsp;&nbsp;
                        <Input name="txt_notun_appv_request" class="text_boxes" placeholder="Display" ID="txt_notun_appv_request" style="width:150px;" title="<?=$unapp_cause; ?>" value="<?=$unapp_cause; ?>" readonly disabled><!-- onClick="openmypage_notunapprove_request();"-->
                </tr>
            </table>
            <table width="400" cellspacing="0" class="" border="0">
                <tr>
                    <td align="center" width="100%" class="button_container">
						<? echo load_submit_buttons( $permission, "fnc_confirm_entry", 0,0 ,"reset_form('confirmStyle_1','','','','')",0,''); ?>
                        <input type="button" value="Approved" name="approve" onClick="fnc_confirm_entry(3)" style="width:80px; display:<? echo $admin_or_leader; ?>" id="approve1" class="formbutton"><input type="button" class="formbutton" value="Close" style="width:80px" onClick="js_set_value();"/>
                        <input style="width:80px;" type="button" id="copy_btn" class="formbutton" value="Mail Send" onClick="mail_send();" />
                    </td> 
                </tr>
            </table>
            <div id="confirm_data_div">
            <table width="1050" cellspacing="0" border="1" rules="all" class="rpt_table">
        	<thead>
            	<th width="100">Item</th>
                <th width="50">Fab. Cons. Yds</th>
                <th width="50">Fab. Amount</th>
                <th width="50">Special Opera.</th>
                <th width="50">Wash</th>
                <th width="50">Access.</th>
                <th width="50">Frieght Cost</th>
                <th width="50">Lab - Test</th>
                <th width="50">Inspection Cost</th>
                <th width="50">Misce.</th>
                <th width="50">Courier Cost</th>
                <th width="50">Other Cost</th>
                <th width="50">Comml. Cost</th>
                <th width="50">Commis. Cost</th>
                <th width="50">FOB ($/PCS)</th>
                <th width="50" title="((CPM*100)/Efficiency)">CPPM</th>
                <th width="50">SMV</th>
                <th width="50">CM</th>
                <th>RMG Qty(Pcs)</th>
            </thead>
			<?
            $sql_item_summ="select item_id, fabric_cost, sp_operation_cost, accessories_cost, cpm, smv, efficiency, cm_cost, frieght_cost, courier_cost, lab_test_cost, miscellaneous_cost, other_cost, commercial_cost, commission_cost, fob_pcs, wash_cost, inspection_cost  from qc_item_cost_summary where mst_id='$qc_no' and status_active=1 and is_deleted=0";
            $sql_result_item_summ=sql_select($sql_item_summ); $z=1;
            foreach($sql_result_item_summ as $rowItemSumm)
            {
				if ($z%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if ($z%2==0) $bgcolorN="#E9F50F"; else $bgcolorN="#D078F6";
				//if ($z%2==0) $bgcolorP="#EADFC8"; else $bgcolorP="#D6BF91";
				$bgcolorP=$bgcolorN;
				$cppm=0;
				if($rowItemSumm[csf("efficiency")]!=0 && $rowItemSumm[csf("cpm")]!=0) $cppm=(($rowItemSumm[csf("cpm")]*100)/$rowItemSumm[csf("efficiency")]);
				
				if($cppm=="nan") $cppm=0;
				$item_id=0;
				$item_id=$rowItemSumm[csf("item_id")];
				
                ?>
                <tr id="trVal_<?=$z; ?>" bgcolor="<?=$bgcolor; ?>">
                    <td style="word-break:break-all"><?=$lib_temp_arr[$item_id]; ?></td>
                    <td align="right" id="tdfabyds<?=$item_id; ?>"><? echo number_format($item_wise_cons_arr[$item_id]['qty_yds'],4); ?></td>
                    <td align="right" id="tdfabamt<?=$item_id; ?>"><? echo number_format($rowItemSumm[csf("fabric_cost")],4); ?></td>
                    <td align="right" id="tdspamt<?=$item_id; ?>"><? echo number_format($rowItemSumm[csf("sp_operation_cost")],4); ?></td>
                    <td align="right" id="tdwamt<?=$item_id; ?>"><? echo number_format($rowItemSumm[csf("wash_cost")],4); ?></td>
                    <td align="right" id="tdaccamt<?=$item_id; ?>"><? echo number_format($rowItemSumm[csf("accessories_cost")],4); ?></td>
                    <td align="right" id="tdfriamt<?=$item_id; ?>"><? echo number_format($rowItemSumm[csf("frieght_cost")],4); ?></td>
                    <td align="right" id="tdlabamt<?=$item_id; ?>"><? echo number_format($rowItemSumm[csf("lab_test_cost")],4); ?></td>
                    <td align="right" id="tdinspamt<?=$item_id; ?>"><? echo number_format($rowItemSumm[csf("inspection_cost")],4); ?></td>
                    <td align="right" id="tdmisamt<?=$item_id; ?>"><? echo number_format($rowItemSumm[csf("miscellaneous_cost")],4); ?></td>
                    <td align="right" id="tdcourieramt<?=$item_id; ?>"><?=number_format($rowItemSumm[csf("courier_cost")],4); ?></td>
                    <td align="right" id="tdothamt<?=$item_id; ?>"><? echo number_format($rowItemSumm[csf("other_cost")],4); ?></td>
                    <td align="right" id="tdcommlamt<?=$item_id; ?>"><? echo number_format($rowItemSumm[csf("commercial_cost")],4); ?></td>
                    <td align="right" id="tdcomamt<?=$item_id; ?>"><? echo number_format($rowItemSumm[csf("commission_cost")],4); ?></td>
                    <td align="right" id="tdfobamt<?=$item_id; ?>"><? echo number_format($rowItemSumm[csf("fob_pcs")],4); ?></td>
                    
                    <td align="right" id="tdcppmamt<?=$item_id; ?>" title="((CPM*100)/Efficiency)"><? if($cppm==0) echo "0"; else echo number_format($cppm,4); ?></td>
                    <td align="right" id="tdsmvamt<?=$item_id; ?>"><? echo number_format($rowItemSumm[csf("smv")],4); ?></td>
                    
                    <td align="right" id="tdcmamt<?=$item_id; ?>"><? echo number_format($rowItemSumm[csf("cm_cost")],4); ?></td>
                    <td align="right" id="tdrmgpcs<?=$item_id; ?>">&nbsp;<? //echo number_format($rowItemSumm[csf("sp_operation_cost")],4); ?></td>
                </tr>
                
                <tr id="trper_<?=$z; ?>" bgcolor="<?=$bgcolorP; ?>">
                    <td title="QC % Limit">QC % Limit</td>
                    <td><input style="width:38px; background-color:<?=$bgcolorP; ?>" type="text" class="text_boxes_numeric" name="txtFabConydsPer_<?=$item_id; ?>" id="txtFabConydsPer_<?=$item_id; ?>" onChange="fnc_consamt_calculate();" onBlur="fncqclimit('<?=$item_id.'_3'; ?>');" <?=$disable; ?>/></td>
                    <td><input style="width:38px; background-color:<?=$bgcolorP; ?>" type="text" class="text_boxes_numeric" name="txtFabCstPer_<?=$item_id; ?>" id="txtFabCstPer_<?=$item_id; ?>" onChange="fnc_consamt_calculate();" onBlur="fncqclimit('<?=$item_id.'_4'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px; background-color:<?=$bgcolorP; ?>" type="text" class="text_boxes_numeric" name="txtSpOpaPer_<?=$item_id; ?>" id="txtSpOpaPer_<?=$item_id; ?>" onChange="fnc_consamt_calculate();" onBlur="fncqclimit('<?=$item_id.'_5'; ?>');" <?=$disable; ?> /></td>
                    
                    <td><input style="width:38px; background-color:<?=$bgcolorP; ?>" type="text" class="text_boxes_numeric" name="txtWashPer_<?=$item_id; ?>" id="txtWashPer_<?=$item_id; ?>" onChange="fnc_consamt_calculate();" onBlur="fncqclimit('<?=$item_id.'_17'; ?>');" <?=$disable; ?> /></td>
                    
                    <td><input style="width:38px; background-color:<?=$bgcolorP; ?>" type="text" class="text_boxes_numeric" name="txtAccPer_<?=$item_id; ?>" id="txtAccPer_<?=$item_id; ?>" onChange="fnc_consamt_calculate();" onBlur="fncqclimit('<?=$item_id.'_6'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px; background-color:<?=$bgcolorP; ?>" type="text" class="text_boxes_numeric" name="txtFrightCstPer_<?=$item_id; ?>" id="txtFrightCstPer_<?=$item_id; ?>" onChange="fnc_consamt_calculate();" onBlur="fncqclimit('<?=$item_id.'_7'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px; background-color:<?=$bgcolorP; ?>" type="text" class="text_boxes_numeric" name="txtLabCstPer_<?=$item_id; ?>" id="txtLabCstPer_<?=$item_id; ?>" onChange="fnc_consamt_calculate();" onBlur="fncqclimit('<?=$item_id.'_8'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px; background-color:<?=$bgcolorP; ?>" type="text" class="text_boxes_numeric" name="txtInspCstPer_<?=$item_id; ?>" id="txtInspCstPer_<?=$item_id; ?>" onChange="fnc_consamt_calculate();" onBlur="fncqclimit('<?=$item_id.'_20'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px; background-color:<?=$bgcolorP; ?>" type="text" class="text_boxes_numeric" name="txtMiscCstPer_<?=$item_id; ?>" id="txtMiscCstPer_<?=$item_id; ?>" onChange="fnc_consamt_calculate();" onBlur="fncqclimit('<?=$item_id.'_9'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px; background-color:<?=$bgcolorP; ?>" type="text" class="text_boxes_numeric" name="txtCourierCstPer_<?=$item_id; ?>" id="txtCourierCstPer_<?=$item_id; ?>" onChange="fnc_consamt_calculate();" onBlur="fncqclimit('<?=$item_id.'_18'; ?>');" <?=$disable; ?> /></td>
                    
                    <td><input style="width:38px; background-color:<?=$bgcolorP; ?>" type="text" class="text_boxes_numeric" name="txtOtherCstPer_<?=$item_id; ?>" id="txtOtherCstPer_<?=$item_id; ?>" onChange="fnc_consamt_calculate();" onBlur="fncqclimit('<?=$item_id.'_10'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px; background-color:<?=$bgcolorP; ?>" type="text" class="text_boxes_numeric" name="txtCommlCstPer_<?=$item_id; ?>" id="txtCommlCstPer_<?=$item_id; ?>" onChange="fnc_consamt_calculate();" onBlur="fncqclimit('<?=$item_id.'_19'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px; background-color:<?=$bgcolorP; ?>" type="text" class="text_boxes_numeric" name="txtCommCstPer_<?=$item_id; ?>" id="txtCommCstPer_<?=$item_id; ?>" onChange="fnc_consamt_calculate();" onBlur="fncqclimit('<?=$item_id.'_11'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px; background-color:<?=$bgcolorP; ?>" type="text" class="text_boxes_numeric" name="txtFobDznPer_<?=$item_id; ?>" id="txtFobDznPer_<?=$item_id; ?>" onChange="fnc_consamt_calculate();" onBlur="fncqclimit('<?=$item_id.'_12'; ?>');" <?=$disable; ?> /></td>
                    
                    <td title="((CPM*100)/Efficiency)"><input style="width:38px; background-color:<?=$bgcolorP; ?>" type="text" class="text_boxes_numeric" name="txtCpmPer_<?=$item_id; ?>" id="txtCpmPer_<?=$item_id; ?>" value="<? if($cppm==0) echo "0"; else echo number_format($cppm,4); ?>" onChange="fnc_consamt_calculate();" onBlur="fncqclimit('<?=$item_id.'_13'; ?>');" disabled <? //echo $disable; ?> /></td>
                    <td><input style="width:38px; background-color:<?=$bgcolorP; ?>" type="text" class="text_boxes_numeric" name="txtSmvPer_<?=$item_id; ?>" id="txtSmvPer_<?=$item_id; ?>" onChange="fnc_consamt_calculate();" onBlur="fnc_cppm_cal(<?=$item_id; ?>); fncqclimit('<?=$item_id.'_14'; ?>');" <?=$disable; ?> /></td>
                    
                    <td><input style="width:38px; background-color:<?=$bgcolorP; ?>" type="text" class="text_boxes_numeric" name="txtCmCstPer_<?=$item_id; ?>" id="txtCmCstPer_<?=$item_id; ?>" onChange="fnc_consamt_calculate();" onBlur="fncqclimit('<?=$item_id.'_15'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:35px; background-color:<?=$bgcolorP; ?>" type="text" class="text_boxes_numeric" name="txtPackPer_<?=$item_id; ?>" id="txtPackPer_<?=$item_id; ?>" onChange="fnc_consamt_calculate();" onBlur="fncqclimit('<?=$item_id.'_16'; ?>');" <?=$disable; ?> />&nbsp;</td>
                </tr>
                
                
                <tr id="tr_<?=$z; ?>" bgcolor="<?=$bgcolorN; ?>">
                    <td title="QC BOM Limit">Budget Limit<input style="width:40px;" type="hidden" name="txtitemid_<?=$item_id; ?>" id="txtitemid_<?=$item_id; ?>" value="<?=$item_id; ?>" /><input style="width:40px;" type="hidden" name="txtdtlsupid_<?=$item_id; ?>" id="txtdtlsupid_<?=$item_id; ?>" value="" /></td>
                    <td><input style="width:38px; background-color:<?=$bgcolorN; ?>" type="text" class="text_boxes_numeric" name="txtFabConyds_<?=$item_id; ?>" id="txtFabConyds_<?=$item_id; ?>" value="<?=number_format($item_wise_cons_arr[$item_id]['qty_yds'],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_3'; ?>');" <?=$disable; ?>/></td>
                    <td><input style="width:38px; background-color:<?=$bgcolorN; ?>" type="text" class="text_boxes_numeric" name="txtFabCst_<?=$item_id; ?>" id="txtFabCst_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("fabric_cost")],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_4'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px; background-color:<?=$bgcolorN; ?>" type="text" class="text_boxes_numeric" name="txtSpOpa_<?=$item_id; ?>" id="txtSpOpa_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("sp_operation_cost")],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_5'; ?>');" <?=$disable; ?> /></td>
                    
                    <td><input style="width:38px; background-color:<?=$bgcolorN; ?>" type="text" class="text_boxes_numeric" name="txtWash_<?=$item_id; ?>" id="txtWash_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("wash_cost")],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_17'; ?>');" <?=$disable; ?> /></td>
                    
                    <td><input style="width:38px; background-color:<?=$bgcolorN; ?>" type="text" class="text_boxes_numeric" name="txtAcc_<?=$item_id; ?>" id="txtAcc_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("accessories_cost")],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_6'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px; background-color:<?=$bgcolorN; ?>" type="text" class="text_boxes_numeric" name="txtFrightCst_<?=$item_id; ?>" id="txtFrightCst_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("frieght_cost")],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_7'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px; background-color:<?=$bgcolorN; ?>" type="text" class="text_boxes_numeric" name="txtLabCst_<?=$item_id; ?>" id="txtLabCst_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("lab_test_cost")],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_8'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px; background-color:<?=$bgcolorN; ?>" type="text" class="text_boxes_numeric" name="txtInspCst_<?=$item_id; ?>" id="txtInspCst_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("inspection_cost")],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_20'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px; background-color:<?=$bgcolorN; ?>" type="text" class="text_boxes_numeric" name="txtMiscCst_<?=$item_id; ?>" id="txtMiscCst_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("miscellaneous_cost")],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_9'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px; background-color:<?=$bgcolorN; ?>" type="text" class="text_boxes_numeric" name="txtCourierCst_<?=$item_id; ?>" id="txtCourierCst_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("courier_cost")],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_18'; ?>');" <?=$disable; ?> /></td>
                    
                    <td><input style="width:38px; background-color:<?=$bgcolorN; ?>" type="text" class="text_boxes_numeric" name="txtOtherCst_<?=$item_id; ?>" id="txtOtherCst_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("other_cost")],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_10'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px; background-color:<?=$bgcolorN; ?>" type="text" class="text_boxes_numeric" name="txtCommlCst_<?=$item_id; ?>" id="txtCommlCst_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("commercial_cost")],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_19'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px; background-color:<?=$bgcolorN; ?>" type="text" class="text_boxes_numeric" name="txtCommCst_<?=$item_id; ?>" id="txtCommCst_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("commission_cost")],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_11'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px; background-color:<?=$bgcolorN; ?>" type="text" class="text_boxes_numeric" name="txtFobDzn_<?=$item_id; ?>" id="txtFobDzn_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("fob_pcs")],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_12'; ?>');" <?=$disable; ?> /></td>
                    
                    <td title="((CPM*100)/Efficiency)"><input style="width:38px; background-color:<?=$bgcolorN; ?>" type="text" class="text_boxes_numeric" name="txtCpm_<?=$item_id; ?>" id="txtCpm_<?=$item_id; ?>" value="<? if($cppm==0) echo "0"; else echo number_format($cppm,4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_13'; ?>');" disabled <? //echo $disable; ?> /></td>
                    <td><input style="width:38px; background-color:<?=$bgcolorN; ?>" type="text" class="text_boxes_numeric" name="txtSmv_<?=$item_id; ?>" id="txtSmv_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("smv")],4); ?>" onChange="fnc_total_calculate();" onBlur="fnc_cppm_cal(<?=$item_id; ?>); fncqclimit('<?=$item_id.'_14'; ?>');" <?=$disable; ?> /></td>
                    
                    <td><input style="width:38px; background-color:<?=$bgcolorN; ?>" type="text" class="text_boxes_numeric" name="txtCmCst_<?=$item_id; ?>" id="txtCmCst_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("cm_cost")],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_15'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:35px; background-color:<?=$bgcolorN; ?>" type="text" class="text_boxes_numeric" name="txtPack_<?=$item_id; ?>" id="txtPack_<?=$item_id; ?>" value="" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_16'; ?>');" <?=$disable; ?> />&nbsp;</td>
                </tr>
                <?
				$z++;
            }
			$sql="select fab_cons_kg, fab_cons_yds, fab_amount, sp_oparation_amount, acc_amount, fright_amount, lab_amount, misce_amount, other_amount, commercial_cost, comm_amount, fob_amount, cm_amount, wash_amount, inspection_cost, rmg_ratio from qc_confirm_dtls where cost_sheet_id='$cost_sheet_id' and status_active=1 and is_deleted=0";
			$dataArr=sql_select($sql);
            ?>
        </table>
        
        <table width="1050" cellspacing="0" border="1" rules="all" class="rpt_table">
        	<tr id="tr_qc" bgcolor="#CCFFCC">
                <td width="100" title="QC Limit Total"><font color="#0000FF" style="font-size:11px; font-weight:100">Budget Limit Total</font></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabConyds_qc" id="txtFabConyds_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabCst_qc" id="txtFabCst_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtSpOpa_qc" id="txtSpOpa_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtWash_qc" id="txtWash_qc" value="" disabled /></td>
                
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtAcc_qc" id="txtAcc_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFrightCst_qc" id="txtFrightCst_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtLabCst_qc" id="txtLabCst_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtInspCst_qc" id="txtInspCst_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtMiscCst_qc" id="txtMiscCst_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtCourierCst_qc" id="txtCourierCst_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtOtherCst_qc" id="txtOtherCst_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtCommlCst_qc" id="txtCommlCst_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtCommCst_qc" id="txtCommCst_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFobDzn_qc" id="txtFobDzn_qc" value="" disabled /></td>
                
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtCpm_qc" id="txtCpm_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtSmv_qc" id="txtSmv_qc" value="" disabled /></td>
                
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtCmCst_qc" id="txtCmCst_qc" value="" disabled /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtPack_qc" id="txtPack_qc" value="" disabled />&nbsp;</td>
            </tr>
        	<tr id="tr_bom" bgcolor="#CCCCCC">
                <td width="100"><font color="#FF0000">Current BOM</font></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabConyds_bom" id="txtFabConyds_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabCst_bom" id="txtFabCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtSpOpa_bom" id="txtSpOpa_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtWash_bom" id="txtWash_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtAcc_bom" id="txtAcc_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFrightCst_bom" id="txtFrightCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtLabCst_bom" id="txtLabCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtInspCst_bom" id="txtInspCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtMiscCst_bom" id="txtMiscCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtCourierCst_bom" id="txtCourierCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtOtherCst_bom" id="txtOtherCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtCommlCst_bom" id="txtCommlCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtCommCst_bom" id="txtCommCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFobDzn_bom" id="txtFobDzn_bom" value="" readonly /></td>
                
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtCpm_bom" id="txtCpm_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtSmv_bom" id="txtSmv_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtCmCst_bom" id="txtCmCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtPack_bom" id="txtPack_bom" value="" readonly />&nbsp;</td>
            </tr>
        </table>
        </div>
        </form>
	</div>
    </body> 
    <script>get_php_form_data($('#txt_costSheet_id').val()+'__'+$('#txtItem_id').val(),'populate_confirm_style_form_data','quick_costing_woven_controller'); fnc_bom_data_load(); fnc_total_calculate(); fnc_select();</script>          
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();	
}

if($action=="save_update_delete_confirm_style")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$user_id=$_SESSION['logic_erp']['user_id'];
	
	if ($operation!=3)
	{
		$approved=0;
		$sql=sql_select("select approved from qc_mst where qc_no=$txt_costSheet_id");
		foreach($sql as $row){
			$approved=$row[csf('approved')];
		}
		if($approved==3) $approved=1; else $approved=$approved;
		
		if($approved==1){
			echo "approvedQc**".str_replace("'","",$txt_costSheet_id);
			disconnect($con);die;
		}
		
		$insert_user_id=return_field_value("inserted_by","qc_mst","qc_no=".$txt_costSheet_id."","inserted_by");
		$updated_by_user=return_field_value("updated_by","qc_mst","qc_no=".$txt_costSheet_id."","updated_by");
		//echo $insert_user_id.'**'.$updated_by_user; die;
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
			disconnect($con);die;
		}
	}
	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		/*$team_dtls_sql=sql_select("select a.user_tag_id from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and b.user_tag_id='$user_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.user_tag_id");
		if(count($team_dtls_sql)==1)
		{
			$team_dtls_arr[$user_id]=$team_dtls_sql[0][csf('user_tag_id')];
		}
		else
		{
			$team_dtls_arr[$user_id]='';
		}
		
		if($team_dtls_arr[$user_id]!=$user_id)
		{
			echo "6**"."Save restricted, This Information is Save Only team Leader.";
			die;
		}*/
		$confirm_id=0;
		$confirm_sql=sql_select("select id from qc_confirm_mst where cost_sheet_id=$txt_costSheet_id and status_active=1 and is_deleted=0");
		foreach($confirm_sql as $row){
			$confirm_id.=$row[csf('id')].',';
		}
		$confirm_ids=chop($confirm_id,',');
		if($confirm_ids=="") $confirm_ids=0;
		$flag=1;
		if($confirm_ids!=0)
		{
			$rIDmst_de1=execute_query( "delete from qc_confirm_mst where id in ($confirm_ids)",0);
			if($rIDmst_de1==1 && $flag==1) $flag=1; else $flag=0;
			$rIDdtls_de1=execute_query( "delete from qc_confirm_dtls where mst_id in ($confirm_ids)",0);
			if($rIDdtls_de1==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		$confirm_mst_id=return_next_id("id", "qc_confirm_mst", 1);
		
		$mst_field_arr="id, cost_sheet_id, lib_item_id, confirm_style, confirm_order_qty, confirm_fob, ship_date, ready_to_approve, job_id, inserted_by, insert_date, status_active, is_deleted";
		$mst_data_arr="(".$confirm_mst_id.",".$txt_costSheet_id.",".$txtItem_id.",".$txt_confirm_style.",".$txt_order_qty.",".$txt_confirm_fob.",".$txt_ship_date.",".$cbo_ready_approve.",".$txt_job_id.",".$user_id.",'".$pc_date_time."',1,0)";
		
		$confirm_dtls_id=return_next_id("id", "qc_confirm_dtls", 1);
		
		$dtls_field_arr="id, mst_id, cost_sheet_id, item_id, fab_cons_yds, fab_amount, sp_oparation_amount, wash_amount, acc_amount, fright_amount, lab_amount, inspection_cost, misce_amount, courier_amount, other_amount, commercial_cost, comm_amount, fob_amount, cppm_amount, smv_amount, cm_amount, rmg_ratio, fab_cons_yds_per, fab_amt_per, sp_oparation_amt_per, wash_amt_per, acc_amt_per, fright_amt_per, lab_amt_per, inspection_amt_per, misce_amt_per, courier_amt_per, other_amt_per, commercial_amt_per, comm_amt_per, fob_amt_per, cppm_amt_per, smv_amt_per, cm_amt_per, rmg_ratio_per, inserted_by, insert_date, status_active, is_deleted";//fab_cons_kg, fab_cons_mtr,
		//echo "10**";
		$itm_id=explode(",",str_replace("'","",$txtItem_id));
		
		$up_job_field="quotation_id";
		$up_job_data="".$txt_costSheet_id."";
		//echo "10**".$up_job_field.''.$up_job_data; die;
		$k=0; $add_comma=0; $dtls_data_arr=""; 
		for($i=1; $i<=count($itm_id); $i++)
		{
			$item_id=0;
			$item_id=trim($itm_id[$k]);
			//'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id,
			$txtitemid='txtitemid_'.$item_id;
			$txtdtlsupid='txtdtlsupid_'.$item_id;
			//$txtFabConkg='txtFabConkg_'.$item_id;
			//$txtFabConmtr='txtFabConmtr_'.$item_id;
			$txtFabConyds='txtFabConyds_'.$item_id;
			$txtFabCst='txtFabCst_'.$item_id;
			$txtSpOpa='txtSpOpa_'.$item_id;
			$txtWash='txtWash_'.$item_id;
			$txtAcc='txtAcc_'.$item_id;
			$txtFrightCst='txtFrightCst_'.$item_id;
			$txtLabCst='txtLabCst_'.$item_id;
			$txtInspCst='txtInspCst_'.$item_id;
			$txtMiscCst='txtMiscCst_'.$item_id;
			$txtCourierCst='txtCourierCst_'.$item_id;
			$txtOtherCst='txtOtherCst_'.$item_id;
			$txtCommlCst='txtCommlCst_'.$item_id;
			$txtCommCst='txtCommCst_'.$item_id;
			$txtFobDzn='txtFobDzn_'.$item_id;
			$txtCpm='txtCpm_'.$item_id;
			$txtSmv='txtSmv_'.$item_id;
			$txtCmCst='txtCmCst_'.$item_id;
			$txtPack='txtPack_'.$item_id;
			
			$txtFabConydsPer='txtFabConydsPer_'.$item_id;
			$txtFabCstPer='txtFabCstPer_'.$item_id;
			$txtSpOpaPer='txtSpOpaPer_'.$item_id;
			$txtWashPer='txtWashPer_'.$item_id;
			$txtAccPer='txtAccPer_'.$item_id;
			$txtFrightCstPer='txtFrightCstPer_'.$item_id;
			$txtLabCstPer='txtLabCstPer_'.$item_id;
			$txtInspCstPer='txtInspCstPer_'.$item_id;
			$txtMiscCstPer='txtMiscCstPer_'.$item_id;
			$txtCourierCstPer='txtCourierCstPer_'.$item_id;
			$txtOtherCstPer='txtOtherCstPer_'.$item_id;
			$txtCommlCstPer='txtCommlCstPer_'.$item_id;
			$txtCommCstPer='txtCommCstPer_'.$item_id;
			$txtFobDznPer='txtFobDznPer_'.$item_id;
			$txtCpmPer='txtCpmPer_'.$item_id;
			$txtSmvPer='txtSmvPer_'.$item_id;
			$txtCmCstPer='txtCmCstPer_'.$item_id;
			$txtPackPer='txtPackPer_'.$item_id;
			
			//".$$txtFabConydsPer.",".$$txtFabCstPer.",".$$txtSpOpaPer.",".$$txtWashPer.",".$$txtAccPer.",".$$txtFrightCstPer.",".$$txtLabCstPer.",".$$txtInspCstPer.",".$$txtMiscCstPer.",".$$txtCourierCstPer.",".$$txtOtherCstPer.",".$$txtCommlCstPer.",".$$txtCommCstPer.",".trim($$txtFobDznPer).",".$$txtCpmPer.",".$$txtSmvPer.",".$$txtCmCstPer.",".$$txtPackPer.",
			
			if ($add_comma!=0) $dtls_data_arr .=",";
			$dtls_data_arr .="(".$confirm_dtls_id.",".$confirm_mst_id.",".$txt_costSheet_id.",".$$txtitemid.",".$$txtFabConyds.",".$$txtFabCst.",".$$txtSpOpa.",".$$txtWash.",".$$txtAcc.",".$$txtFrightCst.",".$$txtLabCst.",".$$txtInspCst.",".$$txtMiscCst.",".$$txtCourierCst.",".$$txtOtherCst.",".$$txtCommlCst.",".$$txtCommCst.",".trim($$txtFobDzn).",".$$txtCpm.",".$$txtSmv.",".$$txtCmCst.",".$$txtPack.",".$$txtFabConydsPer.",".$$txtFabCstPer.",".$$txtSpOpaPer.",".$$txtWashPer.",".$$txtAccPer.",".$$txtFrightCstPer.",".$$txtLabCstPer.",".$$txtInspCstPer.",".$$txtMiscCstPer.",".$$txtCourierCstPer.",".$$txtOtherCstPer.",".$$txtCommlCstPer.",".$$txtCommCstPer.",".trim($$txtFobDznPer).",".$$txtCpmPer.",".$$txtSmvPer.",".$$txtCmCstPer.",".$$txtPackPer.",".$user_id.",'".$pc_date_time."',1,0)";//".$$txtFabConkg.",".$$txtFabConmtr.",
			
			$confirm_dtls_id=$confirm_dtls_id+1;
			$add_comma++;
			$k++;
		}
		//echo "10**INSERT INTO qc_confirm_mst (".$mst_field_arr.") VALUES ".$mst_data_arr; 
		
		//echo "10**INSERT INTO qc_confirm_dtls (".$dtls_field_arr.") VALUES ".$dtls_data_arr; 
		//die;/**/
		
		
		$rID=sql_insert("qc_confirm_mst",$mst_field_arr,$mst_data_arr,1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID1=sql_insert("qc_confirm_dtls",$dtls_field_arr,$dtls_data_arr,1);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		
		if(str_replace("'",'',$txt_job_id)!='')
		{
			$rIDJob=sql_update("wo_po_details_master",$up_job_field,$up_job_data,"id","".$txt_job_id."",1);
			if($rIDJob==1 && $flag==1) $flag=1; else $flag=0;
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
		
		/*$insert_user_id=return_field_value("inserted_by","qc_mst","id=".$txt_costSheet_id."","inserted_by");
		$updated_by_user=return_field_value("updated_by","qc_mst","id=".$txt_costSheet_id."","updated_by");
		$team_dtls_arr=array();
		//$team_dtls_sql=sql_select("Select a.user_tag_id as team_leader, b.user_tag_id as team_member from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and a.user_tag_id='$user_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		if( ($updated_by_user*1)==0 ) $updated_by_user=$user_id;
		
		$team_dtls_sql=sql_select("select b.user_tag_id as team_member from lib_mkt_team_member_info b where b.team_id in (select id from lib_marketing_team a where user_tag_id='$user_id' and a.status_active=1 and a.is_deleted=0 ) and b.status_active=1 and b.is_deleted=0");
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
		
		$mst_field_arr="confirm_style*confirm_order_qty*confirm_fob*ship_date*ready_to_approve*job_id*updated_by*update_date";
		$mst_data_arr="".$txt_confirm_style."*".$txt_order_qty."*".$txt_confirm_fob."*".$txt_ship_date."*".$cbo_ready_approve."*".$txt_job_id."*".$user_id."*'".$pc_date_time."'";
		
		$up_job_field="quotation_id";
		$up_job_data="".$txt_costSheet_id."";
		
		$dtls_field_arr="fab_cons_yds*fab_amount*sp_oparation_amount*wash_amount*acc_amount*fright_amount*lab_amount*inspection_cost*misce_amount*courier_amount*other_amount*commercial_cost*comm_amount*fob_amount*cppm_amount*smv_amount*cm_amount*rmg_ratio*fab_cons_yds_per*fab_amt_per*sp_oparation_amt_per*wash_amt_per*acc_amt_per*fright_amt_per*lab_amt_per*inspection_amt_per*misce_amt_per*courier_amt_per*other_amt_per*commercial_amt_per*comm_amt_per*fob_amt_per*cppm_amt_per*smv_amt_per*cm_amt_per*rmg_ratio_per*updated_by*update_date";//fab_cons_kg*fab_cons_mtr*
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
			//$txtFabConkg='txtFabConkg_'.$item_id;
			//$txtFabConmtr='txtFabConmtr_'.$item_id;
			$txtFabConyds='txtFabConyds_'.$item_id;
			$txtFabCst='txtFabCst_'.$item_id;
			$txtSpOpa='txtSpOpa_'.$item_id;
			$txtWash='txtWash_'.$item_id;
			$txtAcc='txtAcc_'.$item_id;
			$txtFrightCst='txtFrightCst_'.$item_id;
			$txtLabCst='txtLabCst_'.$item_id;
			$txtInspCst='txtInspCst_'.$item_id;
			$txtMiscCst='txtMiscCst_'.$item_id;
			$txtCourierCst='txtCourierCst_'.$item_id;
			$txtOtherCst='txtOtherCst_'.$item_id;
			$txtCommlCst='txtCommlCst_'.$item_id;
			$txtCommCst='txtCommCst_'.$item_id;
			$txtFobDzn='txtFobDzn_'.$item_id;
			$txtCpm='txtCpm_'.$item_id;
			$txtSmv='txtSmv_'.$item_id;
			$txtCmCst='txtCmCst_'.$item_id;
			$txtPack='txtPack_'.$item_id;
			
			$txtFabConydsPer='txtFabConydsPer_'.$item_id;
			$txtFabCstPer='txtFabCstPer_'.$item_id;
			$txtSpOpaPer='txtSpOpaPer_'.$item_id;
			$txtWashPer='txtWashPer_'.$item_id;
			$txtAccPer='txtAccPer_'.$item_id;
			$txtFrightCstPer='txtFrightCstPer_'.$item_id;
			$txtLabCstPer='txtLabCstPer_'.$item_id;
			$txtInspCstPer='txtInspCstPer_'.$item_id;
			$txtMiscCstPer='txtMiscCstPer_'.$item_id;
			$txtCourierCstPer='txtCourierCstPer_'.$item_id;
			$txtOtherCstPer='txtOtherCstPer_'.$item_id;
			$txtCommlCstPer='txtCommlCstPer_'.$item_id;
			$txtCommCstPer='txtCommCstPer_'.$item_id;
			$txtFobDznPer='txtFobDznPer_'.$item_id;
			$txtCpmPer='txtCpmPer_'.$item_id;
			$txtSmvPer='txtSmvPer_'.$item_id;
			$txtCmCstPer='txtCmCstPer_'.$item_id;
			$txtPackPer='txtPackPer_'.$item_id;
			
			$id_arr[]=str_replace("'",'',$$txtdtlsupid);
							
			$dtls_data_arr[str_replace("'",'',$$txtdtlsupid)] =explode("*",("".$$txtFabConyds."*".$$txtFabCst."*".$$txtSpOpa."*".$$txtWash."*".$$txtAcc."*".$$txtFrightCst."*".$$txtLabCst."*".$$txtInspCst."*".$$txtMiscCst."*".$$txtCourierCst."*".$$txtOtherCst."*".$$txtCommlCst."*".$$txtCommCst."*".$$txtFobDzn."*".$$txtCpm."*".$$txtSmv."*".$$txtCmCst."*".$$txtPack."*".$$txtFabConydsPer."*".$$txtFabCstPer."*".$$txtSpOpaPer."*".$$txtWashPer."*".$$txtAccPer."*".$$txtFrightCstPer."*".$$txtLabCstPer."*".$$txtInspCstPer."*".$$txtMiscCstPer."*".$$txtCourierCstPer."*".$$txtOtherCstPer."*".$$txtCommlCstPer."*".$$txtCommCstPer."*".$$txtFobDznPer."*".$$txtCpmPer."*".$$txtSmvPer."*".$$txtCmCstPer."*".$$txtPackPer."*'".$user_id."'*'".$pc_date_time."'"));//".$$txtFabConkg."*".$$txtFabConmtr."*
			$k++;
		}
		$flag=1;
		
		$rID=sql_update("qc_confirm_mst",$mst_field_arr,$mst_data_arr,"id","".$txtConfirm_id."",1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID1=execute_query(bulk_update_sql_statement("qc_confirm_dtls", "id",$dtls_field_arr,$dtls_data_arr,$id_arr ));
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		
		if(str_replace("'",'',$txt_job_id)!='')
		{
			$rIDJob=sql_update("wo_po_details_master",$up_job_field,$up_job_data,"id","".$txt_job_id."",1);
			if($rIDJob==1 && $flag==1) $flag=1; else $flag=0;
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
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		//$joborbom=sql_select("select a.job_no from wo_po_details_master a, qc_mst b, wo_pre_cost_mst c where a.inquiry_id=b.inquery_id and b.qc_no=$txt_costSheet_id and a.id=c.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
		$joborbom=sql_select("select a.job_no from wo_po_details_master a, qc_mst b where a.inquiry_id=b.inquery_id and b.qc_no=$txt_costSheet_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
		$jobNoinBom=$joborbom[0][csf('job_no')];
		if($jobNoinBom!="")
		{
			echo "13**".$jobNoinBom;
			disconnect($con);
			die;
		}
		$confirm_id=0;
		$confirm_sql=sql_select("select id from qc_confirm_mst where cost_sheet_id=$txt_costSheet_id and status_active=1 and is_deleted=0");
		foreach($confirm_sql as $row){
			$confirm_id.=$row[csf('id')].',';
		}
		$confirm_ids=chop($confirm_id,',');
		//echo "10**".$confirm_ids; die;
		if($confirm_ids=="") $confirm_ids=0;
		//echo "10**".$confirm_ids; die;
		$flag=1; $costSheet_id=str_replace("'","",$txt_costSheet_id);
		if($confirm_ids!=0)
		{
			$rIDmst_de1=execute_query( "delete from qc_confirm_mst where id in ($confirm_ids)",0);
			if($rIDmst_de1==1 && $flag==1) $flag=1; else $flag=0;
			$rIDdtls_de1=execute_query( "delete from qc_confirm_dtls where cost_sheet_id in ($costSheet_id)",0);
			if($rIDdtls_de1==1 && $flag==1) $flag=1; else $flag=0;
			//echo "10**delete from qc_confirm_dtls where cost_sheet_id in ($costSheet_id)"; die;
		}
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$confirm_mst_id);
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
				echo "2**".str_replace("'",'',$confirm_mst_id);
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
	else if ($operation==3) // Approve Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$dtls_field_arr="fab_cons_yds*fab_amount*sp_oparation_amount*wash_amount*acc_amount*fright_amount*lab_amount*inspection_cost*misce_amount*courier_amount*other_amount*commercial_cost*comm_amount*fob_amount*cppm_amount*smv_amount*cm_amount*rmg_ratio*fab_cons_yds_per*fab_amt_per*sp_oparation_amt_per*wash_amt_per*acc_amt_per*fright_amt_per*lab_amt_per*inspection_amt_per*misce_amt_per*courier_amt_per*other_amt_per*commercial_amt_per*comm_amt_per*fob_amt_per*cppm_amt_per*smv_amt_per*cm_amt_per*rmg_ratio_per*updated_by*update_date";//fab_cons_kg*fab_cons_mtr*
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
			//$txtFabConkg='txtFabConkg_'.$item_id;
			//$txtFabConmtr='txtFabConmtr_'.$item_id;
			$txtFabConyds='txtFabConyds_'.$item_id;
			$txtFabCst='txtFabCst_'.$item_id;
			$txtSpOpa='txtSpOpa_'.$item_id;
			$txtWash='txtWash_'.$item_id;
			$txtAcc='txtAcc_'.$item_id;
			$txtFrightCst='txtFrightCst_'.$item_id;
			$txtLabCst='txtLabCst_'.$item_id;
			$txtInspCst='txtInspCst_'.$item_id;
			$txtMiscCst='txtMiscCst_'.$item_id;
			$txtCourierCst='txtCourierCst_'.$item_id;
			$txtOtherCst='txtOtherCst_'.$item_id;
			$txtCommlCst='txtCommlCst_'.$item_id;
			$txtCommCst='txtCommCst_'.$item_id;
			$txtFobDzn='txtFobDzn_'.$item_id;
			$txtCpm='txtCpm_'.$item_id;
			$txtSmv='txtSmv_'.$item_id;
			$txtCmCst='txtCmCst_'.$item_id;
			$txtPack='txtPack_'.$item_id;
			
			$txtFabConydsPer='txtFabConydsPer_'.$item_id;
			$txtFabCstPer='txtFabCstPer_'.$item_id;
			$txtSpOpaPer='txtSpOpaPer_'.$item_id;
			$txtWashPer='txtWashPer_'.$item_id;
			$txtAccPer='txtAccPer_'.$item_id;
			$txtFrightCstPer='txtFrightCstPer_'.$item_id;
			$txtLabCstPer='txtLabCstPer_'.$item_id;
			$txtInspCstPer='txtInspCstPer_'.$item_id;
			$txtMiscCstPer='txtMiscCstPer_'.$item_id;
			$txtCourierCstPer='txtCourierCstPer_'.$item_id;
			$txtOtherCstPer='txtOtherCstPer_'.$item_id;
			$txtCommlCstPer='txtCommlCstPer_'.$item_id;
			$txtCommCstPer='txtCommCstPer_'.$item_id;
			$txtFobDznPer='txtFobDznPer_'.$item_id;
			$txtCpmPer='txtCpmPer_'.$item_id;
			$txtSmvPer='txtSmvPer_'.$item_id;
			$txtCmCstPer='txtCmCstPer_'.$item_id;
			$txtPackPer='txtPackPer_'.$item_id;
			
			$id_arr[]=str_replace("'",'',$$txtdtlsupid);
							
			$dtls_data_arr[str_replace("'",'',$$txtdtlsupid)] =explode("*",("".$$txtFabConyds."*".$$txtFabCst."*".$$txtSpOpa."*".$$txtWash."*".$$txtAcc."*".$$txtFrightCst."*".$$txtLabCst."*".$$txtInspCst."*".$$txtMiscCst."*".$$txtCourierCst."*".$$txtOtherCst."*".$$txtCommlCst."*".$$txtCommCst."*".$$txtFobDzn."*".$$txtCpm."*".$$txtSmv."*".$$txtCmCst."*".$$txtPack."*".$$txtFabConydsPer."*".$$txtFabCstPer."*".$$txtSpOpaPer."*".$$txtWashPer."*".$$txtAccPer."*".$$txtFrightCstPer."*".$$txtLabCstPer."*".$$txtInspCstPer."*".$$txtMiscCstPer."*".$$txtCourierCstPer."*".$$txtOtherCstPer."*".$$txtCommlCstPer."*".$$txtCommCstPer."*".$$txtFobDznPer."*".$$txtCpmPer."*".$$txtSmvPer."*".$$txtCmCstPer."*".$$txtPackPer."*'".$user_id."'*'".$pc_date_time."'"));//".$$txtFabConkg."*".$$txtFabConmtr."*
			$k++;
		}
		$flag=1;
		//echo "10**";
		//echo "10**INSERT INTO qc_confirm_mst (".$field_array3.") VALUES ".$data_array3; die;	
		//print_r($dtls_data_arr);
		//$rID=sql_update("qc_confirm_mst",$mst_field_arr,$mst_data_arr,"id","".$txtConfirm_id."",0);
		//if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$qcCons_from=return_field_value("excut_source","variable_order_tracking","excut_source=2 and variable_list=68 and is_deleted=0 and status_active=1 order by id","excut_source");
		$rID1=execute_query(bulk_update_sql_statement("qc_confirm_dtls", "id",$dtls_field_arr,$dtls_data_arr,$id_arr ));
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		
		$field_array="approved*approved_by*approved_date";
		
		if(trim(str_replace("'","",$cbo_approved_status))==2) 
		{
			$data_array="'1'*'".$user_id."'*'".$pc_date."'";
		}
		else 
		{
			$data_array="'2'*'".$user_id."'*'".$pc_date."'";
			if($qcCons_from!=2)
			{
				$rID4=execute_query( "update fabric_booking_approval_cause set updated_by='".$user_id."', update_date='".$pc_date."', status_active=0, is_deleted=1 where booking_id =$txt_costSheet_id and entry_form=28 and approval_type=2 and status_active=1 and is_deleted=0",1);
				if($rID4==1 && $flag==1) $flag=1; else $flag=0;	
			}
		}
		
		$rID2=sql_update("qc_confirm_mst",$field_array,$data_array,"id","".$txtConfirm_id."",0);
		if($rID2==1 && $flag==1) $flag=1; else $flag=0;	
		$rID3=sql_update("qc_mst",$field_array,$data_array,"qc_no","".$txt_costSheet_id."",0);
		if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		//echo "10**".$rID1.'='.$rID2.'='.$rID3.'='.$flag; die;
		
		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");  
				echo "3**".$rID2."**".str_replace("'","",$txtConfirm_id);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID2."**".str_replace("'","",$txtConfirm_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);    
				echo "3**".$rID2."**".str_replace("'","",$txtConfirm_id);
			}
			else{
				oci_rollback($con);
				echo "10**".$rID2."**".str_replace("'","",$txtConfirm_id);
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
	$sql_confirm_mst=sql_select("select id, cost_sheet_id, lib_item_id, confirm_style, confirm_order_qty, confirm_fob, deal_merchant, ship_date, ready_to_approve, job_id, approved from qc_confirm_mst where cost_sheet_id ='$ex_data[0]'") ;
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
		echo "$('#cbo_approved_status').val('".$row_mst[csf("approved")]."');\n";
		echo "$('#cbo_ready_approve').val('".$row_mst[csf("ready_to_approve")]."');\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_confirm_entry',1);\n";
		
		if($row_mst[csf("approved")]==1)
		{
			$approval_cause='';
			$menu_id=$_SESSION['menu_id'];
			$user_id=$_SESSION['logic_erp']['user_id'];
			$sql_request="select MAX(id) as id, approval_cause from fabric_booking_approval_cause where entry_form=28 and user_id='$user_id' and booking_id=".$ex_data[0]." and approval_type=2 and status_active=1 and is_deleted=0 group by approval_cause";//page_id='$menu_id' and

			$nameArray_request=sql_select($sql_request);
			foreach($nameArray_request as $approw)
			{
				$approval_cause=$approw[csf("approval_cause")];
			}
			unset($nameArray_request);
			echo "document.getElementById('approve1').value = 'Un-Approved';\n";
			echo "document.getElementById('txt_un_appv_request').disabled = '".false."';\n";
			echo "document.getElementById('txt_un_appv_request').value = '".$approval_cause."';\n";
		}
		else 
		{
			echo "document.getElementById('approve1').value = 'Approved';\n";
			echo "document.getElementById('txt_un_appv_request').disabled = '".true."';\n";
			echo "document.getElementById('txt_un_appv_request').value = '';\n";
		}
	}
	
	$sql_confirm_dtls=sql_select("select id, mst_id, cost_sheet_id, item_id, fab_cons_kg, fab_cons_mtr, fab_cons_yds, fab_amount, sp_oparation_amount, wash_amount, acc_amount, fright_amount, lab_amount, misce_amount, courier_amount, other_amount, commercial_cost, inspection_cost, comm_amount, fob_amount, cppm_amount, smv_amount, cm_amount, rmg_ratio, fab_cons_yds_per, fab_amt_per, sp_oparation_amt_per, wash_amt_per, acc_amt_per, fright_amt_per, lab_amt_per, misce_amt_per, courier_amt_per, other_amt_per, commercial_amt_per, inspection_amt_per, comm_amt_per, fob_amt_per, cppm_amt_per, smv_amt_per, cm_amt_per, rmg_ratio_per from qc_confirm_dtls where cost_sheet_id='$ex_data[0]'");
	foreach($sql_confirm_dtls as $row_dtls)
	{
		echo "$('#txtitemid_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("item_id")]."');\n";
		//echo "$('#txtFabConkg_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("fab_cons_kg")]."');\n";
		//echo "$('#txtFabConmtr_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("fab_cons_mtr")]."');\n";
		echo "$('#txtFabConyds_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("fab_cons_yds")]."');\n";
		echo "$('#txtFabCst_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("fab_amount")]."');\n";
		echo "$('#txtSpOpa_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("sp_oparation_amount")]."');\n";
		echo "$('#txtWash_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("wash_amount")]."');\n";
		echo "$('#txtAcc_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("acc_amount")]."');\n";
		echo "$('#txtFrightCst_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("fright_amount")]."');\n";
		echo "$('#txtLabCst_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("lab_amount")]."');\n";
		echo "$('#txtInspCst_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("inspection_cost")]."');\n";
		echo "$('#txtMiscCst_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("misce_amount")]."');\n";
		echo "$('#txtCourierCst_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("courier_amount")]."');\n";
		echo "$('#txtOtherCst_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("other_amount")]."');\n";
		echo "$('#txtCommlCst_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("commercial_cost")]."');\n";
		echo "$('#txtCommCst_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("comm_amount")]."');\n";
		echo "$('#txtFobDzn_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("fob_amount")]."');\n";
		echo "$('#txtCpm_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("cppm_amount")]."');\n";
		echo "$('#txtSmv_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("smv_amount")]."');\n";
		echo "$('#txtCmCst_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("cm_amount")]."');\n";
		echo "$('#txtPack_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("rmg_ratio")]."');\n";
		
		echo "$('#txtFabConydsPer_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("fab_cons_yds_per")]."');\n";
		echo "$('#txtFabCstPer_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("fab_amt_per")]."');\n";
		echo "$('#txtSpOpaPer_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("sp_oparation_amt_per")]."');\n";
		echo "$('#txtWashPer_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("wash_amt_per")]."');\n";
		echo "$('#txtAccPer_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("acc_amt_per")]."');\n";
		echo "$('#txtFrightCstPer_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("fright_amt_per")]."');\n";
		echo "$('#txtLabCstPer_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("lab_amt_per")]."');\n";
		echo "$('#txtInspCstPer_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("inspection_amt_per")]."');\n";
		echo "$('#txtMiscCstPer_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("misce_amt_per")]."');\n";
		echo "$('#txtCourierCstPer_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("courier_amt_per")]."');\n";
		echo "$('#txtOtherCstPer_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("other_amt_per")]."');\n";
		echo "$('#txtCommlCstPer_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("commercial_amt_per")]."');\n";
		echo "$('#txtCommCstPer_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("comm_amt_per")]."');\n";
		echo "$('#txtFobDznPer_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("fob_amt_per")]."');\n";
		echo "$('#txtCpmPer_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("cppm_amt_per")]."');\n";
		echo "$('#txtSmvPer_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("smv_amt_per")]."');\n";
		echo "$('#txtCmCstPer_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("cm_amt_per")]."');\n";
		echo "$('#txtPackPer_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("rmg_ratio_per")]."');\n";
		echo "$('#txtdtlsupid_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("id")]."');\n";
	}
	exit();
}

if($action=="budgete_cost_validate")
{
	$job_no=$data;
	$str_data="";
	
	if($job_no!="")
	{
		$fab_cons_arr=array();
		$sql_fabric = "select id, job_no, body_part_id, fab_nature_id, uom, avg_cons, avg_cons_yarn, rate, amount, avg_finish_cons from wo_pre_cost_fabric_cost_dtls where job_no='$job_no' and uom in (12,23,27) and status_active=1";
		$data_arr_fabric=sql_select($sql_fabric);
		foreach($data_arr_fabric as $row)
		{
			$fab_cons_arr[$row[csf("job_no")]][$row[csf("uom")]]+=$row[csf("avg_cons")];
		}
		unset($data_arr_fabric);
		$fab_cons_kg=$fab_cons_mtr=$fab_cons_yds=0;
		$fab_cons_kg=$fab_cons_arr[$job_no][12];
		$fab_cons_mtr=$fab_cons_arr[$job_no][23];
		$fab_cons_yds=$fab_cons_arr[$job_no][27];
	
		$sql="select id, job_no, costing_per_id, order_uom_id, fabric_cost, trims_cost, embel_cost, wash_cost, comm_cost, commission, lab_test, inspection, cm_cost, freight, currier_pre_cost, certificate_pre_cost, common_oh, depr_amor_pre_cost, total_cost, price_dzn from wo_pre_cost_dtls where job_no='$job_no' and status_active=1 and is_deleted=0";
		
		$dataArr=sql_select($sql);
		$fab_amount=$sp_oparation_amount=$wash_amount=$acc_amount=$fright_amount=$lab_amount=$misce_amount=$currier_amount=$other_amount=$comm_amount=$fob_amount=$cm_amount=$rmg_ratio=$comml_cost=$inspection=0;
		
		foreach($dataArr as $row)
		{
			//$sp_cost=0;
			//$sp_cost=$row[csf("embel_cost")]+$row[csf("wash_cost")];
			$fab_amount+=$row[csf("fabric_cost")];
			$sp_oparation_amount+=$row[csf("embel_cost")];
			$wash_amount+=$row[csf("wash_cost")];
			$acc_amount+=$row[csf("trims_cost")];
			$fright_amount+=$row[csf("freight")];
			$lab_amount+=$row[csf("lab_test")];
			$inspection+=$row[csf("inspection")];
			
			$misce_amount+=$row[csf("misce_amount")];
			$currier_amount+=$row[csf("currier_pre_cost")];
			$other_amount+=$row[csf("other_amount")];
			
			$comm_amount+=$row[csf("commission")];
			$fob_amount+=$row[csf("price_dzn")];
			$cm_amount+=$row[csf("cm_cost")];
			$rmg_ratio+=$row[csf("rmg_ratio")];
			$comml_cost+=$row[csf("comm_cost")];
		}
		$str_data=$fab_cons_kg."##".$fab_cons_mtr."##".$fab_cons_yds."##".$fab_amount."##".$sp_oparation_amount."##".$acc_amount."##".$fright_amount."##".$lab_amount."##".$misce_amount."##".$other_amount."##".$comm_amount."##".$fob_amount."##".$cm_amount."##".$rmg_ratio."##".$wash_amount."##".$currier_amount."##".$comml_cost."##".$inspection;
		unset($dataArr);
	}
	
	echo $str_data;
	exit();
}

if ($action=="style_tag_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function set_checkvalue()
		{
			if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
			else document.getElementById('chk_job_wo_po').value=0;
		}
	
		function js_set_value(all_data)
		{ 
			document.getElementById('hidd_job_data').value=all_data;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
            	<tr>
                	<th colspan="8" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>
                    <th>Company Name</th>
                    <th>Buyer Name</th>
                    <th>Job Year</th>
                    <th>Job No</th>
                    <th>Style Ref </th>
                    <th colspan="2">Ship Date Range</th>
                    <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job With QC</th>
                </tr>        
            </thead>
            <tr class="general">
                <td> 
                    <input type="hidden" id="hidd_job_data" />
					<? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-Select Company-", $cbo_company_name,"load_drop_down( 'quick_costing_woven_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_pop_td' );",'' ); ?>
                </td>
        		<td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_id", 130, $blank_array,'', 1, "-Select Buyer-" ); ?></td>
                <td><? echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", "", "",0 );//date('Y') ?></td>
                <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:50px"></td>
                
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td> 
                <td align="center">
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_style').value, 'create_po_search_list_view', 'search_div', 'quick_costing_woven_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        	</tr>
            <tr>
                <td align="center" colspan="8"><? echo load_month_buttons(1);  ?></td>
            </tr>
        </table>
        <div id="search_div"></div>
    </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_po_search_list_view")
{
	//echo $data;die;
	$data=explode('_',$data);
	$year=$data[6];
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else $company="";//{ echo "Please Select Company First."; die; }
	//print_r($data);
	if(str_replace("'","",$data[1])==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer="";
		}
		else $buyer="";
	}
	else $buyer=" and a.buyer_name='$data[1]'";
	
	if($db_type==0)
	{
		if($year==0) $yearCond=""; else $yearCond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)='".$year."'";
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	else if($db_type==2)
	{
		if($year==0) $yearCond=""; else $yearCond=" and to_char(a.insert_date,'YYYY')='".$year."'";
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	
	$order_cond=""; $job_cond=""; $style_cond="";
	if($data[8]==1)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num='$data[5]'";
		if (trim($data[7])!="") $style_cond=" and a.style_ref_no='$data[7]'  "; //else  $style_cond=""; 
	}
	else if($data[8]==4 || $data[8]==0)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '%$data[5]%'"; //else  $job_cond=""; 
		if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '%$data[7]%'  "; //else  $style_cond=""; 
	}
	else if($data[8]==2)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '$data[5]%'"; //else  $job_cond=""; 
		if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '$data[7]%'  "; //else  $style_cond=""; 
	}
	else if($data[8]==3)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '%$data[5]'"; //else  $job_cond=""; 
		if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '%$data[7]'  "; //else  $style_cond=""; 
	}
			
	$buyer_arr=return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name"); 
	$season_arr=return_library_array("select id, season_name from lib_buyer_season", "id", "season_name"); 
	
	if($db_type==0) $year_cond="YEAR(a.insert_date)"; else if ($db_type==2) $year_cond="TO_CHAR(a.insert_date,'YYYY')";
	
	if ($data[2]==0) $quotation_id_cond="and (a.quotation_id=0 or a.quotation_id is null)"; else $quotation_id_cond="and a.quotation_id!=0";
	
	$arr=array(3=>$buyer_arr,5=>$season_arr);
	$sql ="select a.id, a.job_no_prefix_num, $year_cond as year, a.job_no, a.buyer_name, a.style_ref_no, a.season_buyer_wise, a.job_quantity, a.quotation_id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $quotation_id_cond $company $buyer $shipment_date $job_cond $style_cond $yearCond group by a.id, a.job_no_prefix_num, a.insert_date, a.job_no, a.buyer_name, a.style_ref_no, a.season_buyer_wise, a.job_quantity, a.quotation_id order by a.id DESC";
		
	//echo $sql;
	echo  create_list_view("list_view", "Job No, Job Pre., Year, Buyer, Style, Season, Job Qty.,QC No", "100,60,60,110,100,80,90,80","730","250",0, $sql, "js_set_value", "id,job_no,style_ref_no", "", 1, "0,0,0,buyer_name,0,season_buyer_wise,0", $arr ,"job_no,job_no_prefix_num,year,buyer_name,style_ref_no,season_buyer_wise,job_quantity,quotation_id", "","","0,0,0,0,0,0,2,0");	
	
	exit();
}

if($action=="style_tag_popup1")
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
							
							$sql ="select id, job_no_prefix_num, $year_cond as year, job_no, buyer_name, style_ref_no, season_matrix, job_quantity from wo_po_details_master where buyer_name='$data' and status_active=1 and is_deleted=0 and quotation_id=0 order by id DESC";
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
	var permission = '<?=$permission; ?>';
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
		var data="action="+action+get_submitted_data_string('cbo_buyer_id*cbo_season_id*cbo_subDept_id*txt_search_common*hide_curr_cost_id' ,"../../../",4);
		//alert(data);//+"&report_title="+report_title
		//freeze_window(3);
		//alert(data);
		http.open("POST","quick_costing_woven_controller.php",true);
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
    	<div style="display:none"><?=load_freeze_divs ("../../../",$permission);  ?></div>
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
                        	 <? echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name",1, "-- All Buyer--",$selected,"load_drop_down( 'quick_costing_woven_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'quick_costing_woven_controller',this.value, 'load_drop_down_sub_dep', 'sub_td' );",0 ); ?>
                        </td>                 
                        <td id="season_td"><? echo create_drop_down( "cbo_season_id", 130, $blank_array,'', 1, "-- Select Season--",$selected, "" ); ?></td>
                        <td id="sub_td"><? echo create_drop_down( "cbo_subDept_id", 130, $blank_array,'', 1, "-- Select Dept--",$selected, "" ); ?></td>     
                        <td align="center" id="search_by_td"><input type="hidden" name="hide_curr_cost_id" id="hide_curr_cost_id" value="<?=$costSheetId; ?>" />				
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
	$stage_arr=return_library_array( "select tuid, stage_name from lib_stage_name", "tuid", "stage_name");
	$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name");
	$fob_arr=return_library_array( "select mst_id, tot_fob_cost from qc_tot_cost_summary", "mst_id", "tot_fob_cost");
	$agent_location_arr=return_library_array( "select tuid, agent_location from lib_agent_location", "tuid", "agent_location");
	
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
	
	$sql_mst="select a.id as mst_id, a.buyer_id, a.season_id, a.department_id, a.style_ref, a.temp_id, a.lib_item_id, a.offer_qty, a.tgt_price, a.quoted_price, a.stage_id, a.option_id, a.revise_no, a.inserted_by, a.updated_by, b.id, b.meeting_no, b.buyer_agent_id, b.location_id, b.meeting_date, $meeting_time as meeting_time, b.remarks from qc_mst a, qc_meeting_mst b where a.qc_no=b.mst_id and a.status_active=1 and a.is_deleted=0 and (b.remarks<>' ' and b.remarks<>'1.' and b.remarks<>'1. ') $buyer_id_cond $season_id_cond $subDept_id_cond $style_ref_cond order by a.id DESC";
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
		if($confirm_arr[$row[csf('id')]]!="") $confirm='Yes'; else $confirm='No';   
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" onClick="jsset_value('<? echo $meeting_data; ?>'); change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>');" id="tr_<? echo $i; ?>" > 
			<td width="30"><? echo $i; ?></td>
			<td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
			<td width="80"><? echo $season_arr[$row[csf('season_id')]]; ?></td>
			<td width="80"><? echo $department_arr[$row[csf('department_id')]]; ?>&nbsp;</td>
			<td width="100" style="word-break: break-all;"><? echo $row[csf('style_ref')]; ?></td>
			<td width="130" style="word-break: break-all;"><? echo $template_name; ?></td>
			<td width="80" align="right"><? echo number_format($row[csf('offer_qty')],0,'.',''); ?></td>
			<td width="70" align="right"><? echo number_format($fob_arr[$row[csf('id')]],4,'.',''); ?>&nbsp;</td>
			<td width="60" align="right"><? echo number_format($row[csf('tgt_price')],4,'.',''); ?>&nbsp;</td>
			<td width="70" align="right"><? echo number_format($row[csf('quoted_price')],4,'.',''); ?>&nbsp;</td>
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
				http.open("POST","quick_costing_woven_controller.php",true);
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
				
				if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
				{
					$('#update_id').val(reponse[1]);
					if(reponse[0]==2) set_button_status(0, permission, 'fnc_agent_location_entry',1);
					else set_button_status(1, permission, 'fnc_agent_location_entry',1);
					show_list_view(type,'agent_location_data_list','agent_location_data_div','quick_costing_woven_controller','');
				}
				if (reponse[0]==0) alert("Data is Save Successfully");
				else if (reponse[0]==1) alert("Data is Update Successfully");
				else if (reponse[0]==2) alert("Data is Delete Successfully");
				if(reponse[0]==11)
				{
					alert("Duplicate Data Found, Please check again.")
					release_freezing();
					return;
				}
				if(reponse[0]==15)
				{
					alert("Cost Sheet No Found, So Delete Not Possible")
					release_freezing();
					return;
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
    <script>show_list_view(type,'agent_location_data_list','agent_location_data_div','quick_costing_woven_controller','');</script>          
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
		if (is_duplicate_field( "agent_location", "lib_agent_location", "agent_location='".strtoupper(str_replace("'","",$txt_agent_location))."' and type='".$type."' and is_deleted=0" ) == 1)
		{
			echo "11**0"; 
			disconnect($con);die;
		}
		$id=return_next_id("id", "lib_agent_location", 1);
		//echo "10**".$type; //die;
		$tuid=$_SESSION['logic_erp']['user_id'].$id;
		$field_array="id, type, agent_location, inserted_by, insert_date, status_active, is_deleted, tuid";
		$data_array="(".$id.",'".$type."','".strtoupper(str_replace("'","",$txt_agent_location))."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,'".$tuid."')";
		//echo $data_array; die;
		$rID=sql_insert("lib_agent_location",$field_array,$data_array,1);
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "0**".$tuid;
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
				echo "0**".$tuid;
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
		if (is_duplicate_field( "agent_location", "lib_agent_location", "agent_location='".strtoupper(str_replace("'","",$txt_agent_location))."' and type='".$type."' and tuid!=$update_id and is_deleted=0" ) == 1)
		{
			echo "11**0"; 
			disconnect($con);die;
		}
		$field_array="agent_location*updated_by*update_date";
		$data_array="'".strtoupper(str_replace("'","",$txt_agent_location))."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_update("lib_agent_location",$field_array,$data_array,"tuid","".$update_id."",1);
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id);
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
				echo "1**".str_replace("'","",$update_id);
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
	else if ($operation==2) // DELETE Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if($type==1) $agent_loc_cond="buyer_agent_id"; else if($type==2) $agent_loc_cond="location_id";
		if (is_duplicate_field( "$agent_loc_cond", "qc_meeting_mst", "$agent_loc_cond=$update_id and is_deleted=0" ) == 1)
		{
			echo "15**0"; 
			disconnect($con);die;
		}
		
		$rID=execute_query( "update lib_agent_location set status_active='0', is_deleted='1' where tuid=$update_id and is_deleted=0",1);
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$update_id);
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
				echo "2**".str_replace("'","",$update_id);
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
	echo  create_list_view ( "list_view", "Agent/Location Name", "200","200","150",1, "select tuid, agent_location from lib_agent_location where type='$data' and status_active=1 and is_deleted=0 order by id desc", "get_php_form_data", "tuid","'load_php_data_to_form_agent_location'", 1, "0", $arr, "agent_location","quick_costing_woven_controller", 'setFilterGrid("list_view",-1);','0' );
	exit();
}

if($action=="load_php_data_to_form_agent_location")
{
	$sql_arr=sql_select( "select tuid, agent_location from lib_agent_location where status_active=1 and is_deleted=0 and tuid='$data'" );
	foreach ($sql_arr as $inf)
	{
		echo "document.getElementById('txt_agent_location').value  			= '".$inf[csf("agent_location")]."';\n"; 
		echo "document.getElementById('update_id').value  				= '".$inf[csf("tuid")]."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_agent_location_entry',1);\n";  
	}
	exit();
}

if($action=="load_drop_agent_location_name")
{
	if($data==1) echo create_drop_down( "cbo_buyer_agent", 80,"select tuid, agent_location from lib_agent_location where type='$data' and status_active=1 and is_deleted=0","tuid,agent_location", 1, "-Agent-", $selected, "" );
	else if ($data==2)  echo create_drop_down( "cbo_agent_location", 80,"select tuid, agent_location from lib_agent_location where type='$data' and status_active=1 and is_deleted=0","tuid,agent_location", 1, "-Location-", $selected, "" );
	
	exit();
}

if($action=="max_meeting_no")
{
	$max_meeting=return_field_value("max(meeting_no) as max_meeting_no","qc_meeting_mst","is_deleted=0 and status_active=1","max_meeting_no");
	if($max_meeting=="" || $max_meeting==0) $max_meeting=0; else $max_meeting=$max_meeting;
	echo $max_meeting;
	exit();	
}

if($action=="print_btn_id")
{
	$print_report="";
	$print_report_format=return_field_value("format_id","lib_report_template","module_id=2 and report_id=83 and is_deleted=0 and status_active=1");
	if($print_report_format=="" || $print_report_format==0) $print_report=0; else $print_report=$print_report_format;
	$print_report=implode(",",array_unique(explode(",",$print_report)));
	
	echo $print_report;
	exit();	
}

if($action=="fobavg_option_popup")
{
	echo load_html_head_contents("FOB Average Option PopUp","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="620" class="rpt_table" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Option</th>
            <th width="80">Quantity</th>
            <th width="60">FOB</th>
            <th width="80">Total FOB</th>
            <th>Option Remarks</th>
        </thead>
    </table>
    <div style="width:620px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="602" class="rpt_table" id="tbl_list_search" >
        <?
			$sql_fob="select a.option_id, a.offer_qty, a.option_remarks, b.tot_fob_cost from qc_mst a, qc_tot_cost_summary b where a.cost_sheet_no='$data' and a.qc_no=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.option_id, a.offer_qty, a.option_remarks, b.tot_fob_cost"; //a.revise_no,
			//echo $sql_fob;
			$sql_fob_res=sql_select($sql_fob);
			$i=1; 
			foreach($sql_fob_res as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$tot_fob=0;
				$tot_fob=$row[csf('offer_qty')]*$row[csf('tot_fob_cost')];
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>"> 
                	<td width="30" align="center"><?php echo $i; ?></td>
                    <td width="60" align="center"><?php echo $row[csf('option_id')]; ?></td>
                    <td width="80" align="right"><?php echo number_format($row[csf('offer_qty')]); ?></td>
                    <td width="60" align="right"><?php echo number_format($row[csf('tot_fob_cost')],4); ?></td>
                    <td width="80" align="right"><?php echo number_format($tot_fob,4); ?></td>
                    <td style="word-break:break-all"><?php echo $row[csf('option_remarks')]; ?></td>
                </tr>
                <?
				$gnd_qty+=$row[csf('offer_qty')];
				$gnd_tot_fob+=$tot_fob;
				$i++;
			}
			$avg_fob=$gnd_tot_fob/$gnd_qty;
			?>
        </table>
    </div>
    <table width="620" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" > 
       <tr>
            <td width="30">&nbsp;</td>
            <td width="60">Total :</td>
            <td width="80" align="right"><?php echo number_format($gnd_qty); ?></td>
            <td width="60" align="right"><?php echo number_format($avg_fob,4); ?></td>
            <td width="80" align="right"><?php echo number_format($gnd_tot_fob,4); ?></td>
            <td>&nbsp;</td>
        </tr>
    </table>
    <?        
	exit();
}

if($action=="quick_costing_print")
{
	//extract($_REQUEST);
	extract(check_magic_quote_gpc($_REQUEST));
 //	echo load_html_head_contents("Quick Costing Print", "../../../", 1, 1,'','','');
	//extract(check_magic_quote_gpc( $process ));
	//echo $data; die;
	$qc_no="";
	 $data=explode('*',$data);
	$qc_no=str_replace("'","",$data[0]);
	$txt_costSheetNo=str_replace("'","",$data[1]);
	$is_excel=str_replace("'","",$data[3]);
	 
	
	//if($qc_no=="") $qc_no=''; else $qc_no=" and a.qc_no=".$qc_no."";
	//if($costSheetNo=="") $costSheetNo=''; else $costSheetNo=" and a.cost_sheet_no=".$costSheetNo."";

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_name_arr=return_library_array( "select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0",'id','supplier_name');
	$season_name_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0",'id','season_name');
	$brand_name_arr=return_library_array( "select id, brand_name from lib_buyer_brand where status_active=1 and is_deleted=0",'id','brand_name');
	$trim_group=return_library_array( "select item_name,id from lib_item_group", "id", "item_name");
	$color_arr=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name"); 
	
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
	
	$mstSql = "SELECT id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, inquery_id, style_ref, buyer_id, uom, cons_basis, season_id, season_year, brand_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, pre_cost_sheet_id, revise_no, option_id, buyer_remarks, option_remarks, body_color_id,INSERTED_BY from qc_mst where qc_no='$qc_no' and status_active=1 and is_deleted=0";
	 // echo $mstSql;  
	$master_data=sql_select($mstSql);
	$buyer_name=$master_data[0][csf('buyer_id')];
	$style_description=$master_data[0][csf('style_des')];
	
	$lib_item_id=$master_data[0][csf('lib_item_id')];
	$inquery_id=$master_data[0][csf('inquery_id')];
	$INSERTED_BY=$master_data[0][csf('INSERTED_BY')];
	$brand_id=$master_data[0][csf('brand_id')];
	$mstconuom=$unit_of_measurement[$master_data[0][csf('uom')]];
	
	$sqlInquiry="select gmts_item from wo_quotation_inquery where status_active=1 and is_deleted=0 and id='$inquery_id'";
	$sqlInquiryRes=sql_select($sqlInquiry);
	$inquerygmtsitem_id=$sqlInquiryRes[0][csf('gmts_item')];
	if($master_data[0][csf('buyer_id')]!=0) $addseasonYear='-'.$master_data[0][csf('season_year')]; else $addseasonYear="";
	
	$readyToAppArr=return_library_array("select cost_sheet_id, ready_to_approve from qc_confirm_mst where cost_sheet_id='$qc_no' and status_active=1 and is_deleted=0","cost_sheet_id","ready_to_approve"); 
	$unAppReqArr=return_library_array("select booking_id, approval_cause from fabric_booking_approval_cause where booking_id='$qc_no' and entry_form=28 and status_active=1 and is_deleted=0","booking_id","approval_cause");
	
	$image_location = sql_select("SELECT image_location from common_photo_library where file_type=1 and form_name='knit_order_entry' and master_tble_id=$txt_job_no ");
	ob_start();
	?>
		 
        <table width="1350">
			<tr><th style="text-align: center;">BASIC COST SHEET</th></tr>
			<tr><th style="text-align: center;"><?=$buyer_arr[$buyer_name].' '.$style_description; ?> </th></tr>
		</table>
		<table width="1350">
			<tr>
				<td width="120">Master Style : </td>
				<td width="170"><?=$master_data[0][csf('style_ref')]; ?></td>
				<td width="120">Season : </td>
				<td width="170"><?=$season_name_arr[$master_data[0][csf('season_id')]].$addseasonYear; ?></td>
				<td width="120">Costing Date : </td>
				<td width="170"><?=change_date_format($master_data[0][csf('costing_date')],'yyyy-mm-dd','-'); ?></td>
                <td width="120">Brand : </td>
				<td><?=$brand_name_arr[$master_data[0][csf('brand_id')]]; ?></td>
			</tr>
			<tr>
				<td>Currency : </td>
				<td>USD</td>
                <td>Inquiry Item : </td>
				<td><?=$garments_item[$inquerygmtsitem_id]; ?></td>
                <td>Costing Per : </td>
				<td>1 PCS</td>
                <td>Body/Wash Color</td>
				<td><?=$color_arr[$master_data[0][csf('body_color_id')]]; ?></td>
			</tr>
            <tr>
				<td>Option : </td>
				<td><?=$master_data[0][csf('option_id')]; ?></td>
                <td>Ready To App. : </td>
				<td><?=$yes_no[$readyToAppArr[$qc_no]]; ?></td>
                <td>Un-approve Request : </td>
				<td colspan="3"><?=$unAppReqArr[$qc_no]; ?></td>
			</tr>
		</table>
		<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:1350px; margin-top: 10px" rules="all">
			<thead>
				<th width="530">Fabrication</th>
				<th width="100">Fabric Usage</th>
				<th width="60">Cutable Width</th>
				<th width="60">Shrinkage L</th>
				<th width="60">Shrinkage W</th>
				<th width="50">Price/ <?=$mstconuom; ?></th>
				<th width="50">Fina. Char</th>
				<th width="60">Cons. (<?=$mstconuom; ?>)</th>
				<th width="60">Actual Fab Cost</th>
				<th width="60">Total Fabric Price</th>
				<th width="60">CM</th>
				<th width="60">TTL Trims Cost</th>
				<th width="60">Wash</th>
				<th width="60">Emb.</th>
                <th width="60">Courier Cost</th>
				<th width="60">Others</th>
				<th width="60">Total Cost/ 1 PCS</th>
				<th width="50">FOB/ 1 PCS</th>
			</thead>
			<? 
			
			$consSql="select id, mst_id, item_id, type, particular_type_id, consumption, ex_percent, tot_cons, uom, rate, value, fab_id, description, use_for, fc_per, body_part_id, supp_ref from qc_cons_rate_dtls where mst_id='$qc_no' and status_active=1 and is_deleted=0 order by id ASC";
			$consSqlData=sql_select($consSql); $cons_rate_fab_arr=array(); $EmbData=array(); $TrimData=array(); $tot_rows=0; $libFabId="";
			foreach ($consSqlData as $row)
			{
				if($row[csf("type")]==1)
				{
					if($row[csf("tot_cons")]>0)
					{
						$cons_rate_fab_arr[$row[csf("description")]][$row[csf("use_for")]]['val']+=$row[csf("value")];
						$cons_rate_fab_arr[$row[csf("description")]][$row[csf("use_for")]]['cons']+=$row[csf("tot_cons")];
						$cons_rate_fab_arr[$row[csf("description")]][$row[csf("use_for")]]['fc_per']+=$row[csf("fc_per")];
						$cons_rate_fab_arr[$row[csf("description")]][$row[csf("use_for")]]['rate']+=$row[csf("rate")];
						$cons_rate_fab_arr[$row[csf("description")]][$row[csf("use_for")]]['fab_id']=$row[csf("fab_id")];
						$tot_rows++;
						$libFabId.=$row[csf("fab_id")].",";
					}
				}
				if($row[csf("type")]==2)
				{
					$cons_rate_sp_arr[$row[csf("item_id")]].=$row[csf("particular_type_id")].'_'.$row[csf("fab_id")].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf("body_part_id")].'_'.$row[csf("consumption")].'_'.$row[csf("ex_percent")].'_'.$row[csf("tot_cons")].'_'.$row[csf("rate")].'_'.$row[csf("value")].'##';
				}
				if($row[csf("type")]==3)
				{
					if($row[csf("tot_cons")]>0)
					{
						$washqty=$row[csf("tot_cons")];
						$washamount=$row[csf("value")];
						$EmbData[$row[csf("id")]]['emb_name']=3;
						$EmbData[$row[csf("id")]]['emb_type']=$row[csf("particular_type_id")];
						$EmbData[$row[csf("id")]]['cons_dzn_gmts']=$row[csf("tot_cons")];
						$EmbData[$row[csf("id")]]['rate']=$row[csf("rate")];
						$EmbData[$row[csf("id")]]['amount']=$row[csf("value")];
						$EmbData[$row[csf("id")]]['tot_cons']=$washqty;
						$EmbData[$row[csf("id")]]['tot_amount']=$washamount;
						$totWashAmount+=$washamount;
					}
				}
				if($row[csf("type")]==4)
				{
					if($row[csf("tot_cons")]>0)
					{
						$trim_qty=$row[csf("tot_cons")];
						$trim_amount=$row[csf("value")];
						$TrimData[$row[csf('id')]]['trim_group']=$row[csf('particular_type_id')];
						$TrimData[$row[csf('id')]]['description']=$row[csf('description')];
						$TrimData[$row[csf('id')]]['brand_sup_ref']=$row[csf('supp_ref')];
						$TrimData[$row[csf('id')]]['cons_dzn_gmts']=$row[csf("tot_cons")];
						$TrimData[$row[csf('id')]]['uom']=$row[csf("uom")];
						$TrimData[$row[csf('id')]]['rate']=$row[csf('rate')];
						$TrimData[$row[csf('id')]]['amount']=$row[csf("value")];
						$TrimData[$row[csf('id')]]['tot_cons']=$trim_qty;
						$TrimData[$row[csf('id')]]['tot_amount']=$trim_amount;
						$totTrim+=$row[csf("tot_cons")];
						$totTrimAmount+=$trim_amount;
					}
				}
			}
			
			$libFabIds=chop($libFabId,','); $libFabIdCond="";
			if($db_type==2 && $tot_rows>1000)
			{
				$libFabIdCond=" and (";
				$libFabIdsArr=array_chunk(explode(",",$libFabIds),999);
				foreach($libFabIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$libFabIdCond.=" id in($ids) or ";
					
				}
				$libFabIdCond=chop($libFabIdCond,'or ');
				$libFabIdCond.=")";
			}
			else
			{
				$libFabIdCond=" and id in ($libFabIds)";
			}
			
			$sqlRd="select id, type, construction, gsm_weight, weight_type, design, fabric_ref, rd_no, color_range_id, full_width, cutable_width,shrinkage_l,shrinkage_w from lib_yarn_count_determina_mst where status_active=1 and is_deleted=0 $libFabIdCond";
			$sqlRdData=sql_select($sqlRd);
			$cutableArr=array();
			foreach($sqlRdData as $frow)
			{
				$cutableArr[$frow[csf('id')]]['cutable_width']=$frow[csf('cutable_width')];
				$cutableArr[$frow[csf('id')]]['shrinkage_l']=$frow[csf('shrinkage_l')];
				$cutableArr[$frow[csf('id')]]['shrinkage_w']=$frow[csf('shrinkage_w')];
			}
			unset($sqlRdData);
			
			//print_r($EmbData);
			
			$sqlSum="select id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_wash_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_courier_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost from qc_tot_cost_summary where mst_id='$qc_no' and status_active=1 and is_deleted=0";
			$sqlSumData=sql_select($sqlSum);
			$fabric_req_amount=$sqlSumData[0][csf('tot_fab_cost')];
			$cm_cost=$sqlSumData[0][csf('tot_cm_cost')];
			$cm_cost_amount=$sqlSumData[0][csf('tot_cm_cost')];
			$trims_cost=$sqlSumData[0][csf('tot_accessories_cost')];
			
			$totTrimAmount=$sqlSumData[0][csf('tot_accessories_cost')];
			$wash_cost=$sqlSumData[0][csf('tot_wash_cost')];
			$totWashAmount=$sqlSumData[0][csf('tot_wash_cost')];
			$embel_cost=$sqlSumData[0][csf('tot_sp_operation_cost')];
			$total_other_cost=$sqlSumData[0][csf('tot_other_cost')];
			$courier_cost=$sqlSumData[0][csf('tot_courier_cost')];
			$other_cost_value=$sqlSumData[0][csf('tot_other_cost')];
			$commercial_amount=0;
			$price_dzn=$sqlSumData[0][csf('tot_fob_cost')];
			$total_price=$sqlSumData[0][csf('tot_fob_cost')];
			$total_cost=$sqlSumData[0][csf('tot_cost')];
			$k=1;
			$colSpan=count($cons_rate_fab_arr)+1;
			foreach ($cons_rate_fab_arr as $fabric_des=>$useForData) 
			{
				foreach($useForData as $useFor=>$val) 
				{
					?>
					<tr>
						<td width="530"><?=$fabric_des.",".$cutableArr[$val['fab_id']]['shrinkage_l'].",".$cutableArr[$val['fab_id']]['shrinkage_w']; ?></td>
						<td width="100"><?=$useFor; ?></td>
						<td width="60" align="center" title="<?=$val['fab_id']; ?>"><?=$cutableArr[$val['fab_id']]['cutable_width']; ?></td>
						<td width="60" align="center" title="<?=$val['fab_id']; ?>"><?=$cutableArr[$val['fab_id']]['shrinkage_l']; ?></td>
						<td width="60" align="center" title="<?=$val['fab_id']; ?>"><?=$cutableArr[$val['fab_id']]['shrinkage_w']; ?></td>
						<td width="50" align="right"><?=number_format($val['rate'],2); ?></td>
						<td width="50" align="right"><?=$val['fc_per']; ?>&percnt;</td>
						<td width="60" align="right"><?=number_format($val['cons'],2); ?></td>
						<td width="60" align="right"><?=number_format($val['cons']*$val['rate'],2); ?></td>
						<td width="60" align="right"><?=number_format($val['val'],2); ?></td>
						<? if($k==1){ ?>
						<td width="60" rowspan="<?=$colSpan ?>" align="right"><?=number_format($cm_cost,2); ?></td>
						<td width="60" rowspan="<?=$colSpan ?>" align="right"><?=number_format($trims_cost,2); ?></td>
						<td width="60" rowspan="<?=$colSpan ?>" align="right"><?=number_format($wash_cost,2); ?></td>
						<td width="60" rowspan="<?=$colSpan ?>" align="right"><?=number_format($embel_cost,2); ?></td>
                        <td width="60" rowspan="<?=$colSpan ?>" align="right"><?=number_format($courier_cost,2); ?></td>
                        
						<td width="60" rowspan="<?=$colSpan ?>" align="right"><?=number_format($total_other_cost,2); ?></td>
						<td width="60" rowspan="<?=$colSpan ?>" align="right"><?=number_format($total_cost,2); ?></td>
						<td width="50" rowspan="<?=$colSpan ?>" align="right"><?=number_format($price_dzn,2); ?></td>
						<? } ?>
					</tr><?
					$k++;
					$total_fabric_total += $val['val'];
				}
			}
			 ?>
			 <tr>
			 	<th colspan="7" align="center">Total Fabrics Cost</th>
			 	<th align="right"><?=number_format($total_fabric_total,2); ?></th>
			 </tr>
		</table>
		<div style="width: 1250px; margin-top: 10px; margin-bottom: 10px">
			<div style="width: 400px; float: left;">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:400px;" rules="all">
					<thead>
						<th colspan="6">Wash Cost Break Up</th>
					</thead>
					<tr>
						<th colspan="5" align="center">Stable</th>
						<th>Cost</th>
					</tr>
					<? foreach ($EmbData as $index => $row) { ?>						
						<tr>
							<td width="200"><?=$emblishment_wash_type[$row["emb_type"]]; ?></td>
							<td width="40"></td>
							<td width="40"></td>
							<td width="40"></td>
							<td width="40"></td>
							<td width="40"><?=number_format($row["amount"],2); ?></td>
						</tr>						
					<?
						$totaldznamount += $row["amount"];
					 } 
					 ?>
					<tr>
						<th colspan="5" align="center">Total Wash Cost</th>
						<th align="right"><?= number_format($totaldznamount,2); ?></th>
					</tr>			
				</table>
			</div>
			<div style="width: 400px; float: left;">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:400px;" rules="all">
					<tr>
						<th colspan="2">Sketch</th>
					</tr>
					<tr>
						<? foreach ($image_location as $row) { ?>
							<td width="200"><img  src='../../<? echo $row[csf("image_location")]; ?>' height='200' width='190' /></td>
						<? } ?>
					</tr>			
				</table>
			</div>
			<div style="width: 450px; float: left;">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:450px;" rules="all">
					<thead>
						<th colspan="6">TRIMS DETAILS COST</th>
					</thead>
					<tr>
						<th width="40">Sl</th>
						<th width="200">Items Description</th>
						<th width="50">Uom</th>
						<th width="50">Cons/Pcs</th>
						<th width="50">Unit Price</th>
						<th>TTL Cost</th>
					</tr>
					<?
						$i=1;
						foreach ($TrimData as $index => $row) { ?>
							<tr>
								<td width="40"><?=$i; ?></td>
								<td width="200"><?=$trim_group[$row["trim_group"]];  ?></td>
								<td width="50"><?=$unit_of_measurement[$row["uom"]];  ?></td>
								<td width="50"><?=number_format($row["cons_dzn_gmts"],2);?></td>
								<td width="50"><?=number_format($row["rate"],2);  ?></td>
								<td><?=number_format($row["amount"],2); ?></td>
							</tr>
						<? 
							$i++;
							$totalAmount+=$row["amount"];
						}
					?>
					<tr>
						<th colspan="5" align="center">Trims Total</th>
						<th width="50"><?=number_format($totalAmount,2); ?></th>
					</tr>			
				</table>
			</div>	
		</div>
	<?
	//echo signature_table(109, $cbo_company_name, "850px");
	
	
	$messageBody=ob_get_contents( );
	ob_clean();
	
	if($is_mail_send==1){
		
 	$buyerData = sql_select("select b.USER_EMAIL,a.BRAND_ID from electronic_approval_setup a,USER_PASSWD b where b.id=a.user_id and a.page_id=1997 and a.is_deleted=0 ");
		foreach($buyerData as $row){
			foreach(explode(',',$row[BRAND_ID]) as $brand){
				if($brand==$brand_id){$mailAddress[$row[USER_EMAIL]]=$row[USER_EMAIL];}
			}
		}
		
		$sql = "select ID,USER_EMAIL,BRAND_ID from USER_PASSWD where STATUS_ACTIVE=1 and id=$INSERTED_BY";
		$sqlResult = sql_select($sql);
		foreach($sqlResult as $row){
			$mailAddress[$row[USER_EMAIL]]=$row[USER_EMAIL];
		}

		$teamSql="select id,TEAM_LEADER_EMAIL  from lib_marketing_team where project_type=2 and team_type in (0,1) and status_active =1 and is_deleted=0 and id in(select  team_id from lib_mkt_team_member_info where USER_TAG_ID =$INSERTED_BY  and status_active =1 and is_deleted=0) and TEAM_LEADER_EMAIL is not null";
		$teamSqlResult = sql_select($teamSql);
		foreach($teamSqlResult as $row){
			$mailAddress[$row[TEAM_LEADER_EMAIL]]=$row[TEAM_LEADER_EMAIL];
		}

		
		require_once('../../../mailer/class.phpmailer.php');
		require_once('../../../auto_mail/setting/mail_setting.php');
		$to=implode(',',$mailAddress);
		$subject="Quick Costing";
		$header=mailHeader();
		
		echo sendMailMailer( $to, $subject, $messageBody,'','' );
		echo " Mail Address: ".$to;

 	}
	else
	{
		//echo $messageBody;
		if($is_excel==1)
		{
		$user_id=$_SESSION['logic_erp']['user_id'];
		$report_category=100;
	
		//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
		foreach (glob("qcw*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename="qcw".$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, $messageBody);
		 echo "$messageBody####$filename####$report_category";
		}
		else{
			echo $messageBody;
		}
		
	}
	?>
        
        <?
	
	exit();
}
if($action=="quick_costing_print3")
{
	//extract($_REQUEST);
	extract(check_magic_quote_gpc($_REQUEST));
 //	echo load_html_head_contents("Quick Costing Print", "../../../", 1, 1,'','','');
	//extract(check_magic_quote_gpc( $process ));
	//echo $data; die;
	$qc_no="";
	 $data=explode('*',$data);
	$qc_no=str_replace("'","",$data[0]);
	$txt_costSheetNo=str_replace("'","",$data[1]);
	$company=str_replace("'","",$data[2]);
	$revise_no=str_replace("'","",$data[3]);
	$is_excel=str_replace("'","",$data[5]);
	$path='';
	$isPath=true;
	if( count($data) > 6) $path=str_replace("'","",$data[6]);
	if(empty($path))
	{
		$path="../../";
		$isPath=false;
	}
	 
	
	//if($qc_no=="") $qc_no=''; else $qc_no=" and a.qc_no=".$qc_no."";
	//if($costSheetNo=="") $costSheetNo=''; else $costSheetNo=" and a.cost_sheet_no=".$costSheetNo."";

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_name_arr=return_library_array( "select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0",'id','supplier_name');
	$season_name_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0",'id','season_name');
	$brand_name_arr=return_library_array( "select id, brand_name from lib_buyer_brand where status_active=1 and is_deleted=0",'id','brand_name');
	$trim_group=return_library_array( "select item_name,id from lib_item_group", "id", "item_name");
	$color_arr=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name"); 
	
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
	
	$mstSql = "SELECT id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, inquery_id, style_ref, buyer_id, uom, cons_basis, season_id, season_year, brand_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, pre_cost_sheet_id, revise_no, option_id, buyer_remarks, option_remarks, body_color_id,INSERTED_BY from qc_mst where qc_no='$qc_no' and status_active=1 and is_deleted=0";
	 // echo $mstSql;  
	$master_data=sql_select($mstSql);
	$buyer_name=$master_data[0][csf('buyer_id')];
	$style_description=$master_data[0][csf('style_des')];
	
	$lib_item_id=$master_data[0][csf('lib_item_id')];
	$inquery_id=$master_data[0][csf('inquery_id')];
	$INSERTED_BY=$master_data[0][csf('INSERTED_BY')];
	$brand_id=$master_data[0][csf('brand_id')];
	$tgt_price=$master_data[0][csf('tgt_price')];
	$mstconuom=$unit_of_measurement[$master_data[0][csf('uom')]];
	
	$sqlInquiry="select gmts_item from wo_quotation_inquery where status_active=1 and is_deleted=0 and id='$inquery_id'";
	$sqlInquiryRes=sql_select($sqlInquiry);
	$inquerygmtsitem_id=$sqlInquiryRes[0][csf('gmts_item')];
	if($master_data[0][csf('buyer_id')]!=0) $addseasonYear='-'.$master_data[0][csf('season_year')]; else $addseasonYear="";
	
	$readyToAppArr=return_library_array("select cost_sheet_id, ready_to_approve from qc_confirm_mst where cost_sheet_id='$qc_no' and status_active=1 and is_deleted=0","cost_sheet_id","ready_to_approve"); 
	$unAppReqArr=return_library_array("select booking_id, approval_cause from fabric_booking_approval_cause where booking_id='$qc_no' and entry_form=28 and status_active=1 and is_deleted=0","booking_id","approval_cause");
	
	$image_location = sql_select("SELECT image_location from common_photo_library where file_type=1 and form_name='quotation_inquery_front_image' and master_tble_id='$inquery_id'");
	ob_start();
	?>
		 
        <table width="1350">
			<tr><th style="text-align: center;">Marketing Costing Sheet</th></tr>
			<tr><th style="text-align: center;"><?=$company_arr[$company]; ?> </th></tr>
			<tr><th style="text-align: center;"><?=$buyer_arr[$buyer_name]; ?> </th></tr>
		</table>
		<table width="1350">
			<tr>
				<td width="120">Cost Sheet No :  </td>
				<td width="170"><?=$txt_costSheetNo; ?></td>
				<td width="120"> </td>
				<td width="170"></td>
				<td width="120"> </td>
				<td width="170"></td>
                <td width="120">Revise no: : </td>
				<td><?=$revise_no;?></td>
			</tr>
			<tr>
				<td width="120">Master Style : </td>
				<td width="170"><?=$master_data[0][csf('style_ref')]; ?></td>
				<td width="120">Season : </td>
				<td width="170"><?=$season_name_arr[$master_data[0][csf('season_id')]].$addseasonYear; ?></td>
				<td width="120"> Brand : </td>
				<td width="170"><?=$brand_name_arr[$master_data[0][csf('brand_id')]]; ?></td>
                <td width="120">Costing Date : </td>
				<td><?=change_date_format($master_data[0][csf('costing_date')],'yyyy-mm-dd','-'); ?></td>
			</tr>
			<tr>
				<td>Currency : </td>
				<td>USD</td>
                <td>Inquiry Item : </td>
				<td><?=$garments_item[$inquerygmtsitem_id]; ?></td>             
                <td>Body/Wash Color</td>
				<td><?=$color_arr[$master_data[0][csf('body_color_id')]]; ?></td>
				<td>Costing Per : </td>
				<td>1 PCS</td>
			</tr>
			<tr>
				<td>Product Item: </td>
				<td></td>
                <td>Initial QTY: </td>
				<td><?=$master_data[0][csf('offer_qty')];; ?></td>             
                <td></td>
				<td></td>
				<td>Buyer TGT Price: </td>
				<td><?=$tgt_price;?></td>
			</tr>
            
		</table>
	
			<? 
			
			$consSql="select id, mst_id, item_id, type, particular_type_id, consumption, ex_percent, tot_cons, uom, rate, value, fab_id, description, use_for, fc_per, body_part_id, supp_ref from qc_cons_rate_dtls where mst_id='$qc_no' and status_active=1 and is_deleted=0 order by id ASC";
			
			
			$consSqlData=sql_select($consSql); $cons_rate_fab_arr=array(); $EmbData=array(); $TrimData=array(); $tot_rows=0; $libFabId="";
			foreach ($consSqlData as $row)
			{
				if($row[csf("type")]==1)
				{
					if($row[csf("tot_cons")]>0)
					{
						$cons_rate_fab_arr[$row[csf("description")]][$row[csf("use_for")]]['val']+=$row[csf("value")];
						$cons_rate_fab_arr[$row[csf("description")]][$row[csf("use_for")]]['cons']+=$row[csf("tot_cons")];
						$cons_rate_fab_arr[$row[csf("description")]][$row[csf("use_for")]]['fc_per']+=$row[csf("fc_per")];
						$cons_rate_fab_arr[$row[csf("description")]][$row[csf("use_for")]]['rate']+=$row[csf("rate")];
						$cons_rate_fab_arr[$row[csf("description")]][$row[csf("use_for")]]['fab_id']=$row[csf("fab_id")];
						$tot_rows++;
						$libFabId.=$row[csf("fab_id")].",";
					}
				}
				if($row[csf("type")]==2)
				{
					$cons_rate_sp_arr[$row[csf("item_id")]].=$row[csf("particular_type_id")].'_'.$row[csf("fab_id")].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf("body_part_id")].'_'.$row[csf("consumption")].'_'.$row[csf("ex_percent")].'_'.$row[csf("tot_cons")].'_'.$row[csf("rate")].'_'.$row[csf("value")].'##';
					

					   if( $row[csf("particular_type_id")]==1 || $row[csf("particular_type_id")]==2)
						{
				     	$spworkqty=$row[csf("tot_cons")];
						$spworkamount=$row[csf("value")];
						$spwork[$row[csf("particular_type_id")]]['spwork_name']=$row[csf("particular_type_id")];
						$spwork[$row[csf("particular_type_id")]]['spwork_type']=$row[csf("fab_id")];
						$spwork[$row[csf("particular_type_id")]]['cons_dzn_gmts']+=$row[csf("tot_cons")];
						$spwork[$row[csf("particular_type_id")]]['rate']=$row[csf("rate")];
						$spwork[$row[csf("particular_type_id")]]['amount']+=$row[csf("value")];
						$spwork[$row[csf("particular_type_id")]]['tot_cons']+=$spworkqty;
						$spwork[$row[csf("particular_type_id")]]['tot_amount']+=$spworkamount;
						$totspworkAmount+=$spworkamount;
						}
					
				}
				if($row[csf("type")]==3)
				{
					if($row[csf("tot_cons")]>0)
					{
						$washqty=$row[csf("tot_cons")];
						$washamount=$row[csf("value")];
						$EmbData[$row[csf("id")]]['emb_name']=3;
						$EmbData[$row[csf("id")]]['emb_type']=$row[csf("particular_type_id")];
						$EmbData[$row[csf("id")]]['cons_dzn_gmts']=$row[csf("tot_cons")];
						$EmbData[$row[csf("id")]]['rate']=$row[csf("rate")];
						$EmbData[$row[csf("id")]]['amount']=$row[csf("value")];
						$EmbData[$row[csf("id")]]['tot_cons']=$washqty;
						$EmbData[$row[csf("id")]]['tot_amount']=$washamount;
						$totWashAmount+=$washamount;
					}
				}
				if($row[csf("type")]==4)
				{
					if($row[csf("tot_cons")]>0)
					{
						$trim_qty=$row[csf("tot_cons")];
						$trim_amount=$row[csf("value")];
						$TrimData[$row[csf('id')]]['trim_group']=$row[csf('particular_type_id')];
						$TrimData[$row[csf('id')]]['description']=$row[csf('description')];
						$TrimData[$row[csf('id')]]['brand_sup_ref']=$row[csf('supp_ref')];
						$TrimData[$row[csf('id')]]['cons_dzn_gmts']=$row[csf("tot_cons")];
						$TrimData[$row[csf('id')]]['uom']=$row[csf("uom")];
						$TrimData[$row[csf('id')]]['rate']=$row[csf('rate')];
						$TrimData[$row[csf('id')]]['amount']=$row[csf("value")];
						$TrimData[$row[csf('id')]]['tot_cons']=$trim_qty;
						$TrimData[$row[csf('id')]]['tot_amount']=$trim_amount;
						$totTrim+=$row[csf("tot_cons")];
						$totTrimAmount+=$trim_amount;
					}
				}
			}
			
			$libFabIds=chop($libFabId,','); $libFabIdCond="";
			if($db_type==2 && $tot_rows>1000)
			{
				$libFabIdCond=" and (";
				$libFabIdsArr=array_chunk(explode(",",$libFabIds),999);
				foreach($libFabIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$libFabIdCond.=" id in($ids) or ";
					
				}
				$libFabIdCond=chop($libFabIdCond,'or ');
				$libFabIdCond.=")";
			}
			else
			{
				$libFabIdCond=" and id in ($libFabIds)";
			}
			
			$sqlRd="select id, type, construction, gsm_weight, weight_type, design, fabric_ref, rd_no, color_range_id, full_width, cutable_width,shrinkage_l,shrinkage_w from lib_yarn_count_determina_mst where status_active=1 and is_deleted=0 $libFabIdCond";
			$sqlRdData=sql_select($sqlRd);
			$cutableArr=array();
			foreach($sqlRdData as $frow)
			{
				$cutableArr[$frow[csf('id')]]['cutable_width']=$frow[csf('cutable_width')];
				$cutableArr[$frow[csf('id')]]['shrinkage_l']=$frow[csf('shrinkage_l')];
				$cutableArr[$frow[csf('id')]]['shrinkage_w']=$frow[csf('shrinkage_w')];
			}
			unset($sqlRdData);
			
			//  print_r($spwork);
			?>
		<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:1350px; margin-top: 10px" rules="all">
			
			<thead>
				<th width="530">Fabrication</th>
				<th width="100">Fabric Usage</th>
				<th width="60">Cutable Width</th>
	
				<th width="50">Price/ <?=$mstconuom; ?></th>
				<th width="50">Fina. Char</th>
				<th width="60">Cons. (<?=$mstconuom; ?>)</th>
				<th width="60"> Fab Cost</th>
				<th width="60">Total Fabric Price</th>				
				<th width="60">TTL Trims Cost</th>
				<th width="60">Wash</th>
				<th width="60">Emb.</th>               
				<th width="60">Lab Cost</th>
				<th width="60">Courier Cost</th>
				<th width="60">CM</th>
				<th width="50">FOB/ 1 PCS</th>
			</thead>
			<?
			$sqlSum="select id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_wash_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_courier_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost from qc_tot_cost_summary where mst_id='$qc_no' and status_active=1 and is_deleted=0";
			$sqlSumData=sql_select($sqlSum);
			$fabric_req_amount=$sqlSumData[0][csf('tot_fab_cost')];
			$cm_cost=$sqlSumData[0][csf('tot_cm_cost')];
			$cm_cost_amount=$sqlSumData[0][csf('tot_cm_cost')];
			$trims_cost=$sqlSumData[0][csf('tot_accessories_cost')];
			
			$totTrimAmount=$sqlSumData[0][csf('tot_accessories_cost')];
			$wash_cost=$sqlSumData[0][csf('tot_wash_cost')];
			$totWashAmount=$sqlSumData[0][csf('tot_wash_cost')];
			$embel_cost=$sqlSumData[0][csf('tot_sp_operation_cost')];
			$total_other_cost=$sqlSumData[0][csf('tot_other_cost')];
			$courier_cost=$sqlSumData[0][csf('tot_courier_cost')];
			$other_cost_value=$sqlSumData[0][csf('tot_other_cost')];
			$commercial_amount=0;
			$price_dzn=$sqlSumData[0][csf('tot_fob_cost')];
			$total_price=$sqlSumData[0][csf('tot_fob_cost')];
			$total_cost=$sqlSumData[0][csf('tot_cost')];
			$lab_test_cost=$sqlSumData[0][csf('tot_lab_test_cost')];
			$k=1;
			$colSpan=count($cons_rate_fab_arr)+1;

			foreach ($cons_rate_fab_arr as $fabric_des=>$useForData) 
			{
				foreach($useForData as $useFor=>$val) 
				{
					?>
					<tr>
						<td width="530"><?=$fabric_des.",".$cutableArr[$val['fab_id']]['shrinkage_l'].",".$cutableArr[$val['fab_id']]['shrinkage_w']; ?></td>
						<td width="100"><?=$useFor; ?></td>
						<td width="60" align="center" title="<?=$val['fab_id']; ?>"><?=$cutableArr[$val['fab_id']]['cutable_width']; ?></td>

						<td width="50" align="right"><?=number_format($val['rate'],2); ?></td>
						<td width="50" align="right"><?=$val['fc_per']; ?>&percnt;</td>
						<td width="60" align="right"><?=number_format($val['cons'],2); ?></td>
						<td width="60" align="right"><?=number_format($val['cons']*$val['rate'],2); ?></td>
						<td width="60" align="right"><?=number_format($val['val'],2); ?></td>
						<? if($k==1){ ?>
						
						<td width="60" rowspan="<?=$colSpan ?>" align="right"><?=number_format($trims_cost,2); ?></td>
						<td width="60" rowspan="<?=$colSpan ?>" align="right"><?=number_format($wash_cost,2); ?></td>
						<td width="60" rowspan="<?=$colSpan ?>" align="right"><?=number_format($embel_cost,2); ?></td>
						<td width="60" rowspan="<?=$colSpan ?>" align="right"><?=number_format($lab_test_cost,2); ?></td>
                        <td width="60" rowspan="<?=$colSpan ?>" align="right"><?=number_format($courier_cost,2); ?></td>                        
							
						<td width="60" rowspan="<?=$colSpan ?>" align="right"><?=number_format($cm_cost,2); ?></td>		
						<td width="50" rowspan="<?=$colSpan ?>" align="right"><?=number_format($price_dzn,2); ?></td>
						<? } ?>
					</tr><?
					$k++;
					$total_fabric_total += $val['val'];
				}
			}
			 ?>
			 <tr>
			 	<th colspan="5" align="center">Total Fabrics Cost</th>
			 	<th align="right"><?=number_format($total_fabric_total,2); ?></th>
			 </tr>
		</table>
		<div style="width: 1250px; margin-top: 10px; margin-bottom: 10px">
			<div style="width: 400px; float: left;">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:400px;" rules="all">
					<thead>
						<th colspan="6">Wash Cost Break Up</th>
					</thead>
					<tr>
						<th colspan="5" align="center">Stable</th>
						<th>Cost</th>
					</tr>
					<? foreach ($EmbData as $index => $row) { ?>						
						<tr>
							<td width="200" colspan="5"><?=$emblishment_wash_type[$row["emb_type"]]; ?></td>
						
							<td width="60"><?=number_format($row["amount"],2); ?></td>
						</tr>						
					<?
						$totaldznamount += $row["amount"];
					 } 
					 ?>
					<tr>
						<th colspan="5" align="center">Total Wash Cost</th>
						<th align="right"><?= number_format($totaldznamount,2); ?></th>
					</tr>			
				</table>
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:400px;" rules="all">
					<thead>
						<th colspan="6">Special Operation Cost</th>
					</thead>
					<tr>
						<th colspan="5" align="center">Stable</th>
						<th>Cost</th>
					</tr>
					<?
					$totaldznamount=0;
					 foreach ($spwork as $index => $row) { ?>						
						<tr>
							<td width="200" colspan="5"><?=$emblishment_name_array[$row["spwork_name"]]; ?></td>
						
							<td width="60"><?=number_format($row["amount"],2); ?></td>
						</tr>						
					<?
						$totaldznamount += $row["amount"];
					 } 
					 ?>
					<tr>
						<th colspan="5" align="center">Total Special Cost</th>
						<th align="right"><?= number_format($totaldznamount,2); ?></th>
					</tr>			
				</table>
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:400px;" rules="all">
					<thead>
						<th colspan="2">Financial Cost</th>
					</thead>
					
					<?
					$totaldznamount=0;
					 $financial_data=sql_select("select   commission_cost, commercial_cost,  lc_cost from qc_item_cost_summary where mst_id='$qc_no' and status_active=1 and is_deleted=0");
							?>
					
						<tr>
							<td width="200">Comml Cost</td>
							<td width="60"><?=number_format($financial_data[0][csf("commercial_cost")],4);; ?></td>
						</tr>	
						<tr>
							<td width="200">Commision Cost</td>
							<td width="60"><?=number_format($financial_data[0][csf("commission_cost")],4);; ?></td>
						
						</tr>	
						<tr>
							<td width="200">L/C Cost</td>
							<td width="60"><?=number_format($financial_data[0][csf("lc_cost")],4);; ?></td>
						
						</tr>						
					
					<tr>
						<th  align="center">Total Special Cost</th>
						<th align="right"><?= number_format($financial_data[0][csf("commercial_cost")]+$financial_data[0][csf("commission_cost")]+$financial_data[0][csf("lc_cost")],2); ?></th>
					</tr>			
				</table>
			</div>
			<div style="width: 400px; float: left;">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:400px;" rules="all">
					<tr>
						<th colspan="2">Sketch</th>
					</tr>
					<tr>
						<? 
						
						foreach ($image_location as $row) { ?>
							<td width="200"><img  src='<?echo $path;?><? echo $row[csf("image_location")]; ?>' height='200' width='190' /></td>
						<? } ?>
					</tr>			
				</table>
			</div>
			<div style="width: 450px; float: left;">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:450px;" rules="all">
					<thead>
						<th colspan="6">TRIMS DETAILS COST</th>
					</thead>
					<tr>
						<th width="40">Sl</th>
						<th width="200">Items Description</th>
						<th width="50">Uom</th>
						<th width="50">Cons/Pcs</th>
						<th width="50">Unit Price</th>
						<th>TTL Cost</th>
					</tr>
					<?
						$i=1;
						foreach ($TrimData as $index => $row) { ?>
							<tr>
								<td width="40"><?=$i; ?></td>
								<td width="200"><?=$trim_group[$row["trim_group"]];  ?></td>
								<td width="50"><?=$unit_of_measurement[$row["uom"]];  ?></td>
								<td width="50"><?=number_format($row["cons_dzn_gmts"],2);?></td>
								<td width="50"><?=number_format($row["rate"],2);  ?></td>
								<td><?=number_format($row["amount"],2); ?></td>
							</tr>
						<? 
							$i++;
							$totalAmount+=$row["amount"];
						}
					?>
					<tr>
						<th colspan="5" align="center">Trims Total</th>
						<th width="50"><?=number_format($totalAmount,2); ?></th>
					</tr>			
				</table>
			</div>	
		</div>
	<?
	//echo signature_table(109, $cbo_company_name, "850px");
	
	
	$messageBody=ob_get_contents( );
	ob_clean();
	
	if($is_mail_send==1){
		
 	$buyerData = sql_select("select b.USER_EMAIL,a.BRAND_ID from electronic_approval_setup a,USER_PASSWD b where b.id=a.user_id and a.page_id=1997 and a.is_deleted=0 ");
		foreach($buyerData as $row){
			foreach(explode(',',$row[BRAND_ID]) as $brand){
				if($brand==$brand_id){$mailAddress[$row[USER_EMAIL]]=$row[USER_EMAIL];}
			}
		}
		
		$sql = "select ID,USER_EMAIL,BRAND_ID from USER_PASSWD where STATUS_ACTIVE=1 and id=$INSERTED_BY";
		$sqlResult = sql_select($sql);
		foreach($sqlResult as $row){
			$mailAddress[$row[USER_EMAIL]]=$row[USER_EMAIL];
		}

		$teamSql="select id,TEAM_LEADER_EMAIL  from lib_marketing_team where project_type=2 and team_type in (0,1) and status_active =1 and is_deleted=0 and id in(select  team_id from lib_mkt_team_member_info where USER_TAG_ID =$INSERTED_BY  and status_active =1 and is_deleted=0) and TEAM_LEADER_EMAIL is not null";
		$teamSqlResult = sql_select($teamSql);
		foreach($teamSqlResult as $row){
			$mailAddress[$row[TEAM_LEADER_EMAIL]]=$row[TEAM_LEADER_EMAIL];
		}

		
		require_once('../../../mailer/class.phpmailer.php');
		require_once('../../../auto_mail/setting/mail_setting.php');
		$to=implode(',',$mailAddress);
		$subject="Quick Costing";
		$header=mailHeader();
		
		echo sendMailMailer( $to, $subject, $messageBody,'','' );
		echo " Mail Address: ".$to;

 	}
	else
	{
		//echo $messageBody;
		if($is_excel==1)
		{
			$user_id=$_SESSION['logic_erp']['user_id'];
			$report_category=100;
		
			//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
			foreach (glob("qcw*.xls") as $filename) {
			//if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
			}
			//---------end------------//
			$name=time();
			$filename="qcw".$user_id."_".$name.".xls";
			$create_new_doc = fopen($filename, 'w');
			$is_created = fwrite($create_new_doc, $messageBody);
			 echo "$messageBody####$filename####$report_category";
		}
		else
		{
			echo $messageBody;
		}
		
	}
	?>
        
        <?
	
	exit();
}
if($action=="quick_costing_print2")
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
	
	$sql_mst="select id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, style_ref, buyer_id, uom, cons_basis, season_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, revise_no, option_id, option_remarks, buyer_remarks from qc_mst where status_active=1 and is_deleted=0 and qc_no='$data[0]'";
	$sqlMst_res=sql_select($sql_mst);
	
	$qc_mst_id=$sqlMst_res[0][csf('id')];
	
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
    <style>
		.watermark {
			opacity: 0.5;
			color: BLACK;
			position: relative;
			font-size: 18px;
			bottom: 0;
			right: 0;
		}
	</style>
    <div style="width:1000px;">
        <table width="1000" cellspacing="0" border="1" style="margin-right:-10px;" rules="all" class="rpt_table">
            <tr>
                <td colspan="8" align="center" style="font-size:18px"><strong><? //echo $company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr class="form_caption">
            	<td colspan="8" align="center" style="font-size:14px"><strong>ESTIMATE COST SHEET (<? echo $unit_of_measurement[$sqlMst_res[0][csf('uom')]]; ?>)</strong></td>
            </tr>
            <tr>
            	<td width="100"><strong>Style Ref.:</strong></td><td width="145px"><? echo $sqlMst_res[0][csf('style_ref')]; ?></td>
                <td width="100"><strong>Department:</strong></td><td width="145px"><? echo $department_arr[$sqlMst_res[0][csf('department_id')]]; ?></td>
                <td width="100"><strong>Template Name:</strong></td><td width="145px"><? echo $template_name_arr[$sqlMst_res[0][csf('temp_id')]]; ?></td>
                <td width="100"><strong>Cons. Basis:</strong></td><td width="145px"><? echo $qc_consumption_basis[$sqlMst_res[0][csf('cons_basis')]]; ?></td>
            </tr>
            
            <tr>
                <td><strong>Buyer:</strong></td> <td><? echo $buyer_arr[$sqlMst_res[0][csf('buyer_id')]]; ?></td>
                <td><strong>Season:</strong></td><td><? echo $season_arr[$sqlMst_res[0][csf('season_id')]]; ?></td>
                <td><strong>Style Description:</strong></td><td><? echo $sqlMst_res[0][csf('style_des')]; ?></td>
                <td><strong>Costing Date:</strong></td><td><? echo change_date_format($sqlMst_res[0][csf('costing_date')]); ?></td>
            </tr>
            <tr>
                <td><strong>Cost Sheet No:</strong></td><td><? echo $sqlMst_res[0][csf('cost_sheet_no')]; ?></td>
                <td><strong>Target Price($):</strong></td><td><? echo number_format($sqlMst_res[0][csf('tgt_price')],4,'.',''); ?></td>
                <td><strong>Quoted Price($):</strong></td><td><? echo number_format($sqlMst_res[0][csf('quoted_price')],4,'.',''); ?></td>
                <td><strong>Exchange Rate:</strong></td><td><? echo number_format($sqlMst_res[0][csf('exchange_rate')],4,'.',''); ?></td>
            </tr>
            <tr style="border-bottom:1px solid black;">
                <td><strong>Offer Qty.:</strong></td><td><? echo number_format($sqlMst_res[0][csf('offer_qty')],4,'.',''); ?></td>
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
			$mk=0; $nk=11;
			foreach($templateItem_arr as $item_id=>$val)
			{
				$item_name=$lib_temp_arr[$item_id];
				if(strpos($item_name,'TOP') !== false)
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
				if(strpos($item_name,'TOP') !== false)
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
			$sql_cons_rate="select id, item_id, type, particular_type_id, consumption, ex_percent, tot_cons, unit, is_calculation, rate, rate_data, value from qc_cons_rate_dtls where mst_id='$data[0]' and status_active=1 and is_deleted=0 order by id asc";
			$sql_result_cons_rate=sql_select($sql_cons_rate); $item_arr=array(); $cons_rate_fab_arr=array(); $cons_rate_sp_arr=array(); $cons_rate_ac_arr=array();
			foreach ($sql_result_cons_rate as $rowConsRate)
			{
				$item_arr[$rowConsRate[csf("item_id")]]=$rowConsRate[csf("item_id")];
				if($rowConsRate[csf("type")]==1)
				{
					$cons_rate_fab_arr[$rowConsRate[csf("item_id")]].=$rowConsRate[csf("particular_type_id")].'_'.$rowConsRate[csf("consumption")].'_'.$rowConsRate[csf("unit")].'_'.$rowConsRate[csf("rate")].'_'.$rowConsRate[csf("ex_percent")].'_'.$rowConsRate[csf("rate_data")].'_'.$rowConsRate[csf("is_calculation")].'_'.$rowConsRate[csf("tot_cons")].'##';
				}
				
				if($rowConsRate[csf("particular_type_id")]!='')
				{
					if($rowConsRate[csf("particular_type_id")]!=0)
					{
						if($rowConsRate[csf("type")]==2)
						{
							$cons_rate_sp_arr[$rowConsRate[csf("item_id")]].=$rowConsRate[csf("particular_type_id")].'_'.$rowConsRate[csf("consumption")].'_'.$rowConsRate[csf("unit")].'_'.$rowConsRate[csf("rate")].'_'.$rowConsRate[csf("ex_percent")].'_'.$rowConsRate[csf("tot_cons")].'##';
						}
						
						if($rowConsRate[csf("type")]==3)
						{
							$cons_rate_ac_arr[$rowConsRate[csf("item_id")]].=$rowConsRate[csf("particular_type_id")].'_'.$rowConsRate[csf("consumption")].'_'.$rowConsRate[csf("unit")].'_'.$rowConsRate[csf("rate")].'_'.$rowConsRate[csf("ex_percent")].'_'.$rowConsRate[csf("tot_cons")].'##';
						}
					}
				}
			}
			$yarn_dtls_arr=array(); $other_cost_arr=array();
			?>
            	<td colspan="3">
                <? foreach($itemIndex_arr as $ind=>$itemId) { ?>
                	<table width="650" cellspacing="0" border="1" rules="all" class="rpt_table">
                    	<thead>
                        	<th width="80">Item</th>
                            <th width="180">Item Description</th>
                            <th width="70">Unit</th>
                            <th width="80">Cons.</th>
                            <th width="70">Ex.%</th>
                            <th width="80">Tot. Cons.</th>
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
							$row_value=$exFab_data[7]*$exFab_data[3];
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
                                <td align="right"><? echo number_format($exFab_data[4],4); ?></td>
                                <td align="right"><? echo number_format($exFab_data[7],4); ?></td>
                                <td align="right"><? echo number_format($exFab_data[3],4); ?></td>
                                <td align="right"><? echo number_format($row_value,4); ?></td>
							</tr>
							<?  $q++;
							
							//all_data=$('#txt_yarn_dtls1').val()+'~~'+$('#txt_ratePerKg1').val()+'~~'+$('#txt_yarn_costPer1').val()+'~~'+$('#txt_yarn_cost1').val()+'~~'+$('#txt_yarn_dtls2').val()+'~~'+$('#txt_ratePerKg2').val()+'~~'+$('#txt_yarn_costPer2').val()+'~~'+$('#txt_yarn_cost2').val()+'~~'+$('#txt_yarn_dtls3').val()+'~~'+$('#txt_ratePerKg3').val()+'~~'+$('#txt_yarn_costPer3').val()+'~~'+$('#txt_yarn_cost3').val()+'~~'+$('#txt_tot_yarn_costPer').val()+'~~'+$('#txt_tot_yarn_cost').val() 13
							//+'~~'+$('#txt_knit_charge').val()+'~~'+$('#txt_dyeing_charge').val()+'~~'+$('#txt_aop_charge').val()+'~~'+$('#txt_fin_charge').val()+'~~'+$('#txt_other_cost').val()+'~~'+$('#txt_tot_cost').val()+'~~'+$('#txt_process_loss_per').val()+'~~'+$('#txt_process_loss').val()+'~~'+$('#txt_tot_rate').val();
							
							if($exFab_data[5]!="") $edata=explode("~~",$exFab_data[5]); else $edata="";
							
							$ydata=""; $other_data="";
							
							//if($ydata=="") $ydata=$edata[0].'~~'.$edata[1].'~~'.$edata[2].'~~'.$edata[3]; else if ($edata[5]!="" && $ydata!="") $ydata.='@@@'.$edata[4].'~~'.$edata[5].'~~'.$edata[6].'~~'.$edata[7]; else $ydata.='@@@'.$edata[8].'~~'.$edata[9].'~~'.$edata[10].'~~'.$edata[11];
							//$ydata=$edata[0].'~~'.$edata[1].'~~'.$edata[2].'~~'.$edata[3].'~~'.$edata[4].'~~'.$edata[5].'~~'.$edata[6].'~~'.$edata[7].'~~'.$edata[8].'~~'.$edata[9].'~~'.$edata[10].'~~'.$edata[11];
							
							$yarn_dtls_arr[$bodyName][$edata[0]]=$edata[1].'~~'.$edata[2].'~~'.$edata[3];
							$yarn_dtls_arr[$bodyName][$edata[4]]=$edata[5].'~~'.$edata[6].'~~'.$edata[7];
							$yarn_dtls_arr[$bodyName][$edata[8]]=$edata[9].'~~'.$edata[10].'~~'.$edata[11];	
								//if($other_data=="") $other_data=$edata[14].'~~'.$edata[15].'~~'.$edata[16].'~~'.$edata[17].'~~'.$edata[18]; else $other_data.='@@@'.$edata[14].'~~'.$edata[15].'~~'.$edata[16].'~~'.$edata[17].'~~'.$edata[18];
							if($exFab_data[6]==1)
							{	
								$other_cost_arr[$bodyName][1]=$edata[14];
								$other_cost_arr[$bodyName][2]=$edata[15];
								$other_cost_arr[$bodyName][3]=$edata[16];
								$other_cost_arr[$bodyName][4]=$edata[17];
								$other_cost_arr[$bodyName][5]=$edata[18];
							}
						} 
						
						foreach($special_data as $spData)
						{
							$exSp_data=explode('_',$spData);
							$rowSp_value=$exSp_data[5]*$exSp_data[3];
							$item_wise_value_total+=$rowSp_value;
							?>
							<tr>
                                <td><? echo $emblishment_name_array[$exSp_data[0]]; ?></td>
                                <td><? echo $unit_of_measurement[$exSp_data[2]]; ?></td>
                                <td align="right"><? echo number_format($exSp_data[1],4); ?></td>
                                <td align="right"><? echo number_format($exSp_data[4],4); ?></td>
                                <td align="right"><? echo number_format($exSp_data[5],4); ?></td>
                                <td align="right"><? echo number_format($exSp_data[3],4); ?></td>
                                <td align="right"><? echo number_format($rowSp_value,4); ?></td>
							</tr>
                        <?  $q++; } 
						foreach($accessories_data as $acData)
						{
							$exAc_data=explode('_',$acData);
							$rowAc_value=$exAc_data[5]*$exAc_data[3];
							$item_wise_value_total+=$rowAc_value;
							?>
							<tr>
                                <td><? echo $accessories_arr[$exAc_data[0]]; ?></td>
                                <td><? echo $unit_of_measurement[$exAc_data[2]]; ?></td>
                                <td align="right"><? echo number_format($exAc_data[1],4); ?></td>
                                <td align="right"><? echo number_format($exAc_data[4],4); ?></td>
                                <td align="right"><? echo number_format($exAc_data[5],4); ?></td>
                                <td align="right"><? echo number_format($exAc_data[3],4); ?></td>
                                <td align="right"><? echo number_format($rowAc_value,4); ?></td>
							</tr>
                        <?  $q++; } ?>
                        
                        <tr>
                        	<td colspan="7" align="right"><strong>Total</strong></td>
                            <td align="right"><? echo number_format($item_wise_value_total,4); ?>&nbsp;</td>
                        </tr>
                    </table><br>
                    <table width="850" cellspacing="0" border="0">
                    	<tr>
                        	<td width="500" valign="top">
                                <table width="500" cellspacing="0" border="1" rules="all" class="rpt_table">
                                    <thead>
                                        <tr>
                                            <th colspan="5">Yarn Details</th>
                                        </tr>
                                        <tr>
                                            <th width="100">Body part</th>
                                            <th width="220">Details</th>
                                            <th width="70">Rate Per KG</th>
                                            <th width="70">%</th>
                                            <th>Cost</th>
                                        </tr>
                                    </thead>
                                    <?
                                    foreach($yarn_dtls_arr as $bodyPartName=>$rateData)
                                    {
                                        //$edata=explode("@@@",$rateData);
                                        foreach($rateData as $des=>$rows)
                                        {
                                            $yarndata=explode("~~",$rows);
											if($yarndata[2]!="")
											{
                                            ?>
                                                <tr>
                                                    <td width="100"><? echo $bodyPartName; ?></td>
                                                    <td width="220" style="word-break:break-all"><? echo $des; ?></td>
                                                    <td width="70" align="right"><? echo $yarndata[0]; ?></td>
                                                    <td width="70" align="right"><? echo $yarndata[1]; ?></td>
                                                    <td align="right"><? echo $yarndata[2]; ?></td>
                                                </tr>
                                            <?
											}
                                        }
                                    }
                                    ?>
                                </table>
                            </td>
                            <td>&nbsp;</td>
                            <td width="340" valign="top">
                            	<table width="340" cellspacing="0" border="1" rules="all" class="rpt_table">
                                    <thead>
                                        <tr>
                                            <th colspan="3">Other Cost Details</th>
                                        </tr>
                                        <tr>
                                        	<th width="100">Body part</th>
                                            <th width="150">Details</th>
                                            <th>Cost</th>
                                        </tr>
                                    </thead>
                                    <?
									
									$charge_arr=array(1=>"Knitting Charge",2=>"Dyeing Charge",3=>"AOP Charge",4=>"Finishing Charge",5=>"Other Cost");
                                    foreach($other_cost_arr as $nameBodyPart=>$dataRate)
                                    {
                                       // $exdata=explode("@@@",$dataRate);
                                        foreach($dataRate as $seq=>$orows)
                                        {
                                            //$odata=explode("~~",$orows);
											if($orows!="")
											{
                                            ?>
                                                <tr>
                                                    <td width="100"><? echo $nameBodyPart; ?></td>
                                                    <td width="150" style="word-break:break-all"><? echo $charge_arr[$seq]; ?></td>
                                                    <td align="right"><? echo $orows; ?></td>
                                                </tr>
                                            <?
											}
                                        }
                                    }
                                    ?>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <?
						$buyerLocation_arr=return_library_array("select tuid, agent_location from lib_agent_location","tuid","agent_location");
						
						$sql_buyer=sql_select("select buyer_agent_id, location_id from qc_tot_cost_summary where mst_id='$data[0]' and status_active=1 and is_deleted=0");
					?>
                    <table width="850" cellspacing="0" border="1" rules="all" class="rpt_table">
                    	<tr>
                        	<td width="120"><strong>Buyer Agent</strong></td>
                            <td width="250"><? echo $buyerLocation_arr[$sql_buyer[0][csf('buyer_agent_id')]]; ?>&nbsp;</td>
                            <td rowspan="3" class="watermark">Write Comments.</td>
                        </tr>
                        <tr>
                        	<td><strong>Buyer Location</strong></td>
                            <td><? echo $buyerLocation_arr[$sql_buyer[0][csf('location_id')]]; ?>&nbsp;</td>
                        </tr>
                        <tr>
                        	<td><strong>Date</strong></td>
                            <td>&nbsp;</td>
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
        	<? $option_remarks = $sqlMst_res[0][csf('option_remarks')]; ?>
            <thead>
            	<th>Option Remarks</th>
            </thead>
            <tr>
                <td><? echo $option_remarks; ?></td>
            </tr>
        </table>
        <br>
		<?
		$lib_designation=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");
		
		$data_array=sql_select(" select b.id, b.approved_by, b.approved_no, b.approved_date, b.un_approved_reason, b.current_approval_status, c.user_full_name, c.designation, d.approval_cause from qc_mst a join approval_history b on a.id=b.mst_id join user_passwd c on b.approved_by=c.id left join fabric_booking_approval_cause d on b.id=d.approval_history_id where a.id=$qc_mst_id and b.entry_form=36 order by b.id asc");
		?>
		<table width="500" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr style="border:1px solid black;">
                	<th colspan="6" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                	<th width="20" style="border:1px solid black;">SL</th>
                    <th width="100" style="border:1px solid black;">Name</th>
                    <th width="100" style="border:1px solid black;">Designation</th>
                    <th width="60" style="border:1px solid black;">Approval Status</th>
                    <th width="70" style="border:1px solid black;">Approval Date</th>
                    <th style="border:1px solid black;">Un-Approval Cause</th>
                </tr>
            </thead>
            <tbody>
				<?
                $i=1;
                foreach($data_array as $row)
				{
					$approved_status="";
					if($row[csf('current_approval_status')]==1) $approved_status="Yes"; else $approved_status="No";
					?>
					<tr style="border:1px solid black;">
                        <td style="border:1px solid black;"><? echo $i;?></td>
                        <td style="border:1px solid black; word-break:break-all"><? echo $row[csf('user_full_name')]; ?></td>
                        <td style="border:1px solid black; word-break:break-all"><? echo $lib_designation[$row[csf('designation')]];?></td>
                        <td style="border:1px solid black; word-break:break-all"><? echo $approved_status;?></td>
                        <td style="border:1px solid black; word-break:break-all"><? echo date("d-m-Y h:i:s",strtotime($row[csf('approved_date')]));?></td>
                        <td style="border:1px solid black; word-break:break-all"><? echo $row[csf('approval_cause')];?></td>
					</tr>
					<?
					$i++;
                }
                ?>
            </tbody>
		</table>
        </div>
		<?
		
	exit();
}

if ($action=="unapp_request_popup")
{
	$menu_id=$_SESSION['menu_id'];
	$user_id=$_SESSION['logic_erp']['user_id'];

	echo load_html_head_contents("Un Approval Request","../../../", 1, 1, $unicode);
	extract($_REQUEST);

	$data_all=explode('_',$data);
	$costSheet_id=$data_all[0];
	$unapp_request=$data_all[1];

	//$costSheet_id=return_field_value("id", "wo_pre_cost_mst", "job_no='$job_no' and status_active=1 and is_deleted=0");

	if($unapp_request=="")
	{
		$sql_request="select MAX(id) as id from fabric_booking_approval_cause where entry_form=28 and user_id='$user_id' and booking_id='$costSheet_id' and approval_type=2 and status_active=1 and is_deleted=0";//page_id='$menu_id' and

		$nameArray_request=sql_select($sql_request);
		foreach($nameArray_request as $row)
		{
			$unapp_request=return_field_value("approval_cause", "fabric_booking_approval_cause", "id='".$row[csf('id')]."' and status_active=1 and is_deleted=0");
		}
	}
	?>
    <script>

		$( document ).ready(function() {
			document.getElementById("unappv_request").value='<? echo preg_replace('/[\r\n]+/','\n',$unapp_request); ?>';
		});

		var permission='<? echo $permission; ?>';

		function fnc_appv_entry(operation)
		{
			var unappv_request = $('#unappv_request').val();

			if (form_validation('unappv_request','Un Approval Request')==false)
			{
				if (unappv_request=='')
				{
					alert("Please write request.");
				}
				return;
			}
			else
			{

				var data="action=save_update_delete_unappv_request&operation="+operation+get_submitted_data_string('unappv_request*costSheet_id*page_id*user_id',"../../../");
				//alert (data);return;
				freeze_window(operation);
				http.open("POST","quick_costing_woven_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange=fnc_appv_entry_Reply_info;
			}
		}

		function fnc_appv_entry_Reply_info()
		{
			if(http.readyState == 4)
			{
				var reponse=trim(http.responseText).split('**');
				show_msg(reponse[0]);

				set_button_status(1, permission, 'fnc_appv_entry',1);
				release_freezing();
			}
		}

		function fnc_close()
		{
			unappv_request= $("#unappv_request").val();
			document.getElementById('hidden_appv_cause').value=unappv_request;
			parent.emailwindow.hide();
		}

    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="unappv_request" id="unappv_request" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"></textarea>
                        <Input type="hidden" name="costSheet_id" class="text_boxes" ID="costSheet_id" value="<? echo $costSheet_id; ?>" style="width:30px" />
                        <Input type="hidden" name="page_id" class="text_boxes" ID="page_id" value="<? echo $menu_id; ?>" style="width:30px" />
                        <Input type="hidden" name="user_id" class="text_boxes" ID="user_id" value="<? echo $user_id; ?>" style="width:30px" />
                    </td>
                </tr>
            </table>

            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >
                <tr>
                    <td align="center" class="button_container">
                        <?
						//print_r ($id_up_all);
                            if($id_up!='')
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 1,0,"reset_form('size_1','','','','','');",1);
                            }
                            else
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 0,0,"reset_form('size_1','','','','','');",1);
                            }
                        ?>
                        <input type="hidden" name="hidden_appv_cause" id="hidden_appv_cause" class="text_boxes /">

                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
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

if ($action=="save_update_delete_unappv_request")
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

		$approved_no=return_field_value("MAX(approved_no) as approved_no","approval_history","entry_form=28 and mst_id=$costSheet_id","approved_no")+1;

		$unapproved_request=return_field_value("id","fabric_booking_approval_cause","entry_form=28 and user_id=$user_id and booking_id=$costSheet_id and approval_type=2 and approval_no='$approved_no'");//page_id=$page_id and
		
		//echo "select id from fabric_booking_approval_cause where entry_form=28 and user_id=$user_id and booking_id=$costSheet_id and approval_type=2 and approval_no='$approved_no'";die;
		
		
		if($unapproved_request=="")
		{
			$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

			$field_array="id, page_id, entry_form, user_id, booking_id, approval_type, approval_no, approval_cause, inserted_by, insert_date, status_active, is_deleted";
			$data_array="(".$id_mst.",".$page_id.",28,".$user_id.",".$costSheet_id." ,2,'".$approved_no."',".$unappv_request.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			//echo "10**";
			//echo "INSERT INTO fabric_booking_approval_cause (".$field_array.") VALUES ".$data_array; die;
			$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
			//echo $rID; die;

			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");
					echo "0**".$rID."**".str_replace("'","",$costSheet_id)."**".str_replace("'","",$unappv_request)."**".str_replace("'","",$user_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$rID;
				}
			}
			else if($db_type==2)
			{
				if($rID )
				{
					oci_commit($con);
					echo "0**".$rID."**".str_replace("'","",$costSheet_id)."**".str_replace("'","",$unappv_request)."**".str_replace("'","",$user_id);
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
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

			$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
			$data_array="".$page_id."*28*".$user_id."*".$costSheet_id."*2*'".$approved_no."'*".$unappv_request."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

			$rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$unapproved_request."",0);

			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");
					echo "1**".$rID."**".str_replace("'","",$costSheet_id)."**".str_replace("'","",$unappv_request)."**".str_replace("'","",$user_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$rID;
				}
			}
			else if($db_type==2)
			{
				if($rID )
				{
					oci_commit($con);
					echo "1**".$rID."**".str_replace("'","",$costSheet_id)."**".str_replace("'","",$unappv_request)."**".str_replace("'","",$user_id);
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
	}
	else if ($operation==1)  // Update Here
	{

	}
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'quick_costing_woven_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'quick_costing_woven_controller', this.value, 'load_drop_down_brand', 'brand_td');" );
	exit();
}

if ($action=="inquery_id_popup")
{
	//inquery_id_popup
  	echo load_html_head_contents("Inquiry Entry","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$exdata=explode("__",$data);
	$companyId=$exdata[0];
	$buyerId=$exdata[1];
	?>
	<script>
	var companyId='<?=$companyId; ?>';
		function js_set_value( quotation_id )
		{
			document.getElementById('selected_id').value=quotation_id;
			parent.emailwindow.hide();
		}
    </script>

</head>
<body>
    <div align="center" style="width:100%;" >
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="910" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                    <tr>
                    	<th colspan="10"><? echo create_drop_down( "cbo_string_search_type", 140, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
                    </tr>
                    <tr>
                        <th width="140" class="must_entry_caption">Company Name</th>
                        <th width="130">Buyer Name</th>
                        <th width="80">Inquiry ID</th>
                        <th width="100">M.Style Ref/Name</th>
                        <th width="50">Season Year</th>
                        <th width="130">Season</th>
                        <th width="130">Brand</th>
                        <th width="130" colspan="2">Inquiry Date Range</th>
                        <th>&nbsp; </th>
                    </tr>
                </thead>
                <tr class="general">
                    <td><? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $companyId,"load_drop_down( 'quick_costing_woven_controller', this.value, 'load_drop_down_buyer', 'buyer_td');" ); ?><input type="hidden" id="selected_id"></td>
                    <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
                    <td><input type="text" style="width:70px" class="text_boxes"  name="txt_inquery_no" id="txt_inquery_no" placeholder="Write" /></td>
                    <td><input type="text" style="width:90px" class="text_boxes"  name="txt_style" id="txt_style" placeholder="Write" /></td>
                    <td><? echo create_drop_down( "cbo_season_year", 50, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
                    <td id="season_td"><? echo create_drop_down( "cbo_season_id", 130, $blank_array,'', 1, "-- Select Season--",$selected, "" ); ?></td>
                    <td id="brand_td"><? echo create_drop_down( "cbo_brand", 130, $blank_array,"",1, "-Brand-", $selected,""); ?></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td>
                    <td>
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_inquery_no').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_season_id').value+'_'+document.getElementById('cbo_brand').value+'_'+document.getElementById('cbo_season_year').value, 'create_inquery_id_list_view', 'search_div', 'quick_costing_woven_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
                </tr>
                <tr>
                	<td colspan="10" align="center" valign="middle"><?=load_month_buttons(1); ?></td>
                </tr>
            </table>
            <div id="search_div"></div>
    	</form>
   </div>
    </body>
    <script>
		if(companyId!=0) $('#cbo_company_mst').attr('disabled',true); else $('#cbo_company_mst').attr('disabled',false);
		if(companyId!=0) load_drop_down( 'quick_costing_woven_controller', companyId, 'load_drop_down_buyer', 'buyer_td');
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_inquery_id_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and buyer_id='$data[1]'"; else $buyer="" ;
	if (trim($data[7])!=0) $season_cond=" and season_buyer_wise='$data[7]'"; else $season_cond="" ;
	
	if(trim($data[8])==0) $brand_cond=""; else if(trim($data[8])!=0) $brand_cond=" and brand_id='$data[8]' "; else if ($filterBrandId!="") $brand_cond=" and brand_id in ($filterBrandId)"; else $brand_cond="";
	
	if (trim($data[9])!=0) $seasonyear_cond=" and season_year='$data[9]'"; else $seasonyear_cond="" ;
	if($data[6]==1)
	{
		if (trim($data[4])!="") $inquery_id_cond=" and system_number_prefix_num='$data[4]'"; else $inquery_id_cond="";
		if (trim($data[5])!="") $style_cond=" and style_refernce='$data[5]'"; else $style_cond="";
	}
	else if($data[6]==4 || $data[6]==0)
	{
		if (trim($data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '%$data[4]%' "; else $inquery_id_cond="";
		if (trim($data[5])!="") $style_cond=" and style_refernce like '%$data[5]%' "; else $style_cond="";
	}
	else if($data[6]==2)
	{
		if (trim($data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '$data[4]%' "; else $style_id_cond="";
		if (trim($data[5])!="") $style_cond=" and style_refernce like '$data[5]%' "; else $style_cond="";
	}
	else if($data[6]==3)
	{
		if (trim($data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '%$data[4]' "; else $inquery_id_cond="";
		if (trim($data[5])!="") $style_cond=" and style_refernce like '%$data[5]' "; else $style_cond="";
	}

	if($db_type==0)
	{
		if($data[2]!="" &&  $data[3]!="") $est_ship_date= "and inquery_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $est_ship_date="";
	}
	else if($db_type==2)
	{
		if($data[2]!="" &&  $data[3]!="") $est_ship_date="and inquery_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $est_ship_date="";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$seasonArr = return_library_array("select id,season_name from lib_buyer_season ","id","season_name");
	$brand_arr=return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");
	
	$arr=array (1=>$buyer_arr,4=>$seasonArr,8=>$brand_arr);
	$sql= "select id, system_number_prefix_num, company_id, buyer_id, season_buyer_wise, season_year, inquery_date, buyer_request, style_refernce, style_description, brand_id, fabrication, color, offer_qty, buyer_target_price, buyer_submit_price from wo_quotation_inquery where status_active=1  and is_deleted=0 $company $buyer $est_ship_date $inquery_id_cond $style_cond $season_cond $brand_cond  $seasonyear_cond
	order by id DESC";//and id not in (select inquery_id from qc_mst where status_active=1 and is_deleted=0 and inquery_id!=0)
	//echo $sql;
	echo  create_list_view("list_view", "Inquiry ID,Buyer Name,M.Style Ref/Name,Body/Wash Color,Season,Season Year,Inquiry Date, Buyer Request,Brand", "50,100,110,80,70,60,70,70","800","280",0, $sql , "js_set_value", "id,buyer_id,style_refernce,system_number_prefix_num,season_buyer_wise,season_year,brand_id,style_description,fabrication,color,offer_qty,buyer_target_price,buyer_submit_price", "", 1, "0,buyer_id,0,0,season_buyer_wise,0,0,0,brand_id", $arr , "system_number_prefix_num,buyer_id,style_refernce,color,season_buyer_wise,season_year,inquery_date,buyer_request,brand_id", "",'','0,0,0,0,0,0,3,0,0') ;
	exit();
}

if($action=="bodyPart_washType")
{
	echo load_html_head_contents("Body Part Popup","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id, name,type)
		{
			document.getElementById('gid').value=id;
			document.getElementById('gname').value=name;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;">
        <input type="hidden" id="gid" name="gid"/>
        <input type="hidden" id="gname" name="gname"/>
        <?
		if($type==3)
		{
			?>
        <table width="420" cellspacing="0" class="rpt_table" border="0" rules="all">
            <thead>
            	<th width="40">SL</th><th width="300">Wash Type</th>
            </thead>
        </table>
        <table width="420" cellspacing="0" class="rpt_table" border="0" rules="all" id="item_table">
            <tbody>
				<?
                $i=1;
                foreach($emblishment_wash_type as $wid=>$WashName)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr onClick="js_set_value(<?=$wid; ?>,'<?=$WashName; ?>');" bgcolor="<?=$bgcolor; ?>">
                        <td width="40"><?=$i; ?></td>
                        <td><?=$WashName; ?></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
            </tbody>
        </table>
        <? } else {
			$sql_tgroup=sql_select("select id, body_part_full_name, body_part_short_name, body_part_type from lib_body_part where is_deleted=0 and status_active=1 order by body_part_full_name ASC"); ?>
        <table width="420" cellspacing="0" class="rpt_table" border="0" rules="all">
            <thead>
            	<th width="40">SL</th><th width="300">Body Part Full Name</th><th>Type</th>
            </thead>
        </table>
        <table width="420" cellspacing="0" class="rpt_table" border="0" rules="all" id="item_table">
            <tbody>
				<?
                $i=1;
                foreach($sql_tgroup as $row_tgroup)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr onClick="js_set_value(<?=$row_tgroup[csf('id')]; ?>,'<?=$row_tgroup[csf('body_part_full_name')]; ?>');" bgcolor="<?=$bgcolor; ?>">
                        <td width="40"><?=$i; ?></td>
                        <td width="300"><?=$row_tgroup[csf('body_part_full_name')]; ?></td>
                        <td><?=$body_part_type[$row_tgroup[csf('body_part_type')]]; ?></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
            </tbody>
        </table>
        <? }?>
        </div>
	</body>
	<script> setFilterGrid('item_table',-1); </script>
	</html>
	<?
	exit();
}

if($action=="itemGroup_popup")
{
	echo load_html_head_contents("Item Group Popup","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	?>
	<script>
	
	function check_all_data() {
		var tbl_row_count = document.getElementById( 'item_table' ).rows.length;
		tbl_row_count = tbl_row_count;
		if(document.getElementById('check_all').checked){
			for( var i = 1; i <= tbl_row_count; i++ ) {
			//js_set_value( i);
			document.getElementById( 'search' + i ).style.backgroundColor = 'yellow';
			if( jQuery.inArray( $('#txttrimgroupdata_' + i).val(), selected_name ) == -1 ) {
				selected_name.push($('#txttrimgroupdata_' + i).val());
			}
			else{
				for( var j = 0; j < selected_name.length; j++ ) {
					if( selected_name[j] == $('#txttrimgroupdata_' + i).val() ) break;
				}
				selected_name.splice( j,1 );
			}

			}
		}else{
			for( var i = 1; i <= tbl_row_count; i++ ) {
				if(i%2==0  ){
					document.getElementById('search'+i).style.backgroundColor = '#FFFFFF';
				}
				if(i%2!=0 ){
					document.getElementById('search'+i).style.backgroundColor = '#E9F3FF';
				}
				if( jQuery.inArray( $('#txttrimgroupdata_' + i).val(), selected_name ) == -1 ) {
					selected_name.push($('#txttrimgroupdata_' + i).val());
				}
				else{
					for( var j = 0; j < selected_name.length; j++ ) {
						if( selected_name[j] == $('#txttrimgroupdata_' + i).val() ) break;
					}
					selected_name.splice( j,1 );
				}
			}
		}
	}

	function toggle( x, origColor ) {
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}

	function onlyUnique(value, index, self) {
		return self.indexOf(value) === index;
	}

	var selected_name = new Array();

	function js_set_value( str ) {
		if($("#search"+str).css("display") !='none'){
			//toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			if(str%2==0  ){
				toggle( document.getElementById( 'search' + str ), '#FFFFFF');
			}
			if(str%2!=0 ){
				toggle( document.getElementById( 'search' + str ), '#E9F3FF');
			}
			if( jQuery.inArray( $('#txttrimgroupdata_' + str).val(), selected_name ) == -1 ) {
				selected_name.push($('#txttrimgroupdata_' + str).val());
			}
			else{
				for( var i = 0; i < selected_name.length; i++ ) {
					if( selected_name[i] == $('#txttrimgroupdata_' + str).val() ) break;
				}
				selected_name.splice( i,1 );
			}
		}
		var trimgroupdata='';
		for( var i = 0; i < selected_name.length; i++ ) {
			trimgroupdata += selected_name[i] + ',';
		}
		trimgroupdata = trimgroupdata.substr( 0, trimgroupdata.length - 1 );

		$('#itemdata').val( trimgroupdata );
	}
		
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;">
        <input type="hidden" id="gid" name="gid"/>
        <input type="hidden" id="itemdata" name="itemdata"/>
        <? $accessories_arr=sql_select("select id, item_name, trim_uom, cal_parameter, trim_type from lib_item_group where item_category=4 and status_active =1 and is_deleted=0 order by item_name"); ?>
        <table width="420" cellspacing="0" class="rpt_table" border="0" rules="all">
            <thead>
            	<th width="30">SL</th>
                <th width="200">Item Group</th>
				<th width="60">Cons Uom</th>
                <th>Trims Type</th>
            </thead>
        </table>
        <div style="width:420px; overflow-y:scroll; max-height:340px;" >
            <table width="400" cellspacing="0" class="rpt_table" border="0" rules="all" id="item_table">
                <tbody>
                    <?
                    $i=1;
                    foreach($accessories_arr as $row_tgroup)
                    {
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        $str="";
                        $str=$row_tgroup[csf('id')].'***'.$row_tgroup[csf('item_name')].'***'.$row_tgroup[csf('trim_uom')].'***'.$row_tgroup[csf('cal_parameter')];
                        ?>
                        <tr id="search<?=$i; ?>" onClick="js_set_value(<?=$i; ?>);" bgcolor="<?=$bgcolor; ?>">
                            <td width="30" style="word-break:break-all"><?=$i; ?></td>
                            <td width="200" style="word-break:break-all"><?=$row_tgroup[csf('item_name')]; ?>
                                <input type="hidden" name="txttrimgroupdata_<?=$i; ?>" id="txttrimgroupdata_<?=$i; ?>" value="<?=$str; ?>"/>
                            </td>
                            <td width="60" style="word-break:break-all"><?=$unit_of_measurement[$row_tgroup[csf('trim_uom')]]; ?></td>
                            <td style="word-break:break-all"><?=$trim_type[$row_tgroup[csf('trim_type')]]; ?></td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <table width="420" cellspacing="0" cellpadding="0" style="border:none" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%">
                        <div style="width:50%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();" /> Check / Uncheck All
                        </div>
                        <div style="width:50%; float:left" align="left">
                            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        
        </div>
	</body>
	<script> setFilterGrid('item_table',-1); </script>
	</html>
	<?
	exit();
}

if($action=="populate_data_from_rdnolib")
{
	$composition_arr=array();
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$sql_q="select mst_id, copmposition_id, percent, count_id, type_id from lib_yarn_count_determina_dtls where is_deleted=0  and mst_id in($data) order by id";
						
	$data_array=sql_select($sql_q);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			$compo_per="";
			if(($row[csf('percent')]*1)>0) $compo_per=$row[csf('percent')]."% "; else $compo_per="";
			if(array_key_exists($row[csf('mst_id')],$composition_arr))
			{
				$composition_arr[$row[csf('mst_id')]]=$composition_arr[$row[csf('mst_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('mst_id')]]=$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]];
			}
		}
	}
	unset($data_array);
	
	$sqlRd="select id, type, construction, gsm_weight, weight_type, design, fabric_ref, rd_no, color_range_id, full_width, cutable_width,shrinkage_l,shrinkage_w from lib_yarn_count_determina_mst where status_active=1 and is_deleted=0 and id in ($data) order by id ASC";
	$sqlRdData=sql_select($sqlRd);
	$i=1;
	if(count($sqlRdData)>0)
	{
		foreach($sqlRdData as $row)
		{
			$fabric=""; $fabric1="";
			
			$fabric=$row[csf('rd_no')].','.$row[csf('fabric_ref')].','.$row[csf('type')].','.$row[csf('construction')].','.$row[csf('design')].','.$row[csf('gsm_weight')].','.$fabric_weight_type[$row[csf('weight_type')]].','.$color_range[$row[csf('color_range_id')]].','.$composition_arr[$row[csf('id')]].','.$row[csf('full_width')].','.$row[csf('cutable_width')];
			
			$fabric1=$row[csf('rd_no')].','.$row[csf('fabric_ref')].','.$row[csf('type')].','.$row[csf('construction')].','.$row[csf('design')].','.$row[csf('gsm_weight')].','.$fabric_weight_type[$row[csf('weight_type')]].','.$color_range[$row[csf('color_range_id')]].','.$composition_arr[$row[csf('id')]].','.$row[csf('full_width')].','.$row[csf('cutable_width')].','.$row[csf('shrinkage_l')].','.$row[csf('shrinkage_w')];
			
			echo "$('#txt_fabid_".$i."').val('".$row[csf('id')]."');\n";
			echo "$('#txt_fabDesc".$i."').val('".$fabric."');\n"; 
			echo "$('#txt_fabDesc".$i."').attr('title', '".$fabric1."');\n"; 
			$i++;
		}
		unset($sqlRdData);
	}	
	exit();
}

if($action=="fabric_description_popup")
{
	echo load_html_head_contents("Fabric Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var inqueryfab_id='<?=$inqueryfab_id; ?>';
		function js_set_value(data)
		{
			var data=data.split('_');
			document.getElementById('hiddfabid').value=data[0];
			document.getElementById('hiddFabricDescription').value=data[1];
			document.getElementById('hiddFabricDescriptionTitle').value=data[2];
			parent.emailwindow.hide();
		}
		
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
		}
		
		function fnc_inquiry_fabric()
		{
			if(document.getElementById('chk_inquiry_fabric').checked==true)
			{
				if(inqueryfab_id=="")
				{
					alert("Fabric Not found in inquiry.");
					document.getElementById('chk_inquiry_fabric').checked=false;
					document.getElementById('chk_inquiry_fabric').value=2;
					return;
				}
				document.getElementById('chk_inquiry_fabric').value=1;
				
			}
			else if(document.getElementById('chk_inquiry_fabric').checked==false)
			{
				document.getElementById('chk_inquiry_fabric').value=2;
			}
		}
		</script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                <thead>
                    <tr>
                    	<th colspan="2">Fabric From Inquiry Only&nbsp;<input type="checkbox" name="chk_inquiry_fabric" id="chk_inquiry_fabric" onClick="fnc_inquiry_fabric();" value="1" checked style="width:12px;" ></th>
                    	<th colspan="3" align="center"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                    </tr>
                    <tr>
                    	<th class="must_entry_caption">Fabric Nature</td>
                    	<th>RD No</th>
                        <th>Construction</th>
                        <th>Ounce/Weight</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="general">
                    	<td><?=create_drop_down( "cbo_fabric_nature",100, $business_nature_arr,"", 0, "", '3', "",$disabled,"2,3,100" ); ?></td>
                    	<td align="center"><input type="text" style="width:80px" class="text_boxes" name="txt_rdno" id="txt_rdno" /></td>
                        <td align="center"><input type="text" style="width:130px" class="text_boxes" name="txt_construction" id="txt_construction" /></td>
                        <td align="center">	<input type="text" style="width:130px" class="text_boxes" name="txt_gsm_weight" id="txt_gsm_weight" /></td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<?=$txt_fabid; ?>'+'**'+document.getElementById('txt_construction').value+'**'+document.getElementById('txt_gsm_weight').value+'**'+document.getElementById('cbo_string_search_type').value+'**'+document.getElementById('txt_rdno').value+'**'+document.getElementById('chk_inquiry_fabric').value+'**'+'<?=$inqueryfab_id; ?>'+'**'+document.getElementById('cbo_fabric_nature').value, 'fabric_description_popup_search_list_view', 'search_div', 'quick_costing_woven_controller', 'setFilterGrid(\'list_view\',-1)'); toggle( 'tr_'+'<?=$txt_fabid; ?>', '#FFFFCC');" style="width:100px;" />
                        </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:5px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="fabric_description_popup_search_list_view")
{
	extract($_REQUEST);
	list($libyarncountdeterminationid,$construction,$gsm_weight,$string_search_type,$rdno,$isInquiryFabric,$inqueryfab_id,$fabric_nature)=explode('**',$data);
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
	$search_con='';
	$user_arr=return_library_array( "select user_full_name,id from user_passwd", "id", "user_full_name");
	if($string_search_type==1)
	{
		if($construction!='') {$search_con .= " and a.construction='".trim($construction)."'";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight='".trim($gsm_weight)."'";}
		if($rdno!='') {$search_con .= " and a.rd_no='".trim($rdno)."'";}
	}
	else if($string_search_type==2)
	{
		if($construction!='') {$search_con .= " and a.construction like ('".trim($construction)."%')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('".trim($gsm_weight)."%')";}
		if($rdno!='') {$search_con .= " and a.rd_no like ('".trim($rdno)."%')";}
	}
	else if($string_search_type==3)
	{
		if($construction!='') {$search_con .= " and a.construction like ('%".trim($construction)."')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('%".trim($gsm_weight)."')";}
		if($rdno!='') {$search_con .= " and a.rd_no like ('%".trim($rdno)."')";}
	}
	else if($string_search_type==4 || $string_search_type==0)
	{
		if($construction!='') {$search_con .= " and a.construction like ('%".trim($construction)."%')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('%".trim($gsm_weight)."%')";}
		if($rdno!='') {$search_con .= " and a.rd_no like ('%".trim($rdno)."%')";}
	}
	
	if($isInquiryFabric==1 && $inqueryfab_id!="") $inquiryFabricCond=" and a.id in ($inqueryfab_id)"; else $inquiryFabricCond="";
?>
</head>
<body>
        <form>
            <input type="hidden" id="hiddfabid" name="hiddfabid" />
            <input type="hidden" id="hiddFabricDescription" name="hiddFabricDescription" />
            <input type="hidden" id="hiddFabricDescriptionTitle" name="hiddFabricDescriptionTitle" />
        </form>
<?
	$composition_arr=array();
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$arr=array (0=>$item_category, 3=>$color_range,6=>$composition,8=>$lib_yarn_count,9=>$yarn_type);
	$sql="select a.fab_nature_id, a.construction, a.gsm_weight, a.color_range_id, a.stich_length, a.process_loss, b.copmposition_id, b.percent, b.count_id, b.type_id, a.id, b.id as bid from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and a.fab_nature_id= '$fabric_nature' $inquiryFabricCond order by a.id,b.id";
	$table_width='1000';
	$table_width2='1250';
	
	$data_array=sql_select($sql);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			$compo_per="";
			if(($row[csf('percent')]*1)>0) $compo_per=$row[csf('percent')]."% "; else $compo_per="";
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
		}
	}
?>
    <table class="rpt_table" width="1100" cellspacing="0" cellpadding="0" border="0" rules="all" style="position: sticky; top: 0;" >
        <thead>
        	<tr>
        		<th width="25">SL No</th>
	            <th width="80" style="word-break:break-all">Fab Nature</th>
                <th width="70">RD No</th>
	            <th width="80" style="word-break:break-all">Fabric Ref</th>
	            <th width="70">Type</th>
	            <th width="100" style="word-break:break-all">Construction</th>
	            <th width="80">Design</th>
	            <th width="50" style="word-break:break-all">Ounce/Weight</th>
	            <th width="50" style="word-break:break-all">Weight Type</th>
	            <th width="50" style="word-break:break-all">Color Range</th>
	            <th width="50" style="word-break:break-all">Full Width</th>
	            <th width="50" style="word-break:break-all">Cutable Width</th>
	            <th width="50" style="word-break:break-all">Shrinkage L</th>
	            <th width="50" style="word-break:break-all">Shrinkage W</th>
	            <th style="word-break:break-all">Composition</th>          
        	</tr>
       </thead>
   </table>
   
   <table id="list_view" class="rpt_table" width="1100" cellspacing="0" cellpadding="0" border="1" rules="all" style="max-height:300px; overflow-y:scroll">
        <tbody>
	<?
		$sql_data=sql_select("select a.id, a.fab_nature_id, a.type, a.construction, a.gsm_weight, a.weight_type, a.design, a.fabric_ref, a.color_range_id, a.rd_no, a.inserted_by, a.status_active, a.full_width, a.cutable_width,a.shrinkage_l,a.shrinkage_w from lib_yarn_count_determina_mst a where a.is_deleted=0 and a.status_active=1 and a.fab_nature_id= '$fabric_nature' $search_con $inquiryFabricCond order by a.id");

		$i=1;
		foreach($sql_data as $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$fabric="";
			$fabric=$row[csf('rd_no')].','.$row[csf('fabric_ref')].','.$row[csf('type')].','.$row[csf('construction')].','.$row[csf('design')].','.$row[csf('gsm_weight')].','.$fabric_weight_type[$row[csf('weight_type')]].','.$color_range[$row[csf('color_range_id')]].','.$composition_arr[$row[csf('id')]].','.$row[csf('full_width')].','.$row[csf('cutable_width')];

			$fabric1="";
			$fabric1=$row[csf('rd_no')].','.$row[csf('fabric_ref')].','.$row[csf('type')].','.$row[csf('construction')].','.$row[csf('design')].','.$row[csf('gsm_weight')].','.$fabric_weight_type[$row[csf('weight_type')]].','.$color_range[$row[csf('color_range_id')]].','.$composition_arr[$row[csf('id')]].','.$row[csf('full_width')].','.$row[csf('cutable_width')].','.$row[csf('shrinkage_l')].','.$row[csf('shrinkage_w')];
			?>
	            <tr id="tr_<?=$row[csf('id')] ?>" bgcolor="<?=$bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="js_set_value('<?=$row[csf('id')]."_".$fabric."_".$fabric1; ?>')">
	                <td width="25" align="center"><?=$i; ?></td>
	                <td width="80" style="word-break:break-all"><?=$item_category[$row[csf('fab_nature_id')]]; ?></td>
                    <td width="70" style="word-break:break-all"><?=$row[csf('rd_no')]; ?></td>
	                <td width="80" style="word-break:break-all"><?=$row[csf('fabric_ref')]; ?></td>
	                <td width="70" style="word-break:break-all"><?=$row[csf('type')]; ?></td>
	                <td width="100" style="word-break:break-all"><?=$row[csf('construction')]; ?></td>
	                <td width="80" style="word-break:break-all"><?=$row[csf('design')]; ?></td>
	                <td width="50" style="word-break:break-all"><?=$row[csf('gsm_weight')]; ?></td>
	                <td width="50" style="word-break:break-all"><?=$fabric_weight_type[$row[csf('weight_type')]]; ?></td>
	                <td width="50" style="word-break:break-all"><?=$color_range[$row[csf('color_range_id')]]; ?></td>
	                <td width="50" style="word-break:break-all"><?=$row[csf('full_width')]; ?></td>
	                <td width="50" style="word-break:break-all"><?=$row[csf('cutable_width')]; ?></td>
	                <td width="50" style="word-break:break-all"><?=$row[csf('shrinkage_l')]; ?></td>
	                <td width="50" style="word-break:break-all"><?=$row[csf('shrinkage_w')]; ?></td>
	                <td style="word-break:break-all"><?=$composition_arr[$row[csf('id')]]; ?></td>
	            </tr>
			<?
		    $i++;
	    }
    ?>
        </tbody>
    </table>

</body>
</html>
<?
exit();
}

if($action=="trimscons_details_popup")
{
	echo load_html_head_contents("Trims Cons. Details Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
    <script>
		var trimsid='<?=$id; ?>';
		var trimsname='<?=$trimsname; ?>';
		var calparameter='<?=$calparameter; ?>';
		var consCalData='<?=$consCalData; ?>';
		var trimUom='<?=$trimUom; ?>';
		
		function fnc_cons_amt(str)
		{
			var valstr=str.split('_');
			var inc=valstr[0];
			var type=valstr[1];
			var consFab=0;
			
			var row =$('#tbl_fab tbody tr').length; 
			var z=0; var consYds=0; var consKg=0; var isExPer=0; var totConsPack=0; var constotPack=0; var amtTot=0;
			
			for(var i=1; i<=row; i++)
			{
				if(($('#txtcolorratio_'+i).val()*1)!=0)
				{
					if(($('#txtcolorexper_'+i).val()*1)>0) isExPer=1;
					var consFab=0
					/*if (type==2)
					{
						var colorconskg=$('#txtcolorconskg_'+i).val()*1;
						if(fabuom==12)//kg
						{
							var consFab=(colorconskg/36)/((fabwidth*1)*(fabgsm*1)/1550/1000);
						}
						else if(fabuom==23)//mtr
						{
							var consFab=(colorconskg/1.09361/36)/((fabwidth*1)*(fabgsm*1)/1550/1000);
						}
						else if(fabuom==27)//yds
						{
							var consFab=colorconskg;
						}
						$("#txtcolorconsyds_"+i).val( number_format(consFab,4,'.','') );
						//var consPack=(consFab/12)*(itemRatio*1);
						var consPack=(colorconskg/12)*($('#txtcolorratio_'+i).val()*1);
						
						consYds+=(consFab*1);
						consKg+=(colorconskg*1);
					}
					else
					{
						var colorconsyds=$('#txtcolorconsyds_'+i).val()*1;
						if(fabuom==12)//kg
						{
							var consFab=(colorconsyds*36*(fabwidth*1)*(fabgsm*1))/1550/1000;
						}
						else if(fabuom==23)//mtr
						{
							var consFab=colorconsyds*1.09361*36*(fabwidth*1)*(fabgsm*1)/1550/1000;
						}
						else if(fabuom==27)//yds
						{
							var consFab=colorconsyds;
						}
						$("#txtcolorconskg_"+i).val( number_format(consFab,4,'.','') );
						var consPack=(consFab/12)*($('#txtcolorratio_'+i).val()*1);
						
						consYds+=(colorconsyds*1);
						consKg+=(consFab*1);
					}*/
					var colorconskg=$('#txtcolorconskg_'+i).val()*1;
					consKg+=(colorconskg*1);
					var consPack=(colorconskg/12)*($('#txtcolorratio_'+i).val()*1);
					$('#txtcolorconspack_'+i).val( number_format(consPack,4,'.','') );
					var conspackkg=consPack;//$('#txtcolorconspack_'+inc).val()*1;
					constotPack+=consPack;
					var colorexper=$("#txtcolorexper_"+i).val()*1;
					var rowtotCons=0;
					/*if(process_loss_method_id==1)
					{*/
						rowtotCons=conspackkg+(conspackkg*(colorexper/100));
					/*}
					else if(process_loss_method_id==2)
					{
						var devided_val = 1-(colorexper/100);
						rowtotCons=parseFloat(conspackkg/devided_val);
					}
					else rowtotCons=0;*/
					
					if((colorexper*1)==0) var rowtotCons=conspackkg;
					$('#txtcolortotccons_'+i).val( number_format(rowtotCons,4,'.','') );
					totConsPack+=rowtotCons;
					//var rowtotCons=number_format(rowtotCons,4,'.','');
					var packRate=($('#txtcolorrate_'+i).val()*1);
					var rowamt=rowtotCons*packRate;
					
					$('#txtcoloramt_'+i).val( number_format(rowamt,4,'.','') );
					
					amtTot+=rowamt;
					z++;
				}
			}
			//consYds=consYds/z;
			consKg=consKg/z;
			var avg_rate=amtTot/totConsPack;
			if(isExPer==1)
			{
				var avgexper=(totConsPack-constotPack)/constotPack*100;
			}
			else { var avgexper=0; }
			
			//$('#txttotcons_yds').val( number_format(consYds,4,'.','') );
			$('#txttotcons_kg').val( number_format(consKg,4,'.','') );
			
			$('#txttotcons_pack').val( number_format(constotPack,4,'.','') );
			$('#txttotexper').val( number_format(avgexper,4,'.','') );
			$('#txttotcons').val( number_format(totConsPack,4,'.','') );
			$('#txt_avg_rate').val( number_format(avg_rate,4,'.','') );
			$('#txt_amt_pack').val( number_format(amtTot,4,'.','') );
		}
		
		function fnc_total_rate(type)
		{
			if(type==1)
			{
				var row =$('#tbl_fab tbody tr').length; 
				var z=0; var consYds=0; var consKg=0; var isExPer=0; var totConsPack=0; var consPack=0; var amtTot=0;
				for(var i=1; i<=row; i++)
				{
					if(($('#txtcolorratio_'+i).val()*1)!=0)
					{
						if(($('#txtcolorexper_'+i).val()*1)>0) isExPer=1;
						
						//consYds+=($('#txtcolorconsyds_'+i).val()*1);
						consKg+=($('#txtcolorconskg_'+i).val()*1);
						consPack+=($('#txtcolorconspack_'+i).val()*1);
						totConsPack+=($('#txtcolortotccons_'+i).val()*1);
						amtTot+=($('#txtcoloramt_'+i).val()*1);
						z++;
					}
				}
				//consYds=consYds/z;
				consKg=consKg/z;
				var avg_rate=amtTot/totConsPack;
				if(isExPer==1)
				{
					var avgexper=(totConsPack-consPack)/consPack*100;
				}
				else { var avgexper=0; }
				
				//$('#txttotcons_yds').val( number_format(consYds,4,'.','') );
				$('#txttotcons_kg').val( number_format(consKg,4,'.','') );
				
				$('#txttotcons_pack').val( number_format(consPack,4,'.','') );
				$('#txttotexper').val( number_format(avgexper,4,'.','') );
				$('#txttotcons').val( number_format(totConsPack,4,'.','') );
				$('#txt_avg_rate').val( number_format(avg_rate,4,'.','') );
				$('#txt_amt_pack').val( number_format(amtTot,4,'.','') );
			}
			/*else if(type==2)
			{
				var yrow =$('#tbl_yarn tbody tr').length; 
				var z=1; var consYarnTot=0; var amtYarnTot=0;
				for(var i=1; i<=yrow; i++)
				{
					consYarnTot+=($('#txtyarncons_'+i).val()*1);
					amtYarnTot+=($('#txtyarnamt_'+i).val()*1);
				}
				$('#txt_tot_yarncons').val( number_format(consYarnTot,4,'.','') );
				$('#txt_tot_yarnamt').val( number_format(amtYarnTot,4,'.','') );
			}
			else if(type==3)
			{
				var crow =$('#tbl_conv tbody tr').length; 
				var z=1; var consConvTot=0; var amtConvTot=0;
				for(var i=1; i<=crow; i++)
				{
					consConvTot+=($('#txtconvcons_'+i).val()*1);
					amtConvTot+=($('#txtconvamt_'+i).val()*1);
				}
				$('#txt_tot_convcons').val( number_format(consConvTot,4,'.','') );
				$('#txt_tot_convamt').val( number_format(amtConvTot,4,'.','') );
			}
			var totCost=totAmount=0;
			var totCost=($('#txt_tot_amt').val()*1)+($('#txt_tot_yarnamt').val()*1)+($('#txt_tot_convamt').val()*1);
			
			$('#txtamount').val( number_format(totCost,4,'.','') );
			if(($('#txtprocessloss').val()*1)!=0)
			{
				var totAmount=totCost+(totCost*(($('#txtprocessloss').val()*1)/100));
			}
			else { var totAmount=totCost; }
			
			$('#txttotamount').val( number_format(totAmount,4,'.','') );*/
		}
		
		function js_set_value()
		{
			var calculator_string="";
			if(calparameter==1)//Sewing Thread-Comsumption Calculator
			{
				calculator_string=($('#txt_cons_per_gmts').val()*1)+"~~"+$('#txt_cons_for_mtr').val()+"~~"+$('#txt_cons_length').val()+"~~"+$('#txt_caculated_value').val();
			}
			else if(calparameter==2)//Carton-Comsumption Calculator
			{
				calculator_string=($('#txt_gmts_per_catton').val()*1)+"~~"+$('#txt_cost_for').val()+"~~"+$('#txt_caculated_value').val();
			}
			else if(calparameter==3)//Carton Stiker-Comsumption Calculator
			{
				calculator_string=($('#txt_stiker_per_catton').val()*1)+"~~"+$('#txt_req_carton').val()+"~~"+$('#txt_caculated_value').val();
			}
			else if(calparameter==4)//Poly-Comsumption Calculator
			{
				calculator_string=($('#txt_gmts_per_catton').val()*1)+"~~"+$('#txt_cost_for').val()+"~~"+$('#txt_caculated_value').val();
			}
			else if(calparameter==5)//Elastic Calculator
			{
				calculator_string=($('#txt_cons_per_gmts').val()*1)+"~~"+$('#txt_cons_length').val()+"~~"+$('#txt_caculated_value').val();
			}
			else if(calparameter==6)//Gum Tap Calculator
			{
				calculator_string=($('#txt_cons_per_gmts').val()*1)+"~~"+$('#txt_pcs_per_carton').val()+"~~"+$('#txt_cons_length').val()+"~~"+$('#txt_caculated_value').val();			}
			else if(calparameter==7|| calparameter==10 || calparameter==11)//Tag Pin Calculator-7;  Button GG Calculator-10;  Button Gross Calculator-11;
			{
				calculator_string=($('#txt_cons_per_gmts').val()*1)+"~~"+$('#txt_cons_length').val()+"~~"+$('#txt_caculated_value').val();
			}
			else if(calparameter==8)//Sequines Calculator
			{
				calculator_string=($('#txt_cons_per_gmts').val()*1)+"~~"+$('#txt_cons_length').val()+"~~"+$('#txt_caculated_value').val();
			}
			else if(calparameter==9)//Eyelet Calculator
			{
				calculator_string=($('#txt_qty_per_gmts').val()*1)+"~~"+$('#txt_qty_per_grs').val()+"~~"+$('#txt_caculated_value').val();
	
			}
			else if(calparameter==12)//Embroidery Thread-Comsumption Calculator
			{ 
				calculator_string=($('#txt_cons_per_gmts').val()*1)+"~~"+$('#txt_cons_for_mtr').val()+"~~"+$('#txt_cons_length').val()+"~~"+$('#txt_caculated_value').val();
			}
			
			document.getElementById('calculator_string').value=calculator_string;
			parent.emailwindow.hide();
		}
		
		function fnc_consCalacultaion()
		{
			if(calparameter==1)//Sewing Thread-Comsumption Calculator
			{
				var txt_cons_per_gmts=(document.getElementById('txt_cons_per_gmts').value)*1;
				document.getElementById('txt_cons_for_mtr').value=txt_cons_per_gmts*1;
		
				var txt_cons_for_mtr= (document.getElementById('txt_cons_for_mtr').value)*1;
				var txt_cons_length= (document.getElementById('txt_cons_length').value)*1;
				document.getElementById('txt_caculated_value').value=txt_cons_for_mtr/txt_cons_length;
			}
			else if(calparameter==2)//Carton-Comsumption Calculator
			{
				var txt_gmts_per_catton=(document.getElementById('txt_gmts_per_catton').value)*1;
				var txt_cost_for=document.getElementById('txt_cost_for').value;
				var txt_caculated_value=(1/txt_gmts_per_catton)*txt_cost_for;
				document.getElementById('txt_caculated_value').value= number_format_common(txt_caculated_value,5,0);
			}
			else if(calparameter==3)//Carton Stiker-Comsumption Calculator
			{
				var txt_stiker_per_catton=(document.getElementById('txt_stiker_per_catton').value)*1;
				var txt_req_carton=document.getElementById('txt_req_carton').value;
				var txt_caculated_value=txt_stiker_per_catton*txt_req_carton;
				document.getElementById('txt_caculated_value').value= number_format_common(txt_caculated_value,5,0);
			}
			else if(calparameter==4)//Poly-Comsumption Calculator
			{
				var txt_gmts_per_catton=(document.getElementById('txt_gmts_per_catton').value)*1;
				var txt_cost_for=document.getElementById('txt_cost_for').value;
				var txt_caculated_value=(1/txt_gmts_per_catton)*txt_cost_for;
				document.getElementById('txt_caculated_value').value= number_format_common(txt_caculated_value,5,0);
			}
			else if(calparameter==5)//Elastic Calculator
			{
				var txt_cons_per_gmts=(document.getElementById('txt_cons_per_gmts').value)*1;
				var txt_cons_length= (document.getElementById('txt_cons_length').value)*1;
				var cons_dzn=0;
				cons_dzn=(txt_cons_per_gmts/txt_cons_length)*1;
		
				document.getElementById('txt_caculated_value').value=number_format_common(cons_dzn,5,0);
			}
			else if(calparameter==6)//Gum Tap Calculator
			{
				var txt_cons_per_gmts=(document.getElementById('txt_cons_per_gmts').value)*1;
				var txt_pcs_per_carton=(document.getElementById('txt_pcs_per_carton').value)*1;
				var txt_cons_length= (document.getElementById('txt_cons_length').value)*1
				var cons_dzn=0;
				
				cons_dzn=(txt_cons_per_gmts/txt_pcs_per_carton);
				cons_dzn=(cons_dzn/txt_cons_length)*1;
				
				document.getElementById('txt_caculated_value').value=number_format_common(cons_dzn,5,0);
			}
			else if(calparameter==7 || calparameter==10 || calparameter==11 || calparameter==13)//Tag Pin Calculator-7;  Button GG Calculator-10;  Button Gross Calculator-11; Carton Board-13;
			{
				var txt_cons_per_gmts=(document.getElementById('txt_cons_per_gmts').value)*1;
				var txt_cons_length= (document.getElementById('txt_cons_length').value)*1;
				var cons_dzn=0;
				cons_dzn=(txt_cons_per_gmts/txt_cons_length)*1;
		
				document.getElementById('txt_caculated_value').value=number_format_common(cons_dzn,5,0);
			}
			else if(calparameter==8)//Sequines Calculator
			{
				var txt_cons_per_gmts=(document.getElementById('txt_cons_per_gmts').value)*1;
				var txt_cons_length= (document.getElementById('txt_cons_length').value)*1
				var cons_dzn=0;
				cons_dzn=(txt_cons_per_gmts/txt_cons_length)*1;
		
				document.getElementById('txt_caculated_value').value=number_format_common(cons_dzn,5,0);
			}
			else if(calparameter==9)//Eyelet Calculator
			{
				var qty_per_gmts=(document.getElementById('txt_qty_per_gmts').value)*1;
				var qty_per_grs=document.getElementById('txt_qty_per_grs').value;
				var txt_caculated_value=(1*qty_per_gmts)/qty_per_grs;
				document.getElementById('txt_caculated_value').value= number_format_common(txt_caculated_value,5,0);
			}
			
			else if(calparameter==12)//Embroidery Thread-Comsumption Calculator
			{
				var txt_cons_per_gmts=(document.getElementById('txt_cons_per_gmts').value)*1;
				document.getElementById('txt_cons_for_mtr').value=txt_cons_per_gmts*1;
				var txt_cons_for_mtr= (document.getElementById('txt_cons_for_mtr').value)*1;
				var txt_cons_length= (document.getElementById('txt_cons_length').value)*1;
				document.getElementById('txt_caculated_value').value=txt_cons_for_mtr/txt_cons_length;
			}
		}
		
		
	</script>
	</head>
	<body onLoad="set_hotkey();">
        <div id="cons_details" align="center"> 
            <div style="display:none"><?=load_freeze_divs ("../../../",'',1); ?></div>          
            <form name="consDetails_1" id="consDetails_1" autocomplete="off">
            	<label style="font-size:18px; font-weight:bold"><?=$trimsname.' ('.$unit_of_measurement[$trimUom].')'; ?></label>
                <input type="hidden" id="calculator_string"  name="calculator_string" style="width:75px" value="" readonly />
                <!--<input type="hidden" id="txt_caculated_value" name="txt_caculated_value" class="text_boxes_numeric" readonly value=""/>-->
                <div>
                	<table cellspacing="2" cellpadding="0" border="0">
                        <tr>
                            <td><?
								$calculatorstringarr=explode("~~",$consCalData);
                                if($calparameter==1)//Sewing Thread-Comsumption Calculator
                                {
                                    $txt_cons_length=$calculatorstringarr[2];
                                    if($txt_cons_length=="" || $txt_cons_length==0) $txt_cons_length=4000;
                                    ?>
                                    <fieldset>
                                        <legend>Sewing Thread-Comsumption Calculator</legend>
                                        <table class="rpt_table" cellpadding="0" cellspacing="0" align="center" width="800" border="1" rules="all">
                                            <tr>
                                                <td width="190">Cons Per Gmts. &nbsp;&nbsp;&nbsp;<input type="text" id="txt_cons_per_gmts" name="txt_cons_per_gmts" class="text_boxes_numeric" onChange="fnc_consCalacultaion();" style="width:58px" value="<?=$calculatorstringarr[0]; ?>"/> Mtr </td>
                                                <td width="190">Cons 1 PCS &nbsp;&nbsp;&nbsp;<? //=$costing_per[$cbo_costing_per]; ?><input type="text" id="txt_cons_for_mtr" name="txt_cons_for_mtr" class="text_boxes_numeric" style="width:58px"  value="<?=$calculatorstringarr[1]; ?>" readonly/> Mtr</td>
                                                <td width="190">Cone Length &nbsp;&nbsp;&nbsp;<input type="text" id="txt_cons_length" name="txt_cons_length" class="text_boxes_numeric"  onChange="fnc_consCalacultaion();" style="width:58px" value="<?=$txt_cons_length; ?>"/> Mtr</td>
                                                <td>Cons 1 PCS &nbsp;&nbsp;&nbsp;<input type="text" id="txt_caculated_value" name="txt_caculated_value" class="text_boxes_numeric" style="width:58px" readonly value="<?=$calculatorstringarr[3]; ?>" /> Cone
                                                    <input type="hidden" id="txt_clacolator_param_value" name="txt_clacolator_param_value" class="text_boxes_numeric" value="" readonly />
                                                </td>
                                            </tr>
                                        </table>
                                    </fieldset>
                                    <?
                                }
                                else if($calparameter==2)//Carton-Comsumption Calculator
                                {
                                    ?>
                                    <fieldset>
                                        <legend>Carton-Comsumption Calculator</legend>
                                         <table class="rpt_table" cellpadding="0" cellspacing="0" align="center" width="800" border="1" rules="all">
                                            <tr>
                                                <td width="190">Gmts Per Carton &nbsp;&nbsp;&nbsp;<input type="text" id="txt_gmts_per_catton" name="txt_gmts_per_catton" class="text_boxes_numeric" onChange="fnc_consCalacultaion();" style="width:58px" value="<?=$calculatorstringarr[0] ?>"/>
                                                </td>
                                                <td width="190">Costting 1 PCS &nbsp;&nbsp;&nbsp;<? //echo $costing_per[$cbo_costing_per];?><input type="text" id="txt_cost_for" name="txt_cost_for" class="text_boxes_numeric" style="width:58px" value="<?=1; ?>" readonly/></td>
                                                <td>Required Carton &nbsp;&nbsp;&nbsp;
                                                    <input type="text" id="txt_caculated_value" name="txt_caculated_value" class="text_boxes_numeric" style="width:58px" readonly value="<?=$calculatorstringarr[2]; ?>" />
                                                    <input type="hidden" id="txt_clacolator_param_value" name="txt_clacolator_param_value" class="text_boxes_numeric" value="" readonly />
                                                </td>
                                            </tr>
                                        </table>
                                    </fieldset>
                                    <?
                                }
                                else if($calparameter==3)//Carton Stiker-Comsumption Calculator
                                {
                                    $req_carton=1;
                                    ?>
                                    <fieldset>
                                        <legend>Carton Stiker-Comsumption Calculator</legend>
                                        <table class="rpt_table" cellpadding="0" cellspacing="0" align="center" width="800" border="1" rules="all">
                                            <tr>
                                                <td width="190">Stiker Per Carton &nbsp;&nbsp;&nbsp;<input type="text" id="txt_stiker_per_catton" name="txt_stiker_per_catton" class="text_boxes_numeric" onChange="fnc_consCalacultaion();" style="width:58px" value="<?=$calculatorstringarr[0]; ?>"/></td>
                                                <td width="190">Req Car 1 PCS &nbsp;&nbsp;&nbsp;<? //echo $costing_per[$cbo_costing_per];?> <input type="text" id="txt_req_carton" name="txt_req_carton" class="text_boxes_numeric" onChange="clculate_req_carton_stiker()" value="<?=$req_carton; ?>" style="width:58px" readonly /></td>
                                                <td>Required Stiker &nbsp;&nbsp;&nbsp;
                                                    <input type="text" id="txt_caculated_value" name="txt_caculated_value" class="text_boxes_numeric" style="width:58px" readonly value="<?=$calculatorstringarr[2]; ?>" />
                                                    <input type="hidden" id="txt_clacolator_param_value" name="txt_clacolator_param_value" class="text_boxes_numeric" value="" readonly />
                                                </td>
                                            </tr>
                                        </table>
                                    </fieldset>
                                    <?
                                }
                                else if($calparameter==4)//Poly-Comsumption Calculator
                                {
                                    ?>
                                    <fieldset>
                                        <legend>Poly-Comsumption Calculator</legend>
                                        <table class="rpt_table" cellpadding="0" cellspacing="0" align="center" width="800" border="1" rules="all">
                                            <tr>
                                                <td width="190">Gmts Per Poly &nbsp;&nbsp;&nbsp;<input type="text" id="txt_gmts_per_catton" name="txt_gmts_per_catton" class="text_boxes_numeric" style="width:58px" onChange="fnc_consCalacultaion();" value="<?=$calculatorstringarr[0]; ?>"/></td>
                                                <td width="190">Costting 1 PCS &nbsp;&nbsp;&nbsp;<? // echo $costing_per[$cbo_costing_per];?> <input type="text" id="txt_cost_for" name="txt_cost_for" class="text_boxes_numeric" style="width:58px" value="<?=1; ?>" readonly/></td>
                                                <td>Required Poly &nbsp;&nbsp;&nbsp;
                                                    <input type="text" id="txt_caculated_value" name="txt_caculated_value" class="text_boxes_numeric" readonly style="width:58px" value="<?=$calculatorstringarr[2]; ?>" />
                                                    <input type="hidden" id="txt_clacolator_param_value" name="txt_clacolator_param_value" class="text_boxes_numeric" value="" readonly />
                                                </td>
                                            </tr>
                                        </table>
                                    </fieldset>
                                    <?
                                }
                                else if($calparameter==5)//Elastic Calculator
                                {
                                    $RollLength=$calculatorstringarr[1];
                                    if($RollLength == "" || $RollLength==0) $RollLength=48;
                                    ?>
                                    <fieldset>
                                        <legend>Elastic Calculator</legend>
                                        <table class="rpt_table" cellpadding="0" cellspacing="0" align="center" width="800" border="1" rules="all">
                                            <tr>
                                                <td width="190">Cons Per Garment &nbsp;&nbsp;&nbsp;<input type="text" id="txt_cons_per_gmts" name="txt_cons_per_gmts" class="text_boxes_numeric" onChange="fnc_consCalacultaion();" style="width:58px" value="<?=$calculatorstringarr[0]; ?>" /> Yds
                                                </td>
                                                <td width="190">Roll Length &nbsp;&nbsp;&nbsp;<input type="text" id="txt_cons_length" name="txt_cons_length" class="text_boxes_numeric" onChange="fnc_consCalacultaion();" value="<?=$RollLength; ?>" style="width:58px"/> Yds</td>
                                                <td>Cons 1 PCS &nbsp;&nbsp;&nbsp;
                                                    <input type="text" id="txt_caculated_value" name="txt_caculated_value" class="text_boxes_numeric" style="width:58px" readonly value="<?=$calculatorstringarr[2]; ?>" /> Roll
                                                    <input type="hidden" id="txt_clacolator_param_value" name="txt_clacolator_param_value" class="text_boxes_numeric" value="" readonly />
                                                </td>
                                            </tr>
                                        </table>
                                    </fieldset>
                                    <?
                                }
                                else if($calparameter==6)//Gum Tap Calculator
                                {
                                    $RollLength=$calculatorstringarr[2];
                                    if($RollLength == "" || $RollLength==0)$RollLength=48;
                                    ?>
                                    <fieldset>
                                        <legend>Gum Tap Calculator</legend>
                                        <table class="rpt_table" cellpadding="0" cellspacing="0" align="center" width="800" border="1" rules="all">
                                            <tr>
                                                <td width="190">Cons Per Carton &nbsp;&nbsp;&nbsp;<input type="text" id="txt_cons_per_gmts" name="txt_cons_per_gmts" class="text_boxes_numeric" onChange="fnc_consCalacultaion();" style="width:58px" value="<?=$calculatorstringarr[0]; ?>"/> Yds</td>
                                                <td width="190">Pcs Per Carton &nbsp;&nbsp;&nbsp;<input type="text" id="txt_pcs_per_carton" name="txt_pcs_per_carton" class="text_boxes_numeric" onChange="fnc_consCalacultaion();" style="width:58px" value="<?=$calculatorstringarr[1]; ?>"/> Pcs</td>
                                                <td width="190">Roll Length &nbsp;&nbsp;&nbsp;<input type="text" id="txt_cons_length" name="txt_cons_length" class="text_boxes_numeric"  onChange="fnc_consCalacultaion();" value="<?=$RollLength; ?>" style="width:58px" /> Yds</td>
                                                <td>Cons 1 PCS &nbsp;&nbsp;&nbsp; <? // echo $costing_per[$cbo_costing_per];?>
                                                    <input type="text" id="txt_caculated_value" name="txt_caculated_value" class="text_boxes_numeric" style="width:58px" readonly value="<?=$calculatorstringarr[3]; ?>"/> Roll
                                                   <input type="hidden" id="txt_clacolator_param_value" name="txt_clacolator_param_value" class="text_boxes_numeric" value="" readonly />
                                                </td>
                                            </tr>
                                        </table>
                                    </fieldset>
                                    <?
                                }
                                else if($calparameter==7 || $calparameter==10 || $calparameter==11 || $calparameter==13) //Tag Pin Calculator-7;  Button GG Calculator-10;  Button Gross Calculator-11; Carton Board-13;
                                {
									
									if($calparameter==7) 
									{
										$labelCap="Tag Pin Calculator";
										$QtyPerBox=$calculatorstringarr[1];
                                    	if($QtyPerBox=="" || $QtyPerBox== 0) $QtyPerBox=4800;
										$boxPcs="Box"; $reqQtyTd="Cons 1 PCS";
									}
									else if($calparameter==10) 
									{
										$labelCap="Button GG Calculator";
										$QtyPerBox=$calculatorstringarr[1];
                                   		 if($QtyPerBox=="" || $QtyPerBox== 0) $QtyPerBox=1728;
										 $boxPcs="GG"; $reqQtyTd="Cons 1 PCS";
									}
									else if($calparameter==11) 
									{
										$labelCap="Button Gross Calculator";
										$QtyPerBox=$calculatorstringarr[1];
                                    	if($QtyPerBox=="" || $QtyPerBox== 0) $QtyPerBox=144;
										$boxPcs="Gross"; $reqQtyTd="Cons 1 PCS";
									}
									else if($calparameter==13) 
									{
										$labelCap="Carton Board Calculator";
										$QtyPerBox=$calculatorstringarr[1];
                                    	if($QtyPerBox=="" || $QtyPerBox== 0) $QtyPerBox=1;
										$boxPcs=""; $reqQtyTd="Req. Carton Board";
									}
                                   
                                    ?>
                                    <fieldset>
                                        <legend><?=$labelCap; ?></legend>
                                        <table class="rpt_table" cellpadding="0" cellspacing="0" align="center" width="800" border="1" rules="all">
                                            <tr>
                                                <td width="190">Gmts Per Carton Board<input type="text" id="txt_cons_per_gmts" name="txt_cons_per_gmts" class="text_boxes_numeric" onChange="fnc_consCalacultaion();" style="width:55px" value="<?=$calculatorstringarr[0]; ?>"/>Pcs</td>
                                                <td width="190">Qty Per <?=$boxPcs; ?> &nbsp;&nbsp;&nbsp;<input type="text" id="txt_cons_length" name="txt_cons_length" class="text_boxes_numeric"  onChange="fnc_consCalacultaion();" value="<?=$QtyPerBox; ?>" style="width:58px" /> Pcs</td>
                                                <td><?=$reqQtyTd; ?> &nbsp;&nbsp;&nbsp; <? //echo $costing_per[$cbo_costing_per];?>
                                                    <input type="text" id="txt_caculated_value" name="txt_caculated_value" class="text_boxes_numeric" style="width:58px" readonly value="<?=$calculatorstringarr[2]; ?>"/> <?=$boxPcs; ?>
                                                    <input type="hidden" id="txt_clacolator_param_value" name="txt_clacolator_param_value" class="text_boxes_numeric" value="" readonly />
                                                </td>
                                            </tr>
                                        </table>
                                    </fieldset>
                                    <?
                                }
                                else if($calparameter==8)//Sequines Calculator
                                {
                                    $RollLength=$calculatorstringarr[1];
                                    if($RollLength== "" || $RollLength==0)$RollLength=10000;
                                    ?>
                                    <fieldset>
                                        <legend>Sequines Calculator</legend>
                                        <table class="rpt_table" cellpadding="0" cellspacing="0" align="center" width="800" border="1" rules="all">
                                            <tr>
                                                <td width="190">Cons Per Garments &nbsp;&nbsp;&nbsp;<input type="text" id="txt_cons_per_gmts" name="txt_cons_per_gmts" class="text_boxes_numeric" onChange="fnc_consCalacultaion();" style="width:58px" value="<?=$calculatorstringarr[0]; ?>" /> Yds</td>
                                                <td width="190">Roll Length &nbsp;&nbsp;&nbsp;<input type="text" id="txt_cons_length" name="txt_cons_length" class="text_boxes_numeric" style="width:58px" onChange="fnc_consCalacultaion();" value="<?=$RollLength; ?>" /> Yds</td>
                                                <td>Cons 1 PCS &nbsp;&nbsp;&nbsp; <? //echo $costing_per[$cbo_costing_per];?>
                                                    <input type="text" id="txt_caculated_value" name="txt_caculated_value" class="text_boxes_numeric" style="width:58px" readonly value="<?=$calculatorstringarr[2]; ?>"/> Roll
                                                   <input type="hidden" id="txt_clacolator_param_value" name="txt_clacolator_param_value" class="text_boxes_numeric" value="" readonly />
                                                </td>
                                            </tr>
                                        </table>
                                    </fieldset>
                                    <?
                                }
                                else if($calparameter==9)//Eyelet Calculator
                                {
                                    $qtyPerGrs=$calculatorstringarr[1];
                                    if($qtyPerGrs== "" || $qtyPerGrs==0) $qtyPerGrs=144;
                    
                                    ?>
                                    <fieldset>
                                        <legend>Eyelet Calculator</legend>
                                        <table class="rpt_table" cellpadding="0" cellspacing="0" align="center" width="800" border="1" rules="all">
                                            <tr>
                                                <td width="190">Qty per Garments &nbsp;&nbsp;&nbsp;<input type="text" id="txt_qty_per_gmts" name="txt_qty_per_gmts" class="text_boxes_numeric" onChange="fnc_consCalacultaion();" style="width:58px" value="<?=$calculatorstringarr[0]; ?>"/> Pcs.</td>
                                                <td width="190">Qty per Grs &nbsp;&nbsp;&nbsp;<input type="text" id="txt_qty_per_grs" name="txt_qty_per_grs" class="text_boxes_numeric" onChange="fnc_consCalacultaion();" style="width:58px" value="<?=$qtyPerGrs;?>" /> Pcs. </td>
                                                <td>Qty per 1 PCS Garments &nbsp;&nbsp;&nbsp;
                                                    <input type="text" id="txt_caculated_value" name="txt_caculated_value" class="text_boxes_numeric" style="width:58px" readonly value="<?=$calculatorstringarr[2]; ?>" /> Grs.
                                                    <input type="hidden" id="txt_clacolator_param_value" name="txt_clacolator_param_value" class="text_boxes_numeric" value="" readonly />
                                                </td>
                                            </tr>
                                        </table>
                                    </fieldset>
                                    <?
                                }
                                elseif($calparameter==12) //Embroidery Thread-Comsumption Calculator
                                {
                                    $txt_cons_length=$calculatorstringarr[2];
                                    if($txt_cons_length=="" || $txt_cons_length==0) $txt_cons_length=3000;
                                    ?>
                                    <fieldset>
                                        <legend>Embroidery Thread-Comsumption Calculator</legend>
                                        <table class="rpt_table" cellpadding="0" cellspacing="0" align="center" width="800" border="1" rules="all">
                                            <tr>
                                                <td width="190">Cons Per Garment &nbsp;&nbsp;&nbsp;<input type="text" id="txt_cons_per_gmts" name="txt_cons_per_gmts" class="text_boxes_numeric" onChange="fnc_consCalacultaion();" style="width:58px" value="<?=$calculatorstringarr[0]; ?>"/> Mtr</td>
                                                <td width="190">Cons 1 PCS &nbsp;&nbsp;&nbsp; <? //echo $costing_per[$cbo_costing_per];?>&nbsp;&nbsp;&nbsp;<input type="text" id="txt_cons_for_mtr" name="txt_cons_for_mtr" class="text_boxes_numeric" style="width:58px" value="<?=$calculatorstringarr[1]; ?>" readonly/> Mtr</td>
                                                <td width="190">Cone Length &nbsp;&nbsp;&nbsp;<input type="text" id="txt_cons_length" name="txt_cons_length" class="text_boxes_numeric"  onChange="fnc_consCalacultaion();" style="width:58px" value="<?=$txt_cons_length; ?>"/> Mtr</td>
                                                <td>Cons 1 PCS &nbsp;&nbsp;&nbsp; <? //echo $costing_per[$cbo_costing_per];?>&nbsp;&nbsp;&nbsp;<input type="text" id="txt_caculated_value" name="txt_caculated_value" class="text_boxes_numeric" style="width:58px" readonly value="<?=$calculatorstringarr[3]; ?>" /> Cone
                                                   <input type="hidden" id="txt_clacolator_param_value" name="txt_clacolator_param_value" class="text_boxes_numeric" value="" readonly />
                                                </td>
                                            </tr>
                                        </table>
                                    </fieldset>
                                    <?
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                        	<td align="center" width="100%" class="button_container"><input type="button" class="formbutton" value="Close" onClick="js_set_value();"/> </td>
                        </tr>
                    </table>
                </div>
            </form>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="trims_cost_template_name_popup")
{
	extract($_REQUEST);
    echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);?>
    <script>
        var selected_name = new Array();
    	function fnc_close(data)
		{
            var data=data.split('_');
            document.getElementById('hidden_template_name').value=data[0];
           // parent.emailwindow.hide();
        }
		
        function insert_template_data(data)
        {
            var template_data=return_global_ajax_value( data, 'get_template_data', '', 'quick_costing_woven_controller');
            var template_data=trim(template_data) ;
            if(template_data)
            {
                $("tbody#template_date").html('');
                $("tbody#template_date").append(template_data);
                $('#check_all_tbl').css('display','block');
            }
        }
		
        function check_all_data() {
            var tbl_row_count = document.getElementById( 'trmplate_data_tbl' ).rows.length-1; 
            tbl_row_count = tbl_row_count;
            
            if(document.getElementById('check_all').checked){
                for( var i = 1; i <= tbl_row_count; i++ ) { 
                document.getElementById( 'search' + i ).style.backgroundColor = 'yellow';               
                if( jQuery.inArray( $('#txttemplatedata_' + i).val(), selected_name ) == -1 ) {
                    selected_name.push($('#txttemplatedata_' + i).val());
                }
                else{
                    for( var j = 0; j < selected_name.length; j++ ) {
                        if( selected_name[j] == $('#txttemplatedata_' + i).val() ) break;
                    }
                    selected_name.splice( j,1 );
                }
                                
                }
                var templatedata='';
                for( var i = 0; i < selected_name.length; i++ ) {
                    templatedata += selected_name[i] + '#';
                }
                templatedata = templatedata.substr( 0, templatedata.length - 1 );                
                $('#select_template_data').val( templatedata );
            }else{
                for( var i = 1; i <= tbl_row_count; i++ ) {             
                    if(i%2==0  ){
                        document.getElementById('search'+i).style.backgroundColor = '#FFFFFF';
                    }
                    if(i%2!=0 ){
                        document.getElementById('search'+i).style.backgroundColor = '#E9F3FF';
                    }
                    if( jQuery.inArray( $('#txttemplatedata_' + i).val(), selected_name ) == -1 ) {
                        selected_name.push($('#txttemplatedata_' + i).val());
                    }
                    else{
                        for( var j = 0; j < selected_name.length; j++ ) {
                            if( selected_name[j] == $('#txttemplatedata_' + i).val() ) break;
                        }
                        selected_name.splice( j,1 );
                    }                               
                }
                var templatedata='';
                for( var i = 0; i < selected_name.length; i++ ) {
                    templatedata += selected_name[i] + '#';
                }
                templatedata = templatedata.substr( 0, templatedata.length - 1 );                
                $('#select_template_data').val( templatedata );
            }
        }
        
        function toggle( x, origColor) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = (newColor == x.style.backgroundColor)? origColor : newColor;
            }
        }
        
        function onlyUnique(value, index, self) { 
            return self.indexOf(value) === index;
        }
        
        function js_set_value( str) {
            if($("#search"+str).css("display") !='none'){               
                if(str%2==0  ){
                    toggle( document.getElementById( 'search' + str ), '#FFFFFF');
                }
                if(str%2!=0 ){
                    toggle( document.getElementById( 'search' + str ), '#E9F3FF');
                }
                if( jQuery.inArray( $('#txttemplatedata_' + str).val(), selected_name ) == -1 ) {
                    selected_name.push($('#txttemplatedata_' + str).val());
                }
                else{
                    for( var i = 0; i < selected_name.length; i++ ) {
                        if( selected_name[i] == $('#txttemplatedata_' + str).val() ) break;
                    }
                    selected_name.splice( i,1 );
                }
            }
            var templatedata='';
            for( var i = 0; i < selected_name.length; i++ ) {
                templatedata += selected_name[i] + '#';
            }
            templatedata = templatedata.substr( 0, templatedata.length - 1 );                
            $('#select_template_data').val( templatedata );
        }
    </script>
    <?
	//$template_name_sql=sql_select("select  a.template_name  from lib_trim_costing_temp a, lib_trim_costing_temp_dtls b where a.id=b.lib_trim_costing_temp_id and b.buyer_id =$buyer_name and a.is_deleted=0 group by a.template_name");
	
	$template_name_sql=sql_select("select a.template_name from wo_lib_trim_cost_temp a,wo_lib_trim_cost_temp_dtls b where a.id=b.lib_trim_costing_temp_id and b.buyer_id =$buyer_name and a.is_deleted=0 group by  a.template_name");
    ?>
    </head>
    <body>
    <div align="center" style="width:100%;">
    	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="200" class="rpt_table" align="left">
    		<tr>
    			<input id="hidden_template_name" type="hidden" name="hidden_template_name">
    			<th width="100"><span style="font-size: 25px">Template Name</span></th>
    		</tr>
    		<? 
    		$i=0;
    		foreach ($template_name_sql as $row)
			{ 
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; ?>
				<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="insert_template_data('<?=$row[csf("template_name")] ?>')">
					<td width="100" align="center"><span style="font-size: 20px"><?=$row[csf("template_name")]; ?></span></td>
				</tr>
				<? $i++;
			} ?>

    	</table>
        <table id="trmplate_data_tbl" cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table">
            <input type="hidden" id="select_template_data" name="select_template_data"/>
            <thead>
                <tr>                    
                    <th width="100">User Code</th>
                    <th width="100">Group</th> 
                    <th width="100">Description</th>
                    <th width="100">Brand/Sup Ref.</th>
                    <th width="100">Nominated Supp</th>                           
                    <th width="70">Cons UOM</th>
                    <th width="95">Cons/Dzn Gmts</th>
                    <th width="50">Rate</th>
                    <th width="50">Amount</th>
                    <th>Apvl Req.</th>                
                </tr>
            </thead>
            <tbody id="template_date">                         
            </tbody>
        </table>
        <table width="420" id="check_all_tbl" cellspacing="0" cellpadding="0" style="border:none; display: none; margin-top: 10px" align="center">
        <tr>
            <td align="center" height="30" width="200" valign="bottom">
                <div style="width:300px"> 
                    <div style="width:150px; float:left" align="left">
                        <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();" /> Check / Uncheck All
                    </div>
                    <div style="width:150px; float:left" align="left">
                        <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                    </div>
                </div>
            </td>
        </tr>
    </table>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>    
    <?
    exit();
}

if($action == "get_template_data")
{
    $trim_variable=1;
    $trim_variable_sql=sql_select("select trim_rate from  variable_order_tracking where company_name='$data[4]' and variable_list=35 order by id");
    foreach($trim_variable_sql as $trim_variable_row)
    {
        $trim_variable= $trim_variable_row[csf('trim_rate')];
    }
    $lib_item_group_arr=return_library_array( "select item_name,id from lib_item_group where item_category=4 and is_deleted=0 and status_active=1 order by item_name", "id", "item_name");
    $trim_rate_from_library=return_library_array( "select a.id, max(b.rate) as rate from lib_item_group a, lib_item_group_rate b where a.id=b.mst_id and a.item_category=4 and a.status_active=1 and a.is_deleted=0 group by a.id", "id", "rate");
    $supplier_library=return_library_array( "select a.supplier_name, a.id from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(4,5) and a.is_deleted=0  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");
	$data_array=sql_select("SELECT * from wo_lib_trim_cost_temp where template_name='$data' and status_active=1 and is_deleted=0 order by id");
    $i=1;
    foreach( $data_array as $row )
    {
        $rate=$trim_rate_from_library[$row[csf('trims_group')]];
        $amount=$row[csf('cons_dzn_gmts')]*$trim_rate_from_library[$row[csf('trims_group')]];
        if($rate=="" || $rate==0)
        {
            $rate=$row[csf('purchase_rate')];   
            $amount=$row[csf('amount')];    
        }
        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
        $str="";
        $str=$lib_item_group_arr[$row[csf("trims_group")]].'***'.$row[csf('user_code')].'***'.$row[csf('trims_group')].'***'.$row[csf('cons_uom')].'***'.$row[csf('cons_dzn_gmts')].'***'.$row[csf('ex_per')].'***'.$row[csf('tot_cons')].'***'.$row[csf('purchase_rate')].'***'.$row[csf('amount')].'***'.$row[csf('apvl_req')].'***'.$row[csf('supplyer')].'***'.$row[csf('sup_ref')].'***'.$row[csf('item_description')].'***'.$supplier_library[$row[csf("supplyer")]];
     ?>
        <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" class="itemdata" onClick="js_set_value(<?=$i; ?>)" >
            <td>
                <?=$row[csf("user_code")]; ?>
                <input type="hidden" name="txttemplatedata_<?=$i; ?>" id="txttemplatedata_<?=$i; ?>" value="<?=$str; ?>"/>
            </td>
            <td><?=$lib_item_group_arr[$row[csf("trims_group")]]; ?></td>
            <td><?=$row[csf("item_description")];?></td>
            <td><?=$row[csf("sup_ref")]; ?></td>
            <td><?=$supplier_library[$row[csf("supplyer")]]; ?></td>
            <td><?=$unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
            <td align="right"><?=$row[csf("cons_dzn_gmts")];?></td>
            <td align="right"><?=number_format($rate,4,".","");?></td>
            <td align="right"><?=number_format($amount,4,".","");?></td>
            <td><?=$yes_no[$row[csf("apvl_req")]]; ?></td>
        </tr>
    <?  $i++; 
	} 
    exit();   
}

if($action=="openpopup_nomisupplier")
{
	echo load_html_head_contents("Nominated Supplier PopUp","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	?>
    <script>
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'supp_table' ).rows.length;
			tbl_row_count = tbl_row_count;
			if(document.getElementById('check_all').checked){
				for( var i = 1; i <= tbl_row_count; i++ ) {
				//js_set_value( i);
				document.getElementById( 'search' + i ).style.backgroundColor = 'yellow';
				if( jQuery.inArray( $('#txttrimsuppdata_' + i).val(), selected_name ) == -1 ) {
					selected_name.push($('#txttrimsuppdata_' + i).val());
				}
				else{
					for( var j = 0; j < selected_name.length; j++ ) {
						if( selected_name[j] == $('#txttrimsuppdata_' + i).val() ) break;
					}
					selected_name.splice( j,1 );
				}

				}
			}else{
				for( var i = 1; i <= tbl_row_count; i++ ) {
					if(i%2==0  ){
						document.getElementById('search'+i).style.backgroundColor = '#FFFFFF';
					}
					if(i%2!=0 ){
						document.getElementById('search'+i).style.backgroundColor = '#E9F3FF';
					}
					if( jQuery.inArray( $('#txttrimsuppdata_' + i).val(), selected_name ) == -1 ) {
						selected_name.push($('#txttrimsuppdata_' + i).val());
					}
					else{
						for( var j = 0; j < selected_name.length; j++ ) {
							if( selected_name[j] == $('#txttrimsuppdata_' + i).val() ) break;
						}
						selected_name.splice( j,1 );
					}
				}
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function onlyUnique(value, index, self) {
			return self.indexOf(value) === index;
		}

		var selected_name = new Array();

		function js_set_value( str ) {
			if($("#search"+str).css("display") !='none'){
				//toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
				if(str%2==0  ){
					toggle( document.getElementById( 'search' + str ), '#FFFFFF');
				}
				if(str%2!=0 ){
					toggle( document.getElementById( 'search' + str ), '#E9F3FF');
				}
				if( jQuery.inArray( $('#txttrimsuppdata_' + str).val(), selected_name ) == -1 ) {
					selected_name.push($('#txttrimsuppdata_' + str).val());
				}
				else{
					for( var i = 0; i < selected_name.length; i++ ) {
						if( selected_name[i] == $('#txttrimsuppdata_' + str).val() ) break;
					}
					selected_name.splice( i,1 );
				}
			}
			var trimgroupdata='';
			for( var i = 0; i < selected_name.length; i++ ) {
				trimgroupdata += selected_name[i] + ',';
			}
			trimgroupdata = trimgroupdata.substr( 0, trimgroupdata.length - 1 );

			$('#suppdata').val( trimgroupdata );
		}
		
		function close_supp_data()
		{
			var s=$('#suppdata').val();
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;">
        <input type="hidden" id="gid" name="gid"/>
        <input type="hidden" id="suppdata" name="suppdata"/>
        <?
		$suppcond="";
		if($type==1) $suppcond="and b.party_type in(1,9)";
		else if($type==2 || $type==4) $suppcond="and b.party_type in(23)";
		else if($type==5) $suppcond="and b.party_type in(4,5)";
		
		$tag_buyer=return_field_value("tag_buyer as tag_buyer", "lib_supplier_tag_buyer", "tag_buyer=$buyer","tag_buyer");
		if($tag_buyer!='')
		{
			$supplier_library=return_library_array( "select a.supplier_name,a.id from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_buyer c where a.id=b.supplier_id and a.id=c.supplier_id and b.supplier_id=c.supplier_id and c.tag_buyer=$tag_buyer and a.is_deleted=0 and a.status_active=1 $suppcond group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");
		}
		else
		{
			$supplier_library=return_library_array( "select a.supplier_name,a.id from  lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.is_deleted=0 and a.status_active=1 $suppcond group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");
		}
		
		?>
        <table width="420" cellspacing="0" class="rpt_table" border="0" rules="all">
            <thead>
            	<th width="40">SL</th>
                <th>Supplier Name</th>
            </thead>
        </table>
        <div style="width:420px; overflow-y:scroll; max-height:340px;" >
        <table width="400" cellspacing="0" class="rpt_table" border="0" rules="all" id="supp_table">
            <tbody>
				<?
                $i=1;
                if($nominasupplier != '')
                {
                	$nominatedsup_arr = explode(",", $nominasupplier);
                }
                foreach($supplier_library as $sid=>$sname)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$str="";
					$str=$sid.'***'.$sname;
					/*if(in_array($sid, $nominatedsup_arr)){
						$bgcolor = 'yellow';
					}*/
					?>
					<tr id="search<?=$i;?>" onClick="js_set_value(<?=$i; ?>);" bgcolor="<?=$bgcolor; ?>" style="cursor:pointer">
						<td width="40"><?=$i; ?></td>
                        <td><?=$sname; ?>
                        	<input type="hidden" name="txttrimsuppdata_<?=$i; ?>" id="txttrimsuppdata_<?=$i; ?>" value="<?=$str; ?>"/>
                        </td>
					</tr>
					<script type="text/javascript">
                        <?
                            if(in_array($sid, $nominatedsup_arr)){
                                echo "var match_data=1;\n";
                                echo "var sequ=".$i.";\n";
                            }
                            else{
                                echo "var match_data=2;\n";
                            }
                        ?>
                        if(match_data==1)
                        {
                           js_set_value(sequ);
                        }
                    </script>
					<?
					$i++;
                }
                ?>
            </tbody>
        </table>
        </div>
        <table width="420" cellspacing="0" cellpadding="0" style="border:none" align="center">
        <tr>
            <td align="center" height="30" valign="bottom">
                <div style="width:100%">
                    <div style="width:50%; float:left" align="left">
                    	<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();" /> Check / Uncheck All
                    </div>
                    <div style="width:50%; float:left" align="left">
                    	<input type="button" name="close" onClick="close_supp_data();" class="formbutton" value="Close" style="width:100px" />
                    </div>
                </div>
            </td>
        </tr>
	</table>
    </div>
    </body>
	<script>setFilterGrid('supp_table',-1);</script>
	<!--<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>-->
	</html>
	<?
	exit();
}