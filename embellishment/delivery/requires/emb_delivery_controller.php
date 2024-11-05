<?php
session_start();
include('../../../includes/common.php');

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];



if($action=="chk_qty_level_variable")
{
	
	//echo "select quantity_control,id from variable_setting_printing_prod where company_name = $data and variable_list =3 and is_deleted = 0 and status_active = 1";
	
    $sql =  sql_select("select quantity_control,id from variable_setting_printing_prod where company_name = $data and variable_list =4 and is_deleted = 0 and status_active = 1");
	$return_data="";
    if(count($sql)>0)
	{
		$return_data=$sql[0][csf('quantity_control')];
	}
	else
	{ 
		$return_data=0; 
	}
	
	echo $return_data;
	die;
}


if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	if($data[1]==1) $dropdown_name="cbo_location_name";
	else if($data[1]==2) $dropdown_name="cbo_party_location";
	else if($data[1]==3) $dropdown_name="cbo_deli_party_location";
	
	echo create_drop_down( $dropdown_name, 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "" );	
	exit();
}

if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);

	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";
	
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $data[2], "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
	}	
	exit();	 
}

if ($action=="load_drop_down_buyer_pop")
{
	//echo $data; die;
	$data=explode('_',$data);
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $selected, "");
		exit();
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$data[0], "" );
		exit();
	}
}

if ($action=="job_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	$data=explode("_",$data);
	?>
	<script>
		function js_set_value(str)
		{ 
			document.getElementById('selected_order').value=str;
			//alert(str);
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_order_popup(company,within_group,party)
		{
			//alert();
			load_drop_down( 'emb_delivery_controller', company+'_'+within_group+'_'+party, 'load_drop_down_buyer', 'buyer_td' );
			$('#cbo_party_name').attr('disabled','disabled');
		}
		
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Embl. Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer Po');
			else if(val==5) $('#search_by_td').html('Buyer Style');
		}
		
	</script>
	</head>
	<body onLoad="fnc_load_party_order_popup(<? echo $data[0];?>,<? echo $data[3];?>,<? echo $data[2];?>)">
        <div align="center" style="width:100%;" >
            <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
                <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
                        </tr>
                    	<tr>               	 
                            <th width="140">Company Name</th>
                            <th width="140">Party Name</th>
                            <th width="170">Date Range</th>
                            <th width="100">Search By</th>
                            <th width="100" id="search_by_td">Buyer Po</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td><input type="hidden" id="selected_order">  
								<?   
									echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $data[0],"",1 );
                                ?>
                            </td>
                            <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 140, $blank_array,"", 1, "-- Select Party --", $selected, "" ); ?>
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td>
								<?
									$search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style", 6=> "IR/IB");
									echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",4,'search_by(this.value)',0,"" );
								?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $data[3];?>+'_'+document.getElementById('cbo_year_selection').value, 'create_job_search_list_view', 'search_div', 'emb_delivery_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                    </tbody>
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
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size",'id','size_name');
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($within_group==1)
	{
		if($search_type==1)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
				else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
				
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
				else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
				else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";
				else if ($search_by==6) $group_cond=" and d.grouping = '$search_str' ";
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
				else if ($search_by==6) $group_cond=" and d.grouping = '$search_str' ";
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
				else if ($search_by==6) $group_cond=" and d.grouping = '$search_str' ";
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
				else if ($search_by==6) $group_cond=" and d.grouping = '$search_str' ";
			}
		}
		
	}
	
	if($within_group==2)
	{
	
		if($search_type==1)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
				else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
				else if ($search_by==4) $within_no_cond=" and b.buyer_po_no = '$search_str' ";
				else if ($search_by==5) $within_no_cond=" and b.buyer_style_ref = '$search_str' ";
			}
			
			
		}
		else if($search_type==4 || $search_type==0)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
				else if ($search_by==4) $within_no_cond=" and b.buyer_po_no like '%$search_str%'";
				else if ($search_by==5) $within_no_cond=" and b.buyer_style_ref like '$search_str%'"; 
			}
			
		}
		else if($search_type==2)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
				else if ($search_by==4) $within_no_cond=" and b.buyer_po_no like '$search_str%'";
				else if ($search_by==5) $within_no_cond=" and b.buyer_style_ref like '$search_str%'"; 
			}
			
		}
		else if($search_type==3)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
				else if ($search_by==4) $within_no_cond=" and b.buyer_po_no like '%$search_str'";
				else if ($search_by==5) $within_no_cond=" and b.buyer_style_ref like '%$search_str'";  
			}
			
		}	
		
	}
		
	
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";

		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[8]";
		$insert_year="YEAR(a.insert_date)";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";

		$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";
		$insert_year="to_char(a.insert_date,'YYYY')";
	}

	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	if(($job_cond!="" && $search_by==3)|| ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
		
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
		
		if ($po_ids=="")
		{
			$po_idsCond="";
			echo "Not Found."; die;
		}
	}
	
	if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	
	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
	}
	unset($po_sql_res);
	
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	//$arr=array (3=>$comp,6=>$yes_no,7=>$color_lib_arr);
	
	if($db_type==0)
	{
		$ins_year_cond="year(a.insert_date)";
		$color_id_str="group_concat(c.color_id)";
		$buyer_po_id_cond="year(b.buyer_po_id)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str="listagg(c.color_id,',') within group (order by c.color_id)";
		$buyer_po_id_cond="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
	}

	if($within_group==1){
		$sql= "SELECT a.id, a.embellishment_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, d.grouping, a.delivery_date, $color_id_str as color_id, $buyer_po_id_cond as buyer_po_id ,b.buyer_po_no,b.buyer_style_ref ,b.buyer_buyer
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, wo_po_break_down d
		where a.entry_form=311 and a.embellishment_job=b.job_no_mst and d.id=b.buyer_po_id and b.job_no_mst=c.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $year_cond $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $within_no_cond $group_cond  and b.id=c.mst_id  
		group by a.id, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, d.grouping, a.delivery_date,b.buyer_po_no,b.buyer_style_ref ,b.buyer_buyer
		order by a.id DESC";
	}else{
		$sql= "SELECT a.id, a.embellishment_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, $color_id_str as color_id, $buyer_po_id_cond as buyer_po_id ,b.buyer_po_no,b.buyer_style_ref ,b.buyer_buyer
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
		where a.entry_form=311 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 $year_cond $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $within_no_cond  and b.id=c.mst_id  
		group by a.id, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,b.buyer_po_no,b.buyer_style_ref ,b.buyer_buyer
		order by a.id DESC";
	}
	 

	//echo $sql;die;

	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="985" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Job No</th>
			<th width="100">IR/IB</th>
            <th width="60">Year</th>
            <th width="120">W/O No</th>
            <th width="100">Buyer Po</th>
            <th width="100">Buyer Style</th>
            <th width="80">Ord Receive Date</th>
            <th width="80">Delivery Date</th>
            <th>Color</th>
        </thead>
        </table>
        <div style="width:985px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="965" class="rpt_table" id="tbl_po_list">
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
				$buyer_po=""; $buyer_style=""; $buyer_name='';
				$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
				foreach($buyer_po_id as $po_id)
				{
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
					if($buyer_name=="") $buyer_name=$buyer_arr[$buyer_po_arr[$po_id]['buyer']]; else $buyer_name.=','.$buyer_arr[$buyer_po_arr[$po_id]['buyer']];
				}
				$buyer_po=implode(", ",array_unique(explode(",",$buyer_po)));
				$buyer_style=implode(", ",array_unique(explode(",",$buyer_style)));
				$buyer_name=implode(", ",array_unique(explode(",",$buyer_name)));
				if ($within_group==1){
					$buyer_name=$buyer_name;
				}else{
					$buyer_name=$row[csf('buyer_buyer')];
				}
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf('embellishment_job')].'***'.$row[csf('order_no')].'***'.$buyer_style.'***'.$buyer_name; ?>')" style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
					<td width="100" style="word-break:break-all"><? echo $row[csf('grouping')]; ?></td>
                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $row[csf('order_no')]; ?></td>
                    <td width="100" style="word-break:break-all"><?  if ($within_group==1)echo $buyer_po; else echo $row[csf('buyer_po_no')];//echo $buyer_po; ?></td>
                    <td width="100" style="word-break:break-all"><? if ($within_group==1)echo $buyer_style; echo $row[csf('buyer_style_ref')];//echo $buyer_style; ?></td>
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

if($action=="load_php_dtls_form")
{
	//echo $data;
	$exdata=explode("**",$data);
	$jobno=''; $update_id=0;
	$update_id=$exdata[0];
	$jobno=$exdata[1];
	$company=$exdata[3];
	$color_arrey=return_library_array( "select id,color_name from lib_color",'id','color_name');
	$size_arrey=return_library_array( "select id,size_name from  lib_size",'id','size_name');
	$buyer_po_arr=array();
	
	$variable_status=return_field_value("quantity_control","variable_setting_printing_prod","company_name='$company' and variable_list =4 and is_deleted = 0 and status_active = 1");
	
	$po_sql ="SELECT a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	$qcdtls_data_arr=array();
	
	$sql_qc="SELECT a.id, b.id as upid, b.qcpass_qty as quantity, b.po_id, b.color_size_id, a.buyer_po_id from subcon_embel_production_mst a, subcon_embel_production_dtls b where a.id=b.mst_id and a.job_no='$jobno' and a.entry_form=324 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_qc_res =sql_select($sql_qc);
	
	foreach ($sql_qc_res as $row)
	{
		$qcdtls_data_arr[$row[csf("color_size_id")]]['qty']+=$row[csf("quantity")];
	}
	unset($sql_qc_res);
	
	
	$proddtls_data_arr=array();
	
	$sql_prod="SELECT a.id, b.id as upid, b.qcpass_qty as quantity, b.po_id, b.color_size_id, a.buyer_po_id from subcon_embel_production_mst a, subcon_embel_production_dtls b where a.id=b.mst_id and a.job_no='$jobno' and a.entry_form=315 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_prod_res =sql_select($sql_prod);
	
	foreach ($sql_prod_res as $row)
	{
		$proddtls_data_arr[$row[csf("color_size_id")]]['qty']+=$row[csf("quantity")];
	}
	unset($sql_prod_res);
	
	
	
	$sql_del="SELECT a.id, a.mst_id, a.order_id, a.delivery_qty, a.remarks, a.color_size_id, a.bill_status, a.sort_qty, a.reject_qty,a.cutting_number from subcon_delivery_dtls a, subcon_delivery_mst b where b.id=a.mst_id and b.job_no='$jobno' and b.entry_form=325 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_del_res =sql_select($sql_del);
	$updtls_data_arr=array(); $pre_qty_arr=array();
	
	foreach ($sql_del_res as $row)
	{
		if($row[csf("mst_id")]==$update_id)
		{
			$updtls_data_arr[$row[csf("order_id")]][$row[csf("color_size_id")]]['dtlsid']=$row[csf("id")];
			$updtls_data_arr[$row[csf("order_id")]][$row[csf("color_size_id")]]['qty']=$row[csf("delivery_qty")];
			$updtls_data_arr[$row[csf("order_id")]][$row[csf("color_size_id")]]['remarks']=$row[csf("remarks")];
			$updtls_data_arr[$row[csf("order_id")]][$row[csf("color_size_id")]]['bill_status']=$row[csf("bill_status")];
			$updtls_data_arr[$row[csf("order_id")]][$row[csf("color_size_id")]]['sort_qty']=$row[csf("sort_qty")];
			$updtls_data_arr[$row[csf("order_id")]][$row[csf("color_size_id")]]['reject_qty']=$row[csf("reject_qty")];
			$updtls_data_arr[$row[csf("order_id")]][$row[csf("color_size_id")]]['cutting_number']=$row[csf("cutting_number")];
		}
		else
		{
			$pre_qty_arr[$row[csf("order_id")]][$row[csf("color_size_id")]]['qty']+=$row[csf("delivery_qty")];
		}
	}
	unset($sql_del_res);
	
	$sql_job="SELECT a.id, a.embellishment_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, b.main_process_id, b.order_uom, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty,b.buyer_po_no, b.buyer_style_ref,a.within_group
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
		where a.entry_form=311 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and b.id=c.mst_id and a.embellishment_job='$jobno' order by c.id ASC";
	//echo $sql_job;
	
	$sql_result =sql_select($sql_job);
	$k=0;
	$num_rowss=count($sql_result);
	foreach ($sql_result as $row)
	{
		$k++;
		if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		if($row[csf("main_process_id")]==1) $emb_type=$emblishment_print_type;
		else if($row[csf("main_process_id")]==2) $emb_type=$emblishment_embroy_type;
		else if($row[csf("main_process_id")]==3) $emb_type=$emblishment_wash_type;
		else if($row[csf("main_process_id")]==4) $emb_type=$emblishment_spwork_type;
		else if($row[csf("main_process_id")]==5) $emb_type=$emblishment_gmts_type;
		else $emb_type="";
		
		$quantity=0; $dtlsup_id=""; $balanceQty=0; $preDelv_qty=0; $orderQty=0; $remarks=""; $bill_status=0; $sort_qty=0; $reject_qty=0; $variableorderQty=0;
		
		
		if($variable_status==1) //oder entry   wise 
		{
			$variableorderQty=($row[csf("order_uom")]==1) ? number_format($row[csf("qnty")],0,'.','') : number_format($row[csf("qnty")]*12,0,'.','');
			$qc_qty=$variableorderQty;
		}
		else if($variable_status==2) //production  wise 
		{
			//$qc_qty=$proddtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
			$qc_qty=$proddtls_data_arr[$row[csf("breakdown_id")]]['qty'];
		}
		else /// qc entry 
		{
			$qc_qty=$qcdtls_data_arr[$row[csf("breakdown_id")]]['qty'];	
			//$qc_qty=$qcdtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
				
		}
		$preDelv_qty=$pre_qty_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
		//$orderQty=number_format($row[csf("qnty")]*12,4,'.','');
		$orderQty=($row[csf("order_uom")]==1) ? number_format($row[csf("qnty")],0,'.','') : number_format($row[csf("qnty")]*12,0,'.','');
		
		
		$balanceQty=$qc_qty-$preDelv_qty;
		
		if($update_id!=0)
		{
			$quantity=$updtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
			//$update_quantity=$updtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
		}
		else $quantity=$balanceQty;
		if($quantity==0) $quantity='';
		if($balanceQty==0) $balanceQty='';
		
		$dtlsup_id=$updtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['dtlsid'];
		$remarks=$updtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['remarks'];
		$bill_status=$updtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['bill_status'];
		if($bill_status==1) $disable="disabled"; else $disable="";
		
		$sort_qty=$updtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['sort_qty'];
		$reject_qty=$updtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['reject_qty'];
		$cutting_number=$updtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['cutting_number'];
		
		$buyer_po="";$buyer_style="";
		if($row[csf("within_group")]==1)
		{
			$buyer_po=$buyer_po_arr[$row[csf("buyer_po_id")]]['po'];
			$buyer_style=$buyer_po_arr[$row[csf("buyer_po_id")]]['style'];
		}
		else
		{
			$buyer_po=$row[csf("buyer_po_no")];
			$buyer_style=$row[csf("buyer_style_ref")];
		}
		?>
        <tr bgcolor="<? echo $bgcolor; ?>">
            <td align="center"><? echo $k; ?>
            	<input name="txtbuyerPoId_<? echo $k; ?>" id="txtbuyerPoId_<? echo $k; ?>" type="hidden" class="text_boxes" style="width:70px" value="<? echo $row[csf("buyer_po_id")]; ?>" />
            </td>
            <td style="word-break:break-all"><? echo $buyer_po;//$buyer_po_arr[$row[csf("buyer_po_id")]]['po']; ?></td>
            <td style="word-break:break-all"><? echo $buyer_style;//$buyer_po_arr[$row[csf("buyer_po_id")]]['style']; ?></td>
            <td style="word-break:break-all"><? echo $garments_item[$row[csf("gmts_item_id")]]; ?></td>
            <td style="word-break:break-all"><? echo $body_part[$row[csf("body_part")]]; ?></td>
            <td style="word-break:break-all"><? echo $emblishment_name_array[$row[csf("main_process_id")]]; ?></td>
            <td style="word-break:break-all"><? echo $emb_type[$row[csf("embl_type")]]; ?></td>
            <td style="word-break:break-all"><? echo $color_arrey[$row[csf("color_id")]]; ?></td>
            <td style="word-break:break-all"><? echo $size_arrey[$row[csf("size_id")]]; ?></td>
            <td align="right"><? echo $orderQty; ?>&nbsp;</td>
            <td align="right"><? echo $qc_qty; ?>&nbsp;</td>
            <td align="right"><? echo $preDelv_qty; ?>&nbsp;</td>
            <td align="right"><? echo $balanceQty; ?>&nbsp;</td>
            <td title="<? if($bill_status==1) echo "Already Bill Issued."; else echo "";?>"><input type="text" name="txtCurrDelv_<? echo $k; ?>" id="txtCurrDelv_<? echo $k; ?>" class="text_boxes_numeric" style="width:45px;" value="<? echo $quantity; ?>" onBlur="fnc_production_qty_ability(this.value,<? echo $k; ?>); fnc_total_calculate();" placeholder="<? echo $balanceQty; ?>" pre_delv_qty="<? echo $preDelv_qty; ?>"   variable_status="<? echo $variable_status; ?>" delv_qty="<? echo $qc_qty; ?>" <? echo $disable; ?> /></td>
            <td align="center"><input type="text" name="txtsort_<? echo $k; ?>" id="txtsort_<? echo $k; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo $sort_qty; ?>" onBlur="fnc_total_calculate();" /></td>
            <td align="center"><input type="text" name="txtreject_<? echo $k; ?>" id="txtreject_<? echo $k; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo $reject_qty; ?>" onBlur="fnc_total_calculate();" /></td>
             <td><input type="text" name="txtcuttingNo_<? echo $k; ?>" id="txtcuttingNo_<? echo $k; ?>" class="text_boxes" style="width:60px;" value="<? echo $cutting_number ; ?>" /></td>
            <td><input type="text" name="txtRemarks_<? echo $k; ?>" id="txtRemarks_<? echo $k; ?>" class="text_boxes" style="width:60px;" value="<? echo $remarks; ?>" />
                <input type="hidden" name="txtDtlsUpdateId_<? echo $k; ?>" id="txtDtlsUpdateId_<? echo $k; ?>" style="width:80px" class="text_boxes" value="<? echo $dtlsup_id; ?>" />
                <input type="hidden" name="txtColorSizeid_<? echo $k; ?>" id="txtColorSizeid_<? echo $k; ?>" style="width:80px" class="text_boxes" value="<? echo $row[csf("breakdown_id")]; ?>" />
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
 		//table lock here 
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		if(str_replace("'","",$txt_update_id)=="")
		{
			$id=return_next_id("id", "subcon_delivery_mst", 1);

			if($db_type==2) $mrr_cond="and TO_CHAR(insert_date,'YYYY')=".date('Y',time()); else if($db_type==0) $mrr_cond="and year(insert_date)=".date('Y',time());
			$new_sys_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'EMBD', date("Y",time()), 5, "SELECT delivery_prefix, delivery_prefix_num from subcon_delivery_mst where company_id=$cbo_company_name and entry_form=325 $mrr_cond order by id DESC ", "delivery_prefix", "delivery_prefix_num" ));
			
			$field_array_mst="id, delivery_prefix, delivery_prefix_num, delivery_no, company_id, location_id, within_group, party_id, party_location, deli_party, deli_party_location, delivery_date, remarks, job_no, challan_no , entry_form, inserted_by, insert_date, status_active, is_deleted";
			
			$data_array_mst="(".$id.",'".$new_sys_number[1]."','".$new_sys_number[2]."','".$new_sys_number[0]."',".$cbo_company_name.",".$cbo_location_name.",".$cbo_within_group.",".$cbo_party_name.",".$cbo_party_location.",".$cbo_deli_party_name.",".$cbo_deli_party_location.",".$txt_delivery_date.",".$txt_remarks.",".$txtJob_no.",".$txt_challan_no.",325,".$user_id.",'".$pc_date_time."',1,0)";
			
			$mrr_no=$new_sys_number[0];
			$data_arr_dtls="";
			$id_dtls=return_next_id("id", "subcon_delivery_dtls", 1); $po_wise_qty_arr=array(); $color_size_arr=array();
			$field_array_dtls="id, mst_id, buyer_po_id, order_id, remarks, color_size_id, delivery_qty, sort_qty, reject_qty,cutting_number, inserted_by, insert_date, status_active, is_deleted";
			for($i=1; $i<= str_replace("'","",$total_row); $i++)
			{
				$txtbuyerPoId="txtbuyerPoId_".$i;
				$txtCurrDelv="txtCurrDelv_".$i;
				$txtsort="txtsort_".$i;
				$txtreject="txtreject_".$i;
				$txtRemarks="txtRemarks_".$i;
				$txtpoid="txtpoid_".$i;
				$txtColorSizeid="txtColorSizeid_".$i;
				$txtDtlsUpdateId="txtDtlsUpdateId_".$i;
				$txtcuttingNo="txtcuttingNo_".$i;
				 
				if($data_arr_dtls!="") $data_arr_dtls.=","; 	
				$data_arr_dtls.="(".$id_dtls.",".$id.",".$$txtbuyerPoId.",".$$txtpoid.",".$$txtRemarks.",".$$txtColorSizeid.",".$$txtCurrDelv.",".$$txtsort.",".$$txtreject.",".$$txtcuttingNo.",'".$user_id."','".$pc_date_time."',1,0)"; 
				$po_wise_qty_arr[str_replace("'","",$$txtpoid)]+=str_replace("'","",$$txtCurrDelv);
				$color_size_arr[str_replace("'","",$$txtColorSizeid)]=str_replace("'","",$$txtCurrDelv);
				
				$id_dtls=$id_dtls+1;
			}
		}
		else
		{
			$id=str_replace("'","",$txt_update_id);
			$mrr_no=str_replace("'","",$txt_delv_no);
			
			$field_array_update_mst="location_id*within_group*party_id*party_location*deli_party*deli_party_location*delivery_date*remarks*job_no*challan_no*updated_by*update_date";
			$data_array_update_mst="".$cbo_location_name."*".$cbo_within_group."*".$cbo_party_name."*".$cbo_party_location."*".$cbo_deli_party_name."*".$cbo_deli_party_location."*".$txt_delivery_date."*".$txt_remarks."*".$txtJob_no."*".$txt_challan_no."*".$user_id."*'".$pc_date_time."'";
			
			$data_arr_dtls="";
			$id_dtls=return_next_id("id", "subcon_delivery_dtls", 1); $po_wise_qty_arr=array(); $color_size_arr=array();
			$field_array_dtls="id, mst_id, buyer_po_id, order_id, remarks, color_size_id, delivery_qty, sort_qty, reject_qty,cutting_number, inserted_by, insert_date, status_active, is_deleted";
			for($i=1; $i<= str_replace("'","",$total_row); $i++)
			{
				$txtbuyerPoId="txtbuyerPoId_".$i;
				$txtCurrDelv="txtCurrDelv_".$i;
				$txtsort="txtsort_".$i;
				$txtreject="txtreject_".$i;
				$txtRemarks="txtRemarks_".$i;
				$txtpoid="txtpoid_".$i;
				$txtColorSizeid="txtColorSizeid_".$i;
				$txtDtlsUpdateId="txtDtlsUpdateId_".$i;
				$txtcuttingNo="txtcuttingNo_".$i;
				 
				if($data_arr_dtls!="") $data_arr_dtls.=","; 	
				$data_arr_dtls.="(".$id_dtls.",".$id.",".$$txtbuyerPoId.",".$$txtpoid.",".$$txtRemarks.",".$$txtColorSizeid.",".$$txtCurrDelv.",".$$txtsort.",".$$txtreject.",".$$txtcuttingNo.",'".$user_id."','".$pc_date_time."',1,0)"; 
				$po_wise_qty_arr[str_replace("'","",$$txtpoid)]+=str_replace("'","",$$txtCurrDelv);
				$color_size_arr[str_replace("'","",$$txtColorSizeid)]=str_replace("'","",$$txtCurrDelv);
				
				$id_dtls=$id_dtls+1;
			}
		}
		
		$color_size_qty_arr=array();
		$color_size_qty_sql=sql_select("SELECT count(id) as cid, mst_id, sum(qnty) as qnty from subcon_ord_breakdown where job_no_mst=$txtJob_no and delivery_status<>3 group by mst_id");
		foreach ($color_size_qty_sql as $row)
		{
			$color_size_qty_arr[$row[csf("mst_id")]]['qty']=$row[csf("qnty")]*12;
			$color_size_qty_arr[$row[csf("mst_id")]]['cid']=$row[csf("cid")];
		}
		unset($color_size_qty_sql);
		
		$delivery_qty_arr=array();
		$delivery_qty_sql=sql_select("SELECT b.delivery_qty, b.order_id from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.id!=$id and a.entry_form=325 and a.job_no=$txtJob_no");
		foreach ($delivery_qty_sql as $row)
		{
			$delivery_qty_arr[$row[csf("order_id")]]+=$row[csf("delivery_qty")];
		}
		unset($delivery_qty_sql);
		$flag=1;
		$order_status=str_replace("'","",$cboshipingStatus);
		
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
		
		foreach($po_wise_qty_arr as $po_id=>$qty)
		{
			$delv_qty=$delivery_qty_arr[$po_id]['qty']+$qty;
			$cid=$color_size_qty_arr[$po_id]['cid'];
			if($order_status!=0) $order_status=$order_status;
			else if($cid>0) $order_status=2; else $order_status=3;
			
			$sts_po = execute_query("update subcon_ord_dtls set delivery_status=$order_status where id=$po_id and delivery_status<>3",1);
			if($sts_po==1 && $flag==1) $flag=1; else $flag=0;
			
			$sts_cs = execute_query("update subcon_ord_breakdown set delivery_status=$order_status where mst_id=$po_id and delivery_status<>3",1);
			if($sts_cs==1 && $flag==1) $flag=1; else $flag=0;
			
			$sts_mst = execute_query("update subcon_delivery_dtls set delivery_status=$order_status where order_id=$po_id",1);
			if($sts_mst==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		//echo "10**$rID**$rID1"; die;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'","",$id)."**".$mrr_no;
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
				echo "0**".str_replace("'","",$id)."**".$mrr_no;
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
  	else if ($operation==1) // Update Here 
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$del_sql_dtls="SELECT id, bill_status from subcon_delivery_dtls where mst_id=$txt_update_id and status_active=1 and is_deleted=0";//
		$all_dtls_id_arr=array();
		//echo "10**$txt_update_id"; die;
		//echo "10**".$rec_sql_dtls; die;
		$nameArray=sql_select( $del_sql_dtls ); 
		foreach($nameArray as $row)
		{
			$all_dtls_id_arr[]=$row[csf('id')];
		}
		unset($nameArray);
		
		$id=str_replace("'","",$txt_update_id);
		$mrr_no=str_replace("'","",$txt_delv_no);
			
		$field_array_update_mst="location_id*within_group*party_id*party_location*deli_party*deli_party_location*delivery_date*remarks*job_no*challan_no*updated_by*update_date";
		$data_array_update_mst="".$cbo_location_name."*".$cbo_within_group."*".$cbo_party_name."*".$cbo_party_location."*".$cbo_deli_party_name."*".$cbo_deli_party_location."*".$txt_delivery_date."*".$txt_remarks."*".$txtJob_no."*".$txt_challan_no."*".$user_id."*'".$pc_date_time."'";
		
		$data_arr_dtls="";
		$id_dtls=return_next_id("id", "subcon_delivery_dtls", 1); $po_wise_qty_arr=array(); $color_size_arr=array();
		$field_array_dtls="id, mst_id, buyer_po_id, order_id, remarks, color_size_id, delivery_qty, sort_qty, reject_qty,cutting_number, inserted_by, insert_date, status_active, is_deleted";
		
		$field_arr_up="buyer_po_id*order_id*remarks*color_size_id*delivery_qty*sort_qty*reject_qty*cutting_number*updated_by*update_date";
		
		for($i=1; $i<= str_replace("'","",$total_row); $i++)
		{
			$txtbuyerPoId="txtbuyerPoId_".$i;
			$txtCurrDelv="txtCurrDelv_".$i;
			$txtsort="txtsort_".$i;
			$txtreject="txtreject_".$i;
			$txtRemarks="txtRemarks_".$i;
			$txtpoid="txtpoid_".$i;
			$txtColorSizeid="txtColorSizeid_".$i;
			$txtDtlsUpdateId="txtDtlsUpdateId_".$i;
			$txtcuttingNo="txtcuttingNo_".$i;
			
			if(str_replace("'","",$$txtDtlsUpdateId)=="")
			{
				if($data_arr_dtls!="") $data_arr_dtls.=","; 	
				$data_arr_dtls.="(".$id_dtls.",".$id.",".$$txtbuyerPoId.",".$$txtpoid.",".$$txtRemarks.",".$$txtColorSizeid.",".$$txtCurrDelv.",".$$txtsort.",".$$txtreject.",".$$txtcuttingNo.",'".$user_id."','".$pc_date_time."',1,0)"; 
				
				$id_dtls=$id_dtls+1;
			}
			else if(str_replace("'","",$$txtDtlsUpdateId)!="")
			{
				$data_arr_up[str_replace("'","",$$txtDtlsUpdateId)]=explode("*",("".$$txtbuyerPoId."*".$$txtpoid."*".$$txtRemarks."*".$$txtColorSizeid."*".$$txtCurrDelv."*".$$txtsort."*".$$txtreject."*".$$txtcuttingNo."*".$user_id."*'".$pc_date_time."'"));
				$id_arr_delv[]=str_replace("'","",$$txtDtlsUpdateId);
				$hdn_break_id_arr[]=str_replace("'","",$$txtDtlsUpdateId);
			}
		}
		
		$color_size_qty_arr=array();
		$color_size_qty_sql=sql_select("SELECT count(id) as cid, mst_id, sum(qnty) as qnty from subcon_ord_breakdown where job_no_mst=$txtJob_no and delivery_status<>3 group by mst_id");
		foreach ($color_size_qty_sql as $row)
		{
			$color_size_qty_arr[$row[csf("mst_id")]]['qty']=$row[csf("qnty")]*12;
			$color_size_qty_arr[$row[csf("mst_id")]]['cid']=$row[csf("cid")];
		}
		unset($color_size_qty_sql);
		
		$delivery_qty_arr=array();
		$delivery_qty_sql=sql_select("SELECT b.delivery_qty, b.order_id from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.id!=$txt_update_id and a.entry_form=325 and a.job_no=$txtJob_no");
		foreach ($delivery_qty_sql as $row)
		{
			$delivery_qty_arr[$row[csf("order_id")]]+=$row[csf("delivery_qty")];
		}
		unset($delivery_qty_sql);
		$flag=1;
		$order_status=str_replace("'","",$cboshipingStatus);
		
		//$rID=$rID1=$rID2=$rID3=1;
		
		$rID=sql_update("subcon_delivery_mst",$field_array_update_mst,$data_array_update_mst,"id",$txt_update_id,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		//echo "10**$txt_tot_row<pre>";
		//print_r($data_array_update_dtls);die;
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
		
		$field_array_del="status_active*is_deleted*updated_by*update_date";
		$data_array_del="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$distance_delete_id="";
	
		if(implode(',',$id_arr_delv)!="")
		{
			$distance_delete_id=implode(',',array_diff($all_dtls_id_arr,$id_arr_delv));
		}
		else
		{
			$distance_delete_id=implode(',',$all_dtls_id_arr);
		}
		
		if(str_replace("'",'',$distance_delete_id)!="")
		{
			$ex_delete_id=explode(",",$distance_delete_id);
			$rID3=execute_query(bulk_update_sql_statement( "subcon_delivery_dtls", "id", $field_array_del,$data_array_del,$ex_delete_id),1);
			if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		foreach($po_wise_qty_arr as $po_id=>$qty)
		{
			$delv_qty=$delivery_qty_arr[$po_id]['qty']+$qty;
			$cid=$color_size_qty_arr[$po_id]['cid'];
			if($order_status!=0) $order_status=$order_status;
			else if($cid>0) $order_status=2; else $order_status=3;
			
			$sts_po = execute_query("update subcon_ord_dtls set delivery_status=$order_status where id=$po_id and delivery_status<>3",1);
			if($sts_po==1 && $flag==1) $flag=1; else $flag=0;
			
			$sts_cs = execute_query("update subcon_ord_breakdown set delivery_status=$order_status where mst_id=$po_id and delivery_status<>3",1);
			if($sts_cs==1 && $flag==1) $flag=1; else $flag=0;
			
			$sts_mst = execute_query("update subcon_delivery_dtls set delivery_status=$order_status where order_id=$po_id",1);
			if($sts_mst==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		//echo "10**$rID**$rID1**$rID2"; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_update_id)."**".$mrr_no;
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
				echo "1**".str_replace("'","",$txt_update_id)."**".$mrr_no;
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
	else if ($operation==2)  // Delete Here---------------------------------------------------------- 
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$del_sql_dtls="SELECT id, bill_status from subcon_delivery_dtls where mst_id=$txt_update_id and bill_status=1 and status_active=1 and is_deleted=0";//
		$nameArray=sql_select( $del_sql_dtls ); 
		if(count($nameArray)>0)
		{
			echo "13**"; disconnect($con); die;
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="'".$user_id."'*'".$pc_date_time."'*0*1";
		
		$flag=1;
		$rID=sql_delete("subcon_delivery_mst",$field_array,$data_array,"id","".$txt_update_id."",0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$rID1=sql_delete("subcon_delivery_dtls",$field_array,$data_array,"mst_id","".$txt_update_id."",0);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
  		
		//$rID = sql_delete("pro_ex_factory_mst",$field_array,$data_array,"id",$txt_mst_id,1);
		//$dtlsrID = sql_delete("pro_ex_factory_dtls","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);
 		
 		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_update_id);
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
				echo "2**".str_replace("'","",$txt_update_id); 
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
			load_drop_down( 'emb_delivery_controller', company+'_'+within_group+'_'+party_name, 'load_drop_down_buyer_pop', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Embl. Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer Po');
			else if(val==5) $('#search_by_td').html('Buyer Style');
			else if(val==6) $('#search_by_td').html('Challan No');
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
                    		<th width="100" id="search_by_td">Emb Job No</th>
                            <th width="100" colspan="2">Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="selected_job">  <!--  echo $data;-->
							<? 
								echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "load_drop_down( 'embellishment_material_receive_controller', this.value+'_'+".$within_group.", 'load_drop_down_buyer_pop', 'buyer_td' );"); ?>
                            </td>
                            <td>
							<?
								echo create_drop_down( "cbo_within_group", 50, $yes_no,"", 0, "--  --",$within_group, "load_drop_down( 'embellishment_material_receive_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer_pop', 'buyer_td' );" ); ?>
							</td>
                            <td id="buyer_td">
								<? 
								echo create_drop_down( "cbo_party_name", 120, $blank_array,"", 1, "-- Select Party --", $selected, "" );?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:60px" placeholder="Delivery ID" />
                            </td>
                            <td>
								<?
                                    $search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style",6=>"Challan No", 7=> "IR/IB");
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
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_year_selection').value, 'create_delivery_search_list_view', 'search_div', 'emb_delivery_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
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
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[9]";
		$insert_year="YEAR(a.insert_date)";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $delivery_date = "and a.delivery_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $delivery_date ="";
		$year_cond=" and to_char(a.insert_date,'YYYY')=$data[9]";
		$insert_year="to_char(a.insert_date,'YYYY')";
	}


	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	
	if($within_group==1)
	{
		if($search_type==1)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
				else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
				else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
				else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";
				else if ($search_by==6) $challan_cond=" and a.challan_no = '$search_str' ";
				else if ($search_by==7) $group_cond=" and c.grouping = '$search_str' ";
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
				else if ($search_by==6) $challan_cond=" and a.challan_no like '%$search_str%'";
				else if ($search_by==7) $group_cond=" and c.grouping = '$search_str' ";
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
				else if ($search_by==6) $challan_cond=" and a.challan_no like '$search_str%'";
				else if ($search_by==7) $group_cond=" and c.grouping = '$search_str' ";
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
				else if ($search_by==6) $challan_cond=" and a.challan_no like '%$search_str'"; 
				else if ($search_by==7) $group_cond=" and c.grouping = '$search_str' ";
			}
			if ($data[4]!='') $delivery_id_cond=" and a.delivery_prefix_num like '%$data[4]'"; else $delivery_id_cond="";
		}	
	}
	
	
	if($within_group==2)
	{
	
		if($search_type==1)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
				else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
				else if ($search_by==4) $within_no_cond=" and b.buyer_po_no = '$search_str' ";
				else if ($search_by==5) $within_no_cond=" and b.buyer_style_ref = '$search_str' ";
				else if ($search_by==6) $challan_cond=" and a.challan_no = '$search_str' ";
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
				else if ($search_by==4) $within_no_cond=" and b.buyer_po_no like '%$search_str%'";
				else if ($search_by==5) $within_no_cond=" and b.buyer_style_ref like '$search_str%'"; 
				else if ($search_by==6) $challan_cond=" and a.challan_no like '%$search_str%'";  
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
				else if ($search_by==4) $within_no_cond=" and b.buyer_po_no like '$search_str%'";
				else if ($search_by==5) $within_no_cond=" and b.buyer_style_ref like '$search_str%'"; 
				else if ($search_by==6) $challan_cond=" and a.challan_no like '$search_str%'";  
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
				else if ($search_by==4) $within_no_cond=" and b.buyer_po_no like '%$search_str'";
				else if ($search_by==5) $within_no_cond=" and b.buyer_style_ref like '%$search_str'";  
				else if ($search_by==6) $challan_cond=" and a.challan_no like '%$search_str'";  
			}
			if ($data[4]!='') $delivery_id_cond=" and a.delivery_prefix_num like '%$data[4]'"; else $delivery_id_cond="";
		}	
		
	}
	
	$party_arr=return_library_array( "SELECT id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array("SELECT id, company_name from lib_company",'id','company_name');
	$po_arr=return_library_array( "SELECT id,order_no from subcon_ord_dtls",'id','order_no');
	
	$po_ids=''; $buyer_po_arr=array();
	if($within_group==1)
	{
		if($db_type==0) $id_cond="group_concat(b.id)";
		else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
		//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
		if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
		{
			$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
			
			if ($po_ids=="")
			{
				$po_idsCond="";
				echo "Not Found."; die;
			}
		}
		//echo $po_ids;
		if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
		
		$po_sql ="SELECT a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
			$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
		}
		unset($po_sql_res);
	}
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
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.embellishment_job=b.job_no_mst $search_com_cond", "id");
		if ($spo_ids=="")
		{
			$po_idsCond="";
			echo "Not Found."; die;
		}
	}
	
	if ( $spo_ids!="") $spo_idsCond=" and b.order_id in ($spo_ids)"; else $spo_idsCond="";
	if ( $spo_ids!="") $orderidsCond=" and b.id in ($spo_ids)"; else $orderidsCond="";
	
	$order_buyer_po_array=array();
	$orderbuyerpo_arr=array(); 
	$order_sql ="select b.id,b.buyer_po_no,b.buyer_style_ref,b.buyer_buyer from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.entry_form='311' $orderidsCond $within_no_cond"; 
	$order_sql_res=sql_select($order_sql);
	foreach ($order_sql_res as $row)
	{
		$order_buyer_po_array[]=$row[csf("id")];
		$orderbuyerpo_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
		$orderbuyerpo_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
		$orderbuyerpo_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_buyer")];
	}
	if($within_group==2)
	{
		if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2))
		{
			$order_buyer_po=implode(",",$order_buyer_po_array);
			if ($order_buyer_po=="")
			{
				$order_order_buyer_poCond="";
				echo "Not Found."; die;
			}
			if ($order_buyer_po!="") $order_order_buyer_poCond=" and b.order_id in ($order_buyer_po)"; else $order_order_buyer_poCond="";
		}
	}
	
	if($within_group==1)
	{
		$sql= "SELECT a.id, a.delivery_no, a.delivery_prefix_num, $insert_date_cond as year, a.location_id, a.within_group, a.party_id, a.party_location, a.deli_party, c.grouping, a.deli_party_location, a.delivery_date, a.job_no,a.challan_no, $wo_cond as order_id, $buyer_po_id_cond as buyer_po_id, $delivery_status_cond as delivery_status from subcon_delivery_mst a, subcon_delivery_dtls b, wo_po_break_down c 
		where a.id=b.mst_id and c.id=b.buyer_po_id and a.entry_form='325' and c.status_active=1 and c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $delivery_date $company $buyer_cond $year_cond $withinGroup $delivery_id_cond $spo_idsCond $po_idsCond $challan_cond $order_order_buyer_poCond $group_cond
		group by a.id, c.grouping, a.delivery_no, a.delivery_prefix_num, a.insert_date, a.location_id, a.within_group, a.party_id, a.party_location, a.deli_party, a.deli_party_location, a.delivery_date, a.job_no,a.challan_no order by a.id DESC ";
	}else{
		$sql= "SELECT a.id, a.delivery_no, a.delivery_prefix_num, $insert_date_cond as year, a.location_id, a.within_group, a.party_id, a.party_location, a.deli_party, a.deli_party_location, a.delivery_date, a.job_no,a.challan_no, $wo_cond as order_id, $buyer_po_id_cond as buyer_po_id, $delivery_status_cond as delivery_status from subcon_delivery_mst a, subcon_delivery_dtls b 
		where a.id=b.mst_id and a.entry_form='325' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $delivery_date $company $buyer_cond $year_cond $withinGroup $delivery_id_cond $spo_idsCond $po_idsCond $challan_cond $order_order_buyer_poCond
		group by a.id, a.delivery_no, a.delivery_prefix_num, a.insert_date, a.location_id, a.within_group, a.party_id, a.party_location, a.deli_party, a.deli_party_location, a.delivery_date, a.job_no,a.challan_no order by a.id DESC ";
	}
	
	//echo $sql; 
	$result = sql_select($sql);
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="920" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="70">Delivery No</th>
                <th width="70">Year</th>
                <th width="120">Party Name</th>
                <th width="100">Challan No</th>
                <th width="80">Delivery Date</th>
                <th width="120">Order No</th>
                <th width="100">IR/IB</th>
                <th width="100">Buyer Po</th>
                <th>Buyer Style</th>
            </thead>
     	</table>
     <div style="width:920px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach($result as $row)
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$order_no='';$odbuyer_po=""; $odbuyer_style=""; $odbuyer_buyer="";
				$order_id=array_unique(explode(",",$row[csf("order_id")]));
				foreach($order_id as $val)
				{
					if($order_no=="") $order_no=$po_arr[$val]; else $order_no.=",".$po_arr[$val];
					if($odbuyer_po=="") $odbuyer_po=$orderbuyerpo_arr[$val]['po']; else $odbuyer_po.=','.$orderbuyerpo_arr[$val]['po'];
					if($odbuyer_style=="") $odbuyer_style=$orderbuyerpo_arr[$val]['style']; else $odbuyer_style.=','.$orderbuyerpo_arr[$val]['style'];
					if($odbuyer_buyer=="") $odbuyer_buyer=$orderbuyerpo_arr[$val]['buyer']; else $odbuyer_buyer.=','.$orderbuyerpo_arr[$val]['buyer'];
				}
				$order_no=implode(",",array_unique(explode(",",$order_no)));
				
				$buyer_po=""; $buyer_style=""; $buyer_name="";
				$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
				foreach($buyer_po_id as $po_id)
				{
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
					if($buyer_name=="") $buyer_name=$party_arr[$buyer_po_arr[$po_id]['buyer']]; else $buyer_name.=','.$party_arr[$buyer_po_arr[$po_id]['buyer']];
				}
				//$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				//$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				
				if($row[csf("within_group")]==1)
				{
					$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
					$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
					$buyer_name=implode(",",array_unique(explode(",",$buyer_name)));
				}
				else
				{
					$buyer_po=implode(",",array_unique(explode(",",$odbuyer_po)));
					$buyer_style=implode(",",array_unique(explode(",",$odbuyer_style)));
					$buyer_name=implode(",",array_unique(explode(",",$odbuyer_buyer)));
				}
				
				$delivery_status=explode(",",$row[csf('delivery_status')]);
				$party_name="";
				if($row[csf("within_group")]==1) $party_name=$comp[$row[csf("party_id")]]; else $party_name=$party_arr[$row[csf("party_id")]];
				$str_data="";
				$str_data=$row[csf('id')].'***'.$row[csf('delivery_no')].'***'.$row[csf('location_id')].'***'.$row[csf('within_group')].'***'.$row[csf('party_id')].'***'.change_date_format($row[csf('delivery_date')]).'***'.$row[csf('remarks')].'***'.$row[csf('job_no')].'***'.$order_no.'***'.$buyer_style.'***'.$buyer_name.'***'.implode(",",$delivery_status).'***'.$row[csf('party_location')].'***'.$row[csf('deli_party')].'***'.$row[csf('deli_party_location')].'***'.$row[csf('challan_no')];
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $str_data; ?>');" > 
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="70" align="center"><? echo $row[csf("delivery_prefix_num")]; ?></td>
                        <td width="70" align="center"><? echo $row[csf("year")]; ?></td>
                        <td width="120"><? echo $party_name; ?></td>		
						<td width="100"><? echo $row[csf("challan_no")]; ?></td>
						<td width="80"><? echo change_date_format($row[csf("delivery_date")]);  ?></td>
                        <td width="120" style="word-break:break-all"><p><? echo $order_no; ?></p></td>	
                        <td width="100" style="word-break:break-all"><? echo $row[csf('grouping')]; ?></td>
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

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "SELECT id, delivery_no, company_id, location_id, within_group, party_id, delivery_date, job_no, remarks from subcon_delivery_mst where id='$data'" ); 
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_delv_no').value 		= '".$row[csf("delivery_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n"; 
		
		echo "document.getElementById('cbo_within_group').value		= '".$row[csf("within_group")]."';\n"; 
		echo "load_drop_down( 'requires/emb_delivery_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";
		
		echo "load_drop_down( 'requires/emb_delivery_controller', document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer', 'buyer_td' );\n"; 		
		
		echo "document.getElementById('cbo_location_name').value	= '".$row[csf("location_id")]."';\n";  
		echo "document.getElementById('cbo_party_name').value		= '".$row[csf("party_id")]."';\n"; 
		echo "document.getElementById('txt_delivery_date').value	= '".change_date_format($row[csf("delivery_date")])."';\n";
		//echo "document.getElementById('cbo_party_name').value		= '".$row[csf("remarks")]."';\n";  
		echo "document.getElementById('txt_remarks').value 			= '".$row[csf("remarks")]."';\n";  
	    echo "document.getElementById('update_id').value            = '".$row[csf("id")]."';\n";
		//echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_material_receive',1);\n";
		
		echo "$('#cbo_within_group').attr('disabled','true')".";\n"; 
		echo "$('#cbo_party_name').attr('disabled','true')".";\n"; 
	}
	exit();
}

if($action=="embl_delivery_entry_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$show=$data[4];
	$cbo_template_id=$data[5];
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

	$department_arr = return_library_array("select id, department_name from lib_department", "id", "department_name");
	
	$buyer_po_arr=array();
	$po_sql ="Select a.buyer_name, a.style_ref_no, a.job_no, b.id, b.po_number,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['job_no']=$row[csf("job_no")];
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
		$buyer_po_arr[$row[csf("id")]]['grouping']=$row[csf("grouping")];
	}
	unset($po_sql_res);
	
	/*$cust_arr=array();
	$cust_buyer_style_array=sql_select("SELECT id, embellishment_job, order_id, order_no from subcon_ord_mst where entry_form=311 and status_active=1 and is_deleted=0");
	foreach ($cust_buyer_style_array as $cust_val) 
	{
		$cust_arr[$cust_val[csf('embellishment_job')]]['order_no']=$cust_val[csf('order_no')]; 
		$cust_arr[$cust_val[csf('embellishment_job')]]['job']=$cust_val[csf('embellishment_job')]; 
	}
	unset($cust_buyer_style_array);*/
	$sql="select id, prefix_no_num, sys_no, location_id, recipe_id, job_no, order_id,challan_no from subcon_embel_production_mst where entry_form=325 and status_active = 1 and is_deleted = 0 and company_id='$company_id' $location_cond $system_no_cond $recipe_no_cond order by id DESC";
	
	$sql_mst = "select id, delivery_no, company_id, location_id, within_group, party_id, delivery_date, job_no, challan_no, remarks from subcon_delivery_mst where entry_form=325 and id='$data[1]'";
	$dataArray = sql_select($sql_mst); $party_name=""; $party_address=""; $party_address="";
	if( $dataArray[0][csf('within_group')]==1)
	{
		$party_name=$company_library[$dataArray[0][csf('party_id')]];
		
		$party_address=show_company($dataArray[0][csf('party_id')],'','');
		
		//if($party_address!="") $party_address=$party_name.', '.$party_address;
	}
	else if($dataArray[0][csf('within_group')]==2) 
	{
		$party_name=$buyer_library[$dataArray[0][csf('party_id')]];
		//$nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$dataArray[0][csf('party_id')]"); 
		$partyid=$dataArray[0][csf('party_id')];
		$nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$partyid");
		foreach ($nameArray as $result)
		{ 
			if($result!="") $party_address=$result['address_1'];
		}
		//if($address!="") $party_address=$party_name.', '.$party_address;
	}

	$company = $data[0];
	$issue_id = $data[1];
	$system_no = $data[3];

	//for gate pass
	$sql_get_pass = "SELECT a.ID, a.SYS_NUMBER, a.BASIS, a.COMPANY_ID, a.GET_PASS_NO, a.DEPARTMENT_ID, a.ATTENTION, a.SENT_BY, a.WITHIN_GROUP, a.SENT_TO, a.CHALLAN_NO, a.OUT_DATE, a.TIME_HOUR, a.TIME_MINUTE, a.RETURNABLE, a.DELIVERY_AS, a.EST_RETURN_DATE, a.INSERTED_BY, a.CARRIED_BY, a.LOCATION_ID, a.COM_LOCATION_ID, a.VHICLE_NUMBER, a.LOCATION_NAME, a.REMARKS, a.DO_NO, a.MOBILE_NO, a.ISSUE_ID, a.RETURNABLE_GATE_PASS_REFF, a.DELIVERY_COMPANY, a.ISSUE_PURPOSE,a.DRIVER_NAME,a.DRIVER_LICENSE_NO,a.SECURITY_LOCK_NO, b.QUANTITY, b.NO_OF_BAGS FROM inv_gate_pass_mst a, INV_GATE_PASS_DTLS b WHERE a.id = b.mst_id AND a.company_id = ".$company." AND a.basis = 59 AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND a.challan_no LIKE '".$system_no."%'";

	$sql_get_pass_rslt = sql_select($sql_get_pass);
	$is_gate_pass = 0;
	$is_gate_out = 0;
	$gate_pass_id = '';
	$gatePassDataArr = array();
	foreach($sql_get_pass_rslt as $row)
	{
		$exp = explode(',', $row['CHALLAN_NO']);
		// echo "<pre>"; print_r($exp);
		foreach($exp as $key=>$val)
		{
			if($val == $system_no)
			{
				$is_gate_pass = 1;
				$gate_pass_id = $row['ID'];
				
				$row['OUT_DATE'] = ($row['OUT_DATE']!=''?date('d-m-Y', strtotime($row['OUT_DATE'])):'');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE']!=''?date('d-m-Y', strtotime($row['EST_RETURN_DATE'])):'');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE']!=''?date('d-m-Y', strtotime($row['EST_RETURN_DATE'])):'');
				
				if($row['WITHIN_GROUP'] == 1)
				{
					//$row['SENT_TO'] = ($row['BASIS']==50?$buyer_dtls_arr[$row['SENT_TO']]:$supplier_dtls_arr[$row['SENT_TO']]);
					$row['SENT_TO'] = $company_library[$row['SENT_TO']];
					$row['LOCATION_NAME'] = $location_arr[$row['LOCATION_ID']];
				}
				
				//for gate pass info
				$gatePassDataArr[$val]['gate_pass_id'] = $row['SYS_NUMBER'];
				$gatePassDataArr[$val]['from_company'] = $company_library[$row['COMPANY_ID']];
				$gatePassDataArr[$val]['from_location'] =$location_arr[ $row['COM_LOCATION_ID']];
				$gatePassDataArr[$val]['gate_pass_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$val]['returnable'] = $yes_no[$row['RETURNABLE']];
				$gatePassDataArr[$val]['est_return_date'] = $row['EST_RETURN_DATE'];
				
				$gatePassDataArr[$val]['to_company'] = $row['SENT_TO'];
				$gatePassDataArr[$val]['to_location'] = $row['LOCATION_NAME'];
				//$gatePassDataArr[$val]['delivery_kg'] += $row['QUANTITY'];
				$gatePassDataArr[$val]['delivery_pc'] += $row['QUANTITY'];
				$gatePassDataArr[$val]['delivery_bag'] += $row['NO_OF_BAGS'];
				
				$gatePassDataArr[$val]['department'] = $department_arr[$row['DEPARTMENT_ID']];
				$gatePassDataArr[$val]['attention'] = $row['ATTENTION'];
				$gatePassDataArr[$val]['issue_purpose'] = $row['ISSUE_PURPOSE'];
				$gatePassDataArr[$val]['remarks'] = $row['REMARKS'];
				$gatePassDataArr[$val]['carried_by'] = $row['CARRIED_BY'];
				$gatePassDataArr[$val]['vhicle_number'] = $row['VHICLE_NUMBER'];
				$gatePassDataArr[$val]['mobile_no'] = $row['MOBILE_NO'];
				$gatePassDataArr[$val]['driver_name'] = $row['DRIVER_NAME'];
				$gatePassDataArr[$val]['driver_license_no'] = $row['DRIVER_LICENSE_NO'];
				$gatePassDataArr[$val]['security_lock_no'] = $row['SECURITY_LOCK_NO'];
			}
		}
	}
	// echo "<pre>";print_r($gatePassDataArr);

	//for gate out
	if($gate_pass_id != '')
	{
		$sql_gate_out="SELECT OUT_DATE, OUT_TIME FROM INV_GATE_OUT_SCAN WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0 AND INV_GATE_PASS_MST_ID='".$gate_pass_id."'";
		$sql_gate_out_rslt = sql_select($sql_gate_out);
		if(!empty($sql_gate_out_rslt))
		{
			foreach($sql_gate_out_rslt as $row)
			{
				$is_gate_out = 1;
				$gatePassDataArr[$system_no]['out_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$system_no]['out_time'] = $row['OUT_TIME'];
			}
		}
	}
	
	?> 
    
    <div style="width:1020px; font-size:6px">
        <table width="100%" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="70" align="right"> 
                    <img  src='../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='100%' width='100%' />
                </td>
                <td>
                    <table width="800" cellspacing="0" align="center">
                        <tr>
                            <td align="center" style="font-size:20px"><strong ><? echo $company_library[$data[0]]; ?></strong></td>
                        </tr>
                        <tr>
                            <td align="center"  style="font-size:16px"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                        </tr>
                        <tr class="form_caption">
                            <td  align="center" style="font-size:14px">  
                                <? echo show_company($data[0],'',''); ?> 
                            </td>  
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px"><strong><? echo $data[2]; ?></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>   
          <br>
        <table width="100%" cellpadding="0" cellspacing="0" >  
            <tr>
            	<td width="130"><strong>Delivery ID:</strong></td>
                <td width="175"><? echo $dataArray[0][csf('delivery_no')]; ?></td>
            	<td width="130"><strong>Party: </strong></td>
                <td width="175"><? echo $party_name; ?></td>
                <td width="130"><strong>Party Location:</strong></td>
                <td><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
            </tr>
            <tr>
            	<td><strong>Party Address: </strong></td>
                <td colspan="3"><? echo $party_address; ?></td>
            	<td><strong>Delivery Date: </strong></td>
                <td><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
            </tr>
            <tr>
            	<td width="130"><strong>Challan No:</strong></td>
                <td width="175"><? echo $dataArray[0][csf('challan_no')]; ?></td>
            	<td><strong>Remarks: </strong></td>
                <td colspan="3"><? echo $dataArray[0][csf('remarks')]; ?></td>
            </tr>
        </table>
        <br>
        
            <table cellspacing="0" width="1200" border="1" rules="all" class="rpt_table">
                <thead bgcolor="#dddddd" align="center">
                    <th width="30">SL</th>
                    <th width="90">Buyer PO</th>
                    <th width="100">Buyer</th>
                    <th width="90">Style Ref.</th>
                    <th width="100">Buyer JOb</th>
                    <th width="90">Gmts Item</th>
                    <th width="120">Body Part</th>
                    <th width="70">Process/Type</th>
                    <th width="80">Color</th>
                    <th width="80">Size</th>
                    <th width="60">Current Delv (Pcs)</th>
                    <th width="60">Sort Qty.</th>
                    <th width="60">Reject Qty.</th>
                    <th width="60">Cutting Number</th>
                    <th>Remarks</th>
                </thead>
				<?
				
				$mst_id = $data[1];
				$com_id = $data[0];
				$job_no = $dataArray[0][csf('job_no')];
				

				$sql= "select  a.id, a.embellishment_job, b.id as order_id, b.order_no, b.buyer_po_id,b.buyer_po_no, b.buyer_style_ref, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.id as color_size_id, c.color_id, c.size_id, c.qnty as qty, d.delivery_qty, d.sort_qty, d.reject_qty, d.remarks,d.cutting_number,d.id as dtlsid  from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_dtls d where a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and c.id=d.color_size_id 
				and a.entry_form=311 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.company_id='$data[0]' and a.embellishment_job='$job_no' and d.mst_id='$data[1]' order by c.id ASC";
				//echo $sql; die;
				//$delData=sql_select("select id, mst_id, buyer_po_id, order_id, remarks, color_size_id, delivery_qty from subcon_delivery_dtls where status_active=1 and is_deleted=0 and mst_id='$data[1]'");
				$sql_res=sql_select($sql);
				foreach ($sql_res as $row)
				{
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['id']=$row[csf("id")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['embellishment_job']=$row[csf("embellishment_job")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['order_id']=$row[csf("order_id")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['buyer_po_id']=$row[csf("buyer_po_id")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['buyer_po_no']=$row[csf("buyer_po_no")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['buyer_style_ref']=$row[csf("buyer_style_ref")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['main_process_id']=$row[csf("main_process_id")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['gmts_item_id']=$row[csf("gmts_item_id")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['embl_type']=$row[csf("embl_type")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['body_part']=$row[csf("body_part")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['color_size_id']=$row[csf("color_size_id")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['size_id']=$row[csf("size_id")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['qty']+=$row[csf("qty")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['delivery_qty']=$row[csf("delivery_qty")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['sort_qty']=$row[csf("sort_qty")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['reject_qty']=$row[csf("reject_qty")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['remarks']=$row[csf("remarks")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['cutting_number']=$row[csf("cutting_number")];
					$emb_buyer_po_id_arr[$row[csf("order_no")]]['buyer_po_id']=$row[csf("buyer_po_id")];
					$emb_buyer_po_id_arr[$row[csf("order_no")]]['embellishment_job']=$row[csf("embellishment_job")];
				}
				unset($sql_res);
				/*echo '<pre>';
				print_r($emb_del_data_arr);*/
 				$i=1;
 				$grand_tot_qty=$grand_tot_sort_qty=$grand_tot_reject_qty=0;
				foreach ($emb_del_data_arr as $order_no=> $order_no_data ) 
				{
					?>
					<tr bgcolor="#dddddd">
						<td colspan="15" align="left"><b>Embl. Job No: <? echo $emb_buyer_po_id_arr[$order_no]["embellishment_job"] ; ?>;</b>&nbsp;&nbsp;&nbsp;&nbsp;<b>Work Order No: <? echo $order_no; ?>;<b>Internal Ref. No.: <? echo $buyer_po_arr[$emb_buyer_po_id_arr[$order_no]["buyer_po_id"]]['grouping']; ?>;</b>
                        </td>
					</tr>
					<?
					$sub_total_qty=$sub_total_sort_qty=$sub_total_reject_qty=0;
					foreach ($order_no_data as $color_id=> $color_id_data ) 
					{
						$color_total_qty=$color_total_sort_qty=$color_total_reject_qty=0;
						foreach ($color_id_data as $id=> $row ) 
						{

							$embl_name=$row['main_process_id'];
							if($embl_name==1) $new_subprocess_array= $emblishment_print_type;
							else if($embl_name==2) $new_subprocess_array= $emblishment_embroy_type;
							else if($embl_name==3) $new_subprocess_array= $emblishment_wash_type;
							else if($embl_name==4) $new_subprocess_array= $emblishment_spwork_type;
							else if($embl_name==5) $new_subprocess_array= $emblishment_gmts_type;
							else $new_subprocess_array=$blank_array;
							
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
							?>
		                    <tr bgcolor="<? echo $bgcolor; ?>">
		                        <td><? echo $i; ?></td>
		                        <td style="word-break:break-all"><? echo $row["buyer_po_no"];//$buyer_po_arr[$row["buyer_po_id")]]['po']; ?></td>
		                        <td style="word-break:break-all"><? echo $buyer_library[$buyer_po_arr[$emb_buyer_po_id_arr[$order_no]["buyer_po_id"]]['buyer']]; ?></td>

		                        <td style="word-break:break-all"><? echo $row["buyer_style_ref"];//$buyer_po_arr[$row["buyer_po_id")]]['style']; ?></td>
		                        <td style="word-break:break-all"><? echo $buyer_po_arr[$emb_buyer_po_id_arr[$order_no]["buyer_po_id"]]['job_no']; //$buyer_po_arr[$row["buyer_po_id"]]['job_no']; ?></td>

		                        <td style="word-break:break-all"><? echo $garments_item[$row['gmts_item_id']]; ?></td>
		                        <td style="word-break:break-all"><? echo $body_part[$row['body_part']]; ?>&nbsp;</td>
		                        <td style="word-break:break-all"><? echo $new_subprocess_array[$row['embl_type']]; ?>&nbsp;</td>
		                        <td style="word-break:break-all"><? echo $color_arr[$color_id]; ?>&nbsp;</td>
		                        <td style="word-break:break-all" align="center"><? echo $size_arr[$row['size_id']]; ?>&nbsp;</td>
		                        <td align="right"><? echo number_format($row['delivery_qty'], 2, '.', ''); ?>&nbsp;</td>
		                        <td align="right"><? echo number_format($row['sort_qty'], 2, '.', ''); ?>&nbsp;</td>
		                        <td align="right"><? echo number_format($row['reject_qty'], 2, '.', ''); ?>&nbsp;</td>
		                         <td align="right"><? echo $row['cutting_number']; ?>&nbsp;</td>
		                        <td style="word-break:break-all"><? echo $row['remarks']; ?>&nbsp;</td>
		                    </tr>
							<?
							$i++;
							$color_total_qty+=$row['delivery_qty'];
							$color_total_sort_qty+=$row['sort_qty'];
							$color_total_reject_qty+=$row['reject_qty'];
							$sub_total_qty+=$row['delivery_qty'];
							$sub_total_sort_qty+=$row['sort_qty'];
							$sub_total_reject_qty+=$row['reject_qty'];
							$grand_tot_qty+=$row['delivery_qty'];
							$grand_tot_sort_qty+=$row['sort_qty'];
							$grand_tot_reject_qty+=$row['reject_qty'];
						}
						?>
					<tr class="tbl_bottom">
	                    <td colspan="10" align="right"><b>Color Total:</b></td>
	                    <td align="right"><b><? echo number_format($color_total_qty,2); ?></b></td>
	                    <td align="right"><b><? echo number_format($color_total_sort_qty,2); ?></b></td>
	                    <td align="right"><b><? echo number_format($color_total_reject_qty,2); ?></b></td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                </tr>
					<?
					}
				}
				?>
                <tr class="tbl_bottom">
                    <td colspan="10" align="right"><b>Order Total:</b></td>
                    <td align="right"><b><? echo number_format($sub_total_qty,2); ?></b></td>
                    <td align="right"><b><? echo number_format($sub_total_sort_qty,2); ?></b></td>
                    <td align="right"><b><? echo number_format($sub_total_reject_qty,2); ?></b></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr class="tbl_bottom">
                    <td align="right" colspan="10"><strong>Grand Total</strong></td>
                    <td align="right"><? echo number_format($grand_tot_qty, 2, '.', ''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($grand_tot_sort_qty, 2, '.', ''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($grand_tot_reject_qty, 2, '.', ''); ?>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
			<?if($show==1){?>
             <!-- ============= Gate Pass Info Start ========= -->
			<table style="margin-right:-40px;" cellspacing="0" width="1200" border="1" rules="all" class="rpt_table">
                <tr>
                	<td colspan="15" height="30" style="border-left:hidden;border-right:hidden; text-align: center;">For mishandling or other reason no claim is acceptable in any stage, once the Goods is received in good condition and quality and out from factory premises.</td>
                </tr>
                <tr>
                	<td colspan="4" align="center" valign="middle" style="font-size:25px;"><strong>&lt;&lt;Gate Pass&gt;&gt;</strong></td>
                    <td colspan="9" align="center" valign="middle" id="gate_pass_barcode_img_id_1<?php //echo $x; ?>" height="50"></td>
                </tr>
                <tr>
                	<td colspan="2" title="<? echo $system_no; ?>"><strong>From Company:</strong></td>
                	<td colspan="2" width="120"><?php echo $gatePassDataArr[$system_no]['from_company']; ?></td>

                	<td colspan="2"><strong>To Company:</strong></td>
                	<td colspan="3" width="120"><?php echo $gatePassDataArr[$system_no]['to_company']; ?></td>

                	<td colspan="3"><strong>Carried By:</strong></td>
                	<td colspan="3" width="120"><?php echo $gatePassDataArr[$system_no]['carried_by']; ?></td>
                </tr>						
                <tr>
                	<td colspan="2"><strong>From Location:</strong></td>
                	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['from_location']; ?></td>
                	<td colspan="2"><strong>To Location:</strong></td>
                	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['to_location']; ?></td>
                	<td colspan="3"><strong>Driver Name:</strong></td>
                	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['driver_name']; ?></td>
                </tr>
                <tr>
                	<td colspan="2"><strong>Gate Pass ID:</strong></td>
                	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['gate_pass_id']; ?></td>
                	<td colspan="2" rowspan="2"><strong>Delivery Qnty</strong></td>
                	<td align="center"><strong>Kg</strong></td>
                	<td align="center"><strong>Roll</td>
                	<td align="center"><strong>PCS</td>
                	<td colspan="3"><strong>Vehicle Number:</strong></td>
                	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['vhicle_number']; ?></td>
                </tr>						
                <tr>
                	<td colspan="2"><strong>Gate Pass Date:</strong></td>
                	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['gate_pass_date']; ?></td>
                	<td align="center"><?php echo 0;//$gatePassDataArr[$system_no]['delivery_pc']; ?></td>
                	<td align="center"><?php echo $gatePassDataArr[$system_no]['delivery_bag']; ?></td>
                	<td align="center"><?php 
                	/*if ($gatePassDataArr[$system_no]['gate_pass_id'] !="") 
                	{
                		if ($grnd_total_issue_qty_pcs_qnty>0) {
                		 	echo $grnd_total_issue_qty_pcs_qnty;
                		 } 
                	}*/
                	echo $gatePassDataArr[$system_no]['delivery_pc'];
                	?></td>
                	<td colspan="3"><strong>Driver License No.:</strong></td>
                	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['driver_license_no']; ?></td>
                </tr>						
                <tr>
                	<td colspan="2"><strong>Out Date:</strong></td>
                	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['out_date']; ?></td>
                	<td colspan="2"><strong>Dept. Name:</strong></td>
                	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['department']; ?></td>
                	<td colspan="3"><strong>Mobile No.:</strong></td>
                	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['mobile_no']; ?></td>
                </tr>						
                <tr>
                	<td colspan="2"><strong>Out Time:</strong></td>
                	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['out_time']; ?></td>
                	<td colspan="2"><strong>Attention:</strong></td>
                	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['attention']; ?></td>
                	<td colspan="3"><strong>Sequrity Lock No.:</strong></td>
                	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['security_lock_no']; ?></td>
                </tr>						
                <tr>
                	<td colspan="2"><strong>Returnable:</strong></td>
                	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['returnable']; ?></td>
                	<td colspan="2"><strong>Purpose:</strong></td>
                	<td colspan="9"><?php echo $gatePassDataArr[$system_no]['issue_purpose']; ?></td>
                </tr>						
                <tr>
                	<td colspan="2"><strong>Est. Return Date:</strong></td>
                	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['est_return_date']; ?></td>
                	<td colspan="2"><strong>Remarks:</strong></td>
                	<td colspan="9"><?php echo $gatePassDataArr[$system_no]['remarks']; ?></td>
                </tr>
            </table>
            <!-- ============= Gate Pass Info End =========== -->
			<?}?>
            <br>
			<? echo signature_table(154, $com_id, "1200px",$cbo_template_id, 20); 
 			?>
            
         </div>
		<script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		<script>
			//for gate pass barcode
			function generateBarcodeGatePass(valuess)
			{
				//var zs = '<?php// echo $x; ?>';
				var value = valuess;//$("#barcodeValue").val();
				var btype = 'code39';//$("input[name=btype]:checked").val();
				var renderer = 'bmp';// $("input[name=renderer]:checked").val();
				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				$("#gate_pass_barcode_img_id_1").html('11');
				value = {code: value, rect: false};
				$("#gate_pass_barcode_img_id_1").show().barcode(value, btype, settings);
			}
			var value = '<? echo $gatePassDataArr[$system_no]['gate_pass_id']; ?>';
			
			if( value != '')
			{
				generateBarcodeGatePass('<? echo strtoupper($gatePassDataArr[$system_no]['gate_pass_id']); ?>');
			}
		</script>
        <div style="page-break-after:always;"></div>
        <?php
	exit();
}

if($action=="embl_delivery_entry_print_1")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$show=$data[4];
	$cbo_template_id=$data[5];
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

	$department_arr = return_library_array("select id, department_name from lib_department", "id", "department_name");
	
	$buyer_po_arr=array();
	$po_sql ="Select a.buyer_name, a.style_ref_no, a.job_no, b.id, b.po_number,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['job_no']=$row[csf("job_no")];
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
		$buyer_po_arr[$row[csf("id")]]['grouping']=$row[csf("grouping")];
	}
	unset($po_sql_res);
	
	/*$cust_arr=array();
	$cust_buyer_style_array=sql_select("SELECT id, embellishment_job, order_id, order_no from subcon_ord_mst where entry_form=311 and status_active=1 and is_deleted=0");
	foreach ($cust_buyer_style_array as $cust_val) 
	{
		$cust_arr[$cust_val[csf('embellishment_job')]]['order_no']=$cust_val[csf('order_no')]; 
		$cust_arr[$cust_val[csf('embellishment_job')]]['job']=$cust_val[csf('embellishment_job')]; 
	}
	unset($cust_buyer_style_array);*/
	$sql="select id, prefix_no_num, sys_no, location_id, recipe_id, job_no, order_id,challan_no from subcon_embel_production_mst where entry_form=325 and status_active = 1 and is_deleted = 0 and company_id='$company_id' $location_cond $system_no_cond $recipe_no_cond order by id DESC";
	
	$sql_mst = "select id, delivery_no, company_id, location_id, within_group, party_id, delivery_date, job_no, challan_no, remarks from subcon_delivery_mst where entry_form=325 and id='$data[1]'";
	$dataArray = sql_select($sql_mst); $party_name=""; $party_address=""; $party_address="";
	if( $dataArray[0][csf('within_group')]==1)
	{
		$party_name=$company_library[$dataArray[0][csf('party_id')]];
		
		$party_address=show_company($dataArray[0][csf('party_id')],'','');
		
		//if($party_address!="") $party_address=$party_name.', '.$party_address;
	}
	else if($dataArray[0][csf('within_group')]==2) 
	{
		$party_name=$buyer_library[$dataArray[0][csf('party_id')]];
		//$nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$dataArray[0][csf('party_id')]"); 
		$partyid=$dataArray[0][csf('party_id')];
		$nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$partyid");
		foreach ($nameArray as $result)
		{ 
			if($result!="") $party_address=$result['address_1'];
		}
		//if($address!="") $party_address=$party_name.', '.$party_address;
	}

	$company = $data[0];
	$issue_id = $data[1];
	$system_no = $data[3];

	//for gate pass
	$sql_get_pass = "SELECT a.ID, a.SYS_NUMBER, a.BASIS, a.COMPANY_ID, a.GET_PASS_NO, a.DEPARTMENT_ID, a.ATTENTION, a.SENT_BY, a.WITHIN_GROUP, a.SENT_TO, a.CHALLAN_NO, a.OUT_DATE, a.TIME_HOUR, a.TIME_MINUTE, a.RETURNABLE, a.DELIVERY_AS, a.EST_RETURN_DATE, a.INSERTED_BY, a.CARRIED_BY, a.LOCATION_ID, a.COM_LOCATION_ID, a.VHICLE_NUMBER, a.LOCATION_NAME, a.REMARKS, a.DO_NO, a.MOBILE_NO, a.ISSUE_ID, a.RETURNABLE_GATE_PASS_REFF, a.DELIVERY_COMPANY, a.ISSUE_PURPOSE,a.DRIVER_NAME,a.DRIVER_LICENSE_NO,a.SECURITY_LOCK_NO, b.QUANTITY, b.NO_OF_BAGS FROM inv_gate_pass_mst a, INV_GATE_PASS_DTLS b WHERE a.id = b.mst_id AND a.company_id = ".$company." AND a.basis = 59 AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND a.challan_no LIKE '".$system_no."%'";

	$sql_get_pass_rslt = sql_select($sql_get_pass);
	$is_gate_pass = 0;
	$is_gate_out = 0;
	$gate_pass_id = '';
	$gatePassDataArr = array();
	foreach($sql_get_pass_rslt as $row)
	{
		$exp = explode(',', $row['CHALLAN_NO']);
		// echo "<pre>"; print_r($exp);
		foreach($exp as $key=>$val)
		{
			if($val == $system_no)
			{
				$is_gate_pass = 1;
				$gate_pass_id = $row['ID'];
				
				$row['OUT_DATE'] = ($row['OUT_DATE']!=''?date('d-m-Y', strtotime($row['OUT_DATE'])):'');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE']!=''?date('d-m-Y', strtotime($row['EST_RETURN_DATE'])):'');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE']!=''?date('d-m-Y', strtotime($row['EST_RETURN_DATE'])):'');
				
				if($row['WITHIN_GROUP'] == 1)
				{
					//$row['SENT_TO'] = ($row['BASIS']==50?$buyer_dtls_arr[$row['SENT_TO']]:$supplier_dtls_arr[$row['SENT_TO']]);
					$row['SENT_TO'] = $company_library[$row['SENT_TO']];
					$row['LOCATION_NAME'] = $location_arr[$row['LOCATION_ID']];
				}
				
				//for gate pass info
				$gatePassDataArr[$val]['gate_pass_id'] = $row['SYS_NUMBER'];
				$gatePassDataArr[$val]['from_company'] = $company_library[$row['COMPANY_ID']];
				$gatePassDataArr[$val]['from_location'] =$location_arr[ $row['COM_LOCATION_ID']];
				$gatePassDataArr[$val]['gate_pass_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$val]['returnable'] = $yes_no[$row['RETURNABLE']];
				$gatePassDataArr[$val]['est_return_date'] = $row['EST_RETURN_DATE'];
				
				$gatePassDataArr[$val]['to_company'] = $row['SENT_TO'];
				$gatePassDataArr[$val]['to_location'] = $row['LOCATION_NAME'];
				//$gatePassDataArr[$val]['delivery_kg'] += $row['QUANTITY'];
				$gatePassDataArr[$val]['delivery_pc'] += $row['QUANTITY'];
				$gatePassDataArr[$val]['delivery_bag'] += $row['NO_OF_BAGS'];
				
				$gatePassDataArr[$val]['department'] = $department_arr[$row['DEPARTMENT_ID']];
				$gatePassDataArr[$val]['attention'] = $row['ATTENTION'];
				$gatePassDataArr[$val]['issue_purpose'] = $row['ISSUE_PURPOSE'];
				$gatePassDataArr[$val]['remarks'] = $row['REMARKS'];
				$gatePassDataArr[$val]['carried_by'] = $row['CARRIED_BY'];
				$gatePassDataArr[$val]['vhicle_number'] = $row['VHICLE_NUMBER'];
				$gatePassDataArr[$val]['mobile_no'] = $row['MOBILE_NO'];
				$gatePassDataArr[$val]['driver_name'] = $row['DRIVER_NAME'];
				$gatePassDataArr[$val]['driver_license_no'] = $row['DRIVER_LICENSE_NO'];
				$gatePassDataArr[$val]['security_lock_no'] = $row['SECURITY_LOCK_NO'];
			}
		}
	}
	// echo "<pre>";print_r($gatePassDataArr);

	//for gate out
	if($gate_pass_id != '')
	{
		$sql_gate_out="SELECT OUT_DATE, OUT_TIME FROM INV_GATE_OUT_SCAN WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0 AND INV_GATE_PASS_MST_ID='".$gate_pass_id."'";
		$sql_gate_out_rslt = sql_select($sql_gate_out);
		if(!empty($sql_gate_out_rslt))
		{
			foreach($sql_gate_out_rslt as $row)
			{
				$is_gate_out = 1;
				$gatePassDataArr[$system_no]['out_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$system_no]['out_time'] = $row['OUT_TIME'];
			}
		}
	}
	
	?> 
    
    <div style="width:1020px; font-size:6px">
        <table width="100%" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="70" align="right"> 
                    <img  src='../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='100%' width='100%' />
                </td>
                <td>
                    <table width="800" cellspacing="0" align="center">
                        <tr>
                            <td align="center" style="font-size:20px"><strong ><? echo $company_library[$data[0]]; ?></strong></td>
                        </tr>
                        <tr>
                            <td align="center"  style="font-size:16px"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                        </tr>
                        <tr class="form_caption">
                            <td  align="center" style="font-size:14px">  
                                <? echo show_company($data[0],'',''); ?> 
                            </td>  
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px"><strong><? echo $data[2]; ?></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>   
          <br>
        <table width="100%" cellpadding="0" cellspacing="0" >  
            <tr>
            	<td width="130"><strong>Delivery ID:</strong></td>
                <td width="175"><? echo $dataArray[0][csf('delivery_no')]; ?></td>
            	<td width="130"><strong>Party: </strong></td>
                <td width="175"><? echo $party_name; ?></td>
                <td width="130"><strong>Party Location:</strong></td>
                <td><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
            </tr>
            <tr>
            	<td><strong>Party Address: </strong></td>
                <td colspan="3"><? echo $party_address; ?></td>
            	<td><strong>Delivery Date: </strong></td>
                <td><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
            </tr>
            <tr>
            	<td width="130"><strong>Challan No:</strong></td>
                <td width="175"><? echo $dataArray[0][csf('challan_no')]; ?></td>
            	<td><strong>Remarks: </strong></td>
                <td colspan="3"><? echo $dataArray[0][csf('remarks')]; ?></td>
            </tr>
        </table>
        <br>
        
            <table cellspacing="0" width="1200" border="1" rules="all" class="rpt_table">
                <thead bgcolor="#dddddd" align="center">
                    <th width="30">SL</th>
                    <th width="90">Buyer PO</th>
                    <th width="100">Buyer</th>
                    <th width="90">Style Ref.</th>
                    <th width="100">Buyer JOb</th>
                    <th width="90">Gmts Item</th>
                    <th width="120">Body Part</th>
                    <th width="70">Process/Type</th>
                    <th width="80">Color</th>
                    <th width="80">Size</th>
                    <th width="60">Current Delv (Pcs)</th>
                    <th width="60">Sort Qty.</th>
                    <th width="60">Reject Qty.</th>
                    <th width="60">Cutting Number</th>
                    <th>Remarks</th>
                </thead>
				<?
				
				$mst_id = $data[1];
				$com_id = $data[0];
				$job_no = $dataArray[0][csf('job_no')];
				

				$sql= "select  a.id, a.embellishment_job, b.id as order_id, b.order_no, b.buyer_po_id,b.buyer_po_no, b.buyer_style_ref, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.id as color_size_id, c.color_id, c.size_id, c.qnty as qty, d.delivery_qty, d.sort_qty, d.reject_qty, d.remarks,d.cutting_number,d.id as dtlsid  from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_dtls d where a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and c.id=d.color_size_id 
				and a.entry_form=311 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.company_id='$data[0]' and a.embellishment_job='$job_no' and d.mst_id='$data[1]' order by c.size_id ASC";
				//echo $sql; die;
				//$delData=sql_select("select id, mst_id, buyer_po_id, order_id, remarks, color_size_id, delivery_qty from subcon_delivery_dtls where status_active=1 and is_deleted=0 and mst_id='$data[1]'");
				$sql_res=sql_select($sql);
				foreach ($sql_res as $row)
				{
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['id']=$row[csf("id")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['embellishment_job']=$row[csf("embellishment_job")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['order_id']=$row[csf("order_id")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['buyer_po_id']=$row[csf("buyer_po_id")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['buyer_po_no']=$row[csf("buyer_po_no")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['buyer_style_ref']=$row[csf("buyer_style_ref")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['main_process_id']=$row[csf("main_process_id")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['gmts_item_id']=$row[csf("gmts_item_id")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['embl_type']=$row[csf("embl_type")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['body_part']=$row[csf("body_part")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['color_size_id']=$row[csf("color_size_id")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['size_id']=$row[csf("size_id")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['qty']+=$row[csf("qty")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['delivery_qty']=$row[csf("delivery_qty")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['sort_qty']=$row[csf("sort_qty")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['reject_qty']=$row[csf("reject_qty")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['remarks']=$row[csf("remarks")];
					$emb_del_data_arr[$row[csf("order_no")]][$row[csf("color_id")]][$row[csf("dtlsid")]]['cutting_number']=$row[csf("cutting_number")];
					$emb_buyer_po_id_arr[$row[csf("order_no")]]['buyer_po_id']=$row[csf("buyer_po_id")];
					$emb_buyer_po_id_arr[$row[csf("order_no")]]['embellishment_job']=$row[csf("embellishment_job")];
				}
				unset($sql_res);
				/*echo '<pre>';
				print_r($emb_del_data_arr);*/
 				$i=1;
 				$grand_tot_qty=$grand_tot_sort_qty=$grand_tot_reject_qty=0;
				foreach ($emb_del_data_arr as $order_no=> $order_no_data ) 
				{
					?>
					<tr bgcolor="#dddddd">
						<td colspan="15" align="left"><b>Embl. Job No: <? echo $emb_buyer_po_id_arr[$order_no]["embellishment_job"] ; ?>;</b>&nbsp;&nbsp;&nbsp;&nbsp;<b>Work Order No: <? echo $order_no; ?>;<b>Internal Ref. No.: <? echo $buyer_po_arr[$emb_buyer_po_id_arr[$order_no]["buyer_po_id"]]['grouping']; ?>;</b>
                        </td>
					</tr>
					<?
					$sub_total_qty=$sub_total_sort_qty=$sub_total_reject_qty=0;
					foreach ($order_no_data as $color_id=> $color_id_data ) 
					{
						$color_total_qty=$color_total_sort_qty=$color_total_reject_qty=0;
						foreach ($color_id_data as $id=> $row ) 
						{

							$embl_name=$row['main_process_id'];
							if($embl_name==1) $new_subprocess_array= $emblishment_print_type;
							else if($embl_name==2) $new_subprocess_array= $emblishment_embroy_type;
							else if($embl_name==3) $new_subprocess_array= $emblishment_wash_type;
							else if($embl_name==4) $new_subprocess_array= $emblishment_spwork_type;
							else if($embl_name==5) $new_subprocess_array= $emblishment_gmts_type;
							else $new_subprocess_array=$blank_array;
							
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
							?>
		                    <tr bgcolor="<? echo $bgcolor; ?>">
		                        <td><? echo $i; ?></td>
		                        <td style="word-break:break-all"><? echo $row["buyer_po_no"];//$buyer_po_arr[$row["buyer_po_id")]]['po']; ?></td>
		                        <td style="word-break:break-all"><? echo $buyer_library[$buyer_po_arr[$emb_buyer_po_id_arr[$order_no]["buyer_po_id"]]['buyer']]; ?></td>

		                        <td style="word-break:break-all"><? echo $row["buyer_style_ref"];//$buyer_po_arr[$row["buyer_po_id")]]['style']; ?></td>
		                        <td style="word-break:break-all"><? echo $buyer_po_arr[$emb_buyer_po_id_arr[$order_no]["buyer_po_id"]]['job_no']; //$buyer_po_arr[$row["buyer_po_id"]]['job_no']; ?></td>

		                        <td style="word-break:break-all"><? echo $garments_item[$row['gmts_item_id']]; ?></td>
		                        <td style="word-break:break-all"><? echo $body_part[$row['body_part']]; ?>&nbsp;</td>
		                        <td style="word-break:break-all"><? echo $new_subprocess_array[$row['embl_type']]; ?>&nbsp;</td>
		                        <td style="word-break:break-all"><? echo $color_arr[$color_id]; ?>&nbsp;</td>
		                        <td style="word-break:break-all" align="center"><? echo $size_arr[$row['size_id']]; ?>&nbsp;</td>
		                        <td align="right"><? echo number_format($row['delivery_qty'], 2, '.', ''); ?>&nbsp;</td>
		                        <td align="right"><? echo number_format($row['sort_qty'], 2, '.', ''); ?>&nbsp;</td>
		                        <td align="right"><? echo number_format($row['reject_qty'], 2, '.', ''); ?>&nbsp;</td>
		                         <td align="right"><? echo $row['cutting_number']; ?>&nbsp;</td>
		                        <td style="word-break:break-all"><? echo $row['remarks']; ?>&nbsp;</td>
		                    </tr>
							<?
							$i++;
							$color_total_qty+=$row['delivery_qty'];
							$color_total_sort_qty+=$row['sort_qty'];
							$color_total_reject_qty+=$row['reject_qty'];
							$sub_total_qty+=$row['delivery_qty'];
							$sub_total_sort_qty+=$row['sort_qty'];
							$sub_total_reject_qty+=$row['reject_qty'];
							$grand_tot_qty+=$row['delivery_qty'];
							$grand_tot_sort_qty+=$row['sort_qty'];
							$grand_tot_reject_qty+=$row['reject_qty'];
						}
						?>
					<tr class="tbl_bottom">
	                    <td colspan="10" align="right"><b>Color Total:</b></td>
	                    <td align="right"><b><? echo number_format($color_total_qty,2); ?></b></td>
	                    <td align="right"><b><? echo number_format($color_total_sort_qty,2); ?></b></td>
	                    <td align="right"><b><? echo number_format($color_total_reject_qty,2); ?></b></td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                </tr>
					<?
					}
				}
				?>
                <tr class="tbl_bottom">
                    <td colspan="10" align="right"><b>Order Total:</b></td>
                    <td align="right"><b><? echo number_format($sub_total_qty,2); ?></b></td>
                    <td align="right"><b><? echo number_format($sub_total_sort_qty,2); ?></b></td>
                    <td align="right"><b><? echo number_format($sub_total_reject_qty,2); ?></b></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr class="tbl_bottom">
                    <td align="right" colspan="10"><strong>Grand Total</strong></td>
                    <td align="right"><? echo number_format($grand_tot_qty, 2, '.', ''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($grand_tot_sort_qty, 2, '.', ''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($grand_tot_reject_qty, 2, '.', ''); ?>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>

			<br><br>

			<?
			$sql_ressult=sql_select($sql);
			$size_summary_arr=array();
			foreach($sql_ressult as $row)
			{
				$size_summary_arr[$size_arr[$row[csf("size_id")]]]["size_id"]=$size_arr[$row[csf("size_id")]];
				$size_summary_arr[$size_arr[$row[csf("size_id")]]]["delivery_qty"]+=$row[csf("delivery_qty")];
			}

			// print_r($size_summary_arr)."___---";
			?>
			<table cellspacing="0" width="300" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr><th colspan="3">Summary</th></tr>
					<tr>
						<th>Sl</th>
						<th>Size</th>
						<th>Size Qty</th>
					<tr>
				</thead>
				<tbody>
					<? $i=1;
					asort($size_summary_arr);
					foreach($size_summary_arr as $key => $row)
					{ ?>
					<tr>
						<td><?=$i;?></td>
						<td><?=$row['size_id']?></td>
						<td><?=$row['delivery_qty']?></td>
					</tr>
					<?
					 $i++;
				   }
				  
				   ?>
				</tbody>
			</table>
			
            <br>
			<?  
 				echo signature_table(154, $com_id, "1200px",$cbo_template_id, 20); 
			?>
         </div>
		<script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		<script>
			//for gate pass barcode
			function generateBarcodeGatePass(valuess)
			{
				//var zs = '<?php// echo $x; ?>';
				var value = valuess;//$("#barcodeValue").val();
				var btype = 'code39';//$("input[name=btype]:checked").val();
				var renderer = 'bmp';// $("input[name=renderer]:checked").val();
				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				$("#gate_pass_barcode_img_id_1").html('11');
				value = {code: value, rect: false};
				$("#gate_pass_barcode_img_id_1").show().barcode(value, btype, settings);
			}
			var value = '<? echo $gatePassDataArr[$system_no]['gate_pass_id']; ?>';
			
			if( value != '')
			{
				generateBarcodeGatePass('<? echo strtoupper($gatePassDataArr[$system_no]['gate_pass_id']); ?>');
			}
		</script>
        <div style="page-break-after:always;"></div>
        <?php
	exit();
}
?>