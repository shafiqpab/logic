<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_ip=$_SESSION['logic_erp']['user_ip'];

extract($_REQUEST);
$permission=$_SESSION['page_permission'];
include('../../../includes/common.php');


if ($action=="load_team_member")
{
	echo create_drop_down( "cbo_team_member_id", 150, "select id,team_member_name from team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();	
}



if ($action=="report_generate")
{
	
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$txt_team_leader_id=str_replace("'","",$txt_team_leader_id);
	$txt_team_member_id=str_replace("'","",$txt_team_member_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	
	$team_leader_arr=return_library_array( "select id,team_leader_name from lib_team_mst where project_type=1 and status_active =1 and is_deleted=0 order by team_leader_name",'id','team_leader_name');

	$team_member_arr=return_library_array( "select id,team_member_name from team_member_info where status_active =1 and is_deleted=0 order by team_member_name",'id','team_member_name');

	
	if($txt_date_from !='' && $txt_date_to !=''){
		if($db_type==0)
		{
			$txt_date_from = date("Y-m-d",strtotime($txt_date_from));
			$txt_date_to = date("Y-m-d",strtotime($txt_date_to));
		}
		else
		{
			$txt_date_from = date("d-M-Y",strtotime($txt_date_from));
			$txt_date_to = date("d-M-Y",strtotime($txt_date_to));
		}
		$where_con .=" and task_date between '$txt_date_from' and '$txt_date_to'";
	}
	
	if($cbo_company_id !=0){
		$where_con .=" and company_id = '$cbo_company_id'";
	}
	if($txt_team_leader_id !=""){
		$where_con .=" and team_leader_id in($txt_team_leader_id)";
	}
	if($txt_team_member_id !=""){
		$where_con .=" and team_member_id in($txt_team_member_id)";
	}
	

    $sql= "select ID,ISSUE_NUM,ACTIVITY_DETIALS,MINUTES,MINUTES_TYPE,COMMENTS,TASK_DATE,USER_ID,COMPANY_ID,TEAM_LEADER_ID,TEAM_MEMBER_ID ,VARIFIED_MINUTES,RE_COMMENTS FROM DAILY_TASK_MST where status_active=1 and is_deleted=0  $where_con";
	   //echo $sql;
	$result = sql_select($sql);
	$width=1200;
	ob_start();
	?>
	<div style="text-align:left; width:<?= $width+30; ?>px; margin:5px auto;">
    <table width="<?= $width; ?>" class="rpt_table" id="rpt_tablelist_view" rules="all" cellspacing="2" cellpadding="2" border="0" align="left">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="100">Team Leader</th>
                <th width="100">Team Member</th>
                <th width="70">Task Date</th>
                <th width="120">Task ID</th>
                <th width="300">Activity Details</th>
                <th width="60">Minutes Type</th>
                <th width="80">Minutes</th>
                <th width="80">Varified Minutes</th>
                <th>Re-Comments</th>
            </tr>
        </thead>
	</table>
	<div style="overflow-y:scroll; max-height:350px; width:<?= $width+20; ?>px; float:left;" id="scroll_body">
    <table width="<?= $width; ?>" class="rpt_table" id="table_body" rules="all" cellspacing="2" cellpadding="2" border="0" align="left">
		<tbody>
			<?
			$minutes_type_arr=array(1=>"Idle",2=>"Active");
			$i=0;
			foreach($result as $row )
			{
				$i++;
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$total_minute+=$row[MINUTES];
				$total_varified_minute+=$row[VARIFIED_MINUTES];
				
				
            ?>
            <tr style="cursor:pointer" id="tr_<? echo $i; ?>" height="20" bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" >
                <td width="30" align="center"><? echo $i; ?></td>
                <td width="100" ><p><? echo $team_leader_arr[$row[TEAM_LEADER_ID]]; ?></p></td>
                <td width="100" ><p><? echo $team_member_arr[$row[TEAM_MEMBER_ID]]; ?></p></td>
                <td width="70" align="center"><p><? echo change_date_format($row[TASK_DATE]); ?></p></td>
                <td width="120" align="center"  ><? echo $row[ISSUE_NUM]; ?></td>
                <td width="300" align="center"><p><?php echo $row[ACTIVITY_DETIALS]; ?></p></td>
                <td width="60" align="center" ><? echo $minutes_type_arr[$row[MINUTES_TYPE]]; ?></td>
                <td width="80" align="center"><?php echo $row[MINUTES]; ?></td>
                <td width="80" align="center"><?php echo $row[VARIFIED_MINUTES]; ?></td>
                <td align="center" ><p><?php echo $row[RE_COMMENTS]; ?></p></td>
                
            </tr>
            <?
			}
			?>
        </tbody>
	</table>
    </div>
	
    <table width="<?= $width; ?>" class="rpt_table" rules="all" cellspacing="2" cellpadding="2" border="0" align="left">
        <tfoot>
            <tr>
                <th width="30"></td>
                <th width="100"></th>
                <th width="100"></th>
                <th width="70"></th>
                <th width="120"></th>
                <th width="300"></th>
                <th width="60"></th>
                <th width="80"><?= $total_minute;?></th>
                <th width="80" id="td_varified_minutes"><?= $total_varified_minute;?></th>
                <th></th>
            </tr>
        </tfoot>
	</table>

    </div>
	<?
		$html=ob_get_contents();
		ob_clean();

		foreach (glob("*.xls") as $filename) {
			@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$name.".xls";
		$create_new_doc = fopen($filename, 'w');	
		$is_created = fwrite($create_new_doc,$html);
		echo $html."**".$filename;
		exit();	

}


if($action=="team_leader_list")
{
	echo load_html_head_contents("Team Leader", "../../../", 1, 1,'','','');
	extract($_REQUEST);	
	?> 
	<script>
	
	$(document).ready(function(e) {
        setFilterGrid('tbl_list_search',-1);
		set_all();
    });
	
	 var selected_id = new Array, selected_name = new Array();
		
		function check_all_data(is_checked)
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) 
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
        // Keep old selected user id until click on refresh button
		function set_all()
		{
			var old = document.getElementById( 'txt_user_row_id' ).value;          
			if(old !="")
			{   
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{  
					js_set_value( old[i] ) 
				}
			}
		}

		function js_set_value( str) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			 
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id =''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hidden_user_id').val( id );
			$('#hidden_user_name').val( name );
		}
		
    </script>
    <input type="hidden" name="user_id" id="hidden_user_id" value="" />
    <input type="hidden" name="user_name" id="hidden_user_name" value="" />
    <div>	
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="340" class="rpt_table">
            <thead>
                <th width="50">SL</th>
                <th>Team Leader</th>
            </thead>
		</table>
		<div style="width:340px; max-height:280px; overflow-y:scroll">
        	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="320" class="rpt_table" id="tbl_list_search">
            <?php 
				$i=1; $user_row_id=""; $user_id=explode(",",$team_leader_id);
                //$nameArray = sql_select( "select id,user_name from user_passwd where valid=1" );
				$nameArray = sql_select( "select id,team_leader_name from lib_team_mst where project_type=1 and status_active =1 and is_deleted=0 order by team_leader_name" );
				$i=0;
                foreach ($nameArray as $selectResult)
				{
					$i++;    
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    if(in_array($selectResult[csf('id')],$user_id)) 
					{
						if($user_row_id=="") $user_row_id=$i; else $user_row_id.=",".$i;
					}
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
                        <td width="50" align="center"><?php echo $i; ?>
                            <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $selectResult[csf('id')]; ?>"/>	
                            <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $selectResult[csf('team_leader_name')]; ?>"/>
                        </td>	
                        <td><p><?php echo $selectResult[csf('team_leader_name')];?></p></td>
                    </tr>
                    <?                   
                }
                ?>
               	<input type="hidden" name="txt_user_row_id" id="txt_user_row_id" value="<?php echo $user_row_id;?>"/>	
            </table>
        </div>
        <table width="340" cellspacing="0" cellpadding="0" border="1" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%"> 
                        <div style="width:45%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data(this.checked)" /> Check / Uncheck All
                        </div>
                        <div style="width:55%; float:left" align="left">
                            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
	</div>
    <?
	exit();
}



if($action=="team_member_list")
{
	echo load_html_head_contents("Team Leader", "../../../", 1, 1,'','','');
	extract($_REQUEST);	
	?> 
	<script>
	
	$(document).ready(function(e) {
        setFilterGrid('tbl_list_search',-1);
		set_all();
    });
	
	 var selected_id = new Array, selected_name = new Array();
		
		function check_all_data(is_checked)
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) 
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
        // Keep old selected user id until click on refresh button
		function set_all()
		{
			var old = document.getElementById( 'txt_user_row_id' ).value;          
			if(old !="")
			{   
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{  
					js_set_value( old[i] ) 
				}
			}
		}

		function js_set_value( str) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			 
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id =''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hidden_user_id').val( id );
			$('#hidden_user_name').val( name );
		}
		
    </script>
    <input type="hidden" name="user_id" id="hidden_user_id" value="" />
    <input type="hidden" name="user_name" id="hidden_user_name" value="" />
    <div>	
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="340" class="rpt_table">
            <thead>
                <th width="50">SL</th>
                <th>Team Member</th>
            </thead>
		</table>
		<div style="width:340px; max-height:280px; overflow-y:scroll">
        	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="320" class="rpt_table" id="tbl_list_search">
            <?php 
				$i=1; $user_row_id=""; $user_id=explode(",",$team_member_id);
				$nameArray = sql_select( "select id,team_member_name from team_member_info where team_id in($team_leader_id) and status_active =1 and is_deleted=0 order by team_member_name" );
				$i=0;
                foreach ($nameArray as $selectResult)
				{
					$i++;    
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    if(in_array($selectResult[csf('id')],$user_id)) 
					{
						if($user_row_id=="") $user_row_id=$i; else $user_row_id.=",".$i;
					}
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
                        <td width="50" align="center"><?php echo $i; ?>
                            <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $selectResult[csf('id')]; ?>"/>	
                            <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $selectResult[csf('team_member_name')]; ?>"/>
                        </td>	
                        <td><p><?php echo $selectResult[csf('team_member_name')];?></p></td>
                    </tr>
                    <?                   
                }
                ?>
               	<input type="hidden" name="txt_user_row_id" id="txt_user_row_id" value="<?php echo $user_row_id;?>"/>	
            </table>
        </div>
        <table width="340" cellspacing="0" cellpadding="0" border="1" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%"> 
                        <div style="width:45%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data(this.checked)" /> Check / Uncheck All
                        </div>
                        <div style="width:55%; float:left" align="left">
                            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
	</div>
    <?
	exit();
}





?>