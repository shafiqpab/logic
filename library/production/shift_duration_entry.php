<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//-------------------------------------------------------------------------
echo load_html_head_contents("Actual Cost Entry", "../../", 1, 1,'','','');
?>	
 
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<? echo $permission; ?>';

function fnc_shift_duration_entry(operation)
{
	var row_num=$('#tbl_dtls tbody tr').length;
	var data_all="";
	var j=0;
	var cross_date=0;
	$("#tbl_dtls tbody").find('tr').each(function()
	{
		var trId = $(this).attr('id').split('_');
		var i=trId[1];


		if($('#txtChkBox_'+i).is(':checked'))
		{
			cross_date=1;
		}
		else
		{
			cross_date=0;
		}

		j++;
		if (form_validation('cboProTypeId_'+i+'*cboShiftName_'+i+'*txtStratTime_'+i+'*txtEndTime_'+i,'production_type*Shift Name*Strat Time*End Time')==false)
		{
			return;
		}
		data_all+="&updateId_" + j + "='" + $('#updateId_'+i).val()+"'"+"&cboProTypeId_" + j + "='" + $('#cboProTypeId_'+i).val()+"'"+"&cboShiftName_" + j + "='" + $('#cboShiftName_'+i).val()+"'"+"&txtStratTime_" + j + "='" + $('#txtStratTime_'+i).val()+"'"+"&txtEndTime_" + j + "='" + $('#txtEndTime_'+i).val()+"'"+"&txtChkBox_" + j + "='" + cross_date+"'";
	});	
	//alert(data_all);return;
	var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+data_all;
	//alert(data);return;
	freeze_window(operation);
	http.open("POST","requires/shift_duration_entry_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_on_submit_reponse;
}
		
function fnc_on_submit_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=http.responseText.split('**');
		show_msg(trim(reponse[0]));
		if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		{
			//get_php_form_data(reponse[2]+'**'+reponse[3]+'**'+reponse[4], "action_user_data", "requires/shift_duration_entry_controller" );

			//$('#updateId_1').val(reponse[1]);
			//show_list_view(0,'show_update_list_view','shift_dtls','requires/shift_duration_entry_controller','');
			location.reload();
			set_button_status(1, permission, 'fnc_shift_duration_entry',1);
			release_freezing();
		}
		else if(reponse[0]==20)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}
		release_freezing();
	}
}

function fnc_valid_time(val,field_id)
{
	//alert(val+'='+field_id);
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

function add_break_down_tr(i) 
{
	var lastTrId = $('#tbl_dtls tbody tr:last').attr('id').split('_');
	var row_num=lastTrId[1];
	if (row_num!=i)
	{
		return false;
	}
	else
	{
		i++;

		$("#tbl_dtls tbody tr:last").clone().find("input,select").each(function(){

			$(this).attr({
				'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				'name': function(_, name) { return name },
				'value': function(_, value) { return value }
			});

		}).end().appendTo("#tbl_dtls");

		$("#tbl_dtls tbody tr:last ").removeAttr('id').attr('id','tr_'+i);
		
		$('#updateId_'+i).val('');

		$('#cboProTypeId_'+i).removeAttr("value").attr("value","");
		$('#cboShiftName_'+i).removeAttr("value").attr("value","");
		$('#txtStratTime_'+i).removeAttr("value").attr("value","");
		$('#txtEndTime_'+i).removeAttr("value").attr("value","");

		$('#increase_'+i).removeAttr("value").attr("value","+");
		$('#decrease_'+i).removeAttr("value").attr("value","-");
		$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
		$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
	}
	  
}

function fn_deleteRow(rowNo) 
{
	var numRow = $('#tbl_dtls tbody tr').length;
	//if(numRow==rowNo && rowNo!=1)
	if( numRow==1)
	{
		return false;
	}
	if(rowNo!=0)
	{
		/*var updateIdDtls=$('#updateIdDtls_'+rowNo).val();
		var txt_deleted_id=$('#txt_deleted_id').val();
		var selected_id='';

		if(updateIdDtls!='')
		{
			if(txt_deleted_id=='') selected_id=updateIdDtls; else selected_id=txt_deleted_id+','+updateIdDtls;
			$('#txt_deleted_id').val( selected_id );
		}
		var bar_code =$("#barcodeNo_"+rowNo).val();
		var index = scanned_barcode.indexOf(bar_code);
		scanned_barcode.splice(index,1);*/
		//$('#tbl_dtls tbody tr:last').remove();
		$('#tr_'+rowNo).remove();
	}
	else
	{
		return false;
	}
}

function show_details()
{
	show_list_view($('#seacrh_string').val(),'show_details_listview','cm_commercial_list_view','requires/shift_duration_entry','setFilterGrid("table_body",-1);');
}

function show_list_view_details()
{
	/*var isDisbled=$('#update1').attr('class');
	alert(isDisbled);return;*/
	if($('#update1').hasClass('formbutton_disabled'))
	{
		alert("Please Save First.");
		return;
	}
	else
	{
		show_list_view($('#cbo_company_id').val()+"**"+$('#cbo_cost_head').val()+"**"+$('#cbo_based_on').val()+"**"+$('#txt_incurred_date').val()+"**"+$('#txt_incurred_to_date').val(),'show_details_listview_po','cm_commercial_list_view_details','requires/shift_duration_entry','setFilterGrid("table_body_dtls",-1);');
	}
}

function fnc_chk_box(type,sl){
	if(type==1)
	{
		$('#txtCrossTd_'+sl).show();
		$('#txtChkBox_'+sl).show();
	}
	else
	{
		$('#txtCrossTd_'+sl).hide();
		$('#txtChkBox_'+sl).hide();
	}
}

</script>
 
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
     <? echo load_freeze_divs ("../../",$permission); ?>
    <form id="actual_shift_duration">    	
        <fieldset style="width:960px;">
            <legend>Shift Duration Entry</legend>
            <table id="tbl_dtls" width="820" cellspacing="2" cellpadding="0" border="0">
            	<tbody>
            	<?
            	$k=1;
            	$sql = "select id, production_type, shift_name, start_time, end_time, cross_date from shift_duration_entry where status_active=1 and is_deleted=0 order by id"; 
				$result=sql_select($sql);
				$count_td=count($result);
				if($count_td>0)
				{ 
					$update_type=1;
					foreach ($result as $val)
					{
						if($val[csf("production_type")]==1)
						{
							$display = "";
						}else{
							$display ="display: none;";
						}

						if($val[csf("cross_date")]==1)
						{
							$checked = "checked";
						}else{
							$checked ="";
						}
						?>
						<tr id="tr_<? echo $k;?>">
							<input type="hidden" id="updateId_<? echo $k;?>" name="updateId_<? echo $k;?>" value="<? echo $val[csf("id")];?>"/>
							<td width="80" align="center" class="must_entry_caption">Production Type</td>
							<td>
								<? 
								//$prod_type=array(1=>"Knitting", 2=>"Dyeing", 3=>"Sewing Output");
								echo create_drop_down("cboProTypeId_$k", 130, $production_type_for_shift, "", 1, "--Select--",$val[csf("production_type")],"fnc_chk_box(this.value,".$k.")"); 
								?>
							</td> 
							<td width="60" align="center" class="must_entry_caption">Shift</td>
							<td>
								<? echo create_drop_down("cboShiftName_$k", 50, $shift_name, "", 1, "--Shift--", $val[csf("shift_name")], "", 0, "", "", "", "", ""); 
								?>
							</td>
							<td width="50" align="center" class="must_entry_caption">Start Time</td>
							<td>
								<input name="txtStratTime_<? echo $k;?>" id="txtStratTime_<? echo $k;?>" class="text_boxes" style="width:80px" placeholder="24 Hour Format" onBlur="fnc_valid_time(this.value,this.id);" onKeyUp="fnc_valid_time(this.value,this.id);" value="<? echo $val[csf("start_time")];?>" />
							</td>
							<td width="50" align="center" class="must_entry_caption">End Time</td>
							<td>
								<input name="txtEndTime_<? echo $k;?>" id="txtEndTime_<? echo $k;?>" class="text_boxes" style="width:80px" placeholder="24 Hour Format" onBlur="fnc_valid_time(this.value,this.id);" onKeyUp="fnc_valid_time(this.value,this.id);" value="<? echo $val[csf("end_time")];?>"/>
							</td>
							
							<td align="center" id="increment_<? echo $k;?>">
								<input style="width:30px;" type="button" id="increase_<? echo $k;?>" name="increase_<? echo $k;?>"  class="formbutton" value="+" onClick="add_break_down_tr(<? echo $k;?>)"/>
								<input style="width:30px;" type="button" id="decrease_<? echo $k;?>" name="decrease_<? echo $k;?>"  class="formbutton" value="-" onClick="javascript:fn_deleteRow(<? echo $k;?>)"/>&nbsp;
							</td>  
							<td width="70" align="center" class="must_entry_caption" style="<? echo $display ;?>" id="txtCrossTd_<? echo $k;?>" >Cross Date</td>
							<td>
								<input name="txtChkBox_<? echo $k;?>" id="txtChkBox_<? echo $k;?>" class="text_boxes" style="width:50px; <? echo $display ;?>"  type="checkbox"  <? echo $checked;?> />
							</td>                
	                	</tr>
						<? 
					$k++;
					}
				}
				else
				{
            		$update_type=0;
            		foreach ($shift_name as $key => $val) 
					{
            			?>
						<tr id="tr_<? echo $k;?>">
							<input type="hidden" id="updateId_<? echo $k;?>" name="updateId_<? echo $k;?>" value=""/>
							<td width="80" align="center" class="must_entry_caption">Production Type</td>
							<td>
								<? 
								//$prod_type=array(1=>"Knitting", 2=>"Dyeing", 3=>"Sewing Output");
								echo create_drop_down("cboProTypeId_$k", 130, $production_type_for_shift, "", 1, "--Select--","","fnc_chk_box(this.value,".$k.")"); 
								?>
							</td> 
							<td width="60" align="center" class="must_entry_caption">Shift</td>
							<td>
								<? echo create_drop_down("cboShiftName_$k", 50, $shift_name, "", 1, "--Shift--", $key, "", 0, "", "", "", "", ""); 
								?>
							</td>
							<td width="50" align="center" class="must_entry_caption">Start Time</td>
							<td>
								<input name="txtStratTime_<? echo $k;?>" id="txtStratTime_<? echo $k;?>" class="text_boxes" style="width:80px" placeholder="24 Hour Format" onBlur="fnc_valid_time(this.value,this.id);" onKeyUp="fnc_valid_time(this.value,this.id);" />
							</td>
							<td width="50" align="center" class="must_entry_caption">End Time</td>
							<td>
								<input name="txtEndTime_<? echo $k;?>" id="txtEndTime_<? echo $k;?>" class="text_boxes" style="width:80px" placeholder="24 Hour Format" onBlur="fnc_valid_time(this.value,this.id);" onKeyUp="fnc_valid_time(this.value,this.id);"/>
							</td>
							
							<td align="center" id="increment_<? echo $k;?>">
								<input style="width:30px;" type="button" id="increase_<? echo $k;?>" name="increase_<? echo $k;?>"  class="formbutton" value="+" onClick="add_break_down_tr(<? echo $k;?>)"/>
								<input style="width:30px;" type="button" id="decrease_<? echo $k;?>" name="decrease_<? echo $k;?>"  class="formbutton" value="-" onClick="javascript:fn_deleteRow(<? echo $k;?>)"/>&nbsp;
							</td>  
							<td width="70" align="center" class="must_entry_caption" style="display: none;" id="txtCrossTd_<? echo $k;?>">Cross Date</td>
							<td>
								<input name="txtChkBox_<? echo $k;?>" id="txtChkBox_<? echo $k;?>" class="text_boxes" style="width:50px;display: none;"   type="checkbox" />   
							</td>             
						</tr>
	                <?
	                $k++;
	            	}
	            }
	        ?>
                </tbody>
            </table>
            <div style="float:left; margin:auto" align="center" id="load_page"></div>
            <table>
            	<tr>
            		<td width="550" align="center" colspan="4" valign="middle" class="button_container">
                    	<? echo load_submit_buttons($permission, "fnc_shift_duration_entry", $update_type,0 ,"reset_form('actual_shift_duration','load_page*list_view','','','disable_enable_fields(\'cboProTypeId_\');');",1) ; ?>
                    </td>
            	</tr>
            </table>
		</fieldset>
        <div style="margin-top:5px;" align="center" id="list_view"></div>
	</form>        
</div>
</body>
           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>