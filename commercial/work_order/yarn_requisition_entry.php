<?
/*-------------------------------------------- Comments
Purpose			: 	Yarn Requisition entry
Functionality	:
JS Functions	:
Created by		:	Jahid
Creation date 	: 	15-03-2015
Updated by 		: 	Abdullah Al Foysal
Update date		: 	12-03-2017
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
echo load_html_head_contents("Yarn Requisiton Entry","../../", 1, 1, $unicode,'','');

?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
<?
if($_SESSION['logic_erp']['data_arr'][70]!="")
{
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][70] );
	echo "var field_level_data= ". $data_arr . ";\n";
}
?>


	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name", "color_name" ), 0, -1); ?>];


	function add_auto_complete(i)
	{
		 $("#txtyarncolor_"+i).autocomplete({
			 source: str_color
		  });
	}

	/*$(document).ready(function(e) {
        $('input[name^="txtyarncolor"]').each(function(index, element) {
			//alert(1);
            this.autocomplete({
			 source: str_color
		  });
        });
    });*/


	function openmypage_req()
	{
		if( form_validation('cbo_company_name*cbo_item_category','Company Name*Item Category')==false )
		{
			return;
		}

		var company = $("#cbo_company_name").val();
		var itemCategory = $("#cbo_item_category").val();
		var page_link = 'requires/yarn_requisition_entry_controller.php?action=req_popup&company='+company+'&itemCategory='+itemCategory;
		var title = "Requisition Search";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1160px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			freeze_window(5);
			var theform=this.contentDoc.forms[0];
			var hidden_wo_number=this.contentDoc.getElementById("hidden_wo_number").value.split("_");

			$("#txt_wo_number").val(hidden_wo_number[0]);
			$("#update_id").val(hidden_wo_number[1]);
			//reset_form('yarnWorkOrder_1','details_container','','','','cbo_item_category*cbo_currency');
			get_php_form_data(hidden_wo_number[1], "populate_data_from_search_popup", "requires/yarn_requisition_entry_controller" );
			show_list_view(hidden_wo_number[1],'show_dtls_listview_update','details_part_list','requires/yarn_requisition_entry_controller','');
			set_button_status(1, permission, 'fnc_yarn_req_entry',1,1);
			$(".printReport").removeClass("formbutton_disabled");//To make disable print to button
			$(".printReport").addClass("formbutton");//To make enable print to button
			//$(".printReport").removeAttr("onClick");
			//$("#print2").attr("onClick","fnc_yarn_req_entry(6)");
			//$("#print3").attr("onClick","fnc_yarn_req_entry(7)");
			//$("#print4").attr("onClick","fnc_yarn_req_entry(8)");
			//$("#print4").attr("onClick","fnc_yarn_req_entry(10)");
			release_freezing();
		}
	}



	function openmypage_job(row_no)
	{
		if (form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{

			var company = $("#cbo_company_name").val();
			var basis = $("#cbo_basis").val();

			if(basis == 4)
			{
				//alert("To browse job, first select Job Basis");
				//$("#cbo_basis").focus(); //return;
				
				var title="Sales Order Number";
				page_link='requires/yarn_requisition_entry_controller.php?action=sales_order_search_with_wo_popup&company='+company;
				var width = "760px";
			}
			else if(basis ==1){
				alert("First select Job Basis");
				$("#cbo_basis").focus(); return;
			}
			else{
				var title="Job No Popup";
				page_link='requires/yarn_requisition_entry_controller.php?action=order_search_popup&company='+company+'&basis='+basis;
				var width = "620px";
			}
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+width+',height=390px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var data=this.contentDoc.getElementById("hidden_tbl_id").value.split("_");
				//freeze_window(5);
				$("#cbo_basis").attr("disabled","true");
				if(basis == 4)
				{
					var dtls_html=return_global_ajax_value(data[0]+'_'+data[1]+'_'+row_no+'_'+company+'_'+data[2]+'_'+data[3], 'dtls_part_html_sales_row', '', 'requires/yarn_requisition_entry_controller');
				}
				else{

					var dtls_html=return_global_ajax_value(data[0]+'_'+data[1]+'_'+row_no+'_'+company+'_'+$('#cbo_basis').val(), 'dtls_part_html_row', '', 'requires/yarn_requisition_entry_controller');
				}

				if(dtls_html!="")
				{
					$('#tr_'+row_no).remove();
					$('#details_part_list').append(dtls_html);
				}
				set_all_onclick();
				//release_freezing();

			}
		}
	}

	function openmypage_wo(row_no)
	{
		if (form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{

			var company = $("#cbo_company_name").val();
			var basis = $("#cbo_basis").val();
			//var text_wo_number = $("#txtwono_1").val();
			//alert(company);
			// 
			var row_num=$('#tbl_purchase_item tbody tr').length;
			//alert(row_num);//return;
			var prev_wo_ids='';
			var prev_buyer_ids='';
			for (var j=1; j<=row_num; j++)
			{
				var hideWoDtlsId=$('#txtwono_'+j).val();   
				var hide_buyer_ids=$('#cbobuyername_'+j).val();
				if(hideWoDtlsId!="")
				{
					if(prev_wo_ids=="") prev_wo_ids=hideWoDtlsId; else prev_wo_ids+=","+hideWoDtlsId;

					if(prev_buyer_ids=="") prev_buyer_ids=hide_buyer_ids; else prev_buyer_ids+=","+hide_buyer_ids;
				}
			}
			

			var title="Work Order Number";
			page_link='requires/yarn_requisition_entry_controller.php?action=order_search_with_wo_popup&company='+company+'&prev_wo_ids='+prev_wo_ids+'&prev_buyer_ids='+prev_buyer_ids;
			var width = "1050px";


			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+width+',height=390px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var data=this.contentDoc.getElementById("hidden_tbl_id_wo").value.split(",");

				$("#cbo_basis").attr("disabled","true");

				var dtls_html=return_global_ajax_value(data[0]+'_'+data[1]+'_'+row_no+'_'+company+'_'+data[2]+'_'+data[3]+'_'+$('#cbo_basis').val(), 'dtls_part_html_row_with_wo', '', 'requires/yarn_requisition_entry_controller');

				//alert( dtls_html);
				if(dtls_html!="")
				{
					$('#tr_'+row_no).remove();
					$('#details_part_list').append(dtls_html);
				}
				set_all_onclick();
				//release_freezing();

			}
		}
		}

	function fnc_yarn_req_entry(operation)
	{
		if(operation==4)
		 {
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#is_approved').val(),"yarn_requisition_print", "requires/yarn_requisition_entry_controller")
			return;
		 }

		 else if(operation==6)
		 {
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_basis').val(),"yarn_requisition_print_2", "requires/yarn_requisition_entry_controller")
			return;
		 }
		 else if(operation==15)
		 {
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_basis').val(),"yarn_requisition_print_15", "requires/yarn_requisition_entry_controller")
			return;
		 }
		 else if(operation==14)
		 {
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_basis').val(),"yarn_requisition_print_8", "requires/yarn_requisition_entry_controller")
			return;
		 }

		 else if(operation==7)
		 {

			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+operation+'*'+$("#cbo_basis").val(),"yarn_requisition_print_3", "requires/yarn_requisition_entry_controller")
			return;
		 }

		else if(operation==8)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title,"yarn_requisition_print_4", "requires/yarn_requisition_entry_controller")
			return;
		}
		else if(operation==9)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title,"yarn_requisition_print_5", "requires/yarn_requisition_entry_controller")
			return;
		}
		else if(operation==10)
		 {
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_basis').val(),"yarn_requisition_print_6", "requires/yarn_requisition_entry_controller")
			return;
		 }
		else if(operation==11)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title,"yarn_requisition_print_fso", "requires/yarn_requisition_entry_controller")
			return;
		}
        else if(operation==12)
        {
            var report_title=$( "div.form_caption" ).html();
            print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title,"yarn_requisition_print_7", "requires/yarn_requisition_entry_controller")
            return;
        }
		else if(operation==13)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title,"yarn_requisition_print_ikdl", "requires/yarn_requisition_entry_controller")
			return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if( form_validation('cbo_company_name*cbo_item_category*txt_delivery_date*txt_wo_date*cbo_basis','Company Name*Item Category*Delivery Date*WO Date*Basis')==false )
			{
				return;
			}


			var total_row = $("#tbl_purchase_item tbody tr").length;
			var cbo_basis=$("#cbo_basis").val();
			//alert(total_row);
			// save data here
			var detailsData="";
			for(var i=1;i<=total_row;i++)
			{
				try
				{
					if($('#reqqnty_'+i).val()!="" && $('#tr_'+i).css('display') != 'none')
					{
						//if($('#yourID').css('display') == 'none')
						//if($('#tr_'+i).css('display')!='none')

						/*if($('#tr_'+i).css('display') != 'none')
						{

						}*/

						if( form_validation('txtyarncolor_'+i+'*cbocount_'+i+'*cbocompone_'+i+'*cbotype_'+i+'*cbouom_'+i+'*reqqnty_'+i,'Color*Count*Yarn Count*Yarn Composition*Yarn Type*UOM*Quantity')==false )
						{
							return;
						}
						
						if(cbo_basis == 1 || cbo_basis == 5)
						{
							if( $("#reqqnty_"+i).val()*1 > $("#hiddenreqqnty_"+i).val()*1)
							{
								alert("Quantity Not Allow Over BOM Balance");
								$("#reqqnty_"+i).focus();
								return;
							}
						}
						
						
						if( $("#reqqnty_"+i).val()*1 <= 0)
						{
							alert("Quantity Can not be 0 or less than 0");
							$("#reqqnty_"+i).focus();
							return;
						}

						 

						detailsData+='*txtjobno_'+i+'*txtjobid_'+i+'*txtwono_'+i+'*txtwoid_'+i+'*cbobuyername_'+i+'*txtstyleno_'+i+'*txtyarncolor_'+i+'*cbocount_'+i+'*cbocompone_'+i+'*txtpacent_'+i+'*cbotype_'+i+'*cbouom_'+i+'*reqqnty_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtyarndate_'+i+'*txtremarks_'+i+'*txtrowid_'+i+'*hiderow_'+i+'*cboyarnfinish_'+i+'*cboyarnsnippingsystem_'+i+'*isShort_'+i+'*cbocertification_'+i+'*txtorderrcvdate_'+i+'*txtintref_'+i+'*txtstock_'+i;
					}
				}
				catch(err){}
			}
			//alert(detailsData);return;
			var is_approved=$('#is_approved').val();//Chech The Approval requisition item.. Change not allowed

			if(is_approved>0)
			{
				alert("This Requisition is Approved. So Change Not Allowed");
				return;
			}

			var data="action=save_update_delete&operation="+operation+'&total_row='+total_row+get_submitted_data_string('update_id*txt_wo_number*cbo_company_name*cbo_item_category*cbo_supplier*txt_delivery_date*cbo_pay_mode*txt_wo_date*cbo_currency*cbo_source*txt_do_no*txt_deal_march*txt_attention*txt_remarks*txt_tenor*cbo_ready_to_approved*cbo_basis'+detailsData,"../../");

			//alert(data);return;

			freeze_window(operation);
			http.open("POST","requires/yarn_requisition_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_yarn_req_entry_reponse;
		}
	}

	function fnc_yarn_req_entry_reponse()
	{
		if(http.readyState == 4)
		{
			//alert(http.responseText);release_freezing(); return;
			var reponse=trim(http.responseText).split('**');

			/*if(reponse[0]==15)
			{
				setTimeout('fnc_yarn_req_entry('+ reponse[1]+')',8000);
				show_msg(trim(reponse[0]));
				release_freezing();
				return;
			}*/
			
			if(reponse[0]==404)
			{
				alert("Buyer Mix Not Allowed");
				release_freezing();
				return;
			}

			if(reponse[0]==20)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			else if(reponse[0]==11)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			else if(reponse[0]==34)
			{
				show_msg(trim(reponse[0]));
				alert(reponse[1]);
				release_freezing();
				return;
			}
			else if (reponse[0]==2)
			{
				show_msg(trim(reponse[0]));
				reset_form('yarnRequisition_1','','','','$(\'#tbl_purchase_item tbody tr:not(:first)\').remove();','cbo_company_name*cbo_item_category*txt_wo_date');
				$('#cbo_company_name').attr('disabled',false);
				$('#cbo_basis').attr('disabled',false);
				$("#booking_td").text("Job No");
				$('#txtjobno_1').removeAttr("onDblClick").removeAttr("placeholder");
				$('#txtwono_1').removeAttr("onDblClick").removeAttr("placeholder");

				$("#txtjobno_1").attr("onDblClick","openmypage_job(1)");
				$("#txtjobno_1").attr("placeholder", "Doble Click For Job");
				$("#txtwono_1").attr("onDblClick","openmypage_wo(1)");
				$("#txtwono_1").attr("placeholder", "Doble Click For Job");
				release_freezing();
				return;

			}
			else if(reponse[0]==0 || reponse[0]==1)
			{
				show_msg(trim(reponse[0]));
				$("#txt_wo_number").val(reponse[1]);
				$("#update_id").val(reponse[2]);
				show_list_view(reponse[2],'show_dtls_listview_update','details_part_list','requires/yarn_requisition_entry_controller','');
				set_all_onclick();
				set_button_status(1, permission, 'fnc_yarn_req_entry',1);
                $(".printReport").removeClass("formbutton_disabled");//To make disable print to button
                $(".printReport").addClass("formbutton");//To make enable print to button
                //$(".printReport").removeAttr("onClick");
                //$("#print2").attr("onClick","fnc_yarn_req_entry(6)");
                //$("#print3").attr("onClick","fnc_yarn_req_entry(7)");
                //$("#print4").attr("onClick","fnc_yarn_req_entry(8)");
				release_freezing();
			}
			else
			{
				show_msg(trim(reponse[0]));
				release_freezing();
				return;
			}

			//reset_form('yarnWorkOrder_1','details_container','','','','cbo_item_category*cbo_currency');
		}
	}


	// amount calculation here
	function calculate_amount(row_id)
	{
		
		if($("#cbo_basis").val() == 4 || $("#cbo_basis").val() == 1) //Booking or Salse order basis
		{
			//alert("amoutn if");
			if($("#txtwono_" + row_id).val() != "" || $("#txtjobno_" + row_id).val() != ""  )
            {
				if(parseFloat($("#hiddenreqqnty_" + row_id).val()) >= parseFloat($('#reqqnty_'+row_id).val()) )
				{
					var tot_amount=($('#reqqnty_'+row_id).val()*1)*($('#txtrate_'+row_id).val()*1);
					$('#txtamount_'+row_id).val(number_format_common(tot_amount,4));
				}
				else
				{
					//alert("Requisition Qty Should Less than Booking");
					alert("Requisition Qty Should Be Equal or Less than Booking Qty");
					$('#reqqnty_'+row_id).val($("#hiddenreqqnty_" + row_id).val()*1);
                    var tot_amount=($('#reqqnty_'+row_id).val()*1)*($('#txtrate_'+row_id).val()*1);
                    $('#txtamount_'+row_id).val(number_format_common(tot_amount,4));
				}
				
        	}
			
			//===Rate Validation===
			if($("#txtjobno_" + row_id).val() != "" || $("#txtwono_" + row_id).val() != "" )
			{
				var is_short=$("#isShort_" + row_id).val();
				if(is_short != 1)
				{
					if(parseFloat($("#hiddentxtrate_" + row_id).val()) >= parseFloat($('#txtrate_'+row_id).val()) )
					{
						var tot_amount=($('#reqqnty_'+row_id).val()*1)*($('#txtrate_'+row_id).val()*1);
						$('#txtamount_'+row_id).val(number_format_common(tot_amount,4));
					}
					else
					{
						alert("Rate Should Not Be Over than Existing Rate");
						$('#txtrate_'+row_id).val($("#hiddentxtrate_" + row_id).val()*1);
						var tot_amount=($('#reqqnty_'+row_id).val()*1)*($('#txtrate_'+row_id).val()*1);
						$('#txtamount_'+row_id).val(number_format_common(tot_amount,4));
					}
				}
			}
			else
			{
				var tot_amount=($('#reqqnty_'+row_id).val()*1)*($('#txtrate_'+row_id).val()*1);
				$('#txtamount_'+row_id).val(number_format_common(tot_amount,4));
			}
		}
		else
		{
			
			if($("#txtjobno_" + row_id).val() != "" || $("#txtwono_" + row_id).val() != "" )
			{
				if($("#cbo_basis").val() == 2){
					var tot_amount=($('#reqqnty_'+row_id).val()*1)*($('#txtrate_'+row_id).val()*1);
                	$('#txtamount_'+row_id).val(number_format_common(tot_amount,4));
				}else{
					//alert(parseFloat(($("#hiddenreqqnty_" + row_id).val())*1));
					//alert(parseFloat(($('#reqqnty_'+row_id).val())*1));
					if(parseFloat(($("#hiddenreqqnty_" + row_id).val())*1) >= parseFloat(($('#reqqnty_'+row_id).val())*1) )
					{
						var tot_amount=($('#reqqnty_'+row_id).val()*1)*($('#txtrate_'+row_id).val()*1);
						$('#txtamount_'+row_id).val(number_format_common(tot_amount,4));
					}
					else
					{  
						alert("Requisition Qty Should Less than Booking Qty");                 
						$('#reqqnty_'+row_id).val($("#hiddenreqqnty_" + row_id).val()*1);
						var tot_amount=($('#reqqnty_'+row_id).val()*1)*($('#txtrate_'+row_id).val()*1);
						$('#txtamount_'+row_id).val(number_format_common(tot_amount,4));
					}
				}
                
            }
            else
            {
                var tot_amount=($('#reqqnty_'+row_id).val()*1)*($('#txtrate_'+row_id).val()*1);
                $('#txtamount_'+row_id).val(number_format_common(tot_amount,4));
            }
            //===Rate Validation===
			var cbo_basis=$("#cbo_basis").val();
			//alert(cbo_basis);
            if( ($("#txtjobno_" + row_id).val() != "" || $("#txtwono_" + row_id).val() != "")  && cbo_basis !=10)
            {
				if($("#cbo_basis").val() == 2){
					var tot_amount=($('#reqqnty_'+row_id).val()*1)*($('#txtrate_'+row_id).val()*1);
                	$('#txtamount_'+row_id).val(number_format_common(tot_amount,4));
				}else{
					var is_short=$("#isShort_" + row_id).val();
				
					if(is_short != 1)
					{
						if(parseFloat($("#hiddentxtrate_" + row_id).val()) >= parseFloat($('#txtrate_'+row_id).val()))
						{
							var tot_amount=($('#reqqnty_'+row_id).val()*1)*($('#txtrate_'+row_id).val()*1);
							$('#txtamount_'+row_id).val(number_format_common(tot_amount,4));
						}
						else
						{
							alert("Rate Should Not Be Over than Existing Rate");
							$('#txtrate_'+row_id).val($("#hiddentxtrate_" + row_id).val()*1);
							var tot_amount=($('#reqqnty_'+row_id).val()*1)*($('#txtrate_'+row_id).val()*1);
							$('#txtamount_'+row_id).val(number_format_common(tot_amount,4));
						}
					}
				}
            }
            else
            {
                var tot_amount=($('#reqqnty_'+row_id).val()*1)*($('#txtrate_'+row_id).val()*1);
                $('#txtamount_'+row_id).val(number_format_common(tot_amount,4));
            }
		}
	}

	//details part row incriment here
	function add_break_down_tr(i)
	{
		if( form_validation('cbo_company_name*reqqnty_'+i,'Company Name*Quantity')==false )
		{
			return;
		}
		var row_num=$('#tbl_purchase_item tbody tr').length;
		var i = row_num;
		i++;
		var k=i-1;
		$("#tbl_purchase_item tbody tr:last").clone().find("input,select").each(function(){
		$(this).attr({
			'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
			'value': function(_, value) { return value }
		});
		}).end().appendTo("#tbl_purchase_item").show();

		$("#tbl_purchase_item tbody tr:last").css({"height":"10px","background-color":"#FFF"});
		$("#tbl_purchase_item tbody tr:last ").removeAttr('id').attr('id','tr_'+i);
		$('#cbocomponename_'+i).removeAttr("onclick").attr("onclick","OpenComp("+i+");");

		$('#txtrowid_'+i).val('');
		$('#cbobuyername_'+i).val('');
		$('#txtjobno_'+i).removeAttr("onDblClick").removeAttr("placeholder");
		$('#txtjobno_'+k).removeAttr("onDblClick").removeAttr("placeholder");
		$('#txtwono_'+i).removeAttr("onDblClick").removeAttr("placeholder");
		$('#txtwono_'+k).removeAttr("onDblClick").removeAttr("placeholder");		
		$('#txtyarndate_'+i).removeAttr("class").attr("class","datepicker");
		$('#txtorderrcvdate_'+i).removeAttr("class").attr("class","datepicker");			
		$('#reqqnty_'+i).removeAttr("onKeyUp").attr("onKeyUp","calculate_amount("+i+");");
		$('#txtrate_'+i).removeAttr("onKeyUp").attr("onKeyUp","calculate_amount("+i+");");
		$('#txtyarncolor_'+i).removeAttr("onFocus").attr("onFocus","add_auto_complete("+i+");");
		$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deletebreak_down_tr("+i+",0,0);");
		$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
		$('#hiderow_'+i).val("");
		//When update button enabled. Just before update need to execute this condition to enable change of the values on details part.
		if($("#txt_wo_number").val() != ""){ 
			$('#txtjobno_'+i).removeAttr("readonly");
			$('#txtwono_'+i).removeAttr("readonly");
			$('#cbobuyername_'+i).removeAttr("disabled");
			$('#txtstyleno_'+i).removeAttr("disabled");
			$('#txtstyleno_'+i).removeAttr("readonly");
			$('#cboyarnfinish_'+i).removeAttr("disabled");
			$('#cboyarnsnippingsystem_'+i).removeAttr("disabled");
			$('#cbouom_'+i).removeAttr("disabled");
		}
		set_all_onclick();
		
	}

	function fn_deletebreak_down_tr(rowNo,dtls_id,next_transaction)
	{
		var user_confirm = confirm("Press \"OK\" to delete row.\nPress \"Cancel\" does not delete.");
		if (user_confirm == true) 
		{
			if(next_transaction==1)
			{
				alert("Next Transaction Found");return;
			}
			var show_val_column = "1";
			var row_num=$('#tbl_purchase_item tbody tr').length;
			if(row_num!=rowNo)
			{
				if(dtls_id)
				{
					var data="action=save_update_delete_row&dtls_id="+dtls_id;
					//alert(data);return;
					freeze_window();
					http.open("POST","requires/yarn_requisition_entry_controller.php",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = function() 
					{
						if (this.readyState == 4) 
						{
							var response=trim(http.responseText).split('**');
							if(response[0]==2)
							{
								$('#tr_'+rowNo).hide();
								$('#hiderow_'+rowNo).val(1);
							}
							alert(response[1]);
							release_freezing();				
						}
					}
				}
				else
				{
					$('#tr_'+rowNo).hide();
					$('#hiderow_'+rowNo).val(1);
				}
			}else
			{
				$('#tr_'+rowNo).hide();
				$('#hiderow_'+rowNo).val(1);
			}
		}
		else 
		{
			var show_val_column = "0";
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

	function fnc_booking_td_change()
	{
		var basis = $("#cbo_basis").val();
		//alert(basis);
		if(basis ==1)
		{
			$("#txtjobno_1").attr("disabled",true);
			$("#txtwono_1").attr({
				disabled : false,
				placeholder: "Double Click for Booking",
				onDblClick: "openmypage_wo(1)"
			});
			$("#booking_td").text("Fab.Booking");
			$("#details_part_list").find('tbody tr').each(function()
			{
				$(this).find("td").eq(1).find('input').removeAttr("ondblclick");
				$(this).find("td").eq(1).find('input').removeAttr("placeholder");
				$(this).find("td").eq(1).find('input').attr("disabled","true");
			});
		}
		else if(basis == 2){
			$("#txtjobno_1").attr({
				disabled : false,
				placeholder: "Write",
				onDblClick: "",
				readonly: false
			});
			$("#txtwono_1").attr({
				disabled : false,
				placeholder: "Write",
				onDblClick: "",
				readonly: false
			});
			$("#txtremarks_1").attr({
				disabled : false,
				placeholder: "Write",
				onDblClick: "",
				onclick: "",
				readonly: false
			});
			$("#increase_1").attr({
				disabled : false,
				placeholder: "Write",
				onDblClick: "",
				onclick: "add_break_down_tr(1)",
				readonly: false,
				style: "width:30px; display:block"
			});
		}
		else if(basis == 4)
		{
			/*$("#txtjobno_1").attr({
				disabled : false,
				placeholder: "Double Click for Job",
				onDblClick: "openmypage_job(1)"
			});
			
			$("#txtwono_1").attr({
				disabled : false,
				placeholder: "Double Click for Job",
				onDblClick: "openmypage_job(1)"
			});
			$("#txtjobno_1").attr("disabled",false);
			$("#txtwono_1").attr("disabled",false);*/
			
			$("#booking_td").text("Sales Order No");
			
			
			var i=1;
			$("#tbl_purchase_item").find('tbody tr').each(function()
			{
				$(this).find("td").eq(1).find('input').removeAttr("ondblclick");
				$(this).find("td").eq(1).find('input').attr("placeholder","Double Click for Job");
				$(this).find("td").eq(1).find('input').attr("onDblClick","openmypage_job("+i+")");
				$(this).find("td").eq(1).find('input').attr("disabled",false);
				
				$(this).find("td").eq(0).find('input').removeAttr("ondblclick");
				$(this).find("td").eq(0).find('input').attr("placeholder","Double Click for Job");
				$(this).find("td").eq(0).find('input').attr("onDblClick","openmypage_job("+i+")");
				$(this).find("td").eq(0).find('input').attr("disabled",false);
				
				i++;
			});
			//$(this).closest('tr').find('td').eq(0).find('input').val()
			//$(".printReport").removeAttr("onClick");
		}
		else if(basis ==5)
		{
			$("#txtjobno_1").attr({
				disabled : false,
				placeholder: "Double Click for Job",
				onDblClick: "openmypage_job(1)"
			});
			$("#txtjobno_1").attr("disabled",false);
			$("#txtwono_1").attr("disabled",true);
			$("#booking_td").text("Fab.Booking");
		}
		else
		{
			$("#txtjobno_1").attr({
				disabled : false,
				placeholder: "Double Click for Job",
				onDblClick: "openmypage_job(1)"
			});
			$("#txtwono_1").attr({
				disabled : false,
				placeholder: "Double Click for Booking",
				onDblClick: "openmypage_wo(1)"
			});
			$("#booking_td").text("Fab.Booking");
			$("#txtjobno_1").attr("disabled",false);
			var sl = 0;
			$("#tbl_purchase_item").find('tbody tr').each(function()
			{
				sl++;
				$(this).find("td").eq(1).find('input').attr("ondblclick","openmypage_wo(1)");
				$(this).find("td").eq(1).find('input').attr("placeholder","Doble Click For Booking");
				$(this).find("td").eq(1).find('input').removeAttr("disabled");

				"openmypage_wo("+sl+");"
			});
		}
        if(basis == 2){
            $('#details_part_list').find('tr').each(function (){
                var td1 =  $(this).find('td').eq(4);
                var td2 =  $(this).find('td').eq(7);
                td1.find('input').val('');
                td2.find('input').val(100);
            });
        }
        else if(basis == 7){
            $('#details_part_list').find('tr').each(function (){
                var td1 =  $(this).find('td').eq(4);
                var td2 =  $(this).find('td').eq(7);
                td1.find('input').val('GREY');
                td2.find('input').val(100);
            });
        }
        else{
            $('#details_part_list').find('tr').each(function (){
                var td1 =  $(this).find('td').eq(4);
                var td2 =  $(this).find('td').eq(7);
                td1.find('input').val('');
                td2.find('input').val('');
            });
        }
	}

	function fnc_stock_use_td_change()
	{
		var basis = $("#cbo_basis").val();
		//alert(basis);
		if(basis != 2)
		{
			$(document).ready(function(){
				$("#txtstock_1").attr("disabled",true);

				$("#cbocount_1").attr("disabled",true);
				$("#cbotype_1").attr("disabled",true);
				// $('#stock_td').hide()
				// $('#stock_td1').hide()
			});
		}
		else{
			$(document).ready(function(){
				$("#txtstock_1").attr("disabled",false);

				$("#cbocount_1").attr("disabled",false);
				$("#cbotype_1").attr("disabled",false);
				// $('#stock_td').show()
				// $('#stock_td1').show()
			});
		}
	}

	function calculate_sales_amount(row_id)
	{
        if($("#txtwono_" + row_id).val() != "" ){
            var tot_amount=($('#reqqnty_'+row_id).val()*1)*($('#txtrate_'+row_id).val()*1);
            $('#txtamount_'+row_id).val(number_format_common(tot_amount,4));

        }
	}

	function print_button_setting()
	{
		//$('#button_data_panel').html('');
		get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/yarn_requisition_entry_controller' );
	}
	
	
	function independence_basis_controll_function(data)
	{
		reset_form('yarnRequisition_1','','','','$(\'#tbl_purchase_item tbody tr:not(:first)\').remove();fnc_booking_td_change(1);','cbo_company_name*cbo_item_category*txt_wo_date*cbouom_1');
		$('#cbo_currency').val(2);
        var varible_string=return_global_ajax_value( data, 'varible_inventory', '', 'requires/yarn_requisition_entry_controller');
		var varible_string_ref=varible_string.split("**");
		//alert(varible_string_ref[0]);
		if(varible_string_ref[0])
		{
			$('#variable_string_inventory').val(varible_string_ref[1]+"**"+varible_string_ref[2]+"**"+varible_string_ref[3]+"**"+varible_string_ref[4]);
			if(varible_string_ref[1]==1)
			{
				$("#cbo_basis option[value='2']").hide();
			}
			else
			{
				$("#cbo_basis option[value='2']").show();
			}
		}
		else
		{
			$('#variable_string_inventory').val("");
			$("#cbo_basis option[value='2']").show();
		}
	}

	function openprecess(id){
	
		var data=$('#cbocertification_'+id).val();
		var page_link='requires/yarn_requisition_entry_controller.php?action=process_name_pop_up&row_id='+id+'&data='+data;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Select Item Popup', 'width=400px,height=300px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];       
			var process_name=this.contentDoc.getElementById("hidden_process_name").value;	
			var process_id=this.contentDoc.getElementById("hidden_process_id").value;		
					
			if (process_id!="")
			{
				$('#proccessname_'+id).val(process_name);
				$('#cbocertification_'+id).val(process_id);
				
			}else{
				$('#proccessname_'+id).val(null);
				$('#cbocertification_'+id).val(null);

			}
			
		}
	}
	function fnc_update_rate()
	{
		var total_row = $("#tbl_purchase_item tbody tr").length;
		var cbo_basis=$("#cbo_basis").val();
		var detailsData="";
		for(var i=1;i<=total_row;i++)
		{
			try
			{
				if($('#reqqnty_'+i).val()!="" && $('#tr_'+i).css('display') != 'none')
				{
					if( form_validation('txtyarncolor_'+i+'*cbocount_'+i+'*cbocompone_'+i+'*cbotype_'+i+'*cbouom_'+i+'*reqqnty_'+i,'Color*Count*Yarn Count*Yarn Composition*Yarn Type*UOM*Quantity')==false )
					{
						return;
					}
					
					if(cbo_basis == 5)
					{
						if( $("#reqqnty_"+i).val()*1 <= 0)
						{
							alert("Quantity Can not be 0 or less than 0");
							$("#reqqnty_"+i).focus();
							return;
						}
						detailsData+='*txtrowid_'+i+'*hiddentxtupdaterate_'+i+'*hiddentxtupdateamount_'+i;
					}
					else
					{
						return;
					}
				
				}
			}
			catch(err){}
		}
		//alert(detailsData);return;
		var is_approved=$('#is_approved').val();//Chech The Approval requisition item.. Change not allowed

		if(is_approved>0)
		{
			alert("This Requisition is Approved. So Change Not Allowed");
			return;
		}

		var data="action=save_update_delete_rate&total_row="+total_row+get_submitted_data_string('update_id*txt_wo_number'+detailsData,"../../");

		// alert(data);return;

		freeze_window();
		http.open("POST","requires/yarn_requisition_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_update_rate_reponse;
	}
	function fnc_update_rate_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==11)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			else if(reponse[0]==1)
			{
				show_msg(trim(reponse[0]));
				$("#txt_wo_number").val(reponse[2]);
				$("#update_id").val(reponse[1]);
				//reset_form('yarnWorkOrder_1','details_container','','','','cbo_item_category*cbo_currency');
				get_php_form_data(reponse[1], "populate_data_from_search_popup", "requires/yarn_requisition_entry_controller" );
				show_list_view(reponse[1],'show_dtls_listview_update','details_part_list','requires/yarn_requisition_entry_controller','');
				set_button_status(1, permission, 'fnc_yarn_req_entry',1,1);
				document.getElementById("req_sms").innerHTML = "";
				document.getElementById("req_sms1").innerHTML = "";
				$(".printReport").removeClass("formbutton_disabled");//To make disable print to button
				$(".printReport").addClass("formbutton");
				release_freezing();
				return;
			}
			else
			{
				show_msg(trim(reponse[0]));
				release_freezing();
				return;
			}

		}
	}

	
function OpenComp(id){
	
	var data=$('#cbocompone_'+id).val();
	var page_link='requires/yarn_requisition_entry_controller.php?action=comp_name_pop_up&row_id='+id+'&data='+data;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Select Process Popup', 'width=400px,height=300px,center=1,resize=1,scrolling=0','../../')
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
    <div style="width:1300px;">
        <? echo load_freeze_divs ("../../",$permission);  ?><br />
    </div>
		<fieldset style="width:1300px">
			<form name="yarnRequisition_1" id="yarnRequisition_1" method="" >
				<table  cellspacing="2" width="1200" border="1" rules="all">
					<tr>
					  <td>&nbsp;</td>
					  <td>&nbsp;</td>
					  <td>&nbsp;</td>
					  <td>&nbsp;</td>
					  <td  align="left">Requisition No&nbsp;</td><input type="hidden" name="is_approved" id="is_approved" value="">
					  <td><input type="text" name="txt_wo_number"  id="txt_wo_number" class="text_boxes" style="width:159px" placeholder="Double Click to Search" onDblClick="openmypage_req('x','WO Number Search');" readonly />
                      <input type="hidden" id="update_id" name="update_id" >
                      </td>
					  <td>&nbsp;</td>
					  <td>&nbsp;</td>
					  <td>&nbsp;</td>
					  <td>&nbsp;</td>
				  </tr>
					<tr>
						<td width="100" class="must_entry_caption" align="left">Company&nbsp;</td>
						<td width="170">
                        	<?
							   	echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "load_drop_down( 'requires/yarn_requisition_entry_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );load_drop_down( 'requires/yarn_requisition_entry_controller', this.value+'__'+1, 'load_drop_down_buyer1', 'buy_td' );print_button_setting(); setFieldLevelAccess(this.value); independence_basis_controll_function(this.value)" );
 							?>
                            <input type="hidden" name="variable_string_inventory" id="variable_string_inventory" />
						</td>
                        <td width="130" class="must_entry_caption" align="left">Item Category&nbsp;</td>
						<td width="168">
                        	<?
							   	echo create_drop_down( "cbo_item_category", 150, $item_category,"", 1, "-- Select --", 1, "",1 );
 							?>
                        </td>
                        <!--this field temporary hidden-->
						<td  style="display:none" align="left">Supplier&nbsp;</td>
						<td  id="supplier_td" style="display:none">
						  	<?
							   	echo create_drop_down( "cbo_supplier", 170, $blank_array,"", 1, "-- Select --", 0, "",0 );
 							?>
						</td>
                         <!--this field temporary hidden-->
                        <td width="120" class="must_entry_caption" align="left">Required Date&nbsp;</td>
						<td ><input type="text" name="txt_delivery_date"  id="txt_delivery_date" class="datepicker"  style="width:140px" value="<? echo date('d-m-Y')?>" /></td>
						<td  align="left">Pay Mode&nbsp;</td>
						<td ><?
							   	echo create_drop_down( "cbo_pay_mode", 150, $pay_mode,"", 1, "-- Select --", 0, "",0 );
 							?>
						</td>
						<td class="must_entry_caption" align="left">Requisition Date&nbsp;</td>
						<td ><input type="text" name="txt_wo_date" value="<? echo date('d-m-Y');?>" id="txt_wo_date" class="datepicker" style="width:140px" disabled/></td>
					</tr>

					<tr>
                        <td align="left" >Currency&nbsp;</td>
						<td ><?
							   	echo create_drop_down( "cbo_currency", 150, $currency,"", 1, "-- Select --", 2, "",0 );
 							?>						  <!-- when update and decrease row --></td>
 						<td  align="left">Source&nbsp;</td>
						<td ><?
							   	echo create_drop_down( "cbo_source", 150, $source,"", 1, "-- Select --", 0, "",0 );
 							?></td>
 							<td align="left">D/O No.&nbsp;</td>
						<td ><input type="text" name="txt_do_no"  id="txt_do_no" style="width:140px " class="text_boxes" /></td>
						<td align="left">Attention&nbsp;</td>
						<td ><input type="text" name="txt_attention"  id="txt_attention" style="width:140px " class="text_boxes" /></td>
						<td width="100" align="left">Dealing Merchant&nbsp;</td>
						<td ><input type="text" name="txt_deal_march"  id="txt_deal_march" style="width:140px " class="text_boxes" /></td>
					</tr>

					<tr>
						<td class="must_entry_caption" align="left">Basis&nbsp;</td>
                        <td>
                        <?
                        	echo create_drop_down( "cbo_basis", 150, $issue_basis,"", 1, "-- Select--", 0, "fnc_booking_td_change(); fnc_stock_use_td_change(); load_drop_down( 'requires/yarn_requisition_entry_controller', this.value, 'load_drop_down_composition', 'composition_td' );","","","","","3" );
                        ?>
                        </td>
                        <td align="left">Tenor&nbsp;</td>
                        <td><input style="width:140px;" type="text" class="text_boxes_numeric" name="txt_tenor" id="txt_tenor" /></td>
					    <td align="left">Ready to Approve&nbsp;</td>
                        <td>
                        <?
                        	echo create_drop_down( "cbo_ready_to_approved", 150, $yes_no,"", 1, "-- Select--", 2, "","","" );
                        ?>
                        </td>
                        <td align="left">Remarks&nbsp;</td>
						<td colspan="5"><input type="text" name="txt_remarks"  id="txt_remarks" style="width:420px " class="text_boxes" maxlength="150" /></td>
					
					</tr>

					

					<tr>
						<td align="center" height="10" colspan="10">
							<?
                            include("../../terms_condition/terms_condition.php");
                            terms_condition(70,'txt_wo_number','../../');
                            ?>
                            <!--<input type="button" id="set_button" class="image_uploader" style="width:100px; margin-left:30px; margin-top:2px;" value="Terms Condition" onClick="open_terms_condition_popup('requires/yarn_requisition_entry_controller.php?action=terms_condition_popup','Terms Condition')" />-->
                        </td>
					</tr>
					<tr>
						<td align="center" height="10" colspan="6">
							<div id="req_sms" style="font-size:18px; color:#F00"></div>
                        </td>
					</tr>
                </table>
                <br />
                <table class="rpt_table" width="1215" cellspacing="0" cellpadding="0" id="tbl_purchase_item" border="1" rules="all">
                    <thead>
                        <th width="80" id="job_td" style="word-break: break-all;">Sales Job/Booking</th>
                        <th width="80" id="booking_td">Fab.Booking</th>
                        <th width="90">Buyer Name</th>
                        <th width="120">Style</th>
						<th width="120">Internal Ref</th>
                        <th width="80">Yarn Color</th>
                        <th width="70">Count</th>
                        <th width="100">Composition</th>
                        <th width="40">%</th>
                        <th width="100">Yarn Type</th>
                        <th width="100">Yarn Finish</th>
                        <th width="100">Yarn Spinning System</th>
                        <th width="100">Certification</th>
                        <th width="60">UOM</th>
                        <th width="55" class="must_entry_caption">Requisition Qty.</th>
                        <th id="stock_td" width="50">Stock Use</th>
                        <th width="40">Rate</th>
                        <th width="55">Amount</th>
                        <th width="65">Yarn Inhouse Date</th>
                        <th width="65">Order Recieve Date</th>
                        <th width="80">IR/Remarks</th>
                        <th > </th>
                    </thead>
                    <tbody id="details_part_list">
                        <tr class="general" id="tr_1">
                            <td align="center">
                            <input type="text" name="txtjobno_1" id="txtjobno_1" class="text_boxes" value="" style="width:75px;" onDblClick="openmypage_job(1)" placeholder="Double Click For Job" readonly />

                            <input type="hidden" id="txtjobid_1" name="txtjobid_1" style="width:100px;">
                            <input type="hidden" name="txtrowid_1" id="txtrowid_1" class="text_boxes" value="" style="width:70px;" />
                            <input type="hidden" name="hiderow_1" id="hiderow_1" class="text_boxes" value="0" style="width:70px;" />
                            <input type="hidden" name="is_approved" id="is_approved" value="">
                            </td>
                            <td>
                              <input type="text" name="txtwono_1" id="txtwono_1" class="text_boxes" value="" style="width:75px;" onDblClick="openmypage_wo(1)" placeholder="Double Click For WO Number" readonly />
                             <input type="hidden" id="txtwoid_1" name="txtwoid_1">
                             <input type="hidden" id="isShort_1" name="isShort_1">
                            </td>

                            <td id="buy_td">
							<?
							   	echo create_drop_down( "cbobuyername_1", 90, "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select --", 0, "" );
 							?>
                            </td>
                            <td align="center"><input type="text" name="txtstyleno_1" id="txtstyleno_1" class="text_boxes" value="" style="width:75px;" /></td>

							<td align="center"><input type="text" name="txtintref_1" id="txtintref_1" class="text_boxes" value="" style="width:120px;" /></td>


                            <td align="center"><input type="text" name="txtyarncolor[]" id="txtyarncolor_1" class="text_boxes" value="GREY" style="width:75px;" onFocus="add_auto_complete( 1 )" /></td>
                            <td align="center">
                            <?
								echo create_drop_down( "cbocount_1", 70, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1,"-- Select --", 0, "",0,"" );
							?>
                            </td>
                            <td align="center" id="composition_td">
                            <?
								echo create_drop_down( "cbocompone_1", 100, $composition,"", 1, "-- Select --", 0, "",0,"","","",$ommitComposition );
							?>
							<input type="hidden" name="cbocomponename_1" id="cbocomponename_1">
                            </td>
                            <td><input type="text" name="txtpacent_1" id="txtpacent_1" class="text_boxes" value="100" style="width:40px;" /></td>
                            <td>
							<?
								echo create_drop_down( "cbotype_1", 100, $yarn_type,"", 1, "-- Select --", $row[csf("type_id")], "",$disabled,"","","",$ommitYarnType );
							?>
                            </td>
                             <td>
                             	<? echo create_drop_down( "cboyarnfinish_1", 80, $yarn_finish_arr,"", 1, "-- Select --", $row_p[csf("yarn_finish")], '','','','','' ); ?>
                             </td>
                              <td><? echo create_drop_down( "cboyarnsnippingsystem_1", 80, $yarn_spinning_system_arr,"", 1, "-- Select --", $row_p[csf("yarn_spinning_system")], '','','','','' ); ?>
                              	
                              </td>
                             <td>
                             	
                             	<input type='text' style='width:105px' name="proccessname_1" id="proccessname_1"  placeholder='Browse' value="" class='text_boxes' onfocus='openprecess(1)'  /> 
                             	<input type="hidden" name="cbocertification_1" id="cbocertification_1">
                             	
                             </td>
                            <td>

                            <?
								echo create_drop_down( "cbouom_1", 60, $unit_of_measurement,"", 1, "-- Select--", 12, "",1);
							?>
                            </td>
                            <td>
                                <input type="text" id="reqqnty_1" name="reqqnty_1" class="text_boxes_numeric" style="width:55px" onKeyUp="calculate_amount(1)" />
                            	<input type="hidden" id="hiddenreqqnty_1" name="hiddenreqqnty_1" value=""/>
                            </td>

							<td>
                            	<input type="text" name="txtstock_1" id="txtstock_1" class="text_boxes_numeric" value="" disabled style="width:50px;" />
                            	<input type="hidden" id="hiddentxtstock_1" name="hiddentxtstock_1" value=""/>
                            </td>

                            <td>
                            	<input type="text" name="txtrate_1" id="txtrate_1" class="text_boxes_numeric" value="" style="width:40px;" onKeyUp="calculate_amount(1)" />
                            	<input type="hidden" id="hiddentxtrate_1" name="hiddentxtrate_1" value=""/>
                            </td>
                            <td><input type="text" name="txtamount_1" id="txtamount_1" class="text_boxes_numeric" value="" style="width:50px;" readonly /></td>
                            <td><input class="datepicker" type="text" style="width:65px;" name="txtyarndate_1" id="txtyarndate_1" placeholder="Select Date" /></td>

							<td><input class="datepicker" type="text" style="width:65px;" name="txtorderrcvdate_1" id="txtorderrcvdate_1" placeholder="Select Date" /></td>

                            <td><input type="text" name="txtremarks_1" id="txtremarks_1" class="text_boxes" value="" style="width:110px;"  onClick="add_break_down_tr(1)"/></td>

                            <td style="display:flex;">
								<input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(1,0,0);" />
								<input type="button" id="increase_1" name="increase_1" style="width:30px; display: none;" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1)" />
							</td>

                        </tr>
                    </tbody>
                </table>
				<table cellpadding="0" cellspacing="2" width="100%">
                	<tr>
				  		<td align="center" colspan="6" valign="middle" class="button_container">
				  		<div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
							<?
								echo load_submit_buttons( $permission, "fnc_yarn_req_entry", 0,0 ,"reset_form('yarnRequisition_1','','','','$(\'#tbl_purchase_item tbody tr:not(:first)\').remove();$(\'#cbo_basis\').attr(\'disabled\',false);$(\'#cbo_company_name\').attr(\'disabled\',false);','cbo_company_name*cbo_item_category*txt_wo_date');",1);
							?>
								<div id="req_sms1"></div>
								<span id="button_data_panel"></span>
						</td>
					</tr>
				</table>
			</form>
		</fieldset>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
