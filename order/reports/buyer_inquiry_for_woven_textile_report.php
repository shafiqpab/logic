<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Buyer Inquiry/HL/LD/SO Report For Woven Textile
Functionality	:	
JS Functions	:
Created by		:	Md. Mamun Ahmed Sagor
Creation date 	: 	28-08-2023
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
$menu_id=$_SESSION['menu_id'];
//-------------------------------------------------------------------------------------------
echo load_html_head_contents("Buyer Inquiry/HL/LD/SO Report For Woven Textile", "../../", 1, 1,'','','');
 
?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	function fn_report_generated()
	{		
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		

		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*txt_system_id*txt_date_from*txt_date_to*cbo_type*cbo_buyer_name*txt_style_ref',"../../");
		// alert(data);return;
		freeze_window(3);
		http.open("POST","requires/buyer_inquiry_for_woven_textile_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			$('#report_container').html(response[0]);
			
			setFilterGrid("tbl_list_search",-1);
				
			show_msg('3');
			release_freezing();
		}
	}
	
	function load_type(type){
		 
		if(type==1){
			$("#requisitionId").text("Inquery No");
		}else if(type==2){
			$("#requisitionId").text("Requisition No");
		}else{
			$("#requisitionId").text("HL/LD/SO No");
		}
		



	}
	 
		
	 
	
 
	
 
 	

</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",''); ?>
		<form name="requisitionApproval_1" id="requisitionApproval_1"> 
        <h3 style="width:900px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
        <div id="content_search_panel">      
            <fieldset style="width:900px;">
                <table class="rpt_table" width="900" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                    <thead>
                                    
                        <tr>
                            <th class="must_entry_caption">Company Name</th>
							<th>Buyer Name</th>
							<th>Style Ref</th>
							<th>Type</th>
							<th id="requisitionId">Inquery No</th>
                            <th colspan="2" >Date Range</th>                         
                           
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                        </tr>
                    </thead>
                	<tbody>
                    	<tr class="general">
							<td> 
								<?
								echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected,"load_drop_down( 'requires/buyer_inquiry_for_woven_textile_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td');");
								?>
							</td>
							<td id="buyer_td">
                        	<? echo create_drop_down( "cbo_buyer_name", 140, "select id,buyer_name from lib_buyer  where status_active =1 and is_deleted=0  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ,0); ?>
                        		
                       		 </td>
							<td>
								<input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes_numeric" style="width:120px"/>
							</td>
							<td> 
								<?
								$type_arr = array(1=>'Inquery',2=>'Sample Requisition',3=>'HL/LD/SO');
								echo create_drop_down( "cbo_type", 130, $type_arr,"", 0, "", $selected,"load_type(this.value)","", "" );
								?>
							</td>
							
							<td>
								<input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes_numeric" style="width:120px"/>
							</td>
							<td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px;" placeholder="From Date"/></td>					
							<td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px;" placeholder="To Date" /></td>
							
							
							<td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:100px" onClick="fn_report_generated()"/></td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
		</form>
	</div>
    <div id="report_container" align="center"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$('#cbo_approval_type').val(0);
</script>
</html>