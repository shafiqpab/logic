<?
/*************************************************Comments***********************************
* Purpose			: 	This form will create Machine Wash Issue Requisition Entry     		*
* Functionality	:                                                                           *	
* JS Functions	:                                                                           *
* Created by		:	Fuad                                                               	*
* Creation date 	: 	06-04-2015                                                          *
* Updated by 		:                                           							*		
* Update date		:                                                          				*		   
* QC Performed BY	:                                                                       *		
* QC Date			:                                                                       *	
* Comments		:                                                                           *
********************************************************************************************/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
echo load_html_head_contents("Dyes And Chemical Issue Requisition","../../", 1, 1, $unicode,1,1); 
//--------------------------------------------------------------------------------------------------------------------

?>	

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function rcv_basis_reset()
	{
		reset_form('chemicaldyesissuerequisition_1','list_container_recipe_items','','','','cbo_company_name');
	} 
	
	function fnc_chemical_dyes_issue_requisition(operation)
	{
		if(operation==5)
		{
			if( form_validation('update_id','Requisition Number')==false )
			{
				alert("Save Data First.");return;
			}
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title, "chemical_dyes_issue_requisition_print2", "requires/machine_wash_issue_requisition_controller" );
			return;
		}
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title, "chemical_dyes_issue_requisition_print", "requires/machine_wash_issue_requisition_controller" );
			return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{			
			if(operation==2)
			{
				var q=confirm("Press OK to Delete Or Press Cancel");
					if(q==false){
						//release_freezing();
						return;
					}
			}
			var copy_val=$('#copy_id').val();
			//if(copy_val==2) //Copy  no
			//{
				if( form_validation('cbo_company_name*txt_requisition_date*txt_machine_no*txt_tot_liquor*cbo_store_name','Company Name*Requisition Date*Machine No*Total Liquor (ltr)*Store')==false )
				{
					return;
				}
			//}
		
			if(copy_val==1) //Copy Item Yes
			{
				var cbo_company_name = $('#cbo_company_name').val();
				var update_id_check = $('#update_id_check').val();
			}
			
			var row_num=$('#tbl_list_search tbody tr').length; var data_all=""; var i=0;
			for (var j=1; j<=row_num; j++)
			{
				var txt_reqn_qnty=$('#txt_reqn_qnty_'+j).val()*1;
			
				if(txt_reqn_qnty>0) 
				{
					i++;
					data_all+="&txt_prod_id_" + i + "='" + $('#txt_prod_id_'+j).val()+"'"+"&txt_item_cat_" + i + "='" + $('#txt_item_cat_'+j).val()+"'"+"&cbo_dose_base_" + i + "='" + $('#cbo_dose_base_'+j).val()+"'"+"&txt_ratio_" + i + "='" + $('#txt_ratio_'+j).val()+"'"+"&txt_recipe_qnty_" + i + "='" + $('#txt_recipe_qnty_'+j).val()+"'"+"&txt_reqn_qnty_" + i + "='" + $('#txt_reqn_qnty_'+j).val()+"'"+"&txt_remark_" + i + "='" + $('#txt_remark_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'"+"&txt_item_lot_" + i + "='" + $('#txt_item_lot_'+j).val()+"'"+"'"+"&txt_seqno_" + i + "='" + $('#txt_seqno_'+j).val()+"'";
				}
			}			
			var data="action=save_update_delete&operation="+operation+'&total_row='+i+data_all+get_submitted_data_string('txt_mrr_no*update_id*copy_id*update_id_check*txt_copy_from*cbo_company_name*cbo_location_name*txt_requisition_date*cbo_receive_basis*txt_tot_liquor*cbo_method*machine_id*cbo_store_name',"../../");
			 //alert(data_all);return;
			freeze_window(operation);

			http.open("POST","requires/machine_wash_issue_requisition_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_chemical_dyes_issue_requisition_reponse;
		}
	}

	function fnc_chemical_dyes_issue_requisition_reponse()
	{	
		if(http.readyState == 4) 
		{   
			var reponse=trim(http.responseText).split('**');	
			show_msg(reponse[0]);
			
			if((reponse[0]==0 || reponse[0]==1 ))
			{
				document.getElementById('txt_mrr_no').value = reponse[1];
				document.getElementById('update_id').value = reponse[2];
				document.getElementById('update_id_check').value = reponse[2];
				$('#copy_id').val(2);
			   $('#copy_id').removeAttr('disabled','disabled');
			
				var company = $("#cbo_company_name").val();
				var store_name = $("#cbo_store_name").val();
				$('#cbo_company_name').attr('disabled','disabled');
				$('#cbo_store_name').attr('disabled','disabled');
				show_list_view(company+"**"+reponse[2]+"**"+store_name, 'item_details', 'list_container_recipe_items', 'requires/machine_wash_issue_requisition_controller', '');
				setFilterGrid("tbl_list_search",-1);
				set_button_status(1, permission, 'fnc_chemical_dyes_issue_requisition',1,1);
			}
			if(reponse[0]==2)
			{
				document.getElementById('txt_mrr_no').value = '';
				document.getElementById('update_id').value = '';
				document.getElementById('update_id_check').value = '';
				$('#cbo_company_name').attr('disabled',false);
				$('#cbo_store_name').attr('disabled',false);
				show_list_view(company+"**"+reponse[2]+"**"+store_name, 'item_details', 'list_container_recipe_items', 'requires/machine_wash_issue_requisition_controller', '');
				setFilterGrid("tbl_list_search",-1);
				set_button_status(0, permission, 'fnc_chemical_dyes_issue_requisition',1,1);
			}
			
			release_freezing();	
		}
	}
	function fnc_load_report_format(data)
	{
		var report_ids='';
		var report_ids = return_global_ajax_value( data, 'load_report_format', '', 'requires/machine_wash_issue_requisition_controller');
		print_report_button_setting(report_ids);
	}
	
	function print_report_button_setting(print_id)
	{
		$('#btn_report').hide();
		$('#btn_report2').hide();
		var buttonId = print_id.split(',');
 		if(buttonId != "")
		{
			for (var i = 0; i < buttonId.length; ++i)
			{
			    // alert(buttonId[i]);
			    if(buttonId[i]==134){ $('#btn_report').show();}
				if(buttonId[i]==210){ $('#btn_report2').show();}
			}
		}
		else
		{
			$('#btn_report').show();
			$('#btn_report2').show();
		}
	}

	function open_mrrpopup()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var page_link='requires/machine_wash_issue_requisition_controller.php?action=mrr_popup&company='+company; 
		var title="Requisition Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=870px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var reqnId=this.contentDoc.getElementById("hidden_sys_id").value; 
			reset_form('','list_container_recipe_items','','','','');
			get_php_form_data(reqnId, "populate_data_from_data", "requires/machine_wash_issue_requisition_controller");
			var store_name = document.getElementById('cbo_store_name').value;
			show_list_view(company+"**"+reqnId+"**"+store_name, 'item_details', 'list_container_recipe_items', 'requires/machine_wash_issue_requisition_controller', '');
			$('#cbo_store_name').attr('disabled','disabled');
			$('#copy_id').removeAttr('disabled','disabled');
			setFilterGrid("tbl_list_search",-1);
		}
	}
	/*function fn_sub_process_enable()
	{
		// alert('ok');
	}*/
	
	function show_details()
	{
		var cbo_company_name = $('#cbo_company_name').val();
		var cbo_store_name = $('#cbo_store_name').val();
		if(form_validation('cbo_company_name*cbo_store_name','Company*Store')==false)
		{
			return;
		}
		//alert(cbo_store_name);
		show_list_view(cbo_company_name+"****"+cbo_store_name, 'item_details', 'list_container_recipe_items', 'requires/machine_wash_issue_requisition_controller', '');
		setFilterGrid("tbl_list_search",-1);
	}

	function calculate_requs_qty(type,i)
	{
		if(form_validation('txt_tot_liquor','Total Liquor (ltr)')==false)
		{
			$("#txt_recipe_qnty_"+i).val('');
			$("#txt_ratio_"+i).val('');
			$("#txt_reqn_qnty_"+i).val('');
			return;
		}
		
		var txt_tot_liquor = $('#txt_tot_liquor').val()*1;
		var ratio = $("#txt_ratio_"+i).val()*1;	
		
		
		var requisition_qty=0;
		if(type==1)
		{
			requisition_qty=(ratio*txt_tot_liquor)/1000;
			$("#txt_reqn_qnty_"+i).val(requisition_qty.toFixed(6));
			$("#txt_recipe_qnty_"+i).val(requisition_qty.toFixed(6));
		}
		else
		{
			requisition_qty=(txt_reqn_qnty/txt_tot_liquor)*1000;
			$("#txt_ratio_"+i).val(requisition_qty.toFixed(6));
			$("#txt_recipe_qnty_"+i).val(txt_reqn_qnty.toFixed(6));
		}
	}
	
	function fn_machine_seach()
	{
		var cbo_company_id = $('#cbo_company_name').val();
		
		if (form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Machine No Selection Form';	
			var page_link = 'requires/machine_wash_issue_requisition_controller.php?cbo_company_id='+cbo_company_id+'&action=machineNo_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=755px,height=350px,center=1,resize=1,scrolling=0','../');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var machine_id=this.contentDoc.getElementById("hidden_machine_id").value;	
				var machine_name=this.contentDoc.getElementById("hidden_machine_name").value;	
				
				$('#machine_id').val(machine_id);
				$('#txt_machine_no').val(machine_name);
			}
		}
	}

	function color_row(tr_id)
	{
		var txt_ratio=$('#txt_ratio_'+tr_id).val()*1;
		if(txt_ratio<=0)
		{
			$('#search' + tr_id).css('background-color','#FFFFCC');
			$('#txt_ratio_' + tr_id).css('background-color','White');
		}
		else
		{
			$('#search' + tr_id).css('background-color','yellow');
			$('#txt_ratio_' + tr_id).css('background-color','White');
		}
		var txt_reqn_qnty = $("#txt_reqn_qnty_"+tr_id).val()*1;
		var stock_qty= $("#stock_qty_chk_"+tr_id).val()*1;
		//alert(stock_qty+'='+txt_reqn_qnty);
		if(stock_qty<txt_reqn_qnty)
		{
			alert('Req. Qty should not over than stock');
			$("#txt_ratio_"+tr_id).val('');
			$("#txt_recipe_qnty_"+tr_id).val('');
			$("#txt_reqn_qnty_"+tr_id).val('');
			return;
		}


	}
	/*function openmypage_itemLot(id)
	{
		var cbo_company_id = $('#cbo_company_name').val();
		var txt_prod_id = $('#txt_prod_id_'+id).val();
		var txt_item_lot = $('#txt_item_lot_'+id).val();
		//alert  (txt_prod_id);
		if (form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		else
		{
			var title = 'Item Lot Selection Form';
			var page_link = 'requires/machine_wash_issue_requisition_controller.php?cbo_company_id='+cbo_company_id+'&txt_prod_id='+txt_prod_id+'&txt_item_lot='+txt_item_lot+'&action=itemLot_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=350px,height=390px,center=1,resize=1,scrolling=0','');

			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var item_lot=this.contentDoc.getElementById("item_lot").value;
				if(item_lot!="")
				{
					freeze_window(5);
					document.getElementById("txt_item_lot_"+id).value=item_lot;
					release_freezing();
				}
			}
		}
	}*/

	function copy_check(type)
	{
		var recipe_prev_id=$('#txt_mrr_no').val();
		
		var cbo_company_name = $('#cbo_company_name').val();
		var txt_requisition_date = $('#txt_requisition_date').val();
	
		
		if(type==1)
		{
			
			
			//$("#list_container_recipe_items").html('');
			$('#update_id').val('');
			$('#txt_mrr_no').val('');
			$('#txt_requisition_date').val('');
			//$('#txt_machine_no').val('');
			//$('#machine_id').val('');
			 
			
		}
		if(type==1)
		{
			$('#txt_copy_from').val(recipe_prev_id);
		}
		else
		{
			$('#txt_copy_from').val('');

		}
	
		if ( document.getElementById('copy_id').checked==true)
		{
			document.getElementById('copy_id').value=1;
			set_button_status(0, permission, 'fnc_chemical_dyes_issue_requisition',1,1);
			//alert(chk );
		}
		else if(document.getElementById('copy_id').checked==false)
		{
			document.getElementById('copy_id').value=2;
		}
		//alert(type );
	}
	function seq_no_val(id)
	{
		var row_num=$('#tbl_list_search tbody tr').length-1;
		var seq_no =new Array();
		var k=0;
		for(var j=1; j<=row_num; j++)
		{
			if(j!=id)
			{

				if( $('#txt_seqno_'+j).val()*1>0)
				{
					seq_no[k]=$('#txt_seqno_'+j).val()*1;
					k++;
				}
			}
		}
		var largest=0;
		if(seq_no!='')
		{
			var largest = Math.max.apply(Math, seq_no);
		}
		if(largest=='')
		{
			largest=0;
		}//alert (largest)
		/*alert (seq_no+"=="+largest)
		if ($('#txt_seqno_'+id).val()!='')
		{
			$('#txt_max_seq').val(largest*1);
		}*/

		largest=largest+1;
		//alert(largest);
		/*var max_seq=$('#txt_max_seq').val()*1;
		if ($('#txt_ratio_'+id).val()!='')
		{
			max_seq=max_seq+1;
		}*/
		for(var i=1;i<=largest;i++)
		{
			if ($('#txt_ratio_'+id).val()!='')
			{
				if ($('#txt_seqno_'+id).val()=='')
				{
					$('#txt_seqno_'+id).val(largest);
				}
			}
			else
			{
				$('#txt_seqno_'+id).val('');
			}
		}
		//$('#txt_max_seq').val(max_seq);
		//row_sequence(id)
	}
	function row_sequence(row_id)
	{
		var row_num=$('#tbl_list_search tbody tr').length-1;
		var txt_seq=$('#txt_seqno_'+row_id).val();
		//var seq_no=1;
		if(txt_seq=="")
		{
			return;
		}

		for(var j=1; j<=row_num; j++)
		{
			if(j==row_id)
			{
				continue;
			}
			else
			{
				var txt_seq_check=$('#txt_seqno_'+j).val();

				if(txt_seq==txt_seq_check)
				{
					//alert("Duplicate Seq No. "+txt_seq);
					//$('#txt_seqno_'+row_id).val('');
					//return;
				}
			}
		}
	}
	
	function lib_check_labdip(type) 
	{
		var update_id=$('#update_id').val();
		if ( document.getElementById('lab_check_id').checked==true)
		{
			
			//if(update_id=="")
			//{
				var chk=document.getElementById('lab_check_id').value=1;
				show_details();
			//}
			
			
		}
		else if(document.getElementById('lab_check_id').checked==false)
		{
			var chk=document.getElementById('lab_check_id').value=2;
		}
		//alert(chk );
	}

</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission);  ?>   		 
        <form name="chemicaldyesissuerequisition_1" id="chemicaldyesissuerequisition_1" autocomplete="off" > 
    		<div style="width:950px;">       
            	<fieldset style="width:930px;">
                <legend>Dyes And Chemical Issue Requisition</legend>
                	<table width="930" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                        <tr>
                            <td colspan="6" align="center">&nbsp;<b>Requisition Number</b>
                                <input type="text" name="txt_mrr_no" id="txt_mrr_no" class="text_boxes" style="width:155px" placeholder="Double Click To Search" onDblClick="open_mrrpopup()" readonly /> <input type="hidden" name="update_id" id="update_id" />
                                <input type="hidden" name="update_id_check" id="update_id_check" />
                               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                                <strong>Copy</strong>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="copy_id" id="copy_id" onClick="copy_check(1)" value="2" disabled >
                            </td>
                            
                        
                       </tr>
                       <tr>
                           <td width="130" align="right" class="must_entry_caption">Company Name </td>
                           <td width="170">
                                <? 
                                	echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "rcv_basis_reset();fnc_load_report_format(this.value);load_drop_down( 'requires/machine_wash_issue_requisition_controller', this.value, 'load_drop_down_location', 'location_td');load_drop_down( 'requires/machine_wash_issue_requisition_controller', $('#cbo_company_name').val()+'_'+$('#cbo_location_name').val(), 'load_drop_down_store', 'store_td' );","");
                                ?>
                           </td>
                           <td width="130" align="right"> Location </td>
                           <td width="170" id="location_td">
                               <? echo create_drop_down( "cbo_location_name", 170, $blank_array,"", 1, "-- Select Location --", 0, "" ); ?>
                           </td>
                           <td  width="130" align="right" class="must_entry_caption" >Requisition Date </td>
                           <td width="170">
                               <input type="text" name="txt_requisition_date" id="txt_requisition_date" class="datepicker" style="width:160px;" placeholder="Select Date" />
                           </td>
                        </tr>
                        <tr>
                           <td align="right" class="must_entry_caption"> Issue Basis </td>
                           <td>
                                <? echo create_drop_down("cbo_receive_basis",170,$receive_basis_arr,"",0,"- Select Basis -",4,"","1","4"); ?>
                           </td>
                           <td align="right" class="must_entry_caption">Machine No</td>
                           <td>
                                <input type="text" name="txt_machine_no" id="txt_machine_no" class="text_boxes" style="width:158px;" onDblClick="fn_machine_seach();" placeholder="Browse" readonly/>
                                <input type="hidden" name="machine_id" id="machine_id" class="text_boxes"/>
                            </td>
                            <td align="right" class="must_entry_caption"> Total Liquor (ltr)</td>
                           	<td>
                           		<input type="text" name="txt_tot_liquor" id="txt_tot_liquor" class="text_boxes_numeric" style="width:160px;"/>
                        	</td>
                        </tr>
                        <tr>
                           <td align="right">Requisition for</td>
                           <td><? echo create_drop_down( "cbo_method", 170, $dyeing_method,"", 1, "--Select Method--", $selected, "",0,"0,130,140,150,160,170,171,172,173,174,175,176,177,178,179" ); ?></td>
						   <td align="right" class="must_entry_caption">Store Name</td>
                           <td id="store_td"><? echo create_drop_down( "cbo_store_name", 160, "$storeName","id,store_name", 1, "-- Select Store --", $storeName, "",0 ); ?></td>
                           
                           <td> <input type="text" name="txt_copy_from" id="txt_copy_from" class="text_boxes_numeric" placeholder="Copy From" style="width:100px;" disabled/></td>
                           <td><input type="button" value="Show Items" name="show" id="show" class="formbuttonplasminus" style="width:100px" onClick="show_details()"/></td>
                       </tr>
					</table>
				</fieldset>
			</div>
            <div style="width:1040px;">  
                <fieldset>
                    <table cellpadding="0" cellspacing="1" width="100%">
                    	<tr> 
                           <td colspan="6" align="center"><div id="list_container_recipe_items" style="margin-top:10px"></div></td>				
                        </tr>
                        <tr>
                            <td align="center" colspan="4" valign="middle" class="button_container">
                            	<input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
								<? echo load_submit_buttons($permission, "fnc_chemical_dyes_issue_requisition", 0,1,"	reset_form('chemicaldyesissuerequisition_1','list_container_recipe_items','','','','');",1); ?>
                                
                                <input type="button" id="btn_report" style="display: none;" value="Print" class="formbutton" style="width:100px;" onClick="fnc_chemical_dyes_issue_requisition(4)" /> &nbsp;
								<input type="button" style="display: none;" id="btn_report2" value="Without Rate" class="formbutton" style="width:100px;" onClick="fnc_chemical_dyes_issue_requisition(5)" />
                            </td>
                       	</tr> 
                    </table>                 
              	</fieldset>
    		</div>
		</form>
	</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
