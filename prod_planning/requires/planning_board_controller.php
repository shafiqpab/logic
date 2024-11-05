<?php
//  Need to check Revise plan, multiple row in order selection while adding date ad line

header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include( '../../includes/common.php' );

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

if( $action=="overlapped_decision" )
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	
	?>
    <script>
	function js_setvalue( str )
	{
		$('#selected_job').val(str);
		parent.emailwindow.hide();
	}
	
	</script>
    <table class="rpt_table" rules="all">
    <thead>
    	<tr>
        	<th width="50">
            	<input type="hidden" id="selected_job">
            </th>
            <th>
            	Decision
            </th>
        </tr>
    </thead>
        <tr>
        	<td width="50">
            	1
            </td>
            <td onClick="js_setvalue(1)" style="cursor:pointer; height:25px">
            	Push forward Next plan for <? echo $ncopy_plan_length-$available_days; ?> days if days available.
            </td>
        </tr>
        <tr>
        	<td width="50">
            	2
            </td>
            <td  onClick="js_setvalue(2)" style="cursor:pointer; height:25px">
            	Resize copied plan for <? echo $available_days; ?> days and Place here.
            </td>
        </tr>
        <tr>
        	<td width="50">
            	3
            </td>
            <td onClick="js_setvalue(3)" style="cursor:pointer; height:25px">
            	Select another line and Date manually
            </td>
        </tr>
        <tr>
        	<td width="50">
            	4
            </td>
            <td onClick="js_setvalue(4)" style="cursor:pointer; height:25px">
            	Discard Changes
            </td>
        </tr>
    </table>
    <?
	die;
}

if($action=="move_plan_decision")
{
	
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 0, 0, $unicode);
	//var_dump($_REQUEST);
	  //die; ?>
    <script>
	function js_setvalue( str )
	{
		if (str==0 )
		{
			$('#selected_job').val( $('#cbo_offday_dec').val()+"__"+$('#cbo_complexity').val()+"__"+$('#txt_overlapped_qnty').val() );
			parent.emailwindow.hide();
		}
		else
		{
			$('#selected_job').val('');
			parent.emailwindow.hide();
		}
	}
	
	</script>
    <head>
    <body>
    <table class="rpt_table" rules="all">
    <thead>
    	<tr>
        	<th width="50">
            	<input type="hidden" id="selected_job">
            </th>
            <th>
            	Decision
            </th>
        </tr>
    </thead>
         
        <tr>
        	<td width="50">
            	1
            </td>
            <td   style="cursor:pointer; height:25px">
            	<?
					if( $off_day_include==1 )  $str="included"; else $str="excluded";
					$offday_dec=array(1=>"Include",2=>"Exclude");
				?>
            	This plan is <? echo $str; ?> offday production. Copied plan will <? echo create_drop_down( "cbo_offday_dec", 100, $offday_dec,"",0, "",$off_day_include );?> offdays Productions.
            </td>
        </tr>
        <tr>
        	<td width="50">
            	3
            </td>
            <?
			if($compid>0) $learn_yes_no=1; else $learn_yes_no=2;
			?>
            <td  style="cursor:pointer; height:25px">
            	Complexity level of this plan is <strong><? echo $complexity_level[$compid]; ?></strong>. Do You want to apply Learning curve&nbsp; <? echo create_drop_down( "cbo_complexity", 50, $yes_no,"",2, "",$learn_yes_no );?> ?
            </td>
        </tr>
        <tr>
        	<td width="50">
            	4
            </td>
             
            <td  style="cursor:pointer; height:25px">
            	Overlapped Quantity &nbsp;<input type="text" name="txt_overlapped_qnty" id="txt_overlapped_qnty" class="text_boxes_numeric" style="width:100px">
            </td>
        </tr>
        <tr>
        	<td width="50">
            	5
            </td>
            <td onClick="js_setvalue(4)" style="cursor:pointer; height:25px">
            	Discard Changes
            </td>
        </tr>
        <tr>
        	<td width="50" colspan="2" align="center" height="35" valign="middle">
            	<input type="button" name="sub" value="Close" style="width:100px" onClick="js_setvalue(0)" class="formbutton">
            </td>
        </tr>
    </table>
   </body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
    <?
	die;
}

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 170,"select a.id,a.buyer_name from  lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 group by a.id,a.buyer_name ","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
	die;
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select --", '', "load_drop_down( 'requires/planning_board_controller', this.value, 'load_drop_down_floor', 'floor_td' );" );	
	die;	 
}

if ( $action=="load_drop_down_floor" )
{
	echo create_drop_down( "cbo_floor_name", 150, "select id,floor_name from  lib_prod_floor where location_id='$data' and production_process=5 and status_active =1 and is_deleted=0 order by floor_serial_no","id,floor_name", 1, "-- Select --", '', "" );	
	die;
}             

if($action=="revise_planning")
{
	//echo $action;
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	if( $compid==0 ) $sel=2; else $sel=1;
	
	// echo $comptarg."=".$sel."=". $compid."=". $compstart;
	$pos=explode("|*|",$group_po_info); 
	if(count($pos)>1)
	{
		$disable="disabled='disabled'";
	}
	?>
    <script>
	var multpo=0;
	<?
		$complexity_levels= json_encode($complexity_level_data); 
		echo "var complexity_levels = ". $complexity_levels . ";\n";
		
	?>
	
	var comptype='<? echo $sel; ?>';
	var compid='<? echo $compid; ?>';
	var compstart='<? echo $compstart; ?>';
	var comptargt='<? echo $comptarg; ?>';
	var compinc='<? echo $compinc; ?>';
	//alert(comptype)
	function js_setvalue( str )
	{
		if( str==4)
			parent.emailwindow.hide();
		else if(str==5)
		{
			var ln=$('#tbl_group_po tbody tr').length;
			var gpdata='';
			for(var k=1;k<=ln;k++)
			{
				if(($('#grp_plan_qnty'+k).val()*1)>0)
				{
					var rdt=$('#frow'+k).attr('gpdata').split("_");
					rdt[2]=($('#grp_plan_qnty'+k).val()*1);
					var ndata=rdt.join("_");
					if( gpdata !='')
						gpdata=gpdata+"|*|"+ndata;
					else
						gpdata=ndata;
					
				}
				//tot+=($('#grp_plan_qnty'+k).val()*1);
			}
			//alert(gpdata);
			//return;
			$('#selected_job').val( $('#cbo_off_day_plan').val()+"__"+$('#txt_plan_qnty').val()+"__"+$('#cbo_complexity_type').val()+"__"+$('#cbo_complexity_selection').val()+"__"+$('#txt_first_day').val()+"__"+$('#txt_increment').val()+"__"+$('#txt_target').val()+"__"+$('#txt_plan_new_date').val()+"__"+$('#cbo_line_name').val()+"__"+gpdata );
			
			//alert($('#selected_job').val());
			parent.emailwindow.hide();
			
			      
		}
		 
	}
//	alert(compid)
	function fnc_change_complx2( str )
	{
		// alert(str)
		if( str==1 )
		{
			$('#fdouttd').css('width', '120');
			 
			$('#fdouttd').html('First Day Output');
			$('#tdincremnt').css('display', 'block');
			$('#tdincremnt_val').css('display', 'block');
			
			$('#cbo_complexity_selection').removeAttr('disabled'); 
			//$('#cbo_complexity_selection').removeAttr('class'); 
			$('#txt_first_day').attr('class','text_boxes_numeric'); 
			$('#txt_first_day').css('width', '80');
			$('#txt_first_day').attr('title',''); 
			$('#txt_first_day').attr('placeholder',''); 
			
			$('#cbo_complexity_selection').val(compid);
			$('#txt_first_day').val(compstart);
			$('#txt_increment').val(compinc);
		}
		else
		{
			$('#cbo_complexity_selection').val('0');
			$('#fdouttd').attr('width','210');
			$('#fdouttd').html('Output % of Target (Comma Separated)');
			$('#tdincremnt').css('display', 'none');
			$('#tdincremnt_val').css('display', 'none');
			$('#cbo_complexity_selection').attr('disabled','disabled'); 
			//$('#cbo_complexity_selection').removeAttr('class'); 
			$('#txt_first_day').attr('class','text_boxes'); 
			$('#txt_first_day').css('width','210');
			$('#txt_first_day').attr('title','Comma Separated values'); 
			$('#txt_first_day').attr('placeholder','Comma Separated values'); 
			
			$('#cbo_complexity_selection').val(compid);
			$('#txt_first_day').val(compstart);
			$('#txt_increment').val(compinc);
			
			//$('#cbo_complexity_selection').val('');
			//$('#txt_first_day').val('');
			//$('#txt_increment').val('');
		}
		
			jQuery(".text_boxes").keypress(function(e) {
				 var c = String.fromCharCode(e.which);
				 //alert(c);
				 var allowed = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890.-,%@!/\<>?+[]{};: '; // ~ replace of Hash(#)()
				 if (e.which != 8 && e.which !=0 && allowed.indexOf(c) < 0) 
					return false; 
				
			});
		//alert( str )
		//set_all_onclick()
	}
	 
	function fill_complexity(vid)
	{
		 
		if(!vid) var vid=0; 
		// alert(compstart)
  		if((compstart*1)>100000)
		{
			$('#txt_first_day').val(compstart);
			$('#txt_increment').val(compinc);
		}
		else
		{
			
			$('#txt_first_day').val( complexity_levels[vid]['fdout'] );
			$('#txt_increment').val( complexity_levels[vid]['increment'] );
			
			if( $('#selected_po_gsd').val()=='')
				$('#txt_target').val( complexity_levels[vid]['target'] );
			else
				$('#txt_target').val( $('#selected_po_gsd').val() );
				
			if( $('#txt_target').val()=='' )$('#txt_target').val(comptargt)	;
		}
	}
	
	function calc()
	{
		var ln=$('#tbl_group_po tbody tr').length;
		var tot=0;
		for(var k=1;k<=ln;k++)
		{
			tot+=($('#grp_plan_qnty'+k).val()*1);
		}
		$('#txt_plan_qnty').val(tot);
	}
	 
	</script>
    <head>
    <body>
    <center>
    <div style="float:left; width:65%">
    <table class="rpt_table" rules="all">
    <thead>
    	<tr>
        	<th width="50">
            	<input type="hidden" id="selected_job">
            </th>
            <th>
            	Decision
            </th>
        </tr>
    </thead>
        <tr>
        	<td width="50">
            	1
            </td>
            <td onClick="js_setvalue(1)" style="cursor:pointer; height:25px">
            	Existing Plan Qnty is <strong><? echo $plan_qnty; ?></strong>.
                Total Produced Qnty is <strong><? echo $tot_prod_qny; ?></strong>. <br>
                Remaining quantity Before selection <strong><? echo $part_qnty;?></strong><br>
                Remaining quantity after selection <strong><? echo $plan_qnty-$part_qnty;?></strong><br>
                Changed/New Plan Qnty<input type="text" class="text_boxes_numeric" value="<? echo $plan_qnty-$part_qnty;//$rem_plan_qny; ?>" style="width:50px" id="txt_plan_qnty" <? echo $disable;?>> 
            </td>
        </tr>
        <tr>
        	<td width="50">
            	2
            </td>
            <td  onClick="js_setvalue(2)" style="cursor:pointer; height:25px">
            	<? 
					$off_day_sts=array(1=>"Include",2=>"Exclude");
				?>
            	This plan  <strong><? echo $off_day_sts[$off_day_plan]." ".$off_days_count; ?></strong> offdays production. <?  echo create_drop_down( "cbo_off_day_plan", 100, $off_day_sts, "", 0, "-- Select --", $off_day_plan, "" ); ?> those off Days.
            </td>
        </tr>
        <tr>
        	<td width="50">
            	3
            </td>
            <td  onClick="js_setvalue(2)" style="cursor:pointer; height:25px">
            	 New plan start Date <input type="text" class="datepicker" value="<? echo $edtstartdate; ?>" id="txt_plan_new_date" style="width:80px" />
            </td>
        </tr>
        <tr>
        	<td width="50">
            	4
            </td>
            <td  onClick="js_setvalue(2)" style="cursor:pointer; height:25px">
            	Revise plan in new line  <? $str=""; if ( $floor_id!=0) $str=" and floor_name= $floor_id "; echo create_drop_down( "cbo_line_name", 110, "select id,line_name from lib_sewing_line where company_name='$company_id'  and location_name='$location_id' $str order by sewing_line_serial ","id,line_name", 0, "-- Select --", $line_id, "" ); ?>
            </td>
        </tr>
        <tr>
        	<td colspan="2">
            	<table width="450" class="rpt_table" id="tbl_lag">
                    <thead> 
                        <tr>
                            <th colspan="1">Complexity Type</th> 
                            <th colspan="3">
                            <? echo create_drop_down("cbo_complexity_type", 210, $complexity_type_tmp, "", 0, "-- Select --", $sel, "fnc_change_complx2(this.value)",$sel); ?></th>
                        </tr>
                    <tr>
                        <th width="130">Complexity Level</th>
                         <th width="120" id="fdouttd">First Day Output</th>
                         <th width="90" id="tdincremnt">Increment</th>
                         <th width="90">Target</th>
                          
                    </tr>
                    <tr>
                         <th width="120">
                         <? 
                             
                                echo create_drop_down( "cbo_complexity_selection", 110, $complexity_level, "", 0, "-- Select --", $compid, "fill_complexity(this.value)", 0); 
                         ?>
                         </th>
                         <th width="90"><input type="text" class="text_boxes" id="txt_first_day" style="width:80px" /></th>
                         <th width="90" id="tdincremnt_val"><input type="text" class="text_boxes_numeric" id="txt_increment" style="width:80px" /></th>
                         <th width="90"><input type="text" class="text_boxes_numeric" id="txt_target" value="<? echo $comptarg; ?>" style="width:80px" /></th>
                          
                    </tr>
                    </thead>
                     
                    
                </table>
            </td>
            
        </tr>
        <tr>
        	 <td colspan="2" align="center"  onClick="js_setvalue(4)" class="formbutton" style="cursor:pointer; height:25px">
            	<strong>Discard Changes</strong>
            </td>
        </tr>
         <tr>
        	 <td colspan="2" align="center"   style="cursor:pointer; height:5px">
            	 
            </td>
        </tr>
        <tr>
        	<td colspan="2" align="center"  onClick="js_setvalue(5)" class="formbutton" style="cursor:pointer; height:25px; padding-top:5px">
            	<strong>Apply Changes</strong>
            </td>
        </tr>
        
    </table>
    </div>
    <? 
			if(count($pos)>1)
			{
		?>
    <div style="float:left; min-height:300px; max-height:300px; overflow:scroll; width:35%" align="left">
    	
			<table cellpadding="0" id="tbl_group_po" cellspacing="0" width="100%" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th width="30"> SL </th>
						<th width="70">Job NO</th>
						<th width="100">PO NO</th>
						<th width="65">Plan Qnty</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=0;
					// echo $group_po_info;
					  //OG-16-00647_10092_340_06-11-2016_4600166555_06-11-2016_838536_5_2_340_19-10-2016_04-11-2016|*|OG-16-00646_10085_700_02-11-2016_4600166551_02-11-2016_838492_5_2_700_31-07-2016_30-09-2016_

					foreach($pos as $po )
					{
						$i++;
						$npo=explode("_",$po);
						?>
					<tr>
						<td id="frow<? echo $i;?>" gpdata="<? echo $po; ?>"><? echo $i; ?></td>
						<td><? echo $npo[0]; ?></td>
						<td><? echo $npo[4]; ?></td>
						<td align="right"><input type="text" onKeyUp="calc()" class="text_boxes_numeric" title="<? echo $npo[9]; ?>" id="grp_plan_qnty<? echo $i; ?>" style="width:50px;" value="<? echo $npo[9]; ?>"></td>
					</tr>
					<? } ?>
				</tbody>
			</table>
         
    </div><? } ?>
    </center>
   </body> 
   <script>
    //alert(comptype);
	var compstart='<? echo $compstart; ?>';
   	if(comptype==2)
	{
  	 	eval( fnc_change_complx2( comptype ) );
		$('#txt_first_day').val(compstart);
	}
	else { $('#cbo_complexity_selection').val(compid); fill_complexity( compid ); }
   </script>          
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
    <?
	die;
}

if ( $action=="release_board_function" )
{
	$con = connect();
	if( $db_type==0 )
	{
		mysql_query("BEGIN");
	}
	execute_query("update planning_board_status set board_status=0 where user_id='".$_SESSION['logic_erp']['user_id']."'",1);
	
	if($db_type==0) mysql_query("COMMIT");  
	else oci_commit($con);  
		 
	//check_table_status( $data,0);
	echo "2";	
	disconnect($con);
	die;	 
}

if( $action=="show_plan_details" )
{
	$con = connect();
	$data=explode("__",$data);
	check_table_status( $data[3],0);
	
	//$plan_uses_sts=return_field_value("is_locked", "table_status_on_transaction"," form_name='".$data[3]."'");
	$user_list=return_library_array("select id,user_name from user_passwd", "id","user_name");
	
 $current_plan_user=return_field_value("user_id", "planning_board_status"," company_name='".$data[0]."' and location_name='".$data[1]."' and floor_name='".$data[4]."' and board_status=1");// user_id!='".$_SESSION['logic_erp']['user_id']."'");
	
	//$current_plan_user=return_field_value("user_id", "planning_board_status"," company_name='".$data[0]."' and location_name='".$data[1]."' and floor_name='".$data[4]."' and board_status=1");
	/*$current_plan_user=0;
	$sql=sql_select("select id,user_id,board_status from planning_board_status where  company_name='".$data[0]."' and location_name='".$data[1]."' and floor_name='".$data[4]."'");
	$current_planed_id=0;
	foreach($sql as $sdt)
	{
		if( $sdt[csf("board_status")]==1)
		{
			//$current_planed_id=$sdt[csf("id")];
			$current_plan_user=$sdt[csf("user_id")];
		}
		else
		{
			$current_planed_id=$sdt[csf("id")];
			//$current_plan_user=$sdt[csf("user_id")];
		}
	}
	*/
	
	$lock_operation=$data[5];
	if( $current_plan_user!=$_SESSION['logic_erp']['user_id'] && ($current_plan_user*1)!=0 )
	{ 
		//echo "Some one else, lock update";
		$lock_operation=1;
	}
	else if( ($current_plan_user*1)==0 )
	{
		//echo "I am the one new";
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$rID1=execute_query("delete from planning_board_status where board_status=0 and user_id=".$_SESSION['logic_erp']['user_id']."");
		$id=return_next_id( "id", "planning_board_status", 0 ) ;
			
		$field_array="id,company_name,location_name,floor_name,board_status,user_id";
		$data_array="(".$id.",".$data[0].",".$data[1].",".$data[4].",1,".$_SESSION['logic_erp']['user_id'].")";
		//if($current_planed_id==0)
			$rID=sql_insert("planning_board_status",$field_array,$data_array,1);
		// else
		 	
			
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				///echo "0**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				//echo "10**".$rID;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);  
			 
			}
		else
			{
				oci_rollback($con);
			 
			}
		}
		//echo "SDSD";
	}
	 
	
	//die;
	
	//if($plan_uses_sts!=1)
		//check_table_status( $data[3],0);
	//var_dump($plan_uses_sts);
	
	if( $data[4]!=0 ) { $strnam=" and  floor_name= '".$data[4]."'"; $str=" and  floor_id=  '".$data[4]."'";	 }	
	
	 
	$new_line_resource=return_library_array("select id,line_name from lib_sewing_line where company_name='$data[0]'  and location_name='$data[1]' $strnam   order by sewing_line_serial ", "id","line_name");
	
	foreach($new_line_resource as $ids=>$vals)
		$line_names_ids[$ids]=$ids;
	//echo implode(",",$new_line_resource);
	
	$resource_allocation_type= return_field_value("auto_update", "variable_settings_production"," company_name='".$data[0]."' and variable_list=23");
	
 //	$sewing_resource=return_library_array(" select id,id from lib_sewing_line where company_name='$data[0]'  and location_name='$data[1]' $strnam  order by sewing_line_serial ", "id","line_name");
	
	if( $resource_allocation_type==1 )
	{
		$new_line_res=return_library_array("select id,line_number from  prod_resource_mst where company_id='$data[0]' and location_id='$data[1]' $str and is_deleted=0 order by id ", "id", "line_number");
		$sewing_resource=array();
		foreach($new_line_res as $key=>$line)
		{
			$sewing_resource[$key] = (int)($line);
			$res_all[(int)($line)]=$key;
			$line_allocated[$key]=$key;
		}	 
	}
	
	if( $data[2]=="") $data[2]=date("d-m-Y",time());
	$from_date=date("Y-m-d", strtotime($data[2]));
	$days_forward=120;
	$width=(30*$days_forward)+350;
	$to_date=add_date($from_date,$days_forward);
	
	if($db_type==2)
	{
		$from_date = change_date_format( str_replace("'","",trim($from_date)),'','',1);
		$to_date = change_date_format( str_replace("'","",trim($to_date)),'','',1);
	}
 	
	$weeklist=return_library_array("select week,week_date from  week_of_year where week_date between '$from_date' and '$to_date' order by week_date ", "week_date","week");
	if(count($line_names_ids)<1) { echo "Please use Resource Allocation for Line."; disconnect($con); die;}
	
	 $sql="select id,line_id,po_break_down_id,plan_id,start_date,start_hour,end_date,end_hour,duration,plan_qnty,comp_level,first_day_output,increment_qty,terget,day_wise_plan,company_id,location_id,item_number_id,off_day_plan,order_complexity,ship_date,extra_param from ppl_sewing_plan_board where company_id='".$data[0]."' and location_id='".$data[1]."'  and (start_date between '".$from_date."' and '".$to_date."'  or  end_date between '".$from_date."' and '".$to_date."') or ( start_date < '".$from_date."' and end_date> '".$to_date."') and line_id in ( ".implode(",",$line_names_ids)." )   order by po_break_down_id";
	$sql_data=sql_select($sql);    //id in (8,31)  and 
	$m=0;
	$plan_data="";
	foreach($sql_data as $rows)
	{
		$m++;
		$crossed_plan=0;
		$actual_start_date=$rows[csf("start_date")];
		$actual_end_date=$rows[csf("end_date")];
			//if Any plan cross the dash board, before or after starts
		$po_break_down_array[$rows[csf("po_break_down_id")]]=$rows[csf("po_break_down_id")];
		//echo (change_date_format( str_replace("'","",trim($rows[csf("start_date")])),'','',0))."==". (change_date_format( str_replace("'","",trim($from_date)),'','',0)).'----';
		
		if(strtotime(change_date_format( str_replace("'","",trim($rows[csf("start_date")])),'','',0)) < strtotime(change_date_format( str_replace("'","",trim($from_date)),'','',0)))
		{
			$dur=datediff("d",change_date_format( str_replace("'","",trim($rows[csf("start_date")])),'','',0),change_date_format( str_replace("'","",trim($from_date)),'','',0));
			$actual_start_date=$rows[csf("start_date")];
			$rows[csf("start_date")]=$from_date;
			$rows[csf("duration")]=$rows[csf("duration")]-$dur+1;
			$crossed_plan=1;
			$daysItem=explode(",",$rows[csf("day_wise_plan")]);
			array_splice($daysItem,0,$dur-1);
			$rows[csf("day_wise_plan")]=implode(",",$daysItem);
		}
		if(strtotime(change_date_format( str_replace("'","",trim($rows[csf("end_date")])),'','',0)) > strtotime(change_date_format( str_replace("'","",trim($to_date)),'','',0)))
		{
			$dur=datediff("d",change_date_format( str_replace("'","",trim($rows[csf("end_date")])),'','',0),change_date_format( str_replace("'","",trim($to_date)),'','',0));
			 $actual_end_date=$rows[csf("end_date")];
			 $rows[csf("end_date")]=$to_date;
			 $rows[csf("duration")]=$rows[csf("duration")]-$dur+1;
			 $crossed_plan=1;
			 $daysItem=explode(",",$rows[csf("day_wise_plan")]);
			 array_splice($daysItem,$dur-1);
			 $rows[csf("day_wise_plan")]=implode(",",$daysItem);
		}
		//if Any plan cross the dash board, before or after ends
		//echo $crossed_plan."==";
		if($po_number_array[$rows[csf("po_break_down_id")]]=="")
		{
			$number=return_field_value("po_number","wo_po_break_down", "id='".$rows[csf("po_break_down_id")]."'");
			$po_number_array[$rows[csf("po_break_down_id")]]=$number;
		}
		if($rows[csf("ship_date")]=='' || $rows[csf("ship_date")]==0) $rows[csf("ship_date")]="99999999"; else $rows[csf("ship_date")]=$rows[csf("ship_date")];
		if( $plan_data=="")
				$plan_data=$rows[csf("id")]."**".$rows[csf("line_id")]."**".$rows[csf("po_break_down_id")]."**".$rows[csf("plan_id")]."**".change_date_format( $rows[csf("start_date")])."**".$rows[csf("start_hour")]."**".change_date_format( $rows[csf("end_date")])."**".$rows[csf("end_hour")]."**".$rows[csf("duration")]."**".$rows[csf("plan_qnty")]."**".$rows[csf("comp_level")]."**".$rows[csf("first_day_output")]."**".$rows[csf("increment_qty")]."**".$rows[csf("terget")]."**".$crossed_plan."**".change_date_format($actual_start_date)."**".change_date_format($actual_end_date)."**".$number."**".$rows[csf("day_wise_plan")]."**".$rows[csf("item_number_id")]."**".$rows[csf("company_id")]."**".$rows[csf("location_id")]."**".$rows[csf("off_day_plan")]."**".$rows[csf("order_complexity")]."**".$rows[csf("ship_date")]."**". str_replace("__","**",$rows[csf("extra_param")]);
		else
			$plan_data .="**__**".$rows[csf("id")]."**".$rows[csf("line_id")]."**".$rows[csf("po_break_down_id")]."**".$rows[csf("plan_id")]."**".change_date_format( $rows[csf("start_date")])."**".$rows[csf("start_hour")]."**".change_date_format( $rows[csf("end_date")])."**".$rows[csf("end_hour")]."**".$rows[csf("duration")]."**".$rows[csf("plan_qnty")]."**".$rows[csf("comp_level")]."**".$rows[csf("first_day_output")]."**".$rows[csf("increment_qty")]."**".$rows[csf("terget")]."**".$crossed_plan."**".change_date_format($actual_start_date)."**".change_date_format($actual_end_date)."**".$number."**".$rows[csf("day_wise_plan")]."**".$rows[csf("item_number_id")]."**".$rows[csf("company_id")]."**".$rows[csf("location_id")]."**".$rows[csf("off_day_plan")]."**".$rows[csf("order_complexity")]."**".$rows[csf("ship_date")]."**". str_replace("__","**",$rows[csf("extra_param")]);
			
			//5**169**4631**5**11-03-2015 ****21-03-2015**0**11**10000**1**1000**100**1200**0**11-03-2015**21-03-2015**B/19032015**1000,1100,0,1200,1200,1200,1200,1200,1200,0,700**2**1**1****90

		for($k=0; $k<$rows[csf("duration")]; $k++)
		{
			$dates=add_date($rows[csf("start_date")],$k);
			$po_plan_info[$rows[csf("po_break_down_id")]][$rows[csf("line_id")]][change_date_format( str_replace("'","",trim($dates)),'','',0)]=$rows[csf("id")];
		}
		$plan_ids[$rows[csf("id")]]=$rows[csf("id")];
	}
	if(count($plan_ids)>0) //{ echo "No available plan to display"; die; }
	{
		$sql="select plan_id,po_dtls from  PPL_SEWING_PLAN_BOARD_POWISE where plan_id in (".implode(",",$plan_ids).")";
		 
		if(count($plan_ids)>0)$sql_data=sql_select($sql);
		foreach($sql_data as $rows)
		{
			if($group_po_det[$rows[csf("plan_id")]]=="")
				$group_po_det[$rows[csf("plan_id")]]=$rows[csf("po_dtls")];
			else
				$group_po_det[$rows[csf("plan_id")]] .="|*|".$rows[csf("po_dtls")];
		}
	}
	/*CREATE TABLE `logic_erp_3rd_version`.`ppl_sewing_plan_board_powise` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
`plan_id` INT( 11 ) NOT NULL ,
`po_id` INT( 11 ) NOT NULL DEFAULT '0',
`plan_qnty` INT( 11 ) NOT NULL DEFAULT '0',
`po_dtls` VARCHAR( 500 ) NOT NULL ,
`item_number_id` INT( 11 ) NOT NULL DEFAULT '0',
PRIMARY KEY ( `id` )
) ENGINE = InnoDB;*/
	 //List all offdays
//	$sql="select a.mst_id,a.month_id,a.date_calc,a.day_status,comapny_id,capacity_source,location_id from  lib_capacity_calc_dtls a, lib_capacity_calc_mst b where b.id=a.mst_id and date_calc between '".$from_date."' and '".$to_date."' and comapny_id='$data[0]' and location_id='$data[1]'  and day_status=2";
	$sql="select a.mst_id,a.month_id,a.date_calc,a.day_status,comapny_id,capacity_source,location_id from  lib_capacity_calc_dtls a, lib_capacity_calc_mst b where b.id=a.mst_id and date_calc between '".$from_date."' and '".$to_date."' and comapny_id='$data[0]' and day_status=2";
	
	$sql_data=sql_select($sql);
	foreach($sql_data as $rows)
	{
		$day_status[change_date_format( str_replace("'","",trim($rows[csf("date_calc")])),'','',0)]=$rows[csf("day_status")];
		$day_status_days[change_date_format( str_replace("'","",trim($rows[csf("date_calc")])),'','',0)]=change_date_format( str_replace("'","",trim($rows[csf("date_calc")])),'','',0);
	}
	//List all off days ends
	
	//echo $resource_allocation_type;
	if( count($po_break_down_array)>0 )
	{
		
		if($resource_allocation_type!=1)
		{
			if(count($line_names_ids)<1) {echo "No Line Available"; disconnect($con); die;}
			$sql="select po_break_down_id,sum(production_quantity) as production_quantity,production_date,sewing_line,company_id,location from   pro_garments_production_mst where production_type=5 and po_break_down_id in (".implode(",",$po_break_down_array).") and status_active=1 and is_deleted=0 and production_date between '".$from_date."' and '".$to_date."'  and sewing_line in ( ".implode(",",$line_names_ids)." )  group by production_date,po_break_down_id,sewing_line, company_id,location order by sewing_line,po_break_down_id,production_date";
		}
		else
		{
			if(count($line_allocated)<1) {echo "No Allocated Line Available"; disconnect($con); die;}
			$sql="select po_break_down_id,sum(production_quantity) as production_quantity,production_date,sewing_line,company_id,location from   pro_garments_production_mst where production_type=5 and po_break_down_id in (".implode(",",$po_break_down_array).") and status_active=1 and is_deleted=0 and production_date between '".$from_date."' and '".$to_date."'  and sewing_line in ( ".implode(",",$line_allocated)." )  group by production_date,po_break_down_id,sewing_line, company_id,location order by sewing_line,po_break_down_id,production_date";
		}
		// print_r($sewing_resource);  
		
		$sql_data=sql_select($sql);
		$k=0;
		//print_r($po_plan_info);die;
		foreach($sql_data as $rows)
		{
			 //echo $po_plan_info[$rows[csf("po_break_down_id")]][$sewing_resource[$rows[csf("sewing_line")]]][change_date_format( str_replace("'","",trim($rows[csf("production_date")])),'','',0)]; die;
			//print_r($sewing_resource);  
			if($resource_allocation_type==1)
			{
				$production_details[$po_plan_info[$rows[csf("po_break_down_id")]][$sewing_resource[$rows[csf("sewing_line")]]][change_date_format( str_replace("'","",trim($rows[csf("production_date")])),'','',0)]]+=$rows[csf("production_quantity")];
			//echo change_date_format( str_replace("'","",trim($rows[csf("production_date")])));die;
			//[$po_plan_info[$rows[csf("po_break_down_id")]]][$po_plan_info[$rows[csf("sewing_line")]][$sewing_resource[$rows[csf("sewing_line")]]][change_date_format( str_replace("'","",trim($rows[csf("production_date")])),'','',0)]]['prod_qnty']+=$rows[csf("production_quantity")];
			
				$production_details_arr[$sewing_resource[$rows[csf("sewing_line")]]][$rows[csf("po_break_down_id")]][change_date_format( str_replace("'","",trim($rows[csf("production_date")])),'','',0)]+= $rows[csf("production_quantity")];
				
			}
			else
			{
 				$production_details[$po_plan_info[$rows[csf("po_break_down_id")]][$rows[csf("sewing_line")]][change_date_format( str_replace("'","",trim($rows[csf("production_date")])),'','',0)]]+=$rows[csf("production_quantity")];
			
			//[$po_plan_info[$rows[csf("po_break_down_id")]]][$po_plan_info[$rows[csf("sewing_line")]][$sewing_resource[$rows[csf("sewing_line")]]][change_date_format( str_replace("'","",trim($rows[csf("production_date")])),'','',0)]]['prod_qnty']+=$rows[csf("production_quantity")];
			
				$production_details_arr[$rows[csf("sewing_line")]][$rows[csf("po_break_down_id")]][change_date_format( str_replace("'","",trim($rows[csf("production_date")])),'','',0)]+= $rows[csf("production_quantity")];
			}
			 
		}
	}
	//print_r($sewing_resource);  
	// print_r($production_details_arr); 
	foreach($production_details_arr as $id=>$val)
	{
		if($production_details_string=="")
			$production_details_string=$id."_".$val;
		else
			$production_details_string .="**".$id."_".$val;
	}
	
	$tot_month=datediff("m",$from_date,$to_date)+1;
	for( $i=0; $i<$tot_month; $i++ )
	{
		$next_month=month_add($from_date,$i);
		$ldays=cal_days_in_month(CAL_GREGORIAN, date("m",strtotime($next_month)), date("Y",strtotime($next_month)))."-".date("m",strtotime($next_month))."-". date("Y",strtotime($next_month)); // 31
		
		if($i==0) $days[$i]=datediff("d", $from_date, $ldays);
		else if($i==$tot_month-1) $days[$i]=datediff("d", "01-".date("m",strtotime($next_month))."-". date("Y",strtotime($next_month)), $to_date);
		else $days[$i]= cal_days_in_month(CAL_GREGORIAN, date("m",strtotime($next_month)), date("Y",strtotime($next_month)));
	}
	
	if($production_details_arr=='') $production_details_arr='{}';
	if($production_details=='') $production_details='{}';
	
	?>
    <table cellpadding="0" width="<? echo $width; ?>"  cellspacing="0" border="1"  id="bodycont" >
 	<? 
	//	if( $lock_operation==1) $str_lock='<span style="color:red; font:9px; left:0px;position:relative">This Board opened in read only mode. Please dont change anything, Save option is disabled. User:'.$user_list[$lock_operation].'</span>'; 
	 if( $lock_operation==1)
	 {
		 $str_lock=' This board is locked by user: '. $user_list[$current_plan_user].' . Please wait or Release board to activate Save Button. '; 
		$bg_color=' style="background-color:#FE7275" ';
	 }
	?>
     
	<tr>
    	<td class="top_header" >
        <input type="button" class="formbutton" style="width:120px;" onClick="showRightaBar('<? echo $plan_uses_sts; ?>')" value="Show Options">
        <input type="hidden" id="off_day_data" value="<? echo implode(", ",($day_status_days));?>">
        <input type="hidden" id="old_plan_data" value="<? echo  $plan_data; //$single_string; ?>">
        <input type="hidden" id="txt_production_data" value='<? echo json_encode($production_details_arr); ?>'>
        <input type="hidden" id="txt_production_summary" value='<? echo json_encode($production_details); ?>'>
        <input type="hidden" id="txt_overlaaped_id">
        <input type="hidden" id="txt_lock_operation" value="<? echo $lock_operation; ?>">
        <input type="hidden" id="group_po_det" value='<? echo json_encode($group_po_det); ?>'>
        <input type="hidden" id="txt_from_to_date" value='<? echo date("Y-m-d",strtotime($from_date))."__".date("Y-m-d",strtotime($to_date)); ?>'>
        
        </td>
         <?  	for($j=0; $j<count($days); $j++)
				{
					$next_month=month_add($from_date,$j);
					$tdate=date("d-M",strtotime(add_date($from_date,$j-1)));
					?>
					<td  <? if( $bg_color!='' && $j==0 ) echo $bg_color; else echo 'class="top_header" '; ?> <? //if($j==0) echo $bg_color; ?> title="<? if($j==0)  echo $str_lock; ?>" align="center" colspan="<? echo $days[$j]; ?>" ><? if($j==0) echo $str_lockss; echo  date("M Y",strtotime($next_month)); ?></td>
					<?
				}
				for($j=1; $j<$days_forward; $j++)
				{
					if($db_type==2) $dat=date("d-M-y",strtotime(add_date($from_date,$j-1))); else $dat=date("Y-m-d",strtotime(add_date($from_date,$j-1)));
					
					$weekspan[$weeklist[$dat]]++;	
					 	
				}  
			?>
    </tr> 
    <?

    if(count($weekspan)>1)
    {
		?>
    <tr>
    	<td class="top_header">&nbsp;</td>
         <?  	foreach($weekspan as $sp=>$dte)
				{
					?>
					<td class="top_header" colspan="<? echo $dte; ?>" ><? echo  "Week ".$sp; ?></td>
					<?
				}
			?>
    </tr>
    <?
    }
    ?>
	<tr>
    	<td class="top_headerss"><input type="button" class="formbutton" style="width:120px;" onClick="fnc_save_planning()" value="Save Board"><br>Line</td>
         <?  //style="width:150px"
		 		$width=30;
		 		
				for($j=1; $j<$days_forward; $j++)
				{
					$tdate=date("d",strtotime(add_date($from_date,$j-1)));
					$dayname=substr(date("D", strtotime(add_date($from_date,$j-1))),0,1);
					
					if( $day_status[date("d-m-Y",strtotime(add_date($from_date,$j-1)))]==2 ) $head_class="top_headerss_off"; else $head_class="top_headerss";
					?>
					<td class="<? echo $head_class; ?>" style="width:<? echo $width."px"; ?>"><? echo $tdate; ?><br><? echo $dayname; ?></td>
					<?
				}
			?>
    </tr>
	<?  
	
		foreach( $new_line_resource as $i=>$line)
		{
			?>
            <tr>
            <?
				$k=1;
				for( $j=0; $j<$days_forward; $j++ )
				{
					$title="";$txt='';
					$dayname=date("l", strtotime(add_date($from_date,$j-1)));
					if( $j==0 ) { $width=""; $idd=""; $text="".$line; $tdcls="left_header";  } 
					else 
					{ $tdate=date("dmY",strtotime(add_date($from_date,$j-1))); $idd="-".$i."-".$tdate; $width="20"; $text="&nbsp;"; $tdcls="verticalStripes1"; $title="".$line.",Date:".date("d-M-Y",strtotime(add_date($from_date,$j-1)))."; ".$dayname; 
					$txt=date("d-m",strtotime(add_date($from_date,$j-1)))."<br>".$line;
					}
					//SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=3 and b.mail_user_setup_id=c.id and a.company_id=1
					
					if($day_status[date("d-m-Y",strtotime(add_date($from_date,$j-1)))]==2) $tdcls="verticalStripes_off";
					
					//if( $k==7 ) { $tdcls="verticalStripes_off"; $k=0; }
					
					?>
					<td plan_group="0" onMouseOver="showmenu( this.id )" title="<? echo $title; ?>" onClick="paste_plan(this.id)"  class="<? echo $tdcls; ?>" id="tdbody<? echo $idd; ?>" name="tdbody<? echo $i.$j; ?>" align="center" valign="middle"><? echo $text; ?><span style="font-size:7px; color:#333;"><? echo $txt; ?></span> </td>
					<?
					$k++;
				}
			?>
            </tr>
           <!-- <tr> -->
            	<?
				/*$k=1;
				for( $j=0; $j<$days_forward; $j++ )
				{
					$title="";$txt='';
					$dayname=date("l", strtotime(add_date($from_date,$j-1)));
					if( $j==0 ) { $width=""; $idd=""; $text="".$line; $tdcls="left_header"; } 
					else 
					{
						$tdate=date("dmY",strtotime(add_date($from_date,$j-1))); $idd="-".$i."-".$tdate; $width="20"; $text="&nbsp;"; $tdcls="verticalStripes1"; $title="".$line.",Date:".date("d-M-Y",strtotime(add_date($from_date,$j-1)))."; ".$dayname; 
						$txt=date("d-m",strtotime(add_date($from_date,$j-1)))."<br>".$line;
					}
					//SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=3 and b.mail_user_setup_id=c.id and a.company_id=1
					
					if($day_status[date("d-m-Y",strtotime(add_date($from_date,$j-1)))]==2) $tdcls="verticalStripes_off";
					
					//if( $k==7 ) { $tdcls="verticalStripes_off"; $k=0; }
					
					?>
					<td class="verticalStripes_blank" id="tdbody_blank<? echo $idd; ?>"></td>
					<?
					$k++;
				}*/
			?>
           <!-- </tr>-->
            
            
            <?
		}
	?>
</table>
    <?
die;
}

if ($action=="order_popup")
{
  	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
	if( $floor_id!=0 ) { $strnam=" and  floor_name= '".$floor_id."'"; $str=" and  floor_id=  '".$floor_id."'";	 }	
	$new_line_res=return_library_array("select id,line_name from lib_sewing_line where company_name='$company_id'  and location_name='$location_id' $strnam   order by sewing_line_serial ", "id","line_name");
	
	$pdate=substr($pdate,0,2)."-".substr($pdate,2,2)."-".substr($pdate,4,4);
	
?>
<script>
<?
	$new_line_res_js= json_encode($new_line_res); 
	echo "var new_line_res = ". $new_line_res_js . ";\n";
	
	$complexity_levels= json_encode($complexity_level_data); 
	echo "var complexity_levels = ". $complexity_levels . ";\n";
	
	$garments_item_arr= json_encode($garments_item); 
	echo "var garments_item = ". $garments_item_arr . ";\n";
	
	
	?>
	function set_checkvalue()
	{
		if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
		else document.getElementById('chk_job_wo_po').value=0;
	}
	function set_grouppo()
	{
		if(document.getElementById('chk_group_po_Plan').value==0) document.getElementById('chk_group_po_Plan').value=1;
		else document.getElementById('chk_group_po_Plan').value=0;
	}
	var selected_id=new Array();
	var selected_name=new Array();
	 var selected_values=new Array();
	var row_color=new Array();
	var total_po=0;
	var yet_to_plan=0;
	var total_prod=0;
	var plan_qnty=0;
	function check_all_data()
	{
		selected_id=new Array();
		selected_name=new Array();
		row_color=new Array();
		total_po=0;
		yet_to_plan=0;
		total_prod=0;
		
		$('#tbl_list_search tbody tr').each(function( index ) {
		   var tid=this.id;
		   //alert(index);
		   if(tid)
		   {
			   $('#'+tid).trigger('click');
		   }
		});

		 
	}
	
	function js_set_value( job_no, img, gsd, compl, buyern, row_id )
	{
		 
		if( document.getElementById('chk_group_po_Plan').value==0)
		{
			$('#search_div').css('visibility','collapse');
			$('#search_panel').css('visibility','collapse');
			$('#search_div_line').css('visibility','visible');
			var jdata=job_no.split("_"); 
			
			$('#txt_plan_qnty').removeAttr('disabled');
			//job_no,id,po_quantity,shipment_date,po_number,shipment_date,style_ref_no,item_name,order_smv,yettoplan,tna_start,tna_end
			$('#order').html(jdata[4]);
			$('#shipdate').html(jdata[5]);
			$('#orderqnty').html(jdata[2]);
			$('#yettoplan').html(jdata[9]);
			$('#txt_plan_qnty').val(jdata[9]);
			
			$('#tna_start_td').html(jdata[10]);
			$('#tna_end_td').html(jdata[11]);
			$('#production_qnty').html(jdata[12]);
			
			$('#order_smv').html(jdata[7]);
			$('#item_name').html(garments_item[jdata[8]]);
			$('#selected_po_gsd').val(gsd);  // GSD TARGET
			$('#order_compl').val(compl);  // GSD TARGET
			
			$('#imgeloc').attr('src',"../../"+img);
			//imgeloc
			$('#cbo_complexity_selection').val(compl); 
			fill_complexity(compl);
			
			document.getElementById('selected_job').value=job_no+"_"+compl+"_"+buyern;
			
		}
		else
		{ 
			var jdata=job_no.split("_");
			if( row_color[row_id]==undefined ) row_color[row_id]=$('#tr_id'+row_id).attr('bgcolor');
			 
			var job_nsso=jdata.join("_")
			
			if( $('#tr_id'+row_id).attr('bgcolor')=='#FF9900')
				$('#tr_id'+row_id).attr('bgcolor',row_color[row_id])
			else
				$('#tr_id'+row_id).attr('bgcolor','#FF9900')
			//job_no,id,po_quantity,shipment_date,po_number,shipment_date,style_ref_no,item_name,order_smv,yettoplan,tna_start,tna_end
			if( jQuery.inArray( row_id, selected_id ) == -1 ) {
				selected_id.push( row_id );
				selected_name.push(job_no );
				selected_values[row_id]=job_no ;
				total_po+=(jdata[2]*1);
				yet_to_plan+=(jdata[9]*1);
				total_prod+=(jdata[12]*1);
				//plan_qnty+=($('#txt_plan_qnty'+row_id).val()*1);
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] ==row_id ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				total_po=total_po-(jdata[2]*1);
				yet_to_plan=yet_to_plan-(jdata[9]*1);
				//total_prod=total_prod-(jdata[12]*1);
				//plan_qnty=plan_qnty-($('#txt_plan_qnty'+row_id).val()*1);
				selected_values[row_id]='';
			}
			var id ='';
			var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '|*|';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 3 );
			$('#selected_job').val( name );
			//alert(total_po);
			return;
			 
			
			//document.getElementById('selected_job').value=job_no+"_"+compl+"_"+buyern;
		}
		
		
		//parent.emailwindow.hide();
	}
	function show_hide( fm )
	{
		//OG-16-00646_10085_700_02-11-2016_4600166551_02-11-2016_838492_5_2_700_31-07-2016_30-09-2016__0_BIZZBEE
		plan_qnty=0;
		//alert(fm)
		if(fm==0)
		{
			var name = '';
			$.each(selected_id, function( index, value ) {
			 	var tmpd=selected_values[value].split("_");
			 	tmpd[9]=($('#txt_plan_qnty'+value).val()*1);
			 	plan_qnty+=tmpd[9];
			  	name += tmpd.join("_") + '|*|';
			});
			name = name.substr( 0, name.length - 3 );
			//alert(name);
			$('#selected_job').val( name );
			$('#txt_plan_qnty').attr('disabled','disabled');
			$('#search_div').css('visibility','collapse');
			$('#search_panel').css('visibility','collapse');
			$('#search_div_line').css('visibility','visible');
			
			$('#order').html('');
			$('#shipdate').html('');
			$('#orderqnty').html(total_po);
			$('#yettoplan').html(yet_to_plan);
			$('#txt_plan_qnty').val(plan_qnty);
			
			$('#tna_start_td').html('');
			$('#tna_end_td').html('');
			$('#production_qnty').html(total_prod);
			
			$('#order_smv').html('');
			$('#item_name').html('');
			$('#selected_po_gsd').val('');  // GSD TARGET
			$('#order_compl').val(0);  // GSD TARGET
			
			$('#imgeloc').attr('src',"../../"+img);
			//imgeloc
			$('#cbo_complexity_selection').val(0); 
			fill_complexity(0);
			
		}
		if(fm==1)
		{
			var rows =document.getElementById('tbl_line').rows.length-2;
			//alert(rows)
			if((rows*1)<2) { alert('Please Add at least one line.');return; }
			$('#search_div_line').css('visibility','collapse');
			$('#search_div_lag').css('visibility','visible');
			$('#txt_target').val($('#selected_po_gsd').val());
			 //alert($('#selected_po_gsd').val())
		}
		else if(fm==2)
		{
			var planData="";
			var rows =document.getElementById('tbl_line').rows.length-2;
			var totplan=0;
			
			for( var k=1; k<rows; k++ )
			{
				if( ( $('#txt_overlapped_qnty__'+k).html()*1)<1) $('#txt_overlapped_qnty__'+k).html('0')
				
				if(planData!="") planData=planData+"****";
				planData=planData+$('#cbo_line_selection__'+k).val()+"**"+$('#txt_line_date__'+k).html()+"**"+$('#txt_plan_qnty__'+k).html()+"**0**0**1**"+$('#cbo_off_day_plan__'+k).val()+"**"+$('#txt_overlapped_qnty__'+k).html()+"**"+$('#tna_end_td').html();
				totplan+=($('#txt_plan_qnty__'+k).html()*1);
				//alert($('#txt_overlapped_qnty__'+k).html())
			}
			if(($('#txt_first_day').val()*1)<1) { alert('Please Provide First day output.'); return; }
			if(($('#txt_target').val()*1)<1) { alert('Please Provide Target output.'); return; }
			//alert(rows+"="+planData);
			var jdata= document.getElementById('selected_job').value.split("_"); //job_no,id,po_quantity,shipment_date,po_number,shipment_date,style_ref_no
			
			if( $('#chk_group_po_Plan').attr('checked')!="checked")
			{
				jdata[9]=(totplan*1);
			 	var orderinfo= jdata.join("_");
			}
			else
				var orderinfo= document.getElementById('selected_job').value;
			
			planData=orderinfo+"______"+planData+"______"+$('#cbo_complexity_selection').val( )+"**"+$('#txt_first_day').val( )+"**"+$('#txt_increment').val( )+"**"+$('#txt_target').val( )+"**"+jdata[1]+"**0"; 
			//alert(planData);
			document.getElementById('selected_job').value=planData;
			parent.emailwindow.hide();
		}
	}
	
	function add_line()
	{
		if( $('#txt_plan_qnty').val()*1<1)
		{
			alert('Please enter plan quantity.');
			return;
		}
		var rowsss =document.getElementById('tbl_line').rows.length-2;
		for( var kk=1; kk<rowsss; kk++ )
		{
			if( $('#cbo_line_selection').val() == $('#cbo_line_selection__'+kk).val() &&  $('#txt_line_date').val()==$('#txt_line_date__'+kk).html() )
			{	
				alert('Sorry! Duplicate data selected.');
				return;
			}
		}
		
		var rowCount =document.getElementById('tbl_line').rows.length-2;
		if (rowCount%2==0)  
			var bgcolor="#E9F3FF";
		else
			var bgcolor="#FFFFFF";
		 var row_idss= $('#up_row_id').val();
		 if ( row_idss=="" ) var rowCount=(rowCount*1); else rowCount=row_idss;
		if($('#cbo_off_day_plan').val()==1) var str="Yes"; else var str="No"; 
		
		
		var new_html='<tr  bgcolor="'+ bgcolor +'" id="row_' + rowCount + '" onclick="set_update_row(' + rowCount + ',0)" style="cursor:pointer">'
					+ '<td id="txt_line' + rowCount + '" width="20">'+ new_line_res[$('#cbo_line_selection').val()] +'</td>'	
					+ '<td id="txt_line_date__' + rowCount + '" width="100">'+ $('#txt_line_date').val() +'</td>'
					+ '<td id="txt_plan_qnty__' + rowCount + '" width="100">'+ $('#txt_plan_qnty').val() +'</td>'
					+ '<td id="cbo_off_day_data__' + rowCount + '" width="100">'+ str +'</td>'
					+ '<td id="txt_overlapped_qnty__' + rowCount + '" width="100">'+ $('#txt_overlapped_qnty').val() +'</td>'
					+'<td id="blank_' + rowCount + ' onclick="set_update_row(' + rowCount + ',1)"><input type="button" name="" value="Clear"  id="btn_clear_' + rowCount + '"  class="formbutton" onclick="set_update_row(' + rowCount + ',1)"/><input type="hidden" id="cbo_line_selection__' + rowCount + '" value="'+$('#cbo_line_selection').val()+'" /><input type="hidden" id="cbo_off_day_plan__' + rowCount + '" value="'+$('#cbo_off_day_plan').val()+'" /></td></tr>';
			if( row_idss=="")			
					$("#tbl_line tbody").append(new_html);
			else
				$('#row_' + rowCount).replaceWith(new_html);
				$('#up_row_id').val('');
				$('#td_total').html( ($('#td_total').html()*1)+$('#txt_plan_qnty').val()*1  ); 
				
	}
	
	function set_update_row(id, is_clear)
	{ 
	//alert(id)
		if(is_clear==0)
		{
			$('#up_row_id').val(id);
			$("#row_"+id +" td").each(function() {
				var tdid2=$(this).attr('id');
				var tdid=tdid2.split("__");
				
				if(!tdid[1] && tdid[1]!='undefined')
					var d=1;//alert("d=="+tdid[1]);
				else
				{
					$('#'+tdid[0]).val($(this).html());
				}
				// $(this).html('');
			});
			$('#cbo_line_selection').val($('#cbo_line_selection__'+id).val());
			$('#cbo_off_day_plan').val($('#cbo_off_day_plan__'+id).val());
			
		}
		//else
		//{
			$('#td_total').html( ($('#td_total').html()*1)-$('#txt_plan_qnty__'+id).html()*1  );
				
			$("#row_"+id +" td").each(function() {
				 $(this).html('');
			});
			$("#row_"+ id).remove()
			$('#up_row_id').val('');
		//}
	}
	
	function open_back( fm )
	{
		if(fm==1)
		{
			$('#search_div').css('visibility','visible');
			$('#search_panel').css('visibility','visible');
			$('#search_div_line').css('visibility','collapse');
		}
		else if(fm==2)
		{
			$('#search_div_line').css('visibility','visible');
			//$('#').css('visibility','visible');
			$('#search_div_lag').css('visibility','collapse');
		}
	}
	
	function fill_complexity(vid)
	{
		 if(!vid) var vid=0; 
  
		$('#txt_first_day').val( complexity_levels[vid]['fdout'] );
		$('#txt_increment').val( complexity_levels[vid]['increment'] );
		
		if( $('#selected_po_gsd').val()=='')
			$('#txt_target').val( complexity_levels[vid]['target'] );
		else
			$('#txt_target').val( $('#selected_po_gsd').val() );
	}
	
	function show_data()
	{
		selected_id=new Array();
		selected_name=new Array();
		selected_values=new Array();
		row_color=new Array();
		total_po=0;
		yet_to_plan=0;
		total_prod=0;
		plan_qnty=0;
		
		if($('#txt_job_prifix').val()=='' && $('#txt_order_search').val()==''){
			if (form_validation('cbo_company_mst*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false)
			{
				return;
			}
		}
		
		
		show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('garments_nature').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('cbo_date_type').value, 'create_po_search_list_view', 'search_div', 'planning_board_controller', 'setFilterGrid(\'tbl_list_search\',-1)')
	}
	
	function fnc_change_complx( str )
	{
		if( str==1 )
		{
			$('#fdouttd').css('width', '120');
			 
			$('#fdouttd').html('First Day Output');
			$('#tdincremnt').css('display', 'block');
			$('#tdincremnt_val').css('display', 'block');
			
			$('#cbo_complexity_selection').removeAttr('disabled'); 
			$('#cbo_complexity_selection').removeAttr('class'); 
			$('#txt_first_day').attr('class','text_boxes_numeric'); 
			$('#txt_first_day').css('width', '80');
			$('#txt_first_day').attr('title',''); 
			$('#txt_first_day').attr('placeholder',''); 
			
			$('#cbo_complexity_selection').val('');
			$('#txt_first_day').val('');
			$('#txt_increment').val('');
		}
		else
		{
			$('#cbo_complexity_selection').val('0');
			$('#fdouttd').attr('width','210');
			$('#fdouttd').html('Output % of Target (Comma Separated)');
			$('#tdincremnt').css('display', 'none');
			$('#tdincremnt_val').css('display', 'none');
			$('#cbo_complexity_selection').attr('disabled','disabled'); 
			$('#cbo_complexity_selection').removeAttr('class'); 
			$('#txt_first_day').attr('class','text_boxes'); 
			$('#txt_first_day').css('width','210');
			$('#txt_first_day').attr('title','Comma Separated values'); 
			$('#txt_first_day').attr('placeholder','Comma Separated values'); 
			
			$('#cbo_complexity_selection').val('');
			$('#txt_first_day').val('');
			$('#txt_increment').val('');
		}
		
		jQuery(".text_boxes").keypress(function(e) {
			 var c = String.fromCharCode(e.which);
			 //alert(c);
			 var allowed = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890.-,%@!/\<>?+[]{};: '; // ~ replace of Hash(#)()
			 if (e.which != 8 && e.which !=0 && allowed.indexOf(c) < 0) 
				return false; 
			
		});
		//alert( str )
		//set_all_onclick()
	}
	function change_date( vid )
	{ 
		if(vid==1)
			$('#date_caption').html('Shipment Date');
		else
			$('#date_caption').html('TNA Date');
	}
	
	function check_set(i)
	{
	//	$('#tr_id'+i).trigger('click');
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="980" align="center">
    	<tr>
        	<td align="center" width="100%">
            <div id="search_panel">
            	<table  cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" >
                        <thead>
                        <th width="150" colspan="2"> </th>
                         <th width="150" colspan="2" align="right"><?
							  
                               echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" );
                              ?> &nbsp;</th>
                        	<th colspan="2" align="left">&nbsp;&nbsp;
                              <input type="checkbox" value="0"  onClick="set_grouppo()" id="chk_group_po_Plan">Group PO Planning
                            </th>
                          <th width="150" colspan="2"> </th>
                        </thead>
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="80">Job No</th>
                        <th width="120">Order No</th>
                        <th width="120">Date Type</th>
                        <th width="200" id="date_caption">Shipment Date</th>
                        <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Exclude full shipment</th>           
                    </thead>
        			<tr>
                    	<td> 
                        <input type="hidden" id="selected_multiple_job">
                        <input type="hidden" id="selected_job">
                        <input type="hidden" id="garments_nature" value="<? echo $garments_nature; ?>">
							<?
							//echo $company_id;
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_id,"load_drop_down( 'planning_board_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                    </td>
                   	<td id="buyer_td">
                     <?
					 	echo create_drop_down( "cbo_buyer_name", 170,"select a.id,a.buyer_name from  lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$company_id and a.status_active=1 and a.is_deleted=0","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
						//echo create_drop_down( "cbo_buyer_name", 172, $blank_array,'', 1, "-- Select Buyer --" );
					?>	
                    </td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:80px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px"></td>
                    <td>
                     <?php
					 	$types=array(1=>"Shipment Date",2=>"TNA Date");
						echo create_drop_down( "cbo_date_type", 120, $types,'', 0, "-- Select Buyer --",'',"change_date(this.value)" );
					?>	
                    </td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center" rowspan="2">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_data()" style="width:100px;" />
                     <input type="button" class="formbutton" style="width:80px" value="Close" onClick="show_hide(0)" />
                     </td>
        		</tr>
                
                <tr>
                    <td  align="center" colspan="6" height="40" valign="middle">
                     <? 
                    	//echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
                     	echo load_month_buttons(1);  
					?>
                    </td>
                 </tr>
             </table>
             </div>
          </td>
        </tr>
        
        <tr>
            <td align="center" valign="top" id=""> 
				<div style="visibility:visible" id="search_div"></div>
                <div style="visibility:collapse" id="search_div_line">
                <img src="" id="imgeloc" height="" width="200" style="float:left">
                	<table width="630" class="rpt_table" cellspacing="0" rules="all">
                    	<tr>
                        	<td width="100">Order No</td><td width="130" id="order"></td>
                            <td width="100">Shipment Date</td><td width="100" id="shipdate"></td>
                            <td width="100">Order Quantity <input type="hidden" id="selected_po_gsd"><input type="hidden" id="order_compl"> </td><td id="orderqnty"></td>
                            
                        </tr>
                        <tr>
                        	 
                            <td width="100">Yet To Plan </td><td id="yettoplan"></td>
                            <td width="100">SMV </td><td id="order_smv"></td>
                            <td width="100">Item </td><td id="item_name"></td>
                        </tr>
                         <tr>
                        	 
                            <td width="100">TNA Start Date </td><td id="tna_start_td"></td>
                            <td width="100">TNA End Date </td><td id="tna_end_td"></td>
                            <td width="100">Produced Qnty</td><td id="production_qnty"></td>
                        </tr>
                       
                    </table>
                	<table width="350" class="rpt_table" id="tbl_line">
                    	<thead>
                         
                        <tr>
                            <th width="120"> Line No</th>
                             <th width="90">Start Date</th>
                             <th width="90">Quantity</th>
                             <th width="90">Offday Production</th>
                            <th width="90">Overlapped Qnty.</th>
                             <th>
                             	 <input type="hidden" id="up_row_id" />
                                 <input type="button" class="formbutton" style="width:80px" value="Go Back" onClick="open_back(1)" /> 
                            </th>
                        </tr>
                    	<tr>
                             <th width="120"><? echo create_drop_down( "cbo_line_selection", 110, $new_line_res, "", 1, "-- Select --", $pline, "",0 ); ?></th>
                             <th width="90"><input type="text" class="datepicker" value="<? echo $pdate; ?>" id="txt_line_date" style="width:80px" /></th>
                             <th width="90"><input type="text" class="text_boxes_numeric" id="txt_plan_qnty" style="width:80px" /></th>
                             <th width="120"><? echo create_drop_down( "cbo_off_day_plan", 70, $yes_no, "", 0, "-- Select --", 2, "",0 ); ?></th>
                             <th width="120"><input type="text" class="text_boxes_numeric" id="txt_overlapped_qnty" style="width:80px" /></th>
                             <th>
                             	<input type="button" class="formbutton" style="width:80px" value="Add" onClick="add_line()" /> 
                            </th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            	<th colspan="3" align="left">
                                <input type="button" class="formbutton" style="width:80px" value="Close" onClick="show_hide(1)" /></th>
                                <th colspan="2" align="center" id="td_total"></th>
                             	
                        </tfoot>
                    </table>
                </div>
                <div style="visibility:collapse" id="search_div_lag">
                	<table width="450" class="rpt_table" id="tbl_lag">
                    	<thead> 
                        	<tr>
                            	<th colspan="1">Complexity Type</th> 
                                <th colspan="3">
								<?
								echo create_drop_down( "cbo_complexity_type", 210, $complexity_type_tmp, "", 0, "-- Select --",'1', "fnc_change_complx(this.value)",0 ); ?></th>
                                <th id="td_gsd_target"></th>
                            </tr>
                         
                        <tr>
                            <th width="130">Complexity Level</th>
                             <th width="120" id="fdouttd">First Day Output</th>
                             <th width="90" id="tdincremnt">Increment</th>
                             <th width="90">Target</th>
                             <th><input type="button" class="formbutton" style="width:80px" value="Go Back" onClick="open_back(2)" /> </th>
                        </tr>
                        <tr>
                             <th width="120">
							 <? 
								 echo create_drop_down( "cbo_complexity_selection", 110, $complexity_level, "", 0, "-- Select --",'', "fill_complexity(this.value)",0 ); 
							 ?>
                             </th>
                             <th width="90"><input type="text" class="text_boxes" id="txt_first_day" style="width:80px" /></th>
                             <th width="90" id="tdincremnt_val"><input type="text" class="text_boxes_numeric" id="txt_increment" style="width:80px" /></th>
                             <th width="90"><input type="text" class="text_boxes_numeric" id="txt_target" style="width:80px" /></th>
                             <th>
                             	<input type="hidden" class="formbutton" style="width:80px" value="Add" onClick="add_line()" /> 
                            </th>
                        </tr>
                         
                    	
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                        	<th colspan="4" align="center"><input type="button" class="formbutton" style="width:80px" value="Close" onClick="show_hide(2)" /></th>
                        </tfoot>
                    </table>
                </div>
            </td>
        </tr>
    </table>    
    </form>
   </div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_po_search_list_view")
{
	$tim=time();
	$data=explode('_',$data);

	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	
	if($db_type==0)
	{
		$year_cond=" and SUBSTRING_INDEX(a.`insert_date`, '-', 1)=$data[7]";
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
		if($data[10]==1) // Shipment Date
		{
			if ($data[3]!="" &&  $data[4]!="") $tna_date_cond = "and shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $tna_date_cond ="";
		}
		else
		{
			if ($data[3]!="" &&  $data[4]!="") $tna_date_cond = "and task_start_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $tna_date_cond ="";
		}
	}
	
	if($db_type==2)
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";
		
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
		if($data[10]==1) // Shipment Date
		{
			if ($data[3]!="" &&  $data[4]!="") $tna_date_cond = "and shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $tna_date_cond ="";
		}
		else
		{
			if ($data[3]!="" &&  $data[4]!="") $tna_date_cond = "and task_start_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $tna_date_cond ="";
		}
	}
	
	$order_cond="";
	$job_cond=""; 
	
	if( $data[8]==1)
	{
	  if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num='$data[6]'";
	  if (trim($data[9])!="") $order_cond=" and b.po_number='$data[9]'  "; //else  $order_cond=""; 
	}

	else if($data[8]==4 || $data[8]==0)
	{
	  if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]%' "; //else  $job_cond=""; 
	  if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]%'  ";
	}

	else if($data[8]==2)
	{
	  if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '$data[6]%' "; //else  $job_cond=""; 
	  if (trim($data[9])!="") $order_cond=" and b.po_number like '$data[9]%'  ";
	}
	
	else if($data[8]==3)
	{
	  if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]'  "; //else  $job_cond=""; 
	  if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]'  ";
	}

	if (str_replace("'","",$data[6])!="") $tna_job=" and job_no like '%".$data[6]."%'";
	//echo "select min(task_start_date) as task_start_date, max(task_finish_date) as task_finish_date, po_number_id  from tna_process_mst where is_deleted=0 and status_active=1 $tna_job $tna_date_cond and task_number=86   group by po_number_id order by po_number_id"; die;
	
	$sql=sql_select("select min(task_start_date) as task_start_date, max(task_finish_date) as task_finish_date, po_number_id  from tna_process_mst where is_deleted=0 and status_active=1 $tna_job $tna_date_cond and task_number=86   group by po_number_id ");
	foreach($sql as $srows)
	{
		$tna_task_data[$srows[csf("po_number_id")]]['task_start_date']=change_date_format($srows[csf("task_start_date")], "dd-mm-yyyy", "-") ;
		$tna_task_data[$srows[csf("po_number_id")]]['task_finish_date']=change_date_format($srows[csf("task_finish_date")], "dd-mm-yyyy", "-") ;
		
		if($sel_pos=="") $sel_pos=$srows[csf("po_number_id")]; else $sel_pos .=",".$srows[csf("po_number_id")];
		//if($sel_pos=="") $sel_pos=$srows[csf("po_number_id")]; else $sel_pos .=",".$srows[csf("po_number_id")];
	}
	
	if( $sel_pos=="" )
	{
		echo "Sorry! No PO found for planning in TNA process.";
		die;
	} 
	
	if($db_type==0)
	{
		//$sql="select sum(plan_qnty) as plan_qnty,po_break_down_id,item_number_id from  ppl_sewing_plan_board where po_break_down_id in ( $sel_pos )  group by po_break_down_id,item_number_id order by po_break_down_id";
		$sql=" select b.plan_qnty,b.po_id as  po_break_down_id,b.item_number_id  from ppl_sewing_plan_board a, ppl_sewing_plan_board_powise b where a.id=b.plan_id and b.po_id in ( $sel_pos )   "; // group by po_break_down_id,item_number_id order by po_break_down_id
		
	}
	else
	{
		$sel_pos2=array_chunk(array_unique(explode(",",$sel_pos)),999);
		
		 $sql = "select B.PLAN_QNTY,b.PO_ID as PO_BREAK_DOWN_ID,B.ITEM_NUMBER_ID FROM PPL_SEWING_PLAN_BOARD A, PPL_SEWING_PLAN_BOARD_POWISE B where A.id=B.plan_id  and ";
		
		$p=1;
		foreach($sel_pos2 as $job_no_process)
		{
			if($p==1) $sql .=" (b.PO_ID in(".implode(',',$job_no_process).")"; else  $sql .=" or b.PO_ID in(".implode(',',$job_no_process).")";
			$p++;
		}
		$sql .=")";
		
		// $sql .="  group by po_break_down_id,item_number_id order by po_break_down_id ";
	}
	
	// echo $sql;  
	$sql=sql_select($sql);
	$planned_qnty=array();
	foreach($sql as $srows)
	{
		$planned_qnty[$srows[csf("po_break_down_id")]][$srows[csf("item_number_id")]]+=$srows[csf("plan_qnty")];
	}
	//  print_r($planned_qnty);
	  
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (2=>$comp,3=>$buyer_arr,9=>$item_category);
	$data[5]=2;
	 
	
	 
	$sql="select po_break_down_id,sum(production_quantity) as production_quantity from   pro_garments_production_mst where production_type=5 and po_break_down_id in (".$sel_pos.") and status_active=1 and is_deleted=0   group by po_break_down_id";
	 
	//print_r($sewing_resource);  
	
	$sql_data=sql_select($sql);
	$k=0;
	//print_r($po_plan_info);die;
	foreach($sql_data as $rows)
	{
		$production_details[$rows[csf("po_break_down_id")]]=$rows[csf("production_quantity")]; 
	} 
	 
	 
	if($db_type==0)
	{
		$sql="select day_target,po_dtls_id,gmts_item_id from  ppl_gsd_entry_mst where po_dtls_id in ( $sel_pos )";
	}
	else // $day_target=return_field_value("day_target", "ppl_gsd_entry_mst", "po_dtls_id='".$rows[csf("id")]."' and gmts_item_id='".$setdata[0]."'");
	{
		$sel_pos2=array_chunk(array_unique(explode(",",$sel_pos)),999);
		
		$sql = " select day_target,po_dtls_id,gmts_item_id from  ppl_gsd_entry_mst where ";
		
		$p=1;
		foreach($sel_pos2 as $job_no_process)
		{
			if($p==1) $sql .=" (po_dtls_id in(".implode(',',$job_no_process).")"; else  $sql .=" or po_dtls_id in(".implode(',',$job_no_process).")";
			$p++;
		}
		$sql .=")";
		
		 $sql .=" ";
	}
	 
	$sql=sql_select($sql);
	 
	foreach($sql as $srows)
	{
		$day_target[$srows[csf("po_dtls_id")]][$srows[csf("gmts_item_id")]]=$srows[csf("day_target")];
	}
	/* 
	if($db_type==0)
	{
		$sql="select image_location,master_tble_id from  common_photo_library where master_tble_id in ( $sel_pos )";
	}
	else // $day_target=return_field_value("day_target", "ppl_gsd_entry_mst", "po_dtls_id='".$rows[csf("id")]."' and gmts_item_id='".$setdata[0]."'");
	{
		$sel_pos2=array_chunk(array_unique(explode(",",$sel_pos)),999);
		
		$sql = " select day_target,po_dtls_id,gmts_item_id from  common_photo_library where ";
		
		$p=1;
		foreach($sel_pos2 as $job_no_process)
		{
			if($p==1) $sql .=" (po_dtls_id in(".implode(',',$job_no_process).")"; else  $sql .=" or po_dtls_id in(".implode(',',$job_no_process).")";
			$p++;
		}
		$sql .=")";
		
		 $sql .=" ";
	}
	 
	$sql=sql_select($sql);
	 
	foreach($sql as $srows)
	{
		$day_target[$srows[csf("po_dtls_id")]][$srows[csf("gmts_item_id")]]=$srows[csf("day_target")];
	}
	*/
	//$img=return_field_value("image_location","common_photo_library","  	master_tble_id='".$rows[csf("job_no")]."'");
	//print_r($country_ship_date); die;
	
	$str_shi='';
	if ( $data[2]!=0 ) $str_shi= " and b.shiping_status!=3 ";
		
	 
	  
	if($db_type==0)
	{
		$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.shipment_date as shipment_date,b.pub_shipment_date as pub_shipment_date,a.garments_nature,b.plan_cut,SUBSTRING_INDEX(a.insert_date, '-', 1) as year,b.id,set_break_down,total_set_qnty,set_smv from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($sel_pos)  and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 $str_shi $company $buyer $job_cond $order_cond order by b.shipment_date";
	}
	if($db_type==2)
	{
		$sel_pos=array_chunk(array_unique(explode(",",$sel_pos)),999);
		
		$sql = "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.shipment_date as shipment_date,b.pub_shipment_date as pub_shipment_date,a.garments_nature,b.plan_cut,to_char(a.insert_date,'YYYY') as year,b.id,set_break_down,total_set_qnty,set_smv from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and  a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 $str_shi  $company $buyer $job_cond $order_cond ";

		$p=1;
		foreach($sel_pos as $job_no_process)
		{
			if($p==1) $sql .=" and (b.id in(".implode(',',$job_no_process).")"; else  $sql .=" or b.id in(".implode(',',$job_no_process).")";
			
			$p++;
		}
		$sql .=")";
		$sql .="  order by b.shipment_date ";
	}
	 
	 // echo $sql;
	//die;
	 
	
	$sql_exe=sql_select($sql);
	
	?>
    <table width="1220" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
    	<thead> 
        	<th width="40">SL</th>
            <th width="70">Job NO</th>
            <th width="40">Year</th>
            <th width="90">Buyer Name</th>
            <th width="110">Style Ref. No</th>
            <th width="90">Job Qnty</th>
            <th width="110">PO Number</th>
            <th width="140">Item Name</th>
            <th width="70">Item Quantity</th>
            <th width="70">Planned Quantity</th>
            <th width="70">Yet To Plan Quantity</th>
            <th width="60">Plan Quantity</th>
            <th width="50">SMV</th>
            <th width="80">Shipmenet Date</th>
            <th>Plan End Date</th>
        </thead>
       </table>
      <div style="width:1220px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1200" class="rpt_table" id="tbl_list_search">
				<tbody>
    <?
	 
	foreach($sql_exe as $rows)
	{
		
		// $img=return_field_value("image_location","common_photo_library","  	master_tble_id='".$rows[csf("job_no")]."'");
		$set=explode("__",$rows[csf("set_break_down")]);
		///echo 
		//$rows[csf("pub_shipment_date")]=$country_ship_date[$rows[csf("id")]];
		
		foreach($set as $setdtls)
		{
			$i++;
				//js_set_value( 'OG-16-00114_6410_5000_02-05-2016_TNA-1_02-05-2016_T-Shirt_5_1_-7500_25-03-2016_17-04-2016','','','0','BIZZBEE')">
				
//set_breck_down+=$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val()+'_'+$('#smv_'+i).val()+'_'+$('#smvset_'+i).val()+'_'+$('#complexity_'+i).val()+'_'+$('#emblish_'+i).val();

			$setdata=explode("_",$setdtls);
			$day_target=$day_target[$rows[csf("id")]][$setdata[0]];
			 //return_field_value("day_target", "ppl_gsd_entry_mst", "po_dtls_id='".$rows[csf("id")]."' and gmts_item_id='".$setdata[0]."'");
			$css='';
			 //echo $rows[csf("id")]."=".$setdata[0]."*";
			$yet=($setdata[1]*$rows[csf("plan_cut")])-$planned_qnty[$rows[csf("id")]][$setdata[0]] ;
			
			if( $yet > 1 && $planned_qnty[$rows[csf("id")]][$setdata[0]]> 0 )  $css='bgcolor="#FFFF00"';
			else if( $yet < 1 )   $css='bgcolor="#66CC33"'; // $css='bgcolor="#FFFF00"';
			else if( $yet >0  && $planned_qnty[$rows[csf("id")]][$setdata[0]]<1 )   $css='bgcolor="#FFFFFF"'; // $css='bgcolor="#FFFF00"';
			
			// $css='bgcolor="#FFFF00"';
			//if( ($setdata[1]*$rows[csf("plan_cut")])-$planned_qnty[$rows[csf("id")]][$setdata[0]] >0) $css='bgcolor="#FFFF00"';
			//if( ($setdata[1]*$rows[csf("plan_cut")])-$planned_qnty[$rows[csf("id")]][$setdata[0]] >0) $css='bgcolor="#FFFF00"';
			//else if( ($setdata[1]*$rows[csf("plan_cut")])-$planned_qnty[$rows[csf("id")]][$setdata[0]] < 1) $css='bgcolor="#66CC33"';
			
		?> 
        	<tr id="tr_id<? echo $i;?>" <? echo $css; ?> bgcolor="#66CC33" onClick="js_set_value( '<? echo $rows[csf("job_no")]."_".$rows[csf("id")]."_".$setdata[1]*$rows[csf("plan_cut")]."_".change_date_format($rows[csf("pub_shipment_date")], "dd-mm-yyyy", "-")."_".$rows[csf("po_number")]."_".change_date_format($rows[csf("pub_shipment_date")], "dd-mm-yyyy", "-")."_".$rows[csf("style_ref_no")]."_".$setdata[2]."_".$setdata[0]."_".(($setdata[1]*$rows[csf("plan_cut")])-$planned_qnty[$rows[csf("id")]][$setdata[0]])."_".$tna_task_data[$rows[csf("id")]]['task_start_date']."_".$tna_task_data[$rows[csf("id")]]['task_finish_date']."_".$production_details[$rows[csf("id")]]; ?>','<? echo $img; ?>','<? echo $day_target; ?>','<? echo $setdata[4]; ?>','<? echo $buyer_arr[$rows[csf("buyer_name")]]; ?>','<? echo $i; ?>')">
            	
                <td width="40"><? echo $rows[csf("id")]; ?></td>
                <td width="70"><? echo $rows[csf("job_no")]; ?></td>
                <td width="40"><? echo $rows[csf("year")];//."=".$production_details[$rows[csf("id")]]; ?></td>
                <td width="90" style="word-break:break-all; word-wrap:break-word"><? echo $buyer_arr[$rows[csf("buyer_name")]]; ?></td>
                <td width="110" style="word-break:break-all; word-wrap:break-word"><? echo $rows[csf("style_ref_no")]; ?></td>
                <td width="90" align="right"><? echo $rows[csf("job_quantity")]; ?></td>
                <td width="110" style="word-break:break-all; word-wrap:break-word"><? echo $rows[csf("po_number")]; ?></td>
                <td width="140" style="word-break:break-all; word-wrap:break-word"><? echo $garments_item[$setdata[0]]; ?></td>
                <? /// ($setdata[1]*$rows[csf("plan_cut")])-$planned_qnty[$rows[csf("id")]][$setdata[0]] ?>
                <td width="70" align="right"><? echo $setdata[1]*$rows[csf("plan_cut")]; ?></td>
                <td width="70" align="right"><? echo $planned_qnty[$rows[csf("id")]][$setdata[0]]; ?></td>
                <td width="70" align="right"><? echo ($setdata[1]*$rows[csf("plan_cut")])-$planned_qnty[$rows[csf("id")]][$setdata[0]]; ?></td>
                <td width="60" align="right"><input type="text" style="width:50px" class="text_boxes_numeric" name="txt_plan_qnty<? echo $i; ?>" id="txt_plan_qnty<? echo $i; ?>" onBlur="check_set(<? echo $i; ?>)" value="<? echo ($setdata[1]*$rows[csf("plan_cut")])-$planned_qnty[$rows[csf("id")]][$setdata[0]]; ?>"></td>
                <td width="50" align="right"><? echo $setdata[2]; ?></td>
                <td width="80" align="center"><? echo change_date_format($rows[csf("pub_shipment_date")]); ?></td>
                <td align="center"><? echo $tna_task_data[$rows[csf("id")]]['task_finish_date']; ?></td>
            </tr>
        <?
		}
	}
	?>
    </tbody>
    <tfoot>
    	<tr>
        	<td height="35" valign="middle" colspan="15" align="left">
        		<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"><strong>Select all data</strong>
            </td>
        </tr>
    </tfoot>
    </table>
    </div>
    <?
}

if($action=="save_update_delete")
{
	// extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	 
	if ( $operation==0 )  // Insert Here
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		
		$id=return_next_id( "id","ppl_sewing_plan_board", 1 ) ;
		$field_array="id,line_id,po_break_down_id,plan_id,start_date,start_hour,end_date,end_hour,duration,plan_qnty,comp_level,first_day_output,increment_qty,terget,inserted_by,day_wise_plan,company_id,location_id,item_number_id,off_day_plan,order_complexity,ship_date,extra_param";
		$field_array_dtls="id,plan_id,plan_date,plan_qnty";
		$field_array_dtls_g="id,plan_id,po_id,plan_qnty,po_dtls,item_number_id";
		
		$field_array_up="line_id*po_break_down_id*plan_id*start_date*start_hour*end_date*end_hour*duration*plan_qnty*comp_level*first_day_output*increment_qty*terget*updated_by*day_wise_plan*company_id*location_id*item_number_id*off_day_plan*order_complexity*ship_date*extra_param";
		 
		$poid=explode("**",$poid);
		$cmpid=explode("**",$cmpid);
		$cmpstart=explode("**",$cmpstart);
		$cmpinc=explode("**",$cmpinc);
		$cmptarg=explode("**",$cmptarg);
		$lineid=explode("**",$lineid);
		$startdate=explode("**",$startdate);
		$starttime=explode("**",$starttime);
		$duratin=explode("**",$duratin);
		$planqty=explode("**",$planqty);
		$enddate=explode("**",$enddate);
		$updid=explode("**",$updid);
		$planid=explode("**",$planid);
		$isnew=explode("**",$isnew);
		$isedited=explode("**",$isedited);
		$today_plan_qnty=explode("**",$today_plan_qnty);
		$item_name_id=explode("**",$item_name_id);
		$off_day_plan=explode("**",$off_day_plan);
		$order_complexity=explode("**",$order_complexity);
		$tmpshipdate=explode("**",$tmpshipdate);
		$extra_param=explode("**",$extra_param);
		$group_po_info=explode("||**||",$group_po_info);
		//echo "0**";
		//print_r($extra_param);
		// die; 
		for($i=0; $i<$num_row; $i++)
		{
			if( ( $isnew[$i]*1 )==1 )
			{
				if($i==0) $id=return_next_id( "id","ppl_sewing_plan_board", 1 ) ; else $id++;
				if($data_array!="") $data_array.=",";
				if($db_type==0)
				{
					$startdate[$i]=change_date_format( $startdate[$i],"yyyy-mm-dd",'',1);
					$enddate[$i]=change_date_format( $enddate[$i],"yyyy-mm-dd",'',1);
					$tmpshipdate[$i]=change_date_format(date('d-M-Y',strtotime($tmpshipdate[$i])),"yyyy-mm-dd",'',1);
				}
				else
				{
					$startdate[$i]=change_date_format( $startdate[$i],'','',1);
					$enddate[$i]=change_date_format( $enddate[$i],'','',1);
					$tmpshipdate[$i]=date('d-M-Y',strtotime($tmpshipdate[$i]));
				}
				
				
				
				
				$duratin[$i]=$duratin[$i]*1;
				$data_array.="(".$id.",".$lineid[$i].",'".$poid[$i]."','".$id."','".$startdate[$i]."','".$starttime[$i]."','".$enddate[$i]."','0','".$duratin[$i]."','".$planqty[$i]."','".$cmpid[$i]."','".$cmpstart[$i]."','".$cmpinc[$i]."','".$cmptarg[$i]."',".$_SESSION['logic_erp']['user_id'].",'".$today_plan_qnty[$i]."','".$cbo_company_mst."','".$cbo_location_name."','".$item_name_id[$i]."','".$off_day_plan[$i]."','".$order_complexity[$i]."','".$tmpshipdate[$i]."','".$extra_param[$i]."')"; 
				
				//if( $del_ids=='') $del_ids  =$planid[$i]; else  $del_ids .=",".$planid[$i];
				$plsn=explode(",",$today_plan_qnty[$i]);
				for($m=0;$m<$duratin[$i]; $m++)
				{
					if( $id_dtls=='' ) $id_dtls=return_next_id( "id","ppl_sewing_plan_board_dtls", 1 ) ; else $id_dtls++;
					if($data_array_dtls!="") $data_array_dtls.=",";
					if( $db_type==0 ) 
						$cdate=change_date_format( add_date($startdate[$i],$m),"yyyy-mm-dd",'',1);
					else
						$cdate=change_date_format(add_date($startdate[$i],$m),'','',1);
						
					$data_array_dtls.="(".$id_dtls.",'".$id."','".$cdate."','".$plsn[$m]."')"; 
				}
				$group_po=explode("|*|",$group_po_info[$i]);
				foreach($group_po as $gpos)
				{
				 
					$tmp=explode("_",$gpos);
					if( $id_dtls_g=='' ) $id_dtls_g=return_next_id( "id","PPL_SEWING_PLAN_BOARD_POWISE", 1 ) ; else $id_dtls_g++;
					if($data_array_dtls_g!="") $data_array_dtls_g.=",";
					
					$data_array_dtls_g.="(".$id_dtls_g.",'".$id."','".$tmp[1]."','".$tmp[9]."','".$gpos."','".$tmp[8]."')"; 
					//> OG-16-00646_10085_700_02-11-2016_4600166551_02-11-2016_838492_5_2_700_31-07-2016_30-09-2016_
				}
				//print_r($group_po);
				
			}
			else if( ( $isedited[$i]*1 )==1 )
			{
				if( $del_ids=='') $del_ids  =$updid[$i]; else  $del_ids .=",".$updid[$i];
				
				$plsn=explode(",",$today_plan_qnty[$i]);
				for($m=0;$m<$duratin[$i]; $m++)
				{
					if( $id_dtls=='' ) $id_dtls=return_next_id( "id","ppl_sewing_plan_board_dtls", 1 ) ; else $id_dtls++;
					if($data_array_dtls!="") $data_array_dtls.=",";
					if( $db_type==0 ) 
						$cdate=change_date_format( add_date($startdate[$i],$m),"yyyy-mm-dd",'',1);
					else
						$cdate=change_date_format(add_date($startdate[$i],$m),'','',1);
						
					$data_array_dtls.="(".$id_dtls.",'".$updid[$i]."','".$cdate."','".$plsn[$m]."')"; 
				}
				
				$group_po=explode("|*|",$group_po_info[$i]);
				foreach($group_po as $gpos)
				{
				 
					$tmp=explode("_",$gpos);
					if( $id_dtls_g=='' ) $id_dtls_g=return_next_id( "id","PPL_SEWING_PLAN_BOARD_POWISE", 1 ) ; else $id_dtls_g++;
					if($data_array_dtls_g!="") $data_array_dtls_g.=",";
					
					$data_array_dtls_g.="(".$id_dtls_g.",'".$updid[$i]."','".$tmp[1]."','".$tmp[9]."','".$gpos."','".$tmp[8]."')"; 
					//> OG-16-00646_10085_700_02-11-2016_4600166551_02-11-2016_838492_5_2_700_31-07-2016_30-09-2016_
				}
				//print_r($group_po);
				
				if($db_type==0)
				{
					$startdate[$i]=change_date_format( $startdate[$i],"yyyy-mm-dd",'',1);
					$enddate[$i]=change_date_format( $enddate[$i],"yyyy-mm-dd",'',1);
				}
				else
				{
					$startdate[$i]=change_date_format( $startdate[$i],'','',1);
					$enddate[$i]=change_date_format( $enddate[$i],'','',1);
				}
				
				$id_arr[]=$updid[$i];
				$data_array_up[$updid[$i]] =explode("*",("".$lineid[$i]."*'".$poid[$i]."'*'".$id."'*'".$startdate[$i]."'*'".$starttime[$i]."'*'".$enddate[$i]."'*'0'*'".$duratin[$i]."'*'".$planqty[$i]."'*'".$cmpid[$i]."'*'".$cmpstart[$i]."'*'".$cmpinc[$i]."'*'".$cmptarg[$i]."'*".$_SESSION['logic_erp']['user_id']."*'".$today_plan_qnty[$i]."'*'".$cbo_company_mst."'*'".$cbo_location_name."'*'".$item_name_id[$i]."'*'".$off_day_plan[$i]."'*'".$order_complexity[$i]."'*'".$tmpshipdate[$i]."'*'".$extra_param[$i]."'"));
			}
		}
		 
		///echo " delete from ppl_sewing_plan_board_dtls where plan_id in (".$del_ids.") "; die;
		if($del_ids!='')
			$tm=explode(",",$del_ids);
		else
			$tm=array();
		if($all_deleted_ids!='')
			$tm2=explode(",",$all_deleted_ids);
		else
			$tm2=array();
		$tm22=array_merge($tm,$tm2);
		$del_grop=implode(",",$tm22);
		
		
		if( trim($all_deleted_ids)!="")
			$rID_deleted=execute_query(" delete from ppl_sewing_plan_board where id in (".$all_deleted_ids.") ");
		else
			$rID_deleted=1;
			
		if( trim($del_grop)!="" )
			$rID_del_dtls=execute_query(" delete from ppl_sewing_plan_board_dtls where plan_id in (".$del_grop.") ");
		else
			$rID_del_dtls=1;
			
		
		
		// print_r($tm22);die;
		if( trim($del_grop)!="" )
			$rID_del_dtls_g=execute_query(" delete from PPL_SEWING_PLAN_BOARD_POWISE where plan_id in (".$del_grop.") ");
		else
			$rID_del_dtls_g=1;
		
		if(count( $id_arr )>0)
		{
			$rID_up= execute_query( bulk_update_sql_statement( "ppl_sewing_plan_board", "id", $field_array_up, $data_array_up, $id_arr ) );
		}
		else $rID_up=1;
		
	 	if( $data_array!="" )
			$rID=sql_insert( "ppl_sewing_plan_board",$field_array,$data_array,1);
		else $rID=1;
		
		if( $data_array_dtls!="" )
			$rID_dtls=sql_insert( "ppl_sewing_plan_board_dtls",$field_array_dtls,$data_array_dtls,1);
		else $rID_dtls=1;
		
		if( $data_array_dtls_g!="" )
			$rID_dtls_g=sql_insert( "PPL_SEWING_PLAN_BOARD_POWISE",$field_array_dtls_g,$data_array_dtls_g,1);
		else $rID_dtls_g=1;
				
		
		//echo "5**0**0".$data_array;die;
		 //echo  "5**0**0".$rID.'&&'.$rID_up.'&&'.$rID_deleted.'&&'.$rID_del_dtls.'&&'.$rID_dtls.'&&'.$rID_dtls_g.'&&'.$rID_del_dtls_g;die;
		
		
		if($db_type==0)
		{
			if( $rID && $rID_up && $rID_deleted && $rID_del_dtls && $rID_dtls && $rID_dtls_g && $rID_del_dtls_g )
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if( $rID && $rID_up && $rID_deleted  && $rID_del_dtls && $rID_dtls  && $rID_dtls_g  && $rID_del_dtls_g )
			{
				oci_commit($con);
				echo "0**".$id."**0";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="show_planning_details_bottom")
{
	//echo $data;
	$data=explode("__",$data);
	$company=return_library_array("select id,company_name from lib_company","id","company_name");
	$location=return_library_array("select id,location_name from lib_location","id","location_name");
	
	//$sql_po= "select  a.job_no,a.company_name,a.location_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.shipment_date,a.garments_nature,b.plan_cut,b.po_received_date,pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=".$data[1]."";
	/*$sql_po= "select  a.job_no,a.company_name,a.location_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.shipment_date,
a.garments_nature,b.plan_cut,b.po_received_date,pub_shipment_date,c.country_ship_date 
from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst 
and b.job_no_mst=c.job_no_mst and b.id=".$data[1]." order by c.country_ship_date asc";*/
  $sql_po= "select  a.job_no,a.company_name,a.location_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.shipment_date,
a.garments_nature,b.plan_cut,b.po_received_date,pub_shipment_date 
from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  
and b.id=".$data[1]."";
	 
	$sql_data=sql_select($sql_po);
	foreach($sql_data as $row)
	{
		$podtls['company']=$company[$row[csf("company_name")]];
		$podtls['location_name']=$location[$row[csf("location_name")]];
		$podtls['job_no']=$row[csf("job_no")];
		$podtls['style_ref_no']=$row[csf("style_ref_no")];
		$podtls['po_number']=$row[csf("po_number")];
		$podtls['po_received_date']=$row[csf("po_received_date")];
		$podtls['shipment_date']=$row[csf("shipment_date")];
		$podtls['po_quantity']=$row[csf("po_quantity")];
		$podtls['plan_cut']=$row[csf("plan_cut")];
		$podtls['pub_shipment_date']=$row[csf("pub_shipment_date")];//$row[csf("pub_shipment_date")];
	//	$podtls['country_ship_date']=$row[csf("country_ship_date")];//$row[csf("pub_shipment_date")];
	}
	 
	
	$sql="select task_number,task_start_date,task_finish_date,actual_start_date,actual_finish_date from tna_process_mst where po_number_id=".$data[1]." and task_number in (12,60,64,70,84,86) order by task_number";
	$sql_data=sql_select($sql);
	foreach($sql_data as $row)
	{
		$tnadata[$row[csf("task_number")]]['start']=$row[csf("task_start_date")];
		$tnadata[$row[csf("task_number")]]['end']=$row[csf("task_finish_date")];
	}
	
	/*$sql="select task_number,task_start_date,task_finish_date,actual_start_date,actual_finish_date from tna_process_mst where po_number_id=".$data[1]." and task_number in (60,64,84,70,86) order by task_number";
	$sql_data=sql_select($sql);
	foreach($sql_data as $row)
	{
		$tnadata[$row[csf("task_number")]]['start']=$row[csf("actual_start_date")];
		$tnadata[$row[csf("task_number")]]['end']=$row[csf("actual_finish_date")];
	}*/
	
	
	$sql="SELECT po_break_down_id,sum(plan_cut_qnty) as plan_cut_qnty,sum(kint_fin_fab_qnty) as kint_fin_fab_qnty,sum(kint_grey_fab_qnty) as kint_grey_fab_qnty,sum(woven_fin_fab_qnty) as woven_fin_fab_qnty,sum(woven_grey_fab_qnty) as woven_grey_fab_qnty,sum(yarn_qnty) as yarn_qnty,sum(conv_qnty) as conv_qnty,sum(trim_qty) as trim_qty,sum(emb_cons) as emb_cons, sum(wash_cons) as wash_cons,sum(kint_grey_fab_qnty_prod) as kint_grey_fab_qnty_prod,sum(kint_fin_fab_qnty_prod) as kint_fin_fab_qnty_prod,sum(wash_cons) as wash_cons,sum(emb_cons) as emb_cons FROM wo_bom_process WHERE  po_break_down_id in ( ".$data[1]."  ) group by po_break_down_id";
	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{ 
		$tna_task_update_data[60]['reqqnty']=$row[csf("kint_grey_fab_qnty_prod")];
		$tna_task_update_data[64]['reqqnty']=$row[csf("kint_fin_fab_qnty_prod")];
		//$tna_task_update_data[70]['reqqnty']=$row[csf("kint_grey_fab_qnty")];
		$tna_task_update_data[84]['reqqnty']=$podtls['po_quantity'];
		$tna_task_update_data[86]['reqqnty']=$podtls['po_quantity'];
	}
	//60	=> "Gray Fabric Production To Be Done",	
	//64	=> "Finish Fabric Production To Be Done",
	//70	=> "Sewing Trims To Be In-house",	
	//84	=> "Cutting To Be Done",		
	//86	=> "Sewing To Be Done",	
	
	$sql = "SELECT po_break_down_id, min(production_date) as mind,max(production_date) as maxd, production_type,sum(production_quantity) as production_quantity FROM  pro_garments_production_mst  WHERE po_break_down_id in (  ".$data[1]." )   group by po_break_down_id,production_type";
	$result = sql_select( $sql );
	foreach( $result as $row ) 
	{
		$tsktype=0;
		if ($row[csf("production_type")]==1) $tsktype=84;
		else if ($row[csf("production_type")]==3) $tsktype=85;
		else if ($row[csf("production_type")]==5) $tsktype=86;
		else if ($row[csf("production_type")]==7) $tsktype=87;
		else if ($row[csf("production_type")]==8) $tsktype=88; 
		else if ($row[csf("production_type")]==10) $tsktype=87;
		
		$tna_task_update_data[$tsktype]['max_start_date']=$row[csf("maxd")];
		$tna_task_update_data[$tsktype]['min_start_date']=$row[csf("mind")];
	//$tna_task_update_data[$tsktype]['quantity']=$row[csf("production_quantity")];
		$tna_task_update_data[$tsktype]['doneqnty']=$row[csf("production_quantity")];
		//$tna_task_update_data[$tsktype]['reqqnty']=$po_order_details['po_quantity'];
	} 
	//print_r($tna_task_update_data);
	$production_days=return_field_value( "count(distinct(production_date)) as id", "pro_garments_production_mst", "po_break_down_id=".$data[1]." and production_type=5 group by  po_break_down_id", "id" );
	
		//echo 'reza ='.$data[1];
	
	$daily_production=$tna_task_update_data[86]['doneqnty']/$production_days;
	
	$sql = "SELECT b.po_breakdown_id,b.entry_form, min(a.receive_date) mindate, max(a.receive_date) maxdate, sum(quantity) as prod_qntry 
FROM inv_receive_master a,  order_wise_pro_details b, pro_grey_prod_entry_dtls c where  c.mst_id=a.id and c.id=b.dtls_id and b.entry_form in ( 2 ) and b.po_breakdown_id in (  ".$data[1]." ) group by b.po_breakdown_id,b.entry_form order by b.po_breakdown_id";
	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
		$tna_task_update_data[60]['max_start_date']=$row[csf("maxdate")];
		$tna_task_update_data[60]['min_start_date']=$row[csf("mindate")];
		$tna_task_update_data[60]['doneqnty']=$row[csf("prod_qntry")]; 
	}
	
	$sql = "SELECT b.po_breakdown_id,b.entry_form, min(a.receive_date) mindate, max(a.receive_date) maxdate, sum(quantity) as prod_qntry 
FROM inv_receive_master a,  order_wise_pro_details b, pro_finish_fabric_rcv_dtls c where  c.mst_id=a.id and c.id=b.dtls_id and b.entry_form in ( 7 ) and b.po_breakdown_id in  (  ".$data[1]." ) group by b.po_breakdown_id,b.entry_form order by b.po_breakdown_id";

	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
		$tna_task_update_data[64]['max_start_date']=$row[csf("maxdate")];
		$tna_task_update_data[64]['min_start_date']=$row[csf("mindate")];
		$tna_task_update_data[64]['doneqnty']=$row[csf("prod_qntry")]; 
	}
	
	$sql = "SELECT b.po_breakdown_id, min(a.transaction_date) mindate, max(a.transaction_date) maxdate, sum(quantity) as prod_qntry,d.trim_type 
FROM inv_transaction a,  order_wise_pro_details b, product_details_master c , lib_item_group d
where a.prod_id=c.id and b.trans_id=a.id and c.item_group_id=d.id and d.trim_type in (1,2) and b.entry_form in ( 24 ) and b.po_breakdown_id in (  ".$data[1]." ) group by b.po_breakdown_id,d.trim_type order by b.po_breakdown_id";

	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
		$entry=($row[csf("trim_type")] == 1 ? 70 : 71);
		
		$tna_task_update_data[$entry]['max_start_date']=$row[csf("maxdate")];
		$tna_task_update_data[$entry]['min_start_date']=$row[csf("mindate")];
		$tna_task_update_data[$entry]['doneqnty']=$row[csf("prod_qntry")]; 
	}
	
	$line_enganed=return_field_value( "count(distinct(line_id)) as id", "ppl_sewing_plan_board", "po_break_down_id=".$data[1]." group by  po_break_down_id", "id" );
	
	$balance=$podtls['plan_cut']-$tna_task_update_data[86]['doneqnty'];
	$days_required=ceil($balance/$daily_production)*1;
	
	$days_required=(is_infinite($days_required))?$days_required:0;
	$to_be_end=add_date(date("Y-m-d",time()),$days_required);
	

	
	
	$late_early=datediff("d",$to_be_end,$podtls['pub_shipment_date']);
	if($late_early<3)
	{
		$color="red";
	}
	$late_early=$late_early-2;
	?>
    
    <table width="100%" id="tbl_footer" cellspacing="0" cellpadding="2" border="1" rules="all">
                	<tr class="plan_foot_header"  >
                    	<td width="100" align="center">Company Name</td>

                        <td width="100" align="center">Location Name</td>
                        <td width="100" align="center">Job No</td>
                        <td width="100" align="center">Style Ref</td>
                        <td width="100" align="center">Order No</td>
                        <td width="100" align="center">Receive Date</td>
                        <td width="100" align="center">Shipment Date</td>
                        <td width="100" align="center">Order Quantity</td>
                        <td align="center">Planned Cut <span style="position:absolute; background-color:#CCC; border:1px solid #90F; right:2px; top:2px; color:red"><a style="text-decoration:none;color:red" href="##" onClick='$("#footer").hide(1000);'>&nbsp;X&nbsp;</a></span></td>
                    </tr>
                	<tr>
                    	<td  id="cid" align="center"><?php echo $podtls['company']; ?></td>
                        <td  id="cid" align="center"><?php echo $podtls['location_name']; ?></td>
                        <td  id="cid" align="center"><?php echo $podtls['job_no']; ?></td>
                        <td  id="cid" align="center"><?php echo $podtls['style_ref_no']; ?></td>
                        <td  id="cid" align="center"><?php echo $podtls['po_number']; ?></td>
                        <td  id="cid" align="center"><?php echo change_date_format($podtls['po_received_date']); ?></td>
                        <td  id="cid" align="center"><?php echo change_date_format($podtls['pub_shipment_date']); ?></td>
                        <td  id="cid" align="center"><?php echo $podtls['po_quantity']; ?></td>
                        <td  id="cid" align="center"><?php echo $podtls['plan_cut']; ?></td>
                    </tr>
                    
                    <tr>
                    	<td  align="center" colspan="9">
                        	<table width="100%" cellspacing="0" cellpadding="0"  border="1" rules="all">
                            	<tr>
                                	<td></td>
                                    <td width="60">Knitting Start</td>
                                    <td width="60">Knitting End</td>
                                    <td width="60">Fin. Fab. Prod. Start</td>
                                    <td width="60">Fin. Fab. Prod. End</td>
                                    <td width="60">PP Start</td>
                                    <td width="60">PP End</td>
                                    <td width="60">Sew Trim Rev. Start</td>
                                    <td width="60">Sew Trim Rev. End</td>
                                    <td width="60">Cut Start</td>
                                    <td width="60">Cut End</td>
                                    <td width="60">Sew. Start</td>
                                    <td width="60">Sew. End</td>
                                    <td width="60">Line Engaged</td>
                                    <td width="60">Sew Prod./Day</td>
                                    <td width="60">To Be End</td>
                                    <td width="60">Early / Late By</td>
                                    <td>Suggestion</td>
                                </tr>
                                <tr>
                                	<td>As Per TNA</td>
                                    <td><?php echo change_date_format($tnadata[60]['start']); ?></td>
                                    <td><?php echo change_date_format($tnadata[60]['end']); ?></td>
                                    <td><?php echo change_date_format($tnadata[64]['start']); ?></td>
                                    <td><?php echo change_date_format($tnadata[64]['end']); ?></td>
                                    
                                    <td><?php echo change_date_format($tnadata[12]['start']); ?></td>
                                    <td><?php echo change_date_format($tnadata[12]['end']); ?></td>
                                    <td><?php echo change_date_format($tnadata[70]['start']); ?></td>
                                    <td><?php echo change_date_format($tnadata[70]['end']); ?></td>
                                    <td><?php echo change_date_format($tnadata[84]['start']); ?></td>
                                    <td><?php echo change_date_format($tnadata[84]['end']); ?></td>
                                   
                                    <td><?php echo change_date_format($tnadata[86]['start']); ?></td>
                                    <td><?php echo change_date_format($tnadata[86]['end']); ?></td>
                                    <td align="center" rowspan="4"><?php echo $line_enganed; ?></td>
                                    <td rowspan="4" align="center"><?php echo  round($daily_production); ?></td>
                                    <td rowspan="4" align="center" bgcolor="<? echo $color; ?>"><?php echo change_date_format($to_be_end); ?></td>
                                    <td rowspan="4"align="center"><?php echo $late_early; ?></td>
                                    <td rowspan="4" align="center"></td>
                                </tr>
                                <tr>
                                	<td>Qty. Required</td>
                                    <td align="right" colspan="2"><?php echo number_format($tna_task_update_data[60]['reqqnty'],0,'.',''); ?></td>
                                     
                                    <td align="right" colspan="2"><?php echo number_format($tna_task_update_data[64]['reqqnty'],0,'.',''); ?></td>
                                    <td align="right" colspan="2"><?php //echo number_format($tna_task_update_data[70]['reqqnty'],0); ?></td>
                                    <td align="right" colspan="2"><?php //echo $podtls['plan_cut']; //number_format($tna_task_update_data[84]['reqqnty'],0); ?></td>
                                     
                                    <td align="right" colspan="2"><?php  echo $podtls['plan_cut']; ?></td>
                                     
                                    <td align="right" colspan="2"><?php echo $podtls['plan_cut']; //number_format($tna_task_update_data[86]['reqqnty'],0); ?></td>
                                      
                                </tr>
                                <tr>
                                	<td>Qty. Available</td>
                                    <td align="right" colspan="2"><?php echo number_format($tna_task_update_data[60]['doneqnty'],0,'.',''); ?></td> 
                                    <td align="right" colspan="2"><?php echo number_format($tna_task_update_data[64]['doneqnty'],0,'.',''); ?></td> 
                                     <td align="right" colspan="2"><?php //echo number_format($tna_task_update_data[70]['reqqnty'],0); ?></td>
                                    <td align="right" colspan="2"><?php //echo number_format($tna_task_update_data[84]['doneqnty'],0,'.',''); ?></td> 
                                    <td align="right" colspan="2"><?php echo $tna_task_update_data[84]['doneqnty']; ?></td> 
                                    <td align="right" colspan="2"><?php echo number_format($tna_task_update_data[86]['doneqnty'],0,'.',''); ?></td> 
                                </tr>
                                <tr>
                                	<td>Qty. Balance</td>
                                    <td align="right" colspan="2"><?php echo number_format($tna_task_update_data[60]['reqqnty'],0,'.','')-number_format($tna_task_update_data[60]['doneqnty'],0,'.',''); ?></td> 
                                    <td align="right" colspan="2"><?php echo number_format($tna_task_update_data[64]['reqqnty'],0,'.','')-number_format($tna_task_update_data[64]['doneqnty'],0,'.',''); ?></td> 
                                     <td align="right" colspan="2"><?php //echo number_format($tna_task_update_data[70]['reqqnty'],0); ?></td>
                                    <td align="right" colspan="2"><?php //echo number_format($podtls['plan_cut'],0,'.','')- number_format($tna_task_update_data[84]['doneqnty'],0,'.',''); ?></td> 
                                    
                                    <td align="right" colspan="2"><?php echo $podtls['plan_cut']-$tna_task_update_data[84]['doneqnty']; ?></td> 
                                    <td align="right" colspan="2"><?php echo number_format($podtls['plan_cut'],0,'.','')-number_format($tna_task_update_data[86]['doneqnty'],0,'.',''); ?></td> 
                                    
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>   
    <?
	//print_r($tnadata);
	//echo "==".$podtls['company'];
	die;
	  
}


?>