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

/*$color_sql = sql_select("select id,color_name from lib_color order by id");
$color_name = "";
foreach($color_sql as $result)
{
	$color_name.= "{value:'".$result[csf('color_name')]."',id:".$result[csf('id')]."},";
}*/

/*$x = "a";
$y = "b";
$x ^= $y;
$y ^= $x;
$x ^= $y;
echo $x."===".$y;die;*/

?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
<?
$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][70] );
echo "var field_level_data= ". $data_arr . ";\n";
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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=880px,height=370px,center=1,resize=0,scrolling=0','../')
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
				page_link='requires/yarn_requisition_entry_controller.php?action=order_search_popup&company='+company;
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
					var dtls_html=return_global_ajax_value(data[0]+'_'+data[1]+'_'+row_no+'_'+company+'_'+data[2], 'dtls_part_html_sales_row', '', 'requires/yarn_requisition_entry_controller');
				}
				else{

					var dtls_html=return_global_ajax_value(data[0]+'_'+data[1]+'_'+row_no+'_'+company, 'dtls_part_html_row', '', 'requires/yarn_requisition_entry_controller');
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
			for (var j=1; j<=row_num; j++)
			{
				var hideWoDtlsId=$('#txtwono_'+j).val();
				if(hideWoDtlsId!="")
				{
					if(prev_wo_ids=="") prev_wo_ids=hideWoDtlsId; else prev_wo_ids+=","+hideWoDtlsId;
				}
			}
			

			var title="Work Order Number";
			page_link='requires/yarn_requisition_entry_controller.php?action=order_search_with_wo_popup&company='+company+'&prev_wo_ids='+prev_wo_ids;
			var width = "795px";


			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+width+',height=390px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var data=this.contentDoc.getElementById("hidden_tbl_id_wo").value.split(",");

				$("#cbo_basis").attr("disabled","true");

				var dtls_html=return_global_ajax_value(data[0]+'_'+data[1]+'_'+row_no+'_'+company+'_'+data[2]+'_'+data[3], 'dtls_part_html_row_with_wo', '', 'requires/yarn_requisition_entry_controller');

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
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title,"yarn_requisition_print_2", "requires/yarn_requisition_entry_controller")
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

		else if(operation==0 || operation==1 || operation==2)
		{
			if( form_validation('cbo_company_name*cbo_item_category*txt_delivery_date*txt_wo_date','Company Name*Item Category*Delivery Date*WO Date')==false )
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
						detailsData+='*txtjobno_'+i+'*txtjobid_'+i+'*txtwono_'+i+'*txtwoid_'+i+'*cbobuyername_'+i+'*txtstyleno_'+i+'*txtyarncolor_'+i+'*cbocount_'+i+'*cbocompone_'+i+'*txtpacent_'+i+'*cbotype_'+i+'*cbouom_'+i+'*reqqnty_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtyarndate_'+i+'*txtremarks_'+i+'*txtrowid_'+i+'*hiderow_'+i;

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

			var data="action=save_update_delete&operation="+operation+'&total_row='+total_row+get_submitted_data_string('update_id*txt_wo_number*cbo_company_name*cbo_item_category*cbo_supplier*txt_delivery_date*cbo_pay_mode*txt_wo_date*cbo_currency*cbo_source*txt_do_no*txt_deal_march*txt_attention*txt_remarks*cbo_ready_to_approved*cbo_basis'+detailsData,"../../");

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

			if(reponse[0]==15)
			{
				setTimeout('fnc_yarn_req_entry('+ reponse[1]+')',8000);
			}
			else if(reponse[0]==20)
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
		}
		else
		{
			
			if($("#txtjobno_" + row_id).val() != "" || $("#txtwono_" + row_id).val() != "" )
			{
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
            else
            {
                var tot_amount=($('#reqqnty_'+row_id).val()*1)*($('#txtrate_'+row_id).val()*1);
                $('#txtamount_'+row_id).val(number_format_common(tot_amount,4));
            }
            //===Rate Validation===
            if($("#txtjobno_" + row_id).val() != "" || $("#txtwono_" + row_id).val() != "" )
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
			  'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
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


			$('#txtjobno_'+i).removeAttr("onDblClick").removeAttr("placeholder");
			$('#txtjobno_'+k).removeAttr("onDblClick").removeAttr("placeholder");
			$('#txtwono_'+i).removeAttr("onDblClick").removeAttr("placeholder");
			$('#txtwono_'+k).removeAttr("onDblClick").removeAttr("placeholder");

			/*$('#txtjobno_'+k).removeAttr("onDblClick").removeAttr("placeholder");
			$('#txtwono_'+k).removeAttr("onDblClick").removeAttr("placeholder");
			$('#txtjobno_'+row_num).removeAttr("onDblClick").removeAttr("placeholder").attr("onDblClick","openmypage_job("+row_num+");").attr("placeholder","Doble Click For Job Number");
			$('#txtwono_'+row_num).removeAttr("onDblClick").removeAttr("placeholder").attr("onDblClick","openmypage_wo("+row_num+");").attr("placeholder","Doble Click For WO Number");*/

			$('#txtremarks_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
			$('#reqqnty_'+i).removeAttr("onKeyUp").attr("onKeyUp","calculate_amount("+i+");");
			$('#txtrate_'+i).removeAttr("onKeyUp").attr("onKeyUp","calculate_amount("+i+");");
			$('#txtyarncolor_'+i).removeAttr("onFocus").attr("onFocus","add_auto_complete("+i+");");
			$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deletebreak_down_tr("+i+");");
		}
	}

	function fn_deletebreak_down_tr(rowNo)
	{
		var row_num=$('#tbl_purchase_item tbody tr').length;
		if(row_num!=rowNo)
		{
			$('#tr_'+rowNo).hide();
			$('#hiderow_'+rowNo).val(1);
		}

		/*var prev_row=rowNo-1;
		$('#txtjobno_'+prev_row).removeAttr("onDblClick").removeAttr("placeholder").attr("onDblClick","openmypage_job("+prev_row+");").attr("placeholder","Doble Click For Job Number");
		$('#txtwono_'+prev_row).removeAttr("onDblClick").removeAttr("placeholder").attr("onDblClick","openmypage_wo("+prev_row+");").attr("placeholder","Doble Click For WO Number");
		var row_nums=$('#tbl_purchase_item tbody tr:visible').length;
		alert(row_nums+"=="+row_num+"=="+rowNo);
		if(row_nums==1)
		{
			$('#txtjobno_'+row_num).removeAttr("onDblClick").removeAttr("placeholder").attr("onDblClick","openmypage_job("+row_num+");").attr("placeholder","Doble Click For Job Number");
			$('#txtwono_'+row_num).removeAttr("onDblClick").removeAttr("placeholder").attr("onDblClick","openmypage_wo("+row_num+");").attr("placeholder","Doble Click For WO Number");
		}*/
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
		if(basis ==1){
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
		else if(basis == 4)
		{
			$("#booking_td").text("Sales Order No");
			$("#txtjobno_1").attr("disabled",false);

			$("#tbl_purchase_item").find('tbody tr').each(function()
			{
				$(this).find("td").eq(1).find('input').removeAttr("ondblclick");
				$(this).find("td").eq(1).find('input').removeAttr("placeholder");
				$(this).find("td").eq(1).find('input').attr("disabled","true");
			});

			//$(this).closest('tr').find('td').eq(0).find('input').val()
			//$(".printReport").removeAttr("onClick");
		}else if(basis ==5){
			$("#txtjobno_1").attr("disabled",false);
			$("#txtwono_1").attr("disabled",true);
			$("#booking_td").text("Fab.Booking");
		}else{
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

	

</script>
<body onLoad="set_hotkey()">
<div align="center">
    <div style="width:1050px;">
        <? echo load_freeze_divs ("../../",$permission);  ?><br />
    </div>
		<fieldset style="width:1100px">
			<form name="yarnRequisition_1" id="yarnRequisition_1" method="" >
				<table cellpadding="0" cellspacing="2" width="900" border="1" rules="all">
					<tr>
					  <td>&nbsp;</td>
					  <td>&nbsp;</td>
					  <td align="right">Requisition No&nbsp;</td><input type="hidden" name="is_approved" id="is_approved" value="">
					  <td><input type="text" name="txt_wo_number"  id="txt_wo_number" class="text_boxes" style="width:159px" placeholder="Double Click to Search" onDblClick="openmypage_req('x','WO Number Search');" readonly />
                      <input type="hidden" id="update_id" name="update_id" >
                      </td>
					  <td>&nbsp;</td>
					  <td>&nbsp;</td>
				  </tr>
					<tr>
						<td width="94" class="must_entry_caption" align="right">Company&nbsp;</td>
						<td width="211">
                        	<?
							   	echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "load_drop_down( 'requires/yarn_requisition_entry_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );load_drop_down( 'requires/yarn_requisition_entry_controller', this.value+'__'+1, 'load_drop_down_buyer1', 'buy_td' );print_button_setting();" );
 							?>
						</td>
                        <td width="122" class="must_entry_caption" align="right">Item Category&nbsp;</td>
						<td width="168">
                        	<?
							   	echo create_drop_down( "cbo_item_category", 170, $item_category,"", 1, "-- Select --", 1, "",1 );
 							?>
                        </td>
                        <!--this field temporary hidden-->
						<td width="118" style="display:none" align="right">Supplier&nbsp;</td>
						<td width="171" id="supplier_td" style="display:none">
						  	<?
							   	echo create_drop_down( "cbo_supplier", 170, $blank_array,"", 1, "-- Select --", 0, "",0 );
 							?>
						</td>
                         <!--this field temporary hidden-->
                         <td  class="must_entry_caption" align="right">Required Date&nbsp;</td>
						<td ><input type="text" name="txt_delivery_date"  id="txt_delivery_date" class="datepicker"  style="width:159px" value="<? echo date('d-m-Y')?>" /></td>
					</tr>
					<tr>

						<td  align="right">Pay Mode&nbsp;</td>
						<td ><?
							   	echo create_drop_down( "cbo_pay_mode", 170, $pay_mode,"", 1, "-- Select --", 0, "",0 );
 							?></td>
                        <td class="must_entry_caption" align="right">Requisition Date&nbsp;</td>
						<td ><input type="text" name="txt_wo_date" value="<? echo date('d-m-Y');?>" id="txt_wo_date" class="datepicker" style="width:159px" disabled/></td>
                        <td align="right" >Currency&nbsp;</td>
						<td ><?
							   	echo create_drop_down( "cbo_currency", 170, $currency,"", 1, "-- Select --", 2, "",0 );
 							?>						  <!-- when update and decrease row --></td>
					</tr>
					<tr>

						<td  align="right">Source&nbsp;</td>
						<td ><?
							   	echo create_drop_down( "cbo_source", 170, $source,"", 1, "-- Select --", 0, "",0 );
 							?></td>
						<td align="right">D/O No.&nbsp;</td>
						<td ><input type="text" name="txt_do_no"  id="txt_do_no" style="width:159px " class="text_boxes" /></td>
                        <td align="right">Attention&nbsp;</td>
						<td ><input type="text" name="txt_attention"  id="txt_attention" style="width:159px " class="text_boxes" /></td>
					</tr>
					<tr>
						<td align="right">Dealing Merchant&nbsp;</td>
						<td ><input type="text" name="txt_deal_march"  id="txt_deal_march" style="width:159px " class="text_boxes" /></td>

						<td align="right">Basis&nbsp;</td>
                        <td>
                        <?
                        	echo create_drop_down( "cbo_basis", 170, $issue_basis,"", 0, "-- Select--", 1, "fnc_booking_td_change()","","","","","3" );
                        ?>
                        </td>

                        <td align="right">Ready to Approve&nbsp;</td>
                        <td>
                        <?
                        	echo create_drop_down( "cbo_ready_to_approved", 170, $yes_no,"", 1, "-- Select--", 2, "","","" );
                        ?>
                        </td>
					</tr>
					<tr>
						<td align="right">Remarks&nbsp;</td>
						<td colspan="3"><input type="text" name="txt_remarks"  id="txt_remarks" style="width:492px " class="text_boxes" maxlength="150" /></td>
					</tr>

					<tr>
						<td align="center" height="10" colspan="6">
							<?
                            include("../../terms_condition/terms_condition.php");
                            terms_condition(70,'txt_wo_number','../../');
                            ?>

                            <!--<input type="button" id="set_button" class="image_uploader" style="width:100px; margin-left:30px; margin-top:2px;" value="Terms Condition" onClick="open_terms_condition_popup('requires/yarn_requisition_entry_controller.php?action=terms_condition_popup','Terms Condition')" />-->
                        </td>
					</tr>
                </table>
                <br />
                <table class="rpt_table" width="1080" cellspacing="0" cellpadding="0" id="tbl_purchase_item" border="1" rules="all">
                    <thead>
                        <th width="80" id="job_td">Job No</th>
                        <th width="80" id="booking_td">Fab.Booking</th>
                        <th width="90">Buyer Name</th>
                        <th width="120">Style</th>
                        <th width="80">Yarn Color</th>
                        <th width="70">Count</th>
                        <th width="100">Composition</th>
                        <th width="40">%</th>
                        <th width="100">Yarn Type</th>
                        <th width="60">UOM</th>
                        <th width="55" class="must_entry_caption">Requisition Qty.</th>
                        <th width="40">Rate</th>
                        <th width="55">Amount</th>
                        <th width="65">Yarn Inhouse Date</th>
                        <th width="80">Remarks</th>
                        <th > </th>
                    </thead>
                    <tbody id="details_part_list">
                        <tr class="general" id="tr_1">
                            <td align="center">
                            <input type="text" name="txtjobno_1" id="txtjobno_1" class="text_boxes" value="" style="width:75px;" onDblClick="openmypage_job(1)" placeholder="Doble Click For Job" readonly />

                            <input type="hidden" id="txtjobid_1" name="txtjobid_1" style="width:100px;">
                            <input type="hidden" name="txtrowid_1" id="txtrowid_1" class="text_boxes" value="" style="width:70px;" />
                            <input type="hidden" name="hiderow_1" id="hiderow_1" class="text_boxes" value="0" style="width:70px;" />
                            <input type="hidden" name="is_approved" id="is_approved" value="">
                            </td>
                            <td>
                              <input type="text" name="txtwono_1" id="txtwono_1" class="text_boxes" value="" style="width:75px;" onDblClick="openmypage_wo(1)" placeholder="Doble Click For WO Number" readonly />
                             <input type="hidden" id="txtwoid_1" name="txtwoid_1">

                            </td>

                            <td id="buy_td">
							<?
							   	echo create_drop_down( "cbobuyername_1", 90, "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select --", 0, "" );
 							?>
                            </td>
                            <td align="center"><input type="text" name="txtstyleno_1" id="txtstyleno_1" class="text_boxes" value="" style="width:75px;" /></td>
                            <td align="center"><input type="text" name="txtyarncolor[]" id="txtyarncolor_1" class="text_boxes" value="GREY" style="width:75px;" onFocus="add_auto_complete( 1 )" /></td>
                            <td align="center">
                            <?
								echo create_drop_down( "cbocount_1", 70, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1,"-- Select --", 0, "",0,"" );
							?>
                            </td>
                            <td align="center">
                            <?
								echo create_drop_down( "cbocompone_1", 100, $composition,"", 1, "-- Select --", 0, "",0,"","","",$ommitComposition );
							?>
                            </td>
                            <td><input type="text" name="txtpacent_1" id="txtpacent_1" class="text_boxes" value="100" style="width:40px;" /></td>
                            <td>
							<?
								echo create_drop_down( "cbotype_1", 100, $yarn_type,"", 1, "-- Select --", $row[csf("type_id")], "",$disabled,"","","",$ommitYarnType );
							?>
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
                            	<input type="text" name="txtrate_1" id="txtrate_1" class="text_boxes_numeric" value="" style="width:40px;" onKeyUp="calculate_amount(1)" />
                            	<input type="hidden" id="hiddentxtrate_1" name="hiddentxtrate_1" value=""/>
                            </td>
                            <td><input type="text" name="txtamount_1" id="txtamount_1" class="text_boxes_numeric" value="" style="width:50px;" readonly /></td>
                            <td><input class="datepicker" type="text" style="width:65px;" name="txtyarndate_1" id="txtyarndate_1" placeholder="Select Date" /></td>
                            <td><input type="text" name="txtremarks_1" id="txtremarks_1" class="text_boxes" value="" style="width:110px;" onClick="add_break_down_tr(1)" /></td>

                            <td><input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(1);" /></td>

                        </tr>
                    </tbody>
                </table>
				<table cellpadding="0" cellspacing="2" width="100%">
                	<tr>
				  		<td align="center" colspan="6" valign="middle" class="button_container">
				  		<div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
							<?
								echo load_submit_buttons( $permission, "fnc_yarn_req_entry", 0,0 ,"reset_form('yarnRequisition_1','','','','','');",1);
							?>
								<span id="button_data_panel"></span>

								<!-- echo load_submit_buttons( $permission, "fnc_yarn_order_entry", 0,0 ,"reset_form('yarnWorkOrder_1','approved*details_container','','','','cbo_item_category*cbo_currency');$('#cbo_company_name').attr('disabled',false);$('#cbo_wo_basis').attr('disabled',false);$('#ref_closed_msg_id').html('');",1); -->
							

                        <!-- <input type="button" class="formbutton_disabled printReport" id="print2" style="width:80px;" value="Print 2"  /> --><!-- onClick="fnc_yarn_req_entry(6)" -->
						<!-- <input type="button" class="formbutton_disabled printReport" id="print3" style="width:80px;" value="Print 3"  /> --><!--onClick="fnc_yarn_req_entry(7)" -->
						<!-- <input type="button" class="formbutton_disabled printReport" id="print4" style="width:80px;" value="Print 4"  /> --><!--onClick="fnc_yarn_req_entry(8)" -->
						</td>
					</tr>
				</table>
			</form>
		</fieldset>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
