<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$buyer_short_name_arr=return_library_array( "SELECT id, short_name from lib_buyer where status_active =1 and is_deleted=0",'id','short_name');
$company_short_name_arr=return_library_array( "SELECT id,company_short_name from lib_company where status_active =1 and is_deleted=0",'id','company_short_name');
$imge_arr=return_library_array( "SELECT id,master_tble_id,image_location from common_photo_library where status_active =1 and is_deleted=0",'id','image_location');
$party_arr=return_library_array( "SELECT id, buyer_name from  lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');

if ($action=="load_drop_down_buyer")
{
	//echo $data; die;
	$data=explode('_',$data);
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_buyer_id", 150, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "");
		exit();
	}
	else
	{
		echo create_drop_down( "cbo_buyer_id", 150, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$data[2], "" );
		exit();
	}
}

if ($action=="job_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	$data=explode("_",$data);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('selected_order').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_order_popup(company,within_group,party)
		{
			//alert();
			load_drop_down( 'order_wise_production_and_delivery_report_controller', company+'_'+within_group+'_'+party, 'load_drop_down_buyer', 'buyer_td' );
			
			$('#cbo_buyer_id').attr('disabled','disabled');
		}
		
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0)
			{
				$('#search_by_td').html('Embl. Job No');
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
	<body onLoad="fnc_load_party_order_popup(<? echo $data[0];?>,<? echo $data[2];?>,<? echo $data[1];?>)">
        <div align="center" style="width:100%;" >
            <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
                <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                    	<tr>               	 
                            <th width="140">Company Name</th>
                            <th width="140">Party Name</th>
                            <th width="170">Date Range</th>
                            <th width="100">Search By</th>
                            <th width="100" id="search_by_td">Embl. Job No</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="hidden" id="selected_order">  
								<?   
									echo create_drop_down( "cbo_company_id", 140, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $data[0],"",1 );
                                ?>
                            </td>
                            <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_id", 140, $blank_array,"", 1, "-- Select Party --", $selected, "" ); ?>
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td>
								<?
									$search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
									echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
								?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $data[2];?>+'_'+<? echo $data[3];?>, 'create_job_search_list_view', 'search_div', 'order_wise_production_and_delivery_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" align="center" height="40" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                    </tbody>
                </table> 
                <br>
                <div id="search_div"></div>   
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
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	$year =$data[8];
	$year_job = str_replace("'","",$year);

    if($db_type==0) 
	{
		$year_field="year(a.insert_date) as year"; 
		if(trim($year)!=0) $year_field_cond="and YEAR(a.insert_date)=$year_job";
	}
    else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
		if(trim($year)!=0) $year_field_cond=" and to_char(a.insert_date,'YYYY')=$year_job";
	}
    else 
	{
		$year_field="";
		$year_field_cond="";
	}
	
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { $company=""; echo "PLease Select Company name"; die;}
	if ($data[1]!=0) $buyer=" and a.party_id='$data[1]'"; else { $buyer=""; echo "PLease Select Party name"; die;}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $style_cond=" and a.style_ref_no = '$search_str' ";
			else if ($search_by==5) $po_cond=" and b.po_number = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $style_cond=" and a.style_ref_no like '%$search_str%'";  
			else if ($search_by==5) $po_cond=" and b.po_number like '%$search_str%'";  
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $style_cond=" and a.style_ref_no like '$search_str%'";  
			else if ($search_by==5) $po_cond=" and b.po_number like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $style_cond=" and a.style_ref_no like '%$search_str'";  
			else if ($search_by==5) $po_cond=" and b.po_number like '%$search_str'";  
		}
	}	
	
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";
	}
	
	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	if(($job_cond!="" && $search_by==3) || ($style_cond!="" && $search_by==4)|| ($po_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
	}
	
	if ($po_ids!="") 
	{
		$po_idsCond=" and b.buyer_po_id in ($po_ids)";
	} 
	else
	{
		$po_idsCond="";
	}
	
	$buyer_po_arr=array();
	
	$buyer_po_arr=array();
	$order_sql ="select  a.id as job_id, a.job_no_prefix_num, a.within_group,b.id,b.buyer_po_no,b.buyer_style_ref, b.job_no_mst,b.order_no,b.buyer_buyer from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.entry_form='204' $search_com_cond $year_field_cond";
	//echo $order_sql; die;
	$order_sql_res=sql_select($order_sql); $all_subcon_job='';
	foreach ($order_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
		$buyer_po_arr[$row[csf("id")]]['order_no']=$row[csf("order_no")];
		$buyer_po_arr[$row[csf("id")]]['buyer_buyer']=$row[csf("buyer_buyer")];
		$buyer_po_arr[$row[csf("id")]]['within_group']=$row[csf("within_group")];
		//$po_id_arr[$row[csf("id")]]=$row[csf("id")];
		//$all_subcon_job .="'".$row[csf("job_no_mst")]."'".',';
		$all_subcon_job .=$row[csf("job_id")].',';
	}
	
	$comp=return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
	$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
	//$color_lib_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	//$arr=array (3=>$comp,6=>$yes_no,7=>$color_lib_arr);
	
	if($db_type==0)
	{
		$ins_year_cond="year(a.insert_date)";
		$color_id_str="group_concat(c.color_id)";
		$dtls_id_str="group_concat(b.id)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str="listagg(c.color_id,',') within group (order by c.color_id)";
		$dtls_id_str="listagg(b.id,',') within group (order by b.id)";
	}
	$sql= "SELECT a.id, a.embellishment_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, $color_id_str as color_id  ,$dtls_id_str as order_id
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	where a.entry_form=204 and a.embellishment_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $year_field_cond and b.id=c.mst_id  
	group by a.id, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date
	order by a.id DESC";

	//echo $sql;die;

	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="865" >
        <thead>
            <th width="15">SL</th>
            <th width="60">Job No</th>
            <th width="60">Year</th>
            <th width="120">W/O No</th>
            <th width="100">Buyer Po</th>
            <th width="100">Buyer Style</th>
            <th width="80">Ord Receive Date</th>
            <th width="80">Delivery Date</th>
            <th>Color</th>
        </thead>
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$excolor_id=array_unique(explode(",",$row[csf('color_id')]));
				$color_name=$buyer_po=$buyer_style="";	
				foreach ($excolor_id as $color_id)
				{
					if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
				}

				$order_id=explode(",",$row[csf('order_id')]);
				foreach($order_id as $po_id)
				{
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
					if($row[csf('within_group')]==1)
					{
					if($buyer_name=="") $buyer_name=$buyer_arr[$buyer_po_arr[$po_id]['buyer_buyer']]; else $buyer_name.=','.$buyer_arr[$buyer_po_arr[$po_id]['buyer_buyer']];
					}
					else if($row[csf('within_group')]==2)
					{
					if($buyer_name=="") $buyer_name=$buyer_po_arr[$po_id]['buyer_buyer']; else $buyer_name.=','.$buyer_po_arr[$po_id]['buyer_buyer'];
					}
					
				}
				$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				$buyer_name=implode(",",array_unique(explode(",",$buyer_name)));


                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('embellishment_job')].'_'.$row[csf("id")]; ?>")' style="cursor:pointer" >
                    <td width="25"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="120"><? echo $row[csf('order_no')]; ?></td>
                    <td width="100"><? echo $buyer_po; ?></td>
                    <td width="100"><? echo $buyer_style; ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>	
                    <td><? echo $color_name; ?></td>
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

if($action=="style_no_popup")
{
	echo load_html_head_contents("Style Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id)
		{
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
    </script>
    <input type="hidden" id="selected_id" name="selected_id" /> 
	<?
	$year_job = str_replace("'","",$year);

    if($db_type==0) 
	{
		$year_field="year(a.insert_date) as year"; 
		 if(trim($year)!=0) $year_field_cond="and YEAR(a.insert_date)=$year_job";
	}
    else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
		 if(trim($year)!=0) $year_field_cond=" and to_char(a.insert_date,'YYYY')=$year_job";
	}
    else 
	{
		$year_field="";
		$year_field_cond="";
	}
    
    if($cbo_buyer_name==0) $buyer_cond=""; else $buyer_cond=" and a.party_id='$cbo_buyer_name'";
    
    $buyer=return_library_array( "SELECT id,buyer_name from lib_buyer where status_active =1 and is_deleted=0", "id", "buyer_name");
	
   $sql="SELECT a.party_id, a.subcon_job, a.job_no_prefix_num, $year_field, b.order_no, b.cust_style_ref, b.main_process_id from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.company_id='$company_id' $buyer_cond $year_field_cond order by a.id desc";	
	
    ?>
    <table width="500" border="1" rules="all" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="80">Job no</th>
            <th width="70">Year</th>
            <th width="130">Buyer</th>
            <th width="110">Style</th>
            <th width="120">Po number</th>
       </thead>
    </table>
    <div style="max-height:340px; overflow:auto;">
    <table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
     <? $data_array=sql_select($sql);
        $i=1;
		 foreach($data_array as $row)
		 {
			 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		 ?>
			<tr bgcolor="<? echo $bgcolor; ?>"  onClick="js_set_value('<? echo $row[csf('cust_style_ref')]; ?>')" style="cursor:pointer;">
				<td width="30"><? echo $i; ?></td>
				<td align="center"  width="80"><? echo $row[csf('job_no_prefix_num')]; ?></td>
				<td align="center" width="70"><? echo $row[csf('year')]; ?></td>
				<td width="130"><? echo $buyer[$row[csf('party_id')]]; ?></td>
				<td width="110"><p><? echo $row[csf('cust_style_ref')]; ?></p></td>
				<td width="120"><p><? echo $row[csf('order_no')]; ?></p></td>
			</tr>
			<? $i++; 
			} 
		?>
    </table>
    </div>
	<script> setFilterGrid("table_body2",-1); </script>
    <?
	disconnect($con);
	exit();
}

if($action=="order_no_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script type="text/javascript">
		function js_set_value(id)
		{ 
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" id="selected_id" name="selected_id" /> 
	<?
	$buyer = str_replace("'","",$buyer_name);
	$year = str_replace("'","",$year);
	$buyer = str_replace("'","",$buyer_name);
	$job_no = str_replace("'","",$job_no);

    if($db_type==0) 
	{
		$year_field="year(a.insert_date) as year"; 
		 if(trim($year)!=0) $year_field_cond="and YEAR(a.insert_date)=$year";  else $year_field_cond="";
	}
    else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
		 if(trim($year)!=0) $year_field_cond=" and to_char(a.insert_date,'YYYY')=$year"; else $year_field_cond="";
	}
    else 
	{
		$year_field="";
		$year_field_cond="";
	}
	
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num=$job_no";
	//echo $buyer;die;
	
	if(trim($buyer)==0) $sub_buyer_name_cond=""; else $sub_buyer_name_cond=" and a.party_id=$buyer";
	
	$sql="SELECT distinct b.id, b.job_no_mst as job_no ,a.party_id as buyer_name, b.order_no as po_number, a.job_no_prefix_num as job_prefix, $year_field from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where b.job_no_mst=a.subcon_job and a.id=c.mst_id and b.id=c.order_id and a.company_id=$company_id $sub_buyer_name_cond $job_no_cond $year_field_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.party_id, b.job_no_mst, b.order_no, a.job_no_prefix_num, a.company_id, a.insert_date";	
	
	$buyer=return_library_array( "SELECT id,buyer_name from lib_buyer where status_active =1 and is_deleted=0", "id", "buyer_name"  );
	?>
	<table width="500" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="100">Order Number</th>
                <th width="50">Job no</th>
                <th width="80">Buyer</th>
                <th width="40">Year</th>
            </tr>
        </thead>
    </table>
    <div style="max-height:340px; overflow:auto;">
        <table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
			<? 
			$rows=sql_select($sql);
            $i=1;
            foreach($rows as $data)
            {
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $data[csf('id')].'_'.$data[csf('po_number')]; ?>')" style="cursor:pointer;">
                    <td width="30"><? echo $i; ?></td>
                    <td width="100"><p><? echo $data[csf('po_number')]; ?></p></td>
                    <td width="50"><p><? echo $data[csf('job_prefix')]; ?></p></td>
                    <td width="80"><p><? echo $buyer[$data[csf('buyer_name')]]; ?></p></td>
                    <td width="40" align="center"><p><? echo $data[csf('year')]; ?></p></td>
				</tr>
				<? $i++; 
			} ?>
		</table>
	</div>
	<script> setFilterGrid("table_body2",-1); </script>
	<?
	exit();
}

if($action=="report_generate")
{ 
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$job_no=str_replace("'","",$txt_job_no);
	$job_id=str_replace("'","",$txt_job_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	/*$job_data=explode("_",$job_no);
	$job_no_mst=$job_data[0];
	$job_id=$job_data[1];
	$job_data=explode("_",$job_no);*/
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$year_id=str_replace("'","",$cbo_year);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$cbo_within_group=str_replace("'","",$cbo_within_group);
	if($txt_date_from!="" || $txt_date_to!="")
    {
        if($db_type==0) $sys_cond .= " and b.delivery_date '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
        else if($db_type==2) $sys_cond .= " and b.delivery_date between '".change_date_format($txt_date_from,'yyyy-mm-dd','',-1)."' and '".change_date_format($txt_date_to,'yyyy-mm-dd','',-1)."'";
    } 

    if ($db_type == 0)
    {
    	$sys_cond .= " and YEAR(a.insert_date) = $cbo_year ";
    }
    else if ($db_type == 2)
    {
    	$sys_cond .= " and to_char(a.insert_date,'YYYY') = $cbo_year ";
    }

	if($cbo_within_group!=0) $sys_cond.=" and a.within_group=$cbo_within_group ";
	if($cbo_company_id!=0) $sys_cond.=" and a.company_id=$cbo_company_id ";
	if($job_id!='') $sys_cond.=" and a.id=$job_id ";
	//if ($job_no!="") $sys_cond.=" and a.job_no_prefix_num in ($job_no) ";
	if ($txt_style_ref!="") $sys_cond.=" and b.buyer_style_ref like '%$txt_style_ref%' ";
	if ($txt_order_no!="") $sys_cond.=" and a.order_no like '%$txt_order_no%' ";
	//if ($job_no!="") $sys_cond.=" and a.embellishment_job '%$job_no%' ";
	if($cbo_buyer_id!=0) $sys_cond.=" and a.party_id=$cbo_buyer_id ";
	$buyer_arr=return_library_array( "SELECT id,buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name"  ); 
	$company_library=return_library_array( "SELECT id,company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name"  );
	$color_library_arr=return_library_array( "SELECT id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

	if($db_type==0) $group_concat="group_concat(c.id) as color_size_id";
	else if($db_type==2) $group_concat="listagg(c.id,',') within group (order by c.id) as color_size_id";
	  	  
	 $sql="SELECT  a.id as jobid,b.id as po_id,a.embellishment_job as job_no, a.party_id,  b.order_no,b.buyer_po_id, a.job_no_prefix_num as job_prefix,b.body_part ,c.color_id, a.delivery_date, $group_concat, sum(c.qnty) as order_qty, sum(b.wastage) as plan_qty,b.order_uom,b.buyer_po_no, b.buyer_style_ref,b.buyer_buyer
	from subcon_ord_mst a ,subcon_ord_dtls b, subcon_ord_breakdown c
	where a.id=b.mst_id and b.id=c.mst_id and c.job_no_mst=b.job_no_mst and a.entry_form=204 $sys_cond $sql_con $job_cond $wo_cond $year_cond $party_con $buyer_con $location_con $party_location_con $po_con $po_id_con $style_con and c.qnty<>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id,b.id,a.embellishment_job , a.party_id, b.order_no,b.buyer_po_id, a.job_no_prefix_num,b.body_part ,c.color_id, a.delivery_date,b.order_uom,b.buyer_po_no, b.buyer_style_ref,b.buyer_buyer";
	// echo $sql;die;
	$sql_result=sql_select($sql);//
	foreach($sql_result as $row)
	{
		if($row[csf('order_uom')]==1){
			$order_qty=$row[csf('order_qty')];
		}else if($row[csf('order_uom')]==2){ 
			$order_qty=$row[csf('order_qty')]*12;
		}

		$buyerPoDataArr[$row[csf('within_group')]][$row[csf('party_id')]][$row[csf('buyer_buyer')]][$row[csf('jobid')]][$row[csf('job_no')]][$row[csf('order_no')]][$row[csf('buyer_po_no')]][$row[csf('buyer_style_ref')]][$row[csf('color_id')]][$row[csf('body_part')]]['order_qty'] +=$order_qty;
		$buyerPoDataArr[$row[csf('within_group')]][$row[csf('party_id')]][$row[csf('buyer_buyer')]][$row[csf('jobid')]][$row[csf('job_no')]][$row[csf('order_no')]][$row[csf('buyer_po_no')]][$row[csf('buyer_style_ref')]][$row[csf('color_id')]][$row[csf('body_part')]]['po_id'] =$row[csf('po_id')];
		$buyerPoDataArr[$row[csf('within_group')]][$row[csf('party_id')]][$row[csf('buyer_buyer')]][$row[csf('jobid')]][$row[csf('job_no')]][$row[csf('order_no')]][$row[csf('buyer_po_no')]][$row[csf('buyer_style_ref')]][$row[csf('color_id')]][$row[csf('body_part')]]['delivery_date'] =$row[csf('delivery_date')];
		$buyerPoDataArr[$row[csf('within_group')]][$row[csf('party_id')]][$row[csf('buyer_buyer')]][$row[csf('jobid')]][$row[csf('job_no')]][$row[csf('order_no')]][$row[csf('buyer_po_no')]][$row[csf('buyer_style_ref')]][$row[csf('color_id')]][$row[csf('body_part')]]['color_size_id'] .=$row[csf('color_size_id')].',';
		$all_job_no.=$row[csf('job_no')].',';
		$all_po_id.=$row[csf('po_id')].',';
	}

	$embJobArr=array_unique(explode(",",(chop($all_job_no,','))));
	$embPoIdArr=array_unique(explode(",",(chop($all_po_id,','))));
	// ======================================================================
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=1 and ENTRY_FORM=49");
	oci_commit($con);			
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 49, 1, $embPoIdArr, $empty_arr);//PO ID
	// ===================================================================================

	/*echo '<pre>';
	print_r($buyerPoDataArr);*/
	$q=$s=1;
	$po_id_chunk_arr=array_chunk($embPoIdArr,999);
	foreach($po_id_chunk_arr as $po_arr)
	{
		if($q==1){$sql_po_con =" and (a.po_id in(".implode(",",$po_arr).")";} 
		else{$sql_po_con .=" or a.po_id in(".implode(",",$po_arr).")";}
		$q++;

		if($s==1){$sql_po_id_con =" and (b.id in(".implode(",",$po_arr).")";} 
		else{$sql_po_id_con .=" or b.id in(".implode(",",$po_arr).")";}
		$s++;
	}
	$sql_po_con .=")";
	$sql_po_id_con .=")";
		
	//Receive & Issue................................................
	$p=1;
	$po_chunk_arr=array_chunk($embJobArr,999);
	foreach($po_chunk_arr as $po_arr)
	{
		if($p==1){$sql_con =" and (a.embl_job_no in('".implode("','",$po_arr)."')";} 
		else{$sql_con .=" or a.embl_job_no in('".implode("','",$po_arr)."')";}
		$p++;
	}
	$sql_con .=")";
	//sum(case when a.subcon_date = $txt_date_from then b.quantity else 0 end) as today_rec_qty,
    //sum(case when a.subcon_date < $txt_date_from then b.quantity else 0 end) as prev_rec_qty,
	/*$sql_receive_materials="SELECT a.embl_job_no,c.body_part,c.id as po_id,b.job_break_id,a.entry_form,sum(b.quantity) as total_rec_qty
    from  sub_material_mst a, subcon_ord_dtls c, sub_material_dtls b  
	where a.id=b.mst_id and c.id=b.job_dtls_id $sql_con and a.entry_form in( 205 ) and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 
    group by a.embl_job_no,c.body_part,c.id,b.job_break_id,a.entry_form";*/

   $sql_receive_materials="SELECT a.embl_job_no,c.body_part,c.id as po_id,b.job_break_id,a.entry_form,b.quantity as total_rec_qty,b.id as dtls_id
    from  sub_material_mst a, sub_material_dtls b  , subcon_ord_dtls c,GBL_TEMP_ENGINE tmp
	where a.id=b.mst_id and b.job_dtls_id=c.id  and c.id=tmp.ref_val and tmp.entry_form=49  and tmp.user_id=$user_id and tmp.ref_from=1  and a.entry_form in( 205 ) and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";
	$sql_receive_materials_result=sql_select($sql_receive_materials);
	$materialReceiveDataArr=array();
	//$materialIssueDataArr=array();
	foreach($sql_receive_materials_result as $row)
	{
		$key=$row[csf('embl_job_no')].'_'.$row[csf('po_id')].'_'.$row[csf('body_part')].'_'.$row[csf('job_break_id')];
		$materialReceiveDataArr['total'][$key]+=$row[csf('total_rec_qty')];
		$materialReceiveDataArr['dtls_id'][$key] .=$row[csf('dtls_id')].',';
	}

	$sql_receive_bundle="SELECT a.po_id, a.item_rcv_dtls_id, b.item_rcv_id,b.id as dtls_id, b.bundle_qty, c.body_part, d.job_break_id,c.job_no_mst  from  prnting_bundle_mst a, prnting_bundle_dtls b ,subcon_ord_dtls c, sub_material_dtls d ,GBL_TEMP_ENGINE tmp 
   	where a.id=b.mst_id and a.po_id =c.id and a.item_rcv_dtls_id=d.id and b.item_rcv_id=d.mst_id and c.id=tmp.ref_val and tmp.entry_form=49  and tmp.user_id=$user_id and tmp.ref_from=1 and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1";
	$sql_receive_bundle_result=sql_select($sql_receive_bundle);
	$materialReceiveBndlDataArr=array();
	foreach($sql_receive_bundle_result as $row)
	{
		$key=$row[csf('job_no_mst')].'_'.$row[csf('po_id')].'_'.$row[csf('body_part')].'_'.$row[csf('job_break_id')];
		$materialReceiveBndlDataArr['total_bundle_qty'][$key] +=$row[csf('bundle_qty')];
		$materialReceiveBndlDataArr['item_rcv_id'][$key] .=$row[csf('item_rcv_id')].',';
	}
	/*echo '<pre>';
	print_r($materialReceiveDataArr);*/

	$sql_issue_materials="SELECT b.job_no_mst,b.body_part,d.entry_form,d.wo_dtls_id,d.wo_break_id,d.quantity,d.reject_qty,e.id  from subcon_ord_breakdown a, subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e,GBL_TEMP_ENGINE tmp where e.id=d.mst_id and a.id=d.wo_break_id and a.mst_id=b.id and b.id=d.wo_dtls_id and a.job_no_mst=c.embellishment_job and d.wo_id=c.id and b.mst_id=c.id and b.id=tmp.ref_val and tmp.entry_form=49  and tmp.user_id=$user_id and tmp.ref_from=1 and a.status_active =1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0 ";
	$sql_issue_result=sql_select($sql_issue_materials);
	$materialIssueDataArr=array();
	foreach($sql_issue_result as $row)
	{
		$key=$row[csf('job_no_mst')].'_'.$row[csf('wo_dtls_id')].'_'.$row[csf('body_part')].'_'.$row[csf('wo_break_id')];
		if($row[csf('entry_form')]==495){
			$materialIssueDataArr['total_issue_qty'][$key] +=$row[csf('quantity')];
			$materialIssueDataArr['all_issue_id'][$key] .=$row[csf('id')].',';
		} else if ($row[csf('entry_form')]==497){
			$materialIssueDataArr['total_production_qty'][$key] +=$row[csf('quantity')];
			$materialIssueDataArr['all_production_id'][$key] .=$row[csf('id')].',';
		} else if ($row[csf('entry_form')]==498){
			$materialIssueDataArr['total_qc_qty'][$key] +=$row[csf('quantity')];
			$materialIssueDataArr['total_reject_qty'][$key] +=$row[csf('reject_qty')];
			$materialIssueDataArr['all_qc_id'][$key] .=$row[csf('id')].',';
		} else if ($row[csf('entry_form')]==499){
			$materialIssueDataArr['total_delivery_qty'][$key] +=$row[csf('quantity')];
			$materialIssueDataArr['all_delivery_id'][$key] .=$row[csf('id')].',';
		}
		
	}
	
	//Delivery................................................
	$p=1;
	$po_chunk_arr=array_chunk($embJobArr,999);
	foreach($po_chunk_arr as $po_arr)
	{
		if($p==1){$sql_con =" and (a.job_no in('".implode("','",$po_arr)."')";} 
		else{$sql_con .=" or a.job_no in('".implode("','",$po_arr)."')";}
		$p++;
	}
	$sql_con .=")";
	$sql_delivery="SELECT a.job_no,c.body_part,c.id as po_id,b.color_size_id,
	sum(b.delivery_qty) as delivery_qty	from subcon_delivery_mst a,subcon_delivery_dtls b,subcon_ord_dtls c ,GBL_TEMP_ENGINE tmp
	where a.id=b.mst_id and c.id=b.order_id and c.id=tmp.ref_val and tmp.entry_form=49  and tmp.user_id=$user_id and tmp.ref_from=1 and a.entry_form=254 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 
	group by a.job_no,c.body_part ,c.id,b.color_size_id";
	$sql_delivery_result=sql_select($sql_delivery);
	$deliveryDataArr=array();
	foreach($sql_delivery_result as $row)
	{
		$key=$row[csf('job_no')].$row[csf('po_id')].$row[csf('body_part')].$row[csf('color_size_id')];
		/*$deliveryDataArr['today'][$key]=$row[csf('today_delivery_qty')];
		$deliveryDataArr['prev'][$key]=$row[csf('prev_delivery_qty')];*/
		$deliveryDataArr['total'][$key]=$row[csf('delivery_qty')];
	}
	
	//Bill................................................
	$p=1;
	$po_chunk_arr=array_chunk($embJobArr,999);
	foreach($po_chunk_arr as $po_arr)
	{
		if($p==1){$sql_con =" and (c.job_no_mst in('".implode("','",$po_arr)."')";} 
		else{$sql_con .=" or c.job_no_mst in('".implode("','",$po_arr)."')";}
		$p++;
	}
	$sql_con .=")";

	$sql_bill="SELECT c.job_no_mst as job_no,c.body_part,c.id as po_id,b.color_size_id,
	sum(b.delivery_qty) as total_bill_qty, sum(b.amount) as total_bill_amount,a.id
	from subcon_inbound_bill_mst a,subcon_inbound_bill_dtls b,subcon_ord_dtls c ,GBL_TEMP_ENGINE tmp
	where a.id=b.mst_id and c.id=b.order_id and c.id=tmp.ref_val and tmp.entry_form=49  and tmp.user_id=$user_id and tmp.ref_from=1 and b.process_id=13 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 
	group by c.job_no_mst,c.body_part ,c.id,b.color_size_id,a.id";
	$sql_bill_result=sql_select($sql_bill);
	$billDataArr=array();
	foreach($sql_bill_result as $row)
	{
		$key=$row[csf('job_no')].'_'.$row[csf('po_id')].'_'.$row[csf('body_part')].'_'.$row[csf('color_size_id')];
		//$key=$row[csf('job_no')].$row[csf('po_id')].$row[csf('body_part')].$row[csf('color_size_id')];
		//$billDataArr['today'][$key]=$row[csf('today_bill_qty')];
		//$billDataArr['prev'][$key]=$row[csf('prev_bill_qty')];
		$billDataArr['total_qty'][$key]+=$row[csf('total_bill_qty')];
		$billDataArr['total_amount'][$key]+=$row[csf('total_bill_amount')];
		$billDataArr['bill_id'][$key].=$row[csf('id')].',';
	}
	/*echo '<pre>';
	print_r($billDataArr);*/
	$job_cond = where_con_using_array($embJobArr,1,"d.job_no");
	$dyes_issue_sql="SELECT a.issue_number, a.id as issue_id, b.product_id, b.req_qny_edit as req_qny_edit,b.item_category,(c.cons_amount) as dyes_chemical_cost,d.color_id,d.job_no
	from inv_issue_master a, dyes_chem_issue_dtls b, inv_transaction c ,pro_recipe_entry_mst d
	where a.id=b.mst_id and b.trans_id=c.id and a.entry_form=250 and d.entry_form=220 and a.issue_basis=7 and to_char(d.id)= b.recipe_id and b.item_category in (5,6,7,22) and c.transaction_type=2 and a.COMPANY_ID=$cbo_company_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $job_cond";
	$dyes_issue_sql_result=sql_select($dyes_issue_sql);
	
	$dyes_chemical_arr=array();
	foreach($dyes_issue_sql_result as $val)
	{
		/*$dyes_chemical_arr[$val[csf("job_no")]][$val[csf("color_id")]][$val[csf("item_category")]]['chemical_cost']+=$val[csf("dyes_chemical_cost")];
		$dyes_chemical_arr[$val[csf("job_no")]][$val[csf("color_id")]][$val[csf("item_category")]]['dyes_issue_qty']+=$val[csf("req_qny_edit")];*/
		$dyes_chemical_arr[$val[csf("job_no")]][$val[csf("color_id")]]['chemical_cost']+=$val[csf("dyes_chemical_cost")];
		$dyes_chemical_arr[$val[csf("job_no")]][$val[csf("color_id")]]['chemical_issue_qty']+=$val[csf("req_qny_edit")];
		$dyes_chemical_arr[$val[csf("job_no")]][$val[csf("color_id")]]['issue_id'] .=$val[csf("issue_id")].',';
	}

	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=1 and ENTRY_FORM=49");
	oci_commit($con);
	disconnect($con);

	ob_start();
	?>
	<style type="text/css">
		.wrd_brk{word-break: break-all;}
	</style>
    <div style="width:2400px"> 
        <table width="100%" cellspacing="0" >
            <tr style="border:none;">
                <td colspan="28" align="center" style="border:none; font-size:14px;">
                    <b><? echo $company_library[str_replace("'","",$cbo_company_id)]; ?></b>
                </td>
            </tr>
        </table>
        <div style="float:left; width:2300px">
            <table width="2400" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
                <thead>
                    <tr><!-- SL	Job No	Work Order	Buyer Name	Buyer PO	Style	Body Color	Body Part	Order Qty (Pcs)	Delivery Date	Total Receive	Recv Balance	Total Barcode	Barcode Balance	Total Issue	Issue Balance	Total Production	Balance	Total QC	QC Balance	Total Reject	Total Delivery	Delivery Balance	Dyes/Chemical issue	Ttl Chem + Dyes Cost (Tk)	Bill Qty	Balance Bill Qty -->

                        <th width="35">SL</th>
                        <th width="120">Emb. Job</th>
                        <th width="100">Work Order</th>
                        <th width="130">Party Name</th>
                        <th width="100">Buyer Name</th>
                        <th width="80">Buyer PO</th>
                        <th width="100">Style</th>
                        <th width="100">Body Color</th>
                        <th width="100">Body Part</th>
                        <th width="80">Order Qty (Pcs)</th>
                        <th width="80">Delivery Date</th>
                        
                        <th width="80">Total Receive</th>
                        <th width="80">Recv Balance</th>
                        <th width="80">Total Barcode</th>
                        <th width="80">Barcode Balance</th>
                        <th width="80">Total Issue</th>
                        <th width="80">Issue Balance</th>
                        
                        <th width="80">Total Production</th>
                        <th width="80">Balance</th>
                        <th width="80">Total QC</th>
                        <th width="80">Total Reject</th>
                        <th width="80">QC Balance</th>
                        
                        
                        <th width="80">Total Delivery</th>
                        <th width="80">Delivery Balance</th>
                        <th width="80">Dyes/Chemical issue</th>
                        <th width="80">Dyes/Chemical issue Ttl Chem + Dyes Cost (Tk)</th>
                        <th width="80">Bill Qty</th>
                        <th>Balance Bill Qty</th>
                    </tr>
                </thead>
            </table>
            <div style="max-height:350px; width:2400px; overflow-y:auto;" id="scroll_body" >
                <table cellspacing="0" border="1" class="rpt_table"  width="2380" rules="all" id="table_body" >
                <tbody>
				<?  
				$i=1;
				//$buyerPoDataArr[$row[csf('jobid')]][$row[csf('job_no')]][$row[csf('order_no')]][$row[csf('party_id')]][$row[csf('buyer_po_no')]][$row[csf('buyer_style_ref')]][$row[csf('color_id')]][$row[csf('body_part')]]	
				$grand_order_qty=$grand_totalMaterRec=$grand_rcvBal=$grand_total_bundle_qty=$grand_bndlBal=$grand_totalMaterIssue=$grand_issueBal=$grand_totalMaterProduction=$grand_productionBal=$grand_totalMaterQc=$grand_qcBal=$grand_totalMaterReject=$grand_totalMaterDelivery=$grand_delBal=$grand_totalMaterDelivery=$grand_billBal=$grand_days_issue=$grand_days_issue_cost=0;
				foreach($buyerPoDataArr as $within_group=>$within_groupArr)
				{
					foreach($within_groupArr as $party_id=>$partyArr)
					{
						$party_order_qty=$party_totalMaterRec=$party_rcvBal=$party_total_bundle_qty=$party_bndlBal=$party_totalMaterIssue=$party_issueBal=$party_totalMaterProduction=$party_productionBal=$party_totalMaterQc=$party_qcBal=$party_totalMaterReject=$party_totalMaterDelivery=$party_delBal=$party_totalMaterBill=$party_billBal=$party_days_issue=$party_days_issue_cost=0;
						foreach($partyArr as $buyer_buyer=>$buyer_buyerArr)
						{
							foreach($buyer_buyerArr as $jobid=>$jobArr)
							{
								$wo_order_qty=$wo_totalMaterRec=$wo_rcvBal=$wo_total_bundle_qty=$wo_bndlBal=$wo_totalMaterIssue=$wo_issueBal=$wo_totalMaterProduction=$wo_productionBal=$wo_totalMaterQc=$wo_qcBal=$wo_totalMaterReject=$wo_totalMaterDelivery=$wo_delBal=$wo_totalMaterBill=$wo_billBal=$wo_days_issue=$wo_days_issue_cost=0;
								foreach($jobArr as $job_no=>$jobNoArr)
								{
									foreach($jobNoArr as $order_no=>$orderNoArr)
									{	
										foreach($orderNoArr as $buyer_po=>$buyerPoArr)
										{
											foreach($buyerPoArr as $buyer_style_ref=>$buyerStyleArr)
											{
												foreach($buyerStyleArr as $color_id=>$colorArr)
												{
													foreach($colorArr as $body_part_id=>$row)
													{
														$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
														$totalMaterRec=$total_bundle_qty=$totalMaterIssue=$totalMaterProduction=$totalMaterQc=$totalMaterReject=$totalMaterDelivery=$rcvBal=$bndlBal=$issueBal=$productionBal=$qcBal=$delBal=$billBal=$chemical_cost=$chemical_issue_qty=$totalMaterBill=0;
														$all_delivery_id=$all_production_id=$all_issue_id=$all_rcv_dtls_id=$all_bndl_rcv_id=$all_qc_id=$all_bill_id='';
														$color_size_ids=$row['color_size_id'];
														$all_color_size_arr=array_unique(explode(",",(chop($color_size_ids,','))));
														foreach($all_color_size_arr as $key=>$row_val)
														{
															$key=$job_no.'_'.$row['po_id'].'_'.$body_part_id.'_'.$row_val;
															$totalMaterRec+=$materialReceiveDataArr['total'][$key];
															//echo $key.'=='.$totalMaterRec.'#<br>'; 
															$total_bundle_qty+=$materialReceiveBndlDataArr['total_bundle_qty'][$key];
															$totalMaterIssue+=$materialIssueDataArr['total_issue_qty'][$key];
															$totalMaterProduction+=$materialIssueDataArr['total_production_qty'][$key];
															$totalMaterQc+=$materialIssueDataArr['total_qc_qty'][$key];
															$totalMaterReject+=$materialIssueDataArr['total_reject_qty'][$key];
															$totalMaterDelivery+=$materialIssueDataArr['total_delivery_qty'][$key];
															$totalMaterBill+=$billDataArr['total_qty'][$key];

															$all_rcv_dtls_id.=chop($materialReceiveDataArr['dtls_id'][$key],',').',';
															$all_bndl_rcv_id.=chop($materialReceiveBndlDataArr['item_rcv_id'][$key],',').',';
															$all_issue_id.=chop($materialIssueDataArr['all_issue_id'][$key],',').',';
															$all_production_id.=chop($materialIssueDataArr['all_production_id'][$key],',').',';
															$all_delivery_id.=chop($materialIssueDataArr['all_delivery_id'][$key],',').',';
															$all_qc_id.=chop($materialIssueDataArr['all_qc_id'][$key],',').',';
															$all_bill_id.=chop($billDataArr['bill_id'][$key],',').',';
														}
														$rcvBal=$row['order_qty']-$totalMaterRec;
														$bndlBal=$totalMaterRec-$total_bundle_qty;
														$issueBal=$totalMaterRec-$totalMaterIssue;
														$productionBal=$totalMaterIssue-$totalMaterProduction;
														$qcBal=$totalMaterProduction-($totalMaterQc+$totalMaterReject);
														$delBal=$totalMaterQc-$totalMaterDelivery;
														$billBal=$totalMaterDelivery-$totalMaterBill;

														$all_delivery_id=implode(",",array_unique(explode(",",(chop($all_delivery_id,',')))));
														$all_production_id=implode(",",array_unique(explode(",",(chop($all_production_id,',')))));
														$all_issue_id=implode(",",array_unique(explode(",",(chop($all_issue_id,',')))));
														$all_rcv_dtls_id=implode(",",array_unique(explode(",",(chop($all_rcv_dtls_id,',')))));
														$all_bndl_dtls_id=implode(",",array_unique(explode(",",(chop($all_bndl_dtls_id,',')))));
														$all_bndl_rcv_id=implode(",",array_unique(explode(",",(chop($all_bndl_rcv_id,',')))));
														$all_qc_id=implode(",",array_unique(explode(",",(chop($all_qc_id,',')))));
														$all_chemical_issue_id=implode(",",array_unique(explode(",",(chop($dyes_chemical_arr[$job_no][$color_id]['issue_id'],',')))));
														$all_bill_id=implode(",",array_unique(explode(",",(chop($all_bill_id,',')))));

														$chemical_cost=$dyes_chemical_arr[$job_no][$color_id]['chemical_cost'];
														$chemical_issue_qty=$dyes_chemical_arr[$job_no][$color_id]['chemical_issue_qty'];

														$all_rcv_dtls_id = str_replace(",,",",",$all_rcv_dtls_id);
														?>
								                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								                            <td width="35" align="center"><? echo $i;?></td>
								                            <td width="120"><a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_no.'_'.$within_group.'_'.str_replace("'","",$txt_date_from) .'_'.str_replace("'","",$txt_date_to).'_'.$jobid ;?>',1,'Job Popup','emb_job_popup');"><p><? echo $job_no;?></p></a></td>
								                            <td width="100"><p><? echo $order_no;?></p></td>
								                            <td width="130"><p style="width:130px; word-break:break-all"><? echo $buyer_arr[$party_id]; ?></p></td>
								                            <td width="100"><p style="width:100px; word-break:break-all"><? echo $buyer_buyer; ?></p></td>
								                            <td width="80"><p style="width:65px; word-break:break-all"><? echo $buyer_po; ?></p></td>
								                            <td width="100"><p style="width:95px; word-break:break-all"><? echo $buyer_style_ref; ?></p></td>
								                            <td width="100"><p style="width:95px; word-break:break-all"><? echo $color_library_arr[$color_id]; ?></p></td>
								                            <td width="100"><p style="width:95px; word-break:break-all"><? echo $body_part[$body_part_id]; ?></p></td>
								                            <td width="80" align="right" title="<? echo $unit_of_measurement[$row[csf('order_uom')]]; ?>"><a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_no.'_'.$within_group.'_'.str_replace("'","",$txt_date_from) .'_'.str_replace("'","",$txt_date_to).'_'.$all_rcv_dtls_id.'_'.$color_id.'_'.$body_part_id.'_'.$all_bndl_rcv_id.'_'.$all_issue_id.'_'.$all_production_id.'_'.$all_qc_id.'_'.$all_delivery_id.'_'.$all_chemical_issue_id.'_'.$buyer_po ;?>',1,'Order Popup','order_popup');"><? echo number_format($row['order_qty'],2); ?></a></td>
								                            <td width="80" align="center"><? echo change_date_format($row['delivery_date']); ?></td>

								                            <td align="right" width="80"><a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_no.'_'.$within_group.'_'.str_replace("'","",$txt_date_from) .'_'.str_replace("'","",$txt_date_to).'_'.$all_rcv_dtls_id.'_'.$color_id.'_'.$body_part_id ;?>',1,'Receive Popup','receive_popup');"><? echo number_format($totalMaterRec,2); ?></a></td>
								                            <td align="right" width="80"><? echo number_format($rcvBal,2); ?></td>
								                            <td align="right" width="80"><a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_no.'_'.$within_group.'_'.str_replace("'","",$txt_date_from) .'_'.str_replace("'","",$txt_date_to).'_'.$all_bndl_rcv_id.'_'.$color_id.'_'.$body_part_id ;?>',1,'Barcode Popup','bndl_popup');"><? echo number_format($total_bundle_qty,2); ?></a></td>
								                            <td align="right" width="80"><a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_no.'_'.$within_group.'_'.str_replace("'","",$txt_date_from) .'_'.str_replace("'","",$txt_date_to).'_'.$all_rcv_dtls_id.'_'.$color_id.'_'.$body_part_id ;?>',1,'Barcode Balance Popup','bndl_bal_popup');"><? echo number_format($bndlBal,2); ?></a></td>
								                            <td align="right" width="80"><a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_no.'_'.$within_group.'_'.str_replace("'","",$txt_date_from) .'_'.str_replace("'","",$txt_date_to).'_'.$all_issue_id.'_'.$color_id.'_'.$body_part_id ;?>',1,'Issue Popup','issue_popup');"><? echo number_format($totalMaterIssue,2); ?></a></td>
								                            <td align="right" width="80"><? echo number_format($issueBal,2); ?></td>

								                            <td align="right" width="80"><a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_no.'_'.$within_group.'_'.str_replace("'","",$txt_date_from) .'_'.str_replace("'","",$txt_date_to).'_'.$all_production_id.'_'.$color_id.'_'.$body_part_id ;?>',1,'Production Popup','production_popup');"><? echo number_format($totalMaterProduction,2); ?></a></td>
								                            <td align="right" width="80"><a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_no.'_'.$within_group.'_'.str_replace("'","",$txt_date_from) .'_'.str_replace("'","",$txt_date_to).'_'.$all_production_id.'_'.$color_id.'_'.$body_part_id.'_'.$all_issue_id ;?>',1,'Production Balance Popup','production_bal_popup');"><? echo number_format($productionBal,2); ?></a></td>

								                            <td align="right" width="80"><a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_no.'_'.$within_group.'_'.str_replace("'","",$txt_date_from) .'_'.str_replace("'","",$txt_date_to).'_'.$all_qc_id.'_'.$color_id.'_'.$body_part_id ;?>',1,'QC Popup','qc_popup');"><? echo number_format($totalMaterQc,2); ?></a></td>
								                            <td align="right" width="80"><a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_no.'_'.$within_group.'_'.str_replace("'","",$txt_date_from) .'_'.str_replace("'","",$txt_date_to).'_'.$all_qc_id.'_'.$color_id.'_'.$body_part_id ;?>',1,'Reject Popup','reject_popup');"><? echo number_format($totalMaterReject,2); ?></a></td>
								                            <td align="right" width="80"><? echo number_format($qcBal,2); ?></td>
								                            

								                            <td align="right" width="80"><a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_no.'_'.$within_group.'_'.str_replace("'","",$txt_date_from) .'_'.str_replace("'","",$txt_date_to).'_'.$all_delivery_id.'_'.$color_id.'_'.$body_part_id ;?>',1,'Delivery Popup','delivery_popup');"><? echo number_format($totalMaterDelivery,2); ?></a></td>
								                            <td align="right" width="80"><? echo number_format($delBal,2); ?></td>

								                            <td align="right" width="80"><a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_no.'_'.$within_group.'_'.str_replace("'","",$txt_date_from) .'_'.str_replace("'","",$txt_date_to).'_'.$all_chemical_issue_id.'_'.$color_id.'_'.$body_part_id ;?>',1,'Chemical Popup','chemical_issue_popup');"><? echo number_format($chemical_issue_qty,2); ?></a></td>
								                            <td align="right" width="80"><a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_no.'_'.$within_group.'_'.str_replace("'","",$txt_date_from) .'_'.str_replace("'","",$txt_date_to).'_'.$all_chemical_issue_id.'_'.$color_id.'_'.$body_part_id ;?>',1,'Chemical Popup','chemical_issue_cost_popup');"><? echo number_format($chemical_cost,2); ?></a></td>
								                            <td align="right" width="80"><a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_no.'_'.$within_group.'_'.str_replace("'","",$txt_date_from) .'_'.str_replace("'","",$txt_date_to).'_'.$all_bill_id.'_'.$color_id.'_'.$body_part_id ;?>',1,'Bill Popup','bill_qty_pop_up');"><? echo number_format($totalMaterBill,2); ?></a></td>
								                            <td align="right"><? echo number_format($billBal,2); ?></td>
														</tr>
														<?
														$wo_order_qty+=$row['order_qty'];
														$wo_totalMaterRec+=$totalMaterRec;
														$wo_rcvBal+=$rcvBal;
														$wo_total_bundle_qty+=$total_bundle_qty;
														$wo_bndlBal+=$bndlBal;
														$wo_totalMaterIssue+=$totalMaterIssue;
														$wo_issueBal+=$issueBal;
														$wo_totalMaterProduction+=$totalMaterProduction;
														$wo_productionBal+=$productionBal;
														$wo_totalMaterQc+=$totalMaterQc;
														$wo_qcBal+=$qcBal;
														$wo_totalMaterReject+=$totalMaterReject;		
														$wo_totalMaterDelivery+=$totalMaterDelivery;
														$wo_delBal+=$delBal;
														$wo_totalMaterBill+=$totalMaterBill;		
														$wo_billBal+=$billBal;			
														$wo_days_issue+=$chemical_issue_qty;			
														$wo_days_issue_cost+=$chemical_cost;			
														$i++;
													}
												}
											}
										}
									}
								}
								?>		
			                    <tr bgcolor='#eaf3d9'> 
			                        <td colspan="9" align="right"><strong> Order Total:</strong></td>
			                        <td width="80" align="right" title="<? echo $unit_of_measurement[$row[csf('order_uom')]]; ?>"><? echo number_format($wo_order_qty,2); ?></td>
			                        <td width="80" align="center">&nbsp;</td>
			                        <td align="right" width="80"><strong><? echo number_format($wo_totalMaterRec,2); ?></strong></td>
			                        <td align="right" width="80"><strong><? echo number_format($wo_rcvBal,2); ?></strong></td>
			                        <td align="right" width="80"><strong><? echo number_format($wo_total_bundle_qty,2); ?></strong></td>
			                        <td align="right" width="80"><strong><? echo number_format($wo_bndlBal,2); ?></strong></td>
			                        <td align="right" width="80"><strong><? echo number_format($wo_totalMaterIssue,2); ?></strong></td>
			                        <td align="right" width="80"><strong><? echo number_format($wo_issueBal,2); ?></strong></td>

			                        <td align="right" width="80"><strong><? echo number_format($wo_totalMaterProduction,2); ?></strong></td>
			                        <td align="right" width="80"><strong><? echo number_format($wo_productionBal,2); ?></strong></td>

			                        <td align="right" width="80"><strong><? echo number_format($wo_totalMaterQc,2); ?></strong></td>
			                        <td align="right" width="80"><strong><? echo number_format($wo_totalMaterReject,2); ?></strong></td>
			                        <td align="right" width="80"><strong><? echo number_format($wo_qcBal,2); ?></strong></td>

			                        <td align="right" width="80"><strong><? echo number_format($wo_totalMaterDelivery,2); ?></strong></td>
			                        <td align="right" width="80"><strong><? echo number_format($wo_delBal,2); ?></strong></td>

			                       	<td align="right" width="80"><strong><? echo number_format($wo_days_issue,2); ?></strong></td>
		                        	<td align="right" width="80"><strong><? echo number_format($wo_days_issue_cost,2); ?></strong></td>
			                        <td align="right" width="80"><strong><? echo number_format($wo_totalMaterBill,2); ?></strong></td>
			                        <td align="right"><strong><? echo number_format($wo_billBal,2); ?></strong></td>
			                	</tr>
								<?
								$party_order_qty+=$wo_order_qty;
								$party_totalMaterRec+=$wo_totalMaterRec;
								$party_rcvBal+=$wo_rcvBal;
								$party_total_bundle_qty+=$wo_total_bundle_qty;
								$party_bndlBal+=$wo_bndlBal;
								$party_totalMaterIssue+=$wo_totalMaterIssue;
								$party_issueBal+=$wo_issueBal;
								$party_totalMaterProduction+=$wo_totalMaterProduction;
								$party_productionBal+=$wo_productionBal;
								$party_totalMaterQc+=$wo_totalMaterQc;
								$party_qcBal+=$wo_qcBal;
								$party_totalMaterReject+=$wo_totalMaterReject;
								$party_totalMaterDelivery+=$wo_totalMaterDelivery;
								$party_delBal+=$wo_delBal;
								$party_delBal+=$wo_delBal;
								$party_totalMaterBill+=$wo_totalMaterBill;
								$party_billBal+=$wo_billBal;
								$party_days_issue+=$wo_days_issue;
								$party_days_issue_cost+=$wo_days_issue_cost;
							}
						}
						?>
						<tr bgcolor='#e9dcdc'> 
	                        <td colspan="9" align="right"><strong> Party Total:</strong></td>
	                        <td width="80" align="right" title="<? echo $unit_of_measurement[$row[csf('order_uom')]]; ?>"><? echo number_format($party_order_qty,2); ?></td>
	                        <td width="80" align="center">&nbsp;</td>
	                        <td align="right" width="80"><strong><? echo number_format($party_totalMaterRec,2); ?></strong></td>
	                        <td align="right" width="80"><strong><? echo number_format($party_rcvBal,2); ?></strong></td>
	                        <td align="right" width="80"><strong><? echo number_format($party_total_bundle_qty,2); ?></strong></td>
	                        <td align="right" width="80"><strong><? echo number_format($party_bndlBal,2); ?></strong></td>
	                        <td align="right" width="80"><strong><? echo number_format($party_totalMaterIssue,2); ?></strong></td>
	                        <td align="right" width="80"><strong><? echo number_format($party_issueBal,2); ?></strong></td>

	                        <td align="right" width="80"><strong><? echo number_format($party_totalMaterProduction,2); ?></strong></td>
	                        <td align="right" width="80"><strong><? echo number_format($party_productionBal,2); ?></strong></td>

	                        <td align="right" width="80"><strong><? echo number_format($party_totalMaterQc,2); ?></strong></td>
	                        <td align="right" width="80"><strong><? echo number_format($party_totalMaterReject,2); ?></strong></td>
	                        <td align="right" width="80"><strong><? echo number_format($party_qcBal,2); ?></strong></td>
	                        

	                        <td align="right" width="80"><strong><? echo number_format($party_totalMaterDelivery,2); ?></strong></td>
	                        <td align="right" width="80"><strong><? echo number_format($party_delBal,2); ?></strong></td>

	                        <td align="right" width="80"><strong><? echo number_format($party_days_issue,2); ?></strong></td>
	                        <td align="right" width="80"><strong><? echo number_format($party_days_issue_cost,2); ?></strong></td>
	                        <td align="right" width="80"><strong><? echo number_format($party_totalMaterBill,2); ?></strong></td>
	                        <td align="right"><strong><? echo number_format($party_billBal,2); ?></strong></td>
	                	</tr>
						<?
						$grand_order_qty+=$party_order_qty;
						$grand_totalMaterRec+=$party_totalMaterRec;
						$grand_rcvBal+=$party_rcvBal;
						$grand_total_bundle_qty+=$party_total_bundle_qty;
						$grand_bndlBal+=$party_bndlBal;
						$grand_totalMaterIssue+=$party_totalMaterIssue;
						$grand_issueBal+=$party_issueBal;
						$grand_totalMaterProduction+=$party_totalMaterProduction;
						$grand_productionBal+=$party_productionBal;
						$grand_totalMaterQc+=$party_totalMaterQc;
						$grand_qcBal+=$party_qcBal;
						$grand_totalMaterReject+=$party_totalMaterReject;
						$grand_totalMaterDelivery+=$party_totalMaterDelivery;
						$grand_delBal+=$party_delBal;
						$grand_totalMaterBill+=$party_totalMaterBill;
						$grand_billBal+=$party_billBal;
						$grand_days_issue+=$party_days_issue;
						$grand_days_issue_cost+=$party_days_issue_cost;
					}
				}
				
                ?>
                </tbody>
                <tfoot>
                	
					<tr bgcolor='#bfffc7'> 
	                    <th colspan="9" align="right"><strong>Grand Total:</strong></th>
	                    <th width="80" align="right" title="<? echo $unit_of_measurement[$row[csf('order_uom')]]; ?>"><? echo number_format($grand_order_qty,2); ?></th>
	                    <th width="80" align="center">&nbsp;</th>
	                    <th align="right" width="80"><strong><? echo number_format($grand_totalMaterRec,2); ?></strong></th>
	                    <th align="right" width="80"><strong><? echo number_format($grand_rcvBal,2); ?></strong></th>
	                    <th align="right" width="80"><strong><? echo number_format($grand_total_bundle_qty,2); ?></strong></th>
	                    <th align="right" width="80"><strong><? echo number_format($grand_bndlBal,2); ?></strong></th>
	                    <th align="right" width="80"><strong><? echo number_format($grand_totalMaterIssue,2); ?></strong></th>
	                    <th align="right" width="80"><strong><? echo number_format($grand_issueBal,2); ?></strong></th>

	                    <th align="right" width="80"><strong><? echo number_format($grand_totalMaterProduction,2); ?></strong></th>
	                    <th align="right" width="80"><strong><? echo number_format($grand_productionBal,2); ?></strong></th>

	                    <th align="right" width="80"><strong><? echo number_format($grand_totalMaterQc,2); ?></strong></th>
	                    <th align="right" width="80"><strong><? echo number_format($grand_totalMaterReject,2); ?></strong></th>
	                    <th align="right" width="80"><strong><? echo number_format($grand_qcBal,2); ?></strong></th>
	                    

	                    <th align="right" width="80"><strong><? echo number_format($grand_totalMaterDelivery,2); ?></strong></th>
	                    <th align="right" width="80"><strong><? echo number_format($grand_delBal,2); ?></strong></th>
	                    <th align="right" width="80"><strong><? echo number_format($grand_days_issue,2); ?></strong></th>
	                    <th align="right" width="80"><strong><? echo number_format($grand_days_issue_cost,2); ?></strong></th>
	                    <th align="right" width="80"><strong><? echo number_format($grand_totalMaterBill,2); ?></strong></th>
	                    <th align="right"><strong><? echo number_format($grand_billBal,2); ?></th>
	            	</tr>
                </tfoot>
                </table>
             </div>
           </div>
        </div><!-- end main div -->
		<?
	$html = ob_get_contents();
	ob_clean();
	$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
	
	
	foreach (glob("*.xls") as $filename) {
		@unlink($filename);
	}
	
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,$html);
	echo "$html**$filename";
	exit();
}

if($action=="pending_report_generate")
{ 
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$job_no=str_replace("'","",$txt_job_no);
	$job_id=str_replace("'","",$txt_job_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	/*$job_data=explode("_",$job_no);
	$job_no_mst=$job_data[0];
	$job_id=$job_data[1];
	$job_data=explode("_",$job_no);*/
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$year_id=str_replace("'","",$cbo_year);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$cbo_within_group=str_replace("'","",$cbo_within_group);
	if($txt_date_from!="" || $txt_date_to!="")
    {
        if($db_type==0) $sys_cond .= " and b.delivery_date '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
        else if($db_type==2) $sys_cond .= " and b.delivery_date between '".change_date_format($txt_date_from,'yyyy-mm-dd','',-1)."' and '".change_date_format($txt_date_to,'yyyy-mm-dd','',-1)."'";
    } 

    if ($db_type == 0)
    {
    	$sys_cond .= " and YEAR(a.insert_date) = $cbo_year ";
    }
    else if ($db_type == 2)
    {
    	$sys_cond .= " and to_char(a.insert_date,'YYYY') = $cbo_year ";
    }
    $order_cond = '';
	if($cbo_within_group!=0) $sys_cond.=" and a.within_group=$cbo_within_group ";
	if($cbo_within_group!=0) $order_cond.=" and a.within_group=$cbo_within_group ";
	if($cbo_company_id!=0) $sys_cond.=" and a.company_id=$cbo_company_id ";
	if($cbo_company_id!=0) $order_cond.=" and a.company_id=$cbo_company_id ";
	if($job_id!='') $sys_cond.=" and a.id=$job_id ";
	//if ($job_no!="") $sys_cond.=" and a.job_no_prefix_num in ($job_no) ";
	if ($txt_style_ref!="") $sys_cond.=" and b.buyer_style_ref like '%$txt_style_ref%' ";
	if ($txt_order_no!="") $sys_cond.=" and a.order_no like '%$txt_order_no%' ";
	//if ($job_no!="") $sys_cond.=" and a.embellishment_job '%$job_no%' ";
	if($cbo_buyer_id!=0) $sys_cond.=" and a.party_id=$cbo_buyer_id ";
	if($cbo_buyer_id!=0) $order_cond.=" and a.party_id=$cbo_buyer_id ";
	$buyer_arr=return_library_array( "SELECT id,buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name"  ); 
	$company_library=return_library_array( "SELECT id,company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name"  );
	$color_library_arr=return_library_array( "SELECT id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

	if($db_type==0) $group_concat="group_concat(c.id) as color_size_id";
	else if($db_type==2) $group_concat="listagg(c.id,',') within group (order by c.id) as color_size_id";

	$sql_order_ids = sql_select("SELECT c.id as breakdown_id from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_inbound_bill_dtls d where a.id=b.mst_id and b.id=c.mst_id and c.id=d.color_size_id and a.entry_form=204 and d.entry_form=395 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond group by c.id");
	$order_id_array = array();
	foreach($sql_order_ids as $val)
	{
		$order_id_array[$val[csf('breakdown_id')]] = $val[csf('breakdown_id')];
	}

	$sql="SELECT  a.id as jobid,b.id as po_id,a.embellishment_job as job_no, a.party_id,  b.order_no,b.buyer_po_id, a.job_no_prefix_num as job_prefix,b.body_part ,c.color_id, a.delivery_date, $group_concat, sum(c.qnty) as order_qty, sum(b.wastage) as plan_qty,b.order_uom,b.buyer_po_no, b.buyer_style_ref,b.buyer_buyer, c.id as breakdown_id
	from subcon_ord_mst a, subcon_ord_breakdown c ,subcon_ord_dtls b
	where b.job_no_mst=a.embellishment_job and b.id=c.mst_id and c.job_no_mst=b.job_no_mst and a.entry_form=204 $sys_cond $sql_con $job_cond $wo_cond $year_cond $party_con $buyer_con $location_con $party_location_con $po_con $po_id_con $style_con and c.qnty<>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id,b.id,a.embellishment_job , a.party_id, b.order_no,b.buyer_po_id, a.job_no_prefix_num,b.body_part ,c.color_id, a.delivery_date,b.order_uom,b.buyer_po_no, b.buyer_style_ref,b.buyer_buyer,c.id";
	$sql_result=sql_select($sql);//
	foreach($sql_result as $row)
	{
		if($order_id_array[$row[csf('breakdown_id')]]=='')
		{
			if($row[csf('order_uom')]==1){
				$order_qty=$row[csf('order_qty')];
			}else if($row[csf('order_uom')]==2){ 
				$order_qty=$row[csf('order_qty')]*12;
			}

			$buyerPoDataArr[$row[csf('within_group')]][$row[csf('party_id')]][$row[csf('buyer_buyer')]][$row[csf('jobid')]][$row[csf('job_no')]][$row[csf('order_no')]][$row[csf('buyer_po_no')]][$row[csf('buyer_style_ref')]][$row[csf('color_id')]][$row[csf('body_part')]]['order_qty'] +=$order_qty;
			$buyerPoDataArr[$row[csf('within_group')]][$row[csf('party_id')]][$row[csf('buyer_buyer')]][$row[csf('jobid')]][$row[csf('job_no')]][$row[csf('order_no')]][$row[csf('buyer_po_no')]][$row[csf('buyer_style_ref')]][$row[csf('color_id')]][$row[csf('body_part')]]['po_id'] =$row[csf('po_id')];
			$buyerPoDataArr[$row[csf('within_group')]][$row[csf('party_id')]][$row[csf('buyer_buyer')]][$row[csf('jobid')]][$row[csf('job_no')]][$row[csf('order_no')]][$row[csf('buyer_po_no')]][$row[csf('buyer_style_ref')]][$row[csf('color_id')]][$row[csf('body_part')]]['delivery_date'] =$row[csf('delivery_date')];
			$buyerPoDataArr[$row[csf('within_group')]][$row[csf('party_id')]][$row[csf('buyer_buyer')]][$row[csf('jobid')]][$row[csf('job_no')]][$row[csf('order_no')]][$row[csf('buyer_po_no')]][$row[csf('buyer_style_ref')]][$row[csf('color_id')]][$row[csf('body_part')]]['color_size_id'] .=$row[csf('color_size_id')].',';
			$all_job_no.=$row[csf('job_no')].',';
			$all_po_id.=$row[csf('po_id')].',';
		}
	}

	$embJobArr=array_unique(explode(",",(chop($all_job_no,','))));
	$embPoIdArr=array_unique(explode(",",(chop($all_po_id,','))));
	// =============================================================
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2) and ENTRY_FORM=49");
	oci_commit($con);
			
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 49, 1, $embPoIdArr, $empty_arr);//PO ID
	// fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 49, 2, $cut_no_arr, $empty_arr);//CUT ID
	disconnect($con);

	/*echo '<pre>';
	print_r($buyerPoDataArr);*/
	$q=$s=1;
	$po_id_chunk_arr=array_chunk($embPoIdArr,999);
	foreach($po_id_chunk_arr as $po_arr)
	{
		if($q==1){$sql_po_con =" and (a.po_id in(".implode(",",$po_arr).")";} 
		else{$sql_po_con .=" or a.po_id in(".implode(",",$po_arr).")";}
		$q++;

		if($s==1){$sql_po_id_con =" and (b.id in(".implode(",",$po_arr).")";} 
		else{$sql_po_id_con .=" or b.id in(".implode(",",$po_arr).")";}
		$s++;
	}
	$sql_po_con .=")";
	$sql_po_id_con .=")";
		
	//Receive & Issue................................................
	$p=1;
	$po_chunk_arr=array_chunk($embJobArr,999);
	foreach($po_chunk_arr as $po_arr)
	{
		if($p==1){$sql_con =" and (a.embl_job_no in('".implode("','",$po_arr)."')";} 
		else{$sql_con .=" or a.embl_job_no in('".implode("','",$po_arr)."')";}
		$p++;
	}
	$sql_con .=")";
   $sql_receive_materials="SELECT a.embl_job_no,c.body_part,c.id as po_id,b.job_break_id,a.entry_form,b.quantity as total_rec_qty,b.id as dtls_id
    from  sub_material_mst a, subcon_ord_dtls c, sub_material_dtls b,GBL_TEMP_ENGINE tmp  
	where a.id=b.mst_id and c.id=b.job_dtls_id and c.id=tmp.ref_val and tmp.entry_form=49  and tmp.user_id=$user_id and tmp.ref_from=1 and a.entry_form in( 205 ) and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";
	$sql_receive_materials_result=sql_select($sql_receive_materials);
	$materialReceiveDataArr=array();
	//$materialIssueDataArr=array();
	foreach($sql_receive_materials_result as $row)
	{
		$key=$row[csf('embl_job_no')].'_'.$row[csf('po_id')].'_'.$row[csf('body_part')].'_'.$row[csf('job_break_id')];
		$materialReceiveDataArr['total'][$key]+=$row[csf('total_rec_qty')];
		$materialReceiveDataArr['dtls_id'][$key] .=$row[csf('dtls_id')].',';
	}

	$sql_receive_bundle="SELECT a.po_id, a.item_rcv_dtls_id, b.item_rcv_id,b.id as dtls_id, b.bundle_qty, c.body_part, d.job_break_id,c.job_no_mst  from  prnting_bundle_mst a, prnting_bundle_dtls b ,subcon_ord_dtls c, sub_material_dtls d ,GBL_TEMP_ENGINE tmp  
   	where a.id=b.mst_id and a.po_id =c.id and a.item_rcv_dtls_id=d.id and b.item_rcv_id=d.mst_id and c.id=tmp.ref_val and tmp.entry_form=49  and tmp.user_id=$user_id and tmp.ref_from=1 and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1";
	$sql_receive_bundle_result=sql_select($sql_receive_bundle);
	$materialReceiveBndlDataArr=array();
	foreach($sql_receive_bundle_result as $row)
	{
		$key=$row[csf('job_no_mst')].'_'.$row[csf('po_id')].'_'.$row[csf('body_part')].'_'.$row[csf('job_break_id')];
		$materialReceiveBndlDataArr['total_bundle_qty'][$key] +=$row[csf('bundle_qty')];
		$materialReceiveBndlDataArr['item_rcv_id'][$key] .=$row[csf('item_rcv_id')].',';
	}
	/*echo '<pre>';
	print_r($materialReceiveDataArr);*/

	$sql_issue_materials="SELECT b.job_no_mst,b.body_part,d.entry_form,d.wo_dtls_id,d.wo_break_id,d.quantity,d.reject_qty,e.id  from subcon_ord_breakdown a, subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e,GBL_TEMP_ENGINE tmp  where e.id=d.mst_id and a.id=d.wo_break_id and a.mst_id=b.id and b.id=d.wo_dtls_id and a.job_no_mst=c.embellishment_job and d.wo_id=c.id and b.mst_id=c.id  and b.id=tmp.ref_val and tmp.entry_form=49  and tmp.user_id=$user_id and tmp.ref_from=1 and a.status_active =1 and a.is_deleted =0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0  ";
	$sql_issue_result=sql_select($sql_issue_materials);
	$materialIssueDataArr=array();
	foreach($sql_issue_result as $row)
	{
		$key=$row[csf('job_no_mst')].'_'.$row[csf('wo_dtls_id')].'_'.$row[csf('body_part')].'_'.$row[csf('wo_break_id')];
		if($row[csf('entry_form')]==495){
			$materialIssueDataArr['total_issue_qty'][$key] +=$row[csf('quantity')];
			$materialIssueDataArr['all_issue_id'][$key] .=$row[csf('id')].',';
		} else if ($row[csf('entry_form')]==497){
			$materialIssueDataArr['total_production_qty'][$key] +=$row[csf('quantity')];
			$materialIssueDataArr['all_production_id'][$key] .=$row[csf('id')].',';
		} else if ($row[csf('entry_form')]==498){
			$materialIssueDataArr['total_qc_qty'][$key] +=$row[csf('quantity')];
			$materialIssueDataArr['total_reject_qty'][$key] +=$row[csf('reject_qty')];
			$materialIssueDataArr['all_qc_id'][$key] .=$row[csf('id')].',';
		} else if ($row[csf('entry_form')]==499){
			$materialIssueDataArr['total_delivery_qty'][$key] +=$row[csf('quantity')];
			$materialIssueDataArr['all_delivery_id'][$key] .=$row[csf('id')].',';
		}
		
	}
	
	//Delivery................................................
	$p=1;
	$po_chunk_arr=array_chunk($embJobArr,999);
	foreach($po_chunk_arr as $po_arr)
	{
		if($p==1){$sql_con =" and (a.job_no in('".implode("','",$po_arr)."')";} 
		else{$sql_con .=" or a.job_no in('".implode("','",$po_arr)."')";}
		$p++;
	}
	$sql_con .=")";
	$sql_delivery="SELECT a.job_no,c.body_part,c.id as po_id,b.color_size_id,
	sum(b.delivery_qty) as delivery_qty	from subcon_delivery_mst a,subcon_delivery_dtls b,subcon_ord_dtls c,GBL_TEMP_ENGINE tmp  
	where a.id=b.mst_id and c.id=b.order_id and c.id=tmp.ref_val and tmp.entry_form=49 and tmp.user_id=$user_id and tmp.ref_from=1 and a.entry_form=254 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 
	group by a.job_no,c.body_part ,c.id,b.color_size_id";
	$sql_delivery_result=sql_select($sql_delivery);
	$deliveryDataArr=array();
	foreach($sql_delivery_result as $row)
	{
		$key=$row[csf('job_no')].$row[csf('po_id')].$row[csf('body_part')].$row[csf('color_size_id')];
		/*$deliveryDataArr['today'][$key]=$row[csf('today_delivery_qty')];
		$deliveryDataArr['prev'][$key]=$row[csf('prev_delivery_qty')];*/
		$deliveryDataArr['total'][$key]=$row[csf('delivery_qty')];
	}
	
	//Bill................................................
	$p=1;
	$po_chunk_arr=array_chunk($embJobArr,999);
	foreach($po_chunk_arr as $po_arr)
	{
		if($p==1){$sql_con =" and (c.job_no_mst in('".implode("','",$po_arr)."')";} 
		else{$sql_con .=" or c.job_no_mst in('".implode("','",$po_arr)."')";}
		$p++;
	}
	$sql_con .=")";

	$sql_bill="SELECT c.job_no_mst as job_no,c.body_part,c.id as po_id,b.color_size_id,
	sum(b.delivery_qty) as total_bill_qty, sum(b.amount) as total_bill_amount,a.id
	from subcon_inbound_bill_mst a,subcon_inbound_bill_dtls b,subcon_ord_dtls c ,GBL_TEMP_ENGINE tmp 
	where a.id=b.mst_id and c.id=b.order_id and c.id=tmp.ref_val and tmp.entry_form=49  and tmp.user_id=$user_id and tmp.ref_from=1 and b.process_id=13 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 
	group by c.job_no_mst,c.body_part ,c.id,b.color_size_id,a.id";
	$sql_bill_result=sql_select($sql_bill);
	$billDataArr=array();
	foreach($sql_bill_result as $row)
	{
		$key=$row[csf('job_no')].'_'.$row[csf('po_id')].'_'.$row[csf('body_part')].'_'.$row[csf('color_size_id')];
		//$key=$row[csf('job_no')].$row[csf('po_id')].$row[csf('body_part')].$row[csf('color_size_id')];
		//$billDataArr['today'][$key]=$row[csf('today_bill_qty')];
		//$billDataArr['prev'][$key]=$row[csf('prev_bill_qty')];
		$billDataArr['total_qty'][$key]+=$row[csf('total_bill_qty')];
		$billDataArr['total_amount'][$key]+=$row[csf('total_bill_amount')];
		$billDataArr['bill_id'][$key].=$row[csf('id')].',';
	}
	/*echo '<pre>';
	print_r($billDataArr);*/
	$job_cond = where_con_using_array($embJobArr,1,"d.job_no");
	$dyes_issue_sql="SELECT a.issue_number, a.id as issue_id, b.product_id, b.req_qny_edit as req_qny_edit,b.item_category,(c.cons_amount) as dyes_chemical_cost,d.color_id,d.job_no
	from inv_issue_master a, dyes_chem_issue_dtls b, inv_transaction c ,pro_recipe_entry_mst d
	where a.id=b.mst_id and b.trans_id=c.id and a.entry_form=250 and d.entry_form=220 and a.issue_basis=7 and to_char(d.id)= b.recipe_id and b.item_category in (5,6,7) and a.buyer_job_no=d.job_no $job_cond and c.transaction_type=2 and a.COMPANY_ID=$cbo_company_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0";
	$dyes_issue_sql_result=sql_select($dyes_issue_sql);
	
	$dyes_chemical_arr=array();
	foreach($dyes_issue_sql_result as $val)
	{
		/*$dyes_chemical_arr[$val[csf("job_no")]][$val[csf("color_id")]][$val[csf("item_category")]]['chemical_cost']+=$val[csf("dyes_chemical_cost")];
		$dyes_chemical_arr[$val[csf("job_no")]][$val[csf("color_id")]][$val[csf("item_category")]]['dyes_issue_qty']+=$val[csf("req_qny_edit")];*/
		$dyes_chemical_arr[$val[csf("job_no")]][$val[csf("color_id")]]['chemical_cost']+=$val[csf("dyes_chemical_cost")];
		$dyes_chemical_arr[$val[csf("job_no")]][$val[csf("color_id")]]['chemical_issue_qty']+=$val[csf("req_qny_edit")];
		$dyes_chemical_arr[$val[csf("job_no")]][$val[csf("color_id")]]['issue_id'] .=$val[csf("issue_id")].',';
	}
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=1 and ENTRY_FORM=49");
	oci_commit($con);
	disconnect($con);

	ob_start();
	?>
	<style type="text/css">
		.wrd_brk{word-break: break-all;}
	</style>
    <div style="width:2400px"> 
        <table width="100%" cellspacing="0" >
            <tr style="border:none;">
                <td colspan="28" align="center" style="border:none; font-size:14px;">
                    <b><? echo $company_library[str_replace("'","",$cbo_company_id)]; ?></b>
                </td>
            </tr>
        </table>
        <div style="float:left; width:2400px">
            <table width="2380" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
                <thead>
                    <tr><!-- SL	Job No	Work Order	Buyer Name	Buyer PO	Style	Body Color	Body Part	Order Qty (Pcs)	Delivery Date	Total Receive	Recv Balance	Total Barcode	Barcode Balance	Total Issue	Issue Balance	Total Production	Balance	Total QC	QC Balance	Total Reject	Total Delivery	Delivery Balance	Dyes/Chemical issue	Ttl Chem + Dyes Cost (Tk)	Bill Qty	Balance Bill Qty -->

                        <th width="35">SL</th>
                        <th width="120">Emb. Job</th>
                        <th width="100">Work Order</th>
                        <th width="130">Party Name</th>
                        <th width="100">Buyer Name</th>
                        <th width="80">Buyer PO</th>
                        <th width="100">Style</th>
                        <th width="100">Body Color</th>
                        <th width="100">Body Part</th>
                        <th width="80">Order Qty (Pcs)</th>
                        <th width="80">Delivery Date</th>
                        <th width="80">Total Receive</th>
                        <th width="80">Recv %</th>
                        <th width="80">Recv Balance</th>
                        <th width="80">Total Barcode</th>
                        <th width="80">Barcode Balance</th>                        
                        <th width="80">Total Production</th>
                        <th width="80">Balance</th>
                        <th width="80">Total QC</th>
                        <th width="80">Total Reject</th>
                        <th width="80">QC Balance</th>
                        <th width="80">Total Delivery</th>
                        <th width="80">Delivery Balance</th>
                        <th width="80">Total Balance</th>
                    </tr>
                </thead>
            </table>
            <div style="max-height:350px; width:2400px; overflow-y:auto;" id="scroll_body" >
                <table cellspacing="0" border="1" class="rpt_table"  width="2380" rules="all" id="table_body" >
                <tbody>
				<?  
				$i=1;
				$grand_order_qty=$grand_totalMaterRec=$grand_rcvBal=$grand_total_bundle_qty=$grand_bndlBal=$grand_totalMaterIssue=$grand_issueBal=$grand_totalMaterProduction=$grand_productionBal=$grand_totalMaterQc=$grand_qcBal=$grand_totalMaterReject=$grand_delBal=$grand_totalMaterDelivery=$grand_billBal=$grand_days_issue=$grand_days_issue_cost=$grand_total_balance=0;
				foreach($buyerPoDataArr as $within_group=>$within_groupArr)
				{
					foreach($within_groupArr as $party_id=>$partyArr)
					{
						$party_order_qty=$party_totalMaterRec=$party_rcvBal=$party_total_bundle_qty=$party_bndlBal=$party_totalMaterIssue=$party_issueBal=$party_totalMaterProduction=$party_productionBal=$party_totalMaterQc=$party_qcBal=$party_totalMaterReject=$party_totalMaterDelivery=$party_delBal=$party_totalMaterBill=$party_billBal=$party_days_issue=$party_days_issue_cost=$party_total_balance=0;
						foreach($partyArr as $buyer_buyer=>$buyer_buyerArr)
						{
							foreach($buyer_buyerArr as $jobid=>$jobArr)
							{
								$wo_order_qty=$wo_totalMaterRec=$wo_rcvBal=$wo_total_bundle_qty=$wo_bndlBal=$wo_totalMaterIssue=$wo_issueBal=$wo_totalMaterProduction=$wo_productionBal=$wo_totalMaterQc=$wo_qcBal=$wo_totalMaterReject=$wo_totalMaterDelivery=$wo_delBal=$wo_totalMaterBill=$wo_billBal=$wo_days_issue=$wo_days_issue_cost=$wo_total_balance=0;
								foreach($jobArr as $job_no=>$jobNoArr)
								{
									foreach($jobNoArr as $order_no=>$orderNoArr)
									{	
										foreach($orderNoArr as $buyer_po=>$buyerPoArr)
										{
											foreach($buyerPoArr as $buyer_style_ref=>$buyerStyleArr)
											{
												foreach($buyerStyleArr as $color_id=>$colorArr)
												{
													foreach($colorArr as $body_part_id=>$row)
													{
														$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
														$totalMaterRec=$total_bundle_qty=$totalMaterIssue=$totalMaterProduction=$totalMaterQc=$totalMaterReject=$totalMaterDelivery=$rcvBal=$bndlBal=$issueBal=$productionBal=$qcBal=$delBal=$billBal=$chemical_cost=$chemical_issue_qty=$totalMaterBill=$total_balance=0;
														$all_delivery_id=$all_production_id=$all_issue_id=$all_rcv_dtls_id=$all_bndl_rcv_id=$all_qc_id=$all_bill_id='';
														$color_size_ids=$row['color_size_id'];
														$all_color_size_arr=array_unique(explode(",",(chop($color_size_ids,','))));
														foreach($all_color_size_arr as $key=>$row_val)
														{
															$key=$job_no.'_'.$row['po_id'].'_'.$body_part_id.'_'.$row_val;
															$totalMaterRec+=$materialReceiveDataArr['total'][$key];
															//echo $key.'=='.$totalMaterRec.'#<br>'; 
															$total_bundle_qty+=$materialReceiveBndlDataArr['total_bundle_qty'][$key];
															$totalMaterIssue+=$materialIssueDataArr['total_issue_qty'][$key];
															$totalMaterProduction+=$materialIssueDataArr['total_production_qty'][$key];
															$totalMaterQc+=$materialIssueDataArr['total_qc_qty'][$key];
															$totalMaterReject+=$materialIssueDataArr['total_reject_qty'][$key];
															$totalMaterDelivery+=$materialIssueDataArr['total_delivery_qty'][$key];
															$totalMaterBill+=$billDataArr['total_qty'][$key];

															$all_rcv_dtls_id.=chop($materialReceiveDataArr['dtls_id'][$key],',').',';
															$all_bndl_rcv_id.=chop($materialReceiveBndlDataArr['item_rcv_id'][$key],',').',';
															$all_issue_id.=chop($materialIssueDataArr['all_issue_id'][$key],',').',';
															$all_production_id.=chop($materialIssueDataArr['all_production_id'][$key],',').',';
															$all_delivery_id.=chop($materialIssueDataArr['all_delivery_id'][$key],',').',';
															$all_qc_id.=chop($materialIssueDataArr['all_qc_id'][$key],',').',';
															$all_bill_id.=chop($billDataArr['bill_id'][$key],',').',';
														}
														$rcvBal=$row['order_qty']-$totalMaterRec;
														$bndlBal=$totalMaterRec-$total_bundle_qty;
														$issueBal=$totalMaterRec-$totalMaterIssue;
														$productionBal=$totalMaterIssue-$totalMaterProduction;
														$qcBal=$totalMaterProduction-($totalMaterQc+$totalMaterReject);
														$delBal=$totalMaterQc-$totalMaterDelivery;
														$billBal=$totalMaterDelivery-$totalMaterBill;

														$all_delivery_id=implode(",",array_unique(explode(",",(chop($all_delivery_id,',')))));
														$all_production_id=implode(",",array_unique(explode(",",(chop($all_production_id,',')))));
														$all_issue_id=implode(",",array_unique(explode(",",(chop($all_issue_id,',')))));
														$all_rcv_dtls_id=implode(",",array_unique(explode(",",(chop($all_rcv_dtls_id,',')))));
														$all_bndl_dtls_id=implode(",",array_unique(explode(",",(chop($all_bndl_dtls_id,',')))));
														$all_bndl_rcv_id=implode(",",array_unique(explode(",",(chop($all_bndl_rcv_id,',')))));
														$all_qc_id=implode(",",array_unique(explode(",",(chop($all_qc_id,',')))));
														$all_chemical_issue_id=implode(",",array_unique(explode(",",(chop($dyes_chemical_arr[$job_no][$color_id]['issue_id'],',')))));
														$all_bill_id=implode(",",array_unique(explode(",",(chop($all_bill_id,',')))));

														$chemical_cost=$dyes_chemical_arr[$job_no][$color_id]['chemical_cost'];
														$chemical_issue_qty=$dyes_chemical_arr[$job_no][$color_id]['chemical_issue_qty'];

														$total_balance = $totalMaterRec-$totalMaterReject-$totalMaterDelivery;
														?>
								                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								                            <td width="35" align="center"><? echo $i;?></td>
								                            <td width="120"><a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_no.'_'.$within_group.'_'.str_replace("'","",$txt_date_from) .'_'.str_replace("'","",$txt_date_to).'_'.$jobid ;?>',1,'Job Popup','emb_job_popup');"><p><? echo $job_no;?></p></a></td>
								                            <td width="100"><p><? echo $order_no;?></p></td>
								                            <td width="130"><p style="width:130px; word-break:break-all"><? echo $buyer_arr[$party_id]; ?></p></td>
								                            <td width="100"><p style="width:100px; word-break:break-all"><? echo $buyer_buyer; ?></p></td>
								                            <td width="80"><p style="width:65px; word-break:break-all"><? echo $buyer_po; ?></p></td>
								                            <td width="100"><p style="width:95px; word-break:break-all"><? echo $buyer_style_ref; ?></p></td>
								                            <td width="100"><p style="width:95px; word-break:break-all"><? echo $color_library_arr[$color_id]; ?></p></td>
								                            <td width="100"><p style="width:95px; word-break:break-all"><? echo $body_part[$body_part_id]; ?></p></td>
								                            <td width="80" align="right" title="<? echo $unit_of_measurement[$row[csf('order_uom')]]; ?>"><a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_no.'_'.$within_group.'_'.str_replace("'","",$txt_date_from) .'_'.str_replace("'","",$txt_date_to).'_'.$all_rcv_dtls_id.'_'.$color_id.'_'.$body_part_id.'_'.$all_bndl_rcv_id.'_'.$all_issue_id.'_'.$all_production_id.'_'.$all_qc_id.'_'.$all_delivery_id.'_'.$all_chemical_issue_id.'_'.$buyer_po ;?>',1,'Order Popup','order_popup');"><? echo number_format($row['order_qty'],2); ?></a></td>
								                            <td width="80" align="center"><? echo change_date_format($row['delivery_date']); ?></td>

								                            <td align="right" width="80"><a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_no.'_'.$within_group.'_'.str_replace("'","",$txt_date_from) .'_'.str_replace("'","",$txt_date_to).'_'.$all_rcv_dtls_id.'_'.$color_id.'_'.$body_part_id ;?>',1,'Receive Popup','receive_popup');"><? echo number_format($totalMaterRec,2); ?></a></td>
								                             <td align="center" width="80"><?php echo number_format(($totalMaterRec/$row['order_qty'])*100,2);?></td>
								                            <td align="right" width="80"><? echo number_format($rcvBal,2); ?></td>
								                            <td align="right" width="80"><a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_no.'_'.$within_group.'_'.str_replace("'","",$txt_date_from) .'_'.str_replace("'","",$txt_date_to).'_'.$all_bndl_rcv_id.'_'.$color_id.'_'.$body_part_id ;?>',1,'Barcode Popup','bndl_popup');"><? echo number_format($total_bundle_qty,2); ?></a></td>
								                            <td align="right" width="80"><a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_no.'_'.$within_group.'_'.str_replace("'","",$txt_date_from) .'_'.str_replace("'","",$txt_date_to).'_'.$all_rcv_dtls_id.'_'.$color_id.'_'.$body_part_id ;?>',1,'Barcode Balance Popup','bndl_bal_popup');"><? echo number_format($bndlBal,2); ?></a></td>
								                            <td align="right" width="80"><a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_no.'_'.$within_group.'_'.str_replace("'","",$txt_date_from) .'_'.str_replace("'","",$txt_date_to).'_'.$all_production_id.'_'.$color_id.'_'.$body_part_id ;?>',1,'Production Popup','production_popup');"><? echo number_format($totalMaterProduction,2); ?></a></td>
								                            <td align="right" width="80"><a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_no.'_'.$within_group.'_'.str_replace("'","",$txt_date_from) .'_'.str_replace("'","",$txt_date_to).'_'.$all_production_id.'_'.$color_id.'_'.$body_part_id.'_'.$all_issue_id ;?>',1,'Production Balance Popup','production_bal_popup');"><? echo number_format($productionBal,2); ?></a></td>
								                            <td align="right" width="80"><a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_no.'_'.$within_group.'_'.str_replace("'","",$txt_date_from) .'_'.str_replace("'","",$txt_date_to).'_'.$all_qc_id.'_'.$color_id.'_'.$body_part_id ;?>',1,'QC Popup','qc_popup');"><? echo number_format($totalMaterQc,2); ?></a></td>
								                            <td align="right" width="80"><a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_no.'_'.$within_group.'_'.str_replace("'","",$txt_date_from) .'_'.str_replace("'","",$txt_date_to).'_'.$all_qc_id.'_'.$color_id.'_'.$body_part_id ;?>',1,'Reject Popup','reject_popup');"><? echo number_format($totalMaterReject,2); ?></a></td>
								                            <td align="right" width="80"><? echo number_format($qcBal,2); ?></td>
								                            <td align="right" width="80"><a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_no.'_'.$within_group.'_'.str_replace("'","",$txt_date_from) .'_'.str_replace("'","",$txt_date_to).'_'.$all_delivery_id.'_'.$color_id.'_'.$body_part_id ;?>',1,'Delivery Popup','delivery_popup');"><? echo number_format($totalMaterDelivery,2); ?></a></td>
								                            <td align="right" width="80"><? echo number_format($delBal,2); ?></td>
								                            <td align="right" width="80"><?php echo number_format($total_balance,2);?></td>
														</tr>
														<?
														$wo_order_qty+=$row['order_qty'];
														$wo_totalMaterRec+=$totalMaterRec;
														$wo_rcvBal+=$rcvBal;
														$wo_total_bundle_qty+=$total_bundle_qty;
														$wo_bndlBal+=$bndlBal;
														$wo_totalMaterProduction+=$totalMaterProduction;
														$wo_productionBal+=$productionBal;
														$wo_totalMaterQc+=$totalMaterQc;
														$wo_qcBal+=$qcBal;
														$wo_totalMaterReject+=$totalMaterReject;		
														$wo_totalMaterDelivery+=$totalMaterDelivery;
														$wo_delBal+=$delBal;
														$wo_total_balance+=$total_balance;
														$i++;
													}
												}
											}
										}
									}
								}
								?>		
			                    <tr bgcolor='#eaf3d9'> 
			                        <td colspan="9" align="right"><strong> Order Total:</strong></td>
			                        <td width="80" align="right" title="<? echo $unit_of_measurement[$row[csf('order_uom')]]; ?>"><? echo number_format($wo_order_qty,2); ?></td>
			                        <td width="80" align="center">&nbsp;</td>
			                        <td align="right" width="80"><strong><? echo number_format($wo_totalMaterRec,2); ?></strong></td>
			                        <td align="right" width="80"><strong>&nbsp;</strong></td>
			                        <td align="right" width="80"><strong><? echo number_format($wo_rcvBal,2); ?></strong></td>			                      
			                        <td align="right" width="80"><strong><? echo number_format($wo_total_bundle_qty,2); ?></strong></td>
			                        <td align="right" width="80"><strong><? echo number_format($wo_bndlBal,2); ?></strong></td>
			                        <td align="right" width="80"><strong><? echo number_format($wo_totalMaterProduction,2); ?></strong></td>
			                        <td align="right" width="80"><strong><? echo number_format($wo_productionBal,2); ?></strong></td>
			                        <td align="right" width="80"><strong><? echo number_format($wo_totalMaterQc,2); ?></strong></td>
			                        <td align="right" width="80"><strong><? echo number_format($wo_totalMaterReject,2); ?></strong></td>
			                        <td align="right" width="80"><strong><? echo number_format($wo_qcBal,2); ?></strong></td>

			                        <td align="right" width="80"><strong><? echo number_format($wo_totalMaterDelivery,2); ?></strong></td>
			                        <td align="right" width="80"><strong><? echo number_format($wo_delBal,2); ?></strong></td>
			                        <td align="right" width="80"><strong><? echo number_format($wo_total_balance,2); ?></strong></td>
			                	</tr>
								<?
								$party_order_qty+=$wo_order_qty;
								$party_totalMaterRec+=$wo_totalMaterRec;
								$party_rcvBal+=$wo_rcvBal;
								$party_total_bundle_qty+=$wo_total_bundle_qty;
								$party_bndlBal+=$wo_bndlBal;
								$party_totalMaterProduction+=$wo_totalMaterProduction;
								$party_productionBal+=$wo_productionBal;
								$party_totalMaterQc+=$wo_totalMaterQc;
								$party_qcBal+=$wo_qcBal;
								$party_totalMaterReject+=$wo_totalMaterReject;
								$party_totalMaterDelivery+=$wo_totalMaterDelivery;
								$party_delBal+=$wo_delBal;
								$party_total_balance+=$wo_total_balance;
							}
						}
						?>
						<tr bgcolor='#e9dcdc'> 
	                        <td colspan="9" align="right"><strong> Party Total:</strong></td>
	                        <td width="80" align="right" title="<? echo $unit_of_measurement[$row[csf('order_uom')]]; ?>"><? echo number_format($party_order_qty,2); ?></td>
	                        <td width="80" align="center">&nbsp;</td>
	                        <td align="right" width="80"><strong><? echo number_format($party_totalMaterRec,2); ?></strong></td>
	                        <td align="right" width="80"><strong>&nbsp;</strong></td>
	                        <td align="right" width="80"><strong><? echo number_format($party_rcvBal,2); ?></strong></td>
	                        <td align="right" width="80"><strong><? echo number_format($party_total_bundle_qty,2); ?></strong></td>
	                        <td align="right" width="80"><strong><? echo number_format($party_bndlBal,2); ?></strong></td>
	                        <td align="right" width="80"><strong><? echo number_format($party_totalMaterProduction,2); ?></strong></td>
	                        <td align="right" width="80"><strong><? echo number_format($party_productionBal,2); ?></strong></td>
	                        <td align="right" width="80"><strong><? echo number_format($party_totalMaterQc,2); ?></strong></td>
	                        <td align="right" width="80"><strong><? echo number_format($party_totalMaterReject,2); ?></strong></td>
	                        <td align="right" width="80"><strong><? echo number_format($party_qcBal,2); ?></strong></td>
	                        <td align="right" width="80"><strong><? echo number_format($party_totalMaterDelivery,2); ?></strong></td>
	                        <td align="right" width="80"><strong><? echo number_format($party_delBal,2); ?></strong></td>
	                        <td align="right" width="80"><strong><? echo number_format($party_total_balance,2); ?></strong></td>
	                	</tr>
						<?
						$grand_order_qty+=$party_order_qty;
						$grand_totalMaterRec+=$party_totalMaterRec;
						$grand_rcvBal+=$party_rcvBal;
						$grand_total_bundle_qty+=$party_total_bundle_qty;
						$grand_bndlBal+=$party_bndlBal;
						$grand_totalMaterProduction+=$party_totalMaterProduction;
						$grand_productionBal+=$party_productionBal;
						$grand_totalMaterQc+=$party_totalMaterQc;
						$grand_qcBal+=$party_qcBal;
						$grand_totalMaterReject+=$party_totalMaterReject;
						$grand_totalMaterDelivery+=$party_totalMaterDelivery;
						$grand_delBal+=$party_delBal;
						$grand_total_balance+=$party_total_balance;
					}
				}
                ?>
                </tbody>
                <tfoot>
					<tr bgcolor='#bfffc7'> 
	                    <th colspan="9" align="right"><strong>Grand Total:</strong></th>
	                    <th width="80" align="right" title="<? echo $unit_of_measurement[$row[csf('order_uom')]]; ?>"><? echo number_format($grand_order_qty,2); ?></th>
	                    <th width="80" align="center">&nbsp;</th>
	                    <th align="right" width="80"><strong><? echo number_format($grand_totalMaterRec,2); ?></strong></th>
	                    <th align="right" width="80"><strong>&nbsp;</strong></th>
	                    <th align="right" width="80"><strong><? echo number_format($grand_rcvBal,2); ?></strong></th>
	                    <th align="right" width="80"><strong><? echo number_format($grand_total_bundle_qty,2); ?></strong></th>
	                    <th align="right" width="80"><strong><? echo number_format($grand_bndlBal,2); ?></strong></th>
	                    <th align="right" width="80"><strong><? echo number_format($grand_totalMaterProduction,2); ?></strong></th>
	                    <th align="right" width="80"><strong><? echo number_format($grand_productionBal,2); ?></strong></th>
	                    <th align="right" width="80"><strong><? echo number_format($grand_totalMaterQc,2); ?></strong></th>
	                    <th align="right" width="80"><strong><? echo number_format($grand_totalMaterReject,2); ?></strong></th>
	                    <th align="right" width="80"><strong><? echo number_format($grand_qcBal,2); ?></strong></th>
	                    <th align="right" width="80"><strong><? echo number_format($grand_totalMaterDelivery,2); ?></strong></th>
	                    <th align="right" width="80"><strong><? echo number_format($grand_delBal,2); ?></strong></th>
	                    <th align="right" width="80"><strong><? echo number_format($grand_total_balance,2); ?></strong></th>
	            	</tr>
                </tfoot>
                </table>
             </div>
           </div>
        </div><!-- end main div -->
		<?
	$html = ob_get_contents();
	ob_clean();
	$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
	
	
	foreach (glob("*.xls") as $filename) {
		@unlink($filename);
	}
	
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,$html);
	echo "$html**$filename";
	exit();
}

if ($action=='emb_job_popup')
{
	echo load_html_head_contents("Job Popup", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $data; die;
    $ex_data 			= explode("_", $data);
    $job_no 			= $ex_data[0];
    $order_id 			= $ex_data[1];
    $within_group 		= $ex_data[2];
    $date_from 			= $ex_data[3];
    $date_to 			= $ex_data[4];
    $jobid 				= $ex_data[5];
    
	//echo $buyer_po;die;
	$po_break_down_id=str_replace("'","",$po_break_down_id);
	$company_name=str_replace("'","",$company_name);
	$item_id=str_replace("'","",$item_id);
	$country_id=str_replace("'","",$country_id);
	$color_id=str_replace("'","",$color_id);
	
	/*if ($color!=''){
		$dtls_sys_cond.=" and a.color_id=$color";
		$rcv_dtls_sys_cond.=" and d.color_id=$color";
	} 
	if ($bodyPart!=''){
		$dtls_sys_cond.=" and b.body_part=$bodyPart";
		$rcv_dtls_sys_cond.=" and c.body_part=$bodyPart";
	} 
	if ($buyer_po!=''){
		$dtls_sys_cond.=" and b.buyer_po_no='$buyer_po'";
		$rcv_dtls_sys_cond.=" and c.buyer_po_no='$buyer_po'";
	}*/ 

	$sql_rec_del="SELECT d.color_id,d.size_id,b.quantity
	from  sub_material_mst a, subcon_ord_dtls c, sub_material_dtls b ,subcon_ord_breakdown d 
	where a.id=b.mst_id and c.id=b.job_dtls_id and b.job_break_id=d.id and b.job_dtls_id=d.mst_id and a.entry_form in ( 205 ) and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.job_no_mst='$job_no'";
	$sql_rec_del_res=sql_select($sql_rec_del);
	foreach ($sql_rec_del_res as $row)
	{ 
		$size_wise_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]['rec_quantity'] +=$row[csf("quantity")];
	}

	$sql_bndl_del="SELECT d.color_id,d.size_id,f.bundle_qty as quantity
    from  sub_material_mst a, subcon_ord_dtls c, sub_material_dtls b ,subcon_ord_breakdown d ,prnting_bundle_mst e , prnting_bundle_dtls f
	where a.id=b.mst_id and c.id=b.job_dtls_id and b.job_break_id=d.id and b.job_dtls_id=d.mst_id and e.id=f.mst_id and e.po_id =c.id and e.item_rcv_dtls_id=b.id and e.id=f.mst_id and f.item_rcv_id=b.mst_id and a.entry_form in ( 205 ) and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.job_no_mst='$job_no'"; 
	$sql_bndl_del_res=sql_select($sql_bndl_del);
	foreach ($sql_bndl_del_res as $row)
	{ 
		$size_wise_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]['bundle_qty'] +=$row[csf("quantity")];
	}

	$sql_iss_del="SELECT a.size_id,a.color_id,d.quantity from subcon_ord_breakdown a, subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e where e.id=d.mst_id and a.id=d.wo_break_id and a.mst_id=b.id and b.id=d.wo_dtls_id and a.job_no_mst=c.embellishment_job and d.wo_id=c.id and b.mst_id=c.id and d.entry_form=495 and e.entry_form=495 and a.job_no_mst='$job_no' and c.id=$jobid and a.status_active =1 and a.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0 $dtls_sys_cond";
	$sql_iss_del_res=sql_select($sql_iss_del);
	foreach ($sql_iss_del_res as $row)
	{ 
		$size_wise_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]['issue_quantity'] +=$row[csf("quantity")];
	}

	$sql_prod_del="SELECT a.size_id,a.color_id,d.quantity from subcon_ord_breakdown a, subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e where e.id=d.mst_id and a.id=d.wo_break_id and a.mst_id=b.id and b.id=d.wo_dtls_id and a.job_no_mst=c.embellishment_job and d.wo_id=c.id and b.mst_id=c.id and d.entry_form=497 and e.entry_form=497 and a.job_no_mst='$job_no' and c.id=$jobid  and a.status_active =1 and a.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0 $dtls_sys_cond";
	$sql_prod_del_res=sql_select($sql_prod_del);
	foreach ($sql_prod_del_res as $row)
	{ 
		$size_wise_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]['prod_quantity'] +=$row[csf("quantity")];
	} 

	$sql_rej_del="SELECT a.size_id,a.color_id,d.quantity,d.defect_qty,d.reject_qty from subcon_ord_breakdown a, subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e where e.id=d.mst_id and a.id=d.wo_break_id and a.mst_id=b.id and b.id=d.wo_dtls_id and a.job_no_mst=c.embellishment_job and d.wo_id=c.id and b.mst_id=c.id and d.entry_form=498 and e.entry_form=498 and a.job_no_mst='$job_no' and c.id=$jobid  and a.status_active =1 and a.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0  $dtls_sys_cond";
	$sql_rej_del_res=sql_select($sql_rej_del);
	foreach ($sql_rej_del_res as $row)
	{ 
		$defect_qty=$row[csf("defect_qty")];
		$indivisual_defect_qty=explode('_', $defect_qty);
		$size_wise_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]['fab_reject'] 	+=$indivisual_defect_qty[0];
		$size_wise_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]['print_reject'] +=$indivisual_defect_qty[1];
		$size_wise_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]['part_short'] 	+=$indivisual_defect_qty[2];
		$size_wise_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]['qc_quantity'] 	+=$row[csf("quantity")];
		$size_wise_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]['reject_qty'] 	+=$row[csf("reject_qty")];
	}
	/*echo '<pre>';
	print_r($size_wise_qty_arr);*/

	$sql_del_del="SELECT a.size_id,a.color_id,d.quantity from subcon_ord_breakdown a, subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e where e.id=d.mst_id and a.id=d.wo_break_id and a.mst_id=b.id and b.id=d.wo_dtls_id and a.job_no_mst=c.embellishment_job and d.wo_id=c.id and b.mst_id=c.id and d.entry_form=499 and e.entry_form=499 and a.job_no_mst='$job_no' and c.id=$jobid and a.status_active =1 and a.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0 $dtls_sys_cond";
	$sql_del_del_res=sql_select($sql_del_del);
	foreach ($sql_del_del_res as $row)
	{ 
		$size_wise_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]['del_quantity'] +=$row[csf("quantity")];
	}
	/*echo '<pre>';
	print_r($size_wise_qty_arr);*/

	//echo $prod_sql;
	$prod_color_size_data=array();
	
	//var_dump($color_library);die;
	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
	$size_Arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	?>
	 <div id="data_panel" align="center" style="width:100%">
	         <script>
			 	function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
	         </script>
	 	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onClick="new_window()" />
	 </div>
	  
	<div style="width:800px" align="center" id="details_reports"> 
	  	<legend>Color And Size Wise Summary</legend>
	    <table id="tbl_id" class="rpt_table" width="800" border="1" rules="all" >
	    	<thead>
	        	<tr>
	            	<th width="70">Party</th>
	            	<th width="70">Buyer</th>
	                <th width="100">Job Number</th>
	                <th width="120">Style Name</th>
	                <th width="100">Order Number</th>
	                <th width="70">Ship Date</th>
	                <th width="150">Item Name</th>
	                <th >Order Qty.</th>
	            </tr>
	        </thead>
	       	<?
	       	//if ($color!='') $sys_cond.=" and c.color_id=$color";
    		//if ($bodyPart!='') $sys_cond.=" and b.body_part=$bodyPart";
    		if ($job_no!='') $sys_cond.=" and a.embellishment_job='$job_no'";
    		//if ($buyer_po!='') $sys_cond.=" and b.buyer_po_no='$buyer_po'";

			$sql="SELECT  a.id as jobid,a.embellishment_job as job_no, a.party_id,  b.order_no, a.job_no_prefix_num as job_prefix,b.body_part ,c.color_id, a.delivery_date,c.qnty,b.order_uom,b.buyer_po_no, b.buyer_style_ref, b.gmts_item_id,c.size_id,b.buyer_buyer
			from subcon_ord_mst a, subcon_ord_breakdown c ,subcon_ord_dtls b
			where b.job_no_mst=a.embellishment_job and b.id=c.mst_id and c.job_no_mst=b.job_no_mst and a.entry_form=204 $sys_cond and c.qnty<>0 and a.is_deleted =0";
			//$sql_result=sql_select($sql);
			//echo $sql;die;
			$job_arr=array();  $job_wise_qty_arr=array(); $size_library=array(); $color_library=array();
			$resultRow=sql_select($sql);
			foreach ($resultRow as $row)
			{ 
				if($row[csf('order_uom')]==1){
					$order_qty=$row[csf('qnty')];
				}else if($row[csf('order_uom')]==2){ 
					$order_qty=$row[csf('qnty')]*12;
				}
				//$order_qty=$row[csf('qnty')];
				$job_arr[$row[csf("job_no")]]['party_id'] =$row[csf("party_id")];
				$job_arr[$row[csf("job_no")]]['buyer_style_ref'] .=$row[csf("buyer_style_ref")].',';
				$job_arr[$row[csf("job_no")]]['buyer_buyer'] .=$row[csf("buyer_buyer")].',';
				$job_arr[$row[csf("job_no")]]['order_no'] =$row[csf("order_no")];
				$job_arr[$row[csf("job_no")]]['delivery_date'] .=change_date_format($row[csf("delivery_date")]).',';
				$job_arr[$row[csf("job_no")]]['gmts_item_id'] .=$garments_item[$row[csf("gmts_item_id")]].',';
				$job_arr[$row[csf("job_no")]]['qnty'] +=$order_qty;

				$job_wise_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]['order_quantity'] +=$order_qty;
				$size_library[$row[csf("size_id")]]=$row[csf("size_id")];
				$color_library[$row[csf("color_id")]]=$row[csf("color_id")];
			}

			//$party_id=implode(",",array_unique(explode(",",chop($job_arr[$job_no]['party_id'],','))));
			$buyer_buyer=implode(",",array_unique(explode(",",chop($job_arr[$job_no]['buyer_buyer'],','))));
			$buyer_style_ref=implode(",",array_unique(explode(",",chop($job_arr[$job_no]['buyer_style_ref'],','))));
			$delivery_date=implode(",",array_unique(explode(",",chop($job_arr[$job_no]['delivery_date'],','))));
			$gmts_item_id=implode(",",array_unique(explode(",",chop($job_arr[$job_no]['gmts_item_id'],','))));


			/*echo '<pre>';
			print_r($job_wise_qty_arr);*/

			/*$all_size=array_unique(explode(",",chop($resultRow[0][csf("size_id")],',')));
			foreach($all_size as $val)
			{
			 	$size_library[$val]=$val;
			}*/
			//$color_library[$color]=$color;
			$trbgcolor="#c8f4ce";
			$trbgcolor2="#f4d0c8";
	 		?> 
	        <tbody>
	        	<tr bgcolor="<? echo $trbgcolor; ?>" >
	                <td><strong><p><? echo $buyer_short_library[$job_arr[$job_no]['party_id']]; ?></p></strong></td>
	                <td><strong><p><? echo $buyer_buyer; ?></p></strong></td>
	                <td><strong><p><? echo $job_no; ?></p></strong></td>
	                <td><strong><p><? echo $buyer_style_ref; ?></p></strong></td>
	                <td><strong><p><? echo $job_arr[$job_no]['order_no']; ?></p></strong></td>
	                <td align="center"><strong><? echo $delivery_date; ?></strong></td>
	                <td><strong><? echo $gmts_item_id; ?></strong></td>
	                <td align="right"><strong><? echo $job_arr[$job_no]['qnty']; ?></strong></td>
	            </tr>
	            <tr bgcolor="<? echo $trbgcolor2; ?>">	
	                <td colspan="2"><strong>Total Print Reject Qty : <span id="totalprintReject" bgcolor="#E9F3FF" align="left"></span></strong>  </td>
	                <td colspan="2"><strong>Total Fabric Reject  Qty : <span id="totalfebReject" bgcolor="#E9F3FF" align="left"></span></strong></td>
	                <td colspan="2"><strong>Total Short Qty: <span id="totalshortReject" bgcolor="#E9F3FF" align="left"></span></strong></td>
	                <td colspan="2">&nbsp;</td>
	            </tr>
	        </tbody>
	    </table>
	    <?
	  	$count = count($size_library);	
	  	$width= $count*70+350; 		
		?>
	    <table id="tblDtls_id" class="rpt_table" width="<? echo $width; ?>" border="1" rules="all" >
		 	<thead>
	        	<tr>
	            	<th width="100">Color Name</th>
	                <th width="170">Production Type</th>
	 				<?
					foreach($size_library as $val)
					{
					 	?><th width="80"><? echo $size_Arr_library[$val]; ?></th><?
					}
					?>
	     		    <th width="60">Total</th>
	           </tr>
	        </thead>
	        <tbody>
		        <?
				foreach($color_library as $colorId=>$totalorderqnty)
				{
					$row_span=16; $blank_rowspan=3;
 					$bgcolor1="#ecfdfd"; 
					$bgcolor2="#FFFFFF";
					$bgcolor3="#d7f1ff";
					$ord_html=$rcv_html=$rcv_bal_html=$bndl_html=$bndl_bal_html=$issue_html=$issue_bal_html= $prod_html=$prod_bal_html= $qc_html= $qc_bal_html= $reject_html= $ready_del_html= $del_html= $del_bal_html=''; 
					$total_ord=$total_rcv=$total_rcv_bal=$total_bndl=$total_bndl_bal=$total_issue= $total_issue_bal=$total_prod= $total_prod_bal= $total_qc= $total_qc_bal= $total_reject= $total_ready_del= $total_del= $total_bal_del=0; 
					$x=1;
					foreach($size_library as $sizeId=>$sizeRes)
					{ 
						$order_quantity=$rec_quantity=$rcv_balance=$bundle_qty=$bndl_balance=$issue_quantity= $issue_balance=$prod_quantity=$prod_balance=$qc_quantity=$qc_balance= $reject_qty=$fab_reject= $print_reject=$part_short=$ready_del_quantity=$del_quantity=$del_bal_quantity=0;
						$blank_rowspan++;
						//issue_quantity prod_quantity fab_reject print_reject part_short del_quantity
						//echo $sizeId.'=';
						$order_quantity =$job_wise_qty_arr[$colorId][$sizeId]['order_quantity'];
						$rec_quantity 	=$size_wise_qty_arr[$colorId][$sizeId]['rec_quantity'];
						$rcv_balance	=$order_quantity-$rec_quantity;
						$bundle_qty 	=$size_wise_qty_arr[$colorId][$sizeId]['bundle_qty'];
						$bndl_balance	=$rec_quantity-$bundle_qty;
						$issue_quantity =$size_wise_qty_arr[$colorId][$sizeId]['issue_quantity'];
						$issue_balance	=$rec_quantity-$issue_quantity;
						
						$prod_quantity 	=$size_wise_qty_arr[$colorId][$sizeId]['prod_quantity'];
						$prod_balance	=$issue_quantity-$prod_quantity;
						$qc_quantity 	=$size_wise_qty_arr[$colorId][$sizeId]['qc_quantity'];
						$qc_balance		=$prod_quantity-$qc_quantity;
						$reject_qty 	=$size_wise_qty_arr[$colorId][$sizeId]['reject_qty'];
						$fab_reject 	=$size_wise_qty_arr[$colorId][$sizeId]['fab_reject'];
						$print_reject 	=$size_wise_qty_arr[$colorId][$sizeId]['print_reject'];
						$part_short 	=$size_wise_qty_arr[$colorId][$sizeId]['part_short'];
						
						$ready_del_quantity=$qc_quantity-$reject_qty;
						$del_quantity   =$size_wise_qty_arr[$colorId][$sizeId]['del_quantity'];
						$del_bal_quantity=$qc_quantity-$del_quantity;

						$bgCol="bgcolor='#fff7e7'"; 
						$ord_html .='<td '.$bgCol.'>'.$order_quantity.'</td>';
	                    $total_ord+=$order_quantity;
	                    $rcv_html .='<td '.$bgCol.'>'.$rec_quantity.'</td>';
	                    $total_rcv+=$rec_quantity;
						$rcv_bal_html .='<td '.$bgCol.'>'.$rcv_balance.'</td>';
	                    $total_rcv_bal+=$rcv_balance;
	                    $bndl_html .='<td '.$bgCol.'>'.$bundle_qty.'</td>';
	                    $total_bndl+=$bundle_qty;
	                    $bndl_bal_html .='<td '.$bgCol.'>'.$bndl_balance.'</td>';
	                    $total_bndl_bal+=$bndl_balance;

	                    $issue_html .='<td '.$bgCol.'>'.$issue_quantity.'</td>';
	                    $total_issue+=$issue_quantity;
	                    $issue_bal_html .='<td '.$bgCol.'>'.$issue_balance.'</td>';
	                    $total_issue_bal+=$issue_balance;

	                    $prod_html .='<td '.$bgCol.'>'.$prod_quantity.'</td>';
	                    $total_prod+=$prod_quantity;
	                    $prod_bal_html .='<td '.$bgCol.'>'.$prod_balance.'</td>';
	                    $total_prod_bal+=$prod_balance;
	                    $qc_html .='<td '.$bgCol.'>'.$qc_quantity.'</td>';
	                    $total_qc+=$qc_quantity;
	                    $qc_bal_html .='<td '.$bgCol.'>'.$qc_balance.'</td>';
	                    $total_qc_bal+=$qc_balance;
	                    $reject_html .='<td '.$bgCol.'>'.$reject_qty.'</td>';
	                    $total_reject+=$reject_qty;
	                    $total_fab_reject+=$fab_reject;
	                    $total_print_reject+=$print_reject;
	                    $total_part_short+=$part_short;

	                    $ready_del_html .='<td '.$bgCol.'>'.$ready_del_quantity.'</td>';
	                    $total_ready_del+=$ready_del_quantity;
	                    $del_html .='<td '.$bgCol.'>'.$del_quantity.'</td>';
	                    $total_del+=$del_quantity;
	                    $del_bal_html .='<td '.$bgCol.'>'.$del_bal_quantity.'</td>';
	                    $total_bal_del+=$del_bal_quantity;
	                    $x++;
					}// end size foreach loop		
						
					?>	  
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td align="center" rowspan="<? echo $row_span; ?>"  style=" font-size: large; vertical-align : middle; text-align:center;" ><strong><? echo $color_Arr_library[$colorId]; ?></strong>
						</td>
	                	<td><b>Order Quantity</b></td>
	                    <? echo $ord_html; ?> 
	                    <td><? echo $total_ord; ?></td> 
	                </tr>
	                <tr bgcolor="<? echo $bgcolor1; ?>">
	                	<td><b>Plan To Cut (AVG)</b></td>
	                    <? echo $rcv_html; ?> 
	                    <td><? echo $total_rcv; ?></td> 
	                </tr>
					<tr bgcolor="<? echo $bgcolor1; ?>">
	                	<td><b>Cutting Part Receive</b></td>
	                    <? echo $rcv_html; ?> 
	                    <td><? echo $total_rcv; ?></td> 
	                </tr>
	                <tr bgcolor="<? echo $bgcolor2; ?>">
	                	<td><b>Cutting Part Rcvd Balance</b></td>
	                    <? echo $rcv_bal_html; ?> 
	                    <td><? echo $total_rcv_bal; ?></td> 
	                </tr>
	                <tr bgcolor="<? echo $bgcolor1; ?>">
	                	<td><b>Barcode Quantity</b></td>
	                    <? echo $bndl_html; ?> 
	                    <td><? echo $total_bndl; ?></td> 
	                </tr>
	                <tr bgcolor="<? echo $bgcolor2; ?>">
	                	<td><b>Barcode Balance Quantity</b></td>
	                    <? echo $bndl_bal_html; ?> 
	                    <td><? echo $total_bndl_bal; ?></td> 
	                </tr>
	                <tr bgcolor="<? echo $bgcolor1; ?>">
	                	<td><b>Printing Material Issue</b></td>
	                    <? echo $issue_html; ?> 
	                    <td><? echo $total_issue; ?></td> 
	                </tr>
	                <tr bgcolor="<? echo $bgcolor2; ?>">
	                	<td><b>Printing Material Issue Balance</b></td>
	                    <? echo $issue_bal_html; ?> 
	                    <td><? echo $total_issue_bal; ?></td> 
	                </tr>
	                <tr bgcolor="<? echo $bgcolor1; ?>">
	                	<td><b>Printing Production</b></td>
	                    <? echo $prod_html; ?> 
	                    <td><? echo $total_prod; ?></td> 
	                </tr>
	                <tr bgcolor="<? echo $bgcolor2; ?>">
	                	<td><b>Printing Production Balance</b></td>
	                   <? echo $prod_bal_html; ?> 
	                    <td><? echo $total_prod_bal; ?></td> 
	                </tr>
	                <tr bgcolor="<? echo $bgcolor1; ?>">
	                	<td><b>Quality Check Quantity</b></td>
	                    <? echo $qc_html; ?> 
	                    <td><? echo $total_qc; ?></td> 
	                </tr>
	                <tr bgcolor="<? echo $bgcolor2; ?>">
	                	<td><b>Quality Check Quantity Balance</b></td>
	                    <? echo $qc_bal_html; ?> 
	                    <td><? echo $total_qc_bal; ?></td> 
	                </tr>
	                <tr bgcolor="<? echo $bgcolor1; ?>">
	                	<td><b>Quality Reject Quantity</b></td>
	                    <? echo $reject_html; ?> 
	                    <td><? echo $total_reject; ?></td> 
	                </tr>
	                <tr bgcolor="<? echo $bgcolor2; ?>">
	                	<td><b>Ready for Delivery</b></td>
	                    <? echo $ready_del_html; ?> 
	                    <td><? echo $total_ready_del; ?></td> 
	                </tr>
	               	<tr bgcolor="<? echo $bgcolor1; ?>">
	                	<td><b>Delivery Quantity</b></td>
	                   	<? echo $del_html; ?> 
	                    <td><? echo $total_del; ?></td> 
	                </tr>
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><b>Delivery Balance (In-hand)</b></td>
						<? echo $del_bal_html; ?> 
						<td><? echo $total_bal_del; ?></td> 
					</tr>
					<tr bgcolor="<? echo $bgcolor3; ?>"> <td colspan="<? echo $blank_rowspan; ?>">&nbsp;  </td></tr>
				<?	
				}// end color foreach loop
				?>
			</tbody>
	 	</table>
	</div>
	<script type="text/javascript">
    	var total_fab_reject = '<?php echo $total_fab_reject ;?>';
    	document.getElementById("totalfebReject").textContent=total_fab_reject;
    	var total_print_reject = '<?php echo $total_print_reject ;?>';
    	document.getElementById("totalprintReject").textContent=total_print_reject;
    	var total_part_short = '<?php echo $total_part_short ;?>';
    	document.getElementById("totalshortReject").textContent=total_part_short;
    	
    </script>    
	<?
	exit();

}

if ($action=='order_popup')
{
	echo load_html_head_contents("Order Popup", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
    $ex_data 			= explode("_", $data);
    $job_no 			= $ex_data[0];
    $order_id 			= $ex_data[1];
    $within_group 		= $ex_data[2];
    $date_from 			= $ex_data[3];
    $date_to 			= $ex_data[4];
    $all_rcv_dtls_ids 	= $ex_data[5];
    $color 				= $ex_data[6];
    $bodyPart 			= $ex_data[7];
    $all_bndl_rcv_ids 	= $ex_data[8];
    $issue_ids 			= $ex_data[9];
    $production_ids 	= $ex_data[10];
    $qc_reject_ids 		= $ex_data[11];
    $del_ids 			= $ex_data[12];
    $all_chemical_issue_id 	= $ex_data[13];
    $buyer_po 	= $ex_data[14];
	//echo $buyer_po;die;
	$po_break_down_id=str_replace("'","",$po_break_down_id);
	$company_name=str_replace("'","",$company_name);
	$item_id=str_replace("'","",$item_id);
	$country_id=str_replace("'","",$country_id);
	$color_id=str_replace("'","",$color_id);
	
	if ($color!=''){
		$dtls_sys_cond.=" and a.color_id=$color";
		$rcv_dtls_sys_cond.=" and d.color_id=$color";
	} 
	if ($bodyPart!=''){
		$dtls_sys_cond.=" and b.body_part=$bodyPart";
		$rcv_dtls_sys_cond.=" and c.body_part=$bodyPart";
	} 
	if ($buyer_po!=''){
		$dtls_sys_cond.=" and b.buyer_po_no='$buyer_po'";
		$rcv_dtls_sys_cond.=" and c.buyer_po_no='$buyer_po'";
	} 

	$sql_rec_del="SELECT d.color_id,d.size_id,b.quantity
	from  sub_material_mst a, subcon_ord_dtls c, sub_material_dtls b ,subcon_ord_breakdown d 
	where a.id=b.mst_id and c.id=b.job_dtls_id and b.job_break_id=d.id and b.job_dtls_id=d.mst_id and a.entry_form in ( 205 ) and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id in ($all_rcv_dtls_ids) $rcv_dtls_sys_cond";
	$sql_rec_del_res=sql_select($sql_rec_del);
	foreach ($sql_rec_del_res as $row)
	{ 
		$size_wise_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]['rec_quantity'] +=$row[csf("quantity")];
	}

	$sql_bndl_del="SELECT d.color_id,d.size_id,f.bundle_qty as quantity
    from  sub_material_mst a, subcon_ord_dtls c, sub_material_dtls b ,subcon_ord_breakdown d ,prnting_bundle_mst e , prnting_bundle_dtls f
	where a.id=b.mst_id and c.id=b.job_dtls_id and b.job_break_id=d.id and b.job_dtls_id=d.mst_id and e.id=f.mst_id and e.po_id =c.id and e.item_rcv_dtls_id=b.id and e.id=f.mst_id and f.item_rcv_id=b.mst_id and a.entry_form in ( 205 ) and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.id in ($all_bndl_rcv_ids) $rcv_dtls_sys_cond"; 
	$sql_bndl_del_res=sql_select($sql_bndl_del);
	foreach ($sql_bndl_del_res as $row)
	{ 
		$size_wise_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]['bundle_qty'] +=$row[csf("quantity")];
	}

	$sql_iss_del="SELECT a.size_id,a.color_id,d.quantity from subcon_ord_breakdown a, subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e where e.id=d.mst_id and e.id in ($issue_ids) and a.id=d.wo_break_id and a.mst_id=b.id and b.id=d.wo_dtls_id and a.job_no_mst=c.embellishment_job and d.wo_id=c.id and b.mst_id=c.id and d.entry_form=495 and e.entry_form=495 and d.mst_id in ($issue_ids) and a.status_active =1 and a.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0 $dtls_sys_cond";
	$sql_iss_del_res=sql_select($sql_iss_del);
	foreach ($sql_iss_del_res as $row)
	{ 
		$size_wise_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]['issue_quantity'] +=$row[csf("quantity")];
	}

	$sql_prod_del="SELECT a.size_id,a.color_id,d.quantity from subcon_ord_breakdown a, subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e where e.id=d.mst_id and e.id in ($production_ids) and a.id=d.wo_break_id and a.mst_id=b.id and b.id=d.wo_dtls_id and a.job_no_mst=c.embellishment_job and d.wo_id=c.id and b.mst_id=c.id and d.entry_form=497 and e.entry_form=497 and d.mst_id in ($production_ids) and a.status_active =1 and a.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0 $dtls_sys_cond";
	$sql_prod_del_res=sql_select($sql_prod_del);
	foreach ($sql_prod_del_res as $row)
	{ 
		$size_wise_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]['prod_quantity'] +=$row[csf("quantity")];
	} 

	$sql_rej_del="SELECT a.size_id,a.color_id,d.quantity,d.defect_qty,d.reject_qty from subcon_ord_breakdown a, subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e where e.id=d.mst_id and e.id in ($qc_reject_ids) and a.id=d.wo_break_id and a.mst_id=b.id and b.id=d.wo_dtls_id and a.job_no_mst=c.embellishment_job and d.wo_id=c.id and b.mst_id=c.id and d.entry_form=498 and e.entry_form=498 and d.mst_id in ($qc_reject_ids) and a.status_active =1 and a.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0  $dtls_sys_cond";
	$sql_rej_del_res=sql_select($sql_rej_del);
	foreach ($sql_rej_del_res as $row)
	{ 
		$defect_qty=$row[csf("defect_qty")];
		$indivisual_defect_qty=explode('_', $defect_qty);
		$size_wise_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]['fab_reject'] 	+=$indivisual_defect_qty[0];
		$size_wise_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]['print_reject'] +=$indivisual_defect_qty[1];
		$size_wise_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]['part_short'] 	+=$indivisual_defect_qty[2];
		$size_wise_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]['qc_quantity'] 	+=$row[csf("quantity")];
		$size_wise_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]['reject_qty'] 	+=$row[csf("reject_qty")];
	}
	/*echo '<pre>';
	print_r($size_wise_qty_arr);*/

	$sql_del_del="SELECT a.size_id,a.color_id,d.quantity from subcon_ord_breakdown a, subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e where e.id=d.mst_id and e.id in ($del_ids) and a.id=d.wo_break_id and a.mst_id=b.id and b.id=d.wo_dtls_id and a.job_no_mst=c.embellishment_job and d.wo_id=c.id and b.mst_id=c.id and d.entry_form=499 and e.entry_form=499 and d.mst_id in ($del_ids) and a.status_active =1 and a.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0 $dtls_sys_cond";
	$sql_del_del_res=sql_select($sql_del_del);
	foreach ($sql_del_del_res as $row)
	{ 
		$size_wise_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]['del_quantity'] +=$row[csf("quantity")];
	}
	/*echo '<pre>';
	print_r($size_wise_qty_arr);*/

	//echo $prod_sql;
	$prod_color_size_data=array();
	
	//var_dump($color_library);die;
	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
	$size_Arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	?>
	 <div id="data_panel" align="center" style="width:100%">
	         <script>
			 	function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
	         </script>
	 	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onClick="new_window()" />
	 </div>
	  
	<div style="width:800px" align="center" id="details_reports"> 
	  	<legend>Color And Size Wise Summary</legend>
	    <table id="tbl_id" class="rpt_table" width="800" border="1" rules="all" >
	    	<thead>
	        	<tr>
	            	<th width="70">Party</th>
	            	<th width="70">Buyer</th>
	                <th width="100">Job Number</th>
	                <th width="120">Style Name</th>
	                <th width="100">Order Number</th>
	                <th width="70">Ship Date</th>
	                <th width="150">Item Name</th>
	                <th >Order Qty.</th>
	            </tr>
	        </thead>
	       	<?
	       	if ($color!='') $sys_cond.=" and c.color_id=$color";
    		if ($bodyPart!='') $sys_cond.=" and b.body_part=$bodyPart";
    		if ($job_no!='') $sys_cond.=" and a.embellishment_job='$job_no'";
    		if ($buyer_po!='') $sys_cond.=" and b.buyer_po_no='$buyer_po'";

			$sql="SELECT  a.id as jobid,a.embellishment_job as job_no, a.party_id,  b.order_no, a.job_no_prefix_num as job_prefix,b.body_part ,c.color_id, a.delivery_date,c.qnty,b.order_uom,b.buyer_po_no, b.buyer_style_ref, b.gmts_item_id,c.size_id,b.buyer_buyer
			from subcon_ord_mst a, subcon_ord_breakdown c ,subcon_ord_dtls b
			where b.job_no_mst=a.embellishment_job and b.id=c.mst_id and c.job_no_mst=b.job_no_mst and a.entry_form=204 $sys_cond and c.qnty<>0 and a.is_deleted =0";
			//$sql_result=sql_select($sql);
			//echo $sql;die;
			$job_arr=array();  $job_wise_qty_arr=array(); $size_library=array(); $color_library=array();
			$resultRow=sql_select($sql);
			foreach ($resultRow as $row)
			{ 
				if($row[csf('order_uom')]==1){
					$order_qty=$row[csf('qnty')];
				}else if($row[csf('order_uom')]==2){ 
					$order_qty=$row[csf('qnty')]*12;
				}
				//$order_qty=$row[csf('qnty')];
				$job_arr[$row[csf("job_no")]]['party_id'] =$row[csf("party_id")];
				$job_arr[$row[csf("job_no")]]['buyer_style_ref'] =$row[csf("buyer_style_ref")];
				$job_arr[$row[csf("job_no")]]['buyer_buyer'] =$row[csf("buyer_buyer")];
				$job_arr[$row[csf("job_no")]]['order_no'] =$row[csf("order_no")];
				$job_arr[$row[csf("job_no")]]['delivery_date'] =$row[csf("delivery_date")];
				$job_arr[$row[csf("job_no")]]['gmts_item_id'] =$row[csf("gmts_item_id")];
				$job_arr[$row[csf("job_no")]]['qnty'] +=$order_qty;

				$job_wise_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]['order_quantity'] +=$order_qty;
				$size_library[$row[csf("size_id")]]=$row[csf("size_id")];
			}
			/*echo '<pre>';
			print_r($job_wise_qty_arr);*/

			$all_size=array_unique(explode(",",chop($resultRow[0][csf("size_id")],',')));
			foreach($all_size as $val)
			{
			 	$size_library[$val]=$val;
			}
			$color_library[$color]=$color;
			$trbgcolor="#c8f4ce";
			$trbgcolor2="#f4d0c8";
	 		?> 
	        <tbody>
	        	<tr bgcolor="<? echo $trbgcolor; ?>" >
	                <td><strong><p><? echo $buyer_short_library[$job_arr[$job_no]['party_id']]; ?></p></strong></td>
	                <td><strong><p><? echo $job_arr[$job_no]['buyer_buyer']; ?></p></strong></td>
	                <td><strong><p><? echo $job_no; ?></p></strong></td>
	                <td><strong><p><? echo $job_arr[$job_no]['buyer_style_ref']; ?></p></strong></td>
	                <td><strong><p><? echo $job_arr[$job_no]['order_no']; ?></p></strong></td>
	                <td align="center"><strong><? echo change_date_format($job_arr[$job_no]['delivery_date']); ?></strong></td>
	                <td><strong><? echo $garments_item[$job_arr[$job_no]['gmts_item_id']]; ?></strong></td>
	                <td align="right"><strong><? echo $job_arr[$job_no]['qnty']; ?></strong></td>
	            </tr>
	            <tr bgcolor="<? echo $trbgcolor2; ?>">	
	                <td colspan="2"><strong>Total Print Reject Qty : <span id="totalprintReject" bgcolor="#E9F3FF" align="left"></span></strong>  </td>
	                <td colspan="2"><strong>Total Fabric Reject  Qty : <span id="totalfebReject" bgcolor="#E9F3FF" align="left"></span></strong></td>
	                <td colspan="2"><strong>Total Short Qty: <span id="totalshortReject" bgcolor="#E9F3FF" align="left"></span></strong></td>
	                <td colspan="2">&nbsp;</td>
	            </tr>
	        </tbody>
	    </table>
	    <?
	  	$count = count($size_library);	
	  	$width= $count*70+350; 		
		?>
		<table id="tblDtls_id" class="rpt_table" width="<? echo $width; ?>" border="1" rules="all" >
		 	<thead>
	        	<tr>
	            	<th width="100">Color Name</th>
	                <th width="170">Production Type</th>
	 				<?
					foreach($size_library as $val)
					{
					 	?><th width="80"><? echo $size_Arr_library[$val]; ?></th><?
					}
					?>
	     		    <th width="60">Total</th>
	           </tr>
	        </thead>
	        <tbody>
		        <?
				foreach($color_library as $colorId=>$totalorderqnty)
				{
					$row_span=16; $blank_rowspan=3;
 					$bgcolor1="#ecfdfd"; 
					$bgcolor2="#FFFFFF";
					$bgcolor3="#d7f1ff";
					$ord_html=$rcv_html=$rcv_bal_html=$bndl_html=$bndl_bal_html=$issue_html=$issue_bal_html= $prod_html=$prod_bal_html= $qc_html= $qc_bal_html= $reject_html= $ready_del_html= $del_html= $del_bal_html=''; 
					$total_ord=$total_rcv=$total_rcv_bal=$total_bndl=$total_bndl_bal=$total_issue= $total_issue_bal=$total_prod= $total_prod_bal= $total_qc= $total_qc_bal= $total_reject= $total_ready_del= $total_del= $total_bal_del=0; 
					$x=1;
					foreach($size_library as $sizeId=>$sizeRes)
					{ 
						$order_quantity=$rec_quantity=$rcv_balance=$bundle_qty=$bndl_balance=$issue_quantity= $issue_balance=$prod_quantity=$prod_balance=$qc_quantity=$qc_balance= $reject_qty=$fab_reject= $print_reject=$part_short=$ready_del_quantity=$del_quantity=$del_bal_quantity=0;
						$blank_rowspan++;
						//issue_quantity prod_quantity fab_reject print_reject part_short del_quantity
						//echo $sizeId.'=';
						$order_quantity =$job_wise_qty_arr[$colorId][$sizeId]['order_quantity'];
						$rec_quantity 	=$size_wise_qty_arr[$colorId][$sizeId]['rec_quantity'];
						$rcv_balance	=$order_quantity-$rec_quantity;
						$bundle_qty 	=$size_wise_qty_arr[$colorId][$sizeId]['bundle_qty'];
						$bndl_balance	=$rec_quantity-$bundle_qty;
						$issue_quantity =$size_wise_qty_arr[$colorId][$sizeId]['issue_quantity'];
						$issue_balance	=$rec_quantity-$issue_quantity;
						
						$prod_quantity 	=$size_wise_qty_arr[$colorId][$sizeId]['prod_quantity'];
						$prod_balance	=$issue_quantity-$prod_quantity;
						$qc_quantity 	=$size_wise_qty_arr[$colorId][$sizeId]['qc_quantity'];
						$qc_balance		=$prod_quantity-$qc_quantity;
						$reject_qty 	=$size_wise_qty_arr[$colorId][$sizeId]['reject_qty'];
						$fab_reject 	=$size_wise_qty_arr[$colorId][$sizeId]['fab_reject'];
						$print_reject 	=$size_wise_qty_arr[$colorId][$sizeId]['print_reject'];
						$part_short 	=$size_wise_qty_arr[$colorId][$sizeId]['part_short'];
						
						$ready_del_quantity=$qc_quantity-$reject_qty;
						$del_quantity   =$size_wise_qty_arr[$colorId][$sizeId]['del_quantity'];
						$del_bal_quantity=$qc_quantity-$del_quantity;

						$bgCol="bgcolor='#fff7e7'"; 
						$ord_html .='<td '.$bgCol.'>'.$order_quantity.'</td>';
	                    $total_ord+=$order_quantity;
	                    $rcv_html .='<td '.$bgCol.'>'.$rec_quantity.'</td>';
	                    $total_rcv+=$rec_quantity;
						$rcv_bal_html .='<td '.$bgCol.'>'.$rcv_balance.'</td>';
	                    $total_rcv_bal+=$rcv_balance;
	                    $bndl_html .='<td '.$bgCol.'>'.$bundle_qty.'</td>';
	                    $total_bndl+=$bundle_qty;
	                    $bndl_bal_html .='<td '.$bgCol.'>'.$bndl_balance.'</td>';
	                    $total_bndl_bal+=$bndl_balance;

	                    $issue_html .='<td '.$bgCol.'>'.$issue_quantity.'</td>';
	                    $total_issue+=$issue_quantity;
	                    $issue_bal_html .='<td '.$bgCol.'>'.$issue_balance.'</td>';
	                    $total_issue_bal+=$issue_balance;

	                    $prod_html .='<td '.$bgCol.'>'.$prod_quantity.'</td>';
	                    $total_prod+=$prod_quantity;
	                    $prod_bal_html .='<td '.$bgCol.'>'.$prod_balance.'</td>';
	                    $total_prod_bal+=$prod_balance;
	                    $qc_html .='<td '.$bgCol.'>'.$qc_quantity.'</td>';
	                    $total_qc+=$qc_quantity;
	                    $qc_bal_html .='<td '.$bgCol.'>'.$qc_balance.'</td>';
	                    $total_qc_bal+=$qc_balance;
	                    $reject_html .='<td '.$bgCol.'>'.$reject_qty.'</td>';
	                    $total_reject+=$reject_qty;
	                    $total_fab_reject+=$fab_reject;
	                    $total_print_reject+=$print_reject;
	                    $total_part_short+=$part_short;

	                    $ready_del_html .='<td '.$bgCol.'>'.$ready_del_quantity.'</td>';
	                    $total_ready_del+=$ready_del_quantity;
	                    $del_html .='<td '.$bgCol.'>'.$del_quantity.'</td>';
	                    $total_del+=$del_quantity;
	                    $del_bal_html .='<td '.$bgCol.'>'.$del_bal_quantity.'</td>';
	                    $total_bal_del+=$del_bal_quantity;
	                    $x++;
					}// end size foreach loop		
						
					?>	  
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td align="center" rowspan="<? echo $row_span; ?>"  style=" font-size: large; vertical-align : middle; text-align:center;" ><strong><? echo $color_Arr_library[$colorId]; ?></strong>
						</td>
	                	<td><b>Order Quantity</b></td>
	                    <? echo $ord_html; ?> 
	                    <td><? echo $total_ord; ?></td> 
	                </tr>
	                <tr bgcolor="<? echo $bgcolor1; ?>">
	                	<td><b>Plan To Cut (AVG)</b></td>
	                    <? echo $rcv_html; ?> 
	                    <td><? echo $total_rcv; ?></td> 
	                </tr>
					<tr bgcolor="<? echo $bgcolor1; ?>">
	                	<td><b>Cutting Part Receive</b></td>
	                    <? echo $rcv_html; ?> 
	                    <td><? echo $total_rcv; ?></td> 
	                </tr>
	                <tr bgcolor="<? echo $bgcolor2; ?>">
	                	<td><b>Cutting Part Rcvd Balance</b></td>
	                    <? echo $rcv_bal_html; ?> 
	                    <td><? echo $total_rcv_bal; ?></td> 
	                </tr>
	                <tr bgcolor="<? echo $bgcolor1; ?>">
	                	<td><b>Barcode Quantity</b></td>
	                    <? echo $bndl_html; ?> 
	                    <td><? echo $total_bndl; ?></td> 
	                </tr>
	                <tr bgcolor="<? echo $bgcolor2; ?>">
	                	<td><b>Barcode Balance Quantity</b></td>
	                    <? echo $bndl_bal_html; ?> 
	                    <td><? echo $total_bndl_bal; ?></td> 
	                </tr>
	                <tr bgcolor="<? echo $bgcolor1; ?>">
	                	<td><b>Printing Material Issue</b></td>
	                    <? echo $issue_html; ?> 
	                    <td><? echo $total_issue; ?></td> 
	                </tr>
	                <tr bgcolor="<? echo $bgcolor2; ?>">
	                	<td><b>Printing Material Issue Balance</b></td>
	                    <? echo $issue_bal_html; ?> 
	                    <td><? echo $total_issue_bal; ?></td> 
	                </tr>
	                <tr bgcolor="<? echo $bgcolor1; ?>">
	                	<td><b>Printing Production</b></td>
	                    <? echo $prod_html; ?> 
	                    <td><? echo $total_prod; ?></td> 
	                </tr>
	                <tr bgcolor="<? echo $bgcolor2; ?>">
	                	<td><b>Printing Production Balance</b></td>
	                   <? echo $prod_bal_html; ?> 
	                    <td><? echo $total_prod_bal; ?></td> 
	                </tr>
	                <tr bgcolor="<? echo $bgcolor1; ?>">
	                	<td><b>Quality Check Quantity</b></td>
	                    <? echo $qc_html; ?> 
	                    <td><? echo $total_qc; ?></td> 
	                </tr>
	                <tr bgcolor="<? echo $bgcolor2; ?>">
	                	<td><b>Quality Check Quantity Balance</b></td>
	                    <? echo $qc_bal_html; ?> 
	                    <td><? echo $total_qc_bal; ?></td> 
	                </tr>
	                <tr bgcolor="<? echo $bgcolor1; ?>">
	                	<td><b>Quality Reject Quantity</b></td>
	                    <? echo $reject_html; ?> 
	                    <td><? echo $total_reject; ?></td> 
	                </tr>
	                <tr bgcolor="<? echo $bgcolor2; ?>">
	                	<td><b>Ready for Delivery</b></td>
	                    <? echo $ready_del_html; ?> 
	                    <td><? echo $total_ready_del; ?></td> 
	                </tr>
	               	<tr bgcolor="<? echo $bgcolor1; ?>">
	                	<td><b>Delivery Quantity</b></td>
	                   	<? echo $del_html; ?> 
	                    <td><? echo $total_del; ?></td> 
	                </tr>
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><b>Delivery Balance (In-hand)</b></td>
						<? echo $del_bal_html; ?> 
						<td><? echo $total_bal_del; ?></td> 
					</tr>
					<tr bgcolor="<? echo $bgcolor3; ?>"> <td colspan="<? echo $blank_rowspan; ?>">&nbsp;  </td></tr>
				<?	
				}// end color foreach loop
				?>
			</tbody>
	 	</table>
	    
	</div>
	<script type="text/javascript">
    	var total_fab_reject = '<?php echo $total_fab_reject ;?>';
    	document.getElementById("totalfebReject").textContent=total_fab_reject;
    	var total_print_reject = '<?php echo $total_print_reject ;?>';
    	document.getElementById("totalprintReject").textContent=total_print_reject;
    	var total_part_short = '<?php echo $total_part_short ;?>';
    	document.getElementById("totalshortReject").textContent=total_part_short;
    	
    </script>    
	<?
	exit();

}

if($action=="chemical_issue_popup")
{
	echo load_html_head_contents("Chemical Details","../../../", 1, 1, $unicode);
	extract($_REQUEST);
    $ex_data 	= explode("_", $data);
    $job_no 	= $ex_data[0];
    $order_id 	= $ex_data[1];
    $within_group= $ex_data[2];
    $date_from 	= $ex_data[3];
    $date_to 	= $ex_data[4];
    $all_chemical_issue_id = $ex_data[5];
    $color 		= $ex_data[6];
    $bodyPart 	= $ex_data[7];

    /*$date_cond	= "";
    if($date_from !="" && $date_to !="")
    {
    	$date_cond = " and a.delivery_date between '$date_from' and '$date_to' ";
    }
    if ($color!='') $sys_cond.=" and d.color_id=$color";
    if ($bodyPart!='') $sys_cond.=" and c.body_part=$bodyPart";*/
    
	$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
	$order_cond = "";
  	  $dyes_issue_sql="SELECT a.issue_number,a.issue_date, a.id as issue_id,d.recipe_no,d.job_no,d.color_id, b.product_id, b.req_qny_edit as req_qny_edit,b.item_category,(c.cons_amount) as dyes_chemical_cost,b.recipe_qnty, e.color_id as recipe_color,a.req_id
	from inv_issue_master a, dyes_chem_issue_dtls b, inv_transaction c ,pro_recipe_entry_mst d, pro_recipe_entry_dtls e
	where a.id=b.mst_id and b.trans_id=c.id and a.entry_form=250 and d.entry_form=220 and a.issue_basis=7 and to_char(d.id)= b.recipe_id and b.item_category in (5,6,7,22)  and c.transaction_type=2 and d.id=e.mst_id and e.prod_id=c.prod_id and a.id in ($all_chemical_issue_id) and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0";
	$dyes_issue_sql_result=sql_select($dyes_issue_sql);
	/*SL 	Issue Date 	Recipe No 	Job No	Recipie Color 	Garments Color 	Recipie Qty 	Req No 	Req Qty 	Issue No 	Issue Qty*/

	$dyes_chemical_arr=array();
	foreach($dyes_issue_sql_result as $val)
	{
		/*$dyes_chemical_arr[$val[csf("job_no")]][$val[csf("color_id")]][$val[csf("item_category")]]['chemical_cost']+=$val[csf("dyes_chemical_cost")];
		$dyes_chemical_arr[$val[csf("job_no")]][$val[csf("color_id")]][$val[csf("item_category")]]['dyes_issue_qty']+=$val[csf("req_qny_edit")];*/
		$dyes_chemical_arr[$val[csf("job_no")]][$val[csf("color_id")]]['chemical_cost']+=$val[csf("dyes_chemical_cost")];
		$dyes_chemical_arr[$val[csf("job_no")]][$val[csf("color_id")]]['chemical_issue_qty']+=$val[csf("req_qny_edit")];
		$dyes_chemical_arr[$val[csf("job_no")]][$val[csf("color_id")]]['issue_id'] .=$val[csf("issue_id")].',';
	}

    $width=900;
	$width_px=$width.'px';
	?>    
    <div id="data_panel" align="center" style="width:100%">
       	<fieldset style="width: 98%">
	    <legend> Bundle Balance Quantity</legend>
	     	<div style="width:100%;">
	            <table align="center" cellspacing="0" width="<? echo $width; ?>" border="1" rules="all" class="rpt_table" style="font-size:12px">
	                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
	                    <!-- SL 	Issue Date 	Recipe No 	Job No	Recipie Color 	Garments Color 	Recipie Qty 	Req No 	Req Qty 	Issue No 	Issue Qty -->
	                    <th width="30">SL</th>
	                    <th width="60">Issue Date</th>
	                    <th width="100">Recipe No</th>
	                    <th width="120">Job No</th>
	                    <th width="100">Recipie Color</th>
	                    <th width="100">Garments Color</th>
	                    <th width="60">Recipie Qty</th>
	                    <th width="100">Req No</th>
	                    <th width="60">Req Qty</th>
	                    <th width="100">Issue No</th>
	                    <th>Issue Qty</th>
	                </thead>
	                <tbody>
	                	<? $i=1;
	                	foreach($dyes_issue_sql_result as $row)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							
							$recipe_qnty=$row[csf('recipe_qnty')];
							$recipe_qnty_sum += $recipe_qnty;
							
							$req_qny_edit=$row[csf('req_qny_edit')];
							$req_qny_edit_sum += $req_qny_edit;

						/*$dyes_issue_sql="SELECT a.issue_number,a.issue_date, a.id as issue_id,d.recipe_no,d.job_no,d.color_id, b.product_id, b.req_qny_edit as req_qny_edit,b.item_category,(c.cons_amount) as dyes_chemical_cost,b.recipe_qnty, e.color_id as recipe_color,a.req_id
						from inv_issue_master a, dyes_chem_issue_dtls b, inv_transaction c ,pro_recipe_entry_mst d, pro_recipe_entry_dtls e
						where a.id=b.mst_id and b.trans_id=c.id and a.entry_form=250 and d.entry_form=220 and a.issue_basis=7 and to_char(d.id)= b.recipe_id and b.item_category in (5,6,7) and a.buyer_job_no=d.job_no and c.transaction_type=2 and d.id=e.mst_id and e.prod_id=c.prod_id and a.id in ($all_chemical_issue_id) and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0";*/
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
				                <td align="center"><? echo $i; ?></td>
				                <td align="center" style="word-break:break-all"><? echo $row[csf("issue_date")]; ?></td>
				                <td style="word-break:break-all"><? echo $row[csf("recipe_no")]; ?></td>
				                <td style="word-break:break-all"><? echo $row[csf("job_no")]; ?></td>
				                <td style="word-break:break-all"><? echo $color_arr[$row[csf("recipe_color")]]; ?></td>
				                <td style="word-break:break-all"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
				                <td align="right"><? echo number_format($row[csf("recipe_qnty")],2); ?></td>
				                <td align="right"><? echo $row[csf("req_id")]; ?></td>
				                <td align="right"><? echo $row[csf("req_id")]; ?></td>
				                <td align="right"><? echo $row[csf("issue_number")]; ?></td>
				                <td align="right"><? echo number_format($row[csf("req_qny_edit")],2); ?></td>
							</tr>
							<? $i++; 
							$total_recipe_qnty+=$row[csf("recipe_qnty")];
							$total_req_qnty+=$row[csf("recipe_qnty")];
							$total_req_qny_edit+=$row[csf("req_qny_edit")];
						} 
	                	?>
	                </tbody>
	                <tfoot>
	                	<td colspan="6" align="right"><b>Total:</b></td>
	                	<td align="right" ><strong><? echo number_format($total_recipe_qnty,2); ?></strong></td>
	                	<td align="right" >&nbsp;</td>
	                	<td align="right" ><strong><? echo number_format($total_req_qnty,2); ?></strong></td>
	                	<td align="right" >&nbsp;</td>
	                	<td align="right" ><strong><? echo number_format($total_req_qny_edit,2); ?></strong></td>
	                </tfoot>
	            </table>
        </div>
	    </fieldset>
    </div> 
    <?
    exit(); 
}

if($action=="chemical_issue_cost_popup") //
{
	echo load_html_head_contents("Cost Details", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
    $ex_data 	= explode("_", $data);
    $job_no 	= $ex_data[0];
    $order_id 	= $ex_data[1];
    $within_group= $ex_data[2];
    $date_from 	= $ex_data[3];
    $date_to 	= $ex_data[4];
    $all_chemical_issue_id = $ex_data[5];
    $color 		= $ex_data[6];
    $bodyPart 	= $ex_data[7];
	//echo $batch_id;die;
	/*$batch_non_redyeing_id=return_field_value("id","pro_batch_create_mst","batch_against<>2 and status_active=1 and id in($batch_id)","id");
	if($batch_non_redyeing_id=="")die;
	
	if($batch_non_redyeing_id!="") $batch_cond=" and a.batch_no like '$batch_non_redyeing_id'";*/
	
	$sql_dtls_dyes = "select c.id as prod_id, c.product_name_details, c.unit_of_measure, c.avg_rate_per_unit, sum(b.cons_quantity) as qnty,sum(b.cons_amount) as cons_amount
	from inv_issue_master a,inv_transaction b, product_details_master c
	where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=2 and a.entry_form=250 and b.item_category in (6) and a.id in ($all_chemical_issue_id) and b.status_active=1 group by c.id, c.product_name_details, c.unit_of_measure, c.avg_rate_per_unit"; 
	// echo $sql_dtls_dyes;die;
	$sql_result_dyes= sql_select($sql_dtls_dyes);
	
	$sql_dtls_chemical = "select c.id as prod_id, c.product_name_details, c.unit_of_measure, c.avg_rate_per_unit, sum(b.cons_quantity) as qnty,sum(b.cons_amount) as cons_amount
	from inv_issue_master a,inv_transaction b, product_details_master c
	where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=2 and a.entry_form=250  and b.item_category in (5,7,22) and a.id in ($all_chemical_issue_id) and b.status_active=1 group by c.id, c.product_name_details, c.unit_of_measure, c.avg_rate_per_unit"; 
	//echo $sql_dtls;die;
	$sql_result_chemical= sql_select($sql_dtls_chemical);
	?>
    <script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}	
	</script>	
	<div style="width:770px; margin-left:30px" id="report_div">
     <!--<div style="width:870px;" align="center"><input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
    <div style="width:770px; font-family:'Arial Narrow'; font-size:14px;">Dyes</div>
    <table align="center" cellspacing="0" width="770" border="1" rules="all" class="rpt_table" >
        <thead align="center">
    	   <tr>
                <th width="50">SL</th>
                <th width="50">Product Id</th>
                <th width="250">Item Description</th>
                <th width="100">UOM</th>
                <th width="100">Quantity</th>
                <th width="100">Avg. Rate</th> 
                <th>Amount(BDT)</th>
           </tr>
		</thead>
		<?  
		$i=1;
		foreach($sql_result_dyes as $row)
		{
			if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
			?>
			<tbody>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><? echo $i; ?></td>
					<td align="center"><? echo $row[csf("prod_id")]; ?></td>
					<td><? echo $row[csf("product_name_details")]; ?></td>
					<td align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
					<td align="right"><? echo number_format($row[csf("qnty")],4,'.',''); $total_dyeing_qnty+=$row[csf("qnty")]; ?></td>
					<td align="right"><? echo number_format($row[csf("avg_rate_per_unit")],4,'.',''); ?></td>
					<td align="right"><? $amount=0; $amount=$row[csf("cons_amount")]; echo  number_format($amount,6,'.',''); $total_dyeing_amount+=$amount; ?></td>
				</tr>
			</tbody>
			<? 
			$i++;
		}
		?>
        <tfoot>
        	<tr>
                <th colspan="4" align="right">Total:</th>
                <th align="right"><? echo number_format($total_dyeing_qnty,2,'.',''); ?></th>
                <th></th>
                <th align="right"><? echo number_format($total_dyeing_amount,2,'.',''); ?></th>
            </tr>
        </tfoot>
      </table>
      
      <div style="width:770px; font-family:'Arial Narrow'; font-size:14px;">Chemical</div>
    <table align="center" cellspacing="0" width="770" border="1" rules="all" class="rpt_table" >
        <thead align="center">
    	   <tr>
                <th width="50">SL</th>
                <th width="50">Product Id</th>
                <th width="250">Item Description</th>
                <th width="100">UOM</th>
                <th width="100">Quantity</th>
                <th width="100">Avg. Rate</th> 
                <th>Amount(BDT)</th>
           </tr>
		</thead>
		<?  
		foreach($sql_result_chemical as $row)
		{
			if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
			?>
			<tbody>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><? echo $i; ?></td>
					<td align="center"><? echo $row[csf("prod_id")]; ?></td>
					<td><? echo $row[csf("product_name_details")]; ?></td>
					<td align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
					<td align="right"><? echo number_format($row[csf("qnty")],4,'.',''); $total_chemical_qnty+=$row[csf("qnty")]; ?></td>
					<td align="right"><? echo number_format($row[csf("avg_rate_per_unit")],4,'.',''); ?></td>
					<td align="right"><? $amount=0; $amount=$row[csf("cons_amount")]; echo  number_format($amount,6,'.',''); $total_chemical_amount+=$amount; ?></td>
				</tr>
			</tbody>
			<? 
			$i++;
		}
		?>
        <tfoot>
        	<tr>
                <th colspan="4" align="right">Total:</th>
                <th align="right"><? echo number_format($total_chemical_qnty,2,'.',''); ?></th>
                <th></th>
                <th align="right"><? echo number_format($total_chemical_amount,2,'.',''); ?></th>
            </tr>
        </tfoot>
      </table>
    </div>
    <?         
	exit();
}


if($action=="bndl_bal_popup")
{
	echo load_html_head_contents("Bundle Balance Details","../../../", 1, 1, $unicode);
	extract($_REQUEST);
    $ex_data 	= explode("_", $data);
    $job_no 	= $ex_data[0];
    $order_id 	= $ex_data[1];
    $within_group= $ex_data[2];
    $date_from 	= $ex_data[3];
    $date_to 	= $ex_data[4];
    $all_rcv_dtls_id = $ex_data[5];
    $color 		= $ex_data[6];
    $bodyPart 	= $ex_data[7];

    $date_cond	= "";
    if($date_from !="" && $date_to !="")
    {
    	$date_cond = " and a.delivery_date between '$date_from' and '$date_to' ";
    }
    if ($color!='') $sys_cond.=" and d.color_id=$color";
    if ($bodyPart!='') $sys_cond.=" and c.body_part=$bodyPart";
    
	$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
    $size_arr=return_library_array( "SELECT id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
    $buyer_arr = return_library_array("SELECT id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
    $floor_arr = return_library_array("SELECT id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and production_process=8", "id", "floor_name");
    $table_arr = return_library_array("SELECT id, table_name from lib_table_entry where status_active=1 and is_deleted=0", "id", "table_name");
	
	$order_cond = "";

	/*$sql_receive_bundle="SELECT a.po_id, a.item_rcv_dtls_id, b.item_rcv_id,b.id as dtls_id, b.bundle_qty, c.body_part, d.job_break_id,c.job_no_mst  from  prnting_bundle_mst a, prnting_bundle_dtls b ,subcon_ord_dtls c, sub_material_dtls d  
   	where a.id=b.mst_id and a.po_id =c.id and a.item_rcv_dtls_id=d.id and b.item_rcv_id=d.mst_id and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $sql_po_con";*/

   	/*Barcode Date	Customer	Receive Challan NO 	Buyer	Style	Job No	Color	L	M	S	XL	XS	XXL	Total	Remarks */

	$sql_wo_del="SELECT a.subcon_date,a.party_id,a.sys_no as challan_no,c.buyer_buyer, c.buyer_style_ref,c.buyer_po_no,d.color_id,d.size_id,a.insert_date, sum(f.bundle_qty) as bundle_qty,b.quantity
    from  sub_material_mst a, subcon_ord_dtls c, subcon_ord_breakdown d, sub_material_dtls b 
    LEFT JOIN prnting_bundle_mst e
	ON b.id=e.item_rcv_dtls_id
	LEFT JOIN prnting_bundle_dtls f
	ON e.id=f.mst_id
	where a.id=b.mst_id and c.id=b.job_dtls_id and b.job_break_id=d.id and b.job_dtls_id=d.mst_id and a.entry_form in ( 205 ) and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id in ($all_rcv_dtls_id) $sys_cond
	group by a.subcon_date,a.party_id,a.sys_no,c.buyer_buyer, c.buyer_style_ref,c.buyer_po_no,d.color_id,d.size_id,a.insert_date,b.quantity
	"; 
   	
	$sql_wo_del_res=sql_select($sql_wo_del);
	foreach ($sql_wo_del_res as $row)
	{ 
		if($row[csf("size_id")]!="") $all_sizes.=$row[csf("size_id")].',';
		if($row[csf("party_id")]!="") $all_party_id.=$row[csf("party_id")].',';
		if($row[csf("bundle_dtls_id")]!="") $all_bundle_dtls_ids.=$row[csf("bundle_dtls_id")].',';
		if($row[csf("issue_dtls_id")]!="") $all_issue_dtls_ids.=$row[csf("issue_dtls_id")].',';
		$wo_del_arr[$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['quantity'] +=$row[csf("quantity")];
		$wo_del_arr[$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['bundle_qty'] +=$row[csf("bundle_qty")];
		$wo_del_arr[$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['size_id'] .=$row[csf("size_id")].',';
		$wo_del_arr[$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['issue_dtls_id'] .=$row[csf("issue_dtls_id")].',';
		
		$wo_del_size_arr[$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['quantity'] +=$row[csf("quantity")];
		$wo_del_size_arr[$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['bundle_qty'] +=$row[csf("bundle_qty")];
		/*$wo_del_size_arr[$row[csf("subcon_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['bundle_dtls_id'] .=$row[csf("bundle_dtls_id")].',';
		$wo_del_size_arr[$row[csf("subcon_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['issue_dtls_id'] .=$row[csf("issue_dtls_id")].',';*/
	}
	$all_size=array_unique(explode(",",chop($all_sizes,',')));

    $width=970+(count($all_size)*160);
	$mst_width=$width-810;
	$top_width=$width-400;
	$width_px=$width.'px';
	?>    
    <div id="data_panel" align="center" style="width:100%">
       	<fieldset style="width: 98%">
	    <legend> Bundle Balance Quantity</legend>
	     	<div style="width:100%;">
	            <table align="center" cellspacing="0" width="<? echo $width; ?>" border="1" rules="all" class="rpt_table" style="font-size:12px">
	                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
	                    <!-- Customer	Receive Challan NO 	Buyer	Style	Job No	Color	L	M	S	XL	XS	XXL	Total Barcode Balance -->
	                    <th width="30">SL</th>
	                    <th width="100">Customer</th>
	                    <th width="100">Receive Challan</th>
	                    <th width="120">Buyer</th>
	                    <th width="100">Style</th>
	                    <th width="100">Buyer PO</th>
	                    <th width="100">Color</th>
	                    <?
	                    foreach ($all_size as $val)
						{ 
							?>
							<th width="100"><? echo $size_arr[$val]; ?></th>
							<?
						}
					    ?>
                    <th width="100">Total Barcode Balance</th>
                    <th>Remarks</th>
	                </thead>
	                <tbody>
	                	<?
	                	if(count($wo_del_arr)>0)
				    	{ 
				    		//$wo_del_arr[$row[csf("delivery_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]
				    		$i=1; $tot_size_arr=array();
							foreach ($wo_del_arr as $party_id => $party_id_data ) 
							{
								foreach ($party_id_data as $challan_no => $challan_no_data ) 
								{
									foreach ($challan_no_data as $buyer_buyer => $buyer_buyer_data ) 
									{
										foreach ($buyer_buyer_data as $buyer_style_ref => $buyer_style_ref_data ) 
										{
											foreach ($buyer_style_ref_data as $buyer_po_no => $buyer_po_no_data ) 
											{
												foreach ($buyer_po_no_data as $color_id => $row ) 
												{
													//echo $row['size_id'].'=='; 
													//$all_del_sizes=array_unique(explode(",",chop($row['size_id'],',')));
													if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													?>
								                    <tr bgcolor="<? echo $bgcolor; ?>">
								                        <td><? echo $i; ?></td>
								                        <td style="word-break:break-all"><? echo $buyer_arr[$party_id]; ?></td>
								                        <td style="word-break:break-all"><? echo $challan_no; ?></td>
								                        <td style="word-break:break-all"><? echo $buyer_buyer; ?></td>
								                        <td style="word-break:break-all"><? echo $buyer_style_ref; ?></td>
								                        <td style="word-break:break-all"><? echo $buyer_po_no; ?></td>
								                        <td style="word-break:break-all"><? echo $color_arr[$color_id]; ?></td>
								                        <?
								                        $size_total_qty=0; $cut_nos=''; $cut_no=''; $bundle_dtls_id='';
									                    foreach ($all_size as $val)
														{ 
															$quantity=$wo_del_size_arr[$party_id][$challan_no][$buyer_buyer][$buyer_style_ref][$buyer_po_no][$color_id][$val]['quantity'];
															$bundle_qty=$wo_del_size_arr[$party_id][$challan_no][$buyer_buyer][$buyer_style_ref][$buyer_po_no][$color_id][$val]['bundle_qty'];
															$balance=$quantity-$bundle_qty;

															?>
															<td align="right" ><? echo $balance; ?></td>
															<?
															$size_total_qty +=$balance;
															$tot_size_arr[$val]+=$balance;
															$balance=$quantity=$bundle_qty=0;
														}
														?>
								                        <td align="right" ><? echo $size_total_qty; ?></td>
								                        <td style="word-break:break-all"></td>
								                    </tr>
													<?
													$i++;
												}
											}
										}
									}
								}
							}
						}
	                	?>
	                </tbody>
	                <tfoot>
	                	<td colspan="7" align="right"><b>Total:</b></td>
	                	<?
	                    foreach ($all_size as $val)
						{ 
							$totalQuantity=$tot_size_arr[$val];
							$grand_fab_defect_qty+=$fab_defect_qty;
							$grand_print_defect_qty+=$print_defect_qty;
							$grandTotalQuantity+=$totalQuantity;
							?>
							<td align="right" ><strong><? echo $totalQuantity; ?></strong></td>
							
							<?
						}
						?>
	                    <td align="right" ><strong><? echo $grandTotalQuantity; ?></td>
	                    <td align="right" >&nbsp;</td>
	                </tfoot>
	            </table>
        </div>
	    </fieldset>
    </div> 
    <?
    exit(); 
}

if($action=="bndl_popup")
{
	echo load_html_head_contents("Bundle Details","../../../", 1, 1, $unicode);
	extract($_REQUEST);
    $ex_data 	= explode("_", $data);
    $job_no 	= $ex_data[0];
    $order_id 	= $ex_data[1];
    $within_group= $ex_data[2];
    $date_from 	= $ex_data[3];
    $date_to 	= $ex_data[4];
    $all_bndl_rcv_ids = $ex_data[5];
    $color 		= $ex_data[6];
    $bodyPart 	= $ex_data[7];

    $date_cond	= "";
    if($date_from !="" && $date_to !="")
    {
    	$date_cond = " and a.delivery_date between '$date_from' and '$date_to' ";
    }
    if ($color!='') $sys_cond.=" and d.color_id=$color";
    if ($bodyPart!='') $sys_cond.=" and c.body_part=$bodyPart";
    
	$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
    $size_arr=return_library_array( "SELECT id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
    $buyer_arr = return_library_array("SELECT id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
    $floor_arr = return_library_array("SELECT id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and production_process=8", "id", "floor_name");
    $table_arr = return_library_array("SELECT id, table_name from lib_table_entry where status_active=1 and is_deleted=0", "id", "table_name");
	
	$order_cond = "";

	/*$sql_receive_bundle="SELECT a.po_id, a.item_rcv_dtls_id, b.item_rcv_id,b.id as dtls_id, b.bundle_qty, c.body_part, d.job_break_id,c.job_no_mst  from  prnting_bundle_mst a, prnting_bundle_dtls b ,subcon_ord_dtls c, sub_material_dtls d  
   	where a.id=b.mst_id and a.po_id =c.id and a.item_rcv_dtls_id=d.id and b.item_rcv_id=d.mst_id and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $sql_po_con";*/

   	/*Barcode Date	Customer	Receive Challan NO 	Buyer	Style	Job No	Color	L	M	S	XL	XS	XXL	Total	Remarks */

	$sql_wo_del="SELECT a.subcon_date,a.party_id,a.sys_no as challan_no,a.chalan_no as rcv_challan_no,c.buyer_buyer, c.buyer_style_ref,c.buyer_po_no,d.color_id,d.size_id,a.insert_date,f.bundle_qty as quantity
    from  sub_material_mst a, subcon_ord_dtls c, sub_material_dtls b ,subcon_ord_breakdown d ,prnting_bundle_mst e , prnting_bundle_dtls f
	where a.id=b.mst_id and c.id=b.job_dtls_id and b.job_break_id=d.id and b.job_dtls_id=d.mst_id and e.id=f.mst_id and e.po_id =c.id and e.item_rcv_dtls_id=b.id and e.id=f.mst_id and f.item_rcv_id=b.mst_id and a.entry_form in ( 205 ) and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.id in ($all_bndl_rcv_ids) $sys_cond"; 
   	
	$sql_wo_del_res=sql_select($sql_wo_del);
	foreach ($sql_wo_del_res as $row)
	{ 
		if($row[csf("size_id")]!=""){
			$all_sizes[$row[csf("size_id")]]=$row[csf("size_id")];
		}
		if($row[csf("party_id")]!="") $all_party_id.=$row[csf("party_id")].',';
		if($row[csf("bundle_dtls_id")]!="") $all_bundle_dtls_ids.=$row[csf("bundle_dtls_id")].',';
		if($row[csf("issue_dtls_id")]!="") $all_issue_dtls_ids.=$row[csf("issue_dtls_id")].',';
		$wo_del_arr[$row[csf("subcon_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['quantity'] +=$row[csf("quantity")];
		$wo_del_arr[$row[csf("subcon_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['rcv_challan_no'] =$row[csf("rcv_challan_no")];
		$wo_del_arr[$row[csf("subcon_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['size_id'] .=$row[csf("size_id")].',';
		//$wo_del_arr[$row[csf("subcon_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['issue_dtls_id'] .=$row[csf("issue_dtls_id")].',';
		
		$wo_del_size_arr[$row[csf("subcon_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['quantity'] +=$row[csf("quantity")];
		/*$wo_del_size_arr[$row[csf("subcon_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['bundle_dtls_id'] .=$row[csf("bundle_dtls_id")].',';
		$wo_del_size_arr[$row[csf("subcon_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['issue_dtls_id'] .=$row[csf("issue_dtls_id")].',';*/
	}
	$all_size=array_unique($all_sizes);

    $width=970+(count($all_size)*160);
	$mst_width=$width-810;
	$top_width=$width-400;
	$width_px=$width.'px';
	?>    
    <div id="data_panel" align="center" style="width:100%">
       	<fieldset style="width: 98%">
	    <legend> Bundle Quantity</legend>
	     	<div style="width:100%;">
	            <table align="center" cellspacing="0" width="<? echo $width; ?>" border="1" rules="all" class="rpt_table" style="font-size:12px">
	                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
	                    <!-- //Barcode Date	Customer	Receive Challan NO 	Buyer	Style	Job No	Color	L	M	S	XL	XS	XXL	Total	Remarks 
 -->
	                    <th width="30">SL</th>
	                    <th width="120">Barcode Date</th>
	                    <th width="100">Customer</th>
	                    <th width="100">Challan</th>
	                    <th width="100">Receive Challan</th>
	                    <th width="120">Buyer</th>
	                    <th width="100">Style</th>
	                    <th width="100">Buyer PO</th>
	                    <th width="100">Color</th>
	                    <?
	                    foreach ($all_size as $val)
						{ 
							?>
							<th width="100"><? echo $size_arr[$val]; ?></th>
							<?
						}
					    ?>
                    <th width="100">Total</th>
                    <th>Remarks</th>
	                </thead>
	                <tbody>
	                	<?
	                	if(count($wo_del_arr)>0)
				    	{ 
				    		//$wo_del_arr[$row[csf("delivery_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]
				    		$i=1; $tot_size_arr=array();
				    		foreach ($wo_del_arr as $delivery_date => $delivery_date_data ) 
							{
								foreach ($delivery_date_data as $party_id => $party_id_data ) 
								{
									foreach ($party_id_data as $challan_no => $challan_no_data ) 
									{
										foreach ($challan_no_data as $buyer_buyer => $buyer_buyer_data ) 
										{
											foreach ($buyer_buyer_data as $buyer_style_ref => $buyer_style_ref_data ) 
											{
												foreach ($buyer_style_ref_data as $buyer_po_no => $buyer_po_no_data ) 
												{
													$size_total_qty=0;
													foreach ($buyer_po_no_data as $color_id => $row ) 
													{
														$rcv_challan_no=$row['rcv_challan_no'];
														//echo $row['size_id'].'=='; 
														//$all_del_sizes=array_unique(explode(",",chop($row['size_id'],',')));
														if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
														?>
									                    <tr bgcolor="<? echo $bgcolor; ?>">
									                        <td><? echo $i; ?></td>
									                        <td style="word-break:break-all"><? echo $delivery_date ?></td>
									                        <td style="word-break:break-all"><? echo $buyer_arr[$party_id]; ?></td>
									                        <td style="word-break:break-all"><? echo $challan_no; ?></td>
									                        <td style="word-break:break-all"><? echo $rcv_challan_no; ?></td>
									                        <td style="word-break:break-all"><? echo $buyer_buyer; ?></td>
									                        <td style="word-break:break-all"><? echo $buyer_style_ref; ?></td>
									                        <td style="word-break:break-all"><? echo $buyer_po_no; ?></td>
									                        <td style="word-break:break-all"><? echo $color_arr[$color_id]; ?></td>
									                        <?
									                        $cut_nos=''; $cut_no=''; $bundle_dtls_id='';
										                    foreach ($all_size as $val)
															{ 
																$quantity=$wo_del_size_arr[$delivery_date][$party_id][$challan_no][$buyer_buyer][$buyer_style_ref][$buyer_po_no][$color_id][$val]['quantity'];

																?>
																<td align="right" ><? echo $quantity; ?></td>
																<?
																$size_total_qty +=$quantity;
																$tot_size_arr[$val]+=$quantity;
																$quantity=0;
															}
															?>
									                        <td align="right" ><? echo $size_total_qty; ?></td>
									                        <td style="word-break:break-all"></td>
									                    </tr>
														<?
														$i++;
													}
												}
											}
										}
									}
								}
							}
						}
						/*echo '<pre>';
						print_r($tot_size_arr);*/
	                	?>
	                </tbody>
	                <tfoot>
	                	<td colspan="9" align="right"><b>Total:</b></td>
	                	<?
	                    foreach ($all_size as $val)
						{ 
							$totalQuantity=$tot_size_arr[$val];
							$grand_fab_defect_qty+=$fab_defect_qty;
							$grand_print_defect_qty+=$print_defect_qty;
							$grandTotalQuantity+=$totalQuantity;
							?>
							<td align="right" ><strong><? echo $totalQuantity; ?></strong></td>
							
							<?
						}
						?>
	                    <td align="right" ><strong><? echo $grandTotalQuantity; ?></td>
	                    <td align="right" >&nbsp;</td>
	                </tfoot>
	            </table>
        </div>
	    </fieldset>
    </div> 
    <?
    exit(); 
}

if($action=="receive_popup")
{
	echo load_html_head_contents("Receive Details","../../../", 1, 1, $unicode);
	extract($_REQUEST);
    $ex_data 	= explode("_", $data);
    $job_no 	= $ex_data[0];
    $order_id 	= $ex_data[1];
    $within_group= $ex_data[2];
    $date_from 	= $ex_data[3];
    $date_to 	= $ex_data[4];
    $all_rcv_dtls_ids = $ex_data[5];
    $color 		= $ex_data[6];
    $bodyPart 	= $ex_data[7];

    $date_cond	= "";
    if($date_from !="" && $date_to !="")
    {
    	$date_cond = " and a.delivery_date between '$date_from' and '$date_to' ";
    }
    if ($color!='') $sys_cond.=" and d.color_id=$color";
    if ($bodyPart!='') $sys_cond.=" and c.body_part=$bodyPart";
    
	$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
    $size_arr=return_library_array( "SELECT id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
    $buyer_arr = return_library_array("SELECT id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
    $floor_arr = return_library_array("SELECT id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and production_process=8", "id", "floor_name");
    $table_arr = return_library_array("SELECT id, table_name from lib_table_entry where status_active=1 and is_deleted=0", "id", "table_name");
	
	$order_cond = "";

	$sql_wo_del="SELECT a.subcon_date,a.party_id,a.sys_no as challan_no,a.chalan_no as rcv_challan_no,c.buyer_buyer, c.buyer_style_ref,c.buyer_po_no,d.color_id,d.size_id,a.insert_date,b.quantity
    from  sub_material_mst a, subcon_ord_dtls c, sub_material_dtls b ,subcon_ord_breakdown d 
	where a.id=b.mst_id and c.id=b.job_dtls_id and b.job_break_id=d.id and b.job_dtls_id=d.mst_id and a.entry_form in ( 205 ) and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and b.id in ($all_rcv_dtls_ids) $sys_cond"; 
   	
	$sql_wo_del_res=sql_select($sql_wo_del);
	foreach ($sql_wo_del_res as $row)
	{ 
		if($row[csf("size_id")]!="") $all_sizes.=$row[csf("size_id")].',';
		if($row[csf("party_id")]!="") $all_party_id.=$row[csf("party_id")].',';
		if($row[csf("bundle_dtls_id")]!="") $all_bundle_dtls_ids.=$row[csf("bundle_dtls_id")].',';
		if($row[csf("issue_dtls_id")]!="") $all_issue_dtls_ids.=$row[csf("issue_dtls_id")].',';
		$wo_del_arr[$row[csf("subcon_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['quantity'] +=$row[csf("quantity")];
		$wo_del_arr[$row[csf("subcon_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['rcv_challan_no'] =$row[csf("rcv_challan_no")];


		$wo_del_arr[$row[csf("subcon_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['size_id'] .=$row[csf("size_id")].',';
		$wo_del_arr[$row[csf("subcon_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['issue_dtls_id'] .=$row[csf("issue_dtls_id")].',';
		
		$wo_del_size_arr[$row[csf("subcon_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['quantity'] +=$row[csf("quantity")];
		$wo_del_size_arr[$row[csf("subcon_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['insert_date'] =change_date_format($row[csf("insert_date")]);
		/*$wo_del_size_arr[$row[csf("subcon_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['bundle_dtls_id'] .=$row[csf("bundle_dtls_id")].',';
		$wo_del_size_arr[$row[csf("subcon_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['issue_dtls_id'] .=$row[csf("issue_dtls_id")].',';*/
	}
	$all_size=array_unique(explode(",",chop($all_sizes,',')));

    $width=970+(count($all_size)*160);
	$mst_width=$width-810;
	$top_width=$width-400;
	$width_px=$width.'px';
	?>    
    <div id="data_panel" align="center" style="width:100%">
       	<fieldset style="width: 98%">
	    <legend> Issue Quantity</legend>
	     	<div style="width:100%;">
	            <table align="center" cellspacing="0" width="<? echo $width; ?>" border="1" rules="all" class="rpt_table" style="font-size:12px">
	                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
	                    <!-- //Receive Date	Customer	Challan	Buyer	Style	Job No	Color	L	M	S	XL	Total	Entry Date	Remarkes -->
	                    <th width="30">SL</th>
	                    <th width="120">Receive Date</th>
	                    <th width="100">Customer</th>
	                    <th width="100">Challan</th>
	                    <th width="100">Receive Challan</th>
	                    <th width="120">Buyer</th>
	                    <th width="100">Style</th>
	                    <th width="100">Buyer PO</th>
	                    <th width="100">Color</th>
	                    <?
	                    foreach ($all_size as $val)
						{ 
							?>
							<th width="100"><? echo $size_arr[$val]; ?></th>
							<?
						}
					    ?>
                    <th width="100">Total</th>
                    <th width="60">Entry Date</th>
                    <th>Remarks</th>
	                </thead>
	                <tbody>
	                	<?
	                	if(count($wo_del_arr)>0)
				    	{ 
				    		//$wo_del_arr[$row[csf("delivery_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]
				    		$i=1; $tot_size_arr=array();
				    		foreach ($wo_del_arr as $delivery_date => $delivery_date_data ) 
							{
								foreach ($delivery_date_data as $party_id => $party_id_data ) 
								{
									foreach ($party_id_data as $challan_no => $challan_no_data ) 
									{
										foreach ($challan_no_data as $buyer_buyer => $buyer_buyer_data ) 
										{
											foreach ($buyer_buyer_data as $buyer_style_ref => $buyer_style_ref_data ) 
											{
												foreach ($buyer_style_ref_data as $buyer_po_no => $buyer_po_no_data ) 
												{
													foreach ($buyer_po_no_data as $color_id => $row ) 
													{
														$rcv_challan_no=$row['rcv_challan_no'];
														//echo $row['size_id'].'=='; 
														//$all_del_sizes=array_unique(explode(",",chop($row['size_id'],',')));
														if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
														?>
									                    <tr bgcolor="<? echo $bgcolor; ?>">
									                        <td><? echo $i; ?></td>
									                        <td style="word-break:break-all"><? echo $delivery_date ?></td>
									                        <td style="word-break:break-all"><? echo $buyer_arr[$party_id]; ?></td>
									                        <td style="word-break:break-all"><? echo $challan_no; ?></td>
									                        <td style="word-break:break-all"><? echo $rcv_challan_no; ?></td>
									                        <td style="word-break:break-all"><? echo $buyer_buyer; ?></td>
									                        <td style="word-break:break-all"><? echo $buyer_style_ref; ?></td>
									                        <td style="word-break:break-all"><? echo $buyer_po_no; ?></td>
									                        <td style="word-break:break-all"><? echo $color_arr[$color_id]; ?></td>
									                        <?
									                        $size_total_qty=0; $cut_nos=''; $cut_no=''; $bundle_dtls_id=''; $insert_date='';
										                    foreach ($all_size as $val)
															{ 
																$quantity=$wo_del_size_arr[$delivery_date][$party_id][$challan_no][$buyer_buyer][$buyer_style_ref][$buyer_po_no][$color_id][$val]['quantity'];
																$insert_date .=$wo_del_size_arr[$delivery_date][$party_id][$challan_no][$buyer_buyer][$buyer_style_ref][$buyer_po_no][$color_id][$val]['insert_date'].',';

																?>
																<td align="right" ><? echo $quantity; ?></td>
																<?
																$size_total_qty +=$quantity;
																$tot_size_arr[$val]+=$quantity;
																$quantity=0;
															}
															$insert_date=implode(",",array_unique(explode(",",(chop($insert_date,',')))));
															?>
									                        <td align="right" ><? echo $size_total_qty; ?></td>
									                        <td style="word-break:break-all" align="center"><? echo $insert_date; ?></td>
									                        <td style="word-break:break-all"></td>
									                    </tr>
														<?
														$i++;
													}
												}
											}
										}
									}
								}
							}
						}
	                	?>
	                </tbody>
	                <tfoot>
	                	<td colspan="9" align="right"><b>Total:</b></td>
	                	<?
	                    foreach ($all_size as $val)
						{ 
							$totalQuantity=$tot_size_arr[$val];
							$grand_fab_defect_qty+=$fab_defect_qty;
							$grand_print_defect_qty+=$print_defect_qty;
							$grandTotalQuantity+=$totalQuantity;
							?>
							<td align="right" ><strong><? echo $totalQuantity; ?></strong></td>
							
							<?
						}
						?>
	                    <td align="right" ><strong><? echo $grandTotalQuantity; ?></td>
	                    <td colspan="2" align="right" >&nbsp;</td>
	                </tfoot>
	            </table>
        </div>
	    </fieldset>
    </div> 
    <?
    exit(); 
}

if($action=="issue_popup")
{
	echo load_html_head_contents("Issue Details","../../../", 1, 1, $unicode);
	extract($_REQUEST);
    $ex_data 	= explode("_", $data);
    $job_no 	= $ex_data[0];
    $order_id 	= $ex_data[1];
    $within_group= $ex_data[2];
    $date_from 	= $ex_data[3];
    $date_to 	= $ex_data[4];
    $issue_ids = $ex_data[5];
    $color 		= $ex_data[6];
    $bodyPart 	= $ex_data[7];

    $date_cond	= "";
    if($date_from !="" && $date_to !="")
    {
    	$date_cond = " and a.delivery_date between '$date_from' and '$date_to' ";
    }
    if ($color!='') $sys_cond.=" and a.color_id=$color";
    if ($bodyPart!='') $sys_cond.=" and b.body_part=$bodyPart";
    
	$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
    $size_arr=return_library_array( "SELECT id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
    $buyer_arr = return_library_array("SELECT id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
    $floor_arr = return_library_array("SELECT id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and production_process=8", "id", "floor_name");
    $table_arr = return_library_array("SELECT id, table_name from lib_table_entry where status_active=1 and is_deleted=0", "id", "table_name");
	
	$order_cond = "";
   	$sql_wo_del="SELECT a.size_id,a.color_id, b.order_no, b.buyer_buyer, b.buyer_style_ref,b.buyer_po_no, b.job_no_mst, b.embl_type,b.main_process_id, c.party_id , d.id as del_dtls_id,d.mst_id,d.company_id,d.entry_form,d.wo_id,d.wo_dtls_id,d.wo_break_id,d.rcv_id,d.rcv_dtls_id,d.bundle_mst_id,d.bundle_dtls_id,d.quantity,d.issue_dtls_id,e.issue_number as challan_no, e.issue_date as delivery_date, e.remarks as mst_remarks,e.floor_id,e.table_id from subcon_ord_breakdown a, subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e where e.id=d.mst_id and e.id in ($issue_ids) and a.id=d.wo_break_id and a.mst_id=b.id and b.id=d.wo_dtls_id and a.job_no_mst=c.embellishment_job and d.wo_id=c.id and b.mst_id=c.id and d.entry_form=495 and e.entry_form=495 and d.mst_id in ($issue_ids) and a.status_active =1 and a.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0 $sys_cond";
	$sql_wo_del_res=sql_select($sql_wo_del);
	foreach ($sql_wo_del_res as $row)
	{ 
		if($row[csf("size_id")]!="") $all_sizes.=$row[csf("size_id")].',';
		if($row[csf("party_id")]!="") $all_party_id.=$row[csf("party_id")].',';
		if($row[csf("bundle_dtls_id")]!="") $all_bundle_dtls_ids.=$row[csf("bundle_dtls_id")].',';
		if($row[csf("issue_dtls_id")]!="") $all_issue_dtls_ids.=$row[csf("issue_dtls_id")].',';
		$wo_del_arr[$row[csf("delivery_date")]][$row[csf("table_id")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['quantity'] +=$row[csf("quantity")];
		$wo_del_arr[$row[csf("delivery_date")]][$row[csf("table_id")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['size_id'] .=$row[csf("size_id")].',';
		$wo_del_arr[$row[csf("delivery_date")]][$row[csf("table_id")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['issue_dtls_id'] .=$row[csf("issue_dtls_id")].',';
		
		$wo_del_size_arr[$row[csf("delivery_date")]][$row[csf("table_id")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['quantity'] +=$row[csf("quantity")];
		$wo_del_size_arr[$row[csf("delivery_date")]][$row[csf("table_id")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['bundle_dtls_id'] .=$row[csf("bundle_dtls_id")].',';
		$wo_del_size_arr[$row[csf("delivery_date")]][$row[csf("table_id")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['issue_dtls_id'] .=$row[csf("issue_dtls_id")].',';
	}
	$all_size=array_unique(explode(",",chop($all_sizes,',')));

    $width=970+(count($all_size)*160);
	$mst_width=$width-810;
	$top_width=$width-400;
	$width_px=$width.'px';
	?>    
    <div id="data_panel" align="center" style="width:100%">
       	<fieldset style="width: 98%">
	    <legend> Issue Quantity</legend>
	     	<div style="width:100%;">
	            <table align="center" cellspacing="0" width="<? echo $width; ?>" border="1" rules="all" class="rpt_table" style="font-size:12px">
	                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
	                    <!-- Delivery Date	Customer	Challan	Buyer	Style	Job No	Color -->
	                    <th width="30">SL</th>
	                    <th width="120">Issue Date</th>
	                    <th width="50">Table</th>
	                    <th width="100">Customer</th>
	                    <th width="100">Challan/ Issue ID</th>
	                    <th width="120">Buyer</th>
	                    <th width="100">Style</th>
	                    <th width="100">Buyer PO</th>
	                    <th width="100">Color</th>
	                    <?
	                    foreach ($all_size as $val)
						{ 
							?>
							<th width="100"><? echo $size_arr[$val]; ?></th>
							<?
						}
					    ?>
                    <th width="100">Total</th>
                    <th>Remarks</th>
	                </thead>
	                <tbody>
	                	<?
	                	if(count($wo_del_arr)>0)
				    	{ 
				    		//$wo_del_arr[$row[csf("delivery_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]
				    		$i=1; $tot_size_arr=array();
				    		foreach ($wo_del_arr as $delivery_date => $delivery_date_data ) 
							{
								foreach ($delivery_date_data as $table_id => $table_id_data ) 
								{
									foreach ($table_id_data as $party_id => $party_id_data ) 
									{
										foreach ($party_id_data as $challan_no => $challan_no_data ) 
										{
											foreach ($challan_no_data as $buyer_buyer => $buyer_buyer_data ) 
											{
												foreach ($buyer_buyer_data as $buyer_style_ref => $buyer_style_ref_data ) 
												{
													foreach ($buyer_style_ref_data as $buyer_po_no => $buyer_po_no_data ) 
													{
														foreach ($buyer_po_no_data as $color_id => $row ) 
														{
															//echo $row['size_id'].'=='; 
															//$all_del_sizes=array_unique(explode(",",chop($row['size_id'],',')));
															if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
															?>
										                    <tr bgcolor="<? echo $bgcolor; ?>">
										                        <td><? echo $i; ?></td>
										                        <td style="word-break:break-all"><? echo $delivery_date ?></td>
										                        <td style="word-break:break-all"><? echo $table_arr[$table_id]; ?></td>
										                        <td style="word-break:break-all"><? echo $buyer_arr[$party_id]; ?></td>
										                        <td style="word-break:break-all"><? echo $challan_no; ?></td>
										                        <td style="word-break:break-all"><? echo $buyer_buyer; ?></td>
										                        <td style="word-break:break-all"><? echo $buyer_style_ref; ?></td>
										                        <td style="word-break:break-all"><? echo $buyer_po_no; ?></td>
										                        <td style="word-break:break-all"><? echo $color_arr[$color_id]; ?></td>
										                        <?
										                        $size_total_qty=0; $cut_nos=''; $cut_no=''; $bundle_dtls_id='';
											                    foreach ($all_size as $val)
																{ 
																	$quantity=$wo_del_size_arr[$delivery_date][$table_id][$party_id][$challan_no][$buyer_buyer][$buyer_style_ref][$buyer_po_no][$color_id][$val]['quantity'];

																	?>
																	<td align="right" ><? echo $quantity; ?></td>
																	<?
																	$size_total_qty +=$quantity;
																	$tot_size_arr[$val]+=$quantity;
																	$quantity=0;
																}
																?>
										                        <td align="right" ><? echo $size_total_qty; ?></td>
										                        <td style="word-break:break-all"></td>
										                    </tr>
															<?
															$i++;
														}
													}
												}
											}
										}
									}
								}
							}
						}
	                	?>
	                </tbody>
	                <tfoot>
	                	<td colspan="9" align="right"><b>Total:</b></td>
	                	<?
	                    foreach ($all_size as $val)
						{ 
							$totalQuantity=$tot_size_arr[$val];
							$grand_fab_defect_qty+=$fab_defect_qty;
							$grand_print_defect_qty+=$print_defect_qty;
							$grandTotalQuantity+=$totalQuantity;
							?>
							<td align="right" ><strong><? echo $totalQuantity; ?></strong></td>
							
							<?
						}
						?>
	                    <td align="right" ><strong><? echo $grandTotalQuantity; ?></td>
	                    <td align="right" >&nbsp;</td>
	                </tfoot>
	            </table>
        </div>
	    </fieldset>
    </div> 
    <?
    exit(); 
}

if($action=="production_popup")
{
	echo load_html_head_contents("Production Details","../../../", 1, 1, $unicode);
	extract($_REQUEST);
    $ex_data 	= explode("_", $data);
    $job_no 	= $ex_data[0];
    $order_id 	= $ex_data[1];
    $within_group= $ex_data[2];
    $date_from 	= $ex_data[3];
    $date_to 	= $ex_data[4];
    $production_ids = $ex_data[5];
    $color 		= $ex_data[6];
    $bodyPart 	= $ex_data[7];

    $date_cond	= "";
    if($date_from !="" && $date_to !="")
    {
    	$date_cond = " and a.delivery_date between '$date_from' and '$date_to' ";
    }
    if ($color!='') $sys_cond.=" and a.color_id=$color";
    if ($bodyPart!='') $sys_cond.=" and b.body_part=$bodyPart";
    
	$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
    $size_arr=return_library_array( "SELECT id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
    $buyer_arr = return_library_array("SELECT id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
    $floor_arr = return_library_array("SELECT id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and production_process=8", "id", "floor_name");
    $table_arr = return_library_array("SELECT id, table_name from lib_table_entry where status_active=1 and is_deleted=0", "id", "table_name");
	
	$order_cond = "";
   	$sql_wo_del="SELECT a.size_id,a.color_id, b.order_no, b.buyer_buyer, b.buyer_style_ref,b.buyer_po_no, b.job_no_mst, b.embl_type,b.main_process_id, c.party_id , d.id as del_dtls_id,d.mst_id,d.company_id,d.entry_form,d.wo_id,d.wo_dtls_id,d.wo_break_id,d.rcv_id,d.rcv_dtls_id,d.bundle_mst_id,d.bundle_dtls_id,d.quantity,d.issue_dtls_id,e.issue_number as challan_no, e.issue_date as delivery_date, e.remarks as mst_remarks,e.floor_id,e.table_id from subcon_ord_breakdown a, subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e where e.id=d.mst_id and e.id in ($production_ids) and a.id=d.wo_break_id and a.mst_id=b.id and b.id=d.wo_dtls_id and a.job_no_mst=c.embellishment_job and d.wo_id=c.id and b.mst_id=c.id and d.entry_form=497 and e.entry_form=497 and d.mst_id in ($production_ids) and a.status_active =1 and a.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0 $sys_cond";
	$sql_wo_del_res=sql_select($sql_wo_del);
	foreach ($sql_wo_del_res as $row)
	{ 
		if($row[csf("size_id")]!="") $all_sizes.=$row[csf("size_id")].',';
		if($row[csf("party_id")]!="") $all_party_id.=$row[csf("party_id")].',';
		if($row[csf("bundle_dtls_id")]!="") $all_bundle_dtls_ids.=$row[csf("bundle_dtls_id")].',';
		if($row[csf("issue_dtls_id")]!="") $all_issue_dtls_ids.=$row[csf("issue_dtls_id")].',';
		$wo_del_arr[$row[csf("delivery_date")]][$row[csf("floor_id")]][$row[csf("table_id")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['quantity'] +=$row[csf("quantity")];
		$wo_del_arr[$row[csf("delivery_date")]][$row[csf("floor_id")]][$row[csf("table_id")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['size_id'] .=$row[csf("size_id")].',';
		$wo_del_arr[$row[csf("delivery_date")]][$row[csf("floor_id")]][$row[csf("table_id")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['issue_dtls_id'] .=$row[csf("issue_dtls_id")].',';
		
		$wo_del_size_arr[$row[csf("delivery_date")]][$row[csf("floor_id")]][$row[csf("table_id")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['quantity'] +=$row[csf("quantity")];
		$wo_del_size_arr[$row[csf("delivery_date")]][$row[csf("floor_id")]][$row[csf("table_id")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['bundle_dtls_id'] .=$row[csf("bundle_dtls_id")].',';
		$wo_del_size_arr[$row[csf("delivery_date")]][$row[csf("floor_id")]][$row[csf("table_id")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['issue_dtls_id'] .=$row[csf("issue_dtls_id")].',';
	}
	$all_size=array_unique(explode(",",chop($all_sizes,',')));

    $width=970+(count($all_size)*160);
	$mst_width=$width-810;
	$top_width=$width-400;
	$width_px=$width.'px';
	?>    
    <div id="data_panel" align="center" style="width:100%">
       	<fieldset style="width: 98%">
	    <legend> Production Quantity</legend>
	     	<div style="width:100%;">
	            <table align="center" cellspacing="0" width="<? echo $width; ?>" border="1" rules="all" class="rpt_table" style="font-size:12px">
	                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
	                    <!-- Delivery Date	Customer	Challan	Buyer	Style	Job No	Color -->
	                    <th width="30">SL</th>
	                    <th width="120">Production Date</th>
	                    <th width="50">Floor</th>
	                    <th width="50">Table</th>
	                    <th width="100">Customer</th>
	                    <th width="100">Challan</th>
	                    <th width="120">Buyer</th>
	                    <th width="100">Style</th>
	                    <th width="100">Buyer PO</th>
	                    <th width="100">Color</th>
	                    <?
	                    foreach ($all_size as $val)
						{ 
							?>
							<th width="100"><? echo $size_arr[$val]; ?></th>
							<?
						}
					    ?>
                    <th width="100">Total</th>
                    <th>Remarks</th>
	                </thead>
	                <tbody>
	                	<?
	                	if(count($wo_del_arr)>0)
				    	{ 
				    		//$wo_del_arr[$row[csf("delivery_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]
				    		$i=1; $tot_size_arr=array();
				    		foreach ($wo_del_arr as $delivery_date => $delivery_date_data ) 
							{
								foreach ($delivery_date_data as $floor_id => $floor_id_data ) 
								{
									foreach ($floor_id_data as $table_id => $table_id_data ) 
									{
										foreach ($table_id_data as $party_id => $party_id_data ) 
										{
											foreach ($party_id_data as $challan_no => $challan_no_data ) 
											{
												foreach ($challan_no_data as $buyer_buyer => $buyer_buyer_data ) 
												{
													foreach ($buyer_buyer_data as $buyer_style_ref => $buyer_style_ref_data ) 
													{
														foreach ($buyer_style_ref_data as $buyer_po_no => $buyer_po_no_data ) 
														{
															foreach ($buyer_po_no_data as $color_id => $row ) 
															{
																//echo $row['size_id'].'=='; 
																//$all_del_sizes=array_unique(explode(",",chop($row['size_id'],',')));
																if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
																?>
											                    <tr bgcolor="<? echo $bgcolor; ?>">
											                        <td><? echo $i; ?></td>
											                        <td style="word-break:break-all"><? echo $delivery_date ?></td>
											                        <td style="word-break:break-all"><? echo $floor_arr[$floor_id]; ?></td>
											                        <td style="word-break:break-all"><? echo $table_arr[$table_id]; ?></td>
											                        <td style="word-break:break-all"><? echo $buyer_arr[$party_id]; ?></td>
											                        <td style="word-break:break-all"><? echo $challan_no; ?></td>
											                        <td style="word-break:break-all"><? echo $buyer_buyer; ?></td>
											                        <td style="word-break:break-all"><? echo $buyer_style_ref; ?></td>
											                        <td style="word-break:break-all"><? echo $buyer_po_no; ?></td>
											                        <td style="word-break:break-all"><? echo $color_arr[$color_id]; ?></td>
											                        <?
											                        $size_total_qty=0; $cut_nos=''; $cut_no=''; $bundle_dtls_id='';
												                    foreach ($all_size as $val)
																	{ 
																		$quantity=$wo_del_size_arr[$delivery_date][$floor_id][$table_id][$party_id][$challan_no][$buyer_buyer][$buyer_style_ref][$buyer_po_no][$color_id][$val]['quantity'];

																		?>
																		<td align="right" ><? echo $quantity; ?></td>
																		<?
																		$size_total_qty +=$quantity;
																		$tot_size_arr[$val]+=$quantity;
																		$quantity=0;
																	}
																	?>
											                        <td align="right" ><? echo $size_total_qty; ?></td>
											                        <td style="word-break:break-all"></td>
											                    </tr>
																<?
																$i++;
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
	                	?>
	                </tbody>
	                <tfoot>
	                	<td colspan="10" align="right"><b>Total:</b></td>
	                	<?
	                    foreach ($all_size as $val)
						{ 
							$totalQuantity=$tot_size_arr[$val];
							$grand_fab_defect_qty+=$fab_defect_qty;
							$grand_print_defect_qty+=$print_defect_qty;
							$grandTotalQuantity+=$totalQuantity;
							?>
							<td align="right" ><strong><? echo $totalQuantity; ?></strong></td>
							
							<?
						}
						?>
	                    <td align="right" ><strong><? echo $grandTotalQuantity; ?></td>
	                    <td align="right" >&nbsp;</td>
	                </tfoot>
	            </table>
        </div>
	    </fieldset>
    </div> 
    <?
    exit(); 
}

if($action=="qc_popup")
{
	echo load_html_head_contents("QC Details","../../../", 1, 1, $unicode);
	extract($_REQUEST);
    $ex_data 	= explode("_", $data);
    $job_no 	= $ex_data[0];
    $order_id 	= $ex_data[1];
    $within_group= $ex_data[2];
    $date_from 	= $ex_data[3];
    $date_to 	= $ex_data[4];
    $qc_ids = $ex_data[5];
    $color 		= $ex_data[6];
    $bodyPart 	= $ex_data[7];

    $date_cond	= "";
    if($date_from !="" && $date_to !="")
    {
    	$date_cond = " and a.delivery_date between '$date_from' and '$date_to' ";
    }
    if ($color!='') $sys_cond.=" and a.color_id=$color";
    if ($bodyPart!='') $sys_cond.=" and b.body_part=$bodyPart";
    
	$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
    $size_arr=return_library_array( "SELECT id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
    $buyer_arr = return_library_array("SELECT id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
    $floor_arr = return_library_array("SELECT id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and production_process=8", "id", "floor_name");
    $table_arr = return_library_array("SELECT id, table_name from lib_table_entry where status_active=1 and is_deleted=0", "id", "table_name");
	
	$order_cond = "";
   	$sql_wo_del="SELECT a.size_id,a.color_id, b.order_no, b.buyer_buyer, b.buyer_style_ref,b.buyer_po_no, b.job_no_mst, b.embl_type,b.main_process_id, c.party_id , d.id as del_dtls_id,d.mst_id,d.company_id,d.entry_form,d.wo_id,d.wo_dtls_id,d.wo_break_id,d.rcv_id,d.rcv_dtls_id,d.bundle_mst_id,d.bundle_dtls_id,d.quantity,d.issue_dtls_id,e.issue_number as challan_no, e.issue_date as delivery_date, e.remarks as mst_remarks,e.floor_id,e.table_id from subcon_ord_breakdown a, subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e where e.id=d.mst_id and e.id in ($qc_ids) and a.id=d.wo_break_id and a.mst_id=b.id and b.id=d.wo_dtls_id and a.job_no_mst=c.embellishment_job and d.wo_id=c.id and b.mst_id=c.id and d.entry_form=498 and e.entry_form=498 and d.mst_id in ($qc_ids) and a.status_active =1 and a.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0 $sys_cond";
	$sql_wo_del_res=sql_select($sql_wo_del);
	foreach ($sql_wo_del_res as $row)
	{ 
		if($row[csf("size_id")]!="") $all_sizes.=$row[csf("size_id")].',';
		if($row[csf("party_id")]!="") $all_party_id.=$row[csf("party_id")].',';
		if($row[csf("bundle_dtls_id")]!="") $all_bundle_dtls_ids.=$row[csf("bundle_dtls_id")].',';
		if($row[csf("issue_dtls_id")]!="") $all_issue_dtls_ids.=$row[csf("issue_dtls_id")].',';
		$wo_del_arr[$row[csf("delivery_date")]][$row[csf("floor_id")]][$row[csf("table_id")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['quantity'] +=$row[csf("quantity")];
		$wo_del_arr[$row[csf("delivery_date")]][$row[csf("floor_id")]][$row[csf("table_id")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['size_id'] .=$row[csf("size_id")].',';
		$wo_del_arr[$row[csf("delivery_date")]][$row[csf("floor_id")]][$row[csf("table_id")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['issue_dtls_id'] .=$row[csf("issue_dtls_id")].',';
		
		$wo_del_size_arr[$row[csf("delivery_date")]][$row[csf("floor_id")]][$row[csf("table_id")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['quantity'] +=$row[csf("quantity")];
		$wo_del_size_arr[$row[csf("delivery_date")]][$row[csf("floor_id")]][$row[csf("table_id")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['bundle_dtls_id'] .=$row[csf("bundle_dtls_id")].',';
		$wo_del_size_arr[$row[csf("delivery_date")]][$row[csf("floor_id")]][$row[csf("table_id")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['issue_dtls_id'] .=$row[csf("issue_dtls_id")].',';
	}
	$all_size=array_unique(explode(",",chop($all_sizes,',')));

    $width=970+(count($all_size)*160);
	$mst_width=$width-810;
	$top_width=$width-400;
	$width_px=$width.'px';
	?>    
    <div id="data_panel" align="center" style="width:100%">
       	<fieldset style="width: 98%">
	    <legend> QC Quantity</legend>
	     	<div style="width:100%;">
	            <table align="center" cellspacing="0" width="<? echo $width; ?>" border="1" rules="all" class="rpt_table" style="font-size:12px">
	                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
	                    <!-- Delivery Date	Customer	Challan	Buyer	Style	Job No	Color -->
	                    <th width="30">SL</th>
	                    <th width="120">QC Date</th>
	                    <th width="50">Floor</th>
	                    <th width="50">Table</th>
	                    <th width="100">Customer</th>
	                    <th width="100">Challan</th>
	                    <th width="120">Buyer</th>
	                    <th width="100">Style</th>
	                    <th width="100">Buyer PO</th>
	                    <th width="100">Color</th>
	                    <?
	                    foreach ($all_size as $val)
						{ 
							?>
							<th width="100"><? echo $size_arr[$val]; ?></th>
							<?
						}
					    ?>
                    <th width="100">Total</th>
                    <th>Remarks</th>
	                </thead>
	                <tbody>
	                	<?
	                	if(count($wo_del_arr)>0)
				    	{ 
				    		//$wo_del_arr[$row[csf("delivery_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]
				    		$i=1; $tot_size_arr=array();
				    		foreach ($wo_del_arr as $delivery_date => $delivery_date_data ) 
							{
								foreach ($delivery_date_data as $floor_id => $floor_id_data ) 
								{
									foreach ($floor_id_data as $table_id => $table_id_data ) 
									{
										foreach ($table_id_data as $party_id => $party_id_data ) 
										{
											foreach ($party_id_data as $challan_no => $challan_no_data ) 
											{
												foreach ($challan_no_data as $buyer_buyer => $buyer_buyer_data ) 
												{
													foreach ($buyer_buyer_data as $buyer_style_ref => $buyer_style_ref_data ) 
													{
														foreach ($buyer_style_ref_data as $buyer_po_no => $buyer_po_no_data ) 
														{
															foreach ($buyer_po_no_data as $color_id => $row ) 
															{
																//echo $row['size_id'].'=='; 
																//$all_del_sizes=array_unique(explode(",",chop($row['size_id'],',')));
																if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
																?>
											                    <tr bgcolor="<? echo $bgcolor; ?>">
											                        <td><? echo $i; ?></td>
											                        <td style="word-break:break-all"><? echo $delivery_date ?></td>
											                        <td style="word-break:break-all"><? echo $floor_arr[$floor_id]; ?></td>
											                        <td style="word-break:break-all"><? echo $table_arr[$table_id]; ?></td>
											                        <td style="word-break:break-all"><? echo $buyer_arr[$party_id]; ?></td>
											                        <td style="word-break:break-all"><? echo $challan_no; ?></td>
											                        <td style="word-break:break-all"><? echo $buyer_buyer; ?></td>
											                        <td style="word-break:break-all"><? echo $buyer_style_ref; ?></td>
											                        <td style="word-break:break-all"><? echo $buyer_po_no; ?></td>
											                        <td style="word-break:break-all"><? echo $color_arr[$color_id]; ?></td>
											                        <?
											                        $size_total_qty=0; $cut_nos=''; $cut_no=''; $bundle_dtls_id='';
												                    foreach ($all_size as $val)
																	{ 
																		$quantity=$wo_del_size_arr[$delivery_date][$floor_id][$table_id][$party_id][$challan_no][$buyer_buyer][$buyer_style_ref][$buyer_po_no][$color_id][$val]['quantity'];

																		?>
																		<td align="right" ><? echo $quantity; ?></td>
																		<?
																		$size_total_qty +=$quantity;
																		$tot_size_arr[$val]+=$quantity;
																		$quantity=0;
																	}
																	?>
											                        <td align="right" ><? echo $size_total_qty; ?></td>
											                        <td style="word-break:break-all"></td>
											                    </tr>
																<?
																$i++;
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
	                	?>
	                </tbody>
	                <tfoot>
	                	<td colspan="10" align="right"><b>Total:</b></td>
	                	<?
	                    foreach ($all_size as $val)
						{ 
							$totalQuantity=$tot_size_arr[$val];
							$grand_fab_defect_qty+=$fab_defect_qty;
							$grand_print_defect_qty+=$print_defect_qty;
							$grandTotalQuantity+=$totalQuantity;
							?>
							<td align="right" ><strong><? echo $totalQuantity; ?></strong></td>
							
							<?
						}
						?>
	                    <td align="right" ><strong><? echo $grandTotalQuantity; ?></td>
	                    <td align="right" >&nbsp;</td>
	                </tfoot>
	            </table>
        </div>
	    </fieldset>
    </div> 
    <?
    exit(); 
}

if($action=="reject_popup")
{
	echo load_html_head_contents("Reject Details","../../../", 1, 1, $unicode);
	extract($_REQUEST);
    $ex_data 	= explode("_", $data);
    $job_no 	= $ex_data[0];
    $order_id 	= $ex_data[1];
    $within_group= $ex_data[2];
    $date_from 	= $ex_data[3];
    $date_to 	= $ex_data[4];
    $reject_ids = $ex_data[5];
    $color 		= $ex_data[6];
    $bodyPart 	= $ex_data[7];

    $date_cond	= "";
    if($date_from !="" && $date_to !="")
    {
    	$date_cond = " and a.delivery_date between '$date_from' and '$date_to' ";
    }
    if ($color!='') $sys_cond.=" and a.color_id=$color";
    if ($bodyPart!='') $sys_cond.=" and b.body_part=$bodyPart";
    
	$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
    $size_arr=return_library_array( "SELECT id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
    $buyer_arr = return_library_array("SELECT id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
    $floor_arr = return_library_array("SELECT id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and production_process=8", "id", "floor_name");
    $table_arr = return_library_array("SELECT id, table_name from lib_table_entry where status_active=1 and is_deleted=0", "id", "table_name");
	
	$order_cond = "";
   	$sql_wo_del="SELECT a.size_id,a.color_id, b.order_no, b.buyer_buyer, b.buyer_style_ref,b.buyer_po_no, b.job_no_mst, b.embl_type,b.main_process_id, c.party_id , d.id as del_dtls_id,d.mst_id,d.company_id,d.entry_form,d.wo_id,d.wo_dtls_id,d.wo_break_id,d.rcv_id,d.rcv_dtls_id,d.bundle_mst_id,d.bundle_dtls_id,d.quantity,d.issue_dtls_id,e.issue_number as challan_no, e.issue_date as delivery_date, e.remarks as mst_remarks,e.floor_id,e.table_id,d.defect_qty from subcon_ord_breakdown a, subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e where e.id=d.mst_id and e.id in ($reject_ids) and a.id=d.wo_break_id and a.mst_id=b.id and b.id=d.wo_dtls_id and a.job_no_mst=c.embellishment_job and d.wo_id=c.id and b.mst_id=c.id and d.entry_form=498 and e.entry_form=498 and d.mst_id in ($reject_ids) and a.status_active =1 and a.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0 and d.defect_qty is not null $sys_cond";
	$sql_wo_del_res=sql_select($sql_wo_del);
	foreach ($sql_wo_del_res as $row)
	{ 
		if($row[csf("size_id")]!="") $all_sizes.=$row[csf("size_id")].',';
		if($row[csf("party_id")]!="") $all_party_id.=$row[csf("party_id")].',';
		if($row[csf("bundle_dtls_id")]!="") $all_bundle_dtls_ids.=$row[csf("bundle_dtls_id")].',';
		if($row[csf("issue_dtls_id")]!="") $all_issue_dtls_ids.=$row[csf("issue_dtls_id")].',';
		$wo_del_arr[$row[csf("delivery_date")]][$row[csf("floor_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['quantity'] +=$row[csf("quantity")];
		$wo_del_arr[$row[csf("delivery_date")]][$row[csf("floor_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['size_id'] .=$row[csf("size_id")].',';
		$wo_del_arr[$row[csf("delivery_date")]][$row[csf("floor_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['issue_dtls_id'] .=$row[csf("issue_dtls_id")].',';
		
		$defect_qty=$row[csf("defect_qty")];
		$indivisual_defect_qty=explode('_', $defect_qty);
		$wo_del_size_arr[$row[csf("delivery_date")]][$row[csf("floor_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['fab_reject'] +=$indivisual_defect_qty[0];
		$wo_del_size_arr[$row[csf("delivery_date")]][$row[csf("floor_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['print_reject'] +=$indivisual_defect_qty[1];
		$wo_del_size_arr[$row[csf("delivery_date")]][$row[csf("floor_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['part_short'] +=$indivisual_defect_qty[2];
	}
	/*echo '<pre>';
	print_r($wo_del_size_arr);*/
	$all_size=array_unique(explode(",",chop($all_sizes,',')));

    $width=970+(count($all_size)*50);
	$mst_width=$width-810;
	$top_width=$width-400;
	$width_px=$width.'px';
	?>    
    <div id="data_panel" align="center" style="width:100%">
       	<fieldset style="width: 98%">
	    <legend> Production Quantity</legend>
	     	<div style="width:100%;">
	            <table align="center" cellspacing="0" width="<? echo $width; ?>" border="1" rules="all" class="rpt_table" style="font-size:12px">
	                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
	                    <!-- Reject Date 	Floor	Customer	Buyer	Style	Job No	Color	Reject Status	L	M	S	XL	Total	Remarks -->
	                    <th width="30">SL</th>
	                    <th width="120">Reject Date</th>
	                    <th width="50">Floor</th>
	                    <th width="100">Customer</th>
	                    <th width="120">Buyer</th>
	                    <th width="100">Style</th>
	                    <th width="100">Buyer PO</th>
	                    <th width="100">Color</th>
	                    <th width="60">Reject Status</th>
	                    <?
	                    foreach ($all_size as $val)
						{ 
							?>
							<th width="50"><? echo $size_arr[$val]; ?></th>
							<?
						}
					    ?>
                    <th width="50">Total</th>
                    <th>Remarks</th>
	                </thead>
	                <tbody>
	                	<?
	                	if(count($wo_del_arr)>0)
				    	{ 
				    		//$wo_del_arr[$row[csf("delivery_date")]][$row[csf("floor_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['quantity'] +=$row[csf("quantity")];
				    		$i=1; $tot_size_arr=array();
				    		foreach ($wo_del_arr as $delivery_date => $delivery_date_data ) 
							{
								foreach ($delivery_date_data as $floor_id => $floor_id_data ) 
								{
									foreach ($floor_id_data as $party_id => $party_id_data ) 
									{
										foreach ($party_id_data as $buyer_buyer => $buyer_buyer_data ) 
										{
											foreach ($buyer_buyer_data as $buyer_style_ref => $buyer_style_ref_data ) 
											{
												foreach ($buyer_style_ref_data as $buyer_po_no => $buyer_po_no_data ) 
												{
													foreach ($buyer_po_no_data as $color_id => $row ) 
													{
														//echo $row['size_id'].'=='; 
														//$all_del_sizes=array_unique(explode(",",chop($row['size_id'],',')));
														if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
														?>
									                    <tr bgcolor="<? echo $bgcolor; ?>" >
									                        <td rowspan="3"><? echo $i; ?></td>
									                        <td rowspan="3" style="word-break:break-all"><? echo $delivery_date ?></td>
									                        <td rowspan="3" style="word-break:break-all"><? echo $floor_arr[$floor_id]; ?></td>
									                        <td rowspan="3" style="word-break:break-all"><? echo $buyer_arr[$party_id]; ?></td>
									                        <td rowspan="3" style="word-break:break-all"><? echo $buyer_buyer; ?></td>
									                        <td rowspan="3" style="word-break:break-all"><? echo $buyer_style_ref; ?></td>
									                        <td rowspan="3" style="word-break:break-all"><? echo $buyer_po_no; ?></td>
									                        <td rowspan="3" style="word-break:break-all"><? echo $color_arr[$color_id]; ?></td>
									                        <td style="word-break:break-all"><strong>Fabric Reject</strong></td>
									                        <?
									                        $size_total_qty=0; $cut_nos=''; $cut_no=''; $bundle_dtls_id='';
										                    foreach ($all_size as $val)
															{ 
																$fab_reject=$wo_del_size_arr[$delivery_date][$floor_id][$party_id][$buyer_buyer][$buyer_style_ref][$buyer_po_no][$color_id][$val]['fab_reject'];
																?>
																<td align="right" ><? echo $fab_reject; ?></td>
																<?
																$size_total_qty +=$fab_reject;
																$tot_size_arr[$val]+=$fab_reject;
																$fab_reject=0;
															}
															?>
															<td align="right" ><? echo $size_total_qty; ?></td>
									                        <td style="word-break:break-all"></td>
									                    </tr>
									                    <tr>
									                        <td style="word-break:break-all"><strong>Print Reject</strong></td>
									                        <?
									                        $size_total_qty=0; $cut_nos=''; $cut_no=''; $bundle_dtls_id='';
										                    foreach ($all_size as $val)
															{ 
																$print_reject=$wo_del_size_arr[$delivery_date][$floor_id][$party_id][$buyer_buyer][$buyer_style_ref][$buyer_po_no][$color_id][$val]['print_reject'];
																?>
																<td align="right" ><? echo $print_reject; ?></td>
																<?
																$size_total_qty +=$print_reject;
																$tot_size_arr[$val]+=$print_reject;
																$print_reject=0;
															}
															?>
															<td align="right" ><? echo $size_total_qty; ?></td>
									                        <td style="word-break:break-all"></td>
									                    </tr>
									                    <tr>
									                        <td style="word-break:break-all"><strong>Part Short</strong></td>
									                        <?
									                        $size_total_qty=0; $cut_nos=''; $cut_no=''; $bundle_dtls_id='';
										                    foreach ($all_size as $val)
															{ 
																$part_short=$wo_del_size_arr[$delivery_date][$floor_id][$party_id][$buyer_buyer][$buyer_style_ref][$buyer_po_no][$color_id][$val]['part_short'];
																?>
																<td align="right" ><? echo $part_short; ?></td>
																<?
																$size_total_qty +=$part_short;
																$tot_size_arr[$val]+=$part_short;
																$part_short=0;
															}
															?>
															<td align="right" ><? echo $size_total_qty; ?></td>
									                        <td style="word-break:break-all"></td>
									                    </tr>
														<?
														$i++;
													}
												}
											}
										}
									}
								}
							}
						}
	                	?>
	                </tbody>
	                <tfoot>
	                	<td colspan="9" align="right"><b>Total:</b></td>
	                	<?
	                    foreach ($all_size as $val)
						{ 
							$totalQuantity=$tot_size_arr[$val];
							$grand_fab_defect_qty+=$fab_defect_qty;
							$grand_print_defect_qty+=$print_defect_qty;
							$grandTotalQuantity+=$totalQuantity;
							?>
							<td align="right" ><strong><? echo $totalQuantity; ?></strong></td>
							
							<?
						}
						?>
	                    <td align="right" ><strong><? echo $grandTotalQuantity; ?></td>
	                    <td align="right" >&nbsp;</td>
	                </tfoot>
	            </table>
        </div>
	    </fieldset>
    </div> 
    <?
    exit(); 
}
if($action=="production_bal_popup")
{
	echo load_html_head_contents("Production Details","../../../", 1, 1, $unicode);
	extract($_REQUEST);
    $ex_data 	= explode("_", $data);
    $job_no 	= $ex_data[0];
    $order_id 	= $ex_data[1];
    $within_group= $ex_data[2];
    $date_from 	= $ex_data[3];
    $date_to 	= $ex_data[4];
    $production_ids = $ex_data[5];
    $color 		= $ex_data[6];
    $bodyPart 	= $ex_data[7];
    $issue_ids 	= $ex_data[8];

    $date_cond	= "";
    if($date_from !="" && $date_to !="")
    {
    	$date_cond = " and a.delivery_date between '$date_from' and '$date_to' ";
    }
    if ($color!='') $sys_cond.=" and a.color_id=$color";
    if ($bodyPart!='') $sys_cond.=" and b.body_part=$bodyPart";
    
	$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
    $size_arr=return_library_array( "SELECT id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
    $buyer_arr = return_library_array("SELECT id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
    $floor_arr = return_library_array("SELECT id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and production_process=8", "id", "floor_name");
    $table_arr = return_library_array("SELECT id, table_name from lib_table_entry where status_active=1 and is_deleted=0", "id", "table_name");
	
	$order_cond = "";
	$sql_wo_del="(SELECT a.size_id,a.color_id, b.order_no, b.buyer_buyer, b.buyer_style_ref,b.buyer_po_no, b.job_no_mst, b.embl_type,b.main_process_id, c.party_id , d.id as del_dtls_id,d.mst_id,d.company_id,d.entry_form,d.wo_id,d.wo_dtls_id,d.wo_break_id,d.rcv_id,d.rcv_dtls_id,d.bundle_mst_id,d.bundle_dtls_id,d.quantity,d.issue_dtls_id,e.issue_number as challan_no, e.issue_date as delivery_date, e.remarks as mst_remarks,e.floor_id,e.table_id, 1 as type from subcon_ord_breakdown a, subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e where e.id=d.mst_id and e.id in ($production_ids) and a.id=d.wo_break_id and a.mst_id=b.id and b.id=d.wo_dtls_id and a.job_no_mst=c.embellishment_job and d.wo_id=c.id and b.mst_id=c.id and d.entry_form=497 and e.entry_form=497 and d.mst_id in ($production_ids) and a.status_active =1 and a.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0 $sys_cond)
	UNION ALL
	(SELECT a.size_id,a.color_id, b.order_no, b.buyer_buyer, b.buyer_style_ref,b.buyer_po_no, b.job_no_mst, b.embl_type,b.main_process_id, c.party_id , d.id as del_dtls_id,d.mst_id,d.company_id,d.entry_form,d.wo_id,d.wo_dtls_id,d.wo_break_id,d.rcv_id,d.rcv_dtls_id,d.bundle_mst_id,d.bundle_dtls_id,d.quantity,d.issue_dtls_id,e.issue_number as challan_no, e.issue_date as delivery_date, e.remarks as mst_remarks,e.floor_id,e.table_id, 2 as type from subcon_ord_breakdown a, subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e where e.id=d.mst_id and e.id in ($issue_ids) and a.id=d.wo_break_id and a.mst_id=b.id and b.id=d.wo_dtls_id and a.job_no_mst=c.embellishment_job and d.wo_id=c.id and b.mst_id=c.id and d.entry_form=495 and e.entry_form=495 and d.mst_id in ($issue_ids) and a.status_active =1 and a.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0 $sys_cond) order by party_id ASC";

	$sql_wo_del_res=sql_select($sql_wo_del);
	foreach ($sql_wo_del_res as $row)
	{ 
		if($row[csf("size_id")]!="") $all_sizes.=$row[csf("size_id")].',';
		if($row[csf("party_id")]!="") $all_party_id.=$row[csf("party_id")].',';
		if($row[csf("bundle_dtls_id")]!="") $all_bundle_dtls_ids.=$row[csf("bundle_dtls_id")].',';
		if($row[csf("issue_dtls_id")]!="") $all_issue_dtls_ids.=$row[csf("issue_dtls_id")].',';
		$wo_del_arr[$row[csf("party_id")]][$row[csf("table_id")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['quantity'] +=$row[csf("quantity")];
		$wo_del_arr[$row[csf("party_id")]][$row[csf("table_id")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['size_id'] .=$row[csf("size_id")].',';
		
		$wo_del_size_arr[$row[csf("party_id")]][$row[csf("table_id")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("type")]]['quantity'] +=$row[csf("quantity")];
	}
	/*echo '<pre>';
	print_r($wo_del_size_arr);*/
	$all_size=array_unique(explode(",",chop($all_sizes,',')));

    $width=970+(count($all_size)*160);
	$mst_width=$width-810;
	$top_width=$width-400;
	$width_px=$width.'px';
	?>    
    <div id="data_panel" align="center" style="width:100%">
       	<fieldset style="width: 98%">
	    <legend> Production Quantity</legend>
	     	<div style="width:100%;">
	            <table align="center" cellspacing="0" width="<? echo $width; ?>" border="1" rules="all" class="rpt_table" style="font-size:12px">
	                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
	                    <!-- Delivery Date	Customer	Challan	Buyer	Style	Job No	Color -->
	                    <th width="30">SL</th>
	                    <th width="100">Customer</th>
	                    <th width="100">Table</th>
	                    <th width="120">Buyer</th>
	                    <th width="100">Style</th>
	                    <th width="100">Buyer PO</th>
	                    <th width="100">Color</th>
	                    <?
	                    foreach ($all_size as $val)
						{ 
							?>
							<th width="100"><? echo $size_arr[$val]; ?></th>
							<?
						}
					    ?>
                    <th width="100">Total</th>
                    <th>Remarks</th>
	                </thead>
	                <tbody>
	                	<?
	                	if(count($wo_del_arr)>0)
				    	{ 
				    		//[$row[csf("party_id")]][$row[csf("table_id")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]
				    		$i=1; $tot_size_arr=array();
							foreach ($wo_del_arr as $party_id => $party_id_data ) 
							{
								foreach ($party_id_data as $table_id => $table_id_data ) 
								{
									foreach ($table_id_data as $buyer_buyer => $buyer_buyer_data ) 
									{
										foreach ($buyer_buyer_data as $buyer_style_ref => $buyer_style_ref_data ) 
										{
											foreach ($buyer_style_ref_data as $buyer_po_no => $buyer_po_no_data ) 
											{
												foreach ($buyer_po_no_data as $color_id => $row ) 
												{
													//echo $row['size_id'].'=='; 
													//$all_del_sizes=array_unique(explode(",",chop($row['size_id'],',')));
													if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													?>
								                    <tr bgcolor="<? echo $bgcolor; ?>">
								                        <td><? echo $i; ?></td>
								                        <td style="word-break:break-all"><? echo $buyer_arr[$party_id]; ?></td>
								                        <td style="word-break:break-all"><? echo $table_arr[$table_id]; ?></td>
								                        <td style="word-break:break-all"><? echo $buyer_buyer; ?></td>
								                        <td style="word-break:break-all"><? echo $buyer_style_ref; ?></td>
								                        <td style="word-break:break-all"><? echo $buyer_po_no; ?></td>
								                        <td style="word-break:break-all"><? echo $color_arr[$color_id]; ?></td>
								                        <?
								                        $size_total_qty=0; $cut_nos=''; $cut_no=''; $bundle_dtls_id='';
									                    foreach ($all_size as $val)
														{ 
															//[$row[csf("party_id")]][$row[csf("table_id")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("type")]];
															//echo $party_id.'='.$table_id.'='.$buyer_buyer.'='.$buyer_style_ref.'='.$buyer_po_no.'='.$color_id.'='.$val;
															$prod_quantity=$wo_del_size_arr[$party_id][$table_id][$buyer_buyer][$buyer_style_ref][$buyer_po_no][$color_id][$val][1]['quantity'];
															$issue_quantity=$wo_del_size_arr[$party_id][$table_id][$buyer_buyer][$buyer_style_ref][$buyer_po_no][$color_id][$val][2]['quantity'];
															$prod_bal_qty=$issue_quantity-$prod_quantity;
															?>
															<td align="right" ><? echo $prod_bal_qty; ?></td>
															<?
															$size_total_qty +=$prod_bal_qty;
															$tot_size_arr[$val]+=$prod_bal_qty;
															$prod_bal_qty=$prod_quantity=$issue_quantity=0;
														}
														?>
								                        <td align="right" ><? echo $size_total_qty; ?></td>
								                        <td style="word-break:break-all"></td>
								                    </tr>
													<?
													$i++;
												}
											}
										}
									}
								}
							}
						}
	                	?>
	                </tbody>
	                <tfoot>
	                	<td colspan="7" align="right"><b>Total:</b></td>
	                	<?
	                    foreach ($all_size as $val)
						{ 
							$totalQuantity=$tot_size_arr[$val];
							//$grand_fab_defect_qty+=$fab_defect_qty;
							//$grand_print_defect_qty+=$print_defect_qty;
							$grandTotalQuantity+=$totalQuantity;
							?>
							<td align="right" ><strong><? echo $totalQuantity; ?></strong></td>
							
							<?
						}
						?>
	                    <td align="right" ><strong><? echo $grandTotalQuantity; ?></td>
	                    <td align="right" >&nbsp;</td>
	                </tfoot>
	            </table>
        </div>
	    </fieldset>
    </div> 
    <?
    exit(); 
}
if($action=="delivery_popup")
{
	echo load_html_head_contents("Delivery Details","../../../", 1, 1, $unicode);
	extract($_REQUEST);
    $ex_data 	= explode("_", $data);
    $job_no 	= $ex_data[0];
    $order_id 	= $ex_data[1];
    $within_group= $ex_data[2];
    $date_from 	= $ex_data[3];
    $date_to 	= $ex_data[4];
    $del_ids 	= $ex_data[5];
    $color 		= $ex_data[6];
    $bodyPart 	= $ex_data[7];

    $date_cond	= "";
    if($date_from !="" && $date_to !="")
    {
    	$date_cond = " and a.delivery_date between '$date_from' and '$date_to' ";
    }
    if ($color!='') $sys_cond.=" and a.color_id=$color";
    if ($bodyPart!='') $sys_cond.=" and b.body_part=$bodyPart";
    
	$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
    $size_arr=return_library_array( "SELECT id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
    $buyer_arr = return_library_array("SELECT id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
	
	$order_cond = "";
    $sql_wo_del="SELECT a.size_id,a.color_id, b.order_no, b.buyer_buyer, b.buyer_style_ref,b.buyer_po_no, b.job_no_mst, b.embl_type,b.main_process_id, c.party_id , d.id as del_dtls_id,d.mst_id,d.company_id,d.entry_form,d.wo_id,d.wo_dtls_id,d.wo_break_id,d.rcv_id,d.rcv_dtls_id,d.bundle_mst_id,d.bundle_dtls_id,d.challan_no,d.quantity,d.issue_dtls_id,e.issue_number as challan_no, e.issue_date as delivery_date, e.remarks as mst_remarks  from subcon_ord_breakdown a, subcon_ord_dtls b, subcon_ord_mst c, printing_bundle_issue_dtls d, printing_bundle_issue_mst e where e.id=d.mst_id and e.id in ($del_ids) and a.id=d.wo_break_id and a.mst_id=b.id and b.id=d.wo_dtls_id and a.job_no_mst=c.embellishment_job and d.wo_id=c.id and b.mst_id=c.id and d.entry_form=499 and e.entry_form=499 and d.mst_id in ($del_ids) and a.status_active =1 and a.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0 $sys_cond";
	$sql_wo_del_res=sql_select($sql_wo_del);
	foreach ($sql_wo_del_res as $row)
	{ 
		if($row[csf("size_id")]!="") $all_sizes.=$row[csf("size_id")].',';
		if($row[csf("party_id")]!="") $all_party_id.=$row[csf("party_id")].',';
		if($row[csf("bundle_dtls_id")]!="") $all_bundle_dtls_ids.=$row[csf("bundle_dtls_id")].',';
		if($row[csf("issue_dtls_id")]!="") $all_issue_dtls_ids.=$row[csf("issue_dtls_id")].',';
		$wo_del_arr[$row[csf("delivery_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['quantity'] +=$row[csf("quantity")];
		$wo_del_arr[$row[csf("delivery_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['size_id'] .=$row[csf("size_id")].',';
		$wo_del_arr[$row[csf("delivery_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['issue_dtls_id'] .=$row[csf("issue_dtls_id")].',';
		
		$wo_del_size_arr[$row[csf("delivery_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['quantity'] +=$row[csf("quantity")];
		$wo_del_size_arr[$row[csf("delivery_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['bundle_dtls_id'] .=$row[csf("bundle_dtls_id")].',';
		$wo_del_size_arr[$row[csf("delivery_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['issue_dtls_id'] .=$row[csf("issue_dtls_id")].',';
	}
	$all_size=array_unique(explode(",",chop($all_sizes,',')));

    $width=870+(count($all_size)*160);
	$mst_width=$width-710;
	$top_width=$width-400;
	$width_px=$width.'px';
	?>    
    <div id="data_panel" align="center" style="width:100%">
       	<fieldset style="width: 98%">
	    <legend> Delivery Quantity</legend>
	     	<div style="width:100%;">
	            <table align="center" cellspacing="0" width="<? echo $width; ?>" border="1" rules="all" class="rpt_table" style="font-size:12px">
	                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
	                    <!-- Delivery Date	Customer	Challan	Buyer	Style	Job No	Color -->
	                    <th width="30">SL</th>
	                    <th width="120">Delivery Date</th>
	                    <th width="100">Customer</th>
	                    <th width="100">Challan</th>
	                    <th width="120">Buyer</th>
	                    <th width="100">Style</th>
	                    <th width="100">Buyer PO</th>
	                    <th width="100">Color</th>
	                    <?
	                    foreach ($all_size as $val)
						{ 
							?>
							<th width="100"><? echo $size_arr[$val]; ?></th>
							<?
						}
					    ?>
                    <th width="100">Total</th>
                    <th>Remarks</th>
	                </thead>
	                <tbody>
	                	<?
	                	if(count($wo_del_arr)>0)
				    	{ 
				    		//$wo_del_arr[$row[csf("delivery_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]
				    		$i=1; $tot_size_arr=array();
				    		foreach ($wo_del_arr as $delivery_date => $delivery_date_data ) 
							{
								foreach ($delivery_date_data as $party_id => $party_id_data ) 
								{
									foreach ($party_id_data as $challan_no => $challan_no_data ) 
									{
										foreach ($challan_no_data as $buyer_buyer => $buyer_buyer_data ) 
										{
											foreach ($buyer_buyer_data as $buyer_style_ref => $buyer_style_ref_data ) 
											{
												foreach ($buyer_style_ref_data as $buyer_po_no => $buyer_po_no_data ) 
												{
													foreach ($buyer_po_no_data as $color_id => $row ) 
													{
														//echo $row['size_id'].'=='; 
														//$all_del_sizes=array_unique(explode(",",chop($row['size_id'],',')));
														if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
														?>
									                    <tr bgcolor="<? echo $bgcolor; ?>">
									                        <td><? echo $i; ?></td>
									                        <td style="word-break:break-all"><? echo $delivery_date ?></td>
									                        <td style="word-break:break-all"><? echo $buyer_arr[$party_id]; ?></td>
									                        <td style="word-break:break-all"><? echo $challan_no; ?></td>
									                        <td style="word-break:break-all"><? echo $buyer_buyer; ?></td>
									                        <td style="word-break:break-all"><? echo $buyer_style_ref; ?></td>
									                        <td style="word-break:break-all"><? echo $buyer_po_no; ?></td>
									                        <td style="word-break:break-all"><? echo $color_arr[$color_id]; ?></td>
									                        <?
									                        $size_total_qty=0; $cut_nos=''; $cut_no=''; $bundle_dtls_id='';
										                    foreach ($all_size as $val)
															{ 
																$quantity=$wo_del_size_arr[$delivery_date][$party_id][$challan_no][$buyer_buyer][$buyer_style_ref][$buyer_po_no][$color_id][$val]['quantity'];

																?>
																<td align="right" ><? echo $quantity; ?></td>
																<?
																$size_total_qty +=$quantity;
																$tot_size_arr[$val]+=$quantity;
																$quantity=0;
															}
															?>
									                        <td align="right" ><? echo $size_total_qty; ?></td>
									                        <td style="word-break:break-all"></td>
									                    </tr>
														<?
														$i++;
													}
												}
											}
										}
									}
								}
							}
						}
	                	?>
	                </tbody>
	                <tfoot>
	                	<td colspan="8" align="right"><b>Total:</b></td>
	                	<?
	                    foreach ($all_size as $val)
						{ 
							$totalQuantity=$tot_size_arr[$val];
							$grand_fab_defect_qty+=$fab_defect_qty;
							$grand_print_defect_qty+=$print_defect_qty;
							$grandTotalQuantity+=$totalQuantity;
							?>
							<td align="right" ><strong><? echo $totalQuantity; ?></strong></td>
							
							<?
						}
						?>
	                    <td align="right" ><strong><? echo $grandTotalQuantity; ?></td>
	                    <td align="right" >&nbsp;</td>
	                </tfoot>
	            </table>
        </div>
	    </fieldset>
    </div> 
    <?
    exit(); 
}


if($action=="bill_qty_pop_up")
{
	echo load_html_head_contents("Bill Details","../../../", 1, 1, $unicode);
	extract($_REQUEST);
    $ex_data 	= explode("_", $data);
    $job_no 	= $ex_data[0];
    $order_id 	= $ex_data[1];
    $within_group= $ex_data[2];
    $date_from 	= $ex_data[3];
    $date_to 	= $ex_data[4];
    $bill_ids 	= $ex_data[5];
    $color 		= $ex_data[6];
    $bodyPart 	= $ex_data[7];

    $date_cond	= "";
    if($date_from !="" && $date_to !="")
    {
    	$date_cond = " and a.delivery_date between '$date_from' and '$date_to' ";
    }
    if ($color!='') $sys_cond.=" and a.color_id=$color";
    if ($bodyPart!='') $sys_cond.=" and b.body_part=$bodyPart";
    $color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
    $size_arr=return_library_array( "SELECT id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
    $buyer_arr = return_library_array("SELECT id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
   /* $sql_bill="SELECT c.job_no_mst as job_no,c.body_part,c.id as po_id,b.color_size_id,
	sum(case when a.bill_date = $txt_date_from then b.delivery_qty else 0 end) as today_bill_qty,
	sum(case when a.bill_date < $txt_date_from then b.delivery_qty else 0 end) as prev_bill_qty, 
	sum(b.delivery_qty) as total_bill_qty,
	sum(b.amount) as total_bill_amount,a.id
	from subcon_inbound_bill_mst a,subcon_inbound_bill_dtls b,subcon_ord_dtls c 
	where a.id=b.mst_id and c.id=b.order_id $sql_con and b.process_id=13 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 
	group by c.job_no_mst,c.body_part ,c.id,b.color_size_id,a.id";*/

	$sql_wo_del="SELECT a.size_id,a.color_id, b.order_no, b.buyer_buyer, b.buyer_style_ref,b.buyer_po_no, b.job_no_mst, b.embl_type,b.main_process_id, c.party_id ,e.delivery_qty as quantity,d.bill_no as challan_no, d.bill_date as delivery_date  from subcon_ord_breakdown a, subcon_ord_dtls b, subcon_ord_mst c, subcon_inbound_bill_mst d,subcon_inbound_bill_dtls e where d.id=e.mst_id and d.id in ($bill_ids) and a.mst_id=b.id  and e.color_size_id=a.id and a.job_no_mst=c.embellishment_job and b.mst_id=c.id  and e.process_id=13  and e.mst_id in ($bill_ids) and a.status_active =1 and a.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0 $sys_cond";
	$sql_wo_del_res=sql_select($sql_wo_del);
	foreach ($sql_wo_del_res as $row)
	{ 
		if($row[csf("size_id")]!="") $all_sizes.=$row[csf("size_id")].',';
		if($row[csf("party_id")]!="") $all_party_id.=$row[csf("party_id")].',';
		if($row[csf("bundle_dtls_id")]!="") $all_bundle_dtls_ids.=$row[csf("bundle_dtls_id")].',';
		if($row[csf("issue_dtls_id")]!="") $all_issue_dtls_ids.=$row[csf("issue_dtls_id")].',';
		$wo_del_arr[$row[csf("delivery_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['quantity'] +=$row[csf("quantity")];
		$wo_del_arr[$row[csf("delivery_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['size_id'] .=$row[csf("size_id")].',';
		$wo_del_arr[$row[csf("delivery_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]['issue_dtls_id'] .=$row[csf("issue_dtls_id")].',';
		
		$wo_del_size_arr[$row[csf("delivery_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['quantity'] +=$row[csf("quantity")];
		$wo_del_size_arr[$row[csf("delivery_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['bundle_dtls_id'] .=$row[csf("bundle_dtls_id")].',';
		$wo_del_size_arr[$row[csf("delivery_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]][$row[csf("size_id")]]['issue_dtls_id'] .=$row[csf("issue_dtls_id")].',';
	}
	$all_size=array_unique(explode(",",chop($all_sizes,',')));

    $width=870+(count($all_size)*160);
	$mst_width=$width-710;
	$top_width=$width-400;
	$width_px=$width.'px';
	?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                    	<th width="30">SL</th>
                        <th width="70">Bill Date</th>
                        <th width="100">Customer</th>
                        <th width="100">Bill No.</th>
                        <th width="80">Buyer</th>
                        <th width="80">Style</th>
                        <th width="100">Buyer PO</th>
	                    <th width="100">Color</th>
	                    <?
	                    foreach ($all_size as $val)
						{ 
							?>
							<th width="100"><? echo $size_arr[$val]; ?></th>
							<?
						}
					    ?>
                    <th width="100">Total</th>
                    <th>Remarks</th>
                    </tr>
                </thead>
            	<tbody>
                	<?
                	if(count($wo_del_arr)>0)
			    	{ 
			    		//$wo_del_arr[$row[csf("delivery_date")]][$row[csf("party_id")]][$row[csf("challan_no")]][$row[csf("buyer_buyer")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]][$row[csf("color_id")]]
			    		$i=1; $tot_size_arr=array();
			    		foreach ($wo_del_arr as $delivery_date => $delivery_date_data ) 
						{
							foreach ($delivery_date_data as $party_id => $party_id_data ) 
							{
								foreach ($party_id_data as $challan_no => $challan_no_data ) 
								{
									foreach ($challan_no_data as $buyer_buyer => $buyer_buyer_data ) 
									{
										foreach ($buyer_buyer_data as $buyer_style_ref => $buyer_style_ref_data ) 
										{
											foreach ($buyer_style_ref_data as $buyer_po_no => $buyer_po_no_data ) 
											{
												foreach ($buyer_po_no_data as $color_id => $row ) 
												{
													//echo $row['size_id'].'=='; 
													//$all_del_sizes=array_unique(explode(",",chop($row['size_id'],',')));
													if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													?>
								                    <tr bgcolor="<? echo $bgcolor; ?>">
								                        <td><? echo $i; ?></td>
								                        <td style="word-break:break-all"><? echo $delivery_date ?></td>
								                        <td style="word-break:break-all"><? echo $buyer_arr[$party_id]; ?></td>
								                        <td style="word-break:break-all"><? echo $challan_no; ?></td>
								                        <td style="word-break:break-all"><? echo $buyer_buyer; ?></td>
								                        <td style="word-break:break-all"><? echo $buyer_style_ref; ?></td>
								                        <td style="word-break:break-all"><? echo $buyer_po_no; ?></td>
								                        <td style="word-break:break-all"><? echo $color_arr[$color_id]; ?></td>
								                        <?
								                        $size_total_qty=0; $cut_nos=''; $cut_no=''; $bundle_dtls_id='';
									                    foreach ($all_size as $val)
														{ 
															$quantity=$wo_del_size_arr[$delivery_date][$party_id][$challan_no][$buyer_buyer][$buyer_style_ref][$buyer_po_no][$color_id][$val]['quantity'];

															?>
															<td align="right" ><? echo $quantity; ?></td>
															<?
															$size_total_qty +=$quantity;
															$tot_size_arr[$val]+=$quantity;
															$quantity=0;
														}
														?>
								                        <td align="right" ><? echo $size_total_qty; ?></td>
								                        <td style="word-break:break-all"></td>
								                    </tr>
													<?
													$i++;
												}
											}
										}
									}
								}
							}
						}
					}
                	?>
                </tbody>
                <tfoot>
                	<td colspan="8" align="right"><b>Total:</b></td>
                	<?
                    foreach ($all_size as $val)
					{ 
						$totalQuantity=$tot_size_arr[$val];
						$grand_fab_defect_qty+=$fab_defect_qty;
						$grand_print_defect_qty+=$print_defect_qty;
						$grandTotalQuantity+=$totalQuantity;
						?>
						<td align="right" ><strong><? echo $totalQuantity; ?></strong></td>
						
						<?
					}
					?>
                    <td align="right" ><strong><? echo $grandTotalQuantity; ?></td>
                    <td align="right" >&nbsp;</td>
                </tfoot>
            </table>
        </div>
	    </fieldset>
    </div> 
    <?
    exit(); 
}
?>