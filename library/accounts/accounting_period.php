<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Accounting Period
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	06.04.2013
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
echo load_html_head_contents("Accounting Period", "../../", 1, 1,$unicode,'','');
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	var months = new Array(
							new Array('January', 31),
							new Array('February', 28),
							new Array('March', 31),
							new Array('April', 30),
							new Array('May', 31),
							new Array('June', 30),
							new Array('July', 31),
							new Array('August', 31),
							new Array('September', 30),
							new Array('October', 31),
							new Array('November', 30),
							new Array('December', 31)
						);	
	function fnc_accounting_period( operation )
	{
		if (form_validation('cbo_company_name*cbo_starting_year*cbo_starting_month*cbo_ending_month*cbo_status','Company Name*Starting Year*Starting Month*Ending Month*Status')==false)
		{
			return;
		}
		else
		{
		//eval(get_submitted_variables('cbo_company_name*cbo_starting_year*cbo_starting_month*cbo_ending_month*cbo_status*update_id'));
			data1="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_starting_year*month_id*cbo_starting_month*cbo_ending_month*txt_period_name*cbo_status*update_id',"../../");
			var tot_row1=$('#acc_period_table2'+' tbody tr').length;
			var data2='&tot_row1='+tot_row1;
			var data3='';
			for(i=1; i<=tot_row1; i++)
			{
				
				
			data3+=get_submitted_data_string('accounting_period_starting_date_'+i+'*accounting_period_ending_date_'+i+'*accounting_period_title_'+i+'*accounting_period_locked_'+i+'*txt_acc_dates_'+i+'*update_id_dtls'+i,"../../",i);
			}
			
			data=data1+data2+data3;
		
			freeze_window(operation);
			http.open("POST","requires/accounting_period_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_accounts_period_reponse;
		}
	}

	function fnc_accounts_period_reponse()
	{
		if(http.readyState == 4) 
		{
			
			var reponse=trim(http.responseText).split('**');
			//alert (reponse); return;
			if (reponse[0].length>2) reponse[0]=10;
			show_msg(reponse[0]);
			show_list_view(reponse[1],'search_list_view','accounting_period_list_view','../accounts/requires/accounting_period_controller','setFilterGrid("list_view",-1)');
			reset_form('accountingperiod_1','','');
			set_button_status(0, permission, 'fnc_accounting_period',1);
			
			release_freezing();
		}
	}

	function accounting_period_calculate() 
	{
		var starting_month = parseInt( $('select[name="cbo_starting_month"]').val() );
		var ending_month = parseInt( $('select[name="cbo_ending_month"]').val() );
		//alert(ending_month);
		var diff = ending_month - starting_month;
		if( diff == -1 || diff == 11 ) 
		{
			if ( diff==11) 
			{
				document.getElementById('txt_period_name').value=document.getElementById('cbo_starting_year').value;
			}
			else 
			{
				document.getElementById('txt_period_name').value=document.getElementById('cbo_starting_year').value+"-"+((document.getElementById('cbo_starting_year').value*1)+1);
			}
			
			var k=0;
			for( var i = 0; i < 12; i++ ) 
			{
				
				if (starting_month+i > 12)
				{
					var current_month = starting_month + i - 13 ; //( starting_month + i ) > 12 ? ( ) : ( );
					var c_year=(document.getElementById('cbo_starting_year').value*1)+1;
				}
				else
				{
					var current_month = starting_month + i - 1;
					var c_year=document.getElementById('cbo_starting_year').value;
				}
				var c_month=current_month+1;
				//alert(k);
				if(i==0)
				{
					k=k+1;
					document.getElementById('accounting_period_starting_date_1' ).value=months[current_month][0] + ' 1';
					document.getElementById('accounting_period_ending_date_1' ).value=months[current_month][0] + ' 1'; 
					document.getElementById('accounting_period_title_1' ).value='Opening'; 
					document.getElementById('txt_acc_dates_1' ).value=c_year+"-"+ c_month +"-"+1+"__"+c_year+"-"+ c_month +"-"+1; 
					
					k=k+1;
					document.getElementById('accounting_period_starting_date_2' ).value=months[current_month][0] + ' 1';
					document.getElementById('accounting_period_ending_date_2' ).value=months[current_month][0] + ' ' + months[current_month][1]; 
					document.getElementById('accounting_period_title_2' ).value=months[current_month][0];  
					document.getElementById('txt_acc_dates_2' ).value=c_year+"-"+ c_month +"-"+1+"__"+c_year+"-"+ c_month +"-"+months[current_month][1];
				}
				
				else if(i==11) 
				{ 
					k=k+1;
					document.getElementById('accounting_period_starting_date_'+k ).value=months[current_month][0] + ' 1';
					document.getElementById('accounting_period_ending_date_'+k ).value=months[current_month][0] + ' ' + months[current_month][1]; 
					document.getElementById('accounting_period_title_'+k ).value=months[current_month][0]; 
					document.getElementById('txt_acc_dates_'+k ).value=c_year+"-"+ c_month +"-"+1+"__"+c_year+"-"+ c_month +"-"+months[current_month][1];
					k=k+1;
					document.getElementById('accounting_period_starting_date_'+k ).value=months[current_month][0] + ' ' + months[current_month][1];
					document.getElementById('accounting_period_ending_date_'+k ).value=months[current_month][0] + ' ' + months[current_month][1]; 
					document.getElementById('accounting_period_title_'+k ).value='Closing'; 
					document.getElementById('txt_acc_dates_'+k ).value=c_year+"-"+ c_month +"-"+months[current_month][1]+"__"+c_year+"-"+ c_month +"-"+months[current_month][1];
					k=k+1;
					document.getElementById('accounting_period_starting_date_'+k ).value=months[current_month][0] + ' ' + months[current_month][1];
					document.getElementById('accounting_period_ending_date_'+k ).value=months[current_month][0] + ' ' + months[current_month][1]; 
					document.getElementById('accounting_period_title_'+k ).value='Post Closing'; 
					document.getElementById('txt_acc_dates_'+k ).value=c_year+"-"+ c_month +"-"+months[current_month][1]+"__"+c_year+"-"+ c_month +"-"+months[current_month][1];
				}
				else
				{
					k=k+1;
					document.getElementById('accounting_period_starting_date_'+k ).value=months[current_month][0] + ' 1';
					document.getElementById('accounting_period_ending_date_'+k ).value=months[current_month][0] + ' ' + months[current_month][1]; 
					document.getElementById('accounting_period_title_'+k ).value=months[current_month][0];
					document.getElementById('txt_acc_dates_'+k ).value=c_year+"-"+ c_month +"-"+1+"__"+c_year+"-"+ c_month +"-"+months[current_month][1];
				}
			}
		} 
	}

	function myFunction(i)
	{
	 
		if ( document.getElementById('accounting_period_locked_'+i).checked==true)
			document.getElementById('accounting_period_locked_'+i).value=1;
		else
			document.getElementById('accounting_period_locked_'+i).value=0;
			//$("#accounting_period_locked_"+i);
			
	}
		
	function show_accounting_period(id,year)
	{
		year=year.split('-');
		document.getElementById('cbo_starting_year').value=year[0];
		http.open( 'GET', 'requires/accounting_period_controller.php?action=edit_accounting_period&id=' + id );
		http.onreadystatechange = response_show_accounting_period;
		http.send(null);		
	}	
	function response_show_accounting_period()
	{		
		eval(http.response);	    
	}	

</script>
</head>
<body onLoad="set_hotkey()">
<div align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="accountingperiod_1" id="accountingperiod_1" autocomplete="off">
        <fieldset style="width:600px;height:auto;">
        <legend>Accounting Period Set</legend>
                <table width="550px" align="center" id="acc_period_table1">
                    <tr>
                        <td width="100" class="must_entry_caption">Company</td>
                        <td>
							<?
								echo create_drop_down( "cbo_company_name", 150, "select company_name,id from lib_company comp where is_deleted=0  and status_active=1 $company_cond  order by company_name",'id,company_name', 1, '--- Select Company ---', 0, "" );
                            ?>                              
                        </td>
                    </tr>
                    <tr>
                        <td width="100" class="must_entry_caption">Starting Year:</td>
                        <td>
							<?
								$cyear=date("Y",time());
								$pyear=$cyear-5;
								for ($i=0; $i<11; $i++)
								{
									$year[$pyear+$i]=$pyear+$i;
								}
								echo create_drop_down( "cbo_starting_year", 150,$year,"", 1, "-- Select --", $cyear,"" );
                            ?>                              
                        </td>
                        <td width="100">Cur. Year:</td>
                        <td>
							<?
								echo create_drop_down( "cbo_cur_year", 150,$yes_no,"0", 1, "-- Select --", $selected, "" );
                            ?> 
                            <input type="hidden" id="update_id" value="" />       
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Starting Month:</td>
                        <td>
							<?
								echo create_drop_down( "cbo_starting_month", 150,$months,"0", 1, "-- Select --", $selected, "accounting_period_calculate()" );
                            ?> 
                            <input type="hidden" id="month_id" value="<? echo $month_id; ?>" />  
                        </td>
                        <td class="must_entry_caption">Ending Month:</td>
                        <td>
							<? 
								echo create_drop_down( "cbo_ending_month", 150,$months,"0", 1, "-- Select --", $selected, "accounting_period_calculate()" );
                            ?> 
                        </td>
                    </tr>                                
                    <tr>
                        <td>Period Name:</td>
                        <td>
                            <input type="text" readonly name="txt_period_name" id="txt_period_name" class="text_boxes" value="" style="width:138px;" />
                        </td>
                        <td class="must_entry_caption">Is Active:</td>
                        <td>
							<? 
								echo create_drop_down( "cbo_status", 150,$row_status,"0", 1, "-- Select --", $selected, "" );
                            ?> 
                        </td>
                    </tr>
                    <tr><td colspan="4" class="button_container"></td></tr>
                </table>
                <table width="450" height="70" border="0" align="center" id="acc_period_table2">
                    <thead>
                        <tr></tr>
                        <tr></tr>
                        <tr align="left" class="form_caption">
                            <th width="40">SL</th>
                            <th width="120">Starting Date</th>
                            <th width="120">Ending Date</th>
                            <th width="120">Period</th>
                            <th width="50">Locked</th>
                        </tr>
                    </thead>
                    <tbody>												
						<? $kk=0; for( $i = 1; $i <= 15; $i++ ) 
							{ ?>
                                <tr>
                                    <td id="account_period_id_<?php echo $i;?>" align="center"><strong><?php echo $kk; $kk++; ?></strong>&nbsp;</td>
                                    <td><input type="text" id="accounting_period_starting_date_<?php echo $i; ?>" class="text_boxes" value="" style="width:120px;" />
                                        <input type="hidden" id="txt_acc_dates_<?php echo $i; ?>" value="" />
                                        <input type="hidden" id="update_id_dtls<?php echo $i; ?>" value="" />
                                    </td>
                                    <td><input type="text" id="accounting_period_ending_date_<?php echo $i; ?>" class="text_boxes" value="" style="width:120px;" /></td>
                                    <td><input type="text" id="accounting_period_title_<?php echo $i; ?>" class="text_boxes" value="" style="width:120px;" /></td>
                                    <td><input type="checkbox" id="accounting_period_locked_<?php echo $i; ?>" onClick="myFunction(<? echo $i; ?> )" style="width:50px;" /></td>
                                </tr>
						<? } ?>
                    </tbody>
                    <tfoot>
                        <table width="500">      
                            <tr>
                                <td colspan="5" align="center" class="button_container" >
									<? 
										echo load_submit_buttons( $permission, "fnc_accounting_period", 0,0 ,"reset_form('accountingperiod_1','','')",1);
                                    ?> 
                                </td>
                            </tr> 
                        </table>
                    </tfoot>
                </table>        
            </fieldset>
            <br>
            <div id="accounting_period_list_view" style="width:650">
				<?
                    $lib_company_name=return_library_array( "select company_name,id from lib_company", "id", "company_name");
                    $arr = array(0=>$lib_company_name,5=>$row_status);	
                    echo  create_list_view ( "list_view", "Company,Year Name,Year Start,Year End,Period Name,Is Current", "120,100,100,100,100,100","700","250",0, "select  company_id,year_start,year_start_date,year_end_date,period_name,status_active,id from lib_ac_period_mst  where status_active=1 and is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1,"company_id,0,0,0,0,status_active", $arr ,"company_id,year_start,year_start_date,year_end_date,period_name,status_active","../accounts/requires/accounting_period_controller", 'setFilterGrid("list_view",-1);','0,0,3,3,0,0' ) ;
                ?>
            </div>
            <div style="width:500px; float:right;" id="all_jr_list" ></div>
  		</form>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>