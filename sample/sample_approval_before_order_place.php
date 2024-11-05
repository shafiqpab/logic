<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Sample Approval for before order place
					
Functionality	:	
				

JS Functions	:

Created by		:	Rehan Uddin 
Creation date 	: 	05-03-2017
Updated by 		: 		
Update date		: 		   

QC Performed BY	:		

QC Date			:	

Comments		:

*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sample Approval Before Order Place", "../", 1, 1,$unicode,'','');
?>	
 
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  

var permission='<? echo $permission; ?>';
// Master Form--------------------------------------------------------------------------------------------
function openmypage(page_link,title)
{
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=450px,center=1,resize=1,scrolling=0','')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var theemail=this.contentDoc.getElementById("selected_job") //Access form field with id="emailfield"
		if (theemail.value!="")
		{
			reset_form('','accordion_container*po_list_view','');
			freeze_window(5);
 			get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/sample_approval_before_order_place_controller" );
			show_list_view(document.getElementById('hidden_requisition_id').value,'show_sample_approved_list','accordion_container','requires/sample_approval_before_order_place_controller','');
			release_freezing();
		}
	}
}

function fnc_comments(id,value)
{
	var page_link='requires/sample_approval_before_order_place_controller.php?action=comments_popup&comments_data='+value;
	var title='Comments Info';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=440px,height=200px,center=1,resize=1,scrolling=0','');
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
			page_link=page_link+'&txt_requisition_no='+document.getElementById('txt_requisition_no').value;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','')
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
		 //get_php_form_data( id, 'set_php_form_data', 'requires/sample_approval_before_order_place_controller' );
	});
}


function copy_value(value,field_id,i)
{

		  if(field_id=='txttargetapprovaldate_')
		  {
			document.getElementById(field_id+i).value=value;

		  }
		  if(field_id=='txtsendtofatorydate_')
		  {
			document.getElementById(field_id+i).value=value;
		  }
		  if(field_id=='txtsubmissiontobuyerdate_')
		  {
			document.getElementById(field_id+i).value=value;
		  }
		  
		  if(field_id=='cboapprovalstatus_' &&  document.getElementById(field_id+i).disabled ==false )
		  {
			 
			document.getElementById(field_id+i).value=value;
		  }
		  
		  if(field_id=='txtapprovalrejectdate_')
		  {
			document.getElementById(field_id+i).value=value;
		  }
		   if(field_id=='txtsamplecomments_')
		  {
			document.getElementById(field_id+i).value=value;
		  }
		
  }

/*function copy_value(value,field_id,i)
{
	
  //var copy_val=document.getElementById('copy_val').checked;
  var copy_val=true;
   
  var rowCount = $('#tbl_sample_info tr').length-1;
	  if(copy_val==true)
	  {
	  for(var j=i; j<=rowCount; j++)
		{
		  if(field_id=='txttargetapprovaldate_')
		  {
			document.getElementById(field_id+j).value=value;

		  }
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
		}
	  }
  }
*/




function fnc_sample_approval( operation )
{
	var row_num=$('#tbl_sample_info tr').length-1;
		var data_all="";
		for (var i=1; i<=row_num; i++)
		{
			//alert(document.getElementById('cbogarmentsitem_'+i).value)
			/*if (form_validation('txttargetapprovaldate_'+i+'*txtsendtofatorydate_'+i+'*txtsubmissiontobuyerdate_'+i+'*cboapprovalstatus_'+i+'*txtapprovalrejectdate_'+i,'Target Approval Date*Sent To Sample Section*Submission to Buyer *Action*Action Date')==false)
			{
				return;
			}*/
			
			//eval(get_submitted_variables('garments_nature*txt_requisition_no*resubmit_id*cbogarmentsitem_'+i+'*colorsizetableid_'+i+'*cbosampletype_'+i+'*txttargetapprovaldate_'+i+'*txtsendtofatorydate_'+i+'*txtsubmissiontobuyerdate_'+i+'*cboapprovalstatus_'+i+'*txtapprovalrejectdate_'+i+'*txtsamplecomments_'+i+'*cbostatus_'+i+'*updateid_'+i));
			data_all=data_all+get_submitted_data_string('txt_requisition_no*hidden_requisition_id*resubmit_id*sampledtlsid_'+i+'*cbogarmentsitem_'+i+'*cbocolor_'+i+'*cbosampletype_'+i+'*txttargetapprovaldate_'+i+'*txtsendtofatorydate_'+i+'*txtsubmissiontobuyerdate_'+i+'*cboapprovalstatus_'+i+'*txtapprovalrejectdate_'+i+'*txtsamplecomments_'+i+'*cbostatus_'+i+'*updateid_'+i,"../");
		}
		var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+data_all;
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/sample_approval_before_order_place_controller.php",true);
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
		
		// show_list_view(document.getElementById('txt_requisition_no').value,'show_sample_approval_list','accordion_container','requires/sample_approval_before_order_place_controller','');
		 	show_list_view(document.getElementById('hidden_requisition_id').value+'_'+document.getElementById('cbo_sample_type').value,'show_sample_approval_list_form','po_list_view','requires/sample_approval_before_order_place_controller','');
			show_list_view(document.getElementById('hidden_requisition_id').value,'show_sample_approved_list','accordion_container','requires/sample_approval_before_order_place_controller','');


		//reset_form('sizecolormaster_1','','');
		document.getElementById('resubmit_id').value="";
		set_button_status(1, permission, 'fnc_sample_approval',1);
		release_freezing();
		
	}
}


function load_form (sample_type)
{
	
	//var cbo_sample_type=document.getElementById('cbo_sample_type').value;
	//if(cbo_sample_type==0) return;
	
	var req_id=document.getElementById('hidden_requisition_id').value;
	if(req_id=='')
	{
		document.getElementById('cbo_sample_type').value=0;
		alert("Select Requisition No!");
		return;
	}
	else
	{
		// alert(document.getElementById('hidden_requisition_id').value);
		show_list_view(document.getElementById('hidden_requisition_id').value+'_'+sample_type,'show_sample_approval_list_form','po_list_view','requires/sample_approval_before_order_place_controller','');
		$('#cbo_sample_type').val(sample_type);
	}
	document.getElementById('cbo_sample_type').value=sample_type;
	
	if(document.getElementById('updateIdForBtn_1').value !="")
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
	var cbopono=document.getElementById('cbogarmentsitem_'+i).value;
	var cbocolor=document.getElementById('cbocolor_'+i).value;
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
			var cboponoj=document.getElementById('cbogarmentsitem_'+j).value;
			var cbocolorj=document.getElementById('cbocolor_'+j).value;
			//var colorsizetableidj=document.getElementById('colorsizetableid_'+j).value;
			var cbosampletypej=document.getElementById('cbosampletype_'+j).value;
			var cboapprovalstatusj=document.getElementById('cboapprovalstatus_'+j).value;
		    if(cbopono==cboponoj && cbocolor== cbocolorj  && cbosampletype==cbosampletypej && cboapprovalstatusj!=2 )
			{
			alert("It is already resubmitted!");
			return;
			}
		}
	}
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
	
	
	
	
}
function reset_forms()
{
	var rowCount = $('#tbl_sample_info tr').length-1;
	for( i=1; i<=rowCount;i++)
	{
		$("#txttargetapprovaldate_"+i).val('');
		$("#txtsendtofatorydate_"+i).val('');
		$("#txtsubmissiontobuyerdate_"+i).val('');
		$("#cboapprovalstatus_"+i).val('');
		$("#txtapprovalrejectdate_"+i).val('');
		$("#txtsamplecomments_"+i).val('');
	}
}
</script>
 
</head>
 
<body onLoad="set_hotkey()">

<div style="width:100%;" align="center">
												
     <? echo load_freeze_divs ("../",$permission);  ?>
    
     <table width="90%" cellpadding="0" cellspacing="2" align="center">
     	<tr>
        	<td width="70%" align="center" valign="top">  <!--   Form Left Container -->
            	<fieldset style="width:950px;">
                <legend>Sample Approval</legend>
                
            		<table  width="900" cellspacing="2" cellpadding="0" border="0">
                       <tr>
                            <td  width="130" class="must_entry_caption" align="right"> Requisition No </td>              <!-- 11-00030  -->
                                <td  width="170" >
                                <input style="width:160px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/sample_approval_before_order_place_controller.php?action=requisition_popup','Requisition Selection Form')" class="text_boxes" autocomplete="off" placeholder="Search Requisition" name="txt_requisition_no" id="txt_requisition_no" readonly />
                                <input type="hidden" name="hidden_requisition_id" id="hidden_requisition_id">
                                 <input type="hidden" name="update_id" id="update_id">
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
                               Season
                            </td>
                            <td>
                            <? 
							   		echo create_drop_down( "cbo_season", 172, "select id,season_name from lib_buyer_season where  status_active =1 and  is_deleted=0 ", "id,season_name",1, "-- select season--", $selected, "" ,1);
							    ?>	
                                 
                            </td>
                        </tr>
                        <tr>
                            <td height="" align="right">Pord. Dept.</td>   
                                <td >
                                <? 
							   		echo create_drop_down( "cbo_product_department", 172, $product_dept, "",1, "-- Select prod. Dept--", $selected, "" ,1);
							    ?>

                                </td>
                               
                               
                              
                              <td align="right">Agent </td>
                                <td id="agent_td">
                                <?	 	echo create_drop_down( "cbo_agent", 172, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3))  order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "",1 );  
	 	
									 ?>
                                </td>
                                <td align="right">Sample Team</td>   
    						<td>
                             <?    
							  	echo create_drop_down( "cbo_sample_team", 172,"select id,team_name from lib_sample_production_team where is_deleted=0 and status_active=1 order by team_name","id,team_name", 1, "Select Team", $selected,"",1 );
								?>		
                            </td>
                        </tr>
                        <tr>
                            
                            
							<td align="right">Dealing Merchant</td>   
    						<td> 
                            <? 
							  	echo create_drop_down( "cbo_dealing_merchant", 172, "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "",1 );
								?>	
                           </td>
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
                            </span> <input type="hidden" id="resubmit_id" name="resubmit_id"/>
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
                              <? echo load_submit_buttons( $permission, "fnc_sample_approval", 0,0 ,"reset_forms()",1) ; ?>
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
</script>          
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>