<?
/*-------------------------------------------- Comments
Purpose			: Yarn lot ratio planning
Functionality	:	
JS Functions	:
Created by		:	Ashraful Islam
Creation date 	: 	28-11-2018
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
echo load_html_head_contents("Cut and Lay Entry Ratio Wise","../../", 1, 1, $unicode,'','');

$color_type_mandatory_sql=sql_select( "select company_name, color_type_mandatory from variable_settings_production where variable_list=8 and status_active=1");
$color_type_mandatory_arr=array();
if(count($color_type_mandatory_sql)>0)
{
foreach($color_type_mandatory_sql as $key=>$value)
{
	$color_type_mandatory_arr[$value[csf("company_name")]]=$value[csf("color_type_mandatory")];
}
//print_r($color_type_mandatory_arr);
$color_type_mandatory_arr=json_encode($color_type_mandatory_arr);
}

?>
<script>
	var color_type_mandatory_arrs=Array();
	var color_type_mandatory_arr='<? echo $color_type_mandatory_arr;?>';
 	color_type_mandatory_arrs=JSON.parse(color_type_mandatory_arr); 

    var txt_job_id=$("#txt_job_no").val();
	var permission='<? echo $permission; ?>';
	function change_type_color(comp)
	{
		if(color_type_mandatory_arrs[comp]==2)
		{
			$("#color_type_td_id").removeAttr('class','').removeAttr('title','');
			$("#color_type_td_id font").css('color','#524444') ;
		}
		else
		{

		}
	}
	
	function cut_no_duplication_check(row_id)
	{
		var row_num=$('#tbl_list_search tr').length;
		var row_num=$('#tbl_order_details tbody tr').length;
		var orderCutNo=$('#orderCutNo_'+row_id).val();
		var cbocolor=$('#cbocolor_'+row_id).val();
		
		if(orderCutNo*1>0)
		{
			for(var j=1; j<=row_num; j++)
			{
				if(j==row_id)
				{
					continue;
				}
				else
				{
					var cut_no_check=$('#orderCutNo_'+j).val();	
					var cbocolor_check=$('#cbocolor_'+j).val();	
					
					if(orderCutNo==cut_no_check && cbocolor==cbocolor_check)
					{
						alert("Duplicate Order Cut No.");
						$('#orderCutNo_'+row_id).val('');
						return;
					}
				}
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
		var page_link = 'requires/yarn_lot_ratio_planning_controller.php?cbo_company_id='+cbo_company_id+'&action=job_search_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1030px,height=400px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var job_no=(this.contentDoc.getElementById("hidden_job_no").value).split('_');
			$('#txt_buyer_name').val(job_no[1]);
			$('#txt_job_year').val(job_no[2]);
			$('#txt_style_no').val(job_no[3]);
			document.getElementById('txt_job_no').value=job_no[0];
			
			$('#tbl_order_details tbody tr:not(:first)').remove();
			get_php_form_data( job_no[3], "load_drop_down_bodypart", "requires/yarn_lot_ratio_planning_controller" );
			load_drop_down('requires/yarn_lot_ratio_planning_controller', job_no[0]+'_'+1, 'load_drop_down_order_garment','garment_1');
			load_drop_down('requires/yarn_lot_ratio_planning_controller', job_no[0]+'_'+1+'_'+$('#cbogmtsitem_1').val(), 'load_drop_down_color','color_1');
			load_drop_down('requires/yarn_lot_ratio_planning_controller', job_no[0]+'_'+1, 'load_drop_down_color_type','colorTypeId_1');
			
			var length=$("#color_1 option").length;
			if(length==2)
			{
				$('#color_1').val($('#cboProgramNo_1 option:last').val());
			}
			$("#cbo_company_name").attr("disabled",true);
			release_freezing();
		}
	}
	
	function open_bodypart_popup()
	{
		var hidden_body_partstring=$("#hidden_body_partstring").val();
		if(form_validation('txt_job_no','Job No.')==false)
		{
			return;
		}
		
		var title = 'PO Selection Form';	
		var page_link = 'requires/yarn_lot_ratio_planning_controller.php?hidden_body_partstring='+hidden_body_partstring+'&action=bodypart_popup';
 		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=300px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
		}
	}
	
	function style_order_data(str)
	{
		var strex=str.split('_');
		var row_no=strex[0];
		var type=strex[1];
		
		if(form_validation('cbo_company_name*txt_job_no*cbogmtsitem_'+row_no,'Company Name*Job No*Gmt Item')==false)
		{
			return;
		}
		
		var mst_id = $('#update_id').val();
		var txt_job=$("#txt_job_no").val();
		var cbocolor=$('#cbocolor_'+row_no).val();
		var gmt_id = $('#cbogmtsitem_'+row_no).val();
		if(type==1)
		{
			load_drop_down('requires/yarn_lot_ratio_planning_controller', txt_job+'_'+1+'_'+gmt_id, 'load_drop_down_color','color_1');
		}
		get_php_form_data( txt_job+"_"+gmt_id+"_"+cbocolor+"_"+row_no+"_"+mst_id, "load_drop_down_order_qty", "requires/yarn_lot_ratio_planning_controller" );
	}
	
	function reset_fld(i)
	{
		//$('#poId_'+i).val('');
		//$('#cboPoNo_'+i).val('');
		$('#orderCutNo_'+i).val('');
		//$("#cbobatch_'+i+' option[value!='0']").remove();
	}

	function change_data(value,id)
    { 
	    var id=id.split('_');
		var ship_id='ship_'+id[1];
		var order_qty_id='order_'+id[1];
		var gmt_id='garment_'+id[1];
		
		$('#orderCutNo_'+id[1]).val();
		
		load_drop_down( 'requires/yarn_lot_ratio_planning_controller', value+"_"+ship_id, 'load_drop_down_ship', ship_id);
		load_drop_down( 'requires/yarn_lot_ratio_planning_controller', value+"_"+gmt_id, 'load_drop_down_order_garment', gmt_id);
		var gmt_value=$("#cbogmtsitem_"+id[1]).val();
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
		load_drop_down('requires/yarn_lot_ratio_planning_controller', order_id+"_"+value+"_"+id[1], 'load_drop_down_color', color_id);
		var color_value=$("#cbocolor_"+id[1]).val();
		if(color_value!=0)
		{
			var color_id="cbogmtsitem_"+id[1];
		  	change_marker(color_id,color_value);	
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
		
		get_php_form_data( order_id+"_"+gmt_id+"_"+value+"_"+id[1], "load_drop_down_order_qty", "requires/yarn_lot_ratio_planning_controller" );
		load_drop_down('requires/yarn_lot_ratio_planning_controller', order_id+"_"+value+"_"+id[1], 'load_drop_down_batch', 'batch_'+id[1]);
		var length=$("#cbobatch_"+id[1]+" option").length;
		if(length==2)
		{
			$('#cbobatch_'+id[1]).val($('#cbobatch_'+id[1]+' option:last').val());
		}
	}
	
	

	function openmypage_sizeNo(id)
	{
		//$('#'+id).attr("onkeydown","openmypage_sizeNo(id);");
		var job_id = $('#txt_job_no').val();
		var size_set_no = $('#txt_size_set_no').val();
		var cbo_company_id = $('#cbo_company_name').val();
		var id=id.split('_');
		var size=id[1];
		var mst_id = $('#update_id').val();
		var details_id= $('#updateDetails_'+size).val();
		var rollData=$('#rollData_'+size).val();
		
		if(details_id=="" && mst_id=="")
		{
		   alert("Please save first");return;	
		}
		
		if(details_id=="" && mst_id!="")
		{
		   alert("Please Update first");return;	
		}

		var marker_quantity = $('#txtmarkerqty_'+size).val();
		var order_quantity = $('#txtorderqty_'+size).val();
		var total_lay_qty = $('#txttotallay_'+size).val();
		var total_lay_balance = $('#txtlaybalanceqty_'+size).val();
		var piles = $('#txtplics_'+size).val();
		var cutting_no = $('#txt_cutting_no').val();
		var cbo_color_id = $('#cbocolor_'+size).val();
		var cbo_color_type = $('#cboColorType_'+size).val();
		var cbo_gmt_id = $('#cbogmtsitem_'+size).val();
		var sizeid=$('#hiddsizeid_1').val();
		$("#tr_"+size).css({"background-color":"yellow"});
	//	$("#tbl_order_details tbody tr_"+size).css({"background-color":"red"});
		var title = 'Size Ratio Form';
		
		var page_link = 'requires/yarn_lot_ratio_planning_controller.php?cbo_company_id='+cbo_company_id+'&job_id='+job_id+'&mst_id='+mst_id+'&details_id='+details_id+'&cbo_gmt_id='+cbo_gmt_id+'&cbo_color_id='+cbo_color_id+'&size='+size+'&txt_piles='+piles+'&cutting_no='+cutting_no+'&marker_quantity='+marker_quantity+'&order_quantity='+order_quantity+'&total_lay_qty='+total_lay_qty+'&total_lay_balance='+total_lay_balance+'&rollData='+rollData+'&action=size_popup'+'&cbo_color_type='+cbo_color_type+'&sizeid='+sizeid+'&size_set_no='+size_set_no;//+'&size_wise_repeat_cut_no='+size_wise_repeat_cut_no
		//alert(page_link)
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=500px,center=1,resize=0,scrolling=0','../../');
		emailwindow.onclose=function()
		{ 
			get_php_form_data( job_id+"_"+cbo_gmt_id+"_"+cbo_color_id+"_"+size+"_"+mst_id, "load_drop_down_order_qty", "requires/yarn_lot_ratio_planning_controller" );
			
		 }
	 }

	function compare_date( fdate, tdate)
	{
		var fdate=fdate.split('-');
		var new_date_from=fdate[2]+'-'+fdate[1]+'-'+fdate[0];
		
		var tdate=tdate.split('-');
		var new_date_to=tdate[2]+'-'+tdate[1]+'-'+tdate[0];
		
		var fromDate=new Date(new_date_from);
		var toDate=new Date(new_date_to);
		
		if(toDate.getTime() < fromDate.getTime())
		{
			 return false;
		} 
		else
		{
			return true;
		}
	}
	
	function fnc_cut_lay_info( operation )
	{      
		var cut_start_date=$("#txt_entry_date").val();
		var cut_finish_date=$("#txt_end_date").val();
		var comp=$("#cbo_company_name").val();
		// if(!compare_date(cut_start_date,cut_finish_date))
		// {
  //   		alert("Plan End Date Must Be Greater Than Plan Start Date");
		// 	return;
		// }
		
		if(form_validation('cbo_company_name*cbo_knitting_source*cbo_working_company_name*txt_job_no','Company Name*Source*Working Company*Job No')==false)
	  	{
			return;
		}
		
		if($("#cbo_knitting_source").val()==1)
		{
			if(form_validation('cbo_location_name*cbo_floor','WC Location*Floor')==false)
			{
				return;
			}
		}
        var row_num=$('#tbl_order_details tbody tr').length;
		
        var data1="action=save_update_delete&operation="+operation+"&row_num="+row_num+get_submitted_data_string('update_id*txt_job_no*cbo_company_name*cbo_working_company_name*cbo_floor*cbo_location_name*txt_cutting_no*txt_entry_date*cbo_store_name*txt_in_time_hours*txt_in_time_minuties*txt_out_time_hours*txt_out_time_minuties*txt_end_date*txt_marker_cons*hidden_body_partstring*cbo_knitting_source*txt_size_set_no',"../../");
		//alert(data1);return;
	    var data2='';
		for(var i=1; i<=row_num; i++)
		{
			
			if(form_validation('cboColorType_'+i+'*cbocolor_'+i+'*txtplics_'+i,'Color Type*Color*Lot Ratio')==false)//+'*cbobatch_'+i*Batch
			{
				return;
			}
			data2+=get_submitted_data_string('updateDetails_'+i+'*cbogmtsitem_'+i+'*cbocolor_'+i+'*txtplics_'+i+'*hiddsizeid_'+i+'*txtorderqty_'+i+'*orderCutNo_'+i+'*rollData_'+i+'*cboColorType_'+i,"../../",2);
		}
	    var data=data1+data2;
		//alert(data1);return;
		freeze_window(operation);
		http.open("POST","requires/yarn_lot_ratio_planning_controller.php",true);
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
			if(trim(reponse[0])=='issue'){
				alert("Issue found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
				release_freezing();
				return;
			}
			if (reponse[0] == 15)
            {
                setTimeout('fnc_cut_lay_info(' + reponse[1] + ')', 3000);
            }
			
            if(reponse[0]==500)
			{
				alert("Delete Restricted. This information found in Yarn Issue Page Which System Id "+reponse[1]+".");
				show_msg('14');
				release_freezing();
				return;
			}

			if(reponse[0]==200)
			{
				alert("Update Restricted. This information found in Cutting Qc Page Which System Id "+reponse[1]+".");
				show_msg('14');
				release_freezing();
				return;
			}
			if(reponse[0]==13)
			{
				alert(reponse[1]);
				show_msg('11');
				release_freezing();
				return;
			}
			show_msg(trim(reponse[0]));
			if(reponse[0]==0 || reponse[0]==1)
			{
				document.getElementById('update_id').value=reponse[1];
				document.getElementById('update_tbl_id').value=reponse[3];
				document.getElementById('txt_cutting_no').value=reponse[2];
				
				$("#cbo_company_name").attr("disabled",true);
				$("#txt_job_no").attr("disabled",true);
				show_list_view( reponse[1], 'order_details_list', 'cut_details_container', 'requires/yarn_lot_ratio_planning_controller', '' ) ;
				set_button_status(1, permission, 'fnc_cut_lay_info',1,1);
			}

			if(reponse[0]==2)
			{
				document.getElementById('update_id').value="";
				document.getElementById('update_tbl_id').value="";
				document.getElementById('txt_cutting_no').value="";
			
				set_button_status(1, permission, 'fnc_cut_lay_info');
				reset_form('cutandlayentry_1','','','','clear_tr()')
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
		var page_link='requires/yarn_lot_ratio_planning_controller.php?action=cutting_number_popup&company_id='+company_id; 
		var title="Search System Number Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=420px,center=1,resize=0,scrolling=0',' ../');
		emailwindow.onclose=function()
		{
			var sysNumber = this.contentDoc.getElementById("update_mst_id"); 
			var sysNumber=sysNumber.value.split('_');
			get_php_form_data( sysNumber[0], "load_php_mst_form", "requires/yarn_lot_ratio_planning_controller" );
			show_list_view( sysNumber[0], 'order_details_list', 'cut_details_container', 'requires/yarn_lot_ratio_planning_controller', '' ) ;
			
			get_php_form_data( $("#txt_style_no").val(), "load_drop_down_bodypart", "requires/yarn_lot_ratio_planning_controller" );
			
			$("#cbo_company_name").attr("disabled",true);
			$("#txt_job_no").attr("disabled",true);
			$("#txt_job_year").attr("disabled",true);
		}
	}
	
	function openmypage_lot(row_no)
	{
		if(form_validation('cbo_company_name*txt_size_set_no*cbogmtsitem_'+row_no+'*cbocolor_'+row_no+'*cbo_store_name','Company*Size Set No*Sweater Item*Color*Store Name')==false)
		{
			return;
		}
		
		var job_no=$('#txt_job_no').val();
		var balance_qty=$('#txtlaybalanceqty_'+row_no).val();
		var ratio_qty=$('#txtmarkerqty_'+row_no).val();
		var color=$('#cbocolor_'+row_no).val();
		var garments_item=$('#cbogmtsitem_'+row_no).val();
		var store_id=$('#cbo_store_name').val();
		var rollData=$('#rollData_'+row_no).val();
		var update_id=$("#update_id").val();
		var size_set_no=$("#txt_size_set_no").val();

		if(update_id=="") update_id=0;
		
		var title = 'Lot Ratio Pop up';	
		var page_link = 'requires/yarn_lot_ratio_planning_controller.php?job_no='+job_no+'&garments_item='+garments_item+'&store_id='+store_id+'&color='+color+'&job_no='+job_no+'&rollData='+rollData+'&update_id='+update_id+'&balance_qty='+balance_qty+'&ratio_qty='+ratio_qty+'&txt_size_set_no='+size_set_no+'&action=lot_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var plies=this.contentDoc.getElementById("hide_plies").value; //Access form field with id="emailfield"
			var data=this.contentDoc.getElementById("hide_data").value; //Access form field with id="emailfield"
			
			$('#txtplics_'+row_no).val(plies);
			$('#rollData_'+row_no).val(data);
			$("#cbogmtsitem_1,#cbocolor_1,#cboColorType_1,").attr("disabled",true);
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
		//alert(data);
		if(form_validation('txt_cutting_no','Cutting Number')==false)
	   {
		return;
	   }
		window.open("requires/yarn_lot_ratio_planning_controller.php?data=" + data+'&action='+action, true );
	}
	
	function load_location()
	{
		var cbo_company_id = $('#cbo_company_name').val();
		var cbo_knitting_source = $('#cbo_knitting_source').val();
		var cbo_knitting_company = $('#cbo_working_company_name').val();
		if(cbo_knitting_source==1)
		{
			load_drop_down( 'requires/yarn_lot_ratio_planning_controller',cbo_knitting_company, 'load_drop_down_location', 'location_td' );
		}
		else
		{
			$("#cbo_location_name").val(0).attr("disabled",true);
			$("#cbo_floor").val(0).attr("disabled",true);
			//$("#cbo_location_name").val(0);
		}
	}
	
	function open_size_set_popup()
	{ 
		if( form_validation('txt_job_no*cbogmtsitem_1*cbocolor_1','Job No*Garments Item*Color')==false)
		{
			return;
		} 
		var txt_job_no=$("#txt_job_no").val();
		var cbogmtsitem=$("#cbogmtsitem_1").val();
		var cbocolor=$("#cbocolor_1").val();
		var page_link='requires/yarn_lot_ratio_planning_controller.php?action=size_set_number_popup&txt_job_no='+txt_job_no+'&cbogmtsitem='+cbogmtsitem+'&cbocolor='+cbocolor; 
		var title="Search Size Set Number Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=350px,height=250px,center=1,resize=0,scrolling=0',' ../');
		emailwindow.onclose=function()
		{
			var sysNumber = this.contentDoc.getElementById("hidden_sizeset_no").value; 
			$("#txt_size_set_no").val(sysNumber);
		}
	}
	
	function openmypage_posize()
	{
		if(form_validation('cbo_company_name*txt_job_no*cbogmtsitem_1*cbocolor_1','Company*Job No*Sweater Item*Color')==false)
		{
			return;
		}
		
		var job_no=$('#txt_job_no').val();
		var color=$('#cbocolor_1').val();
		var garments_item=$('#cbogmtsitem_1').val();
		var sizeid=$('#hiddsizeid_1').val();

		if(update_id=="") update_id=0;
		var title = 'Po Size Pop up';	
		var page_link = 'requires/yarn_lot_ratio_planning_controller.php?job_no='+job_no+'&garments_item='+garments_item+'&color='+color+'&sizeid='+sizeid+'&action=posize_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var size_id=this.contentDoc.getElementById("hidden_size_id").value; //Access form field with id="emailfield"
			var size_name=this.contentDoc.getElementById("hidden_size_name").value; //Access form field with id="emailfield"
			
			$('#hiddsizeid_1').val(size_id);
			$('#txtposize_1').val(size_name);
			
			$("#txtposize_1").attr("disabled",true);
		}
	}

</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:1300px;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="cutandlayentry_1" id="cutandlayentry_1">
    <table width="100%" cellpadding="0" cellspacing="2" align="center" style="">
     	<tr>
        	<td align="center" valign="top">  <!--   Form Left Container -->
            	<fieldset style="width:1250px;">
                     <legend>Yarn Lot Ratio</legend>
                        <table width="1250" cellspacing="2" cellpadding="0" border="0">
                            <tr>
                                <td colspan="5" align="right"><b>System Number</b></td>
                                <td colspan="5" align="left">
                                    <input type="text" name="txt_cutting_no" id="txt_cutting_no" class="text_boxes" style="width:110px" placeholder="Double Click To Search" onDblClick="open_cutting_popup()" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick()" readonly />
                                    <input type="hidden" name="update_id"  id="update_id"  />
                                </td>
                            </tr>
                          	<tr>
                                <td width="100" class="must_entry_caption">Company Name</td>              <!-- 11-00030  -->
                                <td width="150"><? echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", $selected, "change_type_color(this.value);load_drop_down( 'requires/yarn_lot_ratio_planning_controller', this.value, 'load_drop_down_store','store_td');" ); ?></td>
                                <td width="80" class="must_entry_caption">Source</td>              <!-- 11-00030  -->
                                <td width="140"><?=create_drop_down("cbo_knitting_source",130,$knitting_source,"", 1, "-- Select --", 0,"load_drop_down( 'requires/yarn_lot_ratio_planning_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_knitting_com','knitting_com');load_location();",0,'1,3'); ?></td>
                                <td width="100" class="must_entry_caption">W. Company</td>              <!-- 11-00030  -->
                                <td width="150" id="knitting_com"><?=create_drop_down( "cbo_working_company_name", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Working Company --", $selected, "load_drop_down( 'requires/yarn_lot_ratio_planning_controller', this.value, 'load_drop_down_location', 'location_td' );" );// get_php_form_data(this.value,'size_wise_repeat_cut_no','requires/yarn_lot_ratio_planning_controller.php' );  ?></td>
                                <td width="100">WC Location </td>
                                <td width="150" id="location_td"><? echo create_drop_down( "cbo_location_name", 140, $blank_array,"", 1, "-- Select Location --", $selected, "" ); ?></td>
                                <td width="80">Store Name</td>
                                <td id="store_td"><? echo create_drop_down( "cbo_store_name", 140, $blank_array,"", 1, "-- Select Store --", $selected, "" ); ?></td>
                          	</tr>
                         	<tr>
                                <td class="must_entry_caption">Floor</td>
                                <td id="floor_td"><?=create_drop_down( "cbo_floor", 140,"select id,floor_name from lib_prod_floor where production_process=1  and status_active =1 and is_deleted=0","id,floor_name", 1, "-- Select Floor --", $selected, "" ); ?></td>
                                <td class="must_entry_caption">Job No</td>
                                <td id="job_change_id"><input style="width:115px;" type="text"  onDblClick="openmypage_jobNo();" class="text_boxes" autocomplete="off" placeholder="Browse" name="txt_job_no" id="txt_job_no" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick();" readonly /></td>
                                <td>Job Year</td>
                                <td><?=create_drop_down( "txt_job_year", 140, $year,"", 1, "-- Select year --", $selected, "","1"); ?></td>
                                <td>Plan Start Date</td>
                                <td>
                                    <input style="width:130px;" type="text" class="datepicker" autocomplete="off" name="txt_entry_date" id="txt_entry_date" value="<?=date("d-m-Y"); ?>" />
                                    <input type="hidden" name="update_job_no"  id="update_job_no"  />
                                    <input type="hidden" name="update_tbl_id"  id="update_tbl_id"  />
                                    <input type="hidden" name="roll_maintained" id="roll_maintained" readonly>
                                    <input type="hidden" name="size_wise_repeat_cut_no" id="size_wise_repeat_cut_no" readonly>
                                </td>
                                <td>Start Time</td>           
                                <td>
                                    <input type="text" name="txt_in_time_hours" id="txt_in_time_hours" class="text_boxes_numeric" placeholder="HH"  style="width:55px;"  onKeyUp="fnc_move_cursor(this.value,'txt_in_time_hours','txt_in_time_minuties',2,23);" /> :
                                    <input type="text" name="txt_in_time_minuties" id="txt_in_time_minuties" class="text_boxes_numeric" placeholder="MM"  style="width:50px;" onKeyUp="fnc_move_cursor(this.value,'txt_in_time_minuties','txt_in_time_seconds',2,59)" onBlur="fnc_intime_populate(this.value,'txt_in_time_hours')" />
                                </td>
                   			</tr>
							<tr>
                                <td>Style Ref.</td>
                                <td id="buyer_id"><input type="text" name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:120px" /></td>
                                <td>Plan End Date</td>
                                <td><input style="width:115px;" type="text" class="datepicker" autocomplete="off" name="txt_end_date" id="txt_end_date" /></td>
                                <td>End Time</td>         
                                <td>
                                <input type="text" name="txt_out_time_hours" id="txt_out_time_hours" class="text_boxes_numeric" placeholder="HH"  style="width:55px;"  onKeyUp="fnc_move_cursor(this.value,'txt_out_time_hours','txt_out_time_minuties',2,23);" /> :
                                <input type="text" name="txt_out_time_minuties" id="txt_out_time_minuties" class="text_boxes_numeric" placeholder="MM"  style="width:50px;"  onKeyUp="fnc_move_cursor(this.value,'txt_out_time_minuties','txt_out_time_seconds',2,59);" onBlur="fnc_outtime_populate(this.value,'txt_out_time_hours')"/> 
                                </td>
                                <td class="must_entry_caption">Cons/Dzn(Lbs)</td>
                                <td><input style="width:130px;" type="text"  class="text_boxes_numeric" autocomplete="off" name="txt_marker_cons" id="txt_marker_cons" /></td> 
                                <td>Buyer </td>
                                <td id="buyer_id"><? echo create_drop_down( "txt_buyer_name", 140,"select id, buyer_name from lib_buyer","id,buyer_name", 1, "", $selected, "" ,1); ?></td>
                            </tr>
                          <tr>
                          	<td>Size Set No</td>
                            <td><input type="text" name="txt_size_set_no" id="txt_size_set_no" class="text_boxes" style="width:120px" onDblClick="open_size_set_popup();" readonly/></td>
                          	<td>&nbsp;</td>
                           	<td>
                                <input type="button" id="set_button" class="image_uploader" style="width:130px;" value="Body Part" onClick="open_bodypart_popup()">
                                <input type="hidden" name="hidden_body_partstring" id="hidden_body_partstring" readonly>
                           	</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                          </tr>
                      </table>
                 </fieldset>
              </td>
         </tr>
         <tr>
             <td align="center" valign="top" id="po_list_view">
               <fieldset style="width:1030px; margin-top:10px">
                    <legend>Cut and Lay details</legend>
                <table cellpadding="0" cellspacing="0" width="1030" class="rpt_table" border="1" rules="all" id="tbl_order_details">
                    <thead>
                    	<th width="120">Garments Item</th>
                        <th width="100" class="must_entry_caption">Color</th>
                        <th width="100" class="must_entry_caption" style="display:none">Order No</th>
                        <th width="100" class="must_entry_caption" id="color_type_td_id">Color Type</th>
                        <th width="70">Manual Ratio No</th>
                        <th width="70" class="must_entry_caption">Lot Ratio</th>
                        <th width="100">PO Size</th>
                        <th width="60">Size Ratio</th>
                        <th width="70" >Ratio Qty.</th>
                        <th width="70">Style Qty.</th>
                        <th width="70">Total Ratio Qty.</th>
                        <th>Ratio Balance Qty.</th>
                    </thead>
                    
                    <tbody id="cut_details_container">
                        <tr id="tr_1" style="height:10px;">
                        	<td align="center" id="garment_1"><? echo create_drop_down( "cbogmtsitem_1", 120, $blank_array,"", 1, "-Select Item-", $selected, "",""); ?></td>
                            <td align="center" id="color_1"><? echo create_drop_down( "cbocolor_1", 100, $blank_array,"", 1, "-Select Color-", $selected, ""); ?></td> 
                            <td align="center" id="orderId_1" style="display:none">
                            	<input type="text" name="cboPoNo_1" id="cboPoNo_1" class="text_boxes" style="width:90px;" placeholder="Double Click to Search" onDblClick="openmypage_po(1);" readonly />		 
                                <input type="hidden" name="poId_1"  id="poId_1"  />
                            </td>
                            <td align="center" id="colorTypeId_1"><?  echo create_drop_down( "cboColorType_1", 100, $blank_array,"", 1, "--Select--", $selected, "",1,0 ); ?></td>
                            <td align="center" id="cutNo_1"><input style="width:60px;" class="text_boxes_numeric" type="text" name="orderCutNo_1" id="orderCutNo_1" placeholder="Display" onBlur="cut_no_duplication_check(1);" readonly /></td>                             
                            <td align="center">
                                 <input type="text" name="txtplics_1"  id="txtplics_1" class="text_boxes_numeric" style="width:60px" placeholder="Double Click" onDblClick="openmypage_lot(1);" readonly />
                                 <input type="hidden" name="updateDetails_1"  id="updateDetails_1"  />
                                 <input type="hidden" name="rollData_1" id="rollData_1" class="text_boxes" readonly />
                            </td>
                            <td align="center"><input type="text" name="txtposize_1" id="txtposize_1" class="text_boxes" onDblClick="openmypage_posize();"  placeholder="Browse" style="width:90px" readonly /><input type="hidden" name="hiddsizeid_1" id="hiddsizeid_1" class="text_boxes" readonly /></td>
                            <td align="center"><input type="text" name="txtsizeratio_1"  id="txtsizeratio_1" class="text_boxes_numeric"  onDblClick="openmypage_sizeNo(this.id);"  placeholder="Browse" style="width:50px" /></td>
                            <td align="center" id="marker_1"><input type="text" name="txtmarkerqty_1"  id="txtmarkerqty_1" class="text_boxes_numeric"  placeholder="Display" style="width:60px" readonly/></td>
                            <td align="center" id="order_1"><input type="text" name="txtorderqty_1"  id="txtorderqty_1" class="text_boxes_numeric"  placeholder="Display" style="width:60px"  readonly/></td>
                            <td align="center"><input type="text" name="txttotallay_1"  id="txttotallay_1" class="text_boxes_numeric"  placeholder="Display" style="width:60px"  readonly/> </td>
                            <td align="center"><input type="text" name="txtlaybalanceqty_1" id="txtlaybalanceqty_1" class="text_boxes_numeric" placeholder="Display" style="width:60px" readonly/></td>
                        </tr>
                      </tbody>
                   </table>
                </fieldset> 
              </td>
         </tr>
        <tr>
           <td colspan="4" align="center" class="">
                <? $date=date('d-m-Y'); echo load_submit_buttons( $permission, "fnc_cut_lay_info", 0,0,"reset_form('cutandlayentry_1','','','txt_entry_date,".$date."','clear_tr();')",1); ?>
                  <input type="button" id="btn_cost_print" name="btn_cost_print"   style="width:100px;"  class="formbutton" value="Lay Chart"  onClick="generate_report_lay_chart($('#txt_cutting_no').val()+'*'+$('#txt_job_no').val()+'*'+$('#cbo_working_company_name').val()+'*'+$('#cbo_location_name').val(),'cut_lay_entry_report_print');"/>
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