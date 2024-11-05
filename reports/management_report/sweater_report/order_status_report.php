<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This form will create Order Status report
				
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam
Creation date 	: 	29-01-2020
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
$user_id=$_SESSION['logic_erp']['user_id'];
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Ex-Factory Report","../../../", 1, 1, $unicode,1,1);
?>	
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  

	
	function fn_on_change()
	{
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_working_company_name = $("#cbo_working_company_name").val();
		load_drop_down( 'requires/order_status_report_controller', cbo_company_name, 'load_drop_down_buyer', 'buyer' );
		set_multiselect('cbo_buyer','0','0','','0','fn_on_change2()');
	}
	function fn_on_change2()
	{
		
		//var buyer_id = 1; 
		var buyer_id = $("#cbo_buyer").val();
		load_drop_down( 'requires/order_status_report_controller', buyer_id, 'load_drop_down_brand', 'brand_td' );
		set_multiselect('cbo_brand_name','0','0','','0');
	}
	function fnc_brandload()
	{
		var buyer_id=$('#cbo_buyer').val();
		if(buyer_id!=0)
		{
			load_drop_down( 'requires/order_status_report_controller', buyer, 'load_drop_down_brand', 'brand_td');
		}
	}
	
	function fn_report_generated(type)
	{
		if( form_validation('cbo_gauge','Gauge')==false )
		{
			return;
		}
			
        if(type==6){
            var data="action=report_generate_2&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_working_company_name*cbo_buyer*cbo_brand_name*txt_job*txt_job_id*txt_style*cbo_gauge*cbo_date_type*txt_date_from*txt_date_to*txt_order_no',"../../../");
        }
		else if(type==7){
            var data="action=report_generate_3&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_working_company_name*cbo_buyer*cbo_brand_name*txt_job*txt_job_id*txt_style*cbo_gauge*cbo_date_type*txt_date_from*txt_date_to*txt_order_id*txt_order_no',"../../../");
        }
		/* else if(type==8){
            var data="action=report_generate_8&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_working_company_name*cbo_buyer*cbo_brand_name*txt_job*txt_job_id*txt_style*cbo_gauge*cbo_date_type*txt_date_from*txt_date_to*txt_order_id*txt_order_no',"../../../");
        } */else{
            var data="action=report_generate&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_working_company_name*cbo_buyer*cbo_brand_name*txt_job*txt_job_id*txt_style*cbo_gauge*cbo_date_type*txt_date_from*txt_date_to*txt_order_no',"../../../");
        }
		
		freeze_window(3);
		http.open("POST","requires/order_status_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	
	}
		
	function fn_report_generated_reponse()
	{	
		 
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			 
			$("#report_container2").html(reponse[0]);  
           
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
          
			//setFilterGrid("scroll_body",-1);
			release_freezing();
		}
	}

	function generate_fabric_excel_report(type)
	{
		var is_mail_send=0;var mail_id=0;
		if( form_validation('cbo_gauge','Gauge')==false )
		{
			return;
		}
		else
		{
			
			var path=1;
			freeze_window(5);
			var data="action="+type+get_submitted_data_string('cbo_company_name*cbo_working_company_name*cbo_buyer*cbo_brand_name*txt_job*txt_job_id*txt_style*cbo_gauge*cbo_date_type*txt_date_from*txt_date_to*txt_order_id*txt_order_no',"../../../")+'&is_mail_send='+is_mail_send+'&mail_id='+mail_id+'&path='+path;
			var excel_check=0;

			if(type=='report_generate_8')
			{
				var user_id = "<? echo $user_id; ?>";
				$.ajax({
					url: 'requires/order_status_report_controller.php',
					type: 'POST',
					data: data,
					success: function(data){
						window.open('../../../auto_mail/tmp/order_recap_'+user_id+'.pdf');	
						release_freezing();	
					}
				});
				var excel_check=1;
				
			}
			if (excel_check==1){
				// http.open("POST","requires/order_status_report_controller.php",true);
				// //http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				// http.setRequestHeader("Content-type","application/pdf");
				// http.send(data);
				// http.onreadystatechange = generate_fabric_report_reponse2;
			}
			
		}
	}
	
	function generate_fabric_report_reponse2(){
		if(http.readyState == 4){
			release_freezing();
			var file_data=http.responseText.split("****");
			if(file_data[2]==100){ 
				$('#btn_knit_completed8').removeAttr('href').attr('href','requires/'+trim(file_data[1]));
				document.getElementById('btn_knit_completed8').click();
			}
			else{
				$('#data_panel').html(file_data[0]);
			}
		 
			 
			 
           
			 

			/* var report_title="fabric booking";
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title>'+report_title+'</title></head><body>'+document.getElementById('data_panel').innerHTML+'</body></html>');
			d.close(); */
		}
	}
	

	function new_window()
	{
		 
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	    '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		 
	}

    
    function openmypage_order()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_buyer = $("#cbo_buyer").val();

		var page_link='requires/order_status_report_controller.php?action=order_no_popup&cbo_company_name='+cbo_company_name+'&cbo_buyer='+cbo_buyer;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=460px,height=350px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_no=this.contentDoc.getElementById("hide_order_no").value;
			var order_id=this.contentDoc.getElementById("hide_order_id").value;
			$('#txt_order_no').val(order_no);
			$('#txt_order_id').val(order_id);	 
		}
	}
	function openmypage_job()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_buyer = $("#cbo_buyer").val();

		var page_link='requires/order_status_report_controller.php?action=job_no_popup&cbo_company_name='+cbo_company_name+'&cbo_buyer='+cbo_buyer;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=460px,height=350px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_job').val(job_no);
			$('#txt_job_id').val(job_id);	 
		}
	}

	

	
	
	
 function openmypage_style()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_buyer = $("#cbo_buyer").val();

		var page_link='requires/order_status_report_controller.php?action=style_popup&cbo_company_name='+cbo_company_name+'&cbo_buyer='+cbo_buyer;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=460px,height=350px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_style').val(style_no);
			$('#txt_job_id').val(job_id);	 
		}
	}
</script>
</head>
<body onLoad="set_hotkey();fnc_brandload();">
 <div style="width:100%;" align="center">
<form id="order_status_report_1" name="order_status_report_1">
    <div style="width:1270px;" align="center">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:1710" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:1270px;">
                  <table class="rpt_table" width="1270" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<thead>
                        <tr> 	 	
                            <th>Company</th>
                            <th>Working Company</th>
                            <th>Buyer</th>
							<th>Brand</th>
                            <th>Job</th>
                            <th>Style</th>
                            <th>Order</th>
                            <th class="must_entry_caption">Gauge</th>
                            <th>Type</th>            
                            <th colspan="2">Date Range</th>                              
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton"/></th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr class="general">
                        <td>
                            <? 
                            echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-- All Company --", $selected, "" );
                            ?>                            
                        </td>
                        <td>
                            <? 
                                echo create_drop_down( "cbo_working_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-- All Company --", $selected, "" );
                            ?>
                        </td>
                        <td id="buyer"> 
                            <? 
                                echo create_drop_down( "cbo_buyer", 130, $blank_array,"", 1, "--All Buyer--", $selected, "",0,"" );
                            ?>
                        </td>
						<td id="brand_td"><?=create_drop_down( "cbo_brand_name", 70, $blank_array,"", 1,"--Select--", $selected, "",0,"" ); ?></td>
                        <td> 
                             <input name="txt_job" id="txt_job"  class="text_boxes" style="width:90px" placeholder="Browse/Write" onDblClick="openmypage_job();">
                             <input type="hidden" id="txt_job_id" name="txt_job_id"/>
                        </td>
                        <td> 
                             <input name="txt_style" id="txt_style"  class="text_boxes" style="width:90px" placeholder="Browse/Write" onDblClick="openmypage_style();">
                        </td>
                        <td> 
                             <input name="txt_order_no" id="txt_order_no"  class="text_boxes" style="width:90px" placeholder="Browse" onDblClick="openmypage_order();" readonly>
                             <input type="hidden" id="txt_order_id" name="txt_order_id"/>
                        </td>
                        <td> 
                            <? 
                            //  $gauge_arr=array(1=>"1.5 GG",2=>"3 GG",3=>"5 GG",4=>"7 GG",5=>"10 GG",6=>"12 GG",7=>"14 GG");
                            echo create_drop_down( "cbo_gauge", 80, $gauge_arr,"", 1, "--Gauge--", $selected, "",0,"" );
                            ?>
                        </td>
                        <td>
                            <?
                            $shipment_type_arr=array(1=>"Pub Ship Date", 2=>"PO Receive Date");
                            echo create_drop_down( "cbo_date_type", 120, $shipment_type_arr,"", "", "", 1, "", 0, "" );
                            ?>
                        </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:80px" placeholder="From Date" value="01-01-<?= date('Y');?>">
                        </td>
                        
                        <td>
                            <input name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:80px" placeholder="To Date"  value="31-12-<?= date('Y');?>">
                        </td>
                        <td>
							<input type="button" name="btn_show" id="btn_knit_completed" value="Show" onClick="fn_report_generated(0)" style="width:80px;" class="formbutton"/> 
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7" align="center">
                            <? echo load_month_buttons(1); ?>
                        </td>
                        
                        <td colspan="5" align="right">
							<input type="button" name="btn_confirmed" id="btn_confirmed" value="Confirmed" onClick="fn_report_generated(1)" style="width:80px;" class="formbutton"/> 
                            <input type="button" name="btn_projected" id="btn_projected" value="Projected" onClick="fn_report_generated(2)" style="width:80px;" class="formbutton"/> 
							<input type="button" name="btn_show" id="btn_knit_completed" value="Recap" onClick="fn_report_generated(6)" style="width:80px;" class="formbutton"/> 
							<input type="button" name="btn_show" id="btn_knit_completed" value="Order Recap" onClick="fn_report_generated(7)" style="width:100px;" class="formbutton"/> 
							<!-- <input type="button" name="btn_show" id="btn_knit_completed" value="Order Recap2" onClick="fn_report_generated(8)" style="width:100px;" class="formbutton"/>  -->
							<input type="button" value="Order Recap2" onClick="generate_fabric_excel_report('report_generate_8')"  style="width:100px;" name="btn_show" id="btn_knit_completed" class="formbutton" />&nbsp;<A id="btn_knit_completed8" href="" style="text-decoration:none" download hidden>BB</A>
							<input type="button" name="btn_knit_completed" id="btn_knit_completed" value="Knit Comp" onClick="fn_report_generated(3)" style="width:80px;" class="formbutton"/> 
							<input type="button" name="btn_shipped" id="btn_shipped" value="Shipped" onClick="fn_report_generated(4)" style="width:80px;" class="formbutton"/> 
							<input type="button" name="btn_pending_shipped" id="btn_shipped" value="Shipped Pen" onClick="fn_report_generated(5)" style="width:80px;" class="formbutton"/>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
     </form>
    <div id="report_container"></div>
    <div id="report_container2" style="margin-left:5px"></div> 
 </div>    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	set_multiselect('cbo_company_name','0','0','','0','fn_on_change()');
	set_multiselect('cbo_working_company_name','0','0','','0');
	set_multiselect('cbo_gauge','0','0','','0');
	set_multiselect('cbo_buyer','0','0','','0');
	set_multiselect('cbo_brand_name','0','0','','0');
	
	
	
	
</script>

<?
		$sql=sql_select("select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name");
		$company_id='';
		$is_single_select=0;
		if(count($sql)==1){
			$company_id=$sql[0][csf('id')];
			$is_single_select=1;
			?>
			<script>
			console.log('shariar');
			set_multiselect('cbo_company_name','0','<? echo $is_single_select?>','<? echo $company_id?>','0');
			
			</script>
			
			<?
		}
		
		?>
</html>
