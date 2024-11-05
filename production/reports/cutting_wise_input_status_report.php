<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Report Will Create Cutting wise Input  Report.
Functionality	:	
JS Functions	:
Created by		:	Rehan Uddin
Creation date 	: 	07-04-2018
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
echo load_html_head_contents("Daily RMG Production Report", "../../", 1, 1,$unicode,'1','1');

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
 	

 	function fnc_load_report_format(data)
 	{
 		var report_ids = return_global_ajax_value( data, 'load_report_format', '', 'requires/cutting_wise_input_status_report_controller');
  		print_report_button_setting(report_ids);
 	}

 	function ClearTextBoxValues()
 	{
 		$("#cbo_buyer_name").val('');
	   // $("#cbo_job_year").val('');
	   $("#txt_style_ref_no").val('');
	   $("#txt_style_ref_id").val('');
	   $("#txt_style_ref").val('');
	   $("#txt_order_id_no").val('');
	   $("#txt_order_id").val('');
	   $("#txt_order").val('');
	   $("#txt_internal_ref").val('');
	   $("#txt_style_ref_number").val('');
	   
	}

 	function print_report_button_setting(report_ids)
 	{
 		if(trim(report_ids)=="")
		{
 			$("#show_button").show();
			$("#show_button2").show();
			$("#show_button1").show();
			$("#show_button3").show();
			$("#show_button4").show();
		}
		else
		{
			var report_id=report_ids.split(",");
			$("#show_button").hide();
			$("#show_button2").hide();
			$("#show_button1").hide();
			$("#show_button3").hide();
			$("#show_button4").hide();
			for (var k=0; k<report_id.length; k++)
			{
				if(report_id[k]==124)
				{
					$("#show_button").show();
				}
				else if(report_id[k]==125)
				{
					$("#show_button2").show();
				}
				else if(report_id[k]==126)
				{
					$("#show_button1").show();
				}
				if(report_id[k]==127)
				{
					$("#show_button3").show();
				}

				if(report_id[k]==128)
				{
					$("#show_button4").show();
				}
				
			}
		}
		

	}


			
	function fn_report_generated(type)
	{
		freeze_window(3);
		var company=$("#cbo_company_name").val();
 		var working_comp=$("#cbo_working_company_name").val();
		var job_po=$("#txt_job_po_style_no").val();
		if(company=="0" &&  working_comp=="0")
		{
			if (form_validation('cbo_company_name','Comapny/Working Name')==false)  
			{
				release_freezing();
				return;
			}

		}
		else if(!job_po)
		{
			if (form_validation('cbo_search_by*txt_job_po_style_no','search by*Job/style/po/cut')==false)  
			{
				release_freezing();
				return;
			}
		}
		 
		

		else
		{
			var data="action=report_generate"+"&type="+type+get_submitted_data_string('cbo_company_name*cbo_working_company_name*cbo_buyer_name*cbo_location*cbo_floor*cbo_job_year*cbo_search_by*txt_job_po_style_no',"../../");

			http.open("POST","requires/cutting_wise_input_status_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;

		}
		
		
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**");
			show_msg('3');
			release_freezing();
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			//append_report_checkbox('table_header_1',1);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		  
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
 		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$(htmlSearchValue).prependTo("table#table_body tbody");
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="425px";
	}


function openmypage_remarks(cut_no,action)
{
	 
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/cutting_wise_input_status_report_controller.php?cut_no='+cut_no+'&action='+action, 'Balance View', 'width=500px,height=300px,center=1,resize=0,scrolling=0','../');
}

 


 
	function dynamic_ttl_change(data)
	{
		var titles="";
		if(data==1)
		{
			titles="Job No";
		}
		else if(data==2)
		{
			titles="Style Ref."
		}
		else if(data==3)
		{
			titles="Po No.";
		}
		else if(data==4)
		{
			titles="Cut No.";
		}
		else if(data==5)
		{
			titles="Internal Ref.";
		}
		else
		{
			titles="Job No";
		}
		 
 		$("#dynamic_ttl").html(titles).css("color","blue");
	}
	 
</script>

</head>
 
<body onLoad="set_hotkey();">

<form id="dailyRmgProductionReport">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
         
         <h3 style="width:975px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:975px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
               <thead>                    
	               	<th width="130" class="must_entry_caption">Company Name</th>
	               	<th width="130" class="">Working Company</th>
	               	<th width="120">Location</th>
	               	<th width="110">Cut Floor</th>
	               	<th width="60">Cut. Year</th>
	               	<th width="120">Buyer Name</th>	               	
	               	<th width="100" class="must_entry_caption">Search Type</th> 
	               	<th width="100" id="dynamic_ttl" class="must_entry_caption">Job No</th>	                
 	               	<th width="90">
	               		<input type="reset" id="reset_btn" class="formbutton" style="width:90px; " value="Reset" onClick="reset_form('dailyRmgProductionReport','report_container*report_container2','','','')" />
	               	</th>
                 </thead>
                <tbody>
                <tr class="general">
                    <td> 
                        <?
                            echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/cutting_wise_input_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );	load_drop_down( 'requires/cutting_wise_input_status_report_controller', this.value, 'load_drop_down_location', 'location_td' )" );
                        ?>
                    </td>

                     <td> 
                        <?
                            echo create_drop_down( "cbo_working_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/cutting_wise_input_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );	load_drop_down( 'requires/cutting_wise_input_status_report_controller', this.value, 'load_drop_down_location', 'location_td' )" );
                        ?>
                    </td>

                    <td id="location_td">
                    	<? 
                            echo create_drop_down( "cbo_location", 120, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td id="floor_td" >
                    	<? 
                            echo create_drop_down( "cbo_floor", 110, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                        ?>
                    </td>
                     <td align="center">
					<?
                        $year_current=date("Y");
                        echo create_drop_down( "cbo_job_year", 60, $year,"", 1, "All",$year_current,'','');
                    ?>
                    </td>

                     
                    <td id="buyer_td">
                        <? 
                            echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                        ?>
                    </td>
                    
                   
                    <td>
                    	<?
                            $search_by_arr=[1=>"Job No",2=>"Style Ref.",3=>"Po No",4=>"Cut No",5=>"Internal Ref"];
                             echo create_drop_down( "cbo_search_by", 100, $search_by_arr,'',1, "-- Select--", 1,"dynamic_ttl_change(this.value);" );

                        ?>
                    </td>
                    <td><input type="text" style="width:100px" class="text_boxes"  name="txt_job_po_style_no" id="txt_job_po_style_no" /></td>

                     
                     
                    <td>
                        <input type="button" id="show_button" class="formbutton" style="width:90px;  " value="Show" onClick="fn_report_generated(0)"/>
                        
                        
                      
                    </td>
                </tr>
                </tbody>
            </table>
           
            <br />
        </fieldset>
    </div>
    </div>
        
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>	
	/*set_multiselect('cbo_company_name','0','0','0','0');
  	$("#multiselect_dropdown_table_headercbo_company_name a").click(function(){
		load_buyer_location();
 	});
	function load_buyer_location()
	{
		 

		var company=$("#cbo_company_name").val();
 		load_drop_down( 'requires/cutting_wise_input_status_report_controller',company, 'load_drop_down_buyer', 'buyer_td' );
		load_drop_down( 'requires/cutting_wise_input_status_report_controller', company, 'load_drop_down_location', 'location_td' )
	}*/
</script>

<script>
$('#cbo_location').val(0);
</script>
<!--<script class="include" type="text/javascript" src="../../js/chart/logic_chart.js"></script>-->
</html>
