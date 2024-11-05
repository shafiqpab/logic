<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create planning Info Entry
Functionality	:
JS Functions	:
Created by		:	Fuad Shahriar
Creation date 	: 	27-07-2013
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
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Planning Info Entry", "../", 1, 1,'','','');

?>
<script>
	
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	var permission='<? echo $permission; ?>';

	function show_details(type)
	{
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}

		if(type==2)
		{
			if(form_validation('txt_booking_no','Booking No.')==false)
			{
				return;
			}
		}

		if(($('#txt_order_no').val() != "") || ($('#txt_style_ref').val() != "") || ($('#txt_internal_ref').val() != "") || ($('#txt_file_no').val() != "") || ($('#cbo_buyer_name').val() != 0) || ($('#txt_booking_no').val() != "") ||($('#txt_date_to').val() != "" && $('#txt_date_to').val() != ""))
		{
			var data="action=booking_item_details"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*hide_job_id*cbo_planning_status*txt_order_no*hide_order_id*txt_booking_no*approval_needed_or_not*txt_internal_ref*txt_file_no*txt_date_from*txt_date_to*cbo_booking_type*txtVariableCollarCuff',"../")+'&type='+type;
		}
		else
		{
			if(form_validation('txt_booking_no','Booking No.')==false)
			{
				return;
			}
		}
		//var data="action=booking_item_details"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*hide_job_id*cbo_planning_status*txt_order_no*hide_order_id*txt_booking_no*approval_needed_or_not*txt_internal_ref*txt_file_no*txt_booking_date',"../")+'&type='+type;



		freeze_window(5);
		http.open("POST","requires/planning_info_entry_sm_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_show_details_reponse;
		//show_list_view(cbo_company_id, 'booking_item_details', 'list_container_fabric_desc', 'requires/planning_info_entry_sm_controller', '');
	}

	function fn_show_details_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText);
			$('#list_container_fabric_desc').html(response);
			set_all_onclick();
			show_msg('18');
			release_freezing();
		}
	}

	function openmypage_style()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}

		var companyID = $("#cbo_company_name").val();
		var buyerID = $("#cbo_buyer_name").val();
		var page_link='requires/planning_info_entry_sm_controller.php?action=style_ref_search_popup&companyID='+companyID+'&buyerID='+buyerID;
		var title='Style Ref. Search';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_ref=this.contentDoc.getElementById("hide_style_ref").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;

			$('#txt_style_ref').val(style_ref);
			$('#hide_job_id').val(job_id);
		}
	}

	function openmypage_order()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}

		var companyID = $("#cbo_company_name").val();
		var buyerID = $("#cbo_buyer_name").val();
		var page_link='requires/planning_info_entry_sm_controller.php?action=order_no_search_popup&companyID='+companyID+'&buyerID='+buyerID;
		var title='Order No Search';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=990px,height=390px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_no=this.contentDoc.getElementById("hide_order_no").value;
			var order_id=this.contentDoc.getElementById("hide_order_id").value;

			$('#txt_order_no').val(order_no);
			$('#hide_order_id').val(order_id);
		}
	}

	function generate_yarn_desc(desc)
	{
		var page_link='requires/planning_info_entry_sm_controller.php?action=yarn_desc_pop_up&desc='+desc;
		var title='Desc. Of Yarn';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=430px,height=230px,center=0,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{

		}
	}

	function openmypage_booking()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}

		var companyID = $("#cbo_company_name").val();
		var buyerID = $("#cbo_buyer_name").val();
		var cbo_booking_type = $("#cbo_booking_type").val();

		var page_link='requires/planning_info_entry_sm_controller.php?action=booking_no_search_popup&companyID='+companyID+'&buyerID='+buyerID+'&cbo_booking_type='+cbo_booking_type;;
		var title='Booking Search';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var booking_no=this.contentDoc.getElementById("hidden_booking_no").value;
			var booking_id=this.contentDoc.getElementById("hidden_booking_id").value;

			$('#txt_booking_no').val(booking_no);
		}
	}

	function openmypage_prog()
	{
		var type=$('#txt_type').val();
		if(type==2)
		{
			alert("Not Allow");
			return;
		}
		var tot_row=$('#tbl_list_search tbody tr').length;
		var data='';
		var i=0;
		var selected_row=0;
		var currentRowColor='';
		var booking_no='';
		var body_part_id='';
		var po_wise_bodypartId='';
		var fabric_typee='';
		var buyer_id='';
		var po_id='';
		var dia='';
		var gsm='';
		var desc='';
		var start_date='';
		var end_date='';
		var booking_qnty=0;
		var desc_of_yarn='';
		var plan_id='';
		var determination_id='';
		var pre_cost_id='';
		var color_type_id='';
		var balance_qnty=0;
		var prog_id=''; 

		var body_part_ids='';
		var prog_ids=''; 
		var pre_cost_ids='';


		var companyID=$('#company_id').val();
		var hdnVariableCollarCuff=$('#hiddenVariableCollarCuff').val();

		for(var j=1; j<=tot_row; j++)
		{
			currentRowColor=document.getElementById('tr_' + j ).style.backgroundColor;
			check=$('#check_'+j).val();
			if(check==1 && currentRowColor=='yellow')
			{
				i++;
				selected_row++;

				if(data=='')
				{
					data=trim($('#bookingNo_'+j).val())+"**"
					+trim($('#start_date_'+j).text())+"**"
					+trim($('#end_date_'+j).text())+"**"
					+trim($('#po_id_'+j).text())+"**"
					+trim($('#buyer_id_'+j).val())+"**"
					+trim($('#body_part_id_'+j).val())+"**"
					+trim($('#fabric_typee_'+j).val())+"**"
					+trim($('#pre_cost_id_'+j).val())+"**"
					+trim($('#desc_'+j).text())+"**"
					+trim($('#gsm_weight_'+j).text())+"**"
					+trim($('#dia_width_'+j).text())+"**"
					+trim($('#determination_id_'+j).val())+"**"
					+trim($('#booking_qnty_'+j).text())+"**"
					+trim($('#color_type_id_'+j).val())+"**"
					+trim($('#updateId_'+j).val())+"**"
					+trim($('#ballance_qnty_'+j).val())+"**"
					+trim($('#hdnColorQty_'+j).val())+"**"
					+trim($('#yarn_desc_'+j).text());
				}
				else
				{
					data+="_"+trim($('#bookingNo_'+j).val())+"**"
					+trim($('#start_date_'+j).text())+"**"
					+trim($('#end_date_'+j).text())+"**"
					+trim($('#po_id_'+j).text())+"**"
					+trim($('#buyer_id_'+j).val())+"**"
					+trim($('#body_part_id_'+j).val())+"**"
					+trim($('#fabric_typee_'+j).val())+"**"
					+trim($('#pre_cost_id_'+j).val())+"**"
					+trim($('#desc_'+j).text())+"**"
					+trim($('#gsm_weight_'+j).text())+"**"
					+trim($('#dia_width_'+j).text())+"**"
					+trim($('#determination_id_'+j).val())+"**"
					+trim($('#booking_qnty_'+j).text())+"**"
					+trim($('#color_type_id_'+j).val())+"**"
					+trim($('#updateId_'+j).val())+"**"
					+trim($('#ballance_qnty_'+j).val())+"**"
					+trim($('#hdnColorQty_'+j).val());
				}

				booking_no=trim($('#bookingNo_'+j).val());
				gsm=trim($('#gsm_weight_'+j).text());
				dia=$('#dia_width_'+j).text().trim();
				desc=trim($('#desc_'+j).text());
				start_date=trim($('#start_date_'+j).text());
				end_date=trim($('#end_date_'+j).text());
				desc_of_yarn=trim($('#yarn_desc_'+j).text());
				//var balance_qnty = $('#ballance_qnty_'+j).text()*1;

				//for plan id
				if(plan_id=='')
					plan_id=$('#plan_id_'+j).text();

				//for program id
				if(prog_ids=='')
				{
					prog_ids=$('#prog_no_'+j).text();
				}
				else{
					prog_ids+=","+$('#prog_no_'+j).text();
				}

				//for po id
				if(po_id=='')
					po_id=$('#po_id_'+j).text();
				else
					po_id+=","+$('#po_id_'+j).text();

				//for pre cost id

				if(pre_cost_ids=='')
				{
					pre_cost_ids=$('#pre_cost_id_'+j).val();
				}
				else{
					pre_cost_ids+=","+$('#pre_cost_id_'+j).val();
				}

				//for body_part_ids
				//body_part_ids=$('#body_part_id_'+j).val();

				
				if(body_part_ids=='')
				{
					body_part_ids=$('#body_part_id_'+j).val();
				}
				else{
					body_part_ids+=","+$('#body_part_id_'+j).val();
				}

				if(po_wise_bodypartId=='')
					po_wise_bodypartId=$('#po_wise_bodypartId_'+j).val();
				else
					po_wise_bodypartId+=","+$('#po_wise_bodypartId_'+j).val();

				determination_id=$('#determination_id_'+j).val();
				color_type_id=$('#color_type_id_'+j).val();
				fabric_typee=$('#fabric_typee_'+j).val();
				buyer_id=$('#buyer_id_'+j).val();

				booking_qnty=booking_qnty*1+$('#booking_qnty_'+j).text()*1;
				balance_qnty=balance_qnty*1+$('#ballance_qnty_'+j).text()*1;
			}
		}

		//For body part unique id
		var uniqueArrayBody =[];
		var body_part_idArr = body_part_ids.split(',');
		for (var k = 0; k < body_part_idArr.length; k++) {
			if ($.inArray(body_part_idArr[k], uniqueArrayBody) === -1) {
				body_part_id+=body_part_idArr[k]+",";
				uniqueArrayBody.push(body_part_idArr[k]);
		    } 
		}
		body_part_id=body_part_id.slice(0, -1);

		//For pre cost id unique
		var uniqueArrayPrecost =[];
		var pre_cost_idArr = pre_cost_ids.split(',');
		for (var k = 0; k < pre_cost_idArr.length; k++) {
			if ($.inArray(pre_cost_idArr[k], uniqueArrayPrecost) === -1) {
				pre_cost_id+=pre_cost_idArr[k]+",";
				uniqueArrayPrecost.push(pre_cost_idArr[k]);
		    } 
		}
		pre_cost_id=pre_cost_id.slice(0, -1);

		//For prog no unique
		var uniqueArrayProg =[];
		var prog_noArr = prog_ids.split(',');
		for (var k = 0; k < prog_noArr.length; k++) {
			if ($.inArray(prog_noArr[k], uniqueArrayProg) === -1) {
				prog_id+=prog_noArr[k]+",";
				uniqueArrayProg.push(prog_noArr[k]);
		    } 
		}
		prog_id=prog_id.slice(0, -1);


		if(selected_row<1)
		{
			alert("Please Select At Least One Item");
			return;
		}
		//alert(data);

		//------------Start--------------------RnD-----LocalStorage--------------------
		// Storing an object
		var dataString = {
		  data: data
		};
		localStorage.setItem('dataString', JSON.stringify(dataString));
		// Retrieving the object
		var storedDataString = JSON.parse(localStorage.getItem('dataString'));
		//--------------End------------------RnD---------LocalStorage----------------
		//return;
		//+'&data='+data
		//+'&desc_of_yarn='+desc_of_yarn
		var page_link='requires/planning_info_entry_sm_controller.php?action=prog_qnty_popup&gsm='+gsm+'&dia='+dia+'&desc='+desc+'&start_date='+start_date+'&end_date='+end_date+'&booking_qnty='+booking_qnty+'&companyID='+companyID+'&determination_id='+determination_id+'&booking_no='+booking_no+'&body_part_id='+body_part_id+'&po_wise_bodypartId='+po_wise_bodypartId+'&fabric_type='+fabric_typee+'&buyer_id='+buyer_id+'&po_id='+po_id+'&pre_cost_id='+pre_cost_id+'&color_type_id='+color_type_id+'&balance_qnty='+balance_qnty+'&plan_id='+plan_id+'&prog_id='+prog_id+'&hdnVariableCollarCuff='+hdnVariableCollarCuff;
		var title='Program Qnty Info';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=935px,height=450px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
            show_details(1);
		}
		//prog_no_
    }

	function selected_row(rowNo)
	{
		var approved=parseInt($('#approved_'+rowNo).val());
		if(approved==0)
		{
			alert("Approved Booking First");
			return;
		}

		var color=document.getElementById('tr_' + rowNo ).style.backgroundColor;
		var bookingNo=$('#bookingNo_'+rowNo).val();
		var determinationId=$('#determination_id_'+rowNo).val();
		var widthDiaType=$('#fabric_typee_'+rowNo).val();
		var gsm=$('#gsm_weight_'+rowNo).text();
		var fabricDia=$('#dia_width_'+rowNo).text();
		var plan_id=$('#plan_id_'+rowNo).text();
		var color_type_id=$('#color_type_id_'+rowNo).val();
		var body_part_type =$('#body_part_type_'+rowNo).val();
		var body_part_id =$('#body_part_id_'+rowNo).val();

		var stripe_or_not='';

		if(color_type_id==2 || color_type_id==3 || color_type_id==4)
		{
			stripe_or_not=1;//1 means stripe yes
		}
		else
		{
			stripe_or_not=0;//0 means stripe no
		}

		var currentRowColor=''; var check='';
		if(color!='yellow')
		{
			var tot_row=$('#tbl_list_search tbody tr').length;
			for(var i=1; i<=tot_row; i++)
			{
				if(i!=rowNo)
				{
					check=$('#check_'+i).val();
					currentRowColor=document.getElementById('tr_' + i ).style.backgroundColor;
					if(check==1 && currentRowColor=='yellow')
					{
						var bookingNoCur=$('#bookingNo_'+i).val();
						var determinationIdCur=$('#determination_id_'+i).val();
						var widthDiaTypeCur=$('#fabric_typee_'+i).val();
						var gsmCur=$('#gsm_weight_'+i).text();
						var fabricDiaCur=$('#dia_width_'+i).text();
						var plan_idCur=$('#plan_id_'+i).text();
						var color_type_idCur=$('#color_type_id_'+i).val();
						var body_part_typeCur=$('#body_part_type_'+i).val();

						var stripe_or_notCur='';
						if(color_type_idCur==2 || color_type_idCur==3 || color_type_idCur==4)
						{
							stripe_or_notCur=1;//1 means stripe yes
						}
						else
						{
							stripe_or_notCur=0;//0 means stripe no
						}

						if(plan_id=="" || plan_idCur=="")
						{
							if(!(bookingNo==bookingNoCur && determinationId==determinationIdCur && widthDiaType==widthDiaTypeCur && gsm==gsmCur && fabricDia==fabricDiaCur && stripe_or_not==stripe_or_notCur && color_type_id == color_type_idCur))
							{
								alert("Please Select Same Description");
								return;
							}
						}
						else
						{
							if(!(plan_id==plan_idCur && bookingNo==bookingNoCur && determinationId==determinationIdCur && widthDiaType==widthDiaTypeCur && gsm==gsmCur && fabricDia==fabricDiaCur && stripe_or_not==stripe_or_notCur && color_type_id == color_type_idCur))
							{
								alert("Please Select Same Description and Same Plan ID");
								return;
							}
						}
						if(body_part_type==1 || body_part_typeCur==1)
						{
							if(!(plan_id==plan_idCur && bookingNo==bookingNoCur && determinationId==determinationIdCur && widthDiaType==widthDiaTypeCur && gsm==gsmCur && fabricDia==fabricDiaCur && stripe_or_not==stripe_or_notCur && body_part_type==body_part_typeCur))
							{
								alert("Do Not Allow Merge Other Body Part Type with Collar and Cuff");
								return;
							}
						}
					}
				}
			}

			$('#tr_' + rowNo).css('background-color','yellow');
		}
		else
		{
			var reqsn_found_or_not=$('#reqsn_found_or_not_'+rowNo).val();
			if(reqsn_found_or_not==0)
			{
				$('#tr_' + rowNo).css('background-color','#FFFFCC');
			}
			else
			{
				alert("Requisition Found Against This Planning. So Change Not Allowed");
				return;
			}
		}
	}

	function generate_worder_report(type,txt_booking_no,cbo_company_name,txt_order_no_id,cbo_fabric_natu,cbo_fabric_source,txt_job_no,id_approved_id,print_id,action,i)
	{

		if(print_id==85)
		{
			var report_title='Partial Fabric Booking';
		}
		else if(print_id==269 || print_id==93 || print_id==370)
		{
			var report_title='Main Fabric Booking V2';
		}
		else
		{
			var report_title='Budget Wise Fabric Booking';
		}

		var show_yarn_rate='';
		if(print_id==73 || print_id==2 || print_id==432){
			var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
			if (r==true)
			{
				show_yarn_rate="1";
			}
			else
			{
				show_yarn_rate="0";
			}
		}

		//alert(type);
		var report_type=2;
		var data="action="+action+
		'&txt_booking_no='+"'"+txt_booking_no+"'"+
		'&cbo_company_name='+"'"+cbo_company_name+"'"+
		'&txt_order_no_id='+"'"+txt_order_no_id+"'"+
		'&cbo_fabric_natu='+"'"+cbo_fabric_natu+"'"+
		'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+
		'&id_approved_id='+"'"+id_approved_id+"'"+
		'&report_title='+report_title+
		'&txt_job_no='+"'"+txt_job_no+"'"+
		'&report_type='+"'"+report_type+"'"+
		'&show_yarn_rate='+"'"+show_yarn_rate+"'"+

		'&path=../';
		freeze_window(5);
		// alert(print_id);
		if(type==1)
		{
			if(print_id==269)
			{
				http.open("POST","../order/woven_order/requires/fabric_booking_urmi_controller.php",true);
			}
			else
			{
				http.open("POST","../order/woven_order/requires/short_fabric_booking_controller.php",true);
			}

		}
		else if(type==2)
		{

			if(print_id==85)
			{
				http.open("POST","../order/woven_order/requires/partial_fabric_booking_controller.php",true);
			}
			else if(print_id==2 || print_id==45 || print_id==53 || print_id==73 || print_id==93 || print_id==269 || print_id==719 || print_id==370 || print_id==432)
			{
				http.open("POST","../order/woven_order/requires/fabric_booking_urmi_controller.php",true);
			}
			else
			{
				http.open("POST","../order/woven_order/requires/fabric_booking_controller.php",true);
			}
		}
		else
		{
			http.open("POST","../order/woven_order/requires/sample_booking_controller.php",true);
		}

		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_fabric_report_reponse;
	}

	function generate_fabric_report_reponse()
	{
		if(http.readyState == 4)
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+http.responseText+'</body</html>');
			d.close();
			release_freezing();
		}

	}

	function delete_prog()
	{
		var program_ids = ""; var total_tr=$('#tbl_list_search tr').length;
		for(i=1; i<total_tr; i++)
		{
			try
			{
				if ($('#tbl_'+i).is(":checked"))
				{
					program_id = $('#promram_id_'+i).val();
					if(program_ids=="") program_ids= program_id; else program_ids +=','+program_id;
				}
			}
			catch(e)
			{
				//got error no operation
			}
		}

		if(program_ids=="")
		{
			alert("Please Select At Least One Program");
			return;
		}

		var data="action=delete_program&operation="+operation+'&program_ids='+program_ids+get_submitted_data_string('cbo_company_name',"../");

		freeze_window(operation);

		http.open("POST","requires/planning_info_entry_sm_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_delete_prog_Reply_info;
		//alert(program_ids);
	}

	function fnc_delete_prog_Reply_info()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');

			show_msg(trim(reponse[0]));

			if(reponse[0]==2)
			{
				fnc_remove_tr();
			}

			release_freezing();
		}
	}

	function fnc_remove_tr()
	{
		var tot_row=$('#tbl_list_search tr').length;
		for(var i=1;i<=tot_row;i++)
		{
			try
			{
				if($('#tbl_'+i).is(':checked'))
				{
					$('#tr_'+i).remove();
				}
			}
			catch(e)
			{
				//got error no operation
			}
		}
	}

	function fnc_update(i)
	{
		var prog_qty=$('#prog_qty_'+i).val();
		var program_id=$('#promram_id_'+i).val();
		var data="action=update_program&operation="+operation+'&program_id='+program_id+'&prog_qty='+prog_qty;
		freeze_window(operation);
		http.open("POST","requires/planning_info_entry_sm_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_update_prog_Reply_info;
	}

	function fnc_update_prog_Reply_info()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText);
			if(response==20)
			{
				alert("Program Qty Cannot Be Less Than Knitting Qty.");
				release_freezing();
				return;
			}
			show_msg(response);
			release_freezing();
		}
	}

	function generate_report2(company_id, program_id,entry_form) {
        //alert(program_id);
		var page = 'planning_info_entry';
        var template_id = 1;
        var path = '../';
        if(entry_form==85)
        {
            print_report(company_id + '*' + program_id + '*' + template_id+ '*' + path, "requisition_print3", "requires/yarn_requisition_entry_controller");
        }
        else
        {
			print_report( company_id+'*'+program_id+'*'+path+'*'+page, "print", "requires/yarn_requisition_entry_controller" )
        }
    }

	$(".drag-controls").live("click",function(){
		show_details(1);
	});
	function collar_cuff_variable_chk(companyID)
	{
		var response=return_global_ajax_value( companyID, 'check_collar_cuff_variable', '', 'requires/planning_info_entry_sm_controller');
		var response=response.split("_");
		$('#txtVariableCollarCuff').val(response[1]);
	}
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",$permission); ?>
		 <form name="palnningEntry_1" id="palnningEntry_1">
         <h3 style="width:1330px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
         <div id="content_search_panel">
             <fieldset style="width:1330px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Buyer Name</th>
                            <th>Style Ref.</th>
                            <th>Order No</th>
                            <th>Internal Ref.</th>
                    		<th>File No</th>
                    		<th>Booking Type</th>
                            <th>Booking No</th>
                            <th>Booking Date</th>
                            <th>Planning Status</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('palnningEntry_1','list_container_fabric_desc','','','')" class="formbutton" style="width:60px" /></th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td>
                                    <?
                                        echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/planning_info_entry_sm_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data(this.value,'approval_needed_or_not','requires/planning_info_entry_sm_controller' );collar_cuff_variable_chk(this.value);" );
                                    ?>
                                    <input type="hidden" name="txtVariableCollarCuff" id="txtVariableCollarCuff" readonly />
                                </td>
                                <td id="buyer_td">
                                    <?
                                        echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                                    ?>
                                </td>
                                <td>
                                    <input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:130px" placeholder="Browse" onDblClick="openmypage_style();" readonly>
                                    <input type="hidden" name="hide_job_id" id="hide_job_id" readonly>
                                </td>
                                <td>
                                    <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:130px" placeholder="Browse Or Write" onDblClick="openmypage_order();" onChange="$('#hide_order_id').val('');" autocomplete="off">
                                    <input type="hidden" name="hide_order_id" id="hide_order_id" readonly>
                                </td>
                                <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
                      			<td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px"></td>

                                <td>
		                            <select id="cbo_booking_type" name="cbo_booking_type" class="combo_boxes" style="width: 100px;">
		                                <option value="0_0">-- Select --</option>
		                                <option value="1_2">Main</option>
		                                <option value="1_1">Short</option>
		                                <option value="4_2">Sample With </option>
		                                <!--<option value="90">Sample Fabric Booking Without Order</option>-->
		                            </select>
		                        </td>

                                <td>
                                    <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:100px" placeholder="Browse Or Write" onDblClick="openmypage_booking();">
                                </td>
                                 <td>
                           			 <!-- <input type="text" name="txt_booking_date" id="txt_booking_date"  class="datepicker" style="width:60px;"/> -->
                           			 <input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:55px;"/>
                           			 <input type="text" name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:55px;"/>
                        		</td>
                                <td>
                                    <? echo create_drop_down( "cbo_planning_status", 100, $planning_status,"", 0, "", $selected,"","", "1,2" ); ?>
                                </td>
                                <td>
                                	<input type="button" value="Show" name="show" id="show" class="formbutton" style="width:60px" onClick="show_details(1)"/>
                                    &nbsp;
                                    <input type="button" value="Revised Booking" name="show" id="show" class="formbutton" style="width:100px" onClick="show_details(2)"/>
                                    <input type="hidden" name="approval_needed_or_not" id="approval_needed_or_not" readonly>
                                </td>
                            </tr>
                            <tr>
			                    <td colspan="10" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
			                </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
            <div style="width:100%;margin-top:10px;">
                <input type="button" value="Click For Program" name="generate" id="generate" class="formbutton" style="width:150px" onClick="openmypage_prog()"/>
            </div>
		</form>
	</div>
    <div id="list_container_fabric_desc" style="margin-left:10px;margin-top:10px"></div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>