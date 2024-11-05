<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="../../css/style_common.css" rel="stylesheet" type="text/css" media="screen" />

<script type="text/javascript" src="../../resources/jquery_ui/jquery-1.4.4.min.js"></script>
<link href="../../resources/jquery_ui/jquery-ui-1.8.10.custom.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../resources/jquery_ui/jquery-ui-1.8.10.custom.min.js"></script>

<link href="../../resources/jquery_dataTable/demo_table_jui.css" rel="stylesheet" type="text/css" media="screen" />
<script src="../../resources/jquery_dataTable/jquery.dataTables.js" type="text/javascript"></script>
<script>
function set_date_range(mon)
{
	 $('.month_button_selected').removeClass('month_button_selected').addClass('month_button');
		 if (mon.substr(0,1)=="0") id_id=mon.replace("0",""); else id_id=mon;
		 $('#btn_'+id_id).removeClass('month_button').addClass('month_button_selected');
	var currentTime = new Date();
	var month = currentTime.getMonth() + 1;
	var day = currentTime.getDate();
	var year = currentTime.getFullYear();
	var start_date="01" + "-" + mon  + "-" + year;
	var to_date=daysInMonth(mon,year) + "-" + mon  + "-" + year;
	document.getElementById('txt_date_from').value=start_date;
	document.getElementById('txt_date_to').value=to_date;
	generate_report('report_container','weekly_shipment_schedule_report');
}


function daysInMonth(month,year) 
{
	return new Date(year, month, 0).getDate();
}

function date_validation()
	{
		var txt_date_from=document.getElementById('txt_date_from').value;
		var txt_date_to=document.getElementById('txt_date_to').value;
		var txt_date_from_array=txt_date_from.split("-");
		var txt_date_to_array=txt_date_to.split("-");
		if (txt_date_from=="")
		{
			$("#messagebox").removeClass().addClass('messagebox').text('Please Select From Date....').fadeIn(1000);
			$("#messagebox").removeClass().addClass('messagebox').fadeOut(5000);
			document.getElementById('txt_date_to').value="";
			return false; 
		}
		if (txt_date_from_array[1]!=txt_date_to_array[1] || txt_date_from_array[2]!=txt_date_to_array[2])
		{
			
			$("#messagebox").removeClass().addClass('messagebox').text('Crossed month date range not allowed').fadeIn(1000);
			$("#messagebox").removeClass().addClass('messagebox').fadeOut(10000);
			document.getElementById('txt_date_to').value="";
			return false; 
		}
		
	}


function generate_report(div,type)
{
	$('#messagebox').removeClass().addClass('messagebox').text('Report Generating....').fadeIn(1000);
	document.getElementById(div).innerHTML="";
	
  if($('#cbo_company_mst').val()==0){						
		$("#messagebox").fadeTo(200,0.1,function(){  //start fading the messagebox
			$('#cbo_company_mst').focus();
			$(this).html('Please Select Company Name.').addClass('messageboxerror').fadeTo(900,1);
			$(this).fadeOut(5000);
		});		
	}
  else if($('#txt_date_from').val()==""){						
		$("#messagebox").fadeTo(200,0.1,function(){  //start fading the messagebox
			$('#txt_date_from').focus();
			$(this).html('Please Select From Date.').addClass('messageboxerror').fadeTo(900,1);
			$(this).fadeOut(5000);
		});		
	}
	else if($('#txt_date_to').val()==""){						
		$("#messagebox").fadeTo(200,0.1,function(){  //start fading the messagebox
			$('#txt_date_to').focus();
			$(this).html('Please Select To Date.').addClass('messageboxerror').fadeTo(900,1);
			$(this).fadeOut(5000);
		});		
	}
	else
	{
		var data='';
			
				data=document.getElementById('cbo_company_mst').value+"_"+document.getElementById('cbo_buyer_name').value+"_"+document.getElementById('txt_date_from').value+"_"+document.getElementById('txt_date_to').value;
		//alert(data);<br />
		if( window.XMLHttpRequest ) xmlhttp = new XMLHttpRequest();	// code for IE7+, Firefox, Chrome, Opera, Safari
		else xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");		// code for IE6, IE5
		
		xmlhttp.onreadystatechange = function() {
			if( xmlhttp.readyState == 4 && xmlhttp.status == 200 ) {
				//document.getElementById(div).innerHTML = xmlhttp.responseText;
				var data_split=xmlhttp.responseText.split('####');
				var link_data=data_split[1];
				document.getElementById('print_div').innerHTML='<a href="' + link_data + '">Print Report </a>';
				document.getElementById(div).innerHTML=data_split[0];
	
				$('#messagebox').fadeTo( 200, 0.1, function() {
					$(this).html('Report has been generated succesfully.....').addClass('messageboxerror').fadeTo(900,1);
					$(this).fadeOut(5000);
				});
			}
		}
		xmlhttp.open( "GET", "includes/generate_shipment_schedule.php?data=" + data +"&type="+type, true );
		xmlhttp.send();
	}
}
		
</script>
</head>

<?php
include('../../includes/common.php');
include('../../includes/array_function.php');
include('../../includes/common_functions.php');
?>

<body>
<div style="width:1000px">
	<fieldset style="width:100%">
    	<table width="100%" cellpadding="0" cellspacing="2" border="0">
        	<tr bgcolor="#CCCCCC">
            	<td colspan="16" align="center" height="25" valign="middle"><font size="3">Weekly Shipment Schedule Report</font></td>
            </tr>
            <tr>
            	<td colspan="16" align="center" height="20" valign="middle">
                	<div id="messagebox" style="background:#F99" align="center"></div>
                </td>
            </tr>
            <tr>
            	<td width="100" align="right">Company Name</td>
                <td width="100" align="left">
                	<select name="cbo_company_mst" id="cbo_company_mst" style="width:120px" class="combo_boxes">
						<option value="0">--- Select Company ---</option>
						<?
						$mod_sql= mysql_db_query($DB, "select * from lib_company where is_deleted=0 and status_active=1 order by company_name"); //where is_deleted=0 and status=0
						$n=mysql_num_rows($mod_sql);
						while ($r_mod=mysql_fetch_array($mod_sql))
						{
							if ($n==1) $company_combo=$r_mod["id"];
						?>
						<option value=<? echo $r_mod["id"];
						if ($company_combo==$r_mod["id"]){?> selected <?php }?>><? echo "$r_mod[company_name]" ?> </option>
						<?
						}
						?>
					</select>
                </td>
                <td width="80" align="right">Buyer Name</td>
                <td width="100" align="left">
                	<select name="cbo_buyer_name" id="cbo_buyer_name"  style="width:150px" class="combo_boxes">
						<option value="0">--- All Buyer ---</option>
						<?
						$mod_sql= mysql_db_query($DB, "select * from lib_buyer where is_deleted=0 and status_active=1 and subcontract_party=2 order by buyer_name");
						while ($r_mod=mysql_fetch_array($mod_sql))
						{
							
						?>
						<option value=<? echo $r_mod["id"];
						if ($company_combo==$r_mod["id"]){?> selected <?php }?>><? echo "$r_mod[buyer_name]" ?> </option>
						<?
						}
						
						?>
					</select>
                
                </td>
                <td width="70" align="right">Date From </td>
                <td width="80"><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" disabled="disabled">
					<script type="text/javascript">
						$( "#txt_date_from" ).datepicker({
									dateFormat: 'dd-mm-yy',
									changeMonth: true,
									changeYear: true
								});
					
					</script> </td>
                <td width="60" align="right">Date To </td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" disabled="disabled">
					<script type="text/javascript">
							$( "#txt_date_to" ).datepicker({
								dateFormat: 'dd-mm-yy',
								changeMonth: true,
								changeYear: true,
								onSelect: function(dateText) {
								date_validation();
								}
							});
					</script> 
                    </td>
            </tr>
        	<tr>
            	<td colspan="12" align="center" height="30" valign="bottom">
                	<input type="button" name="btn_1" onclick="set_date_range('01')" id="btn_1" value="Jan" class="month_button" />
                	&nbsp;<input type="button" name="btn_2" onclick="set_date_range('02')" id="btn_2" value="Feb" class="month_button" />
                    &nbsp;<input type="button" name="btn_3" onclick="set_date_range('03')" id="btn_3" value="Mar" class="month_button" />
                    &nbsp;<input type="button" name="btn_4" onclick="set_date_range('04')" id="btn_4" value="Apr" class="month_button" />
                    &nbsp;<input type="button" name="btn_5" onclick="set_date_range('05')" id="btn_5" value="May" class="month_button" />
                    &nbsp;<input type="button" name="btn_6" onclick="set_date_range('06')" id="btn_6" value="Jun" class="month_button" />
                    &nbsp;<input type="button" name="btn_7" onclick="set_date_range('07')" id="btn_7" value="Jul" class="month_button" />
                    &nbsp;<input type="button" name="btn_8" onclick="set_date_range('08')" id="btn_8" value="Aug" class="month_button" />
                    &nbsp;<input type="button" name="btn_9" onclick="set_date_range('09')" id="btn_9" value="Sep" class="month_button" />
                    &nbsp;<input type="button" name="btn_10" onclick="set_date_range('10')" id="btn_10" value="Oct" class="month_button" />
                    &nbsp;<input type="button" name="btn_11" onclick="set_date_range('11')" id="btn_11" value="Nov" class="month_button" />
                    &nbsp;<input type="button" name="btn_12" onclick="set_date_range('12')" id="btn_12" value="Dec" class="month_button" />
                    &nbsp;<input type="button" name="search" id="search" value="Search" onclick="generate_report('report_container','weekly_shipment_schedule_report')" style="width:100px" class="formbutton" />

                    
                </td>
            </tr>
           </table>
           </fieldset>
          <fieldset style="width:100%; height:auto">
         <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
            	<td colspan="16" height="20" id="print_div" align="center">
                </td>
            </tr>
            <tr>
            	<td colspan="16" id="report_container"> 
                </td>
            </tr>
        </table>
    	
    </fieldset>



</div>

</body>
</html>
