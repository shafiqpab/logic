<?
/****************************************************************
|	Purpose			:	This Form Will Create Field Level Access
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Jahid
|	Creation date 	:	19-01-2016
|	Updated by 		:   	
|	Update date		:    
|	QC Performed BY	:		
|	QC Date			:	
|	Comments		:
******************************************************************/
 
	session_start();
	if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
	require_once('../includes/common.php');
	include('../includes/field_list_array.php');
	extract($_REQUEST);
	$_SESSION['page_permission'] = $permission;
	//--------------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents("Field Level Access", "../", 1, 1,'',1,'');
 
	?>	
	<script>
	var field_val_arr='<? echo $json_field_val_arr ; ?>';
	
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission = '<? echo $permission; ?>';
	
    // ======================= user Information pop up Start ===========================
    function openmypage_color()
	{	
        var user_id = $('#text_user_id').val();
		var title = 'User Info';	
		var page_link='requires/field_level_access_controller.php?user_id='+user_id+'&action=color_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=350px,height=350px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("hidden_user_id").value;	 //Access form field with id="emailfield"
			var theename=this.contentDoc.getElementById("hidden_user_name").value; //Access form field with id="emailfield"
			
			$('#txt_user_name').val(theename);
			$('#text_user_id').val(theemail);
		}
	}   
    // ======================= user Information pop up End ===========================
    
    //fnc_field_level_access
	function fnc_field_level_access( operation )
	{
		//alert("su..re");
		if (form_validation('cbo_company_name*text_user_id*cbo_page_id','Company*User ID*Page Name')==false) return;
		
		var row_num=$('#tbl_dtls tbody tr').length;
		//var update_dts_id=$('#update_dts_id').val();
		var update_id=$('#update_id').val();
		var button_status_check=$('#button_status_check').val()*1;
		var data_all="";
		var field_name_arr=new Array();
		var field_user_name_arr=new Array();
		var total_row=0;
		for (var i=1; i<=row_num; i++)
		{
			/* if( form_validation('cboFieldId_'+i,'Field Name')==false )
			{
				return;
			} */
            var cboFieldId=$('#cboFieldId_'+i).val();
            var cboUserId=$('#cboUserId_'+i).val();

            var field_user_name_val = cboFieldId+"_"+cboUserId;
            
            if(cboFieldId!=0) {
            	if(button_status_check==1)
            	{
            		if( jQuery.inArray(field_user_name_val, field_user_name_arr ) == -1)
	                {
	                    
	                    field_user_name_arr.push(field_user_name_val);
	                    
	                }
	                else
	                {
	                    //alert("Duplicate Field Name Not Allow With Same User");return;
	                }
            	}
            	else
            	{
	                if( jQuery.inArray( $('#cboFieldId_' + i).val(), field_name_arr ) == -1 )
	                {
	                    field_name_arr.push( $('#cboFieldId_' + i).val() );
	                    
	                }
	                // else
	                // {
	                //     alert("Duplicate Field Name Not Allow");return;
	                // }
	            }
            }
			
			var txtFieldName=$('#txtFieldName_'+i).val();
			var cboIsDisable=$('#cboIsDisable_'+i).val();
			var setDefaultVal=$('#setDefaultVal_'+i).val();
			var hideDtlsId=$('#hideDtlsId_'+i).val();
			var cboUserId=$('#cboUserId_'+i).val();
			//alert(txtFieldName);
			//return;
			data_all+='&cboFieldId_'+i+'='+cboFieldId+'&txtFieldName_'+i+'='+txtFieldName+'&cboIsDisable_'+i+'='+cboIsDisable+'&setDefaultVal_'+i+'='+setDefaultVal+'&hideDtlsId_'+i+'='+hideDtlsId+'&cboUserId_'+i+'='+cboUserId;
			total_row++;
			
		}
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*text_user_id*cbo_page_id*txtDeleteRow*update_id',"../")+data_all+'&total_row='+total_row;
		
		//alert(data);
		
		freeze_window(operation);
		http.open("POST","requires/field_level_access_controller.php",true);
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
				//show_list_view(reponse[2]+'**'+reponse[3]+'**'+reponse[4],'action_user_data','dtls_body','requires/field_level_access_controller','');
				get_php_form_data(reponse[2]+'**'+reponse[3]+'**'+reponse[4], "action_user_data", "requires/field_level_access_controller" );

				$('#update_id').val(reponse[1]);               
				set_button_status(1, permission, 'fnc_field_level_access',1);
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

	function set_item(val)
	{
		// alert(val);
		/* if(form_validation('cbo_company_name*text_user_id','Company*User ID')==false)
		{
			$('#cbo_page_id').val(0);
			return;
		} */ 

		var company_id = $('#cbo_company_name').val();
		var user_id = $('#text_user_id').val();
		load_drop_down( 'requires/field_level_access_controller', val, 'load_drop_down_item', 'fieldtd');
		// show_list_view(company_id+'**'+user_id+'**'+val,'action_user_data','dtls_body','requires/field_level_access_controller','');
		get_php_form_data(company_id+'**'+user_id+'**'+val, "action_user_data", "requires/field_level_access_controller" );
		
		
		if( $('#txt_update_data_dtls').val()!=0 )
		{
		  var button_status_check=1;//$('#button_status_check').val();
		}
		//var button_status_check=1;//$('#button_status_check').val();
		if(button_status_check==1)
		{
			$('#user_name_th').attr('style','display:true;');
			$('#user_name_td').attr('style','display:true;');
			$('#button_status_check').val(1);
			set_button_status(1, permission, 'fnc_field_level_access', 1);
		}
		else
		{
			$('#user_name_th').attr('style','display:none;');
			$('#user_name_td').attr('style','display:none;');
			$('#button_status_check').val(0);
			set_button_status(0, permission, 'fnc_field_level_access', 1);
		}
		if( $('#txt_update_data_dtls').val()!=0 )
		{
			 
			var strs=$('#txt_update_data_dtls').val();
			var str=strs.split("@@");
			var i=1;
			for(var k=0; k<str.length; k++)
			{
				var srow=str[k].split("*");
				
				$('#cboFieldId_'+i).val(srow[2]);
				set_hide_data( srow[2]+"**"+i );
				//alert(srow[3]);
				$('#cboIsDisable_'+i).val(srow[4]);
				$('#txtFieldName_'+i).val(srow[3]);
				$('#setDefaultVal_'+i).val(srow[5]);
				$('#hideDtlsId_1'+i).val(srow[0]);
				$('#update_id').val(srow[1]);
				$('#cboUserId_'+i).val(srow[6]);
				
				//$('#cboFieldId_'+i).val(srow[2]);
				//$('#cboFieldId_'+i).val(srow[2]);
				
				add_factor_row( i ); 
				
				i++;
			}
		}
		// alert("NO");
	}
	
		
	function add_factor_row( i) 
	{	
		var chargefor=0;
		var row_num=$('#tbl_dtls tbody tr').length;
		//alert(row_num);
		if (row_num!=i)
		{
			return false;
		}
		i++;
		
		if(form_validation('cbo_page_id','Page Name')==false)
		{
			alert("Please Select Page Name Field");return;
			return;
		}
		
		$("#tbl_dtls tbody tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i; },
			'name': function(_, name) { var name=name.split("_"); return name[0]; },
			'value': function(_, value) { return value ; }              
			});
			
		}).end().appendTo("#tbl_dtls");

		$('#tbl_dtls tbody tr:last td:eq(3)').removeAttr('id').attr('id','tdId_'+i);
		$("#tbl_dtls tbody tr:last ").removeAttr('id').attr('id','tr_'+i);
		$("#tbl_dtls tbody tr td:last ").removeAttr('id').attr('id','increment_'+i);
		$("#tbl_dtls tbody tr:last").find(':input:not(:button)','select').val("");
		$('#tbl_dtls tbody tr:last td:eq(3)').removeAttr('id').attr('id','tdId_'+i);
			var k=i-1;
			$('#incrementfactor_'+k).hide();
			$('#decrementfactor_'+k).hide();
	
		  
		  $('#incrementfactor_'+i).removeAttr("onClick").attr("onClick","add_factor_row("+i+");");
		  $('#decrementfactor_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+");");
		  $('#cboFieldId_'+i).removeAttr("onChange").attr("onChange","set_hide_data(this.value"+"+'**'+"+i+");");
		  
	}
	
	function fn_deletebreak_down_tr(rowNo) 
	{
		
		var numRow = $('#tbl_dtls tbody tr').length;
		if(numRow==rowNo && rowNo!=1)
		{
			var delete_row=$('#txtDeleteRow').val();
			var current_delete_row=$('#hideDtlsId_'+rowNo).val();
			if(delete_row!="")
			{
				var tot_delete=delete_row+','+current_delete_row;
			}
			else
			{
				var tot_delete=current_delete_row;
			}
			$('#txtDeleteRow').val(tot_delete);
			var k=rowNo-1;
			$('#incrementfactor_'+k).show();
			$('#decrementfactor_'+k).show();
			
			$('#tbl_dtls tbody tr:last').remove();
		}
		else
			return false;
		
	}
	 
	function set_hide_data(ref)
	{ 
		var ref_arr=ref.split('**');
		var page_id=$('#cbo_page_id').val();
		//get_php_form_data(page_id+'**'+ref_arr[0]+'**'+ref_arr[1], "set_field_name", "requires/field_level_access_controller" );
		// alert(ref[0]);
		load_drop_down( 'requires/field_level_access_controller', page_id+'**'+ref_arr[0]+'**'+ref_arr[1]+'**'+$('#cbo_company_name').val(), 'set_field_name', 'tdId_'+ref_arr[1]);
		
		 
		
	}
	/*function set_drop_dwon_data(ref)
	{
		return;
		var cbo_page_id=$('#cbo_page_id').val();
		if(cbo_page_id==108)
		{
			var ref_arr=ref.split('**');
			
			if(ref_arr[0]==1)
			{
				$('#tdId_'+ref_arr[1]).empty().append('<select name="setDefaultVal_" id="setDefaultVal_' +ref_arr[1]+ '"class="combo_boxes " style="width:150px" onchange=""><option data-attr="" value="0">-UOM-</option><option value="1">Pcs</option><option value="12">Kg</option><option value="23">Mtr</option><option value="27">Yds</option></select>');
			}
			else
			{

				$('#tdId_'+ref_arr[1]).empty().append('<select name="setDefaultVal_" id="setDefaultVal_' +ref_arr[1]+ '" class="combo_boxes " style="width:150px" onchange=""><option data-attr="" value="0">-Fabric Source-</option><option value="1">Production</option><option value="2">Purchase</option><option value="3">Buyer Supplied</option><option value="4">Stock</option></select>');
				
			}
			
		}
		
		
	}*/
    </script>
</head>
<body onLoad="set_hotkey()">
    <div align="center"> 
        <? echo load_freeze_divs ("../../",$permission); ?>
        <form id="fieldlevelaccess_1" name="fieldlevelaccess_1" autocomplete="off">
            <fieldset style="width:800px"><legend>Field Level Access</legend>
                <table width="800" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="tbl_mst">
                    <thead>
                        <th width="250">Company &nbsp;&nbsp;<? echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company  comp where status_active=1 and is_deleted=0 $company_cond order by company_name", "id,company_name",1, "-- Select Company --", "", "",0,"" ); ?></th>
                        <th width="250">
                            User &nbsp;&nbsp;
                            <input type="text" name="txt_user_name" id="txt_user_name" class="text_boxes" style="width:140px;" placeholder="Browse" onDblClick="openmypage_color();" readonly/>
                            <input type="hidden" name="text_user_id" id="text_user_id" />
                            <input type="hidden" name="txt_update_data_dtls" id="txt_update_data_dtls" />
                            <input type="hidden" name="button_status_check" id="button_status_check" />
                        </th>
                        <th>
						Page Name &nbsp;&nbsp;<? echo create_drop_down("cbo_page_id",220,$entry_form,"",1,"-- Select --","","set_item( this.value );","",implode(',',array_keys($fieldlevel_arr)),"","","98","","","cbo_page_id[]"); ?></th>
                    </thead>
                </table>
                <br>
                <table width="800" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="tbl_dtls">
                    <thead>
                        <th width="150" id="user_name_th" style="display:none;">User Name</th>
                        <th width="180">Field Name</th>
						<th width="150">Keep Disable</th>
						<th width="120">Set Default Value</th>
                        <th></th>
                    </thead>
                    <tbody id="dtls_body">
                       <tr>
                            <td align="center" id="user_name_td" style="display:none;">
                              <?php
                              	$nameArray = return_library_array( "select id,user_name from user_passwd where valid=1", "id", "user_name" );
                              	echo create_drop_down("cboUserId_1",150,$nameArray,"id,user_name",1,"----Select----",0,"",0,"","","","","","","cbo_user_id[]");
                              ?>
                            </td>
                            <td align="center" id="fieldtd">
                              <? echo create_drop_down("cboFieldId_1",180,$blank_array,"",1,"----Select----",0,"","","","","","","","","cbo_field_id[]"); ?>
                            </td>
							<td align="center">
                                <? echo create_drop_down("cboIsDisable_1",150,$yes_no,"",1,"-- Select --",0,"","","","","","","","","cbo_permission_id[]"); ?> 
<!--                                <input type="hidden" id="txtFieldName_1" name="" value="" style="width:100px;" />  
-->                            </td>
							<td align="center" id="tdId_1">
                                <input type="text" id="setDefaultVal_1" name="" style="width:100px" class="text_boxes" />
                                <input type="hidden" id="hideDtlsId_1" name="hideDtlsId[]" style="width:100px;" value="" /> 

                            </td>
                            
                            
                            <td align="center" id="increment_1">
                                <input style="width:30px;" type="button" id="incrementfactor_1" name="incrementfactor_1"  class="formbutton" value="+" onClick="add_factor_row(1)"/>
                                <input style="width:30px;" type="button" id="decrementfactor_1" name="decrementfactor_1"  class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1)"/>&nbsp;
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" align="center" style="padding-top:10px;" class="button_container">
                                <? 
                                echo load_submit_buttons($permission, "fnc_field_level_access", 0, 0 ,"reset_form('fieldlevelaccess_1','','','','','cbo_user_id')", 1); 
                                ?>
                                <input type="hidden" id="txtDeleteRow" value="" />
                                <input type="hidden" id="update_id" name="update_id" class="text_boxes" readonly />
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </fieldset>
            <div id="fieldlevel_list_view"></div>
        </form>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script> 
</html>