<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//----------------------------------------------------------
if ($action=="load_drop_down_com_location")
{
	echo create_drop_down( "cbo_com_location_id", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "" );
	exit();
}

//system id popup here----------------------// 
if ($action=="system_id_popup")
{
  	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
        function js_set_value(str)
        {
            $("#hidden_gate_pass_id").val(str);
            parent.emailwindow.hide(); 
        }
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="750" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>                
                <th width="170" class="must_entry_caption">Company</th>
                 <th width="150">Location</th>
                <th width="100">Gate Pass ID</th>
                <th width="200">Date Range</th>
                <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:70px" class="formbutton"  /></th>           
            </thead>
            <tbody>
                <tr>
                    <td>
                    	<? 
						echo create_drop_down( "cbo_company_name", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) order by company_name","id,company_name", 1, "-- Select Company --", $selected,";load_drop_down( 'get_out_entry_controller',this.value, 'load_drop_down_com_location', 'com_location_td' );","0" );
					    ?>					
                    </td>                    
                	<td id="com_location_td" >
                        <? 
                        echo create_drop_down( "cbo_com_location_id", 150, $blank_array,"", 1, "-- Select  --", 0, "",0 );
                        ?>
                    </td>                   
                    <td><input name="txt_get_pass" id="txt_get_pass"  style="width:100px" class="text_boxes" /></td>   
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_get_pass').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_com_location_id').value, 'create_gate_out_id_search_list_view', 'search_div', 'get_out_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />				
                    </td>
                </tr>
                <tr>                  
                    <td align="center" height="40" valign="middle" colspan="5">
                        <? echo load_month_buttons(1);  ?>
                        <input type="hidden" id="hidden_sys_number" value="hidden_sys_number" />
                    </td>
                </tr>    
            </tbody>
        </table>    
        <div align="center" valign="top" id="search_div"> </div> 
        </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_gate_out_id_search_list_view")
{
	$ex_data = explode("_",$data);
	$company = str_replace("'","",$ex_data[0]);
	$gate_pass_id =str_replace("'","", $ex_data[1]);
	$fromDate =str_replace("'","",$ex_data[2]);
	$toDate = str_replace("'","",$ex_data[3]);
	$location = str_replace("'","",$ex_data[4]);
	$user_name_arr=return_library_array( "select id, user_name from  user_passwd",'id','user_name');

    $company_cond=$pass_id_cond=$location_id_cond="";
	if( $company!=0 )  $company_cond=" and b.company_id=$company";
	if( $gate_pass_id!=0 )  $pass_id_cond=" and b.sys_number_prefix_num=$gate_pass_id";
	if( $location!=0 )  $location_id_cond=" and b.com_location_id=$location";
	if( $company==0 )
	{
		echo "Select Company";
        die;
	}

	if($db_type==0)
	{
	   if( $fromDate!=0 && $toDate!=0 ) $sql_cond= " and a.out_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
	}
	else if($db_type==2)
	{
	   if( $fromDate!=0 && $toDate!=0 ) $sql_cond= " and a.out_date  between '".change_date_format($fromDate,'mm-dd-yyyy','-',1)."' and '".change_date_format($toDate,'mm-dd-yyyy','-',1)."'";
	}
	?>
    <input type="hidden" id="hidden_gate_pass_id" value="" />
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="485"  class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="140">Gate Pass No</th>
                <th width="100">Out Date</th>               
                <th width="100">Out Time</th> 
                <th>Insert By</th>
            </thead>
        </table>
        <div style="width:490px; max-height:350px; overflow-y:scroll" id="list_container_batch" >	 
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="470" class="rpt_table" id="list_view">  
				<?
                $sql = "select a.id, a.gate_pass_id, a.out_date,a.inserted_by, a.out_time from inv_gate_out_scan a,inv_gate_pass_mst b where a.gate_pass_id=b.sys_number and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond  $company_cond $pass_id_cond $location_id_cond order by a.id desc";
                $res = sql_select($sql);
                $i=1;
                foreach($res as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; 
                    else $bgcolor="#FFFFFF";	 
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;" > 
                        <td width="30"><? echo $i; ?></td>
                        <td width="140"><p><? echo strtoupper($row[csf('gate_pass_id')]); ?></p></td>
                        <td width="100" align="center"><? echo change_date_format($row[csf('out_date')]); ?></td> 
                        <td width="100" align="center"><? echo $row[csf('out_time')]; ?></td> 
                        <td align="center"><? echo $user_name_arr[$row[csf('inserted_by')]]; ?></td> 
                    </tr>
                    <?
                    $i++;
                }
                ?>
            </table>
        </div>
    </div>
    <?	 
	exit();
}

if ($action=="getpass_id_popup")
{
  	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
        function js_set_value(str)
        {
            $("#hidden_gate_pass_id").val(str);
            parent.emailwindow.hide(); 
        }
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="850" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>                
                <th width="170" class="must_entry_caption">Company</th>
                <th width="150">Location</th>
                <th width="140">System ID</th>
                <th width="250">Date Range</th>
                <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
            </thead>
            <tbody>
                <tr>
                    <td>
                    	<? 
						echo create_drop_down( "cbo_company_name", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) order by company_name","id,company_name", 1, "-- Select Company --", $selected,";load_drop_down( 'get_out_entry_controller',this.value, 'load_drop_down_com_location', 'com_location_td' );","0" );
					    ?>					
                    </td>                    
                	<td id="com_location_td" >
                        <? 
                        echo create_drop_down( "cbo_com_location_id", 150, $blank_array,"", 1, "-- Select  --", 0, "",0 );
                        ?>
                    </td>
                    <td><input name="txt_get_pass" id="txt_get_pass"  style="width:140px" class="text_boxes" /></td>   
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date" />
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_get_pass').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_com_location_id').value, 'create_pass_id_search_list_view', 'search_div', 'get_out_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
                </tr>
                <tr>                  
                    <td align="center" height="40" valign="middle" colspan="5">
                        <? echo load_month_buttons(1);  ?>
                        <input type="hidden" id="hidden_sys_number" value="hidden_sys_number" />
                    </td>
                </tr>    
            </tbody>
        </table>    
        <div align="center" valign="top" id="search_div"> </div> 
        </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <input type="hidden" id="hidden_gate_pass_id" value="" />
    <?
	exit();
}

if($action=="create_pass_id_search_list_view")
{
	$ex_data = explode("_",$data);
	$company = str_replace("'","",$ex_data[0]);
	$gate_pass_id =str_replace("'","", $ex_data[1]);
	$fromDate =str_replace("'","",$ex_data[2]);
	$toDate = str_replace("'","",$ex_data[3]);
	$location = str_replace("'","",$ex_data[4]);
	$user_name_arr=return_library_array( "select id, user_name from  user_passwd",'id','user_name');

    $company_cond=$pass_id_cond=$location_id_cond="";
	if( $company!=0 )  $company_cond=" and company_id=$company";
	if( $gate_pass_id!=0 )  $pass_id_cond=" and sys_number_prefix_num=$gate_pass_id";
	if( $location!=0 )  $location_id_cond=" and com_location_id=$location";
	if( $company==0 )
	{
		echo "Select Company";
        die;
	}

	if($db_type==0)
	{
	    if( $fromDate!=0 && $toDate!=0 ) $sql_cond= " and out_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
	}
	else if($db_type==2)
	{
	    if( $fromDate!=0 && $toDate!=0 ) $sql_cond= " and out_date  between '".change_date_format($fromDate,'mm-dd-yyyy','-',1)."' and '".change_date_format($toDate,'mm-dd-yyyy','-',1)."'";
	}

	if ($db_type==0) $app_nes_setup_date=change_date_format(date('d-m-Y'),'yyyy-mm-dd');
	else $app_nes_setup_date=change_date_format(date('d-m-Y'), "", "",1);

	$approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '$app_nes_setup_date' and company_id='$company')) and page_id=43 and status_active=1 and is_deleted=0";   
	$app_need_setup=sql_select($approval_status);
	$approval_need=$app_need_setup[0][csf("approval_need")];

	$sql = "select id, sys_number_prefix_num, sys_number,inserted_by, basis, sent_by, insert_date, sent_to, out_date,challan_no,extract( year from insert_date) as year,vhicle_number,driver_name from inv_gate_pass_mst where  status_active=1 and is_deleted=0 and sys_number not in(select distinct gate_pass_id from inv_gate_out_scan where  gate_pass_id is not null and status_active=1 and is_deleted=0) $sql_cond $company_cond $pass_id_cond $location_id_cond order by id desc";

	if ($approval_need==1)
	{
		$sql = "select id, sys_number_prefix_num, sys_number,inserted_by, basis, sent_by, insert_date, sent_to, out_date,challan_no,extract( year from insert_date) as year,vhicle_number,driver_name from inv_gate_pass_mst where  status_active=1 and is_deleted=0 and approved=1 and sys_number not in(select distinct gate_pass_id from inv_gate_out_scan where  gate_pass_id is not null and status_active=1 and is_deleted=0) $sql_cond $company_cond $pass_id_cond $location_id_cond order by id desc";
	}

	$arr=array(2=>$get_pass_basis,8=>$user_name_arr);
	echo create_list_view("list_view", "Gate Pass No,Year,Basis,Sent By,Sent To,Gate Pass Date,Challan No,Insert Date Time,Inserted By","60,40,130,90,90,70,150,130,60","910","260",0, $sql , "js_set_value", "id,sys_number,vhicle_number,driver_name", "", 1, "0,0,basis,0,0,0,0,0,inserted_by", $arr, "sys_number_prefix_num,year,basis,sent_by,sent_to,out_date,challan_no,insert_date,inserted_by", "",'setFilterGrid("list_view",-1);','') ;	
	exit();
}

if($action=="create_sys_search_list_view")
{
	$ex_data = explode("_",$data);
	$company = str_replace("'","",$ex_data[0]);
	$gate_pass_id =str_replace("'","", $ex_data[1]);
	$fromDate =str_replace("'","",$ex_data[2]);
	$toDate = str_replace("'","",$ex_data[3]);
	$location = str_replace("'","",$ex_data[4]);
	
 	$company_cond=$pass_id_cond="";
	if( $company!=0 )  $company_cond=" and company_id=$company";
	if( $gate_pass_id!=0 )  $pass_id_cond=" and sys_number_prefix_num=$gate_pass_id";
	if($db_type==0)
	{
	    if( $fromDate!=0 && $toDate!=0 ) $sql_cond= " and out_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
	}
	else if($db_type==2)
	{
	    if( $fromDate!=0 && $toDate!=0 ) $sql_cond= " and out_date  between '".change_date_format($fromDate,'mm-dd-yyyy','-',1)."' and '".change_date_format($toDate,'mm-dd-yyyy','-',1)."'";
	}
	
	$sql = "select sys_number_prefix_num, sys_number, sent_by, sent_to, out_date,department_id,section,challan_no from  inv_gate_out_mst	where status_active=1 and is_deleted=0 $company_cond $pass_id_cond  $sql_cond ";
	//echo $sql;
    $sample_arr = return_library_array( "select id, sample_name from lib_sample",'id','sample_name');
	$arr=array(1=>$sample_arr,2=>$item_category,7=>$currency);
	echo create_list_view("list_view", "Gate Out No,,Item Catagory,Sent By,Sent To,Out Date,Challan No,Currency","70,150,110,100,100,80,70,70","800","260",0, $sql , "js_set_value", "sys_number", "", 1, "0,sample_id,item_category_id,0,0,0,0,currency_id", $arr, "sys_number,sample_id,item_category_id,sent_by,sent_to,out_date,challan_no,currency_id", "",'','') ;	
	exit();	
}

if($action=="populate_master_from_data")
{
	$sql="select sys_number,company_id,sample_id,item_category_id,sent_by,sent_to,out_date,challan_no,currency_id,gate_pass_no,time_hour,time_minute from inv_gate_out_mst where sys_number='$data'";
	//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{	
		echo "$('#cbo_sample').val(".$row[csf("sample_id")].");\n";
		echo "$('#cbo_item_category').val(".$row[csf("item_category_id")].");\n"; 		
		//echo "$('#hidden_type').val(".$row[csf("piworeq_type")].");\n";
		echo "$('#txt_sent_by').val('".$row[csf("sent_by")]."');\n";
		echo "$('#txt_sent_to').val('".$row[csf("sent_to")]."');\n";
		echo "$('#txt_receive_date').val('".change_date_format($row[csf("out_date")])."');\n";	
		echo "$('#txt_challan_no').val('".$row[csf("challan_no")]."');\n";
		echo "$('#cbo_currency').val('".$row[csf("currency_id")]."');\n";	
		echo "$('#txt_gate_pass_no').val(".$row[csf("gate_pass_no")].");\n";
		echo "$('#txt_start_hours').val(".$row[csf("time_hour")].");\n";	
		echo "$('#txt_start_minuties').val(".$row[csf("time_minute")].");\n"; 		  	
		//right side list view 
		//echo "show_list_view(".$row[csf("piworeq_type")]."+'**'+".$row[csf("pi_wo_req_id")].",'show_product_listview','list_product_container','requires/get_out_entry_controller','');\n";
	}
	exit();	
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	
	if( $operation==0 ) // Insert Here------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN");}
		$txt_gate_pass=strtoupper($txt_gate_pass);
		
		//echo $sql_se="select gate_pass_id from  inv_gate_out_scan where gate_pass_id=$txt_gate_pass and status_active=1 and is_deleted=0"; die;
		$res=sql_select($sql_se);
		foreach($res as $row)
		{
			$system_number=$row[csf('gate_pass_id')];
			
		}
        //OG-GPE-21-00101
        //echo "10**select gate_pass_id from inv_gate_out_scan where gate_pass_id=$txt_gate_pass and status_active=1 and is_deleted=0 "; die;
        if (is_duplicate_field( "gate_pass_id", "inv_gate_out_scan", "gate_pass_id=$txt_gate_pass and status_active=1 and is_deleted=0" ) == 1)
        {
            echo "11**0"; die;
        }
        else
        {
    		if( $system_number!=""){ echo 40; disconnect($con);die;}
    		$sql_pre="select id,out_date,sys_number,is_approved, time_hour, time_minute, insert_date, vhicle_number, driver_name from inv_gate_pass_mst where sys_number=$txt_gate_pass and status_active=1 and is_deleted=0";
    		//echo $sql_pre;die;
    		$result=sql_select($sql_pre);
			$vhicle_number=$driver_name='';
    		foreach($result as $val)
    		{
    			$pass_number=$val[csf('sys_number')];
    			$pass_number_id=$val[csf('id')];
    			$out_date=change_date_format($val[csf('out_date')]);
    			//$to_time=$out_date." ".$val[csf('time_hour')].":".$val[csf('time_minute')].":00";
                $to_time=$val[csf('insert_date')];
    			$to_time = strtotime("$to_time");

    			$today_date=date("d-m-Y H:m:s ");
    			$today_date = strtotime("$today_date");
    			$date_difference=$today_date-$to_time;
                //echo "5**".$today_date.'=='.$to_time; die;
    			$diff_hour=floor($date_difference/3600);
    			$is_approved=$val[csf('is_approved')];
				$vhicle_number=$val[csf('vhicle_number')];
				$driver_name=$val[csf('driver_name')];
    		} 
    		//echo "10**".$is_approved;die;
    		if($pass_number=="" )
    		{
    			echo "5**This Gate Pass No. Does Not Match";disconnect($con);die;
    		}
    		if($is_approved!=1)
    		{
				$company_query=sql_select("select company_id from inv_gate_pass_mst where sys_number=$txt_gate_pass");
				$company_id=$company_query[0][csf('company_id')];
				$query="select TOLERANT_PERCENT from variable_settings_inventory where company_name='$company_id' and variable_list=50";
				$hour= sql_select($query);				 
    			if($diff_hour>$hour[0]['TOLERANT_PERCENT'])  {echo "30**".$hour[0]['TOLERANT_PERCENT'];disconnect($con);die;}
    		}
    		//=====================================save start =================================================================================
    		$id=return_next_id("id", "inv_gate_out_scan", 1);			
      		$field_array2="id,inv_gate_pass_mst_id,gate_pass_id,out_date,out_time,inserted_by,insert_date,status_active,is_deleted";
    		$data_array2="(".$id.",".$pass_number_id.",".strtoupper($txt_gate_pass).",".$txt_gate_out_date.",".$txt_gate_out_time.",'".$user_id."','".$pc_date_time."',1,0)";
    		//echo "insert into inv_gate_out_scan($field_array2)values".$data_array2;
    		$rID=sql_insert("inv_gate_out_scan",$field_array2,$data_array2,1); 
    		
    		if($db_type==0)
    		{
    			if($rID )
    			{
    				$get_pass_update = execute_query("update inv_gate_pass_mst a set a.is_gate_out=1, a.vhicle_number=$txt_vehicle_number, a.driver_name=$txt_driver_name where a.id=$pass_number_id");
    				if($get_pass_update)
    				{
    					mysql_query("COMMIT");  
    					echo "0**"."**".str_replace("'",'',$txt_gate_pass)."**".str_replace("'",'',$id);
    				}
    				else
    				{
    					mysql_query("ROLLBACK");
    					echo "10**"."**".str_replace("'",'',$txt_gate_pass)."**".str_replace("'",'',$id);
    				}    				
    			}
    			else
    			{
    				mysql_query("ROLLBACK");
    				echo "10**"."**".str_replace("'",'',$txt_gate_pass)."**".str_replace("'",'',$id);
    			}
    		}
    		if($db_type==2)
    		{	
    			if($rID )
    			{
    				$sys_number = strtoupper($txt_gate_pass);
    				$get_pass_update = execute_query("update inv_gate_pass_mst a set a.is_gate_out=1, a.vhicle_number=$txt_vehicle_number, a.driver_name=$txt_driver_name where a.sys_number=$sys_number");
    				if($get_pass_update)
    				{
    					oci_commit($con);
    					echo "0**"."**".str_replace("'",'',$txt_gate_pass)."**".str_replace("'",'',$id);
    				}
    				else
    				{
    					oci_rollback($con);
    					echo "10**"."**".str_replace("'",'',$txt_gate_pass)."**".str_replace("'",'',$id);
    				}    				
    			}
    			else
    			{
    				oci_rollback($con);
    				echo "10**"."**".str_replace("'",'',$txt_gate_pass)."**".str_replace("'",'',$id);
    			}
    		}
    		disconnect($con);
    		die;
    	}	
    }
}

if($action=="show_dtls_list_view")
{
 	$sql = "select b.id as id,b.item_description,b.quantity,b.uom,b.rate,b.amount,b.remarks from inv_gate_out_mst a,inv_gate_out_dtls b where a.id=b.mst_id and a.sys_number='$data' and b.status_active=1 and b.is_deleted=0 	"; 
	//echo $data; die;
	$arr=array(2=>$unit_of_measurement);
 	echo create_list_view("list_view", "Item Description,Qnty,UOM,Rate,Amount,Remarks","250,70,40,100,150,190","850","260",0, $sql, "get_php_form_data", "id", "'child_form_input_data','requires/get_out_entry_controller'", 1, "0,0,uom,0,0,0", $arr, "item_description,quantity,uom,rate,amount,remarks", "","",'0,2,1,2,2,0',"2  ,quantity,'','',amount,''");	
	exit();
}

if($action=="child_form_input_data")
{
	$sql="select id,item_description,quantity,uom,rate,amount,remarks from inv_gate_out_dtls where id=$data"; 
	$result = sql_select($sql);
	
	foreach($result as $row)
	{
		echo "$('#txt_item_description').val('".$row[csf("item_description")]."');\n";
		echo "$('#txt_quantity').val(".$row[csf("quantity")].");\n";
		echo "$('#cbo_uom').val(".$row[csf("uom")].");\n";
		echo "$('#txt_rate').val(".$row[csf("rate")].");\n";
		echo "$('#txt_amount').val(".$row[csf("amount")].");\n";		
 		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		//update id here
		echo "$('#update_id').val(".$row[csf("id")].");\n";		
		//echo "show_list_view(".$row[csf("wo_po_type")]."+'**'+".$row[csf("wo_pi_no")].",'show_product_listview','list_product_container','requires/yarn_receive_controller','');\n";
		echo "set_button_status(1, permission, 'fnc_getout_entry',1,1);\n";
	}
	exit();
}

if ($action=="get_out_entry_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$store_library=return_library_array( "select id, store_name from   lib_store_location", "id", "store_name"  );
	$sample_library=return_library_array( "select id, sample_name from  lib_sample", "id", "sample_name"  );
	
	$sql="select id, sys_number, company_id, sample_id, item_category_id, sent_by, sent_to, out_date, challan_no, currency_id, gate_pass_no, time_hour, time_minute from   inv_gate_out_mst where company_id='$data[0]' and sys_number='$data[1]' and status_active=1 and is_deleted=0 ";
	//echo $sql;
	$dataArray=sql_select($sql);
    ?>
    <div style="width:930px;" align="center">
    <table width="900" cellspacing="0" align="center" border="0">
        <tr>
            <td colspan="7" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
            <td colspan="7" align="center">
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
					?>
						Plot No: <? echo $result['plot_no']; ?> 
						Level No: <? echo $result['level_no']?>
						Road No: <? echo $result['road_no']; ?> 
						Block No: <? echo $result['block_no'];?> 
						City No: <? echo $result['city'];?> 
						Zip Code: <? echo $result['zip_code']; ?> 
						Province No: <?php echo $result['province'];?> 
						Country: <? echo $country_arr[$result['country_id']]; ?><br> 
						Email Address: <? echo $result['email'];?> 
						Website No: <? echo $result['website'];
					}
                ?> 
            </td>
        </tr>
        <tr>
            <td colspan="7" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
        </tr>
        <tr>
            <td width="160"><strong>System ID:</strong></td> <td width="175px"><? echo $dataArray[0][csf('sys_number')]; ?></td>
            <td width="120"><strong>Sample:</strong></td><td width="175px" ><? echo $sample_library[$dataArray[0][csf('sample_id')]]; ?></td>
            <td width="125"><strong>Item Category:</strong></td><td width="175px" colspan="2"><? echo  $item_category[$dataArray[0][csf('item_category_id')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Sent By:</strong></td> <td width="175px"><? echo $dataArray[0][csf('sent_by')]; ?></td>
            <td><strong>Sent To:</strong></td><td width="175px" ><? echo $dataArray[0][csf('sent_to')]; ?></td>
            <td><strong>Out Date:</strong></td><td width="175px" colspan="2"><? echo change_date_format($dataArray[0][csf('out_date')]); ?></td>
        </tr>
        <tr>
            <td><strong>Challan No :</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Gate Pass No:</strong></td><td width="175px"><? echo $dataArray[0][csf('gate_pass_no')]; ?></td>
            <td><strong>Out-Time:</strong></td><td width="85px" ><? echo $dataArray[0][csf('time_hour')]." HH"; ?></td><td width="85px"><? echo $dataArray[0][csf('time_minute')]." Min"; ?></td>
        </tr>
    </table>
    <br>
    <table align="center" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="180" align="center">Item Description</th>
            <th width="50" align="center">UOM</th>
            <th width="80" align="center">Quantity</th>
            <th width="80" align="center">Rate</th> 
            <th width="80" align="center">Amount </th>
            <th width="180" align="center">Remarks</th>
        </thead>
        <?
        $i=1;
    	$gate_id=$dataArray[0][csf('id')];
    	$sql_dtls= " select id, item_description, quantity, uom, rate, amount, remarks from  inv_gate_out_dtls where mst_id=$gate_id and status_active=1 and is_deleted=0 ";
    	//echo $sql_dtls;
    	$sql_result=sql_select($sql_dtls);
    	
    	foreach($sql_result as $row)
    	{
    		if ($i%2==0) $bgcolor="#E9F3FF";
    		else $bgcolor="#FFFFFF";
    		?>
    		<tr bgcolor="<? echo $bgcolor; ?>">
                <td><? echo $i; ?></td>
                <td><?  echo $row[csf('item_description')]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                <td align="right"><? echo $row[csf('quantity')]; ?></td>
                <td align="right"><? echo number_format($row[csf('rate')],2,'.',''); ?></td>
                <td align="right"><? echo number_format($row[csf('amount')],2,'.',''); ?></td>
                <td><? echo $row[csf('remarks')]; ?></td>
    		</tr>
    		<?
            $i++;
        }
    	?>
    </table>
    </div>
    <div>
		<?
        echo signature_table(34, $data[0], "900px");
        ?>
    </div>    
    <?
    exit();
}

if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);

	$txt_gate_pass=strtoupper($data);
	  $get_pass_sql = "SELECT SYS_NUMBER,COMPANY_ID,BASIS,COM_LOCATION_ID as LOCATION_ID,RETURNABLE,CHALLAN_NO,ISSUE_ID from  inv_gate_pass_mst where  status_active=1 and is_deleted=0 and sys_number='$txt_gate_pass' order by sys_number";  
	$get_pass_result=sql_select($get_pass_sql);
	//$get_pass_result[0]['SYS_NUMBER'];
	$company_id=$get_pass_result[0]['COMPANY_ID'];	
	$dataStr=$get_pass_result[0]['SYS_NUMBER'].'**'.$get_pass_result[0]['COMPANY_ID'].'**'.$get_pass_result[0]['BASIS'].'**'.$get_pass_result[0]['LOCATION_ID'].'**'.$get_pass_result[0]['RETURNABLE'].'**'.$get_pass_result[0]['CHALLAN_NO'].'**'.$get_pass_result[0]['ISSUE_ID'];
	
	
	//echo $dataStr; die;
		
	echo "$('#txt_get_pass_data').val('".$dataStr."');\n";
	
	
	//function fnc_report_button($company_id,$module_id,$page_id,$first_button);
	$print_report_format_arr=explode(",",fnc_report_button($company_id,6,38,0));
	
	//echo "<pre>";
	
	//echo reset($print_report_format_arr); die;
	//print_r($print_report_format_arr); die;
	
	//die;
	echo "$('#txt_gate_pass_first_print_button').val('".reset($print_report_format_arr)."');\n";
	
	// $print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$company_id."'  and module_id=6 and report_id=38 and is_deleted=0 and status_active=1");	
	// $print_report_format_arr=explode(",",$print_report_format);
	//print_r($print_report_format_arr);
	echo "$('#id_print_to_button').hide();\n";
	echo "$('#Printt1').hide();\n";
	echo "$('#id_print_to_button4').hide();\n";
	echo "$('#id_print_to_button5').hide();\n";
	echo "$('#id_print_to_button6').hide();\n";
	echo "$('#id_print_to_button7').hide();\n";
	echo "$('#id_print_to_button8').hide();\n";
	echo "$('#id_print_to_button9').hide();\n";
	echo "$('#id_print_to_button10').hide();\n";
	echo "$('#id_print_to_button11').hide();\n";
	echo "$('#id_print_to_button14').hide();\n";
	echo "$('#id_print_to_button12').hide();\n";
	echo "$('#print13').hide();\n";
	echo "$('#print6').hide();\n";	

	if(count($print_report_format_arr)>0)
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==115){echo "$('#Printt1').show();\n";}
			if($id==116){echo "$('#id_print_to_button').show();\n";}
			if($id==136){echo "$('#with_color_size_print').show();\n";}			
			if($id==137){echo "$('#id_print_to_button5').show();\n";}
			if($id==196){echo "$('#id_print_to_button6').show();\n";}
			if($id==199){echo "$('#id_print_to_button7').show();\n";}
			if($id==206){echo "$('#id_print_to_button8').show();\n";}
			if($id==207){echo "$('#id_print_to_button9').show();\n";}
			if($id==208){echo "$('#id_print_to_button10').show();\n";}
			if($id==212){echo "$('#id_print_to_button11').show();\n";}
			if($id==271){echo "$('#id_print_to_button14').show();\n";}
			if($id==129){echo "$('#id_print_to_button12').show();\n";}
			if($id==191){echo "$('#print13').show();\n";}
			if($id==161){echo "$('#print6').show();\n";}
		}
	}	
	exit();	
}

if($action=="scan_getpass")
{
	extract($_REQUEST);

	 $txt_gate_pass=strtoupper($data);
	 $get_pass_sql = "SELECT SYS_NUMBER,VHICLE_NUMBER,DRIVER_NAME from  inv_gate_pass_mst where  status_active=1 and is_deleted=0 and sys_number='$txt_gate_pass' "; 
	$get_pass_result=sql_select($get_pass_sql);
	if(count($get_pass_result)>0)
	{
		if($get_pass_result[0]["VHICLE_NUMBER"]!=null)
		{
			echo "$('#txt_vehicle_number').val('".$get_pass_result[0]["VHICLE_NUMBER"]."').attr('disabled','disabled');\n";			
		}
		else
		{
			echo "$('#txt_vehicle_number').val(null).removeAttr('disabled');\n";			
		}
		if($get_pass_result[0]["DRIVER_NAME"]!=null)
		{
			echo "$('#txt_driver_name').val('".$get_pass_result[0]["DRIVER_NAME"]."').attr('disabled','disabled');\n";			
		}
		else
		{
			echo "$('#txt_driver_name').val(null).removeAttr('disabled');\n";			
		}
	}
	else
	{
		echo "$('#txt_gate_pass').val(null);\n";			
	}
	
	exit();	
}

?>