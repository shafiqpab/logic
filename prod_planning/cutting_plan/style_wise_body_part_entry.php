<?
/*-------------------------------------------- Comments
Purpose			: 
Functionality	:	
JS Functions	:
Created by		:	Md. Rakib Hasan Mondal 
Creation date 	: 	02-04-2023
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
   echo load_html_head_contents("Style Wise Body Part Entry","../../", 1, 1, $unicode,'','');
   ?>
 <script>
    var permission='<? echo $permission; ?>';
    var txt_job_id=$("#txt_job_no").val(); 
    function browseJobStyle(popupFor)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}

		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#txt_job_year").val(); 
		  
        let title = (popupFor == 1) ? 'Job No Search' : 'Style Search' ; 
		var page_link='requires/style_wise_body_part_entry_controller.php?action=job_style_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id+'&popupFor='+popupFor;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			var popupFor=this.contentDoc.getElementById("hide_popup_for").value;
            if(popupFor == 1)
            {
                $('#txt_job_no').val(job_no);
			    $('#txt_job_id').val(job_id);
            }
            else
            {
                $('#txt_style_no').val(job_no);
            }
			
		}
	} 
    function browseBodyPart(row_no)
    {    
        let rows = $('#hidden_body_part_row_'+row_no).val();
        // +'&selected_id='+body_part_id
        
        let title = 'Body Part List' ; 
        let cbo_year_id = $("#txt_job_year").val(); 
        let company_id = $("#cbo_company_name").val();  
        
        var page_link='requires/style_wise_body_part_entry_controller.php?action=style_wise_bodypart_popup&row_no='+row_no+'&cbo_year_id='+cbo_year_id+'&company_id='+company_id+'&selected_rows='+rows;
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=400px,center=1,resize=1,scrolling=0','../../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];  
            var bodyPartId=this.contentDoc.getElementById("hidden_body_part_id").value;
            var body_part=this.contentDoc.getElementById("hidden_body_part").value; 
            var selected_rows = this.contentDoc.getElementById("hidden_body_part_row").value; 
                
            $('#txt_body_part_'+row_no).val(body_part);  
            $('#hidden_body_part_'+row_no).val(bodyPartId);
            $('#hidden_body_part_row_'+row_no).val(selected_rows);
        }
    }
    function getCompanyId() 
	{	 
	    var company_id = document.getElementById('cbo_work_company_name').value;

	    if(company_id !='') {
	      var data="action=load_drop_down_location&choosenCompany="+company_id;
	      http.open("POST","requires/order_booking_vs_production_and_shipment_controller.php",true);
	      http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	      http.send(data); 
	      http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#location_td').html(response);
	              set_multiselect('cbo_location_name','0','0','','0');
				  setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location_name,'0');getLocationId();") ,3000)];
	          }			 
	      };
	    }         
	}
    function fn_report_generated(id) 
    {   
        let style = $('#txt_style_no').val();
        let job = $('#txt_job_no').val();
        let int_ref = $('#txt_inter_ref').val();
        if (style || job || int_ref) {
            if(form_validation('cbo_company_name', 'Company')==false) {
                return;
            }
        }else{
            if(form_validation('cbo_company_name*txt_job_no', 'Company*Job or Style or Inter. Ref.')==false) {
                return;
            }
        }
        
        freeze_window(operation); 
        var data="action=order_list"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_year*txt_job_no*txt_style_no*txt_inter_ref', "../../");
        http.open("POST", "requires/style_wise_body_part_entry_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_generate_order_list_main_reponse; 
    }  
    function fnc_generate_order_list_main_reponse() 
    {
        if(http.readyState == 4) 
        {
            show_msg('3'); 
            var reponse=trim(http.responseText).split("####");  
            $('#order_list_container').html(reponse[0]);  
            release_freezing();
        }
        
    } 
    function fnc_body_part_entry(operation)
    {
        if(form_validation('cbo_company_name', 'Company')==false) {
            return;
        }
        var total_row=$('#report_table_input tbody tr').length;
		let dataString=""; let j=0;
		for (let i=1; i<=total_row; i++)
		{
			let body_part_ids = $('#hidden_body_part_'+i).val();
			let body_part_row = $('#hidden_body_part_row_'+i).val();
			let company       = $('#hidden_company_name_'+i).val();
			let location      = $('#hidden_location_name_'+i).val();
			let buyer         = $('#hidden_buyer_name_'+i).val();
			let job_year      = $('#hidden_job_year_'+i).val();
			let job_no        = $('#hidden_job_no_'+i).val();
			let job_id        = $('#hidden_job_id_'+i).val();
			let style_ref_no  = $('#hidden_style_ref_no_'+i).val();
			let item          = $('#hidden_item_'+i).val();
			let order_quantity= $('#hidden_order_quantity_'+i).val();
			let uom           = $('#hidden_uom_'+i).val();
			let mstID         = $('#mstId_'+i).val();
			let enableDelete  = $('#enable_delete_'+i).val(); 
			if(body_part_ids!="" )
			{
				j++;
				dataString+='&body_part_ids_' + j + '=' + body_part_ids + '&body_part_row_' + j + '=' + body_part_row + '&company_name_' + j + '=' + company + '&location_name_' + j + '=' + location + '&buyer_name_' + j + '=' + buyer +'&job_year_' + j + '=' + job_year+'&job_no_' + j + '=' + job_no+'&job_id_' + j + '=' + job_id+'&style_ref_no_' + j + '=' + style_ref_no+'&item_' + j + '=' + item+'&order_quantity_' + j + '=' + order_quantity+'&uom_' + j + '=' + uom+'&mstId_' + j + '=' + mstID+'&enable_delete_' + j + '=' + enableDelete;
			}
		};
		if(j<1)
		{
			alert('Body part data needed.');
			return;
		}
        // freeze_window(operation); 
        let data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_inter_ref', "../../")+dataString+'&row_num='+j;
        http.open("POST", "requires/style_wise_body_part_entry_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_body_part_entry_reponse; 
    }
    function fnc_body_part_entry_reponse()
    {
        if(http.readyState == 4) 
		{
			//alert (http.responseText);
			var response=trim(http.responseText).split('**');
            if (response[0]==80) 
            {
                alert('Please select at least one row');
            }
            else
            { 
                show_msg(response[0]);
            }
			
			if(response[0]==0 || response[0]==1|| response[0]==2)
			{ 
				fn_report_generated(1);  
			} 
		} 
    }
    function set_deleted_column(row_id){
        let row = $('#enable_delete_'+row_id);
        if(row.val() == 1){
            row.val(0);
        }else{
            row.val(1);
        }
    }
</script>
 <?
  $company_arr = return_library_array("select id, company_name from lib_company order by company_name","id","company_name");
  $buyer_arr = return_library_array("select id, buyer_name from lib_buyer order by buyer_name","id","buyer_name");
 ?>
</head>
 
<body onLoad="set_hotkey()"> 
    <div style="width:1200px;" align="center"> 
        <? echo load_freeze_divs ("../../",$permission);  ?>
        <form name="body_part" id="body_part">  
            <fieldset style="width:850px;">
                <legend>Search Panel</legend>
                <table class="rpt_table" width="850px" cellpadding="0" cellspacing="0" border="1" align="center">
                    <thead>                    
                        <tr>
                            <th class="must_entry_caption">Company</th>
                            <th id="search_text_td" >Buyer </th>
                            <th>Job Year</th>
                            <th>Job</th>
                            <th>Style</th>
                            <th>Inter. Ref.</th>  
                            <th></th>
                        </tr>  
                    </thead>
                    <tbody>
                    <tr >
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 140, $company_arr,"id,company_name", 1, "-- Select Company --", $selected, " ","");
                            ?>
                        </td> 
                        <td> 
                            <?
                                echo create_drop_down( "cbo_buyer_name", 140, $buyer_arr,"id,buyer_name", 1, "-- Select Buyer --", $selected, " ","");
                            ?>
                        </td>   
                        <td>
                            <?
                                echo create_drop_down( "txt_job_year", 140, $year,"", 1, "-- Select year --", $selected, "","");
                            ?>
                        </td> 
                        <td>
                            <input style="width:140px;" type="text"  onDblClick="browseJobStyle(1)" class="text_boxes" autocomplete="off" placeholder="Browse/Write" name="txt_job_no" id="txt_job_no" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick()" />
                        </td>                  
                        <td>
                            <input style="width:140px;" type="text"  onDblClick="browseJobStyle(2)" class="text_boxes" autocomplete="off" placeholder="Browse/Write" name="txt_style_no" id="txt_style_no" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick()" />
                        </td>  
                        <td>
                            <input name="txt_inter_ref" id="txt_inter_ref" type="text" class="text_boxes" style="width:140px" >
                        </td> 
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>   
        </form>
        <div style="margin-top: 30px;" id="order_list_container"></div>
    </div>
</body>
           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>