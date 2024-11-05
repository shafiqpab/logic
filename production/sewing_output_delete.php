<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create sewing output
				
Functionality	:	
JS Functions	:
Created by		:	Md. Rakib Hasan Mondal
Creation date 	: 	27-05-2023
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
echo load_html_head_contents("Sewing Out Info","../", 1, 1, $unicode,'','');

?>	

<script>
    var permission='<? echo $permission; ?>';
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
    /*
    |--------------------------------------------------------------------------
    | Floor Popup
    |--------------------------------------------------------------------------
    |
    */
    function open_floor_popup()
    {
        if( form_validation('cbo_location','Location')==false )
        {
            return;
        }

        let location=$("#cbo_location").val(); 
        let page_link='requires/sewing_output_delete_controller.php?action=floor_popup&location='+location;
        let title="Search Floor Popup";
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=260px,center=1,resize=0,scrolling=0','../')


        emailwindow.onclose=function()
        {
            let theform=this.contentDoc.forms[0]; 
            let floor_id=this.contentDoc.getElementById("hidden_floor_id").value;
            let floor_name=this.contentDoc.getElementById("hidden_floor_name").value.split('*'); 

            $("#text_hidden_floor").val('');
            $("#cbo_floor").val(''); 

            $("#text_hidden_floor").val(floor_id);
            $("#cbo_floor").val([...new Set(floor_name)]); 
        }
    }
    /*
    |--------------------------------------------------------------------------
    | Line Popup
    |--------------------------------------------------------------------------
    |
    */
    function openmypage_line()
    {
        if( form_validation('cbo_company_name*cbo_floor*txt_date','Company*Floor*Prod Date')==false)
        {
            return;
        }
        var company = $("#cbo_company_name").val();   
        var location=$("#cbo_location").val();
        var floor_id=$("#text_hidden_floor").val();
        var line_id=$("#hidden_line_id").val();
        var date=$("#txt_date").val();

        var page_link='requires/sewing_output_delete_controller.php?action=line_search_popup&company='+company+'&location='+location+'&floor_id='+floor_id+'&txt_date='+date+'&line_id='+line_id; 
        
        var title="Search line Popup";
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=310px,height=370px,center=1,resize=0,scrolling=0','../')
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0]; 
            var prodID=this.contentDoc.getElementById("txt_selected_id").value;
            
            var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
            $("#cbo_line").val(prodDescription);
            $("#hidden_line_id").val(prodID); 
        }
    } 
     /*
    |--------------------------------------------------------------------------
    | Internal Ref Popup
    |--------------------------------------------------------------------------
    |
    */
    function openmypage_intref()
    {    
        if(form_validation('cbo_company_name*cbo_line','Company Name*Line')==false)
        {
            return;
        }
        var company = $("#cbo_company_name").val();
        var location = $("#cbo_location").val();
        var floor = $("#text_hidden_floor").val();
        var line = $("#hidden_line_id").val();
        var prod_date = $("#txt_date").val();
        var hidden_job_id = $("#hidden_job_id").val();

        var page_link='requires/sewing_output_delete_controller.php?action=intref_search_popup&company='+company+'&location='+location+'&floor='+floor+'&line='+line+'&prod_date='+prod_date+'&job_id='+hidden_job_id; 


      /*   var company = $("#cbo_company_name").val();
        var hidden_job_id = $("#hidden_job_id").val();
        var page_link='requires/sewing_output_delete_controller.php?action=intref_search_popup&company='+company+'&job_id='+hidden_job_id;  */
        var title="Search Int Ref Popup";
        
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=310px,height=370px,center=1,resize=0,scrolling=0','../')
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var order_no=this.contentDoc.getElementById("hide_order_no").value;
            var job_id=this.contentDoc.getElementById("hide_order_id").value;
                    
            $("#txt_int_ref").val(order_no);
            $("#hidden_job_id").val(job_id);  
            $("#txt_order_no").val(''); 
            $("#txt_color").val(''); 
        }
    }
    /*
    |--------------------------------------------------------------------------
    | Style Popup
    |--------------------------------------------------------------------------
    |
    */
    function openmypage_style()
    {    
        if(form_validation('cbo_company_name*cbo_line','Company Name*Line')==false)
        {
            return;
        }

        var company = $("#cbo_company_name").val();
        var location = $("#cbo_location").val();
        var floor = $("#text_hidden_floor").val();
        var line = $("#hidden_line_id").val();
        var prod_date = $("#txt_date").val();
        var hidden_job_id = $("#hidden_job_id").val();

        var page_link='requires/sewing_output_delete_controller.php?action=style_search_popup&company='+company+'&location='+location+'&floor='+floor+'&line='+line+'&prod_date='+prod_date+'&job_id='+hidden_job_id; 
        var title="Search Style Popup";
        
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=310px,height=370px,center=1,resize=0,scrolling=0','../')
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var order_no=this.contentDoc.getElementById("hide_order_no").value;
            var job_id=this.contentDoc.getElementById("hide_order_id").value;
                    
            $("#txt_style").val(order_no);
            $("#hidden_job_id").val(job_id);  
            $("#txt_order_no").val(''); 
            $("#txt_color").val(''); 
        }
    }  
    /*
    |--------------------------------------------------------------------------
    | PO Popup
    |--------------------------------------------------------------------------
    |
    */
    function openmypage_po()
    {
        if( form_validation('cbo_company_name*cbo_line','Internal Reference*Line')==false)
        {
            return;
        }

        var company = $("#cbo_company_name").val();
        var location = $("#cbo_location").val();
        var floor = $("#text_hidden_floor").val();
        var line = $("#hidden_line_id").val();
        var prod_date = $("#txt_date").val();
        var hidden_job_id = $("#hidden_job_id").val();

        var page_link='requires/sewing_output_delete_controller.php?action=po_search_popup&company='+company+'&location='+location+'&floor='+floor+'&line='+line+'&prod_date='+prod_date+'&job_id='+hidden_job_id; 
 
        
        var title="Search PO Popup";
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=310px,height=370px,center=1,resize=0,scrolling=0','../')
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0]; 
            var prodID=this.contentDoc.getElementById("hide_order_id").value;
            
            var prodDescription=this.contentDoc.getElementById("hide_order_no").value; // product Description
            $("#txt_order_no").val(prodDescription);
            $("#hidden_po_id").val(prodID); 
            // $("#txt_order_no").val(''); 
            $("#txt_color").val(''); 
        }
    }
    /*
    |--------------------------------------------------------------------------
    | Color Popup
    |--------------------------------------------------------------------------
    |
    */
    function openmypage_color()
    {
        if( form_validation('cbo_company_name*cbo_line','Company*Line')==false)
        {
            return;
        }


        var company = $("#cbo_company_name").val();
        var location = $("#cbo_location").val();
        var floor = $("#text_hidden_floor").val();
        var line = $("#hidden_line_id").val();
        var prod_date = $("#txt_date").val();
        var hidden_job_id = $("#hidden_job_id").val();

        var page_link='requires/sewing_output_delete_controller.php?action=color_search_popup&company='+company+'&location='+location+'&floor='+floor+'&line='+line+'&prod_date='+prod_date+'&job_id='+hidden_job_id; 

 
        var title="Search Color Popup";
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=310px,height=370px,center=1,resize=0,scrolling=0','../')
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0]; 
            var prodID=this.contentDoc.getElementById("hide_order_id").value;
            
            var prodDescription=this.contentDoc.getElementById("hide_order_no").value; // product Description
            $("#txt_color").val(prodDescription);
            $("#hidden_color_id").val(prodID); 
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Genarate Report
    |--------------------------------------------------------------------------
    |
    */
    function fn_report_generated(operation) 
    {   
        
        if(form_validation('cbo_company_name*txt_date', 'Company*Prod. Date')==false) 
        {
            return;
        }
        
        freeze_window(operation); 
        var data="action=generate_report"+get_submitted_data_string('cbo_company_name*cbo_location*text_hidden_floor*hidden_line_id*hidden_job_id*hidden_po_id*hidden_color_id*txt_order_no*txt_date', "../../");
        http.open("POST", "requires/sewing_output_delete_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_generate_reponse; 
    }  
    function fnc_generate_reponse() 
    {
        if(http.readyState == 4) 
        {
            show_msg('3'); 
            var reponse=trim(http.responseText).split("####");  
            $('#report_container').html(reponse[0]);  
            release_freezing();
        }
        
    } 
    function delete_data(operation)
    {
        var total_row=$('#production_details_table tbody tr').length; 
		let dataString=""; let j=1; 
        $IDs = $("#production_details_table input:checkbox:checked").map(function () {
            var thisVal = $(this).val();
            var prod_id = $(this).attr('data-prodId');
            var prod_qty = $(this).attr('data-prodQty');
            dataString+='&prod_dtls_id_' + j+ '=' + thisVal+'&prod_id_' + j+ '=' + prod_id+'&prod_qty_' + j+ '=' + prod_qty;
            j++;
        }).get();   
        let data="action=delete_data&operation="+operation+dataString+'&row_num='+j;
        http.open("POST", "requires/sewing_output_delete_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_deleted_response;
    }
    function fnc_deleted_response()
    {
        if(http.readyState == 4) 
		{
            //alert (http.responseText);
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
            fn_report_generated(2);  
			// console.log('hello');
			// if(response[0]==0 || response[0]==1)
			// { 
			// } 
		} 
    }
    function checkAll(){
        // console.log(`checked : ${$('#checkedAll').is(":checked")}`);
        if($("#checkedAll").is(":checked"))
        {
            $('input:checkbox').removeAttr('checked').attr("checked","checked");  
        }
        else
        {
            $('input:checkbox').removeAttr('checked');  
        }
        count_check();
        
    }

    const count_check = () => { // ES6 arrow function
        var tot_row=$('#production_details_table tbody tr').length;
        var count = 0;
        for(var i=1; i<=tot_row; i++)
        {
            var isChecked=$(`#checkitem_${i}`).is(":checked");
            // console.log(`#checkitem_${i}=${isChecked}`);
            if(isChecked) count++;
        }
        // console.log(`count=${count}`);
        $("#total_checked").text(count);
    }
</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">

        <? echo load_freeze_divs ("../",$permission);  ?>
        <fieldset style="width:1020px;">
            <legend>Search Panel</legend>
            <table class="rpt_table" width="1000px" cellpadding="0" cellspacing="0" border="0" align="center">
               <thead>                    
                    <tr>
                        <th width="140"  class="must_entry_caption">Working Company</th>
                        <th width="110">Location</th>
                        <th width="100">Floor</th> 
                        <th width="100">Line</th> 
                        <th width="100">Style </th>
                        <th width="100">IR</th>
                        <th width="100">Color</th>
                        <th width="100">PO</th>
                        <th id="search_text_td" class="must_entry_caption">Prod. Date</th> 
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                    </tr>    
                </thead>
                <tbody>
                    <tr>
                        <td width="140"> 
                            <?
                                echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sewing_output_delete_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                            ?>
                        </td>                   
                        <td width="110" id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location", 110, $blank_array,"", 1, "-- Select --", $selected, " load_drop_down( 'requires/sewing_output_delete_controller', this.value, 'open_floor_popup', 'floor_td' );", 1, "" );
                            ?>
                        </td>
                        <td width="110" id="floor_td">
                            <input type="text" id="cbo_floor"  name="cbo_floor"  style="width:110px" class="text_boxes" onDblClick="open_floor_popup()" placeholder="Browse" readonly />
                            <input type="hidden" id="text_hidden_floor"  name="text_hidden_floor" />
                        </td>
                        <td width="110" id="line_td">
                            <input type="text" id="cbo_line"  name="cbo_line"  style="width:110px" class="text_boxes" onDblClick="openmypage_line()" placeholder="Browse Line"  readonly/>
                               <input type="hidden" id="hidden_line_id" name="hidden_line_id" />  
                        </td>
                        <td>
                            <input name="txt_style" id="txt_style" class="text_boxes" style="width:140px" placeholder="Browse" onDblClick="openmypage_style()" readonly>  
                            <input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
                            <input type="hidden" id="hidden_po_id">
                            <input type="hidden" id="hidden_color_id">
                        </td>
                        <td>  
                            <input name="txt_int_ref" id="txt_int_ref" class="text_boxes" style="width:140px" placeholder="Browse" onDblClick="openmypage_intref()" readonly>  
                        </td> 
                        <td>  
                        <input name="txt_color" id="txt_color" class="text_boxes" style="width:65px" placeholder="Browse" onDblClick="openmypage_color()" readonly>  
                        </td>   
                        <td>
                            <input name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:65px" placeholder="Browse" onDblClick="openmypage_po()" readonly> 
                        </td>   
                        <td>
                            <input name="txt_date" id="txt_date" class="datepicker" style="width:75px" readonly >
                        </td>
                        <td width="70" align="center">
                        <input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated()" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
        <div style="margin-top: 30px;" id="report_container"></div>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>