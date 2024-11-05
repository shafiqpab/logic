<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$type=$_REQUEST['type'];
/*if ($_SESSION['logic_erp']["data_level_secured"]==1)
{
	if($_SESSION['logic_erp']["buyer_id"]!=0) $buyer_cond=" and a.id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
	if($_SESSION['logic_erp']["company_id"]!=0) $company_cond=" and id in (".$_SESSION['logic_erp']["company_id"].")"; else $company_cond="";
}
else
{
	$buyer_cond="";	$company_cond="";
}*/
$permission=$_SESSION['page_permission'];
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );

//---------------------------------------------------- Start---------------------------------------------------------------
//Master Table=============================================================================================================
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );     	 
} 

if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent", 160, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21))  order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" );   
	 	 
}

if ($action=="cm_cost_predefined_method")
{
	$cm_cost_method=return_field_value("cm_cost_method", "variable_order_tracking", "company_name=$data  and variable_list=22 and status_active=1 and is_deleted=0");
	if($cm_cost_method=="")
	{
		$cm_cost_method=0;
	}
	echo $cm_cost_method;
	die;
	 	 
}

if ($action=="asking_profit_percent")
{
	$data=explode("_",$data);
	if($data[1]=="" || $data[1]==0)
	{
		if($db_type==0)
		{
		   $txt_quotation_date=change_date_format(date('d-m-Y'), "yyyy-mm-dd", "-");	
		}
		if($db_type==2)
		{
			$txt_quotation_date=change_date_format(date('d-m-Y'), "yyyy-mm-dd", "-",1);
		}
	}
	else
	{
		if($db_type==0)
		{
		   $txt_quotation_date=change_date_format($data[1], "yyyy-mm-dd", "-");	
		}
		if($db_type==2)
		{
			$txt_quotation_date=change_date_format($data[1], "yyyy-mm-dd", "-",1)	;
		}
	}
	
	$asking_profit=return_field_value("asking_profit", "lib_standard_cm_entry", "company_id=$data[0]  and '$txt_quotation_date' between applying_period_date and applying_period_to_date  and status_active=1 and is_deleted=0");
	if($asking_profit=="")
	{
		$asking_profit=0;
	}
	echo $asking_profit;
	die;
	 	 
}

if ($action=="cost_per_minute")
{
	$data=explode("_",$data);
	if($data[1]=="" || $data[1]==0)
	{
		if($db_type==0)
		{
		   $txt_quotation_date=change_date_format(date('d-m-Y'), "yyyy-mm-dd", "-");	
		}
		if($db_type==2)
		{
			$txt_quotation_date=change_date_format(date('d-m-Y'), "yyyy-mm-dd", "-",1);
		}
	}
	else
	{
		if($db_type==0)
		{
		   $txt_quotation_date=change_date_format($data[1], "yyyy-mm-dd", "-");	
		}
		if($db_type==2)
		{
			$txt_quotation_date=change_date_format($data[1], "yyyy-mm-dd", "-",1)	;
		}
	}
	
	$monthly_cm_expense=0;
	$no_factory_machine=0;
	$working_hour=0;
	$cost_per_minute=0;
	$sql="select monthly_cm_expense,no_factory_machine,working_hour,cost_per_minute from lib_standard_cm_entry where company_id=$data[0] and '$txt_quotation_date' between applying_period_date and applying_period_to_date  and status_active=1 and is_deleted=0 LIMIT 1";
			//Oracle
			/*$sql="select monthly_cm_expense,no_factory_machine,working_hour,cost_per_minute from lib_standard_cm_entry where company_id=$data  and status_active=1 and is_deleted=0";*/

	$data_array=sql_select($sql);
	foreach ($data_array as $row)
	{
		if($row[csf("monthly_cm_expense")] !="")
		{
		  $monthly_cm_expense=$row[csf("monthly_cm_expense")];
		}
		if($row[csf("no_factory_machine")] !="")
		{
		  $no_factory_machine=$row[csf("no_factory_machine")];
		}
		if($row[csf("working_hour")] !="")
		{
		  $working_hour=$row[csf("working_hour")];
		}
		if($row[csf("cost_per_minute")] !="")
		{
		  $cost_per_minute=$row[csf("cost_per_minute")];
		}
		
	}
	$data=$monthly_cm_expense."_".$no_factory_machine."_".$working_hour."_".$cost_per_minute;
	echo $data;

	 	 
}
if($action=="txt_commission_pre_cost")
{
	$total_amount=0;
	//$data=explode("_",$data);
	$sql="select sum(commission_amount) as commission_amount from wo_pri_quo_commiss_cost_dtls where quotation_id=$data and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
	  $total_amount	=$row[csf("commission_amount")];
	}
	echo $total_amount;
}

if($action=="cofirm_price_commision")
{
	$total_amount=0;
	$data=explode("_",$data);
	$sql="select commission_base_id,commision_rate from wo_pri_quo_commiss_cost_dtls where quotation_id=$data[0] and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
		if($row[csf("commission_base_id")]==1)
		{
			 $txtcommissionrate_percent=$row[csf("commision_rate")]/100;
			 $amount=($data[2]/(1-$txtcommissionrate_percent))-$data[2];
			 $total_amount+=$amount;
		}
		if($row[csf("commission_base_id")]==2)
		{
		   if($data[1]==1)
		   {
			$amount=$row[csf("commision_rate")]*12*1;
			$total_amount+=$amount;
		   }
		   if($data[1]==2)
		   {
			$amount=$row[csf("commision_rate")]*1;
			$total_amount+=$amount;
		   }
		   if($data[1]==3)
		   {
			$amount=$row[csf("commision_rate")]*12*2;
			$total_amount+=$amount;
		   }
		   if($data[1]==4)
		   {
			$amount=$row[csf("commision_rate")]*12*3;
			$total_amount+=$amount;
		   }
		   if($data[1]==5)
		   {
			$amount=$row[csf("commision_rate")]*12*4;
			$total_amount+=$amount;
		   }
			
			//$amount=$row[csf("commision_rate")]*1;
			//$total_amount+=$amount;
		}
		if($row[csf("commission_base_id")]==3)
		{
			if($data[1]==1)
			   {
				$amount=$row[csf("commision_rate")]*1*1;
				$total_amount+=$amount;
			   }
			   if($data[1]==2)
			   {
				$amount=$row[csf("commision_rate")]/12;
				$total_amount+=$amount;
			   }
			   if($data[1]==3)
			   {
				$amount=$row[csf("commision_rate")]*1*2;
				$total_amount+=$amount;
			   }
			   if($data[1]==4)
			   {
				$amount=$row[csf("commision_rate")]*1*3;
				$total_amount+=$amount;
			   }
			   if($data[1]==5)
			   {
				$amount=$row[csf("commision_rate")]*1*4;
				$total_amount+=$amount;
			   }
			//$amount=$row[csf("commision_rate")]/12;
			//$total_amount+=$amount;
		}
	}
	echo $total_amount;
}

if($action=="lead_time_calculate")
{
	$data=explode("_",$data);
	$txt_est_ship_date=gmdate('Y-m-d',strtotime( $data[0]));
	$txt_op_date=gmdate('Y-m-d',strtotime( $data[1]));
	$dayes=datediff('d',$txt_op_date,$txt_est_ship_date);
	if($dayes >= 7)
	{
	$day=$dayes%7;
	$week=($dayes-$day)/7;
	    if($week>1)
		{
			$week_string="W";
		}
		else
		{
			$week_string="W";
		}
		if($day>1)
		{
			$day_string="D";
		}
		else
		{
			$day_string="D";
		}
		if($day != 0)
	    {
		echo $week." ".$week_string." ".$day." ".$day_string;
		}
		else
		{
		echo $week." ".$week_string;
		}
	}
	else
	{
	if($dayes>1)
		{
			$day_string="Days";
		}
		else
		{
			$dayes="Day";
		}	
		echo $dayes." ".$day_string;
	}
	
}

if ($action=="quotation_id_popup")
{
	//inquery_id_popup
  	echo load_html_head_contents("Quotation Entry","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);

?>
     
	<script>
	function js_set_value( quotation_id )
	{
		document.getElementById('selected_id').value=quotation_id;
		parent.emailwindow.hide();
	}
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="1000" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
                     <thead>
                        	<th  colspan="8">
                              <?
                               echo create_drop_down( "cbo_string_search_type", 140, $string_search_type,'', 1, "-- Searching Type --" );
                              ?>
                            </th>
                        
                        </thead>
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="100">Quotation ID</th>
                        <th width="100">Inquery ID</th>
                        <th width="100">Buyer Request</th>
                        <th width="100">Style Reff.</th>
                        <th width="180">Quotation Date Range</th>
                        
                        <th width="100"></th>           
                    </thead>
        			<tr>
                    	<td> <input type="hidden" id="selected_id">
							<? 
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'quotation_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                        </td>
                   	<td id="buyer_td">
                     <? 
						echo create_drop_down( "cbo_buyer_name", 150, $blank_array,'', 1, "-- Select Buyer --" );
					?>	
                    </td>
                    <td >  
                        <input type="text" style="width:70px" class="text_boxes"  name="txt_quotation_no" id="txt_quotation_no"  />	
                    </td>
                     <td >  
                        <input type="text" style="width:70px" class="text_boxes"  name="txt_inquery_no" id="txt_inquery_no"  />	
                    </td>
                     <td >  
                        <input type="text" style="width:100px" class="text_boxes"  name="txt_buyer_request" id="txt_buyer_request"  />	
                    </td>
                    <td  align="center">				
                        <input type="text" style="width:100px" class="text_boxes"  name="txt_style" id="txt_style"  />			
                    </td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_quotation_no').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_inquery_no').value+'_'+document.getElementById('txt_buyer_request').value, 'create_quotation_id_list_view', 'search_div', 'quotation_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        		</tr>
             </table>
          </td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle"><? echo load_month_buttons(1);  ?>
            </td>
            </tr>
        <tr>
            <td align="center"valign="top" id="search_div"> 
	
            </td>
        </tr>
    </table>    
     
    </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_quotation_id_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";// { echo "Please Select Buyer First."; die; }
	$quotation_id_cond="";
	$style_cond="";
	$inquery_cond="";
	$buyer_request_cond="";
	if($data[4]==1)
		{
		   if (trim($data[5])!="") $quotation_id_cond=" and a.id='$data[5]'";
		   if (trim($data[6])!="") $style_cond=" and a.style_ref='$data[6]'";
		   if (trim($data[7])!="") $inquery_cond=" and b.system_number_prefix_num='$data[7]'";
		   if (trim($data[8])!="") $buyer_request_cond=" and b.buyer_request='$data[8]'";
		}
	
	if($data[4]==4 || $data[4]==0)
		{
		  if (trim($data[5])!="") $quotation_id_cond=" and a.id like '%$data[5]%' ";
		  if (trim($data[6])!="") $style_cond=" and a.style_ref like '%$data[6]%' ";
		  if (trim($data[7])!="") $inquery_cond=" and b.system_number_prefix_num like '%$data[7]%' ";
		  if (trim($data[8])!="") $buyer_request_cond=" and b.buyer_request like '%$data[8]%' ";
		}
	
	if($data[4]==2)
		{
		  if (trim($data[5])!="") $quotation_id_cond=" and a.id like '$data[5]%' "; 
		  if (trim($data[6])!="") $style_cond=" and a.style_ref like '$data[6]%' ";
		   if (trim($data[7])!="") $inquery_cond=" and b.system_number_prefix_num like '$data[7]%' ";
		  if (trim($data[8])!="") $buyer_request_cond=" and b.buyer_request like '$data[8]%' ";
		}
	
	if($data[4]==3)
		{
		  if (trim($data[5])!="") $quotation_id_cond=" and a.id like '%$data[5]' ";
		  if (trim($data[6])!="") $style_cond=" and a.style_ref like '%$data[6]' "; 
		  if (trim($data[7])!="") $inquery_cond=" and b.system_number_prefix_num like '%$data[7]' ";
		  if (trim($data[8])!="") $buyer_request_cond=" and b.buyer_request like '%$data[8]' ";
		}
	
    if($db_type==0)
	    {
			if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and a.est_ship_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $est_ship_date ="";
		
		}
	   if($db_type==2)
	    {
			if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and a.est_ship_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $est_ship_date ="";
		
		}
	 
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (1=>$comp,2=>$buyer_arr,7=>$pord_dept);
	$sql= "select a.id,a.company_id, a.buyer_id,b.system_number_prefix_num,b.buyer_request, a.style_ref,a.style_desc,a.pord_dept,a.offer_qnty,a.est_ship_date from  wo_price_quotation a left join wo_quotation_inquery b on a.inquery_id=b.id and b.status_active=1  and b.is_deleted=0 where a.status_active=1  and a.is_deleted=0  $company $buyer $est_ship_date $quotation_id_cond $style_cond $inquery_cond $buyer_request_cond order by id";
	//echo $sql;
	echo  create_list_view("list_view", "Quotation ID,Company,Buyer Name,Inquery ID,Buyer Req.,Style Ref,Style Desc.,Prod. Dept., Offer Qnty, Est Ship Date", "80,120,100,70,80,100,140,100,80","1050","290",0, $sql , "js_set_value", "id", "", 1, "0,company_id,buyer_id,0,0,0,0,pord_dept,0,0", $arr , "id,company_id,buyer_id,system_number_prefix_num,buyer_request,style_ref,style_desc,pord_dept,offer_qnty,est_ship_date", "",'','0,0,0,0,0,0,0,0,2,3') ;
} 


if ($action=="inquery_id_popup")
{
	//inquery_id_popup
  	echo load_html_head_contents("Inquery Entry","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);

?>
     
	<script>
	function js_set_value( quotation_id )
	{
		document.getElementById('selected_id').value=quotation_id;
		parent.emailwindow.hide();
	}
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="880" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
                        <thead>
                        	<th  colspan="7">
                              <?
							  
							  //  $string_search_type=array(1=>"Exact",2=>"Starts with",3=>"Ends with",4=>"Contents");
                               echo create_drop_down( "cbo_string_search_type", 140, $string_search_type,'', 1, "-- Search Catagory --" );
                              ?>
                            </th>
                        
                        </thead>
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="80">Inquery ID</th>
                        <th width="100">Style Reff.</th>
                        <th width="80">Season</th>
                        <th width="200">Inquery Date Range</th><th></th>           
                    </thead>
        			<tr>
                    	<td> <input type="hidden" id="selected_id">
							<? 
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'quotation_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                        </td>
                   	<td id="buyer_td">
                     <? 
						echo create_drop_down( "cbo_buyer_name", 150, $blank_array,'', 1, "-- Select Buyer --" );
					?>	</td>
                     <td width="80">  
                        <input type="text" style="width:80px" class="text_boxes"  name="txt_inquery_no" id="txt_inquery_no"  />	
                    </td>
                    <td width="100" align="center">				
                        <input type="text" style="width:100px" class="text_boxes"  name="txt_style" id="txt_style"  />			
                    </td>
                     <td width="80">  
								<input type="text" style="width:80px" class="text_boxes"  name="txt_season" id="txt_season"  />	
                            </td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_inquery_no').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_season').value, 'create_inquery_id_list_view', 'search_div', 'quotation_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        		</tr>
             </table>
          </td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle"><? echo load_month_buttons(1);  ?>
            </td>
            </tr>
        <tr>
            <td align="center"valign="top" id="search_div"> 
	
            </td>
        </tr>
    </table>    
     
    </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_inquery_id_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and buyer_id='$data[1]'"; else $buyer="" ;
	if (trim($data[7])!="") $season_cond=" and season='$data[7]'"; else $season_cond="" ;
	if($data[6]==1)
		{
		   if (trim($data[4])!="") $inquery_id_cond=" and system_number_prefix_num='$data[4]'"; else $style_id_cond="";
		   if (trim($data[5])!="") $style_cond=" and style_refernce='$data[5]'"; else $style_cond="";
		}
	
	if($data[6]==4 || $data[6]==0)
		{
		  if (trim($data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '%$data[4]%' "; else $style_id_cond="";
		  if (trim($data[5])!="") $style_cond=" and style_refernce like '%$data[5]%' "; else $style_cond="";
		}
	
	if($data[6]==2)
		{
		  if (trim($data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '$data[4]%' "; else $style_id_cond="";
		  if (trim($data[5])!="") $style_cond=" and style_refernce like '$data[5]%' "; else $style_cond="";
		}
	
	if($data[6]==3)
		{
		  if (trim($data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '%$data[4]' "; else $style_id_cond="";
		  if (trim($data[5])!="") $style_cond=" and style_refernce like '%$data[5]' "; else $style_cond="";
		}
	
	
	if($db_type==0)
	{
	  if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and inquery_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $est_ship_date ="";
	}
	if($db_type==2)
		{
		  if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and inquery_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $est_ship_date ="";
		}
	 
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (1=>$comp,2=>$buyer_arr);
	$sql= "select id,system_number_prefix_num,company_id, buyer_id,season,inquery_date,buyer_request,style_refernce from  wo_quotation_inquery where status_active=1  and is_deleted=0 $company $buyer $est_ship_date $inquery_id_cond $style_cond $season_cond and id not in(select inquery_id from wo_price_quotation where company_id='$data[0]' and status_active=1 and is_deleted=0)
	order by id";
	//echo $sql;
	echo  create_list_view("list_view", "Inquery ID,Company,Buyer Name,Style Ref,Season,Inquery Date, Buyer Request", "70,120,100,130,100,100","800","280",0, $sql , "js_set_value", "id,company_id,buyer_id,style_refernce,system_number_prefix_num,Season", "", 1, "0,company_id,buyer_id,0,0,0,0", $arr , "system_number_prefix_num,company_id,buyer_id,style_refernce,season,inquery_date,buyer_request", "",'','0,0,0,0,0,3,0') ;
} 

if ($action=="populate_data_from_search_popup")
{
	$cbo_approved_status=="";

	$data_array=sql_select("select id,inquery_id, company_id, buyer_id, style_ref, revised_no, pord_dept,product_code, style_desc, currency, agent, offer_qnty, region, color_range, incoterm, incoterm_place, machine_line, prod_line_hr, fabric_source, costing_per, quot_date, est_ship_date,op_date, factory, remarks, garments_nature,order_uom,gmts_item_id,set_break_down,total_set_qnty ,cm_cost_predefined_method_id,exchange_rate,sew_smv,cut_smv,sew_effi_percent,cut_effi_percent,efficiency_wastage_percent,season, approved,m_list_no,bh_marchant,inserted_by, insert_date, status_active, is_deleted from wo_price_quotation where id='$data'");
	foreach ($data_array as $row)
	{
		echo "load_drop_down( 'requires/quotation_entry_controller', '".$row[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' ); load_drop_down( 'requires/quotation_entry_controller', '".$row[csf("company_id")]."', 'load_drop_down_agent', 'agent_td' );cm_cost_predefined_method('".$row[csf("company_id")]."') ;\n";
		//echo "show_hide_button('".$row[csf(order_uom)]."')\n";
		echo "change_caption_cost_dtls('".$row[csf(costing_per)]."','change_caption_dzn')\n";
		echo "change_caption_cost_dtls('".$row[csf(order_uom)]."','change_caption_pcs')\n";

         $inquery_id_prifix=return_field_value("system_number_prefix_num", "wo_quotation_inquery", "id=".$row[csf("inquery_id")]."");
		echo "document.getElementById('txt_inquery_prifix').value = '".$inquery_id_prifix."';\n";
		echo "document.getElementById('txt_inquery_id').value = '".$row[csf("inquery_id")]."';\n"; 
		
		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("id")]."';\n"; 
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";  
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref")]."';\n";  
		echo "document.getElementById('txt_revised_no').value = '".$row[csf("revised_no")]."';\n";  
		echo "document.getElementById('cbo_pord_dept').value = '".$row[csf("pord_dept")]."';\n";
		echo "document.getElementById('txt_product_code').value = '".$row[csf("product_code")]."';\n";
		echo "document.getElementById('txt_style_desc').value = '".$row[csf("style_desc")]."';\n";  
		echo "document.getElementById('cbo_currercy').value = '".$row[csf("currency")]."';\n";  
		echo "document.getElementById('cbo_agent').value = '".$row[csf("agent")]."';\n";  
		echo "document.getElementById('txt_offer_qnty').value = '".$row[csf("offer_qnty")]."';\n";  
		echo "document.getElementById('cbo_region').value = '".$row[csf("region")]."';\n";  
		echo "document.getElementById('cbo_color_range').value = '".$row[csf("color_range")]."';\n";  
		echo "document.getElementById('cbo_inco_term').value = '".$row[csf("incoterm")]."';\n";  
		echo "document.getElementById('txt_incoterm_place').value = '".$row[csf("incoterm_place")]."';\n";  
		echo "document.getElementById('txt_machine_line').value = '".$row[csf("machine_line")]."';\n";  
		echo "document.getElementById('txt_prod_line_hr').value = '".$row[csf("prod_line_hr")]."';\n";  
		echo "document.getElementById('cbo_costing_per').value = '".$row[csf("costing_per")]."';\n";  
		echo "document.getElementById('txt_quotation_date').value = '".change_date_format($row[csf("quot_date")],'dd-mm-yyyy','-')."';\n";  
		echo "document.getElementById('txt_est_ship_date').value = '".change_date_format($row[csf("est_ship_date")],'dd-mm-yyyy','-')."';\n"; 
		echo "document.getElementById('txt_op_date').value = '".change_date_format($row[csf("op_date")],'dd-mm-yyyy','-')."';\n"; 
		echo "document.getElementById('txt_factory').value = '".$row[csf("factory")]."';\n";  
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n"; 
		echo "document.getElementById('garments_nature').value = '".$row[csf("garments_nature")]."';\n";  
		echo "document.getElementById('cbo_order_uom').value = '".$row[csf("order_uom")]."';\n";  
		echo "document.getElementById('item_id').value = '".$row[csf("gmts_item_id")]."';\n";  
		echo "document.getElementById('set_breck_down').value = '".$row[csf("set_break_down")]."';\n";  
		echo "document.getElementById('tot_set_qnty').value = '".$row[csf("total_set_qnty")]."';\n";  
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_approved_status').value = '".$row[csf("approved")]."';\n"; 
		//echo "document.getElementById('cm_cost_predefined_method_id').value = '".$row[csf("cm_cost_predefined_method_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('txt_sew_smv').value = '".$row[csf("sew_smv")]."';\n";
		echo "document.getElementById('txt_cut_smv').value = '".$row[csf("cut_smv")]."';\n";
		echo "document.getElementById('txt_sew_efficiency_per').value = '".$row[csf("sew_effi_percent")]."';\n";
		echo "document.getElementById('txt_cut_efficiency_per').value = '".$row[csf("cut_effi_percent")]."';\n";
		echo "document.getElementById('txt_efficiency_wastage').value = '".$row[csf("efficiency_wastage_percent")]."';\n";
		echo "document.getElementById('txt_season').value = '".$row[csf("season")]."';\n";
		echo "document.getElementById('txt_m_list_no').value = '".$row[csf("m_list_no")]."';\n";
		echo "document.getElementById('txt_bh_marchant').value = '".$row[csf("bh_marchant")]."';\n";
		echo "calculate_lead_time();\n";
		
		
		$cbo_approved_status= $row[csf("approved")];
		if($cbo_approved_status==1)
		{
		echo "document.getElementById('approve1').value = 'Un-Approved';\n"; 
		//echo "$('#txt_quotation_id').attr('disabled','true')".";\n";
	    echo "$('#cbo_company_name').attr('disabled','true')".";\n";
	    echo "$('#cbo_buyer_name').attr('disabled','true')".";\n";
	    echo "$('#txt_style_ref').attr('disabled','true')".";\n";
	    echo "$('#txt_revised_no').attr('disabled','true')".";\n";
		
		echo "$('#cbo_pord_dept').attr('disabled','true')".";\n";
	    echo "$('#txt_style_desc').attr('disabled','true')".";\n";
	    echo "$('#cbo_currercy').attr('disabled','true')".";\n";
	    echo "$('#cbo_agent').attr('disabled','true')".";\n";
	    echo "$('#txt_offer_qnty').attr('disabled','true')".";\n";
		
		echo "$('#cbo_region').attr('disabled','true')".";\n";
	    echo "$('#cbo_color_range').attr('disabled','true')".";\n";
	    echo "$('#cbo_inco_term').attr('disabled','true')".";\n";
	    echo "$('#txt_incoterm_place').attr('disabled','true')".";\n";
	    echo "$('#txt_machine_line').attr('disabled','true')".";\n";
		
		echo "$('#txt_prod_line_hr').attr('disabled','true')".";\n";
	    echo "$('#cbo_costing_per').attr('disabled','true')".";\n";
	    echo "$('#txt_quotation_date').attr('disabled','true')".";\n";
	    echo "$('#txt_est_ship_date').attr('disabled','true')".";\n";
		
		echo "$('#txt_factory').attr('disabled','true')".";\n";
	    echo "$('#txt_remarks').attr('disabled','true')".";\n";
	    echo "$('#cbo_costing_per').attr('disabled','true')".";\n";
	    echo "$('#garments_nature').attr('disabled','true')".";\n";
	    echo "$('#cbo_order_uom').attr('disabled','true')".";\n";
	    echo "$('#image_button').attr('disabled','true')".";\n";
	    echo "$('#set_button').attr('disabled','true')".";\n";
	    echo "$('#save1').attr('disabled','true')".";\n";
	    echo "$('#update1').attr('disabled','true')".";\n";
	    echo "$('#Delete1').attr('disabled','true')".";\n";
		//echo "set_button_status(1, '2_2_2', 'fnc_quotation_entry',1);\n";  

		}
		else
		{
		echo "document.getElementById('approve1').value = 'Approved';\n";
		echo "$('#txt_quotation_id').removeAttr('disabled')".";\n";
	    echo "$('#cbo_company_name').removeAttr('disabled')".";\n";
	    echo "$('#cbo_buyer_name').removeAttr('disabled')".";\n";
	    echo "$('#txt_style_ref').removeAttr('disabled')".";\n";
	    echo "$('#txt_revised_no').removeAttr('disabled')".";\n";
		echo "$('#cbo_pord_dept').removeAttr('disabled')".";\n";
	    echo "$('#txt_style_desc').removeAttr('disabled')".";\n";
	    echo "$('#cbo_currercy').removeAttr('disabled')".";\n";
	    echo "$('#cbo_agent').removeAttr('disabled')".";\n";
	    echo "$('#txt_offer_qnty').removeAttr('disabled')".";\n";
		echo "$('#cbo_region').removeAttr('disabled')".";\n";
	    echo "$('#cbo_color_range').removeAttr('disabled')".";\n";
	    echo "$('#cbo_inco_term').removeAttr('disabled')".";\n";
	    echo "$('#txt_incoterm_place').removeAttr('disabled')".";\n";
	    echo "$('#txt_machine_line').removeAttr('disabled')".";\n";
		echo "$('#txt_prod_line_hr').removeAttr('disabled')".";\n";
	    echo "$('#cbo_costing_per').removeAttr('disabled')".";\n";
	    echo "$('#txt_quotation_date').removeAttr('disabled')".";\n";
	    echo "$('#txt_est_ship_date').removeAttr('disabled')".";\n";
		echo "$('#txt_factory').removeAttr('disabled')".";\n";
	    echo "$('#txt_remarks').removeAttr('disabled')".";\n";
	    echo "$('#cbo_costing_per').removeAttr('disabled')".";\n";
	    echo "$('#garments_nature').removeAttr('disabled')".";\n";
	    echo "$('#cbo_order_uom').removeAttr('disabled')".";\n";
		echo "$('#image_button').removeAttr('disabled')".";\n";
	    echo "$('#set_button').removeAttr('disabled')".";\n";
	    echo "$('#save1').removeAttr('disabled')".";\n";
	    echo "$('#update1').removeAttr('disabled')".";\n";
	    echo "$('#Delete1').removeAttr('disabled')".";\n";
		//echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_quotation_entry',1);\n";  

		}

	}
	//currier_pre_cost 	currier_percent 	certificate_pre_cost 	certificate_percent
	$data_array=sql_select("select id, quotation_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent,wash_cost,wash_cost_percent, comm_cost, comm_cost_percent, lab_test,lab_test_percent,inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent,currier_pre_cost,currier_percent,	certificate_pre_cost,certificate_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, commission,commission_percent, final_cost_dzn, final_cost_dzn_percent, final_cost_pcs,final_cost_set_pcs_rate, a1st_quoted_price,a1st_quoted_price_percent,a1st_quoted_price_date, revised_price,revised_price_date, confirm_price,confirm_price_set_pcs_rate,confirm_price_dzn, 	confirm_price_dzn_percent,  margin_dzn, margin_dzn_percent,price_with_commn_dzn,price_with_commn_percent_dzn,price_with_commn_pcs,price_with_commn_percent_pcs,confirm_date,asking_quoted_price,asking_quoted_price_percent, terget_qty,inserted_by, insert_date, updated_by,update_date,status_active,is_deleted from wo_price_quotation_costing_mst where quotation_id='$data' and status_active=1 and is_deleted=0");
	foreach ($data_array as $row)
	{
				
		echo "reset_form('quotationdtls_2','','');\n";  
		echo "document.getElementById('txt_fabric_pre_cost').value = '".$row[csf("fabric_cost")]."';\n";  
		echo "document.getElementById('txt_fabric_po_price').value = '".$row[csf("fabric_cost_percent")]."';\n";  
		echo "document.getElementById('txt_trim_pre_cost').value = '".$row[csf("trims_cost")]."';\n";  
		echo "document.getElementById('txt_trim_po_price').value = '".$row[csf("trims_cost_percent")]."';\n";  
		echo "document.getElementById('txt_embel_pre_cost').value = '".$row[csf("embel_cost")]."';\n";  
		echo "document.getElementById('txt_embel_po_price').value = '".$row[csf("embel_cost_percent")]."';\n"; 
		echo "document.getElementById('txt_wash_pre_cost').value = '".$row[csf("wash_cost")]."';\n";  
		echo "document.getElementById('txt_wash_po_price').value = '".$row[csf("wash_cost_percent")]."';\n";
		echo "document.getElementById('txt_comml_pre_cost').value = '".$row[csf("comm_cost")]."';\n";  
		echo "document.getElementById('txt_comml_po_price').value = '".$row[csf("comm_cost_percent")]."';\n"; 
		echo "document.getElementById('txt_lab_test_pre_cost').value = '".$row[csf("lab_test")]."';\n";  
		echo "document.getElementById('txt_lab_test_po_price').value = '".$row[csf("lab_test_percent")]."';\n";  
		echo "document.getElementById('txt_inspection_pre_cost').value = '".$row[csf("inspection")]."';\n";  
		echo "document.getElementById('txt_inspection_po_price').value = '".$row[csf("inspection_percent")]."';\n";  
		echo "document.getElementById('txt_cm_pre_cost').value = '".$row[csf("cm_cost")]."';\n";  
		echo "document.getElementById('txt_cm_po_price').value = '".$row[csf("cm_cost_percent")]."';\n";  
		echo "document.getElementById('txt_freight_pre_cost').value = '".$row[csf("freight")]."';\n";  
		echo "document.getElementById('txt_freight_po_price').value = '".$row[csf("freight_percent")]."';\n"; 
		
		echo "document.getElementById('txt_currier_pre_cost').value = '".$row[csf("currier_pre_cost")]."';\n";  
		echo "document.getElementById('txt_currier_po_price').value = '".$row[csf("currier_percent")]."';\n";  
		echo "document.getElementById('txt_certificate_pre_cost').value = '".$row[csf("certificate_pre_cost")]."';\n";  
		echo "document.getElementById('txt_certificate_po_price').value = '".$row[csf("certificate_percent")]."';\n";  
		
		echo "document.getElementById('txt_common_oh_pre_cost').value = '".$row[csf("common_oh")]."';\n";  
		echo "document.getElementById('txt_common_oh_po_price').value = '".$row[csf("common_oh_percent")]."';\n";  
		echo "document.getElementById('txt_total_pre_cost').value = '".$row[csf("total_cost")]."';\n";  
		echo "document.getElementById('txt_total_po_price').value = '".$row[csf("total_cost_percent")]."';\n"; 
		echo "document.getElementById('txt_commission_pre_cost').value = '".$row[csf("commission")]."';\n";  
		echo "document.getElementById('txt_commission_po_price').value = '".$row[csf("commission_percent")]."';\n"; 
		echo "document.getElementById('txt_final_cost_dzn_pre_cost').value = '".$row[csf("final_cost_dzn")]."';\n";  
		echo "document.getElementById('txt_final_cost_dzn_po_price').value = '".$row[csf("final_cost_dzn_percent")]."';\n";  
		echo "document.getElementById('txt_final_cost_pcs_po_price').value = '".$row[csf("final_cost_pcs")]."';\n"; 
		echo "document.getElementById('txt_final_cost_set_pcs_rate').value = '".$row[csf("final_cost_set_pcs_rate")]."';\n"; 
		echo "document.getElementById('txt_1st_quoted_price_pre_cost').value = '".$row[csf("a1st_quoted_price")]."';\n"; 
		echo "document.getElementById('txt_1st_quoted_po_price').value = '".$row[csf("a1st_quoted_price_percent")]."';\n"; 
		echo "document.getElementById('txt_first_quoted_price_date').value = '".change_date_format($row[csf("a1st_quoted_price_date")],'dd-mm-yyyy','-')."';\n";  
		echo "document.getElementById('txt_revised_price_pre_cost').value = '".$row[csf("revised_price")]."';\n";  
		echo "document.getElementById('txt_revised_price_date').value = '".change_date_format($row[csf("revised_price_date")],'dd-mm-yyyy','-')."';\n";  
		echo "document.getElementById('txt_confirm_price_pre_cost').value = '".$row[csf("confirm_price")]."';\n";
		echo "document.getElementById('txt_confirm_price_set_pcs_rate').value = '".$row[csf("confirm_price_set_pcs_rate")]."';\n";
		echo "document.getElementById('txt_confirm_price_pre_cost_dzn').value = '".$row[csf("confirm_price_dzn")]."';\n";
		echo "document.getElementById('txt_confirm_price_po_price_dzn').value = '".$row[csf("confirm_price_dzn_percent")]."';\n";
		
		echo "document.getElementById('txt_cost_dzn').value = '".$row[csf("total_cost")]."';\n";  
		echo "document.getElementById('txt_cost_dzn_po_price').value = '".$row[csf("total_cost_percent")]."';\n";
		
		echo "document.getElementById('txt_margin_dzn_pre_cost').value = '".$row[csf("margin_dzn")]."';\n";  
		echo "document.getElementById('txt_margin_dzn_po_price').value = '".$row[csf("margin_dzn_percent")]."';\n";  
		
		echo "document.getElementById('txt_with_commission_pre_cost_dzn').value = '".$row[csf("price_with_commn_dzn")]."';\n";		
		echo "document.getElementById('txt_with_commission_po_price_dzn').value = '".$row[csf("price_with_commn_percent_dzn")]."';\n";
		echo "document.getElementById('txt_with_commission_pre_cost_pcs').value = '".$row[csf("price_with_commn_pcs")]."';\n";
		echo "document.getElementById('txt_with_commission_po_price_pcs').value = '".$row[csf("price_with_commn_percent_pcs")]."';\n";
		echo "document.getElementById('txt_confirm_date_pre_cost').value = '".change_date_format($row[csf("confirm_date")],'dd-mm-yyyy','-')."';\n"; 
		
		//echo "document.getElementById('txt_asking_profit_from_lib').value = '".$row[csf("asking_profit_from_lib")]."';\n";
		echo "document.getElementById('txt_asking_quoted_price').value = '".$row[csf("asking_quoted_price")]."';\n"; 
		echo "document.getElementById('txt_asking_quoted_po_price').value = '".$row[csf("asking_quoted_price_percent")]."';\n"; 
		echo "document.getElementById('txt_terget_qty').value = '".$row[csf("terget_qty")]."';\n"; 
		echo "document.getElementById('update_id_dtls').value = '".$row[csf("id")]."';\n";  
		if($cbo_approved_status==1)
		{
		echo "$('#txt_lab_test_pre_cost').attr('disabled','true')".";\n";
	    echo "$('#txt_inspection_pre_cost').attr('disabled','true')".";\n";
	    echo "$('#txt_cm_pre_cost').attr('disabled','true')".";\n";
		echo "$('#txt_freight_pre_cost').attr('disabled','true')".";\n";
	    echo "$('#txt_common_oh_pre_cost').attr('disabled','true')".";\n";
	    echo "$('#txt_1st_quoted_price_pre_cost').attr('disabled','true')".";\n";
		echo "$('#txt_first_quoted_price_date').attr('disabled','true')".";\n";
	    echo "$('#txt_revised_price_pre_cost').attr('disabled','true')".";\n";
	    echo "$('#txt_revised_price_date').attr('disabled','true')".";\n";
		echo "$('#txt_confirm_price_pre_cost').attr('disabled','true')".";\n";
	    echo "$('#txt_confirm_date_pre_cost').attr('disabled','true')".";\n";
	    echo "$('#save2').attr('disabled','true')".";\n";
	    echo "$('#update2').attr('disabled','true')".";\n";
	    echo "$('#Delete2').attr('disabled','true')".";\n";
		}
		else
		{
		echo "$('#txt_lab_test_pre_cost').removeAttr('disabled')".";\n";
	    echo "$('#txt_inspection_pre_cost').removeAttr('disabled')".";\n";
	    //echo "$('#txt_cm_pre_cost').removeAttr('disabled')".";\n";
		echo "$('#txt_freight_pre_cost').removeAttr('disabled')".";\n";
	    echo "$('#txt_common_oh_pre_cost').removeAttr('disabled')".";\n";
	    echo "$('#txt_1st_quoted_price_pre_cost').removeAttr('disabled')".";\n";
		echo "$('#txt_first_quoted_price_date').removeAttr('disabled')".";\n";
	    echo "$('#txt_revised_price_pre_cost').removeAttr('disabled')".";\n";
	    echo "$('#txt_revised_price_date').removeAttr('disabled')".";\n";
		echo "$('#txt_confirm_price_pre_cost').removeAttr('disabled')".";\n";
	    echo "$('#txt_confirm_date_pre_cost').removeAttr('disabled')".";\n";
	    echo "$('#save2').removeAttr('disabled')".";\n";
	    echo "$('#update2').removeAttr('disabled')".";\n";
	    echo "$('#Delete2').removeAttr('disabled')".";\n";
		}
	}
}

if($action=="open_set_list_view")
{
echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode,'','');
extract($_REQUEST);

?>
<script>
function add_break_down_set_tr( i )
{
	var unit_id= document.getElementById('unit_id').value;
	if(unit_id==1)
	{
		alert('Only One Item');
		return false;	
	}
	var row_num=$('#tbl_set_details tr').length-1;
	if (row_num!=i)
	{
		return false;
	}
	
	if (form_validation('cboitem_'+i+'*txtsetitemratio_'+i,'Gmts Items*Set Ratio')==false)
	{
		return;
	}
	else
	{
		i++;
	 
		 $("#tbl_set_details tr:last").clone().find("input,select,a").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }              
			});
		  }).end().appendTo("#tbl_set_details");
		  
		  $('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_set_tr("+i+")");
		  $('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_delete_down_tr("+i+",'tbl_set_details')");
		  $('#cboitem_'+i).val(''); 
		  set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );

	}
		 
		  
}


function fn_delete_down_tr(rowNo,table_id) 
{   
	
	if(table_id=='tbl_set_details')
	{
		var numRow = $('table#tbl_set_details tbody tr').length; 
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_set_details tbody tr:last').remove();
		}
		/*else
		{
																																																																																																																																																																																																																																																																																																																																									reset_form('','','txtordernumber_'+rowNo+'*txtorderqnty_'+rowNo+'*txtordervalue_'+rowNo+'*txtattachedqnty_'+rowNo+'*txtattachedvalue_'+rowNo+'*txtstyleref_'+rowNo+'*txtitemname_'+rowNo+'*txtjobno_'+rowNo+'*hiddenwopobreakdownid_'+rowNo+'*hiddenunitprice_'+rowNo+'*totalOrderqnty*totalOrdervalue*totalAttachedqnty*totalAttachedvalue');
		} */
		 //set_all_onclick();
		 set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
		  //set_sum_value( 'cons_sum', 'cons_'  );
		  //set_sum_value( 'processloss_sum', 'processloss_'  );
		  //set_sum_value( 'requirement_sum', 'requirement_');
          //set_sum_value( 'pcs_sum', 'pcs_');
	}
	
	
}


function set_sum_value_set(des_fil_id,field_id)
{
	var rowCount = $('#tbl_set_details tr').length-1;
	math_operation( des_fil_id, field_id, '+', rowCount );
}

function js_set_value_set()
{
	var rowCount = $('#tbl_set_details tr').length-1;
	var set_breck_down="";
	var item_id=""
	for(var i=1; i<=rowCount; i++)
	{
		if (form_validation('cboitem_'+i+'*txtsetitemratio_'+i,'Gmts Items*Set Ratio')==false)
		{
			return;
		}
			
		
		if(set_breck_down=="")
		{
			set_breck_down+=$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val();
			item_id+=$('#cboitem_'+i).val();
		  
		}
		else
		{
			set_breck_down+="__"+$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val();
			item_id+=","+$('#cboitem_'+i).val();

		}
		
	}
	document.getElementById('set_breck_down').value=set_breck_down;
	document.getElementById('item_id').value=item_id;

	parent.emailwindow.hide();
}
</script>
</head>
<body>
       <div id="set_details"  align="center">   
    	<fieldset>
        	<form id="setdetails_1" autocomplete="off">
            <input type="hidden" id="set_breck_down" />     
            <input type="hidden" id="item_id" /> 
            <input type="hidden" id="unit_id" value="<? echo $unit_id;  ?>" />        	
       	
            <table width="800" cellspacing="0" class="rpt_table" border="0" id="tbl_set_details" rules="all">
                	<thead>
                    	<tr>
                        	<th width="250">Item</th><th  width="200">Set Item Ratio</th><th width=""></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					
					$data_array=explode("__",$set_breck_down);
					if($data_array[0]=="")
					{
						$data_array=array();
					}
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							$data=explode('_',$row);
							?>
                            	<tr id="settr_1" align="center">
                                    <td>
									<? 
									echo create_drop_down( "cboitem_".$i, 250, $garments_item, "",1," -- Select Item --", $data[0], "",'','' ); 
									?>
                                    
                                    </td>
                                    <td>
                                    <input type="text" id="txtsetitemratio_<? echo $i;?>"   name="txtsetitemratio_<? echo $i;?>" style="width:190px"  class="text_boxes_numeric" onChange="set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' )"  value="<? echo $data[1] ?>" <? if ($unit_id==1){echo "readonly";} else{echo "";}?> /> 
                                    </td>
                                   
                                  
                                    <td>
                                    <input type="button" id="increaseset_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decreaseset_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(<? echo $i; ?> ,'tbl_set_details' );" />
                                     </td> 
                                </tr>
                            
                            <?
							 
						}
					}
					else
					{
						
					?>
                    <tr id="settr_1" align="center">
                                   <td>
									<? 
									echo create_drop_down( "cboitem_1", 240, $garments_item, "",1,"--Select--", 0, '','','' ); 
									?>
                                    </td>
                                     <td>
                                    <input type="text" id="txtsetitemratio_1" name="txtsetitemratio_1" style="width:190px" class="text_boxes_numeric" onChange="set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' )" value="<? if ($unit_id==1) {echo "1";} else{echo "";}?>"  <? if ($unit_id==1){echo "readonly";} else{echo "";}?>  /> 
                                     </td>
                                   
                                    <td>
                                    <input type="button" id="increaseset_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(1)" />
                                    <input type="button" id="decreaseset_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(1 ,'tbl_set_details' );" />
                                    </td> 
                                </tr>
                    <? 
					
					} 
					?>
                </tbody>
                </table>
                <table width="800" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                            <th width="250">Total</th>
                            <th  width="200"><input type="text" id="tot_set_qnty" name="tot_set_qnty"  class="text_boxes_numeric" style="width:190px"  value="<? if($tot_set_qnty !=''){ echo $tot_set_qnty;} else{ echo 1;} ?>" readonly  /></th>
                            <th width=""></th>
                        </tr>
                    </tfoot>
                </table>
                           
                <table width="800" cellspacing="0" class="" border="0">
                
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
                        
						        <input type="button" class="formbutton" value="Close" onClick="js_set_value_set()"/> 

                        </td> 
                    </tr>
                </table>
               
            </form>
        </fieldset>
        </div>
 </body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

<?
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		//if (is_duplicate_field( "company_name", "lib_company", "company_name=$txt_company_name and is_deleted=0" ) == 1)
		//{
			//echo "11**0"; die;
		//}
		//else
		//{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			$id=return_next_id( "id", "wo_price_quotation", 1 ) ;
			$field_array="id,inquery_id, company_id, buyer_id, style_ref, revised_no, pord_dept,product_code, style_desc, currency, agent, offer_qnty, region, color_range, incoterm, incoterm_place, machine_line, prod_line_hr,  costing_per, quot_date, est_ship_date,op_date, factory, remarks, garments_nature,order_uom,gmts_item_id,set_break_down,total_set_qnty,cm_cost_predefined_method_id,exchange_rate,sew_smv,cut_smv,sew_effi_percent,cut_effi_percent,efficiency_wastage_percent,season, inserted_by, insert_date, status_active, is_deleted";
			$data_array="(".$id.",".$txt_inquery_id.",".$cbo_company_name.",".$cbo_buyer_name.",".$txt_style_ref.",".$txt_revised_no.",".$cbo_pord_dept.",".$txt_product_code.",".$txt_style_desc.",".$cbo_currercy.",".$cbo_agent.",".$txt_offer_qnty.",".$cbo_region.",".$cbo_color_range.",".$cbo_inco_term.",".$txt_incoterm_place.",".$txt_machine_line.",".$txt_prod_line_hr.",".$cbo_costing_per.",".$txt_quotation_date.",".$txt_est_ship_date.",".$txt_op_date.",".$txt_factory.",".$txt_remarks.",".$garments_nature.",".$cbo_order_uom.",".$item_id.",".$set_breck_down.",".$tot_set_qnty.",".$cm_cost_predefined_method_id.",".$txt_exchange_rate.",".$txt_sew_smv.",".$txt_cut_smv.",".$txt_sew_efficiency_per.",".$txt_cut_efficiency_per.",".$txt_efficiency_wastage.",".$txt_season.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
			
			//if(str_replace("'","",$cbo_order_uom)==58)
			//{
				$field_array1="id,quotation_id, gmts_item_id,set_item_ratio";
				$add_comma=0;
				$id1=return_next_id( "id", "  wo_price_quotation_set_details", 1 ) ;
				$set_breck_down_array=explode('__',str_replace("'",'',$set_breck_down));
				for($c=0;$c < count($set_breck_down_array);$c++)
				{
					$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
					if ($add_comma!=0) $data_array1 .=",";
					$data_array1 .="(".$id1.",".$id.",'".$set_breck_down_arr[0]."','".$set_breck_down_arr[1]."')";
					$add_comma++;
					$id1=$id1+1;
				}

			//}
			$rID=sql_insert("wo_price_quotation",$field_array,$data_array,0);
			$rID1=sql_insert("wo_price_quotation_set_details",$field_array1,$data_array1,1);


			

			if($db_type==0)
			{
			if(str_replace("'","",$cbo_order_uom)==58 || str_replace("'","",$cbo_order_uom)==57 )
			{
				if($rID==1 && $rID1==1){
					mysql_query("COMMIT");  
					echo "0**".$rID."**".$id;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID."**".$id;
				}
			}
			if(str_replace("'","",$cbo_order_uom)==1)
			{
				if($rID==1){
					mysql_query("COMMIT");  
					echo "0**".$rID."**".$id;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID."**".$id;
				}
			}
			}
			
			if($db_type==2 || $db_type==1 )
			{
				echo '0**0'."**".$id;
			}
			disconnect($con);
			die;
		//}
	}
	
	else if ($operation==1)   // Update Here
	{
		//if (is_duplicate_field( "company_name", "lib_company", "company_name=$txt_company_name and id!=$update_id and is_deleted=0" ) == 1)
		//{
			//echo "11**0"; die;
		//}
		//else
		//{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			 	
			$field_array="inquery_id*company_id*buyer_id*style_ref*revised_no*pord_dept*product_code*style_desc*currency*agent*offer_qnty*region*color_range*incoterm*incoterm_place*machine_line* prod_line_hr*costing_per*quot_date*est_ship_date*op_date*factory*remarks*garments_nature*order_uom*gmts_item_id*set_break_down*total_set_qnty*cm_cost_predefined_method_id*exchange_rate*sew_smv*cut_smv*sew_effi_percent*cut_effi_percent*efficiency_wastage_percent*season*updated_by*update_date*status_active* is_deleted";
			$data_array="".$txt_inquery_id."*".$cbo_company_name."*".$cbo_buyer_name."*".$txt_style_ref."*".$txt_revised_no."*".$cbo_pord_dept."*".$txt_product_code."*".$txt_style_desc."*".$cbo_currercy."*".$cbo_agent."*".$txt_offer_qnty."*".$cbo_region."*".$cbo_color_range."*".$cbo_inco_term."*".$txt_incoterm_place."*".$txt_machine_line."*".$txt_prod_line_hr."*".$cbo_costing_per."*".$txt_quotation_date."*".$txt_est_ship_date."*".$txt_op_date."*".$txt_factory."*".$txt_remarks."*".$garments_nature."*".$cbo_order_uom."*".$item_id."*".$set_breck_down."*".$tot_set_qnty."*".$cm_cost_predefined_method_id."*".$txt_exchange_rate."*".$txt_sew_smv."*".$txt_cut_smv."*".$txt_sew_efficiency_per."*".$txt_cut_efficiency_per."*".$txt_efficiency_wastage."*".$txt_season."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'1'*'0'";
			
			//if(str_replace("'","",$cbo_order_uom)==58)
			//{
				$field_array1="id,quotation_id, gmts_item_id,set_item_ratio";
				$add_comma=0;
				$id1=return_next_id( "id", "  wo_price_quotation_set_details", 1 ) ;
				$set_breck_down_array=explode('__',str_replace("'",'',$set_breck_down));
				for($c=0;$c < count($set_breck_down_array);$c++)
				{
					$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
					if ($add_comma!=0) $data_array1 .=",";
					$data_array1 .="(".$id1.",".$update_id.",'".$set_breck_down_arr[0]."','".$set_breck_down_arr[1]."')";
					$add_comma++;
					$id1=$id1+1;
				}
				
			// }
			$rID=sql_update("wo_price_quotation",$field_array,$data_array,"id","".$update_id."",0);
			$rID1=execute_query( "delete from wo_price_quotation_set_details where  quotation_id =".$update_id."",0);
			$rID2=sql_insert("wo_price_quotation_set_details",$field_array1,$data_array1,1);

			
			if($db_type==0)
			{
			if(str_replace("'","",$cbo_order_uom)==58 || str_replace("'","",$cbo_order_uom)==57)
			{
				if($rID==1 && $rID1==1 && $rID2==1  ){
					mysql_query("COMMIT");  
					echo "1**".$rID."**".str_replace("'","",$update_id);
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID."**".str_replace("'","",$update_id);
				}
			}
			
			if(str_replace("'","",$cbo_order_uom)==1)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "1**".$rID."**".str_replace("'","",$update_id);
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID."**".str_replace("'","",$update_id);
				}
			}
			}
			if($db_type==2 || $db_type==1 )
			{
			echo "1**".$rID."**".str_replace("'","",$update_id);
			}
			disconnect($con);
			die;
		//}
		
	}
	
	else if ($operation==2)   // Update Here
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$rID=execute_query( "delete from wo_price_quotation where  id =".$update_id."",0);
			$rID=execute_query( "delete from wo_price_quotation_costing_mst where  quotation_id =".$update_id."",0);
			$rID=execute_query( "delete from wo_price_quotation_set_details where  quotation_id =".$update_id."",0);
			$rID=execute_query( "delete from wo_pri_quo_comarcial_cost_dtls where  quotation_id =".$update_id."",0);
			
			$rID=execute_query( "delete from wo_pri_quo_commiss_cost_dtls where quotation_id =".$update_id."",0);

			$rID=execute_query( "delete from wo_pri_quo_embe_cost_dtls where quotation_id =".$update_id."",0);

			$rID=execute_query( "delete from wo_pri_quo_fabric_cost_dtls where quotation_id =".$update_id."",0);
			
			$rID=execute_query( "delete from wo_pri_quo_fab_conv_cost_dtls where quotation_id =".$update_id."",0);
			
			$rID=execute_query( "delete from wo_pri_quo_fab_co_avg_con_dtls where quotation_id =".$update_id."",0);
			
			$rID=execute_query( "delete from wo_pri_quo_fab_yarn_cost_dtls where quotation_id =".$update_id."",0);
			
			$rID=execute_query( "delete from wo_pri_quo_sum_dtls where quotation_id =".$update_id."",0);
			
			$rID=execute_query( "delete from wo_pri_quo_trim_cost_dtls where quotation_id =".$update_id."",0);
			
			//$field_array="updated_by*update_date*status_active*is_deleted";
			//$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			//$rID=sql_delete("wo_price_quotation",$field_array,$data_array,"id","".$update_id."",1);
			
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "2**".$rID."**".str_replace("'","",$update_id);
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID."**".str_replace("'","",$update_id);
				}
			}
			if($db_type==2 || $db_type==1 )

			{
				echo "2**".$rID."**".str_replace("'","",$update_id);
			}
			disconnect($con);
			die;
		
	}
	
	
	else if ($operation==3)   // Update Here
	{
		/*if (is_duplicate_field( "b.tag_company", "lib_buyer a, lib_buyer_tag_company b", "a.id=b.buyer_id and b.tag_company=$update_id and a.is_deleted=0" ) == 1)
		{
			echo "13**0"; die;
		}
		if (is_duplicate_field( "b.tag_company", "lib_supplier a, lib_supplier_tag_company b", "a.id=b.supplier_id and b.tag_company=$update_id and a.is_deleted=0" ) == 1)
		{
			echo "13**0"; die;
		}
		if (is_duplicate_field( "company_id", "lib_location", "company_id=$update_id and is_deleted=0" ) == 1)
		{
			echo "13**0"; die;
		}
		if (is_duplicate_field( "company_id", "lib_profit_center", "company_id=$update_id and is_deleted=0" ) == 1)
		{
			echo "13**0"; die;
		}
		if (is_duplicate_field( "company_id", "lib_prod_floor", "company_id=$update_id and is_deleted=0" ) == 1)
		{
			echo "13**0"; die;
		}  
		if (is_duplicate_field( "company_id", "lib_standard_cm_entry", "company_id=$update_id and is_deleted=0" ) == 1)
		{
			echo "13**0"; die;
		}*/
		
		//else
		//{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			$field_array="approved*approved_by*approved_date";
			if(trim(str_replace("'","",$cbo_approved_status))==2) 
			{
				$data_array="'1'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
			}
			else 
			{
				$data_array="'0'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
				
			}						
		    $rID=sql_update("wo_price_quotation",$field_array,$data_array,"id",$update_id,1); 
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "2**".$rID."**".str_replace("'","",$update_id)."**".trim(str_replace("'","",$cbo_approved_status));
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID."**".str_replace("'","",$update_id)."**".trim(str_replace("'","",$cbo_approved_status));
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				echo "2**".$rID."**".str_replace("'","",$update_id)."**".trim(str_replace("'","",$cbo_approved_status));
			}
			disconnect($con);
			die;
		//}
	}
}

if ($action=="copy_quatation")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==5)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$id=return_next_id( "id", "wo_price_quotation", 1 ) ;
		$field_array="id, company_id, buyer_id, style_ref, revised_no, pord_dept,product_code, style_desc, currency, agent, offer_qnty, region, color_range, incoterm, incoterm_place, machine_line, prod_line_hr,  costing_per, quot_date, est_ship_date, factory, remarks, garments_nature,order_uom,gmts_item_id,set_break_down,total_set_qnty,cm_cost_predefined_method_id,exchange_rate,sew_smv,cut_smv,sew_effi_percent,cut_effi_percent,efficiency_wastage_percent,season, inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$id.",".$cbo_company_name.",".$cbo_buyer_name.",".$txt_style_ref.",".$txt_revised_no.",".$cbo_pord_dept.",".$txt_product_code.",".$txt_style_desc.",".$cbo_currercy.",".$cbo_agent.",".$txt_offer_qnty.",".$cbo_region.",".$cbo_color_range.",".$cbo_inco_term.",".$txt_incoterm_place.",".$txt_machine_line.",".$txt_prod_line_hr.",".$cbo_costing_per.",".$txt_quotation_date.",".$txt_est_ship_date.",".$txt_factory.",".$txt_remarks.",".$garments_nature.",".$cbo_order_uom.",".$item_id.",".$set_breck_down.",".$tot_set_qnty.",".$cm_cost_predefined_method_id.",".$txt_exchange_rate.",".$txt_sew_smv.",".$txt_cut_smv.",".$txt_sew_efficiency_per.",".$txt_cut_efficiency_per.",".$txt_efficiency_wastage.",".$txt_season.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
		$field_array1="id,quotation_id, gmts_item_id,set_item_ratio";
		$add_comma=0;
		$id1=return_next_id( "id", "  wo_price_quotation_set_details", 1 ) ;
		$set_breck_down_array=explode('__',str_replace("'",'',$set_breck_down));
		for($c=0;$c < count($set_breck_down_array);$c++)
		{
			$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1 .="(".$id1.",".$id.",'".$set_breck_down_arr[0]."','".$set_breck_down_arr[1]."')";
			$add_comma++;
			$id1=$id1+1;
		}
		$rID=sql_insert("wo_price_quotation",$field_array,$data_array,0);
		$rID1=sql_insert("wo_price_quotation_set_details",$field_array1,$data_array1,1);
		
		$id_costing_mst=save_fabric_cost($id,$txt_quotation_id);
		
		check_table_status( $_SESSION['menu_id'],0);
		
		if($db_type==0)
		{
			if(str_replace("'","",$cbo_order_uom)==58 || str_replace("'","",$cbo_order_uom)==57 )
			{
				if($rID==1 && $rID1==1){
					mysql_query("COMMIT");  
					echo "0**".$rID."**".$id."**".$id_costing_mst;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID."**".$id."**".$id_costing_mst;
				}
			}
			if(str_replace("'","",$cbo_order_uom)==1)
			{
				if($rID==1){
					mysql_query("COMMIT");  
					echo "0**".$rID."**".$id."**".$id_costing_mst;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID."**".$id."**".$id_costing_mst;
				}
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			echo '0**0'."**".$id;
		}
		disconnect($con);
		die;
	}
}

function save_fabric_cost($newid,$txt_quotation_id_old)
{
	    global $pc_date_time;
		$conversion_cost_headarr=array();
		$id=return_next_id( "id", "wo_pri_quo_fabric_cost_dtls", 1 ) ;
		$id1=return_next_id( "id", "wo_pri_quo_fab_co_avg_con_dtls", 1 ) ;
		$field_array="id, quotation_id, item_number_id, body_part_id, fab_nature_id, color_type_id,lib_yarn_count_deter_id,	construction, composition,fabric_description, gsm_weight, avg_cons, fabric_source, rate, amount,avg_finish_cons,	avg_process_loss, inserted_by, insert_date, status_active, is_deleted, company_id, costing_per,fab_cons_in_quotat_varia,process_loss_method,cons_breack_down,msmnt_break_down,yarn_breack_down,marker_break_down,width_dia_type";
		
		$field_array1="id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin,	body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin,front_rise_length,	front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin,total,marker_dia,marker_yds,marker_inch,gmts_pcs,marker_length,net_fab_cons";
		
	   $sql_data=sql_select("Select id, item_number_id,body_part_id,fab_nature_id,color_type_id,lib_yarn_count_deter_id,construction,composition,fabric_description,gsm_weight,avg_cons,fabric_source,rate,amount,avg_finish_cons,avg_process_loss,inserted_by,insert_date,status_active,is_deleted,company_id,costing_per,fab_cons_in_quotat_varia,process_loss_method,cons_breack_down,msmnt_break_down,yarn_breack_down,marker_break_down,width_dia_type from wo_pri_quo_fabric_cost_dtls where quotation_id=$txt_quotation_id_old and status_active=1 and is_deleted=0");
	   $add_comma=0;
	   $i=1;
	foreach($sql_data as $row)
	{
		    if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",'".$newid."','".$row[csf("item_number_id")]."','".$row[csf("body_part_id")]."','".$row[csf("fab_nature_id")]."','".$row[csf("color_type_id")]."','".$row[csf("lib_yarn_count_deter_id")]."','".$row[csf("construction")]."','".$row[csf("composition")]."','".$row[csf("fabric_description")]."','".$row[csf("gsm_weight")]."','".$row[csf("avg_cons")]."','".$row[csf("fabric_source")]."','".$row[csf("rate")]."','".$row[csf("amount")]."','".$row[csf("avg_finish_cons")]."','".$row[csf("avg_process_loss")]."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."','".$row[csf("status_active")]."',0,'".$row[csf("company_id")]."','".$row[csf("costing_per")]."','".$row[csf("fab_cons_in_quotat_varia")]."','".$row[csf("process_loss_method")]."','".$row[csf("cons_breack_down")]."','".$row[csf("msmnt_break_down")]."','".$row[csf("yarn_breack_down")]."','".$row[csf("marker_break_down")]."','".$row[csf("width_dia_type")]."')";
			
			$sql_data_cons=sql_select("Select id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin,	body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin,front_rise_length,	front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin,total,marker_dia,marker_yds,marker_inch,gmts_pcs,marker_length,net_fab_cons from wo_pri_quo_fab_co_avg_con_dtls where quotation_id=$txt_quotation_id_old and wo_pri_quo_fab_co_dtls_id='".$row[csf("id")]."'");
			
			foreach($sql_data_cons as $row_cons)
	        {
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1 .="(".$id1.",".$id.",".$newid.",'".$row_cons[csf("gmts_sizes")]."','".$row_cons[csf("dia_width")]."','".$row_cons[csf("cons")]."','".$row_cons[csf("process_loss_percent")]."','".$row_cons[csf("requirment")]."','".$row_cons[csf("pcs")]."','".$row_cons[csf("body_length")]."','".$row_cons[csf("body_sewing_margin")]."','".$row_cons[csf("body_hem_margin")]."','".$row_cons[csf("sleeve_length")]."','".$row_cons[csf("sleeve_sewing_margin")]."','".$row_cons[csf("sleeve_hem_margin")]."','".$row_cons[csf("half_chest_length")]."','".$row_cons[csf("half_chest_sewing_margin")]."','".$row_cons[csf("front_rise_length")]."','".$row_cons[csf("front_rise_sewing_margin")]."','".$row_cons[csf("west_band_length")]."','".$row_cons[csf("west_band_sewing_margin")]."','".$row_cons[csf("in_seam_length")]."','".$row_cons[csf("in_seam_sewing_margin")]."','".$row_cons[csf("in_seam_hem_margin")]."','".$row_cons[csf("half_thai_length")]."','".$row_cons[csf("half_thai_sewing_margin")]."','".$row_cons[csf("total")]."','".$row_cons[csf("marker_dia")]."','".$row_cons[csf("marker_yds")]."','".$row_cons[csf("marker_inch")]."','".$row_cons[csf("gmts_pcs")]."','".$row_cons[csf("marker_length")]."','".$row_cons[csf("net_fab_cons")]."')";
			$id1=$id1+1;
			$add_comma++;
	        }
			$conversion_cost_headarr[$row[csf("id")]]=$id;
			$id=$id+1;
			$i++;
	}
	    $rID1=sql_insert("wo_pri_quo_fabric_cost_dtls",$field_array,$data_array,0);
		$rID=sql_insert("wo_pri_quo_fab_co_avg_con_dtls",$field_array1,$data_array1,1);
		
		//---Yarn Cost--------------
		 $iy=1;
		 $id_yarn=return_next_id( "id", "wo_pri_quo_fab_yarn_cost_dtls", 1 ) ;
		 $field_array_yarn="id,quotation_id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, status_active, is_deleted";
		 $sql_data_yarn=sql_select("Select id,quotation_id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, status_active, is_deleted from wo_pri_quo_fab_yarn_cost_dtls where quotation_id=$txt_quotation_id_old and status_active=1 and  is_deleted =0");
		 foreach($sql_data_yarn as $row_yarn)
	     {
			if ($iy!=1) $data_array_yarn .=",";
			$data_array_yarn .="(".$id_yarn.",".$newid.",'".$row_yarn[csf("count_id")]."','".$row_yarn[csf("copm_one_id")]."','".$row_yarn[csf("percent_one")]."','".$row_yarn[csf("copm_two_id")]."','".$row_yarn[csf("percent_two")]."','".$row_yarn[csf("type_id")]."','".$row_yarn[csf("cons_ratio")]."','".$row_yarn[csf("cons_qnty")]."','".$row_yarn[csf("rate")]."','".$row_yarn[csf("amount")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$row_yarn[csf("status_active")]."',0)";
			$id_yarn=$id_yarn+1;
			$iy++;
		 }
		 $rID=sql_insert("wo_pri_quo_fab_yarn_cost_dtls",$field_array_yarn,$data_array_yarn,0);
		 //---Yarn Cost End --------------
		 
		 //---Conversion Cost--------------
		 $ifc=1;
		 $idfc=return_next_id( "id", "wo_pri_quo_fab_conv_cost_dtls", 1 ) ;
		 $field_array_fc="id,quotation_id,cost_head,cons_type,req_qnty,charge_unit,amount,charge_lib_id,inserted_by,insert_date,status_active,is_deleted";
		 $sql_data_con=sql_select("Select id,quotation_id,cost_head,cons_type,req_qnty,charge_unit,amount,charge_lib_id,inserted_by,insert_date,status_active,is_deleted from wo_pri_quo_fab_conv_cost_dtls where quotation_id=$txt_quotation_id_old and status_active=1 and  is_deleted =0");
		 foreach($sql_data_con as $row_con)
	     {
			if ($ifc!=1) $data_array_fc .=",";
			$data_array_fc .="(".$idfc.",".$newid.",'".$conversion_cost_headarr[$row_con[csf("cost_head")]]."','".$row_con[csf("cons_type")]."','".$row_con[csf("req_qnty")]."','".$row_con[csf("charge_unit")]."','".$row_con[csf("amount")]."','".$row_con[csf("charge_lib_id")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$row_con[csf("status_active")]."',0)";
			$idfc=$idfc+1;
			$ifc++;
		 }
		 $rID=sql_insert("wo_pri_quo_fab_conv_cost_dtls",$field_array_fc,$data_array_fc,0);
		 //---Conversion Cost End --------------
		 
		 //---Trim Cost--------------
		 $it=1;
		 $idt=return_next_id( "id", "wo_pri_quo_trim_cost_dtls", 1 ) ;
		 $field_array_t="id,quotation_id, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, inserted_by, insert_date, status_active,is_deleted";
		 $sql_data_t=sql_select("Select id,quotation_id, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, inserted_by, insert_date, status_active,is_deleted from wo_pri_quo_trim_cost_dtls where quotation_id=$txt_quotation_id_old and status_active=1 and  is_deleted =0");
		 foreach($sql_data_t as $row_t)
	     {
			if ($it!=1) $data_array_t .=",";
			$data_array_t .="(".$idt.",".$newid.",'".$row_t[csf("trim_group")]."','".$row_t[csf("cons_uom")]."','".$row_t[csf("cons_dzn_gmts")]."','".$row_t[csf("rate")]."','".$row_t[csf("amount")]."','".$row_t[csf("apvl_req")]."','".$row_t[csf("nominated_supp")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$row_t[csf("status_active")]."',0)";
			$idt=$idt+1;
			$it++;
		 }
		 $rID=sql_insert("wo_pri_quo_trim_cost_dtls",$field_array_t,$data_array_t,0);
		 //---Trim Cost End --------------
		 
		 
		 //---Embelishment And Wash Cost--------------
		 $iem=1;
		 $idem=return_next_id( "id", "wo_pri_quo_embe_cost_dtls", 1 ) ;
		 $field_array_em="id,quotation_id,emb_name,emb_type,cons_dzn_gmts,rate,amount,charge_lib_id,inserted_by,insert_date,status_active,is_deleted";
		 
		 $sql_data_em=sql_select("Select id,quotation_id,emb_name,emb_type,cons_dzn_gmts,rate,amount,charge_lib_id,inserted_by,insert_date,status_active,is_deleted from wo_pri_quo_embe_cost_dtls where quotation_id=$txt_quotation_id_old  and status_active=1 and  is_deleted =0");
		 foreach($sql_data_em as $row_em)
	     {
			if ($iem!=1) $data_array_em .=",";
			$data_array_em .="(".$idem.",".$newid.",'".$row_em[csf("emb_name")]."','".$row_em[csf("emb_type")]."','".$row_em[csf("cons_dzn_gmts")]."','".$row_em[csf("rate")]."','".$row_em[csf("amount")]."','".$row_em[csf("charge_lib_id")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$row_em[csf("status_active")]."',0)";
			$idem=$idem+1;
			$iem++;
		 }
		 $rID=sql_insert("wo_pri_quo_embe_cost_dtls",$field_array_em,$data_array_em,0);
		 //---Embelishment And Wash Cost End --------------
		 
		 //---Commercial Cost--------------
		 $icmr=1;
		 $idcmr=return_next_id( "id", "wo_pri_quo_comarcial_cost_dtls", 1 ) ;
		 $field_array_cmr="id,quotation_id,item_id,rate,amount,inserted_by,insert_date,status_active,is_deleted ";
		 
		 $sql_data_cmr=sql_select("Select id,quotation_id,item_id,rate,amount,inserted_by,insert_date,status_active,is_deleted  from wo_pri_quo_comarcial_cost_dtls where quotation_id=$txt_quotation_id_old  and status_active=1 and  is_deleted =0");
		 foreach($sql_data_cmr as $row_cmr)
	     {
			if ($icmr!=1) $data_array_cmr .=",";
			$data_array_cmr .="(".$idcmr.",".$newid.",'".$row_cmr[csf("item_id")]."','".$row_cmr[csf("rate")]."','".$row_cmr[csf("amount")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$row_cmr[csf("status_active")]."',0)";
			$idcmr=$idcmr+1;
			$icmr++;
		 }
		 $rID=sql_insert("wo_pri_quo_comarcial_cost_dtls",$field_array_cmr,$data_array_cmr,0);
		 //---Commercial Cost End --------------
		 
		 //---Commision Cost--------------
		 $icms=1;
		 $idcms=return_next_id( "id", "wo_pri_quo_commiss_cost_dtls", 1 ) ;
		 $field_array_cms="id,quotation_id,particulars_id,commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,status_active,is_deleted ";
		 
		 $sql_data_cms=sql_select("Select id,quotation_id,particulars_id,commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,status_active,is_deleted   from wo_pri_quo_commiss_cost_dtls where quotation_id=$txt_quotation_id_old  and status_active=1 and  is_deleted =0");
		 foreach($sql_data_cms as $row_cms)
	     {
			if ($icms!=1) $data_array_cms .=",";
			$data_array_cms .="(".$idcms.",".$newid.",'".$row_cms[csf("particulars_id")]."','".$row_cms[csf("commission_base_id")]."','".$row_cms[csf("commision_rate")]."','".$row_cms[csf("commission_amount")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$row_cms[csf("status_active")]."',0)";
			$idcms=$idcms+1;
			$icms++;
		 }
		 $rID=sql_insert("wo_pri_quo_commiss_cost_dtls",$field_array_cms,$data_array_cms,0);
		 //---Commision Cost End --------------
		 
		 
		 
		 //---wo_price_quotation_costing_mst Table--------------
		 $id_costing_mst=return_next_id( "id", "wo_price_quotation_costing_mst", 1 ) ;
		$field_array_costing_mst="id,quotation_id,costing_per_id,order_uom_id,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,lab_test,lab_test_percent,inspection, 	inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent,certificate_pre_cost,certificate_percent, common_oh,common_oh_percent,total_cost,total_cost_percent,commission,commission_percent,final_cost_dzn,final_cost_dzn_percent,final_cost_pcs,final_cost_set_pcs_rate,a1st_quoted_price,a1st_quoted_price_percent,a1st_quoted_price_date,revised_price,revised_price_date,confirm_price,confirm_price_set_pcs_rate,confirm_price_dzn, confirm_price_dzn_percent, margin_dzn,margin_dzn_percent,price_with_commn_dzn,price_with_commn_percent_dzn,price_with_commn_pcs,price_with_commn_percent_pcs, confirm_date,asking_quoted_price,asking_quoted_price_percent, inserted_by, insert_date, status_active, is_deleted ";
		 
		 $sql_data_costing_mst=sql_select("Select id,quotation_id,costing_per_id,order_uom_id,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,lab_test,lab_test_percent,inspection, 	inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent,certificate_pre_cost,certificate_percent,common_oh,common_oh_percent,total_cost,total_cost_percent,commission,commission_percent,final_cost_dzn,final_cost_dzn_percent,final_cost_pcs,final_cost_set_pcs_rate,a1st_quoted_price,a1st_quoted_price_percent,a1st_quoted_price_date,revised_price,revised_price_date,confirm_price,confirm_price_set_pcs_rate,confirm_price_dzn, confirm_price_dzn_percent, margin_dzn,margin_dzn_percent,price_with_commn_dzn,price_with_commn_percent_dzn,price_with_commn_pcs,price_with_commn_percent_pcs, confirm_date,asking_quoted_price,asking_quoted_price_percent, inserted_by, insert_date, status_active, is_deleted    from wo_price_quotation_costing_mst where quotation_id=$txt_quotation_id_old  and status_active=1 and  is_deleted =0");
		 foreach($sql_data_costing_mst as $row_costing_mst)
	     {
			$data_array_costing_mst="(".$id_costing_mst.",".$newid.",'".$row_costing_mst[csf("costing_per_id")]."','".$row_costing_mst[csf("order_uom_id")]."','".$row_costing_mst[csf("fabric_cost")]."','".$row_costing_mst[csf("fabric_cost_percent")]."','".$row_costing_mst[csf("trims_cost")]."','".$row_costing_mst[csf("trims_cost_percent")]."','".$row_costing_mst[csf("embel_cost")]."','".$row_costing_mst[csf("embel_cost_percent")]."','".$row_costing_mst[csf("wash_cost")]."','".$row_costing_mst[csf("wash_cost_percent")]."','".$row_costing_mst[csf("comm_cost")]."','".$row_costing_mst[csf("comm_cost_percent")]."','".$row_costing_mst[csf("lab_test")]."','".$row_costing_mst[csf("lab_test_percent")]."','".$row_costing_mst[csf("inspection")]."','".$row_costing_mst[csf("inspection_percent")]."','".$row_costing_mst[csf("cm_cost")]."','".$row_costing_mst[csf("cm_cost_percent")]."','".$row_costing_mst[csf("freight")]."','".$row_costing_mst[csf("freight_percent")]."','".$row_costing_mst[csf("currier_pre_cost")]."','".$row_costing_mst[csf("currier_percent")]."','".$row_costing_mst[csf("certificate_pre_cost")]."','".$row_costing_mst[csf("certificate_percent")]."','".$row_costing_mst[csf("common_oh")]."','".$row_costing_mst[csf("common_oh_percent")]."','".$row_costing_mst[csf("total_cost")]."','".$row_costing_mst[csf("total_cost_percent")]."','".$row_costing_mst[csf("commission")]."','".$row_costing_mst[csf("commission_percent")]."','".$row_costing_mst[csf("final_cost_dzn")]."','".$row_costing_mst[csf("final_cost_dzn_percent")]."','".$row_costing_mst[csf("final_cost_pcs")]."','".$row_costing_mst[csf("final_cost_set_pcs_rate")]."','".$row_costing_mst[csf("a1st_quoted_price")]."','".$row_costing_mst[csf("a1st_quoted_price_percent")]."','".$row_costing_mst[csf("a1st_quoted_price_date")]."','".$row_costing_mst[csf("revised_price")]."','".$row_costing_mst[csf("revised_price_date")]."','".$row_costing_mst[csf("confirm_price")]."','".$row_costing_mst[csf("confirm_price_set_pcs_rate")]."','".$row_costing_mst[csf("confirm_price_dzn")]."','".$row_costing_mst[csf("confirm_price_dzn_percent")]."','".$row_costing_mst[csf("margin_dzn")]."','".$row_costing_mst[csf("margin_dzn_percent")]."','".$row_costing_mst[csf("price_with_commn_dzn")]."','".$row_costing_mst[csf("price_with_commn_percent_dzn")]."','".$row_costing_mst[csf("price_with_commn_pcs")]."','".$row_costing_mst[csf("price_with_commn_percent_pcs")]."','".$row_costing_mst[csf("confirm_date")]."','".$row_costing_mst[csf("asking_quoted_price")]."','".$row_costing_mst[csf("asking_quoted_price_percent")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
		 }
		 $rID=sql_insert("wo_price_quotation_costing_mst",$field_array_costing_mst,$data_array_costing_mst,0);
		 //---wo_price_quotation_costing_mst Cost End --------------
		 
		 
		 
		 //---wo_pri_quo_sum_dtls Table--------------
		 $id_sum=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
		$field_array_sum="id,quotation_id,fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_amount,yarn_cons_qnty,yarn_amount,conv_req_qnty,conv_charge_unit,conv_amount, 	trim_cons,trim_rate,trim_amount,emb_amount,wash_amount,comar_rate,comar_amount,commis_rate,commis_amount,inserted_by,insert_date,status_active,is_deleted";
		 
		 $sql_data_sum=sql_select("Select id,quotation_id,fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_amount,yarn_cons_qnty,yarn_amount,conv_req_qnty,conv_charge_unit,conv_amount, 	trim_cons,trim_rate,trim_amount,emb_amount,wash_amount,comar_rate,comar_amount,commis_rate,commis_amount,inserted_by,insert_date,status_active,is_deleted   from wo_pri_quo_sum_dtls where quotation_id=$txt_quotation_id_old  and status_active=1 and  is_deleted =0");
		 foreach($sql_data_sum as $row_sum)
	     {
			$data_array_sum="(".$id_sum.",".$newid.",'".$row_sum[csf("fab_yarn_req_kg")]."','".$row_sum[csf("fab_woven_req_yds")]."','".$row_sum[csf("fab_knit_req_kg")]."','".$row_sum[csf("fab_amount")]."','".$row_sum[csf("yarn_cons_qnty")]."','".$row_sum[csf("yarn_amount")]."','".$row_sum[csf("conv_req_qnty")]."','".$row_sum[csf("conv_charge_unit")]."','".$row_sum[csf("conv_amount")]."','".$row_sum[csf("trim_cons")]."','".$row_sum[csf("trim_rate")]."','".$row_sum[csf("trim_amount")]."','".$row_sum[csf("emb_amount")]."','".$row_sum[csf("wash_amount")]."','".$row_sum[csf("comar_rate")]."','".$row_sum[csf("comar_amount")]."','".$row_sum[csf("commis_rate")]."','".$row_sum[csf("commis_amount")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
		 }
		 $rID=sql_insert("wo_pri_quo_sum_dtls",$field_array_sum,$data_array_sum,0);
		 
		 return $id_costing_mst;
		 //---wo_pri_quo_sum_dtls Cost End --------------
}

//Master Table End ====================================================================================================================================================
//Dtls Table===========================================================================================================================================================
if ($action=="save_update_delete_quotation_entry_dtls")
{
	//id,quotation_id,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,comm_cost,comm_cost_percent,lab_test,lab_test_percent,inspection, 	inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,common_oh,common_oh_percent,total_cost,total_cost_percent,commission,commission_percent,final_cost_dzn,final_cost_dzn_percent,final_cost_pcs,a1st_quoted_price,revised_price,confirm_price,margin_dzn,confirm_date,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		//if (is_duplicate_field( "company_name", "lib_company", "company_name=$txt_company_name and is_deleted=0" ) == 1)
		//{
			//echo "11**0"; die;
		//}
		//else
		//{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			$id=return_next_id( "id", "wo_price_quotation_costing_mst", 1 ) ;
			/*txt_fabric_pre_cost*txt_fabric_po_price*txt_trim_pre_cost*txt_trim_po_price*
txt_embel_pre_cost*txt_embel_po_price*txt_comml_pre_cost*txt_comml_po_price*
txt_lab_test_pre_cost*txt_lab_test_po_price*txt_inspection_pre_cost*txt_inspection_po_price*
txt_cm_pre_cost*txt_cm_po_price*txt_freight_pre_cost*txt_freight_po_price*
txt_common_oh_pre_cost*txt_common_oh_po_price*txt_total_pre_cost*txt_total_po_price*
txt_commission_pre_cost*txt_commission_po_price*txt_final_cost_dzn_pre_cost*txt_final_cost_dzn_po_price*
txt_final_cost_pcs_po_price*txt_1st_quoted_price_pre_cost*txt_revised_price_pre_cost*
txt_confirm_price_pre_cost*txt_margin_dzn_pre_cost*txt_confirm_date_pre_cost'*/
//currier_pre_cost 	currier_percent 	certificate_pre_cost 	certificate_percent
			$field_array="id,quotation_id,costing_per_id,order_uom_id,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,lab_test,lab_test_percent,inspection, 	inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent,certificate_pre_cost,certificate_percent,common_oh,common_oh_percent,total_cost,total_cost_percent,commission,commission_percent,final_cost_dzn,final_cost_dzn_percent,final_cost_pcs,final_cost_set_pcs_rate,a1st_quoted_price,a1st_quoted_price_percent,a1st_quoted_price_date,revised_price,revised_price_date,confirm_price,confirm_price_set_pcs_rate,confirm_price_dzn, confirm_price_dzn_percent, margin_dzn,margin_dzn_percent,price_with_commn_dzn,price_with_commn_percent_dzn,price_with_commn_pcs,price_with_commn_percent_pcs, confirm_date,asking_quoted_price,asking_quoted_price_percent,terget_qty, inserted_by, insert_date, status_active, is_deleted ";
	//txt_quotation_id*cbo_company_name*cbo_buyer_name*txt_style_ref*txt_revised_no*cbo_pord_dept*txt_style_desc*cbo_currercy*cbo_agent*txt_offer_qnty*cbo_region*cbo_color_range*cbo_inco_term*txt_incoterm_place*txt_machine_line*txt_prod_line_hr*cbo_fabric_source*cbo_costing_per*txt_quotation_date*txt_est_ship_date*txt_factory*txt_remarks*garments_nature*update_id
			$data_array="(".$id.",".$update_id.",".$cbo_costing_per.",".$cbo_order_uom.",".$txt_fabric_pre_cost.",".$txt_fabric_po_price.",".$txt_trim_pre_cost.",".$txt_trim_po_price.",".$txt_embel_pre_cost.",".$txt_embel_po_price.",".$txt_wash_pre_cost.",".$txt_wash_po_price.",".$txt_comml_pre_cost.",".$txt_comml_po_price.",".$txt_lab_test_pre_cost.",".$txt_lab_test_po_price.",".$txt_inspection_pre_cost.",".$txt_inspection_po_price.",".$txt_cm_pre_cost.",".$txt_cm_po_price.",".$txt_freight_pre_cost.",".$txt_freight_po_price.",".$txt_currier_pre_cost.",".$txt_currier_po_price.",".$txt_certificate_pre_cost.",".$txt_certificate_po_price.",".$txt_common_oh_pre_cost.",".$txt_common_oh_po_price.",".$txt_total_pre_cost.",".$txt_total_po_price.",".$txt_commission_pre_cost.",".$txt_commission_po_price.",".$txt_final_cost_dzn_pre_cost.",".$txt_final_cost_dzn_po_price.",".$txt_final_cost_pcs_po_price.",".$txt_final_cost_set_pcs_rate.",".$txt_1st_quoted_price_pre_cost.",".$txt_1st_quoted_po_price.",".$txt_first_quoted_price_date.",".$txt_revised_price_pre_cost.",".$txt_revised_price_date.",".$txt_confirm_price_pre_cost.",".$txt_confirm_price_set_pcs_rate.",".$txt_confirm_price_pre_cost_dzn.",".$txt_confirm_price_po_price_dzn.",".$txt_margin_dzn_pre_cost.",".$txt_margin_dzn_po_price.",".$txt_with_commission_pre_cost_dzn.",".$txt_with_commission_po_price_dzn.",".$txt_with_commission_pre_cost_pcs.",".$txt_with_commission_po_price_pcs.",".$txt_confirm_date_pre_cost.",".$txt_asking_quoted_price.",".$txt_asking_quoted_po_price.",".$txt_terget_qty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
			$rID=sql_insert("wo_price_quotation_costing_mst",$field_array,$data_array,1);
			//echo $rID; die;
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "0**".$rID."**".$id;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID."**".$id;
				}
			}
			
			if($db_type==2 || $db_type==1 )
			{
				echo '0**0'."**".$id;
			}
			disconnect($con);
			die;
		//}
	}
	
	else if ($operation==1)   // Update Here
	{
		//if (is_duplicate_field( "company_name", "lib_company", "company_name=$txt_company_name and id!=$update_id and is_deleted=0" ) == 1)
		//{
			//echo "11**0"; die;
		//}
		//else
		//{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			 	
				if(str_replace("'",'',$update_id_dtls)=="")
				{
					$id=return_next_id( "id", "wo_price_quotation_costing_mst", 1 ) ;
					$field_array="id,quotation_id,costing_per_id,order_uom_id,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,lab_test,lab_test_percent,inspection, 	inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent,certificate_pre_cost,certificate_percent,common_oh,common_oh_percent,total_cost,total_cost_percent,commission,commission_percent,final_cost_dzn,final_cost_dzn_percent,final_cost_pcs,final_cost_set_pcs_rate,a1st_quoted_price,a1st_quoted_price_percent,a1st_quoted_price_date,revised_price,revised_price_date,confirm_price,confirm_price_set_pcs_rate,confirm_price_dzn, confirm_price_dzn_percent, margin_dzn,margin_dzn_percent,price_with_commn_dzn,price_with_commn_percent_dzn,price_with_commn_pcs,price_with_commn_percent_pcs, confirm_date,asking_quoted_price,asking_quoted_price_percent,terget_qty, inserted_by, insert_date, status_active, is_deleted ";
					$data_array="(".$id.",".$update_id.",".$cbo_costing_per.",".$cbo_order_uom.",".$txt_fabric_pre_cost.",".$txt_fabric_po_price.",".$txt_trim_pre_cost.",".$txt_trim_po_price.",".$txt_embel_pre_cost.",".$txt_embel_po_price.",".$txt_wash_pre_cost.",".$txt_wash_po_price.",".$txt_comml_pre_cost.",".$txt_comml_po_price.",".$txt_lab_test_pre_cost.",".$txt_lab_test_po_price.",".$txt_inspection_pre_cost.",".$txt_inspection_po_price.",".$txt_cm_pre_cost.",".$txt_cm_po_price.",".$txt_freight_pre_cost.",".$txt_freight_po_price.",".$txt_currier_pre_cost.",".$txt_currier_po_price.",".$txt_certificate_pre_cost.",".$txt_certificate_po_price.",".$txt_common_oh_pre_cost.",".$txt_common_oh_po_price.",".$txt_total_pre_cost.",".$txt_total_po_price.",".$txt_commission_pre_cost.",".$txt_commission_po_price.",".$txt_final_cost_dzn_pre_cost.",".$txt_final_cost_dzn_po_price.",".$txt_final_cost_pcs_po_price.",".$txt_final_cost_set_pcs_rate.",".$txt_1st_quoted_price_pre_cost.",".$txt_1st_quoted_po_price.",".$txt_first_quoted_price_date.",".$txt_revised_price_pre_cost.",".$txt_revised_price_date.",".$txt_confirm_price_pre_cost.",".$txt_confirm_price_set_pcs_rate.",".$txt_confirm_price_pre_cost_dzn.",".$txt_confirm_price_po_price_dzn.",".$txt_margin_dzn_pre_cost.",".$txt_margin_dzn_po_price.",".$txt_with_commission_pre_cost_dzn.",".$txt_with_commission_po_price_dzn.",".$txt_with_commission_pre_cost_pcs.",".$txt_with_commission_po_price_pcs.",".$txt_confirm_date_pre_cost.",".$txt_asking_quoted_price.",".$txt_asking_quoted_po_price.",".$txt_terget_qty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
					$rID=sql_insert("wo_price_quotation_costing_mst",$field_array,$data_array,1);
				}
				if(str_replace("'",'',$update_id_dtls)!="")
				{
					$field_array="costing_per_id*order_uom_id*fabric_cost*fabric_cost_percent*trims_cost*trims_cost_percent*embel_cost*embel_cost_percent*wash_cost*wash_cost_percent*comm_cost*comm_cost_percent*lab_test*lab_test_percent*inspection* 	inspection_percent*cm_cost*cm_cost_percent*freight*freight_percent*currier_pre_cost*currier_percent*certificate_pre_cost*certificate_percent*common_oh*common_oh_percent*total_cost*total_cost_percent*commission*commission_percent*final_cost_dzn*final_cost_dzn_percent*final_cost_pcs*final_cost_set_pcs_rate*a1st_quoted_price*a1st_quoted_price_percent*a1st_quoted_price_date*revised_price*revised_price_date*confirm_price*confirm_price_set_pcs_rate*confirm_price_dzn* confirm_price_dzn_percent* margin_dzn*margin_dzn_percent*price_with_commn_dzn*price_with_commn_percent_dzn*price_with_commn_pcs*price_with_commn_percent_pcs* confirm_date*asking_quoted_price*asking_quoted_price_percent*terget_qty*updated_by* update_date* status_active* is_deleted ";
					$data_array="".$cbo_costing_per."*".$cbo_order_uom."*".$txt_fabric_pre_cost."*".$txt_fabric_po_price."*".$txt_trim_pre_cost."*".$txt_trim_po_price."*".$txt_embel_pre_cost."*".$txt_embel_po_price."*".$txt_wash_pre_cost."*".$txt_wash_po_price."*".$txt_comml_pre_cost."*".$txt_comml_po_price."*".$txt_lab_test_pre_cost."*".$txt_lab_test_po_price."*".$txt_inspection_pre_cost."*".$txt_inspection_po_price."*".$txt_cm_pre_cost."*".$txt_cm_po_price."*".$txt_freight_pre_cost."*".$txt_freight_po_price."*".$txt_currier_pre_cost."*".$txt_currier_po_price."*".$txt_certificate_pre_cost."*".$txt_certificate_po_price."*".$txt_common_oh_pre_cost."*".$txt_common_oh_po_price."*".$txt_total_pre_cost."*".$txt_total_po_price."*".$txt_commission_pre_cost."*".$txt_commission_po_price."*".$txt_final_cost_dzn_pre_cost."*".$txt_final_cost_dzn_po_price."*".$txt_final_cost_pcs_po_price."*".$txt_final_cost_set_pcs_rate."*".$txt_1st_quoted_price_pre_cost."*".$txt_1st_quoted_po_price."*".$txt_first_quoted_price_date."*".$txt_revised_price_pre_cost."*".$txt_revised_price_date."*".$txt_confirm_price_pre_cost."*".$txt_confirm_price_set_pcs_rate."*".$txt_confirm_price_pre_cost_dzn."*".$txt_confirm_price_po_price_dzn."*".$txt_margin_dzn_pre_cost."*".$txt_margin_dzn_po_price."*".$txt_with_commission_pre_cost_dzn."*".$txt_with_commission_po_price_dzn."*".$txt_with_commission_pre_cost_pcs."*".$txt_with_commission_po_price_pcs."*".$txt_confirm_date_pre_cost."*".$txt_asking_quoted_price."*".$txt_asking_quoted_po_price."*".$txt_terget_qty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'1'*'0'";
					$rID=sql_update("wo_price_quotation_costing_mst",$field_array,$data_array,"id","".$update_id_dtls."",1);
				}
			
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "1**".$rID."**".str_replace("'","",$update_id_dtls);
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID."**".str_replace("'","",$update_id_dtls);
				}
			}
			if($db_type==2 || $db_type==1 )
			{
			echo "1**".$rID."**".str_replace("'","",$update_id_dtls);
			}
			disconnect($con);
			die;
		//}
		
	}
	
	else if ($operation==2)   // Update Here
	{
		/*if (is_duplicate_field( "b.tag_company", "lib_buyer a, lib_buyer_tag_company b", "a.id=b.buyer_id and b.tag_company=$update_id and a.is_deleted=0" ) == 1)
		{
			echo "13**0"; die;
		}
		if (is_duplicate_field( "b.tag_company", "lib_supplier a, lib_supplier_tag_company b", "a.id=b.supplier_id and b.tag_company=$update_id and a.is_deleted=0" ) == 1)

		{
			echo "13**0"; die;
		}
		if (is_duplicate_field( "company_id", "lib_location", "company_id=$update_id and is_deleted=0" ) == 1)
		{
			echo "13**0"; die;
		}
		if (is_duplicate_field( "company_id", "lib_profit_center", "company_id=$update_id and is_deleted=0" ) == 1)
		{
			echo "13**0"; die;
		}
		if (is_duplicate_field( "company_id", "lib_prod_floor", "company_id=$update_id and is_deleted=0" ) == 1)
		{
			echo "13**0"; die;
		}  
		if (is_duplicate_field( "company_id", "lib_standard_cm_entry", "company_id=$update_id and is_deleted=0" ) == 1)
		{
			echo "13**0"; die;
		}*/
		
		//else
		//{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			$field_array="updated_by*update_date*status_active*is_deleted";
			$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			
			$rID=sql_delete("wo_price_quotation",$field_array,$data_array,"id","".$update_id."",1);
			
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "2**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				echo "2**".$rID;
			}
			disconnect($con);
			die;
		//}
	}
}

//Dtls Table End=======================================================================================================================================================
// Fabric Cost=========================================================================================================================================================
if ($action=="show_fabric_cost_listview")
{
$data=explode("_",$data);


	?>
       <h3 align="left" class="accordion_h" onClick="show_hide_content('fabric_cost', '')"> +Fabric Cost </h3>
       <div id="content_fabric_cost" style="display:none;">  
    	<fieldset>
        	<form id="fabriccost_3" autocomplete="off">
            <!--<input type="text" id="cons_breck_down" name="cons_breck_down" value="" width="500" /> 
            <input type="text" id="msmnt_breack_down" name="msmnt_breack_down"/>-->
            <input type="hidden" id="tr_ortder" name="tr_ortder" value="" width="500" /> 
            	<table width="1500" cellspacing="0" class="rpt_table" border="0" id="tbl_fabric_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="100">Gmts Item</th><th  width="100">Body Part</th><th  width="90">Fab Nature</th><th width="90">Color Type</th><th width="220">Fabric Description</th><th  width="90">Fabric Source</th><th id="" width="60">Width/Dia Type</th><th id="gsmweight_caption" width="75">GSM/ Weight</th><th width="100">Consumption Basis</th><th width="75">Fabric Cons</th><th width="73">Rate</th><th width="90">Amount</th><th width="95">Status</th><th width=""></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$gmts_item_id=return_field_value("gmts_item_id", "wo_price_quotation", "id='$data[0]'");
					$approved=return_field_value("approved", "wo_price_quotation", "id='$data[0]'");
					if($approved==1)
					{
					$disabled=1;
					//$permission=0;
					}
					else
					{
					$disabled=0;
					//$permission=$permission;
					}
					$data_array=sql_select("select id, quotation_id, item_number_id, body_part_id, fab_nature_id, color_type_id,lib_yarn_count_deter_id,construction, composition,fabric_description, gsm_weight, avg_cons, fabric_source, rate, amount,avg_finish_cons,avg_process_loss, fab_cons_in_quotat_varia, status_active,cons_breack_down,msmnt_break_down,yarn_breack_down,marker_break_down,width_dia_type from wo_pri_quo_fabric_cost_dtls where quotation_id='$data[0]'");
					if (count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="fabriccosttbltr_<? echo $i; ?>" align="center">
                                    <td><?  echo create_drop_down( "cbogmtsitem_".$i, 95, $garments_item,"", 1, "-- Select Item --", $row[csf("item_number_id")], "",$disabled,$gmts_item_id ); ?></td>
                                    <td><?  echo create_drop_down( "txtbodypart_".$i, 80, $body_part,"", 1, "-- Select --", $row[csf("body_part_id")], "",$disabled,"" ); ?></td>
                                    <td><?  echo create_drop_down( "cbofabricnature_".$i, 80, $item_category,"", 0, "", $row[csf("fab_nature_id")], "change_caption( this.value, 'gsmweight_caption' );",$disabled,"2,3" ); ?></td>
                                    <td><?  echo create_drop_down( "cbocolortype_".$i, 80, $color_type,"", 1, "-- Select --", $row[csf("color_type_id")], "",$disabled,"" ); ?></td>
                                    <td>
                                    <input type="hidden" id="libyarncountdeterminationid_<? echo $i; ?>"  name="libyarncountdeterminationid_<? echo $i; ?>" class="text_boxes" style="width:10px"  value="<? echo $row[csf("lib_yarn_count_deter_id")];  ?>"  <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    <input type="hidden" id="txtconstruction_<? echo $i; ?>"  name="txtconstruction_<? echo $i; ?>" class="text_boxes" style="width:95px" value="<? echo $row[csf("construction")];  ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?>/>
                                    <input type="hidden" id="txtcomposition_<? echo $i; ?>"    name="txtcomposition_<? echo $i; ?>"  class="text_boxes" style="width:95px" value="<? echo $row[csf("composition")];  ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?>/> 
                                    
                                    <input type="text" id="fabricdescription_<? echo $i; ?>"    name="fabricdescription_<? echo $i; ?>"  class="text_boxes" style="width:220px" onDblClick="open_fabric_decription_popup(<? echo $i; ?>)" value="<? echo $row[csf("fabric_description")];  ?>"  <? if($disabled==0){echo "";}else{echo "disabled";}?>   title="<? echo $row[csf("fabric_description")];  ?>" readonly/>
                                    </td>
                                    <td>
									 <?
									 
									 echo create_drop_down( "cbofabricsource_".$i, 80, $fabric_source, "", 0, "", $row[csf("fabric_source")], "enable_disable( this.value,'txtrate_*txtamount_', $i );",$disabled,"" ); 
									 ?>
                                     </td> 
                                     <td>
                                     <?  echo create_drop_down( "cbowidthdiatype_".$i, 100, $fabric_typee,"", 1, "-- Select --", $row[csf("width_dia_type")], "",$disabled,"" ); ?>
                                   
                                    </td>
                                    <td>
                                    <input type="text" id="txtgsmweight_<? echo $i; ?>" name="txtgsmweight_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" onBlur="sum_yarn_required()" value="<? echo $row[csf("gsm_weight")];  ?>"  <? if($disabled==0){echo "";}else{echo "disabled";}?> /> 
                                    </td>
                                    <td>
                                   <? 
									echo create_drop_down( "consumptionbasis_".$i, 100, $consumtion_basis,'', 0, '', $row[csf('fab_cons_in_quotat_varia')], "","","" );
								   ?>
                                   </td>
                                    <td>
                                    <input type="text" id="txtconsumption_<? echo $i; ?>" name="txtconsumption_<? echo $row[csf("id")]; ?>" onBlur="math_operation( 'txtamount_<? echo $i; ?>', 'txtconsumption_<? echo $i; ?>*txtrate_<? echo $i; ?>', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );sum_yarn_required();set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost' ) "  onClick="open_consumption_popup('requires/quotation_entry_controller.php?action=consumption_popup', 'Consumption Entry Form','txtbodypart_<? echo $i; ?>','cbofabricnature_<? echo $i; ?>','txtgsmweight_<? echo $i; ?>','<? echo $i; ?>','updateid_<? echo $i; ?>')"   class="text_boxes_numeric" style="width:60px" value="<? echo $row[csf("avg_cons")]; ?>" readonly/>
                                     </td>
                                     
                                    <td>
                                    <input type="text" id="txtrate_<? echo $i; ?>" name="txtrate_<? echo $i; ?>" onBlur="math_operation( 'txtamount_<? echo $i; ?>', 'txtconsumption_<? echo $i; ?>*txtrate_<? echo $i; ?>', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost' ) "   class="text_boxes_numeric" style="width:60px" value="<? echo $row[csf("rate")]; ?>" <? if($row[csf("fabric_source")]==2 ){echo "";}else{echo "disabled";}?>  /> 
                                    </td>
                                    <td>
                                    <input type="text" id="txtamount_<? echo $i; ?>"  onBlur="set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost' ) "  readonly  name="txtamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("amount")];  ?> " <? if($row[csf("fabric_source")]==2 || $disabled==0){echo "";}else{echo "disabled";}?>  />
                                    </td>
                                   
                                    <td width="95"><? echo create_drop_down( "cbostatus_".$i, 80, $row_status, "", 0, "", $row[csf("status_active")], "",$disabled,"" );  ?></td>  
                                    <td>
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?>/>
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_fabric_cost' );" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    <input type="hidden" id="txtfinishconsumption_<? echo $i; ?>"  name="txtfinishconsumption_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="<? echo $row[csf("avg_finish_cons")]; ?>" readonly/>
                                    <input type="hidden" id="txtavgprocessloss_<? echo $i; ?>"  name="txtavgprocessloss_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="<? echo $row[csf("avg_process_loss")]; ?>" readonly/>
                                    
                                     <input type="hidden" id="consbreckdown_<? echo $i; ?>" name="consbreckdown_<? echo $i; ?>"   class="text_boxes" style="width:90px" value="<? echo $row[csf("cons_breack_down")]; ?>" />                                     
                                    <input type="hidden" id="msmntbreackdown_<? echo $i; ?>" name="msmntbreackdown_<? echo $i; ?>"  class="text_boxes" style="width:90px" value="<? echo  $row[csf("msmnt_break_down")]; ?>" />
                                     <input type="hidden" id="markerbreackdown_<? echo $i; ?>" name="markerbreackdown_<? echo $i; ?>"  class="text_boxes" style="width:90px" value="<? echo  $row[csf("marker_break_down")]; ?>" />
                                    <input type="hidden" id="yarnbreackdown_<? echo $i; ?>" name="yarnbreackdown_<? echo $i; ?>"  class="text_boxes" style="width:90px" value="<? echo  $row[csf("yarn_breack_down")]; ?>" />  
                                    <input type="hidden" id="processlossmethod_<? echo $i; ?>" name="processlossmethod_<? echo $i; ?>"/>
 
                                    <input type="hidden" id="updateid_<? echo $i; ?>" name="updateid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("id")]; ?>"  />                                      
                                     </td> 
                                </tr>
                            
                            <?
							 
						}
					}
					else
					{
					$selected_item=0;
					$gmts_item_id_arr=explode(",",$gmts_item_id);
					if(count($gmts_item_id_arr)==1)
					{
					$selected_item=	$gmts_item_id;
					}

					?>
                    <tr id="fabriccosttbltr_1" align="center">
                                    <td><?  echo create_drop_down( "cbogmtsitem_1", 95, $garments_item,"", 1, "-- Select Item --", $selected_item, "" ,"",$gmts_item_id); ?></td>
                                    <td><?  echo create_drop_down( "txtbodypart_1", 80, $body_part,"", 1, "-- Select --", $selected, "","","" ); ?></td>
                                    <td><?  echo create_drop_down( "cbofabricnature_1", 80, $item_category,"", 0, "", 2, "change_caption( this.value, 'gsmweight_caption' )","","2,3" ); ?></td>
                                    <td><?  echo create_drop_down( "cbocolortype_1", 80, $color_type,"", 1, "-- Select --", $selected, "","","" ); ?></td>
                                    
                                    <td>
                                    <input type="hidden" id="libyarncountdeterminationid_1" name="libyarncountdeterminationid_1" class="text_boxes" style="width:10px"  /> 
                                    <input type="hidden" id="txtconstruction_1" name="txtconstruction_1" class="text_boxes" style="width:95px">
                                    <input type="hidden" id="txtcomposition_1" value=""  name="txtcomposition_1"  class="text_boxes" style="width:95px"> 
                                    <input type="text" id="fabricdescription_1" placeholder="Dobule Click To Search"  name="fabricdescription_1"  class="text_boxes" style="width:220px" onDblClick="open_fabric_decription_popup(1)" readonly /> 
                                    </td>
                                     <td><? echo create_drop_down( "cbofabricsource_1", 80, $fabric_source, "", 0, "", "", "enable_disable( this.value,'txtrate_*txtamount_', 1 );","","" );  ?></td> 
                                     <td><?  echo create_drop_down( "cbowidthdiatype_1", 100, $fabric_typee,"", 1, "-- Select --", $selected, "","","" ); ?></td>
                                    <td><input type="text" id="txtgsmweight_1" name="txtgsmweight_1" class="text_boxes_numeric" style="width:60px" onBlur="sum_yarn_required()"> </td>
                                    <td>
                                   <? 
									echo create_drop_down( "consumptionbasis_1", 100, $consumtion_basis,'', 0, '', $row[csf('fab_cons_in_quotat_varia')], "","","" );
								   ?>
                                   </td>
                                    <td> 
                                    <input type="text" id="txtconsumption_1" name="txtconsumption_1" onBlur="math_operation( 'txtamount_1', 'txtconsumption_1*txtrate_1', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );sum_yarn_required();set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost' )" onClick="open_consumption_popup('requires/quotation_entry_controller.php?action=consumption_popup', 'Consumption Entry Form','txtbodypart_1','cbofabricnature_1','txtgsmweight_1','1','updateid_1')"  value=""  class="text_boxes_numeric" style="width:60px" readonly  /> 
                                    </td>
                                   
                                    <td><input type="text" id="txtrate_1" onBlur="math_operation( 'txtamount_1', 'txtconsumption_1*txtrate_1', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );set_sum_value( 'txtamount_sum', 'txtamount_' ,'tbl_fabric_cost') "  name="txtrate_1" class="text_boxes_numeric" style="width:60px" disabled /> </td>
                                    <td><input type="text" id="txtamount_1"  onBlur="set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost' ) " readonly  name="txtamount_1" class="text_boxes_numeric" style="width:80px" disabled /></td>
                                    
                                    <td width="95"><? echo create_drop_down( "cbostatus_1", 80, $row_status, "", 0, "", "", "","","" );  ?></td>  
                                    <td>
                                    <input type="button" id="increase_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(1)" />
                                    <input type="button" id="decrease_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1,'tbl_fabric_cost');" />
                                    <input type="hidden" id="txtfinishconsumption_1"  name="txtfinishconsumption_1" class="text_boxes_numeric" style="width:60px" readonly/>
                                    <input type="hidden" id="txtavgprocessloss_1"  name="txtavgprocessloss_1" class="text_boxes_numeric" style="width:60px"  readonly/>
                                    <input type="hidden" id="consbreckdown_1" name="consbreckdown_1" value="" class="text_boxes" style="width:90px" /> 
                                    <input type="hidden" id="yarnbreackdown_1" name="yarnbreackdown_1" value="" class="text_boxes" style="width:90px" />
                                    <input type="hidden" id="markerbreackdown_1" name="markerbreackdown_1" value="" class="text_boxes" style="width:90px" />  
                                    <input type="hidden" id="msmntbreackdown_1" name="msmntbreackdown_1" value="" class="text_boxes" style="width:90px" /> 
                                    <input type="hidden" id="processlossmethod_1" name="processlossmethod_1"/>
                                    <input type="hidden" id="updateid_1" name="updateid_1" value="" class="text_boxes" style="width:20px" />
                                    </td> 
                                </tr>
                    <? } ?>
                </tbody>
                </table>
                <table width="1500" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                
                    	<tr>
                        	<th>&nbsp;</th>
                            
                        </tr>
                    	<tr>
                        	<th  width="201">Yarn Req(Kg):<input type="text" id="tot_yarn_needed" name="tot_yarn_needed" class="text_boxes_numeric" style="width:75px;" readonly/>
</th>
                            <th  width=" 415">
                            Woven Fabric Req. (Yds):<input type="text" id="txtwoven_sum" name="txtwoven_sum" class="text_boxes_numeric" style="width:95px" readonly>
                            </th>
                            
                            <th width="225">Knit Fabric Req. (Kg):<input type="text" id="txtknit_sum"  name="txtknit_sum" class="text_boxes_numeric" style="width:60px" readonly></th>
                            <th  width="50"></th>
                           <th width="100"></th>

                            <th width="65"></th>
                            <th width="73"></th>
                            <th width="90"><input type="text" id="txtamount_sum"    name="txtamount_sum" class="text_boxes_numeric" style="width:80px" readonly></th>
                            <th width="95"></th>
                            <th width=""></th>
                        </tr>
                    </tfoot>
                </table>
                           
                <table width="1500" cellspacing="0" class="" border="0">
                
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
                        
						<?
						if ( count($data_array)>0)
					    {
						echo load_submit_buttons( $permission, "fnc_fabric_cost_dtls", 1,0,"reset_form('fabriccost_3','','','cbofabricnature_,3,$i')",3) ;
					    }
						else
						{
						echo load_submit_buttons( $permission, "fnc_fabric_cost_dtls", 0,0,"reset_form('fabriccost_3','','','cbofabricnature_,3,$i')",3) ;
						}
						?>  
                        </td> 
                    </tr>
                </table>
               
            </form>
        </fieldset>
        </div>
        
       <h3 align="left" id="accordion_h_yarn" class="accordion_h" onClick="show_hide_content('yarn_cost', '');sum_yarn_required()">+Yarn Cost &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Yarn Needed:&nbsp;<span id="tot_yarn_needed_span"></span></h3>
       <div id="content_yarn_cost" style="display:none;">            
    	<fieldset>
        	<form id="yarnccost_4" autocomplete="off">
            	<table width="1300" cellspacing="0" class="rpt_table" border="0" id="tbl_yarn_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="100">Count</th><th  width="100">Comp 1</th><th  width="90">%</th><th width="90">Comp 2</th><th width="110">%</th><th width="110">Type</th><th width="75">Cons Ratio</th><th width="75">Cons Qnty</th><th width="73">Rate</th><th width="90">Amount</th><th width="95">Status</th><th width=""></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount,status_active from wo_pri_quo_fab_yarn_cost_dtls where quotation_id='$data[0]'");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="yarncost_1" align="center">
                                    <td>
									<? 
									echo create_drop_down( "cbocount_".$i, 95, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1,"-- Select Item --", $row[csf("count_id")], "",$disabled,"" ); 
									?>
                                    </td>
                                    <td><?  echo create_drop_down( "cbocompone_".$i, 80, $composition,"", 1, "-- Select --", $row[csf("copm_one_id")], "control_composition($i,this.id,'percent_one')",$disabled,"" ); ?></td>
                                   <td>
                                    <input type="text" id="percentone_<? echo $i; ?>"  name="percentone_<? echo $i; ?>" class="text_boxes" style="width:80px" onChange="control_composition(<? echo $i; ?>,this.id,'percent_one')" value="<? echo $row[csf("percent_one")];  ?>"  <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    </td>
                                    <td><?  echo create_drop_down( "cbocomptwo_".$i, 80, $composition,"", 1, "-- Select --", $row[csf("copm_two_id")], "control_composition($i,this.id,'percent_two')",$disabled,"" ); ?></td>
                                    <td>
                                    <input type="text" id="percenttwo_<? echo $i; ?>"  name="percenttwo_<? echo $i; ?>" class="text_boxes" style="width:95px" onChange="control_composition(<? echo $i; ?>,this.id,'percent_two')" value="<? echo $row[csf("percent_two")];  ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    </td>
                                    <td><?  echo create_drop_down( "cbotype_".$i, 80, $yarn_type,"", 1, "-- Select --", $row[csf("type_id")], "",$disabled,"" ); ?></td>
                                    <td>
                                    <input type="text" id="consratio_<? echo $i; ?>" name="consratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" onChange="calculate_yarn_consumption_ratio('consratio_<? echo $i;?>','consqnty_<? echo $i; ?>','txtrateyarn_<? echo $i; ?>','txtamountyarn_<? echo $i; ?>','calculate_consumption')"  value="<? echo $row[csf("cons_ratio")];  ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?> /> 
                                    </td>
                                    <td>
                                    <input type="text" id="consqnty_<? echo $i; ?>" name="consqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" onChange="calculate_yarn_consumption_ratio('consratio_<? echo $i;?>','consqnty_<? echo $i; ?>','txtrateyarn_<? echo $i; ?>','txtamountyarn_<? echo $i; ?>','calculate_ratio')" value="<? echo $row[csf("cons_qnty")]; ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                     </td>
                                    
                                    <td>
                                    <input type="text" id="txtrateyarn_<? echo $i; ?>" name="txtrateyarn_1<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" onChange="calculate_yarn_consumption_ratio('consratio_<? echo $i;?>','consqnty_<? echo $i; ?>','txtrateyarn_<? echo $i; ?>','txtamountyarn_<? echo $i; ?>','calculate_amount')" value="<? echo $row[csf("rate")]; ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?> /> 
                                    </td>
                                    <td>
                                    <input type="text" id="txtamountyarn_<? echo $i; ?>"  name="txtamountyarn_1<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("amount")]; ?>"  readonly/>
                                    </td>
                                   
                                    <td width="95"><? echo create_drop_down( "cbostatusyarn_".$i, 80, $row_status, "", 0, "", $row[csf("status_active")], "",$disabled,"" );  ?></td>  
                                    <td>
                                    <input type="button" id="increaseyarn_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_yarn_cost(<? echo $i; ?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?>  />
                                    <input type="button" id="decreaseyarn_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_yarn_cost' );"  <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    <input type="text" id="updateidyarncost_<? echo $i; ?>" name="updateidyarncost_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("id")]; ?>"  />                                     
                                     </td> 
                                </tr>
                            
                            <?
							 
						}
					}
					else
					{
					?>
                    <tr id="yarncost_1" align="center">
                                   <td>
									<? 
									echo create_drop_down( "cbocount_1", 95, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1," -- Select Item --", '', '','','' ); 
									?>
                                    </td>
                                    <td><?  echo create_drop_down( "cbocompone_1", 80, $composition,"", 1, "-- Select --", '', "control_composition(1,this.id,'comp_one')",'','' ); ?></td>
                                   <td>
                                    <input type="text" id="percentone_1"  name="percentone_1" class="text_boxes" style="width:80px" onChange="control_composition(1,this.id,'percent_one')" value="" />
                                    </td>
                                    <td><?  echo create_drop_down( "cbocomptwo_1", 80, $composition,"", 1, "-- Select --", '', "control_composition(1,this.id,'comp_two')",'','' ); ?></td>
                                    <td>
                                    <input type="text" id="percenttwo_1"  name="percenttwo_1" class="text_boxes" style="width:95px" onChange="control_composition(1,this.id,'percent_two')" value="" />
                                    </td>
                                    <td><?  echo create_drop_down( "cbotype_1", 80, $yarn_type,"", 1, "-- Select --", '', '','','' ); ?></td>
                                    <td>
                                    <input type="text" id="consratio_1" name="consratio_1" class="text_boxes_numeric" style="width:60px" onChange="calculate_yarn_consumption_ratio('consratio_1','consqnty_1','txtrateyarn_1','txtamountyarn_1','calculate_consumption')" value=""> 
                                    </td>
                                    <td>
                                    <input type="text" id="consqnty_1" name="consqnty_1" class="text_boxes_numeric" style="width:60px" onChange="calculate_yarn_consumption_ratio('consratio_1','consqnty_1','txtrateyarn_1','txtamountyarn_1','calculate_ratio')" value=""/>
                                     </td>
                                    
                                    <td>
                                    <input type="text" id="txtrateyarn_1"  name="txtrateyarn_1" class="text_boxes_numeric" style="width:60px" onChange="calculate_yarn_consumption_ratio('consratio_1','consqnty_1','txtrateyarn_1','txtamountyarn_1','calculate_amount')" value="" /> 
                                    </td>
                                    <td>
                                    <input type="text" id="txtamountyarn_1" name="txtamountyarn_1" class="text_boxes_numeric" style="width:80px" value=""  readonly/>
                                    </td>
                                   
                                    <td width="95"><? echo create_drop_down( "cbostatusyarn_1", 80, $row_status,"", 0, "0", '', '','','' );  ?></td>  
                                    <td>
                                    <input type="button" id="increaseyarn_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_yarn_cost(1)" />
                                    <input type="button" id="decreaseyarn_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1,'tbl_yarn_cost' );" />
                                    <input type="text" id="updateidyarncost_1" name="updateidyarncost_1"  class="text_boxes" style="width:20px" value=""  />                                    </td> 
                                    
                                </tr>
                    <? } ?>
                </tbody>
                </table>
                <table width="1300" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                        	<th width="100" style="width:607px;">SUM</th>
                            <th width="75"><input type="text" id="txtconsratio_sum" name="txtconsratio_sum" class="text_boxes_numeric" style="width:60px" readonly></th>
                            <th width="75"><input type="text" id="txtconsumptionyarn_sum" name="txtconsumptionyarn_sum" class="text_boxes_numeric" style="width:60px" readonly></th>
                            <th width="73"></th>
                            <th width="90"><input type="text" id="txtamountyarn_sum" name="txtamountyarn_sum" class="text_boxes_numeric" style="width:80px" readonly></th>
                            <th width="95"></th>
                            <th width=""></th>
                        </tr>
                    </tfoot>
                </table>
                           
                <table width="1300" cellspacing="0" class="" border="0">
                
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
                        
						<?
						if (count($data_array)>0)
					    {
						echo load_submit_buttons( $permission, "fnc_fabric_yarn_cost_dtls", 1,0,"reset_form('yarnccost_4','','',0)",4) ;
					    }
						else
						{
						echo load_submit_buttons( $permission, "fnc_fabric_yarn_cost_dtls", 0,0,"reset_form('yarnccost_4','','',0)",4) ;
						}
						?>  
                        </td> 
                    </tr>
                </table>
               
               
            </form>
        </fieldset>
        </div>
        
        
        
       <h3 align="left" id="accordion_h_conversion" class="accordion_h" onClick="show_hide_content('conversion_cost', '')">+Conversion Cost</h3> 
       <div id="content_conversion_cost" style="display:none;" align="left">            
    	<fieldset>
        	<form id="conversionccost_5" autocomplete="off">
            	<table width="910" cellspacing="0" class="rpt_table" border="0" id="tbl_conversion_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="380">Fabric Description</th><th  width="155">Process</th><th  width="50">Req. Qnty</th><th width="50">Charge/ Unit</th><th width="80">Amount</th><th width="80">Status</th><th width=""></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$conversion_from_chart=return_field_value("conversion_from_chart", "variable_order_tracking", "company_name='$data[1]'  and variable_list=21 and status_active=1 and is_deleted=0");
					if($conversion_from_chart=="")
					{
						$conversion_from_chart=2;
					}
					
//echo $conversion_from_chart;
					$fab_description=array();
					$fab_description_array=sql_select("select id, body_part_id, color_type_id,construction,composition from wo_pri_quo_fabric_cost_dtls where quotation_id='$data[0]' and  fabric_source=1");
					foreach( $fab_description_array as $row_fab_description_array )
						{
						  $fab_description[$row_fab_description_array[csf("id")]]=	$body_part[$row_fab_description_array[csf("body_part_id")]].', '.$color_type[$row_fab_description_array[csf("color_type_id")]].', '.$row_fab_description_array[csf("construction")].', '.$row_fab_description_array[csf("composition")];
						}
					
					$data_array=sql_select("select id, quotation_id, cost_head, cons_type, req_qnty, charge_unit, amount,charge_lib_id, status_active from  wo_pri_quo_fab_conv_cost_dtls where quotation_id='$data[0]'");
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							$onclick="";
							if($conversion_from_chart==1)
							{
							$onclick="set_conversion_charge_unit_pop_up(".$i.")";
							}
							?>
                            	<tr id="conversion_1" align="center">
                                    <td>
									<? 
									echo create_drop_down( "cbocosthead_".$i, 380, $fab_description, "",1," -- All Fabrics --", $row[csf("cost_head")], "set_conversion_qnty(".$i.")",$disabled,"" ); 
									?>
                                    
                                    </td>
                                    <td><?  echo create_drop_down( "cbotypeconversion_".$i, 155, $conversion_cost_head_array,"", 1, "-- Select --", $row[csf("cons_type")], $onclick,$disabled,"" ); ?></td>
                                   <td>
                                    <input type="text" id="txtreqqnty_<? echo $i; ?>"  name="txtreqqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" onChange="calculate_conversion_cost( <? echo $i;?> )"  value="<? echo $row[csf("req_qnty")];  ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?>  />
                                    </td>
                                   <td>
                                    <input type="text" id="txtchargeunit_<? echo $i; ?>"  name="txtchargeunit_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" onChange="calculate_conversion_cost( <? echo $i;?> )"  value="<? echo $row[csf("charge_unit")];  ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?> onClick="<? if($conversion_from_chart==1){ echo "set_conversion_charge_unit_pop_up('".$i."')";}else{echo '';}?>" <? if($conversion_from_chart==1){echo "redonly";}else{echo "";}?>/>
                                    </td>
                                    <td>
                                    <input type="text" id="txtamountconversion_<? echo $i; ?>"  name="txtamountconversion_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("amount")];  ?>"  readonly/>
                                    </td>
                                    
                                    <td><? echo create_drop_down( "cbostatusconversion_".$i, 80, $row_status,"", 0, "0", $row[csf("status_active")], '',$disabled,'' );  ?></td>  
                                    <td>
                                    <input type="button" id="increaseconversion_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_conversion_cost(<? echo $i; ?>,<? echo $conversion_from_chart; ?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?>/>
                                    <input type="button" id="decreaseconversion_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_conversion_cost' );" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    <input type="hidden" id="updateidcoversion_<? echo $i; ?>" name="updateidcoversion_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("id")]; ?>"  readonly />  
                                    <input type="hidden" id="coversionchargelibraryid_<? echo $i; ?>" name="coversionchargelibraryid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("charge_lib_id")]; ?>"   readonly />                                    
                                     </td> 
                                </tr>
                            
                            <?
							 
						}
					}
					else
					{
						
					?>
                    <tr id="conversion_1" align="center">
                                   <td>
									<? 
									echo create_drop_down( "cbocosthead_1", 380, $fab_description, "",1," -- All Fabrics --", "", "set_conversion_qnty(1)","","" ); 
									?>
                                    </td>
                                    <td><?  echo create_drop_down( "cbotypeconversion_1", 155, $conversion_cost_head_array,"", 1, "-- Select --", "", $onclick,"","" ); ?></td>
                                   <td>
                                    <input type="text" id="txtreqqnty_1"  name="txtreqqnty_1" class="text_boxes_numeric" style="width:50px" onChange="calculate_conversion_cost( 1 )" value="" />
                                    </td>
                                   <td>
                                    <input type="text" id="txtchargeunit_1"  name="txtchargeunit_1" class="text_boxes_numeric" style="width:50px" onChange="calculate_conversion_cost( 1 )" value="" onClick="<? if($conversion_from_chart==1){ echo "set_conversion_charge_unit_pop_up(1)";}else{echo '';}?>" <? if($conversion_from_chart==1){echo "redonly";}else{echo "";}?> />
                                    </td>
                                    <td>
                                    <input type="text" id="txtamountconversion_1"  name="txtamountconversion_1" class="text_boxes_numeric" style="width:80px" value="" readonly />
                                    </td>
                                    
                                    <td><? echo create_drop_down( "cbostatusconversion_1", 80, $row_status,"", 0, "0", '', '','','' );  ?></td>  
                                    <td>
                                    <input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_conversion_cost(1,<? echo $conversion_from_chart; ?>)" />
                                    <input type="button" id="decreaseconversion_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1,'tbl_conversion_cost' );" />
                                    <input type="hidden" id="updateidcoversion_1" name="updateidcoversion_1"  class="text_boxes" style="width:20px" value="" readonly  />
                                    <input type="hidden" id="coversionchargelibraryid_1" name="coversionchargelibraryid_1"  class="text_boxes" style="width:20px" value="" readonly  />
                                    
                                    </td> 
                                </tr>
                    <? } ?>
                </tbody>
                </table>
                <table width="910" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                            <th width="536">Sum</th>
                            <th  width="50">
                            <input type="text" id="txtconreqnty_sum"  name="txtconreqnty_sum" class="text_boxes_numeric" style="width:50px"  readonly />
                            </th>
                            <th width="50"> 
                            <input type="text" id="txtconchargeunit_sum"  name="txtconchargeunit_sum" class="text_boxes_numeric" style="width:50px"  readonly />
                            </th>
                            <th width="80">
                             <input type="text" id="txtconamount_sum"  name="txtconamount_sum" class="text_boxes_numeric" style="width:80px"  readonly />
                            </th>
                            <th width="80"></th>
                            <th width=""></th>
                        </tr>
                    </tfoot>
                </table>
                           
                <table width="910" cellspacing="0" class="" border="0">
                
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
                        
						<?
						if ( count($data_array)>0)
					    {
						echo load_submit_buttons( $permission, "fnc_fabric_conversion_cost_dtls", 1,0,"reset_form('fabriccost_3','','',0)",5) ;
					    }
						else
						{
						echo load_submit_buttons( $permission, "fnc_fabric_conversion_cost_dtls", 0,0,"reset_form('fabriccost_3','','',0)",5) ;
						}
						?>  
                        </td> 
                    </tr>
                </table>
               
            </form>
        </fieldset>
        </div>
<?
}

if($action=="fabric_description_popup")
{
echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
extract($_REQUEST);
$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
?>
<script> 
function js_set_value(data)
{
	//alert(data)
	var data=data.split('_');
	var fabric_yarn_description=return_global_ajax_value(data[0], 'fabric_yarn_description', '', 'quotation_entry_controller');
	var fabric_yarn_description_arr=fabric_yarn_description.split("**");
	var fabric_description=trim(data[2])+' '+trim(fabric_yarn_description_arr[0]);
    document.getElementById('fab_des_id').value=data[0];
	document.getElementById('fab_nature_id').value=data[1];
	document.getElementById('construction').value=trim(data[2]);
	document.getElementById('fab_gsm').value=trim(data[3]);
	document.getElementById('process_loss').value=trim(data[4]);
	document.getElementById('fab_desctiption').value=trim(fabric_description);
	document.getElementById('composition').value=trim(fabric_yarn_description_arr[0]);
	document.getElementById('yarn_desctiption').value=trim(fabric_yarn_description_arr[1]);
    parent.emailwindow.hide();
}
function toggle( x, origColor ) 
{
	//alert(x)

			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
}
</script> 
</head>
<body>
<div align="center">
<form>
<input type="hidden" id="fab_des_id" name="fab_des_id" />
<input type="hidden" id="fab_nature_id" name="fab_nature_id" />
<input type="hidden" id="fab_desctiption" name="fab_desctiption" />
<input type="hidden" id="fab_gsm" name="fab_gsm" />
<input type="hidden" id="yarn_desctiption" name="yarn_desctiption" />
<input type="hidden" id="process_loss" name="process_loss" />
<input type="hidden" id="construction" name="construction" />
<input type="hidden" id="composition" name="composition" />
</form>

<?
	$composition_arr=array();
	$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0 order by a.id";
	$data_array=sql_select($sql);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
		}
	}
	
	//$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.fab_nature_id= '$fabric_nature' and  a.is_deleted=0 group by a.id order by a.id";
	//$arr=array (0=>$item_category, 3=>$color_range,6=>$composition_arr,8=>$lib_yarn_count,9=>$yarn_type);
	//echo  create_list_view ( "list_view", "Fab Nature,Construction,GSM/Weight,Color Range,Stich Length,Process Loss,Composition", "100,100,100,100,90,50,300","950","350",0, $sql, "js_set_value", "id,fab_nature_id,construction,gsm_weight,process_loss", "",1, "fab_nature_id,0,0,color_range_id,0,0,id", $arr , "fab_nature_id,construction,gsm_weight,color_range_id,stich_length,process_loss,id", "../merchandising_details/requires/yarn_count_determination_controller", 'setFilterGrid("list_view",-1);','0,0,1,0,1,1,0') ;
?>
<table class="rpt_table" width="950" cellspacing="0" cellpadding="0" border="0" rules="all">
<thead>
<tr>
<th width="50">SL No</th>
<th width="100">Fab Nature</th>
<th width="100">Construction</th>
<th width="100">GSM/Weight</th>
<th width="100">Color Range</th>
<th width="90">Stich Length</th>
<th width="50">Process Loss</th>
<th>Composition</th>
</tr>
</thead>
</table>
<div id="" style="max-height:350px; width:948px; overflow-y:scroll">
<table id="list_view" class="rpt_table" width="930" height="" cellspacing="0" cellpadding="0" border="1" rules="all">
<tbody>
<?
	/*$sql_data=sql_select("select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.fab_nature_id= '$fabric_nature' and  a.is_deleted=0 group by a.id order by a.id");*/
	// Oracle Compitable.
		$sql_data=sql_select("select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.fab_nature_id= '$fabric_nature' and  a.is_deleted=0 group by a.id,a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss order by a.id");

$i=1;
foreach($sql_data as $row)
{
	if ($i%2==0)  
		$bgcolor="#E9F3FF";
	else
		$bgcolor="#FFFFFF";
?>
<tr id="tr_<? echo $row[csf('id')] ?>" bgcolor="<? echo $bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('fab_nature_id')]."_".$row[csf('construction')]."_".$row[csf('gsm_weight')]."_".$row[csf('process_loss')] ?>')">
<td width="50"><? echo $i; ?></td>
<td width="100" align="left"><? echo $item_category[$row[csf('fab_nature_id')]]; ?></td>
<td width="100" align="left"><? echo $row[csf('construction')]; ?></td>
<td width="100" align="right"><? echo $row[csf('gsm_weight')]; ?></td>
<td width="100" align="left"><? echo $color_range[$row[csf('color_range_id')]]; ?></td>
<td width="90" align="right"><? echo $row[csf('stich_length')]; ?></td>
<td width="50" align="right"><? echo $row[csf('process_loss')]; ?></td>
<td><? echo $composition_arr[$row[csf('id')]]; ?></td>
</tr>

<?
$i++;
}
?>
</tbody>
</table>
<script>
setFilterGrid("list_view",-1);
 toggle( "tr_"+"<? echo $libyarncountdeterminationid; ?>", '#FFFFCC');
</script>
</div>
</div>

</body>
</html>
<?
}

if($action =="fabric_yarn_description")
{
	$fab_description="";
	$yarn_description="";
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=$data and  a.is_deleted=0 order by a.id";
	$data_array=sql_select($sql);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if($fab_description!="")
			{
				$fab_description=$fab_description." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				//".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].","
			}
			else
			{
				$fab_description=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				//.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].","
			}
			
			if($yarn_description!="")
			{
				//$yarn_description=$yarn_description."__".$lib_yarn_count[$row[csf('count_id')]]."_".$composition[$row[csf('copmposition_id')]]."_100_".$yarn_type[$row[csf('type_id')]]."_".$row[csf('percent')];
				$yarn_description=$yarn_description."__".$row[csf('count_id')]."_".$row[csf('copmposition_id')]."_100_".$row[csf('type_id')]."_".$row[csf('percent')];

			}
			else
			{
				//$yarn_description=$lib_yarn_count[$row[csf('count_id')]]."_".$composition[$row[csf('copmposition_id')]]."_100_".$yarn_type[$row[csf('type_id')]]."_".$row[csf('percent')];
				$yarn_description=$row[csf('count_id')]."_".$row[csf('copmposition_id')]."_100_".$row[csf('type_id')]."_".$row[csf('percent')];

			}
		}
	}
	echo $fab_description."**".$yarn_description;
	
}
if ($action=="consumption_popup")
{
  	echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);

?>
     
<script>
var str_gmtssizes = [<? echo substr(return_library_autocomplete( "select size_name from  lib_size", "size_name"  ), 0, -1); ?>];
var str_diawidth = [<? echo substr(return_library_autocomplete( "select color_name from lib_color", "color_name"  ), 0, -1); ?>];
function add_break_down_tr( i )
{
	var body_part_id=document.getElementById('body_part_id').value;
	var hid_fab_cons_in_quotation_variable=document.getElementById('hid_fab_cons_in_quotation_variable').value;

	var row_num=$('#tbl_consmption_cost tr').length-1;
	if (i==0)
	{
		i=1;
		 $("#gmtssizes_"+i).autocomplete({
			 source: str_gmtssizes
		  });
		   $("#diawidth_"+i).autocomplete({
			 source:  str_diawidth 
		  }); 
		  return;
	}
	
	
	if (row_num!=i)
	{
		return false;
	}
	/*if ($('#gmtssizes_'+i).val()=='' || $('#diawidth_'+i).val()=='' || $('#cons_'+i).val()=='' || $('#processloss_'+i).val()=='' || $('#requirement_'+i).val()=='' || $('#pcs_'+i).val()=='')
	{
		alert("Fill Up all field");
		return false;
	}
	
if(body_part_id==1 && hid_fab_cons_in_quotation_variable==2)
{
	if ($('#bodylength_'+i).val()=='' || $('#bodysewingmargin_'+i).val()=='' || $('#bodyhemmargin_'+i).val()=='' || $('#sleevelength_'+i).val()=='' || $('#sleevesewingmargin_'+i).val()=='' || $('#sleevehemmargin_'+i).val()=='' || $('#chestlenght_'+i).val()=='' || $('#chestsewingmargin_'+i).val()=='')
	{
		alert("Fill Up all field");
		return false;
	}
}
if(body_part_id==20 && hid_fab_cons_in_quotation_variable==2)
{
	if ($('#frontriselength_'+i).val()=='' || $('#frontrisesewingmargin_'+i).val()=='' || $('#westbandlength_'+i).val()=='' || $('#westbandsewingmargin_'+i).val()=='' || $('#inseamlength_'+i).val()=='' || $('#inseamsewingmargin_'+i).val()=='' || $('#inseamhemmargin_'+i).val()=='' || $('#halfthailength_'+i).val()=='' || $('#halfthaisewingmargin_'+i).val()=='' || $('#totalcons_'+i).val()=='')
	{
		alert("Fill Up all field");
		return false;
	}
}*/

	if (form_validation('gmtssizes_'+i+'*diawidth_'+i+'*cons_'+i+'*processloss_'+i+'*requirement_'+i+'*pcs_'+i,'Gmts Sizes*Width*Cons*Process Loss*Requirement*Pcs')==false)
	{
		//alert("Fill Up all field");
		return;
	}
	
	if(body_part_id==1 && hid_fab_cons_in_quotation_variable==2 && form_validation('bodylength_'+i+'*bodysewingmargin_'+i+'*bodyhemmargin_'+i+'*sleevelength_'+i+'*sleevesewingmargin_'+i+'*sleevehemmargin_'+i+'*chestlenght_'+i+'*chestsewingmargin_'+i,'Body Length*Body Sewing Margin*Body Hem Margin*Sleeve Length*Sleeve Sewing Margin*Sleeve Hem Margin*Chest Length*Chest Sewing Margin')==false)
	{
		//alert("Fill Up all field");
		 return;
	}
	if(body_part_id==20 && hid_fab_cons_in_quotation_variable==2 && form_validation('frontriselength_'+i+'*frontrisesewingmargin_'+i+'*westbandlength_'+i+'*westbandsewingmargin_'+i+'*inseamlength_'+i+'*inseamsewingmargin_'+i+'*inseamhemmargin_'+i+'*halfthailength_'+i+'*halfthaisewingmargin_'+i,'Front Rise Length*Front Rise Sewing Margin*West Band Length*West Band Sewing Margin*Inseam Length*Inseam Sewing Margin*Inseam Hem Margin*Half Thai Length* Half Thai Sewing Margin')==false)
	{
		   //alert("Fill Up all field");
		   return;
	}
	else
	{
		i++;
	 
		 $("#tbl_consmption_cost tr:last").clone().find("input,select,a").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }              
			});
		  }).end().appendTo("#tbl_consmption_cost");
		  
		  $('#addrow_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+")");
		  $('#decreaserow_'+i).removeAttr("onClick").attr("onClick","fn_delete_down_tr("+i+",'tbl_consmption_cost')");
		  $('#cons_'+i).removeAttr("onBlur").attr("onBlur","set_sum_value( 'cons_sum', 'cons_' )");
		  $('#cons_'+i).removeAttr("onChange").attr("onChange","set_sum_value( 'cons_sum', 'cons_' );set_sum_value( 'requirement_sum', 'requirement_' );calculate_requirement( "+i+")");
		  $('#diawidth_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
		  $('#processloss_'+i).removeAttr("onBlur").attr("onBlur","set_sum_value( 'processloss_sum', 'processloss_' )");
		  $('#processloss_'+i).removeAttr("onChange").attr("onChange","calculate_requirement( "+i+");set_sum_value( 'processloss_sum', 'processloss_' );set_sum_value( 'requirement_sum', 'requirement_')");
          $('#requirement_'+i).removeAttr("onBlur").attr("onBlur","set_sum_value( 'requirement_sum', 'requirement_')");
		  $('#requirement_'+i).removeAttr("onChange").attr("onChange","calculate_requirement( "+i+");set_sum_value( 'requirement_sum', 'requirement_')");
		  $('#pcs_'+i).removeAttr("onBlur").attr("onBlur","set_sum_value( 'pcs_sum', 'pcs_')");

		  var j=i-1;
		  $('#gmtssizes_'+i).val(''); 
		  $('#diawidth_'+i).val($('#diawidth_'+j).val());
		  //$('#msmnt_'+i).val($('#msmnt_'+j).val());
		  if(hid_fab_cons_in_quotation_variable==3 )
		  {
		  $('#cons_'+i).val($('#cons_'+j).val());
		  $('#requirement_'+i).val($('#requirement_'+j).val());
		  }
		  else
		  {
		  $('#cons_'+i).val('');
		  $('#requirement_'+i).val('');  
		  }
		  
		  $('#processloss_'+i).val($('#processloss_'+j).val());
		  
		  $('#pcs_'+i).val($('#pcs_'+j).val());
		  $('#updateidcb_'+i).val('');

		  //-----------------------
		  
		  $("#tbl_msmnt_cost tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }              
			});
		  }).end().appendTo("#tbl_msmnt_cost");
		  if(body_part_id==1)
		  {
			  $('#bodylength_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#bodysewingmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#bodyhemmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#sleevelength_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#sleevesewingmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#sleevehemmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#chestlenght_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#chestsewingmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			   
			  $('#bodylength_'+i).val(''); 
			  $('#bodysewingmargin_'+i).val($('#bodysewingmargin_'+j).val());
			  $('#bodyhemmargin_'+i).val($('#bodyhemmargin_'+j).val());
			  $('#sleevelength_'+i).val('');
			  $('#sleevesewingmargin_'+i).val($('#sleevesewingmargin_'+j).val());
			  $('#sleevehemmargin_'+i).val($('#sleevehemmargin_'+j).val());
			  $('#chestlenght_'+i).val('');
			  $('#chestsewingmargin_'+i).val($('#chestsewingmargin_'+j).val());
		  }
		  if(body_part_id==20)
		  {
			  $('#frontriselength_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#frontrisesewingmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#westbandlength_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#westbandsewingmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#inseamlength_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#inseamsewingmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#inseamhemmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#halfthailength_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#halfthaisewingmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  
			  $('#frontriselength_'+i).val($('#frontriselength_'+j).val()); 
			  $('#frontrisesewingmargin_'+i).val($('#frontrisesewingmargin_'+j).val());
			  $('#westbandlength_'+i).val($('#westbandlength_'+j).val());
			  $('#westbandsewingmargin_'+i).val($('#westbandsewingmargin_'+j).val());
			  $('#inseamlength_'+i).val($('#inseamlength_'+j).val());
			  $('#inseamsewingmargin_'+i).val($('#inseamsewingmargin_'+j).val());
			  $('#inseamhemmargin_'+i).val($('#inseamhemmargin_'+j).val());
			  $('#halfthailength_'+i).val($('#halfthailength_'+j).val());
			  $('#halfthaisewingmargin_'+i).val($('#halfthaisewingmargin_'+j).val());
		  }
		  //------------------
		  set_all_onclick();
		  set_sum_value( 'cons_sum', 'cons_'  );
		  set_sum_value( 'processloss_sum', 'processloss_'  );
		  set_sum_value( 'requirement_sum', 'requirement_');
          set_sum_value( 'pcs_sum', 'pcs_');
		  
		  $("#gmtssizes_"+i).autocomplete({
			 source: str_gmtssizes
		  });
		   $("#diawidth_"+i).autocomplete({
			 source:  str_diawidth 
		  });  
	}
}


function fn_delete_down_tr(rowNo,table_id) 
{   
	
	if(table_id=='tbl_consmption_cost')
	{
		var numRow = $('table#tbl_consmption_cost tbody tr').length; 
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_consmption_cost tbody tr:last').remove();
			$('#tbl_msmnt_cost tbody tr:last').remove();

		}
		/*else
		{
																																																																																																																																																																																																																																																																																																																																									reset_form('','','txtordernumber_'+rowNo+'*txtorderqnty_'+rowNo+'*txtordervalue_'+rowNo+'*txtattachedqnty_'+rowNo+'*txtattachedvalue_'+rowNo+'*txtstyleref_'+rowNo+'*txtitemname_'+rowNo+'*txtjobno_'+rowNo+'*hiddenwopobreakdownid_'+rowNo+'*hiddenunitprice_'+rowNo+'*totalOrderqnty*totalOrdervalue*totalAttachedqnty*totalAttachedvalue');
		} */
		 //set_all_onclick();
		  set_sum_value( 'cons_sum', 'cons_'  );
		  set_sum_value( 'processloss_sum', 'processloss_'  );
		  set_sum_value( 'requirement_sum', 'requirement_');
          set_sum_value( 'pcs_sum', 'pcs_');
	}
	
	
}

function set_sum_value(des_fil_id,field_id)
{
	if(des_fil_id=='cons_sum')
	{
	var ddd={dec_type:6,comma:0,currency:1};
	}
	if(des_fil_id=='processloss_sum')
	{
	var ddd={dec_type:1,comma:0,currency:1};
	}
	
	if(des_fil_id=='requirement_sum')
	{
	var ddd={dec_type:1,comma:0,currency:1};
	}
	
	if(des_fil_id=='pcs_sum')
	{
	var ddd={dec_type:6,comma:0};
	}
	var rowCount = $('#tbl_consmption_cost tr').length-1;
	math_operation( des_fil_id, field_id, '+', rowCount,ddd);
	//document.getElementById('txt_fabric_pre_cost').value=document.getElementById('txtamount_sum').value;
	/*document.getElementById('calculated_cons').value=(document.getElementById('requirement_sum').value*1)/rowCount;
	document.getElementById('avg_cons').value=(document.getElementById('cons_sum').value*1)/rowCount;
	document.getElementById('calculated_procloss').value=(document.getElementById('processloss_sum').value*1)/rowCount;
    document.getElementById('calculated_pcs').value=(document.getElementById('pcs_sum').value*1)/rowCount;*/
	claculate_avg()
}
	
function js_set_value()
{
	var body_part_id=document.getElementById('body_part_id').value;
	var hid_fab_cons_in_quotation_variable=document.getElementById('hid_fab_cons_in_quotation_variable').value;
	var rowCount = $('#tbl_consmption_cost tr').length-1;
	var cons_breck_down="";
	var msmnt_breack_down="";
	var marker_breack_down="";
	if(hid_fab_cons_in_quotation_variable==3)
	{
		if(form_validation('txt_marker_dia*txt_marker_yds*txt_marker_inch*txt_gmt_pcs*txt_marker_length_yds*txt_marker_gsm*txt_marker_net_fab_cons','Marker Dia*Marker Yds*Marker Inch*Gmt Pcs*Marker Length*Marker Gsm*Marker Net Fabric')==false)
		{
			return;
		}
		else
		{
			var txt_marker_dia=$('#txt_marker_dia').val();
			var txt_marker_yds=$('#txt_marker_yds').val();
			var txt_marker_inch=$('#txt_marker_inch').val();
			var txt_gmt_pcs=$('#txt_gmt_pcs').val();
			var txt_marker_length_yds=$('#txt_marker_length_yds').val();
			var txt_marker_net_fab_cons=$('#txt_marker_net_fab_cons').val();
			marker_breack_down+=txt_marker_dia+'_'+txt_marker_yds+'_'+txt_marker_inch+'_'+txt_gmt_pcs+'_'+txt_marker_length_yds+'_'+txt_marker_net_fab_cons;
		}
	}
	for(var i=1; i<=rowCount; i++)
	{
		
		if (form_validation('gmtssizes_'+i+'*diawidth_'+i+'*cons_'+i+'*processloss_'+i+'*requirement_'+i+'*pcs_'+i,'Gmts Sizes*Width*Cons*Process Loss*Requirement*Pcs')==false)
			{
				//alert("Fill Up all field");
				return;
			}
			
			if(body_part_id==1 && hid_fab_cons_in_quotation_variable==2 && form_validation('bodylength_'+i+'*bodysewingmargin_'+i+'*bodyhemmargin_'+i+'*sleevelength_'+i+'*sleevesewingmargin_'+i+'*sleevehemmargin_'+i+'*chestlenght_'+i+'*chestsewingmargin_'+i,'Body Length*Body Sewing Margin*Body Hem Margin*Sleeve Length*Sleeve Sewing Margin*Sleeve Hem Margin*Chest Length*Chest Sewing Margin')==false)
		    {
				//alert("Fill Up all field");
				return;
		     }			 
			 if(body_part_id==20 && hid_fab_cons_in_quotation_variable==2 && form_validation('frontriselength_'+i+'*frontrisesewingmargin_'+i+'*westbandlength_'+i+'*westbandsewingmargin_'+i+'*inseamlength_'+i+'*inseamsewingmargin_'+i+'*inseamhemmargin_'+i+'*halfthailength_'+i+'*halfthaisewingmargin_'+i,'Front Rise Length*Front Rise Sewing Margin*West Band Length*West Band Sewing Margin*Inseam Length*Inseam Sewing Margin*Inseam Hem Margin*Half Thai Length* Half Thai Sewing Margin')==false )
		    {
				///alert("Fill Up all field");
				 return;
		    }
		
		if(cons_breck_down=="")
		{
			cons_breck_down+=$('#gmtssizes_'+i).val()+'_'+$('#diawidth_'+i).val()+'_'+$('#cons_'+i).val()+'_'+$('#processloss_'+i).val()+'_'+$('#requirement_'+i).val()+'_'+$('#pcs_'+i).val();
		  
		}
		else
		{
			cons_breck_down+="__"+$('#gmtssizes_'+i).val()+'_'+$('#diawidth_'+i).val()+'_'+$('#cons_'+i).val()+'_'+$('#processloss_'+i).val()+'_'+$('#requirement_'+i).val()+'_'+$('#pcs_'+i).val();

		}
		
		
		
		if(hid_fab_cons_in_quotation_variable==2)
		{
			if(msmnt_breack_down=="")
			{
				if(body_part_id==1)
				{
				msmnt_breack_down+=$('#bodylength_'+i).val()+'_'+$('#bodysewingmargin_'+i).val()+'_'+$('#bodyhemmargin_'+i).val()+'_'+$('#sleevelength_'+i).val()+'_'+$('#sleevesewingmargin_'+i).val()+'_'+$('#sleevehemmargin_'+i).val()+'_'+$('#chestlenght_'+i).val()+'_'+$('#chestsewingmargin_'+i).val()+'_'+$('#totalcons_'+i).val();
				}
				if(body_part_id==20)
				{
				msmnt_breack_down+=$('#frontriselength_'+i).val()+'_'+$('#frontrisesewingmargin_'+i).val()+'_'+$('#westbandlength_'+i).val()+'_'+$('#westbandsewingmargin_'+i).val()+'_'+$('#inseamlength_'+i).val()+'_'+$('#inseamsewingmargin_'+i).val()+'_'+$('#inseamhemmargin_'+i).val()+'_'+$('#halfthailength_'+i).val()+'_'+$('#halfthaisewingmargin_'+i).val()+'_'+$('#totalcons_'+i).val();
				}
			}
			else
			{
				if(body_part_id==1)
				{
				msmnt_breack_down+="__"+$('#bodylength_'+i).val()+'_'+$('#bodysewingmargin_'+i).val()+'_'+$('#bodyhemmargin_'+i).val()+'_'+$('#sleevelength_'+i).val()+'_'+$('#sleevesewingmargin_'+i).val()+'_'+$('#sleevehemmargin_'+i).val()+'_'+$('#chestlenght_'+i).val()+'_'+$('#chestsewingmargin_'+i).val()+'_'+$('#totalcons_'+i).val();
				}
				if(body_part_id==20)
				{
				msmnt_breack_down+="__"+$('#frontriselength_'+i).val()+'_'+$('#frontrisesewingmargin_'+i).val()+'_'+$('#westbandlength_'+i).val()+'_'+$('#westbandsewingmargin_'+i).val()+'_'+$('#inseamlength_'+i).val()+'_'+$('#inseamsewingmargin_'+i).val()+'_'+$('#inseamhemmargin_'+i).val()+'_'+$('#halfthailength_'+i).val()+'_'+$('#halfthaisewingmargin_'+i).val()+'_'+$('#totalcons_'+i).val();
				}
			}
		}
	}
	//alert(cons_breck_down);
	//alert(msmnt_breack_down);
	document.getElementById('cons_breck_down').value=cons_breck_down;
	document.getElementById('msmnt_breack_down').value=msmnt_breack_down;
	document.getElementById('marker_breack_down').value=marker_breack_down;
	claculate_avg()

	/*document.getElementById('calculated_cons').value=(document.getElementById('requirement_sum').value*1)/rowCount;
	document.getElementById('avg_cons').value=(document.getElementById('cons_sum').value*1)/rowCount;
	document.getElementById('calculated_procloss').value=(document.getElementById('processloss_sum').value*1)/rowCount;
    document.getElementById('calculated_pcs').value=(document.getElementById('pcs_sum').value*1)/rowCount;*/

	parent.emailwindow.hide();
}

function calculate_measurement_top(i)
{
	
	var body_part_id=document.getElementById('body_part_id').value;
	var cbofabricnature_id=document.getElementById('cbofabricnature_id').value;
	var cbo_costing_per_id=document.getElementById('cbo_costing_per_id').value;
	var hid_fab_cons_in_quotation_variable=document.getElementById('hid_fab_cons_in_quotation_variable').value;
	if(hid_fab_cons_in_quotation_variable==2)
	{
	
	if (cbo_costing_per_id==1) // knit type
	{
		var dzn_mult=1*12;
	}
	else if (cbo_costing_per_id==2) // knit type
	{
		var dzn_mult=1*1;
	}
	else if (cbo_costing_per_id==3) // knit type
	{
		var dzn_mult=2*12;
	}
	else if (cbo_costing_per_id==4) // knit type
	{
		var dzn_mult=3*12;
	}
	else if (cbo_costing_per_id==5) // knit type
	{
		var dzn_mult=4*12;
	}
	else
	{
		dzn_mult=0;
	}
	
//------------------------------------Knit------------------------------------
	if(cbofabricnature_id==2)//Knit
	{
	var txt_required_gsm_top=document.getElementById('txt_gsm').value;
//alert(txt_required_gsm_top);
		if (body_part_id==1)//main fabric top 
		{
			var txt_body_length_measurement_top=0;
			var txt_body_length_sewing_top=0;
			var txt_body_length_hem_top=0;
			var txt_sleeve_length_measurement_top=0;
			var txt_sleeve_length_sewing_top=0;
			var txt_sleeve_length_hem_top=0;
			var txt_chest_measurement_top=0;
			var txt_chest_sew_top=0;
			txt_body_length_measurement_top=document.getElementById('bodylength_'+i).value;
			txt_body_length_sewing_top=document.getElementById('bodysewingmargin_'+i).value;
			txt_body_length_hem_top=document.getElementById('bodyhemmargin_'+i).value;
			txt_sleeve_length_measurement_top=document.getElementById('sleevelength_'+i).value;
			txt_sleeve_length_sewing_top=document.getElementById('sleevesewingmargin_'+i).value;
			txt_sleeve_length_hem_top=document.getElementById('sleevehemmargin_'+i).value;
			txt_chest_measurement_top=document.getElementById('chestlenght_'+i).value;
			txt_chest_sew_top=document.getElementById('chestsewingmargin_'+i).value;
			
			//[{(Body Lentg +Sleeve Lenth + Sewing Margin + Hem) x (Half Chest + Sewing Margin)} x 2] x 12 x GSM / 10000000
			
            var dbl_total=(((txt_body_length_measurement_top*1)+(txt_body_length_sewing_top*1)+(txt_body_length_hem_top*1)+(txt_sleeve_length_measurement_top*1)+(txt_sleeve_length_sewing_top*1)+(txt_sleeve_length_hem_top*1))*((txt_chest_measurement_top*1)+(txt_chest_sew_top*1))*2*(dzn_mult*1)*(txt_required_gsm_top*1))/10000000;	
			
		}
		if (body_part_id==20)//main fabric bottom
		{
			var txt_front_rise_measurement_bottom=0;
			var txt_front_rise_sewing_bottom=0;
			var txt_west_band_measurement_bottom=0;
			var txt_west_band_sewing_bottom=0;
			var txt_in_seam_measurement_bottom=0;
			var txt_in_seam_sew_bottom=0;
			var txt_in_seam_hem_bottom=0;
			var txt_half_thai_measurement_bottom=0;
			var txt_half_thai_sew_bottom=0;
			
			var txt_front_rise_measurement_bottom=document.getElementById('frontriselength_'+i).value;
			var txt_front_rise_sewing_bottom=document.getElementById('frontrisesewingmargin_'+i).value;
			var txt_west_band_measurement_bottom=document.getElementById('westbandlength_'+i).value;
			var txt_west_band_sewing_bottom=document.getElementById('westbandsewingmargin_'+i).value;
			var txt_in_seam_measurement_bottom=document.getElementById('inseamlength_'+i).value;
			var txt_in_seam_sew_bottom=document.getElementById('inseamsewingmargin_'+i).value;
			var txt_in_seam_hem_bottom=document.getElementById('inseamhemmargin_'+i).value;
			var txt_half_thai_measurement_bottom=document.getElementById('halfthailength_'+i).value;
 			var txt_half_thai_sew_bottom=document.getElementById('halfthaisewingmargin_'+i).value;
			
			
			//[{(Front Rise + In Seam + West Band + Sewing Margin + Hem) x (Half Thai + Sewing Margin)} x 4] x 12  x GSM / 10000000
		// var sum=((FrontRiseBottom+FrontRiseSewBottom+WestBandBottom+WestBandSewBottom+InSeamBottom+InSeamSewingBottom+InSeamHemBottom)*(ThighMeasurementBottom+ThighSewBottom)*4*12*RequiredWeightBottom)/10000000;
			
			var dbl_total=(((txt_front_rise_measurement_bottom*1)+(txt_front_rise_sewing_bottom*1)+(txt_west_band_measurement_bottom*1)+(txt_west_band_sewing_bottom*1)+(txt_in_seam_measurement_bottom*1)+(txt_in_seam_sew_bottom*1)+(txt_in_seam_hem_bottom*1))*((txt_half_thai_measurement_bottom*1)+(txt_half_thai_sew_bottom*1))*4*(dzn_mult*1)*(txt_required_gsm_top*1))/10000000;
			
		}
	}
//------------------------------------End Knit------------------------------------
//----------------------------------- Woven---------------------------------------
	if(cbofabricnature_id==3)//woven
	{
	 var txt_required_weight_top=document.getElementById('diawidth_'+i).value;
		if (body_part_id==1)//main fabric top 
		{
			//alert('www');
			var txt_body_length_measurement_top=0;
			var txt_body_length_sewing_top=0;
			var txt_body_length_hem_top=0;
			var txt_sleeve_length_measurement_top=0;
			var txt_sleeve_length_sewing_top=0;
			var txt_sleeve_length_hem_top=0;
			var txt_chest_measurement_top=0;
			var txt_chest_sew_top=0;
			txt_body_length_measurement_top=document.getElementById('bodylength_'+i).value;
			txt_body_length_sewing_top=document.getElementById('bodysewingmargin_'+i).value;
			txt_body_length_hem_top=document.getElementById('bodyhemmargin_'+i).value;
			txt_sleeve_length_measurement_top=document.getElementById('sleevelength_'+i).value;
			txt_sleeve_length_sewing_top=document.getElementById('sleevesewingmargin_'+i).value;
			txt_sleeve_length_hem_top=document.getElementById('sleevehemmargin_'+i).value;
			txt_chest_measurement_top=document.getElementById('chestlenght_'+i).value;
			txt_chest_sew_top=document.getElementById('chestsewingmargin_'+i).value;
			
			
			//[{(Body Lentg +Sleeve Lenth + Sewing Margin + Hem) x (Half Chest + Sewing Margin)} x 2] x 12 / (Width x 36)
			var dbl_total=(((txt_body_length_measurement_top*1)+(txt_body_length_sewing_top*1)+(txt_body_length_hem_top*1)+(txt_sleeve_length_measurement_top*1)+(txt_sleeve_length_sewing_top*1)+(txt_sleeve_length_hem_top*1))*((txt_chest_measurement_top*1)+(txt_chest_sew_top*1))*2*(dzn_mult*1))/((txt_required_weight_top*1)*36);
			
		}
		if (body_part_id==20)//main fabric bottom 
		{
			var txt_front_rise_measurement_bottom=0;
			var txt_front_rise_sewing_bottom=0;
			var txt_west_band_measurement_bottom=0;
			var txt_west_band_sewing_bottom=0;
			var txt_in_seam_measurement_bottom=0;
			var txt_in_seam_sew_bottom=0;
			var txt_in_seam_hem_bottom=0;
			var txt_half_thai_measurement_bottom=0;
			var txt_half_thai_sew_bottom=0;
			
			var txt_front_rise_measurement_bottom=document.getElementById('frontriselength_'+i).value;
			var txt_front_rise_sewing_bottom=document.getElementById('frontrisesewingmargin_'+i).value;
			var txt_west_band_measurement_bottom=document.getElementById('westbandlength_'+i).value;
			var txt_west_band_sewing_bottom=document.getElementById('westbandsewingmargin_'+i).value;
			var txt_in_seam_measurement_bottom=document.getElementById('inseamlength_'+i).value;
			var txt_in_seam_sew_bottom=document.getElementById('inseamsewingmargin_'+i).value;
			var txt_in_seam_hem_bottom=document.getElementById('inseamhemmargin_'+i).value;
			var txt_half_thai_measurement_bottom=document.getElementById('halfthailength_'+i).value;
 			var txt_half_thai_sew_bottom=document.getElementById('halfthaisewingmargin_'+i).value;
			
			//[{(Front Rise + In Seam + West Band + Sewing Margin + Hem) x (Half Thai + Sewing Margin)} x 4] x 12  / Width x 36
			var dbl_total=(((txt_front_rise_measurement_bottom*1)+(txt_front_rise_sewing_bottom*1)+(txt_west_band_measurement_bottom*1)+(txt_west_band_sewing_bottom*1)+(txt_in_seam_measurement_bottom*1)+(txt_in_seam_sew_bottom*1)+(txt_in_seam_hem_bottom*1))*((txt_half_thai_measurement_bottom*1)+(txt_half_thai_sew_bottom*1))*4*(dzn_mult*1))/((txt_required_weight_top*1)*36);
			
		}
	}
	//----------------------------------- End Woven---------------------------------------
    dbl_total= number_format_common( dbl_total, 1, 0) ;	
	document.getElementById('totalcons_'+i).value=dbl_total;
	document.getElementById('cons_'+i).value=dbl_total;
	//calculate_total_consumption_top();
	//find_total_finish_fab_consumption_top();
	//find_total_dying_wastage_top();
	//find_total_yarn_needed_top();
	set_sum_value( 'cons_sum', 'cons_'  );
	set_sum_value( 'requirement_sum', 'requirement_' )
	calculate_requirement(i)
	claculate_avg()
	}

	/*var rowCount = $('#tbl_consmption_cost tr').length-1;
	document.getElementById('calculated_cons').value=(document.getElementById('requirement_sum').value*1)/rowCount;
	document.getElementById('avg_cons').value=(document.getElementById('cons_sum').value*1)/rowCount;
	document.getElementById('calculated_procloss').value=(document.getElementById('processloss_sum').value*1)/rowCount;
    document.getElementById('calculated_pcs').value=(document.getElementById('pcs_sum').value*1)/rowCount;*/
}
function calculate_requirement(i)
{
	var process_loss_method_id=document.getElementById('process_loss_method_id').value;
	var cons=(document.getElementById('cons_'+i).value)*1;
	var processloss=(document.getElementById('processloss_'+i).value)*1;
	    var WastageQty='';
		if(process_loss_method_id==1)
		{
			WastageQty=cons+cons*(processloss/100);
		}
		else if(process_loss_method_id==2)
		{
			var devided_val = 1-(processloss/100);
			var WastageQty=parseFloat(cons/devided_val);
		}
		else
		{
			WastageQty=0;
		}
		document.getElementById('requirement_'+i).value= number_format_common(WastageQty, 1, 0);
		set_sum_value( 'requirement_sum', 'requirement_' )
		claculate_avg()
	/*var rowCount = $('#tbl_consmption_cost tr').length-1;
	document.getElementById('calculated_cons').value=(document.getElementById('requirement_sum').value*1)/rowCount;
	document.getElementById('avg_cons').value=(document.getElementById('cons_sum').value*1)/rowCount;
	document.getElementById('calculated_procloss').value=(document.getElementById('processloss_sum').value*1)/rowCount;
    document.getElementById('calculated_pcs').value=(document.getElementById('pcs_sum').value*1)/rowCount;*/
}
function claculate_avg()
{
	var rowCount = $('#tbl_consmption_cost tr').length-1;
	
	var calculated_cons=(document.getElementById('requirement_sum').value*1)/rowCount;
	var avg_cons=(document.getElementById('cons_sum').value*1)/rowCount;
	var calculated_procloss=(document.getElementById('processloss_sum').value*1)/rowCount;
    var calculated_pcs=(document.getElementById('pcs_sum').value*1)/rowCount;
	
	document.getElementById('calculated_cons').value=number_format_common(calculated_cons, 1, 0);
	document.getElementById('avg_cons').value=number_format_common(avg_cons, 1, 0);
	document.getElementById('calculated_procloss').value=number_format_common(calculated_procloss, 1, 0);
    document.getElementById('calculated_pcs').value=calculated_pcs;
}

function calculate_marker_length()
{
	var cbo_costing_per_id=document.getElementById('cbo_costing_per_id').value;
	if (cbo_costing_per_id==1) // knit type
	{
		var dzn_mult=1*12;
	}
	else if (cbo_costing_per_id==2) // knit type
	{
		var dzn_mult=1*1;
	}
	else if (cbo_costing_per_id==3) // knit type
	{
		var dzn_mult=2*12;
	}
	else if (cbo_costing_per_id==4) // knit type
	{
		var dzn_mult=3*12;
	}
	else if (cbo_costing_per_id==5) // knit type
	{
		var dzn_mult=4*12;
	}
	else
	{
		dzn_mult=0;
	}
	var txt_marker_yds= (document.getElementById('txt_marker_yds').value)*1;
	var txt_marker_inch= (document.getElementById('txt_marker_inch').value)*1;
	var txt_gmt_pcs= (document.getElementById('txt_gmt_pcs').value)*1;
	var txt_marker_dia= (document.getElementById('txt_marker_dia').value)*1;
	var txt_marker_gsm= (document.getElementById('txt_marker_gsm').value)*1;

	//if(txt_marker_yds !="" && txt_marker_inch !="" && txt_gmt_pcs !="")
	//{
		//alert(txt_marker_inch);
		//alert(txt_gmt_pcs);
		//alert(dzn_mult);
		var txt_marker_length_yds=(txt_marker_inch/36)+txt_marker_yds;
		//alert(txt_marker_length_yds);
		var txt_marker_length_yds2=(txt_marker_length_yds/txt_gmt_pcs)*dzn_mult;
		txt_marker_length_yds3= number_format_common( txt_marker_length_yds2, 1, 0) ;	
        document.getElementById('txt_marker_length_yds').value=txt_marker_length_yds3;
		var txt_marker_net_fab_cons=((txt_marker_length_yds3*36*2.54)/dzn_mult)*(txt_marker_dia*2*2.54*dzn_mult*txt_marker_gsm);
		var txt_marker_net_fab_cons2=txt_marker_net_fab_cons/10000000;
		document.getElementById('txt_marker_net_fab_cons').value=number_format_common(txt_marker_net_fab_cons2,1,0);
		document.getElementById('cons_1').value=number_format_common(txt_marker_net_fab_cons2,1,0);
		document.getElementById('requirement_1').value=number_format_common(txt_marker_net_fab_cons2,1,0);
		 set_sum_value( 'cons_sum', 'cons_'  );
		 set_sum_value( 'requirement_sum', 'requirement_');
		//copy_value(number_format_common(txt_marker_net_fab_cons2,1,0),'cons_',1)

	//}
}
</script>
</head>
<body>
<div align="center" style="width:100%;" >
<fieldset>
            <legend><? echo $body_part_id.'.'.$body_part[$body_part_id].'   Costing '.$costing_per[$cbo_costing_per] ;?></legend>
        	<form id="consumptionform_1" autocomplete="off">
            <input type="hidden" id="cbo_company_id" name="cbo_company_id" value="<? echo $cbo_company_id; ?>"/> 
            <input type="hidden" id="cbo_costing_per_id" name="cbo_costing_per_id" value="<? echo $cbo_costing_per; ?>"/>
            <input type="hidden" id="hid_fab_cons_in_quotation_variable" name="hid_fab_cons_in_quotation_variable" value="<? echo $hid_fab_cons_in_quotation_variable; ?>" width="500" /> 
            <input type="hidden" id="body_part_id" name="body_part_id" value="<? echo $body_part_id; ?>"/>
            <input type="hidden" id="cbofabricnature_id" name="cbofabricnature_id" value="<? echo $cbofabricnature_id; ?>"/> 
            <input type="hidden" id="cons_breck_down" name="cons_breck_down"  width="500"  value="<? echo $cons_breck_downn;?>"/> 
            <input type="hidden" id="marker_breack_down" name="marker_breack_down"  value="<? echo $marker_breack_down;?>"/>
            <input type="hidden" id="msmnt_breack_down" name="msmnt_breack_down"  value="<? echo $msmnt_breack_downn;?>"/>
            <input type="hidden" id="txt_gsm" name="txt_gsm" value="<? echo $txtgsmweight; ?>"/>
			<?
			if($cbo_costing_per==1)
			{
				$pcs_value=1*12;
			}
			if($cbo_costing_per==2)
			{
				$pcs_value=1*1;
			}
			if($cbo_costing_per==3)
			{
				$pcs_value=2*12;
			}
			if($cbo_costing_per==4)
			{
				$pcs_value=3*12;
			}
			if($cbo_costing_per==5)
			{
				$pcs_value=4*12;
			}
			$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_id  and variable_list=18 and item_category_id=$cbofabricnature_id and status_active=1 and is_deleted=0");
            ?>
           <input type="hidden" id="process_loss_method_id" name="process_loss_method_id" value="<? echo $process_loss_method; ?>"/>
           <?
		   if($hid_fab_cons_in_quotation_variable==3){
		   ?>
          <table width="800" cellspacing="0" class="rpt_table" border="0" id="tbl_marker_cost" rules="all">
              <thead>
                    <tr>
                        <th  width="100"  rowspan="2">Marker Dia (Inch)</th><th  width="100" colspan="2">Marker Length</th><th  width="110"  rowspan="2">Gmts. Size Ratio (Pcs)</th><th width="90"  rowspan="2">Marker Length -Yds (1Dzn Gmts)</th><th width="110"  rowspan="2">GSM</th><th width="110"  rowspan="2">Net Fab Cons</th><th></th>
                        
                    </tr>
                    <tr>
                        <th  width="100">Yds</th><th  width="100">Inch</th><th></th>
                        
                    </tr>
              </thead>
              <tbody>
              <?
			  $marker_breack_down_arr=explode("_",$marker_breack_down);
			  ?>
              <tr>
              <td><input type="text" id="txt_marker_dia"  name="txt_marker_dia" class="text_boxes_numeric" style="width:90px" onChange="calculate_marker_length()"  value="<? echo $marker_breack_down_arr[0];  ?>"> </td>
              <td><input type="text" id="txt_marker_yds"  name="txt_marker_yds" class="text_boxes_numeric" style="width:90px"  onChange="calculate_marker_length()" value="<? echo $marker_breack_down_arr[1];  ?>"></td>
              <td><input type="text" id="txt_marker_inch"  name="txt_marker_inch" class="text_boxes_numeric" style="width:90px" onChange="calculate_marker_length()"  value="<? echo $marker_breack_down_arr[2];  ?>"></td>
              <td><input type="text" id="txt_gmt_pcs"  name="txt_gmt_pcs" class="text_boxes_numeric" style="width:110px" onChange="calculate_marker_length()"  value="<? echo $marker_breack_down_arr[3];  ?>"></td>
              <td><input type="text" id="txt_marker_length_yds"  name="txt_marker_length_yds" class="text_boxes_numeric" style="width:110px" readonly  value="<? echo $marker_breack_down_arr[4];  ?>"></td>
              <td><input type="text" id="txt_marker_gsm"  name="txt_marker_gsm" class="text_boxes_numeric" readonly style="width:110px"  value="<? echo $txtgsmweight; ?>"></td>
              <td><input type="text" id="txt_marker_net_fab_cons"  name="txt_marker_net_fab_cons" class="text_boxes_numeric" style="width:110px"  value="<? echo $marker_breack_down_arr[5];  ?>"></td>
              <td></td>
              </tr>
              </tbody>
          </table>
           <?
		   }
		   ?>
<br/>

            	<table width="810" cellspacing="0" class="rpt_table" border="0" id="tbl_consmption_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="50">SL</th><th  width="100">Gmts sizes</th><th  width="110"><? if($cbofabricnature_id==2){echo "Dia"; }else{ echo "Width";}?></th><th width="110">Cons<? if($cbofabricnature_id==2){echo ""; }else{ echo "/Yds";}?></th><th width="110">Process Loss %</th><th width="105">Requirment</th><th width="90">Pcs</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					
					//$data_array=sql_select("select id,wo_pri_quo_fab_co_dtls_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs from wo_pri_quo_fab_co_avg_con_dtls where wo_pri_quo_fab_co_dtls_id='$updateid_fc'");
					//id,wo_pri_quo_fab_co_dtls_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs
					$data_array=explode("__",$cons_breck_downn);
					if($data_array[0]=="")
					{
						$data_array=array();
					}
					//print_r($data_array);
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$data=explode('_',$row);
							$i++;
							?>
                            	<tr id="break_1" align="center">
                                    <td>
                                      <? echo $i;?>
                                    </td>
                                    <td>
                                    <input type="text" id="gmtssizes_<? echo $i;?>"  name="gmtssizes_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $data[0]; ?>">
                                    </td>
                                    <td>
                                    <input type="text" id="diawidth_<? echo $i;?>"    name="diawidth_<? echo $i;?>"  class=" <? if($cbofabricnature_id==2){echo "text_boxes"; }else{ echo "text_boxes_numeric";}?>" style="width:95px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[1]; ?>">    
                                    </td>
                                    <td>
                                    <input type="text" id="cons_<? echo $i;?>" onBlur="set_sum_value( 'cons_sum', 'cons_' )" onChange="set_sum_value( 'cons_sum', 'cons_' );set_sum_value( 'requirement_sum', 'requirement_' );calculate_requirement(<? echo $i;?>)"  name="cons_<? echo $i;?>" class="text_boxes_numeric" style="width:95px" <? if($hid_fab_cons_in_quotation_variable==2 && ($body_part_id==1 || $body_part_id==20)){ echo "readonly";} else{ echo "";} ?> value="<? echo $data[2]; ?>" /> 
                                    </td>
                                    <td>
                                    <input type="text" id="processloss_<? echo $i;?>" onBlur="set_sum_value( 'processloss_sum', 'processloss_' ) "  name="processloss_<? echo $i;?>" class="text_boxes_numeric" style="width:95px" onChange="calculate_requirement(<? echo $i;?>);set_sum_value( 'processloss_sum', 'processloss_' );set_sum_value( 'requirement_sum', 'requirement_' )" value="<? echo $data[3]; ?>" /> 
                                    </td>
                                    <td>
                                    <input type="text" id="requirement_<? echo $i;?>" onBlur="set_sum_value( 'requirement_sum', 'requirement_' ) "  onChange="set_sum_value( 'requirement_sum', 'requirement_' )"  name="requirement_<? echo $i;?>" class="text_boxes_numeric" style="width:90px" readonly value="<? echo $data[4]; ?>"> 
                                    </td>
                                    <td>
                                    <input type="text" id="pcs_<? echo $i;?>"  name="pcs_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) "  class="text_boxes_numeric" style="width:75px" value="<? echo $data[5]; ?>">
                                    </td>
                                     <td>
                                     <input type="button" id="addrow_<? echo $i;?>"  name="addrow_<? echo $i;?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i;?> )" />
                                    <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(<? echo $i;?> ,'tbl_consmption_cost' );" />
                                     
                                    

                                     </td>
                                </tr>
                            
                            <?
							 
						}
						
					}
					else
					{
					?>
                    <tr id="break_1" align="center">
                                    <td>
                                      <? echo $i;?>
                                    </td>
                                     <td>
                                    <input type="text" id="gmtssizes_1"  name="gmtssizes_1" class="text_boxes" style="width:85px"  />
                                    </td>
                                    <td>
                                    <input type="text" id="diawidth_1"  value=""  name="diawidth_1"  class=" <? if($cbofabricnature_id==2){echo "text_boxes"; }else{ echo "text_boxes_numeric";}?>" style="width:95px" onChange="calculate_measurement_top(1)">    
                                    </td>
                                    
                                    <td>
                                    <input type="text" id="cons_1" onBlur="set_sum_value( 'cons_sum', 'cons_' )" onChange="set_sum_value( 'cons_sum', 'cons_' );set_sum_value( 'requirement_sum', 'requirement_' );calculate_requirement(1)"  name="cons_1" class="text_boxes_numeric" style="width:95px" <? if($hid_fab_cons_in_quotation_variable==2 && ($body_part_id==1 || $body_part_id==20)){ echo "readonly";} else{ echo "";} ?>> 
                                    </td>
                                    <td>
                                    <input type="text" id="processloss_1" onBlur="set_sum_value( 'processloss_sum', 'processloss_' ) "  name="processloss_1" class="text_boxes_numeric" style="width:95px" onChange="calculate_requirement(1);set_sum_value( 'processloss_sum', 'processloss_' );set_sum_value( 'requirement_sum', 'requirement_' ) "> 
                                    </td>
                                    <td>
                                    <input type="text" id="requirement_1" onBlur="set_sum_value( 'requirement_sum', 'requirement_' ) " onChange="set_sum_value( 'requirement_sum', 'requirement_' )"  name="requirement_1" class="text_boxes_numeric" style="width:90px" readonly> 
                                    </td>
                                    <td>
                                    <input type="text" id="pcs_1"  name="pcs_1"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:75px"  value="<? echo $pcs_value; ?>" />
                                    </td>
                                    <td id="add_1">
                                     <input type="button" id="addrow_1"  name="addrow_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(1)" />
                                     <input type="button" id="decreaserow_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(1,'tbl_consmption_cost' );" />
                                   <!-- <a href="## " id="addrow_1"  name="addrow_1" style="cursor:pointer;  text-decoration:none;" onClick="add_break_down_tr( 1 )"><b>+</b></a>-->
                                    <!--<input type="text" id="updateidcb_1" name="updateidcb_1" value="" class="text_boxes" style="width:20px" />-->
                                    </td>
                                </tr>
                    <? } ?>
                </tbody>
                </table>
               
                <table width="810" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                        	<th style="width:262px;">SUM</th>
                            <th width="110"><input type="text" id="cons_sum" name="cons_sum" class="text_boxes_numeric" style="width:95px" readonly></th>
                            <th width="110"><input type="text" id="processloss_sum"  name="processloss_sum" class="text_boxes_numeric" style="width:95px" readonly></th>
                            <th width="105"><input type="text" id="requirement_sum"  name="requirement_sum" class="text_boxes_numeric" style="width:90px" readonly></th>
                            <th width="90"><input type="text" id="pcs_sum"    name="pcs_sum" class="text_boxes_numeric" style="width:75px" readonly></th>
                            <th width=""></th>
                        </tr>
                        <tr>
                        	<th style="width:263px;">AVG</th>
                            <th width="110"><input type="text" id="avg_cons" name="avg_cons" class="text_boxes_numeric" style="width:95px" value="<? //echo $calculated_conss;?>" readonly></th>
                            <th width="110"><input type="text" id="calculated_procloss"  name="calculated_procloss" class="text_boxes_numeric" style="width:95px" readonly></th>
                           <th width="105"><input type="text" id="calculated_cons" name="calculated_cons" class="text_boxes_numeric" style="width:90px" value="<? echo $calculated_conss;?>" readonly></th>
                            <th width="90"><input type="text" id="calculated_pcs"    name="calculated_pcs" class="text_boxes_numeric" style="width:75px" readonly></th>
                            <th width=""></th>
                        </tr>
                    </tfoot>
                </table>
                
             				 <script>
							set_sum_value( 'cons_sum', 'cons_'  );
		                    set_sum_value( 'processloss_sum', 'processloss_'  );
		                    set_sum_value( 'requirement_sum', 'requirement_');
                            set_sum_value( 'pcs_sum', 'pcs_');
                            </script>
                
                
            </form>
            
        </fieldset>
   </div>
<?
if ($hid_fab_cons_in_quotation_variable==2)
{
	if ($body_part_id==1)
    {
?>
     

<div align="center" style="width:100%;" >
<fieldset>
        	<form id="fabriccost_3" autocomplete="off">
            	<table width="810" cellspacing="0" class="rpt_table" border="0" id="tbl_msmnt_cost" rules="all">
                	<thead>
                        <tr>
                        	<th  colspan="3">Body</th><th colspan="3">Sleeve </th><th colspan="2">1/2 Chest</th><th width="">Total</th>
                            
                        </tr>
                    	<tr>
                        	<th width="80">Length</th><th  width="80">Sewing Margin</th><th  width="80">Hem Margin</th><th width="80"> Length</th><th width="80">Sewing Margin</th><th width="80">Hem Margin</th><th width="80">Length</th><th width="80">Sewing Margin</th> <th width="">Total</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                    <?
					
					//$data_array=sql_select("select id,wo_pri_quo_fab_co_dtls_id, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length,half_chest_sewing_margin, total from wo_pri_quo_fab_co_avg_con_dtls where wo_pri_quo_fab_co_dtls_id='$updateid_fc'");
					//body_length,body_sewing_margin,body_hem_margin,sleeve_length,sleeve_sewing_margin,sleeve_hem_margin,half_chest_length,half_chest_sewing_margin
					$data_array=explode('__',$msmnt_breack_downn);
					if (count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							$data=explode('_',$row);
							?>
                            	<tr id="break_1" align="center">
                                    <td>
                                    <input type="text" id="bodylength_<? echo $i;?>"  name="bodylength_<? echo $i;?>" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[0]; ?>" />
                                    </td>
                                    <td>
                                    <input type="text" id="bodysewingmargin_<? echo $i;?>"    name="bodysewingmargin_<? echo $i;?>"  class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[1]; ?>" />    
                                    </td>
                                    <td>
                                    <input type="text" id="bodyhemmargin_<? echo $i;?>" name="bodyhemmargin_<? echo $i;?>" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(<? echo $i;?>)" value=" <? echo $data[2]; ?> "/> 
                                    </td>
                                    <td>
                                    <input type="text" id="sleevelength_<? echo $i;?>"  name="sleevelength_<? echo $i;?>" class="text_boxes_numeric" style="width:65px"  onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[3]; ?>" /> 
                                    </td>
                                    <td>
                                    <input type="text" id="sleevesewingmargin_<? echo $i;?>"  name="sleevesewingmargin_<? echo $i;?>" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[4]; ?>" /> 
                                    </td>
                                    <td>
                                    <input type="text" id="sleevehemmargin_<? echo $i;?>"  name="sleevehemmargin_<? echo $i;?>" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[5]; ?>" />
                                    </td>
                                    <td>
                                    <input type="text" id="chestlenght_<? echo $i;?>"  name="chestlenght_<? echo $i;?>" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[6]; ?>" />
                                    </td>
                                    <td>
                                    <input type="text" id="chestsewingmargin_<? echo $i;?>"  name="chestsewingmargin_<? echo $i;?>" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[7]; ?>" />
                                    </td>
                                    <td>
                                    <input type="text" id="totalcons_<? echo $i;?>"  name="totalcons_<? echo $i;?>" class="text_boxes_numeric" style="width:150px" readonly value="<? echo $data[8]; ?>" />
                                    </td>
                                </tr>
                            
                            <?
							 
						}
					}
					else
					{
					?>
                    <tr id="break_1" align="center">
                                    <td>
                                    <input type="text" id="bodylength_1"  name="bodylength_1" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(1)">
                                    </td>
                                    <td>
                                    <input type="text" id="bodysewingmargin_1"    name="bodysewingmargin_1"  class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(1)">    
                                    </td>
                                    <td>
                                    <input type="text" id="bodyhemmargin_1" name="bodyhemmargin_1" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(1)"> 
                                    </td>
                                    <td>
                                    <input type="text" id="sleevelength_1"  name="sleevelength_1" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(1)"> 
                                    </td>
                                    <td>
                                    <input type="text" id="sleevesewingmargin_1"  name="sleevesewingmargin_1" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(1)"> 
                                    </td>
                                    <td>
                                    <input type="text" id="sleevehemmargin_1"  name="sleevehemmargin_1" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(1)">
                                    </td>
                                     <td>
                                    <input type="text" id="chestlenght_1"  name="chestlenght_1" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(1)">
                                    </td>
                                    <td>
                                    <input type="text" id="chestsewingmargin_1"  name="chestsewingmargin_1" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(1)">
                                    </td>
                                    <td>
                                    <input type="text" id="totalcons_1"  name="totalcons_1" class="text_boxes_numeric" style="width:150px" readonly>
                                    </td>
                                </tr>
                    <? } ?>
                </tbody>
                </table>
                
               
            </form>
            
        </fieldset>
   </div>

<?
	}
	if($body_part_id==20)
	{
	?>
		<div align="center" style="width:100%;" >
<fieldset>
        	<form id="fabriccost_3" autocomplete="off">
            	<table width="810" cellspacing="0" class="rpt_table" border="0" id="tbl_msmnt_cost" rules="all">
                	<thead>
                        <tr>
                        	<th  colspan="2">Front Rise</th><th colspan="2">West Band</th><th colspan="3">In Seam</th><th colspan="2"> Half Thai</th><th >Total</th>
                            
                        </tr>
                    	<tr>
                        	<th width="70">Length</th><th  width="70">Sewing Margin</th><th  width="70">Length</th><th width="70"> Sewing Margin</th><th width="70">Length</th><th width="70">Sewing Margin</th><th width="70">Hem Margin</th><th width="70">Length</th> <th width="70">Sewing Margin</th><th width="">Total</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                    <?
					
					//$data_array=sql_select("select id,wo_pri_quo_fab_co_dtls_id, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length,half_chest_sewing_margin, total from wo_pri_quo_fab_co_avg_con_dtls where wo_pri_quo_fab_co_dtls_id='$updateid_fc'");

					$data_array=explode('__',$msmnt_breack_downn);
					if (count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							$data=explode('_',$row);
							?>
                            	<tr id="break_1" align="center">
                                    <td> 
                                    <input type="text" id="frontriselength_<? echo $i;?>"  name="frontriselength_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[0]; ?>">
                                    </td>
                                    <td>
                                    <input type="text" id="frontrisesewingmargin_<? echo $i;?>"    name="frontrisesewingmargin_<? echo $i;?>"  class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[1]; ?>">    
                                    </td>
                                    <td>
                                    <input type="text" id="westbandlength_<? echo $i;?>" name="westbandlength_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[2]; ?>"> 
                                    </td>
                                    <td>
                                    <input type="text" id="westbandsewingmargin_<? echo $i;?>"  name="westbandsewingmargin_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" value="<? echo $data[3]; ?>" onChange="calculate_measurement_top(<? echo $i;?>)"/ > 
                                    </td>
                                    <td>
                                    <input type="text" id="inseamlength_<? echo $i;?>"  name="inseamlength_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[4]; ?>"> 
                                    </td>
                                    <td>
                                    <input type="text" id="inseamsewingmargin_<? echo $i;?>"  name="inseamsewingmargin_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[5]; ?>">

                                    </td>
                                    <td>
                                    <input type="text" id="inseamhemmargin_<? echo $i;?>"  name="inseamhemmargin_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[6]; ?>">
                                    </td>
                                    <td>
                                    <input type="text" id="halfthailength_<? echo $i;?>"  name="halfthailength_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[7]; ?>">
                                    </td>
                                    <td>
                                    <input type="text" id="halfthaisewingmargin_<? echo $i;?>"  name="halfthaisewingmargin_<? echo $i;?>" class="text_boxes_numeric" style="width:55px"  value="<? echo $data[8]; ?>" onChange="calculate_measurement_top(<? echo $i;?>)">
                                    </td>
                                    <td>
                                    <input type="text" id="totalcons_<? echo $i;?>"  name="totalcons_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" readonly value="<? echo $data[9]; ?>">
                                    </td>
                                </tr>
                            
                            <?
							 
						}
					}
					else
					{
					?>
                    <tr id="break_1" align="center">
                                    <td>
                                    <input type="text" id="frontriselength_1"  name="frontriselength_1" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(1)">
                                    </td>
                                    <td>
                                    <input type="text" id="frontrisesewingmargin_1"    name="frontrisesewingmargin_1"  class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(1)">    
                                    </td>
                                    <td>
                                    <input type="text" id="westbandlength_1" name="westbandlength_1" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(1)"> 
                                    </td>
                                    <td>
                                    <input type="text" id="westbandsewingmargin_1"   name="westbandsewingmargin_1" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(1)"> 
                                    </td>

                                    <td>
                                    <input type="text" id="inseamlength_1"  name="inseamlength_1" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(1)"> 
                                    </td>
                                    <td>
                                    <input type="text" id="inseamsewingmargin_1"  name="inseamsewingmargin_1" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(1)">
                                    </td>
                                     <td>
                                    <input type="text" id="inseamhemmargin_1"  name="inseamhemmargin_1" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(1)">
                                    </td>
                                    <td>
                                    <input type="text" id="halfthailength_1"  name="halfthailength_1" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(1)">
                                    </td>
                                    <td>
                                    <input type="text" id="halfthaisewingmargin_1"  name="halfthaisewingmargin_1" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(1)" >
                                    </td>
                                    <td>
                                    <input type="text" id="totalcons_1"  name="totalcons_1" class="text_boxes_numeric" style="width:55px" readonly>
                                    </td>
                                </tr>
                    <? } ?>
                </tbody>
                </table>
                
                
            </form>
            
        </fieldset>
   </div>
   <?
	}
	?>
    
	<?
}
?>
<div align="center" style="width:100%;" >
<fieldset>
                <table width="810" cellspacing="0" class="" border="0" rules="all">
                	 <tr>
                        <td align="center" width="100%" class="button_container"> <input type="button" class="formbutton" value="Close" onClick="js_set_value()"/> </td> 
                    </tr>
                </table>
                </fieldset>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="delete_row_fabric_cost")
{
	$rID_de1=execute_query( "delete from  wo_pri_quo_fabric_cost_dtls where  id =".$data."",0);
	$rID_de1=execute_query( "delete from  wo_pri_quo_fab_co_avg_con_dtls where  wo_pri_quo_fab_co_dtls_id =".$data."",0);

}
if($action=="delete_row_yarn_cost")
{
	
	/*$rID_de1=execute_query( "INSERT INTO wo_pri_quo_fab_yarn_cost_dtls_bc (id,quotation_id,	count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
    SELECT a.id,a.quotation_id,	a.count_id,a.copm_one_id,a.percent_one,a.copm_two_id,a.percent_two,a.type_id,a.cons_ratio,a.cons_qnty,a.rate,a.amount,a.inserted_by,	a.insert_date,a.updated_by,a.update_date,a.status_active,a.is_deleted
    FROM wo_pri_quo_fab_yarn_cost_dtls a WHERE a.id =".$data."",0);*/
	$rID_de1=execute_query( "delete from  wo_pri_quo_fab_yarn_cost_dtls where  id =".$data."",0);
	

}
if($action=="conversion_from_chart")
{
	
	$conversion_from_chart=return_field_value("conversion_from_chart", "variable_order_tracking", "company_name='$data'  and variable_list=21 and status_active=1 and is_deleted=0");
	if($conversion_from_chart=="")
	{
		$conversion_from_chart=2;
	}
	echo trim($conversion_from_chart);

}

if($action=="delete_row_conversion_cost")
{
	$rID_de1=execute_query( "delete from  wo_pri_quo_fab_conv_cost_dtls where  id =".$data."",0);
}

if($action=="delete_row_trim_cost")
{
	$rID_de1=execute_query( "delete from  wo_pri_quo_trim_cost_dtls where  id =".$data."",0);
}

if($action=="delete_row_embellishment_cost")
{
	$rID_de1=execute_query( "delete from  wo_pri_quo_embe_cost_dtls where  id =".$data."",0);
}

if($action=="delete_row_wash_cost")
{
	$rID_de1=execute_query( "delete from  wo_pri_quo_embe_cost_dtls where  id =".$data."",0);
}

if($action=="delete_row_comarcial_cost")
{
	$rID_de1=execute_query( "delete from  wo_pri_quo_comarcial_cost_dtls where  id =".$data."",0);
}


if ($action=="save_update_delet_fabric_cost_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if($operation==0)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$id=return_next_id( "id", "wo_pri_quo_fabric_cost_dtls", 1 ) ;
		$field_array="id, quotation_id, item_number_id, body_part_id, fab_nature_id, color_type_id,lib_yarn_count_deter_id,	construction, composition,fabric_description, gsm_weight, avg_cons, fabric_source, rate, amount,avg_finish_cons,	avg_process_loss, inserted_by, insert_date, status_active, is_deleted, company_id, costing_per,fab_cons_in_quotat_varia,process_loss_method,cons_breack_down,msmnt_break_down,yarn_breack_down,marker_break_down,width_dia_type";
		
	    $field_array1="id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin,	body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin,front_rise_length,	front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin,total,marker_dia,marker_yds,marker_inch,gmts_pcs,marker_length,net_fab_cons";
		 $add_comma=0;
		 $id1=return_next_id( "id", "wo_pri_quo_fab_co_avg_con_dtls", 1 ) ;
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cbogmtsitem="cbogmtsitem_".$i;
			 $txtbodypart="txtbodypart_".$i;
			 $cbofabricnature="cbofabricnature_".$i;
			 $cbocolortype="cbocolortype_".$i;
			 $libyarncountdeterminationid="libyarncountdeterminationid_".$i;
			 $txtconstruction="txtconstruction_".$i;
			 $txtcomposition="txtcomposition_".$i;
			 $fabricdescription="fabricdescription_".$i;
			 $txtgsmweight="txtgsmweight_".$i;
			 $txtconsumption="txtconsumption_".$i;
			 $cbofabricsource="cbofabricsource_".$i;
			 $txtrate="txtrate_".$i;
			 $txtamount="txtamount_".$i;
			 $txtfinishconsumption="txtfinishconsumption_".$i;
			 $txtavgprocessloss="txtavgprocessloss_".$i;
			 $cbostatus="cbostatus_".$i;
			 $consbreckdown="consbreckdown_".$i;
			 $msmntbreackdown="msmntbreackdown_".$i;
			 $yarnbreackdown="yarnbreackdown_".$i;
			 $markerbreackdown="markerbreackdown_".$i;
			 $processlossmethod="processlossmethod_".$i;
			 $consumptionbasis="consumptionbasis_".$i;
			 $cbowidthdiatype="cbowidthdiatype_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$update_id.",".$$cbogmtsitem.",".$$txtbodypart.",".$$cbofabricnature.",".$$cbocolortype.",".$$libyarncountdeterminationid.",".$$txtconstruction.",".$$txtcomposition.",".$$fabricdescription.",".$$txtgsmweight.",".$$txtconsumption.",".$$cbofabricsource.",".$$txtrate.",".$$txtamount.",".$$txtfinishconsumption.",".$$txtavgprocessloss.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatus.",0,".$cbo_company_name.",".$cbo_costing_per.",".$$consumptionbasis.",".$$processlossmethod.",".$$consbreckdown.",".$$msmntbreackdown.",".$$yarnbreackdown.",".$$markerbreackdown.",".$$cbowidthdiatype.")";
			$new_array_size=array();
		    $new_array_color=array();
			$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
			$msmntbreackdown_array=explode('__',str_replace("'",'',$$msmntbreackdown));
			$markerbreackdownarr=explode('_',str_replace("'",'',$$markerbreackdown));
			for($c=0;$c < count($consbreckdown_array);$c++)
			{
			$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
			$msmntbreackdownarr=explode('_',$msmntbreackdown_array[$c]);
			
			if (!in_array($$consbreckdownarr[0],$new_array_size))
			 {
				  $size_id = return_id($consbreckdownarr[0], $size_library, "lib_size", "id,size_name");   
				  $new_array_size[$size_id]=str_replace("'","",$consbreckdownarr[0]);
			 }
			 else
			 {
				$size_id =  array_search($consbreckdownarr[0], $new_array_size); 
			 }
			
			if ($add_comma!=0) $data_array1 .=",";
			if(str_replace("'",'',$$txtbodypart)*1==1)
			{
				$data_array1 .="(".$id1.",".$id.",".$update_id.",'".$size_id."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."','".$msmntbreackdownarr[0]."','".$msmntbreackdownarr[1]."','".$msmntbreackdownarr[2]."','".$msmntbreackdownarr[3]."','".$msmntbreackdownarr[4]."','".$msmntbreackdownarr[5]."','".$msmntbreackdownarr[6]."','".$msmntbreackdownarr[7]."',0,0,0,0,0,0,0,0,0,'".$msmntbreackdownarr[8]."','".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
			}
			else if(str_replace("'",'',$$txtbodypart)*1==20)
			{
				$data_array1 .="(".$id1.",".$id.",".$update_id.",'".$size_id."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."',0,0,0,0,0,0,0,0,'".$msmntbreackdownarr[0]."','".$msmntbreackdownarr[1]."','".$msmntbreackdownarr[2]."','".$msmntbreackdownarr[3]."','".$msmntbreackdownarr[4]."','".$msmntbreackdownarr[5]."','".$msmntbreackdownarr[6]."','".$msmntbreackdownarr[7]."','".$msmntbreackdownarr[8]."','".$msmntbreackdownarr[9]."','".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
			}
			else 
			{
				$data_array1 .="(".$id1.",".$id.",".$update_id.",'".$size_id."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
			}
			$id1=$id1+1;
			$add_comma++;
			}
			$id=$id+1;

		 }
		$rID1=sql_insert("wo_pri_quo_fabric_cost_dtls",$field_array,$data_array,0);
		$rID=sql_insert("wo_pri_quo_fab_co_avg_con_dtls",$field_array1,$data_array1,1);
		//=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array5="id,quotation_id,fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_amount";
			$data_array5="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$tot_yarn_needed.",".$txtwoven_sum.",".$txtknit_sum.",".$txtamount_sum.")";
			$rID_id5=sql_insert("wo_pri_quo_sum_dtls",$field_array5,$data_array5,1);
		}
		
		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			//$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array5="fab_yarn_req_kg*fab_woven_req_yds*fab_knit_req_kg*fab_amount";
			$data_array5 ="".$tot_yarn_needed."*".$txtwoven_sum."*".$txtknit_sum."*".$txtamount_sum."";
			$rID_in5=sql_update("wo_pri_quo_sum_dtls",$field_array5,$data_array5,"quotation_id","".$update_id."",1);
		}
		//=======================sum End =================
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			echo "0**".$new_job_no[0]."**".$rID;
		}
		disconnect($con);
		die;
	}
	
	if($operation==1)
	{
	    $con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		$field_array="id, quotation_id, item_number_id, body_part_id, fab_nature_id, color_type_id,lib_yarn_count_deter_id,	construction, composition,fabric_description, gsm_weight, avg_cons, fabric_source, rate, amount,avg_finish_cons,	avg_process_loss, inserted_by, insert_date, status_active, is_deleted, company_id, costing_per,fab_cons_in_quotat_varia,process_loss_method,cons_breack_down,msmnt_break_down,yarn_breack_down,marker_break_down,width_dia_type";
		$field_array_up="quotation_id*item_number_id*body_part_id*fab_nature_id*color_type_id*lib_yarn_count_deter_id*construction*composition*fabric_description*gsm_weight*avg_cons*fabric_source*rate*amount*avg_finish_cons*avg_process_loss*updated_by*update_date*status_active*is_deleted*company_id*costing_per*fab_cons_in_quotat_varia*process_loss_method*cons_breack_down*msmnt_break_down*yarn_breack_down*marker_break_down*width_dia_type";
		
		$field_array1="id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin,	body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin,front_rise_length,	front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin,total,marker_dia,marker_yds,marker_inch,gmts_pcs,marker_length,net_fab_cons";
		 $add_co=0;
		 $add_comma=0;
		 $id=return_next_id( "id", "wo_pri_quo_fabric_cost_dtls", 1 ) ;
		 $id1=return_next_id( "id", "wo_pri_quo_fab_co_avg_con_dtls", 1 ) ;
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cbogmtsitem="cbogmtsitem_".$i;
			 $txtbodypart="txtbodypart_".$i;
			 $cbofabricnature="cbofabricnature_".$i;
			 $cbocolortype="cbocolortype_".$i;
			 $libyarncountdeterminationid="libyarncountdeterminationid_".$i;
			 $txtconstruction="txtconstruction_".$i;
			 $txtcomposition="txtcomposition_".$i;
			 $fabricdescription="fabricdescription_".$i;
			 $txtgsmweight="txtgsmweight_".$i;
			 $txtconsumption="txtconsumption_".$i;
			 $cbofabricsource="cbofabricsource_".$i;
			 $txtrate="txtrate_".$i;
			 $txtamount="txtamount_".$i;
			 $txtfinishconsumption="txtfinishconsumption_".$i;
			 $txtavgprocessloss="txtavgprocessloss_".$i;
			 $cbostatus="cbostatus_".$i;
			 $consbreckdown="consbreckdown_".$i;
			 $msmntbreackdown="msmntbreackdown_".$i;
			 $yarnbreackdown="yarnbreackdown_".$i;
			 $markerbreackdown="markerbreackdown_".$i;
			 $updateid="updateid_".$i;
			 $processlossmethod="processlossmethod_".$i;
			 $consumptionbasis="consumptionbasis_".$i;
			 $cbowidthdiatype="cbowidthdiatype_".$i;

			if(str_replace("'",'',$$updateid)!="")
			{
				$id_arr[]=str_replace("'",'',$$updateid);
				$data_array_up[str_replace("'",'',$$updateid)] =explode("*",("".$update_id."*".$$cbogmtsitem."*".$$txtbodypart."*".$$cbofabricnature."*".$$cbocolortype."*".$$libyarncountdeterminationid."*".$$txtconstruction."*".$$txtcomposition."*".$$fabricdescription."*".$$txtgsmweight."*".$$txtconsumption."*".$$cbofabricsource."*".$$txtrate."*".$$txtamount."*".$$txtfinishconsumption."*".$$txtavgprocessloss."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$$cbostatus."*0*".$cbo_company_name."*".$cbo_costing_per."*".$$consumptionbasis."*".$$processlossmethod."*".$$consbreckdown."*".$$msmntbreackdown."*".$$yarnbreackdown."*".$$markerbreackdown."*".$$cbowidthdiatype.""));
				
				$rID=execute_query( "delete from wo_pri_quo_fab_co_avg_con_dtls where  wo_pri_quo_fab_co_dtls_id =".$$updateid."",0);
				$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
				$msmntbreackdown_array=explode('__',str_replace("'",'',$$msmntbreackdown));
				$markerbreackdownarr=explode('_',str_replace("'",'',$$markerbreackdown));
				for($c=0;$c < count($consbreckdown_array);$c++)
				{
					$new_array_size=array();
		            $new_array_color=array();
					$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
					$msmntbreackdownarr=explode('_',$msmntbreackdown_array[$c]);
					if (!in_array($$consbreckdownarr[0],$new_array_size))
					 {
						  $size_id = return_id($consbreckdownarr[0], $size_library, "lib_size", "id,size_name");   
						  $new_array_size[$size_id]=str_replace("'","",$consbreckdownarr[0]);
					 }
					 else
					 {
						$size_id =  array_search($consbreckdownarr[0], $new_array_size); 
					 }
					if ($add_comma!=0) $data_array1 .=",";
					if(str_replace("'",'',$$txtbodypart)*1==1)
					{
						$data_array1 .="(".$id1.",".$$updateid.",".$update_id.",'".$size_id."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."','".$msmntbreackdownarr[0]."','".$msmntbreackdownarr[1]."','".$msmntbreackdownarr[2]."','".$msmntbreackdownarr[3]."','".$msmntbreackdownarr[4]."','".$msmntbreackdownarr[5]."','".$msmntbreackdownarr[6]."','".$msmntbreackdownarr[7]."',0,0,0,0,0,0,0,0,0,'".$msmntbreackdownarr[8]."','".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
					}
					else if(str_replace("'",'',$$txtbodypart)*1==20)
					{
						$data_array1 .="(".$id1.",".$$updateid.",".$update_id.",'".$size_id."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."',0,0,0,0,0,0,0,0,'".$msmntbreackdownarr[0]."','".$msmntbreackdownarr[1]."','".$msmntbreackdownarr[2]."','".$msmntbreackdownarr[3]."','".$msmntbreackdownarr[4]."','".$msmntbreackdownarr[5]."','".$msmntbreackdownarr[6]."','".$msmntbreackdownarr[7]."','".$msmntbreackdownarr[8]."','".$msmntbreackdownarr[9]."','".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
					}
					else 
					{
						$data_array1 .="(".$id1.",".$$updateid.",".$update_id.",'".$size_id."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
					}
					$id1=$id1+1;
					$add_comma++;
				}
			}
			if(str_replace("'",'',$$updateid)=="")
			{
				if ($add_co!=0) $data_array .=",";
				$data_array.="(".$id.",".$update_id.",".$$cbogmtsitem.",".$$txtbodypart.",".$$cbofabricnature.",".$$cbocolortype.",".$$libyarncountdeterminationid.",".$$txtconstruction.",".$$txtcomposition.",".$$fabricdescription.",".$$txtgsmweight.",".$$txtconsumption.",".$$cbofabricsource.",".$$txtrate.",".$$txtamount.",".$$txtfinishconsumption.",".$$txtavgprocessloss.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatus.",0,".$cbo_company_name.",".$cbo_costing_per.",".$$consumptionbasis.",".$$processlossmethod.",".$$consbreckdown.",".$$msmntbreackdown.",".$$yarnbreackdown.",".$$markerbreackdown.",".$$cbowidthdiatype.")";
// msmnt break down=================================================================================
				$new_array_size=array();
				$new_array_color=array();
				$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
				$msmntbreackdown_array=explode('__',str_replace("'",'',$$msmntbreackdown));
				$markerbreackdownarr=explode('_',str_replace("'",'',$$markerbreackdown));
				
				for($c=0;$c < count($consbreckdown_array);$c++)
				{
					$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
					$msmntbreackdownarr=explode('_',$msmntbreackdown_array[$c]);
					if (!in_array($$consbreckdownarr[0],$new_array_size))
					 {
						  $size_id = return_id($consbreckdownarr[0], $size_library, "lib_size", "id,size_name");   
						  $new_array_size[$size_id]=str_replace("'","",$consbreckdownarr[0]);
					 }
					 else
					 {
						$size_id =  array_search($consbreckdownarr[0], $new_array_size); 
					 }
					if ($add_comma!=0) $data_array1 .=",";
					if(str_replace("'",'',$$txtbodypart)*1==1)
					{
						$data_array1 .="(".$id1.",".$id.",".$update_id.",'".$size_id."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."','".$msmntbreackdownarr[0]."','".$msmntbreackdownarr[1]."','".$msmntbreackdownarr[2]."','".$msmntbreackdownarr[3]."','".$msmntbreackdownarr[4]."','".$msmntbreackdownarr[5]."','".$msmntbreackdownarr[6]."','".$msmntbreackdownarr[7]."',0,0,0,0,0,0,0,0,0,'".$msmntbreackdownarr[8]."','".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
					}
					else if(str_replace("'",'',$$txtbodypart)*1==20)
					{
					
						$data_array1 .="(".$id1.",".$id.",".$update_id.",'".$size_id."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."',0,0,0,0,0,0,0,0,'".$msmntbreackdownarr[0]."','".$msmntbreackdownarr[1]."','".$msmntbreackdownarr[2]."','".$msmntbreackdownarr[3]."','".$msmntbreackdownarr[4]."','".$msmntbreackdownarr[5]."','".$msmntbreackdownarr[6]."','".$msmntbreackdownarr[7]."','".$msmntbreackdownarr[8]."','".$msmntbreackdownarr[9]."','".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
					}
					else
					{
						$data_array1 .="(".$id1.",".$id.",".$update_id.",'".$size_id."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
					}
					$id1=$id1+1;
					$add_comma++;
				}
// msmnt break down end =================================================================================
			  $id=$id+1;
			  $add_co++;	
			}
		 }
		$rID_up=execute_query(bulk_update_sql_statement( "wo_pri_quo_fabric_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		if($data_array!="")
		{
			$rID=sql_insert("wo_pri_quo_fabric_cost_dtls",$field_array,$data_array,0);
		}
		$rID1=sql_insert("wo_pri_quo_fab_co_avg_con_dtls",$field_array1,$data_array1,1);
		//=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array5="id,quotation_id,fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_amount";
			$data_array5="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$tot_yarn_needed.",".$txtwoven_sum.",".$txtknit_sum.",".$txtamount_sum.")";
			$rID_id5=sql_insert("wo_pri_quo_sum_dtls",$field_array5,$data_array5,1);
		}
		
		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			//$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array5="fab_yarn_req_kg*fab_woven_req_yds*fab_knit_req_kg*fab_amount";
			$data_array5 ="".$tot_yarn_needed."*".$txtwoven_sum."*".$txtknit_sum."*".$txtamount_sum."";
			$rID_in5=sql_update("wo_pri_quo_sum_dtls",$field_array5,$data_array5,"quotation_id","".$update_id."",1);
		}
		update_comarcial_cost($update_id);
		//=======================sum End =================
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID_up){
				mysql_query("COMMIT");  
				echo "1**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			echo "1**".$new_job_no[0]."**".$rID_up;
		}
		disconnect($con);
		die;
	}
}

if ($action=="save_update_delet_fabric_yarn_cost_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if($operation==0)
	{
	     $con = connect();
		 if($db_type==0)
		 {
			mysql_query("BEGIN");
		 }
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		 $id=return_next_id( "id", "wo_pri_quo_fab_yarn_cost_dtls", 1 ) ;
		 $field_array="id,quotation_id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, status_active, is_deleted";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cbocount="cbocount_".$i;
			 $cbocompone="cbocompone_".$i;
			 $percentone="percentone_".$i;
			 $cbocomptwo="cbocomptwo_".$i;
			 $percenttwo="percenttwo_".$i;
			 $cbotype="cbotype_".$i;
			 $consratio="consratio_".$i;
			 $consqnty="consqnty_".$i;
			 $txtrateyarn="txtrateyarn_".$i;
			 $txtamountyarn="txtamountyarn_".$i;
			 $cbostatusyarn="cbostatusyarn_".$i;
			 $updateidyarncost="updateidyarncost_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$update_id.",".$$cbocount.",".$$cbocompone.",".$$percentone.",".$$cbocomptwo.",".$$percenttwo.",".$$cbotype.",".$$consratio.",".$$consqnty.",".$$txtrateyarn.",".$$txtamountyarn.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatusyarn.",0)";
			$id=$id+1;
		 }
		 $rID=sql_insert("wo_pri_quo_fab_yarn_cost_dtls",$field_array,$data_array,0);
		 //=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,yarn_cons_qnty,yarn_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtconsumptionyarn_sum.",".$txtamountyarn_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}
		
		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="yarn_cons_qnty*yarn_amount";
			$data_array2 ="".$txtconsumptionyarn_sum."*".$txtamountyarn_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		//=======================sum End =================
		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			echo "0**".$new_job_no[0]."**".$rID;
		}
		disconnect($con);
		die;
	}
	
	if($operation==1)
	{
	     $con = connect();
		 if($db_type==0)
		 {
			mysql_query("BEGIN");
		 }
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		 $field_array_up="quotation_id*count_id*copm_one_id*percent_one*copm_two_id*percent_two*type_id*cons_ratio*cons_qnty*rate*amount*updated_by*update_date*status_active*is_deleted";
		 $field_array="id,quotation_id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, status_active, is_deleted";
		 $add_comma=0;
         $id=return_next_id( "id", "wo_pri_quo_fab_yarn_cost_dtls", 1 ) ;
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cbocount="cbocount_".$i;
			 $cbocompone="cbocompone_".$i;
			 $percentone="percentone_".$i;
			 $cbocomptwo="cbocomptwo_".$i;
			 $percenttwo="percenttwo_".$i;
			 $cbotype="cbotype_".$i;
			 $consratio="consratio_".$i;
			 $consqnty="consqnty_".$i;
			 $txtrateyarn="txtrateyarn_".$i;
			 $txtamountyarn="txtamountyarn_".$i;
			 $cbostatusyarn="cbostatusyarn_".$i;
			 $updateidyarncost="updateidyarncost_".$i;
			if(str_replace("'",'',$$updateidyarncost)!="")
			{
			$id_arr[]=str_replace("'",'',$$updateidyarncost);
			$data_array_up[str_replace("'",'',$$updateidyarncost)] =explode(",",("".$update_id.",".$$cbocount.",".$$cbocompone.",".$$percentone.",".$$cbocomptwo.",".$$percenttwo.",".$$cbotype.",".$$consratio.",".$$consqnty.",".$$txtrateyarn.",".$$txtamountyarn.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatusyarn.",0"));
			}
			
			if(str_replace("'",'',$$updateidyarncost)=="")
			{
				if ($add_comma!=0) $data_array .=",";
				$data_array ="(".$id.",".$update_id.",".$$cbocount.",".$$cbocompone.",".$$percentone.",".$$cbocomptwo.",".$$percenttwo.",".$$cbotype.",".$$consratio.",".$$consqnty.",".$$txtrateyarn.",".$$txtamountyarn.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatusyarn.",0)";
				$id=$id+1;
			    $add_comma++;
			}
		 }
		 $rID_up=execute_query(bulk_update_sql_statement( "wo_pri_quo_fab_yarn_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		 if($data_array !="")
		 {
		 $rID=sql_insert("wo_pri_quo_fab_yarn_cost_dtls",$field_array,$data_array,0);
		 }
		//=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,yarn_cons_qnty,yarn_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtconsumptionyarn_sum.",".$txtamountyarn_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}
		
		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="yarn_cons_qnty*yarn_amount";
			$data_array2 ="".$txtconsumptionyarn_sum."*".$txtamountyarn_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		update_comarcial_cost($update_id);
		//=======================sum End =================
 		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID_up ){
				mysql_query("COMMIT");  
				echo "1**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			echo "1**".$new_job_no[0]."**".$rID;
		}
		disconnect($con);
		die;
	}
	
	
	
}

if ($action=="set_conversion_charge")
{
	
	$rate=return_field_value("rate", "lib_cost_component", "cost_component_name=$data");
	echo $rate; die; 
}

if($action=="conversion_chart_popup")
{
echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
extract($_REQUEST);

?>
<script> 
function js_set_value(id,rate)
{
	//var data=data.split("_");
	document.getElementById('charge_id').value=id;
	document.getElementById('charge_value').value=rate;
	parent.emailwindow.hide();

}
function toggle( x, origColor ) 
{

			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
}
</script> 
</head>
<body>
<div align="center">
<form>
<input type="hidden" id="charge_id" name="charge_id" />
<input type="hidden" id="charge_value" name="charge_value" />



<?
if($cbotypeconversion==1)
{
	 $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	 //$buyer_arr=return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');
	 
	 $arr=array (0=>$company_arr,1=>$body_part,5=>$unit_of_measurement,7=>$row_status);
	// echo  create_list_view ( "list_view", "Company Name,Body Part,Construction & Composition,GSM,Yarn Description,UOM,In-House Rate,Status", "150,120,180,60,150,70,100,60","980","220",1, "select id,comapny_id,body_part,const_comp,gsm,yarn_description,in_house_rate,uom_id,status_active from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id=2 and comapny_id=$cbo_company_name", "js_set_value", "id,in_house_rate","", 1, "comapny_id,body_part,0,0,0,uom_id,0,status_active", $arr , "comapny_id,body_part,const_comp,gsm,yarn_description,uom_id,in_house_rate,status_active", "../sub_contract_bill/requires/lib_subcontract_knitting_controller", 'setFilterGrid("list_view",-1);','0,0,0,2,0,0,2,0' ) ;
	?>
     <table width="963" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0">
     <thead>
     <th width="50">SL</th>
     <th width="150">Company Name</th>
     <th width="120">Body Part</th>
     <th width="180">Construction & Composition</th>
     <th width="60">GSM</th>
     <th width="150">Yarn Description</th>
     <th width="70">UOM</th>
     <th width="100">In-House Rate</th>
     <th>Status</th>
     </thead>
     </table>
     <div style=" width:980; overflow:scroll-y; max-height:300px">
     <table width="963" class="rpt_table" border="1" rules="all" id="list_view" cellpadding="0" cellspacing="0">
     <?
	 $sql_data=sql_select("select id,comapny_id,body_part,const_comp,gsm,yarn_description,in_house_rate,uom_id,status_active from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id=2 and comapny_id=$cbo_company_name");
	 $i=1;
	 foreach($sql_data as $row)
	 {
		 if ($i%2==0)  
		$bgcolor="#E9F3FF";
		else
		$bgcolor="#FFFFFF";	
		
	 ?>
     <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value(<? echo $row[csf("id")]?>, <? echo $row[csf("in_house_rate")]; ?>)" id="tr_<? echo $row[csf("id")];  ?>">
     <td width="50"><? echo $i; ?></td>
     <td width="150"><? echo $company_arr[$row[csf("comapny_id")]]; ?></td>
     <td width="120"><? echo $body_part[$row[csf("body_part")]]; ?></td>
     <td width="180"><? echo $row[csf("const_comp")]; ?></td>
     <td width="60"><? echo $row[csf("gsm")]; ?></td>
     <td width="150"><? echo $row[csf("yarn_description")]; ?></td>
     <td width="70"><? echo $unit_of_measurement[$row[csf("uom_id")]]; ?></td>
     <td width="100"><? echo $row[csf("in_house_rate")]; ?></td>
     <td><? echo $row_status[$row[csf("status_active")]]; ?></td>
     </tr>
     <?
	  $i++;
	 }
	 ?>
     <script>
	 setFilterGrid("list_view",-1)
     toggle( "tr_"+"<? echo $coversionchargelibraryid ?>", '#FFFFCC');
	 </script>
     </table>
     </div>
     
     <?
}
else
{
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");  
	$arr=array (0=>$company_arr,2=>$process_type,3=>$conversion_cost_head_array,4=>$color_library_arr,5=>$fabric_typee,7=>$unit_of_measurement,8=>$production_process,9=>$row_status);
	//echo "select id,comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id in (3,4,8) and process_id=$cbotypeconversion and comapny_id=$cbo_company_name";
	//echo"select id,comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id in (3,4,8) and process_id=$cbotypeconversion and comapny_id=$cbo_company_name";
	//echo  create_list_view ( "list_view", "Company Name,Const. Compo.,Process Type,Process Name,Color,Width/Dia type,In House Rate,UOM,Rate type,Status", "100,150,70,70,70,80,60,80,60,50","900","250",1, "select id,comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id in (3,4,7,8) and process_id=$cbotypeconversion and comapny_id=$cbo_company_name", "js_set_value", "id,in_house_rate","", 1, "comapny_id,0,process_type_id,process_id,color_id,width_dia_id,0,uom_id,rate_type_id,status_active", $arr, "comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active","requires/lib_subcontract_dyeing_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,2,0,0,0' );
	?>
    <table width="900" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0">
     <thead>
     <th width="50">SL</th>
     <th width="100">Company Name</th>
     <th width="150">Const. Compo.</th>
     <th width="70">Process Type</th>
     <th width="70">Process Name</th>
     <th width="70">Color</th>
     <th width="80">Width/Dia type</th>
     
     <th width="60">In House Rate</th>
     <th width="80">UOM</th>
     <th width="60">Rate type</th>
     <th>Status</th>
     </thead>
     </table>
     <div style=" width:917; overflow:scroll-y; max-height:300px">
     <table width="900" class="rpt_table" border="1" rules="all" id="list_view" cellpadding="0" cellspacing="0">
     <?
	 $sql_data=sql_select("select id,comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id in (3,4,7,8) and process_id=$cbotypeconversion and comapny_id=$cbo_company_name");
	 $i=1;
	 foreach($sql_data as $row)
	 {
		 if ($i%2==0)  
		$bgcolor="#E9F3FF";
		else
		$bgcolor="#FFFFFF";	
		
	 ?>
     <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value(<? echo $row[csf("id")]?>, <? echo $row[csf("in_house_rate")]; ?>)" id="tr_<? echo $row[csf("id")];  ?>">
     <td width="50"><? echo $i; ?></td>
     <td width="100"><? echo $company_arr[$row[csf("comapny_id")]]; ?></td>
     <td width="150"><? echo $row[csf("const_comp")]; ?></td>
     <td width="70"><? echo $process_type[$row[csf("process_type_id")]]; ?></td>
      <td width="70"><? echo $conversion_cost_head_array[$row[csf("process_id")]]; ?></td>
     <td width="70"><? echo $color_library_arr[$row[csf("color_id")]]; ?></td>
     <td width="80"><? echo $fabric_typee[$row[csf("width_dia_id")]]; ?></td>
     
     <td width="60"><? echo $row[csf("in_house_rate")]; ?></td>
     <td width="80"><? echo $unit_of_measurement[$row[csf("uom_id")]]; ?></td>
     <td width="60"><? echo $production_process[$row[csf("rate_type_id")]]; ?></td>
     <td><? echo $row_status[$row[csf("status_active")]]; ?></td>
     </tr>
     <?
	  $i++;
	 }
	 ?>
     <script>
	 setFilterGrid("list_view",-1)
     toggle( "tr_"+"<? echo $coversionchargelibraryid ?>", '#FFFFCC');
	 </script>
     </table>
     </div>
    <?
}
?>
</form>
</div>
</body>
</html>
<?
	
}
if ($action=="set_conversion_qnty")
{
	$avg_cons=return_field_value("avg_cons", "wo_pri_quo_fabric_cost_dtls", "id=$data");
	echo $avg_cons; die; 
}
if ($action=="save_update_delet_fabric_conversion_cost_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if($operation==0)
	{
	     $con = connect();
		 if($db_type==0)
		 {
			mysql_query("BEGIN");

		 }
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		 $id=return_next_id( "id", "wo_pri_quo_fab_conv_cost_dtls", 1 ) ;
		 $field_array="id,quotation_id,cost_head,cons_type,req_qnty,charge_unit,amount,charge_lib_id,inserted_by,insert_date,status_active,is_deleted";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cbocosthead="cbocosthead_".$i;
			 $cbotypeconversion="cbotypeconversion_".$i;
			 $txtreqqnty="txtreqqnty_".$i;
			 $txtchargeunit="txtchargeunit_".$i;
			 $txtamountconversion="txtamountconversion_".$i;
			 $cbostatusconversion="cbostatusconversion_".$i;
			 $updateidcoversion="updateidcoversion_".$i;
			 $coversionchargelibraryid="coversionchargelibraryid_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$update_id.",".$$cbocosthead.",".$$cbotypeconversion.",".$$txtreqqnty.",".$$txtchargeunit.",".$$txtamountconversion.",".$$coversionchargelibraryid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatusconversion.",0)";
			$id=$id+1;
		 }
		 $rID=sql_insert("wo_pri_quo_fab_conv_cost_dtls",$field_array,$data_array,0);
		 //=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,conv_req_qnty,conv_charge_unit,conv_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtconreqnty_sum.",".$txtconchargeunit_sum.",".$txtconamount_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}
		
		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="conv_req_qnty*conv_charge_unit*conv_amount";
		    $data_array2 ="".$txtconreqnty_sum."*".$txtconchargeunit_sum."*".$txtconamount_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		//=======================sum End =================
 		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			echo "0**".$new_job_no[0]."**".$rID;
		}
		disconnect($con);
		die;
	}
	
	if($operation==1)
	{
	     $con = connect();
		 if($db_type==0)
		 {
			mysql_query("BEGIN");
		 }
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		 $field_array_up="quotation_id*cost_head*cons_type*req_qnty*charge_unit*amount*charge_lib_id*updated_by*update_date*status_active*is_deleted";
		 $field_array="id,quotation_id,cost_head,cons_type,req_qnty,charge_unit,amount,charge_lib_id,inserted_by,insert_date,status_active,is_deleted";
		 $add_comma=0;
		 $id=return_next_id( "id", "wo_pri_quo_fab_conv_cost_dtls", 1 ) ;
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cbocosthead="cbocosthead_".$i;
			 $cbotypeconversion="cbotypeconversion_".$i;
			 $txtreqqnty="txtreqqnty_".$i;
			 $txtchargeunit="txtchargeunit_".$i;
			 $txtamountconversion="txtamountconversion_".$i;
			 $cbostatusconversion="cbostatusconversion_".$i;
			 $updateidcoversion="updateidcoversion_".$i;
			 $coversionchargelibraryid="coversionchargelibraryid_".$i;
			if(str_replace("'",'',$$updateidcoversion)!="")
			{
				$id_arr[]=str_replace("'",'',$$updateidcoversion);
				$data_array_up[str_replace("'",'',$$updateidcoversion)] =explode(",",("".$update_id.",".$$cbocosthead.",".$$cbotypeconversion.",".$$txtreqqnty.",".$$txtchargeunit.",".$$txtamountconversion.",".$$coversionchargelibraryid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatusconversion.",0"));
			}
			if(str_replace("'",'',$$updateidcoversion)=="")
			{
				if ($add_comma!=0) $data_array .=",";
				$data_array .="(".$id.",".$update_id.",".$$cbocosthead.",".$$cbotypeconversion.",".$$txtreqqnty.",".$$txtchargeunit.",".$$txtamountconversion.",".$$coversionchargelibraryid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatusconversion.",0)";
				$id=$id+1;
				$add_comma++;
			}
		 }
		 
		 $rID=execute_query(bulk_update_sql_statement( "wo_pri_quo_fab_conv_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		 if($data_array !="")
		 {
			 $rID=sql_insert("wo_pri_quo_fab_conv_cost_dtls",$field_array,$data_array,0);
		 }
		 //=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,conv_req_qnty,conv_charge_unit,conv_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtconreqnty_sum.",".$txtconchargeunit_sum.",".$txtconamount_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}
		
		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="conv_req_qnty*conv_charge_unit*conv_amount";
		    $data_array2 ="".$txtconreqnty_sum."*".$txtconchargeunit_sum."*".$txtconamount_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		//=======================sum End =================
		 check_table_status( $_SESSION['menu_id'],0);
		 if($db_type==0)
		 {

			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		 }
		
		 if($db_type==2 || $db_type==1 )
		 {
			echo "1**".$new_job_no[0]."**".$rID;
		 }
		 disconnect($con);
		 die;
	}
	
	
	
}
// Fabric Cost End =========================================================================================================================================================
?>
<?
// Trim Cost  =========================================================================================================================================================

if ($action=="show_trim_cost_listview")
{
	$data=explode("*",$data);
	//print_r($data);
	
?>
<h3 align="left" class="accordion_h">+Trim Cost</h3> 
       <div id="content_trim_cost"  align="center">            
    	<fieldset>
        	<form id="trimccost_6" autocomplete="off">
            	<table width="880" cellspacing="0" class="rpt_table" border="0" id="tbl_trim_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="100">Group</th> <th  width="100">Cons UOM</th> <th  width="90">Cons/Dzn Gmts</th> <th width="90">Rate</th> <th width="110">Amount</th> <th width="95">Apvl Req.</th> <th width="95">Nominated Supp</th> <th width="95">Status</th> <th width=""></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$approved=return_field_value("approved", "wo_price_quotation", "id='$data[0]'");
					if($approved==1)
					{
					$disabled=1;
					//$permission=0;
					}
					else
					{
					$disabled=0;
					//$permission=$permission;
					}

					$save_update=1;
					$data_array=sql_select("select id, quotation_id, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active from  wo_pri_quo_trim_cost_dtls where quotation_id='$data[0]'");// quotation_id='$data'
					
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="trim_1" align="center">
                                    <td>
									<? 
									echo create_drop_down( "cbogroup_".$i, 95, "select item_name,id from lib_item_group where item_category=4 and is_deleted=0  and 
											status_active=1 order by item_name", "id,item_name",1," -- Select Item --", $row[csf("trim_group")], "set_trim_cons_uom(this.value, ".$i.")",$disabled,"" ); 
									//echo create_drop_down( "cbo_trims_group", 180, "select item_name,id from lib_item_group where item_category=4 and is_deleted=0  and 
											//status_active=1 order by item_name", "id,item_name", 1, '--Select--', 0,"set_cons_uom(this.value)" );
									//create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index )
									?>
                                    
                                    </td>
                                    <td>
									<?  echo create_drop_down( "cboconsuom_".$i, 80, $unit_of_measurement,"", 1, "-- Select --", $row[csf("cons_uom")], "",1,"" ); ?>
                                    </td>
                                   <td><!--onDblClick="open_calculator(<? //echo $i;?> )"-->
                                    <input type="text" id="txtconsdzngmts_<? echo $i; ?>"  name="txtconsdzngmts_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("cons_dzn_gmts")];  ?>" onChange="calculate_trim_cost( <? echo $i;?> )"  <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    </td>
                                   <td>
                                    <input type="text" id="txttrimrate_<? echo $i; ?>"  name="txttrimrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("rate")];  ?>" onChange="calculate_trim_cost( <? echo $i;?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    </td>
                                    <td>
                                    <input type="text" id="txttrimamount_<? echo $i; ?>"  name="txttrimamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:95px" value="<? echo $row[csf("amount")];  ?>"  readonly  />
                                    </td>
                                    <td width="95">
									<? echo create_drop_down( "cboapbrequired_".$i, 80, $yes_no,"", 0, "0", $row[csf("apvl_req")], '',$disabled,'' );  ?>
                                    </td>  
                                    
                                    <td width="95">
									<?
									//echo create_drop_down( "cbonominasupplier_".$i, 80, $row_status,"", 0, "0", $row[csf("nominated_supp")], '','','' ); 
									echo create_drop_down( "cbonominasupplier_".$i,80, "select a.supplier_name,a.id from  lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(4,5) and a.is_deleted=0  and a.status_active=1 group by a.id order by a.supplier_name", "id,supplier_name", 0, '', $row[csf("nominated_supp")],"",$disabled,"" );
									?>
                                    </td>  
                                    
                                    <td width="95">
									<? echo create_drop_down( "cbotrimstatus_".$i, 80, $row_status,"", 0, "0", $row[csf("status_active")], '',$disabled,'' );  ?></td>  
                                    <td>
                                    <input type="button" id="increasetrim_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_trim_cost(<? echo $i; ?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    <input type="button" id="decreasetrim_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_trim_cost' );"  <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    
                                    <input type="hidden" id="updateidtrim_<? echo $i; ?>" name="updateidtrim_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("id")]; ?>"  />  
                                      
  
                                                                      
                                     </td> 
                                </tr>
                            
                            <?
							 
						}
					}
					else
					{
						$data_array=sql_select("select a.trims_group, a.cons_uom, a.cons_dzn_gmts, a.purchase_rate, a.amount, a.apvl_req, a.supplyer from  lib_trim_costing_temp a,lib_trim_costing_temp_dtls b  where a.id=b.lib_trim_costing_temp_id and b.buyer_id='$data[1]' and a.status_active=1 and  a.is_deleted=0 group by a.id");// quotation_id='$data'
					if ( count($data_array)>0 && $data[2]==1)
					{
						$save_update=0;
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
					?>
                    <tr id="trim_1" align="center">
                                   <td>
									<? 
									echo create_drop_down( "cbogroup_".$i, 95, "select item_name,id from lib_item_group where item_category=4 and is_deleted=0  and 
											status_active=1 order by item_name", "id,item_name",1," -- Select Item --", $row[csf("trims_group")], "set_trim_cons_uom(this.value,".$i.")",'',"" ); 
									//echo create_drop_down( "cbogroup_".$i, 95, "select item_name,id from lib_item_group where item_category=4 and is_deleted=0  and 
											//status_active=1 order by item_name", "id,item_name",1," -- Select Item --", $row[csf("trim_group")], '','','' ); 
									?>
                                    </td>
                                    <td><?  echo create_drop_down( "cboconsuom_".$i, 80, $unit_of_measurement,"", 1, "-- Select --", $row[csf("cons_uom")], "",1,"" ); ?></td>
                                   <td>
                                    <input type="text" id="txtconsdzngmts_<? echo $i; ?>"  name="txtconsdzngmts_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("cons_dzn_gmts")];?>" onChange="calculate_trim_cost( <? echo $i;?> )" /><!--onDblClick="open_calculator(<?// echo $i;?> )"-->
                                    </td>
                                   <td>
                                    <input type="text" id="txttrimrate_<? echo $i; ?>"  name="txttrimrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("purchase_rate")];?>" onChange="calculate_trim_cost( <? echo $i;?> )"/>
                                    </td>
                                    <td>
                                    <input type="text" id="txttrimamount_<? echo $i; ?>"  name="txttrimamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:95px" value="<? echo $row[csf("amount")];?>"  readonly />
                                    </td>
                                    <td width="95">
									<? echo create_drop_down( "cboapbrequired_".$i, 80, $yes_no,"", 0, "0", $row[csf("apvl_req")], '','','' );  ?>
                                    </td>  
                                    
                                    <td width="95">
									<? //echo create_drop_down( "cbonominasupplier_1", 80, $row_status,"", 0, "0", $row[csf("supplyer")], '','','' );  
                                    echo create_drop_down( "cbonominasupplier_".$i,80, "select a.supplier_name,a.id from  lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(4,5) and a.is_deleted=0  and a.status_active=1 group by a.id order by a.supplier_name", "id,supplier_name", 1, '-Select-', $row[csf("supplyer")],"","","" );
									?>
                                    </td>  
                                  
                                    <td width="95">
									<? echo create_drop_down( "cbotrimstatus_".$i, 80, $row_status,"", 0, "0", '', '','','' );  ?>
                                    </td>  
                                    <td>
                                    <input type="button" id="increasetrim_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_trim_cost(<? echo $i; ?> )" />
                                    <input type="button" id="decreasetrim_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_trim_cost' );" />
                                    <input type="hidden" id="updateidtrim_<? echo $i; ?>" name="updateidtrim_<? echo $i; ?>"  class="text_boxes" style="width:20px" value=""  />
                                    
                                   </td> 
                                </tr>
                    <? 
						}
					}
					else
					{
					 $save_update=0;
					?>
                    <tr id="trim_1" align="center">
                                   <td>
									<? 
									echo create_drop_down( "cbogroup_1", 95, "select item_name,id from lib_item_group where item_category=4 and is_deleted=0  and 
											status_active=1 order by item_name", "id,item_name",1," -- Select Item --", $row[csf("trims_group")], "set_trim_cons_uom(this.value,1)",'',"" ); 
									//echo create_drop_down( "cbogroup_".$i, 95, "select item_name,id from lib_item_group where item_category=4 and is_deleted=0  and 
											//status_active=1 order by item_name", "id,item_name",1," -- Select Item --", $row[csf("trim_group")], '','','' ); 
									?>
                                    </td>
                                    <td><?  echo create_drop_down( "cboconsuom_1", 80, $unit_of_measurement,"", 1, "-- Select --", $row[csf("cons_uom")], "",1,"" ); ?></td>
                                   <td>
                                    <input type="text" id="txtconsdzngmts_1"  name="txtconsdzngmts_1" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("cons_dzn_gmts")];?>" onChange="calculate_trim_cost( 1 )" /><!--onDblClick="open_calculator(1)"-->
                                    </td>
                                   <td>
                                    <input type="text" id="txttrimrate_1"  name="txttrimrate_1" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("purchase_rate")];?>" onChange="calculate_trim_cost( 1 )"/>
                                    </td>
                                    <td>
                                    <input type="text" id="txttrimamount_1"  name="txttrimamount_1" class="text_boxes_numeric" style="width:95px" value="<? echo $row[csf("amount")];?>"  readonly />
                                    </td>
                                    <td width="95">
									<? echo create_drop_down( "cboapbrequired_1", 80, $yes_no,"", 0, "0", $row[csf("apvl_req")], '','','' );  ?>
                                    </td>  
                                    
                                    <td width="95">
									<? //echo create_drop_down( "cbonominasupplier_1", 80, $row_status,"", 0, "0", $row[csf("supplyer")], '','','' );  
                                    echo create_drop_down( "cbonominasupplier_1",80, "select a.supplier_name,a.id from  lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(4,5) and a.is_deleted=0  and a.status_active=1 group by a.id order by a.supplier_name", "id,supplier_name", 1, '-Select-', $row[csf("supplyer")],"","","" );
									?>
                                    </td>  
                                  
                                    <td width="95">
									<? echo create_drop_down( "cbotrimstatus_1", 80, $row_status,"", 0, "0", '', '','','' );  ?>
                                    </td>  
                                    <td>
                                    <input type="button" id="increasetrim_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_trim_cost(1 )" />
                                    <input type="button" id="decreasetrim_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1 ,'tbl_trim_cost' );" />
                                    <input type="hidden" id="updateidtrim_1" name="updateidtrim_1"  class="text_boxes" style="width:20px" value=""  />
                                    
                                   </td> 
                                </tr>
                    <?
					}
					}
					?>
                </tbody>
                </table>
                <table width="880" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                            <th width="201">
                            Sum
                            </th>
                            <th  width="92">
                            <input type="text" id="txtconsdzntrim_sum"  name="txtconsdzntrim_sum" class="text_boxes_numeric" style="width:80px"  readonly />
                            </th>
                            <th width="92"> 
                            <input type="text" id="txtratetrim_sum"  name="txtratetrim_sum" class="text_boxes_numeric" style="width:80px"  readonly />
                            </th>
                            <th width="110">
                             <input type="text" id="txttrimamount_sum"  name="txttrimamount_sum" class="text_boxes_numeric" style="width:97px"  readonly />
                            </th>
                            <th width="95">
                            </th>
                            <th width="">
                            </th>
                        </tr>
                    </tfoot>
                </table>
                           
                <table width="880" cellspacing="0" class="" border="0">
                
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
                        
						<?
						if ( count($data_array)>0)
					    {
						echo load_submit_buttons( $permission, "fnc_trim_cost_dtls", $save_update,0,"reset_form('trimccost_6','','',0)",6) ;
					    }
						else
						{
						echo load_submit_buttons( $permission, "fnc_trim_cost_dtls", $save_update,0,"reset_form('trimccost_6','','',0)",6) ;
						}
						?>  
                        </td> 
                    </tr>
                </table>
               
            </form>
        </fieldset>
        </div>

<?
}

if ($action=="set_cons_uom")
{
	$cons_uom=return_field_value("trim_uom", "lib_item_group", "id=$data");
	echo $cons_uom; die;
}
if($action=="calculator_parameter")
{
	$cal_parameter_type=return_field_value("cal_parameter", "lib_item_group", "id='$data'");
	echo trim($cal_parameter_type); die;
}

if($action=="calculator_type")
{
   echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode,'','');
   extract($_REQUEST);

?>
<script>
function clculate_cons_for_mtr()
{
  var txt_cons_per_gmts=(document.getElementById('txt_cons_per_gmts').value)*1;	
  var txt_costing_per=document.getElementById('txt_costing_per').value;	
  if(txt_costing_per==1)
  {
  document.getElementById('txt_cons_for_mtr').value=txt_cons_per_gmts*12;	
  }
  if(txt_costing_per==2)
  {
  document.getElementById('txt_cons_for_mtr').value=txt_cons_per_gmts*1;	
  }
  if(txt_costing_per==3)
  {
  document.getElementById('txt_cons_for_mtr').value=txt_cons_per_gmts*2*12;	
  }
  if(txt_costing_per==4)
  {
  document.getElementById('txt_cons_for_mtr').value=txt_cons_per_gmts*3*12;	
  }
  if(txt_costing_per==5)
  {
  document.getElementById('txt_cons_for_mtr').value=txt_cons_per_gmts*4*12;	
  }
  var txt_cons_for_mtr= (document.getElementById('txt_cons_for_mtr').value)*1
  var txt_cons_length= (document.getElementById('txt_cons_length').value)*1
  document.getElementById('txt_cons_for_cone').value=txt_cons_for_mtr/txt_cons_length;	
}
function js_set_value_calculator(type)
{
	if(type=='sewing_thread')
	{
		var clacolator_param_value=document.getElementById('txt_cons_per_gmts').value+'*'+document.getElementById('txt_cons_for_mtr').value+'*'+document.getElementById('txt_cons_length').value+'*'+document.getElementById('txt_cons_for_cone').value+'*'+document.getElementById('txt_costing_per').value;
		
		document.getElementById('txt_clacolator_param_value').value=clacolator_param_value;
		
	}
	
		parent.emailwindow.hide();

	
}
</script>
</head>
<body>
<?
	if($calculator_parameter==1)
	{
		
		?>
        <fieldset>
        <legend>Sewing Thread</legend>
        <table cellpadding="0" cellspacing="2" align="center" width="300">
        <tr>
        <td width="120">
        Cons Per Garment
        </td>
        <td width="">
        <input type="text" id="txt_cons_per_gmts" name="txt_cons_per_gmts" class="text_boxes_numeric" onChange="clculate_cons_for_mtr()" /> Mtr
        </td>
        </tr>
        <tr>
        <td>
        Cons <? echo $costing_per[$cbo_costing_per];?>
        </td>
        <td>
        <input type="text" id="txt_cons_for_mtr" name="txt_cons_for_mtr" class="text_boxes_numeric"  readonly/> Mtr
        </td>
        </tr>
        <tr>
        <td>
        Cone Length
        </td>
        <td>
        <input type="text" id="txt_cons_length" name="txt_cons_length" class="text_boxes_numeric"  onChange="clculate_cons_for_mtr()" value="4000"/> Mtr
        </td>
        </tr>
        <tr>
        <td>
        Cons  <? echo $costing_per[$cbo_costing_per];?>
        </td>
        <td>
        <input type="text" id="txt_cons_for_cone" name="txt_cons_for_cone" class="text_boxes_numeric" readonly /> Cone
        </td>
        </tr>
         <tr>
        
        <td colspan="3" align="center">
        <input type="button" class="formbutton" value="Close" onClick="js_set_value_calculator('sewing_thread')"/> 
        <input type="hidden" id="txt_costing_per" name="txt_costing_per" class="text_boxes_numeric" value="<? echo $cbo_costing_per; ?>" readonly /> 
        <input type="hidden" id="txt_clacolator_param_value" name="txt_clacolator_param_value" class="text_boxes_numeric" value="" readonly /> 

        </td>
        </tr>
        
        </table>
        </fieldset>
        <?
		
	}
	?>
 </body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
    
<?
}
if ($action=="save_update_delet_trim_cost_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if($operation==0)
	{
	$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		 $id=return_next_id( "id", "wo_pri_quo_trim_cost_dtls", 1 ) ;
		 $field_array="id, quotation_id, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, inserted_by, insert_date, status_active,	is_deleted";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cbogroup="cbogroup_".$i;
			 $cboconsuom="cboconsuom_".$i;
			 $txtconsdzngmts="txtconsdzngmts_".$i;
			 $txttrimrate="txttrimrate_".$i;
			 $txttrimamount="txttrimamount_".$i;
			 $cboapbrequired="cboapbrequired_".$i;
			 $cbonominasupplier="cbonominasupplier_".$i;
			 $cbotrimstatus="cbotrimstatus_".$i;
			 $updateidtrim="updateidtrim_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$update_id.",".$$cbogroup.",".$$cboconsuom.",".$$txtconsdzngmts.",".$$txttrimrate.",".$$txttrimamount.",".$$cboapbrequired.",".$$cbonominasupplier.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbotrimstatus.",0)";
			$id=$id+1;
		 }
		 $rID=sql_insert("wo_pri_quo_trim_cost_dtls",$field_array,$data_array,0);
		 //=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,trim_cons,trim_rate,trim_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtconsdzntrim_sum.",".$txtratetrim_sum.",".$txttrimamount_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}
		
		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="trim_cons*trim_rate*trim_amount";
			$data_array2 ="".$txtconsdzntrim_sum."*".$txtratetrim_sum."*".$txttrimamount_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		//=======================sum End =================
		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			echo "0**".$new_job_no[0]."**".$rID;
		}
		disconnect($con);
		die;
	}
	
	if($operation==1)
	{
	$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		 $field_array_up="quotation_id*trim_group*cons_uom*cons_dzn_gmts*rate*amount*apvl_req*nominated_supp*updated_by*update_date*status_active*is_deleted";
		 $field_array="id, quotation_id, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, inserted_by, insert_date, status_active, is_deleted";
		 $add_co=0;
		 $id=return_next_id( "id","wo_pri_quo_trim_cost_dtls", 1 );
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cbogroup="cbogroup_".$i;
			 $cboconsuom="cboconsuom_".$i;
			 $txtconsdzngmts="txtconsdzngmts_".$i;
			 $txttrimrate="txttrimrate_".$i;
			 $txttrimamount="txttrimamount_".$i;
			 $cboapbrequired="cboapbrequired_".$i;
			 $cbonominasupplier="cbonominasupplier_".$i;
			 $cbotrimstatus="cbotrimstatus_".$i;
			 $updateidtrim="updateidtrim_".$i;
			if(str_replace("'",'',$$updateidtrim)!="")
			{
                $id_arr[]=str_replace("'",'',$$updateidtrim);
				$data_array_up[str_replace("'",'',$$updateidtrim)] =explode(",",("".$update_id.",".$$cbogroup.",".$$cboconsuom.",".$$txtconsdzngmts.",".$$txttrimrate.",".$$txttrimamount.",".$$cboapbrequired.",".$$cbonominasupplier.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbotrimstatus.",0"));			}
			if(str_replace("'",'',$$updateidtrim)=="")
			{
				if ($add_co!=0) $data_array .=",";
				$data_array .="(".$id.",".$update_id.",".$$cbogroup.",".$$cboconsuom.",".$$txtconsdzngmts.",".$$txttrimrate.",".$$txttrimamount.",".$$cboapbrequired.",".$$cbonominasupplier.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbotrimstatus.",0)";
			   $id=$id+1;
		       $add_co++;
			}
		 }
		 $rID_up=execute_query(bulk_update_sql_statement( "wo_pri_quo_trim_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		 if($data_array !="")
		 {
		  $rID=sql_insert("wo_pri_quo_trim_cost_dtls",$field_array,$data_array,0);
		 }
		  //=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,trim_cons,trim_rate,trim_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtconsdzntrim_sum.",".$txtratetrim_sum.",".$txttrimamount_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}
		
		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="trim_cons*trim_rate*trim_amount";
			$data_array2 ="".$txtconsdzntrim_sum."*".$txtratetrim_sum."*".$txttrimamount_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		update_comarcial_cost($update_id);
		//=======================sum End =================
 		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{

			if($rID_up ){
				mysql_query("COMMIT");  
				echo "1**".$new_job_no[0]."**".$rID_up;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID_up;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			echo "1**".$new_job_no[0]."**".$rID_up;
		}
		disconnect($con);
		die;
	}
}
// Trim Cost End =========================================================================================================================================================

?>


<?
// Embellisment Cost  =========================================================================================================================================================
if ($action=="show_embellishment_cost_listview")
{
	$header_td="";
	$costing_per=return_field_value("costing_per", "wo_price_quotation", "id='$data'");
	if($costing_per==1)
	{
		$header_td="Cons/ 1 Dzn Gmts";
	}
	else if($costing_per==2)
	{
	$header_td="Cons/ 1 Pcs Gmts";	
	}
	else if($costing_per==3)
	{
	$header_td="Cons/ 2 Dzn Gmts";	
	}
	
	else if($costing_per==4)
	{
	$header_td="Cons/ 3 Dzn Gmts";	
	}
	else if($costing_per==5)
	{
	$header_td="Cons/ 4 Dzn Gmts";	
	}


	
?>
<h3 align="left" class="accordion_h" >+Embellishment Cost</h3> 
       <div id="content_embellishment_cost"  align="center">            
    	<fieldset>
        	<form id="embellishment_7" autocomplete="off">
            <!--<input type="text" id="cons_breck_down" name="cons_breck_down" value="" width="500" /> 
            <input type="text" id="msmnt_breack_down" name="msmnt_breack_down"/>-->
           <!-- <input type="hidden" id="tr_ortder" name="tr_ortder" value="" width="500" /> 
            <input type="text" id="hid_fab_cons_in_quotation_variable" name="hid_fab_cons_in_quotation_variable" value="2" width="500" />
 -->


            	<table width="800" cellspacing="0" class="rpt_table" border="0" id="tbl_embellishment_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="150">Name</th><th  width="100">Type</th><th  width="90"><? echo $header_td; ?></th><th width="90">Rate</th><th width="110">Amount</th><th width="95">Status</th><th width=""></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$approved=return_field_value("approved", "wo_price_quotation", "id='$data'");
					if($approved==1)
					{
					$disabled=1;
					//$permission=0;
					}
					else
					{
					$disabled=0;
					//$permission=$permission;
					}
					
					$data_array=sql_select("select id, quotation_id, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from  wo_pri_quo_embe_cost_dtls where emb_name!=3 and quotation_id='$data'");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
					$type_array=array(1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$blank_array);

						
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="embellishment_1" align="center">
                                    <td id="cboembnametd_<? echo $i;?>">
									<? 
									echo create_drop_down( "cboembname_".$i, 135, $emblishment_name_array, "",1," -- Select--", $row[csf("emb_name")], "cbotype_loder(".$i.")",$disabled,'1,2,4,5' ); 
									?>
                                    
                                    </td>
                                    <td id="embtypetd_<? echo $i;?>"><?  echo create_drop_down( "cboembtype_".$i, 80, $type_array[$row[csf("emb_name")]],"", 1, "-- Select --", $row[csf("emb_type")], "check_duplicate(".$i.")",$disabled,"" ); ?></td>
                                   <td id="txtembconsdzngmtstd_<? echo $i;?>">
                                    <input type="text" id="txtembconsdzngmts_<? echo $i; ?>"  name="txtembconsdzngmts_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("cons_dzn_gmts")];  ?>" onChange="calculate_emb_cost( <? echo $i;?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?>/>
                                    </td>
                                   <td id="txtembratetd_<? echo $i;?>">
                                    <input type="text" id="txtembrate_<? echo $i; ?>"  name="txtembrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("rate")];  ?>" onChange="calculate_emb_cost( <? echo $i;?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    </td>
                                    <td id="txtembamounttd_<? echo $i;?>">
                                    <input type="text" id="txtembamount_<? echo $i; ?>"  name="txtembamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:95px" value="<? echo $row[csf("amount")];  ?>"   <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    </td>
                                    
                                    <td id="cboembstatustd_<? echo $i;?>"><? echo create_drop_down( "cboembstatus_".$i, 80, $row_status,"", 0, "0", $row[csf("status_active")], '',$disabled,'' );  ?></td>  
                                    <td id="buttontd_<? echo $i;?>">
                                    <input type="button" id="increaseemb_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_embellishment_cost(<? echo $i; ?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    <input type="button" id="decreaseemb_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_embellishment_cost' );" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    <input type="hidden" id="embupdateid_<? echo $i; ?>" name="embupdateid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("id")]; ?>"  />                                      
                                     </td> 
                                </tr>
                            
                            <?
							 
						}
					}
					else
					{
						
						$type_array=array(1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$blank_array);

						$i=0;
						foreach( $emblishment_name_array as $row => $value )
						{
							$i++;
							if($i==3)
							{
								continue;
								
							}
							else
							{
								if($i>3)
								{
									$i=$i-1;
								}
							
							//echo  $row;
					?>
                    <tr id="embellishment_1" align="center">
                                   <td id="cboembnametd_<? echo $i;?>">
									<? 
									echo create_drop_down( "cboembname_".$i, 135, $emblishment_name_array, "",1,"--Select--", $row, "cbotype_loder(".$i.")",'','1,2,4,5' ); 
									?>
                                    </td>
                                    <td id="embtypetd_<? echo $i;?>"><?  echo create_drop_down( "cboembtype_".$i, 80, $type_array[$row],"", 1, "-- Select --", "", "check_duplicate(".$i.")","","" ); ?></td>
                                   <td id="txtembconsdzngmtstd_<? echo $i;?>">
                                    <input type="text" id="txtembconsdzngmts_<? echo $i; ?>"  name="txtembconsdzngmts_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="" onChange="calculate_emb_cost( <? echo $i;?> )"/>
                                    </td>
                                   <td id="txtembratetd_<? echo $i;?>">
                                    <input type="text" id="txtembrate_<? echo $i; ?>"  name="txtembrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="" onChange="calculate_emb_cost( <? echo $i;?> )"/>
                                    </td>
                                    <td id="txtembamounttd_<? echo $i;?>">
                                    <input type="text" id="txtembamount_<? echo $i; ?>"  name="txtembamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:95px" value=""   />
                                    </td>
                                    
                                    <td id="cboembstatustd_<? echo $i;?>"><? echo create_drop_down( "cboembstatus_".$i, 80, $row_status,"", 0, "0", '', '','','' );  ?></td>  
                                    <td id="buttontd_<? echo $i;?>">
                                    <input type="button" id="increaseemb_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_embellishment_cost(<? echo $i; ?> )" />
                                    <input type="button" id="decreaseemb_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_embellishment_cost' );" />
                                    <input type="hidden" id="embupdateid_<? echo $i; ?>" name="embupdateid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value=""  />                                    </td> 
                                </tr>
                    <? 
					            if($i==3)
								{
									$i++;
								}
							}
						}
					} 
					?>
                </tbody>
                </table>
                <table width="800" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                            <th width="251">Sum</th>
                            <th  width="92"> 
                           <!-- <input type="text" id="txtconsdznemb_sum"  name="txtconsdznemb_sum" class="text_boxes" style="width:80px"  readonly />-->
                            </th>
                            <th width="92">
                            <!--<input type="text" id="txtrateemb_sum"  name="txtrateemb_sum" class="text_boxes" style="width:80px"  readonly />-->
                            </th>
                            <th width="110">
                            <input type="text" id="txtamountemb_sum"  name="txtamountemb_sum" class="text_boxes_numeric" style="width:95px"  readonly />
                            </th>
                            <th width="95"></th>
                            <th width=""></th>
                        </tr>
                    </tfoot>
                </table>
                           
                <table width="800" cellspacing="0" class="" border="0">
                
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
                        
						<?
						if ( count($data_array)>0)
					    {
						echo load_submit_buttons( $permission, "fnc_embellishment_cost_dtls", 1,0,"reset_form('embellishment_7','','',0)",7) ;
					    }
						else
						{
						echo load_submit_buttons( $permission, "fnc_embellishment_cost_dtls", 0,0,"reset_form('embellishment_7','','',0)",7) ;
						}
						?>  
                        </td> 
                    </tr>
                </table>
               
            </form>
        </fieldset>
        </div>

<?
}
if ($action=="load_drop_down_embtype")
{
	$data=explode('_',$data);
	if($data[0]==1)
	{
		echo create_drop_down( "cboembtype_".$data[1], 100,$emblishment_print_type,"", 1, "-- Select --", "", "check_duplicate(".$data[1].")","","" ); 
		die;
	}
	if($data[0]==2)
	{
		echo create_drop_down( "cboembtype_".$data[1], 100,$emblishment_embroy_type,"", 1, "-- Select --", "", "check_duplicate(".$data[1].")","","" ); 
		die;
	}
	if($data[0]==3)
	{
		echo create_drop_down( "cboembtype_".$data[1], 100,$emblishment_wash_type,"", 1, "-- Select --", "", "check_duplicate(".$data[1].")","","" ); 
		die;
	}
	if($data[0]==4)
	{
		echo create_drop_down( "cboembtype_".$data[1], 100,$emblishment_spwork_type,"", 1, "-- Select --", "", "check_duplicate(".$data[1].")","","" ); 
		die;
	}
	if($data[0]==5)
	{
		echo create_drop_down( "cboembtype_".$data[1], 100,$blank_array,"", 1, "-- Select --", "", "","","" ); 
		die;
	}
}


if ($action=="save_update_delet_embellishment_cost_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if($operation==0)
	{
	     $con = connect();
		 if($db_type==0)
		 {
			mysql_query("BEGIN");
		 }
         if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}		
		 $id=return_next_id( "id", "wo_pri_quo_embe_cost_dtls", 1 ) ;
		 $field_array="id,quotation_id,emb_name,emb_type,cons_dzn_gmts,rate,amount,inserted_by,insert_date,status_active,is_deleted";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cboembname="cboembname_".$i;
			 $cboembtype="cboembtype_".$i;
			 $txtembconsdzngmts="txtembconsdzngmts_".$i;
			 $txtembrate="txtembrate_".$i;
			 $txtembamount="txtembamount_".$i;
			 $cboembstatus="cboembstatus_".$i;
			 $embupdateid="embupdateid_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$update_id.",".$$cboembname.",".$$cboembtype.",".$$txtembconsdzngmts.",".$$txtembrate.",".$$txtembamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cboembstatus.",0)";
			$id=$id+1;
		 }
		 $rID=sql_insert("wo_pri_quo_embe_cost_dtls",$field_array,$data_array,0);
		  //=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,emb_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtamountemb_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}
		
		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="emb_amount";
		    $data_array2 ="".$txtamountemb_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		//=======================sum End =================
		 check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			echo "0**".$new_job_no[0]."**".$rID;
		}
		disconnect($con);
		die;
	}
	
	if($operation==1)
	{
	$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
         if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		 $field_array_up="quotation_id*emb_name*emb_type*cons_dzn_gmts*rate*amount*updated_by*update_date*status_active*is_deleted";
		 $field_array="id,quotation_id,emb_name,emb_type,cons_dzn_gmts,rate,amount,inserted_by,insert_date,status_active,is_deleted";
		 $add_comma=0;
		 $id=return_next_id( "id", "wo_pri_quo_embe_cost_dtls", 1 ) ;
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 
			 $cboembname="cboembname_".$i;
			 $cboembtype="cboembtype_".$i;
			 $txtembconsdzngmts="txtembconsdzngmts_".$i;
			 $txtembrate="txtembrate_".$i;
			 $txtembamount="txtembamount_".$i;
			 $cboembstatus="cboembstatus_".$i;
			 $embupdateid="embupdateid_".$i;
			if(str_replace("'",'',$$embupdateid)!="")
			{
				/*$field_array="quotation_id*emb_name*emb_type*cons_dzn_gmts*rate*amount*updated_by*update_date*status_active*is_deleted";
				$data_array ="".$update_id."*".$$cboembname."*".$$cboembtype."*".$$txtembconsdzngmts."*".$$txtembrate."*".$$txtembamount."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$$cboembstatus."*0";
				$rID=sql_update("wo_pri_quo_embe_cost_dtls",$field_array,$data_array,"id","".$$embupdateid."",0);*/
				$id_arr[]=str_replace("'",'',$$embupdateid);
				$data_array_up[str_replace("'",'',$$embupdateid)] =explode(",",("".$update_id.",".$$cboembname.",".$$cboembtype.",".$$txtembconsdzngmts.",".$$txtembrate.",".$$txtembamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cboembstatus.",0"));

			}
			if(str_replace("'",'',$$embupdateid)=="")
			{
				if ($add_comma!=0) $data_array .=",";
				$data_array ="(".$id.",".$update_id.",".$$cboembname.",".$$cboembtype.",".$$txtembconsdzngmts.",".$$txtembrate.",".$$txtembamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cboembstatus.",0)";
				$id=$id+1;
				$add_comma++;
			}
		 }
		 $rID_up=execute_query(bulk_update_sql_statement( "wo_pri_quo_embe_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		 if($data_array !="")
		 {
		 $rID=sql_insert("wo_pri_quo_embe_cost_dtls",$field_array,$data_array,0);
		 }

		 //=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,emb_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtamountemb_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}
		
		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="emb_amount";
		    $data_array2 ="".$txtamountemb_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		//=======================sum End =================
		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{

			if($rID_up ){
				mysql_query("COMMIT");  
				echo "1**".$new_job_no[0]."**".$rID_up;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID_up;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			echo "1**".$new_job_no[0]."**".$rID_up;
		}
		disconnect($con);
		die;
	}
	
	
	
}
// Embellisment Cost End   =========================================================================================================================================================
?>
<?
// Wash Cost  =========================================================================================================================================================
if ($action=="show_wash_cost_listview")
{
	$data=explode("_",$data);
	$header_td="";
	$costing_per=return_field_value("costing_per", "wo_price_quotation", "id='$data[0]'");
	if($costing_per==1)
	{
		$header_td="Cons/ 1 Dzn Gmts";
	}
	else if($costing_per==2)
	{
	$header_td="Cons/ 1 Pcs Gmts";	
	}
	else if($costing_per==3)
	{
	$header_td="Cons/ 2 Dzn Gmts";	
	}
	
	else if($costing_per==4)
	{
	$header_td="Cons/ 3 Dzn Gmts";	
	}
	else if($costing_per==5)
	{
	$header_td="Cons/ 4 Dzn Gmts";	
	}

	$conversion_from_chart=return_field_value("conversion_from_chart", "variable_order_tracking", "company_name='$data[1]'  and variable_list=21 and status_active=1 and is_deleted=0");
	if($conversion_from_chart=="")
	{
		$conversion_from_chart=2;
	}
	
	
?>
<h3 align="left" class="accordion_h" >+Wash Cost</h3> 
       <div id="content_wash_cost"  align="center">            
    	<fieldset>
        	<form id="wash_7" autocomplete="off">
            	<table width="800" cellspacing="0" class="rpt_table" border="0" id="tbl_wash_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="150">Name</th><th  width="100">Type</th><th  width="90"><? echo $header_td; ?></th><th width="90">Rate</th><th width="110">Amount</th><th width="95">Status</th><th width=""></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$disabled=0;
					$approved=return_field_value("approved", "wo_price_quotation", "id='$data[0]'");
					if($approved==1)
					{
					$disabled=1;
					//$permission=0;
					}
					else
					{
					$disabled=0;
					//$permission=$permission;
					}
					
					/*if($conversion_from_chart==1)
					{
						$disabled=1;
						$select_smg="NO Need";
					}
					if($conversion_from_chart==2)
					{
						$disabled=0;
						$select_smg="-Select-";
					}*/
									
					$data_array=sql_select("select id, quotation_id, emb_name, emb_type, cons_dzn_gmts, rate, amount,charge_lib_id,status_active from  wo_pri_quo_embe_cost_dtls where emb_name=3 and quotation_id='$data[0]'");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
					//$type_array=array(1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$blank_array);

						
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="embellishment_1" align="center">
                                    <td id="cboembnametd_<? echo $i;?>">
									<? 
									echo create_drop_down( "cboembname_".$i, 135, $emblishment_name_array, "",1," -- Select--", $row[csf("emb_name")], "",1,'' ); 
									?>
                                    
                                    </td>
                                    <td id="embtypetd_<? echo $i;?>"><?  echo create_drop_down( "cboembtype_".$i, 80, $emblishment_wash_type,"", 1, "-Select-", $row[csf("emb_type")], "check_duplicate(".$i.")",$disabled,"" ); ?></td>
                                   <td id="txtembconsdzngmtstd_<? echo $i;?>">
                                    <input type="text" id="txtembconsdzngmts_<? echo $i; ?>"  name="txtembconsdzngmts_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("cons_dzn_gmts")];  ?>" onChange="calculate_wash_cost( <? echo $i;?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?>/>
                                    </td>
                                   <td id="txtembratetd_<? echo $i;?>">
                                    <input type="text" id="txtembrate_<? echo $i; ?>"  name="txtembrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("rate")];  ?>" onChange="calculate_wash_cost( <? echo $i;?> )"  <? if($disabled==0){echo "";}else{echo "disabled";}?> onClick="<? if($conversion_from_chart==1){ echo "set_wash_charge_unit_pop_up('".$i."')";}else{echo '';}?>" <? if($conversion_from_chart==1){echo "redonly";}else{echo "";}?>/>
                                    </td>
                                    <td id="txtembamounttd_<? echo $i;?>">
                                    <input type="text" id="txtembamount_<? echo $i; ?>"  name="txtembamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:95px" value="<? echo $row[csf("amount")];  ?>"   <? if($disabled==0){echo "";}else{echo "disabled";}?> readonly />
                                    </td>
                                    
                                    <td id="cboembstatustd_<? echo $i;?>"><? echo create_drop_down( "cboembstatus_".$i, 80, $row_status,"", 0, "0", $row[csf("status_active")], '',$disabled,'' );  ?></td>  
                                    <td id="buttontd_<? echo $i;?>">
                                    <input type="button" id="increaseemb_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_wash_cost(<? echo $i; ?>,<? echo $conversion_from_chart; ?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    <input type="button" id="decreaseemb_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_wash_cost' );" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    <input type="hidden" id="embupdateid_<? echo $i; ?>" name="embupdateid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("id")]; ?>"  />          
                                     <input type="hidden" id="embratelibid_<? echo $i; ?>" name="embratelibid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("charge_lib_id")]; ?>"  />                                         
                                     </td> 
                                </tr>
                            
                            <?
							 
						}
					}
					else
					{
						$i=0;
						$i++;
					?>
                    <tr id="embellishment_1" align="center">
                                   <td id="cboembnametd_<? echo $i;?>">
									<? 
									echo create_drop_down( "cboembname_".$i, 135, $emblishment_name_array, "",1,"--Select--", 3, "",1,'' ); 
									?>
                                    </td>
                                    <td id="embtypetd_<? echo $i;?>"><?  echo create_drop_down( "cboembtype_".$i, 80, $emblishment_wash_type,"", 1, "-Select-", "", "check_duplicate(".$i.")","","" ); ?></td>
                                   <td id="txtembconsdzngmtstd_<? echo $i;?>">
                                    <input type="text" id="txtembconsdzngmts_<? echo $i; ?>"  name="txtembconsdzngmts_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="" onChange="calculate_wash_cost( <? echo $i;?> )"/>
                                    </td>
                                   <td id="txtembratetd_<? echo $i;?>">
                                    <input type="text" id="txtembrate_<? echo $i; ?>"  name="txtembrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="" onChange="calculate_wash_cost( <? echo $i;?> )" onClick="<? if($conversion_from_chart==1){ echo "set_wash_charge_unit_pop_up(1)";}else{echo '';}?>" <? if($conversion_from_chart==1){echo "redonly";}else{echo "";}?> />
                                    </td>
                                    <td id="txtembamounttd_<? echo $i;?>">
                                    <input type="text" id="txtembamount_<? echo $i; ?>"  name="txtembamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:95px" readonly value=""   />
                                    </td>
                                    
                                    <td id="cboembstatustd_<? echo $i;?>"><? echo create_drop_down( "cboembstatus_".$i, 80, $row_status,"", 0, "0", '', '','','' );  ?></td>  
                                    <td id="buttontd_<? echo $i;?>">
                                    <input type="button" id="increaseemb_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_wash_cost(<? echo $i; ?>,<? echo $conversion_from_chart; ?> )" />
                                    <input type="button" id="decreaseemb_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_wash_cost' );" />
                                    <input type="hidden" id="embupdateid_<? echo $i; ?>" name="embupdateid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value=""  />
                                    <input type="hidden" id="embratelibid_<? echo $i; ?>" name="embratelibid_<? echo $i; ?>"  class="text_boxes" style="width:20px"  readonly/>                                     
                                 </td> 
                                </tr>
                    <? 
					} 
					?>
                </tbody>
                </table>
                <table width="800" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>

                    	<tr>
                            <th width="251">Sum</th>
                            <th  width="92"> 
                            </th>
                            <th width="92">
                            </th>
                            <th width="110">
                            <input type="text" id="txtamountemb_sum"  name="txtamountemb_sum" class="text_boxes_numeric" style="width:95px"  readonly />
                            </th>
                            <th width="95"></th>
                            <th width=""></th>
                        </tr>
                    </tfoot>
                </table>
                           
                <table width="800" cellspacing="0" class="" border="0">
                
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
                        
						<?
						if ( count($data_array)>0)
					    {
						echo load_submit_buttons( $permission, "fnc_wash_cost_dtls", 1,0,"reset_form('wash_7','','',0)",7) ;
					    }
						else
						{
						echo load_submit_buttons( $permission, "fnc_wash_cost_dtls", 0,0,"reset_form('wash_7','','',0)",7) ;
						}
						?>  
                        </td> 
                    </tr>
                </table>
               
            </form>
        </fieldset>
        </div>

<?
}

if($action=="wash_chart_popup")
{
echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
extract($_REQUEST);

?>
<script> 
function js_set_value(id,rate)
{
	//var data=data.split("_");
	document.getElementById('charge_id').value=id;
	document.getElementById('charge_value').value=rate;
	parent.emailwindow.hide();

}
function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
		}
</script> 
</head>
<body>
<div align="center">
<form>
<input type="hidden" id="charge_id" name="charge_id" />
<input type="hidden" id="charge_value" name="charge_value" />



<?


	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");  
	$arr=array (0=>$company_arr,2=>$process_type,3=>$conversion_cost_head_array,4=>$color_library_arr,5=>$fabric_typee,7=>$unit_of_measurement,8=>$production_process,9=>$row_status);
	//echo "select id,comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id in (3,4,8) and process_id=$cbotypeconversion and comapny_id=$cbo_company_name";
	//echo  create_list_view ( "list_view", "Company Name,Const. Compo.,Process Type,Process Name,Color,Width/Dia type,In House Rate,UOM,Rate type,Status", "100,150,70,70,70,80,60,80,60,50","900","250",1, "select id,comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id =7  and comapny_id=$cbo_company_name", "js_set_value", "id,in_house_rate","", 1, "comapny_id,0,process_type_id,process_id,color_id,width_dia_id,0,uom_id,rate_type_id,status_active", $arr, "comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active","requires/lib_subcontract_dyeing_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,2,0,0,0' );
	?>
    <table width="900" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0">
     <thead>
     <th width="50">SL</th>
     <th width="100">Company Name</th>
     <th width="150">Const. Compo.</th>
     <th width="70">Process Type</th>
     <th width="70">Process Name</th>
     <th width="70">Color</th>
     <th width="80">Width/Dia type</th>
     
     <th width="60">In House Rate</th>
     <th width="80">UOM</th>
     <th width="60">Rate type</th>
     <th>Status</th>
     </thead>
     </table>
     <div style=" width:917; overflow:scroll-y; max-height:300px">
     <table width="900" class="rpt_table" border="1" rules="all" id="list_view" cellpadding="0" cellspacing="0">
     <?
	 $sql_data=sql_select("select id,comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id =7  and comapny_id=$cbo_company_name");
	 $i=1;
	 foreach($sql_data as $row)
	 {
		 if ($i%2==0)  
		$bgcolor="#E9F3FF";
		else
		$bgcolor="#FFFFFF";	
		
	 ?>
     <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value(<? echo $row[csf("id")]?>, <? echo $row[csf("in_house_rate")]; ?>)" id="tr_<? echo $row[csf("id")];  ?>">
     <td width="50"><? echo $i; ?></td>
     <td width="100"><? echo $company_arr[$row[csf("comapny_id")]]; ?></td>
     <td width="150"><? echo $row[csf("const_comp")]; ?></td>
     <td width="70"><? echo $process_type[$row[csf("process_type_id")]]; ?></td>
      <td width="70"><? echo $conversion_cost_head_array[$row[csf("process_id")]]; ?></td>
     <td width="70"><? echo $color_library_arr[$row[csf("color_id")]]; ?></td>
     <td width="80"><? echo $fabric_typee[$row[csf("width_dia_id")]]; ?></td>
     
     <td width="60"><? echo $row[csf("in_house_rate")]; ?></td>
     <td width="80"><? echo $unit_of_measurement[$row[csf("uom_id")]]; ?></td>
     <td width="60"><? echo $production_process[$row[csf("rate_type_id")]]; ?></td>
     <td><? echo $row_status[$row[csf("status_active")]]; ?></td>
     </tr>
     <?
	  $i++;
	 }
	 ?>
     <script>
	  setFilterGrid("list_view",-1)
	 toggle( "tr_"+"<? echo $embratelibid ?>", '#FFFFCC');
	 </script>
     </table>
     </div>
    <?

?>
</form>
</div>
</body>
</html>
<?
	
}

if ($action=="save_update_delet_wash_cost_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if($operation==0)
	{
	     $con = connect();
		 if($db_type==0)
		 {
			mysql_query("BEGIN");
		 }
         if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}		
		 $id=return_next_id( "id", "wo_pri_quo_embe_cost_dtls", 1 ) ;
		 $field_array="id,quotation_id,emb_name,emb_type,cons_dzn_gmts,rate,amount,charge_lib_id,inserted_by,insert_date,status_active,is_deleted";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cboembname="cboembname_".$i;
			 $cboembtype="cboembtype_".$i;
			 $txtembconsdzngmts="txtembconsdzngmts_".$i;
			 $txtembrate="txtembrate_".$i;
			 $txtembamount="txtembamount_".$i;
			 $cboembstatus="cboembstatus_".$i;
			 $embupdateid="embupdateid_".$i;
			 $embratelibid="embratelibid_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$update_id.",".$$cboembname.",".$$cboembtype.",".$$txtembconsdzngmts.",".$$txtembrate.",".$$txtembamount.",".$$embratelibid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cboembstatus.",0)";
			$id=$id+1;
		 }
		 $rID=sql_insert("wo_pri_quo_embe_cost_dtls",$field_array,$data_array,0);
		  //=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,wash_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtamountemb_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}
		
		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="wash_amount";
		    $data_array2 ="".$txtamountemb_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		//=======================sum End =================
		 check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			echo "0**".$new_job_no[0]."**".$rID;
		}
		disconnect($con);
		die;
	}
	
	if($operation==1)
	{
	$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
         if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		 $field_array_up="quotation_id*emb_name*emb_type*cons_dzn_gmts*rate*amount*charge_lib_id*updated_by*update_date*status_active*is_deleted";
		 $field_array="id,quotation_id,emb_name,emb_type,cons_dzn_gmts,rate,amount,charge_lib_id,inserted_by,insert_date,status_active,is_deleted";
		 $add_comma=0;
		 $id=return_next_id( "id", "wo_pri_quo_embe_cost_dtls", 1 ) ;
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 
			 $cboembname="cboembname_".$i;
			 $cboembtype="cboembtype_".$i;
			 $txtembconsdzngmts="txtembconsdzngmts_".$i;
			 $txtembrate="txtembrate_".$i;
			 $txtembamount="txtembamount_".$i;
			 $cboembstatus="cboembstatus_".$i;
			 $embupdateid="embupdateid_".$i;
			 $embratelibid="embratelibid_".$i;
			if(str_replace("'",'',$$embupdateid)!="")
			{
				/*$field_array="quotation_id*emb_name*emb_type*cons_dzn_gmts*rate*amount*updated_by*update_date*status_active*is_deleted";
				$data_array ="".$update_id."*".$$cboembname."*".$$cboembtype."*".$$txtembconsdzngmts."*".$$txtembrate."*".$$txtembamount."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$$cboembstatus."*0";
				$rID=sql_update("wo_pri_quo_embe_cost_dtls",$field_array,$data_array,"id","".$$embupdateid."",0);*/
				$id_arr[]=str_replace("'",'',$$embupdateid);
				$data_array_up[str_replace("'",'',$$embupdateid)] =explode(",",("".$update_id.",".$$cboembname.",".$$cboembtype.",".$$txtembconsdzngmts.",".$$txtembrate.",".$$txtembamount.",".$$embratelibid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cboembstatus.",0"));

			}
			if(str_replace("'",'',$$embupdateid)=="")
			{
				if ($add_comma!=0) $data_array .=",";
				$data_array .="(".$id.",".$update_id.",".$$cboembname.",".$$cboembtype.",".$$txtembconsdzngmts.",".$$txtembrate.",".$$txtembamount.",".$$embratelibid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cboembstatus.",0)";
				$id=$id+1;
				$add_comma++;
			}
		 }
		 $rID_up=execute_query(bulk_update_sql_statement( "wo_pri_quo_embe_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		// echo 	$data_array;
		 if($data_array !="")
		 {
		 $rID=sql_insert("wo_pri_quo_embe_cost_dtls",$field_array,$data_array,0);
		 }

		 //=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,wash_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtamountemb_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}
		
		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="wash_amount";
		    $data_array2 ="".$txtamountemb_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		//=======================sum End =================
		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{

			if($rID_up ){
				mysql_query("COMMIT");  
				echo "1**".$new_job_no[0]."**".$rID_up;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID_up;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			echo "1**".$new_job_no[0]."**".$rID_up;
		}
		disconnect($con);
		die;
	}
	
	
	
}
//Wash Cost End======================================================================================================================================================
// Commision Cost  =========================================================================================================================================================
if ($action=="show_commission_cost_listview")
{
	
?>
<h3 align="left" class="accordion_h">+Commission Cost</h3> 
       <div id="content_commission_cost" align="center">            
    	<fieldset>
        	<form id="commission_8" autocomplete="off">
            	<table width="700" cellspacing="0" class="rpt_table" border="0" id="tbl_commission_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="150">Particulars</th><th  width="100">Commn. Base</th><th width="90">Commn Rate</th><th width="110">Amount</th><th width="95">Status</th><th width=""></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$approved=return_field_value("approved", "wo_price_quotation", "id='$data'");
					if($approved==1)
					{
					$disabled=1;
					}
					else
					{
					$disabled=0;
					}
					$data_array=sql_select("select id, quotation_id, particulars_id, commission_base_id, commision_rate,commission_amount,status_active from  wo_pri_quo_commiss_cost_dtls where quotation_id='$data'");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="commissiontr_1" align="center">
                                    <td>
									<? 
									echo create_drop_down( "cboparticulars_".$i, 135, $commission_particulars, "",1," -- Select Item --", $row[csf("particulars_id")], "",$disabled,'' ); 
									//create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index )
									?>
                                    
                                    </td>
                                    <td><?  echo create_drop_down( "cbocommissionbase_".$i, 80, $commission_base_array,"", 1, "-- Select --", $row[csf("commission_base_id")], "calculate_commission_cost(".$i.")",$disabled,"" ); ?></td>
                                   
                                   <td>
                                    <input type="text" id="txtcommissionrate_<? echo $i; ?>"  name="txtcommissionrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("commision_rate")];  ?>" onChange="calculate_commission_cost( <? echo $i;?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    </td>
                                    <td>
                                    <input type="text" id="txtcommissionamount_<? echo $i; ?>"  name="txtcommissionamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:95px" value="<? echo $row[csf("commission_amount")];  ?>"  <? if($disabled==0){echo "";}else{echo "disabled";}?>  />
                                    </td>
                                    
                                    <td width="95"><? echo create_drop_down( "cbocommissionstatus_".$i, 80, $row_status,"", 0, "0", $row[csf("status_active")], '',$disabled,'' );  ?></td>  
                                    <td>
                                   
                                    <input type="text" id="commissionupdateid_<? echo $i; ?>" name="commissionupdateid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("id")]; ?>"  />                                      
                                     </td> 
                                </tr>
                            
                            <?
							 
						}
					}
					else
					{
					?>
                    <tr id="commissiontr_1" align="center">
                                   <td>
									<? 
									echo create_drop_down( "cboparticulars_1", 135, $commission_particulars, "",0,"", 1, '','','' ); 
									?>
                                    </td>
                                    <td><?  echo create_drop_down( "cbocommissionbase_1", 80, $commission_base_array,"", 1, "-- Select --", "", "calculate_commission_cost(1)","","" ); ?></td>
                                   
                                   <td>
                                    <input type="text" id="txtcommissionrate_1"  name="txtcommissionrate_1" class="text_boxes_numeric" style="width:80px" value="" onChange="calculate_commission_cost(1 )"/>
                                    </td>
                                    <td>
                                    <input type="text" id="txtcommissionamount_1"  name="txtcommissionamount_1" class="text_boxes_numeric" style="width:95px" value=""   />
                                    </td>
                                    
                                    <td width="95"><? echo create_drop_down( "cbocommissionstatus_1", 80, $row_status,"", 0, "0", '', '','','' );  ?></td>  
                                    <td>
                                    <!--<input type="button" id="increasecommission_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_commission_cost(1 )" />
                                    <input type="button" id="decreasecommission_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1 ,'tbl_commission_cost' );" />-->
                                    <input type="hidden" id="commissionupdateid_1" name="commissionupdateid_1"  class="text_boxes" style="width:20px" value=""  />                                    </td> 
                                </tr>
                                <tr id="commissiontr_2" align="center">
                                   <td>
									<? 
									echo create_drop_down( "cboparticulars_2", 135, $commission_particulars, "",0,"", 2, '','','' ); 
									?>
                                    </td>
                                    <td><?  echo create_drop_down( "cbocommissionbase_2", 80, $commission_base_array,"", 1, "-- Select --", "", "calculate_commission_cost(2)","","" ); ?></td>
                                   
                                   <td>
                                    <input type="text" id="txtcommissionrate_2"  name="txtcommissionrate_2" class="text_boxes_numeric" style="width:80px" value="" onChange="calculate_commission_cost(2)"/>
                                    </td>
                                    <td>
                                    <input type="text" id="txtcommissionamount_2"  name="txtcommissionamount_2" class="text_boxes_numeric" style="width:95px" value=""   />
                                    </td>
                                    
                                    <td width="95"><? echo create_drop_down( "cbocommissionstatus_2", 80, $row_status,"", 0, "0", '', '','','' );  ?></td>  
                                    <td>
                                    <!--<input type="button" id="increasecommission_2" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_commission_cost(2 )" />
                                    <input type="button" id="decreasecommission_2" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(2 ,'tbl_commission_cost' );" />-->
                                    <input type="hidden" id="commissionupdateid_2" name="commissionupdateid_2"  class="text_boxes" style="width:20px" value=""  />                                    </td> 
                                </tr>
                    <? 
					
					} 
					?>
                </tbody>
                </table>
                <table width="700" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                            <th width="251">Sum</th>
                            
                            <th width="92">
                            <input type="text" id="txtratecommission_sum"  name="txtratecommission_sum" class="text_boxes_numeric" style="width:80px"  readonly />
                            </th>
                            <th width="110">
                            <input type="text" id="txtamountcommission_sum"  name="txtamountcommission_sum" class="text_boxes_numeric" style="width:95px"  readonly />
                            </th>
                            <th width="95"></th>
                            <th width=""></th>
                        </tr>
                    </tfoot>
                </table>
                           
                <table width="800" cellspacing="0" class="" border="0">
                
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
                        
						<?
						if ( count($data_array)>0)
					    {
						echo load_submit_buttons( $permission, "fnc_commission_cost_dtls", 1,0,"reset_form('commission_8','','',0)",7) ;
					    }
						else
						{
						echo load_submit_buttons( $permission, "fnc_commission_cost_dtls", 0,0,"reset_form('commission_8','','',0)",7) ;
						}
						?>  
                        </td> 
                    </tr>
                </table>
               
            </form>
        </fieldset>
        </div>

<?
}
if ($action=="save_update_delet_commission_cost_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if($operation==0)
	{
	$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		 $id=return_next_id( "id", "wo_pri_quo_commiss_cost_dtls", 1 ) ;
		 $field_array="id,quotation_id,particulars_id,commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,status_active,is_deleted ";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cboparticulars="cboparticulars_".$i;
			 $cbocommissionbase="cbocommissionbase_".$i;
			 $txtcommissionrate="txtcommissionrate_".$i;
			 $txtcommissionamount="txtcommissionamount_".$i;
			 $cbocommissionstatus="cbocommissionstatus_".$i;
			 $commissionupdateid="commissionupdateid_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$update_id.",".$$cboparticulars.",".$$cbocommissionbase.",".$$txtcommissionrate.",".$$txtcommissionamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbocommissionstatus.",0)";
			$id=$id+1;
		 }
		 
		 $rID=sql_insert("wo_pri_quo_commiss_cost_dtls",$field_array,$data_array,0);
		 //=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,commis_rate,commis_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtratecommission_sum.",".$txtamountcommission_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}
		
		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="commis_rate*commis_amount";
		    $data_array2 ="".$txtratecommission_sum."*".$txtamountcommission_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		//=======================sum End =================
		 check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			echo "0**".$new_job_no[0]."**".$rID;
		}
		disconnect($con);
		die;
	}
	
	if($operation==1)
	{
	$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		 $field_array_up="particulars_id*commission_base_id*commision_rate*commission_amount*updated_by*update_date*status_active*is_deleted ";
		 $field_array="id,quotation_id,particulars_id,commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,status_active,is_deleted ";
		 $add_comma=0;
		 $id=return_next_id( "id", "wo_pri_quo_commiss_cost_dtls", 1 ) ;
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 
			 $cboparticulars="cboparticulars_".$i;
			 $cbocommissionbase="cbocommissionbase_".$i;
			 $txtcommissionrate="txtcommissionrate_".$i;
			 $txtcommissionamount="txtcommissionamount_".$i;
			 $cbocommissionstatus="cbocommissionstatus_".$i;
			 $commissionupdateid="commissionupdateid_".$i;
			if(str_replace("'",'',$$commissionupdateid)!="")
			{
				/*$field_array="particulars_id*commission_base_id*commision_rate*commission_amount*updated_by*update_date*status_active*is_deleted ";
			    $data_array="".$$cboparticulars."*".$$cbocommissionbase."*".$$txtcommissionrate."*".$$txtcommissionamount."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$$cbocommissionstatus."*0";
				$rID=sql_update("wo_pri_quo_commiss_cost_dtls",$field_array,$data_array,"id","".$$commissionupdateid."",0);*/
				$id_arr[]=str_replace("'",'',$$commissionupdateid);
			    $data_array_up[str_replace("'",'',$$commissionupdateid)]=explode(",",("".$$cboparticulars.",".$$cbocommissionbase.",".$$txtcommissionrate.",".$$txtcommissionamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbocommissionstatus.",0"));

			}
			if(str_replace("'",'',$$commissionupdateid)=="")
			{
				//$id=return_next_id( "id", "wo_pri_quo_commiss_cost_dtls", 1 ) ;
		        //$field_array="id,quotation_id,particulars_id,commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,status_active,is_deleted ";
			    $data_array="(".$id.",".$update_id.",".$$cboparticulars.",".$$cbocommissionbase.",".$$txtcommissionrate.",".$$txtcommissionamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbocommissionstatus.",0)";
		       // $rID=sql_insert("wo_pri_quo_commiss_cost_dtls",$field_array,$data_array,0);
			}

		 }
         $rID_up=execute_query(bulk_update_sql_statement( "wo_pri_quo_commiss_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		 if($data_array !="")
		 {
		 $rID=sql_insert("wo_pri_quo_commiss_cost_dtls",$field_array,$data_array,0);
		 }
		 //=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,commis_rate,commis_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtratecommission_sum.",".$txtamountcommission_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}
		
		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="commis_rate*commis_amount";
		    $data_array2 ="".$txtratecommission_sum."*".$txtamountcommission_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		//=======================sum End =================
 		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{

			if($rID_up ){
				mysql_query("COMMIT");  
				echo "1**".$new_job_no[0]."**".$rID_up;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID_up;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			echo "1**".$new_job_no[0]."**".$rID_up;
		}
		disconnect($con);
		die;
	}
}
// Commision Cost End  =========================================================================================================================================================
?>

<?
// Comarcial Cost End  =========================================================================================================================================================
if ($action=="show_comarcial_cost_listview")
{
?>
<h3 align="left" class="accordion_h" >+Commercial Cost</h3> 
       <div id="content_comarcial_cost"  align="center">            
    	<fieldset>
        	<form id="comarcial_9" autocomplete="off">
            	<table width="800" cellspacing="0" class="rpt_table" border="0" id="tbl_comarcial_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="150">Item</th><th width="90">Comml Rate</th><th width="110">Amount</th><th width="95">Status</th><th width=""></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$approved=return_field_value("approved", "wo_price_quotation", "id='$data'");
					if($approved==1)
					{
					$disabled=1;
					//$permission=0;
					}
					else
					{
					$disabled=0;
					//$permission=$permission;
					}
					$data_array=sql_select("select id, quotation_id, item_id, base_id, rate,amount,status_active from  wo_pri_quo_comarcial_cost_dtls where quotation_id='$data'");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="comarcialtr_1" align="center">
                                    <td>
									<? 
									echo create_drop_down( "cboitem_".$i, 135, $camarcial_items, "",1," -- Select Item --", $row[csf("item_id")], "",$disabled,'' ); 
									?>
                                    
                                    </td>
                                    
                                   
                                   <td>
                                    <input type="text" id="txtcomarcialrate_<? echo $i; ?>"  name="txtcomarcialrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("rate")];  ?>" onChange="calculate_comarcial_cost( <? echo $i;?>,'rate' )"   />
                                    </td>
                                    <td>
                                    <input type="text" id="txtcomarcialamount_<? echo $i; ?>"  name="txtcomarcialamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:95px" value="<? echo $row[csf("amount")];  ?>" onChange="calculate_comarcial_cost( <? echo $i;?>,'amount' )" <? if($disabled==0){echo "";}else{echo "disabled";}?>  />
                                    </td>
                                    
                                    <td width="95"><? echo create_drop_down( "cbocomarcialstatus_".$i, 80, $row_status,"", 0, "0", $row[csf("status_active")], '',$disabled,'' );  ?></td>  
                                    <td>
                                    <input type="button" id="increasecomarcial_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_comarcial_cost(<? echo $i; ?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?>/>
                                    <input type="button" id="decreasecomarcial_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_comarcial_cost' );" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    <input type="hidden" id="comarcialupdateid_<? echo $i; ?>" name="comarcialupdateid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("id")]; ?>"  />                                      
                                     </td> 
                                </tr>
                            
                            <?
							 
						}
					}
					else

					{
						
					?>
                    <tr id="comarcialtr_1" align="center">
                                   <td>
									<? 
									echo create_drop_down( "cboitem_1", 135, $camarcial_items, "",0,"", 4, '','','' ); 
									?>
                                    </td>
                                   
                                   
                                   <td>
                                    <input type="text" id="txtcomarcialrate_1"  name="txtcomarcialrate_1" class="text_boxes_numeric" style="width:80px" value="" onChange="calculate_comarcial_cost(1,'rate' )"  />
                                    </td>
                                    <td>
                                    <input type="text" id="txtcomarcialamount_1"  name="txtcomarcialamount_1" class="text_boxes_numeric" style="width:95px" value="" onChange="calculate_comarcial_cost(1,'amount' )"   />
                                    </td>
                                    
                                    <td width="95"><? echo create_drop_down( "cbocomarcialstatus_1", 80, $row_status,"", 0, "0", '', '','','' );  ?></td>  
                                    <td>
                                    <input type="button" id="increasecomarcial_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_comarcial_cost(1 )" />
                                    <input type="button" id="decreasecomarcial_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1 ,'tbl_comarcial_cost' );" />
                                    <input type="hidden" id="comarcialupdateid_1" name="comarcialupdateid_1"  class="text_boxes" style="width:20px" value=""  />                                    </td> 
                                </tr>
                    <? 
					
					} 
					?>
                </tbody>
                </table>
                <table width="800" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                            <th width="151">Sum</th>
                            
                            <th width="92">
                            <input type="text" id="txtratecomarcial_sum"  name="txtratecomarcial_sum" class="text_boxes_numeric" style="width:80px"  readonly />
                            </th>
                            <th width="110">
                            <input type="text" id="txtamountcomarcial_sum"  name="txtamountcomarcial_sum" class="text_boxes_numeric" style="width:95px"  readonly />
                            </th>
                            <th width="95"></th>
                            <th width=""></th>
                        </tr>
                    </tfoot>
                </table>
                           
                <table width="800" cellspacing="0" class="" border="0">
                
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
                        
						<?
						if ( count($data_array)>0)
					    {
						echo load_submit_buttons( $permission, "fnc_comarcial_cost_dtls", 1,0,"reset_form('comarcial_9','','',0)",9) ;
					    }
						else
						{
						echo load_submit_buttons( $permission, "fnc_comarcial_cost_dtls", 0,0,"reset_form('comarcial_9','','',0)",9) ;
						}
						?>  
                        </td> 
                    </tr>
                </table>
               
            </form>
        </fieldset>
        </div>

<?
}
if($action=="sum_fab_yarn_trim_value")
{
	$amount=0;
	$data_array=sql_select("select (fab_amount+yarn_amount+trim_amount) as amount from wo_pri_quo_sum_dtls where quotation_id=$data and status_active=1 and is_deleted=0");
	foreach( $data_array as $row )
	{
		$amount=$row[csf("amount")];
	}
	echo $amount;
	die;
}
function update_comarcial_cost($quatation_id)
{
	$amount=0;
	$data_array=sql_select("select (fab_amount+yarn_amount+trim_amount) as amount from wo_pri_quo_sum_dtls where quotation_id=$quatation_id and status_active=1 and is_deleted=0");
	foreach( $data_array as $row )
	{
		$amount=def_number_format($row[csf("amount")],5,"");
	}
	
	$data_array1=sql_select("select id,rate from wo_pri_quo_comarcial_cost_dtls where quotation_id=$quatation_id and status_active=1 and is_deleted=0");
	foreach( $data_array1 as $row1 )
	{
		$com_amount=def_number_format(($amount*($row1[csf("rate")]/100)),5,"");
		$rID_de=execute_query( "update  wo_pri_quo_comarcial_cost_dtls set amount=$com_amount where id='".$row1[csf("id")]."'",1 );
	}
	
	
}

if ($action=="save_update_delet_comarcial_cost_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if($operation==0)
	{
	$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		 $id=return_next_id( "id", "wo_pri_quo_comarcial_cost_dtls", 1 ) ;
		 $field_array="id,quotation_id,item_id,rate,amount,inserted_by,insert_date,status_active,is_deleted ";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cboitem="cboitem_".$i;
			 $txtcomarcialrate="txtcomarcialrate_".$i;
			 $txtcomarcialamount="txtcomarcialamount_".$i;
			 $cbocomarcialstatus="cbocomarcialstatus_".$i;
			 $comarcialupdateid="comarcialupdateid_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$update_id.",".$$cboitem.",".$$txtcomarcialrate.",".$$txtcomarcialamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbocomarcialstatus.",0)";
			$id=$id+1;

		 }
		 $rID=sql_insert("wo_pri_quo_comarcial_cost_dtls",$field_array,$data_array,0);
		 //=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,comar_rate,comar_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtratecomarcial_sum.",".$txtamountcomarcial_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}
		
		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="comar_rate*comar_amount";
			$data_array2 ="".$txtratecomarcial_sum."*".$txtamountcomarcial_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		//=======================sum End =================
		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			echo "0**".$new_job_no[0]."**".$rID;
		}
		disconnect($con);
		die;
	}
	
	if($operation==1) // Update
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		 $field_array_up="item_id*rate*amount*updated_by*update_date*status_active*is_deleted ";
		 $field_array="id,quotation_id,item_id,rate,amount,inserted_by,insert_date,status_active,is_deleted ";
		 $add_comma=0;
		 $id=return_next_id( "id", "wo_pri_quo_comarcial_cost_dtls", 1 ) ;
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cboitem="cboitem_".$i;
			 $txtcomarcialrate="txtcomarcialrate_".$i;
			 $txtcomarcialamount="txtcomarcialamount_".$i;
			 $cbocomarcialstatus="cbocomarcialstatus_".$i;
			 $comarcialupdateid="comarcialupdateid_".$i;
			if(str_replace("'",'',$$comarcialupdateid)!="")
			{
				$id_arr[]=str_replace("'",'',$$comarcialupdateid);
			    $data_array_up[str_replace("'",'',$$comarcialupdateid)] = explode(",",("".$$cboitem.",".$$txtcomarcialrate.",".$$txtcomarcialamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbocomarcialstatus.",0"));
			}
			if(str_replace("'",'',$$comarcialupdateid)=="")
			{
			    if ($add_comma!=0) $data_array .=",";
			    $data_array .="(".$id.",".$update_id.",".$$cboitem.",".$$txtcomarcialrate.",".$$txtcomarcialamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbocomarcialstatus.",0)";
				$id=$id+1;
			    $add_comma++;
			}
		 }
 		$rID_up=execute_query(bulk_update_sql_statement( "wo_pri_quo_comarcial_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr )); 
		if($data_array != "")
		{
		 $rID=sql_insert("wo_pri_quo_comarcial_cost_dtls",$field_array,$data_array,0);
		}
		//=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,comar_rate,comar_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtratecomarcial_sum.",".$txtamountcomarcial_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}
		
		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="comar_rate*comar_amount";
			$data_array2 ="".$txtratecomarcial_sum."*".$txtamountcomarcial_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		//=======================sum End =================
		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{

			if($rID_up ){
				mysql_query("COMMIT");  
				echo "1**".$new_job_no[0]."**".$rID_up;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID_up;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			echo "1**".$new_job_no[0]."**".$rID_up;
		}
		disconnect($con);
		die;
	}
}

// Comarcial Cost End  =========================================================================================================================================================
//===============================================================================END END END====================================================================================
?>

<?php

// report generate here 
// report start

if($action=="generate_report" && $type=="preCostRpt")
{
	extract($_REQUEST);
 	$txt_quotation_date=change_date_format(str_replace("'","",$txt_quotation_date),'yyyy-mm-dd','-');
	if($txt_quotation_id=="") $quotation_id=''; else $quotation_id=" and a.id=".$txt_quotation_id."";
	if($cbo_company_name=="") $company_name=''; else $company_name=" and a.company_id=".$cbo_company_name."";
	if($cbo_buyer_name=="") $cbo_buyer_name=''; else $cbo_buyer_name=" and a.buyer_id=".$cbo_buyer_name."";
	if($txt_style_ref=="") $txt_style_ref=''; else $txt_style_ref=" and a.style_ref=".$txt_style_ref."";
	if($txt_quotation_date=="") $txt_quotation_date=''; else $txt_quotation_date=" and a.quot_date ='".$txt_quotation_date."'";
 	
	
	//array for display name
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$gsm_weight_top=return_field_value("gsm_weight", "wo_pri_quo_fabric_cost_dtls", "quotation_id=$txt_quotation_id and body_part_id=1");
	$gsm_weight_bottom=return_field_value("gsm_weight", "wo_pri_quo_fabric_cost_dtls", "quotation_id=$txt_quotation_id and body_part_id=20");
		
	 $sql = "select a.id,a.company_id ,a.buyer_id,a.style_ref,a.style_desc,a.season,a.gmts_item_id ,a.order_uom ,a.offer_qnty,a.	est_ship_date,a.op_date,DATEDIFF(est_ship_date,op_date) as date_diff,MAX(b.id) as bid,b.costing_per_id,b.a1st_quoted_price,b.revised_price,b.confirm_price,b.price_with_commn_pcs,b.terget_qty,c.fab_knit_req_kg,c.fab_woven_req_yds,c.fab_yarn_req_kg
			from wo_price_quotation a, wo_price_quotation_costing_mst b, wo_pri_quo_sum_dtls c 
			where a.id=b.quotation_id  and b.quotation_id =c.quotation_id  and a.status_active=1 $quotation_id $company_name $cbo_buyer_name $txt_style_ref $txt_quotation_date order by a.id";  
	$data_array=sql_select($sql);
	
	
	?>
    <div style="width:850px; font-size:20px; font-weight:bold" align="center"><? echo $comp[str_replace("'","",$cbo_company_name)]; ?></div>
    <div style="width:850px; font-size:14px; font-weight:bold" align="center">Quotation</div>
	<?
	foreach ($data_array as $row)
	{	
		$order_price_per_dzn=0;
		$order_job_qnty=0;
		$avg_unit_price=$row[csf("price_with_commn_pcs")];
		if($avg_unit_price==0)
		{
			$avg_unit_price=$row[csf("revised_price")];
		}
		if($avg_unit_price==0)
		{
			$avg_unit_price=$row[csf("a1st_quoted_price")];
		}

		
		$order_values = $row[csf("offer_qnty")]*$avg_unit_price;
		/*$result =sql_select("select po_number,pub_shipment_date from wo_po_break_down where job_no_mst=$txt_job_no order by pub_shipment_date DESC");
		$job_in_orders = '';$pulich_ship_date='';
		foreach ($result as $val)
		{
			$job_in_orders .= $val['po_number'].", ";
			$pulich_ship_date = $val['pub_shipment_date'];
		}
		$job_in_orders = substr(trim($job_in_orders),0,-1);*/
		?>
            	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px" rules="all">
                	<tr>
                    	<td width="80">Quotation ID</td>
                        <td width="80"><b><? echo $row[csf("id")]; ?></b></td>
                        <td width="90">Buyer</td>
                        <td width="100"><b><? echo $buyer_arr[$row[csf("buyer_id")]]; ?></b></td>
                        <td width="80">Garments Item</td>
                        <? 
							/*$grmnt_items = "";
							if($garments_item[$row[csf("gmts_item_id")]]=="")
							{
								
								$grmts_sql = sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no=$txt_job_no");
								foreach($grmts_sql as $key=>$val){
									$grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].", ";
								}
								$grmnt_items = substr_replace($grmnt_items,"",-1,1);
							}else{
								$grmnt_items = $garments_item[$row[csf("gmts_item_id")]];
							}*/
							if($row[csf("order_uom")]==1)
							{
							  $grmnt_items=$garments_item[$row[csf("gmts_item_id")]];	
							}
							else
							{
								$gmt_item=explode(',',$row[csf("gmts_item_id")]);
								foreach($gmt_item as $key=>$val)
								{
									$grmnt_items .=$garments_item[$val].", ";
								}
								
							}
							
						?>
                        <td width="100"><b><? echo $grmnt_items; ?></b></td>
                    </tr>
                    <tr>
                    	<td>Style Ref. No </td>
                        <td><b><? echo $row[csf("style_ref")]; ?></b></td>
                        <td>Order UOM </td>
                        <td><b><? echo $unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
                        <td>Offer Qnty</td>
                        <td><b><? echo $row[csf("offer_qnty")]; ?></b></td>
                    </tr>
                   
                    <tr>
                    	<td>Knit Fabric Cons</td>
                        <td><b><? echo number_format($row[csf("fab_knit_req_kg")],4); ?> (Kg)</b></td>
                        <td>Woven Fabric Cons</td>
                        <td><b><? echo number_format($row[csf("fab_woven_req_yds")],4); ?> (Yds)</b></td>
                        <td>Price Per Unit</td>
                        <td><b><? echo number_format($avg_unit_price,4); ?> USD</b></td>
                    </tr>
                    <tr>
                    	<td>Avg Yarn Req</td>
                        <td><b><? echo number_format($row[csf("fab_yarn_req_kg")],4) ?> (Kg)</b></td>
                        <td>Costing Per</td>
                        <td><b><? echo $costing_per[$row[csf("costing_per_id")]]; ?></b></td>
                        <td>Target Price </td>
                        <td><b><? echo $row[csf("terget_qty")]; ?></b></td>
                    </tr>
                     <tr>
                    	<td>GSM</td>
                        <td><b><? echo $gsm_weight_top.",".$gsm_weight_bottom; ?></b></td>
                        <td>Style Desc</td>
                        <td><b><? echo $row[csf("style_desc")]; ?></b></td>
                        <td>Season</td>
                        <td><b><? echo $row[csf("season")]; ?></b></td>
                        
                    </tr>
                    <tr>
                    	<td> OP Date </td>
                        <td><b><? echo change_date_format($row[csf("op_date")]); ?></b></td>
                    	<td> Est.Ship Date </td>
                        <td><b><? echo change_date_format($row[csf("est_ship_date")]); ?></b></td>
                        <td> Lead Time </td>
                        <td>
						<? 
						
						$dayes=$row[csf("date_diff")]+1;
						if($dayes >= 7)
						{
						$day=$dayes%7;
						$week=($dayes-$day)/7;
							if($week>1)
							{
								$week_string="Weeks";
							}
							else
							{
								$week_string="Week";
							}
							if($day>1)
							{
								$day_string="Days";
							}
							else
							{
								$day_string="Day";
							}
							if($day != 0)
							{
							echo $week." ".$week_string." ".$day." ".$day_string;
							}
							else
							{
							echo $week." ".$week_string;
							}
						}
						else
						{
						if($dayes>1)
							{
								$day_string="Days";
							}
							else
							{
								$dayes="Day";
							}	
							echo $dayes." ".$day_string;
						}
						 
						?>
                        </td>
                        
                        
                    </tr>
                </table>
            <?	
			
			if($row[csf("costing_per_id")]==1){$order_price_per_dzn=12;$costing_per=" DZN";}
			else if($row[csf("costing_per_id")]==2){$order_price_per_dzn=1;$costing_per=" PCS";}
			else if($row[csf("costing_per_id")]==3){$order_price_per_dzn=24;$costing_per=" 2 DZN";}
			else if($row[csf("costing_per_id")]==4){$order_price_per_dzn=36;$costing_per=" 3 DZN";}
			else if($row[csf("costing_per_id")]==5){$order_price_per_dzn=48;$costing_per=" 4 DZN";}
			$order_job_qnty=$row[csf("offer_qnty")];
			//$avg_unit_price=$row[csf("confirm_price")];
			
	}//end first foearch
	//start	all summary report here -------------------------------------------
	$sql = "select MAX(id),fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent ,certificate_pre_cost, certificate_percent,common_oh,common_oh_percent,total_cost ,total_cost_percent,final_cost_dzn ,final_cost_dzn_percent ,confirm_price_dzn ,confirm_price_dzn_percent,final_cost_pcs,margin_dzn,margin_dzn_percent,a1st_quoted_price,confirm_price,revised_price,price_with_commn_dzn
			from wo_price_quotation_costing_mst
			where quotation_id=".$txt_quotation_id."";
	$data_array=sql_select($sql);
	$others_cost_value=0;
 	?>

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="80">SL</td>
                    <td width="300">Particulars</td>
                    <td width="100">Cost</td>
                    <td width="100">Amount (USD)</td>
                    <td width="180">% to Ord. Value</td>                     
                </tr>
            <?
			$percent=0;
			$price_dzn=0;
            $sl=0;
            foreach( $data_array as $row )
            { 
				$sl=$sl+1;
				$price_dzn=$row[csf("confirm_price_dzn")];
  				$others_cost_value=$row[csf("total_cost")]-$row[csf("cm_cost")]-$row[csf("freight")]-$row[csf("comm_cost")]-$row[csf("commission")];
				
				$commission_base_id=return_field_value("commission_base_id", "wo_pri_quo_commiss_cost_dtls", "quotation_id=$txt_quotation_id");
				$commision_rate=return_field_value("sum(commision_rate)", "wo_pri_quo_commiss_cost_dtls", "quotation_id=$txt_quotation_id");
				if($commission_base_id==1)
				{
					$commision=($commision_rate*$row[csf("confirm_price_dzn")])/100;
				}
				if($commission_base_id==2)
				{
					$commision=$commision_rate*$order_price_per_dzn;
				}
				if($commission_base_id==3)
				{
					$commision=($commision_rate/12)*$order_price_per_dzn;
				}
?>	
                <tr>
                    <td><? echo $sl;?></td>			
                    <td align="left"><b>Order Price/<? echo $costing_per; ?></b></td>
                    <td></td>
                    <td align="right"><b><? echo number_format($row[csf("price_with_commn_dzn")],4);//$avg_unit_price*$order_price_per_dzn ?></b></td>
                    <td align="center"><? //echo "100.00%"; ?></td>
                </tr>
                <tr>
                    <td><? echo ++$sl;?></td>			
                    <td align="left"><b>Less Commision/<? echo $costing_per; ?></b></td>
                    <td></td>
                    <td align="right"><b><? echo number_format($row[csf("commission")],4);//$avg_unit_price*$order_price_per_dzn ?></b></td>
                    <td align="center"><? //echo "100.00%"; ?></td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left"><b>Net Quoted Price</b></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?  $less_commision_cost_dzn=($row[csf("price_with_commn_dzn")]-$row[csf("commission")]); echo number_format($less_commision_cost_dzn,4); ?></b></td>
                    <td align="center"><b><? echo "100.00%"; ?></b></td>
                 </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">All Fabric Cost</td>
                    <td align="right"><? echo number_format($row[csf("fabric_cost")],4); ?></td>
                    <td align="right" rowspan="12">&nbsp;</td>
                    <td align="center"><? echo number_format($row[csf("fabric_cost_percent")],2); $percent+=$row[csf("fabric_cost_percent")]; ?>%</td>
                </tr>
              
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Trims Cost</td>
                    <td align="right"><? echo number_format($row[csf("trims_cost")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("trims_cost_percent")],2);  $percent+=$row[csf("trims_cost_percent")];?>%</td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Embellishment Cost</td>
                    <td align="right"><? echo number_format($row[csf("embel_cost")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("embel_cost_percent")],2); $percent+=$row[csf("embel_cost_percent")]; ?>%</td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Commercial Cost</td>
                    <td align="right"><? echo number_format($row[csf("comm_cost")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("comm_cost_percent")],2); $percent+=$row[csf("comm_cost_percent")]; ?>%</td>
                </tr>
               <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Washing Cost (Gmt.)</td>
                    <td align="right"><? echo number_format($row[csf("wash_cost")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("wash_cost_percent")],2); $percent+=$row[csf("wash_cost_percent")]; ?>%</td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Lab Test</td>
                    <td align="right"><? echo number_format($row[csf("lab_test")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("lab_test_percent")],2); $percent+=$row[csf("lab_test_percent")];  ?>%</td>
                 </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Inspection Cost</td>
                    <td align="right"><? echo number_format($row[csf("inspection")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("inspection_percent")],2); $percent+=$row[csf("inspection_percent")]; ?>%</td>
                 </tr>
                <tr> 
                    <td><? echo ++$sl; ?></td>
                    <td align="left">CM Cost</td>
                    <td align="right"><? echo number_format($row[csf("cm_cost")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("cm_cost_percent")],2); $percent+=$row[csf("cm_cost_percent")]; ?>%</td>
                 </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Freight Cost</td>
                    <td align="right"><? echo number_format($row[csf("freight")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("freight_percent")],2); $percent+=$row[csf("freight_percent")]; ?>%</td>
                 </tr>
                 <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Currier Cost</td>
                    <td align="right"><? echo number_format($row[csf("currier_pre_cost")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("currier_percent")],2); $percent+=$row[csf("currier_percent")]; ?>%</td>
                 </tr>
                  <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Certificate Cost</td>
                    <td align="right"><? echo number_format($row[csf("certificate_pre_cost")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("certificate_percent")],2); $percent+=$row[csf("certificate_percent")]; ?>%</td>
                 </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Office OH </td>
                    <td align="right"><? echo number_format($row[csf("common_oh")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("common_oh_percent")],2); $percent+=$row[csf("common_oh_percent")];?>%</td>
                 </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left"><b>Total Cost (4-13)</b></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?  $final_cost_dzn=$row[csf("total_cost")]; echo number_format($final_cost_dzn,4); ?></b></td>
                    <td align="center"><b><?  echo number_format($percent,4);//echo number_format(($final_cost_dzn/$row[csf("total_cost")])*100,2);  ?>%</b></td>
                 </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Margin/<? echo $costing_per; ?> </td>
                    <td>&nbsp;</td>
                    <td align="right"><? $margin_dzn=$less_commision_cost_dzn-$final_cost_dzn; echo number_format($margin_dzn,4); ?></td>
                    <td align="center"><? echo  number_format(($margin_dzn/$less_commision_cost_dzn*100),4); ?></td>
                </tr>
               <!-- <tr>
                    <td><? //echo ++$sl; ?></td>
                    <td align="left">Margin/Pcs</td>
                    <td>&nbsp;</td>
                    <td align="right"><?// echo number_format($margin_dzn/$order_price_per_dzn,4); ?></td>
                    <td align="right"></td>
                </tr>-->
                <tr>
                <?
				$net_quoted_price=number_format($row[csf("confirm_price")],4);
				if($net_quoted_price=="" || $net_quoted_price==0.0000)
				{
				$net_quoted_price=number_format($row[csf("revised_price")],4);
				}
				if($net_quoted_price=="" || $net_quoted_price==0.0000)
				{
					$net_quoted_price=number_format($row[csf("a1st_quoted_price")],4);
			    }
				
				$cost_pcs=number_format($final_cost_dzn/$order_price_per_dzn,4);
				if($cost_pcs>$net_quoted_price)
				{
				$bgcolor_net_quoted_price="#FF0000";	
				$smg="Cost is hiegher than quoted price.";
				}
				?>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Net Quoted Price/ Pcs</td>
                    <td>&nbsp;</td>
                    <td align="right" bgcolor="<? echo $bgcolor_net_quoted_price;  ?>"><? echo number_format($net_quoted_price,4); ?></td>
                    <td align="right"></td>
                </tr>
                 <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Cost /Pcs</td>
                    <td>&nbsp;</td>
                    <td align="right"><? $cost_per_pice=number_format($final_cost_dzn/$order_price_per_dzn,4); echo number_format($final_cost_dzn/$order_price_per_dzn,4); ?></td>
                    <td align="center"></td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Margin/Pcs</td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format(($net_quoted_price-$cost_per_pice),4); ?></td>
                    <td align="center"></td>
                </tr>
 
            <?
                 
            }
            ?>
                 
            </table>
      </div>
      <?
	//End all summary report here -------------------------------------------
	
	
	
	//2	All Fabric Cost part here------------------------------------------- 	   	
	$sql = "select id, quotation_id, body_part_id, fab_nature_id, color_type_id,construction,composition, avg_cons, fabric_source,gsm_weight, rate, amount,avg_finish_cons,status_active   
			from wo_pri_quo_fabric_cost_dtls 
			where quotation_id=".$txt_quotation_id."";
	$data_array=sql_select($sql);
	$knit_fab="";$woven_fab="";
 		 
		$knit_subtotal_avg_cons=0;
		$knit_subtotal_amount=0;
		$woven_subtotal_avg_cons=0;
		$woven_subtotal_amount=0;
		$grand_total_amount=0;
        $i=2;$j=2;
		foreach( $data_array as $row )
        {
            if($row[csf("fab_nature_id")]==2)//knit fabrics
			{
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("construction")].", ".$row[csf("composition")].", ".$row[csf("gsm_weight")];
				$i++;	
                $knit_fab .= '<tr>
                    <td align="left">'.$item_descrition.'</td>
                    <td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
                    <td align="right">'.number_format($row[csf("avg_cons")],4).'</td>
                    <td align="right">'.number_format($row[csf("rate")],4).'</td>
                    <td align="right">'.number_format($row[csf("amount")],4).'</td>  
                </tr>';		
            
				$knit_subtotal_avg_cons += $row[csf("avg_cons")];	
				$knit_subtotal_amount += $row[csf("amount")]; 
			}
			
			if($row[csf("fab_nature_id")]==3)//woven fabrics
			{
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("construction")].", ".$row[csf("composition")].", ".$row[csf("gsm_weight")];
				$j++;
                $woven_fab .= '<tr>
                    <td align="left">'.$item_descrition.'</td>
                    <td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
                    <td align="right">'.number_format($row[csf("avg_cons")],4).'</td>
                    <td align="right">'.number_format($row[csf("rate")],4).'</td>
                    <td align="right">'.number_format($row[csf("amount")],4).'</td>  
                </tr>';		
             
				$woven_subtotal_avg_cons += $row[csf("avg_cons")];	
				$woven_subtotal_amount += $row[csf("amount")]; 
			}
        }	
	
		$knit_fab= '<div style="margin-top:15px">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
					<label><b>All Fabric Cost  </b></label>
						<tr style="font-weight:bold"  align="center">
							<td width="80" rowspan="'.$i.'" ><div class="verticalText"><b>Knit Fabric</b></div></td>
							<td width="350">Description</td>
							<td width="100">Source</td>
							<td width="100">Fab. Cons/'.$costing_per.'</td>
							<td width="100">Rate (USD)</td>
							<td width="100">Amount (USD)</td>
						</tr>'.$knit_fab;
		$woven_fab= '<tr><td colspan="6">&nbsp;</td></tr><tr>
						<td width="80" rowspan="'.$j.'"><div class="verticalText"><b>Woven Fabric</b></div></td></tr>'.$woven_fab;	
						
		//knit fabrics table here 
		$knit_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2">Sub Total</td>
						<td align="right">'.number_format($knit_subtotal_avg_cons,4).'</td>
						<td></td>
						<td align="right">'.number_format($knit_subtotal_amount,4).'</td>
					</tr>';
  		echo $knit_fab;
		
		//woven fabrics table here 
		$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2">Sub Total</td>
						<td align="right">'.number_format($woven_subtotal_avg_cons,4).'</td>
						<td></td>
						<td align="right">'.number_format($woven_subtotal_amount,4).'</td>
					</tr>
   					<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="3">Total</td>
						<td align="right"></td>
						<td></td>
						<td align="right">'.number_format(($woven_subtotal_amount+$knit_subtotal_amount),4).'</td>
					</tr></table></div>';
         echo $woven_fab;           		
  		$grand_total_amount = $knit_subtotal_amount+$woven_subtotal_amount;
		//end 	All Fabric Cost part report-------------------------------------------
  	
	
		//Start	Yarn Cost part report here -------------------------------------------
		$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
		$sql = "select id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, sum(cons_qnty) as cons_qnty, rate, sum(amount) as amount
				from wo_pri_quo_fab_yarn_cost_dtls 
				where quotation_id=".$txt_quotation_id." group by count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, rate";
		$data_array=sql_select($sql);
		?>
        <div style="margin-top:15px">
        	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="70" rowspan="<? echo count($data_array)+2; ?>"><div class="verticalText"><b>Yarn Cost</b></div></td>
                    <td width="350">Yarn Desc</td>
                    <td width="100">Yarn Qnty</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
			<?
            $total_qnty=0;$total_amount=0;
			foreach( $data_array as $row )
            { 
				if($row[csf("percent_one")]==100)
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$yarn_type[$row[csf("type_id")]];
            	else
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_two")]."% ".$yarn_type[$row[csf("type_id")]];
 			
			?>	 
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
				 $total_qnty += $row[csf("cons_qnty")];
				 $total_amount += $row[csf("amount")];
            }
            ?>
            	<tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>
                    <td align="right"><? echo number_format($total_qnty,4); ?></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
        	</table>
      </div>
      <?
	  $grand_total_amount +=$total_amount;
	//End Yarn Cost part report here -------------------------------------------
  	
  
  
  	//start	Conversion Cost to Fabric report here -------------------------------------------
   	$sql = "select a.id, a.quotation_id, a.cons_type, a.req_qnty, a.charge_unit, a.amount, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.construction ,b.composition 
			from wo_pri_quo_fab_conv_cost_dtls a left join wo_pri_quo_fabric_cost_dtls b on a.quotation_id=b.quotation_id and a.cost_head=b.id
			where a.quotation_id=".$txt_quotation_id." ";
	$data_array=sql_select($sql);
	?>

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="80" rowspan="<? echo count($data_array)+3; ?>"><div class="verticalText"><b>Conversion Cost to Fabric</b></div></td>
                    <td width="350">Particulars</td>
                    <td width="100">Process</td>
                    <td width="100">Cons/<? echo $costing_per; ?></td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
 				$item_descrition = $body_part[$row[csf("body_part_id")]].",".$color_type[$row[csf("color_type_id")]].",".$row[csf("construction")].",".$row[csf("composition")];
				
				if(str_replace(",","",$item_descrition)=="")
				{
					$item_descrition="All Fabrics";
				}
			?>	 
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="left"><? echo $conversion_cost_head_array[$row[csf("cons_type")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("req_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("charge_unit")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>                    
                    <td align="right"><? echo $total_amount; ?></td>
                </tr>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td align="left" colspan="4">Total Fabric Cost</td>                    
                    <td align="right"><? echo number_format(($grand_total_amount+$total_amount),4); ?></td>
                </tr>
            </table>
      </div>
      <?
	//End Conversion Cost to Fabric report here -------------------------------------------
  	
  
 
  	//start	Trims Cost part report here -------------------------------------------
   	$sql = "select id, quotation_id, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active
			from wo_pri_quo_trim_cost_dtls  
			where quotation_id=".$txt_quotation_id."";
	$data_array=sql_select($sql);
	?>
 

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Trims Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Item Group</td>
                    <td width="150">Description</td>
                    <td width="150">Brand/Supp Ref</td>
                    <td width="100">UOM</td>
                    <td width="100">Cons/<? echo $costing_per; ?></td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
 				$trim_group=return_library_array( "select item_name,id from  lib_item_group where id=".$row[csf("trim_group")], "id", "item_name"  );
            	
			?>	 
                <tr>
                    <td align="left"><? echo $trim_group[$row[csf("trim_group")]]; ?></td>
                    <td align="left"><? echo $row[csf("description")]; ?></td>
                    <td align="left"><? echo $row[csf("brand_sup_ref")]; ?></td>
                    <td align="left"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="6">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	 //End Trims Cost Part report here -------------------------------------------	
  
  
 	
	//start	Embellishment Details part report here -------------------------------------------
    	$sql = "select id, quotation_id, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active
			from wo_pri_quo_embe_cost_dtls  
			where quotation_id=".$txt_quotation_id." and emb_name !=3";
	$data_array=sql_select($sql);
	?> 
 

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Embellishment Details</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Type</td>
                    <td width="150">Cons/<? echo $costing_per; ?></td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
 				//$emblishment_name_array=array(1=>"Printing",2=>"Embroidery",3=>"Wash",4=>"Special Works",5=>"Others");
 				if($row[csf("emb_name")]==1)$em_type = $emblishment_print_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==2)$em_type = $emblishment_embroy_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==3)$em_type = $emblishment_wash_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==4)$em_type = $emblishment_spwork_type[$row[csf("emb_type")]];
			?>	 
                <tr>
                    <td align="left"><? echo $emblishment_name_array[$row[csf("emb_name")]]; ?></td>
                    <td align="left"><? echo $em_type; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	 //End Embellishment Details Part report here -------------------------------------------	
  
  
  	//start	Commercial Cost part report here -------------------------------------------
   	$sql = "select id, quotation_id, item_id, rate, amount, status_active
			from  wo_pri_quo_comarcial_cost_dtls  
			where quotation_id=".$txt_quotation_id."";
	$data_array=sql_select($sql);

	?> 
 
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commercial Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
  			?>	 
                <tr>
                    <td align="left"><? echo $camarcial_items[$row[csf("item_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="2">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	 //End Commercial Cost Part report here -------------------------------------------	
  
   
  	//start	Commission Cost part report here -------------------------------------------
   	$sql = "select id,quotation_id,particulars_id,commission_base_id,commision_rate,commission_amount, status_active
			from  wo_pri_quo_commiss_cost_dtls  
			where quotation_id=".$txt_quotation_id."";
	$data_array=sql_select($sql);
	?> 
  

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commission Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Commission Basis</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
			$commission_amount=0;
			if($row[csf("commission_base_id")]==1)
				{
					$commission_amount=($row[csf("commision_rate")]*$price_dzn)/100;
				}
				if($row[csf("commission_base_id")]==2)
				{
					$commission_amount=$row[csf("commision_rate")]*$order_price_per_dzn;
				}
				if($row[csf("commission_base_id")]==3)
				{
					$commission_amount=($row[csf("commision_rate")]/12)*$order_price_per_dzn;
				}
  			?>	 
                <tr>
                    <td align="left"><? echo $commission_particulars[$row[csf("particulars_id")]]; ?></td>
                    <td align="left"><? echo $commission_base_array[$row[csf("commission_base_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("commision_rate")],4); ?></td>
                    <td align="right"><?  echo number_format($commission_amount,4); ?></td>
                </tr>
            <?
                 $total_amount += $commission_amount;
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="3">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	 //End Commission Cost Part report here -------------------------------------------	
  
  
	//start	Other Components part report here -------------------------------------------
	$sql = "select fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,wash_cost,wash_cost_percent,embel_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent,certificate_pre_cost,certificate_percent ,common_oh,common_oh_percent,total_cost ,total_cost_percent,final_cost_dzn ,final_cost_dzn_percent ,confirm_price_dzn ,confirm_price_dzn_percent,final_cost_pcs,margin_dzn,margin_dzn_percent,confirm_price
			from wo_price_quotation_costing_mst
			where quotation_id=".$txt_quotation_id."";
	$data_array=sql_select($sql);
	?> 
 
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Others Components</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            {
				if($row[csf("margin_dzn_percent")]<$row[csf("final_cost_dzn_percent")])
				{
				$smg2="Margin percentage is less than standard percentage"	;
				}
  			?>	
            <tr>
                    <td align="left"s>Gmts Wash </td>
                    <td align="right"><? echo number_format($row[csf("wash_cost")],4); ?></td>
                </tr> 

                <tr>
                    <td align="left"s>Lab Test </td>
                    <td align="right"><? echo number_format($row[csf("lab_test")],4); ?></td>
                </tr>
                <tr>
                    <td align="left">Inspection Cost</td>
                    <td align="right"><? echo number_format($row[csf("inspection")],4); ?></td>
                </tr>
                <tr>
                    <td align="left">CM Cost - IE</td>
                    <td align="right"><? echo number_format($row[csf("cm_cost")],4); ?></td>
                </tr>
                <tr>
                    <td align="left">Freight Cost</td>
                    <td align="right"><? echo number_format($row[csf("freight")],4); ?></td>
                </tr>
                 <tr>
                    <td align="left">Currier Cost</td>
                    <td align="right"><? echo number_format($row[csf("currier_pre_cost")],4); ?></td>
                </tr>
                 <tr>
                    <td align="left">Certificate Cost</td>
                    <td align="right"><? echo number_format($row[csf("certificate_pre_cost")],4); ?></td>
                </tr>
                <tr>
                    <td align="left">Office OH</td>
                    <td align="right"><? echo number_format($row[csf("common_oh")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("cm_cost")]+$row[csf("freight")]+$row[csf("currier_pre_cost")]+$row[csf("certificate_pre_cost")]+$row[csf("common_oh")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	 //End Other Components  Part report here -------------------------------------------	  
  
  
  	
  	
      
       
     	 // image show here  -------------------------------------------
		$sql = "select id,master_tble_id,image_location
				from common_photo_library  
				where master_tble_id=".$txt_quotation_id." and form_name='quotation_entry'";
		$data_array=sql_select($sql);
 	  ?> 
          <div style="margin:15px 5px;float:left;width:500px" >
          	<? foreach($data_array AS $inf){ ?>
                <img  src='../../<? echo $inf[csf("image_location")]; ?>' height='97' width='89' />
            <?  } ?>			
          </div>
      
      
      <div style="clear:both"></div>     
      </div>
      <? echo "<br /><b>Note:".$smg." ".$smg2.".</b>"; ?>
    <!--End CM on Net Order Value Part report here ------------------------------------------->
	<? //echo "<br /><b>Note: Other Cost =  Fabric Cost + Trims Cost + Embellishment Cost + Lab Test + Inspection + Office OH</b><br /><br />"; ?>
  		
	<table class="rpt_table" border="0" cellpadding="1" cellspacing="1" style="width:800px;text-align:center;" rules="all">	
		<tr style="alignment-baseline:baseline;">
        	<td height="100" width="33%" style="text-decoration:overline; border:none">Prepared By</td>
            <td width="33%" style="text-decoration:overline; border:none">Checked By</td>
            <td width="33%" style="text-decoration:overline; border:none">Approved By</td>
        </tr>
    </table>
    
    	
 	<?
  
}//end master if condition-------------------------------------------------------
?>
<?
if($action=="generate_report" && $type=="preCostRpt2")
{
	extract($_REQUEST);
 	$txt_quotation_date=change_date_format(str_replace("'","",$txt_quotation_date),'yyyy-mm-dd','-');
	if($txt_quotation_id=="") $quotation_id=''; else $quotation_id=" and a.id=".$txt_quotation_id."";
	if($cbo_company_name=="") $company_name=''; else $company_name=" and a.company_id=".$cbo_company_name."";
	if($cbo_buyer_name=="") $cbo_buyer_name=''; else $cbo_buyer_name=" and a.buyer_id=".$cbo_buyer_name."";
	if($txt_style_ref=="") $txt_style_ref=''; else $txt_style_ref=" and a.style_ref=".$txt_style_ref."";
	if($txt_quotation_date=="") $txt_quotation_date=''; else $txt_quotation_date=" and a.quot_date ='".$txt_quotation_date."'";
 	
	
	//array for display name
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$gsm_weight_top=return_field_value("gsm_weight", "wo_pri_quo_fabric_cost_dtls", "quotation_id=$txt_quotation_id and body_part_id=1");
	$gsm_weight_bottom=return_field_value("gsm_weight", "wo_pri_quo_fabric_cost_dtls", "quotation_id=$txt_quotation_id and body_part_id=20");
		
	 $sql = "select a.id,a.company_id ,a.buyer_id,a.style_ref,a.style_desc,a.season,a.gmts_item_id ,a.order_uom ,a.offer_qnty,a.est_ship_date,a.op_date,DATEDIFF(est_ship_date,op_date) as date_diff,MAX(b.id) as bid,b.costing_per_id,b.a1st_quoted_price,b.revised_price,b.confirm_price,b.price_with_commn_pcs,b.terget_qty,c.fab_knit_req_kg,c.fab_woven_req_yds,c.fab_yarn_req_kg
			from wo_price_quotation a, wo_price_quotation_costing_mst b, wo_pri_quo_sum_dtls c 
			where a.id=b.quotation_id  and b.quotation_id =c.quotation_id  and a.status_active=1 $quotation_id $company_name $cbo_buyer_name $txt_style_ref $txt_quotation_date order by a.id";  
	$data_array=sql_select($sql);
	
	
	?>
    <div style="width:850px; font-size:20px; font-weight:bold" align="center"><? echo $comp[str_replace("'","",$cbo_company_name)]; ?></div>
    <div style="width:850px; font-size:14px; font-weight:bold" align="center">Quotation</div>
	<?
	foreach ($data_array as $row)
	{	
		$order_price_per_dzn=0;
		$order_job_qnty=0;
		$avg_unit_price=$row[csf("price_with_commn_pcs")];
		if($avg_unit_price==0)
		{
			$avg_unit_price=$row[csf("revised_price")];
		}
		if($avg_unit_price==0)
		{
			$avg_unit_price=$row[csf("a1st_quoted_price")];
		}

		
		$order_values = $row[csf("offer_qnty")]*$avg_unit_price;
		/*$result =sql_select("select po_number,pub_shipment_date from wo_po_break_down where job_no_mst=$txt_job_no order by pub_shipment_date DESC");
		$job_in_orders = '';$pulich_ship_date='';
		foreach ($result as $val)
		{
			$job_in_orders .= $val['po_number'].", ";
			$pulich_ship_date = $val['pub_shipment_date'];
		}
		$job_in_orders = substr(trim($job_in_orders),0,-1);*/
		?>
            	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px" rules="all">
                	<tr>
                    	<td width="80">Quotation ID</td>
                        <td width="80"><b><? echo $row[csf("id")]; ?></b></td>
                        <td width="90">Buyer</td>
                        <td width="100"><b><? echo $buyer_arr[$row[csf("buyer_id")]]; ?></b></td>
                        <td width="80">Garments Item</td>
                        <? 
							/*$grmnt_items = "";
							if($garments_item[$row[csf("gmts_item_id")]]=="")
							{
								
								$grmts_sql = sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no=$txt_job_no");
								foreach($grmts_sql as $key=>$val){
									$grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].", ";
								}
								$grmnt_items = substr_replace($grmnt_items,"",-1,1);
							}else{
								$grmnt_items = $garments_item[$row[csf("gmts_item_id")]];
							}*/
							if($row[csf("order_uom")]==1)
							{
							  $grmnt_items=$garments_item[$row[csf("gmts_item_id")]];	
							}
							else
							{
								$gmt_item=explode(',',$row[csf("gmts_item_id")]);
								foreach($gmt_item as $key=>$val)
								{
									$grmnt_items .=$garments_item[$val].", ";
								}
								
							}
							
						?>
                        <td width="100"><b><? echo $grmnt_items; ?></b></td>
                    </tr>
                    <tr>
                    	<td>Style Ref. No </td>
                        <td><b><? echo $row[csf("style_ref")]; ?></b></td>
                        <td>Order UOM </td>
                        <td><b><? echo $unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
                        <td>Offer Qnty</td>
                        <td><b><? echo $row[csf("offer_qnty")]; ?></b></td>
                    </tr>
                   
                    <tr>
                    	<td>Knit Fabric Cons</td>
                        <td><b><? echo number_format($row[csf("fab_knit_req_kg")],4); ?> (Kg)</b></td>
                        <td>Woven Fabric Cons</td>
                        <td><b><? echo number_format($row[csf("fab_woven_req_yds")],4); ?> (Yds)</b></td>
                        <td>Price Per Unit</td>
                        <td><b><? echo number_format($avg_unit_price,4); ?> USD</b></td>
                    </tr>
                    <tr>
                    	<td>Avg Yarn Req</td>
                        <td><b><? echo number_format($row[csf("fab_yarn_req_kg")],4) ?> (Kg)</b></td>
                        <td>Costing Per</td>
                        <td><b><? echo $costing_per[$row[csf("costing_per_id")]]; ?></b></td>
                        <td> Target Price </td>
                        <td><b><? echo $row[csf("terget_qty")]; ?></b></td>
                    </tr>
                    <tr>
                    	<td>GSM</td>
                        <td><b><? echo $gsm_weight_top.",".$gsm_weight_bottom; ?></b></td>
                        <td>Style Desc</td>
                        <td><b><? echo $row[csf("style_desc")]; ?></b></td>
                        <td>Season</td>
                        <td><b><? echo $row[csf("season")]; ?></b></td>
                        
                    </tr>
                    <tr>
                         <td> OP Date </td>
                        <td><b><? echo change_date_format($row[csf("op_date")]); ?></b></td>
                    	<td> Est.Ship Date </td>
                        <td><b><? echo change_date_format($row[csf("est_ship_date")]); ?></b></td>
                        <td> Lead Time </td>
                        <td>
						<? 
						
						$dayes=$row[csf("date_diff")]+1;
						if($dayes >= 7)
						{
						$day=$dayes%7;
						$week=($dayes-$day)/7;
							if($week>1)
							{
								$week_string="Weeks";
							}
							else
							{
								$week_string="Week";
							}
							if($day>1)
							{
								$day_string="Days";
							}
							else
							{
								$day_string="Day";
							}
							if($day != 0)
							{
							echo $week." ".$week_string." ".$day." ".$day_string;
							}
							else
							{
							echo $week." ".$week_string;
							}
						}
						else
						{
						if($dayes>1)
							{
								$day_string="Days";
							}
							else
							{
								$dayes="Day";
							}	
							echo $dayes." ".$day_string;
						}
						 
						?>
                        </td>
                        
                        
                    </tr>
                </table>
            <?	
			
			if($row[csf("costing_per_id")]==1){$order_price_per_dzn=12;$costing_per=" DZN";}
			else if($row[csf("costing_per_id")]==2){$order_price_per_dzn=1;$costing_per=" PCS";}
			else if($row[csf("costing_per_id")]==3){$order_price_per_dzn=24;$costing_per=" 2 DZN";}
			else if($row[csf("costing_per_id")]==4){$order_price_per_dzn=36;$costing_per=" 3 DZN";}
			else if($row[csf("costing_per_id")]==5){$order_price_per_dzn=48;$costing_per=" 4 DZN";}
			$order_job_qnty=$row[csf("offer_qnty")];
			//$avg_unit_price=$row[csf("confirm_price")];
			
	}//end first foearch
	//start	all summary report here -------------------------------------------
	$sql = "select MAX(id),fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent ,certificate_pre_cost, certificate_percent,common_oh,common_oh_percent,total_cost ,total_cost_percent,final_cost_dzn ,final_cost_dzn_percent ,confirm_price_dzn ,confirm_price_dzn_percent,final_cost_pcs,margin_dzn,margin_dzn_percent,a1st_quoted_price,confirm_price,revised_price,price_with_commn_dzn
			from wo_price_quotation_costing_mst
			where quotation_id=".$txt_quotation_id."";
	$data_array=sql_select($sql);
	$others_cost_value=0;
 	?>

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="80">SL</td>
                    <td width="300">Particulars</td>
                    <td width="100">Cost</td>
                    <td width="100">Amount (USD)</td>
                    <td width="180">% to Ord. Value</td>                     
                </tr>
            <?
			$order_price_summ=0;
			$less_commision_summ=0;
			$total_cost_summ=0;
			$margin_summ=0;
			$margin_percent_summ=0;
			$percent=0;
			$price_dzn=0;
            $sl=0;
            foreach( $data_array as $row )
            { 
				$sl=$sl+1;
				$price_dzn=$row[csf("confirm_price_dzn")];
  				$others_cost_value=$row[csf("total_cost")]-$row[csf("cm_cost")]-$row[csf("freight")]-$row[csf("comm_cost")]-$row[csf("commission")];
				
				$commission_base_id=return_field_value("commission_base_id", "wo_pri_quo_commiss_cost_dtls", "quotation_id=$txt_quotation_id");
				$commision_rate=return_field_value("sum(commision_rate)", "wo_pri_quo_commiss_cost_dtls", "quotation_id=$txt_quotation_id");
				if($commission_base_id==1)
				{
					$commision=($commision_rate*$row[csf("confirm_price_dzn")])/100;
				}
				if($commission_base_id==2)
				{
					$commision=$commision_rate*$order_price_per_dzn;
				}
				if($commission_base_id==3)
				{
					$commision=($commision_rate/12)*$order_price_per_dzn;
				}
?>	
                <tr>
                    <td><? echo $sl;?></td>			
                    <td align="left"><b>Order Price/<? echo $costing_per; ?></b></td>
                    <td></td>
                    <td align="right"><b><? echo number_format($row[csf("price_with_commn_dzn")],4); $order_price_summ=$row[csf("price_with_commn_dzn")];//$avg_unit_price*$order_price_per_dzn ?></b></td>
                    <td align="center"><? //echo "100.00%"; ?></td>
                </tr>
                <tr>
                    <td><? echo ++$sl;?></td>			
                    <td align="left"><b>Less Commision/<? echo $costing_per; ?></b></td>
                    <td></td>
                    <td align="right"><b><? echo number_format($row[csf("commission")],4);$less_commision_summ=$row[csf("commission")];//$avg_unit_price*$order_price_per_dzn ?></b></td>
                    <td align="center"><? //echo "100.00%"; ?></td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left"><b>Net Quoted Price</b></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?  $less_commision_cost_dzn=($row[csf("price_with_commn_dzn")]-$row[csf("commission")]); echo number_format($less_commision_cost_dzn,4); ?></b></td>
                    <td align="center"><b><? echo "100.00%"; ?></b></td>
                 </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">All Fabric Cost</td>
                    <td align="right"><? echo number_format($row[csf("fabric_cost")],4); ?></td>
                    <td align="right" rowspan="12">&nbsp;</td>
                    <td align="center"><? echo number_format($row[csf("fabric_cost_percent")],2); $percent+=$row[csf("fabric_cost_percent")]; ?>%</td>
                </tr>
              
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Trims Cost</td>
                    <td align="right"><? echo number_format($row[csf("trims_cost")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("trims_cost_percent")],2);  $percent+=$row[csf("trims_cost_percent")];?>%</td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Embellishment Cost</td>
                    <td align="right"><? echo number_format($row[csf("embel_cost")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("embel_cost_percent")],2); $percent+=$row[csf("embel_cost_percent")]; ?>%</td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Commercial Cost</td>
                    <td align="right"><? echo number_format($row[csf("comm_cost")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("comm_cost_percent")],2); $percent+=$row[csf("comm_cost_percent")]; ?>%</td>
                </tr>
               <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Washing Cost (Gmt.)</td>
                    <td align="right"><? echo number_format($row[csf("wash_cost")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("wash_cost_percent")],2); $percent+=$row[csf("wash_cost_percent")]; ?>%</td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Lab Test</td>
                    <td align="right"><? echo number_format($row[csf("lab_test")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("lab_test_percent")],2); $percent+=$row[csf("lab_test_percent")];  ?>%</td>
                 </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Inspection Cost</td>
                    <td align="right"><? echo number_format($row[csf("inspection")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("inspection_percent")],2); $percent+=$row[csf("inspection_percent")]; ?>%</td>
                 </tr>
                <tr> 
                    <td><? echo ++$sl; ?></td>
                    <td align="left">CM Cost</td>
                    <td align="right"><? echo number_format($row[csf("cm_cost")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("cm_cost_percent")],2); $percent+=$row[csf("cm_cost_percent")]; ?>%</td>
                 </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Freight Cost</td>
                    <td align="right"><? echo number_format($row[csf("freight")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("freight_percent")],2); $percent+=$row[csf("freight_percent")]; ?>%</td>
                 </tr>
                 <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Currier Cost</td>
                    <td align="right"><? echo number_format($row[csf("currier_pre_cost")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("currier_percent")],2); $percent+=$row[csf("currier_percent")]; ?>%</td>
                 </tr>
                  <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Certificate Cost</td>
                    <td align="right"><? echo number_format($row[csf("certificate_pre_cost")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("certificate_percent")],2); $percent+=$row[csf("certificate_percent")]; ?>%</td>
                 </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Office OH </td>
                    <td align="right"><? echo number_format($row[csf("common_oh")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("common_oh_percent")],2); $percent+=$row[csf("common_oh_percent")];?>%</td>
                 </tr>
                <tr>
                
			
		
                    <td><? echo ++$sl; ?></td>
                    <td align="left"><b>Total Cost (4-13)</b></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?  $final_cost_dzn=$row[csf("total_cost")]; echo number_format($final_cost_dzn,4); $total_cost_summ=$final_cost_dzn; ?></b></td>
                    <td align="center"><b><?  echo number_format($percent,4);//echo number_format(($final_cost_dzn/$row[csf("total_cost")])*100,2);  ?>%</b></td>
                 </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Margin/<? echo $costing_per; ?> </td>
                    <td>&nbsp;</td>
                    <td align="right"><? $margin_dzn=$less_commision_cost_dzn-$final_cost_dzn; echo number_format($margin_dzn,4); $margin_summ=$margin_dzn; ?></td>
                    <td align="center"><? echo  number_format(($margin_dzn/$less_commision_cost_dzn*100),4); 	$margin_percent_summ=($margin_dzn/$less_commision_cost_dzn*100);?></td>
                </tr>
               <!-- <tr>
                    <td><? //echo ++$sl; ?></td>
                    <td align="left">Margin/Pcs</td>
                    <td>&nbsp;</td>
                    <td align="right"><?// echo number_format($margin_dzn/$order_price_per_dzn,4); ?></td>
                    <td align="right"></td>
                </tr>-->
                <tr>
                <?
				$net_quoted_price=number_format($row[csf("confirm_price")],4);
				if($net_quoted_price=="" || $net_quoted_price==0.0000)
				{
				$net_quoted_price=number_format($row[csf("revised_price")],4);
				}
				if($net_quoted_price=="" || $net_quoted_price==0.0000)
				{
					$net_quoted_price=number_format($row[csf("a1st_quoted_price")],4);
			    }
				
				$cost_pcs=number_format($final_cost_dzn/$order_price_per_dzn,4);
				if($cost_pcs>$net_quoted_price)
				{
				$bgcolor_net_quoted_price="#FF0000";	
				$smg="Cost is hiegher than quoted price.";
				}
				?>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Net Quoted Price/ Pcs</td>
                    <td>&nbsp;</td>
                    <td align="right" bgcolor="<? echo $bgcolor_net_quoted_price;  ?>"><? echo number_format($net_quoted_price,4); ?></td>
                    <td align="right"></td>
                </tr>
                 <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Cost /Pcs</td>
                    <td>&nbsp;</td>
                    <td align="right"><? $cost_per_pice=number_format($final_cost_dzn/$order_price_per_dzn,4); echo number_format($final_cost_dzn/$order_price_per_dzn,4); ?></td>
                    <td align="center"></td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Margin/Pcs</td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format(($net_quoted_price-$cost_per_pice),4); ?></td>
                    <td align="center"></td>
                </tr>
 
            <?
                 
            }
            ?>
                 
            </table>
      </div>
      <?
	//End all summary report here -------------------------------------------
	
	
	
	//2	All Fabric Cost part here------------------------------------------- 	   	
	$sql = "select id, quotation_id, body_part_id, fab_nature_id, color_type_id,construction,composition, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active   
			from wo_pri_quo_fabric_cost_dtls 
			where quotation_id=".$txt_quotation_id."";
	$data_array=sql_select($sql);
	$knit_fab="";$woven_fab="";
 		 
		$knit_subtotal_avg_cons=0;
		$knit_subtotal_amount=0;
		$woven_subtotal_avg_cons=0;
		$woven_subtotal_amount=0;
		$grand_total_amount=0;
        $i=2;$j=2;
		foreach( $data_array as $row )
        {
            if($row[csf("fab_nature_id")]==2)//knit fabrics
			{
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("construction")].", ".$row[csf("composition")];
				$i++;	
                $knit_fab .= '<tr>
                    <td align="left">'.$item_descrition.'</td>
                    <td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
                    <td align="right">'.number_format($row[csf("avg_cons")],4).'</td>
                    <td align="right">'.number_format($row[csf("rate")],4).'</td>
                    <td align="right">'.number_format($row[csf("amount")],4).'</td>  
                </tr>';		
            
				$knit_subtotal_avg_cons += $row[csf("avg_cons")];	
				$knit_subtotal_amount += $row[csf("amount")]; 
			}
			
			if($row[csf("fab_nature_id")]==3)//woven fabrics
			{
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("construction")].", ".$row[csf("composition")];
				$j++;
                $woven_fab .= '<tr>
                    <td align="left">'.$item_descrition.'</td>
                    <td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
                    <td align="right">'.number_format($row[csf("avg_cons")],4).'</td>
                    <td align="right">'.number_format($row[csf("rate")],4).'</td>
                    <td align="right">'.number_format($row[csf("amount")],4).'</td>  
                </tr>';		
             
				$woven_subtotal_avg_cons += $row[csf("avg_cons")];	
				$woven_subtotal_amount += $row[csf("amount")]; 
			}
        }	
	
		$knit_fab= '<div style="margin-top:15px">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
					<label><b>All Fabric Cost  </b></label>
						<tr style="font-weight:bold"  align="center">
							<td width="80" rowspan="'.$i.'" ><div class="verticalText"><b>Knit Fabric</b></div></td>
							<td width="350">Description</td>
							<td width="100">Source</td>
							<td width="100">Fab. Cons/'.$costing_per.'</td>
							<td width="100">Rate (USD)</td>
							<td width="100">Amount (USD)</td>
						</tr>'.$knit_fab;
		$woven_fab= '<tr><td colspan="6">&nbsp;</td></tr><tr>
						<td width="80" rowspan="'.$j.'"><div class="verticalText"><b>Woven Fabric</b></div></td></tr>'.$woven_fab;	
						
		//knit fabrics table here 
		$knit_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2">Sub Total</td>
						<td align="right">'.number_format($knit_subtotal_avg_cons,4).'</td>
						<td></td>
						<td align="right">'.number_format($knit_subtotal_amount,4).'</td>
					</tr>';
  		echo $knit_fab;
		
		//woven fabrics table here 
		$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2">Sub Total</td>
						<td align="right">'.number_format($woven_subtotal_avg_cons,4).'</td>
						<td></td>
						<td align="right">'.number_format($woven_subtotal_amount,4).'</td>
					</tr>
   					<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="3">Total</td>
						<td align="right"></td>
						<td></td>
						<td align="right">'.number_format(($woven_subtotal_amount+$knit_subtotal_amount),4).'</td>
					</tr></table></div>';
         echo $woven_fab;           		
  		$grand_total_amount = $knit_subtotal_amount+$woven_subtotal_amount;
		//end 	All Fabric Cost part report-------------------------------------------
  	
	
		//Start	Yarn Cost part report here -------------------------------------------
		$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
		$sql = "select id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, sum(cons_qnty) as cons_qnty, rate, sum(amount) as amount
				from wo_pri_quo_fab_yarn_cost_dtls 
				where quotation_id=".$txt_quotation_id." group by count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, rate";
		$data_array=sql_select($sql);
		?>
        <div style="margin-top:15px">
        	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="70" rowspan="<? echo count($data_array)+2; ?>"><div class="verticalText"><b>Yarn Cost</b></div></td>
                    <td width="350">Yarn Desc</td>
                    <td width="100">Yarn Qnty</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
			<?
            $total_qnty=0;$total_amount=0;
			foreach( $data_array as $row )
            { 
				if($row[csf("percent_one")]==100)
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$yarn_type[$row[csf("type_id")]];
            	else
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_two")]."% ".$yarn_type[$row[csf("type_id")]];
 			
			?>	 
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
				 $total_qnty += $row[csf("cons_qnty")];
				 $total_amount += $row[csf("amount")];
            }
            ?>
            	<tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>
                    <td align="right"><? echo number_format($total_qnty,4); ?></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
        	</table>
      </div>
      <?
	  $grand_total_amount +=$total_amount;
	//End Yarn Cost part report here -------------------------------------------
  	
  
  
  	//start	Conversion Cost to Fabric report here -------------------------------------------
   	$sql = "select a.id, a.quotation_id, a.cons_type, a.req_qnty, a.charge_unit, a.amount, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.construction ,b.composition 
			from wo_pri_quo_fab_conv_cost_dtls a left join wo_pri_quo_fabric_cost_dtls b on a.quotation_id=b.quotation_id and a.cost_head=b.id
			where a.quotation_id=".$txt_quotation_id." ";
	$data_array=sql_select($sql);
	?>

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="80" rowspan="<? echo count($data_array)+3; ?>"><div class="verticalText"><b>Conversion Cost to Fabric</b></div></td>
                    <td width="350">Particulars</td>
                    <td width="100">Process</td>
                    <td width="100">Cons/<? echo $costing_per; ?></td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
 				$item_descrition = $body_part[$row[csf("body_part_id")]].",".$color_type[$row[csf("color_type_id")]].",".$row[csf("construction")].",".$row[csf("composition")];
				
				if(str_replace(",","",$item_descrition)=="")
				{
					$item_descrition="All Fabrics";
				}
			?>	 
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="left"><? echo $conversion_cost_head_array[$row[csf("cons_type")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("req_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("charge_unit")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>                    
                    <td align="right"><? echo $total_amount; ?></td>
                </tr>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td align="left" colspan="4">Total Fabric Cost</td>                    
                    <td align="right"><? echo number_format(($grand_total_amount+$total_amount),4); ?></td>
                </tr>
            </table>
      </div>
      <?
	//End Conversion Cost to Fabric report here -------------------------------------------
  	
  
 
  	//start	Trims Cost part report here -------------------------------------------
   	$sql = "select id, quotation_id, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active
			from wo_pri_quo_trim_cost_dtls  
			where quotation_id=".$txt_quotation_id."";
	$data_array=sql_select($sql);
	?>
 

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Trims Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Item Group</td>
                    <td width="150">Description</td>
                    <td width="150">Brand/Supp Ref</td>
                    <td width="100">UOM</td>
                    <td width="100">Cons/<? echo $costing_per; ?></td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
 				$trim_group=return_library_array( "select item_name,id from  lib_item_group where id=".$row[csf("trim_group")], "id", "item_name"  );
            	
			?>	 
                <tr>
                    <td align="left"><? echo $trim_group[$row[csf("trim_group")]]; ?></td>
                    <td align="left"><? echo $row[csf("description")]; ?></td>
                    <td align="left"><? echo $row[csf("brand_sup_ref")]; ?></td>
                    <td align="left"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="6">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	 //End Trims Cost Part report here -------------------------------------------	
  
  
 	
	//start	Embellishment Details part report here -------------------------------------------
    	$sql = "select id, quotation_id, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active
			from wo_pri_quo_embe_cost_dtls  
			where quotation_id=".$txt_quotation_id." and emb_name !=3";
	$data_array=sql_select($sql);
	?> 
 

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Embellishment Details</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Type</td>
                    <td width="150">Cons/<? echo $costing_per; ?></td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
 				//$emblishment_name_array=array(1=>"Printing",2=>"Embroidery",3=>"Wash",4=>"Special Works",5=>"Others");
 				if($row[csf("emb_name")]==1)$em_type = $emblishment_print_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==2)$em_type = $emblishment_embroy_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==3)$em_type = $emblishment_wash_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==4)$em_type = $emblishment_spwork_type[$row[csf("emb_type")]];
			?>	 
                <tr>
                    <td align="left"><? echo $emblishment_name_array[$row[csf("emb_name")]]; ?></td>
                    <td align="left"><? echo $em_type; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	 //End Embellishment Details Part report here -------------------------------------------	
  
  
  	//start	Commercial Cost part report here -------------------------------------------
   	$sql = "select id, quotation_id, item_id, rate, amount, status_active
			from  wo_pri_quo_comarcial_cost_dtls  
			where quotation_id=".$txt_quotation_id."";
	$data_array=sql_select($sql);

	?> 
 
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commercial Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
  			?>	 
                <tr>
                    <td align="left"><? echo $camarcial_items[$row[csf("item_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="2">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	 //End Commercial Cost Part report here -------------------------------------------	
  
   
  	//start	Commission Cost part report here -------------------------------------------
   	$sql = "select id,quotation_id,particulars_id,commission_base_id,commision_rate,commission_amount, status_active
			from  wo_pri_quo_commiss_cost_dtls  
			where quotation_id=".$txt_quotation_id."";
	$data_array=sql_select($sql);
	?> 
  

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commission Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Commission Basis</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
			$commission_amount=0;
			if($row[csf("commission_base_id")]==1)
				{
					$commission_amount=($row[csf("commision_rate")]*$price_dzn)/100;
				}
				if($row[csf("commission_base_id")]==2)
				{
					$commission_amount=$row[csf("commision_rate")]*$order_price_per_dzn;
				}
				if($row[csf("commission_base_id")]==3)
				{
					$commission_amount=($row[csf("commision_rate")]/12)*$order_price_per_dzn;
				}
  			?>	 
                <tr>
                    <td align="left"><? echo $commission_particulars[$row[csf("particulars_id")]]; ?></td>
                    <td align="left"><? echo $commission_base_array[$row[csf("commission_base_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("commision_rate")],4); ?></td>
                    <td align="right"><?  echo number_format($row[csf("commission_amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("commission_amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="3">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	 //End Commission Cost Part report here -------------------------------------------	
  
  
	//start	Other Components part report here -------------------------------------------
	$sql = "select fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,wash_cost,wash_cost_percent,embel_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent,certificate_pre_cost,certificate_percent ,common_oh,common_oh_percent,total_cost ,total_cost_percent,final_cost_dzn ,final_cost_dzn_percent ,confirm_price_dzn ,confirm_price_dzn_percent,final_cost_pcs,margin_dzn,margin_dzn_percent,confirm_price
			from wo_price_quotation_costing_mst
			where quotation_id=".$txt_quotation_id."";
	$data_array=sql_select($sql);
	?> 
 
        <div style="margin-top:15px">
        <table>
        <tr>
        <td>
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:250px;text-align:center;" rules="all">
            <label><b>Others Components</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            {
				if($row[csf("margin_dzn_percent")]<$row[csf("final_cost_dzn_percent")])
				{
				$smg2="Margin percentage is less than standard percentage"	;
				}
  			?>	
            <tr>
                    <td align="left"s>Gmts Wash </td>
                    <td align="right"><? echo number_format($row[csf("wash_cost")],4); ?></td>
                </tr> 

                <tr>
                    <td align="left"s>Lab Test </td>
                    <td align="right"><? echo number_format($row[csf("lab_test")],4); ?></td>
                </tr>
                <tr>
                    <td align="left">Inspection Cost</td>
                    <td align="right"><? echo number_format($row[csf("inspection")],4); ?></td>
                </tr>
                <tr>
                    <td align="left">CM Cost - IE</td>
                    <td align="right"><? echo number_format($row[csf("cm_cost")],4); ?></td>
                </tr>
                <tr>
                    <td align="left">Freight Cost</td>
                    <td align="right"><? echo number_format($row[csf("freight")],4); ?></td>
                </tr>
                <tr>
                    <td align="left">Currier Cost</td>
                    <td align="right"><? echo number_format($row[csf("currier_pre_cost")],4); ?></td>
                </tr>
                 <tr>
                    <td align="left">Certificate Cost</td>
                    <td align="right"><? echo number_format($row[csf("certificate_pre_cost")],4); ?></td>
                </tr>
                <tr>
                    <td align="left">Office OH</td>
                    <td align="right"><? echo number_format($row[csf("common_oh")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("cm_cost")]+$row[csf("freight")]+$row[csf("currier_pre_cost")]+$row[csf("certificate_pre_cost")]+$row[csf("common_oh")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
            </td>
            <td rowspan="2">
            <?
             // image show here  -------------------------------------------
		$sql_img = "select id,master_tble_id,image_location
				from common_photo_library  
				where master_tble_id=".$txt_quotation_id." and form_name='quotation_entry' limit 1";
		$data_array_img=sql_select($sql_img);
 	  ?> 
          <div style="margin:15px 5px;float:left;width:500px">
          	<? foreach($data_array_img AS $inf_img){ ?>
                <img  src='../../<? echo $inf_img[csf("image_location")]; ?>' height='400' width='300'/>
            <?  } ?>			
          </div>
            </td>
            </tr>
            <tr>
            <td>
                <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:250px;text-align:center;" rules="all">
                <label><b>Price Summary :Quotation Id:<? echo trim($txt_quotation_id,"'");?></b></label>
                    <tr style="font-weight:bold">
                        <td width="150">Particulars</td>
                        <td width="100">Amount (USD)</td>
                     </tr>
                <?
                $total_amount=0;
                foreach( $data_array as $row )
                {
                    if($row[csf("margin_dzn_percent")]<$row[csf("final_cost_dzn_percent")])
                    {
                    $smg2="Margin percentage is less than standard percentage"	;
                    }
                ?>	
                <tr>
                        <td align="left"s>Price <? echo $costing_per; ?> </td>
                        <td align="right"><? echo number_format($order_price_summ,4); ?></td>
                    </tr> 
    
                    <tr>
                        <td align="left"s>Less Commision/ <? echo $costing_per; ?></td>
                        <td align="right"><? echo number_format($less_commision_summ,4); ?></td>
                    </tr>
                    <tr>
                        <td align="left">Net Quoted Price/<? echo $costing_per; ?></td>
                        <td align="right"><? echo number_format($order_price_summ-$less_commision_summ,4); ?></td>
                    </tr>
                    
                    <tr>
                        <td align="left">Total cost/<? echo $costing_per; ?></td>
                        <td align="right"><? echo number_format( $total_cost_summ,4); ?></td>
                    </tr>
                    <tr>
                        <td align="left">Margin/<? echo $costing_per; ?></td>
                        <td align="right"><? echo number_format($margin_summ,4); ?></td>
                    </tr>
                    <tr>
                        <td align="left">Margin %</td>
                        <td align="right"><? echo number_format($margin_percent_summ,4); ?></td>
                    </tr>
                <?
                     $total_amount += $row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("cm_cost")]+$row[csf("freight")]+$row[csf("common_oh")];
                }
                ?>
                </table>
                </td>
               
                </tr>
            </table>
      </div>
     
  
  
  	
  	
      
       
     	
      
      
      <div style="clear:both"></div>     
      </div>
      <? echo "<br /><b>Note:".$smg." ".$smg2.".</b>"; ?>
    <!--End CM on Net Order Value Part report here ------------------------------------------->
	<? //echo "<br /><b>Note: Other Cost =  Fabric Cost + Trims Cost + Embellishment Cost + Lab Test + Inspection + Office OH</b><br /><br />"; ?>
  		
	<table class="rpt_table" border="0" cellpadding="1" cellspacing="1" style="width:800px;text-align:center;" rules="all">	
		<tr style="alignment-baseline:baseline;">
        	<td height="100" width="33%" style="text-decoration:overline; border:none">Prepared By</td>
            <td width="33%" style="text-decoration:overline; border:none">Checked By</td>
            <td width="33%" style="text-decoration:overline; border:none">Approved By</td>
        </tr>
    </table>
    
    	
 	<?
  
}//end master if condition-------------------------------------------------------
?>