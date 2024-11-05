<?
/*--------------------------------------------Comments----------------
Version (MySql)          :  
Version (Oracle)         :  
Converted by             :  
Converted Date           :  
Purpose			         : 	This form will create Order Allocation Entry
Functionality	         :	
JS Functions	         :
Created by		         :	Kaiyum 
Creation date 	         : 	5-10-2016
Requirment Client        : 
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                   
DB Script                : 
Updated by 		         : 	
Update date		         : 		   
QC Performed BY	         :		
QC Date			         :	
Comments		         :
----------------------------------------------------------------------*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Allocation","../", 1, 1, $unicode,1,'');

?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";  
var permission='<? echo $permission; ?>';


function openmypage(page_link,title)
{
	var garments_nature=document.getElementById('garments_nature').value;
	page_link=page_link+'&garments_nature='+garments_nature;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1250px,height=450px,center=1,resize=0,scrolling=0','')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_job");
		//alert(theemail);
		if (theemail.value!="")
		{
			//freeze_window(5);
		    //reset_form('','','txt_po_no*txt_po_received_date*txt_pub_shipment_date*txt_org_shipment_date*txt_po_quantity*txt_avg_price*txt_amount*txt_excess_cut*txt_plan_cut*cbo_status*txt_details_remark*cbo_delay_for*show_textcbo_delay_for*cbo_packing_po_level*update_id_details*color_size_break_down','','');
			get_php_form_data(theemail.value, "populate_data_from_search_popup", "requires/order_allocation_controller" );
			//show_list_view(theemail.value,'show_po_active_listview','po_list_view','requires/order_allocation_controller','');
			//$('#cbo_company_name').attr('disabled',true);
		 	//show_list_view(theemail.value,'show_deleted_po_active_listview','deleted_po_list_view','../woven_order/requires/woven_order_entry_controller','');
			//set_button_status(1, permission, 'fnc_order_entry',1);
			//load_drop_down( 'requires/woven_order_entry_controller', theemail.value, 'load_drop_down_projected_po', 'projected_po_td' )
			release_freezing();
		}
	}
}
function get_buyer_config(buyer_id)
{
	load_drop_down( 'requires/order_allocation_controller', buyer_id+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_season_buyer', 'season_td');
}

function open_date_wise_distr_popup(page_link,title)
{
	var date_form=document.getElementById('txt_date_from').value;	
	var date_to=document.getElementById('txt_date_to').value;
	var txt_allocated_qty=document.getElementById('txt_allocated_qty').value;
	var tot_smv_qnty=document.getElementById('tot_smv_qnty').value;
	var dist_data=document.getElementById('hidden_row_pop').value;
	//var garments_nature=document.getElementById('garments_nature').value;
	page_link=page_link + "&date_form="+date_form+"&date_to="+date_to+"&txt_allocated_qty="+txt_allocated_qty+"&tot_smv_qnty="+tot_smv_qnty+'&dist_data='+dist_data;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=300px,height=450px,center=1,resize=0,scrolling=0','')
	emailwindow.onclose=function()
	{
		var data_dtls_row=this.contentDoc.getElementById("hidden_distr_month_row").value;
		data_dtls_row = data_dtls_row.slice(0, -1);
		document.getElementById('hidden_row_pop').value = data_dtls_row;
		document.getElementById('txt_popup_no').value = data_dtls_row;
	}
}
function total_smv(){
  var smv_qty 		=$("#tot_smv_qnty").val()*1;
  var allocated_qty =$("#txt_allocated_qty").val()*1;
  var cal_total_smv = (smv_qty*allocated_qty);
  $("#txt_total_smv").val(cal_total_smv);
  job_qty_bln();
  $("#hidden_row_pop").val('') 
}
function job_qty_bln(){
	 var allocated_qtyy =$("#txt_allocated_qty").val()*1;
	 var total_job_qtyy =$("#txt_total_job_quantity").val()*1;
	 var balance_job_qty=(total_job_qtyy-allocated_qtyy);
	 $("#txt_qty_balance").val(balance_job_qty);
}
//save operation
function fnc_order_allocation_entry(operation)
{ 
	if (form_validation('txt_job_no*cbo_company_mst*txt_popup_no','Job Number*Company Name*Date Wise Distribute')== false)
	{
	return;
	}
	var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_job_no*txt_qty_balance*cbo_company_mst*cbo_location_name*cbo_item*cbo_complexity*tot_smv_qnty*txt_allocated_qty*txt_total_smv*txt_date_from*txt_date_to*cbo_po_no*hidden_row_pop*update_id',"../");
	//alert(data);
	freeze_window(operation);
	http.open("POST","requires/order_allocation_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_section_response;
}

function fnc_section_response()
{
	if(http.readyState == 4)
	{
		//alert (http.responseText); return ;
		var reponse=http.responseText.split('**');
		show_msg(trim(reponse[0]));
		show_list_view(reponse[1],'section_list_view_action','section_list_view','requires/order_allocation_controller','setFilterGrid("list_view",-1)');
		//reset_form('orderallocation_1','','');
		//set_button_status(0, permission, 'fnc_order_allocation_entry',1);
		
		release_freezing();
	}
}
function fnc_change_dist(){
	 $("#hidden_row_pop").val('');
	 $("#txt_popup_no").val(''); 
}
/*var txt_job_noo=document.getElementById('txt_job_no').value;	
//var txt_job_noo=$("#txt_job_no").val();
alert(txt_job_noo);*/
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
<!-- Important Field outside Form -->  
     <? echo load_freeze_divs ("../",$permission);  ?>
      <form name="orderallocation_1" id="orderallocation_1" autocomplete="off">
    
     <table width="90%" cellpadding="0" cellspacing="2" align="center" >
     	<tr>
        	<td width="70%" align="center" valign="top">  <!--   Form Left Container -->
            	<fieldset style="width:950px;">
                <legend> Order Allocation Info </legend>
               
            		<table  width="900" cellspacing="2" cellpadding="0" border="0">
                       <tr>
                            <td  width="130" height="" align="right" class="must_entry_caption">Job No</td>              <!-- 11-00030  -->
                                <td  width="170" >
                                <input style="width:160px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/order_allocation_controller.php?action=order_popup','Job/Order Selection Form')" class="text_boxes" placeholder="New Job No" name="txt_job_no" id="txt_job_no" readonly  />                               
                                </td>
                                <td  width="130" align="right">Company Name </td>
                                <td width="170">
                               		<?
									//echo "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name";
									//get_company_config(this.value);set_smv_check(this.value)
							   			echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, " ",1 );
							   		?> 
                                 </td>
                              <td width="130" align="right" >Buyer Name</td>
                               <td id="buyer_td">
                              <? 
							  	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1 ); 
                              //  echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1 );
                               ?>	  
                              </td>
                        </tr>
                       <tr>
                            <td  width="130" align="right">Team Leader</td>
                            <td width="170">
                            		<?  
							  	echo create_drop_down( "cbo_team_leader", 172, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "",1);
									?>	
							  
                          	</td>
                          	<td width="130" align="right">Dealing Merchant</td>
                          	<td id="marchant_td">
                          		<? 
								echo create_drop_down( "cbo_dealing_merchant", 172, "select id,team_member_name from lib_mkt_team_member_info where   status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "",1 );
                            	//echo create_drop_down( "cbo_dealing_merchant", 172, $blank_array,"", 1, "-- Select Team Member --", $selected, "",1 );
                            	?>	  
                          </td>
                          <td width="130" align="right">Prod. Dept </td>
                           <td>
                              <?
                                echo create_drop_down( "cbo_product_department", 115, $product_dept, "", 1, "-Select-", $selected, "", 1, "" );
                               ?>
                                 <input class="text_boxes" type="text" style="width:40px;" name="txt_product_code" id="txt_product_code" maxlength="10" title="Maximum 10 Character" disabled />	  
                           </td>
                          </tr>
                         <tr>
                         		<td width="130" align="right" >Job Qty</td>
                               	<td>
                              		<input  value="" name="txt_total_job_quantity" id="txt_total_job_quantity" style="width:52px " class="text_boxes_numeric" readonly disabled/>
                                    Balance
                                  <input class="text_boxes_numeric" type="text" style="width:52px;" name="txt_qty_balance" id="txt_qty_balance" maxlength="10" title="Maximum 10 Character"  disabled/>	  
                              	</td>
                           
                                <td  width="130" align="right">Season</td>
                                <td width="170" id="season_td">
                               		<? 
									echo create_drop_down( "cbo_season_name", 172, "select a.id,a.season_name from lib_buyer_season a,variable_order_tracking b where a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0   and b.season_mandatory=1 and b.variable_list=44","id,season_name", 1, "-- Select Season --", $selected, "",1 );
									//echo create_drop_down( "cbo_season_name", 172, $blank_array,"", 1, "-- Select Season --", $selected, "",1 ); ?>           
                                 </td>
                                 <td width="130" align="right" >Entry Date</td>
                               	 <td>
                              		<input  value="" name="txt_entry_date" id="txt_entry_date" style="width:160px " class="text_boxes" readonly disabled/>
                              	 </td>
                             
                        </tr>
                                           
                    </table>
                 
              </fieldset>
           </td>
         </tr>
         
	</table>
    <!--Second part -->
     <table width="400px;" cellpadding="0" cellspacing="1" align="center" >
     	<tr>
        	<td width="100%" align="center" valign="top">  <!--   Form Left Container -->
            	<fieldset style="width:1180px;">
	               <legend> Order Allocation Entry </legend>
	            

                      <table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" width="100%">
                  	 	<thead>  
                           	<th width="50">PO No</th> 
                            <th width="36">Item</th>           	 
	                        <th width="69"  class="must_entry_caption">Company Name</th>
                            <th width="82">Location</th>
	                        <th width="78">Complexity</th>
	                        <th width="36">SMV</th>
                            <th width="65">PO Qty</th>
	                        <th width="67">Allocated Qty</th>
	                        <th width="61">Total SMV</th>
	                        <!--<th width="150">Buyer Name</th>
	                        <th width="80">Job No</th>
	                        <th width="100">Style Ref </th>
	                        <th width="100">Internal Ref</th>
	                        <th width="100">File No</th>
	                        <th width="120">Order No</th>-->
	                        <th width="329">Sewing Date Range</th>
                            <th width="159"  class="must_entry_caption">Date Wise Distribution</th>        
                    	<td width="1"></thead>
        				<tr>
                        <td align="left" id="po_td"> 
                       
							<? 
								echo create_drop_down( "cbo_po_no", 140, "",1, "-- Select Company --", $selected, "" );
							?>
                    	</td>
                        <td align="left" id="gmts_item_td">
  					  		<? 
								echo create_drop_down( "cbo_item", 192, $garments_item, "",1," -- Select Item --", 0); 
							?>
                      	</td>
                    	<td align="left"> 
                       
							<? 
								echo create_drop_down( "cbo_company_mst", 155, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $selected, "load_drop_down( 'requires/order_allocation_controller', this.value, 'load_location', 'location_td' ) " );
							?>
                    	</td>
                        <td align="left" id="location_td">
  					  		<? 
								 echo create_drop_down( "cbo_location_name", 130, "select location_name,id from lib_location where is_deleted=0  and status_active=1 order by location_name",'id,location_name', 1, '--- Select Location ---', 0, "" );
							?>
                      	</td>
                        
                      <td align="left" id="complexity_td">
  					  	<?
						echo create_drop_down( "cbo_complexity", 80, $complexity_level,"", 1, "-- Select Complexity --", 0, "","","" );
					   ?>
                      </td>
                      <td align="left" >
                    <input class="text_boxes_numeric" type="text" style="width:30px;" name="tot_smv_qnty" id="tot_smv_qnty" readonly onKeyUp="total_smv();"/>                          
                      </td> 
                      <td align="center"><input name="txt_po_qty"  id="txt_po_qty" class="text_boxes_numeric" style="width:50px" readonly></td>
                      <td align="center"><input name="txt_allocated_qty"  id="txt_allocated_qty" class="text_boxes_numeric" style="width:50px" onKeyUp="total_smv();" onClick="fnc_change_dist();"></td> 
                      <td align="center"><input name="txt_total_smv"  id="txt_total_smv" class="text_boxes_numeric" style="width:50px" readonly></td> 

                    <td align="center"><input name="txt_date_from" id="txt_date_from" readonly class="datepicker" style="width:50px" onClick="fnc_change_dist();">
					  <input name="txt_date_to" id="txt_date_to" readonly class="datepicker" style="width:50px" onClick="fnc_change_dist();">
					 </td>                     
                     <td align="center"> 
            		 <input style="width:50px;"  title="Click to Date Wise Distribution" onClick="open_date_wise_distr_popup('requires/order_allocation_controller.php?action=date_wise_distr_popup','Date wise distribution')" class="text_boxes" placeholder="Popup" name="txt_popup_no" id="txt_popup_no" readonly /> 
                     </td>
        		</tr>
                <tr>
                        <input type="hidden" id="update_id"> 
                        <input type="hidden" id="hidden_row_pop">
                </tr>
               		 <tr>
            		<td  align="center" height="40" valign="middle" colspan="11">
			             <? 
						echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 );		
						?>
						<? echo load_month_buttons();  ?>
            		</td>
            	</tr>
                
                	  <tr>
                        <td align="center" colspan="11" valign="middle" class="button_container">
                       <? 
//function reset_form( forms, divs, fields, default_val, extra_func, non_refresh_ids ) 
					   echo load_submit_buttons( $permission, "fnc_order_allocation_entry", 0,0 ,"reset_form('orderallocation_1','','')"); ?>
                        </td>
                      </tr>
             		 </table>
               	
               </fieldset>
             </td>
          </tr>
       </table>
       <fieldset style="width:950px;">
		<legend>List View</legend>
			<div style="width:950px;" id="section_list_view" align="left">
			<?php
	      	 $lib_company_arr=return_library_array( "select id,company_name from lib_company", "id","company_name" );
        	 $arr=array(1=>$lib_company_arr, 2=>$garments_item,3=>$complexity_level);
	        echo create_list_view("list_view","Job No,Company Name,Item,Complexity,SMV,Allocated Qty,Total SMV","130,160,160,160,100,100","950","200",0, "select id,job_no,company_id,item,complexity,smv,allocated_qty,total_smv from ppl_order_allocation_mst where is_deleted=0 and status_active=1","get_php_form_data", "id","'load_php_data_to_form'", 1, "0,company_id,item,complexity", $arr , "job_no,company_id,item,complexity,smv,allocated_qty,total_smv", "requires/order_allocation_controller", 'setFilterGrid("list_view",-1);' ); 
	        ?>
			</div>
		</fieldset>
        </form>
	</div>
    
</body>
       
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>