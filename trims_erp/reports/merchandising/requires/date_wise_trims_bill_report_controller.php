<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location") {	
	echo create_drop_down( 'cbo_location_name', 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );
	exit();
}


if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
    
	// if($data[1]==1) $load_function="fnc_load_party(document.getElementById('cbo_within_group').value)";
	// else $load_function="";
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $data[2], "");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
	}	
	exit();	 
} 



if ($action=="order_popup")
{	
	echo load_html_head_contents("Order Search","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	function js_set_value(id)
	{
		//alert(booking_no); 
		document.getElementById('hidd_booking_data').value=id;
		parent.emailwindow.hide();
	}

	function search_by(val,type)
	{
		if(type==2)
		{
			$('#txt_search_string').val('');
			if(val==1) $('#search_shellect').html('Wo No.');		
			else if(val==2) $('#search_shellect').html('Style Ref');
			else if(val==3) $('#search_shellect').html('Challan No');
			else if(val==4) $('#search_shellect').html('Bill No');
		}
	}
	
</script>
</head>
<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
					<tr>
					   <th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
					</tr>
                    <tr>                	 
                        <th width="120">Company</th>
                        <th width="120">Search By</th>
                        <th width="120" id="search_shellect">WO No</th>
                        <th colspan="2" width="160">Bill Chlln Date</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" id="hidd_booking_data">
                        </th>
                    </tr>                                 
                </thead>
                <tr class="general">
					<td>
					<? 
						echo create_drop_down( "cbo_company_id",120,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.id=$company order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "","","","","","",2);  
					?>                            
					</td>
					<td>
						<?
							$search_by_arr=array(1=>"Wo No",2=>"Style Ref",3=>"Challan No",4=>"Bill no");
							echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "", "",'search_by(this.value,2)',0 );
						?>
                    </td>
					<td id="search_by_td"><input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:70px" />
				    </td>
                    <input name="txt_order_number" type="hidden" id="txt_order_number" class="text_boxes" style="width:90px">
                    <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From"></td>
                    <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To"></td> 
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_order_number').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company;?>+'_'+<? echo $party_name;?>+'_'+<? echo $customer_source;?>+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_search_string').value, 'create_booking_search_list_view', 'search_div', 'date_wise_trims_bill_report_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
                </tr>
            </table>
            <div id="search_div"></div>   
        </form>
    </div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
if($action=="create_booking_search_list_view")
{	
	$data=explode('_',$data);
	// print_r($data);
	$search_by=str_replace("'","",$data[6]);
	$search_str=trim(str_replace("'","",$data[8]));
	$search_type =$data[7];

	$com_buyer_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
	if ($data[3]!=0) $company=" and a.company_id='$data[3]'";
	if ($data[4]!=0) $party=" and a.party_id='$data[4]'";
	if($db_type==0)
	{
		if ($data[1]!="" &&  $data[2]!="") $bill_date = "and a.bill_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	else if($db_type==2)
	{
		if ($data[1]!="" &&  $data[2]!="")  $bill_date= "and a.bill_date between '".change_date_format($data[1], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	} 


	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and b.order_no='$search_str'";
			else if($search_by==2) $search_com_cond="and c.buyer_style_ref='$search_str'";
			else if($search_by==3) $search_com_cond="and d.challan_no='$search_str'";
			else if($search_by==4) $search_com_cond="and a.bill_no='$search_str'";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and b.order_no like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and c.buyer_style_ref like '%$search_str%'";  			
			if ($search_by==3) $search_com_cond=" and d.challan_no like '%$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and a.bill_no like '%$search_str%'"; 
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and b.order_no like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and c.buyer_style_ref like '$search_str%'";  			
			else if ($search_by==3) $search_com_cond=" and d.challan_no like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and a.bill_no like '$search_str%'";
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and b.order_no like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and c.buyer_style_ref like '%$search_str'";  		
			else if ($search_by==3) $search_com_cond=" and d.challan_no like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and a.bill_no like '%$search_str'"; 
		}
	}
	
	
	$sql= "SELECT a.id, a.trims_bill, a.bill_no_prefix, a.bill_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.bill_date, a.received_id, a.order_id, a.bill_no, b.order_no, d.challan_no, c.buyer_style_ref FROM trims_bill_mst a, trims_bill_dtls b, trims_delivery_dtls c,trims_delivery_mst d, subcon_ord_dtls e WHERE a.entry_form = 276 AND a.id = b.mst_id and c.id=b.production_dtls_id and d.id=c.mst_id and e.mst_id=c.received_id and a.status_active=1 and b.status_active=1 $search_com_cond $bill_date  $company group by a.id, a.trims_bill, a.bill_no_prefix, a.bill_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.bill_date, a.received_id, a.order_id, a.bill_no,b.order_no, d.challan_no, c.buyer_style_ref order by a.id DESC";
	// echo $sql; die; 
	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="840" >
        <thead>
            <th width="30">SL</th>
            <th width="100">Work Order No</th>
            <th width="100">Bill No</th>
            <th width="100">Party Name</th>
            <th width="100">Delv Challan No.</th>
            <th width="70">Year</th>
        </thead>
        </table>
        <div style="width:840px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table" id="list_view">
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
				if($row[csf('within_group')]==1){

				     $buyer=$com_buyer_arr[$row[csf('party_id')]];
				}else{
					$buyer=$buyer_arr[$row[csf('party_id')]];
				}
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('order_no')].'_'.$row[csf('currency_id')].'_'.$row[csf('within_group')]; ?>")' style="cursor:pointer">
                    <td width="30"><? echo $i; ?></td>
                    <td width="100" align="center"><? echo $row[csf('order_no')]; ?></td>
                    <td width="100" align="center"><? echo $row[csf('bill_no')]; ?></td>
                    <td width="100" align="center"><? echo $buyer; ?></td>
                    <td width="100" align="center"><? echo $row[csf('trims_bill')]; ?></td>
                    <td width="70"><? echo change_date_format($row[csf('bill_date')]); ?></td>
                </tr>
				<? 
                $i++; 
            } 
            ?>
        </tbody>
    </table>
	<?
	exit();
}



if ($action=="bill_order_popup")
{	
	echo load_html_head_contents("Order Search","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	function js_set_value(id)
	{
		//alert(booking_no); 
		document.getElementById('hidd_booking_data_bill').value=id;
		parent.emailwindow.hide();
	}

	function search_by(val,type)
	{
		if(type==2)
		{
			$('#txt_search_string').val('');
			if(val==1) $('#search_shellect').html('Wo No.');		
			else if(val==2) $('#search_shellect').html('Style Ref');
			else if(val==3) $('#search_shellect').html('Challan No');
			else if(val==4) $('#search_shellect').html('Bill No');
		}
	}
	
</script>
</head>
<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
					<tr>
					   <th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
					</tr>
                    <tr>                	 
                        <th width="120">Company</th>
                        <th width="120">Search By</th>
                        <th width="120" id="search_shellect">WO No</th>
                        <th colspan="2" width="160">Bill Chlln Date</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" id="hidd_booking_data_bill">
                        </th>
                    </tr>                                 
                </thead>
                <tr class="general">
					<td>
					<? 
						echo create_drop_down( "cbo_company_id",120,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.id=$company order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "","","","","","",2);  
					?>                            
					</td>
					<td>
						<?
							$search_by_arr=array(1=>"Wo No",2=>"Style Ref",3=>"Challan No",4=>"Bill no");
							echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "", "",'search_by(this.value,2)',0 );
						?>
                    </td>
					<td id="search_by_td"><input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:70px" />
				    </td>
                    <input name="txt_order_number" type="hidden" id="txt_order_number" class="text_boxes" style="width:90px">
                    <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From"></td>
                    <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To"></td> 
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_order_number').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company;?>+'_'+<? echo $party_name;?>+'_'+<? echo $customer_source;?>+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_search_string').value, 'create_booking_search_bill_list_view', 'search_div', 'date_wise_trims_bill_report_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
                </tr>
            </table>
            <div id="search_div"></div>   
        </form>
    </div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
if($action=="create_booking_search_bill_list_view")
{	
	$data=explode('_',$data);
	// print_r($data);
	$search_by=str_replace("'","",$data[6]);
	$search_str=trim(str_replace("'","",$data[8]));
	$search_type =$data[7];

	$com_buyer_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
	if ($data[3]!=0) $company=" and a.company_id='$data[3]'";
	if ($data[4]!=0) $party=" and a.party_id='$data[4]'";
	if($db_type==0)
	{
		if ($data[1]!="" &&  $data[2]!="") $bill_date = "and a.bill_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	else if($db_type==2)
	{
		if ($data[1]!="" &&  $data[2]!="")  $bill_date= "and a.bill_date between '".change_date_format($data[1], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	} 


	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and b.order_no='$search_str'";
			else if($search_by==2) $search_com_cond="and c.buyer_style_ref='$search_str'";
			else if($search_by==3) $search_com_cond="and d.challan_no='$search_str'";
			else if($search_by==4) $search_com_cond="and a.bill_no='$search_str'";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and b.order_no like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and c.buyer_style_ref like '%$search_str%'";  			
			if ($search_by==3) $search_com_cond=" and d.challan_no like '%$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and a.bill_no like '%$search_str%'"; 
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and b.order_no like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and c.buyer_style_ref like '$search_str%'";  			
			else if ($search_by==3) $search_com_cond=" and d.challan_no like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and a.bill_no like '$search_str%'";
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and b.order_no like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and c.buyer_style_ref like '%$search_str'";  		
			else if ($search_by==3) $search_com_cond=" and d.challan_no like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and a.bill_no like '%$search_str'"; 
		}
	}
	
	
	$sql= "SELECT a.id, a.trims_bill, a.bill_no_prefix, a.bill_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.bill_date, a.received_id, a.order_id, a.bill_no, b.order_no, d.challan_no, c.buyer_style_ref FROM trims_bill_mst a, trims_bill_dtls b, trims_delivery_dtls c,trims_delivery_mst d, subcon_ord_dtls e WHERE a.entry_form = 276 AND a.id = b.mst_id and c.id=b.production_dtls_id and d.id=c.mst_id and e.mst_id=c.received_id and a.status_active=1 and b.status_active=1 $search_com_cond $bill_date  $company group by a.id, a.trims_bill, a.bill_no_prefix, a.bill_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.bill_date, a.received_id, a.order_id, a.bill_no,b.order_no, d.challan_no, c.buyer_style_ref order by a.id DESC";
	// echo $sql; die; 
	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="840" >
        <thead>
            <th width="30">SL</th>
            <th width="100">Work Order No</th>
            <th width="100">Bill No</th>
            <th width="100">Party Name</th>
            <th width="100">Delv Challan No.</th>
            <th width="70">Year</th>
        </thead>
        </table>
        <div style="width:840px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table" id="list_view">
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
				if($row[csf('within_group')]==1){

				     $buyer=$com_buyer_arr[$row[csf('party_id')]];
				}else{
					$buyer=$buyer_arr[$row[csf('party_id')]];
				}
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('bill_no')].'_'.$row[csf('currency_id')].'_'.$row[csf('within_group')]; ?>")' style="cursor:pointer">
                    <td width="30"><? echo $i; ?></td>
                    <td width="100" align="center"><? echo $row[csf('order_no')]; ?></td>
                    <td width="100" align="center"><? echo $row[csf('bill_no')]; ?></td>
                    <td width="100" align="center"><? echo $buyer; ?></td>
                    <td width="100" align="center"><? echo $row[csf('trims_bill')]; ?></td>
                    <td width="70"><? echo change_date_format($row[csf('bill_date')]); ?></td>
                </tr>
				<? 
                $i++; 
            } 
            ?>
        </tbody>
    </table>
	<?
	exit();
}


if ($action=="challan_order_popup")
{	
	echo load_html_head_contents("Order Search","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	function js_set_value(id)
	{
		//alert(booking_no); 
		document.getElementById('hidd_booking_challan').value=id;
		parent.emailwindow.hide();
	}

	function search_by(val,type)
	{
		if(type==2)
		{
			$('#txt_search_string').val('');
			if(val==1) $('#search_shellect').html('Wo No.');		
			else if(val==2) $('#search_shellect').html('Style Ref');
			else if(val==3) $('#search_shellect').html('Challan No');
			else if(val==4) $('#search_shellect').html('Bill No');
		}
	}
	
</script>
</head>
<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
					<tr>
					   <th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
					</tr>
                    <tr>                	 
                        <th width="120">Company</th>
                        <th width="120">Search By</th>
                        <th width="120" id="search_shellect">WO No</th>
                        <th colspan="2" width="160">Bill Chlln Date</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" id="hidd_booking_challan">
                        </th>
                    </tr>                                 
                </thead>
                <tr class="general">
					<td>
					<? 
						echo create_drop_down( "cbo_company_id",120,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.id=$company order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "","","","","","",2);  
					?>                            
					</td>
					<td>
						<?
							$search_by_arr=array(1=>"Wo No",2=>"Style Ref",3=>"Challan No",4=>"Bill no");
							echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "", "",'search_by(this.value,2)',0 );
						?>
                    </td>
					<td id="search_by_td"><input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:70px" />
				    </td>
                    <input name="txt_order_number" type="hidden" id="txt_order_number" class="text_boxes" style="width:90px">
                    <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From"></td>
                    <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To"></td> 
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_order_number').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company;?>+'_'+<? echo $party_name;?>+'_'+<? echo $customer_source;?>+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_search_string').value, 'create_booking_search_challan_list_view', 'search_div', 'date_wise_trims_bill_report_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
                </tr>
            </table>
            <div id="search_div"></div>   
        </form>
    </div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
if($action=="create_booking_search_challan_list_view")
{	
	$data=explode('_',$data);
	// print_r($data);
	$search_by=str_replace("'","",$data[6]);
	$search_str=trim(str_replace("'","",$data[8]));
	$search_type =$data[7];

	$com_buyer_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
	if ($data[3]!=0) $company=" and a.company_id='$data[3]'";
	if ($data[4]!=0) $party=" and a.party_id='$data[4]'";
	if($db_type==0)
	{
		if ($data[1]!="" &&  $data[2]!="") $bill_date = "and a.bill_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	else if($db_type==2)
	{
		if ($data[1]!="" &&  $data[2]!="")  $bill_date= "and a.bill_date between '".change_date_format($data[1], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	} 


	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and b.order_no='$search_str'";
			else if($search_by==2) $search_com_cond="and c.buyer_style_ref='$search_str'";
			else if($search_by==3) $search_com_cond="and d.challan_no='$search_str'";
			else if($search_by==4) $search_com_cond="and a.bill_no='$search_str'";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and b.order_no like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and c.buyer_style_ref like '%$search_str%'";  			
			if ($search_by==3) $search_com_cond=" and d.challan_no like '%$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and a.bill_no like '%$search_str%'"; 
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and b.order_no like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and c.buyer_style_ref like '$search_str%'";  			
			else if ($search_by==3) $search_com_cond=" and d.challan_no like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and a.bill_no like '$search_str%'";
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and b.order_no like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and c.buyer_style_ref like '%$search_str'";  		
			else if ($search_by==3) $search_com_cond=" and d.challan_no like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and a.bill_no like '%$search_str'"; 
		}
	}
	
	$sql= "SELECT a.id, a.trims_bill, a.bill_no_prefix, a.bill_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.bill_date, a.received_id, a.order_id, a.bill_no, b.order_no, d.challan_no, c.buyer_style_ref FROM trims_bill_mst a, trims_bill_dtls b, trims_delivery_dtls c,trims_delivery_mst d, subcon_ord_dtls e WHERE a.entry_form = 276 AND a.id = b.mst_id and c.id=b.production_dtls_id and d.id=c.mst_id and e.mst_id=c.received_id and a.status_active=1 and b.status_active=1 $search_com_cond $bill_date  $company group by a.id, a.trims_bill, a.bill_no_prefix, a.bill_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.bill_date, a.received_id, a.order_id, a.bill_no,b.order_no, d.challan_no, c.buyer_style_ref order by a.id DESC";
	// echo $sql; die; 
	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="840" >
        <thead>
            <th width="30">SL</th>
            <th width="100">Work Order No</th>
            <th width="100">Bill No</th>
            <th width="100">Party Name</th>
            <th width="100">Delv Challan No.</th>
            <th width="70">Year</th>
        </thead>
        </table>
        <div style="width:840px; max-height:470px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table" id="list_view">
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
				if($row[csf('within_group')]==1){

				     $buyer=$com_buyer_arr[$row[csf('party_id')]];
				}else{
					$buyer=$buyer_arr[$row[csf('party_id')]];
				}
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('challan_no')].'_'.$row[csf('currency_id')].'_'.$row[csf('within_group')]; ?>")' style="cursor:pointer">
                    <td width="30"><? echo $i; ?></td>
                    <td width="100" align="center"><? echo $row[csf('order_no')]; ?></td>
                    <td width="100" align="center"><? echo $row[csf('bill_no')]; ?></td>
                    <td width="100" align="center"><? echo $buyer; ?></td>
                    <td width="100" align="center"><? echo $row[csf('challan_no')]; ?></td>
                    <td width="70"><? echo change_date_format($row[csf('bill_date')]); ?></td>
                </tr>
				<? 
                $i++; 
            } 
            ?>
        </tbody>
    </table>
	<?
	exit();
}

if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_id=str_replace("'","", $cbo_company_id);
	$cbo_within_group=str_replace("'","", $cbo_within_group);
	$cbo_section_id=str_replace("'","", $cbo_section_id);
	$cbo_party_name=str_replace("'","", $cbo_party_name);
	$txt_bill_no=str_replace("'","", $txt_bill_no);
	$txt_challan=str_replace("'","", $txt_challan);
	$txt_wo_no=str_replace("'","", $txt_wo_no);
	$hid_wo_id=str_replace("'","", $hid_wo_id);
	$cbo_item_id=str_replace("'","", $cbo_item_id);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);


	if($cbo_company_id){$where_con.=" and a.company_id='$cbo_company_id'";} 
	
	if($cbo_within_group !=0){
		$where_con.=" and a.within_group='$cbo_within_group'";
	} 

	if($cbo_section_id !=0){
		$where_con.=" and b.section='$cbo_section_id'";
	} 

	if($cbo_item_id !=0){
		$where_con.=" and e.item_group='$cbo_item_id'";
	} 
	
	if($cbo_party_name){
		$where_con.=" and a.party_id='$cbo_party_name'";
	} 

	if($txt_date_from!="" and $txt_date_to!="")
	{	
		$where_con.=" and a.bill_date between '$txt_date_from' and '$txt_date_to'";
	}		
	if(trim($txt_wo_no)!="")
	{
			$sql_cond="and b.order_no like '%$txt_wo_no%'";
	}
	if(trim($txt_bill_no)!="")
	{
			$sql_cond="and a.bill_no like '%$txt_bill_no%'";
	}
	
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 ","id","buyer_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 ","id","supplier_name");
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$colorNameArr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
    $size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
	$machine_noArr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0","id","machine_no");
	
	$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1 and con_date=(select max(con_date) as con_date from currency_conversion_rate where currency=2 and status_active=1)" , "conversion_rate" );

	$trims_bill_sql = "select a.id, a.bill_no_prefix_num, a.within_group, a.currency_id, a.party_id, a.exchange_rate,b.size_id, a.bill_date, a.trims_bill, a.bill_no, b.section, b.order_no, b.challan_no, b.quantity, b.bill_rate, b.bill_amount, d.qnty as workoder_qty, e.buyer_buyer, c.mst_id as deli_id, e.item_group, e.mst_id as order_rcv_id from trims_bill_mst a, trims_bill_dtls b, trims_delivery_dtls c, subcon_ord_breakdown d, subcon_ord_dtls e where a.entry_form=276 and a.id=b.mst_id and c.id=b.production_dtls_id and c.break_down_details_id=d.id  and d.mst_id=e.id and e.mst_id=c.received_id and a.company_id='$cbo_company_id' and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $sql_cond $where_con $company order by a.id ASC"; 

		// echo $trims_bill_sql; die;	  
		$result = sql_select($trims_bill_sql);
        $date_array=array();
        $deli_ids=array();
        $order_rcv_ids=array();	
        foreach($result as $row)
        {	
			$deli_ids[$row[csf("deli_id")]]=$row[csf("deli_id")];
			$order_rcv_ids[$row[csf("order_rcv_id")]]=$row[csf("order_rcv_id")];
			
       	 	$date_array[$row[csf('trims_bill')]][$row[csf('size_id')]][$row[csf('party_id')]][$row[csf('order_no')]][$row[csf('item_group')]][$row[csf('challan_no')]]['trims_bill']=$row[csf('trims_bill')];
       	 	$date_array[$row[csf('trims_bill')]][$row[csf('size_id')]][$row[csf('party_id')]][$row[csf('order_no')]][$row[csf('item_group')]][$row[csf('challan_no')]]['currency_id']=$row[csf('currency_id')];
       	 	$date_array[$row[csf('trims_bill')]][$row[csf('size_id')]][$row[csf('party_id')]][$row[csf('order_no')]][$row[csf('item_group')]][$row[csf('challan_no')]]['party_id']=$row[csf('party_id')];
       	 	$date_array[$row[csf('trims_bill')]][$row[csf('size_id')]][$row[csf('party_id')]][$row[csf('order_no')]][$row[csf('item_group')]][$row[csf('challan_no')]]['exchange_rate']=$row[csf('exchange_rate')];
       	 	$date_array[$row[csf('trims_bill')]][$row[csf('size_id')]][$row[csf('party_id')]][$row[csf('order_no')]][$row[csf('item_group')]][$row[csf('challan_no')]]['bill_date']=$row[csf('bill_date')];
       	 	$date_array[$row[csf('trims_bill')]][$row[csf('size_id')]][$row[csf('party_id')]][$row[csf('order_no')]][$row[csf('item_group')]][$row[csf('challan_no')]]['bill_no']=$row[csf('bill_no')];
       	 	$date_array[$row[csf('trims_bill')]][$row[csf('size_id')]][$row[csf('party_id')]][$row[csf('order_no')]][$row[csf('item_group')]][$row[csf('challan_no')]]['order_no']=$row[csf('order_no')];
       	 	$date_array[$row[csf('trims_bill')]][$row[csf('size_id')]][$row[csf('party_id')]][$row[csf('order_no')]][$row[csf('item_group')]][$row[csf('challan_no')]]['challan_no']=$row[csf('challan_no')];
       	 	$date_array[$row[csf('trims_bill')]][$row[csf('size_id')]][$row[csf('party_id')]][$row[csf('order_no')]][$row[csf('item_group')]][$row[csf('challan_no')]]['quantity']+=$row[csf('quantity')];
       	 	$date_array[$row[csf('trims_bill')]][$row[csf('size_id')]][$row[csf('party_id')]][$row[csf('order_no')]][$row[csf('item_group')]][$row[csf('challan_no')]]['bill_rate']=$row[csf('bill_rate')];
       	 	$date_array[$row[csf('trims_bill')]][$row[csf('size_id')]][$row[csf('party_id')]][$row[csf('order_no')]][$row[csf('item_group')]][$row[csf('challan_no')]]['bill_amount']+=$row[csf('bill_amount')];
       	 	$date_array[$row[csf('trims_bill')]][$row[csf('size_id')]][$row[csf('party_id')]][$row[csf('order_no')]][$row[csf('item_group')]][$row[csf('challan_no')]]['workoder_qty']+=$row[csf('workoder_qty')];
       	 	$date_array[$row[csf('trims_bill')]][$row[csf('size_id')]][$row[csf('party_id')]][$row[csf('order_no')]][$row[csf('item_group')]][$row[csf('challan_no')]]['item_group']=$row[csf('item_group')];
       	 	$date_array[$row[csf('trims_bill')]][$row[csf('size_id')]][$row[csf('party_id')]][$row[csf('order_no')]][$row[csf('item_group')]][$row[csf('challan_no')]]['buyer_buyer']=$row[csf('buyer_buyer')];
       	 	$date_array[$row[csf('trims_bill')]][$row[csf('size_id')]][$row[csf('party_id')]][$row[csf('order_no')]][$row[csf('item_group')]][$row[csf('challan_no')]]['order_rcv_id']=$row[csf('order_rcv_id')];
       	 	$date_array[$row[csf('trims_bill')]][$row[csf('size_id')]][$row[csf('party_id')]][$row[csf('order_no')]][$row[csf('item_group')]][$row[csf('challan_no')]]['within_group']=$row[csf('within_group')];
       	 	$date_array[$row[csf('trims_bill')]][$row[csf('size_id')]][$row[csf('party_id')]][$row[csf('order_no')]][$row[csf('item_group')]][$row[csf('challan_no')]]['deli_id']=$row[csf('deli_id')];

       	 	$date_array[$row[csf('trims_bill')]][$row[csf('size_id')]][$row[csf('party_id')]][$row[csf('order_no')]][$row[csf('item_group')]][$row[csf('challan_no')]]['size_id']=$row[csf('size_id')];
			
 		}

 		$deli_ids = array_unique($deli_ids);
 		//$deli_id=implode(',', $deli_ids);
 		$order_rcv_id=implode(',', $order_rcv_ids);
		$deli_id_con=where_con_using_array($deli_ids,0,"a.id");

		$delivery_sql="select a.id, a.delivery_date, b.buyer_buyer from trims_delivery_mst a, trims_delivery_dtls b where  a.id=b.mst_id and a.entry_form=208 $deli_id_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; //and a.id in($deli_id)
        //echo $delivery_sql; die;

        $delivery_sql_arr=sql_select($delivery_sql);
        $delivery_array = array();
        foreach ($delivery_sql_arr as $row) 
        {
            $delivery_array[$row[csf("id")]]['delivery_date']=$row[csf("delivery_date")];
            $delivery_array[$row[csf("id")]]['buyer_buyer']=$row[csf("buyer_buyer")];

        }

	$width=1435;
	ob_start();
	?>	
	<div align="center" style="height:auto; width:<? echo $width+14;?>px; margin:0 auto; padding:0;">
    	<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
				<thead class="form_caption" >
					<tr>
						<td colspan="14" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
					</tr>
					<tr>
						<td colspan="14" align="center" style="font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
					</tr>
					<tr>
						<td colspan="14" align="center" style="font-size:14px; font-weight:bold">
							<? echo " From : ".change_date_format($txt_date_from) ." To : ". change_date_format($txt_date_to) ;?>
						</td>
					</tr>
				</thead>
			</table>
            <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
				<thead>
                    <th width="35">SL</th>
                    <th width="100">Party Name</th>
                    <th width="100">Bill Number</th>
                    <th width="100">Bill Date</th>                  
                    <th width="100">Item Name</th>
                    <th width="100">Item Size</th>
                    <th width="100">Work Order No</th>
                    <th width="100">Challan No</th>
                    <th width="100">Order Qty</th>
                    <th width="100">Bill Qty</th>				
                    <th width="100">Bill Rate[$]</th>
					<th width="100">Bill Amount[$]</th>
                    <th width="100">Bill Rate[TK]</th>
                    <th width="100">Bill Amount[TK]</th>                 
				</thead>
			</table>
        <div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
        	<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $width;?>" rules="all" align="left">
            <? 
				$i=1;
				$total_order_qty=0;$total_bill_qty=0;$total_bill_tk=0;$total_bill_usd=0;
				
				foreach($date_array as $bill_no=>$bill_no_arr)
				{
					foreach($bill_no_arr as $bill_size_id=>$size_id_date_arr)
					{
						foreach($size_id_date_arr as $party_id=>$party_id_arr)
						{
							foreach($party_id_arr as $order_no=>$order_no_data)
							{
								foreach($order_no_data as $item_group=>$item_group_arr)
								{
									foreach($item_group_arr as $challan_no=>$row)
									{

										$bill_quantity=$row['quantity'];								
										$orderquantity=$row['workoder_qty'];
										$bill_amount=$row['bill_amount'];
										$bill_rate=number_format($bill_amount/$bill_quantity,4);
										$currency_id=$row['currency_id'];
										if($currency_id==1)
										{
											 $takarate=$bill_rate;
											 $bill_amount_taka=$bill_amount;
											 $usdrate=$bill_rate/$currency_rate;
											 $bill_amount_usd=$bill_amount/$currency_rate;
										}
										else if($currency_id==2)
										{
											$takarate=$bill_rate*$currency_rate;
											$bill_amount_taka=$bill_amount*$currency_rate;
											$usdrate=$bill_rate;
											$bill_amount_usd=$bill_amount;
										}
									
									?>
					                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					                	<td width="35" style="word-break: break-all;" align="center"><? echo $i;?></td>
                                        <td width="100" style="word-break: break-all;" align="center"><? echo $party=($row['within_group']==1)?$companyArr[$party_id]:$buyerArr[$party_id];?></td>
					                    <td width="100" style="word-break: break-all;" align="center"><? echo $bill_no;?></td>
					                    <td width="100" style="word-break: break-all;" align="center"><? echo change_date_format($row['bill_date']);?></td>					                   				            
					                    <td width="100" style="word-break: break-all;" align="center"><? echo $trimsGroupArr[$item_group];?></td>
					                    <td width="100" style="word-break: break-all;" align="center"><? echo $size_arr[$row['size_id']];?></td>
					                    <td width="100" style="word-break: break-all;" align="center"><? echo $order_no;?></td>
					                    <td width="100" style="word-break: break-all;" align="center"><? echo $challan_no; ?></td>
					                    <td width="100" style="word-break: break-all;" align="right"><? echo number_format($orderquantity,2);?></td>
					                    <td width="100" style="word-break: break-all;" align="right"><? echo number_format($bill_quantity,2); ?></td>
										<td width="100" style="word-break: break-all;" align="right"><? echo number_format($usdrate,4);?></td>
					                    <td width="100" style="word-break: break-all;" align="right"><? echo number_format($bill_amount_usd, 2); ?></td>
					                    <td width="100" style="word-break: break-all;" align="right"><? echo number_format($takarate,4); ?></td>
					                   	<td width="100" style="word-break: break-all;" align="right"><p><? echo number_format($bill_amount_taka,2);?></p></td>					                      	  
					                </tr>
					                <? 	
									$i++;

									$total_order_qty+=$orderquantity;
									$total_bill_qty+=$bill_quantity;
									$total_bill_tk+=$bill_amount_taka;
									$total_bill_usd+=$bill_amount_usd;

								}

							}
						}
					}
				}
			}
		
			
			//print_r($prod_sammary_array);
				?>
                
                
       		 </table>
        </div>
        <table width="<? echo $width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
				<tfoot>
                
                	<th width="35"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100">Total:</th>
                    <th width="100"><? echo number_format($total_order_qty,2);?></th>
                    <th width="100"><? echo number_format($total_bill_qty,2);?></th>
                    <th width="100"> </th>
                    <th width="100"> <? echo number_format($total_bill_usd,4);?></th>
                    <th width="100"></th>
                    <th width="100"> <? echo number_format($total_bill_tk,4);?></th>
				</tfoot>
			</table>
       
    </div>
    <br>
	<br>
    
    <div align="left" style="height:auto; width:500px; margin:0 auto; padding:0;">
    	
            <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="500" rules="all" id="rpt_table_header" align="left">
				<thead>
				    <tr>
						<td colspan="5" align="center" style="font-size:14px; font-weight:bold" ><? echo "Summary"; ?></td>
					</tr>
					<tr>
						<th width="35">SL</th>
						<th width="100">Party Name</th>
						<th width="100">Iteams</th>
						<th width="100">Curr. Bill Value ($)</th>
						<th>Curr. Bill Value (TK)</th>
					</tr>
				</thead>
			</table>
        
        	<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="500" rules="all" align="left">
             <? 
			$sammary_sql= "select a.id, a.currency_id, a.party_id, a.exchange_rate, a.within_group, b.bill_amount, e.item_group from trims_bill_mst a, trims_bill_dtls b, trims_delivery_dtls c, subcon_ord_breakdown d, subcon_ord_dtls e where a.entry_form=276 and a.id=b.mst_id and c.id=b.production_dtls_id and c.break_down_details_id=d.id  and d.mst_id=e.id and e.mst_id=c.received_id and a.company_id='$cbo_company_id' and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $sql_cond $where_con $company order by a.id ASC";
			  
			$sammary_result = sql_select($sammary_sql);
	        $sammary_array=array();
	        foreach($sammary_result as $row)
	        {
	       	 	$sammary_array[$row[csf('party_id')]][$row[csf('item_group')]]['item_group']=$row[csf('item_group')];
	       	 	$sammary_array[$row[csf('party_id')]][$row[csf('item_group')]]['party_id']=$row[csf('party_id')];
	       	 	$sammary_array[$row[csf('party_id')]][$row[csf('item_group')]]['bill_amount']+=$row[csf('bill_amount')];
	       	 	$sammary_array[$row[csf('party_id')]][$row[csf('item_group')]]['currency_id']=$row[csf('currency_id')];
	       	 	$sammary_array[$row[csf('party_id')]][$row[csf('item_group')]]['exchange_rate']=$row[csf('exchange_rate')];
	       	 	$sammary_array[$row[csf('party_id')]][$row[csf('item_group')]]['within_group']=$row[csf('within_group')];			
	 		}	
				$t=1;
				$bill_total_tk=0;
				$bill_total_usd=0;
				foreach($sammary_array as $party_id=>$party_id_arr)
				{
					$bill_amount_taka="";
					foreach($party_id_arr as $item_group=>$row)
					{	
						$bill_amount=$row['bill_amount'];
						$currency_id=$row['currency_id'];
						if($currency_id==1)
						{					 
							 $bill_amount_taka=$bill_amount;
							 $bill_amount_usd=$bill_amount/$currency_rate;						 	 	 
						}
						elseif($currency_id==2)
						{						
							$bill_amount_taka=$bill_amount*$currency_rate;
							$bill_amount_usd=$bill_amount;							
						}

						?>
                           <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $t; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $t; ?>">
                            <td width="35"  align="center"><? echo $t;?></td>
                            <td width="100" align="center"><? echo $party=($row['within_group']==1)?$companyArr[$party_id]:$buyerArr[$party_id];?></td>
                            <td width="100" align="center"><? echo $trimsGroupArr[$item_group];?></td>
                            <td width="100" align="right"><? echo number_format($bill_amount_usd,2);?></td>
                            <td align="right"><? echo number_format($bill_amount_taka,2);?></td>
                           </tr>
						<? 
					$t++;
					$bill_total_tk+=$bill_amount_taka;
					$bill_total_usd+=$bill_amount_usd;
					}
					?>
                    <tr style="background-color:#CCC">
                    	<td colspan="3" align="right"><b>Total</b></td>
                   	 	<td width="100" align="right"><b><? echo number_format($bill_total_usd,2);?></b></td>
                    	<td align="right"><b><? echo number_format($bill_total_tk,2);?></b></td>
                    </tr>
                 	<?
				} 
				?>
       		 </table>
    </div>
	<?
	
	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$report_type";
    exit();
	
}

?>