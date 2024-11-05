<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Work Progress Report.
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	04-06-2014
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Knitting Bill Report", "../../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	var permission = '<? echo $permission; ?>';
			
	function fn_report_generated(type)
	{
		if (form_validation('cbo_company_id*cbo_process_id*txt_date_from*txt_date_to*cbo_body_part_search','Comapny Name*Process*date_from*date_to*body_part_search')==false)//*txt_date_from*txt_date_to----*From Date*To Date
		{
			return;
		}
		else
		{
			if(type==1)
			{
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_name*cbo_buyer_id*cbo_process_id*cbo_search_by*txt_bill_no*txt_date_from*txt_date_to*cbo_body_part_search*cbo_bill_section',"../../../")+'&type='+type;
			}
			if(type==2)
			{
				//alert("Its Me");
			var data="action=report_generate_summary"+get_submitted_data_string('cbo_company_id*cbo_location_name*cbo_buyer_id*cbo_process_id*cbo_search_by*txt_bill_no*txt_date_from*txt_date_to*cbo_body_part_search*cbo_bill_section',"../../../")+'&type='+type;
			}
			if(type==3)
			{
				//alert("Its Me");
			var data="action=report_generate_summary_bill"+get_submitted_data_string('cbo_company_id*cbo_location_name*cbo_buyer_id*cbo_process_id*cbo_search_by*txt_bill_no*txt_date_from*txt_date_to*cbo_body_part_search*cbo_bill_section',"../../../")+'&type='+type;
			}
			freeze_window(3);
			http.open("POST","requires/knitting_bill_report_controller.php",true);
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
			//alert(reponse[0]);return;
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			//append_report_checkbox('table_header_1',1);
			setFilterGrid("table_body",-1);
			show_msg('3');
			release_freezing();
		}
	}
	
	function show_progress_report_details(action,order_id,width)
	{ 
		 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/knitting_bill_report_controller.php?action='+action+'&order_id='+order_id, 'Work Progress Report Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../');
		
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
		var page_link = 'requires/knitting_bill_report_controller.php?&action=image_view_popup&id='+id;
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
		}
	}
	

</script>
</head>
<body onLoad="set_hotkey();">
<form id="workProgressReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../../",'');  ?>
         <h3 style="width:1490px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1490px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="135" class="must_entry_caption">Company</th>
					<th width="135">Location</th>
                    <th width="125">Party </th>
                    <th width="110"  class="must_entry_caption">Process</th>
                    <th width="100">Bill Type</th>
					<th width="100">Bill Section</th>
					<th width="110">Body Part Type</th>
					<th width="80">Bill No</th>
                    <th width="250">Bill Date</th>
                    <th> <input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /> </th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td  align="center"> 
                            <?
								echo create_drop_down( "cbo_company_id",135,"select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/knitting_bill_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td'); load_drop_down( 'requires/knitting_bill_report_controller', this.value, 'load_drop_down_location', 'location_td');"); 
                            ?>
                        </td>
						<td id="location_td"><? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "--Select Location--", $selected,"","","","","","",3); ?></td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_id", 145, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                            ?>
                        </td>
                        <td>
                            <? 
                                //echo create_drop_down( "cbo_process_id", 110, $production_process,"", 1, "-Select Process-", $selected, "","","" );
								echo create_drop_down( "cbo_process_id", 110, $production_process,"", 1, "-Select Process-", 2, "","","" );
                            ?>
                        </td>
                        <td>
                            <? 
                              $bill_type = array(0=>"--All--",1 => "Order", 2 => "Sample with order", 3 => "Sample without order", 4 => "FSO For Service");
                                echo create_drop_down( "cbo_search_by", 100, $bill_type,"",0, "", " ",'',0 );//$bill_for
                             ?>
                        </td>
						<td>
                            <? 
                                $bill_section_arr = array(0=>"--All--",1=>"Knitting",2=>"Drawstring",3=>"Collar and Cuff",4=>"Twill Tape");
                                echo create_drop_down( "cbo_bill_section", 100, $bill_section_arr,"",0, "", " ",'',0 );
                             ?>
                        </td>
						 <td>
                            <? 
                                //$cbo_body_part_search_array = array(0=>"---All---",1=>"Without Collar N Cuff",2=>"Collar N Cuff",3=>"Drawstring",4=>"Others");
								$cbo_body_part_search_array = array(0=>"---All---",1=>"Without Collar N Cuff",2=>"Collar N Cuff",3=>"Drawstring");
                                echo create_drop_down( "cbo_body_part_search", 110, $cbo_body_part_search_array,"",0, "", "1",'',0 );
                             ?>
                        </td>
                       <td>
					   
                            <input type="text" name="txt_bill_no" id="txt_bill_no" class="text_boxes"  style="width:100px" placeholder="Write Bill ID" >                        
						</td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:75x" placeholder="From Date" >&nbsp; To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:75px"  placeholder="To Date"  >
                        </td>
						
                        <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:90px" value="Bill" onClick="fn_report_generated(1)" />
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="Date Summary" onClick="fn_report_generated(2)" />
							<input type="button" id="show_button" class="formbutton" style="width:80px" value="Bill Summary" onClick="fn_report_generated(3)" />
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
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
 </form>    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
