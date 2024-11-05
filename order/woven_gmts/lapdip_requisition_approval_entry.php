<?
/*-------------------------------------------- Comments ----------------------------------------------
Purpose			: 	This form will create Lab Dip Requisition Approval 
Functionality	:	
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor 
Creation date 	: 	20-05-2023
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
	echo load_html_head_contents("Lapdip Requisition Approval", "../../", 1, 1,'','','');

 ?>	
 
<script>

 if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
 var permission='<? echo $permission; ?>';

	 
		function openmypage(page_link,title)
		{
			var garments_nature=$('#garments_nature').val(); 
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'?action=order_popup&garments_nature='+garments_nature, title, 'width=990px,height=400px,center=1,resize=1,scrolling=0','../');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var theemail=this.contentDoc.getElementById("selected_job") //Access form field with id="emailfield"
				$('#hide_color_id').val('');
				if(theemail.value!="")
				{
					freeze_window(5);
					
					get_php_form_data(theemail.value, "populate_data_from_search_popup", "requires/lapdip_requisition_approval_entry_controller" );
					show_list_view(theemail.value+'**'+0,'lapdip_approval_list_view_edit','lapdip_approval_list_view','requires/lapdip_requisition_approval_entry_controller','',0);
					var check_lapdip_data = return_global_ajax_value( theemail.value, 'check_lapdip_data', '', 'requires/lapdip_requisition_approval_entry_controller');
					console.log(check_lapdip_data);
					if(check_lapdip_data >0){
						set_button_status(1, permission, 'fnc_lapdip_approval',1);
					}else{
						set_button_status(0, permission, 'fnc_lapdip_approval',1);
					}
					
				
					release_freezing();
				}
			}
		}

		function fnc_comments(id,value)
		{
			var page_link='requires/lapdip_requisition_approval_entry_controller.php?action=comments_popup&comments_data='+value;
			var title='Comments Info';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=440px,height=200px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var comments_data=this.contentDoc.getElementById("txt_comments").value;

				$('#'+id).val(comments_data);
			}
		}

		
		function fnc_lapdip_approval( operation )
		{
			 
			if (form_validation('txt_requisition_no','Job No')==false)
			{
				return;
			}	
			else
			{
				var garments_nature=$('#garments_nature').val();
				var txt_requisition_no=$('#txt_requisition_no').val();
				var hiddReqId=$('#hiddReqId').val();				
				var numberOfColor=$('#numberOfColor').val();
				var color_data='';
				var rowString='';
				var z=1;


				for(z=1; z<=numberOfColor; z++){

					var hide_color_id=$('#colorId_'+z).val();
					var current_status=$('#current_status_'+hide_color_id).val();
					var tot_row=$('#table_'+hide_color_id+' tbody tr').length;

				
					for(i=1; i<=tot_row; i++)
					{
						var action=$('#action_'+hide_color_id+'_'+i).val();
						
						
						if(action==2 || action==3)
						{
							if(form_validation('actionDate_'+hide_color_id+'_'+i+'','Action Date')==false)
							{
								return;
							}
						}

						color_data+=get_submitted_data_string('targetAppDate_'+hide_color_id+'_'+i+'*color_'+hide_color_id+'_'+i+'*sendToFactoryDate_'+hide_color_id+'_'+i+'*recvFromFactoryDate_'+hide_color_id+'_'+i+'*submittedToBuyer_'+hide_color_id+'_'+i+'*action_'+hide_color_id+'_'+i+'*actionDate_'+hide_color_id+'_'+i+'*txtLapdipNo_'+hide_color_id+'_'+i+'*txtComments_'+hide_color_id+'_'+i+'*cboStatus_'+hide_color_id+'_'+i+'*updateid_'+hide_color_id+'_'+i+'*hiddReqDtlsId_'+z+'*colorId_'+z,"../../",i);	
					}
					// colorRow_$('#colorRow_'+hide_color_id).val(i);
					rowString+="&colorRow_"+hide_color_id+"="+i;	

				}
				
				//   alert (color_data); return;

				var data="action=save_update_delete&operation="+operation+color_data+"&garments_nature="+garments_nature+"&txt_requisition_no="+txt_requisition_no+"&current_status="+current_status+"&tot_row="+tot_row+"&numberOfColor="+numberOfColor+rowString+"&hiddReqId="+hiddReqId;
				// alert (data); return;
				freeze_window(operation);
				http.open("POST","requires/lapdip_requisition_approval_entry_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_lapdip_approval_reponse;
			}
			
		}
	 
		function fnc_lapdip_approval_reponse()
		{
			if(http.readyState == 4) 
			{
			// alert(http.responseText);
				var response=trim(http.responseText).split('**');
				show_msg(trim(response[0]));
				$('#hide_color_id').val('');
				//  get_php_form_data(response[1], "populate_data_from_search_popup", "requires/lapdip_requisition_approval_entry_controller" );
				//  show_list_view(response[1]+'**'+0,'lapdip_approval_list_view_edit','lapdip_approval_list_view','requires/lapdip_requisition_approval_entry_controller','',0);
				if(response[0]==0){
					set_button_status(1, permission, 'fnc_lapdip_approval',1);
				}else{
					set_button_status(0, permission, 'fnc_lapdip_approval',1);
				}
				
				release_freezing();
			}
		}


		function add_break_down_tr(color_id,i )
		{
			var row_num=$('#table_'+color_id+' tbody tr').length;

           
			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;
				$("#table_"+color_id+" tbody tr:last").clone().find("input,select").each(function(){
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+color_id+"_"+ i },
					'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+color_id+"_"+ i },
					'value': function(_, value) { return value }
				});

				}).end().appendTo("#table_"+color_id);

                $('#targetAppDate_'+color_id+'_'+i).val('')
                $('#sendToFactoryDate_'+color_id+'_'+i).val('')
                $('#recvFromFactoryDate_'+color_id+'_'+i).val('')
                $('#submittedToBuyer_'+color_id+'_'+i).val('')
                $('#action_'+color_id+'_'+i).val('')
                $('#actionDate_'+color_id+'_'+i).val('')
                $('#txtLapdipNo_'+color_id+'_'+i).val('')
                $('#txtComments_'+color_id+'_'+i).val('')
                $('#cboStatus_'+color_id+'_'+i).val('')

				
				$('#increase_'+color_id+'_'+i).removeAttr("value").attr("value","+");
				$('#decrease_'+color_id+'_'+i).removeAttr("value").attr("value","-");
				$('#increase_'+color_id+'_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+color_id+','+i+");");
				$('#decrease_'+color_id+'_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+color_id+','+i+");");
                
 			}
		}

		 
		function fn_deleteRow(color_id,rowNo)
		{

			var row_num=$('#table_'+color_id+' tbody tr').length;

			if(row_num!=1)
			{
				// alert(row_num);
				$("#tr_"+rowNo).remove();
				var i = 1;
				
				$("#table_"+color_id+" tbody").find('tr').each(function()
				{
					$(this).removeAttr('id').attr('id','tr_'+i);

					var tr_id = $(this).attr('id');
					console.log('tr => '+tr_id);

					$("#"+tr_id).find("input,select").each(function(){
						$(this).attr({
							'id': function(_, id) {var id=id.split("_"); return id[0] +"_"+color_id+"_"+ i }
						});
					});

					$("#"+tr_id).find("td").each(function(){
						var td_id = $(this).attr('id');
						if(td_id)
						{
							var td_id=td_id.split("_"); 
							td_id = td_id[0] +"_"+color_id+"_"+ i;
							$(this).attr('id',td_id);
						}
					});

					

					$('#increase_'+color_id+'_'+i).removeAttr("value").attr("value","+");
					$('#decrease_'+color_id+'_'+i).removeAttr("value").attr("value","-");
					$('#increase_'+color_id+'_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+color_id+','+i+");");
					$('#decrease_'+color_id+'_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+color_id+','+i+");");
					i++;
				});
			}
		}
</script>
 
</head>
 
<body onLoad="set_hotkey()">

<div style="width:100%;" align="center">
     <? echo load_freeze_divs ("../../",$permission); ?>
    <form id="lapdipapproval_1">
        <fieldset style="width:1280px;">
		<legend>Lapdip Approval</legend>
        	<table width="1275" cellspacing="2" cellpadding="0" border="0">
				<tr>
					<td width="130" align="right" class="must_entry_caption">Requisition No </td>  
                    <td width="170">
                    <input style="width:160px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/lapdip_requisition_approval_entry_controller.php','Requisition  Selection Form')" class="text_boxes" autocomplete="off" placeholder="Search Requisition" name="txt_requisition_no" id="txt_requisition_no" readonly />
                     
                    </td>
                    <td width="130" align="right">Company Name </td>
                    <td width="170"><? echo create_drop_down( "cbo_company_name", 172, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "Display", $selected, "",1 );?> 
                    </td>
                    <td width="130" align="right">Location Name</td>
                    <td><? echo create_drop_down( "cbo_location_name", 172, "select id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "Display", $selected, "",1 );	?>	
                    </td>
                </tr>
                <tr>
                    <td align="right">Buyer Name</td>
                    <td>
                        <? 
                            echo create_drop_down( "cbo_buyer_name", 172, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id order by buyer_name","id,buyer_name", 1, "Display", $selected, "" ,1);   
                        ?>	  
                    </td>
                    <td align="right">Style Ref.</td>
                    <td>
                        <input class="text_boxes" type="text" style="width:160px" disabled placeholder="Display" name="txt_style_ref" id="txt_style_ref"/>	
                    </td>
                    <td align="right">Style Description</td>
                    <td>	
                        <input class="text_boxes" type="text" style="width:160px;" name="txt_style_description" id="txt_style_description" placeholder="Display" disabled/>
                    </td>
                </tr>
                <tr>
                    <td align="right">Pord. Dept.</td>   
                    <td><? echo create_drop_down( "cbo_product_department", 172, $product_dept, "",1, "Display", $selected, "" ,1); ?>
                    </td>
                    <td align="right">Currency</td>
                    <td>
                      <? 
                            echo create_drop_down( "cbo_currercy", 172, $currency, "", 1, "Display", "", "",1 );
                      ?>	  
                    </td>
                    <td align="right">Agent</td>
                    <td>
                    <?	
                        echo create_drop_down( "cbo_agent", 172, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  and a.id in (select  buyer_id from lib_buyer_party_type where party_type in (20,21)) order by buyer_name","id,buyer_name", 1, "Display", $selected, "",1 );  
                    ?>
                    </td>
                </tr>
                <tr>
                    <td  align="right">Region</td>
                    <td>
                        <? 
                            echo create_drop_down( "cbo_region", 172, $region, "",1, "Display", $selected, "",1 );
                        ?>	  
                    </td>
                    <td align="right">Team Leader</td>   
                    <td>
                        <?  
                            echo create_drop_down( "cbo_team_leader", 172, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "Display", $selected, "",1 );
                        ?>		
                    </td>
                    <td align="right">Dealing Merchant</td>   
                    <td> 
                        <? 
                            echo create_drop_down( "cbo_dealing_merchant", 172, "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "Display", $selected, "",1 );
                        ?>	
                   </td>
                </tr>
                 
                <tr>
                	<td colspan="6" id="lapdip_approval_list_view"></td>
                </tr>
                <tr>
                    <td align="center" colspan="10" valign="middle" class="button_container">
                      <? echo load_submit_buttons($permission, "fnc_lapdip_approval", 0,0 ,"reset_form('lapdipapproval_1','','','','')",1) ; ?>
                    </td>
                </tr>
            </table>
		</fieldset>
	</form>        
</div>
</body>
           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>