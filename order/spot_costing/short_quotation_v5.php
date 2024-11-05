<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Short Quotation-V5 [Children Place Ltd]
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	13-08-2022
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
echo load_html_head_contents("Short Quotation-V5","../../", 1, 1, $unicode,1,'');

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

$countqcsizename=count($qcsizenamearr);

?>	
<script>

	var permission='<?=$permission; ?>';
	//alert(qcsizenamearr);
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	// Master Form-----------------------------------------------------------------------------
	var prev_item_id='';
	function fnc_select()
	{
		$(document).ready(function() {
			$("input:text").focus(function() { $(this).select(); } );
		});
	}
	
	var str_bodyPart_head = [<? //=substr(return_library_autocomplete_fromArr( $body_part ), 0, -1); ?>];
	var str_washType = [<? //=substr(return_library_autocomplete_fromArr( $emblishment_wash_type ), 0, -1); ?>];
	var str_acc= [<? //=substr(return_library_autocomplete( "select item_name from lib_item_group where status_active=1 and is_deleted=0 and item_category=4","item_name" ),0,-1); ?>];
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
				$("#txtAcctext_"+i).focus();
				return;
			}
			else
			{
				$("#txtAccId_"+i).val(itemGroupReverse[itemGroup_name]);
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
			load_drop_down( 'requires/short_quotation_v5_controller', '', 'load_drop_template_name', 'template_td');
		}
	}
	
	function fnc_template_view()
	{
		var temp_id=$("#cbo_temp_id").val();
		if(temp_id!=0)
		{
			var item_data=return_ajax_request_value(temp_id, 'load_lib_item_id', 'requires/short_quotation_v5_controller');
			var exitemdata=item_data.split("__");
			var item_id=exitemdata[0];
			if($("#cbo_temp_id").val()==0) exitemdata[1]=0;
			var itemNratio=exitemdata[2];
			//load_drop_down( 'requires/short_quotation_v5_controller', itemNratio, 'load_drop_down_tempItem', 'item_td');
			//alert(item_id);
			
			$("#txt_temp_id").val(item_id);
			var id=item_id.split(",");
			var asc_id=id; //.sort();
			//alert(asc_id); return;
			//var item_name=get_dropdown_text('cbo_temp_id');
			
			var skillsSelect = document.getElementById("cbo_temp_id");
			var selectedText = skillsSelect.options[skillsSelect.selectedIndex].text;
			
			var nameItem=selectedText.split(",");
			fnc_item_list(id+"__"+selectedText);
			//fnc_summary_dtls(asc_id+"__"+selectedText);
			//fnc_dtls_ganerate(trim(id[0])+"__"+nameItem[0]);
			//return;
			//fnc_change_data();
			//alert($("#cboItemId").val())
			//var itemDetails=get_dropdown_text('cboItemId');
			//var itemRatio=itemDetails.split("::");
			//if($("#cboItemId").val()==0) itemRatio[1]=0;
			//$("#tditemRatio").html(itemRatio[1]);
			//navigate_arrow_key();
			//fnc_specialAcc_reset();
			
			var count_td=id.length;
					
			if(count_td==1)
			{
				$('#uom_td').text('/PCS');
				$('#fobtxt').text('F.O.B($/PCS)');
			}
			else if(count_td>1)
			{
				$('#uom_td').text('/Set');
				$('#fobtxt').text('F.O.B($/Set)');
			}
			else
			{
				$('#uom_td').text('');
				$('#fobtxt').text('F.O.B($/PCS)');
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
		//Item Wise Hidden data
		//var htmlhidd="";
		//$('#td_hiddData').html('');
		
		var k=0; var bgcolor=''; var inp_cls='';
		for(var l=1; l<=gmtId.length; l++)
		{
			var itm_id=0;
			itm_id=trim(gmtId[k]);
			//htmlhidd += '<input style="width:120px;" type="hidden" class="text_boxes" name="txtfabricData_'+itm_id+'" id="txtfabricData_'+itm_id+'" /><input style="width:120px;" type="hidden" class="text_boxes" name="txtspData_'+itm_id+'" id="txtspData_'+itm_id+'" /><input style="width:120px;" type="hidden" class="text_boxes" name="txtwashData_'+itm_id+'" id="txtwashData_'+itm_id+'" /><input style="width:120px;" type="hidden" class="text_boxes" name="txtaccData_'+itm_id+'" id="txtaccData_'+itm_id+'" />';
			k++;
		}
		//alert(htmlhidd);
		//$("#td_hiddData").append(htmlhidd);
		
		//Item Wise Fabric Data
		$('#fabricitemdetails').html('');
		
		var fabhtml=""; var q=0; 
		
		for(var i=1; i<=gmtId.length; i++)
		{
			var gmtitmid=0;
			gmtitmid=trim(gmtId[q]);
			var td=5; var hd=5;
			
			fabhtml += '<tr><td class="must_entry_caption" colspan="13" align="center" bgcolor="#CCFFCC"><b>Gmts Item:'+itemNameArr[q]+'</b></td></tr>';
			
			for(var m=1; m<=hd; m++)
			{
				fabhtml += '<tr><td width="100"><input style="width:87px;" type="text" class="text_boxes" name="txtbodyparttext_'+gmtitmid+'_'+m+'" id="txtbodyparttext_'+gmtitmid+'_'+m+'" placeholder="Browse" onFocus="add_auto_complete('+"'"+gmtitmid+'_'+m+'_1'+"'"+');" onDblClick="fnc_bodyPart('+"'"+gmtitmid+'_'+m+'_1'+"'"+');" onBlur="fnc_itemwise_data_cache(); fn_getIndex('+"'"+gmtitmid+'_'+m+'_1'+"'"+',this.value);" readonly /><input style="width:50px;" type="hidden" name="txtbodypartid_'+gmtitmid+'_'+m+'" id="txtbodypartid_'+gmtitmid+'_'+m+'" readonly /><input style="width:50px;" type="hidden" name="txtfabupid_'+gmtitmid+'_'+m+'" id="txtfabupid_'+gmtitmid+'_'+m+'" readonly /></td><td width="180"><input style="width:167px;" type="text" class="text_boxes" name="txtfabdesc_'+gmtitmid+'_'+m+'" id="txtfabdesc_'+gmtitmid+'_'+m+'" readonly placeholder="Browse" onDblClick="fnc_fabric_popup('+"'"+gmtitmid+'_'+m+"'"+');" /><input style="width:50px;" type="hidden" name="txtfabid_'+gmtitmid+'_'+m+'" id="txtfabid_'+gmtitmid+'_'+m+'" readonly /></td><td width="50" align="center"><input style="width:37px;" type="text" class="text_boxes" name="txtgsm_'+gmtitmid+'_'+m+'" id="txtgsm_'+gmtitmid+'_'+m+'" /></td><td width="60" align="center"><input style="width:47px;" type="text" class="text_boxes" name="txtusefor_'+gmtitmid+'_'+m+'" id="txtusefor_'+gmtitmid+'_'+m+'" /></td><td width="60"><?=create_drop_down("cbofabuom_'+gmtitmid+'_'+m+'", 58, $unit_of_measurement,'', 1, '-select-', "0", "fnc_itemwise_data_cache();","","1,12,15,23,27"); ?></td>';
				
				for(var s=1; s<=td; s++)
				{
					fabhtml += '<td width="50" class=""><input style="width:37px;" type="text" class="text_boxes_numeric size_cons" name="txtfabconsumtion_'+gmtitmid+'_'+m+'_'+s+'" id="txtfabconsumtion_'+gmtitmid+'_'+m+'_'+s+'" placeholder="Write" onChange="fnc_amount_calculation('+"'fabric'"+','+"'"+gmtitmid+'_'+m+'_'+s+"'"+',document.getElementById('+"'txtfabrate_"+gmtitmid+"_"+m+"'"+').value);" onBlur="fnc_itemwise_data_cache();" /></td>';
				}
				fabhtml += '<td width="50"><input style="width:37px;" type="text" class="text_boxes_numeric fabric_rate" name="txtfabrate_'+gmtitmid+'_'+m+'" id="txtfabrate_'+gmtitmid+'_'+m+'" placeholder="Write" onChange="fnc_amount_calculation('+"'fabric'"+','+"'"+gmtitmid+'_'+m+'_'+0+"'"+',document.getElementById('+"'txtfabrate_"+gmtitmid+"_"+m+"'"+').value);" /></td><td width="60"><input style="width:47px;" type="text" class="text_boxes_numeric" name="txtfabvalue_'+gmtitmid+'_'+m+'" id="txtfabvalue_'+gmtitmid+'_'+m+'" placeholder="Display" readonly /></td><td><input type="button" id="decreasefab_'+gmtitmid+'_'+m+'" name="decreasefab_'+gmtitmid+'_'+m+'" style="width:30px" class="formbutton" value="-" onClick="javascript:fnc_remove_row('+"'"+gmtitmid+'_'+m+"'"+',3);" /></td></tr>';
			}
			q++;
		} 
		//alert(fabhtml);
		$("#fabricitemdetails").append(fabhtml);
		
		//fnc_itemwise_data_cache();
		set_all_onclick();
		
		fnc_select();
	}
	
	function fnc_summary_dtls(val)
	{
		var value=val.split("__");
		
		var gmtId=value[0].split(",");
		var gmtName=value[1].split(",");
		
		var a=0; var itemName_arr = new Array();
		for(var b=1; b<=gmtName.length; b++)
		{
			itemName_arr[a]=gmtName[a];
			a++;
		}
		var count_item=gmtName.length;
		var html="";
		$("#summary_td table").remove();
		html += '<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all"><thead><tr><th colspan="'+count_item+2+'">Item Wise Cost Summary ($/DZN)</th></tr><tr><th width="120">Description</th>';
		var i=0;
		for(var r=1; r<=gmtName.length; r++)
		{
			html += '<th width="70" style="word-wrap:break-word; width:70px">'+gmtName[i]+'</th>';
			i++;
		}
		
		html += '<th width="80">Total</th></tr></thead><tbody><tr><td>Yarn</td>';
		
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
        html += '<td id="totFriCst_td" align="right">&nbsp;</td></tr><tr><td>Lab - Test</td>';
		
        var o=0;
		for(var x=1; x<=gmtName.length; x++)
		{
			html += '<td align="center"><input name="txtLtstCost_[]" id="txtLtstCost_'+trim(gmtId[o])+'" value="" class="text_boxes_numeric" style="width:40px;" onChange="fnc_amount_calculation(6,'+gmtId[o]+',0);" placeholder="Write" /></td>';
			o++;
		}
        html += '<td id="totLbTstCst_td" align="right">&nbsp;</td></tr><tr><td>Opt. Exp.&nbsp;</td>';
		
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
		
        html += '<td id="totCommlCst_td" align="right">&nbsp;</td></tr><tr><td title="Commission Cost">Com.(%)<input name="txt_commPer" id="txt_commPer" value="" class="text_boxes_numeric" style="width:20px;" title="Com(%)" placeholder="%" onChange="fnc_amount_calculation(9,0,0);" /></td>';
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
		
        html += '<td id="totFOBCost_td" align="right">&nbsp;</td></tr><tr><td style="display:none">RMG(Ratio)</td>';
		
		var ac=0;
		for(var aw=1; aw<=gmtName.length; aw++)
		{
            html += '<td align="center" style="display:none"><input name="txtRmgQty_[]" id="txtRmgQty_'+trim(gmtId[ac])+'" value="" class="text_boxes_numeric" style="width:40px; display:none" onChange="fnc_amount_calculation(10,'+trim(gmtId[ac])+',0);" placeholder="Write" /></td>';
			ac++;
		}
		html += '<td id="totRmgQty_td" align="right" style="display:none">&nbsp;</td></tr><tr><td>Knitting Time</td>';
		
		var kt=0;
		for(var tk=1; tk<=gmtName.length; tk++)
		{
            html += '<td align="center"><input name="txtknittingtime_[]" id="txtknittingtime_'+trim(gmtId[kt])+'" value="" class="text_boxes_numeric" style="width:40px;" onChange="fnc_amount_calculation(13,'+trim(gmtId[kt])+',0);" placeholder="Write" /></td>';
			kt++;
		}
		html += '<td id="totknittime_td" align="right">&nbsp;</td></tr><tr><td>Make Up Time</td>';
		
		var mt=0;
		for(var tm=1; tm<=gmtName.length; tm++)
		{
            html += '<td align="center"><input name="txtmakeuptime_[]" id="txtmakeuptime_'+trim(gmtId[mt])+'" value="" class="text_boxes_numeric" style="width:40px;" onChange="fnc_amount_calculation(14,'+trim(gmtId[mt])+',0);" placeholder="Write" /></td>';
			mt++;
		}
		
		html += '<td id="totmakeuptime_td" align="right">&nbsp;</td></tr><tr><td>Finishing Time</td>';
		
		var ft=0;
		for(var tf=1; tf<=gmtName.length; tf++)
		{
            html += '<td align="center"><input name="txtfinishingtime_[]" id="txtfinishingtime_'+trim(gmtId[ft])+'" value="" class="text_boxes_numeric" style="width:40px;" onChange="fnc_amount_calculation(15,'+trim(gmtId[ft])+',0);" placeholder="Write" /></td>';
			ft++;
		}
		
		html += '<td id="totfinishtime_td" align="right">&nbsp;</td></tr></tbody></table>';
		
		$("#summary_td").append(html);
		
		fnc_select();
		navigate_arrow_key();
	}
	
	function fnc_dtls_ganerate(val)
	{
		//alert(val);
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
			if($("#txtfabricData_0").val()!="")
			{
				var txt_itemConsRateData=$("#txtfabricData_0").val();
				//alert(txt_itemConsRateData)
				var itemConsRateData=txt_itemConsRateData.split("#^");
			}
			//alert(itemConsRateData.length);
		//}
		var txt_specialData=""; var specialData="";
		if($("#txtspData_0").val()!="")
		{
			var txt_specialData=$("#txtspData_0").val();
			var specialData=txt_specialData.split("##");
		}
			//alert(specialData);
			
		var txt_washData=""; var washData="";
		if($("#txtwashData_0").val()!="")
		{
			var txt_washData=$("#txtwashData_0").val();
			var washData=txt_washData.split("##");
			var washln=washData.length-1;
		}
		else
		{
			var washln=$('#tbl_wash tbody tr').length; 
			//alert(gmtId+'qq_'+accln);
		}
		
			
		var txt_itemAccData=""; var itemAccData="";
		if($("#txtaccData_0").val()!="")
		{
			var txt_itemAccData=$("#txtaccData_0").val();
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
		
		var f=5; var q=0; var td=5;
		for(var i=1; i<=gmtId.length; i++)
		{
			var gmtitmid=0;
			gmtitmid=gmtId[q];
			for(var m=1; m<=f; m++)
			{
				$('#txtbodyparttext_'+gmtitmid+'_'+m).val('');
				$('#txtbodypartid_'+gmtitmid+'_'+m).val('');
				$('#txtfabdesc_'+gmtitmid+'_'+m).val('');
				$('#txtfabid_'+gmtitmid+'_'+m).val('');
				$('#txtgsm_'+gmtitmid+'_'+m).val('');
				$('#cbofabuom_'+gmtitmid+'_'+m).val(0);
				$('#txtusefor_'+gmtitmid+'_'+m).val('');
				$('#txtfabupid_'+gmtitmid+'_'+m).val('');
				
				for(var s=1; s<=td; s++)
				{
					$('#txtfabconsumtion_'+gmtitmid+'_'+m+'_'+s).val('');
				}
				
				$('#txtfabrate_'+gmtitmid+'_'+m).val('');
				$('#txtfabvalue_'+gmtitmid+'_'+m).val('');
			}
		}
		
		var sp_row=4;
		for(var n=1; n<=sp_row; n++)
		{
			$('#cboSpeciaOperationId_'+n).val('');
			$('#cboSpeciaTypeId_'+n).val('');
			$('#txtspbodyparttext_'+n).val('');
			$('#txtspbodypartid_'+n).val('');
			
			for(var s=1; s<=td; s++)
			{
				$('#txtspconsumtion_'+n+'_'+s).val('');
			}
			
			$('#txtSpRate_'+n).val('');
			$('#txtspValue_'+n).val('');
			$('#txtSpRemarks_'+n).val('');
		}
		
		var wash_row=washln;
		for(var o=1; o<=wash_row; o++)
		{
			//append_row(o+'_3');//'+"'"+counter+"_5'"+'
			
			$('#txtWashTypetext_'+o).val('');
			$('#txtWashTypeId_'+o).val('');
			$('#txtWbodyparttext_'+o).val('');
			$('#txtWbodypartid_'+o).val('');
			for(var s=1; s<=td; s++)
			{
				$('#txtWConsumtion_'+o+'_'+s).val('');
			}
			
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
			$('#cboconsuom_'+q).val(0);
			
			for(var s=1; s<=td; s++)
			{
				$('#txtaccconsumtion_'+q+'_'+s).val('');
			}
			
			$('#txtAccRate_'+q).val('');
			$('#txtAccValue_'+q).val('');
		}
		
		//alert(itemConsRateData)
		//var itemConsRate_fab=""; var specialData_sp="";
		var q=0;
		if(itemConsRateData!="")
		{
			var m=1;
			for(var k=1; k<=itemConsRateData.length-1; k++)
			{
				var itemConsRate_fab=itemConsRateData[q].split("~!~");
				var itemid=itemConsRate_fab[15];
				
				var bodypart=bodypartid=fabDesc=fabid=gsm=fabNbCons=fabInfantCons=fabToddlerCons=fabBiggerCons=fabBigBiggerCons=fabRate=fabVal=fabupid=""; var fabuom=0;
						
				var bodypart=itemConsRate_fab[0];
				var bodypartid=itemConsRate_fab[1];
				var fabDesc=itemConsRate_fab[2];
				var fabid=itemConsRate_fab[3];
				var gsm=itemConsRate_fab[4];
				var usefor=itemConsRate_fab[5];
				var fabuom=itemConsRate_fab[6];
				var fabNbCons=itemConsRate_fab[7];
				var fabInfantCons=itemConsRate_fab[8];
				var fabToddlerCons=itemConsRate_fab[9];
				var fabBiggerCons=itemConsRate_fab[10];
				var fabBigBiggerCons=itemConsRate_fab[11];
				var fabRate=itemConsRate_fab[12];
				var fabVal=itemConsRate_fab[13];
				var fabupid=itemConsRate_fab[14];
				
				$('#txtbodyparttext_'+itemid+'_'+m).val(bodypart);
				$('#txtbodypartid_'+itemid+'_'+m).val(bodypartid);
				$('#txtfabdesc_'+itemid+'_'+m).val(fabDesc);
				$('#txtfabid_'+itemid+'_'+m).val(fabid);
				$('#txtgsm_'+itemid+'_'+m).val(gsm);
				$('#txtusefor_'+itemid+'_'+m).val(usefor);
				$('#cbofabuom_'+itemid+'_'+m).val(fabuom);
				var td=5;
				for(var s=1; s<=td; s++)
				{
					var fabcons=0;
					if(s==1) fabcons=fabNbCons;
					else if(s==2) fabcons=fabInfantCons;
					else if(s==3) fabcons=fabToddlerCons;
					else if(s==4) fabcons=fabBiggerCons;
					else if(s==5) fabcons=fabBigBiggerCons;
					$('#txtfabconsumtion_'+itemid+'_'+m+'_'+s).val(fabcons);
				}
				
				$('#txtfabrate_'+itemid+'_'+m).val(fabRate);
				$('#txtfabvalue_'+itemid+'_'+m).val(fabVal);
				$('#txtfabupid_'+itemid+'_'+m).val(fabupid);
				
				//alert(itemid);
				m++;
				if(k==5) m=1;
				q++;
			}
		}
		
		//return;
		/*if(itemConsRateData!="")
		{
			for(var i=1; i<=gmtId.length; i++)
			{
				var gmtitmid=0; var f=5;
				gmtitmid=gmtId[q]; 
				for(var m=1; m<=f; m++)
				{
					//fnc_change_data();
					var itemConsRate_fab="";
					var itemConsRate_fab=itemConsRateData[q].split("~!~");
					var itemid=itemConsRate_fab[15];
					//alert(itemConsRateData[q]+'__'+gmtitmid+'__'+q)
					
					if(itemConsRate_fab[15]==gmtitmid)
					{
						var bodypart=bodypartid=fabDesc=fabid=gsm=fabNbCons=fabInfantCons=fabToddlerCons=fabBiggerCons=fabBigBiggerCons=fabRate=fabVal=fabupid=""; var fabuom=0;
						
						var bodypart=itemConsRate_fab[0];
						var bodypartid=itemConsRate_fab[1];
						var fabDesc=itemConsRate_fab[2];
						var fabid=itemConsRate_fab[3];
						var gsm=itemConsRate_fab[4];
						var usefor=itemConsRate_fab[5];
						var fabuom=itemConsRate_fab[6];
						var fabNbCons=itemConsRate_fab[7];
						var fabInfantCons=itemConsRate_fab[8];
						var fabToddlerCons=itemConsRate_fab[9];
						var fabBiggerCons=itemConsRate_fab[10];
						var fabBigBiggerCons=itemConsRate_fab[11];
						var fabRate=itemConsRate_fab[12];
						var fabVal=itemConsRate_fab[13];
						var fabupid=itemConsRate_fab[14];
						
						//alert(fabDesc)
						$('#txtbodyparttext_'+gmtitmid+'_'+m).val(bodypart);
						$('#txtbodypartid_'+gmtitmid+'_'+m).val(bodypartid);
						$('#txtfabdesc_'+gmtitmid+'_'+m).val(fabDesc);
						$('#txtfabid_'+gmtitmid+'_'+m).val(fabid);
						$('#txtgsm_'+gmtitmid+'_'+m).val(gsm);
						$('#txtusefor_'+gmtitmid+'_'+m).val(usefor);
						$('#cbofabuom_'+gmtitmid+'_'+m).val(fabuom);
						var td=5;
						for(var s=1; s<=td; s++)
						{
							var fabcons=0;
							if(s==1) fabcons=fabNbCons;
							else if(s==2) fabcons=fabInfantCons;
							else if(s==3) fabcons=fabToddlerCons;
							else if(s==4) fabcons=fabBiggerCons;
							else if(s==5) fabcons=fabBigBiggerCons;
							$('#txtfabconsumtion_'+gmtitmid+'_'+m+'_'+s).val(fabcons);
						}
						
						$('#txtfabrate_'+gmtitmid+'_'+m).val(fabRate);
						$('#txtfabvalue_'+gmtitmid+'_'+m).val(fabVal);
						$('#txtfabupid_'+gmtitmid+'_'+m).val(fabupid);
					}
				}
				q++;
			}
		}*/
			
		var sp=4; var q=0;
		if(specialData!="")
		{
			for(var n=1; n<=sp; n++)
			{
				var spConsRate="";
				var spConsRate=specialData[q].split("_");
				//alert(spConsRate);
				var speciaOperationId=speciaTypeId=spbodyparttext=spbodypartid=spNbCons=spInfantCons=spToddlerCons=spBiggerCons=spBigBiggerCons=spRate=spVal=spRemarks="";
				
				var speciaOperationId=spConsRate[0];
				var speciaTypeId=spConsRate[1];
				var spbodyparttext=spConsRate[2];
				var spbodypartid=spConsRate[3];
				
				var spNbCons=spConsRate[4];
				var spInfantCons=spConsRate[5];
				var spToddlerCons=spConsRate[6];
				var spBiggerCons=spConsRate[7];
				var spBigBiggerCons=spConsRate[8];
				
				var spRate=spConsRate[9];
				var spVal=spConsRate[10];
				var spRemarks=spConsRate[11];
				
				$('#cboSpeciaOperationId_'+n).val(speciaOperationId);
				$('#cboSpeciaTypeId_'+n).val(speciaTypeId);
				$('#txtspbodyparttext_'+n).val(spbodyparttext);
				$('#txtspbodypartid_'+n).val(spbodypartid);
				
				var td=5;
				for(var s=1; s<=td; s++)
				{
					var spcons=0;
					if(s==1) spcons=spNbCons;
					else if(s==2) spcons=spInfantCons;
					else if(s==3) spcons=spToddlerCons;
					else if(s==4) spcons=spBiggerCons;
					else if(s==5) spcons=spBigBiggerCons;
					$('#txtspconsumtion_'+n+'_'+s).val(spcons);
				}

				$('#txtSpRate_'+n).val(spRate);
				$('#txtspValue_'+n).val(spVal);
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
				
				var washTypetext=washTypeid=wbodyparttext=wbodypartid=wNbCons=wInfantCons=wToddlerCons=wBiggerCons=wBigBiggerCons=wRate=wVal=wRemarks="";
				
				var washTypetext=washConsRate[0];
				var washTypeid=washConsRate[1];
				var wbodyparttext=washConsRate[2];
				var wbodypartid=washConsRate[3];
				
				var wNbCons=washConsRate[4];
				var wInfantCons=washConsRate[5];
				var wToddlerCons=washConsRate[6];
				var wBiggerCons=washConsRate[7];
				var wBigBiggerCons=washConsRate[8];
				
				var wRate=washConsRate[9];
				var wVal=washConsRate[10];
				var wRemarks=washConsRate[11];
				
				$('#txtWashTypetext_'+o).val(washTypetext);
				$('#txtWashTypeId_'+o).val(washTypeid);
				$('#txtWbodyparttext_'+o).val(wbodyparttext);
				$('#txtWbodypartid_'+o).val(wbodypartid);
				
				var td=5;
				for(var s=1; s<=td; s++)
				{
					var wcons=0;
					if(s==1) wcons=wNbCons;
					else if(s==2) wcons=wInfantCons;
					else if(s==3) wcons=wToddlerCons;
					else if(s==4) wcons=wBiggerCons;
					else if(s==5) wcons=wBigBiggerCons;
					$('#txtWConsumtion_'+o+'_'+s).val(wcons);
				}
				
				$('#txtwRate_'+o).val(wRate);
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
				
					var acctext=accId=accDescription=accBandRef=acconsUom=accNbCons=accInfantCons=accToddlerCons=accBiggerCons=accBigBiggerCons=acRate=acValue="";
					
					var acctext=accData[0];
					var accId=accData[1];
					var accDescription=accData[2];
					var accBandRef=accData[3];
					var acconsUom=accData[4];
					
					var accNbCons=accData[5];
					var accInfantCons=accData[6];
					var accToddlerCons=accData[7];
					var accBiggerCons=accData[8];
					var accBigBiggerCons=accData[9];
					
					var acRate=accData[10];
					var acValue=accData[11];
					
					$('#txtAcctext_'+q).val(acctext);
					$('#txtAccId_'+q).val(accId);
					$('#txtAccDescription_'+q).val(accDescription);
					$('#txtAccBandRef_'+q).val(accBandRef);
					$('#cboconsuom_'+q).val(acconsUom);
					var td=5;
					for(var s=1; s<=td; s++)
					{
						var acccons=0;
						if(s==1) acccons=accNbCons;
						else if(s==2) acccons=accInfantCons;
						else if(s==3) acccons=accToddlerCons;
						else if(s==4) acccons=accBiggerCons;
						else if(s==5) acccons=accBigBiggerCons;
						$('#txtaccconsumtion_'+q+'_'+s).val(acccons);
					}
					
					$('#txtAccRate_'+q).val(acRate);
					$('#txtAccValue_'+q).val(acValue);
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
		if($("#cbo_temp_id").val()!=0)
		{
			var extmpData=tmpData.split(","); var packQty=0; var r=0;
			for(var s=1; s<=extmpData.length; s++)
			{
				var itemRatio=extmpData[r].split("::");
				packQty+=(itemRatio[1]*1);
				r++;
			}
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
		//var item_id=$("#cboItemId").val();
		var hid_all_item_id=$("#txt_temp_id").val();
		var all_item_id_hid=hid_all_item_id.split(",");
		//var itemRatio=$("#tditemRatio").html()*1;
		//alert(hid_all_item_id+'='+inc_id+'='+all_item_id_hid)
		var itemWiseTot_arr = new Array();
		
		if (type=='fabric')
		{
			var item_id=$("#txt_temp_id").val();
			var gmtId=item_id.split(",");
			var fabData=''; var f=5; var q=0; var td=5; var totAmt=0;
			
			/*var size_cons=0;
			var fabricitemdetailsRow = $("#fabricitemdetails").find("tr");
			$('#fabricitemdetails tr .size_cons').each(function() { 
				var abc = $(this).parents("tr").find(".size_cons").val()*1;
				var fab_rate = $(this).parents("tr").find(".fabric_rate").val()*1;
				var fab_rate_cons = (abc*fab_rate);
				//alert(fab_rate_cons);
				size_cons = size_cons + fab_rate_cons;
			});
		    alert(size_cons);
			return;*/
			
			var fabnbCost=0; var fabinfantCost=0; var fabtoddlerCost=0; var fabbiggerCost=0; var fabbigbiggerCost=0;
			
			for(var i=1; i<=gmtId.length; i++)
			{
				var gmtitmid=0;
				gmtitmid=trim(gmtId[q]);
				for(var m=1; m<=f; m++)
				{
					var rowCons=0; var rowfabamount=0;
					var fabrate=($('#txtfabrate_'+gmtitmid+'_'+m).val()*1);
					
					for(var s=1; s<=td; s++)
					{
						var rowCons=($('#txtfabconsumtion_'+gmtitmid+'_'+m+'_'+s).val()*1);
						var rowfabcost=(rowCons*fabrate)*1;
						if(s==1) fabnbCost=fabnbCost+rowfabcost;
						else if(s==2) fabinfantCost=fabinfantCost+rowfabcost;
						else if(s==3) fabtoddlerCost=fabtoddlerCost+rowfabcost;
						else if(s==4) fabbiggerCost=fabbiggerCost+rowfabcost;
						else if(s==5) fabbigbiggerCost=fabbigbiggerCost+rowfabcost;
						
						/*alert(rowCons+'_'+fabrate+'_'+rowfabcost); return; */
						rowfabamount=rowfabamount+rowfabcost;
					}
					$('#txtfabvalue_'+gmtitmid+'_'+m).val(number_format(rowfabamount,4,'.',''));
					totAmt=totAmt+rowfabamount;
				}
				q++;
			}
			
			for(var s=1; s<=td; s++)
			{
				if(s==1) $("#fabtd_"+s).text( number_format(fabnbCost,4,'.','') );
				else if(s==2) $("#fabtd_"+s).text( number_format(fabinfantCost,4,'.','') );
				else if(s==3) $("#fabtd_"+s).text( number_format(fabtoddlerCost,4,'.','') );
				else if(s==4) $("#fabtd_"+s).text( number_format(fabbiggerCost,4,'.','') );
				else if(s==5) $("#fabtd_"+s).text( number_format(fabbigbiggerCost,4,'.','') );
			}
			
			$("#totFab_td").text( number_format(totAmt,4,'.',''));
		}
		else if (type=='special')
		{
			var rowSpamount=0;  var totAmt=0;
			var spnbCost=0; var spinfantCost=0; var sptoddlerCost=0; var spbiggerCost=0; var spbigbiggerCost=0;
			for(var i=1; i<=4; i++)
			{
				var rowSpAmt=0; var rowSpamount=0; var td=5;
				var spRate=$("#txtSpRate_"+i).val()*1;
				for(var s=1; s<=td; s++)
				{
					var spCons=($("#txtspconsumtion_"+i+'_'+s).val()*1);
					var rowSpAmt=(spCons*spRate)*1;
					
					if(s==1) spnbCost=spnbCost+rowSpAmt;
					else if(s==2) spinfantCost=spinfantCost+rowSpAmt;
					else if(s==3) sptoddlerCost=sptoddlerCost+rowSpAmt;
					else if(s==4) spbiggerCost=spbiggerCost+rowSpAmt;
					else if(s==5) spbigbiggerCost=spbigbiggerCost+rowSpAmt;
					
					/*alert(rowCons+'_'+fabrate+'_'+rowfabcost); return; */
					rowSpamount=rowSpamount+rowSpAmt;
				}
				
				$("#txtspValue_"+i).val(number_format(rowSpamount,4,'.',''));
				totAmt=totAmt+rowSpamount;
			}
			
			for(var s=1; s<=td; s++)
			{
				if(s==1) $("#spOpetd_"+s).text( number_format(spnbCost,4,'.','') );
				else if(s==2) $("#spOpetd_"+s).text( number_format(spinfantCost,4,'.','') );
				else if(s==3) $("#spOpetd_"+s).text( number_format(sptoddlerCost,4,'.','') );
				else if(s==4) $("#spOpetd_"+s).text( number_format(spbiggerCost,4,'.','') );
				else if(s==5) $("#spOpetd_"+s).text( number_format(spbigbiggerCost,4,'.','') );
			}
			
			$("#totSpc_td").text( number_format(totAmt,4,'.',''));
		}
		else if (type=='wash')
		{
			var rowWashamount=0;  var totAmt=0;
			var washnbCost=0; var washinfantCost=0; var washtoddlerCost=0; var washbiggerCost=0; var washbigbiggerCost=0; 
			
			var washtr=$('#tbl_wash tbody tr').length; 
			
			for(var i=1; i<=washtr; i++)
			{
				var rowWashAmt=0; var td=5;
				var washRate=$("#txtwRate_"+i).val()*1;
				for(var s=1; s<=td; s++)
				{
					var washCons=($("#txtWConsumtion_"+i+'_'+s).val()*1);
					
					var washAmt=(washCons*washRate)*1;
					
					if(s==1) washnbCost=washnbCost+washAmt;
					else if(s==2) washinfantCost=washinfantCost+washAmt;
					else if(s==3) washtoddlerCost=washtoddlerCost+washAmt;
					else if(s==4) washbiggerCost=washbiggerCost+washAmt;
					else if(s==5) washbigbiggerCost=washbigbiggerCost+washAmt;
					
					/*alert(rowCons+'_'+fabrate+'_'+rowfabcost); return; */
					rowWashAmt=rowWashAmt+washAmt;
				}
				
				var washamount=washCons*washRate;
				$("#txtWValue_"+i).val(number_format(rowWashAmt,4,'.',''));
				totAmt=totAmt+rowWashAmt;
			}
			
			for(var s=1; s<=td; s++)
			{
				if(s==1) $("#washtd_"+s).text( number_format(washnbCost,4,'.','') );
				else if(s==2) $("#washtd_"+s).text( number_format(washinfantCost,4,'.','') );
				else if(s==3) $("#washtd_"+s).text( number_format(washtoddlerCost,4,'.','') );
				else if(s==4) $("#washtd_"+s).text( number_format(washbiggerCost,4,'.','') );
				else if(s==5) $("#washtd_"+s).text( number_format(washbigbiggerCost,4,'.','') );
			}
			
			$("#totWash_td").text( number_format(totAmt,4,'.',''));
		}
		else if (type=='accessories')
		{
			var rowAccamount=0;  var totAmt=0;
			var accnbCost=0; var accinfantCost=0; var acctoddlerCost=0; var accbiggerCost=0; var accbigbiggerCost=0;
			
			var acctr=$('#tbl_acc tbody tr').length;
			
			for(var i=1; i<=acctr; i++)
			{
				var rowAccAmt=0; var td=5;
				var accRate=$("#txtAccRate_"+i).val()*1;
				for(var s=1; s<=td; s++)
				{
					var accCons=($("#txtaccconsumtion_"+i+'_'+s).val()*1);
					var accAmt=(accCons*accRate)*1;
					
					if(s==1) accnbCost=accnbCost+accAmt;
					else if(s==2) accinfantCost=accinfantCost+accAmt;
					else if(s==3) acctoddlerCost=acctoddlerCost+accAmt;
					else if(s==4) accbiggerCost=accbiggerCost+accAmt;
					else if(s==5) accbigbiggerCost=accbigbiggerCost+accAmt;
					
					/*alert(rowCons+'_'+fabrate+'_'+rowfabcost); return; */
					rowAccAmt=rowAccAmt+accAmt;
				}
				
				$("#txtAccValue_"+i).val(number_format(rowAccAmt,4,'.',''));
				totAmt=totAmt+rowAccAmt;
			}
			
			for(var s=1; s<=td; s++)
			{
				if(s==1) $("#acctd_"+s).text( number_format(accnbCost,4,'.','') );
				else if(s==2) $("#acctd_"+s).text( number_format(accinfantCost,4,'.','') );
				else if(s==3) $("#acctd_"+s).text( number_format(acctoddlerCost,4,'.','') );
				else if(s==4) $("#acctd_"+s).text( number_format(accbiggerCost,4,'.','') );
				else if(s==5) $("#acctd_"+s).text( number_format(accbigbiggerCost,4,'.','') );
			}
			$("#totAcc_td").text( number_format(totAmt,4,'.',''));
			//alert(accCons+'-'+accRate+'-'+accamount+'-'+totAmt)
		}
		else if (type=='3')
		{
			var cc=1;  var cpm_from_financial_parameter=0; 
			if($("#cmPop").val()==1)
			{
				var costing_date=$("#txt_costingDate").val();
				if(cc==1)
				{
					//var cpm_from_financial_parameter=return_ajax_request_value(costing_date, 'cpm_check_load', 'requires/short_quotation_v5_controller');
					cc++;
				}
				
				var exchange_rate=($("#txt_exchangeRate").val()*1); var td=5;
				for(var s=1; s<=td; s++)
				{
					var cpm=($('#txtcpm_'+s).val()*1);
					var smv=($('#txtsmv_'+s).val()*1);
					var eff=($('#txteff_'+s).val()*1);
					var cm_cost=(((cpm*100)/eff)*smv)*1;
					$("#txtCmCost_"+s).val( number_format(cm_cost,4,'.',''));
				}
				
				//var cm_cost=((smv*cpm_from_financial_parameter)*12+(smv*cpm_from_financial_parameter*12)*eff)/exchange_rate;
				/*var cm_cost=(((cpm*100)/eff)*smv)*itemRatio;
				$("#txtCmCost_"+inc_id).val( number_format(cm_cost,4,'.',''));*/
				fnc_amount_calculation(4,0,0)
			}
		}
		else if (type=='4')
		{
			var cmnbCost=0; var cminfantCost=0; var cmtoddlerCost=0; var cmbiggerCost=0; var cmbigbiggerCost=0;
			var rowCmCost=0; var td=5;
			
			for(var s=1; s<=td; s++)
			{
				var cmcost=($("#txtCmCost_"+s).val()*1);
				
				/*if(s==1) cmnbCost=cmnbCost+cmcost;
				else if(s==2) cminfantCost=cminfantCost+cmcost;
				else if(s==3) cmtoddlerCost=cmtoddlerCost+cmcost;
				else if(s==4) cmbiggerCost=cmbiggerCost+cmcost;
				else if(s==5) cmbigbiggerCost=cmbigbiggerCost+cmcost;*/
				
				/*alert(rowCons+'_'+fabrate+'_'+rowfabcost); return; */
				rowCmCost=rowCmCost+cmcost;
			}
			$("#totCm_td").text( number_format(rowCmCost,4,'.',''));
			
		}
		else if (type=='5')
		{
			var rowFriCost=0; var td=5;
			for(var s=1; s<=td; s++)
			{
				var fricost=($("#txtFriCost_"+s).val()*1);
				rowFriCost=rowFriCost+fricost;
			}
			
			$("#totFriCst_td").text( number_format(rowFriCost,4,'.',''));
		}
		else if (type=='6')
		{
			var rowLabCost=0; var td=5;
			for(var s=1; s<=td; s++)
			{
				var labcost=($("#txtLtstCost_"+s).val()*1);
				rowLabCost=rowLabCost+labcost;
			}
			
			$("#totLbTstCst_td").text( number_format(rowLabCost,4,'.',''));
		}
		else if (type=='7')
		{
			/*var lum_sum_cost=($("#txt_lumSum_cost").val()*1);
			var offer_qty=($("#txt_offerQty").val()*1);
			if($("#txt_offerQty").val()!="")
			{
				var item_wise_miss_cost=(lum_sum_cost/offer_qty)*12;
				
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
			}*/
		}
		else if (type=='8')
		{
			var rowOthCost=0; var td=5;
			for(var s=1; s<=td; s++)
			{
				var othercost=($("#txtOtherCost_"+s).val()*1);
				rowOthCost=rowOthCost+othercost;
			}
			
			$("#totOtherCst_td").text( number_format(rowOthCost,4,'.',''));
		}
		else if (type=='9')
		{
			var td=5; var commiBnbCost=0; var commiBinfantCost=0; var commiBtoddlerCost=0; var commiBbiggerCost=0; var commiBbigbiggerCost=0; var totAmt=0;
			var commPer=($("#txt_commPer").val()*1)/100;
			for(var s=1; s<=td; s++)
			{
				if(s==1) 
				{
					var before_commission_cost=($("#fabtd_"+s).text()*1)+($("#spOpetd_"+s).text()*1)+($("#washtd_"+s).text()*1)+($("#acctd_"+s).text()*1)+($("#txtCmCost_"+s).val()*1)+($("#txtFriCost_"+s).val()*1)+($("#txtLtstCost_"+s).val()*1)+($("#txtOtherCost_"+s).val()*1)+($("#txtCommlCost_"+s).val()*1)+($("#txtOptExp_"+s).val()*1);
					
					var commissson_cost=((before_commission_cost/(1-commPer))-before_commission_cost);
					$("#txtCommCost_"+s).val( number_format(commissson_cost,4,'.','') );
					
					commiBnbCost=commiBnbCost+commissson_cost;
				}
				else if(s==2) 
				{
					var before_commission_cost=($("#fabtd_"+s).text()*1)+($("#spOpetd_"+s).text()*1)+($("#washtd_"+s).text()*1)+($("#acctd_"+s).text()*1)+($("#txtCmCost_"+s).val()*1)+($("#txtFriCost_"+s).val()*1)+($("#txtLtstCost_"+s).val()*1)+($("#txtOtherCost_"+s).val()*1)+($("#txtCommlCost_"+s).val()*1)+($("#txtOptExp_"+s).val()*1);
					
					var commissson_cost=((before_commission_cost/(1-commPer))-before_commission_cost);
					$("#txtCommCost_"+s).val( number_format(commissson_cost,4,'.','') );
					
					commiBinfantCost=commiBinfantCost+commissson_cost;
				}
				else if(s==3) 
				{
					var before_commission_cost=($("#fabtd_"+s).text()*1)+($("#spOpetd_"+s).text()*1)+($("#washtd_"+s).text()*1)+($("#acctd_"+s).text()*1)+($("#txtCmCost_"+s).val()*1)+($("#txtFriCost_"+s).val()*1)+($("#txtLtstCost_"+s).val()*1)+($("#txtOtherCost_"+s).val()*1)+($("#txtCommlCost_"+s).val()*1)+($("#txtOptExp_"+s).val()*1);
					
					var commissson_cost=((before_commission_cost/(1-commPer))-before_commission_cost);
					$("#txtCommCost_"+s).val( number_format(commissson_cost,4,'.','') );
					
					commiBtoddlerCost=commiBtoddlerCost+commissson_cost;
				}
				else if(s==4) 
				{
					var before_commission_cost=($("#fabtd_"+s).text()*1)+($("#spOpetd_"+s).text()*1)+($("#washtd_"+s).text()*1)+($("#acctd_"+s).text()*1)+($("#txtCmCost_"+s).val()*1)+($("#txtFriCost_"+s).val()*1)+($("#txtLtstCost_"+s).val()*1)+($("#txtOtherCost_"+s).val()*1)+($("#txtCommlCost_"+s).val()*1)+($("#txtOptExp_"+s).val()*1);
					
					var commissson_cost=((before_commission_cost/(1-commPer))-before_commission_cost);
					$("#txtCommCost_"+s).val( number_format(commissson_cost,4,'.','') );
					
					commiBbiggerCost=commiBbiggerCost+commissson_cost;
				}
				else if(s==5) 
				{
					var before_commission_cost=($("#fabtd_"+s).text()*1)+($("#spOpetd_"+s).text()*1)+($("#washtd_"+s).text()*1)+($("#acctd_"+s).text()*1)+($("#txtCmCost_"+s).val()*1)+($("#txtFriCost_"+s).val()*1)+($("#txtLtstCost_"+s).val()*1)+($("#txtOtherCost_"+s).val()*1)+($("#txtCommlCost_"+s).val()*1)+($("#txtOptExp_"+s).val()*1);
					
					var commissson_cost=((before_commission_cost/(1-commPer))-before_commission_cost);
					$("#txtCommCost_"+s).val( number_format(commissson_cost,4,'.','') );
					
					commiBbigbiggerCost=commiBbigbiggerCost+commissson_cost;
				}
				totAmt=totAmt+commissson_cost;
			}
			
			$("#totCommCst_td").text( number_format(totAmt,4,'.',''));
		}
		else if (type=='10')
		{
			/*var aw=0; var row_rmgQty=0;
			for(var aq=1; aq<=all_item_id_hid.length; aq++)
			{
				row_rmgQty=row_rmgQty+($("#txtRmgQty_"+trim(all_item_id_hid[aw])).val()*1);
				aw++;
			}
			$("#totRmgQty_td").text(row_rmgQty);*/
		}
		else if (type=='11')
		{
			var commlPer=($("#txt_commlPer").val()*1)/100;
			
			var td=5; var commlnbCost=0; var commlinfantCost=0; var commltoddlerCost=0; var commlbiggerCost=0; var commlbigbiggerCost=0; var totAmt=0;
			for(var s=1; s<=td; s++)
			{
				if(s==1) 
				{
					var before_commercial_cost=($("#fabtd_"+s).text()*1)+($("#spOpetd_"+s).text()*1)+($("#washtd_"+s).text()*1)+($("#acctd_"+s).text()*1)+($("#txtCmCost_"+s).val()*1)+($("#txtFriCost_"+s).val()*1)+($("#txtLtstCost_"+s).val()*1)+($("#txtOtherCost_"+s).val()*1)+($("#txtOptExp_"+s).val()*1);
					
					var commercial_cost=((before_commercial_cost/(1-commlPer))-before_commercial_cost);
					$("#txtCommlCost_"+s).val( number_format(commercial_cost,4,'.','') );
					
					commlnbCost=commlnbCost+commercial_cost;
				}
				else if(s==2) 
				{
					var before_commercial_cost=($("#fabtd_"+s).text()*1)+($("#spOpetd_"+s).text()*1)+($("#washtd_"+s).text()*1)+($("#acctd_"+s).text()*1)+($("#txtCmCost_"+s).val()*1)+($("#txtFriCost_"+s).val()*1)+($("#txtLtstCost_"+s).val()*1)+($("#txtOtherCost_"+s).val()*1)+($("#txtOptExp_"+s).val()*1);
					
					var commercial_cost=((before_commercial_cost/(1-commlPer))-before_commercial_cost);
					$("#txtCommlCost_"+s).val( number_format(commercial_cost,4,'.','') );
					
					commlinfantCost=commlinfantCost+commercial_cost;
				}
				else if(s==3) 
				{
					var before_commercial_cost=($("#fabtd_"+s).text()*1)+($("#spOpetd_"+s).text()*1)+($("#washtd_"+s).text()*1)+($("#acctd_"+s).text()*1)+($("#txtCmCost_"+s).val()*1)+($("#txtFriCost_"+s).val()*1)+($("#txtLtstCost_"+s).val()*1)+($("#txtOtherCost_"+s).val()*1)+($("#txtOptExp_"+s).val()*1);
					
					var commercial_cost=((before_commercial_cost/(1-commlPer))-before_commercial_cost);
					$("#txtCommlCost_"+s).val( number_format(commercial_cost,4,'.','') );
					
					commltoddlerCost=commltoddlerCost+commercial_cost;
				}
				else if(s==4) 
				{
					var before_commercial_cost=($("#fabtd_"+s).text()*1)+($("#spOpetd_"+s).text()*1)+($("#washtd_"+s).text()*1)+($("#acctd_"+s).text()*1)+($("#txtCmCost_"+s).val()*1)+($("#txtFriCost_"+s).val()*1)+($("#txtLtstCost_"+s).val()*1)+($("#txtOtherCost_"+s).val()*1)+($("#txtOptExp_"+s).val()*1);
					
					var commercial_cost=((before_commercial_cost/(1-commlPer))-before_commercial_cost);
					$("#txtCommlCost_"+s).val( number_format(commercial_cost,4,'.','') );
					
					commlbiggerCost=commlbiggerCost+commercial_cost;
				}
				else if(s==5) 
				{
					var before_commercial_cost=($("#fabtd_"+s).text()*1)+($("#spOpetd_"+s).text()*1)+($("#washtd_"+s).text()*1)+($("#acctd_"+s).text()*1)+($("#txtCmCost_"+s).val()*1)+($("#txtFriCost_"+s).val()*1)+($("#txtLtstCost_"+s).val()*1)+($("#txtOtherCost_"+s).val()*1)+($("#txtOptExp_"+s).val()*1);
					
					var commercial_cost=((before_commercial_cost/(1-commlPer))-before_commercial_cost);
					$("#txtCommlCost_"+s).val( number_format(commercial_cost,4,'.','') );
					
					commlbigbiggerCost=commlbigbiggerCost+commercial_cost;
				}
				totAmt=totAmt+commercial_cost;
			}
			$("#totCommlCst_td").text(number_format(totAmt,4,'.',''));
		}
		else if (type=='12')
		{
			var optexpnbCost=0; var optexpinfantCost=0; var optexptoddlerCost=0; var optexpbiggerCost=0; var optexpbigbiggerCost=0;
			var rowoptexpCost=0; var td=5;
			
			for(var s=1; s<=td; s++)
			{
				var optexpcost=($("#txtOptExp_"+s).val()*1);
				
				/*if(s==1) cmnbCost=cmnbCost+cmcost;
				else if(s==2) cminfantCost=cminfantCost+cmcost;
				else if(s==3) cmtoddlerCost=cmtoddlerCost+cmcost;
				else if(s==4) cmbiggerCost=cmbiggerCost+cmcost;
				else if(s==5) cmbigbiggerCost=cmbigbiggerCost+cmcost;*/
				
				/*alert(rowCons+'_'+fabrate+'_'+rowfabcost); return; */
				rowoptexpCost=rowoptexpCost+cmcost;
			}
			$("#totOptExp_td").text( number_format(rowoptexpCost,4,'.',''));
		}
		
		var td=5; var totAmt=0;
		var commlPer=($("#txt_commlPer").val()*1)/100;
		var commPer=($("#txt_commPer").val()*1)/100;
		for(var s=1; s<=td; s++)
		{
			if(s==1) 
			{
				var before_commercial_cost=($("#fabtd_"+s).text()*1)+($("#spOpetd_"+s).text()*1)+($("#washtd_"+s).text()*1)+($("#acctd_"+s).text()*1)+($("#txtCmCost_"+s).val()*1)+($("#txtFriCost_"+s).val()*1)+($("#txtLtstCost_"+s).val()*1)+($("#txtOtherCost_"+s).val()*1)+($("#txtOptExp_"+s).val()*1);
				
				var commercial_cost=((before_commercial_cost/(1-commlPer))-before_commercial_cost);
				$("#txtCommlCost_"+s).val( number_format(commercial_cost,4,'.','') );
				
				var before_commission_cost=before_commercial_cost+commercial_cost;
				var commissson_cost=((before_commission_cost/(1-commPer))-before_commission_cost);
				$("#txtCommCost_"+s).val( number_format(commissson_cost,4,'.','') );
				
				var totsizeCost=before_commission_cost+commissson_cost;
				$("#fobTtd_"+s).text( number_format(totsizeCost,4,'.',''));
				$("#fobPcsTtd_"+s).text( number_format(totsizeCost/12,4,'.',''));
				
				commlnbCost=commlnbCost+commercial_cost;
			}
			else if(s==2) 
			{
				var before_commercial_cost=($("#fabtd_"+s).text()*1)+($("#spOpetd_"+s).text()*1)+($("#washtd_"+s).text()*1)+($("#acctd_"+s).text()*1)+($("#txtCmCost_"+s).val()*1)+($("#txtFriCost_"+s).val()*1)+($("#txtLtstCost_"+s).val()*1)+($("#txtOtherCost_"+s).val()*1)+($("#txtOptExp_"+s).val()*1);
				
				var commercial_cost=((before_commercial_cost/(1-commlPer))-before_commercial_cost);
				$("#txtCommlCost_"+s).val( number_format(commercial_cost,4,'.','') );
				
				var before_commission_cost=before_commercial_cost+commercial_cost;
				var commissson_cost=((before_commission_cost/(1-commPer))-before_commission_cost);
				$("#txtCommCost_"+s).val( number_format(commissson_cost,4,'.','') );
				
				var totsizeCost=before_commission_cost+commissson_cost;
				$("#fobTtd_"+s).text( number_format(totsizeCost,4,'.',''));
				$("#fobPcsTtd_"+s).text( number_format(totsizeCost/12,4,'.',''));
				
				commlinfantCost=commlinfantCost+commercial_cost;
			}
			else if(s==3) 
			{
				var before_commercial_cost=($("#fabtd_"+s).text()*1)+($("#spOpetd_"+s).text()*1)+($("#washtd_"+s).text()*1)+($("#acctd_"+s).text()*1)+($("#txtCmCost_"+s).val()*1)+($("#txtFriCost_"+s).val()*1)+($("#txtLtstCost_"+s).val()*1)+($("#txtOtherCost_"+s).val()*1)+($("#txtOptExp_"+s).val()*1);
				
				var commercial_cost=((before_commercial_cost/(1-commlPer))-before_commercial_cost);
				$("#txtCommlCost_"+s).val( number_format(commercial_cost,4,'.','') );
				
				var before_commission_cost=before_commercial_cost+commercial_cost;
				var commissson_cost=((before_commission_cost/(1-commPer))-before_commission_cost);
				$("#txtCommCost_"+s).val( number_format(commissson_cost,4,'.','') );
				
				var totsizeCost=before_commission_cost+commissson_cost;
				$("#fobTtd_"+s).text( number_format(totsizeCost,4,'.',''));
				$("#fobPcsTtd_"+s).text( number_format(totsizeCost/12,4,'.',''));
				
				commltoddlerCost=commltoddlerCost+commercial_cost;
			}
			else if(s==4) 
			{
				var before_commercial_cost=($("#fabtd_"+s).text()*1)+($("#spOpetd_"+s).text()*1)+($("#washtd_"+s).text()*1)+($("#acctd_"+s).text()*1)+($("#txtCmCost_"+s).val()*1)+($("#txtFriCost_"+s).val()*1)+($("#txtLtstCost_"+s).val()*1)+($("#txtOtherCost_"+s).val()*1)+($("#txtOptExp_"+s).val()*1);
				
				var commercial_cost=((before_commercial_cost/(1-commlPer))-before_commercial_cost);
				$("#txtCommlCost_"+s).val( number_format(commercial_cost,4,'.','') );
				
				var before_commission_cost=before_commercial_cost+commercial_cost;
				var commissson_cost=((before_commission_cost/(1-commPer))-before_commission_cost);
				$("#txtCommCost_"+s).val( number_format(commissson_cost,4,'.','') );
				
				var totsizeCost=before_commission_cost+commissson_cost;
				$("#fobTtd_"+s).text( number_format(totsizeCost,4,'.',''));
				$("#fobPcsTtd_"+s).text( number_format(totsizeCost/12,4,'.',''));
				
				commlbiggerCost=commlbiggerCost+commercial_cost;
			}
			else if(s==5) 
			{
				var before_commercial_cost=($("#fabtd_"+s).text()*1)+($("#spOpetd_"+s).text()*1)+($("#washtd_"+s).text()*1)+($("#acctd_"+s).text()*1)+($("#txtCmCost_"+s).val()*1)+($("#txtFriCost_"+s).val()*1)+($("#txtLtstCost_"+s).val()*1)+($("#txtOtherCost_"+s).val()*1)+($("#txtOptExp_"+s).val()*1);
				
				var commercial_cost=((before_commercial_cost/(1-commlPer))-before_commercial_cost);
				$("#txtCommlCost_"+s).val( number_format(commercial_cost,4,'.','') );
				
				var before_commission_cost=before_commercial_cost+commercial_cost;
				var commissson_cost=((before_commission_cost/(1-commPer))-before_commission_cost);
				$("#txtCommCost_"+s).val( number_format(commissson_cost,4,'.','') );
				
				var totsizeCost=before_commission_cost+commissson_cost;
				$("#fobTtd_"+s).text( number_format(totsizeCost,4,'.',''));
				$("#fobPcsTtd_"+s).text( number_format(totsizeCost/12,4,'.',''));
				
				commlbigbiggerCost=commlbigbiggerCost+commercial_cost;
			}
			totAmt=totAmt+totsizeCost;
		}
		$("#totCost_td").text(number_format(totAmt,4,'.',''));
		$("#totFOBCost_td").text(number_format(totAmt/12,4,'.',''));
		
		var tot_fob_cost=(totAmt*($("#txt_noOfPack").val()*1))+($("#txtmarign").val()*1);
		$("#totalFob_td").text( number_format((tot_fob_cost),4,'.','') );
		
		fnc_itemwise_data_cache();
	}
	
	function fnc_meeting_remarks_pop_up(costSheetId,styleRef)
	{
		var title = 'Meeting Remarks Form';	
		var page_link = 'requires/short_quotation_v5_controller.php?action=meeting_remarks_popup';
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
			var gmtId=gmtsItem_id.split(","); var td=5;
			if(document.getElementById('cmPop').checked==true)
			{
				document.getElementById('cmPop').value=1;
				var k=0; 
				for(var y=1; y<=td; y++)
				{
					$('#txtcpm_'+y).removeAttr('disabled','disabled');
					$('#txtsmv_'+y).removeAttr('disabled','disabled');
					$('#txteff_'+y).removeAttr('disabled','disabled');
					
					$('#txtCmCost_'+y).val('');
					$('#txtCmCost_'+y).attr("placeholder", "Cal.");
					$('#txtCmCost_'+y).attr("readonly", "readonly");
					k++;
				}
				
				//=((SMV*CPM)*Costing per + (SMV*CPM*Costing per)* Efficiency Wastage%)/Exchange Rate
			}
			else if(document.getElementById('cmPop').checked==false)
			{
				document.getElementById('cmPop').value=2;
				var j=0;
				for(var u=1; u<=td; u++)
				{
					$('#txtcpm_'+u).attr('disabled','disabled');
					$('#txtsmv_'+u).attr('disabled','disabled');
					$('#txteff_'+u).attr('disabled','disabled');
					/*$('#txt_cpm_'+trim(gmtId[j])).val('');
					$('#txt_smv_'+trim(gmtId[j])).val('');
					$('#txt_eff_'+trim(gmtId[j])).val('');*/
					
					$('#txtCmCost_'+u).val('');
					$('#txtCmCost_'+u).attr("placeholder", "Write");
					$('#txtCmCost_'+u).removeAttr("readonly", "readonly");
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
					
					$('#txt_consumtion'+inc_id).val('');
					$('#txt_exper'+inc_id).val('');
					$('#txt_totConsumtion'+inc_id).val('');
					$('#txt_rate'+inc_id).val('');
					$('#txtRateData_'+inc_id).val('');
					$('#txt_consumtion'+inc_id).attr('readonly','readonly');
					$('#txt_exper'+inc_id).attr('readonly','readonly');
					$('#txt_rate'+inc_id).attr('readonly','readonly');
					$('#txtRateData_'+inc_id).attr('readonly','readonly');
					$('#txt_consumtion'+inc_id).attr("placeholder","Browse");
					$('#txt_exper'+inc_id).attr("placeholder","Display");
					//$('#txt_rate'+inc_id).attr("placeholder", "Browse");
					$('#txt_consumtion'+inc_id).removeAttr("onDblClick").attr("onDblClick","fnc_openmypage_rate("+inc_id+",'1');");
				}
				else if(document.getElementById('ratePop_'+inc_id).checked==false)
				{
					document.getElementById('ratePop_'+inc_id).value=2;
					
					$('#txt_consumtion'+inc_id).val('');
					$('#txt_exper'+inc_id).val('');
					$('#txt_totConsumtion'+inc_id).val('');
					$('#txt_rate'+inc_id).val('');
					$('#txtRateData_'+inc_id).val('');
					$('#txt_consumtion'+inc_id).removeAttr('readonly','readonly');
					$('#txt_exper'+inc_id).removeAttr('readonly','readonly');
					$('#txt_rate'+inc_id).removeAttr('readonly','readonly');
					$('#txt_consumtion'+inc_id).attr("placeholder","Write");
					$('#txt_exper'+inc_id).attr("placeholder","Write");
					$('#txt_rate'+inc_id).attr("placeholder", "Write");
					$('#txt_consumtion'+inc_id).removeAttr("onDblClick");
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
		/*if (qc_no=="")
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
		}*/	
		if(popuptype==1)
		{
			var packQty=$('#tdpackQty').text();
			var itemRatio=$('#tditemRatio').text();
			var rateData=$('#txtRateData_'+inc_id).val();
			var fabuom=$('#cbofabuom_'+inc_id).val();
			
			var title = 'Fab. Cons. Details PopUp';	
			var page_link = 'requires/short_quotation_v5_controller.php?action=rate_details_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&qc_no='+qc_no+'&bodypartid='+bodypartid+'&consRateId='+consRateId+'&packQty='+packQty+'&itemRatio='+itemRatio+'&fabuom='+fabuom+'&rateData='+rateData, title, 'width=850px,height=380px,center=1,resize=0,scrolling=0','../')
			release_freezing();
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var txtdatastr=this.contentDoc.getElementById("txtdatastr").value;
				var split_txtdatastr=txtdatastr.split('~~');
				
				//$('#txtdznyds_'+inc_id).val( totconsyds );
				var totconsexper=(split_txtdatastr[3]*1)+(split_txtdatastr[4]*1);
				//alert(split_txtdatastr[2]+'__'+totconsexper+'__'+split_txtdatastr[7])
				$('#txt_consumtion'+inc_id).val( split_txtdatastr[2] );
				$('#txt_exper'+inc_id).val( totconsexper );
				$('#txt_totConsumtion'+inc_id).val( split_txtdatastr[7] );
				$('#txtRateData_'+inc_id).val( txtdatastr );
				fnc_amount_calculation('fabric', inc_id, '');
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
			var page_link = 'requires/short_quotation_v5_controller.php?action=accrate_details_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&qc_no='+qc_no+'&acctext='+acctext+'&accuom='+accuom+'&accRateData='+accRateData+'&itemRatio='+itemRatio+'&process_loss_method_id='+process_loss_method_id, title, 'width=650px,height=380px,center=1,resize=0,scrolling=0','../')
			release_freezing();
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var totconsdzn=this.contentDoc.getElementById("txttotcons_dzn").value;
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
		var page_link = 'requires/short_quotation_v5_controller.php?action=stage_saveUpdate_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=600px,height=380px,center=1,resize=0,scrolling=0','../')
		release_freezing();
		emailwindow.onclose=function()
		{
			load_drop_down( 'requires/short_quotation_v5_controller', '', 'load_drop_stage_name', 'stage_td');
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
		var item_id=$("#txt_temp_id").val();
		var gmtId=item_id.split(",");
		var fabData=''; var q=0; var td=5;
		for(var i=1; i<=gmtId.length; i++)
		{
			var gmtitmid=0; var f=5;
			gmtitmid=trim(gmtId[q]); 
			for(var m=1; m<=f; m++)
			{
				fabData+=($('#txtbodyparttext_'+gmtitmid+'_'+m).val())+'~!~'+($('#txtbodypartid_'+gmtitmid+'_'+m).val()*1)+'~!~'+($('#txtfabdesc_'+gmtitmid+'_'+m).val())+'~!~'+($('#txtfabid_'+gmtitmid+'_'+m).val())+'~!~'+($('#txtgsm_'+gmtitmid+'_'+m).val())+'~!~'+($('#txtusefor_'+gmtitmid+'_'+m).val())+'~!~'+($('#cbofabuom_'+gmtitmid+'_'+m).val()*1)+'~!~';
				for(var s=1; s<=td; s++)
				{
					fabData+=($('#txtfabconsumtion_'+gmtitmid+'_'+m+'_'+s).val()*1)+'~!~';
				}
				fabData+=($('#txtfabrate_'+gmtitmid+'_'+m).val()*1)+'~!~'+($('#txtfabvalue_'+gmtitmid+'_'+m).val()*1)+'~!~'+($('#txtfabupid_'+gmtitmid+'_'+m).val()*1)+'~!~'+gmtitmid+'#^';
			}
			q++;
		}
		
		
		//alert(fabData);
		
		var sp_row=4; var specialData='';
		for(var n=1; n<=sp_row; n++)
		{
			specialData+= $('#cboSpeciaOperationId_'+n).val()+'_'+($('#cboSpeciaTypeId_'+n).val()*1)+'_'+($('#txtspbodyparttext_'+n).val())+'_'+($('#txtspbodypartid_'+n).val()*1)+'_';
			for(var s=1; s<=td; s++)
			{
				specialData+=($('#txtspconsumtion_'+n+'_'+s).val()*1)+'_';
			}
			specialData+=($('#txtSpRate_'+n).val()*1)+'_'+($('#txtspValue_'+n).val()*1)+'_'+($('#txtSpRemarks_'+n).val())+'##';
		}
		
		var wash_row=$('#tbl_wash tbody tr').length; var washData='';
		for(var o=1; o<=wash_row; o++)
		{
			washData+= $('#txtWashTypetext_'+o).val()+'_'+($('#txtWashTypeId_'+o).val()*1)+'_'+($('#txtWbodyparttext_'+o).val())+'_'+($('#txtWbodypartid_'+o).val()*1)+'_'
			
			for(var s=1; s<=td; s++)
			{
				washData+=($('#txtWConsumtion_'+o+'_'+s).val()*1)+'_';
			}
			washData+=($('#txtwRate_'+o).val()*1)+'_'+($('#txtWValue_'+o).val()*1)+'_'+($('#txtWashRemarks_'+o).val())+'##';
		}
		// alert( item_id_tmp+'=='+item_id );
		var acc=$('#tbl_acc tbody tr').length; var accessoriesData="";
		for(var q=1; q<=acc; q++)
		{
			accessoriesData+= ($('#txtAcctext_'+q).val())+'~!~'+($('#txtAccId_'+q).val()*1)+'~!~'+($('#txtAccDescription_'+q).val())+'~!~'+($('#txtAccBandRef_'+q).val())+'~!~'+($('#cboconsuom_'+q).val()*1)+'~!~';
			for(var s=1; s<=td; s++)
			{
				accessoriesData+=($('#txtaccconsumtion_'+q+'_'+s).val()*1)+'~!~';
			}
			
			accessoriesData+=($('#txtAccRate_'+q).val()*1)+'~!~'+($('#txtAccValue_'+q).val()*1)+'#^';
		}
		//all_data_string=fabData+****+specialData+****+washData+****+accessoriesData;
		//alert(cons_rate_data);
		
		$('#txtfabricData_0').val( fabData ); 
		$('#txtspData_0').val( specialData );
		$('#txtwashData_0').val( washData );
		$('#txtaccData_0').val( accessoriesData );
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
			$('#txtspValue_'+n).val('');
		}
		
		var ac_row=10;
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
		
		if (form_validation('cbo_company_id*cbo_temp_id*txt_exchangeRate*cbo_buyer_id*cbo_season_id*txt_styleRef*txt_costingDate','Company*Template Name*Exchange Rate*Buyer Name*Season*Style Ref.*Costing Date')==false)
		{
			release_freezing();
			return;
		}	
		else
		{
			if(operation==4)
			{
				var report_title=$( "div.form_caption" ).html();
				generate_report_file( $('#hid_qc_no').val()+'*'+$('#txt_costSheetNo').val()+'*'+report_title,'quick_costing_print','requires/short_quotation_v5_controller');
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
				var td=5;
				
				var ae=0; var consData=''; var item_wise_tot_data="";
				for(s=1; s<=td; s++)
				{
					item_wise_tot_data+=($('#fabtd_'+s).text()*1)+'_'+($('#spOpetd_'+s).text()*1)+'_'+($('#washtd_'+s).text()*1)+'_'+($('#acctd_'+s).text()*1)+'_'+($('#txtcpm_'+s).val()*1)+'_'+($('#txtsmv_'+s).val()*1)+'_'+($('#txteff_'+s).val()*1)+'_'+($('#txtCmCost_'+s).val()*1)+'_'+($('#txtFriCost_'+s).val()*1)+'_'+($('#txtLtstCost_'+s).val()*1)+'_'+($('#txtOtherCost_'+s).val()*1)+'_'+($('#txtCommCost_'+s).val()*1)+'_'+($('#fobTtd_'+s).text()*1)+'_'+($('#fobPcsTtd_'+s).text()*1)+'_'+($('#txtCommlCost_'+s).val()*1)+'_'+($('#txtOptExp_'+s).val()*1)+'_'+s+'__';
					
					ae++;
				}
				//alert(item_wise_tot_data); release_freezing(); return;
				consData+=get_submitted_data_string('txtfabricData_0*txtspData_0*txtwashData_0*txtaccData_0',"../../",2);
				//alert(consData);   return;
				var data_tot_cost_summ=($('#cbo_buyer_agent').val()*1)+'_'+($('#cbo_agent_location').val()*1)+'_'+($('#txt_noOfPack').val()*1)+'_'+($('#cmPop').val()*1)+'_'+($('#txt_commPer').val()*1)+'_'+($('#totFab_td').text()*1)+'_'+($('#totSpc_td').text()*1)+'_'+($('#totWash_td').text()*1)+'_'+($('#totAcc_td').text()*1)+'_'+($('#totCm_td').text()*1)+'_'+($('#totFriCst_td').text()*1)+'_'+($('#totLbTstCst_td').text()*1)+'_'+($('#totOtherCst_td').text()*1)+'_'+($('#totCommCst_td').text()*1)+'_'+($('#totCost_td').text()*1)+'_'+($('#totalFob_td').text()*1)+'_'+($('#totCommlCst_td').text()*1)+'_'+($('#txt_commlPer').val()*1)+'_'+($('#totOptExp_td').text()*1);
				
				var data_mst="action=save_update_delete&operation="+operation+"&data_tot_cost_summ="+data_tot_cost_summ+"&item_wise_tot_data="+item_wise_tot_data+"&type="+type+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_temp_id*txt_temp_id*txt_inquery_id*txt_styleRef*txt_costSheetNo*txt_update_id*cbo_buyer_id*cbo_season_id*cbo_season_year*cbo_brand*txt_styleDesc*cbo_subDept_id*txt_delivery_date*txt_exchangeRate*txt_offerQty*txt_quotedPrice*txt_tgtPrice*cbo_stage_id*txt_costingDate*txt_costing_remarks*cbo_revise_no*cbo_option_id*txt_option_remarks*txt_meeting_date*txt_meeting_time*chk_is_new_meeting*txt_meeting_remarks*txt_meeting_no*txtmarign*cbo_product_department*txt_product_code*txt_article*txt_fab_process_loss_method*txt_acc_process_loss_method*hid_qc_no',"../../");
				
				var data=data_mst+consData;
				//alert(data); release_freezing(); return;
				
				http.open("POST","requires/short_quotation_v5_controller.php",true);
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
				alert("This Option (QC) is Confirm.");
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
					var temp_style_list=return_ajax_request_value(reponse[2]+'__'+1, 'temp_style_list_view', 'requires/short_quotation_v5_controller');
					$('#style_td').html( temp_style_list );
					var user_id='<? echo $_SESSION['logic_erp']['user_id']; ?>';
					//get_php_form_data(reponse[1]+"__"+user_id+"__"+reponse[2]+"__"+0+"***"+0, 'populate_style_details_data', 'requires/short_quotation_v5_controller');
					load_drop_down('requires/short_quotation_v5_controller', reponse[2]+'__'+reponse[4]+'__'+reponse[3], 'load_drop_down_revise_no', 'revise_td');
					load_drop_down('requires/short_quotation_v5_controller', reponse[2]+'__'+reponse[4]+'__'+reponse[4], 'load_drop_down_option_id', 'option_td');
					set_onclick_style_list(reponse[2]+'__'+user_id+'__'+reponse[2]);
				}
				if(reponse[5]==6) //Revise
				{
					load_drop_down('requires/short_quotation_v5_controller', reponse[2]+'__'+reponse[4]+'__0', 'load_drop_down_revise_no', 'revise_td');
					load_drop_down('requires/short_quotation_v5_controller', reponse[2]+'__'+reponse[4]+'__'+reponse[4], 'load_drop_down_option_id', 'option_td');
					set_onclick_style_list(reponse[2]+'__'+user_id+'__'+reponse[2]);
				}
				else if(  reponse[5]==7) //Option
				{	
					load_drop_down('requires/short_quotation_v5_controller', reponse[2]+'__'+reponse[4]+'__'+$('#cbo_revise_no').val(), 'load_drop_down_revise_no', 'revise_td');
					load_drop_down('requires/short_quotation_v5_controller', reponse[2]+'__'+reponse[4]+'__0', 'load_drop_down_option_id', 'option_td');
					
					//load_drop_down('requires/short_quotation_v5_controller', reponse[2]+'__'+reponse[4]+'__'+$('#cbo_revise_no').val(), 'load_drop_down_revise_no', 'revise_td');
					//load_drop_down('requires/short_quotation_v5_controller', reponse[2]+'__'+reponse[4]+'__0', 'load_drop_down_option_id', 'option_td');
					set_onclick_style_list(reponse[2]+'__'+user_id+'__'+reponse[2]);
				}
			}
			else if (reponse[0]==2)
			{
				reset_fnc();
				release_freezing();
				return;
			}
			
			var temp_style_list=return_ajax_request_value(reponse[2]+'__'+0, 'temp_style_list_view', 'requires/short_quotation_v5_controller');
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
		window.open("requires/short_quotation_v5_controller.php?data=" + data+'&action='+action, true );
	}
	
	function openmypage_style(type)
	{
		var page_link='requires/short_quotation_v5_controller.php?action=style_popup';
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
				var temp_style_list=return_ajax_request_value(cost_sheet_no+'__'+1, 'temp_style_list_view', 'requires/short_quotation_v5_controller');
				$('#style_td').html( temp_style_list );
				
				var str=style_id.split(",");
				var strcst=cost_sheet_no.split(",");
				for( var i=0; i< str.length; i++ ) {
					if( k==1)
					{
						set_onclick_style_list(str[0]+'__'+user_id+'__'+strcst[0]+'__'+0);
						var qc_no=$('#hid_qc_no').val()*1;
						change_color_tr( qc_no , $('#tr_'+qc_no).attr('bgcolor') );
						load_drop_down('requires/short_quotation_v5_controller', cost_sheet_no+'__0__0', 'load_drop_down_option_id', 'option_td');
						load_drop_down('requires/short_quotation_v5_controller', cost_sheet_no+'__'+$('#cbo_option_id').val()+'__0', 'load_drop_down_revise_no', 'revise_td');
						k++;
					}
				}
			}
			//alert(temp_style_list)
			//$('#style_td').html( $('#style_td').html() +""+ return_ajax_request_value(style_id, 'temp_style_list_view', 'requires/short_quotation_v5_controller') );
			//show_list_view( style_id,'temp_style_list_view','style_td','requires/short_quotation_v5_controller','',1);//setFilterGrid(\'tbl_po_list\',-1)
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
			load_drop_down('requires/short_quotation_v5_controller', datas[2]+'__0__0', 'load_drop_down_option_id', 'option_td');
			load_drop_down('requires/short_quotation_v5_controller', datas[2]+'__'+$('#cbo_option_id').val()+'__0', 'load_drop_down_revise_no', 'revise_td');
			
			var val=document.getElementById('cbo_revise_no').value+'***'+document.getElementById('cbo_option_id').value+'***'+document.getElementById('chk_is_new_meeting').value;
			get_php_form_data(datas[0]+"__"+datas[1]+"__"+datas[2]+"__"+val, 'populate_style_details_data', 'requires/short_quotation_v5_controller');
			$('#txt_seleted_row_id').val(datas[0]);
			$('#hid_selected_cost_no').val(datas[2]);
			//$('#chk_is_new_meeting').val(2);
			//document.getElementById('chk_is_new_meeting').checked=false;
			//fnc_meeting_no(2);
		}
		else
		{
			var val=document.getElementById('cbo_revise_no').value+'***'+document.getElementById('cbo_option_id').value+'***'+document.getElementById('chk_is_new_meeting').value;
			get_php_form_data(datas[0]+"__"+datas[1]+"__"+datas[2]+"__"+val, 'populate_style_details_data', 'requires/short_quotation_v5_controller');
			$('#txt_seleted_row_id').val(datas[0]);
			$('#hid_selected_cost_no').val(datas[2]);
			//$('#chk_is_new_meeting').val(2);
			//document.getElementById('chk_is_new_meeting').checked=false;
			//fnc_meeting_no(2);
		}
		//$('#cbo_revise_no').val(0);
		//$('#cbo_option_id').val(0);
		
		//load_drop_down('requires/short_quotation_v5_controller', reponse[2]+'__'+reponse[4]+'__'+$('#cbo_revise_no').val(), 'load_drop_down_revise_no', 'revise_td');
		//load_drop_down('requires/short_quotation_v5_controller', reponse[2]+'__'+reponse[4]+'__0', 'load_drop_down_option_id', 'option_td');
	}
	
	function fnc_delete_style_row()
	{
		var style_id=$('#txt_seleted_row_id').val();
		var hid_qc_no=$('#hid_qc_no').val();
		var temp_style_list=return_ajax_request_value(hid_qc_no+'__'+3, 'temp_style_list_view', 'requires/short_quotation_v5_controller');
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
		get_php_form_data(($("#hid_qc_no").val()*1)+'__'+user_id+'__'+0, 'populate_style_details_data', 'requires/short_quotation_v5_controller');
	}
	
	function fnc_copy_cost_sheet( operation )
	{
		//alert( $('#txt_update_id').val() );
		var data_copy="action=copy_cost_sheet&operation="+operation+get_submitted_data_string('txt_costSheetNo*txt_update_id*txt_styleRef*cbo_season_id*cbo_buyer_id*hid_qc_no',"../../");
		var data=data_copy;
		//alert(data);
		//return;
		freeze_window(operation);
		http.open("POST","requires/short_quotation_v5_controller.php",true);
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
			var temp_style_list=return_ajax_request_value(style_id+'__'+2, 'temp_style_list_view', 'requires/short_quotation_v5_controller');
			$('#style_td').html('');
			reset_fnc();
		}
	}
	
	function fnc_confirm_style()
	{
		if($('#txt_update_id').val()!="")
		{
			var data=$('#hid_qc_no').val()+'__'+$('#txt_update_id').val();
			var page_link='requires/short_quotation_v5_controller.php?action=confirmStyle_popup';
			var title="Confirm Style Popup";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&data='+data, title, 'width=950px,height=450px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				/*var theform=this.contentDoc.forms[0];
				var style_id=this.contentDoc.getElementById("hide_style_id").value; // product ID
				var temp_style_list=return_ajax_request_value(style_id+'__'+1, 'temp_style_list_view', 'requires/short_quotation_v5_controller');
				$('#style_td').html( temp_style_list );*/
				//alert(temp_style_list)
				//$('#style_td').html( $('#style_td').html() +""+ return_ajax_request_value(style_id, 'temp_style_list_view', 'requires/short_quotation_v5_controller') );
				//show_list_view( style_id,'temp_style_list_view','style_td','requires/short_quotation_v5_controller','',1);//setFilterGrid(\'tbl_po_list\',-1)
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
		
		get_php_form_data(hid_qc_no+"__"+user_id+"__"+cost_no+"__"+val+"__from_option", 'populate_style_details_data', 'requires/short_quotation_v5_controller');
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
			if(type==1) load_drop_down( 'requires/short_quotation_v5_controller', type, 'load_drop_agent_location_name', 'agent_td');
			else if (type==2) load_drop_down( 'requires/short_quotation_v5_controller', type, 'load_drop_agent_location_name', 'location_td');
		}
	}
	
	function fnc_meeting_no(chk_meeting_val)
	{
		var meeting_val=1;
		var max_meeting_no=return_ajax_request_value('', 'max_meeting_no', 'requires/short_quotation_v5_controller');
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
			var page_link='requires/short_quotation_v5_controller.php?action=fobavg_option_popup';
			var title="FOB Average Option PopUp";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&data='+data, title, 'width=650px,height=450px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				
			}
		}
	}
	
	function print_report_button_setting()
	{
		var report_ids=return_ajax_request_value('', 'print_btn_id', 'requires/short_quotation_v5_controller');
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
		generate_report_file( $('#hid_qc_no').val()+'*'+$('#txt_costSheetNo').val()+'*'+report_title, action,'requires/short_quotation_v5_controller');
		return;
	}
	
	function fnc_openmypage_inquery()
	{
		if( form_validation('cbo_temp_id','Template Name')==false)
		{
			return;
		}
		var page_link='requires/short_quotation_v5_controller.php?action=inquery_id_popup';
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
				
				load_drop_down( 'requires/short_quotation_v5_controller', theemail[1], 'load_drop_down_season', 'season_td');
				load_drop_down( 'requires/short_quotation_v5_controller', theemail[1], 'load_drop_down_brand', 'brand_td');
				
				document.getElementById('txt_styleRef').value=theemail[2];
				document.getElementById('txt_inquiry_no').value=theemail[3];
				document.getElementById('cbo_season_id').value=theemail[4];
				document.getElementById('cbo_season_year').value=theemail[5];
				document.getElementById('cbo_brand').value=theemail[6];
				document.getElementById('txt_styleDesc').value=theemail[7];
				
				//get_php_form_data(theemail[8], "populate_data_from_rdnolib", "requires/short_quotation_v5_controller" );
			}
		}
	}
	
	function fnc_bodyPart(inc)
	{
		//alert(inc);
		var incVal=inc.split("_");
		if(incVal[2]==1)
		{
			var gitm=incVal[0];
			var i=incVal[1];
			var type=incVal[2];
		}else{
			var i=incVal[0];
			var type=incVal[1];
		}
		//var bodyPart=$('#txtbodyparttext_'+i).val();
		var cbofabricnature=2;
		var pageTitle="";
		if(type==3) pageTitle="Wash Type PopUp";
		else if(type==5) pageTitle="Item Group PopUp";
		else pageTitle="Body Part PopUp";
		var page_link='requires/short_quotation_v5_controller.php?action=bodyPart_washType_ItemGroup_popup&type='+type;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, pageTitle, 'width=460px,height=450px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var id=this.contentDoc.getElementById("gid").value;
			var name=this.contentDoc.getElementById("gname").value;
			if(type==1)
			{
				document.getElementById('txtbodyparttext_'+gitm+'_'+i).value=name;
				document.getElementById('txtbodypartid_'+gitm+'_'+i).value=id;
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
				var iduom=id.split("_");
				document.getElementById('txtAcctext_'+i).value=name;
				document.getElementById('txtAccId_'+i).value=iduom[0];
				document.getElementById('cboconsuom_'+i).value=iduom[1];
			}
		}
	}
	
	function fnc_type_loder( i )
	{
		var cboembname=document.getElementById('cboSpeciaOperationId_'+i).value
		load_drop_down( 'requires/short_quotation_v5_controller', cboembname+'_'+i, 'load_drop_down_embtype', 'embtypetd_'+i );
	}
	
	function fnc_fabric_popup(inc)
	{
		var txt_fabid =document.getElementById('txtfabid_'+inc).value
		var page_link='requires/short_quotation_v5_controller.php?action=fabric_description_popup&txt_fabid='+txt_fabid;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Yarn Description', 'width=1060px,height=450px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var fab_des_id=this.contentDoc.getElementById("hiddfabid");
			var fab_desctiption=this.contentDoc.getElementById("hiddFabricDescription");
			var fab_gsm=this.contentDoc.getElementById("fab_gsm");
			
			document.getElementById('txtfabid_'+inc).value=fab_des_id.value;
			document.getElementById('txtfabdesc_'+inc).value=fab_desctiption.value;
			document.getElementById('txtfabdesc_'+inc).title=fab_desctiption.value;
			document.getElementById('txtgsm_'+inc).value=fab_gsm.value;
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
						'id': function(_, id) { var id=id.split("_"); if(id[2]) { return id[0] +"_"+ i +"_"+ id[2] } else { return id[0] +"_"+ i} },
						//'name': function(_, name) { return name + i },
						'name': function(_, name) { var name=name.split("_"); if(name[2]) { return name[0] +"_"+ i +"_"+ name[2] } else { return name[0] +"_"+ i} },
						'value': function(_, value) { return value }
					});
				}).end().appendTo("#tbl_wash");
				
				$('#txtWashTypetext_'+i).removeAttr("onDblClick").attr("onDblClick","fnc_bodyPart('"+i+"_3')");
				$('#txtWashTypetext_'+i).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
				
				$('#txtWbodyparttext_'+i).removeAttr("onDblClick").attr("onDblClick","fnc_bodyPart('"+i+"_4')");
				$('#txtWbodyparttext_'+i).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
				
				var td=5;
				for(var s=1; s<=td; s++)
				{
					$('#txtWConsumtion_'+i+'_'+s).removeAttr("onBlur").attr("onBlur","fnc_amount_calculation('wash','"+i+"',0); fnc_itemwise_data_cache()");
					//$('#txtWConsumtion_'+i+'_'+s).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
					
					$('#txtWConsumtion_'+i+'_'+s).val("");
				}
				
				$('#txtwRate_'+i).removeAttr("onChange").attr("onChange","fnc_amount_calculation('wash','"+i+"',0)");
				
				$('#increasewash_'+i).removeAttr("onClick").attr("onClick","append_row("+i+",'1');");
				$('#decreasewash_'+i).removeAttr("onClick").attr("onClick","fnc_remove_row("+i+",'1');");
				
				$('#txtWashTypetext_'+i).val("");
				$('#txtWashTypeId_'+i).val("");
				$('#txtWbodyparttext_'+i).val("");
				$('#txtWbodypartid_'+i).val("");
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
						'id': function(_, id) { var id=id.split("_"); if(id[2]) { return id[0] +"_"+ i +"_"+ id[2] } else { return id[0] +"_"+ i} },
						'name': function(_, name) { var name=name.split("_"); if(name[2]) { return name[0] +"_"+ i +"_"+ name[2] } else { return name[0] +"_"+ i} }
						//'name': function(_, name) { return name + i }
						//'value': function(_, value) { return value }
					});
				}).end().appendTo("#tbl_acc tbody:last");
				
				$('#txtAcctext_'+i).removeAttr("onDblClick").attr("onDblClick","fnc_bodyPart('"+i+"_5')");
				$('#txtAcctext_'+i).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
				
				$('#txtAccDescription_'+i).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
				$('#txtAccBandRef_'+i).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
				$('#cboconsuom_'+i).removeAttr("onchange").attr("onchange","fnc_itemwise_data_cache()");
				
				var td=5;
				for(var s=1; s<=td; s++)
				{
					$('#txtAccConsumtion_'+i+'_'+s).removeAttr("onBlur").attr("onBlur","fnc_amount_calculation('accessories','"+i+"',0); fnc_itemwise_data_cache()");
					//$('#txtAccConsumtion_'+i+'_'+s).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
					$('#txtAccConsumtion_'+i+'_'+s).val("");
				}
								
				$('#txtAccRate_'+i).removeAttr("onBlur").attr("onBlur","fnc_amount_calculation('accessories','"+i+"',0)");
				
				$('#increasetrim_'+i).removeAttr("onClick").attr("onClick","append_row("+i+",'2');");
				$('#decreasetrim_'+i).removeAttr("onClick").attr("onClick","fnc_remove_row("+i+",'2');");
				
				
				$('#txtAcctext_'+i).val("");
				$('#txtAccId_'+i).val("");
				$('#txtAccDescription_'+i).val("");
				$('#txtAccBandRef_'+i).val("");
				$('#cboconsuom_'+i).val(0);
				$('#txtAccRate_'+i).val("");
				$('#txtAccValue_'+i).val("");
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
							'id': function(_, id) { var id=id.split("_"); if(id[2]) { return id[0] +"_"+ i +"_"+ id[2] } else { return id[0] +"_"+ i} },
							'name': function(_, name) { var name=name.split("_"); if(name[2]) { return name[0] +"_"+ i +"_"+ name[2] } else { return name[0] +"_"+ i} }
							//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
							//'value': function(_, value) { return value }
						});
						
						$('#txtWashTypetext_'+i).removeAttr("onDblClick").attr("onDblClick","fnc_bodyPart('"+i+"_3')");
						$('#txtWashTypetext_'+i).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
						
						$('#txtWbodyparttext_'+i).removeAttr("onDblClick").attr("onDblClick","fnc_bodyPart('"+i+"_4')");
						$('#txtWbodyparttext_'+i).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
						
						var td=5;
						for(var s=1; s<=td; s++)
						{
							$('#txtWConsumtion_'+i+'_'+s).removeAttr("onBlur").attr("onBlur","fnc_amount_calculation('wash','"+i+"',0); fnc_itemwise_data_cache()");
							//$('#txtWConsumtion_'+i+'_'+s).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
						}
						
						$('#txtwRate_'+i).removeAttr("onChange").attr("onChange","fnc_amount_calculation('wash','"+i+"',0)");
						
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
							'id': function(_, id) { var id=id.split("_"); if(id[2]) { return id[0] +"_"+ i +"_"+ id[2] } else { return id[0] +"_"+ i} },
							'name': function(_, name) { var name=name.split("_"); if(name[2]) { return name[0] +"_"+ i +"_"+ name[2] } else { return name[0] +"_"+ i} }
							//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
							//'value': function(_, value) { return value }
						});
						
						$('#txtAcctext_'+i).removeAttr("onDblClick").attr("onDblClick","fnc_bodyPart('"+i+"_5')");
						$('#txtAcctext_'+i).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
						
						$('#txtAccDescription_'+i).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
						$('#txtAccBandRef_'+i).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
						$('#cboconsuom_'+i).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
						var td=5;
						for(var s=1; s<=td; s++)
						{
							$('#txtaccconsumtion_'+i+'_'+s).removeAttr("onBlur").attr("onBlur","fnc_amount_calculation('accessories','"+i+"','pack'); fnc_itemwise_data_cache()");
							//$('#txtaccconsumtion_'+i+'_'+s).removeAttr("onBlur").attr("onBlur","fnc_itemwise_data_cache()");
						}
						
						$('#txtAccRate_'+i).removeAttr("onBlur").attr("onBlur","fnc_amount_calculation('accessories','"+i+"',0)");
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
			$('#txtfabdesc_'+inc).val('');
			$('#txtfabid_'+inc).val('');
			$('#txtgsm_'+inc).val('');
			$('#cbofabuom_'+inc).val(0);
			$('#txtusefor_'+inc).val('');
			var td=5;
			for(var s=1; s<=td; s++)
			{
				$('#txtfabconsumtion_'+inc+'_'+s).val('');
			}
			
			$('#txtfabrate_'+inc).val('');
			$('#txtfabvalue_'+inc).val('');
			
			$('#txtfabrate_'+inc).removeAttr('readonly','readonly');
			
			fnc_amount_calculation('fabric','"+i+"',0);
		}
	}
	
	function openmypage_template_name(title)
	{
		var page_link='requires/short_quotation_v5_controller.php?action=trims_cost_template_name_popup&buyer_name=' + document.getElementById('cbo_buyer_id').value;
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
					var itemdata=select_template_data.split(",");
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
		var response=return_global_ajax_value( cbo_currercy+"**"+costing_date+"**"+cbo_company_name, 'check_conversion_rate_variable', '', 'requires/short_quotation_v5_controller');
		var response=response.split("_");
		$('#txt_exchangeRate').val(response[1]);
		$('#txt_fab_process_loss_method').val(1);
		$('#txt_acc_process_loss_method').val(1);
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
        <div style="display:none"><?=load_freeze_divs ("../../",$permission); ?></div>
        <form name="quickCosting_1" id="quickCosting_1" autocomplete="off">
            <fieldset style="width:1200px;">
                <table width="1200px" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                    	<td width="110" class="must_entry_caption"><strong>Company</strong></td>
                    	<td width="130"><? echo create_drop_down( "cbo_company_id", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "--Company--", $selected,"load_drop_down( 'requires/short_quotation_v5_controller', this.value, 'load_drop_down_location', 'location_td'); check_exchange_rate_variable();",'' ); ?></td>
                        <td width="110">Location</td>
                    	<td width="130" id="location_td"><? echo create_drop_down( "cbo_location_id", 130, $blank_array,"", 1, "--Location--", $selected, "" ); ?></td>
                    	<td width="110" class="must_entry_caption">Template Name<input type="button" class="formbutton" style="width:20px; font-style:italic" value="N" onClick="openmypage_temp('requires/short_quotation_v5_controller.php?action=template_popup','Create Template')"/>
                        <input type="hidden" id="txt_seleted_row_id">
                        <input type="hidden" id="hid_selected_cost_no">
                        <input type="hidden" id="hid_qc_no">
                        <input type="hidden" id="txt_inquery_id">
                        <input type="hidden" id="txt_fab_process_loss_method">
                        <input type="hidden" id="txt_acc_process_loss_method">
                        <input style="width:70px;" type="hidden" class="text_boxes" name="txt_temp_id" id="txt_temp_id" />
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
                        <td width="110" class="must_entry_caption">Style Ref.</td>
                        <td width="130"><input style="width:120px;" type="text" onDblClick="openmypage_style(0);" class="text_boxes textbox1" name="txt_styleRef" id="txt_styleRef" placeholder="Write/Browse"/></td>
                        <td width="110"><strong>Cost Sheet No</strong></td>
                        <td><input style="width:120px;" type="text" class="text_boxes_numeric textbox2" name="txt_costSheetNo" id="txt_costSheetNo" placeholder="Display"  readonly/><input style="width:40px;" type="hidden" name="txt_update_id" id="txt_update_id"/></td>
                    </tr>
                    <tr>
                    	<td><strong>Inquiry ID</strong></td>
                        <td><input style="width:120px;" type="text" class="text_boxes" name="txt_inquiry_no" id="txt_inquiry_no" readonly placeholder="Browse" onDblClick="fnc_openmypage_inquery();"/></td>
                        <td class="must_entry_caption"><strong>Buyer Name</strong></td>
                        <td><? echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down('requires/short_quotation_v5_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'requires/short_quotation_v5_controller',this.value, 'load_drop_down_sub_dep', 'sub_td' ); load_drop_down( 'requires/short_quotation_v5_controller', this.value, 'load_drop_down_brand', 'brand_td');" ); ?></td>
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
                        <td><strong>TGT Price</strong></td>
                        <td><input style="width:120px;" type="text" class="text_boxes_numeric" name="txt_tgtPrice" id="txt_tgtPrice" /></td>
                        <td><strong>Article</strong></td>
                        <td><input style="width:120px;" type="text" class="text_boxes" name="txt_article" id="txt_article" /></td>
                    </tr>
                    <tr>
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
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </fieldset>
            <br>
            <fieldset style="width:1200px;">
            <table width="1200" cellspacing="2" cellpadding="0" border="0">
            	<tr>
                	<td colspan="2" width="885" valign="top"> <!--Fabric-->
                    	<table width="880" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                        	<thead>
                                <tr>
                                	<th class="must_entry_caption" colspan="13">Particulars For Fabric</th>
                                </tr>
                                <tr>
                                    <th width="100" rowspan="2">Body Part</th>
                                    <th width="180" rowspan="2">Fabric Description</th>
                                    
                                    <th width="50" rowspan="2">GSM</th>
                                    <th width="60" rowspan="2">Use For</th>
                                    <th width="60" rowspan="2">UOM</th>
                                    <th colspan="<?=$countqcsizename; ?>">Cons/DZN</th>
                                    <th width="50" rowspan="2">Avg. Rate</th>
                                    <th width="60" rowspan="2">Value</th>
                                    <th rowspan="2">&nbsp;</th>
                                </tr>
                                <tr>
                                	<? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                                    <th width="50"><font style="font-size:10px;"><?=$qsname; ?></font></th>
                                    <? } ?>
                                </tr>
                            </thead>
                        </table>
                        <div style="width:880px; max-height:150px; overflow-y:scroll" id="scroll_body" > 
                    	<table width="860" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                            <tbody id="fabricitemdetails">
                            	<tr>
                                	<td class="must_entry_caption" colspan="13" align="center" bgcolor="#CCFFCC"><b>Gmts Item:</b></td>
                                </tr>
                                <? $hd=5; $m=1;
								for($n=1; $n<=$hd; $n++) { ?>
                                <tr>
                                    <td width="100"><input style="width:87px;" type="text" class="text_boxes" name="txtbodyparttext_0_<?=$m; ?>" id="txtbodyparttext_0_<?=$m; ?>" placeholder="Browse" onFocus="add_auto_complete('<?='0_'.$m.'_1'; ?>');" onDblClick="fnc_bodyPart('<?='0_'.$m.'_1'; ?>');" onBlur="fnc_itemwise_data_cache(); fn_getIndex('<?='0_'.$m.'_1'; ?>',this.value);" readonly /><input style="width:50px;" type="hidden" name="txtbodypartid_0_<?=$m; ?>" id="txtbodypartid_0_<?=$m; ?>" readonly /><input style="width:50px;" type="hidden" name="txtfabupid_0_<?=$m; ?>" id="txtfabupid_0_<?=$m; ?>" readonly /></td>
                                    <td width="180"><input style="width:167px;" type="text" class="text_boxes" name="txtfabdesc_0_<?=$m; ?>" id="txtfabdesc_0_<?=$m; ?>" readonly placeholder="Browse" onDblClick="fnc_fabric_popup(<?='0_'.$m; ?>);" /><input style="width:50px;" type="hidden" name="txtfabid_0_<?=$m; ?>" id="txtfabid_0_<?=$m; ?>" readonly /></td>
                                    <td width="50" align="center"><input style="width:37px;" type="text" class="text_boxes" name="txtgsm_0_<?=$m; ?>" id="txtgsm_0_<?=$m; ?>" /></td>
                                    <td width="60" align="center"><input style="width:47px;" type="text" class="text_boxes" name="txtusefor_0_<?=$m; ?>" id="txtusefor_0_<?=$m; ?>" /></td>
                                    <td width="60"><?=create_drop_down("cbofabuom_0_".$m, 58, $unit_of_measurement,'', 1, '-select-', "0", "fnc_itemwise_data_cache();","","1,12,15,23,27"); ?></td>
                                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                                    	<td width="50"><input style="width:37px;" type="text" class="text_boxes_numeric" name="txtfabconsumtion_0_<?=$m.'_'.$qsid; ?>" id="txtfabconsumtion_0_<?=$m.'_'.$qsid; ?>" placeholder="Write" onChange="fnc_amount_calculation('fabric',<?=$m; ?>,document.getElementById('txtfabrate_0_<?=$m; ?>').value);" onBlur="fnc_itemwise_data_cache();" /></td>
                                    <? } ?>
                                    <td width="50"><input style="width:37px;" type="text" class="text_boxes_numeric" name="txtfabrate_0_<?=$m; ?>" id="txtfabrate_0_<?=$m; ?>" placeholder="Write" onChange="fnc_amount_calculation('fabric',<?=$m; ?>,document.getElementById('txtfabrate_0_<?=$m; ?>').value);" /></td>
                                    <td width="60"><input style="width:47px;" type="text" class="text_boxes_numeric" name="txtfabvalue_0_<?=$m; ?>" id="txtfabvalue_0_<?=$m; ?>" placeholder="Display" readonly /></td>
                                    <td><input type="button" id="decreasefab_0_<?=$m; ?>" name="decreasefab_0_<?=$m; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fnc_remove_row(<?=$m; ?>,3);" /></td>
                                </tr>
                                <? $m++; } ?>
                                </tbody>
                            </table>
                        </div>
                    </td>
                    <td valign="top"><!--Style List-->
                        <table width="315" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                            <thead>
                                <tr>
                                    <th width="80">Style Ref.</th>
                                    <th width="35">S.Year</th>
                                    <th width="60">Season</th>
                                    <th>Insert User</th>
                                </tr>
                            </thead>
                        </table>
                        <div style="width:315px; max-height:150px; overflow-y:scroll" id="scroll_body" > 
                            <table width="295" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                                <tbody id="style_td">
                                </tbody>
                            </table>
                        </div>
                        <div id="test"><input type="button" name="clear_style" id="clear_style" value="Clear" onClick="fnc_clear_all();" style="width:50px" class="formbuttonplasminus" />&nbsp;<input type="button" name="delete_style" id="delete_style" value="Clear ST" onClick="fnc_delete_style_row();" style="width:60px" class="formbuttonplasminus" /><strong> Rv: </strong><span id="revise_td"><? echo create_drop_down( "cbo_revise_no", 45, $blank_array,'', 1, "-0-",$selected, "","","","","","" ); ?></span><strong> Op: </strong><span id="option_td"><? echo create_drop_down( "cbo_option_id", 45, $blank_array,'', 1, "-0-",1, "","","","","","" ); ?></span></div>
                        <div title="Option Reason/Remarks"><textarea id="txt_option_remarks" pre_opt_remarks="" class="text_area" style="width:240px; height:40px;" placeholder="Option Reason/Remarks"></textarea></div> 
                    </td>
                </tr>
                <tr>
                	<td width="685" valign="top"><!--Accessories-->
                    	<table width="680" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                            <thead>
                                <tr>
                                    <th class="must_entry_caption" colspan="12">Particulars For Accessories</th>
                                </tr>
                                <tr>
                                    <th width="80">Item Group <span id="load_temp" style="float:right; width:10px; font-weight: bold;background-color: white;color:black; border: 1px white solid; cursor: pointer;" onClick="openmypage_template_name('Template Search');">...</span></th>
                                    <th width="85">Description</th>
                                    <th width="45">Brand /Sup Ref</th>
                                    <th width="40">Cons. UOM</th>
                                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                                    <th width="50"><font style="font-size:10px; font-weight:100"><?=$qsname; ?></font></th>
                                    <? } ?>
                                    <th width="40">Rate($)</th>
                                    <th width="50">Value($)</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                        </table>
                        <div style="width:680px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
                           <table width="660" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" id="tbl_acc">
                           		<tbody>
								<?
                                $acc=10; $k=1;
                                for($l=1; $l<=$acc; $l++){ 
								?>
									<tr id="tracc_<?=$k; ?>">
										<td width="80"><input style="width:67px;" type="text" class="text_boxes" name="txtAcctext_<?=$k; ?>" id="txtAcctext_<?=$k; ?>" placeholder="Browse"  onDblClick="fnc_bodyPart('<?=$k; ?>_5');" onBlur="fnc_itemwise_data_cache();" readonly /><input style="width:50px;" type="hidden" name="txtAccId_<?=$k; ?>" id="txtAccId_<?=$k; ?>" readonly /></td>
										<td width="85"><input style="width:72px;" type="text" class="text_boxes" name="txtAccDescription_<?=$k; ?>" id="txtAccDescription_<?=$k; ?>" placeholder="Write" onBlur="fnc_itemwise_data_cache();" /></td>
										<td width="45"><input style="width:32px;" type="text" class="text_boxes" name="txtAccBandRef_<?=$k; ?>" id="txtAccBandRef_<?=$k; ?>" placeholder="Write" onBlur="fnc_itemwise_data_cache();" /></td>
										<td width="40"><?=create_drop_down( "cboconsuom_$k", 40, $unit_of_measurement,"", 1, "-Select-", 0, "fnc_itemwise_data_cache();",1,"" ); ?></td>
										<? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
											<td width="50"><input style="width:37px;" type="text" class="text_boxes_numeric" name="txtaccconsumtion_<?=$k.'_'.$qsid; ?>" id="txtaccconsumtion_<?=$k.'_'.$qsid; ?>" placeholder="Write" onBlur="fnc_amount_calculation('accessories',<?=$k; ?>,document.getElementById('txtAccRate_<?=$k; ?>').value); fnc_itemwise_data_cache();" /></td>
										<? } ?>
										
										<td width="40"><input style="width:27px;" type="text" class="text_boxes_numeric" name="txtAccRate_<?=$k; ?>" id="txtAccRate_<?=$k; ?>" onBlur="fnc_amount_calculation('accessories',<?=$k; ?>,this.value);" /></td>
										<td width="50"><input style="width:37px;" type="text" class="text_boxes_numeric" name="txtAccValue_<?=$k; ?>" id="txtAccValue_<?=$k; ?>" readonly /></td>
										 <td><input type="button" id="increasetrim_<?=$k; ?>" name="increasetrim_<?=$k; ?>" style="width:25px" class="formbutton" value="+" onClick="append_row(<?=$k; ?>,2);" />
										<input type="button" id="decreasetrim_<?=$k; ?>" name="decreasetrim_<?=$k; ?>" style="width:20px" class="formbutton" value="-" onClick="javascript:fnc_remove_row(<?=$k; ?>,2);" /></td>
									</tr>
                                <? $k++; } ?>
                            </tbody>
                        </table>
                    </div>
                    </td>
                    <td valign="top" width="200"><!--Remarks-->
                    	<table width="198" cellspacing="0" cellpadding="0" border="0" class="rpt_table" rules="all">
                            <tr>
                                <td colspan="3">&nbsp;</td>
                            </tr>
                            <tr valign="top">
                                <td width="80"><strong>Buyer Agent</strong><input type="button" class="formbutton" style="width:15px; font-style:italic" value="N" onClick="openmypage_agent_location('requires/quick_costing_woven_controller.php?action=agent_location_popup','Create Buyer Agent',1);"/></td>
                                <td width="70" id="agent_td"><?=create_drop_down( "cbo_buyer_agent", 70,"select tuid, agent_location from lib_agent_location where type=1 and status_active=1 and is_deleted=0","tuid,agent_location", 1, "-Agent-", $selected, "" ); ?></td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td><strong>B. Location
                                    <input type="button" class="formbutton" style="width:15px; font-style:italic" value="N" onClick="openmypage_agent_location('requires/quick_costing_woven_controller.php?action=agent_location_popup','Create Location',2)"/></strong></td>
                                <td id="location_td"><?=create_drop_down( "cbo_agent_location", 70,"select tuid, agent_location from lib_agent_location where type=2 and status_active=1 and is_deleted=0","tuid,agent_location", 1, "-Location-", $selected, "" ); ?></td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td><strong style="color:#F00">Margin $</strong></td>
                                <td><input type="text" name="txtmarign" id="txtmarign" style="width:57px" class="text_boxes_numeric" value="" onChange="fnc_amount_calculation(0,0,0);" /></td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="height:28px; font-size:25px; background:#00CED1;" onClick="fnc_fobavg_option();">F.O.B $</td>
                                <td style="height:28px; font-size:25px; color:#FFFFFF; background:#FE00FF;" id="totalFob_td" prev_fob="" align="right" title="(Rmg Ratio*Fob (Pcs))*No Of. Pack's">&nbsp;</td>
                                <td style="height:28px; font-size:25px; background:#00FA9A;" id="uom_td">&nbsp;</td>
                            </tr>
                            <tr>
                                <td><strong>No Of. Pack's</strong></td>
                                <td><input type="text" name="txt_noOfPack" id="txt_noOfPack" style="width:57px" class="text_boxes_numeric" value="1" onChange="fnc_check_zero_val(this.value); fnc_amount_calculation(0,0,0);" /></td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                            	<td align="center"><input type="button" class="formbutton" value="Approval" onClick="openmypage_style(1); "/></td>
                                <td align="center"><input type="button" name="confirm_style" id="confirm_style" value="Confirm" onClick="fnc_confirm_style();" style="width:50px" class="formbuttonplasminus" /></td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr><td colspan="3" align="center"><strong>Merchandiser Remarks : </strong></td></tr>
                            <tr>
                                <td colspan="3"><textarea id="txt_costing_remarks" pre_costing_remarks="" class="text_area" style="width:190px; height:40px;" placeholder="Merchandiser Remarks">1. </textarea></td>
                            </tr>
                        </table>
                    </td>
                    <td valign="top"><!--Button-->
                    	<table width="250" cellspacing="0" cellpadding="0" border="0" class="rpt_table" rules="all">
                            <tr>
                                <td colspan="3" align="center" height="40" class="button_container">
                                <? echo load_submit_buttons($permission,"fnc_qcosting_entry",0,0,"reset_fnc();",1); ?><br>
                                <input type="button" id="report_btn_1" class="formbutton" style="width:30px;" value="PNT" onClick="fnc_print_report('quick_costing_print');" />&nbsp;<input type="button" id="set_button" class="formbutton" style="width:60px;" value="Copy" onClick="fnc_copy_cost_sheet(0);" />&nbsp; <input type="button" id="set_button" class="formbutton" style="width:45px;" value="Revise" onClick="fnc_qcosting_entry(6);" />&nbsp; <input type="button" id="set_button" class="formbutton" style="width:45px;" value="Option" onClick="fnc_qcosting_entry(7);" />
                                </td>
                            </tr>
                            <tr><td colspan="3"><strong>Date & Time:</strong><input style="width:45px;" type="text" class="datepicker" name="txt_meeting_date" id="txt_meeting_date" value="<? echo date('d-m-Y');?>" /><input name="txt_meeting_time" id="txt_meeting_time" class="text_boxes" type="text" style="width:30px;" placeholder="24 H. Format" onChange="fnc_valid_time(this.value,'txt_meeting_time');" onKeyUp="fnc_valid_time(this.value,'txt_meeting_time');" onKeyPress="return numOnly(this,event,this.id);" value="<? echo date('H:i', time()); ?>" />
                            <strong>N. Meeting No.</strong>
                            <input type="checkbox" name="chk_is_new_meeting" id="chk_is_new_meeting" onClick="fnc_rate_write_popup('meeting');" value="2" style="width:12px;" ></td></tr>
                            <tr><td><strong>Meeting No:</strong></td><td align="center"><input style="width:60px;" type="text" class="text_boxes" name="txt_meeting_no" id="txt_meeting_no" disabled readonly /></td><td><input type="button" name="meeting_remarks" id="meeting_remarks" value="Meeting Minutes" onClick="fnc_meeting_remarks_pop_up(document.getElementById('txt_update_id').value, document.getElementById('txt_styleRef').value);" style="width:100px" class="formbuttonplasminus" /></td></tr>
                            <tr><td colspan="3" align="center"><strong>Meeting Remarks:</strong></td></tr>
                            <tr>
                                <td colspan="3"><textarea id="txt_meeting_remarks" pre_meeting_remark="" class="text_area" style="width:250px; height:40px;" placeholder="Meeting Remarks">1. </textarea></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top" style="height:100px"><!--Special Operation-->
                    	<table width="680" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                            <thead>
                                <tr>
                                    <th class="must_entry_caption" colspan="11">Particulars For Special Operation</th>
                                </tr>
                                <tr>
                                    <th width="70">Name</th>
                                    <th width="80">Type</th>
                                    <th width="80">Body Part</th>
                                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                                    <th width="50"><font style="font-size:10px; font-weight:100"><?=$qsname; ?></font></th>
                                    <? } ?>
                                    <th width="40">Rate ($)</th>
                                    <th width="50">Value ($)</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                        </table>
                        <div style="width:680px; max-height:80px; overflow-y:scroll" id="scroll_body" > 
                               <table width="660" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all"> 
                                
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
                                    <td width="70"><?=create_drop_down( "cboSpeciaOperationId_$j", 70, $emblishment_name_array,"", 1, "--Select--", $sel, "fnc_type_loder(".$j.");","","1,2,4,5,99" ); ?></td>
                                    <td width="80" id="embtypetd_<?=$j; ?>"><?=create_drop_down( "cboSpeciaTypeId_$j", 80, $emb_typearr[$sel],"", 1, "-Select-", "", "" ); ?></td>
                                    <td width="80"><input style="width:67px;" type="text" class="text_boxes" name="txtspbodyparttext_<?=$j; ?>" id="txtspbodyparttext_<?=$j; ?>" placeholder="Browse" onFocus="add_auto_complete('<?=$j.'_2'; ?>');" onDblClick="fnc_bodyPart('<?=$j.'_2'; ?>');" onBlur="fnc_itemwise_data_cache(); fn_getIndex('<?=$j.'_2'; ?>',this.value);" readonly/><input style="width:50px;" type="hidden" name="txtspbodypartid_<?=$j; ?>" id="txtspbodypartid_<?=$j; ?>" readonly /></td>
                                    
                                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                                        <td width="50"><input style="width:37px;" type="text" class="text_boxes_numeric" name="txtspconsumtion_<?=$j.'_'.$qsid; ?>" id="txtspconsumtion_<?=$j.'_'.$qsid; ?>" placeholder="Write" onBlur="fnc_amount_calculation('special',<?=$j; ?>,document.getElementById('txtSpRate_<?=$j; ?>').value); fnc_itemwise_data_cache();" /></td>
                                    <? } ?>
                                    <td width="40"><input style="width:27px;" type="text" class="text_boxes_numeric" name="txtSpRate_<?=$j; ?>" id="txtSpRate_<?=$j; ?>" onChange="fnc_amount_calculation('special',<?=$j; ?>,this.value);" /></td>
                                    <td width="50"><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtspValue_<?=$j; ?>" id="txtspValue_<?=$j; ?>" readonly /></td>
                                    <td><input style="width:70px;" type="text" class="text_boxes" name="txtSpRemarks_<?=$j; ?>" id="txtSpRemarks_<?=$j; ?>" placeholder="Write" /></td>
                                </tr>
                                <? $j++; } ?>
                            </table>
                        </div>
                    </td>
                    <td colspan="2" rowspan="2" valign="top" id="summary_td"><!--Summary Start-->
                    	<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="480">
                            <thead>
                                <tr>
                                    <th colspan="7">Style Cost Summary [$/DZN]</th>
                                </tr>
                                <tr>
                                    <th width="100">Description</th>
                                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                                    <th width="60"><font style="font-size:10px; font-weight:100;"><?=$qsname; ?></font></th>
                                    <? } ?>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Fabric</td>
                                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                                    <td id="fabtd_<?=$qsid; ?>" align="right"><strong>&nbsp;</strong></td>
                                    <? } ?>
                                    <td id="totFab_td" align="right">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Special Operation</td>
                                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                                    <td id="spOpetd_<?=$qsid; ?>" align="right"><strong>&nbsp;</strong></td>
                                    <? } ?>
                                    <td id="totSpc_td" align="right">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Wash</td>
                                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                                    <td id="washtd_<?=$qsid; ?>" align="right"><strong>&nbsp;</strong></td>
                                    <? } ?>
                                    <td id="totWash_td" align="right">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Accessories</td>
                                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                                    <td id="acctd_<?=$qsid; ?>" align="right"><strong>&nbsp;</strong></td>
                                    <? } ?>
                                    <td id="totAcc_td" align="right">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>CPM &nbsp;&nbsp;&nbsp;
                                      <input type="checkbox" name="cmPop" id="cmPop" onClick="fnc_rate_write_popup('cm')" value="2" style="width:12px;" title="Is Calculative?" /></td>
                                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                                    <td align="center"><input name="txtcpm_<?=$qsid; ?>" id="txtcpm_<?=$qsid; ?>" class="text_boxes_numeric" style="width:37px;" title="CPM" placeholder="CPM" disabled onChange="fnc_amount_calculation(3,<?=$qsid; ?>,0);" /></td>
                                    <? } ?>
                                    
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>SMV / EFI</td>
                                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                                    <td align="center"><input name="txtsmv_<?=$qsid; ?>" id="txtsmv_<?=$qsid; ?>" class="text_boxes_numeric" style="width:16px;" title="SMV" placeholder="SMV" disabled onChange="fnc_amount_calculation(3,<?=$qsid; ?>,0);" /><input name="txteff_<?=$qsid; ?>" id="txteff_<?=$qsid; ?>" class="text_boxes_numeric" style="width:16px;" title="Efficiency" placeholder="EFF" disabled onChange="fnc_amount_calculation(3,<?=$qsid; ?>,0);"/></td>
                                    <? } ?>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>CM&nbsp;</td>
                                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                                    <td align="center"><input name="txtCmCost_<?=$qsid; ?>" id="txtCmCost_<?=$qsid; ?>" class="text_boxes_numeric" style="width:37px;" onChange="fnc_amount_calculation(4,<?=$qsid; ?>,0);" placeholder="Write"/></td>
                                    <? } ?>
                                    <td id="totCm_td" align="right">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Frieght Cost</td>
                                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                                    <td align="center"><input name="txtFriCost_<?=$qsid; ?>" id="txtFriCost_<?=$qsid; ?>" class="text_boxes_numeric" style="width:37px;" onChange="fnc_amount_calculation(5,<?=$qsid; ?>,0);" placeholder="Write" /></td>
                                    <? } ?>
                                    <td id="totFriCst_td" align="right">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Lab - Test</td>
                                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                                    <td align="center"><input name="txtLtstCost_<?=$qsid; ?>" id="txtLtstCost_<?=$qsid; ?>" class="text_boxes_numeric" style="width:37px;" onChange="fnc_amount_calculation(6,<?=$qsid; ?>,0);" placeholder="Write" /></td>
                                    <? } ?>
                                    <td id="totLbTstCst_td" align="right">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Opt. Exp.&nbsp;</td>
                                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                                    <td align="center"><input name="txtOptExp_<?=$qsid; ?>" id="txtOptExp_<?=$qsid; ?>" class="text_boxes_numeric" style="width:37px;" onChange="fnc_amount_calculation(12,<?=$qsid; ?>,0);" placeholder="Write" /></td>
                                    <? } ?>
                                    <td id="totOptExp_td" align="right">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Other Cost</td>
                                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                                    <td align="center"><input name="txtOtherCost_<?=$qsid; ?>" id="txtOtherCost_<?=$qsid; ?>" class="text_boxes_numeric" style="width:37px;" onChange="fnc_amount_calculation(8,<?=$qsid; ?>,0);" placeholder="Write" /></td>
                                    <? } ?>
                                    <td id="totOtherCst_td" align="right">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td title="Commercial Cost">Comml.                                                  
                                    <input type="text" name="txt_commlPer" id="txt_commlPer" class="text_boxes_numeric" style="width:23px;" title="Commercial Cost" placeholder="%" onChange="fnc_amount_calculation(11,0,0);" /></td>
                                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                                    <td align="center"><input name="txtCommlCost_<?=$qsid; ?>" id="txtCommlCost_<?=$qsid; ?>" class="text_boxes_numeric" style="width:37px;" onChange="fnc_amount_calculation(11,<?=$qsid; ?>,0);" placeholder="Display" readonly /></td>
                                    <? } ?>
                                    <td id="totCommlCst_td" align="right">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td title="Commission Cost">Com(%)<input name="txt_commPer" id="txt_commPer" class="text_boxes_numeric" style="width:20px;" title="Commission (%)" placeholder="%" onChange="fnc_amount_calculation(9,0,0);" /></td>
                                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                                    <td align="center"><input name="txtCommCost_<?=$qsid; ?>" id="txtCommCost_<?=$qsid; ?>" class="text_boxes_numeric" style="width:37px;" onChange="fnc_amount_calculation(9,<?=$qsid; ?>,0);" placeholder="Display" readonly /></td>
                                    <? } ?>
                                    <td id="totCommCst_td" align="right">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>F.O.B</td>
                                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                                    <td id="fobTtd_<?=$qsid; ?>">&nbsp;</td>
                                    <? } ?>
                                    <td id="totCost_td" align="right">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td id="fobtxt">F.O.B($/PCS)</td>
                                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                                    <td id="fobPcsTtd_<?=$qsid; ?>">&nbsp;</td>
                                    <? } ?>
                                    <td id="totFOBCost_td" align="right">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="display:none">RMG (Ratio)</td>
                                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                                    <td  style="display:none" align="center"><input name="txtRmgQty_<?=$qsid; ?>" id="txtRmgQty_<?=$qsid; ?>" class="text_boxes_numeric" style="width:37px;" onChange="" disabled /></td>
                                    <? } ?>
                                    <td id="totRmgQty_td" align="right" style="display:none">&nbsp;</td>
                                </tr>
                            </tbody>
                       </table>
                    </td>
                </tr>
                <tr>
                	<td valign="top" ><!--Wash Start-->
                		<table width="680" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                            <thead>
                                <tr>
                                    <th class="must_entry_caption" colspan="11">Particulars For Wash</th>
                                </tr>
                                <tr>
                                    <th width="80">Wash Type</th>
                                    <th width="80">Body Part</th>
                                    <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                                    <th width="50"><font style="font-size:10px; font-weight:100;"><?=$qsname; ?></font></th>
                                    <? } ?>
                                    <th width="40">Rate ($)</th>
                                    <th width="50">Value ($)</th>
                                    <th width="90">Remarks</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                        </table>
                        <div style="width:680px; max-height:100px; overflow-y:scroll" id="scroll_body" > 
                               <table width="660" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" id="tbl_wash"> 
                                
                                <? /*$spw=17; $w=1;
                                for($sw=1; $sw<=$spw; $sw++) {*/ 
                                //if($i==4) $sel=99; else $sel=$j;
                                ?>
                                <tbody>
                                    <tr bgcolor="#FFFF00" id="trwash_1">
                                        <td width="80"><input style="width:68px;" type="text" class="text_boxes" name="txtWashTypetext_1" id="txtWashTypetext_1" placeholder="Browse" onDblClick="fnc_bodyPart('1_3');" onBlur="fnc_itemwise_data_cache();" readonly /><input style="width:50px;" type="hidden" name="txtWashTypeId_1" id="txtWashTypeId_1" readonly /></td>
                                        <td width="80"><input style="width:68px;" type="text" class="text_boxes" name="txtWbodyparttext_1" id="txtWbodyparttext_1" placeholder="Browse" onDblClick="fnc_bodyPart('1_4');" onBlur="fnc_itemwise_data_cache();" readonly /><input style="width:50px;" type="hidden" name="txtWbodypartid_1" id="txtWbodypartid_1" readonly /></td>
                                        <? foreach($qcsizenamearr as $qsid=>$qsname) { ?>
                                        <td width="50"><input style="width:37px;" type="text" class="text_boxes_numeric" name="txtWConsumtion_1_<?=$qsid; ?>" id="txtWConsumtion_1_<?=$qsid; ?>" placeholder="Write" onBlur="fnc_amount_calculation('wash',1,0); fnc_itemwise_data_cache();" /></td>
                                        <? } ?>
                                        
                                        <td width="40"><input style="width:27px;" type="text" class="text_boxes_numeric" name="txtwRate_1" id="txtwRate_1" onChange="fnc_amount_calculation('wash',1,0);" /></td>
                                        <td width="50"><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtWValue_1" id="txtWValue_1" readonly /></td>
                                        <td width="90"><input style="width:78px;" type="text" class="text_boxes" name="txtWashRemarks_1" id="txtWashRemarks_1" placeholder="Write" /></td>
                                        <td><input type="button" id="increasewash_1" name="increasewash_1" style="width:25px" class="formbutton" value="+" onClick="append_row(1,1);" />
                                    <input type="button" id="decreasewash_1" name="decreasewash_1" style="width:25px" class="formbutton" value="-" onClick="javascript:fnc_remove_row(1,1);" /></td>
                                    </tr>
                                </tbody>
                                <? //$w++; } ?>
                            </table>
                        </div>
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
		var temp_style_list=return_ajax_request_value(style_id+'__'+0, 'temp_style_list_view', 'requires/short_quotation_v5_controller');
		$('#style_td').html( temp_style_list );
		//$('#style_td').html( localStorage.getItem("temp_style_list_view") ); 
		//fnc_select(); fnc_meeting_no(0); print_report_button_setting();
    </script>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>