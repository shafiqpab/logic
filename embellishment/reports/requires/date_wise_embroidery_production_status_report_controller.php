<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
$size_arr=return_library_array( "SELECT id, size_name from lib_size where status_active=1 and is_deleted=0","id","size_name");
//--------------------------------------------------------------------------------------------------------------------


if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 120, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location--", $selected, "",0 );
	exit();     	 
}

if ($action=="load_drop_down_party_location")
{
	echo create_drop_down( "cbo_party_location_id", 100, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location--", $selected, "",0 );
	exit();     	 
}

if ($action=="load_drop_down_party")
{
	$data=explode('_',$data);
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_id", 125, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "load_drop_down( 'requires/date_wise_embroidery_production_status_report_controller', this.value, 'load_drop_down_party_location', 'party_location_td' );");
		exit();
	}
	else
	{
		echo create_drop_down( "cbo_party_id", 125, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$data[2], "" );
		exit();
	}
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 125, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   	 
	exit();
}



//job search.......................................................
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
			load_drop_down( 'date_wise_embroidery_production_status_report_controller', company+'_'+within_group+'_'+party, 'load_drop_down_party', 'party_td' );
			
			$('#cbo_party_id').attr('disabled','disabled');
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
                            <td id="party_td"><? echo create_drop_down( "cbo_party_id", 140, $blank_array,"", 1, "-- Select Party --", $selected, "" ); ?>
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td>
								<?
									$search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
									echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,"$('#search_by_td').html($(this).find('option:selected').text());$('#txt_search_string').val('');",0 );
								?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $data[2];?>, 'job_search_list_view', 'search_div', 'date_wise_embroidery_production_status_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
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

if($action=="job_search_list_view")
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
			else if($search_by==2) $search_com_cond="and b.po_number='$search_str'";
			else if ($search_by==3) $search_com_cond=" and e.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $search_com_cond=" and d.po_number = '$search_str' ";
			else if ($search_by==5) $search_com_cond=" and e.style_ref_no = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.embellishment_job like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '%$search_str%'";  
			else if ($search_by==3) $search_com_cond=" and e.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and d.po_number like '%$search_str%'";
			else if ($search_by==5) $search_com_cond=" and e.style_ref_no like '%$search_str%'";    
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.embellishment_job like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '$search_str%'";  
			else if ($search_by==3) $search_com_cond=" and e.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and d.po_number like '$search_str%'";  
			else if ($search_by==5) $search_com_cond=" and e.style_ref_no like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.embellishment_job like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '%$search_str'";  
			else if ($search_by==3) $search_com_cond=" and e.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and d.po_number like '%$search_str'";
			else if ($search_by==5) $search_com_cond=" and e.style_ref_no like '%$search_str'";  
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
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
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
	 /*$sql= "select a.id, a.embellishment_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, $color_id_str as color_id, b.buyer_po_id  
	 from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c ,wo_po_break_down d 
	 where a.entry_form=204 and a.embellishment_job=b.job_no_mst and b.booking_dtls_id!=0 and a.status_active=1 and b.status_active=1 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond and b.id=c.mst_id 
	 and b.buyer_po_id=d.id  
	 group by a.id, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, b.buyer_po_id
	 order by a.id DESC";*/
	 
	$sql="SELECT a.id, a.embellishment_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, $color_id_str as color_id, b.buyer_po_id 
  from  subcon_ord_mst a, subcon_ord_breakdown c ,subcon_ord_dtls b
  left join wo_po_break_down d on b.buyer_po_id=d.id and d.is_deleted=0 and d.status_active=1
  left join  wo_po_details_master e on d.job_no_mst=e.job_no and d.is_deleted=0 and d.status_active=1	
	where b.job_no_mst=a.embellishment_job and b.id=c.mst_id and a.entry_form=311 $company $party $job_cond $year_field_cond $search_com_cond $withinGroup  and a.is_deleted =0 group by a.id, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, b.buyer_po_id";
	 
	 

	 //echo $sql;

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

//Buyer Style search.......................................................

if ($action=="style_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	list($company_id,$party_id,$within_group,$job_no)=explode("_",$data);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_order_popup(company,within_group,party)
		{
			load_drop_down( 'date_wise_embroidery_production_status_report_controller', company+'_'+within_group+'_'+party, 'load_drop_down_party', 'party_td' );
			
			$('#cbo_party_id').attr('disabled','disabled');
		}
		
	</script>
	</head>
	<body onLoad="fnc_load_party_order_popup(<? echo $company_id;?>,<? echo $within_group;?>,<? echo $party_id;?>)">
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
                            <td><input type="hidden" id="selected_id">  
								<?   
									echo create_drop_down( "cbo_company_id", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $data[0],"",1 );
                                ?>
                            </td>
                            <td id="party_td"><? echo create_drop_down( "cbo_party_id", 140, $blank_array,"", 1, "-- Select Party --", $selected, "" ); ?>
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td>
								<?
									$search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
									echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,"$('#search_by_td').html($(this).find('option:selected').text());$('#txt_search_string').val('');",0 );
								?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $within_group;?>+'_'+<? echo $year;?>, 'style_no_search_list_view', 'search_div', 'date_wise_embroidery_production_status_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
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

if($action=="style_no_search_list_view")
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
	$data=explode('_',$data);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	$year =$data[8];
	
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { $company=""; echo "PLease Select Company name"; die;}
	if ($data[1]!=0) $party=" and a.party_id='$data[1]'"; else { $party="";}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and b.po_number='$search_str'";
			else if ($search_by==3) $search_com_cond=" and e.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $search_com_cond=" and d.po_number = '$search_str' ";
			else if ($search_by==5) $search_com_cond=" and e.style_ref_no = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.embellishment_job like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '%$search_str%'";  
			else if ($search_by==3) $search_com_cond=" and e.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and d.po_number like '%$search_str%'";
			else if ($search_by==5) $search_com_cond=" and e.style_ref_no like '%$search_str%'";    
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.embellishment_job like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '$search_str%'";  
			else if ($search_by==3) $search_com_cond=" and e.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and d.po_number like '$search_str%'";  
			else if ($search_by==5) $search_com_cond=" and e.style_ref_no like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.embellishment_job like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '%$search_str'";  
			else if ($search_by==3) $search_com_cond=" and e.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and d.po_number like '%$search_str'";
			else if ($search_by==5) $search_com_cond=" and e.style_ref_no like '%$search_str'";  
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
		$year_field="year(a.insert_date) as year"; 
		if(trim($year)!=0) $year_field_cond="and YEAR(a.insert_date)=$year";  else $year_field_cond="";
		$color_id_str="group_concat(c.color_id)";
	}
	else if($db_type==2)
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
		if(trim($year)!=0) $year_field_cond=" and to_char(a.insert_date,'YYYY')=$year"; else $year_field_cond="";
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str="listagg(c.color_id,',') within group (order by c.color_id)";
	}
	
	
	
	

	
	/*$sql="select  e.style_ref_no,b.job_no_mst as job_no ,e.buyer_name,b.buyer_po_id, b.order_no, a.job_no_prefix_num as job_prefix, $year_field,d.po_number from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c ,wo_po_break_down d,wo_po_details_master e  where b.job_no_mst=a.embellishment_job and b.id=c.mst_id and b.buyer_po_id=d.id and d.job_no_mst=e.job_no and a.entry_form=204 $company $party $job_cond $year_field_cond $search_com_cond  and a.is_deleted =0 group by e.style_ref_no,d.po_number, e.buyer_name, b.job_no_mst,b.buyer_po_id, b.order_no, a.job_no_prefix_num, a.company_id, a.insert_date";*/
	
	$sql="SELECT  e.style_ref_no,b.job_no_mst as job_no ,e.buyer_name,b.buyer_po_id, b.order_no, a.job_no_prefix_num as job_prefix, $year_field,d.po_number 
  from  subcon_ord_mst a, subcon_ord_breakdown c ,subcon_ord_dtls b
  left join wo_po_break_down d on b.buyer_po_id=d.id
  left join  wo_po_details_master e on    d.job_no_mst=e.job_no	
	where b.job_no_mst=a.embellishment_job and b.id=c.mst_id and a.entry_form=311 $company $party $job_cond $year_field_cond $search_com_cond $withinGroup  and a.is_deleted =0 and a.status_active=1 group by e.style_ref_no,d.po_number, e.buyer_name, b.job_no_mst,b.buyer_po_id, b.order_no, a.job_no_prefix_num, a.company_id, a.insert_date";
		
	   //echo $sql;
	
	$buyer_arr=return_library_array( "SELECT id,buyer_name from lib_buyer where status_active =1 and is_deleted=0", "id", "buyer_name"  );
	?>
	<table width="700" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="35">SL</th>
                <th width="100">Work Order</th>
                <th width="120">Emb. Job no</th>
                <th width="120">Style Ref</th>
                <th width="100">Buyer</th>
                <th width="100">Buyer PO</th>
                <th>Year</th>
            </tr>
        </thead>
    </table>
    <div style="max-height:340px; overflow:auto;">
        <table id="table_body2" width="700" border="1" rules="all" class="rpt_table">
			<? 
			$rows=sql_select($sql);
            $i=1;
            foreach($rows as $data)
            {
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $data[csf('style_ref_no')]; ?>')" style="cursor:pointer;">
                    <td width="35"><? echo $i; ?></td>
                    <td width="100"><p><? echo $data[csf('order_no')]; ?></p></td>
                    <td width="120"><p><? echo $data[csf('job_no')]; ?></p></td>
                    <td width="120"><p><? echo $data[csf('style_ref_no')]; ?></p></td>
                    <td width="100"><p><? echo $buyer_arr[$data[csf('buyer_name')]]; ?></p></td>
                    <td width="100"><p><? echo $data[csf('po_number')]; ?></p></td>
                    <td align="center"><p><? echo $data[csf('year')]; ?></p></td>
				</tr>
				<? $i++; 
			} ?>
		</table>
	</div>
	<script> setFilterGrid("table_body2",-1); </script>
	<?
	exit();
}

if ($action=="int_ref_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	list($company_id,$party_id,$within_group,$job_no)=explode("_",$data);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_order_popup(company,within_group,party)
		{
			load_drop_down( 'date_wise_embroidery_production_status_report_controller', company+'_'+within_group+'_'+party, 'load_drop_down_party', 'party_td' );
			
			$('#cbo_party_id').attr('disabled','disabled');
		}
		
	</script>
	</head>
	<body onLoad="fnc_load_party_order_popup(<? echo $company_id;?>,<? echo $within_group;?>,<? echo $party_id;?>)">
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
                            <td><input type="hidden" id="selected_id">  
								<?   
									echo create_drop_down( "cbo_company_id", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $data[0],"",1 );
                                ?>
                            </td>
                            <td id="party_td"><? echo create_drop_down( "cbo_party_id", 140, $blank_array,"", 1, "-- Select Party --", $selected, "" ); ?>
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td>
								<?
									$search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
									echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,"$('#search_by_td').html($(this).find('option:selected').text());$('#txt_search_string').val('');",0 );
								?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $within_group;?>+'_'+<? echo $year;?>, 'int_ref_popup_search_list_view', 'search_div', 'date_wise_embroidery_production_status_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
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

if($action=="int_ref_popup_search_list_view")
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
	$data=explode('_',$data);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	$year =$data[8];
	
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { $company=""; echo "PLease Select Company name"; die;}
	if ($data[1]!=0) $party=" and a.party_id='$data[1]'"; else { $party="";}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and b.po_number='$search_str'";
			else if ($search_by==3) $search_com_cond=" and e.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $search_com_cond=" and d.po_number = '$search_str' ";
			else if ($search_by==5) $search_com_cond=" and e.style_ref_no = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.embellishment_job like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '%$search_str%'";  
			else if ($search_by==3) $search_com_cond=" and e.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and d.po_number like '%$search_str%'";
			else if ($search_by==5) $search_com_cond=" and e.style_ref_no like '%$search_str%'";    
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.embellishment_job like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '$search_str%'";  
			else if ($search_by==3) $search_com_cond=" and e.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and d.po_number like '$search_str%'";  
			else if ($search_by==5) $search_com_cond=" and e.style_ref_no like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.embellishment_job like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '%$search_str'";  
			else if ($search_by==3) $search_com_cond=" and e.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and d.po_number like '%$search_str'";
			else if ($search_by==5) $search_com_cond=" and e.style_ref_no like '%$search_str'";  
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
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
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
		$year_field="year(a.insert_date) as year"; 
		if(trim($year)!=0) $year_field_cond="and YEAR(a.insert_date)=$year";  else $year_field_cond="";
		$color_id_str="group_concat(c.color_id)";
	}
	else if($db_type==2)
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
		if(trim($year)!=0) $year_field_cond=" and to_char(a.insert_date,'YYYY')=$year"; else $year_field_cond="";
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str="listagg(c.color_id,',') within group (order by c.color_id)";
	}
	
	/*$sql="select  e.style_ref_no,b.job_no_mst as job_no ,e.buyer_name,b.buyer_po_id, b.order_no, a.job_no_prefix_num as job_prefix, $year_field,d.po_number from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c ,wo_po_break_down d,wo_po_details_master e  where b.job_no_mst=a.embellishment_job and b.id=c.mst_id and b.buyer_po_id=d.id and d.job_no_mst=e.job_no and a.entry_form=204 $company $party $job_cond $year_field_cond $search_com_cond  and a.is_deleted =0 group by e.style_ref_no,d.po_number, e.buyer_name, b.job_no_mst,b.buyer_po_id, b.order_no, a.job_no_prefix_num, a.company_id, a.insert_date";*/
	
	$sql="SELECT  e.style_ref_no,b.job_no_mst as job_no ,e.buyer_name,b.buyer_po_id, b.order_no, a.job_no_prefix_num as job_prefix, $year_field,d.po_number,d.grouping 
  from  subcon_ord_mst a, subcon_ord_breakdown c ,subcon_ord_dtls b
  left join wo_po_break_down d on b.buyer_po_id=d.id
  left join  wo_po_details_master e on d.job_no_mst=e.job_no	
	where b.job_no_mst=a.embellishment_job and b.id=c.mst_id and a.entry_form=311 $company $party $job_cond $year_field_cond $search_com_cond $withinGroup  and a.is_deleted =0 group by e.style_ref_no,d.po_number,d.grouping, e.buyer_name, b.job_no_mst,b.buyer_po_id, b.order_no, a.job_no_prefix_num, a.company_id, a.insert_date";
		
	   // echo $sql;
	
	$buyer_arr=return_library_array( "SELECT id,buyer_name from lib_buyer where status_active =1 and is_deleted=0", "id", "buyer_name"  );
	?>
	<table width="700" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="35">SL</th>
                <th width="100">Work Order</th>
                <th width="120">Emb. Job no</th>
                <th width="120">Internal Ref</th>
                <th width="100">Buyer</th>
                <th width="100">Buyer PO</th>
                <th>Year</th>
            </tr>
        </thead>
    </table>
    <div style="max-height:340px; overflow:auto;">
        <table id="table_body2" width="700" border="1" rules="all" class="rpt_table">
			<? 
			$rows=sql_select($sql);
            $i=1;
            foreach($rows as $data)
            {
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $data[csf('grouping')]; ?>')" style="cursor:pointer;">
                    <td width="35"><? echo $i; ?></td>
                    <td width="100"><p><? echo $data[csf('order_no')]; ?></p></td>
                    <td width="120"><p><? echo $data[csf('job_no')]; ?></p></td>
                    <td width="120"><p><? echo $data[csf('grouping')]; ?></p></td>
                    <td width="100"><p><? echo $buyer_arr[$data[csf('buyer_name')]]; ?></p></td>
                    <td width="100"><p><? echo $data[csf('po_number')]; ?></p></td>
                    <td align="center"><p><? echo $data[csf('year')]; ?></p></td>
				</tr>
				<? $i++; 
			} ?>
		</table>
	</div>
	<script> setFilterGrid("table_body2",-1); </script>
	<?
	exit();
}

//Generat report.......................................................
if($action=="report_generate")
{  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$buyer_library=return_library_array( "SELECT id,buyer_name from lib_buyer where status_active =1 and is_deleted=0", "id", "buyer_name"  ); 
	$company_library=return_library_array( "SELECT id,company_name from lib_company where status_active =1 and is_deleted=0", "id", "company_name"  );


	if(str_replace("'","",$cbo_company_id)==0)$company_name=""; else $company_name=" and a.company_id=$cbo_company_id";		
	if(str_replace("'","",$cbo_within_group)==0)$cbo_within_group=""; else $cbo_within_group=" and a.within_group=$cbo_within_group";		
	if(str_replace("'","",$cbo_party_id)==0)$party_con="";else $party_con=" and a.party_id=$cbo_party_id";	
	if(str_replace("'","",$txt_style_ref)=='')$style_con="";else $style_con=" and e.style_ref_no like('%".trim(str_replace("'","",$txt_style_ref))."%')";
	if(str_replace("'","",$txt_int_ref)=='')$int_ref_con="";else $int_ref_con=" and d.grouping like('%".trim(str_replace("'","",$txt_int_ref))."%')";
	if(str_replace("'","",trim($txt_date_from))=="")$date_con=""; else $date_con=" and b.production_date between $txt_date_from and $txt_date_to";
	if(str_replace("'","",trim($txt_job_no))!="") $job_cond="and a.embellishment_job = $txt_job_no"; 
	
	//============================= MAIN QUERY ================================



	$sql="SELECT a.id,listagg(b.id,',') within group(order by b.id) as po_id, a.party_id, a.embellishment_job as job_no , b.order_id,e.job_no as buyer_job_no,e.style_ref_no, a.job_no_prefix_num as job_prefix,e.client_id,d.grouping as int_ref,b.order_uom,a.within_group,b.buyer_po_no,sum(b.order_quantity*12) as order_qty,sum(b.amount) as order_amt
  	from subcon_ord_mst a, subcon_ord_dtls b
	left join wo_po_break_down d on b.buyer_po_id=d.id and d.is_deleted=0 and d.status_active=1
	left join wo_po_details_master e on d.job_no_mst=e.job_no and e.is_deleted=0 and e.status_active=1 
	where b.job_no_mst=a.embellishment_job and a.id=b.mst_id and a.entry_form=311 $cbo_within_group
	 $company_name $job_cond $party_con $style_con $int_ref_con
	  and a.is_deleted =0 group by a.id,a.party_id, a.embellishment_job,b.order_id,e.job_no, e.style_ref_no, a.job_no_prefix_num,e.client_id,d.grouping,b.order_uom,a.within_group,b.buyer_po_no";
	 //echo $sql;
	$sql_result=sql_select($sql);

	foreach($sql_result as $row)
	{
		$totalOrderQtyPcs+=$row[csf('order_qty')];
		
		$poArr[$row[csf('buyer_po_id')]]=$row[csf('buyer_po_id')];
		$embJobArr[$row[csf('job_no')]]=$row[csf('job_no')];
	}	
		
	//=========================== MATERIAL RECEIVE & ISSUE ===============================
	$p=1;
	$po_chunk_arr=array_chunk($embJobArr,999);
	foreach($po_chunk_arr as $po_arr)
	{
		if($p==1){$sql_con =" and (a.embl_job_no in('".implode("','",$po_arr)."')";} 
		else{$sql_con .=" or a.embl_job_no in('".implode("','",$po_arr)."')";}
		$p++;
	}
	$sql_con .=")";	
	$get_qty = "";
	if(str_replace("'", "", $txt_date_from) !="")
	{
		$get_qty = ",sum(case when a.subcon_date between $txt_date_from and $txt_date_to then b.quantity else 0 end) as qty";
	}
	else
	{
		$get_qty = ",sum(b.quantity) as qty";
	}
	// echo $sql_con;die();
	$sql_materials="SELECT c.buyer_po_no,a.embl_job_no as job_no,a.entry_form $get_qty
    from sub_material_mst a,sub_material_dtls b,subcon_ord_dtls c where a.id=b.mst_id and c.id=b.job_dtls_id $sql_con and a.entry_form in( 312,313) and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 
    group by c.buyer_po_no,a.embl_job_no,a.entry_form";
   //echo $sql_materials;die();
	$sql_materials_result=sql_select($sql_materials);
	$materialDataArr=array();
	foreach($sql_materials_result as $row)
	{
		//205=Receive; 207=Issue
		//$materialDataArr[$row[csf('job_no')]][$row[csf('entry_form')]] = $row[csf('qty')];
		$materialDataArr[$row[csf('job_no')]][$row[csf('buyer_po_no')]][$row[csf('entry_form')]]= $row[csf('qty')];
	}
	// print_r($materialDataArr);
	//=============================== PRODUCTION & QC ==========================================
	$p=1;
	$po_chunk_arr=array_chunk($embJobArr,999);
	foreach($po_chunk_arr as $po_arr)
	{
		if($p==1){$sql_con =" and (a.job_no in('".implode("','",$po_arr)."')";} 
		else{$sql_con .=" or a.job_no in('".implode("','",$po_arr)."')";}
		$p++;
	}
	$sql_con .=")";
	$prod_date_cond = "";
	if(str_replace("'", "", $txt_date_from) !="")
	{
		$prod_date_cond = " and b.production_date between $txt_date_from and $txt_date_to";
	}
	$sql_production="SELECT a.job_no,a.entry_form,c.buyer_po_no,sum(b.qcpass_qty) as qcpass_qty,sum(b.reje_qty) as reje_qty
  	from subcon_embel_production_mst a,subcon_embel_production_dtls b,subcon_ord_dtls c 
  	where a.id=b.mst_id and c.id=b.po_id $sql_con $prod_date_cond and a.entry_form in( 315,324)  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1
   	group by a.job_no,a.entry_form,c.buyer_po_no";
   	//echo $sql_production;die();
	$sql_production_result=sql_select($sql_production);
	$productionDataArr=array();
	$QcDataArr=array();
	foreach($sql_production_result as $row)
	{
		//222=Production; 223=QC
		//$productionDataArr[$row[csf('job_no')]][$row[csf('entry_form')]]['qcpass_qty']=$row[csf('qcpass_qty')];
		//$productionDataArr[$row[csf('job_no')]][$row[csf('entry_form')]]['reje_qty']=$row[csf('reje_qty')];
		
		
		$productionDataArr[$row[csf('job_no')]][$row[csf('buyer_po_no')]][$row[csf('entry_form')]]['qcpass_qty']=$row[csf('qcpass_qty')];
		$productionDataArr[$row[csf('job_no')]][$row[csf('buyer_po_no')]][$row[csf('entry_form')]]['reje_qty']=$row[csf('reje_qty')];
		
	}	
	// print_r($productionDataArr);
	//===================================== DELIVERY ==========================================
	$p=1;
	$po_chunk_arr=array_chunk($embJobArr,999);
	foreach($po_chunk_arr as $po_arr)
	{
		if($p==1){$sql_con =" and (a.job_no in('".implode("','",$po_arr)."')";} 
		else{$sql_con .=" or a.job_no in('".implode("','",$po_arr)."')";}
		$p++;
	}
	$sql_con .=")";
	$del_date_cond = "";
	if(str_replace("'", "", $txt_date_from) !="")
	{
		$del_date_cond = " and a.delivery_date between $txt_date_from and $txt_date_to";
	}
	$sql_delivery="SELECT a.job_no, c.buyer_po_no,sum(b.delivery_qty) as delivery_qty from subcon_delivery_mst a,subcon_delivery_dtls b,subcon_ord_dtls c where a.id=b.mst_id and c.id=b.order_id $sql_con $del_date_cond and a.entry_form=325 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by a.job_no,c.buyer_po_no"; 
	// echo $sql_delivery;
	$sql_delivery_result=sql_select($sql_delivery);
	$deliveryDataArr=array();
	foreach($sql_delivery_result as $row)
	{
		//$deliveryDataArr[$row[csf('job_no')]]=$row[csf('delivery_qty')];
		$deliveryDataArr[$row[csf('job_no')]][$row[csf('buyer_po_no')]]=$row[csf('delivery_qty')];
	}	
	
	//========================================= BILL =====================================
	$p=1;
	$po_chunk_arr=array_chunk($embJobArr,999);
	foreach($po_chunk_arr as $po_arr)
	{
		if($p==1){$sql_con =" and (c.job_no_mst in('".implode("','",$po_arr)."')";} 
		else{$sql_con .=" or c.job_no_mst in('".implode("','",$po_arr)."')";}
		$p++;
	}
	$sql_con .=")";
	$bill_date_cond = "";
	if(str_replace("'", "", $txt_date_from) !="")
	{
		$bill_date_cond = " and a.bill_date between $txt_date_from and $txt_date_to";
	}
	$sql_bill="SELECT c.job_no_mst as job_no,c.buyer_po_no, sum(b.delivery_qty) as tbill_qty, sum(b.amount) as total_bill_amount from subcon_inbound_bill_mst a,subcon_inbound_bill_dtls b,subcon_ord_dtls c where a.id=b.mst_id and c.id=b.order_id $company_name  $sql_con $bill_date_cond and b.process_id=14 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by c.job_no_mst,c.buyer_po_no"; 
	//echo $sql_bill;
	$sql_bill_result=sql_select($sql_bill);
	$billDataArr=array();
	foreach($sql_bill_result as $row)
	{		
		//$billDataArr[$row[csf('job_no')]]['billQty']=$row[csf('tbill_qty')];
		//$billDataArr[$row[csf('job_no')]]['billAmt']=$row[csf('total_bill_amount')];
		$billDataArr[$row[csf('job_no')]][$row[csf('buyer_po_no')]]['billQty']=$row[csf('tbill_qty')];
		$billDataArr[$row[csf('job_no')]][$row[csf('buyer_po_no')]]['billAmt']=$row[csf('total_bill_amount')];
	}
		
	ob_start();
	?>
	<style type="text/css">
		table tr td{word-break: break-all;word-wrap: break-word;}
	</style>
    <div style="width:1535px;padding: 0 5px;"> 
        <table width="1515" cellspacing="0" >
            <tr style="border:none;">
                <td colspan="18" align="center" style="border:none; font-size:20px; font-weight: bold;padding-bottom: 10px;">
                    <? echo $company_library[str_replace("'","",$cbo_company_id)]; ?>
                </td>
            </tr>
        </table>
        
        <div style="float:left; width:1535px">
            <table width="1515" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
                <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="100">Party</th>
                        
                        <th width="100">Buyer Job No</th>
                        <th width="100">Emb.Job No</th>
                        <th width="100">Internal Ref.</th>
                        <th width="100">Buyer Style</th>
                        <th width="80">Job Qty.</th>
                        <th width="80">Job Value</th>
                        <th width="60">UOM</th>
                        <th width="80">Mat. Rcv.</th>
                        <th width="80">Mat. Issue</th>
                        <th width="80">Prod. Qty</th>
                        <th width="80">QC Qty</th>
                        <th width="80">Delivery Qty</th>
                        <th width="80">Rej. Qty</th>
                        <th width="80">Bill Qty</th>
                        <th width="80">Bill Amount</th>                        
                    </tr>
                </thead>
            </table>
           
            <div style="max-height:350px; width:1535px; overflow-y:auto;" id="scroll_body" >
                <table cellspacing="0" border="1" class="rpt_table"  width="1515" rules="all" id="table_body" cellpadding="0">
					<tbody>
						<?
						$i=1;
						$tot_job_qty 	= 0;
						$tot_job_val 	= 0;
						$tot_mat_rcv 	= 0;
						$tot_mat_issue 	= 0;
						$tot_prod_qty 	= 0;
						$tot_qc_qty 	= 0;
						$tot_del_qty 	= 0;
						$tot_rej_qty 	= 0;
						$tot_bill_qty 	= 0;
						$tot_bill_amt 	= 0;
						foreach ($sql_result as $val) 
						{
							$mat_rcv_qty 	= $materialDataArr[$val[csf('job_no')]][$val[csf('buyer_po_no')]][312];
							$mat_issue_qty 	= $materialDataArr[$val[csf('job_no')]][$val[csf('buyer_po_no')]][313];

							$prod_qty 		= $productionDataArr[$val[csf('job_no')]][$val[csf('buyer_po_no')]][315]['qcpass_qty'];
							$qc_qty 		= $productionDataArr[$val[csf('job_no')]][$val[csf('buyer_po_no')]][324]['qcpass_qty'];
							$rej_qty 		= $productionDataArr[$val[csf('job_no')]][$val[csf('buyer_po_no')]][324]['reje_qty'];

							$delivery_qty	= $deliveryDataArr[$val[csf('job_no')]][$val[csf('buyer_po_no')]];

							$bill_qty 		= $billDataArr[$val[csf('job_no')]][$val[csf('buyer_po_no')]]['billQty'];
							$bill_amount	= $billDataArr[$val[csf('job_no')]][$val[csf('buyer_po_no')]]['billAmt'];
							$job_no 	= $val[csf('job_no')];
							$order_id 	= $val[csf('order_id')];
							$po_id 	= $val[csf('po_id')];
							$within_group= $val[csf('within_group')];
							$date_from 	= str_replace("'", "", $txt_date_from);
							$date_to 	= str_replace("'", "", $txt_date_to);
							if($mat_rcv_qty !=0 || $mat_issue_qty !=0 || $prod_qty !=0 || $qc_qty !=0 || $delivery_qty !=0 || $bill_qty !=0)
							{
								$bgcolor 	= ($i%2==0)?"#E9F3FF":"#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
			                        <td width="35"><? echo $i;?></td>
			                        <td width="100">
			                        	<?
			                        	$cbo_company_id=str_replace("'","",$cbo_company_id);
			                        	if($val[csf('within_group')]==1)
			                        	{
			                        		echo $company_library[$val[csf('party_id')]];
			                        	}
			                        	else
			                        	{
			                        		echo $buyer_library[$val[csf('party_id')]];
			                        	}
			                        	?>
			                        </td>
			                        
			                        <td width="100" align="center"><? echo $val[csf('buyer_job_no')]; ?></td>
			                        <td width="100"><? echo $val[csf('job_no')]; ?></td>
			                        <td width="100"><? echo $val[csf('int_ref')]; ?></td>
			                        <td width="100"><? echo $val[csf('style_ref_no')]; ?></td>
			                        <td width="80" align="right">
			                        	<a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$po_id.'_'.$within_group.'_'.$date_from.'_'.$date_to;?>',0,'Order Qnty Popup','order_popup');">

			                        		<? if ($val[csf('order_uom')]==2) {
			                        			echo number_format(($val[csf('order_qty')]),0);
			                        		}else{
			                        			echo number_format($val[csf('order_qty')]/12,0);
			                        		}

			                        		 ?>	
			                        	</a>	                        		
			                        </td>
			                        <td width="80" align="right"><? echo number_format($val[csf('order_amt')],2); ?></td>
			                        <td width="60" align="center"><? echo $unit_of_measurement[$val[csf('order_uom')]];?></td>
			                        <td width="80" align="right">
			                        	<a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_id.'_'.$within_group.'_'.$date_from.'_'.$date_to;?>',1,'Material Receive Popup','material_popup');">
			                        		<? echo number_format($mat_rcv_qty,0); ?>
			                        	</a>		                        		
			                        </td>
			                        <td width="80" align="right">
			                        	<a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_id.'_'.$within_group.'_'.$date_from.'_'.$date_to;?>',2,'Material Issue Popup','material_popup');">
			                        		<? echo number_format($mat_issue_qty,0); ?>
			                        	</a>		                        		
			                        </td>
			                        <td width="80" align="right">
			                        	<a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$po_id.'_'.$within_group.'_'.$date_from.'_'.$date_to;?>',1,'Production Qnty Popup','production_popup');">
			                        	<? echo number_format($prod_qty,0); ?>
			                        	</a>		                        		
			                        </td>
			                        <td width="80" align="right">
			                        	<a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$po_id.'_'.$within_group.'_'.$date_from.'_'.$date_to;?>',2,'QC Pass Qnty Popup','production_popup');">
			                        		<? echo number_format($qc_qty,0); ?>
			                        	</a>		                        		
			                        </td>
			                        <td width="80" align="right">
			                        	<a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_id.'_'.$within_group.'_'.$date_from.'_'.$date_to;?>',0,'Delivery Popup','delivery_popup');">
			                        		<? echo number_format($delivery_qty,0); ?>
			                        	</a>		                        		
			                        </td>
			                        <td width="80" align="right"><? echo number_format($rej_qty,0); ?></td>
			                        <td width="80" align="right">
			                        	<a href="javascript:void()" onClick="open_popup('<? echo $job_no.'_'.$order_id.'_'.$within_group.'_'.$date_from.'_'.$date_to.'_'.$cbo_company_id;?>',0,'Bill Qnty Popup','bill_popup');">
			                        		<? echo number_format($bill_qty,0); ?>
			                        	</a>		                        		
			                        </td>
			                        <td width="80" align="right"><? echo number_format($bill_amount,2); ?></td>                        
			                    </tr>
			                    <?
			                    $i++;
			                    $tot_job_qty 	+= $val[csf('order_qty')];
								$tot_job_val 	+= $val[csf('order_amt')];
								$tot_mat_rcv 	+= $mat_rcv_qty;
								$tot_mat_issue 	+= $mat_issue_qty;
								$tot_prod_qty 	+= $prod_qty;
								$tot_qc_qty 	+= $qc_qty;
								$tot_del_qty 	+= $delivery_qty;
								$tot_rej_qty 	+= $rej_qty;
								$tot_bill_qty 	+= $bill_qty;
								$tot_bill_amt 	+= $bill_amount;
							}
	                	}
	                    ?>
					</tbody>
                </table>
                </div>                
                <table width="1515" border="1" class="rpt_table" rules="all" id="report_table_footer" cellpadding="0" cellspacing="0">
                    <tfoot>	                    
	                    <tr>
	                        <th width="35"></th>
	                        <th width="100"></th>
	                        
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th id="tot_job_qty" width="80"><? echo number_format($tot_job_qty,0) ?></th>
	                        <th id="tot_job_val" width="80"><? echo number_format($tot_job_val,2) ?></th>
	                        <th width="60"></th>
	                        <th id="tot_mat_rcv" width="80"><? echo number_format($tot_mat_rcv,0); ?></th>
	                        <th id="tot_mat_issue" width="80"><? echo number_format($tot_mat_issue,0); ?></th>
	                        <th id="tot_prod_qty" width="80"><? echo number_format($tot_prod_qty,0); ?></th>
	                        <th id="tot_qc_qty" width="80"><? echo number_format($tot_qc_qty,0); ?></th>
	                        <th id="tot_del_qty" width="80"><? echo number_format($tot_del_qty,0); ?></th>
	                        <th id="tot_rej_qty" width="80"><? echo number_format($tot_rej_qty,0); ?></th>
	                        <th id="tot_bill_qty" width="80"><? echo number_format($tot_bill_qty,0); ?></th>
	                        <th id="tot_bill_amt" width="80"><? echo number_format($tot_bill_amt,2); ?></th>                        
	                    </tr>
                    </tfoot>
                </table>
	           </div>     	
	        </div><!-- end main div -->
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

if($action=="order_popup")
{
	echo load_html_head_contents("Order Details","../../../", 1, 1, $unicode);
    $ex_data 	= explode("_", $data);
    $job_no 	= $ex_data[0];
    $order_id 	= $ex_data[1];
    $within_group= $ex_data[2];
    $date_from 	= $ex_data[3];
    $date_to 	= $ex_data[4];
    // $date_cond	= "";
    // if($date_from !="" && $date_to !="")
    // {
    // 	$date_cond = "";
    // }
    /*$order_cond = "";
    if ($within_group==1) 
    {
    	$order_cond = " and a.order_id=$order_id and b.order_id=$order_id";
    }*/

	$color_name_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
    	
	$sql="SELECT c.color_id,c.size_id,d.grouping,sum(c.qnty*12) as order_qty
  	from subcon_ord_mst a, subcon_ord_breakdown c ,subcon_ord_dtls b
	left join wo_po_break_down d on b.buyer_po_id=d.id and d.is_deleted=0 and d.status_active=1
	where b.job_no_mst=a.embellishment_job and b.id=c.mst_id and a.entry_form=311 and a.embellishment_job='$job_no' $order_cond and a.within_group=$within_group and  b.id in($order_id) and c.qnty>0
	group by c.color_id,c.size_id,d.grouping";		
	// echo $sql;
	$sql_sel=sql_select($sql);
     
    $color_size_wise_qnty=array();
    $size_all_arr=array();
    $data_array = array();

    foreach($sql_sel as $val)
    {
        $color_size_wise_qnty[$val[csf("color_id")]][$val[csf("size_id")]]["order_quantity"]+=$val[csf("order_qty")];
        $size_all_arr[$val[csf("size_id")]]=$val[csf("size_id")];
        $data_array[$val[csf("grouping")]][$val[csf("color_id")]] = $val[csf("color_id")];
    }
    // echo "<pre>";
    // print_r($sample_data);
    // echo "</pre>";
    $table_width = 290+(count($size_all_arr)*50);
	?>    
    <div id="data_panel" align="center" style="width:100%">
       	<fieldset style="width: 98%">
	    <legend> Order Quantity</legend>
	     	<div style="width:<? echo $table_width;?>px; margin-top:10px">
		        <table cellspacing="0" width="<? echo $table_width;?>" class="rpt_table" cellpadding="0" border="1" rules="all">
		            <thead>
		                <th width="30" >SL</th>
		                <th width="100" >Int. Ref.</th>
		                <th width="100" >Color Name</th>
		                <?
		                    foreach($size_all_arr as $key=>$val)
		                    {
		                        ?>
		                        <th width="50"><? echo $size_arr[$key] ;?></th>
		                        <?
		                    }
		                ?>
		                <th width="60">Total Qty</th>
		            </thead>  
		            <tbody>
		        
		            <?
		            $i=1;
		            $gr_size_total=array();
		            foreach ($data_array as $grouping => $color_data) 
		            {
		                foreach ($color_data as $color_id => $row) 
		                {    
		                    $total_sizeqnty=0;            
		                    ?>                         
		                    <tr>
		                        <td width="30" align="center"><? echo $i; ?></td>
		                        <td width="100" align="center"><? echo  $grouping; ?></td>
		                        <td width="100" align="center"><? echo  $color_arr[$color_id]; ?></td>                        
		                        <?
		                            foreach($size_all_arr as $key=>$val)
		                            {
		                                ?>
		                                <td width="50" align="right"><? echo number_format($color_size_wise_qnty[$color_id][$key]['order_quantity'],0) ;?></td>
		                                <?
		                                $total_sizeqnty += $color_size_wise_qnty[$color_id][$key]['order_quantity'];
		                                $gr_size_total[$key] += $color_size_wise_qnty[$color_id][$key]['order_quantity'];
		                            }
		                        ?>
		                        <td width="60" align="right"><? echo  number_format($total_sizeqnty,0); ?></td>
		                    </tr>
		         
		                     <?
		                     $i++;
		                 }
		             }
		         ?>
		         </tbody>
		         <tfoot>
		                <tr>
		                    <th colspan="3" ></td>
		                    <?
		                    $total_qnty = 0;
		                        foreach($size_all_arr as $size_key=>$val)
		                        {
		                            ?>
		                            <th  align="right"><? echo number_format($gr_size_total[$size_key],0) ;?></th>
		                            <?
		                            $total_qnty += $gr_size_total[$size_key];
		                        }
		                    ?>
		                    <th align="right">  <? echo number_format($total_qnty,0); ?></td>
		                </tr>
		            </tfoot>        
		        </table>
	     	</div>
	    </fieldset>
    </div> 
    <?
    exit(); 
}

if($action=="material_popup")
{
	echo load_html_head_contents("Material Details","../../../", 1, 1, $unicode);
	extract($_REQUEST);
    $ex_data 	= explode("_", $data);
    $job_no 	= $ex_data[0];
    $order_id 	= $ex_data[1];
    $within_group= $ex_data[2];
    $date_from 	= $ex_data[3];
    $date_to 	= $ex_data[4];
    // $type 		= $ex_data[4];
    // $date_cond	= "";
    if($date_from !="" && $date_to !="")
    {
    	$date_cond = " and a.subcon_date between '$date_from' and '$date_to' ";
    }
    
	$color_name_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
    
	$entry_form_cond = "";
	if($type==1){$entry_form_cond=" and a.entry_form=312";}else{$entry_form_cond=" and a.entry_form=313";}
	$order_cond = "";
    if ($within_group==1) 
    {
    	$order_cond = " and b.order_id=$order_id";
    }

	$sql_tran="SELECT a.subcon_date,a.chalan_no,a.prefix_no_num,b.job_dtls_id, b.job_break_id,c.color_id,c.size_id, sum(b.quantity) as qty
    from sub_material_mst a,sub_material_dtls b, subcon_ord_breakdown c where a.id=b.mst_id and b.job_break_id=c.id and a.embl_job_no='$job_no' $entry_form_cond $date_cond and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1
    group by a.subcon_date,a.chalan_no,a.prefix_no_num,a.embl_job_no,b.job_dtls_id, b.job_break_id,c.color_id,c.size_id order by c.size_id";		
	// echo $sql_tran;
	$sql_tran_res=sql_select($sql_tran);
	$transection_data_array = array();
	$size_all_arr=array();
	foreach ($sql_tran_res as $val) 
	{
		$transection_data_array[$val[csf('subcon_date')]][$val[csf("color_id")]]['date'] 		= $val[csf('subcon_date')];
		$transection_data_array[$val[csf('subcon_date')]][$val[csf("color_id")]]['chalan'] 	= $val[csf('chalan_no')];
		$transection_data_array[$val[csf('subcon_date')]][$val[csf("color_id")]]['trans_id']= $val[csf('prefix_no_num')];
		$size_all_arr[$val[csf("size_id")]]=$val[csf("size_id")];
		$color_size_wise_qnty[$val[csf('subcon_date')]][$val[csf("color_id")]][$val[csf("size_id")]]["qty"]+=$val[csf("qty")];
	}
	
    $table_width = 490+(count($size_all_arr)*50);
	?>    
    <div id="data_panel" align="center" style="width:100%">
       	<fieldset style="width: 98%">
	    <legend> Material <? if($type==1) echo "Receive"; else echo "Issue";?> Quantity</legend>
	     	<div style="width:<? echo $table_width;?>px; margin-top:10px">
		        <table cellspacing="0" width="<? echo $table_width;?>" class="rpt_table" cellpadding="0" border="1" rules="all">
		            <thead>
		                <th width="30" >SL</th>
		                <th width="100" >Tran. ID</th>
		                <th width="100" >Chalan No</th>
		                <th width="100" >Date</th>
		                <th width="100" >Color Name</th>
		                <?
		                    foreach($size_all_arr as $key=>$val)
		                    {
		                        ?>
		                        <th width="50"><? echo $size_arr[$key] ;?></th>
		                        <?
		                    }
		                ?>
		                <th width="60">Total Qty</th>
		            </thead>  
		            <tbody>
		        
		            <?
		            $i=1;
		            $gr_size_total=array();
		            foreach ($transection_data_array as $tran_date => $date_data) 
		            {				            	                    
		                foreach ($date_data as $color_id => $row) 
		            	{
		                    $total_sizeqnty=0;        
		                    ?>                         
		                    <tr>
		                        <td width="30" align="center"><? echo $i; ?></td>
		                        <td width="100" align="center"><? echo  $row['trans_id']; ?></td>
		                        <td width="100" align="center"><? echo  $row['chalan']; ?></td>
		                        <td width="100" align="center"><? echo  change_date_format($tran_date); ?></td>
		                        <td width="100" align="center"><? echo  $color_arr[$color_id]; ?></td>                        
		                        <?
		                            foreach($size_all_arr as $key=>$val)
		                            {
		                                ?>
		                                <td width="50" align="right"><? echo number_format($color_size_wise_qnty[$tran_date][$color_id][$key]['qty'],0) ;?></td>
		                                <?
		                                $total_sizeqnty += $color_size_wise_qnty[$tran_date][$color_id][$key]['qty'];
		                                $gr_size_total[$key] += $color_size_wise_qnty[$tran_date][$color_id][$key]['qty'];
		                            }
		                        ?>
		                        <td width="60" align="right"><? echo  number_format($total_sizeqnty,0); ?></td>
		                    </tr>
		         
		                    <?
		                    $i++;	
		                }		                                
		            }
		         ?>
		         </tbody>
		         <tfoot>
		                <tr>
		                    <th colspan="5" ></td>
		                    <?
		                    $total_qnty = 0;
		                        foreach($size_all_arr as $size_key=>$val)
		                        {
		                            ?>
		                            <th  align="right"><? echo number_format($gr_size_total[$size_key],0) ;?></th>
		                            <?
		                            $total_qnty += $gr_size_total[$size_key];
		                        }
		                    ?>
		                    <th align="right">  <? echo number_format($total_qnty,0); ?></td>
		                </tr>
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
    // $date_cond	= "";
    if($date_from !="" && $date_to !="")
    {
    	$date_cond = " and b.production_date between '$date_from' and '$date_to' ";
    }
    
	$color_name_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
    
	$entry_form_cond = "";
	if($type==1){$entry_form_cond=" and a.entry_form=315";}else{$entry_form_cond=" and a.entry_form=324";}
	$order_cond = "";
    /*if ($within_group==1) 
    {
    	$order_cond = " and b.order_id=$order_id";
    }*/

	$sql_tran="SELECT b.production_date,a.prefix_no_num,a.order_id, c.color_id,c.size_id,sum(b.qcpass_qty) as qty
    from subcon_embel_production_mst a,subcon_embel_production_dtls b, subcon_ord_breakdown c  where a.id=b.mst_id and b.color_size_id=c.id and a.job_no='$job_no' $entry_form_cond $date_cond and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 
    group by b.production_date,a.prefix_no_num,a.order_id, c.color_id,c.size_id";		
	 //echo $sql_tran;die();
	$sql_tran_res=sql_select($sql_tran);
	$transection_data_array = array();
	
	$order_id_array = array();
	$break_down_id_array = array();
	$prod_qty_array = array();
	foreach ($sql_tran_res as $val) 
	{
		$transection_data_array[$val[csf('production_date')]][$val[csf("order_id")]][$val[csf("color_id")]]['date'] 		= $val[csf('production_date')];
		// $transection_data_array[$val[csf('production_date')]][$val[csf("order_id")]][$val[csf("color_id")]]['chalan'] 	= $val[csf('chalan_no')];
		$transection_data_array[$val[csf('production_date')]][$val[csf("order_id")]][$val[csf("color_id")]]['trans_id']	= $val[csf('prefix_no_num')];
		$transection_data_array[$val[csf('production_date')]][$val[csf("order_id")]][$val[csf("color_id")]]['qty'] 		+= $val[csf('qty')];
		$prod_qty_array[$val[csf('production_date')]][$val[csf("order_id")]][$val[csf("color_id")]][$val[csf("size_id")]]	+= $val[csf('qty')];
		
		$transection_data_array[$val[csf('production_date')]][$val[csf("order_id")]][$val[csf("color_id")]]['size_id'] = $val[csf('size_id')];
		
		$order_id_array[$val[csf("order_id")]] = $val[csf("order_id")];
		$break_down_id_array[$val[csf("color_id")]] = $val[csf("color_id")];
	}
	// echo "<pre>";
 	// print_r($prod_qty_array);
	// print_r($transection_data_array);
	// =============================== getting recipe ====================================
		
	$ordeIds 		= implode(",", $order_id_array);	
	$breakDownId 	= implode(",", $break_down_id_array);	
	//$recipe_array 	= return_library_array( "SELECT id, recipe_no from pro_recipe_entry_mst where id in($recipeIds) and entry_form=220 and status_active =1 and is_deleted=0","id","recipe_no");
	// ============================= getting internal ref. ===============================
	if($within_group==1)
	{
		$int_ref_arr=return_library_array( "SELECT b.order_id, a.grouping from wo_po_break_down a,subcon_ord_dtls b where a.id=b.buyer_po_id and b.order_id in($order_id) and a.status_active=1 and a.is_deleted=0","order_id","grouping");
	}


	// ============================= GET COLOR AND SIZE WISE QTY ========================================
	$sql="SELECT b.id as dtls_id,c.id as break_id,c.color_id,c.size_id
  	from subcon_ord_mst a, subcon_ord_breakdown c ,subcon_ord_dtls b
	where b.job_no_mst=a.embellishment_job and b.id=c.mst_id and a.entry_form=311 and a.embellishment_job='$job_no' $order_cond and a.within_group=$within_group and b.id in($order_id) and c.color_id in($breakDownId)
	group by b.id,c.id,c.color_id,c.size_id order by c.size_id";
	// echo $sql; die;
	$sql_sel=sql_select($sql);     
    
    $size_all_arr=array();
    $color_size_all_arr=array();
    $data_array = array();

    foreach($sql_sel as $val)
    {
        $size_all_arr[$val[csf("size_id")]] 			= $val[csf("size_id")];
        $color_size_all_arr[$val[csf("color_id")]]		= $val[csf("size_id")];
        $data_array[$val[csf("color_id")]]['dtls_id'] 	= $val[csf("dtls_id")];
        $data_array[$val[csf("color_id")]]['color_id'] 	= $val[csf("color_id")];
        // $data_array[$val[csf("color_id")]]['color_id'] 	= $val[csf("color_id")];
        
    }
    // echo "<pre>";
    // print_r($data_array);
    // echo "</pre>";
    $table_width = 590+(count($size_all_arr)*50);
	?>    
    <div id="data_panel" align="center" style="width:100%">
       	<fieldset style="width: 98%">
	    <legend> Order Quantity</legend>
	     	<div style="width:<? echo $table_width;?>px; margin-top:10px">
		        <table cellspacing="0" width="<? echo $table_width;?>" class="rpt_table" cellpadding="0" border="1" rules="all">
		            <thead>
		                <th width="30" >SL</th>
		                <th width="100" >Tran. ID</th>
		                <th width="100" >Chalan No</th>
		                <th width="100" >Date</th>
		                <th width="100" >Int. Ref.</th>
		                
		                <th width="100" >Color Name</th>
		                <?
		                    foreach($size_all_arr as $key=>$val)
		                    {
		                        ?>
		                        <th width="50"><? echo $size_arr[$key] ;?></th>
		                        <?
		                    }
		                ?>
		                <th width="60">Total Qty</th>
		            </thead>  
		            <tbody>
		        
		            <?
		            $i=1;
		            $gr_size_total=array();
		            // echo "<pre>";print_r($size_all_arr);die();
		            foreach ($transection_data_array as $date => $date_data) 
		            {
			            foreach ($date_data as $orderId => $order_data) 
			            {
				            foreach ($order_data as $color_id => $row) 
				            {		                    
			                    ?>                         
			                    <tr>
			                        <td width="30" align="center"><? echo $i; ?></td>
			                        <td width="100" align="center"><? echo  $row['trans_id']; ?></td>
			                        <td width="100" align="center"><? echo  $chalan; ?></td>
			                        <td width="100" align="center"><? echo  change_date_format($date); ?></td>
			                        <td width="100" align="center"><? echo  $int_ref_arr[$order_id]; ?></td>
			                        
			                        <td width="100" align="center"><? echo  $color_arr[$data_array[$color_id]['color_id']]; ?></td>                        
			                        <?
			                        	$total_sizeqnty=0;
			                            foreach($size_all_arr as $key=>$val)
			                            {
			                                ?>
			                                <td width="50" align="right"><? 
			                                // echo $date."==".$orderId."==".$color_id."<br>";
			                                //if($val==$row['size_id']){
			                                	echo $prod_qty_array[$date][$orderId][$color_id][$key];
			                                	$total_sizeqnty += $prod_qty_array[$date][$orderId][$color_id][$key];
			                                	$gr_size_total[$key] += $prod_qty_array[$date][$orderId][$color_id][$key];
			                               // }
			                               // else{
			                                	//echo '' ;
			                                //} 
			                                ?></td>
			                                <?
			                            }
			                        ?>
			                        <td width="60" align="right"><? echo  $total_sizeqnty; ?></td>
			                    </tr>
			                    <?
			                    $i++;	
			                }	
		                }                
		            }
		        ?>
		         </tbody>
		         <tfoot>
		                <tr>
		                    <th colspan="6" ></td>
		                    <?
		                    $total_qnty = 0;
		                        foreach($size_all_arr as $size_key=>$val)
		                        {
		                            ?>
		                            <th  align="right"><? echo $gr_size_total[$size_key] ;?></th>
		                            <?
		                            $total_qnty += $gr_size_total[$size_key];
		                        }
		                    ?>
		                    <th align="right">  <? echo $total_qnty; ?></td>
		                </tr>
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

    $date_cond	= "";
    if($date_from !="" && $date_to !="")
    {
    	$date_cond = " and a.delivery_date between '$date_from' and '$date_to' ";
    }
    
	$color_name_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
    
	
	$order_cond = "";
    /*if ($within_group==1) 
    {
    	$order_cond = " and b.order_id=$order_id";
    }*/

    $sql_tran="SELECT a.delivery_date,a.challan_no,a.delivery_prefix_num,b.order_id,c.color_id,c.size_id, sum(b.delivery_qty) as delivery_qty 
    from subcon_delivery_mst a,subcon_delivery_dtls b , subcon_ord_breakdown c  
    where a.id=b.mst_id and b.color_size_id=c.id  and a.entry_form=325 and a.job_no='$job_no' $date_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.delivery_date,a.challan_no,a.delivery_prefix_num,b.order_id,c.color_id,c.size_id"; 	
	// echo $sql_tran;die();
	$sql_tran_res=sql_select($sql_tran);
	$transection_data_array = array();
	//$recipe_id_array = array();
	$order_id_array = array();
	$break_down_id_array = array();
	$delv_qty_array = array();
	foreach ($sql_tran_res as $val) 
	{
		$transection_data_array[$val[csf('delivery_date')]][$val[csf("order_id")]][$val[csf("color_id")]]['date'] 		= $val[csf('delivery_date')];
		$transection_data_array[$val[csf('delivery_date')]][$val[csf("order_id")]][$val[csf("color_id")]]['size_id'] 		= $val[csf('size_id')];
		// $transection_data_array[$val[csf('delivery_date')]][$val[csf("order_id")]][$val[csf("color_id")]]['chalan'] 	= $val[csf('chalan_no')];
		$transection_data_array[$val[csf('delivery_date')]][$val[csf("order_id")]][$val[csf("color_id")]]['trans_id']	= $val[csf('delivery_prefix_num')];
		$transection_data_array[$val[csf('delivery_date')]][$val[csf("order_id")]][$val[csf("color_id")]]['qty'] 		= $val[csf('delivery_qty')];
		$delv_qty_array[$val[csf('delivery_date')]][$val[csf("order_id")]][$val[csf("color_id")]][$val[csf("size_id")]] 	+= $val[csf('delivery_qty')];
		$order_id_array[$val[csf("order_id")]] = $val[csf("order_id")];
		$break_down_id_array[$val[csf("color_id")]] = $val[csf("color_id")];
	}
	// print_r($transection_data_array);	
	$ordeIds 		= implode(",", $order_id_array);	
	$breakDownId 	= implode(",", $break_down_id_array);	
	
	// ============================= getting internal ref. ===============================
	if($within_group==1)
	{
		$int_ref_arr=return_library_array( "select b.order_id, a.grouping from wo_po_break_down a,subcon_ord_dtls b where a.id=b.buyer_po_id and b.order_id in($order_id) and a.status_active=1 and a.is_deleted=0","order_id","grouping");
	}


	// ============================= GET COLOR AND SIZE WISE QTY ========================================
	$sql="SELECT b.id as dtls_id,c.id as break_id,c.color_id,c.size_id
  	from subcon_ord_mst a, subcon_ord_breakdown c ,subcon_ord_dtls b
	where b.job_no_mst=a.embellishment_job and b.id=c.mst_id and a.entry_form=311 and a.embellishment_job='$job_no' $order_cond and a.within_group=$within_group and b.id in($ordeIds) and c.color_id in($breakDownId)
	group by b.id,c.id,c.color_id,c.size_id order by c.size_id";
	// echo $sql;
	$sql_sel=sql_select($sql);     
    
    $size_all_arr=array();
    $color_size_all_arr=array();
    $data_array = array();

    foreach($sql_sel as $val)
    {
        $size_all_arr[$val[csf("size_id")]] 			= $val[csf("size_id")];
        $color_size_all_arr[$val[csf("break_id")]] 		= $val[csf("size_id")];
        $data_array[$val[csf("color_id")]] 	= $val[csf("color_id")];
    }
    // echo "<pre>";
    // print_r($sample_data);
    // echo "</pre>";
    $table_width = 590+(count($size_all_arr)*50);
	?>    
    <div id="data_panel" align="center" style="width:100%">
       	<fieldset style="width: 98%">
	    <legend> Order Quantity</legend>
	     	<div style="width:<? echo $table_width;?>px; margin-top:10px">
		        <table cellspacing="0" width="<? echo $table_width;?>" class="rpt_table" cellpadding="0" border="1" rules="all">
		            <thead>
		                <th width="30" >SL</th>
		                <th width="100" >Tran. ID</th>
		                <th width="100" >Chalan No</th>
		                <th width="100" >Date</th>
		                <th width="100" >Int. Ref.</th>
		                
		                <th width="100" >Color Name</th>
		                <?
		                    foreach($size_all_arr as $key=>$val)
		                    {
		                        ?>
		                        <th width="50"><? echo $size_arr[$key] ;?></th>
		                        <?
		                    }
		                ?>
		                <th width="60">Total Qty</th>
		            </thead>  
		            <tbody>
		        
		            <?
		            $i=1;
		            $gr_size_total=array();
		            foreach ($transection_data_array as $del_date => $date_data) 
		            {
			            foreach ($date_data as $orderId => $order_data) 
			            {
				            foreach ($order_data as $color_id => $row) 
				            {		                    
			                    $total_sizeqnty=0;   
			                    // $chalan = $transection_data_array[$row['dtls_id']][$row['color_id']]['chalan'];       
			                    $date 	= $transection_data_array[$row['dtls_id']][$row['color_id']]['date'];       
			                    $trans_id= $transection_data_array[$row['dtls_id']][$row['color_id']]['trans_id'];       
			                    //$recipeId= $transection_data_array[$row['dtls_id']][$row['color_id']]['recipe_id'];       
			                    ?>                         
			                    <tr>
			                        <td width="30" align="center"><? echo $i; ?></td>
			                        <td width="100" align="center"><? echo  $row['trans_id']; ?></td>
			                        <td width="100" align="center"><? echo  $chalan; ?></td>
			                        <td width="100" align="center"><? echo  change_date_format($del_date); ?></td>
			                        <td width="100" align="center"><? echo  $int_ref_arr[$order_id]; ?></td>
			                        
			                        <td width="100" align="center"><? echo  $color_arr[$color_id]; ?></td>                        
			                        <?
			                            $total_sizeqnty=0;
			                            foreach($size_all_arr as $key=>$val)
			                            {
			                                ?>
			                                <td width="50" align="right"><? 
			                                //if($val==$row['size_id']){
			                                	echo $delv_qty_array[$del_date][$orderId][$color_id][$key] ;
			                                	$total_sizeqnty += $delv_qty_array[$del_date][$orderId][$color_id][$key];
			                                	$gr_size_total[$key] += $delv_qty_array[$del_date][$orderId][$color_id][$key];
			                               // }
			                                //else{
			                                	//echo '' ;
			                               // } 
			                                ?></td>
			                                <?
			                            }
			                        ?>
			                        <td width="60" align="right"><? echo  $total_sizeqnty; ?></td>
			                    </tr>
			         
			                    <?
			                    $i++;	
		                    }
	                    }	                
		            }
		         ?>
		         </tbody>
		         <tfoot>
		                <tr>
		                    <th colspan="6" ></td>
		                    <?
		                    $total_qnty = 0;
		                        foreach($size_all_arr as $size_key=>$val)
		                        {
		                            ?>
		                            <th  align="right"><? echo $gr_size_total[$size_key] ;?></th>
		                            <?
		                            $total_qnty += $gr_size_total[$size_key];
		                        }
		                    ?>
		                    <th align="right">  <? echo $total_qnty; ?></td>
		                </tr>
		            </tfoot>        
		        </table>
	     	</div>
	    </fieldset>
    </div> 
    <?
    exit(); 
}

if($action=="bill_popup")
{
	echo load_html_head_contents("Bill Details","../../../", 1, 1, $unicode);
	extract($_REQUEST);
    $ex_data 	= explode("_", $data);
    $job_no 	= $ex_data[0];
    $order_id 	= $ex_data[1];
    $within_group= $ex_data[2];
    $date_from 	= $ex_data[3];
    $date_to 	= $ex_data[4];
    $cbo_company_id 	= $ex_data[5];

    if(str_replace("'","",$cbo_company_id)==0)$company_name=""; else $company_name=" and a.company_id=$cbo_company_id";
    $date_cond	= "";
    if($date_from !="" && $date_to !="")
    {
    	$date_cond = " and a.bill_date between '$date_from' and '$date_to' ";
    }	

	$sql_tran="SELECT a.bill_no,a.bill_date,b.order_id,b.color_size_id,b.rate,b.amount,b.remarks, d.size_id, sum(b.delivery_qty) as tbill_qty, sum(b.amount) as total_bill_amount,b.process_id from subcon_inbound_bill_mst a,subcon_inbound_bill_dtls b, subcon_ord_dtls c, subcon_ord_breakdown d where c.job_no_mst='$job_no' $company_name and a.id=b.mst_id and c.id=b.order_id and d.mst_id=c.id and b.color_size_id=d.id $date_cond and b.process_id=14 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by a.bill_no,a.bill_date,b.order_id,b.color_size_id,b.rate,b.amount,b.remarks,b.process_id,d.size_id";
	// echo $sql_tran;die();
	$sql_tran_res=sql_select($sql_tran);
	$transection_data_array = array();
	//$recipe_id_array = array();
	$order_id_array = array();
	$break_down_id_array = array();
    $rowspan_arr = array();
	foreach ($sql_tran_res as $val) 
	{
		$transection_data_array[$val[csf("order_id")]][$val[csf("color_size_id")]][$val[csf("size_id")]]['date'] 		= $val[csf('bill_date')];
		$transection_data_array[$val[csf("order_id")]][$val[csf("color_size_id")]][$val[csf("size_id")]]['rate'] 		= $val[csf('rate')];
		$transection_data_array[$val[csf("order_id")]][$val[csf("color_size_id")]][$val[csf("size_id")]]['amount'] 	+= $val[csf('amount')];
		$transection_data_array[$val[csf("order_id")]][$val[csf("color_size_id")]][$val[csf("size_id")]]['bill_no']	= $val[csf('bill_no')];
		$transection_data_array[$val[csf("order_id")]][$val[csf("color_size_id")]][$val[csf("size_id")]]['qty'] 		+= $val[csf('tbill_qty')];
		$transection_data_array[$val[csf("order_id")]][$val[csf("color_size_id")]][$val[csf("size_id")]]['remarks'] 	= $val[csf('remarks')];
		$transection_data_array[$val[csf("order_id")]][$val[csf("color_size_id")]][$val[csf("size_id")]]['process_id']= $val[csf('process_id')];
		$order_id_array[$val[csf("order_id")]] = $val[csf("order_id")];
		$break_down_id_array[$val[csf("color_size_id")]] = $val[csf("color_size_id")];
		$rowspan_arr[$val[csf("bill_no")]]++;
	}
	// print_r($rowspan_arr);
	$ordeIds 		= implode(",", $order_id_array);	
	$breakDownId 	= implode(",", $break_down_id_array);
	
	// ============================= getting internal ref. ===============================
	if($within_group==1)
	{
		$int_ref_arr=return_library_array( "SELECT b.order_id, a.grouping from wo_po_break_down a,subcon_ord_dtls b where a.id=b.buyer_po_id and b.order_id in($order_id) and a.status_active=1 and a.is_deleted=0","order_id","grouping");
	}


	// ============================= GET COLOR AND SIZE WISE QTY ========================================
	$sql="SELECT b.id as dtls_id,c.id as break_id,c.color_id,c.size_id,b.gmts_item_id as item_id,b.body_part,b.embl_type,b.main_process_id
  	from subcon_ord_mst a, subcon_ord_breakdown c ,subcon_ord_dtls b
	where b.job_no_mst=a.embellishment_job and b.id=c.mst_id  and a.id=b.mst_id and a.entry_form=311 and a.embellishment_job='$job_no' $order_cond and a.within_group=$within_group and b.id in($ordeIds) and c.id in($breakDownId)
	group by b.id,c.id,c.color_id,c.size_id,b.gmts_item_id,b.body_part,b.embl_type,b.main_process_id order by c.size_id";
	// echo $sql;die();
	$sql_sel=sql_select($sql);     
    
    $size_all_arr=array();
    $color_size_all_arr=array();
    $data_array = array();
    foreach($sql_sel as $val)
    {
        $size_all_arr[$val[csf("size_id")]] 			= $val[csf("size_id")];
        $color_size_all_arr[$val[csf("break_id")]] 		= $val[csf("break_id")];

        if($val[csf("main_process_id")]==1) $emb_type=$emblishment_print_type;
		else if($val[csf("main_process_id")]==2) $emb_type=$emblishment_embroy_type;
		else if($val[csf("main_process_id")]==3) $emb_type=$emblishment_wash_type;
		else if($val[csf("main_process_id")]==4) $emb_type=$emblishment_spwork_type;
		else if($val[csf("main_process_id")]==5) $emb_type=$emblishment_gmts_type;
		else $emb_type="";

        $data_array[$val[csf("color_id")]][$val[csf("break_id")]][$val[csf("size_id")]]['dtls_id'] 		= $val[csf("dtls_id")];
        $data_array[$val[csf("color_id")]][$val[csf("break_id")]][$val[csf("size_id")]]['item_id'] 		= $val[csf("item_id")];
        $data_array[$val[csf("color_id")]][$val[csf("break_id")]][$val[csf("size_id")]]['body_part'] 		= $val[csf("body_part")];
        $data_array[$val[csf("color_id")]][$val[csf("break_id")]][$val[csf("size_id")]]['embl_type'] 		= $val[csf("embl_type")];
        $data_array[$val[csf("color_id")]][$val[csf("break_id")]][$val[csf("size_id")]]['size_id'] 		= $val[csf("size_id")];
        $data_array[$val[csf("color_id")]][$val[csf("break_id")]][$val[csf("size_id")]]['main_process_id']= $val[csf("main_process_id")];
    }
    // echo "<pre>";
    //print_r($data_array);
    // echo "</pre>";
    // $table_width = 690+(count($size_all_arr)*50);
	?>    
    <div id="data_panel" align="center" style="width:100%">
       	<fieldset style="width: 98%">
	    <legend> Order Quantity</legend>
	     	<div style="width:1130px; margin-top:10px">
		        <table cellspacing="0" width="1130" class="rpt_table" cellpadding="0" border="1" rules="all">
		            <thead>
		                <th width="30">SL</th>
		                <th width="100">Bill No</th>
		                <th width="60">Date</th>
		                <th width="100">Int. Ref.</th>
		                <th width="100">Gmts Item</th>
		                <th width="100">Body Part</th>
		                <th width="100">Embel. Name</th>
		                <th width="100">Process/Type</th>
		                <th width="100">Color</th>
		                <th width="60">Size</th>
		                <th width="60">Bill Qty</th>
		                <th width="60">Rate</th>
		                <th width="60">Amount</th>
		                <th width="100">Remarks</th>
		            </thead>  
		            <tbody>
		            <?
		            $i=1;
		            $gr_total_qty = 0;
		            $gr_total_amt = 0;
		            $r=0;
		            foreach ($data_array as $color_id => $color_data) 
		            {
			            foreach ($color_data as $break_down_id => $break_data) 
			            {
				            foreach ($break_data as $size_id => $row) 
				            {	  
			                    $date 	= $transection_data_array[$row['dtls_id']][$break_down_id][$size_id]['date'];        
			                    $bill_no= $transection_data_array[$row['dtls_id']][$break_down_id][$size_id]['bill_no'];       
			                    $rate 	= $transection_data_array[$row['dtls_id']][$break_down_id][$size_id]['rate'];       
			                    $amount = $transection_data_array[$row['dtls_id']][$break_down_id][$size_id]['amount'];       
			                    $qty 	= $transection_data_array[$row['dtls_id']][$break_down_id][$size_id]['qty'];       
			                    $remarks= $transection_data_array[$row['dtls_id']][$break_down_id][$size_id]['remarks'];   
			                    $gr_total_qty += $qty;
			            		$gr_total_amt += $amount; 
			                    ?>                         
			                    <tr>
			                        <td width="30" valign="middle" align="center"><? echo $i; ?></td>
			                        <td width="100" valign="middle" align="center"><? echo  $bill_no; ?></td>
			                        <td width="60" valign="middle" align="center"><? echo  change_date_format($date); ?></td>
			                        <td width="100" valign="middle" align="center"><? echo  $int_ref_arr[$order_id]; ?></td>
			                        <td width="100" align="center"><? echo  $garments_item[$row['item_id']]; ?></td>
			                        <td width="100" align="center"><? echo  $body_part[$row['body_part']]; ?></td>
			                        <td width="100" align="center"><? echo  $emblishment_name_array[$row['main_process_id']]; ?></td>
			                        <td width="100" align="center"><? echo  $emb_type[$row['embl_type']]; ?></td> 
			                        <td width="100" align="center"><? echo  $color_arr[$color_id]; ?></td> 
			                        <td width="60" align="right"><? echo  $size_arr[$size_id]; ?></td>
			                        <td width="60" align="right"><? echo  $qty; ?></td>
			                        <td width="60" align="right"><? echo number_format($amount/$qty,2); //$rate; ?></td>
			                        <td width="60" align="right"><? echo  $amount; ?></td>
			                        <td width="100" align="left"><? echo  $remarks; ?></td>
			                    </tr>
			                    <?
			                    $i++;	
			                    $r++;
			                }	
			            }                
		            }
		         ?>
		         </tbody>
		         <tfoot>
		                <tr>
		                    <th colspan="10" ></td>
		                    <th align="right">  <? echo $gr_total_qty; ?></td>
		                    <th align="right"></td>
		                    <th align="right">  <? echo $gr_total_amt; ?></td>
		                    <th align="right"></td>
		                </tr>
		            </tfoot>        
		        </table>
	     	</div>
	    </fieldset>
    </div> 
    <?
    exit(); 
}
?>