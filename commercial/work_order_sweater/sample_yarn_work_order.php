<?
/*-------------------------------------------- Comments
Purpose			: 	Yarn Work order entry
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	13-03-19
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
$req_variable_setting=2;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sample Yarn Work Order","../../", 1, 1, $unicode,1,''); 

/*$color_sql = sql_select("select id,color_name from lib_color order by id");
$color_name = "";
foreach($color_sql as $result)
{ 
	$color_name.= "{value:'".$result[csf('color_name')]."',id:".$result[csf('id')]."},";
}*/
 
?> 
<script>
	var permission='<? echo $permission; ?>';
	//var req_variable_setting='<?// echo $req_variable_setting; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name", "color_name" ), 0, -1); ?>];

	
	function add_auto_complete(i)
	{
		 $("#txtyarncolor_"+i).autocomplete({
			 source: str_color
		  });
	}

	function fn_disable_enable(str)
	{
		var dtls_html=return_global_ajax_value(str, 'ini_dtls_listview', '', 'requires/sample_yarn_work_order_controller');
		//alert(dtls_html);
		if(dtls_html!="")
		{
			$("#txt_requisition").val("");
			$("#txt_req_id").val("");
			$("#txt_req_dtls_id").val("");
			$('#details_part_list').html("");
			$('#details_part_list').append(dtls_html);
		}
		
		if(str==1)
		{
			$("#txt_requisition").attr("disabled",false);
			$('input[name="txtjobno[]"]').attr("disabled",true);
			$('input[name="increase[]"]').attr("disabled",true);
			$('select[name="cbobuyername[]"]').attr("disabled",true);
			$('input[name="txtstyleno[]"]').attr("disabled",true);
			$('input[name="txtstyleno[]"]').attr("readonly",true);
			
		}
		else if(str==2)
		{
			$("#txt_requisition").attr("disabled",true);
			$('input[name="txtjobno[]"]').attr("disabled",false);
			$('input[name="increase[]"]').attr("disabled",true);
			$('select[name="cbobuyername[]"]').attr("disabled",true);
			$('input[name="txtstyleno[]"]').attr("disabled",true);
			$('input[name="txtstyleno[]"]').attr("readonly",true);
		}
		else if(str==3)
		{
			$(".leader_td_label").css("color","blue");
			$(".merchant_td_label").css("color","blue");

			$("#txt_requisition").attr("disabled",true);
			$('input[name="txtjobno[]"]').attr("disabled",true);
			$('input[name="increase[]"]').attr("disabled",false);
			$('select[name="cbobuyername[]"]').attr("disabled",false);
			$('input[name="txtstyleno[]"]').attr("disabled",false);
			$('input[name="txtstyleno[]"]').attr("readonly",false);
		}
		else
		{
			$("#txt_requisition").attr("disabled",true);
			$('input[name="txtjobno[]"]').attr("disabled",true);
			$('input[name="increase[]"]').attr("disabled",false);
			$('select[name="cbobuyername[]"]').attr("disabled",false);
			$('input[name="txtstyleno[]"]').attr("disabled",false);
			$('input[name="txtstyleno[]"]').attr("readonly",false);
		}
		set_all_onclick();
	}
	
	//details part row incriment here Sample Basis
	function add_break_down_tr(i)
	{
		//alert(i);
		if( form_validation('cbo_company_name*reqqnty_'+i,'Company Name*Quantity')==false )
		{
			return;
		}
		
		/*if(trim($('#txtjobid_'+i).val())!="")
		{
			return false;
		}*/
		
		var row_num=$('#tbl_purchase_item tbody tr').length;
		if (row_num!=i)
		{
			return false;
		}
		else
		{ 
			i++;
            var k=i-1;
			$("#tbl_purchase_item tbody tr:last").clone().find("input,select").each(function(){
			$(this).attr({ 
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  //'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
			  'value': function(_, value) { return value }              
			});
			}).end().appendTo("#tbl_purchase_item");
			
			$("#tbl_purchase_item tbody tr:last").css({"height":"10px","background-color":"#FFF"});	
			$("#tbl_purchase_item tbody tr:last ").removeAttr('id').attr('id','tr_'+i);
			
			$('#txtrowid_'+i).val('');
			$('#cbobuyername_'+i).val('');
			$('#txtstyleno_'+i).val('');
			$('#reqqnty_'+i).val('');
			$('#txtrate_'+i).val('');
			$('#txtamount_'+i).val('');
			$('#txtremarks_'+i).val('');
			
			
			/*$('#txtjobno_'+i).removeAttr("onDblClick").removeAttr("placeholder");
			$('#txtjobno_'+k).removeAttr("onDblClick").removeAttr("placeholder");
			$('#txtwono_'+i).removeAttr("onDblClick").removeAttr("placeholder");
			$('#txtwono_'+k).removeAttr("onDblClick").removeAttr("placeholder");
			
			$('#txtjobno_'+k).removeAttr("onDblClick").removeAttr("placeholder");
			$('#txtwono_'+k).removeAttr("onDblClick").removeAttr("placeholder");
			$('#txtjobno_'+row_num).removeAttr("onDblClick").removeAttr("placeholder").attr("onDblClick","openmypage_job("+row_num+");").attr("placeholder","Doble Click For Job Number");
			$('#txtwono_'+row_num).removeAttr("onDblClick").removeAttr("placeholder").attr("onDblClick","openmypage_wo("+row_num+");").attr("placeholder","Doble Click For WO Number");*/
			
			$('#reqqnty_'+i).removeAttr("onKeyUp").attr("onKeyUp","calculate_amount("+i+");");
			$('#txtrate_'+i).removeAttr("onKeyUp").attr("onKeyUp","calculate_amount("+i+");");
			$('#txtyarncolor_'+i).removeAttr("onFocus").attr("onFocus","add_auto_complete("+i+");");
			$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deletebreak_down_tr("+i+");");
			set_all_onclick();
		}
	}
	
	
	function fn_deletebreak_down_tr(rowNo) 
	{
		var row_num=$('#tbl_purchase_item tbody tr').length;
		var attr=$("#decrease_"+rowNo).attr("readonly");
		//alert(attr);
		if(attr=="readonly")
		{ 
			alert("Already added to Work Order("+woNum+"). Update/Delete not possible");
			return;
		}
		else
		{
			if(rowNo!=row_num)
			{ 		
				$('#tr_'+rowNo).hide();
			}
		}
	}
 
	// for Job
	function openmypage_job(row_no)
	{
		if (form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}	
		else
		{
		
			var company = $("#cbo_company_name").val();
			var cbo_basis = $("#cbo_basis").val();
			var title="Job No Popup";
			page_link='requires/sample_yarn_work_order_controller.php?action=order_search_popup&company='+company+'&cbo_basis='+ cbo_basis;
			var width = "620px";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+width+',height=390px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var data=this.contentDoc.getElementById("hidden_tbl_id").value.split("_");
				//freeze_window(5);
				var dtls_html=return_global_ajax_value(data[0]+'_'+data[1]+'_'+row_no+'_'+company+'_'+cbo_basis, 'dtls_part_html_row', '', 'requires/sample_yarn_work_order_controller');

				if(dtls_html!="")
				{
					$('#tr_'+row_no).remove();
					$('#details_part_list').append(dtls_html);
				}
				$("#cbo_basis").attr('disabled',true);
				$('#cbo_company_name').attr('disabled','disabled');
				set_all_onclick();
				//release_freezing();
			}
			
		}
	}
    function print_button_setting()
	{
		//$('#button_data_panel').html('');
		get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/sample_yarn_work_order_controller' ); 
	}
	
	// for Rew
	function openmypage_req()
	{
		var company = $("#cbo_company_name").val();
		var garments_nature = $("#garments_nature").val(); 
		var txt_req_id = $("#txt_req_id").val();
		var txt_req_dtls_id = $("#txt_req_dtls_id").val();
		var cbo_wo_basis = $("#cbo_wo_basis").val();
		var page_link = 'requires/sample_yarn_work_order_controller.php?action=requisition_popup&company='+company+'&garments_nature='+garments_nature+'&txt_req_id='+txt_req_id+'&txt_req_dtls_id='+txt_req_dtls_id+'&cbo_wo_basis='+cbo_wo_basis;
		
		var title = "Requisition Search"; 
		
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=390px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var dtls_id=this.contentDoc.getElementById("txt_dtls_id").value; //dtls id here
			var mst_id=this.contentDoc.getElementById("txt_mst_id").value; // req mst id
			var req_no=this.contentDoc.getElementById("txt_req_no").value; // req no
			$("#txt_req_id").val(mst_id); 
			$("#txt_req_dtls_id").val(dtls_id);
			$("#txt_requisition").val(req_no);
			
			var update_id=$("#update_id").val();
			//alert(dtls_id+'***'+mst_id);
			var dtls_html=return_global_ajax_value(dtls_id+'***'+mst_id+'***'+update_id, 'show_req_dtls_listview', '', 'requires/sample_yarn_work_order_controller');
			//alert(dtls_html);
			if(dtls_html!="")
			{
				//$('#tr_'+row_no).remove();
				$('#details_part_list').html("");
				$('#details_part_list').append(dtls_html);
			}
			$("#cbo_basis").attr('disabled',true);
			$('#cbo_company_name').attr('disabled','disabled');
			set_all_onclick();
			
			/*if(dtls_id!="")
			{			
				freeze_window(5);			
				show_list_view(dtls_id+'***'+mst_id+'***'+update_id,'show_req_dtls_listview','details_container','requires/sample_yarn_work_order_controller','');
				release_freezing();
			}
			else
			{
				$("#details_container").html('');
			}
			
			if(update_id!="")
			{
				var delID=return_global_ajax_value( update_id, 'previous_dtls_id', '', 'requires/sample_yarn_work_order_controller');//For Buyer Po Changed
				$("#txt_delete_row").val(delID);
			}
			
			$("#cbo_basis").attr('disabled',true);
			$('#cbo_company_name').attr('disabled','disabled');*/
			
		}
	}
	
	// amount calculation here
	function calculate_amount(row_id)
	{
		var tot_amount=($('#reqqnty_'+row_id).val()*1)*($('#txtrate_'+row_id).val()*1);
		$('#txtamount_'+row_id).val(number_format_common(tot_amount,4));
	}
	
	function fnc_yarn_order_entry(operation)
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#ref_closed_sts').val(),"yarn_work_order_print", "requires/sample_yarn_work_order_controller")
			return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if(operation==2)
			{
				alert("Delete Permission Not Allow");show_msg(13);return;
			}
			if($("#ref_closed_sts").val()== 1)
			{
				alert('Reference Closed so Update / Delete is not Possible'); return;
			}
			
           if( form_validation('cbo_company_name*cbo_currency*cbo_supplier*txt_wo_date*cbo_pay_mode*cbo_wo_basis*txt_delivery_date*cbo_booking_type*cbo_payterm_id*cbo_source','Company*Currency*Supplier*WO Date*Pay Mode*WO Basis*Delivery Date*Booking Type*Pay Term*Source')==false )
			{
				return;
			}
			
			if($("#cbo_wo_basis").val()==3)
			{
				if( form_validation('cbo_team_leader','Team Leader')==false )
				{
					$("#cbo_team_leader").focus();
					return;
				}
				if( form_validation('cbo_dealing_merchant','Dealing Merchant')==false )
				{
					$("#cbo_dealing_merchant").focus();
					return;
				}
			}
			  
			// save data here
			var detailsData="";
			var wo_basis=$("#cbo_wo_basis").val();
			var row = $("#tbl_purchase_item tbody tr").length;
			var all_deleted_id="";
			for(var i=1;i<=row;i++)
			{		
					var proces_loss_methode_id = $('#process_loss_method_id_'+i).val();
					// alert(proces_loss_methode_ids);return;
					if(proces_loss_methode_id==2){
						var HiddenWoQnty=($('#HiddenWoQnty_'+i).val()*1);
						var HiddenPreCostQty=($('#HiddenPreCostQty_'+i).val()*1);
						// alert(HiddenPreCostQty+"__"+HiddenWoQnty);
						if(HiddenPreCostQty>HiddenWoQnty){
							alert("ADDISONAL NOT ALLOW PLZ FULL FILL WORK ORDER QTY");return;
						}
					}
				try
				{
					if($('#tr_'+i).css('display') == 'none')
					{
						if($('#upDtlsId_'+i).val() !="")
						{
							if(all_deleted_id=="") all_deleted_id +=$('#upDtlsId_'+i).val(); else all_deleted_id +=","+$('#upDtlsId_'+i).val();
						}
					}
					else
					{
						if(wo_basis==3)
						{
							if( form_validation('txtyarncolor_'+i+'*cbocount_'+i+'*cbocompone_'+i+'*cbotype_'+i+'*cbouom_'+i+'*reqqnty_'+i+'*txtrate_'+i+'*txtamount_'+i,'Color*Count*Yarn Type*UOM*Quantity*Rate*Amount')==false )
							{
								return;
							}
							
						}
						else
						{
							if(wo_basis==1)
							{
								if( form_validation('txtjobno_'+i+'*txtyarncolor_'+i+'*cbocount_'+i+'*cbocompone_'+i+'*cbotype_'+i+'*cbouom_'+i+'*reqqnty_'+i+'*txtrate_'+i+'*txtamount_'+i,'Job*Color*Count*composition*Yarn Type*UOM*Quantity*Rate*Amount')==false )
								{
									return;
								}
								
							}
							else
							{
								if($('#txtjobno_'+i).val()!="")
								{
									//alert(i);//return;
									if( form_validation('txtjobno_'+i+'*txtyarncolor_'+i+'*cbocount_'+i+'*cbocompone_'+i+'*cbotype_'+i+'*cbouom_'+i+'*reqqnty_'+i+'*txtrate_'+i+'*txtamount_'+i,'Job*Color*Count*composition*Yarn Type*UOM*Quantity*Rate*Amount')==false )
									{
										alert(i);return;
									}
								}
							}
						}
						if($('#reqqnty_'+i).val()>0)
						{
							detailsData+='*txtjobno_'+i+'*txtjobid_'+i+'*txtreq_'+i+'*txtreqdtlsid_'+i+'*cbobuyername_'+i+'*txtstyleno_'+i+'*txtyarncolor_'+i+'*cbocount_'+i+'*cbocompone_'+i+'*txtpacent_'+i+'*cbotype_'+i+'*cbouom_'+i+'*reqqnty_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtdeliveryStart_'+i+'*txtdeliveryEnd_'+i+'*upDtlsId_'+i;
						}
						//
					}
					
				}
				catch(err){}
			}
			//alert(detailsData); return;
			
			var is_approved=$('#is_approved').val();//approval requisition item Change not allowed
			if(is_approved==1)
			{
				alert("This Work Order is Approved. So Change Not Allowed");
				return;	
			}
			//
			var data="action=save_update_delete&operation="+operation+'&total_row='+row+'&all_deleted_id='+all_deleted_id+get_submitted_data_string('garments_nature*txt_wo_number*is_approved*update_id*cbo_company_name*cbo_currency*cbo_supplier*txt_wo_date*cbo_pay_mode*cbo_wo_basis*txt_requisition*txt_req_id*txt_delivery_date*cbo_booking_type*cbo_pi_issue_to*cbo_payterm_id*cbo_source*cbo_delivery_mode*txt_do_no*txt_tenor*cbo_inco_term*txt_inco_term_place*txt_attention*txt_remarks*cbo_team_leader*cbo_dealing_merchant*cbo_ready_to_approved'+detailsData,"../../");
			//alert(data);return;
			
			freeze_window(operation);
			http.open("POST","requires/sample_yarn_work_order_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_yarn_order_entry_reponse;
		}
	}

	function fnc_yarn_order_entry_reponse()
	{	
	
		$("#id_print_to_button").removeClass( "formbutton_disabled"); //To make disable print to button
		$("#id_print_to_button").addClass( "formbutton"); //To make enable print to button
		if(http.readyState == 4) 
		{
			//alert(http.responseText);release_freezing();return;  		
			var reponse=trim(http.responseText).split('**');
			show_msg(trim(reponse[0]));
			if(reponse[0]==0 || reponse[0]==1)
			{
				$("#txt_wo_number").val(reponse[1]);
				$("#update_id").val(reponse[2]);
				disable_enable_fields( 'cbo_company_name*cbo_wo_basis', 1, '', '' ); 
				//show_list_view(reponse[2],'show_dtls_listview_update','details_container','requires/sample_yarn_work_order_controller','');
				var dtls_html=return_global_ajax_value(reponse[2], 'show_dtls_listview_update', '', 'requires/sample_yarn_work_order_controller');
				if(dtls_html!="")
				{
					//$('#tr_'+row_no).remove();
					$('#details_part_list').html("");
					$('#details_part_list').append(dtls_html);
				}
				set_all_onclick();
				set_button_status(1, permission, 'fnc_yarn_order_entry',1);	
			}
			else if(reponse[0]==11)
			{
				alert(reponse[1]);release_freezing(); return;
			}
			release_freezing();
		}
	}
	
	function open_terms_condition_popup(page_link,title)
	{
		var txt_id_no=document.getElementById('update_id').value;
		if (txt_id_no=="")
		{
			alert("Save The Yarn Work Order First");
			return;
		}	
		else
		{
			page_link=page_link+get_submitted_data_string('update_id','../../');
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=370px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function(){};
		}
	}

	function openmypage_wo()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		
		var company = $("#cbo_company_name").val();
		var itemCategory = 1;
		var garments_nature = $("#garments_nature").val();
		var page_link = 'requires/sample_yarn_work_order_controller.php?action=wo_popup&company='+company+'&itemCategory='+itemCategory+'&garments_nature='+garments_nature;
		var title = "Work Order Search"; 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			freeze_window(5);
			var theform=this.contentDoc.forms[0];
			var hidden_wo_number=this.contentDoc.getElementById("hidden_wo_number").value.split("_"); 
			reset_form('yarnWorkOrder_1','','','','','cbo_currency');
			//reset_form('yarnWorkOrder_1','approved*details_container','','','','cbo_item_category*cbo_currency');
			$("#txt_wo_number").val(hidden_wo_number[0]);
			$("#update_id").val(hidden_wo_number[1]);
			//$("#hidden_pi_id").val(hidden_wo_number[3]);
			
			get_php_form_data(hidden_wo_number[1], "populate_data_from_search_popup", "requires/sample_yarn_work_order_controller" );
			disable_enable_fields( 'cbo_company_name*cbo_wo_basis', 1, '', '' );
			var dtls_html=return_global_ajax_value(hidden_wo_number[1], 'show_dtls_listview_update', '', 'requires/sample_yarn_work_order_controller');
			if(dtls_html!="")
			{
				//$('#tr_'+row_no).remove();
				$('#details_part_list').html("");
				$('#details_part_list').append(dtls_html);
			}
			set_button_status(1, permission, 'fnc_yarn_order_entry',1,1);
			set_all_onclick();		
			release_freezing();
		}
	}

 	function CompareDate(i) {
		var start=$("#txt_inhouse_date_"+i).val();
		var end= $("#txt_delivery_end_date_"+i).val();
		start=start.split('-');
		end=end.split('-');
		
		if(start[0]*1!=0 && end[0]*1!=0){
			var dateOne = new Date(start[2],start[1],start[0]); //Year, Month, Date
			var dateTwo = new Date(end[2],end[1],end[0]); //Year, Month, Date
			if (dateOne > dateTwo) {
				alert("End date not allowed less than Start date");
				$("#txt_delivery_end_date_"+i).val('');
			}
		}

    }
	
	
	function print_to_html_report(type)
	{
		var report_title=$( "div.form_caption" ).html();
					//alert(report_title);
		if(type == 1){	
        	window.open("requires/sample_yarn_work_order_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+type+'*'+$('#ref_closed_sts').val()+'&action='+"print_to_html_report", true );
        }
		else if(type == 4){
			if($('#update_id').val()=="")
			{
				alert("Save Data First.");return;
			}
			else
			{
				var rate_check=confirm("Generate Rate With Amount");
				if(rate_check==true)
				{
					var rate_amt=1;
				}
				else
				{
					var rate_amt=0;
				}
				//alert(rate_amt);return;
				window.open("requires/sample_yarn_work_order_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+type+'*'+$('#ref_closed_sts').val()+'*'+rate_amt+'&action='+"print_to_html_report4", true ); 
			}
        }else{
        	window.open("requires/sample_yarn_work_order_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+type+'*'+$('#ref_closed_sts').val()+'&action='+"print_to_html_report2", true );    
        }
    }
 
 
</script>	
<body onLoad="set_hotkey()">
<div align="center">
    <div style="width:1150px;"><? echo load_freeze_divs ("../../",$permission);  ?><br /></div>
        <fieldset style="width:1150px">
            <form name="yarnWorkOrder_1" id="yarnWorkOrder_1" method="" >
                <table cellpadding="0" cellspacing="2" width="900" align="center">
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right">WO Number</td>
                        <td><input type="text" name="txt_wo_number"  id="txt_wo_number" class="text_boxes" style="width:159px" placeholder="Double Click to Search" onDblClick="openmypage_wo('x','WO Number Search');" readonly />
                        <input type="hidden" name="is_approved" id="is_approved" value="">
                        <input type="hidden" name="update_id" id="update_id" value="">
                        </td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="100" class="must_entry_caption">Company</td>
                        <td width="170"><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "load_drop_down( 'requires/sample_yarn_work_order_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );print_button_setting()" ); ?>
                        </td>
                        <td width="100" class="must_entry_caption">Currency</td>
                        <td width="170"><? echo create_drop_down( "cbo_currency", 150, $currency,"", 1, "-- Select --", 2, "",0 ); ?></td>
                        <td width="100" class="must_entry_caption">Supplier</td>
                        <td id="supplier_td"><? echo create_drop_down( "cbo_supplier", 150, $blank_array,"", 1, "-- Select --", 0, "",0 ); ?></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">WO Date</td>
                        <td><input type="text" name="txt_wo_date" id="txt_wo_date" class="datepicker" style="width:140px"/></td>
                        <td class="must_entry_caption">Pay Mode</td>
                        <td><? echo create_drop_down( "cbo_pay_mode", 150, $pay_mode,"", 1, "-- Select --", 0, "",0 ); ?></td> 
                        <td class="must_entry_caption">WO Basis</td>
                        <td><? echo create_drop_down( "cbo_wo_basis", 150, $sample_wo_basis,"", 1, "-- Select --", 0, "fn_disable_enable(this.value);",0,'','','' ); ?></td>
                    </tr>
                    <tr>
                        <td>Requisition</td>
                        <td><input type="text" name="txt_requisition"  id="txt_requisition" class="text_boxes" style="width:140px" placeholder="Double Click To Search" onDblClick="openmypage_req()" readonly disabled />
                        <input type="hidden" name="txt_req_id"  id="txt_req_id" readonly disabled />
                        <input type="hidden" name="txt_req_dtls_id"  id="txt_req_dtls_id" readonly disabled />
                        </td>
                        <td class="must_entry_caption">Delivery Date</td>
                        <td><input type="text" name="txt_delivery_date"  id="txt_delivery_date" class="datepicker"  style="width:140px" /></td>
                        <td class="must_entry_caption">Booking Type</td>
                        <td>
						<?
						$nonOrder_booking_type = array(1 => "Aditional", 2=>"Compensative"); 
						echo create_drop_down( "cbo_booking_type", 150, $nonOrder_booking_type,"", 1, "-- Select --", 0, "",0 ); 
						?>
                        </td>
                    </tr>
                    <tr>
                        <td>PI issue To</td>
                        <td><? echo create_drop_down( "cbo_pi_issue_to", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "" ); ?></td>
                        <td class="must_entry_caption">Pay Term</td>
                        <td><?php echo create_drop_down( "cbo_payterm_id",150,$pay_term,'',1,'-Select-',0,"",0,'');//set_port_loading_value(this.value)1,2 ?></td> 
                        <td class="must_entry_caption">Source</td>
                        <td><? echo create_drop_down( "cbo_source", 150, $source,"", 1, "-- Select --", 0, "",0 ); ?></td>
                    </tr>
                    <tr>
                    	<td>Delivery Mode</td>
                        <td><? echo create_drop_down( "cbo_delivery_mode", 150, $shipment_mode,"", 1, "-- Select --", 0, "" ); ?></td>
                        <td>D/O No.</td>
                        <td><input type="text" name="txt_do_no"  id="txt_do_no" style="width:140px " class="text_boxes" /></td>
                        <td>Tenor</td>
                        <td><input type="text"  name="txt_tenor" style="width:140px" id="txt_tenor" class="text_boxes_numeric" /></td>
                    </tr>
                    <tr>
                    	<td>Inco Term</td>
                        <td><?php echo create_drop_down("cbo_inco_term", 150, $incoterm, "", 0, "", 0, ""); ?></td>
                        <td>Inco Term Place</td>
                        <td><input type="text" name="txt_inco_term_place" style="width:140px" id="txt_inco_term_place" class="text_boxes" /></td>
                        <td>Attention</td>
                        <td><input type="text" name="txt_attention"  id="txt_attention" style="width:140px " class="text_boxes" /></td>
                    </tr>
					<tr>
                        <td class="leader_td_label">Team Leader</td>
                        <td id="leader_td"><? echo create_drop_down( "cbo_team_leader", 150, "select id,team_leader_name from lib_marketing_team where project_type=6 and status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "load_drop_down( 'requires/sample_yarn_work_order_controller', this.value, 'cbo_dealing_merchant', 'div_marchant' );" );
                        ?></td>
                        <td class="merchant_td_label">Dealing Merchant</td>
                        <td id="div_marchant"><? echo create_drop_down( "cbo_dealing_merchant", 150, $blank_array,"", 1, "-- Select Dealing Merchant --", $selected, "" ); ?></td>
                        <td>Ready to Approve</td>
                        <td><? echo create_drop_down( "cbo_ready_to_approved", 150, $yes_no,"", 1, "-- Select--", 2, "","","" ); ?></td>
                    </tr>
                    <tr>
                        <td>Remarks</td>
                        <td  colspan="2"><input type="text" name="txt_remarks"  id="txt_remarks" style="width:140px" class="text_boxes" /></td>
                        <td>&nbsp;</td>
                        <td align="center" colspan="2">
                        	<?
								include("../../terms_condition/terms_condition.php");
								terms_condition(284,'update_id','../../');
                            ?>
                        	<!--<input type="button" id="set_button" class="image_uploader" style="width:100px; margin-left:30px; margin-top:2px;" value="Terms Condition" onClick="open_terms_condition_popup('requires/sample_yarn_work_order_controller.php?action=terms_condition_popup','Terms Condition')" />-->
                        </td>
                    </tr>
                    <tr style="display:none">
                        <td colspan="6"><p id="ref_closed_msg_id" style="font-size:16px; font-weight:bold; color:red;"></p>
                        	<input type="hidden"  name="ref_closed_sts" style="width:159px" id="ref_closed_sts" class="text_boxes_numeric" />
                        </td>
                    </tr>
                </table>
                <br/>
                <div style="width:1170px" id="details_container" align="left">
                <table class="rpt_table" width="1170" cellspacing="0" cellpadding="0" id="tbl_purchase_item" border="1" rules="all">
                    <thead>
                        <th width="110" id="job_no_td" class="must_entry_caption">Job No</th>
                        <th width="90">Buyer Name</th>
                        <th width="85">Style</th>
                        <th width="85">Yarn Color</th>
                        <th width="70">Count</th>
                        <th width="100">Composition</th>
                        <th width="40">%</th>
                        <th width="100">Yarn Type</th>
                        <th width="60">UOM</th>
                        <th width="55" class="must_entry_caption">WO Qty.</th>
                        <th width="40">WO Rate</th>
                        <th width="55">WO Amount</th>
                        <th width="65">Delivery Start Date</th>
                        <th width="65">Delivery End Date</th>
                        <th> </th>
                    </thead>
                    <tbody id="details_part_list">
                        <tr class="general" id="tr_1">
                            <td align="center">
                            <input type="text" id="txtjobno_1" name="txtjobno[]" class="text_boxes" value="" style="width:90px;" onDblClick="openmypage_job(1)" placeholder="Doble Click For Job" readonly />
                           
                            <input type="hidden" id="txtjobid_1" name="txtjobid[]" />
                            <input type="hidden" id="txtreq_1" name="txtreq[]" />
                            <input type="hidden" id="txtreqdtlsid_1" name="txtreqdtlsid[]" />
                            <input type="hidden" name="txtrowid[]" id="txtrowid_1" />
                            <input type="hidden" name="upDtlsId[]" id="upDtlsId_1" />
							<input type="hidden" name="process_loss_method_id[]" id="process_loss_method_id_1"/>
                            </td>
                            <td id="buy_td">
							<?
							   	echo create_drop_down( "cbobuyername_1", 90, "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select --", 0, "",1,"","","","","","","cbobuyername[]" );
 							?>
                            </td>
                            <td align="center"><input type="text" name="txtstyleno[]" id="txtstyleno_1" class="text_boxes" value="" style="width:75px;" readonly disabled /></td>
                            <td align="center"><input type="text" name="txtyarncolor[]" id="txtyarncolor_1" class="text_boxes" value="" style="width:75px;" onFocus="add_auto_complete( 1 )" />
								<input type="hidden" id="hidden_txtyarncolor_id" readonly/>
							</td>
                            <td align="center">
                            <? 
								echo create_drop_down( "cbocount_1", 70, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1,"-- Select --", 0, "",0,"","","","","","","cbocount[]" ); 
							?>
                            </td> 
                            <td align="center">
                            <?  
								echo create_drop_down( "cbocompone_1", 100, $composition,"", 1, "-- Select --", 0, "",0,"","","","","","","cbocompone[]"); 
							?>
                            </td> 
                            <td><input type="text" id="txtpacent_1" name="txtpacent[]"  class="text_boxes" value="100" style="width:40px;" /></td>
                            <td>
							<?  
								echo create_drop_down( "cbotype_1", 100, $yarn_type,"", 1, "-- Select --", $row[csf("type_id")], "",$disabled,"","","","","","","cbotype[]" ); 
							?>
                            </td>
                            <td>
                            <? 
								echo create_drop_down( "cbouom_1", 60, $unit_of_measurement,"", 1, "-- Select--", 15, "",1,"","","","","","","cbouom[]"); 
							?>
                            </td>
                            <td>
                                <input type="text" id="reqqnty_1" name="reqqnty[]" class="text_boxes_numeric" style="width:50px" onKeyUp="calculate_amount(1)" />
                            	<input type="hidden" id="hiddenreqqnty_1" name="hiddenreqqnty[]" value=""/>
								<input type="hidden" id="HiddenWoQnty_<? echo $i; ?>" name="HiddenWoQnty[]" value=""/>
           						<input type="hidden" id="HiddenPreCostQty_<? echo $i; ?>" name="HiddenPreCostQty[]" value=""/>
                            </td>
                            <td>
                            	<input type="text" name="txtrate[]" id="txtrate_1" class="text_boxes_numeric" value="" style="width:40px;" onKeyUp="calculate_amount(1)" />
                            	<input type="hidden" id="hiddentxtrate_1" name="hiddentxtrate[]" value=""/>
                            </td>
                            <td><input type="text" name="txtamount[]" id="txtamount_1" class="text_boxes_numeric" value="" style="width:55px;" readonly /></td>
                            <td><input class="datepicker" type="text" style="width:60px;" name="txtdeliveryStart[]" id="txtdeliveryStart_1" placeholder="Select Date" /></td>	
                            <td><input class="datepicker" type="text" style="width:60px;" name="txtdeliveryEnd[]" id="txtdeliveryEnd_1" placeholder="Select Date" /></td>
                            <td>
                            <input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1);" />
                            <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(1);" />
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>
                <table cellpadding="0" cellspacing="2" width="100%">
                    <tr>
                        <td align="center" colspan="6" valign="middle" class="button_container"><div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
                        <? echo load_submit_buttons( $permission, "fnc_yarn_order_entry", 0,0 ,"reset_form('yarnWorkOrder_1','approved*details_container','','','','cbo_item_category*cbo_currency');$('#cbo_company_name').attr('disabled',false);$('#cbo_wo_basis').attr('disabled',false);$('#ref_closed_msg_id').html('');",1); ?>
                        <span id="button_data_panel"></span>                      
                        <!-- <input type="button" style="width:80px;" id="id_print_to_button" onClick="print_to_html_report(4)" class="formbutton" name="id_print_to_button" value="Print4" /> -->
                        </td>
                        <td>&nbsp;</td>				
                    </tr>
                </table> 
            </form>
        </fieldset>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>