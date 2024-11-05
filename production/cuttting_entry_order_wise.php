<?
/*-------------------------------------------- Comments
Purpose			: 
Functionality	:	
JS Functions	:
Created by		:	Ashraful Islam 
Creation date 	: 	23-03-2014
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
   echo load_html_head_contents("Cut and Lay Entry","../", 1, 1, $unicode,'','');
   ?>
 <script>
    var txt_job_id=$("#txt_job_no").val();
	var permission='<? echo $permission; ?>';
	$('#txt_cutting_no').focus(); 
function  add_row(id)
{
	var inner_loop=0;
	    inner_loop=inner_loop+1;
	var tbl_id=id.split("_");
	var table_tr="#table_tr_"+tbl_id[2]+"_"+tbl_id[3];
	var size_td="#size_id_"+tbl_id[2]+"_"+tbl_id[3];
	
	var size_name=document.getElementById('txt_size_' + tbl_id[2]+'_'+tbl_id[3]).innerHTML;
	//alert(size_name)
	var tr_id="'new_tr_"+tbl_id[2]+"_"+inner_loop+"'";
	var del_td="'txt_del_"+tbl_id[2]+"_"+inner_loop+"'";
	var size_id=$(size_td).val();
	
	$(table_tr).after("<tr bgcolor='#FEFEE0' id="+tr_id+"><td></td><td>New row</td><td> <input type='hidden' name='size_id_'  id='size_id_'   /> </td><td align='center'><input type='text' class='text_boxes_numeric' style='width:65px'  /></td><td align='center'><input type='text' class='text_boxes_numeric' style='width:65px'  /></td><td align='center'><input type='text' class='text_boxes_numeric' style='width:65px'  /></td><td align='center'><input type='text' class='text_boxes_numeric' style='width:65px'  /><td align='center'><input type='text' class='text_boxes_numeric' style='width:65px'  /></td><td ><input type='button' id="+del_td+" value='Delete'  class='formbuttonplasminus' /></td></tr>");
	var del_tr="#txt_del_"+tbl_id[2]+"_"+inner_loop;
	var del_id="new_tr_"+tbl_id[2]+"_"+inner_loop;
	
	$('#increase_'+i).removeAttr("value").attr("value","+");
  $(del_tr).removeAttr("onclick").attr("onclick","fn_deleteRow("+del_id+");");
	
}

function fn_deleteRow(rowNo) 
		{
			alert(rowNo)
			$("#"+rowNo).remove();
		}
function clear_tr()
{
	$('#txt_cutting_date').val(""); 
	$('#txt_cutting_hour').val(""); 
	total_order=$('#total_order_id').val();	
	var total_qc_pass=0;
	
	for(var j=1; j<=total_order; j++)
		{
			  var txt_row_num="#tbl_body_"+j+"  tr";
		      var row_num=$(txt_row_num).length;
			  var pppp=0;
			  for(var i=1; i<=row_num; i++)
				   {
					 var txt_qc_id="#txt_qcpass_"+j+"_"+i;
					 document.getElementById('txt_qcpass_' + j+'_'+i).value=document.getElementById('hidden_qcpass_' + j+'_'+i).value;   
					 document.getElementById('txt_reject_' + j+'_'+i).value=""; 
					 pppp+=($(txt_qc_id).val()!='')?$(txt_qc_id).val()*1:0;
					   
				   }
			 $('#total_reject_qty_'+j).text("");
			 $('#hidden_reject_qty_'+j).val(0);
		     $('#total_qc_qty_'+j).text(pppp);
			 $('#hidden_total_qc_qty_'+j).val(pppp);
			 total_qc_pass+=pppp;
		}
		 $('#grand_reject_qty').text(0);
		 $('#grand_qc_qty').text(total_qc_pass);
}
		
function fnc_cut_qc_info( operation )
{        

        if(form_validation('txt_cutting_no*txt_cutting_date*txt_cutting_hour','Cutting Number*Cutting Date*Cutting Hour')==false)
			   {
				return;
			   }
			   var current_date='<? echo date("d-m-Y"); ?>';
				if(date_compare($('#txt_cutting_date').val(), current_date)==false)
				{
					alert("Cutting Date Can not Be Greater Than Current Date");
					return;
				}	
		total_order=$('#total_order_id').val();	  
	  	var   txt_qty            ="";
		var   txt_bundle_no      ="";
		var   txt_start          ="";
	    var   txt_end            ="";
     	var   size_id            ="";
		var   txt_qcpass         ="";
		var   txt_reject         ="";
		var   txt_replace        ="";
		var   size_details_id    ="";
		var   size_details_qty   ="";
		var   size_details_bdl   ="";
		var   pcs_per_bdl        ="";
		var   update_details_id  ="";
		var   country_id  ="";
		for(var j=1; j<=total_order; j++)
		{
				
			  	
			   if(j==1) 
				   {
					   
					   var   hidden_color            =$('#hidden_color_'+j).val(); 
					   var   txt_gmt_id              =$('#hidden_gmt_'+j).val();
					   var   txt_order_id            =$('#hidden_po_'+j).val();
					   var   txt_oder_qty            = escape(document.getElementById('txt_oder_qty_' + j).innerHTML); 
					   var   total_reject_qty        = escape(document.getElementById('total_reject_qty_' + j).innerHTML);
					   var   total_qc_qty            = escape(document.getElementById('total_qc_qty_' + j).innerHTML); 
					         size_details_id         =$('#txt_dtls_size_id_'+j).val();
				             size_details_qty        =$('#txt_dtls_size_qty_'+j).val();
							 size_details_bdl        =$('#txt_dtls_size_bdl_'+j).val();
							 pcs_per_bdl             =$('#txt__pcs_per_bdl_'+j).val();
							 country_id             =$('#hidden_country_'+j).val();
					    
				   }
				else 
				   {        hidden_color            =hidden_color+"*"+$('#hidden_color_'+j).val();
				            txt_gmt_id              =txt_gmt_id+"*"+$('#hidden_gmt_'+j).val();
							txt_order_id            =txt_order_id+"*"+$('#hidden_po_'+j).val();
							txt_oder_qty            =txt_oder_qty+"*"+escape(document.getElementById('txt_oder_qty_' + j).innerHTML);
							total_reject_qty         =total_reject_qty+"*"+escape(document.getElementById('total_reject_qty_' + j).innerHTML);
							total_qc_qty            =total_qc_qty+"*"+escape(document.getElementById('total_qc_qty_' + j).innerHTML);
						    size_details_id         =size_details_id+"___"+$('#txt_dtls_size_id_'+j).val();
				            size_details_qty        =size_details_qty+"___"+$('#txt_dtls_size_qty_'+j).val();
						    size_details_bdl        =size_details_bdl+"___"+$('#txt_dtls_size_bdl_'+j).val();
							pcs_per_bdl             =pcs_per_bdl+"___"+$('#txt__pcs_per_bdl_'+j).val();	
							country_id             =country_id+"*"+$('#hidden_country_'+j).val();				
				   }
		   
		       var txt_row_num="#tbl_body_"+j+"  tr";
		       var row_num=$(txt_row_num).length;
			   if(row_num!=0)
				   {
				   for(var i=1; i<=row_num; i++)
					   {
						   
						if(j==1)
						{                  		  
								if(i==1) 
								{ 
								
									 txt_qty      = escape(document.getElementById('txt_qty_' + j+'_'+i).innerHTML); 
									 txt_bundle_no      = escape(document.getElementById('txt_bundle_' + j+'_'+i).innerHTML); 
									 txt_start      = escape(document.getElementById('txt_start_' + j+'_'+i).innerHTML); 
									 txt_end      = escape(document.getElementById('txt_end_' + j+'_'+i).innerHTML); 
									 size_id      =escape(document.getElementById('size_id_' + j+'_'+i).value); 
									 txt_qcpass      =escape(document.getElementById('txt_qcpass_' + j+'_'+i).value); 
									 txt_reject      =escape(document.getElementById('txt_reject_' + j+'_'+i).value); 
									 txt_replace      =escape(document.getElementById('txt_replace_' + j+'_'+i).value); 
									 update_details_id      =escape(document.getElementById('update_details_id_' + j+'_'+i).value); 
									
								 }
									 
							   else 
								 {
									 txt_qty            =txt_qty+"*"+escape(document.getElementById('txt_qty_' + j+'_'+i).innerHTML);
									 txt_bundle_no      =txt_bundle_no+"*"+escape(document.getElementById('txt_bundle_' + j+'_'+i).innerHTML);
									 txt_start          =txt_start+"*"+escape(document.getElementById('txt_start_' + j+'_'+i).innerHTML);
									 txt_end            =txt_end+"*"+escape(document.getElementById('txt_end_' + j+'_'+i).innerHTML);
									 size_id            =size_id+"*"+(document.getElementById('size_id_' + j+'_'+i).value);
									 txt_qcpass         =txt_qcpass+"*"+escape(document.getElementById('txt_qcpass_' + j+'_'+i).value);
									 txt_reject         =txt_reject+"*"+escape(document.getElementById('txt_reject_' + j+'_'+i).value);
									 txt_replace        =txt_replace+"*"+escape(document.getElementById('txt_replace_' + j+'_'+i).value);
									 update_details_id  =update_details_id+"*"+escape(document.getElementById('update_details_id_' + j+'_'+i).value);		
								 }
						}
						else
						{
							if(i==1)  
								  { 
									  txt_qty            =txt_qty+"___"+ escape(document.getElementById('txt_qty_' + j+'_'+i).innerHTML);
									  txt_bundle_no      = txt_bundle_no+"___"+escape(document.getElementById('txt_bundle_' + j+'_'+i).innerHTML); 
									  txt_start          = txt_start+"___"+escape(document.getElementById('txt_start_' + j+'_'+i).innerHTML); 
									  txt_end            = txt_end+"___"+escape(document.getElementById('txt_end_' + j+'_'+i).innerHTML); 
									  size_id            =size_id+"___"+escape(document.getElementById('size_id_' + j+'_'+i).value); 
									  txt_qcpass         =txt_qcpass+"___"+escape(document.getElementById('txt_qcpass_' + j+'_'+i).value);
									  txt_reject         =txt_reject+"___"+escape(document.getElementById('txt_reject_' + j+'_'+i).value); 
									  txt_replace        =txt_replace+"___"+escape(document.getElementById('txt_replace_' + j+'_'+i).value); 
									  update_details_id  =update_details_id+"___"+escape(document.getElementById('update_details_id_' + j+'_'+i).value); 
								  }
							 else
								  {
									   txt_qty            =txt_qty+"*"+escape(document.getElementById('txt_qty_' + j+'_'+i).innerHTML);
									   txt_bundle_no      =txt_bundle_no+"*"+escape(document.getElementById('txt_bundle_' + j+'_'+i).innerHTML);
									   txt_start          =txt_start+"*"+escape(document.getElementById('txt_start_' + j+'_'+i).innerHTML);
									   txt_end            =txt_end+"*"+escape(document.getElementById('txt_end_' + j+'_'+i).innerHTML);
									   size_id            =size_id+"*"+(document.getElementById('size_id_' + j+'_'+i).value);
									   txt_qcpass         =txt_qcpass+"*"+escape(document.getElementById('txt_qcpass_' + j+'_'+i).value);	
									   txt_reject         =txt_reject+"*"+escape(document.getElementById('txt_reject_' + j+'_'+i).value);	
									   txt_replace        =txt_replace+"*"+escape(document.getElementById('txt_replace_' + j+'_'+i).value);
									   update_details_id  =update_details_id+"*"+escape(document.getElementById('update_details_id_' + j+'_'+i).value);	
								  }
						}
						
				  }
			   }
		}
			//alert(update_details_id) txt_replace_
		
				var cbo_company						   = escape(document.getElementById('cbo_company_name').value);
				var cbo_location_name				   = escape(document.getElementById('cbo_location_name').value);
				var cbo_floor_name					   = escape(document.getElementById('cbo_floor_name').value);
				var txt_cutting_date				   = escape(document.getElementById('txt_cutting_date').value);
				var txt_cutting_hour				   = escape(document.getElementById('txt_cutting_hour').value);	
				var txt_batch_no				       = escape(document.getElementById('txt_batch_no').value);
				var txt_cutting_no                     = escape(document.getElementById('txt_cutting_no').value);
			    var txt_cut_prifix                     = escape(document.getElementById('txt_cut_prifix').value);
				
				var txt_table_no					   = escape(document.getElementById('txt_table_no').value);
				var txt_marker_length				   = escape(document.getElementById('txt_marker_length').value);
				var txt_marker_width				   = escape(document.getElementById('txt_marker_width').value);
				var txt_fabric_width				   = escape(document.getElementById('txt_fabric_width').value);
				var txt_gsm				               = escape(document.getElementById('txt_gsm').value);	
				var txt_job_no				           = escape(document.getElementById('txt_job_no').value);
				var txt_entry_date                     = escape(document.getElementById('txt_entry_date').value);
				var txt_in_time_hours                  = escape(document.getElementById('txt_in_time_hours').value);
				var txt_in_time_minuties               = escape(document.getElementById('txt_in_time_minuties').value);
				
				var txt_out_time_hours			       = escape(document.getElementById('txt_out_time_hours').value);	
				var txt_out_time_minuties		       = escape(document.getElementById('txt_out_time_minuties').value);
				var cbo_width_dia                     = escape(document.getElementById('cbo_width_dia').value);
				var txt_end_date                      = escape(document.getElementById('txt_end_date').value);
				var update_id                         = escape(document.getElementById('update_id').value);
				var txt_system_no                     = escape(document.getElementById('txt_system_no').value);
				//txt_cut_prifix update_idtxt_system_no
				
			var data='action=save_update_delete&operation='+operation+
				'&cbo_company='+cbo_company+
				'&cbo_location_name='+cbo_location_name+
				'&cbo_floor_name='+cbo_floor_name+
				'&txt_cutting_date='+txt_cutting_date+
				'&txt_cutting_hour='+txt_cutting_hour+
				'&txt_cutting_no='+txt_cutting_no+
				'&txt_batch_no='+txt_batch_no+
				'&txt_cut_prifix='+txt_cut_prifix+
				'&country_id='+country_id+
				
				'&txt_table_no='+txt_table_no+
				'&txt_marker_length='+txt_marker_length+
				'&txt_marker_width='+txt_marker_width+
				'&txt_fabric_width='+txt_fabric_width+
				'&txt_gsm='+txt_gsm+
				'&txt_job_no='+txt_job_no+
				'&txt_entry_date='+txt_entry_date+
				'&txt_in_time_hours='+txt_in_time_hours+
				
				'&txt_in_time_minuties='+txt_in_time_minuties+
				'&txt_out_time_hours='+txt_out_time_hours+
				'&txt_out_time_minuties='+txt_out_time_minuties+
				'&cbo_width_dia='+cbo_width_dia+
				'&txt_end_date='+txt_end_date+
				'&update_details_id='+update_details_id+
				'&update_id='+update_id+
				'&txt_system_no='+txt_system_no+
				
				'&size_details_id='+size_details_id+
				'&size_details_qty='+size_details_qty+
				'&size_details_bdl='+size_details_bdl+  
				'&pcs_per_bdl='+pcs_per_bdl+
				'&txt_order_id='+txt_order_id+
				'&hidden_color='+hidden_color+
				'&txt_gmt_id='+txt_gmt_id+
				'&txt_oder_qty='+txt_oder_qty+
				'&txt_qty='+txt_qty+
				'&txt_bundle_no='+txt_bundle_no+
				'&txt_start='+txt_start+	
				'&txt_end='+txt_end+
				'&size_id='+size_id+					
				'&txt_qcpass='+txt_qcpass+
				'&txt_replace='+txt_replace+
				'&total_qc_qty='+total_qc_qty+
				'&total_reject_qty='+total_reject_qty+
				'&txt_reject='+txt_reject;
				
				
				//alert(data);
	
		freeze_window(operation);
		http.open("POST","requires/cutting_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_cut_qc_info_reponse;
	}


function fnc_cut_qc_info_reponse()
{
	if(http.readyState == 4) 
	{
		//alert(http.responseText);
		var reponse=trim(http.responseText).split('**');
		show_msg(reponse[0]);
		if(reponse[0]==0 || reponse[0]==1)
		{
		$("#txt_system_no").val(reponse[1]);
		set_button_status(1, permission, 'fnc_cut_qc_info',1,1);
		get_php_form_data( reponse[1], "load_system_mst_form", "requires/cutting_entry_controller" );
		show_list_view( reponse[1]+'**'+reponse[2], 'update_order_details_list', 'cut_details_container', 'requires/cutting_entry_controller', '' ) ;
		}
		
		
		release_freezing();
	}
} 

function open_cutting_popup()
{ 
	/*if( form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}*/ 
	var company_id=$("#cbo_company_name").val();
	var page_link='requires/cutting_entry_controller.php?action=cutting_number_popup&company_id='+company_id; 
	var title="Search Cutting Number Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=420px,center=1,resize=0,scrolling=0','');
	emailwindow.onclose=function()
	{
		var sysNumber = this.contentDoc.getElementById("update_mst_id"); 
		var sysNumber=sysNumber.value.split('_');
		get_php_form_data( sysNumber[0], "load_php_mst_form", "requires/cutting_entry_controller" );
		show_list_view( sysNumber[0], 'order_details_list', 'cut_details_container', 'requires/cutting_entry_controller', '' ) ;
		$("#cbo_company_name").attr("disabled",true);
		$("#txt_job_no").attr("disabled",true);
		$("#txt_job_year").attr("disabled",true);
		
	     $("#cbo_location_name").attr("disabled",true);
		$("#cbo_floor_name").attr("disabled",true);
		$("#txt_table_no").attr("disabled",true);
		$("#txt_marker_length").attr("disabled",true);
		$("#txt_marker_width").attr("disabled",true);
        $("#txt_fabric_width").attr("disabled",true);
        
        $("#txt_gsm").attr("disabled",true);
		$("#txt_batch_no").attr("disabled",true);
		$("#txt_entry_date").attr("disabled",true);
		$("#txt_in_time_hours").attr("disabled",true);
		$("#txt_in_time_minuties").attr("disabled",true);
		$("#txt_end_date").attr("disabled",true);
        
        $("#txt_out_time_minuties").attr("disabled",true);
		$("#txt_out_time_hours").attr("disabled",true);
		$("#cbo_width_dia").attr("disabled",true);
		//set_button_status(0, permission, 'fnc_cut_qc_info',1,1);
	
 	}
}



function system_number_popup()
{ 
	/*if( form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}*/ 
	var company_id=$("#cbo_company_name").val();
	var page_link='requires/cutting_entry_controller.php?action=system_number_popup&company_id='+company_id; 
	var title="Search Cutting Number Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=420px,center=1,resize=0,scrolling=0','');
	emailwindow.onclose=function()
	{
		var sysNumber = this.contentDoc.getElementById("update_mst_id"); 
		var sysNumber=sysNumber.value.split('_');
		get_php_form_data( sysNumber[0], "load_system_mst_form", "requires/cutting_entry_controller" );
		show_list_view( sysNumber[0]+'**'+sysNumber[1], 'update_order_details_list', 'cut_details_container', 'requires/cutting_entry_controller', '' ) ;
		
		$("#txt_system_no").val(sysNumber[0]);
		$("#cbo_company_name").attr("disabled",true);
		$("#txt_job_no").attr("disabled",true);
		$("#txt_job_year").attr("disabled",true);
	     $("#cbo_location_name").attr("disabled",true);
		$("#cbo_floor_name").attr("disabled",true);
		$("#txt_table_no").attr("disabled",true);
		$("#txt_marker_length").attr("disabled",true);
		$("#txt_marker_width").attr("disabled",true);
        $("#txt_fabric_width").attr("disabled",true);
        $("#txt_gsm").attr("disabled",true);
		$("#txt_batch_no").attr("disabled",true);
		$("#txt_entry_date").attr("disabled",true);
		$("#txt_in_time_hours").attr("disabled",true);
		$("#txt_in_time_minuties").attr("disabled",true);
		$("#txt_end_date").attr("disabled",true);
        
        $("#txt_out_time_minuties").attr("disabled",true);
		$("#txt_out_time_hours").attr("disabled",true);
		$("#cbo_width_dia").attr("disabled",true);
		//set_button_status(0, permission, 'fnc_cut_qc_info',1,1);
	
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

	$('#txt_cutting_no').live('keydown', function(e) {
	   
		if (e.keyCode === 13) {
			e.preventDefault();
			  scan_cut_number(this.value); 
		}
	});	
	function scan_cut_number(str)
		 {
		
			get_php_form_data( str, "load_php_mst_form", "requires/cutting_entry_controller" );
			show_list_view( str, 'order_details_list', 'cut_details_container', 'requires/cutting_entry_controller', '' ) ;
			$("#cbo_company_name").attr("disabled",true);
			$("#txt_job_no").attr("disabled",true);
			$("#txt_job_year").attr("disabled",true);
			
			 $("#cbo_location_name").attr("disabled",true);
			$("#cbo_floor_name").attr("disabled",true);
			$("#txt_table_no").attr("disabled",true);
			$("#txt_marker_length").attr("disabled",true);
			$("#txt_marker_width").attr("disabled",true);
			$("#txt_fabric_width").attr("disabled",true);
			
			$("#txt_gsm").attr("disabled",true);
			$("#txt_batch_no").attr("disabled",true);
			$("#txt_entry_date").attr("disabled",true);
			$("#txt_in_time_hours").attr("disabled",true);
			$("#txt_in_time_minuties").attr("disabled",true);
			$("#txt_end_date").attr("disabled",true);
			
			$("#txt_out_time_minuties").attr("disabled",true);
			$("#txt_out_time_hours").attr("disabled",true);
			$("#cbo_width_dia").attr("disabled",true);
		 }

	
	function total_qc_pass(ids,value,order_sequence)
	 { 
	    var id=ids.split("_");
		var txt_reject=$("#txt_reject_"+id[2]+"_"+id[3]).val();
		var hidden_qcpass="#hidden_qcpass_"+id[2]+"_"+id[3];
		var txt_replace_qty=$("#txt_replace_"+id[2]+"_"+id[3]).val();
		var txt_qc_pass="#txt_qcpass_"+id[2]+"_"+id[3];
		var cutting_qty=$(hidden_qcpass).val();         
		if(txt_replace_qty=="")  txt_replace_qty=0;
		if(parseInt(txt_reject)<=parseInt(cutting_qty)) 
		{
			
			var qc_pass=((cutting_qty*1)-(txt_reject*1)+(txt_replace_qty*1));
			$(txt_qc_pass).val(qc_pass);
			total_order=$('#total_order_id').val();
			var total_qc_pass=0;
			var total_reject_quantity=0;
			var total_replace_quantity=0;
			for(var j=1; j<=total_order; j++)
			{
				 var txt_row_num="#tbl_body_"+j+"  tr";
				 var row_num=$(txt_row_num).length;
				 var hidden_order_id=$('#hidden_order_'+j).val();
				 var xxxx=0;
				 var yyyy=0;
				  var zzzz=0;
				 if(order_sequence==j)
				 {
				   for(var i=1; i<=row_num; i++)
					   {
						   var txt_qc_id="#txt_qcpass_"+id[2]+"_"+i;
						   xxxx+=($(txt_qc_id).val()!='')?$(txt_qc_id).val()*1:0;
						  // total_qc_pass+=($(txt_qc_id).val()!='')?$(txt_qc_id).val()*1:0; 
						 
					   }
					   
				  $('#total_qc_qty_'+j).text(xxxx);
				  $('#hidden_total_qc_qty_'+j).val(xxxx);
				  for(var i=1; i<=row_num; i++)
					   {
						   
						   var txt_reject_id="#txt_reject_"+id[2]+"_"+i;
						   var txt_replace_id="#txt_replace_"+id[2]+"_"+i;
						   //alert($(txt_replace_id).val());
						   yyyy+=($(txt_reject_id).val()!='')?$(txt_reject_id).val()*1:0; 
						   zzzz+=($(txt_replace_id).val()!='')?$(txt_replace_id).val()*1:0; 
						  // total_replace+=($(txt_replace_id).val()!='')?$(txt_replace_id).val()*1:0; 
					   }
				  $('#total_reject_qty_'+j).text(yyyy);
				 // $('#total_reject_qty_'+j).text(yyyy);
				  $('#total_replace_qty_'+j).text(zzzz);
				  $('#hidden_reject_qty_'+j).val(yyyy);
				  $('#hidden_replace_qty_'+j).val(zzzz);
				 }
				
				total_reject_quantity+=parseInt($('#hidden_reject_qty_'+j).val());
				total_qc_pass+=parseInt($('#hidden_total_qc_qty_'+j).val());
				total_replace_quantity+=parseInt($('#hidden_replace_qty_'+j).val());
				//alert(total_replace)
			}
			//alert(total_reject_quantity)
			 $('#grand_reject_qty').text(total_reject_quantity);
			 $('#grand_qc_qty').text(total_qc_pass);
			 $('#grand_replace_qty').text(total_replace_quantity);
		}
		else
		{
			$("#"+ids).val("");
			$(txt_qc_pass).val(cutting_qty);
			
		}
	 }
	
	 function focace_change()
	  {
		$('#txt_cutting_no').focus();  
	  }		
	  
	  
function fnc_valid_time(val,field_id)
{
	var val_length=val.length;
	if(val_length==2)
	{
		document.getElementById(field_id).value=val+":";
	}
	
	var colon_contains=val.contains(":");
	if(colon_contains==false)
	{
		if(val>23)
		{
			document.getElementById(field_id).value='23:';
		}
	}
	else
	{
		var data=val.split(":");
		var minutes=data[1];
		var str_length=minutes.length;
		var hour=data[0]*1;
		
		if(hour>23)
		{
			hour=23;
		}
		
		if(str_length>=2)
		{
			minutes= minutes.substr(0, 2);
			if(minutes*1>59)
			{
				minutes=59;
			}
		}
		
		var valid_time=hour+":"+minutes;
		document.getElementById(field_id).value=valid_time;
	}
}

function numOnly(myfield, e, field_id)
{
	var key;
	var keychar;
	if (window.event)
		key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;
	keychar = String.fromCharCode(key);

	// control keys
	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
	return true;
	// numbers
	else if ((("0123456789:").indexOf(keychar) > -1))
	{
		var dotposl=document.getElementById(field_id).value.lastIndexOf(":");
		if(keychar==":" && dotposl!=-1)
		{
			return false;
		}
		return true;
	}
	else
		return false;
}
 	
</script>
 
</head>
 
<body onLoad="set_hotkey();focace_change()">

<div style="width:1200px;" align="center">
	
     <? echo load_freeze_divs ("../",$permission);  ?>
     <form name="cutandlayentry_1" id="cutandlayentry_1">
    
    <table width="95%" cellpadding="0" cellspacing="2" align="center">
     	<tr>
        	<td width="95%" align="center" valign="top">  
            	<fieldset style="width:1000px;">
                     <legend>cutting entry</legend>
                        <table  width="1050" cellspacing="2" cellpadding="0" border="0">
                         <tr>
                            <td colspan="4" align="right"><b>System Number</b></td>
                            <td  align="left"><input type="text" name="txt_system_no" id="txt_system_no" class="text_boxes" style="width:130px" placeholder="Browse " onDblClick="system_number_popup()"   /></td>
                      </tr>
                           <tr>
                                <td  width="110"  align="left" class="must_entry_caption">Cutting No</td>              <!-- 11-00030  -->
                                 <td  align="left"><input type="text" name="txt_cutting_no" id="txt_cutting_no" class="text_boxes" style="width:130px" placeholder="Browse or Scan" onDblClick="open_cutting_popup()"   /></td>
                                <td  width="110"  align="left" class="must_entry_caption"> Company Name</td>              <!-- 11-00030  -->
                                <td  width="150" align="" >
                                 <? 
                                    echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/cutting_entry_controller', this.value, 'load_drop_down_location', 'location_td' );",1 );
                                ?>
                                </td>
                                 <td  width="110" align="left" >Locaton </td>
                                <td width="150" id="location_td">
                                     <? 
                                    echo create_drop_down( "cbo_location_name", 140, $blank_array,"", 1, "-- Select Location --", $selected, "",1 );
                                     ?>
                                </td>
                               <td width="120" align="left" >Floor</td>
                                <td  width="140" id="floor_td">
                                    <? 
									//echo create_drop_down( "cbo_floor_name", 140, "select id,floor_name from lib_prod_floor where production_process=1 and status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 ); 
                                   echo create_drop_down( "cbo_floor_name", 140,"select id,floor_name from lib_prod_floor where production_process=1  and status_active =1 and is_deleted=0","id,floor_name", 1, "-- Select Floor --", $selected, "",1 );
                                     ?>
                               
                                </td>
                          </tr>
                          <tr>
                                <td   align="left">Table No </td>
                                <td >
                                    <input style="width:130px;" type="text"   class="text_boxes_numeric" autocomplete="off"  name="txt_table_no" id="txt_table_no"   disabled/>
                                </td>
                               
                                <td  align="left">CAD Marker length
                                </td>
                                <td   align="left">
                                   <input style="width:140px;" type="text"  class="text_boxes" autocomplete="off"  name="txt_marker_length" id="txt_marker_length" disabled/>
                                     
                                </td>
                                <td   height="" align="left">CAD Marker Width  </td>           
                                <td   >
                                        <input style="width:130px;" type="text"  class="text_boxes_numeric" autocomplete="off"  name="txt_marker_width" id="txt_marker_width" disabled/>
                                </td>
                                 <td  width="" height="" align="left">CAD Fabric Width </td>         
                                <td  width="" >
                                     <input type="text" name="txt_fabric_width" id="txt_fabric_width" class="text_boxes_numeric" style="width:130px"  disabled/>
                                </td>
                          </tr>
                           <tr>
                                <td width="" align="left">CAD GSM</td>
                                <td >
                                     <input style="width:130px;" type="text"  class="text_boxes_numeric" autocomplete="off"  name="txt_gsm" id="txt_gsm" disabled/>  
                                </td>
                                     
                                 <td  width="" class="must_entry_caption">Job No</td>
                                 <td width=""  id="job_change_id">
                                     <input style="width:140px;" type="text"  onDblClick="openmypage_jobNo()" class="text_boxes" autocomplete="off"  name="txt_job_no" id="txt_job_no"  disabled/>

                                  </td>
                                  <td  width="" align="left" >Year</td>
                                  <td>
                                     <?
                                      	     echo create_drop_down( "txt_job_year", 140, $year,"", 1, "-- Select year --", $selected, "change_order(this.value)",1);
											 ?>

                                  </td> 
                                  <td  width="" align="left">Batch   </td>
                                  <td>
                                          <input style="width:130px;" type="text"   class="text_boxes" autocomplete="off"  name="txt_batch_no" id="txt_batch_no"  disabled/>
                                  </td>
                                    
                            </tr>
                             <tr>
                                 <td  width="" align="left" >Buyer </td>
                                     <td id="buyer_id">
                                          	<? 
                                    echo create_drop_down( "txt_buyer_name", 140,"select id, buyer_name from  lib_buyer","id,buyer_name", 1, "", $selected, "" ,1);
                                     ?>
                                     </td>
                              
                                <td width="" align="left">Plan Start Date
                                </td>
                                <td  width="" align="left">
                                      <input style="width:140px;" type="text"   class="datepicker" autocomplete="off"  name="txt_entry_date" id="txt_entry_date"  disabled />
                                      <input type="hidden" name="update_job_no"  id="update_job_no"  />
                                      <input type="hidden" name="update_id"  id="update_id"  />
                                      <input type="hidden" name="txt_cut_prifix"  id="txt_cut_prifix"  />
                               
                                </td>
                                <td  width="" height="" align="left"> Start Time</td>         
                                <td  width="" >
                                     <input type="text" name="txt_in_time_hours" id="txt_in_time_hours" class="text_boxes_numeric" placeholder="HH"  style="width:30px;"  onKeyUp="fnc_move_cursor(this.value,'txt_in_time_hours','txt_in_time_minuties',2,23);"  disabled/> :
                                    <input type="text" name="txt_in_time_minuties" id="txt_in_time_minuties" class="text_boxes_numeric" placeholder="MM"  style="width:30px;" onKeyUp="fnc_move_cursor(this.value,'txt_in_time_minuties','txt_in_time_seconds',2,59)" onBlur="fnc_intime_populate(this.value,'txt_in_time_hours')"  disabled/>
                                </td>
                                <td width="" align="left">Plan End Date
                                </td>
                                <td  width="" align="left">
                                   <input style="width:130px;" type="text"   class="datepicker" autocomplete="off"  name="txt_end_date" id="txt_end_date"  disabled/>
                                 </td>
                          </tr>
                          <tr>
                                 
                               <td  width="90" height="" align="left"> End Time </td>         
                               <td  width="120" >
                                    <input type="text" name="txt_out_time_hours" id="txt_out_time_hours" class="text_boxes_numeric" placeholder="HH"  style="width:30px;"  onKeyUp="fnc_move_cursor(this.value,'txt_out_time_hours','txt_out_time_minuties',2,23);"  disabled/> :
                                    <input type="text" name="txt_out_time_minuties" id="txt_out_time_minuties" class="text_boxes_numeric" placeholder="MM"  style="width:30px;"  onKeyUp="fnc_move_cursor(this.value,'txt_out_time_minuties','txt_out_time_seconds',2,59)" onBlur="fnc_outtime_populate(this.value,'txt_out_time_hours')" disabled/> 
    
                                </td>
                                <td width="" align="left">Width/Dia Type
                                </td>
                                <td  width="" align="left">
                                     <? 
                                      echo create_drop_down( "cbo_width_dia", 150, $fabric_typee,"", 1, "-- Select --", "", "",1,"" );              
                                    ?>
                               
                                </td>
                                <td  width="" height="" align="left" class="must_entry_caption"> Cutting QC Date </td>           
                                <td  width="" >
                                      <input style="width:130px;" type="text"   class="datepicker" autocomplete="off"  name="txt_cutting_date" id="txt_cutting_date"  />
                                </td>
                               <td width="" align="left" class="must_entry_caption">Cutting Hour
                                </td>
                                <td  width="" align="left">
                            
                                   <input name="txt_cutting_hour" id="txt_cutting_hour" class="text_boxes" style="width:128px" placeholder="24 Hour Format" onBlur="fnc_valid_time(this.value,'txt_cutting_hour');" onKeyUp="fnc_valid_time(this.value,'txt_cutting_hour');" onKeyPress="return numOnly(this,event,this.id);" maxlength="8" />
                                 </td>
                               
                            </tr>
                      </table>

                 </fieldset>
              </td>
         </tr>
         <tr>
             <td align="center" valign="top" id="po_list_view">
            
                     
              </td>
         </tr>
      
	</table>
       <fieldset style="width:700px; margin-top:10px">
               <legend>Cutting QC</legend>
               <div style="width:680px; margin-top:10px" id="cut_details_container" align="left">   
                </div>
                
        </fieldset> 
    </form>
	</div>
 
</body>
           
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>

$('#txt_cutting_no').focus(); 
</script>
</html>