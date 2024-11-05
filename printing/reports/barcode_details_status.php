<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Barcode Status Details Report.
Functionality	:	
JS Functions	:
Created by		:	Md. Minul Hasan  
Creation date 	: 	23-05-2022
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
echo load_html_head_contents("Barcode Status Details Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';

	function search_by(val)
	{
		$('#txt_search_string').val('');
		if(val==1 || val==0) $('#search_by_td').html('Embl. Job No.');
		else if(val==2) $('#search_by_td').html('W/O No');
		else if(val==3) $('#search_by_td').html('Buyer Job');
		else if(val==4) $('#search_by_td').html('Buyer Po');
		else if(val==5) $('#search_by_td').html('Buyer Style');
	}

	function generate_report()
	{

		var cbo_company_name = document.getElementById('cbo_company_name').value;
		var cbo_party_name = document.getElementById('cbo_party_name').value;
		var txt_date_from = document.getElementById('txt_date_from').value;
		var txt_date_to = document.getElementById('txt_date_to').value;
		var txt_search_common = document.getElementById('txt_search_common').value;
		var txt_search_challan = document.getElementById('txt_search_challan').value;
		var cbo_string_search_type = document.getElementById('cbo_string_search_type').value;
		var cbo_within_group = document.getElementById('cbo_within_group').value;
		var cbo_type = document.getElementById('cbo_type').value;
		var txt_search_string = document.getElementById('txt_search_string').value;
		var cbo_location_name = document.getElementById('cbo_location_name').value;
		
		
		var data="action=create_receive_search_list_view&cbo_company_name="+cbo_company_name+"&cbo_party_name="+cbo_party_name+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&txt_search_common="+txt_search_common+"&txt_search_challan="+txt_search_challan+"&cbo_string_search_type="+cbo_string_search_type+"&cbo_within_group="+cbo_within_group+"&cbo_type="+cbo_type+"&txt_search_string="+txt_search_string+"&cbo_location_name="+cbo_location_name;
		
		//alert(data);
		freeze_window(3);
		http.open("POST","requires/barcode_details_status_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			$("#report_container").html(reponse[0]);  
			document.getElementById('report_container1').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("rec_issue_table",-1,'');
			show_msg('3');
			release_freezing();
		}
	}

	function new_window(type)
	{
		document.getElementById('list_div').style.overflow="none";
		document.getElementById('list_div').style.maxHeight="100%";
		$('#rec_issue_table tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close(); 
		$('#rec_issue_table tr:first').show();
		document.getElementById('list_div').style.overflow="auto"; 
		document.getElementById('list_div').style.maxHeight="350px";
	}
</script>
</head>
<body onLoad="set_hotkey();">
<form>
         <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <div align="center" style="width:100%;" >
            <form name="searchreceivefrm_1"  id="searchreceivefrm_1" autocomplete="off">
                <table width="870" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="11"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                    	<tr>                	 
                            <th width="140" class="must_entry_caption">Company Name</th>
                            <th width="140">Location Name</th>
                            <th width="50">Within Group</th>
                            <th width="120">Customer</th>
                            <th width="70">Receive ID</th>
                            <th width="80">Challan No</th>
                            <th width="100">Search By</th>
                    		<th width="100" id="search_by_td">Embl. Job No.</th>
                            <th width="100" colspan="2">Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="selected_job">  <!--  echo $data;-->
							<? 
								echo create_drop_down( "cbo_company_name", 140, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --",'0', "load_drop_down( 'requires/barcode_details_status_controller', this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer_pop', 'buyer_td' );load_drop_down( 'requires/barcode_details_status_controller', this.value, 'load_drop_down_location', 'location_td' );"); ?>
                            </td>
							<td id="location_td">
								<? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "" );?>
							</td>
                            <td>
							<?
								echo create_drop_down( "cbo_within_group", 50, $yes_no,"", 0, "--  --",'2', "load_drop_down( 'requires/barcode_details_status_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer_pop', 'buyer_td' );" ); ?>
							</td>
                            <td id="buyer_td">
								<? 
								echo create_drop_down( "cbo_party_name", 120, $blank_array,"", 1, "-- Select Party --", $selected, "" );?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:60px" placeholder="Receive ID" />
                            </td>
                            <td>
                                <input type="text" name="txt_search_challan" id="txt_search_challan" class="text_boxes" style="width:70px" placeholder="Challan" />
                            </td>
                            <td>
								<?
                                    $search_by_arr=array(1=>"Embl. Job No.",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                                    echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                                ?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From">
                            </td>
                            <td>
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="generate_report();" style="width:100px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="10" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                    </tbody>
                </table>
                
            </form>
        </div>
    </div>
    <div style="margin-top:10px " id="report_container1" align="center"></div>
    <div id="report_container" align="center"></div>
 </form>    
</body>
<script>set_multiselect('cbo_shiping_status','0','0','','0','0');</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
