<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Report Signature
Functionality	:	Must fill Company, Reort List, Sequence No
JS Functions	:
Created by		:	shajib
Creation date 	: 	17-11-2013
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
echo load_html_head_contents("Report Signature", "../../", 1, 1,'','1','');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';
	var str_designation = [<? echo substr(return_library_autocomplete( "select distinct(designation) from variable_settings_signature", "designation"  ), 0, -1); ?>];
	
	function fnc_report_signature( operation )
	{  
	// if(operation==1)
	// {
	// 	alert("Update Is Restricted in Variable Settings");
	// 	return;
	// }
	// if(operation==2)
	// {
	// 	alert("Delete Is Restricted in Variable Settings");
	// 	return;
	// }
		if(form_validation('cbo_report_name','Report List')==false)
		{
			return;
		}
		else
		{ 
			var tot_row=$('#tbl_report tbody tr').length;
			var data_all=''; var j=0;
			for(var i=1;i<=tot_row;i++)
			{
				var txtDesignation=$('#txtDesignation_'+i).val();
				var txtName=$('#txtName_'+i).val();
				var txtSequenceNo=$('#txtSequenceNo_'+i).val();
				
				if ((txtDesignation!="")) //&& txtName!="" && txtSequenceNo!=""
				{
					j++;
					data_all+="&txtDesignation_" + j + "='" + $('#txtDesignation_'+i).val()+"'"+"&txtName_" + j + "='" + $('#txtName_'+i).val()+"'"+"&txtSequenceNo_" + j + "='" + $('#txtSequenceNo_'+i).val()+"'"+"&txtActivities_" + j + "='" + $('#txtActivities_'+i).val()+"'"+"&txtUser_" + j + "='" + $('#txtUser_'+i).val()+"'";
				}
			}
			
			if(data_all=='')
			{
				alert("No Data Select");	
				return;
			}
			//alert(data_string);return;
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_report_name*cbo_template_id*cbo_prepared_by',"../../")+data_all+'&tot_row='+j;
			
			freeze_window(operation);
			http.open("POST","requires/report_signature_all_company_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_report_signature_reponse;
		}
	}

	function fnc_report_signature_reponse()
	{
		if(http.readyState == 4) 
		{
			
			var response=trim(http.responseText).split('**');
			
			show_msg(response[0]);
			
			if((response[0]==0 || response[0]==1 || response[0]==2))
			{
				show_list_view(response[1]+'_'+response[2]+'_'+response[4], 'signature_details', 'list_view_report_signature', 'requires/report_signature_all_company_controller', '' ) ;
			}

			set_button_status(response[3], permission, 'fnc_report_signature',1);
			release_freezing();
			
		}
	}
	
	function check_company() 
	{
		if(form_validation('cbo_report_name','Report List')==false)
		{
			//alert('Please Select Company First');
			return;
		}
	
		reset_form('','','','',"$('#tbl_report tbody tr:not(:first)').remove();",'');
		show_list_view(document.getElementById('cbo_report_name').value+'_'+document.getElementById('cbo_template_id').value, 'signature_details', 'list_view_report_signature', 'requires/report_signature_all_company_controller', '' ) ;	
		
		var button_status=$('#button_status').val();
		set_button_status(button_status, permission, 'fnc_report_signature',1);
		
	}
	
	
	function add_break_down_tr( i )
	{ 
		var row_num=$('#tbl_report tbody tr').length;
		
		if (row_num!=i)
		{
			return false;
		}
		else
		{ 
			var lst_sqnc_no=$('#txtSequenceNo_'+i).val();
			i++;
	
			$("#tbl_report tbody tr:last").clone().find("input,select").each(function(){
				  
			$(this).attr({ 
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name },
			  'value': function(_, value) { return '' }              
			});
			 
			}).end().appendTo("#tbl_report");
				
			$("#tbl_report tbody tr:last").removeAttr('id').attr('id','row_'+i);
			
			$('#txtSequenceNo_'+i).val(lst_sqnc_no*1+1);
			$('#increase_'+i).removeAttr("value").attr("value","+");
			$('#decrease_'+i).removeAttr("value").attr("value","-");
			$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
			   
			add_auto_complete(i);
		}
	}

		
	function fn_deleteRow(rowNo) 
	{ 
		var numRow=$('#tbl_report tbody tr').length;
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_report tbody tr:last').remove();
		}
		else
		{
			$("#txtDesignation_"+rowNo).val('');
			$("#txtName_"+rowNo).val('');
			$("#txtSequenceNo_"+rowNo).val('');
		}
	}
	
	function add_auto_complete(i)
	{
		 $("#txtDesignation_"+i).autocomplete({
			 source: str_designation
		  });
	}


</script>

</head>

<body onLoad="set_hotkey()">
	<div align="center" style="width:1000px;">
		<? echo load_freeze_divs ("../../",$permission); ?>
        
        <fieldset style="width:750px;">
        <legend>Report Signature</legend>
            <form name="reportsignature_1" id="reportsignature_1" autocomplete="off">	
      			<table width="800" cellspacing="0" cellpadding="0" border="1" rules="all">
                	<tr>
                    	
					<td class="must_entry_caption">Report List</td>
                        <td>
							<? 
							    $rep_arr=array(0 => "-- Select Report --",481 => "Comparative Statement General", 523 => "Comparative Statement Yarn", 482 => "Comparative Statements Accessories", 512 => "Comparative Statements Fabrics",  513 => "Comparative Statement");
								echo create_drop_down( "cbo_report_name", 150, $rep_arr,'', 0, '', 0, "check_company();");
                            ?>
                    </td>
                        <td class="must_entry_caption">Template</td>
                        <td>
							<?
							
                                echo create_drop_down( "cbo_template_id", 100, $report_template_list,'', 0, '', 0, "check_company();");
                            ?>
                        </td>
                    </tr>
        		</table>
                <div style="margin-top:10px" id="list_view_report_signature"></div>
		</form>	
	</fieldset>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>