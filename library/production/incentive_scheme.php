<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Incentive Schema Library
					 
Functionality	:	 
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	03-12-2013
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
echo load_html_head_contents("Sewing Operation Entry", "../../", 1, 1,$unicode,'','');
 ?>	
<script language="javascript">
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission='<? echo $permission; ?>';

	function class_interval()
	{
		var data=document.getElementById('cbo_company_id').value+"__"+document.getElementById('cbo_location_id').value+"__"+document.getElementById('cbo_department_id').value+"__"+document.getElementById('cbo_section_id').value+"__"+document.getElementById('cbo_designation_id').value;
		show_list_view(data,'show_dtls_list_view','list_container','requires/incentive_scheme_controller','setFilterGrid("list_view",-1)','');
	}

	function update_incentive_data( id )
	{
		var text=return_global_ajax_value( id, "show_incentive_list", '', 'requires/incentive_scheme_controller');
		$("#tbl_share_details_entry tbody").html('');
		$("#tbl_share_details_entry tbody").html(text);
	}

	function fnc_incentive_scheme( operation )
	{
		 if(operation==2)
			{
				show_msg('13');
				return;
			}
		
		if (form_validation('cbo_company_id*cbo_designation_id*txtlowerlimit_1*txtuperlimit_1*txttakaday_1','Company Name*Designation*Lower Limit*Uper Limit*Taka/Day')==false)	
		{
			return;
		}
	
		else
		{
			var tot_row=$('#tbl_share_details_entry tr').length-1;
			var data1="action=save_update_delete&operation="+operation+"&tot_row="+tot_row+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_department_id*cbo_section_id*cbo_designation_id*update_id',"../../");
			
			var data2='';
			for(var i=1; i<=tot_row; i++)
			{
				data2+=get_submitted_data_string('txtlowerlimit_'+i+'*txtuperlimit_'+i+'*txttakaday_'+i+'*updateiddtls_'+i+'',"../../");
			}
			var data=data1+data2;
			freeze_window(operation);
			http.open("POST","requires/incentive_scheme_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_incentive_scheme_reponse;
		}
	}
	
	function fnc_incentive_scheme_reponse()
	{
		if(http.readyState == 4) 
		{	
		
			var reponse=trim(http.responseText).split('**');
			//document.getElementById('update_id').value = reponse[1];
			show_msg(reponse[0]);
			show_list_view(document.getElementById('cbo_company_id').value,'show_dtls_list_view','list_container','requires/incentive_scheme_controller','setFilterGrid("list_view",-1)','');
			var tot_row=$('#tbl_share_details_entry tr').length-1;
			for(var i=1; i<=tot_row; i++)
			{
				reset_form('','','txtlowerlimit_'+i+'*txtuperlimit_'+i+'*txttakaday_'+i+'*updateiddtls_'+i+'*delid_'+i+'');
			}
			set_button_status(0, permission, 'fnc_incentive_scheme',1);
			release_freezing();
		}
	}

	function add_share_row( i ) 
	{
		var row_num=$('#tbl_share_details_entry tr').length-1;
		if (row_num!=i)
		{
			return false;
		}
		
		if (form_validation('cbo_company_id*cbo_designation_id*txtlowerlimit_'+i+'*txtuperlimit_'+i+'*txttakaday_'+i+'','Company Name*Designation*Lower Limit*Uper Limit*Taka/Day')==false)
		{
			return;
		}
		i++;
		$("#tbl_share_details_entry tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			'name': function(_, name) { return name + i },
			'value': function(_, value) { return value }              
		   });
		}).end().appendTo("#tbl_share_details_entry");
		reset_form('','','updateiddtls_'+i+'*txtlowerlimit_'+i+'*txtuperlimit_'+i+'*txttakaday_'+i+'');
		$('#txtlowerlimit_'+i).removeAttr("onBlur").attr("onBlur","duplication_check("+i+");");
		$('#txtuperlimit_'+i).removeAttr("onBlur").attr("onBlur","duplication_check("+i+");");
/*		$('#increaseconversion_'+i).removeAttr("onClick").attr("onClick","add_share_row("+i+");");
		$('#decreaseconversion_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+','+'"tbl_share_details_entry"'+");");
*/		$('#increaseconversion_'+i).removeAttr("onClick").attr("onClick","add_share_row("+i+");");
		$('#decreaseconversion_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_share_details_entry');");
		set_all_onclick();
	}

	function fn_deletebreak_down_tr(rowNo,table_id ) 
	{
		var numRow = $('#'+table_id+' tbody tr').length;
		var updel=document.getElementById('updateiddtls_'+rowNo).value;
		var del=$('#delid_'+rowNo).val( updel );
		//alert (del);
		if(numRow==rowNo && rowNo!=1)
		{
			$('#'+table_id+' tbody tr:last').remove();
		}
		else
			return false;
	}
	
	function value_check()
	{
		var tot_row=$('#tbl_share_details_entry tr').length-1;
		for(var i=1; i<=tot_row; i++)
		{
			var low=document.getElementById('txtlowerlimit_'+i).value;
			var up=document.getElementById('txtuperlimit_'+i).value;
		
			if (low>up)
			{
				alert ('Uper limit Gretar then lower limit.');
				$('#txtuperlimit_'+i).val('');
				$('#txtuperlimit_'+i).focus();
				$('#txtuperlimit_'+i).select();
			}
		}
	}
	
function duplication_check(row_id)
{
	var row_num=$('#tbl_share_details_entry tr').length-1;
	var txtlowerlimit=$('#txtlowerlimit_'+row_id).val();
	var txtuperlimit=$('#txtuperlimit_'+row_id).val();
	
	for(var j=1; j<=row_num; j++)
	{
		if(j==row_id)
		{
			continue;
		}
		else
		{
			var txt_lower_check=$('#txtlowerlimit_'+j).val();
			var txt_uper_check=$('#txtuperlimit_'+j).val();

			if(txtlowerlimit==txt_lower_check)
			{
				alert("Duplicate value found.");
				$('#txtlowerlimit_'+row_id).val('');
				return;
			}
			if(txtuperlimit==txt_uper_check)
			{
				alert("Duplicate value found.");
				$('#txtuperlimit_'+row_id).val('');
				return;
			}
		}
	}
}
	
</script>
</head>
<body onLoad="set_hotkey()">
<div align="center" style="width:100%">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="incentivescheme_1" id="incentivescheme_1" autocomplete="off">	
	<fieldset style="width:1080px;">
	<legend>Incentive Scheme Info</legend>
        <table cellpadding="0" cellspacing="2" width="1070">
            <tr>
                <td width="70" class="must_entry_caption">Company</td>
                <td width="145">		
                    <? 
                        echo create_drop_down( "cbo_company_id", 140, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name",'id,company_name', 1,"--Select Company--",'' ,"load_drop_down( 'requires/incentive_scheme_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/incentive_scheme_controller', this.value, 'load_drop_down_designation', 'designation_td');class_interval();" );
                   ?>
                   <input type="hidden" id="update_id" style="width:90px" /> 
                </td>
                <td width="65" >Location</td>
                <td width="145" id="location_td">		
                    <? 
                        echo create_drop_down( "cbo_location_id", 140, $blank_array,'', 1, '--Select Location--', 0, ""  );                      
                    ?>
                </td>
                <td width="70" >Department</td>
                <td width="145" id="department_td">		
                    <? 
                        echo create_drop_down( "cbo_department_id", 140, "select id,department_name from  lib_department where is_deleted=0  and status_active=1  order by department_name",'id,department_name', 1, '--Select Department--', 0, "load_drop_down( 'requires/incentive_scheme_controller', this.value, 'load_drop_down_section', 'section_td' )"  );                      
                    ?>
                </td>
                <td width="60">Section</td>
                <td width="145" id="section_td">		
                    <? 
                        echo create_drop_down( "cbo_section_id", 140, $blank_array,'', 1, '--Select Section--', 0, ""  );                      
                    ?>
                </td>
                <td width="75" class="must_entry_caption">Designation</td>
                <td width="145" id="designation_td">		
                    <? 
                        echo create_drop_down( "cbo_designation_id", 140,$blank_array,'', 1, '--Select Designation--', 0, ""  );                      
                    ?>
                </td>
            </tr>
        </table>
    </fieldset>
    <br/>
    <fieldset style="width:300px;">
        <table align="center" width="100%" border="1" rules="all" class="rpt_table" id="tbl_share_details_entry">
        	<thead>
            	<th width="60">Lower Limit(%)</th>
                <th width="60">Uper Limit(%)</th>
                <th width="60">Taka/Day</th>
                <th width="80" >&nbsp;</th>
            </thead>
        	<tbody >
                <tr>
                    <td width="60"><input type="text" name="txtlowerlimit_1" id="txtlowerlimit_1" class="text_boxes_numeric" style="width:60px" onBlur="duplication_check(1);" />
                    <input type="hidden" name="updateiddtls_1" id="updateiddtls_1" style="width:80px" /> </td>
                    <td width="60"><input type="text" name="txtuperlimit_1" id="txtuperlimit_1" class="text_boxes_numeric" style="width:60px" onBlur="value_check();" /> </td>
                    <td width="60"><input type="text" name="txttakaday_1" id="txttakaday_1" class="text_boxes_numeric" style="width:60px" /> </td>
                    <td width="80">
                        <input type="button" id="increaseconversion_1" style="width:35px" class="formbutton" value="+" onClick="add_share_row(1)"/>
                        <input type="button" id="decreaseconversion_1" style="width:35px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1,'tbl_share_details_entry')"/><input type="hidden" name="delid_1" id="delid_1" style="width:80px" /></td>
                </tr>
            </tbody>
        </table>
    </fieldset>
    <br/>
        <table cellpadding="0" cellspacing="1" width="410">
            <tr>
                <td align="center" colspan="4" valign="middle" class="button_container">
					<? 
                        echo load_submit_buttons( $permission, "fnc_incentive_scheme", 0,0,"reset_form('incentivescheme_1','list_container','','','$(\'#tbl_share_details_entry tbody tr:not(:first)\').remove();')",1);
                    ?>
                </td>
            </tr> 
        </table> 
        <div id="list_container"></div>
    </form>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
