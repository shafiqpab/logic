<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="load_drop_down_customer_name")
{

	$data=explode('_',$data);
	//echo "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond group by buy.id, buy.buyer_name order by buy.buyer_name"; die;
	if($data[1]==2)
	{
		echo create_drop_down( "cbo_customer_id", 151, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected,'',0,'','','','','','',"cbo_customer_id[]");
	}
	else
	{
		echo create_drop_down( "cbo_customer_id", 151, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Customer --", $data[2],'',0,'','','','','','',"cbo_customer_id[]");
	}
	exit();
}

if($action=="check_conversion_rate")
{
	$data=explode("**",$data);
	
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$exchange_rate=set_conversion_rate( $data[0], $conversion_date );
	echo $exchange_rate;
	exit();	
}	


if ($action=="load_drop_down_buyer")
{

	$data=explode('_',$data);
	//print_r($data);
	echo create_drop_down( "cboCustBuyer_".$data[2], 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected,'',0,'','','','','','',"cboCustBuyer[]");
	exit();
}

if ($action=="load_drop_down_section")
{
	$data=explode("_",$data);
	echo create_drop_down( "cboSection_".$data[2], 70, "select a.id, a.section_name from lib_section a,lib_department b,lib_division c where c.company_id= $data[0] and b.division_id=c.id and a.department_id=b.id and a.status_active=1 and b.status_active=1 and c.status_active=1","id,section_name", 1, "-- Select Section --","",'',0,'','','','','','',"cboSection[]");
	exit();	 
} 

if ($action=="load_drop_down_group")
{
	$data=explode("_",$data);
	echo create_drop_down( "cboItemGroup_".$data[2], 70, "select id, item_name from lib_item_group where status_active=1","id,item_name", 1, "-- Select Group --","",'',0,'','','','','','',"cboItemGroup[]");
	exit();	 
} 

if ($action=="order_popup")
{	
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	function js_set_value(id)
	{
		//alert(booking_no); 
		document.getElementById('hidd_booking_data').value=id;
		parent.emailwindow.hide();
	}

	function fnc_load_party_order_popup(company,customer_id)
	{   	
		load_drop_down( 'trims_order_receive_controller', company+'_'+1+'_'+customer_id, 'load_drop_down_customer_name', 'buyer_td' );
		$('#cbo_customer_id').attr('disabled',true);
	}
	
	function search_by(val,type)
	{
		if(type==1)
		{
			if(val==1 || val==0)
			{
				$('#txt_search_common').val('');
				$('#search_td').html('W/O No');
			}
			else if(val==2)
			{
				$('#txt_search_common').val('');
				$('#search_td').html('Job NO');
			}
			else if(val==3)
			{
				$('#txt_search_common').val('');
				$('#search_td').html('Style Ref.');
			}
			else if(val==4)
			{
				$('#txt_search_common').val('');
				$('#search_td').html('Buyer Po');
			}
		}
	}
</script>
</head>
<body onLoad="fnc_load_party_order_popup(<? echo $company;?>,<? echo $customer_id;?>)">
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                    <tr>
                        <th colspan="7" align="center">
                            <? echo create_drop_down( "cbo_search_category", 110, $string_search_type,'', 1, "-- Search Catagory --" ); ?>
                        </th>
                    </tr>
                    <tr>                	 
                        <th width="150">Customer Name</th>
                        <th width="80">Search Type</th>
                        <th width="100" id="search_td">W/O No</th>
                        <th width="60">W/O Year</th>
                        <th colspan="2" width="120">W/O Date Range</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" id="hidd_booking_data">
                        </th>
                    </tr>                                 
                </thead>
                <tr class="general">
                    <td id="buyer_td"><? echo create_drop_down( "cbo_customer_id", 150, $blank_array,"", 1, "-- Select Customer --" ); ?></td>
                    <td>
                        <? 
                            $searchtype_arr=array(1=>"W/O No",2=>"Buyer Job",3=>"Buyer Style Ref.",4=>"Buyer Po");
                            echo create_drop_down( "cbo_search_type", 80, $searchtype_arr,"", 0, "", 1, "search_by(this.value,1)",0,"" );
                        ?>
                    </td>
                    <td><input name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:90px"></td>
                    <td><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From"></td>
                    <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To"></td> 
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_customer_id').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_search_category').value+'_'+<? echo $company;?>+'_'+document.getElementById('cbo_search_type').value, 'create_booking_search_list_view', 'search_div', 'trims_order_receive_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="7"align="center" height="30" valign="middle"><?  echo load_month_buttons(); ?></td>
                </tr>
            </table>
            <div id="search_div"></div>   
        </form>
    </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}


if($action=="create_booking_search_list_view")
{	
	$data=explode('_',$data);
	$search_type=$data[7];
	
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Customer First."; die; }
	//if ($data[0]!=0 && ) $buyer=" and buyer_id='$data[1]'"; else  $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[4]"; } else if($db_type==2) { $year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]"; }
	$master_company=$data[6];

	$woorder_cond=""; $job_cond=""; $style_cond=""; $po_cond="";
	if($data[5]==1)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no = '$data[1]' ";
			if ($search_type==2) $job_cond=" and a.job_no_prefix_num = '$data[1]' ";
			if ($search_type==3) $style_cond=" and a.style_ref_no = '$data[1]' ";
			if ($search_type==4) $po_cond=" and b.po_number = '$data[1]' ";
		}
	}
	if($data[5]==2)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no like '$data[1]%' ";
			if ($search_type==2) $job_cond=" and a.job_no_prefix_num like '$data[1]%' ";
			if ($search_type==3) $style_cond=" and a.style_ref_no like '$data[1]%' ";
			if ($search_type==4) $po_cond=" and b.po_number like '$data[1]%' ";
		}
	}
	if($data[5]==3)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no like '%$data[1]' ";
			if ($search_type==2) $job_cond=" and a.job_no_prefix_num like '%$data[1]' ";
			if ($search_type==3) $style_cond=" and a.style_ref_no like '%$data[1]' ";
			if ($search_type==4) $po_cond=" and b.po_number like '%$data[1]' ";
		}	
	}
	if($data[5]==4 || $data[5]==0)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no like '%$data[1]%' ";
			if ($search_type==2) $job_cond=" and a.job_no_prefix_num like '%$data[1]%' ";
			if ($search_type==3) $style_cond=" and a.style_ref_no like '%$data[1]%' ";
			if ($search_type==4) $po_cond=" and b.po_number like '%$data[1]%' ";
		}
	}
	
	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	if(($job_cond!="" && $search_type==2) || ($style_cond!="" && $search_type==3)|| ($po_cond!="" && $search_type==4))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
		if ($po_ids=="")
		{
			$po_idsCond="";
			echo "Not Found."; die;
		}
	}
	
	if ($po_ids!="") $po_idsCond=" and b.po_break_down_id in ($po_ids)"; else $po_idsCond="";
	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
	}
	unset($po_sql_res);
	
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date = "and a.booking_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
		$wo_year="YEAR(a.insert_date)";
		$pre_cost_trims_cond="group_concat(b.pre_cost_fabric_cost_dtls_id)";
		//$gmts_item_cond="group_concat(b.gmt_item)";
		$po_id_cond="group_concat(b.po_break_down_id)";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date = "and a.booking_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
		$wo_year="to_char(a.insert_date,'YYYY')";
		$pre_cost_trims_cond="listagg(b.pre_cost_fabric_cost_dtls_id,',') within group (order by b.pre_cost_fabric_cost_dtls_id)";
		//$gmts_item_cond="listagg(b.gmt_item,',') within group (order by b.gmt_item)";
		$po_id_cond="listagg(b.po_break_down_id,',') within group (order by b.po_break_down_id)";
	} 
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (0=>$comp,1=>$buyer_arr);
	
	//$sql= "select $wo_year as year, id, booking_type, booking_no, company_id, buyer_id, job_no, booking_date from  wo_booking_mst where booking_type=6 and status_active=1 and is_deleted=0 $booking_date $company $woorder_cond $year_cond order by booking_no"; 
	$sql= "select $wo_year as year, a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, $pre_cost_trims_cond as pre_cost_trims_id, $po_id_cond as po_id from  wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=2 and a.status_active=1 and a.lock_another_process!=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_date $company $woorder_cond $year_cond $po_idsCond group by a.insert_date, a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id order by a.id DESC";
	//echo $sql;die;
	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="840" >
        <thead>
            <th width="30">SL</th>
            <th width="60">W/O Year</th>
            <th width="60">W/O No</th>
            <th width="70">W/O Date</th>
            <th width="100">Buyer</th>
            <th width="100">Buyer Po</th>
            <th width="100">Buyer Style</th>
            <th width="100">Buyer Job</th>
        </thead>
        </table>
        <div style="width:840px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table" id="list_view">
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$expo_id=array_unique(explode(",",$row[csf('po_id')]));
				$buyer_name=""; $po_no=""; $buyer_style=""; $buyer_job="";
				foreach ($expo_id as $po_id)
				{
					if($buyer_name=="") $buyer_name=$buyer_arr[$buyer_po_arr[$po_id]['buyer']]; else $buyer_name.=','.$buyer_arr[$buyer_po_arr[$po_id]['buyer']];
					if($po_no=="") $po_no=$buyer_po_arr[$po_id]['po']; else $po_no.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
					if($buyer_job=="") $buyer_job=$buyer_po_arr[$po_id]['job']; else $buyer_job.=','.$buyer_po_arr[$po_id]['job'];
				}
				
				$buyer_name=implode(", ",array_unique(explode(",",$buyer_name)));
				$po_no=implode(", ",array_unique(explode(",",$po_no)));
				$buyer_style=implode(", ",array_unique(explode(",",$buyer_style)));
				$buyer_job=implode(", ",array_unique(explode(",",$buyer_job)));
				
				$expre_cost_trims_id=array_unique(explode(",",$row[csf('pre_cost_trims_id')]));
				$body_part_name=""; $embl_name=""; 
				foreach ($expre_cost_trims_id as $pre_cost_id)
				{
					if($body_part_name=="") $body_part_name=$body_part[$pre_cost_trims_arr[$pre_cost_id]['body_part_id']]; else $body_part_name.=','.$body_part[$pre_cost_trims_arr[$pre_cost_id]['body_part_id']];
					
					if($pre_cost_trims_arr[$pre_cost_id]['emb_name']==1) $emb_type=$emblishment_print_type;
					else if($pre_cost_trims_arr[$pre_cost_id]['emb_name']==2) $emb_type=$emblishment_embroy_type;
					else if($pre_cost_trims_arr[$pre_cost_id]['emb_name']==3) $emb_type=$emblishment_wash_type;
					else if($pre_cost_trims_arr[$pre_cost_id]['emb_name']==4) $emb_type=$emblishment_spwork_type;
					else if($pre_cost_trims_arr[$pre_cost_id]['emb_name']==5) $emb_type=$emblishment_gmts_type;
				}
				
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('booking_no')].'_'.$row[csf('currency_id')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60" align="center"><? echo $row[csf('year')]; ?></td>
                    <td width="60" align="center"><? echo $row[csf('booking_no_prefix_num')]; ?></td>
                    <td width="70"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_name; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $po_no; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_style; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_job; ?></td>
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

if( $action=='order_dtls_list_view' )
{
	$data=explode('_',$data);
	$trim_group= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$section_arr= return_library_array("select a.id, a.section_name from lib_section a,lib_department b,lib_division c where c.company_id= $data[2] and b.division_id=c.id and a.department_id=b.id and a.status_active=1 and b.status_active=1 and c.status_active=1",'id','section_name');
	
	$po_sql= "select a.buyer_name,a.style_ref_no ,b.id, b.po_number from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1";
		$po_qry_result=sql_select($po_sql); $po_dtls_arr=array();
		foreach ($po_qry_result as $row)
		{
			$po_dtls_arr[$row[csf("id")]]['po_number']=$row[csf("po_number")];
			$po_dtls_arr[$row[csf("id")]]['buyer_name']=$row[csf("buyer_name")];
			$po_dtls_arr[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
		}

	$buyer_arr= return_library_array("select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[2]' group by buy.id, buy.buyer_name order by buy.buyer_name",'id','buyer_name');
	if($data[3]==1)
	{
		$sql= "select  a.id, a.booking_type, a.booking_no, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, b.trim_group ,b.delivery_date,b.fabric_description, b.uom, c.id as cons_id, c.po_break_down_id,c.item_color, c.item_size,c.requirment,c.description,c.rate,c.amount from  wo_booking_mst a, wo_booking_dtls b, wo_trim_book_con_dtls c where a.booking_no=b.booking_no and a.booking_type=2 and c.wo_trim_booking_dtls_id=b.id and c.requirment>0 and a.id=$data[0] and a.status_active=1 and a.lock_another_process!=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
		$qry_result=sql_select($sql); $i=1;
		foreach ($qry_result as $row)
		{
			if($row[csf('description')]=='')
			{
				$description=$row[csf('fabric_description')];
			}
			else
			{
				$description=$row[csf('description')];
			}
			$dom_rate=$data[2]*$row[csf('rate')];
			$dom_amount=$data[2]*$row[csf('amount')];
			?>
			<tr id="tr_<? echo $i; ?>">
	        	<td width="30" ><input type="text" name="txtSl[]" id="txtSl_<? echo $i; ?>" class="text_boxes_numeric" style="width:17px" value="<? echo $i; ?>" readonly /></td>
	        	<td width="100" ><input type="text" name="txtCustOrder[]" id="txtCustOrder_<? echo $i; ?>" class="text_boxes" style="width:87px" value="<? echo $po_dtls_arr[$row[csf('po_break_down_id')]]['po_number']; ?>" readonly /><input type="hidden" name="txtCustOrderId[]" id="txtCustOrderId_<? echo $i; ?>" class="text_boxes_numeric" style="width:87px" value="<? echo $row[csf('po_break_down_id')]; ?>" readonly /></td>
	            <td width="80" ><input type="text" name="txtCustStyle[]" id="txtCustStyle_<? echo $i; ?>" class="text_boxes" style="width:67px" value="<? echo $po_dtls_arr[$row[csf('po_break_down_id')]]['style_ref_no']; ?>" readonly /></td>
	            <td width="100" ><?
					echo create_drop_down( "cboCustBuyer_".$i, 100, $buyer_arr,"", 1, "-- Select Buyer --", $po_dtls_arr[$row[csf('po_break_down_id')]]['buyer_name'],'',0,'','','','','','',"cboCustBuyer[]");
				?></td>
	            <td width="70" ><?
					echo create_drop_down( "cboSection_".$i, 70, $section_arr,"", 1, "-- Select Buyer --", '','',0,'','','','','','',"cboSection[]");
				?></td>
	            <td width="70" ><?
					echo create_drop_down( "cboItemGroup_".$i, 70, $trim_group,"", 1, "-- Select Group --", $row[csf('trim_group')],'',0,'','','','','','',"cboItemGroup[]");
				?></td>
	            <td width="100" ><input type="text" name="txtItemDes[]" id="txtItemDes_<? echo $i; ?>" class="text_boxes_numeric" style="width:87px" value="<? echo $description; ?>"; readonly /></td>
	            <td width="60" ><input type="text" name="txtItemColor[]" id="txtItemColor_<? echo $i; ?>" class="text_boxes" style="width:47px" value="<? echo $row[csf('item_color')]; ?>" readonly /></td>
	            <td width="60" ><input type="text" name="txtItemSize[]" id="txtItemSize_<? echo $i; ?>" class="text_boxes_numeric" style="width:47px" value="<? echo $row[csf('item_size')]; ?>" readonly /></td>
	            <td width="60" ><?
					echo create_drop_down( "cboUom_".$i, 100, $unit_of_measurement,"", 1, "-- Select UOM --", $row[csf('uom')],'',0,'','','','','','',"cboUom[]");
				?></td>
	            <td width="100" ><input type="text" name="txtQty[]" id="txtQty_<? echo $i; ?>" class="text_boxes_numeric" style="width:87px"  value="<? echo $row[csf('requirment')]; ?>" readonly /></td>
	            <td width="60" ><input type="text" name="txtRate[]" id="txtRate_<? echo $i; ?>" class="text_boxes_numeric" style="width:47px" value="<? echo $row[csf('rate')]; ?>" readonly /></td>
	            <td width="100" ><input type="text" name="txtAmount[]" id="txtAmount_<? echo $i; ?>" class="text_boxes_numeric" style="width:87px" value="<? echo $row[csf('amount')]; ?>" readonly /></td>
	            <td width="60" ><input type="text" name="txtDomRate[]" id="txtDomRate_<? echo $i; ?>" class="text_boxes_numeric" style="width:47px" value="<? echo $dom_rate; ?>" readonly /></td>
	            <td width="100" ><input type="text" name="txtDomAmount[]" id="txtDomAmount_<? echo $i; ?>" class="text_boxes_numeric" style="width:87px" value="<? echo $dom_amount; ?>" readonly /></td>
	            <td width="70" ><input type="text" name="txtDelDate[]" id="txtDelDate_<? echo $i; ?>" class="datepicker" style="width:57px" value="<? echo change_date_format($row[csf('delivery_date')]); ?>" readonly />
	            <td style="display:none"><?
					echo create_drop_down( "cboStausId_".$i, 50,$row_status,'', 1, 'Select',0,'',0,'','','','','','',"cboStausId[]");
					?>
					<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_"<? echo $i; ?> class="text_boxes_numeric" style="width:87px" readonly />
                    <input type="hidden" name="consIdDtls[]" id="consIdDtls_"<? echo $i; ?> class="text_boxes_numeric" style="width:87px" readonly />
				</td>
	        </tr>
        <?
        $i++;
		}
	}
	exit();
}


if ($action=="save_update_delete")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//id, job_no_mst, order_no,  order_quantity, order_uom, rate,  amount, order_rcv_date, delivery_date,  cust_buyer, cust_style_ref, main_process_id,  process_id, remarks, delay_for,   inserted_by, insert_date, updated_by,   update_date, status_active, is_deleted,  smv, grey_req, mst_id,    order_id, wastage, buyer_po_id,   gmts_item_id, embl_type, body_part, booking_dtls_id
		//txtSl*txtCustOrder*txtCustStyle*cboCustBuyer*cboSection*cboItemGroup*txtItemDes*txtItemSize*cboUom*txtQty*txtRate*txtAmount*txtDomRate*txtDomAmount*txtDelDate*cboStausId*updateIdDtls
		
		$id=return_next_id( "id","subcon_ord_mst", 1 ) ;
		$new_rcv_number		=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'TOR', date("Y",time()), 5, "select job_no_prefix,job_no_prefix_num from subcon_ord_mst where company_id=$cbo_company_id and entry_form=255  order by id desc ", "job_no_prefix", "job_no_prefix_num" ));

		$field_array="id, job_no_prefix, job_no_prefix_num, subcon_job, entry_form, company_id, within_group ,party_id ,order_no , order_id, currency_id, receive_date, exchange_rate, remarks, status_active, inserted_by, insert_date";
		

		$data_array="(".$id.",'".$new_rcv_number[1]."','".$new_rcv_number[2]."','".$new_rcv_number[0]."',255,".$cbo_company_id.",".$cbo_source_id.",".$cbo_customer_id.",".$txt_wo_no.",".$txt_wo_id.",".$cbo_currency_id.",".$txt_rcv_date.",".$txt_ex_rate.",".$txt_remarks.",".$cbo_staus_id.",".$user_id.",'".$pc_date_time."')";

		$dtlsId=return_next_id( "id","subcon_ord_dtls", 1 ) ;
		$breakId=return_next_id( "id","subcon_ord_breakdown", 1 ) ;
		$trims_chk_arr=array(); $qnty_sum=''; $amount_sum=''; $avg_rate='';
		$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
		$size_library_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
		$data_array_dtls 	= $data_array_break="";  $add_commaa=0; $add_commadtls=0; $dtls_arr=array(); $breakdown_arr=array();
		for($i=1;$i<=$total_row;$i++)
		{
			$txtCustOrder="txtCustOrder_".$i;
			$txtCustOrderId="txtCustOrderId_".$i;
			$txtCustStyle="txtCustStyle_".$i;
			$cboCustBuyer="cboCustBuyer_".$i;
			$cboSection="cboSection_".$i;
			$cboItemGroup="cboItemGroup_".$i;
			$txtItemDes="txtItemDes_".$i;
			$txtItemColor="txtItemColor_".$i;
			$txtItemSize="txtItemSize_".$i;
			$cboUom="cboUom_".$i;
			$txtQty="txtQty_".$i;
			$txtRate="txtRate_".$i;
			$txtAmount="txtAmount_".$i;
			$txtDomRate="txtDomRate_".$i;
			$txtDomAmount="txtDomAmount_".$i;
			$txtDelDate="txtDelDate_".$i;
			$cboStausId="cboStausId_".$i;
			$updateIdDtls="updateIdDtls_".$i;
			$item=str_replace("'","",$$cboSection)."_".str_replace("'","",$$cboItemGroup)."_".str_replace("'","",$$txtItemDes)."_".str_replace("'","",$$txtItemColor)."_".str_replace("'","",$$txtItemSize)."_".str_replace("'","",$$cboUom);
			$str_wo_no=str_replace("'","",$txt_wo_no).'_'.str_replace("'","",$txt_wo_id);
			$dtls_arr[$str_wo_no][str_replace("'","",$$txtCustOrderId)][str_replace("'","",$$cboUom)]['qty']+=str_replace("'","",$$txtQty);
			$dtls_arr[$str_wo_no][str_replace("'","",$$txtCustOrderId)][str_replace("'","",$$cboUom)]['amt']+=str_replace("'","",$$txtAmount);
			
			$breakdown_arr[$str_wo_no][str_replace("'","",$$txtCustOrderId)][str_replace("'","",$$cboUom)].=$item."_".str_replace("'","",$$txtQty)."_".str_replace("'","",$$txtRate)."_".str_replace("'","",$$txtAmount)."_".str_replace("'","",$$txtDomRate);
			

			$qnty_sum +=$$txtQty; $amount_sum +=$$txtAmount; 
			$avg_rate=$amount_sum/$qnty_sum;
			if(!in_array($item, $trims_chk_arr, true))
			{
	    		array_push( $trims_chk_arr, $item);

	    		$field_array_dtls="id,mst_id, job_no_mst,buyer_po_id, order_id, order_no, cust_style_ref, cust_buyer, order_quantity, rate, ,amount, status_active, inserted_by, insert_date";
	    		$data_array_dtls .="(".$dtlsId.",".$id.",".$new_rcv_number[0].",'".str_replace("'","",$$txtCustOrderId)."','".str_replace("'","",$txt_wo_id)."','".str_replace("'","",$txt_wo_no)."','".str_replace("'","",$$txtCustStyle)."','".str_replace("'","",$$cboCustBuyer)."','".$qnty_sum."','".$avg_rate."','".$$amount_sum."','".str_replace("'","",$$cboStausId)."',".$user_id.",'".$pc_date_time."')";
	    		$dtlsId++; $qnty_sum=0; $amount_sum=0; $avg_rate=0;

			}
			
			if(str_replace("'","",$$txtItemColor)!="")
			{ 
				if (!in_array(str_replace("'","",$txtItemColor),$new_array_color))
				{
					$color_id = return_id( str_replace("'","",$txtItemColor), $color_library_arr, "lib_color", "id,color_name","255");  
					//echo $$txtColorName.'='.$color_id.'<br>';
					$new_array_color[$color_id]=str_replace("'","",$txtItemColor);
					
				}
				else $color_id =  array_search(str_replace("'","",$txtItemColor), $new_array_color); 
			}
			else
			{
				$color_id=0;
			}
			if(str_replace("'","",$$txtItemSize)!="")
			{ 
				if (!in_array(str_replace("'","",$$txtItemSize),$new_array_size))
				{
					$size_id = return_id( str_replace("'","",$$txtItemSize), $size_library_arr, "lib_size", "id,size_name","255");  
					//echo $$txtColorName.'='.$color_id.'<br>';
					$new_array_size[$size_id]=str_replace("'","",$$txtItemSize);
				}
				else $size_id =  array_search(str_replace("'","",$$txtItemSize), $new_array_size); 
			}
			else
			{
				$size_id=0;
			}
			$field_array_break="id, mst_id, order_id, job_no_mst, description, item_id, color_id, size_id, qnty, rate, amount, status_active, inserted_by, insert_date";
			$data_array_break .="(".$breakId.",".$dtlsId.",".$txt_wo_id.",".$new_rcv_number[0].",'".str_replace("'","",$$txtItemDes)."','".str_replace("'","",$$txtAmount)."',".$color_id.",".$size_id.",'".str_replace("'","",$$txtQty)."','".str_replace("'","",$$txtRate)."','".str_replace("'","",$$txtAmount)."','".str_replace("'","",$$cboStausId)."',".$user_id.",'".$pc_date_time."')";
			$breakId++;
			//id, mst_id, order_id,   item_id, color_id, size_id,  qnty, rate, amount,   excess_cut, plan_cut, process_loss,   gsm, embellishment_type, description,   dia_width_type, grey_dia, finish_dia,    body_part, job_no_mst, book_con_dtls_id
			
		}
		//echo "5**insert into com_export_pi_dtls (".$field_array.") Values ".$data_array."";die;
		unset($trims_chk_arr);
		$rID=sql_insert("subcon_ord_mst",$field_array,$data_array,0);
		$rID1=sql_insert("subcon_ord_dtls",$field_array_dtls,$data_array_dtls,0);
		$rID2=sql_insert("subcon_ord_breakdown",$field_array_break,$data_array_break,0);
		
		//echo "5**".$rID."**".$rID2;die;
		
		if($db_type==0)
		{
			if($rID && $rID1 && $rID2)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".$new_rcv_number[0];
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1 && $rID2)
			{
				oci_commit($con);
				echo "0**".$id."**".$new_rcv_number[0];
			}
			else
			{
				oci_rollback($con); 
				echo "5**0";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{ 
		$sql_app=sql_select("select approved from com_export_pi_mst where id=$update_id and approved=1");
		if(count($sql_app)>0)
		{
			echo "17**1**1"; 
			die;	
		}

		$do_value=return_field_value("sum(do_value) as do_value","yarn_delivery_order_do_dtls","pi_id=$update_id and status_active=1 and is_deleted=0","do_value");
		if($do_value>str_replace("'","",$txt_total_amount_net))
		{
			echo "16**".$do_value; 
			die;
		}
		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$lc_array=sql_select("select a.id, a.lc_value, a.tolerance  from com_export_lc a,com_export_lc_order_info b where a.id=b.com_export_lc_id and b.pi_id=$update_id and a.status_active=1 and b.status_active=1" );
		if($lc_array[0][csf('lc_value')]!='')
		{
			$new_lc_value=($lc_array[0][csf('lc_value')]-str_replace("'", '', $txt_total_amount_net_old))+str_replace("'", '', $txt_total_amount_net);
		//echo "10**".$lc_array[0][csf('lc_value')]."**".str_replace("'", '', $txt_total_amount_net_old)."**".str_replace("'", '', $txt_total_amount_net); die; 
			$invoice_value=return_field_value("sum(net_invo_value)  as net_invo_value","com_export_invoice_ship_mst","lc_sc_id='".$lc_array[0][csf('id')]."' and is_lc =1 and status_active=1 and is_deleted=0","net_invo_value");

			if($invoice_value=='')
			{
				$invoice_value=0;
			}
			$minimum_lc_value=$invoice_value/(1+($lc_array[0][csf('tolerance')]/100));
			if($new_lc_value<$minimum_lc_value)
			{
				echo "15**"; die;
			}
		}
		else
		{
			$new_lc_value='';
		}

		//echo "10**".$lc_array[0][csf('lc_value')]."**".str_replace("'", '', $txt_total_amount_net_old)."**".str_replace("'", '', $txt_total_amount_net); die; 10****125**125 
		$field_array_update="color_id*count_name*composition*comm_rate*yarn_type*uom*quantity*rate*amount*net_pi_rate*net_pi_amount*comm_amount*updated_by*update_date";
		$field_array_update_qty="quantity*rate*amount*net_pi_rate*net_pi_amount*comm_amount*updated_by*update_date";

		$field_array="id,pi_id,work_order_no, work_order_id, work_order_dtls_id,color_id,count_name,composition,comm_rate,yarn_type,uom,quantity,rate,amount,net_pi_rate,net_pi_amount,comm_amount,inserted_by,insert_date"; 

		$field_array_update2="total_amount*upcharge*discount*net_total_amount";
		$field_array_lc="lc_value*updated_by*update_date";
		if($cbo_currency_id==1)
		{
			$txt_total_amount=number_format($txt_total_amount,$dec_place[4],'.','');
			$txt_total_amount_net=number_format($txt_total_amount_net,$dec_place[4],'.','');
		}
		else
		{
			$txt_total_amount=number_format($txt_total_amount,$dec_place[5],'.','');
			$txt_total_amount_net=number_format($txt_total_amount_net,$dec_place[5],'.','');
		}

		$data_array_update2=$txt_total_amount."*'".$txt_upcharge."'*'".$txt_discount."'*".$txt_total_amount_net;
		$data_array_lc=$new_lc_value."*".$user_id."*'".$pc_date_time."'";

		$id = return_next_id( "id","com_export_pi_dtls", 1 );
		$data_array==""; $data_array_update=array(); $item_dup_chk_arr=array();
		$remove_id=chop($data_delete,",");

		for($i=1;$i<=$total_row;$i++)
		{
			$updateIdDtls="updateIdDtls_".$i;
			$workOrderNo="workOrderNo_".$i;
			$workOrderId="hideWoId_".$i;
			$workOrderDtlsId="hideWoDtlsId_".$i;
			//$colorId="colorId_".$i;
			$colorName="colorName_".$i;
			$countName="countName_".$i;
			$Composition="yarnComposition_".$i;
			$commRate="commRate_".$i;
			$type="type_".$i;
			$uom="uom_".$i;
			$quantity="quantity_".$i;
			$rate="rate_".$i;
			$amount="amount_".$i;
			$hideDoChk="hideDoChk_".$i;
			$hideDoQty="hideDoQty_".$i;

			$perc=(str_replace("'","",$$amount)/$txt_total_amount)*100;
			$net_pi_amount=($perc*$txt_total_amount_net)/100;
			$net_pi_rate=$net_pi_amount/str_replace("'","",$$quantity);

			if($cbo_currency_id==1)
				$net_pi_amount=number_format($net_pi_amount,$dec_place[4],'.','');
			else
				$net_pi_amount=number_format($net_pi_amount,$dec_place[5],'.','');

			$net_pi_rate=number_format($net_pi_rate,$dec_place[3],'.','');
			//echo "5**".str_replace("'","",$$commRate)."nazim"; die;
			if($cbo_sales_com_bsis==1)
			{
				$commAmount=number_format((($net_pi_amount*str_replace("'","",$$commRate))/100),$dec_place[3],'.','');
			}
			else if($cbo_sales_com_bsis==2 || $cbo_sales_com_bsis==3)
			{
				$commAmount=number_format((str_replace("'","",$$quantity)*str_replace("'","",$$commRate)),$dec_place[3],'.','');
			}
			else $commAmount='';

			if(str_replace("'","",$$colorName)!="")
			{ 
				if (!in_array(str_replace("'","",$$colorName),$new_array_color))
				{
					$color_id = return_id( str_replace("'","",$$colorName), $color_library, "lib_color", "id,color_name");  
					$new_array_color[$color_id]=str_replace("'","",$$colorName);
				}
				else 
				{
					$color_id =  array_search(str_replace("'","",$$colorName), $new_array_color);
				}
			}
			else
			{
				$color_id=0;
			}
			//echo "5**nazim".$$updateIdDtls; //die;
			$item=str_replace("'","",$color_id)."_".str_replace("'","",$$countName)."_".str_replace("'","",$$Composition)."_".str_replace("'","",$$type);

			if(!in_array($item, $item_dup_chk_arr, true))
			{
        		array_push( $item_dup_chk_arr, $item);
    		}
    		else
    		{
    			echo "26**"; die; 			
    		}

			if(str_replace("'","",$$updateIdDtls)!="")
			{
				if(str_replace("'",'',$$hideDoChk) !=1)
				{
					$id_arr[]=str_replace("'",'',$$updateIdDtls);
					$data_array_update[str_replace("'",'',$$updateIdDtls)] = explode("*",("'".str_replace("'","",$color_id)."'*'".str_replace("'","",$$countName)."'*'".str_replace("'","",$$Composition)."'*".$$commRate."*'".str_replace("'","",$$type)."'*'".str_replace("'","",$$uom)."'*".$$quantity."*".$$rate."*".$$amount."*'".$net_pi_rate."'*".$net_pi_amount."*'".$commAmount."'*'".$user_id."'*'".$pc_date_time."'"));
				}
				else if( str_replace("'",'',$$hideDoChk)==1 && (str_replace("'",'',$$quantity) >= str_replace("'",'',$$hideDoQty)))
				{
					$id_arr_qty[]=str_replace("'",'',$$updateIdDtls);
					$data_array_update_qty[str_replace("'",'',$$updateIdDtls)] = explode("*",($$quantity."*".$$rate."*".$$amount."*'".$net_pi_rate."'*".$net_pi_amount."*'".$commAmount."'*'".$user_id."'*'".$pc_date_time."'"));
				}
				else
				{
					echo "25**".str_replace("'",'',$$hideDoQty); 
					die;
				}
				
			}
			else
			{
				if($data_array!="") $data_array.=",";
				$data_array .="(".$id.",".$update_id.",'".str_replace("'","",$$workOrderNo)."','".str_replace("'","",$$workOrderIdtype)."','".str_replace("'","",$$workOrderDtlsId)."','".str_replace("'","",$color_id)."','".str_replace("'","",$$countName)."','".str_replace("'","",$$Composition)."',".$$commRate.",'".str_replace("'","",$$type)."','".str_replace("'","",$$uom)."',".$$quantity.",".$$rate.",".$$amount.",'".$net_pi_rate."',".$net_pi_amount.",'".$commAmount."',".$user_id.",'".$pc_date_time."')";

				$id=$id+1;
			}
		}


		//echo "5**insert into com_export_pi_dtls (".$field_array.") Values ".$data_array."";die;
		$rID=true; $rID2=true; $rID4=true; $rID5=true;  $rID6=true;  $rID7=true; unset($item_dup_chk_arr);
		if(count($data_array_update)>0)
		{
			//echo "10**".bulk_update_sql_statement( "com_export_pi_dtls", "id", $field_array_update, $data_array_update, $id_arr ); die;
			$rID=execute_query(bulk_update_sql_statement( "com_export_pi_dtls", "id", $field_array_update, $data_array_update, $id_arr ));
		}
		if(count($data_array_update_qty)>0)
		{
			//echo "10**".bulk_update_sql_statement( "com_export_pi_dtls", "id", $field_array_update, $data_array_update, $id_arr ); die;
			$rID=execute_query(bulk_update_sql_statement( "com_export_pi_dtls", "id", $field_array_update_qty, $data_array_update_qty, $id_arr_qty ));
		}
		if($data_array!="")
		{
			$rID2=sql_insert("com_export_pi_dtls",$field_array,$data_array,0);
		}
		//echo "10**".$txt_deleted_id; die;
		if($txt_deleted_id!="")
		{
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$user_id."*'".$pc_date_time."'*0*1";

			$rID4=sql_multirow_update("com_export_pi_dtls",$field_array_status,$data_array_status,"id",$txt_deleted_id,0);
			if($flag==1)
			{
				if($rID4) $flag=1; else $flag=0; 
			} 
		}

		if($remove_id!="")
		{
			$field_array_del="updated_by*update_date*status_active*is_deleted";
			$data_array_del=$user_id."*'".$pc_date_time."'*0*1";

			$rID5=sql_multirow_update("com_export_pi_dtls",$field_array_del,$data_array_del,"id",$remove_id,1);
			if($flag==1)
			{
				if($rID5) $flag=1; else $flag=0; 
			} 
		}


		$rID3=sql_update("com_export_pi_mst",$field_array_update2,$data_array_update2,"id",$update_id,1);
		if($lc_array[0][csf('id')]!='')
		{
			$rID6=sql_update("com_export_lc",$field_array_lc,$data_array_lc,"id",$lc_array[0][csf('id')],1);
		}

		//echo "5**".$rID."**".$rID2."**".$rID3."**".$rID4."**".$rID5."**".$rID6."**".$rID7;die;
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7 )
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**".$txt_total_amount_net;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6)
			{
				oci_commit($con); 
				echo "1**".str_replace("'", '', $update_id)."**".$txt_total_amount_net;
			}
			else
			{
				oci_rollback($con);
				echo "6**0";
			}
		}
		disconnect($con);
		die;
	}
}
?>