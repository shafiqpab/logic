<?
include('../../../includes/common.php');
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	if($data[1]==1) $dropdown_name="cbo_location_name";
	else $dropdown_name="cbo_party_location";
	echo create_drop_down( $dropdown_name, 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",1 );	
	exit();
}

if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);

	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";
	
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $data[2], "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
	}	
	exit();	 
} 


if ($action=="devivery_workorder_popup")
{
	echo load_html_head_contents("Job Popup Info","../../../", 1, 1, $unicode,'','');
	//echo $data; die;
	?>
	<script>
		function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_popup(type,within_group)
		{
			var company = $('#cbo_company_name').val();
			var party_name = $('#cbo_party_name').val();
			var location_name = $('#cbo_location_name').val();
			load_drop_down( 'trims_delivery_entry_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0)
			{
				$('#search_by_td').html('System ID');
			}
			else if(val==2)
			{
				$('#search_by_td').html('W/O No');
			}
			else if(val==3)
			{
				$('#search_by_td').html('Buyer Job');
			}
			else if(val==4)
			{
				$('#search_by_td').html('Buyer Po');
			}
			else if(val==5)
			{
				$('#search_by_td').html('Buyer Style');
			}
		}
	</script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="940" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead> 
                <tr>
                    <th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>               	 
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="100">Within Group</th>                           
                    <th width="140">Party Name</th>
                    <th width="100">Search By</th>
                    <th width="100" id="search_by_td">Receive System ID</th>
                    <th width="100">Year</th>
                    <th width="170">Date Range</th>                            
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                </tr>           
            </thead>
            <tbody>
                <tr class="general">
                    <td><input type="hidden" id="selected_job"><? $data=explode("_",$data); ?>  <!--  echo $data;-->
                        <? 
                        echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
                    </td>
                    <td>
                        <?php echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 0, "--  --", $data[3], "fnc_load_party_popup(1,this.value);" ); ?>
                    </td>
                    <td id="buyer_td">
                        <? echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );   	 
                        ?>
                    </td>
                    <td>
						<?
                            $search_by_arr=array(1=>"System ID",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                    </td>
                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 100, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value, 'create_del_workorder_list_view', 'search_div', 'trims_delivery_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" valign="middle">
                            <? echo load_month_buttons();  ?>
                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" valign="top" id=""><div id="search_div"></div></td>
                    </tr>
                </tbody>
            </table>    
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}
	
if($action=="create_del_workorder_list_view")
{	
	$data=explode('_',$data);
	$party_id=str_replace("'","",$data[1]);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}

	if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	//echo $search_type; die;
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
	}	

	if($party_id!=0) $party_id_cond=" and a.party_id='$party_id'"; else $party_id_cond="";

	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";
	}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	if($within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
	
	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	//echo $po_ids;
	if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$buyer_po_arr=array();
	if($within_group==1)
	{
		$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		}
		unset($po_sql_res);
	}
	$buyer_po_id_str=""; $buyer_po_no_str=""; $buyer_po_style_str="";
	if($db_type==0) 
	{
		$ins_year_cond="year(a.insert_date)";
		$color_id_str=",group_concat(c.color_id) as color_id";
		if($within_group==1)
		{
			$buyer_po_id_str=",group_concat(b.buyer_po_id) as buyer_po_id";
		}
		else
		{
			$buyer_po_no_str=",group_concat(b.buyer_po_no) as buyer_po_id";
			$buyer_po_style_str=",group_concat(b.buyer_style_ref) as buyer_style";
		}
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str=",listagg(c.color_id,',') within group (order by c.color_id) as color_id";
		
		if($within_group==1)
		{
			$buyer_po_id_str=",listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)  as buyer_po_id";
		}
		else
		{
			$buyer_po_no_str=",listagg(b.buyer_po_no,',') within group (order by b.id) as buyer_po_no";
			$buyer_po_style_str=",listagg(b.buyer_style_ref,',') within group (order by b.id) as buyer_style";
		}
	}
	$sql= "select a.id, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date $color_id_str $buyer_po_id_str $buyer_po_no_str $buyer_po_style_str
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c , trims_production_mst d  
	where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.id=d.received_id and a.status_active=1 and b.status_active=1 and d.status_active=1 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $withinGroup and b.id=c.mst_id  
	group by a.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date
	order by a.id DESC";
	//echo $sql;

	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="885" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Job No</th>
            <th width="60">Year</th>
            <th width="120">W/O No</th>
            <th width="100">Buyer Po</th>
            <th width="100">Buyer Style</th>
            <th width="80">Ord Receive Date</th>
            <th width="80">Delivery Date</th>
            <th>Color</th>
        </thead>
        </table>
        <div style="width:885px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="865" class="rpt_table" id="tbl_po_list">
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$excolor_id=array_unique(explode(",",$row[csf('color_id')]));
				$color_name="";	
				foreach ($excolor_id as $color_id)
				{
					if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
				}
				if($within_group==1)
				{
					$buyer_po=""; $buyer_style="";
					$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
					foreach($buyer_po_id as $po_id)
					{
						if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
						if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
					}
					$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
					$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				}
				else
				{
					$buyer_po=implode(",",array_unique(explode(",",$row[csf('buyer_po_no')])));
					$buyer_style=implode(",",array_unique(explode(",",$row[csf('buyer_style')])));
				}
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('subcon_job')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="120"><? echo $row[csf('order_no')]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_po; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_style; ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>	
                    <td style="word-break:break-all"><? echo $color_name; ?></td>
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
 
if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id, subcon_job, company_id, location_id, party_id, currency_id, party_location, delivery_date, rec_start_date, rec_end_date, receive_date, within_group, party_location, order_id, order_no,exchange_rate,remarks from subcon_ord_mst where subcon_job='$data' and entry_form=255 and status_active=1" );
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('received_id').value          	= ".$row[csf("id")].";\n";
		//echo "document.getElementById('txt_job_no').value 				= '".$row[csf("subcon_job")]."';\n";  
		echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_within_group').value 		= '".$row[csf("within_group")]."';\n";  
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "fnc_load_party(1,".$row[csf("within_group")].");\n";	
	//echo "load_drop_down( 'requires/trims_delivery_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";	
		echo "document.getElementById('cbo_location_name').value 		= '".$row[csf("location_id")]."';\n";
	//echo "load_drop_down( 'requires/trims_delivery_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "document.getElementById('cbo_party_name').value			= '".$row[csf("party_id")]."';\n";
		echo "document.getElementById('cbo_currency').value				= '".$row[csf("currency_id")]."';\n";
		echo "fnc_load_party(2,".$row[csf("within_group")].");\n";	 
		echo "document.getElementById('cbo_party_location').value		= '".$row[csf("party_location")]."';\n";	
		echo "document.getElementById('txt_delivery_date').value		= '".change_date_format($row[csf("delivery_date")])."';\n"; 
		echo "document.getElementById('hid_order_id').value          	= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('txt_order_no').value         	= '".$row[csf("order_no")]."';\n";
		echo "$('#txt_order_no').attr('disabled','true')".";\n";
		echo "$('#cbo_within_group').attr('disabled','true')".";\n";
		echo "$('#cbo_party_name').attr('disabled','true')".";\n";
		echo "$('#cbo_currency').attr('disabled','true')".";\n";
		
		//echo "set_button_status(0,'".$_SESSION['page_permission']."', 'fnc_job_order_entry',1);\n";	
	}
	exit();	
}

if( $action=='dalivery_order_dtls_list_view' ) 
{
	$data=explode('_',$data);
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
	$tblRow=0;
	//die;
	//print_r($data_dreak_arr);
	if($data[0]==1)
	{
		//$sql = "select id, mst_id, job_no_mst, order_id, order_no, buyer_po_id, booking_dtls_id, order_quantity , order_uom, rate, amount, delivery_date, buyer_po_no, buyer_style_ref, buyer_buyer, section, item_group as trim_group, rate_domestic,  amount_domestic from subcon_ord_dtls where job_no_mst='$data[1]' and mst_id='$data[3]' and status_active=1 and is_deleted=0 order by id ASC";
		
		//$sql = "select a.id, a.mst_id, a.job_no_mst, a.order_id, a.order_no, a.buyer_po_id, a.booking_dtls_id, b.qnty as order_quantity , a.order_uom, a.rate, a.amount, a.delivery_date, a.buyer_po_no, a.buyer_style_ref, a.buyer_buyer, a.section, a.item_group as trim_group, a.rate_domestic,  a.amount_domestic,b.item_id, b.color_id, b.size_id,b.description from subcon_ord_dtls a,subcon_ord_breakdown b where   a.id=b.mst_id and  a.job_no_mst='$data[1]' and a.mst_id='$data[3]' and a.status_active=1 and a.is_deleted=0  order by a.id ASC";
		 
		// $sql= "select a.id, a.mst_id, a.job_no_mst,a.receive_dtls_id, a.book_con_dtls_id,a.booking_dtls_id,a.buyer_po_no,a.buyer_po_id, a.buyer_style_ref, a.item_description, a.color_id, a.size_id, a.sub_section, a.uom, a.job_quantity,  a.impression,a.material_color, b.id as break_id, b.order_id, b.job_no_mst, b.product_id, b.description, b.specification, b.unit, b.pcs_unit, b.cons_qty, b.process_loss, b.process_loss_qty, b.req_qty, b.remarks from trims_job_card_dtls a ,trims_job_card_breakdown b where a.id=b.mst_id and a.status_active=1 and a.mst_id=$data[1]";
		
		
		// Buyer's PO 	Section 	Trims Group 	Item Description 	Gmts Color 	Gmts Size 	Order UOM 	WO Qty 	Prod. Qty 	Cum. Delv Qty 	Curr. Delv Qnty 	Delv Balance 	Remarks 	Status
		$sql= "select a.id, a.trims_job, a.company_id, a.location_id, a.party_id,a.order_id,  a.order_no,a.received_no, a.section_id ,b.id as jobDtlsId,b.receive_dtls_id, b.book_con_dtls_id,b.booking_dtls_id,b.buyer_po_no,b.buyer_po_id, b.buyer_style_ref, b.item_description, b.color_id, b.size_id, c.received_id,b.job_quantity,d.qc_qty,b.uom,d.id as prod_dtls_id from trims_job_card_mst a, trims_job_card_dtls b, trims_production_mst c, trims_production_dtls d
		where a.entry_form=257 and a.trims_job=b.job_no_mst and a.id=b.mst_id and c.entry_form=269 and c.id=d.mst_id and a.id=c.job_id and b.id=d.job_dtls_id and c.status_active=1 and d.status_active=1 and a.status_active=1 and b.status_active=1 and c.received_id=$data[3]
		group by a.id, a.trims_job, a.company_id, a.location_id, a.party_id,  a.order_no,a.received_no, a.section_id ,b.id,b.receive_dtls_id, b.book_con_dtls_id,b.booking_dtls_id,b.buyer_po_no,b.buyer_po_id, b.buyer_style_ref, b.item_description, b.color_id, b.size_id, c.received_id,b.job_quantity,d.qc_qty,b.uom,d.id,a.order_id";
	}
	else
	{
		$sql = "select id, mst_id, booking_dtls_id, receive_dtls_id, job_dtls_id, production_dtls_id,  order_id, order_no, buyer_po_id, buyer_po_no,  buyer_style_ref, buyer_buyer, section,item_group as trim_group, order_uom, order_quantity,delevery_qty, claim_qty, remarks,color_id, size_id, 
   description, delevery_status, color_name, 
   size_name,workoder_qty from trims_delivery_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0 order by id ASC";
	}

//echo $sql; Buyer's PO 	Section 	Trims Group 	Item Description 	Gmts Color 	Gmts Size 	Order UOM 	WO Qty

	$rcv_arr=array(); $rcv_result =sql_select($sql);
	foreach($rcv_result as $rows)
	{
		$rcv_arr[$rows[csf('received_id')]][$rows[csf('section_id')]][$rows[csf('id')]][$rows[csf('jobDtlsId')]]['buyer_style_ref']=$rows[csf('buyer_style_ref')];
		$rcv_arr[$rows[csf('received_id')]][$rows[csf('section_id')]][$rows[csf('id')]][$rows[csf('jobDtlsId')]]['order_no']=$rows[csf('order_no')];
		$rcv_arr[$rows[csf('received_id')]][$rows[csf('section_id')]][$rows[csf('id')]][$rows[csf('jobDtlsId')]]['order_id']=$rows[csf('order_id')];
		$rcv_arr[$rows[csf('received_id')]][$rows[csf('section_id')]][$rows[csf('id')]][$rows[csf('jobDtlsId')]]['buyer_po_no']=$rows[csf('buyer_po_no')];
		$rcv_arr[$rows[csf('received_id')]][$rows[csf('section_id')]][$rows[csf('id')]][$rows[csf('jobDtlsId')]]['section_id']=$rows[csf('section_id')];
		$rcv_arr[$rows[csf('received_id')]][$rows[csf('section_id')]][$rows[csf('id')]][$rows[csf('jobDtlsId')]]['item_description']=$rows[csf('item_description')];
		$rcv_arr[$rows[csf('received_id')]][$rows[csf('section_id')]][$rows[csf('id')]][$rows[csf('jobDtlsId')]]['color_id']=$rows[csf('color_id')];
		$rcv_arr[$rows[csf('received_id')]][$rows[csf('section_id')]][$rows[csf('id')]][$rows[csf('jobDtlsId')]]['size_id']=$rows[csf('size_id')];
		$rcv_arr[$rows[csf('received_id')]][$rows[csf('section_id')]][$rows[csf('id')]][$rows[csf('jobDtlsId')]]['job_quantity']=$rows[csf('job_quantity')];
		$rcv_arr[$rows[csf('received_id')]][$rows[csf('section_id')]][$rows[csf('id')]][$rows[csf('jobDtlsId')]]['qc_qty']+=$rows[csf('qc_qty')];
		$rcv_arr[$rows[csf('received_id')]][$rows[csf('section_id')]][$rows[csf('id')]][$rows[csf('jobDtlsId')]]['uom']=$rows[csf('uom')];
	$rcv_arr[$rows[csf('received_id')]][$rows[csf('section_id')]][$rows[csf('id')]][$rows[csf('jobDtlsId')]]['jobDtlsId']+=$rows[csf('jobDtlsId')];
		$rcv_arr[$rows[csf('received_id')]][$rows[csf('section_id')]][$rows[csf('id')]][$rows[csf('jobDtlsId')]]['receive_dtls_id'].=$rows[csf('receive_dtls_id')].",";
		$rcv_arr[$rows[csf('received_id')]][$rows[csf('section_id')]][$rows[csf('id')]][$rows[csf('jobDtlsId')]]['prod_dtls_id'].=$rows[csf('prod_dtls_id')].",";
		$rcv_arr[$rows[csf('received_id')]][$rows[csf('section_id')]][$rows[csf('id')]][$rows[csf('jobDtlsId')]]['booking_dtls_id'].=$rows[csf('booking_dtls_id')].",";
	
	}
	
	
	
	$delevery_qty_trims_arr=array();
	$pre_sql ="Select job_dtls_id, sum(delevery_qty) as delevery_qty  from trims_delivery_dtls where status_active=1 and is_deleted=0 group by job_dtls_id";
	$pre_sql_res=sql_select($pre_sql);
	foreach ($pre_sql_res as $row)
	{
		$delevery_qty_trims_arr[$row[csf("job_dtls_id")]]['delevery_qty']=$row[csf("delevery_qty")];
		
	}
	unset($pre_sql_res);
	
	
	$trims_groups_arr=array();
	$trim_sql ="Select id, item_group,buyer_buyer  from subcon_ord_dtls where status_active=1 and is_deleted=0 and item_group is not null ";
	$trim_sql_res=sql_select($trim_sql);
	foreach ($trim_sql_res as $row)
	{
		$trims_groups_arr[$row[csf("id")]]['item_group']=$row[csf("item_group")];
		$trims_groups_arr[$row[csf("id")]]['buyer_buyer']=$row[csf("buyer_buyer")];
		
	}
	unset($trim_sql_res);
	
	
	//echo "<pre>";
	//print_r($trims_groups_arr); die;
	
	

//print_r($cumelative_arr);

	/*if($data[0]==2)
	{
		$qry_result=sql_select( "select id, mst_id, del_no_mst,  order_id, book_con_dtls_id, description, color_id, size_id, qnty from trims_delivery_breakdown where del_no_mst='$data[4]' order by id");	
		$data_dreak_arr=array(); $data_dreak=''; $add_comma=0; $k=1;
		foreach ($qry_result as $row)
		{
			if($row[csf('id')]=="") $row[csf('id')]=0;
			if($row[csf('mst_id')]=="") $row[csf('mst_id')]=0;
			if($row[csf('description')]=="") $row[csf('description')]=0;
			if($row[csf('color_id')]=="") $row[csf('color_id')]=0;
			if($row[csf('size_id')]=="") $row[csf('size_id')]=0;
			if($row[csf('qnty')]=="") $row[csf('qnty')]=0;
			if($row[csf('book_con_dtls_id')]=="") $row[csf('book_con_dtls_id')]=0;
			
			if(!in_array($row[csf('mst_id')],$temp_arr_mst_id))
			{
				$temp_arr_mst_id[]=$row[csf('mst_id')];
				//if($k!=1) {  }
				$add_comma=0; $data_dreak='';
				
			}
			//echo $add_comma.'='.$data_dreak.'='.$k.'<br>';
			$k++;
			
			if ($add_comma!=0) $data_dreak ="***";
			$data_dreak_arr[$row[csf('mst_id')]].=$row[csf('id')].'_'.$row[csf('mst_id')].'_'.$row[csf('description')].'_'.$row[csf('color_id')].'_'.$row[csf('size_id')].'_'.$row[csf('qnty')].'_'.$row[csf('book_con_dtls_id')].',';
			$add_comma++;
		}
	}*/
	//echo "<pre>";
	//print_r($data_dreak_arr);
	//echo $sql ;
	
	
	
$tblRow=0;

if($data[0]==1)
{
	foreach($rcv_arr as $rcv_arr_val)
	{
		foreach($rcv_arr_val as $section_id_val)
		{
			foreach($section_id_val as $job_id_val)
			{
				foreach($job_id_val as $jobDtlsId_val=> $row)
				{
					$tblRow++;
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
						<td style="display:none"><input id="txtWorkOrder<? echo $tblRow; ?>" name="txtWorkOrder[]" type="hidden" value="<? echo $row['order_no']; ?>" class="text_boxes" style="width:100px" readonly/>
							<input id="txtWorkOrderID<? echo $tblRow; ?>" name="txtWorkOrderID[]" type="hidden" value="<? echo $row['order_id']; ?>" class="text_boxes" style="width:100px"readonly/>
						</td>
						<td><input id="txtbuyerPo_<? echo $tblRow; ?>" name="txtbuyerPo[]" value="<? echo $row['buyer_po_no']; ?>" class="text_boxes" type="text"  style="width:100px" disabled />
							<input id="txtbuyerPoId_<? echo $tblRow; ?>" name="txtbuyerPoId[]" value="<? echo $row['buyer_po_id']; ?>" class="text_boxes" type="hidden" style="width:70px" readonly />
						</td>
						<td><? echo create_drop_down( "cboSection_".$tblRow, 90, $trims_section,"", 1, "-- Select Section --",$row['section_id'],'',1,'','','','','','',"cboSection[]"); ?></td>	
						<td style="display:none"><input id="txtstyleRef_<? echo $tblRow; ?>" name="txtstyleRef[]" value="<? echo $row['buyer_style_ref']; ?>" class="text_boxes" type="text"  style="width:100px" disabled/></td>
						<td style="display:none">
							<? 
							if($data[2]==1)
							{
								echo create_drop_down( "txtbuyer_".$tblRow, 100, "select id, buyer_name from lib_buyer where status_active=1","id,buyer_name", 1, "-- Select --",$trims_groups_arr[chop($row['receive_dtls_id'],",")]['buyer_buyer'], "",1,'','','','','','',"txtbuyer[]"); 
							}
							else
							{
								?>
								<input id="txtbuyer_<? echo $tblRow; ?>" name="txtbuyer[]" value="<? echo $trims_groups_arr[chop($row['receive_dtls_id'],",")]['buyer_buyer']; ?>" class="text_boxes" type="text"  style="width:87px" disabled />
								<?
							}
							?>
						</td>
								
						<td><? echo create_drop_down( "cboItemGroup_".$tblRow, 90, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- Select --",$trims_groups_arr[chop($row['receive_dtls_id'],",")]['item_group'], "",1,'','','','','','',"cboItemGroup[]"); ?></td>
						
						<td><input id="txtItem_<? echo $tblRow; ?>" name="txtItem[]" type="text" class="text_boxes" style="width:87px" value="<? echo $row['item_description']; ?>" placeholder="Display" readonly disabled/>
						<td><input id="txtcolor_<? echo $tblRow; ?>" name="txtcolor[]" type="text" class="text_boxes" style="width:57px" value="<? echo $color_library[$row['color_id']] ?>" placeholder="Display" readonly disabled/>
						<input id="txtcolorID_<? echo $tblRow; ?>" name="txtcolorID[]" type="hidden" class="text_boxes" style="width:57px"  value="<? echo $row['color_id'] ?>" placeholder="Display" readonly disabled/></td>
						<td><input id="txtsize_<? echo $tblRow; ?>" name="txtsize[]" type="text" class="text_boxes" style="width:57px" value="<? echo $size_arr[$row['size_id']] ?>"  placeholder="Display" readonly disabled/>
						<input id="txtsizeID_<? echo $tblRow; ?>" name="txtsizeID[]" type="hidden"  value="<? echo $row['size_id'] ?>"  class="text_boxes" style="width:57px" placeholder="Display" readonly disabled/></td>
						
						<td><? echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- Select --",$row['uom'],"", 1,'','','','','','',"cboUom[]"); ?>	</td>
						 <td><input id="txtWorkOrderQuantity_<? echo $tblRow; ?>" name="txtWorkOrderQuantity[]" value="<? echo $row['job_quantity']; ?>" class="text_boxes_numeric" type="text"  style="width:60px" placeholder="" readonly /></td>
						<td title="<? echo $row[csf('id')];?> "><input id="txtOrderQuantity_<? echo $tblRow; ?>" name="txtOrderQuantity[]" value="<? echo $row['qc_qty']; ?>" class="text_boxes_numeric" type="text"  style="width:60px" 
							onClick="openmypage_order_qnty1(1,'<? echo $receive_dtls_id; ?>',<? echo $tblRow; ?>)" placeholder="Click To Search" readonly /></td>
						
						<td><input id="txtPrevQty_<? echo $tblRow; ?>" name="txtPrevQty[]" value="<? if($delevery_qty_trims_arr[$row["jobDtlsId"]]['delevery_qty']!=''){echo $delevery_qty_trims_arr[$row["jobDtlsId"]]['delevery_qty'];}else{echo "0";} ?>" type="text"  class="text_boxes_numeric" style="width:60px" readonly /></td>
						<td><input id="txtCurQty_<? echo $tblRow; ?>" name="txtCurQty[]" onKeyUp="cal_values(<? echo $tblRow; ?>);" value=" " type="text" style="width:70px"  class="text_boxes_numeric"  /></td>
						<td style="display:none"><input id="txtClaimQty_<? echo $tblRow; ?>" name="txtClaimQty[]" value="<? echo $claim_qty; ?>" type="text"  class="text_boxes_numeric" style="width:57px" /></td>
						<td><input id="txtDelvBalance_<? echo $tblRow; ?>" name="txtDelvBalance[]"  value="<? echo ($row['job_quantity']-($delevery_qty_trims_arr[$row["jobDtlsId"]]['delevery_qty'])); ?>"   type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td> 
						<td><input id="txtRemarksDtls_<? echo $tblRow; ?>" name="txtRemarksDtls[]" value="<? echo $remarks; ?>" type="text"  class="text_boxes" style="width:77px" />
							
							<input id="hdnDtlsUpdateId_<? echo $tblRow; ?>" name="hdnDtlsUpdateId[]" type="hidden" value="<? echo $row[csf('buyer_style_ref')]; ?>" class="text_boxes_numeric" style="width:40px" />
							<input type="hidden" id="hdnDtlsdata_<? echo $tblRow; ?>" name="hdnDtlsdata[]" value="<? echo implode("**",array_filter(explode(',',$data_dreak_arr[$dtls_id]))); ?>">
							<input type="hidden" id="hdnbookingDtlsId_<? echo $tblRow; ?>" name="hdnbookingDtlsId[]" value="<? echo chop($row['booking_dtls_id'],","); ?>">
							<input type="hidden" id="hdnReceiveDtlsId_<? echo $tblRow; ?>" name="hdnReceiveDtlsId[]" value="<? echo chop($row['receive_dtls_id'],","); ?>">
							<input type="hidden" id="hdnJobDtlsId_<? echo $tblRow; ?>" name="hdnJobDtlsId[]" value="<? echo $row['jobDtlsId']; ?>">
							<input type="hidden" id="hdnProductionDtlsId_<? echo $tblRow; ?>" name="hdnProductionDtlsId[]" value="<? echo chop($row['prod_dtls_id'],","); ?>">
						</td>
						<td><?   echo create_drop_down( "cboStatus_".$tblRow, 90, $row_status,"",0, $selected,0,'','','','','','','','',"cboStatus[]");?>	</td>
						<td width="65" style="display: none;">
							<input type="button" id="increase_<? echo $tblRow; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(
							<? echo $tblRow.","."'tbl_dtls_emb'".","."'row_'" ;?>)" />
							<input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(<? echo $tblRow.","."'tbl_dtls_emb'".","."'row_'" ; ?>);" />
						</td>
					</tr>
					<?
				}
				}
			}
		}
}	
else
{
	
	foreach($rcv_result as $row)
	{
		$tblRow++;
		
		
	?>
		<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
			<td style="display:none"><input id="txtWorkOrder<? echo $tblRow; ?>" name="txtWorkOrder[]" type="hidden" value="<? echo $row[csf('order_no')]; ?>" class="text_boxes" style="width:100px" disabled/>
				<input id="txtWorkOrderID<? echo $tblRow; ?>" name="txtWorkOrderID[]" type="hidden" value="<? echo $row[csf('order_id')]; ?>" class="text_boxes" style="width:100px"readonly/>
			</td>
			<td><input id="txtbuyerPo_<? echo $tblRow; ?>" name="txtbuyerPo[]" value="<? echo $row[csf('buyer_po_no')]; ?>" class="text_boxes" type="text"  style="width:100px" disabled />
				<input id="txtbuyerPoId_<? echo $tblRow; ?>" name="txtbuyerPoId[]" value="<? echo $row[csf('buyer_po_id')]; ?>" class="text_boxes" type="hidden" style="width:70px" disabled />
			</td>
			<td><? echo create_drop_down( "cboSection_".$tblRow, 90, $trims_section,"", 1, "-- Select Section --",$row[csf('section')],'',1,'','','',"",'','',"cboSection[]"); ?></td>
			<td style="display:none"><input id="txtstyleRef_<? echo $tblRow; ?>" name="txtstyleRef[]" value="<? echo $row[csf('buyer_style_ref')]; ?>" class="text_boxes" type="text"  style="width:100px" disabled/></td>
			<td style="display:none">
				<? 
				if($data[2]==1)
				{
					echo create_drop_down( "txtbuyer_".$tblRow, 100, "select id, buyer_name from lib_buyer where status_active=1","id,buyer_name", 1, "-- Select --",$row[csf('buyer_buyer')], "",1,'','','','','','',"txtbuyer[]"); 
				}
				else
				{
					?>
					<input id="txtbuyer_<? echo $tblRow; ?>" name="txtbuyer[]" value="<? echo $row[csf('buyer_buyer')]; ?>" class="text_boxes" type="text"  style="width:87px" disabled />
					<?
				}
				?>
			</td>
						
			<td><? echo create_drop_down( "cboItemGroup_".$tblRow, 90, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- Select --",$row[csf('trim_group')], "",1,'','','','','','',"cboItemGroup[]"); ?></td>
			
		<td><input id="txtItem_<? echo $tblRow; ?>" name="txtItem[]" type="text" class="text_boxes" style="width:87px" value="<? echo $row[csf('description')]; ?>" placeholder="Display" readonly disabled/>
			<td><input id="txtcolor_<? echo $tblRow; ?>" name="txtcolor[]" type="text" class="text_boxes" style="width:57px" value="<? echo $row[csf('color_name')]; ?>" placeholder="Display" readonly disabled/>
			<input id="txtcolorID_<? echo $tblRow; ?>" name="txtcolorID[]" type="hidden" class="text_boxes" style="width:57px"  value="<? echo $row[csf('color_id')]; ?>" placeholder="Display" readonly disabled/></td>
			<td><input id="txtsize_<? echo $tblRow; ?>" name="txtsize[]" type="text" class="text_boxes" style="width:57px" value="<? echo $row[csf('size_name')]; ?>" placeholder="Display" readonly disabled/>
			<input id="txtsizeID_<? echo $tblRow; ?>" name="txtsizeID[]" type="hidden"   value="<? echo $row[csf('size_id')]; ?>"  class="text_boxes" style="width:57px" placeholder="Display" readonly disabled/></td>
			
			<td><? echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- Select --",$row[csf('order_uom')],"", 1,'','','','','','',"cboUom[]"); ?>	</td>
			<td><input id="txtWorkOrderQuantity_<? echo $tblRow; ?>" name="txtWorkOrderQuantity[]" class="text_boxes_numeric" type="text"  value="<? echo $row[csf('workoder_qty')]; ?>" style="width:60px" placeholder="" readonly /></td>
			<td><input id="txtOrderQuantity_<? echo $tblRow; ?>" name="txtOrderQuantity[]" value="<? echo number_format($row[csf('order_quantity')],4,'.',''); ?>" class="text_boxes_numeric" type="text"  style="width:60px" 
				onClick="openmypage_order_qnty1(1,'<? echo $receive_dtls_id; ?>',<? echo $tblRow; ?>)" placeholder="Click To Search" readonly /></td>
			
			<td><input id="txtPrevQty_<? echo $tblRow; ?>" name="txtPrevQty[]"  value="<? echo $CumDelvQty=($delevery_qty_trims_arr[$row[csf("job_dtls_id")]]['delevery_qty']-$row[csf('delevery_qty')]); ?>"  type="text"  class="text_boxes_numeric" style="width:60px" disabled /></td>
			<td><input id="txtCurQty_<? echo $tblRow; ?>" name="txtCurQty[]" onKeyUp="cal_values(<? echo $tblRow; ?>);"  value="<? echo number_format($row[csf('delevery_qty')],4,'.',''); ?>"  type="text" style="width:70px"  class="text_boxes_numeric"  /></td>
			<td style="display:none"><input id="txtClaimQty_<? echo $tblRow; ?>" name="txtClaimQty[]" value="<? echo $claim_qty; ?>" type="text"  class="text_boxes_numeric" style="width:57px" /></td>
			<td><input id="txtDelvBalance_<? echo $tblRow; ?>" name="txtDelvBalance[]" value="<? echo ($row[csf('workoder_qty')]-($CumDelvQty+$row[csf('delevery_qty')])); ?>"  type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td> 
			<td><input id="txtRemarksDtls_<? echo $tblRow; ?>" name="txtRemarksDtls[]" value="<? echo $row[csf('remarks')]; ?>" type="text"  class="text_boxes" style="width:77px" />
				
				<input id="hdnDtlsUpdateId_<? echo $tblRow; ?>" name="hdnDtlsUpdateId[]" type="hidden" value="<? echo  $row[csf('id')];//$updateDtlsId; ?>" class="text_boxes_numeric" style="width:40px" />
				<input type="hidden" id="hdnDtlsdata_<? echo $tblRow; ?>" name="hdnDtlsdata[]" value="<? echo implode("***",array_filter(explode(',',$data_dreak_arr[$updateDtlsId]))); ?>">
				<input type="hidden" id="hdnbookingDtlsId_<? echo $tblRow; ?>" name="hdnbookingDtlsId[]" value="<? echo $row[csf('booking_dtls_id')]; ?>">
				<input type="hidden" id="hdnReceiveDtlsId_<? echo $tblRow; ?>" name="hdnReceiveDtlsId[]" value="<? echo $row[csf('receive_dtls_id')]; ?>">
				<input type="hidden" id="hdnJobDtlsId_<? echo $tblRow; ?>" name="hdnJobDtlsId[]" value="<? echo $row[csf('job_dtls_id')]; ?>">
				<input type="hidden" id="hdnProductionDtlsId_<? echo $tblRow; ?>" name="hdnProductionDtlsId[]" value="<? echo $row[csf('production_dtls_id')]; ?>">
			</td>
		  
		   <td><? echo create_drop_down( "cboStatus_".$tblRow, 60, $row_status,"",0, $selected,$row[csf('delevery_status')],"",'','','','','','','',"cboStatus[]"); ?>	</td> 
			
			
			<td width="65" style="display: none;">
				<input type="button" id="increase_<? echo $tblRow; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(
				<? echo $tblRow.","."'tbl_dtls_emb'".","."'row_'" ;?>)" />
				<input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(<?echo $tblRow.","."'tbl_dtls_emb'".","."'row_'" ;?>);" />
			</td>
		</tr>
		<?
		//$sql = "select id, mst_id, booking_dtls_id, receive_dtls_id, job_dtls_id, production_dtls_id,  order_id, order_no, buyer_po_id, buyer_po_no,  buyer_style_ref, buyer_buyer, section,   item_group as trim_group, order_uom, order_quantity,delevery_qty, claim_qty, remarks,color_id, size_id, 
  // description, delevery_status, color_name, 
 //  size_name,workoder_qty from trims_delivery_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0 order by id ASC";
	}
}

	
	
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	/*echo '<pre>';
	print_r($cbo_company_name);die;*/
	$user_id=$_SESSION['logic_erp']['user_id'];
	
	if ($operation==0) // Insert Start Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
		
		$new_del_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'TD', date("Y",time()), 5, "select del_no_prefix,del_no_prefix_num from trims_delivery_mst where entry_form=208 and company_id=$cbo_company_name $insert_date_con order by id desc ", "del_no_prefix", "del_no_prefix_num" ));
		
		if($db_type==0)
		{
			$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date),'yyyy-mm-dd');
		}
		else
		{
			$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date), "", "",1);
		}
		$id=return_next_id("id","trims_delivery_mst",1);
		$id1=return_next_id( "id", "trims_delivery_dtls",1) ;
		$id3=return_next_id( "id", "trims_delivery_breakdown", 1 ) ;
		$rID3=true;
		$field_array="id, entry_form, trims_del, del_no_prefix, del_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id,  delivery_date, receive_id, order_id, challan_no, gate_pass_no, remarks , inserted_by, insert_date";

		$data_array="(".$id.", 208, '".$new_del_no[0]."', '".$new_del_no[1]."', '".$new_del_no[2]."', '".$cbo_company_name."', '".$cbo_location_name."', '".$cbo_within_group."', '".$cbo_party_name."', '".$cbo_party_location."', '".$cbo_currency."', '".$txt_delivery_date."','".$received_id."','".$hid_order_id."', '".$txt_challan_no."', '".$txt_gate_pass_no."', '".$txt_remarks."', ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";
		
		$txt_job_no=$new_del_no[0];
		$field_array2="id, mst_id, booking_dtls_id, receive_dtls_id, job_dtls_id, production_dtls_id,  order_id, order_no, buyer_po_id, buyer_po_no,  buyer_style_ref, buyer_buyer, section,   item_group, order_uom, order_quantity, delevery_qty, claim_qty, remarks,description, color_id, size_id,color_name,size_name,delevery_status,workoder_qty, inserted_by, insert_date";
		

		$field_array3="id, mst_id, del_no_mst,  order_id, book_con_dtls_id, description, color_id, size_id, qnty";
		$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
		$size_library_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");

		$data_array2 	= $data_array3="";  $add_commaa=0; $add_commadtls=0;
		for($i=1; $i<=$total_row; $i++)
		{	
			$txtWorkOrder			= "txtWorkOrder_".$i; 
			$txtWorkOrderID			= "txtWorkOrderID_".$i; 
			$txtbuyerPoId			= "txtbuyerPoId_".$i; 
			$txtbuyerPo				= "txtbuyerPo_".$i;
			$txtstyleRef			= "txtstyleRef_".$i;
			$txtbuyer				= "txtbuyer_".$i;
			$cboSection				= "cboSection_".$i;
			$cboItemGroup			= "cboItemGroup_".$i;
			$txtOrderQuantity		= "txtOrderQuantity_".$i;
			$cboUom 				= "cboUom_".$i;
			$txtPrevQty 			= "txtPrevQty_".$i;
			$txtCurQty 				= "txtCurQty_".$i;			
			$txtClaimQty 			= "txtClaimQty_".$i;
			$txtRemarksDtls 		= "txtRemarksDtls_".$i;
			$txtDomamount 			= "txtDomamount_".$i;
			$hdnDtlsdata 			= "hdnDtlsdata_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$hdnbookingDtlsId 		= "hdnbookingDtlsId_".$i;
			$hdnReceiveDtlsId 		= "hdnReceiveDtlsId_".$i;
			$hdnJobDtlsId 			= "hdnJobDtlsId_".$i;
			$hdnProductionDtlsId 	= "hdnProductionDtlsId_".$i;
			
			$txtItem				= "txtItem_".$i; 
			$txtcolor				= "txtcolor_".$i;
			$txtsize				= "txtsize_".$i;
			$txtcolorID				= "txtcolorID_".$i;
			$txtsizeID				= "txtsizeID_".$i;
			$cboStatus				= "cboStatus_".$i;
			$txtWorkOrderQuantity	= "txtWorkOrderQuantity_".$i;
			
			

			if(str_replace("'",'',$$txtbuyerPoId)=="") $txtbuyerPoId=0; else $txtbuyerPoId=str_replace("'",'',$$txtbuyerPoId);
			if ($add_commaa!=0) $data_array2 .=","; $add_comma=0;
			
			$data_array2 .="(".$id1.",".$id.",".$$hdnbookingDtlsId.",".$$hdnReceiveDtlsId.",".$$hdnJobDtlsId.",".$$hdnProductionDtlsId.",".$$txtWorkOrderID.",".$$txtWorkOrder.",'".$$txtbuyerPoId."',".$$txtbuyerPo.",".$$txtstyleRef.",".$$txtbuyer.",".$$cboSection.",".$$cboItemGroup.",".$$cboUom.",".str_replace(",",'',$$txtOrderQuantity).",".str_replace(",",'',$$txtCurQty).",".str_replace(",",'',$$txtClaimQty).",".$$txtRemarksDtls.",".$$txtItem.",".$$txtcolorID.",".$$txtsizeID.",".$$txtcolor.",".$$txtsize.",".$$cboStatus.",".$$txtWorkOrderQuantity.",'".$user_id."','".$pc_date_time."')";
			
			
			
			
			
			
			
			$dtls_data=explode("***",str_replace("'",'',$$hdnDtlsdata));
			for($j=0; $j<count($dtls_data); $j++)
			{
				$exdata=explode("_",$dtls_data[$j]);
				$breakId="'".$exdata[0]."'";
				$mst_id="'".$exdata[1]."'";
				$description="'".$exdata[2]."'";
				$colorname="'".$exdata[3]."'";
				$sizename="'".$exdata[4]."'";
				$qty="'".str_replace(",",'',$exdata[5])."'";
				$book_con_dtls_id="'".$exdata[6]."'";
				
				if ($add_commadtls!=0) $data_array3 .=",";
				//$field_array3="id, mst_id, del_no_mst,  order_id, book_con_dtls_id, description, color_id, size_id, qnty";
				$data_array3.="(".$id3.",".$id1.",'".$txt_job_no."','".$hid_order_id."',".$book_con_dtls_id.",".$description.",".$colorname.",".$sizename.",".$qty.")";
				$id3=$id3+1; $add_commadtls++;
			}
			
			$id1=$id1+1; $add_commaa++;
			//echo "10**INSERT INTO subcon_ord_breakdown (".$field_array3.") VALUES ".$data_array3; die;			
		}
		//echo "10**INSERT INTO subcon_ord_mst (".$field_array.") VALUES ".$data_array; die;
		//echo "10**INSERT INTO trims_delivery_breakdown (".$field_array3.") VALUES ".$data_array3; die;
		//echo "10**INSERT INTO trims_delivery_dtls (".$field_array2.") VALUES ".$data_array2; die;
		$flag=1;
		$rID=sql_insert("trims_delivery_mst",$field_array,$data_array,1);
		if($rID==1) $flag=1; else $flag=0;
		$rID2=sql_insert("trims_delivery_dtls",$field_array2,$data_array2,1);
		if($rID2==1) $flag=1; else $flag=0;
		if($data_array3!="")
		{
			$rID3=sql_insert("trims_delivery_breakdown",$field_array3,$data_array3,1);
			if($rID3==1) $flag=1; else $flag=0;
		}
		//echo "10**".$rID."**".$rID2."**".$rID3; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$txt_job_no)."**".$id."**".str_replace("'",'',$txt_order_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_job_no)."**".$id."**".str_replace("'",'',$txt_order_no);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$txt_job_no)."**".$id."**".str_replace("'",'',$txt_order_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_job_no)."**".$id."**".str_replace("'",'',$txt_order_no);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$color_library_arr=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name"  );
		$size_library_arr=return_library_array( "select id,size_name from lib_size  where status_active=1 and is_deleted=0", "id", "size_name"  );
		if($db_type==0)
		{
			$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date),'yyyy-mm-dd');
		}
		else
		{
			$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date), "", "",1);
		}

		$field_array="location_id*within_group*party_id*party_location*currency_id*delivery_date*receive_id*order_id*challan_no*gate_pass_no*remarks*updated_by*update_date";	
		$data_array="'".$cbo_location_name."'*'".$cbo_within_group."'*'".$cbo_party_name."'*'".$cbo_party_location."'*'".$cbo_currency."'*'".$txt_delivery_date."'*'".$received_id."'*'".$hid_order_id."'*'".$txt_challan_no."'*'".$txt_gate_pass_no."'*'".$txt_remarks."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$field_array2="booking_dtls_id*receive_dtls_id*job_dtls_id*production_dtls_id*order_id*order_no*buyer_po_id*buyer_po_no*buyer_style_ref*buyer_buyer*section*item_group*order_uom*order_quantity*delevery_qty*claim_qty*remarks*description*color_id*size_id*color_name*size_name*delevery_status*workoder_qty*updated_by*update_date";
		$field_array3="order_id*book_con_dtls_id*description*color_id*size_id*qnty";
		//echo "10**".$operation; die;
		$add_comma=0;	$flag="";
		for($i=1; $i<=$total_row; $i++)
		{	
			$txtWorkOrder			= "txtWorkOrder_".$i; 
			$txtWorkOrderID			= "txtWorkOrderID_".$i; 
			$txtbuyerPoId			= "txtbuyerPoId_".$i; 
			$txtbuyerPo				= "txtbuyerPo_".$i;
			$txtstyleRef			= "txtstyleRef_".$i;
			$txtbuyer				= "txtbuyer_".$i;
			$cboSection				= "cboSection_".$i;
			$cboItemGroup			= "cboItemGroup_".$i;
			$txtOrderQuantity		= "txtOrderQuantity_".$i;
			$cboUom 				= "cboUom_".$i;
			$txtPrevQty 			= "txtPrevQty_".$i;
			$txtCurQty 				= "txtCurQty_".$i;			
			$txtClaimQty 			= "txtClaimQty_".$i;
			$txtRemarksDtls 		= "txtRemarksDtls_".$i;
			$txtDomamount 			= "txtDomamount_".$i;
			$hdnDtlsdata 			= "hdnDtlsdata_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$hdnbookingDtlsId 		= "hdnbookingDtlsId_".$i;
			$hdnReceiveDtlsId 		= "hdnReceiveDtlsId_".$i;
			$hdnJobDtlsId 			= "hdnJobDtlsId_".$i;
			$hdnProductionDtlsId 	= "hdnProductionDtlsId_".$i;
			
			$txtItem				= "txtItem_".$i; 
			$txtcolor				= "txtcolor_".$i;
			$txtsize				= "txtsize_".$i;
			$txtcolorID				= "txtcolorID_".$i;
			$txtsizeID				= "txtsizeID_".$i;
			$cboStatus				= "cboStatus_".$i;
			$txtWorkOrderQuantity	= "txtWorkOrderQuantity_".$i;
			
			$aa	=str_replace("'",'',$$hdnDtlsUpdateId);

			if(str_replace("'",'',$$txtbuyerPoId)=="") $txtbuyerPoId=0; else $txtbuyerPoId=str_replace("'",'',$$txtbuyerPoId);
			//if ($add_commaa!=0) $data_array2 .=","; $add_comma=0;

			if(str_replace("'",'',$$hdnDtlsUpdateId)!="")
			{
				$field_array2="booking_dtls_id*receive_dtls_id*job_dtls_id*production_dtls_id*order_id*order_no*buyer_po_id*buyer_po_no*buyer_style_ref*buyer_buyer*section*item_group*order_uom*order_quantity*delevery_qty*claim_qty*remarks*description*color_id*size_id*color_name*size_name*delevery_status*workoder_qty*updated_by*update_date";
				$data_array2[$aa]=explode("*",("".$$hdnbookingDtlsId."*".$$hdnReceiveDtlsId."*".$$hdnJobDtlsId."*".$$hdnProductionDtlsId."*".$$txtWorkOrderID."*".$$txtWorkOrder."*".$$txtbuyerPoId."*".$$txtbuyerPo."*".$$txtstyleRef."*".$$txtbuyer."*".$$cboSection."*".$$cboItemGroup."*".$$cboUom."*".str_replace(",",'',$$txtOrderQuantity)."*".str_replace(",",'',$$txtCurQty)."*".str_replace(",",'',$$txtClaimQty)."*".$$txtRemarksDtls."*".$$txtItem."*".$$txtcolorID."*".$$txtsizeID."*".$$txtcolor."*".$$txtsize."*".$$cboStatus."*".$$txtWorkOrderQuantity."*".$user_id."*'".$pc_date_time."'"));
				$hdn_dtls_id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
			}
			
			$dtls_data=explode("***",str_replace("'",'',$$hdnDtlsdata));
			for($j=0; $j<count($dtls_data); $j++)
			{
				$exdata=explode("_",$dtls_data[$j]);
				$breakId="'".$exdata[0]."'";
				$mst_id="'".$exdata[1]."'";
				$description="'".$exdata[2]."'";
				$colorname="'".$exdata[3]."'";
				$sizename="'".$exdata[4]."'";
				$qty="'".str_replace(",",'',$exdata[5])."'";
				$book_con_dtls_id="'".$exdata[6]."'";
				$bb=$exdata[0];
				if($bb!=0)
				{
					$data_array3[$bb]=explode("*",("".$hid_order_id."*".$book_con_dtls_id."*".$description."*".$colorname."*".$sizename."*".$qty.""));
					$hdn_break_id_arr[]	=$bb;
				}
			}
		}
		//echo "10**".bulk_update_sql_statement( "trims_delivery_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr); die;
		$rID=sql_update("trims_delivery_mst",$field_array,$data_array,"id",$update_id,0);  
		if($rID) $flag=1; else $flag=0;

		if($data_array2!="")
		{
			$rID2=execute_query(bulk_update_sql_statement( "trims_delivery_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr),1);
			if($rID2) $flag=1; else $flag=0;
		}
		
		$id_break=implode(',',$hiddenTblIdBreak);
		if($data_array3!="")
		{
			$rID3=execute_query(bulk_update_sql_statement( "trims_delivery_breakdown", "id",$field_array3,$data_array3,$hdn_break_id_arr),1);
			if($rID3) $flag=1; else $flag=0;
		}
		//echo "10**".bulk_update_sql_statement( "trims_delivery_breakdown", "id",$field_array3,$data_array3,$hdn_break_id_arr); die;
		//echo "10**".$rID.'='.$rID2.'='.$rID3; die;
		//if($rID4) $flag=1; else $flag=0;	
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_dalivery_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_dalivery_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);
			}
		}
		else if($db_type==2)
		{  
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$txt_dalivery_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_dalivery_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // delete here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");  
		}
		
		$rec_number=return_field_value( "sys_no", "sub_material_mst"," subcon_job=$txt_job_no and status_active=1 and is_deleted=0 and trans_type=1");
		if($rec_number){
			echo "emblRec**".str_replace("'","",$txt_job_no)."**".$rec_number;
			die;
		}

		//if ( $delete_master_info==1 )
		//{
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$rID=sql_update("subcon_ord_mst",$field_array,$data_array,"id",$update_id,1);  
		$rID=sql_update("subcon_ord_dtls",$field_array,$data_array,"job_no_mst",$txt_job_no,1);  
		//$rID = sql_delete("subcon_ord_dtls","status_active*is_deleted*updated_by*update_date","0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'",'id',$update_id2,1);
		$rID=execute_query( "delete from subcon_ord_breakdown where job_no_mst=$txt_job_no",0);
		
		$rID=execute_query( "update wo_booking_mst set lock_another_process=0 where booking_no =".$txt_order_no."",1);
		//}
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2)
		{
			if($rID)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id);;
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
}

if ($action=="job_popup")
{
	echo load_html_head_contents("Job Popup Info","../../../", 1, 1, $unicode,'','');
	?>
	<script>
		function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_popup(type,within_group)
		{
			var company = $('#cbo_company_name').val();
			var party_name = $('#cbo_party_name').val();
			var location_name = $('#cbo_location_name').val();
			load_drop_down( 'trims_delivery_entry_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0)
			{
				$('#search_by_td').html('System ID');
			}
			else if(val==2)
			{
				$('#search_by_td').html('W/O No');
			}
			else if(val==3)
			{
				$('#search_by_td').html('Buyer Job');
			}
			else if(val==4)
			{
				$('#search_by_td').html('Buyer Po');
			}
			else if(val==5)
			{
				$('#search_by_td').html('Buyer Style');
			}
		}
	</script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="940" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead> 
                <tr>
                    <th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>               	 
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="100">Within Group</th>                           
                    <th width="140">Party Name</th>
                    <th width="100">Search By</th>
                    <th width="100" id="search_by_td">System ID</th>
                    <th width="100">Year</th>
                    <th width="170">Date Range</th>                            
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                </tr>           
            </thead>
            <tbody>
                <tr class="general">
                    <td><input type="hidden" id="selected_job"><? $data=explode("_",$data); ?>  <!--  echo $data;-->
                        <? 
                        echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
                    </td>
                    <td>
                        <?php echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 0, "--  --", $data[3], "fnc_load_party_popup(1,this.value);" ); ?>
                    </td>
                    <td id="buyer_td">
                        <? echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );   	 
                        ?>
                    </td>
                    <td>
						<?
                            $search_by_arr=array(1=>"System ID",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                    </td>
                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 100, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value, 'create_job_search_list_view', 'search_div', 'trims_delivery_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" valign="middle">
                            <? echo load_month_buttons();  ?>
                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" valign="top" id=""><div id="search_div"></div></td>
                    </tr>
                </tbody>
            </table>    
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}
	
if($action=="create_job_search_list_view")
{	
	$data=explode('_',$data);
	$party_id=str_replace("'","",$data[1]);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}

	if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	//echo $search_type; die;
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
	}	

	if($party_id!=0) $party_id_cond=" and a.party_id='$party_id'"; else $party_id_cond="";

	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";
	}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	if($within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
	
	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	//echo $po_ids;
	if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$buyer_po_arr=array();
	if($within_group==1)
	{
		$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		}
		unset($po_sql_res);
	}
	$buyer_po_id_str=""; $buyer_po_no_str=""; $buyer_po_style_str="";
	if($db_type==0) 
	{
		$ins_year_cond="year(a.insert_date)";
		$color_id_str=",group_concat(c.color_id) as color_id";
		if($within_group==1)
		{
			$buyer_po_id_str=",group_concat(b.buyer_po_id) as buyer_po_id";
		}
		else
		{
			$buyer_po_no_str=",group_concat(b.buyer_po_no) as buyer_po_id";
			$buyer_po_style_str=",group_concat(b.buyer_style_ref) as buyer_style";
		}
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str=",listagg(c.color_id,',') within group (order by c.color_id) as color_id";
		
		if($within_group==1)
		{
			$buyer_po_id_str=",listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)  as buyer_po_id";
		}
		else
		{
			$buyer_po_no_str=",listagg(b.buyer_po_no,',') within group (order by b.id) as buyer_po_no";
			$buyer_po_style_str=",listagg(b.buyer_style_ref,',') within group (order by b.id) as buyer_style";
		}
	}
	 $sql= "select a.id, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date $color_id_str $buyer_po_id_str $buyer_po_no_str $buyer_po_style_str
	 from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	 where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $withinGroup and b.id=c.mst_id  
	 group by a.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date
	 order by a.id DESC";
	 //echo $sql;
	 $data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="885" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Job No</th>
            <th width="60">Year</th>
            <th width="120">W/O No</th>
            <th width="100">Buyer Po</th>
            <th width="100">Buyer Style</th>
            <th width="80">Ord Receive Date</th>
            <th width="80">Delivery Date</th>
            <th>Color</th>
        </thead>
        </table>
        <div style="width:885px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="865" class="rpt_table" id="tbl_po_list">
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$excolor_id=array_unique(explode(",",$row[csf('color_id')]));
				$color_name="";	
				foreach ($excolor_id as $color_id)
				{
					if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
				}
				if($within_group==1)
				{
					$buyer_po=""; $buyer_style="";
					$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
					foreach($buyer_po_id as $po_id)
					{
						if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
						if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
					}
					$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
					$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				}
				else
				{
					$buyer_po=implode(",",array_unique(explode(",",$row[csf('buyer_po_no')])));
					$buyer_style=implode(",",array_unique(explode(",",$row[csf('buyer_style')])));
				}
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('subcon_job')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="120"><? echo $row[csf('order_no')]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_po; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_style; ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>	
                    <td style="word-break:break-all"><? echo $color_name; ?></td>
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
 
/*if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id, subcon_job, company_id, location_id, party_id, currency_id, party_location, delivery_date, rec_start_date, rec_end_date, receive_date, within_group, party_location, order_id, order_no,exchange_rate,remarks from subcon_ord_mst where subcon_job='$data' and entry_form=255 and status_active=1" );
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_job_no').value 				= '".$row[csf("subcon_job")]."';\n";  
		echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_within_group').value 		= '".$row[csf("within_group")]."';\n";  
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "fnc_load_party(1,".$row[csf("within_group")].");\n";	
	//echo "load_drop_down( 'requires/trims_delivery_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";	
		echo "document.getElementById('cbo_location_name').value 		= '".$row[csf("location_id")]."';\n";
	//echo "load_drop_down( 'requires/trims_delivery_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "document.getElementById('cbo_party_name').value			= '".$row[csf("party_id")]."';\n";
		echo "document.getElementById('cbo_currency').value				= '".$row[csf("currency_id")]."';\n";
		echo "fnc_load_party(2,".$row[csf("within_group")].");\n";	 
		echo "document.getElementById('cbo_party_location').value		= '".$row[csf("party_location")]."';\n";	
		echo "document.getElementById('txt_order_receive_date').value	= '".change_date_format($row[csf("receive_date")])."';\n"; 
		echo "document.getElementById('txt_delivery_date').value		= '".change_date_format($row[csf("delivery_date")])."';\n"; 
		echo "document.getElementById('txt_rec_start_date').value		= '".change_date_format($row[csf("rec_start_date")])."';\n"; 
		echo "document.getElementById('txt_rec_end_date').value			= '".change_date_format($row[csf("rec_end_date")])."';\n"; 
		echo "document.getElementById('hid_order_id').value          	= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('txt_order_no').value         	= '".$row[csf("order_no")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value        = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('txt_remarks').value         	= '".$row[csf("remarks")]."';\n";
		echo "$('#txt_order_no').attr('disabled','true')".";\n";
		echo "$('#cbo_within_group').attr('disabled','true')".";\n";
		echo "$('#cbo_party_name').attr('disabled','true')".";\n";
		echo "$('#cbo_currency').attr('disabled','true')".";\n";
		echo "document.getElementById('update_id').value          		= '".$row[csf("id")]."';\n";	
		echo "set_button_status(1,'".$_SESSION['page_permission']."', 'fnc_job_order_entry',1);\n";	
	}
	exit();	
}*/

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

	function fnc_load_party_order_popup(company,party_name)
	{   	
		load_drop_down( 'trims_delivery_entry_controller', company+'_'+1+'_'+party_name, 'load_drop_down_buyer', 'buyer_td' );
		$('#cbo_party_name').attr('disabled',true);
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
<body onLoad="fnc_load_party_order_popup(<? echo $company;?>,<? echo $party_name;?>)">
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
                        <th width="150">Party Name</th>
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
                    <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "-- Select Buyer --" ); ?></td>
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_search_category').value+'_'+<? echo $company;?>+'_'+document.getElementById('cbo_search_type').value, 'create_booking_search_list_view', 'search_div', 'trims_delivery_entry_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
	
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Party First."; die; }
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
	
	$pre_cost_trims_arr=array();
	$pre_sql ="Select id, emb_name, emb_type, body_part_id  from wo_pre_cost_embe_cost_dtls where status_active=1 and is_deleted=0";
	$pre_sql_res=sql_select($pre_sql);
	foreach ($pre_sql_res as $row)
	{
		$pre_cost_trims_arr[$row[csf("id")]]['emb_name']=$row[csf("emb_name")];
		$pre_cost_trims_arr[$row[csf("id")]]['emb_type']=$row[csf("emb_type")];
		$pre_cost_trims_arr[$row[csf("id")]]['body_part_id']=$row[csf("body_part_id")];
	}
	unset($pre_sql_res);
	
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date = "and a.booking_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
		$wo_year="YEAR(a.insert_date)";
		$pre_cost_trims_cond="group_concat(b.pre_cost_fabric_cost_dtls_id)";
		$gmts_item_cond="group_concat(b.gmt_item)";
		$po_id_cond="group_concat(b.po_break_down_id)";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date = "and a.booking_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
		$wo_year="to_char(a.insert_date,'YYYY')";
		$pre_cost_trims_cond="listagg(b.pre_cost_fabric_cost_dtls_id,',') within group (order by b.pre_cost_fabric_cost_dtls_id)";
		$gmts_item_cond="listagg(b.gmt_item,',') within group (order by b.gmt_item)";
		$po_id_cond="listagg(b.po_break_down_id,',') within group (order by b.po_break_down_id)";
	} 
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (0=>$comp,1=>$buyer_arr);
	//$sql= "select $wo_year as year, id, booking_type, booking_no, company_id, buyer_id, job_no, booking_date from  wo_booking_mst where booking_type=6 and status_active=1 and is_deleted=0 $booking_date $company $woorder_cond $year_cond order by booking_no"; 
	//$sql= "select $wo_year as year, a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, $pre_cost_trims_cond as pre_cost_trims_id, $gmts_item_cond as gmts_item, $po_id_cond as po_id from  wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=6 and a.status_active=1 and a.lock_another_process!=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_date $company $woorder_cond $year_cond $po_idsCond group by a.insert_date, a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id order by a.id DESC";

	$sql= "select $wo_year as year, a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, $pre_cost_trims_cond as pre_cost_trims_id, $po_id_cond as po_id from  wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=2 and a.status_active=1 and a.lock_another_process!=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_date $company $woorder_cond $year_cond $po_idsCond group by a.insert_date, a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id order by a.id DESC";
	
	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="640" >
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
        <div style="width:640px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="620" class="rpt_table" id="list_view">
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

if ($action=="populate_data_from_search_popup")
{
	//echo $action."nazim"; die;
	$data=explode('_',$data);
	$nameArray=sql_select( "select id, booking_type, booking_no, company_id, buyer_id, job_no, booking_date,currency_id from  wo_booking_mst where booking_type=6 and status_active=1 and is_deleted=0 and id='$data[0]'" );
	//$sql= "select to_char(insert_date,'YYYY') as year, id, booking_type, booking_no, company_id, buyer_id, job_no, booking_date from  wo_booking_mst where booking_type=6 and status_active=1 and is_deleted=0 $booking_date $company $order_cond order by booking_no";
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_order_no').value 	= '".$row[csf("booking_no")]."';\n";  
		echo "document.getElementById('cbo_party_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('hid_order_id').value		= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_currency').value		= '".$row[csf("currency_id")]."';\n";
		//if($row[csf("booking_date")]=="0000-00-00" || $row[csf("booking_date")]=="") $booking_date=""; else $booking_date=change_date_format($row[csf("booking_date")]);   
		//echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		//echo "load_drop_down( 'requires/trims_delivery_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";	
		//echo "document.getElementById('cbo_location_name').value 	= '".$row[csf("location_id")]."';\n";
		//echo "load_drop_down( 'requires/trims_delivery_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_buyer', 'buyer_td' );\n";

		//echo "document.getElementById('txt_process_id').value		= '".$row[csf("service_type")]."';\n"; 
		//echo "document.getElementById('cbo_currency').value			= '".$row[csf("currency_id")]."';\n"; 
	    //echo "document.getElementById('update_id').value          	= '".$row[csf("id")]."';\n";	
		//echo "set_button_status(0,'".$_SESSION['page_permission']."', 'fnc_job_order_entry',1);\n";	
	}
	exit();	
}

if( $action=='order_dtls_list_view' ) 
{
	//echo $data; die; 1_FAL-TB-18-00091_1 
	$data=explode('_',$data);
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
	$tblRow=0;
	$buyer_po_arr=array();
	
	$buyer_po_sql = sql_select("select a.style_ref_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst");
	
	foreach($buyer_po_sql as $row)
	{
		$buyer_po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
		$buyer_po_arr[$row[csf('id')]]['buyerpo']=$row[csf('po_number')];
		$buyer_po_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
	}
	unset($buyer_po_sql);
	if($data[0]==2)
	{

		$qry_result=sql_select( "select id, mst_id, order_id, job_no_mst, book_con_dtls_id, description, color_id, size_id, qnty, rate, amount from subcon_ord_breakdown where job_no_mst='$data[1]'");	
		$data_dreak_arr=array(); $data_dreak=''; $add_comma=0; $k=1;
		foreach ($qry_result as $row)
		{
			if($row[csf('description')]=="") $row[csf('description')]=0;
			if($row[csf('color_id')]=="") $row[csf('color_id')]=0;
			if($row[csf('size_id')]=="") $row[csf('size_id')]=0;
			if($row[csf('qnty')]=="") $row[csf('qnty')]=0;
			if($row[csf('rate')]=="") $row[csf('rate')]=0;
			if($row[csf('amount')]=="") $row[csf('amount')]=0;
			if($row[csf('book_con_dtls_id')]=="") $row[csf('book_con_dtls_id')]=0;
			if(!in_array($row[csf('mst_id')],$temp_arr_mst_id))
			{
				$temp_arr_mst_id[]=$row[csf('mst_id')];
				//if($k!=1) {  }
				$add_comma=0; $data_dreak='';
				
			}
			//echo $add_comma.'='.$data_dreak.'='.$k.'<br>';
			$k++;
			
			if ($add_comma!=0) $data_dreak ="__";
			$data_dreak_arr[$row[csf('mst_id')]].=$row[csf('description')].'_'.$color_library[$row[csf('color_id')]].'_'.$size_arr[$row[csf('size_id')]].'_'.$row[csf('qnty')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('book_con_dtls_id')].'_'.$row[csf('id')].',';
			$add_comma++;
		}
	}
	//die;
	//print_r($data_dreak_arr);
	if($data[2]==1 && $data[0]==1 )
	{
		$sql = "select  a.id, a.booking_type, a.booking_no, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, b.id as booking_dtls_id, b.po_break_down_id,  b.trim_group ,b.delivery_date,b.fabric_description, b.uom, b.wo_qnty, b.rate, b.amount
		from  wo_booking_mst a, wo_booking_dtls b, wo_trim_book_con_dtls c where a.booking_no=b.booking_no and a.booking_type=2 and c.wo_trim_booking_dtls_id=b.id and c.requirment>0 and  b.booking_no=trim('$data[1]') and a.status_active=1and a.lock_another_process!=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.booking_type, a.booking_no, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, b.id, b.po_break_down_id, b.trim_group ,b.delivery_date,b.fabric_description, b.uom, b.wo_qnty, b.rate, b.amount order by b.id ASC";
	}
	else if($data[2]==1 && $data[0]==2 )
	{
		$sql = "select id, mst_id, job_no_mst, order_id, order_no, buyer_po_id as po_break_down_id, booking_dtls_id, order_quantity as wo_qnty, order_uom, rate, amount, delivery_date, buyer_po_no, buyer_style_ref, buyer_buyer, section, item_group as trim_group, rate_domestic,  amount_domestic from subcon_ord_dtls where job_no_mst='$data[1]' and mst_id='$data[3]' and status_active=1 and is_deleted=0 order by id ASC";
	}
	else
	{
		$sql = "select id, mst_id, job_no_mst, order_id, order_no, buyer_po_id as po_break_down_id, booking_dtls_id, order_quantity as wo_qnty, order_uom, rate, amount, delivery_date, buyer_po_no, buyer_style_ref, buyer_buyer, section, item_group as trim_group, rate_domestic,  amount_domestic from subcon_ord_dtls where job_no_mst='$data[1]' and mst_id='$data[3]' and status_active=1 and is_deleted=0 order by id ASC";
	}
	//echo $sql; //die; 
	$data_array=sql_select($sql);
	if(count($data_array) > 0)
	{
		$exchange_rate=$data[3];
		foreach($data_array as $row)
		{
			$tblRow++;
			$dtls_id=0; $order_uom=0; $wo_qnty=0;
			if($data[2]==1)  //within group yes 
			{
				$dtls_id=$row[csf('id')]; 
				$row[csf("delivery_date")]=$row[csf('delivery_date')];
				if($data[0]==1)
				{
					$order_uom=$row[csf('uom')];
				}
				else
				{
					$order_uom=$row[csf('order_uom')];
				} 
				$wo_qnty=$row[csf('wo_qnty')];
				$buyerpo=$buyer_po_arr[$row[csf('po_break_down_id')]]['buyerpo'];
				$style=$buyer_po_arr[$row[csf('po_break_down_id')]]['style'];
				$break_down_id=$row[csf('po_break_down_id')];
				/*if($data[0]==2) //update
				{
					$dtls_id=$embl_po_arr[$row[csf('booking_dtls_id')]]['id']; 
					$row[csf("delivery_date")]=$embl_po_arr[$row[csf('booking_dtls_id')]]['delivery_date']; 
					$order_uom=$embl_po_arr[$row[csf('booking_dtls_id')]]['order_uom'];
					$wo_qnty=$row[csf('wo_qnty')];
				}*/
			}
			else if($data[2]==2)
			{
				if($data[0]==2)
				{
					$dtls_id=$row[csf('id')]; 
					$row[csf("delivery_date")]=$row[csf('delivery_date')];
					$order_uom=$row[csf('order_uom')];
					$wo_qnty=$row[csf('wo_qnty')];
					$buyerpo=$row[csf('buyer_po_no')];
					$style=$row[csf('buyer_style_ref')];
					$buyer_buyer=$row[csf('buyer_buyer')];
					$break_down_id="";
				}
				else
				{
					$wo_qnty=0;
				}
			}

			if($data[0]==1)
			{
				$domRate=$row[csf('rate')]*$exchange_rate; 
				$domAmount=$row[csf('amount')]*$exchange_rate;
				$buyer_buyer='';
				$disabled='disabled';
				$disable_dropdown='1';
			}
			else
			{
				$domRate=$row[csf('rate_domestic')]; 
				$domAmount=$row[csf('amount_domestic')];
				$buyer_buyer=$row[csf('buyer_buyer')];
				$disabled='';
				$disable_dropdown='0';
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
				<td><input id="txtbuyerPo_<? echo $tblRow; ?>" name="txtbuyerPo[]" value="<? echo $buyerpo; ?>" class="text_boxes" type="text"  style="width:100px" <? echo $disabled ?> />
					<input id="txtbuyerPoId_<? echo $tblRow; ?>" name="txtbuyerPoId[]" value="<? echo $break_down_id; ?>" class="text_boxes" type="hidden" style="width:70px" readonly />
				</td>
				<td><input id="txtstyleRef_<? echo $tblRow; ?>" name="txtstyleRef[]" value="<? echo $style; ?>" class="text_boxes" type="text"  style="width:100px" <? echo $disabled ?> /></td>
				<td>
					<? 
					if($data[2]==1)
					{
						echo create_drop_down( "txtbuyer_".$tblRow, 100, "select id, buyer_name from lib_buyer where status_active=1","id,buyer_name", 1, "-- Select --",$buyer_po_arr[$row[csf('po_break_down_id')]]['buyer_name'], "",$disable_dropdown,'','','','','','',"txtbuyer[]"); 
					}
					else
					{
						?>
						<input id="txtbuyer_<? echo $tblRow; ?>" name="txtbuyer[]" value="<? echo $buyer_buyer; ?>" class="text_boxes" type="text"  style="width:87px" $disabled />
						<?
					}
					?>
				</td>
				<td><? echo create_drop_down( "cboSection_".$tblRow, 90, $trims_section,"", 1, "-- Select Section --",$row[csf('section')],'',1,'','','','','','',"cboSection[]"); ?></td>			
				<td><? echo create_drop_down( "cboItemGroup_".$tblRow, 90, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- Select --",$row[csf('trim_group')], "",1,'','','','','','',"cboItemGroup[]"); ?></td>
				<td><? echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- Select --",$order_uom,"", 1,'','','','','','',"cboUom[]"); ?>	</td>
				<td><input id="txtOrderQuantity_<? echo $tblRow; ?>" name="txtOrderQuantity[]" value="<? echo number_format($row[csf('wo_qnty')],4,'.',''); ?>" class="text_boxes_numeric" type="text"  style="width:60px" onClick="openmypage_order_qnty1(1,'<? echo $row[csf('booking_dtls_id')]; ?>',<? echo $tblRow; ?>)" placeholder="Click To Search" readonly /></td>
				<!-- Previous Delv Qty 	Curr. Delv Qnty 	Claim Qnty -->
				<td><input id="txtPrevQty_<? echo $tblRow; ?>" name="txtPrevQty[]" value="<? echo number_format($row[csf('rate')],4,'.',''); ?>" type="text"  class="text_boxes_numeric" style="width:60px" readonly/></td>
				<td><input id="txtCurQty_<? echo $tblRow; ?>" name="txtCurQty[]"  value="<? echo number_format($row[csf('amount')],4,'.',''); ?>" type="text" style="width:70px"  class="text_boxes_numeric" disabled /></td>
				<td><input id="txtClaimQty_<? echo $tblRow; ?>" name="txtClaimQty[]" value="<? echo number_format($domRate,4,'.',''); ?>" type="text"  class="text_boxes_numeric" style="width:57px" <? echo $disabled ?> /></td>
				<td><input id="txtRemarksDtls_<? echo $tblRow; ?>" name="txtRemarksDtls[]" value="<? echo number_format($domAmount,4,'.',''); ?>" type="text"  class="text_boxes_numeric" style="width:77px" <? echo $disabled ?> />
					<input id="hdnDtlsUpdateId_<? echo $tblRow; ?>" name="hdnDtlsUpdateId[]" type="hidden" value="<? echo $dtls_id; ?>" class="text_boxes_numeric" style="width:40px" />
					<input type="hidden" id="hdnDtlsdata_<? echo $tblRow; ?>" name="hdnDtlsdata[]" value="<? echo implode("__",array_filter(explode(',',$data_dreak_arr[$dtls_id]))); ?>">
	                <input type="hidden" id="hdnbookingDtlsId_<? echo $tblRow; ?>" name="hdnbookingDtlsId[]" value="<? echo $row[csf('booking_dtls_id')]; ?>">
				</td>
				
                <td width="65">
					<input type="button" id="increase_<? echo $tblRow; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(
					<? echo $tblRow.","."'tbl_dtls_emb'".","."'row_'" ;?>)" />
					<input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(<?echo $tblRow.","."'tbl_dtls_emb'".","."'row_'" ;?>);" />
				</td>
			</tr>
			<?
		}
	}
	else
	{
		?>		
		<tr id="row_1">
            <td><input id="txtbuyerPo_1" name="txtbuyerPo[]" name="text" class="text_boxes" style="width:100px" placeholder="Display"/>
            	<input id="txtbuyerPoId_1" name="txtbuyerPoId[]" type="hidden" class="text_boxes" style="width:70px"readonly />
            </td>
            <td><input id="txtstyleRef_1" name="txtstyleRef[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/></td>
             <td><input id="txtbuyer_1" name="txtbuyer[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" /></td>
            <td><? echo create_drop_down( "cboSection_1", 90, $trims_section,"id,section_name", 1, "-- Select Section --","",'',0,'','','','','','',"cboSection[]"); ?></td>
            <td><? echo create_drop_down( "cboItemGroup_1", 90, "select id, item_name from lib_item_group where item_category=4 and  status_active=1","id,item_name", 1, "-- Select --",$selected, "",0,'','','','','','',"cboItemGroup[]"); ?>	</td>
            <td><? echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 1, "-- Select --",2,1, 1,'','','','','','',"cboUom[]"); ?>	</td>
            <td><input id="txtOrderQuantity_1" name="txtOrderQuantity[]" class="text_boxes_numeric" type="text"  style="width:60px" onClick="openmypage_order_qnty1(1,'0',1)" placeholder="Click To Search" readonly /></td>
            <td><input id="txtPrevQty_1" name="txtPrevQty[]" type="text"  class="text_boxes_numeric" style="width:60px" readonly="readonly" /></td>
            <td><input id="txtCurQty_1" name="txtCurQty[]" type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td> 
            <td><input id="txtClaimQty_1" name="txtClaimQty[]" type="text"  class="text_boxes_numeric" style="width:57px" readonly="readonly" /></td> 
            <td><input id="txtRemarksDtls_1" name="txtRemarksDtls[]" type="text"  class="text_boxes_numeric" style="width:77px" readonly="readonly"  />
            	<input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_1">
                <input type="hidden" name="hdnDtlsdata[]" id="hdnDtlsdata_1">
                <input type="hidden" name="hdnbookingDtlsId[]" id="hdnbookingDtlsId_1"></td> 
            <td>
            </td>
            <td width="65">
				<input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(1,'tbl_dtls_emb','row_')" />
				<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(1,'tbl_dtls_emb','row_');" />
			</td>
        </tr> 
		<?
	}
	exit();
}

if($action=="order_qty_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size",'id','size_name');
    ?>
    <script>
    	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name ", "color_name" ), 0, -1); ?> ];
    	var str_size = [<? echo substr(return_library_autocomplete( "select size_name from lib_size group by size_name ", "size_name" ), 0, -1); ?> ];
		
		function set_auto_complete(type)
		{
			if(type=='color_return')
			{
				$(".txt_color").autocomplete({
					source: str_color
				});
			}
		}

		function set_auto_complete_size(type)
		{
			if(type=='size_return')
			{
				$(".txt_size").autocomplete({
					source: str_size
				});
			}
		}

		/*function add_share_row( i ) 
		{
			//var row_num=$('#tbl_share_details_entry tbody tr').length-1;
			var row_num=$('#tbl_share_details_entry tbody tr').length;
			if (row_num!=i)
			{
				return false;
			}
			i++;
			$("#tbl_share_details_entry tbody tr:last").clone().find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { return name + i },
					'value': function(_, value) { return value }              
				});
			}).end().appendTo("#tbl_share_details_entry tbody");
			$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_share_row("+i+");");
			$('#txtorderquantity_'+i).removeAttr("onKeyUp").attr("onKeyUp","sum_total_qnty("+i+");");
			$('#txtorderrate_'+i).removeAttr("onKeyUp").attr("onKeyUp","sum_total_qnty("+i+");");
			$('#txtcolor_'+i).removeAttr("disabled");
			$('#txtorderquantity_'+i).removeAttr("disabled");
			$('#decreaseset_'+i).removeAttr("disabled");			
			//$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+','+'"tbl_share_details_entry"'+");");
			$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
			$('#txtsize_'+i).val('');
			//$('#loss_'+i).val('');
			$('#hiddenid_'+i).val('');
			set_all_onclick();
			$("#txtcolor_"+i).autocomplete({
				source: str_color
			});
			$("#txtsize_"+i).autocomplete({
				source: str_size
			});
			sum_total_qnty(i);
		}		
		
		function fn_deletebreak_down_tr(rowNo) 
		{ 
			var numRow = $('table#tbl_share_details_entry tbody tr').length; 
			if(numRow==rowNo && rowNo!=1)
			{
				var updateIdDtls=$('#hiddenid_'+rowNo).val();
				var txtDeletedId=$('#txtDeletedId').val();
				var selected_id='';
				if(updateIdDtls!='')
				{
					if(txtDeletedId=='') selected_id=updateIdDtls; else selected_id=txtDeletedId+','+updateIdDtls;
					$('#txtDeletedId').val( selected_id );
				}
				
				$('#tbl_share_details_entry tbody tr:last').remove();
			}
			else
			{
				return false;
			}
			sum_total_qnty(rowNo);
		}*/

		function fnc_close()
		{
			var tot_row=$('#tbl_share_details_entry tbody tr').length;
			
			var data_break_down="";
			for(var i=1; i<=tot_row; i++)
			{
				if (form_validation('txtorderquantity_'+i,'Quantity')==false)
				{
					return;
				}

				if($("#txtdescription_"+i).val()=="") $("#txtdescription_"+i).val(0);
				if($("#txtcolor_"+i).val()=="") $("#txtcolor_"+i).val(0);
				if($("#txtsize_"+i).val()=="") $("#txtsize_"+i).val(0);
				if($("#txtorderquantity_"+i).val()=="") $("#txtorderquantity_"+i).val(0);
				if($("#hidbookingconsid_"+i).val()=="") $("#hidbookingconsid_"+i).val(0);
				if($("#hiddenid_"+i).val()=="") $("#hiddenid_"+i).val(0);
				if($("#txtcolorId_"+i).val()=="") $("#txtcolorId_"+i).val(0);
				if($("#txtsizeID_"+i).val()=="") $("#txtsizeID_"+i).val(0);
				//alert($("#hidbookingconsid_"+i).val());
				if(data_break_down=="")
				{
					data_break_down+=$('#hiddenid_'+i).val()+'_'+$('#hiddenMstid_'+i).val()+'_'+$('#txtdescription_'+i).val()+'_'+$('#txtcolorId_'+i).val()+'_'+$('#txtsizeID_'+i).val()+'_'+$('#txtorderquantity_'+i).val()+'_'+$('#hidbookingconsid_'+i).val();
				}
				else
				{
					data_break_down+="***"+$('#hiddenid_'+i).val()+'_'+$('#hiddenMstid_'+i).val()+'_'+$('#txtdescription_'+i).val()+'_'+$('#txtcolorId_'+i).val()+'_'+$('#txtsizeID_'+i).val()+'_'+$('#txtorderquantity_'+i).val()+'_'+$('#hidbookingconsid_'+i).val();
				}
			}
			
			$('#hidden_break_tot_row').val( data_break_down );
			//alert(data_break_down);return;
			parent.emailwindow.hide();
		}

		function sum_total_qnty(id)
		{
			var ddd={ dec_type:5, comma:0, currency:''};
			var tot_row=$('#tbl_share_details_entry tbody tr').length;
			math_operation( "txt_total_order_qnty", "txtorderquantity_", "+", tot_row,ddd );
			var tot_row=$('#tbl_share_details_entry tbody tr').length;
		}

	</script>
</head>
<body >
	<div align="center" style="width:100%;" >
		<form name="qntypopup_1"  id="qntypopup_1" autocomplete="off">
			<table class="rpt_table" width="630px" cellspacing="0" cellpadding="0" rules="all" id="tbl_share_details_entry">
				<thead>
					<th width="200">Description</th>
					<th width="150">Color</th>
					<th width="150">Size</th>
					<th class="must_entry_caption">Quantity</th>
				</thead>
				<tbody>
					<input type="hidden" name="txtDeletedId" id="txtDeletedId" class="text_boxes_numeric" style="width:90px" readonly />
					<input type="hidden" name="hidden_break_tot_row" id="hidden_break_tot_row" class="text_boxes" style="width:90px" />
					<?
					//echo $type; die;
					if($type==2)
					{
						echo  $sql_break_down="select id, mst_id, del_no_mst,  order_id, book_con_dtls_id, description, color_id, size_id, qnty from trims_delivery_breakdown where mst_id='$hdnDtlsUpdateId'"; //die;
						$data_break_down=sql_select($sql_break_down);
						$data_break="";
						foreach($data_break_down as $row)
						{
							if($row[csf('id')]=="") $row[csf('id')]=0;
							if($row[csf('mst_id')]=="") $row[csf('mst_id')]=0;
							if($row[csf('description')]=="") $row[csf('description')]=0;
							if($row[csf('color_id')]=="") $row[csf('color_id')]=0;
							if($row[csf('size_id')]=="") $row[csf('size_id')]=0;
							if($row[csf('qnty')]=="") $row[csf('qnty')]=0;
							if($row[csf('book_con_dtls_id')]=="") $row[csf('book_con_dtls_id')]=0;
							//if($break_down_arr[$row[csf('id')]]=="") $break_down_arr[$row[csf('id')]]=0;
							//echo $data_break."++";
							if($data_break=="") $data_break.=$row[csf('id')].'_'.$row[csf('mst_id')].'_'.$row[csf('description')].'_'.$color_arr[$row[csf('color_id')]].'_'.$size_arr[$row[csf('size_id')]].'_'.$row[csf('qnty')].'_'.$row[csf('book_con_dtls_id')];
							else $data_break.='***'.$row[csf('id')].'_'.$row[csf('mst_id')].'_'.$row[csf('description')].'_'.$color_arr[$row[csf('color_id')]].'_'.$size_arr[$row[csf('size_id')]].'_'.$row[csf('qnty')].'_'.$row[csf('book_con_dtls_id')];
						}
					}
					else
					{
						if($data_break=="")
						{	
							$sql = "select  a.id, a.mst_id , a.color_id, a.size_id, a.production_qty , a.booking_dtls_id as book_con_dtls_id,b.item from trims_production_dtls a, trims_production_mst b where a.mst_id=b.id and a.receive_dtls_id in ('$receive_dtls_id') and a.status_active=1 and a.is_deleted=0 order by a.id desc"; //die;
							$data_arr=sql_select($sql);
							//$data_break="";
							foreach($data_arr as $row)
							{//echo "1--";
								if($row[csf('id')]=="") $row[csf('id')]=0;
								if($row[csf('mst_id')]=="") $row[csf('mst_id')]=0;
								if($row[csf('item')]=="") $row[csf('item')]=0;
								if($row[csf('color_id')]=="") $row[csf('color_id')]=0;
								if($row[csf('size_id')]=="") $row[csf('size_id')]=0;
								if($row[csf('production_qty')]=="") $row[csf('production_qty')]=0;
								if($row[csf('book_con_dtls_id')]=="") $row[csf('book_con_dtls_id')]=0;
								//if($break_down_arr[$row[csf('id')]]=="") $break_down_arr[$row[csf('id')]]=0;
								//echo $data_break."++";
								if($data_break=="") $data_break.=$row[csf('id')].'_'.$row[csf('mst_id')].'_'.$row[csf('item')].'_'.$row[csf('color_id')].'_'.$row[csf('size_id')].'_'.$row[csf('production_qty')].'_'.$row[csf('book_con_dtls_id')];
								else $data_break.='***'.$row[csf('id')].'_'.$row[csf('mst_id')].'_'.$row[csf('item')].'_'.$row[csf('color_id')].'_'.$row[csf('size_id')].'_'.$row[csf('production_qty')].'_'.$row[csf('book_con_dtls_id')];
							}
						}
						
					}
					//echo $data_break."nazim";// die;
					$data_array=explode("***",$data_break);
					//echo $within_group;
					$k=0;
					//echo "nazivcbcxbcm"; die;
					//echo count($data_array);
					if($within_group==1) $disabled="disabled"; else $disabled="";
					if(count($data_array)>0)
					{
						foreach($data_array as $row)
						{
							$data=explode('_',$row);
							$k++;
							?>
							<tr>
								<td><input type="text" id="txtdescription_<? echo $k;?>" name="txtdescription_<? echo $k;?>" class="text_boxes" style="width:200px" value="<? echo $data[2]; ?>" disabled />
								</td>
								<td>
									<input type="text" id="txtcolor_<? echo $k;?>" name="txtcolor_<? echo $k;?>" class="text_boxes txt_color" style="width:150px" value="<? echo $color_arr[$data[3]]; ?>" disabled >
									<input type="hidden" id="txtcolorId_<? echo $k;?>" name="txtcolorId_<? echo $k;?>" class="text_boxes_numeric" style="width:90px" value="<? echo $data[3]; ?>"  /></td>
								<td><input type="text" id="txtsize_<? echo $k;?>" name="txtsize_<? echo $k;?>" class="text_boxes txt_size" style="width:150px" value="<? echo $size_arr[$data[4]]; ?>" disabled >
									<input type="hidden" id="txtsizeID_<? echo $k;?>" name="txtsizeID_<? echo $k;?>" class="text_boxes_numeric" style="width:70px" value="<? echo $data[4]; ?>"></td>
								<td>
									<input type="text" id="txtorderquantity_<? echo $k;?>" name="txtorderquantity_<? echo $k;?>" class="text_boxes_numeric" style="width:70px" onKeyUp="sum_total_qnty(<? echo $k;?>);" value="<? echo number_format($data[5],4,'.',''); ?>" />
									<input type="hidden" id="hiddenOrderQuantity_<? echo $k;?>" name="hiddenOrderQuantity_<? echo $k;?>" class="text_boxes_numeric" style="width:70px" value="<? echo $data[5]; ?>"  />
                                    <input type="hidden" id="hiddenid_<? echo $k; ?>" name="hiddenid_<? echo $k; ?>"  style="width:15px;" class="text_boxes" value="<? echo $data[0]; ?>" />
                                    <input type="hidden" id="hiddenUpid_<? echo $k; ?>" name="hiddenUpid_<? echo $k; ?>"  style="width:15px;" class="text_boxes" value="" />
                                    <input type="hidden" id="hiddenMstid_<? echo $k; ?>" name="hiddenMstid_<? echo $k; ?>"  style="width:15px;" class="text_boxes" value="<? echo $data[1]; ?>" />
                                    <input type="hidden" id="hidbookingconsid_<? echo $k; ?>" name="hidbookingconsid_<? echo $k; ?>"  style="width:15px;" class="text_boxes" value="<? echo $data[6]; ?>" />
								</td>
								
							</tr>
							<?
						}
					}
					else
					{
						?>
                        <tr>
                            <td><input type="text" id="txtdescription_1" name="txtdescription_1" class="text_boxes" style="width:120px" value="" /></td>
                            <td><input type="text" id="txtcolor_1" name="txtcolor_1" class="text_boxes txt_color" style="width:90px" value="" /><input type="hidden" id="txtcolorID_1" name="txtcolorID_1" class="text_boxes_numeric" style="width:90px" value="" /></td>
                            <td><input type="text" id="txtsize_1" name="txtsize_1" class="text_boxes txt_size" style="width:70px" value="" ><input type="hidden" id="txtsize_ID_1" name="txtsize_ID_1" class="text_boxes_numeric" style="width:90px" value="" /></td>
                            <td>
                                <input type="text" id="txtorderquantity_1" name="txtorderquantity_1" class="text_boxes_numeric" style="width:70px" onKeyUp="sum_total_qnty(1);" value="" />
                                <input type="hidden" id="hiddenOrderQuantity_1" name="hiddenOrderQuantity_1" class="text_boxes_numeric" style="width:70px" value="" />
                                <input type="hidden" id="hidbookingconsid_1" name="hidbookingconsid_1"  style="width:15px;" class="text_boxes" value="" />
                                <input type="hidden" id="hiddenid_1" name="hiddenid_1"  style="width:15px;" class="text_boxes" value="" />
                                <input type="hidden" id="hiddenMstid_1" name="hiddenMstid_1"  style="width:15px;" class="text_boxes" value="" />
                            </td>
                        </tr>
						<?
					}
					?> 
				</tbody>
				<tfoot>
					<th colspan="3">Total</th> 
					<th><input type="text" id="txt_total_order_qnty" name="txt_total_order_qnty" class="text_boxes_numeric" readonly style="width:70px" value="<? echo $break_tot_qty;//number_format($break_tot_qty,4); ?>"; /></th>
				</tfoot>
			</table> 
			<table>
				<tr>
					<td align="center"><input type="button" name="main_close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" /></td>
				</tr>
			</table>
		</form>
	</div>
</body>
<script>sum_total_qnty(0);</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}
if($action=="check_conversion_rate")
{
	//$data=explode("**",$data);
	
	/*if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}*/
	$conversion_date=date("Y/m/d");
	$exchange_rate=set_conversion_rate( $data, $conversion_date );
	echo $exchange_rate;
	exit();	
}

if($action=="check_uom")
{
	$uom=return_field_value( "order_uom","lib_item_group","id='$data'");
	echo $uom;
	exit();	
}


if ($action=="delivery_popup")
	{
		echo load_html_head_contents("Delivery Popup Info","../../../", 1, 1, $unicode,'','');
		?>
		<script>
			function js_set_value(id)
			{ 
				$("#hidden_mst_id").val(id);
				document.getElementById('selected_job').value=id;
				parent.emailwindow.hide();
			}
			
			function fnc_load_party_popup(type,within_group)
			{
				var company = $('#cbo_company_name').val();
				var party_name = $('#cbo_party_name').val();
				var location_name = $('#cbo_location_name').val();
				load_drop_down( 'trims_production_entry_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
			}
			function search_by(val)
			{
				$('#txt_search_string').val('');
				if(val==1 || val==0)
				{
					$('#search_by_td').html('System ID');
				}
				else if(val==2)
				{
					$('#search_by_td').html('W/O No');
				}
				else if(val==3)
				{
					$('#search_by_td').html('Buyer Job');
				}
				else if(val==4)
				{
					$('#search_by_td').html('Buyer Po');
				}
				else if(val==5)
				{
					$('#search_by_td').html('Buyer Style');
				}
			}
		</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        <table width="940" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
	            <thead> 
	                <tr>
	                    <th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
	                </tr>
	                <tr>               	 
	                    <th width="140" class="must_entry_caption">Company Name</th>
	                    <th width="100">Within Group</th>                           
	                    <th width="140">Party Name</th>
	                    
	                    <th width="100" id="search_by_td">System ID</th>
	                    <th width="100">Year</th>
	                    <th width="170">Date Range</th>                            
	                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
	                </tr>           
	            </thead>
	            <tbody>
	                <tr class="general">
	                    <td><input type="hidden" id="selected_job"><? $data=explode("_",$data); ?>  <!--  echo $data;-->
	                        <? 
	                        echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
	                    </td>
	                    <td>
	                        <?php echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 0, "--  --", $data[3], "fnc_load_party_popup(1,this.value);",1 ); ?>
	                    </td>
	                    <td id="buyer_td">
	                        <? echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );   	 
	                        ?>
	                    </td>
	                    <td style="display: none;">
	                    							<?
	                            $search_by_arr=array(1=>"System ID",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
	                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
	                        ?>
	                    </td>
	                    <td align="center">
	                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
	                    </td>
	                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 100, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
	                    <td align="center">
	                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
	                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
	                    </td>
	                    <td align="center">
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value, 'create_delivery_search_list_view', 'search_div', 'trims_delivery_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
	                    </tr>
	                    <tr>
	                        <td colspan="8" align="center" valign="middle">
	                            <? echo load_month_buttons();  ?>
	                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
	                        </td>
	                    </tr>
	                    <tr>
	                        <td colspan="8" align="center" valign="top" id=""><div id="search_div"></div></td>
	                    </tr>
	                </tbody>
	            </table>    
	            </form>
	        </div>
	    </body>           
	    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	    </html>
	    <?
	    exit();
	}

	if($action=="create_delivery_search_list_view")
	{	
		$data=explode('_',$data);
		$party_id=str_replace("'","",$data[1]);
		$search_by=str_replace("'","",$data[4]);
		$search_str=trim(str_replace("'","",$data[5]));
		$search_type =$data[6];
		$within_group =$data[7];
		if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
		if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}

		if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
		//echo $search_type; die;
		$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
		if($search_type==1)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.del_no_prefix='$search_str'";
				else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
				
				if ($search_by==3) $job_cond=" and a.del_no_prefix = '$search_str' ";
				else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
				else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";
			}
		}
		else if($search_type==4 || $search_type==0)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.del_no_prefix like '%$search_str%'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
				
				if ($search_by==3) $job_cond=" and a.del_no_prefix like '%$search_str%'";  
				else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str%'"; 
				else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str%'";   
			}
		}
		else if($search_type==2)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.del_no_prefix like '$search_str%'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
				
				if ($search_by==3) $job_cond=" and a.del_no_prefix like '$search_str%'";  
				else if ($search_by==4) $po_cond=" and b.po_number like '$search_str%'";
				else if ($search_by==5) $style_cond=" and a.style_ref_no like '$search_str%'";  
			}
		}
		else if($search_type==3)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.del_no_prefix like '%$search_str'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
				
				if ($search_by==3) $job_cond=" and a.del_no_prefix like '%$search_str'";  
				else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'";
				else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str'";  
			}
		}	

		if($party_id!=0) $party_id_cond=" and a.party_id='$party_id'"; else $party_id_cond="";

		if($db_type==0)
		{ 
			if ($data[2]!="" &&  $data[3]!="") $delivery_date = "and a.delivery_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $delivery_date ="";
		}
		else
		{
			if ($data[2]!="" &&  $data[3]!="") $delivery_date = "and a.delivery_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $delivery_date ="";
		}
		if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
		if($within_group==1)
		{
			$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		}
		else
		{
			$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		}

		if($db_type==0) 
		{
			$ins_year_cond="year(a.insert_date)";
		}
		else if($db_type==2)
		{
			$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		}
		
		$sql= "select a.id, a.trims_del, a.del_no_prefix, a.del_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.delivery_date, a.receive_id, a.order_id, a.challan_no, a.gate_pass_no ,$ins_year_cond as year from trims_delivery_mst a, trims_delivery_dtls b where a.entry_form=208 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $delivery_date $company $buyer $withinGroup $search_com_cond $po_idsCond $withinGroup group by a.id, a.trims_del, a.del_no_prefix, a.del_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.delivery_date, a.receive_id, a.order_id, a.challan_no, a.gate_pass_no ,a.insert_date order by a.id DESC";
		 //echo $sql;
		 $data_array=sql_select($sql);
		?>
	     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="685" >
	        <thead>
	            <th width="30">SL</th>
	            <th width="170">Delevery No</th>
	            <th width="80">Year</th>
	            <th width="170">Challan No.</th>
	            <th>Gate Pass No.</th>
	        </thead>
	        </table>
	        <div style="width:685px; max-height:270px;overflow-y:scroll;" >	 
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="665" class="rpt_table" id="tbl_po_list">
	        <tbody>
	            <? 
	            $i=1;
	            foreach($data_array as $row)
	            {  
	                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                ?>
	                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('trims_del')]; ?>")' style="cursor:pointer" >
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="170"><? echo $row[csf('trims_del')]; ?></td>
	                    <td width="80" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
	                    <td width="170"><? echo $row[csf('challan_no')]; ?></td>
	                    <td style="text-align:center;"><? echo $row[csf('gate_pass_no')]; ?></td>
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

	if ($action=="load_delivery_data_to_form")
	{
		$sql="select a.id, a.trims_del, a.del_no_prefix, a.del_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.delivery_date, a.receive_id, a.order_id, a.challan_no, a.gate_pass_no, a.remarks from trims_delivery_mst a where a.entry_form=208 and a.id=$data and a.status_active=1 ";

		$nameArray=sql_select( $sql );
		foreach ($nameArray as $row)
		{
			$order_no = return_field_value("order_no", "subcon_ord_mst", "id=".$row[csf("receive_id")]."", "order_no");

			echo "document.getElementById('txt_dalivery_no').value 			= '".$row[csf("trims_del")]."';\n";
			echo "document.getElementById('update_id').value 				= '".$row[csf("id")]."';\n";
			echo "document.getElementById('received_id').value 				= '".$row[csf("receive_id")]."';\n";
			echo "document.getElementById('txt_order_no').value 			= '".$order_no."';\n";  
			echo "document.getElementById('hid_order_id').value 			= '".$row[csf("order_id")]."';\n";  

			echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_id")]."';\n";
			echo "document.getElementById('cbo_within_group').value 		= '".$row[csf("within_group")]."';\n";  
			
			echo "document.getElementById('txt_challan_no').value 			= '".$row[csf("challan_no")]."';\n";  
			echo "document.getElementById('txt_gate_pass_no').value 		= '".$row[csf("gate_pass_no")]."';\n";  
			echo "document.getElementById('cbo_currency').value 			= '".$row[csf("currency_id")]."';\n";  
			echo "document.getElementById('txt_remarks').value 				= '".$row[csf("remarks")]."';\n";  
			
			echo "fnc_load_party(2,'".$row[csf("within_group")]."');\n";
			echo "document.getElementById('cbo_party_name').value			= ".$row[csf("party_id")].";\n";
			echo "fnc_load_party(2,'".$row[csf("within_group")]."');\n";
			echo "document.getElementById('cbo_party_location').value		= ".$row[csf("party_location")].";\n";
			echo "document.getElementById('cbo_location_name').value 		= ".$row[csf("location_id")].";\n";
			echo "document.getElementById('txt_delivery_date').value		= '".change_date_format($row[csf("delivery_date")])."';\n"; 
			echo "$('#cbo_company_name').attr('disabled','true')".";\n";
			echo "$('#cbo_within_group').attr('disabled','true')".";\n";
			echo "$('#cbo_party_name').attr('disabled','true')".";\n";
		}
		exit();	
	}	

if($action=="challan_print") 
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$sql_company = sql_select("SELECT * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
  	foreach($sql_company as $company_data) 
  	{
		if($company_data[csf('plot_no')]!='')$plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
		if($company_data[csf('level_no')]!='')$level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
		if($company_data[csf('road_no')]!='')$road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
		if($company_data[csf('block_no')]!='')$block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
		if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
		if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
		if($company_data[csf('country_id')]!=0)$country = $company_data[csf('country_id')].','.' ';else $country='';
		
		$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
	}
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$company_plot_no_arr=return_library_array( "select id, plot_no from lib_company",'id','plot_no');
?>
	<style type="text/css">
			.opacity_1
			{
				opacity:0.3; 
				position: absolute; 
				font-size: 400px; 
				left: 40%; 
				top: 20%;
			}	
			.opacity_2
			{
				opacity:0.3; 
				position: absolute; 
				font-size: 400px; 
				left: 40%; 
				top: 230%;
			}
			.opacity_3
			{
				opacity:0.3; 
				position: absolute; 
				font-size: 400px; 
				left: 40%; 
				top: 20%;
			}
			
			
			
			
			@media print {
				.page-break	{ display: block; page-break-after: always;}
			}
			
			#table_1,#table_2,#table_3{  background-position: center;background-repeat: no-repeat; }
			#table_1{background-image:url(../../../img/bg-1.jpg);}
			#table_2{background-image:url(../../../img/bg-2.jpg); }
			#table_3{background-image:url(../../../img/bg-3.jpg);}
			
		</style>
		<?
			$sql_mst = sql_select("select id, entry_form, trims_del, del_no_prefix, del_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id,  delivery_date, receive_id, order_id, challan_no, gate_pass_no, remarks from trims_delivery_mst where id= $data[1]");
			
			
	
	
	if($data[2]==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
	
	$lib_location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	//$copy_print = 3;
	//for($k=1; $k <= $copy_print; $k++)
	//{
	$k=0;	
	$copy_no=array(1,2,3); //for Dynamic Copy here 
	 foreach($copy_no as $cid)
	 {
		 $k++;
	?>
        
    <div style="width:1200px" class="page-break">
        <table width="100%" id="table_<? echo $cid;?>">
			<tr>
				<td rowspan="2" width="200">
					<img src="../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
				</td>
            	<td style="font-size:20px;" align="center" colspan="6">
					<? echo $company_arr[$data[0]]; ?>
                </td>
                <td align="right">
					<? 
					if($k==1){
					echo "1st Copy";
					}
					else if($k==2){
					echo "2nd Copy";
					}
					else if($k==3){
					echo "3rd Copy";
					}
					?> 
				</td>
            </tr>
            <tr>
				<td colspan="6" align="center">
					<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, vat_number,city from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
					foreach ($nameArray as $result)
					{ 
						?>
						<? echo $result[csf('city')]; ?><br>
						<b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
					}
					?> 
				</td>
        		<td id="barcode_img_id_<? echo $k; ?>" colspan="2"></td>
			</tr>
			<tr>
            	<td rowspan="2">&nbsp;</td>
            	<td style="font-size:20px;" align="center" colspan="6">
					<strong><? echo $data[3]; ?></strong>
                </td>
            </tr> 
            <tr>
            	<td colspan="2" rowspan="2">&nbsp;</td>
            	<td style="font-size:20px;" align="center" colspan="6">&nbsp; </td>
            </tr> 
        </table>
        <br>
		<table class="rpt_table" width="100%" cellspacing="1" >
            <tr>
                <td valign="top" width="100">Delevery To</td>
                <td valign="top" width="150">: <? echo $party_arr[$sql_mst[0][csf("party_id")]]; ?></td>
                <td valign="top" width="250">&nbsp;</td>
                <td valign="top" width="120">Challan No. </td>
                <td valign="top">: <? echo $data[5]; ?></td>
            </tr>
            <tr>
            	<td valign="top" width="120">Address</td>
                <td valign="top">: <? echo $lib_location_arr[$sql_mst[0][csf("party_location")]]; ?></td>
                <td valign="top" width="250">&nbsp;</td>
                <td valign="top" width="100">Delevery Date</td>
                <td valign="top" width="150">: <? echo change_date_format($sql_mst[0][csf("delivery_date")],'yyyy-mm-dd'); ?></td>
            </tr>
            <tr>
            	<td valign="top" width="100">WO NO.</td>
                <td valign="top" width="150">: <? echo $data[4];//$order_no_trims_arr[$sql_mst[0][csf("receive_id")]]['order_no']; ?></td>
            </tr>
      	</table>
         <br>
      	<table  class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
      		<thead>
	            <tr>
	            	<th width="40">SL</th>
                    <th width="130">Cust. PO</th>
                    <th width="80">Section</th>
	                <th width="80">Item Group</th>
	                <th width="140">Item Description</th>	
                    <th width="80">RMG Color </th>
	                <th width="80">RMG Size</th>				
	                <th width="70">Order UOM</th>
                    <th width="80">WO Qty.</th>
	                <th width="80">Cum. Delv Qty</th>
	                <th width="80">Curr. Delv Qty</th>
	                <th width="80">Delv Balance Qty</th>
	                <th>Remarks</th>
	            </tr>
            </thead>
            <tbody>
			<?
			$i = 1;
			
			
				$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
				 $total_ammount = 0; $total_quantity=0;
				$style_ref_no=return_library_array( "select job_no,style_ref_no from fabric_sales_order_mst", "Job_no", "Style_ref_no" );
				
			$sql = "select id, mst_id, booking_dtls_id, receive_dtls_id, job_dtls_id, production_dtls_id,  order_id, order_no, buyer_po_id, buyer_po_no,  buyer_style_ref, buyer_buyer, section,   item_group as trim_group, order_uom, order_quantity,   delevery_qty, claim_qty, remarks,color_id, size_id, 
   description, delevery_status, color_name, 
   size_name,workoder_qty from trims_delivery_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0 order by id ASC";
   
   
   	$delevery_qty_trims_arr=array();
	$pre_sql ="Select job_dtls_id, sum(delevery_qty) as delevery_qty  from trims_delivery_dtls where status_active=1 and is_deleted=0 group by job_dtls_id";
	$pre_sql_res=sql_select($pre_sql);
	foreach ($pre_sql_res as $row)
	{
		$delevery_qty_trims_arr[$row[csf("job_dtls_id")]]['delevery_qty']=$row[csf("delevery_qty")];
		
	}
	unset($pre_sql_res);
	
	
	
	
	
				$data_array=sql_select($sql);
				foreach($data_array as $row)
				{
				?>
                    <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $row[csf('buyer_po_no')]; ?></td>
                    <td><?php echo $trims_section[$row[csf('section')]]; ?></td>
	                <td><?php echo $item_group_arr[$row[csf('trim_group')]]; ?></td>
	                <td><?php echo $row[csf('description')]; ?></td>	
                    <td><?php echo $row[csf('color_name')]; ?> </td>
	                <td><?php echo $row[csf('size_name')]; ?></td>				
	                <td><?php echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                    <td><?php echo $row[csf('workoder_qty')];$total_quantity += $row[csf('quantity')]; ?></td>
	                <td><?php echo  $CumDelvQty=$delevery_qty_trims_arr[$row[csf("job_dtls_id")]]['delevery_qty']-$row[csf('delevery_qty')]; ?></td>
	                <td><?php echo $row[csf('delevery_qty')]; ?></td>
	                <td><?php echo $row[csf('workoder_qty')]-($row[csf('delevery_qty')]+$CumDelvQty); ?></td>
	                <td><?php echo $row[csf('remarks')]; ?></td>
                    </tr>
				<? 
				$i++;
                } 
                ?>
            </tbody> 
        </table>
	</div>
    <br>
    <br>
    
<?


	

?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    function generateBarcode( valuess )
    {
        var value = valuess;//$("#barcodeValue").val();
        var btype = 'code39';//$("input[name=btype]:checked").val();
        var renderer ='bmp';// $("input[name=renderer]:checked").val();
        var settings = {
          output:renderer,
          bgColor: '#FFFFFF',
          color: '#000000',
          barWidth: 1,
          barHeight: 30,
          moduleSize:5,
          posX: 10,
          posY: 20,
          addQuietZone: 1
        };
        $("#barcode_img_id_<? echo $k; ?>").html('11');
         value = {code:value, rect: false};
        $("#barcode_img_id_<? echo $k; ?> ").show().barcode(value, btype, settings);
    } 
    generateBarcode("<? echo $data[0]; ?>");
    </script>
   <?
  	 
	 }
 exit();
	
 }

?>