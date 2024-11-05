<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');
ini_set('memory_limit','2000M');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];

$action=$_REQUEST['action'];



//========== user credential start ========
$userCredential = sql_select("SELECT WORKING_UNIT_ID, unit_id as company_id,company_location_id as location_id FROM user_passwd where id=$user_id");
$working_unit_id = $userCredential[0][csf('WORKING_UNIT_ID')];
$working_credential_cond = "";

if ($working_unit_id > 0) {
 $working_credential_cond = " and comp.id in($working_unit_id)";
}


//$order_number_arr=return_library_array( "select id, po_number from   wo_po_break_down",'id','po_number');
// $color_arr=return_library_array( "select id,color_name  from  lib_color", "id", "color_name"  );

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/cutting_entry_controller_urmi', this.value, 'load_drop_down_floor', 'floor_td' )","" );
	exit();
}
if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_name", 140, "select id,floor_name from lib_prod_floor where production_process=1 and status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "","" );
	die;
}
if($db_type==0){ $insert_year="SUBSTRING_INDEX(b.insert_date, '-', 1)";}
if($db_type==2){ $insert_year="extract(year from b.insert_date)";}

/*if ($action=="load_drop_down_buyer")
{
     $data=explode("**",$data);
	 $sql="select distinct c.id,c.buyer_name from  wo_po_break_down a,wo_po_details_master b,lib_buyer c where  a.job_no_mst=b.job_no and b.company_name=".$data[2]."  and job_no_prefix_num='".$data[0]."' and $insert_year='".$data[1]."' and b.buyer_name=c.id and a.status_active=1";
	$result=sql_select($sql);
	foreach($result as $val)
	{
	 $buyer_value=$val[csf('buyer_name')];

	}
   echo create_drop_down( "txt_buyer_name", 140,$sql, "id,buyer_name", 0, "select Buyer" ,$buyer_value);
   exit();
}*/


if($action=="load_drop_down_cutt_company")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];

	if($data==3)
	{
		if($db_type==0)
		{
			echo create_drop_down( "cbo_cutting_company", 150, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "fnc_workorder_search(this.value);load_drop_down( 'requires/cutting_entry_controller_urmi', this.value, 'load_drop_down_location', 'location_td' );" );
		}
		else
		{
			echo create_drop_down( "cbo_cutting_company", 150, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=22 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select--", $selected, "fnc_workorder_search(this.value);load_drop_down( 'requires/cutting_entry_controller_urmi', this.value, 'load_drop_down_location', 'location_td' );" );
		}
	}
	else if($data==1)
		echo create_drop_down( "cbo_cutting_company", 150, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $working_credential_cond order by company_name","id,company_name", 1, "--- Select ---", $selected_company, "load_drop_down( 'requires/cutting_entry_controller_urmi', this.value, 'load_drop_down_location', 'location_td' );",0,0 );
	else
		echo create_drop_down( "cbo_cutting_company", 150, $blank_array,"", 1, "--- Select ---", $selected, "",0 );

		//echo "select id,company_name from lib_company where is_deleted=0 and status_active=1 $working_credential_cond order by company_name";


	exit();
}

if ($action=="load_drop_down_workorder")
{
	$explode_data = explode("_",$data);

	$sql = "SELECT a.id,a.sys_number from piece_rate_wo_mst a, piece_rate_wo_dtls b where a.id=b.mst_id and b.order_id in(".$explode_data[2].") and a.company_id=$explode_data[0]  and a.rate_for=20 and a.service_provider_id=$explode_data[1]   and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by a.id,a.sys_number order by a.id";
	//echo $sql;
	echo create_drop_down( "cbo_work_order", 150, $sql,"id,sys_number", 1, "-- Select Work Order --", $selected, "fnc_workorder_rate(this.value,'$data')",0 );
}
if($action=="populate_workorder_rate")
{
	$data=explode("_",$data);
	$po_break_down_id=$data[3];
	$company_id=$data[1];
	$suppplier=$data[2];
	$sql = sql_select("select a.id,a.sys_number,a.currence,a.exchange_rate,sum(b.avg_rate) as rate,b.uom,b.order_id from piece_rate_wo_mst a, piece_rate_wo_dtls b where a.id=".$data[0]." and a.id=b.mst_id and b.order_id in(".$po_break_down_id.") and a.company_id=$company_id and a.service_provider_id=$suppplier and a.rate_for=20   and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by a.id,a.sys_number,a.currence ,a.exchange_rate,b.uom,b.order_id order by a.id");
	//print_r($sql);
	$data_string="";
	foreach($sql as $val)
	{
		if($val[csf('uom')]==2)
		{
			$rate=$val[csf('rate')]/12;
		}
		else
		{
			$rate=$val[csf('rate')];
		}
		if($data_string!="") $data_string.=",";
		$data_string.=$val[csf('order_id')]."_".$val[csf('currence')]."_".$val[csf('exchange_rate')]."_".$rate;
	/*	echo "$('#piece_rate_data_string').val('".$sql[0][csf('currence')]."');\n";
		echo "$('#hidden_exchange_rate').val('".$sql[0][csf('exchange_rate')]."');\n";
		echo "$('#hidden_piece_rate').val('".$rate."');\n";*/
	}
	echo "$('#piece_rate_data_string').val('".$data_string."');\n";
}

if($action=="create_job_search_list_view")
{
    $ex_data = explode("_",$data);
	$company = $ex_data[0];
	$buyer = $ex_data[1];
	$from_date = $ex_data[2];
	$to_date = $ex_data[3];
	$job_prifix= $ex_data[4];
	$job_year = $ex_data[5];
	$po_no = $ex_data[6];
	$job_cond="";

	if(str_replace("'","",$company)=="") $conpany_cond=""; else $conpany_cond="and b.company_name=".str_replace("'","",$company)."";
	if(str_replace("'","",$buyer)==0) $buyer_cond=""; else $buyer_cond="and b.buyer_name=".str_replace("'","",$buyer)."";
    if($db_type==2) $year_cond=" and extract(year from b.insert_date)=$job_year";
    if($db_type==0) $year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=$job_year";
	if(str_replace("'","",$job_prifix)!="")  $job_cond="and b.job_no_prefix_num=".str_replace("'","",$job_prifix)."  $year_cond";
	if(str_replace("'","",$po_no)!="")  $order_cond="and a.po_number like '%".str_replace("'","",$po_no)."%' "; else $order_cond="";
	if($db_type==0)
	{
		if( $from_date!="" && $to_date!="" ) $sql_cond = " and a.pub_shipment_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
	$sql_order="select b.job_no,b.buyer_name,a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num as job_prefix,SUBSTRING_INDEX(b.insert_date, '-', 1) as year from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no $buyer_cond  $sql_cond $conpany_cond $job_cond $order_cond group by b.buyer_name,b.job_no,a.po_number ";
	}

	if($db_type==2)
	{
	 if( str_replace("'","",$from_date)!="" && str_replace("'","",$to_date)!="" )
	  {
		  $sql_cond = " and a.pub_shipment_date  between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
	  }


	$sql_order="select b.job_no,b.buyer_name,a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num as job_prefix,extract(year from b.insert_date) as year from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no $buyer_cond  $sql_cond $conpany_cond $job_cond $order_cond group by  b.job_no,b.buyer_name,a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num, b.insert_date order by  job_no_prefix_num";
	}
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$arr=array (3=>$buyer_arr);
	echo create_list_view("list_view", "Job NO,Year,Style Ref,Buyer Name, Orer No,Shipment Date","100,100,150,150,150,150","850","270",0, $sql_order , "js_set_order", "job_no,buyer_name,year", "", 1, "0,0,0,buyer_name,0,0,0", $arr, "job_prefix,year,style_ref_no,buyer_name,po_number,pub_shipment_date", "","setFilterGrid('list_view',-1)") ;

}

$operation_mapping[91]="3_1*2_1";
$operation_mapping[92]="3_2*2_2";
$operation_mapping[94]="3_4*2_4";
$operation_mapping[95]="3_1*2_1";

$operation_mapping[1]="3_1*2_1";
$operation_mapping[1]="3_1*2_1";
$operation_mapping[1]="3_1*2_1";
$operation_mapping[1]="3_1*2_1";


if ($action=="service_booking_popup")
{
	echo load_html_head_contents("Booking Search","../../", 1, 1, $unicode);
	extract($_REQUEST);


	$preBookingNos = 0;
	?>

	<script>
		
		function js_set_value(booking_no)
		{
			// alert(booking_no);
			document.getElementById('selected_booking').value=booking_no; //return;
	 	 	parent.emailwindow.hide();
		}
		
	</script>

	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="1300" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
				<tr>
					<td align="center" width="100%">
						<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                        	 <input type="text" id="selected_batchDtls" class="text_boxes" style="width:70px" value="<? echo $txt_batch_dtls;?>">
                              <input type="text" id="booking_no" class="text_boxes" style="width:70px" value="">
                              <input type="text" id="booking_id" class="text_boxes" style="width:70px">
                             
                             
							<thead>
								<th  colspan="11">
									<?
									echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --",1 );
									?>
								</th>
							</thead>
							<thead>                  
								<th width="150">Company Name</th>
								<th width="150">Supplier Name</th>
								<th width="150">Buyer  Name</th>
								<th width="100">Job  No</th>
								<th width="100">Order No</th>
								<th width="100">Internal Ref.</th>
								<th width="100">File No</th>
								<th width="100">Style No.</th>
								<th width="100">WO No</th>
								<th width="200">Date Range</th>
								<th></th>           
							</thead>
							<tr>
								<td> <input type="hidden" id="selected_booking">
									<? 
									echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", "".$company_id."", "load_drop_down( 'fabric_issue_to_finishing_process_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1);
									?>
								</td>
								<td>
									<?php 
									if($cbo_service_source==3)
									{
										echo create_drop_down( "cbo_supplier_name", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and b.party_type in (21,24,25) group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", "".$supplier_id."", "",1 );
									}
									else
									{
										echo create_drop_down( "cbo_supplier_name", 152, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name", 1, "-- Select --", "".$supplier_id."", "",1 );
									}
									?>
								</td>
								<td id="buyer_td">
									<? 
									echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
									?>
								</td>
								<td>
									<input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:70px">
								</td> 


								<td>
									<input name="txt_order_number" id="txt_order_number" class="text_boxes" style="width:70px">
								</td> 
								<td>
									<input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px">
								</td> 
								<td>
									<input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px">
								</td> 



								<td>
									<input name="txt_style" id="txt_style" class="text_boxes" style="width:70px">
								</td>
								<td>
									<input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:70px">
								</td> 
								<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px">
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px">
								</td> 
								<td align="center">
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_style').value+'_'+<? echo $preBookingNos;?>+'_'+document.getElementById('txt_order_number').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+<? echo $cbo_service_source;?>, 'create_booking_search_list_view', 'search_div', 'cutting_entry_controller_urmi','setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" /></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td  align="center" height="40" valign="middle"><? echo load_month_buttons(1);  ?>
						</td>
					</tr>
         
   </table>    
   <div style="width:100%; margin-top:5px" id="search_div" align="left"></div>
   
	</form>
	</div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if ($action=="create_booking_search_list_view")
{

	$data=explode('_',$data);
	// echo "<pre>";print_r($data);
	if ($data[0]!=0) $company=" and c.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
    if ($data[1]!=0) $buyer=" and c.buyer_name='$data[1]'"; else $buyer="";
    
    if($db_type==0)
    {
    	if ($data[2]!="" &&  $data[3]!="") $wo_date  = "and a.wo_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $wo_date ="";
    }
    
    if($db_type==2)
    {
    	if ($data[2]!="" &&  $data[3]!="") $wo_date  = "and a.wo_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $wo_date ="";
    }
    //echo $data[8];
    if($data[6]==1)
    {
    	if (str_replace("'","",$data[5])!="") $wo_cond=" and a.sys_number_prefix_num='$data[5]'    "; else  $wo_cond="";
    	if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num='$data[4]'  "; else  $job_cond=""; 
    	if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no='$data[8]'  "; else  $style_cond=""; 
    }
    if($data[6]==4 || $data[6]==0)
    {
    	if (str_replace("'","",$data[5])!="") $wo_cond=" and a.sys_number_prefix_num like '%$data[5]%'  $booking_year_cond  "; else  $wo_cond="";
    	if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]%'  $year_cond  "; else  $job_cond=""; 
    	if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no like '%$data[8]%'  "; else  $style_cond=""; 
    }
    
    if($data[6]==2)
    {
    	if (str_replace("'","",$data[5])!="") $wo_cond=" and a.sys_number_prefix_num like '$data[5]%'  $booking_year_cond  "; else  $wo_cond="";
    	if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '$data[4]%'  $year_cond  "; else  $job_cond=""; 
    	if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no like '$data[8]%'  "; else  $style_cond=""; 
    }
    
    if($data[6]==3)
    {
    	if (str_replace("'","",$data[5])!="") $wo_cond=" and a.sys_number_prefix_num like '%$data[5]'  $booking_year_cond  "; else  $wo_cond="";
    	if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]'  $year_cond  "; else  $job_cond="";
    	if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no like '%$data[8]'  "; else  $style_cond="";  
    } 

    if ($data[9]!="")
    {
    	foreach(explode(",", $data[9]) as $bok){
    		$bookingnos .= "'".$bok."',";
    	}
    	$bookingnos = chop($bookingnos,",");
		if( $service_source!=1)
		{
    	$preBookingNos_1 = " and a.booking_no not in (".$bookingnos.")";
    	$preBookingNos_2 = " and a.wo_no not in (".$bookingnos.")";
		}
    }
    if ($data[10]!="")
    {
    	$po_number_cond = " and d.po_number = '$data[10]'";  	
    }
    if ($data[11]!="")
    {    	
    	$internal_ref_cond = " and d.grouping = '$data[11]'";
    }
    if ($data[12]!="")
    {
    	$file_cond = " and d.file_no = '$data[12]'";
    }


    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
    $comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
    $suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
    $po_no=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
    
    $arr=array (2=>$comp,3=>$conversion_cost_head_array,4=>$buyer_arr,7=>$po_no,8=>$item_category,9=>$fabric_source,10=>$suplier);
         
	$sql= "SELECT a.id, a.sys_number_prefix_num, a.sys_number, a.wo_date, c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no, c.job_no, b.po_id, d.file_no,d.po_number,d.grouping  
	from garments_service_wo_mst a, garments_service_wo_dtls b, wo_po_details_master c ,wo_po_break_down d 
	where a.id = b.mst_id  and b.po_id=d.id and c.id=d.job_id  $company $buyer $wo_date $wo_cond $style_cond $file_cond $po_number_cond $internal_ref_cond  and  a.status_active=1 and a.is_deleted=0 and b.rate_for=20 $job_cond
	group by a.id, a.sys_number_prefix_num, a.sys_number, a.wo_date, c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no, c.job_no, b.po_id, d.file_no,d.po_number,d.grouping";    
   	// echo $sql;	
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="770" class="rpt_table">
    	<thead>
    		<tr>
    			<th width="20">SL No.</th>
    			<th width="120">WO No</th>
    			<th width="60">WO Date</th>
    			<th width="80">Company</th>
    			<th width="100">Buyer</th>
    			<th width="50">Job No</th>

    			<th width="70">Internal Ref.</th>
    			<th width="70">File No</th>


    			<th width="100">Style No.</th>
    			<th width="100">PO number</th>
    		</tr>
    	</thead>
    </table>
    <div style="width:1288px; max-height:400px; overflow-y:scroll;" >	 
    	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="770" class="rpt_table" id="tbl_list_search" >  
    		<tbody>
    			<?
    			$result = sql_select($sql);	        
	    		$i=1; 
	            foreach($result as $row)
	            { 					
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                     <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('sys_number')]; ?>');"> 
                    
						<td width="20"><? echo $i; ?></td>
						<td width="120"><p><? echo $row[csf('sys_number')]; ?></p></td>
						<td width="60"><p><? echo change_date_format($row[csf('wo_date')]); ?></p></td>
						<td width="80"><p><? echo $comp[$row[csf('company_name')]]; ?></p></td>

						<td width="100"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
						<td width="50"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>


						<td width="70"><p><? echo $row[csf('grouping')]; ?></p></td>
						<td width="70"><p><? echo $row[csf('file_no')]; ?></p></td>

						<td width="100"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
						<td width="100"><p><? echo $row[csf('po_number')]; ?></p></td>
						
					</tr>
					<?
					$i++;    				
    			}
    			?>
    		</tbody>
    	</table>
    </div>
    <script type="text/javascript">
    	setFilterGrid("tbl_list_search",-1);
    </script>
    <?	

    exit();
}

//master data save update delete here------------------------------//
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	
	$bundle_wise_type_sql="SELECT b.order_id, b.bundle_no ,b.color_type_id from ppl_cut_lay_mst a,ppl_cut_lay_bundle b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.cutting_no='$txt_cutting_no' ";
	$bundle_wise_type_array=array();
	$bundle_wise_data=sql_select($bundle_wise_type_sql);
	$order_id_arr = array();
	foreach($bundle_wise_data as $vals)
	{
		$bundle_wise_type_array[$vals[csf("bundle_no")]]=$vals[csf("color_type_id")];
		$order_id_arr[$vals[csf("order_id")]] = $vals[csf("order_id")];
	}

	$order_id_cond = where_con_using_array($order_id_arr,0,"po_break_down_id");

	$color_size_arr=sql_select("SELECT id,size_number_id,color_number_id,po_break_down_id,country_id,item_number_id from wo_po_color_size_breakdown where status_active =1 and is_deleted=0 $order_id_cond ");
	$color_beckdown_arr=array();
	foreach($color_size_arr as $val)
	{
		$color_beckdown_arr[$val[csf("po_break_down_id")]][$val[csf("country_id")]][$val[csf("item_number_id")]][$val[csf("color_number_id")]][$val[csf("size_number_id")]]=$val[csf("id")];
		$color_beckdown_arr_grp[$val[csf("po_break_down_id")]][$val[csf("item_number_id")]][$val[csf("color_number_id")]][$val[csf("size_number_id")]] .=",".$val[csf("id")];
	}




	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
	    $con = connect();
	    if($db_type==0)	{ mysql_query("BEGIN"); }
		 // entry form 160 = production all pages
	     //if ( check_table_status( 160, 1 )==0 ) { echo "15**0"; die;}
		$cutting_qc_no= return_field_value("cutting_qc_no"," pro_gmts_cutting_qc_mst","status_active=1 and is_deleted=0 and cutting_no='".trim($txt_cutting_no)."'");
		if($cutting_qc_no!="") { echo "200**".$cutting_qc_no; disconnect($con); die;}
		//echo "10**$txt_cutting_no rr";die;




	    $field_array="id,delivery_mst_id, garments_nature, company_id, production_source, serving_company,country_id, po_break_down_id,item_number_id, location, production_date,production_quantity,production_type, entry_break_down_type, production_hour, floor_id, reject_qnty,replace_qty,total_produced, yet_to_produced, cut_no, batch_no,wo_order_id,wo_order_no,currency_id,exchange_rate,rate,amount,shift_name, inserted_by, insert_date";

	    $field_array_dtls="id, mst_id,delivery_mst_id,production_type,color_size_break_down_id,production_qnty,cut_no,bundle_no,barcode_no,reject_qty,replace_qty,color_type_id";

		//$field_array_qc_mst="id,cut_qc_prefix,cut_qc_prefix_no,cutting_qc_no,cutting_no,location_id,floor_id,table_no, company_id,cutting_qc_date, cutting_qc_time, production_source, serving_company, work_order_id, workorder_rate_breakdown,remarks, inserted_by,insert_date, status_active,is_deleted";
		$field_array_qc_mst="id,cut_qc_prefix,cut_qc_prefix_no,cutting_qc_no,cutting_no,location_id,floor_id,table_no,job_no,batch_id, company_id, entry_date,start_time, end_date,end_time ,marker_length,marker_width,fabric_width, gsm,width_dia,cutting_qc_date, cutting_qc_time, production_source, serving_company, work_order_id,wo_order_no, workorder_rate_breakdown,remarks, inserted_by,insert_date, status_active,is_deleted";

	    $field_array_qc_dtls="id,mst_id,order_id,item_id,country_id,color_id,size_id,color_size_id,bundle_no,barcode_no,number_start,number_end,bundle_qty, reject_qty, replace_qty, qc_pass_qty, inserted_by, insert_date, status_active,is_deleted";
		$field_array_defect="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty,bundle_no, inserted_by, insert_date";
		// echo "10**";
		//die;

		$txt_order_id=explode("*",$txt_order_id);
		$hidden_lay_dtls_id=explode("*",$hidden_lay_dtls_id);
		$country_id=explode("___",$country_id);
		$hidden_color=explode("*",$hidden_color);
		$txt_gmt_id=explode("*",$txt_gmt_id);
		$total_qc_qty=explode("*",$total_qc_qty);
		$total_reject_qty=explode("*",$total_reject_qty);
		$txt_qty=explode("___",trim($txt_qty));
		$txt_bundle_no=explode("___",$txt_bundle_no);
		$txt_start=explode("___",$txt_start);
		$txt_end=explode("___",$txt_end);
		$txt_reject=explode("___",$txt_reject);
		$txt_qcpass=explode("___",$txt_qcpass);
		$txt_replace=explode("___",$txt_replace);
		$size_id=explode("___",$size_id);
		$size_details_id=explode("___",$size_details_id);
		$size_details_qty=explode("___",$size_details_qty);
		$size_details_bdl=explode("___",$size_details_bdl);
		$pcs_per_bdl=explode("___",$pcs_per_bdl);
		$actual_reject=explode("##",$actual_reject);
		$hidden_barcode_no_data=explode("__",$hidden_barcode_no);

		//$id=return_next_id("id", "pro_garments_production_mst", 1);
		 // $id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",   "pro_garments_production_mst", $con );

		//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
		 $dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

		//$qc_id=return_next_id("id", "pro_gmts_cutting_qc_mst", 1);
		$qc_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_mst_seq",  "pro_gmts_cutting_qc_mst", $con );

	    //$qc_dtls_id=return_next_id("id", "pro_gmts_cutting_qc_dtls", 1);
	    $qc_dtls_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_dtls_seq",   "pro_gmts_cutting_qc_dtls", $con );
		//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
		$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq", "pro_gmts_prod_dft", $con );
		if($db_type==0)
		{
			$txt_cutting_date=change_date_format($txt_cutting_date,"yyyy-mm-dd");
			$txt_entry_date=change_date_format($txt_entry_date,"yyyy-mm-dd");
			$txt_end_date=change_date_format($txt_end_date,"yyyy-mm-dd");
			$year_id="YEAR(insert_date)=";
		}
		if($db_type==2)
		{
			$txt_cutting_date=change_date_format($txt_cutting_date,"yyyy-mm-dd","-",1);
			$txt_entry_date=change_date_format($txt_entry_date,"yyyy-mm-dd","-",1);
			$txt_end_date=change_date_format($txt_end_date,"yyyy-mm-dd","-",1);
			$year_id=" extract(year from insert_date)=";
			$txt_cutting_hour=str_replace("'","",$txt_cutting_date)." ".str_replace("'","",$txt_cutting_hour);
			$txt_cutting_hour="to_date('".$txt_cutting_hour."','DD MONTH YYYY HH24:MI:SS')";
		}

		$new_system_id = explode("*", return_next_id_by_sequence("", "pro_gmts_cutting_qc_mst",$con,1,$cbo_company,'CQ',0,date("Y",time()),0,0,0,0,0 ));

		if(str_replace("'","",$txt_in_time_hours)!="" && str_replace("'","",$txt_in_time_minuties)!="")
		{
	     	$start_time=str_replace("'","",$txt_in_time_hours).":".str_replace("'","",$txt_in_time_minuties);
		}
		else  $start_time="";
		if(str_replace("'","",$txt_out_time_hours)!="" && str_replace("'","",$txt_out_time_minuties)!="")
		{
		 	$end_time=str_replace("'","",$txt_out_time_hours).":".str_replace("'","",$txt_out_time_minuties);
		}
		else  $end_time="";
		/*if($db_type==0)
		{
			 $data_arra_cutt_mst="(".$qc_id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."','".$txt_cutting_no."',".$cbo_location_name.",".$cbo_floor_name.",'".$txt_table_no."',".$cbo_company.",'".$txt_cutting_date."','".$txt_cutting_hour."','".$cbo_source."','".$cbo_cutting_company."','".$cbo_work_order."','".$piece_rate_data_string."',".$txt_remarks.",".$user_id.",'".$pc_date_time."',1,0)";
		}
		else
		{
		 	$data_arra_cutt_mst="insert into pro_gmts_cutting_qc_mst (".$field_array_qc_mst.") VALUES(".$qc_id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."','".$txt_cutting_no."',".$cbo_location_name.",".$cbo_floor_name.",'".$txt_table_no."',".$cbo_company.",'".$txt_cutting_date."',".$txt_cutting_hour.",'".$cbo_source."','".$cbo_cutting_company."','".$cbo_work_order."','".$piece_rate_data_string."',".$txt_remarks.",".$user_id.",'".$pc_date_time."',1,0)";
		}
		*/

		if($db_type==0)
		{
			 $data_arra_cutt_mst="(".$qc_id.",'".$new_system_id[1]."',".(int)$new_system_id[2].",'".$new_system_id[0]."','".$txt_cutting_no."','".$cbo_location_name."','".$cbo_floor_name."','".$txt_table_no."','".$txt_job_no."', '".$txt_batch_no."',".$cbo_company.",'".$txt_entry_date."','".$start_time."','".$txt_end_date."','".$end_time."','".$txt_marker_length."','".$txt_marker_width."','".$txt_fabric_width."','".$txt_gsm."','".$cbo_width_dia."','".$txt_cutting_date."','".$txt_cutting_hour."','".$cbo_source."','".$cbo_cutting_company."','".$cbo_work_order."','".$txt_wo_no."','".$piece_rate_data_string."',".$txt_remarks.",".$user_id.",'".$pc_date_time."',1,0)";
		}
		else
		{
		 	 $data_arra_cutt_mst="insert into pro_gmts_cutting_qc_mst (".$field_array_qc_mst.") VALUES(".$qc_id.",'".$new_system_id[1]."',".(int)$new_system_id[2].",'".$new_system_id[0]."','".$txt_cutting_no."','".$cbo_location_name."','".$cbo_floor_name."','".$txt_table_no."','".$txt_job_no."', '".$txt_batch_no."',".$cbo_company.",'".$txt_entry_date."','".$start_time."','".$txt_end_date."','".$end_time."','".$txt_marker_length."','".$txt_marker_width."','".$txt_fabric_width."','".$txt_gsm."','".$cbo_width_dia."','".$txt_cutting_date."',".$txt_cutting_hour.",'".$cbo_source."','".$cbo_cutting_company."','".$cbo_work_order."','".$txt_wo_no."','".$piece_rate_data_string."',".$txt_remarks.",".$user_id.",'".$pc_date_time."',1,0)";
		}


		$order_wise_rate_data_arr=array();
		$piece_rate_data_string=explode(",",$piece_rate_data_string);
		foreach($piece_rate_data_string as $order_rate_data)
		{
			$order_rate_data_arr=explode("_",$order_rate_data);
			$order_wise_rate_data_arr[$order_rate_data_arr[0]]["currency"]=$order_rate_data_arr[1];
			$order_wise_rate_data_arr[$order_rate_data_arr[0]]["exchange_rate"]=$order_rate_data_arr[2];
			$order_wise_rate_data_arr[$order_rate_data_arr[0]]["rate"]=$order_rate_data_arr[3];
		}
		$sequence_array=array();
		$color_size_is_zero=1;
		// echo "10**$txt_cutting_no==".count($txt_order_id);die();

		$mst_id_array = array();
		$mst_id_chk_array = array();
		$country_qcpass_qty = array();
	    for($i=0;$i<count($txt_order_id); $i++)
		{
			// $txt_lay_dtls_id=explode("*",$hidden_lay_dtls_id[$i]);
			$txt_country_id=explode("*",$country_id[$i]);
			$txt_size_qty=explode("*",trim($txt_qty[$i]));
			$txt_bundle_number=explode("*",$txt_bundle_no[$i]);
			$txt_bdl_start=explode("*",$txt_start[$i]);
			$txt_bdl_end=explode("*",$txt_end[$i]);
			$txt_reject_qty=explode("*",$txt_reject[$i]);
			$txt_replace_qty=explode("*",$txt_replace[$i]);
			$txt_qcpass_qty=explode("*",$txt_qcpass[$i]);
			$txt_size_id=explode("*",$size_id[$i]);
			$txt_size_details_id=explode("*",$size_details_id[$i]);
	    	$txt_size_details_qty=explode("*",$size_details_qty[$i]);
		    $txt_size_details_bdl=explode("*",$size_details_bdl[$i]);
		    $txt_pcs_per_bdl=explode("*",$pcs_per_bdl[$i]);
			$txt_rmg_start=explode("*",$txt_start[$i]);
		    $txt_rmg_end=explode("*",$txt_end[$i]);
			$hidden_barcode_no_all=explode("*",$hidden_barcode_no_data[$i]);
			$txt_actual_reject=explode("_",$actual_reject[$i]);
			$k=0;

			//echo "10**";
			//if($i!=0) $data_array_qc_detls.=",";
			//if($i!=0) $data_array_bundle_dtls.=",";
			//if($i!=0) $data_array_bundle.=",";
			//if($i!=0) $data_array_dtls.=",";

		    for($m=0;   $m<count($txt_bundle_number); $m++)
		    {

				$grp_col_id=ltrim($color_beckdown_arr_grp[$txt_order_id[$i]][$txt_gmt_id[$i]][$hidden_color[$i]][$txt_size_id[$m]],",");
				//echo $grp_col_id."==";
				 if($checked_pos[$grp_col_id]=='')
				 {
					$sequence_array=check_operation_status( $grp_col_id, $txt_order_id[$i], $txt_job_no, $txt_cutting_no, $hidden_barcode_no_all[$m], $sequence_array );
					$checked_pos[$grp_col_id]=$grp_col_id;
				 }


				if(str_replace("'","",$txt_qcpass_qty[$m])!="")
				{
					$color_size_bkdown_id=$color_beckdown_arr[$txt_order_id[$i]][$txt_country_id[$m]][$txt_gmt_id[$i]][$hidden_color[$i]][$txt_size_id[$m]];
					if(!$color_size_bkdown_id)
					{
					 	$color_size_is_zero++;
					}
					// print_r($sequence_array);

					if(!isset($mst_id_chk_array[$txt_order_id[$i]][$txt_gmt_id[$i]][$txt_country_id[$m]][$hidden_lay_dtls_id[$i]]))
					{
						$id = return_next_id_by_sequence(  "pro_gar_production_mst_seq", "pro_garments_production_mst", $con );
						$mst_id_array[$txt_order_id[$i]][$txt_gmt_id[$i]][$txt_country_id[$m]][$hidden_lay_dtls_id[$i]] = $id;
						$mst_id_chk_array[$txt_order_id[$i]][$txt_gmt_id[$i]][$txt_country_id[$m]][$hidden_lay_dtls_id[$i]] = $id;
					}
					$id = $mst_id_array[$txt_order_id[$i]][$txt_gmt_id[$i]][$txt_country_id[$m]][$hidden_lay_dtls_id[$i]];
					// echo $id."<br>";

					 if(str_replace("'","",$txt_reject_qty[$m])=="") $txt_reject_qty[$m]=0;
					 if(str_replace("'","",$txt_replace_qty[$m])=="") $txt_replace_qty[$m]=0;
					// die;
					 $rls=0;
					 if($txt_actual_reject[$m]!="")
					 {
						$actual_reject_info=explode("**",$txt_actual_reject[$m]);
						for($rls=0;$rls<count($actual_reject_info); $rls++)
						{
							$bundle_reject_info=explode("*",$actual_reject_info[$rls]);
							if( trim($data_array_defect)!="") $data_array_defect.=",";
							$defectPointId=$bundle_reject_info[0];
							$defect_qty=$bundle_reject_info[1];

							$data_array_defect.="(".$dft_id.",".$id.",1,".$txt_order_id[$i].",3,".$defectPointId.",'".$defect_qty."','".$hidden_barcode_no_all[$m]."',".$user_id.",'".$pc_date_time."')";
							//$dft_id++;
							$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
						}
					 }
					 $bundleCheckArr[trim($txt_bundle_number[$m])]=trim($txt_bundle_number[$m]);
					 if($data_array_dtls!='') $data_array_dtls.=",";
					 $data_array_dtls .= "(".$dtls_id.",".$id.",".$qc_id.",1,'".$color_size_bkdown_id."','".$txt_qcpass_qty[$m]."','".$txt_cutting_no."','".$txt_bundle_number[$m]."','".$hidden_barcode_no_all[$m]."','".$txt_reject_qty[$m]."','".$txt_replace_qty[$m]."','".$bundle_wise_type_array[$txt_bundle_number[$m]]."')";
					 //$dtls_id=$dtls_id+1;
					 $dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
					 // echo $id."<br>";


					 if($data_array_qc_detls!='') $data_array_qc_detls.=",";

					 $data_array_qc_detls.="(".$qc_dtls_id.",".$qc_id.",".$txt_order_id[$i].",".$txt_gmt_id[$i].",'".$txt_country_id[$m]."','".$hidden_color[$i]."','".$txt_size_id[$m]."','".$color_size_bkdown_id."','".$txt_bundle_number[$m]."','".$hidden_barcode_no_all[$m]."','".$txt_rmg_start[$m]."','".$txt_rmg_end[$m]."','".trim($txt_size_qty[$m])."','".$txt_reject_qty[$m]."','".$txt_replace_qty[$m]."','".$txt_qcpass_qty[$m]."',".$user_id.",'".$pc_date_time."',1,0)";
					  $qc_dtls_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_dtls_seq","pro_gmts_cutting_qc_dtls", $con );
					
					$country_qcpass_qty[$txt_order_id[$i]][$txt_gmt_id[$i]][$txt_country_id[$m]]+=$txt_qcpass_qty[$m]*1;
					$country_reject_qty[$txt_order_id[$i]][$txt_gmt_id[$i]][$txt_country_id[$m]]+=$txt_reject_qty[$m]*1;
					$country_replace_qty[$txt_order_id[$i]][$txt_gmt_id[$i]][$txt_country_id[$m]]+=$txt_replace_qty[$m]*1;
				}
			}
			//  die;
			// die;
			// print_r($country_qcpass_qty);die;
			// echo "10**$txt_cutting_no==".count($country_qcpass_qty);die();
			foreach($country_qcpass_qty as $order_id=>$order_data)
			{
				foreach($order_data as $item_id=>$item_data)
				{
					foreach($item_data as $c_id=>$c_qty)
					{
						$plan_cut_qnty=return_field_value("sum(plan_cut_qnty) as plan_cut_qty","wo_po_color_size_breakdown","po_break_down_id=".$order_id."
						and item_number_id=".$item_id." and  country_id=".$c_id." and status_active =1 and is_deleted=0","plan_cut_qty");

						$total_produced = return_field_value("sum(production_quantity) as total_cut","pro_garments_production_mst","po_break_down_id=".$order_id." and item_number_id=".$item_id." and  country_id=".$c_id." and production_type=1 and is_deleted=0","total_cut");
						$yet_to_produced=$plan_cut_qnty-$total_produced;

						$currency_id=$order_wise_rate_data_arr[$order_id]["currency"];
						$exchange_rate=$order_wise_rate_data_arr[$order_id]["exchange_rate"];
						$rate=str_replace("'","",$order_wise_rate_data_arr[$order_id]["rate"]);
						if($rate!="") {  $amount=$rate*str_replace("'","",trim($c_qty))*1;}
						else {$amount="";}

						if($country_reject_qty[$order_id][$item_id][$c_id]=="") $country_reject_qty[$order_id][$item_id][$c_id]=0;
						if($country_replace_qty[$order_id][$item_id][$c_id]=="") $country_replace_qty[$order_id][$item_id][$c_id]=0;

						$id = $mst_id_array[$order_id][$item_id][$c_id][$hidden_lay_dtls_id[$i]];
						if($db_type==0)
						{
							if($data_array!='') $data_array .=",";
							$data_array.="(".$id.",".$qc_id.",".$garments_nature.",".$cbo_company.",'".$cbo_source."','".$cbo_cutting_company."',".$c_id.",".$order_id.", ".$item_id.",'".$cbo_location_name."','".$txt_cutting_date."',".trim($c_qty).",1,3,'".$txt_cutting_hour."','".$cbo_floor_name."',".$country_reject_qty[$order_id][$item_id][$c_id].",".$country_replace_qty[$order_id][$item_id][$c_id].",'".$total_produced."','".$yet_to_produced."','".$txt_cutting_no."','".$txt_batch_no."','".$cbo_work_order."','".$txt_wo_no."','".$currency_id."','".$exchange_rate."','".$rate."','".$amount."',".$cbo_shift_name.",".$user_id.",'".$pc_date_time."')";
							//$id=$id+1;
							// $id= return_next_id_by_sequence(  "pro_gar_production_mst_seq", "pro_garments_production_mst", $con );
						}
						else
						{
							$data_array.=" INTO pro_garments_production_mst (".$field_array.") VALUES(".$id.",".$qc_id.",".$garments_nature.",".$cbo_company.",'".$cbo_source."','".$cbo_cutting_company."','".$c_id."',".$order_id.", ".$item_id.",'".$cbo_location_name."', '".$txt_cutting_date."',".trim($c_qty).",1,3,".$txt_cutting_hour.",'".$cbo_floor_name."','".$country_reject_qty[$order_id][$item_id][$c_id]."','".$country_replace_qty[$order_id][$item_id][$c_id]."','".$total_produced."', '".$yet_to_produced."','".$txt_cutting_no."','".$txt_batch_no."','".$cbo_work_order."','".$txt_wo_no."','".$currency_id."','".$exchange_rate."','".$rate."', '".$amount."',".$cbo_shift_name.", ".$user_id.",'".$pc_date_time."')";
							//$id=$id+1;
							// $id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );
						}
					}
				}
			}
			// echo "10**".$data_array;die;
			unset($country_qcpass_qty);
			unset($country_reject_qty);
			unset($country_replace_qty);
		    unset($check_size_id);
		} // end for loop
		// echo "10**".$color_size_is_zero;die;
		 if ($color_size_is_zero!=1)
		  {
		  	echo "222**222";disconnect($con);die;
 		  }

 		  $bundle="'".implode("','",$bundleCheckArr)."'";
 		  $receive_sql="SELECT c.barcode_no,c.bundle_no from pro_garments_production_dtls c where  c.bundle_no  in ($bundle)  and c.production_type=1 and c.status_active=1 and c.is_deleted=0";
            $receive_result = sql_select($receive_sql);
            foreach ($receive_result as $row)
            {

                $duplicate_bundle[trim($row[csf('bundle_no')])]=trim($row[csf('bundle_no')]);
            }
            if(count($duplicate_bundle)>0) { echo "200**"; disconnect($con); die;}
           // echo "10**".count($duplicate_bundle) ;die;

		 //echo "10**";
		/* $duplicate_seq=array();
		 $dupsql=sql_select("select col_size_id from pro_production_sequence where col_size_id in ( ".implode(",", $checked_pos ).") and cutting_no='".$txt_cutting_no."'");
		 foreach($dupsql as $dupsq)
		 {
			 $duplicate_seq[$dupsq[csf("col_size_id")]]=$dupsq[csf("col_size_id")];
		 }*/
		 //echo "select col_size_id from pro_production_sequence where col_size_id in ( ".implode(",", $checked_pos ).")";
		//print_r( $sequence_array);
		//die;
		 $duplicate_seq=array();

		 $field_array_sq="id,job_no,po_number,col_size_id,preceding_op,succeding_op,current_operation,embel_name,cutting_no";
		 foreach($sequence_array as $po=>$seq)
		 {
			  foreach($seq as $op=>$opd)
			  {
				  $pos=explode(",",$po);
				  foreach($pos as $pid)
				  {
					  if( $duplicate_seq[$pid]=='')
					  {
					  	$seqid = return_next_id_by_sequence(  "pro_production_sequence_seq",  "pro_production_sequence", $con );
						  if($data_array_sq!='') $data_array_sq .=",";
							$emp=explode("_",$opd['preceding']);
							$data_array_sq.="(".$seqid.",'".$opd['job_no']."',".$opd['po_no'].",".$pid.",'".$emp[0]."','".$opd['succeding']."',".$op.",'".$emp[1]."','".$opd['cut_no']."')";
 					  }
				  }
			  }
		 }

		if( $data_array_sq!='' ) $rIDsq=sql_insert("pro_production_sequence",$field_array_sq,$data_array_sq,1); else $rIDsq=1;
		// print_r( $sequence_array );
		 // oci_rollback($con);
		//$seqid=return_next_id("id", "pro_production_sequence", 1);
	 	// die;
		//
		// echo "10**INSERT INTO pro_garments_production_mst (".$field_array.") VALUES ".$data_array."";
		// echo "10**INSERT INTO pro_garments_production_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls.""; die;
		// echo "10**".$query="INSERT ALL ".$data_array." SELECT * FROM dual";die();
	    if($db_type==2)
		{
			$con=connect();
			$query="INSERT ALL ".$data_array." SELECT * FROM dual";
			$rID=execute_query($query);
			$rID_mst=execute_query($data_arra_cutt_mst);
		}
		else
		{
			$rID=sql_insert("pro_garments_production_mst",$field_array,$data_array,1);
			$rID_mst=sql_insert("pro_gmts_cutting_qc_mst",$field_array_qc_mst,$data_arra_cutt_mst,1);
		}
		$rID_dtls=sql_insert("pro_gmts_cutting_qc_dtls",$field_array_qc_dtls,$data_array_qc_detls,1);
		$defectQ=1;
		if($data_array_defect!="")
		{
			//echo $data_array_defect;die;
			$defectQ=sql_insert("pro_gmts_prod_dft",$field_array_defect,$data_array_defect,1);
		}


			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
 		// echo $data_array_qc_detls;

		// echo "10**".$rID . $dtlsrID . $rID_mst . $rID_dtls .$defectQ  . $rIDsq;die;	

		if($db_type==0)
		{
			if($rID && $dtlsrID && $rID_mst && $rID_dtls && $defectQ )
			{
				mysql_query("COMMIT");
				echo "0**".$new_system_id[0]."**".$txt_cutting_no;
			}
	  		else
			{
				mysql_query("ROLLBACK");
				echo "10**".$new_system_id[0];
			}
		 }
		

		 if($db_type==2 || $db_type==1 )
		 {

			if($rID && $dtlsrID && $rID_mst && $rID_dtls && $defectQ  && $rIDsq)
			{
				oci_commit($con);
				echo "0**".$new_system_id[0]."**".$txt_cutting_no;
			}
	   		else
			{
				oci_rollback($con);
				echo "10**".$new_system_id[0];
			}

		}
		//check_table_status( 160,0);
		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		//$con = connect();
		//if($db_type==0)	{ mysql_query("BEGIN"); }
		//check_table_status( $_SESSION['menu_id'],0);
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$con = connect();
	    if($db_type==0)	{ mysql_query("BEGIN"); }
	   // if( check_table_status( 160, 1 )==0 ) { echo "15**1"; die;}
		$cut_no_prifix=return_field_value("cut_num_prefix_no"," ppl_cut_lay_mst"," status_active=1 and is_deleted=0 and cutting_no='".trim($txt_cutting_no)."'");//die;

		$field_array1="production_source*serving_company*location*floor_id*production_date*production_quantity*production_hour*reject_qnty*replace_qty*wo_order_id*wo_order_no*shift_name*currency_id* exchange_rate* rate*amount*updated_by*update_date";
	    $field_array_dtls="id, mst_id,production_type,color_size_break_down_id,production_qnty,cut_no,bundle_no,barcode_no,reject_qty,replace_qty,delivery_mst_id,color_type_id";
		//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
		$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
		//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
		$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );

		//$qc_dtls_id=return_next_id("id", "pro_gmts_cutting_qc_dtls", 1);
		$qc_dtls_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_dtls_seq",  "pro_gmts_cutting_qc_dtls", $con );

		$field_array_qc_mst="production_source*serving_company*location_id*floor_id*work_order_id*wo_order_no*workorder_rate_breakdown*cutting_qc_date*cutting_qc_time*remarks*updated_by*update_date";
	    $field_array_qc_dtls="reject_qty*replace_qty*qc_pass_qty* updated_by*update_date*is_deleted*status_active";
		$field_array_defect="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty,bundle_no, inserted_by, insert_date";
		$field_array_qc_save="id,mst_id,order_id,item_id,country_id,color_id,size_id,color_size_id,bundle_no,barcode_no,number_start,number_end,bundle_qty,reject_qty, replace_qty, qc_pass_qty,inserted_by,insert_date,status_active,is_deleted";

		 //echo "10** select * from pro_garments_production_mst where production_type=1 and cut_no='".$txt_cutting_no."'";die;
		//echo "10**";
		$product_mst_sql=sql_select("select * from pro_garments_production_mst where production_type=1 and status_active=1 and is_deleted=0 and cut_no='".trim($txt_cutting_no)."'");

		$product_mst_arr=array();
		foreach($product_mst_sql as $row)
		{
			$update_mst_arr[]=$row[csf("id")];
			$product_mst_arr[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]]=$row[csf("id")];
			$all_po_arr[]=$row[csf("id")];
		}


		$sql_check=sql_select("select bundle_no,production_type,production_qnty,reject_qty,replace_qty,spot_qty,alter_qty from pro_garments_production_dtls where cut_no='".trim($txt_cutting_no)."' and production_type=1 and status_active=1 and is_deleted=0");
		foreach($sql_check as $chkrow)
		{
				$actu_reject[$chkrow[csf("bundle_no")]]+=$chkrow[csf("reject_qty")];
				$old_prod_data[$chkrow[csf("bundle_no")]]['production_qnty']=$chkrow[csf("production_qnty")];
				$old_prod_data[$chkrow[csf("bundle_no")]]['replace_qty']=$chkrow[csf("replace_qty")];
				$old_prod_data[$chkrow[csf("bundle_no")]]['spot_qty']=$chkrow[csf("spot_qty")];
				$old_prod_data[$chkrow[csf("bundle_no")]]['alter_qty']=$chkrow[csf("alter_qty")];
				$old_prod_data[$chkrow[csf("bundle_no")]]['reject_qty']=$chkrow[csf("reject_qty")];
		}

		$sql_check=sql_select("select bundle_no,production_type,production_qnty,replace_qty,spot_qty,alter_qty,reject_qty from pro_garments_production_dtls where cut_no='".trim($txt_cutting_no)."' and production_type>1    and status_active=1 and is_deleted=0 order by production_type desc"); //and production_type>1
		// echo "10**";
		//$sql_check=sql_select("select bundle_no,production_type,production_qnty,reject_qty from pro_garments_production_dtls where cut_no='".$txt_cutting_no."' and production_type>1 and status_active=1 and is_deleted=0");
		foreach($sql_check as $chkrow)
		{
			/*if($chkrow[csf("production_type")]==1)
			{
				$old_prod_data[$chkrow[csf("bundle_no")]]['production_qnty']=$chkrow[csf("production_qnty")];
				$old_prod_data[$chkrow[csf("bundle_no")]]['replace_qty']=$chkrow[csf("replace_qty")];
				$old_prod_data[$chkrow[csf("bundle_no")]]['spot_qty']=$chkrow[csf("spot_qty")];
				$old_prod_data[$chkrow[csf("bundle_no")]]['alter_qty']=$chkrow[csf("alter_qty")];
				$old_prod_data[$chkrow[csf("bundle_no")]]['reject_qty']=$chkrow[csf("reject_qty")];
				//$old_prod_data[$chkrow[csf("bundle_no")]]['production_qnty']=$chkrow[csf("production_qnty")];
				//$old_prod_data[$chkrow[csf("bundle_no")]]['production_qnty']=$chkrow[csf("production_qnty")];
			}
			else*/
				$validate_on_prod[$chkrow[csf("bundle_no")]]= $chkrow[csf("production_qnty")]*1;
		}

			//print_r($validate_on_prod)	; die;

		$country_id=explode("___",str_replace(" ","",$country_id));
	    $update_details_id=explode("___",str_replace(" ","",$update_details_id));
		$txt_order_id=explode("*",str_replace(" ","",$txt_order_id));
		$hidden_color=explode("*",str_replace(" ","",$hidden_color));
		$txt_gmt_id=explode("*",str_replace(" ","",$txt_gmt_id));
		$total_qc_qty=explode("*",str_replace(" ","",$total_qc_qty));
		$total_reject_qty=explode("*",str_replace(" ","",$total_reject_qty));
		$txt_qty=explode("___",str_replace(" ","",$txt_qty));
		$txt_bundle_no=explode("___",str_replace(" ","",$txt_bundle_no));
		$txt_start=explode("___",str_replace(" ","",$txt_start));
		$txt_end=explode("___",$txt_end);
		$txt_reject=explode("___",str_replace(" ","",$txt_reject));
		$txt_replace=explode("___",str_replace(" ","",$txt_replace));
		$txt_qcpass=explode("___",str_replace(" ","",$txt_qcpass));
		$size_id=explode("___",str_replace(" ","",$size_id));
		$size_details_id=explode("___",str_replace(" ","",$size_details_id));
		$size_details_qty=explode("___",str_replace(" ","",$size_details_qty));
		$size_details_bdl=explode("___",str_replace(" ","",$size_details_bdl));
		$pcs_per_bdl=explode("___",str_replace(" ","",$pcs_per_bdl));
		$actual_reject=explode("##",str_replace(" ","",$actual_reject));
		$hidden_barcode_no_data=explode("__",$hidden_barcode_no);
		if($db_type==0)
		{
			  $txt_cutting_date=change_date_format($txt_cutting_date,"yyyy-mm-dd");
			  $txt_entry_date=change_date_format($txt_entry_date,"yyyy-mm-dd");
			  $txt_end_date=change_date_format($txt_end_date,"yyyy-mm-dd");
			  $year_id="YEAR(insert_date)=";
			  $txt_cutting_hour="'".$txt_cutting_hour."'";
		}
		if($db_type==2)
		{
			  $txt_cutting_date=change_date_format($txt_cutting_date,"yyyy-mm-dd","-",1);
			  $txt_entry_date=change_date_format($txt_entry_date,"yyyy-mm-dd","-",1);
			  $txt_end_date=change_date_format($txt_end_date,"yyyy-mm-dd","-",1);
			  $year_id=" extract(year from insert_date)=";
			  $txt_cutting_hour=str_replace("'","",$txt_cutting_date)." ".str_replace("'","",$txt_cutting_hour);
			  $txt_cutting_hour="to_date('".$txt_cutting_hour."','DD MONTH YYYY HH24:MI:SS')";
		}

		if($db_type==2)
		{
			$txt_reporting_hour=str_replace("'","",$txt_cutting_date)." ".str_replace("'","",$txt_reporting_hour);
			$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
		}

	  	$txt_mst_id=implode(",",$all_po_arr);
	  	$data_arra_cutt_mst="'".$cbo_source."'*".$cbo_cutting_company."*'".$cbo_location_name."'*'".$cbo_floor_name."'*'".$cbo_work_order."'*'".$txt_wo_no."'*'".$piece_rate_data_string."'*'".$txt_cutting_date."'*".$txt_cutting_hour."*".$txt_remarks."*".$user_id."*'".$pc_date_time."'";

		$order_wise_rate_data_arr=array();
		$piece_rate_data_string=explode(",",$piece_rate_data_string);
		foreach($piece_rate_data_string as $order_rate_data)
		{
			$order_rate_data_arr=explode("_",$order_rate_data);
			$order_wise_rate_data_arr[$order_rate_data_arr[0]]["currency"]=$order_rate_data_arr[1];
			$order_wise_rate_data_arr[$order_rate_data_arr[0]]["exchange_rate"]=$order_rate_data_arr[2];
			$order_wise_rate_data_arr[$order_rate_data_arr[0]]["rate"]=$order_rate_data_arr[3];
		}

	  	// echo "10**";
		//print_r($txt_order_id);die;
		for($i=0;$i<count($txt_order_id); $i++)
	  	{
			if($db_type==2)
			{
				$txt_reporting_hour=str_replace("'","",$txt_cutting_date)." ".str_replace("'","",$txt_reporting_hour);
				$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
			}

		    $txt_country_id=explode("*",$country_id[$i]);
			$update_detail_id=explode("*",$update_details_id[$i]);
			$txt_size_qty=explode("*",$txt_qty[$i]);
			$txt_bundle_number=explode("*",$txt_bundle_no[$i]);
			$txt_bdl_start=explode("*",$txt_start[$i]);
			$txt_bdl_end=explode("*",$txt_end[$i]);
			$txt_reject_qty=explode("*",$txt_reject[$i]);
			$txt_replace_qty=explode("*",$txt_replace[$i]);
			$txt_qcpass_qty=explode("*",$txt_qcpass[$i]);
			$txt_size_id=explode("*",$size_id[$i]);
			$txt_size_details_id=explode("*",$size_details_id[$i]);
	    	$txt_size_details_qty=explode("*",$size_details_qty[$i]);
		    $txt_size_details_bdl=explode("*",$size_details_bdl[$i]);
		    $txt_pcs_per_bdl=explode("*",$pcs_per_bdl[$i]);
			$txt_rmg_start=explode("*",$txt_start[$i]);
		    $txt_rmg_end=explode("*",$txt_end[$i]);
			$txt_actual_reject=explode("_",$actual_reject[$i]);
			$hidden_barcode_no_all=explode("*",$hidden_barcode_no_data[$i]);

			$k=0;
		    for($m=0;$m<count($txt_bundle_number); $m++)
		    {
				$txt_size_qty[$m]= trim($txt_size_qty[$m])*1;
				$chk=0;
				if( ( $validate_on_prod[$txt_bundle_number[$m]]-($txt_qcpass_qty[$m]*1)) >0 ) // $chkrow[csf("production_qnty")]
				{
					$chk=1;
					$mathced[$txt_bundle_number[$m]]=$txt_bundle_number[$m];
				}

				if( trim($txt_qcpass_qty[$m]) > trim($txt_size_qty[$m]) )
				{
					$chk=1;
					$mathced[$txt_bundle_number[$m]]=$txt_bundle_number[$m];
					//if($txt_bundle_number[$m]=='UHM-17-7106-227'){ echo "10**".$txt_size_qty[$m]."=".$txt_qcpass_qty[$m]; die;}
				}
				//if($txt_bundle_number[$m]=='UHM-17-7106-227'){ echo "10**".$txt_size_qty[$m]; die;}
			   $rls=0;
			   if( $txt_actual_reject[$m]!="" )
			   {
					$actual_reject_info=explode("**",$txt_actual_reject[$m]);
					for($rls=0;$rls<count($actual_reject_info); $rls++)
					{
						$bundle_reject_info=explode("*",$actual_reject_info[$rls]);
						if( trim($data_array_defect)!="") $data_array_defect.=",";
						$defectPointId=$bundle_reject_info[0];
						$defect_qty=$bundle_reject_info[1];

						$data_array_defect.="(".$dft_id.",".$product_mst_arr[$txt_order_id[$i]][$txt_gmt_id[$i]][$txt_country_id[$m]].",1,".$txt_order_id[$i].",3,".$defectPointId.",'".$defect_qty."','".$hidden_barcode_no_all[$m]."',".$user_id.",'".$pc_date_time."')";
						//$dft_id++;
						$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq", "pro_gmts_prod_dft", $con );
					}
			   }

				if($chk==0)
				//if(0==0)
				{
					if(str_replace("'","",$txt_qcpass_qty[$m])!="")
					{
					   $color_size_bkdown_id=$color_beckdown_arr[$txt_order_id[$i]][$txt_country_id[$m]][$txt_gmt_id[$i]][$hidden_color[$i]][$txt_size_id[$m]];
					   $bundle_no_creation=1;
					   if(str_replace("'","",$txt_reject_qty[$m])=="") $txt_reject_qty[$m]=0;
					   if(str_replace("'","",$txt_replace_qty[$m])=="") $txt_replace_qty[$m]=0;

					   if(str_replace("'",'',$update_detail_id[$m])!="")
					   {
						   $update_detail_arr[]=str_replace("'",'',$update_detail_id[$m]);
						   $update_detail=str_replace("'",'',$update_detail_id[$m]);
						   $data_array_qc_detls[$update_detail] =explode(",",("'".$txt_reject_qty[$m]."','".$txt_replace_qty[$m]."','".$txt_qcpass_qty[$m]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1"));
					   }
					   else
					   {
							if($data_array_qc_save!="") $data_array_qc_save.=",";
							$data_array_qc_save.="(".$qc_dtls_id.",".$update_id.",".$txt_order_id[$i].",'".$txt_country_id[$m]."','".$hidden_color[$i]."','".$txt_size_id[$m]."','".$color_size_bkdown_id."','".$txt_bundle_number[$m]."','".$hidden_barcode_no_all[$m]."','".$txt_rmg_start[$m]."','".$txt_rmg_end[$m]."','".trim($txt_size_qty[$m])."','".$txt_reject_qty[$m]."','".$txt_replace_qty[$m]."','".$txt_qcpass_qty[$m]."',".$user_id.",'".$pc_date_time."',1,0)";
							//$qc_dtls_id++;
							$qc_dtls_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_dtls_seq",  "pro_gmts_cutting_qc_dtls", $con );
					   }

					   if($m!=0) $data_array_dtls.=",";
					   if($i!=0 && $m==0) $data_array_dtls.=",";

					   $data_array_dtls .= "(".$dtls_id.",'".$product_mst_arr[$txt_order_id[$i]][$txt_gmt_id[$i]][$txt_country_id[$m]]."',1,'".$color_size_bkdown_id."','".$txt_qcpass_qty[$m]."','".$txt_cutting_no."','".$txt_bundle_number[$m]."','".$hidden_barcode_no_all[$m]."','".$txt_reject_qty[$m]."','".$txt_replace_qty[$m]."','".$update_id."','".$bundle_wise_type_array[$txt_bundle_number[$m]]."')";
					   //$dtls_id=$dtls_id+1;
					   $dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );

					  $qc_dtls_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_dtls_seq",  "pro_gmts_cutting_qc_dtls", $con );

					   $product_mst_qc[$txt_order_id[$i]][$c_id]+=$txt_qcpass_qty[$m]*1;
					   $product_mst_re[$txt_order_id[$i]][$c_id]+=$txt_replace_qty[$m]*1;
					   $country_qcpass_qty[$txt_country_id[$m]]+=$txt_qcpass_qty[$m]*1;
					   $country_reject_qty[$txt_country_id[$m]]+=$txt_reject_qty[$m]*1;
					   $country_replace_qty[$txt_country_id[$m]]+=$txt_replace_qty[$m]*1;
					}
					else // if qnty blank
					{
						if(str_replace("'",'',$update_detail_id[$m])!="")
					   {
						   $update_detail_arr[]=str_replace("'",'',$update_detail_id[$m]);
						   $update_detail=str_replace("'",'',$update_detail_id[$m]);
						   $data_array_qc_detls[$update_detail] =explode(",",("'".$txt_reject_qty[$m]."','".$txt_replace_qty[$m]."','".$txt_qcpass_qty[$m]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0"));
					   }
					}
				}
				else  // on validation
				{
					$txt_qcpass_qty[$m]=$old_prod_data[$txt_bundle_number[$m]]['production_qnty'];
					$txt_reject_qty[$m]=$old_prod_data[$txt_bundle_number[$m]]['reject_qty'];
					$txt_replace_qty[$m]=$old_prod_data[$txt_bundle_number[$m]]['replace_qty'];
					//$txt_qcpass_qty[$m]=$old_prod_data[$txt_bundle_number[$m]]['alter_qty'];
					//$txt_qcpass_qty[$m]=$old_prod_data[$txt_bundle_number[$m]]['spot_qty'];

					$color_size_bkdown_id=$color_beckdown_arr[$txt_order_id[$i]][$txt_country_id[$m]][$txt_gmt_id[$i]][$hidden_color[$i]][$txt_size_id[$m]];
					   $bundle_no_creation=1;
					   if(str_replace("'","",$txt_reject_qty[$m])=="") $txt_reject_qty[$m]=0;
					   if(str_replace("'","",$txt_replace_qty[$m])=="") $txt_replace_qty[$m]=0;

					   if(str_replace("'",'',$update_detail_id[$m])!="")
					   {
						   $update_detail_arr[]=str_replace("'",'',$update_detail_id[$m]);
						   $update_detail=str_replace("'",'',$update_detail_id[$m]);
						   $data_array_qc_detls[$update_detail] =explode(",",("'".$txt_reject_qty[$m]."','".$txt_replace_qty[$m]."','".$txt_qcpass_qty[$m]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1"));
					   }
					   else
					   {
							if($data_array_qc_save!="") $data_array_qc_save.=",";
							$data_array_qc_save.="(".$qc_dtls_id.",".$update_id.",".$txt_order_id[$i].",".$txt_gmt_id[$i].",'".$txt_country_id[$m]."','".$hidden_color[$i]."','".$txt_size_id[$m]."','".$color_size_bkdown_id."','".$txt_bundle_number[$m]."','".$hidden_barcode_no_all[$m]."','".$txt_rmg_start[$m]."','".$txt_rmg_end[$m]."','".trim($txt_size_qty[$m])."','".$txt_reject_qty[$m]."','".$txt_replace_qty[$m]."','".$txt_qcpass_qty[$m]."',".$user_id.",'".$pc_date_time."',1,0)";
							$qc_dtls_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_dtls_seq",   "pro_gmts_cutting_qc_dtls", $con );
					   }

					   if($m!=0) $data_array_dtls.=",";
					   if($i!=0 && $m==0) $data_array_dtls.=",";

					   $data_array_dtls .= "(".$dtls_id.",'".$product_mst_arr[$txt_order_id[$i]][$txt_gmt_id[$i]][$txt_country_id[$m]]."',1,'".$color_size_bkdown_id."','".$txt_qcpass_qty[$m]."','".$txt_cutting_no."','".$txt_bundle_number[$m]."','".$hidden_barcode_no_all[$m]."','".$txt_reject_qty[$m]."','".$txt_replace_qty[$m]."','".$update_id."','".$bundle_wise_type_array[$txt_bundle_number[$m]]."')";
					   //$dtls_id=$dtls_id+1;
					   $dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );

					  $qc_dtls_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_dtls_seq",   "pro_gmts_cutting_qc_dtls", $con );

					   $product_mst_qc[$txt_order_id[$i]][$c_id]+=$txt_qcpass_qty[$m]*1;
					   $product_mst_re[$txt_order_id[$i]][$c_id]+=$txt_replace_qty[$m]*1;
					   $country_qcpass_qty[$txt_country_id[$m]]+=$txt_qcpass_qty[$m]*1;
					   $country_reject_qty[$txt_country_id[$m]]+=$txt_reject_qty[$m]*1;
					   $country_replace_qty[$txt_country_id[$m]]+=$txt_replace_qty[$m]*1;
				}
		 	 }

			 foreach($country_qcpass_qty as $c_id=>$c_qty)
			 {
				$currency_id=$order_wise_rate_data_arr[$txt_order_id[$i]]["currency"];
				$exchange_rate=$order_wise_rate_data_arr[$txt_order_id[$i]]["exchange_rate"];
				$rate=str_replace("'","",$order_wise_rate_data_arr[$txt_order_id[$i]]["rate"]);
				if($rate!="") {$amount=$rate*str_replace("'","",trim($country_qcpass_qty[$c_id]))*1;}
				else {$amount="";}
				if($country_reject_qty[$c_id]==""){$country_reject_qty[$c_id]=0;}
				if($country_replace_qty[$c_id]==""){$country_replace_qty[$c_id]=0;}
				$update_id_mst_arr[]=str_replace("'",'',$product_mst_arr[$txt_order_id[$i]][$txt_gmt_id[$i]][$c_id]);
				$data_array_prod[str_replace("'",'',$product_mst_arr[$txt_order_id[$i]][$txt_gmt_id[$i]][$c_id])] =explode("*",("'".$cbo_source."'*'".$cbo_cutting_company."'*'".$cbo_location_name."'*'".$cbo_floor_name."'*'".$txt_cutting_date."'*'".trim($country_qcpass_qty[$c_id])."'*".$txt_cutting_hour."*'".$country_reject_qty[$c_id]."'*'".$country_replace_qty[$c_id]."'*'".$cbo_work_order."'*'".$txt_wo_no."'*'".$cbo_shift_name."'*'".$currency_id."'*'".$exchange_rate."'*'".$rate."'*'".$amount."'*'".$user_id."'*'".$pc_date_time."'"));

			 }

			 unset($country_qcpass_qty);
			 unset($country_reject_qty);
			 unset($country_replace_qty);
		     unset($check_size_id);
			 $id=$id+1;
		 }

		$rejectDelete=execute_query("DELETE FROM pro_gmts_prod_dft WHERE mst_id in (".$txt_mst_id.") and defect_type_id=3 and production_type=1");
	    $dtlsrDelete = execute_query("delete from pro_garments_production_dtls where mst_id in (".$txt_mst_id.")",0);
		//$dtlsrDelete=1;
		//$rejectDelete=1;
		//echo "10**".bulk_update_sql_statement("pro_garments_production_mst","id",$field_array1,$data_array_prod,$update_id_mst_arr);die;
		$query=execute_query( bulk_update_sql_statement("pro_garments_production_mst","id",$field_array1,$data_array_prod,$update_id_mst_arr),1);
 		//$query=1;
 		if(count($update_detail_arr)>999)
 		{
 			$chunk_arr = array_chunk($update_detail_arr, 999);
 			foreach ($chunk_arr as $key => $id_arr) 
 			{
 				// echo "<pre>";;print_r($id_arr);die;
 				$rID_dtls_qc=execute_query( bulk_update_sql_statement("pro_gmts_cutting_qc_dtls","id",$field_array_qc_dtls,$data_array_qc_detls,$id_arr),1);
 			}
 		}
 		else
 		{
			$rID_dtls_qc=execute_query( bulk_update_sql_statement("pro_gmts_cutting_qc_dtls","id",$field_array_qc_dtls,$data_array_qc_detls,$update_detail_arr),1);
		}
		
		$defectQ=1;
		if($data_array_defect!="")
		{
			$defectQ=sql_insert("pro_gmts_prod_dft",$field_array_defect,$data_array_defect,1);
		}

		$rID_dtlsqc=1;
		if($data_array_qc_save!="")
		{
			$rID_dtlsqc=sql_insert("pro_gmts_cutting_qc_dtls",$field_array_qc_save,$data_array_qc_save,1);
		}

		$rID_mst_qc=sql_update("pro_gmts_cutting_qc_mst",$field_array_qc_mst,$data_arra_cutt_mst,"id",$update_id,1);
    	$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);

    	// echo "10**insert into pro_garments_production_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;

		/*
		if( $chk==1 ) // if any change found restrict full update, need to check again here as full update should not be restricted
		{
			oci_rollback($con);
			echo "10**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_cutting_no)."**".implode(",",$mathced);
			disconnect($con);
			die;
		 }
		*/

		//3 lay
		//2 qc

		// echo "10**".$data_arra_cutt_mst;die;
		 // echo "10**".$dtlsrDelete."**".$query."**".$rID_dtls_qc."**".$rID_mst_qc."**".$dtlsrID."**".$rejectDelete."**".$rID_dtlsqc;die;
		//echo "10**".$field_array_dtls.'=='.$data_array_dtls;die;

		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($dtlsrDelete && $query && $rID_dtls_qc && $rID_mst_qc && $dtlsrID && $rejectDelete && $rID_dtlsqc)
			{
				mysql_query("COMMIT");
			    echo "1**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_cutting_no)."**".implode(",",$mathced);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_cutting_no)."**".implode(",",$mathced);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
		    if($dtlsrDelete && $query && $rID_dtls_qc && $rID_mst_qc && $dtlsrID && $rejectDelete  && $rID_dtlsqc)
		    {
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_cutting_no)."**".implode(",",$mathced);
			}
			else
		    {
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_cutting_no)."**".implode(",",$mathced);
		    }
		 }
		  //check_table_status( 160,0);
			disconnect($con);
			die;
	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		$con = connect();
	    if($db_type==0)	{ mysql_query("BEGIN"); }
	    $txt_order_id=explode("*",str_replace(" ","",$txt_order_id));
	    $txt_bundle_no=explode("___",str_replace(" ","",$txt_bundle_no));
	    $hidden_barcode_no_data=explode("__",$hidden_barcode_no);
	    $all_bundle_no="";
	    $all_barcode_no="";
	    for($i=0;$i<count($txt_order_id); $i++)
	  	{
	  		$txt_bundle_number=explode("*",$txt_bundle_no[$i]);
	  		$hidden_barcode_no_all=explode("*",$hidden_barcode_no_data[$i]);
	  		for($m=0;$m<count($txt_bundle_number); $m++)
		    {
		    	$bundle="'".trim(str_replace("'", "",$txt_bundle_number[$m]))."'";
		    	$barcode_no="'".trim(str_replace("'", "",$hidden_barcode_no_all[$m]))."'";
		    	$all_bundle_no.=($all_bundle_no=="")? $bundle : ','.$bundle;
		    	$all_barcode_no.=($all_barcode_no=="")? $barcode_no : ','.$barcode_no;
		    }
	  	}
	  	$all_bundle_arr=explode(",", $all_bundle_no);
	  	$barcode_arr=explode(",", $all_barcode_no);
	  	$bundle_count=count($all_bundle_arr);
	  	$bundle_nos_cond="";
	  	$barcode_nos_cond="";
		if($db_type==2 && $bundle_count>400)
		{
			$bundle_nos_cond=" and (";
			$barcode_nos_cond=" and (";
			$bundleArr=array_chunk($all_bundle_arr,399);
			$barcodeArr=array_chunk($barcode_arr,399);
			foreach($bundleArr as $bundleNos)
			{
				$bundleNos=implode(",",$bundleNos);
				$bundle_nos_cond.=" bundle_no in($bundleNos) or ";
 			}

			foreach($barcodeArr as $barcodeNos)
			{
				$barcodeNos=implode(",",$barcodeNos);
 				$barcode_nos_cond.=" bundle_no in($barcodeNos) or ";
			}


			$bundle_nos_cond=chop($bundle_nos_cond,'or ');
			$barcode_nos_cond=chop($bundle_nos_cond,'or ');
			$bundle_nos_cond.=")";
			$barcode_nos_cond.=")";
		}
		else
		{
			$bundle_nos_cond=" and bundle_no in ($all_bundle_no)";
			$barcode_nos_cond=" and bundle_no in ($all_barcode_no)";
		}

	  	 //	echo '10**'.$barcode_nos_cond;die;
 		$sqls=sql_select("SELECT id from pro_garments_production_dtls where status_active=1 and is_deleted=0 and production_type not in(1)  $bundle_nos_cond");
		if(count($sqls)>0)
		{
			echo "444**444";disconnect($con);die;
		}
		else
		{
 			$user=$_SESSION['logic_erp']['user_id'];
			$cutt_mst=execute_query("UPDATE pro_gmts_cutting_qc_mst set status_active=0, is_deleted=1,updated_by='$user',update_date='$pc_date_time' where id=$update_id" );
			$cutt_dtls=execute_query("UPDATE pro_gmts_cutting_qc_dtls set status_active=0, is_deleted=1,updated_by='$user',update_date='$pc_date_time' where mst_id=$update_id " );
			$pro_mst=execute_query("UPDATE pro_garments_production_mst set status_active=0, is_deleted=1,updated_by='$user',update_date='$pc_date_time' where delivery_mst_id=$update_id and production_type=1" );
			$pro_dtls=execute_query("UPDATE pro_garments_production_dtls set status_active=0, is_deleted=1 where delivery_mst_id=$update_id and production_type=1" );
			$pro_dft=execute_query("UPDATE pro_gmts_prod_dft set status_active=0, is_deleted=1,updated_by='$user',update_date='$pc_date_time' where status_active=1  $barcode_nos_cond " );
 			if($db_type==0)
			{
				if($cutt_mst && $cutt_dtls && $pro_mst && $pro_dtls)
				{
					mysql_query("COMMIT");
				    echo "2**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_cutting_no);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_cutting_no);
				}
			}

			if($db_type==2 || $db_type==1 )
			{
			    if($cutt_mst && $cutt_dtls && $pro_mst && $pro_dtls)
			    {
					oci_commit($con);
					echo "2**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_cutting_no);
				}
				else
			    {
					oci_rollback($con);
					echo "10**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_cutting_no);
			    }
	  	    }
 			disconnect($con);
			die;


		}

	}
}


if($action=="cutting_number_popup")
{
  	echo load_html_head_contents("Batch Info","../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_cutting_value(strCon )
		{

		document.getElementById('update_mst_id').value=strCon;
		parent.emailwindow.hide();
		}

    </script>
	</head>
	<body>
	<div align="center" style="width:100%; overflow-y:hidden;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="980" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
                <tr>
                    <th width="130">Company name</th>
                    <th width="130">Working Company</th>
                    <th width="80">Cutting No</th>
                    <th width="80">Job No</th>
                    <th width="80">Style</th>
                    <th width="80">Order No</th>
                    <th width="80">Internal Ref</th>
                    <th width="250">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
                </tr>
            </thead>
            <tbody>
                  <tr class="general">
                        <td>
                              <?

                                   echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --",$company_id, "");
                             ?>
                        </td>

                        <td>
                              <?

                                   echo create_drop_down( "cbo_wo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --",$company_id, "");
                             ?>
                        </td>

                        <td align="center" >
                                <input type="text" id="txt_cut_no" name="txt_cut_no" style="width:80px"  class="text_boxes"/>
                                <input type="hidden" id="update_mst_id" name="update_mst_id" />
                        </td>
                        <td align="center">
                               <input name="txt_job_search" id="txt_job_search" class="text_boxes" style="width:80px"/>
                        </td>
                        <td align="center">
                               <input name="txt_style_search" id="txt_style_search" class="text_boxes" style="width:80px"  />
                        </td>
                        <td align="center">
                               <input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"  />
                        </td>
						<td align="center"><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px">
					</td>

                        <td align="center" width="250">
                               <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" />
                               <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" />
                        </td>
					
                        <td align="center">
                               <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( 
								document.getElementById('cbo_company_name').value+'_'+
							   document.getElementById('txt_cut_no').value+'_'+
							   document.getElementById('txt_job_search').value+'_'+
							   document.getElementById('txt_date_from').value+'_'+
							   document.getElementById('txt_date_to').value+'_'+
							   document.getElementById('cbo_year_selection').value+'_'+
							   document.getElementById('txt_order_search').value+'_'+
							   document.getElementById('cbo_wo_company_name').value+'_'+
							   document.getElementById('txt_style_search').value+'_'+
							   document.getElementById('txt_internal_ref').value, 'create_cutting_search_list_view', 'search_div', 'cutting_entry_controller_urmi', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                        </td>
                 </tr>
        		 <tr>
                        <td align="center" valign="middle" colspan="8">
                            <? echo load_month_buttons(1);  ?>
                        </td>
                </tr>
            </tbody>
         </tr>
      </table>
     <div align="center" valign="top" id="search_div"> </div>
  	</form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_cutting_search_list_view")
{
    $ex_data = explode("_",$data);
	$company = $ex_data[0];
	$cutting_no = $ex_data[1];
	$job_no = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$cut_year= $ex_data[5];
	$order_no= $ex_data[6];
	$wo_company= $ex_data[7];
	$style_ref= $ex_data[8];
	$internal_ref= $ex_data[9];

	if($cutting_no =="" && $job_no =="" && $from_date =="" && $to_date=="" && $order_no=="" && $style_ref=="" && $internal_ref=="")
	{
		echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Please enter search value of any one field.</div>';die();
	}
	
    if($db_type==2) { $year_cond=" and extract(year from a.insert_date)=$cut_year"; $year=" extract(year from a.insert_date)";}
    if($db_type==0) {$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year"; $year=" SUBSTRING_INDEX(a.insert_date, '-', 1) ";}
	if(str_replace("'","",$company)==0) $conpany_cond=""; else $conpany_cond="and a.company_id=".str_replace("'","",$company)."";
	if(str_replace("'","",$wo_company)==0) $wo_company_cond=""; else $wo_company_cond="and a.working_company_id=".str_replace("'","",$wo_company)."";
	if(str_replace("'","",$cutting_no)=="") $cut_cond=""; else $cut_cond="and a.cut_num_prefix_no='".str_replace("'","",$cutting_no)."'  $year_cond";
	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and b.job_no_prefix_num='".str_replace("'","",$job_no)."'";
	if(str_replace("'","",$style_ref)=="") $style_ref_cond=""; else $jstyle_ref_cond="and b.style_ref_no='".str_replace("'","",$style_ref)."'";

	if(str_replace("'","",$order_no)=="") $order_cond=""; else $order_cond="and c.po_number like '%".trim($order_no)."%' ";
	if(str_replace("'","",$internal_ref)=="") $internal_ref_cond=""; else $internal_ref_cond="and c.grouping like '%".trim($internal_ref)."%' ";

	

	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==0)
		{
			$sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		}
		else if($db_type==2)
		{
			$sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		}
	}
	//print_r($production_sql);	
	
	$sql_order="SELECT a.id, a.cut_num_prefix_no, a.cutting_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.marker_length, a.marker_width, a.fabric_width, c.po_number,c.grouping, e.order_id, d.color_id,d.order_cut_no, $year as year FROM ppl_cut_lay_mst a,wo_po_details_master b,wo_po_break_down c,ppl_cut_lay_dtls d ,ppl_cut_lay_bundle e where a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 and  e.status_active=1 and e.is_deleted=0 and 
	 a.id=d.mst_id and a.job_no=b.job_no and b.id=c.job_id  and d.mst_id=e.mst_id and c.id=e.order_id  $conpany_cond $wo_company_cond $cut_cond $job_cond $jstyle_ref_cond $sql_cond $order_cond $internal_ref_cond group by  a.id,a.cut_num_prefix_no,a.cutting_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.marker_length, a.marker_width, a.fabric_width,c.po_number,c.grouping,e.order_id,d.color_id,d.order_cut_no,$year order by a.entry_date desc";
	// echo $sql_order;
	$res = sql_select($sql_order);
	$po_id_array = array(); $color_id_arr = array(); 
	foreach ($res as $val) 
	{
		$po_id_array[$val['ORDER_ID']] = $val['ORDER_ID'];
		$color_id_arr[$val['COLOR_ID']] = $val['COLOR_ID'];
	}

	$po_id_cond = where_con_using_array($po_id_array,0,"a.po_break_down_id");
	$production_sql=return_library_array( "SELECT b.cut_no, b.mst_id from pro_garments_production_mst a,pro_garments_production_dtls b  where a.id=b.mst_id and  a.production_type=1 and a.status_active=1 and a.is_deleted=0  and b.production_type=1 and b.status_active=1 and b.is_deleted=0  and b.cut_no is not null $po_id_cond $conpany_cond group by b.cut_no ", "cut_no", "mst_id");

	
	$color_id_cond = where_con_using_array($color_id_arr,0,"id");
	$color_arr = return_library_array( "select id, color_name from lib_color where status_active=1 $color_id_cond",'id','color_name');
	$table_no_arr=return_library_array( "select id,table_no  from  lib_cutting_table",'id','table_no');
	?>
	 <div style="width:950px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="80">Cut No</th>
                <th width="80">Order Cut No</th>
                <th width="40">Year</th>
                <th width="60">Table No</th>
                <th width="100">Job No</th>
                <th width="100">Order NO</th>
				<th width="80">Internal Ref</th>
                <th width="60">Color</th>
                <th width="80">Marker Length</th>
                <th width="80">Markar Width</th>
                <th width="80">Fabric Width</th>
                <th>Entry Date</th>
            </thead>
     	</table>
     </div>
     <div style="width:950px; max-height:270px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="932" class="rpt_table" id="list_view">
			<?
			$i=1;
            foreach( $res as $row )
            {
				if($production_sql[$row[csf('cutting_no')]]=="")
				{
					if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_cutting_value('<?=$row[csf("cutting_no")]; ?>');" >
						<td width="30" align="center"><?=$i; ?></td>
						<td width="80" align="center" style="word-break:break-all"><?=$row[csf("cutting_no")]; ?></td>
						<td width="80" align="center" style="word-break:break-all"><?=$row[csf("order_cut_no")]; ?></td>
						<td width="40" style="word-break:break-all"><?=$row[csf("year")]; ?></td>
						<td width="60" style="word-break:break-all"><?=$table_no_arr[$row[csf("table_no")]]; ?></td>
						<td width="100" style="word-break:break-all"><?=$row[csf("job_no")]; ?></td>
						<td width="100" style="word-break:break-all"><?=$row[csf("po_number")]; ?></td>
						<td width="80" style="word-break:break-all"><?=$row[csf("grouping")]; ?></td>
						<td width="60" style="word-break:break-all"><?=$color_arr[$row[csf("color_id")]]; ?>&nbsp;</td>
						<td width="80" align="right"><?=$row[csf("marker_length")]; ?>&nbsp;</td>
						<td width="80" align="right"><?=$row[csf("marker_width")]; ?></td>
					   	<td width="80" align="right"><?=$row[csf("fabric_width")]; ?></td>
						<td><?=change_date_format($row[csf("entry_date")]);?> </td>
					</tr>
					<?
					$i++;
				}
            }
   		?>
        </table>
    </div>
	<?
	exit();
}

if($action=="create_cutting_search_list_view_bk")
{

    $ex_data = explode("_",$data);
	$company = $ex_data[0];
	$cutting_no = $ex_data[1];
	$job_no = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$cut_year= $ex_data[5];
	$order_no= $ex_data[6];

	if($cutting_no =="" && $job_no =="" && $from_date =="" && $to_date=="" && $order_no=="")
	{
		echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Please enter search value of any one field.</div>';die();
	}

    if($db_type==2) { $year_cond=" and extract(year from a.insert_date)=$cut_year"; $year=" extract(year from a.insert_date)";}
    if($db_type==0) {$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year"; $year=" SUBSTRING_INDEX(a.insert_date, '-', 1) ";}
	if(str_replace("'","",$company)==0) $conpany_cond=""; else $conpany_cond="and a.company_id=".str_replace("'","",$company)."";
	if(str_replace("'","",$cutting_no)=="") $cut_cond=""; else $cut_cond="and a.cut_num_prefix_no='".str_replace("'","",$cutting_no)."'  $year_cond";
	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and b.job_no_prefix_num='".str_replace("'","",$job_no)."'";
	if(str_replace("'","",$order_no)=="") $order_cond=""; else $order_cond="and c.po_number like '%".trim($order_no)."%' ";
	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==0)
	       {
			   $sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		   }
	  if($db_type==2)
	       {
			    $sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		   }
	}



	$production_sql=return_library_array( "SELECT b.cut_no from pro_garments_production_mst a,pro_garments_production_dtls b  where a.id=b.mst_id and  a.production_type=1 and a.status_active=1 and a.is_deleted=0  and b.production_type=1 and b.status_active=1 and b.is_deleted=0    $conpany_cond group by b.cut_no ", "cut_no", "cut_no"  );
	//print_r($production_sql);

	/*	 select a.id,a.cut_num_prefix_no,a.cutting_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.marker_length, a.marker_width, a.fabric_width,d.order_id,d.color_id, extract(year from a.insert_date) as year FROM ppl_cut_lay_mst a,wo_po_details_master b,wo_po_break_down c,ppl_cut_lay_bundle e,ppl_cut_lay_dtls d where a.id=d.mst_id and a.job_no=b.job_no and b.job_no=c.job_no_mst and d.mst_id=e.mst_id and c.id=e.order_id and a.entry_form=99 and a.company_id=3 and a.cut_num_prefix_no='2' and extract(year from a.insert_date)=2016 group by a.id,a.cut_num_prefix_no,a.cutting_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.marker_length, a.marker_width, a.fabric_width,d.order_id,d.color_id, extract(year from a.insert_date) order by id*/

	$sql_order="SELECT a.id,a.cut_num_prefix_no,a.cutting_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.marker_length, a.marker_width, a.fabric_width,e.order_id,d.color_id,$year as year FROM ppl_cut_lay_mst a,wo_po_details_master b,wo_po_break_down c,ppl_cut_lay_dtls d ,ppl_cut_lay_bundle e where a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 and  e.status_active=1 and e.is_deleted=0 and  a.id=d.mst_id and a.job_no=b.job_no and b.id=c.job_id  and d.mst_id=e.mst_id and c.id=e.order_id  $conpany_cond  $cut_cond $job_cond $sql_cond $order_cond group by  a.id,a.cut_num_prefix_no,a.cutting_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.marker_length, a.marker_width, a.fabric_width,e.order_id,d.color_id,$year order by id";
	// echo $sql_order;
	$ppl_mst_id_arr=array();
	foreach(sql_select($sql_order) as $vals)
	{
		$cutting=$vals[csf("cutting_no")];
		if($production_sql[$cutting])
		{
			$ppl_mst_id_arr[$vals[csf("id")]]=$vals[csf("id")];
		}

	}
	$new_sql_cond="";
	$ppl_mst_ids=implode(",", $ppl_mst_id_arr);
	if($ppl_mst_ids && count($ppl_mst_id_arr)>999 )
	{
		$chnk_arr=array_chunk($ppl_mst_id_arr, 999);
		foreach($chnk_arr as $v)
		{
			$ids=implode(",",$v);
			$new_sql_cond.=" and a.id not in($ids)";


		}

		 //$new_sql_cond= " and a.id not in($ppl_mst_ids) ";
	}
	else if($ppl_mst_ids && count($ppl_mst_id_arr)<999)
	{
		 $new_sql_cond= " and a.id not in($ppl_mst_ids) ";
	}


	 $sql_order="SELECT a.id,a.cut_num_prefix_no,a.cutting_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.marker_length, a.marker_width, a.fabric_width,e.order_id,d.color_id,$year as year FROM ppl_cut_lay_mst a,wo_po_details_master b,wo_po_break_down c,ppl_cut_lay_dtls d ,ppl_cut_lay_bundle e where a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 and  e.status_active=1 and e.is_deleted=0 and  a.id=d.mst_id and a.job_no=b.job_no and b.id=c.job_id  and d.mst_id=e.mst_id and c.id=e.order_id  $conpany_cond  $cut_cond $job_cond $sql_cond $order_cond $new_sql_cond  group by  a.id,a.cut_num_prefix_no,a.cutting_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.marker_length, a.marker_width, a.fabric_width,e.order_id,d.color_id,$year order by a.entry_date desc";


	//echo $sql_order;
	$table_no_arr=return_library_array( "select id,table_no  from  lib_cutting_table",'id','table_no');
	$order_number_arr=return_library_array( "select id, po_number from   wo_po_break_down",'id','po_number');
	$arr=array(2=>$table_no_arr,4=>$order_number_arr,5=>$color_arr);
	echo create_list_view("list_view", "Cut No,Year,Table No,Job No,Order NO,Color,Marker Length,Markar Width,Fabric Width,Entry Date","90,50,60,120,120,100,80,80,80,120","950","270",0, $sql_order , "js_set_cutting_value", "cutting_no", "", 1, "0,0,table_no,0,order_id,color_id,0,0,0,0,0", $arr, "cut_num_prefix_no,year,table_no,job_no,order_id,color_id,marker_length,marker_width,fabric_width,entry_date", "","setFilterGrid('list_view',-1)") ;

}


if($action=="system_number_popup")
{
  	echo load_html_head_contents("Batch Info","../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_system_value(strCon )
		{
		document.getElementById('update_mst_id').value=strCon;
		parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:100%; overflow-y:hidden;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="950" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>
                    <th width="130" class="must_entry_caption">Company name</th>
                    <th width="100">Cutting QC No</th>
                    <th width="100">Cutting No</th>
                    <th width="100">Job No</th>
                    <th width="100">Order No</th>
                    <th width="80">Internal Ref</th>
                    <th width="180">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
                </tr>
            </thead>
            <tbody>
                  <tr class="general">
                        <td>
                              <?

                                 echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --","", "");
                             ?>
                        </td>
                        <td align="center">
                               <input name="txt_cut_qc" id="txt_cut_qc" class="text_boxes" style="width:100px"  placeholder="Write"/>
                        </td>
                        <td align="center" >
                                <input type="text" id="txt_cut_no" name="txt_cut_no" style="width:100px"  class="text_boxes" placeholder="Write"/>
                                <input type="hidden" id="update_mst_id" name="update_mst_id" />
                        </td>
                        <td align="center">
                               <input name="txt_job_search" id="txt_job_search" class="text_boxes" style="width:100px"  placeholder="Write"/>
                        </td>

                        <td align="center">
                               <input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:100px" placeholder="Write" />
                        </td>

						<td align="center"><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:80px"></td>

                        <td align="center">
                               <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                               <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                        </td>
						
                        <td align="center">
                               <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_cut_qc').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_internal_ref').value, 'create_system_search_list_view', 'search_div', 'cutting_entry_controller_urmi', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                        </td>
						
                 </tr>
        		 <tr>
                        <td align="center" valign="middle" colspan="8">
                            <? echo load_month_buttons(1);  ?>
                        </td>
                </tr>
            </tbody>
         </tr>
      </table>
     <div align="center" valign="top" id="search_div"> </div>
  </form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_system_search_list_view")
{
    $ex_data = explode("_",$data);
	$company = $ex_data[0];
	$cutting_no = $ex_data[1];
	$job_no = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$cut_year= $ex_data[5];
	$system_no= $ex_data[6];
	$order_no= $ex_data[7];
	$internal_ref= $ex_data[8];
	//echo "<pre>";
	//print_r($internal_ref);
	if(str_replace("'","",$company)==0) { echo "Please select company First"; die;}
    if($db_type==2){ $year_cond=" and extract(year from a.insert_date)=$cut_year"; $year=" extract(year from a.insert_date) as year";}
    if($db_type==0) { $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year"; $year=" SUBSTRING_INDEX(a.insert_date, '-', 1) as year";}
	if(str_replace("'","",$company)==0) $conpany_cond=""; else $conpany_cond="and a.company_id=".str_replace("'","",$company)."";
	if(str_replace("'","",$cutting_no)=="") $cut_cond=""; else $cut_cond="and b.cut_num_prefix_no='".str_replace("'","",$cutting_no)."'  ";
	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and c.job_no_prefix_num='".str_replace("'","",$job_no)."'";

	if(str_replace("'","",$system_no)=="") $system_cond=""; else $system_cond="and a.cut_qc_prefix_no=".trim($system_no)." $year_cond";

	if(str_replace("'","",$order_no)=="") $order_cond=""; else $order_cond=" and d.po_number='".str_replace("'","",$order_no)."'";
	
	if(str_replace("'","",$internal_ref)=="") $internal_ref_cond=""; else $internal_ref_cond=" and d.grouping like'%".str_replace("'","",$internal_ref)."%'";

	//if(str_replace("'","",$order_no)=="") $order_cond=""; else $order_cond="and c.po_number like '%".trim($order_no)."%' "; for example

	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==0)
		{
			$sql_cond= " and a.cutting_qc_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		}
		if($db_type==2)
		{
			$sql_cond= " and a.cutting_qc_date  between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		}
	}

	$sql_order="SELECT a.id,a.cutting_no,a.cut_qc_prefix_no,a.cutting_qc_no, a.table_no, a.job_no, a.batch_id, a.cutting_qc_date, a.marker_length, a.marker_width, a.fabric_width,c.job_no_prefix_num,b.cut_num_prefix_no,$year, d.po_number,d.grouping
    FROM pro_gmts_cutting_qc_mst a, ppl_cut_lay_mst b,ppl_cut_lay_dtls e,wo_po_details_master c,wo_po_break_down d
    where a.cutting_no=b.cutting_no and  b.job_no=c.job_no and c.id=d.job_id $conpany_cond  $cut_cond $job_cond $sql_cond $order_cond $system_cond $internal_ref_cond and a.status_active=1 and a.is_deleted=0 and b.id=e.mst_id   order by a.id desc";
	//echo $sql_order;
    // and find_in_set(e.order_ids,d.id)>0
	//a.cutting_no=b.cutting_no and a.job_no=b.job_no and a.job_no=c.job_no and c.job_no=d.job_no_mst
	//echo $sql_order;die;

	$table_no_arr=return_library_array( "select id,table_no  from  lib_cutting_table",'id','table_no');
	//$order_library=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"); and a.job_no=c.job_no
	$arr=array(3=>$table_no_arr);
	echo create_list_view("list_view", "Cutting QC No,Year,Cut No,Table No,Job No,Internal Ref,Order No,Batch No,Marker Length,Markar Width,Fabric Width,Cutting QC Date","60,60,60,60,80,80,80,60,80,80,80","960","370",0, $sql_order , "js_set_system_value", "cutting_qc_no,cutting_no", "", 1, "0,0,0,0,table_no,0,0,0,0,0,0,0", $arr, "cut_qc_prefix_no,year,cut_num_prefix_no,table_no,job_no,grouping,po_number,batch_id,marker_length,marker_width,fabric_width,cutting_qc_date", "","setFilterGrid('list_view',-1)","0,0,0,0,0,0,0,0,0,0,3") ;
}

if($action=="load_system_mst_form")
{
    if($db_type==0) $cutting_hour=" TIME_FORMAT(a.cutting_qc_time, '%H:%i' ) as cutting_qc_time";
	if($db_type==2) $cutting_hour=" TO_CHAR(a.cutting_qc_time,'HH24:MI') as cutting_qc_time";
	$company_arr=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0 ",'id','company_name');
	$data=explode("__",$data);


	$sql_qc_mst=sql_select("SELECT a.id,a.cut_qc_prefix,a.cut_qc_prefix_no,a.remarks,a.cutting_qc_no,a.cutting_no,a.location_id,a.floor_id,a.table_no, a.job_no, a.batch_id, a.company_id,a.entry_date,a.start_time,a.end_date,a.end_time,a.marker_length,a.marker_width,a.fabric_width,a.gsm, a.width_dia, a.cutting_qc_date, $cutting_hour, a.table_no,a.location_id,a.floor_id,a.production_source,a.serving_company,a.work_order_id,a.wo_order_no,a.workorder_rate_breakdown from pro_gmts_cutting_qc_mst a where a.cutting_qc_no='".$data[0]."' and a.status_active=1 and a.is_deleted=0");

	foreach($sql_qc_mst as $val)
	{
		if($data[1]!='on_save')
		{
			$working_company=return_field_value("working_company_id","ppl_cut_lay_mst "," cutting_no='".$val[csf("cutting_no")]."' and status_active=1 and is_deleted=0 ","working_company_id");
			$shift_name=return_field_value("shift_name","pro_garments_production_mst "," delivery_mst_id='".$val[csf('id')]."' and status_active=1 and is_deleted=0 and production_type=1","shift_name");

			echo "document.getElementById('cbo_source').value  = '".($val[csf("production_source")])."';\n";
			echo "load_drop_down( 'requires/cutting_entry_controller_urmi', '".$val[csf('production_source')]."', 'load_drop_down_cutt_company', 'cutt_company_td' );\n";
			echo "document.getElementById('cbo_cutting_company').value  = '".($val[csf("serving_company")])."';\n";
			echo "load_drop_down( 'requires/cutting_entry_controller_urmi', '".($val[csf("serving_company")])."', 'load_drop_down_location', 'location_td' );\n";

			echo "document.getElementById('cbo_company_name').value = '".($val[csf("company_id")])."';\n";
			echo "document.getElementById('txt_table_no').value = '".($val[csf("table_no")])."';\n";
			echo "document.getElementById('txt_lay_company').value = '".$company_arr[$working_company]."';\n";
			echo "document.getElementById('cbo_location_name').value  = '".($val[csf("location_id")])."';\n";
			echo "load_drop_down( 'requires/cutting_entry_controller_urmi', '".($val[csf("location_id")])."', 'load_drop_down_floor', 'floor_td' );\n";
			echo "document.getElementById('txt_cutting_date').value = '".change_date_format(($val[csf("cutting_qc_date")]))."';\n";
			echo "document.getElementById('txt_cutting_hour').value = '".($val[csf("cutting_qc_time")])."';\n";
			echo "document.getElementById('txt_remarks').value  = '".($val[csf("remarks")])."';\n";

			echo "document.getElementById('txt_cutting_no').value = '".($val[csf("cutting_no")])."';\n";
			echo "document.getElementById('cbo_floor_name').value  = '".($val[csf("floor_id")])."';\n";
			echo "document.getElementById('cbo_shift_name').value  = '".($shift_name)."';\n";
		}
		echo "document.getElementById('txt_system_no').value = '".($val[csf("cutting_qc_no")])."';\n";
		echo "document.getElementById('update_id').value = '".($val[csf("id")])."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_cut_lay_info',1);\n";
		//==========================================================================================================
		$start_time=explode(":",$val[csf("start_time")]);
		$end_time=explode(":",$val[csf("end_time")]);
		echo "$('#txt_entry_date').val('".change_date_format($val[csf("entry_date")])."');\n";
		echo "$('#txt_end_date').val('".change_date_format($val[csf("end_date")])."');\n";
		echo "document.getElementById('txt_marker_length').value  = '".($val[csf("marker_length")])."';\n";
		echo "document.getElementById('txt_marker_width').value  = '".($val[csf("marker_width")])."';\n";
		echo "document.getElementById('txt_fabric_width').value = '".($val[csf("fabric_width")])."';\n";
		echo "document.getElementById('txt_gsm').value  = '".($val[csf("gsm")])."';\n";
		echo "document.getElementById('cbo_width_dia').value  = '".($val[csf("width_dia")])."';\n";
		echo "document.getElementById('txt_batch_no').value = '".($val[csf("batch_id")])."';\n";
		echo "document.getElementById('txt_job_no').value = '".($val[csf("job_no")])."';\n";
		echo "document.getElementById('txt_cut_prifix').value  = '".($val[csf("cut_num_prefix_no")])."';\n";
		echo "document.getElementById('txt_in_time_hours').value  = '".($start_time[0])."';\n";
		echo "document.getElementById('txt_in_time_minuties').value = '".($start_time[1])."';\n";
		echo "document.getElementById('txt_out_time_hours').value = '".($end_time[0])."';\n";
		echo "document.getElementById('txt_out_time_minuties').value  = '".($end_time[1])."';\n";

		if($db_type==0){ $insert_year="SUBSTRING_INDEX(b.insert_date, '-', 1) as year";}
		if($db_type==2){ $insert_year="extract(year from b.insert_date) as year";}

		$sql=sql_select("select distinct c.id,c.buyer_name,$insert_year from  wo_po_break_down a,wo_po_details_master b,lib_buyer c where  a.job_no_mst=b.job_no and b.job_no='".$val[csf("job_no")]."' and b.buyer_name=c.id and a.status_active =1 ");

		foreach($sql as $row)
		{
			echo "document.getElementById('txt_buyer_name').value = '".$row[csf("id")]."';\n";
			echo "document.getElementById('txt_job_year').value = '".$row[csf("year")]."';\n";
		}

		//===========================================================================================================

		if($db_type==0)
		{
			$allorder_id=return_field_value("group_concat(distinct(a.order_id)) as order_id","ppl_cut_lay_dtls a,ppl_cut_lay_mst b","  b.id=a.mst_id and b.cutting_no='".$val[csf("cutting_no")]."' and status_active=1 and is_deleted=0 ","order_id");
		}
		else
		{
			$allorder_id=return_field_value("listagg(a.order_id,',') within group (order by a.order_id) as order_id","ppl_cut_lay_dtls a,ppl_cut_lay_mst b"," b.id=a.mst_id and b.cutting_no='".$val[csf("cutting_no")]."' and b.status_active=1 and b.is_deleted=0 ","order_id");
		}
		echo "document.getElementById('all_order_id').value  = '".$allorder_id."';\n";
		echo "load_drop_down( 'requires/cutting_entry_controller_urmi', '".$val[csf('company_id')]."_".$val[csf("serving_company")]."_".$allorder_id."', 'load_drop_down_workorder', 'workorder_td' );\n";
		echo "document.getElementById('txt_wo_no').value  = '".($val[csf("wo_order_no")])."';\n";


		echo "document.getElementById('cbo_work_order').value  = '".($val[csf("work_order_id")])."';\n";
		echo "document.getElementById('piece_rate_data_string').value  = '".($val[csf("workorder_rate_breakdown")])."';\n";

	}
}

if($action=="load_php_mst_form")
{

	$company_arr=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0 ",'id','company_name');
    if($db_type==0) $cutting_hour=" TIME_FORMAT(a.cutting_qc_time, '%H:%i' ) as cutting_qc_time";
	if($db_type==2) $cutting_hour=" TO_CHAR(a.cutting_qc_time,'HH24:MI') as cutting_qc_time";

   	//$sql_qc_mst=sql_select("select a.id,a.cut_qc_prefix,a.cut_qc_prefix_no,a.cutting_qc_no,a.cutting_no,a.location_id, a.floor_id,a.table_no,a.company_id, a.production_source,a.serving_company, a.cutting_qc_date, $cutting_hour,a.work_order_id,a.workorder_rate_breakdown from pro_gmts_cutting_qc_mst a where a.cutting_no='".$data."' and a.status_active=1 and a.is_deleted=0");

	$sql_qc_mst=sql_select("SELECT a.id,a.cut_qc_prefix,a.cut_qc_prefix_no,a.cutting_qc_no,a.cutting_no,a.location_id, a.floor_id,a.table_no,a.job_no, a.batch_id,a.company_id, a.entry_date, a.start_time,a.end_date,a.end_time ,a.marker_length,a.marker_width,a.fabric_width,a.gsm, a.width_dia,a.table_no, a.production_source,a.serving_company, a.cutting_qc_date, $cutting_hour, a.location_id, a.floor_id,a.work_order_id,a.workorder_rate_breakdown from pro_gmts_cutting_qc_mst a where a.cutting_no='".$data."' and a.status_active=1 and a.is_deleted=0");

	if(count($sql_qc_mst)>0)
	{
		$working_company=return_field_value("working_company_id","ppl_cut_lay_mst "," cutting_no='".$data."' and status_active=1 and is_deleted=0 ","working_company_id");
		//echo $working_company;die;
		foreach($sql_qc_mst as $val)
		{

			// $shift_name=return_field_value("shift_name","pro_garments_production_mst "," delivery_mst_id='".$val[csf('id')]."' and status_active=1 and is_deleted=0 and production_type=1","shift_name");

			echo "document.getElementById('cbo_source').value  = '".($val[csf("production_source")])."';\n";
			echo "load_drop_down( 'requires/cutting_entry_controller_urmi', '".$val[csf('production_source')]."', 'load_drop_down_cutt_company', 'cutt_company_td' );\n";
			echo "document.getElementById('cbo_cutting_company').value  = '".($val[csf("serving_company")])."';\n";
			echo "load_drop_down( 'requires/cutting_entry_controller_urmi', '".($val[csf("serving_company")])."', 'load_drop_down_location', 'location_td' );\n";

			echo "document.getElementById('txt_system_no').value = '".($val[csf("cutting_qc_no")])."';\n";
			echo "document.getElementById('cbo_company_name').value = '".($val[csf("company_id")])."';\n";
			echo "document.getElementById('txt_table_no').value = '".($val[csf("table_no")])."';\n";
			echo "document.getElementById('txt_lay_company').value = '".$company_arr[$working_company]."';\n";
			echo "document.getElementById('cbo_location_name').value  = '".($val[csf("location_id")])."';\n";
			echo "load_drop_down( 'requires/cutting_entry_controller_urmi', '".($val[csf("location_id")])."', 'load_drop_down_floor', 'floor_td' );\n";
			echo "document.getElementById('txt_cutting_date').value = '".change_date_format(($val[csf("cutting_qc_date")]))."';\n";
			echo "document.getElementById('txt_cutting_hour').value = '".($val[csf("cutting_qc_time")])."';\n";
			//echo "document.getElementById('txt_cut_prifix').value  = '".($val[csf("cut_num_prefix_no")])."';\n";
			echo "document.getElementById('update_id').value = '".($val[csf("id")])."';\n";
			echo "document.getElementById('txt_cutting_no').value = '".($val[csf("cutting_no")])."';\n";
			echo "document.getElementById('cbo_floor_name').value  = '".($val[csf("floor_id")])."';\n";
			// echo "document.getElementById('cbo_shift_name').value  = '".($shift_name)."';\n";
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_cut_lay_info',1);\n";

			//extra data
			$start_time=explode(":",$val[csf("start_time")]);
			$end_time=explode(":",$val[csf("end_time")]);
			echo "$('#txt_entry_date').val('".change_date_format($val[csf("entry_date")])."');\n";
			echo "$('#txt_end_date').val('".change_date_format($val[csf("end_date")])."');\n";
			echo "document.getElementById('txt_marker_length').value  = '".($val[csf("marker_length")])."';\n";
			echo "document.getElementById('txt_marker_width').value  = '".($val[csf("marker_width")])."';\n";
			echo "document.getElementById('txt_fabric_width').value = '".($val[csf("fabric_width")])."';\n";
			echo "document.getElementById('txt_gsm').value  = '".($val[csf("gsm")])."';\n";
			echo "document.getElementById('cbo_width_dia').value  = '".($val[csf("width_dia")])."';\n";
			echo "document.getElementById('txt_batch_no').value = '".($val[csf("batch_id")])."';\n";
			echo "document.getElementById('txt_job_no').value = '".($val[csf("job_no")])."';\n";
			echo "document.getElementById('txt_cut_prifix').value  = '".($val[csf("cut_num_prefix_no")])."';\n";
			echo "document.getElementById('txt_in_time_hours').value  = '".($start_time[0])."';\n";
			echo "document.getElementById('txt_in_time_minuties').value = '".($start_time[1])."';\n";
			echo "document.getElementById('txt_out_time_hours').value = '".($end_time[0])."';\n";
			echo "document.getElementById('txt_out_time_minuties').value  = '".($end_time[1])."';\n";

			if($db_type==0){ $insert_year="SUBSTRING_INDEX(b.insert_date, '-', 1) as year";}
			if($db_type==2){ $insert_year="extract(year from b.insert_date) as year";}

			$sql=sql_select("select distinct c.id,c.buyer_name,$insert_year from  wo_po_break_down a,wo_po_details_master b,lib_buyer c where  a.job_no_mst=b.job_no and b.job_no='".$val[csf("job_no")]."' and b.buyer_name=c.id and a.status_active =1 ");

			foreach($sql as $row)
		   	{
				echo "document.getElementById('txt_buyer_name').value = '".$row[csf("id")]."';\n";
				echo "document.getElementById('txt_job_year').value = '".$row[csf("year")]."';\n";
		   	}
			// end extra data

			if($db_type==0)
			{
				$allorder_id=return_field_value("group_concat(distinct(a.order_id)) as order_id","ppl_cut_lay_dtls a,ppl_cut_lay_mst b","  b.id=a.mst_id and b.cutting_no='".$val[csf("cutting_no")]."' and status_active=1 and is_deleted=0 ","order_id");
			}
			else
			{
				$allorder_id=return_field_value("listagg(a.order_id,',') within group (order by a.order_id) as order_id","ppl_cut_lay_dtls a,ppl_cut_lay_mst b"," b.id=a.mst_id and b.cutting_no='".$val[csf("cutting_no")]."' and b.status_active=1 and b.is_deleted=0 ","order_id");
			}
			echo "document.getElementById('all_order_id').value  = '".$allorder_id."';\n";

			echo "load_drop_down( 'requires/cutting_entry_controller_urmi', '".$val[csf('company_id')]."_".$val[csf("serving_company")]."_".$allorder_id."', 'load_drop_down_workorder', 'workorder_td' );\n";

			echo "document.getElementById('cbo_work_order').value  = '".($val[csf("work_order_id")])."';\n";
			echo "document.getElementById('piece_rate_data_string').value  = '".($val[csf("workorder_rate_breakdown")])."';\n";
		}
	}
	else
	{
		$sql_data=sql_select("SELECT b.id as tbl_id,b.table_no,b.location_id,b.floor_id,a.id,a.job_no,a.company_id,a.entry_date, end_date, a.marker_length, a.marker_width,a.fabric_width,a.gsm,a.width_dia,a.cutting_no, a.batch_id,a.start_time,a.end_time, a.cut_num_prefix_no,a.working_company_id,a.entry_form from  ppl_cut_lay_mst a left join lib_cutting_table b on  a.table_no=b.id where   a.cutting_no='".$data."' and a.status_active=1 and a.is_deleted=0  ");

		foreach($sql_data as $val)
		{

			echo "document.getElementById('cbo_company_name').value = '".($val[csf("company_id")])."';\n";
			echo "document.getElementById('cbo_source').value = '1';\n";
			if( $val[csf("entry_form")]==77)
			{
				echo "load_drop_down( 'requires/cutting_entry_controller_urmi', '1**'+$('#company_id').val(), 'load_drop_down_cutt_company', 'cutt_company_td' );";
				echo "document.getElementById('cbo_cutting_company').value = '".($val[csf("company_id")])."';\n";
				echo "load_drop_down( 'requires/cutting_entry_controller_urmi', '".($val[csf("company_id")])."', 'load_drop_down_location', 'location_td' );\n";
				echo "document.getElementById('txt_lay_company').value = '".$company_arr[$val[csf("company_id")]]."';\n";
			}
			else
			{
				echo "load_drop_down( 'requires/cutting_entry_controller_urmi', '1**'+$('#working_company_id').val(), 'load_drop_down_cutt_company', 'cutt_company_td' );";
				echo "document.getElementById('cbo_cutting_company').value = '".($val[csf("working_company_id")])."';\n";
				echo "load_drop_down( 'requires/cutting_entry_controller_urmi', '".($val[csf("working_company_id")])."', 'load_drop_down_location', 'location_td' );\n";
				echo "document.getElementById('txt_lay_company').value = '".$company_arr[$val[csf("working_company_id")]]."';\n";
			}




			echo "document.getElementById('txt_table_no').value = '".($val[csf("table_no")])."';\n";
			echo "document.getElementById('cbo_location_name').value  = '".($val[csf("location_id")])."';\n";
			echo "load_drop_down( 'requires/cutting_entry_controller_urmi', '".($val[csf("location_id")])."', 'load_drop_down_floor', 'floor_td' );\n";
			//echo "document.getElementById('txt_cut_prifix').value  = '".($val[csf("cut_num_prefix_no")])."';\n";
			echo "document.getElementById('update_id').value = '".($val[csf("id")])."';\n";
			echo "document.getElementById('txt_cutting_no').value = '".($val[csf("cutting_no")])."';\n";
			echo "document.getElementById('cbo_floor_name').value  = '".($val[csf("floor_id")])."';\n";
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_cut_lay_info',1);\n";

			//============================================================================================================
			$start_time=explode(":",$val[csf("start_time")]);
			$end_time=explode(":",$val[csf("end_time")]);
			echo "$('#txt_entry_date').val('".change_date_format($val[csf("entry_date")])."');\n";
			echo "$('#txt_end_date').val('".change_date_format($val[csf("end_date")])."');\n";
			echo "document.getElementById('txt_marker_length').value  = '".($val[csf("marker_length")])."';\n";
			echo "document.getElementById('txt_marker_width').value  = '".($val[csf("marker_width")])."';\n";
			echo "document.getElementById('txt_fabric_width').value = '".($val[csf("fabric_width")])."';\n";
			echo "document.getElementById('txt_gsm').value  = '".($val[csf("gsm")])."';\n";
			echo "document.getElementById('cbo_width_dia').value  = '".($val[csf("width_dia")])."';\n";
			echo "document.getElementById('txt_batch_no').value = '".($val[csf("batch_id")])."';\n";
			echo "document.getElementById('txt_job_no').value = '".($val[csf("job_no")])."';\n";
			echo "document.getElementById('txt_cut_prifix').value  = '".($val[csf("cut_num_prefix_no")])."';\n";
			echo "document.getElementById('txt_in_time_hours').value  = '".($start_time[0])."';\n";
			echo "document.getElementById('txt_in_time_minuties').value = '".($start_time[1])."';\n";
			echo "document.getElementById('txt_out_time_hours').value = '".($end_time[0])."';\n";
			echo "document.getElementById('txt_out_time_minuties').value  = '".($end_time[1])."';\n";
			if($db_type==0){ $insert_year="SUBSTRING_INDEX(b.insert_date, '-', 1) as year";}
			if($db_type==2){ $insert_year="extract(year from b.insert_date) as year";}
			$sql=sql_select("select distinct c.id,c.buyer_name,$insert_year from  wo_po_break_down a,wo_po_details_master b,lib_buyer c where  a.job_no_mst=b.job_no and b.job_no='".$val[csf("job_no")]."' and b.buyer_name=c.id and a.status_active =1 ");

			foreach($sql as $row)
			{
				echo "document.getElementById('txt_buyer_name').value = '".$row[csf("id")]."';\n";
				echo "document.getElementById('txt_job_year').value = '".$row[csf("year")]."';\n";
			}
			//=============================================================================================================
			if($db_type==0)
			{
				$allorder_id=return_field_value("group_concat(distinct(order_id)) as order_id","ppl_cut_lay_dtls","mst_id=".$val[csf("id")]." and status_active=1 and is_deleted=0 ","order_id");
			}
			else
			{
				$allorder_id=return_field_value("listagg(order_id,',') within group (order by order_id) as order_id","ppl_cut_lay_dtls","mst_id=".$val[csf("id")]." and status_active=1 and is_deleted=0 ","order_id");
			}
			echo "document.getElementById('all_order_id').value  = '".$allorder_id."';\n";
			echo "load_drop_down( 'requires/cutting_entry_controller_urmi', '".$val[csf('company_id')]."_".$val[csf("working_company_id")]."_".$allorder_id."', 'load_drop_down_workorder', 'workorder_td' );\n";

			echo "document.getElementById('cbo_work_order').value  = '".($val[csf("work_order_id")])."';\n";
			echo "document.getElementById('piece_rate_data_string').value  = '".($val[csf("workorder_rate_breakdown")])."';\n";
		}
	}
}

if($action=="order_details_list")
{

	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$color_arr=return_library_array( "select id,color_name  from  lib_color", "id", "color_name"  );
	//echo " select a.id,a.mst_id,a.order_id,a.color_id,a.size_id,a.color_size_id,a.bundle_no,a.number_start,a.number_end,a.bundle_qty,a.reject_qty,a.replace_qty,a.qc_pass_qty from pro_gmts_cutting_qc_dtls a,pro_gmts_cutting_qc_mst b where a.mst_id=b.id and b.cutting_no='".$data."' and  b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0";
	$cutt_qc_dtls=sql_select(" SELECT a.id,a.mst_id,a.order_id,a.item_id,a.color_id,a.size_id,a.color_size_id,a.bundle_no,a.number_start,a.number_end,a.bundle_qty,a.reject_qty,a.replace_qty,a.qc_pass_qty from pro_gmts_cutting_qc_dtls a,pro_gmts_cutting_qc_mst b where a.mst_id=b.id and b.cutting_no='".$data."' and  b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");
	$qc_details_arr=array();
	$total_qty=array();
	$order_color_qty=array();
	foreach($cutt_qc_dtls as $inf)
	{
		$qc_details_arr[$inf[csf("order_id")]][$inf[csf("color_id")]][$inf[csf("bundle_no")]]['qc_pass_qty']=$inf[csf("qc_pass_qty")];
		$qc_details_arr[$inf[csf("order_id")]][$inf[csf("color_id")]][$inf[csf("bundle_no")]]['update_id']=$inf[csf("id")];
		$qc_details_arr[$inf[csf("order_id")]][$inf[csf("color_id")]][$inf[csf("bundle_no")]]['reject_qty']=$inf[csf("reject_qty")];
		$qc_details_arr[$inf[csf("order_id")]][$inf[csf("color_id")]][$inf[csf("bundle_no")]]['replace_qty']=$inf[csf("replace_qty")];

		$order_color_qty[$inf[csf("order_id")]][$inf[csf("item_id")]][$inf[csf("color_id")]]['qc_pass_qty']+=$inf[csf("qc_pass_qty")];
		$order_color_qty[$inf[csf("order_id")]][$inf[csf("item_id")]][$inf[csf("color_id")]]['reject_qty']+=$inf[csf("reject_qty")];
		$order_color_qty[$inf[csf("order_id")]][$inf[csf("item_id")]][$inf[csf("color_id")]]['replace_qty']+=$inf[csf("replace_qty")];

		//echo $inf[csf("id")]."*".$inf[csf("qc_pass_qty")]."=";
		$total_qty['qc_pass_qty']+=$inf[csf("qc_pass_qty")];
		$total_qty['reject_qty']+=$inf[csf("reject_qty")];
		$total_qty['replace_qty']+=$inf[csf("replace_qty")];
	}
	//echo "<pre>";
	//print_r($total_qty);
	//	die;
	if(count($cutt_qc_dtls)!=0)
	{
		$j=1;
		$sql_bundle_reject=sql_select("select a.defect_type_id,a.production_type,a.defect_point_id,a.defect_qty,a.bundle_no from  pro_gmts_prod_dft a where a.production_type=1 and defect_type_id=3 and a.status_active=1 and a.is_deleted=0 order by id");
		$bundle_reject_data=array();
		foreach($sql_bundle_reject as $inf)
		{
			//if(in_array($inf[csf('bundle_no')],$check_arr))
			if( $check_arr[$inf[csf('bundle_no')]]!='')
			 {
				$bundle_reject_data[$inf[csf('bundle_no')]] .="**".$inf[csf('defect_point_id')]."*".$inf[csf('defect_qty')];
			 }
			 else
			 {
				$bundle_reject_data[$inf[csf('bundle_no')]]=$inf[csf('defect_point_id')]."*".$inf[csf('defect_qty')];
			 }
			 /*
			if( $check_arr[$inf[csf('bundle_no')]]!='' )
			{
				$bundle_reject_data[$inf[csf('bundle_no')]].="**".$inf[csf('defect_point_id')]."*".$inf[csf('defect_qty')];
			}
			else
			{
				$bundle_reject_data[$inf[csf('bundle_no')]]=$inf[csf('defect_point_id')]."*".$inf[csf('defect_qty')];
			}*/
			$check_arr[$inf[csf('bundle_no')]]=$inf[csf('bundle_no')];
		}

		$sql_dtls=sql_select("select b.id,c.order_id,a.ship_date,a.color_id,a.gmt_item_id,a.plies,a.marker_qty,a.order_qty, a.total_lay_qty, a.lay_balance_qty, b.job_no,b.job_year, b.company_id , a.id as details_id from ppl_cut_lay_dtls a, ppl_cut_lay_mst b,ppl_cut_lay_bundle c where b.id=a.mst_id and b.cutting_no='".$data."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.id=c.mst_id and a.id=c.dtls_id  group by b.id,c.order_id,a.ship_date, a.color_id, a.gmt_item_id, a.plies, a.marker_qty, a.order_qty, a.total_lay_qty, a.lay_balance_qty, b.job_no, b.job_year, b.company_id , a.id order by a.id,c.order_id desc");
		?>
         	<table cellpadding="0" cellspacing="0" width="550" class="" border="1" rules="all" id="" align="center">
            	<tr style=" font-size:larger;color:#093">
                	<td width="150" style="font-size:larger;">Erase Bundle Qty</td>
                    <td width="120" style="font-size:larger;">From:
                    <input type="text" id="txt_erage_from" name="txt_erage_from" style="width:70px;" class="text_boxes_numeric" />
                    </td>
                    <td width="120" style="font-size:larger;">To:
                    <input type="text" id="txt_erage_to" name="txt_erage_to" class="text_boxes_numeric"  style="width:70px;" />
                    </td>
                    <td width="100"><input type="button" value="OK" style="width:80px"  class="formbutton" onClick="fnc_erage_qty()"/></td>
                </tr>
                <tr style=" font-size:larger;color:#093">
                	<td width="150" style="font-size:larger;">Replace Bundle Qty</td>
                    <td width="120" style="font-size:larger;">From:
                    <input type="text" id="txt_replace_from" name="txt_replace_from" style="width:70px;" class="text_boxes_numeric" />
                    </td>
                    <td width="120" style="font-size:larger;">To:
                    <input type="text" id="txt_replace_to" name="txt_replace_to" class="text_boxes_numeric"  style="width:70px;" />
                    </td>
                    <td width="100"><input type="button" value="OK" style="width:80px"  class="formbutton" onClick="fnc_replace_qty()"/></td>
                </tr>
            </table>
        <?
		$job_qty=0;
		$order_number_arr=return_library_array( "select id, po_number from   wo_po_break_down",'id','po_number');
		foreach($sql_dtls as $val)
		{
	 	$ship_date=return_field_value("pub_shipment_date","wo_po_break_down","id=".$val[csf('order_id')]."");


		?>
			<div style="width:800px; margin-top:10px" id="" align="left">
				<table cellpadding="0" cellspacing="0" width="800" class="" border="1" rules="all" id="order_table_<? echo $j; ?>">
					<tr >
						<td colspan="6">
							 <b>  Order No:<? echo $order_number_arr[$val[csf('order_id')]]; ?> ;&nbsp; Gmt Item:<? echo $garments_item[$val[csf('gmt_item_id')]]; ?> ;&nbsp; Color:<? echo $color_arr[$val[csf('color_id')]]; ?>  ; &nbsp; </b><br/>
							 <b>
							   Ship Date:<? echo change_date_format($ship_date);?>;&nbsp;Order Qty:<? echo $val[csf('order_qty')]; ?></b>
							   <br/>
			  				<p style="color:red; font-size:12px; text-align:center">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  Double Click on Reject Qty Field for Defect Record</p>
						</td>
						<td>
					 	<?

						$size_total_sql=sql_select("select  a.country_id,sum(a.size_qty) as marker_qty,count(a.size_id) as bdl_no,max(a.size_qty) as pcs_per_bundle from ppl_cut_lay_bundle a, ppl_cut_lay_size b where a.mst_id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.mst_id=".$val[csf('id')]." and a.dtls_id=".$val[csf('details_id')]." and a.order_id=".$val[csf('order_id')]."  group by a.country_id,b.size_id");
					    $s=1;
					    foreach($size_total_sql as $sval)
					    {
							if($s==1)
							{
								$dtls_size_qty=$sval[csf("marker_qty")];
								$bundle_no=$sval[csf("bdl_no")];
								$pcs_per_bundle=$sval[csf("pcs_per_bundle")];
							}
							else
							{
								$dtls_size_qty.="*".$sval[csf("marker_qty")];
								$bundle_no.="*".$sval[csf("bdl_no")];
								$pcs_per_bundle.="*".$sval[csf("pcs_per_bundle")];
							}
							$s++;
					    }
					?>
					 <input type="hidden" name="txt_dtls_size_id_<? echo $j; ?>"  id="txt_dtls_size_id_<? echo $j; ?>"  value="<? echo $dtls_size_id; ?>"  />
					 <input type="hidden" name="txt_dtls_size_qty_<? echo $j; ?>"  id="txt_dtls_size_qty_<? echo $j; ?>"  value="<? echo $dtls_size_qty; ?>"  />
					 <input type="hidden" name="txt_dtls_size_bdl_<? echo $j; ?>"  id="txt_dtls_size_bdl_<? echo $j; ?>"  value="<? echo $bundle_no; ?>"  />                 <input type="hidden" name="txt__pcs_per_bdl_<? echo $j; ?>"  id="txt__pcs_per_bdl_<? echo $j; ?>"  value="<? echo $pcs_per_bundle; ?>"  />
					 <input type="hidden" name="txt_oder_qty_<? echo $j; ?>"  id="txt_oder_qty_<? echo $j; ?>"  value="<? echo $val[csf('order_qty')]; ?>"  />
					 <input type="hidden" name="hidden_po_<? echo $j; ?>"  id="hidden_po_<? echo $j; ?>"  value="<? echo $val[csf('order_id')]; ?>"  />
					 <input type="hidden" name="hidden_lay_dtls_id_<? echo $j; ?>"  id="hidden_lay_dtls_id_<? echo $j; ?>"  value="<? echo $val[csf('details_id')]; ?>"  />
					 <input type="hidden" name="hidden_gmt_<? echo $j; ?>"  id="hidden_gmt_<? echo $j; ?>"  value="<? echo $val[csf('gmt_item_id')]; ?>"  />
                      <input type="hidden" name="hidden_color_<? echo $j; ?>"  id="hidden_color_<? echo $j; ?>"  value="<? echo $val[csf('color_id')]; ?>"  />
							</td>
					 </tr>
			  </table>
			  <table cellpadding="0" cellspacing="0" width="650" class="rpt_table" border="1" rules="all" id="order_table_<? echo $j; ?>">
			  	<thead>
				  	<tr>
                        <th  width="30" rowspan="2" rclass="">SL</th>
                        <th width="100" rowspan="2">Country</th>
                        <th width="80" rowspan="2">Bundle No</th>
                        <th width="100" rowspan="2" >Size</th>
                        <th width="100" colspan="2">RMG No</th>
                        <th width="60" rowspan="2" class="">Bundle Qty </th>
                        <th width="50" rowspan="2">Reject Qty</th>
                        <th width="50" rowspan="2">Replace Qty</th>
                        <th width="60" rowspan="2">QC Pass Qty</th>
					  </tr>
					  <tr>
                        <th  width="50"  >From</th>
                        <th width="50"  >To</th>
					 </tr>
			   </thead>
			   <tbody id="tbl_body_<? echo $j; ?>">
		<?
				   $bundle_data=sql_select("SELECT DISTINCT a.id,a.country_id,a.bundle_no,a.barcode_no,a.size_id,a.number_start,a.number_end,a.size_qty from ppl_cut_lay_bundle a, ppl_cut_lay_size b where  a.mst_id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.mst_id=".$val[csf('id')]." and a.dtls_id=".$val[csf('details_id')]." and a.order_id=".$val[csf('order_id')]." and a.size_qty>0 order by a.id ASC");
					$i=1;
					$color_qty=0;
					foreach($bundle_data as $row)
					{
						$color_qty+=$row[csf('size_qty')];
						$job_qty+=$row[csf('size_qty')];
		?>
					 <tr id="table_tr_<? echo $j."_".$i; ?>">
						  <td id=""><? echo $i; ?>
						  <input type="hidden" id="hidden_order_<? echo $i; ?>"  name="hidden_order_<? echo $i; ?>" value="<? echo $val[csf('order_id')]; ?>"/>
						 <input type="hidden" name="size_id_<? echo $j."_".$i; ?>"  id="size_id_<? echo $j."_".$i; ?>"  value="<? echo $row[csf('size_id')]; ?>"  />
						 <input type="hidden" name="update_details_id_<? echo $j."_".$i; ?>"  id="update_details_id_<? echo $j."_".$i; ?>"  value="<? echo $qc_details_arr[$val[csf("order_id")]][$val[csf("color_id")]][$row[csf("bundle_no")]]['update_id'];  ?>"  />
						  <input type="hidden" name="hidden_country_<? echo $j."_".$i; ?>"  id="hidden_country_<? echo $j."_".$i; ?>"  value="<? echo $row[csf('country_id')]; ?>"  />
						  <input type="hidden" name="actual_reject_<? echo $j."_".$i; ?>"  id="actual_reject_<? echo $j."_".$i; ?>"  value="<? echo $bundle_reject_data[$row[csf('barcode_no')]]; ?>"  />

						  </td>
						  <td align="center" id="txt_country_name_<? echo $j."_".$i; ?>"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
						  <td align="center" id="txt_bundle_<? echo $j."_".$i; ?>" title="Barcode No: <?php echo $row[csf('barcode_no')]; ?>"><? echo $row[csf('bundle_no')]; ?></td>
						  <td align="center" id="txt_size_<? echo $j."_".$i; ?>" ><? echo $size_arr[$row[csf('size_id')]]; ?>
						  </td>
						  <td align="right" id="txt_start_<? echo $j."_".$i; ?>"><? echo $row[csf('number_start')]; ?></td>
						  <td align="right" id="txt_end_<? echo $j."_".$i; ?>"><? echo $row[csf('number_end')]; ?></td>
						  <td align="right" id="txt_qty_<? echo $j."_".$i; ?>"><? echo $row[csf('size_qty')]; ?>
						  </td>
						  <td  align="center">
                         <?  $colo=''; if($bundle_reject_data[$row[csf('barcode_no')]]!='') $colo="  border-color:#FF0000";?>
							   <input type="text" name="txt_reject_<? echo $j."_".$i; ?>"  id="txt_reject_<? echo $j."_".$i; ?>" class="text_boxes_numeric" style="width:68px; <? echo  $colo; ?>"   onKeyUp="total_qc_pass(this.id,this.value,<? echo $j; ?>)"  onDblClick="pop_entry_reject(<? echo $j; ?>,<? echo $i; ?>)" value="<?  if($qc_details_arr[$val[csf("order_id")]][$val[csf("color_id")]][$row[csf("bundle_no")]]['reject_qty']!=0) echo $qc_details_arr[$val[csf("order_id")]][$val[csf("color_id")]][$row[csf("bundle_no")]]['reject_qty'];  ?>"/>                      </td>
							<td  align="center">
							 <input type="text" name="txt_replace_<? echo $j."_".$i; ?>"  id="txt_replace_<? echo $j."_".$i; ?>" class="text_boxes_numeric" style="width:50px"   onKeyUp="total_qc_pass(this.id,this.value,<? echo $j; ?>)" value="<?  if($qc_details_arr[$val[csf("order_id")]][$val[csf("color_id")]][$row[csf("bundle_no")]]['replace_qty']!=0) echo $qc_details_arr[$val[csf("order_id")]][$val[csf("color_id")]][$row[csf("bundle_no")]]['replace_qty'];  ?>" />                      </td>

						  <td align="center">
							   <input type="text" name="txt_qcpass_<? echo $j."_".$i; ?>"  id="txt_qcpass_<? echo $j."_".$i; ?>" class="text_boxes_numeric"  value="<? echo ($row[csf('size_qty')] -$qc_details_arr[$val[csf("order_id")]][$val[csf("color_id")]][$row[csf("bundle_no")]]['reject_qty'])+$qc_details_arr[$val[csf("order_id")]][$val[csf("color_id")]][$row[csf("bundle_no")]]['replace_qty'];  ?>" style="width:68px"  readonly/>
							   <input type="hidden" name="hidden_qcpass_<? echo $j."_".$i; ?>"  id="hidden_qcpass_<? echo $j."_".$i; ?>" class="text_boxes_numeric"  value="<? echo $row[csf('size_qty')]; ?>"  />
                                <input type="hidden" id="hidden_barcode_<? echo $j."_".$i; ?>" name="hidden_barcode_<? echo $j."_".$i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>" />
						   </td>
					 </tr>
		<?
					$i++;
					}
					?>
					</tbody>
					  <tr  style=" background-color:#B0B0B0;"  b height="10">
							  <td id="" ></td>
							  <td align="center"  ><?   //echo $j; ?></td>
							   <td align="center"  ><?   //echo $j; ?></td>
							  <td align="center" ><? //echo $row[csf('bundle_no')]; ?></td>
							  <td align="right" ><input type="hidden" id="hidden_total_qc_qty_<? echo $j; ?>"
							   name="hidden_total_qc_qty_<? echo $j; ?>"  value="<? echo $order_color_qty[$val[csf("order_id")]][$val[csf("gmt_item_id")]][$val[csf("color_id")]]['qc_pass_qty'];  ?> "/></td>
							  <td align="right" ><input type="hidden" id="hidden_reject_qty_<? echo $j; ?>"
							  name="hidden_reject_qty_<? echo $j; ?>"  value="<? echo $order_color_qty[$val[csf("order_id")]][$val[csf("gmt_item_id")]][$val[csf("color_id")]]['reject_qty'];  ?>" />
							  <input type="hidden" id="hidden_replace_qty_<? echo $j; ?>"
							  name="hidden_replace_qty_<? echo $j; ?>"  value="<? echo $order_color_qty[$val[csf("order_id")]][$val[csf("gmt_item_id")]][$val[csf("color_id")]]['replace_qty'];  ?>" />

							  </td>
							  <td align="right" >Total</td>
							  <td  align="right" id="total_reject_qty_<? echo $j; ?>"><? echo $order_color_qty[$val[csf("order_id")]][$val[csf("gmt_item_id")]][$val[csf("color_id")]]['reject_qty'];  ?></td>
							   <td  align="right" id="total_replace_qty_<? echo $j; ?>"> <? echo $order_color_qty[$val[csf("order_id")]][$val[csf("gmt_item_id")]][$val[csf("color_id")]]['replace_qty'];  ?></td>
							  <td align="right" id="total_qc_qty_<? echo $j; ?>"><? echo $order_color_qty[$val[csf("order_id")]][$val[csf("gmt_item_id")]][$val[csf("color_id")]]['qc_pass_qty'];  ?>
							  </td>
					 </tr>
				 <?
				$j++;
		   }

		?>
			  <tfoot>
				 <tr class="general"  height="15" >
					  <th width="480"  align="right"  colspan="7"> Grand Total</th>
					  <th width="60"  align=" right" id="grand_reject_qty"><? echo $total_qty['reject_qty'];  ?> </th>
					  <th width="60"  align=" right" id="grand_replace_qty"> <? echo $total_qty['replace_qty'];  ?> </th>
					  <th  width="60" align="right" id="grand_qc_qty"><? echo $total_qty['qc_pass_qty'];  ?> </th>


				 </tr>
			 </tfoot>
		  </table>
		  </div>

			 <table width="800" cellpadding="0" cellspacing="2" align="center">
				   <tr>
					   <td colspan="7" align="center" class="">
							<?
							   echo load_submit_buttons( $permission, "fnc_cut_qc_info", 1,0,"reset_form('','','','','clear_tr()')",1);
							?>
                             <input class="formbutton" value="Print" onClick="fnc_cut_qc_info(5)" style="width:80px;" type="button">
							 <input class="formbutton" value="Print 2" onClick="fnc_cut_qc_info(9)" style="width:80px;" type="button">


							</td>
				  </tr>
				</table>
			<input type="hidden" id="total_order_id" name="total_order_id" value="<? echo $j-1; ?>"  />
		<?
	}
	else
	{
	 	$j=1;

	 	$sql_dtls=sql_select("SELECT b.id,c.order_id,a.ship_date,a.color_id,a.gmt_item_id,a.plies,a.marker_qty,a.order_qty,a.total_lay_qty, a.lay_balance_qty, b.job_no, b.job_year, b.company_id , a.id as details_id from ppl_cut_lay_dtls a, ppl_cut_lay_mst b,ppl_cut_lay_bundle c where b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.cutting_no='".$data."' and b.id=c.mst_id and a.id=c.dtls_id and c.size_qty>0  group by b.id, c.order_id, a.ship_date,a.color_id, a.gmt_item_id,a.plies,a.marker_qty, a.order_qty, a.total_lay_qty, a.lay_balance_qty, b.job_no, b.job_year, b.company_id ,a.id order by a.id,c.order_id desc");
	 	?>
     		<table cellpadding="0" cellspacing="0" width="550" class="" border="1" rules="all" id="" align="center">
            	<tr style=" font-size:larger;color:#093">
                	<td width="150" style="font-size:larger;">Erase Bundle Qty</td>
                    <td width="120" style="font-size:larger;">From:
                    <input type="text" id="txt_erage_from" name="txt_erage_from" style="width:70px;" class="text_boxes_numeric" />
                    </td>
                    <td width="120" style="font-size:larger;">To:
                    <input type="text" id="txt_erage_to" name="txt_erage_to" class="text_boxes_numeric"  style="width:70px;" />
                    </td>
                    <td width="100"><input type="button" value="OK" style="width:80px"  class="formbutton" onClick="fnc_erage_qty()"/></td>
                </tr>
                <tr style=" font-size:larger;color:#093">
                	<td width="150" style="font-size:larger;">Replace Bundle Qty</td>
                    <td width="120" style="font-size:larger;">From:
                    <input type="text" id="txt_replace_from" name="txt_replace_from" style="width:70px;" class="text_boxes_numeric" />
                    </td>
                    <td width="120" style="font-size:larger;">To:
                    <input type="text" id="txt_replace_to" name="txt_replace_to" class="text_boxes_numeric"  style="width:70px;" />
                    </td>
                    <td width="100"><input type="button" value="OK" style="width:80px"  class="formbutton" onClick="fnc_replace_qty()"/></td>
                </tr>
            </table>
     <?
	 $job_qty=0;
	 $order_number_arr=return_library_array( "select id, po_number from   wo_po_break_down",'id','po_number');
	 foreach($sql_dtls as $val)
	 {
		 $ship_date=return_field_value("pub_shipment_date","wo_po_break_down","id=".$val[csf('order_id')]."");
		?>
     	<div style="width:800px; margin-top:10px" id="" align="left">
     	<table cellpadding="0" cellspacing="0" width="800" class="" border="1" rules="all" id="order_table_<? echo $j; ?>" >
    	 <tr >
        		 <td colspan="6" >
                         <b>  Order No:<? echo $order_number_arr[$val[csf('order_id')]]; ?> ;&nbsp; Gmt Item:<? echo $garments_item[$val[csf('gmt_item_id')]]; ?> ;&nbsp; Color:<? echo $color_arr[$val[csf('color_id')]]; ?>  ;

                           </b><br/>
                          <b>
                           Ship Date:<? echo change_date_format($ship_date); ?>;&nbsp;Order Qty:<? echo $val[csf('order_qty')]; ?>
                           </b>

            <br/>
           <p style="color:red; font-size:12px; text-align:center">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  Double Click on Reject Qty Field for Defect Record</p>

                  </td>
                  <td>
                   <?

                    $size_total_sql=sql_select("select  a.country_id,sum(a.size_qty) as marker_qty,count(a.size_id) as bdl_no,max(a.size_qty) as pcs_per_bundle from ppl_cut_lay_bundle a, ppl_cut_lay_size b where a.mst_id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.mst_id=".$val[csf('id')]." and a.dtls_id=".$val[csf('details_id')]." and a.order_id=".$val[csf('order_id')]." group by a.country_id,b.size_id");

				 $s=1;
				 foreach($size_total_sql as $sval)
				 {

					if($s==1)
					{

						$dtls_size_qty=$sval[csf("marker_qty")];
						$bundle_no=$sval[csf("bdl_no")];
						$pcs_per_bundle=$sval[csf("pcs_per_bundle")];
					}
					else
					{

						$dtls_size_qty.="*".$sval[csf("marker_qty")];
						$bundle_no.="*".$sval[csf("bdl_no")];
						$pcs_per_bundle.="*".$sval[csf("pcs_per_bundle")];
					}
						$s++;
				 }
        ?>
                 <input type="hidden" name="txt_dtls_size_id_<? echo $j; ?>"  id="txt_dtls_size_id_<? echo $j; ?>"  value="<? echo $dtls_size_id; ?>"  />
                 <input type="hidden" name="txt_dtls_size_qty_<? echo $j; ?>"  id="txt_dtls_size_qty_<? echo $j; ?>"  value="<? echo $dtls_size_qty; ?>"  />
                 <input type="hidden" name="txt_dtls_size_bdl_<? echo $j; ?>"  id="txt_dtls_size_bdl_<? echo $j; ?>"  value="<? echo $bundle_no; ?>"  />                 <input type="hidden" name="txt__pcs_per_bdl_<? echo $j; ?>"  id="txt__pcs_per_bdl_<? echo $j; ?>"  value="<? echo $pcs_per_bundle; ?>"  />
                 <input type="hidden" name="txt_oder_qty_<? echo $j; ?>"  id="txt_oder_qty_<? echo $j; ?>"  value="<? echo $val[csf('order_qty')]; ?>"  />
                 <input type="hidden" name="hidden_po_<? echo $j; ?>"  id="hidden_po_<? echo $j; ?>"  value="<? echo $val[csf('order_id')]; ?>"  />
                 <input type="hidden" name="hidden_lay_dtls_id_<? echo $j; ?>"  id="hidden_lay_dtls_id_<? echo $j; ?>"  value="<? echo $val[csf('details_id')]; ?>"  />
                 <input type="hidden" name="hidden_gmt_<? echo $j; ?>"  id="hidden_gmt_<? echo $j; ?>"  value="<? echo $val[csf('gmt_item_id')]; ?>"  />                 <input type="hidden" name="hidden_color_<? echo $j; ?>"  id="hidden_color_<? echo $j; ?>"  value="<? echo $val[csf('color_id')]; ?>"  />

                    </td>
             </tr>
      </table>
      <table cellpadding="0" cellspacing="0" width="650" class="rpt_table" border="1" rules="all" id="order_table_<? echo $j; ?>">
          <thead >
                 <tr>
                        <th  width="30" rowspan="2" rclass="">SL</th>
                        <th width="100" rowspan="2">Country</th>
                        <th width="80" rowspan="2">Bundle No</th>
                        <th width="100" rowspan="2" >Size</th>
                        <th width="100" colspan="2">RMG No</th>
                        <th width="60" rowspan="2" class="">Bundle Qty </th>
                        <th width="50" rowspan="2">Reject Qty</th>
                        <th width="50" rowspan="2">Replace Qty</th>
                        <th width="60" rowspan="2">QC Pass Qty</th>
                   </tr>
                  <tr>
                        <th  width="50"  >From</th>
                        <th width="50"  >To</th>
                   </tr>
         </thead>
        <tbody id="tbl_body_<? echo $j; ?>">
		<?

				 $bundle_data=sql_select("select  a.country_id,a.id,a.bundle_no,a.barcode_no,a.size_id,a.number_start,a.number_end,a.size_qty from ppl_cut_lay_bundle a, ppl_cut_lay_size b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and a.size_id=b.size_id and a.mst_id=".$val[csf('id')]." and a.dtls_id=".$val[csf('details_id')]." and a.order_id=".$val[csf('order_id')]." and a.size_qty>0  group by a.country_id,a.id,a.bundle_no,a.size_id,a.number_start,a.number_end,a.size_qty,a.barcode_no  order by a.id ASC");
			    $i=1;
				$color_qty=0;
				foreach($bundle_data as $row)
				{
					$color_qty+=$row[csf('size_qty')];
					$job_qty+=$row[csf('size_qty')];
		?>
                 <tr id="table_tr_<? echo $j."_".$i; ?>">
                     <td id=""><? echo $i; ?>
                     <input type="hidden" id="hidden_order_<? echo $i; ?>"  name="hidden_order_<? echo $i; ?>" value="<? echo $val[csf('order_id')]; ?>"/>
                     <input type="hidden" name="size_id_<? echo $j."_".$i; ?>"  id="size_id_<? echo $j."_".$i; ?>"  value="<? echo $row[csf('size_id')]; ?>"  />
                      <input type="hidden" name="update_details_id_<? echo $j."_".$i; ?>"  id="update_details_id_<? echo $j."_".$i; ?>"  value="<? //echo $qc_details_arr[$val[csf("order_id")]][$val[csf("color_id")]][$row[csf("bundle_no")]]['update_id'];  ?>"  />
                     <input type="hidden" name="hidden_country_<? echo $j."_".$i; ?>"  id="hidden_country_<? echo $j."_".$i; ?>"  value="<? echo $row[csf('country_id')]; ?>"  />
                      <input type="hidden" name="actual_reject_<? echo $j."_".$i; ?>"  id="actual_reject_<? echo $j."_".$i; ?>"  value=""  />
                      </td>
                      <td align="center" id="txt_country_name_<? echo $j."_".$i; ?>"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
                      <td align="center" id="txt_bundle_<? echo $j."_".$i; ?>" title="Barcode No: <?php echo $row[csf('barcode_no')]; ?>"><? echo $row[csf('bundle_no')]; ?></td>
                      <td align="center" id="txt_size_<? echo $j."_".$i; ?>" ><? echo $size_arr[$row[csf('size_id')]]; ?>
                      </td>
                      <td align="right" id="txt_start_<? echo $j."_".$i; ?>"><? echo $row[csf('number_start')]; ?></td>
                      <td align="right" id="txt_end_<? echo $j."_".$i; ?>"><? echo $row[csf('number_end')]; ?></td>
                      <td align="right" id="txt_qty_<? echo $j."_".$i; ?>"><? echo $row[csf('size_qty')]; ?>
                      </td>
                      <td  align="center">
                           <input type="text" name="txt_reject_<? echo $j."_".$i; ?>"  id="txt_reject_<? echo $j."_".$i; ?>" class="text_boxes_numeric" style="width:40px"   onKeyUp="total_qc_pass(this.id,this.value,<? echo $j; ?>)" onDblClick="pop_entry_reject(<? echo $j; ?>,<? echo $i; ?>)"/>                      </td>
                      <td  align="center">
                         <input type="text" name="txt_replace_<? echo $j."_".$i; ?>"  id="txt_replace_<? echo $j."_".$i; ?>" class="text_boxes_numeric" style="width:40px"   onKeyUp="total_qc_pass(this.id,this.value,<? echo $j; ?>)"/>                      </td>
                      <td align="center">
                           <input type="text" name="txt_qcpass_<? echo $j."_".$i; ?>"  id="txt_qcpass_<? echo $j."_".$i; ?>" class="text_boxes_numeric"  value="<? echo $row[csf('size_qty')]; ?>" style="width:50px"  readonly/>
                           <input type="hidden" name="hidden_qcpass_<? echo $j."_".$i; ?>"  id="hidden_qcpass_<? echo $j."_".$i; ?>" class="text_boxes_numeric"  value="<? echo $row[csf('size_qty')]; ?>"   readonly/>
                           <input type="hidden" id="hidden_barcode_<? echo $j."_".$i; ?>" name="hidden_barcode_<? echo $j."_".$i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>" />
                       </td>
                 </tr>
		<?
            $i++;
		    }
			?>
            </tbody>
              <tr  style=" background-color:#B0B0B0;"  b height="10">
                      <td id="" ></td>
                      <td id="" ></td>
                      <td align="center"  ><? // echo $j; ?></td>
                      <td align="center" ><? //echo $row[csf('bundle_no')]; ?></td>
                      <td align="right" ><input type="hidden" id="hidden_total_qc_qty_<? echo $j; ?>"
                       name="hidden_total_qc_qty_<? echo $j; ?>"  value="<? echo $color_qty;  ?> "/></td>
                      <td align="right" ><input type="hidden" id="hidden_reject_qty_<? echo $j; ?>"
                      name="hidden_reject_qty_<? echo $j; ?>"  value="0" />
                      <input type="hidden" id="hidden_replace_qty_<? echo $j; ?>"
						  name="hidden_replace_qty_<? echo $j; ?>"  value="0" />
                      </td>
                      <td align="right" >Total</td>
                      <td  align="right" id="total_reject_qty_<? echo $j; ?>"></td>
                      <td  align="right" id="total_replace_qty_<? echo $j; ?>"></td>
                      <td align="right" id="total_qc_qty_<? echo $j; ?>"><? echo $color_qty;  ?>
                      </td>
                 </tr>
			 <?
		 $j++;
	 }

		?>
            <tfoot>
                 <tr class="general"  height="15" >
                      <th width="480"  align="right"  colspan="7"> Grand Total</th>
                      <th width="60"  align=" right" id="grand_reject_qty"></th>
                      <th width="60"  align=" right" id="grand_replace_qty"></th>
                      <th  width="60" align="right" id="grand_qc_qty"><? echo $job_qty;  ?> </th>

                 </tr>
             </tfoot>
    	</table>
    	</div>
      	<table cellpadding="0" cellspacing="0" width="650" class="rpt_table" border="1" rules="all" >

           </table>
            <table width="800" cellpadding="0" cellspacing="2" align="center">
               	<tr>
                   	<td colspan="7" align="center" class="">                      
                        
                    
                        <?
                           echo load_submit_buttons( $permission, "fnc_cut_qc_info", 0,0,"reset_form('','','','','clear_tr()')",1);
                        ?>
                         <input class="formbutton" value="Print" onClick="fnc_cut_qc_info(5)" style="width:80px;" type="button">
                         <input class="formbutton" value="Reject Qnty Challan" onClick="fnc_cut_qc_info(6)" style="width:150px;" type="button">
                    </td>
              	</tr>
            </table>
    	<input type="hidden" id="total_order_id" name="total_order_id" value="<? echo $j-1; ?>"  />
    	<?
	}

}

if($action=="update_order_details_list")
{
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$color_arr=return_library_array( "select id,color_name  from  lib_color", "id", "color_name"  );
	$data=explode("**",$data);

	$cutt_qc_dtls=sql_select(" SELECT a.id,a.mst_id,a.order_id,a.item_id,a.country_id,a.color_id,a.size_id,a.color_size_id,a.bundle_no,a.number_start,a.number_end,a.bundle_qty,a.reject_qty,a.replace_qty,a.qc_pass_qty from pro_gmts_cutting_qc_dtls a,pro_gmts_cutting_qc_mst b where a.mst_id=b.id and b.cutting_qc_no='".$data[0]."' and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0");
	$qc_details_arr=array();
	$total_qty=array();
	$po_id_array=array();
	$order_color_qty=array();
	foreach($cutt_qc_dtls as $inf)
	{
		$qc_details_arr[$inf[csf("order_id")]][$inf[csf("country_id")]][$inf[csf("color_id")]][$inf[csf("bundle_no")]]['qc_pass_qty']=$inf[csf("qc_pass_qty")];
		$qc_details_arr[$inf[csf("order_id")]][$inf[csf("country_id")]][$inf[csf("color_id")]][$inf[csf("bundle_no")]]['update_id']=$inf[csf("id")];
		$qc_details_arr[$inf[csf("order_id")]][$inf[csf("country_id")]][$inf[csf("color_id")]][$inf[csf("bundle_no")]]['reject_qty']=$inf[csf("reject_qty")];
		$qc_details_arr[$inf[csf("order_id")]][$inf[csf("country_id")]][$inf[csf("color_id")]][$inf[csf("bundle_no")]]['replace_qty']=$inf[csf("replace_qty")];
		$order_color_qty[$inf[csf("order_id")]][$inf[csf("item_id")]][$inf[csf("country_id")]][$inf[csf("color_id")]]['qc_pass_qty']+=$inf[csf("qc_pass_qty")];
		$order_color_qty[$inf[csf("order_id")]][$inf[csf("item_id")]][$inf[csf("country_id")]][$inf[csf("color_id")]]['reject_qty']+=$inf[csf("reject_qty")];
		$order_color_qty[$inf[csf("order_id")]][$inf[csf("item_id")]][$inf[csf("country_id")]][$inf[csf("color_id")]]['replace_qty']+=$inf[csf("replace_qty")];
		$total_qty['qc_pass_qty']+=$inf[csf("qc_pass_qty")];
		$total_qty['reject_qty']+=$inf[csf("reject_qty")];
		$total_qty['replace_qty']+=$inf[csf("replace_qty")];
		$po_id_array[$inf[csf("order_id")]]=$inf[csf("order_id")];
	}
	$poIds = implode(",", $po_id_array);
	$sql = sql_select( "SELECT PO_BREAK_DOWN_ID,COUNTRY_ID, COUNTRY_SHIP_DATE from wo_po_color_size_breakdown where po_break_down_id in($poIds)");
	$country_ship_date_arr = array();
	foreach ($sql as $val) 
	{
		$country_ship_date_arr[$val['PO_BREAK_DOWN_ID']][$val['COUNTRY_ID']] = $val['COUNTRY_SHIP_DATE'];
	}
	$sql_cutting_mst=sql_select("SELECT b.bundle_no from pro_gmts_cutting_qc_mst a,pro_gmts_cutting_qc_dtls b where a.status_active=1 and b.status_active=1 and a.id=b.mst_id and a.cutting_qc_no='$data[0]'");
	foreach($sql_cutting_mst as $k=>$val)
	{
		$all_bundle_no_arr[$val[csf("bundle_no")]]="'".$val[csf("bundle_no")]."'";
	}

	$all_bundle_arr=$all_bundle_no_arr;
	  	$bundle_count=count($all_bundle_arr);
  	$bundle_nos_cond="";
		if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
			$bundleArr=array_chunk($all_bundle_arr,399);
			foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" bundle_no in($bundleNos) or ";
			}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
			$bundle_nos_cond.=")";
		}
	else
	{
		$all_bundle_no=implode(",",$all_bundle_no_arr);
		$bundle_nos_cond=" and bundle_no in ($all_bundle_no)";
		}



	$sql_is_bundle_exist_next="SELECT bundle_no from  pro_garments_production_dtls where status_active=1 and is_deleted=0  and production_type <> 1 $bundle_nos_cond ";
	foreach(sql_select($sql_is_bundle_exist_next) as $key=>$values)
	{
		$is_bundle_exist_next_arr[$values[csf("bundle_no")]]=$values[csf("bundle_no")];
	}
	//print_r($is_bundle_exist_next_arr);

	 $sql_bundle_reject=sql_select("SELECT a.defect_type_id,a.production_type,a.defect_point_id,sum(a.defect_qty) as defect_qty,a.bundle_no from  pro_gmts_prod_dft a,pro_garments_production_mst b where b.id=a.mst_id and  b.production_type=1 and b.cut_no='$data[1]' and defect_type_id=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.defect_type_id,a.production_type,a.defect_point_id, a.bundle_no ");
	 $bundle_reject_data=array();
	 foreach($sql_bundle_reject as $inf)
	 {
		 //if(in_array($inf[csf('bundle_no')],$check_arr)) // Replaced by CTO
		 if( $check_arr[$inf[csf('bundle_no')]]!='')
		 {
		 	$bundle_reject_data[$inf[csf('bundle_no')]] .="**".$inf[csf('defect_point_id')]."*".$inf[csf('defect_qty')];
		 }
		 else
		 {
		 	$bundle_reject_data[$inf[csf('bundle_no')]]=$inf[csf('defect_point_id')]."*".$inf[csf('defect_qty')];
		 }
		$check_arr[$inf[csf('bundle_no')]]=$inf[csf('bundle_no')];
	 }
	// echo "<pre>";
	//print_r($bundle_reject_data);

	 $j=1;
	 $sql_dtls=sql_select("SELECT b.id,c.order_id,a.ship_date,a.color_id,a.gmt_item_id,a.plies,a.marker_qty,a.order_qty,a.total_lay_qty,a.lay_balance_qty,
	 b.job_no,b.job_year,b.company_id ,c.country_id,a.id as details_id from ppl_cut_lay_dtls a, ppl_cut_lay_mst b,ppl_cut_lay_bundle c
	 where b.id=a.mst_id and a.id=c.dtls_id and b.id=c.mst_id and b.cutting_no='".$data[1]."'  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	 group by b.id,c.order_id,a.ship_date,a.color_id,a.gmt_item_id,a.plies,a.marker_qty,a.order_qty,a.total_lay_qty,a.lay_balance_qty,
	 b.job_no,b.job_year,b.company_id ,c.country_id,a.id  order by a.id");
	 ?>
	 	<table cellpadding="0" cellspacing="0" width="550" class="" border="1" rules="all" id="" align="center">
            	<tr style=" font-size:larger;color:#093">
                	<td width="150" style="font-size:larger;">Erase Bundle Qty</td>
                    <td width="120" style="font-size:larger;">From:
                    <input type="text" id="txt_erage_from" name="txt_erage_from" style="width:70px;" class="text_boxes_numeric" />
                    </td>
                    <td width="120" style="font-size:larger;">To:
                    <input type="text" id="txt_erage_to" name="txt_erage_to" class="text_boxes_numeric"  style="width:70px;" />
                    </td>
                    <td width="100"><input type="button" value="OK" style="width:80px"  class="formbutton" onClick="fnc_erage_qty()"/></td>
                </tr>
                <tr style=" font-size:larger;color:#093">
                	<td width="150" style="font-size:larger;">Replace Bundle Qty</td>
                    <td width="120" style="font-size:larger;">From:
                    <input type="text" id="txt_replace_from" name="txt_replace_from" style="width:70px;" class="text_boxes_numeric" />
                    </td>
                    <td width="120" style="font-size:larger;">To:
                    <input type="text" id="txt_replace_to" name="txt_replace_to" class="text_boxes_numeric"  style="width:70px;" />
                    </td>
                    <td width="100"><input type="button" value="OK" style="width:80px"  class="formbutton" onClick="fnc_replace_qty()"/></td>
                </tr>
            </table>
	 <?
	 $job_qty=0;$po_id_arr = array();
	 foreach ($sql_dtls as $val) 
	 {
	 	$po_id_arr[$val['ORDER_ID']] = $val['ORDER_ID'];
	 }
	 $po_id_cond = where_con_using_array($po_id_arr,0,"id");
	 $order_number_arr=return_library_array( "select id, po_number from   wo_po_break_down where status_active=1  $po_id_cond",'id','po_number');
	 foreach($sql_dtls as $val)
	 {
	?>
        <div style="width:800px; margin-top:10px" id="" align="left">
        <table cellpadding="0" cellspacing="0" width="800" class="" border="1" rules="all" id="order_table_<? echo $j; ?>">
             <tr >
                    <td colspan="6">
                         <b>  Order No:<? echo $order_number_arr[$val[csf('order_id')]]; ?> ;&nbsp; Gmt Item:<? echo $garments_item[$val[csf('gmt_item_id')]]; ?> ;&nbsp; Color:<? echo $color_arr[$val[csf('color_id')]]; ?>  ; &nbsp; </b><br/>
                         <b>                           
                           Ship Date:<? echo change_date_format($country_ship_date_arr[$val[csf('order_id')]][$val[csf('country_id')]]); ?>;&nbsp;Order Qty:<? echo $val[csf('order_qty')]; ?></b>
    <br/>
           <p style="color:red; font-size:12px; text-align:center">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  Double Click on Reject Qty Field for Defect Record</p>
                    </td>
                    <td>
                          <?
                    $size_total_sql=sql_select("SELECT a.country_id, b.size_id,b.marker_qty,count(a.size_id) as bdl_no,max(a.size_qty) as pcs_per_bundle from ppl_cut_lay_bundle a, ppl_cut_lay_size b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.mst_id=".$val[csf('id')]." and a.dtls_id=".$val[csf('details_id')]." and a.country_id=".$val[csf('country_id')]."  and a.order_id=".$val[csf('order_id')]." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.size_id,b.marker_qty,a.country_id ");
					 $s=1;
					 foreach($size_total_sql as $sval)
					 {

						if($s==1)
							{
							$dtls_size_id=$sval[csf("size_id")];
							$dtls_size_qty=$sval[csf("marker_qty")];
							$bundle_no=$sval[csf("bdl_no")];
							$pcs_per_bundle=$sval[csf("pcs_per_bundle")];
							}
							else
							{
							$dtls_size_id.="*".$sval[csf("size_id")];
							$dtls_size_qty.="*".$sval[csf("marker_qty")];
							$bundle_no.="*".$sval[csf("bdl_no")];
							$pcs_per_bundle.="*".$sval[csf("pcs_per_bundle")];
							}
							$s++;
					 }
        ?>
                 <input type="hidden" name="txt_dtls_size_id_<? echo $j; ?>"  id="txt_dtls_size_id_<? echo $j; ?>"  value="<? echo $dtls_size_id; ?>"  />
                 <input type="hidden" name="txt_dtls_size_qty_<? echo $j; ?>"  id="txt_dtls_size_qty_<? echo $j; ?>"  value="<? echo $dtls_size_qty; ?>"  />
                 <input type="hidden" name="txt_dtls_size_bdl_<? echo $j; ?>"  id="txt_dtls_size_bdl_<? echo $j; ?>"  value="<? echo $bundle_no; ?>"  />                 <input type="hidden" name="txt__pcs_per_bdl_<? echo $j; ?>"  id="txt__pcs_per_bdl_<? echo $j; ?>"  value="<? echo $pcs_per_bundle; ?>"  />
                 <input type="hidden" name="txt_oder_qty_<? echo $j; ?>"  id="txt_oder_qty_<? echo $j; ?>"  value="<? echo $val[csf('order_qty')]; ?>"  />
                 <input type="hidden" name="hidden_po_<? echo $j; ?>"  id="hidden_po_<? echo $j; ?>"  value="<? echo $val[csf('order_id')]; ?>"  />
                 <input type="hidden" name="hidden_gmt_<? echo $j; ?>"  id="hidden_gmt_<? echo $j; ?>"  value="<? echo $val[csf('gmt_item_id')]; ?>"  />                 <input type="hidden" name="hidden_color_<? echo $j; ?>"  id="hidden_color_<? echo $j; ?>"  value="<? echo $val[csf('color_id')]; ?>"  />

                    	</td>
            	 </tr>
     	  </table>
    	  <table cellpadding="0" cellspacing="0" width="650" class="rpt_table" border="1" rules="all" id="order_table_<? echo $j; ?>">
        	  <thead >
                	 <tr>
                        <th  width="40" rowspan="2" rclass="">SL</th>
                        <th width="100" rowspan="2">Country</th>
                        <th width="100" rowspan="2">Bundle No</th>
                        <th width="120" rowspan="2" >Size</th>
                        <th width="120" colspan="2">RMG No</th>
                        <th width="80" rowspan="2" class="">Bundle Qty </th>
                        <th width="60" rowspan="2">Reject Qty</th>
                        <th width="60" rowspan="2">Replace Qty</th>
                        <th width="60" rowspan="2">QC Pass Qty</th>
                  	 </tr>
                	  <tr>
                        <th  width="60"  >From</th>
                        <th width="60"  >To</th>
                   </tr>
       	   </thead>
       	   <tbody id="tbl_body_<? echo $j; ?>">
	<?


			   $bundle_data=sql_select("SELECT a.country_id, a.id,a.bundle_no,a.barcode_no,a.size_id,a.number_start,a.number_end,a.size_qty from ppl_cut_lay_bundle a, ppl_cut_lay_size b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.mst_id=".$val[csf('id')]." and a.dtls_id=".$val[csf('details_id')]." and a.country_id=".$val[csf('country_id')]." and a.order_id=".$val[csf('order_id')]." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by a.country_id, a.id,a.bundle_no,a.barcode_no, a.size_id, a.number_start, a.number_end, a.size_qty order by a.id ASC");
			    $i=1;
				$color_qty=0;
				foreach($bundle_data as $row)
				{
					if($row[csf('size_qty')]>0)
					{
						$color_qty+=$row[csf('size_qty')];
						$job_qty+=$row[csf('size_qty')];
	?>
					 <tr id="table_tr_<? echo $j."_".$i; ?>">
						  <td id=""><? echo $i; ?>
						  <input type="hidden" id="hidden_order_<? echo $i; ?>"  name="hidden_order_<? echo $i; ?>" value="<? echo $val[csf('order_id')]; ?>"/>
						 <input type="hidden" name="size_id_<? echo $j."_".$i; ?>"  id="size_id_<? echo $j."_".$i; ?>"  value="<? echo $row[csf('size_id')]; ?>"  />
						 <input type="hidden" name="update_details_id_<? echo $j."_".$i; ?>"  id="update_details_id_<? echo $j."_".$i; ?>"  value="<? echo $qc_details_arr[$val[csf("order_id")]][$val[csf('country_id')]][$val[csf("color_id")]][$row[csf("bundle_no")]]['update_id'];  ?>"  />
						 <input type="hidden" name="hidden_country_<? echo $j."_".$i; ?>"  id="hidden_country_<? echo $j."_".$i; ?>"  value="<? echo $row[csf('country_id')]; ?>"  />
						 <input type="hidden" name="actual_reject_<? echo $j."_".$i; ?>"  id="actual_reject_<? echo $j."_".$i; ?>"  value="<? echo $bundle_reject_data[$row[csf('barcode_no')]]; ?>"  />
						  </td>
						  <td align="center" id="txt_country_name_<? echo $j."_".$i; ?>"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
						  <td align="center" id="txt_bundle_<? echo $j."_".$i; ?>" title="Barcode No: <?php echo $row[csf('barcode_no')]; ?>"><? echo $row[csf('bundle_no')]; ?></td>
						  <td align="center" id="txt_size_<? echo $j."_".$i; ?>" ><? echo $size_arr[$row[csf('size_id')]]; ?>
						  </td>
						  <td align="right" id="txt_start_<? echo $j."_".$i; ?>"><? echo $row[csf('number_start')]; ?></td>
						  <td align="right" id="txt_end_<? echo $j."_".$i; ?>"><? echo $row[csf('number_end')]; ?></td>
						  <td align="right" id="txt_qty_<? echo $j."_".$i; ?>"><? echo $row[csf('size_qty')]; ?>
						  </td>
						  <td  align="center">
							<?  $colo=''; if($bundle_reject_data[$row[csf('barcode_no')]]!='') $colo="  border-color:#FF0000";
							$readonly="";
							$reject_bg="";
							if($is_bundle_exist_next_arr[$row[csf("bundle_no")]])
							{
								$readonly="readonly";
								$reject_bg=";background-color:red;";
							}

							?>
							   <input type="text" name="txt_reject_<? echo $j."_".$i; ?>"  id="txt_reject_<? echo $j."_".$i; ?>" <? echo $readonly; ?> class="text_boxes_numeric" style="width:68px; <? echo $colo.$reject_bg;  ?>"   onKeyUp="total_qc_pass(this.id,this.value,<? echo $j; ?>)"  onDblClick="pop_entry_reject(<? echo $j; ?>,<? echo $i; ?>)" value="<?  if($qc_details_arr[$val[csf("order_id")]][$val[csf('country_id')]][$val[csf("color_id")]][$row[csf("bundle_no")]]['reject_qty']!=0) echo $qc_details_arr[$val[csf("order_id")]][$val[csf('country_id')]][$val[csf("color_id")]][$row[csf("bundle_no")]]['reject_qty'];  ?>"/>                      </td>
						  <td  align="center">
							 <input type="text" name="txt_replace_<? echo $j."_".$i; ?>"  id="txt_replace_<? echo $j."_".$i; ?>" class="text_boxes_numeric" style="width:50px"   onKeyUp="total_qc_pass(this.id,this.value,<? echo $j; ?>)" value="<?  if($qc_details_arr[$val[csf("order_id")]][$val[csf('country_id')]][$val[csf("color_id")]][$row[csf("bundle_no")]]['replace_qty']!=0) echo $qc_details_arr[$val[csf("order_id")]][$val[csf('country_id')]][$val[csf("color_id")]][$row[csf("bundle_no")]]['replace_qty'];  ?>" />                      </td>
						  <td align="center">
							   <input type="text" name="txt_qcpass_<? echo $j."_".$i; ?>"  id="txt_qcpass_<? echo $j."_".$i; ?>" class="text_boxes_numeric"  value="<? echo $qc_details_arr[$val[csf("order_id")]][$val[csf('country_id')]][$val[csf("color_id")]][$row[csf("bundle_no")]]['qc_pass_qty'];  ?>" style="width:68px"  readonly/>
							   <input type="hidden" name="hidden_qcpass_<? echo $j."_".$i; ?>"  id="hidden_qcpass_<? echo $j."_".$i; ?>" class="text_boxes_numeric"  value="<? echo $row[csf('size_qty')]; ?>"  />
								<input type="hidden" id="hidden_barcode_<? echo $j."_".$i; ?>" name="hidden_barcode_<? echo $j."_".$i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>" />
						   </td>
					 </tr>
	<?
					$i++;
					}
				}
				?>
				</tbody>
				  <tr  style=" background-color:#B0B0B0;"  b height="10">
						  <td id="" ></td>
						  <td align="center"  ><?   //echo $j; ?></td>
						  <td align="center" ><? //echo $row[csf('bundle_no')]; ?></td>
                          <td align="center" ><? //echo $row[csf('bundle_no')]; ?></td>
						  <td align="right" ><input type="hidden" id="hidden_total_qc_qty_<? echo $j; ?>"
						   name="hidden_total_qc_qty_<? echo $j; ?>"  value="<? echo $color_qty;  ?> "/></td>
						  <td align="right" ><input type="hidden" id="hidden_reject_qty_<? echo $j; ?>"
						  name="hidden_reject_qty_<? echo $j; ?>"  value="<? echo $order_color_qty[$val[csf("order_id")]][$val[csf("gmt_item_id")]][$val[csf('country_id')]][$val[csf("color_id")]]['reject_qty'];  ?>" />
                          <input type="hidden" id="hidden_replace_qty_<? echo $j; ?>"
						  name="hidden_replace_qty_<? echo $j; ?>"  value="<? echo $order_color_qty[$val[csf("order_id")]][$val[csf("gmt_item_id")]][$val[csf('country_id')]][$val[csf("color_id")]]['replace_qty'];  ?>" />

                          </td>
						  <td align="right" >Total</td>
						  <td  align="right" id="total_reject_qty_<? echo $j; ?>"><? echo $order_color_qty[$val[csf("order_id")]][$val[csf("gmt_item_id")]][$val[csf('country_id')]][$val[csf("color_id")]]['reject_qty'];  ?></td>
                          <td  align="right" id="total_replace_qty_<? echo $j; ?>"> <? echo $order_color_qty[$val[csf("order_id")]][$val[csf("gmt_item_id")]][$val[csf('country_id')]][$val[csf("color_id")]]['replace_qty'];  ?></td>
						  <td align="right" id="total_qc_qty_<? echo $j; ?>"><? echo $order_color_qty[$val[csf("order_id")]][$val[csf("gmt_item_id")]][$val[csf('country_id')]][$val[csf("color_id")]]['qc_pass_qty'];  ?>
						  </td>
				 </tr>
			 <?
		    $j++;
	   }
	?>
          <tfoot>
             <tr class="general"  height="15" >
                  <th width="480"  align="right"  colspan="7"> Grand Total</th>
                  <th width="60"  align=" right" id="grand_reject_qty"><? echo $total_qty['reject_qty'];  ?> </th>
                  <th width="60"  align=" right" id="grand_replace_qty"> <? echo $total_qty['replace_qty'];  ?> </th>
                  <th  width="60" align="right" id="grand_qc_qty"><? echo $total_qty['qc_pass_qty'];  ?> </th>

             </tr>
         </tfoot>
      </table>
      </div>
         <table width="800" cellpadding="0" cellspacing="2" align="center">
               <tr>
                   <td colspan="7" align="center" class="">
                        <?
                           echo load_submit_buttons( $permission, "fnc_cut_qc_info", 1,0,"reset_form('','','','','clear_tr()')",1);
                        ?>
                         <input class="formbutton" value="Print" onClick="fnc_cut_qc_info(5)" style="width:80px;" type="button">
						 <input class="formbutton" value="Print2" onClick="fnc_cut_qc_info(7)" style="width:80px;" type="button">
						 <input class="formbutton" value="Print3" onClick="fnc_cut_qc_info(8)" style="width:80px;" type="button">
						 <input class="formbutton" value="Print4" onClick="fnc_cut_qc_info(9)" style="width:80px;" type="button">
						 <input class="formbutton" value="Reject Qnty Challan" onClick="fnc_cut_qc_info(6)" style="width:150px;" type="button">
                        </td>
              </tr>
            </table>
        <input type="hidden" id="total_order_id" name="total_order_id" value="<? echo $j-1; ?>"  />
 <?
}

if ($action == "print_reject_report") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$cbo_cutting_company = $data[0];
	$cbo_company_name = $data[1];
	$update_id = $data[2];
	$cbo_source = $data[3];
	$report_title = $data[4];
	$cutting_no = $data[5];
	


	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$location_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
	$size_arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$batch_lib_arr = return_library_array("select id, batch_no from pro_batch_create_mst", 'id', 'batch_no');
	$party_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$order_array = return_library_array("select id, order_no from subcon_ord_dtls", "id", "order_no");
	$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");

	?>
	<div>
	<table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1000" align="left" style="text-align:center;">
		<tr>
			<td rowspan="3" width="75" height="75" style="border: 0px solid;">
				<!-- <img src="../../images/jk.png" width="75" height="75"> -->
				<img src="../../<? echo $image_location; ?>" width="75" height="75">
			</td>
	    	<td style="border-color:#FFF;font-size:x-large;padding-right: 75px;"><strong><? echo $company_library[$data[0]]; ?></strong></td></td>
	     </tr>
	    	<tr>
	        <td colspan="2" style="border-color:#FFF;padding-right: 75px;">
				<?
					echo  show_company($data[0],'','');
				//cutting_qc_reject_type
				/*$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
				foreach ($nameArray as $result) {
				?>
					Plot No: <? echo $result[csf('plot_no')]; ?>
					Level No: <? echo $result[csf('level_no')]; ?>
					Road No: <? echo $result[csf('road_no')]; ?>
					Block No: <? echo $result[csf('block_no')]; ?>
					Zip Code: <? echo $result[csf('zip_code')];
				}*/
				?>
	        </td>
	      </tr>
	      <tr>
	        <td colspan="2" style="border: 0px solid; padding-right: 75px;"><strong>100% CUT PANAL CHECK REPORT(CUTTING SECTION)</strong></td>
	     </tr>
	</table>
	</div>
	<?
		$order_number_arr=return_library_array( "select id, po_number from   wo_po_break_down",'id','po_number');



		 $sql_mst_cutting="SELECT a.id,a.company_id,a.wo_order_no,a.serving_company,a.cutting_no,a.cutting_qc_no,a.job_no,a.entry_date,a.remarks,a.production_source,b.color_id,b.size_id
	from pro_gmts_cutting_qc_mst a, pro_gmts_cutting_qc_dtls b  where a.id=b.mst_id and a.company_id=$cbo_company_name and a.serving_company=$cbo_cutting_company and a.id=$update_id and a.status_active=1 and a.is_deleted=0 group by  a.id,a.company_id,a.wo_order_no,a.serving_company,a.cutting_no,a.cutting_qc_no,a.job_no,a.entry_date,a.remarks,a.production_source,b.color_id,b.size_id order by b.size_id";
		$dataArray_cut=sql_select($sql_mst_cutting);
		$sql_dtls_lay=sql_select("SELECT b.id,b.cutting_no,c.order_id,a.ship_date,a.color_id,a.gmt_item_id,a.plies,a.marker_qty,a.order_qty,a.total_lay_qty,
			a.lay_balance_qty,a.batch_id, b.batch_id as mst_batch,b.job_no,b.job_year,b.company_id ,a.id as details_id from ppl_cut_lay_dtls a, ppl_cut_lay_mst b,ppl_cut_lay_bundle c
			where b.id=a.mst_id and b.cutting_no='".$cutting_no."' and b.id=c.mst_id and a.id=c.dtls_id
			group by b.id,b.cutting_no,c.order_id,a.ship_date,a.color_id,a.gmt_item_id,a.plies,a.marker_qty,a.order_qty,a.total_lay_qty,
			a.lay_balance_qty,a.batch_id,b.batch_id,b.job_no,b.job_year,b.company_id ,a.id order by a.id");
				

		$order_ids="";$batch_ids="";$marker_qty=0;$job_no_lay="";$batch_mst_id="";
		foreach( $sql_dtls_lay as $row )
		{

			$job_no_lay=$row[csf('job_no')];
			$marker_qty+=$row[csf('marker_qty')];
			$order_ids.= $order_number_arr[$row[csf('order_id')]].',';
			$batch_ids.= $batch_lib_arr[$row[csf('batch_id')]].',';
			$batch_mst_id=$row[csf('mst_batch')];
		}
		$sql_style_buyer=sql_select("select job_no,style_ref_no,buyer_name from wo_po_details_master where company_name=$cbo_company_name and job_no='$job_no_lay'");
		foreach( $sql_style_buyer as $rows )
		{
		  $data_wo_dtls_arr[$rows[csf('job_no')]]['style_ref_no']=$rows[csf('style_ref_no')];
		  $data_wo_dtls_arr[$rows[csf('job_no')]]['buyer_name']=$rows[csf('buyer_name')];
		}

		$sql_size_qty=sql_select("select c.size_id,sum(c.size_qty) as size_qty from  ppl_cut_lay_mst b,ppl_cut_lay_bundle c
		where b.cutting_no='".$cutting_no."' and b.id=c.mst_id group by c.size_id");


		foreach( $sql_size_qty as $dataRow )
		{
		  $data_sizeQty_arr[$dataRow[csf('size_id')]]['size_qty']=$dataRow[csf('size_qty')];
		}

		$cut_qc_arr=sql_select("SELECT sum(b.production_qnty) as qnty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=1 and b.production_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.cut_no='$cutting_no' ");
		$qc_qnty=$cut_qc_arr[0][csf("qnty")];

	?>


	<div>
	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1000" align="left">
	<caption><h2><? //echo $color_size_sql[0][csf('style_ref_no')] ;?> </h2></caption>
		<tr>
	    	<td width="150">Company Name</td>
	        <td width="150"><? echo $company_library[$cbo_company_name]; ?></td>
	        <td>Source:</td>
	     	<td width="150"><? echo $knitting_source[$dataArray_cut[0][csf('production_source')]]; ?></td>
	        <td width="150">CUTTING NO:</td>
	        <td width="150"><? echo $dataArray_cut[0][csf('cutting_no')]; ?></td>
	    </tr>
	    <tr>
	    	<td>WORKING FACTORY:</td>
	        <td><? echo $company_library[$dataArray_cut[0][csf('serving_company')]]; ?></td>
	        <td width="150">Date:</td>
	        <td width="150"><? echo change_date_format($dataArray_cut[0][csf('entry_date')],"dd-mm-yyyy");; ?></td>
	        <td>CUT QTY</td>
	        <td><? echo $marker_qty;  ?></td>
	    </tr>
	    <tr>
	    	<td>BUYER:</td>
	        <td><? echo  $buyer_library[$data_wo_dtls_arr[$dataArray_cut[0][csf('job_no')]]['buyer_name']]; ?></td>
	        <td>COLOR:</td>
	        <td><? echo $color_arr[$dataArray_cut[0][csf('color_id')]]; ?> </td>
	        <td>CUTTING QC.NO:</td>
	        <td><? echo $dataArray_cut[0][csf('cutting_qc_no')]; ?></td>
	    </tr>
	    <tr>
	    	<td>Style No:</td>
	        <td><? echo  $data_wo_dtls_arr[$dataArray_cut[0][csf('job_no')]]['style_ref_no']; ?></td>
	        <td>BATCH NO:</td>
	        <td><?
		        if ($batch_ids==0) {
		        	$batch=$batch_mst_id;
		        }else{
		         	$batch=chop($batch_ids,',');
			    } 
			    echo $batch;
		     ?></td>
	        
	        <td>Remarks</td>
	        <td><? echo $dataArray_cut[0][csf('remarks')]; ?></td>
	    </tr>
	    <tr>
	    	<td>ORD No: </td>
	        <td><? echo chop($order_ids,','); ?></td>
	        <td>Total QC Qty:</td>
	        <td><? echo $qc_qnty; ?></td>
	        <td>WO No</td>
	        <td><? echo $dataArray_cut[0][csf('wo_order_no')]; ?></td>
	    </tr>
	</table>
	</div>
	<div>
	<?
	$sql_dtls_defect=sql_select("select b.defect_point_id,sum(b.defect_qty) as defect_qty,d.size_number_id  as col_size from pro_garments_production_mst a, pro_gmts_prod_dft b ,pro_garments_production_dtls c,wo_po_color_size_breakdown d where a.id=b.mst_id and a.cut_no='".$cutting_no."' and c.cut_no='".$cutting_no."' and b.po_break_down_id=d.po_break_down_id
	and a.po_break_down_id=d.po_break_down_id and c.color_size_break_down_id=d.id
	and a.company_id=$cbo_company_name and b.bundle_no=c.barcode_no and c.mst_id = a.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0  and b.status_active=1  and a.production_type=1 and c.production_type=1
	group by b.defect_point_id ,d.size_number_id   order by b.defect_point_id");

	$data_dft_arr=array();
	foreach( $sql_dtls_defect as $datas )
		{

		  $data_dft_arr[$datas[csf('defect_point_id')]][$datas[csf('col_size')]]['defect_qty']+=$datas[csf('defect_qty')];
		  $data_dft_arr_size[$datas[csf('col_size')]]['defect_qty']+=$datas[csf('defect_qty')];
		  $data_dfts[$datas[csf('defect_point_id')]]=$datas[csf('defect_point_id')];
		}


	?>
	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1000" align="left">
	<caption><h2><? //echo $color_size_sql[0][csf('style_ref_no')] ;?> </h2></caption>
		<thead>
	        <tr bgcolor="#95B3D7">
	           <th width="80" colspan="2"></th>
	            <th width="300" colspan="<? echo count($dataArray_cut); ?>">Size</th>
	            <th width="60" colspan="4"></th>

	       </tr>
	     </thead>
	     <thead>
	        <tr bgcolor="#95B3D7">
				<th width="30">SL</th>
				<th width="150">DEFECT NAME</th>
	            <?
	            foreach( $dataArray_cut as $sizeID )
				{
	            ?>
					<th width="30"><? echo $size_arr_library[$sizeID[csf('size_id')]];  ?></th>
	            <?
	            }
				?>

				<th width="100">TOTAL DEFECT</th>
				<th width="100">DEFECT %</th>
				<th>REMARKS</th>
			</tr>
	    </thead>
	    <tbody>
	    	 <?
			 //$grand_total_defect_qty=0;$grand_totalCut_qty=0;
			 $i=1;
			 $gr_total_defect_qty=0;
			 foreach($data_dfts as $key=>$row)
			 {
			?>
				<tr>
	            	<td><? echo $i; ?></td>
	                <td><? echo $cutting_qc_reject_type[$data_dfts[$key]]; ?></td>
	                 <?
						foreach( $dataArray_cut as $sizeID )
						{
						?>
							<td align="center"><?
							echo $data_dft_arr[$key][$sizeID[csf('size_id')]]['defect_qty'];
							$total_defect_qty+=$data_dft_arr[$key][$sizeID[csf('size_id')]]['defect_qty'];
							$gr_total_defect_qty+=$total_defect_qty;
							?>
							</td>
						<?
						}
					?>
	                <td align="center"><? echo $total_defect_qty; ?></td>
	                <td align="center"><?  $defect_percent=($total_defect_qty/$qc_qnty)*100; echo number_format($defect_percent,2).'%'; ?></td>
	                <td></td>
	            </tr>

			<?
			$i++;
			unset($total_defect_qty);
			 }
			 ?>
	    </tbody>
	    <tfoot>
	    		<tr bgcolor="#95B3D7" align="center">
	                <td colspan="2">TOTAL DEFECT</td>
	                 <?
	                 	$gr_defect=0;
			            foreach( $dataArray_cut as $sizeID )
						{
							$gr_defect+=$data_dft_arr_size[$sizeID[csf('size_id')]]['defect_qty'];
			            ?>
			             <td><? echo $data_dft_arr_size[$sizeID[csf('size_id')]]['defect_qty']; ?></td>
			            <?
			            }
					?>
	                <td align="center"><? echo $gr_defect; ?></td>
	                <td align="center"><? echo $deft_perc= number_format(($gr_defect/$qc_qnty)*100,2).'%';?></td>
	                <td></td>
	            </tr>
	            <tr bgcolor="#BFBFBF" align="center">
	                <td colspan="2">TOTAL CUT QTY</td>
	                 <?
	                 	$totalCutQnty = 0;
			            foreach( $dataArray_cut as $sizeID )
						{
			            ?>
			             <td><? echo  $grand_totalCut_qty=$data_sizeQty_arr[$sizeID[csf('size_id')]]['size_qty']; ?></td>
						 <?
						 $totalCutQnty += $grand_totalCut_qty;
			            }
					?>
	                <td><?php echo $totalCutQnty;?></td>
	                <td></td>
	                <td></td>
	            </tr>
	            <tr bgcolor="#E6B9B8" align="center">
	                <td colspan="2">SIZE WISE DEFECT %</td>
	               <?
			            foreach( $dataArray_cut as $sizeID )
						{
			            ?>
			             <td><?
						 echo number_format(($data_dft_arr_size[$sizeID[csf('size_id')]]['defect_qty']/$data_sizeQty_arr[$sizeID[csf('size_id')]]['size_qty'])*100,3).'%';
						 ?></td>
						 <?
			            }
					?>
	                <td></td>
	                <td></td>
	                <td></td>
	            </tr>
	    </tfoot>
	</table>
	<br>
	<?
	echo signature_table(244, $cbo_company_name, "1060px");
	?>
	</div>

	<?
}

if ($action == "print2_reject_report") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$cbo_cutting_company = $data[0];
	$cbo_company_name = $data[1];
	$update_id = $data[2];
	$cbo_source = $data[3];
	$report_title = $data[4];
	$cutting_no = $data[5];


	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$location_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
	$size_arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$batch_lib_arr = return_library_array("select id, batch_no from pro_batch_create_mst", 'id', 'batch_no');
	$party_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$order_array = return_library_array("select id, order_no from subcon_ord_dtls", "id", "order_no");
	$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$floor_library=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name");
	$table_library=return_library_array( "select id, table_name from  lib_table_entry", "id", "table_name");
    
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	?>
	<div>
	<table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1000" align="left" style="text-align:center;">
		<tr>
			<td rowspan="3" width="75" height="75" style="border: 0px solid;">
				<!-- <img src="../../images/jk.png" width="75" height="75"> -->
				<img src="../../<? echo $image_location; ?>" height="50" width="60">
			</td>
	    	<td style="border-color:#FFF;font-size:x-large;padding-right: 75px;"><strong><? echo $company_library[$data[0]]; ?></strong></td></td>
	     </tr>
	    	<tr>
	        <td colspan="2" style="border-color:#FFF;padding-right: 75px;">
				<?
					echo  show_company($data[0],'','');
				//cutting_qc_reject_type
				/*$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
				foreach ($nameArray as $result) {
				?>
					Plot No: <? echo $result[csf('plot_no')]; ?>
					Level No: <? echo $result[csf('level_no')]; ?>
					Road No: <? echo $result[csf('road_no')]; ?>
					Block No: <? echo $result[csf('block_no')]; ?>
					Zip Code: <? echo $result[csf('zip_code')];
				}*/
				?>
	        </td>
	      </tr>
	      <tr>
	        <td colspan="2" style="border: 0px solid; padding-right: 75px;"><strong>100% CUT PANAL CHECK REPORT(CUTTING SECTION)</strong></td>
	     </tr>
	</table>
	</div>
	<?
		$order_number_arr=return_library_array( "select id, po_number from   wo_po_break_down",'id','po_number');



		 $sql_mst_cutting="select a.id,a.company_id,a.serving_company,a.cutting_no,a.cutting_qc_no,a.job_no,a.entry_date,a.remarks,a.production_source,b.color_id,b.size_id
	from pro_gmts_cutting_qc_mst a, pro_gmts_cutting_qc_dtls b  where a.id=b.mst_id and a.company_id=$cbo_company_name and a.serving_company=$cbo_cutting_company and a.id=$update_id and a.status_active=1 and a.is_deleted=0 group by  a.id,a.company_id,a.serving_company,a.cutting_no,a.cutting_qc_no,a.job_no,a.entry_date,a.remarks,a.production_source,b.color_id,b.size_id order by b.size_id";
		$dataArray_cut=sql_select($sql_mst_cutting);

		$sql_dtls_lay=sql_select("SELECT b.id,b.cutting_no,c.order_id,a.ship_date,a.color_id,a.gmt_item_id,a.plies,a.marker_qty,a.order_qty,a.total_lay_qty,
			a.lay_balance_qty,a.batch_id,b.job_no,b.job_year,b.company_id ,a.id as details_id from ppl_cut_lay_dtls a, ppl_cut_lay_mst b,ppl_cut_lay_bundle c
			where b.id=a.mst_id and b.cutting_no='".$cutting_no."' and b.id=c.mst_id and a.id=c.dtls_id
			group by b.id,b.cutting_no,c.order_id,a.ship_date,a.color_id,a.gmt_item_id,a.plies,a.marker_qty,a.order_qty,a.total_lay_qty,
			a.lay_balance_qty,a.batch_id,b.job_no,b.job_year,b.company_id ,a.id order by a.id");
          
		$order_ids="";$batch_ids="";$marker_qty=0;$job_no_lay="";
		foreach( $sql_dtls_lay as $row )
		{

			$job_no_lay=$row[csf('job_no')];
			$marker_qty+=$row[csf('marker_qty')];
			$order_ids.= $order_number_arr[$row[csf('order_id')]].',';
			$batch_ids.= $batch_lib_arr[$row[csf('batch_id')]].',';
		}
		$sql_style_buyer=sql_select("select job_no,style_ref_no,buyer_name from wo_po_details_master where company_name=$cbo_company_name and job_no='$job_no_lay'");
		foreach( $sql_style_buyer as $rows )
		{
		  $data_wo_dtls_arr[$rows[csf('job_no')]]['style_ref_no']=$rows[csf('style_ref_no')];
		  $data_wo_dtls_arr[$rows[csf('job_no')]]['buyer_name']=$rows[csf('buyer_name')];
		}
        
		$cut_floor_lay_arr=array();
		$cut_floor_sql="SELECT a.cutting_no,a.floor_id,c.table_no,b.job_no_mst,b.grouping FROM ppl_cut_lay_mst a,wo_po_break_down b, pro_garments_production_mst c WHERE a.cutting_no= '".$cutting_no."' AND c.production_type=1 AND a.cutting_no = c.cut_no AND a.job_no=b.job_no_mst AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0";
		// echo $cut_floor_sql; die;
		$cut_floor_sql_data=sql_select($cut_floor_sql);
		foreach($cut_floor_sql_data as $row)
		{
			$cut_floor_lay_arr[$row[csf('cutting_no')]][$row[csf('floor_id')]]=$row[csf('floor_id')];
			$cut_floor_lay_arr[$row[csf('cutting_no')]][$row[csf('table_no')]]=$row[csf('table_no')];
			$cut_floor_lay_arr[$row[csf('job_no_mst')]][$row[csf('grouping')]]=$row[csf('grouping')];
		}
		// echo "<pre>";
		// print_r($cut_floor_lay_arr);
		
		$sql_size_qty=sql_select("select c.size_id,sum(c.size_qty) as size_qty from  ppl_cut_lay_mst b,ppl_cut_lay_bundle c
		where b.cutting_no='".$cutting_no."' and b.id=c.mst_id group by c.size_id");
		
		foreach( $sql_size_qty as $dataRow )
		{
		  $data_sizeQty_arr[$dataRow[csf('size_id')]]['size_qty']=$dataRow[csf('size_qty')];
		}

		$cut_qc_arr=sql_select("SELECT sum(b.production_qnty) as qnty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=1 and b.production_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.cut_no='$cutting_no' ");
		$qc_qnty=$cut_qc_arr[0][csf("qnty")];

	?>


	<div>
	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1000" align="left">
	<caption><h2><? //echo $color_size_sql[0][csf('style_ref_no')] ;?> </h2></caption>
		<tr>
	    	<td width="150"><b>Company Name:</b> </td>
	        <td width="150"><? echo $company_library[$cbo_company_name]; ?></td>
	        <td><b>Source:</b></td>
	     	<td width="150"><? echo $knitting_source[$dataArray_cut[0][csf('production_source')]]; ?></td>
			<td width="150"><b>Date:</b></td>
	        <td width="150"><? echo change_date_format($dataArray_cut[0][csf('entry_date')],"dd-mm-yyyy");; ?></td>
	    </tr>
	    <tr>
	    	<td><b>Working Company:</b></td>
	        <td><? echo $company_library[$dataArray_cut[0][csf('serving_company')]]; ?></td>
			<td><b>Cutting Floor:</b></td>
	        <td><? echo $floor_library[$cut_floor_lay_arr[$row[csf('cutting_no')]][$row[csf('floor_id')]]]; ?></td>
			<td width="150"><b>Cutting No:</b></td>
	        <td width="150"><? echo $dataArray_cut[0][csf('cutting_no')]; ?></td>
	    </tr>
	    <tr>
	    	<td><b>Buyer:</b></td>
	        <td><? echo  $buyer_library[$data_wo_dtls_arr[$dataArray_cut[0][csf('job_no')]]['buyer_name']]; ?></td>
			<td><b>Cutting Table:</b></td>
	        <td><? echo $table_library[$cut_floor_lay_arr[$row[csf('cutting_no')]][$row[csf('table_no')]]]; ?></td>
	        <td><b>Cutting QC.No:</b></td>
	        <td><? echo $dataArray_cut[0][csf('cutting_qc_no')]; ?></td>
	    </tr>
		<tr>
	    	<td><b>Job No:</b></td>
	        <td> <? echo $dataArray_cut[0][csf('job_no')]; ?> </td>
	        <td><b>Int. ref. No. :</b></td>
	        <td><? echo $cut_floor_lay_arr[$row[csf('job_no_mst')]][$row[csf('grouping')]]; ?> </td>
	        <td><b>Cutting Qty (Pcs):</b></td>
	        <td><b><? echo $marker_qty;  ?></b></td>
	    </tr>
	    <tr>
	    	<td><b>Style No:</b></td>
	        <td><? echo  $data_wo_dtls_arr[$dataArray_cut[0][csf('job_no')]]['style_ref_no']; ?></td>
	        <td><b>BATCH NO:</b></td>
	        <td><? echo chop($batch_ids,','); ?></td>
			<td><b>Total QC Qty (Pcs):</b></td>
	        <td><b><? echo $qc_qnty; ?></b></td>
	    </tr>
	    <tr>
	    	<td><b>Order No:</b></td>
	        <td colspan="5"><? echo chop($order_ids,','); ?></td>
	    </tr>
		<tr>
			<td><b>Color:</b></td>
	        <td colspan="5"><? echo $color_arr[$dataArray_cut[0][csf('color_id')]]; ?> </td>
	    </tr>
		<tr>
			<td><b>Remarks:</b></td>
	        <td colspan="5"><? echo $dataArray_cut[0][csf('remarks')]; ?></td>
	    </tr>
	</table>
	</div>
	<div>
	<?



	$sql_dtls_defect=sql_select("select b.defect_point_id,sum(b.defect_qty) as defect_qty,d.size_number_id  as col_size from pro_garments_production_mst a, pro_gmts_prod_dft b ,pro_garments_production_dtls c,wo_po_color_size_breakdown d where a.id=b.mst_id and a.cut_no='".$cutting_no."' and c.cut_no='".$cutting_no."' and b.po_break_down_id=d.po_break_down_id
	and a.po_break_down_id=d.po_break_down_id and c.color_size_break_down_id=d.id
	and a.company_id=$cbo_company_name and b.bundle_no=c.barcode_no and c.mst_id = a.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0  and b.status_active=1  and a.production_type=1 and c.production_type=1
	group by b.defect_point_id ,d.size_number_id   order by b.defect_point_id");

	$data_dft_arr=array();
	foreach( $sql_dtls_defect as $datas )
		{

		  $data_dft_arr[$datas[csf('defect_point_id')]][$datas[csf('col_size')]]['defect_qty']+=$datas[csf('defect_qty')];
		  $data_dft_arr_size[$datas[csf('col_size')]]['defect_qty']+=$datas[csf('defect_qty')];
		  $data_dfts[$datas[csf('defect_point_id')]]=$datas[csf('defect_point_id')];
		}


	?>
	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1000" align="left">
	<caption><h2><? //echo $color_size_sql[0][csf('style_ref_no')] ;?> </h2></caption>
		<thead>
	        <tr bgcolor="#95B3D7">
	           <th width="80" colspan="2"></th>
	            <th width="300" colspan="<? echo count($dataArray_cut); ?>">Size (Number of Defect) </th>
	            <th width="60" colspan="4"></th>

	       </tr>
	     </thead>
	     <thead>
	        <tr bgcolor="#95B3D7">
				<th width="30">SL</th>
				<th width="150">DEFECT NAME</th>
	            <?
	            foreach( $dataArray_cut as $sizeID )
				{
	            ?>
					<th width="30"><? echo $size_arr_library[$sizeID[csf('size_id')]];  ?></th>
	            <?
	            }
				?>

				<th width="100">TOTAL DEFECT</th>
				<th width="100">DEFECT %</th>
				<th>REMARKS</th>
			</tr>
	    </thead>
	    <tbody>
	    	 <?
			 //$grand_total_defect_qty=0;$grand_totalCut_qty=0;
			 $i=1;
			 $gr_total_defect_qty=0;
			 foreach($data_dfts as $key=>$row)
			 {
			?>
				<tr>
	            	<td><? echo $i; ?></td>
	                <td><? echo $cutting_qc_reject_type[$data_dfts[$key]]; ?></td>
	                 <?
						foreach( $dataArray_cut as $sizeID )
						{
						?>
							<td align="center"><?
							echo $data_dft_arr[$key][$sizeID[csf('size_id')]]['defect_qty'];
							$total_defect_qty+=$data_dft_arr[$key][$sizeID[csf('size_id')]]['defect_qty'];
							$gr_total_defect_qty+=$total_defect_qty;
							?>
							</td>
						<?
						}
					?>
	                <td align="center"><? echo $total_defect_qty; ?></td>
	                <td align="center" title="<?='('.$total_defect_qty.'/'.$marker_qty.')*100';?>">
	                	<?  $defect_percent=($total_defect_qty/$marker_qty)*100; echo number_format($defect_percent,2).'%'; ?>	                		
	                </td>
	                <td></td>
	            </tr>

			<?
			$i++;
			unset($total_defect_qty);
			 }
			 ?>
	    </tbody>
	    <tfoot>
	    		<tr bgcolor="#95B3D7" align="center">
	                <td colspan="2">TOTAL DEFECT</td>
	                 <?
	                 	$gr_defect=0;
			            foreach( $dataArray_cut as $sizeID )
						{
							$gr_defect+=$data_dft_arr_size[$sizeID[csf('size_id')]]['defect_qty'];
			            ?>
			             <td><? echo $data_dft_arr_size[$sizeID[csf('size_id')]]['defect_qty']; ?></td>
			            <?
			            }
					?>
	                <td align="center"><? echo $gr_defect; ?></td>
	                <td align="center" title='<?=$gr_defect."/".$marker_qty."*100";?>'><? echo $deft_perc= number_format(($gr_defect/$marker_qty)*100,2).'%';?></td>
	                <td></td>
	            </tr>
	            <tr bgcolor="#BFBFBF" align="center">
	                <td colspan="2">TOTAL CUT QTY</td>
	                 <?
	                 	$totalCutQnty = 0;
			            foreach( $dataArray_cut as $sizeID )
						{
			            ?>
			             <td><? echo  $grand_totalCut_qty=$data_sizeQty_arr[$sizeID[csf('size_id')]]['size_qty']; ?></td>
						 <?
						 $totalCutQnty += $grand_totalCut_qty;
			            }
					?>
	                <td><?php echo $totalCutQnty;?></td>
	                <td></td>
	                <td></td>
	            </tr>
	            <tr bgcolor="#E6B9B8" align="center">
	                <td colspan="2">SIZE WISE DEFECT %</td>
	               <?
			            foreach( $dataArray_cut as $sizeID )
						{
			            ?>
			             <td title='<?=$dft_qty."/".$qc_qty."*100";?>'>
			             <?
						 echo number_format(($data_dft_arr_size[$sizeID[csf('size_id')]]['defect_qty']/$data_sizeQty_arr[$sizeID[csf('size_id')]]['size_qty'])*100,3).'%';
						 ?></td>
						 <?
			            }
					?>
	                <td></td>
	                <td></td>
	                <td></td>
	            </tr>
	    </tfoot>
	</table>
	<br>
	<?
	echo signature_table(244, $cbo_company_name, "1060px");
	?>
	</div>

	<?
}

if ($action == "print_only_reject_report") 
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	$data = explode('*', $data);
	$cbo_cutting_company = $data[0];
	$cbo_company_name = $data[1];
	$update_id = $data[2];
	$cbo_source = $data[3];
	$report_title = $data[4];
	$cutting_no = $data[5];


	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	// $location_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
	$size_arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	// $party_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$order_array = return_library_array("select id, order_no from subcon_ord_dtls", "id", "order_no");
	// $po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");

	$sql_mst_cutting="SELECT a.id,a.company_id,a.serving_company,a.cutting_no,a.cutting_qc_no,a.job_no,a.entry_date,a.remarks,a.production_source,b.color_id,b.size_id,a.cutting_qc_date as qcdate from pro_gmts_cutting_qc_mst a, pro_gmts_cutting_qc_dtls b  where a.id=b.mst_id and a.company_id=$cbo_company_name and a.serving_company=$cbo_cutting_company and a.id=$update_id and a.status_active=1 and a.is_deleted=0 group by  a.id,a.company_id,a.serving_company,a.cutting_no,a.cutting_qc_no,a.job_no,a.entry_date,a.remarks,a.production_source,b.color_id,b.size_id,a.cutting_qc_date order by b.size_id";
	//echo $sql_mst_cutting;
	$dataArray_cut=sql_select($sql_mst_cutting);

	if($db_type==0)
	{
		$order_group="group_concat(distinct(c.order_id)) as order_id";
	}
	else
	{ 
		$order_group="listagg(cast(c.order_id as varchar(4000)),',') within group(order by c.id) as order_id";
	}

	$sql_dtls_lay=sql_select("SELECT b.id,b.cutting_no,$order_group,a.ship_date,a.color_id,a.gmt_item_id,a.plies,a.marker_qty,a.order_qty,a.total_lay_qty, a.lay_balance_qty,a.batch_id,b.job_no,b.job_year,b.company_id ,a.id as details_id 
	from ppl_cut_lay_dtls a, ppl_cut_lay_mst b,ppl_cut_lay_bundle c 
	where b.id=a.mst_id and b.cutting_no='".$cutting_no."' and b.id=c.mst_id and a.id=c.dtls_id group by b.id,b.cutting_no,a.ship_date,a.color_id,a.gmt_item_id,a.plies,a.marker_qty,a.order_qty,a.total_lay_qty, a.lay_balance_qty,a.batch_id,b.job_no,b.job_year,b.company_id ,a.id order by a.id");
	//  echo "SELECT b.id,b.cutting_no,$order_group,a.ship_date,a.color_id,a.gmt_item_id,a.plies,a.marker_qty,a.order_qty,a.total_lay_qty, a.lay_balance_qty,a.batch_id,b.job_no,b.job_year,b.company_id ,a.id as details_id from ppl_cut_lay_dtls a, ppl_cut_lay_mst b,ppl_cut_lay_bundle c where b.id=a.mst_id and b.cutting_no='".$cutting_no."' and b.id=c.mst_id and a.id=c.dtls_id group by b.id,b.cutting_no,a.ship_date,a.color_id,a.gmt_item_id,a.plies,a.marker_qty,a.order_qty,a.total_lay_qty, a.lay_balance_qty,a.batch_id,b.job_no,b.job_year,b.company_id ,a.id order by a.id";
	//  echo "<pre>";
	//  var_dump($sql_dtls_lay);

		$order_ids="";$batch_ids="";$marker_qty=0;$job_no_lay="";$item_id=0;
		$batch_id_arr = array();
		$po_id_arr = array();
		foreach( $sql_dtls_lay as $row )
		{
			$order_id_arr=array_unique(explode(",",$row[csf('order_id')]));
			
			foreach ($order_id_arr as $value) {
				$po_id_arr[$value] = $value;
			}
			
			$batch_id_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
		}
		$po_id_cond = where_con_using_array($po_id_arr,0,"id");
		$batch_id_cond = where_con_using_array($batch_id_arr,0,"id");
		$order_number_arr=return_library_array( "SELECT id, po_number from   wo_po_break_down where status_active=1 $po_id_cond",'id','po_number');
		$batch_lib_arr = return_library_array("SELECT id, batch_no from pro_batch_create_mst where status_active=1 $batch_id_cond", 'id', 'batch_no');

		foreach( $sql_dtls_lay as $row )
		{
			$job_no_lay=$row[csf('job_no')];
			$marker_qty+=$row[csf('marker_qty')];
			//$order_ids.= $order_number_arr[$row[csf('order_id')]].',';

			$order_id_arr=array_unique(explode(",",$row[csf('order_id')]));
			foreach ($order_id_arr as $value) {
				$order_ids.= $order_number_arr[$value].',';
			}
			


			$batch_ids.= $batch_lib_arr[$row[csf('batch_id')]].',';
			$item_id = $row[csf('gmt_item_id')];
			$po_id_arr[$row[csf('order_id')]] = [$row[csf('order_id')]];
			$batch_id_arr[$row[csf('batch_id')]] = [$row[csf('batch_id')]];
		}
		$sql_style_buyer=sql_select("SELECT job_no,style_ref_no,buyer_name from wo_po_details_master where company_name=$cbo_company_name and job_no='$job_no_lay'");
		foreach( $sql_style_buyer as $rows )
		{
		  $data_wo_dtls_arr[$rows[csf('job_no')]]['style_ref_no']=$rows[csf('style_ref_no')];
		  $data_wo_dtls_arr[$rows[csf('job_no')]]['buyer_name']=$rows[csf('buyer_name')];
		}

	$cut_qc_arr=sql_select("SELECT sum(b.production_qnty) as qnty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=1 and b.production_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.cut_no='$cutting_no' ");
		$qc_qnty=$cut_qc_arr[0][csf("qnty")];

	// ================= getting reject qty ====================
	$sql = "SELECT c.color_number_id as color_id, c.size_number_id as size_id, b.reject_qty from pro_garments_production_dtls b, wo_po_color_size_breakdown c where b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 and b.cut_no='".$cutting_no."'";
	$sql_res = sql_select($sql);
	$color_size_array = array();
	$size_array = array();
	$color_array = array();
	foreach ($sql_res as $val) 
	{
		$color_size_qty_array[$val[csf('color_id')]][$val[csf('size_id')]] += $val[csf('reject_qty')];
		$size_array[$val[csf('size_id')]] = $val[csf('size_id')];
		$color_array[$val[csf('color_id')]] = $val[csf('color_id')];
	}
	$tbl_width = 250+(count($size_array)*50);
	?>
	<style type="text/css">
		@media all {
		    table.rptTable {
		        border: solid #000 !important;
		        border-width: 0px 0 0 0px !important;
		    }
		    table.rptTable th, table.rptTable td {
		        border: solid #000 !important;
		        border-width: 0 0px 0px 0 !important;
		    }
		}
	</style>
	<div style="width: 1000px;">
		<div>
			<table border="0" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1000" align="left" style="text-align:center;">
				<tr>
					<td rowspan="3" width="75" height="75" style="border: 0px solid;">
						<!--img src="../../images/jk.png" width="75" height="75"/-->
                        <img src="../../<? echo $image_location; ?>" width="75" height="75">
					</td>
			    	<td style="border-color:#FFF;font-size:x-large;"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			     </tr>
			    	<tr>
			        <td colspan="2" style="border-color:#FFF;padding-right: 75px;">
						<?
							echo  show_company($data[0],'','');
						?>
			        </td>
			      </tr>
			      <tr>
			        <td colspan="2" style="border: 0px solid; padding-right: 75px;"><strong>Reject Qnty Challan</strong></td>
			     </tr>
			</table>
		</div>
		<br clear="all">
		<div style="margin-top: 20px;">
			<table border="0" cellpadding="0" cellspacing="0" rules="all" width="1000" align="left" class="rptTable">
				<tr>
					<td width="20%">Company Name</td>
					<td width="5%">:</td>
					<td width="25%"><? echo $company_library[$cbo_company_name]; ?></td>
					<td width="20%">Buyer</td>
					<td width="5%">:</td>
					<td width="25%"><? echo  $buyer_library[$data_wo_dtls_arr[$dataArray_cut[0][csf('job_no')]]['buyer_name']]; ?></td>
				</tr>
				<tr>
					<td width="20%">Source</td>
					<td width="5%">:</td>
					<td width="25%"><? echo $knitting_source[$dataArray_cut[0][csf('production_source')]]; ?></td>
					<td width="20%">Style No</td>
					<td width="5%">:</td>
					<td width="25%"><? echo  $data_wo_dtls_arr[$dataArray_cut[0][csf('job_no')]]['style_ref_no']; ?></td>
				</tr>
				<tr>
					<td width="20%">Order No</td>
					<td width="5%">:</td>
					<td width="25%"><? echo chop($order_ids,','); ?></td>
					<td width="20%">Color</td>
					<td width="5%">:</td>
					<td width="25%"><? echo $color_arr[$dataArray_cut[0][csf('color_id')]]; ?></td>
				</tr>
				<tr>
					<td width="20%">Cutting No</td>
					<td width="5%">:</td>
					<td width="25%"><? echo $dataArray_cut[0][csf('cutting_no')]; ?></td>
					<td width="20%">Item Name</td>
					<td width="5%">:</td>
					<td width="25%"><? echo  $garments_item[$item_id]; ?></td>
				</tr>
				<tr>
					<td width="20%">Cutting QC.No</td>
					<td width="5%">:</td>
					<td width="25%"><? echo $dataArray_cut[0][csf('cutting_qc_no')]; ?></td>
					<td width="20%">Cut Qty</td>
					<td width="5%">:</td>
					<td width="25%"><? echo  $marker_qty; ?></td>
				</tr>
				<tr>
					<td width="20%">Batch No</td>
					<td width="5%">:</td>
					<td width="25%"><? echo chop($batch_ids,','); ?></td>
					<td width="20%">Qc Qty</td>
					<td width="5%">:</td>
					<td width="25%"><? echo $qc_qnty; ?></td>
				</tr>
				<tr>
					<td width="20%">Date</td>
					<td width="5%">:</td>
					<td width="25%"><? echo change_date_format($dataArray_cut[0][csf('qcdate')]); ?></td>
					<td width="20%"></td>
					<td width="5%"></td>
					<td width="25%"></td>
				</tr>				
				
			</table>
		</div>
		<br clear="all">
		<div style="margin-top: 20px;">	
			<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $tbl_width;?>" align="left">
				
			     <thead>
			        <tr>
						<th width="30">SL</th>
						<th width="150">Color/Size</th>
			            <?
			            foreach( $size_array as $sizeID )
						{
			            ?>
							<th width="50"><? echo $size_arr_library[$sizeID];  ?></th>
			            <?
			            }
						?>

						<th width="100">Color Total</th>
					</tr>
			    </thead>
			    <tbody>
			    	<?
			    	$i=1;
			    	$size_wise_tot_qty_array = array();
			    	foreach ($color_array as  $val) 
			    	{
			    		?>
			    		<tr>
			    			<td><? echo $i;?></td>
			    			<td><? echo $color_arr[$val];?></td>
			    			<?
			    			$col_total = 0;
				            foreach( $size_array as $sizeID )
							{
					            ?>
									<td align="right" width="50"><? echo $color_size_qty_array[$val][$sizeID];  ?></td>
					            <?
					            $col_total += $color_size_qty_array[$val][$sizeID];
					            $size_wise_tot_qty_array[$sizeID] += $color_size_qty_array[$val][$sizeID];
				            }
							?>
			    			<td align="right"><? echo $col_total;?></td>
			    		</tr>
			    		<?
			    		$i++;
			    	}
			    	?> 

			    </tbody>
			    <tfoot>
			    	<tr>
			    		<th colspan="2" align="right">Total </th>
			    		<?
			    		$stotal = 0;
			            foreach( $size_array as $sizeID )
						{
				            ?>
								<th align="right" width="50"><? echo $size_wise_tot_qty_array[$sizeID];  ?></th>
				            <?
				            $stotal += $size_wise_tot_qty_array[$sizeID];
			            }
						?>
			    		<th align="right"><? echo $stotal;?></th>
			    	</tr>	
			    </tfoot>
			</table>
		</div>
		
		<?
		echo signature_table(244, $cbo_company_name, "1000px");
		?>
	
	</div>
	<?
}

if ($action == "print3_reject_report") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$cbo_cutting_company = $data[0];
	$cbo_company_name = $data[1];
	$update_id = $data[2];
	$cbo_source = $data[3];
	$report_title = $data[4];
	$cutting_no = $data[5];


	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$location_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
	$size_arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$batch_lib_arr = return_library_array("select id, batch_no from pro_batch_create_mst", 'id', 'batch_no');
	$party_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	// $order_array = return_library_array("select id, order_no from subcon_ord_dtls", "id", "order_no");
	// $po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

	?>
	<div>
	<table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1000" align="left" style="text-align:center;">
		<tr>
			<td rowspan="3" width="75" height="75" style="border: 0px solid;">
				<!-- <img src="../../images/jk.png" width="75" height="75"> -->
			</td>
	    	<td style="border-color:#FFF;font-size:x-large;padding-right: 75px;"><strong><? echo $company_library[$data[0]]; ?></strong></td></td>
	     </tr>
	    	<tr>
	        <td colspan="2" style="border-color:#FFF;padding-right: 75px;">
				<?
					echo  show_company($data[0],'','');
				//cutting_qc_reject_type
				/*$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
				foreach ($nameArray as $result) {
				?>
					Plot No: <? echo $result[csf('plot_no')]; ?>
					Level No: <? echo $result[csf('level_no')]; ?>
					Road No: <? echo $result[csf('road_no')]; ?>
					Block No: <? echo $result[csf('block_no')]; ?>
					Zip Code: <? echo $result[csf('zip_code')];
				}*/
				?>
	        </td>
	      </tr>
	      <tr>
	        <td colspan="2" style="border: 0px solid; padding-right: 75px;"><strong>100% CUT PANAL CHECK REPORT(CUTTING SECTION)</strong></td>
	     </tr>
	</table>
	</div>
	<?
		$order_number_arr=return_library_array( "select id, po_number from   wo_po_break_down",'id','po_number');



		 $sql_mst_cutting="select a.id,a.company_id,a.serving_company,a.cutting_no,a.cutting_qc_no,a.job_no,a.entry_date,a.remarks,a.production_source,b.color_id,b.size_id
	from pro_gmts_cutting_qc_mst a, pro_gmts_cutting_qc_dtls b  where a.id=b.mst_id and a.company_id=$cbo_company_name and a.serving_company=$cbo_cutting_company and a.id=$update_id and a.status_active=1 and a.is_deleted=0 group by  a.id,a.company_id,a.serving_company,a.cutting_no,a.cutting_qc_no,a.job_no,a.entry_date,a.remarks,a.production_source,b.color_id,b.size_id order by b.size_id";
		$dataArray_cut=sql_select($sql_mst_cutting);

		$sql_dtls_lay=sql_select("SELECT b.id,b.cutting_no,c.order_id,a.ship_date,a.color_id,a.gmt_item_id,a.plies,a.marker_qty,a.order_qty,a.total_lay_qty,
			a.lay_balance_qty,a.batch_id,b.job_no,b.job_year,b.company_id ,a.id as details_id from ppl_cut_lay_dtls a, ppl_cut_lay_mst b,ppl_cut_lay_bundle c
			where b.id=a.mst_id and b.cutting_no='".$cutting_no."' and b.id=c.mst_id and a.id=c.dtls_id
			group by b.id,b.cutting_no,c.order_id,a.ship_date,a.color_id,a.gmt_item_id,a.plies,a.marker_qty,a.order_qty,a.total_lay_qty,
			a.lay_balance_qty,a.batch_id,b.job_no,b.job_year,b.company_id ,a.id order by a.id");

		$order_ids="";$batch_ids="";$marker_qty=0;$job_no_lay="";
		foreach( $sql_dtls_lay as $row )
		{

			$job_no_lay=$row[csf('job_no')];
			$marker_qty+=$row[csf('marker_qty')];
			$order_ids.= $order_number_arr[$row[csf('order_id')]].',';
			$batch_ids.= $batch_lib_arr[$row[csf('batch_id')]].',';
		}
		$sql_style_buyer=sql_select("select job_no,style_ref_no,buyer_name from wo_po_details_master where company_name=$cbo_company_name and job_no='$job_no_lay'");
		foreach( $sql_style_buyer as $rows )
		{
		  $data_wo_dtls_arr[$rows[csf('job_no')]]['style_ref_no']=$rows[csf('style_ref_no')];
		  $data_wo_dtls_arr[$rows[csf('job_no')]]['buyer_name']=$rows[csf('buyer_name')];
		}

        // ========================================= Internal Ref. Query ======================================================
		$data_internal_ref_arr = array();
		$sql_internal_ref=sql_select("select a.job_no,b.job_no_mst,b.grouping from ppl_cut_lay_mst a, wo_po_break_down b where a.job_no=b.job_no_mst and a.cutting_no='".$cutting_no."'");
		foreach( $sql_internal_ref as $rows )
		{
		  $data_internal_ref_arr[$rows[csf('job_no_mst')]]['internal_ref']=$rows[csf('grouping')];
		}

		// echo "<pre>";
		// print_r($data_internal_ref_arr);

		// ========================================= Total Bundle Qnty Query ======================================================

		$bundle_sql="SELECT b.job_no, COUNT(a.id) as total_bundle, b.cutting_no FROM ppl_cut_lay_bundle a, ppl_cut_lay_mst b, ppl_cut_lay_dtls c WHERE a.mst_id = b.id AND a.dtls_id = c.id AND b.id = c.mst_id AND b.cutting_no='".$cutting_no."' AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 GROUP BY 	b.job_no,
		b.cutting_no";
        $bundle_sql_result=sql_select($bundle_sql);	
		$bundle_sql_data_array= array();
		foreach($bundle_sql_result as $res)
		{
			$bundle_sql_data_array[$res[csf('job_no')]][$res[csf('cutting_no')]]['total_bundle']+=$res[csf('total_bundle')];
		}
		// echo "<pre>";
		// print_r($bundle_sql_data_array);

		$sql_size_qty=sql_select("select c.size_id,sum(c.size_qty) as size_qty from  ppl_cut_lay_mst b,ppl_cut_lay_bundle c
		where b.cutting_no='".$cutting_no."' and b.id=c.mst_id group by c.size_id");


		foreach( $sql_size_qty as $dataRow )
		{
		  $data_sizeQty_arr[$dataRow[csf('size_id')]]['size_qty']=$dataRow[csf('size_qty')];
		}

		$cut_qc_arr=sql_select("SELECT sum(b.production_qnty) as qnty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=1 and b.production_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.cut_no='$cutting_no' ");
		$qc_qnty=$cut_qc_arr[0][csf("qnty")];

	?>


	<div>
	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1000" align="left">
	<caption><h2><? //echo $color_size_sql[0][csf('style_ref_no')] ;?> </h2></caption>
		<tr>
	    	<td width="150">Company Name</td>
	        <td width="150"><? echo $company_library[$cbo_company_name]; ?></td>
	        <td>Source:</td>
	     	<td width="150"><? echo $knitting_source[$dataArray_cut[0][csf('production_source')]]; ?></td>
	        <td width="150">CUTTING NO:</td>
	        <td width="150"><? echo $dataArray_cut[0][csf('cutting_no')]; ?></td>
	    </tr>
	    <tr>
	    	<td>WORKING FACTORY:</td>
	        <td><? echo $company_library[$dataArray_cut[0][csf('serving_company')]]; ?></td>
	        <td width="150">Date:</td>
	        <td width="150"><? echo change_date_format($dataArray_cut[0][csf('entry_date')],"dd-mm-yyyy");; ?></td>
	        <td>CUT QTY</td>
	        <td><? echo $marker_qty;  ?></td>
	    </tr>
	    <tr>
	    	<td>BUYER:</td>
	        <td><? echo  $buyer_library[$data_wo_dtls_arr[$dataArray_cut[0][csf('job_no')]]['buyer_name']]; ?></td>
	        <td>COLOR:</td>
	        <td><? echo $color_arr[$dataArray_cut[0][csf('color_id')]]; ?> </td>
	        <td>CUTTING QC.NO:</td>
	        <td><? echo $dataArray_cut[0][csf('cutting_qc_no')]; ?></td>
	    </tr>
	    <tr>
	    	<td>Style No:</td>
	        <td><? echo  $data_wo_dtls_arr[$dataArray_cut[0][csf('job_no')]]['style_ref_no']; ?></td>
	        <td>BATCH NO:</td>
	        <td><? echo chop($batch_ids,','); ?></td>
	        <td>Remarks</td>
	        <td><? echo $dataArray_cut[0][csf('remarks')]; ?></td>
	    </tr>
	    <tr>
	    	<td>ORD No: </td>
	        <td><? echo chop($order_ids,','); ?></td>
	        <td>Total QC Qty:</td>
	        <td><? echo $qc_qnty; ?></td>
	        <td></td>
	        <td></td>
	    </tr>
	    <tr>
	    	<td>Internal Ref: </td>
	        <td><?  echo $data_internal_ref_arr[$rows[csf('job_no_mst')]]['internal_ref']; ?></td>
	        <td>No. of Bundle:</td>
	        <td><?  echo $bundle_sql_data_array[$res[csf('job_no')]][$res[csf('cutting_no')]]['total_bundle']; ?></td>
	        <td></td>
	        <td></td>
	    </tr>
	</table>
	</div>
	<div>
	<?



	$sql_dtls_defect=sql_select("select b.defect_point_id,sum(b.defect_qty) as defect_qty,d.size_number_id  as col_size from pro_garments_production_mst a, pro_gmts_prod_dft b ,pro_garments_production_dtls c,wo_po_color_size_breakdown d where a.id=b.mst_id and a.cut_no='".$cutting_no."' and c.cut_no='".$cutting_no."' and b.po_break_down_id=d.po_break_down_id
	and a.po_break_down_id=d.po_break_down_id and c.color_size_break_down_id=d.id
	and a.company_id=$cbo_company_name and b.bundle_no=c.barcode_no and c.mst_id = a.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0  and b.status_active=1  and a.production_type=1 and c.production_type=1
	group by b.defect_point_id ,d.size_number_id   order by b.defect_point_id");

	$data_dft_arr=array();
	foreach( $sql_dtls_defect as $datas )
		{

		  $data_dft_arr[$datas[csf('defect_point_id')]][$datas[csf('col_size')]]['defect_qty']+=$datas[csf('defect_qty')];
		  $data_dft_arr_size[$datas[csf('col_size')]]['defect_qty']+=$datas[csf('defect_qty')];
		  $data_dfts[$datas[csf('defect_point_id')]]=$datas[csf('defect_point_id')];
		}


	?>
	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1000" align="left">
	<caption><h2><? //echo $color_size_sql[0][csf('style_ref_no')] ;?> </h2></caption>
		<thead>
	        <tr bgcolor="#95B3D7">
	           <th width="80" colspan="2"></th>
	            <th width="300" colspan="<? echo count($dataArray_cut); ?>">Size</th>
	            <th width="60" colspan="4"></th>

	       </tr>
	     </thead>
	     <thead>
	        <tr bgcolor="#95B3D7">
				<th width="30">SL</th>
				<th width="150">DEFECT NAME</th>
	            <?
	            foreach( $dataArray_cut as $sizeID )
				{
	            ?>
					<th width="30"><? echo $size_arr_library[$sizeID[csf('size_id')]];  ?></th>
	            <?
	            }
				?>

				<th width="100">TOTAL DEFECT</th>
				<th width="100">DEFECT %</th>
				<th>REMARKS</th>
			</tr>
	    </thead>
	    <tbody>
	    	 <?
			 //$grand_total_defect_qty=0;$grand_totalCut_qty=0;
			 $i=1;
			 $gr_total_defect_qty=0;
			 foreach($data_dfts as $key=>$row)
			 {
			?>
				<tr>
	            	<td><? echo $i; ?></td>
	                <td><? echo $cutting_qc_reject_type[$data_dfts[$key]]; ?></td>
	                 <?
						foreach( $dataArray_cut as $sizeID )
						{
						?>
							<td align="center"><?
							echo $data_dft_arr[$key][$sizeID[csf('size_id')]]['defect_qty'];
							$total_defect_qty+=$data_dft_arr[$key][$sizeID[csf('size_id')]]['defect_qty'];
							$gr_total_defect_qty+=$total_defect_qty;
							?>
							</td>
						<?
						}
					?>
	                <td align="center"><? echo $total_defect_qty; ?></td>
	                <td align="center"><?  $defect_percent=($total_defect_qty/$qc_qnty)*100; echo number_format($defect_percent,2).'%'; ?></td>
	                <td></td>
	            </tr>

			<?
			$i++;
			unset($total_defect_qty);
			 }
			 ?>
	    </tbody>
	    <tfoot>
	    		<tr bgcolor="#95B3D7" align="center">
	                <td colspan="2">TOTAL DEFECT</td>
	                 <?
	                 	$gr_defect=0;
			            foreach( $dataArray_cut as $sizeID )
						{
							$gr_defect+=$data_dft_arr_size[$sizeID[csf('size_id')]]['defect_qty'];
			            ?>
			             <td><? echo $data_dft_arr_size[$sizeID[csf('size_id')]]['defect_qty']; ?></td>
			            <?
			            }
					?>
	                <td align="center"><? echo $gr_defect; ?></td>
	                <td align="center"><? echo $deft_perc= number_format(($gr_defect/$qc_qnty)*100,2).'%';?></td>
	                <td></td>
	            </tr>
	            <tr bgcolor="#BFBFBF" align="center">
	                <td colspan="2">TOTAL CUT QTY</td>
	                 <?
	                 	$totalCutQnty = 0;
			            foreach( $dataArray_cut as $sizeID )
						{
			            ?>
			             <td><? echo  $grand_totalCut_qty=$data_sizeQty_arr[$sizeID[csf('size_id')]]['size_qty']; ?></td>
						 <?
						 $totalCutQnty += $grand_totalCut_qty;
			            }
					?>
	                <td><?php echo $totalCutQnty;?></td>
	                <td></td>
	                <td></td>
	            </tr>
	            <tr bgcolor="#E6B9B8" align="center">
	                <td colspan="2">SIZE WISE DEFECT %</td>
	               <?
			            foreach( $dataArray_cut as $sizeID )
						{
			            ?>
			             <td><?
						 echo number_format(($data_dft_arr_size[$sizeID[csf('size_id')]]['defect_qty']/$data_sizeQty_arr[$sizeID[csf('size_id')]]['size_qty'])*100,3).'%';
						 ?></td>
						 <?
			            }
					?>
	                <td></td>
	                <td></td>
	                <td></td>
	            </tr>
	    </tfoot>
	</table>
	<br>
	<?
	echo signature_table(244, $cbo_company_name, "1060px");
	?>
	</div>

	<?
}


if($action=="reject_qty_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	$caption_name="";
	//print_r($sew_fin_alter_defect_type);die;

	?>
   <script>
   		function fnc_close()
		{
			var save_string='';
			var total_qty=0;
			$("#tbl_list_search").find('tr').each(function()
			{

				var txtDefectQnty=$(this).find('input[name="txtDefectQnty[]"]').val();
				var txtDefectId=$(this).find('input[name="txtDefectId[]"]').val();
				//var txtDefectUpdateId=$(this).find('input[name="txtDefectUpdateId[]"]').val();

				if(txtDefectQnty*1>0)
				{
					if(save_string=="")
					{
						save_string=txtDefectId+"*"+txtDefectQnty;
						total_qty+=txtDefectQnty*1;
					}
					else
					{
						save_string+="**"+txtDefectId+"*"+txtDefectQnty;
						total_qty+=txtDefectQnty*1;
					}
				}
			});

			$('#actual_reject_infos').val( save_string );
			$('#actual_reject_qty').val(total_qty);

			parent.emailwindow.hide();
		}
   function calculate_reject()
   {
	 var reject_qty=0;
	 $("#tbl_list_search").find('tbody tr').each(function()
		{
			//alert(4);
			var qty =$(this).find('input[name="txtDefectQnty[]"]').val()*1;
			// console.log(Number(qty));
			// reject_qty+=$(this).find('input[name="txtDefectQnty[]"]').val()*1;
			if(!Number.isNaN(qty)){
				reject_qty+=Number(qty);
		}
		
		});
	   $("#reject_qty_td").text(reject_qty);
   }

   </script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
        <form name="defect_1"  id="defect_1" autocomplete="off">
			<? //echo load_freeze_divs ("../../",$permission,1); ?>
            <fieldset style="width:360px;">

			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="350">
            	<thead>
                	<tr><th colspan="3">Reject Record</th></tr>
                	<tr><th width="40">SL</th><th width="150">Reject Name</th><th>No. of Defect</th></tr>
                </thead>
            </table>
            <div style="width:350px; max-height:300px; overflow-y:scroll" id="list_container" align="left">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="320" id="tbl_list_search">
                <tbody>
                    <?

						$explSaveData = explode("**",$actual_infos);

						$defect_dataArray=array();
						foreach($explSaveData as $val)
						{
							$difectVal = explode("*",$val);
							//$defect_dataArray['up_id']=$difectVal[0];
							$defect_dataArray[$difectVal[0]]['defectid']=$difectVal[0];
							$defect_dataArray[$difectVal[0]]['defectQnty']=$difectVal[1];
						}

                        $i=1;
						$total_reject=0;
                        foreach($cutting_qc_reject_type as $id=>$val)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$total_reject+=$defect_dataArray[$id]['defectQnty'];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><? echo $val; ?></td>
                                <td align="center">
                                    <input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>"  onKeyUp="calculate_reject()">
                                    <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                                    <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
					?>
                    </tbody>
                    <tfoot>
                        <tr class="tbl_bottom">
                            <td align="right" colspan="2">Total</td>

                            <td align="right"  id="reject_qty_td" style="padding-right:20px"> <? echo $total_reject; ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
			<table width="320" id="table_id">
				 <tr>
					<td align="center" colspan="3">
						<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                        <input type="hidden" id="actual_reject_infos" />
                         <input type="hidden" id="actual_reject_qty" />
					</td>
				</tr>
			</table>
            </fieldset>
        </form>
        </div>
		<script>
		 setFilterGrid('tbl_list_search',-1);
		</script>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
  <?
}
//Print Button 4 Start
if ($action == "print4_reject_report") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$cbo_cutting_company = $data[0];
	$cbo_company_name = $data[1];
	$update_id = $data[2];
	$cbo_source = $data[3];
	$report_title = $data[4];
	$cutting_no = $data[5];
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$country_library = return_library_array("select id, country_name from lib_country", "id", "country_name");
	$location_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
	$size_arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	
	$batch_lib_arr = return_library_array("select id, batch_no from pro_batch_create_mst", 'id', 'batch_no');
	$party_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$order_array = return_library_array("select id, order_no from subcon_ord_dtls", "id", "order_no");
	$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$floor_library=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name");
	$brand_library=return_library_array( "select id, brand_name from  lib_buyer_brand", "id", "brand_name");
	


	$table_library=return_library_array( "select id, table_name from  lib_table_entry", "id", "table_name");
    
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	?>
	<div>
		<table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1000" align="left" style="text-align:center;">
			<tr>
				<td rowspan="3" width="75" height="75" style="border: 0px solid;">
					<img src="../../<? echo $image_location; ?>" height="50" width="60">
				</td>
				<td style="border-color:#FFF;font-size:x-large;padding-right: 75px;"><strong><? echo $company_library[$data[0]]; ?></strong></td></td>
			</tr>
			<tr>
				<td colspan="2" style="border-color:#FFF;padding-right: 75px;">
					<?echo  show_company($data[0],'','');?>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="border: 0px solid; padding-right: 75px;"><strong>100% CUT PANAL CHECK REPORT(CUTTING SECTION)</strong></td>
			</tr>
		</table>
	</div>
	<?
		$order_number_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
		$sql_mst_cutting="SELECT a.id,a.company_id,a.serving_company,a.cutting_no,a.cutting_qc_no,a.job_no,a.table_no,a.entry_date,a.remarks,a.production_source,b.color_id,b.size_id,c.brand_id,c.fit_id
		from pro_gmts_cutting_qc_mst a, pro_gmts_cutting_qc_dtls b,
		wo_po_details_master c,wo_po_break_down d  
		where a.id=b.mst_id 
		and c.id=d.job_id
		and a.company_id=$cbo_company_name
		and b.order_id=d.id
		and a.job_no=c.job_no
		and a.serving_company=$cbo_cutting_company 
		and a.id=$update_id 
		and a.status_active=1 
		and a.is_deleted=0 
		order by b.size_id";
	//	echo $sql_mst_cutting; die;
		$dataArray_cut=sql_select($sql_mst_cutting);
		$sql_dtls_lay=sql_select("SELECT b.id,b.cutting_no,c.order_id,a.ship_date,a.color_id,a.gmt_item_id,a.plies,a.marker_qty,a.order_qty,a.total_lay_qty,
		a.lay_balance_qty,a.batch_id,b.batch_id as mst_batch,b.job_no,b.job_year,b.company_id ,a.id as details_id 
		from ppl_cut_lay_dtls a, ppl_cut_lay_mst b,ppl_cut_lay_bundle c
		where b.id=a.mst_id and b.cutting_no='".$cutting_no."' and b.id=c.mst_id and a.id=c.dtls_id
		group by b.id,b.cutting_no,c.order_id,a.ship_date,a.color_id,a.gmt_item_id,a.plies,a.marker_qty,a.order_qty,a.total_lay_qty,
		a.lay_balance_qty,a.batch_id,b.batch_id,b.job_no,b.job_year,b.company_id ,a.id order by a.id");
		//print_r( $sql_dtls_lay); 
		$order_ids="";$batch_ids="";$marker_qty=0;$job_no_lay="";$batch_mst_id="";
		foreach( $sql_dtls_lay as $row )
		{

			$job_no_lay=$row[csf('job_no')];
			$marker_qty+=$row[csf('marker_qty')];
			$order_ids.= $order_number_arr[$row[csf('order_id')]].',';
			$batch_ids.= $batch_lib_arr[$row[csf('batch_id')]].',';
			$batch_mst_id=$row[csf('mst_batch')];
		}
		$sql_style_buyer=sql_select("select job_no,style_ref_no,buyer_name from wo_po_details_master where company_name=$cbo_company_name and job_no='$job_no_lay'");
		foreach( $sql_style_buyer as $rows )
		{
		  $data_wo_dtls_arr[$rows[csf('job_no')]]['style_ref_no']=$rows[csf('style_ref_no')];
		  $data_wo_dtls_arr[$rows[csf('job_no')]]['buyer_name']=$rows[csf('buyer_name')];
		}
        
		$cut_floor_lay_arr=array();
		$cut_floor_sql="SELECT a.cutting_no,a.floor_id,c.table_no,b.job_no_mst,b.grouping FROM ppl_cut_lay_mst a,wo_po_break_down b, pro_garments_production_mst c WHERE a.cutting_no= '".$cutting_no."' AND c.production_type=1 AND a.cutting_no = c.cut_no AND a.job_no=b.job_no_mst AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0";
		// echo $cut_floor_sql; die;
		$cut_floor_sql_data=sql_select($cut_floor_sql);
		foreach($cut_floor_sql_data as $row)
		{
			$cut_floor_lay_arr[$row[csf('cutting_no')]][$row[csf('floor_id')]]=$row[csf('floor_id')];
			$cut_floor_lay_arr[$row[csf('cutting_no')]][$row[csf('table_no')]]=$row[csf('table_no')];
			$cut_floor_lay_arr[$row[csf('job_no_mst')]][$row[csf('grouping')]]=$row[csf('grouping')];
		}
		// echo "<pre>";
		// print_r($cut_floor_lay_arr);
		
		$sql_size_qty=sql_select("select c.size_id,sum(c.size_qty) as size_qty from  ppl_cut_lay_mst b,ppl_cut_lay_bundle c
		where b.cutting_no='".$cutting_no."' and b.id=c.mst_id group by c.size_id");
		
		foreach( $sql_size_qty as $dataRow )
		{
		  $data_sizeQty_arr[$dataRow[csf('size_id')]]['size_qty']=$dataRow[csf('size_qty')];
		}

		$cut_qc_arr=sql_select("SELECT sum(b.production_qnty) as qnty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=1 and b.production_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.cut_no='$cutting_no' ");
		$qc_qnty=$cut_qc_arr[0][csf("qnty")];

	?>
	<div>
		<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1200" align="left">
			<caption><h2><? echo $color_size_sql[0][csf('style_ref_no')] ;?> </h2></caption>
			<tr>
				<td width="150"><b>Company Name:</b> </td>
				<td width="150"><? echo $company_library[$cbo_company_name]; ?></td>
				<td><b>Source:</b></td>
				<td width="150"><? echo $knitting_source[$dataArray_cut[0][csf('production_source')]]; ?></td>
				<td width="150"><b>Date:</b></td>
				<td width="150"><? echo change_date_format($dataArray_cut[0][csf('entry_date')],"dd-mm-yyyy");; ?></td>
			</tr>
			<tr>
				<td><b>Working Company:</b></td>
				<td><? echo $company_library[$dataArray_cut[0][csf('serving_company')]]; ?></td>
				<td><b>Cutting Floor:</b></td>
				<td><? echo $floor_library[$cut_floor_lay_arr[$row[csf('cutting_no')]][$row[csf('floor_id')]]]; ?></td>
				<td width="150"><b>Cutting No:</b></td>
				<td width="150"><? echo $dataArray_cut[0][csf('cutting_no')]; ?></td>
			</tr>
			<tr>
				<td><b>Buyer:</b></td>
				<td><? echo  $buyer_library[$data_wo_dtls_arr[$dataArray_cut[0][csf('job_no')]]['buyer_name']]; ?></td>
				<td><b>Cutting Table:</b></td>
				<td><? echo $dataArray_cut[0][csf('table_no')]; ?></td>
				<td><b>Cutting QC.No:</b></td>
				<td><? echo $dataArray_cut[0][csf('cutting_qc_no')]; ?></td>
	    	</tr>
			<tr>
				<td><b>Job No:</b></td>
				<td> <? echo $dataArray_cut[0][csf('job_no')]; ?> </td>
				<td><b>Int. ref. No. :</b></td>
				<td><? echo $cut_floor_lay_arr[$row[csf('job_no_mst')]][$row[csf('grouping')]]; ?> </td>
				<td><b>Cutting Qty (Pcs):</b></td>
				<td><b><? echo $marker_qty;  ?></b></td>
			</tr>
			<tr>
				<td><b>Style No:</b></td>
				<td><? echo  $data_wo_dtls_arr[$dataArray_cut[0][csf('job_no')]]['style_ref_no']; ?></td>
				<td><b>BATCH NO:</b></td>
				<td><?
		        if ($batch_ids==0) {
		        	$batch=$batch_mst_id;
		        }else{
		         	$batch=chop($batch_ids,',');
			    } 
			    echo $batch;
		     ?></td>
				<td><b>Total QC Qty (Pcs):</b></td>
				<td><b><? echo $qc_qnty; ?></b></td>
			</tr>
			<tr>
				<td><b>Order No:</b></td>
				<td colspan="5"><? echo chop($order_ids,','); ?></td>
			</tr>
			<tr>
				<td><b>Brand:</b></td>
				<td colspan=""><? echo $brand_library[$dataArray_cut[0][csf('brand_id')]]; ?> </td>
				<td><b>Color:</b></td>
				<td colspan="3"><? echo $color_arr[$dataArray_cut[0][csf('color_id')]]; ?> </td>
			</tr>
			<tr>
				<td><b>Remarks:</b></td>
				<td colspan="3"><? echo $dataArray_cut[0][csf('remarks')]; ?></td>
				<td><b>Fit:</b></td>
				<td colspan="2"><? echo $fit_list_arr[$dataArray_cut[0][csf('fit_id')]]; ?></td>
			</tr>
			
		</table>
		
	</div>
	<br>
	
		<table align="left" cellspacing="0" width="1200" border="1" rules="all" class="rpt_table" style="margin-top: 10px;">
              <thead bgcolor="#dddddd" align="center">
               
                      <th colspan="8"></th>
                      <th>Bundle</th>
                      <th colspan="2">RMG Number</th>
                      <th colspan="4">QC</th>
                      <th ></th>
              </thead>
              <thead bgcolor="#dddddd" align="center">
                      <th width="50">SL No</th>
                      <th width="90">Cut No</th>
                      <th width="90">Order No</th>
                      <th width="90">Country Name</th>
                      <th width="80">Pattern No</th>
                      <th width="70">Roll No</th>
                      <th width="150">Batch No</th>
                      <th width="80">Bundle No</th>
                      <th width="80">Quantity</th>
                      <th width="80">From</th>
                      <th width="80">To</th>
                      <th width="60">Size</th>
                      <th width="70">REJ</th>
                      <th width="70">REP</th>
                      <th width="120">QC Pass Qty</th>
                      <th width="90">Remarks</th>
					 
                </thead>
				<tbody>
					<?
					$cutting_data_arr=array();
					$sql_mst_cutting="SELECT a.id,a.company_id,a.serving_company,a.cutting_no,a.cutting_qc_no,a.job_no,a.entry_date,a.remarks,a.production_source,a.remarks,b.color_id,b.size_id,b.bundle_no,b.country_id,b.order_id,b.barcode_no,b.number_start,b.number_end,b.bundle_qty,b.qc_pass_qty,b.reject_qty,b.replace_qty,c.brand_id,c.fit_id,d.po_number
					from pro_gmts_cutting_qc_mst a, pro_gmts_cutting_qc_dtls b,
					wo_po_details_master c,wo_po_break_down d  
					where a.id=b.mst_id 
					and c.id=d.job_id
					and a.company_id=$cbo_company_name
					and b.order_id=d.id
					and a.job_no=c.job_no
					and a.serving_company=$cbo_cutting_company 
					and a.id=$update_id 
					and a.status_active=1 
					and a.is_deleted=0 
					order by b.size_id,b.barcode_no";
					//echo $sql_mst_cutting; die;
					$pattren_sql="SELECT barcode_no,roll_no,pattern_no,order_id from ppl_cut_lay_bundle where status_active=1";
					$pattren_data_arr=array();
					$pattren_sql_data=sql_select($pattren_sql);
					foreach ($pattren_sql_data as $value)
					{
						$pattren_data_arr[$value[csf('order_id')]][$value[csf('barcode_no')]]['pattern_no']=$value[csf('pattern_no')];
						$pattren_data_arr[$value[csf('order_id')]][$value[csf('barcode_no')]]['roll_no']=$value[csf('roll_no')];
					
					}

					$sql_mst_cutting_data=sql_select($sql_mst_cutting);
					foreach ($sql_mst_cutting_data as $row) 
					{
						$cutting_data_arr[$row[csf('size_id')]][$row[csf('cutting_no')]][$row[csf('bundle_no')]]['size_id']=$row[csf('size_id')];
						$cutting_data_arr[$row[csf('size_id')]][$row[csf('cutting_no')]][$row[csf('bundle_no')]]['cutting_no']=$row[csf('cutting_no')];
						$cutting_data_arr[$row[csf('size_id')]][$row[csf('cutting_no')]][$row[csf('bundle_no')]]['country_id']=$row[csf('country_id')];
						$cutting_data_arr[$row[csf('size_id')]][$row[csf('cutting_no')]][$row[csf('bundle_no')]]['bundle_no']=$row[csf('bundle_no')];
						$cutting_data_arr[$row[csf('size_id')]][$row[csf('cutting_no')]][$row[csf('bundle_no')]]['bundle_qty']=$row[csf('bundle_qty')];
						$cutting_data_arr[$row[csf('size_id')]][$row[csf('cutting_no')]][$row[csf('bundle_no')]]['qc_pass_qty']=$row[csf('qc_pass_qty')];
						$cutting_data_arr[$row[csf('size_id')]][$row[csf('cutting_no')]][$row[csf('bundle_no')]]['number_start']=$row[csf('number_start')];
						$cutting_data_arr[$row[csf('size_id')]][$row[csf('cutting_no')]][$row[csf('bundle_no')]]['number_end']=$row[csf('number_end')];
						$cutting_data_arr[$row[csf('size_id')]][$row[csf('cutting_no')]][$row[csf('bundle_no')]]['reject_qty']=$row[csf('reject_qty')];
						$cutting_data_arr[$row[csf('size_id')]][$row[csf('cutting_no')]][$row[csf('bundle_no')]]['replace_qty']=$row[csf('replace_qty')];
						$cutting_data_arr[$row[csf('size_id')]][$row[csf('cutting_no')]][$row[csf('bundle_no')]]['remarks']=$row[csf('remarks')];

						$cutting_data_arr[$row[csf('size_id')]][$row[csf('cutting_no')]][$row[csf('bundle_no')]]['po_number']=$row[csf('po_number')];

						// $color_size_summary_arr[$row[csf('color_id')]][$row[csf('size_id')]]=$row[csf('color_id')];
					}

					$i=1;
					foreach ($cutting_data_arr as $size_id => $cut_data) 
					{
						$subtotal_qs_pass_qty=0;
						$subtotal_reject_qty=0;
						$subtotal_replace_qty=0;
						$sub_total_qs_pass_qty=0;
						foreach ($cut_data as $cut_no => $bundle_data) 
						{
							foreach ($bundle_data as $bundle_no => $row) 
								{
									//var_dump($row);
									$bundle_prifix=explode('-',$row['bundle_no']);
									//print_r($row[csf('bundle_no')])
									//echo $row['bundle_no']."Test";

									?>
									<tr>
										<td><?=$i;?></td>
										<td align="center"><?=$row['cutting_no'];?></td>
										<td align="center"><?=$row['po_number'];?></td>
										<td align="center"><?=$country_library[$row['country_id']];?></td>
										<td align="center"><?=$pattren_data_arr[$row['order_id']][$row['barcode_no']]['pattern_no'];?></td>
										<td align="center"><?=$pattren_data_arr[$row['order_id']][$row['barcode_no']]['roll_no'];?></td>
										<td align="center"><?echo chop($batch_ids,','); ?></td>
										<td align="center"><?=$bundle_prifix[3];?></td>
										<td align="center"><?=$row['bundle_qty'];?></td>
										<td align="center"><?=$row['number_start'];?></td>
										<td align="center"><?=$row['number_end'];?></td>
										
										<td align="center"><?=$size_arr_library[$row['size_id']];?></td>
										<td align="center"><?=$row['reject_qty'];?></td>
										<td align="center"><?=$row['replace_qty'];?></td>
										<td align="center"><?
										$qs_pass_qty=($row['bundle_qty']-$row['reject_qty'])+$row['replace_qty'];
										echo $qs_pass_qty;
										?></td>
										<td align="center"><?=$row['remarks'];?></td>
									
									</tr>
									<?	
									$subtotal_qs_pass_qty+=$row['bundle_qty'];
									$subtotal_reject_qty+=$row['reject_qty'];
									$subtotal_replace_qty+=$row['replace_qty'];
									$sub_total_qs_pass_qty+=$qs_pass_qty;
									$i++;

									$color_size_summary_arr[$size_id]["bundle_qty"]+=$row['bundle_qty'];
									$color_size_summary_arr[$size_id]["reject_qty"]+=$row['reject_qty'];
									$color_size_summary_arr[$size_id]["replace_qty"]+=$row['replace_qty'];
									$color_size_summary_arr[$size_id]["qs_pass_qty"]+=$qs_pass_qty;
								}

								?>
								<tr>
										<td align="center" colspan="8"><strong><?echo $size_arr_library[$row['size_id']]." "?>Size Total</strong></td> 
										<td align="center"><strong><?=$subtotal_qs_pass_qty?></strong></td> 

										<td >&nbsp;</td> 
										<td>&nbsp;</td> 
										<td>&nbsp;</td> 
										<td align="center"><strong><?=$subtotal_reject_qty?></strong></td> 
										<td align="center"><strong><?=$subtotal_replace_qty?></strong></td> 
										<td align="center"><strong><?=$sub_total_qs_pass_qty?></strong></td> 
										<td>&nbsp;</td> 
								</tr>
							<?
						}
						$grand_total_qs_pass_qty+=$subtotal_qs_pass_qty;
						$grand_total_reject_qty+=$subtotal_reject_qty;
						$grand_total_replace_qty+=$subtotal_replace_qty;
						$grand_total_sub_total_qs_pass_qty+=$sub_total_qs_pass_qty;

					}
					?>
				</tbody>
				<tfoot>
					<tr bgcolor="#dddddd">
						<td align="center" colspan="8"><strong>Total marker qty.</strong></td>
						<td align="center" ><strong><?=$grand_total_qs_pass_qty?></strong></td>
						<td>&nbsp;</td> 
						<td>&nbsp;</td> 
						<td>&nbsp;</td> 
						<td align="center"><strong><?=$grand_total_reject_qty?></strong></td>
						<td align="center"><strong><?=$grand_total_replace_qty?></strong></td>
						<td align="center"><strong><?=$grand_total_sub_total_qs_pass_qty?></strong></td>
						<td>&nbsp;</td> 
					</tr>
				</tfoot>
		</table>
		<br>

		<table align="left" cellspacing="0" border="1" width="550" rules="all" class="rpt_table" style="margin-top: 10px;">
		<thead>
			<tr>
					<th bgcolor="#dddddd" colspan="8">Size Wise Summary</th>
				</tr>
			<tr>
				<th align="center">SL No.</th>
				<th align="center">Color</th>
				<th align="center">Size</th>
				<th align="center">Cut Qty.</th>
				<th align="center">Rej</th>
				<th align="center">Rep</th>
				<th align="center">QC Pass</th>
				<th align="center">Reject%</th>
			</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					$color_rowspan=count($color_size_summary_arr);
					
					foreach ($color_size_summary_arr as $key => $value) 
					{
						$reject= number_format($value["reject_qty"] / $value["bundle_qty"],8) * 100;
		
						?>
						<tr>

							<td><?=$i;?></td>
							<? if($i==1)
							{
								
								?>
								<td rowspan="<?=$color_rowspan;?>"><?=$color_arr[$dataArray_cut[0][csf('color_id')]];?></td>
								<?
							} ?>

							<td align="center"><?=$size_arr_library[$key];?></td>
							<td align="center"><?=number_format($value["bundle_qty"],0);?></td>
							<td align="center"><?=number_format($value["reject_qty"],0);?></td>
							<td align="center"><?=number_format($value["replace_qty"],0);?></td>
							<td align="center"><?=number_format($value["qs_pass_qty"],0);?></td>
							<td align="center"><? echo $reject ; ?></td>

						
						</tr>
						<?
						$i++;
						$color_total_qs_pass_qty+=$value["bundle_qty"];
						$color_total_reject_qty+=$value["reject_qty"];
						$color_total_replace_qty+=$value["replace_qty"];
						$color_total_qs+=$value["qs_pass_qty"];
						
					}
					?>
				</tbody>
				<tfoot>
					<tr bgcolor="#dddddd" >
						<td align="center" colspan="3"><strong>Total</strong></td>
						<td align="center"><strong><?=$color_total_qs_pass_qty;?></strong></td>
						<td align="center"><strong><?=$color_total_reject_qty;?></strong></td>
						<td align="center"><strong><?=$color_total_replace_qty;?></strong></td>
						<td align="center"><strong><?=$color_total_qs;?></strong></td>
					</tr>
				</tfoot>
		</table>
		<?
		echo signature_table(244, $cbo_company_name, "1060px");
		?>
	<?
}
?>