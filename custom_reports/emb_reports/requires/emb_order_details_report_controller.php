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
			load_drop_down( 'emb_order_details_report_controller', company+'_'+within_group+'_'+party, 'load_drop_down_buyer', 'buyer_td' );
			
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
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $data[2];?>, 'create_job_search_list_view', 'search_div', 'emb_order_details_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
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
	
	$po_sql ="SELECT a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	
	$comp=return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
	//$color_lib_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	//$arr=array (3=>$comp,6=>$yes_no,7=>$color_lib_arr);
	
	if($db_type==0)
	{
		$ins_year_cond="year(a.insert_date)";
		$color_id_str="group_concat(c.color_id)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str="listagg(c.color_id,',') within group (order by c.color_id)";
	}
	 $sql= "SELECT a.id, a.embellishment_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, $color_id_str as color_id, b.buyer_po_id  
	 from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	 where a.entry_form=204 and a.embellishment_job=b.job_no_mst and b.booking_dtls_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond and b.id=c.mst_id  
	 group by a.id, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, b.buyer_po_id
	 order by a.id DESC";

	//echo $sql;die;

	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="885" >
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
				$color_name="";	
				foreach ($excolor_id as $color_id)
				{
					if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
				}
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('embellishment_job')]; ?>")' style="cursor:pointer" >
                    <td width="25"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="120"><? echo $row[csf('order_no')]; ?></td>
                    <td width="100"><? echo $buyer_po_arr[$row[csf('buyer_po_id')]]['po']; ?></td>
                    <td width="100"><? echo $buyer_po_arr[$row[csf('buyer_po_id')]]['style']; ?></td>
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
	
	$sql="SELECT distinct b.id, b.job_no_mst as job_no ,a.party_id as buyer_name, b.order_no as po_number, a.job_no_prefix_num as job_prefix, $year_field from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where b.job_no_mst=a.subcon_job and a.id=c.mst_id and b.id=c.order_id and a.company_id=$company_id $sub_buyer_name_cond $job_no_cond $year_field_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 group by b.id, a.party_id, b.job_no_mst, b.order_no, a.job_no_prefix_num, a.company_id, a.insert_date";	
	
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
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value('<? echo $data[csf('id')].'_'.$data[csf('po_number')]; ?>')" style="cursor:pointer;">
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
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$year_id=str_replace("'","",$cbo_year);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	//$cbo_process=str_replace("'","",$cbo_process_id);
	if($year_id!=0) $year_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_cond="";
	if($cbo_buyer_id!=0) $buyer_id_cond=" AND SOM.PARTY_ID ='$cbo_buyer_id'"; else $buyer_id_cond="";
	
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	//if($txt_style_ref!="") $style_ref_cond="%".trim($txt_style_ref)."%"; else $style_ref_cond="%%";
	if ($txt_style_ref!='') $style_ref_cond=" and b.cust_style_ref like '%$txt_style_ref%'"; else $style_ref_cond="";
	//if(trim($txt_order_no)!="") $order_no_cond="%".trim($txt_order_no)."%"; else $order_no_cond="%%";
	if ($txt_order_no!='') $order_no_cond=" and b.order_no like '%$txt_order_no%'"; else $order_no_cond="";
	//if ($cbo_process==0) $process_id_cond=""; else $process_id_cond=" and b.main_process_id=$cbo_process_id";
	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond=""; else $date_cond=" and b.delivery_date between $txt_date_from and $txt_date_to";
	
	?>
    <div>
		<?/* echo "Test";
		select lib_company.company_name, lib_company1.company_name as party_name, lib_buyer.buyer_name, lib_location.location_name, lib_garment_item.item_name, lib_color.color_name, lib_size.size_name, wo_po_break_down.po_number, wo_po_details_master.style_ref_no, mst.company_id, mst.location_id, mst.party_id, mst.receive_date, mst.delivery_date, mst.job_no_prefix_num, mst.job_no_mst, mst.order_no, mst.po_delivery_date, mst.order_id, mst.item_id, mst.color_id, mst.size_id, mst.qnty, mst.rate, mst.amount, mst.buyer_po_id, mst.gmts_item_id, mst.embl_type, mst.body_part from (select som.company_id, som.location_id, som.party_id, som.receive_date, som.delivery_date, som.job_no_prefix_num, sod.job_no_mst, sod.order_no, sod.delivery_date as po_delivery_date, sob.order_id, sob.item_id, sob.color_id, sob.size_id, sob.qnty, sob.rate, sob.amount, sod.buyer_po_id, sod.gmts_item_id, sod.embl_type, sod.body_part from subcon_ord_mst som inner join subcon_ord_dtls sod on som.id = sod.mst_id inner join subcon_ord_breakdown sob on sod.id = sob.mst_id where som.company_id = 3 and som.job_no_prefix_num = 11 and som.status_active = 1 ) mst inner join lib_company on mst.company_id = lib_company.id inner join lib_location on lib_location.id = mst.location_id inner join lib_garment_item on mst.item_id = lib_garment_item.id inner join lib_color on mst.color_id = lib_color.id inner join lib_size on mst.size_id = lib_size.id inner join wo_po_break_down on mst.buyer_po_id = wo_po_break_down.id inner join wo_po_details_master on wo_po_break_down.job_no_mst = wo_po_details_master.job_no inner join lib_buyer on lib_buyer.id = wo_po_details_master.buyer_name inner join lib_company lib_company1 on lib_company1.id = mst.party_id 
		*/
		//--------------------------------------------------------Start----------------------------------------
		
	$query = "SELECT lib_company.company_name, lib_company1.company_name as party_name, lib_buyer.buyer_name, lib_location.location_name, lib_garment_item.item_name, lib_color.color_name, lib_size.size_name, wo_po_break_down.po_number, wo_po_details_master.style_ref_no,  mst.company_id, mst.location_id, mst.party_id, mst.receive_date, mst.delivery_date, mst.job_no_prefix_num, mst.job_no_mst, mst.order_no, mst.po_delivery_date, mst.order_id, mst.item_id, mst.color_id, mst.size_id,  mst.qnty, mst.rate, mst.amount, mst.buyer_po_id, mst.gmts_item_id, mst.embl_type, mst.body_part
	FROM
	(SELECT som.company_id, som.location_id, som.party_id, som.receive_date, som.delivery_date, som.job_no_prefix_num, sod.job_no_mst, sod.order_no, sod.delivery_date as po_delivery_date, sob.order_id, sob.item_id, sob.color_id, sob.size_id, sob.qnty, sob.rate, sob.amount, sod.buyer_po_id, sod.gmts_item_id, sod.embl_type, sod.body_part
	FROM subcon_ord_mst som inner join subcon_ord_dtls sod on som.id=sod.mst_id inner join subcon_ord_breakdown sob on sod.id=sob.mst_id
	WHERE som.company_id=3 $buyer_id_cond and som.status_active=1) mst
	inner join lib_company on mst.company_id=lib_company.id inner join lib_location on lib_location.id=mst.location_id
	inner join lib_garment_item on mst.item_id=lib_garment_item.id inner join lib_color on mst.color_id=lib_color.id 
	inner join lib_size on mst.size_id=lib_size.id inner join wo_po_break_down on mst.buyer_po_id=wo_po_break_down.id
	inner join wo_po_details_master on wo_po_break_down.job_no_mst=wo_po_details_master.job_no inner join lib_buyer on lib_buyer.id=wo_po_details_master.buyer_name inner join lib_company lib_company1
	on lib_company1.id=mst.party_id ";

	//echo $query;
	$sql_data_query = sql_select($query);
	$countRecords = count($query); 
	//echo $sql_data_query;
	ob_start();
	$details_data=array();
	/*
		  company_name,  company_name as party_name,  buyer_name,  location_name,  item_name,
		  color_name,  size_name,  po_number,  style_ref_no,  company_id,  location_id,  party_id,
		  receive_date,  delivery_date,  job_no_prefix_num,  job_no_mst,  order_no,  po_delivery_date,  order_id,  item_id,  color_id,
		  size_id,  qnty,  rate,  amount,  buyer_po_id,  gmts_item_id,  embl_type,  body_part
	*/
	foreach( $sql_data_query as $row)
	{
		//detail data in Array  
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["COMPANY_NAME"]=$row[csf(COMPANY_NAME)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["PARTY_NAME"]=$row[csf(PARTY_NAME)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["BUYER_NAME"]=$row[csf(BUYER_NAME)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["PO_NUMBER"]=$row[csf(PO_NUMBER)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["RECEIVE_DATE"]=$row[csf(RECEIVE_DATE)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["PO_DELIVERY_DATE"]=$row[csf(PO_DELIVERY_DATE)];
		
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["ORDER_NO"]=$row[csf(ORDER_NO)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["STYLE_REF_NO"]=$row[csf(STYLE_REF_NO)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["COLOR_NAME"]=$row[csf(COLOR_NAME)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["ITEM_NAME"]=$row[csf(ITEM_NAME)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["SIZE_NAME"]=$row[csf(SIZE_NAME)];
		
				
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["EMBL_TYPE"]=$row[csf(EMBL_TYPE)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["BODY_PART"]=$row[csf(BODY_PART)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["RATE"]=$row[csf(RATE)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["QNTY"]=$row[csf(QNTY)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["AMOUNT"]=$row[csf(AMOUNT)];

	}
		
	?>
	<div style="width:1670px; margin:0 auto;">
		<fieldset style="width:100%;">	
		    <table width="98%" cellpadding="0" cellspacing="0" id="caption">
	            <tr>  
	                <td align="center" width="100%" colspan="11" class="form_caption" >
	                	<strong style="font-size:18px">Screen Print Order Details</strong>
	                </td>
	            </tr>
	        </table>
			<div style="width:1670px; align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">	
				<table width="1650"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" rules="all">
					<thead>
						<th style="word-wrap: break-word;word-break: break-all;" width="30" align="center">Sl </th>
						<th style="word-wrap: break-word;word-break: break-all;" width="200" align="center">Company </th>
						<th style="word-wrap: break-word;word-break: break-all;" width="85" align="center">Party </th>
						<th style="word-wrap: break-word;word-break: break-all;" width="90" align="center">Buyer </th>
						<th style="word-wrap: break-word;word-break: break-all;" width="150" align="center">PO</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="120" align="center">Rcv Date</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="85" align="center">Ship Date</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="85" align="center">Wo No</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="120" align="center">Style</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="85" align="center">Item</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="85" align="center">Color</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="85" align="center">Size</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="85" align="center">Embl Type</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="85" align="center">Body Part</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="85" align="center">Rate</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="85" align="center">Qnty</th>
						<th style="word-wrap: break-word;word-break: break-all;" align="center">Amount</th>						
					</thead>
				</table>
			</div>
			<div style="width:1670px; align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">	
				<table width="1650"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<tbody>
						<?	
						$k=1;
						foreach($details_data as $company_id=>$company_data)
						{
							foreach($company_data as $party_id=>$party_data)
							{
								foreach($party_data as $po_id=>$po_data)
								{
									foreach($po_data as $color_id=>$color_data)
									{
										foreach($color_data as $size_id=>$size_data)
										{
											if ($k%2==0)  
											$bgcolor="#E9F3FF";
											else
											$bgcolor="#FFFFFF";
										?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k;?>">
												<td style="word-wrap: break-word;word-break: break-all;" width="30"> <? echo $k; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="200"> <? echo $size_data[('COMPANY_NAME')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="85">  <? echo $size_data[('PARTY_NAME')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="90">  <? echo $size_data[('BUYER_NAME')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="150"> <? echo $size_data[('PO_NUMBER')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="120">  <? echo $size_data[('RECEIVE_DATE')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="85">  <? echo $size_data[('PO_DELIVERY_DATE')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="85">  <? echo $size_data[('ORDER_NO')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="120">  <? echo $size_data[('STYLE_REF_NO')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="85">  <? echo $size_data[('ITEM_NAME')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="85">  <? echo $size_data[('COLOR_NAME')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="85">  <? echo $size_data[('SIZE_NAME')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="85">  <? echo $emblishment_print_type[$size_data[('EMBL_TYPE')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="85"> <? echo $size_data[('BODY_PART')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="85">  <? echo $size_data[('RATE')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="85" align="right">  <? echo $size_data[('QNTY')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" align="right">  <? echo $size_data[('AMOUNT')]; ?></td>
											</tr>
											<?
											$k++;
											$total_rate+=$size_data[('RATE')];
											$total_qty+=$size_data[('QNTY')];
											$total_amount+=$size_data[('AMOUNT')];
										}
									}
								}
							}
						}
						?>
					</tbody>
				</table>
			</div>
			<table width="1650" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
				<tfoot>
	                <tr bgcolor="#dddddd">   
						<td style="word-wrap: break-word;word-break: break-all;" width="30"></td>
						<td style="word-wrap: break-word;word-break: break-all;" width="200"></td>
						<td style="word-wrap: break-word;word-break: break-all;" width="85"></td>
						<td style="word-wrap: break-word;word-break: break-all;" width="90"></td>
						<td style="word-wrap: break-word;word-break: break-all;" width="150"></td>
						<td style="word-wrap: break-word;word-break: break-all;" width="120"></td>
						<td style="word-wrap: break-word;word-break: break-all;" width="85"></td>
						<td style="word-wrap: break-word;word-break: break-all;" width="85"></td>
						<td style="word-wrap: break-word;word-break: break-all;" width="120"> </td>
						<td style="word-wrap: break-word;word-break: break-all;" width="85"> </td>
						<td style="word-wrap: break-word;word-break: break-all;" width="85"> </td>
						<td style="word-wrap: break-word;word-break: break-all;" width="85"> </td>
						<td style="word-wrap: break-word;word-break: break-all;" width="85"> </td>
						<td style="word-wrap: break-word;word-break: break-all;" width="85" >Grand Total :</td>
						<td style="word-wrap: break-word;word-break: break-all;" width="85" id="value_total_rate"><? echo number_format($total_rate,2,'.',''); ?></td>
						<td style="word-wrap: break-word;word-break: break-all;" width="85" id="value_total_qty"><? echo number_format($total_qty,2,'.',''); ?></td>
						<td style="word-wrap: break-word;word-break: break-all;" align="right" id="value_total_amount"><? echo number_format($total_amount,2,'.',''); ?></td>
	                </tr>
	            </tfoot>
            </table>
		</fieldset>
	</div>	
		<?
				//--------------------------------------------------------End----------------------------------------
		?>
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
    echo "$html**$filename"; 
    exit();
}

if($action=="material_desc_popup")
{
	echo load_html_head_contents("Material Description Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Receive Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Receive ID</th>
                        <th width="70">Challan No</th>
                        <th width="70">Rec. Date</th>
                        <th width="60">Party</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Receive Qty</th>
                        <th width="80">Bag/ Roll</th>
                        <th>Cone</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $party_arr=return_library_array( "SELECT id, short_name from  lib_buyer where status_active =1 and is_deleted=0",'id','short_name');
                $po_arr=return_library_array( "SELECT id, order_no from subcon_ord_dtls where status_active =1 and is_deleted=0",'id','order_no');
				$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
                $i=0;
                
                $sql= "SELECT a.sys_no, a.prefix_no_num, a.chalan_no, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia, sum(b.quantity) as quantity, sum(b.subcon_roll) as subcon_roll, sum(b.rec_cone) as rec_cone from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=2 and b.is_deleted=0 and a.trans_type=1 group by a.sys_no, a.prefix_no_num, a.chalan_no, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia order by a.sys_no, a.subcon_date";
               //echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					//if ($row[csf("color_id")]!=0 && $row[csf("gsm")]!=0  && $row[csf("grey_dia")]!=0  && $row[csf("fin_dia")]!=0 )
					//{
						$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
					//}
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("prefix_no_num")];?> </td>
                    <td width="70"><? echo $row[csf("chalan_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("subcon_date")]);?> </td> 
                    <td width="60"><p><? echo $party_arr[$row[csf("party_id")]]; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td align="center" width="150"><? echo $material_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="center" width="80"><? echo $row[csf("subcon_roll")];; ?></td>
                    <td><p><? echo $row[csf("rec_cone")]; ?></p></td>
                </tr>
                <? 
                $tot_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
        </div> 
	</fieldset>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Return Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Return ID</th>
                        <th width="70">Challan No</th>
                        <th width="70">Return Date</th>
                        <th width="60">Party</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Return Qty</th>
                        <th width="80">Bag/ Roll</th>
                        <th>Cone</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $party_arr=return_library_array( "SELECT id, short_name from  lib_buyer where status_active =1 and is_deleted=0",'id','short_name');
                $po_arr=return_library_array( "SELECT id, order_no from subcon_ord_dtls where status_active =1 and is_deleted=0",'id','order_no');
				$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
                $i=0;
                
                $sql_ret= "SELECT a.sys_no, a.prefix_no_num, a.chalan_no, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia, sum(b.quantity) as quantity, sum(b.subcon_roll) as subcon_roll, sum(b.rec_cone) as rec_cone from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trans_type=3 group by a.sys_no, a.prefix_no_num, a.chalan_no, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia order by a.sys_no, a.subcon_date";
               //echo $sql;
                $material_ret_sql= sql_select($sql_ret);
                foreach( $material_ret_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					//if ($row[csf("color_id")]!=0 && $row[csf("gsm")]!=0  && $row[csf("grey_dia")]!=0  && $row[csf("fin_dia")]!=0 )
					//{
					$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
					//}
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("prefix_no_num")];?> </td>
                    <td width="70"><? echo $row[csf("chalan_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("subcon_date")]);?> </td> 
                    <td width="60"><p><? echo $party_arr[$row[csf("party_id")]]; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td align="center" width="150"><? echo $material_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="center" width="80"><? echo $row[csf("subcon_roll")];; ?></td>
                    <td><p><? echo $row[csf("rec_cone")]; ?></p></td>
                </tr>
                <? 
                $tot_ret_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_ret_qty,2); ?></p></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
	<?
	exit();
}

if($action=="material_desc_iss_popup")
{
	echo load_html_head_contents("Material Issue Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Issue Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Issue ID</th>
                        <th width="70">Challan No</th>
                        <th width="70">Issue Date</th>
                        <th width="60">Issue To</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Issue Qty</th>
                        <th width="80">Bag/ Roll</th>
                        <th>Cone</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "SELECT id, short_name from lib_supplier where status_active =1 and is_deleted=0",'id','short_name');
				$company_array=return_library_array( "SELECT id, company_short_name from lib_company where status_active =1 and is_deleted=0",'id','company_short_name');
                $po_arr=return_library_array( "SELECT id, order_no from subcon_ord_dtls where status_active =1 and is_deleted=0",'id','order_no');
				$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
                $i=0;
                
                $sql= "SELECT a.sys_no, a.prefix_no_num, a.chalan_no, a.prod_source, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia, sum(b.quantity) as quantity, sum(b.subcon_roll) as subcon_roll, sum(b.rec_cone) as rec_cone from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trans_type=2 group by a.sys_no, a.prefix_no_num, a.chalan_no, a.prod_source, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia order by a.sys_no, a.subcon_date";
               //echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					//if ($row[csf("color_id")]!=0 && $row[csf("gsm")]!=0  && $row[csf("grey_dia")]!=0  && $row[csf("fin_dia")]!=0 )
					//{
						$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
					//}
					$issue_to="";
					if($row[csf("prod_source")]==1) $issue_to=$company_array[$row[csf("party_id")]]; else if($row[csf("prod_source")]==3) $issue_to=$supplier_array[$row[csf("party_id")]]; else $issue_to="";
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("prefix_no_num")];?> </td>
                    <td width="70"><? echo $row[csf("chalan_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("subcon_date")]);?> </td> 
                    <td width="60"><p><? echo  $issue_to; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td align="center" width="150"><? echo $material_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="center" width="80"><? echo $row[csf("subcon_roll")];; ?></td>
                    <td><p><? echo $row[csf("rec_cone")]; ?></p></td>
                </tr>
                <? 
                $tot_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
<?
exit();
}

if($action=="product_qty_pop_up")
{
	echo load_html_head_contents("Production Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	$order_id=$expData[0];
	$process_id=$expData[1];
?>
        <fieldset style="width:820px">
            <div style="width:100%;" align="center">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                    <thead>
                    	<tr>
                            <th width="30">SL</th>
                            <th width="60"><? if ($process_id==3) echo "Batch NO"; else echo "Sys ID" ?></th>
                            <th width="70">Prod. Date</th>
                            <th width="100">Party</th>
                            <th width="80">Order No</th>
                            <th width="130">Process</th>
                            <th width="150">Description</th>
                            <th width="">Prod. Qty</th>
                    	</tr>
                    </thead>
                </table>
            </div>  
            <div style="width:100%; max-height:330px; overflow-y:scroll" align="left">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                    <?
					$party_arr=return_library_array( "SELECT id, buyer_name from  lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
					$kniting_item_arr=return_library_array( "SELECT id, const_comp from lib_subcon_charge where status_active =1 and is_deleted=0",'id','const_comp');
					$po_arr=return_library_array( "SELECT id, order_no from subcon_ord_dtls where status_active =1 and is_deleted=0",'id','order_no');
					$po_party_arr=return_library_array( "SELECT b.id, a.party_id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0",'id','party_id');
					$color_arr=return_library_array( "SELECT id, color_name from  lib_color where status_active =1 and is_deleted=0",'id','color_name');
                    $i=0;
					if ($process_id==1)
					{
						 $sql = "SELECT '' as sys_id, production_date, order_id, gmts_item_id as item_id, '' as process, sum(production_qnty) as production_qnty from subcon_gmts_prod_dtls where order_id='$order_id' and production_type=1 and status_active =1 and is_deleted=0 group by production_date, order_id, gmts_item_id";
					}
					else if ($process_id==5)
					{
						$sql = "SELECT '' as sys_id, production_date, order_id, gmts_item_id as item_id, '' as process, sum(production_qnty) as production_qnty from subcon_gmts_prod_dtls where order_id='$order_id' and production_type=2 and status_active =1 and is_deleted=0 group by production_date, order_id, gmts_item_id";

					}
					else if ($process_id==11)
					{
						$sql = "SELECT '' as sys_id, production_date, order_id, gmts_item_id as item_id, '' as process, sum(production_qnty) as production_qnty from subcon_gmts_prod_dtls where order_id='$order_id' and production_type=4 and status_active =1 and is_deleted=0 group by production_date, order_id, gmts_item_id";

					}
					else if ($process_id==2)
					{
						$sql="SELECT a.prefix_no_num as sys_id, a.product_no, a.party_id, a.product_date as production_date, b.order_id, b.process, b.cons_comp_id as item_id, b.color_id, sum(b.no_of_roll) as roll_qty, sum(b.product_qnty) as production_qnty from subcon_production_mst a, subcon_production_dtls b where b.order_id='$order_id' and b.product_type=2 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prefix_no_num, a.product_no, a.party_id, a.product_date, b.order_id, b.process, b.cons_comp_id, b.color_id order by b.color_id";
					}
					else if($process_id==3)
					{
						if($db_type==0)
						{
							$sql="SELECT b.batch_no as sys_id, a.process_end_date as production_date, c.po_id as order_id, c.item_description as item_id, a.process_id as process, sum(c.batch_qnty) as production_qnty from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.entry_form=38 and a.result=1 and a.load_unload_id=2 and b.entry_form=36 and c.po_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.batch_no, a.process_end_date, a.process_id, c.po_id, c.item_description ";
						}
						elseif($db_type==2)
						{
							$sql="SELECT b.batch_no as sys_id, a.process_end_date as production_date, c.po_id as order_id, c.item_description as item_id, a.process_id as process, sum(c.batch_qnty) as production_qnty from pro_fab_subprocess a, pro_batch_create_mst b,  pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.entry_form=38 and a.result=1 and a.load_unload_id=2 and b.entry_form=36 and c.po_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.batch_no, a.process_end_date, a.process_id, c.po_id, c.item_description ";
						}
					}
					else if($process_id==4)
					{
						$sql = "SELECT a.prefix_no_num as sys_id, a.product_no, a.product_date as production_date, a.party_id, c.order_id, b.process as process, b.fabric_description as item_id, sum(c.quantity) as production_qnty from subcon_production_mst a, subcon_production_dtls b, subcon_production_qnty c where a.id=b.mst_id and b.id=c.dtls_id and c.order_id in ($order_id) and b.product_type='$process_id' group by a.prefix_no_num, a.product_no, a.party_id, a.product_date, c.order_id, b.process, b.fabric_description";
					}
                   //echo $sql;
					$production_sql= sql_select($sql); $color_array=array(); $k=1;
					foreach( $production_sql as $row )
                    {
                        $i++;
                        if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						
						if ($process_id==1 || $process_id==5 || $process_id==11)
						{
							$item_name=$garments_item[$row[csf('item_id')]];
							$party_name=$party_arr[$po_party_arr[$row[csf("order_id")]]];
						}
						else if ($process_id==2)
						{
							$party_name=$party_arr[$row[csf("party_id")]];
							$process_name=$conversion_cost_head_array[$row[csf("process")]];
							$item_name=$kniting_item_arr[$row[csf('item_id')]];
						}
						else if ($process_id==3)
						{
							$party_name=$party_arr[$po_party_arr[$row[csf("order_id")]]];
							$process_name="";
							$process_id=explode(',',$row[csf('process')]);
							foreach($process_id as $val)
							{
								if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=','.$conversion_cost_head_array[$val];
							}
							$item_name=$row[csf('item_id')];
						}
						else if ($process_id==4)
						{
							$party_name=$party_arr[$row[csf("party_id")]];
							$process_name="";
							$process_id=explode(',',$row[csf('process')]);
							foreach($process_id as $val)
							{
								if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=','.$conversion_cost_head_array[$val];
							}
							$item_name=$row[csf('item_id')];
						}
						else
						{
							$item_name=$row[csf('item_id')];
						}
						if ($process_id==2)
						{
							if (!in_array($row[csf("color_id")],$color_array) )
							{
								if($k!=1)
								{
								?>
									<tr class="tbl_bottom">
										<td colspan="7" align="right"><b>Color Total:</b></td>
										<td align="right"><? echo number_format($color_qty); ?></td>
									</tr>
									<tr bgcolor="#dddddd">
										<td colspan="8" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
									</tr>
								<?
									unset($color_qty);
								}
								else
								{
									?>
									<tr bgcolor="#dddddd">
										<td colspan="8" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
									</tr>
									<?
								}					
								$color_array[]=$row[csf('color_id')];            
								$k++;
							}							
						   ?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="60" align="center"><? echo $row[csf("sys_id")];?> </td>
								<td width="70"><? echo change_date_format($row[csf("production_date")]);?> </td> 
								<td width="100"><p><? echo $party_name; ?></p></td>
								<td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
								<td align="center" width="130"><p><? echo $process_name; ?></p></td>
								<td align="center" width="150"><p><? echo $item_name; ?></p></td>
								<td align="right" width=""><? echo number_format($row[csf("production_qnty")],2); ?></td>
							</tr>
							<? 
							$color_qty+=$row[csf("production_qnty")];
							$tot_qty+=$row[csf("production_qnty")];
							
						}
						else
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="60" align="center"><? echo $row[csf("sys_id")];?> </td>
								<td width="70"><? echo change_date_format($row[csf("production_date")]);?> </td> 
								<td width="100"><p><? echo $party_name; ?></p></td>
								<td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
								<td align="center" width="130"><p><? echo $process_name; ?></p></td>
								<td align="center" width="150"><p><? echo $item_name; ?></p></td>
								<td align="right" width=""><? echo number_format($row[csf("production_qnty")],2); ?></td>
							</tr>
							<?
							$tot_qty+=$row[csf("production_qnty")];
						}
					}
					if ($process_id==2)
					{ 
                    ?>
                        <tr class="tbl_bottom">
                            <td colspan="7" align="right"><b>Color Total:</b></td>
                            <td align="right"><? echo number_format($color_qty); ?></td>
                        </tr>
					<? } ?>
                    <tr class="tbl_bottom">
                    	<td colspan="7" align="right">Total: </td>
                        <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    </tr>
                </table>
            </div> 
	</fieldset>
 </div> 
<?
exit();
}

if($action=="delivery_qty_pop_up")
{
	echo load_html_head_contents("Delivery Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
?>
        <fieldset style="width:820px">
            <div style="width:100%;" align="center">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                    <thead>
                    	<tr>
                            <th width="30">SL</th>
                            <th width="60">Delivery ID</th>
                            <th width="70">Delivery Date</th>
                            <th width="80">Batch No</th>
                            <th width="80">Order No</th>
                            <th width="80">Category</th>
                            <th width="150">Description</th>
                            <th width="">Delivery Qty</th>
                    	</tr>
                    </thead>
                </table>
            </div>  
            <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                    <?
					$batch_arr=return_library_array( "SELECT id, batch_no from pro_batch_create_mst where status_active =1 and is_deleted=0",'id','batch_no');
					$po_arr=return_library_array( "SELECT id, order_no from subcon_ord_dtls where status_active =1 and is_deleted=0",'id','order_no');
					$color_arr=return_library_array( "SELECT id, color_name from  lib_color where status_active =1 and is_deleted=0",'id','color_name');
					$kniting_item_arr=return_library_array( "SELECT id, const_comp from lib_subcon_charge where status_active =1 and is_deleted=0",'id','const_comp');
					$dye_fin_item_arr=return_library_array( "SELECT id, item_description from pro_batch_create_dtls where status_active =1 and is_deleted=0",'id','item_description');
                    $i=0;
                    $sql= "SELECT a.delivery_prefix_num, a.delivery_date, a.party_id, b.order_id, b.process_id, b.item_id, b.color_id, b.batch_id, sum(b.delivery_qty) as quantity from  subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and b.process_id='$expData[1]' and a.status_active=1 and a.is_deleted=0 group by a.delivery_prefix_num, a.delivery_date, a.party_id, b.order_id, b.process_id, b.batch_id, b.item_id, b.color_id order by a.delivery_prefix_num, a.delivery_date";
                    //echo $sql;
					$production_sql= sql_select($sql); $color_array=array(); $k=1; $process_id=0;
					foreach( $production_sql as $row )
                    {
                        $i++;
                        if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						$process_id=$row[csf("process_id")];
						if ($row[csf("process_id")]==1 || $row[csf("process_id")]==5)
						{
							$item_name=$garments_item[$row[csf('item_id')]];
						}
						else if ($row[csf("process_id")]==2)
						{
							$item_name=$kniting_item_arr[$row[csf('item_id')]];
						}
						else if ($row[csf("process_id")]==3 || $row[csf("process_id")]==4)
						{
							$item_name=$dye_fin_item_arr[$row[csf('item_id')]];
						}
						
						if ($row[csf("process_id")]==2)
						{
							if (!in_array($row[csf("color_id")],$color_array) )
							{
								if($k!=1)
								{
								?>
									<tr class="tbl_bottom">
										<td colspan="7" align="right"><b>Color Total:</b></td>
										<td align="right"><? echo number_format($color_qty); ?></td>
									</tr>
									<tr bgcolor="#dddddd">
										<td colspan="8" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
									</tr>
								<?
									unset($color_qty);
								}
								else
								{
									?>
									<tr bgcolor="#dddddd">
										<td colspan="8" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
									</tr>
									<?
								}					
								$color_array[]=$row[csf('color_id')];            
								$k++;
							}							
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="60"><? echo $row[csf("delivery_prefix_num")];?> </td>
								<td width="70"><? echo change_date_format($row[csf("delivery_date")]);?> </td> 
								<td width="80"><p><? echo $batch_arr[$row[csf("batch_id")]]; ?></p></td>
								<td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
								<td align="center" width="80"><? echo $production_process[$row[csf("process_id")]]; ?></td>
								<td align="center" width="150"><p><? echo $item_name; ?></p></td>
								<td align="right" width=""><? echo number_format($row[csf("quantity")],2); ?></td>
							</tr>
							<? 
							$color_qty+=$row[csf("quantity")];
							$tot_qty+=$row[csf("quantity")];
						}
						else
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="60"><? echo $row[csf("delivery_prefix_num")];?> </td>
								<td width="70"><? echo change_date_format($row[csf("delivery_date")]);?> </td> 
								<td width="80"><p><? echo $batch_arr[$row[csf("batch_id")]]; ?></p></td>
								<td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
								<td align="center" width="80"><? echo $production_process[$row[csf("process_id")]]; ?></td>
								<td align="center" width="150"><p><? echo $item_name; ?></p></td>
								<td align="right" width=""><? echo number_format($row[csf("quantity")],2); ?></td>
							</tr>
							<? 
							$tot_qty+=$row[csf("quantity")];
						}
					} 
					if($process_id==2)
					{
					?>
                        <tr class="tbl_bottom">
                            <td colspan="7" align="right"><b>Color Total:</b></td>
                            <td align="right"><? echo number_format($color_qty); ?></td>
                        </tr>
                    <?
					}
					?>
                    <tr class="tbl_bottom">
                    	<td colspan="7" align="right">Total: </td>
                        <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    </tr>
                </table>
            </div> 
	</fieldset>
 </div> 
<?
exit();
}

if($action=="bill_qty_pop_up")
{
	echo load_html_head_contents("Delivery Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Bill ID</th>
                        <th width="70">Bill Date</th>
                        <th width="100">Party</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Bill Qty</th>
                        <th>Amount</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $party_arr=return_library_array( "SELECT id, buyer_name from  lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
                $po_arr=return_library_array( "SELECT id, order_no from subcon_ord_dtls where status_active =1 and is_deleted=0",'id','order_no');
				$kniting_item_arr=return_library_array( "SELECT id, const_comp from lib_subcon_charge where status_active =1 and is_deleted=0",'id','const_comp');
				$dye_fin_item_arr=return_library_array( "SELECT id, item_description from pro_batch_create_dtls where status_active =1 and is_deleted=0",'id','item_description');
                $i=0;
                $sql= "SELECT a.bill_no, a.bill_date, a.party_id, b.order_id, b.process_id, b.item_id, sum(b.delivery_qty) as quantity, sum(b.amount) as amount from  subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and b.process_id='$expData[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted =0  group by a.bill_no, a.bill_date, a.party_id, b.order_id, b.process_id, b.item_id order by a.bill_no, a.bill_date";
                //echo $sql;
                $production_sql= sql_select($sql);
                foreach( $production_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					if ($row[csf("process_id")]==1 || $row[csf("process_id")]==5)
					{
						$item_name=$garments_item[$row[csf('item_id')]];
					}
					else if ($row[csf("process_id")]==2)
					{
						$item_name=$kniting_item_arr[$row[csf('item_id')]];
					}
					else if ($row[csf("process_id")]==3 || $row[csf("process_id")]==4)
					{
						$item_name=$dye_fin_item_arr[$row[csf('item_id')]];
					}
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="100"><? echo $row[csf("bill_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("bill_date")]);?> </td> 
                    <td width="100"><p><? echo $party_arr[$row[csf("party_id")]]; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $production_process[$row[csf("process_id")]]; ?></td>
                    <td align="center" width="150"><? echo $item_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="right" width=""><? echo number_format($row[csf("amount")],2); ?></td>
                </tr>
                <? 
                $tot_qty+=$row[csf("quantity")];
                $tot_amount+=$row[csf("amount")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="7" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    <td align="right"><p><? echo number_format($tot_amount,2); ?></p></td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
<?
exit();
}

if($action=="image_view_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Work Progress Info","../../../", 1, 1, $unicode);
	//echo "select master_tble_id,image_location from common_photo_library where form_name='sub_contract_order_entry' and file_type=1 and master_tble_id='$id'";

	$imge_data=sql_select("SELECT id,master_tble_id,image_location from common_photo_library where form_name='sub_contract_order_entry' and file_type=1 and master_tble_id='$id'");
	?>
	<table>
        <tr>
			<?
            foreach($imge_data as $row)
            {
				?>
                    <td><img src='../../../<? echo $imge_arr[$row[csf("id")]]; ?>' height='100px' width='100px' /></td>
				<?
            }
            ?>
        </tr>
	</table>
	<?
	exit();
}

if($action=="batch_qty_pop_up")
{
	echo load_html_head_contents("Batch Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	$order_id=$expData[0];
	//$process_id=$expData[1];
	$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
	$po_arr=return_library_array( "SELECT id, order_no from subcon_ord_dtls where status_active =1 and is_deleted=0",'id','order_no');
	?>
    <fieldset style="width:800px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="80">Batch No</th>
                        <th width="30">Ext.</th>
                        <th width="65">Batch Date</th>
                        <th width="100">Color</th>
                        <th width="100">Order</th>
                        <th width="100">Rec. Challan</th>
                        <th width="180">Description</th>
                        <th width="">Batch Qty</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:330px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >    
			<?
				$sql_batch="SELECT a.batch_no, a.extention_no, a.batch_date, a.color_id, b.po_id, b.item_description, b.rec_challan, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=36 and b.po_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.batch_no, a.extention_no, a.batch_date, a.color_id, b.po_id, b.item_description, b.rec_challan";
				$sql_batch_result=sql_select($sql_batch); $i=0;
				foreach ($sql_batch_result as $row)
				{
					$i++;
					if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="30"><? echo $i; ?></td>
						<td width="80" align="center"><? echo $row[csf("batch_no")];?> </td>
						<td width="30" align="center"><? echo $row[csf("extention_no")];?> </td>
						<td width="65"><? echo change_date_format($row[csf("batch_date")]);?> </td> 
						<td width="100"><p><? echo $color_arr[$row[csf("color_id")]];?></p></td>
						<td width="100"><? echo $po_arr[$row[csf("po_id")]]; ?></td>
						<td width="100"><p><? echo $row[csf("rec_challan")]; ?></p></td>
						<td width="180"><p><? echo $row[csf("item_description")]; ?></p></td>
						<td align="right" width=""><? echo number_format($row[csf("batch_qnty")],2); ?></td>
					</tr>
					<?
					$tot_batch_qnty+=$row[csf("batch_qnty")];
				}
				?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_batch_qnty,2); ?></p></td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
<?
exit();
}

if($action=="payment_rec_pop_up")
{
	echo load_html_head_contents("Payment Receive Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	$order_id=$expData[0];
	$order_bill_amount=$expData[1];
	//$process_id=$expData[1];
	$po_arr=return_library_array( "SELECT id, order_no from subcon_ord_dtls where status_active =1 and is_deleted=0",'id','order_no');
	$buyer_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
	?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Rec. No</th>
                        <th width="120">Party</th>
                        <th width="65">Rec. Date</th>
                        <th width="80">Instrument</th>
                        <th width="60">Currency</th>
                        <th width="120">Bill No</th>
                        <th width="80">Order No</th>
                        <th width="65">Bill Date</th>
                        <th width="">Rec. Amount</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:330px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >    
			<?
			$order_wise_tot_bill="SELECT a.id, d.order_id, b.bill_id, b.bill_amount as bill_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.order_id='$order_id' and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 order by a.id asc";
			$order_wise_tot_bill_result=sql_select($order_wise_tot_bill);
			foreach ($order_wise_tot_bill_result as $row)
			{
				$order_wise_tot_bill_arr2[$row[csf('order_id')]][$row[csf('bill_id')]][$row[csf('id')]]=$row[csf('bill_amount')];
			}

			$sum=0;
			foreach ($order_wise_tot_bill_arr2 as $key=>$value) 
			{
				foreach ($value as $val) 
				{
					foreach ($val as $val2) 
					{
						 $sum+=$val2;
						 break;
					}
				}
				$order_wise_tot_bill_arr[$key]=$sum;
				$sum=0;
			}

				//$payment_sql="select a.prefix_no_num, a.receive_no, a.party_name, a.receipt_date, a.instrument_id, a.currency_id, c.bill_no, c.bill_date, d.order_id, sum(b.total_adjusted) as rec_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and d.order_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.prefix_no_num, a.receive_no, a.party_name, a.receipt_date, a.instrument_id, a.currency_id, c.bill_no, c.bill_date, d.order_id";

			$payment_sql="SELECT a.prefix_no_num, a.receive_no, a.party_name, a.receipt_date, a.instrument_id, a.currency_id, c.bill_no, c.bill_date, d.order_id, b.total_adjusted as rec_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and d.order_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.prefix_no_num, a.receive_no, a.party_name, a.receipt_date, a.instrument_id, a.currency_id, c.bill_no, c.bill_date, d.order_id,b.total_adjusted";

				$payment_sql_result=sql_select($payment_sql); $i=0;
				foreach ($payment_sql_result as $row)
				{
					$i++;
					if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="30"><? echo $i; ?></td>
						<td width="100" align="center"><? echo $row[csf("receive_no")];?> </td>
						<td width="120" align="center"><? echo $buyer_arr[$row[csf("party_name")]];?> </td>
						<td width="65"><? echo change_date_format($row[csf("receipt_date")]);?> </td> 
						<td width="80"><p><? echo $instrument_payment[$row[csf("instrument_id")]];?></p></td>
						<td width="60"><? echo $currency[$row[csf("currency_id")]]; ?></td>
						<td width="120"><p><? echo $row[csf("bill_no")]; ?></p></td>
                        <td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
						<td width="65"><? echo change_date_format($row[csf("bill_date")]);?> </td> 
						<td align="right" width="">
							<? 
							$received_amount = ($row[csf("rec_amount")]/$order_wise_tot_bill_arr[$order_id])*$order_bill_amount;
							echo number_format($received_amount,2); 
							
							//echo number_format($row[csf("rec_amount")],2); 
							?>
						</td>
					</tr>
					<?
					$tot_rec_amount+=$received_amount;
				}
				?>
                <tr class="tbl_bottom">
                    <td colspan="9" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_rec_amount,2); ?></p></td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
<?
exit();
}


if($action=="order_desc_popup")
{
	echo load_html_head_contents("Order Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Order Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Order No</th>
                        <th width="70">Category</th>
                        <th width="120">Item Description </th>
                        <th width="80">Color</th>
                        <th width="60">Size</th>
                        <th width="80">Receive Date</th>
                        <th width="50">Rate</th>
                        <th width="93">Quantity</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "SELECT id, short_name from lib_supplier where status_active =1 and is_deleted=0",'id','short_name');
				//$company_array=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

                $item_arr=return_library_array( "SELECT id,const_comp from lib_subcon_charge where status_active =1 and is_deleted=0",'id','const_comp');	
				$size_arr=return_library_array( "SELECT id,size_name from  lib_size where status_active =1 and is_deleted=0",'id','size_name');
				$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
                $i=0;

                $sql="SELECT a.party_id, b.order_no, b.order_rcv_date, b.main_process_id, c.item_id, c.color_id, c.size_id, c.qnty, c.rate, c.gsm, c.grey_dia, c.finish_dia, c.dia_width_type from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where c.mst_id=a.id and c.order_id=b.id and a.subcon_job=b.job_no_mst and b.id=$expData[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
              
                $order_dtls_sql= sql_select($sql);
                foreach( $order_dtls_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";

                    $process_id=$row[csf('main_process_id')];
					
						//$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("order_no")];?> </td>
                    <td align="center" width="70"><? echo $production_process[$row[csf("main_process_id")]];?> </td>
                    <td width="120">
                    	<? 
			                if($process_id==2 || $process_id==3 || $process_id==4 || $process_id==6 || $process_id==7)
							{
								echo $item_arr[$row[csf('item_id')]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("finish_dia")];	
							}
							else
							{
								echo $garments_item[$row[csf('item_id')]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("finish_dia")];
							}
                    	?> 
                    </td> 
                    <td align="center" width="80"><p><? echo  $color_arr[$row[csf("color_id")]]; ?></p></td>
                    <td align="center" width="60"><? echo $size_arr[$row[csf("size_id")]]; ?></td>
                    <td align="center" width="80"><? echo change_date_format($row[csf("order_rcv_date")]); ?></td>
                    
                    <td align="right" width="50"><? echo $row[csf("rate")]; ?> &nbsp; </td>
                    <td align="right" width="80"><? echo number_format($row[csf("qnty")]); ?> &nbsp;</td>
                   
                </tr>
                <? 
                $tot_qty+=$row[csf("qnty")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: &nbsp;</td>
                    <td align="right"><p><? echo number_format($tot_qty); ?> &nbsp; </p></td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
<?
exit();
}

?>