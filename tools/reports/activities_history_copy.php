<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<link href="../../css/style_common.css" rel="stylesheet" type="text/css" media="screen" />
	 
	<script src="includes/ajax_submit.js" type="text/javascript"></script>
	<script src="includes/functions.js" type="text/javascript"></script>
	
	<script type="text/javascript" src="../../resources/jquery_ui/jquery-1.4.4.min.js"></script>
	<link href="../../resources/jquery_ui/jquery-ui-1.8.10.custom.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="../../resources/jquery_ui/jquery-ui-1.8.10.custom.min.js"></script>
    <script>
	function set_date_range(mon)
	{
		 $('.month_button_selected').removeClass('month_button_selected').addClass('month_button');
		 if (mon.substr(0,1)=="0") id_id=mon.replace("0",""); else id_id=mon;
		 $('#btn_'+id_id).removeClass('month_button').addClass('month_button_selected');
		var currentTime = new Date();
		var month = currentTime.getMonth() + 1;
		var day = currentTime.getDate();
		var year = document.getElementById('cbo_year').value;//currentTime.getFullYear();
		
		var start_date="01" + "-" + mon  + "-" + year;
		var to_date=daysInMonth(mon,year) + "-" + mon  + "-" + year;
		
		document.getElementById('txt_date_from').value=start_date;
		document.getElementById('txt_date_to').value=to_date;
	}
	
   	function daysInMonth(month,year) 
   	{
    	return new Date(year, month, 0).getDate();
	}
	function change_color(v_id,e_color)
	{
		//alert(v_id);
		//alert(e_color);
		//alert(document.getElementById(v_id).bgColor);
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}

	
	</script>
</head>

<?php
session_start();
include('../../includes/common.php');
include('../../includes/array_function.php');
include('../../includes/common_functions.php');
 
 ?>

<body onLoad="document.onkeydown = checkKeycode;">
<div style="width:1120px">
	<fieldset style="width:100%">
    	<table width="100%" cellpadding="0" cellspacing="2">
        	<tr class="form_caption">
            	<td colspan="10">
                	Activities History
                </td>
            </tr>
            <tr>
            	<td colspan="10" align="center" height="20" valign="middle">
                	<div id="messagebox" style="background:#F99" align="center"></div>
                </td>
            </tr>
            
            <tr>
            	<td colspan="10" align="center" height="30" valign="middle">
                	<table class="rpt_table" cellspacing="0" cellpadding="0" width="450">
                    	<thead>
                            <th width="150" colspan="2" align="right"> User Name</th>
                            <th width="150" colspan="2" align="right"> Module Name</th>
                            <th width="150" colspan="2" align="right"> Menu Name</th>
                             <th width="150" colspan="2" align="right"> Search By</th>  
                             
                            <th  colspan="4" width="200"> Date Range</th><th> </th>
                      </thead>
                      <tr class="general">
            	 			<td width="150" colspan="2" align="right">
                                <select name="cbo_user_name" id="cbo_user_name" style="width:150px"  class="combo_boxes">
                                	 
                                    <option value="0">--- All User ---</option>
                                    <?
										 
                                    $mod_sql= mysql_db_query($DB, "select id,user_name from user_passwd where valid=1  order by user_name"); //where is_deleted=0 and status=0
                                    
                                    while ($r_mod=mysql_fetch_array($mod_sql))
                                    {
                                       
                                    ?>
                                    <option value=<? echo $r_mod["id"];
                                    if ($company_combo==$r_mod["id"]){?> selected <?php }?>><? echo "$r_mod[user_name]" ?> </option>
                                    <?
                                    }
                                    ?>
                                </select>
                            
                            </td>
                            <td width="150" colspan="2" align="right">
                                <select name="cbo_mdule_name" id="cbo_mdule_name" style="width:150px"  class="combo_boxes" onchange="load_drop_down(this.value, 1, 'menu_container')">
                                	 
                                    <option value="0">--- All User ---</option>
                                    <?
										 
                                    $mod_sql= mysql_db_query($DB, "select m_mod_id,main_module from main_module where status=1  order by main_module"); //where is_deleted=0 and status=0
                                    
                                    while ($r_mod=mysql_fetch_array($mod_sql))
                                    {
                                    ?>
                                    <option value=<? echo $r_mod["m_mod_id"];
                                    if ($company_combo==$r_mod["m_mod_id"]){?> selected <?php }?>><? echo "$r_mod[main_module]" ?> </option>
                                    <?
                                    }
                                    ?>
                                </select>
                            
                            </td>
                            <td width="150" colspan="2" align="right" id="menu_container">
                                <select name="cbo_menu_name" id="cbo_menu_name" style="width:150px"  class="combo_boxes">
                                	 
                                    <option value="0">--- All User ---</option>
                                    <?
										 
                                    $mod_sql= mysql_db_query($DB, "select m_menu_id,menu_name from main_menu where status=1  order by menu_name"); //where is_deleted=0 and status=0
                                    
                                    while ($r_mod=mysql_fetch_array($mod_sql))
                                    {
                                       
                                    ?>
                                    <option value=<? echo $r_mod["m_menu_id"];
                                    if ($company_combo==$r_mod["m_menu_id"]){?> selected <?php }?>><? echo "$r_mod[menu_name]" ?> </option>
                                    <?
                                    }
                                    ?>
                                </select>
                            
                            </td>
                              <td width="150" colspan="2" align="right"> 
                              	<select name="cbo_search_by" id="cbo_search_by" style="width:150px"  class="combo_boxes">
                                	 
                                    <option value="0">--- All Data ---</option>
                                    <option value="1">New Entry</option>
                                    <option value="2">Edit/Update</option>
                                    <option value="3">Delete Operation</option>
                                </select>
                              </td>  
                                 
                           
                            <td  colspan="2"> <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
                                <script type="text/javascript">
                                    $( "#txt_date_from" ).datepicker({
                                                dateFormat: 'dd-mm-yy',
                                                changeMonth: true,
                                                changeYear: true
                                            });
                                
                                </script> </td>
                            
                            <td  colspan="2" align="left"> <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                                <script type="text/javascript">
                                    $( "#txt_date_to" ).datepicker({
                                                dateFormat: 'dd-mm-yy',
                                                changeMonth: true,
                                                changeYear: true
                                            });
                                
                                </script> 
                                </td>
                                <td width="5%"><input type="button" name="Show" id="Show" value="Show" class="formbutton" style="width:80px" onclick="generate_activities_history_report()" /></td>
                        </tr>
                  </table>
               </td>
                 
            
        	<tr style="visibility:hidden">
            	<td colspan="10" align="center" height="40" valign="middle">
                 <?
                    	$c_year= date("Y");
                    	$p_year=$c_year-15;
					
                    
                    ?>
                    <select name="cbo_year" id="cbo_year"  style="width:80px" class="combo_boxes">
						 
						<?
						for($i=0;$i<21;$i++)
						{
						?>
						<option value=<? echo $p_year+$i;
						if ($c_year==$p_year+$i){?> selected <?php }?>><? echo $p_year+$i; ?> </option>
						<?
						}
						?>
					</select>
                	<input type="button" name="btn_1" onclick="set_date_range('01')" id="btn_1" value="January" class="month_button" />
                	&nbsp;<input type="button" name="btn_2" onclick="set_date_range('02')" id="btn_2" value="February" class="month_button" />
                    &nbsp;<input type="button" name="btn_3" onclick="set_date_range('03')" id="btn_3" value="March" class="month_button" />
                    &nbsp;<input type="button" name="btn_4" onclick="set_date_range('04')" id="btn_4" value="April" class="month_button" />
                    &nbsp;<input type="button" name="btn_5" onclick="set_date_range('05')" id="btn_5" value="May" class="month_button" />
                    &nbsp;<input type="button" name="btn_6" onclick="set_date_range('06')" id="btn_6" value="June" class="month_button" />
                    &nbsp;<input type="button" name="btn_7" onclick="set_date_range('07')" id="btn_7" value="July" class="month_button" />
                    &nbsp;<input type="button" name="btn_8" onclick="set_date_range('08')" id="btn_8" value="August" class="month_button" />
                    &nbsp;<input type="button" name="btn_9" onclick="set_date_range('09')" id="btn_9" value="September" class="month_button" />
                    &nbsp;<input type="button" name="btn_10" onclick="set_date_range('10')" id="btn_10" value="October" class="month_button" />
                    &nbsp;<input type="button" name="btn_11" onclick="set_date_range('11')" id="btn_11" value="November" class="month_button" />
                    &nbsp;<input type="button" name="btn_12" onclick="set_date_range('12')" id="btn_12" value="December" class="month_button" />
                   
                    
                </td>
            </tr>
            
            <tr>
            	<td colspan="10" height="20">
                
                </td>
            </tr>
            <tr>
            	<td colspan="10" id="report_container"> 
                 
                </td>
            </tr>
        </table>
    	
    </fieldset>



</div>

</body>
</html>
