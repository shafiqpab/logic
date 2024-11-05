<?
/****************************************************************
|	Purpose			:	This Form Will Create Mandatory Field
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Md. Saidul Islam REZA
|	Creation date 	:	29-05-2019
|	Updated by 		:   	
|	Update date		:    
|	QC Performed BY	:		
|	QC Date			:	
|	Comments		:

Note:
flow blew link for Mandatory Field
-- inventory/get_pass_entry.php

or blew code

if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][251]);?>'){
	if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][251]);?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][251]);?>')==false)
	{
		return;
	}
}			


******************************************************************/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Mandatory Field", "../", 1, 1,'',1,'');
?>	
<script>
	
	var field_val_arr='<? echo $json_field_val_arr ; ?>';
	
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission = '<? echo $permission; ?>';
    
	function fn_set_item(val)
	{		
		//alert(val);
		load_drop_down( 'requires/mandatory_field_controller', val, 'load_drop_down_item', 'field_td');
		get_php_form_data(val, "action_user_data", "requires/mandatory_field_controller" );		
		
		if( $('#txt_update_data_dtls').val()!=0 )
		{
		    set_button_status(1, permission, 'fnc_mandatory_field',1);
		}
		else
		{
			set_button_status(0, permission, 'fnc_mandatory_field',1);
		}		
		
		var row_num=$('#tbl_dtls tbody tr').length;
		for (var i=1; i<=row_num; i++)
		{
			$('#cboFieldId_'+i).val(0);
			$('#cboIsMandatory_'+i).val(0);
			fn_deletebreak_down_tr(i);
		}		
		
		if( $('#txt_update_data_dtls').val()!=0 )
		{			
			var strs=$('#txt_update_data_dtls').val();
			var str=strs.split("@@");
			for(var i=1; i <= str.length; i++)
			{
				if(i<str.length)add_break_down_tr( i );
				var row=str[(i-1)].split("*");
				$('#cboFieldId_'+i).val(row[2]);
				$('#cboIsMandatory_'+i).val(row[4]);
			}
		}	
	}
	
	function openmypage()
	{		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/mandatory_field_controller.php?action=pagename_popup','Page Name Pop Up', 'width=400px,height=400px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("entry_form_id");
			var theemail2=this.contentDoc.getElementById("entry_form_name");
			document.getElementById('cbo_page_id').value=theemail.value;
			document.getElementById('cbo_page_name').value=theemail2.value;
			fn_set_item(theemail.value);
		}
	}

	//fnc_field_level_access
	function fnc_mandatory_field( operation )
	{
		if (form_validation('cbo_page_id','Page Name')==false){return;}
		else
		{
		
			var field_id_arr=new Array();
			var row_num=$('#tbl_dtls tbody tr').length;
			var field='cbo_page_id';
			for (var i=1; i<=row_num; i++)
			{
				var cboFieldId=$('#cboFieldId_'+i).val();
				if(cboFieldId!=0) {
					if( jQuery.inArray( $('#cboFieldId_' + i).val(), field_id_arr ) == -1 )
					{
						field_id_arr.push( $('#cboFieldId_' + i).val() );
					}
					else
					{
						alert("Duplicate Field Name Not Allow");return;
					}
				}
				field+='*cboFieldId_'+i+'*cboIsMandatory_'+i;
			}
		}
		
		var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+get_submitted_data_string(field,"../");
		freeze_window(operation);
		http.open("POST","requires/mandatory_field_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_mandatory_field_reponse;
	}
		
	function fnc_mandatory_field_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=http.responseText.split('**');
			show_msg(trim(reponse[0]));
			if(reponse[0]==0 || reponse[0]==1)
			{
				get_php_form_data(reponse[1], "action_user_data", "requires/mandatory_field_controller" );
				set_button_status(1, permission, 'fnc_mandatory_field',1);
				release_freezing();
			}
			else if(reponse[0]==20)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			else if(reponse[0]==2)
			{
				var row_num=$('#tbl_dtls tbody tr').length;
				for (var i=1; i<=row_num; i++)
				{
					$('#cboFieldId_'+i).val(0);
					$('#cboIsMandatory_'+i).val(0);
					fn_deletebreak_down_tr(i);
				}
				set_button_status(0, permission, 'fnc_mandatory_field',1);
				release_freezing();
				return;
			}
			
			release_freezing();
		}
	}
	
	function add_break_down_tr( i) 
	{
		var chargefor=0;
		var row_num=$('#tbl_dtls tbody tr').length;
		if (row_num!=i)
		{
			return false;
		}
		i++;
		
		if(form_validation('cbo_page_id','Page Name')==false)
		{
			alert("Please Select Page Name Field"); return;
		}
		
		$("#tbl_dtls tbody tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i; },
			'name': function(_, name) { var name=name.split("_"); return name[0]; },
			'value': function(_, value) { return value ; }              
			});
			
		}).end().appendTo("#tbl_dtls");

		$('#incrementfactor_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
		$('#decrementfactor_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+");");
		  
	}
	
	function fn_deletebreak_down_tr(rowNo) 
	{
		if(rowNo!=1)
		{
			var index=rowNo-1
			$("#tbl_dtls tbody tr:eq("+index+")").remove();
			var numRow=$('#tbl_dtls tbody tr').length;
			for(i = rowNo;i <= numRow;i++){
				$("#tbl_dtls tr:eq("+i+")").find("input,select").each(function() {
					$(this).attr({
					  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					  'value': function(_, value) { return value }              
					}); 
					
				$('#incrementfactor_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
				$('#decrementfactor_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
				})

			}
		}		
	}
    </script>
</head>
<body onLoad="set_hotkey()">
    <div align="center"> 
        <? echo load_freeze_divs ("../../",$permission); ?>
        <form id="mandatory_field_1" name="mandatory_field_1" autocomplete="off">
            <fieldset style="width:500px"><legend>Mandatory Field</legend>
                <table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="tbl_mst">
                    <thead>
                        <th colspan="3">
                        	Page Name <input type="text" name="cbo_page_name" id="cbo_page_name" class="text_boxes" style="width:130px" placeholder="Browse" onDblClick="openmypage();"/>
                        	<input type="hidden" id="cbo_page_id" name="cbo_page_id"/>
                        <? //echo create_drop_down("cbo_page_id",220,$entry_form,"",1,"-- Select --","","fn_set_item( this.value );","",implode(',',array_keys($fieldlevel_arr)),"","","","","",""); ?>
                        </th>
                    </thead>
                </table>
                <br>
                <table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="tbl_dtls">
                    <thead>
                        <th width="220">Field Name</th>
                        <th width="180">Mandatory</th>
                        <th></th>
                    </thead>
                    <tbody id="dtls_body">
                        <tr>
                            <td align="center" id="field_td">
                              <? echo create_drop_down("cboFieldId_1",200,$blank_array,"",1,"----Select----",0,"","","","","","","","","cbo_field_id[]"); ?>
                            </td>
                            <td align="center">
                                <? echo create_drop_down("cboIsMandatory_1",150,$yes_no,"",1,"-- Select --",0,"","","","","","","","","cbo_permission_id[]"); ?> 
                            </td>
                            <td align="center" id="increment_1">
                                <input style="width:30px;" type="button" id="incrementfactor_1" name="incrementfactor_1"  class="formbutton" value="+" onClick="add_break_down_tr(1)"/>
                                <input style="width:30px;" type="button" id="decrementfactor_1" name="decrementfactor_1"  class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1)"/>&nbsp;
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" align="center" style="padding-top:10px;" class="button_container">
                                <? 
                                echo load_submit_buttons( $permission, "fnc_mandatory_field()", 0,0 ,"reset_form('mandatory_field_1','','','','','')",1); 
                                ?>
                                <input type="hidden" id="txt_update_data_dtls" readonly disabled>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </fieldset>
            <div id="fieldlevel_list_view"></div>
        </form>
    </div>
</body>
</html>