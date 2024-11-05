<?
/*-------------------------------------------- Comments
Purpose			: Cut and Lay Entry Roll Wise
Functionality	:	
JS Functions	:
Created by		:	Fuad Shahriar
Creation date 	: 	19-05-2015
Updated by 		: 		
Update date		: 	Ashraful	   
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
echo load_html_head_contents("Cut and Lay Entry Roll Wise","../../", 1, 1, $unicode,'','');

?>
<script>
	var mandatory_field=new Array();
	var mandatory_message=new Array();
	<?
	
	if(isset($_SESSION['logic_erp']['mandatory_field'][711]))
	{
		echo "var mandatory_field = '". implode('*',$_SESSION['logic_erp']['mandatory_field'][711]) . "';\n";
		echo "var mandatory_message = '". implode('*',$_SESSION['logic_erp']['mandatory_message'][711]) . "';\n";
	}
	?>
    var txt_job_id=$("#txt_job_no").val();
	var permission='<? echo $permission; ?>';
	function add_break_down_tr(i)
   	{ 
		var row_num=$('#tbl_order_details tbody tr').length;
		if (row_num!=i)
		{
			return false;
		}
		else
		{ 
			i++;
		    var k=i-1;
			$("#tbl_order_details tbody tr:last").clone().find("input,select").each(function(){
			$(this).attr({ 
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
			  'value': function(_, value) { return value }              
			});
			}).end().appendTo("#tbl_order_details");
			$("#tbl_order_details tbody tr:last").css({"height":"10px","background-color":"#FFF"});	
			$("#tbl_order_details tbody tr:last ").removeAttr('id').attr('id','tr_'+i);
			$("#tbl_order_details tbody tr:last td:nth-child(2)").removeAttr('id').attr('id','cutNo_'+i);
			$("#tbl_order_details tbody tr:last td:nth-child(3)").removeAttr('id').attr('id','ship_'+i);
			$("#tbl_order_details tbody tr:last td:nth-child(4)").removeAttr('id').attr('id','garment_'+i);
			$("#tbl_order_details tbody tr:last td:nth-child(5)").removeAttr('id').attr('id','color_'+i);
			$("#tbl_order_details tbody tr:last td:nth-child(8)").removeAttr('id').attr('id','batch_'+i);
			$("#tbl_order_details tbody tr:last td:nth-child(10)").removeAttr('id').attr('id','order_'+i);
			$("#tbl_order_details tbody tr:last td:nth-child(9)").removeAttr('id').attr('id','marker_'+i);
			
			$('#txtplics_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_roll("+i+");");
			
			$('#cbogmtsitem_'+i).val('');
			$('#orderCutNo_'+i).val('');
			$('#updateDetails_'+i).val('');
			$('#cboorderno_'+i).val('');
			$('#txtorderqty_'+i).val('');
			$('#txtshipdate_'+i).val('');
			$('#cbocolor_'+i).val('');
			$('#txtplics_'+i).val('');
			$('#rollData_'+i).val('');
			$('#txtmarkerqty_'+i).val('');
			$('#cboorderno_'+i).val('');
			$('#txttotallay_'+i).val('');
			$('#txtlaybalanceqty_'+i).val('');
			$('#txtshipdate_'+i).addClass("datepicker");
			$("#cbobatch_"+i+" option[value!='0']").remove();
			
			$('#increase_'+i).removeAttr("value").attr("value","+");
			$('#decrease_'+i).removeAttr("value").attr("value","-");
			$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
			$('#increase_'+i).attr("onkeydown","add_break_down_tr("+i+");");
		}
   	}
	   
	function fn_deleteRow(rowNo) 
	{ 
		if($('#tbl_order_details').val()!=2)
		{
			var numRow = $('#tbl_order_details tbody tr').length; 
			var k=rowNo-1;
			if(numRow==rowNo && rowNo!=1)
			{
				var updateIdDtls=$('#updateIdDtls_'+rowNo).val();
				var txt_deleted_id=$('#txt_deleted_id').val();
				var selected_id='';
				if(updateIdDtls!='')
				{
					if(txt_deleted_id=='') selected_id=updateIdDtls; else selected_id=txt_deleted_id+','+updateIdDtls;
					$('#txt_deleted_id').val( selected_id );
				}
				$('#tbl_order_details tbody tr:last').remove();
			}
			else
			{
				return false;
			}
		}
  	}
	
	function openmypage_jobNo(id)
	{
		var cbo_company_id = $('#cbo_company_name').val();
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var title = 'Search Job No';	
		var page_link = 'requires/cut_and_lay_gmts_no_wise_entry_controller.php?cbo_company_id='+cbo_company_id+'&action=job_search_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=400px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var job_no=(this.contentDoc.getElementById("hidden_job_no").value).split('_');
			$('#txt_buyer_name').val(job_no[1]);
			$('#txt_job_year').val(job_no[2]);
			$('#txt_style').val(job_no[3]);
			document.getElementById('txt_job_no').value=job_no[0];
			load_drop_down( 'requires/cut_and_lay_gmts_no_wise_entry_controller',job_no[0]+'**'+cbo_company_id, 'load_drop_down_order', 'order_id' );
			var $job_name=job_no[0];
			release_freezing();
		}
	}
	
	//openmypage_country
	


	function openmypage_country1()
	{
		var hidden_body_partstring=$("#hidden_body_partstring").val();
		if(form_validation('txt_job_no','Job No.')==false)
		{
			return;
		}
		
		var title = 'PO Selection Form';	
		var page_link = 'requires/cut_and_lay_gmts_no_wise_entry_controller.php?hidden_body_partstring='+hidden_body_partstring+'&action=country_popup';
 		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=300px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
		}
	}



  	function change_order(value)
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var txt_job=$("#txt_job_no").val();
		var cbo_company_id = $('#cbo_company_name').val();
		if(txt_job.length<5)
		{
			load_drop_down( 'requires/cut_and_lay_gmts_no_wise_entry_controller',txt_job+'**'+value+'**'+cbo_company_id, 'load_drop_down_job', 'job_change_id' );
			load_drop_down( 'requires/cut_and_lay_gmts_no_wise_entry_controller',txt_job+'**'+value+'**'+cbo_company_id, 'load_drop_down_buyer', 'buyer_id' );
		}
		var txt_job=$("#txt_job_no").val();
		load_drop_down( 'requires/cut_and_lay_gmts_no_wise_entry_controller',txt_job+'**'+cbo_company_id, 'load_drop_down_order', 'order_id' );
  	}
	

	function change_data(value,id)
    { 
	    var id=id.split('_');
		var ship_id='ship_'+id[1];
		var order_qty_id='order_'+id[1];
		var gmt_id='garment_'+id[1];
		
		$('#orderCutNo_'+id[1]).val();
		
		load_drop_down( 'requires/cut_and_lay_gmts_no_wise_entry_controller', value+"_"+ship_id, 'load_drop_down_ship', ship_id);
		load_drop_down( 'requires/cut_and_lay_gmts_no_wise_entry_controller', value+"_"+gmt_id, 'load_drop_down_order_garment', gmt_id);
		var gmt_value=$("#cbogmtsitem_"+id[1]).val();
		var cbocolor=$("#cbocolor_"+id[1]).val();
		load_drop_down( 'requires/cut_and_lay_gmts_no_wise_entry_controller', value+"_"+id[1]+"_"+cbocolor+"_"+gmt_value, 'load_drop_down_color_type', 'colorTypeId_'+id[1]);
		if(gmt_value!=0)
		{
		 	var gmt_id="cbogmtsitem_"+id[1];
		  	change_color(gmt_id,gmt_value);	
		}
    }

	function change_color(id,value)
	{
		var id=id.split('_');
		var color_id='color_'+id[1];
		var order_id=$('#cboorderno_'+id[1]).val();
		load_drop_down( 'requires/cut_and_lay_gmts_no_wise_entry_controller', order_id+"_"+value+"_"+id[1], 'load_drop_down_color', color_id);
		var color_value=$("#cbocolor_"+id[1]).val();
		if(color_value!=0)
		{
			var color_id="cbogmtsitem_"+id[1];
		  	change_marker(color_id,color_value);	
		}
	}
	
	function openmypage_country(row)
	{
		var hidden_country_id=$("#countryId_"+row).val();
		if(form_validation('cbocolor_'+row,'Color')==false)
		{
			return;
		}
		var order_id=$('#cboorderno_'+row).val();
		var gmt_id=$('#cbogmtsitem_'+row).val();
		var gmt_value=$("#cbogmtsitem_"+row).val();
		var color_value=$("#cbocolor_"+row).val();
		var hiddiscountryseq=$("#hiddiscountryseq_"+row).val();
		var title = 'Country Selection Form';	
		var page_link = 'requires/cut_and_lay_gmts_no_wise_entry_controller.php?hidden_country_id='+hidden_country_id+'&action=country_popup'+'&order_id='+order_id+'&gmt_id='+gmt_id+'&color_value='+color_value+'&hiddiscountryseq='+hiddiscountryseq+'&action=country_popup';
 		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=300px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var country_id=this.contentDoc.getElementById("hidden_search_id").value;
			var country_name=this.contentDoc.getElementById("hidden_search_name").value;
			var is_seq=this.contentDoc.getElementById("chk_is_seq").value;

			$("#countryId_"+row).val(country_id);
			$("#countryName_"+row).val(country_name);
			$("#hiddiscountryseq_"+row).val(is_seq);
			get_php_form_data( order_id+"_"+gmt_id+"_"+color_value+"_"+row+"_"+country_id, "load_drop_down_order_qty_with_country", "requires/cut_and_lay_gmts_no_wise_entry_controller" );
		}
	}

	function change_marker(id,value)
	{
		id=id.split('_');
		var order_id_no='order_'+id[1];
		marker_id='marker_'+id[1];
		var order_id=$('#cboorderno_'+id[1]).val();
		var gmt_id=$('#cbogmtsitem_'+id[1]).val();
		var txt_job_no=$("#txt_job_no").val();
		var ship_date=$("#txtshipdate_"+id[1]).val();
		var gmt_value=$("#cbogmtsitem_"+id[1]).val();
		var color_value=$("#cbocolor_"+id[1]).val();
		var row_num=$('#tbl_order_details tbody tr').length;
		
		get_php_form_data( order_id+"_"+gmt_id+"_"+value+"_"+id[1], "load_drop_down_order_qty", "requires/cut_and_lay_gmts_no_wise_entry_controller" );
		load_drop_down('requires/cut_and_lay_gmts_no_wise_entry_controller', order_id+"_"+value+"_"+id[1], 'load_drop_down_batch', 'batch_'+id[1]);
		var length=$("#cbobatch_"+id[1]+" option").length;
		if(length==2)
		{
			$('#cbobatch_'+id[1]).val($('#cbobatch_'+id[1]+' option:last').val());
		}
	}
	
	function batch_match(id,value)
	{
		id=id.split('_');
		var order_id=$('#cboorderno_'+id[1]).val();
		var ship_date=$("#txtshipdate_"+id[1]).val();
		var gmt_value=$("#cbogmtsitem_"+id[1]).val();
		var color_value=$("#cbocolor_"+id[1]).val();
		var batch_id=$("#cbobatch_"+id[1]).val();
		var row_num=$('#tbl_order_details tbody tr').length;
		
		for(var i=1;i<=row_num;i++)
		{
			if(row_num!=1 && id[1]!=i)
			{
				if(order_id==$('#cboorderno_'+i).val() && gmt_value==$('#cbogmtsitem_'+i).val() && color_value==$('#cbocolor_'+i).val() && ship_date==$('#txtshipdate_'+i).val() && batch_id==$('#cbobatch_'+i).val())
				{
					alert(" Order number,Ship date,Gmt Item,Color,Batch are same");
				   	$("#cbobatch_"+id[1]).val("");
				  	return;
				}
			}
		}
	}

	function openmypage_sizeNo(id)
	{
		$('#'+id).attr("onkeydown","openmypage_sizeNo(id);");
		var job_id = $('#txt_job_no').val();
		var rmg_no_creation = $('#rmg_no_creation').val();
		var cbo_company_id = $('#cbo_company_name').val();
		var id=id.split('_');
		var size=id[1];
		var mst_id = $('#update_id').val();
		var details_id= $('#updateDetails_'+size).val();
		var rollData=$('#rollData_'+size).val();
		var cbo_color_type = $('#cboColorType_'+size).val();
		if(details_id=="" && mst_id=="")
		{
		   alert("Please save first");return;	
		}
		
		if(details_id=="" && mst_id!="")
		{
		   alert("Please Update first");return;	
		}

		var order_id= $('#cboorderno_'+size).val();
		var marker_quantity = $('#txtmarkerqty_'+size).val();
		var order_quantity = $('#txtorderqty_'+size).val();
		var total_lay_qty = $('#txttotallay_'+size).val();
		var total_lay_balance = $('#txtlaybalanceqty_'+size).val();
		var piles = $('#txtplics_'+size).val();
		var cutting_no = $('#txt_cutting_no').val();
		var cbo_color_id = $('#cbocolor_'+size).val();
		var cbo_gmt_id = $('#cbogmtsitem_'+size).val();
		var cbo_countries = $('#countryId_'+size).val();
		var hiddiscountryseq = $('#hiddiscountryseq_'+size).val();

		
		$("#tr_"+size).css({"background-color":"yellow"});
		//	$("#tbl_order_details tbody tr_"+size).css({"background-color":"red"});
		var title = 'Size Ratio Form';
		
		var page_link = 'requires/cut_and_lay_gmts_no_wise_entry_controller.php?cbo_company_id='+cbo_company_id+'&job_id='+job_id+'&mst_id='+mst_id+'&details_id='+details_id+'&cbo_gmt_id='+cbo_gmt_id+'&cbo_color_id='+cbo_color_id+'&size='+size+'&txt_piles='+piles+'&cutting_no='+cutting_no+'&order_id='+order_id+'&marker_quantity='+marker_quantity+'&order_quantity='+order_quantity+'&total_lay_qty='+total_lay_qty+'&total_lay_balance='+total_lay_balance+'&rollData='+rollData+'&rmg_no_creation='+rmg_no_creation+'&action=size_popup'+'&cbo_color_type='+cbo_color_type+'&cbo_countries='+cbo_countries+'&hiddiscountryseq='+hiddiscountryseq;//
		//alert(page_link)
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=500px,center=1,resize=0,scrolling=0','../../');
		emailwindow.onclose=function()
		{ 
			var sysNumber=this.contentDoc.getElementById("hidden_marker_no_x").value;
			alert(sysNumber)
			var marker_no=sysNumber.split('**');
			$('#txtmarkerqty_'+marker_no[0]).val(marker_no[1]);
			$('#txtorderqty_'+marker_no[0]).val(marker_no[2]);
			$('#txttotallay_'+marker_no[0]).val(marker_no[3]);
			$('#txtlaybalanceqty_'+marker_no[0]).val(marker_no[4]);
			//freeze_window(5);
			$("#tr_"+marker_no[0]).css({"background-color":"white"});
			//release_freezing();
		 }
	 }

	function fnc_cut_lay_info( operation )
	{      
		if(operation==2)
	  	{
			show_msg('13');
			return;
		}  
		if(mandatory_field!="") 
		{
			if (form_validation(mandatory_field,mandatory_message)==false)
			{
				release_freezing();
				return;
			}
		}
		if(form_validation('cbo_company_name*cbo_working_company_name*txt_job_no*txt_entry_date*cbo_location_name*cbo_floor_name*txt_table_no','Company Name* Working Company*Job No*Plan Start Date*Location Name*Floor Name*Table No')==false)
	  	{
			return;
		}
		
        var row_num=$('#tbl_order_details tbody tr').length;
		var tna_order_id="";
		for(var r=1; r<=row_num; r++)
		{
			if(tna_order_id=="") tna_order_id=$("#cboorderno_"+r).val();
			else                 tna_order_id=tna_order_id+","+$("#cboorderno_"+r).val();
			
		}
		
	    var cut_start_date=$("#txt_entry_date").val();
		var cut_finish_date=$("#txt_end_date").val();
		var company_name=$("#cbo_company_name").val();
		var tna_data=cut_start_date+"**"+cut_finish_date+"**"+tna_order_id+"**"+company_name;
		
		var tna_date=return_ajax_request_value(tna_data, "tna_date_status", "requires/cut_and_lay_gmts_no_wise_entry_controller");
		if(tna_date!=2)
		{
			tna_date=trim(tna_date).split("##");
			if(tna_date[0]==0)
			{
				var new_table="Cutting Plane date range has been crossed \nTNA date range\n";
				new_table+="Po Number TNA Start Date TNA End Date\n";
				var tna_order=(tna_date[1]).split("**");
				var tna_start=tna_date[2].split("**");
				var tna_end=tna_date[3].split("**");
				for(var p=0; p<tna_order.length;p++)
				{
					new_table+=tna_order[p]+"            "+tna_start[p]+"             "+tna_end[p]+"\n";
				}
				new_table+="Maximum TNA Date-"+tna_date[4]+"\n Minimum TNA Date-"+tna_date[5];
				r=confirm(new_table);
				if(r==false)
				{
				  $("#txt_entry_date").val(tna_date[4]);
				  $("#txt_end_date").val(tna_date[5]);
				  return;	
				}
			}
		 }
        var data1="action=save_update_delete&operation="+operation+"&row_num="+row_num+get_submitted_data_string('update_id*update_tbl_id*txt_job_no*txt_batch_no*cbo_company_name*cbo_floor_name*cbo_location_name*txt_cutting_no*txt_table_no*txt_entry_date*txt_marker_length*txt_marker_width*txt_fabric_width*txt_gsm*txt_cutting_no*txt_in_time_hours*txt_in_time_minuties*txt_out_time_hours*txt_out_time_minuties*txt_end_date*cbo_width_dia*txt_marker_cons*roll_maintained*cbo_working_company_name*txt_remark',"../../");
		//alert(row_num)
	    var data2='';
		for(var i=1; i<=row_num; i++)
		{
			if(form_validation('cboorderno_'+i+'*cbocolor_'+i+'*txtplics_'+i+'*cbobatch_'+i,'Order No*Color*Plies*Batch')==false)//+'*cbobatch_'+i*Batch hiddiscountryseq_1
			{
				return;
			}
			data2+=get_submitted_data_string('updateDetails_'+i+'*cboorderno_'+i+'*cbogmtsitem_'+i+'*txtshipdate_'+i+'*cbocolor_'+i+'*txtplics_'+i+'*txtorderqty_'+i+'*orderCutNo_'+i+'*orderCutNo_'+i+'*rollData_'+i+'*cbobatch_'+i+'*cboColorType_'+i+'*countryId_'+i+'*hiddiscountryseq_'+i,"../../",2);
		}
	     var data=data1+data2;
		//alert(data1);orderCutNo_1
		freeze_window(operation);
		http.open("POST","requires/cut_and_lay_gmts_no_wise_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_cut_lay_info_reponse;
	}


function fnc_cut_lay_info_reponse()
{
	if(http.readyState == 4) 
	{
		//alert(http.responseText);
		//release_freezing();return;
		var reponse=trim(http.responseText).split('**');
		if(reponse[0]==200)
		{
			alert("Update Restricted.This information found in Cutting Qc Page Which System Id "+reponse[1]+".");
		}
		else if(reponse[0]==786)
		{
			alert("Projected PO is not allowed to production. Please check variable settings."); return;
		}
		else if(reponse[0]==300)
		{
			alert("Update Restricted. You will not be able to mixed order "); return;
		}
		show_msg(trim(reponse[0]));
		if(reponse[0]==0 || reponse[0]==1)
			{
				document.getElementById('update_id').value=reponse[1];
				document.getElementById('update_tbl_id').value=reponse[3];
				document.getElementById('txt_cutting_no').value=reponse[2];
				/*var details_id=reponse[4].split('_');
				for(var i=1;i<=details_id.length;i++)
					{
						var data=details_id[i-1].split("#");
						document.getElementById('updateDetails_'+i).value=data[0];	
						//document.getElementById('orderCutNo_'+i).value=data[1];	
					}*/
				$("#cbo_company_name").attr("disabled",true);
				$("#txt_job_no").attr("disabled",true);
				show_list_view( reponse[1], 'order_details_list', 'cut_details_container', 'requires/cut_and_lay_gmts_no_wise_entry_controller', '' ) ;
				set_button_status(1, permission, 'fnc_cut_lay_info',1,1);
			}
		//set_button_status(1, permission, 'fnc_cut_lay_info');
		release_freezing();
	}
} 

function clear_tr()
{
 var row_num=$('#tbl_order_details tbody tr').length;
   for(var j=1;j<=row_num;j++)
   {
	   if(j!=1)
	   {
	     $('#tbl_order_details tbody tr:last').remove();   
	   }
   }
   $("#cbo_company_name").attr("disabled",false);
   $("#txt_job_no").attr("disabled",false);
}

function open_cutting_popup()
{ 
	if( form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	} 
	var company_id=$("#cbo_company_name").val();
	var page_link='requires/cut_and_lay_gmts_no_wise_entry_controller.php?action=cutting_number_popup&company_id='+company_id; 
	var title="Search Cutting Number Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=420px,center=1,resize=0,scrolling=0',' ../');
	emailwindow.onclose=function()
	{
		var sysNumber = this.contentDoc.getElementById("update_mst_id"); 
		var sysNumber=sysNumber.value.split('_');
		get_php_form_data( sysNumber[0], "load_php_mst_form", "requires/cut_and_lay_gmts_no_wise_entry_controller" );
		show_list_view( sysNumber[0], 'order_details_list', 'cut_details_container', 'requires/cut_and_lay_gmts_no_wise_entry_controller', '' ) ;
		$("#cbo_company_name").attr("disabled",true);
		$("#txt_job_no").attr("disabled",true);
		$("#txt_job_year").attr("disabled",true);
		set_button_status(0, permission, 'fnc_cut_lay_info');
 	}
}

function openmypage_roll(row_no)
{
	if(form_validation('cbo_company_name*cboorderno_'+row_no+'*cbocolor_'+row_no,'Company*Order No*Color')==false)
	{
		return;
	}
	
	let roll_maintained=$('#roll_maintained').val();
	let order_no=$('#cboorderno_'+row_no).val();
	let color=$('#cbocolor_'+row_no).val();
	let rollData=$('#rollData_'+row_no).val();
	let batch_id=$('#cbobatch_'+row_no).val();
	
	let title = 'Plies Entry Roll Wise Form';	
	let page_link = 'requires/cut_and_lay_gmts_no_wise_entry_controller.php?roll_maintained='+roll_maintained+'&order_no='+order_no+'&color='+color+'&rollData='+rollData+'&batch_id='+batch_id+'&action=roll_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		let theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		let plies=this.contentDoc.getElementById("hide_plies").value; //Access form field with id="emailfield"
		let data=this.contentDoc.getElementById("hide_data").value; //Access form field with id="emailfield"
		
		$('#txtplics_'+row_no).val(plies);
		$('#rollData_'+row_no).val(data);
	}
}

function fnc_move_cursor(val,id, field_id,lnth,max_val)
{
	var str_length=val.length;
	
	if(str_length==lnth)
	{
		$('#'+field_id).select();
		$('#'+field_id).focus();
	}
	
	if(val>max_val)
	{
		document.getElementById(id).value=max_val;
	}
}
	

function fnc_intime_populate(val2,val1)
{
	var tot_row=$('#emp_tab tr').length;
	var intimeho=document.getElementById(val1).value;
	
	if(val2== '')
	{
		val2='00';
	}
	for(var i=1; i<=tot_row; i++)
	{
		if($("#txtintimehours_"+i).val()== '')
		{
			$("#txtintimehours_"+i).val(intimeho);
			$("#txtintimeminuties_"+i).val(val2);
		}
	}
}  
	
function fnc_outtime_populate(val2,val1)
{
	var tot_row=$('#emp_tab tr').length;
	var outtimeho=document.getElementById(val1).value;
	
	if(val2== '')
	{
		val2='00';
	}
	
	for(var i=1; i<=tot_row; i++)
	{
		if($("#txtouttimehours_"+i).val()== '')
		{
			$("#txtouttimehours_"+i).val(outtimeho);
			$("#txtouttimeminuties_"+i).val(val2);
		}
	}
}


// for report lay chart
 function generate_report_lay_chart(data,action)
	{
		if(form_validation('txt_cutting_no','Cutting Number')==false)
			   {
				return;
			   }
		window.open("requires/cut_and_lay_gmts_no_wise_entry_controller.php?data=" + data+'&action='+action, true );
	}
	
	
</script>
 
</head>
 
<body onLoad="set_hotkey()">

<div style="width:1200px;" align="center">
	
     <? echo load_freeze_divs ("../../",$permission);  ?>
     <form name="cutandlayentry_1" id="cutandlayentry_1">
    
    <table width="95%" cellpadding="0" cellspacing="2" align="center">
     	<tr>
        	<td width="95%" align="center" valign="top">  <!--   Form Left Container -->
            	<fieldset style="width:1000px;">
                     <legend>cut and lay entry</legend>
                        <table  width="1050" cellspacing="2" cellpadding="0" border="0">
                            <tr>
                                <td colspan="4" align="right"><b>Cutting Number</b></td>
                                <td  align="left">
                                    <input type="text" name="txt_cutting_no" id="txt_cutting_no" class="text_boxes" style="width:130px" placeholder="Double Click To Search" onDblClick="open_cutting_popup();" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick()" readonly />
                                     <input type="hidden" name="update_id"  id="update_id"  />
                                </td>
                            </tr>
                            <tr>
                                    <td  width="110"  align="left" class="must_entry_caption"> Company Name</td>              <!-- 11-00030  -->
                                    <td  width="150" align="" colspan="2">
                                     <? 
                                        echo create_drop_down( "cbo_company_name", 152, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", $selected, "get_php_form_data(this.value,'roll_maintained','requires/cut_and_lay_gmts_no_wise_entry_controller');" );
                                    ?>
                                    </td>
                                    <td  width="110"  align="left" class="must_entry_caption">Working Company</td>              <!-- 11-00030  -->
	                                <td  width="150" align="">
	                                 <? 
	                                    echo create_drop_down( "cbo_working_company_name", 142, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Working Company --", $selected, "load_drop_down( 'requires/cut_and_lay_gmts_no_wise_entry_controller', this.value, 'load_drop_down_location', 'location_td' );" );// get_php_form_data(this.value,'size_wise_repeat_cut_no','requires/cut_and_lay_ratio_wise_entry_controller_urmi' );
	                                	?>
	                                </td>
                                    <td class="must_entry_caption"  width="110" align="left" >Location </td>
                                    <td width="150" id="location_td">
                                        <? echo create_drop_down( "cbo_location_name", 142, $blank_array,"", 1, "-- Select Location --", $selected, "" ); ?>
                                    </td>
                                    <td class="must_entry_caption" width="90" align="left" >Floor</td>
                                    <td  width="140" id="floor_td">
                                        <? 
                                        //echo create_drop_down( "cbo_floor_name", 140, "select id,floor_name from lib_prod_floor where production_process=1 and status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 ); 
                                       echo create_drop_down( "cbo_floor_name", 132,"select id,floor_name from lib_prod_floor where production_process=1  and status_active =1 and is_deleted=0","id,floor_name", 1, "-- Select Floor --", $selected, "" );
                                         ?>
                                   
                                    </td>
                                  	
                              </tr>
							<tr>
                                <td  align="left">CAD Marker length
                                </td>
                                <td   align="left">
                                <input style="width:140px;" type="text"  class="text_boxes" autocomplete="off"  name="txt_marker_length" id="txt_marker_length"  />
                                </td>
                                <td  width="" align="left">
                                 
                                </td>
                                <td   height="" align="left">CAD Marker Width  </td>           
                                <td>
                                    <input style="width:130px;" type="text"  class="text_boxes_numeric" autocomplete="off"  name="txt_marker_width" id="txt_marker_width" />
                                </td>
                                <td  width="" height="" align="left">CAD Fabric Width </td>         
                                <td  width="" >
                                 <input type="text" name="txt_fabric_width" id="txt_fabric_width" class="text_boxes_numeric" style="width:130px" />
                                </td>
                                <td class="must_entry_caption"  width="90" align="left">Table No </td>
                                <td width="120">
                                    <input style="width:120px;" type="text"   class="text_boxes_numeric" autocomplete="off"  name="txt_table_no" id="txt_table_no"  />
                                </td>
                            </tr>
							<tr>
                            	<td  width="" class="must_entry_caption">Job No</td>
                                <td width="" colspan="2" id="job_change_id">
                                  <input style="width:140px;" type="text"  onDblClick="openmypage_jobNo()" class="text_boxes" autocomplete="off" placeholder="Browse" name="txt_job_no" id="txt_job_no" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick()" readonly />
                                </td>
                                <td  width="" align="left" >Year</td>
                                <td>
                                    <? echo create_drop_down( "txt_job_year", 142, $year,"", 1, "-- Select year --", $selected, "change_order(this.value)",""); ?>
                                 </td>
                                 <td  width="" align="left">Batch   </td>
                                 <td>
                                      <input style="width:130px;" type="text"   class="text_boxes" autocomplete="off"  name="txt_batch_no" id="txt_batch_no"  />
                                </td> 
                              	<td width="" align="left">CAD GSM</td>
                                <td>
                                     <input style="width:120px;" type="text"  class="text_boxes_numeric" autocomplete="off"  name="txt_gsm" id="txt_gsm" />  
                                </td>
							</tr>
                            <tr>
                                <td width="" align="left" class="must_entry_caption">Plan Start Date
                                </td>
                                <td width="" align="left">
                                      <input style="width:140px;" type="text" class="datepicker" autocomplete="off"  name="txt_entry_date" id="txt_entry_date"  />
                                      <input type="hidden" name="update_job_no"  id="update_job_no"  />
                                      <input type="hidden" name="update_tbl_id"  id="update_tbl_id"  />
                                      <input type="hidden" name="roll_maintained" id="roll_maintained" readonly>
                               		  <input type="hidden" name="rmg_no_creation" id="rmg_no_creation" readonly>
                                </td>
                                <td  width="" align="left">
                                </td>
                                <td  width="" height="" align="left"> Start Time </td>           
                                <td  width="" >
                                     <input type="text" name="txt_in_time_hours" id="txt_in_time_hours" class="text_boxes_numeric" placeholder="HH"  style="width:30px;"  onKeyUp="fnc_move_cursor(this.value,'txt_in_time_hours','txt_in_time_minuties',2,23);" /> :
                                    <input type="text" name="txt_in_time_minuties" id="txt_in_time_minuties" class="text_boxes_numeric" placeholder="MM"  style="width:30px;" onKeyUp="fnc_move_cursor(this.value,'txt_in_time_minuties','txt_in_time_seconds',2,59)" onBlur="fnc_intime_populate(this.value,'txt_in_time_hours')" />
                                </td>
                                <td width="" align="left">Plan End Date
                                </td>
                                <td  width="" align="left">
                                      <input style="width:130px;" type="text"   class="datepicker" autocomplete="off"  name="txt_end_date" id="txt_end_date"  />
                                </td>
                                <td  width="" align="left" >Buyer </td>
                                <td id="buyer_id">
                                <? echo create_drop_down( "txt_buyer_name", 132,"select id, buyer_name from lib_buyer","id,buyer_name", 1, "", $selected, "" ,1); ?>
                                </td>
                          </tr>
							<tr>
                               <td  width="" >Width/Dia Type</td>
                                <td width="" >
                                	<? echo create_drop_down( "cbo_width_dia", 152, $fabric_typee,"", 1, "-- Select --", 1, "",$disabled,"" ); ?>
                                </td>
                                <td colspan="2">CAD Marker Cons/Dzn</td>
                                <td><input style="width:130px;" type="text"  class="text_boxes_numeric" autocomplete="off" name="txt_marker_cons" id="txt_marker_cons" />
                                </td> 
                                <td  width="90" height="" align="left"> End Time </td>         
                               	<td  width="120" >
                                     <input type="text" name="txt_out_time_hours" id="txt_out_time_hours" class="text_boxes_numeric" placeholder="HH"  style="width:30px;"  onKeyUp="fnc_move_cursor(this.value,'txt_out_time_hours','txt_out_time_minuties',2,23);" /> :
                                    <input type="text" name="txt_out_time_minuties" id="txt_out_time_minuties" class="text_boxes_numeric" placeholder="MM"  style="width:30px;"  onKeyUp="fnc_move_cursor(this.value,'txt_out_time_minuties','txt_out_time_seconds',2,59)" onBlur="fnc_outtime_populate(this.value,'txt_out_time_hours')"/> 
    
                                </td>
                                <td align="left">Style Ref. </td>
                                <td id=""><input style="width:120px;" type="text"  class="text_boxes" name="txt_style" id="txt_style" readonly /></td>
                            </tr>
                            <tr>
                            	<td>Remark</td>
                            	<td>
                            		<input style="width: 140px;" type="text" class="text_boxes" name="txt_remark" id="txt_remark" placeholder="Max length 100 characters" maxlength="100" >
                            	</td>
                            	<td></td>
                            	<td></td>
                            	<td></td>
                            	<td></td>
                            	<td></td>
                            	<td></td>
                            </tr>
						</table>
                 </fieldset>
              </td>
         </tr>
         <tr>
             <td align="center" valign="top" id="po_list_view">
               <fieldset style="width:1280px; margin-top:10px">
                    <legend>Cut and Lay details</legend>
                <table cellpadding="0" cellspacing="0" width="1280" class="rpt_table" border="1" rules="all" id="tbl_order_details">
                    <thead>
                        <th class="must_entry_caption">Order No</th>
                        <th>Order Cut No</th>
                        <th>Ship Date</th>
                        <th>Gmt Item</th>
                        <th class="must_entry_caption">Color</th>
                        <th id="color_type_td_id" >Color Type</th>
                        <th id="" >Country</th>
                        <th class="must_entry_caption">Batch</th><!-- class="must_entry_caption"-->
                        <th class="must_entry_caption">Plies</th>
                        <th >Size Ratio</th>
                        <th >Marker Qnty</th>
                        <th>Order qty</th>
                        <th>Total Lay qty</th>
                        <th>Lay balance qty</th>
                        <th></th>
                    </thead>
                    
                    <tbody id="cut_details_container">
                        <tr class="" id="tr_1" style="height:10px;">
                            <td align="center" id="order_id">
                            	<?
							        $sql="select id,job_no_mst,po_number from  wo_po_break_down where  status_active=1";
                            		echo create_drop_down( "cboorderno_1", 120, $blank_array,"id,po_number", 1, "select order", $selected, "");	
								?>		 
                            </td>
                            <td align="center" id="cutNo_1">
                                <input style="width:40px;" class="text_boxes_numeric" type="text" name="orderCutNo_1" id="orderCutNo_1" placeholder=""  />
                            </td>                             
                            <td align="center" id="ship_1">
                                <input style="width:70px;" type="text" class="datepicker" autocomplete="off" name="txtshipdate_1" id="txtshipdate_1" placeholder="Display" disabled readonly/>
                            </td>                              
                            <td align="center" id="garment_1">
                                 <? 
								 	echo create_drop_down( "cbogmtsitem_1", 120, $blank_array,"", 1, "-- Select Item --", $selected, "","");
								  
                                 ?>
                            </td>
                            <td align="center" id="color_1">
                                 <? 
								     echo create_drop_down( "cbocolor_1", 100, $blank_array,"", 1, "select color", $selected, "");
                                 ?>
                            </td>
                            <td align="center" id="colorTypeId_1">
                                <?
                                echo create_drop_down( "cboColorType_1", 100, $blank_array,"", 1, "--Select--", $selected, "",1,0 );
                                ?>
                            </td>
                            <td align="center" id="">
                                <input style="width:70px;" class="text_boxes" type="text" name="countryName_1" id="countryName_1" placeholder="Browse"  onDblClick="openmypage_country(1);"/>
                                <input class="text_boxes" type="hidden" name="countryId_1" id="countryId_1" />
                                <input class="text_boxes" type="hidden" name="hiddiscountryseq_1" id="hiddiscountryseq_1" />
                            </td>
                            <td align="center" id="batch_1">
                                 <? 
								     echo create_drop_down( "cbobatch_1", 100, $blank_array,"", 1, "select Batch", $selected, "");
                                 ?>
                            </td>
                            <td align="center">
                                 <input type="text" name="txtplics_1"  id="txtplics_1" class="text_boxes_numeric" style="width:60px" placeholder="Double Click" onDblClick="openmypage_roll(1)" readonly />
                                 <input type="hidden" name="hiddenorder_1"  id="hiddenorder_1"  />
                                 <input type="hidden" name="updateDetails_1"  id="updateDetails_1"  />
                                 <input type="hidden" name="rollData_1" id="rollData_1" class="text_boxes" readonly />
                            </td>
                            <td align="center">
                                <input type="text" name="txtsizeratio_1"  id="txtsizeratio_1" class="text_boxes_numeric"  onDblClick="openmypage_sizeNo(this.id);"  placeholder="Browse" style="width:50px" />
                            </td>
                            <td align="center" id="marker_1">
                                <input type="text" name="txtmarkerqty_1"  id="txtmarkerqty_1" class="text_boxes_numeric"  placeholder="Display" style="width:60px"  readonly/>
                            </td>
                              <td align="center" id="order_1">
                                <input type="text" name="txtorderqty_1"  id="txtorderqty_1" class="text_boxes_numeric"  placeholder="Display" style="width:60px"  readonly/>
                            </td>
                              <td align="center">
                                <input type="text" name="txttotallay_1"  id="txttotallay_1" class="text_boxes_numeric"  placeholder="Display" style="width:60px"  readonly/>
                            </td>
                              <td align="center">
                                <input type="text" name="txtlaybalanceqty_1"  id="txtlaybalanceqty_1" class="text_boxes_numeric"  placeholder="Display" style="width:60px"  readonly/>
                            </td>
                          <td width="70">
                                <input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1)"  onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).onclick()" />
                                <input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                            </td>
                        </tr>
                      </tbody>
                            
                   </table>
                </fieldset> 
                     
              </td>
         </tr>
        <tr>
               <td colspan="4" align="center" class="">
                    <? 
                       echo load_submit_buttons( $permission, "fnc_cut_lay_info", 0,0,"reset_form('cutandlayentry_1','','','','clear_tr()')",1);
                    ?>
                      <input type="button" id="btn_cost_print" name="btn_cost_print"   style="width:100px;"  class="formbutton" value="Lay Chart"  onClick="generate_report_lay_chart($('#txt_cutting_no').val()+'*'+$('#txt_job_no').val(),'cut_lay_entry_report_print');"/>
                      
                      <input type="button" id="btn_cost_print" name="btn_cost_print"   style="width:105px;"  class="formbutton" value="Woven Lay Chart"  onClick="generate_report_lay_chart($('#txt_cutting_no').val()+'*'+$('#txt_job_no').val(),'cut_lay_entry_report_print_jk');"/>
                    </td>
                    <td align="left" colspan="4">
                  
                </td>				
            </tr>
	</table>
    </form>
	</div>
</body>
           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$("#cbo_location_name").val(0);

</script>
</html>