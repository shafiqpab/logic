<?php
session_start();
include('../../../includes/common.php');

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];


if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	if($data[1]==1) $dropdown_name="cbo_location_name";
	else if($data[1]==2) $dropdown_name="cbo_party_location";
	echo create_drop_down( $dropdown_name, 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "" );	
	exit();
}

/*if ($action == "company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."'  and module_id=20 and report_id=107 and is_deleted=0 and status_active=1");

	$print_report_format_arr=explode(",",$print_report_format);
	echo "$('#print').hide();\n";
	
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==78){echo "$('#print').show();\n";}			
		}
	}	
	exit();
}*/

if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);

	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";
	//$company_cond
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name","id,company_name", 1, "--Select Company--", $data[2], "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
	}	
	exit();	 
}


if ($action=="delivery_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	$data=explode("_",$data);
	$company=$data[0];
	$location=$data[1];
	$party_name=$data[2];
	$within_group=$data[3];
	?>
	<script>
		function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_order_popup(company,party_name,within_group)
		{   //alert(company+'_'+party_name+'_'+within_group);	
			load_drop_down( 'wash_delivery_controller', company+'_'+within_group+'_'+party_name, 'load_drop_down_buyer_pop', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Wash Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer PO');
			else if(val==5) $('#search_by_td').html('Buyer Style');
		}		
	</script>
	</head>
	<body onLoad="fnc_load_party_order_popup(<? echo $company;?>,<? echo $party_name;?>,<? echo $within_group;?>)">
        <div align="center" style="width:100%;" >
            <form name="searchreceivefrm_1"  id="searchreceivefrm_1" autocomplete="off">
                <table width="870" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="9"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                    	<tr>                	 
                            <th width="140">Company Name</th>
                            <th width="50">Within Group</th>
                            <th width="120">Party Name</th>
                            <th width="70">Delivery ID</th>
                            <th width="100">Search By</th>
                    		<th width="100" id="search_by_td">Wash Job No</th>
                            <th width="100" colspan="2">Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="selected_job">  <!--  echo $data;-->
							<? 
								echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "load_drop_down( 'wash_delivery_controller', this.value+'_'+".$within_group.", 'load_drop_down_buyer_pop', 'buyer_td' );"); ?>
                            </td>
                            <td>
							<?
								echo create_drop_down( "cbo_within_group", 50, $yes_no,"", 1, "--  --",$within_group, "load_drop_down( 'wash_delivery_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer_pop', 'buyer_td' );" ); ?>
							</td>
                            <td id="buyer_td">
								<? 
								echo create_drop_down( "cbo_party_name", 120, $blank_array,"", 1, "-- Select Party --", $selected, "" );?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:60px" placeholder="Receive ID" />
                            </td>
                            <td>
								<?
                                    $search_by_arr=array(1=>"Wash Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer PO",5=>"Buyer Style");
                                    echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                                ?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From">
                            </td>
                            <td>
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value, 'create_delivery_search_list_view', 'search_div', 'wash_delivery_return_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="9" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id"  style="width:70px">
                            </td>
                        </tr>
                    </tbody>
                </table> 
                <div id="search_div"></div>   
            </form>
        </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>$('#cbo_company_name').attr('disabled','disabled');</script>
	</html>
	<?
	exit();
}

if($action=="create_delivery_search_list_view")
{
	$data=explode('_',$data);
	$search_type =$data[5];
	$within_group =$data[6];
	$search_by=str_replace("'","",$data[7]);
	$search_str=trim(str_replace("'","",$data[8]));

	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer_cond=" and a.party_id='$data[1]'"; else $buyer_cond="";
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $delivery_date = "and a.delivery_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $delivery_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $delivery_date = "and a.delivery_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $delivery_date ="";
	}
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
			else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";
		}
		if ($data[4]!='') $delivery_id_cond=" and a.delivery_prefix_num='$data[4]'"; else $delivery_id_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str%'"; 
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str%'";   
		}
		if ($data[4]!='') $delivery_id_cond=" and a.delivery_prefix_num like '%$data[4]%'"; else $delivery_id_cond="";
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '$search_str%'";
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '$search_str%'";  
		}
		if ($data[4]!='') $delivery_id_cond=" and a.delivery_prefix_num like '$data[4]%'"; else $delivery_id_cond="";
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'";
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str'";  
		}
		if ($data[4]!='') $delivery_id_cond=" and a.delivery_prefix_num like '%$data[4]'"; else $delivery_id_cond="";
	}	
	
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array("select id, company_name from lib_company",'id','company_name');
	$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
	
	$po_ids=''; $buyer_po_arr=array();
	if($within_group==1)
	{
		if($db_type==0) $id_cond="group_concat(b.id)";
		else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
		//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
		if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
		{
			$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
		}
		//echo $po_ids;
		if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
		
		$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		}
		unset($po_sql_res);
	}
	
	
	$order_arr=array(); 
	$order_sql = sql_select("select a.subcon_job, a.within_group, a.party_id, b.id, b.order_no, b.order_quantity, b.buyer_po_id,b.buyer_po_no, b.buyer_style_ref, 
   b.buyer_buyer from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.entry_form=295 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($order_sql as $row)
	{
		$order_arr[$row[csf('id')]]['buyer_style_ref']=$row[csf('buyer_style_ref')];
		$order_arr[$row[csf('id')]]['buyer_po_no']=$row[csf('buyer_po_no')];
	}
	unset($order_sql);
	
	$spo_ids='';
	
	if($db_type==0)
	{
		$id_cond="group_concat(b.id)";
		$insert_date_cond="year(a.insert_date)";
		$wo_cond="group_concat(distinct(b.order_id))";
		$buyer_po_id_cond="group_concat(distinct(b.buyer_po_id))";
		
		$delivery_status_cond="group_concat(distinct(b.delivery_status))";
	}
	else if($db_type==2)
	{
		$id_cond="listagg(b.id,',') within group (order by b.id)";
		$insert_date_cond="TO_CHAR(a.insert_date,'YYYY')";
		$wo_cond="listagg(b.order_id,',') within group (order by b.order_id)";
		$buyer_po_id_cond="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
		$delivery_status_cond="listagg(b.delivery_status,',') within group (order by b.delivery_status)";
	}
	if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2))
	{
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.subcon_job=b.job_no_mst $search_com_cond", "id");
	}
	
	if ( $spo_ids!="") $spo_idsCond=" and b.order_id in ($spo_ids)"; else $spo_idsCond="";
	
	$sql= "select a.id, a.delivery_no, a.delivery_prefix_num, $insert_date_cond as year, a.location_id, a.within_group, a.party_id, a.party_location, a.deli_party, a.deli_party_location, a.delivery_date, a.job_no, $wo_cond as order_id, $buyer_po_id_cond as buyer_po_id, $delivery_status_cond as delivery_status from subcon_delivery_mst a, subcon_delivery_dtls b 
	
	where a.id=b.mst_id and a.entry_form='303' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $delivery_date $company $buyer_cond $withinGroup $delivery_id_cond $spo_idsCond $po_idsCond 
	
	group by a.id, a.delivery_no, a.delivery_prefix_num, a.insert_date, a.location_id, a.within_group, a.party_id, a.party_location, a.deli_party, a.deli_party_location, a.delivery_date, a.job_no order by a.id DESC ";
	//echo $sql; 
	$result = sql_select($sql);
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="70">Delivery No</th>
                <th width="70">Year</th>
                <th width="120">Party Name</th>
                <th width="100">Challan No</th>
                <th width="80">Delivery Date</th>
                <th width="120">Order No</th>
                <th width="100">Buyer PO</th>
                <th>Buyer Style</th>
            </thead>
     	</table>
     <div style="width:820px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach($result as $row)
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$order_no='';
				$order_id=array_unique(explode(",",$row[csf("order_id")]));
				foreach($order_id as $val)
				{
					if($order_no=="") $order_no=$po_arr[$val]; else $order_no.=",".$po_arr[$val];
					
					if($buyer_po=="") $buyer_po=$order_arr[$val]['buyer_po_no']; else $buyer_po.=','.$order_arr[$val]['buyer_po_no'];
					if($buyer_style=="") $buyer_style=$order_arr[$val]['buyer_style_ref']; else $buyer_style.=','.$order_arr[$val]['buyer_style_ref'];
				}
				$order_no=implode(",",array_unique(explode(",",$order_no)));
				
				$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				$delivery_status=explode(",",$row[csf('delivery_status')]);
				$party_name="";
				if($row[csf("within_group")]==1) $party_name=$comp[$row[csf("party_id")]]; else $party_name=$party_arr[$row[csf("party_id")]];
				$str_data="";
				$str_data=$row[csf('id')].'***'.$row[csf('delivery_no')].'***'.$row[csf('location_id')].'***'.$row[csf('within_group')].'***'.$row[csf('party_id')].'***'.change_date_format($row[csf('delivery_date')]).'***'.$row[csf('remarks')].'***'.$row[csf('job_no')].'***'.$order_no.'***'.$buyer_style.'***'.$buyer_po.'***'.implode(",",$delivery_status).'***'.$row[csf('party_location')].'***'.$row[csf('deli_party')].'***'.$row[csf('deli_party_location')];
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $str_data; ?>');" > 
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="70" align="center"><? echo $row[csf("delivery_prefix_num")]; ?></td>
                        <td width="70" align="center"><? echo $row[csf("year")]; ?></td>
                        <td width="120"><? echo $party_name; ?></td>		
						<td width="100"><? echo $row[csf("chalan_no")]; ?></td>
						<td width="80"><? echo change_date_format($row[csf("delivery_date")]);  ?></td>
                        <td width="120" style="word-break:break-all"><p><? echo $order_no; ?></p></td>	
                        <td width="100" style="word-break:break-all"><? echo $buyer_po; ?></td>
                        <td style="word-break:break-all"><? echo $buyer_style; ?></td>
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
if ($action=="delivery_return_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	$data=explode("_",$data);
	$company=$data[0];
	$location=$data[1];
	$party_name=$data[2];
	$within_group=$data[3];
	?>
	<script>
		function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_order_popup(company,party_name,within_group)
		{   //alert(company+'_'+party_name+'_'+within_group);	
			load_drop_down( 'wash_delivery_controller', company+'_'+within_group+'_'+party_name, 'load_drop_down_buyer_pop', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Wash Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer PO');
			else if(val==5) $('#search_by_td').html('Buyer Style');
		}		
	</script>
	</head>
	<body onLoad="fnc_load_party_order_popup(<? echo $company;?>,<? echo $party_name;?>,<? echo $within_group;?>)">
        <div align="center" style="width:100%;" >
            <form name="searchreceivefrm_1"  id="searchreceivefrm_1" autocomplete="off">
                <table width="870" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="9"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                    	<tr>                	 
                            <th width="140">Company Name</th>
                            <th width="50">Within Group</th>
                            <th width="120">Party Name</th>
                            <th width="70">Delivery ID</th>
                            <th width="100">Search By</th>
                    		<th width="100" id="search_by_td">Wash Job No</th>
                            <th width="100" colspan="2">Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="selected_job">  <!--  echo $data;-->
							<? 
								echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "load_drop_down( 'wash_delivery_controller', this.value+'_'+".$within_group.", 'load_drop_down_buyer_pop', 'buyer_td' );"); ?>
                            </td>
                            <td>
							<?
								echo create_drop_down( "cbo_within_group", 50, $yes_no,"", 1, "--  --",$within_group, "load_drop_down( 'wash_delivery_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer_pop', 'buyer_td' );" ); ?>
							</td>
                            <td id="buyer_td">
								<? 
								echo create_drop_down( "cbo_party_name", 120, $blank_array,"", 1, "-- Select Party --", $selected, "" );?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:60px" placeholder="Receive ID" />
                            </td>
                            <td>
								<?
                                    $search_by_arr=array(1=>"Wash Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer PO",5=>"Buyer Style");
                                    echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                                ?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From">
                            </td>
                            <td>
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value, 'create_delivery_return_search_list_view', 'search_div', 'wash_delivery_return_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="9" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id"  style="width:70px">
                            </td>
                        </tr>
                    </tbody>
                </table> 
                <div id="search_div"></div>   
            </form>
        </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>$('#cbo_company_name').attr('disabled','disabled');</script>
	</html>
	<?
	exit();
}
if($action=="create_delivery_return_search_list_view")
{
	$data=explode('_',$data);
	$search_type =$data[5];
	$within_group =$data[6];
	$search_by=str_replace("'","",$data[7]);
	$search_str=trim(str_replace("'","",$data[8]));

	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer_cond=" and a.party_id='$data[1]'"; else $buyer_cond="";
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $delivery_date = "and a.delivery_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $delivery_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $delivery_date = "and a.delivery_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $delivery_date ="";
	}
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
			else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";
		}
		if ($data[4]!='') $delivery_id_cond=" and a.delivery_prefix_num='$data[4]'"; else $delivery_id_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str%'"; 
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str%'";   
		}
		if ($data[4]!='') $delivery_id_cond=" and a.delivery_prefix_num like '%$data[4]%'"; else $delivery_id_cond="";
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '$search_str%'";
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '$search_str%'";  
		}
		if ($data[4]!='') $delivery_id_cond=" and a.delivery_prefix_num like '$data[4]%'"; else $delivery_id_cond="";
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'";
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str'";  
		}
		if ($data[4]!='') $delivery_id_cond=" and a.delivery_prefix_num like '%$data[4]'"; else $delivery_id_cond="";
	}	
	
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array("select id, company_name from lib_company",'id','company_name');
	$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
	
	$po_ids=''; $buyer_po_arr=array();
	if($within_group==1)
	{
		if($db_type==0) $id_cond="group_concat(b.id)";
		else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
		//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
		if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
		{
			$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
		}
		//echo $po_ids;
		if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
		
		$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		}
		unset($po_sql_res);
	}
	
	
	$order_arr=array(); 
	$order_sql = sql_select("select a.subcon_job, a.within_group, a.party_id, b.id, b.order_no, b.order_quantity, b.buyer_po_id,b.buyer_po_no, b.buyer_style_ref, 
   b.buyer_buyer from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.entry_form=295 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($order_sql as $row)
	{
		$order_arr[$row[csf('id')]]['buyer_style_ref']=$row[csf('buyer_style_ref')];
		$order_arr[$row[csf('id')]]['buyer_po_no']=$row[csf('buyer_po_no')];
	}
	unset($order_sql);
	
	$spo_ids='';
	
	if($db_type==0)
	{
		$id_cond="group_concat(b.id)";
		$insert_date_cond="year(a.insert_date)";
		$wo_cond="group_concat(distinct(b.order_id))";
		$buyer_po_id_cond="group_concat(distinct(b.buyer_po_id))";
		
		$delivery_status_cond="group_concat(distinct(b.delivery_status))";
	}
	else if($db_type==2)
	{
		$id_cond="listagg(b.id,',') within group (order by b.id)";
		$insert_date_cond="TO_CHAR(a.insert_date,'YYYY')";
		$wo_cond="listagg(b.order_id,',') within group (order by b.order_id)";
		$buyer_po_id_cond="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
		$delivery_status_cond="listagg(b.delivery_status,',') within group (order by b.delivery_status)";
	}
	if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2))
	{
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.subcon_job=b.job_no_mst $search_com_cond", "id");
	}
	
	if ( $spo_ids!="") $spo_idsCond=" and b.order_id in ($spo_ids)"; else $spo_idsCond="";
	
	$sql= "select a.id as update_id, a.delivery_no, a.delivery_prefix_num, $insert_date_cond as year, a.location_id, a.within_group, a.party_id, a.party_location, a.deli_party, a.deli_party_location, a.delivery_date, a.job_no, $wo_cond as order_id, $buyer_po_id_cond as buyer_po_id,a.return_challan_no, $delivery_status_cond as delivery_status,a.delivery_mst_id,a.delivery_challan_no from subcon_delivery_mst a, subcon_delivery_dtls b 
	
	where a.id=b.mst_id and a.entry_form='360' and  b.entry_form='360' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $delivery_date $company $buyer_cond $withinGroup $delivery_id_cond $spo_idsCond $po_idsCond 
	
	group by a.id, a.delivery_no, a.delivery_prefix_num, a.insert_date, a.location_id, a.within_group, a.party_id, a.party_location, a.deli_party, a.deli_party_location, a.delivery_date, a.job_no,a.return_challan_no ,a.delivery_mst_id,a.delivery_challan_no order by a.id DESC ";
	//echo $sql; 
	$result = sql_select($sql);
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="70">Delv Retn No</th>
                <th width="70">Year</th>
                <th width="120">Party Name</th>
                <th width="100">Return Challan</th>
                <th width="80">Delivery Date</th>
                <th width="120">Order No</th>
                <th width="100">Buyer PO</th>
                <th>Buyer Style</th>
            </thead>
     	</table>
     <div style="width:820px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach($result as $row)
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$order_no='';
				$order_id=array_unique(explode(",",$row[csf("order_id")]));
				foreach($order_id as $val)
				{
					if($order_no=="") $order_no=$po_arr[$val]; else $order_no.=",".$po_arr[$val];
					
					if($buyer_po=="") $buyer_po=$order_arr[$val]['buyer_po_no']; else $buyer_po.=','.$order_arr[$val]['buyer_po_no'];
					if($buyer_style=="") $buyer_style=$order_arr[$val]['buyer_style_ref']; else $buyer_style.=','.$order_arr[$val]['buyer_style_ref'];
				}
				$order_no=implode(",",array_unique(explode(",",$order_no)));
				
				$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				$delivery_status=explode(",",$row[csf('delivery_status')]);
				$party_name="";
				if($row[csf("within_group")]==1) $party_name=$comp[$row[csf("party_id")]]; else $party_name=$party_arr[$row[csf("party_id")]];
				$str_data="";
				$str_data=$row[csf('id')].'***'.$row[csf('delivery_no')].'***'.$row[csf('location_id')].'***'.$row[csf('within_group')].'***'.$row[csf('party_id')].'***'.change_date_format($row[csf('delivery_date')]).'***'.$row[csf('return_challan_no')].'***'.$row[csf('job_no')].'***'.$order_no.'***'.$buyer_style.'***'.$buyer_po.'***'.implode(",",$delivery_status).'***'.$row[csf('party_location')].'***'.$row[csf('deli_party')].'***'.$row[csf('deli_party_location')].'***'.$row[csf('update_id')].'***'.$row[csf('delivery_mst_id')].'***'.$row[csf('delivery_challan_no')];
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $str_data; ?>');" > 
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="70" align="center"><? echo $row[csf("delivery_prefix_num")]; ?></td>
                        <td width="70" align="center"><? echo $row[csf("year")]; ?></td>
                        <td width="120"><? echo $party_name; ?></td>		
						<td width="100"><? echo $row[csf("return_challan_no")]; ?></td>
						<td width="80"><? echo change_date_format($row[csf("delivery_date")]);  ?></td>
                        <td width="120" style="word-break:break-all"><p><? echo $order_no; ?></p></td>	
                        <td width="100" style="word-break:break-all"><? echo $buyer_po; ?></td>
                        <td style="word-break:break-all"><? echo $buyer_style; ?></td>
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
if($action=="load_php_dtls_form")
{
	
	$exdata=explode("**",$data);
	$jobno=''; 
	$delivery_id=$exdata[0];
	$jobno=$exdata[1];
	$update_id=0;
	$update_id=$exdata[3];
	$delivery_mst_id=$exdata[4];
	
	
	
	//print_r();
	
	//Array ( [0] => 639 [1] => OG-WOE-19-00057 [2] => 1 ) 
	//print_r($exdata); die;
	//-------------------------------------------------------------------------------------------------------------------------------------------
	$color_arrey=return_library_array( "select id,color_name from lib_color",'id','color_name');
	$size_arrey=return_library_array( "select id,size_name from  lib_size",'id','size_name');
	//-------------------------------------------------------------------------------------------------------------------------------------------
	
	//-------------------------------------------------------------------------------------------------------------------------------------------
	$buyer_po_arr=array();
 	$po_sql="select a.subcon_job, a.within_group, a.party_id, b.id, b.order_no, b.order_quantity, b.buyer_po_id,b.buyer_po_no, b.buyer_style_ref, 
   b.buyer_buyer from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.entry_form=295 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
	}
	unset($po_sql_res);
	//-------------------------------------------------------------------------------------------------------------------------------------------
	
	
	//-------------------------------------------------------------------------------------------------------------------------------------------
	if($db_type==0) $process_type_cond="group_concat(c.process,'*',c.embellishment_type)";
	else if ($db_type==2) $process_type_cond="listagg(c.process||'*'||c.embellishment_type,',') within group (order by c.process||'*'||c.embellishment_type)";
	
		
		
		
		
		if($update_id>0)
		{
			 $sql_job="select a.id, a.within_group, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.buyer_po_no, b.gmts_item_id, 3 as main_process_id, b.order_uom, b.gmts_color_id as color_id, b.gmts_size_id, b.order_quantity as qnty, $process_type_cond as process_type,e.delivery_qty,e.id as delivery_return_dtls_id,e.delivery_dtls_id
			from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c,subcon_delivery_mst d,subcon_delivery_dtls e
			where a.entry_form=295 and a.subcon_job=b.job_no_mst  and a.id=b.mst_id and  d.entry_form=360 and e.entry_form=360  and d.id=e.mst_id and b.id=e.order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id=c.mst_id  and d.id=$update_id 
			group by a.id, a.within_group, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, b.buyer_po_no, a.delivery_date, b.id, b.buyer_po_id, b.gmts_item_id, b.order_uom, b.gmts_color_id, b.gmts_size_id, b.order_quantity,e.delivery_qty,e.id,e.delivery_dtls_id
			order by b.id ASC";
		}
		else
		{
			$sql_job="select a.id, a.within_group, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.buyer_po_no, b.gmts_item_id, 3 as main_process_id, b.order_uom, b.gmts_color_id as color_id, b.gmts_size_id, b.order_quantity as qnty,e.delivery_qty ,$process_type_cond as process_type,e.id as delivery_details_id
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c,subcon_delivery_mst d,subcon_delivery_dtls e
		where a.entry_form=295 and a.subcon_job=b.job_no_mst  and a.id=b.mst_id and  d.entry_form=303 and d.id=e.mst_id and b.id=e.order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id=c.mst_id and d.id=$delivery_id
		group by a.id, a.within_group, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, b.buyer_po_no, a.delivery_date, b.id, b.buyer_po_id, b.gmts_item_id, b.order_uom, b.gmts_color_id, b.gmts_size_id, b.order_quantity,e.delivery_qty,e.id
		order by b.id ASC";
			}
			$sql_result =sql_select($sql_job);
			
		if($update_id>0)
		{
			 $delivery_qty_sql="select id,delivery_qty
			from subcon_delivery_dtls 
			where  status_active=1 and is_deleted=0 and mst_id=$delivery_mst_id";
			$delv_qty_sql_result =sql_select($delivery_qty_sql);
			$delv_qty_arr=array();
			foreach ($delv_qty_sql_result as $row)
			{
				$delv_qty_arr[$row[csf("id")]]['delivery_qty']=$row[csf("delivery_qty")];
			}
			unset($delv_qty_sql_result);
			
			//echo "<pre>";
			//print_r($delv_qty_arr); die;
		}
			
	//-------------------------------------------------------------------------------------------------------------------------------------------
	
	
	$k=0;
	$num_rowss=count($sql_result);
	foreach ($sql_result as $row)
	{
		$k++;
		if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		
		$ex_process=array_unique(explode(",",$row[csf("process_type")]));
		$process_name=""; $sub_process_name="";
		foreach($ex_process as $process_data)
		{
			$ex_process_type=explode("*",$process_data);
			$process_id=$ex_process_type[0];
			$type_id=$ex_process_type[1];
			if($process_id==1) $process_type_arr=$wash_wet_process;
			else if($process_id==2) $process_type_arr=$wash_dry_process;
			else if($process_id==3) $process_type_arr=$wash_laser_desing;
			else $process_type_arr=$blank_array;
			
			if($process_name=="") $process_name=$wash_type[$process_id]; else $process_name.=','.$wash_type[$process_id];
			
			if($sub_process_name=="") $sub_process_name=$process_type_arr[$type_id]; else $sub_process_name.=','.$process_type_arr[$type_id];
		}
		$process_name=implode(",",array_unique(explode(",",$process_name)));
		$sub_process_name=implode(",",array_unique(explode(",",$sub_process_name)));
		
		
		if($update_id>0)
		{
			$quantity=$row[csf("delivery_qty")];
			$delivery_qty=$delv_qty_arr[$row[csf("delivery_dtls_id")]]['delivery_qty'];
			$bllance=$delivery_qty-$quantity;
		}
		else
		{
			$delivery_qty=$row[csf("delivery_qty")];
		}
		
		?>
        <tr bgcolor="<? echo $bgcolor; ?>" >
            <td align="center">
				<? echo $k; ?>
            	<input name="txtbuyerPoId_<? echo $k; ?>" id="txtbuyerPoId_<? echo $k; ?>" type="hidden" class="text_boxes" style="width:70px" value="<? echo $row[csf("buyer_po_id")]; ?>" />
            </td>
            <td style="word-break:break-all">
				<? if ($row[csf("within_group")] == 1) echo $buyer_po_arr[$row[csf("buyer_po_id")]]['po']; else echo $row[csf("buyer_po_no")]; ?>
            </td>
            <td style="word-break:break-all"><? echo $garments_item[$row[csf("gmts_item_id")]]; ?></td>
            <td style="word-break:break-all"><? echo $process_name; ?></td>
            <td style="word-break:break-all"><? echo $sub_process_name; ?></td>
            <td style="word-break:break-all"><? echo $color_arrey[$row[csf("color_id")]]; ?></td>
            <td style="word-break:break-all"><? echo $size_arrey[$row[csf("gmts_size_id")]]; ?></td>
            <td align="right"><input type="text" name="txtPrvCurrDelv_<? echo $k; ?>" id="txtPrvCurrDelv_<? echo $k; ?>" class="text_boxes_numeric" style="width:70px;" value="<? echo $delivery_qty; ?>"  readonly /></td>
           <td align="right">
           <input type="text" name="txtTotCurrReturnDelv_<? echo $k; ?>" id="txtTotCurrReturnDelv_<? echo $k; ?>" class="text_boxes_numeric" style="width:70px;"  onKeyUp="fnc_production_qty_ability(this.value,<? echo $k; ?>); fnc_total_calculate();"  value="<? echo $quantity; ?>"/>
            </td>
            <td align="right">
                <input type="text" name="txtTotCurrReturnDelvBalance_<? echo $k; ?>" id="txtTotCurrReturnDelvBalance_<? echo $k; ?>" class="text_boxes_numeric" style="width:70px;" value="<? echo $bllance; ?>" readonly/>
                <input type="hidden" name="txtDtlsUpdateId_<? echo $k; ?>" id="txtDtlsUpdateId_<? echo $k; ?>" style="width:80px" class="text_boxes" value="<? echo $row[csf("delivery_return_dtls_id")]; ?>"  />
                <input type="hidden" name="txtdeliverydtlsid_<? echo $k; ?>" id="txtdeliverydtlsid_<? echo $k; ?>" style="width:80px" class="text_boxes" value="<? echo $row[csf("delivery_details_id")]; ?>" />
                <input type="hidden" name="txtColorSizeid_<? echo $k; ?>" id="txtColorSizeid_<? echo $k; ?>" style="width:80px" class="text_boxes" />
                <input type="hidden" name="txtpoid_<? echo $k; ?>" id="txtpoid_<? echo $k; ?>" style="width:80px" class="text_boxes" value="<? echo $row[csf("po_id")]; ?>" />
             </td> 
        </tr>
	<?	
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		if(str_replace("'","",$txt_update_id)=="")
		{
			$id=return_next_id("id", "subcon_delivery_mst", 1);
			if($db_type==2) $mrr_cond="and TO_CHAR(insert_date,'YYYY')=".date('Y',time()); else if($db_type==0) $mrr_cond="and year(insert_date)=".date('Y',time());
			$new_sys_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'WDR', date("Y",time()), 5, "select delivery_prefix, delivery_prefix_num from subcon_delivery_mst where company_id=$cbo_company_name and entry_form=360 $mrr_cond order by id DESC ", "delivery_prefix", "delivery_prefix_num" ));
			
		
			$field_array_mst="id, delivery_prefix, delivery_prefix_num, delivery_no, company_id, location_id, within_group, party_id, party_location,delivery_date,job_no,entry_form,process_id,return_challan_no,delivery_mst_id,delivery_challan_no, inserted_by, insert_date, status_active, is_deleted";
			
			$data_array_mst="(".$id.",'".$new_sys_number[1]."','".$new_sys_number[2]."','".$new_sys_number[0]."',".$cbo_company_name.",".$cbo_location_name.",".$cbo_within_group.",".$cbo_party_name.",".$cbo_party_location.",".$txt_delivery_return_date.",".$txtJob_no.",360,360,".$txt_return_challan_no.",".$txt_delv_id.",".$txt_delv_no.",".$user_id.",'".$pc_date_time."',1,0)";
			
			$mrr_no=$new_sys_number[0];
		}
		else
		{
			$id=str_replace("'","",$txt_update_id);
			$mrr_no=str_replace("'","",$txt_delv_return_no);
			
			$field_array_update_mst="location_id*within_group*party_id*party_location*delivery_date*job_no*return_challan_no*updated_by*update_date";
			$data_array_update_mst="".$cbo_location_name."*".$cbo_within_group."*".$cbo_party_name."*".$cbo_party_location."*".$txt_delivery_return_date."*".$txtJob_no."*".$txt_return_challan_no."*".$user_id."*'".$pc_date_time."'";
		}
		$data_arr_dtls="";
		$id_dtls=return_next_id("id", "subcon_delivery_dtls", 1); $po_wise_qty_arr=array(); $color_size_arr=array();
		$field_array_dtls="id, mst_id, buyer_po_id, order_id,color_size_id, delivery_qty,entry_form,delivery_dtls_id,process_id,inserted_by, insert_date, status_active, is_deleted";
		
		for($i=1; $i<= str_replace("'","",$total_row); $i++)
		{
			$txtbuyerPoId="txtbuyerPoId_".$i;
			$txtCurrDelv="txtCurrDelv_".$i;
			$txtTotCurrReturnDelv="txtTotCurrReturnDelv_".$i;
			$txtpoid="txtpoid_".$i;
			$txtColorSizeid="txtColorSizeid_".$i;
			$txtDtlsUpdateId="txtDtlsUpdateId_".$i;
			$txtdeliverydtlsid="txtdeliverydtlsid_".$i;
			if($data_arr_dtls!="") $data_arr_dtls.=",";
			$data_arr_dtls.="(".$id_dtls.",".$id.",".$$txtbuyerPoId.",".$$txtpoid.",".$$txtColorSizeid.",".$$txtTotCurrReturnDelv.",360,".$$txtdeliverydtlsid.",360,'".$user_id."','".$pc_date_time."',1,0)"; 
			$id_dtls=$id_dtls+1;
		}
		
		$flag=1;
		//echo "10**".$id_dtls."**".$QcPassQtyArr[$$emblProdDtlsId];    die;
		//echo "10**INSERT INTO subcon_delivery_mst (".$field_array_mst.") VALUES ".$data_array_mst;    die;
		//echo "10**$total_row**INSERT INTO subcon_delivery_dtls (".$field_array_dtls.") VALUES ".$data_arr_dtls;    die;
		if(str_replace("'","",$txt_update_id)=="")
		{
			$rID=sql_insert("subcon_delivery_mst",$field_array_mst,$data_array_mst,1);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
		}else{
			$rID=sql_update("subcon_delivery_mst",$field_array_update_mst,$data_array_update_mst,"id",$txt_update_id,0);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		$rID1=sql_insert("subcon_delivery_dtls",$field_array_dtls,$data_arr_dtls,1);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		//echo "10**$rID**$rID1"; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'","",$id)."**".$mrr_no."**".str_replace("'","",$txtJob_no)."**".str_replace("'","",$id_dtls);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "0**".str_replace("'","",$id)."**".$mrr_no."**".str_replace("'","",$txtJob_no)."**".str_replace("'","",$id_dtls);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$id=str_replace("'","",$txt_update_id);
		$mrr_no=str_replace("'","",$txt_delv_return_no);
		
		$field_array_update_mst="location_id*within_group*party_id*party_location*delivery_date*job_no*return_challan_no*updated_by*update_date";
		$data_array_update_mst="".$cbo_location_name."*".$cbo_within_group."*".$cbo_party_name."*".$cbo_party_location."*".$txt_delivery_return_date."*".$txtJob_no."*".$txt_return_challan_no."*".$user_id."*'".$pc_date_time."'";
			
		$data_arr_dtls="";
		$id_dtls=return_next_id("id", "subcon_delivery_dtls", 1);
		 $po_wise_qty_arr=array();
		  $color_size_arr=array();
		  
		//$field_array_dtls="id, mst_id, buyer_po_id, order_id,color_size_id, delivery_qty,inserted_by, insert_date, status_active, is_deleted";
		$field_array_dtls="id, mst_id, buyer_po_id, order_id,color_size_id, delivery_qty,entry_form,delivery_dtls_id,inserted_by, insert_date, status_active, is_deleted";
		$field_arr_up="buyer_po_id*order_id*color_size_id*delivery_qty*updated_by*update_date";
		for($i=1; $i<= str_replace("'","",$total_row); $i++)
		{
			$txtbuyerPoId="txtbuyerPoId_".$i;
			$txtCurrDelv="txtCurrDelv_".$i;
			$txtTotCurrReturnDelv="txtTotCurrReturnDelv_".$i;
			$txtpoid="txtpoid_".$i;
			$txtColorSizeid="txtColorSizeid_".$i;
			$txtDtlsUpdateId="txtDtlsUpdateId_".$i;
			$txtdeliverydtlsid="txtdeliverydtlsid_".$i;
			
			//echo "10**".str_replace("'","",$$txtDtlsUpdateId);die;
			
			if(str_replace("'","",$$txtDtlsUpdateId)=="")
			{
				if($data_arr_dtls!="") $data_arr_dtls.=","; 	
				$data_arr_dtls.="(".$id_dtls.",".$id.",".$$txtbuyerPoId.",".$$txtpoid.",".$$txtColorSizeid.",".$$txtTotCurrReturnDelv.",360,".$$txtdeliverydtlsid.",'".$user_id."','".$pc_date_time."',1,0)"; 
				$id_dtls=$id_dtls+1;
			}
			else if(str_replace("'","",$$txtDtlsUpdateId)!="")
			{
				$data_arr_up[str_replace("'","",$$txtDtlsUpdateId)]=explode("*",("".$$txtbuyerPoId."*".$$txtpoid."*".$$txtColorSizeid."*".$$txtTotCurrReturnDelv."*".$user_id."*'".$pc_date_time."'"));
				$id_arr_delv[]=str_replace("'","",$$txtDtlsUpdateId);
				$hdn_break_id_arr[]=str_replace("'","",$$txtDtlsUpdateId);
			}
		}
		
		$flag=1;
		
		
		$rID=sql_update("subcon_delivery_mst",$field_array_update_mst,$data_array_update_mst,"id",$txt_update_id,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		//echo "10**".bulk_update_sql_statement("subcon_delivery_dtls", "id", $field_arr_up, $data_arr_up, $id_arr_delv); die;
		if($data_arr_up!=""){
			$rID1=execute_query(bulk_update_sql_statement( "subcon_delivery_dtls", "id", $field_arr_up, $data_arr_up, $id_arr_delv));
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if($data_arr_dtls !=""){
			//echo "10**insert into subcon_delivery_dtls ($field_array_dtls) values $data_array_dtls "; die;
			$rID2=sql_insert("subcon_delivery_dtls",$field_array_dtls,$data_arr_dtls,1);
			if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		}
		//echo "10**$rID**$rID1**$rID2**$rID3**$sts_po**$sts_mst**$flag"; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_update_id)."**".$mrr_no."**".str_replace("'","",$txtJob_no)."**".str_replace("'","",$id_dtls);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "1**".str_replace("'","",$txt_update_id)."**".$mrr_no."**".str_replace("'","",$txtJob_no)."**".str_replace("'","",$id_dtls);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_update_id);
			}
		}
		disconnect($con);
		die;
	}
}

?>