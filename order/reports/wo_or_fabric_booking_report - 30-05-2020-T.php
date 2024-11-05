<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Work Order [Booking] Report.
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	29-12-2013
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
echo load_html_head_contents("Work Order [Booking] Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';	

	var tableFilters = 
	{
		col_23: "none",
		col_24: "none",
		col_6: "select",
		col_operation: {
		id: ["value_tot_fin_fab_qnty"],
		col: [21],
		operation: ["sum"],
		write_method: ["innerHTML"]
		}
	} 
	
	var tableFilters1 = 
	{
		col_21: "none",
		col_6: "select",
		col_operation: {
		id: ["value_tot_fin_fab_qnty","value_tot_grey_fab_qnty"],
		col: [21,22],
		operation: ["sum","sum"],
		write_method: ["innerHTML","innerHTML"]
		}
	}
	var tableFilters2 = 
	{
		col_21: "none",
		col_6: "select",
		col_operation: {
		id: ["value_tot_fin_fab_qnty","value_tot_grey_fab_qnty"],
		col: [22,23],
		operation: ["sum","sum"],
		write_method: ["innerHTML","innerHTML"]
		}
	} 

	function openmypage_job()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		else
		{	
			var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#cbo_job_year_id").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/wo_or_fabric_booking_report_controller.php?data='+data+'&action=job_no_popup', 'Job No Search', 'width=700px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_job_id");
				var response=theemailid.value.split('_');
				if ( theemailid.value!="" )
				{
					//alert (response[0]);
					freeze_window(5);
					$("#hidd_job_id").val(response[0]);
					$("#txt_job_no").val(response[1]);
					release_freezing();
				}
			}
		}
	}
	
	function openmypage_wo()
	{
		if(form_validation('cbo_company_id*cbo_category_id','Company Name*Item Category')==false)
		{
			return;
		}
		else
		{	
			var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#cbo_year_id").val()+"_"+$("#cbo_category_id").val()+"_"+$("#cbo_wo_type").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/wo_or_fabric_booking_report_controller.php?data='+data+'&action=wo_no_popup', 'Wo No Search', 'width=650px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_wo_id");
				var theemailval=this.contentDoc.getElementById("txt_wo_no");
				//var response=theemailid.value.split('_');
				if ( theemailval.value!="" )
				{
					//alert (response[0]);
					freeze_window(5);
					$("#hidd_wo_id").val(theemailid.value);
					$("#txt_wo_no").val(theemailval.value);
					release_freezing();
				}
			}
		}
	}
		
	function openmypage_po()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		else
		{	
			var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#txt_job_no").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/wo_or_fabric_booking_report_controller.php?data='+data+'&action=po_no_popup', 'PO No Search', 'width=700px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_po_id");
				var theemailval=this.contentDoc.getElementById("txt_po_val");
				if (theemailid.value!="" || theemailval.value!="")
				{
					//alert (theemailid.value);
					freeze_window(5);
					$("#hidd_po_id").val(theemailid.value);
					$("#txt_po_no").val(theemailval.value);
					release_freezing();
				}
			}
		}
	}
	
	function fn_report_generated(operation)
	{
			
			var  txt_date_from= $("#txt_date_from").val();
			var  txt_date_to= $("#txt_date_to").val();
			if(txt_date_to!='' &&  txt_date_from!="")
			{
					if(form_validation('cbo_category_id','Item Category')==false)
					{
						return;
					}
			}
			else
			{
				if(form_validation('cbo_company_id*cbo_category_id','Company Name*Item Category')==false)
				{
					return;
				}
			}
		
			
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_category_id*cbo_wo_type*cbo_year_id*txt_wo_no*hidd_wo_id*cbo_job_year_id*txt_job_no*hidd_job_id*hidd_po_id*txt_date_from*txt_date_to*txt_internal_ref*txt_file_no*txt_date_category*cbo_fabric_source*cbo_order_status*cbo_season_id',"../../")+'&report_title='+report_title;
			freeze_window(3);
			http.open("POST","requires/wo_or_fabric_booking_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**");
			//var tot_rows=reponse[2];
			$('#report_container4').html(reponse[0]);
			//document.getElementById('report_container3').innerHTML=report_convert_button('../../');
			document.getElementById('report_container3').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
	 		show_msg('3');
			
			var cat_id=$("#cbo_category_id").val();
			if(cat_id==2 || cat_id==3 || cat_id==24 || cat_id==25)
			{
				setFilterGrid("table_body",-1,tableFilters1);
			}
			else if(cat_id==12)
			{
				setFilterGrid("table_body",-1,tableFilters2);
			}
			else if(cat_id==4)
			{
				setFilterGrid("table_body",-1,tableFilters);
			}
			release_freezing();
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$("#table_body tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container4').innerHTML+'</body</html>');
		d.close(); 
		$("#table_body tr:first").show();
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";
	}
	
	function openmypage_job_color_size(page_link,title)
	{
		//alert("monzu");
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
		}
	}
	
	function generate_fabric_report(type,booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved,category,is_short,emb_name,item_number_id,action_type,i,supplier_id,cbo_level)
	{
		//alert(category+"=="+type);return;
		if(category==2 || category==3)//Knit Finish Fabrics/Woven
		{
			if(type==4)//sample booking without order
			{
				var data="action=show_fabric_booking_report"+
							'&txt_booking_no='+"'"+booking_no+"'"+
							'&cbo_company_name='+"'"+company_id+"'"+
							'&id_approved_id='+"'"+approved+"'";	
							
				
				http.open("POST","../woven_order/requires/sample_booking_non_order_controller.php",true);			
			}
			else
			{ 
				var data="action="+action_type+
				'&txt_booking_no='+"'"+booking_no+"'"+
				'&cbo_company_name='+"'"+company_id+"'"+
				'&txt_order_no_id='+"'"+order_id+"'"+
				'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
				'&cbo_fabric_source='+"'"+fabric_source+"'"+
				'&id_approved_id='+"'"+approved+"'"+
				'&txt_job_no='+"'"+job_no+"'";
				'&path=../../';
				if(type==1)	//short fabric booking
				{			
					http.open("POST","../woven_order/requires/short_fabric_booking_controller.php",true);
				}
				else if(type==2) //main fabric booking
				{
					http.open("POST","../woven_order/requires/fabric_booking_controller.php",true);
				}
				else if((type==73) || (type==93 || type==269 || type==28 || type==45 || type==53 || type==93  || type==193)) //main fabric booking v2
				{
					http.open("POST","../woven_order/requires/fabric_booking_urmi_controller.php",true);
				}
				else if(type==271) //woven Partial main fabric booking
				{
					http.open("POST","../woven_gmts/requires/partial_fabric_booking_controller.php",true);
				}
				else if(type==108) //Partial main fabric booking
				{
					//alert(type);
					http.open("POST","../woven_order/requires/partial_fabric_booking_controller.php",true);
				}
				else if(type==140) //Sample fabric booking Req without order
				{
					//alert(type);
					http.open("POST","../woven_order/requires/sample_requisition_booking_non_order_controller.php",true);
				}
				else if(type==139) //Sample fabric booking Req with order
				{
					//alert(type);
					http.open("POST","../woven_order/requires/sample_requisition_booking_with_order_controller.php",true);
				}
				else if(type==90) //Sample fabric booking withOut order
				{
					//alert(type);
					http.open("POST","../woven_order/requires/sample_booking_non_order_controller.php",true);
				}
				else if(type==89) //Sample fabric booking with order
				{
					//alert(type);
					http.open("POST","../woven_order/requires/sample_booking_controller.php",true);
				}
				else if(type==88) //Short fabric booking 
				{
					//alert(type);
					http.open("POST","../woven_order/requires/short_fabric_booking_controller.php",true);
				}
				else  
				{
					http.open("POST","../woven_order/requires/sample_booking_controller.php",true);
				}
			}
		}
		else if(category==4) //Accessories
		{
			if(type==87 || type==272 || type==43 || type==262) // trims_booking_multi_job_controllerurmi; entry_form=87
			{
				var show_comment='';
				var r=confirm("Press  \"Cancel\"  to hide  Remarks\nPress  \"OK\"  to Show Remarks");
				if (r==true){
					show_comment="1";
				}
				else{
					show_comment="0";
				}
			
				var data="action="+action_type+
					'&txt_booking_no='+"'"+booking_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&id_approved_id='+"'"+approved+"'"+
					'&cbo_isshort='+"'"+is_short+"'"+
					'&cbo_level='+"'"+cbo_level+"'"+
					'&show_comment='+"'"+show_comment+"'"+
					'&report_title= Multiple Job Wise Trims Booking V2 '+
					'&report_type=1&path=../../';
							
				if(type==87)
				{
					http.open("POST","../woven_order/requires/trims_booking_multi_job_controllerurmi.php",true);	
				}
				else if(type==43)
				{
					http.open("POST","../woven_order/requires/trims_booking_controller2.php",true);	
				}
				else if(type==262)
				{
					http.open("POST","../woven_order/requires/short_trims_booking_multi_job_controllerurmi.php",true);	
				}
				else
				{
					http.open("POST","../woven_gmts/requires/trims_booking_multi_job_controllerurmi.php",true);
				}			
			}
			else if(type==4) // trims sample booking without order
			{
				var data="action=show_fabric_booking_report"+
							'&txt_booking_no='+"'"+booking_no+"'"+
							'&cbo_company_name='+"'"+company_id+"'"+
							'&id_approved_id='+"'"+approved+"'";	
							
				http.open("POST","../woven_order/requires/trims_sample_booking_without_order_controller.php",true);			
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
					var data="action="+action_type+
							'&txt_booking_no='+"'"+booking_no+"'"+
							'&cbo_company_name='+"'"+company_id+"'"+
							'&id_approved_id='+"'"+approved+"'"+
							'&cbo_isshort='+"'"+is_short+"'"+
							'&show_comment='+"'"+show_comment+"'"
							'&path=../../';
							//alert(data);
				if(type==1)	//short trim booking
				{
					//alert(type);			
					http.open("POST","../woven_order/requires/short_trims_booking_controller.php",true);
				}
				else if(type==2) // main trim booking
				{ 
					http.open("POST","../woven_order/requires/trims_booking_controller_v2.php",true);
				}
				else // sample trim booking
				{
					http.open("POST","../woven_order/requires/trims_sample_booking_with_order_controller.php",true);
				}
			}
		}
		else if(category==12) //Services - Fabric
		{
			if(action='show_trim_booking_report2')
			{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to Show  Rate and Amount\nPress  \"OK\"  to Hide Rate and Amount");
			if (r==true)
			{
				show_rate="1";
			}
			else
			{
				show_rate="0";
			}
			}
			
			var data="action="+action+
					'&txt_booking_no='+"'"+booking_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&id_approved_id='+"'"+approved+"'"+
					'&show_rate='+"'"+show_rate+"'"+
					'&cbo_isshort='+"'"+is_short+"'";
				if(type==2) // service booking main
				{
					http.open("POST","../woven_order/requires/service_booking_controller.php",true);
				}
				else if(type==163 || type==164) // service booking main
				{
					http.open("POST","../woven_order/requires/service_booking_aop_urmi_controller.php",true);
				}
		}
		else if(category==24) //Services - Yarn Dyeing
		{
			var data="action=show_trim_booking_report"+
						'&txt_booking_no='+"'"+booking_no+"'"+
						'&cbo_company_name='+"'"+company_id+"'"+
						'&id_approved_id='+"'"+approved+"'"+
						'&cbo_isshort='+"'"+is_short+"'";
			if(type==2) // yarn dyeing charge booking
			{
				http.open("POST","../woven_order/requires/yarn_dyeing_charge_booking_controller.php",true);
			}
		}
		else if(category==25) //Services - Embellishment
		{
			//var supplier_name=0;
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
			
			
			if(type==201) // Multiple Job Wise Embellishment Work Order
			{var data="action=show_trim_booking_report2"+
							'&txt_booking_no='+"'"+booking_no+"'"+
							'&txt_job_no='+"'"+job_no+"'"+
							'&cbo_company_name='+"'"+company_id+"'"+
							'&txt_order_no_id='+"'"+order_id+"'"+
							'&cbo_supplier_name='+"'"+supplier_id+"'"+
							'&cbo_booking_natu='+"'"+emb_name+"'"+
							'&cbo_gmt_item='+"'"+item_number_id+"'"+
							'&show_comment='+"'"+show_comment+"'"+
							'&cbo_template_id=1'+
							'&report_title= Multiple Job Wise Embellishment Work Order '+
							'&id_approved_id='+"'"+approved+"'"
							'&path=../../';
				http.open("POST","../woven_order/requires/print_booking_multijob_controller.php",true);
			}				
			else // main print booking
			{
				var data="action=show_trim_booking_report1"+
							'&txt_booking_no='+"'"+booking_no+"'"+
							'&txt_job_no='+"'"+job_no+"'"+
							'&cbo_company_name='+"'"+company_id+"'"+
							'&txt_order_no_id='+"'"+order_id+"'"+
							'&cbo_supplier_name='+"'"+supplier_id+"'"+
							'&cbo_booking_natu='+"'"+emb_name+"'"+
							'&cbo_gmt_item='+"'"+item_number_id+"'"+
							'&show_comment='+"'"+show_comment+"'"+
							'&cbo_template_id=1'+
							'&id_approved_id='+"'"+approved+"'";
				http.open("POST","../woven_order/requires/print_booking_controller.php",true);
			}
		}
		
		else if(category==31) //Services - Embellishment
		{	
			var report_title='Lab Test Work Order';
			var data="action=show_trim_booking_report"+
							'&data='+"'"+company_id+'*'+booking_no+'*'+order_id+'*'+fabric_source+'*'+report_title+"'";
				http.open("POST","../woven_order/requires/labtest_work_order_controller.php",true);
		
		}
		
		
				
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function(){
			if(http.readyState == 4) 
			{
				var file_data=http.responseText.split('****');
				if(type==272){var html=file_data[1];}
				else{var html=file_data[0];}
				
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+html+'</body</html>');
				d.close();
			}
			
		}
	}

	
	
	function generate_fabric_booking_report(txt_booking_no,action)// Report here
	{ 
		var data="action="+action+'&txt_booking_no='+txt_booking_no;
		http.open("POST","../woven_order/requires/print_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_trim_report_reponse;	
	}
	
	function generate_trim_report_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert( http.responseText);return;
			$('#data_panel').html( http.responseText );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
		}
	}	

</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission); ?>    		 
    <form name="wofbreport_1" id="wofbreport_1" autocomplete="off" > 
    <h3 style="width:1250px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" >
            <fieldset style="width:1250px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>   
                    <th width="100" class="must_entry_caption">Item Category</th>
                    <th width="80">Wo Type</th>
                    <th width="120" class="must_entry_caption">Company Name</th>
                    <th width="100">Buyer</th>
                     <th width="100">Season</th>
                    <th width="60">Wo Year</th>
                    <th width="75">Wo No.</th>
                    <th width="60">Job Year</th>
                    <th width="75">Job No.</th>
                    <th width="75">PO No.</th>
                    <th width="70">Internal Ref.</th>
                    <th width="70">File No</th>
                    <th width="120" colspan="2">Date</th>
                    <th width="80">Date Category</th>
                    <th width="80">Fabric Source</th>
                    <th>Order Status</th>
                </thead>
                 <tbody>
                    <tr class="general">
                        <td><? echo create_drop_down( "cbo_category_id", 100, $item_category,"", 1, "--Select Category--", $selected, "",0,"2,3,4,12,24,25,31","" ); ?></td>
                        <td>
							<? 
								$wo_type=array(1=>"Short",2=>"Main",3=>"Sample With Order",4=>"Sample Non Order");
								echo create_drop_down( "cbo_wo_type", 80, $wo_type,"", 1, "--All--", $selected, "",0,"","" );
                            ?>
                        </td>
                        <td><? echo create_drop_down( "cbo_company_id", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select Company-", $selected, "load_drop_down( 'requires/wo_or_fabric_booking_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?> </td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_id", 100, $blank_array,"", 1, "-Select Buyer-", $selected, "",1,"" ); ?></td>
                        <td id="season_td"><? echo create_drop_down( "cbo_season_id", 100, $blank_array,"", 1, "-Select Season-", $selected, "",1,"" ); ?></td>
                        <td><? echo create_drop_down( "cbo_year_id", 60, $year,"", 1, "-Year-", date("Y"), "",0,"","" ); ?></td>
                        <td>
                            <input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:65px" placeholder="Browse" onDblClick="openmypage_wo();" readonly />
                            <input type="hidden" id="hidd_wo_id" name="hidd_wo_id" style="width:50px" />
                        </td>
                        <td><? echo create_drop_down( "cbo_job_year_id", 60, $year,"", 1, "-Year-", date("Y"), "",0,"","" ); ?></td>
                        <td>
                            <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:65px" placeholder="Double Click" onDblClick="openmypage_job();" readonly />
                            <input type="hidden" id="hidd_job_id" name="hidd_job_id" style="width:50px" />
                        </td>
                        <td>
                            <input type="text" name="txt_po_no" id="txt_po_no" class="text_boxes" style="width:65px" placeholder="Double Click" onDblClick="openmypage_po();" readonly />
                            <input type="hidden" id="hidd_po_id" name="hidd_po_id" style="width:50px" />
                        </td>
                        
                        <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:60px"></td>
                      	<td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:60px"></td>
                         
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" ></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To Date" ></td>
                        <td>
							<? 
								$date_cat=array(1=>'Booking Date',2=>'Delivery Date');
								$selected_year=1;
								echo create_drop_down( "txt_date_category", 80, $date_cat,"", 0, "--All--", 1, "",0,"","" );
                            ?>
                        </td>
                        <td><? echo create_drop_down( "cbo_fabric_source", 80, $fabric_source,"", 1, "-- Select --", "","", "", ""); ?></td>
                        <td><? echo create_drop_down( "cbo_order_status", 70, $row_status,"", 0, "", 1,"", "", "");	?></td>
                    </tr>
                    <tr>
                        <td colspan="16" align="center">
                            <? echo load_month_buttons(1); ?>
                            &nbsp;&nbsp; &nbsp;&nbsp;
                            <input type="button" id="show_button" class="formbutton" style="width:90px" value="Show" onClick="fn_report_generated(0)" />
                            <input type="reset" id="reset_btn" class="formbutton" style="width:90px" value="Reset" onClick="reset_form('wofbreport_1','report_container3*report_container4','','','')" />
                        </td>
                    </tr>
                </tbody>
            </table> 
        	</fieldset>
        </div>
        <div id="report_container3" align="center"></div>
        <div id="report_container4" align="left"></div>
    </form> 
</div>
   <div style="display:none" id="data_panel"></div>
</body>
<script>//set_multiselect('cbo_wo_type','0','0','','0');</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
