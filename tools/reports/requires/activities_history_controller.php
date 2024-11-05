<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------
$menu_library=return_library_array( "select m_menu_id, menu_name from  main_menu", "m_menu_id", "menu_name"  );
$query_type=array(0=>"New Insert",1=>"Update/Edit",2=>"Delete");

function decrypt($string='', $key='') { 
	$key = "logic_erp_2011_2012_platform";
	$result = ''; 
	$string = base64_decode($string);		
	for($i=0; $i<strlen($string); $i++) {
		$char = substr($string, $i, 1); 
		$keychar = substr($key, ($i % strlen($key))-1, 1); 
		$char = chr(ord($char)-ord($keychar)); 
		$result.=$char; 
	}		
	return $result; 
}


if ($action=="report_generate_login_history")  // Item Description wise Search
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$user_name=str_replace("'","",$cbo_user_name);
	$cbo_menu_name=str_replace("'","",$cbo_menu_name);
	$cbo_module_name=str_replace("'","",$cbo_module_name);
	$cbo_search=str_replace("'","",$cbo_search);
	$search_value=str_replace("'","",$search_value);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	
		if($db_type==2)
		{
			if( $date_from==0 && $date_to==0 ) $log_date=""; else $log_date= "   entry_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		}
		else
		{
			if( $date_from==0 && $date_to==0 ) $log_date=""; else $log_date= "   entry_date between '".$date_from."' and '".$date_to."'";
	
		}
	if($user_name==0)
		$user_cond="";
	else
		$user_cond=" and user_id=$user_name";
		
	if($cbo_search==0)
		$search_cond="";
	else if($cbo_search==1)
		$search_cond=" and query_type=0";
	else if($cbo_search==2)
		$search_cond=" and query_type=1";
	else if($cbo_search==3)
		$search_cond=" and query_type=2";
	
	
	if ($cbo_module_name==0)
		$module_name="";
	else
		$module_name=" and module_name=$cbo_module_name";
	
	if ($cbo_menu_name==0)
		$menu_name="";
	else
		$menu_name="  and form_name=$cbo_menu_name";
	 
	
	//echo $user_name;die;
	ob_start();	
	
	?>
      <div style="width:980px;">
   	  <table width="980" cellspacing="0" cellpadding="0" border="0" rules="all" >
            <tr class="form_caption">
                <td colspan="11" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
            </tr>
     </table>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="980" class="rpt_table" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Session</th>
            <th width="80">IP ADDRESS</th>
            <th width="80">MAC</th>
            <th width="70">Entry Date</th>
            <th width="110">Entry Time</th>
            <th width="250">Page Name</th>
            <th width="80">Operation</th>
            <th width="">Qeury Details</th>
        </thead>
     </table>
     <div style="width:1000px; overflow-y:scroll; max-height:350px;font-size:12px; overflow-x:hidden;" id="scroll_body">
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="980" class="rpt_table" id="table_body" >
	<? 
		$user_data="select id,query_details,IP_ADDRESS,MAC,session_id,entry_time,entry_date,module_name,form_name,query_type from activities_history where $log_date $module_name $menu_name $user_cond   $search_cond order by entry_date,entry_time desc";
		$nameArray=sql_select( $user_data );
		// echo $user_data;
		$i=1; $log_status_arr=array( 0=>"success", 1=>"pc ip fail", 2=>"password" , 3=>"user", 4=>"proxy");
		foreach($nameArray as $row)
		{
	if ($i%2==0)  
                $bgcolor="#E9F3FF";
            else
                $bgcolor="#FFFFFF";
     ?>
        <tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
           <td width="30"><? echo $i;?></td>
           <td width="60" align="center"><p><? echo $row[csf('session_id')]; ?></p></td>
           <td width="80"><p><?=$row[IP_ADDRESS]; ?></p></td>
           <td width="80"><p><?=$row[MAC]; ?></p></td>
           <td width="70" align="center"><p><? echo ($row[csf('entry_date')] == '0000-00-00' || $row[csf('entry_date')] == '' ? '' : change_date_format($row[csf('entry_date')]));?></p></td>
           <td width="110"><p><? echo $row[csf('entry_time')];?></p></td>
           <td width="250"><p><? echo $menu_library[$row[csf('form_name')]];?></p></td>
           <td width="80"><p><? echo $query_type[$row[csf('query_type')]];?></p></td>
           <td width=""><p><? echo decrypt($row[csf('query_details')]);?></p></td>
        </tr>
        <?
		$i++;
		}
		?>
    </table>
    </div>
     <b style="color:#F00; ">Note: <i>All History Data Will Be Deleted Before 90 Days.</i></b>
     <?
		$user_id=$_SESSION['logic_erp']['user_id'];
		foreach (glob("$user_id*.xls") as $filename) 
		{
			if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc,ob_get_contents());
		$filename=$user_id."_".$name.".xls";
		echo "$total_data####$filename";
		//exit();
	 

	
	if($db_type==0)
	{
		$previous_date = date('Y-m-d', strtotime('-90 day', time())); 
	}
	else
	{
		$previous_date = change_date_format(date('Y-m-d', strtotime('-90 day', time())),'','',1);
	}
	  
	$con = connect();
	$rID = execute_query("delete from activities_history where ENTRY_DATE < '$previous_date'",1);	
	if($db_type==2 || $db_type==1 )
	{
		if($rID)
		{
			oci_commit($con);   
			//echo 0;
		}
		else
		{
			oci_rollback($con);
			//echo 10;
		}
	}
	disconnect($con);

	exit();
}

?>