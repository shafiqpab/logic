<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Party Wise Batch Balance Report.
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	12-11-2014
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
echo load_html_head_contents("Party Wise Batch Balance Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
			
	function fn_report_generated()
	{
		$("#cbofabricfrom").attr("disabled",false);
		
		if (form_validation('cbo_company_id*txt_date_from','Comapny Name*Date')==false)
		{
			return;
		}
		else
		{
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*txt_party_id*cbo_value_with*cbo_bill_type*txt_date_from*cbofabricfrom',"../../")+'&report_title='+report_title;
			freeze_window(3);
			http.open("POST","requires/subcon_batch_balance_report_controller.php",true);
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
			$("#cbofabricfrom").attr("disabled",true);
			//append_report_checkbox('table_header_1',1);
			//setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
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

	function openmypage_party()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var page_link='requires/subcon_batch_balance_report_controller.php?action=party_popup&companyID='+companyID;
		var title='Party Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=420px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var party_name=this.contentDoc.getElementById("hide_party_name").value;
			var party_id=this.contentDoc.getElementById("hide_party_id").value;
			
			$('#txt_party_name').val(party_name);
			$('#txt_party_id').val(party_id);	 
		}
	}
	
	function openmypage_batch_dtls(id,action,type)
	{
		//alert (production_date);
		var width_pop=570;
		var page_title='Batch Popup';
		var cbo_company_id = $("#cbo_company_id").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/subcon_batch_balance_report_controller.php?action='+action+'&cbo_company_id='+cbo_company_id+'&id='+id+'&type='+type, page_title, 'width='+width_pop+'px,height=400px,center=1,resize=0,scrolling=0','../');
	}	
</script>
</head>
<body onLoad="set_hotkey();">
<form id="batchBalanceReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:800px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:800px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="150" class="must_entry_caption">Company</th>
                    <th width="120">Bill Type</th>
                    <th width="160">Party </th>
                    <th width="80">Type </th>
                    <th width="115">Value </th>
                    <th width="80" class="must_entry_caption">Date As On</th>
                    <th width=""><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                </thead>
                <tbody>
                    <tr class="general" >
                        <td  align="center"> 
                            <?
                                echo create_drop_down( "cbo_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/subcon_batch_balance_report_controller',this.value,'load_fabric_source_from_variable_settings', 'dyenamic_fabricfrom');" );//load_drop_down( 'requires/subcon_party_statement_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );
                            ?>
                        </td>
                        <td>
                            <? 
                                echo create_drop_down( "cbo_bill_type", 120, $production_process,"", 1, "-Select Type-", $selected, "","","" );
                            ?>
                        </td>
                        <td>
                           <input type="text" id="txt_party_name" name="txt_party_name" class="text_boxes" style="width:150px" onDblClick="openmypage_party();" placeholder="Browse Party" />
                           <input type="hidden" id="txt_party_id" name="txt_party_id" class="text_boxes" style="width:70px" />
                        </td>
                        <td id="dyenamic_fabricfrom">
                                <?  
                                /*$fabricfrom=array(1=>"Receive",2=>"Production",3=>"Issue"); 
                                echo create_drop_down( "cbofabricfrom_1", 70, $fabricfrom, "", 1, "--Select --", 0, "", 0,"","","","","","","","fabric_source"); */   

                               // echo create_drop_down( "cbofabricfrom_1", 70, $blank_array, "", 1, "--Select --", 0, "", 1,"","","","","","","","fabric_source");
								echo create_drop_down("cbofabricfrom", 80, $blank_array, "", 1, "--Select --", 0, "", 1, "", "", "", "", "", "", "cbofabricfrom[]");                            
                                ?>
                            </td>
                        <td>
							<?   
								$valueWithArr=array(1=>'Value With 0',2=>'Value Without 0');
								echo create_drop_down( "cbo_value_with", 115, $valueWithArr, "", 0, "--  --", 2, "", "", "");
                            ?>
                        </td> 
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" >
                        </td>
                        <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated();" />
                        </td>
                    </tr>
                </tbody>
           </table> 
           <br />
        </fieldset>
    </div>
    </div>
        
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
