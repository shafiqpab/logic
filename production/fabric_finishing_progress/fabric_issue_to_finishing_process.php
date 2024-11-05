<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create

Functionality	:
JS Functions	:
Created by		:	Ashraful
Creation date 	: 	20-3-2016
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
echo load_html_head_contents("Grey Fabric Issue Roll Wise","../../", 1, 1, $unicode,'','');
$grey_issue_basis=array(1=>"Booking",2=>"Independent",3=>"Knitting Plan");


?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';

	var tableFilters = 
	{
		col_1: "none",col_10: "none",col_12: "none",col_13: "none",col_19: "none",col_20: "none",col_21: "none",col_22: "none"
	} 

	var scanned_barcode=new Array();
	var scanned_batch_arr=new Array();
	<?
	$scanned_barcode_array=array(); $barcode_dtlsId_array=array(); $barcode_rollTableId_array=array();
	?>

	function openmypage_issue()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_service_source = $('#cbo_service_source').val();
		if (form_validation('cbo_company_id*cbo_service_source','Company*Source')==false)
		{
			return;
		}

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/fabric_issue_to_finishing_process_controller.php?cbo_company_id='+cbo_company_id+'&cbo_service_source='+cbo_service_source+'&action=issue_popup','Issue Popup', 'width=780px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var issue_id=this.contentDoc.getElementById("hidden_system_id").value;	 //challan Id and Number

			if(issue_id!="")
			{
				fnc_reset_form(1);
				get_php_form_data(issue_id, "populate_data_from_data", "requires/fabric_issue_to_finishing_process_controller");
				show_list_view(issue_id+"_"+cbo_company_id+"_"+cbo_service_source, 'grey_item_details_update', 'scanning_tbody','requires/fabric_issue_to_finishing_process_controller', 'setFilterGrid(\'scanning_tbl\',-1,tableFilters);' ); 
				set_button_status(1, permission, 'fnc_grey_roll_issue_to_subcon',1);
			}
		}
	}

	function generate_report_file(data,action)
	{
		window.open("requires/fabric_issue_to_finishing_process_controller.php?data=" + data+'&action='+action, true );
	}

	function fnc_grey_roll_issue_to_subcon( operation )
	{
		var service_source=$("#cbo_service_source").val();
		if(operation==2)
		{
			//show_msg('13');
			//return;
		}

		if(operation==4)
		{

			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_issue_no').val(),'subcon_issue_print','requires/aop_roll_receive_entry_controller');
			return;
		}

	 	if(form_validation('cbo_company_id*cbo_service_source*cbo_service_company*txt_issue_date','Company*Service Source*Service Company*Issue Date')==false)
		{
			return;
		}

		var m=0; var j=0; var dataString=''; var error=0; var totIssueQnty=0;
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			
			if(m>0)
			{
				var prifix=$(this).attr("id").split("_");
				
				var id=prifix[1];

				var cboProcess=$(this).find('select[name="cboProcess[]"]').val();
				var txt_issue_qty=$(this).find('input[name="txtIssueQty[]"]').val();
				var hidden_txt_issue_qty=$(this).find('input[name="hiddenTxtIssueQty[]"]').val();
				var txt_remarks=$(this).find('input[name="txtRemarks[]"]').val();
				var txtRollNo=$(this).find('input[name="txtRollNo[]"]').val();
				//alert(txt_issue_qty);
				totIssueQnty+=txt_issue_qty;

				if(cboProcess!=0)
				{
					/*if(form_validation('txtIssueQty_'+id,'Issue Qty.')==false)
					{
						error=1;
						return;
					}*/
				}
				if(txt_issue_qty!="")
				{
					if(form_validation('cboProcess_'+id,'Process')==false)
					{
						error=1;
						return;
					}
				}


				var batchId=$(this).find('input[name="batchId[]"]').val();
				var productId=$(this).find('input[name="productId[]"]').val();
				var bodypartId=$(this).find('input[name="bodypartId[]"]').val();
				var determinationId=$(this).find('input[name="determinationId[]"]').val();
				var colorId=$(this).find('input[name="colorId[]"]').val();
				var buyerId=$(this).find('input[name="buyerId[]"]').val();
				var orderId=$(this).find('input[name="orderId[]"]').val();
				var batchWgt=$(this).find('input[name="batchWgt[]"]').val();
				var widthTypeId=$(this).find('input[name="widthTypeId[]"]').val();
				var widthTypeId=$(this).find('input[name="widthTypeId[]"]').val();
				var finDia=$(this).find('input[name="finDia[]"]').val();
				var finGsm=$(this).find('input[name="finGsm[]"]').val();
				var booking_no=$(this).find('input[name="bookingNo[]"]').val();
				var woRate=$(this).find('input[name="woRate[]"]').val();
				var bookingType=$(this).find('input[name="bookingType[]"]').val();
				var bookingDate=$(this).find('input[name="bookingDate[]"]').val();
				var bookingDtls=$(this).find('input[name="bookingDtls[]"]').val();
				var processID=$(this).find('input[name="processId[]"]').val();
				var colorTypeId=$(this).find('input[name="colorTypeId[]"]').val();
				var isSales=$(this).find('input[name="isSales[]"]').val();
				//alert(cboProcess);
				if(operation==0)
				{
					if(cboProcess==35 && service_source==1)//AOP
					{
						var privCurrentQty=$(this).find('input[name="privCurrentQty[]"]').val();
						var batchtQty=$(this).find('input[name="batchtQty[]"]').val();
						var txtIssueQty=$(this).find('input[name="txtIssueQty[]"]').val();
						var issue_bal_chk=privCurrentQty*1+txtIssueQty*1;

						if(issue_bal_chk>batchtQty)
						{
							//alert('G');
							alert('Issue Qty is over then Batch Qty.\n'+'IssueQty:'+privCurrentQty+'\n BatchQty:'+batchtQty);
							error=1;
							return;
						}
					}
					else if(cboProcess==35 && service_source==3)//AOP
					{
						var privCurrentQty=$(this).find('input[name="privCurrentQty[]"]').val();
						var batchtQty=$(this).find('input[name="batchtQty[]"]').val();

						if(privCurrentQty<=batchtQty || batchtQty==0)
						{
							//alert('G');
							alert('Issue Qty is over then Batch Qty.\n'+'IssueQty:'+privCurrentQty+'\n BatchQty:'+batchtQty);
							error=1;
							return;
						}
					}
				}

				var outBoundBatchNo=$(this).find('input[name="txtBatchNo[]"]').val();

				// var jobNo=$(this).find("td:eq(18)").text();
				// var gsm=$(this).find("td:eq(7)").text();
				// var dia=$(this).find("td:eq(8)").text();

				var jobNo=$(this).find("td:eq(16)").text();
				var gsm=$(this).find("td:eq(5)").text();
				var dia=$(this).find("td:eq(6)").text();

				//alert("dia="+dia+", gsm="+gsm+", job="+jobNo);return;

				var dtlsId=$(this).find('input[name="dtlsId[]"]').val();


				j++;
				dataString+='&cboProcess_' + j + '=' + cboProcess + '&determinationId_' + j + '=' + determinationId + '&buyerId_' + j + '=' + buyerId + '&productId_' + j + '=' + productId + '&orderId_' + j + '=' + orderId + '&batchWgt_' + j + '=' + batchWgt + '&jobNo_' + j + '=' + jobNo + '&colorId_' + j + '=' + colorId + '&dtlsId_' + j + '=' + dtlsId + '&gsm_' + j + '=' + gsm + '&dia_' + j + '=' + dia+ '&batchId_' + j + '=' + batchId + '&bodypartId_' + j + '=' + bodypartId + '&txtIssueQty_' + j + '=' + txt_issue_qty+ '&txtRollNo_' + j + '=' + txtRollNo+ '&widthTypeId_' + j + '=' + widthTypeId+ '&tr_' + j + '=' + id+ '&finDia_' + j + '=' + finDia+ '&finGsm_' + j + '=' + finGsm+ '&outBoundBatchNo_' + j + '=' + outBoundBatchNo + '&bookingNo_' + j + '=' + booking_no + '&woRate_' + j + '=' + woRate+ '&bookingType_' + j + '=' + bookingType+ '&bookingDate_' + j + '=' + bookingDate + '&bookingDtls_' + j + '=' + bookingDtls + '&txtRemarks_' + j + '=' + txt_remarks+ '&hiddenTxtIssueQty_' + j + '=' + hidden_txt_issue_qty+ '&colorTypeId_' + j + '=' + colorTypeId+ '&isSales_' + j + '=' + isSales;
				
			}
			m++;
		});
		if (totIssueQnty<=0)
		{
			alert("Issue Qty 0 or Empty is not allow");
			return;
		}
		if(error==1)
		{
			return;
		}

		if(j<1)
		{
			alert('No data');
			return;
		}

		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_issue_no*cbo_company_id*cbo_service_source*cbo_service_company*txt_issue_date*cbo_basis*txt_car_no*txt_do_no*txt_gate_no*update_id',"../../")+dataString;
		freeze_window(operation);

		http.open("POST","requires/fabric_issue_to_finishing_process_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_grey_roll_issue_to_subcon_Reply_info;
	}

	function fnc_grey_roll_issue_to_subcon_Reply_info()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);

			if((response[0]==0 || response[0]==1))
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_issue_no').value = response[2];
				/*add_dtls_data(response[3]);
				$('#cbo_company_id').attr('disabled',true);
				$('#cbo_service_source').attr('disabled',true);
				$('#cbo_service_company').attr('disabled',true);
				$('#cbo_basis').attr('disabled',true);
				set_button_status(1, permission, 'fnc_grey_roll_issue_to_subcon',1);*/

				var cbo_company_id = $('#cbo_company_id').val();
				var cbo_service_source = $('#cbo_service_source').val();
				fnc_reset_form(1);
				get_php_form_data(response[1], "populate_data_from_data", "requires/fabric_issue_to_finishing_process_controller");
				show_list_view(response[1]+"_"+cbo_company_id+"_"+cbo_service_source, 'grey_item_details_update', 'scanning_tbody','requires/fabric_issue_to_finishing_process_controller', 'setFilterGrid(\'scanning_tbl\',-1,tableFilters);' );
				set_button_status(1, permission, 'fnc_grey_roll_issue_to_subcon',1);



			}
			if(response[0]=='Previous') //
			{
				alert("Previous Qty  :"+trim(response[3])+" Found in Issue Number "+ trim(response[2])+" \n So Not allow issue qty over than WO balance qty.\n Update Not Possible");
				release_freezing();
				return;
			}
			if(response[0]=='PreviousRecv') //
			{
				alert("Previous Receive Qty  :"+trim(response[3])+" Found in Receive Number "+ trim(response[2])+" \n So Not allow issue qty less than Receive qty.\n Update Not Possible");
				release_freezing();
				return;
			}
			if(response[0]==13) //
			{
				alert(response[1]);
				release_freezing();
				return;
			}
			if(response[0]==2)
			{
			fnc_reset_form(1);
			}

			release_freezing();
		}
	}

	function create_row_wo(is_update,book_id,book_no,booking_type,booking_date)
	{
		
		var book_id=trim(book_id);
		var row_num =$('#scanning_tbl tbody tr').length;
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_service_source = $('#cbo_service_source').val();
		//alert(booking_date);

		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		if(cbo_service_source==1)
		{
			var book_data=trim(return_global_ajax_value(book_id+"*"+book_no+"*"+booking_type+"*"+booking_date, 'populate_wo_data2', '', 'requires/fabric_issue_to_finishing_process_controller'));
		}
		else
		{
			var book_data=trim(return_global_ajax_value(book_id+"*"+book_no+"*"+booking_type, 'populate_wo_data', '', 'requires/fabric_issue_to_finishing_process_controller'));

		}

		$("#cboProcess_"+row_num).val(0);
		$("#productId_"+row_num).val("");
		$("#orderId_"+row_num).val("");
		$("#colorId_"+row_num).val("");
		$("#dtlsId_"+row_num).val('');
		$("#batchId_"+row_num).val('');
		$("#bodypartId_"+row_num).val('');
		$("#buyerId_"+row_num).val('');
		$("#determinationId_"+row_num).val('');
		$("#widthTypeId_"+row_num).val('');
		$("#txtIssueQty_"+row_num).val("");
		$("#hiddenTxtIssueQty_"+row_num).val("");
		$("#txtRemarks_"+row_num).val("");
		$("#txtRollNo_"+row_num).val("");
		$("#finDia_"+row_num).val("");
		$("#finGsm_"+row_num).val("");
		$("#bookingNo_"+row_num).val("");
		$("#bookingNoShow_"+row_num).text("");
		$("#woRate_"+row_num).val("");
		$("#bookingDate_"+row_num).val("");
		$("#bookingType_"+row_num).val("");
		$("#bookingDtls_"+row_num).val("");
		$("#batch_wo_td_id").text("BatchWgt/WO Qty");
		$(".wo_dtls_td").css("display","none");
		$("#privCurrentQty_"+row_num).val("");
		$("#batchtQty_"+row_num).val("");
		$("#internalRef_"+row_num).val("");
		$("#colorTypeId_"+row_num).val("");
		$("#colorTypeNO_"+row_num).text("");


		var book_datas=book_data.split("#");
		
		for(var k=0; k<book_datas.length; k++)
		{
			var last_tr_booking_no=$('#bookingNo_'+row_num).val();
			if(last_tr_booking_no!="")
			{
				row_num++;

				$("#scanning_tbl tbody tr:first").clone().find("td,input,select").each(function()
				{
					$(this).attr({
					  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
					  'value': function(_, value) { return value }
					});
				}).end().appendTo("#scanning_tbl");
				$("#scanning_tbl tbody tr:last").removeAttr('id').attr('id','tr_'+row_num);
			}
			//alert(book_datas[k]);
			var data=book_datas[k].split("**");
			var width_dia_type_id=data[0];
			var bodyPart=data[1];
			var construction=data[4];
			var gsm=data[2];
			var dia=data[3];
			//alert(data[19]);
			var color=data[5];
			var job_no=data[6];
			var po_id=data[7];
			var wo_qnty=data[8]*1;
			var color_id=data[9];
			var body_part_id=data[10];
			var determination_id=data[11];
			var rate=data[12];
			var process_id =data[13];
			var width_dia_type =data[14];
			var bookingDtlsId =data[15];
			var bookingDtlsBalance =data[16];

			var buyer_name =data[19];
			var job_no =data[20];
			var po_number =data[21];
			var buyer_id =data[22];
			var buyer_id_non_order =data[23];
			var buyer_name_non_order =data[24];
			var batch_no =data[25];
			var priv_currentQty =data[26]*1;
			var batchQty =data[27]*1;
			var batch_Id =data[28];
			var internalRef =data[29];
			var fabricColor =data[30];
			var colorTypeId =data[31];
			var colorTypeNO =data[32];

			var isSales="";
			if(booking_type==696)
			{
				isSales=1;
			}

			$("#sl_"+row_num).text(row_num);
			$("#bodyPart_"+row_num).text(bodyPart);
			$("#cons_"+row_num).text(construction);
			$("#gsm_"+row_num).text(gsm);
			$("#dia_"+row_num).text(dia);
			$("#color_"+row_num).text(color);
			$("#fabColor_"+row_num).text(fabricColor);
			$("#cboProcess_"+row_num).val(process_id);
			if(po_id!=0)
			{
				$("#buyer_"+row_num).text(buyer_name);
				$("#job_"+row_num).text(job_no);
				$("#order_"+row_num).text(po_number);
				$("#buyerId_"+row_num).val(buyer_id);
				$("#internalRef_"+row_num).text(internalRef);

			}
			else
			{
				$("#buyer_"+row_num).text(buyer_name_non_order);
				$("#buyerId_"+row_num).val(buyer_id_non_order);
			}
			$("#orderId_"+row_num).val(po_id);
			$("#txtBatchNo_"+row_num).val(batch_no);
			$("#batchId_"+row_num).val(batch_Id);
			$("#widthTypeId_"+row_num).val(width_dia_type_id);
			$("#diaType_"+row_num).text(width_dia_type);
			$("#batchWeight_"+row_num).text(wo_qnty);
			$("#batchWgt_"+row_num).val(wo_qnty);
			$("#colorId_"+row_num).val(color_id);
			$("#determinationId_"+row_num).val(determination_id);
			$("#bodypartId_"+row_num).val(body_part_id);
			$("#bookingType_"+row_num).val(booking_type);
			$("#processId_"+row_num).val(process_id);

			$("#bookingDate_"+row_num).val(booking_date);
			$("#bookingDtls_"+row_num).val(bookingDtlsId);

			$("#txtIssueQty_"+row_num).attr("placeholder",bookingDtlsBalance);
			$("#txtIssueQty_"+row_num).attr("title",bookingDtlsBalance);

			$("#colorTypeId_"+row_num).val(colorTypeId);
			$("#colorTypeNO_"+row_num).text(colorTypeNO);
			$("#isSales_"+row_num).val(isSales);


			//alert(priv_currentQty+'='+batchQty);
			if(process_id==35)//AOP
			{
				//if(priv_currentQty<=batchQty || batchQty==0)
				if(batchQty==0 || bookingDtlsBalance==0)
				{
					//alert('G');
					$("#issueQtyTd_"+row_num).css('background-color', 'red');
					$('#txtIssueQty_'+row_num).focus();
				}
			}

			$("#txtIssueQty_"+row_num).attr("onkeyup","fnc_calculate(this.id)");
			if(cbo_service_source==1)
			{
				if(process_id==35)//AOP
				{
				$("#txtBatchNo_"+row_num).attr("readonly",true);
				}
				else  { $("#txtBatchNo_"+row_num).attr("readonly",false);}
			}
			else
			{
				$("#txtBatchNo_"+row_num).attr("readonly",false);
			}

			//onkeyup="fnc_calculate(this.id)"
			//$("#productId_"+row_num).val(prod_id);
			$("#bookingNo_"+row_num).val(book_no);
			$("#bookingNoShow_"+row_num).text(book_no);
			$("#woRate_"+row_num).val(rate);
			$("#privCurrentQty_"+row_num).val(priv_currentQty);
			$("#batchtQty_"+row_num).val(batchQty);
			$('#increase_'+row_num).removeAttr("onclick").attr("onclick","fn_add_row("+row_num+");");
			$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fnc_remove_row("+row_num+");");

			
		}

		setFilterGrid("scanning_tbl",-1,tableFilters);

	}
	//var scanned_barcode=new Array();

	function add_dtls_data( data )
	{
		var batch_datas=data.split(",");
		for(var k=0; k<batch_datas.length; k++)
		{
			var datas=batch_datas[k].split("__");
			var tr_no=datas[0];
			var dtls_id=datas[1];
			$("#dtlsId_"+tr_no).val(dtls_id);
		}

	}


	function openmypage_batchNo()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_service_source = $('#cbo_service_source').val();
		var cbo_basis = $('#cbo_basis').val();
		var cbo_service_company = $('#cbo_service_company').val()
		var txt_batch_dtls = $('#txt_batch_dtls').val();



		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
	  	{
			if(cbo_basis==2)
			{
				if (form_validation('cbo_service_company','Service Company')==false)
				{
					return;
				}

				var dataString="";
				$("#scanning_tbl").find('tbody tr').each(function()
				{
					var booking_no=$(this).find('input[name="bookingNo[]"]').val();

					dataString+= booking_no+"_";
				});



				var page_link='requires/fabric_issue_to_finishing_process_controller.php?company_id='+cbo_company_id+'&cbo_service_source='+cbo_service_source+'&cbo_basis='+cbo_basis+'&supplier_id='+cbo_service_company+'&cbo_service_source='+cbo_service_source+'&bookingnos='+dataString+'&txt_batch_dtls='+txt_batch_dtls+'&action=service_booking_popup';
				var title='WO Number Popup';
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1320px,height=390px,center=1,resize=1,scrolling=0','../');
				emailwindow.onclose=function()
				{
					//alert();
					var theform=this.contentDoc.forms[0];
					if(cbo_service_source!=1)
					{
						var theemail=this.contentDoc.getElementById("selected_booking");
						if (theemail.value!="")
		  				{
							//'198_FAL-FSWO-23-00026_FAL-23-02595__30-DEC-23_1000_FSWO_'
						var booking_data=(theemail.value).split("_");
		  				var book_no=booking_data[1];
		  				var book_id=booking_data[0];
						var job_no=booking_data[2];
						var booking_type=booking_data[3];
						var booking_date=booking_data[4];
						var booking_balance=booking_data[5];
						var booking_format=booking_data[6];
						var process_id=booking_data[7];

						var color_id=1;
						var service_source=1;
						$('#txt_batch_no').val(book_no);

						if(booking_format=="NAP" || process_id==35)
						{
							//alert(56);
							//return;
							//new business will be implemented here
						}
						if(book_id!="")
						{
							//$('#txt_batch_no').val(batch_no);
							//$('#txt_batch_id').val(batch_id);

							if( jQuery.inArray( book_id, scanned_batch_arr )>-1)
							{
								alert('Sorry! Booking Already Scanned.');
								$('#txt_batch_no').val('');
								$('#txt_batch_id').val('');
								return;
							}
							//alert(book_id);
							//check_batch(batch_no);
							scanned_batch_arr.push(book_id);
							create_row_wo(0,book_id,book_no,booking_type,booking_date,booking_format);
						}
		  			 }
					}
					else
					{
						var theemail2=this.contentDoc.getElementById("selected_batchDtls");
						var theemail3=this.contentDoc.getElementById("booking_no");
						var theemail4=this.contentDoc.getElementById("booking_id");
						var book_no =theemail3.value;
						var book_id =theemail4.value;

						var booking_type=1;
						$('#txt_batch_no').val(book_no);
						$('#txt_batch_dtls').val(theemail2.value);
						var txt_batch_dtls=$('#txt_batch_dtls').val();
						scanned_batch_arr.push(book_id);
						
						create_row_wo(0,book_id,book_no,booking_type,txt_batch_dtls);
					}

					//alert(theemail.value);
					//return;


		  			$('#cbo_basis').attr("disabled",true);
				}
			}

		}

	}

	$('#txt_batch_no').live('keydown', function(e) {
		if (e.keyCode === 13)
		{
			e.preventDefault();
			var batch_no=$('#txt_batch_no').val();
			check_batch(batch_no.trim());
			$('#txt_batch_no').val('');
		}
	});


	function check_batch(data)
	{
		if(data=="")
		{
			$('#txt_batch_id').val('');
			return;
		}
		var cbo_company_id=$('#cbo_company_id').val();
		var cbo_service_source=$('#cbo_service_source').val();
		var cbo_basis=$('#cbo_basis').val();
		var cbo_service_company=$('#cbo_service_company').val();
		if (form_validation('cbo_company_id*cbo_service_source*cbo_basis','Company*Source*Basis')==false)
		{
			return;
		}
		if(cbo_basis == 1){
			var batch_id_data=return_global_ajax_value( data+"**"+cbo_company_id+"**"+cbo_service_source, 'check_batch_no', '', 'requires/fabric_issue_to_finishing_process_controller');
		}
		else if(cbo_basis == 2)
		{
			if (form_validation('cbo_service_company','Service Company')==false)
			{
				return;
			}
			var booking_data=return_global_ajax_value( data+"**"+cbo_company_id+"**"+cbo_service_company+"**"+cbo_basis, 'check_booking_no', '', 'requires/fabric_issue_to_finishing_process_controller');
		}
		//alert(booking_data);
		if(cbo_basis == 2)
		{
			if(booking_data==0)
			{
				alert("Booking Not Found");
				$('#txt_batch_no').val('');
				$('#txt_batch_id').val('');
				return;
			}
			else
			{
				book_id_data=booking_data.split("**");
				var booking_id=book_id_data[0];
				var booking_no=book_id_data[1];
				var booking_type=book_id_data[2];
				var booking_date=book_id_data[3];

				if( jQuery.inArray( booking_id, scanned_batch_arr )>-1)
				{
					alert('Sorry! Booking Already Scanned.');
					$('#txt_batch_no').val('');
					$('#txt_batch_id').val('');
					return;
				}

				scanned_batch_arr.push(booking_id);
				create_row_wo(0,booking_id,booking_no,booking_type,booking_date)
			}
		}

	}

	function fnc_reset_form(type)
	{
		$('#scanning_tbl tbody tr').remove();

		var html='<tr id="tr_1" align="center" valign="middle"><td width="30" id="sl_1"></td><td width="90" align="right" id=""><input type="text" id="txtBatchNo_1" name="txtBatchNo[]"  style=" width:70px" class="text_boxes_numeric"/></td><td style="word-break:break-all;" width="90" id="bodyPart_1"></td><td style="word-break:break-all;" width="90" id="colorTypeNO_1"></td><td style="word-break:break-all;" width="130" id="cons_1" align="left"></td><td style="word-break:break-all;" width="50" id="gsm_1"></td><td style="word-break:break-all;" width="50" id="dia_1"></td><td style="word-break:break-all;" width="70" id="color_1"></td><td style="word-break:break-all;" width="70" id="fabColor_1"></td><td style="word-break:break-all;" width="70" id="diaType_1"></td><td width="100" align="right" id=""><? echo create_drop_down( "cboProcess_1", 120, $conversion_cost_head_array,"", 1, "-- Select Process --", 11, "","1","","","","","","","cboProcess[]" ); ?></td><td width="70" align="right" id="batchWeight_1"></td><td width="80" align="right" id=""><input type="text" id="txtRollNo_1" name="txtRollNo[]"  style=" width:68px" class="text_boxes_numeric"/></td><td width="80" align="right" id=""><input type="text" id="txtIssueQty_1" name="txtIssueQty[]"  style=" width:68px" class="text_boxes_numeric"/></td><td style="word-break:break-all;" width="100" id="bookingNoShow_1"></td><td style="word-break:break-all;" width="60" id="buyer_1"></td><td style="word-break:break-all;" width="80" id="job_1"></td><td style="word-break:break-all;" width="80" id="order_1"></td><td style="word-break:break-all;" width="100" id="internalRef_1"></td><td width="80" align="right" id=""><input type="text" id="txtRemarks_1" name="txtRemarks[]" style=" width:80px" class="text_boxes"/></td><td width="90" id="batchNo_1" class="wo_dtls_td" style="display: none;"></td><td width="60" id="prodId_1" class="wo_dtls_td" style="display: none;"></td><td id="button_1" align="center"><input type="button" id="decrease_1" name="decrease[]" style="width:20px" class="formbuttonplasminus" value="-" onClick="fnc_remove_row(1);" /> &nbsp;<input type="hidden" name="recvBasis[]" id="recvBasis_1"/><input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/><input type="hidden" name="productId[]" id="productId_1"/><input type="hidden" name="orderId[]" id="orderId_1"/><input type="hidden" name="batchWgt[]" id="batchWgt_1"/><input type="hidden" name="colorId[]" id="colorId_1"/><input type="hidden" name="dtlsId[]" id="dtlsId_1"/><input type="hidden" name="batchId[]" id="batchId_1"/><input type="hidden" name="finDia[]" id="finDia_1"/><input type="hidden" name="finGsm[]" id="finGsm_1"/><input type="hidden" name="bookingNo[]" id="bookingNo_1"/><input type="hidden" name="woRate[]" id="woRate_1"/><input type="hidden" name="determinationId[]" id="determinationId_1"/><input type="hidden" name="widthTypeId[]" id="widthTypeId_1"/><input type="hidden" name="bodypartId[]" id="bodypartId_1"/><input type="hidden" name="colorTypeId[]" id="colorTypeId_1"/><input type="hidden" name="buyerId[]" id="buyerId_1"/><input type="hidden" name="processId[]" id="processId_1"/><input type="hidden" name="bookingDate[]" id="bookingDate_1"/><input type="hidden" name="hiddenTxtIssueQty[]" id="hiddenTxtIssueQty_1"/><input type="hidden" name="bookingType[]" id="bookingType_1"/><input type="hidden" name="bookingDtls[]" id="bookingDtls_1"/><input type="hidden" name="isSales[]" id="isSales_1"/></td></tr>';

		if(type == 1)
		{
			$('#cbo_company_id').val(0);
			$('#cbo_company_id').attr('disabled',false);
			$('#cbo_service_source').val(0);
			$('#cbo_service_source').attr('disabled',false);
			$('#cbo_service_company').val(0);
			$('#cbo_service_company').attr('disabled',false);
			$('#cbo_basis').val(0);
			$('#cbo_basis').attr('disabled',false);
			$('#txt_issue_date').val('');
		}

		scanned_batch_arr.length = 0;


		$('#txt_tot_row').val(1);
		$('#update_id').val('');
		$('#txt_issue_no').val('');

		$('#txt_deleted_id').val('');
		$('#cbo_process').val(0);
		$('#txt_batch_no').val('');
		$('#txt_batch_dtls').val('');
		$('#txt_batch_id').val('');
		$("#scanning_tbl tbody").html(html);
	}

	function fn_add_row(row_id)
	{
		var row_num =($('#scanning_tbl tbody tr').length)+1;
		$("#tr_"+row_id).clone().find("td,input,select").each(function()
		{
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
			  'value': function(_, value) { return value }
			});
		}).end().insertAfter("#tr_"+row_id);
		$("#tr_"+row_id).next(this).removeAttr('id').attr('id','tr_'+row_num);

		$('#increase_'+row_num).removeAttr("onclick").attr("onclick","fn_add_row("+row_num+");");
		$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fnc_remove_row("+row_num+");");
		$('#dtlsId_'+row_num).val('');

		var sl=0;
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			sl++;
			$(this).find("td:eq(0)").text(sl);
		});
	}

	function fnc_remove_row( id )
	{
		var id=(id*1);
		if(id>0)
		{
			var index = $('#tr_'+id).closest("tr").index();
			$("#scanning_tbody  tr:eq("+index+")").remove()
		}

		var row_num =($('#scanning_tbl tbody tr').length);

		if(row_num==0)
		{
			var html='<tr id="tr_1" align="center" valign="middle"><td width="30" id="sl_1"></td><td width="90" align="right" id=""><input type="text" id="txtBatchNo_1" name="txtBatchNo[]"  style=" width:70px" class="text_boxes_numeric"/></td><td style="word-break:break-all;" width="90" id="bodyPart_1"></td><td style="word-break:break-all;" width="90" id="colorTypeNO_1"></td><td style="word-break:break-all;" width="130" id="cons_1" align="left"></td><td style="word-break:break-all;" width="50" id="gsm_1"></td><td style="word-break:break-all;" width="50" id="dia_1"></td><td style="word-break:break-all;" width="70" id="color_1"></td><td style="word-break:break-all;" width="70" id="fabColor_1"></td><td style="word-break:break-all;" width="70" id="diaType_1"></td><td width="100" align="right" id=""><? echo create_drop_down( "cboProcess_1", 120, $conversion_cost_head_array,"", 1, "-- Select Process --", 11, "","1","","","","","","","cboProcess[]" ); ?></td><td width="70" align="right" id="batchWeight_1"></td><td width="80" align="right" id=""><input type="text" id="txtRollNo_1" name="txtRollNo[]"  style=" width:68px" class="text_boxes_numeric"/></td><td width="80" align="right" id=""><input type="text" id="txtIssueQty_1" name="txtIssueQty[]"  style=" width:68px" class="text_boxes_numeric"/></td><td style="word-break:break-all;" width="100" id="bookingNoShow_1"></td><td style="word-break:break-all;" width="60" id="buyer_1"></td><td style="word-break:break-all;" width="80" id="job_1"></td><td style="word-break:break-all;" width="80" id="order_1"></td><td style="word-break:break-all;" width="100" id="internalRef_1"></td><td width="80" align="right" id=""><input type="text" id="txtRemarks_1" name="txtRemarks[]" style=" width:80px" class="text_boxes"/></td><td width="90" id="batchNo_1" class="wo_dtls_td" style="display: none;"></td><td width="60" id="prodId_1" class="wo_dtls_td" style="display: none;"></td><td id="button_1" align="center"><input type="button" id="decrease_1" name="decrease[]" style="width:20px" class="formbuttonplasminus" value="-" onClick="fnc_remove_row(1);" /> &nbsp;<input type="hidden" name="recvBasis[]" id="recvBasis_1"/><input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/><input type="hidden" name="productId[]" id="productId_1"/><input type="hidden" name="orderId[]" id="orderId_1"/><input type="hidden" name="batchWgt[]" id="batchWgt_1"/><input type="hidden" name="colorId[]" id="colorId_1"/><input type="hidden" name="dtlsId[]" id="dtlsId_1"/><input type="hidden" name="batchId[]" id="batchId_1"/><input type="hidden" name="finDia[]" id="finDia_1"/><input type="hidden" name="finGsm[]" id="finGsm_1"/><input type="hidden" name="bookingNo[]" id="bookingNo_1"/><input type="hidden" name="woRate[]" id="woRate_1"/><input type="hidden" name="determinationId[]" id="determinationId_1"/><input type="hidden" name="widthTypeId[]" id="widthTypeId_1"/><input type="hidden" name="bodypartId[]" id="bodypartId_1"/><input type="hidden" name="colorTypeId[]" id="colorTypeId_1"/><input type="hidden" name="buyerId[]" id="buyerId_1"/><input type="hidden" name="processId[]" id="processId_1"/><input type="hidden" name="bookingDate[]" id="bookingDate_1"/><input type="hidden" name="hiddenTxtIssueQty[]" id="hiddenTxtIssueQty_1"/><input type="hidden" name="bookingType[]" id="bookingType_1"/><input type="hidden" name="bookingDtls[]" id="bookingDtls_1"/><input type="hidden" name="isSales[]" id="isSales_1"/></td></tr>';
			
			$("#scanning_tbl tbody").html(html);
			$('#txt_tot_row').val(1);
			scanned_batch_arr.length = 0;
		}
	}

	function fnc_process_issue_to_subcon(operation)
	{
		var report_title=$( "div.form_caption" ).html();

		var show_item='';
		var r=confirm("Press  \"Cancel\"  to hide  Buyer &  Style\nPress  \"OK\"  to Show Buyer &  Style");
		if (r==true)
		{
			show_item="1";
		}
		else
		{
			show_item="0";
		}
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_issue_no').val()+'*'+show_item,'subprocess_issue_print','requires/fabric_issue_to_finishing_process_controller');
			return;
			//generate_report_file();
			 //var report_title=$( "div.form_caption" ).html();
			// print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_issue_no').val(), "subprocess_issue_print", "requires/fabric_issue_to_finishing_process_controller" ) ;

			// show_msg("3");
	}

	function fnc_process_issue_to_subcon_print3()
	{
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_issue_no').val(),'subprocess_issue_print3','requires/fabric_issue_to_finishing_process_controller');
		return;
	}
	function fnc_process_issue_to_subcon_print4()
	{
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_issue_no').val(),'subprocess_issue_print4','requires/fabric_issue_to_finishing_process_controller');
		return;
	}

	function fnc_process_issue_to_subcon_print5()
	{
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_issue_no').val(),'subprocess_issue_print5','requires/fabric_issue_to_finishing_process_controller');
		return;
	}

function fnc_basis_popup_chng(id)
{
	if(id==1)
	{
		document.getElementById("basis_title_td").innerHTML="Batch No";
	}
	else
	{
		document.getElementById("basis_title_td").innerHTML="WO No";
	}
}
function fnc_calculate(id)
{
	var qnty = $("#"+id).val();
	var update_id=$("#update_id").val();
	var place_val=$("#"+id).attr("placeholder")*1;
	var curr_IssueQty=$("#txtIssueQty_"+id).val();
	var prev_issue_qty=$("#"+id).attr("title")*1;
	var chk_issue_qty=(prev_issue_qty+place_val);
	//alert(qnty+'='+chk_issue_qty);
	if(update_id=="") //save
	{
		if(qnty > place_val)
		{
			//$("#"+id).val("");
			$("#"+id).val(place_val);
		}
	}
	else //Update
	{
		//if(qnty > place_val)
		if(qnty > chk_issue_qty)
		{
			//$("#"+id).val("");
			$("#"+id).val(prev_issue_qty);
		}
	}
}
function fn_fab_issueToFinishingProcess_print(){

	if( form_validation('cbo_company_id*cbo_service_source*cbo_service_company','Company Name*Service Source*Service Company')==false )
	{
		return;
	}
	var company = $("#cbo_company_id").val();
	var source = $("#cbo_service_source").val();
	var service_company = $("#cbo_service_company").val();
	var page_link='requires/fabric_issue_to_finishing_process_controller.php?action=issue_multy_number_popup&company='+company+'&source='+source+'&service_company='+service_company;
	var title="Search Subcon Issue Number Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=410px,center=1,resize=0,scrolling=0',' ')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var returnNumber=this.contentDoc.getElementById("hidden_return_number").value; // mrr number
		var report_title=$( "div.form_caption" ).html();
		var show_item='';
		var r=confirm("Press  \"OK\"  to Hide Buyer Style & Order\nPress  \"Cancel\"  to Show Buyer Style & Order");
		if (r==true)
		{
			show_item="1";
		}
		else
		{
			show_item="0";
		}
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+returnNumber+'*'+$('#cbo_service_source').val()+'*'+$('#cbo_service_company').val()+'*'+show_item, "subprocess_issue_print_3", "requires/fabric_issue_to_finishing_process_controller" )
		return;
	}
}

//$(".wo_dtls_td").css("display","hide");
</script>
</head>
<body onLoad="set_hotkey();">
    <div align="center" style="width:100%;">
		<? echo load_freeze_divs ("../../",$permission); ?>
        <form name="rollscanning_1" id="rollscanning_1"  autocomplete="off"  >
            <fieldset style="width:810px;">
				<legend>Roll Scanning</legend>
                <table cellpadding="0" cellspacing="2" width="800">
                    <tr>
                        <td colspan="2"></td>
                        <td align="center"><b>Issue No</b></td>
                        <td>
                        	<input type="text" name="txt_issue_no" id="txt_issue_no" class="text_boxes" style="width:140px;" onDblClick="openmypage_issue()" placeholder="Double Click To Search" readonly/>
                            <input type="hidden" name="update_id" id="update_id"/>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td align="right" class="must_entry_caption">Company</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "get_php_form_data( this.value, 'company_wise_report_button_setting','requires/fabric_issue_to_finishing_process_controller' );",0 );
                            ?>
                        </td>
                        <td class="must_entry_caption" align="right">Service Source</td>
                        <td>
							<?
                                echo create_drop_down( "cbo_service_source", 152, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'requires/fabric_issue_to_finishing_process_controller',this.value+'**'+$('#cbo_company_id').val(),'load_drop_down_knitting_com','dyeing_company_td' );","","1,3" );
                            ?>
                        </td>
                        <td align="right" class="must_entry_caption">Service Company</td>
                        <td id="dyeing_company_td">
                            <?
                                echo create_drop_down( "cbo_service_company", 152, $blank_array,"", 1, "-- Select --", $selected, "","","" );
                            ?>
                        </td>
                    </tr>
                    <tr>
                    	<td align="right" class="must_entry_caption" width="100">Issue Date</td>
                        <td><input type="text" name="txt_issue_date" id="txt_issue_date" class="datepicker" style="width:140px;" readonly /></td>
                        <td align="right">Basis</td>
                        <td>
                        	 <?
                                echo create_drop_down( "cbo_basis", 152, $fabric_issue_basis,"", 1, "-- Select --", 1, "fnc_basis_popup_chng(this.value);","","2","" );
                            ?>
                        </td>
                         <td align="right">Gate Pass No</td>
                        <td>
                        	<input type="text" name="txt_gate_no" id="txt_gate_no" class="text_boxes" style="width:140px;" />
                        </td>
                    </tr>
                    <tr>
                    	<td align="right"  width="100">DO No</td>
                        <td><input type="text" name="txt_do_no" id="txt_do_no" class="text_boxes" style="width:140px;"  /></td>
                         <td id="basis_title_td" align="right">Batch No</td>
                        <td>
                        	<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:140px;" placeholder="Browse/Write/scan" onDblClick="openmypage_batchNo();" onChange="check_batch(this.value);" />
                            <input type="hidden" id="txt_batch_id" />
                             <input type="hidden" id="txt_batch_dtls" />

                        </td>
                        <td align="right">Vehicle No</td>
                        <td>
                        	<input type="text" name="txt_car_no" id="txt_car_no" class="text_boxes" style="width:140px;"  />

                        </td>

                    </tr>

                </table>
			</fieldset>
			<br>
			<fieldset style="width:1390px;text-align:left">
				<style>
                    #scanning_tbl tr td
                    {
                        background-color:#FFF;
                        color:#000;
                        border: 1px solid #666666;
                        line-height:12px;
                        height:20px;
                        overflow:auto;
                    }
                </style>
				<table cellpadding="0" width="1640" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="30">SL</th>
                        <th width="90">Batch No</th>
                        <th width="60" class="wo_dtls_td" style="display: none;">Product Id</th>
                        <th width="90">Body Part</th>
                        <th width="90">Color Type</th>
                        <th width="130">Construction/ Composition</th>
                        <th width="50">GSM</th>
                        <th width="50">Dia</th>
                        <th width="70">Gmts.Color</th>
                        <th width="70">Fab. Color</th>
                        <th width="70">Dia/Width Type</th>
                        <th width="120">Process</th>
                        <th width="70" id="batch_wo_td_id">Batch Wgt./WO Qnty</th>
                        <th width="80">Roll No</th>
                        <th width="80">Issue Qty.</th>
                        <th width="100">Booking No</th>
                        <th width="60">Buyer</th>
                        <th width="80">Job No</th>
                        <th width="80">Order No</th>
                        <th width="100">Internal Ref.</th>
                        <th width="80">Remarks</th>
                        <th></th>
                    </thead>
                </table>
                <div style="width:1660px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="1640" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody id="scanning_tbody">
                        	<tr id="tr_1" align="center" valign="middle">
                                <td width="30" id="sl_1"></td>
                                <td width="90" align="right" id=""><input type="text" id="txtBatchNo_1" name="txtBatchNo[]"  style=" width:70px" class="text_boxes"/></td>
                                <td style="word-break:break-all;" width="90" id="bodyPart_1"></td>
                                <td style="word-break:break-all;" width="90" id="colorTypeNO_1"></td>
                                <td style="word-break:break-all;" width="130" id="cons_1" align="left"></td>
                                <td style="word-break:break-all;" width="50" id="gsm_1"></td>
                                <td style="word-break:break-all;" width="50" id="dia_1"></td>
                                <td style="word-break:break-all;" width="70" id="color_1"></td>
                                <td style="word-break:break-all;" width="70" id="fabColor_1"></td>
                                <td style="word-break:break-all;" width="70" id="diaType_1"></td>
                                <td width="100" align="right" id="">
                                	<?
										echo create_drop_down( "cboProcess_1", 120, $conversion_cost_head_array,"", 1, "-- Select Process --", "", "","1","","","","","","","cboProcess[]" );
									?>
                                </td>
                                <td width="70" align="right" id="batchWeight_1"></td>
                                <td width="80" align="right" id=""><input type="text" id="txtRollNo_1" name="txtRollNo[]"  style=" width:68px" class="text_boxes_numeric"/></td>
                                 <td width="80" align="right" id="issueQtyTd_1"><input type="text" id="txtIssueQty_1" name="txtIssueQty[]"  style=" width:68px" class="text_boxes_numeric" placeholder="" onKeyUp="fnc_calculate(this.id)"/></td>
                                <td style="word-break:break-all;" width="100" id="bookingNoShow_1"></td>
                                <td style="word-break:break-all;" width="60" id="buyer_1"></td>
                                <td style="word-break:break-all;" width="80" id="job_1"></td>
                                <td style="word-break:break-all;" width="80" id="order_1" align="left"></td>
                                <td style="word-break:break-all;" width="100" id="internalRef_1" align="left"></td>
                                <td style="word-break:break-all;" width="80" id="remarks_1" align="left"></td>
								<td width="90" id="batchNo_1" class="wo_dtls_td" style="display: none;"></td>
                                <td width="60" id="prodId_1" class="wo_dtls_td" style="display: none;"></td>
                                <td id="button_1" align="center">
                                    <!-- <input type="button" id="increase_1" name="increase[]" style="width:20px" class="formbuttonplasminus" value="+" onClick="fn_add_row(1);" /> -->&nbsp; 	<input type="button" id="decrease_1" name="decrease[]" style="width:20px" class="formbuttonplasminus" value="-" onClick="fnc_remove_row(1);" />
                                    <input type="hidden" name="recvBasis[]" id="recvBasis_1"/>
                                    <input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/>
                                    <input type="hidden" name="productId[]" id="productId_1"/>
                                    <input type="hidden" name="orderId[]" id="orderId_1"/>
                                    <input type="hidden" name="processId[]" id="processId_1"/>
                                    <input type="hidden" name="batchWgt[]" id="batchWgt_1"/>
                                    <input type="hidden" name="colorId[]" id="colorId_1"/>
                                    <input type="hidden" name="dtlsId[]" id="dtlsId_1"/>
                                    <input type="hidden" name="batchId[]" id="batchId_1"/>
                                    <input type="hidden" name="bodypartId[]" id="bodypartId_1"/>
                                    <input type="hidden" name="colorTypeId[]" id="colorTypeId_1"/>
                                    <input type="hidden" name="buyerId[]" id="buyerId_1"/>
                                    <input type="hidden" name="determinationId[]" id="determinationId_1"/>
                                    <input type="hidden" name="widthTypeId[]" id="widthTypeId_1"/>
                                    <input type="hidden" name="finDia[]" id="finDia_1"/>
                                    <input type="hidden" name="finGsm[]" id="finGsm_1"/>
                                    <input type="hidden" name="bookingNo[]" id="bookingNo_1"/>
                                    <input type="hidden" name="woRate[]" id="woRate_1"/>
                                    <input type="hidden" name="bookingType[]" id="bookingType_1"/>
                                    <input type="hidden" name="hiddenTxtIssueQty[]" id="hiddenTxtIssueQty_1"/>
                                    <input type="hidden" name="bookingDate[]" id="bookingDate_1"/>
                                    <input type="hidden" name="bookingDtls[]" id="bookingDtls_1"/>
                                    <input type="hidden" name="privCurrentQty[]" id="privCurrentQty_1"/>
                                    <input type="hidden" name="batchtQty[]" id="batchtQty_1"/>
                                    <input type="hidden" name="remarks_[]" id="remarks_1"/>
                                    <input type="hidden" name="isSales[]" id="isSales_1"/>
                                </td>
                            </tr>
                        </tbody>
                	</table>
                </div>
                <br>
                <table width="1480" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                        	<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
                            <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes" value="">
                            <?
                            	echo load_submit_buttons($permission,"fnc_grey_roll_issue_to_subcon",0,0,"fnc_reset_form(1)",1);
                            ?>
							<input type="button" id="print_report" name="print_report" style="width:60px" class="formbuttonplasminus" value="Print " onClick="fnc_grey_roll_issue_to_subcon(4);" />

                            <input type="button" id="print_report2" name="print_report2" style="width:60px" class="formbuttonplasminus" value="Print 2" onClick="fnc_process_issue_to_subcon(4);" />

							<input type="button" id="print_report3" name="print_report3" style="width:60px" class="formbuttonplasminus" value="Print 3" onClick="fnc_process_issue_to_subcon_print3();" />
							<input type="button" id="print_report4" name="print_report4" style="width:60px" class="formbuttonplasminus" value="Print 4" onClick="fnc_process_issue_to_subcon_print4();" />
                           <input type="button" name="print_multiple_subcon" value="Print Multiple Subcon Issue No" id="print_multiple_subcon" class="formbutton" style="width: 180px;" onClick="fn_fab_issueToFinishingProcess_print()" />
						   <input type="button" id="print_report5" name="print_report5" style="width:60px" class="formbuttonplasminus" value="Print 5" onClick="fnc_process_issue_to_subcon_print5();" />

                        </td>
                    </tr>
                </table>
			</fieldset>
        </form>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>
