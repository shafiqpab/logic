<?
/*-------------------------------------------- Comments
Purpose			: 	Yarn Work order entry
Functionality	:
JS Functions	:
Created by		:	Bilas
Creation date 	: 	22-04-13
Updated by 		: 	Kausar	(Creating Report)
Update date		: 	13-02-2014
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//$req_variable_setting=2;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn Work Order","../../", 1, 1, $unicode,1,'');

$color_sql = sql_select("select id,color_name from lib_color order by id");
$color_name = "";
foreach($color_sql as $result)
{
	$color_name.= "{value:'".$result[csf('color_name')]."',id:".$result[csf('id')]."},";
}

$independent_control_arr = return_library_array( "select company_name, independent_controll from variable_settings_inventory where variable_list=20 and menu_page_id=144 and status_active=1 and is_deleted=0",'company_name','independent_controll');
if(count($independent_control_arr)<1) $independent_control_arr=array(0=>0);

?>
<script>
	var permission='<? echo $permission; ?>';
	//var req_variable_setting='<? //echo $req_variable_setting; ?>';
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
			$("#cbo_yarn_basis").attr("disabled",true);
		}
		else if(str==1)
		{
			$("#txt_requisition").attr("disabled",false);
			$("#txt_buyer_po_no").val('');
			$("#txt_buyer_po").val('');
			$("#txt_job_selected").val('');
			$("#txt_buyer_po_no").attr("disabled",true);
			$("#cbo_yarn_basis").attr("disabled",true);
		}
		else
		{
			$("#txt_buyer_po_no").val('');
			$("#txt_buyer_po").val('');
			$("#txt_job_selected").val('');
			$("#txt_buyer_po_no").attr("disabled",true);
			$("#txt_requisition").attr("disabled",true);
			$("#cbo_yarn_basis").attr("disabled",false);
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
			var update_id = $("#update_id").val();
			
			if(break_down_id!="")
			{
				freeze_window(5);
				show_list_view(break_down_id+'***'+job_number+'***'+update_id,'show_dtls_listview','details_container','requires/yarn_work_order_controller','');
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

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1290px,height=430px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var dtls_id=this.contentDoc.getElementById("txt_dtls_id").value; //dtls id here
			var mst_id=this.contentDoc.getElementById("txt_mst_id").value; // req mst id
			var req_no=this.contentDoc.getElementById("txt_req_no").value; // req no
			// req no unique
			var req_arr = req_no.split(",");
			var uniqueSet = new Set(req_arr);
			var uniqueArr = Array.from(uniqueSet);
			var req_no_unique = uniqueArr.join(",");
			//alert('System');

			$("#txt_req_id").val(mst_id);
			$("#txt_req_dtls_id").val(dtls_id);
			$("#txt_requisition").val(req_no_unique);


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

	function calculate_yarn_consumption_ratio(i,precost_rate,row_id)
	{
		var rate=$('#txt_rate_'+i).val()*1;
		var basis=$('#txt_requ_basis_'+i).val()*1;
		var wo_basis=$('#cbo_wo_basis').val()*1;

		var txt_job_id=$('#txt_job_id_'+i).val()*1;
		var txt_job=$('#txt_job_'+i).val();
		var txt_requ_rate=$('#txt_requ_rate_'+i).val()*1;

		var req_variable_setting = $('#item_rate_match_with_budget_variable').val()*1;
		//alert(rate +"="+ precost_rate +"="+ wo_basis +"="+ txt_job +"="+ basis +"="+ req_variable_setting+"="+ txt_requ_rate);return;
		var is_short=$("#isShort_" + row_id).val();
		if(rate>precost_rate && wo_basis==1 && txt_job != ""  && ( basis==1 || basis==5 || basis==0) && req_variable_setting ==1 && is_short !=1){
			alert("Yarn rate not allow over the pre-costing. Note: Pre Cost Rate:"+precost_rate);
			$('#txt_rate_'+i).val(precost_rate);
		}

		/* if(rate>txt_requ_rate && wo_basis==1 && txt_job == "" && req_variable_setting ==1 && is_short !=1){
			alert("Yarn rate not allow over the Requision. Note: Requision Rate:"+txt_requ_rate);
			$('#txt_rate_'+i).val(txt_requ_rate);
		} */


		if(rate>precost_rate && wo_basis==3 && req_variable_setting ==1){
			alert("Yarn rate not allow over the pre-costing. Note: Pre Cost Rate:"+precost_rate);
			$('#txt_rate_'+i).val(precost_rate);
		}


	/*	if(rate>precost_rate && (wo_basis==1 || wo_basis==3)){
			alert("Yarn rate not allow over the pre-costing. Note: Pre Cost Rate:"+precost_rate);
			$('#txt_rate_'+i).val(precost_rate);
		}*/


		var txt_req_qnty=$('#txt_req_qnty_'+i).val()*1;

		var cbocount=$('#txt_quantity_'+i).val()*1;
		if((Math.floor(txt_req_qnty/50)*50+50)<cbocount)
		{
			alert('WO quantity must be less than or equal to round 50 of Requision Quantity');
			$('#txt_quantity_'+i).val(0);
			$('#txt_quantity_'+i).focus();

		}
		var cbocompone=$('#txt_rate_'+i).val();
		var amount = cbocount*1*cbocompone*1;
		$('#txt_amount_'+i).val(amount.toFixed(4));
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

	function fn_inc_decr_row(rowid,type)
	{
		if(type=="increase")
		{
			var row = $("#tbl_details tbody tr:last").attr('id');
			var valuesLastRow = $("#tbl_details tbody tr:last").find('input[name=txt_color_'+row+']').val();
			$('#cbocomponename_'+row).removeAttr("onclick").attr("onclick","OpenComp("+row+");");
			if(valuesLastRow!="")
			{
				row = row*1+1;
				var responseHtml = return_ajax_request_value(row+'__'+valuesLastRow, 'append_load_details_container', 'requires/yarn_work_order_controller');
				$("#tbl_details tbody").append(responseHtml);
				set_all_onclick();
			}
		}
		else if(type=="decrease")
		{
			//alert(rowid);
			var row = $("#tbl_details tr").length-1;
			if(rowid*1!="" && row*1>1)
			{
				var vals = $("#txt_delete_row").val();
				var delID = $("#txt_row_id_"+rowid).val();
				if(vals!="")
 				$("#txt_delete_row").val(vals+','+delID);
			else
				$("#txt_delete_row").val(delID);

			$("#tbl_details tr#"+rowid).remove();
			}
			else
				return;
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

    function print_to_html_report(type)
	{
		var report_title=$( "div.form_caption" ).html();
		//alert(report_title);
		if(type == 1){
        	window.open("requires/yarn_work_order_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+type+'*'+$('#ref_closed_sts').val()+'*'+$('#is_approved_id').val()+'*'+$('#cbo_wo_basis').val()+'&action='+"print_to_html_report", true );
        }else if(type == 4){
        	window.open("requires/yarn_work_order_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+type+'*'+$('#ref_closed_sts').val()+'*'+$('#is_approved_id').val()+'&action='+"print_to_html_report4", true );
        }
		else if(type == 5){
        	print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#ref_closed_sts').val()+'*'+$('#cbo_pay_mode').val()+'*'+$('#is_approved_id').val(),"yarn_work_order_print5", "requires/yarn_work_order_controller");
			return;
        }else if(type == 3){
			//alert(type);
        	window.open("requires/yarn_work_order_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+type+'*'+$('#ref_closed_sts').val()+'*'+$('#is_approved_id').val()+'&action='+"print_to_html_report3", true );
			return;
    	}else if(type == 7){
			//alert(type);
        	window.open("requires/yarn_work_order_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+type+'*'+$('#cbo_supplier').val()+'&action='+"print_to_html_report7", true );
			return;
        }else if(type == 8){
			// alert(type);
        	window.open("requires/yarn_work_order_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+type+'&action='+"yarn_work_order_print8", true );
			return;
		}else if(type == 9){
			var r = confirm("Ok to print without Job No \n Cancel to print with Job No");
			if (r == true) {
				type = 1;
			} else {
				type = 2;
			}
			// alert(type);

        	window.open("requires/yarn_work_order_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+type+'*'+$('#cbo_supplier').val()+'*'+$('#cbo_wo_basis').val()+'&action='+"print_to_html_report9", true );
			return;
        }else{
        	window.open("requires/yarn_work_order_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+type+'*'+$('#ref_closed_sts').val()+'*'+$('#is_approved_id').val()+'&action='+"print_to_html_report2", true );
        }
    }


	function fnc_yarn_order_entry(operation)
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#ref_closed_sts').val()+'*'+$('#cbo_pay_mode').val()+'*'+$('#is_approved_id').val(),"yarn_work_order_print", "requires/yarn_work_order_controller")
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
						if( form_validation('txt_color_'+i+'*cbocount_'+i+'*cbocompone_'+i+'*cbotype_'+i+'*cbo_uom_'+i+'*txt_quantity_'+i+'*txt_rate_'+i+'*txt_amount_'+i+'*txt_number_of_lot_'+i,'Color*Count*Composition*Yarn Type*UOM*Quantity*Rate*Amount*Number of Lot')==false )
						{
							return;
						}

						if( $("#txt_quantity_"+i).val()*1 <= 0 || $("#txt_rate_"+i).val()*1 <= 0 )
						{
							alert("Quantity OR Rate Can not be 0 or less than 0");
							$("#txt_quantity_"+i).focus();
							return;
						}

						detailsData+='*txt_req_'+i+'*txt_req_dtls_id_'+i+'*txt_job_'+i+'*txt_job_id_'+i+'*txt_booking_'+i+'*txt_buyer_id_'+i+'*txt_style_'+i+'*txt_po_brakdown_id_'+i+'*txt_color_'+i+'*hidden_colorID_'+i+'*cbocount_'+i+'*cbocompone_'+i+'*percentone_'+i+'*cbocomptwo_'+i+'*percenttwo_'+i+'*cbotype_'+i+'*cbo_uom_'+i+'*txt_req_qnty_'+i+'*txt_quantity_'+i+'*txt_rate_'+i+'*txt_amount_'+i+'*txt_sample_date_'+i+'*txt_inhouse_date_'+i+'*txt_delivery_end_date_'+i+'*txt_row_id_'+i+'*txt_remarks_'+i+'*txt_number_of_lot_'+i+'*isShort_'+i;
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
						if( form_validation('txt_color_'+i+'*cbocount_'+i+'*cbocompone_'+i+'*cbotype_'+i+'*cbo_uom_'+i+'*txt_quantity_'+i+'*txt_rate_'+i+'*txt_amount_'+i+'*percentone_'+i+'*txt_number_of_lot_'+i,'Color*Count*Composition*Yarn Type*UOM*Quantity*Rate*Amount*Percentage*Number of Lot')==false )
						{
							return;
						}

						if( $("#txt_quantity_"+i).val()*1 <= 0 || $("#txt_rate_"+i).val()*1 <= 0 )
						{
							alert("Quantity OR Rate Can not be 0 or less than 0");
							$("#txt_quantity_"+i).focus();
							return;
						}

						detailsData+='*txt_po_brakdown_id_'+i+'*txt_color_'+i+'*hidden_colorID_'+i+'*cbocount_'+i+'*cbocompone_'+i+'*percentone_'+i+'*cbocomptwo_'+i+'*percenttwo_'+i+'*cbotype_'+i+'*cbo_uom_'+i+'*txt_quantity_'+i+'*txt_rate_'+i+'*txt_amount_'+i+'*txt_sample_date_'+i+'*txt_inhouse_date_'+i+'*txt_delivery_end_date_'+i+'*txt_row_id_'+i+'*txt_remarks_'+i+'*txt_number_of_lot_'+i;
					}
					catch(err){}
				}
			}

			var is_approved=$('#is_approved_id').val();//approval requisition item Change not allowed
			if(is_approved==1)
			{
				alert("This Work Order is Approved. So Change Not Allowed");
				return;
			}

			var data="action=save_update_delete&operation="+operation+'&total_row='+row+get_submitted_data_string('garments_nature*update_id*txt_wo_number*cbo_company_name*cbo_item_category*cbo_supplier*txt_wo_date*cbo_currency*cbo_wo_basis*cbo_pay_mode*cbo_source*txt_delivery_date*txt_attention*txt_buyer_po*txt_delete_row*txt_buyer_name*txt_style*txt_do_no*txt_remarks*cbo_wo_type*cbo_payterm_id*txt_tenor*cbo_inco_term*cbo_pi_issue_to*cbo_ready_to_approved*cbo_ship_mode*cbo_deal_merchant'+detailsData,"../../");
			// alert(data);return;

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
			if(reponse[0]==0 || reponse[0]==1)
			{
				$("#txt_wo_number").val(reponse[1]);
				$("#update_id").val(reponse[2]);
				disable_enable_fields( 'cbo_company_name*cbo_currency*cbo_wo_basis', 1, '', '' );
				var cbo_pay_mode = $("#cbo_pay_mode").val();
				show_list_view(reponse[2]+'****'+reponse[4]+'****'+cbo_pay_mode,'show_dtls_listview_update','details_container','requires/yarn_work_order_controller','');
				set_button_status(1, permission, 'fnc_yarn_order_entry',1);
			}
			else if(reponse[0]==404)
			{
				alert("Buyer Mix Not Allowed.");
				release_freezing(); 
				return;				 
			}
			else if(reponse[0]==500)
			{
				alert("This Work Order is Approved. So Update/Delete Not Allowed");
				release_freezing(); 
				return;				 
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
			if(reponse[0]==2){
				reset_form('yarnWorkOrder_1','details_container','','','','cbo_item_category*cbo_currency');
			}

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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1250px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			freeze_window(5);
			var theform=this.contentDoc.forms[0];
			var hidden_wo_number=this.contentDoc.getElementById("hidden_wo_number").value.split("_");
			reset_form('yarnWorkOrder_1','details_container','','','','cbo_item_category*cbo_currency');
			//reset_form('yarnWorkOrder_1','approved*details_container','','','','cbo_item_category*cbo_currency');
			$("#txt_wo_number").val(hidden_wo_number[0]);
			$("#update_id").val(hidden_wo_number[1]);

			get_php_form_data(hidden_wo_number[1], "populate_data_from_search_popup", "requires/yarn_work_order_controller" );
			disable_enable_fields( 'cbo_company_name*cbo_currency*cbo_wo_basis', 1, '', '' );
			
            const comId = $("#cbo_company_name").val();
			//load_drop_down(comId, "load_drop_down_supplier_new", "requires/yarn_work_order_controller" );

			load_drop_down( 'requires/yarn_work_order_controller',comId+'*'+hidden_wo_number[1], 'load_drop_down_supplier_new', 'cbo_supplier' );
            
			var cbo_pay_mode = $("#cbo_pay_mode").val();			
			show_list_view(hidden_wo_number[1]+'****'+hidden_wo_number[2]+'****'+cbo_pay_mode,'show_dtls_listview_update','details_container','requires/yarn_work_order_controller','');
			set_button_status(1, permission, 'fnc_yarn_order_entry',1,1);
			release_freezing();
			//$("#id_print_to_button").removeClass( "formbutton_disabled"); //To make disable print to button
			//$("#id_print_to_button").addClass( "formbutton"); //To make enable print to button
			$(".printReport").removeClass( "formbutton_disabled"); //To make disable print to button
			$(".printReport").addClass( "formbutton"); //To make enable print to button
			buyer_style(document.getElementById('cbo_wo_basis').value);
		}
	}

	function buyer_style(val)
	{
		//alert (val)
		if (val==2)
		{
			 $('#txt_buyer_name').attr('disabled',false);
			 document.getElementById('txt_style').disabled=false;
		}
		else
		{
			$('#txt_buyer_name').attr('disabled',true);
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

	function fn_plan_popup(row_id)
	{

		if( form_validation('txt_inhouse_date_'+row_id+'*txt_delivery_end_date_'+row_id,'Bulk Delv. Start*Count*Bulk Delv. End')==false )
		{
			return;
		}

		var dtls_id=$('#txt_row_id_'+row_id).val();
		var color_name=$('#txt_color_'+row_id).val();
		var yarn_count=$('#cbocount_'+row_id).find(":selected").text();
		var yarn_comp=$('#cbocompone_'+row_id).find(":selected").text();
		var yarn_type=$('#cbotype_'+row_id).find(":selected").text();
		var delv_start_date=$('#txt_inhouse_date_'+row_id).val();
		var delv_end_date=$('#txt_delivery_end_date_'+row_id).val();
		var percentone=$('#percentone_'+row_id).val();
		var wo_qnty=$('#txt_quantity_'+row_id).val();
		var cbo_uom=$('#cbo_uom_'+row_id).find(":selected").text();

		var page_link = 'requires/yarn_work_order_controller.php?action=delv_plan_popup&yarn_count='+yarn_count+'&yarn_comp='+yarn_comp+'&yarn_type_id='+yarn_type+'&delv_start_date='+delv_start_date+'&delv_end_date='+delv_end_date+'&dtls_id='+dtls_id+'&percentone='+percentone+'&wo_qnty='+wo_qnty+'&color_name='+color_name+'&cbo_uom='+cbo_uom;
		var title = "Delivery Plan Details";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=580px,height=480px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var txt_date_from=this.contentDoc.getElementById("hdn_txt_date_form").value;			
			var txt_date_to=this.contentDoc.getElementById("hdn_txt_date_to").value;
			$('#txt_inhouse_date_'+row_id).val(txt_date_from);
			$('#txt_delivery_end_date_'+row_id).val(txt_date_to);
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

	function item_rate_match_with_budget(company_id)
	{
		//$('#button_data_panel').html('');
		get_php_form_data(company_id,'item_rate_match_with_budget_variable_setting','requires/yarn_work_order_controller' );
	}


	function independence_basis_controll_function(data)
	{
		var independent_control_arr = JSON.parse('<? echo json_encode($independent_control_arr); ?>');
		$("#cbo_wo_basis").val(0);
		$("#cbo_wo_basis option[value='2']").show();
		//alert(independent_control_arr[data]);
		if(independent_control_arr[data]==1)
		{
			$("#cbo_wo_basis option[value='2']").hide();
		}
	}

	function check_all_report(column_id) {
		var total_row = $("#tbl_details tbody tr").length;
		//alert(column_id);
		if(column_id == "bulk_delvi_start" || column_id == "bulk_delvi_end")
		{
			var date_cond = $('#txt_inhouse_date_1').val();
			var date_cond2 = $('#txt_delivery_end_date_1').val();
			//alert(start_date);
			for (var i = 1; i <= total_row; i++) {
				if(date_cond != "")
				{
					$('#txt_inhouse_date_'+i).val(date_cond);
					//$('#txt_delivery_end_date_'+i).val(date_cond2);
				}else{
					$('#txt_inhouse_date_1').focus();
					//$('#txt_delivery_end_date_1').focus();
				}
				if(date_cond2 != "")
				{
					//$('#txt_inhouse_date_'+i).val(date_cond);
					$('#txt_delivery_end_date_'+i).val(date_cond2);
				}else{
					//$('#txt_inhouse_date_1').focus();
					$('#txt_delivery_end_date_1').focus();
				}

				
			}
		}else{
			var no_lot_cond = $('#txt_number_of_lot_1').val();
			for (var i = 1; i <= total_row; i++) {
			//alert(date_cond);return;

				if(no_lot_cond != "")
				{
					$('#txt_number_of_lot_'+i).val(no_lot_cond);
				}else{
					$('#txt_number_of_lot_').focus();
				}
				
			}
		}
		
	}

	function fn_cmposion_load(str)
	{
		var comp_all_data = return_global_ajax_value(str, 'composiotn_all_data', '', 'requires/yarn_work_order_controller');
		//alert(comp_all_data);return;
		var JSONObject_comp = JSON.parse(comp_all_data);
		
		var tot_row=$("#tbl_details tbody tr").length;
		for(var i=1; i<=tot_row; i++)
		{
			$('#cbocompone_'+i).html("");
			$('#cbocompone_'+i).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject_comp))
			{
				$('#cbocompone_'+i).append('<option value="'+key+'">'+JSONObject_comp[key]+'</option>');
			}
		}
		//alert(JSONObject_comp + "="+ tot_row);
	}

	function OpenComp(id){
	
		var data=$('#cbocompone_'+id).val();
		var page_link='requires/yarn_work_order_controller.php?action=comp_name_pop_up&row_id='+id+'&data='+data;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Select Composition Popup', 'width=400px,height=300px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];       
			var composition_name=this.contentDoc.getElementById("composition_name").value;	
			var composition_id=this.contentDoc.getElementById("composition_id").value;		
					// alert(composition_name+composition_id);
			if (composition_id!="")
			{
				$('#cbocomponename_'+id).val(composition_name);
				$('#cbocompone_'+id).val(composition_id);
				
			}else{
				$('#cbocompone_'+id).val(null);
				$('#cbocomponename_'+id).val(null);

			}
			
		}
	}

	

</script>
<body onLoad="set_hotkey()">
<div align="center">
    <div style="width:1450px;">
        <? echo load_freeze_divs ("../../",$permission);  ?><br />
    </div>
		<fieldset style="width:1600px">
			<form name="yarnWorkOrder_1" id="yarnWorkOrder_1" method="" >
				<table cellpadding="0" cellspacing="2" width="1500" align="center">
					<tr>
					  <td>&nbsp;
					  	<input type="hidden" name="is_approved" id="is_approved" value="">
					  	
					  	<input type="hidden" name="item_rate_match_with_budget_variable" id="item_rate_match_with_budget_variable" value="" readonly>
					  	 
					  </td>
					  <td>&nbsp;<input type="hidden" name="update_id" id="update_id" value=""></td>
					   <td>&nbsp;</td>
					   <td>&nbsp;</td>
					  <td align="left">WO Number</td><input type="hidden" name="is_approved_id" id="is_approved_id" value="">
					  <td><input type="text" name="txt_wo_number"  id="txt_wo_number" class="text_boxes" style="width:159px" placeholder="Double Click to Search" onDblClick="openmypage_wo('x','WO Number Search');" readonly />
                      </td>
					  
				  </tr>
					<tr>
						<td align="left" width="100" class="must_entry_caption">Company</td>
						<td width="100">
                        	<?
							   	echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "load_drop_down( 'requires/yarn_work_order_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );print_button_setting();independence_basis_controll_function(this.value);item_rate_match_with_budget(this.value);" );
 							?>
						</td>
                        <td align="left" width="80" class="must_entry_caption">Item Category</td>
						<td width="100">
                        	<?
							   	echo create_drop_down( "cbo_item_category", 170, $item_category,"", 1, "-- Select --", 1, "",1 );
 							?>
                        </td>
						<td align="left" width="150" class="must_entry_caption">Supplier</td>
						<td width="170" id="supplier_td">
						  	<?
							   	echo create_drop_down( "cbo_supplier", 150, $blank_array,"", 1, "-- Select --", 0, "",0 );
 							?>
						</td>
						<td width="100" align="left" class="must_entry_caption">WO Date</td>
						<td width="">
							<input type="text" name="txt_wo_date" id="txt_wo_date" value="<? echo date("d-m-Y")?>" class="datepicker" style="width:159px"/>
 						</td>
						 <td align="left">Wo Type&nbsp;</td>
                        <td>
						<?
						echo create_drop_down("cbo_wo_type", 170, $wo_type_array, "", 1, "-- Select --", 0, "", "", "");
						?>
                        </td>
					</tr>
					<tr>
					
						<td align="left" width="" class="must_entry_caption">Currency</td>
						<td width="">
                         	<?
							   	echo create_drop_down( "cbo_currency", 170, $currency,"", 1, "-- Select --", 2, "",0 );
 							?>
                        </td>
                        <td align="left" width="" class="must_entry_caption">WO Basis</td>
						<td width="">
                        	<?
 								echo create_drop_down( "cbo_wo_basis", 170, $wo_basis,"", 1, "-- Select --", 0, "fn_disable_enable(this.value);load_drop_down( 'requires/yarn_work_order_controller', this.value, 'load_details_container', 'details_container' );buyer_style(this.value);",0,'','','' );
 							?>
                        </td>
                        <td align="left" width="" >Buyer PO</td>
						<td width=""><input type="text" name="txt_buyer_po_no"  id="txt_buyer_po_no" class="text_boxes" style="width:140px" placeholder="Double Click To Search" onDblClick="openmypage()" readonly disabled />
                          <input type="hidden" name="txt_buyer_po"  id="txt_buyer_po" readonly disabled />
                          <input type="hidden" name="txt_job_selected"  id="txt_job_selected" readonly disabled />
                          <!-- when update and decrease row -->
                        <input type="hidden" name="txt_delete_row"  id="txt_delete_row"/></td>
                        <td align="left" width="" class="must_entry_caption">Pay Mode</td>
						<td width=""><?	echo create_drop_down( "cbo_pay_mode", 170, $pay_mode,"", 1, "-- Select --", 0, "",0 );?></td>
						<td align="left">file</td>
                    	<td> <input type="button" class="image_uploader" style="width:160px" value="CLICK TO ADD FILE" onClick="file_uploader ( '../../', document.getElementById('txt_wo_number').value,'', 'yarn_purchase_order', 2 ,1)"> </td>
					</tr>
					<tr>
						
						<td align="left" width="" class="must_entry_caption">Source</td>
						<td width=""><?
							   	echo create_drop_down( "cbo_source", 170, $source,"", 1, "-- Select --", 0, "",0 );
 							?></td>
 						<td align="left" width="" >Requisition</td>
						<td width=""><input type="text" name="txt_requisition"  id="txt_requisition" class="text_boxes" style="width:159px" placeholder="Double Click To Search" onDblClick="openmypage_req()" readonly disabled />
                          <input type="hidden" name="txt_req_id"  id="txt_req_id" readonly disabled />
                          <input type="hidden" name="txt_req_dtls_id"  id="txt_req_dtls_id" readonly disabled />
                      </td>
                      <td align="left" width=""><span class="must_entry_caption">Target Delivery Date</span></td>
						<td width=""><input type="text" name="txt_delivery_date"  id="txt_delivery_date" class="datepicker"  style="width:140px" /></td>
						<td align="left" width="">Attention</td>
						<td width=""><input type="text" name="txt_attention"  id="txt_attention" style="width:159px " class="text_boxes" /></td>
						<td align="left">Ready to Approve&nbsp;</td>
                        <td>
                        <?
                        	echo create_drop_down( "cbo_ready_to_approved", 150, $yes_no,"", 1, "-- Select--", 2, "","","" );
                        ?>
                        </td>
					</tr>
                    <tr>
						<td align="left" width="">Buyer Name</td>
                    	<td width="">
                        <!--<input type="text" name="txt_buyer_name"  id="txt_buyer_name" style="width:159px " class="text_boxes" disabled />-->
                            <?
                                echo create_drop_down( "txt_buyer_name", 170, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "","","" );
                            ?>
                        </td>
						<td align="left" width="">Style</td>
						<td width=""><!-- END -->
                        <input type="text" name="txt_style"  id="txt_style" style="width:159px " class="text_boxes" disabled /></td>
                        <td align="left">D/O No.</td>
                        <td><input type="text" name="txt_do_no"  id="txt_do_no" style="width:140px " class="text_boxes" /></td>
                        <td align="left" class="must_entry_caption">Pay Term</td>
                        <td><?php echo create_drop_down("cbo_payterm_id", 165, $pay_term, '', 1, '-Select-', 0, "", 0, ''); //set_port_loading_value(this.value)1,2  ?></td>
						<td width="">Dealing Merchant</td>
						<td width=""><? echo create_drop_down( "cbo_deal_merchant", 170, "select id,team_member_name from lib_mkt_team_member_info where  status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select --",0, "",0 );
 							?></td>
                    </tr>
                    <tr>
                                            
                        <td align="left">Tenor</td>
						<td><input type="text"  name="txt_tenor" style="width:159px" id="txt_tenor" class="text_boxes_numeric" /></td>
						<td align="left">Incoterm</td>
                        <td>
                            <?
								echo create_drop_down("cbo_inco_term", 170, $incoterm, "", 0, "", 0, "");
							?>
                        </td>
                        <td align="left">PI issue To</td>
						<td>
                            <?
                            echo create_drop_down( "cbo_pi_issue_to", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "" );
                            ?>

                        </td>
                        <td align="left">Ship Mode</td>
						<td>
							<? echo create_drop_down( "cbo_ship_mode", 170, $shipment_mode,"", 1, "-- Select Ship Mode --", 0, "" ); ?>
                        </td>
                        <!-- <td>Delivery End Date</td>
                        <td><input type="text" name="txt_delivery_end_date" class="datepicker" id="txt_remarks" style="width:159px" class="text_boxes" /></td>-->

						<td align="left">Remarks</td>
                        <td colspan="3"><input type="text" name="txt_remarks"  id="txt_remarks" style="width:230px" class="text_boxes" /></td>
                    </tr>
                    <tr>
                    	<td colspan="6"><p id="ref_closed_msg_id" style="font-size:16px; font-weight:bold; color:red;"></p>
                        <input type="hidden"  name="ref_closed_sts" style="width:159px" id="ref_closed_sts" class="text_boxes_numeric" />
                        </td>
                    </tr>
					<tr>
						<td align="center" height="10" colspan="6">
							<?
                            include("../../terms_condition/terms_condition.php");
                            terms_condition(144,'txt_wo_number','../../');
                            ?>
                        </td>
					</tr>

                </table>
                <br/>
                <div style="width:1600px" id="details_container" align="left"></div>
				<table cellpadding="0" cellspacing="2" width="100%">
                	<tr>
				  		<td align="center" colspan="6" valign="middle" class="button_container">
						  <div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
							<?
								echo load_submit_buttons( $permission, "fnc_yarn_order_entry", 0,0 ,"reset_form('yarnWorkOrder_1','approved*details_container','','','','cbo_item_category*cbo_currency');$('#cbo_company_name').attr('disabled',false);$('#cbo_wo_basis').attr('disabled',false);$('#ref_closed_msg_id').html('');",1);
							?>
                                <span id="button_data_panel"></span>
                                <!-- <input type="button" style="width:80px;" id="id_print_to_button"  onClick="print_to_html_report(1)"   class="formbutton_disabled printReport" name="id_print_to_button" value="Print2" />
                                <input type="button" style="width:80px;" id="id_print_to_button2"  onClick="print_to_html_report(2)" class="formbutton_disabled printReport" name="id_print_to_button2" value="Print3" />-->
						</td>
                        <td>

                        </td>
					</tr>
				</table>
			</form>
		</fieldset>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	// set_multiselect('txt_buyer_name','0','0','0','0');
	// $('#show_texttxt_buyer_name').attr('disabled',true);
</script>
</html>
