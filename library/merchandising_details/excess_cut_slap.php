<?php
/******************************************************************
|	Purpose			:	This form will create Excess Cut Slap Entry
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Aziz
|	Creation date 	:	19.12.2015
|	Updated by 		: 		
|	Update date		:    
|	QC Performed BY	:		
|	QC Date			:	
|	Comments		:
********************************************************************/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
//echo load_html_head_contents("Excess Cut Entry", "../../", 1, 1,$unicode,1,'');
echo load_html_head_contents("Excess Cut Entry","../../", 1, 1, "",'','');

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	function fnc_excess_cut_slap( operation )
	{
		freeze_window(operation);
		if (form_validation('cbo_company_name*cbo_buyer_name','Company Name*Buyer Name')==false)
		{
			release_freezing();
			return;
		}
		var row_num=$('#tbl_cut_details tbody tr').length;
		var data1=''; var z=1;
		
		for(var i=1; i<=row_num; i++)
		{
			//data2+=get_submitted_data_string('updateid_'+i+'*txtupperid_'+i+'*txtlowerid_'+i+'*txtslapid_'+i+'*txttotalper_'+i+'*txtprint_'+i+'*txtembro_'+i+'*txtgmtwash_'+i+'*txtspworks_'+i+'*txtcutting_'+i+'*txtsewing_'+i+'*txtfinishing_'+i+'*cboprint_'+i+'*cboembro_'+i+'*cbogmtwash_'+i+'*cbospworks_'+i+'*cbocutting_'+i+'*cbosewing_'+i+'*cbofinishing_'+i+'*txtshipplan_'+i+'*cboshipplan_'+i,"../../",i);
			
			data1+="&updateid_" + z + "='" + $('#updateid_'+i).val()+"'"+"&txtupperid_" + z + "='" + $('#txtupperid_'+i).val()+"'"+"&txtlowerid_" + z + "='" + $('#txtlowerid_'+i).val()+"'"+"&txtslapid_" + z + "='" + $('#txtslapid_'+i).val()+"'"+"&txttotalper_" + z + "='" + $('#txttotalper_'+i).val()+"'"+"&txtprint_" + z + "='" + $('#txtprint_'+i).val()+"'"+"&txtembro_" + z + "='" + $('#txtembro_'+i).val()+"'"+"&txtgmtwash_" + z + "='" + $('#txtgmtwash_'+i).val()+"'"+"&txtspworks_" + z + "='" + $('#txtspworks_'+i).val()+"'"+"&txtcutting_" + z + "='" + $('#txtcutting_'+i).val()+"'"+"&txtsewing_" + z + "='" + $('#txtsewing_'+i).val()+"'"+"&txtfinishing_" + z + "='" + $('#txtfinishing_'+i).val()+"'"+"&cboprint_" + z + "='" + $('#cboprint_'+i).val()+"'"+"&cboembro_" + z + "='" + $('#cboembro_'+i).val()+"'"+"&cbogmtwash_" + z + "='" + $('#cbogmtwash_'+i).val()+"'"+"&cbospworks_" + z + "='" + $('#cbospworks_'+i).val()+"'"+"&cbocutting_" + z + "='" + $('#cbocutting_'+i).val()+"'"+"&cbosewing_" + z + "='" + $('#cbosewing_'+i).val()+"'"+"&cbofinishing_" + z + "='" + $('#cbofinishing_'+i).val()+"'"+"&txtshipplan_" + z + "='" + $('#txtshipplan_'+i).val()+"'"+"&txtshipplan_" + z + "='" + $('#txtshipplan_'+i).val()+"'";
			//txtshipplan_1
			
			z++;
		}
		var data="action=save_update_delete&operation="+operation+"&row_num="+row_num+get_submitted_data_string('cbo_company_name*cbo_buyer_name',"../../")+data1;
		//alert(data); release_freezing();return;
		
		http.open("POST","requires/excess_cut_slap_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_excess_cut_slap_reponse;
		
	}
	
	function fnc_excess_cut_slap_reponse()
	{
		if(http.readyState == 4) 
		{  
			var reponse=trim(http.responseText).split('**');
			//alert(reponse[0])
			//reset_form('cuting_list_cont','','');
			if(reponse[0]==10)
			{
				show_msg(trim(reponse[0]));
				release_freezing();
				return;
			}
			if(reponse[0]==0 || reponse[0]==1)
			{
				show_msg(trim(reponse[0]));
				release_freezing();
				set_button_status(1, permission, 'fnc_excess_cut_slap',1,1);
			}
			if(reponse[0]==14)
			{
				alert('Not Allow');
				release_freezing();
				return;
			}
			//alert (reponse[4]);
			var return_id_arr=reponse[4].split(',');
			var k=0;
			for(var j=1;j<=return_id_arr.length;j++)
			{ 
				$("#updateid_"+j).val(return_id_arr[k]);
				k++;
			}
		}
	}
	
	function append_cut_slap_row(val,id)
	{
		//alert(val+'='+id);
			if((val*1)==0)
			{
				fnc_remove_row( id );
				return 0;
			}
			else
			{
				var counter =$('#tbl_cut_details tbody tr').length;
				if(id!=counter) return;
				
				var counter =$('#tbl_cut_details tbody tr').length; 
				
				var upper_val=$("#txtupperid_"+counter).val()*1;
				var lower_val=$("#txtlowerid_"+counter).val()*1;
				//alert(upper_val);
				if(lower_val>upper_val)
				{
					$("#txtupperid_"+counter).val('');
					$('#txtupperid_'+counter).focus();
					return;
				}
				
				if(counter>=1) counter++;
				else if (counter<1) counter=1;
				var z=1;
				for(var i=1;i<=counter;i++)
				{	
					if($("#txtupperid_"+i).val()=="")
					{
						
						z++;
						
					}
					
				}
				//alert(z)
				
				if(z==1)
				{ 
					
					$('#tbl_cut_details tbody').append(
						'<tr id="trCut_'+counter+'">' +
						'<td><input type="text" name="txtslapid[]" class="text_boxes_numeric" id="txtslapid_'+counter+'"  value="'+counter+'" style="width:50px;" readonly/></td><td><input type="text"  name="txtlowerid[]" class="text_boxes_numeric" id="txtlowerid_'+counter+'"  style="width:100px;" readonly/></td><td><input type="text" name="txtupperid[]" align="right" class="text_boxes_numeric" id="txtupperid_'+counter+'"  style="width:100px;" onBlur="append_cut_slap_row(this.value,'+counter+')" /></td><td><input type="text" name="txtpercentid[]" class="text_boxes_numeric" id="txtpercentid_'+counter+'"  style="width:70px;" /><input type="hidden" name="updateid[]" class="text_boxes_numeric" id="updateid_'+counter+'"  style="width:20px;" /></td>'+ '</tr>'
					);
					//fnc_add_val_row(val,counter);
					
					
				}
				//fnc_remove_row();
				
				var data_id='';
				data_id=id+1;
				//alert(data_id);
				var lower_val='';
				lower_val=(val*1)+1;
				$("#txtlowerid_"+data_id).val(lower_val);
				
			}
			
		
		
	}
	function fnc_remove_row( id )
	{
		//alert(id)
			 var id=(id*1)+1;
			$('#trCut_'+id).remove();
				
			//alert(counter);
			
	}
	
	
	function fnResetForm()
	{
		reset_form('tbl_cut_details','variable_settings_container','','','','');
	}
	function add_break_down_tr(i)
	{
		var upper_value=$("#txtupperid_"+i).val();
		if(upper_value == '')
		{
			alert("Upper Limit(Qty) can not be null");
			return;
		}
		var row_num=$('#cut_slap_dtls tr').length;
		var j=i;
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			$("#cut_slap_dtls tr:last").clone().find("input,select").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }
				});
			}).end().appendTo("#cut_slap_dtls");
			//$("#cut_slap_dtls tr:last").removeAttr('id').attr('id','trCut_'+i);
			$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+");");

			$('#txtprint_'+i).removeAttr("onChange").attr("onChange","calculate_total_per("+i+");");
			$('#txtembro_'+i).removeAttr("onChange").attr("onChange","calculate_total_per("+i+");");
			$('#txtgmtwash_'+i).removeAttr("onChange").attr("onChange","calculate_total_per("+i+");");
			$('#txtspworks_'+i).removeAttr("onChange").attr("onChange","calculate_total_per("+i+");");
			$('#txtcutting_'+i).removeAttr("onChange").attr("onChange","calculate_total_per("+i+");");
			$('#txtsewing_'+i).removeAttr("onChange").attr("onChange","calculate_total_per("+i+");");
			$('#txtfinishing_'+i).removeAttr("onChange").attr("onChange","calculate_total_per("+i+");");
			$('#txtupperid_'+i).removeAttr("onChange").attr("onChange","check_qty_limit("+i+");");



			$('#txtslapid_'+i).val( i );		
			$("#txtlowerid_"+i).val($("#txtupperid_"+j).val()*1+1);
			$("#txtupperid_"+i).val("")
			$('#txtprint_'+i).val("");
			$('#txtembro_'+i).val("");
			$('#txtgmtwash_'+i).val("");
			$('#txtspworks_'+i).val("");
			$('#txtcutting_'+i).val("");
			$('#txtsewing_'+i).val("");
			$('#txtfinishing_'+i).val("");
			$('#txttotalper_'+i).val("");
			$('#updateid_'+i).val("");
			$("#txtupperid_"+j).attr('disabled','disabled');
		}
	}
	function fn_deletebreak_down_tr(rowNo)
	{
		//var  tnumRow= $('#cut_slap_dtls tr').length;		
		var j=rowNo-1;
		var k=0;
		if(rowNo!=1){
			var index=rowNo-1
			$("table#tbl_cut_details tbody tr:eq("+index+")").remove()
			var numRow = $('#tbl_cut_details tr').length;
			for(i = rowNo;i <= numRow;i++){
				$("#tbl_cut_details tr:eq("+i+")").find("input,select").each(function() {
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						'value': function(_, value) { return value }
					});
					$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
					$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+");");
					$('#txtprint_'+i).removeAttr("onChange").attr("onChange","calculate_total_per("+i+");");
					$('#txtembro_'+i).removeAttr("onChange").attr("onChange","calculate_total_per("+i+");");
					$('#txtgmtwash_'+i).removeAttr("onChange").attr("onChange","calculate_total_per("+i+");");
					$('#txtspworks_'+i).removeAttr("onChange").attr("onChange","calculate_total_per("+i+");");
					$('#txtcutting_'+i).removeAttr("onChange").attr("onChange","calculate_total_per("+i+");");
					$('#txtsewing_'+i).removeAttr("onChange").attr("onChange","calculate_total_per("+i+");");
					$('#txtfinishing_'+i).removeAttr("onChange").attr("onChange","calculate_total_per("+i+");");
					$('#txtupperid_'+i).removeAttr("onChange").attr("onChange","check_qty_limit("+i+");");
					$('#txtslapid_'+i).val( i );
					k=i;					
				})				
			}
			var last_id=numRow-1;
			$('#txtupperid_'+last_id).removeAttr('disabled');
			
		}
	}
	function calculate_total_per(i)
	{
		var total_per=$('#txtprint_'+i).val()*1+$('#txtembro_'+i).val()*1+$('#txtgmtwash_'+i).val()*1+$('#txtspworks_'+i).val()*1+$('#txtcutting_'+i).val()*1+$('#txtsewing_'+i).val()*1+$('#txtfinishing_'+i).val()*1;
		$('#txttotalper_'+i).val(total_per);
	}
	function check_qty_limit(i)
	{
		var lower_val=$('#txtlowerid_'+i).val()*1;
		var upper_val=$('#txtupperid_'+i).val()*1;
		if(lower_val>upper_val)
		{
			alert("Upper Limit can not less then Lower Limit");
			$('#txtupperid_'+i).val("");
			return;
		}
	}
</script>
</head>
<body  onload="set_hotkey()">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <div align="center" style="width:100%;">	
		<form name="excess_cut_1" id="excess_cut_1"  autocomplete="off">
            <fieldset style="width:1300px;"><legend></legend>
			<table cellpadding="0" cellspacing="2" width="520px">
			 	<tr>
                       
                       <td class="must_entry_caption"> Company Name</td>
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/excess_cut_slap_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );show_list_view(this.value,'on_change_data','variable_settings_container','requires/excess_cut_slap_controller','');" );
                            ?>
                        </td>
                        <td class="must_entry_caption"> Buyer Name</td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select --", $selected, "","" );
                            ?>
                           
                        </td>
                        </tr>
               
		   </table>
		 <div style="width:1300px; float:left; min-height:40px; margin:auto" align="center" id="variable_settings_container"></div>
	 </fieldset>
    </form>
	</div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
