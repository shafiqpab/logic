<?
/*-------------------------------------------- Comments
Purpose			: 	Yarn Work order entry
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	22-04-17
Updated by 		: 	Kausar	(Creating Report)		
Update date		: 	13-02-2017	  	   
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
echo load_html_head_contents("Yarn Work Order","../../", 1, 1, $unicode,1,''); 

$color_sql = sql_select("select id,color_name from lib_color order by id");
$color_name = "";
foreach($color_sql as $result)
{ 
	$color_name.= "{value:'".$result[csf('color_name')]."',id:".$result[csf('id')]."},";
}
 
?> 
<script>
	var permission='<? echo $permission; ?>';
	var req_variable_setting='<? echo $req_variable_setting; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	function fn_disable_enable(str)
	{
		if(str==3)
		{
			$("#txt_buyer_po_no").attr("disabled",false);
			$("#txt_req_id").val('');
			$("#txt_req_dtls_id").val('');
			$("#txt_requisition").val('');
			$("#txt_requisition").attr("disabled",true);
		}
		else if(str==1)
		{
			$("#txt_requisition").attr("disabled",false);
			$("#txt_buyer_po_no").val('');
			$("#txt_buyer_po").val('');
			$("#txt_job_selected").val('');
			$("#txt_buyer_po_no").attr("disabled",true);
		}
		else
		{
			$("#txt_buyer_po_no").val('');
			$("#txt_buyer_po").val('');
			$("#txt_job_selected").val('');
			$("#txt_buyer_po_no").attr("disabled",true);
			$("#txt_requisition").attr("disabled",true);
		}
	}
 
	// for buyer po
	function openmypage()
	{
		var company = $("#cbo_company_name").val();
		var garments_nature = $("#garments_nature").val(); 
		var txt_buyer_po_no = $("#txt_buyer_po_no").val(); // if value has then it will be selected
		var txt_buyer_po = $("#txt_buyer_po").val(); // if value has then it will be selected
		var txt_job_selected = $("#txt_job_selected").val();
		var cbo_wo_basis = $("#cbo_wo_basis").val();
		
		var page_link = 'requires/yarn_work_order_controller.php?action=order_popup&company='+company+'&garments_nature='+garments_nature+'&txt_buyer_po_no='+txt_buyer_po_no+'&txt_buyer_po='+txt_buyer_po+'&txt_job_selected='+txt_job_selected+'&cbo_wo_basis='+cbo_wo_basis;
		var title = "Order Search"; 
		
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var break_down_id=this.contentDoc.getElementById("txt_selected_id").value; //break down id here
			var po_number=this.contentDoc.getElementById("txt_selected").value; // po_number
			var job_number=this.contentDoc.getElementById("txt_selected_job").value; // job_number
			$("#txt_buyer_po_no").val(po_number); 
			$("#txt_buyer_po").val(break_down_id);
			$("#txt_job_selected").val(job_number);
			
			if(break_down_id!="")
			{			
				freeze_window(5);			
				show_list_view(break_down_id+'***'+job_number,'show_dtls_listview','details_container','requires/yarn_work_order_controller','');
				release_freezing();
			}
			else
			{
				$("#details_container").html('');
			}
			
			var update_id=$("#update_id").val();
			if(update_id!="")
			{
				var delID=return_global_ajax_value( update_id, 'previous_dtls_id', '', 'requires/yarn_work_order_controller');//For Buyer Po Changed
				$("#txt_delete_row").val(delID);
			}
			
		}
	}
	
	function openmypage_req()
	{
		var company = $("#cbo_company_name").val();
		var garments_nature = $("#garments_nature").val(); 
		var txt_req_id = $("#txt_req_id").val();
		var txt_req_dtls_id = $("#txt_req_dtls_id").val();
		var cbo_wo_basis = $("#cbo_wo_basis").val();
		
		var page_link = 'requires/yarn_work_order_controller.php?action=requisition_popup&company='+company+'&garments_nature='+garments_nature+'&txt_req_id='+txt_req_id+'&txt_req_dtls_id='+txt_req_dtls_id+'&cbo_wo_basis='+cbo_wo_basis;
		
		var title = "Requisition Search"; 
		
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1250px,height=390px,center=1,resize=1,scrolling=0','../')
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
			if(dtls_id!="")
			{			
				freeze_window(5);			
				show_list_view(dtls_id+'***'+mst_id+'***'+update_id,'show_req_dtls_listview','details_container','requires/yarn_work_order_controller','');
				release_freezing();
			}
			else
			{
				$("#details_container").html('');
			}
			
			if(update_id!="")
			{
				var delID=return_global_ajax_value( update_id, 'previous_dtls_id', '', 'requires/yarn_work_order_controller');//For Buyer Po Changed
				$("#txt_delete_row").val(delID);
			}
			
		}
	}

	function control_composition(id,td,type)
	{
		var cbocompone=(document.getElementById('cbocompone_'+id).value);
		var cbocomptwo=(document.getElementById('cbocomptwo_'+id).value);
		var percentone=(document.getElementById('percentone_'+id).value)*1;
		var percenttwo=(document.getElementById('percenttwo_'+id).value)*1;
		var row_num=$('#tbl_yarn_cost tr').length-1;
		
		if(type=='percent_one' && percentone>100)
		{
			alert("Greater Than 100 Not Allwed");
			document.getElementById('percentone_'+id).value="";
		}
		
		if(type=='percent_one' && percentone<=0)
		{
			alert("0 Or Less Than 0 Not Allwed")
			document.getElementById('percentone_'+id).value="";
			document.getElementById('percentone_'+id).disabled=true;
			document.getElementById('cbocompone_'+id).value=0;
			document.getElementById('cbocompone_'+id).disabled=true;
			document.getElementById('percenttwo_'+id).value=100;
			document.getElementById('percenttwo_'+id).disabled=false;
			document.getElementById('cbocomptwo_'+id).disabled=false;
		}
		if(type=='percent_one' && percentone==100)
		{
			document.getElementById('percenttwo_'+id).value="";
			document.getElementById('cbocomptwo_'+id).value=0;
			document.getElementById('percenttwo_'+id).disabled=true;
			document.getElementById('cbocomptwo_'+id).disabled=true;
		}
		
		if(type=='percent_one' && percentone < 100 && percentone > 0 )
		{
			document.getElementById('percenttwo_'+id).value=100-percentone;
			document.getElementById('percenttwo_'+id).disabled=false;
			document.getElementById('cbocomptwo_'+id).disabled=false;
		}
		
		if(type=='comp_one' && cbocompone==cbocomptwo  )
		{
			alert("Same Composition Not Allowed");
			document.getElementById('cbocompone_'+id).value=0;
		}
		
		if(type=='percent_two' && percenttwo>100)
		{
			alert("Greater Than 100 Not Allwed")
			document.getElementById('percenttwo_'+id).value="";
		}
		if(type=='percent_two' && percenttwo<=0)
		{
			alert("0 Or Less Than 0 Not Allwed")
			document.getElementById('percenttwo_'+id).value="";
			document.getElementById('percenttwo_'+id).disabled=true;
			document.getElementById('cbocomptwo_'+id).value=0;
			document.getElementById('cbocomptwo_'+id).disabled=true;
			document.getElementById('percentone_'+id).value=100;
			document.getElementById('percentone_'+id).disabled=false;
			document.getElementById('cbocompone_'+id).disabled=false;
		}
		if(type=='percent_two' && percenttwo==100)
		{
			document.getElementById('percentone_'+id).value="";
			document.getElementById('cbocompone_'+id).value=0;
			document.getElementById('percentone_'+id).disabled=true;
			document.getElementById('cbocompone_'+id).disabled=true;
		}
		
		if(type=='percent_two' && percenttwo<100 && percenttwo>0)
		{
			document.getElementById('percentone_'+id).value=100-percenttwo;
			document.getElementById('percentone_'+id).disabled=false;
			document.getElementById('cbocompone_'+id).disabled=false;
		}
		
		if(type=='comp_two' && cbocomptwo==cbocompone)
		{
			alert("Same Composition Not Allowed");
			document.getElementById('cbocomptwo_'+id).value=0;
		}
	}

	function calculate_yarn_consumption_ratio(i,precost_rate)
	{
		var rate=$('#txt_rate_'+i).val()*1;
		var basis=$('#txt_requ_basis_'+i).val()*1;
		var wo_basis=$('#cbo_wo_basis').val()*1;
		
		var txt_job_id=$('#txt_job_id_'+i).val()*1;
		var txt_job=$('#txt_job_'+i).val()*1;
		var txt_requ_rate=$('#txt_requ_rate_'+i).val()*1;
		var pre_cost_rate=$('#txt_pre_cost_rate_'+i).val()*1;
		//alert(txt_job_id +"**"+basis+"**"+req_variable_setting);
		
		/*if(rate>precost_rate && wo_basis==1 && txt_job != ""  && ( basis==1 || basis==0) && req_variable_setting !=1 ){
			alert("Yarn rate not allow over the pre-costing. Note: Pre Cost Rate:"+precost_rate);
			$('#txt_rate_'+i).val(precost_rate);
		} 
		 

		if(rate>txt_requ_rate && wo_basis==1 && txt_job == "" && req_variable_setting !=1 ){
			alert("Yarn rate not allow over the Requision. Note: Requision Rate:"+txt_requ_rate);
			$('#txt_rate_'+i).val(txt_requ_rate);
		} 

		
		if(rate>precost_rate && wo_basis==3 && req_variable_setting !=1){
			alert("Yarn rate not allow over the pre-costing. Note: Pre Cost Rate:"+precost_rate);
			$('#txt_rate_'+i).val(precost_rate);
		}
		
		if(rate>precost_rate && (wo_basis==1 || wo_basis==3)){
			alert("Yarn rate not allow over the pre-costing. Note: Pre Cost Rate:"+precost_rate);
			$('#txt_rate_'+i).val(precost_rate);
		}*/

		if(rate>pre_cost_rate && wo_basis==1 ){
			alert("Yarn rate not allow over the pre-costing. Note: Pre Cost Rate: "+pre_cost_rate);
			$('#txt_rate_'+i).val(pre_cost_rate);
		}
		
		
		var txt_req_qnty=$('#txt_req_qnty_'+i).val()*1;

		var cbocount=$('#txt_quantity_'+i).val()*1;
		// if(txt_req_qnty<cbocount)
		if((Math.floor(txt_req_qnty/50)*50+50)<cbocount)
		{
			// alert('WO quantity must be less than or equal Requision Quantity');
			alert('WO quantity must be less than or equal to round 50 of Requision Quantity');
			$('#txt_quantity_'+i).val(0);
			$('#txt_quantity_'+i).focus();
			
		}
		var cbocompone=$('#txt_rate_'+i).val();
		var amount = cbocount*1*cbocompone*1;
		$('#txt_amount_'+i).val(amount);
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

	function fn_inc_decr_row(rowid,type,pi_id)
	{
		
		if(type=="increase")
		{ 
			var row = $("#tbl_details tbody tr:last").attr('id'); 			
			var valuesLastRow = $("#tbl_details tbody tr:last").find('input[name=txt_color_'+row+']').val(); 	
			if(valuesLastRow!="")
			{
				row = row*1+1;
				var responseHtml = return_ajax_request_value(row, 'append_load_details_container', 'requires/yarn_work_order_controller');
				$("#tbl_details tbody").append(responseHtml);
				set_all_onclick(); 
			}
		}
		else if(type=="decrease")
		{
			
			var txtPiID = $("#txtPiID_"+rowid).val();
			//alert(pi_id);
			//if(txtPiID!="")
			// {
			// 	alert("Already add in PI No.(PI ID ="+txtPiID+"). Delete/Remove is not possible.");
			// 	return;
			// }
			if(pi_id>0)
			{
				alert("Already add in PI No.(PI ID ="+pi_id+"). Delete/Remove is not possible.");
				return;
			}
			else{
				var row = $("#tbl_details tr").length-1;
				if(rowid*1!="" && row*1>1)
				{ 								 
					var vals = $("#txt_delete_row").val();
					var delID = $("#txt_row_id_"+rowid).val();
					if(vals!="")
						$("#txt_delete_row").val(vals+','+delID);
					else
						$("#txt_delete_row").val(delID);				
					if($("#hidden_pi_id").val()==""){
						$("#tbl_details tr#"+rowid).remove();
					}
				}	
						
			}				
		}
	}

	function colorName(rowID)
	{
		$("#hidden_colorID_"+rowID).val('');
		$(function() {
			var color_name = [<? echo substr($color_name, 0, -1); ?>]; 
			$("#txt_color_"+rowID).autocomplete({
				source: color_name,
				select: function (event, ui) {
					$("#txt_color_"+rowID).val(ui.item.value); // display the selected text
					$("#hidden_colorID_"+rowID).val(ui.item.id); // save selected id to hidden input
					fn_copy_color(rowID);
				} 
			});
		});
	}

	function fn_copy_color(i)
	{
		var colorName = $("#txt_color_"+i).val();
		var colorID = $("#hidden_colorID_"+i).val();
		var rowCount = $('#tbl_details tr').length-1;
		for(var j=i; j<=rowCount; j++)
		{
			try
			{
				$("#txt_color_"+j).val(colorName);
				$("#hidden_colorID_"+j).val(colorID);	 	
			}
			catch(err)
			{
			//do nothing
			}
		}
	}
	
    function print_to_html_report(type, data_str, is_mail_send)
	{
		var report_title=$( "div.form_caption" ).html();
		//alert(data_str);
		if(type == 1){	
        	window.open("requires/yarn_work_order_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+type+'*'+$('#ref_closed_sts').val()+'&data_str='+data_str+'&is_mail_send='+is_mail_send+'&action='+"print_to_html_report", true );
        }
		else if(type == 4)
		{
			var rate_amount='';
			var r=confirm("Press  \"OK\"  to open with rate and amount\nPress  \"Cancel\"  to open without rate and amount");
			if (r==true) rate_amount="1"; else rate_amount="0";
			// alert(rate_amount);return;
        	window.open("requires/yarn_work_order_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+type+'*'+$('#ref_closed_sts').val()+'*'+rate_amount+'&data_str='+data_str+'&is_mail_send='+is_mail_send+'&action='+"print_to_html_report4", true );    
        }
		else if(type == 5){
			window.open("requires/yarn_work_order_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+type+'*'+$('#ref_closed_sts').val()+'&data_str='+data_str+'&is_mail_send='+is_mail_send+'&action='+"print_to_html_report5", true );    
		}
        else
        {
        	window.open("requires/yarn_work_order_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+type+'*'+$('#ref_closed_sts').val()+'&data_str='+data_str+'&is_mail_send='+is_mail_send+'&action='+"print_to_html_report2", true );
        }
    }
	
	function fnc_yarn_order_entry(operation, data_str, is_mail_send)
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#ref_closed_sts').val()+'*'+data_str +'*'+ is_mail_send,"yarn_work_order_print", "requires/yarn_work_order_controller")
			return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{ 

			if($("#ref_closed_sts").val()== 1)
			{
				alert('Reference Closed so Update / Delete is not Possible'); return;
			}
            if($("#cbo_payterm_id").val()== 2){
				if( form_validation('cbo_company_name*cbo_item_category*cbo_supplier*txt_wo_date*cbo_currency*cbo_wo_basis*cbo_pay_mode*cbo_source*txt_delivery_date*txt_tenor*cbo_payterm_id','Company Name*Item Category*Supplier Name*WO Date*Currency*WO Basis*Pay Mode*Source*Delivery Date*Tenor*Pay Term')==false )
				{
					return;
				}
			}else{
				if( form_validation('cbo_company_name*cbo_item_category*cbo_supplier*txt_wo_date*cbo_currency*cbo_wo_basis*cbo_pay_mode*cbo_source*txt_delivery_date*cbo_payterm_id','Company Name*Item Category*Supplier Name*WO Date*Currency*WO Basis*Pay Mode*Source*Delivery Date*Pay Term')==false )
				{
					return;
				}
			}    
			if($("#cbo_wo_basis").val()==3 && form_validation('txt_buyer_po','Buyer PO')==false ) //buyer po basis
			{
				return;
			}
			try
			{
				var row = $("#tbl_details tbody tr:last").attr('id');
				if(row<=0) throw "Save Not Possible!!Input Item Details For Save";
			}
			catch(err)
			{
				alert("Error : "+err);
				return;
			}
			
			// save data here
			var wo_basis=$("#cbo_wo_basis").val();
			if(wo_basis==1)
			{
				var detailsData="";
				for(var i=1;i<=row;i++)
				{
					try
					{ 
						if( form_validation('txt_color_'+i+'*cbocount_'+i+'*cbotype_'+i+'*cbo_uom_'+i+'*txt_quantity_'+i+'*txt_rate_'+i+'*txt_amount_'+i,'Color*Count*Yarn Type*UOM*Quantity*Rate*Amount')==false )
						{
							return;
						}
	
						if( $("#txt_quantity_"+i).val()*1 <= 0 || $("#txt_rate_"+i).val()*1 <= 0 )
						{
							alert("Quantity OR Rate Can not be 0 or less than 0");
							$("#txt_quantity_"+i).focus();
							return;
						}
									  
						detailsData+='*txt_req_'+i+'*txt_req_dtls_id_'+i+'*txt_job_'+i+'*txt_job_id_'+i+'*txt_buyer_id_'+i+'*txt_style_'+i+'*txt_po_brakdown_id_'+i+'*txt_color_'+i+'*hidden_colorID_'+i+'*cbocount_'+i+'*cbocompone_'+i+'*percentone_'+i+'*cbocomptwo_'+i+'*percenttwo_'+i+'*cbotype_'+i+'*cbo_uom_'+i+'*txt_req_qnty_'+i+'*txt_quantity_'+i+'*txt_rate_'+i+'*txt_amount_'+i+'*txt_inhouse_date_'+i+'*txt_delivery_end_date_'+i+'*txt_row_id_'+i+'*txt_remarks_'+i+'*txt_number_of_lot_'+i+'*txt_Lab_Dip_Aprrov_Shade_'+i;
					}
					catch(err){}
				}
			}
			else
			{
				var detailsData="";
				for(var i=1;i<=row;i++)
				{
					try
					{ 
						if( form_validation('txt_color_'+i+'*cbocount_'+i+'*cbotype_'+i+'*cbo_uom_'+i+'*txt_quantity_'+i+'*txt_rate_'+i+'*txt_amount_'+i+'*percentone_'+i,'Color*Count*Yarn Type*UOM*Quantity*Rate*Amount*Percentage')==false )
						{
							return;
						}
	
						if( $("#txt_quantity_"+i).val()*1 <= 0 || $("#txt_rate_"+i).val()*1 <= 0 )
						{
							alert("Quantity OR Rate Can not be 0 or less than 0");
							$("#txt_quantity_"+i).focus();
							return;
						}
									  
						detailsData+='*txt_po_brakdown_id_'+i+'*txt_color_'+i+'*hidden_colorID_'+i+'*cbocount_'+i+'*cbocompone_'+i+'*percentone_'+i+'*cbocomptwo_'+i+'*percenttwo_'+i+'*cbotype_'+i+'*cbo_uom_'+i+'*txt_quantity_'+i+'*txt_rate_'+i+'*txt_amount_'+i+'*txt_inhouse_date_'+i+'*txt_delivery_end_date_'+i+'*txt_row_id_'+i+'*txt_remarks_'+i+'*txt_number_of_lot_'+i+'*txt_Lab_Dip_Aprrov_Shade_'+i;
					}
					catch(err){}
				}
			}
			
			var is_approved=$('#is_approved').val();//approval requisition item Change not allowed
			if(is_approved==1)
			{
				alert("This Work Order is Approved. So Change Not Allowed");
				return;	
			}

			var data="action=save_update_delete&operation="+operation+'&total_row='+row+get_submitted_data_string('garments_nature*update_id*txt_wo_number*cbo_company_name*cbo_item_category*cbo_supplier*txt_wo_date*cbo_currency*cbo_wo_basis*cbo_pay_mode*cbo_source*txt_delivery_date*txt_attention*txt_buyer_po*txt_delete_row*txt_buyer_name*txt_style*txt_do_no*txt_remarks*cbo_payterm_id*txt_tenor*cbo_inco_term*cbo_pi_issue_to*cbo_ready_to_approved*txt_inco_term_place*cbo_delivery_mode'+detailsData,"../../");
			//alert(data);return;
			
			freeze_window(operation);
			http.open("POST","requires/yarn_work_order_controller.php",true);
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
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			{
				$("#txt_wo_number").val(reponse[1]);
				$("#update_id").val(reponse[2]);
				disable_enable_fields( 'cbo_company_name*cbo_currency*cbo_wo_basis', 1, '', '' ); 
				show_list_view(reponse[2]+'****'+reponse[4],'show_dtls_listview_update','details_container','requires/yarn_work_order_controller','');
				set_button_status(1, permission, 'fnc_yarn_order_entry',1);	
			}
			else if(reponse[0]==11)
			{
				alert(reponse[1]);release_freezing(); return;
				/*if(reponse[2]>0)
				{
					show_list_view(reponse[2],'show_dtls_listview_update','details_container','requires/stationary_work_order_controller','');
				}*/
				
			}
			release_freezing();
			//$("#id_print_to_button").removeClass( "formbutton_disabled"); //To make disable print to button
			//$("#id_print_to_button").addClass( "formbutton"); //To make enable print to button
			$(".printReport").removeClass( "formbutton_disabled");//To make disable print to button
			$(".printReport").addClass( "formbutton");//To make enable print to button
			//reset_form('yarnWorkOrder_1','details_container','','','','cbo_item_category*cbo_currency');
		}
	}

	function openmypage_wo()
	{
		if( form_validation('cbo_company_name*cbo_item_category','Company Name*Item Category')==false )
		{
			return;
		}
		
		var company = $("#cbo_company_name").val();
		var itemCategory = $("#cbo_item_category").val();
		var garments_nature = $("#garments_nature").val();
		var page_link = 'requires/yarn_work_order_controller.php?action=wo_popup&company='+company+'&itemCategory='+itemCategory+'&garments_nature='+garments_nature;
		var title = "Order Search"; 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			freeze_window(5);
			var theform=this.contentDoc.forms[0];
			var hidden_wo_number=this.contentDoc.getElementById("hidden_wo_number").value.split("_"); 
			reset_form('yarnWorkOrder_1','details_container','','','','cbo_item_category*cbo_currency');
			//reset_form('yarnWorkOrder_1','approved*details_container','','','','cbo_item_category*cbo_currency');
			$("#txt_wo_number").val(hidden_wo_number[0]);
			$("#update_id").val(hidden_wo_number[1]);
			//$("#hidden_pi_id").val(hidden_wo_number[3]);
			
			get_php_form_data(hidden_wo_number[1], "populate_data_from_search_popup", "requires/yarn_work_order_controller" );
			disable_enable_fields( 'cbo_company_name*cbo_currency*cbo_wo_basis', 1, '', '' );
			show_list_view(hidden_wo_number[1]+'****'+hidden_wo_number[2],'show_dtls_listview_update','details_container','requires/yarn_work_order_controller','');
			set_button_status(1, permission, 'fnc_yarn_order_entry',1,1);		
			release_freezing();
			//$("#id_print_to_button").removeClass( "formbutton_disabled"); //To make disable print to button
			//$("#id_print_to_button").addClass( "formbutton"); //To make enable print to button
			$(".printReport").removeClass( "formbutton_disabled"); //To make disable print to button
			$(".printReport").addClass( "formbutton"); //To make enable print to button
			buyer_style(document.getElementById('cbo_wo_basis').value);
			//document.getElementById('rate_copy').checked=true;
		}
	}
	
	function buyer_style(val)
	{
		//alert (val)
		if (val==2)
		{
			$('#show_texttxt_buyer_name').attr('disabled',false);
			document.getElementById('txt_style').disabled=false;
		}
		else
		{
			$('#txt_buyer_name').val("");
			$('#show_texttxt_buyer_name').val("Select Multiple Item");
			$('#show_texttxt_buyer_name').attr('disabled',true);
			//document.getElementById('txt_buyer_name').value='';
			document.getElementById('txt_style').value='';
			document.getElementById('txt_style').disabled=true;
		}
	}
	
	function fn_view(row_id)
	{
		
		if( form_validation('cbo_company_name*cbocount_'+row_id+'*cbocompone_'+row_id+'*cbotype_'+row_id,'Company*Count*Composition*Yarn Type')==false )
		{
			return;
		}
		//alert(row_id);
		var company = $("#cbo_company_name").val();
		var yarn_count=$('#cbocount_'+row_id).val();
		var yarn_comp=$('#cbocompone_'+row_id).val();
		var yarn_type=$('#cbotype_'+row_id).val();
		
		var page_link = 'requires/yarn_work_order_controller.php?action=stock_popup&cbo_company_name='+company+'&yarn_count='+yarn_count+'&yarn_comp='+yarn_comp+'&yarn_type_id='+yarn_type;
		var title = "Current Stock Details"; 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1350px,height=480px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
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
 
    function print_button_setting()
	{
		//$('#button_data_panel').html('');
		get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/yarn_work_order_controller' ); 
	}

	function fnc_copy_rate(value,i)
	{
		var colorName=$("#txt_color_"+i).val();
		var count_id=$("#cbocount_"+i).val();
		var compo_id=$("#cbocompone_"+i).val();
		var rowCount = $('#tbl_details tbody tr').length;
		var rate_copy=$('input[name="rate_copy"]:checked').val()*1;

		for(var j=i; j<=rowCount; j++)
		{
			if(rate_copy==1)
			{
				$("#txt_rate_"+j).val( value );
			}
			else if(rate_copy==2)
			{
				if( colorName==$("#txt_color_"+j).val() ) $("#txt_rate_"+j).val( value );
			}
			else if(rate_copy==3)
			{
				if( count_id==$("#cbocount_"+j).val() ) $("#txt_rate_"+j).val( value );
			}
			else if(rate_copy==4)
			{
				if( compo_id==$("#cbocompone_"+j).val() )$("#txt_rate_"+j).val( value );
			}
			calculate_yarn_consumption_ratio( j, value);
		}
	}


	function call_print_button_for_mail(mail_address,mail_body,type){
		get_php_form_data( document.getElementById('cbo_company_name').value+'____'+mail_address+'____'+mail_body+'____'+type, "get_first_selected_print_report", "requires/yarn_work_order_controller" );
	}

	function openmypage_work_order()
	{	
		if( form_validation('cbo_company_name*update_id*txt_wo_number','Company*update_id* WO No')==false )
		{
			return;
		}
		var wo_id=$("#update_id").val();
		var company_id=$("#cbo_company_name").val();
		var wo_no=$("#txt_wo_number").val();
		var wo_basis=$("#cbo_wo_basis").val();
		var supplier=$("#cbo_supplier").val();
		var title="Work Or. Details";

		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', '../../approval/requires/yarn_work_order_approval_sweater_v2_controller.php?action=wo_details&wo_id='+wo_id+'&company_id='+company_id+'&wo_no='+wo_no+'&wo_basis='+wo_basis+'&supplier='+supplier, title, 'width=1200px,height=350px,center=1,resize=0,scrolling=0','../../');
		emailwindow.onclose=function()
		{
		}
	} 
	
</script>	
<body onLoad="set_hotkey()">
<div align="center">
    <div style="width:1150px;"><? echo load_freeze_divs ("../../",$permission);  ?><br /></div>
        <fieldset style="width:1370px">
            <form name="yarnWorkOrder_1" id="yarnWorkOrder_1" method="" >
                <table cellpadding="0" cellspacing="2" width="100%" align="center">
                    <tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						
                        <td>&nbsp;<input type="hidden" name="is_approved" id="is_approved" value=""></td>
                        <td>&nbsp;<input type="hidden" name="hidden_pi_id" id="hidden_pi_id" value="">
                        <input type="hidden" name="update_id" id="update_id" value=""></td>
                        <td align="right">WO Number</td>
                        <td><input type="text" name="txt_wo_number"  id="txt_wo_number" class="text_boxes" style="width:138px" placeholder="Double Click to Search" onDblClick="openmypage_wo('x','WO Number Search');" readonly />
                        </td>
                        <td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="100" class="must_entry_caption">Company</td>
                        <td width="170"><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "load_drop_down( 'requires/yarn_work_order_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );print_button_setting();" ); ?>
                        </td>
                        <td  width="100" class="must_entry_caption">Item Category</td>
                        <td width="170"><? echo create_drop_down( "cbo_item_category", 150, $item_category,"", 1, "-- Select --", 1, "",1 ); ?></td>
                        <td width="100" class="must_entry_caption">Supplier</td>
                        <td id="supplier_td"><? echo create_drop_down( "cbo_supplier", 150, $blank_array,"", 1, "-- Select --", 0, "",0 ); ?></td>
						<td class="must_entry_caption">WO Date</td>
                        <td><input type="text" name="txt_wo_date" id="txt_wo_date" class="datepicker" style="width:140px"/></td>
                        <td class="must_entry_caption">Currency</td>
                        <td><? echo create_drop_down( "cbo_currency", 150, $currency,"", 1, "-- Select --", 2, "",0 ); ?></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">WO Basis</td>
                        <td><? echo create_drop_down( "cbo_wo_basis", 150, $wo_basis,"", 1, "-- Select --", 1, "fn_disable_enable(this.value);load_drop_down( 'requires/yarn_work_order_controller', this.value, 'load_details_container', 'details_container' );buyer_style(this.value);",0,'','','' ); ?></td>
                        <td>Buyer PO</td>
                        <td><input type="text" name="txt_buyer_po_no"  id="txt_buyer_po_no" class="text_boxes" style="width:140px" placeholder="Double Click To Search" onDblClick="openmypage()" readonly disabled />
                            <input type="hidden" name="txt_buyer_po"  id="txt_buyer_po" readonly disabled />
                            <input type="hidden" name="txt_job_selected"  id="txt_job_selected" readonly disabled />
                            <!-- when update and decrease row -->
                            <input type="hidden" name="txt_delete_row"  id="txt_delete_row"/></td>
                        <td class="must_entry_caption">Pay Mode</td>
                        <td><? echo create_drop_down( "cbo_pay_mode", 150, $pay_mode,"", 1, "-- Select --", 2, "", 0, "2" ); ?></td> 
                        <td class="must_entry_caption">Source</td>
                        <td><? echo create_drop_down( "cbo_source", 150, $source,"", 1, "-- Select --", 0, "",0 ); ?></td>
						<td class="must_entry_caption">Requisition</td>
                        <td><input type="text" name="txt_requisition"  id="txt_requisition" class="text_boxes" style="width:140px" placeholder="Double Click To Search" onDblClick="openmypage_req()" readonly />
                            <input type="hidden" name="txt_req_id"  id="txt_req_id" readonly disabled />
                            <input type="hidden" name="txt_req_dtls_id"  id="txt_req_dtls_id" readonly disabled />
                    </tr>
                    <tr>
                        
                        <td class="must_entry_caption">Delivery Date</td>
                        <td><input type="text" name="txt_delivery_date"  id="txt_delivery_date" class="datepicker"  style="width:140px" /></td>
                        <td>Attention</td>
                        <td><input type="text" name="txt_attention"  id="txt_attention" style="width:140px " class="text_boxes" /></td>
                        <td>Buyer Name</td>
                        <td><? echo create_drop_down( "txt_buyer_name", 150, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 0, "-- All Buyer --", $selected, "",1,"" ); ?></td>
                        <td>Style</td>
                        <td><input type="text" name="txt_style"  id="txt_style" style="width:140px " class="text_boxes" disabled /></td>
                        <td>D/O No.</td>
                        <td><input type="text" name="txt_do_no"  id="txt_do_no" style="width:140px " class="text_boxes" /></td>
                    </tr>
                    <tr>
                        <td>Remarks</td>
                        <td><input type="text" name="txt_remarks"  id="txt_remarks" style="width:140px" class="text_boxes" /></td>
                        <td class="must_entry_caption">Pay Term</td>
                        <td><?php echo create_drop_down( "cbo_payterm_id",150,$pay_term,'',1,'-Select-',0,"",0,'');//set_port_loading_value(this.value)1,2 ?></td> 
                        <td>Tenor</td>
                        <td><input type="text"  name="txt_tenor" style="width:140px" id="txt_tenor" class="text_boxes_numeric" /></td>
						<td>Incoterm</td>
                        <td><?php echo create_drop_down("cbo_inco_term", 150, $incoterm, "", 0, "", 0, ""); ?></td>
                        <td>PI issue To</td>
                        <td><? echo create_drop_down( "cbo_pi_issue_to", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "" ); ?></td>
                    </tr>
                    <tr>
                        <td >Ready to Approve</td>
                        <td><? echo create_drop_down( "cbo_ready_to_approved", 150, $yes_no,"", 1, "-- Select--", 2, "","","" ); ?></td>
                   
                    	<td>Incoterm Place</td>
                        <td><input type="text" name="txt_inco_term_place" style="width:140px" id="txt_inco_term_place" class="text_boxes" /></td>
                        <td>Delivery Mode</td>
                        <td><? echo create_drop_down( "cbo_delivery_mode", 150, $shipment_mode,"", 1, "-- Select --", 0, "" ); ?></td>
						<td >Add File</td>
						<td >
							<input type="button" class="image_uploader" style="width:140px" value="CLICK TO ADD FILE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'Yarn_Purc_Ord_Sweater', 2 ,1)"> 
						</td>
                        <td align="center">
                        	<!-- <input type="button" id="set_button" class="image_uploader" style="width:100px; margin-left:30px; margin-top:2px;" value="Terms Condition" onClick="open_terms_condition_popup('requires/yarn_work_order_controller.php?action=terms_condition_popup','Terms Condition')" /> -->							
                        </td>					
                    </tr>
					<tr>
					    <td align="center" height="10" colspan="10">
							<?
                            include("../../terms_condition/terms_condition.php");
                            terms_condition(234,'update_id','../../');
                            ?>
                        </td>
					</tr>
                    <tr>
                    	<td colspan="6">
                            <b>Copy</b> : &nbsp;&nbsp;
                                <input type="radio" name="rate_copy" value="1" checked><b>All</b> &nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="rate_copy" value="2"><b>Color Wise</b> &nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="rate_copy" value="3"><b>Count Wise</b> &nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="rate_copy" value="4"><b>Comp 1 Wise</b> &nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="reset" value="Reset">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6"><p id="ref_closed_msg_id" style="font-size:16px; font-weight:bold; color:red;"></p>
                        	<input type="hidden"  name="ref_closed_sts" style="width:159px" id="ref_closed_sts" class="text_boxes_numeric" />
                        </td>
                    </tr>
                </table>
                <br/>
                <div style="width:100%" id="details_container" align="left"></div>
                <table cellpadding="0" cellspacing="3" width="100%">
					<tr>
						<td align="center" colspan="8" valign="middle" class="button_container">
							<? 
							$date=date('d-m-Y'); 
							echo load_submit_buttons( $permission, "fnc_yarn_order_entry", 0,0 ,"reset_form('yarnWorkOrder_1','approved*details_container','','','','cbo_item_category*cbo_currency');$('#cbo_company_name').attr('disabled',false);$('#cbo_wo_basis').attr('disabled',false);$('#ref_closed_msg_id').html('');",1); ?>
							<span id="button_data_panel"></span>
							<input class="formbutton" type="button" onClick="fnSendMail('../../','update_id',1,1,0,0,0,$('#cbo_company_name').val()+'_134_0')" value="Mail Send" style="width:80px;">
							<input type="button" style="width:80px" onclick="openmypage_work_order()" class="printReport formbutton" value="Work Or. Details">
						</td>
					</tr>
                </table> 
            </form>
        </fieldset>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_multiselect('txt_buyer_name','0','0','0','0');
	$('#show_texttxt_buyer_name').attr('disabled',true);
</script>
</html>
