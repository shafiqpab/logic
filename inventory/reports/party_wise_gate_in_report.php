<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Gate Pass and In Report
				
Functionality	:	
JS Functions	:
Created by		:	Shakil Ahmed 
Creation date 	: 	02/01/2021
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
echo load_html_head_contents("Party Wise Gate In Report","../../", 1, 1, $unicode,1,0); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";




		
function  generate_report(type)
	{
		if( form_validation('cbo_company_name*cbo_within_group*txt_date_from*txt_date_to','Company Name*Within Group*Date From*Date To')==false )
		{
			return;
		} 
			
			
			var cbo_company_name= $("#cbo_company_name").val();
			var cbo_location_id = $("#cbo_location_id").val();
			var cbo_within_group = $("#cbo_within_group").val();
			var cbo_party_name	= $("#cbo_party_name").val();
			var cbo_item_cat 	 = $("#cbo_item_cat").val();
			var cbo_returnable 	= $("#cbo_returnable").val();
			var txt_gate_pass 	= $("#txt_gate_pass").val();
			var txt_gate_in 	= $("#txt_gate_in").val();
			var txt_date_from 	= $("#txt_date_from").val();
			var txt_date_to 	= $("#txt_date_to").val();
			
			
			var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_location_id="+cbo_location_id+"&cbo_within_group="+cbo_within_group+"&cbo_party_name="+cbo_party_name+"&cbo_item_cat="+cbo_item_cat+"&cbo_returnable="+cbo_returnable+"&txt_gate_pass="+txt_gate_pass+"&txt_gate_in="+txt_gate_in+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&type="+type;

			var data="action=generate_report"+dataString;
			freeze_window(3);
			http.open("POST","requires/party_wise_gate_in_report_contorller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_report_reponse; 
		
	}

	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
		var reponse=trim(http.responseText).split("####");
		$("#report_container2").html(reponse[0]);  
		document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		
			/*var tableFilters3 = 
			 {
				col_operation: {id: ["td_total_qty","td_total_amt"],col: [12,14], operation: ["sum","sum"],write_method: ["innerHTML","innerHTML"]}
			 }	*/	
		
			/*if(reponse[2]==3){
				setFilterGrid("table_body_1",-1,tableFilters3);
			}
			else
			{
				setFilterGrid("table_body_1",-1);
			}*/
			setFilterGrid("table_body_1",-1);
		show_msg('3');
		release_freezing();
		}
	} 





function fnc_converted_data(cbo_company,txt_pass_id,system_id,returnable) 
{
	//alert(cbo_company+'_'+txt_pass_id+'_'+system_id+'_'+returnable);


	
	if(returnable==1)
	{
		page_link='requires/party_wise_gate_in_report_contorller.php?action=converted_data&cbo_company='+cbo_company+'&txt_pass_id='+txt_pass_id+'&system_id='+system_id;
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Returnable Item Popup', 'width=900px, height=350px, center=1, resize=0, scrolling=0','');


		emailwindow.onclose=function(){}
	}
	else
	{
		alert("Returnable=No");return;
	}
}

 

</script>
</head>

<body onLoad="set_hotkey()">
 <form name="item_receive_issue_1" id="item_receive_issue_1" autocomplete="off" >
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
   <div style="width:1150px;" align="center">
    <h3 align="center" id="accordion_h1" style="width:1150px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    </div>
    <div style="width:1150px;" align="center" id="content_search_panel">
        <fieldset style="width:1150px;">
             <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                <thead>
                    <tr>
                    	
                        
                        <th width="100" class="must_entry_caption">Company</th>                                
                        <th width="150">Location</th>                                
                        
                        <th width="60" class="must_entry_caption">Within Group</th>
                        <th width="100"> Party Name</th>
						
						<th width="130"><p>Item Category</p><p>&nbsp;</p></th>
						<th width="60">Is Returnable</th>
                        <th width="100">Gate Pass No</th>
                        <th width="100">Gate In No</th>
                        <th width="80" class="must_entry_caption">Date From</th>
                        <th width="80" class="must_entry_caption">Date To</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_field()" /></th>
                    </tr>
                </thead>
                <tr class="general">
                	
                    
                    <td>
                        <?
                        echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/party_wise_gate_in_report_contorller',this.value, 'load_drop_down_location', 'com_location_td' );" );
                        ?>                          
                    </td>
                    
                        <td id="com_location_td" >
							<? 
								echo create_drop_down( "cbo_location_id", 150, $blank_array,"", 1, "-- All  --", 0, "",0 );
                            ?>
                        </td>
                   
                    	<td>
						<? 
                            echo create_drop_down( "cbo_within_group",60, $yes_no,"",1, "--All--", 0,"load_drop_down( 'requires/party_wise_gate_in_report_contorller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_party', 'party_td' );",0 );
                        ?>
                       </td>
                     
	                    <td id="party_td">
	                    	<?
								 echo create_drop_down( "cbo_party_name", 100, $blank_array,"", 1, "-- Select --", $selected, "","","" );
	                         ?>
	                    </td>
					 
                       <td>
						<?
					   	echo create_drop_down( "cbo_item_cat", 120, $item_category,"", 1, "--- Select ---", $selected, "","","",0 );
                        ?>
                    </td>
                    <td >
                        <? 
                            echo create_drop_down( "cbo_returnable",60, $yes_no,"",1, "----", 1,"",0 );
                        ?>           
                    </td> 
                    
                     <td align="center">
                        <input type="text" style="width:95px;"  name="txt_gate_pass" id="txt_gate_pass"  class="text_boxes" placeholder=" Write"   />         
                    </td>
                    <td align="center">
                        <input type="text" style="width:95px;"  name="txt_gate_in" id="txt_gate_in"  class="text_boxes" placeholder=" Write"   />         
                    </td>
                      <td>
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" value="<?php echo date("d-m-Y"); ?>" style="width:65px;" readonly/> 
                    </td>
                    <td>
                    	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" value="<?php echo date("d-m-Y"); ?>" style="width:65px;" readonly/>
                    </td>
                    <td>
                      <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:100px" class="formbutton" />   
                    </td>
                </tr>
                <tr>
                	<td colspan="11" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                    
                </tr>
            </table>  
        </fieldset> 
           
    </div>
    	<div id="report_container" align="center"></div>
        <div id="report_container2"></div> 
</div> 
 </form>    
</body> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
