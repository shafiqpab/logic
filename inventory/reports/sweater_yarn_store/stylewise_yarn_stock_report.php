<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Stylewise Yarn Stock Ledger
				
Functionality	:	
JS Functions	:
Created by		:	Mohammad Shafiqur Rahman 
Creation date 	: 	16-03-2019
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Stylewise Yarn Stock","../../../", 1, 1, $unicode,1,1); 

?>	

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	var tableFilters = 
	{
		col_30: "none",
		col_operation: {
		id: ["value_total_opening","value_total_opening_value","value_gt_purchage","value_gt_issue_return","value_gt_trans_in","value_gt_loan_rcvd","value_gt_twisting_rcvd","value_gt_reconning_recvd","value_gt_recvd","value_gt_rec_value","value_gt_knitting","value_gt_reconning_issue","value_gt_twisting_issue","value_gt_sample","value_gt_linking","value_gt_loan_issue","value_gt_others","value_gt_recv_return","value_gt_trans_out","value_gt_issue","value_gt_issue_value","value_gt_current_stock","value_gt_usd_amount","value_gt_bdt_amount","value_gt_allocated","value_gt_allocatedYarnBalance","value_gt_available"],
		//col: [10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,33,34,35,36,37],
		col: [11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,34,35,36,37,38],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function openmypage_job_no()
	{
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		
		var data = $("#cbo_company_name").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#cbo_year_selection").val()+"_"+$("#text_style_no").val()+"_"+$("#txt_job_no").val()+"_"+$("#txt_date_from").val()+"_"+$("#txt_date_to").val()+"_"+$("#cbo_store_wise").val()+"_"+$("#cbo_store_name").val();

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/stylewise_yarn_stock_report_controller.php?data='+data+'&action=job_no_popup', 'Job No Search', 'width=700px,height=420px,center=1,resize=0,scrolling=0','../');

			emailwindow.onclose=function()
			{				
				var theemailid=this.contentDoc.getElementById("txt_job_id").value;
				var theemailjob=this.contentDoc.getElementById("txt_job_no").value;
				var theemailstyle=this.contentDoc.getElementById("txt_style_ref").value;
				//var response=theemailid.value.split('_');
				if ( theemailid!="" )
				{
					//alert (response[0]);
					freeze_window(5);
					$("#hidd_job_id").val(theemailid);
					$("#txt_job_no").val(theemailjob);
					$("#txt_style_no").val(theemailstyle);
					release_freezing();
				}
			}
	}

	function openmypage_style_no()
	{
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		
		var data = $("#cbo_company_name").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#cbo_year_selection").val()+"_"+$("#text_style_no").val()+"_"+$("#txt_job_no").val()+"_"+$("#txt_date_from").val()+"_"+$("#txt_date_to").val()+"_"+$("#cbo_store_wise").val()+"_"+$("#cbo_store_name").val();

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/stylewise_yarn_stock_report_controller.php?data='+data+'&action=job_no_popup', 'Style No Search', 'width=700px,height=420px,center=1,resize=0,scrolling=0','../');

			emailwindow.onclose=function()
			{				
				var theemailid=this.contentDoc.getElementById("txt_job_id").value;
				var theemailjob=this.contentDoc.getElementById("txt_job_no").value;
				var theemailstyle=this.contentDoc.getElementById("txt_style_ref").value;
				//var response=theemailid.value.split('_');
				if ( theemailid!="" )
				{
					//alert (response[0]);
					freeze_window(5);
					$("#hidd_job_id").val(theemailid);
					$("#txt_job_no").val(theemailjob);
					$("#txt_style_no").val(theemailstyle);
					release_freezing();
				}
			}
	}

	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		
		//$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><style></style></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="350px";
		
		//$("#table_body tr:first").show();
	}



	function generate_report(type)
	{
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_buyer = $("#cbo_buyer_id").val();
		var txt_style_no = $("#txt_style_no").val();
		var txt_job_no 	= $("#txt_job_no").val();
		var cbo_store_wise 	= $("#cbo_store_wise").val();
		var from_date 	= $("#txt_date_from").val();
		var to_date 	= $("#txt_date_to").val();
		var cbo_store_name 	= $("#cbo_store_name").val();
		var cbo_year_selection 	= $("#cbo_year_selection").val();
		var cbo_ship_status 	= $("#cbo_ship_status").val();
		var cbo_date_type 	= $("#cbo_date_type").val();
		var txt_composition_id 	= $("#txt_composition_id").val();
		var cbo_value_range_by 	= $("#cbo_value_range_by").val();
		
		
		if ($("#cbo_company_name").val() > 0 && $("#cbo_buyer_id").val() == 0 && $("#txt_job_no").val()=="" && $("#cbo_store_name").val() == 0 ) 
		{	
			$("#curr_date_range").addClass('must_entry_caption');
			//alert("if block");
			if( form_validation('txt_date_from*txt_date_to','From Date*To Date')==false )
			{
				return;
			}
		}
		else if(cbo_company_name ==0 ){
			if( form_validation('cbo_company_name','Company')==false )
			{
				return;
			}
		} 
		else {								
			if(cbo_store_wise ==1 && cbo_store_name==0){
				if( form_validation('cbo_store_name','Store Name')==false )
				{
					return;
				}
			}
		}
		
		if(type==3)
		{
			if( form_validation('txt_date_from*txt_date_to','From Date*To Date')==false )
			{
				return;
			}
		}

		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_buyer="+cbo_buyer+"&txt_style_no="+txt_style_no+"&txt_job_no="+txt_job_no+"&cbo_store_wise="+cbo_store_wise+"&from_date="+from_date+"&to_date="+to_date+"&cbo_store_name="+cbo_store_name+"&cbo_year_selection="+cbo_year_selection+"&cbo_ship_status="+cbo_ship_status+"&txt_composition_id="+txt_composition_id+"&rpt_type="+type+"&cbo_date_type="+cbo_date_type+"&cbo_value_range_by="+cbo_value_range_by;
		//alert(dataString);
		var data="action=generate_report"+dataString;
		freeze_window(3);
		http.open("POST","requires/stylewise_yarn_stock_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}

	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			//alert(reponse);
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			if(reponse[2]==1)
			{
				//setFilterGrid("scroll_body",tableFilters,-1);,tableFilters
				setFilterGrid("scroll_body",-1);
			}
			else if( reponse[2]==3)
			{
				setFilterGrid("scroll_body",-1,tableFilters);
			}
			
			show_msg('3');
			release_freezing();
		}
	} 
 
	function fn_photo_view(photo_location)
	{ 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/stylewise_yarn_stock_report_controller.php?photo_location='+photo_location+'&action=photo_view'+'&permission='+permission, "Photo View", 'width=460px,height=300px,center=1,resize=1,scrolling=0','../')
	}

	function openmypage(rec_id,prod_id,action,from_date,to_date)
	{ //alert(des_prod)
		var companyID = $("#cbo_company_name").val();
		var popup_width='1100px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/stylewise_yarn_stock_report_controller.php?companyID='+companyID+'&rec_id='+rec_id+'&from_date='+from_date+'&to_date='+to_date+'&action='+action+'&prod_id='+prod_id, 'Details Veiw', 'width='+popup_width+', height=250px,center=1,resize=0,scrolling=0','../../');
	}

	function openmypage_issue(issue_id,transfer_id,prod_id,job_no,action,from_date,to_date)
	{ //alert(des_prod)
		var companyID = $("#cbo_company_name").val();
		var popup_width='1100px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/stylewise_yarn_stock_report_controller.php?companyID='+companyID+'&issue_id='+issue_id+'&from_date='+from_date+'&to_date='+to_date+'&action='+action+'&prod_id='+prod_id+'&job_no='+job_no+'&transfer_id='+transfer_id, 'Details Veiw', 'width='+popup_width+', height=250px,center=1,resize=0,scrolling=0','../../');
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#scroll_body tr:first').show();
		//document.getElementById('scroll_body').style.maxWidth="120px";
	}

	function openmypage_composition()
	{
		var pre_composition_id = $("#txt_composition_id").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/stylewise_yarn_stock_report_controller.php?action=composition_popup', 'Composition Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../../');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var composition_des=this.contentDoc.getElementById("hidden_composition").value; 
			var composition_id=this.contentDoc.getElementById("hidden_composition_id").value;
			$("#txt_composition").val(composition_des);
			$("#txt_composition_id").val(composition_id);
			
		}
	}

	function openmypage_image(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=450px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
		}
	}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../../", $permission);  ?>    		 
    <form name="stylewise_yarn_stock_ledger_1" id="stylewise_yarn_stock_ledger_1" autocomplete="off" > 
    <div style="width:100%;" align="center">
        <h3 style="width:1250px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div style="width:100%;" id="content_search_panel">
            <fieldset style="width:1250px;">
                <table class="rpt_table" width="1250" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th class="must_entry_caption" >Company</th> 
                            <th>Buyer</th>                               
                            <th>Style No</th>
                            <th>Job No</th>
							<th>Yarn Composition</th>
                            <th>Date Type</th>
                            <th id="curr_date_range" colspan="2">Current Date Range</th>
							<th>Stock Value</th>
                            <th>Store Wise</th>
                            <th>Store Name</th>
                            <th>Shipping Mode</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:140px" class="formbutton" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
							<? 
                               echo create_drop_down( "cbo_company_name", 110, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- All Company --", $selected, "load_drop_down( 'requires/stylewise_yarn_stock_report_controller', this.value, 'load_drop_down_buyer', 'buyer' );load_drop_down( 'requires/stylewise_yarn_stock_report_controller', this.value+'**'+document.getElementById('cbo_store_wise').value, 'load_drop_down_store', 'store_td' );" );
                            ?>                            
                        </td>
                        <td id="buyer"> 
							<?
								$buyer_sql = "select a.id, a.buyer_name from lib_buyer a, lib_buyer_party_type b where  a.id = b.buyer_id and a.status_active =1 and a.is_deleted=0 and b.party_type in (1,3,21,90)group by a.id, a.buyer_name order by a.buyer_name"; 
								
								echo create_drop_down("cbo_buyer_id", 120, $buyer_sql, "id,buyer_name", 1, "-- Select --", 0, "", 0);
								
								//echo create_drop_down( "cbo_supplier", 120, $blank_array,"",0, "--- Select Supplier ---", $selected, "",0);
                            ?>
                        </td>
                        <td>
                             <input type="text" name="txt_style_no" id="txt_style_no" class="text_boxes" placeholder="Browse" onDblClick="openmypage_style_no()" readonly/>
                             <input type="hidden" name="hidden_style_id" id="hidden_style_id" />
                        </td>
                        <td>
                             <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes"  placeholder="Browse" onDblClick="openmypage_job_no()" readonly />
                             <input type="hidden" name="hidden_job_id" id="hidden_job_id" />
                        </td>
						<td>
                            <input type="text" id="txt_composition" name="txt_composition" class="text_boxes" style="width:100px" value="" onDblClick="openmypage_composition();" placeholder="Browse" readonly />
                            <input type="hidden" id="txt_composition_id" name="txt_composition_id" class="text_boxes" style="width:70px" value=""  />
                        </td>
                        <td> 
							<?
							//, 2=>"PO Receive Date", 3=>"Shipment Date"
							$date_type_arr=array(1=>"Transaction Date");
							echo create_drop_down("cbo_date_type", 100, $date_type_arr, "", 0, "", 1, "", 0);
							?>
                        </td>
                        <td align="center">
                             <input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:55px" placeholder="From Date" readonly/>
                          </td>
                        <td align="center">
                            
                             <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To Date" readonly/>
                        </td>
						<td>
                            <?
								$value_range_by=array(1=>'Value with 0',2=>'Value without 0');
                                echo create_drop_down( "cbo_value_range_by", 90, $value_range_by,"", 1, "--Select--", 1, "",0 );
                            ?>
                        </td>
                        <td> 
                            <?
                                echo create_drop_down( "cbo_store_wise", 50, $yes_no,"", 0, "--Select--", 2, "load_drop_down( 'requires/stylewise_yarn_stock_report_controller', document.getElementById('cbo_company_name').value+'**'+this.value, 'load_drop_down_store', 'store_td' );",0 );
                            ?>
                        </td>
                        <td id="store_td">
                            <? 
                                echo create_drop_down( "cbo_store_name", 100, $blank_array,"", 1, "-- All Store --", $storeName, "",1 );
                            ?>
                        </td>
                         <td>
                            	<?
								$ship_status_arr = array(1=>"Pending + Partial",2=>"Full Shipment/Closed"); 
								echo create_drop_down( "cbo_ship_status", 100, $ship_status_arr,"", 1,"-All-","", "",0,"" ); ?>
                         </td>
                        <td >
                            <input type="button" name="search" id="search1" value="Show" onClick="generate_report(1)" style="width:60px;" class="formbutton" />
							<input type="button" name="search" id="search1" value="Show 2" onClick="generate_report(3)" style="width:60px;" class="formbutton" />
                        </td>
                    </tr>	
					<tr>
						<td colspan="12" align="center">
							<? echo load_month_buttons(1); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="search" id="search1" value="Register" onClick="generate_report(2)" style="width:100px;" class="formbutton" />
						</td>
					</tr>				
                    
                </table> 
            </fieldset> 
		</div>
    </div>
    <br /> 
        <!-- Result Contain Start-->
         
        	<div id="report_container" align="center"></div>
            <div id="report_container2" style="margin-left:5px"></div> 
        
        <!-- Result Contain END-->
    
    
    </form>    
</div>   
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
<script>
	setFilterGrid('rpt_tablelist_view',-1);
</script>
</body> 


</html>
