<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Embellishment report .
Functionality	:	
JS Functions	:
Created by		:	Shafiq 
Creation date 	: 	06-04-2022
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
echo load_html_head_contents("Embellishment Report", "../../", 1, 1,$unicode,'','');

?>	

<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';
			
	function fn_report_generated(rptType)
	{   
        var buyer = $('#cbo_buyer_name').val();
        var style = $('#txt_style_no').val();
        var job = $('#txt_job_no').val();
        var order = $('#txt_order_no').val();
        var int_ref = $('#txt_int_ref').val();
        var supplier = $('#cbo_supplier_name').val();
        var flag = 0;
        if(buyer !=0 || style !="" || job !="" || order !="" || int_ref !="")
        {
            flag = 1;
        }
        else
        {            
            if(form_validation('cbo_company_name*cbo_embel_type','Comapny Name*Embellishment Type')==false)
            {
                flag = 0;
                return;
            }
        }	
			
		var data="action=report_generate&rptType="+rptType+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_style_no*txt_job_no*txt_order_no*txt_int_ref*cbo_embel_type*cbo_year*cbo_supplier_name',"../../");
		freeze_window(3);
		http.open("POST","requires/embellishment_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;	
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
            var response=trim(http.responseText).split("####");
            $("#report_container").html(response[0]);  
            document.getElementById('report_button_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			// setFilterGrid("table_body",-1);		
			show_msg('3');
			release_freezing();
		}
	}

    function new_window()
    {
        document.getElementById('scroll_body').style.overflow="auto";
        document.getElementById('scroll_body').style.maxHeight="none";
        $('#table_body tr:first').hide();
        
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="all"/></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
        d.close(); 
        $('#table_body tr:first').show();
        document.getElementById('scroll_body').style.overflowY="scroll";
        document.getElementById('scroll_body').style.maxHeight="330px";
    }
</script>
</head>
 
<body onLoad="set_hotkey();">
<form id="">
    <div style="width:100%;" align="center"> 

        <? echo load_freeze_divs ("../../",'');  ?>  

         <fieldset style="width:830px;">
      
        	<legend>Search Panel</legend>
            <table class="rpt_table" width="830px" cellpadding="0" cellspacing="0" align="center">
               <thead>                    
                    <tr>
                        <th class="must_entry_caption" width="100">Company Name</th>
                        <th width="100">Buyer Name</th>
                        <th width="100">Supplier</th>
                        <th width="100">Year</th>
                        <th width="80">Style No</th>
                        <th width="80">Job No</th>
                        <th width="80">Order No</th>
                        <th width="80">Int. Ref.</th>
                        <th class="must_entry_caption" width="100">Embel. Type</th>
                        <th width="80"><input type="reset" id="reset_btn" class="formbutton" style="width:60px" value="Reset" /></th>
                    </tr>    
                 </thead>
                <tbody>
                <tr class="general">
                    <td> 
                        <?
                            echo create_drop_down( "cbo_company_name", 100, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/embellishment_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/embellishment_report_controller',this.value, 'load_drop_down_supplier', 'supplier_td' );" );
                        ?>
                    </td>
                    <td id="buyer_td">
                        <? 
                            echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td id="supplier_td">
                        <? 
                            echo create_drop_down( "cbo_supplier_name", 100, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td id="year">
                        <? 
                            echo create_drop_down( "cbo_year", 100, $year,"", 1, "-- Select Year --", $selected, "",0,"" );
                        ?>
                    </td>
                    <td>
                  	 <input type="text"  name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:80px;"   placeholder="Write">
                    </td>
                    <td>
                    	<input type="text"  name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:80px;"   placeholder="Write">
                    </td>
                    <td>
                    	<input type="text"  name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:80px;"   placeholder="Write">
                    </td>
                    <td>
                        <input type="text"  name="txt_int_ref" id="txt_int_ref" class="text_boxes" style="width:80px;"   placeholder="Write">
                    </td>
                    
                    <td id="embel_type_td">
                        <? 

                            echo create_drop_down( "cbo_embel_type", 100, $emblishment_name_array,"", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/embellishment_report_controller',this.value+'_'+document.getElementById('cbo_wo_company_name').value+'_'+document.getElementById('cbo_location_name').value, 'load_drop_down_emb_floor', 'embel_floor_td' );","","0,1,2,3" );
                        ?>
                    </td>
                    <td>
                        <input type="button" id="show_button" class="formbutton" style="width:60px" value="Show" onClick="fn_report_generated(1)" />
                    </td>
                </tr>
                </tbody>
            </table>
            <!--<table>
            	<tr>
                	<td colspan="8">
 						<? //echo load_month_buttons(1); ?> 
                   	</td>
                </tr>
            </table>  -->
            <br />
        </fieldset>
    </div>
    <br>
    
    <div id="report_button_container" align="center" style="margin: 0 auto;padding-bottom:10px;"></div>
    <div id="report_container" align="center" style="margin: 0 auto;"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
