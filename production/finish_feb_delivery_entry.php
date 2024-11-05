<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Finish febric delivery

Functionality	:
JS Functions	:
Created by		:	Jahid
Creation date 	: 	04-12-2014
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
//$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][54] );

//$data_arr= $_SESSION['logic_erp']['data_arr'][54];
//$data_arr['action_company_id']='cbo_company_id';
//$data_arr= json_encode($data_arr);

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Finish febric delivery Entry","../", 1, 1, $unicode,1,1);
?>
<script>
	if ($('#index_page', window.parent.document).val() != 1)
		window.location.href = "../../logout.php";

	var permission = '<? echo $permission; ?>';
	<?
	//echo "var field_level_data= ". $data_arr . ";\n";
	if(isset(  $_SESSION['logic_erp']['data_arr'][54] ))
	{
		$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][54] );
		echo "var field_level_data= ". $data_arr . ";\n";
	}else{
		echo "var field_level_data= '';\n";
	}

	?>
	
	function generate_list(action_type)
	{
		var cbo_dyeing_source=$('#cbo_dyeing_source').val();
		if(cbo_dyeing_source == 1)
		{
			if(form_validation('cbo_company_id*cbo_dyeing_source*cbo_dyeing_company*cbo_location_dyeing','Company Name*Dyeing Source*Dyeing Company*Dyeing Location')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_id*cbo_dyeing_source*cbo_dyeing_company','Company Name*Dyeing Source*Dyeing Company')==false)
			{
				return;
			}
		}

		if(action_type == 0)
		{
			if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][54]);?>')
			{
				if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][54]);?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][54]);?>')==false)
				{
					return;
				}
			}
		}

 
		var is_sales = $("#cbo_is_sales").val();
		if(is_sales==1){
			var action = "list_generate_sales";
		}else{
			var action = "list_generate";
		}
		var data="action="+action+""+get_submitted_data_string("cbo_company_id*cbo_location_id*cbo_buyer_id*cbo_dyeing_source*cbo_dyeing_company*cbo_location_dyeing*txt_sys_prod_id*txt_batch_no*cbo_year*txt_job_no*txt_ord_no*txt_date_from*txt_date_to*cbo_status*update_mst_id*hidden_receive_id*hidden_product_id*hidden_order_id*cbo_order_status*txt_file_no*txt_ref_no*cbo_is_sales","../");
		freeze_window(4);
		http.open("POST","requires/finish_feb_delivery_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		//http.onreadystatechange = fn_generate_list_response;
		http.onreadystatechange=function () {
			if(http.readyState == 4)
			{
				var response=trim(http.responseText).split("####");

				$('#list_view_container').html(response[0]);
				document.getElementById('report_container').innerHTML='<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				setFilterGrid('table_body',-1);

				if($("#txt_sys_num").val() == "")
				{
					show_msg('4');
				}
				
				if(action_type == 1){
					set_button_status(1, permission, 'fnc_prod_delivery',1,1);
				}
				release_freezing();
			}
		};
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tbody').find('tr:first').hide();
		$('#tbl_header thead').find('th:nth-child(18),th:nth-child(19)').hide();
		$('#table_body tbody tr').find('td:nth-child(18),td:nth-child(19)').hide();
		$('#tbl_footer tfoot').find('th:nth-child(5),th:nth-child(6)').hide();
		$('#tbl_footer tfoot').find('th:nth-child(4)').attr("width","100");
		$('#tbl_footer tfoot').find('th:nth-child(3)').attr("width","100");
		$('#tbl_footer tfoot').find('th:nth-child(2)').attr("width","100");
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><title></title><link rel="stylesheet" href="../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_print').innerHTML+'</body</html>');
		d.close();
		$('#table_body tbody').find('tr:first').show();
		$('#tbl_header thead').find('th:nth-child(18),th:nth-child(19)').show();
		$('#table_body tbody tr').find('td:nth-child(18),td:nth-child(19)').show();
		$('#tbl_footer tfoot').find('th:nth-child(5),th:nth-child(6)').show();
		$('#tbl_footer tfoot').find('th:nth-child(4)').removeAttr().attr("width","90");
		$('#tbl_footer tfoot').find('th:nth-child(3)').removeAttr().attr("width","90");
		$('#tbl_footer tfoot').find('th:nth-child(2)').removeAttr().attr("width","90");
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="200px";
	}

	function fnc_prod_delivery(operation)
	{
		/*if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][54]);?>'){
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][54]);?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][54]);?>')==false)
			{
				return;
			}
		}*/

		var cbo_dyeing_source=$('#cbo_dyeing_source').val();
		if(cbo_dyeing_source == 1)
		{
			if(form_validation('txt_delevery_date*cbo_company_id*cbo_location_id*cbo_dyeing_source*cbo_dyeing_company*cbo_location_dyeing','Delivery Date*Company Name*Location*Dyeing Source*Dyeing Company*Dyeing Location')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('txt_delevery_date*cbo_company_id*cbo_location_id*cbo_dyeing_source*cbo_dyeing_company','Delivery Date*Company Name*Location*Dyeing Source*Dyeing Company')==false)
			{
				return;
			}
		}
		if(operation==4 || operation==5 || operation==6 || operation==7)
		{
			var program_ids = "";var product_ids=""; var order_ids="";  var batch_ids=""; var fabricshade_ids="";
			var total_tr=$('#table_body tbody tr').length-1;
			var company=$('#cbo_company_id').val();
			var location=$('#cbo_location_id').val();
			var buyer=$('#cbo_buyer_id').val();
			var from_date=$('#txt_date_from').val();
			var to_date=$('#txt_date_to').val();
			var update_mst_id=$('#update_mst_id').val();
			var delivery_date=$('#txt_delevery_date').val();
			var Challan_no=$('#txt_sys_num').val();
			var fin_prod_type=$('#cbo_order_status').val();
			var txt_remarks=$('#txt_remarks').val();
			var cbo_is_sales=$('#cbo_is_sales').val();
			var dyeing_company=$('#cbo_dyeing_company').val();
			var cbo_template_id=$('#cbo_template_id').val();
			var deli_company=$('#cbo_deli_company_id').val();
			var deli_location=$('#cbo_deli_location_id').val();
			var dyeing_location=$('#cbo_location_dyeing').val();
			var txt_remark=$('#txt_remarks').val();

			// var deli_location=$('#cbo_deli_company_id').val();cbo_location_dyeing
			// var deli_location=$('#cbo_deli_location_id').val();

			for(i=1; i<=total_tr; i++)
			{
				try
				{
					if ($('#txtcurrentdelivery_'+i).val()!="")
					{
						fabricshade_id = $('#hidefabshade_'+i).val();
						if(fabricshade_ids=="") fabricshade_ids= fabricshade_id; else fabricshade_ids +=','+fabricshade_id;
						program_id = $('#hidesysid_'+i).val();
						if(program_ids=="") program_ids= program_id; else program_ids +=','+program_id;
						batch_id = $('#hidebatch_'+i).val();
						if(batch_ids=="") batch_ids= batch_id; else batch_ids +=','+batch_id;
						product_id = $('#hideprodid_'+i).val();
						if(product_ids=="") product_ids= product_id; else product_ids +=','+product_id;
						order_id = $('#hideorder_'+i).val();
						if(order_ids=="") order_ids= order_id; else order_ids +=','+order_id;
					}
				}
				catch(e)
				{

				}
			}

			if(program_ids=="")
			{
				alert("Please Enter At Least Single Quantity in Current Delivery Field");
				return;
			}

			if(operation==4 || operation==5)
			{
				if(cbo_is_sales == 1){
					print_report(program_ids+'_'+company+'_'+from_date+'_'+to_date+'_'+product_ids+'_'+order_ids+'_'+location+'_'+buyer+'_'+update_mst_id+'_'+delivery_date+'_'+Challan_no+'_'+fin_prod_type+'_'+batch_ids+'_'+operation+'_'+txt_remarks+'_'+cbo_template_id+'_'+deli_company+'_'+deli_location, "delivery_challan_print_sales", "requires/finish_feb_delivery_entry_controller" ) ;
				}else{
					print_report(program_ids+'_'+company+'_'+from_date+'_'+to_date+'_'+product_ids+'_'+order_ids+'_'+location+'_'+buyer+'_'+update_mst_id+'_'+delivery_date+'_'+Challan_no+'_'+fin_prod_type+'_'+batch_ids+'_'+operation+'_'+txt_remarks+'_'+cbo_template_id+'_'+deli_company+'_'+deli_location+'_'+dyeing_location+'_'+txt_remark, "delivery_challan_print", "requires/finish_feb_delivery_entry_controller" ) ;
				}
			}
			else if (operation==6)
			{
				if(cbo_is_sales == 1){
					print_report(program_ids+'_'+company+'_'+from_date+'_'+to_date+'_'+product_ids+'_'+order_ids+'_'+location+'_'+buyer+'_'+update_mst_id+'_'+delivery_date+'_'+Challan_no+'_'+fin_prod_type+'_'+batch_ids+'_'+operation+'_'+txt_remarks+'_'+cbo_template_id+'_'+deli_company+'_'+deli_location, "delivery_challan_print_sales_3", "requires/finish_feb_delivery_entry_controller" ) ;
				}else{
					print_report(program_ids+'_'+company+'_'+from_date+'_'+to_date+'_'+product_ids+'_'+order_ids+'_'+location+'_'+buyer+'_'+update_mst_id+'_'+delivery_date+'_'+Challan_no+'_'+fin_prod_type+'_'+batch_ids+'_'+operation+'_'+txt_remarks+'_'+dyeing_company+'_'+cbo_template_id+'_'+deli_company+'_'+deli_location, "delivery_challan_print_3", "requires/finish_feb_delivery_entry_controller" ) ;
				}
			}
			else if (operation==7)
			{
				if(cbo_is_sales == 1){
					print_report(program_ids+'_'+company+'_'+from_date+'_'+to_date+'_'+product_ids+'_'+order_ids+'_'+location+'_'+buyer+'_'+update_mst_id+'_'+delivery_date+'_'+Challan_no+'_'+fin_prod_type+'_'+batch_ids+'_'+operation+'_'+txt_remarks+'_'+cbo_template_id+'_'+deli_company+'_'+deli_location, "delivery_challan_print_sales_4", "requires/finish_feb_delivery_entry_controller" ) ;
				}
				// else{
				// 	print_report(program_ids+'_'+company+'_'+from_date+'_'+to_date+'_'+product_ids+'_'+order_ids+'_'+location+'_'+buyer+'_'+update_mst_id+'_'+delivery_date+'_'+Challan_no+'_'+fin_prod_type+'_'+batch_ids+'_'+operation+'_'+txt_remarks+'_'+dyeing_company+'_'+cbo_template_id+'_'+deli_company+'_'+deli_location, "delivery_challan_print_4", "requires/finish_feb_delivery_entry_controller" ) ;
				// }
			}

		}
		else if(operation==2)
		{
			alert("This Operation is not available");
			return;
		}
		else
		{

			var details_data=""
			var total_row=$('#table_body tbody tr').length-1;
			var totaldeliveryqty = 0;
			for(var i=1; i<=total_row; i++)
			{
				var qnty=$('#txtcurrentdelivery_'+i).val()*1;
				var hiddendtlsid=$('#hiddendtlsid_'+i).val();

				totaldeliveryqty = totaldeliveryqty+qnty;

				if(qnty*1>0 || hiddendtlsid!="")
				{
					details_data +='hidesysid_'+i+'*'+'hidesysnum_'+i+'*'+'hideprodid_'+i+'*'+'hidejob_'+i+'*'+'hideorder_'+i+'*'+'hideconstruction_'+i+'*'+'hidecomposition_'+i+'*'+'hidegsm_'+i+'*'+'hidedia_'+i+'*'+'txtcurrentdelivery_'+i+'*'+'hiddendtlsid_'+i+'*'+'txtroll_'+i+'*'+'hideprogrum_'+i+'*'+'hidefindtls_'+i+'*'+'hidebatch_'+i+'*'+'hidefabshade_'+i+'*'+'body_part_id_'+i+'*'+'color_id_'+i+'*'+'dia_width_type_'+i+'*'+'hideuom_'+i+'*'+'hidegreyused_'+i+'*'+'hidden_current_val_'+i+'*';
				}
			}

			if(totaldeliveryqty==0)
			{
				alert("Delivery qnty can not zero");
				return;
			}

			var master_data='txt_sys_num*txt_delevery_date*cbo_company_id*cbo_location_id*cbo_buyer_id*update_mst_id*cbo_order_status*txt_remarks*cbo_is_sales*cbo_dyeing_source*cbo_dyeing_company*cbo_location_dyeing*cbo_deli_company_id*cbo_deli_location_id';

			var total_datastring=details_data+master_data;
			var data="action=save_update_delete&operation="+operation+"&total_row="+total_row+get_submitted_data_string(total_datastring,"../");

			freeze_window(operation);
			http.open("POST","requires/finish_feb_delivery_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_prod_delivery_response;
		}
	}

	function fnc_prod_delivery_response()
	{
		if(http.readyState == 4)
		{
			var company=$("#cbo_company_id").val();
			var response=trim(http.responseText).split("**");
			if(trim(response[0])=='receive'){
				alert("Receive  found :"+trim(response[2])+"\n So Update/Delete Not Possible");
				release_freezing();
				return;
			}

			if(response[0]==0)
			{
				$('#txt_sys_num').val(response[2]);
				$('#update_mst_id').val(response[1]);

				get_php_form_data(response[1], "populate_master_from_data", "requires/finish_feb_delivery_entry_controller" );
				$('#cbo_is_sales').attr('disabled','true');
				generate_list(1);
				//set_button_status(1, permission, 'fnc_prod_delivery',1,1);
				get_php_form_data( company, 'company_wise_report_button_setting','requires/finish_feb_delivery_entry_controller' );
				show_msg(response[0]);
				//release_freezing();
			}
			else if(response[0]==1) 
			{

				get_php_form_data(response[1], "populate_master_from_data", "requires/finish_feb_delivery_entry_controller" );
				$('#cbo_is_sales').attr('disabled','true');
				generate_list(1);
				//set_button_status(1, permission, 'fnc_prod_delivery',1,1);
				get_php_form_data( company, 'company_wise_report_button_setting','requires/finish_feb_delivery_entry_controller' );
				show_msg(response[0]);
				release_freezing();
			}
			else if(response[0]==20)
			{
				alert(response[1]);

				var validate_row =response[2].split(",");
				var row_no =validate_row.length;
				//alert(row_no);
				
				$(".rmvQty").css("background-color", "");

				var k;
				for(k=0;k<row_no;k++)
				{
					//alert('no='+validate_row[k]);
					$("#txtcurrentdelivery_"+validate_row[k]).css("background-color", "red");
				}


				release_freezing();
				return;
			}
			else if(response[0]==30)
			{
				alert(response[1]);
				release_freezing();
				return;
			}
			else
			{
				show_msg(response[0]);
				release_freezing();
			}
		}
	}

	function open_mypage()
	{
		var company=$("#cbo_company_id").val();
		var is_sales=$("#cbo_is_sales").val();
		if (form_validation('cbo_company_id','Buyer')==false )
		{
			return;
		}

		var page_link='requires/finish_feb_delivery_entry_controller.php?action=delevery_search&company='+company+'&is_sales='+is_sales;
		var title='Delivery Information Entry Form';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1230px,height=400px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var cbo_is_sales=this.contentDoc.getElementById("cbo_is_sales").value;
			var ex_data=this.contentDoc.getElementById("hidden_tbl_id").value.split("_");
			$('#cbo_is_sales').val(cbo_is_sales);
			$('#cbo_is_sales').attr('disabled','true');
			if(trim(ex_data[0])!="")
			{
				$('#txt_sys_num').val(ex_data[3]);
				get_php_form_data(ex_data[0], "populate_master_from_data", "requires/finish_feb_delivery_entry_controller" );//+"**"+invoice_id
				generate_list(1);

				get_php_form_data( company, 'company_wise_report_button_setting','requires/finish_feb_delivery_entry_controller' );

				
			}
		}
	}

	function sum_all_td()
	{
		var total_tr =$('#table_body tbody tr').length-1;
		var ttl_sum=0;	var k;
		for(k=1;k<=total_tr;k++)
		{
			//ttl_sum+=$('#hidden_current_val_'+k).val()*1;
			ttl_sum+=$('#txtcurrentdelivery_'+k).val()*1;
		}
		ttl_sum = number_format(ttl_sum,2,'.','');
		$('#total_current_val').text(ttl_sum);	 
	}

	function setHideval( i ) 
	{
		var dev_qty=$('#txtcurrentdelivery_'+i).val()*1;
		var prod_qty=$('#totalqtyTd_'+i).text().replace(/,/g,'');
		var hide_cur_val=$('#hidden_current_val_'+i).val()*1;
		if((dev_qty*1)>(prod_qty*1))
		{
			alert("Delivery quantity can not be greater than balance quantity");
			$('#txtcurrentdelivery_'+i).val("");
		}
		else
		{
			//$('#total_current_val').text(($('#total_current_val').text().replace(/,/g,'')*1)-(hide_cur_val));
			//$('#total_current_val').text(number_format((($('#total_current_val').text()*1)+(dev_qty*1)),2));
			

			//$('#hidden_current_val_'+i).val(dev_qty);
		}
		sum_all_td();
	}

	function fncGreyUsedQty(i){
		var process_loss_perc=$('#txt_process_loss_perc_'+i).val()*1;
		var dev_qty=$('#txtcurrentdelivery_'+i).val()*1;

		var row_grey_used= dev_qty + (process_loss_perc*dev_qty)/100;

		$('#greyQtyTd_'+i).text(number_format(row_grey_used,2,'.' , ""));

		var total_tr =$('#table_body tbody tr').length-1;
		var ttl_sum=0;	var k;
		for(k=1;k<=total_tr;k++)
		{
			var grey_used=$('#greyQtyTd_'+k).text().replace(/,/g,'');
			ttl_sum+=grey_used*1;
		}
		ttl_sum = number_format(ttl_sum,2,'.','');
		$('#total_grey_used').text(ttl_sum);	 
		
	}

	function checkuom( i, uom )
	{
		if(uom =="" || uom==0 )
		{
			alert("Product UOM is not found");
			$('#txtcurrentdelivery_'+i).val("");
			$('#txtcurrentdelivery_'+i).focus();
		}
	}


	function total_roll(i)
	{
		var roll_qty=($('#txtroll_'+i).val()*1);
		var hideroll=($('#hideroll_'+i).val()*1);
		if(hideroll>0)
		{
			$('#total_roll').text($('#total_roll').text()-$('#hideroll_'+i).val());
		}
		$('#total_roll').text(number_format((($('#total_roll').text()*1)+roll_qty),2));
		$('#hideroll_'+i).val(roll_qty);
	}
	function fnResetForm()
	{
		$('#cbo_company_id').removeAttr('disabled','');
		$('#cbo_location_id').removeAttr('disabled','');
		$('#cbo_buyer_id').removeAttr('disabled','');
		$('#cbo_dyeing_source').removeAttr('disabled','');
		$('#cbo_dyeing_company').removeAttr('disabled','');
		$('#cbo_location_dyeing').removeAttr('disabled','');
		reset_form('proddelivery_1','list_view_container','','','');
	}

	function load_location()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_dyeing_source = $('#cbo_dyeing_source').val();
		var cbo_dyeing_company = $('#cbo_dyeing_company').val();
		if(cbo_dyeing_source==1)
		{
			load_drop_down( 'requires/finish_feb_delivery_entry_controller',cbo_dyeing_company, 'load_drop_down_location', 'location_td_dyeing' );
		}
		else
		{

			load_drop_down( 'requires/finish_feb_delivery_entry_controller',0, 'load_drop_down_location', 'location_td_dyeing' );
		}
	}

	function details_reset()
	{
		$("#list_view_container").html("");
		$("#report_container").html("");
	}

	function fnc_batch_popup()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		else
		{
			var cbo_company_id = $('#cbo_company_id').val();
			var page_link='requires/finish_feb_delivery_entry_controller.php?cbo_company_id='+cbo_company_id+'&action=batch_number_popup';
			var title='Batch Number Popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=960px,height=420px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var batch_str=this.contentDoc.getElementById("hidden_batch_data").value;
				var batch_strArr = batch_str.split("_");
				if(batch_strArr[1]!="")
				{
					freeze_window(5);
					$('#txt_batch_no').val(batch_strArr[1]);
					$('#cbo_dyeing_source').val(batch_strArr[2]);
					load_drop_down( 'requires/finish_feb_delivery_entry_controller', batch_strArr[2]+'_'+cbo_company_id, 'load_drop_down_dyeing_com','dyeingcom_td');
					$('#cbo_dyeing_company').val(batch_strArr[3]);
					load_location();
					//load_drop_down( 'requires/finish_feb_delivery_entry_controller',batch_strArr[3], 'load_drop_down_location', 'location_td_dyeing' );
					$('#cbo_location_dyeing').val(batch_strArr[4]);
					$('#cbo_location_id').val(batch_strArr[5]);
					$('#cbo_buyer_id').val(batch_strArr[6]);
					
					$('#cbo_company_id').attr('disabled','disabled');
					$('#cbo_location_id').attr('disabled','disabled');
					$('#cbo_buyer_id').attr('disabled','disabled');
					$('#cbo_dyeing_source').attr('disabled','disabled');
					$('#cbo_dyeing_company').attr('disabled','disabled');
					$('#cbo_location_dyeing').attr('disabled','disabled');
					release_freezing();
				}
			}
		}
	}

	

</script>

<style type="text/css">
	hr.hr_class {
	  width: 100%;
	  height: 1px;
	  border-top: 2px solid #99B9E2;
	  border-bottom: 1px solid #fff;
	}
</style>
</head>
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="left">
		<? echo load_freeze_divs ("../",$permission);  ?><br />
		<form name="proddelivery_1" id="proddelivery_1" autocomplete="off" >
			<div>
				<fieldset style="width:910px;" align="center">
					<table class="" width="900" cellpadding="0" cellspacing="0" align="center">
						<tr>
							<td align="center" colspan="6">Challan No:
								<input type="text" name="txt_sys_num" id="txt_sys_num" class="text_boxes" style="width:150px;" onDblClick="open_mypage()" placeholder="Browse For Challan No" readonly/>
							</td>
						</tr>
						<tr>
							<td align="center" colspan="6">&nbsp;</td>
						</tr>

						<tr>
							<td width="130" class="must_entry_caption">Company</td>
							<td>
								<?
								echo create_drop_down( "cbo_company_id", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected , "load_drop_down( 'requires/finish_feb_delivery_entry_controller', this.value, 'load_drop_down_location_lc', 'location_td' );load_drop_down( 'requires/finish_feb_delivery_entry_controller', this.value, 'load_drop_down_buyer_form', 'buyer_td' );details_reset();" );
								?>
							</td>
							<td width="120" class="must_entry_caption">Location</td>
							<td id="location_td">
							<?
								echo create_drop_down( "cbo_location_id",140,$blank_array,"", 1, "--Select Location--", $selected, "","","","","","",2);
							?>
							</td>
							<td width="120">Buyer</td>
							<td id="buyer_td">
								<?
								echo create_drop_down( "cbo_buyer_id",130,$blank_array,"", 1, "--Select Buyer--", $selected, "","","","","","",2);
								?>
							</td>
						</tr>
						<tr>
							<td class="must_entry_caption">Dyeing Source</td>
							<td>
								<?
								echo create_drop_down("cbo_dyeing_source", 140, $knitting_source,"", 1,"-- Select Source --", 0,"load_drop_down( 'requires/finish_feb_delivery_entry_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_dyeing_com','dyeingcom_td');load_location();details_reset();","","1,3");
								?>
							</td>
							<td class="must_entry_caption">Dyeing Company</td>
							<td id="dyeingcom_td">
								<?
								echo create_drop_down("cbo_dyeing_company", 140, $blank_array,"", 1,"-- Select Dyeing Company --", 0,"");
								?>
							</td>
							<td class="must_entry_caption">Dyeing Location</td>
							<td id="location_td_dyeing">
								<?
								echo create_drop_down("cbo_location_dyeing", 130, $blank_array,"", 1,"--Select --", 0,"");
								?>
							</td>
                    	</tr>
                    	<tr>
							<td class="must_entry_caption">Delevery Date:</td>
							<td>
								<input type="text" name="txt_delevery_date" id="txt_delevery_date" class="datepicker" style="width:130px;" value="<? echo date("d-m-Y"); ?>" disabled />
							</td>

							<td>Remarks:</td>
							<td colspan="3">
								<input name="txt_remarks" id="txt_remarks" class="text_boxes" type="text"  style="width:426px">
							</td>
						</tr>

						<tr>
							<td width="130">Delivery To Company</td>
							<td>
								<?
								echo create_drop_down( "cbo_deli_company_id", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected , "load_drop_down( 'requires/finish_feb_delivery_entry_controller', this.value, 'load_drop_down_location_deli', 'deli_location_td' );" );
								?>
							</td>
							<td width="120">Delivery To Location</td>
							<td id="deli_location_td">
							<?
								echo create_drop_down( "cbo_deli_location_id",140,$blank_array,"", 1, "--Select Location--", $selected, "","","","","","",2);
							?>
							</td>
							<td colspan="2">&nbsp;
								
							</td>
						</tr>

						<tr>
							<td align="center" colspan="6" >&nbsp;<hr class="hr_class"></td>
						</tr>
						<tr>
							<td align="center" colspan="6" >&nbsp;</td>
						</tr>
                    	<tr>
                    		<td>System Id</td>
                    		<td >
								<input type="text" name="txt_sys_prod_id" id="txt_sys_prod_id" class="text_boxes" style="width:130px;" />
							</td>
							<td>Is Sales</td>
							<td>
								<?
								$is_sales_ar = array( 0 =>"No",1=>"Yes");
								echo create_drop_down( "cbo_is_sales", 130, $is_sales_ar,"", 0, "--Select --", 1);
								?>

							</td>
							<td>Batch No</td>
							<td >
								<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:120px;" placeholder="Browse" onDblClick="fnc_batch_popup();" readonly />
							</td>
                    	</tr>
                    	<tr>
                    		<td>Job Year</td>
                    		<td>
								<?
								$year_current=date("Y");
								echo create_drop_down( "cbo_year", 140, $year,"", 1, "--Select Year--", $year_current, "" );
								?>
							</td>
							<td>Job No</td>
							<td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:120px;" /></td>
							<td>Order/FSO No</td>
							<td ><input type="text" name="txt_ord_no" id="txt_ord_no" class="text_boxes" style="width:120px;" /></td>
                    	</tr>
                    	<tr>
                    		<td>File No</td>
                    		<td ><input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:130px;" /></td>
                    		<td>Ref No</td>
                    		<td ><input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:120px;" /></td>
                    		<td>Status</td>
                    		<td>
								<?
								$delevery_status=array(1=>"Pending",2=>"Full Delivery");
								echo create_drop_down( "cbo_status", 130, $delevery_status,"", 1, "-Select Status-", 1 );
								?>
							</td>
                    	</tr>
                    	<tr>
                    		<td>Date From</td>
                    		<td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:130px;" readonly/></td>
                    		<td>Date To</td>
                    		<td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:120px;" readonly/></td>
                    		<td>Order Status</td>
                    		<td>
								<?
								$delevery_status=array(1=>"With Order",2=>"Without Order");
								echo create_drop_down( "cbo_order_status", 130, $delevery_status,"", 1, "-Select Status-", 1 );
								?>
							</td>
                    	</tr>
                    	<tr>
							<td align="center" colspan="6" >&nbsp;</td>
						</tr>
                    	<tr>
                    		<td colspan="2"></td>
                    		<td>
								<input type="hidden" id="update_mst_id" name="update_mst_id" value="" >
								<input type="hidden" id="hidden_receive_id" name="hidden_receive_id" value="" >
								<input type="hidden" id="hidden_product_id" name="hidden_product_id" value="" >
								<input type="hidden" id="hidden_order_id" name="hidden_order_id" value="" >
								<input type="button" name="search" id="search" value="Show" style="width:60px" class="formbutton" onClick="generate_list(0)" />
							</td>
                    		<td ><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="fnResetForm();" /></td>
							<td colspan="2"></td>
                    	</tr>

					</table>

					<div style="margin-top:10px;" id="report_container" align="center"></div>
					<div style="margin-top:10px;" id="list_view_container" align="left"></div>
				</fieldset>
			</div>
		</form>
	</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	//$('#cbo_is_sales').val(1);
</script>
</html>
