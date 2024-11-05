<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create WS GSD Entry				
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	18-01-2016
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
echo load_html_head_contents("GSD Entry", "../../", 1,1, $unicode,1,'');

?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
var permission='<? echo $permission; ?>';

function openmypage_style()
{ 
    if( form_validation('cbo_company_id','Company Name')==false)
	{
		return;
	}
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/ws_gsd_controller.php?action=style_ref_popup&data='+document.getElementById("cbo_company_id").value+'&buyer_id='+document.getElementById("cbo_buyer").value,'Style Ref. Popup', 'width=930px,height=350px,center=1,resize=1,scrolling=0','../')
	
	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("style_ref_id");
		var response=theemail.value.split('_');
		if (theemail.value!="")
		{
			freeze_window(5);
			document.getElementById("wo_po_id").value=response[0];
			document.getElementById("job_no").value=response[1];
			document.getElementById("txt_job_no").value=response[1];
			document.getElementById("cbo_buyer").value=response[2];
			document.getElementById("txt_style_ref").value=response[3];
			document.getElementById("cbo_gmt_item").value=response[4];
			document.getElementById("txt_order_no").value=response[5];
			release_freezing();
		}
	}
}

function openmypage_sysnum()
{ 
	//alert(2);
    if( form_validation('cbo_company_id','Company Name')==false)
	{
		return;
	}
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/ws_gsd_controller.php?action=systemid_popup&data='+document.getElementById("cbo_company_id").value,'System ID Popup', 'width=930px,height=350px,center=1,resize=1,scrolling=0','../')
	
	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("system_id");
		var response=theemail.value.split('_');
		if (theemail.value!="")
		{
			freeze_window(5);
			document.getElementById("update_id").value=response[0];
			document.getElementById("wo_po_id").value=response[1];
			document.getElementById("txt_job_no").value=response[2];
			document.getElementById("cbo_buyer").value=response[3];
			document.getElementById("txt_style_ref").value=response[4];
			document.getElementById("cbo_gmt_item").value=response[5];
			document.getElementById("txt_order_no").value=response[6];
			
			document.getElementById("txt_working_hour").value=response[7];
			document.getElementById("txt_operation_count").value=response[8];
			document.getElementById("txt_mcOperationCount").value=response[9];
			document.getElementById("txt_tot_smv").value=response[10];
			document.getElementById("txt_mc_smv").value=response[11];
			document.getElementById("txt_operatorPitch_time").value=response[12];
			document.getElementById("txt_pitch_time").value=response[13];

			show_list_view(response[0],'load_php_dtls_form','gsd_entry_info_list','requires/ws_gsd_controller','');

			/*if(document.getElementById('update_id').value!=0 && document.getElementById('update_id').value!="")
			{
				//show_list_view(document.getElementById('update_id').value,'load_php_dtls_form','new_tbl','requires/ws_gsd_controller','');
				set_button_status(1, permission, 'fnc_gsd_entry',1);
				counter=  $("#gsd_tbl tbody tr").length;
				 
				var operator_smv_tot=0;
				var helper_smv_tot=0;
				var smv_tot=0;
				for(var i=1;i<=counter; i++)
				{
					try{
						operator_smv_tot = operator_smv_tot*1+document.getElementById('txt_operator_'+i).value*1;
						helper_smv_tot = helper_smv_tot*1+document.getElementById('txt_helper_'+i).value*1;
						smv_tot = smv_tot*1+document.getElementById('txt_total_'+i).value*1;
					}
					catch(err){}
				}
				document.getElementById('txt_operator_tot').value=operator_smv_tot;
				document.getElementById('txt_helper_tot').value=helper_smv_tot;
				document.getElementById('txt_total_tot').value=smv_tot; 
				$('#deleted_id').val( '' );
			}
			else
			{
				show_list_view(1+'_'+response[5],'load_php_dtls_item','gsd_entry_info_list','requires/ws_gsd_controller','');
				var row_num=$('#tbl_body_item tbody tr').length;
				if(row_num>0) { $("#txtAttachment_1").focus(); }	
			}*/
			release_freezing();
		}
	}
} 

function openmypage_operation()
{ 
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/ws_gsd_controller.php?action=operation_popup','Operation Popup', 'width=850px,height=350px,center=1,resize=1,scrolling=0','')
	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("operation_id");
		var response=theemail.value.split('_');
		if (theemail.value!="")
		{
			freeze_window(5);
			document.getElementById("txt_operation").value=response[1];
			document.getElementById("cbo_resource").value=response[2];
			document.getElementById("txt_operator").value=response[3];
			document.getElementById("txt_helper").value=response[4];
			document.getElementById("hidden_operation").value=response[0];
			release_freezing();
		}
	}
}

function openmypage_attachment()
{ 
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/ws_gsd_controller.php?action=attachment_popup','Attachment Popup', 'width=400px,height=350px,center=1,resize=1,scrolling=0','../')
	
	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("attachment_id");
		var response=theemail.value.split('_');
		if (theemail.value!="")
		{
			freeze_window(5);
			document.getElementById("txt_attachment_id").value=response[0];
			document.getElementById("txt_attachment").value=response[1];
			//reset_form();
			//get_php_form_data( response[1], "load_php_data_to_form_attachment", "requires/ws_gsd_controller" );
			release_freezing();
		}
	}
}

function openmypage_attachment_multuple(id,show_id)
{ 
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/ws_gsd_controller.php?action=attachment_popup','Attachment Popup', 'width=400px,height=350px,center=1,resize=1,scrolling=0','../')
	
	emailwindow.onclose=function()
	{
		//alert(id)
		var theemail=this.contentDoc.getElementById("attachment_id");
		var response=theemail.value.split('_');
		if (theemail.value!="")
		{
			freeze_window(5);
			document.getElementById(id).value=response[0];
			document.getElementById(show_id).value=response[1];
			//reset_form();
			//get_php_form_data( response[1], "load_php_data_to_form_attachment", "requires/ws_gsd_controller" );
			release_freezing();
		}
	}
}

function fnc_gsd_entry( operation )
{
	
	
	if( form_validation('cbo_company_id*txt_style_ref*txt_working_hour','Company Name*Style Ref.*Working Hour')==false)
	{
		return;
	}
	if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][149]);?>'){
				if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][149]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][149]);?>')==false)
				{
					
					return;
				}
			}
	
	var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_id*txt_style_ref*cbo_buyer*txt_job_no*txt_order_no*cbo_gmt_item*wo_po_id*txt_working_hour*cbo_action*txt_seqNo*cbo_body_part*hidden_operation*cbo_resource*txt_attachment_id*txt_operator*txt_helper*txt_efficiency*txt_dtls_id*txt_operation_count*txt_mcOperationCount*txt_tot_smv*txt_mc_smv*txt_pitch_time*txt_operatorPitch_time*update_id',"../../");
	
	freeze_window(operation);
	http.open("POST","requires/ws_gsd_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_gsd_entry_response;
}

function fnc_gsd_entry_response()
{
	if(http.readyState == 4) 
	{
		var response=trim(http.responseText).split('**');
		show_msg(response[0]);
		
		if(response[0]==0 || response[0]==1 )
		{
			document.getElementById('update_id').value = response[1];
			show_list_view(response[1],'load_php_dtls_form','gsd_entry_info_list','requires/ws_gsd_controller','');
			set_button_status(1, permission, 'fnc_gsd_entry',1);
		}
		release_freezing();
	}
}

function duplication_check(row_id)
{
	var row_num=$('#gsd_tbl tbody tr').length;
	var txt_seq=$('#txt_seq_'+row_id).val();
	
	for(var j=1; j<=row_num; j++)
	{
		if(j==row_id)
		{
			continue;
		}
		else
		{
			var txt_seq_check=$('#txt_seq_'+j).val();

			if(txt_seq==txt_seq_check)
			{
				alert("Duplicate Seq No. "+txt_seq);
				$('#txt_seq_'+row_id).val('');
				return;
			}
		}
	}
}

function print_gsd_report()
{
	print_report( $('#cbo_company_id').val()+'*'+$('#txt_job_no').val()+'*'+$('#update_id').val(), "print_gsd_report", "requires/ws_gsd_controller") 
	//return;
	show_msg("3");
}

var ddd={ dec_type:2, comma:0, currency:1}

	function load_body_part_value()
	{
		var bodypart=$("#cbo_body_part").val();
		show_list_view(0+'_'+bodypart,'load_php_dtls_item','gsd_entry_info_list','requires/ws_gsd_controller','');
		var row_num=$('#tbl_body_item tbody tr').length;
		if(row_num>0) { $("#txtAttachment_1").focus(); }
	}


	
	
</script>



</head>
<body onLoad="set_hotkey()">
 <div align="center" style="width:100%;">
   <? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="gsdentry_1" id="gsdentry_1"  autocomplete="off"  >
        <div  style="width:900px;">
        <fieldset style="width:880px;">
        <legend>GSD Entry Info </legend>
            <table cellpadding="0" cellspacing="2" width="100%">
            	 <tr>
                     <td colspan="3" align="right"><strong>System ID</strong></td>
                     <td colspan="3" align="left"><input type="text" id="update_id" class="text_boxes_numeric" style="width:140px;" placeholder="Browse" onDblClick="openmypage_sysnum();" readonly /></td>
                 </tr>
                 <tr>
                    <td width="120" class="must_entry_caption">Company</td>
                    <td width="150">
                    <input type="hidden" id="update_id" style="width:140px;" />
                    <input type="hidden" id="wo_po_id" style="width:140px;" />
                    <input type="hidden" id="job_no" style="width:140px;" />
                    <?
                        echo create_drop_down( "cbo_company_id",150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "","","","","","",2);
                    ?>
                    </td>
                    <td width="120" class="must_entry_caption">Style Ref.</td>                                              
                    <td width="140">
                         <input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:138px" placeholder="Browse" onDblClick="openmypage_style();" readonly />
                    </td>
                    <td width="120">Buyer Name</td>                                              
                    <td width="140">
                        <?
                            echo create_drop_down( "cbo_buyer", 150, "select id,buyer_name from lib_buyer", "id,buyer_name", 1, " Display ", 0, "", 1);	
                        ?>
                    </td>
                </tr> 
                <tr>
                    <td>Job No.</td>
                    <td><input type="text" name="txt_job_no" id="txt_job_no"  class="text_boxes" style="width:138px" readonly /></td>
                    <td>Order No.</td>
                    <td><input type="text" name="txt_order_no" id="txt_order_no"  class="text_boxes" style="width:138px" readonly /></td>
                    <td>Garment Item</td>
                    <td><? echo create_drop_down( "cbo_gmt_item", 150, $garments_item, "", 1, " Display ", 0, "", 1); ?></td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Working Hour</td>
                    <td><input type="text" name="txt_working_hour" id="txt_working_hour" class="text_boxes_numeric" style="width:138px" value="10" onKeyUp="fnc_move_cursor(this.value,'txt_working_hour','cbo_action',2,23)"/></td>
                    <td>Action</td>
                    <td><? echo create_drop_down( "cbo_action",148, $row_status,"", 1, "--Select Action--", 1, "","","","","","",""); ?></td>
                    <td width="270" colspan="2">
                          <input type="button" class="image_uploader" style="width:285px" value="DISPLAY GERMENTS IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'gsd_entry', 0 ,1)">
                    </td>
                </tr>
                <tr height="5"><td colspan="6">&nbsp;</td></tr>
                <tr>
                    <td colspan="6" align="center"><input type="button" name="button" class="formbuttonplasminus" id="resetBtn" style="width:120px;" value="Copy GSD" onClick="reset_form('','','update_id*wo_po_id*txt_style_ref*cbo_buyer*txt_job_no*txt_order_no*cbo_gmt_item*txt_working_hour','','','');set_button_status(0, permission, 'fnc_gsd_entry',1);" /></td>
                </tr>
            </table>
        </fieldset>
            <br>
        <div>
        <fieldset style="width:700px;">
            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="100%">
                <thead class="form_table_header">
                	<th width="50" class="must_entry_caption">Seq.No</th>
                    <th width="115" class="must_entry_caption"> Body Part</th>
                    <th width="70" class="must_entry_caption">Operation</th>
                    <th width="100" class="must_entry_caption">Resource</th>
                    <th width="70" >Attachment</th>
                    <th width="70" class="must_entry_caption">Operator SMV</th>
                    <th width="70" class="must_entry_caption">Helper SMV</th>
                    <th>Efficiency</th>
                </thead>
                <tbody>
                    <tr class="general">
                    	<td><input type="text" name="txt_seqNo" id="txt_seqNo"  class="text_boxes_numeric" style="width:50px" />
                        	<input type="hidden" name="txt_dtls_id" id="txt_dtls_id" /></td>
                        <td>
                            <?
							$sql_bpart="select a.body_part_full_name,b.mst_id,b.entry_page_id from lib_body_part_tag_entry_page b, lib_body_part a where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0";
						$sql_result=sql_select($sql_bpart);
						foreach ($sql_result as $value) 
						{
							if($value[csf("entry_page_id")]==145)
							{
								$tag_body_part_arr[$value[csf("mst_id")]]=$value[csf("body_part_full_name")];
							}
								$all_body_part_arr[$value[csf("mst_id")]]=$value[csf("body_part_full_name")];
						}
					   $body_partArr=array();
					   if(count($tag_body_part_arr)>0)
					   {
						$body_partArr=$tag_body_part_arr;   
					   }
					   else
					   {
						 $body_partArr=$all_body_part_arr;     
					   }
					  // print_r($body_partArr);
                                //asort($body_part);
                               echo create_drop_down( "cbo_body_part",110,$body_partArr,"", 1, "--Select--", 0, "","","","","","","");
                            ?>
                            
                        </td>
                        <td>
                             <input type="text" name="txt_operation" id="txt_operation"  class="text_boxes" style="width:70px" placeholder="Browse" onDblClick="openmypage_operation();" readonly /><input type="hidden" id="hidden_operation" style="width:50px;" >
                        </td>
                        <td>
                            <?
                               asort($production_resource);
							   echo create_drop_down( "cbo_resource",100,$production_resource,"", 1, "--Select--", 0, "","","","","","","");
                            ?>
                        </td>
                        <td>
                             <input type="text" name="txt_attachment" id="txt_attachment"  class="text_boxes" style="width:70px" placeholder="Write/Browse" onDblClick="openmypage_attachment();" readonly />
                             <input type="hidden" name="txt_attachment_id" id="txt_attachment_id" />
                        </td>
                        <td>
                             <input type="text" name="txt_operator" id="txt_operator" onKeyUp="math_operation( 'txt_total', 'txt_operator*txt_helper', '+', '', ddd)"  class="text_boxes_numeric" style="width:70px" />
                        </td>
                        <td>
                             <input type="text" name="txt_helper" id="txt_helper" onKeyUp="math_operation( 'txt_total', 'txt_operator*txt_helper', '+', '', ddd)"  class="text_boxes_numeric" style="width:70px" />
                        </td>
                        <td>
                        	<input type="text" name="txt_efficiency" id="txt_efficiency"  class="text_boxes_numeric" style="width:70px" />
                        </td>
                    </tr>
                    <tr><td colspan="8">&nbsp;</td></tr>
                    <tr>
                        <td colspan="3"><strong>All Operation Count </strong> <input type="text" name="txt_operation_count" id="txt_operation_count"  class="text_boxes_numeric" style="width:100px" readonly /></td>
                        <td  colspan="3"><strong>M/C Operation Count </strong><input type="text" name="txt_mcOperationCount" id="txt_mcOperationCount"  class="text_boxes_numeric" style="width:100px" readonly /></td>
                        <td colspan="2"><strong>Total SMV </strong><input type="text" name="txt_tot_smv" id="txt_tot_smv"  class="text_boxes_numeric" style="width:70px" readonly /></td>
                    </tr>
                    <tr><td colspan="8">&nbsp;</td></tr>
                    <tr>
                    	<td colspan="3"><strong>Total MC SMV</strong>
                        	<input type="text" name="txt_mc_smv" id="txt_mc_smv"  class="text_boxes_numeric" style="width:100px" readonly /></td>
                        <td colspan="2"><strong>Pitch Time</strong>
                        	<input type="text" name="txt_pitch_time" id="txt_pitch_time"  class="text_boxes_numeric" style="width:100px" readonly /></td>
                        <td colspan="3"><strong>Operator Pitch Time</strong>
                        	<input type="text" name="txt_operatorPitch_time" id="txt_operatorPitch_time"  class="text_boxes_numeric" style="width:100px" readonly /></td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" class="button_container">
                            <? echo load_submit_buttons($permission,"fnc_gsd_entry",0,0,"reset_form('gsdentry_1','gsd_entry_info_list','','txt_working_hour,10','')",1); ?>
                            <input type="button" name="button" class="formbutton" value="Assending"  onClick="arrange_table();" />
                            <input type="button" name="button" class="formbutton" value="Print GSD"  onClick="print_gsd_report();" />
                            <!--<input type="button" name="button" class="formbutton" value="Print Line Layout" onClick="" />-->
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>
        </fieldset>
        </div>
        </div>
     </form><br>
     <div id="gsd_entry_info_list" style="width:810px;" align="center"></div>
   </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>			