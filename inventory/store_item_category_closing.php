<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Store Item Category Year Clossing
				
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	13-03-2021
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
echo load_html_head_contents("Store Item Year Closing","../", 1, 1, $unicode);
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function fnc_start_processing()
{
	if( form_validation('cbo_company_id*cbo_year*cbo_item_category_id','Company Name*Year*Category')==false )
	{
		return;
	}
	var confirm_msg=window.confirm("Would You Want To Close This Year");
	//alert(confirm_msg);return;
	if(confirm_msg)
	{
		var data="action=save_update_delete_process"+get_submitted_data_string('cbo_company_id*cbo_year*cbo_month*cbo_item_category_id',"../");
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/store_item_category_closing_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_start_processing_reponse;
	}
	
}

function fnc_start_processing_reponse()
{	
	if(http.readyState == 4) 
	{	 
		var response=trim(http.responseText).split('__');
		if(response[0]==50)
		{
			alert(response[1]);
			release_freezing();
			//reset_form('storeItemInquiry_1','','','','','');
			location.reload();
			return;
		}
		alert(http.responseText);
		$('#report_container2').html(http.responseText);
		release_freezing();
		//reset_form('','','cbo_company_id*','','','');
		location.reload();
	}
} 
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../",$permission);  ?>    		 
    <form name="storeItemInquiry_1" id="storeItemInquiry_1" autocomplete="off" > 
    <div style="width:100%;" align="center">
        <h3 style="width:830px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div style="width:100%;" id="content_search_panel">
            <fieldset style="width:830px;">
                <table class="rpt_table" width="830" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th class="must_entry_caption">Company</th> 
                            <th class="must_entry_caption">Year</th>                               
                            <th>Month</th>
                            <th class="must_entry_caption">Category</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
							<? 
                            	echo create_drop_down( "cbo_company_id", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/store_item_category_closing_controller',this.value, 'load_drop_down_year', 'year_td' );" );
                            ?>                            
                        </td>
                        <td id="year_td"> 
                            <? 
							//date("Y",time())-1
							echo create_drop_down( "cbo_year", 150, $blank_array,"", 1,"-- All --", 0, "",0,"" ); ?>
                        </td>
						<td id="month_td"> 
                            <? echo create_drop_down( "cbo_month", 150, $blank_array,"", 1,"-- All --", 0, "",0,"" ); ?>
                        </td>
                        <td id="category_td">
							<? echo create_drop_down( "cbo_item_category_id", 170, $blank_array,"", 1,"-- Select Category --", 0, "",0,"" ); ?>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Closing Process Start" onClick="fnc_start_processing()" style="width:150px" class="formbutton" />
                        </td>
                    </tr>
                </table> 
            </fieldset> 
		</div>
    </div>
    <br /> 
    <div id="report_container2" style="margin-left:300px; font-size:20px; font-weight:bold; color:#FF0000;"></div> 
    </form>    
</div>  
</body> 
<script src="../includes/functions_bottom.js" type="text/javascript"></script>  
</html>
