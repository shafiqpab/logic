<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Work Progress Report.
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	04-06-2014
Updated by 		: 	Shafiq	
Update date		: 	26-05-2019	   
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
echo load_html_head_contents("Work Progress Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
			
	function fn_report_generated(type)
	{
		if (form_validation('cbo_company_id','Comapny Name')==false)//*txt_date_from*txt_date_to----*From Date*To Date
		{
			return;
		}
		
		if(($('#txt_job_no').val()=="") &&  ($('#txt_style_ref').val()=="") && ($('#txt_order_no').val()=="") && (($('#txt_date_from').val()=="") || ($('#txt_date_to').val()=="") ) && (($('#txt_date_from_rec').val()=="") || ($('#txt_date_to_rec').val()=="")) )
		{
			alert("Please Input At Least One Field Job No/Style Ref./Order No/Delivery Date/Receive Date");
			return;
		}
		else
		{
			/*if(type==2)
			{
				if ($('#cbo_process_id').val()!=0)
				{
					if($('#cbo_process_id').val()!=4 || $('#cbo_process_id').val()!=3)
					{
						alert ("Bill on Batch Use for Dyeing & Fabric Finishing.");
						return;
					}
				}
			}*/




			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_process_id*cbo_search_by*cbo_year*txt_job_no*txt_job_id*txt_style_ref*txt_order_no*txt_date_from*txt_date_to*txt_date_from_rec*txt_date_to_rec',"../../")+'&type='+type;
			freeze_window(3);
			http.open("POST","requires/work_progress_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**"); 
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			append_report_checkbox('table_header_1',1);
			//setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}
	
	function show_progress_report_details(action,order_id,width,color_id)
	{ 
		 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/work_progress_report_controller.php?action='+action+'&order_id='+order_id+'&color_id='+color_id, 'Work Progress Report Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../');
		
	} 

	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}

	function openImageWindow(id)
	{
		var title = 'Image View';	
		var page_link = 'requires/work_progress_report_controller.php?&action=image_view_popup&id='+id;
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
		}
	}
	
	function openmypage_job()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_id').value;
		var year=document.getElementById('cbo_year').value;
		var cbo_process_id=document.getElementById('cbo_process_id').value;
		var page_link="requires/work_progress_report_controller.php?action=job_no_popup&company_id="+company_name+"&cbo_buyer_name="+cbo_buyer_name+"&year="+year+"&cbo_process_id="+cbo_process_id;
		var title="Job Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			var theemail_job=this.contentDoc.getElementById("selected_job_no").value;
			//var job=theemail.split("_");
			document.getElementById('txt_job_no').value=theemail_job;
			document.getElementById('txt_job_id').value=theemail;
			release_freezing();
		}
	}	
	
	function openmypage_style()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_id').value;
		var year=document.getElementById('cbo_year').value;
		var cbo_process_id=document.getElementById('cbo_process_id').value;
		var page_link="requires/work_progress_report_controller.php?action=style_no_popup&company_id="+company_name+"&cbo_buyer_name="+cbo_buyer_name+"&year="+year+"&cbo_process_id="+cbo_process_id;
		var title="Style Ref.";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			//var job=theemail.split("_");
			document.getElementById('txt_style_ref').value=theemail;
			release_freezing();
		}
	}

	function openmypage_order()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_id').value;
		var year=document.getElementById('cbo_year').value;
		var cbo_process_id=document.getElementById('cbo_process_id').value;
		var job_no=document.getElementById('txt_job_no').value;
		var page_link="requires/work_progress_report_controller.php?action=order_no_popup&company_id="+company_name+"&cbo_buyer_name="+cbo_buyer_name+"&year="+year+"&cbo_process_id="+cbo_process_id+"&job_no="+job_no;
		var title="Order Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			var job=theemail.split("_");
			document.getElementById('txt_order_id').value=job[0];
			document.getElementById('txt_order_no').value=job[1];
			release_freezing();
		}
	}
	function fabric_finishing(company_id,id,bill_no,main_process_id)
		{
		
			var company_name=company_id;
			var update_id=id;
			var txt_bill_no=bill_no;
			var report_title=$( "div.form_caption" ).html();
			var data=company_name+'*'+update_id+'*'+txt_bill_no+'*'+report_title;
			//alert(data);
			if(main_process_id==2) //Kntting
			{
				
				generate_report_file(company_name+'*'+update_id+'*'+txt_bill_no+'*'+report_title,'knitting_bill_print', '../requires/knitting_bill_issue_controller.php',main_process_id);
			}
			else if(main_process_id==3) //Dyeing
			{
				var show_val_column='';
				var r=confirm("Press \"OK\" to open with Fabric Details.\nPress \"Cancel\" to open without Fabric Details.");
				if (r==true) show_val_column="1"; else show_val_column="0";
			
				var type=1;
				generate_report_file(company_name+'*'+update_id+'*'+txt_bill_no+'*'+report_title+'*'+type+'*'+show_val_column,'fabric_finishing_print', '../requires/sub_fabric_finishing_bill_issue_controller.php',main_process_id);
			}
			else if(main_process_id==1) //cutting
			{
				
				generate_report_file(company_name+'*'+update_id+'*'+txt_bill_no+'*'+report_title,'cutting_bill_print', '../requires/subcon_cutting_bill_issue_controller.php',main_process_id);
			}
			else if(main_process_id==5) //sewing
			{
				
				generate_report_file(company_name+'*'+update_id+'*'+txt_bill_no+'*'+report_title,'sewing_bill_print', '../requires/sewing_bill_issue_controller.php',main_process_id);
			}
			else if(main_process_id==10) //iron
			{
				
				generate_report_file(company_name+'*'+update_id+'*'+txt_bill_no+'*'+report_title,'iron_bill_print', '../requires/subcon_iron_bill_issue_controller.php',main_process_id);
			}
			else if(main_process_id==11) //Gmts_Finishing
			{
				
				generate_report_file(company_name+'*'+update_id+'*'+txt_bill_no+'*'+report_title,'packing_bill_print', '../requires/subcon_packing_bill_issue_controller.php',main_process_id);
			}
			else if(main_process_id==4) //Dyeing
			{
				var show_val_column='';
				var r=confirm("Press \"OK\" to open with Fabric Details.\nPress \"Cancel\" to open without Fabric Details.");
				if (r==true) show_val_column="1"; else show_val_column="0";
			
				var type=1;
				generate_report_file(company_name+'*'+update_id+'*'+txt_bill_no+'*'+report_title+'*'+type+'*'+show_val_column,'fabric_finishing_print', '../requires/sub_fabric_finishing_bill_issue_controller.php',main_process_id);
			}
		}


	function generate_report_file(data,action,path,type)
	{
			if(type==2) //Kntting
			{
			window.open("../requires/knitting_bill_issue_controller.php?data=" +data+ '&action='+action, true );
			}
			else if(type==3) //Dyeing
			{
			window.open("../requires/sub_fabric_finishing_bill_issue_controller.php?data=" +data+ '&action='+action, true );
			}
			else if(type==1) //cutting
			{
			window.open("../requires/subcon_cutting_bill_issue_controller.php?data=" +data+ '&action='+action, true );
			}
			else if(type==5) //sewing
			{
			window.open("../requires/sewing_bill_issue_controller.php?data=" +data+ '&action='+action, true );
			}
			else if(type==10) //iron
			{
			window.open("../requires/subcon_iron_bill_issue_controller.php?data=" +data+ '&action='+action, true );
			}
			else if(type==11) //Gmts_Finishing
			{
			window.open("../requires/subcon_packing_bill_issue_controller.php?data=" +data+ '&action='+action, true );
			}
			else if(type==4) //finishing
			{
			window.open("../requires/sub_fabric_finishing_bill_issue_controller.php?data=" +data+ '&action='+action, true );
			}
	}

</script>
</head>
<body onLoad="set_hotkey();">
<form id="workProgressReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1200px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1200px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="135" class="must_entry_caption">Company</th>
                    <th width="125">Party </th>
                    <th width="80">Process</th>
                    <th width="80" style="display:none">Type</th>
                    <th width="60">Year</th>                     
                    <th width="60">Job No</th>
                    <th width="80">Style Ref.</th>
                    <th width="80">Order No</th>
                    <th width="200">Delivery Date</th>
                     <th width="200">Receive Date</th>
                    <th width="250">
                    <input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" />
                    </th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td  align="center"> 
                            <?
                                echo create_drop_down( "cbo_company_id", 135, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/work_progress_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_id", 125, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                            ?>
                        </td>
                        <td>
                            <? 
                                echo create_drop_down( "cbo_process_id", 80, $production_process,"", 1, "-Select Process-", $selected, "","","" );
                            ?>
                        </td>
                        <td  style="display:none">
                            <? 
                                $search_by_arr = array(1=>"Order Wise",2=>"Style Wise");
                                echo create_drop_down( "cbo_search_by", 80, $search_by_arr,"",0, "", "",'',0 );//search_by(this.value)
                             ?>
                        </td>
                        <td>
                            <?
                                $selected_year=date("Y");
                                echo create_drop_down( "cbo_year", 60, $year,"", 1, "--Select Year--", $selected_year, "",0 );
                            ?>
                        </td>
                        <td>
                            <input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:55px" placeholder="Wr/Br Job" onDblClick="openmypage_job();" >
                            <input  type="hidden"  name="txt_job_id" id="txt_job_id" class="text_boxes" style="width:50px" >
                        </td>
                        <td>
                            <input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:75px" placeholder="Wr/Br Style" onDblClick="openmypage_style();" >
                        </td>
                        <td>
                            <input name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:75px"  placeholder="Wr/Br Order" onDblClick="openmypage_order();" >
                            <input type="hidden" name="txt_order_id" id="txt_order_id" class="text_boxes" style="width:70px">
                        </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date" >&nbsp; To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date"  >
                        </td>
                        <td>
                            <input name="txt_date_from_rec" id="txt_date_from_rec" class="datepicker" style="width:50px" placeholder="From Date" >&nbsp; To
                            <input name="txt_date_to_rec" id="txt_date_to_rec" class="datepicker" style="width:50px"  placeholder="To Date"  >
                        </td>
                        <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:90px" value="Bill on Delivery" onClick="fn_report_generated(1)" />
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="Bill on Batch" onClick="fn_report_generated(2)" />
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="Summary" onClick="fn_report_generated(3)" />
							<input type="button" id="show_button" class="formbutton" style="width:100px" value="Bill on Delivery 2" onClick="fn_report_generated(4)" />
                            <input type="button" id="show_button" class="formbutton" style="width:100px" value="DyeFinWip" onClick="fn_report_generated(5)" />
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="10" align="center">
							<? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </tfoot>
           </table> 
           <br />
        </fieldset>
    </div>
    </div>
    <div id="report_container" align="center" style="padding: 10px;"></div>
    <div id="report_container2" align="left"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
