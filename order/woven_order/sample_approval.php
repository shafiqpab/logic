<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Woven Garments Sample Approval
Functionality	:	
JS Functions	:
Created by		:	CTO 
Creation date 	: 	12-11-2012
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
echo load_html_head_contents("Woven Sample Approval", "../../", 1, 1,$unicode,'','');
?>	
 
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  

var permission='<? echo $permission; ?>';
// Master Form--------------------------------------------------------------------------------------------
function openmypage(page_link,title)
{
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1150px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var theemail=this.contentDoc.getElementById("selected_job") //Access form field with id="emailfield"
		if (theemail.value!="")
		{
			reset_form('','accordion_container*po_list_view','');
			freeze_window(5);
			get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/sample_approval_controller" );
			var buyer_name=document.getElementById('cbo_buyer_name').value;
			show_list_view(document.getElementById('txt_job_no').value+'_'+buyer_name,'show_sample_approved_list','accordion_container','../woven_order/requires/sample_approval_controller','');
			
			//show_list_view(document.getElementById('txt_job_no').value,'show_sample_approval_list_form','po_list_view','../woven_order/requires/sample_approval_controller','');
			//load_drop_down( '../woven_order/requires/sample_approval_controller', theemail.value, 'load_drop_down_po', 'po_td' )
		 	//set_button_status(0, permission, 'fnc_sample_approval',1);
			release_freezing();
		}
	}
}

function fnc_comments(id,value)
{
	var page_link='requires/sample_approval_controller.php?action=comments_popup&comments_data='+value;
	var title='Comments Info';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=440px,height=200px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var comments_data=this.contentDoc.getElementById("txt_comments").value;

		$('#'+id).val(comments_data);
	}
}


function openmypage_po_color(page_link,title,type)
{
	if (type=="order")
	{
		page_link=page_link+'&txt_job_no='+document.getElementById('txt_job_no').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
	}
	emailwindow.onclose=function()
	{
		if (type=="order")
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var id=this.contentDoc.getElementById("po_number_id") //Access form field with id="emailfield"
			var po=this.contentDoc.getElementById("po_number") //Access form field with id="emailfield"
			var color_id=this.contentDoc.getElementById("color_id") //Access form field with id="emailfield"
			var color_name=this.contentDoc.getElementById("po_color") //Access form field with id="emailfield"
			if (id.value!="")
			{
				freeze_window(5);
				document.getElementById('txt_po_no_id').value=id.value;
				document.getElementById('txt_po_no').value=po.value;
				document.getElementById('txt_color_id').value=color_id.value;
				document.getElementById('txt_color').value=color_name.value;
				release_freezing();
			}
		}
	}
}
// Master Form End --------------------------------------------------------------------------------------------
function show_hide_content(row, id) 
{
	$('#row_'+row).toggle('fast', function() {
		 //get_php_form_data( id, 'set_php_form_data', '../woven_order/requires/sample_approval_controller' );
	});
}


function copy_value(value,field_id,i)
{
	
  	var copy_val=document.getElementById('copy_val').checked;
 	// alert(copy_val)
  	var rowCount = $('#tbl_sample_info tr').length-1;
  	var today = new Date();
	var dd = String(today.getDate()).padStart(2, '0');
	var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
	var yyyy = today.getFullYear();
	var current_date = mm + '-' + dd + '-' + yyyy;
	  if(copy_val==true)
	  {
	  for(var j=i; j<=rowCount; j++)
		{
			  if(field_id=='txtsendtofatorydate_')
			  {
				document.getElementById(field_id+j).value=value;
			  }
			  if(field_id=='txtsubmissiontobuyerdate_')
			  {
				document.getElementById(field_id+j).value=value;
			  }
			  
			  if(field_id=='cboapprovalstatus_' &&  document.getElementById(field_id+j).disabled ==false )
			  {
				 
				document.getElementById(field_id+j).value=value;
			  }
			  
			  if(field_id=='txtapprovalrejectdate_')
			  {
				document.getElementById(field_id+j).value=value;
			  }
			   if(field_id=='txtsamplecomments_')
			  {
				document.getElementById(field_id+j).value=value;
			  }
		  	if(field_id=='txtapprovalrejectdate_' || field_id=='txttargetapprovaldate_'){
				var action_date=document.getElementById('txtapprovalrejectdate_'+j).value;
				var datediff = date_compare(action_date,current_date);
				if(datediff==false)
				{
					document.getElementById(field_id+j).value='';
					alert("Not Allowed Greater Than Current Date");
					return;
				}
				else{
					document.getElementById(field_id+j).value=value;
				}							
			}
		}
	  }
	  else{
	  	if(field_id=='txtapprovalrejectdate_' || field_id=='txttargetapprovaldate_'){
				var action_date=document.getElementById('txtapprovalrejectdate_'+i).value;
				var datediff = date_compare(action_date,current_date);
				if(datediff==false)
				{
					document.getElementById(field_id+i).value='';
					alert("Not Allowed Greater Than Current Date");
					return;
				}
				else{
					document.getElementById(field_id+i).value=value;
				}							
			}
	  }
  }

function fnc_sample_approval( operation )
{
	if(operation==2)
	{
		alert("Delete Restricted");
		release_freezing();
		return;
	}
	var row_num=$('#tbl_sample_info tr').length-1;
	var data_all="";
	for (var i=1; i<=row_num; i++)
	{
		var cboapprovalstatus=$('#cboapprovalstatus_'+i).val();
		
		if(cboapprovalstatus!=0)
		{
			if (form_validation('txtapprovalrejectdate_'+i,'Action Date')==false)
			{
				return;
			}
		}
		
		if(cboapprovalstatus==2) // Rejected
		{
			if (form_validation('txtapprovalrejectdate_'+i+'*txtsamplecomments_'+i,'Action Date*Merchant Comments')==false)
			{
				return;
			}
		}
		else if(cboapprovalstatus==1) //Submitted
		{
			if (form_validation('txtsubmissiontobuyerdate_'+i,'Submission to Buyer')==false)
			{
				return;
			}
		}
		else if(cboapprovalstatus==3) //Approve
		{
			if (form_validation('txtapprovalrejectdate_'+i,'Action Date')==false)
			{
				return;
			}
		}
		else if(cboapprovalstatus==5) //Re-Submitted
		{
			if (form_validation('txtsubmissiontobuyerdate_'+i+'*txtsamplecomments_'+i,'Submission to Buyer*Merchant Comments')==false)
			{
				return;
			}
		}
		else if(cboapprovalstatus==4) //Cancel
		{
			if (form_validation('txtapprovalrejectdate_'+i+'*txtsamplecomments_'+i,'Action Date*Merchant Comments')==false)
			{
				return;
			}
		}
	//alert(document.getElementById('cbopono_'+i).value)
	/*if (form_validation('txttargetapprovaldate_'+i+'*txtsendtofatorydate_'+i+'*txtsubmissiontobuyerdate_'+i+'*cboapprovalstatus_'+i+'*txtapprovalrejectdate_'+i,'Target Approval Date*Sent To Sample Section*Submission to Buyer *Action*Action Date')==false)
	{
		return;
	}*/
		
		//eval(get_submitted_variables('garments_nature*txt_job_no*resubmit_id*cbopono_'+i+'*colorsizetableid_'+i+'*cbosampletype_'+i+'*txttargetapprovaldate_'+i+'*txtsendtofatorydate_'+i+'*txtsubmissiontobuyerdate_'+i+'*cboapprovalstatus_'+i+'*txtapprovalrejectdate_'+i+'*txtsamplecomments_'+i+'*cbostatus_'+i+'*updateid_'+i));
		data_all=data_all+get_submitted_data_string('garments_nature*txt_job_no*resubmit_id*cbopono_'+i+'*colorsizetableid_'+i+'*cbosampletype_'+i+'*txttargetapprovaldate_'+i+'*txtsendtofatorydate_'+i+'*txtsubmissiontobuyerdate_'+i+'*cboapprovalstatus_'+i+'*txtapprovalrejectdate_'+i+'*txtsamplecomments_'+i+'*cbostatus_'+i+'*updateid_'+i,"../../");
	}
	var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+data_all;
	freeze_window(operation);
	http.open("POST","requires/sample_approval_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_sample_approval_reponse;
}
	 
function fnc_sample_approval_reponse()
{
	
	if(http.readyState == 4) 
	{
		//alert(http.responseText);
		var reponse=trim(http.responseText).split('**');
		 
		 show_msg(trim(reponse[0]));
		
		// show_list_view(document.getElementById('txt_job_no').value,'show_sample_approval_list','accordion_container','../woven_order/requires/sample_approval_controller','');
		 	show_list_view(document.getElementById('txt_job_no').value+'_'+document.getElementById('cbo_sample_type').value,'show_sample_approval_list_form','po_list_view','requires/sample_approval_controller','');
			show_list_view(document.getElementById('txt_job_no').value+'_'+document.getElementById('cbo_buyer_name').value,'show_sample_approved_list','accordion_container','requires/sample_approval_controller','');
			//show_list_view(document.getElementById('txt_job_no').value+'_'+buyer_name,'show_sample_approved_list','accordion_container','../woven_order/requires/sample_approval_controller','');


		//reset_form('sizecolormaster_1','','');
		document.getElementById('resubmit_id').value=""
		set_button_status(1, permission, 'fnc_sample_approval',1);
		release_freezing();
		
	}
}
function check_date_status(str)
{
	/*if(str==1)
	{
		if(document.getElementById('txt_send_to_fatory_date1').value>document.getElementById('txt_request_date1').value)
		{
			$("#messagebox_sample").removeClass().addClass('messagebox').text('Validating....').fadeIn(1000);
			
			$("#messagebox_sample").fadeTo(200,0.1,function() //start fading the messagebox
			{ 
			  $('#txt_send_to_fatory_date1').focus();
			  //document.getElementById('txt_send_to_fatory_date1').value="";
			  $(this).html('Selected date must be equal or smaller than Target Approval Date.').addClass('messageboxerror').fadeTo(900,1);
			});
			
		}
	}
	else
	{
		if(document.getElementById('txt_submission_to_buyer_date1').value>document.getElementById('txt_request_date1').value)
		{
			$("#messagebox_sample").removeClass().addClass('messagebox').text('Validating....').fadeIn(1000);
			
			$("#messagebox_sample").fadeTo(200,0.1,function() //start fading the messagebox
			{ 
			  $('#txt_submission_to_buyer_date1').focus();
			 // document.getElementById('txt_submission_to_buyer_date1').value="";
			  $(this).html('Selected date must be equal or smaller than Target Approval Date.').addClass('messageboxerror').fadeTo(900,1);
			});
			//txt_send_to_fatory_date1
		}
		if(document.getElementById('txt_submission_to_buyer_date1').value<document.getElementById('txt_send_to_fatory_date1').value)
		{
			$("#messagebox_sample").removeClass().addClass('messagebox').text('Validating....').fadeIn(1000);
			
			$("#messagebox_sample").fadeTo(200,0.1,function() //start fading the messagebox
			{ 
			  $('#txt_submission_to_buyer_date1').focus();
			 // document.getElementById('txt_submission_to_buyer_date1').value="";
			  $(this).html('Selected date must be equal or Greater than Factory Date.').addClass('messageboxerror').fadeTo(900,1);
			});
			//txt_send_to_fatory_date1
		}
	}*/
}

function load_form (sample_type)
{
	
	//var cbo_sample_type=document.getElementById('cbo_sample_type').value;
	//if(cbo_sample_type==0) return;
	
	var txt_job_no=document.getElementById('txt_job_no').value;
	if(txt_job_no=='')
	{
		document.getElementById('cbo_sample_type').value=0;
		alert("Select Job No!");
		return;
	}
	else
	{
		//alert(sample_type)
		show_list_view(document.getElementById('txt_job_no').value+'_'+sample_type,'show_sample_approval_list_form','po_list_view','../woven_order/requires/sample_approval_controller','');
	}
	document.getElementById('cbo_sample_type').value=sample_type;
	
	if(document.getElementById('updateid_1').value !="")
	{
		set_button_status(1, permission, 'fnc_sample_approval',1);
	}
	else
	{
		set_button_status(0, permission, 'fnc_sample_approval',1);
	}
}

function resubmit(i)
{
	var row_num=$('#tbl_sample_info tr').length-1;
	var cbopono=document.getElementById('cbopono_'+i).value;
	var cbocolor=document.getElementById('cbocolor_'+i).value;
	var colorsizetableid=document.getElementById('colorsizetableid_'+i).value;
	var cbosampletype=document.getElementById('cbosampletype_'+i).value;
	var cboapprovalstatus=document.getElementById('cboapprovalstatus_'+i).value;

	//var txtapprovalrejectdate=document.getElementById('txtapprovalrejectdate_'+i).value;
	//var txtsamplecomments=document.getElementById('txtsamplecomments_'+i).value;
	//var cbostatus=document.getElementById('cbostatus_'+i).value;
	//var updateid=document.getElementById('updateid_'+i).value;
	for (var j=1; j<=row_num; j++)
	{
		if(j==i)
		{
			continue;
		}
		else
		{
			var cboponoj=document.getElementById('cbopono_'+j).value;
			var cbocolorj=document.getElementById('cbocolor_'+j).value;
			var colorsizetableidj=document.getElementById('colorsizetableid_'+j).value;
			var cbosampletypej=document.getElementById('cbosampletype_'+j).value;
			var cboapprovalstatusj=document.getElementById('cboapprovalstatus_'+j).value;
		    if(cbopono==cboponoj && cbocolor== cbocolorj && colorsizetableid==colorsizetableidj && cbosampletype==cbosampletypej && cboapprovalstatusj!=2 )
			{
			alert("It is already resubmitted!");
			return;
			}
		}
	}
	
	var resubmit_id_old=document.getElementById('resubmit_id').value;
	var updateid=document.getElementById('updateid_'+i).value;
	//var resubmit_id_new=""
	if(updateid !="")
	{
	if(resubmit_id_old=="")
	{
		 resubmit_id_old=updateid+",";
	}
	else
	{
		 resubmit_id_old=resubmit_id_old+updateid+",";
	}
	}
	document.getElementById('resubmit_id').value=resubmit_id_old;
	
	
	document.getElementById('txttargetapprovaldate_'+i).value='';
	document.getElementById('txtsendtofatorydate_'+i).value='';
	document.getElementById('txtsubmissiontobuyerdate_'+i).value='';
	document.getElementById('cboapprovalstatus_'+i).value=0;
	document.getElementById('txtapprovalrejectdate_'+i).value='';
	document.getElementById('txtsamplecomments_'+i).value='';
	document.getElementById('cbostatus_'+i).value='';
	document.getElementById('updateid_'+i).value='';
	
	$('#txttargetapprovaldate_'+i).removeAttr("disabled");
	$('#txtsendtofatorydate_'+i).removeAttr("disabled");
	$('#txtsubmissiontobuyerdate_'+i).removeAttr("disabled");
	$('#cboapprovalstatus_'+i).removeAttr("disabled");
	$('#txtapprovalrejectdate_'+i).removeAttr("disabled");
	$('#txtsamplecomments_'+i).removeAttr("disabled");
	$('#cbostatus_'+i).removeAttr("disabled");
	
}
</script>
 
</head>
 
<body onLoad="set_hotkey()">

<div style="width:100%;" align="center">
												
     <? echo load_freeze_divs ("../../",$permission);  ?>
    
     <table width="90%" cellpadding="0" cellspacing="2" align="center">
     	<tr>
        	<td width="70%" align="center" valign="top">  <!--   Form Left Container -->
            	<fieldset style="width:950px;">
                <legend>Sample Approval</legend>
                
            		<table  width="900" cellspacing="2" cellpadding="0" border="0">
                       <tr>
                            <td  width="130" height="" align="right"> Job No </td>              <!-- 11-00030  -->
                                <td  width="170" >
                                <input style="width:160px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/sample_approval_controller.php?action=order_popup','Job/Order Selection Form')" class="text_boxes" autocomplete="off" placeholder="Search Job" name="txt_job_no" id="txt_job_no" readonly />
                                 
                                </td>
                                <td  width="130" align="right">Company Name </td>
                                <td width="170">
                               <?
							   		echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "",1 );
							   ?> 
                                 </td>
                              <td width="130" align="right">Location Name</td>
                              <td id="location">
                              <? 
							  
							  	echo create_drop_down( "cbo_location_name", 172, "select id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select --", $selected, "",1 );		
								
								?>	
                               
                              </td>
                        </tr>
                        <tr>
                        	<td align="right">Buyer Name</td>
                              <td id="buyer_td">
                              <? 
                                echo create_drop_down( "cbo_buyer_name", 172, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ,1);   
                                ?>	  
                              </td>
                            <td align="right">Style Ref.</td>
                        	<td>
                            	<input class="text_boxes" type="text" style="width:160px" disabled placeholder="Double Click for Quotation" name="txt_style_ref" id="txt_style_ref"/>	
                            </td>
                            <td align="right">
                               Style Description
                            </td>
                            <td>	
                                <input class="text_boxes" type="text" style="width:160px;" disabled name="txt_style_description" id="txt_style_description"/>
                            </td>
                        </tr>
                        <tr>
                            <td height="" align="right">Pord. Dept.</td>   
                                <td >
                                <? 
							   		echo create_drop_down( "cbo_product_department", 172, $product_dept, "",1, "-- Select prod. Dept--", $selected, "" ,1);
							    ?>

                                </td>
                               
                               
                              <td align="right">Currency</td>
                              <td>
                              <? 
							  	echo create_drop_down( "cbo_currercy", 172, $currency, "", 1, "-- Select Currency--", 2, "",1 );
								?>	  
                              </td>
                              <td align="right">Agent </td>
                                <td id="agent_td">
                                <?	 	echo create_drop_down( "cbo_agent", 172, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3))  order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "",1 );  
	 	
									 ?>
                                </td>
                        </tr>
                        <tr>
                            
                              <td  align="right">Region</td>
                              <td>
                              <? 
							  	echo create_drop_down( "cbo_region", 172, $region, "",1, "-- Select Region --", $selected, "",1 );
								?>	  
                              </td>
                               <td align="right">Team Leader</td>   
    						<td>
                             <?  
							  	echo create_drop_down( "cbo_team_leader", 172, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "",1 );
								?>		
                            </td>
							<td align="right">Dealing Merchant</td>   
    						<td> 
                            <? 
							  	echo create_drop_down( "cbo_dealing_merchant", 172, "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "",1 );
								?>	
                           </td>
                        </tr>
						<tr>
							<td align="right"></td>
							<td><input type="button" class="image_uploader" style="width:172px" value="ADD FILE" onClick="file_uploader ( '../../', document.getElementById('txt_job_no').value,'', 'sample_app', 2 ,1)"></td>
							<td></td>   
						    <td></td>
						  	<td></td>   
						  	<td></td>
					  </tr>
                        <tr>
                        	<td align="center" height="10" colspan="6"></td>
                        </tr>
                       
                        <tr>
                        	<td colspan="6">
                            <h3 align="left" class="accordion_h"> + Sample Type
                            <span id="dropdown_span">
									<?
                                    echo create_drop_down( "cbo_sample_type", 140, "select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name","id,sample_name", '1', "--Select--", '', "load_form(this.value)",1,'' );
                                    ?> 
                            </span>   <b>Copy</b> : <input type="checkbox" id="copy_val" name="copy_val"/> <input type="hidden" id="resubmit_id" name="resubmit_id"/>
                            <span id="msg_span" style="padding-left:100px; color:#F00"></span> 
                            </h3>
                            
                                </td>
                        </tr>
                        
                        <tr>
                        	<td align="center" colspan="6" valign="top" id="po_list_view">
                            	
                            </td>
                        </tr>
                        <tr>
                            <td align="center" colspan="10" valign="middle" class="button_container">
                              <? echo load_submit_buttons( $permission, "fnc_sample_approval", 0,0 ,"reset_form('sampleapproval_1','','')",1) ; ?>
                            </td>
                        </tr>
                         <tr>
                        	<td align="left" height="20" colspan="6" id="accordion_container"></td>
                        </tr>
                       
                    </table>
                 
              </fieldset>
           </td>
         </tr>
         
	</table>
	</div>
</body>
 <script>
	//set_multiselect('cbo_po_no*cbo_color','0*0','0','','__load_drop_down_color__../woven_order/requires/sample_approval_controller*0');
	//set_multiselect( fld_id, max_selection, is_update, update_values, on_close_fnc_param )
</script>          
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>