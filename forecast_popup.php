<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];



if($action=="forecast_popup")
{
	
	echo load_html_head_contents("Popup Info","", 1, 1, $unicode,1,1);
	extract($_REQUEST);
	$data=explode("_",$data);
	$company=$data[0];
	$location=$data[1];
	$lineArr=return_library_array( "select id, line_name from lib_sewing_line",'id','line_name');
	$started_month=return_field_value("sales_year_started", "variable_order_tracking", "company_name=$company and variable_list=12");
	//echo "select starting_year from wo_sales_target_mst where company_id=$company group by starting_year order by starting_year";
	$sql_sales=sql_select("select starting_year from wo_sales_target_mst where company_id=$company group by starting_year order by starting_year");
?>
	<script>
    
        var selected_id = new Array(); var selected_name = new Array(); var selected_team_leader = new Array(); var selected_agent = new Array(); var selected_buyer = new Array();
        
        function check_all_data() 
        {
            var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 
    
            tbl_row_count = tbl_row_count-1;
            for( var i = 1; i <= tbl_row_count; i++ ) {
                js_set_value( i );
            }
        }
        
        function toggle( x, origColor ) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }
        
        function set_all()
        {
            var old=document.getElementById('txt_process_row_id').value; 
            if(old!="")
            {   
                old=old.split(",");
                for(var k=0; k<old.length; k++)
                {   
                    js_set_value( old[k] ) 
                } 
            }
        }
		
		function set_color( str )
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
		}
        
        function js_set_value( str ) 
        {
            /*var currentRowColor=document.getElementById( 'search' + str ).style.backgroundColor;
            if(currentRowColor=='yellow')
            {
                var mandatory=$('#txt_mandatory' + str).val();
                var process_name=$('#txt_individual' + str).val();
                if(mandatory==1)
                {
                    alert(process_name+" Subprocess is Mandatory. So You can't De-select");
                    return;
                }
            }*/
            
            toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
            
            if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
                selected_id.push( $('#txt_start_date' + str).val() );
                selected_name.push( $('#txt_end_date' + str).val() );
                selected_team_leader.push( $('#cbo_team_leader' + str).val() );
				selected_agent.push( $('#cbo_agent' + str).val() );
				selected_buyer.push( $('#cbo_buyer_name' + str).val() );
            }
            else 
			{
                for( var i = 0; i < selected_id.length; i++ ) {
                    if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
                }
                selected_id.splice( i, 1 );
                selected_name.splice( i, 1 );
				selected_team_leader.splice( i, 1 );
				selected_agent.splice( i, 1 );
				selected_buyer.splice( i, 1 );
            }
            
            var id = ''; var name = ''; var team_leader = ''; var agent = ''; var buyer = '';
            for( var i = 0; i < selected_id.length; i++ ) {
                id += selected_id[i] + ',';
                name += selected_name[i] + ',';
				team_leader += selected_team_leader[i] + ',';
				agent += selected_agent[i] + ',';
				buyer += selected_buyer[i] + ',';
            }
            
            id = id.substr( 0, id.length - 1 );
            name = name.substr( 0, name.length - 1 );
			team_leader = team_leader.substr( 0, team_leader.length - 1 );
			agent = agent.substr( 0, agent.length - 1 );
			buyer = buyer.substr( 0, buyer.length - 1 );
            
            $('#start_date').val(id);
            $('#end_date').val(name);
			$('#team_leader').val(team_leader);
			$('#agent').val(agent);
			$('#buyer_name').val(buyer);
			
			/*if($('#chk_box' + str).attr('checked'))
			{
				$('#cbo_team_leader' + str).attr("disabled","disabled");
			}
			else
			{
				$('#cbo_team_leader' + str).removeAttr('disabled');
			}*/
        }
		
		function fnc_close()
		{
			var start_date=''; var end_date=''; var team_leader=''; var selected_agent=''; var selected_buyer=''; 
			var i=0;
			$("#tbl_list_search").find('tr').each(function()
			{
				if($(this).find("input[type='checkbox']").is(':checked')==true)
				{
					if(start_date=="")
					{
						start_date=$(this).find('input[name="txt_start_date[]"]').val();
						end_date=$(this).find('input[name="txt_end_date[]"]').val();
						team_leader=$(this).find('select[name="cbo_team_leader[]"]').val();
						selected_agent=$(this).find('select[name="cbo_agent[]"]').val();
						selected_buyer=$(this).find('select[name="cbo_buyer_name[]"]').val();
					}
					else
					{
						start_date+=','+$(this).find('input[name="txt_start_date[]"]').val();
						end_date+=','+$(this).find('input[name="txt_end_date[]"]').val();
						team_leader+=','+$(this).find('select[name="cbo_team_leader[]"]').val();
						selected_agent+=','+$(this).find('select[name="cbo_agent[]"]').val();
						selected_buyer+=','+$(this).find('select[name="cbo_buyer_name[]"]').val();
					}
					i++;
				}

			});
			
			$('#start_date').val(start_date);
            $('#end_date').val(end_date);
			$('#team_leader').val(team_leader);
			$('#agent').val(selected_agent);
			$('#buyer_name').val(selected_buyer);
			
			parent.emailwindow.hide();
		}
		
		
		
    </script>
    </head>
	<body>
	<div id="report_container" align="center">
        <fieldset style="width:650px; margin-left:4px; margin-top:2px">
        <input type="hidden" style="width:150px;" name="start_date" id="start_date" value=""/>
        <input type="hidden" style="width:150px;" name="end_date" id="end_date" value=""/>
        <input type="hidden" style="width:100px;" name="team_leader" id="team_leader" value=""/>
        <input type="hidden" style="width:100px;" name="agent" id="agent" value=""/>
        <input type="hidden" style="width:100px;" name="buyer_name" id="buyer_name" value=""/>
        <table border="1" align="center" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                	<th width="30"></th>
                    <th width="30">SL</th>
                    <th width="70">Start Month</th>
                    <th width="60">Start Year</th>
                    <th width="70">End Month</th>
                    <th width="60">End Year</th> 	 	
                    <th width="100">Team Leader</th>
                    <th width="100">Agent Name</th>
                    <th width="">Buyer Name</th>
                </tr>
            </thead>
         </table>
         <div style="width:650px; overflow-y:scroll; max-height:240px;" id="buyer_list_view" align="center">
         <table cellspacing="0" cellpadding="0" border="1" rules="all" width="632" class="rpt_table" id="tbl_list_search" >
         		<?
				$c_year=date("Y");
				$i=1;
                foreach($sql_sales as $row)
				{
					if($c_year==$row[csf('starting_year')])
					{
						$checked_con="checked";
						$ch_color="style='background-color: yellow;'";
					} 
					else
					{
						$checked_con="";
						$ch_color="";
					}
					
					$start=$row[csf('starting_year')]."-".$started_month."-01";
					$start_date=date("Y-m-d",strtotime($start));
					
					for($e=0;$e<=11;$e++)
					{
						$tmp=add_month(date("Y-m-d",strtotime($start)),$e);
						$yr_mon_part=date("Y-m",strtotime($tmp));
					}
					$cps=explode("-",$yr_mon_part);
					$year_to=$cps[0];
					$month_to=$cps[1];
					$num_days = cal_days_in_month(CAL_GREGORIAN, $month_to, $year_to);
					$end_date=$year_to."-".$month_to."-$num_days";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>"  id="search<? echo $i;?>" <? echo $ch_color; ?> > <!--style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? //echo $i;?>)"-->
                    	<td width="30"><input type="checkbox" name="chk_box<? echo $i;?>" id="chk_box<? echo $i;?>" onClick="set_color(<? echo $i;?>)" <? echo $checked_con; ?> ></td>
						<td width="30"><? echo $i; ?></td>
						<td width="70"><? echo $months[$started_month]; ?></td>
						<td width="60"><? echo $row[csf('starting_year')];//."==".$start_date; ?></td>
						<td width="70"><? echo $months[abs($month_to)]; ?></td>
						<td width="60"><? echo $year_to;//."==".$end_date; ?> </td>
                        <td width="100"><? echo create_drop_down( "cbo_team_leader".$i, 100, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select --", 0,"",0,"","","","","","","cbo_team_leader[]" ); ?></td>
						<td width="100"><?  echo create_drop_down( "cbo_agent".$i, 100, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  and a.id in (select  buyer_id from lib_buyer_party_type where party_type in (20,21)) group by a.id,a.buyer_name order by a.buyer_name","id,buyer_name", 1, "-- Select --", 0,"",0,"","","","","","","cbo_agent[]" );  ?></td>
						<td width=""><? echo create_drop_down( "cbo_buyer_name".$i, 100, "select buy.buyer_name, buy.id from lib_buyer buy where status_active =1 and is_deleted=0 order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected,"",0,"","","","","","","cbo_buyer_name[]"); ?>
                        <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $start_date; ?>"/>
                        <input type="hidden" name="txt_start_date[]" id="txt_start_date<?php echo $i ?>" value="<? echo $start_date; ?>"/>
                        <input type="hidden" name="txt_end_date[]" id="txt_end_date<?php echo $i ?>" value="<? echo $end_date; ?>"/>
                        </td>
					</tr>
					<?
					$i++;
				}
				?>
        </table>
        </div>
         <div style="width:500px;" align="center">
            <input type="button" name="close" class="formbutton" value="Generate" id="main_close" onClick="fnc_close();" style="width:100px" />
         </div>
        </fieldset>
    </div>
    </body>           
	<script src="includes/functions_bottom.js" type="text/javascript"></script>
</html>  
<?
exit();
}

if($action=="fabric_and_order_analysis_popup")
{
	echo load_html_head_contents("Popup Info","", 1, 1, $unicode,1,1);
	extract($_REQUEST);
	
?>
    <table border="1" align="center" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th>Buyer</th>
                <th>Construction</th>
                <th>Composition</th>
                <th>GSM</th>
                <th colspan="2"> Month Range</th>
                <th>Year</th>
                <th>Previous Perios</th> 	 	
                <th>Value</th> 	 	
                <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
            </tr>
        </thead>
        <tbody>
        	<tr>
            	<td>
                <? echo create_drop_down( "cbo_buyer_name", 100, "select buy.buyer_name, buy.id from lib_buyer buy where status_active =1 and is_deleted=0 order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected,"",0,"","","","","","","cbo_buyer_name[]"); ?>
                </td>
                <td><? echo create_drop_down( "cbo_constraction", 60, $yes_no,"",0, "-- All --", 2,"alert(1)"); ?></td>
                <td><? echo create_drop_down( "cbo_composition", 60, $yes_no,"",0, "-- All --", 2,"alert(1)"); ?></td>
                <td><? echo create_drop_down( "cbo_gsm", 60, $yes_no,"",0, "-- All --",2,"alert(1)"); ?></td>
                
                <td><? echo create_drop_down( "cbo_start_month", 80, $months,"",0, "-- All --", $selected,"alert(1)"); ?></td>
                <td><? echo create_drop_down( "cbo_end_month", 80, $months,"", 0, "-- All --", $selected,"alert(1)"); ?></td>
                <td><? echo create_drop_down( "cbo_year", 60, $year,"", 0, "-- All --", $selected,"alert(1)"); ?></td>
               <td><input type="text" id="txt_previous_period" name="txt_previous_period" class="text_boxes_numeric" style="width:50px;"></td>
                <td><? 
				$show_value=array(1=>'GMT Qty',2=>'Fabric Weight');
				echo create_drop_down( "cbo_value", 80,$show_value,"", 0, "-- Select --", $status_select,"alert(11)" );
				?></td>
                <td><input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="parent.emailwindow.hide();" /></td>
            
            </tr>
        </tbody>
        
        
     </table>







<?

exit();
}


if($action=="daily_finishing_capacity_achievment_iron_popup")
{
	echo load_html_head_contents("Popup Info","", 1, 1, $unicode,1,1);
	extract($_REQUEST);
	
?>
    <table border="1" align="center" class="rpt_table" rules="all" width="395" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th colspan="2">Date</th>
                <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
            </tr>
        </thead>
        <tbody>
        	<tr>
                <td align="center"><input id="txt_date_from" class="datepicker" type="text" value="" placeholder="From Date" style="width:100px" name="txt_date_from"></td>
                <td align="center"><input id="txt_date_to" class="datepicker" type="text" value="" placeholder="To Date" style="width:100px" name="txt_date_to"></td>
                <td align="center"><input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="parent.emailwindow.hide();" /></td>
            
            </tr>
        </tbody>
     </table>
	<script src="includes/functions_bottom.js" type="text/javascript"></script>
     
<?

exit();
}


if($action=="dyeing_capacity_vs_load_popup")
{
	echo load_html_head_contents("Popup Info","", 1, 1, $unicode,1,1);
	extract($_REQUEST);
	?>
    <script>
	function fn_date_calculate(str)
	{
		$("#txt_date_to").val(add_days(str,'29'));
	}
	</script>
    <table border="1" align="center" class="rpt_table" rules="all" width="395" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th colspan="2">Date</th>
                <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
            </tr>
        </thead>
        <tbody>
        	<tr>
                <td align="center"><input id="txt_date_from" class="datepicker" type="text" value="" placeholder="From Date" style="width:100px" name="txt_date_from" readonly onChange="fn_date_calculate(this.value);" ></td>
                <td align="center"><input id="txt_date_to" class="datepicker" type="text" value="" placeholder="To Date" style="width:100px" name="txt_date_to" readonly disabled></td>
                <td align="center"><input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="parent.emailwindow.hide();" /></td>
            </tr>
        </tbody>
     </table>
	<script src="includes/functions_bottom.js" type="text/javascript"></script>
	<?
    exit();
}


if($action=="daily_finishing_capacity_achievment_iron_popup")
{
	echo load_html_head_contents("Popup Info","", 1, 1, $unicode,1,1);
	extract($_REQUEST);
	
?>
    <table border="1" align="center" class="rpt_table" rules="all" width="395" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th colspan="2">Date</th>
                <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
            </tr>
        </thead>
        <tbody>
        	<tr>
                <td align="center"><input id="txt_date_from" class="datepicker" type="text" value="" placeholder="From Date" style="width:100px" name="txt_date_from"></td>
                <td align="center"><input id="txt_date_to" class="datepicker" type="text" value="" placeholder="To Date" style="width:100px" name="txt_date_to"></td>
                <td align="center"><input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="parent.emailwindow.hide();" /></td>
            
            </tr>
        </tbody>
     </table>
	<script src="includes/functions_bottom.js" type="text/javascript"></script>
     
<?

exit();
}


function add_month($orgDate,$mon){
	$cd = strtotime($orgDate);
	$retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
	return $retDAY;
}


if($action=="opendate_popup")
{
	echo load_html_head_contents("Popup Info","", 1, 1, $unicode,'','');
	//$ex_data=explode('_',$data);
	?>
	<script>
		function js_set_value()
		{
			if( form_validation('txt_date_from','Select Start Date')==false)
			{
				return;
			}
			else
			{
				parent.emailwindow.hide();
			}
		}
	</script>
    </head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="keydate_1"  id="keydate_1" autocomplete="off">
                <table width="270" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                         <tr>
                            <th width="270">Select Start Date</th>
                         </tr>
                  	</thead>
                    <tr>
                    	<td align="center"><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:250px" placeholder="Select Start Date"></td>
                    </tr>
                    <tr>
                    	<td align="center"><input type="button" name="button2" class="formbutton" value="Close" onClick="js_set_value( document.getElementById('txt_date_from').value )" style="width:70px;" /></td>
                    </tr>
                 </table>
             </form>
             </div>
	</body>           
	<script src="includes/functions_bottom.js" type="text/javascript"></script>
	</html>
    <?
	exit();
}


if($action=="opendate_type_search_popup")
{
	echo load_html_head_contents("Popup Info","", 1, 1, $unicode,'','');
	//$ex_data=explode('_',$data);
	?>
	<script>
		function js_set_value()
		{
			parent.emailwindow.hide();
			/*if( form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
			{
				return;
			}
			else
			{
				parent.emailwindow.hide();
			}*/
		}
	</script>
    </head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="keydate_1"  id="keydate_1" autocomplete="off">
                <table width="370" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                         <tr>
                            <th width="150">Type</th>
                            <th width="110">From Date</th>
                            <th width="110">To Date</th>
                         </tr>
                  	</thead>
                    <tr>
                    	<td align="center">
						<?
							$typeArr=array(1=>'Ship Date',2=>'Country Ship Date');
							echo create_drop_down( "cbo_type", 150, $typeArr,"", 1, "--Select --", 1, "",0 );
						?>
                        </td>
                    	<td align="center"><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="Select Start Date"></td>
                    	<td align="center"><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="Select End Date"></td>
                    </tr>
                    <tr>
                    	<td colspan="3" align="center"><input type="button" name="button2" class="formbutton" value="Close" onClick="js_set_value( document.getElementById('txt_date_from').value )" style="width:70px;" /></td>
                    </tr>
                 </table>
             </form>
             </div>
	</body>           
	<script src="includes/functions_bottom.js" type="text/javascript"></script>
	</html>
    <?
	exit();
}



if($action=="open_tna_progress_report_date_type_search_popup")
{
	echo load_html_head_contents("Popup Info","", 1, 1, $unicode,'','');
	//$ex_data=explode('_',$data);
	?>
	<script>
		function js_set_value()
		{
			parent.emailwindow.hide();
		}
	</script>
    </head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="keydate_1"  id="keydate_1" autocomplete="off">
                <table width="370" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                         <tr>
                            <th width="150">Type</th>
                            <th width="110">From Date</th>
                            <th width="110">To Date</th>
                         </tr>
                  	</thead>
                    <tr>
                    	<td align="center">
						<?
							$typeArr=array(1=>"Ship Date",2=>"Country Ship Date",3=>"PO Insert Date",4=>"Plan Start Date",5=>"Plan Finish Date");
							echo create_drop_down( "cbo_type", 150, $typeArr,"", 1, "--Select --", 1, "",0 );
						?>
                        </td>
                    	<td align="center"><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="Select Start Date"></td>
                    	<td align="center"><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="Select End Date"></td>
                    </tr>
                    <tr>
                    	<td colspan="3" align="center"><input type="button" name="button2" class="formbutton" value="Close" onClick="js_set_value( document.getElementById('txt_date_from').value )" style="width:70px;" /></td>
                    </tr>
                 </table>
             </form>
             </div>
	</body>           
	<script src="includes/functions_bottom.js" type="text/javascript"></script>
	</html>
    <?
	exit();
}



?>