<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];

//========== user credential start ========
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");

$location_id = $userCredential[0][csf('location_id')];
$location_credential_cond="";

if ($location_id !='') {
    $location_credential_cond = " and id in($location_id)";
}

if($action=="check_conversion_rate")
{
	$data=explode("**",$data);
	if($db_type==0) $conversion_date=change_date_format($data[1], "Y-m-d", "-",1); else $conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	$currency_rate=set_conversion_rate( $data[0], $conversion_date, $data[2]);
	
	$location_cpm_cost=0;
	$sql_data=sql_select("select yarn_iss_with_serv_app from variable_order_tracking where company_name='$data[2]' and variable_list=67 and status_active=1 and is_deleted=0");
	foreach($sql_data as $sql_row)
	{
		$location_cpm_cost=$sql_row[csf('yarn_iss_with_serv_app')];
	}

	if($data[1]=="" || $data[1]==0)
	{
		if($db_type==0) $txt_quotation_date=change_date_format(date('d-m-Y'), "yyyy-mm-dd", "-"); else if($db_type==2) $txt_quotation_date=change_date_format(date('d-m-Y'), "yyyy-mm-dd", "-",1);
	}
	else
	{
		if($db_type==0) $txt_quotation_date=change_date_format($data[1], "yyyy-mm-dd", "-"); else if($db_type==2)$txt_quotation_date=change_date_format($data[1], "yyyy-mm-dd", "-",1)	;
	}
	
	if($db_type==0) $limit_cond="LIMIT 1"; else if($db_type==2) $limit_cond="";
	if($location_cpm_cost==1 && $data[3]!=0)//Location Wse variable Yes
	{
		$sql="select a.depreciation_amorti, a.operating_expn, a.interest_expense, a.income_tax, b.monthly_cm_expense, b.no_factory_machine, b.working_hour, b.cost_per_minute from lib_standard_cm_entry a,lib_standard_cm_entry_dtls b where a.id=b.mst_id and a.company_id=$data[2] and b.location_id='$data[3]' and '$conversion_date' between b.applying_period_date and b.applying_period_to_date  and b.status_active=1 and b.is_deleted=0 $limit_cond";
	}
	else
	{
		$sql="select  depreciation_amorti, operating_expn, interest_expense, income_tax, monthly_cm_expense, no_factory_machine, working_hour, cost_per_minute from lib_standard_cm_entry where company_id=$data[2] and '$conversion_date' between applying_period_date and applying_period_to_date and status_active=1 and is_deleted=0 $limit_cond";
	}
	//echo $sql;
	$data_array=sql_select($sql); $cost_per_minute=0;
	foreach ($data_array as $row)
	{
/*		if($row[csf("monthly_cm_expense")] !="") $monthly_cm_expense=$row[csf("monthly_cm_expense")];
		if($row[csf("no_factory_machine")] !="") $no_factory_machine=$row[csf("no_factory_machine")];
		if($row[csf("working_hour")] !="") $working_hour=$row[csf("working_hour")];
*/		if($row[csf("cost_per_minute")] !="") $cost_per_minute=$row[csf("cost_per_minute")];
		/*if($row[csf("depreciation_amorti")] !="") $depreciation_amorti=$row[csf("depreciation_amorti")];
		if($row[csf("operating_expn")] !="") $operating_expn=$row[csf("operating_expn")];
		if($row[csf("interest_expense")] !="") $interest_expense=$row[csf("interest_expense")];
		if($row[csf("income_tax")] !="") $income_tax=$row[csf("income_tax")];*/
	}
	
	echo "1_".$currency_rate."_".$cost_per_minute;
	exit();
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

if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent", 130, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21))  order by buyer_name","id,buyer_name", 1, "-Select Agent-", $selected, "" );
	exit();
}

if ($action=="load_drop_down_revise_no")
{
	$ex_data=explode('__',$data);
	$user_arr=return_library_array("select id, user_name from user_passwd","id","user_name");
	if($ex_data[1]=="") $option_cond=""; else $option_cond="and option_id='$ex_data[1]'";
	//echo "select revise_no from qc_mst where cost_sheet_no='$ex_data[0]' $option_cond order by revise_no Desc";
	$sql=sql_select("select inserted_by, revise_no from qc_mst where cost_sheet_no='$ex_data[0]' and entry_form=444 and status_active=1 and is_deleted=0 $option_cond order by revise_no Desc");
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

if ($action=="load_drop_down_option_id")
{
	$ex_data=explode('__',$data);
	/*if($ex_data[1]!=0)
		$selectopt=$ex_data[1];
	else
		$selectopt=$selected;*/
		//load_drop_down('requires/quick_costing_v2_controller', '".$row[csf("cost_sheet_no")].'__'.$row[csf("option_id")].'__'.$row[csf("revise_no")]."', 'load_drop_down_revise_no', 'revise_td');
	$max_option=return_field_value("max(option_id) as option_id","qc_mst","cost_sheet_no='$ex_data[0]' and is_deleted=0 and status_active=1 and entry_form=444","option_id");
	$user_arr=return_library_array("select id, user_name from user_passwd","id","user_name");
	if ($db_type==0) 
		$sql_op=sql_select("select option_id, inserted_by, concat(option_id,'-',option_remarks) as option_name from qc_mst where cost_sheet_no='$ex_data[0]' and entry_form=444 and status_active=1 and is_deleted=0 order by option_id Desc");
	else if ($db_type==2)
		$sql_op=sql_select("select option_id, inserted_by, option_id || '-' || option_remarks as option_name from qc_mst where cost_sheet_no='$ex_data[0]' and status_active=1 and is_deleted=0 and entry_form=444 order by option_id Desc");
	
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
	echo create_drop_down( "cbo_option_id", 45, $rvs,"", 0, "-0-", $selectopt, "load_drop_down( 'requires/quick_costing_v2_controller', document.getElementById('txt_costSheetNo').value+'__'+this.value+'__'+0, 'load_drop_down_revise_no', 'revise_td'); fnc_option_rev( document.getElementById('cbo_revise_no').value+'***'+this.value+'***2' );" );//
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
		http.open("POST","quick_costing_v2_controller.php",true);
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
				show_list_view( '','template_list_view','save_up_list_view','quick_costing_v2_controller','setFilterGrid(\'tbl_upListView\',-1)');
			}
		}
	}
	
	function get_temp_data(temp_id)
	{
		var list_view_grid = return_global_ajax_value( temp_id, 'load_php_dtls_form', '', 'quick_costing_v2_controller');
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
	show_list_view( '','template_list_view','save_up_list_view','quick_costing_v2_controller','setFilterGrid(\'tbl_upListView\',-1)');
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

if($action=="ydsrate_details_popup")
{
	echo load_html_head_contents("YDS Details Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
    <script>
		function append_row(str)
		{
			var inc=str;
			var counter =$('#tbl_rateDetails tbody tr').length; 
			var i=inc;
			if (counter!=i)
			{
				return false;
			}
			else
			{
				i++;
				$("#tbl_rateDetails tbody tr:last").clone().find("input,select").each(function() {
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
						'value': function(_, value) { return value }
					});
				}).end().appendTo("#tbl_rateDetails tbody");
				
				$('#txtydscons_'+i).removeAttr("onChange").attr("onChange","fnc_consamt("+i+")");
				$('#txtydsexper_'+i).removeAttr("onChange").attr("onChange","fnc_consamt("+i+")");
				$('#txtydsrate_'+i).removeAttr("onChange").attr("onChange","fnc_consamt("+i+")");
				
				$('#increasewash_'+i).removeAttr("onClick").attr("onClick","append_row("+i+");");
				$('#decreasewash_'+i).removeAttr("onClick").attr("onClick","fnc_remove_row("+i+");");
				
				$('#cboydsbodypart_'+i).val(0);
				$('#txtydsdesc_'+i).val("");
				$('#txtydscons_'+i).val("");
				$('#txtydsexper_'+i).val("");
				$('#txtydstotcons_'+i).val("");
				$('#txtydsrate_'+i).val("");
				$('#txtydsamt_'+i).val("");
			}
			fnc_consamt();
		}
		
		function fnc_remove_row(inc)
		{
			var counter =$('#tbl_rateDetails tbody tr').length; 
			if(counter!=1)
			{
				var index=inc-1;
				$("table#tbl_rateDetails tbody tr:eq("+index+")").remove();
				
				var numRow = $('table#tbl_rateDetails tbody tr').length;
				for(var i=1; i<=counter; i++)
				{
					var index=i-1;
					$("#tbl_rateDetails tbody tr:eq("+index+")").find("input,select").each(function() {
						$(this).attr({
							'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						});
					});
					
					$("#tbl_rateDetails tbody tr:eq("+index+")").each(function(){
						$('#txtydscons_'+i).removeAttr("onChange").attr("onChange","fnc_consamt("+i+")");
						$('#txtydsexper_'+i).removeAttr("onChange").attr("onChange","fnc_consamt("+i+")");
						$('#txtydsrate_'+i).removeAttr("onChange").attr("onChange","fnc_consamt("+i+")");
						
						$('#increasewash_'+i).removeAttr("onClick").attr("onClick","append_row("+i+");");
						$('#decreasewash_'+i).removeAttr("onClick").attr("onClick","fnc_remove_row("+i+");");
					});
				}
				fnc_consamt();
			}
		}
		
		function fnc_consamt()
		{
			var rowCount =$('#tbl_rateDetails tbody tr').length; 
			var gcons=gtotcons=gamt=0;
			for(var i=1; i<=rowCount; i++)
			{
				var cons=$('#txtydscons_'+i).val()*1;
				gcons+=(cons*1);
				var exper=$('#txtydsexper_'+i).val()*1;
				var totcons=(cons*1)+((cons*1)*((exper*1)/100));
				$('#txtydstotcons_'+i).val(number_format(totcons,2,'.',''));
				
				gtotcons+=(totcons*1);
				var rate=$('#txtydsrate_'+i).val()*1;
				var amt=(totcons*1)*rate;
				
				$('#txtydsamt_'+i).val(number_format(amt,2,'.',''));
				gamt+=(amt*1);
			}
			var avgexper=(((gtotcons*1)-(gcons*1))/(gcons*1))*100;
			
			//((gcons*1)/(gtotcons*1))*100;
			var avgrate=(gamt*1)/(gtotcons*1);
			$('#txttotydscons').val(number_format(gcons,4,'.',''));
			$('#txtydtotsexper').val(number_format(avgexper,4,'.',''));
			$('#txtydstottotcons').val(number_format(gtotcons,4,'.',''));
			$('#txtydstotrate').val(number_format(avgrate,2,'.',''));
			$('#txtydstotamt').val(number_format(gamt,2,'.',''));
		}
		
		function js_set_value()
		{
			fnc_consamt();
			var row=$('#tbl_rateDetails tbody tr').length; 
			var consData="";
			for(var i=1; i<=row; i++)
			{
				if( ($('#txtydscons_'+i).val()*1)>0 )
				{
					if(consData=="") consData+=$('#cboydsbodypart_'+i).val()+'~~'+$('#txtydscons_'+i).val()+'~~'+$('#txtydsexper_'+i).val()+'~~'+$('#txtydstotcons_'+i).val()+'~~'+$('#txtydsrate_'+i).val()+'~~'+$('#txtydsamt_'+i).val()+'~~'+$('#txtydsdesc_'+i).val();
					else consData+="@@!"+$('#cboydsbodypart_'+i).val()+'~~'+$('#txtydscons_'+i).val()+'~~'+$('#txtydsexper_'+i).val()+'~~'+$('#txtydstotcons_'+i).val()+'~~'+$('#txtydsrate_'+i).val()+'~~'+$('#txtydsamt_'+i).val()+'~~'+$('#txtydsdesc_'+i).val();
				}
			}
			document.getElementById('hidden_all_data').value=consData;
			document.getElementById('txttotydscons').value;
			document.getElementById('txtydtotsexper').value;
			document.getElementById('txtydstottotcons').value;
			document.getElementById('txtydstotrate').value;
			document.getElementById('txtydstotamt').value;
			parent.emailwindow.hide();
		}
		
		function fnc_row_generate()
		{
			var dataall=$('#hidden_all_data').val();
			if(dataall!="")
			{
				var exdata=dataall.split("@@!");
				var i=0;
				for(var q=1; q<=exdata.length; q++)
				{
					if(trim(exdata[i])!="")
					{
						append_row(q);
						var datadtls=exdata[i].split("~~");
						
						$('#cboydsbodypart_'+q).val(datadtls[0]);
						$('#txtydscons_'+q).val(datadtls[1]);
						$('#txtydsexper_'+q).val(datadtls[2]);
						$('#txtydstotcons_'+q).val(datadtls[3]);
						$('#txtydsrate_'+q).val(datadtls[4]);
						$('#txtydsamt_'+q).val(datadtls[5]);
						$('#txtydsdesc_'+q).val(datadtls[6]);
						
						i++;
					}
				}
				fnc_consamt();
			}
		}
		
	</script>
	</head>
	<body>
    <div id="rate_details"  align="center">            
        <form name="rateDetails_1" id="rateDetails_1" autocomplete="off">
            <table width="600" cellspacing="0" border="1" class="rpt_table" id="tbl_rateDetails" rules="all">
            	<thead>
                	<th width="120">Body Part</th>
                    <th width="110">Description</th>
                    <th width="60">Cons</th>
                	<th width="35">EX %</th>
                    <th width="60">Tot Cons. <input type="hidden" class="text_boxes" name="hidden_all_data" id="hidden_all_data" value="<?=$rateData; ?>"/></th>
                    <th width="50">Rate</th>
                    <th width="60">Value</th>
                    <th>&nbsp;</th>
                </thead>
                <tbody>
                    <tr>
                        <td><?=create_drop_down( "cboydsbodypart_1", 120, $body_part,'', 1, "-Body Part 1-",$selected, "","","","","",""); ?></td>
                        <td><input style="width:97px;" type="text" class="text_boxes" name="txtydsdesc_1" id="txtydsdesc_1" /></td>
                        <td><input style="width:47px;" type="text" class="text_boxes_numeric" name="txtydscons_1" id="txtydscons_1" onChange="fnc_consamt();" /></td>
                        <td><input style="width:22px;" type="text" class="text_boxes_numeric" name="txtydsexper_1" id="txtydsexper_1" onChange="fnc_consamt();" /></td>
                        <td><input style="width:47px;" type="text" class="text_boxes_numeric" name="txtydstotcons_1" id="txtydstotcons_1" disabled /></td>
                        <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtydsrate_1" id="txtydsrate_1" onChange="fnc_consamt();" /></td>
                        <td><input style="width:47px;" type="text" class="text_boxes_numeric" name="txtydsamt_1" id="txtydsamt_1" disabled /></td>
                        <td>
                            <input type="button" id="increasewash_1" name="increasewash_1" style="width:30px" class="formbutton" value="+" onClick="append_row(1);" />
                            <input type="button" id="decreasewash_1" name="decreasewash_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fnc_remove_row(1);" />
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr bgcolor="#CCCCAA">
                        <td colspan="2" align="right">Total=</td>
                        <td><input style="width:47px;" type="text" class="text_boxes_numeric" name="txttotydscons" id="txttotydscons" disabled /></td>
                        <td><input style="width:22px;" type="text" class="text_boxes_numeric" name="txtydtotsexper" id="txtydtotsexper" disabled /></td>
                        <td><input style="width:47px;" type="text" class="text_boxes_numeric" name="txtydstottotcons" id="txtydstottotcons" disabled /></td>
                        <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtydstotrate" id="txtydstotrate" disabled /></td>
                        <td><input style="width:47px;" type="text" class="text_boxes_numeric" name="txtydstotamt" id="txtydstotamt" disabled /></td>
                        <td>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
            <table width="600" cellspacing="0" class="" border="0">
                <tr>
                    <td align="center" width="100%" class="button_container"><input type="button" class="formbutton" value="Close" onClick="js_set_value();"/></td> 
                </tr>
            </table>
        </form>
    </div>
    </body>
    <script>fnc_row_generate(); </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();	
}

if($action=="rate_details_popup")
{
	echo load_html_head_contents("Yarn Details Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
    <script>
		var costingDate='<?=$costingDate; ?>';
		//alert(costingDate)
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
			var tot_yarn_cost=0; var yarn=3; var yarnDtls_str=""; var yarnlibId_str="";
			for(var y=1; y<=yarn; y++)
			{
				tot_yarn_cost+=$('#txt_yarn_cost'+y).val()*1;
				yarnDtls_str+=$('#txt_yarn_dtls'+y).val()+'~~'+$('#txt_ratePerKg'+y).val()+'~~'+$('#txt_yarn_costPer'+y).val()+'~~'+$('#txt_yarn_cost'+y).val()+'~~';
				yarnlibId_str+=$('#hid_libyarnid_'+y).val()+'~~';
			}
			
			$('#txt_tot_yarn_cost').val( number_format(tot_yarn_cost,4) );
			
			var tot_knit_cost=0; var knit=3; var knitDtls_str="";
			for(var k=1; k<=knit; k++)
			{
				tot_knit_cost+=$('#txtknitcost'+k).val()*1;
				
				knitDtls_str+=$('#txtknitdtls'+k).val()+'~~'+$('#hidlibknitid_'+k).val()+'~~'+$('#txtknitcost'+k).val()+'~~';
			}
			
			$('#txt_knit_charge').val( number_format(tot_knit_cost,4) );
			
			var tot_dye_cost=0; var dye=3; var dyeDtls_str="";
			for(var d=1; d<=dye; d++)
			{
				tot_dye_cost+=$('#txtdyecost'+d).val()*1;
				dyeDtls_str+=$('#txtdyedtls'+d).val()+'~~'+$('#hidlibdyeid_'+d).val()+'~~'+$('#txtdyecost'+d).val()+'~~';
			}
			
			$('#txt_dyeing_charge').val( number_format(tot_dye_cost,4) );
			
			var tot_aop_cost=0; var aop=3; var aopDtls_str="";
			for(var a=1; a<=aop; a++)
			{
				tot_aop_cost+=$('#txtaopcost'+a).val()*1;
				if(aopDtls_str=="") aopDtls_str=$('#txtaopdtls'+a).val()+'~~'+$('#hidlibaopid_'+a).val()+'~~'+$('#txtaopcost'+a).val();
				else aopDtls_str+='~~'+$('#txtaopdtls'+a).val()+'~~'+$('#hidlibaopid_'+a).val()+'~~'+$('#txtaopcost'+a).val();
			}
			
			$('#txt_aop_charge').val( number_format(tot_aop_cost,4) );
			//alert(aopDtls_str);
			//var knit_cost=$('#txt_knit_charge').val()*1;
			//var dye_cost=$('#txt_dyeing_charge').val()*1;
			//var aop_cost=$('#txt_aop_charge').val()*1;
			var fini_cost=$('#txt_fin_charge').val()*1;
			var other_cost=$('#txt_other_cost').val()*1;
			
			var tot_cost=tot_knit_cost+tot_dye_cost+tot_aop_cost+fini_cost+tot_yarn_cost+other_cost;
			$('#txt_tot_cost').val( number_format(tot_cost,4));
			
			var process_lossPer=($('#txt_process_loss_per').val()*1)/100;
			//processLoss_cost=((tot_cost/(1-process_lossPer))-tot_cost);
			processLoss_cost=((tot_cost*process_lossPer));
			//alert(process_lossPer)
			$('#txt_process_loss').val( number_format(processLoss_cost,4) );
			
			var process_loss=$('#txt_process_loss').val()*1;
			
			var tot_rate=tot_cost+process_loss;
			
			$('#txt_tot_rate').val( number_format(tot_rate,4));
			
			var all_data=""; var all_datastr="";
			
			all_data=yarnDtls_str+$('#txt_tot_yarn_costPer').val()+'~~'+$('#txt_tot_yarn_cost').val()+'~~'+$('#txt_knit_charge').val()+'~~'+$('#txt_dyeing_charge').val()+'~~'+$('#txt_aop_charge').val()+'~~'+$('#txt_fin_charge').val()+'~~'+$('#txt_other_cost').val()+'~~'+$('#txt_tot_cost').val()+'~~'+$('#txt_process_loss_per').val()+'~~'+$('#txt_process_loss').val()+'~~'+$('#txt_tot_rate').val()+'~~'+yarnlibId_str+knitDtls_str+dyeDtls_str+aopDtls_str;
			
			//all_data = all_datastr.substring(all_datastr.length - 2, all_datastr.length);
			
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
			
			$('#hid_libyarnid_1').val(exData[23]);
			$('#hid_libyarnid_2').val(exData[24]);
			$('#hid_libyarnid_3').val(exData[25]);
			
			$('#txtknitdtls1').val(exData[26]);
			$('#hidlibknitid_1').val(exData[27]);
			$('#txtknitcost1').val(exData[28]);
			
			$('#txtknitdtls2').val(exData[29]);
			$('#hidlibknitid_2').val(exData[30]);
			$('#txtknitcost2').val(exData[31]);
			
			$('#txtknitdtls3').val(exData[32]);
			$('#hidlibknitid_3').val(exData[33]);
			$('#txtknitcost3').val(exData[34]);
			
			$('#txtdyedtls1').val(exData[35]);
			$('#hidlibdyeid_1').val(exData[36]);
			$('#txtdyecost1').val(exData[37]);
			
			$('#txtdyedtls2').val(exData[38]);
			$('#hidlibdyeid_2').val(exData[39]);
			$('#txtdyecost2').val(exData[40]);
			
			$('#txtdyedtls3').val(exData[41]);
			$('#hidlibdyeid_3').val(exData[42]);
			$('#txtdyecost3').val(exData[43]);
			
			$('#txtaopdtls1').val(exData[44]);
			$('#hidlibaopid_1').val(exData[45]);
			$('#txtaopcost1').val(exData[46]);
			
			$('#txtaopdtls2').val(exData[47]);
			$('#hidlibaopid_2').val(exData[48]);
			$('#txtaopcost2').val(exData[49]);
			
			$('#txtaopdtls3').val(exData[50]);
			$('#hidlibaopid_3').val(exData[51]);
			$('#txtaopcost3').val(exData[52]);
		}
		
		function fnc_details_popup(row,action,type)
        {
            //alert(row)
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe','quick_costing_v2_controller.php?action='+action+'&costingDate='+costingDate,'Details Popup', 'width=580px,height=300px,center=1,resize=0','../../');
            emailwindow.onclose=function()
            {
                var popupData=this.contentDoc.getElementById("popupData").value;
                var popupData=popupData.split('_');
               // alert(popupData);
                if(action=='yarn_count_popup'){
                    $('#hid_libyarnid_'+row).val(popupData[0]);
					var exdata=popupData[1]+', '+popupData[2]+', '+popupData[3];
                    $('#txt_yarn_dtls'+row).val(exdata);
                }
				if(action=='knit_popup')
				{
					$('#hidlibknitid_'+row).val(popupData[0]);
					var exdata=popupData[1]+', '+popupData[2]+', '+popupData[3];
                    $('#txtknitdtls'+row).val(exdata);
				}
				if(action=='dye_popup')
				{
					$('#hidlibdyeid_'+row).val(popupData[0]);
					var exdata=popupData[1]+', '+popupData[2]+', '+popupData[3]+', '+popupData[4];
                    $('#txtdyedtls'+row).val(exdata);
				}
				if(action=='aop_popup')
				{
					$('#hidlibaopid_'+row).val(popupData[0]);
					var exdata=popupData[1]+', '+popupData[2]+', '+popupData[3];
                    $('#txtaopdtls'+row).val(exdata);
				}
            }
        }
		
		function fnc_value_reset(type,inc)
		{
			if(type==1)
			{
				$('#txt_yarn_dtls'+inc).val('');
				$('#hid_libyarnid_'+inc).val('');
				$('#txt_ratePerKg'+inc).val('');
				$('#txt_yarn_costPer'+inc).val('');
				$('#txt_yarn_cost'+inc).val('');
				fnc_yarn_cost_percent_check(inc);
			}
			else if(type==2)
			{
				$('#txtknitdtls'+inc).val('');
				$('#hidlibknitid_'+inc).val('');
				$('#txtknitcost'+inc).val('');
			}
			else if(type==3)
			{
				$('#txtdyedtls'+inc).val('');
				$('#hidlibdyeid_'+inc).val('');
				$('#txtdyecost'+inc).val('');
			}
			else if(type==4)
			{
				$('#txtaopdtls'+inc).val('');
				$('#hidlibaopid_'+inc).val('');
				$('#txtaopcost'+inc).val('');
			}
			fnc_total_rate();
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
                    <th width="60">Cost <input type="hidden" class="text_boxes" name="hidden_all_data" id="hidden_all_data" value="<?=$rateData; ?>"/></th>
                    <th>&nbsp;</th>
                </thead>
                <? $yarn=3; 
				for($y=1; $y<=$yarn; $y++) { ?>
                <tr>
                    <td><input style="width:147px;" type="text" class="text_boxes" name="txt_yarn_dtls<?=$y; ?>" id="txt_yarn_dtls<?=$y; ?>" placeholder="Br. Yarn Cost <?=$y; ?>" onChange="fnc_total_rate();" onClick="fnc_details_popup('<?=$y; ?>','yarn_count_popup','1');" readonly />
                    	<input type="hidden" class="text_boxes" name="hid_libyarnid_<?=$y; ?>" id="hid_libyarnid_<?=$y; ?>" value=""/>
                    </td>
                    <td><input style="width:57px;" type="text" class="text_boxes_numeric" name="txt_ratePerKg<?=$y; ?>" id="txt_ratePerKg<?=$y; ?>" placeholder="Rate <?=$y; ?>" onChange="fnc_yarn_cost_percent_check(<?=$y; ?>);" /></td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_yarn_costPer<?=$y; ?>" id="txt_yarn_costPer<?=$y; ?>" onChange="fnc_yarn_cost_percent_check(<?=$y; ?>);" /></td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_yarn_cost<?=$y; ?>" id="txt_yarn_cost<?=$y; ?>" onChange="fnc_total_rate();" /></td>
                    <td><input type="button" id="btnreset_<?=$y; ?>" name="btnreset_<?=$y; ?>" style="width:30px" class="formbutton" value="-" onClick="fnc_value_reset(1,<?=$y; ?>);" /></td>
                </tr>
                <? } ?>
               
                <tr bgcolor="#CCCCAA">
                    <td colspan="2" align="right">Total Yarn Cost=</td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_tot_yarn_costPer" id="txt_tot_yarn_costPer" value="100" disabled /></td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_tot_yarn_cost" id="txt_tot_yarn_cost" disabled /></td>
                    <td>&nbsp;</td>
                </tr>
                
                <tr>
                	<td colspan="5" align="center" bgcolor="#99FFCC"><b>Knitting Charge Details</b></td>
                </tr>
                <? $knit=3; 
				for($k=1; $k<=$knit; $k++) { ?>
                <tr>
                    <td colspan="3"><input style="width:280px;" type="text" class="text_boxes" name="txtknitdtls<?=$k; ?>" id="txtknitdtls<?=$k; ?>" placeholder="Br. Knit Dtls <?=$k; ?>" onClick="fnc_details_popup('<?=$k; ?>','knit_popup','2');" readonly/>
                    <input type="hidden" class="text_boxes" name="hidlibknitid_<?=$k; ?>" id="hidlibknitid_<?=$k; ?>" value=""/>
                    </td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txtknitcost<?=$k; ?>" id="txtknitcost<?=$k; ?>" onChange="fnc_total_rate();" /></td>
                    <td><input type="button" id="btnreset_<?=$k; ?>" name="btnreset_<?=$k; ?>" style="width:30px" class="formbutton" value="-" onClick="fnc_value_reset(2,<?=$k; ?>);" /></td>
                </tr>
                <? } ?>
                
                <tr bgcolor="#CCCCAA">
                    <td colspan="3" align="right">Total Knitting Charge=</td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_knit_charge" id="txt_knit_charge" readonly placeholder="Display" /></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                	<td colspan="5" align="center" bgcolor="#99FFCC"><b>Dyeing Charge Details</b></td>
                </tr>
                <? $dye=3; 
				for($d=1; $d<=$dye; $d++) { ?>
                <tr>
                    <td colspan="3"><input style="width:280px;" type="text" class="text_boxes" name="txtdyedtls<?=$d; ?>" id="txtdyedtls<?=$d; ?>" placeholder="Br. Dyeing Dtls <?=$d; ?>" onClick="fnc_details_popup('<?=$d; ?>','dye_popup','3');" readonly/>
                    <input type="hidden" class="text_boxes" name="hidlibdyeid_<?=$d; ?>" id="hidlibdyeid_<?=$d; ?>" value=""/>
                    </td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txtdyecost<?=$d; ?>" id="txtdyecost<?=$d; ?>" onChange="fnc_total_rate();" /></td>
                    <td><input type="button" id="btnreset_<?=$d; ?>" name="btnreset_<?=$d; ?>" style="width:30px" class="formbutton" value="-" onClick="fnc_value_reset(3,<?=$d; ?>);" /></td>
                </tr>
                <? } ?>
                
                <tr bgcolor="#CCCCAA">
                    <td colspan="3" align="right">Total Dyeing Charge=</td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_dyeing_charge" id="txt_dyeing_charge" readonly placeholder="Display" /></td>
                    <td>&nbsp;</td>
                </tr>
                
                <tr>
                	<td colspan="5" align="center" bgcolor="#99FFCC"><b>AOP Charge Details</b></td>
                </tr>
                <? $aop=3; 
				for($a=1; $a<=$aop; $a++) { ?>
                <tr>
                    <td colspan="3"><input style="width:280px;" type="text" class="text_boxes" name="txtaopdtls<?=$a; ?>" id="txtaopdtls<?=$a; ?>" placeholder="Br. AOP Dtls <?=$a; ?>" onClick="fnc_details_popup('<?=$a; ?>','aop_popup','4');" readonly/>
                    <input type="hidden" class="text_boxes" name="hidlibaopid_<?=$a; ?>" id="hidlibaopid_<?=$a; ?>" value=""/>
                    </td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txtaopcost<?=$a; ?>" id="txtaopcost<?=$a; ?>" onChange="fnc_total_rate();" /></td>
                    <td><input type="button" id="btnreset_<?=$a; ?>" name="btnreset_<?=$a; ?>" style="width:30px" class="formbutton" value="-" onClick="fnc_value_reset(4,<?=$a; ?>);" /></td>
                </tr>
                <? } ?>
                
                <tr bgcolor="#CCCCAA">
                    <td colspan="3" align="right">Total AOP Charge=</td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_aop_charge" id="txt_aop_charge" onChange="fnc_total_rate();" readonly placeholder="Display" /></td>
                     <td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="3">Finishing Charge</td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_fin_charge" id="txt_fin_charge" onChange="fnc_total_rate();" /></td>
                    <td>&nbsp;</td>
                </tr>
                
                
                
                <tr>
                    <td colspan="3">Other Cost</td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_other_cost" id="txt_other_cost" onChange="fnc_total_rate();" /></td>
                    <td>&nbsp;</td>
                </tr>
                <tr bgcolor="#CCCCAA">
                    <td colspan="3">Total Cost</td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_tot_cost" id="txt_tot_cost" disabled /></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td  colspan="2">Process Loss</td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_process_loss_per" id="txt_process_loss_per" onChange="fnc_total_rate();" /></td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_process_loss" id="txt_process_loss" onChange="fnc_total_rate();" disabled /></td>
                    <td>&nbsp;</td>
                </tr>
                <tr bgcolor="#FF66FF">
                    <td colspan="3">Total Rate</td>
                    <td><input style="width:50px;" type="text" class="text_boxes_numeric" name="txt_tot_rate" id="txt_tot_rate" disabled /></td>
                    <td>&nbsp;</td>
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

if($action=="yarn_count_popup")
{
    echo load_html_head_contents("Yarn Count Info","../../../", 1, 1, '','1','');
    extract($_REQUEST); 
	if($db_type==0) $costingDate=change_date_format($costingDate,"yyyy-mm-dd");
	else if($db_type==2) $costingDate=change_date_format($costingDate, "", "",1);
    ?>
    <script>
        function js_set_value(data)
        {
            //alert(data)
            document.getElementById('popupData').value=data;
            parent.emailwindow.hide();
        }
    </script>
    </head>
    <body>
        <fieldset style="width:530px;margin-left:10px">
            <?
            $lib_sup=return_library_array("select supplier_name,id from lib_supplier", "id", "supplier_name");
            $lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
            $sql="select max(id) as id, supplier_id, yarn_count, composition, percent, yarn_type from lib_yarn_rate where status_active=1 and is_deleted=0 and effective_date<='$costingDate' group by supplier_id, yarn_count, composition, percent, yarn_type order by id";
            $data_array=sql_select($sql);// 
            ?>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="530" >
                <thead>
                    <th width="30">SL</th>
                    <th width="120">Supplier Name</th>
                    <th width="80">Yarn Count</th>
                    <th width="150">Composition</th>
                    <th width="50">Percent</th>
                    <th>Type</th>
                </thead>
            </table>
            <div style="width:530px; max-height:250px;overflow-y:scroll;" >  
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="510" class="rpt_table" id="list_view">
                    <tbody>
                        <? 
                        $i=1;
                        foreach($data_array as $row)
                        {  
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<?=$row[csf('id')].'_'.$lib_yarn_count[$row[csf('yarn_count')]].'_'.$yarn_type[$row[csf('yarn_type')]].'_'.$composition[$row[csf('composition')]]; ?>")' style="cursor:pointer" >
                                <td width="30"><? echo $i; ?></td>
                                <td width="120" style="word-break:break-all"><? echo $lib_sup[$row[csf('supplier_id')]]; ?></td>
                                <td width="80" style="word-break:break-all"><? echo $lib_yarn_count[$row[csf('yarn_count')]]; ?></td>
                                <td width="150" style="word-break:break-all"><? echo $composition[$row[csf('composition')]]; ?></td>
                                <td width="50" style="word-break:break-all" align="right"><? echo $row[csf('percent')]; ?></td>
                                <td style="word-break:break-all"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></td>
                            </tr>
                            <? 
                            $i++; 
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <input type="hidden" name="popupData" id="popupData" value="" style="width:50px">
        </fieldset>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>setFilterGrid('list_view',-1);</script>
    </html>
    <?
    exit();
}

if($action=="knit_popup")
{
    echo load_html_head_contents("Kniting details Info","../../../", 1, 1, '','1','');
    extract($_REQUEST); 
    ?>
    <script>
        function js_set_value(data)
        {
            //alert(data)
            document.getElementById('popupData').value=data;
            parent.emailwindow.hide();
        }
    </script>
    </head>
    <body>
        <fieldset style="width:540px;margin-left:10px">
            <?
            $buyer_arr=return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');
            $sql="select id, body_part, const_comp, gsm, gauge, yarn_description, uom_id, status_active, buyer_id from lib_subcon_charge where is_deleted=0 and rate_type_id=2 and status_active=1 order by id desc";
            $data_array=sql_select($sql);
            ?>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="530" >
                <thead>
                    <th width="30">SL</th>
                    <th width="80">Buyer Name</th>
                    <th width="70">Body Part</th>
                    <th width="100">Construction & Composition</th>
                    <th width="40">GSM</th>
                    <th width="40">Gauge</th>
                    <th width="100">Yarn Description</th>
                    <th>UOM</th>
                </thead>
                </table>
                <div style="width:530px; max-height:250px;overflow-y:scroll;" >  
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="510" class="rpt_table" id="list_view">
                        <tbody>
                            <? 
                            $i=1;
                            foreach($data_array as $row)
                            {  
                                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                ?>
                                <tr bgcolor="<?=$bgcolor; ?>" onClick='js_set_value("<?=$row[csf('id')].'_'.$body_part[$row[csf('body_part')]].'_'.$row[csf('const_comp')].'_'.$row[csf('yarn_description')]; ?>")' style="cursor:pointer" >
                                    <td width="30"><?=$i; ?></td>
                                    <td width="80" style="word-break:break-all"><?=$buyer_arr[$row[csf('buyer_id')]]; ?></td>
                                    <td width="70" style="word-break:break-all"><?=$body_part[$row[csf('body_part')]]; ?></td>
                                    <td width="100" style="word-break:break-all"><?=$row[csf('const_comp')]; ?></td>
                                    <td width="40" style="word-break:break-all"><?=$row[csf('gsm')]; ?></td>
                                    <td width="40" style="word-break:break-all"><?=$row[csf('gauge')]; ?></td>
                                    <td width="100" style="word-break:break-all"><?=$row[csf('yarn_description')]; ?></td>
                                    <td style="word-break:break-all" align="center"><?=$unit_of_measurement[$row[csf('uom_id')]]; ?></td>
                                </tr>
                                <? 
                                $i++; 
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            <input type="hidden" name="popupData" id="popupData" value="" style="width:50px">
        </fieldset>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>setFilterGrid('list_view',-1);</script>
    </html>
    <?
    exit();
}

if($action=="dye_popup")
{
    echo load_html_head_contents("Dyeing Finishing Details Info","../../../", 1, 1, '','1','');
    extract($_REQUEST); 
    ?>
    <script>
        function js_set_value(data)
        {
            //alert(data)
            document.getElementById('popupData').value=data;
            parent.emailwindow.hide();
        }
    </script>
    </head>
    <body>
        <fieldset style="width:540px;margin-left:10px">
            <?
            $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
            $company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
            $color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"); 
            $sql="select id, comapny_id, const_comp, process_type_id, process_id, color_id, width_dia_id, uom_id, buyer_id, status_active, color_range_id from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id in (3,4,7,6)";
            $data_array=sql_select($sql);
            ?>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="530" >
                <thead>
                    <th width="30">SL</th>
                    <th width="40">Com.</th>
                    <th width="100">Const. Compo.</th>
                    <th width="70">Color Range</th>
                    <th width="70">Process Type</th>
                    <th width="70">Process Name</th>
                    <th width="50">Color</th>
                    <th width="40">Width / Dia type</th>
                    <th>UOM</th>
                    
                </thead>
            </table>
            <div style="width:530px; max-height:250px;overflow-y:scroll;" >  
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="510" class="rpt_table" id="list_view">
                    <tbody>
                        <? 
                        $i=1;
                        foreach($data_array as $row)
                        {  
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            ?>
                            <tr bgcolor="<?=$bgcolor; ?>" onClick='js_set_value("<?=$row[csf('id')].'_'.$color_library_arr[$row[csf('color_id')]].'_'.$process_type[$row[csf('process_type_id')]].'_'.$color_range[$row[csf('color_range_id')]].'_'.$conversion_cost_head_array[$row[csf('process_id')]]; ?>")' style="cursor:pointer" >
                                <td width="30"><?=$i; ?></td>
                                <td width="40" style="word-break:break-all"><?=$company_arr[$row[csf('comapny_id')]]; ?></td> 
                                <td width="100" style="word-break:break-all"><?=$row[csf('const_comp')]; ?></td>
                                <td width="70" style="word-break:break-all"><?=$color_range[$row[csf('color_range_id')]]; ?></td>
                                <td width="70" style="word-break:break-all"><?=$process_type[$row[csf('process_type_id')]]; ?></td>
                                <td width="70" style="word-break:break-all"><?=$conversion_cost_head_array[$row[csf('process_id')]]; ?></td>
                                <td width="50" style="word-break:break-all"><?=$color_library_arr[$row[csf('color_id')]]; ?></td>
                                <td width="40" style="word-break:break-all"><?=$fabric_typee[$row[csf('width_dia_id')]]; ?></td>
                                <td style="word-break:break-all" align="center"><?=$unit_of_measurement[$row[csf('uom_id')]]; ?></td>
                                
                            </tr>
                            <? 
                            $i++; 
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <input type="hidden" name="popupData" id="popupData" value="" style="width:50px">
        </fieldset>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>setFilterGrid('list_view',-1);</script>
    </html>
    <?
    exit();
}

if($action=="aop_popup")
{
    echo load_html_head_contents("Dyeing Finishing Details Info","../../../", 1, 1, '','1','');
    extract($_REQUEST); 
    ?>
    <script>
        function js_set_value(data)
        {
            //alert(data)
            document.getElementById('popupData').value=data;
            parent.emailwindow.hide();
        }
    </script>
    </head>
    <body>
        <fieldset style="width:540px;margin-left:10px">
            <?
            $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
            $company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
            $color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"); 
            $sql="select id, comapny_id, const_comp, process_type_id, process_id, color_id, width_dia_id, in_house_rate, uom_id, rate_type_id ,customer_rate, buyer_id, status_active,color_range_id from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id in (3,4,7,6) and process_id=35";
            $data_array=sql_select($sql);
            ?>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="530" >
                <thead>
                    <th width="30">SL</th>
                    <th width="40">Com.</th>
                    <th width="100">Const. Compo.</th>
                    <th width="70">Color Range</th>
                    <th width="70">Process Type</th>
                    <th width="70">Process Name</th>
                    <th width="50">Color</th>
                    <th width="40">Width / Dia type</th>
                    <th>UOM</th>
                    
                </thead>
            </table>
            <div style="width:530px; max-height:250px;overflow-y:scroll;" >  
            	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="510" class="rpt_table" id="list_view">
                    <tbody>
                        <? 
                        $i=1;
                        foreach($data_array as $row)
                        {  
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            ?>
                            <tr bgcolor="<?=$bgcolor; ?>" onClick='js_set_value("<?=$row[csf('id')].'_'.$color_library_arr[$row[csf('color_id')]].'_'.$process_type[$row[csf('process_type_id')]].'_'.$color_range[$row[csf('color_range_id')]]; ?>");' style="cursor:pointer" >
                                <td width="30"><?=$i; ?></td>
                                <td width="40" style="word-break:break-all"><?=$company_arr[$row[csf('comapny_id')]]; ?></td>
                                <td width="100" style="word-break:break-all"><?=$row[csf('const_comp')]; ?></td>
                                <td width="70" style="word-break:break-all"><?=$color_range[$row[csf('color_range_id')]]; ?></td>
                                <td width="70" style="word-break:break-all"><?=$process_type[$row[csf('process_type_id')]]; ?></td>
                                <td width="70" style="word-break:break-all"><?=$conversion_cost_head_array[$row[csf('process_id')]]; ?></td>
                                <td width="50" style="word-break:break-all"><?=$color_library_arr[$row[csf('color_id')]]; ?></td>
                                <td width="40" style="word-break:break-all"><?=$fabric_typee[$row[csf('width_dia_id')]]; ?></td>
                                <td style="word-break:break-all" align="center"><?=$unit_of_measurement[$row[csf('uom_id')]]; ?></td>
                                
                            </tr>
                            <? 
                            $i++; 
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <input type="hidden" name="popupData" id="popupData" value="" style="width:50px">
        </fieldset>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>setFilterGrid('list_view',-1);</script>
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
				http.open("POST","quick_costing_v2_controller.php",true);
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
					show_list_view('','stage_data_list','stage_data_div','quick_costing_v2_controller','');
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
    <script>show_list_view('','stage_data_list','stage_data_div','quick_costing_v2_controller','');</script>          
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
	echo  create_list_view ( "list_view", "Stage Name", "200","200","150",1, "select tuid, stage_name from lib_stage_name where status_active=1 and is_deleted=0 order by tuid desc", "get_php_form_data", "tuid","'load_php_data_to_form_satge'", 1, "0", $arr, "stage_name","quick_costing_v2_controller", 'setFilterGrid("list_view",-1);','0' );
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
			disconnect($con);
			die;
		}
		
		//echo "10**";
		$sqlMargin="select id from qc_margin_mst where status_active=1 and is_deleted=0 and qc_no=$hid_qc_no";
		$sqlMarginRes=sql_select($sqlMargin);
		$isMargin=0;
		foreach($sqlMarginRes as $mrow){
			$isMargin=$mrow[csf('id')];
		}
		if($isMargin>0){
			echo "marginEntry**".str_replace("'","",$txt_costSheet_id);
			disconnect($con);
			die;
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
		$msg="Duplicate Styel Ref, Buyer, Season.";
		if($type==1)
		{
			if (is_duplicate_field( "cost_sheet_no", "qc_mst", "style_ref='$dup_styleRef' and season_id='$dup_season_id' and buyer_id='$dup_buyer_id' and status_active=1 and is_deleted=0 and entry_form=444" ) == 1)
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
		$qcno=$_SESSION['logic_erp']['user_id'].'000'.$mst_id;
		$txt_option_remarks=str_replace($str_rep,' ',str_replace("'","",$txt_option_remarks));
		$txt_costing_remarks=str_replace($str_rep,' ',str_replace("'","",$txt_costing_remarks));
		$txt_meeting_remarks=str_replace($str_rep,' ',str_replace("'","",$txt_meeting_remarks));
		//echo $revise_no."=select max(revise_no) as revise_no from qc_mst where cost_sheet_no='$txt_costSheetNo'"; die;
		$field_array_mst=" id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, style_ref, buyer_id, uom, cons_basis, season_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, pre_cost_sheet_id, revise_no, option_id, buyer_remarks, option_remarks, qc_no, inserted_by, insert_date, status_active, is_deleted, entry_form, company_id, location_id, agent_id, costing_per";
		
		$data_array_mst="(".$mst_id.",".$mst_id.",'".$autoCostSheetNo."',".$cbo_temp_id.",".$txt_temp_id.",'".strtoupper(str_replace("'","",$txt_styleRef))."',".$cbo_buyer_id.",".$cbouom.",".$cbo_cons_basis_id.",".$cbo_season_id.",'".strtoupper(str_replace("'","",$txt_styleDesc))."',".$cbo_subDept_id.",".$txt_delivery_date.",".$txt_exchangeRate.",".$txt_offerQty.",".$txt_quotedPrice.",".$txt_tgtPrice.",".$cbo_stage_id.",".$txt_costingDate.",'".$update_id."','".$revise_no."','".$option_id."','".strtoupper($txt_costing_remarks)."','".strtoupper($txt_option_remarks)."','".$qcno."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,444,".$cbo_company_id.",".$cbo_location_id.",".$cbo_agent.",".$cbo_costingper_id.")";
		
		//echo $data_array; die;
		//$template_id=return_next_id("id", "qc_template", 1);
		//$template_id = return_id( $txt_temp_id, $template_library, "qc_template", "id,item_id");
		$dtls_id=return_next_id("id", "qc_fabric_dtls", 1);
		$temp_id=explode(",",str_replace("'","",$txt_temp_id));
		
		$field_array_fab="id, mst_id, item_id, uniq_id, body_part, des, value, alw, inserted_by, insert_date";
   		//$fstr_id=return_next_id("id", "qc_fabric_string_data", 1);
		$cons_rate_id=return_next_id("id", "qc_cons_rate_dtls", 1);
		$field_array_consrate="id, mst_id, item_id, type, particular_type_id, formula, consumption, ex_percent, tot_cons, is_calculation, rate, rate_data, value, description, uom, inserted_by, insert_date";
		
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
					$data_array_fab .="(".$dtls_id.",".$qcno.",'".$item_id."','".$uniq_id."','".$body_part_id."','".strtoupper(str_replace("'","",$$txtItemDes))."',".$$txtVal.",".$$txtAw.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					
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
					$totCons=$ex_itemConsRate[6];
					
					$tot_val=$cons*$rate;
					
					if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
					$data_array_cons_rate .="(".$cons_rate_id.",".$qcno.",'".$item_id."',1,'".$head_id."',0,'".$cons."','".$exPer."','".$totCons."','".$is_rate_cal."','".$rate."','".$rateData."','".$tot_val."','',0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					
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
				$totSpCons=$ex_itemSpConsRate[4];
				
				$tot_spVal=$spConsumtion*$spRate;
				
				if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
				$data_array_cons_rate .="(".$cons_rate_id.",".$qcno.",'".$item_id."',2,'".$speciaOperation_id."',0,'".$spConsumtion."','".$spExper."','".$totSpCons."','','".$spRate."','','".$tot_spVal."','',0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
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
				$totAccCons=$ex_itemAcConsRate[4];
				$acdesc=$ex_itemAcConsRate[5];
				$acuom=$ex_itemAcConsRate[6];
				
				$tot_acVal=$acConsumtion*$acRate;
				
				if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
				$data_array_cons_rate .="(".$cons_rate_id.",".$qcno.",'".$item_id."',3,'".$accessories_id."',0,'".$acConsumtion."','".$acExPer."','".$totAccCons."',0,'".$acRate."','','".$tot_acVal."','".$acdesc."','".$acuom."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$cons_rate_id=$cons_rate_id+1;
				$add_comma_cons_rate++;
			}
		}
		//echo "10**";
		//echo $data_array_fab; 
		//die;
		$tc_sum_id=return_next_id("id", "qc_tot_cost_summary", 1);
		$field_array_tot_cost="id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, tot_rmg_ratio, commercial_per, commercial_cost, inserted_by, insert_date";
		
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
		$totCommlCst_td=trim($ex_tot_cost_data[18]);
		$txt_commlPer=trim($ex_tot_cost_data[19]);
		
		$data_array_tot_cost="(".$tc_sum_id.",".$qcno.",'".$cbo_buyer_agent."','".$cbo_agent_location."','".$txt_noOfPack."',0,'".$cmPop."','".$txt_lumSum_cost."','".$txt_commPer."','".$totFab_td."','".$totSpc_td."','".$totAcc_td."','".$totCm_td."','".$totFriCst_td."','".$totLbTstCst_td."','".$totMissCst_td."','".$totOtherCst_td."','".$totCommCst_td."','".$totCost_td."','".$totalFob_td."','".$totRmgQty_td."','".$txt_commlPer."','".$totCommlCst_td."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//echo $data_array_tot_cost; die;
		$field_array_item_tot=" id, mst_id, item_id, fabric_cost, sp_operation_cost, accessories_cost, cpm, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs, rmg_ratio, commercial_cost, inserted_by, insert_date";
		$item_cost_id=return_next_id("id", "qc_item_cost_summary", 1);
		$ex_item_wise_tot_data=array_filter(explode('__',str_replace("'","",$item_wise_tot_data)));
		$add_comma_item_tot=0; $data_array_item_tot="";
		foreach($ex_item_wise_tot_data as $item_wise_tot)
		{
			$fab_td=$spOpe_td=$acc_td=$txtCpm=$txtSmv=$txtEff=$txtCmCost=$txtFriCost=$txtLtstCost=$txtMissCost=$txtOtherCost=$txtCommCost=$fobT_td=$txtCommlCost=$tot_item_id=0;
			
			$ex_item_tot=explode('_',$item_wise_tot);
			$fab_td=trim($ex_item_tot[0]);
			$spOpe_td=trim($ex_item_tot[1]);
			$acc_td=trim($ex_item_tot[2]);
			$txtCpm=trim($ex_item_tot[3]);
			$txtSmv=trim($ex_item_tot[4]);
			$txtEff=trim($ex_item_tot[5]);
			$txtCmCost=trim($ex_item_tot[6]);
			$txtFriCost=trim($ex_item_tot[7]);
			$txtLtstCost=trim($ex_item_tot[8]);
			$txtMissCost=trim($ex_item_tot[9]);
			$txtOtherCost=trim($ex_item_tot[10]);
			$txtCommCost=trim($ex_item_tot[11]);
			$fobT_td=trim($ex_item_tot[12]);
			$txtRmgQty=trim($ex_item_tot[13]);
			$txtCommlCost=trim($ex_item_tot[14]);
			$tot_item_id=trim($ex_item_tot[15]);
			
			if ($add_comma_item_tot!=0) $data_array_item_tot .=",";
			$data_array_item_tot .="(".$item_cost_id.",".$qcno.",'".$tot_item_id."','".$fab_td."','".$spOpe_td."','".$acc_td."','".$txtCpm."','".$txtSmv."','".$txtEff."','".$txtCmCost."','".$txtFriCost."','".$txtLtstCost."','".$txtMissCost."','".$txtOtherCost."','".$txtCommCost."','".$fobT_td."','".$txtRmgQty."','".$txtCommlCost."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
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
		//echo "10**insert into qc_fabric_dtls (".$field_array_fab.") values ".$data_array_fab;die;
		/*$meetMst_id=return_next_id("id", "qc_meeting_mst", 1);
		$meetPer_id=return_next_id("id", "qc_meeting_mst", 1);
		$meetDtls_id=return_next_id("id", "qc_meeting_mst", 1);*/
		
		$flag=1;
		
		$rID=sql_insert("qc_mst",$field_array_mst,$data_array_mst,0);	
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID_fab=sql_insert("qc_fabric_dtls",$field_array_fab,$data_array_fab,0);	
		if($rID_fab==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID_consRate=sql_insert("qc_cons_rate_dtls",$field_array_consrate,$data_array_cons_rate,0);	
		if($rID_consRate==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID_tot_cost=sql_insert("qc_tot_cost_summary",$field_array_tot_cost,$data_array_tot_cost,0);	
		if($rID_tot_cost==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID_item_tot=sql_insert("qc_item_cost_summary",$field_array_item_tot,$data_array_item_tot,0);	
		if($rID_item_tot==1 && $flag==1) $flag=1; else $flag=0;
		//echo '10**'.$flag.'='.$rID_fab.'='.$rID_consRate.'='.$rID_tot_cost.'='.$rID_item_tot.'='.$rID; die;
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
		
		if (is_duplicate_field( "cost_sheet_no", "qc_mst", "style_ref='$dup_styleRef' and season_id='$dup_season_id' and buyer_id='$dup_buyer_id' and cost_sheet_no!=$txt_costSheetNo and status_active=1 and is_deleted=0 and entry_form=444") == 1)
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
		$txt_option_remarks=str_replace($str_rep,' ',str_replace("'","",$txt_option_remarks));
		$txt_costing_remarks=str_replace($str_rep,' ',str_replace("'","",$txt_costing_remarks));
		$txt_meeting_remarks=str_replace($str_rep,' ',str_replace("'","",$txt_meeting_remarks));
		$qcno=str_replace("'","",$hid_qc_no);
		$field_array_mst="temp_id*lib_item_id*style_ref*buyer_id*uom*cons_basis*season_id*style_des*department_id*delivery_date*exchange_rate*offer_qty*quoted_price*tgt_price*stage_id*costing_date*buyer_remarks*option_remarks*updated_by*update_date*company_id*location_id*agent_id*costing_per";
		
		//$txt_temp_id=explode(",",str_replace("'","",$txt_temp_id));
		//asort($txt_temp_id);
		//$txt_temp_id="'".implode(",",$txt_temp_id)."'";
		
		$data_array_mst="".$cbo_temp_id."*".$txt_temp_id."*'".strtoupper(str_replace("'","",$txt_styleRef))."'*".$cbo_buyer_id."*".$cbouom."*".$cbo_cons_basis_id."*".$cbo_season_id."*'".strtoupper(str_replace("'","",$txt_styleDesc))."'*".$cbo_subDept_id."*".$txt_delivery_date."*".$txt_exchangeRate."*".$txt_offerQty."*".$txt_quotedPrice."*".$txt_tgtPrice."*".$cbo_stage_id."*".$txt_costingDate."*'".strtoupper($txt_costing_remarks)."'*'".strtoupper($txt_option_remarks)."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_company_id."*".$cbo_location_id."*".$cbo_agent."*".$cbo_costingper_id."";
		
		//$template_id = return_id( $txt_temp_id, $template_library, "qc_template", "id,item_id");
		
		$temp_id=explode(",",str_replace("'","",$txt_temp_id));
		
		$field_array_fab="item_id*uniq_id*body_part*des*value*alw*updated_by*update_date";
		
		$cons_rate_id=return_next_id("id", "qc_cons_rate_dtls", 1);
		$field_array_consrate="id, mst_id, item_id, type, particular_type_id, formula, consumption, ex_percent, tot_cons, is_calculation, rate, rate_data, value, description, uom, inserted_by, insert_date";
		
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
			//echo "10**"; 
			$ex_itemConsRateData=array_filter(explode('##',str_replace("'","",$$itemConsRateData)));
			//echo $ex_itemConsRateData;
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
					$totCons=$ex_itemConsRate[6];
					
					$tot_val=$cons*$rate;
					
					if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
					$data_array_cons_rate .="(".$cons_rate_id.",".$qcno.",'".$item_id."',1,'".$head_id."',0,'".$cons."','".$exPer."','".$totCons."','".$is_rate_cal."','".$rate."','".$rateData."','".$tot_val."','',0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					
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
				$totSpCons=$ex_itemSpConsRate[4];
				
				$tot_spVal=$spConsumtion*$spRate;
				
				if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
				$data_array_cons_rate .="(".$cons_rate_id.",".$qcno.",'".$item_id."',2,'".$speciaOperation_id."',0,'".$spConsumtion."','".$spExper."','".$totSpCons."','','".$spRate."','','".$tot_spVal."','',0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
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
				$totAccCons=$ex_itemAcConsRate[4];
				$acdesc=$ex_itemAcConsRate[5];
				$acuom=$ex_itemAcConsRate[6];
				
				$tot_acVal=$acConsumtion*$acRate;
				
				if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
				$data_array_cons_rate .="(".$cons_rate_id.",".$qcno.",'".$item_id."',3,'".$accessories_id."',0,'".$acConsumtion."','".$acExPer."','".$totAccCons."',0,'".$acRate."','','".$tot_acVal."','".$acdesc."','".$acuom."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
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
		$field_array_tot_cost="id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, tot_rmg_ratio, commercial_per, commercial_cost, inserted_by, insert_date";
		
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
		$totCommlCst_td=trim($ex_tot_cost_data[18]);
		$txt_commlPer=trim($ex_tot_cost_data[19]);
		
		$data_array_tot_cost="(".$tc_sum_id.",".$qcno.",'".$cbo_buyer_agent."','".$cbo_agent_location."','".$txt_noOfPack."',0,'".$cmPop."','".$txt_lumSum_cost."','".$txt_commPer."','".$totFab_td."','".$totSpc_td."','".$totAcc_td."','".$totCm_td."','".$totFriCst_td."','".$totLbTstCst_td."','".$totMissCst_td."','".$totOtherCst_td."','".$totCommCst_td."','".$totCost_td."','".$totalFob_td."','".$totRmgQty_td."','".$txt_commlPer."','".$totCommlCst_td."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//echo $data_array_tot_cost; die;
		$field_array_item_tot=" id, mst_id, item_id, fabric_cost, sp_operation_cost, accessories_cost, cpm, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs, rmg_ratio, commercial_cost, inserted_by, insert_date";
		
		$item_cost_id=return_next_id("id", "qc_item_cost_summary", 1);
		$ex_item_wise_tot_data=array_filter(explode('__',str_replace("'","",$item_wise_tot_data)));
		$add_comma_item_tot=0; $data_array_item_tot="";
		foreach($ex_item_wise_tot_data as $item_wise_tot)
		{
			$fab_td=$spOpe_td=$acc_td=$txtCpm=$txtSmv=$txtEff=$txtCmCost=$txtFriCost=$txtLtstCost=$txtMissCost=$txtOtherCost=$txtCommCost=$fobT_td=$txtCommlCost=$tot_item_id=0;
			
			$ex_item_tot=explode('_',$item_wise_tot);
			$fab_td=trim($ex_item_tot[0]);
			$spOpe_td=trim($ex_item_tot[1]);
			$acc_td=trim($ex_item_tot[2]);
			$txtCpm=trim($ex_item_tot[3]);
			$txtSmv=trim($ex_item_tot[4]);
			$txtEff=trim($ex_item_tot[5]);
			$txtCmCost=trim($ex_item_tot[6]);
			$txtFriCost=trim($ex_item_tot[7]);
			$txtLtstCost=trim($ex_item_tot[8]);
			$txtMissCost=trim($ex_item_tot[9]);
			$txtOtherCost=trim($ex_item_tot[10]);
			$txtCommCost=trim($ex_item_tot[11]);
			$fobT_td=trim($ex_item_tot[12]);
			$txtRmgQty=trim($ex_item_tot[13]);
			$txtCommlCost=trim($ex_item_tot[14]);
			$tot_item_id=trim($ex_item_tot[15]);
			
			if ($add_comma_item_tot!=0) $data_array_item_tot .=",";
			$data_array_item_tot .="(".$item_cost_id.",".$qcno.",'".$tot_item_id."','".$fab_td."','".$spOpe_td."','".$acc_td."','".$txtCpm."','".$txtSmv."','".$txtEff."','".$txtCmCost."','".$txtFriCost."','".$txtLtstCost."','".$txtMissCost."','".$txtOtherCost."','".$txtCommCost."','".$fobT_td."','".$txtRmgQty."','".$txtCommlCost."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$item_cost_id=$item_cost_id+1;
			$add_comma_item_tot++;
		}
		
		$meeting_update_id=return_field_value("meeting_no","qc_mst","qc_no=$qcno and is_deleted=0 and status_active=1 and entry_form=444","meeting_no");
		$max_meeting=return_field_value("max(meeting_no) as max_meeting_no","qc_meeting_mst"," mst_id =$qcno and is_deleted=0 and status_active=1","max_meeting_no");
		$meeting_qc_no=return_field_value("qc_no","qc_mst","meeting_no='$meeting_update_id' and is_deleted=0 and status_active=1 and entry_form=444","qc_no");
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
		
		//echo "10**".$txt_update_id;//insert into qc_meeting_mst (".$field_arr_buyer_meeting.") values ".$data_arr_buyer_meeting;die;
		/*$meetMst_id=return_next_id("id", "qc_meeting_mst", 1);
		$meetPer_id=return_next_id("id", "qc_meeting_mst", 1);
		$meetDtls_id=return_next_id("id", "qc_meeting_mst", 1);*/
		
		
		$rID=sql_update("qc_mst",$field_array_mst,$data_array_mst,"id","".$txt_update_id."",1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		//echo bulk_update_sql_statement("qc_fabric_dtls", "id",$field_array_fab,$data_array_fab,$id_arr ); die;
		$rID_fab=execute_query(bulk_update_sql_statement("qc_fabric_dtls", "id",$field_array_fab,$data_array_fab,$id_arr ));
		//$rID_fab=sql_insert("qc_fabric_dtls",$field_array_fab,$data_array_fab,0);	
		if($rID_fab==1 && $flag==1) $flag=1; else $flag=0;
		
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
		
		//echo "10**".$flag."=".$rID."=".$rID_fab."=".$rID_consRate."=".$rID_tot_cost."=".$rID_item_tot."=".$rID_buyer_meeting_up."=".$rID_upmst; die;
		
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
		$user_wise_msg="You have no right for delete. If you need delete, please contract with MIS Department.";
		if($insert_user_id!=$user_id)
		{
			echo "11**".$user_wise_msg; 
			die;
		}
		//if (is_duplicate_field( "cost_sheet_no", "qc_mst", "style_ref='$dup_styleRef' and season_id='$dup_season_id' and buyer_id='$dup_buyer_id' and cost_sheet_no!=$txt_costSheetNo and status_active=1 and is_deleted=0") == 1)
		$sql_cost_sheet=sql_select("select id from qc_mst where cost_sheet_no=$txt_costSheetNo and status_active=1 and is_deleted=0");
		
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//echo "11**"."Delete Restricted";
		
		$rID=sql_update("qc_mst",$field_array,$data_array,"id","".$txt_update_id."",1);
		if($rID) $flag=1; else $flag=0;
		
		$rID_fab=sql_update("qc_fabric_dtls",$field_array,$data_array,"mst_id","".$hid_qc_no."",1);
		if($rID_fab) $flag=1; else $flag=0;
		
		$rID_consRate=sql_update("qc_cons_rate_dtls",$field_array,$data_array,"mst_id","".$hid_qc_no."",1);
		if($rID_consRate) $flag=1; else $flag=0;
		
		$rID_tot_cost=sql_update("qc_tot_cost_summary",$field_array,$data_array,"mst_id","".$hid_qc_no."",1);
		if($rID_tot_cost) $flag=1; else $flag=0;
		
		$rID_item_tot=sql_update("qc_item_cost_summary",$field_array,$data_array,"mst_id","".$hid_qc_no."",1);
		if($rID_item_tot) $flag=1; else $flag=0;
		
		//$rID_temp_style=sql_update("qc_style_temp_data",$field_array,$data_array,"qc_no","".$hid_qc_no."",1);
		if(count($sql_cost_sheet)==1)
		{
			$rID_temp_style=execute_query( "update qc_style_temp_data set status_active='0', is_deleted='1', updated_by='$user_id', update_date='$pc_date_time' where cost_sheet_no=$txt_costSheetNo and inserted_by=".$user_id."",0);
			if($rID_temp_style) $flag=1; else $flag=0;
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
	$buyerId=$ex_data[0];
	$type=$ex_data[1];
	?>
    <script>
	
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
		show_list_view (document.getElementById('cbo_buyer_id').value+'**'+document.getElementById('cbo_season_id').value+'**'+document.getElementById('cbo_subDept_id').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_cost_sheet_no').value+'**'+document.getElementById('hide_dataType').value+'**'+document.getElementById('cbo_approval_type').value+'**'+document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_string_search_type').value, 'style_ref_search_list_view', 'search_div', 'quick_costing_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
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
			var page_link='quick_costing_v2_controller.php?action=confirmStyle_popup';
			var title="Confirm Style Popup";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&data='+data, title, 'width=950px,height=350px,center=1,resize=0,scrolling=0','../../')
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
                	<tr>
                    	<th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                	</tr>
                	<tr>
                        <th width="130">Company</th>
                        <th width="130">Buyer</th>
                        <th width="130">Season</th>
                        <th width="130">Department</th>
                        <th width="90">Style</th>
                        <th width="90">Cost Sheet No</th>
                        <th width="90">Approval Type</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('styleRefform_1','search_div','','','','');"></th> 			
                   </tr>		
                </thead>
                <tbody>
                	<tr class="general">
                    	<td><? echo create_drop_down( "cbo_company_id", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "--Company--", $selected,"",'' ); ?></td>
                        <td><? echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name",1, "-- All Buyer--",$buyerId,"load_drop_down( 'quick_costing_v2_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'quick_costing_v2_controller',this.value, 'load_drop_down_sub_dep', 'sub_td' );",0 ); ?></td>                 
                        <td id="season_td"><? echo create_drop_down( "cbo_season_id", 130, $blank_array,'', 1, "-- Select Season--",$selected, "" ); ?></td>
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
	</script>
    </html>
    <?
	exit(); 
}

if($action=="style_ref_search_list_view")
{
	extract($_REQUEST);
	$compShortArr=return_library_array("select id, company_short_name from lib_company","id","company_short_name");
	$buyer_arr=return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");	
	$season_arr=return_library_array("select id, season_name from lib_buyer_season","id","season_name");
	$department_arr=return_library_array("select id, sub_department_name from lib_pro_sub_deparatment","id","sub_department_name");
	$stage_arr=return_library_array("select tuid, stage_name from lib_stage_name","tuid","stage_name");
	$user_arr=return_library_array("select id, user_name from user_passwd","id","user_name");
	
	//echo $data;
	$ex_data=explode('**',$data);

	$buyer_id=str_replace("'","",$ex_data[0]);
	$season_id=str_replace("'","",$ex_data[1]);
	$department_id=str_replace("'","",$ex_data[2]);
	$style_ref=strtoupper(str_replace("'","",$ex_data[3]));
	$cost_sheet_no=str_replace("'","",$ex_data[4]);
	$type=str_replace("'","",$ex_data[5]);
	$approvaltype=str_replace("'","",$ex_data[6]);
	$company_id=str_replace("'","",$ex_data[7]);
	$string_search_type=str_replace("'","",$ex_data[8]);
	$unappreq="";
	
	if($type==1)
	{
		if($company_id!=0) $compCond="and a.company_id=$company_id"; else $compCond="";
		if($buyer_id==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond=" and a.buyer_id=$buyer_id";
		
		//if($buyer_id!=0) $buyer_id_cond="and a.buyer_id=$buyer_id"; else $buyer_id_cond="";
		if($season_id!=0) $season_id_cond="and a.season_id=$season_id"; else $season_id_cond="";
		if($department_id!=0) $department_id_cond="and a.department_id=$department_id"; else $department_id_cond="";
		$style_ref_cond=""; $cost_sheet_no_cond="";
		
		if($string_search_type==1)
		{
			if($style_ref!='') $style_ref_cond=" and a.style_ref='$style_ref'"; else $style_ref_cond="";
			if($cost_sheet_no!='') $cost_sheet_no_cond=" and a.cost_sheet_no='$cost_sheet_no'"; else $cost_sheet_no_cond="";
		}
		else if($string_search_type==2)
		{
			if($style_ref!='') $style_ref_cond=" and a.style_ref like '$style_ref%'"; else $style_ref_cond="";
			if($cost_sheet_no!='') $cost_sheet_no_cond=" and a.cost_sheet_no like '$cost_sheet_no%'"; else $cost_sheet_no_cond="";
		}
		else if($string_search_type==3)
		{
			if($style_ref!='') $style_ref_cond=" and a.style_ref like '%$style_ref'"; else $style_ref_cond="";
			if($cost_sheet_no!='') $cost_sheet_no_cond=" and a.cost_sheet_no like '%$cost_sheet_no'"; else $cost_sheet_no_cond="";
		}
		else if($string_search_type==4 || $string_search_type==0)
		{
			if($style_ref!='') $style_ref_cond=" and a.style_ref like '%$style_ref%'"; else $style_ref_cond="";
			if($cost_sheet_no!='') $cost_sheet_no_cond=" and a.cost_sheet_no like '%$cost_sheet_no%'"; else $cost_sheet_no_cond="";
		}
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
		if($company_id!=0) $compCond="and company_id=$company_id"; else $compCond="";
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
		$style_ref_cond=""; $cost_sheet_no_cond="";
		
		if($string_search_type==1)
		{
			if($style_ref!='') $style_ref_cond=" and a.style_ref='$style_ref'"; else $style_ref_cond="";
			if($cost_sheet_no!='') $cost_sheet_no_cond=" and a.cost_sheet_no='$cost_sheet_no'"; else $cost_sheet_no_cond="";
		}
		else if($string_search_type==2)
		{
			if($style_ref!='') $style_ref_cond=" and a.style_ref like '$style_ref%'"; else $style_ref_cond="";
			if($cost_sheet_no!='') $cost_sheet_no_cond=" and a.cost_sheet_no like '$cost_sheet_no%'"; else $cost_sheet_no_cond="";
		}
		else if($string_search_type==3)
		{
			if($style_ref!='') $style_ref_cond=" and a.style_ref like '%$style_ref'"; else $style_ref_cond="";
			if($cost_sheet_no!='') $cost_sheet_no_cond=" and a.cost_sheet_no like '%$cost_sheet_no'"; else $cost_sheet_no_cond="";
		}
		else if($string_search_type==4 || $string_search_type==0)
		{
			if($style_ref!='') $style_ref_cond=" and a.style_ref like '%$style_ref%'"; else $style_ref_cond="";
			if($cost_sheet_no!='') $cost_sheet_no_cond=" and a.cost_sheet_no like '%$cost_sheet_no%'"; else $cost_sheet_no_cond="";
		}
		
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
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1180" class="rpt_table">
        <thead>
            <th width="20">SL</th>
            <th width="50">Company</th>
            <th width="100">Buyer Name</th>
            <th width="70">Season</th>
            <th width="70">Depar tment</th>
            <th width="100">Style Ref.</th>
            <th width="140">Template</th>
            <th width="60">FOB ($)</th>
            <th width="60">Stage</th>
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
            	<th width="145">Un-approved Request</th>
             	<?
			}
			?>
        </thead>
    </table>
    <div style="width:1180px; overflow-y:scroll; max-height:260px;" align="center">
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1160" class="rpt_table" id="tbl_list_search" >
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
			$sql_style = "select a.id, a.cost_sheet_no, a.company_id, a.costing_date, a.buyer_id, a.season_id, a.style_ref, a.department_id, a.temp_id, a.lib_item_id, a.stage_id, a.inserted_by, a.updated_by, a.qc_no, a.revise_no, a.option_id, c.approval_cause from qc_mst a, qc_confirm_mst b, fabric_booking_approval_cause c where a.qc_no=b.cost_sheet_id and c.booking_id=a.qc_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=28 and c.approval_type=2 and c.status_active=1 and c.is_deleted=0 and a.entry_form=444
			$compCond $buyer_id_cond $style_ref_cond $cost_sheet_no_cond $season_id_cond $department_id_cond $approvaltype_cond $unappreq 
			group by a.id, a.cost_sheet_no, a.company_id, a.costing_date, a.buyer_id, a.season_id, a.style_ref, a.department_id, a.temp_id, a.lib_item_id, a.stage_id, a.inserted_by, a.updated_by, a.qc_no, a.revise_no, a.option_id, c.approval_cause order by a.id Desc";
		}
		else
		{
			$sql_style = "select a.id, a.cost_sheet_no, a.company_id, a.costing_date, a.buyer_id, a.season_id, a.style_ref, a.department_id, a.temp_id, a.lib_item_id, a.stage_id, a.inserted_by, a.updated_by, a.qc_no, a.revise_no, a.option_id from qc_mst a, qc_confirm_mst b where a.qc_no=b.cost_sheet_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=444
			$compCond $buyer_id_cond $style_ref_cond $cost_sheet_no_cond $season_id_cond $department_id_cond $approvaltype_cond
			group by a.id, a.cost_sheet_no, a.company_id, a.costing_date, a.buyer_id, a.season_id, a.style_ref, a.department_id, a.temp_id, a.lib_item_id, a.stage_id, a.inserted_by, a.updated_by, a.qc_no, a.revise_no, a.option_id order by a.id Desc";
		}
	}
	else
	{
		$sql_style = "select min(a.id) as id, a.cost_sheet_no, max(a.costing_date) as costing_date, a.company_id, a.buyer_id, a.season_id, a.style_ref, a.department_id, a.temp_id, a.lib_item_id, a.stage_id, max(a.inserted_by) as inserted_by, max(a.updated_by) as updated_by, min(a.qc_no) as qc_no from qc_mst a where a.status_active=1 and a.is_deleted=0 and a.entry_form=444 $compCond $buyer_id_cond $style_ref_cond $cost_sheet_no_cond $season_id_cond $department_id_cond $approvaltype_cond group by a.cost_sheet_no, a.company_id, a.buyer_id, a.season_id, a.style_ref, a.department_id, a.temp_id, a.lib_item_id, a.stage_id order by id Desc";
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
		
		$confirm='';
		if($cost_summ_arr[$row[csf("qc_no")]]['is_confirm']==1) $confirm='Yes'; else $confirm='No';
		?>
        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i; ?>" onClick="<? echo $jsFunction; ?>"> 
            <td width="20" align="center"><?php echo $i; ?></td>
            <td width="50" style="word-break:break-all"><?=$compShortArr[$row[csf("company_id")]]; ?></td>
            <td width="100" style="word-break:break-all"><?php echo $buyer_arr[$row[csf("buyer_id")]]; ?></td>
            <td width="70" style="word-break:break-all"><?php echo $season_arr[$row[csf("season_id")]]; ?></td>
            <td width="70" style="word-break:break-all"><?php echo $department_arr[$row[csf("department_id")]]; ?>&nbsp;</td>
            <td width="100" style="word-break:break-all"><?php echo $unapp_link; ?> <?php echo $row[csf("style_ref")]; ?><?php echo $unapp_endlink; ?></td>
            <td width="140" style="word-break:break-all"><?php echo $template_name; ?></td>
            <td width="60" align="right"><?php echo number_format($cost_summ_arr[$row[csf("qc_no")]]['fob'],2); ?>&nbsp;</td>
            <td width="60" style="word-break:break-all"><?php echo $stage_arr[$row[csf("stage_id")]]; ?>&nbsp;</td>	
            <td width="40"><? echo $confirm; ?>&nbsp;</td>
            <td width="65" style="word-break:break-all"><?php echo $row[csf("cost_sheet_no")]; ?></td>
            <td width="60" style="word-break:break-all"><?php echo change_date_format($row[csf("costing_date")]); ?></td>
            <?
			if($type==1)
			{
				?>
                <td width="20" align="center"><?php echo $row[csf("option_id")]; ?></td>
                <td width="20" align="center"><?php echo $row[csf("revise_no")]; ?></td>
                <?
			}
			?>
            <td width="70" style="word-break:break-all"><?php echo $user_arr[$row[csf("inserted_by")]]; ?></td>
            <td style="word-break:break-all"><?php echo $user_arr[$row[csf("updated_by")]]; ?></td>
            <? if($approvaltype==2) 
			{
				?>
                <td width="125" style="word-break:break-all"><?php echo $row[csf("approval_cause")]; ?></td>
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
        	<input type="hidden" name="txt_select_row_id" id="txt_select_row_id" value="<?php echo $style_temp_id; ?>"/>
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
		
		$sql_style= "select min(id) as id, cost_sheet_no, style_ref, min(qc_no) as qc_no, season_id, inserted_by from qc_mst where cost_sheet_no in ($ex_data[0]) and entry_form=444 and cost_sheet_no not in (select cost_sheet_no from qc_style_temp_data where cost_sheet_no in ($ex_data[0]) and inserted_by='$user_id' and status_active=1 and is_deleted=0 ) and revise_no=0 and option_id=0 and status_active=1 and is_deleted=0 group by cost_sheet_no, style_ref, season_id, inserted_by";
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
	
	$sql_temp_style=sql_select("select a.id, a.cost_sheet_no, a.style_ref, a.season_id, a.inserted_by, a.updated_by, a.qc_no from qc_mst a, qc_style_temp_data b where a.id=b.mst_id and b.inserted_by='$user_id' and a.entry_form=444 and b.status_active=1 and b.is_deleted=0 order by a.style_ref ASC");
	
	$i=1;
	foreach($sql_temp_style as $row)
	{  
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		if($ex_data[0]==$row[csf('cost_sheet_no')]) $bgcolor="#33CC00"; else $bgcolor;
		$user_name="";
		if($row[csf('updated_by')]==0 || $row[csf('updated_by')]=="") $user_name=$user_arr[$row[csf('inserted_by')]]; else $user_name=$user_arr[$row[csf('updated_by')]];
		
		?>
        <tr id="tr_<? echo $row[csf('qc_no')]; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="change_color_tr('<? echo $row[csf('qc_no')]; ?>','<? echo $bgcolor; ?>'); set_onclick_style_list('<? echo $row[csf('qc_no')].'__'.$row[csf('inserted_by')].'__'.$row[csf('cost_sheet_no')].'__25'; ?>')"> <!--change_color('tr_<?// echo $i; ?>','<?// echo $bgcolor;?>');-->
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
	$data_array=sql_select("select id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, style_ref, buyer_id, uom, cons_basis, season_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, revise_no, option_id, buyer_remarks, option_remarks, inserted_by, meeting_no, qc_no, company_id, location_id, agent_id, costing_per from qc_mst where status_active=1 and is_deleted=0 and entry_form=444 and cost_sheet_no='$cost_sheet_no' $rev_no_cond $option_id_cond");//$starting_row_cond and inserted_by='$cond_user'  qc_no='$cost_sheet_id'   
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
		echo "$('#cbo_costingper_id').val('".$row[csf("costing_per")]."');\n";
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
		echo "$('#cbouom').val('".$row[csf("uom")]."');\n";
		echo "$('#cbo_cons_basis_id').val('".$row[csf("cons_basis")]."');\n";
		//echo "fnc_consumption_write_disable('".$row[csf("cons_basis")]."');\n"; 
		echo "load_drop_down('requires/quick_costing_v2_controller', '".$row[csf("buyer_id")]."', 'load_drop_down_season', 'season_td');\n"; 
		echo "load_drop_down('requires/quick_costing_v2_controller', '".$row[csf("buyer_id")]."', 'load_drop_down_sub_dep', 'sub_td');\n"; 
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
		echo "$('#txt_costing_remarks').attr('pre_costing_remarks','".$buyer_remarks."');\n"; 
		echo "$('#txt_option_remarks').val('".$row[csf("option_remarks")]."');\n";
		echo "$('#txt_option_remarks').attr('pre_opt_remarks','".$row[csf("option_remarks")]."');\n";
		echo "$('#hid_qc_no').val('".$row[csf("qc_no")]."');\n";
		echo "$('#cbo_company_id').val('".$row[csf("company_id")]."');\n";
		echo "load_drop_down('requires/quick_costing_v2_controller', '".$row[csf("company_id")]."', 'load_drop_down_location', 'location_td');\n"; 
		echo "$('#cbo_location_id').val('".$row[csf("location_id")]."');\n";
		echo "load_drop_down('requires/quick_costing_v2_controller', '".$row[csf("company_id")]."', 'load_drop_down_agent', 'agent_td');\n"; 
		echo "$('#cbo_agent').val('".$row[csf("agent_id")]."');\n";
		echo "$('#txt_isUpdate').val(1);\n"; 
		echo "disable_enable_fields('cbo_temp_id*cbouom*cbo_cons_basis_id*txt_costingDate',1);\n";
		if($ex_data[4]!="from_option")
		{
			//echo "load_drop_down('requires/quick_costing_v2_controller', '".$row[csf("cost_sheet_no")].'__'.$row[csf("option_id")].'__'.$row[csf("revise_no")]."', 'load_drop_down_revise_no', 'revise_td');\n"; //.'__'.$row[csf("revise_no")]
			//echo "load_drop_down('requires/quick_costing_v2_controller', '".$row[csf("cost_sheet_no")].'__'.$row[csf("option_id")]."', 'load_drop_down_option_id', 'option_td');\n"; 
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
	$sql_fab_dtls="Select id, item_id, uniq_id, body_part, des, value, alw from qc_fabric_dtls where status_active=1 and is_deleted=0 and mst_id='$cond_data'";
	$sql_result_fab_dtls=sql_select($sql_fab_dtls); $item_body_array=array(); $itemDVAData_array=array(); 
	foreach ($sql_result_fab_dtls as $rowFbDt)
	{
		$exUid=explode("_",$rowFbDt[csf("uniq_id")]);
		$buid=$exUid[2];
		$item_body_array[$rowFbDt[csf("item_id")]][$buid]=$rowFbDt[csf("body_part")];
		$itemDVAData_array[$rowFbDt[csf("item_id")]].=$rowFbDt[csf("des")].'_'.($rowFbDt[csf("value")]*1).'_'.($rowFbDt[csf("alw")]*1).'##';
		
		echo "$('#txtItemDes_".$rowFbDt[csf("uniq_id")]."').val('".$rowFbDt[csf("des")]."');\n";
		echo "$('#txtVal_".$rowFbDt[csf("uniq_id")]."').val('".$rowFbDt[csf("value")]."');\n";
		echo "$('#txtAw_".$rowFbDt[csf("uniq_id")]."').val('".$rowFbDt[csf("alw")]."');\n";
		echo "$('#txtdvaId_".$rowFbDt[csf("uniq_id")]."').val('".$rowFbDt[csf("id")]."');\n";
	}
	unset($sql_result_fab_dtls);
	$sql_cons_rate="select id, item_id, type, particular_type_id, consumption, ex_percent, tot_cons, is_calculation, rate, rate_data, value, description, uom from qc_cons_rate_dtls where mst_id=$cond_data and status_active=1 and is_deleted=0  order by id asc";
	$sql_result_cons_rate=sql_select($sql_cons_rate);
	$cons_rate_fab_arr=array(); $cons_rate_sp_arr=array(); $cons_rate_ac_arr=array();
	
	foreach ($sql_result_cons_rate as $rowConsRate)
	{
		if($rowConsRate[csf("type")]==1)
		{
			$cons_rate_fab_arr[$rowConsRate[csf("item_id")]].=$rowConsRate[csf("particular_type_id")].'_'.$rowConsRate[csf("consumption")].'_'.$rowConsRate[csf("is_calculation")].'_'.$rowConsRate[csf("rate")].'_'.$rowConsRate[csf("rate_data")].'_'.$rowConsRate[csf("ex_percent")].'_'.$rowConsRate[csf("tot_cons")].'##';
		}
		
		if($rowConsRate[csf("type")]==2)
		{
			$cons_rate_sp_arr[$rowConsRate[csf("item_id")]].=$rowConsRate[csf("particular_type_id")].'_'.$rowConsRate[csf("consumption")].'_'.$rowConsRate[csf("ex_percent")].'_'.$rowConsRate[csf("rate")].'_'.$rowConsRate[csf("tot_cons")].'##';
		}
		
		if($rowConsRate[csf("type")]==3)
		{
			$cons_rate_ac_arr[$rowConsRate[csf("item_id")]].=$rowConsRate[csf("particular_type_id")].'_'.$rowConsRate[csf("consumption")].'_'.$rowConsRate[csf("ex_percent")].'_'.$rowConsRate[csf("rate")].'_'.$rowConsRate[csf("tot_cons")].'_'.$rowConsRate[csf("description")].'_'.$rowConsRate[csf("uom")].'##';
		}
	}
	unset($sql_result_cons_rate);
	$active_item=1;
	foreach($item_body_array as $item_id=>$body_id)
	{
		$item_body_data='';
		foreach($body_id as $seq=>$body_part_id)
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
	
	$sql_summ="select buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, tot_rmg_ratio, commercial_per, commercial_cost from qc_tot_cost_summary where mst_id='$cond_data' and status_active=1 and is_deleted=0";
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
		echo "$('#txt_commPer').val('".$rowSumm[csf("commision_per")]."');\n";
		echo "$('#totFab_td').text('".$rowSumm[csf("tot_fab_cost")]."');\n";
		echo "$('#totSpc_td').text('".$rowSumm[csf("tot_sp_operation_cost")]."');\n";
		echo "$('#totAcc_td').text('".$rowSumm[csf("tot_accessories_cost")]."');\n";
		echo "$('#totCm_td').text('".$rowSumm[csf("tot_cm_cost")]."');\n";
		echo "$('#totFriCst_td').text('".$rowSumm[csf("tot_fright_cost")]."');\n";
		echo "$('#totLbTstCst_td').text('".$rowSumm[csf("tot_lab_test_cost")]."');\n";
		echo "$('#totMissCst_td').text('".$rowSumm[csf("tot_miscellaneous_cost")]."');\n";
		echo "$('#totOtherCst_td').text('".$rowSumm[csf("tot_other_cost")]."');\n";
		echo "$('#txt_commlPer').val('".$rowSumm[csf("commercial_per")]."');\n";
		echo "$('#totCommlCst_td').text('".$rowSumm[csf("commercial_cost")]."');\n";
		echo "$('#totCommCst_td').text('".$rowSumm[csf("tot_commission_cost")]."');\n";
		echo "$('#totCost_td').text('".$rowSumm[csf("tot_cost")]."');\n";
		echo "$('#totalFob_td').text('".$rowSumm[csf("tot_fob_cost")]."');\n";
		echo "$('#totalFob_td').attr('prev_fob','".$rowSumm[csf("tot_fob_cost")]."');\n";
		
		echo "$('#totRmgQty_td').text('".$rowSumm[csf("tot_rmg_ratio")]."');\n";
		echo "$('#totFOBCost_td').text('".number_format(($rowSumm[csf("tot_cost")]/12),4)."');\n";
	}
	unset($sql_result_summ);
	$sql_item_summ="select item_id, fabric_cost, sp_operation_cost, accessories_cost, cpm, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs, rmg_ratio, commercial_cost from qc_item_cost_summary where mst_id='$cond_data' and status_active=1 and is_deleted=0";
	$sql_result_item_summ=sql_select($sql_item_summ);
	foreach($sql_result_item_summ as $rowItemSumm)
	{
		echo "$('#fab_td".$rowItemSumm[csf("item_id")]."').text('".$rowItemSumm[csf("fabric_cost")]."');\n";
		echo "$('#spOpe_td".$rowItemSumm[csf("item_id")]."').text('".$rowItemSumm[csf("sp_operation_cost")]."');\n";
		echo "$('#acc_td".$rowItemSumm[csf("item_id")]."').text('".$rowItemSumm[csf("accessories_cost")]."');\n";
		echo "$('#txt_cpm_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("cpm")]."');\n";
		echo "$('#txt_smv_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("smv")]."');\n";
		echo "$('#txt_eff_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("efficiency")]."');\n";
		echo "$('#txtCmCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("cm_cost")]."');\n";
		echo "$('#txtFriCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("frieght_cost")]."');\n";
		echo "$('#txtLtstCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("lab_test_cost")]."');\n";
		echo "$('#txtMissCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("miscellaneous_cost")]."');\n";
		echo "$('#txtOtherCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("other_cost")]."');\n";
		echo "$('#txtCommlCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("commercial_cost")]."');\n";
		echo "$('#txtCommCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("commission_cost")]."');\n";
		
		echo "$('#fobT_td".$rowItemSumm[csf("item_id")]."').text('".$rowItemSumm[csf("fob_pcs")]."');\n";
		echo "$('#txtRmgQty_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("rmg_ratio")]."');\n";
		echo "$('#fobPcsT_td".$rowItemSumm[csf("item_id")]."').text('".number_format(($rowItemSumm[csf("fob_pcs")]/12),4)."');\n";
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

if($action=="accuom_load")
{
	$accuom=return_field_value("trim_uom","lib_item_group","id=$data and item_category=4","trim_uom");
	echo $accuom;
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
		$msg="Duplicate Styel Ref, Buyer, Season.";
		
		if (is_duplicate_field( "cost_sheet_no", "qc_mst", "style_ref='$dup_styleRef' and season_id='$dup_season_id' and buyer_id='$dup_buyer_id' and status_active=1 and is_deleted=0 and entry_form=444 " ) == 1)
		{
			echo "11**".$msg; 
			die;
		}
		//all_table qc_template, qc_mst, qc_style_temp_data, qc_fabric_dtls, qc_fabric_string_data, qc_cons_rate_dtls, qc_tot_cost_summary, qc_item_cost_summary, qc_meeting_mst, qc_meeting_person, qc_meeting_dtls
		//echo $txtItemDes_1_1_1; die;
		
		$mst_id=return_next_id("id", "qc_mst", 1);
		
		$qcno=$_SESSION['logic_erp']['user_id'].'000'.$mst_id;
		$sql_mst=sql_select("select id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, style_ref, buyer_id, uom, cons_basis, season_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, buyer_remarks, company_id, costing_per from qc_mst where qc_no='$hid_qc_no'");
		//echo "select id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, style_ref, buyer_id, cons_basis, season_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date from qc_mst where id='$update_id'";
		
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
		$txt_costingDate=$pc_date;
		$txt_costing_remarks=$sql_mst[0][csf('buyer_remarks')];
		$old_id=$sql_mst[0][csf('id')];
		$cbo_company_id=$sql_mst[0][csf('company_id')];
		$cbo_costingper_id=$sql_mst[0][csf('costing_per')];
		
		$field_array_mst=" id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, style_ref, buyer_id, uom, cons_basis, season_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, buyer_remarks, pre_cost_sheet_id, revise_no, option_id, qc_no, inserted_by, insert_date, status_active, is_deleted, entry_form, company_id, costing_per";
		
		//$txt_temp_id=explode(",",str_replace("'","",$txt_temp_id));
		//asort($txt_temp_id);
		//$txt_temp_id="'".implode(",",$txt_temp_id)."'";
		$option_id=0;
		$revise_no=0;
		$autoCostSheetNo=$_SESSION['logic_erp']['user_id'].str_pad($mst_id,8,'0',STR_PAD_LEFT);
		
		$data_array_mst="(".$mst_id.",".$mst_id.",'".$autoCostSheetNo."','".$cbo_temp_id."','".$txt_temp_id."',".$txt_styleRef.",'".$cbo_buyer_id."','".$cbouom."','".$cbo_cons_basis_id."',".$cbo_season_id.",'".$txt_styleDesc."','".$cbo_subDept_id."','".$txt_delivery_date."','".$txt_exchangeRate."','".$txt_offerQty."','".$txt_quotedPrice."','".$txt_tgtPrice."','".$cbo_stage_id."','".$txt_costingDate."','".$txt_costing_remarks."','".$hid_qc_no."','".$revise_no."','".$option_id."','".$qcno."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,444,'".$cbo_company_id."','".$cbo_costingper_id."')";
		
		//echo $data_array; die;
		//$template_id=return_next_id("id", "qc_template", 1);
		//$template_id = return_id( $txt_temp_id, $template_library, "qc_template", "id,item_id");
		$dtls_id=return_next_id("id", "qc_fabric_dtls", 1);
		$temp_id=explode(",",str_replace("'","",$txt_temp_id));
		$sql_fab=sql_select("select id, mst_id, item_id, uniq_id, body_part, des, value, alw from qc_fabric_dtls where mst_id='$hid_qc_no' order by id ASC");
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
			$data_array_fab .="(".$dtls_id.",".$qcno.",'".$item_id."','".$uniq_id."','".$body_part_id."','".$txtItemDes."','".$txtVal."','".$txtAw."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$add_comma_fab++;
			$dtls_id=$dtls_id+1;
		}
		
		$cons_rate_id=return_next_id("id", "qc_cons_rate_dtls", 1);
		$sql_consrate=sql_select("select id, mst_id, item_id, type, particular_type_id, formula, consumption, ex_percent, tot_cons, unit, is_calculation, rate, rate_data, value from qc_cons_rate_dtls where mst_id='$hid_qc_no' order by id ASC");
		$field_array_consrate="id, mst_id, item_id, type, particular_type_id, formula, consumption, ex_percent, tot_cons, unit, is_calculation, rate, rate_data, value, inserted_by, insert_date";
		$add_comma_cons_rate=0; $data_array_cons_rate="";
		foreach( $sql_consrate as $rowConsrate )
		{
			$old_consrate_id=$rowConsrate[csf('id')];
			$item_id=$rowConsrate[csf('item_id')];
			$type=$rowConsrate[csf('type')];
			$head_id=$rowConsrate[csf('particular_type_id')];
			$formula=$rowConsrate[csf('formula')];
			$cons=$rowConsrate[csf('consumption')];
			$tot_cons=$rowConsrate[csf('tot_cons')];
			$ex_percent=$rowConsrate[csf('ex_percent')];
			$unit_id=$rowConsrate[csf('unit')];
			$is_rate_cal=$rowConsrate[csf('is_calculation')];
			$rate=$rowConsrate[csf('rate')];
			$rateData=$rowConsrate[csf('rate_data')];
			$tot_val=$rowConsrate[csf('value')];
			
			if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
			$data_array_cons_rate .="(".$cons_rate_id.",".$qcno.",'".$item_id."','".$type."','".$head_id."','".$formula."','".$cons."','".$ex_percent."','".$tot_cons."','".$unit_id."','".$is_rate_cal."','".$rate."','".$rateData."','".$tot_val."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$cons_rate_id=$cons_rate_id+1;
			$add_comma_cons_rate++;
		}
		
		//echo $data_array_cons_rate; 
		//die;
		$tc_sum_id=return_next_id("id", "qc_tot_cost_summary", 1);
		$sql_tot_cost=sql_select("select id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, commercial_per, commercial_cost from qc_tot_cost_summary where mst_id='$hid_qc_no'");
		
		$field_array_tot_cost="id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, commercial_per, commercial_cost, inserted_by, insert_date";
		
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
		$totCommlCst_td=$sql_tot_cost[0][csf('commercial_cost')];
		$txt_commlPer=$sql_tot_cost[0][csf('commercial_per')];
		
		$data_array_tot_cost="(".$tc_sum_id.",".$qcno.",'".$cbo_buyer_agent."','".$cbo_agent_location."','".$txt_noOfPack."',0,'".$cmPop."','".$txt_lumSum_cost."','".$txt_commPer."','".$totFab_td."','".$totSpc_td."','".$totAcc_td."','".$totCm_td."','".$totFriCst_td."','".$totLbTstCst_td."','".$totMissCst_td."','".$totOtherCst_td."','".$totCommCst_td."','".$totCost_td."','".$totalFob_td."','".$txt_commlPer."','".$totCommlCst_td."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//echo $data_array_tot_cost; die;
		$sql_item_tot=sql_select("select id, mst_id, item_id, fabric_cost, sp_operation_cost, accessories_cost, cpm, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, commercial_cost, fob_pcs from qc_item_cost_summary where mst_id='$hid_qc_no' order by id ASC");
		$field_array_item_tot=" id, mst_id, item_id, fabric_cost, sp_operation_cost, accessories_cost, cpm, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, commercial_cost, fob_pcs, inserted_by, insert_date";
		$item_cost_id=return_next_id("id", "qc_item_cost_summary", 1);
		$add_comma_item_tot=0; $data_array_item_tot="";
		foreach($sql_item_tot as $rowItem_tot)
		{
			$fab_td=$spOpe_td=$acc_td=$txtCpm=$txtSmv=$txtEff=$txtCmCost=$txtFriCost=$txtLtstCost=$txtMissCost=$txtOtherCost=$txtCommCost=$txtCommlCost=$fobT_td=$tot_item_id=0;
			$fab_td=$rowItem_tot[csf('fabric_cost')];
			$spOpe_td=$rowItem_tot[csf('sp_operation_cost')];
			$acc_td=$rowItem_tot[csf('accessories_cost')];
			$txtCpm=$rowItem_tot[csf('cpm')];
			$txtSmv=$rowItem_tot[csf('smv')];
			$txtEff=$rowItem_tot[csf('efficiency')];
			$txtCmCost=$rowItem_tot[csf('cm_cost')];
			$txtFriCost=$rowItem_tot[csf('frieght_cost')];
			$txtLtstCost=$rowItem_tot[csf('lab_test_cost')];
			$txtMissCost=$rowItem_tot[csf('miscellaneous_cost')];
			$txtOtherCost=$rowItem_tot[csf('other_cost')];
			$txtCommCost=$rowItem_tot[csf('commission_cost')];
			$fobT_td=$rowItem_tot[csf('fob_pcs')];
			$txtCommlCost=$rowItem_tot[csf('commercial_cost')];
			$tot_item_id=$rowItem_tot[csf('item_id')];
			
			if ($add_comma_item_tot!=0) $data_array_item_tot .=",";
			$data_array_item_tot .="(".$item_cost_id.",".$qcno.",'".$tot_item_id."','".$fab_td."','".$spOpe_td."','".$acc_td."','".$txtCpm."','".$txtSmv."','".$txtEff."','".$txtCmCost."','".$txtFriCost."','".$txtLtstCost."','".$txtMissCost."','".$txtOtherCost."','".$txtCommCost."','".$txtCommlCost."','".$fobT_td."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
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
	$costingper=$exdata[2];
	if($costingper==2) $costingcap="$/PCS"; else if($costingper==1) $costingcap="$/DZN"; else $costingcap="";
	//echo $costingper;
	$user_id=$_SESSION['logic_erp']['user_id'];
	$user_level=$_SESSION['logic_erp']["user_level"];
	$sql_data=sql_select("Select cost_sheet_no, buyer_id, season_id, department_id, temp_id, lib_item_id, style_ref, offer_qty, revise_no, option_id, delivery_date, uom from qc_mst where qc_no='$qc_no' and entry_form=444");
	
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
	if($qcCons_from==2) $cons_cond="tot_cons";//Tot Cons
	else $cons_cond="consumption";//Tot Cons
	
	
	$sql_summ=sql_select("select no_of_pack, tot_fob_cost from qc_tot_cost_summary where mst_id='$qc_no' and status_active=1 and is_deleted=0");
	//$sql_cons_rate="select id, item_id, type, particular_type_id, consumption, unit, is_calculation, rate, rate_data, value from qc_cons_rate_dtls where mst_id='$data[0]' and status_active=1 and is_deleted=0 order by id asc";
	$sql_cons=sql_select("select item_id, sum(CASE WHEN particular_type_id in (1,20,4,6,7,998) THEN $cons_cond ELSE 0 END) as qty_kg, sum(CASE WHEN particular_type_id=999 THEN $cons_cond ELSE 0 END) as qty_yds from qc_cons_rate_dtls where mst_id='$qc_no' and type=1 group by item_id");//type ='1' and
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
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_yds']=$cRow[csf("qty_kg")]+$cRow[csf("qty_yds")];
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
	//echo $user_level.'-'.$isteam_leader;
	if($user_level==2 || $isteam_leader!='') $admin_or_leader="";  else $admin_or_leader="none";
	
	if($approved_need==1) $admin_or_leader="none";
	//echo $admin_or_leader.'===';
	
	?>
    <script>
		var permission='<? echo $permission; ?>'; 
		var qcCons_from='<? echo $qcCons_from; ?>'; 
		var costingcap='<? echo $costingcap; ?>'; 
		function fnc_confirm_entry( operation )
		{
			freeze_window(operation);
			if(operation==3)
			{
				var fab_cons_kg=$("#txtFabConkg_bom").val();
				var fab_cons_mtr=$("#txtFabConmtr_bom").val();
				var fab_cons_yds=$("#txtFabConyds_bom").val();
				var fab_amount=$("#txtFabCst_bom").val();
				var sp_oparation_amount=$("#txtSpOpa_bom").val();
				var acc_amount=$("#txtAcc_bom").val();
				var fright_amount=$("#txtFrightCst_bom").val();
				var lab_amount=$("#txtLabCst_bom").val();
				var misce_amount=$("#txtMiscCst_bom").val();
				var other_amount=$("#txtOtherCst_bom").val();
				var comml_amount=$("#txtCommlCst_bom").val();
				var comm_amount=$("#txtCommCst_bom").val();
				var fob_amount=$("#txtFobDzn_bom").val();
				var cm_amount=$("#txtCmCst_bom").val();
				var rmg_ratio=$("#txtPack_bom").val();
							
				var temp_id=$('#txtItem_id').val();
				var split_tmep_id=temp_id.split(','); var ab=0;
				var qc_fab_kg=0; var qc_fab_mtr=0; var qc_fab_yds=0; var qc_fab_amt=0; var qc_sp_amt=0; var qc_acc_amt=0; var qc_fri_amt=0; var qc_lab_amt=0; var qc_misce_amt=0; var qc_other_amt=0; var qc_comml_amt=0; var qc_comm_amt=0; var qc_fob_amt=0; var qc_cm_amt=0; var qc_rmg_amt=0;
				for(j=1; j<=split_tmep_id.length; j++)
				{
					var itm_id=trim(split_tmep_id[ab]);
					
					qc_fab_kg+=$("#txtFabConkg_"+itm_id).val()*1;
					qc_fab_mtr+=$("#txtFabConmtr_"+itm_id).val()*1;
					qc_fab_yds+=$("#txtFabConyds_"+itm_id).val()*1;
					qc_fab_amt+=$("#txtFabCst_"+itm_id).val()*1;
					qc_sp_amt+=$("#txtSpOpa_"+itm_id).val()*1;
					qc_acc_amt+=$("#txtAcc_"+itm_id).val()*1;
					qc_fri_amt+=$("#txtFrightCst_"+itm_id).val()*1;
					qc_lab_amt+=$("#txtLabCst_"+itm_id).val()*1;
					qc_misce_amt+=$("#txtMiscCst_"+itm_id).val()*1;
					qc_other_amt+=$("#txtOtherCst_"+itm_id).val()*1;
					qc_comml_amt+=$("#txtCommlCst_"+itm_id).val()*1;
					qc_comm_amt+=$("#txtCommCst_"+itm_id).val()*1;
					qc_fob_amt+=$("#txtFobDzn_"+itm_id).val()*1;
					qc_cm_amt+=$("#txtCmCst_"+itm_id).val()*1;
					qc_rmg_amt+=$("#txtPack_"+itm_id).val()*1;
					
					ab++;
				}
				//alert(qc_fab_amt); return;
				var job_no=$("#txt_job_style").val();
				if(job_no!="")
				{
					if(qc_fab_kg<fab_cons_kg)
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
					else if(qc_fab_yds<fab_cons_yds)
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
					else if(qc_sp_amt<sp_oparation_amount)
					{
						alert("BOM Special Opera. Amount is Greater Than QC.");
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
					}
					else if(qc_comml_amt<comml_amount)
					{
						alert("BOM Commercial Cost is Greater Than QC.");
						release_freezing();
						return;
					}
					*/
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
				data_dtls+=get_submitted_data_string('txtitemid_'+itm_id+'*txtdtlsupid_'+itm_id+'*txtFabConkg_'+itm_id+'*txtFabConmtr_'+itm_id+'*txtFabConyds_'+itm_id+'*txtFabCst_'+itm_id+'*txtSpOpa_'+itm_id+'*txtAcc_'+itm_id+'*txtFrightCst_'+itm_id+'*txtLabCst_'+itm_id+'*txtMiscCst_'+itm_id+'*txtOtherCst_'+itm_id+'*txtCommlCst_'+itm_id+'*txtCommCst_'+itm_id+'*txtFobDzn_'+itm_id+'*txtCpm_'+itm_id+'*txtSmv_'+itm_id+'*txtCmCst_'+itm_id+'*txtPack_'+itm_id,"../../",2);
				k++;
			}
			var data_mst="action=save_update_delete_confirm_style&operation="+operation+get_submitted_data_string('txt_costSheet_id*txtConfirm_id*txtItem_id*txt_confirm_style*txt_order_qty*txt_confirm_fob*txt_ship_date*txt_job_id*cbo_approved_status*cbo_ready_approve',"../../",2);
			
			var data=data_mst+data_dtls;
			//alert(data); //return;
			
			http.open("POST","quick_costing_v2_controller.php",true);
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
					get_php_form_data($('#txt_costSheet_id').val()+'__'+$('#txtItem_id').val(),'populate_confirm_style_form_data','quick_costing_v2_controller');
					//show_list_view('','stage_data_list','stage_data_div','quick_costing_v2_controller','');
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
			page_link='quick_costing_v2_controller.php?action=style_tag_popup&data='+data;
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
				var str_data=return_global_ajax_value( job_no, 'budgete_cost_validate', '', 'quick_costing_v2_controller');
				
				var spdata=str_data.split("##");
				var fab_cons_kg=spdata[0]; var fab_cons_mtr=spdata[1]; var fab_cons_yds=spdata[2]; var fab_amount=spdata[3]; var sp_oparation_amount=spdata[4]; var acc_amount=spdata[5]; var fright_amount=spdata[6]; var lab_amount=spdata[7]; var misce_amount=spdata[8]; var other_amount=spdata[9]; var comm_amount=spdata[10]; var fob_amount=spdata[11]; var cm_amount=spdata[12]; var rmg_ratio=spdata[13]; var comml_amount=spdata[14];
				
				if(qcCons_from==1)
				{
					var bomTotCost=(number_format(fab_amount,2)*1)+(number_format(sp_oparation_amount,2)*1)+(number_format(acc_amount,2)*1)+(number_format(fright_amount,2)*1)+(number_format(lab_amount,2)*1)+(number_format(misce_amount,2)*1)+(number_format(other_amount,2)*1)+(number_format(comm_amount,2)*1)+(number_format(comml_amount,2)*1);
					
					var bomCost='Fab='+fab_amount+'+ SP='+sp_oparation_amount+'+ ACC='+acc_amount+'+ FR='+fright_amount+'+ Lab='+lab_amount+'+ Mis='+misce_amount+'+ Others='+other_amount+'+ Comml. Amt.='+comml_amount+'+ Comm='+comm_amount+'= Total='+bomTotCost;
					
					$("#txtFobDzn_bom").attr('title',bomCost);
					
					var bom_tot_cm=(number_format(fob_amount,2)*1)-(number_format(bomTotCost,2)*1);
				}
				else
				{
					var bom_tot_cm=cm_amount;
				}
				
				$("#txtFabConkg_bom").val(fab_cons_kg);
				$("#txtFabConmtr_bom").val(fab_cons_mtr);
				$("#txtFabConyds_bom").val(fab_cons_yds);
				$("#txtFabCst_bom").val(fab_amount);
				$("#txtSpOpa_bom").val(sp_oparation_amount);
				$("#txtAcc_bom").val(acc_amount);
				$("#txtFrightCst_bom").val(fright_amount);
				$("#txtLabCst_bom").val(lab_amount);
				$("#txtMiscCst_bom").val(misce_amount);
				$("#txtOtherCst_bom").val(other_amount);
				$("#txtCommlCst_bom").val(comml_amount);
				$("#txtCommCst_bom").val(comm_amount);
				$("#txtFobDzn_bom").val(fob_amount);
				$("#txtCmCst_bom").val(bom_tot_cm);
				$("#txtPack_bom").val(rmg_ratio);
			}
		}
		
		function fnc_total_calculate()
		{
			var temp_id=$('#txtItem_id').val();
			var split_tmep_id=temp_id.split(',');
			var ab=0;
			var qc_fab_kg=0; var qc_fab_mtr=0; var qc_fab_yds=0; var qc_fab_amt=0; var qc_sp_amt=0; var qc_acc_amt=0; var qc_fri_amt=0; var qc_lab_amt=0; var qc_misce_amt=0; var qc_other_amt=0; var qc_comml_amt=0; var qc_comm_amt=0; var qc_fob_amt=0; var qc_cpm_amt=0; var qc_smv_amt=0; var qc_cm_amt=0; var qc_rmg_amt=0;
			for(j=1; j<=split_tmep_id.length; j++)
			{
				var item_tot_amount=0; var item_tot_cm=0;
				var itm_id=trim(split_tmep_id[ab]);
				
				qc_fab_kg+=$("#txtFabConkg_"+itm_id).val()*1;
				qc_fab_mtr+=$("#txtFabConmtr_"+itm_id).val()*1;
				qc_fab_yds+=$("#txtFabConyds_"+itm_id).val()*1;
				qc_fab_amt+=$("#txtFabCst_"+itm_id).val()*1;
				qc_sp_amt+=$("#txtSpOpa_"+itm_id).val()*1;
				qc_acc_amt+=$("#txtAcc_"+itm_id).val()*1;
				qc_fri_amt+=$("#txtFrightCst_"+itm_id).val()*1;
				qc_lab_amt+=$("#txtLabCst_"+itm_id).val()*1;
				qc_misce_amt+=$("#txtMiscCst_"+itm_id).val()*1;
				qc_other_amt+=$("#txtOtherCst_"+itm_id).val()*1;
				qc_comml_amt+=$("#txtCommlCst_"+itm_id).val()*1;
				qc_comm_amt+=$("#txtCommCst_"+itm_id).val()*1;
				qc_fob_amt+=$("#txtFobDzn_"+itm_id).val()*1;
				
				qc_cpm_amt+=$("#txtCpm_"+itm_id).val()*1;
				qc_smv_amt+=$("#txtSmv_"+itm_id).val()*1;
				
				qc_cm_amt+=$("#txtCmCst_"+itm_id).val()*1;
				qc_rmg_amt+=$("#txtPack_"+itm_id).val()*1;
				
				item_tot_amount=($("#txtFabCst_"+itm_id).val()*1)+($("#txtSpOpa_"+itm_id).val()*1)+($("#txtAcc_"+itm_id).val()*1)+($("#txtFrightCst_"+itm_id).val()*1)+($("#txtLabCst_"+itm_id).val()*1)+($("#txtMiscCst_"+itm_id).val()*1)+($("#txtOtherCst_"+itm_id).val()*1)+($("#txtCommlCst_"+itm_id).val()*1)+($("#txtCommCst_"+itm_id).val()*1);
				
				var itemCost='Fab='+$("#txtFabCst_"+itm_id).val()+'+ SP='+$("#txtSpOpa_"+itm_id).val()+'+ ACC='+$("#txtAcc_"+itm_id).val()+'+ FR='+$("#txtFrightCst_"+itm_id).val()+'+ Lab='+$("#txtLabCst_"+itm_id).val()+'+ Mis='+$("#txtMiscCst_"+itm_id).val()+'+ Others='+$("#txtOtherCst_"+itm_id).val()+'+ Comml='+$("#txtCommlCst_"+itm_id).val()+'+ Comm='+$("#txtCommCst_"+itm_id).val()+'= Total='+item_tot_amount;
				
				item_tot_cm=($("#txtFobDzn_"+itm_id).val()*1)-item_tot_amount;
				
				$('#txtFobDzn_'+itm_id).attr('title',itemCost);
				
				$("#txtCmCst_"+itm_id).val( number_format(item_tot_cm,2,'.',''))
				
				ab++;
			}
			
			$("#txtFabConkg_qc").val( number_format(qc_fab_kg,2,'.','') );
			$("#txtFabConmtr_qc").val( number_format(qc_fab_mtr,2,'.','') );
			$("#txtFabConyds_qc").val( number_format(qc_fab_yds,2,'.','') );
			$("#txtFabCst_qc").val( number_format(qc_fab_amt,2,'.','') );
			$("#txtSpOpa_qc").val( number_format(qc_sp_amt,2,'.','') );
			$("#txtAcc_qc").val( number_format(qc_acc_amt,2,'.','') );
			$("#txtFrightCst_qc").val( number_format(qc_fri_amt,2,'.','') );
			$("#txtLabCst_qc").val( number_format(qc_lab_amt,2,'.','') );
			$("#txtMiscCst_qc").val( number_format(qc_misce_amt,2,'.','') );
			$("#txtOtherCst_qc").val( number_format(qc_other_amt,2,'.','') );
			$("#txtCommlCst_qc").val( number_format(qc_comml_amt,2,'.','') );
			$("#txtCommCst_qc").val( number_format(qc_comm_amt,2,'.','') );
			$("#txtFobDzn_qc").val( number_format(qc_fob_amt,2,'.','') );
			
			$("#txtCpm_qc").val( number_format(qc_cpm_amt,4,'.','') );
			$("#txtSmv_qc").val( number_format(qc_smv_amt,2,'.','') );
			
			$("#txtPack_qc").val( number_format(qc_rmg_amt,2,'.','') );
			
			var total_amount=qc_fab_amt+qc_sp_amt+qc_acc_amt+qc_fri_amt+qc_lab_amt+qc_misce_amt+qc_other_amt+qc_comml_amt+qc_comm_amt;
			var cal_cm=qc_fob_amt-total_amount;
			$("#txtCmCst_qc").val( number_format(cal_cm,2,'.','') );
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
			var page_link = 'quick_costing_v2_controller.php?data='+data+'&action=unapp_request_popup';
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
			
			var tdfabkg=$('#tdfabkg'+item_id).text()*1;
			var tdfabmtr=$('#tdfabmtr'+item_id).text()*1;
			var tdfabyds=$('#tdfabyds'+item_id).text()*1;
			var tdfabamt=$('#tdfabamt'+item_id).text()*1;
			var tdspamt=$('#tdspamt'+item_id).text()*1;
			var tdaccamt=$('#tdaccamt'+item_id).text()*1;
			var tdfriamt=$('#tdfriamt'+item_id).text()*1;
			var tdlabamt=$('#tdlabamt'+item_id).text()*1;
			var tdmisamt=$('#tdmisamt'+item_id).text()*1;
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
				if( validatetype==1 && tdfabkg < ($("#txtFabConkg_"+item_id).val()*1) )
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
				}
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
					alert("QC BOM Limit (FOB ("+costingcap+")) is Greater Than QC!!!");
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
				if( validatetype==17 && tdcommlamt < ($("#txtCommlCst_"+item_id).val()*1) )
				{
					alert("QC BOM Limit (Commercial Cost) is Greater Than QC!!!");
					$("#txtCommlCst_"+item_id).val(tdcommlamt);
					//return;
				}
				fnc_total_calculate();
				return;
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
                    <td width="120"><? echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-Select Buyer-", $sql_data[0][csf('buyer_id')], "load_drop_down( 'quick_costing_v2_controller', this.value, 'load_drop_down_season_conf', 'season_conf_td'); load_drop_down( 'quick_costing_v2_controller',this.value, 'load_drop_down_sub_depConf', 'subConf_td' );",1 ); ?></td>
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
                	<td><strong>Ready To Approved</strong></td>
                    <td><? echo create_drop_down( "cbo_ready_approve", 100, $yes_no,"", 1, "--", 0, "","","" ); ?></td>
                	<td colspan="3"><strong>Un-approve Request</strong>&nbsp;&nbsp;
                        <Input name="txt_un_appv_request" class="text_boxes" placeholder="Double Click" ID="txt_un_appv_request" style="width:110px;" onClick="openmypage_unapprove_request();" readonly disabled>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
            <table width="400" cellspacing="0" class="" border="0">
                <tr>
                    <td align="center" width="100%" class="button_container">
						<? echo load_submit_buttons( $permission, "fnc_confirm_entry", 0,0 ,"reset_form('confirmStyle_1','','','','')",0,''); ?>
                        <input type="button" value="Approved" name="approve" onClick="fnc_confirm_entry(3)" style="width:80px; display:<? echo $admin_or_leader; ?>" id="approve1" class="formbutton"><input type="button" class="formbutton" value="Close" style="width:80px" onClick="js_set_value();"/>
                    </td> 
                </tr>
            </table>
            <div id="confirm_data_div">
            <table width="925" cellspacing="0" border="1" rules="all" class="rpt_table">
        	<thead>
            	<th width="80">Item</th>
                <th width="50">Fab. Cons. Kg</th>
                <th width="50">Fab. Cons. Mtr</th>
                <th width="50">Fab. Cons. Yds</th>
                <th width="50">Fab. Amount</th>
                <th width="50">Special Opera.</th>
                <th width="50">Access.</th>
                <th width="50">Frieght Cost</th>
                <th width="50">Lab - Test</th>
                <th width="50">Misce.</th>
                <th width="50">Other Cost</th>
                <th width="50">Comml. Cost</th>
                <th width="50">Commis.</th>
                <th width="50">FOB (<?=$costingcap; ?>)</th>
                <th width="50" title="((CPM*100)/Efficiency)">CPPM</th>
                <th width="50">SMV</th>
                <th width="50">CM</th>
                <th>RMG Qty(Pcs)</th>
            </thead>
			<?
            $sql_item_summ="select item_id, fabric_cost, sp_operation_cost, accessories_cost, cpm, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commercial_cost, commission_cost, fob_pcs from qc_item_cost_summary where mst_id='$qc_no' and status_active=1 and is_deleted=0";
            $sql_result_item_summ=sql_select($sql_item_summ); $z=1;
            foreach($sql_result_item_summ as $rowItemSumm)
            {
				if ($z%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if ($z%2==0) $bgcolorN="#E9F50F"; else $bgcolorN="#D078F6";
				
				$cppm=0;
				if($rowItemSumm[csf("efficiency")]!=0 && $rowItemSumm[csf("cpm")]!=0) $cppm=(($rowItemSumm[csf("cpm")]*100)/$rowItemSumm[csf("efficiency")]);
				
				if($cppm=="nan") $cppm=0;
				$item_id=0;
				$item_id=$rowItemSumm[csf("item_id")];
				
                ?>
                <tr id="trVal_<? echo $z; ?>" bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $lib_temp_arr[$item_id]; ?></td>
                    <td align="right" id="tdfabkg<?=$item_id; ?>"><? echo number_format($item_wise_cons_arr[$item_id]['qty_kg'],4); ?></td>
                    <td align="right" id="tdfabmtr<?=$item_id; ?>"><? echo number_format($item_wise_cons_arr[$item_id]['qty_mtr'],4); ?></td>
                    <td align="right" id="tdfabyds<?=$item_id; ?>"><? echo number_format($item_wise_cons_arr[$item_id]['qty_yds'],4); ?></td>
                    <td align="right" id="tdfabamt<?=$item_id; ?>"><? echo number_format($rowItemSumm[csf("fabric_cost")],4); ?></td>
                    <td align="right" id="tdspamt<?=$item_id; ?>"><? echo number_format($rowItemSumm[csf("sp_operation_cost")],4); ?></td>
                    <td align="right" id="tdaccamt<?=$item_id; ?>"><? echo number_format($rowItemSumm[csf("accessories_cost")],4); ?></td>
                    <td align="right" id="tdfriamt<?=$item_id; ?>"><? echo number_format($rowItemSumm[csf("frieght_cost")],4); ?></td>
                    <td align="right" id="tdlabamt<?=$item_id; ?>"><? echo number_format($rowItemSumm[csf("lab_test_cost")],4); ?></td>
                    <td align="right" id="tdmisamt<?=$item_id; ?>"><? echo number_format($rowItemSumm[csf("miscellaneous_cost")],4); ?></td>
                    <td align="right" id="tdothamt<?=$item_id; ?>"><? echo number_format($rowItemSumm[csf("other_cost")],4); ?></td>
                    <td align="right" id="tdcommlamt<?=$item_id; ?>"><? echo number_format($rowItemSumm[csf("commercial_cost")],4); ?></td>
                    <td align="right" id="tdcomamt<?=$item_id; ?>"><? echo number_format($rowItemSumm[csf("commission_cost")],4); ?></td>
                    <td align="right" id="tdfobamt<?=$item_id; ?>"><? echo number_format($rowItemSumm[csf("fob_pcs")],4); ?></td>
                    
                    <td align="right" id="tdcppmamt<?=$item_id; ?>" title="((CPM*100)/Efficiency)"><? if($cppm==0) echo "0"; else echo number_format($cppm,4); ?></td>
                    <td align="right" id="tdsmvamt<?=$item_id; ?>"><? echo number_format($rowItemSumm[csf("smv")],4); ?></td>
                    
                    <td align="right" id="tdcmamt<?=$item_id; ?>"><? echo number_format($rowItemSumm[csf("cm_cost")],4); ?></td>
                    <td align="right" id="tdrmgpcs<?=$item_id; ?>">&nbsp;<? //echo number_format($rowItemSumm[csf("sp_operation_cost")],4); ?></td>
                </tr>
                <tr id="tr_<? echo $z; ?>" bgcolor="<? echo $bgcolorN; ?>">
                    <td>QC BOM Limit<input style="width:40px;" type="hidden" name="txtitemid_<?=$item_id; ?>" id="txtitemid_<?=$item_id; ?>" value="<?=$item_id; ?>" /><input style="width:40px;" type="hidden" name="txtdtlsupid_<?=$item_id; ?>" id="txtdtlsupid_<?=$item_id; ?>" value="" /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtFabConkg_<?=$item_id; ?>" id="txtFabConkg_<?=$item_id; ?>" value="<?=number_format($item_wise_cons_arr[$item_id]['qty_kg'],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_1'; ?>');" <?=$disable; ?> /></td>
                    
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtFabConmtr_<?=$item_id; ?>" id="txtFabConmtr_<?=$item_id; ?>" value="<?=number_format($item_wise_cons_arr[$item_id]['qty_mtr'],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_2'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtFabConyds_<?=$item_id; ?>" id="txtFabConyds_<?=$item_id; ?>" value="<?=number_format($item_wise_cons_arr[$item_id]['qty_yds'],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_3'; ?>');" <?=$disable; ?>/></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtFabCst_<?=$item_id; ?>" id="txtFabCst_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("fabric_cost")],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_4'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtSpOpa_<?=$item_id; ?>" id="txtSpOpa_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("sp_operation_cost")],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_5'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtAcc_<?=$item_id; ?>" id="txtAcc_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("accessories_cost")],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_6'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtFrightCst_<?=$item_id; ?>" id="txtFrightCst_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("frieght_cost")],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_7'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtLabCst_<?=$item_id; ?>" id="txtLabCst_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("lab_test_cost")],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_8'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtMiscCst_<?=$item_id; ?>" id="txtMiscCst_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("miscellaneous_cost")],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_9'; ?>');" <?=$disable; ?> /></td>
                    
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtOtherCst_<?=$item_id; ?>" id="txtOtherCst_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("other_cost")],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_10'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtCommlCst_<?=$item_id; ?>" id="txtCommlCst_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("commercial_cost")],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_17'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtCommCst_<?=$item_id; ?>" id="txtCommCst_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("commission_cost")],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_11'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtFobDzn_<?=$item_id; ?>" id="txtFobDzn_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("fob_pcs")],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_12'; ?>');" <?=$disable; ?> /></td>
                    
                    <td title="((CPM*100)/Efficiency)"><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtCpm_<?=$item_id; ?>" id="txtCpm_<?=$item_id; ?>" value="<? if($cppm==0) echo "0"; else echo number_format($cppm,4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_13'; ?>');" disabled <? //echo $disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtSmv_<?=$item_id; ?>" id="txtSmv_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("smv")],4); ?>" onChange="fnc_total_calculate();" onBlur="fnc_cppm_cal(<?=$item_id; ?>); fncqclimit('<?=$item_id.'_14'; ?>');" <?=$disable; ?> /></td>
                    
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtCmCst_<?=$item_id; ?>" id="txtCmCst_<?=$item_id; ?>" value="<?=number_format($rowItemSumm[csf("cm_cost")],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_15'; ?>');" <?=$disable; ?> /></td>
                    <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtPack_<?=$item_id; ?>" id="txtPack_<?=$item_id; ?>" value="" onChange="fnc_total_calculate();" onBlur="fncqclimit('<?=$item_id.'_16'; ?>');" <?=$disable; ?> />&nbsp;</td>
                </tr>
                <?
				$z++;
            }
			$sql="select fab_cons_kg, fab_cons_yds, fab_amount, sp_oparation_amount, acc_amount, fright_amount, lab_amount, misce_amount, other_amount, commercial_cost, comm_amount, fob_amount, cm_amount, rmg_ratio from qc_confirm_dtls where cost_sheet_id='$cost_sheet_id' and status_active=1 and is_deleted=0";
			$dataArr=sql_select($sql);
            ?>
        </table>
        
        <table width="925" cellspacing="0" border="1" rules="all" class="rpt_table">
        	<tr id="tr_qc" bgcolor="#CCFFCC">
                <td width="80"><font color="#0000FF">QC Limit Total</font></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabConkg_qc" id="txtFabConkg_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabConmtr_qc" id="txtFabConmtr_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabConyds_qc" id="txtFabConyds_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabCst_qc" id="txtFabCst_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtSpOpa_qc" id="txtSpOpa_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtAcc_qc" id="txtAcc_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFrightCst_qc" id="txtFrightCst_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtLabCst_qc" id="txtLabCst_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtMiscCst_qc" id="txtMiscCst_qc" value="" disabled /></td>
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
                <td width="80"><font color="#FF0000">Current BOM</font></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabConkg_bom" id="txtFabConkg_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabConmtr_bom" id="txtFabConmtr_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabConyds_bom" id="txtFabConyds_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabCst_bom" id="txtFabCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtSpOpa_bom" id="txtSpOpa_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtAcc_bom" id="txtAcc_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFrightCst_bom" id="txtFrightCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtLabCst_bom" id="txtLabCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtMiscCst_bom" id="txtMiscCst_bom" value="" readonly /></td>
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
    <script>get_php_form_data($('#txt_costSheet_id').val()+'__'+$('#txtItem_id').val(),'populate_confirm_style_form_data','quick_costing_v2_controller'); fnc_bom_data_load(); fnc_total_calculate(); fnc_select();</script>          
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
			die;
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
			die;
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
		$flag=1;
		if($confirm_ids!=0)
		{
			if($confirm_ids!="")
			{
				$rIDmst_de1=execute_query( "delete from qc_confirm_mst where id in ($confirm_ids)",0);
				if($rIDmst_de1==1 && $flag==1) $flag=1; else $flag=0;
				$rIDdtls_de1=execute_query( "delete from qc_confirm_dtls where mst_id in ($confirm_ids)",0);
				if($rIDdtls_de1==1 && $flag==1) $flag=1; else $flag=0;
			}
		}
		
		$confirm_mst_id=return_next_id("id", "qc_confirm_mst", 1);
		
		$mst_field_arr="id, cost_sheet_id, lib_item_id, confirm_style, confirm_order_qty, confirm_fob, ship_date, ready_to_approve, job_id, inserted_by, insert_date";
		$mst_data_arr="(".$confirm_mst_id.",".$txt_costSheet_id.",".$txtItem_id.",".$txt_confirm_style.",".$txt_order_qty.",".$txt_confirm_fob.",".$txt_ship_date.",".$cbo_ready_approve.",".$txt_job_id.",".$user_id.",'".$pc_date_time."')";
		
		$confirm_dtls_id=return_next_id("id", "qc_confirm_dtls", 1);
		
		$dtls_field_arr="id, mst_id, cost_sheet_id, item_id, fab_cons_kg, fab_cons_mtr, fab_cons_yds, fab_amount, sp_oparation_amount, acc_amount, fright_amount, lab_amount, misce_amount, other_amount, commercial_cost, comm_amount, fob_amount, cppm_amount, smv_amount, cm_amount, rmg_ratio, inserted_by, insert_date";
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
			$txtFabConkg='txtFabConkg_'.$item_id;
			$txtFabConmtr='txtFabConmtr_'.$item_id;
			$txtFabConyds='txtFabConyds_'.$item_id;
			$txtFabCst='txtFabCst_'.$item_id;
			$txtSpOpa='txtSpOpa_'.$item_id;
			$txtAcc='txtAcc_'.$item_id;
			$txtFrightCst='txtFrightCst_'.$item_id;
			$txtLabCst='txtLabCst_'.$item_id;
			$txtMiscCst='txtMiscCst_'.$item_id;
			$txtOtherCst='txtOtherCst_'.$item_id;
			$txtCommlCst='txtCommlCst_'.$item_id;
			$txtCommCst='txtCommCst_'.$item_id;
			$txtFobDzn='txtFobDzn_'.$item_id;
			$txtCpm='txtCpm_'.$item_id;
			$txtSmv='txtSmv_'.$item_id;
			
			$txtCmCst='txtCmCst_'.$item_id;
			$txtPack='txtPack_'.$item_id;
			
			if ($add_comma!=0) $dtls_data_arr .=",";
			$dtls_data_arr .="(".$confirm_dtls_id.",".$confirm_mst_id.",".$txt_costSheet_id.",".$$txtitemid.",".$$txtFabConkg.",".$$txtFabConmtr.",".$$txtFabConyds.",".$$txtFabCst.",".$$txtSpOpa.",".$$txtAcc.",".$$txtFrightCst.",".$$txtLabCst.", ".$$txtMiscCst.", ".$$txtOtherCst.",".$$txtCommlCst.", ".$$txtCommCst.", ".trim($$txtFobDzn).",".$$txtCpm.",".$$txtSmv.",".$$txtCmCst.",".$$txtPack.",".$user_id.",'".$pc_date_time."')";
			
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
		
		$dtls_field_arr="fab_cons_kg*fab_cons_mtr*fab_cons_yds*fab_amount*sp_oparation_amount*acc_amount*fright_amount*lab_amount*misce_amount*other_amount*commercial_cost*comm_amount*fob_amount*cppm_amount*smv_amount*cm_amount*rmg_ratio*updated_by*update_date";
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
			$txtFabConmtr='txtFabConmtr_'.$item_id;
			$txtFabConyds='txtFabConyds_'.$item_id;
			$txtFabCst='txtFabCst_'.$item_id;
			$txtSpOpa='txtSpOpa_'.$item_id;
			$txtAcc='txtAcc_'.$item_id;
			$txtFrightCst='txtFrightCst_'.$item_id;
			$txtLabCst='txtLabCst_'.$item_id;
			$txtMiscCst='txtMiscCst_'.$item_id;
			$txtOtherCst='txtOtherCst_'.$item_id;
			$txtCommlCst='txtCommlCst_'.$item_id;
			$txtCommCst='txtCommCst_'.$item_id;
			$txtFobDzn='txtFobDzn_'.$item_id;
			$txtCpm='txtCpm_'.$item_id;
			$txtSmv='txtSmv_'.$item_id;
			$txtCmCst='txtCmCst_'.$item_id;
			$txtPack='txtPack_'.$item_id;
			
			$id_arr[]=str_replace("'",'',$$txtdtlsupid);
							
			$dtls_data_arr[str_replace("'",'',$$txtdtlsupid)] =explode("*",("".$$txtFabConkg."*".$$txtFabConmtr."*".$$txtFabConyds."*".$$txtFabCst."*".$$txtSpOpa."*".$$txtAcc."*".$$txtFrightCst."*".$$txtLabCst."*".$$txtMiscCst."*".$$txtOtherCst."*".$$txtCommlCst."*".$$txtCommCst."*".$$txtFobDzn."*".$$txtCpm."*".$$txtSmv."*".$$txtCmCst."*".$$txtPack."*'".$user_id."'*'".$pc_date_time."'"));
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
		
	}
	else if ($operation==3) // Approve Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$dtls_field_arr="fab_cons_kg*fab_cons_mtr*fab_cons_yds*fab_amount*sp_oparation_amount*acc_amount*fright_amount*lab_amount*misce_amount*other_amount*commercial_cost*comm_amount*fob_amount*cppm_amount*smv_amount*cm_amount*rmg_ratio*updated_by*update_date";
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
			$txtFabConmtr='txtFabConmtr_'.$item_id;
			$txtFabConyds='txtFabConyds_'.$item_id;
			$txtFabCst='txtFabCst_'.$item_id;
			$txtSpOpa='txtSpOpa_'.$item_id;
			$txtAcc='txtAcc_'.$item_id;
			$txtFrightCst='txtFrightCst_'.$item_id;
			$txtLabCst='txtLabCst_'.$item_id;
			$txtMiscCst='txtMiscCst_'.$item_id;
			$txtOtherCst='txtOtherCst_'.$item_id;
			$txtCommlCst='txtCommlCst_'.$item_id;
			$txtCommCst='txtCommCst_'.$item_id;
			$txtFobDzn='txtFobDzn_'.$item_id;
			$txtCpm='txtCpm_'.$item_id;
			$txtSmv='txtSmv_'.$item_id;
			$txtCmCst='txtCmCst_'.$item_id;
			$txtPack='txtPack_'.$item_id;
			
			$id_arr[]=str_replace("'",'',$$txtdtlsupid);
							
			$dtls_data_arr[str_replace("'",'',$$txtdtlsupid)] =explode("*",("".$$txtFabConkg."*".$$txtFabConmtr."*".$$txtFabConyds."*".$$txtFabCst."*".$$txtSpOpa."*".$$txtAcc."*".$$txtFrightCst."*".$$txtLabCst."*".$$txtMiscCst."*".$$txtOtherCst."*".$$txtCommlCst."*".$$txtCommCst."*".$$txtFobDzn."*".$$txtCpm."*".$$txtSmv."*".$$txtCmCst."*".$$txtPack."*'".$user_id."'*'".$pc_date_time."'"));
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
	
	$sql_confirm_dtls=sql_select("select id, mst_id, cost_sheet_id, item_id, fab_cons_kg, fab_cons_mtr, fab_cons_yds, fab_amount, sp_oparation_amount, acc_amount, fright_amount, lab_amount, misce_amount, other_amount, commercial_cost, comm_amount, fob_amount, cppm_amount, smv_amount, cm_amount, rmg_ratio from qc_confirm_dtls where cost_sheet_id='$ex_data[0]'");
	foreach($sql_confirm_dtls as $row_dtls)
	{
		echo "$('#txtitemid_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("item_id")]."');\n";
		echo "$('#txtFabConkg_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("fab_cons_kg")]."');\n";
		echo "$('#txtFabConmtr_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("fab_cons_mtr")]."');\n";
		echo "$('#txtFabConyds_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("fab_cons_yds")]."');\n";
		echo "$('#txtFabCst_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("fab_amount")]."');\n";
		echo "$('#txtSpOpa_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("sp_oparation_amount")]."');\n";
		echo "$('#txtAcc_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("acc_amount")]."');\n";
		echo "$('#txtFrightCst_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("fright_amount")]."');\n";
		echo "$('#txtLabCst_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("lab_amount")]."');\n";
		echo "$('#txtMiscCst_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("misce_amount")]."');\n";
		echo "$('#txtOtherCst_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("other_amount")]."');\n";
		echo "$('#txtCommlCst_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("commercial_cost")]."');\n";
		echo "$('#txtCommCst_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("comm_amount")]."');\n";
		echo "$('#txtFobDzn_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("fob_amount")]."');\n";
		
		echo "$('#txtCpm_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("cppm_amount")]."');\n";
		echo "$('#txtSmv_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("smv_amount")]."');\n";
		
		echo "$('#txtCmCst_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("cm_amount")]."');\n";
		echo "$('#txtPack_".$row_dtls[csf("item_id")]."').val('".$row_dtls[csf("rmg_ratio")]."');\n";
		
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
	
		$sql="select id, job_no, costing_per_id, order_uom_id, fabric_cost, trims_cost, embel_cost, wash_cost, comm_cost, commission, lab_test, inspection, cm_cost, freight, currier_pre_cost, certificate_pre_cost, common_oh, depr_amor_pre_cost, total_cost, price_dzn from  wo_pre_cost_dtls where job_no='$job_no' and status_active=1 and is_deleted=0";
		
		$dataArr=sql_select($sql);
		$fab_amount=$sp_oparation_amount=$acc_amount=$fright_amount=$lab_amount=$misce_amount=$other_amount=$comm_amount=$fob_amount=$cm_amount=$rmg_ratio=$commlcost=0;
		
		foreach($dataArr as $row)
		{
			$sp_cost=0;
			$sp_cost=$row[csf("embel_cost")]+$row[csf("wash_cost")];
			$fab_amount+=$row[csf("fabric_cost")];
			$sp_oparation_amount+=$sp_cost;
			$acc_amount+=$row[csf("trims_cost")];
			$fright_amount+=$row[csf("freight")];
			$lab_amount+=$row[csf("lab_test")];
			
			$misce_amount+=$row[csf("misce_amount")];
			$other_amount+=$row[csf("other_amount")];
			
			$comm_amount+=$row[csf("commission")];
			$fob_amount+=$row[csf("price_dzn")];
			$cm_amount+=$row[csf("cm_cost")];
			$rmg_ratio+=$row[csf("rmg_ratio")];
			$commlcost+=$row[csf("comm_cost")];
		}
		$str_data=$fab_cons_kg."##".$fab_cons_mtr."##".$fab_cons_yds."##".$fab_amount."##".$sp_oparation_amount."##".$acc_amount."##".$fright_amount."##".$lab_amount."##".$misce_amount."##".$other_amount."##".$comm_amount."##".$fob_amount."##".$cm_amount."##".$rmg_ratio."##".$commlcost;
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
					<? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-Select Company-", $cbo_company_name,"load_drop_down( 'quick_costing_v2_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_pop_td' );",'' ); ?>
                </td>
        		<td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_id", 130, $blank_array,'', 1, "-Select Buyer-" ); ?></td>
                <td><? echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", "", "",0 );//date('Y') ?></td>
                <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:50px"></td>
                
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td> 
                <td align="center">
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_style').value, 'create_po_search_list_view', 'search_div', 'quick_costing_v2_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
		http.open("POST","quick_costing_v2_controller.php",true);
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
                        	 <? echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name",1, "-- All Buyer--",$selected,"load_drop_down( 'quick_costing_v2_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'quick_costing_v2_controller',this.value, 'load_drop_down_sub_dep', 'sub_td' );",0 ); ?>
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
	
	$sql_mst="select a.id as mst_id, a.buyer_id, a.season_id, a.department_id, a.style_ref, a.temp_id, a.lib_item_id, a.offer_qty, a.tgt_price, a.quoted_price, a.stage_id, a.option_id, a.revise_no, a.inserted_by, a.updated_by, b.id, b.meeting_no, b.buyer_agent_id, b.location_id, b.meeting_date, $meeting_time as meeting_time, b.remarks from qc_mst a, qc_meeting_mst b where a.qc_no=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.entry_form=444 and (b.remarks<>' ' and b.remarks<>'1.' and b.remarks<>'1. ') $buyer_id_cond $season_id_cond $subDept_id_cond $style_ref_cond order by a.id DESC";
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
				http.open("POST","quick_costing_v2_controller.php",true);
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
					show_list_view(type,'agent_location_data_list','agent_location_data_div','quick_costing_v2_controller','');
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
    <script>show_list_view(type,'agent_location_data_list','agent_location_data_div','quick_costing_v2_controller','');</script>          
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
			die;
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
			die;
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
			die;
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
	echo  create_list_view ( "list_view", "Agent/Location Name", "200","200","150",1, "select tuid, agent_location from lib_agent_location where type='$data' and status_active=1 and is_deleted=0 order by id desc", "get_php_form_data", "tuid","'load_php_data_to_form_agent_location'", 1, "0", $arr, "agent_location","quick_costing_v2_controller", 'setFilterGrid("list_view",-1);','0' );
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
			$sql_fob="select a.option_id, a.offer_qty, a.option_remarks, b.tot_fob_cost from qc_mst a, qc_tot_cost_summary b where a.cost_sheet_no='$data' and a.qc_no=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=444 group by a.option_id, a.offer_qty, a.option_remarks, b.tot_fob_cost"; //a.revise_no,
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
                    <td width="60" align="right"><?php echo number_format($row[csf('tot_fob_cost')],2); ?></td>
                    <td width="80" align="right"><?php echo number_format($tot_fob,2); ?></td>
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
            <td width="60" align="right"><?php echo number_format($avg_fob,2); ?></td>
            <td width="80" align="right"><?php echo number_format($gnd_tot_fob,2); ?></td>
            <td>&nbsp;</td>
        </tr>
    </table>
    <?        
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
	
	$sql_mst="select id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, style_ref, buyer_id, uom, cons_basis, season_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, revise_no, option_id, option_remarks, buyer_remarks from qc_mst where status_active=1 and is_deleted=0 and entry_form=444 and qc_no='$data[0]'";
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
            	<td colspan="6" align="center" style="font-size:14px"><strong>ESTIMATE COST SHEET (<? echo $unit_of_measurement[$sqlMst_res[0][csf('uom')]]; ?>)</strong></td>
				<td align="right" style="font-size:14px">
							<?
							if(str_replace("'","",$id_approved_id) ==1){ ?>
                            <span style="font-size:14px; float:center;"><strong> <font style="color:green"> <? echo "[Approved]"; ?> </font></strong></span> 
                               <? }else{ ?>
								<span style="font-size:14px; float:center;"><strong> <font style="color:red"><? echo "[Not Approved]"; ?> </font></strong></span> 
							   <? } ?>
                            </td>
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
			$mk=0; $nk=11;
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
			$sql_cons_rate="select id, item_id, type, particular_type_id, consumption, ex_percent, unit, is_calculation, rate, rate_data, value from qc_cons_rate_dtls where mst_id='$data[0]' and status_active=1 and is_deleted=0 order by id asc";
			$sql_result_cons_rate=sql_select($sql_cons_rate); $item_arr=array(); $cons_rate_fab_arr=array(); $cons_rate_sp_arr=array(); $cons_rate_ac_arr=array();
			foreach ($sql_result_cons_rate as $rowConsRate)
			{
				$item_arr[$rowConsRate[csf("item_id")]]=$rowConsRate[csf("item_id")];
				if($rowConsRate[csf("type")]==1)
				{
					$cons_rate_fab_arr[$rowConsRate[csf("item_id")]].=$rowConsRate[csf("particular_type_id")].'_'.$rowConsRate[csf("consumption")].'_'.$rowConsRate[csf("unit")].'_'.$rowConsRate[csf("rate")].'_'.$rowConsRate[csf("ex_percent")].'_'.$rowConsRate[csf("rate_data")].'##';
				}
				
				if($rowConsRate[csf("particular_type_id")]!='')
				{
					if($rowConsRate[csf("particular_type_id")]!=0)
					{
						if($rowConsRate[csf("type")]==2)
						{
							$cons_rate_sp_arr[$rowConsRate[csf("item_id")]].=$rowConsRate[csf("particular_type_id")].'_'.$rowConsRate[csf("consumption")].'_'.$rowConsRate[csf("unit")].'_'.$rowConsRate[csf("rate")].'_'.$rowConsRate[csf("ex_percent")].'##';
						}
						
						if($rowConsRate[csf("type")]==3)
						{
							$cons_rate_ac_arr[$rowConsRate[csf("item_id")]].=$rowConsRate[csf("particular_type_id")].'_'.$rowConsRate[csf("consumption")].'_'.$rowConsRate[csf("unit")].'_'.$rowConsRate[csf("rate")].'_'.$rowConsRate[csf("ex_percent")].'##';
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
                            <th width="70">Ex.%</th>
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
                                <td align="right"><? echo number_format($exFab_data[4],2); ?></td>
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
                                <td align="right"><? echo number_format($exSp_data[4],2); ?></td>
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
                                <td align="right"><? echo number_format($exAc_data[4],2); ?></td>
                                <td align="right"><? echo number_format($exAc_data[3],2); ?></td>
                                <td align="right"><? echo number_format($rowAc_value,4); ?></td>
							</tr>
                        <?  $q++; } ?>
                        
                        <tr>
                        	<td colspan="6" align="right"><strong>Total</strong></td>
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
	
	$sql_mst="select id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, style_ref, buyer_id, uom, cons_basis, season_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, revise_no, option_id, option_remarks, buyer_remarks, costing_per from qc_mst where status_active=1 and is_deleted=0 and entry_form=444 and qc_no='$data[0]'";
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
                <td><strong>Costing Date:</strong></td><td><?=change_date_format($sqlMst_res[0][csf('costing_date')]); ?></td>
            </tr>
            <tr>
                <td><strong>Cost Sheet No:</strong></td><td><? echo $sqlMst_res[0][csf('cost_sheet_no')]; ?></td>
                <td><strong>Target Price($):</strong></td><td><? echo number_format($sqlMst_res[0][csf('tgt_price')],3,'.',''); ?></td>
                <td><strong>Quoted Price($):</strong></td><td><? echo number_format($sqlMst_res[0][csf('quoted_price')],3,'.',''); ?></td>
                <td><strong>Exchange Rate:</strong></td><td><? echo number_format($sqlMst_res[0][csf('exchange_rate')],3,'.',''); ?></td>
            </tr>
            <tr style="border-bottom:1px solid black;">
                <td><strong>Offer Qty.:</strong></td><td><? echo number_format($sqlMst_res[0][csf('offer_qty')],2,'.',''); ?></td>
                <td><strong>Revise/Option No:</strong></td><td><? echo 'R:- '.$sqlMst_res[0][csf('revise_no')]."/ O:- ".$sqlMst_res[0][csf('option_id')]; ?></td>
                <td><strong>Costing Per</strong></td><td><?=$qccosting_per[$sqlMst_res[0][csf('costing_per')]]; ?></td>
                <td><strong>Delivery Date</strong></td><td><?=change_date_format($sqlMst_res[0][csf('delivery_date')]); ?></td>
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
			$sql_cons_rate="select id, item_id, type, particular_type_id, consumption, ex_percent, tot_cons, uom, is_calculation, rate, rate_data, value, description from qc_cons_rate_dtls where mst_id='$data[0]' and status_active=1 and is_deleted=0 order by id asc";
			$sql_result_cons_rate=sql_select($sql_cons_rate); $item_arr=array(); $cons_rate_fab_arr=array(); $cons_rate_sp_arr=array(); $cons_rate_ac_arr=array(); $itemYdsrowSpan=array();
			foreach ($sql_result_cons_rate as $rowConsRate)
			{
				$item_arr[$rowConsRate[csf("item_id")]]=$rowConsRate[csf("item_id")];
				if($rowConsRate[csf("type")]==1)
				{
					$cons_rate_fab_arr[$rowConsRate[csf("item_id")]].=$rowConsRate[csf("particular_type_id")].'_'.$rowConsRate[csf("consumption")].'_'.$rowConsRate[csf("uom")].'_'.$rowConsRate[csf("rate")].'_'.$rowConsRate[csf("ex_percent")].'_'.$rowConsRate[csf("rate_data")].'_'.$rowConsRate[csf("is_calculation")].'_'.$rowConsRate[csf("tot_cons")].'##';
					if($rowConsRate[csf("particular_type_id")]==999)
					{
						$itemYdsrowSpan[$rowConsRate[csf("item_id")]]['span']=count(explode("@@!",$rowConsRate[csf("rate_data")]))-1;
						$itemYdsrowSpan[$rowConsRate[csf("item_id")]]['ratedata']=array_filter(explode("@@!",$rowConsRate[csf("rate_data")]));
					}
				}
				
				if($rowConsRate[csf("particular_type_id")]!='')
				{
					if($rowConsRate[csf("particular_type_id")]!=0)
					{
						if($rowConsRate[csf("type")]==2)
						{
							$cons_rate_sp_arr[$rowConsRate[csf("item_id")]].=$rowConsRate[csf("particular_type_id")].'_'.$rowConsRate[csf("consumption")].'_'.$rowConsRate[csf("uom")].'_'.$rowConsRate[csf("rate")].'_'.$rowConsRate[csf("ex_percent")].'_'.$rowConsRate[csf("tot_cons")].'##';
						}
						
						if($rowConsRate[csf("type")]==3)
						{
							$cons_rate_ac_arr[$rowConsRate[csf("item_id")]].=$rowConsRate[csf("particular_type_id")].'_'.$rowConsRate[csf("consumption")].'_'.$rowConsRate[csf("uom")].'_'.$rowConsRate[csf("rate")].'_'.$rowConsRate[csf("ex_percent")].'_'.$rowConsRate[csf("tot_cons")].'_'.$rowConsRate[csf("description")].'##';
						}
					}
				}
			}
			$yarn_dtls_arr=array(); $other_cost_arr=array();
			?>
            	<td colspan="3">
                <? foreach($itemIndex_arr as $ind=>$itemId) { ?>
                	<table width="830" cellspacing="0" border="1" rules="all" class="rpt_table">
                    	<thead>
                        	<th width="80">Item</th>
                            <th width="150">Item Description</th>
                            <th width="130">Description</th>
                            <th width="70">Uom</th>
                            <th width="80">Cons.</th>
                            <th width="70">Ex.%</th>

                            <th width="80">Tot. Cons.</th>
                            <th width="70">U.Price</th>
                            <th>Value($)</th>
                        </thead>
                        <? $fabric_data=''; $special_data=''; $accessories_data=''; $ydsData="";
						$count_fab=0; $count_sp=0; $count_acc=0;
						$yds_data=$itemYdsrowSpan[$itemId]['ratedata'];
						$fabric_data=array_filter(explode('##',$cons_rate_fab_arr[$itemId]));
						$special_data=array_filter(explode('##',$cons_rate_sp_arr[$itemId]));
						$accessories_data=array_filter(explode('##',$cons_rate_ac_arr[$itemId]));
						$count_fab=count($fabric_data)-1; $count_sp=count($special_data); $count_acc=count($accessories_data);
						$colspan=$count_fab+$count_sp+$count_acc;
						$item_name=$lib_temp_arr[$itemId]; $q=1; $item_wise_value_total=0;
						?>
                        <tr>
                        	<td colspan="9" align="center" bgcolor="#CCCCCC"><strong>Fabric Cost</strong></td>
                        </tr>
						<?
						foreach($fabric_data as $fabData)
						{
							//echo $fabData.'===';
							$exFab_data=explode('_',$fabData);
							$bodyName='';
							if($exFab_data[0]!=0)
							{
								$bodyName=$body_part[$exFab_data[0]];
							}
							if(trim($exFab_data[0])!=999)
							{
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
									<td rowspan="<?=$colspan+3+$itemYdsrowSpan[$itemId]['span']; ?>" align="center"><?=$item_name; ?></td>
									<? } ?>
									<td><? echo $bodyName; ?></td>
									<td>&nbsp;</td>
									<td><? echo $unit_of_measurement[$exFab_data[2]]; ?></td>
									<td align="right"><? echo number_format($exFab_data[1],4); ?></td>
									<td align="right"><? echo number_format($exFab_data[4],2); ?></td>
									<td align="right"><? echo number_format($exFab_data[7],2); ?></td>
									<td align="right"><? echo number_format($exFab_data[3],2); ?></td>
									<td align="right"><? echo number_format($row_value,4); ?></td>
								</tr>
								<?  $q++;
								
								if($exFab_data[5]!="") $edata=explode("~~",$exFab_data[5]); else $edata="";
								
								$ydata=""; $other_data="";
								$yarn_dtls_arr[$bodyName][$edata[0]]=$edata[1].'~~'.$edata[2].'~~'.$edata[3];
								$yarn_dtls_arr[$bodyName][$edata[4]]=$edata[5].'~~'.$edata[6].'~~'.$edata[7];
								$yarn_dtls_arr[$bodyName][$edata[8]]=$edata[9].'~~'.$edata[10].'~~'.$edata[11];	
								if($exFab_data[6]==1)
								{	
									$other_cost_arr[$bodyName][1][$edata[23]]=$edata[25];
									$other_cost_arr[$bodyName][1][$edata[26]]=$edata[28];
									$other_cost_arr[$bodyName][1][$edata[29]]=$edata[31];
									
									$other_cost_arr[$bodyName][2][$edata[32]]=$edata[34];
									$other_cost_arr[$bodyName][2][$edata[35]]=$edata[37];
									$other_cost_arr[$bodyName][2][$edata[38]]=$edata[40];
									
									$other_cost_arr[$bodyName][3][$edata[41]]=$edata[43];
									$other_cost_arr[$bodyName][3][$edata[44]]=$edata[46];
									$other_cost_arr[$bodyName][3][$edata[47]]=$edata[49];
									
									/*$other_cost_arr[$bodyName][2]=$edata[15];
									$other_cost_arr[$bodyName][3]=$edata[16];
									$other_cost_arr[$bodyName][4]=$edata[17];
									$other_cost_arr[$bodyName][5]=$edata[18];*/
								}
							}
							//if(trim($exFab_data[0])==999 &&)
						} 
						?>
                        <tr>
                        	<td colspan="9" align="center" bgcolor="#CCCCCC"><strong>Yds Cost</strong></td>
                        </tr>
						<?
						foreach($yds_data as $ydsData)
						{
							$exYds_data=explode('~~',$ydsData);
							$rowYds_value=$exYds_data[3]*$exYds_data[4];
							$item_wise_value_total+=$rowYds_value;
							?>
							<tr>
                                <td><?=$body_part[$exYds_data[0]]; ?></td>
                                <td><?=$exYds_data[6]; ?></td>
                                <td>&nbsp;</td>
                                <td align="right"><? echo number_format($exYds_data[1],4); ?></td>
                                <td align="right"><? echo number_format($exYds_data[2],2); ?></td>
                                <td align="right"><? echo number_format($exYds_data[3],2); ?></td>
                                <td align="right"><? echo number_format($exYds_data[4],2); ?></td>
                                <td align="right"><? echo number_format($rowYds_value,4); ?></td>
							</tr>
                        <?  $q++; } 
						?>
                        <tr>
                        	<td colspan="9" align="center" bgcolor="#CCCCCC"><strong>Special Operation Cost</strong></td>
                        </tr>
						<?
						foreach($special_data as $spData)
						{
							$exSp_data=explode('_',$spData);
							$rowSp_value=$exSp_data[5]*$exSp_data[3];
							$item_wise_value_total+=$rowSp_value;
							?>
							<tr>
                                <td><? echo $emblishment_name_array[$exSp_data[0]]; ?></td>
                                <td>&nbsp;</td>
                                <td><? echo $unit_of_measurement[$exSp_data[2]]; ?></td>
                                <td align="right"><? echo number_format($exSp_data[1],4); ?></td>
                                <td align="right"><? echo number_format($exSp_data[4],2); ?></td>
                                <td align="right"><? echo number_format($exSp_data[5],2); ?></td>
                                <td align="right"><? echo number_format($exSp_data[3],2); ?></td>
                                <td align="right"><? echo number_format($rowSp_value,4); ?></td>
							</tr>
                        <?  $q++; } 
						?>
                        <tr>
                        	<td colspan="9" align="center" bgcolor="#CCCCCC"><strong>Accessorises Cost</strong></td>
                        </tr>
						<?
						foreach($accessories_data as $acData)
						{
							$exAc_data=explode('_',$acData);
							$rowAc_value=$exAc_data[5]*$exAc_data[3];
							$item_wise_value_total+=$rowAc_value;
							?>
							<tr>
                                <td><?=$accessories_arr[$exAc_data[0]]; ?></td>
                                <td><?=$exAc_data[6]; ?></td>
                                <td><?=$unit_of_measurement[$exAc_data[2]]; ?></td>
                                <td align="right"><? echo number_format($exAc_data[1],4); ?></td>
                                <td align="right"><? echo number_format($exAc_data[4],2); ?></td>
                                <td align="right"><? echo number_format($exAc_data[5],2); ?></td>
                                <td align="right"><? echo number_format($exAc_data[3],2); ?></td>
                                <td align="right"><? echo number_format($rowAc_value,4); ?></td>
							</tr>
                        <?  $q++; } ?>
                        
                        <tr>
                        	<td colspan="8" align="right"><strong>Total</strong></td>
                            <td align="right"><? echo number_format($item_wise_value_total,4); ?>&nbsp;</td>
                        </tr>
                    </table><br>
                    <table width="850" cellspacing="0" border="0">
                    	<tr>
                        	<td width="460" valign="top">
                                <table width="460" cellspacing="0" border="1" rules="all" class="rpt_table">
                                    <thead>
                                        <tr>
                                            <th colspan="5">Yarn Details</th>
                                        </tr>
                                        <tr>
                                            <th width="100">Body part</th>
                                            <th width="180">Details</th>
                                            <th width="60">Rate Per KG</th>
                                            <th width="60">%</th>
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
                                                    <td width="180" style="word-break:break-all"><? echo $des; ?></td>
                                                    <td width="60" align="right"><? echo $yarndata[0]; ?></td>
                                                    <td width="60" align="right"><? echo $yarndata[1]; ?></td>
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
                            <td width="400" valign="top">
                            	<table width="400" cellspacing="0" border="1" rules="all" class="rpt_table">
                                    <thead>
                                        <tr>
                                            <th colspan="4">Other Cost Details</th>
                                        </tr>
                                        <tr>
                                        	<th width="80">Body part</th>
                                            <th width="80">Charge for</th>
                                            <th width="170">Details</th>
                                            <th>Cost</th>
                                        </tr>
                                    </thead>
                                    <?
									$charge_arr=array(1=>"Knitting Charge",2=>"Dyeing Charge",3=>"AOP Charge",4=>"Finishing Charge",5=>"Other Cost");
                                    foreach($other_cost_arr as $nameBodyPart=>$dataseq)
                                    {
                                       // $exdata=explode("@@@",$dataRate);
                                        foreach($dataseq as $seq=>$datastr)
                                        {
											foreach($datastr as $str=>$orows)
                                            //$odata=explode("~~",$orows);
											if($orows!="")
											{
                                            ?>
                                                <tr>
                                                    <td width="80" style="word-break:break-all"><?=$nameBodyPart; ?></td>
                                                    <td width="80" style="word-break:break-all"><?=$charge_arr[$seq]; ?></td>
                                                    <td width="170" style="word-break:break-all"><?=$str; ?></td>
                                                    <td align="right"><?=$orows; ?></td>
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
        <table width="900" cellspacing="0" border="1" rules="all" class="rpt_table">
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
                <th width="60">Commercial Cost</th>
                <th width="60">Commission</th>
                <th width="60">F.O.B($/DZN)</th>
                <th width="60">F.O.B($/PCS)</th>
                <th>RMG Qty(Pcs)</th>
            </thead>
			<?
            $sql_item_summ="select item_id, fabric_cost, sp_operation_cost, accessories_cost, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commercial_cost, commission_cost, fob_pcs from qc_item_cost_summary where mst_id='$data[0]' and status_active=1 and is_deleted=0";
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
                    <td align="right"><? echo number_format($rowItemSumm[csf("commercial_cost")],4); ?></td>
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
				$tot_comml+=$rowItemSumm[csf("commercial_cost")];
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
                <td align="right"><? echo number_format($tot_comml,4); ?></td>
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
				http.open("POST","quick_costing_v2_controller.php",true);
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