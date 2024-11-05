<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Style Wise Budget Report.
Functionality	:	
JS Functions	:
Created by		:	Md. Helal Uddin
Creation date 	: 	23-06-2021
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
echo load_html_head_contents("Fabric and GMTS Production Follow-up Report", "../../../", 1, 1,$unicode,1,1);
?>	

<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	var permission = '<? echo $permission; ?>';
	 	
	 

	function generate_report(type)
	{
		
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var txt_date_from=$("#txt_date_from").val();
		var txt_date_to=$("#txt_date_to").val();
		var txt_job_no=$("#txt_job_no").val();
		var txt_style_ref=$("#txt_style_ref").val();
		var txt_int_ref=$("#txt_int_ref").val();
		var cbo_string_search_type=$("#cbo_string_search_type").val();
		//console.log(hidden_order_id);
		//console.log(txt_order_no);

		if((txt_job_no.length==0 ) && (txt_date_from.length==0 || txt_date_to.length==0) && (txt_style_ref.length==0) && ( txt_int_ref.length==0))
		{
			alert('Job or Order or Date Mandatory');
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*cbo_year_selection*cbo_year*txt_style_ref*txt_int_ref*txt_week*txt_date_from*txt_date_to*txt_int_ref*cbo_season*cbo_string_search_type',"../../../")+'&report_title='+report_title+'&type='+type;
		freeze_window(3);
		http.open("POST","requires/order_focusing_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
			show_msg('3');
			release_freezing();
			$("#report_container2").html(reponse[0]);
			 var overflow_div = document.getElementById("overflow_div");
     		 var clientHeight = overflow_div.clientHeight;
     		 if(clientHeight>=400)
     		 {
     		 	overflow_div.style.marginLeft ="17px";
     		 }
      		
			var cnt=reponse[2];
			var id_name='';
			var col_name='';
			var operation_name='';
			var method_name='';
			for(var i=1;i<=cnt;i++)
			{
				if(i>1)
				{
					operation_name+=',';
					id_name+=',';
					col_name+=',';
					method_name+=',';
				}
				var nm="fin_"+i;
				id_name+='"'+nm+'"';
				var n=Number(Number(i)+Number(cnt)+Number(16));
				col_name+=""+n;
				operation_name+='"sum"';
				method_name+='"innerHTML"';
			}
			for(var i=1;i<=cnt;i++)
			{
				if(i>1)
				{
					operation_name+=',';
					id_name+=',';
					col_name+=',';
					method_name+=',';
				}
				var nm="grey_"+i;
				id_name+='"'+nm+'"';
				var n=Number(Number(i)+Number(cnt)+Number(cnt)+Number(16));
				col_name+=""+n;
				operation_name+='"sum"';
				method_name+='"innerHTML"';
			}
			
			console.log(id_name);console.log(col_name);console.log(operation_name);console.log(method_name);
			var tableFilters =
            {
                //col_0: "none",col_5: "none",col_5: "none",col_21: "none",col_29: "none",
               
                 col_operation: {
                    id: ["total_order_qnty","total_plan_cut",id_name],
                    col: [13,14,col_name],
                    operation: ["sum","sum",operation_name],
                    write_method: ["innerHTML","innerHTML",method_name]
                }
            }  
            setFilterGrid("scroll_body",-1,tableFilters);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

			
		} 

	} 
	

	function new_window()
	{
		const el = document.querySelector('#scroll_body');
		  if (el) {
		    document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none"; 
			$("#scroll_body tr:first").hide();

		}
		
		//$(".flt").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		 if (el) {
		    document.getElementById('scroll_body').style.overflowY="auto"; 
			document.getElementById('scroll_body').style.maxHeight="400px";
			$("#scroll_body tr:first").show();

		}
		
		//$(".flt").show();
	}
	
	function set_week_date()
	{
		var week=document.getElementById('txt_week').value*1;
		var year=document.getElementById('cbo_year_selection').value;
	
		if(week){
			
			$('.month_button').attr('disabled','true');
			$('.month_button_selected').attr('disabled','true');
			$('#txt_date_from').attr('disabled','true');
			$('#txt_date_to').attr('disabled','true');
			var week_date=return_global_ajax_value(week+"_"+year, 'week_date', '', 'requires/weekly_capacity_and_order_booking_status_controller');
			var week_date_arr=week_date.split('_');
			document.getElementById('txt_date_from').value=week_date_arr[0];
			document.getElementById('txt_date_to').value=week_date_arr[1];
		}else{
			$('.month_button').removeAttr('disabled');
			$('.month_button_selected').removeAttr('disabled');
			$('#txt_date_from').removeAttr('disabled');
			$('#txt_date_to').removeAttr('disabled');
		}
	}

	function generate_worder_report(txt_booking_no,cbo_company_name,txt_order_no_id,cbo_fabric_natu,cbo_fabric_source,txt_job_no,id_approved_id,print_id,entry_form,type,i,fabric_nature)
	{
		
		
		var show_yarn_rate='';
		var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
		
		if (r==true) show_yarn_rate="1"; else show_yarn_rate="0";
		
		var report_title="";
	
		report_title='Main Fabric Booking';
		
		var data="action="+type+
		'&txt_booking_no='+"'"+txt_booking_no+"'"+
		'&cbo_company_name='+"'"+cbo_company_name+"'"+
		'&txt_order_no_id='+"'"+txt_order_no_id+"'"+
		'&cbo_fabric_natu='+"'"+cbo_fabric_natu+"'"+
		'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+
		'&id_approved_id='+"'"+id_approved_id+"'"+
		'&report_title='+report_title+
		'&txt_job_no='+"'"+txt_job_no+"'"+
		'&show_yarn_rate='+show_yarn_rate+
		'&path=../../../';
			
		freeze_window(5);
		//alert(entry_form);
		
	
		http.open("POST","../../../order/woven_order/requires/fabric_booking_urmi_controller.php",true);
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
		    {
				var w = window.open("Surprise", "_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				d.close();
				release_freezing();
		   }
		}
	}
	 		 	 
</script>

</head>
 
<body onLoad="set_hotkey();">
  <div style="width:100%;" align="center"> 
   <? echo load_freeze_divs ("../../../",'');  ?>
    <h3 style="width:1310px;  margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div style="width:100%;" align="center" id="content_search_panel">
    <form id="dateWiseProductionReport_1">    
      <fieldset style="width:1310px;">
            <table class="rpt_table" width="1300px" cellpadding="0" cellspacing="0" align="center">
             <thead>
                <th colspan="10" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </thead>
               <thead>                    
                       <tr>
                        <th class="must_entry_caption" width="150">Company Name</th>
                        
                        <th width="150" >Buyer Name</th>
                        <th width="65" >Job Year</th>
                        <th width="100">Job No</th>
                        <th width="100">Style Ref.</th>
                        <th width="100">Internal Ref.</th>
                        <th width="110">Week</th>
                      	<th width="70">Season</th>
                     
                      
                        
                        <th class="must_entry_caption" width="150" colspan="2" > <span id="search_by_td_up">Pub. Shipment Date</span> </th>
                      
                        <th width="180"><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" onClick="reset_form()"/></th>
                    </tr>   
              </thead>
                <tbody>
                <tr class="general">
                    <td width="140"> 
                        <?
                            echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/order_focusing_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                        ?>
                    </td>
                    
                    <td width="120" id="buyer_td">
                        <? 
                            echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td><? echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", date('Y'), "",0,"" ); ?></td>
                    <td width="">
                       <input type="text" id="txt_job_no"  name="txt_job_no"  style="width:100px" class="text_boxes"  placeholder="Write" />
                       <input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
                       <!-- onDblClick="open_job_no();$('#hidden_job_id').val('');$('#txt_job_no').val('');" -->
                    </td>
                    <td >
                    	 <input type="text" id="txt_style_ref"  name="txt_style_ref"  style="width:100px" class="text_boxes"  placeholder="Write" />
                    </td>
                    <td >
                    	 <input type="text" id="txt_int_ref"  name="txt_int_ref"  style="width:100px" class="text_boxes"  placeholder="Write" />
                    </td>
                    
                   
                    <td >
                     

                       <?
						$weekArr=array();
						$sql=sql_select("select id,week from week_of_year  where year=".date("Y"));
						foreach($sql as $row){
							$weekArr[$row[csf('week')]]="Week-".$row[csf('week')];
						}
                                echo create_drop_down( "txt_week", 80, $weekArr,"", 1, "-- Select --", $selected, "set_week_date()"  );
					    ?>
                       
                        
                    </td>

                    <td id="season_td"><?=create_drop_down( "cbo_season", 70, "select id, season_name from lib_buyer_season where  status_active =1 and is_deleted=0  order by season_name ASC","id,season_name", 1, "-Season-", "", "" ); ?></td>
                  
                    
                   
                    
                     <td  colspan="2">
                     	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" >
                     	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date"  >
                     </td>
                   
                   
                  
                    <td width="180">
                        <input type="button" id="show_button" class="formbutton" style="width:80px;" value="Show" onClick="generate_report(1)" />
                        
                    </td>
                </tr>
                </tbody>
            </table>
            <table>
            	<tr>
                	<td>
 						<? echo load_month_buttons(1); ?>
                   	</td>
                </tr>
            </table> 
      </fieldset>
      <div   id="report_container" align="center"></div>
      <div id="report_container2"></div>  
 </form> 
 </div>   
</body>

</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
