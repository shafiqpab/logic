<?
/*-------------------------------------------- Comments
Purpose			: 
Functionality	:	
JS Functions	:
Created by		:	Md. Rakib Hasan Mondal 
Creation date 	: 	14-09-2023
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
   echo load_html_head_contents("Style Wise Body Part Entry","../", 1, 1, $unicode,'','');
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
		  
        let title = (popupFor == 1) ? 'Job No Search' : 'Style Search' ; 
		var page_link='requires/independent_finish_garments_entry_controller.php?action=job_style_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&popupFor='+popupFor;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			var popupFor=this.contentDoc.getElementById("hide_popup_for").value;
            $('#txt_job_id').val(job_id);
            if(popupFor == 1)
            {
                $('#txt_job_no').val(job_no);
            }
            else
            { 
                $('#txt_style_no').val(job_no);
            }
			
		}
	} 
    
    
    function fn_report_generated(id) 
    {   
        let style = $('#txt_style_no').val();
        let job = $('#txt_job_no').val();
        if (style || job ) {
            if(form_validation('cbo_company_name', 'Company')==false) {
                return;
            }
        }else{
            if(form_validation('cbo_company_name*txt_date_from*txt_date_to', 'Company*Pub. Ship Date*Pub. Ship Date')==false) {
                return;
            }
        }
        
        freeze_window(operation); 
        var data="action=order_list"+get_submitted_data_string('cbo_company_name*cbo_location*cbo_buyer_name*txt_job_no*txt_style_no*txt_date_from*txt_date_to', "../");
        http.open("POST", "requires/independent_finish_garments_entry_controller.php", true);
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
            setFilterGrid("table_body",-1);
            release_freezing();
        }
        
    } 
    function calculateTotal(inputElement) {
        var $row = $(inputElement).closest("tr");
        var total = 0;

        balance = $row.find(".balance_qty").text();
        this_rej_qty = inputElement.value;
         

         $row.find(".reject_qty").each(function() {
            var value = parseFloat($(this).val()) || 0; // Parse as float, default to 0 if not a valid number
            total += value;
        });

        if (balance < total) 
        {
            alert("Total Qty can't be more than Balance")
            inputElement.value ="";
            $row.find(".total_reject").val( total- this_rej_qty );
        }
        else
        {
            $row.find(".total_reject").val(total);
        }

    }
    function fnc_reject_data_entry(operation)
    {
        if(form_validation('cbo_company_name', 'Company')==false) {
            return;
        }
        var total_row=$('#table_body tbody tr').length;
        var formattedData = ''; 
        var error_rows = ''; 
        var error_flag = false; 
        var total_checked = 0
        
        // Loop through each table row
        $("#table_body tbody tr").each(function(index) {
            var po_id = $(this).find("input.checkSingle").val();
            var is_checked = $(this).find("input.checkSingle").is(":checked");
            var hidden_mst_id = $(this).find("input.hidden_mst_id").val();
            var total_reject = $(this).find("input.total_reject").val()*1; 

            if (is_checked && !total_reject) //(!is_checked && total_reject) || (is_checked && !total_reject)
            {
                var row_no = index; 
                
                if (error_rows !="") {
                    error_rows  +=","+row_no; 
                }else{
                    error_rows=row_no; 
                }
                error_flag = true; 
            }
            
            var i=j=0;
            if (is_checked && total_reject)
            {
                total_checked++;
                $(this).find("input.reject_qty").each(function() 
                {
                    if (i==0 && total_reject>0) 
                    { 
                        if (index > 0) {
                            formattedData += '##';
                        }
    
                        formattedData += po_id+"_"+ hidden_mst_id +"_"+ total_reject +'@@';
                    }
                    i++;
                    var id = $(this).attr("id");
                    var id_split = id.split("_");
                    var reason = id_split[2];
                    var reject_qty = $(this).val();
                    if(reject_qty*1 ){
                        if (j>0) {
                            formattedData+="@";
                        }
                        formattedData += reason + '_' + reject_qty;
                        j++;
                    }
                });

            }
            
        });
        
        // console.log(formattedData);
        if (error_flag) 
        {
            alert('Please check Row no:'+error_rows);
            return;
        }
        if (total_checked==0) 
        {
            alert("Please check at least one ");
            return;
        }
		freeze_window(operation); 
        let data="action=save_update_delete&operation="+operation+"&data="+formattedData+get_submitted_data_string('cbo_company_name*cbo_location*cbo_buyer_name*txt_job_id',"../");
        http.open("POST", "requires/independent_finish_garments_entry_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_reject_data_entry_reponse; 
    }
    function fnc_reject_data_entry_reponse()
    {
        if(http.readyState == 4) 
		{
			//alert (http.responseText);
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			
			/* if(response[0]==0 || response[0]==1)
			{  */
                data = $("#cbo_company_name").val()+'**'+$('#cbo_location').val()+'**'+$('#cbo_buyer_name').val()+'**'+$('#txt_job_no').val()+'**'+$('#txt_style_no').val()+'**'+$('#txt_date_from').val()+'**'+$('#txt_date_to').val();

                show_list_view(data,'populate_data_from_search_popup','order_list_container','requires/independent_finish_garments_entry_controller','');
			// } 
            setFilterGrid("table_body",-1);
            release_freezing();
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
  $company_arr = return_library_array("select id, company_name from lib_company  WHERE status_active=1 and is_deleted=0 order by company_name","id","company_name"); 
 ?>
</head>
 
<body onLoad="set_hotkey()"> 
    <div style="width:1200px;" align="center"> 
        <? echo load_freeze_divs ("../",$permission);  ?>
        <form name="body_part" id="body_part">  
            <fieldset style="width:1000px;">
                <legend>Search Panel</legend>
                <table class="rpt_table" width="1000px" cellpadding="0" cellspacing="0" border="1" align="center">
                    <thead>                    
                        <tr>
                            <th class="must_entry_caption">Company</th>
                            <th>Location</th>
                            <th id="search_text_td" >Buyer </th>
                            <th>Job</th>
                            <th>Style</th>
                            <th class="must_entry_caption">Pub. Ship Date</th> 
                            <th></th>
                        </tr>  
                    </thead>
                    <tbody>
                    <tr >
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 140, $company_arr,"id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/independent_finish_garments_entry_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/independent_finish_garments_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );","");
                            ?>
                        </td> 
                        <td id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location", 150, $blank_array,"", 1, "-- Select Location --", $selected, "", 1, "" );
                            ?>
                        </td>
                        <td id="buyer_td"> 
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "", 1, "" );
                            ?>
                        </td>   
                        <td>
                            <input style="width:140px;" type="text"  onDblClick="browseJobStyle(1)" class="text_boxes" autocomplete="off" placeholder="Browse/Write" name="txt_job_no" id="txt_job_no" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick()" />
                            <input  type="hidden"  name="txt_job_id" id="txt_job_id"  />

                        </td>                  
                        <td>
                            <input style="width:140px;" type="text"  onDblClick="browseJobStyle(2)" class="text_boxes" autocomplete="off" placeholder="Browse/Write" name="txt_style_no" id="txt_style_no" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick()" />
                        </td>  
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:55px"/>                                             
                            To
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:55px"/>
                        </td>   
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="7"><? echo load_month_buttons(1);  ?></td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>   
        </form>
        <div style="margin-top: 30px;" id="order_list_container"></div>
    </div>
</body>
           
<script src="../includes/functions_bottom.js" type="text/javascript"></script> 
</html>