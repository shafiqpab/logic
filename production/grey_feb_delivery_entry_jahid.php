<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Grey febric delivery  
				
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	04-05-2014
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
echo load_html_head_contents("Import CI Statement Report","../", 1, 1, $unicode,1,1); 


?>	
<script>
var permission='<? echo $permission; ?>';



function generate_list()
{
	if(form_validation('cbo_company_id','Company Name')==false)
	{
	return;
	}
		var data="action=list_generate"+get_submitted_data_string("cbo_company_id*cbo_location_id*cbo_buyer_id*txt_prog_no*cbo_year*txt_job_no*txt_ord_no*txt_date_from*txt_date_to*cbo_status*update_mst_id*hidden_receive_id*hidden_product_id*hidden_order_id*cbo_order_type","../");
	freeze_window(4);
	http.open("POST","requires/grey_feb_delivery_entry_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_generate_list_response;
}
	
function fn_generate_list_response()
{
	if(http.readyState == 4) 
	{
		var response=trim(http.responseText).split("####");
		//alert(http.responseText);
		$('#list_view_container').html(response[0]);
		document.getElementById('report_container').innerHTML='<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
		setFilterGrid('table_body',-1);
		show_msg('4');
		if(response[2]!="")
		{
			set_button_status(1, permission, 'fnc_prod_delivery',1,1);
		}
		release_freezing();
	}
}

function new_window()
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";
	$('#table_body tbody').find('tr:first').hide();
	$('#tbl_header thead').find('th:nth-child(18),th:nth-child(19)').hide();
	$('#table_body tbody tr').find('td:nth-child(18),td:nth-child(19)').hide();
	$('#tbl_footer tfoot').find('th:nth-child(5),th:nth-child(6)').hide();
	$('#tbl_footer tfoot').find('th:nth-child(4)').attr("width","100");
	$('#tbl_footer tfoot').find('th:nth-child(3)').attr("width","100");
	$('#tbl_footer tfoot').find('th:nth-child(2)').attr("width","100");
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_print').innerHTML+'</body</html>');
	d.close(); 
	$('#table_body tbody').find('tr:first').show();
	$('#tbl_header thead').find('th:nth-child(18),th:nth-child(19)').show();
	$('#table_body tbody tr').find('td:nth-child(18),td:nth-child(19)').show();
	$('#tbl_footer tfoot').find('th:nth-child(5),th:nth-child(6)').show();
	$('#tbl_footer tfoot').find('th:nth-child(4)').removeAttr().attr("width","90");
	$('#tbl_footer tfoot').find('th:nth-child(3)').removeAttr().attr("width","90");
	$('#tbl_footer tfoot').find('th:nth-child(2)').removeAttr().attr("width","90");
	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="200px";
}

function fnc_prod_delivery(operation)
{
	if(form_validation('txt_delevery_date*cbo_company_id','Delivery Date*Company Name')==false)
	{
		return;
	}
	if(operation==4)
	{
		var program_ids=product_ids=order_ids="";
		var total_tr=$('#table_body tbody tr').length-1;
		var company=$('#cbo_company_id').val();
		var location=$('#cbo_location_id').val();
		var buyer=$('#cbo_buyer_id').val();
		var from_date=$('#txt_date_from').val();
		var to_date=$('#txt_date_to').val();
		var update_mst_id=$('#update_mst_id').val();
		var delivery_date=$('#txt_delevery_date').val();
		var Challan_no=$('#txt_sys_num').val();
		var cbo_order_type=$('#cbo_order_type').val();
		//alert(update_mst_id);
		for(i=1; i<=total_tr; i++)
		{
			try 
			{
				if ($('#txtcurrentdelivery_'+i).val()!="")
				{
					program_id = $('#hidesysid_'+i).val();
					if(program_ids=="") program_ids= program_id; else program_ids +=','+program_id;
					product_id = $('#hideprodid_'+i).val();
					if(product_ids=="") product_ids= product_id; else product_ids +=','+product_id;
					order_id = $('#hideorder_'+i).val();
					if(order_ids=="") order_ids= order_id; else order_ids +=','+order_id;
				}
				
			}
			catch(e) 
			{
				//got error no operation
			}
		}
		
		if(program_ids=="")
		{
			alert("Please Enter At Least Single Quantity in Current Delivery Field");
			return;
		}
	
		print_report(program_ids+'_'+company+'_'+from_date+'_'+to_date+'_'+product_ids+'_'+order_ids+'_'+location+'_'+buyer+'_'+update_mst_id+'_'+delivery_date+'_'+Challan_no+'_'+cbo_order_type, "delivery_challan_print", "requires/grey_feb_delivery_entry_controller" ) ;
	}
	else
	{
		var details_data=""
		var total_row=$('#table_body tbody tr').length-1;
		for(var i=1; i<=total_row; i++)
		{
			var qnty=$('#txtcurrentdelivery_'+i).val();
			var hiddendtlsid=$('#hiddendtlsid_'+i).val();
			if(qnty*1>0 || hiddendtlsid!="")
			{
				details_data +='hidesysid_'+i+'*'+'hidesysnum_'+i+'*'+'hideprodid_'+i+'*'+'hidejob_'+i+'*'+'hideorder_'+i+'*'+'hideconstruction_'+i+'*'+'hidecomposition_'+i+'*'+'hidegsm_'+i+'*'+'hidedia_'+i+'*'+'txtcurrentdelivery_'+i+'*'+'hiddendtlsid_'+i+'*'+'txtroll_'+i+'*'+'hideprogrum_'+i+'*'+'hidefindtls_'+i+'*';
			}
		}
		//alert(details_data);return;
		var master_data='txt_delevery_date*cbo_company_id*cbo_location_id*cbo_buyer_id*update_mst_id*cbo_order_type';
		
		var total_datastring=details_data+master_data;
		var data="action=save_update_delete&operation="+operation+"&total_row="+total_row+get_submitted_data_string(total_datastring,"../");
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/grey_feb_delivery_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_prod_delivery_response;
	}
}

function fnc_prod_delivery_response()
{
	if(http.readyState == 4) 
	{
		//alert(http.responseText);
		var response=trim(http.responseText).split("**");
		
		if(response[0]==0)
		{
			$('#txt_sys_num').val(response[2]);
			$('#update_mst_id').val(response[1]);
			var dtls_id=response[3];	
			var table_row=$('#table_body tbody tr').length-1;
			var k=1;
			for(var i=1;i<=table_row;i++)
			{
			
				var chack_field=$('#txtcurrentdelivery_'+i).val();
				if(chack_field!="")
				{
					if(k!=1) dtls_id=(dtls_id*1)+1;
					$('#hiddendtlsid_'+i).val(dtls_id);
					k++;
				}
				
			}
			$('#cbo_order_type').attr("disabled",true);
			set_button_status(1, permission, 'fnc_prod_delivery',1,1);
			show_msg(response[0]);
			release_freezing();
		}
		else if(response[0]==1)
		{
			var dtls_id=response[2];	
			var table_row=$('#table_body tbody tr').length-1;
			var k=1;
			for(var i=1;i<=table_row;i++)
			{
			
				var chack_field=$('#txtcurrentdelivery_'+i).val();
				var up_dtls=trim($('#hiddendtlsid_'+i).val());
				//alert(up_dtls);
				if(chack_field!="")
				{
					if(up_dtls=="")
					{
						if(k!=1) dtls_id=(dtls_id*1)+1;
						$('#hiddendtlsid_'+i).val(dtls_id);
						k++;
					}
				}
				
			}
			set_button_status(1, permission, 'fnc_prod_delivery',1,1);
			show_msg(response[0]);
			release_freezing();
		}
		else
		{
			show_msg(response[0]);
			release_freezing();
			
		}
	}
}

function open_mypage()
{
	var company=$("#cbo_company_id").val();
	if (form_validation('cbo_company_id','Buyer')==false )
	{
		return;
	}
	
	var page_link='requires/grey_feb_delivery_entry_controller.php?action=delevery_search&company='+company;
	var title='Delivery Information Entry Form';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=980px,height=400px,center=1,resize=1,scrolling=0','');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var ex_data=this.contentDoc.getElementById("hidden_tbl_id").value.split("_");
		//alert(ex_data[0]);
		if(trim(ex_data[0])!="")
		{
			$('#txt_sys_num').val(ex_data[3]);
			get_php_form_data(ex_data[0], "populate_master_from_data", "requires/grey_feb_delivery_entry_controller" );//+"**"+invoice_id
			$('#cbo_order_type').attr("disabled",true);
			generate_list();
		}
		//set_button_status(1, permission, 'fnc_prod_delivery',1,1);
	}
}



function setHideval( i )
{
	var dev_qty=$('#txtcurrentdelivery_'+i).val()*1;
	var prod_qty=$('#totalqtyTd_'+i).text();
	if((dev_qty*1)>(prod_qty*1))
	{
		alert("Delivery Quantity Must be Less Then Blance Quantity");
		$('#txtcurrentdelivery_'+i).val("");
	}
	else
	{
		$('#total_current_val').text(($('#total_current_val').text()*1)-($('#hidden_current_val_'+i).val()*1));
		
		$('#total_current_val').text(number_format((($('#total_current_val').text()*1)+(dev_qty*1)),2));
		$('#hidden_current_val_'+i).val(dev_qty);
	}
}

function total_roll(i)
{
	var roll_qty=($('#txtroll_'+i).val()*1);
	var hideroll=($('#hideroll_'+i).val()*1);
	if(hideroll>0)
	{
		$('#total_roll').text($('#total_roll').text()-$('#hideroll_'+i).val());
	}
	$('#total_roll').text(number_format((($('#total_roll').text()*1)+roll_qty),2));
	$('#hideroll_'+i).val(roll_qty);
}


</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="left">
    <? echo load_freeze_divs ("../",$permission);  ?><br />    		 
        <form name="prod_delivery" id="prod_delivery" autocomplete="off" > 
        <div>
        <fieldset style="width:1180px;">
			<legend>Search Panel</legend>
            <table class="" width="1180" cellpadding="0" cellspacing="0" rules="all" border="1">
                <tr>
                    <td align="right" width="200" class="must_entry_caption">Delevery Date&nbsp;:</td>
                    <td width="170">
                            &nbsp;<input type="text" name="txt_delevery_date" id="txt_delevery_date" class="datepicker" style="width:150px;" readonly />
                    </td>
                    <td align="right" width="100">Chalan No&nbsp;:</td>
                    <td>
                            &nbsp;<input type="text" name="txt_sys_num" id="txt_sys_num" class="text_boxes" style="width:150px;" onDblClick="open_mypage()" placeholder="Browse For Challan No" readonly/>
                    </td>
                </tr>
            </table>
       <!-- <h3 align="left" id="accordion_h1" style="width:1120px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>--> 
            <div id="content_search_panel" style="width:1180px" align="left">      
            <table class="rpt_table" width="1180" cellpadding="0" cellspacing="0" rules="all" border="1">
                <thead>
                    <th width="130" class="must_entry_caption">Company</th>
                    <th width="120">Location</th>
                    <th width="120">Buyer</th>
                    <th width="100">Prog/Book No</th>
                    <th width="80">Job Year</th>
                    <th width="90">Job No</th>
                    <th width="100">Order No</th>
                    <th width="70">Date From</th>
                    <th width="70">Date To</th>
                    <th width="100">Status</th>
                    <th width="100">Type</th>
                    <th ><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('prod_delivery','list_view_container','','','')" /></th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <? 
                                echo create_drop_down( "cbo_company_id", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected , "load_drop_down( 'requires/grey_feb_delivery_entry_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/grey_feb_delivery_entry_controller', this.value, 'load_drop_down_buyer_form', 'buyer_td' );" );
                            ?>                            
                        </td>
                        <td id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location_id",120,$blank_array,"", 1, "--Select Location--", $selected, "","","","","","",2);
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_id",120,$blank_array,"", 1, "--Select Buyer--", $selected, "","","","","","",2);
                            ?>
                        </td>
                         <td >
                            <input type="text" name="txt_prog_no" id="txt_prog_no" class="text_boxes" style="width:80px;" />
                        </td>
                        <td>
                            <? 
                                $year_current=date("Y");
                                echo create_drop_down( "cbo_year", 80, $year,"", 1, "--Select Year--", $year_current, "" );
                            ?>                            
                        </td>
                        <td >
                            <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:85px;" />
                        </td>
                        <td >
                            <input type="text" name="txt_ord_no" id="txt_ord_no" class="text_boxes" style="width:95px;" />
                        </td>
                        <td>
                            <input type="date" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px;" readonly/> 
                        </td>
                        <td>
                            <input type="date" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px;" readonly/>
                        </td>
                        <td>
                            <? 
                                $delevery_status=array(1=>"Pending",2=>"Full Delivery");
                                echo create_drop_down( "cbo_status", 100, $delevery_status,"", 0, "", 1 );
                            ?>
                        </td>
                        <td>
                            <? 
                                $order_status=array(1=>"With Order",2=>"Without Order");
                                echo create_drop_down( "cbo_order_type", 100, $order_status,"", 0, "", 1 );
                            ?>
                        </td>
                        <td>
                            <input type="hidden" id="update_mst_id" name="update_mst_id" value="" >
                            <input type="hidden" id="hidden_receive_id" name="hidden_receive_id" value="" >
                            <input type="hidden" id="hidden_product_id" name="hidden_product_id" value="" >
                            <input type="hidden" id="hidden_order_id" name="hidden_order_id" value="" >
                            <input type="button" name="search" id="search" value="Show" style="width:80px" class="formbutton" onClick="generate_list()" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="12" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                    </tr>
              </tbody>
            </table> 
        </div>
        

    <div style="margin-top:10px;" id="report_container" align="center"></div>
    <div style="margin-top:10px;" id="list_view_container" align="left"></div>
    </fieldset>
    </div>
 </form>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
