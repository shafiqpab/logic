<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create .
Functionality	:	
JS Functions	:
Created by		:	Sohel 
Creation date 	: 	02-02-2014
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
echo load_html_head_contents("Manual Attendance Entry", "../", 1, 1,$unicode,'','');
?>	

<script>

var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
function fnc_employee_generated()
{
	if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false)
	{
		return;
	}
	var detls=$('#cbo_company_name').val()+"*"+$('#cbo_department').val()+"*"+$('#cbo_line').val()+"*"+$('#txt_emp_code').val()+"*"+$('#txt_date_from').val()+"*"+$('#txt_date_to').val();
	show_list_view(detls,'report_container','report_container1','requires/manual_attendance_entry_controller','');
	// $('#chk_all').removeAttr("checked");
	set_button_status(0, permission, 'fnc_manual_attendance_entry',1);
} 


function fnc_move_cursor(val,id, field_id,lnth,max_val)
{
	var str_length=val.length;
	
	if(str_length==lnth)
	{
		$('#'+field_id).select();
		$('#'+field_id).focus();
	}
	
	if(val>max_val)
	{
		document.getElementById(id).value=max_val;
	}
}




function fnc_intime_populate(val2,val1)
{
	var tot_row=$('#emp_tab tr').length;
	var intimeho=document.getElementById(val1).value;
	
	if(val2== '')
	{
		val2='00';
	}
	for(var i=1; i<=tot_row; i++)
	{
		if($("#txtintimehours_"+i).val()== '')
		{
			$("#txtintimehours_"+i).val(intimeho);
			$("#txtintimeminuties_"+i).val(val2);
		}
	}
}

function fnc_outtime_populate(val2,val1)
{
	var tot_row=$('#emp_tab tr').length;
	var outtimeho=document.getElementById(val1).value;
	
	if(val2== '')
	{
		val2='00';
	}
	
	for(var i=1; i<=tot_row; i++)
	{
		if($("#txtouttimehours_"+i).val()== '')
		{
			$("#txtouttimehours_"+i).val(outtimeho);
			$("#txtouttimeminuties_"+i).val(val2);
		}
	}
}



function fnc_manual_attendance_entry(operation)
	{
		var bgcolor='-moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%)';
		var tot_row=$('#emp_tab tr').length;
		var data1="action=save_update_delete&operation="+operation+"&tot_row="+tot_row+get_submitted_data_string('cbo_company_name*cbo_department*cbo_line',"../");
		var data2='';
		 for(var i=1; i<=tot_row; i++)
		  {
			  if (form_validation('txtintimehours_'+i+'*txtouttimehours_'+i,'In Time*Out Time')==false)
				{
					return;
				}
				
				if (!$('#txtouttimehours_'+i).val()=="" )
				{
					if (document.getElementById('chk_all_'+i).checked== false && $('#txtintimehours_'+i).val()*1 > $('#txtouttimehours_'+i).val()*1 )
					{
							document.getElementById('txtouttimehours_'+i).style.backgroundImage=bgcolor;
							alert ('Out-time can not be less than in-time.');
							return;
					}
				}

				data2+=get_submitted_data_string('txtempcode_'+i+'*txtattdate_'+i+'*txtintimehours_'+i+'*txtintimeminuties_'+i+'*txtouttimehours_'+i+'*txtouttimeminuties_'+i+'*updateid_'+i+'*chk_all_'+i,"../");
		  }
		var data=data1+data2;
		//alert(data);
		freeze_window(operation);
		
		http.open("POST","requires/manual_attendance_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_manual_attendance_Reply_info;
	}
	
	function fnc_manual_attendance_Reply_info()
	{
		if(http.readyState == 4) 
		{ 
			//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');	
			show_msg(reponse[0]);
			fnc_employee_generated();
			//set_button_status(0, permission, 'fnc_manual_attendance_entry',1);
			release_freezing();	
		}
	}
	
	function openmypage_employee()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var page_link='requires/manual_attendance_entry_controller.php?action=employee_search_popup&companyID='+companyID;
		var title='Employee Info';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=830px,height=370px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			//var po_id=this.contentDoc.getElementById("hide_po_id").value;
			var emp_code=this.contentDoc.getElementById("hide_emp_code").value;
			
			$('#txt_emp_code').val(emp_code);
			//$('#hide_order_id').val(po_id);	 
		}
	}
	
	
	var selected_id = new Array ;var selected_date = new Array();
	
	function checkAll(field)
	{
		if ($('#chk_all').val()==0)
		{	
			$('#chk_all').val('1');
			for (i = 1; i <= document.getElementsByName('chk_all').length-1; i++)
			{	
				$('#chk_all_'+i).attr('checked', 'checked');
				js_set_value( i );
				$('#chk_all_'+i).val('1');
				//var new_date=add_days($('#chk_all_'+i).val(),1);
				//$('#txtattdate_'+i).val(new_date)
				//alert (add_days($('#chk_all_'+i).val(),1));
				//add_days($('#chk_all_'+i).val(),1);
			}
		}
		else
		{
			$('#chk_all').val('0');
			for (i = 1; i <= document.getElementsByName('chk_all').length-1; i++)
			{
				$('#chk_all_'+i).removeAttr("checked");
				js_set_value( i );
				$('#chk_all_'+i).val('0');				
			}
		}
	}
	
	function js_set_value( str ) 
	{
		//alert (str);
		if( jQuery.inArray( str, selected_id ) == -1 ) 
		{
			selected_id.push( str );
			$('#chk_all_'+str).val('1');
			//alert ('ssss');
		}
		else 
		{
			for( var i = 0; i < selected_id.length; i++ ) 
			{
				if( selected_id[i] == str ) break;
			}
			selected_id.splice( i, 1 );
			$('#chk_all_'+str).val('0');
			//alert ('kkkk');
		}
		var id = '';
		for( var i = 0; i < selected_id.length; i++ ) 
		{
			id += selected_id[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		$('#selected_id_val').val( id );
	}
	
	
	function check_box_val_reset()
	{
		$('#chk_all').val('0');
	} 
	
/*		
function js_set_value(id)
{
	var str=id.split("_");
	
		if( jQuery.inArray(  str[0] , selected_id ) == -1) {
			
			selected_id.push( str[0] );
			selected_date.push( str[1] );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
			if( selected_id[i] == str[0]  ) break;
		}
			selected_id.splice( i, 1 );
			selected_date.splice( i, 1 );
		}
		var id = ''; var attdate = '';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			attdate += selected_date[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		attdate = attdate.substr( 0, attdate.length - 1 );
		
		//$('#selected_order_id').val( id );
		$('#selected_id_val').val( attdate );
	
}
*/



</script>

</head>
 
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center"> 
        <? echo load_freeze_divs ("../",'');  ?>
         <fieldset style="width:950px;">
        <legend>Production Attendance Entry</legend>  
         <form name="productionattendance_1" id="productionattendance_1" autocomplete="off">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" align="center"><input type="hidden" name="selected_id_val" id="selected_id_val" />
               <thead>                    
                        <th width="150">Company Name</th>
                        <th width="110">Department</th>
                        <th width="110">Line</th>
                        <th width="100">Emp Code</th>
                        <th width="200">Entry Date</th>
                        <th width="110"><input type="button" id="reset_btn" class="formbutton" style="width:100px" value="Reset" onClick="reset_form('','','cbo_department*cbo_line*txt_emp_code*txt_date_from*txt_date_to','','')" /></th>
                 </thead>
                <tbody>
                <tr >
                    <td> 
                        <?
						if($db_type==0) $m_null="company_name!=''";
						if($db_type==2) $m_null="company_name!='0' ";
						
							  
						//echo "select distinct  company_name from lib_employee comp where status_active =1 and is_deleted=0 and $m_null order by company_name";
                            echo create_drop_down( "cbo_company_name", 150, "select distinct  company_name from lib_employee comp where status_active =1 and is_deleted=0 and $m_null order by company_name","company_name,company_name", 1, "-- Select Company --", 0, "" );
                        ?>
                    </td>
                     <td>
                    	<? 
						if($db_type==0) $d_null="department_name!='' ";
						if($db_type==2) $d_null="department_name!='0' ";
						
						echo create_drop_down( "cbo_department", 110,"Select distinct  department_name from lib_employee where $d_null order by department_name","department_name,department_name", 1, "-- Select --", 0, "",0,"" );
                        ?>
                    </td> 
                    <td>
                    	<?
						if($db_type==0) $l_null="line_name!='' ";
						if($db_type==2) $l_null="line_name!='0' ";
						
                        echo create_drop_down( "cbo_line", 110, "Select distinct line_name from  lib_employee where  $l_null order by line_name","line_name,line_name", 1, "-- Select --", 0, "",'',"" );
                        ?>
                    </td> 
                      
                    <td>
                    	<input name="txt_emp_code" id="txt_emp_code" class="text_boxes" style="width:150px" placeholder="Double Click To Search" onDblClick="openmypage_employee();"  >
                    </td>
                    <td align="center">
                         <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:80px" placeholder="From Date"/>
                         &nbsp;To&nbsp;
                         <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:80px" placeholder="To Date"/>
                    </td>
                    <td>
                        <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fnc_employee_generated()" />
                    </td>
                </tr>
        </tbody>
        </table>
           <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" align="center">      
                <tr  align="center">
                	<td colspan="5" align="center">
                        <table class="rpt_table" width="50%" cellspacing="1" align="center">
                            <thead>
                                <th width="150">In Time</th>				    
                                <th width="150">Out Time</th>	
                                <th width="100">Next Day</th>				                     
                            </thead>
                            <tbody>
                            <tr align="center" >
                                <td width="150">
                                    <input type="text" name="txt_in_time_hours" id="txt_in_time_hours" class="text_boxes_numeric" placeholder="HH"  style="width:20px;"  onKeyUp="fnc_move_cursor(this.value,'txt_in_time_hours','txt_in_time_minuties',2,23);" /> :
                                    <input type="text" name="txt_in_time_minuties" id="txt_in_time_minuties" class="text_boxes_numeric" placeholder="MM"  style="width:20px;" onKeyUp="fnc_move_cursor(this.value,'txt_in_time_minuties','txt_in_time_seconds',2,59)" onBlur="fnc_intime_populate(this.value,'txt_in_time_hours')" />
                                              
                                </td>
                                <td width="150">
                                    <input type="text" name="txt_out_time_hours" id="txt_out_time_hours" class="text_boxes_numeric" placeholder="HH"  style="width:20px;"  onKeyUp="fnc_move_cursor(this.value,'txt_out_time_hours','txt_out_time_minuties',2,23);" /> :
                                    <input type="text" name="txt_out_time_minuties" id="txt_out_time_minuties" class="text_boxes_numeric" placeholder="MM"  style="width:20px;"  onKeyUp="fnc_move_cursor(this.value,'txt_out_time_minuties','txt_out_time_seconds',2,59)" onBlur="fnc_outtime_populate(this.value,'txt_out_time_hours')"/> 
                                   
                                </td>
                                <td width="100"><input type="checkbox" name="chk_all" id="chk_all" value="0" onClick="checkAll( this.value )" /></td>
                                
                            </tr>
                            </tbody>
                        </table>
                    </td>
                    <td width="250" style="font-style:italic; color:#FF0000;"> Note : 24 hour format has been considerd.</td>
                </tr>
                <tr>
                    <td colspan="6">
                    <div id="report_container1"></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" align="center" class="button_container">
						<? 
							echo load_submit_buttons('',"fnc_manual_attendance_entry",0,0,"reset_form('','report_container1','cbo_department*cbo_line*txt_emp_code*txt_date_from*txt_date_to*txt_in_time_hours*txt_in_time_minuties*txt_out_time_hours*txt_out_time_minuties*chk_all','','check_box_val_reset()','')",1);
                        ?>
                    </td>
                </tr>
            </table>
 </form>
 </fieldset>
 </div>  
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>

<script>
$( ".combo_boxes" ).each(function( index ) {

	if(  this.id !='cbo_company_name')
			$('#'+this.id).val($('#'+this.id+' option:first').val());

});
</script>
</html>
