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
	if($db_type==0) $conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	else $conversion_date=change_date_format($data[1], "d-M-y", "-",1);

	$currency_rate=set_conversion_rate( $data[0], $conversion_date,$data[2]);
	echo "1_".$currency_rate.'_'.$fabprocess_loss_method.'_'.$accprocess_loss_method;
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

if ($action=="load_drop_down_brand")
{
	echo create_drop_down( "cbo_brand", 130, "select id, brand_name from lib_buyer_brand where buyer_id='$data' and status_active =1 and is_deleted=0 order by brand_name ASC","id,brand_name", 1, "-Brand-", $selected, "" );
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
	
	echo create_drop_down( "cbo_revise_no", 45, $rvs ,"", 0, "-0-", $select, "fnc_option_rev( this.value+'***'+document.getElementById('cbo_option_id').value+'***2');" );
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

	echo create_drop_down( "cboSpeciaTypeId_".$data[1], 100,$emb_typearr,"", 1, "--Select--", "", "","","" );
	exit();
}

if ($action=="load_drop_down_option_id")
{
	$ex_data=explode('__',$data);
	/*if($ex_data[1]!=0)
		$selectopt=$ex_data[1];
	else
		$selectopt=$selected;*/
		//load_drop_down('requires/short_quotation_v5_controller', '".$row[csf("cost_sheet_no")].'__'.$row[csf("option_id")].'__'.$row[csf("revise_no")]."', 'load_drop_down_revise_no', 'revise_td');
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
	echo create_drop_down( "cbo_option_id", 45, $rvs,"", 0, "-0-", $selectopt, "load_drop_down( 'requires/short_quotation_v5_controller', document.getElementById('txt_costSheetNo').value+'__'+this.value+'__'+0, 'load_drop_down_revise_no', 'revise_td'); fnc_option_rev( document.getElementById('cbo_revise_no').value+'***'+this.value+'***2' );" );//
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
			$('#cboItemBts_'+i).val(0);
			$('#txtBtsRatio_'+i).val('');
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
		if(operation==2)
		{
			alert("Delete Restricted.");
			release_freezing();
			return;
		}
		var tot_row=$('#tbl_tempCreat tr').length;
		var all_data=''; var z=0;
		for(var i=1; i<=tot_row; i++)
		{
			if($('#cboItemBts_'+i).val()!=0 && ($('#txtBtsRatio_'+i).val()*1)!=0)
			{
				all_data+=get_submitted_data_string('cboItemBts_'+i+'*txtBtsRatio_'+i+'*txtrowid_'+i+'*txttmpid_'+i,"../../../",i);
				z++;
			}
		}
		if(all_data=="" && z==0)
		{
			alert("Please Select minimum 1 item and input ratio.")
			release_freezing();
			return;
		}
		//alert(all_data);
		var data="action=save_update_delete_tamplete&operation="+operation+'&tot_row='+tot_row+all_data;
		
		http.open("POST","short_quotation_v5_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_template_entry_reponse;
	}

	function fnc_template_entry_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			
			if(reponse[0]==0 || reponse[0]==1)
			{
				show_msg(trim(reponse[0]));
				$('#tbl_tempCreat tr:not(:first)').remove();
				$('#cboItemBts_1').val(0);
				$('#txtBtsRatio_1').val('');
				$('#txtrowid_1').val('');
				if(trim(reponse[0])==0) alert("Data is Saved Successfully.");
				else if(trim(reponse[0])==1) alert("Data is Update Successfully.");
				
				parent.emailwindow.hide();
				set_button_status(0, permission, 'fnc_template_entry',1);
				show_list_view( '','template_list_view','save_up_list_view','short_quotation_v5_controller','setFilterGrid(\'tbl_upListView\',-1)');
			}
			else if(trim(reponse[0])==2) alert("Data is Delete Successfully.");
			else if(trim(reponse[0])==11) alert("Duplicate Data Found, Please check again.");
			else if(trim(reponse[0])==10) alert("Invalid Operation, Please check again.");
			release_freezing();
		}
	}
	
	function get_temp_data(temp_id)
	{
		var list_view_grid = return_global_ajax_value( temp_id, 'load_php_dtls_form', '', 'short_quotation_v5_controller');
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
        <tr><td width="300" valign="top">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="282" class="rpt_table">
                <thead>
                    <tr>
                        <th colspan="4" align="center">Create Template</th>
                    </tr>
                    <tr>
                        <th width="20">SL</th>
                        <th width="150">Item</th>
                        <th width="53">Ratio</th>
                        <th><input type="hidden" name="template_break_data" id="template_break_data" value="" /></th>
                    </tr>
                </thead>
              </table>
              <div style="width:300px; overflow-y:scroll; max-height:220px;" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="282" class="rpt_table" id="tbl_tempCreat" >
                	<tbody id="tbd_temp">
                        <tr id="tr_1">
                            <td width="20">1</td>
                            <td width="150"><input style="width:40px;" type="hidden" id="txtrowid_1" /><input style="width:40px;" type="hidden" id="txttmpid_1" />
                            <? echo create_drop_down( "cboItemBts_1", 150, $garments_item,"", 1, "-Select-", 0, "", "", "", "", "", "", "", "", "cboItemBts[]" ); ?></td>
                            <td width="53"><input style="width:40px;" type="text" class="text_boxes_numeric" name="txtBtsRatio[]" id="txtBtsRatio_1" /></td>
                            
                            <td><input type="button" id="increaseset_1" style="width:23px" class="formbutton" value="+" onClick="add_break_down_tr(1);" />
                                <input type="button" id="decreaseset_1" style="width:23px" class="formbutton" value="-" onClick="javascript:fn_delete_tr(1 ,'tbl_tempCreat');"/></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <table width="300" cellspacing="0" border="0" height="40">
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
	show_list_view( '','template_list_view','save_up_list_view','short_quotation_v5_controller','setFilterGrid(\'tbl_upListView\',-1)');
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
		
		$sql_tmp="select tuid, temp_id, item_id1, ratio1, status_active from qc_template where status_active=1 and is_deleted=0 and lib_item_id='0' order by temp_id ASC";
		$sql_tmp_res=sql_select($sql_tmp);
		$tmpItemRatioArr=array(); $tmpidStr=array();
		foreach($sql_tmp_res as $row)
		{
			$tmpItemRatioArr[$row[csf('item_id1')]][$row[csf('ratio1')]]=$row[csf('temp_id')];
			$tmpidStr[$row[csf('temp_id')]].=$row[csf('item_id1')].'_'.$row[csf('ratio1')].',';
		}
		
		$rowid=return_next_id( "id", "qc_template", 1);
		$tempid=$rowid;
		$data_arr="";
		$field_arr="id, temp_id, item_id1, ratio1, lib_item_id, inserted_by, insert_date, tuid";
		$m=0; $n=0; $tuid=0; $libTemp_id=0; $tmpidArr=array();
		for ($i=1; $i<=$tot_row; $i++)
		{
			$itemBts="cboItemBts_".$i;
			$btsRatio="txtBtsRatio_".$i;
			$update_id="txtrowid_".$i;
			
			$isPreTemp=0;
			$isPreTemp=$tmpItemRatioArr[str_replace("'",'',$$itemBts)][str_replace("'",'',$$btsRatio)];
			$tmpstr=array_filter(explode(",",$tmpidStr[$isPreTemp]));
			$q=0;
			foreach($tmpstr as $tstr)
			{
				$itmratiostr="";
				$itmratiostr=explode("_",$tstr);
				//echo count($tmpstr).'=='.$tot_row;
				if($tmpItemRatioArr[$itmratiostr[0]][$itmratiostr[1]]==$isPreTemp && count($tmpstr)==$tot_row)
				{
					$q++;
					$itemCount=$q;
					$tmpidArr[$itmratiostr[0]][$itmratiostr[1]]=$isPreTemp;
				}
			}
			if($libTemp_id=="") $libTemp_id=str_replace("'",'',$$itemBts); else if($libTemp_id!="") $libTemp_id.=','.str_replace("'",'',$$itemBts);
			
			$tuid=$_SESSION['logic_erp']['user_id'].$tempid;
			if ($i!=1) $data_arr .=",";
			$data_arr .="(".$rowid.",'".$tempid."',".$$itemBts.",".$$btsRatio.",'0',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$tuid."')";
			$rowid=$rowid+1;
			$m++;
		}//die;
		
		$k=0;
		if($m==$q)
		{
			for ($i=1; $i<=$tot_row; $i++)
			{
				$itemBts="cboItemBts_".$i;
				$btsRatio="txtBtsRatio_".$i;
				//echo $m.'--'.$q.'--'.$tmpItemRatioArr[str_replace("'",'',$$itemBts)][str_replace("'",'',$$btsRatio)].'=='.$tmpidArr[str_replace("'",'',$$itemBts)][str_replace("'",'',$$btsRatio)].'<br>';
				if($tmpItemRatioArr[str_replace("'",'',$$itemBts)][str_replace("'",'',$$btsRatio)]==$tmpidArr[str_replace("'",'',$$itemBts)][str_replace("'",'',$$btsRatio)] && $tmpItemRatioArr[str_replace("'",'',$$itemBts)][str_replace("'",'',$$btsRatio)]!="" && $tmpidArr[str_replace("'",'',$$itemBts)][str_replace("'",'',$$btsRatio)]!="")
				{
					$k++;
				}
			}
		}
		
		if($k==$m)
		{
			echo "11**0"; disconnect($con); die; //Duplicate Data Check
		}
		
		//echo "10**".$k.'='.$m.'='.$q; disconnect($con); die;
		
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
		//echo "select temp_id from qc_template where id=".$txtrowid_1.""; die;
		$previous_temp_id=return_field_value("temp_id","qc_template","id=".$txtrowid_1."","temp_id");
		
		$sql_tmp="select tuid, temp_id, item_id1, ratio1, status_active from qc_template where status_active=1 and is_deleted=0 and lib_item_id='0' order by temp_id ASC";
		$sql_tmp_res=sql_select($sql_tmp);
		$row_id_arr=array(); $tmpItemRatioArr=array(); $tmpidStr=array();
		foreach($sql_tmp_res as $row)
		{
			if($row[csf('temp_id')]==$previous_temp_id)
			{
				$row_id_arr[]=$row[csf('id')];
			}
			else
			{
				$tmpItemRatioArr[$row[csf('item_id1')]][$row[csf('ratio1')]]=$row[csf('temp_id')];
				$tmpidStr[$row[csf('temp_id')]].=$row[csf('item_id1')].'_'.$row[csf('ratio1')].',';
			}
		}
		
		//print_r($row_id_arr); die;
		$rowid=return_next_id( "id", "qc_template", 1);
		//$tempid=$rowid;
		$data_arr="";
		$field_arr="id, temp_id, item_id1, ratio1, lib_item_id, inserted_by, insert_date, tuid";
		$field_arr_up="item_id1*ratio1*lib_item_id*updated_by*update_date";
		$m=0; $n=0; $tuid=0;
		for ($i=1;$i<=$tot_row;$i++)
		{
			$itemBts="cboItemBts_".$i;
			$btsRatio="txtBtsRatio_".$i;
			$update_id="txtrowid_".$i;
			$tempid="txttmpid_".$i;
			//if($libTemp_id=="") $libTemp_id=str_replace("'",'',$$itemBts); else $libTemp_id.=','.str_replace("'",'',$$itemBts);
			
			$isPreTemp=0;
			$isPreTemp=$tmpItemRatioArr[str_replace("'",'',$$itemBts)][str_replace("'",'',$$btsRatio)];
			$tmpstr=array_filter(explode(",",$tmpidStr[$isPreTemp]));
			$q=0;
			foreach($tmpstr as $tstr)
			{
				$itmratiostr="";
				$itmratiostr=explode("_",$tstr);
				
				if($tmpItemRatioArr[$itmratiostr[0]][$itmratiostr[1]]==$isPreTemp && count($tmpstr)==$tot_row)
				{
					$q++;
					$itemCount=$q;
					$tmpidArr[$itmratiostr[0]][$itmratiostr[1]]=$isPreTemp;
				}
			}
			
			if(str_replace("'",'',$$update_id)=="")
			{
				$tuid=$_SESSION['logic_erp']['user_id'].$tempid;
				if ($i!=1) $data_arr .=",";
				$data_arr .="(".$rowid.",'".$$tempid."',".$$itemBts.",".$$btsRatio.",'0',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$tuid."')";
				$rowid=$rowid+1;
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$update_id);
				$data_arr_up[str_replace("'",'',$$update_id)] =explode("*",("".$$itemBts."*".$$btsRatio."*'0'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
			}
			$m++;
		}
		//echo "0**".$data_arr; die;
		
		$k=0;
		if($m==$q)
		{
			for ($i=1; $i<=$tot_row; $i++)
			{
				$itemBts="cboItemBts_".$i;
				$btsRatio="txtBtsRatio_".$i;
				if($tmpItemRatioArr[str_replace("'",'',$$itemBts)][str_replace("'",'',$$btsRatio)]==$tmpidArr[str_replace("'",'',$$itemBts)][str_replace("'",'',$$btsRatio)] && $tmpItemRatioArr[str_replace("'",'',$$itemBts)][str_replace("'",'',$$btsRatio)]!="" && $tmpidArr[str_replace("'",'',$$itemBts)][str_replace("'",'',$$btsRatio)]!="")
				{
					$k++;
				}
			}
		}
		if($k==$m)
		{
			echo "11**0"; disconnect($con); die;//Duplicate Data Check
		}
		
		//echo "10**".$k.'='.$m.'='.$n; disconnect($con); die;
		
		
		$flag=$rID=$rID1=$rID5=1;
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
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
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
		//echo "10**".trim(implode(',',$distance_delete_id));
		//print_r ($distance_delete_id);
		if(trim(implode(',',$distance_delete_id))==",") $distance_delete_id=array();
		if(trim(implode(',',$distance_delete_id))!="")
		{
			foreach($distance_delete_id as $id_val)
			{
				$rID5=sql_update("qc_template",$field_array_del,$data_array_del,"id","".$id_val."",1);
				if($rID5==1 && $flag==1) $flag=1; else $flag=0;
			}
		}
		//echo "10**".$rID.'='.$rID1.'='.$rID5.'='.$flag; die;
		
		if($db_type==0)
		{
			if($flag==1 ){
				mysql_query("COMMIT");  
				echo "1**";
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
				echo "1**";
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
				$sql_tmp="select tuid, temp_id, item_id1, ratio1, status_active from qc_template where status_active=1 and is_deleted=0 and lib_item_id='0' order by  item_id1, ratio1 ASC";
				$sql_tmp_res=sql_select($sql_tmp);
				$mst_temp_arr=array();
				foreach($sql_tmp_res as $row)
				{
					$mst_temp_arr[$row[csf('tuid')]].=$row[csf('item_id1')].'**'.$row[csf('ratio1')].'**'.$row[csf('status_active')].'__';
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
						$item_id1=''; $ratio1=0;
						
						$item_id1=$ex_tmp_val[0]; 
						$ratio1=$ex_tmp_val[1]; 
						
						if($template_name=='') $template_name=$garments_item[$item_id1].'-'.$ratio1; else $template_name.=','.$garments_item[$item_id1].'-'.$ratio1;
					}
					//sort($template_name);
					if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr id="tr_<?=$i; ?>" bgcolor="<?=$bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="get_temp_data('<?=$temp_id; ?>');"> 
                        <td width="30" align="center"><?=$i; ?></td>
                        <td width="50" align="center"><?=$temp_id; ?></td>
                        <td><div style="word-wrap:break-word; width:250px"><?=$template_name; ?></div></td>
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
	$sql_tmp="select tuid, temp_id, item_id1, ratio1, status_active from qc_template where status_active=1 and is_deleted=0 and lib_item_id='0' order by id ASC";
	$sql_tmp_res=sql_select($sql_tmp);
	$mst_temp_arr=array();
	foreach($sql_tmp_res as $row)
	{
		$mst_temp_arr[$row[csf('tuid')]].=$row[csf('item_id1')].'**'.$row[csf('ratio1')].'**'.$row[csf('status_active')].'__';
	}
	$template_name_arr=array();
	foreach($mst_temp_arr as $temp_id=>$tmp_data)
	{
		$template_data=array_filter(explode('__',$tmp_data));
		$template_name='';
		foreach($template_data as $temp_val)
		{
			$ex_tmp_val=explode('**',$temp_val);
			$item_id1=''; $ratio1=0;
			
			$item_id1=$ex_tmp_val[0]; 
			$ratio1=$ex_tmp_val[1]; 
			
			if($template_name=='') $template_name=$garments_item[$item_id1].'::'.$ratio1; else $template_name.=','.$garments_item[$item_id1].'::'.$ratio1;
			
		}
		$template_name_arr[$temp_id]=$template_name;
	}
	
	echo create_drop_down( "cbo_temp_id", 130, $template_name_arr,'', 1, "-Select Template-",$selected, "fnc_template_view();" ); 
	exit();
}

if($action=="load_drop_down_tempItem")
{
	$exdata=explode(",",$data);
	$itemRatioArr=array();
	
	foreach($exdata as $xdata)
	{
		$exitemdata=explode("!!",$xdata);
		
		$itemRatioArr[$exitemdata[0]]=$garments_item[$exitemdata[0]].'::'.$exitemdata[1];
	}
	
	echo create_drop_down( "cboItemId", 90, $itemRatioArr,"", 0, "-Select-", $selected, "fnc_change_data();","","");
	exit();
}

if($action=="load_php_dtls_form")
{
	$sql_tmp="select id, tuid, temp_id, item_id1, ratio1, status_active from qc_template where tuid='$data' and status_active=1 and is_deleted=0 and lib_item_id='0' order by id ASC";
	$sql_tmp_res=sql_select($sql_tmp); $i=1; $j=1;
	foreach($sql_tmp_res as $row)
	{
		?>
        <tr id="tr_<?=$i; ?>">
            <td width="20"><?=$i; ?></td>
            <td width="150"><input style="width:40px;" type="hidden" id="txtrowid_<?=$i; ?>" value="<?=$row[csf('id')]; ?>" />
            <input style="width:40px;" type="hidden" id="txttmpid_<?=$i; ?>" value="<?=$row[csf('temp_id')]; ?>" />
                <? echo create_drop_down( "cboItemBts_$i", 150, $garments_item,"", 0, "-Select-", $row[csf('item_id1')], "", "", "", "", "", "", "", "", "cboItemBts[]" ); ?></td>
            <td width="53"><input style="width:40px;" type="text" class="text_boxes_numeric" name="txtBtsRatio[]" id="txtBtsRatio_<?=$i; ?>" value="<?=$row[csf('ratio1')]; ?>" /></td>
            <td><input type="button" id="increaseset_<?=$i; ?>" style="width:23px" class="formbutton" value="+" onClick="add_break_down_tr(<?=$i; ?>)" />
                <input type="button" id="decreaseset_<?=$i; ?>" style="width:23px" class="formbutton" value="-" onClick="javascript:fn_delete_tr(<?=$i; ?> ,'tbl_tempCreat');"/></td>
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
	echo load_html_head_contents("Fab. Rate Details Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	/*$yarnCountArr=return_library_array("select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id","yarn_count");
	
	$sqlFab="select id, type, yarn_lib_id, fab_des from qc_fab_yarn_conv where qc_no='$qc_no' and cons_rate_dtls_id='$consRateId' and type=1 and status_active=1 and is_deleted=0";
	 
	$sqlFabData=sql_select($sqlFab); $fab_description=array();
	foreach($sqlFabData as $row)
	{
		$fab_description[$row[csf("id")]]=$body_part[$body_part_id].', '.$row[csf("fab_des")];
	}
	unset($sqlFabData);*/
	
	$adata=$qc_no.'!!!'.$bodypartid.'!!!'.$consRateId.'!!!'.$packQty.'!!!'.$itemRatio.'!!!'.$rateData;
	?>
    <script>
		var qc_no='<?=$qc_no; ?>';
		var bodypartid='<?=$bodypartid; ?>';
		var consRateId='<?=$consRateId; ?>';
		var adata='<?=$adata; ?>';
		var fabuom='<?=$fabuom; ?>';
		var itemRatio='<?=$itemRatio; ?>';
		
		function fnc_cons_amt()
		{
			var consgrampcs=$('#txtknitgrampcs').val()*1;
			var conslbspcs=consgrampcs/453.594;
			var conslbsdzn=conslbspcs*12;
			
			var sizegrading=$('#txtsizegrading').val()*1;
			var wastage=$('#txtwastage').val()*1;
			
			var actuakconskgpcs=((consgrampcs*((sizegrading+wastage)/100))+consgrampcs)/1000;
			
			//(consgrampcs+(consgrampcs*(sizegrading+wastage)))/1000;
			var actuakconslbspcs=actuakconskgpcs*2.20462;
			var actuakconslbsdzn=actuakconslbspcs*12;
			
			$('#txtknitlbspcs').val( number_format(conslbspcs,8,'.','') );
			$('#txtknitlbsdzn').val( number_format(conslbsdzn,4,'.','') );
			$('#txtactualconskgpcs').val( number_format(actuakconskgpcs,4,'.','') );
			
			$('#txtactualconslbspcs').val( number_format(actuakconslbspcs,8,'.','') );
			$('#txtactualconslbsdzn').val( number_format(actuakconslbsdzn,4,'.','') );
		}
		
		function js_set_value()
		{
			//fnc_total_rate(0);
			//var row =$('#tbl_fab tbody tr').length; 
			var consData=$('#txtknitgrampcs').val()+'~~'+$('#txtknitlbspcs').val()+'~~'+$('#txtknitlbsdzn').val()+'~~'+$('#txtsizegrading').val()+'~~'+$('#txtwastage').val()+'~~'+$('#txtactualconskgpcs').val()+'~~'+$('#txtactualconslbspcs').val()+'~~'+$('#txtactualconslbsdzn').val();
			
			document.getElementById('txtdatastr').value=consData;
			
			parent.emailwindow.hide();
		}
		
		function fnc_row_generate()
		{
			var data=adata;
			
			show_list_view(data,'populate_confirm_fabric_data','fabricDiv','short_quotation_v5_controller','');
			fnc_cons_amt("0_1");
		}
		
	</script>
	</head>
	<body onLoad="set_hotkey();">
        <div id="rate_details"  align="center"> 
            <?=load_freeze_divs ("../../../",'',1); ?>           
            <form name="rateDetails_1" id="rateDetails_1" autocomplete="off">
                <div id="fabricDiv"></div>
            </form>
        </div>
        <input type="hidden" class="text_boxes" name="hid_qc_no" id="hid_qc_no" value="<?=$qc_no; ?>">
        <input type="hidden" class="text_boxes" name="hid_cons_rate_dtls_id" id="hid_cons_rate_dtls_id" value="<?=$consRateId; ?>">
        <input type="hidden" class="text_boxes" name="hid_bodypart_id" id="hid_bodypart_id" value="<?=$bodypartid; ?>">
        <input type="hidden" class="text_boxes" name="hid_body_cons" id="hid_body_cons" value="<?=$totCons; ?>">
        <input type="hidden" class="text_boxes" name="txtdatastr" id="txtdatastr" value="">
    </body>
    <script>fnc_row_generate(); </script>      
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="accrate_details_popup")
{
	echo load_html_head_contents("Acc. Rate Details Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	/*$yarnCountArr=return_library_array("select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id","yarn_count");
	
	$sqlFab="select id, type, yarn_lib_id, fab_des from qc_fab_yarn_conv where qc_no='$qc_no' and cons_rate_dtls_id='$consRateId' and type=1 and status_active=1 and is_deleted=0";
	 
	$sqlFabData=sql_select($sqlFab); $fab_description=array();
	foreach($sqlFabData as $row)
	{
		$fab_description[$row[csf("id")]]=$body_part[$body_part_id].', '.$row[csf("fab_des")];
	}
	unset($sqlFabData);*/
	
	$adata=$qc_no.'!!!'.$itemRatio.'!!!'.$acctext.'!!!'.$accuom.'!!!'.$accRateData;
	?>
    <script>
		var qc_no='<?=$qc_no; ?>';
		var acctext='<?=$acctext; ?>';
		var accuom='<?=$accuom; ?>';
		var accRateData='<?=$accRateData; ?>';
		var adata='<?=$adata; ?>';
		var itemRatio='<?=$itemRatio; ?>';
		var process_loss_method_id='<?=$process_loss_method_id; ?>';
		
		function fnc_cons_amt(str)
		{
			var valstr=str.split('_');
			var inc=valstr[0];
			var type=valstr[1];
			
			var colorratio=$("#txtcolorratio_"+inc).val()*1;
			
			if(type==1)
			{
				var colorconsdzn=$('#txtcolorconsdzn_'+inc).val()*1;
				var consAccPk=(colorconsdzn/12)*colorratio;
				//alert(colorconsdzn+'__'+colorratio)
				$("#txtcolorconspack_"+inc).val( number_format(consAccPk,4,'.','') );
			}
			else if(type==2)
			{
				var consAccPk=$("#txtcolorconspack_"+inc).val()*1;
				var consAccDzn=(consAccPk/colorratio)*12;
				$("#txtcolorconsdzn_"+inc).val( number_format(consAccDzn,4,'.','') );
			}
			
			//var colorconsdzn=($('#txtcolorconsdzn_'+inc).val()*1)/12;
			var conspack=$("#txtcolorconspack_"+inc).val()*1;//number_format(colorconsdzn,4,'.','')*($('#txtcolorratio_'+inc).val()*1);
			//$('#txtcolorconspack_'+inc).val( number_format(conspack,4,'.','') );
			
			var ex_acper=$("#txtcolorexper_"+inc).val()*1;
			//alert(conspack+'__'+conspack+'__'+ex_acper+'__'+process_loss_method_id)
			var rowtotCons=0;
			if(process_loss_method_id==1)
			{
				rowtotCons=conspack+(conspack*(ex_acper/100));
				
			}
			else if(process_loss_method_id==2)
			{
				var devided_val = 1-(ex_acper/100);
				rowtotCons=parseFloat(conspack/devided_val);
			}
			else rowtotCons=0;
			
			if((ex_acper*1)==0) rowtotCons=conspack;
			$('#txtcolortotccons_'+inc).val( number_format(rowtotCons,4,'.','') );
			
			var rowtotCons=number_format(rowtotCons,4,'.','');
			var packRate=($('#txtcolorrate_'+inc).val()*1);
			var rowamt=rowtotCons*packRate;
			
			$('#txtcoloramt_'+inc).val( number_format(rowamt,4,'.','') );
			
			fnc_total_rate(valstr[1]);
		}
		
		function fnc_total_rate(type)
		{
			if(type==1)
			{
				var row =$('#tbl_acc tbody tr').length; 
				var z=0; var consDzn=0; var isExPer=0; var totConsPack=0; var consPack=0; var amtTot=0;
				for(var i=1; i<=row; i++)
				{
					if(($('#txtcolorratio_'+i).val()*1)!=0)
					{
						if(($('#txtcolorexper_'+i).val()*1)>0) isExPer=1;
						
						consDzn+=($('#txtcolorconsdzn_'+i).val()*1); 
						consPack+=($('#txtcolorconspack_'+i).val()*1);
						totConsPack+=($('#txtcolortotccons_'+i).val()*1);
						amtTot+=($('#txtcoloramt_'+i).val()*1);
						z++;
					}
				}
				consDzn=consDzn/z;
				var avg_rate=amtTot/totConsPack;
				if(isExPer==1)
				{
					var avgexper=(totConsPack-consPack)/consPack*100;
				}
				else { var avgexper=0; }
				
				$('#txttotcons_dzn').val( number_format(consDzn,4,'.','') );
				$('#txttotcons_pack').val( number_format(consPack,4,'.','') );
				$('#txttotexper').val( number_format(avgexper,4,'.','') );
				$('#txttotcons').val( number_format(totConsPack,4,'.','') );
				$('#txt_avg_rate').val( number_format(avg_rate,4,'.','') );
				$('#txt_amt_pack').val( number_format(amtTot,4,'.','') );
			}
		}
		
		function js_set_value()
		{
			fnc_total_rate(0);
			var row =$('#tbl_acc tbody tr').length; 
			var consData=""; var ratioVal=0;
			for(var i=1; i<=row; i++)
			{
				if(($('#txtcolorratio_'+i).val()*1)!=0)
				{
					if(consData=="") consData+=$('#txtitemqty_'+i).val()+'_'+$('#hidupid_'+i).val()+'_'+$('#txtcoloroption_'+i).val()+'_'+$('#txtcolorratio_'+i).val()+'_'+$('#txtcolorconsdzn_'+i).val()+'_'+$('#txtcolorconspack_'+i).val()+'_'+$('#txtcolorexper_'+i).val()+'_'+$('#txtcolortotccons_'+i).val()+'_'+$('#txtcolorrate_'+i).val()+'_'+$('#txtcoloramt_'+i).val();
					else consData+="@@!"+$('#txtitemqty_'+i).val()+'_'+$('#hidupid_'+i).val()+'_'+$('#txtcoloroption_'+i).val()+'_'+$('#txtcolorratio_'+i).val()+'_'+$('#txtcolorconsdzn_'+i).val()+'_'+$('#txtcolorconspack_'+i).val()+'_'+$('#txtcolorexper_'+i).val()+'_'+$('#txtcolortotccons_'+i).val()+'_'+$('#txtcolorrate_'+i).val()+'_'+$('#txtcoloramt_'+i).val();
					ratioVal=ratioVal+($('#txtcolorratio_'+i).val()*1);
				}
			}
			if((ratioVal*1)>(itemRatio*1))
			{
				alert("Sum of ratio must be less than or equal to Item Qty.");
				return;	
			}
			
			document.getElementById('txtdatastr').value=consData;
			document.getElementById('txttotcons_dzn').value;
			document.getElementById('txttotcons_pack').value;
			document.getElementById('txttotexper').value;
			document.getElementById('txttotcons').value;
			document.getElementById('txt_avg_rate').value;
			document.getElementById('txt_amt_pack').value;
			parent.emailwindow.hide();
		}
		
		function append_row(str)
		{
			var strval=str.split('_');
			var inc=strval[0];
			var counter =$('#tbl_acc tbody tr').length; 
			var i=inc;
			if (counter!=i)
			{
				return false;
			}
			else
			{
				i++;
				$("#tbl_acc tbody tr:last").clone().find("input").each(function() {
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						'name': function(_, name) { return name + i },
						'value': function(_, value) { return value }
					});
				}).end().appendTo("#tbl_acc");
				
				$('#txtcolorratio_'+i).removeAttr("onChange").attr("onChange","fnc_cons_amt('"+i+"_1')");
				$('#txtcolorconsdzn_'+i).removeAttr("onChange").attr("onChange","fnc_cons_amt('"+i+"_1')");
				$('#txtcolorconspack_'+i).removeAttr("onChange").attr("onChange","fnc_cons_amt('"+i+"_2')");
				$('#txtcolorexper_'+i).removeAttr("onChange").attr("onChange","fnc_cons_amt('"+i+"_1')");
				$('#txtcolorrate_'+i).removeAttr("onChange").attr("onChange","fnc_cons_amt('"+i+"_1')");
				
				$('#increasewash_'+i).removeAttr("onClick").attr("onClick","append_row('"+i+"_1');");
				$('#decreasewash_'+i).removeAttr("onClick").attr("onClick","fnc_remove_row("+i+");");
				
				$('#hidupid_'+i).val("");
				$('#txtcoloroption_'+i).val("");
				$('#txtcolorratio_'+i).val("");
				$('#txtcolorconsdzn_'+i).val("");
				$('#txtcolorconspack_'+i).val("");
				$('#txtcolorexper_'+i).val("");
				$('#txtcolortotccons_'+i).val("");
				$('#txtcolorrate_'+i).val("");
				$('#txtcoloramt_'+i).val("");
			}
			fnc_cons_amt('"+i+"_1');
		}
		
		function fnc_remove_row(inc)
		{
			var counter =$('#tbl_acc tbody tr').length; 
			if(counter!=1)
			{
				var index=inc-1;
				$("table#tbl_acc tbody tr:eq("+index+")").remove();
				
				var numRow = $('table#tbl_acc tbody tr').length;
				for(var i=1; i<=counter; i++)
				{
					var index=i-1;
					$("#tbl_acc tbody tr:eq("+index+")").find("input").each(function() {
						$(this).attr({
							'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						});
					});
					
					$("#tbl_acc tbody tr:eq("+index+")").each(function(){
						$('#txtcolorratio_'+i).removeAttr("onChange").attr("onChange","fnc_cons_amt('"+i+"_1')");
						$('#txtcolorconsdzn_'+i).removeAttr("onChange").attr("onChange","fnc_cons_amt('"+i+"_1')");
						$('#txtcolorconspack_'+i).removeAttr("onChange").attr("onChange","fnc_cons_amt('"+i+"_2')");
						$('#txtcolorexper_'+i).removeAttr("onChange").attr("onChange","fnc_cons_amt('"+i+"_1')");
						$('#txtcolorrate_'+i).removeAttr("onChange").attr("onChange","fnc_cons_amt('"+i+"_1')");
						
						
						$('#increasewash_'+i).removeAttr("onClick").attr("onClick","append_row('"+i+"_1');");
						$('#decreasewash_'+i).removeAttr("onClick").attr("onClick","fnc_remove_row("+i+");");
					});
				}
				fnc_cons_amt('"+i+"_1');
			}
		}
		
		var permission='<?=$permission; ?>';
		function fnc_accrow_generate()
		{
			var data=adata;
			
			show_list_view(data,'populate_confirm_acc_data','accDiv','short_quotation_v5_controller','');
		}
		
	</script>
	</head>
	<body onLoad="set_hotkey();">
        <div id="rate_details"  align="center"> 
            <?=load_freeze_divs ("../../../",'',1); ?>           
            <form name="rateDetails_1" id="rateDetails_1" autocomplete="off">
                <div id="accDiv"></div>
            </form>
        </div>
        <input type="hidden" class="text_boxes" name="hid_qc_no" id="hid_qc_no" value="<?=$qc_no; ?>">
    </body>
    <script>fnc_accrow_generate(); </script>      
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="fabric_popup")
{
    echo load_html_head_contents("Fabric Info","../../../", 1, 1, '','1','');
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
        <fieldset style="width:530px;margin-left:10px">
            <?
			$composition_arr=array(); $yarn_descriptionArr=array();
			$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
			$sql="select b.copmposition_id, b.percent, b.count_id, b.type_id, b.yarn_rate, a.id, b.id as bid from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id, b.id";
			$data_array=sql_select($sql);
			if (count($data_array)>0)
			{
				foreach( $data_array as $row )
				{
					if(array_key_exists($row[csf('id')],$composition_arr))
					{
						$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
					}
					else
					{
						$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
					}
					
					if(array_key_exists($row[csf('id')],$yarn_descriptionArr))
					{
						$yarn_descriptionArr[$row[csf('id')]].="##".$row[csf('count_id')].",".$row[csf('copmposition_id')].",100,".$row[csf('type_id')].",".$row[csf('percent')].",".$row[csf('yarn_rate')];
					}
					else 
					{
						$yarn_descriptionArr[$row[csf('id')]]=$row[csf('count_id')].",".$row[csf('copmposition_id')].",100,".$row[csf('type_id')].",".$row[csf('percent')].",".$row[csf('yarn_rate')];
					}
				}
				unset($data_array);
			}
			//print_r($yarn_descriptionArr);
			
			$sqlConv="select mst_id, process_id, process_loss, rate from conversion_process_loss where status_active=1 and is_deleted=0 and rate>0";
			
			$sqlConvRes=sql_select($sqlConv); $convDataArr=array();
			if (count($sqlConvRes)>0)
			{
				foreach($sqlConvRes as $row)
				{
					if(array_key_exists($row[csf('mst_id')],$convDataArr))
					{
						$convDataArr[$row[csf('mst_id')]].="##".$row[csf('process_id')].",".$row[csf('rate')];
					}
					else
					{
						$convDataArr[$row[csf('mst_id')]]=$row[csf('process_id')].",".$row[csf('rate')];
					}
				}
				unset($sqlConvRes);
			}
			
            $lib_sup=return_library_array("select supplier_name,id from lib_supplier", "id", "supplier_name");
            $lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
            $sql="select id, supplier_id, yarn_count, composition, percent, yarn_type from lib_yarn_rate where status_active=1 and is_deleted=0 order by id";
            $data_array=sql_select($sql);
            ?>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="530" >
                <thead>
                    <th width="30">SL</th>
                    <th width="90">Fab Nature</th>
                    <th width="80">Construction</th>
                    <th width="150">Composition</th>
                    <th width="50">GSM/ Weight</th>
                    <th>Color Range</th>
                </thead>
            </table>
            <div style="width:530px; max-height:250px;overflow-y:scroll;" >  
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="510" class="rpt_table" id="list_view">
                    <tbody>
                        <? 
                        $i=1;
						$sql_data=sql_select("select a.fab_nature_id, a.construction, a.gsm_weight, a.color_range_id, a.stich_length, a.process_loss, a.id, a.sequence_no from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.fab_nature_id, a.construction, a.gsm_weight, a.color_range_id, a.stich_length, a.process_loss, a.sequence_no order by a.id ASC");
						$i=1;
						foreach($sql_data as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$yarnBreak="";
							//if($row[csf('fab_nature_id')]==2) 
							$yarnBreak=$yarn_descriptionArr[$row[csf('id')]]; 
							//else $yarnBreak="";
							?>
							<tr id="tr_<?=$row[csf('id')]; ?>" bgcolor="<?=$bgcolor; ?>" style="cursor:pointer; word-break:break-all;" onClick="js_set_value('<?=$row[csf('id')]."_".$row[csf('construction')]."_".$composition_arr[$row[csf('id')]]."_".$row[csf('gsm_weight')]."_".$yarnBreak."_".$convDataArr[$row[csf('id')]]; ?>');">
								<td width="30" align="center" title="<?=$yarnBreak.'--'.$convDataArr[$row[csf('id')]]; ?>"><?=$i; ?></td>
								<td width="90" style="word-break:break-all"><?=$item_category[$row[csf('fab_nature_id')]]; ?></td>
								<td width="80" style="word-break:break-all"><?=$row[csf('construction')]; ?></td>
								<td width="150" style="word-break:break-all"><?=$composition_arr[$row[csf('id')]]; ?></td>
								<td width="50" style="word-break:break-all" align="right"><?=$row[csf('gsm_weight')]; ?></td>
								<td style="word-break:break-all"><?=$color_range[$row[csf('color_range_id')]]; ?></td>
							</tr>
							<?
							$i++;
						}
						
                        ?>
                    </tbody>
                </table>
            </div>
            <input type="" name="popupData" id="popupData" value="" style="width:50px">
        </fieldset>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>setFilterGrid('list_view',-1);</script>
    </html>
    <?
    exit();
}

if($action=="save_update_delete_fabric")
{
	$process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    //echo "10**".$operation; die;
    if ($operation==0)  // Insert Here
    {
        $con = connect();
        if($db_type==0) mysql_query("BEGIN");

        $id=return_next_id( "id", "qc_fab_yarn_conv", 1);
		$field_array="id, qc_no, body_part_id, type, yarn_lib_id, count, composition, percent, yarn_type, fab_id, process, fab_des, cons, exper, tot_cons, rate, value, yarn_break_down, conv_break_down, cons_rate_dtls_id, inserted_by, insert_date, status_active, is_deleted";
		//echo "10**";
		$add_comma=0; $data_array=''; $amt=0; $totAmt=0;
        for($i=1; $i<=$numRowFabric; $i++) 
        {
            $hidLibid       ="hidLibid_".$i;
            $txtFabricDtls  ="txtFabricDtls_".$i;
			$txtYarnDtls    ="txtYarnDtls_".$i;
			$txtConvDtls    ="txtConvDtls_".$i;
            $txtfabcons    	="txtfabcons_".$i;
            $txtfabex   	="txtfabex_".$i;
            $txtfabtotcons  ="txtfabtotcons_".$i;
            $txtfabrate     ="txtfabrate_".$i;
            $txtfabamt      ="txtfabamt_".$i;
            $hidupid        ="hidupid_".$i;
			//echo $$txtYarnDtls.'*****'.$$txtConvDtls.'<br>';
			
			if((str_replace("'",'',$$txtfabtotcons)*1)!=0)
			{
				if ($add_comma!=0) $data_array .=","; $add_comma=0;
				$data_array .="(".$id.",".$hid_qc_no.",".$hid_bodypart_id.",1,".$$hidLibid.",0,0,0,0,0,0,".$$txtFabricDtls.",".$$txtfabcons.",".$$txtfabex.",".$$txtfabtotcons.",".$$txtfabrate.",".$$txtfabamt.",".$$txtYarnDtls.",".$$txtConvDtls.",".$hid_cons_rate_dtls_id.",'".$user_id."','".$pc_date_time."',1,0)";
				$fabid=$id;
				$id++; $add_comma++;
				
				if(str_replace("'",'',$$txtYarnDtls)!="")//Yarn Data
				{
					$yarnbreckdown_array=explode('##',str_replace("'",'',$$txtYarnDtls));
					for($y=0; $y< count($yarnbreckdown_array); $y++)
					{
						$counter++;
						$yarnbreckdownarr=explode(',',$yarnbreckdown_array[$y]);
						
						$cons=def_number_format(((str_replace("'",'',$$txtfabtotcons)*$yarnbreckdownarr[4])/100),5,"");
						$yarnamt=$cons*$yarnbreckdownarr[5];
						
						if ($add_comma!=0) $data_array .=","; $add_comma=0;
						$data_array .="(".$id.",".$hid_qc_no.",".$hid_bodypart_id.",2,0,'".$yarnbreckdownarr[0]."','".$yarnbreckdownarr[1]."','".$yarnbreckdownarr[4]."','".$yarnbreckdownarr[3]."','".$fabid."',0,'','".$cons."',0,0,'".$yarnbreckdownarr[5]."','".$yarnamt."','','',".$hid_cons_rate_dtls_id.",'".$user_id."','".$pc_date_time."',1,0)";
						$id++; $add_comma++;
						$amt+=$yarnamt; 
						$totAmt+=$yarnamt;
					}
				}
				
				if(str_replace("'",'',$$txtConvDtls)!="")//Conv Data
				{
					$convbreakdownArr=explode('##',str_replace("'",'',$$txtConvDtls));
					for($c=0; $c< count($convbreakdownArr); $c++)
					{
						$counter++;
						$convbreakarr=explode(',',$convbreakdownArr[$c]);
						
						$convCons=str_replace("'",'',$$txtfabtotcons);
						$convamt=$convCons*$convbreakarr[1];
						
						if ($add_comma!=0) $data_array .=","; $add_comma=0;
						$data_array .="(".$id.",".$hid_qc_no.",".$hid_bodypart_id.",3,0,0,0,0,0,'".$fabid."','".$convbreakarr[0]."','','".$convCons."',0,0,'".$convbreakarr[1]."','".$convamt."','','',".$hid_cons_rate_dtls_id.",'".$user_id."','".$pc_date_time."',1,0)";
						
						$id++; $add_comma++;
						$amt+=$convamt; 
						$totAmt+=$convamt;
					}
				}
			}
        }
		//die;
		
		if((str_replace("'",'',$txtamount)*1)==0) $txtamount=$amt; else $txtamount=str_replace("'",'',$txtamount)*1;
		if((str_replace("'",'',$txttotamount)*1)==0) $txttotamount=$totAmt; else $txttotamount=str_replace("'",'',$txttotamount)*1;
		
		//echo "10**insert into qc_fab_yarn_conv (".$field_array.") values ".$data_array;die;
		$flag=1;
		$rID=sql_insert("qc_fab_yarn_conv",$field_array,$data_array,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$rIDMst=execute_query("update qc_cons_rate_dtls set fab_cost='".$txtamount."', process_loss=".$txtprocessloss.", totfab_cost='".$txttotamount."' where mst_id =".$hid_qc_no." and id =".$hid_cons_rate_dtls_id." and type=1");
		if($rIDMst==1 && $flag==1) $flag=1; else $flag=0;
		//echo "10**".$rID.'='.$rIDMst.'='.$flag; die;
		if($db_type==0)
        {
            if($flag==1)
            {
                mysql_query("COMMIT");
                echo "0**".str_replace("'", '', $hid_qc_no);
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "6**".str_replace("'", '', $hid_qc_no);
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($flag==1)
            {
                oci_commit($con);
                echo "0**".str_replace("'", '', $hid_qc_no);
            }
            else
            {
                oci_rollback($con);
                echo "6**".str_replace("'", '', $hid_qc_no);
            }
        }
		disconnect($con);
		die;
    }
    else if ($operation==1)   // Update Here
    {
        $con = connect();
        if($db_type==0) mysql_query("BEGIN");
		
		$id=return_next_id( "id", "qc_fab_yarn_conv", 1);
		$field_array="id, qc_no, body_part_id, type, yarn_lib_id, count, composition, percent, yarn_type, fab_id, process, fab_des, cons, exper, tot_cons, rate, value, yarn_break_down, conv_break_down, cons_rate_dtls_id, inserted_by, insert_date, status_active, is_deleted";
		
		$add_comma=0; $data_array=''; $amt=0; $totAmt=0;
        for($i=1; $i<=$numRowFabric; $i++) 
        {
            $hidLibid       ="hidLibid_".$i;
            $txtFabricDtls  ="txtFabricDtls_".$i;
			$txtYarnDtls    ="txtYarnDtls_".$i;
			$txtConvDtls    ="txtConvDtls_".$i;
            $txtfabcons    	="txtfabcons_".$i;
            $txtfabex   	="txtfabex_".$i;
            $txtfabtotcons  ="txtfabtotcons_".$i;
            $txtfabrate     ="txtfabrate_".$i;
            $txtfabamt      ="txtfabamt_".$i;
            $hidupid        ="hidupid_".$i;
			
			if((str_replace("'",'',$$txtfabtotcons)*1)!=0)
			{
				if ($add_comma!=0) $data_array .=","; $add_comma=0;
				$data_array .="(".$id.",".$hid_qc_no.",".$hid_bodypart_id.",1,".$$hidLibid.",0,0,0,0,0,0,".$$txtFabricDtls.",".$$txtfabcons.",".$$txtfabex.",".$$txtfabtotcons.",".$$txtfabrate.",".$$txtfabamt.",".$$txtYarnDtls.",".$$txtConvDtls.",".$hid_cons_rate_dtls_id.",'".$user_id."','".$pc_date_time."',1,0)";
				$fabid=$id;
				$id++; $add_comma++;
				
				if(str_replace("'",'',$$txtYarnDtls)!="")//Yarn Data
				{
					$yarnbreckdown_array=explode('##',str_replace("'",'',$$txtYarnDtls));
					for($y=0; $y< count($yarnbreckdown_array); $y++)
					{
						$counter++;
						$yarnbreckdownarr=explode(',',$yarnbreckdown_array[$y]);
						
						$cons=def_number_format(((str_replace("'",'',$$txtfabtotcons)*$yarnbreckdownarr[4])/100),5,"");
						$yarnamt=$cons*$yarnbreckdownarr[5];
						
						if ($add_comma!=0) $data_array .=","; $add_comma=0;
						$data_array .="(".$id.",".$hid_qc_no.",".$hid_bodypart_id.",2,0,'".$yarnbreckdownarr[0]."','".$yarnbreckdownarr[1]."','".$yarnbreckdownarr[4]."','".$yarnbreckdownarr[3]."','".$fabid."',0,'','".$cons."',0,0,'".$yarnbreckdownarr[5]."','".$yarnamt."','','',".$hid_cons_rate_dtls_id.",'".$user_id."','".$pc_date_time."',1,0)";
						$id++; $add_comma++;
						$amt+=$yarnamt; 
						$totAmt+=$yarnamt;
					}
				}
				
				if(str_replace("'",'',$$txtConvDtls)!="")//Conv Data
				{
					$convbreakdownArr=explode('##',str_replace("'",'',$$txtConvDtls));
					for($c=0; $c< count($convbreakdownArr); $c++)
					{
						$counter++;
						$convbreakarr=explode(',',$convbreakdownArr[$c]);
						
						$convCons=str_replace("'",'',$$txtfabtotcons);
						$convamt=$convCons*$convbreakarr[1];
						
						if ($add_comma!=0) $data_array .=","; $add_comma=0;
						$data_array .="(".$id.",".$hid_qc_no.",".$hid_bodypart_id.",3,0,0,0,0,0,'".$fabid."','".$convbreakarr[0]."','','".$convCons."',0,0,'".$convbreakarr[1]."','".$convamt."','','',".$hid_cons_rate_dtls_id.",'".$user_id."','".$pc_date_time."',1,0)";
						
						$id++; $add_comma++;
						$amt+=$convamt; 
						$totAmt+=$convamt;
					}
				}
			}
        }
		
		if((str_replace("'",'',$txtamount)*1)==0) $txtamount=$amt; else $txtamount=str_replace("'",'',$txtamount)*1;
		if((str_replace("'",'',$txttotamount)*1)==0) $txttotamount=$totAmt; else $txttotamount=str_replace("'",'',$txttotamount)*1;
		
		$flag=1;
		$fycRid=execute_query("delete from qc_fab_yarn_conv where qc_no =".$hid_qc_no." and cons_rate_dtls_id =".$hid_cons_rate_dtls_id." and type in (1,2,3)",1);
		if($fycRid==1 && $flag==1) $flag=1; else  $flag=0;
		$rID=sql_insert("qc_fab_yarn_conv",$field_array,$data_array,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$rIDMst=execute_query("update qc_cons_rate_dtls set fab_cost='".$txtamount."', process_loss=".$txtprocessloss.", totfab_cost='".$txttotamount."' where mst_id =".$hid_qc_no." and id =".$hid_cons_rate_dtls_id." and type=1");
		if($rIDMst==1 && $flag==1) $flag=1; else $flag=0;
		
		//echo "10**".$fycRid.'='.$rID.'='.$flag; die;
		if($db_type==0)
        {
            if($flag==1)
            {
                mysql_query("COMMIT");
                echo "1**".str_replace("'", '', $hid_qc_no);
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "6**".str_replace("'", '', $hid_qc_no);
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($flag==1)
            {
                oci_commit($con);
                echo "1**".str_replace("'", '', $hid_qc_no);
            }
            else
            {
                oci_rollback($con);
                echo "6**".str_replace("'", '', $hid_qc_no);
            }
        }
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
    {
        $con = connect();
        if($db_type==0) mysql_query("BEGIN");
		
		if($db_type==0)
        {
            if($rID==1)
            {
                mysql_query("COMMIT");
                echo "2**".str_replace("'", '', $update_id);
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "6**".str_replace("'", '', $update_id);
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($rID==1)
            {
                oci_commit($con);
                echo "2**".str_replace("'", '', $update_id);
            }
            else
            {
                oci_rollback($con);
                echo "6**".str_replace("'", '', $update_id);
            }
        }
        disconnect($con);
        die;
	}
}

if($action=="populate_confirm_fabric_data")
{
	//echo $data;
	$edata = explode("!!!",$data);
	$qc_no=$edata[0];
	$body_part_id=$edata[1];
	$consRateId=$edata[2];
	$packQty=$edata[3];
	$itemRatio=$edata[4];
	$ratedata=$edata[5];
	$exratedata=explode("~~",$ratedata);
	?>
    <table width="650" cellspacing="0" border="1" class="rpt_table" id="tbl_fab" rules="all">
        <thead>
            <td colspan="8" align="center" bgcolor="#99FFCC"><b><?=$body_part[$body_part_id]; ?></b></td>
        </thead>
        <thead>
            <th width="80">Knitting Weight [Gram]/ PCS</th>
            <th width="80">Knitting Weight [Lbs]/ PCS</th>
            <th width="80">Knitting Weight [Lbs]/ Dzn</th>
            <th width="70">Size Grading %</th>
            <th width="70">Wastage %</th>
            <th width="80">Actual Cons [Kg]/PCS</th>
            <th width="80">Actual Cons [Lbs]/PCS</th>
            <th>Actual Cons [Lbs]/DZN</th>
        </thead>
        <tbody id="fabtbody">
            <tr id="fabtr">
                <td><input style="width:68px;" type="text" class="text_boxes_numeric" name="txtknitgrampcs" id="txtknitgrampcs" value="<?=$exratedata[0]; ?>" placeholder="Write" onBlur="fnc_cons_amt();" /></td>
                <td><input style="width:68px;" type="text" class="text_boxes_numeric" name="txtknitlbspcs" id="txtknitlbspcs" value="<?=$exratedata[1]; ?>" placeholder="Display" readonly /></td>
                <td><input style="width:68px;" type="text" class="text_boxes_numeric" name="txtknitlbsdzn" id="txtknitlbsdzn" value="<?=$exratedata[2]; ?>" placeholder="Display" readonly /></td>
                <td><input style="width:58px;" type="text" class="text_boxes_numeric" name="txtsizegrading" id="txtsizegrading" value="<?=$exratedata[3]; ?>" placeholder="Write" onBlur="fnc_cons_amt();" /></td>
                <td><input style="width:58px;" type="text" class="text_boxes_numeric" name="txtwastage" id="txtwastage" value="<?=$exratedata[4]; ?>" onBlur="fnc_cons_amt();" placeholder="Write" /></td>
                
                <td><input style="width:68px;" type="text" class="text_boxes_numeric" name="txtactualconskgpcs" id="txtactualconskgpcs" readonly placeholder="Display" /></td>
                <td><input style="width:68px;" type="text" class="text_boxes_numeric" name="txtactualconslbspcs" id="txtactualconslbspcs"  placeholder="Display" readonly value="<?=$exratedata[6]; ?>" /></td>
                <td><input style="width:68px;" type="text" class="text_boxes_numeric" name="txtactualconslbsdzn" id="txtactualconslbsdzn" placeholder="Dispaly" readonly value="<?=$exratedata[7]; ?>" /></td>
           </tr>
        </tbody>
        <tfoot>
            <tr>
            	<td colspan="8" align="center"><input type="button" class="formbutton" style="width:100px" value="Close" onClick="js_set_value();"/></td>
            </tr>
        </tfoot>
    </table>
    </table>
    <?
	unset($sqlFabData);
}

if($action=="populate_confirm_acc_data")
{
	//echo $data;
	//$adata=$qc_no.'!!!'.$itemRatio.'!!!'.$acctext.'!!!'.$accuom.'!!!'.$accRateData;
	$edata = explode("!!!",$data);
	$qc_no=$edata[0];
	$itemRatio=$edata[1];
	$acctext=$edata[2];
	$accuom=$edata[3];
	$ratedata=$edata[4];
	
	?>
    <table width="600" cellspacing="0" border="1" class="rpt_table" id="tbl_acc" rules="all">
        <thead>
            <td colspan="11" align="center" bgcolor="#99FFCC"><b><?=$acctext.', '.$unit_of_measurement[$accuom]; ?></b></td>
        </thead>
        <thead>
            <th width="60">Item Qty/Pack</th>
            <th width="80">Color/ Option</th>
            <th width="50">Color Ratio</th>
            <th width="50">Cons/DZN</th>
            <th width="50">Cons/Pack</th>
            <th width="50">Ex %</th>
            <th width="50">Tot. Cons [Pack]</th>
            <th width="50">Rate</th>
            <th width="50">Value [Pack]</th>
            <th>&nbsp;</th>
        </thead>
        <tbody id="fabtbody">
			<?
            $i=1;
			if($ratedata!="")
			{
				$exrdata=explode("@@!",$ratedata);
				foreach($exrdata as $rdata)
				{
					$xrdata=explode("_",$rdata);
					//consData+=$('#txtitemqty_'+i).val()+'_'+$('#hidupid_'+i).val()+'_'+$('#txtcoloroption_'+i).val()+'_'+$('#txtcolorratio_'+i).val()+'_'+$('#txtcolorconsdzn_'+i).val()+'_'+$('#txtcolorconspack_'+i).val()+'_'+$('#txtcolorexper_'+i).val()+'_'+$('#txtcolortotccons_'+i).val()+'_'+$('#txtcolorrate_'+i).val()+'_'+$('#txtcoloramt_'+i).val();
					?>
					<tr id="acctr_<?=$i; ?>">
						<td><input style="width:48px;" type="text" class="text_boxes_numeric" name="txtitemqty_<?=$i; ?>" id="txtitemqty_<?=$i; ?>" value="<?=$xrdata[0]; ?>" readonly />
							<input type="hidden" class="text_boxes" name="hidupid_<?=$i; ?>" id="hidupid_<?=$i; ?>" value="<?=$xrdata[1]; ?>"/>
						</td>
						<td><input style="width:68px;" type="text" class="text_boxes" name="txtcoloroption_<?=$i; ?>" id="txtcoloroption_<?=$i; ?>" value="<?=$xrdata[2]; ?>" /></td>
						<td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtcolorratio_<?=$i; ?>" id="txtcolorratio_<?=$i; ?>" value="<?=$xrdata[3]; ?>" onChange="fnc_cons_amt('<?=$i.'_1'; ?>');" /></td>
						<td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtcolorconsdzn_<?=$i; ?>" id="txtcolorconsdzn_<?=$i; ?>" value="<?=$xrdata[4]; ?>" onChange="fnc_cons_amt('<?=$i.'_1'; ?>');" /></td>
						<td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtcolorconspack_<?=$i; ?>" id="txtcolorconspack_<?=$i; ?>" value="<?=$xrdata[5]; ?>" onChange="fnc_cons_amt('<?=$i.'_2'; ?>');" /></td>
                        <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtcolorexper_<?=$i; ?>" id="txtcolorexper_<?=$i; ?>" onChange="fnc_cons_amt('<?=$i.'_1'; ?>');" value="<?=$xrdata[6]; ?>" /></td>
                <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtcolortotccons_<?=$i; ?>" id="txtcolortotccons_<?=$i; ?>" onChange="fnc_cons_amt('<?=$i.'_1'; ?>');" value="<?=$xrdata[7]; ?>" readonly /></td>
						<td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtcolorrate_<?=$i; ?>" id="txtcolorrate_<?=$i; ?>" onChange="fnc_cons_amt('<?=$i.'_1'; ?>');" value="<?=$xrdata[8]; ?>" /></td>
						<td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtcoloramt_<?=$i; ?>" id="txtcoloramt_<?=$i; ?>" placeholder="Display" readonly value="<?=$xrdata[9]; ?>" /></td>
						<td>
							<input type="button" id="increasewash_1" name="increasewash_1" style="width:30px" class="formbutton" value="+" onClick="append_row('<?=$i.'_1'; ?>');" />
							<input type="button" id="decreasewash_1" name="decreasewash_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fnc_remove_row(<?=$i; ?>);" />
						</td>
					</tr>
					<?
					$i++;
					$consTotDzn+=$xrdata[4];
					$consTotPack+=$xrdata[5];
					$consTotTot+=$xrdata[7];
					$amtTot+=$xrdata[9];
				}
				$rowi=$i-1;
				$consTotDzn=$consTotDzn/$rowi;
				$avgRatePack=$amtTot/$consTotTot;
				$avgExper=($consTotTot-$consTotPack)/$consTotPack*100;
			}
            ?>
            <tr id="acctr_<?=$i; ?>">
                <td><input style="width:48px;" type="text" class="text_boxes_numeric" name="txtitemqty_<?=$i; ?>" id="txtitemqty_<?=$i; ?>" value="<?=$itemRatio; ?>" readonly />
                    <input type="hidden" class="text_boxes" name="hidupid_<?=$i; ?>" id="hidupid_<?=$i; ?>" value=""/>
                </td>
                <td><input style="width:68px;" type="text" class="text_boxes" name="txtcoloroption_<?=$i; ?>" id="txtcoloroption_<?=$i; ?>" value="" /></td>
                <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtcolorratio_<?=$i; ?>" id="txtcolorratio_<?=$i; ?>" value="" onChange="fnc_cons_amt('<?=$i.'_1'; ?>');" /></td>
                <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtcolorconsdzn_<?=$i; ?>" id="txtcolorconsdzn_<?=$i; ?>" value="" onChange="fnc_cons_amt('<?=$i.'_1'; ?>');"/></td>
                <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtcolorconspack_<?=$i; ?>" id="txtcolorconspack_<?=$i; ?>" value="" onChange="fnc_cons_amt('<?=$i.'_2'; ?>');"  /></td>
                
                <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtcolorexper_<?=$i; ?>" id="txtcolorexper_<?=$i; ?>" onChange="fnc_cons_amt('<?=$i.'_1'; ?>');" value="" /></td>
                <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtcolortotccons_<?=$i; ?>" id="txtcolortotccons_<?=$i; ?>" onChange="fnc_cons_amt('<?=$i.'_1'; ?>');" value="" readonly /></td>
                
                
                <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtcolorrate_<?=$i; ?>" id="txtcolorrate_<?=$i; ?>" onChange="fnc_cons_amt('<?=$i.'_1'; ?>');" value="" /></td>
                <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtcoloramt_<?=$i; ?>" id="txtcoloramt_<?=$i; ?>" placeholder="Display" readonly value="" /></td>
                <td>
                    <input type="button" id="increasewash_1" name="increasewash_1" style="width:30px" class="formbutton" value="+" onClick="append_row('<?=$i.'_1'; ?>');" />
                    <input type="button" id="decreasewash_1" name="decreasewash_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fnc_remove_row(<?=$i; ?>);" />
                </td>
           </tr>
        </tbody>
        <tfoot>
            <tr bgcolor="#CCCCAA">
                <td align="right" colspan="3">Sum=</td>
                <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txttotcons_dzn" id="txttotcons_dzn" value="<?=$consTotDzn; ?>" disabled /></td>
                <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txttotcons_pack" id="txttotcons_pack" value="<?=$consTotPack; ?>" disabled /></td>
                <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txttotexper" id="txttotexper" value="<?=$avgExper; ?>" disabled /></td>
                <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txttotcons" id="txttotcons" value="<?=$consTotTot; ?>" disabled /></td>
                <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txt_avg_rate" id="txt_avg_rate" value="<?=$avgRatePack; ?>" disabled /></td>
                <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txt_amt_pack" id="txt_amt_pack" value="<?=$amtTot; ?>" disabled /></td>
                <td><input style="width:38px;" type="hidden" class="text_boxes" name="txtdatastr" id="txtdatastr" value="<?=$ratedata; ?>" />&nbsp;</td>
            </tr>
            <tr>
            	<td colspan="10" align="center"><input type="button" class="formbutton" style="width:100px" value="Close" onClick="js_set_value();"/></td>
            </tr>
        </tfoot>
    </table>
    </table>
    <?
	unset($sqlFabData);
}

if($action=="save_update_delete_yarn")
{
	$process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    //echo "10**".$operation; die;
    if ($operation==0)  // Insert Here
    {
        $con = connect();
        if($db_type==0) mysql_query("BEGIN");

        $id=return_next_id( "id", "qc_fab_yarn_conv", 1);
		$field_array="id, qc_no, body_part_id, type, fab_id, count, composition, percent, yarn_type, cons, rate, value, cons_rate_dtls_id, inserted_by, insert_date, status_active, is_deleted";
		
		$add_comma=0; $data_array=''; 
        for($i=1; $i<=$numRowYarn; $i++) 
        {
            $cbocount       ="cbocount_".$i;
            $hidfabid  		="hidfabid_".$i;
            $cbocompone    	="cbocompone_".$i;
            $txtpercentone  ="txtpercentone_".$i;
            $cbotype 		="cbotype_".$i;
            $txtyarncons    ="txtyarncons_".$i;
            $txtyarnrate    ="txtyarnrate_".$i;
            $txtyarnamt     ="txtyarnamt_".$i;
			$hidyarnupid    ="hidyarnupid_".$i;
			
			if((str_replace("'",'',$$txtyarncons)*1)!=0)
			{
				if ($add_comma!=0) $data_array .=","; $add_comma=0;
				$data_array .="(".$id.",".$hid_qc_no.",".$hid_bodypart_id.",2,".$$hidfabid.",".$$cbocount.",".$$cbocompone.",".$$txtpercentone.",".$$cbotype.",".$$txtyarncons.",".$$txtyarnrate.",".$$txtyarnamt.",".$hid_cons_rate_dtls_id.",'".$user_id."','".$pc_date_time."',1,0)";
				$id++; $add_comma++;
			}
        }
		
		//echo "10**insert into qc_fab_yarn_conv (".$field_array.") values ".$data_array;die;
		$flag=1;
		$rID=sql_insert("qc_fab_yarn_conv",$field_array,$data_array,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$rIDMst=execute_query("update qc_cons_rate_dtls set fab_cost=".$txtamount.", process_loss=".$txtprocessloss.", totfab_cost=".$txttotamount." where mst_id =".$hid_qc_no." and id =".$hid_cons_rate_dtls_id." and type=1");
		if($rIDMst==1 && $flag==1) $flag=1; else $flag=0;
		
		if($db_type==0)
        {
            if($rID==1)
            {
                mysql_query("COMMIT");
                echo "0**".str_replace("'", '', $hid_qc_no);
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "6**".str_replace("'", '', $hid_qc_no);
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($rID==1)
            {
                oci_commit($con);
                echo "0**".str_replace("'", '', $hid_qc_no);
            }
            else
            {
                oci_rollback($con);
                echo "6**".str_replace("'", '', $hid_qc_no);
            }
        }
		disconnect($con);
		die;
    }
    else if ($operation==1)   // Update Here
    {
        $con = connect();
        if($db_type==0) mysql_query("BEGIN");
		
		$id=return_next_id( "id", "qc_fab_yarn_conv", 1);
		$field_array="id, qc_no, body_part_id, type, fab_id, count, composition, percent, yarn_type, cons, rate, value, cons_rate_dtls_id, inserted_by, insert_date, status_active, is_deleted";
		
		$add_comma=0; $data_array=''; 
        for($i=1; $i<=$numRowYarn; $i++) 
        {
            $cbocount       ="cbocount_".$i;
            $hidfabid  		="hidfabid_".$i;
            $cbocompone    	="cbocompone_".$i;
            $txtpercentone  ="txtpercentone_".$i;
            $cbotype 		="cbotype_".$i;
            $txtyarncons    ="txtyarncons_".$i;
            $txtyarnrate    ="txtyarnrate_".$i;
            $txtyarnamt     ="txtyarnamt_".$i;
			$hidyarnupid    ="hidyarnupid_".$i;
			
			if((str_replace("'",'',$$txtyarncons)*1)!=0)
			{
				if ($add_comma!=0) $data_array .=","; $add_comma=0;
				$data_array .="(".$id.",".$hid_qc_no.",".$hid_bodypart_id.",2,".$$hidfabid.",".$$cbocount.",".$$cbocompone.",".$$txtpercentone.",".$$cbotype.",".$$txtyarncons.",".$$txtyarnrate.",".$$txtyarnamt.",".$hid_cons_rate_dtls_id.",'".$user_id."','".$pc_date_time."',1,0)";
				$id++; $add_comma++;
			}
        }
		$flag=1;
		$fycRid=execute_query( "delete from qc_fab_yarn_conv where qc_no =".$hid_qc_no." and cons_rate_dtls_id =".$hid_cons_rate_dtls_id." and type=2",1);
		if($fycRid==1 && $flag==1) $flag=1; else  $flag=0;
		$rID=sql_insert("qc_fab_yarn_conv",$field_array,$data_array,0);
		if($rID==1 && $flag==1) $flag=1; else  $flag=0;
		
		$rIDMst=execute_query("update qc_cons_rate_dtls set fab_cost=".$txtamount.", process_loss=".$txtprocessloss.", totfab_cost=".$txttotamount." where mst_id =".$hid_qc_no." and id =".$hid_cons_rate_dtls_id." and type=1");
		if($rIDMst==1 && $flag==1) $flag=1; else $flag=0;
		
		if($db_type==0)
        {
            if($flag==1)
            {
                mysql_query("COMMIT");
                echo "1**".str_replace("'", '', $hid_qc_no);
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "6**".str_replace("'", '', $hid_qc_no);
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($flag==1)
            {
                oci_commit($con);
                echo "1**".str_replace("'", '', $hid_qc_no);
            }
            else
            {
                oci_rollback($con);
                echo "6**".str_replace("'", '', $hid_qc_no);
            }
        }
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
    {
        $con = connect();
        if($db_type==0) mysql_query("BEGIN");
		
		if($db_type==0)
        {
            if($rID==1)
            {
                mysql_query("COMMIT");
                echo "2**".str_replace("'", '', $update_id);
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "6**".str_replace("'", '', $update_id);
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($rID==1)
            {
                oci_commit($con);
                echo "2**".str_replace("'", '', $update_id);
            }
            else
            {
                oci_rollback($con);
                echo "6**".str_replace("'", '', $update_id);
            }
        }
        disconnect($con);
        die;
	}
}

if($action=="save_update_delete_conv")
{
	$process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    //echo "10**".$operation; die;
    if ($operation==0)  // Insert Here
    {
        $con = connect();
        if($db_type==0) mysql_query("BEGIN");

        $id=return_next_id( "id", "qc_fab_yarn_conv", 1);
		$field_array="id, qc_no, body_part_id, type, fab_id, process, cons, rate, value, cons_rate_dtls_id, inserted_by, insert_date, status_active, is_deleted";
		
		$add_comma=0; $data_array=''; 
        for($i=1; $i<=$numRowConv; $i++) 
        {
            $cbofab       	="cbofab_".$i;
            $cboprocess  	="cboprocess_".$i;
            $txtconvcons    ="txtconvcons_".$i;
            $txtconvrate  	="txtconvrate_".$i;
            $txtconvamt 	="txtconvamt_".$i;
            $hidconvupid    ="hidconvupid_".$i;
			
			if((str_replace("'",'',$$txtconvcons)*1)!=0)
			{
				if ($add_comma!=0) $data_array .=","; $add_comma=0;
				$data_array .="(".$id.",".$hid_qc_no.",".$hid_bodypart_id.",3,".$$cbofab.",".$$cboprocess.",".$$txtconvcons.",".$$txtconvrate.",".$$txtconvamt.",".$hid_cons_rate_dtls_id.",'".$user_id."','".$pc_date_time."',1,0)";
				$id++; $add_comma++;
			}
        }
		
		//echo "10**insert into qc_fab_yarn_conv (".$field_array.") values ".$data_array;die;
		$flag=1;
		$rID=sql_insert("qc_fab_yarn_conv",$field_array,$data_array,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$rIDMst=execute_query("update qc_cons_rate_dtls set fab_cost=".$txtamount.", process_loss=".$txtprocessloss.", totfab_cost=".$txttotamount." where mst_id =".$hid_qc_no." and id =".$hid_cons_rate_dtls_id." and type=1");
		if($rIDMst==1 && $flag==1) $flag=1; else $flag=0;
		
		if($db_type==0)
        {
            if($rID==1)
            {
                mysql_query("COMMIT");
                echo "0**".str_replace("'", '', $hid_qc_no);
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "6**".str_replace("'", '', $hid_qc_no);
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($rID==1)
            {
                oci_commit($con);
                echo "0**".str_replace("'", '', $hid_qc_no);
            }
            else
            {
                oci_rollback($con);
                echo "6**".str_replace("'", '', $hid_qc_no);
            }
        }
		disconnect($con);
		die;
    }
    else if ($operation==1)   // Update Here
    {
        $con = connect();
        if($db_type==0) mysql_query("BEGIN");
		
		$id=return_next_id( "id", "qc_fab_yarn_conv", 1);
		$field_array="id, qc_no, body_part_id, type, fab_id, process, cons, rate, value, cons_rate_dtls_id, inserted_by, insert_date, status_active, is_deleted";
		
		$add_comma=0; $data_array=''; 
        for($i=1; $i<=$numRowConv; $i++) 
        {
            $cbofab       	="cbofab_".$i;
            $cboprocess  	="cboprocess_".$i;
            $txtconvcons    ="txtconvcons_".$i;
            $txtconvrate  	="txtconvrate_".$i;
            $txtconvamt 	="txtconvamt_".$i;
            $hidconvupid    ="hidconvupid_".$i;
			
			if((str_replace("'",'',$$txtconvcons)*1)!=0)
			{
				if ($add_comma!=0) $data_array .=","; $add_comma=0;
				$data_array .="(".$id.",".$hid_qc_no.",".$hid_bodypart_id.",3,".$$cbofab.",".$$cboprocess.",".$$txtconvcons.",".$$txtconvrate.",".$$txtconvamt.",".$hid_cons_rate_dtls_id.",'".$user_id."','".$pc_date_time."',1,0)";
				$id++; $add_comma++;
			}
        }
		$flag=1;
		$fycRid=execute_query( "delete from qc_fab_yarn_conv where qc_no =".$hid_qc_no." and cons_rate_dtls_id =".$hid_cons_rate_dtls_id." and type=3",1);
		if($fycRid==1 && $flag==1) $flag=1; else  $flag=0;
		$rID=sql_insert("qc_fab_yarn_conv",$field_array,$data_array,0);
		if($rID==1 && $flag==1) $flag=1; else  $flag=0;
		
		$rIDMst=execute_query("update qc_cons_rate_dtls set fab_cost=".$txtamount.", process_loss=".$txtprocessloss.", totfab_cost=".$txttotamount." where mst_id =".$hid_qc_no." and id =".$hid_cons_rate_dtls_id." and type=1");
		if($rIDMst==1 && $flag==1) $flag=1; else $flag=0;
		
		if($db_type==0)
        {
            if($flag==1)
            {
                mysql_query("COMMIT");
                echo "1**".str_replace("'", '', $hid_qc_no);
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "6**".str_replace("'", '', $hid_qc_no);
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($flag==1)
            {
                oci_commit($con);
                echo "1**".str_replace("'", '', $hid_qc_no);
            }
            else
            {
                oci_rollback($con);
                echo "6**".str_replace("'", '', $hid_qc_no);
            }
        }
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
    {
        $con = connect();
        if($db_type==0) mysql_query("BEGIN");
		
		if($db_type==0)
        {
            if($rID==1)
            {
                mysql_query("COMMIT");
                echo "2**".str_replace("'", '', $update_id);
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "6**".str_replace("'", '', $update_id);
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($rID==1)
            {
                oci_commit($con);
                echo "2**".str_replace("'", '', $update_id);
            }
            else
            {
                oci_rollback($con);
                echo "6**".str_replace("'", '', $update_id);
            }
        }
        disconnect($con);
        die;
	}
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
				http.open("POST","short_quotation_v5_controller.php",true);
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
					show_list_view('','stage_data_list','stage_data_div','short_quotation_v5_controller','');
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
    <script>show_list_view('','stage_data_list','stage_data_div','short_quotation_v5_controller','');</script>          
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
	echo  create_list_view ( "list_view", "Stage Name", "200","200","150",1, "select tuid, stage_name from lib_stage_name where status_active=1 and is_deleted=0 order by tuid desc", "get_php_form_data", "tuid","'load_php_data_to_form_satge'", 1, "0", $arr, "stage_name","short_quotation_v5_controller", 'setFilterGrid("list_view",-1);','0' );
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
	
	$color_library=return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$size_library=return_library_array("select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	
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
		$msg="Duplicate Styel Ref, Buyer, Season.";
		if($type==1)
		{
			if (is_duplicate_field( "cost_sheet_no", "qc_mst", "style_ref='$dup_styleRef' and season_id='$dup_season_id' and buyer_id='$dup_buyer_id' and status_active=1 and is_deleted=0" ) == 1)
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
		$qcno=$_SESSION['logic_erp']['user_id'].'000'.$mst_id;
		$txt_option_remarks=str_replace($str_rep,' ',str_replace("'","",$txt_option_remarks));
		$txt_costing_remarks=str_replace($str_rep,' ',str_replace("'","",$txt_costing_remarks));
		$txt_meeting_remarks=str_replace($str_rep,' ',str_replace("'","",$txt_meeting_remarks));
		//echo $revise_no."=select max(revise_no) as revise_no from qc_mst where cost_sheet_no='$txt_costSheetNo'"; die;
		$field_array_mst="id, company_id, location_id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, inquery_id, style_ref, buyer_id, season_id, season_year, brand_id, style_des, prod_dept, prod_code, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, margin, stage_id, costing_date, pre_cost_sheet_id, revise_no, option_id, buyer_remarks, option_remarks, article, qc_no, entry_form, fab_process_loss_method, acc_process_loss_method, inserted_by, insert_date, status_active, is_deleted";
		
		$data_array_mst="(".$mst_id.", ".$cbo_company_id.", ".$cbo_location_id.", ".$mst_id.", '".$autoCostSheetNo."', ".$cbo_temp_id.", ".$txt_temp_id.", ".$txt_inquery_id.", '".strtoupper(str_replace("'","",$txt_styleRef))."', ".$cbo_buyer_id.", ".$cbo_season_id.", ".$cbo_season_year.", ".$cbo_brand.", '".strtoupper(str_replace("'","",$txt_styleDesc))."', ".$cbo_product_department.", ".$txt_product_code.", ".$cbo_subDept_id.", ".$txt_delivery_date.", ".$txt_exchangeRate.", ".$txt_offerQty.", ".$txt_quotedPrice.", ".$txt_tgtPrice.", ".$txtmarign.", ".$cbo_stage_id.", ".$txt_costingDate.", '".$update_id."', '".$revise_no."', '".$option_id."', '".strtoupper($txt_costing_remarks)."', '".strtoupper($txt_option_remarks)."', ".$txt_article.", '".$qcno."', 552, ".$txt_fab_process_loss_method.", ".$txt_acc_process_loss_method.", ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."', 1, 0)";
		
		//echo $data_array; die;
		//$template_id=return_next_id("id", "qc_template", 1);
		//$template_id = return_id( $txt_temp_id, $template_library, "qc_template", "id,item_id");
		$temp_id=explode(",",str_replace("'","",$txt_temp_id));
		
		$cons_rate_id=return_next_id("id", "qc_cons_rate_dtls", 1);
		$field_array_consrate="id, mst_id, item_id, type, particular_type_id, nb_cons, infant_cons, toddler_cons, bigger_cons, bigbigger_cons, rate, value, fab_id, description, body_part_id, supp_ref, remarks, uom, gsmweight, inserted_by, insert_date, status_active, is_deleted, use_for";
		
		//type=1 fab; particular_type_id= bodyPartid
		//type=2 sp; particular_type_id=Special Operation Name
		//type=3 wash; particular_type_id=wash type
		//type=4 acc;  particular_type_id=item_group
		
		$add_comma_cons_rate=0; $data_array_cons_rate="";
		$itemConsRateData='txtfabricData_0';
		$ex_itemConsRateData=array_filter(explode('#^',str_replace("'","",$$itemConsRateData)));
		foreach($ex_itemConsRateData as $item_wise_consRateData)
		{
			if($item_wise_consRateData!="")
			{
				//echo $item_wise_consRateData.'==';
				$ex_itemConsRate=explode('~!~',$item_wise_consRateData);
				$fabDesc="";
				$fabbodypartid=$fabid=$gsm=$fabusefor=$fabuom=$fabNbCons=$fabInfantCons=$fabToddlerCons=$fabBiggerCons=$fabBigBiggerCons=$fabRate=$fabAmt=$fabupid=$item_id=0;
				
				$fabbodypartid=$ex_itemConsRate[1];
				$fabDesc=$ex_itemConsRate[2];
				$fabid=$ex_itemConsRate[3];
				$gsm=$ex_itemConsRate[4];
				$fabusefor=$ex_itemConsRate[5];
				$fabuom=$ex_itemConsRate[6];
				
				$fabNbCons=$ex_itemConsRate[7];
				$fabInfantCons=$ex_itemConsRate[8];
				$fabToddlerCons=$ex_itemConsRate[9];
				$fabBiggerCons=$ex_itemConsRate[10];
				$fabBigBiggerCons=$ex_itemConsRate[11];
				$fabRate=$ex_itemConsRate[12];
				$fabAmt=$ex_itemConsRate[13];
				$fabupid=$ex_itemConsRate[14];
				$item_id=trim($ex_itemConsRate[15]);
				
				if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
				$data_array_cons_rate .="(".$cons_rate_id.",".$qcno.",'".$item_id."',1,0,'".$fabNbCons."','".$fabInfantCons."','".$fabToddlerCons."','".$fabBiggerCons."','".$fabBigBiggerCons."','".$fabRate."','".$fabAmt."','".$fabid."','".$fabDesc."','".$fabbodypartid."','','','".$fabuom."','".$gsm."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,'".$fabusefor."')";
				
				$cons_rate_id=$cons_rate_id+1;
				$add_comma_cons_rate++;
			}
		}
			
		$specialData='txtspData_0';
		$ex_specialData=array_filter(explode('##',str_replace("'","",$$specialData)));
		foreach($ex_specialData as $item_wise_specialData)
		{
			$ex_itemSpConsRate=explode('_',$item_wise_specialData);
			
			$speciaOperation_id=$speciaTypeId=$spbodypartid=$spNbCons=$spInfantCons=$spToddlerCons=$spBiggerCons=$spBigBiggerCons=$spRate=$spValue=$spRemarks=0;
			
			$speciaOperation_id=$ex_itemSpConsRate[0];
			$speciaTypeId=$ex_itemSpConsRate[1];
			$spbodypartid=$ex_itemSpConsRate[3];
			
			$spNbCons=$ex_itemSpConsRate[4];
			$spInfantCons=$ex_itemSpConsRate[5];
			$spToddlerCons=$ex_itemSpConsRate[6];
			$spBiggerCons=$ex_itemSpConsRate[7];
			$spBigBiggerCons=$ex_itemSpConsRate[8];
			
			$spRate=$ex_itemSpConsRate[9];
			$spValue=$ex_itemSpConsRate[10];
			$spRemarks=$ex_itemSpConsRate[11];
			
			
			if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
			$data_array_cons_rate .="(".$cons_rate_id.",".$qcno.",'0',2,'".$speciaOperation_id."','".$spNbCons."','".$spInfantCons."','".$spToddlerCons."','".$spBiggerCons."','".$spBigBiggerCons."','".$spRate."','".$spValue."','".$speciaTypeId."','','".$spbodypartid."','','".$spRemarks."',0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,'')";
			
			$cons_rate_id=$cons_rate_id+1;
			$add_comma_cons_rate++;
		}
		
		
		$washData='txtwashData_0';
		$ex_washData=array_filter(explode('##',str_replace("'","",$$washData)));
		foreach($ex_washData as $item_wise_washData)
		{
			$ex_itemwashConsRate=explode('_',$item_wise_washData);
			$washTypeId=$wbodypartid=$wNbCons=$wInfantCons=$wToddlerCons=$wBiggerCons=$wBigBiggerCons=$wRate=$wValue=$wRemarks=0;
			
			$washTypeId=$ex_itemwashConsRate[1];
			$wbodypartid=$ex_itemwashConsRate[3];
			
			$wNbCons=$ex_itemwashConsRate[4];
			$wInfantCons=$ex_itemwashConsRate[5];
			$wToddlerCons=$ex_itemwashConsRate[6];
			$wBiggerCons=$ex_itemwashConsRate[7];
			$wBigBiggerCons=$ex_itemwashConsRate[8];
			
			$wRate=$ex_itemwashConsRate[9];
			$wValue=$ex_itemwashConsRate[10];
			$wRemarks=$ex_itemwashConsRate[11];
			
			if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
			$data_array_cons_rate .="(".$cons_rate_id.",".$qcno.",'0',3,'".$washTypeId."','".$wNbCons."','".$wInfantCons."','".$wToddlerCons."','".$wBiggerCons."','".$wBigBiggerCons."','".$wRate."','".$wValue."',0,'','".$wbodypartid."','','".$wRemarks."',0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,'')";
			
			$cons_rate_id=$cons_rate_id+1;
			$add_comma_cons_rate++;
		}
		
		$itemAccData='txtaccData_0';
		$ex_itemAccData=array_filter(explode('#^',str_replace("'","",$$itemAccData)));
		foreach($ex_itemAccData as $item_wise_itemAccData)
		{
			$ex_itemAcConsRate=explode('~!~',$item_wise_itemAccData);
			
			$accessories_id=$accDescription=$accBandRef=$accUseFor=$accNbCons=$accInfantCons=$accToddlerCons=$accBiggerCons=$accBigBiggerCons=$acRate=$acVal=$acconsUom=$acConsCal=$acRateData=0;
			
			$accessories_id=$ex_itemAcConsRate[1];
			$accDescription=$ex_itemAcConsRate[2];
			$accBandRef=$ex_itemAcConsRate[3];
			$acconsUom=$ex_itemAcConsRate[4];
			
			$accNbCons=$ex_itemAcConsRate[5];
			$accInfantCons=$ex_itemAcConsRate[6];
			$accToddlerCons=$ex_itemAcConsRate[7];
			$accBiggerCons=$ex_itemAcConsRate[8];
			$accBigBiggerCons=$ex_itemAcConsRate[9];
			$acRate=$ex_itemAcConsRate[10];
			$acVal=$ex_itemAcConsRate[11];
			
			if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
			$data_array_cons_rate .="(".$cons_rate_id.",".$qcno.",'0',4,'".$accessories_id."','".$accNbCons."','".$accInfantCons."','".$accToddlerCons."','".$accBiggerCons."','".$accBigBiggerCons."','".$acRate."','".$acVal."',0,'".$accDescription."',0,'".$accBandRef."','','".$acconsUom."',0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,'')";
			
			$cons_rate_id=$cons_rate_id+1;
			$add_comma_cons_rate++;
		}
		
		/*echo "10**";
		echo $data_array_cons_rate; 
		die;*/
		$tc_sum_id=return_next_id("id", "qc_tot_cost_summary", 1);
		$field_array_tot_cost="id, mst_id, buyer_agent_id, location_id, no_of_pack, is_cm_calculative, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_wash_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, commercial_per, commercial_cost, operating_exp, inserted_by, insert_date, status_active, is_deleted";
		
		$ex_tot_cost_data=explode('_',$data_tot_cost_summ);
		
		$cbo_buyer_agent=trim($ex_tot_cost_data[0]);
		$cbo_agent_location=trim($ex_tot_cost_data[1]);
		$txt_noOfPack=trim($ex_tot_cost_data[2]);
		$cmPop=trim($ex_tot_cost_data[3]);
		
		$txt_commPer=trim($ex_tot_cost_data[4]);
		$totFab_td=trim($ex_tot_cost_data[5]);
		$totSpc_td=trim($ex_tot_cost_data[6]);
		$totWash_td=trim($ex_tot_cost_data[7]);
		$totAcc_td=trim($ex_tot_cost_data[8]);
		$totCm_td=trim($ex_tot_cost_data[9]);
		$totFriCst_td=trim($ex_tot_cost_data[10]);
		$totLbTstCst_td=trim($ex_tot_cost_data[11]);
		$totOtherCst_td=trim($ex_tot_cost_data[12]);
		$totCommCst_td=trim($ex_tot_cost_data[13]);
		$totCost_td=trim($ex_tot_cost_data[14]);
		$totalFob_td=trim($ex_tot_cost_data[15]);
		$totCommlCst_td=trim($ex_tot_cost_data[16]);
		$txt_commlPer=trim($ex_tot_cost_data[17]);
		$txt_optexp=trim($ex_tot_cost_data[18]);
		
		$data_array_tot_cost="(".$tc_sum_id.",".$qcno.",'".$cbo_buyer_agent."','".$cbo_agent_location."','".$txt_noOfPack."','".$cmPop."','".$txt_commPer."','".$totFab_td."','".$totSpc_td."','".$totWash_td."','".$totAcc_td."','".$totCm_td."','".$totFriCst_td."','".$totLbTstCst_td."','".$totOtherCst_td."','".$totCommCst_td."','".$totCost_td."','".$totalFob_td."','".$txt_commlPer."','".$totCommlCst_td."','".$txt_optexp."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		//echo $data_array_tot_cost; die;
		$field_array_item_tot=" id, mst_id, item_id, fabric_cost, sp_operation_cost, wash_cost, accessories_cost, cpm, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, other_cost, commission_cost, fob_pcs, commercial_cost, operating_exp, inserted_by, insert_date, status_active, is_deleted";
		$item_cost_id=return_next_id("id", "qc_item_cost_summary", 1);
		$ex_item_wise_tot_data=array_filter(explode('__',str_replace("'","",$item_wise_tot_data)));
		$add_comma_item_tot=0; $data_array_item_tot="";
		foreach($ex_item_wise_tot_data as $size_wise_tot)
		{
			$fab_td=$spOpe_td=$wash_td=$acc_td=$txtCpm=$txtSmv=$txtEff=$txtCmCost=$txtFriCost=$txtLtstCost=$txtOtherCost=$txtCommCost=$fobT_td=$fobPcs_td=$txtCommlCost=$txtoptexp=$sizeitem_id=0;
			
			$ex_size_tot=explode('_',$size_wise_tot);
			$fab_td=trim($ex_size_tot[0]);
			$spOpe_td=trim($ex_size_tot[1]);
			$wash_td=trim($ex_size_tot[2]);
			$acc_td=trim($ex_size_tot[3]);
			$txtCpm=trim($ex_size_tot[4]);
			$txtSmv=trim($ex_size_tot[5]);
			$txtEff=trim($ex_size_tot[6]);
			$txtCmCost=trim($ex_size_tot[7]);
			$txtFriCost=trim($ex_size_tot[8]);
			$txtLtstCost=trim($ex_size_tot[9]);
			$txtOtherCost=trim($ex_size_tot[10]);
			$txtCommCost=trim($ex_size_tot[11]);
			$fobT_td=trim($ex_size_tot[12]);
			$fobPcs_td=trim($ex_size_tot[13]);
			$txtCommlCost=trim($ex_size_tot[14]);
			$txtoptexp=trim($ex_size_tot[15]);
			$sizeitem_id=trim($ex_size_tot[16]);
			
			if ($add_comma_item_tot!=0) $data_array_item_tot .=",";
			$data_array_item_tot .="(".$item_cost_id.",".$qcno.",'".$sizeitem_id."','".$fab_td."','".$spOpe_td."','".$wash_td."','".$acc_td."','".$txtCpm."','".$txtSmv."','".$txtEff."','".$txtCmCost."','".$txtFriCost."','".$txtLtstCost."','".$txtOtherCost."','".$txtCommCost."','".$fobT_td."','".$txtCommlCost."','".$txtoptexp."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			
			$item_cost_id=$item_cost_id+1;
			$add_comma_item_tot++;
		}
		
		//echo "10**".$data_array_item_tot; check_table_status( $_SESSION['menu_id'],0); die;
		
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
		
		//echo '10**'.$flag.'='.$rID_consRate.'='.$rID_tot_cost.'='.$rID_item_tot.'='.$rID; oci_rollback($con); check_table_status( $_SESSION['menu_id'],0); die;
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
		$msg_confirm="This option is already confirm. So You can't Change, Edit or Delete.";
		$isconfirm_id=str_replace("'","",$txt_update_id);
		if (is_duplicate_field( "cost_sheet_id", "qc_confirm_mst", "cost_sheet_id=$hid_qc_no and status_active=1 and is_deleted=0") == 1)
		{
			echo "11**".$msg_confirm; 
			disconnect($con);
			die;
		}
		
		$dup_styleRef=str_replace("'","",$txt_styleRef);
		$dup_season_id=str_replace("'","",$cbo_season_id);
		$dup_buyer_id=str_replace("'","",$cbo_buyer_id);
		$msg="Duplicate Styel Ref, Buyer, Season.";
		
		if (is_duplicate_field( "cost_sheet_no", "qc_mst", "style_ref='$dup_styleRef' and season_id='$dup_season_id' and buyer_id='$dup_buyer_id' and cost_sheet_no!=$txt_costSheetNo and status_active=1 and is_deleted=0") == 1)
		{
			echo "11**".$msg; 
			disconnect($con);die;
		}
		
		$insert_user_id=return_field_value("inserted_by","qc_mst","id=".$txt_update_id."","inserted_by");
		$updated_by_user=return_field_value("updated_by","qc_mst","id=".$txt_update_id."","updated_by");
		$team_dtls_arr=array();
		//$team_dtls_sql=sql_select("Select a.user_tag_id as team_leader, b.user_tag_id as team_member from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and a.user_tag_id='$user_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		if( ($updated_by_user*1)==0 ) $updated_by_user=$user_id;
		
		$team_dtls_sql=sql_select("select b.user_tag_id as team_member from lib_mkt_team_member_info b 
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
		if($_SESSION['logic_erp']["user_level"]!=2)//Admin Check
		{
			if($team_dtls_arr[$insert_user_id]=='')
			{
				echo "6**"."Update restricted, This Information is Update Only Team Leader or Member.";
				disconnect($con);die;
			}
		}
		
		$sql_dtls="Select id from qc_cons_rate_dtls where mst_id=$hid_qc_no and type=1 and status_active=1 and is_deleted=0";
		$nameArray=sql_select( $sql_dtls );
		foreach($nameArray as $row)
		{
			$dtls_update_id_array[]=$row[csf('id')];
		}
		
		$txt_option_remarks=str_replace($str_rep,' ',str_replace("'","",$txt_option_remarks));
		$txt_costing_remarks=str_replace($str_rep,' ',str_replace("'","",$txt_costing_remarks));
		$txt_meeting_remarks=str_replace($str_rep,' ',str_replace("'","",$txt_meeting_remarks));
		$qcno=str_replace("'","",$hid_qc_no);
		$field_array_mst="location_id*temp_id*lib_item_id*inquery_id*style_ref*buyer_id*season_id*season_year*brand_id*style_des*prod_dept*prod_code*department_id*delivery_date*exchange_rate*offer_qty*quoted_price*tgt_price*margin*stage_id*costing_date*buyer_remarks*article*option_remarks*updated_by*update_date";
		
		
		//$txt_temp_id=explode(",",str_replace("'","",$txt_temp_id));
		//asort($txt_temp_id);
		//$txt_temp_id="'".implode(",",$txt_temp_id)."'";
		
		$data_array_mst="".$cbo_location_id."*".$cbo_temp_id."*".$txt_temp_id."*".$txt_inquery_id."*'".strtoupper(str_replace("'","",$txt_styleRef))."'*".$cbo_buyer_id."*".$cbo_season_id."*".$cbo_season_year."*".$cbo_brand."*'".strtoupper(str_replace("'","",$txt_styleDesc))."'*".$cbo_product_department."*".$txt_product_code."*".$cbo_subDept_id."*".$txt_delivery_date."*".$txt_exchangeRate."*".$txt_offerQty."*".$txt_quotedPrice."*".$txt_tgtPrice."*".$txtmarign."*".$cbo_stage_id."*".$txt_costingDate."*'".strtoupper($txt_costing_remarks)."'*".$txt_article."*'".strtoupper($txt_option_remarks)."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		//$template_id = return_id( $txt_temp_id, $template_library, "qc_template", "id,item_id");
		
		$temp_id=explode(",",str_replace("'","",$txt_temp_id));
		$cons_rate_id=return_next_id("id", "qc_cons_rate_dtls", 1);
		
		$field_array_consrate="id, mst_id, item_id, type, particular_type_id, nb_cons, infant_cons, toddler_cons, bigger_cons, bigbigger_cons, rate, value, fab_id, description, body_part_id, supp_ref, remarks, uom, gsmweight, inserted_by, insert_date, status_active, is_deleted, use_for";
		
		$fieldArrConsRate="nb_cons*infant_cons*toddler_cons*bigger_cons*bigbigger_cons*rate*value*fab_id*description*body_part_id*uom*gsmweight*updated_by*update_date*use_for";
		//type=1 fab; particular_type_id= bodyPartid
		//type=2 sp; particular_type_id=Special Operation Name
		//type=3 wash; particular_type_id=wash type
		//type=4 acc;  particular_type_id=item_group
		//echo "10**";
		$add_comma_cons_rate=0; $data_array_cons_rate=""; $fabConsRateUpdateArr=array();
		$itemConsRateData='txtfabricData_0';
		//echo str_replace("'","",$$itemConsRateData).'<br>';
		$ex_itemConsRateData=array_filter(explode('#^',str_replace("'","",$$itemConsRateData)));
		foreach($ex_itemConsRateData as $item_wise_consRateData)
		{
			if($item_wise_consRateData!="")
			{
				//echo $item_wise_consRateData.'==';
				$ex_itemConsRate=explode('~!~',$item_wise_consRateData);
				
				$fabDesc="";
				$fabbodypartid=$fabid=$gsm=$fabusefor=$fabuom=$fabNbCons=$fabInfantCons=$fabToddlerCons=$fabBiggerCons=$fabBigBiggerCons=$fabRate=$fabAmt=$fabupid=$item_id=0;
				
				$fabbodypartid=$ex_itemConsRate[1];
				$fabDesc=$ex_itemConsRate[2];
				$fabid=$ex_itemConsRate[3];
				$gsm=$ex_itemConsRate[4];
				$fabusefor=$ex_itemConsRate[5];
				$fabuom=$ex_itemConsRate[6];
				
				$fabNbCons=$ex_itemConsRate[7];
				$fabInfantCons=$ex_itemConsRate[8];
				$fabToddlerCons=$ex_itemConsRate[9];
				$fabBiggerCons=$ex_itemConsRate[10];
				$fabBigBiggerCons=$ex_itemConsRate[11];
				$fabRate=$ex_itemConsRate[12];
				$fabAmt=$ex_itemConsRate[13];
				$fabupid=$ex_itemConsRate[14];
				$item_id=$ex_itemConsRate[15];
				
				if($fabupid>0)
				{
					$id_arr[]=$fabupid;
					$fabConsRateUpdateArr[$fabupid] =explode("*",("'".$fabNbCons."'*'".$fabInfantCons."'*'".$fabToddlerCons."'*'".$fabBiggerCons."'*'".$fabBigBiggerCons."'*'".$fabRate."'*'".$fabAmt."'*'".$fabid."'*'".$fabDesc."'*'".$fabbodypartid."'*'".$fabuom."'*'".$gsm."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*'".$fabusefor."'"));
				}
				else if($fabupid==0)
				{
					if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
					$data_array_cons_rate .="(".$cons_rate_id.",".$qcno.",'".$item_id."',1,0,'".$fabNbCons."','".$fabInfantCons."','".$fabToddlerCons."','".$fabBiggerCons."','".$fabBigBiggerCons."','".$fabRate."','".$fabAmt."','".$fabid."','".$fabDesc."','".$fabbodypartid."','','','".$fabuom."','".$gsm."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,'".$fabusefor."')";
					
					$cons_rate_id=$cons_rate_id+1;
					$add_comma_cons_rate++;
				}
			}
		}
		
		$specialData='txtspData_0';
		$ex_specialData=array_filter(explode('##',str_replace("'","",$$specialData)));
		foreach($ex_specialData as $item_wise_specialData)
		{
			$ex_itemSpConsRate=explode('_',$item_wise_specialData);
			
			$speciaOperation_id=$speciaTypeId=$spbodypartid=$spNbCons=$spInfantCons=$spToddlerCons=$spBiggerCons=$spBigBiggerCons=$spRate=$spValue=$spRemarks=0;
			
			$speciaOperation_id=$ex_itemSpConsRate[0];
			$speciaTypeId=$ex_itemSpConsRate[1];
			$spbodypartid=$ex_itemSpConsRate[3];
			
			$spNbCons=$ex_itemSpConsRate[4];
			$spInfantCons=$ex_itemSpConsRate[5];
			$spToddlerCons=$ex_itemSpConsRate[6];
			$spBiggerCons=$ex_itemSpConsRate[7];
			$spBigBiggerCons=$ex_itemSpConsRate[8];
			
			$spRate=$ex_itemSpConsRate[9];
			$spValue=$ex_itemSpConsRate[10];
			$spRemarks=$ex_itemSpConsRate[11];
			
			if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
			$data_array_cons_rate .="(".$cons_rate_id.",".$qcno.",'0',2,'".$speciaOperation_id."','".$spNbCons."','".$spInfantCons."','".$spToddlerCons."','".$spBiggerCons."','".$spBigBiggerCons."','".$spRate."','".$spValue."','".$speciaTypeId."','','".$spbodypartid."','','".$spRemarks."',0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,'')";
			
			$cons_rate_id=$cons_rate_id+1;
			$add_comma_cons_rate++;
		}
		
		$washData='txtwashData_0';
		$ex_washData=array_filter(explode('##',str_replace("'","",$$washData)));
		foreach($ex_washData as $item_wise_washData)
		{
			$ex_itemwashConsRate=explode('_',$item_wise_washData);
			$washTypeId=$wbodypartid=$wNbCons=$wInfantCons=$wToddlerCons=$wBiggerCons=$wBigBiggerCons=$wRate=$wValue=$wRemarks=0;
			
			$washTypeId=$ex_itemwashConsRate[1];
			$wbodypartid=$ex_itemwashConsRate[3];
			
			$wNbCons=$ex_itemwashConsRate[4];
			$wInfantCons=$ex_itemwashConsRate[5];
			$wToddlerCons=$ex_itemwashConsRate[6];
			$wBiggerCons=$ex_itemwashConsRate[7];
			$wBigBiggerCons=$ex_itemwashConsRate[8];
			
			$wRate=$ex_itemwashConsRate[9];
			$wValue=$ex_itemwashConsRate[10];
			$wRemarks=$ex_itemwashConsRate[11];
			
			if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
			$data_array_cons_rate .="(".$cons_rate_id.",".$qcno.",'0',3,'".$washTypeId."','".$wNbCons."','".$wInfantCons."','".$wToddlerCons."','".$wBiggerCons."','".$wBigBiggerCons."','".$wRate."','".$wValue."',0,'','".$wbodypartid."','','".$wRemarks."',0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,'')";
			
			$cons_rate_id=$cons_rate_id+1;
			$add_comma_cons_rate++;
		}
		
		$itemAccData='txtaccData_0';
		$ex_itemAccData=array_filter(explode('#^',str_replace("'","",$$itemAccData)));
		foreach($ex_itemAccData as $item_wise_itemAccData)
		{
			$ex_itemAcConsRate=explode('~!~',$item_wise_itemAccData);
			
			$accessories_id=$accDescription=$accBandRef=$accUseFor=$accNbCons=$accInfantCons=$accToddlerCons=$accBiggerCons=$accBigBiggerCons=$acRate=$acVal=$acconsUom=$acConsCal=$acRateData=0;
			
			$accessories_id=$ex_itemAcConsRate[1];
			$accDescription=$ex_itemAcConsRate[2];
			$accBandRef=$ex_itemAcConsRate[3];
			$acconsUom=$ex_itemAcConsRate[4];
			
			$accNbCons=$ex_itemAcConsRate[5];
			$accInfantCons=$ex_itemAcConsRate[6];
			$accToddlerCons=$ex_itemAcConsRate[7];
			$accBiggerCons=$ex_itemAcConsRate[8];
			$accBigBiggerCons=$ex_itemAcConsRate[9];
			$acRate=$ex_itemAcConsRate[10];
			$acVal=$ex_itemAcConsRate[11];
			
			if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
			$data_array_cons_rate .="(".$cons_rate_id.",".$qcno.",'0',4,'".$accessories_id."','".$accNbCons."','".$accInfantCons."','".$accToddlerCons."','".$accBiggerCons."','".$accBigBiggerCons."','".$acRate."','".$acVal."',0,'".$accDescription."',0,'".$accBandRef."','','".$acconsUom."',0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,'')";
			
			$cons_rate_id=$cons_rate_id+1;
			$add_comma_cons_rate++;
		}
		
		//echo "10**";
		//print_r ($data_array_cons_rate); 
		//echo bulk_update_sql_statement("qc_fabric_dtls", "id",$field_array_fab,$data_array_fab,$id_arr ); die;
		/*echo "insert into qc_cons_rate_dtls (".$field_array_consrate.") values ".$data_array_cons_rate; die;
		die;*/
		//echo $data_array_cons_rate; 
		
		$tc_sum_id=return_next_id("id", "qc_tot_cost_summary", 1);
		$field_array_tot_cost="id, mst_id, buyer_agent_id, location_id, no_of_pack, is_cm_calculative, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_wash_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, commercial_per, commercial_cost, operating_exp, inserted_by, insert_date, status_active, is_deleted";
		
		$ex_tot_cost_data=explode('_',$data_tot_cost_summ);
		
		$cbo_buyer_agent=trim($ex_tot_cost_data[0]);
		$cbo_agent_location=trim($ex_tot_cost_data[1]);
		$txt_noOfPack=trim($ex_tot_cost_data[2]);
		$cmPop=trim($ex_tot_cost_data[3]);
		
		$txt_commPer=trim($ex_tot_cost_data[4]);
		$totFab_td=trim($ex_tot_cost_data[5]);
		$totSpc_td=trim($ex_tot_cost_data[6]);
		$totWash_td=trim($ex_tot_cost_data[7]);
		$totAcc_td=trim($ex_tot_cost_data[8]);
		$totCm_td=trim($ex_tot_cost_data[9]);
		$totFriCst_td=trim($ex_tot_cost_data[10]);
		$totLbTstCst_td=trim($ex_tot_cost_data[11]);
		$totOtherCst_td=trim($ex_tot_cost_data[12]);
		$totCommCst_td=trim($ex_tot_cost_data[13]);
		$totCost_td=trim($ex_tot_cost_data[14]);
		$totalFob_td=trim($ex_tot_cost_data[15]);
		$totCommlCst_td=trim($ex_tot_cost_data[16]);
		$txt_commlPer=trim($ex_tot_cost_data[17]);
		$txt_optexp=trim($ex_tot_cost_data[18]);
		
		$data_array_tot_cost="(".$tc_sum_id.",".$qcno.",'".$cbo_buyer_agent."','".$cbo_agent_location."','".$txt_noOfPack."','".$cmPop."','".$txt_commPer."','".$totFab_td."','".$totSpc_td."','".$totWash_td."','".$totAcc_td."','".$totCm_td."','".$totFriCst_td."','".$totLbTstCst_td."','".$totOtherCst_td."','".$totCommCst_td."','".$totCost_td."','".$totalFob_td."','".$txt_commlPer."','".$totCommlCst_td."','".$txt_optexp."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		//echo $data_array_tot_cost; die;
		$field_array_item_tot=" id, mst_id, item_id, fabric_cost, sp_operation_cost, wash_cost, accessories_cost, cpm, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, other_cost, commission_cost, fob_pcs, commercial_cost, operating_exp, inserted_by, insert_date, status_active, is_deleted";
		$item_cost_id=return_next_id("id", "qc_item_cost_summary", 1);
		$ex_item_wise_tot_data=array_filter(explode('__',str_replace("'","",$item_wise_tot_data)));
		$add_comma_item_tot=0; $data_array_item_tot="";
		foreach($ex_item_wise_tot_data as $size_wise_tot)
		{
			$fab_td=$spOpe_td=$wash_td=$acc_td=$txtCpm=$txtSmv=$txtEff=$txtCmCost=$txtFriCost=$txtLtstCost=$txtOtherCost=$txtCommCost=$fobT_td=$fobPcs_td=$txtCommlCost=$txtoptexp=$sizeitem_id=0;
			
			$ex_size_tot=explode('_',$size_wise_tot);
			$fab_td=trim($ex_size_tot[0]);
			$spOpe_td=trim($ex_size_tot[1]);
			$wash_td=trim($ex_size_tot[2]);
			$acc_td=trim($ex_size_tot[3]);
			$txtCpm=trim($ex_size_tot[4]);
			$txtSmv=trim($ex_size_tot[5]);
			$txtEff=trim($ex_size_tot[6]);
			$txtCmCost=trim($ex_size_tot[7]);
			$txtFriCost=trim($ex_size_tot[8]);
			$txtLtstCost=trim($ex_size_tot[9]);
			$txtOtherCost=trim($ex_size_tot[10]);
			$txtCommCost=trim($ex_size_tot[11]);
			$fobT_td=trim($ex_size_tot[12]);
			$fobPcs_td=trim($ex_size_tot[13]);
			$txtCommlCost=trim($ex_size_tot[14]);
			$txtoptexp=trim($ex_size_tot[15]);
			$sizeitem_id=trim($ex_size_tot[16]);
			
			if ($add_comma_item_tot!=0) $data_array_item_tot .=",";
			$data_array_item_tot .="(".$item_cost_id.",".$qcno.",'".$sizeitem_id."','".$fab_td."','".$spOpe_td."','".$wash_td."','".$acc_td."','".$txtCpm."','".$txtSmv."','".$txtEff."','".$txtCmCost."','".$txtFriCost."','".$txtLtstCost."','".$txtOtherCost."','".$txtCommCost."','".$fobT_td."','".$txtCommlCost."','".$txtoptexp."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			
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
		
		$d_cons_rate=execute_query( "delete from qc_cons_rate_dtls where mst_id =".$hid_qc_no." and type<>1",1);
		if($d_cons_rate==1 && $flag==1) $flag=1; else  $flag=0;
		//echo "10**";
		$rIDfab=1;
		if($fabConsRateUpdateArr!="")
		{
			//echo bulk_update_sql_statement("qc_cons_rate_dtls", "id",$fieldArrConsRate,$fabConsRateUpdateArr,$id_arr);
			$rIDfab=execute_query(bulk_update_sql_statement("qc_cons_rate_dtls", "id",$fieldArrConsRate,$fabConsRateUpdateArr,$id_arr));
			if($rIDfab==1 && $flag==1) $flag=1; else $flag=0;
		}
		//die;
		
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
		
		if(implode(',',$id_arr)!="")
		{
			$distance_delete_id=implode(',',array_diff($dtls_update_id_array,$id_arr));
		}
		else
		{
			$distance_delete_id=implode(',',$dtls_update_id_array);
		}
		if(str_replace("'",'',$distance_delete_id)!="" && $flag==1)
		{
			$rID3=execute_query( "update qc_cons_rate_dtls set status_active=0, is_deleted=1 where id in ($distance_delete_id) and mst_id=$hid_qc_no and type=1 and status_active=1 and is_deleted=0",1);
			if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		//echo "10**".$flag."=".$rID."=".$rIDfab."=".$rID_consRate."=".$rID_tot_cost."=".$rID_item_tot."=".$rID_buyer_meeting_up."=".$rID_upmst."=".$rID3; die;
		
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
		
		$msg_confirm="This option is already confirm. So You can't Change, Edit or Delete.";
		$isconfirm_id=str_replace("'","",$txt_update_id);
		if (is_duplicate_field( "cost_sheet_id", "qc_confirm_mst", "cost_sheet_id=$hid_qc_no and status_active=1 and is_deleted=0") == 1)
		{
			echo "11**".$msg_confirm; 
			disconnect($con);
			die;
		}
		
		$insert_user_id=return_field_value("inserted_by","qc_mst","id=".$txt_update_id."","inserted_by");
		$user_wise_msg="You have no right for delete. If you need delete, please contract with MIS Department.";
		if($insert_user_id!=$user_id)
		{
			echo "11**".$user_wise_msg; 
			disconnect($con);die;
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
		show_list_view (document.getElementById('cbo_buyer_id').value+'**'+document.getElementById('cbo_season_id').value+'**'+document.getElementById('cbo_subDept_id').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_cost_sheet_no').value+'**'+document.getElementById('hide_dataType').value+'**'+document.getElementById('cbo_approval_type').value+'**'+document.getElementById('cbo_string_search_type').value, 'style_ref_search_list_view', 'search_div', 'short_quotation_v5_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
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
			var page_link='short_quotation_v5_controller.php?action=confirmStyle_popup';
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
                    	<th colspan="7"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                	</tr>
                	<tr>
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
                        <td><? echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name",1, "-- All Buyer--",$buyerId,"load_drop_down( 'short_quotation_v5_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'short_quotation_v5_controller',this.value, 'load_drop_down_sub_dep', 'sub_td' );",0 ); ?></td>                 
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
	$string_search_type=str_replace("'","",$ex_data[7]);
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
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1170" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="100">Buyer Name</th>
            <th width="80">Season</th>
            <th width="70">Depar tment</th>
            <th width="100">Style Ref.</th>
            <th width="140">Template</th>
            <th width="60">FOB ($)</th>
            <th width="80">Stage</th>
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
            	<th width="165">Un-approved Request</th>
             	<?
			}
			?>
        </thead>
    </table>
    <div style="width:1170px; overflow-y:scroll; max-height:260px;" align="center">
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1150" class="rpt_table" id="tbl_list_search" >
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
			$sql_style = "select a.id, a.cost_sheet_no, a.costing_date, a.buyer_id, a.season_id, a.style_ref, a.department_id, a.temp_id, a.lib_item_id, a.stage_id, a.inserted_by, a.updated_by, a.qc_no, a.revise_no, a.option_id, c.approval_cause from qc_mst a, qc_confirm_mst b, fabric_booking_approval_cause c where a.qc_no=b.cost_sheet_id and c.booking_id=a.qc_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=28 and c.approval_type=2 and c.status_active=1 and c.is_deleted=0 and a.entry_form=552
			$buyer_id_cond $style_ref_cond $cost_sheet_no_cond $season_id_cond $department_id_cond $approvaltype_cond $unappreq
			group by a.id, a.cost_sheet_no, a.costing_date, a.buyer_id, a.season_id, a.style_ref, a.department_id, a.temp_id, a.lib_item_id, a.stage_id, a.inserted_by, a.updated_by, a.qc_no, a.revise_no, a.option_id, c.approval_cause order by a.id Desc";
		}
		else
		{
			$sql_style = "select a.id, a.cost_sheet_no, a.costing_date, a.buyer_id, a.season_id, a.style_ref, a.department_id, a.temp_id, a.lib_item_id, a.stage_id, a.inserted_by, a.updated_by, a.qc_no, a.revise_no, a.option_id from qc_mst a, qc_confirm_mst b where a.qc_no=b.cost_sheet_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=552
			$buyer_id_cond $style_ref_cond $cost_sheet_no_cond $season_id_cond $department_id_cond $approvaltype_cond
			group by a.id, a.cost_sheet_no, a.costing_date, a.buyer_id, a.season_id, a.style_ref, a.department_id, a.temp_id, a.lib_item_id, a.stage_id, a.inserted_by, a.updated_by, a.qc_no, a.revise_no, a.option_id order by a.id Desc";

		}
	}
	else
	{
		$sql_style = "select min(a.id) as id, a.cost_sheet_no, max(a.costing_date) as costing_date, a.buyer_id, a.season_id, a.style_ref, a.department_id, a.temp_id, a.lib_item_id, a.stage_id, max(a.inserted_by) as inserted_by, max(a.updated_by) as updated_by, min(a.qc_no) as qc_no from qc_mst a where a.status_active=1 and a.is_deleted=0 and a.entry_form=552 $buyer_id_cond $style_ref_cond $cost_sheet_no_cond $season_id_cond $department_id_cond $approvaltype_cond group by a.cost_sheet_no, a.buyer_id, a.season_id, a.style_ref, a.department_id, a.temp_id, a.lib_item_id, a.stage_id order by id Desc";
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
            <td width="30" align="center"><?php echo $i; ?></td>
            <td width="100" style="word-break:break-all"><?php echo $buyer_arr[$row[csf("buyer_id")]]; ?></td>
            <td width="80" style="word-break:break-all"><?php echo $season_arr[$row[csf("season_id")]]; ?></td>
            <td width="70" style="word-break:break-all"><?php echo $department_arr[$row[csf("department_id")]]; ?>&nbsp;</td>
            <td width="100" style="word-break:break-all"><?php echo $unapp_link; ?> <?php echo $row[csf("style_ref")]; ?><?php echo $unapp_endlink; ?></td>
            <td width="140" style="word-break:break-all"><?php echo $template_name; ?></td>
            <td width="60" align="right"><?php echo number_format($cost_summ_arr[$row[csf("qc_no")]]['fob'],4); ?>&nbsp;</td>
            <td width="80" style="word-break:break-all"><?php echo $stage_arr[$row[csf("stage_id")]]; ?>&nbsp;</td>	
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
                <td width="145" style="word-break:break-all"><?php echo $row[csf("approval_cause")]; ?></td>
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
		
		$sql_style= "select min(id) as id, cost_sheet_no, style_ref, min(qc_no) as qc_no, season_id, inserted_by from qc_mst where entry_form=552 and cost_sheet_no in ($ex_data[0]) and cost_sheet_no not in (select cost_sheet_no from qc_style_temp_data where cost_sheet_no in ($ex_data[0]) and inserted_by='$user_id' and status_active=1 and is_deleted=0 ) and revise_no=0 and option_id=0 and status_active=1 and is_deleted=0 group by cost_sheet_no, style_ref, season_id, inserted_by";
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
	
	$sql_temp_style=sql_select("select a.id, a.cost_sheet_no, a.style_ref, a.season_id, a.inserted_by, a.updated_by, a.qc_no, a.season_year from qc_mst a, qc_style_temp_data b where a.id=b.mst_id and b.inserted_by='$user_id' and a.entry_form=552 and b.status_active=1 and b.is_deleted=0 order by a.style_ref ASC");
	
	$i=1;
	foreach($sql_temp_style as $row)
	{  
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		if($ex_data[0]==$row[csf('cost_sheet_no')]) $bgcolor="#33CC00"; else $bgcolor;
		$user_name="";
		//if($row[csf('updated_by')]==0 || $row[csf('updated_by')]=="") $user_name=$user_arr[$row[csf('inserted_by')]]; else $user_name=$user_arr[$row[csf('updated_by')]];
		$user_name=$user_arr[$row[csf('inserted_by')]];
		
		?>
        <tr id="tr_<?=$row[csf('qc_no')]; ?>" bgcolor="<?=$bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="change_color_tr('<?=$row[csf('qc_no')]; ?>','<?=$bgcolor; ?>'); set_onclick_style_list('<?=$row[csf('qc_no')].'__'.$row[csf('inserted_by')].'__'.$row[csf('cost_sheet_no')].'__25'; ?>')">
        	<td width="80" style="word-break:break-all"><?=$row[csf('style_ref')]; ?>&nbsp;</td>
            <td width="35" style="word-break:break-all"><?=$row[csf('season_year')]; ?>&nbsp;</td>
            <td width="60" style="word-break:break-all"><?=$season_arr[$row[csf('season_id')]]; ?>&nbsp;</td>
            <td style="word-break:break-all" title="<?='U.User='.$user_arr[$row[csf('updated_by')]]; ?>"><?=$user_name; ?>&nbsp;</td>
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
	
	/*$lib_temp_arr=return_library_array("select id, item_name from lib_qc_template","id","item_name");
	if($db_type==0) $concat_cond="group_concat(lib_item_id)";
	else if($db_type==2) $concat_cond="listagg(cast(lib_item_id as varchar2(4000)),',') within group (order by lib_item_id)";
	else $concat_cond="";
	$sql_tmp="select tuid, temp_id, $concat_cond as lib_item_id from qc_template where status_active=1 and is_deleted=0 group by tuid, temp_id order by temp_id ASC";
	$sql_tmp_res=sql_select($sql_tmp);*/
	
	$inqueryNoArr=return_library_array("select id, system_number_prefix_num from wo_quotation_inquery","id","system_number_prefix_num");
	$itemGroupArr=array();
	$itemGroupSql=sql_select("select id, item_name from lib_item_group where status_active=1 and is_deleted=0 and item_category=4");
	foreach($itemGroupSql as $row)
	{
		$itemGroupArr[$row[csf('id')]]=strtoupper($row[csf('item_name')]);
	}
	unset($itemGroupSql);
	
	//print_r($sql_tmp_res);die;
	$sql_tmp="select tuid, temp_id, item_id1, ratio1, status_active from qc_template where status_active=1 and is_deleted=0 and lib_item_id='0' order by id ASC";
	$sql_tmp_res=sql_select($sql_tmp);
	$mst_temp_arr=array();
	foreach($sql_tmp_res as $row)
	{
		$mst_temp_arr[$row[csf('tuid')]].=$row[csf('item_id1')].'**'.$row[csf('ratio1')].'**'.$row[csf('status_active')].'__';
	}
	$template_name_arr=array(); $itemNratioArr=array();
	foreach($mst_temp_arr as $temp_id=>$tmp_data)
	{
		$template_data=array_filter(explode('__',$tmp_data));
		$template_name=''; $itemNratio=""; $packQty=0;
		foreach($template_data as $temp_val)
		{
			$ex_tmp_val=explode('**',$temp_val);
			$item_id1=''; $ratio1=0;
			
			$item_id1=$ex_tmp_val[0]; 
			$ratio1=$ex_tmp_val[1]; 
			
			if($template_name=='') $template_name=$garments_item[$item_id1].'-'.$ratio1; else $template_name.=','.$garments_item[$item_id1].'-'.$ratio1;
			if($itemNratio=='') $itemNratio=trim($item_id1).'!!'.$ratio1; else $itemNratio.=','.trim($item_id1).'!!'.$ratio1;
			$packQty+=$ratio1;
		}
		$template_name_arr[$temp_id]=$template_name;
		$itemNratioArr[$temp_id]['ratio']=$itemNratio;
		$itemNratioArr[$temp_id]['packqty']=$packQty;
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
	
	$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name"  );
	
	//if ($chk_new_meeting==1) $max_meeting=$max_meeting+1;
	//echo "select id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, style_ref, buyer_id, cons_basis, season_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, revise_no, option_id, buyer_remarks, option_remarks, inserted_by, meeting_no, qc_no from qc_mst where status_active=1 and is_deleted=0 and qc_no='$cost_sheet_id'"; die;
	$data_array=sql_select("select id, company_id, location_id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, inquery_id, style_ref, buyer_id, uom, cons_basis, season_id, season_year, brand_id, style_des, prod_dept, prod_code, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, margin, stage_id, costing_date, revise_no, option_id, buyer_remarks, article, option_remarks, inserted_by, meeting_no, qc_no, fab_process_loss_method, acc_process_loss_method from qc_mst where status_active=1 and is_deleted=0 and cost_sheet_no='$cost_sheet_no' $rev_no_cond $option_id_cond");//$starting_row_cond and inserted_by='$cond_user'  qc_no='$cost_sheet_id'   
	
  //echo $data_array;die();
	$cond_data=$data_array[0][csf('qc_no')];
	$cost_sheet_id=$data_array[0][csf('id')];
	$itemIdcomm=$data_array[0][csf('lib_item_id')];
	foreach ($data_array as $row)
	{
		echo "$('#cbo_company_id').val('".$row[csf("company_id")]."');\n";
		echo "$('#txt_fab_process_loss_method').val('".$row[csf("fab_process_loss_method")]."');\n";
		echo "$('#txt_acc_process_loss_method').val('".$row[csf("acc_process_loss_method")]."');\n";
		echo "load_drop_down('requires/short_quotation_v5_controller', '".trim($row[csf("company_id")])."', 'load_drop_down_location', 'location_td'); \n";
		echo "$('#cbo_location_id').val('".$row[csf("location_id")]."');\n";
		$ex_tmpelate_id=explode(',',$row[csf("temp_id")]);
		$count_temp=count(explode(',',$row[csf("lib_item_id")]));
		$template_name='';
		foreach($ex_tmpelate_id as $tmpelate_id)
		{
			if($template_name=="") $template_name=$template_name_arr[$tmpelate_id]; else $template_name.=','.$template_name_arr[$tmpelate_id];
		}
		//$template_name=$template_name_arr[$row[csf("temp_id")]];
		echo "$('#cbo_temp_id').val('".$row[csf("temp_id")]."');\n";
		echo "$('#txt_temp_id').val('".trim($row[csf("lib_item_id")])."');\n";
		//echo "load_drop_down('requires/short_quotation_v5_controller', '".trim($itemNratioArr[$row[csf("temp_id")]]['ratio'])."', 'load_drop_down_tempItem', 'item_td'); \n";
		echo "fnc_item_list('".trim($row[csf("lib_item_id")])."__".$template_name."');\n"; 
		//echo "fnc_summary_dtls('".trim($row[csf("lib_item_id")])."__".$template_name."');\n"; 
		//
		echo "navigate_arrow_key();\n";
		//echo "fnc_change_data();\n";txt_temp_id

		
		
		echo "$('#txt_inquery_id').val('".$row[csf("inquery_id")]."');\n";
		echo "$('#txt_inquiry_no').val('".$inqueryNoArr[$row[csf("inquery_id")]]."');\n";
		echo "$('#txt_styleRef').val('".$row[csf("style_ref")]."');\n"; 
		echo "$('#txt_update_id').val('".$row[csf("id")]."');\n";
		//echo "$('#txt_costSheet_id').val('".$row[csf("cost_sheet_id")]."');\n";
		echo "$('#txt_costSheetNo').val('".$row[csf("cost_sheet_no")]."');\n";
		echo "$('#cbo_buyer_id').val('".$row[csf("buyer_id")]."');\n";
		//echo "$('#cbouom').val('".$row[csf("uom")]."');\n";
		//echo "$('#cbo_cons_basis_id').val('".$row[csf("cons_basis")]."');\n";
		//echo "fnc_consumption_write_disable('".$row[csf("cons_basis")]."');\n"; 
		echo "load_drop_down('requires/short_quotation_v5_controller', '".$row[csf("buyer_id")]."', 'load_drop_down_season', 'season_td');\n"; 
		echo "load_drop_down('requires/short_quotation_v5_controller', '".$row[csf("buyer_id")]."', 'load_drop_down_brand', 'brand_td');\n";
		echo "load_drop_down('requires/short_quotation_v5_controller', '".$row[csf("buyer_id")]."', 'load_drop_down_sub_dep', 'sub_td');\n";
		
		echo "$('#cbo_season_id').val('".$row[csf("season_id")]."');\n";
		echo "$('#cbo_season_year').val('".$row[csf("season_year")]."');\n";
		echo "$('#cbo_brand').val('".$row[csf("brand_id")]."');\n";
		
		echo "$('#txt_styleDesc').val('".$row[csf("style_des")]."');\n";
		echo "$('#cbo_product_department').val('".$row[csf("prod_dept")]."');\n";
		echo "$('#txt_product_code').val('".$row[csf("prod_code")]."');\n";
		
		echo "$('#cbo_subDept_id').val('".$row[csf("department_id")]."');\n";
		echo "$('#txt_delivery_date').val('".change_date_format($row[csf("delivery_date")])."');\n";
		echo "$('#txt_exchangeRate').val('".$row[csf("exchange_rate")]."');\n";
		
		echo "$('#txt_offerQty').val('".$row[csf("offer_qty")]."');\n";
		echo "$('#txt_quotedPrice').val('".$row[csf("quoted_price")]."');\n"; 
		echo "$('#txt_tgtPrice').val('".$row[csf("tgt_price")]."');\n";
		echo "$('#txt_article').val('".$row[csf("article")]."');\n";
		echo "$('#txtmarign').val('".$row[csf("margin")]."');\n";
		echo "$('#cbo_stage_id').val('".$row[csf("stage_id")]."');\n";
		echo "$('#txt_costingDate').val('".change_date_format($row[csf("costing_date")])."');\n"; 
		$buyer_remarks = str_replace("\n", "\\n", $row[csf("buyer_remarks")]);
		echo "$('#txt_costing_remarks').val('".$buyer_remarks."');\n";
		echo "$('#txt_costing_remarks').attr('pre_costing_remarks','".$buyer_remarks."');\n"; 
		echo "$('#txt_option_remarks').val('".$row[csf("option_remarks")]."');\n";
		echo "$('#txt_option_remarks').attr('pre_opt_remarks','".$row[csf("option_remarks")]."');\n";
		echo "$('#hid_qc_no').val('".$row[csf("qc_no")]."');\n"; 
		echo "$('#txt_isUpdate').val(1);\n"; 
		echo "disable_enable_fields('cbo_temp_id',1);\n";
		if($ex_data[4]!="from_option")
		{
			//echo "load_drop_down('requires/short_quotation_v5_controller', '".$row[csf("cost_sheet_no")].'__'.$row[csf("option_id")].'__'.$row[csf("revise_no")]."', 'load_drop_down_revise_no', 'revise_td');\n"; //.'__'.$row[csf("revise_no")]
			//echo "load_drop_down('requires/short_quotation_v5_controller', '".$row[csf("cost_sheet_no")].'__'.$row[csf("option_id")]."', 'load_drop_down_option_id', 'option_td');\n"; 
		}
		//echo "$('#cbo_option_id').val('".$row[csf("option_id")]."');\n";
		if($count_temp==1) {
			$set_pcs='PCS';
			$fob_set_pcs='F.O.B($/PCS)';
		}
		else if($count_temp>1){
			$set_pcs='Set';
			$fob_set_pcs='F.O.B($/Set)';
		}
		else {
			$set_pcs='';
			$fob_set_pcs='';
		}
		
		echo "$('#uom_td').text('".$set_pcs."');\n";
		echo "$('#fobtxt').text('".$fob_set_pcs."');\n";
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
	
	$sql_cons_rate="select id, mst_id, item_id, type, particular_type_id, nb_cons, infant_cons, toddler_cons, bigger_cons, bigbigger_cons, rate, value, fab_id, description, body_part_id, supp_ref, remarks, uom, gsmweight, use_for from qc_cons_rate_dtls where mst_id=$cond_data and status_active=1 and is_deleted=0 order by id asc";
	$sql_result_cons_rate=sql_select($sql_cons_rate);
	$cons_rate_fab_data=""; $cons_rate_sp_data=""; $cons_rate_wash_data=""; $cons_rate_acc_data="";
	
	foreach ($sql_result_cons_rate as $rowConsRate)
	{
		if($rowConsRate[csf("type")]==1)
		{
			$cons_rate_fab_data.=$body_part[$rowConsRate[csf("body_part_id")]].'~!~'.$rowConsRate[csf("body_part_id")].'~!~'.$rowConsRate[csf("description")].'~!~'.$rowConsRate[csf("fab_id")].'~!~'.$rowConsRate[csf("gsmweight")].'~!~'.$rowConsRate[csf("use_for")].'~!~'.$rowConsRate[csf("uom")].'~!~'.$rowConsRate[csf("nb_cons")].'~!~'.$rowConsRate[csf("infant_cons")].'~!~'.$rowConsRate[csf("toddler_cons")].'~!~'.$rowConsRate[csf("bigger_cons")].'~!~'.$rowConsRate[csf("bigbigger_cons")].'~!~'.$rowConsRate[csf("rate")].'~!~'.$rowConsRate[csf("value")].'~!~'.$rowConsRate[csf("id")].'~!~'.$rowConsRate[csf("item_id")].'#^';
		}
		if($rowConsRate[csf("type")]==2)
		{
			$cons_rate_sp_data.=$rowConsRate[csf("particular_type_id")].'_'.$rowConsRate[csf("fab_id")].'_'.$body_part[$rowConsRate[csf("body_part_id")]].'_'.$rowConsRate[csf("body_part_id")].'_'.$rowConsRate[csf("nb_cons")].'_'.$rowConsRate[csf("infant_cons")].'_'.$rowConsRate[csf("toddler_cons")].'_'.$rowConsRate[csf("bigger_cons")].'_'.$rowConsRate[csf("bigbigger_cons")].'_'.$rowConsRate[csf("rate")].'_'.$rowConsRate[csf("value")].'_'.$rowConsRate[csf("remarks")].'##';
		}
		if($rowConsRate[csf("type")]==3)
		{
			$cons_rate_wash_data.=$emblishment_wash_type[$rowConsRate[csf("particular_type_id")]].'_'.$rowConsRate[csf("particular_type_id")].'_'.$body_part[$rowConsRate[csf("body_part_id")]].'_'.$rowConsRate[csf("body_part_id")].'_'.$rowConsRate[csf("nb_cons")].'_'.$rowConsRate[csf("infant_cons")].'_'.$rowConsRate[csf("toddler_cons")].'_'.$rowConsRate[csf("bigger_cons")].'_'.$rowConsRate[csf("bigbigger_cons")].'_'.$rowConsRate[csf("rate")].'_'.$rowConsRate[csf("value")].'_'.$rowConsRate[csf("remarks")].'##';
		}
		if($rowConsRate[csf("type")]==4)
		{
			$cons_rate_acc_data.=$itemGroupArr[$rowConsRate[csf("particular_type_id")]].'~!~'.$rowConsRate[csf("particular_type_id")].'~!~'.$rowConsRate[csf("description")].'~!~'.$rowConsRate[csf("supp_ref")].'~!~'.$rowConsRate[csf("uom")].'~!~'.$rowConsRate[csf("nb_cons")].'~!~'.$rowConsRate[csf("infant_cons")].'~!~'.$rowConsRate[csf("toddler_cons")].'~!~'.$rowConsRate[csf("bigger_cons")].'~!~'.$rowConsRate[csf("bigbigger_cons")].'~!~'.$rowConsRate[csf("rate")].'~!~'.$rowConsRate[csf("value")].'#^';
		}
	}
	unset($sql_result_cons_rate);
	
	//echo $cons_rate_fab_data.'<br>';
	echo "$('#txtfabricData_0').val('".$cons_rate_fab_data."');\n";
	//echo $cons_rate_fab_data;
	echo "$('#txtspData_0').val('".$cons_rate_sp_data."');\n";
	echo "$('#txtwashData_0').val('".$cons_rate_wash_data."');\n";
	echo "$('#txtaccData_0').val('".$cons_rate_acc_data."');\n";
	
	echo "fnc_dtls_ganerate('".$itemIdcomm.'__'.$template_name."');\n"; 
	
	$sql_summ="select buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_wash_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, commercial_per, commercial_cost, operating_exp from qc_tot_cost_summary where mst_id='$cond_data' and status_active=1 and is_deleted=0";
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
		echo "$('#txt_commlPer').val('".$rowSumm[csf("commercial_per")]."');\n";
		echo "$('#txt_commPer').val('".$rowSumm[csf("commision_per")]."');\n";
		echo "$('#totFab_td').text('".$rowSumm[csf("tot_fab_cost")]."');\n";
		echo "$('#totSpc_td').text('".$rowSumm[csf("tot_sp_operation_cost")]."');\n";
		echo "$('#totWash_td').text('".$rowSumm[csf("tot_wash_cost")]."');\n";
		echo "$('#totAcc_td').text('".$rowSumm[csf("tot_accessories_cost")]."');\n";
		echo "$('#totCm_td').text('".$rowSumm[csf("tot_cm_cost")]."');\n";
		echo "$('#totFriCst_td').text('".$rowSumm[csf("tot_fright_cost")]."');\n";
		echo "$('#totLbTstCst_td').text('".$rowSumm[csf("tot_lab_test_cost")]."');\n";
		echo "$('#totOptExp_td').text('".$rowSumm[csf("operating_exp")]."');\n";
		
		echo "$('#totOtherCst_td').text('".$rowSumm[csf("tot_other_cost")]."');\n";
		echo "$('#totCommlCst_td').text('".$rowSumm[csf("commercial_cost")]."');\n";
		echo "$('#totCommCst_td').text('".$rowSumm[csf("tot_commission_cost")]."');\n";
		echo "$('#totCost_td').text('".$rowSumm[csf("tot_cost")]."');\n";
		echo "$('#totalFob_td').text('".$rowSumm[csf("tot_fob_cost")]."');\n";
		echo "$('#totalFob_td').attr('prev_fob','".$rowSumm[csf("tot_fob_cost")]."');\n";
		echo "$('#totFOBCost_td').text('".number_format(($rowSumm[csf("tot_cost")]/12),4)."');\n";
	}
	unset($sql_result_summ);
	$sql_item_summ="select item_id, fabric_cost, sp_operation_cost, wash_cost, accessories_cost, cpm, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, other_cost, commission_cost, fob_pcs, rmg_ratio, commercial_cost, operating_exp from qc_item_cost_summary where mst_id='$cond_data' and status_active=1 and is_deleted=0";
	$sql_result_item_summ=sql_select($sql_item_summ);
	foreach($sql_result_item_summ as $rowItemSumm)
	{
		echo "$('#fabtd_".$rowItemSumm[csf("item_id")]."').text('".$rowItemSumm[csf("fabric_cost")]."');\n";
		echo "$('#spOpetd_".$rowItemSumm[csf("item_id")]."').text('".$rowItemSumm[csf("sp_operation_cost")]."');\n";
		echo "$('#washtd_".$rowItemSumm[csf("item_id")]."').text('".$rowItemSumm[csf("wash_cost")]."');\n";
		echo "$('#acctd_".$rowItemSumm[csf("item_id")]."').text('".$rowItemSumm[csf("accessories_cost")]."');\n";
		echo "$('#txtcpm_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("cpm")]."');\n";
		echo "$('#txtsmv_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("smv")]."');\n";
		echo "$('#txteff_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("efficiency")]."');\n";
		echo "$('#txtCmCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("cm_cost")]."');\n";
		echo "$('#txtFriCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("frieght_cost")]."');\n";
		echo "$('#txtLtstCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("lab_test_cost")]."');\n";
		echo "$('#txtOptExp_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("operating_exp")]."');\n";
		echo "$('#txtOtherCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("other_cost")]."');\n";
		echo "$('#txtCommlCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("commercial_cost")]."');\n";
		echo "$('#txtCommCost_".$rowItemSumm[csf("item_id")]."').val('".$rowItemSumm[csf("commission_cost")]."');\n";
		
		echo "$('#fobTtd_".$rowItemSumm[csf("item_id")]."').text('".$rowItemSumm[csf("fob_pcs")]."');\n";
		echo "$('#fobPcsTtd_".$rowItemSumm[csf("item_id")]."').text('".number_format(($rowItemSumm[csf("fob_pcs")]/12),4)."');\n";
	}
	unset($sql_result_item_summ);
	if($cond_data!='' || $cond_data!=0)
	{
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_qcosting_entry',1);\n";
	}
	else
	{
		echo "$('#txt_costSheetNo').val('');\n";
		//echo "$('#txt_costSheetNo').focus();\n";qc_template
	}
	exit();
}

if($action=="load_lib_item_id")
{
	$sql_item_id="Select item_id1, ratio1 from qc_template where tuid='$data' and status_active=1 and is_deleted=0";
	$lib_item_id=""; $packqty=0; $itemNratio="";
	$sql_item_id_res=sql_select($sql_item_id); 
	foreach($sql_item_id_res as $row)
	{
		if($lib_item_id=='') $lib_item_id=trim($row[csf("item_id1")]); else $lib_item_id.=','.trim($row[csf("item_id1")]);
		if($itemNratio=='') $itemNratio=trim($row[csf("item_id1")]).'!!'.$row[csf("ratio1")]; else $itemNratio.=','.trim($row[csf("item_id1")]).'!!'.$row[csf("ratio1")];
		$packqty+=$row[csf("ratio1")];
	}
	echo $lib_item_id.'__'.$packqty.'__'.$itemNratio;
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
		
		if (is_duplicate_field( "cost_sheet_no", "qc_mst", "style_ref='$dup_styleRef' and season_id='$dup_season_id' and buyer_id='$dup_buyer_id' and entry_form=552 and status_active=1 and is_deleted=0")==1)
		{
			echo "11**".$msg; 
			die;
		}
		//all_table qc_template, qc_mst, qc_style_temp_data, qc_fabric_dtls, qc_fabric_string_data, qc_cons_rate_dtls, qc_tot_cost_summary, qc_item_cost_summary, qc_meeting_mst, qc_meeting_person, qc_meeting_dtls
		//echo $txtItemDes_1_1_1; die;
		
		$mst_id=return_next_id("id", "qc_mst", 1);
		
		$qcno=$_SESSION['logic_erp']['user_id'].'000'.$mst_id;
		$sql_mst=sql_select("select id, company_id, location_id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, inquery_id, style_ref, buyer_id, uom, cons_basis, season_id, season_year, brand_id, style_des, prod_dept, prod_code, margin, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, buyer_remarks, option_remarks, article, fab_process_loss_method, acc_process_loss_method from qc_mst where qc_no='$hid_qc_no'");
		
		//echo "select id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, style_ref, buyer_id, cons_basis, season_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date from qc_mst where id='$update_id'";
		$cbo_company_id=$sql_mst[0][csf('company_id')];
		$cbo_location_id=$sql_mst[0][csf('location_id')];
		$cbo_temp_id=$sql_mst[0][csf('temp_id')];
		$txt_temp_id=$sql_mst[0][csf('lib_item_id')];
		$txtmarign=$sql_mst[0][csf('margin')];
		$cbo_buyer_id=$sql_mst[0][csf('buyer_id')];
		$cbouom=$sql_mst[0][csf('uom')];
		$cbo_cons_basis_id=$sql_mst[0][csf('cons_basis')];
		$cbo_product_department=$sql_mst[0][csf('prod_dept')];
		$txt_product_code=$sql_mst[0][csf('prod_code')];
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
		$txt_inquery_id=$sql_mst[0][csf('inquery_id')];
		$cbo_season_year=$sql_mst[0][csf('season_year')];
		$cbo_brand=$sql_mst[0][csf('brand_id')];
		$txt_article=$sql_mst[0][csf('article')];
		$txt_fab_process_loss_method=$sql_mst[0][csf('fab_process_loss_method')];
		$txt_acc_process_loss_method=$sql_mst[0][csf('acc_process_loss_method')];
		
		$old_id=$sql_mst[0][csf('id')];
		
		$field_array_mst="id, company_id, location_id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, inquery_id, style_ref, buyer_id, uom, cons_basis, season_id, season_year, brand_id, style_des, prod_dept, prod_code, margin, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, pre_cost_sheet_id, revise_no, option_id, buyer_remarks, option_remarks, article, qc_no, entry_form, fab_process_loss_method, acc_process_loss_method, inserted_by, insert_date, status_active, is_deleted";
		
		//$txt_temp_id=explode(",",str_replace("'","",$txt_temp_id));
		//asort($txt_temp_id);
		//$txt_temp_id="'".implode(",",$txt_temp_id)."'";
		$option_id=0;
		$revise_no=0;
		$autoCostSheetNo=$_SESSION['logic_erp']['user_id'].str_pad($mst_id,8,'0',STR_PAD_LEFT);
		
		$data_array_mst="(".$mst_id.",'".$cbo_company_id."','".$cbo_location_id."',".$mst_id.",'".$autoCostSheetNo."','".$cbo_temp_id."','".$txt_temp_id."','".$txt_inquery_id."',".$txt_styleRef.",'".$cbo_buyer_id."','".$cbouom."','".$cbo_cons_basis_id."',".$cbo_season_id.",'".$cbo_season_year."','".$cbo_brand."','".$txt_styleDesc."','".$cbo_product_department."','".$txt_product_code."','".$txtmarign."','".$cbo_subDept_id."','".$txt_delivery_date."','".$txt_exchangeRate."','".$txt_offerQty."','".$txt_quotedPrice."','".$txt_tgtPrice."','".$cbo_stage_id."','".$txt_costingDate."','".$hid_qc_no."','".$revise_no."','".$option_id."','".$txt_costing_remarks."','".$txt_option_remarks."','".$txt_article."','".$qcno."',552,'".$txt_fab_process_loss_method."','".$txt_acc_process_loss_method."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		
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
		$sql_consrate=sql_select("select id, mst_id, item_id, type, particular_type_id, consumption, ex_percent, tot_cons, is_calculation, nb_cons, infant_cons, toddler_cons, bigger_cons, bigbigger_cons, rate, value, fab_id, description, use_for, fc_per, body_part_id, supp_ref, remarks, uom, color_type_id, gsmweight, fabwidth, dznyds, dznkg, rate_data from qc_cons_rate_dtls where mst_id='$hid_qc_no' order by id ASC");
		
		//$field_array_consrate="id, mst_id, item_id, type, particular_type_id, consumption, ex_percent, tot_cons, rate, value, fab_id, description, use_for, fc_per, body_part_id, supp_ref, inserted_by, insert_date, status_active, is_deleted";
		
		
		$field_array_consrate="id, mst_id, item_id, type, particular_type_id, consumption, ex_percent, tot_cons, is_calculation, nb_cons, infant_cons, toddler_cons, bigger_cons, bigbigger_cons, rate, value, fab_id, description, use_for, fc_per, body_part_id, supp_ref, remarks, uom, inserted_by, insert_date, status_active, is_deleted, color_type_id, gsmweight, fabwidth, dznyds, dznkg, rate_data";
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
			$ratePop=$rowConsrate[csf('is_calculation')];
			
			$nb_cons=$rowConsrate[csf('nb_cons')];
			$infant_cons=$rowConsrate[csf('infant_cons')];
			$toddler_cons=$rowConsrate[csf('toddler_cons')];
			$bigger_cons=$rowConsrate[csf('bigger_cons')];
			$bigbigger_cons=$rowConsrate[csf('bigbigger_cons')];
			
			$rateData=$rowConsrate[csf('rate_data')];
			$rate=$rowConsrate[csf('rate')];
			$tot_val=$rowConsrate[csf('value')];
			$fab_id=$rowConsrate[csf('fab_id')];
			$description=$rowConsrate[csf('description')];
			$use_for=$rowConsrate[csf('use_for')];
			$fc_per=$rowConsrate[csf('fc_per')];
			$body_part_id=$rowConsrate[csf('body_part_id')];
			$supp_ref=$rowConsrate[csf('supp_ref')];
			$consRemarks=$rowConsrate[csf('remarks')];
			$consuom=$rowConsrate[csf('uom')];
			$fabcolortype=$rowConsrate[csf('color_type_id')];
			
			$gsmweight=$rowConsrate[csf('gsmweight')];
			$fabwidth=$rowConsrate[csf('fabwidth')];
			$dznyds=$rowConsrate[csf('dznyds')];
			$dznkg=$rowConsrate[csf('dznkg')];
			
			if ($add_comma_cons_rate!=0) $data_array_cons_rate .=",";
			$data_array_cons_rate .="(".$cons_rate_id.",".$qcno.",'".$item_id."','".$type."','".$head_id."','".$cons."','".$ex_percent."','".$tot_cons."','".$ratePop."','".$nb_cons."','".$infant_cons."','".$toddler_cons."','".$bigger_cons."','".$bigbigger_cons."','".$rate."','".$tot_val."','".$fab_id."','".$description."','".$use_for."','".$fc_per."','".$body_part_id."','".$supp_ref."','".$consRemarks."','".$consuom."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,'".$fabcolortype."','".$gsmweight."','".$fabwidth."','".$dznyds."','".$dznkg."','".$rateData."')";
			
			$cons_rate_id=$cons_rate_id+1;
			$add_comma_cons_rate++;
		}
		
		//echo $data_array_cons_rate; 
		//die;
		$tc_sum_id=return_next_id("id", "qc_tot_cost_summary", 1);
		$sql_tot_cost=sql_select("select id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_wash_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, tot_rmg_ratio, commercial_per, commercial_cost, operating_exp, knitting_time, makeup_time, finishing_time from qc_tot_cost_summary where mst_id='$hid_qc_no'");
		
		$field_array_tot_cost="id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_wash_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, tot_rmg_ratio, commercial_per, commercial_cost, operating_exp, knitting_time, makeup_time, finishing_time, inserted_by, insert_date, status_active, is_deleted";
		
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
		$totLbTstCst_td=$sql_tot_cost[0][csf('tot_lab_test_cost')];
		$totMissCst_td=$sql_tot_cost[0][csf('tot_miscellaneous_cost')];
		$totOtherCst_td=$sql_tot_cost[0][csf('tot_other_cost')];
		$totCommlCst_td=$sql_tot_cost[0][csf('commercial_cost')];
		$totCommCst_td=$sql_tot_cost[0][csf('tot_commission_cost')];
		$totCost_td=$sql_tot_cost[0][csf('tot_cost')];
		$totalFob_td=$sql_tot_cost[0][csf('tot_fob_cost')];
		$totalRmg_td=$sql_tot_cost[0][csf('tot_rmg_ratio')];
		$txt_optexp=$sql_tot_cost[0][csf('operating_exp')];
		$totknittime_td=$sql_tot_cost[0][csf('knitting_time')];
		$totmakeuptime_td=$sql_tot_cost[0][csf('makeup_time')];
		$totfinishtime_td=$sql_tot_cost[0][csf('finishing_time')];
		
		
		$data_array_tot_cost="(".$tc_sum_id.",".$qcno.",'".$cbo_buyer_agent."','".$cbo_agent_location."','".$txt_noOfPack."',0,'".$cmPop."','".$txt_lumSum_cost."','".$txt_commPer."','".$totFab_td."','".$totSpc_td."','".$totWash_td."','".$totAcc_td."','".$totCm_td."','".$totFriCst_td."','".$totLbTstCst_td."','".$totMissCst_td."','".$totOtherCst_td."','".$totCommCst_td."','".$totCost_td."','".$totalFob_td."','".$totalRmg_td."','".$txt_commlPer."','".$totCommlCst_td."','".$txt_optexp."','".$totknittime_td."','".$totmakeuptime_td."','".$totfinishtime_td."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		//echo $data_array_tot_cost; die;
		$sql_item_tot=sql_select("select id, mst_id, item_id, fabric_cost, sp_operation_cost, wash_cost, accessories_cost, cpm, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs, rmg_ratio, commercial_cost, operating_exp, knitting_time, makeup_time, finishing_time from qc_item_cost_summary where mst_id='$hid_qc_no' order by id ASC");
		
		$field_array_item_tot="id, mst_id, item_id, fabric_cost, sp_operation_cost, wash_cost, accessories_cost, cpm, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs, rmg_ratio, commercial_cost, operating_exp, knitting_time, makeup_time, finishing_time, inserted_by, insert_date, status_active, is_deleted";
		
		$item_cost_id=return_next_id("id", "qc_item_cost_summary", 1);
		$add_comma_item_tot=0; $data_array_item_tot="";
		foreach($sql_item_tot as $rowItem_tot)
		{
			$fab_td=$spOpe_td=$wash_td=$acc_td=$txtCpm=$txtSmv=$txtEff=$txtCmCost=$txtFriCost=$txtLtstCost=$txtMissCost=$txtOtherCost=$txtCommCost=$fobT_td=$tot_item_id=$rmg_ratio=$txtoptexp=$txtknittingtime=$txtmakeuptime=$txtfinishingtime=0;
			$fab_td=$rowItem_tot[csf('fabric_cost')];
			$spOpe_td=$rowItem_tot[csf('sp_operation_cost')];
			$wash_td=$rowItem_tot[csf('wash_cost')];
			$acc_td=$rowItem_tot[csf('accessories_cost')];
			$txtCpm=$rowItem_tot[csf('cpm')];
			$txtSmv=$rowItem_tot[csf('smv')];
			$txtEff=$rowItem_tot[csf('efficiency')];
			$txtCmCost=$rowItem_tot[csf('cm_cost')];
			$txtFriCost=$rowItem_tot[csf('frieght_cost')];
			$txtLtstCost=$rowItem_tot[csf('lab_test_cost')];
			$txtMissCost=$rowItem_tot[csf('miscellaneous_cost')];
			$txtOtherCost=$rowItem_tot[csf('other_cost')];
			$txtCommlCost=$rowItem_tot[csf('commercial_cost')];
			$txtCommCost=$rowItem_tot[csf('commission_cost')];
			$fobT_td=$rowItem_tot[csf('fob_pcs')];
			$rmg_ratio=$rowItem_tot[csf('rmg_ratio')];
			$txtoptexp=$rowItem_tot[csf('operating_exp')];
			$txtknittingtime=$rowItem_tot[csf('knitting_time')];
			$txtmakeuptime=$rowItem_tot[csf('makeup_time')];
			$txtfinishingtime=$rowItem_tot[csf('finishing_time')];
			$tot_item_id=$rowItem_tot[csf('item_id')];
			
			if ($add_comma_item_tot!=0) $data_array_item_tot .=",";
			$data_array_item_tot .="(".$item_cost_id.",".$qcno.",'".$tot_item_id."','".$fab_td."','".$spOpe_td."','".$wash_td."','".$acc_td."','".$txtCpm."','".$txtSmv."','".$txtEff."','".$txtCmCost."','".$txtFriCost."','".$txtLtstCost."','".$txtMissCost."','".$txtOtherCost."','".$txtCommCost."','".$fobT_td."','".$rmg_ratio."','".$txtCommlCost."','".$txtoptexp."','".$txtknittingtime."','".$txtmakeuptime."','".$txtfinishingtime."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			
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
	$user_id=$_SESSION['logic_erp']['user_id'];
	$user_level=$_SESSION['logic_erp']["user_level"];
	$sql_data=sql_select("Select cost_sheet_no, buyer_id, season_id, department_id, temp_id, lib_item_id, style_ref, offer_qty, revise_no, option_id, delivery_date, uom from qc_mst where qc_no='$qc_no' ");
	
	$uom=$sql_data[0][csf('uom')];
	$temp_id=$sql_data[0][csf('temp_id')];
	
	$sql_tmp="select tuid, temp_id, item_id1, ratio1, status_active from qc_template where status_active=1 and is_deleted=0 and tuid=$temp_id and lib_item_id='0' order by item_id1, ratio1 ASC";
	$sql_tmp_res=sql_select($sql_tmp);
	$mst_temp_arr=array();
	foreach($sql_tmp_res as $row)
	{
		$mst_temp_arr[$row[csf('tuid')]].=$row[csf('item_id1')].'**'.$row[csf('ratio1')].'**'.$row[csf('status_active')].'__';
	}
	$template_name_arr=array(); $gmttempname='';
	foreach($mst_temp_arr as $temp_id=>$tmp_data)
	{
		$template_data=array_filter(explode('__',$tmp_data));
		$template_name='';
		foreach($template_data as $temp_val)
		{
			$ex_tmp_val=explode('**',$temp_val);
			$item_id1=''; $ratio1=0;
			
			$item_id1=$ex_tmp_val[0]; 
			$ratio1=$ex_tmp_val[1]; 
			
			if($gmttempname=='') $gmttempname=$garments_item[$item_id1].'::'.$ratio1; else $gmttempname.=','.$garments_item[$item_id1].'::'.$ratio1;
		}
		$template_name_arr[$temp_id]=$gmttempname;
	}
	unset($sql_tmp_res);
	//echo $gmttempname;
	$gmt_type_arr=array(1=>'Pcs',2=>'Set');
	$gmt_itm_count=count(explode(',',$template_name_arr[$sql_data[0][csf('temp_id')]]));
	$selected_gmt_type=0;
	if($gmt_itm_count>1) $selected_gmt_type=2; else $selected_gmt_type=1;
	
	/*$qcCons_from=return_field_value("excut_source","variable_order_tracking","variable_list=68 and is_deleted=0 and status_active=1 order by id","excut_source");
	//$qcCons_from=2;excut_source=2 and 
	if($qcCons_from==2) $cons_cond="tot_cons";//Tot Cons
	else $cons_cond="consumption";//Tot Cons*/
	$qcCons_from=2;
	
	$sql_summ=sql_select("select no_of_pack, tot_fob_cost from qc_tot_cost_summary where mst_id='$qc_no' and status_active=1 and is_deleted=0");
	//$sql_cons_rate="select id, item_id, type, particular_type_id, consumption, unit, is_calculation, rate, rate_data, value from qc_cons_rate_dtls where mst_id='$data[0]' and status_active=1 and is_deleted=0 order by id asc";
	$sql_cons=sql_select("select item_id, uom, (nb_cons+infant_cons+toddler_cons+bigger_cons+bigbigger_cons) as tot_cons from qc_cons_rate_dtls where mst_id='$qc_no' and type=1 and status_active=1 and is_deleted=0");//type ='1' and
	//echo "select item_id, uom, tot_cons from qc_cons_rate_dtls where mst_id='$qc_no' and type=1 and status_active=1 and is_deleted=0";
	$item_wise_cons_arr=array();
	foreach($sql_cons as $cRow)
	{
		$item_wise_cons_arr[$cRow[csf("uom")]]['qty']+=$cRow[csf("tot_cons")];
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
		var permission='<?=$permission; ?>'; 
		var qcCons_from='<?=$qcCons_from; ?>'; 
		function fnc_confirm_entry( operation )
		{
			if(operation==3)
			{
				var fab_cons_kg=$("#txtFabConkg_bom").val();
				var fab_cons_mtr=$("#txtFabConmtr_bom").val();
				var fab_cons_yds=$("#txtFabConyds_bom").val();
				var fab_amount=$("#txtFabCst_bom").val();
				var sp_oparation_amount=$("#txtSpOpa_bom").val();
				var wash_amount=$("#txtWash_bom").val();
				var acc_amount=$("#txtAcc_bom").val();
				var fright_amount=$("#txtFrightCst_bom").val();
				var lab_amount=$("#txtLabCst_bom").val();
				var misce_amount=$("#txtMiscCst_bom").val();
				var other_amount=$("#txtOtherCst_bom").val();
				var comm_amount=$("#txtCommCst_bom").val();
				var fob_amount=$("#txtFobDzn_bom").val();
				var cm_amount=$("#txtCmCst_bom").val();
				var rmg_ratio=$("#txtPack_bom").val();
							
				//var temp_id=$('#txtItem_id').val();
				//var split_tmep_id=temp_id.split(',');
				var ab=0;
				var qc_fab_kg=0; var qc_fab_mtr=0; var qc_fab_yds=0; var qc_fab_amt=0; var qc_sp_amt=0; var qc_wash_amt=0; var qc_acc_amt=0; var qc_fri_amt=0; var qc_lab_amt=0; var qc_misce_amt=0; var qc_other_amt=0; var qc_comm_amt=0; var qc_fob_amt=0; var qc_cm_amt=0; var qc_rmg_amt=0;
				/*for(j=1; j<=split_tmep_id.length; j++)
				{*/
					//var itm_id=trim(split_tmep_id[ab]);
					
					qc_fab_kg+=$("#txtFabConkg").val()*1;
					qc_fab_mtr+=$("#txtFabConmtr").val()*1;
					qc_fab_yds+=$("#txtFabConyds").val()*1;
					qc_fab_amt+=$("#txtFabCst").val()*1;
					qc_sp_amt+=$("#txtSpOpa").val()*1;
					qc_wash_amt+=$("#txtWash").val()*1;
					qc_acc_amt+=$("#txtAcc").val()*1;
					qc_fri_amt+=$("#txtFrightCst").val()*1;
					qc_lab_amt+=$("#txtLabCst").val()*1;
					qc_misce_amt+=$("#txtMiscCst").val()*1;
					qc_other_amt+=$("#txtOtherCst").val()*1;
					qc_comm_amt+=$("#txtCommCst").val()*1;
					qc_fob_amt+=$("#txtFobDzn").val()*1;
					qc_cm_amt+=$("#txtCmCst").val()*1;
					qc_rmg_amt+=$("#txtPack").val()*1;
					
					ab++;
				//}
				//alert(qc_fab_amt); return;
				var job_no=$("#txt_job_style").val();
				if(job_no!="")
				{
					if(qc_fab_kg<fab_cons_kg)
					{
						alert("BOM Fab. Cons. Kg is Greater Than QC.");
						return;
					}
					else if(qc_fab_mtr<fab_cons_mtr)
					{
						alert("BOM Fab. Cons. Mtr is Greater Than QC.");
						return;
					}
					else if(qc_fab_yds<fab_cons_yds)
					{
						alert("BOM Fab. Cons. Yds is Greater Than QC.");
						return;
					}
					else if(qc_fab_amt<fab_amount)
					{
						alert("BOM Fab. Amount is Greater Than QC.");
						return;
					}
					else if(qc_sp_amt<sp_oparation_amount)
					{
						alert("BOM Special Opera. Amount is Greater Than QC.");
						return;
					}
					else if(qc_wash_amt<wash_amount)
					{
						alert("BOM Wash Amount is Greater Than QC.");
						return;
					}
					else if(qc_acc_amt<acc_amount)
					{
						alert("BOM Accessories Amount is Greater Than QC.");
						return;
					}
					else if(qc_fri_amt<fright_amount)
					{
						alert("BOM Frieght Amount is Greater Than QC.");
						return;
					}
					else if(qc_lab_amt<lab_amount)
					{
						alert("BOM Lab - Test Amount is Greater Than QC.");
						return;
					}
					/*else if(qc_misce_amt<misce_amount)
					{
						alert("BOM Misce. Amount is Greater Than QC.");
						return;
					}
					else if(qc_other_amt<other_amount)
					{
						alert("BOM Other Amount is Greater Than QC.");
						return;
					}*/
					else if(qc_comm_amt<comm_amount)
					{
						alert("BOM Commis. Amount is Greater Than QC.");
						return;
					}
					/*else if(qc_fob_amt<fob_amount)
					{
						alert("BOM FOB/DZN Amount is Greater Than QC.");
						return;
					}
					else if(qc_cm_amt<cm_amount)
					{
						alert("BOM CM Amount is Greater Than QC.");
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
			/*var temp_id=$('#txtItem_id').val();
			var split_tmep_id=temp_id.split(',');	
			var data_mst=''; var data_dtls=''; var k=0;
			for(i=1; i<=split_tmep_id.length; i++)
			{
				var itm_id=trim(split_tmep_id[k]);
				data_dtls+=get_submitted_data_string('txtitemid_'+itm_id+'*txtdtlsupid_'+itm_id+'*txtFabConkg_'+itm_id+'*txtFabConmtr_'+itm_id+'*txtFabConyds_'+itm_id+'*txtFabCst_'+itm_id+'*txtSpOpa_'+itm_id+'*txtAcc_'+itm_id+'*txtFrightCst_'+itm_id+'*txtLabCst_'+itm_id+'*txtMiscCst_'+itm_id+'*txtOtherCst_'+itm_id+'*txtCommCst_'+itm_id+'*txtFobDzn_'+itm_id+'*txtCpm_'+itm_id+'*txtSmv_'+itm_id+'*txtCmCst_'+itm_id+'*txtPack_'+itm_id,"../../",2);
				k++;
			}*/
			
			var data_mst="action=save_update_delete_confirm_style&operation="+operation+get_submitted_data_string('txt_costSheet_id*txtConfirm_id*txtItem_id*txt_confirm_style*txt_order_qty*txt_confirm_fob*txt_ship_date*txt_job_id*cbo_approved_status*cbo_ready_approve*txtitemid*txtdtlsupid*txtFabConkg*txtFabConmtr*txtFabConyds*txtFabCst*txtSpOpa*txtWash*txtAcc*txtFrightCst*txtLabCst*txtMiscCst*txtOtherCst*txtCommCst*txtFobDzn*txtCpm*txtSmv*txtCmCst*txtPack',"../../",2);
			
			var data=data_mst;
			//alert(data); //return;
			freeze_window(operation);
			http.open("POST","short_quotation_v5_controller.php",true);
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
					get_php_form_data($('#txt_costSheet_id').val()+'__'+$('#txtItem_id').val(),'populate_confirm_style_form_data','short_quotation_v5_controller');
					//show_list_view('','stage_data_list','stage_data_div','short_quotation_v5_controller','');
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
			page_link='short_quotation_v5_controller.php?action=style_tag_popup&data='+data;
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
				var str_data=return_global_ajax_value( job_no, 'budgete_cost_validate', '', 'short_quotation_v5_controller');
				
				var spdata=str_data.split("##");
				var fab_cons_kg=spdata[0]; var fab_cons_mtr=spdata[1]; var fab_cons_yds=spdata[2]; var fab_amount=spdata[3]; var sp_oparation_amount=spdata[4]; var acc_amount=spdata[5]; var fright_amount=spdata[6]; var lab_amount=spdata[7]; var misce_amount=spdata[8]; var other_amount=spdata[9]; var comm_amount=spdata[10]; var fob_amount=spdata[11]; var cm_amount=spdata[12]; var rmg_ratio=spdata[13]; var wash_amount=spdata[14]
				
				if(qcCons_from==1)
				{
					var bomTotCost=(number_format(fab_amount,4)*1)+(number_format(sp_oparation_amount,4)*1)+(number_format(wash_amount,4)*1)+(number_format(acc_amount,4)*1)+(number_format(fright_amount,4)*1)+(number_format(lab_amount,4)*1)+(number_format(misce_amount,4)*1)+(number_format(other_amount,4)*1)+(number_format(comm_amount,4)*1);
					
					var bomCost='Fab='+fab_amount+'+ SP='+sp_oparation_amount+'+ WASH='+wash_amount+'+ ACC='+acc_amount+'+ FR='+fright_amount+'+ Lab='+lab_amount+'+ Mis='+misce_amount+'+ Others='+other_amount+'+ Comm='+comm_amount+'= Total='+bomTotCost;
					
					$("#txtFobDzn_bom").attr('title',bomCost);
					
					var bom_tot_cm=(number_format(fob_amount,4)*1)-(number_format(bomTotCost,4)*1);
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
				$("#txtWash_bom").val(wash_amount);
				$("#txtAcc_bom").val(acc_amount);
				$("#txtFrightCst_bom").val(fright_amount);
				$("#txtLabCst_bom").val(lab_amount);
				$("#txtMiscCst_bom").val(misce_amount);
				$("#txtOtherCst_bom").val(other_amount);
				$("#txtCommCst_bom").val(comm_amount);
				$("#txtFobDzn_bom").val(fob_amount);
				$("#txtCmCst_bom").val(bom_tot_cm);
				$("#txtPack_bom").val(rmg_ratio);
			}
		}
		
		function fnc_total_calculate()
		{
			var temp_id=$('#txtItem_id').val();
			//var split_tmep_id=temp_id.split(',');
			var ab=0;
			var qc_fab_kg=0; var qc_fab_mtr=0; var qc_fab_yds=0; var qc_fab_amt=0; var qc_sp_amt=0; var qc_wash_amt=0; var qc_acc_amt=0; var qc_fri_amt=0; var qc_lab_amt=0; var qc_misce_amt=0; var qc_other_amt=0; var qc_comm_amt=0; var qc_fob_amt=0; var qc_cpm_amt=0; var qc_smv_amt=0; var qc_cm_amt=0; var qc_rmg_amt=0;
			/*for(j=1; j<=split_tmep_id.length; j++)
			{*/
				var item_tot_amount=0; var item_tot_cm=0;
				//var itm_id=trim(split_tmep_id[ab]);
				
				qc_fab_kg+=$("#txtFabConkg").val()*1;
				qc_fab_mtr+=$("#txtFabConmtr").val()*1;
				qc_fab_yds+=$("#txtFabConyds").val()*1;
				qc_fab_amt+=$("#txtFabCst").val()*1;
				qc_sp_amt+=$("#txtSpOpa").val()*1;
				qc_wash_amt+=$("#txtWash").val()*1;
				qc_acc_amt+=$("#txtAcc").val()*1;
				qc_fri_amt+=$("#txtFrightCst").val()*1;
				qc_lab_amt+=$("#txtLabCst").val()*1;
				qc_misce_amt+=$("#txtMiscCst").val()*1;
				qc_other_amt+=$("#txtOtherCst").val()*1;
				qc_comm_amt+=$("#txtCommCst").val()*1;
				qc_fob_amt+=$("#txtFobDzn").val()*1;
				
				qc_cpm_amt+=$("#txtCpm").val()*1;
				qc_smv_amt+=$("#txtSmv").val()*1;
				
				qc_cm_amt+=$("#txtCmCst").val()*1;
				qc_rmg_amt+=$("#txtPack").val()*1;
				
				item_tot_amount=($("#txtFabCst").val()*1)+($("#txtSpOpa").val()*1)+($("#txtWash").val()*1)+($("#txtAcc").val()*1)+($("#txtFrightCst").val()*1)+($("#txtLabCst").val()*1)+($("#txtMiscCst").val()*1)+($("#txtOtherCst").val()*1)+($("#txtCommCst").val()*1);
				
				var itemCost='Fab='+$("#txtFabCst").val()+'+ SP='+$("#txtSpOpa").val()+'+ Wash='+$("#txtWash").val()+'+ ACC='+$("#txtAcc").val()+'+ FR='+$("#txtFrightCst").val()+'+ Lab='+$("#txtLabCst").val()+'+ Mis='+$("#txtMiscCst").val()+'+ Others='+$("#txtOtherCst").val()+'+ Comm='+$("#txtCommCst").val()+'= Total='+item_tot_amount;
				
				var fobdzn=$("#txtFobDzn").val();
				
				item_tot_cm=number_format(fobdzn,4,'.','')-number_format(item_tot_amount,4,'.','');
				//alert(fobdzn+'-'+item_tot_cm)
				$('#txtFobDzn').attr('title',itemCost);
				
				$("#txtCmCst").val( number_format(item_tot_cm,4,'.',''))
				
				ab++;
			//}
			
			$("#txtFabConkg_qc").val( number_format(qc_fab_kg,4,'.','') );
			$("#txtFabConmtr_qc").val( number_format(qc_fab_mtr,4,'.','') );
			$("#txtFabConyds_qc").val( number_format(qc_fab_yds,4,'.','') );
			$("#txtFabCst_qc").val( number_format(qc_fab_amt,4,'.','') );
			$("#txtSpOpa_qc").val( number_format(qc_sp_amt,4,'.','') );
			$("#txtWash_qc").val( number_format(qc_wash_amt,4,'.','') );
			$("#txtAcc_qc").val( number_format(qc_acc_amt,4,'.','') );
			$("#txtFrightCst_qc").val( number_format(qc_fri_amt,4,'.','') );
			$("#txtLabCst_qc").val( number_format(qc_lab_amt,4,'.','') );
			$("#txtMiscCst_qc").val( number_format(qc_misce_amt,4,'.','') );
			$("#txtOtherCst_qc").val( number_format(qc_other_amt,4,'.','') );
			$("#txtCommCst_qc").val( number_format(qc_comm_amt,4,'.','') );
			$("#txtFobDzn_qc").val( number_format(qc_fob_amt,4,'.','') );
			
			$("#txtCpm_qc").val( number_format(qc_cpm_amt,4,'.','') );
			$("#txtSmv_qc").val( number_format(qc_smv_amt,4,'.','') );
			
			$("#txtPack_qc").val( number_format(qc_rmg_amt,4,'.','') );
			
			var total_amount=qc_fab_amt+qc_sp_amt+qc_wash_amt+qc_acc_amt+qc_fri_amt+qc_lab_amt+qc_misce_amt+qc_other_amt+qc_comm_amt;
			var cal_cm=qc_fob_amt-total_amount;
			$("#txtCmCst_qc").val( number_format(cal_cm,4,'.','') );
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
			var txtSmv=$("#txtSmv").val()*1;
			var txtCm=$("#txtCmCst").val()*1;
			
			var cppm=( txtCm/txtSmv);
			var cppm_nf=number_format((cppm/12),4,'.','');
			if(cppm_nf=="nan") cppm_nf=0;
			$("#txtCpm").val( cppm_nf );
			
			fnc_total_calculate();
		}
		
		function openmypage_unapprove_request()
		{
			var costSheet_id=document.getElementById('txt_costSheet_id').value;
			var txt_un_appv_request=document.getElementById('txt_un_appv_request').value;
			var data=costSheet_id+"_"+txt_un_appv_request;
			var title = 'Un Approval Request';
			var page_link = 'short_quotation_v5_controller.php?data='+data+'&action=unapp_request_popup';
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
			//var strdata=str.split("_");
			//var item_id=strdata[0];
			var validatetype=str;
			
			var tdfabkg=$('#tdfabkg').text()*1;
			var tdfabmtr=$('#tdfabmtr').text()*1;
			var tdfabyds=$('#tdfabyds').text()*1;
			var tdfabamt=$('#tdfabamt').text()*1;
			var tdspamt=$('#tdspamt').text()*1;
			var tdwashamt=$('#tdwashamt').text()*1;
			var tdaccamt=$('#tdaccamt').text()*1;
			var tdfriamt=$('#tdfriamt').text()*1;
			var tdlabamt=$('#tdlabamt').text()*1;
			var tdmisamt=$('#tdmisamt').text()*1;
			var tdothamt=$('#tdothamt').text()*1;
			var tdcomamt=$('#tdcomamt').text()*1;
			var tdfobamt=$('#tdfobamt').text()*1;
			var tdcppmamt=$('#tdcppmamt').text()*1;
			var tdsmvamt=$('#tdsmvamt').text()*1;
			var tdcmamt=$('#tdcmamt').text()*1;
			var tdrmgpcs=$('#tdrmgpcs').text()*1;
			//alert(tdcmamt+'-'+($("#txtCmCst").val()*1))
			if(qcCons_from==2)
			{
				if( validatetype==1 && tdfabkg < ($("#txtFabConkg").val()*1) )
				{
					alert("QC BOM Limit (Fab. Cons. Kg) is Greater Than QC!!!");
					$("#txtFabConkg").val(tdfabkg);
					//return;
				}
				if( validatetype==2 && tdfabmtr < ($("#txtFabConmtr").val()*1) )
				{
					alert("QC BOM Limit (Fab. Cons. Mtr) is Greater Than QC!!!");
					$("#txtFabConmtr").val(tdfabmtr);
					//return;
				}
				if( validatetype==3 && tdfabyds < ($("#txtFabConyds").val()*1) )
				{
					alert("QC BOM Limit (Fab. Cons. Yds) is Greater Than QC!!!");
					$("#txtFabConyds").val(tdfabyds);
					//return;
				}
				if( validatetype==4 && tdfabamt < ($("#txtFabCst").val()*1) )
				{
					alert("QC BOM Limit (Fab. Amt.) is Greater Than QC!!!");
					$("#txtFabCst").val(tdfabamt);
					//return;
				}
				if( validatetype==5 && tdspamt < ($("#txtSpOpa").val()*1) )
				{
					alert("QC BOM Limit (Special Opera.) is Greater Than QC!!!");
					$("#txtSpOpa").val(tdspamt);
					//return;
				}
				if( validatetype==6 && tdaccamt < ($("#txtAcc").val()*1) )
				{
					alert("QC BOM Limit (Access.) is Greater Than QC!!!");
					$("#txtAcc").val(tdaccamt);
					//return;
				}
				if( validatetype==7 && tdfriamt < ($("#txtFrightCst").val()*1) )
				{
					alert("QC BOM Limit (Frieght Cost) is Greater Than QC!!!");
					$("#txtFrightCst").val(tdfriamt);
					//return;
				}
				if( validatetype==8 && tdlabamt < ($("#txtLabCst").val()*1) )
				{
					alert("QC BOM Limit (Lab - Test) is Greater Than QC!!!");
					$("#txtLabCst").val(tdlabamt);
					//return;
				}
				if( validatetype==9 && tdmisamt < ($("#txtMiscCst").val()*1) )
				{
					alert("QC BOM Limit (Misce.) is Greater Than QC!!!");
					$("#txtMiscCst").val(tdmisamt);
					//return;
				}
				if( validatetype==10 && tdothamt < ($("#txtOtherCst").val()*1) )
				{
					alert("QC BOM Limit (Other Cost) is Greater Than QC!!!");
					$("#txtOtherCst").val(tdothamt);
					//return;
				}
				if( validatetype==11 && tdcomamt < ($("#txtCommCst").val()*1) )
				{
					alert("QC BOM Limit (Commis.) is Greater Than QC!!!");
					$("#txtCommCst").val(tdcomamt);
					//return;
				}
				if( validatetype==12 && tdfobamt < ($("#txtFobDzn").val()*1) )
				{
					alert("QC BOM Limit (FOB ($/DZN)) is Greater Than QC!!!");
					$("#txtFobDzn").val(tdfobamt);
					//return;
				}
				if( validatetype==13 && tdcppmamt < ($("#txtCpm").val()*1) )
				{
					alert("QC BOM Limit (CPPM) is Greater Than QC!!!");
					$("#txtCpm").val(tdcppmamt);
					//return;
				}
				if( validatetype==14 && tdsmvamt < ($("#txtSmv").val()*1) )
				{
					alert("QC BOM Limit (SMV) is Greater Than QC!!!");
					$("#txtSmv").val(tdsmvamt);
					//return;
				}
				if( validatetype==15 && (tdcmamt*1) < ($("#txtCmCst").val()*1) )
				{
					alert("QC BOM Limit (CM) is Greater Than QC!!!");
					$("#txtCmCst").val(tdcmamt);
					//return;
				}
				if( validatetype==16 && tdrmgpcs < ($("#txtPack").val()*1) )
				{
					alert("QC BOM Limit (RMG Qty(Pcs)) is Greater Than QC!!!");
					$("#txtPack").val(tdrmgpcs);
					//return;
				}
				if( validatetype==17 && tdwashamt < ($("#txtWash").val()*1) )
				{
					alert("QC BOM Limit (Wash) is Greater Than QC!!!");
					$("#txtWash").val(tdwashamt);
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
                    <td width="120"><? echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-Select Buyer-", $sql_data[0][csf('buyer_id')], "load_drop_down( 'short_quotation_v5_controller', this.value, 'load_drop_down_season_conf', 'season_conf_td'); load_drop_down( 'short_quotation_v5_controller',this.value, 'load_drop_down_sub_depConf', 'subConf_td' );",1 ); ?></td>
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
            	<th width="100">Item</th>
                <th width="45">Fab. Cons. Kg</th>
                <th width="45">Fab. Cons. Mtr</th>
                <th width="45">Fab. Cons. Yds</th>
                <th width="45">Fab. Amount</th>
                <th width="45">Special Opera.</th>
                <th width="45">Wash</th>
                <th width="45">Access.</th>
                <th width="45">Frieght Cost</th>
                <th width="45">Lab - Test</th>
                <th width="45">Misce.</th>
                <th width="45">Other Cost</th>
                <th width="45">Commis.</th>
                <th width="45">FOB ($/DZN)</th>
                <th width="45" title="((CPM*100)/Efficiency)">CPPM</th>
                <th width="45">SMV</th>
                <th width="45">CM</th>
                <th>RMG Qty(Pcs)</th>
            </thead>
			<?
            $sql_item_summ="select count(id) as noofrow, sum(fabric_cost) as fabric_cost, sum(sp_operation_cost) as sp_operation_cost, sum(wash_cost) as wash_cost, sum(accessories_cost) as accessories_cost, sum(cpm) as cpm, sum(smv) as smv, sum(efficiency) as efficiency, sum(cm_cost) as cm_cost, sum(frieght_cost) as frieght_cost, sum(lab_test_cost) as lab_test_cost, sum(miscellaneous_cost) as miscellaneous_cost, sum(other_cost) as other_cost, sum(commission_cost) as commission_cost, sum(fob_pcs) as fob_pcs from qc_item_cost_summary where mst_id='$qc_no' and status_active=1 and is_deleted=0";
			//echo $sql_item_summ;
            $sql_result_item_summ=sql_select($sql_item_summ); $z=1;
            foreach($sql_result_item_summ as $rowItemSumm)
            {
				if ($z%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if ($z%2==0) $bgcolorN="#E9F50F"; else $bgcolorN="#D078F6";
				$rowItemSumm[csf("cpm")]=$rowItemSumm[csf("cpm")]/$rowItemSumm[csf("noofrow")];
				$rowItemSumm[csf("smv")]=$rowItemSumm[csf("smv")]/$rowItemSumm[csf("noofrow")];
				$cppm=0;
				if($rowItemSumm[csf("efficiency")]!=0 && $rowItemSumm[csf("cpm")]!=0) $cppm=(($rowItemSumm[csf("cpm")]*100)/$rowItemSumm[csf("efficiency")]);
				
				if($cppm=="nan") $cppm=0;
				//$item_id=0;
				//$item_id=$rowItemSumm[csf("item_id")];
				
                ?>
                <tr id="trVal_<?=$z; ?>" bgcolor="<?=$bgcolor; ?>">
                    <td style="word-break:break-all"><?=$gmttempname; ?></td>
                    <td align="right" id="tdfabkg"><? echo number_format($item_wise_cons_arr[12]['qty'],4,'.',''); ?></td>
                    <td align="right" id="tdfabmtr"><? echo number_format($item_wise_cons_arr[23]['qty'],4,'.',''); ?></td>
                    <td align="right" id="tdfabyds"><? echo number_format($item_wise_cons_arr[27]['qty'],4,'.',''); ?></td>
                    <td align="right" id="tdfabamt"><? echo number_format($rowItemSumm[csf("fabric_cost")],4,'.',''); ?></td>
                    <td align="right" id="tdspamt"><? echo number_format($rowItemSumm[csf("sp_operation_cost")],4,'.',''); ?></td>
                    <td align="right" id="tdwashamt"><? echo number_format($rowItemSumm[csf("wash_cost")],4); ?></td>
                    <td align="right" id="tdaccamt"><? echo number_format($rowItemSumm[csf("accessories_cost")],4,'.',''); ?></td>
                    <td align="right" id="tdfriamt"><? echo number_format($rowItemSumm[csf("frieght_cost")],4,'.',''); ?></td>
                    <td align="right" id="tdlabamt"><? echo number_format($rowItemSumm[csf("lab_test_cost")],4,'.',''); ?></td>
                    <td align="right" id="tdmisamt"><? echo number_format($rowItemSumm[csf("miscellaneous_cost")],4,'.',''); ?></td>
                    <td align="right" id="tdothamt"><? echo number_format($rowItemSumm[csf("other_cost")],4,'.',''); ?></td>
                    <td align="right" id="tdcomamt"><? echo number_format($rowItemSumm[csf("commission_cost")],4,'.',''); ?></td>
                    <td align="right" id="tdfobamt"><? echo number_format($rowItemSumm[csf("fob_pcs")],4,'.',''); ?></td>
                    
                    <td align="right" id="tdcppmamt" title="((CPM*100)/Efficiency)"><? if($cppm==0) echo "0"; else echo number_format($cppm,4,'.',''); ?></td>
                    <td align="right" id="tdsmvamt"><? echo number_format($rowItemSumm[csf("smv")],4,'.',''); ?></td>
                    
                    <td align="right" id="tdcmamt"><? echo number_format($rowItemSumm[csf("cm_cost")],4,'.',''); ?></td>
                    <td align="right" id="tdrmgpcs">&nbsp;<? //echo number_format($rowItemSumm[csf("sp_operation_cost")],4); ?></td>
                </tr>
                <tr id="tr_<?=$z; ?>" bgcolor="<?=$bgcolorN; ?>">
                    <td>QC BOM Limit<input style="width:40px;" type="hidden" name="txtitemid" id="txtitemid" value="<?=$item_id; ?>" /><input style="width:40px;" type="hidden" name="txtdtlsupid" id="txtdtlsupid" value="" /></td>
                    <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtFabConkg" id="txtFabConkg" value="<?=number_format($item_wise_cons_arr[12]['qty'],4,'.',''); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit(1);" <?=$disable; ?> /></td>
                    
                    <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtFabConmtr" id="txtFabConmtr" value="<?=number_format($item_wise_cons_arr[23]['qty'],4,'.',''); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit(2);" <?=$disable; ?> /></td>
                    <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtFabConyds" id="txtFabConyds" value="<?=number_format($item_wise_cons_arr[27]['qty'],4,'.',''); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit(3);" <?=$disable; ?>/></td>
                    <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtFabCst" id="txtFabCst" value="<?=number_format($rowItemSumm[csf("fabric_cost")],4,'.',''); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit(4);" <?=$disable; ?> /></td>
                    <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtSpOpa" id="txtSpOpa" value="<?=number_format($rowItemSumm[csf("sp_operation_cost")],4,'.',''); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit(5);" <?=$disable; ?> /></td>
                    <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtWash" id="txtWash" value="<?=number_format($rowItemSumm[csf("wash_cost")],4); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit();" <?=$disable; ?> /></td>
                    <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtAcc" id="txtAcc" value="<?=number_format($rowItemSumm[csf("accessories_cost")],4,'.',''); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit(6);" <?=$disable; ?> /></td>
                    <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtFrightCst" id="txtFrightCst" value="<?=number_format($rowItemSumm[csf("frieght_cost")],4,'.',''); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit(7);" <?=$disable; ?> /></td>
                    <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtLabCst" id="txtLabCst" value="<?=number_format($rowItemSumm[csf("lab_test_cost")],4,'.',''); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit(8);" <?=$disable; ?> /></td>
                    <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtMiscCst" id="txtMiscCst" value="<?=number_format($rowItemSumm[csf("miscellaneous_cost")],4,'.',''); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit(9);" <?=$disable; ?> /></td>
                    
                    <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtOtherCst" id="txtOtherCst" value="<?=number_format($rowItemSumm[csf("other_cost")],4,'.',''); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit(10);" <?=$disable; ?> /></td>
                    <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtCommCst" id="txtCommCst" value="<?=number_format($rowItemSumm[csf("commission_cost")],4,'.',''); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit(11);" <?=$disable; ?> /></td>
                    <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtFobDzn" id="txtFobDzn" value="<?=number_format($rowItemSumm[csf("fob_pcs")],4,'.',''); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit(12);" <?=$disable; ?> /></td>
                    
                    <td title="((CPM*100)/Efficiency)"><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtCpm" id="txtCpm" value="<? if($cppm==0) echo "0"; else echo number_format($cppm,4,'.',''); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit(13);" disabled <? //echo $disable; ?> /></td>
                    <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtSmv" id="txtSmv" value="<?=number_format($rowItemSumm[csf("smv")],4); ?>" onChange="fnc_total_calculate();" onBlur="fnc_cppm_cal(); fncqclimit(14);" <?=$disable; ?> /></td>
                    
                    <td title="FOB ($/DZN)-All Cost"><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtCmCst" id="txtCmCst" value="<?=number_format($rowItemSumm[csf("cm_cost")],4,'.',''); ?>" onChange="fnc_total_calculate();" onBlur="fncqclimit(15);" <?=$disable; ?> /></td>
                    <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtPack" id="txtPack" value="" onChange="fnc_total_calculate();" onBlur="fncqclimit(16);" <?=$disable; ?> />&nbsp;</td>
                </tr>
                <?
				$z++;
            }
			//$sql="select fab_cons_kg, fab_cons_yds, fab_amount, sp_oparation_amount, acc_amount, fright_amount, lab_amount, misce_amount, other_amount, comm_amount, fob_amount, cm_amount, rmg_ratio from qc_confirm_dtls where cost_sheet_id='$cost_sheet_id' and status_active=1 and is_deleted=0";
			//$dataArr=sql_select($sql);
            ?>
        </table>
        
        <table width="925" cellspacing="0" border="1" rules="all" class="rpt_table">
        	<tr id="tr_qc" bgcolor="#CCFFCC">
                <td width="100"><font color="#0000FF">QC Limit Total</font></td>
                <td width="45"><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtFabConkg_qc" id="txtFabConkg_qc" value="" disabled /></td>
                <td width="45"><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtFabConmtr_qc" id="txtFabConmtr_qc" value="" disabled /></td>
                <td width="45"><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtFabConyds_qc" id="txtFabConyds_qc" value="" disabled /></td>
                <td width="45"><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtFabCst_qc" id="txtFabCst_qc" value="" disabled /></td>
                <td width="45"><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtSpOpa_qc" id="txtSpOpa_qc" value="" disabled /></td>
                <td width="45"><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtWash_qc" id="txtWash_qc" value="" disabled /></td>
                
                <td width="45"><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtAcc_qc" id="txtAcc_qc" value="" disabled /></td>
                <td width="45"><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtFrightCst_qc" id="txtFrightCst_qc" value="" disabled /></td>
                <td width="45"><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtLabCst_qc" id="txtLabCst_qc" value="" disabled /></td>
                <td width="45"><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtMiscCst_qc" id="txtMiscCst_qc" value="" disabled /></td>
                <td width="45"><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtOtherCst_qc" id="txtOtherCst_qc" value="" disabled /></td>
                <td width="45"><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtCommCst_qc" id="txtCommCst_qc" value="" disabled /></td>
                <td width="45"><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtFobDzn_qc" id="txtFobDzn_qc" value="" disabled /></td>
                
                <td width="45"><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtCpm_qc" id="txtCpm_qc" value="" disabled /></td>
                <td width="45"><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtSmv_qc" id="txtSmv_qc" value="" disabled /></td>
                
                <td width="45"><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtCmCst_qc" id="txtCmCst_qc" value="" disabled /></td>
                <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtPack_qc" id="txtPack_qc" value="" disabled />&nbsp;</td>
            </tr>
        	<tr id="tr_bom" bgcolor="#CCCCCC">
                <td width="100"><font color="#FF0000">Current BOM</font></td>
                <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtFabConkg_bom" id="txtFabConkg_bom" value="" readonly /></td>
                <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtFabConmtr_bom" id="txtFabConmtr_bom" value="" readonly /></td>
                <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtFabConyds_bom" id="txtFabConyds_bom" value="" readonly /></td>
                <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtFabCst_bom" id="txtFabCst_bom" value="" readonly /></td>
                <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtSpOpa_bom" id="txtSpOpa_bom" value="" readonly /></td>
                <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtWash_bom" id="txtWash_bom" value="" readonly /></td>
                <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtAcc_bom" id="txtAcc_bom" value="" readonly /></td>
                <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtFrightCst_bom" id="txtFrightCst_bom" value="" readonly /></td>
                <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtLabCst_bom" id="txtLabCst_bom" value="" readonly /></td>
                <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtMiscCst_bom" id="txtMiscCst_bom" value="" readonly /></td>
                <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtOtherCst_bom" id="txtOtherCst_bom" value="" readonly /></td>
                <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtCommCst_bom" id="txtCommCst_bom" value="" readonly /></td>
                <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtFobDzn_bom" id="txtFobDzn_bom" value="" readonly /></td>
                
                <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtCpm_bom" id="txtCpm_bom" value="" readonly /></td>
                <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtSmv_bom" id="txtSmv_bom" value="" readonly /></td>
                
                <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtCmCst_bom" id="txtCmCst_bom" value="" readonly /></td>
                <td><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtPack_bom" id="txtPack_bom" value="" readonly />&nbsp;</td>
            </tr>
        </table>
            </div>
        </form>
	</div>
    </body> 
    <script>get_php_form_data($('#txt_costSheet_id').val()+'__'+$('#txtItem_id').val(),'populate_confirm_style_form_data','short_quotation_v5_controller'); fnc_bom_data_load(); fnc_total_calculate(); fnc_select();</script>          
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
		if($_SESSION['logic_erp']["user_level"]!=2)//Admin Check
		{
			if($team_dtls_arr[$insert_user_id]=='')
			{
				echo "6**"."Update restricted, This Information is Update Only team Leader or member.";
				disconnect($con);die;
			}
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
		
		$dtls_field_arr="id, mst_id, cost_sheet_id, item_id, fab_cons_kg, fab_cons_mtr, fab_cons_yds, fab_amount, sp_oparation_amount, wash_amount, acc_amount, fright_amount, lab_amount, misce_amount, other_amount, comm_amount, fob_amount, cppm_amount, smv_amount, cm_amount, rmg_ratio, inserted_by, insert_date";
		//echo "10**";
		$itm_id=explode(",",str_replace("'","",$txtItem_id));
		
		$up_job_field="quotation_id";
		$up_job_data="".$txt_costSheet_id."";
		//echo "10**".$up_job_field.''.$up_job_data; die;
		$k=0; $add_comma=0; $dtls_data_arr=""; 
		/*for($i=1; $i<=count($itm_id); $i++)
		{
			$item_id=0;
			$item_id=trim($itm_id[$k]);*/
			//'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id,
			/*$txtitemid='txtitemid';
			$txtdtlsupid='txtdtlsupid';
			$txtFabConkg='txtFabConkg';
			$txtFabConmtr='txtFabConmtr';
			$txtFabConyds='txtFabConyds';
			$txtFabCst='txtFabCst';
			$txtSpOpa='txtSpOpa';
			$txtAcc='txtAcc';
			$txtFrightCst='txtFrightCst';
			$txtLabCst='txtLabCst';
			$txtMiscCst='txtMiscCst';
			$txtOtherCst='txtOtherCst';
			$txtCommCst='txtCommCst';
			$txtFobDzn='txtFobDzn';
			$txtCpm='txtCpm';
			$txtSmv='txtSmv';
			
			$txtCmCst='txtCmCst';
			$txtPack='txtPack';*/
			
			//if ($add_comma!=0) $dtls_data_arr .=",";
			$dtls_data_arr ="(".$confirm_dtls_id.",".$confirm_mst_id.",".$txt_costSheet_id.",".$txtitemid.",".$txtFabConkg.",".$txtFabConmtr.",".$txtFabConyds.",".$txtFabCst.",".$txtSpOpa.",".$txtWash.",".$txtAcc.",".$txtFrightCst.",".$txtLabCst.", ".$txtMiscCst.", ".$txtOtherCst.", ".$txtCommCst.", ".trim($txtFobDzn).",".$txtCpm.",".$txtSmv.",".$txtCmCst.",".$txtPack.",".$user_id.",'".$pc_date_time."')";
			
			//$confirm_dtls_id=$confirm_dtls_id+1;
			//$add_comma++;
			//$k++;
		//}
		//echo "10**INSERT INTO qc_confirm_mst (".$mst_field_arr.") VALUES ".$mst_data_arr; 
		
		//echo "10**INSERT INTO qc_confirm_dtls (".$dtls_field_arr.") VALUES ".$dtls_data_arr; 
		//die;/**/
		
		
		$rID=sql_insert("qc_confirm_mst",$mst_field_arr,$mst_data_arr,1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID1=sql_insert("qc_confirm_dtls",$dtls_field_arr,$dtls_data_arr,1);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		
		//echo "10**".$rID.'='.$rID1.'='.$flag; die;
		
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
		
		$dtls_field_arr="fab_cons_kg*fab_cons_mtr*fab_cons_yds*fab_amount*sp_oparation_amount*wash_amount*acc_amount*fright_amount*lab_amount*misce_amount*other_amount*comm_amount*fob_amount*cppm_amount*smv_amount*cm_amount*rmg_ratio*updated_by*update_date";
		//echo "10**";
		$itm_id=explode(",",str_replace("'","",$txtItem_id));
		//print_r($itm_id);
		//echo count($itm_id);
		/*$k=0; $add_comma=0; $dtls_data_arr=array(); 
		for($i=1; $i<=count($itm_id); $i++)
		{
			$item_id=0;
			$item_id=trim($itm_id[$k]);
			//'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id, 
			$txtitemid='txtitemid';
			$txtdtlsupid='txtdtlsupid';
			$txtFabConkg='txtFabConkg';
			$txtFabConmtr='txtFabConmtr';
			$txtFabConyds='txtFabConyds';
			$txtFabCst='txtFabCst';
			$txtSpOpa='txtSpOpa';
			$txtAcc='txtAcc';
			$txtFrightCst='txtFrightCst';
			$txtLabCst='txtLabCst';
			$txtMiscCst='txtMiscCst';
			$txtOtherCst='txtOtherCst';
			$txtCommCst='txtCommCst';
			$txtFobDzn='txtFobDzn';
			$txtCpm='txtCpm';
			$txtSmv='txtSmv';
			$txtCmCst='txtCmCst';
			$txtPack='txtPack';*/
			
			$id_arr[]=str_replace("'",'',$txtdtlsupid);
							
			$dtls_data_arr[str_replace("'",'',$txtdtlsupid)] =explode("*",("".$txtFabConkg."*".$txtFabConmtr."*".$txtFabConyds."*".$txtFabCst."*".$txtSpOpa."*".$txtWash."*".$txtAcc."*".$txtFrightCst."*".$txtLabCst."*".$txtMiscCst."*".$txtOtherCst."*".$txtCommCst."*".$txtFobDzn."*".$txtCpm."*".$txtSmv."*".$txtCmCst."*".$txtPack."*'".$user_id."'*'".$pc_date_time."'"));
			/*$k++;
		}*/
		//print_r($dtls_data_arr);
		$flag=1;
		
		$rID=sql_update("qc_confirm_mst",$mst_field_arr,$mst_data_arr,"id","".$txtConfirm_id."",1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		//echo "10**".bulk_update_sql_statement("qc_confirm_dtls", "id",$dtls_field_arr,$dtls_data_arr,$id_arr ); die;
		$rID1=execute_query(bulk_update_sql_statement("qc_confirm_dtls", "id",$dtls_field_arr,$dtls_data_arr,$id_arr ));
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		
		if(str_replace("'",'',$txt_job_id)!='')
		{
			$rIDJob=sql_update("wo_po_details_master",$up_job_field,$up_job_data,"id","".$txt_job_id."",1);
			if($rIDJob==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		//echo "10**".$rID.'=='.$rID1.'=='.$rIDJob.'=='.$flag; die;
		
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
		
		$dtls_field_arr="fab_cons_kg*fab_cons_mtr*fab_cons_yds*fab_amount*sp_oparation_amount*wash_amount*acc_amount*fright_amount*lab_amount*misce_amount*other_amount*comm_amount*fob_amount*cppm_amount*smv_amount*cm_amount*rmg_ratio*updated_by*update_date";
		//echo "10**";
		$itm_id=explode(",",str_replace("'","",$txtItem_id));
		//print_r($itm_id);
		//echo count($itm_id);
		/*$k=0; $add_comma=0; $dtls_data_arr=""; 
		for($i=1; $i<=count($itm_id); $i++)
		{
			$item_id=0;
			$item_id=trim($itm_id[$k]);
			//'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id+'*'+itm_id, 
			$txtitemid='txtitemid';
			$txtdtlsupid='txtdtlsupid';
			$txtFabConkg='txtFabConkg';
			$txtFabConmtr='txtFabConmtr';
			$txtFabConyds='txtFabConyds';
			$txtFabCst='txtFabCst';
			$txtSpOpa='txtSpOpa';
			$txtAcc='txtAcc';
			$txtFrightCst='txtFrightCst';
			$txtLabCst='txtLabCst';
			$txtMiscCst='txtMiscCst';
			$txtOtherCst='txtOtherCst';
			$txtCommCst='txtCommCst';
			$txtFobDzn='txtFobDzn';
			$txtCpm='txtCpm';
			$txtSmv='txtSmv';
			$txtCmCst='txtCmCst';
			$txtPack='txtPack';*/
			
			$id_arr[]=str_replace("'",'',$txtdtlsupid);
							
			$dtls_data_arr[str_replace("'",'',$txtdtlsupid)] =explode("*",("".$txtFabConkg."*".$txtFabConmtr."*".$txtFabConyds."*".$txtFabCst."*".$txtSpOpa."*".$txtWash."*".$txtAcc."*".$txtFrightCst."*".$txtLabCst."*".$txtMiscCst."*".$txtOtherCst."*".$txtCommCst."*".$txtFobDzn."*".$txtCpm."*".$txtSmv."*".$txtCmCst."*".$txtPack."*'".$user_id."'*'".$pc_date_time."'"));
			/*$k++;
		}*/
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
	
	$sql_confirm_dtls=sql_select("select id, mst_id, cost_sheet_id, item_id, fab_cons_kg, fab_cons_mtr, fab_cons_yds, fab_amount, sp_oparation_amount, wash_amount, acc_amount, fright_amount, lab_amount, misce_amount, other_amount, comm_amount, fob_amount, cppm_amount, smv_amount, cm_amount, rmg_ratio from qc_confirm_dtls where cost_sheet_id='$ex_data[0]'");
	foreach($sql_confirm_dtls as $row_dtls)
	{
		echo "$('#txtitemid').val('".$row_dtls[csf("item_id")]."');\n";
		echo "$('#txtFabConkg').val('".$row_dtls[csf("fab_cons_kg")]."');\n";
		echo "$('#txtFabConmtr').val('".$row_dtls[csf("fab_cons_mtr")]."');\n";
		echo "$('#txtFabConyds').val('".$row_dtls[csf("fab_cons_yds")]."');\n";
		echo "$('#txtFabCst').val('".$row_dtls[csf("fab_amount")]."');\n";
		echo "$('#txtSpOpa').val('".$row_dtls[csf("sp_oparation_amount")]."');\n";
		echo "$('#txtWash').val('".$row_dtls[csf("wash_amount")]."');\n";
		echo "$('#txtAcc').val('".$row_dtls[csf("acc_amount")]."');\n";
		echo "$('#txtFrightCst').val('".$row_dtls[csf("fright_amount")]."');\n";
		echo "$('#txtLabCst').val('".$row_dtls[csf("lab_amount")]."');\n";
		echo "$('#txtMiscCst').val('".$row_dtls[csf("misce_amount")]."');\n";
		echo "$('#txtOtherCst').val('".$row_dtls[csf("other_amount")]."');\n";
		echo "$('#txtCommCst').val('".$row_dtls[csf("comm_amount")]."');\n";
		echo "$('#txtFobDzn').val('".$row_dtls[csf("fob_amount")]."');\n";
		
		echo "$('#txtCpm').val('".$row_dtls[csf("cppm_amount")]."');\n";
		echo "$('#txtSmv').val('".$row_dtls[csf("smv_amount")]."');\n";
		
		echo "$('#txtCmCst').val('".$row_dtls[csf("cm_amount")]."');\n";
		echo "$('#txtPack').val('".$row_dtls[csf("rmg_ratio")]."');\n";
		
		echo "$('#txtdtlsupid').val('".$row_dtls[csf("id")]."');\n";
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
		$fab_amount=$sp_oparation_amount=$wash_amt=$acc_amount=$fright_amount=$lab_amount=$misce_amount=$other_amount=$comm_amount=$fob_amount=$cm_amount=$rmg_ratio=0;
		
		foreach($dataArr as $row)
		{
			$sp_cost=0;
			$sp_cost=$row[csf("embel_cost")]+$row[csf("wash_cost")];
			$fab_amount+=$row[csf("fabric_cost")];
			$sp_oparation_amount+=$row[csf("embel_cost")];
			$wash_amt+=$row[csf("wash_cost")];
			$acc_amount+=$row[csf("trims_cost")];
			$fright_amount+=$row[csf("freight")];
			$lab_amount+=$row[csf("lab_test")];
			
			$misce_amount+=$row[csf("misce_amount")];
			$other_amount+=$row[csf("other_amount")];
			
			$comm_amount+=$row[csf("commission")];
			$fob_amount+=$row[csf("price_dzn")];
			$cm_amount+=$row[csf("cm_cost")];
			$rmg_ratio+=$row[csf("rmg_ratio")];
		}
		$str_data=$fab_cons_kg."##".$fab_cons_mtr."##".$fab_cons_yds."##".$fab_amount."##".$sp_oparation_amount."##".$acc_amount."##".$fright_amount."##".$lab_amount."##".$misce_amount."##".$other_amount."##".$comm_amount."##".$fob_amount."##".$cm_amount."##".$rmg_ratio."##".$wash_amt;
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
					<? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-Select Company-", $cbo_company_name,"load_drop_down( 'short_quotation_v5_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_pop_td' );",'' ); ?>
                </td>
        		<td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_id", 130, $blank_array,'', 1, "-Select Buyer-" ); ?></td>
                <td><? echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", "", "",0 );//date('Y') ?></td>
                <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:50px"></td>
                
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td> 
                <td align="center">
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_style').value, 'create_po_search_list_view', 'search_div', 'short_quotation_v5_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
		http.open("POST","short_quotation_v5_controller.php",true);
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
                        	 <? echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name",1, "-- All Buyer--",$selected,"load_drop_down( 'short_quotation_v5_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'short_quotation_v5_controller',this.value, 'load_drop_down_sub_dep', 'sub_td' );",0 ); ?>
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
				http.open("POST","short_quotation_v5_controller.php",true);
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
					show_list_view(type,'agent_location_data_list','agent_location_data_div','short_quotation_v5_controller','');
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
    <script>show_list_view(type,'agent_location_data_list','agent_location_data_div','short_quotation_v5_controller','');</script>          
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
	echo  create_list_view ( "list_view", "Agent/Location Name", "200","200","150",1, "select tuid, agent_location from lib_agent_location where type='$data' and status_active=1 and is_deleted=0 order by id desc", "get_php_form_data", "tuid","'load_php_data_to_form_agent_location'", 1, "0", $arr, "agent_location","short_quotation_v5_controller", 'setFilterGrid("list_view",-1);','0' );
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
	extract($_REQUEST);
	echo load_html_head_contents("Quick Costing Print", "../../../", 1, 1,'','','');
	//extract(check_magic_quote_gpc( $process ));
	//echo $data; die;
	$data=explode('*',$data);
	$qc_no=$data[0];
	$costSheetNo=$data[1];
	//if($qc_no=="") $qc_no=''; else $qc_no=" and a.qc_no=".$qc_no."";
	//if($costSheetNo=="") $costSheetNo=''; else $costSheetNo=" and a.cost_sheet_no=".$costSheetNo."";

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_name_arr=return_library_array( "select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0",'id','supplier_name');
	$season_name_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0",'id','season_name');
	$brand_name_arr=return_library_array( "select id, brand_name from lib_buyer_brand where status_active=1 and is_deleted=0",'id','brand_name');	
	
	$mstSql = "SELECT id, company_id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, inquery_id, style_ref, buyer_id, uom, cons_basis, season_id, season_year, brand_id, style_des, prod_dept, margin, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, pre_cost_sheet_id, revise_no, option_id, buyer_remarks, option_remarks, gauge, no_of_ends, sample_color, sample_size, sample_range from qc_mst where qc_no='$qc_no' and status_active=1 and is_deleted=0";
	//echo $mstSql; die;
	$master_data=sql_select($mstSql);
	$temp_id=$master_data[0][csf('temp_id')];
	$style_description=$master_data[0][csf('style_des')];
	$cbo_company_id=$master_data[0][csf('company_id')];
	$marchendiserRemarks=nl2br($master_data[0][csf('buyer_remarks')]); //str_replace("_//_", "\n", $master_data[0][csf('buyer_remarks')]);
	$margin=$master_data[0][csf('margin')];
	
	$count_td=explode(",",$temp_id);
	
	if($count_td==1) $uomtxt="/PCS";
	else if($count_td>1) $uomtxt="/Set";
	else $uomtxt="";
	
	$sql_tmp="select tuid, temp_id, item_id1, ratio1, status_active from qc_template where status_active=1 and is_deleted=0 and lib_item_id='0' and tuid='$temp_id' order by id ASC";
	$sql_tmp_res=sql_select($sql_tmp);
	$mst_temp_arr=array();
	foreach($sql_tmp_res as $row)
	{
		$mst_temp_arr[$row[csf('tuid')]].=$row[csf('item_id1')].'**'.$row[csf('ratio1')].'**'.$row[csf('status_active')].'__';
	}
	$template_name_arr=array(); $packQty=0;
	foreach($mst_temp_arr as $temp_id=>$tmp_data)
	{
		$template_data=array_filter(explode('__',$tmp_data));
		$template_name='';
		foreach($template_data as $temp_val)
		{
			$ex_tmp_val=explode('**',$temp_val);
			$item_id1=''; $ratio1=0;
			
			$item_id1=$ex_tmp_val[0]; 
			$ratio1=$ex_tmp_val[1]; 
			
			if($template_name=='') $template_name=$garments_item[$item_id1].'::'.$ratio1; else $template_name.=','.$garments_item[$item_id1].'::'.$ratio1;
			$packQty+=$ratio1;
		}
		$template_name_arr[$temp_id]=$template_name;
	}
	unset($sql_tmp_res);
	
	$lib_item_id=$master_data[0][csf('lib_item_id')];
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$image_location = sql_select("SELECT image_location from common_photo_library where file_type=1 and form_name='short_quotation_v2' and master_tble_id='$qc_no' ");
	
	$sqlSum="select id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_wash_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, commercial_cost, tot_commission_cost, tot_cost, tot_fob_cost, operating_exp, commercial_per, commision_per from qc_tot_cost_summary where mst_id='$qc_no' and status_active=1 and is_deleted=0";
	$sqlSumData=sql_select($sqlSum);
	$fobTot=$sqlSumData[0][csf('tot_fob_cost')];
	$commercial_per=$sqlSumData[0][csf('commercial_per')];
	$commision_per=$sqlSumData[0][csf('commision_per')];
	
	$costHeadSummArr=array(1=>"Yarn", 2=>"Accessories", 3=>"Special Operation", 4=>"Wash", 5=>"Lab Test", 6=>"Sample Cost", 7=>"Cost Of Making [CM]", 8=>"Commercial Cost", 9=>"Commission", 11=>"Margin $", 12=>"FOB");
	
	$costValueSummArr=array(1=>$sqlSumData[0][csf('tot_fab_cost')], 2=>$sqlSumData[0][csf('tot_accessories_cost')], 3=>$sqlSumData[0][csf('tot_sp_operation_cost')], 4=>$sqlSumData[0][csf('tot_wash_cost')], 5=>$sqlSumData[0][csf('tot_lab_test_cost')], 6=>"", 7=>$sqlSumData[0][csf('tot_cm_cost')], 8=>$sqlSumData[0][csf('commercial_cost')], 9=>$sqlSumData[0][csf('tot_commission_cost')], 10=>"", 11=>$margin, 12=>$sqlSumData[0][csf('tot_fob_cost')]);
	?>
		<table width="930">
            <tr>
                <td width="70" align="center"> 
                    <img src='../../../<?=$imge_arr[str_replace("'","",$master_data[0][csf('company_id')])]; ?>' height='70' width='200' />
                </td>
                <td>
                    <table width="860" cellspacing="0">
                        <tr>
                            <td align="center" style="font-size:20px; margin-left:150px; float:left;"><strong><?=$company_arr[$master_data[0][csf('company_id')]]; ?></strong></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:16px; margin-left:170px; float:left;"><strong><?=$data[2]; ?></strong></td>
                        </tr>
                        <tr class="form_caption">
                            <td align="center" style="font-size:14px; margin-left:160px; float:left;"><strong>Costing Date: </strong><?=change_date_format($master_data[0][csf('costing_date')]); ?></td>  
                        </tr>
                    </table>
                </td>
            </tr>
		</table>
		<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:930px;" rules="all">
			<tr>
            	<td width="110">Buyer : </td>
				<td width="130"><?=$buyer_arr[$master_data[0][csf('buyer_id')]]; ?></td>
				<td width="110">Costing Date : </td>
				<td width="130"><?=change_date_format($master_data[0][csf('costing_date')]); ?></td>
                <td width="110">Cost Sheet No : </td>
				<td width="130"><?=$master_data[0][csf('cost_sheet_no')]; ?></td>
                <td width="110">Delivery Date : </td>
				<td><?=change_date_format($master_data[0][csf('delivery_date')],'yyyy-mm-dd','-'); ?></td>
            </tr>
			<tr>
            	<td>Season : </td>
				<td><?=$season_name_arr[$master_data[0][csf('season_id')]]; ?></td>
                <td>Season Year : </td>
				<td><?=$master_data[0][csf('season_year')]; ?></td>
                <td>Brand : </td>
				<td><?=$brand_name_arr[$master_data[0][csf('brand_id')]] ?></td>
                <td>Style Ref. : </td>
				<td><?=$master_data[0][csf('style_ref')]; ?></td>
			</tr>
            <tr>
            	<td>Product Dept. : </td>
				<td><?=$product_dept[$master_data[0][csf('prod_dept')]]; ?></td>
                <td>Garments Item : </td>
				<td colspan="3"><?=$template_name_arr[$master_data[0][csf('temp_id')]]; ?></td>
                <td>Style Description : </td>
				<td><?=$master_data[0][csf('style_des')]; ?></td>
			</tr>
            <tr>
				<td>Gauge : </td>
				<td><?=$gauge_arr[$master_data[0][csf('gauge')]]; ?></td>
                <td>No Of Ends : </td>
				<td><?=$master_data[0][csf('no_of_ends')]; ?></td>
                <td>Sample Color : </td>
				<td><?=$color_library[$master_data[0][csf('sample_color')]]; ?></td>
                <td>Sample Size : </td>
				<td><?=$size_library[$master_data[0][csf('sample_size')]]; ?></td>
			</tr>
			<tr>
            	<td>Sample Range : </td>
				<td align="center"><?=$master_data[0][csf('sample_range')]; ?></td>
				<td>Offer Qty : </td>
				<td align="center"><?=$master_data[0][csf('offer_qty')]; ?></td>
                <td>FOB <?=$uomtxt; ?> : </td>
				<td align="center"><?=$fobTot; ?></td>
                <td>Quoted Price ($) : </td>
				<td><?=$master_data[0][csf('quoted_price')]; ?></td>
			</tr>
		</table>
        <br>
        <?
		/*$sqlMeeting="select meeting_no, meeting_date, meeting_time, remarks from qc_meeting_mst where status_active=1 and is_deleted=0 and mst_id='$qc_no'";
		
		$sqlMeetingData=sql_select($sqlMeeting);*/
		?>
        <table cellpadding="1" cellspacing="1" style="width:930px;">
        	<tr>
            	<td width="460" valign="top">
                	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:460px;" rules="all">
                    	<tr>
                        	<td align="center"><b>Merchandiser Remarks :</b></td>
                        </tr>
                        <?
						$excomments=explode("\n",$marchendiserRemarks);
						//print_r($excomments);
						foreach($excomments as $comments)
						{
							?>
							<tr>
                        		<td style="word-break:break-all"><?=$comments; ?></td>
                        	</tr>
                            <?
						}
						?>
                        
                    </table>
            	</td>
                <td>
                	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:470px;" rules="all">
                        <tr>
                        	<td style="word-break:break-all">
                            <? foreach ($image_location as $row) { ?>
                                <img src='../../../<?=$row[csf("image_location")]; ?>' height='100' width='100' />
                            <? } ?>
                            </td>
                        </tr>			
                    </table>
                </td>
            </tr>
        </table>
        
		<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:930px; margin-top: 10px" rules="all">
			<thead>
            	<tr>
                	<th colspan="12">Fabric Details</th>
                </tr>
            	<tr>
                    <th width="120" rowspan="2">Body Part</th>
                    <th width="250" rowspan="2">Fab. Description</th>
                    <th width="50" rowspan="2">GSM</th>
                    <th width="100" rowspan="2">Use For</th>
                    <th width="40" rowspan="2">UOM</th>
                    <th colspan="5">Cons/Dzn</th>
                    <th width="40" rowspan="2">Avg. Rate</th>
                    <th rowspan="2">Value</th>
                </tr>
                <tr>
                <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                	<th width="50"><font style="font-size:10px; font-weight:100"><?=$qsname; ?></font></th>
                <? } ?>
                </tr>
			</thead>
			<? 
			$consSql="select id, mst_id, item_id, type, particular_type_id, nb_cons, infant_cons, toddler_cons, bigger_cons, bigbigger_cons, rate, value, fab_id, description, use_for, body_part_id, uom, gsmweight, supp_ref from qc_cons_rate_dtls where mst_id='$qc_no' and status_active=1 and is_deleted=0 order by id ASC";
			$consSqlData=sql_select($consSql); $fabArr=array(); $accArr=array(); $embArr=array(); 
			foreach ($consSqlData as $frow)
			{
				if(($frow[csf('rate')]*1)>0)
				{
					if($frow[csf('type')]==1)//fab
						$fabArr[$frow[csf('item_id')]][$frow[csf('id')]]['str']=$frow[csf('body_part_id')].'**'.$frow[csf('description')].'**'.$frow[csf('gsmweight')].'**'.$frow[csf('use_for')].'**'.$frow[csf('uom')].'**'.$frow[csf('nb_cons')].'**'.$frow[csf('infant_cons')].'**'.$frow[csf('toddler_cons')].'**'.$frow[csf('bigger_cons')].'**'.$frow[csf('bigbigger_cons')].'**'.$frow[csf('rate')].'**'.$frow[csf('value')];
					else if($frow[csf('type')]==4)//acc
						$accArr[$frow[csf('id')]]['str']=$frow[csf('particular_type_id')].'**'.$frow[csf('description')].'**'.$frow[csf('supp_ref')].'**'.$frow[csf('uom')].'**'.$frow[csf('nb_cons')].'**'.$frow[csf('infant_cons')].'**'.$frow[csf('toddler_cons')].'**'.$frow[csf('bigger_cons')].'**'.$frow[csf('bigbigger_cons')].'**'.$frow[csf('rate')].'**'.$frow[csf('value')];
					else if($frow[csf('type')]==2)//sp
						$embArr[$frow[csf('id')]]['str']=$frow[csf('particular_type_id')].'**'.$frow[csf('fab_id')].'**'.$frow[csf('body_part_id')].'**'.$frow[csf('nb_cons')].'**'.$frow[csf('infant_cons')].'**'.$frow[csf('toddler_cons')].'**'.$frow[csf('bigger_cons')].'**'.$frow[csf('bigbigger_cons')].'**'.$frow[csf('rate')].'**'.$frow[csf('value')];
					else if($frow[csf('type')]==3)//wash
						$embArr[$frow[csf('id')]]['str']=$frow[csf('type')].'**'.$frow[csf('particular_type_id')].'**'.$frow[csf('body_part_id')].'**'.$frow[csf('nb_cons')].'**'.$frow[csf('infant_cons')].'**'.$frow[csf('toddler_cons')].'**'.$frow[csf('bigger_cons')].'**'.$frow[csf('bigbigger_cons')].'**'.$frow[csf('rate')].'**'.$frow[csf('value')];
				}
			}
			//print_r($embArr);
			
			foreach ($fabArr as $gitemid=>$itemdata)
			{
				?>
				<tr>
                	<th colspan="12" align="center"><b>Gmts. Item : <?=$garments_item[$gitemid]; ?></b></th>
                </tr>
                <? $itemtot=0;
				foreach ($itemdata as $rowid=>$data)
				{
					$exdata=array_filter(explode('**',$data['str']));
					?>
					<tr>
						<td width="120"><?=$body_part[$exdata[0]]; ?></td>
						<td width="250" style="word-break:break-all"><?=$exdata[1]; ?></td>
						<td width="50" style="word-break:break-all"><?=$exdata[2]; ?></td>
						<td width="100" style="word-break:break-all"><?=$exdata[3]; ?></td>
						<td width="40"><?=$unit_of_measurement[$exdata[4]]; ?></td>
                        
						<td width="50" align="right"><? if(($exdata[5]*1)>0) echo number_format($exdata[5],4); else echo ""; ?></td>
						<td width="50" align="right"><? if(($exdata[6]*1)>0) echo number_format($exdata[6],4); else echo ""; ?></td>
						<td width="50" align="right"><? if(($exdata[7]*1)>0) echo number_format($exdata[7],4); else echo ""; ?></td>
						<td width="50" align="right"><? if(($exdata[8]*1)>0) echo number_format($exdata[8],4); else echo ""; ?></td>
						<td width="50" align="right"><? if(($exdata[9]*1)>0) echo number_format($exdata[9],4); else echo ""; ?></td>
                        
						<td width="40" align="right"><?=number_format($exdata[10],4); ?></td>
						<td align="right"><?=number_format($exdata[11],4); ?></td>
					</tr><?
					$itemtot+=$exdata[11];
					$fabtot+=$exdata[11];
				}
				?>
                <tr bgcolor="#CCCCCC">
                    <td width="120">&nbsp;</td>
                    <td width="250" align="right"><b>G.Item Wise Cost:&nbsp;</b></td>
                    <td width="50">&nbsp;</td>
                    <td width="100" align="center">&nbsp;</td>
                    <td width="40" align="center">&nbsp;</td>
    
                    <td width="50">&nbsp;</td>
                    <td width="50" align="right">&nbsp;</td>
                    <td width="50" align="right">&nbsp;</td>
                    <td width="50" align="right">&nbsp;</td>
                    <td width="50" align="right">&nbsp;</td>
                    <td width="40" align="right">&nbsp;</td>
                    <td align="right"><?=number_format($itemtot,4); ?></td>
                </tr>
                <?
			}
			?>
            <tr bgcolor="#999999">
                <td width="120">&nbsp;</td>
                <td width="250" align="right"><b>Total Fabric Cost:&nbsp;</b></td>
                <td width="50">&nbsp;</td>
                <td width="100" align="center">&nbsp;</td>
                <td width="40" align="center">&nbsp;</td>

                <td width="50">&nbsp;</td>
                <td width="50" align="right">&nbsp;</td>
                <td width="50" align="right">&nbsp;</td>
                <td width="50" align="right">&nbsp;</td>
                <td width="50" align="right">&nbsp;</td>
                <td width="40" align="right">&nbsp;</td>
                <td align="right"><?=number_format($fabtot,4); ?></td>
            </tr>
        </table>
        
        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:930px; margin-top: 10px" rules="all">
			<thead>
            	<tr>
                	<th colspan="11">Accessories Details</th>
                </tr>
            	<tr>
                    <th width="150" rowspan="2">Item Group</th>
                    <th width="250" rowspan="2">Description</th>
                    <th width="70" rowspan="2">Brand/ Sup Ref</th>
                    <th width="70" rowspan="2">Cons. UOM</th>
                    <th colspan="5">Cons/Dzn</th>
                    <th width="50" rowspan="2">Avg. Rate</th>
                    <th rowspan="2">Cost</th>
                </tr>
                <tr>
                <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                	<th width="55"><font style="font-size:10px; font-weight:100"><?=$qsname; ?></font></th>
                <? } ?>
                </tr>
			</thead>
			<?
			$itemGroupArr=return_library_array( "select id, item_name from lib_item_group where status_active=1 and is_deleted=0 and item_category=4",'id','item_name');
			foreach ($accArr as $rowid=>$trdata)
			{
				$exrdata=explode('**',$trdata['str']);
				?>
                <tr>
                    <td width="150"><?=$itemGroupArr[$exrdata[0]]; ?></td>
                    <td width="250"><?=$exrdata[1]; ?></td>
                    <td width="70"><?=$exrdata[2]; ?></td>
                    <td width="70" align="center"><?=$unit_of_measurement[$exrdata[3]]; ?></td>
                    
                    <td width="55" align="right"><? if(($exrdata[4]*1)>0) echo number_format($exrdata[4],4); else echo ""; ?></td>
                    <td width="55" align="right"><? if(($exrdata[5]*1)>0) echo number_format($exrdata[5],4); else echo ""; ?></td>
                    <td width="55" align="right"><? if(($exrdata[6]*1)>0) echo number_format($exrdata[6],4); else echo ""; ?></td>
                    <td width="55" align="right"><? if(($exrdata[7]*1)>0) echo number_format($exrdata[7],4); else echo ""; ?></td>
                    <td width="55" align="right"><? if(($exrdata[8]*1)>0) echo number_format($exrdata[8],4); else echo ""; ?></td>
                    <td width="50" align="right"><?=number_format($exrdata[9],4); ?></td>
                    <td align="right"><?=number_format($exrdata[10],4); ?></td>
                </tr><?
				$acctot+=$exrdata[10];
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td width="150">&nbsp;</td>
                <td width="250">Total Accessories Cost:</td>
                <td width="70" align="center">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="55" align="right">&nbsp;</td>
                <td width="55" align="right">&nbsp;</td>
                <td width="55" align="right">&nbsp;</td>
                <td width="55" align="right">&nbsp;</td>
                <td width="55" align="right">&nbsp;</td>
                <td width="50" align="right">&nbsp;</td>
                <td align="right"><?=number_format($acctot,4); ?></td>
            </tr>
        </table>
        
        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:930px; margin-top: 10px" rules="all">
			<thead>
            	<tr>
                	<th colspan="10">Embellishment Details</th>
                </tr>
            	<tr>
                    <th width="150" rowspan="2">Embellishment Name</th>
                    <th width="150" rowspan="2">Embellishment Type</th>
                    <th width="170" rowspan="2">Body Part</th>
                    <th colspan="5">Cons/Dzn</th>
                    <th width="60" rowspan="2">Avg. Rate</th>
                    <th rowspan="2">Value ($)</th>
                </tr>
                <tr>
                <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                	<th width="60"><font style="font-size:10px; font-weight:100"><?=$qsname; ?></font></th>
                <? } ?>
                </tr>
			</thead>
			<? 
			foreach ($embArr as $rowid=>$edata)
			{
				$exrdata=explode('**',$edata['str']);
				$embName=""; $emb_type="";
				
				$embName=$emblishment_name_array[$exrdata[0]];
				if($exrdata[0]==1) $emb_type=$emblishment_print_type[$exrdata[1]];
				else if($exrdata[0]==2) $emb_type=$emblishment_embroy_type[$exrdata[1]];
				else if($exrdata[0]==3) $emb_type=$emblishment_wash_type[$exrdata[1]];
				else if($exrdata[0]==4) $emb_type=$emblishment_spwork_type[$exrdata[1]];
				else if($exrdata[0]==5) $emb_type=$emblishment_gmts_type[$exrdata[1]];
				?>
				<tr>
					<td width="150"><?=$embName; ?></td>
					<td width="150"><?=$emb_type; ?></td>
					<td width="170"><?=$body_part[$exrdata[2]]; ?></td>
					
					<td width="60" align="right"><? if(($exrdata[3]*1)>0) echo number_format($exrdata[3],4); else echo ""; ?></td>
					<td width="60" align="right"><? if(($exrdata[4]*1)>0) echo number_format($exrdata[4],4); else echo ""; ?></td>
					<td width="60" align="right"><? if(($exrdata[5]*1)>0) echo number_format($exrdata[5],4); else echo ""; ?></td>
					<td width="60" align="right"><? if(($exrdata[6]*1)>0) echo number_format($exrdata[6],4); else echo ""; ?></td>
					<td width="60" align="right"><? if(($exrdata[7]*1)>0) echo number_format($exrdata[7],4); else echo ""; ?></td>
					<td width="60" align="right"><?=number_format($exrdata[8],4); ?></td>
					<td align="right"><?=number_format($exrdata[9],2); ?></td>
				</tr><?
				$embtot+=$exrdata[9];
			}
			?>
            <tr bgcolor="#CCCCCC">
            	<td width="150">&nbsp;</td>
                <td width="150">&nbsp;</td>
                <td width="170">Total Embellishment Cost:</td>
                <td width="60" align="right">&nbsp;</td>
                <td width="60" align="right">&nbsp;</td>
                <td width="60" align="right">&nbsp;</td>
                <td width="60" align="right">&nbsp;</td>
                <td width="60" align="right">&nbsp;</td>
                <td width="60" align="right">&nbsp;</td>
                <td align="right"><?=number_format($embtot,2); ?></td>
            </tr>
        </table>
        <br>
        <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="560">
            <thead>
                <tr>
                    <th colspan="8">Style Cost Summary [$/DZN]</th>
                </tr>
                <tr>
                    <th width="100">Description</th>
                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                    <th width="60"><font style="font-size:10px; font-weight:100;"><?=$qsname; ?></font></th>
                    <? } ?>
                    <th width="80">Total</th>
                    <th>% To FOB</th>
                </tr>
            </thead>
            <?
			$sqlItemSumm="select id, mst_id, item_id, fabric_cost, sp_operation_cost, wash_cost, accessories_cost, cpm, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, other_cost, commission_cost, fob_pcs, commercial_cost, operating_exp from qc_item_cost_summary where mst_id='$qc_no' and status_active=1 and is_deleted=0";
			$sqlItemSummData=sql_select($sqlItemSumm); $summItemArr=array(); $summArr=array();
			foreach ($sqlItemSummData as $srow)
			{
				$summItemArr[$srow[csf('item_id')]]['fabric_cost']=$srow[csf('fabric_cost')];
				$summItemArr[$srow[csf('item_id')]]['sp']=$srow[csf('sp_operation_cost')];
				$summItemArr[$srow[csf('item_id')]]['wash']=$srow[csf('wash_cost')];
				$summItemArr[$srow[csf('item_id')]]['acc']=$srow[csf('accessories_cost')];
				$summItemArr[$srow[csf('item_id')]]['cpm']=$srow[csf('cpm')];
				$summItemArr[$srow[csf('item_id')]]['smv']=$srow[csf('smv')];
				$summItemArr[$srow[csf('item_id')]]['eff']=$srow[csf('efficiency')];
				$summItemArr[$srow[csf('item_id')]]['cm']=$srow[csf('cm_cost')];
				$summItemArr[$srow[csf('item_id')]]['fr']=$srow[csf('frieght_cost')];
				$summItemArr[$srow[csf('item_id')]]['lb']=$srow[csf('lab_test_cost')];
				$summItemArr[$srow[csf('item_id')]]['op']=$srow[csf('operating_exp')];
				$summItemArr[$srow[csf('item_id')]]['ot']=$srow[csf('other_cost')];
				$summItemArr[$srow[csf('item_id')]]['coml']=$srow[csf('commercial_cost')];
				$summItemArr[$srow[csf('item_id')]]['comm']=$srow[csf('commission_cost')];
				$summItemArr[$srow[csf('item_id')]]['fob']=$srow[csf('fob_pcs')];
				
				$summArr['fabric_cost']+=$srow[csf('fabric_cost')];
				$summArr['sp']+=$srow[csf('sp_operation_cost')];
				$summArr['wash']+=$srow[csf('wash_cost')];
				$summArr['acc']+=$srow[csf('accessories_cost')];
				$summArr['cpm']+=$srow[csf('cpm')];
				$summArr['smv']+=$srow[csf('smv')];
				$summArr['eff']+=$srow[csf('efficiency')];
				$summArr['cm']+=$srow[csf('cm_cost')];
				$summArr['fr']+=$srow[csf('frieght_cost')];
				$summArr['lb']+=$srow[csf('lab_test_cost')];
				$summArr['op']+=$srow[csf('operating_exp')];
				$summArr['ot']+=$srow[csf('other_cost')];
				$summArr['coml']+=$srow[csf('commercial_cost')];
				$summArr['comm']+=$srow[csf('commission_cost')];
				$summArr['fob']+=$srow[csf('fob_pcs')];
			}
			unset($sqlItemSummData);
			?>
            <tbody>
                <tr>
                    <td>Fabric</td>
                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                    <td align="right"><strong><? if(($summItemArr[$qsid]['fabric_cost']*1)>0) echo $summItemArr[$qsid]['fabric_cost']; else echo ""; ?></strong></td>
                    <? } ?>
                    <td align="right"><? if(($summArr['fabric_cost']*1)>0) echo number_format($summArr['fabric_cost'],4);  else echo "";?></td>
                    <td align="right"><? $fabPer=(($summArr['fabric_cost']*1)/$summArr['fob'])*100; if(($fabPer*1)>0) echo number_format($fabPer,4); else echo "";?></td>
                </tr>
                <tr>
                    <td>Special Operation</td>
                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                    <td align="right"><strong><? if(($summItemArr[$qsid]['sp']*1)>0) echo $summItemArr[$qsid]['sp']; else echo ""; ?></strong></td>
                    <? } ?>
                   <td align="right"><? if(($summArr['sp']*1)>0) echo number_format($summArr['sp'],4); else echo ""; ?></td>
                   <td align="right"><? $spPer=(($summArr['sp']*1)/$summArr['fob'])*100; if(($spPer*1)>0) echo number_format($spPer,4); else echo "";?></td>
                </tr>
                <tr>
                    <td>Wash</td>
                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                    <td align="right"><strong><? if(($summItemArr[$qsid]['wash']*1)>0) echo $summItemArr[$qsid]['wash']; else echo ""; ?></strong></td>
                    <? } ?>
                    <td align="right"><? if(($summArr['wash']*1)>0) echo number_format($summArr['wash'],4); else echo ""; ?></td>
                    <td align="right"><? $washPer=(($summArr['wash']*1)/$summArr['fob'])*100; if(($washPer*1)>0) echo number_format($washPer,4); else echo "";?></td>
                </tr>
                <tr>
                    <td>Accessories</td>
                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                    <td align="right"><strong><? if(($summItemArr[$qsid]['acc']*1)>0) echo $summItemArr[$qsid]['acc']; else echo ""; ?></strong></td>
                    <? } ?>
                    <td align="right"><? if(($summArr['acc']*1)>0) echo number_format($summArr['acc'],4); else echo ""; ?></td>
                    <td align="right"><? $accPer=(($summArr['acc']*1)/$summArr['fob'])*100; if(($accPer*1)>0) echo number_format($accPer,4); else echo "";?></td>
                </tr>
                <tr>
                    <td>CPM</td>
                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                    <td align="right"><strong><? if(($summItemArr[$qsid]['cpm']*1)>0) echo $summItemArr[$qsid]['cpm']; else echo ""; ?></strong></td>
                    <? } ?>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>SMV / EFI</td>
                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                   <td align="right"><strong><? if(($summItemArr[$qsid]['smv']*1)>0) echo $summItemArr[$qsid]['smv'].'/'.$summItemArr[$qsid]['eff']; else echo ""; ?></strong></td>
                    <? } ?>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>CM&nbsp;</td>
                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                    <td align="right"><strong><? if(($summItemArr[$qsid]['cm']*1)>0) echo $summItemArr[$qsid]['cm']; else echo ""; ?></strong></td>
                    <? } ?>
                    <td align="right"><? if(($summArr['cm']*1)>0) echo number_format($summArr['cm'],4); else echo ""; ?></td>
                    <td align="right"><? $cmPer=(($summArr['cm']*1)/$summArr['fob'])*100; if(($cmPer*1)>0) echo number_format($cmPer,4); else echo "";?></td>
                </tr>
                <tr>
                    <td>Frieght Cost</td>
                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                    <td align="right"><strong><? if(($summItemArr[$qsid]['fr']*1)>0) echo $summItemArr[$qsid]['fr']; else echo ""; ?></strong></td>
                    <? } ?>
                    <td align="right"><? if(($summArr['fr']*1)>0) echo number_format($summArr['fr'],4); else echo ""; ?></td>
                    <td align="right"><? $frPer=(($summArr['fr']*1)/$summArr['fob'])*100; if(($frPer*1)>0) echo number_format($frPer,4); else echo "";?></td>
                </tr>
                <tr>
                    <td>Lab - Test</td>
                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                    <td align="right"><strong><? if(($summItemArr[$qsid]['lb']*1)>0) echo $summItemArr[$qsid]['lb']; else echo ""; ?></strong></td>
                    <? } ?>
                    <td align="right"><? if(($summArr['lb']*1)>0) echo number_format($summArr['lb'],4); else echo ""; ?></td>
                    <td align="right"><? $lbPer=(($summArr['lb']*1)/$summArr['fob'])*100; if(($lbPer*1)>0) echo number_format($lbPer,4); else echo "";?></td>
                </tr>
                <tr>
                    <td>Opt. Exp.&nbsp;</td>
                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                    <td align="right"><strong><? if(($summItemArr[$qsid]['op']*1)>0) echo $summItemArr[$qsid]['op']; else echo ""; ?></strong></td>
                    <? } ?>
                    <td align="right"><? if(($summArr['op']*1)>0) echo number_format($summArr['op'],4); else echo ""; ?></td>
                    <td align="right"><? $opPer=(($summArr['op']*1)/$summArr['fob'])*100; if(($opPer*1)>0) echo number_format($opPer,4); else echo "";?></td>
                </tr>
                <tr>
                    <td>Other Cost</td>
                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                    <td align="right"><strong><? if(($summItemArr[$qsid]['ot']*1)>0) echo $summItemArr[$qsid]['ot']; else echo ""; ?></strong></td>
                    <? } ?>
                    <td align="right"><? if(($summArr['ot']*1)>0) echo number_format($summArr['ot'],4); else echo ""; ?></td>
                    <td align="right"><? $otPer=(($summArr['ot']*1)/$summArr['fob'])*100; if(($otPer*1)>0) echo number_format($otPer,4); else echo "";?></td>
                </tr>
                <tr>
                    <td title="Commercial Cost">Comml. &nbsp;<b><?=$commercial_per; ?>%</b></td>
                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                    <td align="right"><strong><? if(($summItemArr[$qsid]['coml']*1)>0) echo $summItemArr[$qsid]['coml']; else echo ""; ?></strong></td>
                    <? } ?>
                    <td align="right"><? if(($summArr['coml']*1)>0) echo number_format($summArr['coml'],4); else echo ""; ?></td>
                    <td align="right"><? $comlPer=(($summArr['coml']*1)/$summArr['fob'])*100; if(($comlPer*1)>0) echo number_format($comlPer,4); else echo "";?></td>
                </tr>
                <tr>
                    <td title="Commission Cost">Com(%)&nbsp;<b><?=$commision_per; ?>%</b></td>
                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                    <td align="right"><strong><? if(($summItemArr[$qsid]['comm']*1)>0) echo $summItemArr[$qsid]['comm']; else echo ""; ?></strong></td>
                    <? } ?>
                    <td align="right"><? if(($summArr['comm']*1)>0) echo number_format($summArr['comm'],4); else echo ""; ?></td>
                    <td align="right"><? $commPer=(($summArr['comm']*1)/$summArr['fob'])*100; if(($commPer*1)>0) echo number_format($commPer,4); else echo "";?></td>
                </tr>
                <tr>
                    <td>F.O.B</td>
                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                    <td align="right"><strong><? if(($summItemArr[$qsid]['fob']*1)>0) echo $summItemArr[$qsid]['fob']; else echo ""; ?></strong></td>
                    <? } ?>
                    <td align="right"><? if(($summArr['fob']*1)>0) echo number_format($summArr['fob'],4); else echo ""; ?></td>
                    <td align="right"><?="100"; ?></td>
                </tr>
                <tr>
                    <td>F.O.B($<?=$uomtxt; ?>)</td>
                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                    <td align="right"><strong><? if(($summItemArr[$qsid]['fob']*1)>0) echo number_format($summItemArr[$qsid]['fob']/12,4); else echo ""; ?></strong></td>
                    <? } ?>
                    <td align="right"><? if(($summArr['fob']*1)>0) echo number_format($summArr['fob']/12,4); else echo ""; ?></td>
                    <td align="right"><?="100"; ?></td>
                </tr>
            </tbody>
       </table>
       <?
	echo signature_table(292, $cbo_company_id, "930px");
	exit();
}

if($action=="quick_costing_print2")
{
	extract($_REQUEST);
	echo load_html_head_contents("Quick Costing Print", "../../../", 1, 1,'','','');
	$data=explode('*',$data);
	//print_r( $data);
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$season_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	//$lib_temp_arr=return_library_array("select id, item_name from lib_qc_template","id","item_name");
	
	//$accessories_arr=return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active =1 and is_deleted=0","id","item_name");
	
	$sql_mst="select id, cost_sheet_id, company_id, cost_sheet_no, temp_id, lib_item_id, style_ref, buyer_id, uom, cons_basis, season_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, brand_id, costing_date, revise_no, option_id, option_remarks, buyer_remarks from qc_mst where status_active=1 and is_deleted=0 and qc_no='$data[0]'";
	$sqlMst_res=sql_select($sql_mst);
	
	$exitem_id=explode(",",$sqlMst_res[0][csf('lib_item_id')]);
	$countitemid=count($exitem_id);
	$uomstr="";
	$tblWeight=250+(70*$countitemid);
	
	if($countitemid==1) $uomstr="Pack";
	else $uomstr="Set";
	
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
    <div style="width:735px;">
        <table width="735" cellspacing="0" border="1" style="margin-right:-10px;" rules="all" class="rpt_table">
            <tr class="form_caption">
            	<td colspan="6" align="center" style="font-size:18px"><strong>Buyer COST SHEET <br> <?=$company_arr[$sqlMst_res[0][csf('company_id')]]; ?></strong></td>
            </tr>
            <tr>
                <td width="100"><strong>Buyer:</strong></td><td width="145px"><?=$buyer_arr[$sqlMst_res[0][csf('buyer_id')]]; ?></td>
                <td width="100"><strong>Season:</strong></td><td width="145px"><?=$season_arr[$sqlMst_res[0][csf('season_id')]]; ?></td>
                <td width="100"><strong>Costing Date:</strong></td><td><?=change_date_format($sqlMst_res[0][csf('costing_date')]); ?></td>
            </tr>
            <tr>
            	<td><strong>Master Style:</strong></td><td><?=$sqlMst_res[0][csf('style_ref')]; ?></td>
                <td><strong>Brand:</strong></td><td><?=$brand_arr[$sqlMst_res[0][csf('brand_id')]]; ?></td>
                <td><strong>UOM:</strong></td><td><?=$uomstr; ?></td>
            </tr>
        </table>
        <br>
       
        <table width="<?=$tblWeight; ?>" cellspacing="0" border="1" rules="all" class="rpt_table">
        	<thead>
            	<tr>
                	<th colspan="<?=$countitemid+2; ?>">Item Wise Cost Summary ($/<?=$uomstr; ?>)</th>
                </tr>
                <tr>
                    <th width="150">Description</th>
                    <?
					foreach($exitem_id as $itemid)
					{
						?>
                        <th width="70"><?=$garments_item[$itemid]; ?></th>
                        <?
					}
					?>
                    <th>Total</th>
                </tr>
            </thead>
			<?
            $sql_item_summ="select item_id, fabric_cost, sp_operation_cost, accessories_cost, wash_cost, smv, operating_exp, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commercial_cost, commission_cost, fob_pcs from qc_item_cost_summary where mst_id='$data[0]' and status_active=1 and is_deleted=0";
            $sql_result_item_summ=sql_select($sql_item_summ); $itemWiseCostSummaryArr=array();
			foreach($sql_result_item_summ as $rowItemSumm)
			{
				$itemWiseCostSummaryArr[1][$rowItemSumm[csf("item_id")]]+=$rowItemSumm[csf("fabric_cost")];
				$itemWiseCostSummaryArr[2][$rowItemSumm[csf("item_id")]]+=$rowItemSumm[csf("sp_operation_cost")];
				$itemWiseCostSummaryArr[3][$rowItemSumm[csf("item_id")]]+=$rowItemSumm[csf("wash_cost")];
				$itemWiseCostSummaryArr[4][$rowItemSumm[csf("item_id")]]+=$rowItemSumm[csf("accessories_cost")];
				$itemWiseCostSummaryArr[5][$rowItemSumm[csf("item_id")]]+=$rowItemSumm[csf("cm_cost")];
				$itemWiseCostSummaryArr[6][$rowItemSumm[csf("item_id")]]+=$rowItemSumm[csf("frieght_cost")];
				$itemWiseCostSummaryArr[7][$rowItemSumm[csf("item_id")]]+=$rowItemSumm[csf("lab_test_cost")];
				$itemWiseCostSummaryArr[8][$rowItemSumm[csf("item_id")]]+=$rowItemSumm[csf("operating_exp")];
				$itemWiseCostSummaryArr[9][$rowItemSumm[csf("item_id")]]+=$rowItemSumm[csf("other_cost")];
				$itemWiseCostSummaryArr[10][$rowItemSumm[csf("item_id")]]+=$rowItemSumm[csf("commercial_cost")];
				$itemWiseCostSummaryArr[11][$rowItemSumm[csf("item_id")]]+=$rowItemSumm[csf("commission_cost")];
				$itemWiseCostSummaryArr[12][$rowItemSumm[csf("item_id")]]+=0;//margin
				$itemWiseCostSummaryArr[13][$rowItemSumm[csf("item_id")]]+=$rowItemSumm[csf("fob_pcs")];
			}
            
			$despQcArr=array();
			$despQcArr=array(1 => "Yarn", 2 => "Special Operation", 3 => "Wash", 4 => "Accessories", 5 => "CM [$/".$uomstr."]", 6 => "Frieght Cost[$/".$uomstr."]", 7 => "Lab - Test[$/".$uomstr."]", 8 => "Opt. Exp. ", 9 => "Other Cost[$/".$uomstr."]", 10 => "Comml.[$/".$uomstr."]", 11 => "Com.(%)[$/".$uomstr."]", 12 => "Margin/Profit", 13 => "F.O.B[$/".$uomstr."]");
			foreach($despQcArr as $key=>$val)
			{
				?>
                <tr style="border:1px solid black;">
                	<td style="word-break:break-all"><?=$val; ?></td>
                    <?
					$headTot=0;
					foreach($exitem_id as $itemid)
					{
						$qcAmt=0;
						$qcAmt=$itemWiseCostSummaryArr[$key][$itemid];
						$headTot+=$qcAmt;
						?>
                        <th width="70" align="right"><?=$qcAmt; ?></th>
                        <?
					}
					?>
                	<td align="right"><?=number_format($headTot,4); ?>&nbsp;</td>
                </tr>
                <?
			}
            ?>
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
				http.open("POST","short_quotation_v5_controller.php",true);
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

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'short_quotation_v5_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'short_quotation_v5_controller', this.value, 'load_drop_down_brand', 'brand_td');" );
	exit();
}

if ($action=="inquery_id_popup")
{
	//inquery_id_popup
  	echo load_html_head_contents("Inquiry Entry","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	?>
	<script>
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
                        <th width="100">Style Ref.</th>
                        <th width="50">Season Year</th>
                        <th width="130">Season</th>
                        <th width="130">Brand</th>
                        <th width="130" colspan="2">Inquiry Date Range</th>
                        <th>&nbsp; </th>
                    </tr>
                </thead>
                <tr class="general">
                    <td><? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'short_quotation_v5_controller', this.value, 'load_drop_down_buyer', 'buyer_td');" ); ?><input type="hidden" id="selected_id"></td>
                    <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
                    <td><input type="text" style="width:70px" class="text_boxes"  name="txt_inquery_no" id="txt_inquery_no" placeholder="Write" /></td>
                    <td><input type="text" style="width:90px" class="text_boxes"  name="txt_style" id="txt_style" placeholder="Write" /></td>
                    <td><? echo create_drop_down( "cbo_season_year", 50, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
                    <td id="season_td"><? echo create_drop_down( "cbo_season_id", 130, $blank_array,'', 1, "-- Select Season--",$selected, "" ); ?></td>
                    <td id="brand_td"><? echo create_drop_down( "cbo_brand", 130, $blank_array,"",1, "-Brand-", $selected,""); ?></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td>
                    <td>
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_inquery_no').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_season_id').value+'_'+document.getElementById('cbo_brand').value+'_'+document.getElementById('cbo_season_year').value, 'create_inquery_id_list_view', 'search_div', 'short_quotation_v5_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
                </tr>
                <tr>
                	<td colspan="10" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
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

if($action=="create_inquery_id_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and buyer_id='$data[1]'"; else $buyer="" ;
	if (trim($data[7])!=0) $season_cond=" and season_buyer_wise='$data[7]'"; else $season_cond="" ;
	if (trim($data[8])!=0) $brand_cond=" and brand_id='$data[8]'"; else $brand_cond="" ;
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
	$brandArr = return_library_array("select id,brand_name from lib_buyer_brand ","id","brand_name");
	$arr=array (1=>$buyer_arr,3=>$seasonArr,6=>$brandArr);
	$sql= "select id, system_number_prefix_num, company_id, buyer_id, season_buyer_wise, season_year, inquery_date, buyer_request, style_refernce, style_description, brand_id, fabrication from wo_quotation_inquery where status_active=1  and is_deleted=0 $company $buyer $est_ship_date $inquery_id_cond $style_cond $season_cond $brand_cond  $seasonyear_cond
	order by id DESC";//and id not in (select inquery_id from qc_mst where and status_active=1 and is_deleted=0 and inquery_id!=0)
	//echo $sql;
	echo  create_list_view("list_view", "Inquiry ID,Buyer Name,Style Ref,Season,Inquiry Date, Buyer Request,Brand", "60,120,100,80,70,80","800","280",0, $sql , "js_set_value", "id,buyer_id,style_refernce,system_number_prefix_num,season_buyer_wise,season_year,brand_id,style_description,fabrication", "", 1, "0,buyer_id,0,season_buyer_wise,0,0,brand_id", $arr , "system_number_prefix_num,buyer_id,style_refernce,season_buyer_wise,inquery_date,buyer_request,brand_id", "",'','0,0,0,0,3,0,0') ;
	exit();
}

if($action=="bodyPart_washType_ItemGroup_popup")
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
        <? } else if($type==5) {
			$accessories_arr=sql_select("select id, item_name, trim_uom from lib_item_group where item_category=4 and status_active =1 and is_deleted=0 order by item_name");
			?>
        <table width="420" cellspacing="0" class="rpt_table" border="0" rules="all">
            <thead>
            	<th width="40">SL</th><th width="280">Item Group</th><th>Cons Uom</th>
            </thead>
        </table>
        <table width="420" cellspacing="0" class="rpt_table" border="0" rules="all" id="item_table">
            <tbody>
				<?
                $i=1;
                foreach($accessories_arr as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr onClick="js_set_value('<?=$row[csf('id')].'_'.$row[csf('trim_uom')]; ?>','<?=$row[csf('item_name')]; ?>');" bgcolor="<?=$bgcolor; ?>">
                        <td width="40"><?=$i; ?></td>
                        <td width="280"><?=$row[csf('item_name')]; ?></td>
                        <td><?=$unit_of_measurement[$row[csf('trim_uom')]]; ?></td>
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
			if(array_key_exists($row[csf('mst_id')],$composition_arr))
			{
				$composition_arr[$row[csf('mst_id')]]=$composition_arr[$row[csf('mst_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('mst_id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]];
			}
		}
	}
	unset($data_array);
	
	$sqlRd="select id, type, construction, gsm_weight, weight_type, design, fabric_ref, rd_no, color_range_id, full_width, cutable_width from lib_yarn_count_determina_mst where entry_form=426 and status_active=1 and is_deleted=0 and id in ($data)";
	$sqlRdData=sql_select($sqlRd);
	$i=1;
	if(count($sqlRdData)>0)
	{
		foreach($sqlRdData as $row)
		{
			$fabric="";
			$fabric=$row[csf('rd_no')].','.$row[csf('fabric_ref')].','.$row[csf('type')].','.$row[csf('construction')].','.$row[csf('design')].','.$row[csf('gsm_weight')].','.$fabric_weight_type[$row[csf('weight_type')]].','.$color_range[$row[csf('color_range_id')]].','.$composition_arr[$row[csf('id')]].','.$row[csf('full_width')].','.$row[csf('cutable_width')];
			echo "$('#txt_fabid_".$i."').val('".$row[csf('id')]."');\n";
			echo "$('#txt_fabDesc".$i."').val('".$fabric."');\n"; 
			$i++;
		}
		unset($sqlRdData);
	}	
	exit();
}

if($action=="fabric_description_popup")
{
	echo load_html_head_contents("Yarn Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(data)
		{
			var data=data.split('_');
			document.getElementById('hiddfabid').value=data[0];
			document.getElementById('hiddFabricDescription').value=data[5];
			document.getElementById('fab_gsm').value=data[3];
			parent.emailwindow.hide();
		}
		
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
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
                    	<th colspan="3" align="center"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                    </tr>
                    <tr>
                        <th>Construction</th>
                        <th>GSM/Weight</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="general">
                        <td align="center"><input type="text" style="width:130px" class="text_boxes" name="txt_construction" id="txt_construction" /></td>
                        <td align="center">	<input type="text" style="width:130px" class="text_boxes" name="txt_gsm_weight" id="txt_gsm_weight" /></td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<?=$txt_fabid; ?>'+'**'+document.getElementById('txt_construction').value+'**'+document.getElementById('txt_gsm_weight').value+'**'+document.getElementById('cbo_string_search_type').value, 'fabric_description_popup_search_list_view', 'search_div', 'short_quotation_v5_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" /><!--toggle( 'tr_'+'<?//=$txt_fabid; ?>', '#FFFFCC');-->
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
	list($libyarncountdeterminationid,$construction,$gsm_weight,$string_search_type,$rdno)=explode('**',$data);
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
	$search_con='';
	$user_arr=return_library_array( "select user_full_name,id from user_passwd", "id", "user_full_name");
	if($string_search_type==1)
	{
		if($construction!='') {$search_con .= " and a.construction='".trim($construction)."'";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight='".trim($gsm_weight)."'";}
	}
	else if($string_search_type==2)
	{
		if($construction!='') {$search_con .= " and a.construction like ('".trim($construction)."%')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('".trim($gsm_weight)."%')";}
	}
	else if($string_search_type==3)
	{
		if($construction!='') {$search_con .= " and a.construction like ('%".trim($construction)."')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('%".trim($gsm_weight)."')";}
	}
	else if($string_search_type==4 || $string_search_type==0)
	{
		if($construction!='') {$search_con .= " and a.construction like ('%".trim($construction)."%')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('%".trim($gsm_weight)."%')";}
	}
?>
</head>
<body>
    <div align="center">
        <form>
            <input type="hidden" id="hiddfabid" name="hiddfabid" />
            <input type="hidden" id="fab_gsm" name="fab_gsm" />
            <input type="hidden" id="process_loss" name="process_loss" />
            <input type="hidden" id="hiddFabricDescription" name="hiddFabricDescription" />
        </form>
	<?
	$composition_arr=array(); $fab_description_arr=array();
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$arr=array (0=>$item_category, 3=>$color_range,6=>$composition,8=>$lib_yarn_count,9=>$yarn_type);
	$sql="select a.fab_nature_id, a.construction, a.gsm_weight, a.color_range_id, a.stich_length, a.process_loss, b.copmposition_id, b.percent, b.count_id, b.type_id, a.id, b.id as bid from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id, b.id";
	$data_array=sql_select($sql);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			
			if(array_key_exists($row[csf('id')],$fab_description_arr))
			{
				$fab_description_arr[$row[csf('id')]]=$row[csf('construction')]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ,";
			}
			else
			{
				$fab_description_arr[$row[csf('id')]]=$row[csf('construction')]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')];
			}
		}
	}
	?>
    <table class="rpt_table" width="950" cellspacing="0" cellpadding="0" border="0" rules="all">
        <thead>
            <tr>
                <th width="30">SL No</th>
                <th width="50">Sequence No</th>
                <th width="100">Fab Nature</th>
                <th width="100">Construction</th>
                <th width="100">GSM/Weight</th>
                <th width="100">Color Range</th>
                <th width="90">Stich Length</th>
                <th width="50">Process Loss</th>
                <th>Composition</th>
            </tr>
       </thead>
   </table>
   <div id="" style="max-height:300px; width:948px; overflow-y:scroll">
   <table id="list_view" class="rpt_table" width="930" height="" cellspacing="0" cellpadding="0" border="1" rules="all">
        <tbody>
	<?
	$sql_data=sql_select("select a.fab_nature_id, a.construction, a.gsm_weight, a.color_range_id, a.stich_length, a.process_loss, a.id, a.sequence_no from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_con group by a.id, a.fab_nature_id, a.construction, a.gsm_weight, a.color_range_id, a.stich_length, a.process_loss, a.sequence_no order by a.id");//and a.fab_nature_id=2
	$i=1;
	foreach($sql_data as $row)
	{
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
        <tr id="tr_<?=$row[csf('id')]; ?>" bgcolor="<?=$bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="js_set_value('<?=$row[csf('id')]."_".$row[csf('fab_nature_id')]."_".$row[csf('construction')]."_".$row[csf('gsm_weight')]."_".$row[csf('process_loss')]."_".$fab_description_arr[$row[csf('id')]]; ?>')">
            <td width="30"><?=$i; ?></td>
            <td width="50" align="left"><?=$row[csf('sequence_no')]; ?></td>
            <td width="100" align="left"><?=$item_category[$row[csf('fab_nature_id')]]; ?></td>
            <td width="100" align="left"><?=$row[csf('construction')]; ?></td>
            <td width="100" align="right"><?=$row[csf('gsm_weight')]; ?></td>
            <td width="100" align="left"><?=$color_range[$row[csf('color_range_id')]]; ?></td>
            <td width="90" align="right"><?=$row[csf('stich_length')]; ?></td>
            <td width="50" align="right"><?=$row[csf('process_loss')]; ?></td>
            <td><?=$composition_arr[$row[csf('id')]]; ?></td>
        </tr>
		<?
        $i++;
    }
    ?>
        </tbody>
    </table>
</div>
</div>
</body>
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
            parent.emailwindow.hide();
        }
		
        function insert_template_data(data)
        {
            var template_data=return_global_ajax_value( data, 'get_template_data', '', 'short_quotation_v5_controller');
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
                    templatedata += selected_name[i] + ',';
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
                    templatedata += selected_name[i] + ',';
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
                templatedata += selected_name[i] + ',';
            }
            templatedata = templatedata.substr( 0, templatedata.length - 1 );                
            $('#select_template_data').val( templatedata );
        }
    </script>
    <?
	$template_name_sql=sql_select("select  a.template_name  from lib_trim_costing_temp a, lib_trim_costing_temp_dtls b where a.id=b.lib_trim_costing_temp_id and b.buyer_id =$buyer_name and a.is_deleted=0 group by a.template_name");
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
                    <th width="80">Apvl Req.</th>                
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
    $trim_rate_from_library=return_library_array( "select a.id, max(b.rate) as rate from lib_item_group a, lib_item_group_rate b where a.id=b.mst_id and a.item_category=4 and a.status_active=1 and    a.is_deleted=0 group by a.id", "id", "rate");
    $supplier_library=return_library_array( "select a.supplier_name, a.id from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(4,5) and a.is_deleted=0  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");
    $data_array=sql_select("SELECT * from lib_trim_costing_temp where template_name='$data' and status_active=1 and is_deleted=0");
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
        $str=$lib_item_group_arr[$row[csf("trims_group")]].'***'.$row[csf('user_code')].'***'.$row[csf('trims_group')].'***'.$row[csf('cons_uom')].'***'.$row[csf('cons_dzn_gmts')].'***'.$row[csf('purchase_rate')].'***'.$row[csf('amount')].'***'.$row[csf('apvl_req')].'***'.$row[csf('supplyer')].'***'.$row[csf('sup_ref')].'***'.$row[csf('item_description')];
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
            <td><?=$row[csf("cons_dzn_gmts")];?></td>
            <td><?=$rate;?></td>
            <td><?=$amount;?></td>
            <td><?=$yes_no[$row[csf("apvl_req")]]; ?></td>
        </tr>
    <?  $i++; 
	} 
    exit();   
}