<?
/*-----------------------------
Purpose			: Revised Booking Report
Functionality	:
JS Functions	:
Created by		:	Md. Sakibul Islam
Creation date 	: 	25-09-2023
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
$user_id=$_SESSION['logic_erp']['user_id'];
echo load_html_head_contents("Revised Booking Entry", "../../", 1, 1,$unicode,'','');



//----------------------------------------------------------------------------------------------------------------------------------
$date                  = date('d-m-Y');
$buyer_cond            = set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond          = set_user_lavel_filtering(' and comp.id','company_id');
$cbo_company_name      = create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "load_drop_down( 'requires/revised_booking_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",0 );

$cbo_buyer_name= create_drop_down( "cbo_buyer_name", 150,"select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","" );
$buttons = load_submit_buttons( $permission, "fnc_revised_booking_info", 0,0 ,"reset_form('revisedbooking_1','booking_list_view','','')",1);//reset_form('revisedbooking_1','booking_list_view','','')
?>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="revisedbooking_1" autocomplete="off" id="revisedbooking_1">
        <fieldset style="width:900px;">
            <legend title="V3">Revised Booking &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;   <font id="app_sms" style="color:#F00"></font></legend>
            <table  width="900" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td align="right" class="must_entry_caption" colspan="4"><b>System NO</b></td>
                    <td colspan="4" align="left">
                        <input class="text_boxes" type="text" style="width:140px" onDblClick="openmypage_systemNumber('requires/revised_booking_report_controller.php?action=system_number_search_popup','System Number Search');" placeholder="Double Click for search" name="txt_system_no" id="txt_system_no" readonly/>
                        <input type="hidden" id="id_approved_id">
                        <input type="hidden" id="update_id"/>
                    </td>
                </tr> 
                <tr>
                    <td width="" align="Left" class="must_entry_caption">Company</td>
					<td width=""><?=$cbo_company_name; ?></td>
                    <td width="" align="Left">Buyer</td>
                    <td width="" id="buyer_td"><?=$cbo_buyer_name; ?></td>

                    <td width="" align="Left" class="must_entry_caption">Order No</td>
                    <td><input type="text" onDblClick="openmypage_order('requires/revised_booking_report_controller.php?action=order_search_popup','Order Search')"  readonly placeholder="Double Click" name="txt_select_item" id="txt_select_item"  style="width:150px"  class="text_boxes" /></td>
                    <input class="text_boxes" type="hidden" style="width:160px"  readonly placeholder="Double Click" name="txt_selected_po" id="txt_selected_po"/>
                    <input class="text_boxes" type="hidden" style="width:160px"  readonly placeholder="Double Click" name="txt_selected_trim_id" id="txt_selected_trim_id"/></legend>

                    <td width="" align="Left">Revised No</td>
                    <td><input style="width:150px;" type="text" class="text_boxes_numeric" name="text_revised_no" id="text_revised_no" readonly/></td>
                </tr>
                <tr>
                    <td align="Left">Part No</td>
                    <td><input style="width:140px;" type="text" class="text_boxes" name="txt_part_no" id="txt_part_no" /></td>
                    <td align="Left">Fab Delivery Date</td>
                    <td><input class="datepicker" type="text" style="width:140px" name="txt_delivery_date" id="txt_delivery_date"/></td>
                    <td align="Left">Remarks</td>
                    <td colspan="5"><input class="text_boxes" type="text" style="width:376px" placeholder="Write"  name="txt_remarks" id="txt_remarks"/></td>
        
                </tr>
                <tr>
                    <td align="Left">Revised Date</td>
                    <td><input  class="datepicker" type="text" style="width:140px" name="txt_revised_date" id="txt_revised_date" value="<?=$date ?>"/></td>
					<td align="Left">Revised Reason</td>
                    <td colspan="7"><input class="text_boxes" type="text" style="width:580px" placeholder="Write"  name="txt_revision_reason" id="txt_revision_reason"/></td>
                </tr>
                <tr>
                    <td align="center" colspan="8" valign="middle" class="button_container"><?=$buttons ;?>
					<input type="button" value="Print" onClick="generate_revised_report();" name="print_booking" id="print_booking" style="width:80px;" class="formbutton" />
                    </td>
                </tr>
				
            </table>
			<div id=""><font id="save_sms" style="color:#F00"></font></div>
        </fieldset>
    </form> 
</div>
<div></div>
<div id="booking_list_view"></div>
<div style="display:none" id="data_panel"></div>
</body>
<script>
			if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
			var permission='<? echo $permission; ?>';

			function openmypage_systemNumber(page_link,title){
				if (form_validation('cbo_company_name','Company Name')==false)
                {
					release_freezing();
					return;
                }
				var cbo_company_name=document.getElementById('cbo_company_name').value;
				var page_link=page_link+'&cbo_company_name='+cbo_company_name;
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=770px,height=450px,center=1,resize=1,scrolling=0','../')
				emailwindow.onclose=function(){
					var theform=this.contentDoc.forms[0];
					var id=this.contentDoc.getElementById("id");		
					if (id.value!=""){
						
						document.getElementById('update_id').value=id.value;
						get_php_form_data( id.value, "populate_data_from_search_popup_booking", "requires/revised_booking_report_controller" );
						set_button_status(1, permission, 'fnc_revised_booking_info',1);
						var param=document.getElementById('txt_select_item').value;
						var po_id=document.getElementById('txt_selected_po').value;
						var cbo_company_name=document.getElementById('cbo_company_name').value;
						fnc_generate_booking(param,po_id,cbo_company_name)
						
					}
				}
			}
			function fnc_revised_booking_info( operation )
            { 
                freeze_window(operation);
				var data_all="";
				//if (form_validation('cbo_company_name*txt_select_item','Company Name* PO Number')==false)
				if (form_validation('cbo_company_name','Company Name')==false)
                {
					release_freezing();
					return;
                }
                else
                {
					data_all=data_all+get_submitted_data_string('txt_system_no*cbo_company_name*cbo_buyer_name*txt_select_item*txt_selected_po*text_revised_no*txt_part_no*txt_delivery_date*txt_remarks*txt_revised_date*txt_revision_reason*update_id',"../../");

				}   
				var row_num=$('#tbl_list_search tr').length;
				var z=1; var dataAll="";var dataAll2="";
				for (var i=1; i<=row_num; i++)
				{
					var composition=encodeURIComponent("'"+$('#composition_'+i).val()+"'");

					//data_all=data_all+get_submitted_data_string('fab_booking_id_'+i+'*pobreakdownid_'+i+'*colortype_'+i+'*construction_'+i+'*precostfabriccostdtlsid_'+i+'*composition_'+i+'*cotaid_'+i+'*preconskg_'+i+'*gsmweight_'+i+'*diawidth_'+i+'*gmtscolorid_'+i+'*colorid_'+i+'*finscons_'+i+'*booking_row_id_'+i,"../../");

					data_all+="&pobreakdownid_" + z + "='" + $('#pobreakdownid_'+i).val()+"'"+"&precostfabriccostdtlsid_" + z + "='" + $('#precostfabriccostdtlsid_'+i).val()+"'"+"&cotaid_" + z + "='" + $('#cotaid_'+i).val()+"'"+"&preconskg_" + z + "='" + $('#preconskg_'+i).val()+"'"+"&colorid_" + z + "='" + $('#colorid_'+i).val()+"'"+"&finscons_" + z + "='" + $('#finscons_'+i).val()+"'"+"&fab_booking_id_" + z + "='" + $('#fab_booking_id_'+i).val()+"'"+"&booking_row_id_" + z + "='" + $('#booking_row_id_'+i).val()+"'"+"&colortype_" + z + "='" + $('#colortype_'+i).val()+"'"+"&construction_" + z + "='" + $('#construction_'+i).val()+"'"+"&gsmweight_" + z + "='" + $('#gsmweight_'+i).val()+"'"+"&diawidth_" + z + "='" + $('#diawidth_'+i).val()+"'"+"&gmtscolorid_" + z + "='" + $('#gmtscolorid_'+i).val()+"'";
					
					dataAll2+="&composition_" + z + "=" +composition+"";
					z++;
				}
			//	alert(data_all)
				if(z==1)
				{
					alert('No data Found.');
					return;
				}
				var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+data_all+dataAll2;
				//alert(data)
                    
                    http.open("POST","requires/revised_booking_report_controller.php",true);
                    http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                    http.send(data);
                    http.onreadystatechange = fnc_revised_booking_info_response;
                
            }
            function fnc_revised_booking_info_response()
            {
                if(http.readyState == 4) 
                {
					
					var reponse=trim(http.responseText).split('**');
					show_msg(reponse[0]);

                    release_freezing();
                    if (reponse[0] == 0) 
                    {
                        $("#update_id").val(reponse[1]);
				        $("#txt_system_no").val(reponse[2]);
                        set_button_status(1, permission, 'fnc_revised_booking_info',1);
						//fnc_generate_booking();
						//reset_form('revisedbooking_1','booking_list_view','');
						var param=document.getElementById('txt_select_item').value;
						var po_id=document.getElementById('txt_selected_po').value;
						var cbo_company_name=document.getElementById('cbo_company_name').value;
						fnc_generate_booking(param,po_id,cbo_company_name);
                        release_freezing();
                        
                    }
                    else if (reponse[0] == 1) 
                    {
                      
						 
						$("#text_revised_no").val(reponse[3]);
						var param=document.getElementById('txt_select_item').value;
						var po_id=document.getElementById('txt_selected_po').value;
						var cbo_company_name=document.getElementById('cbo_company_name').value;
						fnc_generate_booking(param,po_id,cbo_company_name);
					//	reset_form('revisedbooking_1','booking_list_view','');
					set_button_status(1, permission, 'fnc_revised_booking_info',1,1);
                        release_freezing();
                        
                    }
                    else if (reponse[0] == 2) 
                    {
                        if (reponse[0].length>2) reponse[0]=10;
                        show_msg(reponse[0]);
                        reset_form('revisedbooking_1','booking_list_view','');
                        set_button_status(0, permission, 'fnc_revised_booking_info',1);
                        release_freezing();
                    }
                    release_freezing();
                }
            }
			function fnc_generate_booking(param,po_id,cbo_company_name){

				var txt_system_no=document.getElementById('txt_system_no').value;
				var permission='<? echo $permission; ?>';
				var param="'"+param+"'"
				var data="'"+po_id+"'"
				var data="action=show_fabric_booking&data="+data+'&cbo_company_name='+cbo_company_name+'&param='+param+'&txt_system_no='+txt_system_no+'&permission='+permission;
				
				http.open("POST","requires/revised_booking_report_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_generate_booking_reponse;
			}

			function fnc_generate_booking_reponse(){
				if(http.readyState == 4){
					document.getElementById('booking_list_view').innerHTML=http.responseText;
					set_button_status(0, permission, 'fnc_revised_booking_info',2);
					//compare_date(1);
					set_all_onclick();
					release_freezing();
				}
			}
			function openmypage_order(page_link,title)
			{
				if (form_validation('cbo_company_name','Company')==false)
				{
					return;
				}	
				else
				{   var cbo_company_name=document.getElementById('cbo_company_name').value;
					page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_delivery_date','../../');
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1300px,height=470px,center=1,resize=1,scrolling=0','../')
					emailwindow.onclose=function()
					{
						var theform=this.contentDoc.forms[0];;
						var id=this.contentDoc.getElementById("po_number_id");
						var po=this.contentDoc.getElementById("po_number");
						if (id.value!="")
						{
							
							freeze_window(5);
							document.getElementById('txt_selected_po').value=id.value;
							document.getElementById('txt_select_item').value=po.value;
							release_freezing();
							fnc_generate_booking(po.value,id.value,cbo_company_name)
							
				
						}
					}
				}
			}
			function generate_revised_report()
			{
				if (form_validation('txt_system_no','System No')==false)
				{
					return;
				}
				else
				{
					var show_comment='';
					var r=confirm("Press  \"Cancel\"  to hide  comments\nPress  \"OK\"  to Show comments");
					if (r==true)
					{
						show_comment="1";
					}
					else
					{
						show_comment="0";
					}
					var txt_select_item=document.getElementById('txt_select_item').value;
					var txt_selected_po=document.getElementById('txt_selected_po').value;
					var cbo_company_name=document.getElementById('cbo_company_name').value;
					var update_id=document.getElementById('update_id').value;
					$report_title=$( "div.form_caption" ).html();
					var data="action=show_revised_booking_report"+get_submitted_data_string('txt_system_no*cbo_company_name*txt_select_item*txt_selected_po*update_id',"../../")+'&report_title='+$report_title+'&show_comment='+show_comment;
					//freeze_window(5);
					//alert(data)
					http.open("POST","requires/revised_booking_report_controller.php",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = generate_revised_report_reponse;
				}	
			}
			function generate_revised_report_reponse()
			{
				if(http.readyState == 4) 
				{
					var file_data=http.responseText.split('****');
					$('#pdf_file_name').html(file_data[1]);
					$('#data_panel').html(file_data[0] );
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
					d.close();
				}
			}

			function compare_date(str)
			{
				var row_num=$('#tbl_list_search tr').length;
				//alert(str);
				for (var i=1; i<=row_num; i++){
					//var txt_delevary_date_data=document.getElementById('txtddate_'+i).value;
					//var txt_delevary_date_data=document.getElementById('txttnadate_'+i).value;
					
					var txt_delevary_date_data=document.getElementById('txtddate_'+i).value;
					var txt_tna_date_data=document.getElementById('txttnadate_'+i).value;
					var booking_date=document.getElementById('txt_booking_date').value;
				if(txt_delevary_date_data=='')
				{
					txt_delevary_date_data=document.getElementById('txttnadate_'+i).value;
				}
				txt_delevary_date_data= txt_delevary_date_data.split('-');
				var txt_delevary_date_inv=txt_delevary_date_data[2]+"-"+txt_delevary_date_data[1]+"-"+txt_delevary_date_data[0];
				txt_tna_date_data = txt_tna_date_data.split('-');
				var txt_tna_date_inv=txt_tna_date_data[2]+"-"+txt_tna_date_data[1]+"-"+txt_tna_date_data[0];
				booking_date = booking_date.split('-');
				var booking_date_inv=booking_date[2]+"-"+booking_date[1]+"-"+booking_date[0];
				
				var txt_delevary_date = new Date(txt_delevary_date_inv);
				var txt_tna_date = new Date(txt_tna_date_inv);
				var txt_booking_date = new Date(booking_date_inv);
				var lib_tna_intregrate=$('#lib_tna_intregrate').val();
				//alert(lib_tna_intregrate);
				var cbo_isshort=2;
				if(cbo_isshort==1)
				{
					if(txt_delevary_date < txt_booking_date)
					{
						//salert('Delivery Date Not Allowed Less than Booking Date');
						//document.getElementById('txt_delevary_date').value='';
						txt_delevary_date_data=document.getElementById('txttnadate_'+i).value;
					}
				}
				else
				{
					if(str==1)
					{
						if(txt_tna_date_data !='')
						{
							if( lib_tna_intregrate==1)
							{
								if(txt_delevary_date > txt_tna_date)
								{
									alert('Delivery Date Not Allowed Greater Than TNA Date');
									if(txt_tna_date>txt_booking_date)
									{
										//document.getElementById('txt_delevary_date').value=document.getElementById('txt_tna_date').value;
										document.getElementById('txtddate_'+i).value=document.getElementById('txttnadate_'+i).value;
									}
									else
									{
										document.getElementById('txtddate_'+i).value='';
									}
									
									//return;
								}
								else if((txt_delevary_date < txt_booking_date) ||  (txt_booking_date > txt_tna_date))
								{
									alert('Delivery Date Not Allowed Less than Booking Date');
									//document.getElementById('txt_delevary_date').value=document.getElementById('txt_booking_date').value;
									document.getElementById('txtddate_'+i).value='';
								}
							}
							else
							{
								if((txt_delevary_date < txt_booking_date))
								{
									//alert('Delivery Date Not Allowed Less than Booking Date');
									//document.getElementById('txt_delevary_date').value=document.getElementById('txt_booking_date').value;
									//document.getElementById('txtddate_'+i).value='';
								}
							}
						}
						else
						{
							if(txt_delevary_date < txt_booking_date )
							{
								//alert('Delivery Date Not Allowed Less than Booking Date');
								
								//document.getElementById('txt_delevary_date').value=document.getElementById('txt_booking_date').value;
								document.getElementById('txtddate_'+i).value=document.getElementById('txt_booking_date').value;
							}
						}
					}
					if(str==2)
					{
						if(lib_tna_intregrate==1)
						{
							//alert(txt_tna_date);
							if(txt_tna_date !='')
							{
								if(txt_tna_date < txt_booking_date)
								{
									alert('TNA Date is Less than Booking Date');
									//document.getElementById('txt_delevary_date').value='';
									document.getElementById('txtddate_'+i).value='';
									//document.getElementById('txt_tna_date').value='';
									return;
								}
								else
								{
									//document.getElementById('txt_delevary_date').value=document.getElementById('txt_tna_date').value;
									document.getElementById('txtddate_'+i).value=document.getElementById('txttnadate_'+i).value;
									return;
								}
							}
						}
					}
				}
				} //Loop End
			}



			function copy_value(value,field_id,i){
				var copy_val=document.getElementById('copy_val').checked;
				var txttrimgroup=document.getElementById('txttrimgroup_'+i).value;
				var rowCount = $('#tbl_list_search tr').length;

				if(copy_val==true){
					freeze_window(operation);
					for(var j=i; j<=rowCount; j++){
						if(field_id=='txtdescription_'){
							if( txttrimgroup==document.getElementById('txttrimgroup_'+j).value){
								document.getElementById(field_id+j).value=value;
							}
						}
						if(field_id=='txtbrandsupref_'){
							if( txttrimgroup==document.getElementById('txttrimgroup_'+j).value){
								document.getElementById(field_id+j).value=value;
							}
						}
						if(field_id=='cbocolorsizesensitive_'){

							if( txttrimgroup==document.getElementById('txttrimgroup_'+j).value){
								document.getElementById(field_id+j).value=value;
							}
						}
					}
					release_freezing();
				}
			}

			function open_consumption_popup(page_link,title,po_id,i)
			{
				var garments_nature=document.getElementById('garments_nature').value;
				var cbo_company_name=document.getElementById('cbo_company_name').value;
				var txt_job_no=document.getElementById('txtjob_'+i).value;
				var txt_po_id =document.getElementById(po_id).value;
				var cbo_trim_precost_id=document.getElementById('txttrimcostid_'+i).value;
				var txt_trim_group_id=document.getElementById('txttrimgroup_'+i).value;
				var txt_update_dtls_id=document.getElementById('txtbookingid_'+i).value;
				var cbo_colorsizesensitive=document.getElementById('cbocolorsizesensitive_'+i).value;
				var txt_req_quantity=document.getElementById('txtreqqnty_'+i).value;
				var txtwoq=document.getElementById('txtwoq_'+i).value;
				var txt_req_amount=document.getElementById('txtreqamount_'+i).value;
				var txt_avg_price=document.getElementById('txtrate_'+i).value;
				var txt_country=document.getElementById('txtcountry_'+i).value;
				var txt_pre_des=document.getElementById('txtdesc_'+i).value;
				var txt_pre_brand_sup=document.getElementById('txtbrandsup_'+i).value;
				var txtcuwoq=document.getElementById('txtcuwoq_'+i).value;
				var txtcuamount=document.getElementById('txtcuamount_'+i).value*1;
				var cbo_level=document.getElementById('cbo_level').value*1;
				var cons_breck_downn=document.getElementById('consbreckdown_'+i).value;
				var txt_system_no=document.getElementById('txt_system_no').value;
				var cbo_supplier_name=document.getElementById('cbo_supplier_name').value;
				var txtuom=document.getElementById('txtuom_'+i).value;
				var txtexchrate=document.getElementById('txtexchrate_'+i).value;
				1
				if(po_id==0 ){
					alert("Select Po Id")
				}
				else{
					var page_link=page_link+'&garments_nature='+garments_nature+'&cbo_company_name='+cbo_company_name+'&txt_job_no='+txt_job_no+'&txt_po_id='+txt_po_id+'&cbo_trim_precost_id='+cbo_trim_precost_id+'&txt_trim_group_id='+txt_trim_group_id+'&txt_update_dtls_id='+txt_update_dtls_id+'&cbo_colorsizesensitive='+cbo_colorsizesensitive+'&txt_req_quantity='+txt_req_quantity+'&txt_avg_price='+txt_avg_price+'&txt_country='+txt_country+'&txt_pre_des='+txt_pre_des+'&txt_pre_brand_sup='+txt_pre_brand_sup+"&cbo_level="+cbo_level+"&txtwoq="+txtwoq+"&txt_system_no="+txt_system_no+"&cbo_supplier_name="+cbo_supplier_name+"&txtuom="+txtuom+"&txtexchrate="+txtexchrate;
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1280px,height=450px,center=1,resize=1,scrolling=0','../')
					emailwindow.onclose=function(){
						var cons_breck_down=this.contentDoc.getElementById("cons_breck_down");
						var woq=this.contentDoc.getElementById("woqty_sum");
						var rate=this.contentDoc.getElementById("rate_sum");
						var amount=this.contentDoc.getElementById("amount_sum");
						var json_data=this.contentDoc.getElementById("json_data");
						document.getElementById('consbreckdown_'+i).value=cons_breck_down.value;
						document.getElementById('txtwoq_'+i).value=woq.value;
						document.getElementById('txtrate_'+i).value=rate.value;
						document.getElementById('txtamount_'+i).value=amount.value;
						document.getElementById('jsondata_'+i).value=json_data.value;
						calculate_amount(i);
					}
				}
			}

			function set_cons_break_down(i){

				document.getElementById('consbreckdown_'+i).value="";
				document.getElementById('jsondata_'+i).value="";

				var garments_nature=document.getElementById('garments_nature').value;
				var cbo_company_name=document.getElementById('cbo_company_name').value;
				var txt_job_no=document.getElementById('txtjob_'+i).value;
				var txt_po_id =document.getElementById('txtpoid_'+i).value;
				var cbo_trim_precost_id=document.getElementById('txttrimcostid_'+i).value;
				var txt_trim_group_id=document.getElementById('txttrimgroup_'+i).value;
				var txt_update_dtls_id=document.getElementById('txtbookingid_'+i).value;
				var cbo_colorsizesensitive=document.getElementById('cbocolorsizesensitive_'+i).value;
				var txt_req_quantity=document.getElementById('txtreqqnty_'+i).value;
				var txt_avg_price=document.getElementById('txtrate_'+i).value;
				var txt_country=document.getElementById('txtcountry_'+i).value;
				var txt_pre_des=document.getElementById('txtdesc_'+i).value;
				var txt_pre_brand_sup=document.getElementById('txtbrandsup_'+i).value;
				var txtwoq=document.getElementById('txtbalwoq_'+i).value;
				var txtcurwoq=document.getElementById('txtwoq_'+i).value*1;
				var cbo_level=document.getElementById('cbo_level').value*1;
				var cons_breack_down=trim(return_global_ajax_value(garments_nature+"_"+cbo_company_name+"_"+txt_job_no+"_"+txt_po_id+"_"+cbo_trim_precost_id+"_"+txt_trim_group_id+"_"+txt_update_dtls_id+"_"+cbo_colorsizesensitive+"_"+txt_req_quantity+"_"+txt_avg_price+"_"+txt_country+"_"+txt_pre_des+"_"+txt_pre_brand_sup+"_"+cbo_level+"_"+txtcurwoq, 'set_cons_break_down', '', 'requires/revised_booking_report_controller'));
				//alert(cons_breack_down);
				cons_breack_down=cons_breack_down.split("**");
				document.getElementById('consbreckdown_'+i).value=trim(cons_breack_down[0]);
				document.getElementById('jsondata_'+i).value=cons_breack_down[1];
			}

function fnc_show_booking_reponse(){
	if(http.readyState == 4){
        $("#cbo_currency").attr("disabled",true);
		document.getElementById('booking_list_view').innerHTML=http.responseText;
			//compare_date(2);
		set_all_onclick();
		release_freezing();
	}
}

function print_report_button_setting(report_ids) 
{
	$("#print_booking1").hide();
	$("#print_booking2").hide();
	$("#print_booking4").hide();
	$("#print_booking5").hide();
	$("#print_booking6").hide();
	$("#print_booking7").hide();
	$("#print_booking8").hide();
	$("#print_booking9").hide();
	$("#print_booking10").hide();
	$("#print_booking11").hide();
    $("#print_booking12").hide();
	$("#print_booking13").hide();
	$("#print_booking14").hide();
	$("#print_booking15").hide();
	$("#print_booking16").hide();
	$("#print_booking17").hide();
	$("#print_booking18").hide();
	$("#print_booking19").hide();
	$("#print_booking20").hide();
	$("#print_booking21").hide();
	$("#print_booking22").hide();
	$("#print_booking23").hide();
	$("#print_booking24").hide();
	$("#print_booking_wg").hide();
	$("#print_booking25").hide();
	$("#print_booking26").hide();
	$("#print_booking_aal").hide();
	$("#print_booking23_1").hide();
	
	var report_id=report_ids.split(",");
	for (var k=0; k<report_id.length; k++)
	{
		//177
		//alert(report_id[k])
		if(report_id[k]==67) $("#print_booking1").show();
		else if(report_id[k]==183) $("#print_booking2").show();		
		else if(report_id[k]==209) $("#print_booking3").show();
		else if(report_id[k]==235) $("#print_booking5").show();
        else if(report_id[k]==176) $("#print_booking6").show();
		else if(report_id[k]==746) $("#print_booking7").show();
		else if(report_id[k]==227) $("#print_booking8").show();
		else if(report_id[k]==177) $("#print_booking9").show();
		else if(report_id[k]==241) $("#print_booking11").show();
		else if(report_id[k]==274) $("#print_booking10").show();
        else if(report_id[k]==269) $("#print_booking12").show();
		else if(report_id[k]==28) $("#print_booking13").show();
		else if(report_id[k]==280) $("#print_booking14").show();
		else if(report_id[k]==304) $("#print_booking15").show();
		else if(report_id[k]==14) $("#print_booking16").show();
		else if(report_id[k]==719) $("#print_booking17").show();
		else if(report_id[k]==339) $("#print_booking18").show();
		else if(report_id[k]==433) $("#print_booking19").show();
		else if(report_id[k]==768) $("#print_booking20").show();
		else if(report_id[k]==404) $("#print_booking21").show();
		else if(report_id[k]==419) $("#print_booking22").show();
		else if(report_id[k]==426) $("#print_booking23").show();
		else if(report_id[k]==809) $("#print_booking23_1").show();
		else if(report_id[k]==774) $("#print_booking_wg").show();
		else if(report_id[k]==452) $("#print_booking24").show();
		else if(report_id[k]==786) $("#print_booking25").show();
		else if(report_id[k]==502) $("#print_booking26").show();
		else if(report_id[k]==845) $("#print_booking_aal").show();

	}
}

function openmypage_unapprove_request()
{
	if (form_validation('txt_system_no','Booking Number')==false)
	{
		return;
	}

	var txt_system_no=document.getElementById('txt_system_no').value;
	var txt_un_appv_request=document.getElementById('txt_un_appv_request').value;
	var data=txt_system_no+"_"+txt_un_appv_request;
	var title = 'Un Approval Request';
	var page_link = 'requires/revised_booking_report_controller.php?data='+data+'&action=unapp_request_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','../');

	emailwindow.onclose=function()
	{
		var unappv_request=this.contentDoc.getElementById("hidden_appv_cause");
		$('#txt_un_appv_request').val(unappv_request.value);
	}
}

function openlabeldtls_popup(trimitem,i)
{
	var title = 'Label Details';
	
	var page_link = 'requires/revised_booking_report_controller.php?data='+trimitem+'&action=labeldtls_popup';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','../');

	emailwindow.onclose=function()
	{
		var labeldtlsdata=this.contentDoc.getElementById("hidd_dtlsdata").value;
		
		$('#hiddlabeldtlsdata_'+i).val(labeldtlsdata);
		
	}
}

function deletedata()
{
	
		var operation=2;
		freeze_window(operation);
		
		var delete_cause='';
		if(operation==2){
			delete_cause = prompt("Please enter your delete cause", "");
			if(delete_cause==""){
				alert("You have to enter a delete cause");
				release_freezing();
				return;
			}
			if(delete_cause==null){
				release_freezing();
				return;
			}
			var r=confirm("Press OK to Delete Or Press Cancel");
			if(r==false){
				release_freezing();
				return;
			}
		}

		var txt_system_no=document.getElementById('txt_system_no').value;
		var check_is_booking_used_id=return_global_ajax_value(txt_system_no, 'check_is_booking_used', '', 'requires/revised_booking_report_controller');
		var reponse=trim(check_is_booking_used_id).split('**');
		if(trim(reponse[0])!="")
		{
			if(trim(reponse[0])=='approved'){
				alert("This booking is approved");
				release_freezing();
				return;
			}

			if(trim(reponse[0])=='pi1'){
				alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}

			if(trim(reponse[0])=='rec1'){
				alert("Receive  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
				release_freezing();
				return;
			}

			if(trim(reponse[0])=='iss1'){
				alert("Issue found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
				release_freezing();
				return;
			}
			release_freezing();
			//alert("This booking used in PI Table. So Adding or removing order is not allowed")
			return;
		}

		var row_num=1;
		if (form_validation('txt_system_no','Booking No')==false){
			release_freezing()
			return;
		}
		
        var i=1; var dltsid=""; var z=1;
		var data_all=get_submitted_data_string('txt_system_no',"../../",i);
		var listrows =$('#list_view tbody tr').length; 
		
		if(document.getElementById('chkdeleteall').checked==true)
		{
			for (var i = 1; i <= listrows; i++)
			{
				document.getElementById('chkdelete_'+i).checked=true;
				dltsid+="&txtbookingid_"+z+"='" + $('#txtdelete'+i).val()+"'";
				z++;
			}
		}
		else
		{
			for (var i = 1; i <= listrows; i++)
			{
				if(document.getElementById('chkdelete_'+i).checked==true)
				{
					dltsid+="&txtbookingid_"+z+"='" + $('#txtdelete'+i).val()+"'";
					z++;
				}
			}
		}
		if(z==1 && dltsid=="")
		{
			alert("Please Select minimum 1 row.");
			release_freezing()
			return;
		}
		var data="action=delete_dtls_data&operation="+operation+'&total_row='+z+dltsid+data_all+"&delete_cause="+delete_cause;
		
		/*alert(data);release_freezing()
			return;*/
		http.open("POST","requires/revised_booking_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_revised_booking_dtls_reponse;
}

</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>