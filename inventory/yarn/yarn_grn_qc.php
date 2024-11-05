<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn GRN QC Entry 
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	22/05/2022
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
$payment_yes_no=array(0=>"yes", 1=>"No");
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn GRN QC Entry", "../../", 1, 1,'','',''); 

?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	function fnc_print(company_id, productID)
		{
			var data=company_id + '*' + productID;
			var action='yarn_test_report2';
			window.open("../reports/yarn/requires/daily_yarn_stock_report_controller.php?data=" + data + '&action=' + action, true);
		}
	
	function set_receive_basis(i)
	{
		if(i==1)
		{
			disable_enable_fields( 'cbo_company_id*txt_booking_pi_no', 0, '', '' );
		}
		
		var cbo_company_id = $('#cbo_company_id').val();
		
		$('#txt_booking_pi_no').val('');	
		$('#txt_wo_pi_id').val('');
		
		var list_view_wo =trim(return_global_ajax_value( cbo_company_id, 'mrr_details', '', 'requires/yarn_grn_qc_controller'));
		$('#list_fabric_desc_container').html('');
		$('#list_fabric_desc_container').html(list_view_wo);
	}
	

	
	function openmypage_wo_pi_popup()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var update_id = $('#update_id').val();
		var cbo_supplier_name = $('#cbo_supplier').val();
		//alert(exchange_rate);
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'WO/PI Selection Form';	
			//var page_link = 'requires/yarn_grn_qc_controller.php?cbo_company_id='+cbo_company_id+'&recieve_basis='+recieve_basis+'&update_id='+update_id+'&dtls_id='+dtls_id+'&action=wo_pi_popup';
			var page_link = 'requires/yarn_grn_qc_controller.php?cbo_company_id='+cbo_company_id+'&update_id='+update_id+'&cbo_supplier_name='+cbo_supplier_name+'&action=wo_pi_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=930px,height=450px,center=1,resize=1,scrolling=0','../');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var hidden_wo_pi_id=this.contentDoc.getElementById("hidden_wo_pi_id").value;
				var hidden_wo_pi_no=this.contentDoc.getElementById("hidden_wo_pi_no").value; 
				var hidden_supplier_id=this.contentDoc.getElementById("hidden_supplier_id").value; 
				
				//alert(hidden_wo_pi_id+"**"+hidden_wo_pi_no+"**"+hidden_supplier_id); //return;

				freeze_window(5);
				set_receive_basis(0);
				
				$('#txt_booking_pi_no').val(hidden_wo_pi_no);
				$('#txt_wo_pi_id').val(hidden_wo_pi_id);
				$('#cbo_supplier').val(hidden_supplier_id);
				$('#txt_booking_pi_no').attr("disabled",true);
				$('#cbo_supplier').attr("disabled",true);
				
				show_list_view(hidden_wo_pi_id, 'show_fabric_desc_listview', 'list_fabric_desc_container', 'requires/yarn_grn_qc_controller', 'setFilterGrid("list_fabric_desc_container",-1)' ) ;
				
				//calculate(1);
				release_freezing();
			}
		}
	}
	
	
	
	function load_details_data(booking_pi_id)
	{
		var cbo_company_id=$('#cbo_company_id').val();
		show_list_view(booking_pi_id, 'show_fabric_desc_listview_update', 'list_fabric_desc_container', 'requires/yarn_grn_qc_controller', 'setFilterGrid("list_fabric_desc_container",-1)' ) ;
		//calculate(1);
		set_button_status(1, permission, 'fnc_yarn_qc',1,1);
	}
	
	
	
	
	function fnc_yarn_qc(operation)
	{
		if(operation==4)
		{
			alert("Under Construction....");
			 //var report_title=$( "div.form_caption" ).html();
			 //print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "trims_receive_entry_print", "requires/yarn_grn_qc_controller" ) 
			 return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if(operation==2)
			{
				show_msg('13');
				return;
			}
			
			if( form_validation('cbo_company_id*txt_booking_pi_no','Company*Booking No')==false )
			{
				return;
			}
		
			
			var j=0; var i=1; var dataString='';
			$("#tbl_fabric_desc_item").find('tbody tr').not(':first').each(function()
			{
				var grn=$('#grn_'+i).attr('title');
				var grndate=$('#grndate_'+i).attr('title');
				var TxtBrand=$('#tdbrand_'+i).attr('title');
				var TxtLot=$('#tdlot_'+i).attr('title');
				var count=$('#count_'+i).attr('title');
				var composition=$('#composition_'+i).attr('title');
				var comPersent=$('#comPersent_'+i).attr('title');
				var color=$('#color_'+i).attr('title');
				var yarnType=$('#yarnType_'+i).attr('title');
				var uom=$('#uom_'+i).attr('title');
				
				var grnqnty=$('#grnqnty_'+i).attr('title');
				var rejectqnty=$('#rejectqnty_'+i).val();
				var qcqnty=$('#qcqnty_'+i).val();

				var cbostatus=$('#cbostatus_'+i).val();
				var cbograde=$('#cbograde_'+i).val();
				var cboyarntest=$('#cboyarntest_'+i).val();
				var comments=$('#comments_'+i).val();
				
				var updatedtlsid=$('#updatedtlsid_'+i).val();
				var piWoId=$('#piWoId_'+i).val();
				var piWoDtlsId=$('#piWoDtlsId_'+i).val();
				var grnDtlsId=$('#grnDtlsId_'+i).val();
				
				
				if(qcqnty>=0 && cbostatus>0)	
				{
					j++;
					dataString+='&grn' + j + '=' + grn + '&grndate' + j + '=' + grndate + '&TxtLot' + j + '=' + TxtLot + '&TxtBrand' + j + '=' + TxtBrand + '&count' + j + '=' + count + '&composition' + j + '=' + composition + '&comPersent' + j + '=' + comPersent + '&yarnType' + j + '=' + yarnType + '&color' + j + '=' + color + '&uom' + j + '=' + uom + '&grnqnty' + j + '=' + grnqnty+ '&rejectqnty' + j + '=' + rejectqnty+ '&qcqnty' + j + '=' + qcqnty + '&cbostatus' + j + '=' + cbostatus + '&cbograde' + j + '=' + cbograde + '&cboyarntest' + j + '=' + cboyarntest + '&comments' + j + '=' + comments + '&updatedtlsid' + j + '=' + updatedtlsid+ '&piWoId' + j + '=' + piWoId+ '&piWoDtlsId' + j + '=' + piWoDtlsId+ '&grnDtlsId' + j + '=' + grnDtlsId;
				
				}
				
				i++;
			});
			
			// alert(dataString);return;
				
			if(j<1)
			{
				alert('No data');return;
			}
			
			//alert(dataString);return;
			
			var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_recieved_id*cbo_company_id*cbo_supplier*txt_qa_person*cbo_supplier_grade*txt_booking_pi_no*txt_wo_pi_id*update_id',"../../")+dataString;
		
			//alert(data); return;
			
			freeze_window(operation);
			http.open("POST","requires/yarn_grn_qc_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange =fnc_yarn_qc_Reply_info;
		}
	}
	
	function fnc_yarn_qc_Reply_info()
	{
		if(http.readyState == 4) 
		{
			 //alert(http.responseText);return;
			var reponse=trim(http.responseText).split('**');	
			if(reponse[0]==20 || reponse[0]==40 )
			{
				alert(reponse[1]); release_freezing();
				return;	
			}
			else
			{	
				show_msg(reponse[0]);
				if(reponse[0]==0 || reponse[0]==1 )
				{
					document.getElementById('update_id').value = reponse[1];
					document.getElementById('txt_recieved_id').value = reponse[2];
					$('#cbo_company_id').attr('disabled',true);
					$('#txt_booking_pi_no').attr('disabled',true);
					var booking_without_order=0;
					load_details_data(reponse[1]);
					set_button_status(1, permission, 'fnc_yarn_qc',1,1);	
				}
				if(reponse[0]==2)
				{
					show_msg(reponse[0]);
					reset_form('trimsreceive_1','list_container_trims*list_fabric_desc_container','','','','');
					$('#cbo_company_id').attr("disabled",false);
					$('#txt_booking_pi_no').attr("disabled",false);
					$('#cbo_receive_basis').attr("disabled",false);
					set_button_status(0, permission, 'fnc_yarn_qc',1,1);
					release_freezing();	
				}
			}
			release_freezing();	
		}
	}
	
	
	
	function yarn_receive_popup()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var page_link='requires/yarn_grn_qc_controller.php?cbo_company_id='+cbo_company_id+'&action=yarn_receive_popup_search';
			var title='Yarn QC Form';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=420px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var trims_recv_id=this.contentDoc.getElementById("hidden_recv_id").value;

				if(trims_recv_id!="")
				{
					freeze_window(5);
					reset_form('trimsreceive_1','list_container_trims*list_fabric_desc_container','','','','');
					
					//var posted_in_account=this.contentDoc.getElementById("hidden_posted_in_account").value; // is posted accounct
					//$("#is_posted_account").val(posted_in_account);
					//if(posted_in_account==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
					//else 	document.getElementById("accounting_posted_status").innerHTML="";
					
					get_php_form_data(trims_recv_id, "populate_data_from_trims_recv", "requires/yarn_grn_qc_controller" );
					load_details_data(trims_recv_id);
					$('#txt_booking_pi_no').attr('disabled',true);
					//$('#cbo_receive_basis').attr('disabled',true);
					set_button_status(1, permission, 'fnc_yarn_qc',1,1);	
					release_freezing();
				}
			}
		}
	}
	
	function calculate(i)
	{
		if(i>0)
		{
			var grn_qnty 		= $('#grnqnty_'+i).attr("title")*1;
			if($('#rejectqnty_'+i).val()!="")
			{
				var reject_qnty 	= $('#rejectqnty_'+i).val()*1;
				var issue_qnty		= grn_qnty-reject_qnty;
				//alert(grn_qnty+"="+reject_qnty+"="+issue_qnty);
				$('#qcqnty_'+i).val(issue_qnty);
			}
			else
			{
				$('#qcqnty_'+i).val("");
			}
		}
		
		var ddd={ dec_type:5, comma:0, currency:''}
		var numRow = $('table#tbl_fabric_desc_item tbody tr').length-1;
		//alert(numRow);
		math_operation( "tot_rcv_qnty", "qcqnty_", "+", numRow,ddd );
		
		
	}
	
	function fn_fill_qnty()
	{
		var i=1;
		if($('#check_qnty').is(':checked'))
		{
			$("#tbl_fabric_desc_item").find('tbody tr').not(':first').each(function(index, element) {
				if($('#row_'+i).css('display') != 'none')
				{
					var grn_qnty=$("#grnqnty_"+i).attr("title")*1;
					var reject_qnty=$("#rejectqnty_"+i).val()*1;
					var issue_qnty=grn_qnty-reject_qnty;
					//alert(grn_qnty+"="+reject_qnty+"="+issue_qnty);//return;
					if(issue_qnty>0)
					{
						$(this).find('input[name="qcqnty[]"]').val(issue_qnty);
					}
				}
                i++;
            });
		}
		else
		{
			$("#tbl_fabric_desc_item").find('tbody tr').not(':first').each(function(index, element) {
				$(this).find('input[name="qcqnty[]"]').val("");
            });
			
		}
		calculate(0);
	}
	
	function copy_all(str)
	{
		var trall=$("#tbl_fabric_desc_item tbody tr").length-1;
		//alert(trall);
		var copy_tr=parseInt(trall);
		if($('#check_status').is(':checked'))
		{
			data_value=$("#cbostatus_"+str).val();
		}

		var first_tr=parseInt(str)+1;
		for(var k=first_tr; k<=copy_tr; k++)
		{
			if($('#check_status').is(':checked'))
			{
				$("#cbostatus_"+k).val(data_value);
			}	
		}
	}
	

</script>
<body onLoad="set_hotkey();">
<div style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission); ?>
    <form name="trimsreceive_1" id="trimsreceive_1">
    	<div style="width:1300px;" align="center">        
            <fieldset style="width:1300px">
            <legend>Yarn QC Entry Master Part</legend>
			<fieldset style="width:1150px;">
            <table cellpadding="0" cellspacing="2" width="100%" align="left">
                <tr>
                    <td align="right" colspan="5"><strong> Yarn QC ID </strong></td>
                    <td colspan="5">
                        <input type="hidden" name="update_id" id="update_id" />
                        <input type="text" name="txt_recieved_id" id="txt_recieved_id" class="text_boxes" style="width:130px" placeholder="Double Click" onDblClick="yarn_receive_popup();" >
                    </td>
                </tr>
                <tr>
                    <td width="80" class="must_entry_caption"> Company </td>
                    <td width="150">
						<?
						echo create_drop_down( "cbo_company_id", 142, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down( 'requires/yarn_grn_qc_controller', this.value, 'load_drop_down_supplier', 'supplier' );reset_form('trimsreceive_1','list_container_trims*list_fabric_desc_container','','','set_receive_basis(0);','cbo_company_id');" );
                        ?>
                    </td>
                    <td width="80" id="supplier_td"> Supplier </td>
                    <td id="supplier" width="150">
                        <?
                          echo create_drop_down( "cbo_supplier", 142, $blank_array,"", 1, "--- Select Supplier ---", $selected, "",1);
                        ?>
                    </td>
                    <td width="80">Q A Person</td>
                    <td width="150">
                        <input type="text" name="txt_qa_person" id="txt_qa_person" class="text_boxes" style="width:130px" >
                    </td>
                    <td width="80">Supplier Greading</td>
                    <td width="150">
						<?
                        echo create_drop_down( "cbo_supplier_grade", 142, $fabric_shade,"", 1, "-- Select Grade --", 0, "" );
                        ?>
                    </td>
                    <td width="80" class="must_entry_caption"><strong>Parking/GRN</strong></td>
                    <td width="150">
                    <input type="text" name="txt_booking_pi_no" id="txt_booking_pi_no" class="text_boxes" style="width:130px"  placeholder="Double Click to Search" onDblClick="openmypage_wo_pi_popup();" readonly>
                    <input type="hidden" id="txt_wo_pi_id" name="txt_wo_pi_id" value="" />
                    </td>
                </tr>
            </table>
			</fieldset>

            <fieldset style="width:1300px; margin-top:10px;">
                 	<legend>Yarn QC Entry details part</legend>
                    <? $i=1; ?>
                    	<table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" id="tbl_fabric_desc_item">
                        	<thead>
								<tr>
                                	<th width="100">GRN</th>
                                    <th width="60">GRN Date</th>
                                    <th width="65">Brand</th>
                                    <th width="65">Lot</th>
									<th width="65">Count</th>
									<th width="150">Composition</th>
									<th width="30">%</th>
                                    <th width="100">Color</th>
									<th width="80">Yarn Type</th>					
									<th width="40">UOM</th>
									<th width="70">GRN Qty</th>
									<th width="70" class="must_entry_caption">Reject Qty</th>
                                    <th width="70" class="must_entry_caption"><input type="checkbox" id="check_qnty" name="check_qnty" onChange="fn_fill_qnty()" />&nbsp;QC. Pass Qty</th>	
                                    <th width="80" class="must_entry_caption"><input type="checkbox" id="check_status" name="check_status" />Status</th>
                                    <th width="60">Grading</th>
                                    <th width="60">Yarn Test</th>
                                    <th>Comments</th>
								</tr>
                            </thead>
                            <tbody id="list_fabric_desc_container">
                            	<tr id="row_1" align="center">
                                    <td id="grn_1"></td>
                                    <td id="grndate_1"></td>
                                    <td id="tdbrand_1"></td>
                                    <td id="tdlot_1"></td>
                                    <td id="count_1"></td>
                                    <td id="composition_1"></td>
                                    <td id="comPersent_1"></td>
                                    <td id="color_1"></td>
                                    <td id="yarnType_1"></td>
                                    <td id="uom_1"></td>
                                    <td id="grnqnty_1"></td>
                                    <td id="tdrejectqnty_1"><input type="text" name="rejectqnty[]" id="rejectqnty_1" class="text_boxes_numeric" style="width:60px;" value="" onBlur="calculate(1);"/></td>
                                    <td id="tdqcqnty_1"><input type="text" name="qcqnty[]" id="qcqnty_1" class="text_boxes_numeric" style="width:60px;" value="" onBlur="calculate(1);"/></td>
                                    
                                    <td id="tdstatus_1">
                                    <? 
                                    echo create_drop_down( "cbostatus_1",70, $yarn_qc_statusArr ,'', 1, '--Select--',0,"copy_all(1);","","","","","","","","cbostatus[]",""); 
                                    ?>
                                    </td>
                                    <td id="tdgrading_1">
                                    <? 
                                    echo create_drop_down( "cbograde_1",70, $fabric_shade,'', 1, '--Select--',0,"","","","","","","","","cbograde[]",""); 
                                    ?>
                                    </td>
                                    <td id="tdyarntest_1">
                                    <? 
                                    echo create_drop_down( "cboyarntest_1",70, $comments_acceptance_arr ,'', 1, '--Select--',0,"","","","","","","","","cboyarntest[]",""); 
                                    ?>
                                    </td>
                                    <td id="tdcomments_1">
                                    <input type="text" name="comments[]" id="comments_1" class="text_boxes" style="width:90px;" value=""/>
                                    <input type="hidden" name="updatedtlsid[]" id="updatedtlsid_1" value="" readonly>
                                    <input type="hidden" name="previousprodid[]" id="previousprodid_1" value="" readonly>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>					
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th><input type="text" id="tot_rcv_qnty" name="tot_rcv_qnty" style="width:60px;" class="text_boxes_numeric" readonly disabled /></th>
                                
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                            </tfoot>
                        </table>
                </fieldset>
                 <table width="100%">
                    <tr>
                        <td width="80%" align="center"> 
                        <?
                        echo load_submit_buttons($permission, "fnc_yarn_qc", 0,1,"reset_form('trimsreceive_1','list_container_trims*list_fabric_desc_container','','','set_receive_basis(1);','')",1);
                        
                        ?>
                        <input type="hidden" id="is_posted_account" name="is_posted_account" value=""/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" align="center">
                            <div id="accounting_posted_status" style=" color:red; font-size:24px;";  ></div>
                        </td>
                    </tr>
                </table> 
            <br>
            <div style="width:650px;" id="list_container_trims"></div>
		</fieldset>
        </div>  
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
/*$('input[name^="receiveqnty"]').live('keydown', function(e) {
	
	switch (e.keyCode) {
			case 38:
				var target_id_arr=e.target.id.split('_');
				var row_num=parseInt(target_id_arr[1])-1;
				//alert(row_num);
				//$('#receiveqnty_'+row_num).focus();
				$('#receiveqnty_'+row_num).select();
				break;
			case 40:
				var target_id_arr=e.target.id.split('_');
				var row_num=parseInt(target_id_arr[1])+1;
				//alert(row_num);
				//$('#receiveqnty_'+row_num).focus();
				$('#receiveqnty_'+row_num).select();
				break;
	}
});
*/
</script>
</html>