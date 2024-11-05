<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');


$company_array= return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
$buyer_short_name_arr=return_library_array( "SELECT id, short_name from lib_buyer where status_active =1 and is_deleted=0",'id','short_name');
$company_short_name_arr=return_library_array( "SELECT id,company_short_name from lib_company where status_active =1 and is_deleted=0",'id','company_short_name');
$imge_arr=return_library_array( "SELECT id,master_tble_id,image_location from common_photo_library where status_active =1 and is_deleted=0",'id','image_location');
$party_arr=return_library_array( "SELECT id, buyer_name from  lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
$buyer_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');

if ($action=="load_drop_down_buyer")
{
	//echo $data; die;
	$data=explode('_',$data);
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_buyer_id", 125, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "");
		exit();
	}
	else
	{
		echo create_drop_down( "cbo_buyer_id", 125, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$data[2], "" );
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
			load_drop_down( 'printing_daily_chemical_uses_costing_report_controller', company+'_'+within_group+'_'+party, 'load_drop_down_buyer', 'buyer_td' );
			
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
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $data[2];?>, 'create_job_search_list_view', 'search_div', 'printing_daily_chemical_uses_costing_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
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
			else if ($search_by==4) $style_cond1=" and b.buyer_style_ref = '$search_str' ";
			else if ($search_by==5) $po_cond=" and b.po_number = '$search_str' ";
			else if ($search_by==5) $po_cond1=" and b.buyer_po_no = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num = '$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $style_cond=" and a.style_ref_no like '%$search_str%'";  
			else if ($search_by==4) $style_cond1=" and b.buyer_style_ref like '%$search_str%'";  
			else if ($search_by==5) $po_cond=" and b.po_number like '%$search_str%'";  
			else if ($search_by==5) $po_cond1=" and b.buyer_po_no like '%$search_str%'";  
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num = '$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $style_cond=" and a.style_ref_no like '$search_str%'";  
			else if ($search_by==4) $style_cond1=" and b.buyer_style_ref like '$search_str%'";  
			else if ($search_by==5) $po_cond=" and b.po_number like '$search_str%'";  
			else if ($search_by==5) $po_cond1=" and b.buyer_po_no like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num = '$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $style_cond=" and a.style_ref_no like '%$search_str'";  
			else if ($search_by==4) $style_cond1=" and b.buyer_style_ref like '%$search_str'";  
			else if ($search_by==5) $po_cond=" and b.po_number like '%$search_str'";  
			else if ($search_by==5) $po_cond1=" and b.buyer_po_no like '%$search_str'";  
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
	if ($withinGroup==1) {
		
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

	}
	else
	{

		
		if($db_type==0) $id_cond="group_concat(b.id)";
		else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
		if( ($style_cond1!="" && $search_by==4)|| ($po_cond1!="" && $search_by==5))
		{
			//$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
			$po_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.embellishment_job=b.job_no_mst $style_cond1 $po_cond1 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
		}
		
		if ( $po_ids!="") $po_idsCond=" and b.id in ($po_ids)"; else $po_idsCond="";



	}

	//------

	if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2))
	{
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.embellishment_job=b.job_no_mst $search_com_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
	}
	
	//if ( $spo_ids!="") $spo_idsCond=" and b.order_id in ($spo_ids)"; else $spo_idsCond="";
	if ( $spo_ids!="") $spo_idsCond=" and b.id in ($spo_ids)"; else $spo_idsCond="";


	
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
	 where a.entry_form=204 and a.embellishment_job=b.job_no_mst  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $spo_idsCond and b.id=c.mst_id  
	 group by a.id, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, b.buyer_po_id
	 order by a.id DESC"; //and b.booking_dtls_id!=0

	//echo $sql;//die;

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
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('embellishment_job')].'_'.$row[csf('id')]; ?>")' style="cursor:pointer" >
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

if ($action=="order_popup")
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
			load_drop_down( 'printing_daily_chemical_uses_costing_report_controller', company+'_'+within_group+'_'+party, 'load_drop_down_buyer', 'buyer_td' );
			
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
                            <th width="100" id="search_by_td">W/O No</th>
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
									echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",2,'search_by(this.value)',0 );
								?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $data[2];?>, 'create_order_search_list_view', 'search_div', 'printing_daily_chemical_uses_costing_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
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

if($action=="create_order_search_list_view")
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
	 where a.entry_form=204 and a.embellishment_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond and b.id=c.mst_id  
	 group by a.id, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, b.buyer_po_id
	 order by a.id DESC"; //and b.booking_dtls_id!=0

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
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('order_no')].'_'.$row[csf('id')]; ?>")' style="cursor:pointer" >
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
	$process = array( &$_POST );
	//var_dump($process);
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_within_group=str_replace("'","",$cbo_within_group);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$cbo_buyer_buyer=str_replace("'","",$cbo_buyer_buyer);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_int_ref=str_replace("'","",$txt_int_ref);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$txt_buyer_po=str_replace("'","",$txt_buyer_po);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_recipe=str_replace("'","",$txt_recipe);
	$txt_color=str_replace("'","",$txt_color);
	$type=str_replace("'","",$type);
	$cbo_year_selection=str_replace("'","",$cbo_year_selection);
	$cbo_year=str_replace("'","",$cbo_year);

	
	
	$query_cond='';
	if($cbo_company_id!=0) $query_cond.=" and f.company_id ='$cbo_company_id'"; 
	if($cbo_buyer_id!=0) $query_cond.=" and f.party_id ='$cbo_buyer_id'";
	if($cbo_within_group!=0) $query_cond.=" and f.within_group ='$cbo_within_group'";  
	if($txt_job_no!="") $query_cond.=" and f.embellishment_job like '%$txt_job_no%'";
	if($txt_order_no!="") $query_cond.=" and f.order_no like '%$txt_order_no%'";

	if($txt_buyer_po!="") $query_cond.=" and g.buyer_po_no like '%$txt_buyer_po%'";
	if($txt_style_ref!="") $query_cond.=" and g.buyer_style_ref like '%$txt_style_ref%'";
	if($txt_style_ref!="") $query_cond.=" and g.buyer_style_ref like '%$txt_style_ref%'";
	//if($job_year_selection!="") $year_cond=" and to_char(a.insert_date,'YYYY')=$job_year_selection";


	if($db_type==0)
	{
		if(str_replace("'","",$cbo_year)!=0) $year_cond=" and year(a.insert_date)=".str_replace("'","",$cbo_year).""; else $year_cond="";
	}
	else
	{
		if(str_replace("'","",$cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$cbo_year).""; else $year_cond="";
	}
	
	//echo $year_cond; die;
	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond=""; else $date_cond=" and a.issue_date between $txt_date_from and $txt_date_to";
	if($cbo_within_group==1){
		$party_arr=return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
	}else{
		$party_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
	}
	$group_arr=return_library_array( "SELECT id,item_name from lib_item_group where  status_active=1 and is_deleted=0",'id','item_name');
	$color_arr=return_library_array("SELECT id,color_name from lib_color where status_active=1 and is_deleted=0" ,"id","color_name");
	$recipe_arr=return_library_array( "SELECT id,recipe_no from pro_recipe_entry_mst where  company_id=$cbo_company_id and entry_form=220 and  status_active=1 and is_deleted=0",'id','recipe_no');
	if($txt_recipe!='')
	{
		
		//echo "SELECT a.id, a.recipe_no, a.store_id,po_ids as po_id from pro_recipe_entry_mst a where a.company_id=$cbo_company_id and a.entry_form=220 and a.status_active =1 and a.is_deleted=0 and a.recipe_no like '%$txt_recipe%' "; die;
		
		$recipe_sql=sql_select("SELECT a.id, a.recipe_no, a.store_id,po_ids as po_id from pro_recipe_entry_mst a where a.company_id=$cbo_company_id and a.entry_form=220 and a.status_active =1 and a.is_deleted=0 and a.recipe_no like '%$txt_recipe%' ");
		//echo "SELECT a.id, a.recipe_no, a.store_id from pro_recipe_entry_mst a where a.company_id=$cbo_company_id and a.entry_form=220 and a.status_active =1 and a.is_deleted=0 and a.recipe_no like '%$txt_recipe%' "; die;
		foreach( $recipe_sql as $row)
		{
			$recipe_id.=$row[csf("id")].',';
		}
		
		$recipe_id=rtrim($recipe_id,",");
		 //echo $recipe_id; die;
		$recipe_cond=''; $recipe_cond2='';
		if($recipe_id!='')
		{
			$recipe_id=array_chunk(array_unique(explode(",",$recipe_id)),999, true);
			$ji=0;
			foreach($recipe_id as $key=> $value)
			{
			   if($ji==0)
			   {
					$recipe_cond=" and d.recipe_id in('".implode("','",$value)."')";
					$recipe_cond2=" and d.id in('".implode("','",$value)."')";
			   }
			   else
			   {
					$recipe_cond.=" or d.recipe_id  in('".implode("','",$value)."')";
					$recipe_cond2.=" or d.id  in('".implode("','",$value)."')";
			   }
			   $ji++;
			}
			$all_po_ids .=$row[csf('po_id')].',';
		}
	}else{
		$recipe_sql=sql_select("SELECT po_ids as po_id from pro_recipe_entry_mst where status_active=1 and is_deleted=0 and entry_form=220");
		foreach($recipe_sql as $row)
		{
			$all_po_ids .=$row[csf('po_id')].',';
			//$issue_data_arr[$row[csf('job_break_id')]]['quantity']=$row[csf('quantity')];
		}
	}
 //echo $recipe_cond; die;
 	$sub_process_cond2='';
	if($txt_color!='')
	{
		$color_sql=sql_select("SELECT a.id, a.color_name from lib_color a where a.status_active =1 and a.is_deleted=0 and a.color_name like '%$txt_color%' ");
		//echo "SELECT a.id, a.recipe_no, a.store_id from pro_recipe_entry_mst a where a.company_id=$cbo_company_id and a.entry_form=220 and a.status_active =1 and a.is_deleted=0 and a.recipe_no like '%$txt_recipe%' "; die;
		foreach( $color_sql as $row)
		{
			$color_id.=$row[csf("id")].',';
		}
		//echo $color_id; die;
		$color_id=rtrim($color_id,","); $sub_process_cond='';
		if($color_id!='')
		{
			$color_id=array_chunk(array_unique(explode(",",$color_id)),999, true);
			$ji=0;
			foreach($color_id as $key=> $value)
			{
			   if($ji==0)
			   {
					$sub_process_cond=" and d.sub_process in(".implode(",",$value).")";
					$sub_process_cond2=" and d.color_id in(".implode(",",$value).")";
			   }
			   else
			   {
					$sub_process_cond.=" or d.sub_process  in(".implode(",",$value).")";
					$sub_process_cond2.=" or d.color_id  in(".implode(",",$value).")";
			   }
			   $ji++;
			}
		}
	}
	if($type==1)
	{
		// and a.id in(".implode(",",$all_recipe_id).")
		/* echo "Test";
		select lib_company.company_name, lib_company1.company_name as party_name, lib_buyer.buyer_name, lib_location.location_name, lib_garment_item.item_name, lib_color.color_name, lib_size.size_name, wo_po_break_down.po_number, wo_po_details_master.style_ref_no, mst.company_id, mst.location_id, mst.party_id, mst.receive_date, mst.delivery_date, mst.job_no_prefix_num, mst.job_no_mst, mst.order_no, mst.po_delivery_date, mst.order_id, mst.item_id, mst.color_id, mst.size_id, mst.qnty, mst.rate, mst.amount, mst.buyer_po_id, mst.gmts_item_id, mst.embl_type, mst.body_part from (select som.company_id, som.location_id, som.party_id, som.receive_date, som.delivery_date, som.job_no_prefix_num, sod.job_no_mst, sod.order_no, sod.delivery_date as po_delivery_date, sob.order_id, sob.item_id, sob.color_id, sob.size_id, sob.qnty, sob.rate, sob.amount, sod.buyer_po_id, sod.gmts_item_id, sod.embl_type, sod.body_part from subcon_ord_mst som inner join subcon_ord_dtls sod on som.id = sod.mst_id inner join subcon_ord_breakdown sob on sod.id = sob.mst_id where som.company_id = 3 and som.job_no_prefix_num = 11 and som.status_active = 1 ) mst inner join lib_company on mst.company_id = lib_company.id inner join lib_location on lib_location.id = mst.location_id inner join lib_garment_item on mst.item_id = lib_garment_item.id inner join lib_color on mst.color_id = lib_color.id inner join lib_size on mst.size_id = lib_size.id inner join wo_po_break_down on mst.buyer_po_id = wo_po_break_down.id inner join wo_po_details_master on wo_po_break_down.job_no_mst = wo_po_details_master.job_no inner join lib_buyer on lib_buyer.id = wo_po_details_master.buyer_name inner join lib_company lib_company1 on lib_company1.id = mst.party_id 
		*/
		//--------------------------------------------------------Start----------------------------------------
		/*$sql = sql_select("SELECT a.location_id, a.issue_date, a.issue_basis, a.req_no, a.req_id, a.issue_purpose, a.company_id, a.loan_party, a.lap_dip_no, a.batch_no, a.order_id, a.sub_order_id, a.style_ref, a.store_id, a.buyer_job_no, a.is_posted_account, a.lc_company, a.floor_id, a.machine_id, a.remarks, b.product_id, b.req_qny_edit from inv_issue_master a, dyes_chem_issue_dtls b, subcon_ord_mst c where a.id=$data and a.entry_form=250 and a.status_active =1 and a.is_deleted=0");*/

		/*$sql ="SELECT a.requ_no, a.requ_prefix_num, a.company_id, a.requisition_date, a.requisition_basis, a.recipe_id, a.id, a.order_id, a.buyer_po_id
		from dyes_chem_issue_requ_mst a where a.company_id=$company and a.is_apply_last_update!=2 and a.entry_form=221 and a.status_active=1 and a.is_deleted=0 and a.recipe_id!=$null_cond $req_cond";

		$sql2="SELECT b.id, b.sub_process as sub_process_id, a.store_id from inv_transaction a, dyes_chem_issue_dtls b where a.id=b.trans_id and a.transaction_type=2 and b.mst_id='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id";

		$sql3="SELECT a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.id as dtls_id, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty as required_qnty, b.req_qny_edit,b.item_lot 
						from product_details_master a, dyes_chem_issue_requ_dtls b where a.id=b.product_id and b.mst_id='".$req_id."' and b.multicolor_id=$sub_process_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' and a.item_category_id in(5,6,7,23,22) and a.status_active=1 and a.is_deleted=0 order by a.item_category_id ";

		$total_issue_sql4=sql_select("SELECT a.issue_number, b.product_id, b.req_qny_edit as req_qny_edit from inv_issue_master a, dyes_chem_issue_dtls b where a.id=b.mst_id and a.entry_form=250 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and a.issue_basis=7 and a.req_id='".$req_id."' and b.sub_process=$sub_process_id");*/

		//e.subcon_job=f.job_no_mst
		//f.mst_id=e.id
		$query = "SELECT a.location_id, a.issue_date, a.issue_basis, a.req_no, a.req_id, a.issue_purpose, a.company_id, a.loan_party, a.lap_dip_no, a.batch_no, a.order_id, a.sub_order_id, a.style_ref, a.store_id, a.buyer_job_no, a.is_posted_account, a.lc_company, a.floor_id, a.machine_id, a.remarks, b.id,a.issue_number,b.store_id,b.cons_uom, b.cons_quantity, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id,b.cons_rate,b.cons_amount,c.item_description, c.item_group_id, c.sub_group_name, c.item_size, d.sub_process,d.id as issue_dtls_id, d.item_category, d.dose_base, d.ratio, d.recipe_qnty, d.adjust_percent, d.adjust_type, d.required_qnty, d.req_qny_edit, d.recipe_id,e.embellishment_job,e.party_id ,e.order_no, f.buyer_po_no,f.buyer_style_ref,f.body_part,f.embl_type,f.order_quantity, e.within_group,f.buyer_buyer,f.order_uom, g.grouping 
		from inv_issue_master a, inv_transaction b, product_details_master c, dyes_chem_issue_dtls d, subcon_ord_mst e , subcon_ord_dtls f

		left join wo_po_break_down g on f.buyer_po_id=g.id and g.is_deleted=0 and g.status_active=1
		left join wo_po_details_master h on g.job_no_mst=h.job_no and h.is_deleted=0 and h.status_active=1 
	    where a.id=d.mst_id and b.id =d.trans_id and d.product_id=c.id and a.sub_order_id=f.id and f.mst_id=e.id and a.buyer_job_no=e.order_no and e.entry_form=204  and b.transaction_type=2 and a.entry_form=250 and b.item_category in (5,6,7,23,22) and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0 and f.status_active =1 and f.is_deleted =0 $date_cond $query_cond $recipe_cond $sub_process_cond order by a.id ";
	    

		//echo $query;
		$sql_data_query = sql_select($query);
		$countRecords = count($query); 
		//echo $sql_data_query;
		ob_start();
		$details_data=array();
		
		foreach( $sql_data_query as $row)
		{
			//detail data in Array  
			$details_data[$row[csf("company_id")]][$row[csf("issue_date")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_po_no")]][$row[csf("buyer_style_ref")]][$row[csf("body_part")]][$row[csf("embl_type")]][$row[csf("order_quantity")]][$row[csf("item_group_id")]][$row[csf("item_description")]][$row[csf("buyer_buyer")]][$row[csf("order_uom")]][$row[csf("grouping")]]["recipe_qnty"] +=$row[csf(recipe_qnty)];
			$details_data[$row[csf("company_id")]][$row[csf("issue_date")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_po_no")]][$row[csf("buyer_style_ref")]][$row[csf("body_part")]][$row[csf("embl_type")]][$row[csf("order_quantity")]][$row[csf("item_group_id")]][$row[csf("item_description")]][$row[csf("buyer_buyer")]][$row[csf("order_uom")]][$row[csf("grouping")]]["req_qny_edit"] +=$row[csf(req_qny_edit)];
			$details_data[$row[csf("company_id")]][$row[csf("issue_date")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_po_no")]][$row[csf("buyer_style_ref")]][$row[csf("body_part")]][$row[csf("embl_type")]][$row[csf("order_quantity")]][$row[csf("item_group_id")]][$row[csf("item_description")]][$row[csf("buyer_buyer")]][$row[csf("order_uom")]][$row[csf("grouping")]]["required_qnty"] +=$row[csf(required_qnty)];
			$details_data[$row[csf("company_id")]][$row[csf("issue_date")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_po_no")]][$row[csf("buyer_style_ref")]][$row[csf("body_part")]][$row[csf("embl_type")]][$row[csf("order_quantity")]][$row[csf("item_group_id")]][$row[csf("item_description")]][$row[csf("buyer_buyer")]][$row[csf("order_uom")]][$row[csf("grouping")]]["recipe_id"] =$row[csf(recipe_id)];
			$details_data[$row[csf("company_id")]][$row[csf("issue_date")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_po_no")]][$row[csf("buyer_style_ref")]][$row[csf("body_part")]][$row[csf("embl_type")]][$row[csf("order_quantity")]][$row[csf("item_group_id")]][$row[csf("item_description")]][$row[csf("buyer_buyer")]][$row[csf("order_uom")]][$row[csf("grouping")]]["cons_rate"] =$row[csf(cons_rate)];
			$details_data[$row[csf("company_id")]][$row[csf("issue_date")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_po_no")]][$row[csf("buyer_style_ref")]][$row[csf("body_part")]][$row[csf("embl_type")]][$row[csf("order_quantity")]][$row[csf("item_group_id")]][$row[csf("item_description")]][$row[csf("buyer_buyer")]][$row[csf("order_uom")]][$row[csf("grouping")]]["cons_amount"] +=$row[csf(cons_amount)];
			$details_data[$row[csf("company_id")]][$row[csf("issue_date")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_po_no")]][$row[csf("buyer_style_ref")]][$row[csf("body_part")]][$row[csf("embl_type")]][$row[csf("order_quantity")]][$row[csf("item_group_id")]][$row[csf("item_description")]][$row[csf("buyer_buyer")]][$row[csf("order_uom")]][$row[csf("grouping")]]["sub_process"] =$row[csf(sub_process)];
			$details_data[$row[csf("company_id")]][$row[csf("issue_date")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_po_no")]][$row[csf("buyer_style_ref")]][$row[csf("body_part")]][$row[csf("embl_type")]][$row[csf("order_quantity")]][$row[csf("item_group_id")]][$row[csf("item_description")]][$row[csf("buyer_buyer")]][$row[csf("order_uom")]][$row[csf("grouping")]]["issue_dtls_id"] .=$row[csf(issue_dtls_id)].',';
			$details_data[$row[csf("company_id")]][$row[csf("issue_date")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_po_no")]][$row[csf("buyer_style_ref")]][$row[csf("body_part")]][$row[csf("embl_type")]][$row[csf("order_quantity")]][$row[csf("item_group_id")]][$row[csf("item_description")]][$row[csf("buyer_buyer")]][$row[csf("order_uom")]][$row[csf("grouping")]]["within_group"] =$row[csf(within_group)];
		}
		//echo "<pre>";
		//print_r($details_data);
		?>
		<style type="text/css">
			.brk_word {
			  word-wrap: break-word;
			  word-break: break-all;
			}
		</style>
		<div style="width:2200px; margin:0 auto;">
			<fieldset style="width:100%;">	
			    <table width="98%" cellpadding="0" cellspacing="0" id="caption">
		            <tr>  
		                <td align="center" width="100%" colspan="11" class="form_caption" >
		                	<strong style="font-size:18px">Daily Chemical Uses and Costing Report</strong>
		                </td>
		            </tr>
		        </table>
				<div style="width:2200px;" align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">	
					<table width="2180"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" rules="all">
						<thead><!-- Date	Job Number	Order No	Buyer	Buyer PO Number	Buyer Style	Body Color	Boady Part	Print Type	Print Name	Order/Qty	Chemical Group	Chemical Name	Recipe Qty	Requisiton Qty	 Issue Qty / Kg 	 Rate / Taka 	 Amount / Tk 	Recipie No -->

							<th class="brk_word" width="30" align="center">Sl </th>
							<th class="brk_word" width="60" align="center">Date </th>
							<th class="brk_word" width="120" align="center">Job Number </th>
							<th class="brk_word" width="120" align="center">Order No </th>
							<th class="brk_word" width="120" align="center">Internal Ref </th>
							<th class="brk_word" width="150" align="center">Buyer</th>
							<th class="brk_word" width="150" align="center">Buyer's Buyer</th>
							<th class="brk_word" width="60" align="center">UOM</th>
							<th class="brk_word" width="100" align="center">Buyer PO Number</th>
							<th class="brk_word" width="85" align="center">Buyer Style</th>
							<th class="brk_word" width="85" align="center">Body Color</th>
							<th class="brk_word" width="100" align="center">Boady Part</th>
							<th class="brk_word" width="85" align="center">Print Type</th>
							<th class="brk_word" width="65" align="center">Print Name</th>
							<th class="brk_word" width="85" align="center">Order Qty.</th>
							<th class="brk_word" width="85" align="center">Chemical Group</th>
							<th class="brk_word" width="120" align="center">Chemical Name</th>
							<th class="brk_word" width="85" align="center">Recipe Qty</th>
							<th class="brk_word" width="85" align="center">Requisiton Qty</th>
							<th class="brk_word" width="85" align="center">Issue Qty / Kg</th>
							<th class="brk_word" width="85" align="center">Rate / Taka</th>
							<th class="brk_word" width="85" align="center">Amount / Tk</th>
							<th class="brk_word" width="85" align="center">Recipie No </th>
						</thead>
					</table>
				</div>
				<div style="width:2200px;" align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">	
					<table width="2180"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<tbody>
							<?	
							$k=1;$total_rate=$total_qty=$total_amount=$total_recipie_qty=$total_req_qty=0;
							foreach($details_data as $company_id=>$company_data)
							{
								foreach($company_data as $issue_date=>$issue_date_data)
								{
									foreach($issue_date_data as $embellishment_job=>$embellishment_job_data)
									{
										foreach($embellishment_job_data as $order_no=>$order_no_data)
										{
											$buyer_wise_rate=$buyer_wise_qty=$buyer_wise_amount=$buyer_wise_recipie_qty=$buyer_wise_req_qty=0;
											foreach($order_no_data as $party_id=>$party_id_data)
											{
												foreach($party_id_data as $buyer_po_no=>$buyer_po_data)
												{
													$style_wise_rate=$style_wise_qty=$style_wise_amount=$style_wise_recipie_qty=$style_wise_req_qty=0;
													foreach($buyer_po_data as $buyer_style_ref=>$buyer_style_data)
													{
														foreach($buyer_style_data as $body_part_id=>$body_part_data)
														{
															foreach($body_part_data as $embl_type=>$embl_type_data)
															{
																foreach($embl_type_data as $order_quantity=>$order_quantity_data)
																{
																	foreach($order_quantity_data as $item_group_id=>$item_group_data)
																	{
																		foreach($item_group_data as $item_description=>$item_description_data)
																		{

																			foreach ($item_description_data as $buyer_buyer => $buyer_buyer_data) 
																			{
																				foreach ($buyer_buyer_data as $order_uom => $order_uom_data) 
																				{
																					foreach ($order_uom_data as $grouping => $row) 
																					{
																						
																				
																						if ($k%2==0)  
																						$bgcolor="#E9F3FF";
																						else
																						$bgcolor="#FFFFFF";
																						$issue_dtls_id=chop($row[('issue_dtls_id')],',');

																						if($row['within_group']==1) 
																						{
																							$partyarr = $company_array;
																							$buyerArr = $buyer_arr;
																						}
																						else
																						{
																							$partyarr = $party_arr;
																							$buyerArr = $buyer_buyer;
																						}
																						
																						?>
																						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k;?>">
																							<!-- Date	Job Number	Order No	Buyer	Buyer PO Number	Buyer Style	Body Color	Boady Part	Print Type	Print Name	Order/Qty	Chemical Group	Chemical Name	Recipe Qty	Requisiton Qty	 Issue Qty / Kg 	 Rate / Taka 	 Amount / Tk 	Recipie No -->
																							<td class="brk_word" width="30" align="center"><? echo $k; ?> </td>
																							<td class="brk_word" width="60" align="center"><? echo $issue_date; ?></td>
																							<td class="brk_word" width="120" align="center"><? echo $embellishment_job; ?></td>
																							<td class="brk_word" width="120" align="center"><? echo $order_no; ?></td>
																							<td class="brk_word" width="120" align="center">
																							<?
																							if($row['within_group']==1) 
																							{
																							
																								echo $grouping; 
																							}
																							 ?>
																							</td>
																							<td class="brk_word" width="150" align="center">
																							<?
																							echo $partyarr[$party_id]; 
																							?>
																							</td>
																							<td class="brk_word" width="150" align="center">
																							<?

																								if($row['within_group']==1) 
																								{
																								
																									echo $buyer_arr[$buyer_buyer];
																								}
																								else
																								{
																									echo $buyer_buyer;
																								}
																							//echo $buyerArr[$buyer_buyer]; 
																							// if($cbo_within_group==1){echo $buyer_arr[$buyer_buyer];}else{echo $buyerArr;} 
																							?>
																							</td>
																							<td class="brk_word" width="60" align="center">
																							<? echo $unit_of_measurement[$order_uom]; ?>
																							</td>
																							<td class="brk_word" width="100" align="center"><? echo $buyer_po_no; ?></td>
																							<td class="brk_word" width="85" align="center"><? echo $buyer_style_ref; ?></td>
																							<td class="brk_word" width="85" align="center"><? echo $color_arr[$row[('sub_process')]]; ?></td>
																							<td class="brk_word" width="100" align="center"><? echo $body_part[$body_part_id]; ?></td>
																							<td class="brk_word" width="85" align="center"><? echo $emblishment_print_type[$embl_type]; ?></td>
																							<td class="brk_word" width="65" align="center"><? echo 'Printing'; ?></td>
																							<td class="brk_word" width="85" align="right"><? echo $order_quantity; ?></td>
																							<td class="brk_word" width="85" align="center"><? echo $group_arr[$item_group_id]; ?></td>
																							<td class="brk_word" width="120" align="center"><? echo $item_description; ?></td>
																							<td class="brk_word" width="85" align="right"><? echo number_format($row[('recipe_qnty')],2); ?></td>
																							<td class="brk_word" width="85" align="right"><? echo number_format($row[('required_qnty')],2); ?></td>
																							<td class="brk_word" width="85" align="right">
																								<a href="##" onClick="openmypage_qty('<? echo $issue_dtls_id; ?>','issue_qty_popup')">
																									<? echo number_format($row[('req_qny_edit')],2);?>
																								</a>
																							</td>
																							<td class="brk_word" width="85" align="right"><? echo number_format($row[('cons_rate')],2); ?></td>
																							<td class="brk_word" width="85" align="right"><? echo number_format($row[('cons_amount')],2); ?></td>
																							<td class="brk_word" width="85" align="center"><? echo $recipe_arr[$row[('recipe_id')]]; ?> </td>
																						</tr>
																						<?
																						$k++;
																						$total_recipie_qty+=$row[('recipe_qnty')];
																						$total_req_qty+=$row[('required_qnty')];
																						$total_qty+=$row[('req_qny_edit')];
																						$total_rate+=$row[('cons_rate')];
																						$total_amount+=$row[('cons_amount')];

																						$style_wise_recipie_qty+=$row[('recipe_qnty')];
																						$style_wise_req_qty+=$row[('required_qnty')];
																						$style_wise_qty+=$row[('req_qny_edit')];
																						$style_wise_rate+=$row[('cons_rate')];
																						$style_wise_amount+=$row[('cons_amount')];

																						$buyer_wise_recipie_qty+=$row[('recipe_qnty')];
																						$buyer_wise_req_qty+=$row[('required_qnty')];
																						$buyer_wise_qty+=$row[('req_qny_edit')];
																						$buyer_wise_rate+=$row[('cons_rate')];
																						$buyer_wise_amount+=$row[('cons_amount')];
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
													<tr bgcolor="#f7ffdd">
														<td colspan="17" align="right"><strong>Style Total :</strong></td>
														<td align="right"><strong><? echo number_format($style_wise_recipie_qty,2);  ?></strong> </td>
														<td align="right"><strong><? echo number_format($style_wise_req_qty,2);  ?></strong> </td>
														<td align="right"><strong><? echo number_format($style_wise_qty,2);  ?></strong> </td>
														<td align="right"><strong><? echo number_format($style_wise_rate,2);  ?></strong> </td>
														<td align="right"><strong><? echo number_format($style_wise_amount,2);  ?></strong> </td>
														<td align="right">&nbsp;</td>
													</tr>
													<?
												}
											}
											?>
											<tr bgcolor="#ddf9ff">
												<td colspan="17" align="right"><strong>Buyer Total :</strong></td>
												<td align="right"><strong><? echo number_format($buyer_wise_recipie_qty,2);  ?></strong> </td>
												<td align="right"><strong><? echo number_format($buyer_wise_req_qty,2);  ?></strong> </td>
												<td align="right"><strong><? echo number_format($buyer_wise_qty,2);  ?></strong> </td>
												<td align="right"><strong><? echo number_format($buyer_wise_rate,2);  ?></strong> </td>
												<td align="right"><strong><? echo number_format($buyer_wise_amount,2);  ?></strong> </td>
												<td align="right">&nbsp;</td>
											</tr>
											<?
										}
									}
								}
							}
							?>
							<tr bgcolor="#ddffdf">
								<td colspan="17" align="right"><strong>Grand Total :</strong></td>
								<td align="right"><strong><? echo number_format($total_recipie_qty,2);  ?></strong> </td>
								<td align="right"><strong><? echo number_format($total_req_qty,2);  ?></strong> </td>
								<td align="right"><strong><? echo number_format($total_qty,2);  ?></strong> </td>
								<td align="right"><strong><? echo number_format($total_rate,2);  ?></strong> </td>
								<td align="right"><strong><? echo number_format($total_amount,2);  ?></strong> </td>
								<td align="right">&nbsp;</td>
							</tr>
											
						</tbody>
					</table>
				</div>
				<!-- <table width="1850" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
					<tfoot>
		                <tr bgcolor="#dddddd">   
							<td class="brk_word" width="30"></td>
							<td class="brk_word" width="200"></td>
							<td class="brk_word" width="85"></td>
							<td class="brk_word" width="90"></td>
							<td class="brk_word" width="150"></td>
							<td class="brk_word" width="120"></td>
							<td class="brk_word" width="85"></td>
							<td class="brk_word" width="85"></td>
							<td class="brk_word" width="120"> </td>
							<td class="brk_word" width="85"> </td>
							<td class="brk_word" width="85"> </td>
							<td class="brk_word" width="85"> </td>
							<td class="brk_word" width="85"> </td>
							<td class="brk_word" width="85" >Grand Total :</td>
							<td class="brk_word" width="85" id="value_total_rate"><? //echo number_format($total_rate,2,'.',''); ?></td>
							<td class="brk_word" width="85" id="value_total_qty"><? //echo number_format($total_qty,2,'.',''); ?></td>
							<td class="brk_word" align="right" id="value_total_amount"><? //echo number_format($total_amount,2,'.',''); ?></td>
		                </tr>
		            </tfoot>
	            </table> -->
			</fieldset>
		</div>	
	    <?
	}
	else if($type==2)
	{
    $group_cond = "";
    if($txt_int_ref!="") $group_cond =" and h.grouping like '%$txt_int_ref%'";
	$all_subcon_job_arr=array_unique(explode(",",(chop($all_po_ids,','))));

	$con = connect();
	foreach($all_subcon_job_arr as $key=>$row_val)
	{
		//echo $row_val; die;
		$r_id2=execute_query("insert into tmp_job_no (userid, job_id, entry_form) values ($user_id,$row_val,1220)");
	}

	if($db_type==0)
	{
		if($r_id2)
		{
			mysql_query("COMMIT");  
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		//echo $r_id2; die;
		if($r_id2)
		{
			oci_commit($con);  
		}
	} 
	$subcon_cond=" and g.id in (select job_id from tmp_job_no where userid=$user_id and entry_form=1220) ";
	$buyer_po_arr=array();
	$order_sql ="select f.job_no_prefix_num, f.within_group,g.id,g.buyer_po_no,g.buyer_style_ref, g.job_no_mst,g.order_no,g.buyer_buyer,g.main_process_id,g.gmts_item_id, g.embl_type, g.body_part,c.qnty,c.color_id from subcon_ord_mst f, subcon_ord_dtls g , subcon_ord_breakdown c where f.id=g.mst_id and f.embellishment_job=c.job_no_mst and g.id=c.mst_id and f.entry_form='204' $search_com_cond $query_cond ";
	//$search_com_cond
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
		$buyer_po_arr[$row[csf("id")]]['qty'] +=$row[csf("qnty")];
		$buyer_po_arr[$row[csf("id")]]['party_id']=$row[csf("party_id")];
		$buyer_po_arr[$row[csf("id")]]['main_process_id']=$row[csf("main_process_id")];
		$buyer_po_arr[$row[csf("id")]]['gmts_item_id']=$row[csf("gmts_item_id")];
		$buyer_po_arr[$row[csf("id")]]['embl_type']=$row[csf("embl_type")];
		$buyer_po_arr[$row[csf("id")]]['body_part']=$row[csf("body_part")];
		$buyer_po_arr[$row[csf("id")]]['color_id']=$row[csf("color_id")];

		//$po_id_arr[$row[csf("id")]]=$row[csf("id")];
		//$all_subcon_job .="'".$row[csf("job_no_mst")]."'".',';
	}
	unset($order_sql_res);

	$product_arr=array();
	$product_sql ="select c.id,c.item_description, c.item_group_id, c.sub_group_name, c.item_size from product_details_master c where c.status_active=1 and c.is_deleted=0 and item_category_id in (5,6,7,23,22)";
	//$search_com_cond
	//echo $order_sql; die;
	$product_sql_res=sql_select($product_sql); 
	foreach ($product_sql_res as $row)
	{
		$product_arr[$row[csf("id")]]['item_description']=$row[csf("item_description")];
		$product_arr[$row[csf("id")]]['item_group_id']=$row[csf("item_group_id")];
		$product_arr[$row[csf("id")]]['sub_group_name']=$row[csf("sub_group_name")];
		$product_arr[$row[csf("id")]]['item_size']=$row[csf("item_size")];
		
	}
	unset($product_sql_res);

	if($cbo_within_group==0 || $cbo_within_group==1){
		$int_ref_arr=array();
		$po_sql ="SELECT b.id,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$int_ref_arr[$row[csf("id")]]['grouping']=$row[csf("grouping")];
		}
		unset($po_sql_res);
	}
    if($cbo_within_group==0 || $cbo_within_group==1) {
        $query = "SELECT a.location_id, a.issue_date, a.issue_basis, a.req_no, a.req_id, a.issue_purpose, a.company_id, a.loan_party, a.lap_dip_no, a.batch_no, a.order_id, a.sub_order_id, a.style_ref, a.store_id, a.buyer_job_no, a.is_posted_account, a.lc_company, a.floor_id, a.machine_id, a.remarks, b.id,a.issue_number,b.store_id,b.cons_uom, b.cons_quantity, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id,b.cons_rate,b.cons_amount, c.sub_process,c.id as issue_dtls_id, c.item_category, c.dose_base, c.ratio, c.recipe_qnty, c.adjust_percent, c.adjust_type, c.required_qnty, c.req_qny_edit, c.recipe_id, d.recipe_no ,d.po_ids as po_id ,f.embellishment_job,f.party_id ,f.order_no, g.buyer_po_no,g.buyer_style_ref,g.body_part,g.embl_type,g.order_quantity, f.within_group,g.buyer_buyer,g.order_uom , g.buyer_po_id
	from inv_issue_master a, inv_transaction b, dyes_chem_issue_dtls c, pro_recipe_entry_mst d, subcon_ord_mst f, subcon_ord_dtls g left join wo_po_break_down h on g.buyer_po_id = h.id
	where a.id=c.mst_id and a.id=b.mst_id and b.id =c.trans_id and g.mst_id=f.id and a.buyer_job_no=f.order_no and to_char(d.id)= c.recipe_id and b.prod_id=c.product_id  and f.entry_form=204 and d.entry_form=220  and b.transaction_type=2 and a.entry_form=250 and b.item_category in (5,6,7,23,22) and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and f.status_active =1 and f.is_deleted =0 $date_cond $query_cond $recipe_cond $sub_process_condn_cond$subcon_cond $group_cond $year_cond order by a.id ";
    }else{
        $query = "SELECT a.location_id, a.issue_date, a.issue_basis, a.req_no, a.req_id, a.issue_purpose, a.company_id, a.loan_party, a.lap_dip_no, a.batch_no, a.order_id, a.sub_order_id, a.style_ref, a.store_id, a.buyer_job_no, a.is_posted_account, a.lc_company, a.floor_id, a.machine_id, a.remarks, b.id,a.issue_number,b.store_id,b.cons_uom, b.cons_quantity, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id,b.cons_rate,b.cons_amount, c.sub_process,c.id as issue_dtls_id, c.item_category, c.dose_base, c.ratio, c.recipe_qnty, c.adjust_percent, c.adjust_type, c.required_qnty, c.req_qny_edit, c.recipe_id, d.recipe_no ,d.po_ids as po_id ,f.embellishment_job,f.party_id ,f.order_no, g.buyer_po_no,g.buyer_style_ref,g.body_part,g.embl_type,g.order_quantity, f.within_group,g.buyer_buyer,g.order_uom , g.buyer_po_id
	from inv_issue_master a, inv_transaction b, dyes_chem_issue_dtls c, pro_recipe_entry_mst d, subcon_ord_mst f, subcon_ord_dtls g
	where a.id=c.mst_id and a.id=b.mst_id and b.id =c.trans_id and g.mst_id=f.id and a.buyer_job_no=f.order_no and to_char(d.id)= c.recipe_id and b.prod_id=c.product_id  and f.entry_form=204 and d.entry_form=220  and b.transaction_type=2 and a.entry_form=250 and b.item_category in (5,6,7,23,22) and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and f.status_active =1 and f.is_deleted =0 $date_cond $query_cond $recipe_cond $sub_process_condn_cond$subcon_cond $year_cond order by a.id ";
    }
//echo $query; die;
	$sql_data_query = sql_select($query);
	$countRecords = count($query); 
	//echo $sql_data_query;
	ob_start();
	$details_data=array();
	foreach( $sql_data_query as $row)
	{
		//detail data in Array  
		$details_data[$row[csf("company_id")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_style_ref")]][$row[csf("prod_id")]]["recipe_qnty"] +=$row[csf(recipe_qnty)];
		$details_data[$row[csf("company_id")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_style_ref")]][$row[csf("prod_id")]]["req_qny_edit"] =$row[csf(req_qny_edit)];
		$details_data[$row[csf("company_id")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_style_ref")]][$row[csf("prod_id")]]["required_qnty"] +=$row[csf(required_qnty)];
		$details_data[$row[csf("company_id")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_style_ref")]][$row[csf("prod_id")]]["cons_amount"] +=$row[csf(cons_amount)];
		$details_data[$row[csf("company_id")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_style_ref")]][$row[csf("prod_id")]]["issue_dtls_id"] .=$row[csf(issue_dtls_id)].',';
		$details_data[$row[csf("company_id")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_style_ref")]][$row[csf("prod_id")]]["po_id"] .=$row[csf(po_id)].',';
		$details_data[$row[csf("company_id")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_style_ref")]][$row[csf("prod_id")]]["order_uom"] =$row[csf(order_uom)];
		$details_data[$row[csf("company_id")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_style_ref")]][$row[csf("prod_id")]]["sub_process"] =$row[csf(sub_process)];
		$details_data[$row[csf("company_id")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_style_ref")]][$row[csf("prod_id")]]["recipe_no"] =$row[csf(recipe_no)];
		$details_data[$row[csf("company_id")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_style_ref")]][$row[csf("prod_id")]]["buyer_po_id"] .=$row[csf(buyer_po_id)].',';
	}
	/*echo "<pre>";
	print_r($product_arr);*/
	?>
	<style type="text/css">
		.brk_word {
		  word-wrap: break-word;
		  word-break: break-all;
		}
	</style>
	<div style="width:2200px; margin:0 auto;">
		<fieldset style="width:100%;">	
	    <table width="98%" cellpadding="0" cellspacing="0" id="caption">
            <tr>  
                <td align="center" width="100%" colspan="11" class="form_caption" >
                	<strong style="font-size:18px">Daily Chemical Uses and Costing Report</strong>
                </td>
            </tr>
        </table>
		<div style="width:2200px;" align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">	
			<table width="2180"  class="rpt_table" cellpadding="2" cellspacing="2" border="1" rules="all" id="table_header_1" rules="all">
				<thead><!-- Date	Job Number	Order No	Buyer	Buyer PO Number	Buyer Style	Body Color	Boady Part	Print Type	Print Name	Order/Qty	Chemical Group	Chemical Name	Recipe Qty	Requisiton Qty	 Issue Qty / Kg 	 Rate / Taka 	 Amount / Tk 	Recipie No -->
					<th class="brk_word" width="30" align="center">Sl </th>
					<th class="brk_word" width="60" align="center">Date </th>
					<th class="brk_word" width="120" align="center">Job Number </th>
					<th class="brk_word" width="120" align="center">Order No </th>
					<th class="brk_word" width="120" align="center">Internal Ref </th>
					<th class="brk_word" width="150" align="center">Buyer</th>
					<th class="brk_word" width="150" align="center">Buyer's Buyer</th>
					<th class="brk_word" width="60" align="center">UOM</th>
					<th class="brk_word" width="100" align="center">Buyer PO Number</th>
					<th class="brk_word" width="85" align="center">Buyer Style</th>
					<th class="brk_word" width="85" align="center">Body Color</th>
					<th class="brk_word" width="100" align="center">Boady Part</th>
					<th class="brk_word" width="85" align="center">Print Type</th>
					<th class="brk_word" width="65" align="center">Print Name</th>
					<th class="brk_word" width="85" align="center">Order Qty.</th>
					<th class="brk_word" width="85" align="center">Chemical Group</th>
					<th class="brk_word" width="120" align="center">Chemical Name</th>
					<th class="brk_word" width="85" align="center">Recipe Qty</th>
					<th class="brk_word" width="85" align="center">Requisiton Qty</th>
					<th class="brk_word" width="85" align="center">Issue Qty / Kg</th>
					<th class="brk_word" width="60" align="center">Rate / Taka</th>
					<th class="brk_word" width="85" align="center">Amount / Tk</th>
					<th class="brk_word" align="center">Recipie No </th>
				</thead>
			</table>
		</div>
		<div style="width:2200px;" align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">	
			<table width="2180"  class="rpt_table" cellpadding="2" cellspacing="2" border="1" rules="all" id="table_body">
				<tbody>
					<?	
					/*$details_data[$row[csf("company_id")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_style_ref")]][$row[csf("prod_id")]]*/
					$k=1;$total_rate=$total_qty=$total_amount=$total_recipie_qty=$total_req_qty=0;
					foreach($details_data as $company_id=>$company_data)
					{
						foreach($company_data as $issue_date=>$issue_date_data)
						{
							foreach($issue_date_data as $within_group=>$within_group_data)
							{
								if($within_group==1) 
								{
									$partyarr = $company_array;
									$buyerArr = $buyer_arr;
								}
								else
								{
									$partyarr = $party_arr;
									$buyerArr = $buyer_buyer;
								}

								foreach($within_group_data as $embellishment_job=>$embellishment_job_data)
								{
									foreach($embellishment_job_data as $order_no=>$order_no_data)
									{
										$buyer_wise_rate=$buyer_wise_qty=$buyer_wise_amount=$buyer_wise_recipie_qty=$buyer_wise_req_qty=0;
										foreach($order_no_data as $party_id=>$party_id_data)
										{
											$style_wise_rate=$style_wise_qty=$style_wise_amount=$style_wise_recipie_qty=$style_wise_req_qty=0;
											foreach($party_id_data as $buyer_style_ref=>$buyer_style_data)
											{
												foreach($buyer_style_data as $product_id=>$row)
												{
													if ($k%2==0)  
													$bgcolor="#E9F3FF";
													else
													$bgcolor="#FFFFFF";
													$issue_dtls_id=chop($row[('issue_dtls_id')],',');
													$po_id=explode(",",$row['po_id']);
													//echo $row['buyer_po_id'];
													$buyer_po_id=explode(",",trim($row['buyer_po_id'], ','));

													foreach($buyer_po_id as $k1 => $val)
													{
														if($k1 == 0)
                                                            $int_ref=$int_ref_arr[$val]['grouping'];
                                                        else
                                                            $int_ref.=','.$int_ref_arr[$val]['grouping'];
													}

													//$po_id=explode(",",$row[csf('order_id')]);
													$buyer_po=$buyer_style=$job_no=$order_no=$buyerBuyer=$gmts_item_id=$body_part_name=$embl_type=$color_id=$main_process_id=$buyer_buyer=$item_description=$item_group_id=$sub_group_name=$item_size='';
													foreach($po_id as $val) 
													{
														//echo $val;
														if($buyer_po=="") $buyer_po=$buyer_po_arr[$val]['po']; else $buyer_po.=','.$buyer_po_arr[$val]['po'];
														if($buyer_style=="") $buyer_style=$buyer_po_arr[$val]['style']; else $buyer_style.=','.$buyer_po_arr[$val]['style'];
														if($job_no=="") $job_no=$buyer_po_arr[$val]['job']; else $job_no.=','.$buyer_po_arr[$val]['job'];
														if($order_no=="") $order_no=$buyer_po_arr[$val]['order_no']; else $order_no.=','.$buyer_po_arr[$val]['order_no'];
														//if($party_id=="") $party_id=$partyarr[$buyer_po_arr[$val]['party_id']]; else $party_id.=','.$partyarr[$buyer_po_arr[$val]['party_id']];
														if($gmts_item_id=="") $gmts_item_id=$buyer_po_arr[$val]['gmts_item_id']; else $gmts_item_id.=','.$buyer_po_arr[$val]['gmts_item_id'];
														if($body_part_name=="") $body_part_name=$body_part[$buyer_po_arr[$val]['body_part']]; else $body_part_name.=','.$body_part[$buyer_po_arr[$val]['body_part']];
														if($embl_type=="") $embl_type=$emblishment_print_type[$buyer_po_arr[$val]['embl_type']]; else $embl_type.=','.$emblishment_print_type[$buyer_po_arr[$val]['embl_type']];
														if($color_id=="") $color_id=$buyer_po_arr[$val]['color_id']; else $color_id.=','.$buyer_po_arr[$val]['color_id'];


														if($main_process_id=="") $main_process_id=$buyer_po_arr[$val]['main_process_id']; else $main_process_id.=','.$buyer_po_arr[$val]['main_process_id'];
														//if($buyer_buyer=="") $buyer_buyer=$buyer_po_arr[$val]['buyer_buyer']; else $buyer_buyer.=','.$buyer_po_arr[$val]['buyer_buyer'];
														$within_group=$buyer_po_arr[$val]['within_group'];
														if ($within_group==1) {
															if($buyer_buyer=="") $buyer_buyer=$buyerArr[$buyer_po_arr[$val]['buyer_buyer']]; else $buyer_buyer.=','.$buyerArr[$buyer_po_arr[$val]['buyer_buyer']];
												        }else{
												           if($buyer_buyer=="") $buyer_buyer=$buyer_po_arr[$val]['buyer_buyer']; else $buyer_buyer.=','.$buyer_po_arr[$val]['buyer_buyer'];
												        }
														if($within_group=="") $within_group=$buyer_po_arr[$val]['within_group']; else $within_group.=','.$buyer_po_arr[$val]['within_group'];
														$qty +=$buyer_po_arr[$val]['qty'];
													}
													$buyer_po=implode(",",array_unique(explode(",",chop($buyer_po,','))));
													$buyer_style=implode(",",array_unique(explode(",",chop($buyer_style,','))));
													$job_no=implode(",",array_unique(explode(",",chop($job_no,','))));
													$order_no=implode(",",array_unique(explode(",",chop($order_no,','))));
													$buyer_buyer=implode(",",array_unique(explode(",",chop($buyer_buyer,','))));
													//$party_id=implode(",",array_unique(explode(",",chop($party_id,','))));
													$gmts_item_id=implode(",",array_unique(explode(",",chop($gmts_item_id,','))));
													$body_part_name=implode(",",array_unique(explode(",",chop($body_part_name,','))));
													$embl_type=implode(",",array_unique(explode(",",chop($embl_type,','))));
													$color_id=implode(",",array_unique(explode(",",chop($color_id,','))));
													//$within_group=implode(",",array_unique(explode(",",chop($buyer_po,','))));
													$main_process_id=implode(",",array_unique(explode(",",chop($main_process_id,','))));
													$int_ref=implode(",",array_unique(explode(",",chop($int_ref,','))));


													$item_description=$product_arr[$product_id]['item_description'];
													$item_group_id=$product_arr[$product_id]['item_group_id'];
													$sub_group_name=$product_arr[$product_id]['sub_group_name'];
													$item_size=$product_arr[$product_id]['item_size'];
													
													?>
													<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k;?>">
														<td class="brk_word" width="30" align="center"><? echo $k; ?> </td>
														<td class="brk_word" width="60" align="left"><? echo $issue_date; ?></td>
														<td class="brk_word" width="120" align="left"><? echo $embellishment_job; ?></td>
														<td class="brk_word" width="120" align="left"><? echo $order_no; ?></td>
														<td class="brk_word" width="120" align="left"><? echo $int_ref; ?></td>
														<td class="brk_word" width="150" align="left"><? echo $partyarr[$party_id]; ?>
														</td>
														<td class="brk_word" width="150" align="left"><? echo $buyer_buyer; ?></td>
														<td class="brk_word" width="60" align="left"><? echo $unit_of_measurement[$row['order_uom']]; ?></td>
														<td class="brk_word" width="100" align="left"><? echo $buyer_po; ?></td>
														<td class="brk_word" width="85" align="left"><? echo $buyer_style_ref; ?></td>
														<td class="brk_word" width="85" align="left"><? echo $color_arr[$row[('sub_process')]]; ?></td>
														<td class="brk_word" width="100" align="left"><? echo $body_part_name; ?></td>
														<td class="brk_word" width="85" align="left"><? echo $embl_type; ?></td>
														<td class="brk_word" width="65" align="left"><? echo 'Printing'; ?></td>
														<td class="brk_word" width="85" align="right"><? echo $qty; ?></td>
														<td class="brk_word" width="85" align="left"><? echo $group_arr[$item_group_id]; ?></td>
														<td class="brk_word" width="120" align="left"><? echo $item_description; ?></td>
														<td class="brk_word" width="85" align="right"><? echo number_format($row[('recipe_qnty')],2); ?></td>
														<td class="brk_word" width="85" align="right"><? echo number_format($row[('required_qnty')],2); ?></td>
														<td class="brk_word" width="85" align="right"><a href="##" onClick="openmypage_qty('<? echo $issue_dtls_id; ?>','issue_qty_popup')"><? echo number_format($row[('req_qny_edit')],2);?></a>
															</td>
														<td class="brk_word" width="60" align="right"><? echo number_format($row[('cons_amount')]/$row[('req_qny_edit')],2); ?></td>
														<td class="brk_word" width="85" align="right"><? echo number_format($row[('cons_amount')],2); ?></td>
														<td class="brk_word" align="left"><? echo $row[('recipe_no')]; ?> </td>
													</tr>
													<?
													$k++;
													$total_recipie_qty+=$row[('recipe_qnty')];
													$total_req_qty+=$row[('required_qnty')];
													$total_qty+=$row[('req_qny_edit')];
													$total_rate+=$row[('cons_rate')];
													$total_amount+=$row[('cons_amount')];

													$style_wise_recipie_qty+=$row[('recipe_qnty')];
													$style_wise_req_qty+=$row[('required_qnty')];
													$style_wise_qty+=$row[('req_qny_edit')];
													$style_wise_rate+=$row[('cons_rate')];
													$style_wise_amount+=$row[('cons_amount')];

													$buyer_wise_recipie_qty+=$row[('recipe_qnty')];
													$buyer_wise_req_qty+=$row[('required_qnty')];
													$buyer_wise_qty+=$row[('req_qny_edit')];
													$buyer_wise_rate+=$row[('cons_rate')];
													$buyer_wise_amount+=$row[('cons_amount')];
																				
												}
												?>
												<tr bgcolor="#f7ffdd">
													<td colspan="17" align="right"><strong>Style Total :</strong></td>
													<td align="right"><strong><? echo number_format($style_wise_recipie_qty,2);  ?></strong> </td>
													<td align="right"><strong><? echo number_format($style_wise_req_qty,2);  ?></strong> </td>
													<td align="right"><strong><? echo number_format($style_wise_qty,2);  ?></strong> </td>
													<td align="right"><strong>&nbsp;</strong> </td>
													<td align="right"><strong><? echo number_format($style_wise_amount,2);  ?></strong> </td>
													<td align="right">&nbsp;</td>
												</tr>
												<?
											}
										}
										?>
										<tr bgcolor="#ddf9ff">
											<td colspan="17" align="right"><strong>Buyer Total :</strong></td>
											<td align="right"><strong><? echo number_format($buyer_wise_recipie_qty,2);  ?></strong> </td>
											<td align="right"><strong><? echo number_format($buyer_wise_req_qty,2);  ?></strong> </td>
											<td align="right"><strong><? echo number_format($buyer_wise_qty,2);  ?></strong> </td>
											<td align="right"><strong>&nbsp;</strong> </td>
											<td align="right"><strong><? echo number_format($buyer_wise_amount,2);  ?></strong> </td>
											<td align="right">&nbsp;</td>
										</tr>
										<?
									}
								}
							}
						}
					}
					?>
					<tr bgcolor="#ddffdf">
						<td colspan="17" align="right"><strong>Grand Total :</strong></td>
						<td align="right"><strong><? echo number_format($total_recipie_qty,2);  ?></strong> </td>
						<td align="right"><strong><? echo number_format($total_req_qty,2);  ?></strong> </td>
						<td align="right"><strong><? echo number_format($total_qty,2);  ?></strong> </td>
						<td align="right"><strong>&nbsp;</strong> </td>
						<td align="right"><strong><? echo number_format($total_amount,2);  ?></strong> </td>
						<td align="right">&nbsp;</td>
					</tr>
									
				</tbody>
			</table>
		</div>
		</fieldset>
	</div>	
    <?
    $r_id3=execute_query("delete from tmp_job_no where userid=$user_id and entry_form=1220");
	if($db_type==0)
	{
		if($r_id3)
		{
			mysql_query("COMMIT");
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($r_id3)
		{
			oci_commit($con);
		}
	}
	}
	
	else if($type==3)
	{

	$all_subcon_job_arr=array_unique(explode(",",(chop($all_po_ids,','))));
	//print_r($all_subcon_job_arr); die;
	
	$con = connect();
	foreach($all_subcon_job_arr as $key=>$row_val)
	{
		//echo $row_val; die;
		$r_id2=execute_query("insert into tmp_job_no (userid, job_id, entry_form) values ($user_id,$row_val,1220)");
	}
	//print_r($issue_item_arr);
	//$wo_ids=implode(",",array_unique(explode(",",(chop($wo_id,',')))));
	if($db_type==0)
	{
		if($r_id2)
		{
			mysql_query("COMMIT");  
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		//echo $r_id2; die;
		if($r_id2)
		{
			oci_commit($con);  
		}
	} 
	$subcon_cond=" and g.id in (select job_id from tmp_job_no where userid=$user_id and entry_form=1220) ";
	$buyer_po_arr=array();
	$order_sql ="select f.job_no_prefix_num, f.within_group,g.id,g.buyer_po_no,g.buyer_style_ref, g.job_no_mst,g.order_no,g.buyer_buyer,g.main_process_id,g.gmts_item_id, g.embl_type, g.body_part,c.qnty,c.color_id from subcon_ord_mst f, subcon_ord_dtls g , subcon_ord_breakdown c where f.id=g.mst_id and f.embellishment_job=c.job_no_mst and g.id=c.mst_id and f.entry_form='204' $search_com_cond $query_cond ";
	//$search_com_cond
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
		$buyer_po_arr[$row[csf("id")]]['qty'] +=$row[csf("qnty")];
		$buyer_po_arr[$row[csf("id")]]['party_id']=$row[csf("party_id")];
		$buyer_po_arr[$row[csf("id")]]['main_process_id']=$row[csf("main_process_id")];
		$buyer_po_arr[$row[csf("id")]]['gmts_item_id']=$row[csf("gmts_item_id")];
		$buyer_po_arr[$row[csf("id")]]['embl_type']=$row[csf("embl_type")];
		$buyer_po_arr[$row[csf("id")]]['body_part']=$row[csf("body_part")];
		$buyer_po_arr[$row[csf("id")]]['color_id']=$row[csf("color_id")];

		//$po_id_arr[$row[csf("id")]]=$row[csf("id")];
		//$all_subcon_job .="'".$row[csf("job_no_mst")]."'".',';
	}
	unset($order_sql_res);

	$product_arr=array();
	$product_sql ="select c.id,c.item_description, c.item_group_id, c.sub_group_name, c.item_size from product_details_master c where c.status_active=1 and c.is_deleted=0 and item_category_id in (5,6,7,23,22)";
	//$search_com_cond
	//echo $order_sql; die;
	$product_sql_res=sql_select($product_sql); 
	foreach ($product_sql_res as $row)
	{
		$product_arr[$row[csf("id")]]['item_description']=$row[csf("item_description")];
		$product_arr[$row[csf("id")]]['item_group_id']=$row[csf("item_group_id")];
		$product_arr[$row[csf("id")]]['sub_group_name']=$row[csf("sub_group_name")];
		$product_arr[$row[csf("id")]]['item_size']=$row[csf("item_size")];
		
	}
	unset($product_sql_res);

	if($cbo_within_group=='' || $cbo_within_group==1){
		$int_ref_arr=array();
		$po_sql ="SELECT b.id,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$int_ref_arr[$row[csf("id")]]['grouping']=$row[csf("grouping")];
		}
		unset($po_sql_res);
	}
	

	$query = "SELECT a.location_id, a.issue_date, a.issue_basis, a.req_no, a.req_id, a.issue_purpose, a.company_id, a.loan_party, a.lap_dip_no, a.batch_no, a.order_id, a.sub_order_id, a.style_ref, a.store_id, a.buyer_job_no, a.is_posted_account, a.lc_company, a.floor_id, a.machine_id, a.remarks, b.id,a.issue_number,b.store_id,b.cons_uom, b.cons_quantity, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id,b.cons_rate,b.cons_amount, c.sub_process,c.id as issue_dtls_id, c.item_category, c.dose_base, c.ratio, c.recipe_qnty, c.adjust_percent, c.adjust_type, c.required_qnty, c.req_qny_edit, c.recipe_id, d.recipe_no ,d.po_ids as po_id ,f.embellishment_job,f.party_id ,f.order_no, g.buyer_po_no,g.buyer_style_ref,g.body_part,g.embl_type,g.order_quantity, f.within_group,g.buyer_buyer,g.order_uom , g.buyer_po_id
	from inv_issue_master a, inv_transaction b, dyes_chem_issue_dtls c, pro_recipe_entry_mst d, subcon_ord_mst f , subcon_ord_dtls g
	where a.id=c.mst_id and a.id=b.mst_id and b.id =c.trans_id and g.mst_id=f.id and a.buyer_job_no=f.order_no and to_char(d.id)= c.recipe_id and b.prod_id=c.product_id  and f.entry_form=204 and d.entry_form=220  and b.transaction_type=2 and a.entry_form=250 and b.item_category in (5,6,7,23,22) and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and f.status_active =1 and f.is_deleted =0 $date_cond $query_cond $recipe_cond $sub_process_cond $subcon_cond $year_cond order by a.id "; 
	    //,c.item_description, c.item_group_id, c.sub_group_name, c.item_size
	    //and a.sub_order_id=g.id

		 //echo $query;
	$sql_data_query = sql_select($query);
	$countRecords = count($query); 
	//echo $sql_data_query;
	ob_start();
	$details_data=array();
	foreach( $sql_data_query as $row)
	{
		//detail data in Array  
		$details_data[$row[csf("company_id")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_style_ref")]][$row[csf("prod_id")]]["recipe_qnty"] +=$row[csf(recipe_qnty)];
		$details_data[$row[csf("company_id")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_style_ref")]][$row[csf("prod_id")]]["req_qny_edit"] +=$row[csf(req_qny_edit)];
		$details_data[$row[csf("company_id")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_style_ref")]][$row[csf("prod_id")]]["required_qnty"] +=$row[csf(required_qnty)];
		$details_data[$row[csf("company_id")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_style_ref")]][$row[csf("prod_id")]]["cons_amount"] +=$row[csf(cons_amount)];
		$details_data[$row[csf("company_id")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_style_ref")]][$row[csf("prod_id")]]["issue_dtls_id"] .=$row[csf(issue_dtls_id)].',';
		$details_data[$row[csf("company_id")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_style_ref")]][$row[csf("prod_id")]]["po_id"] .=$row[csf(po_id)].',';
		$details_data[$row[csf("company_id")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_style_ref")]][$row[csf("prod_id")]]["order_uom"] =$row[csf(order_uom)];
		$details_data[$row[csf("company_id")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_style_ref")]][$row[csf("prod_id")]]["sub_process"] =$row[csf(sub_process)];
		$details_data[$row[csf("company_id")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_style_ref")]][$row[csf("prod_id")]]["recipe_no"] =$row[csf(recipe_no)];
		$details_data[$row[csf("company_id")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_style_ref")]][$row[csf("prod_id")]]["buyer_po_id"] .=$row[csf(buyer_po_id)].',';
	}
	/*echo "<pre>";
	print_r($product_arr);*/
	?>
	<style type="text/css">
		.brk_word {
		  word-wrap: break-word;
		  word-break: break-all;
		}
	</style>
	<div style="width:1100PX; margin:0 auto;">
		<fieldset style="width:100%;">	
	    <table width="98%" cellpadding="0" cellspacing="0" id="caption">
            <tr>  
                <td align="center" width="100%" colspan="11" class="form_caption" >
                	<strong style="font-size:18px">Style Wise Chemical Cost Report</strong>
                </td>
            </tr>
        </table>
		<div style="width:1200PX;" align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">	
			<table width="1180"  class="rpt_table" cellpadding="2" cellspacing="2" border="1" rules="all" id="table_header_1" rules="all">
				<thead><!-- Date	Job Number	Order No	Buyer	Buyer PO Number	Buyer Style	Body Color	Boady Part	Print Type	Print Name	Order/Qty	Chemical Group	Chemical Name	Recipe Qty	Requisiton Qty	 Issue Qty / Kg 	 Rate / Taka 	 Amount / Tk 	Recipie No -->
					<th class="brk_word" width="30" align="center">Sl </th>
					<th class="brk_word" width="85" align="center">Buyer Style</th>
					<th class="brk_word" width="150" align="center">Cus Buyer</th>
					<th class="brk_word" width="150" align="center">Boady Part</th>
					<th class="brk_word" width="85" align="center">Body Part Qty.</th>
					<th class="brk_word" width="120" align="center">Item Name</th>
					<th class="brk_word" width="120" align="center">UOM</th>
					<th class="brk_word" width="85" align="center">Requisiton Qty</th>
					<th class="brk_word" width="85" align="center">Issue Qty</th>
					<th class="brk_word" width="60" align="center">Rate</th>
					<th class="brk_word" width="85" align="center">Issue Value</th>
					<th class="brk_word" align="center">Blance Qty.</th>
				</thead>
			</table>
		</div>
		<div style="width:1200PX;" align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">	
			<table width="1180"  class="rpt_table" cellpadding="2" cellspacing="2" border="1" rules="all" id="table_body">
				<tbody>
					<?	
					/*$details_data[$row[csf("company_id")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_style_ref")]][$row[csf("prod_id")]]*/
					$k=1;$total_rate=$total_qty=$total_amount=$total_recipie_qty=$total_req_qty=0;
					foreach($details_data as $company_id=>$company_data)
					{

							foreach($company_data as $within_group=>$within_group_data)
							{
								if($within_group==1) 
								{
									$partyarr = $company_array;
									$buyerArr = $buyer_arr;
								}
								else
								{
									$partyarr = $party_arr;
									$buyerArr = $buyer_buyer;
								}

								foreach($within_group_data as $embellishment_job=>$embellishment_job_data)
								{
									foreach($embellishment_job_data as $order_no=>$order_no_data)
									{
										$buyer_wise_rate=$buyer_wise_qty=$buyer_wise_amount=$buyer_wise_recipie_qty=$buyer_wise_req_qty=0;
										foreach($order_no_data as $party_id=>$party_id_data)
										{
											$style_wise_rate=$style_wise_qty=$style_wise_amount=$style_wise_recipie_qty=$style_wise_req_qty=0;
											foreach($party_id_data as $buyer_style_ref=>$buyer_style_data)
											{
												$total_req_qty = $total_issue=$total_rate=$total_issue_value=$total_blance=0;
												$row_span = count($buyer_style_data);
													$row_con = '';
												foreach($buyer_style_data as $product_id=>$row)
												{
													if ($k%2==0)  
													$bgcolor="#E9F3FF";
													else
													$bgcolor="#FFFFFF";
													$issue_dtls_id=chop($row[('issue_dtls_id')],',');
													$po_id=explode(",",$row['po_id']);
													//echo $row['buyer_po_id'];
													$buyer_po_id=explode(",",$row['buyer_po_id']);
													foreach($buyer_po_id as $val) 
													{
														if($int_ref=="") $int_ref=$int_ref_arr[$val]['grouping']; else $int_ref.=','.$int_ref_arr[$val]['grouping'];
													}

													//$po_id=explode(",",$row[csf('order_id')]);
													$buyer_po=$buyer_style=$job_no=$order_no=$buyerBuyer=$gmts_item_id=$body_part_name=$embl_type=$color_id=$main_process_id=$buyer_buyer=$item_description=$item_group_id=$sub_group_name=$item_size='';
													foreach($po_id as $val) 
													{
														//echo $val;
														if($buyer_po=="") $buyer_po=$buyer_po_arr[$val]['po']; else $buyer_po.=','.$buyer_po_arr[$val]['po'];
														if($buyer_style=="") $buyer_style=$buyer_po_arr[$val]['style']; else $buyer_style.=','.$buyer_po_arr[$val]['style'];
														if($job_no=="") $job_no=$buyer_po_arr[$val]['job']; else $job_no.=','.$buyer_po_arr[$val]['job'];
														if($order_no=="") $order_no=$buyer_po_arr[$val]['order_no']; else $order_no.=','.$buyer_po_arr[$val]['order_no'];
														//if($party_id=="") $party_id=$partyarr[$buyer_po_arr[$val]['party_id']]; else $party_id.=','.$partyarr[$buyer_po_arr[$val]['party_id']];
														if($gmts_item_id=="") $gmts_item_id=$buyer_po_arr[$val]['gmts_item_id']; else $gmts_item_id.=','.$buyer_po_arr[$val]['gmts_item_id'];
														if($body_part_name=="") $body_part_name=$body_part[$buyer_po_arr[$val]['body_part']]; else $body_part_name.=','.$body_part[$buyer_po_arr[$val]['body_part']];
														if($embl_type=="") $embl_type=$emblishment_print_type[$buyer_po_arr[$val]['embl_type']]; else $embl_type.=','.$emblishment_print_type[$buyer_po_arr[$val]['embl_type']];
														if($color_id=="") $color_id=$buyer_po_arr[$val]['color_id']; else $color_id.=','.$buyer_po_arr[$val]['color_id'];


														if($main_process_id=="") $main_process_id=$buyer_po_arr[$val]['main_process_id']; else $main_process_id.=','.$buyer_po_arr[$val]['main_process_id'];
														//if($buyer_buyer=="") $buyer_buyer=$buyer_po_arr[$val]['buyer_buyer']; else $buyer_buyer.=','.$buyer_po_arr[$val]['buyer_buyer'];
														$within_group=$buyer_po_arr[$val]['within_group'];
														if ($within_group==1) {
															if($buyer_buyer=="") $buyer_buyer=$buyerArr[$buyer_po_arr[$val]['buyer_buyer']]; else $buyer_buyer.=','.$buyerArr[$buyer_po_arr[$val]['buyer_buyer']];
												        }else{
												           if($buyer_buyer=="") $buyer_buyer=$buyer_po_arr[$val]['buyer_buyer']; else $buyer_buyer.=','.$buyer_po_arr[$val]['buyer_buyer'];
												        }
														if($within_group=="") $within_group=$buyer_po_arr[$val]['within_group']; else $within_group.=','.$buyer_po_arr[$val]['within_group'];
														$qty +=$buyer_po_arr[$val]['qty'];
													}
													$buyer_po=implode(",",array_unique(explode(",",chop($buyer_po,','))));
													$buyer_style=implode(",",array_unique(explode(",",chop($buyer_style,','))));
													$job_no=implode(",",array_unique(explode(",",chop($job_no,','))));
													$order_no=implode(",",array_unique(explode(",",chop($order_no,','))));
													$buyer_buyer=implode(",",array_unique(explode(",",chop($buyer_buyer,','))));
													//$party_id=implode(",",array_unique(explode(",",chop($party_id,','))));
													$gmts_item_id=implode(",",array_unique(explode(",",chop($gmts_item_id,','))));
													$body_part_name=implode(",",array_unique(explode(",",chop($body_part_name,','))));
													$embl_type=implode(",",array_unique(explode(",",chop($embl_type,','))));
													$color_id=implode(",",array_unique(explode(",",chop($color_id,','))));
													//$within_group=implode(",",array_unique(explode(",",chop($buyer_po,','))));
													$main_process_id=implode(",",array_unique(explode(",",chop($main_process_id,','))));
													$int_ref=implode(",",array_unique(explode(",",chop($int_ref,','))));

													$item_description=$product_arr[$product_id]['item_description'];
													$item_group_id=$product_arr[$product_id]['item_group_id'];
													$sub_group_name=$product_arr[$product_id]['sub_group_name'];
													$item_size=$product_arr[$product_id]['item_size'];
													
													?>
													<tr bgcolor="<? echo $bgcolor; ?>"  id="tr_<? echo $k;?>">
														<td class="brk_word" width="30" align="left"><? echo $k; ?> </td>
														<?php
															if($row_con==''){
														?>
														<td rowspan="<?php echo $row_span;?>" class="brk_word" width="85" align="left"><? echo $buyer_style_ref; ?></td>
														<td rowspan="<?php echo $row_span;?>" class="brk_word" width="150" align="left"><? echo $buyer_buyer; ?></td>
														<td rowspan="<?php echo $row_span;?>" class="brk_word" width="150" align="left"><? echo $body_part_name; ?></td>
														<?php
															$row_con =1;
															}
															
														?>
														<td class="brk_word" width="85" align="right"><? echo $qty; ?></td>
														
														<td class="brk_word" width="120" align="left"><? echo $item_description; ?></td>
														<td class="brk_word" width="120" align="left"><? echo $unit_of_measurement[$row[('order_uom')]]; ?></td>    
														<td class="brk_word" width="85" align="right"><? echo number_format($row[('required_qnty')],2); ?></td>
														<td class="brk_word" width="85" align="right"><a href="##" onClick="openmypage_qty('<? echo $issue_dtls_id; ?>','issue_qty_popup')"><? echo number_format($row[('req_qny_edit')],2);?></a>
															</td>
														<td class="brk_word" width="60" align="right"><? echo number_format($row[('cons_amount')]/$row[('req_qny_edit')],2); ?></td>
														<td class="brk_word" width="85" align="right"><? echo number_format($row[('cons_amount')],2); ?></td>
														<td class="brk_word" align="right"><?php echo  $blance= number_format($row[('required_qnty')]-$row[('req_qny_edit')],2);?></td>
													</tr>
													<?
													$k++;

													$rate = $row[('cons_amount')]/$row[('req_qny_edit')];

													$total_req_qty+=$row[('required_qnty')];
													$total_issue+=$row[('req_qny_edit')];
													$total_rate+=$rate;
													$total_issue_value+=$row[('cons_amount')];
													$total_blance+=$blance;																				
												}
												?>
												<tr bgcolor="#f7ffdd">
													<td colspan="7" align="right"><strong>Total :</strong></td>
													<td align="right"><strong><? echo number_format($total_req_qty,2);  ?></strong> </td>
													<td align="right"><strong><? echo number_format($total_issue,2);  ?></strong> </td>
													<td align="right"><strong><? echo number_format($total_rate,2);  ?></strong> </td>
													<td align="right"><strong><? echo number_format($total_issue_value,2);  ?></strong> </td>
													<td align="right"><strong><? echo number_format($total_blance,2);  ?></strong> </td>
												</tr>
												<?
											}

										}
										
									}

								}


						}

					}
					?>
									
				</tbody>
			</table>
		</div>
		</fieldset>
	</div>	
    <?
    $r_id3=execute_query("delete from tmp_job_no where userid=$user_id and entry_form=1220");
	if($db_type==0)
	{
		if($r_id3)
		{
			mysql_query("COMMIT");  
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($r_id3)
		{
			oci_commit($con);  
		}
	}
	}
	  else if($type==4)
	{
		
		
		
	$query_cond2='';
	if($cbo_company_id!=0) $query_cond2.=" and f.company_id ='$cbo_company_id'"; 
	if($cbo_buyer_id!=0)   $query_cond2.=" and f.party_id ='$cbo_buyer_id'";
	if($cbo_within_group!=0) $query_cond2.=" and f.within_group ='$cbo_within_group'";  
	if($txt_job_no!="") $query_cond2.=" and f.embellishment_job like '%$txt_job_no%'";
	if($txt_order_no!="") $query_cond2.=" and f.order_no like '%$txt_order_no%'";

	//if($txt_buyer_po!="") $query_cond2.=" and g.buyer_po_no like '%$txt_buyer_po%'";
	//if($txt_style_ref!="") $query_cond2.=" and g.buyer_style_ref like '%$txt_style_ref%'";
	//if($txt_style_ref!="") $query_cond2.=" and g.buyer_style_ref like '%$txt_style_ref%'";
	//if($job_year_selection!="") $year_cond=" and to_char(a.insert_date,'YYYY')=$job_year_selection";

 
	 
    $group_cond = "";
    if($txt_int_ref!="") $group_cond =" and h.grouping like '%$txt_int_ref%'";
	$all_subcon_job_arr=array_unique(explode(",",(chop($all_po_ids,','))));

	$con = connect();
	foreach($all_subcon_job_arr as $key=>$row_val)
	{
		//echo $row_val; die;
		$r_id2=execute_query("insert into tmp_job_no (userid, job_id, entry_form) values ($user_id,$row_val,1220)");
	}

	if($db_type==0)
	{
		if($r_id2)
		{
			mysql_query("COMMIT");  
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		//echo $r_id2; die;
		if($r_id2)
		{
			oci_commit($con);  
		}
	} 
	$subcon_cond=" and g.id in (select job_id from tmp_job_no where userid=$user_id and entry_form=1220) ";
	$buyer_po_arr=array();
	 $order_sql ="select f.job_no_prefix_num, f.within_group,g.id,g.buyer_po_no,g.buyer_style_ref, g.job_no_mst,g.order_no,g.buyer_buyer,g.main_process_id,g.gmts_item_id, g.embl_type, g.body_part,c.qnty,c.color_id,g.buyer_po_id ,g.order_uom,f.embellishment_job from subcon_ord_mst f, subcon_ord_dtls g , subcon_ord_breakdown c where f.id=g.mst_id and f.embellishment_job=c.job_no_mst and g.id=c.mst_id and f.entry_form='204' $search_com_cond $query_cond ";
	//$search_com_cond
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
		//$buyer_po_arr[$row[csf("id")]]['qty'] +=$row[csf("qnty")];
		$buyer_order_qty_arr[$row[csf("embellishment_job")]]['qty'] +=$row[csf("qnty")];
		$buyer_po_arr[$row[csf("id")]]['party_id']=$row[csf("party_id")];
		$buyer_po_arr[$row[csf("id")]]['main_process_id']=$row[csf("main_process_id")];
		$buyer_po_arr[$row[csf("id")]]['gmts_item_id']=$row[csf("gmts_item_id")];
		$buyer_po_arr[$row[csf("id")]]['embl_type']=$row[csf("embl_type")];
		$buyer_po_arr[$row[csf("id")]]['body_part']=$row[csf("body_part")];
		$buyer_po_arr[$row[csf("id")]]['color_id']=$row[csf("color_id")];
		$buyer_po_arr[$row[csf("id")]]['buyer_po_id']=$row[csf("buyer_po_id")];
		$buyer_po_arr[$row[csf("id")]]['order_uom']=$row[csf("order_uom")]; 
		$po_ids_arr[$row[csf("id")]]="'".$row[csf("id")]."'".',';  
		 
		 
	}
	unset($order_sql_res);
 
	 
	$order_po_ids=implode(",",array_unique($po_ids_arr)); 
    $order_po_ids_string = rtrim($order_po_ids, ','); 
 	if($txt_buyer_po!="") $query_cond2.=" and a.sub_order_ids in ($order_po_ids_string)";
	 

	$product_arr=array();
	$product_sql ="select c.id,c.item_description, c.item_group_id, c.sub_group_name, c.item_size from product_details_master c where c.status_active=1 and c.is_deleted=0 and item_category_id in (5,6,7,23,22)";
	//$search_com_cond
	//echo $order_sql; die;
	$product_sql_res=sql_select($product_sql); 
	foreach ($product_sql_res as $row)
	{
		$product_arr[$row[csf("id")]]['item_description']=$row[csf("item_description")];
		$product_arr[$row[csf("id")]]['item_group_id']=$row[csf("item_group_id")];
		$product_arr[$row[csf("id")]]['sub_group_name']=$row[csf("sub_group_name")];
		$product_arr[$row[csf("id")]]['item_size']=$row[csf("item_size")];
		
	}
	unset($product_sql_res);

	if($cbo_within_group==0 || $cbo_within_group==1)
	{
		$int_ref_arr=array();
		 $po_sql ="SELECT b.id,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$int_ref_arr[$row[csf("id")]]['grouping']=$row[csf("grouping")];
		}
		unset($po_sql_res);
	}
	
	
    if($cbo_within_group==0 || $cbo_within_group==1) 
	{
          $query = "SELECT a.location_id, a.issue_date,a.issue_number, a.issue_basis, a.req_no, a.req_id, a.issue_purpose, a.company_id, a.loan_party, a.lap_dip_no, a.batch_no, a.order_id, a.sub_order_id, a.style_ref, a.store_id, a.buyer_job_no, a.is_posted_account, a.lc_company, a.floor_id, a.machine_id, a.remarks, b.id,a.issue_number,b.store_id,b.cons_uom, b.cons_quantity, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id,b.cons_rate,b.cons_amount, c.sub_process,c.id as issue_dtls_id, c.item_category, c.dose_base, c.ratio, c.recipe_qnty, c.adjust_percent, c.adjust_type, c.required_qnty, c.req_qny_edit, c.recipe_id, d.recipe_no ,d.po_ids as po_id ,f.embellishment_job,f.party_id ,f.order_no,d.recipe_no,a.sub_order_ids,f.within_group
	from inv_issue_master a, inv_transaction b, dyes_chem_issue_dtls c, pro_recipe_entry_mst d, subcon_ord_mst f 
	where a.id=b.mst_id and a.id=c.mst_id and  b.id =c.trans_id and a.buyer_job_no=f.order_no and to_char(d.id)= c.recipe_id  and f.entry_form=204 and d.entry_form=220  and b.transaction_type=2 and a.entry_form=250 and b.item_category in (5,6,7,23,22) and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and f.status_active =1 and f.is_deleted =0 $date_cond $query_cond2 $recipe_cond2 $sub_process_cond2 $group_cond $year_cond $order_order_buyer_poCond order by a.id ";
    }
	else
	{
         $query = "SELECT a.location_id, a.issue_date, a.issue_basis,a.issue_number, a.req_no, a.req_id, a.issue_purpose, a.company_id, a.loan_party, a.lap_dip_no, a.batch_no, a.order_id, a.sub_order_id, a.style_ref, a.store_id, a.buyer_job_no, a.is_posted_account, a.lc_company, a.floor_id, a.machine_id, a.remarks, b.id,a.issue_number,b.store_id,b.cons_uom, b.cons_quantity, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id,b.cons_rate,b.cons_amount, c.sub_process,c.id as issue_dtls_id, c.item_category, c.dose_base, c.ratio, c.recipe_qnty, c.adjust_percent, c.adjust_type, c.required_qnty, c.req_qny_edit, c.recipe_id, d.recipe_no ,d.po_ids as po_id ,f.embellishment_job,f.party_id ,f.order_no,f.within_group,d.recipe_no,a.sub_order_ids
	from inv_issue_master a, inv_transaction b, dyes_chem_issue_dtls c, pro_recipe_entry_mst d, subcon_ord_mst f 
	where a.id=c.mst_id and a.id=b.mst_id and b.id =c.trans_id  and a.buyer_job_no=f.order_no and to_char(d.id)= c.recipe_id and b.prod_id=c.product_id  and f.entry_form=204 and d.entry_form=220  and b.transaction_type=2 and a.entry_form=250  and b.item_category in (5,6,7,23,22) and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and f.status_active =1 and f.is_deleted =0 $date_cond $query_cond2 $recipe_cond2 $sub_process_cond2  $year_cond $order_order_buyer_poCond order by a.id ";
    }
 //echo $query; die;
	$sql_data_query = sql_select($query);
	$countRecords = count($query); 
	//echo $sql_data_query;
	ob_start();
	$details_data=array();
	foreach( $sql_data_query as $row)
	{
		//detail data in Array  
		$details_data[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("prod_id")]]["issue_number"] +=$row[csf(issue_number)];
		$details_data[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("prod_id")]]["recipe_qnty"] +=$row[csf(recipe_qnty)];
		$details_data[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("prod_id")]]["req_qny_edit"]+=number_format($row[csf(req_qny_edit)],2,".",""); 
		$details_data[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("prod_id")]]["required_qnty"] +=number_format($row[csf(required_qnty)],2,".","");
		$details_data[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("prod_id")]]["cons_amount"] +=$row[csf(cons_amount)];
		$details_data[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("prod_id")]]["issue_dtls_id"] .=$row[csf(issue_dtls_id)].',';
		$details_data[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("prod_id")]]["po_id"] .=$row[csf(sub_order_ids)].',';
		$details_data[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("prod_id")]]["order_uom"] =$row[csf(order_uom)];
		$details_data[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("prod_id")]]["sub_process"] =$row[csf(sub_process)];
		$details_data[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("prod_id")]]["recipe_no"] =$row[csf(recipe_no)];
		$details_data[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("prod_id")]]["buyer_po_id"] .=$row[csf(buyer_po_id)].',';	
		$details_data[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("prod_id")]]["prod_id"]=$row[csf(prod_id)];
		$details_data[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("prod_id")]]["item_category"]=$row[csf(item_category)];
		$details_data[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("prod_id")]]["req_no"]=$row[csf(req_no)];
	}
	//echo "<pre>";
	//print_r($details_data);
	?>
	<style type="text/css">
		.brk_word {
		  word-wrap: break-word;
		  word-break: break-all;
		}
	</style>
	<div style="width:2300px; margin:0 auto;">
		<fieldset style="width:100%;">	
	    <table width="98%" cellpadding="0" cellspacing="0" id="caption">
            <tr>  
                <td align="center" width="100%" colspan="11" class="form_caption" >
                	<strong style="font-size:18px">Daily Chemical Uses and Costing Report</strong>
                </td>
            </tr>
        </table>
		<div style="width:2300px;max-height:500px; overflow-y:scroll;" align="center"   id="scroll_body">	
			<table width="2280"  class="rpt_table" cellpadding="2" cellspacing="2" border="1" id="table_header_1" rules="all">
				<thead> 
					<th class="brk_word" width="30" align="center">Sl </th>
					<th class="brk_word" width="80" align="center">Issued Date </th>
					<th class="brk_word" width="100" align="center">Issued No </th>
					<th class="brk_word" width="120" align="center">Recipie No</th>
					<th class="brk_word" width="120" align="center">Requistion No</th>
					<th class="brk_word" width="120" align="center">Job Number</th>
					<th class="brk_word" width="120" align="center">Work Order No</th>
					<th class="brk_word" width="80" align="center">Internal Ref</th>
					<th class="brk_word" width="100" align="center">Buyer</th>
					<th class="brk_word" width="120" align="center">Buyer's Buyer</th>
					<th class="brk_word" width="120" align="center">Buyer Style</th>
					<th class="brk_word" width="100" align="center">Body Color</th>
					<th class="brk_word" width="85" align="center">Print Type</th>
					<th class="brk_word" width="65" align="center">Print Name</th>
					<th class="brk_word" width="85" align="center">Product ID</th>
					<th class="brk_word" width="85" align="center">Item Category</th>
					<th class="brk_word" width="120" align="center">Item Name</th>
					<th class="brk_word" width="85" align="center">Order UOM</th>
					<th class="brk_word" width="85" align="center">Order Qty</th>
					<th class="brk_word" width="85" align="center">Requisiton Qty</th>
					<th class="brk_word" width="60" align="center">Issue Qty Kg</th>
					<th class="brk_word" width="85" align="center">Avg Rate Tk</th>
                    <th class="brk_word" width="85" align="center">Amount Tk</th>
					<th class="brk_word" align="center">Cost/Order UOM</th>
				</thead>
			</table>
		</div>
		<div style="width:2300px;max-height:500px; overflow-y:scroll;" align="center" id="scroll_body">	
			<table width="2280"  class="rpt_table" cellpadding="2" cellspacing="2" border="1" rules="all" id="table_body">
				<tbody>
					<?	
					/*$details_data[$row[csf("company_id")]][$row[csf("issue_date")]][$row[csf("within_group")]][$row[csf("embellishment_job")]][$row[csf("order_no")]][$row[csf("party_id")]][$row[csf("buyer_style_ref")]][$row[csf("prod_id")]]*/
					$k=1;$total_rate=$total_qty=$total_amount=$total_recipie_qty=$total_req_qty=0;
					foreach($details_data as $company_id=>$company_data)
					{
						foreach($company_data as $issue_number=>$issue_number_data)
						{	
							foreach($issue_number_data as $issue_date=>$issue_date_data)
							{
								foreach($issue_date_data as $within_group=>$within_group_data)
								{
									
									//echo $within_group;
									if($within_group==1) 
									{
										$partyarr = $company_array;
										$buyerArr = $buyer_arr;
									}
									else
									{
										$partyarr = $party_arr;
										$buyerArr = $buyer_buyer;
									}
	
									foreach($within_group_data as $embellishment_job=>$embellishment_job_data)
									{
										foreach($embellishment_job_data as $order_no=>$order_no_data)
										{
											$buyer_wise_rate=$buyer_wise_qty=$buyer_wise_amount=$buyer_wise_recipie_qty=$buyer_wise_req_qty=0;
											foreach($order_no_data as $party_id=>$party_id_data)
											{
												$style_wise_rate=$style_wise_qty=$style_wise_amount=$style_wise_recipie_qty=$style_wise_req_qty=0;
											//	foreach($party_id_data as $buyer_style_ref=>$buyer_style_data)
												//{
													foreach($party_id_data as $product_id=>$row)
													{
														if ($k%2==0)  
														$bgcolor="#E9F3FF";
														else
														$bgcolor="#FFFFFF";
														$issue_dtls_id=chop($row[('issue_dtls_id')],',');
														$po_id=explode(",",$row['po_id']);
														 //echo $po_id; buyer_po_id
														
														//$po_id=explode(",",$row[csf('order_id')]);
														$buyer_po=$buyer_style=$job_no=$order_no=$buyerBuyer=$gmts_item_id=$body_part_name=$embl_type=$color_id=$main_process_id=$buyer_buyer=$item_description=$item_group_id=$sub_group_name=$item_size=$buyers_po_ids=$order_uoms=''; 
														foreach($po_id as $q1 =>$val) 
														{
															
															//echo $val;
															
															//echo $val;
															if($buyers_po_ids=="") $buyers_po_ids=$buyer_po_arr[$val]['buyer_po_id']; else $buyers_po_ids.=','.$buyer_po_arr[$val]['buyer_po_id'];
															
															
															if($order_uoms=="") $order_uoms=$unit_of_measurement[$buyer_po_arr[$val]['order_uom']]; else $order_uoms.=','.$unit_of_measurement[$buyer_po_arr[$val]['order_uom']];
															
															//echo $buyers_po_ids;
															if($buyer_po=="") $buyer_po=$buyer_po_arr[$val]['po']; else $buyer_po.=','.$buyer_po_arr[$val]['po'];
															if($buyer_style=="") $buyer_style=$buyer_po_arr[$val]['style']; else $buyer_style.=','.$buyer_po_arr[$val]['style'];
															if($job_no=="") $job_no=$buyer_po_arr[$val]['job']; else $job_no.=','.$buyer_po_arr[$val]['job'];
															if($order_no=="") $order_no=$buyer_po_arr[$val]['order_no']; else $order_no.=','.$buyer_po_arr[$val]['order_no'];
															//if($party_id=="") $party_id=$partyarr[$buyer_po_arr[$val]['party_id']]; else $party_id.=','.$partyarr[$buyer_po_arr[$val]['party_id']];
															if($gmts_item_id=="") $gmts_item_id=$buyer_po_arr[$val]['gmts_item_id']; else $gmts_item_id.=','.$buyer_po_arr[$val]['gmts_item_id'];
															if($body_part_name=="") $body_part_name=$body_part[$buyer_po_arr[$val]['body_part']]; else $body_part_name.=','.$body_part[$buyer_po_arr[$val]['body_part']];
															if($embl_type=="") $embl_type=$emblishment_print_type[$buyer_po_arr[$val]['embl_type']]; else $embl_type.=','.$emblishment_print_type[$buyer_po_arr[$val]['embl_type']];
															if($color_id=="") $color_id=$buyer_po_arr[$val]['color_id']; else $color_id.=','.$buyer_po_arr[$val]['color_id'];
	
	
															if($main_process_id=="") $main_process_id=$buyer_po_arr[$val]['main_process_id']; else $main_process_id.=','.$buyer_po_arr[$val]['main_process_id'];
															//if($buyer_buyer=="") $buyer_buyer=$buyer_po_arr[$val]['buyer_buyer']; else $buyer_buyer.=','.$buyer_po_arr[$val]['buyer_buyer'];
															$within_group=$buyer_po_arr[$val]['within_group'];
															
															  
															if ($within_group==1)
															 {
																if($buyer_buyer=="") $buyer_buyer=$buyerArr[$buyer_po_arr[$val]['buyer_buyer']]; else $buyer_buyer.=','.$buyerArr[$buyer_po_arr[$val]['buyer_buyer']];								 //echo $buyer_buyer; 
															}
															else
															{
															   if($buyer_buyer=="") $buyer_buyer=$buyer_po_arr[$val]['buyer_buyer']; else $buyer_buyer.=','.$buyer_po_arr[$val]['buyer_buyer'];
															}
															if($within_group=="") $within_group=$buyer_po_arr[$val]['within_group']; else $within_group.=','.$buyer_po_arr[$val]['within_group'];
															
															
															 
															//else
																//$qty +=$buyer_po_arr[$val]['qty'];
														}
														
														 // $buyers_po_ids=implode(",",array_unique(explode(",",chop($buyers_po_ids,','))));
														  
														 
														$buyers_po_ids=explode(",",trim($buyers_po_ids, ','));
														
														
	
														foreach($buyers_po_ids as $k1 => $val)
														{
															
															//echo $val;
															if($k1 == 0)
																$int_ref=$int_ref_arr[$val]['grouping'];
															else
																$int_ref.=','.$int_ref_arr[$val]['grouping'];
														}
	
														
														$buyer_po=implode(",",array_unique(explode(",",chop($buyer_po,','))));
														$buyer_style=implode(",",array_unique(explode(",",chop($buyer_style,','))));
														$job_no=implode(",",array_unique(explode(",",chop($job_no,','))));
														$order_no=implode(",",array_unique(explode(",",chop($order_no,','))));
														$buyer_buyer=implode(",",array_unique(explode(",",chop($buyer_buyer,','))));
														//$party_id=implode(",",array_unique(explode(",",chop($party_id,','))));
														$gmts_item_id=implode(",",array_unique(explode(",",chop($gmts_item_id,','))));
														$body_part_name=implode(",",array_unique(explode(",",chop($body_part_name,','))));
														$embl_type=implode(",",array_unique(explode(",",chop($embl_type,','))));
														$color_id=implode(",",array_unique(explode(",",chop($color_id,','))));
														//$within_group=implode(",",array_unique(explode(",",chop($buyer_po,','))));
														$main_process_id=implode(",",array_unique(explode(",",chop($main_process_id,','))));
														$int_ref=implode(",",array_unique(explode(",",chop($int_ref,','))));
														$order_uoms=implode(",",array_unique(explode(",",chop($order_uoms,','))));
	
														$item_description=$product_arr[$product_id]['item_description'];
														$item_group_id=$product_arr[$product_id]['item_group_id'];
														$sub_group_name=$product_arr[$product_id]['sub_group_name'];
														$item_size=$product_arr[$product_id]['item_size']; 
														 $qty=$buyer_order_qty_arr[$embellishment_job]['qty']
														?>
														<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k;?>">
                                                            <td class="brk_word" width="30" align="center"><? echo $k ;?></td>
                                                            <td class="brk_word" width="80" align="center"><? echo change_date_format($issue_date); ?></td>
                                                            <td class="brk_word" width="100" align="center"><? echo $issue_number; ?></td>
                                                            <td class="brk_word" width="120" align="center"><? echo $row[('recipe_no')]; ?></td>
                                                            <td class="brk_word" width="120" align="center"><? echo $row[('req_no')]; ?></td>
                                                            <td class="brk_word" width="120" align="center"><? echo $embellishment_job; ?></td>
                                                            <td class="brk_word" width="120" align="center"><? echo $order_no; ?></td>
                                                            <td class="brk_word" width="80" align="center"><? echo $int_ref; ?></td>
                                                            <td class="brk_word" width="100" align="center"><? echo $partyarr[$party_id]; ?></td>
                                                            <td class="brk_word" width="120" align="center"><? echo $buyer_buyer; ?></td>
                                                            <td class="brk_word" width="120" align="center"><? echo $buyer_style; ?></td>
                                                            <td class="brk_word" width="100" align="center"><? echo $color_arr[$row[('sub_process')]]; ?></td>
                                                            <td class="brk_word" width="85" align="center"><? echo $embl_type; ?></td>
                                                            <td class="brk_word" width="65" align="center"><? echo 'Printing'; ?></td>
                                                            <td class="brk_word" width="85" align="center"><? echo $row[('prod_id')];?></td>
                                                            <td class="brk_word" width="85" align="center"><? echo $item_category[$row[('item_category')]];?></td>
                                                            <td class="brk_word" width="120" align="center"><? echo $item_description; ?></td>
                                                            <td class="brk_word" width="85" align="center"><? echo $order_uoms; ?></td>
                                                            <td class="brk_word" width="85" align="center"></td>
                                                            <td class="brk_word" width="85" align="right"><? echo number_format($row[('required_qnty')],2,".",""); ?></td>
                                                            <td class="brk_word" width="60" align="right"><? echo number_format($row[('req_qny_edit')],2,".","");
															 ?></td>
                                                            <td class="brk_word" width="85" align="right"><? echo number_format($row[('cons_amount')]/$row[('req_qny_edit')],2,".",""); ?></td>
                                                            <td class="brk_word" width="85" align="right"><? echo number_format($row[('cons_amount')],2,".",""); ?></td>
                                                            <td class="brk_word" align="center"></td>
														</tr>
														<?
														$k++;
														$total_recipie_qty+=$row[('recipe_qnty')];
														$total_req_qty+=$row[('required_qnty')];
														$total_qty+=$row[('req_qny_edit')];
														$total_rate+=$row[('cons_rate')];
														$total_amount+=$row[('cons_amount')];
	
														$style_wise_recipie_qty+=$row[('recipe_qnty')];
														$style_wise_req_qty+=$row[('required_qnty')];
														$style_wise_qty+=$row[('req_qny_edit')];
														$style_wise_rate+=$row[('cons_rate')];
														$style_wise_amount+=$row[('cons_amount')];
	
														$buyer_wise_recipie_qty+=$row[('recipe_qnty')];
														$buyer_wise_req_qty+=$row[('required_qnty')];
														$buyer_wise_qty+=$row[('req_qny_edit')];
														$buyer_wise_rate+=$row[('cons_rate')];
														$buyer_wise_amount+=$row[('cons_amount')];
																					
													}
													?>
													<tr bgcolor="#f7ffdd">
														<td colspan="18" align="right"><strong>Job Wise Sub Total :</strong></td>
														<td align="right"><strong><? echo number_format($qty,2,".","");  ?></strong> </td>
														<td align="right"><strong><? echo number_format($style_wise_req_qty,2,".","");  ?></strong> </td>
														<td align="right"><strong><? echo number_format($style_wise_qty,2,".","");  ?></strong> </td>
														<td align="right"><strong>&nbsp;</strong> </td>
														<td align="right"><strong><? echo number_format($style_wise_amount,2,".","");  ?></strong> </td>
														<td align="right"><? echo number_format($style_wise_amount/$qty,2,".","");  ?></td>
													</tr>
													<?
													$total_order_qty+=$qty;
													$total_order_qty_cost+=number_format($style_wise_amount/$qty,2,".","");
												//}
											}
											 
										}
									}
								}
							}
						}
					}
					?>
					<tr bgcolor="#ddffdf">
						<td colspan="18" align="right"><strong>Grand Total :</strong></td>
						<td align="right"><strong><? echo number_format($total_order_qty,2,".","");  ?></strong> </td>
						<td align="right"><strong><? echo number_format($total_req_qty,2,".","");  ?></strong> </td>
						<td align="right"><strong><? echo number_format($total_qty,2,".","");  ?></strong> </td>
						<td align="right"><strong>&nbsp;</strong> </td>
						<td align="right"><strong><? echo number_format($total_amount,2,".","");  ?></strong> </td>
						<td align="right"><? echo number_format($total_order_qty_cost,2,".","");  ?></td>
					</tr>
									
				</tbody>
			</table>
		</div>
		</fieldset>
	</div>	
    <?
    $r_id3=execute_query("delete from tmp_job_no where userid=$user_id and entry_form=1220");
	if($db_type==0)
	{
		if($r_id3)
		{
			mysql_query("COMMIT");
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($r_id3)
		{
			oci_commit($con);
		}
	}
	}
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

if($action=="issue_qty_popup")
{
	echo load_html_head_contents("Issue Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	//$expData=explode('_',$order_id);
	//$order_id=$expData[0];
	//$process_id=$expData[1];
	?>
        <fieldset style="width:1120px">
            <div style="width:100%;" align="center">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                    <thead>
                    	<tr>							  	   	  	  	  
                            <th width="30">SL</th>
                            <th width="60">Issue Date</th>
                            <th width="110">Recipe No</th>
                            <th width="100">Recipie Color</th>
                            <th width="80">Garments Color</th>
                            <th width="100">Item Group</th>
                            <th width="150">Item Name</th>
                            <th width="40">Ratio</th>
                            <th width="60">Recipie  Qty</th>
                            <th width="120">Req No</th>
                            <th width="60">Req Qty</th>
                            <th width="120">Issue No</th>
                            <th width="">Issue Qty</th>
                    	</tr>
                    </thead>
                </table>
            </div>  
            <div style="width:100%; max-height:330px; overflow-y:scroll" align="left">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                    <?
                    $color_arr=return_library_array("SELECT id,color_name from lib_color where status_active=1 and is_deleted=0" ,"id","color_name");
                    $group_arr=return_library_array( "SELECT id,item_name from lib_item_group where  status_active=1 and is_deleted=0",'id','item_name');
					$sql_dtls = "SELECT a.location_id, a.issue_date, a.issue_basis, a.req_no, a.req_id, a.issue_purpose, a.company_id, a.loan_party, a.lap_dip_no, a.batch_no, a.order_id, a.sub_order_id, a.style_ref, a.store_id, a.buyer_job_no, a.is_posted_account, a.lc_company, a.floor_id, a.machine_id, a.remarks, b.id,a.issue_number,b.store_id,b.cons_uom, b.cons_quantity, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id,b.cons_rate,b.cons_amount,c.item_description, c.item_group_id, c.sub_group_name, c.item_size, d.sub_process,d.id as issue_dtls_id, d.item_category, d.dose_base, d.ratio, d.recipe_qnty, d.adjust_percent, d.adjust_type, d.required_qnty, d.req_qny_edit, d.recipe_id, e.recipe_no from inv_issue_master a, inv_transaction b, product_details_master c, dyes_chem_issue_dtls d, pro_recipe_entry_mst e
					where a.id=d.mst_id and b.id =d.trans_id and d.product_id=c.id  and b.transaction_type=2 and a.entry_form=250 and b.item_category in (5,6,7,23,22) and to_char(e.id)= d.recipe_id and e.entry_form=220 and d.id in ($issue_dtls_ids) and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0 order by d.sub_process ";

                   	//echo $sql;
					$sql_dtls_res= sql_select($sql_dtls);  $k=0;
					$tot_recipe_qty=$tot_required_qnty=$tot_req_qny_edit=0;
					foreach( $sql_dtls_res as $row )
                    {
                        $k++; 
                        if ($k%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";

                        ?>
                        <!-- Issue Date	Recipe No	Recipie Color	Garments Color	Item Group	Item Name	Ratio	 Recipie  Qty 	 Req No  	 Req Qty 	 Issue No 	 Issue Qty  -->

						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
							<td width="30"><? echo $i; ?></td>
                            <td width="60"><? echo change_date_format($row[csf("issue_date")]);?> </td>
                            <td width="110"><? echo $row[csf("recipe_no")];?></td>
                            <td width="100"><? echo $color_arr[$row[csf("sub_process")]];?></td>
                            <td width="80"><? echo $color_arr[$row[csf("sub_process")]];?></td>
                            <td width="100"><? echo $group_arr[$row[csf("item_group_id")]];?></td>
                            <td width="150"><? echo $row[csf("item_description")];?></td>
                            <td width="40" align="center"><? echo $row[csf("ratio")];?></td>
                            <td width="60" align="right"><? echo number_format($row[csf("recipe_qnty")],2);?></td>
                            <td width="120"><? echo $row[csf("req_no")];?></td>
                            <td width="60" align="right"><? echo number_format($row[csf("required_qnty")],2);?></td>
                            <td width="120"><? echo $row[csf("issue_number")];?></td>
                            <td width="" align="right"><? echo number_format($row[csf("req_qny_edit")],2);?></td>
						</tr>
						<?
						$tot_recipe_qty+=$row[csf("recipe_qnty")];
						$tot_required_qnty+=$row[csf("required_qnty")];
						$tot_req_qny_edit+=$row[csf("req_qny_edit")];
					}
					?>
                    <tr class="tbl_bottom">
                    	<td colspan="8" align="right">Total: </td>
                        <td align="right"><p><? echo number_format($tot_recipe_qty,2); ?></p></td>
                        <td>&nbsp;</td>
                        <td align="right"><p><? echo number_format($tot_required_qnty,2); ?></p></td>
                        <td>&nbsp;</td>
                        <td align="right"><p><? echo number_format($tot_req_qny_edit,2); ?></p></td>
                    </tr>
                </table>
            </div> 
	</fieldset>
 </div> 
<?
exit();
}

?>