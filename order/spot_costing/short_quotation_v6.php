<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Short Quotation -V6
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	13-07-2023
Updated by 		: 		
Update date		: 
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Short Quotation -V6","../../", 1, 1, $unicode,1,'');

$bodyPartReverse=array();
foreach($body_part as $key=>$val)
{
	$bodyPartReverse[strtoupper($val)]=$key;
}

$washTypeReverse=array();
foreach($emblishment_wash_type as $key=>$val)
{
	$washTypeReverse[strtoupper($val)]=$key;
}

$itemGroupReverse=array(); $itemGroupArr=array();
$itemGroupSql=sql_select("select id, item_name from lib_item_group where status_active=1 and is_deleted=0 and item_category=4");
foreach($itemGroupSql as $row)
{
	$itemGroupReverse[strtoupper($row[csf('item_name')])]=$row[csf('id')];
	$itemGroupArr[$row[csf('id')]]=strtoupper($row[csf('item_name')]);
}

?>

<script type="text/javascript">
	<?
		$field_mst='';
		foreach($_SESSION['logic_erp']['mandatory_field'][634] as $key=>$value)
		{
			if($key==1)
			{
				$field_mst=$value;
			}
		}
		echo "var fabuom_mandatory = '". ($field_mst) . "';\n";
		echo "var mandatory_field = '". implode('*',$_SESSION['logic_erp']['mandatory_field'][634]) . "';\n";
		echo "var field_message = '". implode('*',$_SESSION['logic_erp']['field_message'][634]) . "';\n";

		if($_SESSION['logic_erp']['mandatory_field'][634]!="")
		{
			$mandatory_field_arr= json_encode( $_SESSION['logic_erp']['mandatory_field'][634] );
			echo "var mandatory_field_arr= ". $mandatory_field_arr . ";\n";
		}
	?>
</script>	
<script>
	var field_level_data=new Array();
	<?
	if(isset($_SESSION['logic_erp']['data_arr'][634]))
	{
		$field_lavel_access = $_SESSION['logic_erp']['data_arr'][634];
		//echo count($_SESSION['logic_erp']['data_arr'][634]);
		$data_arr= json_encode($field_lavel_access);
		echo "var field_level_data= ". $data_arr . ";\n";
		//echo count($_SESSION['logic_erp']['data_arr'][634]);
	}
	?>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	// Master Form-----------------------------------------------------------------------------
	var prev_item_id='';
	function fnc_select()
	{
		$(document).ready(function() {
			$("input:text").focus(function() { $(this).select(); } );
		});
	}
	
	var str_bodyPart_head = [<?=substr(return_library_autocomplete_fromArr( $body_part ), 0, -1); ?>];
	var str_washType = [<?=substr(return_library_autocomplete_fromArr( $emblishment_wash_type ), 0, -1); ?>];
	var str_acc= [<?=substr(return_library_autocomplete( "select item_name from lib_item_group where status_active=1 and is_deleted=0 and item_category=4","item_name" ),0,-1); ?>];
	function add_auto_complete(inc)
	{
		var incVal=inc.split("_");
		
		var i=incVal[0];
		var type=incVal[1];
		if(type==1)
		{
			$("#txtbodyparttext_"+i).autocomplete({
				source: str_bodyPart_head
			});
		}
		else if(type==2)
		{
			$("#txtspbodyparttext_"+i).autocomplete({
				source: str_bodyPart_head
			});
		}
		else if(type==3)
		{
			$("#txtWashTypetext_"+i).autocomplete({
				source: str_washType
			});
		}
		else if(type==4)
		{
			$("#txtWbodyparttext_"+i).autocomplete({
				source: str_bodyPart_head
			});
		}
		else if(type==5)
		{
			$("#txtAcctext_"+i).autocomplete({
				source: str_acc
			});
		}
	}
	
	function fn_getIndex(incdata,val)
	{
		var inc_d=incdata.split("_");
		var i=inc_d[0];
		var type=inc_d[1];
		if(type==1)
		{
			var bodyPartReverse = JSON.parse('<?  echo json_encode($bodyPartReverse); ?>');
			var bodyPart_name=val.toUpperCase();
			
			if(bodyPartReverse[bodyPart_name]=="" || bodyPartReverse[bodyPart_name]==undefined)
			{
				//alert("Body Part Not found.");
				$("#txtbodyparttext_"+i).val('');
				$("#txtbodyparttext_"+i).focus();
				return;
			}
			else
			{
				$("#txtbodypartid_"+i).val(bodyPartReverse[bodyPart_name]);
			}
		}
		else if(type==2)
		{
			var bodyPartReverse = JSON.parse('<?  echo json_encode($bodyPartReverse); ?>');
			var bodyPart_name=val.toUpperCase();
			
			if(bodyPartReverse[bodyPart_name]=="" || bodyPartReverse[bodyPart_name]==undefined)
			{
				//alert("Body Part Not found.");
				$("#txtspbodyparttext_"+i).val('');
				$("#txtspbodyparttext_"+i).focus();
				return;
			}
			else
			{
				$("#txtspbodypartid_"+i).val(bodyPartReverse[bodyPart_name]);
			}
		}
		else if(type==3)
		{
			var washTypeReverse = JSON.parse('<?  echo json_encode($washTypeReverse); ?>');
			var washType_name=val.toUpperCase();
			
			if(washTypeReverse[washType_name]=="" || washTypeReverse[washType_name]==undefined)
			{
				//alert("Body Part Not found.");
				$("#txtWashTypetext_"+i).val('');
				$("#txtWashTypetext_"+i).focus();
				return;
			}
			else
			{
				$("#txtWashTypeId_"+i).val(washTypeReverse[washType_name]);
			}
		}
		else if(type==4)
		{
			var bodyPartReverse = JSON.parse('<?  echo json_encode($bodyPartReverse); ?>');
			var bodyPart_name=val.toUpperCase();
			
			if(bodyPartReverse[bodyPart_name]=="" || bodyPartReverse[bodyPart_name]==undefined)
			{
				//alert("Body Part Not found.");
				$("#txtWbodyparttext_"+i).val('');
				$("#txtWbodyparttext_"+i).focus();
				return;
			}
			else
			{
				$("#txtWbodypartid_"+i).val(bodyPartReverse[bodyPart_name]);
			}
		}
		else if(type==5)
		{
			var itemGroupReverse = JSON.parse('<?  echo json_encode($itemGroupReverse); ?>');
			var itemGroup_name=val.toUpperCase();
			
			if(itemGroupReverse[itemGroup_name]=="" || itemGroupReverse[itemGroup_name]==undefined)
			{
				//alert("Body Part Not found.");
				$("#txtAcctext_"+i).val('');
				$("#cboconsuom_"+i).val(0);
				$("#txtAccId_"+i).val('');
				$("#txtAcctext_"+i).focus();
				$("#hiddencalparameter_"+i).val('');
				$('#txtAccConsCalData_'+i).val('');
				return;
			}
			else
			{
				//alert(itemGroupReverse[itemGroup_name])
				var extrimval=itemGroupReverse[itemGroup_name].split("_");
				$("#txtAccId_"+i).val(extrimval[0]);
				$("#cboconsuom_"+i).val(extrimval[1]);
				$("#hiddencalparameter_"+i).val(extrimval[2]);
				$('#txtAccConsCalData_'+i).val('');
				
				if(extrimval[2]==1 || extrimval[2]==2 || extrimval[2]==3 || extrimval[2]==4 || extrimval[2]==5 || extrimval[2]==6 || extrimval[2]==7 || extrimval[2]==8 || extrimval[2]==9 || extrimval[2]==10 || extrimval[2]==11 || extrimval[2]==12 || extrimval[2]==13)
				{
					$('#txtaccConsumtion_'+i).removeAttr("onDblClick").attr("onDblClick","fnc_openmypage_calparameter("+extrimval[0]+",'"+$("#txtAcctext_"+i).val()+"',"+extrimval[2]+","+i+");");
					//$('#txtaccConsumtion_'+i).attr('readonly','readonly');
					$('#txtaccConsumtion_'+i).attr("placeholder", "Browse/Write");
				}
				else
				{
					//$('#txtaccConsumtion_'+i).removeAttr('readonly','readonly');
					$('#txtaccConsumtion_'+i).attr('placeholder','');
					$('#txtaccConsumtion_'+i).removeAttr("onDblClick");
				}
			}
		}
	}
	
	function openmypage_temp(page_link,title)
	{
		var temp_id=document.getElementById('cbo_temp_id').value;
		page_link=page_link+'&temp_id='+temp_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=350px,center=1,resize=0,scrolling=0','../')
		release_freezing();
		emailwindow.onclose=function()
		{
			load_drop_down( 'requires/short_quotation_v6_controller', '', 'load_drop_template_name', 'template_td');
		}
	}
	
	function fnc_template_view()
	{
		var temp_id=$("#cbo_temp_id").val();
		if(temp_id!=0)
		{
			var item_data=return_ajax_request_value(temp_id, 'load_lib_item_id', 'requires/short_quotation_v6_controller');
			var exitemdata=item_data.split("__");
			var item_id=exitemdata[0];
			if($("#cbo_temp_id").val()==0) exitemdata[1]=0;
			$("#tdpackQty").html(exitemdata[1]);
			var itemNratio=exitemdata[2];
			load_drop_down( 'requires/short_quotation_v6_controller', itemNratio, 'load_drop_down_tempItem', 'item_td');
			//alert(item_id);
			
			$("#txt_temp_id").val(item_id);
			var id=item_id.split(",");
			var asc_id=id; //.sort();
			//alert(asc_id); return;
			var item_name=get_dropdown_text('cbo_temp_id');
			var nameItem=item_name.split(",");
			fnc_item_list(id+"__"+item_name);
			fnc_summary_dtls(asc_id+"__"+item_name);
			//fnc_dtls_ganerate(trim(id[0])+"__"+nameItem[0]);
			//return;
			fnc_change_data();
			//alert($("#cboItemId").val())
			var itemDetails=get_dropdown_text('cboItemId');
			var itemRatio=itemDetails.split("::");
			if($("#cboItemId").val()==0) itemRatio[1]=0;
			$("#tditemRatio").html(itemRatio[1]);
			//navigate_arrow_key();
			//fnc_specialAcc_reset();
			
			var count_td=id.length;
					
			if(count_td==1)
			{
				$('#uom_td').text('/PCS');
			}
			else if(count_td>1)
			{
				$('#uom_td').text('/Set');
			}
			else
			{
				$('#uom_td').text('');
			}
		}
	}
	
	function fnc_item_list(val)
	{
		//alert(val); return;
		val=val.replace(/'/g, '');
		var value=val.split("__");
		//alert(value[0]);
		var gmtId=value[0].split(",");
		//var gmtId=gmtId.replace('/\s+/g'," ");
		var gmtName=value[1].split(",");
		
		var j=0; var itemNameArr = new Array();
		for(var i=1; i<=gmtName.length; i++)
		{
			itemNameArr[j]=gmtName[j];
			j++;
		}
		
		var html="";
		$('#td_hiddData').html('');
		
		var k=0; var bgcolor=''; var inp_cls='';
		for(var l=1; l<=gmtId.length; l++)
		{
			var itm_id=0;
			itm_id=trim(gmtId[k]);
			html += '<input style="width:120px;" type="hidden" class="text_boxes" name="txtfabricData_'+itm_id+'" id="txtfabricData_'+itm_id+'" /><input style="width:120px;" type="hidden" class="text_boxes" name="txtspData_'+itm_id+'" id="txtspData_'+itm_id+'" /><input style="width:120px;" type="hidden" class="text_boxes" name="txtwashData_'+itm_id+'" id="txtwashData_'+itm_id+'" /><input style="width:120px;" type="hidden" class="text_boxes" name="txtaccData_'+itm_id+'" id="txtaccData_'+itm_id+'" />';
			k++;
		}
		//alert(html);
		$("#td_hiddData").append(html);
		fnc_itemwise_data_cache();
		set_all_onclick();
		
		fnc_select();
	}
	
	function fnc_summary_dtls(val)
	{
		var value=val.split("__");
		
		var gmtId=value[0].split(",");
		var gmtName=value[1].split(",");
		console.log(value[1]);
		
		var a=0; var itemName_arr = new Array();
		for(var b=1; b<=gmtName.length; b++)
		{
			itemName_arr[a]=gmtName[a];
			a++;
		}
		var count_item=gmtName.length;
		var html="";
		$("#summary_td table").remove();
		html += '<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all"><thead><tr><th colspan="'+count_item+2+'">Item Wise Cost Summary ($)</th></tr><tr><th width="120">Description</th>';
		var i=0;
		for(var r=1; r<=gmtName.length; r++)
		{
			html += '<th width="70" style="word-wrap:break-word; width:70px" id="itemname'+gmtId[i].replace(/\s+/g, "")+'">'+gmtName[i]+'</th>';
			i++;
		}
		
		html += '<th width="80">Total</th></tr></thead><tbody><tr><td>Fabric</td>';
		
		var j=0;
		for(var s=1; s<=gmtName.length; s++)
		{
			html += '<td id="fab_td'+trim(gmtId[j])+'" align="right">&nbsp;</td>';
			j++;
		}
        html += '<td id="totFab_td" align="right">&nbsp;</td></tr><tr><td>Special Operation</td>';
        var k=0;
		for(var t=1; t<=gmtName.length; t++)
		{
			html += '<td id="spOpe_td'+trim(gmtId[k])+'" align="right">&nbsp;</td>';
			k++;
		}
		
		
		html += '<td id="totSpc_td" align="right">&nbsp;</td></tr><tr><td>Wash</td>';
        var k=0;
		for(var t=1; t<=gmtName.length; t++)
		{
			html += '<td id="wash_td'+trim(gmtId[k])+'" align="right">&nbsp;</td>';
			k++;
		}
		
        html += '<td id="totWash_td" align="right">&nbsp;</td></tr><tr><td>Accessories</td>';
        var l=0;
		for(var u=1; u<=gmtName.length; u++)
		{
			html += '<td id="acc_td'+trim(gmtId[l])+'" align="right">&nbsp;</td>';
			l++;
		}
		
		html += '<td id="totAcc_td" align="right">&nbsp;</td></tr><tr><td>CPM &nbsp;&nbsp;&nbsp;<input type="checkbox" name="cmPop" id="cmPop" onClick="fnc_rate_write_popup('+"'cm'"+')" value="2" style="width:12px;" title="Is Calculative?" ></td>';
		var cpm=0;
		for(var cp=1; cp<=gmtName.length; cp++)
		{
			html += '<td align="center"><input name="txt_cpm_[]" id="txt_cpm_'+trim(gmtId[cpm])+'" class="text_boxes_numeric" style="width:40px;" title="CPM" placeholder="CPM" disabled onChange="fnc_amount_calculation(3,'+trim(gmtId[cpm])+',0);" /></td>';
			cpm++;
		}		
		
        html += '<td>&nbsp;</td></tr><tr><td>SMV / EFI</td>';
		
		var cl=0;
		for(var se=1; se<=gmtName.length; se++)
		{
			html += '<td><input name="txt_smv_[]" id="txt_smv_'+trim(gmtId[cl])+'" class="text_boxes_numeric" style="width:20px;" title="SMV" placeholder="SMV" disabled onChange="fnc_amount_calculation(3,'+trim(gmtId[cl])+',0);" /><input name="txt_eff_[]" id="txt_eff_'+trim(gmtId[cl])+'" class="text_boxes_numeric" style="width:20px;" title="Efficiency" placeholder="EFI" disabled onChange="fnc_amount_calculation(3,'+trim(gmtId[cl])+',0);" /></td>';
			cl++;
		}
		
		html += '<td>&nbsp;</td></tr><tr><td>CM</td>';
        var m=0;
		for(var v=1; v<=gmtName.length; v++)
		{
			html += '<td align="center"><input name="txtCmCost_[]" id="txtCmCost_'+trim(gmtId[m])+'" value="" class="text_boxes_numeric" style="width:40px;" onChange="fnc_amount_calculation(4,'+trim(gmtId[m])+',0);" placeholder="Write" /></td>';
			m++;
		}
		html += '<td id="totCm_td" align="right">&nbsp;</td></tr><tr><td>Frieght Cost</td>';
        var n=0;
		for(var w=1; w<=gmtName.length; w++)
		{
			html += '<td align="center"><input name="txtFriCost_[]" id="txtFriCost_'+trim(gmtId[n])+'" value="" class="text_boxes_numeric" style="width:40px;" onChange="fnc_amount_calculation(5,'+trim(gmtId[n])+',0);" placeholder="Write" /></td>';
			n++;
		}
        html += '<td id="totFriCst_td" align="right">&nbsp;</td></tr><tr><td>Courier Cost</td>';
		
		var cou=0;
		for(var w=1; w<=gmtName.length; w++)
		{
			html += '<td align="center"><input name="txtCourierCost_[]" id="txtCourierCost_'+trim(gmtId[cou])+'" value="" class="text_boxes_numeric" style="width:40px;" onChange="fnc_amount_calculation(13,'+trim(gmtId[cou])+',0);" placeholder="Write" /></td>';
			cou++;
		}
		html += '<td id="totCourierCst_td" align="right">&nbsp;</td></tr><tr><td>Lab - Test</td>';
		
        var o=0;
		for(var x=1; x<=gmtName.length; x++)
		{
			html += '<td align="center"><input name="txtLtstCost_[]" id="txtLtstCost_'+trim(gmtId[o])+'" value="" class="text_boxes_numeric" style="width:40px;" onChange="fnc_amount_calculation(6,'+gmtId[o]+',0);" placeholder="Write" /></td>';
			o++;
		}
		
		html += '<td id="totLbTstCst_td" align="right">&nbsp;</td></tr><tr><td>Insp. Cost</td>';
		
		var ins=0;
		for(var ns=1; ns<=gmtName.length; ns++)
		{
			html += '<td align="center"><input name="txtInspCost_[]" id="txtInspCost_'+trim(gmtId[ins])+'" value="" class="text_boxes_numeric" style="width:40px;" onChange="fnc_amount_calculation(15,'+gmtId[ins]+',0);" placeholder="Write" /></td>';
			ins++;
		}
        html += '<td id="totInspCst_td" align="right">&nbsp;</td></tr><tr><td>Opt. Exp.&nbsp;</td>';
		
		var opt=0;
		for(var x=1; x<=gmtName.length; x++)
		{
			html += '<td align="center"><input name="txtOptExp_[]" id="txtOptExp_'+trim(gmtId[opt])+'" value="" class="text_boxes_numeric" style="width:40px;" onChange="fnc_amount_calculation(12,'+gmtId[opt]+',0);" placeholder="Write" /></td>';
			opt++;
		}
		html += '<td id="totOptExp_td" align="right">&nbsp;</td></tr><tr style="display:none"><td>Mis/Offer Qty.&nbsp; <input name="txt_lumSum_cost" id="txt_lumSum_cost" value="" class="text_boxes_numeric" style="width:27px;" title="Lum Sum Cost" placeholder="L.S" onChange="fnc_amount_calculation(7,0,0);" /></td>';
		
		var aa=0;
		for(var bb=1; bb<=gmtName.length; bb++)
		{
			html += '<td align="center"><input name="txtMissCost_[]" id="txtMissCost_'+trim(gmtId[aa])+'" value="" class="text_boxes_numeric" style="width:40px;" onChange="fnc_amount_calculation(7,'+trim(gmtId[aa])+',0);" placeholder="Display" /></td>';
			aa++;
		}
        html += '<td id="totMissCst_td" align="right">&nbsp;</td></tr><tr><td>Other Cost</td>';
		var ab=0;
		for(var bc=1; bc<=gmtName.length; bc++)
		{
			html += '<td align="center"><input name="txtOtherCost_[]" id="txtOtherCost_'+trim(gmtId[ab])+'" value="" class="text_boxes_numeric" style="width:40px;" onChange="fnc_amount_calculation(8,'+trim(gmtId[ab])+',0);" placeholder="Write" /></td>';
			ab++;
		}
		
		html += '<td id="totOtherCst_td" align="right">&nbsp;</td></tr><tr><td title="Commercial Cost">Comml.<input type="text" name="txt_commlPer" id="txt_commlPer" value="" class="text_boxes_numeric" style="width:20px;" placeholder="%" onChange="fnc_amount_calculation(11,0,0);" /></td>';
        var p=0;
		for(var y=1; y<=gmtName.length; y++)
		{
			html += '<td align="center"><input name="txtCommlCost_[]" id="txtCommlCost_'+trim(gmtId[p])+'" value="" class="text_boxes_numeric" style="width:40px;" onChange="fnc_amount_calculation(11,'+trim(gmtId[p])+',0);" placeholder="Display" readonly /></td>';
			p++;
		}
		
        html += '<td id="totCommlCst_td" align="right">&nbsp;</td></tr><tr><td>D. LC Cost<input type="text" name="txt_lcPer" id="txt_lcPer" value="" class="text_boxes_numeric" style="width:20px;" placeholder="%" onChange="fnc_amount_calculation(14,0,0);" />%</td>';
		var dlc=0;
		for(var lc=1; lc<=gmtName.length; lc++)
		{
			html += '<td align="center"><input name="txtdlcCost_[]" id="txtdlcCost_'+trim(gmtId[dlc])+'" value="" class="text_boxes_numeric" style="width:40px;" onChange="fnc_amount_calculation(14,'+trim(gmtId[dlc])+',0);" placeholder="Display" readonly /></td>';
			dlc++;
		}
		
		html += '<td id="totDlcCst_td" align="right">&nbsp;</td></tr><tr><td title="Commission Cost">Com.(%)<input name="txt_commPer" id="txt_commPer" value="" class="text_boxes_numeric" style="width:20px;" title="Com(%)($/Pack)" placeholder="%" onChange="fnc_amount_calculation(9,0,0);" /></td>';
        var p=0;
		for(var y=1; y<=gmtName.length; y++)
		{
			html += '<td align="center" title="Commissson Cost=((Total Cost Before Commission Cost/(1-Comm %))-Total Cost Before Commission Cost);" ><input name="txtCommCost_[]" id="txtCommCost_'+trim(gmtId[p])+'" value="" class="text_boxes_numeric" style="width:40px;" onChange="fnc_amount_calculation(9,'+trim(gmtId[p])+',0);" placeholder="Display" readonly /></td>';
			p++;
		}
		
        html += '<td id="totCommCst_td" align="right">&nbsp;</td></tr><tr><td>F.O.B</td>';
		var q=0;
		for(var z=1; z<=gmtName.length; z++)
		{
            html += '<td id="fobT_td'+trim(gmtId[q])+'" align="right">&nbsp;</td>';
			q++;
		}
		
		html += '<td id="totCost_td" align="right">&nbsp;</td></tr><tr><td>F.O.B($/PCS)</td>';
		var op=0;
		for(var az=1; az<=gmtName.length; az++)
		{
            html += '<td id="fobPcsT_td'+trim(gmtId[op])+'" align="right">&nbsp;</td>';
			op++;
		}
		
        html += '<td id="totFOBCost_td" align="right">&nbsp;</td></tr><tr><td>RMG(Ratio)</td>';
		
		var ac=0;
		for(var aw=1; aw<=gmtName.length; aw++)
		{
            html += '<td align="center"><input name="txtRmgQty_[]" id="txtRmgQty_'+trim(gmtId[ac])+'" value="" class="text_boxes_numeric" style="width:40px;" onChange="fnc_amount_calculation(10,'+trim(gmtId[ac])+',0);" placeholder="Write" /></td>';
			ac++;
		}
		html += '<td id="totRmgQty_td" align="right">&nbsp;</td></tr></tbody></table>';
		
		$("#summary_td").append(html);
		
		fnc_select();
		navigate_arrow_key();
	}
	
	function fnc_dtls_ganerate(val)
	{
		var gmtdtls=val.split("__");
		var gmtId=gmtdtls[0].split(",");
		var gmtName=gmtdtls[1].split(",");
		//fnc_specialAcc_reset();
		var gmts_name=gmtName[0];
		
		//var gmtId=trim(gmtId);
		//alert(gmtId); return;
 
		/*$('#item_tbl').find('input').each(function (index, element) {
			$('#'+this.id).attr('disabled',true);
		});

 		$('#item_tbl .tr'+gmtId).find('input').each(function (index, element) {
			$('#'+this.id).attr('disabled',false);
		});*/
		
		var txt_itemConsRateData=""; var itemConsRateData="";
		/*if($("#txtfabricData_"+gmtId).val()!='')
		{*/
			var txt_itemConsRateData=""; var itemConsRateData="";
			if($("#txtfabricData_"+gmtId).val()!="")
			{
				var txt_itemConsRateData=$("#txtfabricData_"+gmtId).val();
				//alert(txt_itemConsRateData)
				var itemConsRateData=txt_itemConsRateData.split("#^");
			}
			//alert(itemConsRateData.length);
		//}
		var txt_specialData=""; var specialData="";
		if($("#txtspData_"+gmtId).val()!="")
		{
			var txt_specialData=$("#txtspData_"+gmtId).val();
			var specialData=txt_specialData.split("##");
		}
			//alert(specialData);
			
		var txt_washData=""; var washData="";
		if($("#txtwashData_"+gmtId).val()!="")
		{
			var txt_washData=$("#txtwashData_"+gmtId).val();
			var washData=txt_washData.split("##");
			var washln=washData.length-1;
		}
		else
		{
			var washln=$('#tbl_wash tbody tr').length; 
			//alert(gmtId+'qq_'+accln);
		}
		
			
		var txt_itemAccData=""; var itemAccData="";
		if($("#txtaccData_"+gmtId).val()!="")
		{
			var txt_itemAccData=$("#txtaccData_"+gmtId).val();
			var itemAccData=txt_itemAccData.split("#^");
			var accln=itemAccData.length-1;
			//alert(gmtId+'kk_'+accln);
		}
		else
		{
			var accln=$('#tbl_acc tbody tr').length; 
			//alert(gmtId+'qq_'+accln);
		}
		
		
		//alert(washln+'_'+accln);
		
		var f=9;
		for(var m=1; m<=f; m++)
		{
			$('#txtbodyparttext_'+m).val('');
			$('#txtbodypartid_'+m).val('');
			$('#txt_fabDesc'+m).val('');
			$('#txt_fabid_'+m).val('');
			$('#txtYarnDtls_'+m).val('');
			$('#cbocolortype_'+m).val(0);
			$('#cbofabuom_'+m).val(0);
			$('#txtgsmweight_'+m).val('');
			$('#txtfabwidth_'+m).val('');
			$('#txtusefor_'+m).val('');
			$('#txt_consumtion'+m).val('');
			$('#txt_exper'+m).val('');
			$('#txt_totConsumtion'+m).val('');
			//document.getElementById('ratePop_'+m).checked=false;
			$("#ratePop_"+m).attr('checked', false);
			$("#ratePop_"+m).val(2);
			$('#txtRateData_'+m).val('');
			$('#txt_rate'+m).val('');
			$('#txt_value'+m).val('');
			$('#txtfabupid_'+m).val('');
			$('#cbofabuom_'+m).val(0);
			
			$('#txt_consumtion'+m).removeAttr('readonly','readonly');
			$('#txt_exper'+m).removeAttr('readonly','readonly');
			$('#txt_rate'+m).removeAttr('readonly','readonly');
			$('#txt_consumtion'+m).attr("placeholder","Write");
			$('#txt_exper'+m).attr("placeholder","Write");
			$('#txt_rate'+m).attr("placeholder", "Write");
			$('#txt_rate'+m).removeAttr("onDblClick");
		}
		
		var sp_row=4;
		for(var n=1; n<=sp_row; n++)
		{
			$('#cboSpeciaOperationId_'+n).val('');
			$('#cboSpeciaTypeId_'+n).val('');
			$('#txtspbodyparttext_'+n).val('');
			$('#txtspbodypartid_'+n).val('');
			$('#txt_spConsumtion'+n).val('');
			$('#txt_spexper'+n).val('');
			$('#txt_totSpConsumtion'+n).val('');
			$('#txtSpRate_'+n).val('');
			$('#txt_spValue'+n).val('');
			$('#txtSpRemarks_'+n).val('');
		}
		
		var wash_row=washln;
		for(var o=1; o<=wash_row; o++)
		{
			//append_row(o+'_3');//'+"'"+counter+"_5'"+'
			
			$('#txtWashTypetext_'+o).val('');
			$('#txtWbodypartid_'+o).val('');
			$('#txtWbodyparttext_'+o).val('');
			$('#txtWbodypartid_'+o).val('');
			$('#txtWConsumtion_'+o).val('');
			$('#txtWexper_'+o).val('');
			$('#txtTotWConsumtion_'+o).val('');
			$('#txtwRate_'+o).val('');
			$('#txtWValue_'+o).val('');
			$('#txtWashRemarks_'+o).val('');
		}
		
		var acc=accln; 
		for(var q=1; q<=acc; q++)
		{
			//append_row(q+'_4');
			$('#txtAcctext_'+q).val('');
			$('#txtAccId_'+q).val('');
			$('#txtAccDescription_'+q).val('');
			$('#txtAccBandRef_'+q).val('');
			$('#txtAccUseFor_'+q).val('');
			$('#txtAccConsumtion_'+q).val('');
			$('#txtAcexper_'+q).val('');
			$('#txtTotAccConsumtion_'+q).val('');
			$('#cboconsuom_'+q).val(0);
			$('#txtAccRate_'+q).val('');
			$('#txtAccRateData_'+q).val('');
			$('#txtAccValue_'+q).val('');
			
			$('#hiddencalparameter_'+q).val('');
			$('#txtAccConsCalData_'+q).val('');
			
			//document.getElementById('accRatePop_'+q).checked=false;
			$("#accRatePop_"+q).attr('checked', false);
			$("#accRatePop_"+q).val(2);
					
			$('#txtAccConsumtion_'+q).removeAttr('readonly','readonly');
			$('#txtAcexper_'+q).removeAttr('readonly','readonly');
			$('#txtAccRate_'+q).removeAttr('readonly','readonly');
			$('#txtAccConsumtion_'+q).attr("placeholder","Write");
			$('#txtAcexper_'+q).attr("placeholder","Write");
			$('#txtAccRate_'+q).attr("placeholder", "Write");
			$('#txtAccRate_'+q).removeAttr("onDblClick");
			//alert(q)
		}
		
		
		if($("#txtfabricData_"+gmtId).val()=='')
		{
			//fnc_change_data();
		}
		
		//alert(specialData.length)
		//var itemConsRate_fab=""; var specialData_sp="";
		var f=9; var q=0;
		if(itemConsRateData!="")
		{
			for(var m=1; m<=f; m++)
			{
				//fnc_change_data();
				var itemConsRate_fab="";
				var itemConsRate_fab=itemConsRateData[q].split("~!~");
				
				var bodypart=bodypartid=cons=exper=totCons=ratePop=rate=fabVal=fabRemarks=fabupid=fabuom=yarnDtls="";
				
				var bodypart=itemConsRate_fab[0];
				var bodypartid=itemConsRate_fab[1];
				var fabDesc=itemConsRate_fab[2];
				var fabid=itemConsRate_fab[3];
				var colortype=itemConsRate_fab[4];
				var gsmweight=itemConsRate_fab[5];
				var fabwidth=itemConsRate_fab[6];
				var usefor=itemConsRate_fab[7];
				
				var cons=itemConsRate_fab[8];
				var exper=itemConsRate_fab[9];
				var totCons=itemConsRate_fab[10];
				var ratePop=itemConsRate_fab[11];
				var rateData=itemConsRate_fab[12];
				var rate=itemConsRate_fab[13];
				var fabVal=itemConsRate_fab[14];
				var fabupid=itemConsRate_fab[15];
				var fabuom=itemConsRate_fab[16];
				var yarnDtls=itemConsRate_fab[17];
				
				$('#txtbodyparttext_'+m).val(bodypart);
				$('#txtbodypartid_'+m).val(bodypartid);
				$('#txt_fabDesc'+m).val(fabDesc);
				$('#txt_fabid_'+m).val(fabid);
				$('#txtYarnDtls_'+m).val(yarnDtls);
				$('#cbocolortype_'+m).val(colortype);
				$('#cbofabuom_'+m).val(fabuom);
				$('#txtgsmweight_'+m).val(gsmweight);
				$('#txtfabwidth_'+m).val(fabwidth);
				$('#txtusefor_'+m).val(usefor);
				$('#txt_consumtion'+m).val(cons);
				$('#txt_exper'+m).val(exper);
				$('#txt_totConsumtion'+m).val(totCons);
				$('#cbofabuom_'+m).val(fabuom);
				if(ratePop==2)
				{
					//document.getElementById('ratePop_'+m).checked=false;
					$("#ratePop_"+m).attr("checked", false);
					$("#ratePop_"+m).val(2);
					$('#txt_rate'+m).removeAttr("onDblClick");
					$('#txtRateData_'+m).val('');
					
					$('#txt_consumtion'+m).removeAttr('readonly','readonly');
					//$('#txt_exper'+m).removeAttr('readonly','readonly');
					$('#txt_rate'+m).removeAttr('readonly','readonly');
					$('#txt_consumtion'+m).attr("placeholder","Write");
					//$('#txt_exper'+m).attr("placeholder","Write");
					$('#txt_rate'+m).attr("placeholder", "Write");
					$('#txt_rate'+m).removeAttr("onDblClick");
				}
				else if(ratePop==1)
				{
					//document.getElementById('ratePop_'+m).checked=true;
					$("#ratePop_"+m).attr("checked", true);
					$("#ratePop_"+m).val(1);
					$('#txt_rate'+m).removeAttr("onDblClick").attr("onDblClick","fnc_openmypage_rate("+m+",'1');");
					$('#txtRateData_'+m).val(rateData);
					
					//$('#txt_consumtion'+m).attr('readonly','readonly');
					//$('#txt_exper'+m).attr('readonly','readonly');
					$('#txt_rate'+m).attr('readonly','readonly');
					$('#txt_consumtion'+m).attr("placeholder","Write");
					//$('#txt_exper'+m).attr("placeholder","Display");
					$('#txt_rate'+m).attr("placeholder", "Browse");
				}
				
				$('#txt_rate'+m).val(rate);
				$('#txt_value'+m).val(fabVal);
				$('#txtfabupid_'+m).val(fabupid);
				
				q++;
			}
		}
			
		var sp=4; var q=0;
		if(specialData!="")
		{
			for(var n=1; n<=sp; n++)
			{
				var spConsRate="";
				var spConsRate=specialData[q].split("_");
				//alert(spConsRate);
				
				var speciaOperationId=speciaTypeId=spbodyparttext=spbodypartid=spConsumtion=spexper=totSpConsumtion=rate=spVal=spRemarks="";
				
				var speciaOperationId=spConsRate[0];
				var speciaTypeId=spConsRate[1];
				var spbodyparttext=spConsRate[2];
				var spbodypartid=spConsRate[3];
				var spConsumtion=spConsRate[4];
				var spexper=spConsRate[5];
				var totSpConsumtion=spConsRate[6];
				var rate=spConsRate[7];
				var spVal=spConsRate[8];
				var spRemarks=spConsRate[9];
				
				$('#cboSpeciaOperationId_'+n).val(speciaOperationId);
				$('#cboSpeciaTypeId_'+n).val(speciaTypeId);
				$('#txtspbodyparttext_'+n).val(spbodyparttext);
				$('#txtspbodypartid_'+n).val(spbodypartid);
				$('#txt_spConsumtion'+n).val(spConsumtion);
				$('#txt_spexper'+n).val(spexper);
				$('#txt_totSpConsumtion'+n).val(totSpConsumtion);
				$('#txtSpRate_'+n).val(rate);
				$('#txt_spValue'+n).val(spVal);
				$('#txtSpRemarks_'+n).val(spRemarks);
				q++;
			}
		}
			
		//var w=$('#tbl_wash tbody tr').length; 
		var w=washln;
		//alert(w);
		var q=0;
		if(washData!="")
		{
			for(var o=1; o<=w; o++)
			{
				if(o>1) append_row(q,1);
				var washConsRate="";
				var washConsRate=washData[q].split("_");
				
				var washTypetext=washTypeid=wbodyparttext=wbodypartid=wConsumtion=wexper=totwConsumtion=rate=wVal=wRemarks="";
				
				var washTypetext=washConsRate[0];
				var washTypeid=washConsRate[1];
				var wbodyparttext=washConsRate[2];
				var wbodypartid=washConsRate[3];
				var wConsumtion=washConsRate[4];
				var wexper=washConsRate[5];
				var totwConsumtion=washConsRate[6];
				var rate=washConsRate[7];
				var wVal=washConsRate[8];
				var wRemarks=washConsRate[9];
				
				$('#txtWashTypetext_'+o).val(washTypetext);
				$('#txtWashTypeId_'+o).val(washTypeid);
				$('#txtWbodyparttext_'+o).val(wbodyparttext);
				$('#txtWbodypartid_'+o).val(wbodypartid);
				$('#txtWConsumtion_'+o).val(wConsumtion);
				$('#txtWexper_'+o).val(wexper);
				$('#txtTotWConsumtion_'+o).val(totwConsumtion);
				$('#txtwRate_'+o).val(rate);
				$('#txtWValue_'+o).val(wVal);
				$('#txtWashRemarks_'+o).val(wRemarks);
				q++;
			}
		}
		
		var accs=accln;
				
		//var accs=$('#tbl_acc tbody tr').length; 
		var adata=0;// itemAccData_ac='';
		if(itemAccData!="")
		{
			for(var q=1; q<=accs; q++)
			{
				var accData="";
				if(itemAccData[adata]!=undefined)
				{
					if(q>1) append_row(adata,2);
					var accData=itemAccData[adata].split("~!~");
				
					var acctext=accId=accDescription=accBandRef=accUseFor=accConsumtion=acexper=totAccConsumtion=acRate=acValue=acconsUom=acconsCal=accRateData=accConsCalData=accCalParaMeter="";
					
					var acctext=accData[0];
					var accId=accData[1];
					var accDescription=accData[2];
					var accBandRef=accData[3];
					var accConsumtion=accData[4];
					var acexper=accData[5];
					var totAccConsumtion=accData[6];
					var acRate=accData[7];
					var acValue=accData[8];
					var accUseFor=accData[9];
					var acconsUom=accData[10];
					//var acconsCal=accData[11];
					//var accRateData=accData[12];
					var accConsCalData=accData[14];
					var accCalParaMeter=trim(accData[13]);
					
					
					$('#txtAcctext_'+q).val(acctext);
					$('#txtAccId_'+q).val(accId);
					$('#txtAccDescription_'+q).val(accDescription);
					$('#txtAccBandRef_'+q).val(accBandRef);
					$('#txtAccConsumtion_'+q).val(accConsumtion);
					$('#txtAcexper_'+q).val(acexper);
					$('#txtTotAccConsumtion_'+q).val(totAccConsumtion);
					$('#cboconsuom_'+q).val(acconsUom);
					
					if(accCalParaMeter==1 || accCalParaMeter==2 || accCalParaMeter==3 || accCalParaMeter==4 || accCalParaMeter==5 || accCalParaMeter==6 || accCalParaMeter==7 || accCalParaMeter==8 || accCalParaMeter==9 || accCalParaMeter==10 || accCalParaMeter==11 || accCalParaMeter==12 || accCalParaMeter==13)
					{
						$('#txtAccConsumtion_'+q).removeAttr("onDblClick").attr("onDblClick","fnc_openmypage_calparameter("+accId+",'"+acctext+"',"+accCalParaMeter+","+q+");");
						//$('#txtAccConsumtion_'+q).attr('readonly','readonly');
						$('#txtAccConsumtion_'+q).attr("placeholder", "Browse/Write");
					}
					else
					{
						//$('#txtaccConsumtion_'+q).removeAttr('readonly','readonly');
						$('#txtAccConsumtion_'+q).attr('placeholder','');
						$('#txtAccConsumtion_'+q).removeAttr("onDblClick");
					}
					
					$('#hiddencalparameter_'+q).val(accCalParaMeter);
					$('#txtAccConsCalData_'+q).val(accConsCalData);
					
					/*if(acconsCal==2)
					{
						//document.getElementById('ratePop_'+m).checked=false;
						$("#accRatePop_"+q).attr("checked", false);
						$("#accRatePop_"+q).val(2);
						$('#txtAccRate_'+q).removeAttr("onDblClick");
						$('#txtAccRateData_'+q).val('');
					}
					else if(acconsCal==1)
					{
						//document.getElementById('ratePop_'+m).checked=true;
						$("#accRatePop_"+q).attr("checked", true);
						$("#accRatePop_"+q).val(1);
						$('#txtAccRate_'+q).removeAttr("onDblClick").attr("onDblClick","fnc_openmypage_rate("+q+",'2');");
						$('#txtAccRateData_'+q).val(accRateData);
					}*/
					$('#txtAccRate_'+q).val(acRate);
					$('#txtAccValue_'+q).val(acValue);
					$('#txtAccUseFor_'+q).val(accUseFor);
					adata++;
				}
			}
		}
		fnc_itemwise_data_cache();
		//navigate_arrow_key();
		//fnc_consumption_write_disable( $("#cbo_cons_basis_id").val()+"__"+0 );
	}
	
	function fnc_change_data()
	{
		var tmpData=get_dropdown_text('cbo_temp_id');
		var itemid=$("#cboItemId").val();
		if($("#cbo_temp_id").val()!=0)
		{
			var extmpData=tmpData.split(","); var packQty=0; var r=0; var fobpcsTot=0;
			for(var s=1; s<=extmpData.length; s++)
			{
				var itemRatio=extmpData[r].split("::");
				packQty+=(itemRatio[1]*1);
				fobpcsTot+=($('#fobPcsT_td'+itemid).text()*1);
				//alert(($('#fobPcsT_td'+itemid).text()*1))
				r++;
			}
			$("#totFOBCost_td").html(fobpcsTot);
			$("#tdpackQty").html(packQty);
			
			var itemDetails=get_dropdown_text('cboItemId');
			var itemRatio=itemDetails.split("::");
			if($("#cboItemId").val()==0) itemRatio[1]=0;
			$("#tditemRatio").html(itemRatio[1]);
			//fnc_itemwise_data_cache();
			var tmpitemname=get_dropdown_text('cboItemId');
			fnc_dtls_ganerate(trim($("#cboItemId").val())+"__"+tmpitemname);
		}
	}
	
	function fnc_amount_calculation(type,inc_id,rate)
	{
		var item_id=$("#cboItemId").val();
		var costing_perid=$("#cbo_costing_per").val()*1;
		
		if(costing_perid=="") costing_perid=0;
		if(costing_perid==0)
		{
			alert("Please Select Costing Per.");
			$("#cbo_costing_per").focus();
			return;
		}
		var hid_all_item_id=$("#txt_temp_id").val();
		var all_item_id_hid=hid_all_item_id.split(",");
		//var itemRatio=$("#tditemRatio").html()*1;
		var item_des=$('#itemname'+inc_id).text();
		var itemRatio_arr=item_des.split("::");
		var itemRatio=itemRatio_arr[1];
		//alert(hid_all_item_id+'='+inc_id+'='+all_item_id_hid)
		var itemWiseTot_arr = new Array();
		
		var costingper=0;
		if(costing_perid==1) costingper=12;
		else if(costing_perid==2) costingper=1;
		else costingper=0;
		
		if (type=='fabric')
		{
			var itemRatio=$("#tditemRatio").html()*1;
			var fabWidth=($("#txtfabwidth_"+inc_id).val()*1);
			var fabGsm=($("#txtgsmweight_"+inc_id).val()*1);
			var fabUom=($("#cbofabuom_"+inc_id).val()*1);
			var isitemratepop=($("#ratePop_"+inc_id).val()*1);
			var rateData=$("#txtRateData_"+inc_id).val();
			var fabItemRatio=0;
			if(isitemratepop==1 && rateData!="")
			{
				var exrateData=rateData.split("@@!");
				var packRatio=0; var r=0;
				for(var s=1; s<=exrateData.length; s++)
				{
					var exPackRatio=exrateData[r].split("_");
					packRatio+=(exPackRatio[3]*1);
					r++;
				}
				if((packRatio*1)>0) fabItemRatio=packRatio; else fabItemRatio=itemRatio;
			}
			else fabItemRatio=itemRatio;
			var consFab=0;
			
			var conskgpack=$("#txt_consumtion"+inc_id).val()*1;
			
			var process_loss_method_id=$("#txt_fab_process_loss_method").val()*1;
			//alert(process_loss_method_id)
			var exper=$("#txt_exper"+inc_id).val()*1;
			var totCons=0;
			if(process_loss_method_id==1)
			{
				totCons=conskgpack+(conskgpack*(exper/100));
			}
			else if(process_loss_method_id==2)
			{
				var devided_val = 1-(exper/100);
				totCons=parseFloat(conskgpack/devided_val);
			}
			else totCons=0;
			
			if(exper=="" || exper==0) { var totCons=conskgpack; }
			totCons=number_format(totCons,4,'.','');
			//else { var totCons=conspackkg+(conspackkg*(exper/100)); }
			$("#txt_totConsumtion"+inc_id).val( totCons );
			var fabVal=totCons*($("#txt_rate"+inc_id).val()*1);
			fabVal=number_format(fabVal,4,'.','');
			$("#txt_value"+inc_id).val( fabVal );
			
			var fab_item_cost=0;
			for(var i=1; i<=9; i++)
			{
				fab_item_cost=fab_item_cost+($("#txt_value"+i).val()*1);
			}
			//alert(fab_item_cost+'='+item_id)
			$("#fab_td"+item_id).text( number_format(fab_item_cost,4,'.','') );
			//alert($("#fab_td"+item_id).text())
			var r=0; var row_fab_cost=0;
			for(var s=1; s<=all_item_id_hid.length; s++)
			{
				row_fab_cost=row_fab_cost+($("#fab_td"+trim(all_item_id_hid[r])).text()*1);
				r++;
			}
			//alert(fab_item_cost)
			
			$("#totFab_td").text( number_format(row_fab_cost,4,'.',''));
		}
		else if (type=='special')
		{
			//alert(rate)
			var cons_val=''; var sp_rate=''; var row_tot_value=''; var ex_spper="";
			cons_val=$("#txt_spConsumtion"+inc_id).val();
			ex_spper=$("#txt_spexper"+inc_id).val()*1;
			var ex_sp_cons=0;
			if(ex_spper!=0) ex_sp_cons=((cons_val*1)+((cons_val*ex_spper)/100));
			else ex_sp_cons=(cons_val*1);
			$("#txt_totSpConsumtion"+inc_id).val( number_format(ex_sp_cons,4,'.','') );
			sp_rate=$("#txtSpRate_"+inc_id).val();
			row_tot_value=(ex_sp_cons*1)*sp_rate;
			$("#txt_spValue"+inc_id).val(number_format(row_tot_value,4,'.',''));
			
			var sp_item_cost=0;
			for(var i=1; i<=4; i++)
			{
				sp_item_cost=sp_item_cost+($("#txt_spValue"+i).val()*1);
			}
			$("#spOpe_td"+item_id).text( number_format(sp_item_cost,4,'.','') );
			var t=0; var item_fob=0; var row_special_cost=0;
			for(var p=1; p<=all_item_id_hid.length; p++)
			{
				row_special_cost=row_special_cost+($("#spOpe_td"+trim(all_item_id_hid[t])).text()*1);
				t++;
			}
			$("#totSpc_td").text( number_format(row_special_cost,4,'.',''));
		}
		else if (type=='wash')
		{
			//alert(rate)
			var cons_val=''; var w_rate=''; var row_tot_value=''; var ex_wper="";
			cons_val=$("#txtWConsumtion_"+inc_id).val();
			ex_wper=$("#txtWexper_"+inc_id).val()*1;
			var ex_wash_cons=0;
			if(ex_spper!=0) ex_wash_cons=((cons_val*1)+((cons_val*ex_wper)/100));
			else ex_wash_cons=(cons_val*1);
			$("#txtTotWConsumtion_"+inc_id).val( number_format(ex_wash_cons,4,'.','') );
			w_rate=$("#txtwRate_"+inc_id).val()*1;
			row_tot_value=(ex_wash_cons*1)*w_rate;
			$("#txtWValue_"+inc_id).val(number_format(row_tot_value,4,'.',''));
			
			var w_item_cost=0;
			var washtr=$('#tbl_wash tbody tr').length; 
			for(var i=1; i<=washtr; i++)
			{
				w_item_cost=w_item_cost+($("#txtWValue_"+i).val()*1);
			}
			$("#wash_td"+item_id).text( number_format(w_item_cost,4,'.','') );
			var t=0; var item_fob=0; var row_wash_cost=0;
			for(var p=1; p<=all_item_id_hid.length; p++)
			{
				row_wash_cost=row_wash_cost+($("#wash_td"+trim(all_item_id_hid[t])).text()*1);
				t++;
			}
			$("#totWash_td").text( number_format(row_wash_cost,4,'.',''));
		}
		else if (type=='accessories')
		{
			var itemRatio=$('#tditemRatio').text()*1;
			
			var isitemratepop=($("#accRatePop_"+inc_id).val()*1);
			var rateData=$("#txtAccRateData_"+inc_id).val();
			var accItemRatio=0;
			if(isitemratepop==1 && rateData!="")
			{
				var exrateData=rateData.split("@@!");
				var packRatio=0; var r=0;
				for(var s=1; s<=exrateData.length; s++)
				{
					var exPackRatio=exrateData[r].split("_");
					packRatio+=(exPackRatio[3]*1);
					r++;
				}
				if((packRatio*1)>0) accItemRatio=packRatio; else accItemRatio=itemRatio;
			}
			else accItemRatio=itemRatio;
			
			var consAccPack=$("#txtAccConsumtion_"+inc_id).val()*1;
			
			var process_loss_method_id=$("#txt_acc_process_loss_method").val()*1;
			var ex_acper=$("#txtAcexper_"+inc_id).val()*1;
			var totConsAcc=0;
			if(process_loss_method_id==1)
			{
				totConsAcc=consAccPack+(consAccPack*(ex_acper/100));
			}
			else if(process_loss_method_id==2)
			{
				var devided_val = 1-(ex_acper/100);
				totConsAcc=parseFloat(consAccPack/devided_val);
			}
			else totConsAcc=0;
			
			if(ex_acper==0) totConsAcc=(consAccPack*1);
			$("#txtTotAccConsumtion_"+inc_id).val( number_format(totConsAcc,4,'.','') );
			var accRate=$("#txtAccRate_"+inc_id).val()*1;
			var row_tot_value=(totConsAcc*1)*accRate;
			$("#txtAccValue_"+inc_id).val(number_format(row_tot_value,4,'.',''));
			
			var acctr=$('#tbl_acc tbody tr').length;
			var acc_item_cost=0;
			for(var i=1; i<=acctr; i++)
			{
				acc_item_cost=acc_item_cost+($("#txtAccValue_"+i).val()*1);
			}
			$("#acc_td"+item_id).text( number_format(acc_item_cost,4,'.','') );
			var t=0; var item_fob=0; var row_accessories_cost=0;
			for(var p=1; p<=all_item_id_hid.length; p++)
			{
				row_accessories_cost=row_accessories_cost+($("#acc_td"+trim(all_item_id_hid[t])).text()*1);
				t++;
			}
			$("#totAcc_td").text( number_format(row_accessories_cost,4,'.',''));
		}
		else if (type=='3')
		{
			var cc=1; var e=0; var item_cm=0; var row_cm_cost=0; var cpm_from_financial_parameter=0; 
			var item_des=$('#itemname'+inc_id).text();
			var itemRatio_arr=item_des.split("::");
			var itemRatio=itemRatio_arr[1];
			console.log(itemRatio);
			if($("#cmPop").val()==1)
			{
				var costing_date=$("#txt_costingDate").val();
				if(cc==1)
				{
					//var cpm_from_financial_parameter=return_ajax_request_value(costing_date, 'cpm_check_load', 'requires/short_quotation_v6_controller');
					cc++;
				}
				
				var cpm=0; var smv=0; var eff=0; 
				var exchange_rate=($("#txt_exchangeRate").val()*1);
				cpm=($('#txt_cpm_'+inc_id).val()*1);
				smv=($('#txt_smv_'+inc_id).val()*1);
				eff=($('#txt_eff_'+inc_id).val()*1);
				
				//var cm_cost=((smv*cpm_from_financial_parameter)*12+(smv*cpm_from_financial_parameter*12)*eff)/exchange_rate;
				var cm_cost=(((cpm*100)/eff)*smv)*itemRatio;
				$("#txtCmCost_"+inc_id).val( number_format(cm_cost,4,'.',''));
				fnc_amount_calculation(4,inc_id,0)
			}
		}
		else if (type=='4')
		{
			var e=0; var item_cm=0; var row_cm_cost=0;
			for(var f=1; f<=all_item_id_hid.length; f++)
			{
				row_cm_cost=row_cm_cost+($("#txtCmCost_"+trim(all_item_id_hid[e])).val()*1);
				e++;
			}
			$("#totCm_td").text( number_format(row_cm_cost,4,'.',''));
		}
		else if (type=='5')
		{
			var g=0; var item_cm=0; var row_fri_cost=0;
			for(var h=1; h<=all_item_id_hid.length; h++)
			{
				row_fri_cost=row_fri_cost+($("#txtFriCost_"+trim(all_item_id_hid[g])).val()*1);
				g++;
			}
			$("#totFriCst_td").text( number_format(row_fri_cost,4,'.',''));
		}
		else if (type=='6')
		{
			var l=0; var item_cm=0; var row_lab_cost=0;
			for(var m=1; m<=all_item_id_hid.length; m++)
			{
				row_lab_cost=row_lab_cost+($("#txtLtstCost_"+trim(all_item_id_hid[l])).val()*1);
				l++;
			}
			$("#totLbTstCst_td").text( number_format(row_lab_cost,4,'.',''));
		}
		else if (type=='7')
		{
			var lum_sum_cost=($("#txt_lumSum_cost").val()*1);
			var offer_qty=($("#txt_offerQty").val()*1);
			if($("#txt_offerQty").val()!="")
			{
				var item_wise_miss_cost=(lum_sum_cost/offer_qty)*costingper;
				
				var aa=0; var row_miss_cost=0;
				for(var ab=1; ab<=all_item_id_hid.length; ab++)
				{
					//alert("#txtMissCost_"+trim(all_item_id_hid[aa]))
					$("#txtMissCost_"+trim(all_item_id_hid[aa])).val( number_format(item_wise_miss_cost,4,'.','') );
					row_miss_cost=row_miss_cost+($("#txtMissCost_"+trim(all_item_id_hid[aa])).val()*1);
					aa++;
				}
				$("#totMissCst_td").text( number_format(row_miss_cost,4,'.',''));
			}
			else
			{
				alert("Please fill up Offer Qty Field Value.")
				return;
			}
		}
		else if (type=='8')
		{
			var ab=0; var row_other_cost=0;
			for(var bc=1; bc<=all_item_id_hid.length; bc++)
			{
				row_other_cost=row_other_cost+($("#txtOtherCost_"+trim(all_item_id_hid[ab])).val()*1);
				ab++;
			}
			$("#totOtherCst_td").text( number_format(row_other_cost,4,'.',''));
		}
		else if (type=='9')
		{
			var aa=0; var itemFoBarr=new Array();
			for(var bb=1; bb<=all_item_id_hid.length; bb++)
			{
				var before_commission_cost=0; var itemWiseTot=0; var commissson_cost=0; var commPer=0; var mis_cost=0;
				
				mis_cost=(($("#txt_lumSum_cost").val()*1)/($("#txt_offerQty").val()*1))*itemRatio;
				
				$("#txtMissCost_"+trim(all_item_id_hid[aa])).val( number_format(mis_cost,4,'.','') );
				
				before_commission_cost=($("#fab_td"+trim(all_item_id_hid[aa])).text()*1)+($("#spOpe_td"+trim(all_item_id_hid[aa])).text()*1)+($("#wash_td"+trim(all_item_id_hid[aa])).text()*1)+($("#acc_td"+trim(all_item_id_hid[aa])).text()*1)+($("#txtCmCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtFriCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtCourierCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtLtstCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtMissCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtOtherCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtCommlCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtOptExp_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtInspCost_"+trim(all_item_id_hid[aa])).val()*1);
				commPer=($("#txt_commPer").val()*1)/100;
				//alert("#txtCommCost_"+trim(all_item_id_hid[aa]));
				commissson_cost=((before_commission_cost/(1-commPer))-before_commission_cost);
				$("#txtCommCost_"+trim(all_item_id_hid[aa])).val( number_format(commissson_cost,4,'.','') );
				itemWiseTot=before_commission_cost+($("#txtCommCost_"+trim(all_item_id_hid[aa])).val()*1);
				var item_id_hid=trim(all_item_id_hid[aa]);
				itemFoBarr[item_id_hid]=itemWiseTot;
				//$("#fobT_td"+trim(all_item_id_hid[aa])).text( number_format(itemWiseTot,4,'.',''));
				//
				aa++;
			}
			
			var p=0; var item_cm=0; var row_commision_cost=0;
			for(var q=1; q<=all_item_id_hid.length; q++)
			{
				row_commision_cost=row_commision_cost+($("#txtCommCost_"+trim(all_item_id_hid[p])).val()*1);
				p++;//alert(row_commision_cost);
			}
			
			$("#totCommCst_td").text( number_format(row_commision_cost,4,'.',''));
		}
		else if (type=='10')
		{
			var aw=0; var row_rmgQty=0;
			for(var aq=1; aq<=all_item_id_hid.length; aq++)
			{
				row_rmgQty=row_rmgQty+($("#txtRmgQty_"+trim(all_item_id_hid[aw])).val()*1);
				aw++;
			}
			$("#totRmgQty_td").text(row_rmgQty);
		}
		else if (type=='11')
		{
			var aa=0; var row_cl=0;
			for(var bb=1; bb<=all_item_id_hid.length; bb++)
			{
				var before_commercial_cost=0; var commercial_cost=0; var commlPer=0;
				
				before_commercial_cost=($("#fab_td"+trim(all_item_id_hid[aa])).text()*1)+($("#spOpe_td"+trim(all_item_id_hid[aa])).text()*1)+($("#wash_td"+trim(all_item_id_hid[aa])).text()*1)+($("#acc_td"+trim(all_item_id_hid[aa])).text()*1)+($("#txtCmCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtFriCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtCourierCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtLtstCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtMissCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtOtherCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtOptExp_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtInspCost_"+trim(all_item_id_hid[aa])).val()*1);
				commlPer=($("#txt_commlPer").val()*1)/100;
				//alert("#txtCommCost_"+trim(all_item_id_hid[aa]));
				commercial_cost=(before_commercial_cost*commlPer);
				$("#txtCommlCost_"+trim(all_item_id_hid[aa])).val( number_format(commercial_cost,4,'.','') );
				row_cl=row_cl+commercial_cost;
				//$("#fobT_td"+trim(all_item_id_hid[aa])).text( number_format(itemWiseTot,4,'.',''));
				//
				aa++;
			}
			
			/*var cl=0; 
			for(var lc=1; lc<=all_item_id_hid.length; lc++)
			{
				row_cl=row_cl+($("#txtCommlCost_"+trim(all_item_id_hid[cl])).val()*1);
				cl++;
			}*/
			$("#totCommlCst_td").text(number_format(row_cl,4,'.',''));
		}
		else if (type=='12')//Opt. Exp. 
		{
			var ab=0; var row_optexp_cost=0;
			for(var bc=1; bc<=all_item_id_hid.length; bc++)
			{
				row_optexp_cost=row_optexp_cost+($("#txtOptExp_"+trim(all_item_id_hid[ab])).val()*1);
				ab++;
			}
			$("#totOptExp_td").text( number_format(row_optexp_cost,4,'.',''));
		}
		else if (type=='13')//Courier Cost
		{
			var cou=0; var item_cou=0; var row_cou_cost=0;
			for(var h=1; h<=all_item_id_hid.length; h++)
			{
				row_cou_cost=row_cou_cost+($("#txtCourierCost_"+trim(all_item_id_hid[cou])).val()*1);
				cou++;
			}
			$("#totCourierCst_td").text( number_format(row_cou_cost,4,'.',''));
		}
		else if (type=='14')//D. LC Cost
		{
			var aa=0; var itemFoBarr=new Array();
			for(var bb=1; bb<=all_item_id_hid.length; bb++)
			{
				var before_commission_cost=0; var itemWiseTot=0; var lc_cost=0; var lcPer=0; var mis_cost=0;
				
				mis_cost=(($("#txt_lumSum_cost").val()*1)/($("#txt_offerQty").val()*1))*12;
				
				$("#txtMissCost_"+trim(all_item_id_hid[aa])).val( number_format(mis_cost,4,'.','') );
				
				before_commission_cost=($("#fab_td"+trim(all_item_id_hid[aa])).text()*1)+($("#spOpe_td"+trim(all_item_id_hid[aa])).text()*1)+($("#wash_td"+trim(all_item_id_hid[aa])).text()*1)+($("#acc_td"+trim(all_item_id_hid[aa])).text()*1)+($("#txtCmCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtFriCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtCourierCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtLtstCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtMissCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtOtherCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtCommlCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtInspCost_"+trim(all_item_id_hid[aa])).val()*1);
				
				lcPer=($("#txt_lcPer").val()*1)/100;
				lc_cost=((before_commission_cost/(1-lcPer))-before_commission_cost);
				$("#txtdlcCost_"+trim(all_item_id_hid[aa])).val( number_format(lc_cost,4,'.','') );
				itemWiseTot=before_commission_cost+($("#txtdlcCost_"+trim(all_item_id_hid[aa])).val()*1);
				var item_id_hid=trim(all_item_id_hid[aa]);
				itemFoBarr[item_id_hid]=itemWiseTot;
				//$("#fobT_td"+trim(all_item_id_hid[aa])).text( number_format(itemWiseTot,4,'.',''));
				aa++;
			}
			
			var p=0; var item_cm=0; var row_lc_cost=0;
			for(var q=1; q<=all_item_id_hid.length; q++)
			{
				row_lc_cost=row_lc_cost+($("#txtdlcCost_"+trim(all_item_id_hid[p])).val()*1);
				p++;//alert(row_lc_cost);
			}
			
			$("#totDlcCst_td").text( number_format(row_lc_cost,4,'.',''));
		}
		else if (type=='15')//Inspection Cost
		{
			var ins=0; var item_ins=0; var row_ins_cost=0;
			for(var ns=1; ns<=all_item_id_hid.length; ns++)
			{
				row_ins_cost=row_ins_cost+($("#txtInspCost_"+trim(all_item_id_hid[ins])).val()*1);
				ins++;
			}
			$("#totInspCst_td").text( number_format(row_ins_cost,4,'.',''));
		}
		//alert(all_item_id_hid)
		var aa=0; var itemFoBarr=new Array(); var val_td_fob=0; var totCommercial_Cost=totCommiss_Cost=totDlcCost=0;
		for(var bb=1; bb<=all_item_id_hid.length; bb++)
		{
			var before_commercial_cost=before_commission_cost=0; var itemWiseTot=0; var commercial_cost=commissson_cost=0; var commlPer=commPer=0; var mis_cost=0; var dlccost=lcPer=0;
			
			mis_cost=(($("#txt_lumSum_cost").val()*1)/($("#txt_offerQty").val()*1));
			
			$("#txtMissCost_"+trim(all_item_id_hid[aa])).val( number_format(mis_cost,4,'.','') );
			
			before_commercial_cost=($("#fab_td"+trim(all_item_id_hid[aa])).text()*1)+($("#spOpe_td"+trim(all_item_id_hid[aa])).text()*1)+($("#wash_td"+trim(all_item_id_hid[aa])).text()*1)+($("#acc_td"+trim(all_item_id_hid[aa])).text()*1)+($("#txtCmCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtFriCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtCourierCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtLtstCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtMissCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtOtherCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtOptExp_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtInspCost_"+trim(all_item_id_hid[aa])).val()*1);
			
			commlPer=($("#txt_commlPer").val()*1)/100;
			commercial_cost=(before_commercial_cost*commlPer);
			totCommercial_Cost+=commercial_cost;
			
			$("#txtCommlCost_"+trim(all_item_id_hid[aa])).val( number_format(commercial_cost,4,'.','') );
			
			lcPer=(($("#txt_lcPer").val()*1)/100);
			dlccost=(((before_commercial_cost+commercial_cost)/(1-lcPer))-(before_commercial_cost+commercial_cost));
			
			$("#txtdlcCost_"+trim(all_item_id_hid[aa])).val( number_format(dlccost,4,'.','') );
			
			totDlcCost+=dlccost;
			
			before_commission_cost=before_commercial_cost+commercial_cost+($("#txtdlcCost_"+trim(all_item_id_hid[aa])).val()*1);
			commPer=($("#txt_commPer").val()*1)/100;
			
			//alert(($("#txtInspCost_"+trim(all_item_id_hid[aa])).val()*1));
			commissson_cost=((before_commission_cost/(1-commPer))-before_commission_cost);
			totCommiss_Cost+=commissson_cost;
			$("#txtCommCost_"+trim(all_item_id_hid[aa])).val( number_format(commissson_cost,4,'.','') );
			itemWiseTot=before_commission_cost+($("#txtCommCost_"+trim(all_item_id_hid[aa])).val()*1);
			var item_id_hid=trim(all_item_id_hid[aa]);
			itemFoBarr[item_id_hid]=itemWiseTot;
			//alert(before_commercial_cost+'='+commercial_cost);
			$("#fobT_td"+trim(all_item_id_hid[aa])).text( number_format(itemWiseTot,4,'.',''));
			$("#fobPcsT_td"+trim(all_item_id_hid[aa])).text( number_format(itemWiseTot/itemRatio,4,'.',''));
			var fob_rmg=0;
			if(($("#txtRmgQty_"+trim(all_item_id_hid[aa])).val()*1)!=0)
			{
				fob_rmg=(($("#txtRmgQty_"+trim(all_item_id_hid[aa])).val()*1)*($("#fobPcsT_td"+trim(all_item_id_hid[aa])).text()*1));
			}
			else
			{
				fob_rmg=($("#fobPcsT_td"+trim(all_item_id_hid[aa])).text()*1);
			}
			val_td_fob+=fob_rmg;
			//
			aa++;
		}
		//alert(fob_rmg);
		$("#totCommlCst_td").text( number_format(totCommercial_Cost,4,'.',''));
		$("#totDlcCst_td").text( number_format(totDlcCost,4,'.',''));
		$("#totCommCst_td").text( number_format(totCommiss_Cost,4,'.',''));
		
		var packQty=$("#tdpackQty").html()*1;
		var tot_cost=($("#totFab_td").text()*1)+($("#totSpc_td").text()*1)+($("#totWash_td").text()*1)+($("#totAcc_td").text()*1)+($("#totCm_td").text()*1)+($("#totFriCst_td").text()*1)+($("#totCourierCst_td").text()*1)+($("#totLbTstCst_td").text()*1)+($("#totMissCst_td").text()*1)+($("#totOtherCst_td").text()*1)+($("#totCommlCst_td").text()*1)+($("#totCommCst_td").text()*1)+($("#totOptExp_td").text()*1)+($("#totDlcCst_td").text()*1)+($("#totInspCst_td").text()*1);
		var tot_costPcs=tot_cost/packQty;
		
		//alert(tot_cost+'='+tot_costPcs);
		
		$("#totCost_td").text( number_format(tot_cost,4,'.','') );
		$("#totFOBCost_td").text( number_format(tot_costPcs,4,'.','') );
		var tot_fob_cost=(tot_cost*($("#txt_noOfPack").val()*1))+($("#txtmarign").val()*1);
		$("#totalFob_td").text( number_format((tot_fob_cost),4,'.','') );
		
		
		fnc_itemwise_data_cache();
	}
	
	function fnc_meeting_remarks_pop_up(costSheetId,styleRef)
	{
		var title = 'Meeting Remarks Form';	
		var page_link = 'requires/short_quotation_v6_controller.php?action=meeting_remarks_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&costSheetId='+costSheetId+'&styleRef='+styleRef, title, 'width=1050px,height=420px,center=1,resize=0,scrolling=0','../')
		release_freezing();
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var meeting_data=this.contentDoc.getElementById("hide_meeting_data").value;	 //Access form field with id="emailfield"
			//alert(meeting_data);  
			
			var ex_data=meeting_data.split("__");
			$('#txt_meeting_no').val(ex_data[0]);
			$('#cbo_buyer_agent').val(ex_data[1]);
			$('#cbo_agent_location').val(ex_data[2]);
			$('#txt_meeting_date').val(ex_data[3]);
			$('#txt_meeting_time').val(ex_data[4]);
			$('#txt_meeting_remarks').val(ex_data[5]);
		}
	}
	
	function fnc_rate_write_popup(inc_id,type)
	{
		if(inc_id=='meeting')
		{
			if(document.getElementById('chk_is_new_meeting').checked==true)
			{
				document.getElementById('chk_is_new_meeting').value=1;
				//$('#txt_meeting_date').removeAttr('disabled','disabled');
				//$('#txt_meeting_time').removeAttr('disabled','disabled');
				$('#txt_meeting_remarks').val('1. ');
			}
			else if(document.getElementById('chk_is_new_meeting').checked==false)
			{
				document.getElementById('chk_is_new_meeting').value=2;
				//$('#txt_meeting_date').attr('disabled','disabled');
				//$('#txt_meeting_time').attr('disabled','disabled');
				//$('#txt_meeting_remarks').attr('disabled','disabled');
			}
			var chk_meeting_val=$('#chk_is_new_meeting').val();
			
			fnc_meeting_no(chk_meeting_val);
		}
		else if(inc_id=='cm')
		{
			var gmtsItem_id=$('#txt_temp_id').val();
			//alert(gmtsItem_id);
			var gmtId=gmtsItem_id.split(",");
			if(document.getElementById('cmPop').checked==true)
			{
				document.getElementById('cmPop').value=1;
				var k=0;
				for(var y=1; y<=gmtId.length; y++)
				{
					$('#txt_cpm_'+trim(gmtId[k])).removeAttr('disabled','disabled');
					$('#txt_smv_'+trim(gmtId[k])).removeAttr('disabled','disabled');
					$('#txt_eff_'+trim(gmtId[k])).removeAttr('disabled','disabled');
					
					$('#txtCmCost_'+trim(gmtId[k])).val('');
					$('#txtCmCost_'+trim(gmtId[k])).attr("placeholder", "Cal.");
					$('#txtCmCost_'+trim(gmtId[k])).attr("readonly", "readonly");
					k++;
				}
				
				//=((SMV*CPM)*Costing per + (SMV*CPM*Costing per)* Efficiency Wastage%)/Exchange Rate
			}
			else if(document.getElementById('cmPop').checked==false)
			{
				document.getElementById('cmPop').value=2;
				var j=0;
				for(var u=1; u<=gmtId.length; u++)
				{
					$('#txt_cpm_'+trim(gmtId[j])).attr('disabled','disabled');
					$('#txt_smv_'+trim(gmtId[j])).attr('disabled','disabled');
					$('#txt_eff_'+trim(gmtId[j])).attr('disabled','disabled');
					/*$('#txt_cpm_'+trim(gmtId[j])).val('');
					$('#txt_smv_'+trim(gmtId[j])).val('');
					$('#txt_eff_'+trim(gmtId[j])).val('');*/
					
					$('#txtCmCost_'+trim(gmtId[j])).val('');
					$('#txtCmCost_'+trim(gmtId[j])).attr("placeholder", "Write");
					$('#txtCmCost_'+trim(gmtId[j])).removeAttr("readonly", "readonly");
					j++;
				}
			}
		}
		else
		{
			if(type==1)
			{
				if(document.getElementById('ratePop_'+inc_id).checked==true)
				{
					var consumtion=$('#txt_consumtion'+inc_id).val();
					document.getElementById('ratePop_'+inc_id).value=1;
					
					//$('#txt_consumtion'+inc_id).val('');
					//$('#txt_exper'+inc_id).val('');
					//$('#txt_totConsumtion'+inc_id).val('');
					$('#txt_rate'+inc_id).val('');
					$('#txtRateData_'+inc_id).val('');
					//$('#txt_consumtion'+inc_id).attr('readonly','readonly');
					//$('#txt_exper'+inc_id).attr('readonly','readonly');
					$('#txt_rate'+inc_id).attr('readonly','readonly');
					$('#txtRateData_'+inc_id).attr('readonly','readonly');
					//$('#txt_consumtion'+inc_id).attr("placeholder","Display");
					//$('#txt_exper'+inc_id).attr("placeholder","Display");
					$('#txt_rate'+inc_id).attr("placeholder", "Browse");
					$('#txt_rate'+inc_id).removeAttr("onDblClick").attr("onDblClick","fnc_openmypage_rate("+inc_id+",'1');");
					$('#txt_value'+inc_id).val('');
				}
				else if(document.getElementById('ratePop_'+inc_id).checked==false)
				{
					document.getElementById('ratePop_'+inc_id).value=2;
					
					//$('#txt_consumtion'+inc_id).val('');
					//$('#txt_exper'+inc_id).val('');
					//$('#txt_totConsumtion'+inc_id).val('');
					$('#txt_rate'+inc_id).val('');
					$('#txtRateData_'+inc_id).val('');
					//$('#txt_consumtion'+inc_id).removeAttr('readonly','readonly');
					//$('#txt_exper'+inc_id).removeAttr('readonly','readonly');
					$('#txt_rate'+inc_id).removeAttr('readonly','readonly');
					//$('#txt_consumtion'+inc_id).attr("placeholder","Write");
					//$('#txt_exper'+inc_id).attr("placeholder","Write");
					$('#txt_rate'+inc_id).attr("placeholder", "Write");
					$('#txt_rate'+inc_id).removeAttr("onDblClick");
					$('#txt_value'+inc_id).val('');
				}
			}
			else if(type==2)
			{
				if(document.getElementById('accRatePop_'+inc_id).checked==true)
				{
					document.getElementById('accRatePop_'+inc_id).value=1;
					
					$('#txtAccConsumtion_'+inc_id).val('');
					$('#txtAcexper_'+inc_id).val('');
					$('#txtTotAccConsumtion_'+inc_id).val('');
					$('#txtAccRate_'+inc_id).val('');
					$('#txtAccRateData_'+inc_id).val('');
					$('#txtAccValue_'+inc_id).val('');
					$('#txtAccConsumtion_'+inc_id).attr('readonly','readonly');
					$('#txtAcexper_'+inc_id).attr('readonly','readonly');
					$('#txtAccRate_'+inc_id).attr('readonly','readonly');
					$('#txtRateData_'+inc_id).attr('readonly','readonly');
					$('#txtAccConsumtion_'+inc_id).attr("placeholder","Display");
					$('#txtAcexper_'+inc_id).attr("placeholder","Display");
					$('#txtAccRate_'+inc_id).attr("placeholder", "Browse");
					$('#txtAccRate_'+inc_id).removeAttr("onDblClick").attr("onDblClick","fnc_openmypage_rate("+inc_id+",'2');");
				}
				else if(document.getElementById('accRatePop_'+inc_id).checked==false)
				{
					document.getElementById('accRatePop_'+inc_id).value=2;
					
					$('#txtAccConsumtion_'+inc_id).val('');
					$('#txtAcexper_'+inc_id).val('');
					$('#txtTotAccConsumtion_'+inc_id).val('');
					$('#txtAccRate_'+inc_id).val('');
					$('#txtAccRateData_'+inc_id).val('');
					$('#txtAccValue_'+inc_id).val('');
					$('#txtAccConsumtion_'+inc_id).removeAttr('readonly','readonly');
					$('#txtAcexper_'+inc_id).removeAttr('readonly','readonly');
					$('#txtAccRate_'+inc_id).removeAttr('readonly','readonly');
					$('#txtAccConsumtion_'+inc_id).attr("placeholder","Write");
					$('#txtAcexper_'+inc_id).attr("placeholder","Write");
					$('#txtAccRate_'+inc_id).attr("placeholder", "Write");
					$('#txtAccRate_'+inc_id).removeAttr("onDblClick");
				}
			}
		}
	}
	
	function fnc_openmypage_rate(inc_id,popuptype)
	{
		var qc_no=$('#hid_qc_no').val();
		var bodypartid=$('#txtbodypartid_'+inc_id).val();
		var consRateId=$('#txtfabupid_'+inc_id).val();
		if (qc_no=="")
		{
			alert("Please Save First.");
			return;
		}
		
		if (consRateId=="" || consRateId==0)
		{
			alert("Please Save First.");
			return;
		}
		
		if (bodypartid=="" || bodypartid==0)
		{
			alert("Please Select Body Part.");
			return;
		}
		if(popuptype==1)
		{
			var txt_fabDesc=$('#txt_fabDesc'+inc_id).val();
			var cbocolortype=$('#cbocolortype_'+inc_id).val();
			var txtgsmweight=$('#txtgsmweight_'+inc_id).val();
			var txtfabwidth=$('#txtfabwidth_'+inc_id).val();
			var packQty=$('#tdpackQty').text();
			var itemRatio=$('#tditemRatio').text();
			var rateData=$('#txtRateData_'+inc_id).val();
			var fabuom=$('#cbofabuom_'+inc_id).val();
			var fabid=$("#txt_fabid_"+inc_id).val()*1;
			var totConsumtion=$("#txt_totConsumtion"+inc_id).val()*1;
			var process_loss_method_id=$("#txt_fab_process_loss_method").val()*1;
			
			var title = 'Fab. Rate Details PopUp';	
			var page_link = 'requires/short_quotation_v6_controller.php?action=rate_details_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&qc_no='+qc_no+'&bodypartid='+bodypartid+'&consRateId='+consRateId+'&txt_fabDesc='+txt_fabDesc+'&cbocolortype='+cbocolortype+'&txtgsmweight='+txtgsmweight+'&txtfabwidth='+txtfabwidth+'&totConsumtion='+totConsumtion+'&itemRatio='+itemRatio+'&fabuom='+fabuom+'&process_loss_method_id='+process_loss_method_id+'&fabid='+fabid+'&rateData='+rateData, title, 'width=850px,height=380px,center=1,resize=0,scrolling=0','../')
			release_freezing();
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var totamount=this.contentDoc.getElementById("txttotamount").value;
				
				var avgRate=(totamount*1)/(totConsumtion*1)
				//alert(totConsumtion+'__'+avgRate+'__'+totamount)
				
				$('#txt_rate'+inc_id).val( number_format(avgRate,4,'.','') );
				$('#txt_value'+inc_id).val( totamount );
				fnc_amount_calculation('fabric', inc_id, '');
				
				fnc_qcosting_entry(1);
			}
		}
		else if (popuptype==2)
		{
			var acctext=$('#txtAcctext_'+inc_id).val();
			var accuom=$('#cboconsuom_'+inc_id).val();
			var accRateData=$('#txtAccRateData_'+inc_id).val();
			var itemRatio=$('#tditemRatio').text();
			var process_loss_method_id=$("#txt_acc_process_loss_method").val()*1;
			
			var title = 'Acc. Rate Details PopUp';	
			var page_link = 'requires/short_quotation_v6_controller.php?action=accrate_details_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&qc_no='+qc_no+'&acctext='+acctext+'&accuom='+accuom+'&accRateData='+accRateData+'&itemRatio='+itemRatio+'&process_loss_method_id='+process_loss_method_id, title, 'width=650px,height=380px,center=1,resize=0,scrolling=0','../')
			release_freezing();
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				//var totconsdzn=this.contentDoc.getElementById("txttotcons_dzn").value;
				var totcons=this.contentDoc.getElementById("txttotcons_pack").value;
				var totconsexper=this.contentDoc.getElementById("txttotexper").value;
				var tottotcons=this.contentDoc.getElementById("txttotcons").value;
				var avgRate=this.contentDoc.getElementById("txt_avg_rate").value;	 //Access form field with id="emailfield"
				var totamount=this.contentDoc.getElementById("txt_amt_pack").value;
				var txtdatastr=this.contentDoc.getElementById("txtdatastr").value;
				//alert(totcons+'__'+avgRate+'__'+totamount)
				$('#txtAccConsumtion_'+inc_id).val( totcons );
				$('#txtAcexper_'+inc_id).val( totconsexper );
				$('#txtTotAccConsumtion_'+inc_id).val( tottotcons );
				$('#txtAccRate_'+inc_id).val( avgRate );
				$('#txtAccValue_'+inc_id).val( totamount );
				$('#txtAccRateData_'+inc_id).val( txtdatastr );
				fnc_amount_calculation('accessories', inc_id, '');
			}
		}
	}
	
	function fnc_new_stage_popup()
	{
		var title = 'Stage Entry/Update PopUp';	
		var page_link = 'requires/short_quotation_v6_controller.php?action=stage_saveUpdate_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=600px,height=380px,center=1,resize=0,scrolling=0','../')
		release_freezing();
		emailwindow.onclose=function()
		{
			load_drop_down( 'requires/short_quotation_v6_controller', '', 'load_drop_stage_name', 'stage_td');
		}
	}
	
	function fnc_consumption_write_disable(val)
	{
		//alert(val)
		var ex_id=val.split("__");
		if(ex_id[0]==1 || ex_id[0]==3)//manual
		{
			for(var i=1; i<=5; i++)
			{
				var rate=0;
				rate=$('#txt_rate'+i).val()*1;
				if(ex_id[1]==1)
					$('#txt_consumtion'+i).val('');
					
				$('#txt_consumtion'+i).removeAttr('disabled','disabled');
				$('#txt_consumtion'+i).attr("placeholder", "Write");
				$('#txt_consumtion'+i).removeAttr("onChange").attr("onChange","fnc_amount_calculation('fabric','"+i+"','"+rate+"');");
			}
		}
		else
		{
			for(var i=1; i<=5; i++)
			{
				if(ex_id[1]==1)
					$('#txt_consumtion'+i).val('');
				$('#txt_consumtion'+i).attr('disabled','disabled');
				$('#txt_consumtion'+i).attr("placeholder", "Display");
				$('#txt_consumtion'+i).removeAttr("onChange");
			}
		}
	}
	
	function reset_fnc()
	{
		location.reload(); 
	}
	
	function fnc_itemwise_data_cache()
	{
		var item_id=document.getElementById('cboItemId').value;
		var fabData=''; var f=9;
		for(var m=1; m<=f; m++)
		{
			fabData+=($('#txtbodyparttext_'+m).val())+'~!~'+($('#txtbodypartid_'+m).val()*1)+'~!~'+($('#txt_fabDesc'+m).val())+'~!~'+($('#txt_fabid_'+m).val())+'~!~'+($('#cbocolortype_'+m).val())+'~!~'+($('#txtgsmweight_'+m).val()*1)+'~!~'+($('#txtfabwidth_'+m).val()*1)+'~!~'+($('#txtusefor_'+m).val())+'~!~'+($('#txt_consumtion'+m).val()*1)+'~!~'+($('#txt_exper'+m).val()*1)+'~!~'+($('#txt_totConsumtion'+m).val()*1)+'~!~'+($('#ratePop_'+m).val()*1)+'~!~'+($('#txtRateData_'+m).val())+'~!~'+($('#txt_rate'+m).val()*1)+'~!~'+($('#txt_value'+m).val()*1)+'~!~'+($('#txtfabupid_'+m).val())+'~!~'+($('#cbofabuom_'+m).val())+'~!~'+($('#txtYarnDtls_'+m).val())+'#^';
		}
		
		var sp_row=4; var specialData='';
		for(var n=1; n<=sp_row; n++)
		{
			specialData+= $('#cboSpeciaOperationId_'+n).val()+'_'+($('#cboSpeciaTypeId_'+n).val()*1)+'_'+($('#txtspbodyparttext_'+n).val())+'_'+($('#txtspbodypartid_'+n).val()*1)+'_'+($('#txt_spConsumtion'+n).val()*1)+'_'+($('#txt_spexper'+n).val()*1)+'_'+($('#txt_totSpConsumtion'+n).val()*1)+'_'+($('#txtSpRate_'+n).val()*1)+'_'+($('#txt_spValue'+n).val()*1)+'_'+($('#txtSpRemarks_'+n).val())+'##';
		}
		
		var wash_row=$('#tbl_wash tbody tr').length; var washData='';
		for(var o=1; o<=wash_row; o++)
		{
			washData+= $('#txtWashTypetext_'+o).val()+'_'+($('#txtWashTypeId_'+o).val()*1)+'_'+($('#txtWbodyparttext_'+o).val())+'_'+($('#txtWbodypartid_'+o).val()*1)+'_'+($('#txtWConsumtion_'+o).val()*1)+'_'+($('#txtWexper_'+o).val()*1)+'_'+($('#txtTotWConsumtion_'+o).val()*1)+'_'+($('#txtwRate_'+o).val()*1)+'_'+($('#txtWValue_'+o).val()*1)+'_'+($('#txtWashRemarks_'+o).val())+'##';
		}
		// alert( item_id_tmp+'=='+item_id );
		var acc=$('#tbl_acc tbody tr').length; var accessoriesData="";
		for(var q=1; q<=acc; q++)
		{
			accessoriesData+= ($('#txtAcctext_'+q).val())+'~!~'+($('#txtAccId_'+q).val()*1)+'~!~'+($('#txtAccDescription_'+q).val())+'~!~'+($('#txtAccBandRef_'+q).val())+'~!~'+($('#txtAccConsumtion_'+q).val()*1)+'~!~'+($('#txtAcexper_'+q).val()*1)+'~!~'+($('#txtTotAccConsumtion_'+q).val()*1)+'~!~'+($('#txtAccRate_'+q).val()*1)+'~!~'+($('#txtAccValue_'+q).val()*1)+'~!~'+($('#txtAccUseFor_'+q).val())+'~!~'+($('#cboconsuom_'+q).val())+'~!~'+($('#accRatePop_'+q).val()*1)+'~!~'+$('#txtAccRateData_'+q).val()+'~!~'+($('#hiddencalparameter_'+q).val()*1)+'~!~'+$('#txtAccConsCalData_'+q).val()+'#^';
		}
		//all_data_string=fabData+****+specialData+****+washData+****+accessoriesData;
		//alert(cons_rate_data);
		
		$('#txtfabricData_'+item_id).val( fabData ); 
		$('#txtspData_'+item_id).val( specialData );
		$('#txtwashData_'+item_id).val( washData );
		$('#txtaccData_'+item_id).val( accessoriesData );
		prev_item_id=item_id;
	}
	
	function fnc_specialAcc_reset()
	{
		var sp_row=5;
		for(var n=1; n<=sp_row; n++)
		{
			$('#txt_spConsumtion'+n).val('');
			$('#txt_spexper'+n).val('');
			$('#txtSpRate_'+n).val('');
			$('#txt_spValue'+n).val('');
		}
		
		var ac_row=15;
		for(var q=1; q<=ac_row; q++)
		{
			$('#txtAccConsumtion_'+q).val('');
			$('#txtAcexper_'+q).val('');
			$('#txtAccRate_'+q).val('');
		}
	}
	
	function fnc_select()
	{
		$(document).ready(function() {
			$("input:text").focus(function() { $(this).select(); } );
		});
	}
	
	function fnc_qcosting_entry( operation )
	{
		freeze_window(operation);
		//release_freezing();	
		//alert(operation); return;
		if(mandatory_field !=''){
            if (form_validation(mandatory_field,field_message)==false){
                release_freezing();
                return;
            }
        }
		var type=0;
		fnc_itemwise_data_cache();
		if( operation==6)
		{
			type=6; operation=0;
		}
		else if( operation==7)
		{
			type=7; operation=0;
		}
		else
		{
			type=1; operation=operation;
		}
		
		
		/*if( ($('#totalFob_td').text()*1)==0 )
		{
			alert("Please fill up F.O.B $.");
			release_freezing();	
			return;
		}*/
		
		if(operation==2)
		{
			var rr=confirm("You are going to delete Cost Sheet No.\n Are you sure?");
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
		
		if (form_validation('cbo_company_id*cbo_temp_id*txt_exchangeRate*cbo_buyer_id*cbo_season_id*txt_styleRef*txt_costingDate*txt_tgtPrice*cbo_costing_per','Company*Template Name*Exchange Rate*Buyer Name*Season*Style Ref.*Costing Date*TGT Price*Costing Per')==false)
		{
			release_freezing();
			return;
		}	
		else
		{
			if(operation==1 || operation==2)
			{
				for (var i = 1; i<20; i++)
				{
					const fabuom = document.querySelector('#cbofabuom_'+i);
					if(fabuom)
					{
						var fcon=document.getElementById('txt_totConsumtion'+i).value*1;
						if(fcon>0 && fabuom_mandatory!='')
						{
							if (form_validation('cbofabuom_'+i,'Fabric Uom')==false)
							{
								release_freezing();
								return;
							}
						}
					}
				}
			}
			if(operation==4)
			{
				var report_title=$( "div.form_caption" ).html();
				generate_report_file( $('#hid_qc_no').val()+'*'+$('#txt_costSheetNo').val()+'*'+report_title,'quick_costing_print','requires/short_quotation_v6_controller');
				release_freezing();
				return;
			}
			else
			{
				var meeting_txt=$('#txt_meeting_remarks').val();
				if(trim(meeting_txt)!="" && trim(meeting_txt)!="1.")
				{
					if($('#txt_meeting_date').val()=="" && $('#txt_meeting_time').val()=="")
					{
						alert("Please fill up meeting date and time.");
						release_freezing();
						return;
					}
					
					if( $('#cbo_buyer_agent').val()==0 && $('#cbo_agent_location').val()==0)
					{
						alert("Please fill up meeting Buyer agent and location.");
						release_freezing();
						return;
					}
				}
				
				var temp_id=$('#txt_temp_id').val();
				var split_tmep_id=temp_id.split(',');
				
				var ae=0; var consData=''; var item_wise_tot_data="";
				for(i=1; i<=split_tmep_id.length; i++)
				{
					var itm_id=trim(split_tmep_id[ae]);
					consData+=get_submitted_data_string('txtfabricData_'+itm_id+'*txtspData_'+itm_id+'*txtwashData_'+itm_id+'*txtaccData_'+itm_id,"../../",2);//
					
					item_wise_tot_data+=($('#fab_td'+itm_id).text()*1)+'_'+($('#spOpe_td'+itm_id).text()*1)+'_'+($('#wash_td'+itm_id).text()*1)+'_'+($('#acc_td'+itm_id).text()*1)+'_'+($('#txt_cpm_'+itm_id).val()*1)+'_'+($('#txt_smv_'+itm_id).val()*1)+'_'+($('#txt_eff_'+itm_id).val()*1)+'_'+($('#txtCmCost_'+itm_id).val()*1)+'_'+($('#txtFriCost_'+itm_id).val()*1)+'_'+($('#txtLtstCost_'+itm_id).val()*1)+'_'+($('#txtMissCost_'+itm_id).val()*1)+'_'+($('#txtOtherCost_'+itm_id).val()*1)+'_'+($('#txtCommCost_'+itm_id).val()*1)+'_'+($('#fobT_td'+itm_id).text()*1)+'_'+($('#fobPcsT_td'+itm_id).text()*1)+'_'+($('#txtRmgQty_'+itm_id).val()*1)+'_'+($('#txtCommlCost_'+itm_id).val()*1)+'_'+($('#txtOptExp_'+itm_id).val()*1)+'_'+($('#txtCourierCost_'+itm_id).val()*1)+'_'+($('#txtdlcCost_'+itm_id).val()*1)+'_'+($('#txtInspCost_'+itm_id).val()*1)+'_'+itm_id+'__';
					
					ae++;
				}
				//alert(consData);   return;
				//alert(item_wise_tot_data);
				var data_tot_cost_summ=($('#cbo_buyer_agent').val()*1)+'_'+($('#cbo_agent_location').val()*1)+'_'+($('#txt_noOfPack').val()*1)+'_'+($('#cmPop').val()*1)+'_'+($('#txt_lumSum_cost').val()*1)+'_'+($('#txt_commPer').val()*1)+'_'+($('#totFab_td').text()*1)+'_'+($('#totSpc_td').text()*1)+'_'+($('#totWash_td').text()*1)+'_'+($('#totAcc_td').text()*1)+'_'+($('#totCm_td').text()*1)+'_'+($('#totFriCst_td').text()*1)+'_'+($('#totLbTstCst_td').text()*1)+'_'+($('#totMissCst_td').text()*1)+'_'+($('#totOtherCst_td').text()*1)+'_'+($('#totCommCst_td').text()*1)+'_'+($('#totCost_td').text()*1)+'_'+($('#totalFob_td').text()*1)+'_'+($('#totRmgQty_td').text()*1)+'_'+($('#totCommlCst_td').text()*1)+'_'+($('#txt_commlPer').val()*1)+'_'+($('#totOptExp_td').text()*1)+'_'+($('#totCourierCst_td').text()*1)+'_'+($('#txt_lcPer').val()*1)+'_'+($('#totDlcCst_td').text()*1)+'_'+($('#totInspCst_td').text()*1);
				
				//var data_mst="action=save_update_delete&operation="+operation+"&data_tot_cost_summ="+data_tot_cost_summ+"&item_wise_tot_data="+item_wise_tot_data+"&type="+type+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_temp_id*txt_temp_id*txt_inquery_id*txt_styleRef*txt_costSheetNo*txt_update_id*cbo_buyer_id*cbo_season_id*cbo_season_year*cbo_brand*txt_styleDesc*cbo_subDept_id*txt_delivery_date*txt_exchangeRate*txt_offerQty*txt_quotedPrice*txt_tgtPrice*cbo_stage_id*txt_costingDate*txt_costing_remarks*cbo_revise_no*cbo_option_id*txt_option_remarks*txt_meeting_date*txt_meeting_time*chk_is_new_meeting*txt_meeting_remarks*txt_meeting_no*txtmarign*cbo_product_department*txt_product_code*txt_article*hid_qc_no',"../../");
				var data_mst="action=save_update_delete&operation="+operation+"&data_tot_cost_summ="+data_tot_cost_summ+"&item_wise_tot_data="+item_wise_tot_data+"&type="+type+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_temp_id*txt_temp_id*txt_inquery_id*txt_styleRef*txt_costSheetNo*txt_update_id*cbo_buyer_id*cbo_season_id*cbo_season_year*cbo_brand*txt_styleDesc*cbo_subDept_id*txt_delivery_date*txt_exchangeRate*txt_offerQty*txt_quotedPrice*txt_tgtPrice*cbo_stage_id*txt_costingDate*txt_costing_remarks*cbo_revise_no*cbo_option_id*txt_option_remarks*txt_meeting_date*txt_meeting_time*chk_is_new_meeting*txt_meeting_remarks*txt_meeting_no*txtmarign*cbo_product_department*txt_product_code*txt_article*txt_fab_process_loss_method*txt_acc_process_loss_method*cbo_costing_per*cbo_style_owner*hid_qc_no',"../../");
				
				var data=data_mst+consData;
				//alert(data); return;
				
				http.open("POST","requires/short_quotation_v6_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_qcosting_entry_response;
			}
		}
	}
	
	function fnc_qcosting_entry_response()
	{
		if(http.readyState == 4) 
		{
			var reponse=http.responseText.split('**');
			
			if (reponse[0]=="approvedQc")
			{
				alert("This Option (QC) is Approved.");
				release_freezing();
				return;
			}
			
			if (reponse[0]==11)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			
			if(reponse[0]==15) 
			{ 
				 setTimeout('fnc_qcosting_entry('+ reponse[1]+')',8000); 
			}
			else if (reponse[0]==1 || reponse[0]==0)
			{
				$('#txt_update_id').val(reponse[1]);
				$('#hid_qc_no').val(reponse[7]);
				$('#txt_costSheetNo').val(reponse[2]);
				//if(reponse[6]!='') $('#txt_meeting_no').val(reponse[6]);
				//alert(reponse[1])
				set_button_status(1, permission, 'fnc_qcosting_entry',1);
				if (reponse[0]==0) alert("Data is Save Successfully");
				else if (reponse[0]==1) alert("Data is Update Successfully");
			//}
			//return;
			//if (reponse[0]==0 || reponse[0]==1)
			//{
				var user_id='<? echo $_SESSION['logic_erp']['user_id']; ?>';
				if(reponse[5]==1)//Insert
				{
					var temp_style_list=return_ajax_request_value(reponse[2]+'__'+1, 'temp_style_list_view', 'requires/short_quotation_v6_controller');
					$('#style_td').html( temp_style_list );
					var user_id='<? echo $_SESSION['logic_erp']['user_id']; ?>';
					//get_php_form_data(reponse[1]+"__"+user_id+"__"+reponse[2]+"__"+0+"***"+0, 'populate_style_details_data', 'requires/short_quotation_v6_controller');
					load_drop_down('requires/short_quotation_v6_controller', reponse[2]+'__'+reponse[4]+'__'+reponse[3], 'load_drop_down_revise_no', 'revise_td');
					load_drop_down('requires/short_quotation_v6_controller', reponse[2]+'__'+reponse[4]+'__'+reponse[4], 'load_drop_down_option_id', 'option_td');
					set_onclick_style_list(reponse[2]+'__'+user_id+'__'+reponse[2]);
				}
				if(reponse[5]==6) //Revise
				{
					load_drop_down('requires/short_quotation_v6_controller', reponse[2]+'__'+reponse[4]+'__0', 'load_drop_down_revise_no', 'revise_td');
					load_drop_down('requires/short_quotation_v6_controller', reponse[2]+'__'+reponse[4]+'__'+reponse[4], 'load_drop_down_option_id', 'option_td');
					set_onclick_style_list(reponse[2]+'__'+user_id+'__'+reponse[2]);
				}
				else if(  reponse[5]==7) //Option
				{	
					load_drop_down('requires/short_quotation_v6_controller', reponse[2]+'__'+reponse[4]+'__'+$('#cbo_revise_no').val(), 'load_drop_down_revise_no', 'revise_td');
					load_drop_down('requires/short_quotation_v6_controller', reponse[2]+'__'+reponse[4]+'__0', 'load_drop_down_option_id', 'option_td');
					
					//load_drop_down('requires/short_quotation_v6_controller', reponse[2]+'__'+reponse[4]+'__'+$('#cbo_revise_no').val(), 'load_drop_down_revise_no', 'revise_td');
					//load_drop_down('requires/short_quotation_v6_controller', reponse[2]+'__'+reponse[4]+'__0', 'load_drop_down_option_id', 'option_td');
					set_onclick_style_list(reponse[2]+'__'+user_id+'__'+reponse[2]);
				}
			}
			else if (reponse[0]==2)
			{
				reset_fnc();
				release_freezing();
				return;
			}
			
			var temp_style_list=return_ajax_request_value(reponse[2]+'__'+0, 'temp_style_list_view', 'requires/short_quotation_v6_controller');
			$('#style_td').html( temp_style_list );
			//alert(reponse[7]);
			//change_color_tr( reponse[7], $('#tr_'+trim(reponse[7])).attr('bgcolor') );
			
			if (reponse[0]==6) alert(reponse[1]);
			if (reponse[0]==11) alert(reponse[1]);
			//alert(show_msg(trim(reponse[0])));
			release_freezing();
		}
	}
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/short_quotation_v6_controller.php?data=" + data+'&action='+action, true );
	}
	
	function openmypage_style(type)
	{
		var page_link='requires/short_quotation_v6_controller.php?action=style_popup';
		var title="Style Search Popup";
		var data=$('#cbo_buyer_id').val()+'__'+type;
		var user_id='<? echo $_SESSION['logic_erp']['user_id']; ?>';
		var k=1;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&data='+data, title, 'width=1200px,height=450px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_id=this.contentDoc.getElementById("hide_style_id").value; // product ID
			var cost_sheet_no=this.contentDoc.getElementById("hide_cost_no").value;
			//alert(style_id);
			if(style_id!="")
			{
				var temp_style_list=return_ajax_request_value(cost_sheet_no+'__'+1, 'temp_style_list_view', 'requires/short_quotation_v6_controller');
				$('#style_td').html( temp_style_list );
				
				var str=style_id.split(",");
				var strcst=cost_sheet_no.split(",");
				for( var i=0; i< str.length; i++ ) {
					if( k==1)
					{
						set_onclick_style_list(str[0]+'__'+user_id+'__'+strcst[0]+'__'+0);
						var qc_no=$('#hid_qc_no').val()*1;
						change_color_tr( qc_no , $('#tr_'+qc_no).attr('bgcolor') );
						load_drop_down('requires/short_quotation_v6_controller', cost_sheet_no+'__0__0', 'load_drop_down_option_id', 'option_td');
						load_drop_down('requires/short_quotation_v6_controller', cost_sheet_no+'__'+$('#cbo_option_id').val()+'__0', 'load_drop_down_revise_no', 'revise_td');
						k++;
					}
				}
			}
			//alert(temp_style_list)
			//$('#style_td').html( $('#style_td').html() +""+ return_ajax_request_value(style_id, 'temp_style_list_view', 'requires/short_quotation_v6_controller') );
			//show_list_view( style_id,'temp_style_list_view','style_td','requires/short_quotation_v6_controller','',1);//setFilterGrid(\'tbl_po_list\',-1)
			//localStorage.setItem( "temp_style_list_view", $('#style_td').html() );
		}
	}
	
	function set_onclick_style_list( data )
	{
		var datas=data.split("__");
		 
		//$('#cbo_revise_no').val(0);
		//$('#cbo_option_id').val(0);
		//alert(datas[3]);
		
		if(datas[3]=="25")
		{
			var cost_sheet_no=$('#txt_costSheetNo').val()*1;
			var styleRef=$('#txt_styleRef').val();
			
			var recent_fob=$('#totalFob_td').text()*1;
			var recent_costing_remarks=$('#txt_costing_remarks').val();
			var recent_opt_remarks=$('#txt_option_remarks').val();
			var recent_meeting_remark=$('#txt_meeting_remarks').val();
			
			var pre_fob=$('#totalFob_td').attr('prev_fob')*1;
			var pre_costing_remarks=$('#txt_costing_remarks').attr('pre_costing_remarks');
			var pre_opt_remarks=$('#txt_option_remarks').attr('pre_opt_remarks');
			var pre_meeting_remark=$('#txt_meeting_remarks').attr('pre_meeting_remark');
			
			if(styleRef!="")
			{
				if(cost_sheet_no!=0)
				{
					if( pre_fob!=recent_fob || pre_costing_remarks!=recent_costing_remarks || pre_opt_remarks!=recent_opt_remarks || pre_meeting_remark!=recent_meeting_remark )
					{
						//var r=confirm("You are Going to Generate Style Data Without Update.\n Please, Press OK to Generate.\n Otherwise Press Cencel.");
						
						//var r=confirm("Switch to another costing need update, Press OK. \n Otherwise Press Cencel then switch. \n Do You Want To Update?");
						var r=confirm("Do You Want To Update?");
						//alert(r); return;
						if(r==true)
						{
							fnc_qcosting_entry(1);
						}
						else
						{
							//release_freezing();	
							//return;
						}
					}
				}
				else if(cost_sheet_no==0)
				{
					//var r=confirm("You are Going to Generate Style Data Without Save.\n Please, Press OK to Generate.\n Otherwise Press Cencel.");
					//var r=confirm("Switch to another costing need Save, Press OK. \n Otherwise Press Cencel then switch. \n Do You Want To Update?");
					var r=confirm("Do You Want To Update?");
					//alert(r); return;
					if(r==true)
					{
						fnc_qcosting_entry(1);
					}
					else
					{
						//release_freezing();	
						//return;
					}
				}
			}
			load_drop_down('requires/short_quotation_v6_controller', datas[2]+'__0__0', 'load_drop_down_option_id', 'option_td');
			load_drop_down('requires/short_quotation_v6_controller', datas[2]+'__'+$('#cbo_option_id').val()+'__0', 'load_drop_down_revise_no', 'revise_td');
			
			var val=document.getElementById('cbo_revise_no').value+'***'+document.getElementById('cbo_option_id').value+'***'+document.getElementById('chk_is_new_meeting').value;
			get_php_form_data(datas[0]+"__"+datas[1]+"__"+datas[2]+"__"+val, 'populate_style_details_data', 'requires/short_quotation_v6_controller');
			$('#txt_seleted_row_id').val(datas[0]);
			$('#hid_selected_cost_no').val(datas[2]);
			//$('#chk_is_new_meeting').val(2);
			//document.getElementById('chk_is_new_meeting').checked=false;
			//fnc_meeting_no(2);
		}
		else
		{
			var val=document.getElementById('cbo_revise_no').value+'***'+document.getElementById('cbo_option_id').value+'***'+document.getElementById('chk_is_new_meeting').value;
			get_php_form_data(datas[0]+"__"+datas[1]+"__"+datas[2]+"__"+val, 'populate_style_details_data', 'requires/short_quotation_v6_controller');
			$('#txt_seleted_row_id').val(datas[0]);
			$('#hid_selected_cost_no').val(datas[2]);
			//$('#chk_is_new_meeting').val(2);
			//document.getElementById('chk_is_new_meeting').checked=false;
			//fnc_meeting_no(2);
		}
		//$('#cbo_revise_no').val(0);
		//$('#cbo_option_id').val(0);
		
		//load_drop_down('requires/short_quotation_v6_controller', reponse[2]+'__'+reponse[4]+'__'+$('#cbo_revise_no').val(), 'load_drop_down_revise_no', 'revise_td');
		//load_drop_down('requires/short_quotation_v6_controller', reponse[2]+'__'+reponse[4]+'__0', 'load_drop_down_option_id', 'option_td');
	}
	
	function fnc_delete_style_row()
	{
		var style_id=$('#txt_seleted_row_id').val();
		var hid_qc_no=$('#hid_qc_no').val();
		var temp_style_list=return_ajax_request_value(hid_qc_no+'__'+3, 'temp_style_list_view', 'requires/short_quotation_v6_controller');
		$('#style_td').html( temp_style_list );
		reset_fnc();
		/*if( (td*1)>1 )
		{
			$('#tr_'+td).remove();
			//localStorage.setItem( "temp_style_list_view", $('#style_td').html() );
			
		}*/
	}
	
	function fnc_cost_id_write()
	{
		var user_id='<? echo $_SESSION['logic_erp']['user_id']; ?>';
		get_php_form_data(($("#hid_qc_no").val()*1)+'__'+user_id+'__'+0, 'populate_style_details_data', 'requires/short_quotation_v6_controller');
	}
	
	function fnc_copy_cost_sheet( operation )
	{
		//alert( $('#txt_update_id').val() );
		var data_copy="action=copy_cost_sheet&operation="+operation+get_submitted_data_string('txt_costSheetNo*txt_update_id*txt_styleRef*cbo_season_id*cbo_buyer_id*hid_qc_no',"../../");
		var data=data_copy;
		//alert(data);
		//return;
		freeze_window(operation);
		http.open("POST","requires/short_quotation_v6_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_qcosting_entry_response;
	}
	
	function fnc_check_zero_val(val)
	{
		var new_val=val*1;
		if(new_val<1)
		{
			alert("Less Then 1 (one) value not Allowed");
			$("#txt_noOfPack").val(1);
			return;
		}
	}
	
	function fnc_clear_all()
	{
		//localStorage.removeItem("temp_style_list_view", $('#style_td').html() ); 
		var r=confirm("Do you want clear style list?\n If ok List will clear.");
		if(r==false)
		{
			return;
		}
		else
		{
			var style_id='';
			var temp_style_list=return_ajax_request_value(style_id+'__'+2, 'temp_style_list_view', 'requires/short_quotation_v6_controller');
			$('#style_td').html('');
			reset_fnc();
		}
	}
	
	function fnc_confirm_style()
	{
		if($('#txt_update_id').val()!="")
		{
			var tgt_price=$('#txt_tgtPrice').val();
			var fobval=$('#totalFob_td').text();
			//alert(fobval+'=='+tgt_price)
			
			if( ((fobval*1)<(tgt_price*1)) || tgt_price=="")
			{
				alert("F.O.B $ is less then TGT Price. Please Check and Confirm");
				return;
			}
			var data=$('#hid_qc_no').val()+'__'+$('#txt_update_id').val();
			var page_link='requires/short_quotation_v6_controller.php?action=confirmStyle_popup';
			var title="Confirm Style Popup";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&data='+data, title, 'width=1150px,height=450px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				/*var theform=this.contentDoc.forms[0];
				var style_id=this.contentDoc.getElementById("hide_style_id").value; // product ID
				var temp_style_list=return_ajax_request_value(style_id+'__'+1, 'temp_style_list_view', 'requires/short_quotation_v6_controller');
				$('#style_td').html( temp_style_list );*/
				//alert(temp_style_list)
				//$('#style_td').html( $('#style_td').html() +""+ return_ajax_request_value(style_id, 'temp_style_list_view', 'requires/short_quotation_v6_controller') );
				//show_list_view( style_id,'temp_style_list_view','style_td','requires/short_quotation_v6_controller','',1);//setFilterGrid(\'tbl_po_list\',-1)
				//localStorage.setItem( "temp_style_list_view", $('#style_td').html() );
			}
		}
		else
		{
			alert("Please Cost Sheet Save First.");
			return;
		}
	}
	
	function fnc_option_rev( val )
	{
		var user_id='<? echo $_SESSION['logic_erp']['user_id']; ?>';
		var selected_id=$('#txt_seleted_row_id').val();
		var cost_no=$('#hid_selected_cost_no').val();
		var hid_qc_no=$('#hid_qc_no').val();
		//alert(user_id)
		
		get_php_form_data(hid_qc_no+"__"+user_id+"__"+cost_no+"__"+val+"__from_option", 'populate_style_details_data', 'requires/short_quotation_v6_controller');
	}
	
	var row_color=new Array();
	var lastid='';
	function change_color_tr( v_id, e_color )
	{
		//alert(v_id+'='+e_color)
		if(lastid!='') $('#tr_'+lastid).attr('bgcolor',row_color[lastid])
		
		if( row_color[v_id]==undefined ) row_color[v_id]=$('#tr_'+v_id).attr('bgcolor');
		
		if( $('#tr_'+v_id).attr('bgcolor')=='#FF9900')
			$('#tr_'+v_id).attr('bgcolor',row_color[v_id])
		else
			$('#tr_'+v_id).attr('bgcolor','#FF9900')
		
		lastid=v_id;
	}
	
	$(document).ready(function(){
	  navigate_arrow_key()
	});
	
	 new function ($) {
        $.fn.getCursorPosition = function () {
            var pos = 0;
            var el = $(this).get(0);
            // IE Support
            if (document.selection) {
                el.focus();
                var Sel = document.selection.createRange();
                var SelLength = document.selection.createRange().text.length;
                Sel.moveStart('character', -el.value.length);
                pos = Sel.text.length - SelLength;
            }
            // Firefox support
            else if (el.selectionStart || el.selectionStart == '0')
                pos = el.selectionStart;
            return pos;
        }
    } (jQuery);
   
	function navigate_arrow_key()
	{
		$('#item_tbl input').keyup(function(e){
			
			if( e.which==39 )
			{
				 if( $(this).getCursorPosition() == $(this).val().length ) 
				 	$(this).closest('td').next().find('.text_boxes,.text_boxes_numeric').focus();
			}
			else if( e.which==37 )
			{
				if( $(this).getCursorPosition() == 0 ) 
					$(this).closest('td').prev().find('.text_boxes,.text_boxes_numeric').focus();
			}
			else if( e.which==40 )
			{
				// alert( (($(this).closest('tr').index()*1)+1)%5 )
				 
				 /*if( $(this).closest('tr').index()!=0 && (($(this).closest('tr').index()*1)+1)%5==0)
				 
				 	$(this).closest('tr').next().find('td:eq('+ tind +')').find('.text_boxes,.text_boxes_numeric').focus();
				 return;
				  alert( $(this).closest('td').index() )
				  */
				//  && (($(this).closest('tr').next().index()*1)+1)%5!=0  alert( $(this).closest('tr').next().index() );
				var i=1;
				
				if( $(this).closest('tr').index()==0 ){
					var ind= $(this).closest('td').index()-1;
					//alert('k');
				}
				else if((($(this).closest('tr').index()*1)+1)%5==0  )
				{
					var ind= ($(this).closest('td').index()*1)+1;
					//alert('f');
					//alert( $(this).closest('tr').index())
					//i=1;
				}
				else{
					if((($(this).closest('tr').prev().index()*1)+1)%5==0  )
					var ind= ($(this).closest('td').index()*1)-1;
					else
					var ind= ($(this).closest('td').index()*1);
					//ind-i
					//i=0
				}
				
				 $(this).closest('tr').next().find('td:eq('+ind +')').find('.text_boxes,.text_boxes_numeric').focus();
				 return;
				/*if( (($(this).closest('tr').index()*1)+1)%5==0 ) 
					var tind= ($(this).closest('td').index()*1)+1; 
				else  
					var tind= ($(this).closest('td').index()*1);
				*/
				if( $(this).closest('tr').index()!=0 )
					$(this).closest('tr').next().find('td:eq('+ $(this).closest('td').index() +')').find('.text_boxes,.text_boxes_numeric').focus();
				else
					$(this).closest('tr').next().find('td:eq('+ind +')').find('.text_boxes,.text_boxes_numeric').focus();
			}
			else if( e.which==38 )
			{
				var ind= ($(this).closest('td').index()*1)+1;
				if($(this).closest('tr').index()!=1)
					$(this).closest('tr').prev().find('td:eq('+$(this).closest('td').index()+')').find('.text_boxes,.text_boxes_numeric').focus();
				else
					$(this).closest('tr').prev().find('td:eq('+ind+')').find('.text_boxes,.text_boxes_numeric').focus();
			}
		});
	}
	
	function fnc_valid_time(val,field_id)
	{
		var val_length=val.length;
		if(val_length==2)
		{
			document.getElementById(field_id).value=val+":";
		}
		
		var colon_contains=val.includes(":");
		if(colon_contains==false)
		{
			if(val>23)
			{
				document.getElementById(field_id).value='23:';
			}
		}
		else
		{
			var data=val.split(":");
			var minutes=data[1];
			var str_length=minutes.length;
			var hour=data[0]*1;
			
			if(hour>23)
			{
				hour=23;
			}
			
			if(str_length>=2)
			{
				minutes= minutes.substr(0, 2);
				if(minutes*1>59)
				{
					minutes=59;
				}
			}
			
			var valid_time=hour+":"+minutes;
			document.getElementById(field_id).value=valid_time;
		}
	}
	
	function numOnly(myfield, e, field_id)
	{
		var key;
		var keychar;
		if (window.event) key = window.event.keyCode;
		else if (e) key = e.which;
		else return true;
		keychar = String.fromCharCode(key);
	
		// control keys
		if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;
		// numbers
		else if ((("0123456789:").indexOf(keychar) > -1))
		{
			var dotposl=document.getElementById(field_id).value.lastIndexOf(":");
			if(keychar==":" && dotposl!=-1)
			{
				return false;
			}
			return true;
		}
		else
			return false;
	}
	
	function openmypage_agent_location(page_link,title,type)
	{
		var temp_id=document.getElementById('cbo_temp_id').value;
		page_link=page_link+'&temp_id='+temp_id+'&type='+type;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=350px,center=1,resize=0,scrolling=0','../')
		release_freezing();
		emailwindow.onclose=function()
		{
			if(type==1) load_drop_down( 'requires/short_quotation_v6_controller', type, 'load_drop_agent_location_name', 'agent_td');
			else if (type==2) load_drop_down( 'requires/short_quotation_v6_controller', type, 'load_drop_agent_location_name', 'location_td');
		}
	}
	
	function fnc_meeting_no(chk_meeting_val)
	{
		var meeting_val=1;
		var max_meeting_no=return_ajax_request_value('', 'max_meeting_no', 'requires/short_quotation_v6_controller');
		if(chk_meeting_val==1)
		{
			meeting_val=(max_meeting_no*1)+1;
		}
		else
		{
			meeting_val=(max_meeting_no*1);
		}
		$('#txt_meeting_no').val(meeting_val);
	}
	
	function fnc_fobavg_option()
	{
		if( form_validation('txt_costSheetNo','Please Save First.')==false)
		{
			return;
		}
		else
		{
			var data=$('#txt_costSheetNo').val();
			var page_link='requires/short_quotation_v6_controller.php?action=fobavg_option_popup';
			var title="FOB Average Option PopUp";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&data='+data, title, 'width=650px,height=450px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				
			}
		}
	}
	
	function print_report_button_setting()
	{
		var report_ids=return_ajax_request_value('', 'print_btn_id', 'requires/short_quotation_v6_controller');
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==84) $("#report_btn_2").show();
			if(report_id[k]==86) $("#report_btn_1").show();
		}
	}
	
	function fnc_print_report(action)
	{
		if( form_validation('txt_costSheetNo','Please Save First.')==false)
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#hid_qc_no').val()+'*'+$('#txt_costSheetNo').val()+'*'+report_title, action,'requires/short_quotation_v6_controller');
		return;
	}
	
	function fnc_openmypage_inquery()
	{
		if( form_validation('cbo_temp_id','Template Name')==false)
		{
			return;
		}
		var page_link='requires/short_quotation_v6_controller.php?action=inquery_id_popup';
		var title='Inquiry ID Selection Form' ;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1020px,height=450px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theval=this.contentDoc.getElementById("selected_id").value;
			//alert(theval);
			if(theval!="")
			{
				var theemail=theval.split("_");
				//alert(theemail[1]);
				document.getElementById('txt_inquery_id').value=theemail[0];
				document.getElementById('cbo_buyer_id').value=theemail[1];
				
				load_drop_down( 'requires/short_quotation_v6_controller', theemail[1], 'load_drop_down_season', 'season_td');
				load_drop_down( 'requires/short_quotation_v6_controller', theemail[1], 'load_drop_down_brand', 'brand_td');
				
				document.getElementById('txt_styleRef').value=theemail[2];
				document.getElementById('txt_inquiry_no').value=theemail[3];
				document.getElementById('cbo_season_id').value=theemail[4];
				document.getElementById('cbo_season_year').value=theemail[5];
				document.getElementById('cbo_brand').value=theemail[6];
				document.getElementById('cbo_product_department').value=theemail[7];
				document.getElementById('txt_styleDesc').value=theemail[8];
				document.getElementById('txt_inqueryfab_id').value=theemail[9];
				
				get_php_form_data(theemail[9], "populate_data_from_rdnolib", "requires/short_quotation_v6_controller" );
			}
		}
	}
	
	function fnc_bodyPart(inc)
	{
		var incVal=inc.split("_");
		var i=incVal[0];
		var type=incVal[1];
		//var bodyPart=$('#txtbodyparttext_'+i).val();
		var cbofabricnature=2;
		var pageTitle="";
		
		if(type==3) 
		{
			pageTitle="Wash Type PopUp";
			var page_link='requires/short_quotation_v6_controller.php?action=bodyPart_washType&type='+type;
		}
		else if(type==5)
		{
			pageTitle="Item Group PopUp";
			var page_link='requires/short_quotation_v6_controller.php?action=itemGroup_popup&type='+type;
		}
		else 
		{ 
			pageTitle="Body Part PopUp";
			var page_link='requires/short_quotation_v6_controller.php?action=bodyPart_washType&type='+type;
		}
		
		//var page_link='requires/short_quotation_v6_controller.php?action=bodyPart_washType_ItemGroup_popup&type='+type;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, pageTitle, 'width=460px,height=450px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			if(type!=5)
			{
				var id=this.contentDoc.getElementById("gid").value;
				var name=this.contentDoc.getElementById("gname").value;
			}
			if(type==1)
			{
				document.getElementById('txtbodyparttext_'+i).value=name;
				document.getElementById('txtbodypartid_'+i).value=id;
			}
			else if(type==2)
			{
				document.getElementById('txtspbodyparttext_'+i).value=name;
				document.getElementById('txtspbodypartid_'+i).value=id;
			}
			else if(type==3)
			{
				document.getElementById('txtWashTypetext_'+i).value=name;
				document.getElementById('txtWashTypeId_'+i).value=id;
			}
			else if(type==4)
			{
				document.getElementById('txtWbodyparttext_'+i).value=name;
				document.getElementById('txtWbodypartid_'+i).value=id;
			}
			else if(type==5)
			{
				/*var iduom=id.split("_");
				document.getElementById('txtAcctext_'+i).value=name;
				document.getElementById('txtAccId_'+i).value=iduom[0];
				document.getElementById('cboconsuom_'+i).value=iduom[1];*/
				
				var itemdata=this.contentDoc.getElementById("itemdata").value;
				//alert(itemdata);
				var row_count=$('#particulars_acc tr').length;
				var itemdata=itemdata.split(",");
				//alert(row_count)
				var a=0; var n=0;
				for(var b=1; b<=itemdata.length; b++)
				{
					//alert(itemdata[a]);
					var exdata="";
					var exname=itemdata[a].split("***");
					//alert(exname);
					if(a==0)
					{
						document.getElementById('txtAcctext_'+i).value=exname[1];
						document.getElementById('txtAccId_'+i).value=exname[0];
						document.getElementById('cboconsuom_'+i).value=exname[2];
						document.getElementById('hiddencalparameter_'+i).value=exname[3];
						$('#txtAccConsCalData_'+i).val('');
						if(exname[3]==1 || exname[3]==2 || exname[3]==3 || exname[3]==4 || exname[3]==5 || exname[3]==6 || exname[3]==7 || exname[3]==8 || exname[3]==9 || exname[3]==10 || exname[3]==11 || exname[3]==12 || exname[3]==13)
						{
							$('#txtAccConsumtion_'+i).removeAttr("onDblClick").attr("onDblClick","fnc_openmypage_calparameter("+exname[0]+",'"+exname[1]+"',"+exname[3]+","+i+");");
							//$('#txtaccConsumtion_'+i).attr('readonly','readonly');
							$('#txtAccConsumtion_'+i).attr("placeholder", "Browse/Write");
						}
						else
						{
							//$('#txtaccConsumtion_'+i).removeAttr('readonly','readonly');
							$('#txtAccConsumtion_'+i).attr('placeholder','Write');
							$('#txtAccConsumtion_'+i).removeAttr("onDblClick");
						}
					}
					else
					{
						//add_break_down_tr_trim_cost(row_count);
						i++;
						//alert(row_count)
						if(i>15) append_row();
						document.getElementById('txtAcctext_'+i).value=exname[1];
						document.getElementById('txtAccId_'+i).value=exname[0];
						document.getElementById('cboconsuom_'+i).value=exname[2];
						document.getElementById('hiddencalparameter_'+i).value=exname[3];
						$('#txtAccConsCalData_'+i).val('');
						if(exname[3]==1 || exname[3]==2 || exname[3]==3 || exname[3]==4 || exname[3]==5 || exname[3]==6 || exname[3]==7 || exname[3]==8 || exname[3]==9 || exname[3]==10 || exname[3]==11 || exname[3]==12 || exname[3]==13)
						{
							$('#txtAccConsumtion_'+i).removeAttr("onDblClick").attr("onDblClick","fnc_openmypage_calparameter("+exname[0]+",'"+exname[1]+"',"+exname[3]+","+i+");");
							$('#txtAccConsumtion_'+i).attr('readonly','readonly');
							$('#txtAccConsumtion_'+i).attr("placeholder", "Browse");
						}
						else
						{
							$('#txtAccConsumtion_'+i).removeAttr('readonly','readonly');
							$('#txtAccConsumtion_'+i).attr('placeholder','');
							$('#txtAccConsumtion_'+i).removeAttr("onDblClick");
						}
						//alert(a)
						//document.getElementById('cbogroup_'+row_count).value=exdata[0];
						//document.getElementById('cbogrouptext_'+row_count).value=exdata[1];
						//document.getElementById('cboconsuom_'+row_count).value=exdata[2];
						//$('#cbogrouptext_'+row_count).removeAttr("title").attr( 'title',exdata[1] );
						//set_trim_cons_uom(exdata[0],row_count);
					}
					a++;
				}
			}
		}
	}
	
	function fnc_openmypage_calparameter(id, trimsname, calparameter,inc)
	{
		//alert(calparameter)
		var consCalData=$('#txtAccConsCalData_'+inc).val();
		var trimUom=$('#cboconsuom_'+inc).val();
		var title = 'Trims Cons. Details PopUp';	
		var page_link = 'requires/short_quotation_v6_controller.php?action=trimscons_details_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&id='+id+'&trimsname='+trimsname+'&calparameter='+calparameter+'&consCalData='+consCalData+'&trimUom='+trimUom, title, 'width=850px,height=380px,center=1,resize=0,scrolling=0','../')
		release_freezing();
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var calculatedCons=this.contentDoc.getElementById("txt_caculated_value").value;
			var calculator_string=this.contentDoc.getElementById("calculator_string").value;
			
			$('#txtAccConsumtion_'+inc).val( calculatedCons );
			$('#txtAccConsCalData_'+inc).val( calculator_string );
			//alert(calculatedCons)
			//fnc_amount_calculation('accessories', inc, '');
			fnc_amount_calculation('accessories',inc,0)
		}
	}
	
	function fnc_type_loder( i )
	{
		var cboembname=document.getElementById('cboSpeciaOperationId_'+i).value
		load_drop_down( 'requires/short_quotation_v6_controller', cboembname+'_'+i, 'load_drop_down_embtype', 'embtypetd_'+i );
	}
	
	function fnc_fabric_popup(inc)
	{
		var txt_fabid =document.getElementById('txt_fabid_'+inc).value;
		var inqueryfab_id =document.getElementById('txt_inqueryfab_id').value;
		var page_link='requires/short_quotation_v6_controller.php?action=fabric_description_popup&txt_fabid='+txt_fabid+'&inqueryfab_id='+inqueryfab_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Fabric Description', 'width=1060px,height=450px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var fab_des_id=this.contentDoc.getElementById("hiddfabid");
			var fab_desctiption=this.contentDoc.getElementById("hiddFabricDescription");
			var fab_gsm=this.contentDoc.getElementById("fab_gsm");
			var yarnDesc=this.contentDoc.getElementById("hiddYarnDescription");
			
			document.getElementById('txt_fabid_'+inc).value=fab_des_id.value;
			document.getElementById('txt_fabDesc'+inc).value=fab_desctiption.value;
			document.getElementById('txt_fabDesc'+inc).title=fab_desctiption.value;
			document.getElementById('txtfabwidth_'+inc).value=fab_gsm.value;
			document.getElementById('txtYarnDtls_'+inc).value=yarnDesc.value;
		}
	}
	
	function append_row(inc,type)
	{
		if(type==1)
		{
			var counter =$('#tbl_wash tbody tr').length; 
			var i=inc;
			if (counter!=i)
			{
				return false;
			}
			else
			{
				i++;
				$("#tbl_wash tbody tr:last").clone().find("input,select").each(function() {
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						'name': function(_, name) { return name + i },
						'value': function(_, value) { return value }
					});
				}).end().appendTo("#tbl_wash");
				
				$('#txtWashTypetext_'+i).removeAttr("onDblClick").attr("onDblClick","fnc_bodyPart('"+i+"_3')");
				$('#txtWashTypetext_'+i).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
				
				$('#txtWbodyparttext_'+i).removeAttr("onDblClick").attr("onDblClick","fnc_bodyPart('"+i+"_4')");
				$('#txtWbodyparttext_'+i).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
				
				$('#txtWConsumtion_'+i).removeAttr("onChange").attr("onChange","fnc_amount_calculation('wash','"+i+"',0)");
				$('#txtWexper_'+i).removeAttr("onChange").attr("onChange","fnc_amount_calculation('wash','"+i+"',0)");
				
				$('#txtwRate_'+i).removeAttr("onChange").attr("onChange","fnc_amount_calculation('wash','"+i+"',0)");
				
				$('#increasewash_'+i).removeAttr("onClick").attr("onClick","append_row("+i+",'1');");
				$('#decreasewash_'+i).removeAttr("onClick").attr("onClick","fnc_remove_row("+i+",'1');");
				
				$('#txtWashTypetext_'+i).val("");
				$('#txtWashTypeId_'+i).val("");
				$('#txtWbodyparttext_'+i).val("");
				$('#txtWbodypartid_'+i).val("");
				$('#txtWConsumtion_'+i).val("");
				
				$('#txtWexper_'+i).val("");
				$('#txtTotWConsumtion_'+i).val("");
				$('#txtwRate_'+i).val("");
				$('#txtWValue_'+i).val("");
				$('#txtWashRemarks_'+i).val("");
			}
			fnc_amount_calculation('wash','"+i+"',0)
		}
		else if(type==2)
		{
			var counter =$('#tbl_acc tbody tr').length; 
			var i=inc;
			//alert(counter+'_'+i);// return;
			if(counter!=i)
			{
				return false;
			}
			else
			{
				i++;
				$("#tbl_acc tbody tr:last").clone().find("input,select").each(function() {
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						'name': function(_, name) { return name + i },
						'value': function(_, value) { return value }
					});
				}).end().appendTo("#tbl_acc tbody:last");
				
				$('#txtAcctext_'+i).removeAttr("onDblClick").attr("onDblClick","fnc_bodyPart('"+i+"_5')");
				$('#txtAcctext_'+i).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
				
				$('#txtAccDescription_'+i).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
				$('#txtAccBandRef_'+i).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
				$('#txtAccUseFor_'+i).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
				$('#cboconsuom_'+i).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
				$('#txtAccConsumtion_'+i).removeAttr("onChange").attr("onChange","fnc_amount_calculation('accessories','"+i+"','pack')");
				$('#txtAcexper_'+i).removeAttr("onChange").attr("onChange","fnc_amount_calculation('accessories','"+i+"',0)");
				$('#accRatePop_'+i).removeAttr("onClick").attr("onClick","fnc_rate_write_popup('"+i+"','2')");
				
				$('#txtAccRate_'+i).removeAttr("onChange").attr("onChange","fnc_amount_calculation('accessories','"+i+"',0)");
				$('#txtAccRate_'+i).removeAttr("onDblClick");
				$('#increasetrim_'+i).removeAttr("onClick").attr("onClick","append_row("+i+",'2');");
				$('#decreasetrim_'+i).removeAttr("onClick").attr("onClick","fnc_remove_row("+i+",'2');");
				
				$('#txtAccConsumtion_'+i).removeAttr('readonly','readonly');
				$('#txtAcexper_'+i).removeAttr('readonly','readonly');
				$('#txtAccRate_'+i).removeAttr('readonly','readonly');
				$('#txtAccConsumtion_'+i).attr("placeholder","Write");
				$('#txtAcexper_'+i).attr("placeholder","Write");
				$('#txtAccRate_'+i).attr("placeholder", "Write");
				
				$('#txtAcctext_'+i).val("");
				$('#txtAccId_'+i).val("");
				$('#txtAccDescription_'+i).val("");
				$('#txtAccBandRef_'+i).val("");
				$('#txtAccUseFor_'+i).val("");
				$('#cboconsuom_'+i).val(0);
				$('#accRatePop_'+i).prop('checked', false);
				$('#accRatePop_'+i).val(2);
				$('#txtAccRateData_'+i).val("");
				$('#txtAccConsumtion_'+i).val("");
				
				$('#txtAcexper_'+i).val("");
				$('#txtTotAccConsumtion_'+i).val("");
				
				$('#txtAccRate_'+i).val("");
				$('#txtAccValue_'+i).val("");
				$('#txtAccRemarks_'+i).val("");
				$("#txtAcctext_"+i).focus();
			}
			fnc_amount_calculation('accessories',"'"+i+"'",0)
		}
	}
	
	function fnc_remove_row(inc,type)
	{
		if(type==1)//wash
		{
			var counter =$('#tbl_wash tbody tr').length; 
			if(counter!=1)
			{
				//var permission_array=permission.split("_");
				/*var updateid=$('#updateidtrim_'+rowNo).val();
				var txt_job_no=$('#txt_job_no').val();
				if(updateid !="" && permission_array[2]==1)
				{
					
				}*/
				var index=inc-1
				$("table#tbl_wash tbody tr:eq("+index+")").remove()
				var numRow = $('table#tbl_wash tbody tr').length;
				for(i = inc; i <= numRow; i++)
				{
					var indx=i-1;
					$("#tbl_wash tbody tr:eq("+indx+")").find("input,select").each(function() {
						$(this).attr({
							'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
							'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
							//'value': function(_, value) { return value }
						});
						
						$('#txtWashTypetext_'+i).removeAttr("onDblClick").attr("onDblClick","fnc_bodyPart('"+i+"_3')");
						$('#txtWashTypetext_'+i).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
						
						$('#txtWbodyparttext_'+i).removeAttr("onDblClick").attr("onDblClick","fnc_bodyPart('"+i+"_4')");
						$('#txtWbodyparttext_'+i).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
						
						$('#txtWConsumtion_'+i).removeAttr("onChange").attr("onChange","fnc_amount_calculation('wash','"+i+"',0)");
						$('#txtWexper_'+i).removeAttr("onChange").attr("onChange","fnc_amount_calculation('wash','"+i+"',0)");
						
						$('#txtwRate_'+i).removeAttr("onChange").attr("onChange","fnc_amount_calculation('wash','"+i+"',0)");
						//$('#txtwRate_'+i).removeAttr("onKeyUp").attr("onKeyUp","append_row('"+i+",'1')");
						
						$('#increasewash_'+i).removeAttr("onClick").attr("onClick","append_row("+i+",'1');");
						$('#decreasewash_'+i).removeAttr("onClick").attr("onClick","fnc_remove_row("+i+",'1');");
						
					})
				}
				fnc_amount_calculation('wash','"+i+"',0)
			}
		}
		else if(type==2)//trim
		{
			var counter =$('#tbl_acc tbody tr').length; 
			if(counter!=1)
			{
				//var permission_array=permission.split("_");
				/*var updateid=$('#updateidtrim_'+rowNo).val();
				var txt_job_no=$('#txt_job_no').val();
				if(updateid !="" && permission_array[2]==1)
				{
				
				}*/
				var index=inc-1
				$("table#tbl_acc tbody tr:eq("+index+")").remove()
				var numRow = $('table#tbl_acc tbody tr').length;
				for(i = inc; i <= numRow; i++)
				{
					var indx=i-1;
					$("#tbl_acc tbody tr:eq("+indx+")").find("input,select").each(function() {
						$(this).attr({
							'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
							'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
							//'value': function(_, value) { return value }
						});
						
						$('#txtAcctext_'+i).removeAttr("onDblClick").attr("onDblClick","fnc_bodyPart('"+i+"_5')");
						$('#txtAcctext_'+i).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
						
						$('#txtAccDescription_'+i).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
						$('#txtAccBandRef_'+i).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
						$('#txtAccUseFor_'+i).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
						$('#cboconsuom_'+i).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
						$('#txtAccConsumtion_'+i).removeAttr("onChange").attr("onChange","fnc_amount_calculation('accessories','"+i+"','pack')");
						$('#txtAcexper_'+i).removeAttr("onChange").attr("onChange","fnc_amount_calculation('accessories','"+i+"',0)");
						
						$('#txtAccRate_'+i).removeAttr("onChange").attr("onChange","fnc_amount_calculation('accessories','"+i+"',0)");
						$('#increasetrim_'+i).removeAttr("onClick").attr("onClick","append_row("+i+",'2');");
						$('#decreasetrim_'+i).removeAttr("onClick").attr("onClick","fnc_remove_row("+i+",'2');");
					})
				}
				fnc_amount_calculation('accessories','"+i+"',0);
			}
		}
		else if(type==3)//Fabric
		{
			$('#txtbodyparttext_'+inc).val('');
			$('#txtbodypartid_'+inc).val('');
			$('#txt_fabDesc'+inc).val('');
			$('#txt_fabid_'+inc).val('');
			$('#txtYarnDtls_'+inc).val('');
			$('#cbocolortype_'+inc).val(0);
			$('#cbofabuom_'+inc).val(0);
			$('#txtgsmweight_'+inc).val('');
			$('#txtfabwidth_'+inc).val('');
			$('#txtusefor_'+inc).val('');
			$('#txt_consumtion'+inc).val('');
			$('#txt_exper'+inc).val('');
			$('#txt_totConsumtion'+inc).val('');
			document.getElementById('ratePop_'+inc).checked=false;
			$("#ratePop_"+inc).val(2);
			$('#txtRateData_'+inc).val('');
			$('#txt_rate'+inc).val('');
			$('#txt_value'+inc).val('');
			//$('#txtfabupid_'+inc).val('');
			$('#cbofabuom_'+inc).val(0);
			
			$('#txt_consumtion'+inc).removeAttr('readonly','readonly');
			$('#txt_exper'+inc).removeAttr('readonly','readonly');
			$('#txt_rate'+inc).removeAttr('readonly','readonly');
			$('#txt_consumtion'+inc).attr("placeholder","Write");
			$('#txt_exper'+inc).attr("placeholder","Write");
			$('#txt_rate'+inc).attr("placeholder", "Write");
			$('#txt_rate'+inc).removeAttr("onDblClick");
			
			fnc_amount_calculation('fabric','"+i+"',0);
		}
	}
	
	function openmypage_template_name(title)
	{
		var page_link='requires/short_quotation_v6_controller.php?action=trims_cost_template_name_popup&buyer_name=' + document.getElementById('cbo_buyer_id').value;
		if(form_validation('cbo_buyer_id','Buyer Name')==false )
		{
			return;
		}
		else
		{
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=400px,center=1,resize=0,scrolling=0', '../')
			emailwindow.onclose = function()
			{
				var theform = this.contentDoc.forms[0];
				var select_template_data = this.contentDoc.getElementById('select_template_data').value;
				//alert(select_template_data);
				if(select_template_data!='')
				{
					var itemdata=select_template_data.split("__");
					var row_count=$('#tbl_acc tbody tr').length;
					//load_template_data(select_template_data);
					//alert(itemdata.length+'-'+row_count)
					var a=0; var n=0;
					for(var b=1; b<=itemdata.length; b++)
					{
						var exdata="";
						var exdata=itemdata[a].split("***");
						if(row_count == 1 && document.getElementById('cboconsuom_1').value == 0)
						{
							document.getElementById('txtAcctext_1').value=exdata[0];
							document.getElementById('txtAccId_1').value=exdata[2];
							document.getElementById('txtAccDescription_1').value=exdata[10];
							document.getElementById('txtAccBandRef_1').value=exdata[9];
							document.getElementById('cboconsuom_1').value=exdata[3];
							document.getElementById('txtAccConsumtion_1').value=exdata[4];
							document.getElementById('txtAccRate_1').value=exdata[5];
							document.getElementById('txtAccValue_1').value=exdata[6];
						}
						else if(row_count == 1 && document.getElementById('cboconsuom_1').value == 42)
						{
							document.getElementById('txtAcctext_1').value=exdata[0];
							document.getElementById('txtAccId_1').value=exdata[2];
							document.getElementById('txtAccDescription_1').value=exdata[10];
							document.getElementById('txtAccBandRef_1').value=exdata[9];
							document.getElementById('cboconsuom_1').value=exdata[3];
							document.getElementById('txtAccConsumtion_1').value=exdata[4];
							document.getElementById('txtAccRate_1').value=exdata[5];
							document.getElementById('txtAccValue_1').value=exdata[6];
						}
						else
						{
							//alert(itemdata.length+'-'+row_count)
							//add_break_down_tr_trim_cost(row_count);
							if(b<=row_count)
							{
								row_count=b;
							}
							else
							{
								append_row(row_count,2);
								n++;
								row_count++;
							}
							document.getElementById('txtAcctext_'+row_count).value=exdata[0];
							document.getElementById('txtAccId_'+row_count).value=exdata[2];
							document.getElementById('txtAccDescription_'+row_count).value=exdata[10];
							document.getElementById('txtAccBandRef_'+row_count).value=exdata[9];
							document.getElementById('cboconsuom_'+row_count).value=exdata[3];
							document.getElementById('txtAccConsumtion_'+row_count).value=exdata[4];
							document.getElementById('txtAccRate_'+row_count).value=exdata[5];
							document.getElementById('txtAccValue_'+row_count).value=exdata[6];
						}
						a++;
						fnc_amount_calculation('accessories',row_count,'dzn');
					}
				}
			}
		}
	}
	
	function check_exchange_rate_variable()
	{
		var cbo_currercy=2;
		var costing_date = $('#txt_costingDate').val();
		var cbo_company_name = $('#cbo_company_id').val();
		$('#cbo_style_owner').val( cbo_company_name );
		var response=return_global_ajax_value( cbo_currercy+"**"+costing_date+"**"+cbo_company_name, 'check_conversion_rate_variable', '', 'requires/short_quotation_v6_controller');
		var response=response.split("_");
		$('#txt_exchangeRate').val(response[1]);
		$('#txt_fab_process_loss_method').val(response[2]);
		$('#txt_acc_process_loss_method').val(response[3]);
	}
	
	function fnc_uomchange(inc)
	{
		var fabuom = $('#cbofabuom_'+inc).val();
		if(fabuom==12)
		{
			fnc_amount_calculation('fabric',inc,'yds');
		}
		else if(fabuom==23)
		{
			fnc_amount_calculation('fabric',inc,'yds');
		}
		else if(fabuom==27)
		{
			fnc_amount_calculation('fabric',inc,'yds');
		}
	}
	
	function location_select()
	{
		if($('#cbo_location_id option').length==2)
		{
			if($('#cbo_location_id option:first').val()==0)
			{
				$('#cbo_location_id').val($('#cbo_location_id option:last').val());
				//eval($('#cbo_location_id').attr('onchange')); 
			}
		}
		else if($('#cbo_location_id option').length==1)
		{
			$('#cbo_location_id').val($('#cbo_location_id option:last').val());
			//eval($('#cbo_location_id').attr('onchange'));
		}	
	}
	
</script>
<style>
	.textbox1
	{
		background-color : #FFC0CB;
	}
	.textbox2
	{
		background-color : #DDA0DD;
	}
	
	.tr1
	{
		background:#FFC0CB;
	}
	
	.tr2
	{
		background:#DDA0DD;
	}
</style> 	
    
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;">
        <div style="display:none"><? echo load_freeze_divs ("../../",$permission); ?></div>
        <form name="quickCosting_1" id="quickCosting_1" autocomplete="off">
            <fieldset style="width:1200px;">
                <table width="1200px" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                    	<td width="110" class="must_entry_caption"><strong>Company</strong></td>
                    	<td width="130"><? echo create_drop_down( "cbo_company_id", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "--Company--", $selected,"load_drop_down( 'requires/short_quotation_v6_controller', this.value, 'load_drop_down_location', 'location_td'); check_exchange_rate_variable(); location_select();",'' ); ?></td>
                        <td width="110">Location</td>
                    	<td width="130" id="location_td"><? echo create_drop_down( "cbo_location_id", 130, $blank_array,"", 1, "--Location--", $selected, "" ); ?></td>
                    	<td width="110" class="must_entry_caption">Template Name<input type="button" class="formbutton" style="width:20px; font-style:italic" value="N" onClick="openmypage_temp('requires/short_quotation_v6_controller.php?action=template_popup','Create Template')"/>
                        <input type="hidden" id="txt_seleted_row_id">
                        <input type="hidden" id="hid_selected_cost_no">
                        <input type="hidden" id="hid_qc_no">
                        <input type="hidden" id="txt_inquery_id">
                        <input type="hidden" id="txt_inqueryfab_id">
                        <input type="hidden" id="txt_fab_process_loss_method">
                        <input type="hidden" id="txt_acc_process_loss_method">
                        <input style="width:70px;" type="hidden" name="txt_temp_id" id="txt_temp_id" />
                        </td>
                        <td width="130" id="template_td"><?  
                        $sql_tmp="select tuid, temp_id, item_id1, ratio1, status_active from qc_template where status_active=1 and is_deleted=0 and lib_item_id='0' order by item_id1, ratio1 ASC";
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
                        unset($sql_tmp_res);
                        //print_r($template_name_arr);
                        echo create_drop_down( "cbo_temp_id", 130, $template_name_arr,'', 1, "-Select Template-",$selected, "fnc_template_view();" ); ?></td>
                        
                        <td width="110" class="must_entry_caption"><strong>Inquiry ID</strong></td>
                        <td  width="130"><input style="width:120px;" type="text" class="text_boxes" name="txt_inquiry_no" id="txt_inquiry_no" readonly placeholder="Browse" onDblClick="fnc_openmypage_inquery();"/></td>
                        <td width="110"><strong>Cost Sheet No</strong></td>
                        <td><input style="width:120px;" type="text" class="text_boxes_numeric textbox2" name="txt_costSheetNo" id="txt_costSheetNo" placeholder="Display"  readonly/><input style="width:40px;" type="hidden" name="txt_update_id" id="txt_update_id"/></td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">Style Ref.</td>
                        <!--Cons. Basis<?// echo create_drop_down( "cbouom", 40, $unit_of_measurement,'', 0, '-Uom-', 12, "",$disabled,"12,23,27" ); ?>
                        <?// echo create_drop_down( "cbo_cons_basis_id", 130, $qc_consumption_basis,'', 1, "--Select Basis--",1, "fnc_consumption_write_disable(this.value+'__'+1);", "","1" ); ?>-->
                        <td><input style="width:120px;" type="text" onDblClick="openmypage_style(0);" class="text_boxes textbox1" name="txt_styleRef" id="txt_styleRef" placeholder="Browse/Write" /></td>
                        <td class="must_entry_caption"><strong>Buyer Name</strong></td>
                        <td><? echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down('requires/short_quotation_v6_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'requires/short_quotation_v6_controller',this.value, 'load_drop_down_sub_dep', 'sub_td' ); load_drop_down( 'requires/short_quotation_v6_controller', this.value, 'load_drop_down_brand', 'brand_td');" ); ?></td>
                        <td class="must_entry_caption"><strong>Season</strong>&nbsp;&nbsp;&nbsp;<? echo create_drop_down( "cbo_season_year", 50, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
                        <td id="season_td"><? echo create_drop_down("cbo_season_id", 130, $blank_array,'', 1, "-- Select Season--",$selected, "" ); ?></td>
                        <td><strong>Brand</strong></td>
                        <td id="brand_td"><? echo create_drop_down("cbo_brand", 130, $blank_array,"",1, "-Brand-", $selected,""); ?></td>
                        <td><strong>Style Description</strong></td>
                        <td><input style="width:120px;" type="text" class="text_boxes" name="txt_styleDesc" id="txt_styleDesc" /></td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption"><strong>Costing Date</strong></td>
                        <td><input style="width:120px;" type="text" class="datepicker" name="txt_costingDate" id="txt_costingDate" value="<?=date('d-m-Y');?>" /></td>
                        <td class="must_entry_caption"><strong>Exchange Rate</strong></td>
                        <td><input style="width:120px;" type="text" class="text_boxes_numeric" name="txt_exchangeRate" id="txt_exchangeRate" value="80" /></td>
                    	<td class="must_entry_caption">Prod. Dept.</td>
                        <td><? echo create_drop_down( "cbo_product_department", 75, $product_dept, "", 1, "-Select-", $selected, "", "", "" ); ?>&nbsp;<input class="text_boxes" type="text" style="width:35px;" name="txt_product_code" id="txt_product_code" placeholder="P.Code" maxlength="10" title="Maximum 10 Character" />
                        </td>
                        <td><strong>Sub. Dept.</strong></td>
                        <td id="sub_td"><? echo create_drop_down( "cbo_subDept_id", 130, $blank_array,'', 1, "-- Select Dept--",$selected, "" ); ?></td>
                        <td><strong>Delivery Date</strong></td>
                        <td><input name="txt_delivery_date" id="txt_delivery_date" class="datepicker" type="text" style="width:117px;" value="" /></td>
                    </tr>
                    <tr>
                    	
                        <td><strong>Offer Qty</strong></td>
                        <td><input style="width:120px;" type="text" class="text_boxes_numeric" name="txt_offerQty" id="txt_offerQty" onChange="fnc_amount_calculation(7,0,0);" /></td>
                    	<td><strong>Stage </strong><input type="button" class="formbutton" style="width:50px; font-style:italic" value="New" onClick="fnc_new_stage_popup();"/></td>
                        <td id="stage_td"><?=create_drop_down( "cbo_stage_id", 130,"select tuid, stage_name from lib_stage_name where status_active=1 and is_deleted=0","tuid,stage_name", 1, "-- Select --", $selected, "" ); ?></td>
                        <td><strong>Quoted Price ($)</strong></td>
                        <td><input style="width:120px;" type="text" class="text_boxes_numeric" name="txt_quotedPrice" id="txt_quotedPrice" /></td>
                        <td class="must_entry_caption"><strong>TGT Price</strong></td>
                        <td><input style="width:120px;" type="text" class="text_boxes_numeric" name="txt_tgtPrice" id="txt_tgtPrice" /></td>
                        <td><strong>Article</strong></td>
                        <td><input style="width:120px;" type="text" class="text_boxes" name="txt_article" id="txt_article" /></td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption"><strong>Costing Per</strong></td>
                        <td><?=create_drop_down( "cbo_costing_per", 130, $qccosting_per,"", 1, "-Costing Per-", $selected, "" ); ?></td>
                        <td><strong>Style Owner</strong></td>
                        <td><?=create_drop_down( "cbo_style_owner", 130, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Style Owner--", $selected, ""); ?></td>
                        <td><input type="button" class="image_uploader" style="width:90px" value="ADD IMG" onClick="file_uploader ( '../../', document.getElementById('hid_qc_no').value,'', 'short_quotation_v2', 0 ,1)"></td>
                        
                        <td><input type="button" class="image_uploader" style="width:120px" value="ADD FILE" onClick="file_uploader ( '../../', document.getElementById('hid_qc_no').value,'', 'short_quotation_v2', 2 ,1)">
                        	<div id="td_hiddData" style="display:none">
                        	<input style="width:120px;" type="hidden" class="text_boxes" name="txtfabricData_0" id="txtfabricData_0" />
                        	<input style="width:120px;" type="hidden" class="text_boxes" name="txtspData_0" id="txtspData_0" />.
                            <input style="width:120px;" type="hidden" class="text_boxes" name="txtwashData_0" id="txtwashData_0" />
                            <input style="width:120px;" type="hidden" class="text_boxes" name="txtaccData_0" id="txtaccData_0" />
                            </div>
                        </td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </fieldset>
            <br>
            <fieldset style="width:1200px;">
            <table width="1200" cellspacing="2" cellpadding="0" border="0" class="rpt_table" rules="all">
                <tr>
                	<td width="885" valign="top">
                    	<table width="885" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                        	<thead>
                                <tr>
                                	<th class="must_entry_caption" id="item_td" colspan="2"><?=create_drop_down( "cboItemId", 90, $blank_array,"", 1, "-Select-", 1, "fnc_change_data(); fnc_itemwise_data_cache();" ); ?></th>
                                	<th class="must_entry_caption" colspan="14">Particulars For Fabric</th>
                                </tr>
                                <tr>
                                    <th width="100">Total Ratio</th>
                                    <th width="150" align="center" id="tdpackQty">&nbsp;</th>
                                    <th width="60">Item Ratio</th>
                                    <th width="80" align="center" id="tditemRatio">&nbsp;</th>
                                    <th width="50" rowspan="2">F. Width [Inch]</th>
                                    <th width="50" rowspan="2" title="Fabric Weight [GSM]">GSM</th>
                                    <th width="70" rowspan="2">Use For</th>
                                    <th width="20" rowspan="2">Cal</th>
                                    <th width="40" rowspan="2">Cons</th>
                                    <th width="30" rowspan="2">Ex %</th>
                                    <th width="40" rowspan="2">Tot. Cons</th>
                                    <th width="40" rowspan="2">Avg. Rate</th>
                                    <th width="50" rowspan="2">Value</th>
                                    <th rowspan="2">&nbsp;</th>
                                </tr>
                                <tr>
                                	<th width="100">Body Part</th>
                                    <th width="150">Fab. Des.</th>
                                    <th width="60">UOM</th>
                                    <th width="80">Color Type</th>
                                    
                                </tr>
                            </thead>
                        </table>
                        <div style="width:885px; max-height:110px; overflow-y:scroll" id="scroll_body" > 
                    	<table width="865" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                            <tbody>
                                <? $hd=9; $m=1;
								for($n=1; $n<=$hd; $n++) { ?>
                                <tr>
                                    <td width="100"><input style="width:87px;" type="text" class="text_boxes" name="txtbodyparttext_<?=$m; ?>" id="txtbodyparttext_<?=$m; ?>" placeholder="Browse" onFocus="add_auto_complete('<?=$m.'_1'; ?>');" onDblClick="fnc_bodyPart('<?=$m.'_1'; ?>');" onBlur="fnc_itemwise_data_cache(); fn_getIndex('<?=$m.'_1'; ?>',this.value);" readonly /><input style="width:50px;" type="hidden" name="txtbodypartid_<?=$m; ?>" id="txtbodypartid_<?=$m; ?>" readonly /><input style="width:50px;" type="hidden" name="txtfabupid_<?=$m; ?>" id="txtfabupid_<?=$m; ?>" readonly /></td>
                                    <td width="150"><input style="width:137px;" type="text" class="text_boxes" name="txt_fabDesc<?=$m; ?>" id="txt_fabDesc<?=$m; ?>" readonly placeholder="Browse" onDblClick="fnc_fabric_popup(<?=$m; ?>);" />
                                    <input style="width:50px;" type="hidden" name="txt_fabid_<?=$m; ?>" id="txt_fabid_<?=$m; ?>" />
                                    <input type="hidden" class="text_boxes" name="txtYarnDtls_<?=$m; ?>" id="txtYarnDtls_<?=$m; ?>" value=""/>
                                    
                                    </td>
                                    <td width="60"><?=create_drop_down( "cbofabuom_".$m, 58, $unit_of_measurement,'', 1, '-select-', "", "fnc_uomchange( $m );","","1,12,23,27" ); ?></td>
                                    <td width="80"><? echo create_drop_down( "cbocolortype_".$m, 78, $color_type,"", 1, "-Select-", $selected, "fnc_itemwise_data_cache();",0,"" ); ?></td>
                                    <td width="50"><input style="width:37px;" type="text" class="text_boxes_numeric" name="txtgsmweight_<?=$m; ?>" id="txtgsmweight_<?=$m; ?>" placeholder="Write" onBlur="fnc_uomchange(<?=$m; ?>);" /></td>
                                    <td width="50"><input style="width:37px;" type="text" class="text_boxes_numeric" name="txtfabwidth_<?=$m; ?>" id="txtfabwidth_<?=$m; ?>" placeholder="Write" onBlur="fnc_uomchange(<?=$m; ?>);" /></td>
                                    <td width="70"><input style="width:57px;" type="text" class="text_boxes" name="txtusefor_<?=$m; ?>" id="txtusefor_<?=$m; ?>" placeholder="Write" onBlur="fnc_itemwise_data_cache();"/></td>
                                    <td width="20" align="center"><input type="checkbox" name="ratePop_<?=$m; ?>" id="ratePop_<?=$m; ?>" onClick="fnc_rate_write_popup(<?=$m; ?>,1);" value="2" style="width:12px;" ><input style="width:10px;" type="hidden" class="text_boxes" name="txtRateData_<?=$m; ?>" id="txtRateData_<?=$m; ?>" /></td>
                                    
                                    <td width="40"><input style="width:27px;" type="text" class="text_boxes_numeric" name="txt_consumtion<?=$m; ?>" id="txt_consumtion<?=$m; ?>" placeholder="Write" onChange="fnc_amount_calculation('fabric',<?=$m; ?>,document.getElementById('txt_rate<?=$m; ?>').value);" onBlur="fnc_itemwise_data_cache();" /></td>
                                    <td width="30"><input style="width:17px;" type="text" class="text_boxes_numeric" name="txt_exper<?=$m; ?>" id="txt_exper<?=$m; ?>" placeholder="Write" onChange="fnc_amount_calculation('fabric',<?=$m; ?>,document.getElementById('txt_rate<?=$m; ?>').value);" /></td>
                                    <td width="40"><input style="width:27px;" type="text" class="text_boxes_numeric" name="txt_totConsumtion<?=$m; ?>" id="txt_totConsumtion<?=$m; ?>" disabled /></td>
                                    <td width="40"><input style="width:27px;" type="text" class="text_boxes_numeric" name="txt_rate<?=$m; ?>" id="txt_rate<?=$m; ?>" placeholder="Write" onChange="fnc_amount_calculation('fabric',<?=$m; ?>,document.getElementById('txt_rate<?=$m; ?>').value);" /></td>
                                    <td width="50"><input style="width:37px;" type="text" class="text_boxes_numeric" name="txt_value<?=$m; ?>" id="txt_value<?=$m; ?>" placeholder="Display" readonly /></td>
                                    <td><input type="button" id="decreasefab_<?=$m; ?>" name="decreasefab_<?=$m; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fnc_remove_row(<?=$m; ?>,3);" /></td>
                                </tr>
                                <? $m++; } ?>
                                </tbody>
                            </table>
                        </div>
                        <table width="850" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                            <tr>
                        		<td width="620" valign="top">
                                    <table width="620" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                                        <thead>
                                            <tr>
                                                <th class="must_entry_caption" colspan="13">Particulars For Accessories</th>
                                            </tr>
                                            <tr>
                                                <th width="100">Item Group <span id="load_temp" style="float:right; width:10px; font-weight: bold;background-color: white;color:black; border: 1px white solid; cursor: pointer;" onClick="openmypage_template_name('Template Search');">...</span></th>
                                                <th width="80">Description</th>
                                                <th width="50">Brand /Sup Ref</th>
                                                <th width="45">Use For</th>
                                                <th width="40">Cons. UOM</th>
                                                <th width="20" style="display:none">Cal</th>
                                                <th width="40">Cons.</th>
                                                <th width="30">Ex %</th>
                                                <th width="40">Tot. Cons.</th>
                                                <th width="40">Rate ($)</th>
                                                <th width="45">Value ($)</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>
                                    </table>
                                    <div style="width:620px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
                                       <table width="600" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" id="tbl_acc">
                                        <?
                                        $acc=15; $k=1;
                                        for($l=1; $l<=$acc; $l++){ 
                                        ?>
                                        <tbody>
                                            <tr id="tracc_<?=$k; ?>">
                                                <td width="100"><input style="width:87px;" type="text" class="text_boxes" name="txtAcctext_<?=$k; ?>" id="txtAcctext_<?=$k; ?>" placeholder="Browse"  onDblClick="fnc_bodyPart('<?=$k; ?>_5');" onBlur="fnc_itemwise_data_cache();" readonly /><input style="width:50px;" type="hidden" name="txtAccId_<?=$k; ?>" id="txtAccId_<?=$k; ?>" readonly /><input style="width:50px;" type="hidden" name="hiddencalparameter_<?=$k; ?>" id="hiddencalparameter_<?=$k; ?>" readonly /><input style="width:50px;" type="hidden" name="txtAccConsCalData_<?=$k; ?>" id="txtAccConsCalData_<?=$k; ?>" readonly /></td>
                                                <td width="80"><input style="width:67px;" type="text" class="text_boxes" name="txtAccDescription_<?=$k; ?>" id="txtAccDescription_<?=$k; ?>" placeholder="Write" onBlur="fnc_itemwise_data_cache();" /></td>
                                                <td width="50"><input style="width:37px;" type="text" class="text_boxes" name="txtAccBandRef_<?=$k; ?>" id="txtAccBandRef_<?=$k; ?>" placeholder="Write" onBlur="fnc_itemwise_data_cache();" /></td>
                                                <td width="40"><input style="width:32px;" type="text" class="text_boxes" name="txtAccUseFor_<?=$k; ?>" id="txtAccUseFor_<?=$k; ?>" placeholder="Write" onBlur="fnc_itemwise_data_cache();" /></td>
                                                <td width="40"><?=create_drop_down( "cboconsuom_$k", 40, $unit_of_measurement,"", 1, "-Select-", 0, "fnc_itemwise_data_cache();",1,"" ); ?></td>
                                                <td width="20" align="center" style="display:none"><input type="checkbox" name="accRatePop_<?=$k; ?>" id="accRatePop_<?=$k; ?>" onClick="fnc_rate_write_popup(<?=$k; ?>,2);" value="2" style="width:12px;" ><input style="width:10px;" type="hidden" class="text_boxes" name="txtAccRateData_<?=$k; ?>" id="txtAccRateData_<?=$k; ?>" /></td>
                                                
                                                <td width="40"><input style="width:28px;" type="text" class="text_boxes_numeric" name="txtAccConsumtion_<?=$k; ?>" id="txtAccConsumtion_<?=$k; ?>" onChange="fnc_amount_calculation('accessories',<?=$k; ?>,'pack');" /></td>
                                                <td width="30"><input style="width:17px;" type="text" class="text_boxes_numeric" name="txtAcexper_<?=$k; ?>" id="txtAcexper_<?=$k; ?>" placeholder="Write" onChange="fnc_amount_calculation('accessories',<?=$k; ?>,0);" /></td>
                                                <td width="40"><input style="width:28px;" type="text" class="text_boxes_numeric" name="txtTotAccConsumtion_<?=$k; ?>" id="txtTotAccConsumtion_<?=$k; ?>" disabled /></td>
                                                
                                                <td width="40"><input style="width:27px;" type="text" class="text_boxes_numeric" name="txtAccRate_<?=$k; ?>" id="txtAccRate_<?=$k; ?>"  onChange="fnc_amount_calculation('accessories',<?=$k; ?>,this.value);" /></td>
                                                <td width="45"><input style="width:33px;" type="text" class="text_boxes_numeric" name="txtAccValue_<?=$k; ?>" id="txtAccValue_<?=$k; ?>" readonly /></td>
                                                 <td><input type="button" id="increasetrim_<?=$k; ?>" name="increasetrim_<?=$k; ?>" style="width:27px" class="formbutton" value="+" onClick="append_row(<?=$k; ?>,2);" />
                                                <input type="button" id="decreasetrim_<?=$k; ?>" name="decreasetrim_<?=$k; ?>" style="width:27px" class="formbutton" value="-" onClick="javascript:fnc_remove_row(<?=$k; ?>,2);" /></td>
                                            </tr>
                                            <? $k++; } ?>
                                        </tbody>
                                    </table>
                                </div>
                                    <table width="620" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                                        <thead>
                                            <tr>
                                                <th class="must_entry_caption" colspan="9">Particulars For Special Operation</th>
                                            </tr>
                                            <tr>
                                                <th width="90">Name</th>
                                                <th width="100">Type</th>
                                                <th width="100">Body Part</th>
                                                <th width="40">Cons.</th>
                                                <th width="33">Ex %</th>
                                                <th width="50">Tot. Cons.</th>
                                                <th width="40">Rate ($)</th>
                                                <th width="50">Value ($)</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                    </table>
                                    <div style="width:620px; max-height:100px; overflow-y:scroll" id="scroll_body" > 
                                           <table width="600" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all"> 
                                            
                                            <? $sp=4; $j=1;
                                            for($i=1; $i<=$sp; $i++) { 
                                            if($i==3) $sel=4; else if ($i==4) $sel=5; else if ($i==5) $sel=99; else $sel=$j;
                                            
                                            if($sel==1) $emb_typearr[$sel]=$emblishment_print_type;
                                            else if($sel==2) $emb_typearr[$sel]=$emblishment_embroy_type;
                                            else if($sel==3) $emb_typearr[$sel]=$emblishment_wash_type;
                                            else if($sel==4) $emb_typearr[$sel]=$emblishment_spwork_type;
                                            else if($sel==5) $emb_typearr[$sel]=$emblishment_gmts_type;
                                            else if($sel==99) $emb_typearr[$sel]=$blank_array;
                                            else $emb_typearr[$sel]=$blank_array;
                                            ?>
                                            <tr bgcolor="#FFCCFF">
                                                <td width="90"><?=create_drop_down( "cboSpeciaOperationId_$j", 90, $emblishment_name_array,"", 1, "--Select--", $sel, "fnc_type_loder(".$j.");","","1,2,4,5,99" ); ?></td>
                                                <td width="100" id="embtypetd_<?=$j; ?>"><?=create_drop_down( "cboSpeciaTypeId_$j", 100, $emb_typearr[$sel],"", 1, "-Select-", "", "" ); ?></td>
                                                <td width="100"><input style="width:87px;" type="text" class="text_boxes" name="txtspbodyparttext_<?=$j; ?>" id="txtspbodyparttext_<?=$j; ?>" placeholder="Browse" onFocus="add_auto_complete('<?=$j.'_2'; ?>');" onDblClick="fnc_bodyPart('<?=$j.'_2'; ?>');" onBlur="fnc_itemwise_data_cache(); fn_getIndex('<?=$j.'_2'; ?>',this.value);" readonly/><input style="width:50px;" type="hidden" name="txtspbodypartid_<?=$j; ?>" id="txtspbodypartid_<?=$j; ?>" readonly /></td>
                                                <td width="40"><input style="width:28px;" type="text" class="text_boxes_numeric" name="txt_spConsumtion<?=$j; ?>" id="txt_spConsumtion<?=$j; ?>" onChange="fnc_amount_calculation('special',<?=$j; ?>,document.getElementById('txtSpRate_<?=$j; ?>').value);" /></td>
                                                <td width="33"><input style="width:20px;" type="text" class="text_boxes_numeric" name="txt_spexper<?=$j; ?>" id="txt_spexper<?=$j; ?>" placeholder="Write" onChange="fnc_amount_calculation('special',<?=$j; ?>,document.getElementById('txtSpRate_<?=$j; ?>').value);" /></td>
                                                <td width="50"><input style="width:38px;" type="text" class="text_boxes_numeric" name="txt_totSpConsumtion<?=$j; ?>" id="txt_totSpConsumtion<?=$j; ?>" disabled /></td>
                                                <td width="40"><input style="width:27px;" type="text" class="text_boxes_numeric" name="txtSpRate_<?=$j; ?>" id="txtSpRate_<?=$j; ?>" onChange="fnc_amount_calculation('special',<?=$j; ?>,this.value);" /></td>
                                                <td width="50"><input style="width:38px;" type="text" class="text_boxes_numeric" name="txt_spValue<?=$j; ?>" id="txt_spValue<?=$j; ?>" readonly /></td>
                                                <td><input style="width:70px;" type="text" class="text_boxes" name="txtSpRemarks_<?=$j; ?>" id="txtSpRemarks_<?=$j; ?>" placeholder="Write" /></td>
                                            </tr>
                                            <? $j++; } ?>
                                        </table>
                        			</div>
                                    <table width="620" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                                        <thead>
                                            <tr>
                                                <th class="must_entry_caption" colspan="9">Particulars For Wash</th>
                                            </tr>
                                            <tr>
                                                <th width="100">Wash Type</th>
                                                <th width="100">Body Part</th>
                                                <th width="50">Cons.</th>
                                                <th width="40">Ex %</th>
                                                <th width="50">Tot. Cons.</th>
                                                <th width="40">Rate ($)</th>
                                                <th width="50">Value ($)</th>
                                                <th width="90">Remarks</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>
                                    </table>
                                    <div style="width:620px; max-height:100px; overflow-y:scroll" id="scroll_body" > 
                                           <table width="600" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" id="tbl_wash"> 
                                            
                                            <? /*$spw=17; $w=1;
                                            for($sw=1; $sw<=$spw; $sw++) {*/ 
                                            //if($i==4) $sel=99; else $sel=$j;
                                            ?>
                                            <tbody>
                                                <tr bgcolor="#FFFF00" id="trwash_1">
                                                    <td width="100"><input style="width:88px;" type="text" class="text_boxes" name="txtWashTypetext_1" id="txtWashTypetext_1" placeholder="Browse" onDblClick="fnc_bodyPart('1_3');" onBlur="fnc_itemwise_data_cache();" readonly /><input style="width:50px;" type="hidden" name="txtWashTypeId_1" id="txtWashTypeId_1" readonly /></td>
                                                    <td width="100"><input style="width:88px;" type="text" class="text_boxes" name="txtWbodyparttext_1" id="txtWbodyparttext_1" placeholder="Browse" onDblClick="fnc_bodyPart('1_4');" onBlur="fnc_itemwise_data_cache();" readonly /><input style="width:50px;" type="hidden" name="txtWbodypartid_1" id="txtWbodypartid_1" readonly /></td>
                                                    
                                                    <td width="50"><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtWConsumtion_1" id="txtWConsumtion_1" onChange="fnc_amount_calculation('wash',1,0);" /></td>
                                                    <td width="40"><input style="width:27px;" type="text" class="text_boxes_numeric" name="txtWexper_1" id="txtWexper_1" placeholder="Write" onChange="fnc_amount_calculation('wash',1,0);" /></td>
                                                    <td width="50"><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtTotWConsumtion_1" id="txtTotWConsumtion_1" disabled /></td>
                                                    <td width="40"><input style="width:27px;" type="text" class="text_boxes_numeric" name="txtwRate_1" id="txtwRate_1" onChange="fnc_amount_calculation('wash',1,0);" /></td>
                                                    <td width="50"><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtWValue_1" id="txtWValue_1" readonly /></td>
                                                    <td width="90"><input style="width:78px;" type="text" class="text_boxes" name="txtWashRemarks_1" id="txtWashRemarks_1" placeholder="Write" /></td>
                                                    <td><input type="button" id="increasewash_1" name="increasewash_1" style="width:30px" class="formbutton" value="+" onClick="append_row(1,1);" />
                                                <input type="button" id="decreasewash_1" name="decreasewash_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fnc_remove_row(1,1);" /></td>
                                                </tr>
                                            </tbody>
                                            <? //$w++; } ?>
                                        </table>
                                    </div>
                                </td>
                                <td>
                                    <table width="260" cellspacing="0" cellpadding="0" border="0" class="rpt_table" rules="all">
                                    	<tr>
                                        	<td colspan="3">&nbsp;</td>
                                        </tr>
                                        <tr valign="top">
                                            <td width="80"><strong>Buyer Agent</strong><input type="button" class="formbutton" style="width:15px; font-style:italic" value="N" onClick="openmypage_agent_location('requires/quick_costing_woven_controller.php?action=agent_location_popup','Create Buyer Agent',1)"/></td>
                                            <td width="80" id="agent_td"><? echo create_drop_down( "cbo_buyer_agent", 80,"select tuid, agent_location from lib_agent_location where type=1 and status_active=1 and is_deleted=0","tuid,agent_location", 1, "-Agent-", $selected, "" ); ?></td>
                                            <td width="100"><input type="button" class="formbutton" value="Approval" onClick="openmypage_style(1); "/></td>
                                        </tr>
                                        <tr>
                                            <td><strong>B. Location
                                                <input type="button" class="formbutton" style="width:15px; font-style:italic" value="N" onClick="openmypage_agent_location('requires/quick_costing_woven_controller.php?action=agent_location_popup','Create Location',2)"/></strong></td>
                                            <td id="location_td"><? echo create_drop_down( "cbo_agent_location", 80,"select tuid, agent_location from lib_agent_location where type=2 and status_active=1 and is_deleted=0","tuid,agent_location", 1, "-Location-", $selected, "" ); ?></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td><strong style="color:#F00">Margin $</strong></td>
                                            <td><input type="text" name="txtmarign" id="txtmarign" style="width:68px" class="text_boxes_numeric" value="" onChange="fnc_amount_calculation(0,0,0);" /></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td style="height:28px; font-size:25px; background:#00CED1;" onClick="fnc_fobavg_option();">F.O.B $</td>
                                            <td style="height:28px; font-size:25px; color:#FFFFFF; background:#FE00FF;" id="totalFob_td" prev_fob="" align="right" title="(Rmg Ratio*Fob (Pcs))*No Of. Pack's">&nbsp;</td>
                                            <td style="height:28px; font-size:25px; background:#00FA9A;" id="uom_td">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td><strong>No Of. Pack's</strong></td>
                                            <td><input type="text" name="txt_noOfPack" id="txt_noOfPack" style="width:68px" class="text_boxes_numeric" value="1" onChange="fnc_check_zero_val(this.value); fnc_amount_calculation(0,0,0);" /></td>
                                            <td><input type="button" name="confirm_style" id="confirm_style" value="Confirm" onClick="fnc_confirm_style();" style="width:80px" class="formbuttonplasminus" /></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" align="center" height="40" class="button_container">
                                            <? echo load_submit_buttons($permission,"fnc_qcosting_entry",0,0,"reset_fnc();",1); ?><br><input type="button" id="report_btn_1" class="formbutton" style="width:30px;" value="PNT" onClick="fnc_print_report('quick_costing_print');" />&nbsp; <input type="button" id="report_btn_2" class="formbutton" style="width:35px;" value="PNT2" onClick="fnc_print_report('quick_costing_print2');" />&nbsp; <input type="button" id="set_button" class="formbutton" style="width:60px;" value="Copy" onClick="fnc_copy_cost_sheet(0);" />&nbsp; <input type="button" id="set_button" class="formbutton" style="width:45px;" value="Revise" onClick="fnc_qcosting_entry(6);" />&nbsp; <input type="button" id="set_button" class="formbutton" style="width:45px;" value="Option" onClick="fnc_qcosting_entry(7)" />
                                            </td>
                                        </tr>
                                        <tr><td colspan="3" align="center"><strong>Merchandiser Remarks : </strong></td></tr>
                                        <tr>
                                            <td colspan="3"><textarea id="txt_costing_remarks" pre_costing_remarks="" class="text_area" style="width:250px; height:40px;" placeholder="Merchandiser Remarks">1. </textarea></td>
                                        </tr>
                                        <tr><td colspan="3"><strong>Date & Time:</strong><input style="width:45px;" type="text" class="datepicker" name="txt_meeting_date" id="txt_meeting_date" value="<? echo date('d-m-Y');?>" /><input name="txt_meeting_time" id="txt_meeting_time" class="text_boxes" type="text" style="width:30px;" placeholder="24 H. Format" onChange="fnc_valid_time(this.value,'txt_meeting_time');" onKeyUp="fnc_valid_time(this.value,'txt_meeting_time');" onKeyPress="return numOnly(this,event,this.id);" value="<? echo date('H:i', time()); ?>" /><strong>New Meeting No.</strong><input type="checkbox" name="chk_is_new_meeting" id="chk_is_new_meeting" onClick="fnc_rate_write_popup('meeting');" value="2" style="width:12px;" ></td></tr>
                                        <tr><td><strong>Meeting No:</strong></td><td align="center"><input style="width:60px;" type="text" class="text_boxes" name="txt_meeting_no" id="txt_meeting_no" disabled readonly /></td><td><input type="button" name="meeting_remarks" id="meeting_remarks" value="Meeting Minutes" onClick="fnc_meeting_remarks_pop_up(document.getElementById('txt_update_id').value, document.getElementById('txt_styleRef').value);" style="width:100px" class="formbuttonplasminus" /></td></tr>
                                        <tr><td colspan="3" align="center"><strong>Meeting Remarks:</strong></td></tr>
                                        <tr>
                                            <td colspan="3"><textarea id="txt_meeting_remarks" pre_meeting_remark="" class="text_area" style="width:250px; height:40px;" placeholder="Meeting Remarks">1. </textarea></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellspacing="2" cellpadding="0" border="0" class="rpt_table" rules="all">
                            <tr>
                                <td valign="top">
                                    <table width="250" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                                        <thead>
                                            <tr>
                                                <th width="80">Style Ref.</th>
                                                <th width="35">S.Year</th>
                                                <th width="60">Season</th>
                                                <th>Insert User</th>
                                            </tr>
                                        </thead>
                                    </table>
                                    <div style="width:250px; max-height:100px; overflow-y:scroll" id="scroll_body" > 
                                        <table width="230" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                                            <tbody id="style_td">
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="test"><input type="button" name="clear_style" id="clear_style" value="Clear" onClick="fnc_clear_all();" style="width:50px" class="formbuttonplasminus" />&nbsp;<input type="button" name="delete_style" id="delete_style" value="Clear ST" onClick="fnc_delete_style_row();" style="width:60px" class="formbuttonplasminus" /><strong> Rv: </strong><span id="revise_td"><? echo create_drop_down( "cbo_revise_no", 45, $blank_array,'', 1, "-0-",$selected, "","","","","","" ); ?></span><strong> Op: </strong><span id="option_td"><? echo create_drop_down( "cbo_option_id", 45, $blank_array,'', 1, "-0-",1, "","","","","","" ); ?></span></div>
                                   <div title="Option Reason/Remarks"><textarea id="txt_option_remarks" pre_opt_remarks="" class="text_area" style="width:240px; height:40px;" placeholder="Option Reason/Remarks"></textarea></div> 
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" id="summary_td">
                                    <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                                        <thead>
                                            <tr>
                                                <th colspan="3">Item Wise Cost Summary ($)</th>
                                            </tr>
                                            <tr>
                                                <th width="120">Description</th>
                                                <th width="70">Item Name</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Fabric</td>
                                                <td id="fab_td0" align="right"><strong>&nbsp;</strong></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td>Special Operation</td>
                                                <td id="spOpe_td0"><strong>&nbsp;</strong></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td>Wash</td>
                                                <td id="wash_td0"><strong>&nbsp;</strong></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td>Accessories</td>
                                                <td id="acc_td0"><strong>&nbsp;</strong></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td>CPM &nbsp;&nbsp;&nbsp;
                                                  <input type="checkbox" name="cmPop" id="cmPop" onClick="fnc_rate_write_popup('cm')" value="2" style="width:12px;" title="Is Calculative?" disabled /></td>
                                                <td align="center"><input name="txt_cpm_0" id="txt_cpm_0" class="text_boxes_numeric" style="width:40px;" title="CPM" placeholder="CPM" disabled /></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td>SMV / EFI</td>
                                                <td align="center"><input name="txt_smv_0" id="txt_smv_0" class="text_boxes_numeric" style="width:20px;" title="SMV" placeholder="SMV" disabled /><input name="txt_eff_0" id="txt_eff_0" class="text_boxes_numeric" style="width:20px;" title="Efficiency" placeholder="EFF" disabled /></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td>CM</td>
                                                <td align="center"><input name="txtCmCost_0" id="txtCmCost_0" class="text_boxes_numeric" style="width:40px;" onChange="" disabled /></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td>Frieght Cost</td>
                                                <td align="center"><input name="txtFriCost_0" id="txtFriCost_0" class="text_boxes_numeric" style="width:40px;" disabled /></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td>Courier Cost</td>
                                                <td align="center"><input name="txtCourierCost_0" id="txtCourierCost_0" class="text_boxes_numeric" style="width:40px;" disabled /></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td>Lab - Test</td>
                                                <td align="center"><input name="txtLtstCost_0" id="txtLtstCost_0" class="text_boxes_numeric" style="width:40px;" disabled /></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td>Insp. Cost</td>
                                                <td align="center"><input name="txtInspCost_0" id="txtInspCost_0" class="text_boxes_numeric" style="width:40px;" disabled /></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td>Opt. Exp.</td>
                                                <td align="center"><input name="txtOptExp_0" id="txtOptExp_0" class="text_boxes_numeric" style="width:40px;" disabled /></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr style="display:none">
                                                <td>Mis/Offer Qty.<input name="txt_lumSum_cost" id="txt_lumSum_cost" class="text_boxes_numeric" style="width:27px;" title="Lum Sum Cost" placeholder="L.S.C" disabled /></td>
                                                <td align="center"><input name="txtMissCost_0" id="txtMissCost_0" class="text_boxes_numeric" style="width:40px;" disabled /></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td>Other Cost</td>
                                                <td align="center"><input name="txtOtherCost_0" id="txtOtherCost_0" class="text_boxes_numeric" style="width:40px;" disabled /></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td title="Commercial Cost">Comml.                                                  
                                                <input type="text" name="txt_commlPer" id="txt_commlPer" class="text_boxes_numeric" style="width:23px;" title="Commercial Cost" placeholder="%" disabled />%</td>
                                                <td align="center"><input type="text" name="txtCommlCost_0" id="txtCommlCost_0" class="text_boxes_numeric" style="width:40px;" disabled /></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td>D. LC Cost<input type="text" name="txt_lcPer" id="txt_lcPer" value="" class="text_boxes_numeric" style="width:20px;" placeholder="%" />%</td>
                                                <td align="center"><input name="txtdlcCost_0" id="txtdlcCost_0" class="text_boxes_numeric" style="width:40px;" disabled /></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td title="Commission Cost">Com(%)                                                  
                                                <input name="txt_commPer" id="txt_commPer" class="text_boxes_numeric" style="width:20px;" title="Commission (%)" placeholder="%" disabled /></td>
                                                <td align="center"><input name="txtCommCost_0" id="txtCommCost_0" class="text_boxes_numeric" style="width:40px;" disabled /></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td>F.O.B</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td>F.O.B($/PCS)</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td>RMG (Ratio)</td>
                                                <td><input name="txtRmgQty_0" id="txtRmgQty_0" class="text_boxes_numeric" style="width:40px;" disabled /></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                        </tbody>
                                   </table>
                                </td>
                            </tr>
                        </table>
                    </td>
            	</tr>
            </table>
            </fieldset>
        </form>
        </div>
    </body>
    <script>
		jQuery("#txt_costing_remarks").keyup(function(e) 
		{
			var c = String.fromCharCode(e.which);
			var evt = (e) ? e : window.event;
			var key = (evt.keyCode) ? evt.keyCode : evt.which;
			// var key = e.keyCode;
			 //alert (key )
			if (key == 13) 
			{
				var text = $("#txt_costing_remarks").val();   
				var lines = text.split(/\r|\r\n|\n/);
				var count = (lines.length*1)+1;
				document.getElementById("txt_costing_remarks").value =document.getElementById("txt_costing_remarks").value + "\n"+count+". ";
				return false;
			}
			else {
				return true;
			}
		});
		
		jQuery("#txt_meeting_remarks").keyup(function(e) 
		{
			var c = String.fromCharCode(e.which);
			var evt = (e) ? e : window.event;
			var key = (evt.keyCode) ? evt.keyCode : evt.which;
			// var key = e.keyCode;
			 //alert (key )
			if (key == 13) 
			{
				var text = $("#txt_meeting_remarks").val();   
				var lines = text.split(/\r|\r\n|\n/);
				var count = (lines.length*1)+1;
				document.getElementById("txt_meeting_remarks").value =document.getElementById("txt_meeting_remarks").value + "\n"+count+". ";
				return false;
			}
			else {
				return true;
			}
		});
	</script>
    <script> 
		var style_id='';
		var temp_style_list=return_ajax_request_value(style_id+'__'+0, 'temp_style_list_view', 'requires/short_quotation_v6_controller');
		$('#style_td').html( temp_style_list );
		//$('#style_td').html( localStorage.getItem("temp_style_list_view") ); 
		//fnc_select(); fnc_meeting_no(0); print_report_button_setting();
    </script>

<script>
$(document).ready(function() {
		for (var property in mandatory_field_arr) {
			$("#"+mandatory_field_arr[property]).parent().prev('td').css("color", "blue");
		}
	});
</script>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>