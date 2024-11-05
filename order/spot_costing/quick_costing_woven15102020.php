<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Quick Costing Woven
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	16-08-2020
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

$qcCons_from=return_field_value("excut_source","variable_order_tracking","excut_source=2 and variable_list=68 and is_deleted=0 and status_active=1 order by id","excut_source");

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Quick Costing Woven","../../", 1, 1, $unicode,1,'');

?>	
<script>

	var permission='<? echo $permission; ?>';
	var cons_cond='<? echo $qcCons_from; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	// Master Form-----------------------------------------------------------------------------
	var prev_item_id='';
	function fnc_select()
	{
		$(document).ready(function() {
			$("input:text").focus(function() { $(this).select(); } );
		});
	}
	
	function openmypage_temp(page_link,title)
	{
		var temp_id=document.getElementById('cbo_temp_id').value;
		page_link=page_link+'&temp_id='+temp_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=350px,center=1,resize=0,scrolling=0','../')
		release_freezing();
		emailwindow.onclose=function()
		{
			load_drop_down( 'requires/quick_costing_woven_controller', '', 'load_drop_template_name', 'template_td');
		}
	}
	
	function fnc_template_view()
	{
		var temp_id=$("#cbo_temp_id").val();
		var item_id=return_ajax_request_value(temp_id, 'load_lib_item_id', 'requires/quick_costing_woven_controller');
		//alert(item_id);
		$("#txt_temp_id").val(item_id);
		var id=item_id.split(",");
		var asc_id=id; //.sort();
		//alert(asc_id); return;
		var item_name=get_dropdown_text('cbo_temp_id');
		var nameItem=item_name.split(",");
		fnc_item_list(id+"__"+item_name);
		fnc_summary_dtls(asc_id+"__"+item_name);
		fnc_dtls_ganerate(trim(id[0])+"__"+nameItem[0]);
		//return;
		fnc_change_data($("#cbo_head_1").val(),1);
		fnc_change_data($("#cbo_head_2").val(),2);
		fnc_change_data($("#cbo_head_3").val(),3);
		fnc_change_data($("#cbo_head_4").val(),4);
		fnc_change_data($("#cbo_head_5").val(),5);
		
		navigate_arrow_key();
		fnc_specialAcc_reset();
		
		var count_td=id.length;
				
		if(count_td==1)
		{
			$('#uom_td').text('/Pcs');
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
		$("#item_tbl tbody tr").remove();
		
		var k=0; var bgcolor=''; var inp_cls='';
		for(var l=1; l<=gmtId.length; l++)
		{
			var itm_id=0;
			itm_id=trim(gmtId[k]);
			if (k%2==0) bgcolor="tr"+itm_id; else bgcolor="tr"+itm_id;
			if (k%2==0) inp_cls="textbox1"; else inp_cls="textbox2";	
			html += '<tr class="'+bgcolor+'"><td width="90px" rowspan="9" valign="middle" onClick="fnc_dtls_ganerate(\''+itm_id+'__'+gmtName[k]+'\');"><strong>'+gmtName[k]+'</strong><input style="width:50px;" type="hidden" name="txt_itemId_'+itm_id+'" id="txt_itemId_'+itm_id+'" value="'+itm_id+'" /><input style="width:50px;" type="hidden" name="txt_itemBodyData_'+itm_id+'" id="txt_itemBodyData_'+itm_id+'" /><input style="width:50px;" type="hidden" name="txt_itemDVAData_'+itm_id+'" id="txt_itemDVAData_'+itm_id+'" /><input style="width:50px;" type="hidden" name="txt_itemConsRateData_'+itm_id+'" id="txt_itemConsRateData_'+itm_id+'" /><input style="width:50px;" type="hidden" name="txt_specialData_'+itm_id+'" id="txt_specialData_'+itm_id+'" /><input style="width:50px;" type="hidden" name="txt_itemAccData_'+itm_id+'" id="txt_itemAccData_'+itm_id+'" /></td>'; 
			//var m=1;
			var txt_itemDVAData=$("#txt_itemDVAData_"+itm_id).val();
			//alert ()
			var hd=5; var def=0;
			for(var m=1; m<=hd; m++)
			{
				var defult_des='';
				//if(itm_id==20 || itm_id==21 || itm_id==22 || itm_id==23 || itm_id==28 || itm_id==29 || itm_id==30 || itm_id==34 || itm_id==35 || itm_id==67 || itm_id==70 || itm_id==71)
				if(gmtName[k].search('BTS'))
				{
					if( m==1 ) defult_des="THIGH";
					else if( m==2 ) defult_des="Hem Hight";
					else if( m==3 ) defult_des="Front HIGHT";
					else defult_des="THIGH";
				}
				else
				{
					if( m==1 ) defult_des="BODY LENGTH";
					else if( m==2 ) defult_des="RIB LENGTH";
					else  defult_des="LENGTH";
				}
				
				html += '<td width="86px"><input style="width:72px;" type="text" class="text_boxes '+inp_cls+'" name="txtItemDes_'+itm_id+'_1_'+m+'" id="txtItemDes_'+itm_id+'_1_'+m+'" value="'+defult_des+'" /></td><td width="37px"><input style="width:25px;" type="text" class="text_boxes_numeric '+inp_cls+'" name="txtVal_'+itm_id+'_1_'+m+'" id="txtVal_'+itm_id+'_1_'+m+'" onChange="fnc_consumption_calculation('+itm_id+',1,'+m+', this.value, document.getElementById('+"'txtAw_"+itm_id+"_1_"+m+"'"+').value );" /></td><td width="37px"><input style="width:25px;" type="text" class="text_boxes_numeric '+inp_cls+'" name="txtAw_'+itm_id+'_1_'+m+'" id="txtAw_'+itm_id+'_1_'+m+'" onChange="fnc_consumption_calculation('+itm_id+',1,'+m+', document.getElementById('+"'txtVal_"+itm_id+"_1_"+m+"'"+').value, this.value );" /><input style="width:30px;" type="hidden" name="txtdvaId_'+itm_id+'_1_'+m+'" id="txtdvaId_'+itm_id+'_1_'+m+'" /></td>';
			}
			html += '</tr>';
			var trLen=8; var col=2;
			for(var j=1; j<=trLen; j++)
			{
				html += '<tr class="'+bgcolor+'">';
				for(var n=1; n<=hd; n++)
				{
					var defult_des="";
					if(n==1)
					{
						//if(itm_id==20 || itm_id==21 || itm_id==22 || itm_id==23 || itm_id==28 || itm_id==29 || itm_id==30 || itm_id==34 || itm_id==35 || itm_id==67 || itm_id==70 || itm_id==71)
						if(gmtName[k].search('BTS'))
						{
							if( col==2) defult_des="IN SEAM";
							else if( col==3 ) defult_des="Waist Band Height";
							else if( col==4 ) defult_des="Waist Band Width";
							else if( col==5 ) defult_des="Low HIP";
							else if( col==6 ) defult_des="Bottom Opening";
							else if( col==7 ) defult_des="";
							else if( col==8 ) defult_des="FABRIC WEIGHT";
							else if( col==9 ) defult_des="FAB. WIDTH-INCH";
						}
						else
						{
							if( col==2) defult_des="SLEEVE LENGTH";
							else if( col==3 ) defult_des="HALF CHEST";
							else if( col==4 ) defult_des="FABRIC WEIGHT";
							else if( col==5 ) defult_des="FAB. WIDTH-INCH";
							else if( col==6 ) defult_des="";
							else if( col==7 ) defult_des="";
							else if( col==8 ) defult_des="";
							else if( col==9 ) defult_des="";
						}
					}
					else if( n==2 )
					{
						if( col==2) defult_des="Hem Length"; 
						else if( col==3 ) defult_des="";
						else if( col==4 ) defult_des="";
						else if( col==5 ) defult_des="";
						else if( col==6 ) defult_des="";
						else if( col==7 ) defult_des="";
						else if( col==8 ) defult_des="FABRIC WEIGHT";
						else if( col==9 ) defult_des="FAB. WIDTH-INCH";
					}
					else if( n==3)
					{
						if( col==2) defult_des="Front Length"; 
						else if( col==3 ) defult_des="Back HIGHT";
						else if( col==4 ) defult_des="Back Length";
						else if( col==5 ) defult_des="";
						else if( col==6 ) defult_des="";
						else if( col==7 ) defult_des="";
						else if( col==8 ) defult_des="FABRIC WEIGHT";
						else if( col==9 ) defult_des="FAB. WIDTH-INCH";
					}
					else
					{
						if( col==2) defult_des="OUT SIDE LENGTH"; 
						else if( col==3 ) defult_des="";
						else if( col==4 ) defult_des="";
						else if( col==5 ) defult_des="";
						else if( col==6 ) defult_des="";
						else if( col==7 ) defult_des="";
						else if( col==8 ) defult_des="FABRIC WEIGHT";
						else if( col==9 ) defult_des="FAB. WIDTH-INCH";
					}
					html += '<td width="84px"><input style="width:72px;" type="text" class="text_boxes '+inp_cls+'" name="txtItemDes_'+itm_id+'_'+col+'_'+n+'" id="txtItemDes_'+itm_id+'_'+col+'_'+n+'" value="'+defult_des+'" /></td><td width="37px"><input style="width:25px;" type="text" class="text_boxes_numeric '+inp_cls+'" name="txtVal_'+itm_id+'_'+col+'_'+n+'" id="txtVal_'+itm_id+'_'+col+'_'+n+'" onChange="fnc_consumption_calculation('+itm_id+','+col+','+n+', this.value, document.getElementById('+"'txtAw_"+itm_id+"_"+col+"_"+n+"'"+').value );" /></td><td width="37px"><input style="width:25px;" type="text" class="text_boxes_numeric '+inp_cls+'" name="txtAw_'+itm_id+'_'+col+'_'+n+'" id="txtAw_'+itm_id+'_'+col+'_'+n+'" onChange="fnc_consumption_calculation('+itm_id+','+col+','+n+', document.getElementById('+"'txtVal_"+itm_id+"_"+col+"_"+n+"'"+').value, this.value );" /><input style="width:30px;" type="hidden" name="txtdvaId_'+itm_id+'_1_'+m+'" id="txtdvaId_'+itm_id+'_'+col+'_'+n+'" /></td>';
				}
				html += '</tr>';
				col++;
			}
			//alert(html)
			k++;
		}
		$("#item_tbody").append(html);
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
        html += '<td id="totSpc_td" align="right">&nbsp;</td></tr><tr><td>Accessories</td>';
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
		
		html += '<td>&nbsp;</td></tr><tr><td>CM ($/DZN)</td>';
        var m=0;
		for(var v=1; v<=gmtName.length; v++)
		{
			html += '<td align="center"><input name="txtCmCost_[]" id="txtCmCost_'+trim(gmtId[m])+'" value="" class="text_boxes_numeric" style="width:40px;" onChange="fnc_amount_calculation(4,'+trim(gmtId[m])+',0);" placeholder="Write" /></td>';
			m++;
		}
		html += '<td id="totCm_td" align="right">&nbsp;</td></tr><tr><td>Frieght Cost($/DZN)</td>';
        var n=0;
		for(var w=1; w<=gmtName.length; w++)
		{
			html += '<td align="center"><input name="txtFriCost_[]" id="txtFriCost_'+trim(gmtId[n])+'" value="" class="text_boxes_numeric" style="width:40px;" onChange="fnc_amount_calculation(5,'+trim(gmtId[n])+',0);" placeholder="Write" /></td>';
			n++;
		}
        html += '<td id="totFriCst_td" align="right">&nbsp;</td></tr><tr><td>Lab - Test($/DZN)</td>';
		
        var o=0;
		for(var x=1; x<=gmtName.length; x++)
		{
			html += '<td align="center"><input name="txtLtstCost_[]" id="txtLtstCost_'+trim(gmtId[o])+'" value="" class="text_boxes_numeric" style="width:40px;" onChange="fnc_amount_calculation(6,'+gmtId[o]+',0);" placeholder="Write" /></td>';
			o++;
		}
        html += '<td id="totLbTstCst_td" align="right">&nbsp;</td></tr><tr><td>Mis/Offer Qty.&nbsp; <input name="txt_lumSum_cost" id="txt_lumSum_cost" value="" class="text_boxes_numeric" style="width:37px;" title="Lum Sum Cost" placeholder="L.S" onChange="fnc_amount_calculation(7,0,0);"  /></td>';
		var aa=0;
		for(var bb=1; bb<=gmtName.length; bb++)
		{
			html += '<td align="center"><input name="txtMissCost_[]" id="txtMissCost_'+trim(gmtId[aa])+'" value="" class="text_boxes_numeric" style="width:40px;" onChange="fnc_amount_calculation(7,'+trim(gmtId[aa])+',0);" placeholder="Display" /></td>';
			aa++;
		}
        html += '<td id="totMissCst_td" align="right">&nbsp;</td></tr><tr><td>Other Cost($/DZN)</td>';
		var ab=0;
		for(var bc=1; bc<=gmtName.length; bc++)
		{
			html += '<td align="center"><input name="txtOtherCost_[]" id="txtOtherCost_'+trim(gmtId[ab])+'" value="" class="text_boxes_numeric" style="width:40px;" onChange="fnc_amount_calculation(8,'+trim(gmtId[ab])+',0);" placeholder="Write" /></td>';
			ab++;
		}
		html += '<td id="totOtherCst_td" align="right">&nbsp;</td></tr><tr><td title="Commission">Com.(%)($/DZN)<input name="txt_commPer" id="txt_commPer" value="" class="text_boxes_numeric" style="width:23px;" title="Com(%)($/DZN)" placeholder="%" onChange="fnc_amount_calculation(9,0,0);" placeholder="Display" /></td>';
        var p=0;
		for(var y=1; y<=gmtName.length; y++)
		{
			html += '<td align="center"><input name="txtCommCost_[]" id="txtCommCost_'+trim(gmtId[p])+'" value="" class="text_boxes_numeric" style="width:40px;" onChange="fnc_amount_calculation(9,'+trim(gmtId[p])+',0);" placeholder="Write" /></td>';
			p++;
		}
        html += '<td id="totCommCst_td" align="right">&nbsp;</td></tr><tr><td>F.O.B($/DZN)</td>';
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
		fnc_specialAcc_reset();
		var gmts_name=gmtName[0];
		//var gmtId=trim(gmtId);
		//alert(gmtId);
 
		$('#item_tbl').find('input').each(function (index, element) {
			$('#'+this.id).attr('disabled',true);
		});

 		$('#item_tbl .tr'+gmtId).find('input').each(function (index, element) {
			$('#'+this.id).attr('disabled',false);
		});
		
		var fs=5;
		for(var k=1; k<=fs; k++)
		{
			$("#txt_consumtion"+k).val('');
			$("#txt_exper"+k).val('');
			$("#txt_totConsumtion"+k).val('');
			$("#txt_rate"+k).val('');
			$("#txt_value"+k).val('');
			
			document.getElementById('ratePop_'+k).checked==false
			$("#ratePop_"+k).val(2);
			$("#txtRateData_"+k).val('');
			
			$("#txt_spConsumtion"+k).val('');
			$("#txt_spexper"+k).val('');
			$("#txt_totSpConsumtion"+k).val('');
			$("#txtSpRate_"+k).val('');
			$("#txt_spValue"+k).val('');	
		}
		
		var acs=15;
		for(var q=1; q<=acs; q++)
		{
			$("#txt_accConsumtion"+q).val('');
			$("#txt_acexper"+q).val('');
			$("#txt_totAccConsumtion"+q).val('');
			$("#txt_acRate"+q).val('');
			$("#txt_acValue"+q).val('');
		}
		
		$("#txt_itemName").val(gmts_name);
		$('#txt_itemName').attr('disabled',true);
		
		$("#txt_itemId_tmp").val('');
		$("#txt_itemId").val(gmtId);
		if($("#txt_itemBodyData_"+gmtId).val()=='')
		{
			//if(gmtsId==20 || gmtsId==21 || gmtsId==22 || gmtsId==23 || gmtsId==28 || gmtsId==29 || gmtsId==30 || gmtsId==34 || gmtsId==35 || gmtsId==67 || gmtsId==70 || gmtsId==71)
			if(gmts_name.search('BTS'))
			{
				$("#cbo_head_1").val('20');
				$("#cbo_head_2").val('17');
				$("#cbo_head_3").val('21');
				$("#cbo_head_4").val('22');
				$("#cbo_head_5").val('997');
			}
			else
			{
				$("#cbo_head_1").val('1');
				$("#cbo_head_2").val('16');
				$("#cbo_head_3").val('21');
				$("#cbo_head_4").val('9');
				$("#cbo_head_5").val('996');
			}
			
			$('#cbo_head_1').attr('disabled',true);
			$('#cbo_head_2').attr('disabled',true);
			$('#cbo_head_3').attr('disabled',true);
			$('#cbo_head_4').attr('disabled',true);
			$('#cbo_head_5').attr('disabled',true);
			
			fnc_change_data($("#cbo_head_1").val(),1);
			fnc_change_data($("#cbo_head_2").val(),2);
			fnc_change_data($("#cbo_head_3").val(),3);
			fnc_change_data($("#cbo_head_4").val(),4);
			fnc_change_data($("#cbo_head_5").val(),5);
		}
		else if($("#txt_itemBodyData_"+gmtId).val()!='')
		{
			var txt_itemBodyData=$("#txt_itemBodyData_"+gmtId).val();
			var txt_itemBody=txt_itemBodyData.split("_");
			var txt_itemConsRateData=""; var itemConsRateData="";
			if($("#txt_itemConsRateData_"+gmtId).val()!="")
			{
				var txt_itemConsRateData=$("#txt_itemConsRateData_"+gmtId).val();
				var itemConsRateData=txt_itemConsRateData.split("##");
			}
			//alert(itemConsRateData.length);
			var txt_specialData=""; var specialData="";
			if($("#txt_itemConsRateData_"+gmtId).val()!="")
			{
				var txt_specialData=$("#txt_specialData_"+gmtId).val();
				var specialData=txt_specialData.split("##");
			}
			var txt_itemAccData=""; var itemAccData="";
			if($("#txt_itemConsRateData_"+gmtId).val()!="")
			{
				var txt_itemAccData=$("#txt_itemAccData_"+gmtId).val();
				var itemAccData=txt_itemAccData.split("##");
			}
			var accln=itemAccData.length-1;
			//alert(specialData.length)
			//var itemConsRate_fab=""; var specialData_sp="";
			var hd=5; var q=0;
			for(var j=1; j<=hd; j++)
			{
				$("#cbo_head_"+j).val(txt_itemBody[q]);
				$("#txt_head_id_"+j).val(txt_itemBody[q]);
				
				if(txt_itemBody[q]==0 || typeof(txt_itemBody[q])=="undefined")
				{
					//alert(j)
				}
				else
				{
					fnc_change_data(txt_itemBody[q],j);
				}
				//if(itemConsRateData.length>0) 
				var itemConsRate_fab=itemConsRateData[q].split("_");
				var consumption_fab=itemConsRate_fab[1]*1;
				var is_calculation_fab=itemConsRate_fab[2];
				var rate_fab=itemConsRate_fab[3]*1;
				var ex_per=(itemConsRate_fab[5]*1);
				var tot_cons_fab=(itemConsRate_fab[6]*1);
				//alert (itemConsRateData[q])
				if(consumption_fab!=0) $("#txt_consumtion"+j).val(number_format(consumption_fab,2,'.','' ));
				$("#ratePop_"+j).val(is_calculation_fab);
				if(rate_fab!=0) $("#txt_rate"+j).val(number_format(rate_fab,2,'.','' ));
				var fab_amount=0; var per_fab=0;
				per_fab=(consumption_fab*ex_per)/100;
				
				if(ex_per!=0) fab_amount=((consumption_fab+per_fab)*rate_fab);
				else fab_amount=(consumption_fab*rate_fab);
				//alert(consumption_fab+per_fab);
				if(fab_amount!=0) $("#txt_value"+j).val((number_format(fab_amount,2,'.','' )));
				$("#txtRateData_"+j).val(itemConsRate_fab[4]);
				if(ex_per!=0) $("#txt_exper"+j).val((number_format(ex_per,2,'.','' )));
				if(tot_cons_fab!=0) $("#txt_totConsumtion"+j).val(number_format(tot_cons_fab,2,'.','' ));
				$('#txt_value'+j).attr('disabled','disabled');
				//fnc_rate_write_popup(j);
				if(is_calculation_fab==1)
				{
					$('#txt_rate'+j).attr('readonly','readonly');
					var consumtion=$('#txt_consumtion'+j).val();
					document.getElementById('ratePop_'+j).checked=true;
					$('#txt_rate'+j).removeAttr("onDblClick").attr("onDblClick","fnc_openmypage_rate("+j+",'"+consumtion+"');");
				}
				else
				{
					$('#txt_rate'+j).removeAttr('readonly','readonly');
					document.getElementById('ratePop_'+j).checked=false;
					$('#txt_rate'+j).removeAttr("onDblClick");
				}
				//if(specialData.length>0) 
				var specialData_sp=specialData[q].split("_");
				var particular_type_sp=0; var consumption_sp=0; var spexper=''; var rate_sp=0; var tot_cons_sp=0;
				particular_type_sp=specialData_sp[0];
				consumption_sp=specialData_sp[1]*1;
				spexper=(specialData_sp[2]*1);
				rate_sp=specialData_sp[3]*1;
				tot_cons_sp=specialData_sp[4]*1;
				
				//alert(particular_type_sp+'='+j)
				$("#cboSpeciaOperationId_"+j).val(particular_type_sp);
				if(consumption_sp!=0)  $("#txt_spConsumtion"+j).val(number_format(consumption_sp,2,'.','' ));
				if(spexper!=0)  $("#txt_spexper"+j).val(number_format(spexper,2,'.','' ));
				if(tot_cons_sp!=0)  $("#txt_totSpConsumtion"+j).val(number_format(tot_cons_sp,2,'.','' ));
				//$("#txt_spUnit"+j).val(unit_sp);
				if(rate_sp!=0) $("#txtSpRate_"+j).val( number_format(rate_sp,2,'.','' ) );
				var tmp_val=0; var per_val=0;
				per_val=(consumption_sp*spexper)/100;
				if(spexper!=0) tmp_val=((consumption_sp+per_val)*rate_sp);
				else tmp_val=(consumption_sp*rate_sp);
				
				if( isNaN(tmp_val)==false )
				{
					if(tmp_val!=0) $("#txt_spValue"+j).val( number_format(tmp_val,2,'.','' ) );
				}
				else
					$("#txt_spValue"+j).val( 0 );
				
				$('#txt_spValue'+j).attr('disabled','disabled');
				q++;
			}
			if(accln==7) var accs=7; else var accs=15; 
			var adata=0;// itemAccData_ac='';
			for(var v=1; v<=accs; v++)
			{
				//if(itemAccData.length>0)
				var itemAccData_ac=itemAccData[adata].split("_");
				var particular_type_ac=itemAccData_ac[0];
				var consumption_ac=itemAccData_ac[1]*1;
				var exper_ac=itemAccData_ac[2]*1;
				var rate_ac=itemAccData_ac[3]*1;
				var tot_cons_acc=itemAccData_ac[4]*1;
				
				$("#cbo_accessories_id"+v).val(particular_type_ac);
				if(consumption_ac!=0) $("#txt_accConsumtion"+v).val(number_format(consumption_ac,2,'.','' ));
				if(exper_ac!=0) $("#txt_acexper"+v).val(number_format(exper_ac,2,'.','' ));
				if(tot_cons_acc!=0) $("#txt_totAccConsumtion"+v).val(number_format(tot_cons_acc,2,'.','' ));
				//$("#txt_acUnit"+j).val(unit_ac);
				if(rate_ac!=0) $("#txt_acRate"+v).val(number_format(rate_ac,2,'.','' ));
				var tmpac_val=0;
				if(exper_ac!=0) tmpac_val=((consumption_ac+((consumption_ac*exper_ac)/100))*rate_ac);
				else tmpac_val=(consumption_ac*rate_ac);
				
				if( isNaN(tmpac_val)==false )
				{
					if(tmpac_val!=0) $("#txt_acValue"+v).val(number_format(tmpac_val,2,'.','' ));
				}
				else
					$("#txt_acValue"+v).val( 0 );
					
				$('#txt_acValue'+v).attr('disabled','disabled');
					
				adata++;
			}
		}
		fnc_itemwise_data_cache(gmtId);
		navigate_arrow_key();
		fnc_consumption_write_disable( $("#cbo_cons_basis_id").val()+"__"+0 );
	}
	
	function fnc_change_data(val,hd_no)
	{
		var hd_dorp_down_val="";
		hd_dorp_down_val=get_dropdown_text('cbo_head_'+hd_no);
		$("#txt_head"+hd_no).val(trim(hd_dorp_down_val));
		$("#txt_head_id_"+hd_no).val(val);
		$("#fomula_td"+hd_no).html(hd_dorp_down_val);
		$('#txt_head'+hd_no).attr('disabled',true);
	}
	
	function fnc_formula_bilder(id)
	{
		var item_id=$('#txt_temp_id').val();
		var gmtId=item_id.split(",");
		var id_item=''; var k=0;
		for(var l=1; l<=gmtId.length; l++)
		{
			id_item=$('#txt_itemId_'+gmtId[k]).val();
		}
		//alert (id_item)
		if($('#txt_head'+id).val()!="")
		{
			var data="";
			var title = 'Formula Bilder';	
			var page_link = 'requires/quick_costing_woven_controller.php?&action=formulaBilder_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=980px,height=400px,center=1,resize=1,scrolling=0','../');
		}
	}
	
	function fnc_consumption_calculation(item_id, row_id, col_id, val, aw)
	{
		var itm_name=$("#txt_itemName").val();
		var chk_itm_id=$("#txt_itemId").val();
		if(item_id!=chk_itm_id)
		{
			alert("Please First Click in Item Name.");
			return;
		}
		
		var uom=$("#cbouom").val();
		var cons_basis_id=$("#cbo_cons_basis_id").val();
		if(uom==0)
		{
			alert("Please Select Cons. UOM");
			return;	
		}
		
		if(cons_basis_id==0)
		{
			alert("Please Select Cons. Basis");
			return;	
		}
		$("#cbo_temp_id").attr('disabled',true);
		$("#cbouom").attr('disabled',true);
		$("#cbo_cons_basis_id").attr('disabled',true);
		
		var cm_inch=0;
		if(cons_basis_id==2) cm_inch=1; else if(cons_basis_id==4) cm_inch=2.54; else cm_inch=0;
		
		if(cons_cond==2) var wastage_per=1; else var wastage_per=1.05;
		
		if(col_id==1)
		{
			$("#txt_consumtion1").val('');
			var body_length=(($("#txtVal_"+item_id+"_1_1").val()*1)+($("#txtAw_"+item_id+"_1_1").val()*1))*cm_inch;
			var sleeve_length=(($("#txtVal_"+item_id+"_2_1").val()*1)+($("#txtAw_"+item_id+"_2_1").val()*1))*cm_inch;
			//alert(cm_inch)
			var fabric_weight=(($("#txtVal_"+item_id+"_4_1").val()*1)+($("#txtAw_"+item_id+"_4_1").val()*1));
			//alert(body_length+'__'+sleeve_length+'__'+fabric_weight);
			var half_chest=0;
			var item_cons_body=0;
			if(itm_name.search('BTS'))
			{
				var btm_item_cons_body=((body_length*sleeve_length)*4)
				var item_cons_body=(btm_item_cons_body*12)*wastage_per*fabric_weight; 
				//alert(btm_item_cons_body)
			}
			else
			{
				var half_chest=(($("#txtVal_"+item_id+"_3_1").val()*1)+($("#txtAw_"+item_id+"_3_1").val()*1))*cm_inch;
				var item_cons_body=(((((body_length+sleeve_length)*(half_chest*2))*12)*wastage_per)*fabric_weight);
				//alert(half_chest+'__'+item_cons_body)
			}
			var body_consp=0;
			var body_consp=(item_cons_body/10000000);
			//alert(body_consp)
			var fab_fth_row=(($("#txtVal_"+item_id+"_5_1").val()*1)+($("#txtAw_"+item_id+"_5_1").val()*1));
			var uom_str_val=0;
			if(uom==23) 
			{
				uom_str_val= ((39.37*fab_fth_row) /1550)*(fabric_weight/1000);
			} 
			else if(uom==27) 
			{
				uom_str_val =((36*fab_fth_row) /1550)*(fabric_weight/1000);
			} 
			else uom_str_val=0;
			
			//alert(body_consp);
			if(uom==23 || uom==27)
			{
				var fab_inc=body_consp/uom_str_val;
				var fab_width_inch_yds=number_format(fab_inc,8,'.','' );
				body_cons=fab_width_inch_yds;
				//alert(fab_inc+'__'+fab_width_inch_yds);
			}
			else { body_cons=body_consp; }
			
			$("#txt_consumtion1").val( number_format(body_cons,2,'.','' ) );
		}
		else if(col_id==2)
		{
			$("#txt_consumtion2").val('');
			var rib_length=(($("#txtVal_"+item_id+"_1_2").val()*1)+($("#txtAw_"+item_id+"_1_2").val()*1))*cm_inch;
			var rib_weight=(($("#txtVal_"+item_id+"_2_2").val()*1)+($("#txtAw_"+item_id+"_2_2").val()*1))*cm_inch;
			//var half_chest=(($("#txtVal_"+item_id+"_3_1").val()*1)+($("#txtAw_"+item_id+"_3_1").val()*1));
			var fabric_weight_rib=(($("#txtVal_"+item_id+"_4_2").val()*1)+($("#txtAw_"+item_id+"_4_2").val()*1));
			var item_cons_rib=(((((rib_length*rib_weight)*2)*12)*wastage_per)*fabric_weight_rib);
			var rib_cons=0;
			rib_cons=(item_cons_rib/10000000);
			//alert(rib_cons)
			var rib_uom_str_val=0;
			var rib_fth_row=(($("#txtVal_"+item_id+"_5_2").val()*1)+($("#txtAw_"+item_id+"_5_2").val()*1));
			
			if(uom==23) 
			{
				rib_uom_str_val= ((39.37*rib_fth_row) /1550)*(fabric_weight_rib/1000);
			} 
			else if(uom==27) 
			{
				rib_uom_str_val =((36*rib_fth_row) /1550)*(fabric_weight_rib/1000);
			} 
			else rib_uom_str_val=0;
			
			if(uom==23 || uom==27)
			{
				var rib_inc=rib_cons/rib_uom_str_val;
				var rib_width_inch_yds=number_format(rib_inc,8,'.','' );
				rib_cons=rib_width_inch_yds;
				//alert(fab_inc+'__'+fab_width_inch_yds);
			}
			else { rib_cons=rib_cons; }
			
			/*if(uom==23) 
			{
				rib_uom_str_val=((39.37*fab_fth_row) /1550)*(fabric_weight/1000)
				var rib_width_inch=(rib_cons/(39.37*rib_fth_row));
				var rib_width_in=(rib_width_inch/1550);
				rib_cons=rib_width_in*(rib_weight/1000);
			}
			else if(uom==27) 
			{
				var rib_width_yds=(rib_cons/(36*rib_fth_row));
				var rib_width_yd=(rib_width_yds/1550);
				rib_cons=rib_width_yd*(rib_weight/1000);
			}
			else rib_cons=rib_cons;*/
			
			$("#txt_consumtion2").val( number_format(rib_cons,2,'.','' ) );
		}
		else //
		{
			$("#txt_consumtion"+col_id).val('');
			//(POCKET LENGTH_V+POCKET LENGTH_A)*(RIB WIDTH_V+RIB WIDTH_A)*2/10000*12*1.05*(FABRIC WEIGHT_V+FABRIC WEIGHT_A)/1000
			var pocket_length=(($("#txtVal_"+item_id+"_1_"+col_id).val()*1)+($("#txtAw_"+item_id+"_1_"+col_id).val()*1))*cm_inch;
			var pocket_weight=(($("#txtVal_"+item_id+"_2_"+col_id).val()*1)+($("#txtAw_"+item_id+"_2_"+col_id).val()*1))*cm_inch;
			//var half_chest=(($("#txtVal_"+item_id+"_3_1").val()*1)+($("#txtAw_"+item_id+"_3_1").val()*1)); 
			var fabric_weight_pocket=(($("#txtVal_"+item_id+"_4_"+col_id).val()*1)+($("#txtAw_"+item_id+"_4_"+col_id).val()*1));
			var item_cons_pocket=(((((pocket_length*pocket_weight)*2)*12)*wastage_per)*fabric_weight_pocket);
			var pocket_cons=0;
			pocket_cons=(item_cons_pocket/10000000);
			//alert(item_id+"_1_"+col_id)
			
			if(col_id==3 || col_id==4)
			{
				var pocket_uom_str_val=0;
				var pocket_fth_row=(($("#txtVal_"+item_id+"_5_"+col_id).val()*1)+($("#txtAw_"+item_id+"_5_"+col_id).val()*1));
				
				if(uom==23) 
				{
					pocket_uom_str_val= ((39.37*pocket_fth_row) /1550)*(fabric_weight_pocket/1000);
				} 
				else if(uom==27) 
				{
					pocket_uom_str_val =((36*pocket_fth_row) /1550)*(fabric_weight_pocket/1000);
				} 
				else pocket_uom_str_val=0;
				
				if(uom==23 || uom==27)
				{
					var pocket_inc=pocket_cons/pocket_uom_str_val;
					var pocket_width_inch_yds=number_format(pocket_inc,8,'.','' );
					pocket_cons=pocket_width_inch_yds;
					//alert(pocket_cons+'__'+pocket_width_inch_yds);
				}
				else { pocket_cons=pocket_cons; }
				
				/*if(uom==23) 
				{
					var pocket_width_inch=(pocket_cons/(39.37*pocket_fth_row));
					var pocket_width_in=(pocket_width_inch/1550);
					pocket_cons=pocket_width_in*(pocket_weight/1000);
				}
				else if(uom==27) 
				{
					var pocket_width_yds=(pocket_cons/(36*pocket_fth_row));
					var pocket_width_yd=(pocket_width_yds/1550);
					pocket_cons=pocket_width_yd*(pocket_weight/1000);
				}
				else pocket_cons=pocket_cons;*/
			}
			else pocket_cons=pocket_cons;
			$("#txt_consumtion"+col_id).val( number_format(pocket_cons,2,'.','' ) );
		}
		//}
		fnc_itemwise_data_cache( item_id );
		fnc_amount_calculation('fabric',col_id,'0');
	}
	
	function fnc_amount_calculation(type,inc_id,rate)
	{
		var item_id=$("#txt_itemId").val();
		var hid_all_item_id=$("#txt_temp_id").val();
		var all_item_id_hid=hid_all_item_id.split(",");
		//alert(hid_all_item_id+'='+inc_id)
		var itemWiseTot_arr = new Array();
		
		if (type=='fabric')
		{
			var cons_val=0; var row_tot_value=''; var ex_per=""; var rate=0;
			cons_val=$("#txt_consumtion"+inc_id).val()*1;
			ex_per=$("#txt_exper"+inc_id).val()*1;
			if(rate=="" || rate==0)
			{
				rate=($("#txt_rate"+inc_id).val()*1);
			}
			var ex_fab_cons=0;
			if(ex_per!=0) ex_fab_cons=((cons_val*1)+((cons_val*ex_per)/100));
			else ex_fab_cons=(cons_val*1);
			$("#txt_totConsumtion"+inc_id).val( number_format(ex_fab_cons,2,'.','') );
			//alert(ex_fab_cons);
			row_tot_value=(ex_fab_cons)*rate;
			$("#txt_value"+inc_id).val( number_format(row_tot_value,2,'.','') );
			
			var fab_item_cost=0;
			for(var i=1; i<=5; i++)
			{
				fab_item_cost=fab_item_cost+($("#txt_value"+i).val()*1);
			}

			$("#fab_td"+item_id).text( number_format(fab_item_cost,2,'.','') );
			var r=0; var item_fob=0; var row_fab_cost=0;
			for(var s=1; s<=all_item_id_hid.length; s++)
			{
				row_fab_cost=row_fab_cost+($("#fab_td"+trim(all_item_id_hid[r])).text()*1);
				r++;
			}
			//alert(row_fab_cost)
			
			$("#totFab_td").text( number_format(row_fab_cost,2,'.',''));
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
			$("#txt_totSpConsumtion"+inc_id).val( number_format(ex_sp_cons,2,'.','') );
			sp_rate=$("#txtSpRate_"+inc_id).val();
			row_tot_value=(ex_sp_cons*1)*sp_rate;
			$("#txt_spValue"+inc_id).val(number_format(row_tot_value,2,'.',''));
			
			var sp_item_cost=0;
			for(var i=1; i<=5; i++)
			{
				sp_item_cost=sp_item_cost+($("#txt_spValue"+i).val()*1);
			}
			$("#spOpe_td"+item_id).text( number_format(sp_item_cost,2,'.','') );
			var t=0; var item_fob=0; var row_special_cost=0;
			for(var p=1; p<=all_item_id_hid.length; p++)
			{
				row_special_cost=row_special_cost+($("#spOpe_td"+trim(all_item_id_hid[t])).text()*1);
				t++;
			}
			$("#totSpc_td").text( number_format(row_special_cost,2,'.',''));
		}
		else if (type=='accessories')
		{
			var cons_val=''; var row_tot_value=''; var ex_acper="";
			cons_val=$("#txt_accConsumtion"+inc_id).val();
			ex_acper=$("#txt_acexper"+inc_id).val()*1;
			var ex_ac_cons=0;
			if(ex_acper!=0) ex_ac_cons=((cons_val*1)+((cons_val*ex_acper)/100));
			else ex_ac_cons=(cons_val*1);
			$("#txt_totAccConsumtion"+inc_id).val( number_format(ex_ac_cons,2,'.','') );
			
			row_tot_value=(ex_ac_cons*1)*rate;
			$("#txt_acValue"+inc_id).val(number_format(row_tot_value,2,'.',''));
			
			var acc_item_cost=0;
			for(var i=1; i<=15; i++)
			{
				acc_item_cost=acc_item_cost+($("#txt_acValue"+i).val()*1);
			}
			$("#acc_td"+item_id).text( number_format(acc_item_cost,2,'.','') );
			var t=0; var item_fob=0; var row_accessories_cost=0;
			for(var p=1; p<=all_item_id_hid.length; p++)
			{
				row_accessories_cost=row_accessories_cost+($("#acc_td"+trim(all_item_id_hid[t])).text()*1);
				t++;
			}
			$("#totAcc_td").text( number_format(row_accessories_cost,2,'.',''));
		}
		else if (type=='3')
		{
			var cc=1; var e=0; var item_cm=0; var row_cm_cost=0; var cpm_from_financial_parameter=0; 
			if($("#cmPop").val()==1)
			{
				var costing_date=$("#txt_costingDate").val();
				if(cc==1)
				{
					//var cpm_from_financial_parameter=return_ajax_request_value(costing_date, 'cpm_check_load', 'requires/quick_costing_woven_controller');
					cc++;
				}
				
				var cpm=0; var smv=0; var eff=0; 
				var exchange_rate=($("#txt_exchangeRate").val()*1);
				cpm=($('#txt_cpm_'+inc_id).val()*1);
				smv=($('#txt_smv_'+inc_id).val()*1);
				eff=($('#txt_eff_'+inc_id).val()*1);
				
				//var cm_cost=((smv*cpm_from_financial_parameter)*12+(smv*cpm_from_financial_parameter*12)*eff)/exchange_rate;
				var cm_cost=(((cpm*100)/eff)*smv)*12;
				$("#txtCmCost_"+inc_id).val( number_format(cm_cost,2,'.',''));
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
			$("#totCm_td").text( number_format(row_cm_cost,2,'.',''));
		}
		else if (type=='5')
		{
			var g=0; var item_cm=0; var row_fri_cost=0;
			for(var h=1; h<=all_item_id_hid.length; h++)
			{
				row_fri_cost=row_fri_cost+($("#txtFriCost_"+trim(all_item_id_hid[g])).val()*1);
				g++;
			}
			$("#totFriCst_td").text( number_format(row_fri_cost,2,'.',''));
		}
		else if (type=='6')
		{
			var l=0; var item_cm=0; var row_lab_cost=0;
			for(var m=1; m<=all_item_id_hid.length; m++)
			{
				row_lab_cost=row_lab_cost+($("#txtLtstCost_"+trim(all_item_id_hid[l])).val()*1);
				l++;
			}
			$("#totLbTstCst_td").text( number_format(row_lab_cost,2,'.',''));
		}
		else if (type=='7')
		{
			var lum_sum_cost=($("#txt_lumSum_cost").val()*1);
			var offer_qty=($("#txt_offerQty").val()*1);
			if($("#txt_offerQty").val()!="")
			{
				var item_wise_miss_cost=(lum_sum_cost/offer_qty)*12;
				
				var aa=0; var row_miss_cost=0;
				for(var ab=1; ab<=all_item_id_hid.length; ab++)
				{
					//alert("#txtMissCost_"+trim(all_item_id_hid[aa]))
					$("#txtMissCost_"+trim(all_item_id_hid[aa])).val( number_format(item_wise_miss_cost,2,'.','') );
					row_miss_cost=row_miss_cost+($("#txtMissCost_"+trim(all_item_id_hid[aa])).val()*1);
					aa++;
				}
				$("#totMissCst_td").text( number_format(row_miss_cost,2,'.',''));
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
			$("#totOtherCst_td").text( number_format(row_other_cost,2,'.',''));
		}
		else if (type=='9')
		{
			var aa=0; var itemFoBarr=new Array();
			for(var bb=1; bb<=all_item_id_hid.length; bb++)
			{
				var before_commission_cost=0; var itemWiseTot=0; var commissson_cost=0; var commPer=0; var mis_cost=0;
				
				mis_cost=(($("#txt_lumSum_cost").val()*1)/($("#txt_offerQty").val()*1))*12;
				
				$("#txtMissCost_"+trim(all_item_id_hid[aa])).val( number_format(mis_cost,2,'.','') );
				
				before_commission_cost=($("#fab_td"+trim(all_item_id_hid[aa])).text()*1)+($("#spOpe_td"+trim(all_item_id_hid[aa])).text()*1)+($("#acc_td"+trim(all_item_id_hid[aa])).text()*1)+($("#txtCmCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtFriCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtLtstCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtMissCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtOtherCost_"+trim(all_item_id_hid[aa])).val()*1);
				commPer=($("#txt_commPer").val()*1)/100;
				//alert("#txtCommCost_"+trim(all_item_id_hid[aa]));
				commissson_cost=((before_commission_cost/(1-commPer))-before_commission_cost);
				$("#txtCommCost_"+trim(all_item_id_hid[aa])).val( number_format(commissson_cost,2,'.','') );
				itemWiseTot=before_commission_cost+($("#txtCommCost_"+trim(all_item_id_hid[aa])).val()*1);
				var item_id_hid=trim(all_item_id_hid[aa]);
				itemFoBarr[item_id_hid]=itemWiseTot;
				//$("#fobT_td"+trim(all_item_id_hid[aa])).text( number_format(itemWiseTot,2,'.',''));
				//
				aa++;
			}
			
			var p=0; var item_cm=0; var row_commision_cost=0;
			for(var q=1; q<=all_item_id_hid.length; q++)
			{
				row_commision_cost=row_commision_cost+($("#txtCommCost_"+trim(all_item_id_hid[p])).val()*1);
				p++;//alert(row_commision_cost);
			}
			
			$("#totCommCst_td").text( number_format(row_commision_cost,2,'.',''));
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
		//alert(all_item_id_hid)
		var aa=0; var itemFoBarr=new Array(); var val_td_fob=0; var totCommiss_Cost=0;
		for(var bb=1; bb<=all_item_id_hid.length; bb++)
		{
			var before_commission_cost=0; var itemWiseTot=0; var commissson_cost=0; var commPer=0; var mis_cost=0;
			
			mis_cost=(($("#txt_lumSum_cost").val()*1)/($("#txt_offerQty").val()*1))*12;
			
			$("#txtMissCost_"+trim(all_item_id_hid[aa])).val( number_format(mis_cost,2,'.','') );
			
			before_commission_cost=($("#fab_td"+trim(all_item_id_hid[aa])).text()*1)+($("#spOpe_td"+trim(all_item_id_hid[aa])).text()*1)+($("#acc_td"+trim(all_item_id_hid[aa])).text()*1)+($("#txtCmCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtFriCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtLtstCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtMissCost_"+trim(all_item_id_hid[aa])).val()*1)+($("#txtOtherCost_"+trim(all_item_id_hid[aa])).val()*1);
			commPer=($("#txt_commPer").val()*1)/100;
			//alert("#txtCommCost_"+trim(all_item_id_hid[aa]));
			commissson_cost=((before_commission_cost/(1-commPer))-before_commission_cost);
			totCommiss_Cost+=commissson_cost;
			$("#txtCommCost_"+trim(all_item_id_hid[aa])).val( number_format(commissson_cost,2,'.','') );
			itemWiseTot=before_commission_cost+($("#txtCommCost_"+trim(all_item_id_hid[aa])).val()*1);
			var item_id_hid=trim(all_item_id_hid[aa]);
			itemFoBarr[item_id_hid]=itemWiseTot;
			$("#fobT_td"+trim(all_item_id_hid[aa])).text( number_format(itemWiseTot,2,'.',''));
			$("#fobPcsT_td"+trim(all_item_id_hid[aa])).text( number_format(itemWiseTot/12,2,'.',''));
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
		var tot_cost=($("#totFab_td").text()*1)+($("#totSpc_td").text()*1)+($("#totAcc_td").text()*1)+($("#totCm_td").text()*1)+($("#totFriCst_td").text()*1)+($("#totLbTstCst_td").text()*1)+($("#totMissCst_td").text()*1)+($("#totOtherCst_td").text()*1)+($("#totCommCst_td").text()*1);
		$("#totCost_td").text( number_format(tot_cost,2,'.','') );
		$("#totFOBCost_td").text( number_format(tot_cost/12,2,'.','') );
		var tot_fob_cost=(val_td_fob*($("#txt_noOfPack").val()*1));
		$("#totalFob_td").text( number_format((tot_fob_cost),2,'.','') );
		$("#totCommCst_td").text( number_format(totCommiss_Cost,2,'.',''));
		
		fnc_itemwise_data_cache(trim( $("#txt_itemId").val()));
	}
	
	function fnc_meeting_remarks_pop_up(costSheetId,styleRef)
	{
		var title = 'Meeting Remarks Form';	
		var page_link = 'requires/quick_costing_woven_controller.php?action=meeting_remarks_popup';
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
	
	function fnc_rate_write_popup(inc_id)
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
			if(document.getElementById('ratePop_'+inc_id).checked==true)
			{
				var consumtion=$('#txt_consumtion'+inc_id).val();
				document.getElementById('ratePop_'+inc_id).value=1;
				$('#txt_rate'+inc_id).val('');
				$('#txtRateData_'+inc_id).val('');
				$('#txt_rate'+inc_id).attr('readonly','readonly');
				$('#txt_rate'+inc_id).attr('readonly','readonly');
				$('#txt_rate'+inc_id).attr("placeholder", "Browse");
				$('#txt_rate'+inc_id).removeAttr("onDblClick").attr("onDblClick","fnc_openmypage_rate("+inc_id+",'"+consumtion+"');");
			}
			else if(document.getElementById('ratePop_'+inc_id).checked==false)
			{
				document.getElementById('ratePop_'+inc_id).value=2;
				$('#txt_rate'+inc_id).val('');
				$('#txtRateData_'+inc_id).val('');
				$('#txt_rate'+inc_id).removeAttr('readonly','readonly');
				$('#txt_rate'+inc_id).attr("placeholder", "Write");
				$('#txt_rate'+inc_id).removeAttr("onDblClick");
			}
		}
	}
	
	function fnc_openmypage_rate(inc_id,cons)
	{
		var styleRef=$('#txt_styleRef').val();
		var rateData=$('#txtRateData_'+inc_id).val();
		var title = 'Rate Details PopUp';	
		var page_link = 'requires/quick_costing_woven_controller.php?action=rate_details_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&styleRef='+styleRef+'&cons='+cons+'&rateData='+rateData, title, 'width=550px,height=380px,center=1,resize=0,scrolling=0','../')
		release_freezing();
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var pop_rate=this.contentDoc.getElementById("txt_tot_rate").value;	 //Access form field with id="emailfield"
			var all_rate_data=this.contentDoc.getElementById("hidden_all_data").value;
			$('#txt_rate'+inc_id).val( number_format(pop_rate,2,'.','') );
			$('#txtRateData_'+inc_id).val( all_rate_data );
			//$('#txt_value'+inc_id).val( cons*all_rate_data );
			fnc_amount_calculation('fabric', inc_id, pop_rate);
		}
	}
	
	function fnc_new_stage_popup()
	{
		var title = 'Stage Entry/Update PopUp';	
		var page_link = 'requires/quick_costing_woven_controller.php?action=stage_saveUpdate_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=600px,height=380px,center=1,resize=0,scrolling=0','../')
		release_freezing();
		emailwindow.onclose=function()
		{
			load_drop_down( 'requires/quick_costing_woven_controller', '', 'load_drop_stage_name', 'stage_td');
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
	
	function fnc_itemwise_data_cache( item_id )
	{
		//alert(item_id)
		//var temp_id=$('#txt_temp_id').val();
		//alert('#txt_specialData_'+item_id)
		//var split_tmep_id=temp_id.split(',');
		//$('#txt_specialData_'+item_id).val('');
		//var all_data_string="";
		var body_part_data=$('#cbo_head_1').val()+'_'+$('#cbo_head_2').val()+'_'+$('#cbo_head_3').val()+'_'+trim($('#cbo_head_4').val())+'_'+trim($('#cbo_head_5').val());
		var hd=9; var description_value_alw_data=''; 
		for(var m=1; m<=hd; m++)
		{
			var trLen=5;
			for(var j=1; j<=trLen; j++)
			{
				description_value_alw_data+= $('#txtItemDes_'+item_id+'_'+m+'_'+j).val()+'_'+$('#txtVal_'+item_id+'_'+m+'_'+j).val()+'_'+$('#txtAw_'+item_id+'_'+m+'_'+j).val()+'##';
			}
		} //alert ( cons_rate_data ); return;
		
		
		var sp_row=5; var specialOpt_data=''; var cons_rate_data='';
		for(var n=1; n<=sp_row; n++)
		{
			cons_rate_data+= ($('#txt_head_id_'+n).val()*1)+'_'+($('#txt_consumtion'+n).val()*1)+'_'+($('#ratePop_'+n).val()*1)+'_'+($('#txt_rate'+n).val()*1)+'_'+$('#txtRateData_'+n).val()+'_'+$('#txt_exper'+n).val()+'_'+$('#txt_totConsumtion'+n).val()+'##';
			
			specialOpt_data+= $('#cboSpeciaOperationId_'+n).val()+'_'+($('#txt_spConsumtion'+n).val()*1)+'_'+($('#txt_spexper'+n).val()*1)+'_'+($('#txtSpRate_'+n).val()*1)+'_'+($('#txt_totSpConsumtion'+n).val()*1)+'##';
		}
		// alert( item_id_tmp+'=='+item_id );
		var acc=15; var accessories_cons_data="";
		for(var q=1; q<=acc; q++)
		{
			accessories_cons_data+= ($('#cbo_accessories_id'+q).val()*1)+'_'+($('#txt_accConsumtion'+q).val()*1)+'_'+($('#txt_acexper'+q).val()*1)+'_'+($('#txt_acRate'+q).val()*1)+'_'+($('#txt_totAccConsumtion'+q).val()*1)+'##';
		}
		//all_data_string=body_part_data+****+description_value_alw_data+****+cons_rate_data+****+special_opt_data+****+accessories_cons_data;
		//alert(cons_rate_data);
		
		$('#txt_itemBodyData_'+item_id).val( body_part_data ); 
		$('#txt_itemDVAData_'+item_id).val( description_value_alw_data );
		$('#txt_itemConsRateData_'+item_id).val( cons_rate_data );
		//alert(specialOpt_data_arr[item_id])
		$('#txt_specialData_'+item_id).val( specialOpt_data );
		$('#txt_itemAccData_'+item_id).val( accessories_cons_data );
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
			$('#txt_accConsumtion'+q).val('');
			$('#txt_acexper'+q).val('');
			$('#txt_acRate'+q).val('');
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
		var type=0;
		fnc_itemwise_data_cache(prev_item_id);
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
		
		if( ($('#totalFob_td').text()*1)==0 )
		{
			alert("Please fill up F.O.B $.");
			release_freezing();	
			return;
		}
		
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
		
		if (form_validation('cbo_temp_id*cbo_cons_basis_id*txt_exchangeRate*cbo_buyer_id*cbo_season_id*cbo_season_year*cbo_brand*txt_styleRef*txt_offerQty*txt_costingDate','Template Name*Cons. Basis*Exchange Rate*Buyer Name*Season*Season Year*Brand*Style Ref.*Offer Qty*Costing Date')==false)
		{
			return;
		}	
		else
		{
			if(operation==4)
			{
				var report_title=$( "div.form_caption" ).html();
				generate_report_file( $('#hid_qc_no').val()+'*'+$('#txt_costSheetNo').val()+'*'+report_title,'quick_costing_print','requires/quick_costing_woven_controller');
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
						return;
					}
					
					if( $('#cbo_buyer_agent').val()==0 && $('#cbo_agent_location').val()==0)
					{
						alert("Please fill up meeting Buyer agent and location.");
						return;
					}
				}
				
				var temp_id=$('#txt_temp_id').val();
				var split_tmep_id=temp_id.split(',');
				
				var ae=0; var detls_data=''; var data_tot_cost_summ= ''; var description_value_alw_data=''; var cons_rate_data=''; var item_wise_tot_data='';
				for(i=1; i<=split_tmep_id.length; i++)
				{
					var itm_id=trim(split_tmep_id[ae]);
					detls_data+=get_submitted_data_string('txt_itemBodyData_'+itm_id+'*txt_itemDVAData_'+itm_id+'*txt_itemConsRateData_'+itm_id+'*txt_specialData_'+itm_id+'*txt_itemAccData_'+itm_id,"../../",2);//
					// alert(detls_data);   
					var hd=9;
					for(var m=1; m<=hd; m++)
					{
						var trLen=5;
						for(var j=1; j<=trLen; j++)
						{
							description_value_alw_data+= get_submitted_data_string('txtItemDes_'+itm_id+'_'+m+'_'+j+'*txtVal_'+itm_id+'_'+m+'_'+j+'*txtAw_'+itm_id+'_'+m+'_'+j+'*txtdvaId_'+itm_id+'_'+m+'_'+j,"../../",2);
						}
					}
					item_wise_tot_data+=($('#fab_td'+itm_id).text()*1)+'_'+($('#spOpe_td'+itm_id).text()*1)+'_'+($('#acc_td'+itm_id).text()*1)+'_'+($('#txt_cpm_'+itm_id).val()*1)+'_'+($('#txt_smv_'+itm_id).val()*1)+'_'+($('#txt_eff_'+itm_id).val()*1)+'_'+($('#txtCmCost_'+itm_id).val()*1)+'_'+($('#txtFriCost_'+itm_id).val()*1)+'_'+($('#txtLtstCost_'+itm_id).val()*1)+'_'+($('#txtMissCost_'+itm_id).val()*1)+'_'+($('#txtOtherCost_'+itm_id).val()*1)+'_'+($('#txtCommCost_'+itm_id).val()*1)+'_'+($('#fobT_td'+itm_id).text()*1)+'_'+($('#txtRmgQty_'+itm_id).val()*1)+'_'+itm_id+'__';
					
					ae++;
				}
				//alert(description_value_alw_data);   return;
				//alert(item_wise_tot_data);
				var data_tot_cost_summ=($('#cbo_buyer_agent').val()*1)+'_'+($('#cbo_agent_location').val()*1)+'_'+($('#txt_noOfPack').val()*1)+'_'+($('#cmPop').val()*1)+'_'+($('#txt_lumSum_cost').val()*1)+'_'+($('#txt_commPer').val()*1)+'_'+($('#totFab_td').text()*1)+'_'+($('#totSpc_td').text()*1)+'_'+($('#totAcc_td').text()*1)+'_'+($('#totCm_td').text()*1)+'_'+($('#totFriCst_td').text()*1)+'_'+($('#totLbTstCst_td').text()*1)+'_'+($('#totMissCst_td').text()*1)+'_'+($('#totOtherCst_td').text()*1)+'_'+($('#totCommCst_td').text()*1)+'_'+($('#totCost_td').text()*1)+'_'+($('#totalFob_td').text()*1)+'_'+($('#totRmgQty_td').text()*1);
				
				var data_mst="action=save_update_delete&operation="+operation+"&data_tot_cost_summ="+data_tot_cost_summ+"&item_wise_tot_data="+item_wise_tot_data+"&type="+type+get_submitted_data_string('cbo_temp_id*txt_temp_id*txt_inquery_id*txt_styleRef*txt_costSheetNo*txt_update_id*cbo_buyer_id*cbouom*cbo_cons_basis_id*cbo_season_id*cbo_season_year*cbo_brand*txt_styleDesc*cbo_subDept_id*txt_delivery_date*txt_exchangeRate*txt_offerQty*txt_quotedPrice*txt_tgtPrice*cbo_stage_id*txt_costingDate*txt_costing_remarks*cbo_revise_no*cbo_option_id*txt_option_remarks*txt_meeting_date*txt_meeting_time*chk_is_new_meeting*txt_meeting_remarks*txt_meeting_no*hid_qc_no',"../../");
				
				var data=data_mst+detls_data+description_value_alw_data;
				/*alert(data_mst);
				return;*/
				freeze_window(operation);
				http.open("POST","requires/quick_costing_woven_controller.php",true);
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
					var temp_style_list=return_ajax_request_value(reponse[2]+'__'+1, 'temp_style_list_view', 'requires/quick_costing_woven_controller');
					$('#style_td').html( temp_style_list );
					var user_id='<? echo $_SESSION['logic_erp']['user_id']; ?>';
					//get_php_form_data(reponse[1]+"__"+user_id+"__"+reponse[2]+"__"+0+"***"+0, 'populate_style_details_data', 'requires/quick_costing_woven_controller');
					load_drop_down('requires/quick_costing_woven_controller', reponse[2]+'__'+reponse[4]+'__'+reponse[3], 'load_drop_down_revise_no', 'revise_td');
					load_drop_down('requires/quick_costing_woven_controller', reponse[2]+'__'+reponse[4]+'__'+reponse[4], 'load_drop_down_option_id', 'option_td');
					set_onclick_style_list(reponse[2]+'__'+user_id+'__'+reponse[2]);
				}
				if(reponse[5]==6) //Revise
				{
					load_drop_down('requires/quick_costing_woven_controller', reponse[2]+'__'+reponse[4]+'__0', 'load_drop_down_revise_no', 'revise_td');
					load_drop_down('requires/quick_costing_woven_controller', reponse[2]+'__'+reponse[4]+'__'+reponse[4], 'load_drop_down_option_id', 'option_td');
					set_onclick_style_list(reponse[2]+'__'+user_id+'__'+reponse[2]);
				}
				else if(  reponse[5]==7) //Option
				{	
					load_drop_down('requires/quick_costing_woven_controller', reponse[2]+'__'+reponse[4]+'__'+$('#cbo_revise_no').val(), 'load_drop_down_revise_no', 'revise_td');
					load_drop_down('requires/quick_costing_woven_controller', reponse[2]+'__'+reponse[4]+'__0', 'load_drop_down_option_id', 'option_td');
					
					//load_drop_down('requires/quick_costing_woven_controller', reponse[2]+'__'+reponse[4]+'__'+$('#cbo_revise_no').val(), 'load_drop_down_revise_no', 'revise_td');
					//load_drop_down('requires/quick_costing_woven_controller', reponse[2]+'__'+reponse[4]+'__0', 'load_drop_down_option_id', 'option_td');
					set_onclick_style_list(reponse[2]+'__'+user_id+'__'+reponse[2]);
				}
			}
			else if (reponse[0]==2)
			{
				reset_fnc();
				return;
			}
			
			var temp_style_list=return_ajax_request_value(reponse[2]+'__'+0, 'temp_style_list_view', 'requires/quick_costing_woven_controller');
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
		window.open("requires/quick_costing_woven_controller.php?data=" + data+'&action='+action, true );
	}
	
	function openmypage_style(type)
	{
		var page_link='requires/quick_costing_woven_controller.php?action=style_popup';
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
				var temp_style_list=return_ajax_request_value(cost_sheet_no+'__'+1, 'temp_style_list_view', 'requires/quick_costing_woven_controller');
				$('#style_td').html( temp_style_list );
				
				var str=style_id.split(",");
				var strcst=cost_sheet_no.split(",");
				for( var i=0; i< str.length; i++ ) {
					if( k==1)
					{
						set_onclick_style_list(str[0]+'__'+user_id+'__'+strcst[0]+'__'+0);
						var qc_no=$('#hid_qc_no').val()*1;
						change_color_tr( qc_no , $('#tr_'+qc_no).attr('bgcolor') );
						load_drop_down('requires/quick_costing_woven_controller', cost_sheet_no+'__0__0', 'load_drop_down_option_id', 'option_td');
						load_drop_down('requires/quick_costing_woven_controller', cost_sheet_no+'__'+$('#cbo_option_id').val()+'__0', 'load_drop_down_revise_no', 'revise_td');
						k++;
					}
				}
			}
			//alert(temp_style_list)
			//$('#style_td').html( $('#style_td').html() +""+ return_ajax_request_value(style_id, 'temp_style_list_view', 'requires/quick_costing_woven_controller') );
			//show_list_view( style_id,'temp_style_list_view','style_td','requires/quick_costing_woven_controller','',1);//setFilterGrid(\'tbl_po_list\',-1)
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
			load_drop_down('requires/quick_costing_woven_controller', datas[2]+'__0__0', 'load_drop_down_option_id', 'option_td');
			load_drop_down('requires/quick_costing_woven_controller', datas[2]+'__'+$('#cbo_option_id').val()+'__0', 'load_drop_down_revise_no', 'revise_td');
			
			var val=document.getElementById('cbo_revise_no').value+'***'+document.getElementById('cbo_option_id').value+'***'+document.getElementById('chk_is_new_meeting').value;
			get_php_form_data(datas[0]+"__"+datas[1]+"__"+datas[2]+"__"+val, 'populate_style_details_data', 'requires/quick_costing_woven_controller');
			$('#txt_seleted_row_id').val(datas[0]);
			$('#hid_selected_cost_no').val(datas[2]);
			//$('#chk_is_new_meeting').val(2);
			//document.getElementById('chk_is_new_meeting').checked=false;
			//fnc_meeting_no(2);
		}
		else
		{
			var val=document.getElementById('cbo_revise_no').value+'***'+document.getElementById('cbo_option_id').value+'***'+document.getElementById('chk_is_new_meeting').value;
			get_php_form_data(datas[0]+"__"+datas[1]+"__"+datas[2]+"__"+val, 'populate_style_details_data', 'requires/quick_costing_woven_controller');
			$('#txt_seleted_row_id').val(datas[0]);
			$('#hid_selected_cost_no').val(datas[2]);
			//$('#chk_is_new_meeting').val(2);
			//document.getElementById('chk_is_new_meeting').checked=false;
			//fnc_meeting_no(2);
		}
		//$('#cbo_revise_no').val(0);
		//$('#cbo_option_id').val(0);
		
		//load_drop_down('requires/quick_costing_woven_controller', reponse[2]+'__'+reponse[4]+'__'+$('#cbo_revise_no').val(), 'load_drop_down_revise_no', 'revise_td');
		//load_drop_down('requires/quick_costing_woven_controller', reponse[2]+'__'+reponse[4]+'__0', 'load_drop_down_option_id', 'option_td');
	}
	
	function fnc_delete_style_row()
	{
		var style_id=$('#txt_seleted_row_id').val();
		var hid_qc_no=$('#hid_qc_no').val();
		var temp_style_list=return_ajax_request_value(hid_qc_no+'__'+3, 'temp_style_list_view', 'requires/quick_costing_woven_controller');
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
		get_php_form_data(($("#hid_qc_no").val()*1)+'__'+user_id+'__'+0, 'populate_style_details_data', 'requires/quick_costing_woven_controller');
	}
	
	function fnc_copy_cost_sheet( operation )
	{
		//alert( $('#txt_update_id').val() );
		var data_copy="action=copy_cost_sheet&operation="+operation+get_submitted_data_string('txt_costSheetNo*txt_update_id*txt_styleRef*cbo_season_id*cbo_buyer_id*hid_qc_no',"../../");
		var data=data_copy;
		//alert(data);
		//return;
		freeze_window(operation);
		http.open("POST","requires/quick_costing_woven_controller.php",true);
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
			var temp_style_list=return_ajax_request_value(style_id+'__'+2, 'temp_style_list_view', 'requires/quick_costing_woven_controller');
			$('#style_td').html('');
			reset_fnc();
		}
	}
	
	function fnc_confirm_style()
	{
		if($('#txt_update_id').val()!="")
		{
			var data=$('#hid_qc_no').val()+'__'+$('#txt_update_id').val();
			var page_link='requires/quick_costing_woven_controller.php?action=confirmStyle_popup';
			var title="Confirm Style Popup";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&data='+data, title, 'width=950px,height=450px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				/*var theform=this.contentDoc.forms[0];
				var style_id=this.contentDoc.getElementById("hide_style_id").value; // product ID
				var temp_style_list=return_ajax_request_value(style_id+'__'+1, 'temp_style_list_view', 'requires/quick_costing_woven_controller');
				$('#style_td').html( temp_style_list );*/
				//alert(temp_style_list)
				//$('#style_td').html( $('#style_td').html() +""+ return_ajax_request_value(style_id, 'temp_style_list_view', 'requires/quick_costing_woven_controller') );
				//show_list_view( style_id,'temp_style_list_view','style_td','requires/quick_costing_woven_controller','',1);//setFilterGrid(\'tbl_po_list\',-1)
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
		
		get_php_form_data(hid_qc_no+"__"+user_id+"__"+cost_no+"__"+val+"__from_option", 'populate_style_details_data', 'requires/quick_costing_woven_controller');
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
			if(type==1) load_drop_down( 'requires/quick_costing_woven_controller', type, 'load_drop_agent_location_name', 'agent_td');
			else if (type==2) load_drop_down( 'requires/quick_costing_woven_controller', type, 'load_drop_agent_location_name', 'location_td');
		}
	}
	
	function fnc_meeting_no(chk_meeting_val)
	{
		var meeting_val=1;
		var max_meeting_no=return_ajax_request_value('', 'max_meeting_no', 'requires/quick_costing_woven_controller');
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
			var page_link='requires/quick_costing_woven_controller.php?action=fobavg_option_popup';
			var title="FOB Average Option PopUp";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&data='+data, title, 'width=650px,height=450px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				
			}
		}
	}
	
	function print_report_button_setting()
	{
		var report_ids=return_ajax_request_value('', 'print_btn_id', 'requires/quick_costing_woven_controller');
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
		generate_report_file( $('#hid_qc_no').val()+'*'+$('#txt_costSheetNo').val()+'*'+report_title, action,'requires/quick_costing_woven_controller');
		return;
	}
	
	function fnc_openmypage_inquery()
	{
		var page_link='requires/quick_costing_woven_controller.php?action=inquery_id_popup';
		var title='Inquiry ID Selection Form' ;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=450px,center=1,resize=1,scrolling=0','../');
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
				load_drop_down( 'requires/quick_costing_woven_controller', theemail[1], 'load_drop_down_season', 'season_td');
				load_drop_down( 'requires/quick_costing_woven_controller', theemail[1], 'load_drop_down_brand', 'brand_td');
				
				document.getElementById('txt_styleRef').value=theemail[2];
				document.getElementById('txt_inquiry_no').value=theemail[3];
				document.getElementById('cbo_season_id').value=theemail[4];
				document.getElementById('cbo_season_year').value=theemail[5];
				document.getElementById('cbo_brand').value=theemail[6];
			}
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
        <div style="display:none"><? echo load_freeze_divs ("../../",$permission);  ?></div>
        <form name="quickCosting_1" id="quickCosting_1" autocomplete="off">
            <fieldset style="width:1200px;">
            <table width="1200px" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td width="110" class="must_entry_caption">Template Name<input type="button" class="formbutton" style="width:20px; font-style:italic" value="N" onClick="openmypage_temp('requires/quick_costing_woven_controller.php?action=template_popup','Create Template')"/>
                    <input type="hidden" id="txt_seleted_row_id">
                    <input type="hidden" id="hid_selected_cost_no">
                    <input type="hidden" id="hid_qc_no">
                    <input type="hidden" id="txt_inquery_id">
                    </td>
                    <td width="130" id="template_td"><?  
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
					//print_r($template_name_arr);
					echo create_drop_down( "cbo_temp_id", 130, $template_name_arr,'', 1, "-Select Template-",$selected, "fnc_template_view();" ); ?><input style="width:70px;" type="hidden" class="text_boxes" name="txt_temp_id" id="txt_temp_id" /></td>
                    <td width="110" class="must_entry_caption">Cons. Basis<? echo create_drop_down( "cbouom", 40, $unit_of_measurement,'', 0, '-Uom-', 27, "",$disabled,"12,23,27" ); ?></td>
                    <td width="130"><? echo create_drop_down( "cbo_cons_basis_id", 130, $qc_consumption_basis,'', 1, "--Select Basis--",3, "fnc_consumption_write_disable(this.value+'__'+1);", "","1,3" ); ?></td>
                    <td width="110"><strong>Inquiry ID</strong></td>
                    <td width="130"><input style="width:120px;" type="text" class="text_boxes" name="txt_inquiry_no" id="txt_inquiry_no" readonly placeholder="Browse"  onDblClick="fnc_openmypage_inquery();"/></td>
                    <td width="110" class="must_entry_caption"><strong>Style Ref.</strong></td>
                    <td width="130"><input style="width:120px;" type="text" onDblClick="openmypage_style(0);" class="text_boxes textbox1" name="txt_styleRef" id="txt_styleRef" placeholder="Write/Browse"/></td>
                    
                    <td width="110"><strong>Cost Sheet No</strong></td>
                    <td width="130"><input style="width:120px;" type="text" class="text_boxes_numeric textbox2" name="txt_costSheetNo" id="txt_costSheetNo" placeholder="Display"  readonly/><input style="width:40px;" type="hidden" name="txt_update_id" id="txt_update_id"/></td>
                </tr>
                <tr>
                	<td class="must_entry_caption"><strong>Buyer Name</strong></td>
                    <td><? echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down('requires/quick_costing_woven_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'requires/quick_costing_woven_controller',this.value, 'load_drop_down_sub_dep', 'sub_td' ); load_drop_down( 'requires/quick_costing_woven_controller', this.value, 'load_drop_down_brand', 'brand_td');" ); ?></td>
                	<td class="must_entry_caption"><strong>Season</strong>&nbsp;&nbsp;&nbsp;<? echo create_drop_down( "cbo_season_year", 50, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
                    <td id="season_td"><? echo create_drop_down("cbo_season_id", 130, $blank_array,'', 1, "-- Select Season--",$selected, "" ); ?></td>
                    <td class="must_entry_caption"><strong>Brand</strong></td>
                    <td id="brand_td"><? echo create_drop_down("cbo_brand", 130, $blank_array,"",1, "-Brand-", $selected,""); ?></td>
                    <td width="110" class="must_entry_caption"><strong>Costing Date</strong></td>
                    <td><input style="width:120px;" type="text" class="datepicker" name="txt_costingDate" id="txt_costingDate" value="<?=date('d-m-Y');?>" /></td>
                    <td class="must_entry_caption"><strong>Exchange Rate</strong></td>
                    <td><input style="width:120px;" type="text" class="text_boxes_numeric" name="txt_exchangeRate" id="txt_exchangeRate" value="78" /></td>
                </tr>
                <tr>
                	<td><strong>Department</strong></td>
                    <td id="sub_td"><? echo create_drop_down( "cbo_subDept_id", 130, $blank_array,'', 1, "-- Select Dept--",$selected, "" ); ?></td>
                    <td><strong>Style Description</strong></td>
                    <td><input style="width:120px;" type="text" class="text_boxes" name="txt_styleDesc" id="txt_styleDesc" /></td>
                    <td><strong>Delivery Date</strong></td>
                    <td><input name="txt_delivery_date" id="txt_delivery_date" class="datepicker" type="text" style="width:117px;" value="" /></td>
                    <td><strong>Stage </strong><input type="button" class="formbutton" style="width:50px; font-style:italic" value="New" onClick="fnc_new_stage_popup();"/></td>
                    <td id="stage_td"><?=create_drop_down( "cbo_stage_id", 130,"select tuid, stage_name from lib_stage_name where status_active=1 and is_deleted=0","tuid,stage_name", 1, "-- Select --", $selected, "" ); ?></td>
                    <td class="must_entry_caption"><strong>Offer Qty</strong></td>
                    <td><input style="width:120px;" type="text" class="text_boxes_numeric" name="txt_offerQty" id="txt_offerQty" onChange="fnc_amount_calculation(7,0,0);" /></td>
                </tr>
                
                <tr>
                    <td><strong>Quoted Price ($)</strong></td>
                    <td><input style="width:120px;" type="text" class="text_boxes_numeric" name="txt_quotedPrice" id="txt_quotedPrice" /></td>
                    <td><strong>TGT Price</strong></td>
                    <td><input style="width:120px;" type="text" class="text_boxes_numeric" name="txt_tgtPrice" id="txt_tgtPrice" /></td>
                    <td><strong>&nbsp;</strong></td>
                    <td>&nbsp;</td>
                    <td><strong>&nbsp;</strong></td>
                    <td>&nbsp;</td>
                    <td><strong>&nbsp;</strong></td>
                    <td>&nbsp;</td>
                </tr>
            </table>
            </fieldset>
            
            <fieldset style="width:1200px;">
            <table width="1200" cellspacing="2" cellpadding="0" border="0" class="rpt_table" rules="all">
                <tr>
                	<td width="930" valign="top">
                    	<table width="930" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                        	<thead>
                            	<tr>
                                    <th width="90" rowspan="2">Gmts Item<input style="width:50px;" type="hidden" name="txt_isUpdate" id="txt_isUpdate" value="0" /></th>
                                    <th colspan="3"><? echo create_drop_down( "cbo_head_1", 150, $body_part,'', 0, "-Body Part 1-","20", "fnc_change_data(this.value,1);","","1,20","","","4,6"); ?></th>
                                    <th colspan="3"><? echo create_drop_down( "cbo_head_2", 150, $body_part,'', 0, "-Body Part 2-","17", "fnc_change_data(this.value,2);","","16,17","","","1,6,20"); ?></th>
                                    <th colspan="3"><? echo create_drop_down( "cbo_head_3", 150, $body_part,'', 0, "-Body Part 3-",21, "fnc_change_data(this.value,3);","","21","","","1,4,20" ); ?></th>
                                    <th colspan="3"><? echo create_drop_down( "cbo_head_4", 150, $body_part,'', 0, "-Other-","22", "fnc_change_data(this.value,4);","","9,22","","","" ); ?></th>
                                    <th colspan="3"><? 
									$col5Arr=array(996 => "Others", 997 => "Knit [Kg]");
									echo create_drop_down( "cbo_head_5", 150, $col5Arr,'', 0, "-Knit (Kg)-","997", "fnc_change_data(this.value,5);","","","","","" ); ?></th>
                                </tr>
                                <tr>
                                    <th width="85">Description</th>
                                    <th width="37">Value</th>
                                    <th width="37">ALW</th>
                                    
                                    <th width="85">Description</th>
                                    <th width="37">Value</th>
                                    <th width="37">ALW</th>
                                    
                                    <th width="85">Description</th>
                                    <th width="37">Value</th>
                                    <th width="37">ALW</th>
                                    
                                    <th width="85">Description</th>
                                    <th width="37">Value</th>
                                    <th width="37">ALW</th>
                                    
                                    <th width="85">Description</th>
                                    <th width="37">Value</th>
                                    <th>ALW</th>
                                </tr>
                            </thead>
                        </table>
                        <div style="width:930px; max-height:140px; overflow-y:scroll" id="scroll_body" > 
                    	<table width="910" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" id="item_tbl">
                        	<tbody id="item_tbody">
                            	<tr bgcolor="#FFCCFF">
                                	<td width="90" rowspan="9" valign="middle">Item Name</td>
                                    <?
									$hd=5;
									for($m=1; $m<=$hd; $m++)
									{
									?>
										<td width="84"><input style="width:71px;" type="text" class="text_boxes" name="txtItemDes_1_<? echo $m; ?>" id="txtItemDes_1_<? echo $m; ?>" /></td>
										<td width="36"><input style="width:25px;" type="text" class="text_boxes_numeric" name="txtVal_1_<? echo $m; ?>" id="txtVal_1_<? echo $m; ?>" onChange="fnc_consumption_calculation(1,<? echo $m; ?>,this.value,document.getElementById('txtAw_1_<? echo $m; ?>').value);" /></td>
										<td width="36"><input style="width:25px;" type="text" class="text_boxes_numeric" name="txtAw_1_<? echo $m; ?>" id="txtAw_1_<? echo $m; ?>" onChange="fnc_consumption_calculation(1,<? echo $m; ?>,document.getElementById('txtVal_1_<? echo $m; ?>').value,this.value);" /></td>
									<?
									}
									?>
                                 </tr>
                                 <?
								$trLen=8; $col=2;
								for($j=1; $j<=$trLen; $j++)
								{
									?>
                                    <tr>
                                    <?
									for($n=1; $n<=$hd; $n++)
									{
									?>
                                        <td width="84"><input style="width:71px;" type="text" class="text_boxes" name="txtItemDes_<? echo $col; ?>_<? echo $n; ?>" id="txtItemDes_<? echo $col; ?>_<? echo $n; ?>" /></td>
                                        <td width="36"><input style="width:25px;" type="text" class="text_boxes_numeric" name="txtVal_<? echo $col; ?>_<? echo $n; ?>" id="txtVal_<? echo $col; ?>_<? echo $n; ?>" onChange="fnc_consumption_calculation(<? echo $col; ?>,<? echo $n; ?>,this.value,document.getElementById('txtAw_<? echo $col; ?>_<? echo $n; ?>').value);" /></td>
                                        <td width="36"><input style="width:25px;" type="text" class="text_boxes_numeric" name="txtAw_<? echo $col; ?>_<? echo $n; ?>" id="txtAw_<? echo $col; ?>_<? echo $n; ?>" onChange="fnc_consumption_calculation(<? echo $col; ?>,<? echo $n; ?>,document.getElementById('txtVal_<? echo $col; ?>_<? echo $n; ?>').value,this.value);" /></td>
                                    <?
									}
									?>
                                    </tr>
									<?	
                                    $col++;
								}
								?>
                             </tbody>
                        </table>
                    </div>
                    </td>
                    <td valign="top">
                    	<table width="250" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                        	<thead>
                                <tr>
                                    <th width="90">Style Ref.</th>
                                    <th width="70">Season</th>
                                    <th>User</th>
                                </tr>
                            </thead>
                        </table>
                        <div style="width:250px; max-height:100px; overflow-y:scroll" id="scroll_body" > 
                            <table width="230" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                                <tbody id="style_td">
                                </tbody>
                            </table>
                    	</div>
                        <div id="test"><input type="button" name="clear_style" id="clear_style" value="Clear" onClick="fnc_clear_all()" style="width:50px" class="formbuttonplasminus" />&nbsp;<input type="button" name="delete_style" id="delete_style" value="Clear ST" onClick="fnc_delete_style_row()" style="width:60px" class="formbuttonplasminus" /><strong> Rv: </strong><span id="revise_td"><? echo create_drop_down( "cbo_revise_no", 45, $blank_array,'', 1, "-0-",$selected, "","","","","","" ); ?></span><strong> Op: </strong><span id="option_td"><? echo create_drop_down( "cbo_option_id", 45, $blank_array,'', 1, "-0-",1, "","","","","","" ); ?></span></div>
                       <div title="Option Reason/Remarks"><textarea id="txt_option_remarks" pre_opt_remarks="" class="text_area" style="width:250px; height:40px;" placeholder="Option Reason/Remarks"></textarea></div> 
                    </td>
                </tr>
            </table>
            </fieldset>
            <fieldset style="width:1200px;">
            <table width="1200" cellspacing="2" cellpadding="0" border="0" class="rpt_table" rules="all">
                <tr>
                	<td width="520" valign="top">
                    	<table width="520" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                        	<thead>
                                <tr>
                                    <th width="100">&nbsp;</th>
                                    <th width="100">Heading</th>
                                    <th width="60">Cons.</th>
                                    <th width="40">Ex %</th>
                                    <th width="50">Tot. Cons.</th>
                                    <th width="25">Cal</th>
                                    <th width="50">Rate ($)</th>
                                    <th>Value ($)</th>
                                </tr>
                            </thead>
                        </table>
                        <div style="width:520px; max-height:320px; overflow-y:scroll" id="scroll_body" > 
                    	<table width="500" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                            <tbody>
                            	<tr>
                                    <td width="100" rowspan="5" valign="middle"><input style="width:88px;" type="text" class="text_boxes" name="txt_itemName" id="txt_itemName" readonly /><input style="width:50px;" type="hidden" class="text_boxes" name="txt_itemId" id="txt_itemId" /><input style="width:50px;" type="hidden" class="text_boxes" name="txt_itemId_tmp" id="txt_itemId_tmp" /></td>
                                    <td width="100"><input style="width:88px;" type="text" class="text_boxes" name="txt_head1" id="txt_head1" readonly /><input style="width:50px;" type="hidden" name="txt_head_id_1" id="txt_head_id_1" readonly /></td>
                                    <td width="60"><input style="width:48px;" type="text" class="text_boxes_numeric" name="txt_consumtion1" id="txt_consumtion1" disabled /></td>
                                    <td width="40"><input style="width:27px;" type="text" class="text_boxes_numeric" name="txt_exper1" id="txt_exper1" placeholder="Write" onChange="fnc_amount_calculation('fabric',1,document.getElementById('txt_rate1').value);" /></td>
                                    <td width="50"><input style="width:38px;" type="text" class="text_boxes_numeric" name="txt_totConsumtion1" id="txt_totConsumtion1" disabled /></td>
                                    <td width="25"><input type="checkbox" name="ratePop_1" id="ratePop_1" onClick="fnc_rate_write_popup(1)" value="2" style="width:12px;" ><input style="width:50px;" type="hidden" class="text_boxes" name="txtRateData_1" id="txtRateData_1" /></td>
                                    <td width="50"><input style="width:37px;" type="text" class="text_boxes_numeric" name="txt_rate1" id="txt_rate1" onChange="fnc_amount_calculation('fabric',1,this.value);" placeholder="Write" /></td>
                                    <td><input style="width:55px;" type="text" class="text_boxes_numeric" name="txt_value1" id="txt_value1" readonly /></td>
                                </tr>
                                <? $hd=4; $m=2;
								for($n=1; $n<=$hd; $n++) { ?>
                                <tr>
                                    <td width="100"><input style="width:88px;" type="text" class="text_boxes" name="txt_head<? echo $m; ?>" id="txt_head<? echo $m; ?>" readonly /><input style="width:50px;" type="hidden" name="txt_head_id_<? echo $m; ?>" id="txt_head_id_<? echo $m; ?>" readonly /></td>
                                    <td width="60"><input style="width:48px;" type="text" class="text_boxes_numeric" name="txt_consumtion<? echo $m; ?>" id="txt_consumtion<? echo $m; ?>" disabled /></td>
                                    <td width="40"><input style="width:27px;" type="text" class="text_boxes_numeric" name="txt_exper<? echo $m; ?>" id="txt_exper<? echo $m; ?>" placeholder="Write" onChange="fnc_amount_calculation('fabric',<? echo $m; ?>,document.getElementById('txt_rate<? echo $m; ?>').value);" /></td>
                                    <td width="50"><input style="width:38px;" type="text" class="text_boxes_numeric" name="txt_totConsumtion<? echo $m; ?>" id="txt_totConsumtion<? echo $m; ?>" disabled /></td>
                                    <td width="25"><input type="checkbox" name="ratePop_<? echo $m; ?>" id="ratePop_<? echo $m; ?>" onClick="fnc_rate_write_popup(<? echo $m; ?>)" value="2" style="width:12px;" ><input style="width:50px;" type="hidden" class="text_boxes" name="txtRateData_<? echo $m; ?>" id="txtRateData_<? echo $m; ?>" /></td>
                                    <td width="50"><input style="width:37px;" type="text" class="text_boxes_numeric" name="txt_rate<? echo $m; ?>" id="txt_rate<? echo $m; ?>" onChange="fnc_amount_calculation('fabric',<? echo $m; ?>,this.value);" placeholder="Write" /></td>
                                    <td><input style="width:55px;" type="text" class="text_boxes_numeric" name="txt_value<? echo $m; ?>" id="txt_value<? echo $m; ?>" readonly /></td>
                                </tr>
                                <? $m++; } ?>
                                
                                <tr bgcolor="#FFCCFF">
                                    <td width="100" rowspan="5" valign="middle">Special Operation ($)/DZN</td>
                                    <td width="100"><? echo create_drop_down( "cboSpeciaOperationId_1", 100, $emblishment_name_array,"", 1, "--Select--", 1, "" ); ?></td>
                                    <td width="60"><input style="width:48px;" type="text" class="text_boxes_numeric" name="txt_spConsumtion1" id="txt_spConsumtion1" onChange="fnc_amount_calculation('special',1,document.getElementById('txtSpRate_1').value); fnc_itemwise_data_cache(document.getElementById('txt_itemId').value); " /></td>
                                    <td width="40"><input style="width:27px;" type="text" class="text_boxes_numeric" name="txt_spexper1" id="txt_spexper1" placeholder="Write" onChange="fnc_amount_calculation('special',1,document.getElementById('txtSpRate_1').value); fnc_itemwise_data_cache(document.getElementById('txt_itemId').value); " /></td>
                                    <td width="50"><input style="width:38px;" type="text" class="text_boxes_numeric" name="txt_totSpConsumtion1" id="txt_totSpConsumtion1" disabled /></td>
                                    <td width="25">&nbsp;</td>
                                    <td width="50"><input style="width:37px;" type="text" class="text_boxes_numeric" name="txtSpRate_1" id="txtSpRate_1" onChange="fnc_amount_calculation('special',1,this.value); fnc_itemwise_data_cache(document.getElementById('txt_itemId').value);" /></td>
                                    <td><input style="width:55px;" type="text" class="text_boxes_numeric" name="txt_spValue1" id="txt_spValue1" readonly /></td>
                                </tr>
                                <? $sp=4; $j=2;
								for($i=1; $i<=$sp; $i++) { 
								if($i==4) $sel=99; else $sel=$j;
								?>
                                <tr bgcolor="#FFCCFF">
                                    <td width="100"><? echo create_drop_down( "cboSpeciaOperationId_$j", 100, $emblishment_name_array,"", 1, "--Select--", $sel, "" ); ?></td>
                                    <td width="60"><input style="width:48px;" type="text" class="text_boxes_numeric" name="txt_spConsumtion<? echo $j; ?>" id="txt_spConsumtion<? echo $j; ?>"  onChange="fnc_amount_calculation('special',<? echo $j; ?>,document.getElementById('txtSpRate_<? echo $j; ?>').value); fnc_itemwise_data_cache(document.getElementById('txt_itemId').value);" /></td>
                                    <td width="40"><input style="width:27px;" type="text" class="text_boxes_numeric" name="txt_spexper<? echo $j; ?>" id="txt_spexper<? echo $j; ?>" placeholder="Write" onChange="fnc_amount_calculation('special',<? echo $j; ?>,document.getElementById('txtSpRate_<? echo $j; ?>').value); fnc_itemwise_data_cache(document.getElementById('txt_itemId').value);" /></td>
                                    <td width="50"><input style="width:38px;" type="text" class="text_boxes_numeric" name="txt_totSpConsumtion<? echo $j; ?>" id="txt_totSpConsumtion<? echo $j; ?>" disabled /></td>
                                    <td width="25">&nbsp;</td>
                                    <td width="50"><input style="width:37px;" type="text" class="text_boxes_numeric" name="txtSpRate_<? echo $j; ?>" id="txtSpRate_<? echo $j; ?>" onChange="fnc_amount_calculation('special',<? echo $j; ?>,this.value); fnc_itemwise_data_cache(document.getElementById('txt_itemId').value);" /></td>
                                    <td><input style="width:55px;" type="text" class="text_boxes_numeric" name="txt_spValue<? echo $j; ?>" id="txt_spValue<? echo $j; ?>" readonly /></td>
                                </tr>
                                <? $j++; } 
								$accessories_arr=return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active =1 and is_deleted=0 order by item_name","id","item_name");
								$selected_acc='';
								foreach($accessories_arr as $row=>$val)
								{
									if($val=='All Accessories')
									{
										$selected_acc=$row;
									}
								}
								?>
                                <tr>
                                    <td width="100" rowspan="15" valign="top">Accessories ($)/DZN</td>
                                    <td width="100"><? echo create_drop_down( "cbo_accessories_id1", 100, $accessories_arr,"", 1, "-Accessories-", $selected_acc, "" ); ?></td>
                                    <td width="60"><input style="width:48px;" type="text" class="text_boxes_numeric" name="txt_accConsumtion1" id="txt_accConsumtion1" onChange="fnc_amount_calculation('accessories',1,document.getElementById('txt_acRate1').value); fnc_itemwise_data_cache(document.getElementById('txt_itemId').value);" /></td>
                                    <td width="40"><input style="width:27px;" type="text" class="text_boxes_numeric" name="txt_acexper1" id="txt_acexper1" placeholder="Write" onChange="fnc_amount_calculation('accessories',1,document.getElementById('txt_acRate1').value); fnc_itemwise_data_cache(document.getElementById('txt_itemId').value);" /></td>
                                    <td width="50"><input style="width:38px;" type="text" class="text_boxes_numeric" name="txt_totAccConsumtion1" id="txt_totAccConsumtion1" disabled /></td>
                                    <td width="25">&nbsp;</td>
                                    <td width="50"><input style="width:37px;" type="text" class="text_boxes_numeric" name="txt_acRate1" id="txt_acRate1" onChange="fnc_amount_calculation('accessories',1,this.value);" /></td>
                                    <td><input style="width:55px;" type="text" class="text_boxes_numeric" name="txt_acValue1" id="txt_acValue1" readonly /></td>
                                </tr>
                                <? $acc=14; $k=2;
								for($l=1; $l<=$acc; $l++){ ?>
                                <tr>
                                    <td width="100"><? echo create_drop_down( "cbo_accessories_id$k", 100, $accessories_arr,"", 1, "-Accessories-", $selected, "" ); ?></td>
                                    <td width="60"><input style="width:48px;" type="text" class="text_boxes_numeric" name="txt_accConsumtion<? echo $k; ?>" id="txt_accConsumtion<? echo $k; ?>" onChange="fnc_amount_calculation('accessories',<? echo $k; ?>,document.getElementById('txt_acRate<? echo $k; ?>').value);  fnc_itemwise_data_cache(document.getElementById('txt_itemId').value);" /></td>
                                    <td width="40"><input style="width:27px;" type="text" class="text_boxes_numeric" name="txt_acexper<? echo $k; ?>" id="txt_acexper<? echo $k; ?>" placeholder="Write" onChange="fnc_amount_calculation('accessories',<? echo $k; ?>,document.getElementById('txt_acRate<? echo $k; ?>').value);  fnc_itemwise_data_cache(document.getElementById('txt_itemId').value);" /></td>
                                    <td width="50"><input style="width:38px;" type="text" class="text_boxes_numeric" name="txt_totAccConsumtion<? echo $k; ?>" id="txt_totAccConsumtion<? echo $k; ?>" disabled /></td>
                                    <td width="25">&nbsp;</td>
                                    <td width="50"><input style="width:37px;" type="text" class="text_boxes_numeric" name="txt_acRate<? echo $k; ?>" id="txt_acRate<? echo $k; ?>"  onChange="fnc_amount_calculation('accessories',<? echo $k; ?>,this.value);" /></td>
                                    <td><input style="width:55px;" type="text" class="text_boxes_numeric" name="txt_acValue<? echo $k; ?>" id="txt_acValue<? echo $k; ?>" readonly /></td>
                                </tr>
                                <? $k++; } ?>
                            </tbody>
                        </table>
                    </div>
                    </td>
                    <td valign="top" width="270">
                    	<table width="270" cellspacing="0" cellpadding="0" border="0" class="rpt_table" rules="all">
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
                            	<td style="height:28px; font-size:25px; background:#00CED1;" onClick="fnc_fobavg_option();">F.O.B $</td>
                                <td style="height:28px; font-size:25px; color:#FFFFFF; background:#FE00FF;" id="totalFob_td" prev_fob="" align="right" title="(Rmg Ratio*Fob (Pcs))*No Of. Pack's">&nbsp;</td>
                                <td style="height:28px; font-size:25px; background:#00FA9A;" id="uom_td">&nbsp;</td>
                            </tr>
                            <tr>
                            </tr>
                            <tr>
                            	<td><strong>No Of. Pack's</strong></td>
                                <td><input type="text" name="txt_noOfPack" id="txt_noOfPack" style="width:68px" class="text_boxes_numeric" value="1" onChange="fnc_check_zero_val(this.value); fnc_amount_calculation(0,0,0);" /></td>
                                <td><input type="button" name="confirm_style" id="confirm_style" value="Confirm" onClick="fnc_confirm_style();" style="width:80px" class="formbuttonplasminus" /></td>
                            </tr>
                            <tr>
                            	<td colspan="3" align="center" height="40" class="button_container">
								<? echo load_submit_buttons($permission,"fnc_qcosting_entry",0,0,"reset_fnc();",1); ?><br><input type="button" id="report_btn_1" class="formbutton" style="width:30px; display:none;" value="PNT" onClick="fnc_print_report('quick_costing_print')" />&nbsp; <input type="button" id="report_btn_2" class="formbutton" style="width:35px;display:none;" value="PNT2" onClick="fnc_print_report('quick_costing_print2')" />&nbsp; <input type="button" id="set_button" class="formbutton" style="width:60px;" value="Save As" onClick="fnc_copy_cost_sheet(0)" />&nbsp; <input type="button" id="set_button" class="formbutton" style="width:45px;" value="Revise" onClick="fnc_qcosting_entry(6)" />&nbsp; <input type="button" id="set_button" class="formbutton" style="width:45px;" value="Option" onClick="fnc_qcosting_entry(7)" />
                                </td>
                            </tr>
                            <tr><td colspan="3" align="center"><strong>Merchandiser Remarks : </strong></td></tr>
                            <tr>
                            	<td colspan="3"><textarea id="txt_costing_remarks" pre_costing_remarks="" class="text_area" style="width:260px; height:40px;" placeholder="Merchandiser Remarks">1. </textarea></td>
                            </tr>
                            <tr><td colspan="3"><strong>Date & Time:</strong><input style="width:45px;" type="text" class="datepicker" name="txt_meeting_date" id="txt_meeting_date" value="<? echo date('d-m-Y');?>" /><input name="txt_meeting_time" id="txt_meeting_time" class="text_boxes" type="text" style="width:30px;" placeholder="24 H. Format" onChange="fnc_valid_time(this.value,'txt_meeting_time');" onKeyUp="fnc_valid_time(this.value,'txt_meeting_time');" onKeyPress="return numOnly(this,event,this.id);" value="<? echo date('H:i', time()); ?>" /><strong>New Meeting No.</strong><input type="checkbox" name="chk_is_new_meeting" id="chk_is_new_meeting" onClick="fnc_rate_write_popup('meeting');" value="2" style="width:12px;" ></td></tr>
                            <tr><td><strong>Meeting No:</strong></td><td align="center"><input style="width:60px;" type="text" class="text_boxes" name="txt_meeting_no" id="txt_meeting_no" disabled readonly /></td><td><input type="button" name="meeting_remarks" id="meeting_remarks" value="Meeting Minutes" onClick="fnc_meeting_remarks_pop_up(document.getElementById('txt_update_id').value, document.getElementById('txt_styleRef').value);" style="width:100px" class="formbuttonplasminus" /></td></tr>
                            <tr><td colspan="3" align="center"><strong>Meeting Remarks:</strong></td></tr>
                            <tr>
                            	<td colspan="3"><textarea id="txt_meeting_remarks" pre_meeting_remark="" class="text_area" style="width:260px; height:40px;" placeholder="Meeting Remarks">1. </textarea></td>
                            </tr>
                        </table>
                    </td>
                     <td valign="top" id="summary_td">
                    	<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                        	<thead>
                            	<tr>
                                	<th colspan="3">Item Wise Cost Summary ($/DZN)</th>
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
                                	<td>Accessories</td>
                                    <td id="acc_td0"><strong>&nbsp;</strong></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                	<td>CPM &nbsp;&nbsp;&nbsp;
                                	  <input type="checkbox" name="cmPop" id="cmPop" onClick="fnc_rate_write_popup('cm')" value="2" style="width:12px;" title="Is Calculative?" ></td>
                                    <td align="center"><input name="txt_cpm_0" id="txt_cpm_0" class="text_boxes_numeric" style="width:40px;" title="CPM" placeholder="CPM" disabled /></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                	<td>SMV / EFI</td>
                                    <td align="center"><input name="txt_smv_0" id="txt_smv_0" class="text_boxes_numeric" style="width:20px;" title="SMV" placeholder="SMV" disabled /><input name="txt_eff_0" id="txt_eff_0" class="text_boxes_numeric" style="width:20px;" title="Efficiency" placeholder="EFF" disabled /></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                	<td>CM&nbsp;($/DZN)</td>
                                    <td align="center"><input name="txtCmCost_0" id="txtCmCost_0" class="text_boxes_numeric" style="width:40px;" onChange="" disabled /></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                	<td>Frieght Cost($/DZN)</td>
                                    <td align="center"><input name="txtFriCost_0" id="txtFriCost_0" class="text_boxes_numeric" style="width:40px;" disabled /></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                	<td>Lab - Test($/DZN)</td>
                                    <td align="center"><input name="txtLtstCost_0" id="txtLtstCost_0" class="text_boxes_numeric" style="width:40px;" disabled /></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                	<td>Mis/Offer Qty.&nbsp;<input name="txt_lumSum_cost" id="txt_lumSum_cost" class="text_boxes_numeric" style="width:37px;" title="Lum Sum Cost" placeholder="L.S.C" disabled /></td>
                                    <td align="center"><input name="txtMissCost_0" id="txtMissCost_0" class="text_boxes_numeric" style="width:40px;" disabled /></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                	<td>Other Cost($/DZN)</td>
                                    <td align="center"><input name="txtOtherCost_0" id="txtOtherCost_0" class="text_boxes_numeric" style="width:40px;" disabled /></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                	<td>Com(%)($/DZN)<input name="txt_commPer" id="txt_commPer" class="text_boxes_numeric" style="width:23px;" title="Commission (%)" placeholder="%" disabled /></td>
                                    <td align="center"><input name="txtCommCost_0" id="txtCommCost_0" class="text_boxes_numeric" style="width:40px;" disabled /></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                	<td>F.O.B($/DZN)</td>
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
		var temp_style_list=return_ajax_request_value(style_id+'__'+0, 'temp_style_list_view', 'requires/quick_costing_woven_controller');
		$('#style_td').html( temp_style_list );
		//$('#style_td').html( localStorage.getItem("temp_style_list_view") ); 
		fnc_select(); fnc_meeting_no(0); print_report_button_setting();
    </script>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>