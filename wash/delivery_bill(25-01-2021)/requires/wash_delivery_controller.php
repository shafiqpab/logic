<?php
session_start();
include('../../../includes/common.php');

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

/*if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );
	exit();	 
}*/

if($action=="chk_qty_level_variable")
{
	
    $sql =  sql_select("select variable_dtls,id from variable_setting_wash where company_name = $data and variable_list =1 and is_deleted = 0 and status_active = 1");
	$return_data="";
    if(count($sql)>0)
	{
		$return_data=$sql[0][csf('variable_dtls')];
	}
	else
	{ 
		$return_data=0; 
	}
	
	echo $return_data;
	die;
}

$order_num_arr=return_library_array("select id, order_no from subcon_ord_dtls", "id", "order_no");
if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	if($data[1]==1) $dropdown_name="cbo_location_name";
	else if($data[1]==2) $dropdown_name="cbo_party_location";
	else if($data[1]==3) $dropdown_name="cbo_deli_party_location";
	
	echo create_drop_down( $dropdown_name, 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "" );	
	exit();
}

if ($action == "company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=20 and report_id=105 and is_deleted=0 and status_active=1");

	$print_report_format_arr=explode(",",$print_report_format);
	echo "$('#print').hide();\n";
	echo "$('#print2').hide();\n";
	
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==78){echo "$('#print').show();\n";}
			if($id==66){echo "$('#print2').show();\n";}
		}
	}	
	exit();
}

if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);

	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";
	//$company_cond
	if($data[1]==1)
	{
		//echo "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name";
		echo create_drop_down( "cbo_party_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name","id,company_name", 1, "--Select Company--", $data[2], "$load_function");
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

	if ($within_group == 1) {
		echo create_drop_down( "cbo_party_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $party_name, "load_drop_down( 'requires/wash_delivery_controller', this.value+'_'+2, 'load_drop_down_location', 'party_location_td'); location_select(); $load_function;");
	} else {
		echo create_drop_down( "cbo_party_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond  and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $company, "" );
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
			load_drop_down( 'wash_delivery_controller', company+'_'+within_group+'_'+party, 'load_drop_down_buyer', 'buyer_td' );
			$('#cbo_party_name').attr('disabled','disabled');
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
                            <th width="100" id="search_by_td">Buyer PO</th>
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
									$search_by_arr=array(1=>"Wash Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer PO",5=>"Buyer Style");
									echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",4,'search_by(this.value)',0,"" );
								?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $data[3];?>, 'create_job_search_list_view', 'search_div', 'wash_delivery_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" />
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
	if(($job_cond!="" && $search_by==3)|| ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	
	if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	
	/*$buyer_po_arr=array();
	
	$po_sql ="Select a.style_ref_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
	}
	unset($po_sql_res);*/
	
	
	/*$buyer_po_sql="select a.subcon_job, a.within_group, a.party_id, b.id, b.order_no, b.order_quantity, b.buyer_po_id,b.buyer_po_no, b.buyer_style_ref, 
   b.buyer_buyer from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.entry_form=295 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$buyer_sql_res=sql_select($buyer_po_sql);
	foreach ($buyer_sql_res as $row)
	{
		$order_buyer_po_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
		$order_buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
		$order_buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
	}
	unset($buyer_sql_res);*/
	
	
	
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	//$arr=array (3=>$comp,6=>$yes_no,7=>$color_lib_arr);
	
	if($db_type==0)
	{
		$id_cond="group_concat(b.id)";
		$ins_year_cond="year(a.insert_date)";
		$color_id_str="group_concat(b.gmts_color_id)";
		$buyer_po_id_cond="group_concat(distinct(b.buyer_po_id))";
		$buyer_buyer_cond="group_concat(distinct(b.party_buyer_name))";
		$buyer_po_no_cond="group_concat(distinct(b.buyer_po_no))";
		$buyer_style_cond="group_concat(distinct(b.buyer_style_ref))";
	}
	else if($db_type==2)
	{
		$id_cond="listagg(b.id,',') within group (order by b.id)";
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str="listagg(b.gmts_color_id,',') within group (order by b.gmts_color_id)";
		$buyer_po_id_cond="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
		$buyer_buyer_cond="listagg(b.party_buyer_name,',') within group (order by b.party_buyer_name)";
		
		$buyer_po_no_cond="listagg(b.buyer_po_no,',') within group (order by b.buyer_po_no)";
		$buyer_style_cond="listagg(b.buyer_style_ref,',') within group (order by b.buyer_style_ref)";
		
	}
	
	
	 $sql= "select a.id, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, $color_id_str as color_id, $buyer_po_id_cond as buyer_po_id,$id_cond as order_id ,$buyer_buyer_cond  as buyer_buyer,$buyer_po_no_cond as buyer_po_no ,$buyer_style_cond  as buyer_style
	 from subcon_ord_mst a, subcon_ord_dtls b
	 where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond 
	 group by a.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date
	 order by a.id DESC";

	//echo $sql;die;

	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="885" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Job No</th>
            <th width="60">Year</th>
            <th width="120">W/O No</th>
            <th width="100">Buyer PO</th>
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
				/*$buyer_po=""; $buyer_style=""; $buyer_name='';
				
				$order_id_buyer_po_id=explode(",",$row[csf('order_id')]);
				foreach($order_id_buyer_po_id as $val)
				{
					if($buyer_po=="") $buyer_po=$order_buyer_po_arr[$val]['po']; else $buyer_po.=','.$order_buyer_po_arr[$val]['po'];
					if($buyer_style=="") $buyer_style=$order_buyer_po_arr[$val]['style']; else $buyer_style.=','.$order_buyer_po_arr[$val]['style'];
					//if($buyer_name=="") $buyer_name=$buyer_arr[$buyer_po_arr[$po_id]['buyer']]; else $buyer_name.=','.$buyer_arr[$buyer_po_arr[$po_id]['buyer']];
				}*/
				
				/*$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
				foreach($buyer_po_id as $po_id)
				{
					//if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
					//if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
					if($buyer_name=="") $buyer_name=$buyer_arr[$buyer_po_arr[$po_id]['buyer']]; else $buyer_name.=','.$buyer_arr[$buyer_po_arr[$po_id]['buyer']];
				}*/
				
				
				$buyer_po=implode(",",array_unique(explode(",",$row[csf('buyer_po_no')])));
				$buyer_style=implode(",",array_unique(explode(",",$row[csf('buyer_style')])));
				$buyer_name=implode(",",array_unique(explode(",",$row[csf('buyer_buyer')])));
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf('subcon_job')].'***'.$row[csf('order_no')].'***'.$buyer_style.'***'.$buyer_name.'***'.$buyer_po; ?>')" style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $row[csf('order_no')]; ?></td>
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
if($action=="delivery_list_view")
{
	?>	
	<div style="width:400px">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="120" >Order No</th>
                <th width="120" >Job No</th>
                <th width="80" >Delivery Qty</th>                    
            </thead>
    	</table> 
    </div>
	<div style="width:400px;max-height:180px; overflow:y-scroll" id="" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="details_table">
		<?php  
			$i=1;
			
		$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
		if($db_type==0)
		{
			$wo_cond="group_concat(distinct(b.order_id))";
			$do_cond="group_concat(distinct(b.id))";
		}
		else if($db_type==2)
		{
			$wo_cond="listagg(b.order_id,',') within group (order by b.order_id)";
			$do_cond="listagg(b.id,',') within group (order by b.id)";
		}
	$sql= "select a.id,sum(b.delivery_qty) as delivery_qty,$wo_cond as order_id,c.job_no_mst,$do_cond as dtls_id  from subcon_delivery_mst a, subcon_delivery_dtls b , subcon_ord_dtls c 
	where a.id=b.mst_id and a.entry_form='303' and  a.id='$data' and b.order_id=c.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,c.job_no_mst order by a.id DESC ";
			$sqlResult =sql_select($sql); 
 			foreach($sqlResult as $row)
			{
					
				$order_no='';
				$order_id=array_unique(explode(",",$row[csf("order_id")]));
				foreach($order_id as $val)
				{
					if($order_no=="") $order_no=$po_arr[$val]; else $order_no.=",".$po_arr[$val];
				}
				$order_no=implode(",",array_unique(explode(",",$order_no)));
				
 			?>
                <tr bgcolor="<?php echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="fnc_embl_delivery_dtls('<?php echo $row[csf('id')]; ?>_<?php echo $row[csf('dtls_id')]; ?>_<?php echo $row[csf('job_no_mst')]; ?>');" > 
                    <td width="40" align="center"><?php echo $i; ?></td>
                    <td width="120" align="center"><p><?php echo $order_no; ?>&nbsp;</p></td>
                    <td width="120" align="center"><p><?php echo $row[csf('job_no_mst')];; ?>&nbsp;</p></td>
                    <td width="80" align="center"><p><?php echo $row[csf('delivery_qty')]; ?></p></td>
                </tr>
			<?php
			$i++;
			}
			?>
		</table>
	</div>
	<?php
	exit();
}
if($action=="populate_data_from_data")
{
	
	$exdata=explode("**",$data);
	$jobno=''; 
	$update_id=0;
	$update_id=$exdata[0];
	$jobno=$exdata[1];
	$update_details_id=$exdata[3];
	$MsstDtlsID=explode('_',$update_details_id);
	$mstID = $MsstDtlsID[0];
	$dtlsID = $MsstDtlsID[1];
	$mstDtlsIdCond = "and b.id in ($dtlsID)";
	$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
	$job_arr=return_library_array( "select a.id,b.subcon_job from subcon_ord_dtls a , subcon_ord_mst b where a.mst_id=b.id",'id','subcon_job');
	
	
	$po_sql="select a.subcon_job, a.within_group, a.party_id, b.id, b.order_no, b.order_quantity, b.buyer_po_id,b.buyer_po_no, b.buyer_style_ref, 
   b.party_buyer_name as buyer_buyer from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.entry_form=295 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	
	$po_sql_res=sql_select($po_sql);
	
	
	foreach ($po_sql_res as $row)
	{
		//$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		//$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
		$buyer_po_arr[$row[csf("id")]]['buyer_buyer']=$row[csf("buyer_buyer")];
	}
	unset($po_sql_res);
	
	//print_r($job_arr);
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
	
	$sql= "select a.id, a.delivery_no, a.delivery_prefix_num, $insert_date_cond as year, a.location_id, a.within_group, a.party_id, a.party_location, a.deli_party, a.deli_party_location, a.delivery_date, a.job_no, $wo_cond as order_id, $buyer_po_id_cond as buyer_po_id, $delivery_status_cond as delivery_status,b.id as details_id from subcon_delivery_mst a, subcon_delivery_dtls b 
	where a.id=b.mst_id and a.entry_form='303' $mstDtlsIdCond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  
	group by a.id, a.delivery_no, a.delivery_prefix_num, a.insert_date, a.location_id, a.within_group, a.party_id, a.party_location, a.deli_party, a.deli_party_location, a.delivery_date, a.job_no,b.id order by a.id DESC ";
	//echo $sql; 
	$result = sql_select($sql);
	foreach($result as $row)
	{

		$order_no='';
		$order_id=array_unique(explode(",",$row[csf("order_id")]));
		foreach($order_id as $val)
		{
			if($order_no=="") $order_no=$po_arr[$val]; else $order_no.=",".$po_arr[$val];
		}
		$order_no=implode(",",array_unique(explode(",",$order_no)));
		
		$buyer_po=""; $buyer_style=""; $buyer_buyer="";
		$buyer_po_id=explode(",",$row[csf('order_id')]);
		foreach($buyer_po_id as $po_id)
		{
			if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
			if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
			if($buyer_buyer=="") $buyer_buyer=$buyer_po_arr[$po_id]['buyer_buyer']; else $buyer_buyer.=','.$buyer_po_arr[$po_id]['buyer_buyer'];
			
		}
		$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
		$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
		$buyer_buyer=implode(",",array_unique(explode(",",$buyer_buyer)));
		
		$delivery_status=explode(",",$row[csf('delivery_status')]);
		
		$party_name="";
		if($row[csf("within_group")]==1) $party_name=$comp[$row[csf("party_id")]]; else $party_name=$party_arr[$row[csf("party_id")]];
		
		echo "$('#txtJob_no').val('".$job_arr[$row[csf("order_id")]]."');\n";
		echo "$('#txt_wo_no').val('".$order_no."');\n";
		echo "$('#txtStyleRef').val('".$buyer_style."');\n";
		echo "$('#txtBuyerName').val('".$buyer_buyer."');\n";
		echo "$('#txt_update_details_id').val('".$row[csf("details_id")]."');\n";
		echo "$('#cboshipingStatus').val('".implode(",",$delivery_status)."');\n";
		
 	}
	exit();	
}
if($action=="load_php_dtls_form")
{
	//echo $data;
	$exdata=explode("**",$data);
	//print_r($exdata);
	
	$jobno=''; 
	$update_id=0;
	$update_id=$exdata[0];
	$jobno=$exdata[1];
	$update_details_id=$exdata[3];
	$mstID =$dtlsID ='';
	if($update_details_id!=0 || $update_details_id!='')
	{
		$MsstDtlsID=explode('_',$update_details_id);
		$mstID = $MsstDtlsID[0];
		$dtlsID = $MsstDtlsID[1];
	}
	
	$mstDtlsIdCond = "and e.id in ($dtlsID)";
	$color_arrey=return_library_array( "select id,color_name from lib_color",'id','color_name');
	$size_arrey=return_library_array( "select id,size_name from  lib_size",'id','size_name');
	//echo "10**".$update_id; die; 
	
	if($update_id>0)
	{
		$job_number = $MsstDtlsID[2];
		$embellishment_type_result=sql_select( "select id, mst_id, description, process, embellishment_type, rate,prod_sequence_no from subcon_ord_breakdown where job_no_mst in ('$job_number') order by id");
	}
	else
	{
		//echo  "select id, mst_id, description, process, embellishment_type, rate,prod_sequence_no from subcon_ord_breakdown where job_no_mst='$jobno' order by id"; die;
		$embellishment_type_result=sql_select( "select id, mst_id, description, process, embellishment_type, rate,prod_sequence_no from subcon_ord_breakdown where job_no_mst='$jobno' order by id");
	}
	
	$prod_sequence_no_arr=array();
	foreach ($embellishment_type_result as $row)
	{
		$prod_sequence_no_arr[$row[csf("mst_id")]][$row[csf("prod_sequence_no")]]=$row;
	}
	
	foreach($prod_sequence_no_arr as $mstID=>$dd){
		$keyu=max(array_keys($prod_sequence_no_arr[$mstID]));
		$newArr[$mstID]=$prod_sequence_no_arr[$mstID][$keyu];
	}
	unset($breakdownembellishmenttype);
	//echo "<pre>";
	//print_r($newArr);
	if($update_id>0)
	{
		$job_number = $MsstDtlsID[2];
		$dry_sql_qc="select a.id, b.id as upid, b.qcpass_qty as quantity, b.po_id, a.buyer_po_id,a.recipe_id,b.order_qty,b.process_id,b.wash_type_id from subcon_embel_production_mst a, subcon_embel_production_dtls b where a.id=b.mst_id and a.entry_form=342 and  a.job_no='$job_number' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	}
	else
	{
		$dry_sql_qc="select a.id, b.id as upid, b.qcpass_qty as quantity, b.po_id, a.buyer_po_id,a.recipe_id,b.order_qty,b.process_id,b.wash_type_id from subcon_embel_production_mst a, subcon_embel_production_dtls b where a.id=b.mst_id and a.entry_form=342 and  a.job_no='$jobno' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	}
	
	$drysql_qc_res =sql_select($dry_sql_qc);
	
	foreach ($drysql_qc_res as $row)
	{
		$dryqcdtls_data_arr[$row[csf("po_id")]][$row[csf("process_id")]][$row[csf("wash_type_id")]]['qty']+=$row[csf("quantity")];
	}
	//echo  "select id, mst_id, description, process, embellishment_type, rate,prod_sequence_no from subcon_ord_breakdown where job_no_mst='$jobno'"; die;
	unset($dry_sql_qc);
	
	if($update_id>0)
	{
		$job_number = $MsstDtlsID[2];
		$wet_batch_sql = "select a.id,b.po_id,a.operation_type, c.qcpass_qty as quantity,a.process_id
		from pro_batch_create_mst a, pro_batch_create_dtls b,subcon_embel_production_dtls c ,subcon_embel_production_mst d 
		where a.id=b.mst_id and a.process_id='1'  and  a.status_active=1 and 
		a.entry_form=316 and b.po_id=c.po_id and c.mst_id=d.id  and  d.job_no='$job_number'  and  d.entry_form=301 and a.id=d.recipe_id and a.is_deleted=0 
		group by a.id,b.po_id,a.operation_type,c.qcpass_qty,a.process_id";
		
	}
	else
	{
		$wet_batch_sql = "select a.id,b.po_id,a.operation_type, c.qcpass_qty as quantity,a.process_id
		from pro_batch_create_mst a, pro_batch_create_dtls b,subcon_embel_production_dtls c ,subcon_embel_production_mst d 
		where a.id=b.mst_id and a.process_id='1'  and  a.status_active=1 and 
		a.entry_form=316 and b.po_id=c.po_id and c.mst_id=d.id  and  d.job_no='$jobno'  and  d.entry_form=301 and a.id=d.recipe_id and a.is_deleted=0 
		group by a.id,b.po_id,a.operation_type,c.qcpass_qty,a.process_id";
	}
	
   	$wet_batch_result=sql_select($wet_batch_sql);
	$wetqcdtls_data_arr=array(); 
	foreach ($wet_batch_result as $row)
	{
		
		$wetqcdtls_data_arr[$row[csf("po_id")]][$row[csf("process_id")]][$row[csf("operation_type")]]['qty']+=$row[csf("quantity")];
		$wetqcdtls_data_arr[$row[csf("po_id")]][$row[csf("process_id")]][$row[csf("operation_type")]]['operation_type']=$row[csf("operation_type")];
	}
	//echo "<pre>";
	//print_r($wetqcdtls_data_arr);
	
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
	$qcdtls_data_arr=array();
	if($update_id>0)
	{
	  $sql_del="select a.id, a.mst_id, a.order_id, a.delivery_qty, a.remarks, a.bill_status, a.sort_qty, a.reject_qty from subcon_delivery_dtls a, subcon_delivery_mst b where b.id=a.mst_id and  a.id in ($dtlsID) and b.entry_form=303 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	}
	else
	{
		$sql_del="select a.id, a.mst_id, a.order_id, a.delivery_qty, a.remarks, a.bill_status, a.sort_qty, a.reject_qty from subcon_delivery_dtls a, subcon_delivery_mst b where b.id=a.mst_id and b.job_no='$jobno' and b.entry_form=303 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	}
	
	//echo $update_id;
	$sql_del_res =sql_select($sql_del);
	$updtls_data_arr=array();
	$pre_qty_arr=array();
	
	foreach ($sql_del_res as $row)
	{
		if($update_id>0)
		{
			$updtls_data_arr[$row[csf("order_id")]]['dtlsid']=$row[csf("id")];
			$updtls_data_arr[$row[csf("order_id")]]['qty']+=$row[csf("delivery_qty")];
			$updtls_data_arr[$row[csf("order_id")]]['remarks']=$row[csf("remarks")];
			$updtls_data_arr[$row[csf("order_id")]]['bill_status']=$row[csf("bill_status")];
			$updtls_data_arr[$row[csf("order_id")]]['sort_qty']+=$row[csf("sort_qty")];
			$updtls_data_arr[$row[csf("order_id")]]['reject_qty']+=$row[csf("reject_qty")];
		}
		else
		{
			$pre_qty_arr[$row[csf("order_id")]]['qty']+=$row[csf("delivery_qty")];
		}
	}
	if($update_id>0)
	{
	  $pre_total_del_sql="select a.id, a.mst_id, a.order_id, a.delivery_qty, a.remarks, a.bill_status, a.sort_qty, a.reject_qty from subcon_delivery_dtls a, subcon_delivery_mst b where b.id=a.mst_id and  a.id not in ($dtlsID) and b.entry_form=303 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	  $pre_total_sql_del_res =sql_select($pre_total_del_sql);
		
	}
	
	$pre_total_updtls_data_arr=array();
	foreach ($pre_total_sql_del_res as $row)
	{
		if($update_id>0)
		{
			$pre_total_updtls_data_arr[$row[csf("order_id")]]['dtlsid']=$row[csf("id")];
			$pre_total_updtls_data_arr[$row[csf("order_id")]]['qty']+=$row[csf("delivery_qty")];
			$pre_total_updtls_data_arr[$row[csf("order_id")]]['remarks']=$row[csf("remarks")];
			$pre_total_updtls_data_arr[$row[csf("order_id")]]['bill_status']=$row[csf("bill_status")];
			$pre_total_updtls_data_arr[$row[csf("order_id")]]['sort_qty']+=$row[csf("sort_qty")];
			$pre_total_updtls_data_arr[$row[csf("order_id")]]['reject_qty']+=$row[csf("reject_qty")];
		}
	}
	
	
	//echo "<pre>";
	//print_r($pre_total_updtls_data_arr);
	
	//echo "<pre>";
	//print_r($pre_qty_arr);
	
	unset($sql_del_res);
	
	if($db_type==0) $process_type_cond="group_concat(c.process,'*',c.embellishment_type)";
	else if ($db_type==2) $process_type_cond="listagg(c.process||'*'||c.embellishment_type,',') within group (order by c.process||'*'||c.embellishment_type)";
	
	
		if($update_id>0)
		{
			$sql_job="select a.id, a.company_id, a.within_group, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.buyer_po_no, b.gmts_item_id, 3 as main_process_id, b.order_uom, b.gmts_color_id as color_id, b.gmts_size_id, b.order_quantity as qnty, $process_type_cond as process_type,e.delivery_qty
			from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c,subcon_delivery_mst d,subcon_delivery_dtls e
			where a.entry_form=295 and a.subcon_job=b.job_no_mst  and a.id=b.mst_id and  d.entry_form=303 and d.id=e.mst_id and b.id=e.order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id=c.mst_id $mstDtlsIdCond 
			group by a.id, a.company_id, a.within_group, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, b.buyer_po_no, a.delivery_date, b.id, b.buyer_po_id, b.gmts_item_id, b.order_uom, b.gmts_color_id, b.gmts_size_id, b.order_quantity,e.delivery_qty
			order by b.id ASC";
		}
		else
		{
			$sql_job="select a.id, a.company_id, a.within_group, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.buyer_po_no, b.gmts_item_id, 3 as main_process_id, b.order_uom, b.gmts_color_id as color_id, b.gmts_size_id, b.order_quantity as qnty, $process_type_cond as process_type
				from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c
				where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.subcon_job='$jobno' and b.id=c.mst_id
				group by a.id, a.company_id, a.within_group, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, b.buyer_po_no, a.delivery_date, b.id, b.buyer_po_id, b.gmts_item_id, b.order_uom, b.gmts_color_id, b.gmts_size_id, b.order_quantity
				order by b.id ASC";
		}
			
			
	//echo $sql_job; 
	
	$sql_result =sql_select($sql_job);
	$k=0;
	//$company=$exdata[4];
	$company=$sql_result[0][csf('company_id')];
	$subcon_job=$sql_result[0][csf('subcon_job')];
	$variable_status=return_field_value("variable_dtls","variable_setting_wash","company_name='$company' and variable_list =1 and is_deleted = 0 and status_active = 1");
	if($variable_status==2)
	{
		$sql_rec="select a.id, a.mst_id, a.quantity, a.uom, a.job_dtls_id, a.buyer_po_id, a.remarks from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.embl_job_no='$subcon_job' and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$sql_rec_res =sql_select($sql_rec); $rcv_qty_arr=array();
		foreach ($sql_rec_res as $rows)
		{
			$rcv_qty_arr[$rows[csf("job_dtls_id")]]['qty']+=$rows[csf("quantity")];
		}
		
		 $sql_rec_return="select a.id, a.mst_id, a.quantity, a.uom, a.job_dtls_id, a.buyer_po_id, a.remarks from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.embl_job_no='$subcon_job' and b.entry_form=372 and b.trans_type=3  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$sql_rec_return_res =sql_select($sql_rec_return); $rcv_return_qty_arr=array();
		foreach ($sql_rec_return_res as $rows)
		{
			$rcv_return_qty_arr[$rows[csf("job_dtls_id")]]['qty']+=$rows[csf("quantity")];
		}
	}
	
	
	//echo "<pre>";
	//print_r($rcv_qty_arr); die;
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
		
		$quantity=0; $dtlsup_id=""; $balanceQty=0; $preDelv_qty=0; $orderQty=0; $remarks=""; $bill_status=0; $sort_qty=0; $reject_qty=0;
		
		//echo "";
		//print_r($newArr);
		if($db_type==0)
		{
			if($update_id>0)
			{
				$breakdownid=$newArr[$row[csf("po_id")]]['id'];
				$breakdownmst_id=$newArr[$row[csf("po_id")]]['mst_id'];
				$breakdownprocess=$newArr[$row[csf("po_id")]]['process'];
				$breakdownembellishmenttype=$newArr[$row[csf("po_id")]]['embellishment_type'];
				$breakdownprodsequence_no=$newArr[$row[csf("po_id")]]['prod_sequence_no'];
			}
			else
			{
				$breakdownid=$newArr[$row[csf("po_id")]]['id'];
				$breakdownmst_id=$newArr[$row[csf("po_id")]]['mst_id'];
				$breakdownprocess=$newArr[$row[csf("po_id")]]['process'];
				$breakdownembellishmenttype=$newArr[$row[csf("po_id")]]['embellishment_type'];
				$breakdownprodsequence_no=$newArr[$row[csf("po_id")]]['prod_sequence_no'];
			}
		}
		else if($db_type==2)
		{
			if($update_id>0)
			{
				$breakdownid=$newArr[$row[csf("po_id")]]['ID'];
				$breakdownmst_id=$newArr[$row[csf("po_id")]]['MST_ID'];
				$breakdownprocess=$newArr[$row[csf("po_id")]]['PROCESS'];
				$breakdownembellishmenttype=$newArr[$row[csf("po_id")]]['EMBELLISHMENT_TYPE'];
				$breakdownprodsequence_no=$newArr[$row[csf("po_id")]]['PROD_SEQUENCE_NO'];
			}
			else
			{
				$breakdownid=$newArr[$row[csf("po_id")]]['ID'];
				$breakdownmst_id=$newArr[$row[csf("po_id")]]['MST_ID'];
				$breakdownprocess=$newArr[$row[csf("po_id")]]['PROCESS'];
				$breakdownembellishmenttype=$newArr[$row[csf("po_id")]]['EMBELLISHMENT_TYPE'];
				$breakdownprodsequence_no=$newArr[$row[csf("po_id")]]['PROD_SEQUENCE_NO'];
			}		
			
		}
		//print_r($newArr);
		//echo $breakdownprodsequence_no."==".$breakdownembellishmenttype."==".$breakdownprocess."==".$breakdownmst_id."mahbub";
		
		if($variable_status==2)
		{
			//echo $row[csf("po_id")];
			$qc_qty=$rcv_qty_arr[$row[csf("po_id")]]['qty'];
			$rcv_return_qty=$rcv_return_qty_arr[$row[csf("po_id")]]['qty'];
	
		}
		else
		{
			if($breakdownprocess==2)
			{
				$qc_qty=$dryqcdtls_data_arr[$row[csf("po_id")]][$breakdownprocess][$breakdownembellishmenttype]['qty'];
			}
			else if($breakdownprocess==3)
			{
				$qc_qty=$dryqcdtls_data_arr[$row[csf("po_id")]][$breakdownprocess][$breakdownembellishmenttype]['qty'];
			}
			else if($breakdownprocess==1)
			{
				$firstwash=$wetqcdtls_data_arr[$row[csf("po_id")]][1][1]['operation_type'];
				$fainalwash=$wetqcdtls_data_arr[$row[csf("po_id")]][1][2]['operation_type'];
				$firstdying=$wetqcdtls_data_arr[$row[csf("po_id")]][1][3]['operation_type'];
				$seconddying=$wetqcdtls_data_arr[$row[csf("po_id")]][1][4]['operation_type'];
				
				if($firstwash==1 && $fainalwash==2)
				{
					$qc_qty=$wetqcdtls_data_arr[$row[csf("po_id")]][1][2]['qty'];
				}
				elseif($firstwash==1 && $fainalwash=="")
				{
					$qc_qty=$wetqcdtls_data_arr[$row[csf("po_id")]][1][1]['qty'];
				}
				elseif($firstwash=="" && $fainalwash==2)
				{
					$qc_qty=$wetqcdtls_data_arr[$row[csf("po_id")]][1][2]['qty'];
				}
				elseif($firstdying==3 && $seconddying==4)
				{
					$qc_qty=$wetqcdtls_data_arr[$row[csf("po_id")]][1][4]['qty'];
				}
				elseif($firstdying==3 && $seconddying=="")
				{
					$qc_qty=$wetqcdtls_data_arr[$row[csf("po_id")]][1][3]['qty'];
				}
				elseif($firstdying=="" && $seconddying==4)
				{
					$qc_qty=$wetqcdtls_data_arr[$row[csf("po_id")]][1][4]['qty'];
				}
				
			}
		}
		
		//echo "<pre>";
		//print_r($wetqcdtls_data_arr);
		//echo $firstwash."==".$fainalwash."==".$firstdying."==".$scenddying;
		if($update_id>0)
		{
			//pre_total_updtls_data_arr
			//$currentDelv_qty=$updtls_data_arr[$row[csf("po_id")]]['qty'];
			$preDelv_qty=$pre_total_updtls_data_arr[$row[csf("po_id")]]['qty'];
			///$pre_qty_arr[$row[csf("po_id")]]['qty'];
		}
		else
		{
			$preDelv_qty=$pre_qty_arr[$row[csf("po_id")]]['qty'];
		}		
		//$orderQty=number_format($row[csf("qnty")],0,'.','');
		
		if($variable_status==2)
		{
			//echo $row[csf("po_id")];
			
			$balace_rev_qty=$qc_qty-$rcv_return_qty;
			$balanceQty=$balace_rev_qty-$preDelv_qty;
	
		}
		else
		{
			$balanceQty=$qc_qty-$preDelv_qty;
		}
		
		
		/*if($balanceQty>0)
		{
			$balanceQty=$balanceQty;
		}
		else
		{
			$balanceQty=0;
		}*/
		if($update_id>0)
		{
			//$quantity=$updtls_data_arr[$row[csf("po_id")]]['qty'];
			$quantity=$row[csf("delivery_qty")];
			
		}
		else $quantity=$balanceQty;
		if($quantity==0) $quantity='';
		if($balanceQty==0) $balanceQty='';
		
		if($update_id>0)
		{
			$dtlsup_id=$updtls_data_arr[$row[csf("po_id")]]['dtlsid'];
		}
		else
		{
			$dtlsup_id=$updtls_data_arr[$row[csf("po_id")]]['dtlsid'];
		}
		
		$remarks=$updtls_data_arr[$row[csf("po_id")]]['remarks'];
		$bill_status=$updtls_data_arr[$row[csf("po_id")]]['bill_status'];
		if($bill_status==1) $disable="disabled"; else $disable="";
		
		$sort_qty=$updtls_data_arr[$row[csf("po_id")]]['sort_qty'];
		$reject_qty=$updtls_data_arr[$row[csf("po_id")]]['reject_qty'];
		if($row[csf("order_uom")]==2)
		{
			//$orderQty=number_format($row[csf("qnty")]*12,4,'.','');
			$orderQty=round($row[csf("qnty")]*12);
		}
		else
		{
			//$orderQty=number_format($row[csf("qnty")],4,'.','');
			$orderQty=round($row[csf("qnty")]);
		}
		//echo "<pre>";
		//print_r($newArr);	
		?>
        <tr bgcolor="<? echo $bgcolor; ?>" >
            <td align="center"><? echo $k; ?>
            	<input name="txtbuyerPoId_<? echo $k; ?>" id="txtbuyerPoId_<? echo $k; ?>" type="hidden" class="text_boxes" style="width:70px" value="<? echo $row[csf("buyer_po_id")]; ?>" />
            </td>
            <td style="word-break:break-all"><?  echo $row[csf("buyer_po_no")]; ?></td>
            <td style="word-break:break-all"><? echo $garments_item[$row[csf("gmts_item_id")]]; ?></td>
            <td style="word-break:break-all"><? echo $process_name; ?></td>
            <td style="word-break:break-all"><? echo $sub_process_name; ?></td>
            <td style="word-break:break-all"><? echo $color_arrey[$row[csf("color_id")]]; ?></td>
            <td style="word-break:break-all"><? echo $size_arrey[$row[csf("gmts_size_id")]]; ?></td>
            <td align="right"><? echo $orderQty; ?>&nbsp;</td>
            <td align="right"><? echo $qc_qty; ?>&nbsp;</td>
			<? if($variable_status==2) {?><td align="right"><? echo $rcv_return_qty; ?>&nbsp;</td><? }?>
            <td align="right"><? echo $preDelv_qty; ?>&nbsp;</td>
            <td align="right"><? echo $balanceQty; ?>&nbsp;</td>
            <td title="<? if($bill_status==1) echo "Already Bill Issued."; else echo "";?>"><input type="text" name="txtCurrDelv_<? echo $k; ?>" id="txtCurrDelv_<? echo $k; ?>" class="text_boxes_numeric" style="width:55px;" value="<? echo $quantity; ?>" onBlur="fnc_production_qty_ability(this.value,<? echo $k; ?>); fnc_total_calculate();" placeholder="<? echo $balanceQty; ?>" pre_delv_qty="<? echo $preDelv_qty; ?>" delv_qty="<? echo $quantity; ?>" <? echo $disable; ?> /></td>
            <td align="center"><input type="text" name="txtsort_<? echo $k; ?>" id="txtsort_<? echo $k; ?>" class="text_boxes_numeric" style="width:50px;" value="<? echo $sort_qty; ?>" onBlur="fnc_total_calculate();" /></td>
            <td align="center"><input type="text" name="txtreject_<? echo $k; ?>" id="txtreject_<? echo $k; ?>" class="text_boxes_numeric" style="width:50px;" value="<? echo $reject_qty; ?>" onBlur="fnc_total_calculate();" /></td>
            <td><input type="text" name="txtRemarks_<? echo $k; ?>" id="txtRemarks_<? echo $k; ?>" class="text_boxes" style="width:60px;" value="<? echo $remarks; ?>" />
                <input type="hidden" name="txtDtlsUpdateId_<? echo $k; ?>" id="txtDtlsUpdateId_<? echo $k; ?>" style="width:80px" class="text_boxes" value="<? echo $dtlsup_id; ?>" />
                <input type="hidden" name="txtColorSizeid_<? echo $k; ?>" id="txtColorSizeid_<? echo $k; ?>" style="width:80px" class="text_boxes" value="<? //echo $row[csf("breakdown_id")]; ?>" />
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
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die;}
		if(str_replace("'","",$txt_update_id)=="")
		{
			$id=return_next_id("id", "subcon_delivery_mst", 1);

			if($db_type==2) $mrr_cond="and TO_CHAR(insert_date,'YYYY')=".date('Y',time()); else if($db_type==0) $mrr_cond="and year(insert_date)=".date('Y',time());
			$new_sys_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'WD', date("Y",time()), 5, "select delivery_prefix, delivery_prefix_num from subcon_delivery_mst where company_id=$cbo_company_name and entry_form=303 $mrr_cond order by id DESC ", "delivery_prefix", "delivery_prefix_num" ));
			
			$field_array_mst="id, delivery_prefix, delivery_prefix_num, delivery_no, company_id, location_id, within_group, party_id, party_location, deli_party, deli_party_location, delivery_date, remarks, job_no,variable_status, entry_form, inserted_by, insert_date, status_active, is_deleted";
			
			$data_array_mst="(".$id.",'".$new_sys_number[1]."','".$new_sys_number[2]."','".$new_sys_number[0]."',".$cbo_company_name.",".$cbo_location_name.",".$cbo_within_group.",".$cbo_party_name.",".$cbo_party_location.",".$cbo_deli_party_name.",".$cbo_deli_party_location.",".$txt_delivery_date.",".$txt_remarks.",".$txtJob_no.",".$txt_variable_status.",303,".$user_id.",'".$pc_date_time."',1,0)";
			
			$mrr_no=$new_sys_number[0];
		}
		else
		{
			$id=str_replace("'","",$txt_update_id);
			$mrr_no=str_replace("'","",$txt_delv_no);
			
			$field_array_update_mst="location_id*within_group*party_id*party_location*deli_party*deli_party_location*delivery_date*remarks*job_no*updated_by*update_date";
			$data_array_update_mst="".$cbo_location_name."*".$cbo_within_group."*".$cbo_party_name."*".$cbo_party_location."*".$cbo_deli_party_name."*".$cbo_deli_party_location."*".$txt_delivery_date."*".$txt_remarks."*".$txtJob_no."*".$user_id."*'".$pc_date_time."'";
		}
		
		$data_arr_dtls="";
		$id_dtls=return_next_id("id", "subcon_delivery_dtls", 1); $po_wise_qty_arr=array(); $color_size_arr=array();
		$field_array_dtls="id, mst_id, buyer_po_id, order_id, remarks, color_size_id, delivery_qty, sort_qty, reject_qty, inserted_by, insert_date, status_active, is_deleted";
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
			 
			if($data_arr_dtls!="") $data_arr_dtls.=",";
			$data_arr_dtls.="(".$id_dtls.",".$id.",".$$txtbuyerPoId.",".$$txtpoid.",".$$txtRemarks.",".$$txtColorSizeid.",".$$txtCurrDelv.",".$$txtsort.",".$$txtreject.",'".$user_id."','".$pc_date_time."',1,0)"; 
			$po_wise_qty_arr[str_replace("'","",$$txtpoid)]+=str_replace("'","",$$txtCurrDelv);
			$color_size_arr[str_replace("'","",$$txtColorSizeid)]=str_replace("'","",$$txtCurrDelv);
			
			$id_dtls=$id_dtls+1;
		}
		
		/*$color_size_qty_arr=array();
		$color_size_qty_sql=sql_select("select count(id) as cid, mst_id, sum(qnty) as qnty from subcon_ord_breakdown where job_no_mst=$txtJob_no and delivery_status<>3 group by mst_id");
		foreach ($color_size_qty_sql as $row)
		{
			$color_size_qty_arr[$row[csf("mst_id")]]['qty']=$row[csf("qnty")]*12;
			$color_size_qty_arr[$row[csf("mst_id")]]['cid']=$row[csf("cid")];
		}
		unset($color_size_qty_sql);*/
		
		$delivery_qty_arr=array();
		$delivery_qty_sql=sql_select("select b.delivery_qty, b.order_id from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.id!=$id and a.entry_form=303 and a.job_no=$txtJob_no");
		foreach ($delivery_qty_sql as $row)
		{
			$delivery_qty_arr[$row[csf("order_id")]]+=$row[csf("delivery_qty")];
		}
		unset($delivery_qty_sql);
		$flag=1;
		$order_status=str_replace("'","",$cboshipingStatus);
		
		//echo "10**".$id_dtls."**".$QcPassQtyArr[$$emblProdDtlsId];    disconnect($con); die;
		//echo "10**INSERT INTO subcon_delivery_mst (".$field_array_mst.") VALUES ".$data_array_mst;    disconnect($con); die;
		
		//echo "10**$total_row**INSERT INTO subcon_delivery_dtls (".$field_array_dtls.") VALUES ".$data_arr_dtls;    disconnect($con); die;
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
			
			/*$sts_cs = execute_query("update subcon_ord_breakdown set delivery_status=$order_status where mst_id=$po_id and delivery_status<>3",1);
			if($sts_cs==1 && $flag==1) $flag=1; else $flag=0;*/
			
			$sts_mst = execute_query("update subcon_delivery_dtls set delivery_status=$order_status where order_id=$po_id",1);
			if($sts_mst==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		//echo "10**$rID**$rID1**$sts_po**$sts_mst**$flag"; disconnect($con); die;
		
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
		$mrr_no=str_replace("'","",$txt_delv_no);
			
		$field_array_update_mst="location_id*within_group*party_id*party_location*deli_party*deli_party_location*delivery_date*remarks*job_no*variable_status*updated_by*update_date";
		$data_array_update_mst="".$cbo_location_name."*".$cbo_within_group."*".$cbo_party_name."*".$cbo_party_location."*".$cbo_deli_party_name."*".$cbo_deli_party_location."*".$txt_delivery_date."*".$txt_remarks."*".$txtJob_no."*".$txt_variable_status."*".$user_id."*'".$pc_date_time."'";
		
		$data_arr_dtls="";
		$id_dtls=return_next_id("id", "subcon_delivery_dtls", 1);
		 $po_wise_qty_arr=array();
		  $color_size_arr=array();
		  
		$field_array_dtls="id, mst_id, buyer_po_id, order_id, remarks, color_size_id, delivery_qty, sort_qty, reject_qty, inserted_by, insert_date, status_active, is_deleted";
		
		$field_arr_up="buyer_po_id*order_id*remarks*color_size_id*delivery_qty*sort_qty*reject_qty*updated_by*update_date";
		
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
			
			//echo "10**".str_replace("'","",$$txtDtlsUpdateId);
			
			if(str_replace("'","",$$txtDtlsUpdateId)=="")
			{
				if($data_arr_dtls!="") $data_arr_dtls.=","; 	
				$data_arr_dtls.="(".$id_dtls.",".$id.",".$$txtbuyerPoId.",".$$txtpoid.",".$$txtRemarks.",".$$txtColorSizeid.",".$$txtCurrDelv.",".$$txtsort.",".$$txtreject.",'".$user_id."','".$pc_date_time."',1,0)"; 
				
				$id_dtls=$id_dtls+1;
			}
			else if(str_replace("'","",$$txtDtlsUpdateId)!="")
			{
				$data_arr_up[str_replace("'","",$$txtDtlsUpdateId)]=explode("*",("".$$txtbuyerPoId."*".$$txtpoid."*".$$txtRemarks."*".$$txtColorSizeid."*".$$txtCurrDelv."*".$$txtsort."*".$$txtreject."*".$user_id."*'".$pc_date_time."'"));
				$id_arr_delv[]=str_replace("'","",$$txtDtlsUpdateId);
				$hdn_break_id_arr[]=str_replace("'","",$$txtDtlsUpdateId);
			}
		}
		
		/*$color_size_qty_arr=array();
		$color_size_qty_sql=sql_select("select count(id) as cid, mst_id, sum(qnty) as qnty from subcon_ord_breakdown where job_no_mst=$txtJob_no and delivery_status<>3 group by mst_id");
		foreach ($color_size_qty_sql as $row)
		{
			$color_size_qty_arr[$row[csf("mst_id")]]['qty']=$row[csf("qnty")]*12;
			$color_size_qty_arr[$row[csf("mst_id")]]['cid']=$row[csf("cid")];
		}
		unset($color_size_qty_sql);*/
		
		$delivery_qty_arr=array();
		$delivery_qty_sql=sql_select("select b.delivery_qty, b.order_id from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.id!=$txt_update_id and a.entry_form=303 and a.job_no=$txtJob_no");
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
		//echo "10**".bulk_update_sql_statement("subcon_delivery_dtls", "id", $field_arr_up, $data_arr_up, $id_arr_delv); disconnect($con); die;
		if($data_arr_up!=""){
			$rID1=execute_query(bulk_update_sql_statement( "subcon_delivery_dtls", "id", $field_arr_up, $data_arr_up, $id_arr_delv));
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if($data_arr_dtls !=""){
			//echo "10**insert into subcon_delivery_dtls ($field_array_dtls) values $data_array_dtls "; disconnect($con); die;
			$rID2=sql_insert("subcon_delivery_dtls",$field_array_dtls,$data_arr_dtls,1);
			if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		//print_r($hdn_break_id_arr);
		$distancedeleteid=implode(",",$hdn_break_id_arr);
		 $del_sql_dtls="Select id, bill_status from subcon_delivery_dtls where id in ($distancedeleteid) and status_active=1 and is_deleted=0";//
		$all_dtls_id_arr=array();
		//echo "10**".$del_sql_dtls; disconnect($con); die;
		$nameArray=sql_select( $del_sql_dtls ); 
		foreach($nameArray as $row)
		{
			$all_dtls_id_arr[]=$row[csf('id')];
		}
		unset($nameArray);
		
		
		
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
			$field_array_del="status_active*is_deleted*updated_by*update_date";
			$data_array_del="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$ex_delete_id=explode(",",$distance_delete_id);
			
			//echo "10**".bulk_update_sql_statement("subcon_delivery_dtls", "id", $field_array_del, $data_array_del, $ex_delete_id); disconnect($con); die;
			
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
			
			/*$sts_cs = execute_query("update subcon_ord_breakdown set delivery_status=$order_status where mst_id=$po_id and delivery_status<>3",1);
			if($sts_cs==1 && $flag==1) $flag=1; else $flag=0;*/
			
			$sts_mst = execute_query("update subcon_delivery_dtls set delivery_status=$order_status where order_id=$po_id",1);
			if($sts_mst==1 && $flag==1) $flag=1; else $flag=0;
		}
		//10**1 **      **1    **1   **       **          **1
		//echo "10**$rID**$rID1**$rID2**$rID3**$sts_po**$sts_mst**$flag"; disconnect($con); die;
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
	else if ($operation==2)  // Delete Here---------------------------------------------------------- 
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$del_sql_dtls="Select id, bill_status from subcon_delivery_dtls where mst_id=$txt_update_id and bill_status=1 and status_active=1 and is_deleted=0";//
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
	$load_function = '';

	if($within_group==1) {
		$load_function="fnc_load_party(2, $within_group)";
	}
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
	<body>
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
								echo create_drop_down( "cbo_within_group", 50, $yes_no,"", 1, "-- Select --",$within_group, "load_drop_down( 'wash_delivery_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer_pop', 'buyer_td' );" ); ?>
							</td>
                            <td id="buyer_td">
								<?php
									if ($within_group == 1) {
										echo create_drop_down( "cbo_party_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $party_name, "load_drop_down( 'requires/wash_delivery_controller', this.value+'_'+2, 'load_drop_down_location', 'party_location_td'); location_select(); $load_function;");
									} else {
										echo create_drop_down( "cbo_party_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond  and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $party_name, "" );
									}
									
								// echo create_drop_down( "cbo_party_name", 120, $blank_array,"", 1, "-- Select Party --", $selected, "" );
								?>
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
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value, 'create_delivery_search_list_view', 'search_div', 'wash_delivery_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
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
	
	$sql= "select a.id, a.delivery_no, a.delivery_prefix_num, $insert_date_cond as year, a.location_id, a.within_group, a.party_id, a.party_location, a.deli_party, a.deli_party_location, a.remarks, a.delivery_date, a.job_no, $wo_cond as order_id, $buyer_po_id_cond as buyer_po_id, $delivery_status_cond as delivery_status from subcon_delivery_mst a, subcon_delivery_dtls b 
	
	where a.id=b.mst_id and a.entry_form='303' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $delivery_date $company $buyer_cond $withinGroup $delivery_id_cond $spo_idsCond $po_idsCond 
	
	group by a.id, a.delivery_no, a.delivery_prefix_num, a.insert_date, a.location_id, a.within_group, a.party_id, a.party_location, a.deli_party, a.deli_party_location, a.remarks, a.delivery_date, a.job_no order by a.id DESC ";
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
				
				/*$buyer_po=""; $buyer_style="";
				$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
				foreach($buyer_po_id as $po_id)
				{
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
				}
				$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));*/
				
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

/*if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id, delivery_no, company_id, location_id, within_group, party_id, delivery_date, job_no, remarks from subcon_delivery_mst where id='$data'" ); 
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_delv_no').value 		= '".$row[csf("delivery_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n"; 
		
		echo "document.getElementById('cbo_within_group').value		= '".$row[csf("within_group")]."';\n"; 
		echo "load_drop_down( 'requires/wash_delivery_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";
		
		echo "load_drop_down( 'requires/wash_delivery_controller', document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer', 'buyer_td' );\n"; 		
		
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
*/




if($action=="embl_delivery_entry_print")
{
	extract($_REQUEST);
	list($company_id, $update_id, $job_no, $report_title) = explode("*", $data);

	$company_library= return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library  = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$location_arr   = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$color_arr      = return_library_array("select id, color_name from lib_color", 'id', 'color_name');	
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');	
	
	/*if($db_type==0)
	{
		$id_cond="group_concat(b.id)";
		$ins_year_cond="year(a.insert_date)";
		$color_id_str="group_concat(b.gmts_color_id)";
		$buyer_po_id_cond="group_concat(distinct(b.buyer_po_id))";
		$buyer_buyer_cond="group_concat(distinct(b.buyer_buyer))";
		$buyer_po_no_cond="group_concat(distinct(b.buyer_po_no))";
		$buyer_style_cond="group_concat(distinct(b.buyer_style_ref))";
	}
	else if($db_type==2)
	{
		$id_cond="listagg(b.id,',') within group (order by b.id)";
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str="listagg(b.gmts_color_id,',') within group (order by b.gmts_color_id)";
		$buyer_po_id_cond="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
		$buyer_buyer_cond="listagg(b.buyer_buyer,',') within group (order by b.buyer_buyer)";
		
		$buyer_po_no_cond="listagg(b.buyer_po_no,',') within group (order by b.buyer_po_no)";
		$buyer_style_cond="listagg(b.buyer_style_ref,',') within group (order by b.buyer_style_ref)";
		$buyer_po=implode(",",array_unique(explode(",",$row[csf('buyer_po_no')])));
		//$buyer_style=implode(",",array_unique(explode(",",$row[csf('buyer_style')])));
		//$buyer_name=implode(",",array_unique(explode(",",$row[csf('buyer_buyer')])));
		
	}*/
	
	
	
	
	
	$sql_main="SELECT a.id, a.company_id, a.delivery_no, a.location_id, a.within_group, a.party_id, a.delivery_date, a.job_no, a.remarks, b.delivery_qty, b.order_id, b.remarks as dtls_remarks, d.subcon_job, d.order_no, c.buyer_style_ref,c.party_buyer_name as buyer_buyer, c.gmts_item_id, c.gmts_color_id as color_id, c.buyer_po_id, c.buyer_po_no
		from subcon_delivery_mst a, subcon_delivery_dtls b, subcon_ord_dtls c, subcon_ord_mst d
		where a.id=b.mst_id and b.order_id=c.id and c.mst_id=d.id and a.entry_form=303 and a.id='$update_id' and d.subcon_job='$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$dataArray = sql_select($sql_main);
	
	
	
	
	
	foreach ($dataArray as $val)
	{
		$order_id    .= $val[csf('order_id')].',';
		$buyer_po_id .= $val[csf('buyer_po_id')].',';
	}
	$order_ids    = chop($order_id,',');
	$buyer_po_ids = chop($buyer_po_id,',');

	
	if ($buyer_po_ids != 0)
	{		
		$po_sql ="SELECT a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where b.id in('$buyer_po_ids') and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$po_sql_res=sql_select($po_sql);
		$buyer_po_arr = array();
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style_ref_no'] = $row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po_number']    = $row[csf("po_number")];
		}
	}	


	if($db_type==0) $process_type_cond="group_concat(c.process,'*',c.embellishment_type)";
	else if ($db_type==2) $process_type_cond="listagg(c.process||'*'||c.embellishment_type,',') within group (order by c.process||'*'||c.embellishment_type)";

	if ($order_ids != "")
	{
		$sql_process = "select c.mst_id as order_id, $process_type_cond as process_type from subcon_ord_breakdown c where c.mst_id in($order_ids) group by c.mst_id";
		$sql_process_res = sql_select($sql_process);
		$process_wash_type_arr = array();
		foreach ($sql_process_res as $val)
		{
			$process_wash_type_arr[$val[csf('order_id')]]['process_type'] = $val[csf('process_type')];
		}
	}


	if ($dataArray[0][csf('within_group')] == 1)  // within group yes
	{
		$party_name      = $company_library[$dataArray[0][csf('party_id')]];
		$order_no        = $buyer_po_arr[$dataArray[0][csf('buyer_po_id')]]['po_number'];
		$buyer_style_ref = $buyer_po_arr[$dataArray[0][csf('buyer_po_id')]]['style_ref_no'];
	} 
	else 
	{
		$party_name      = $buyer_library[$dataArray[0][csf('party_id')]];
		$order_no        = $dataArray[0][csf('order_no')];
		$buyer_style_ref = $dataArray[0][csf('buyer_style_ref')];
	}
	
	?>
	<style type="text/css">
		table,tr,td,th{font-size: 18px;}		
	</style>
    <div style="width:1100px; font-size:20px">
        <table width="100%" cellpadding="1" cellspacing="1">
            <tr>
                <td width="70" align="right"> 
                    <img  src='../../<? echo $imge_arr[$company_id]; ?>' height='100%' width='100%'/>
                </td>
                <td>
                    <table width="800" cellspacing="0" align="center">
                        <tr>
                            <td align="center" style="font-size:x-large;"><strong ><? echo $company_library[$company_id]; ?></strong></td>
                        </tr>
                        <tr>
                            <td align="center"  style="font-size:25"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                        </tr>
                        <tr class="form_caption">
                            <td  align="center" style="font-size:25px">  
                                <? echo show_company($company_id,'',''); ?>
                            </td>  
                        </tr>
                        <tr>
                            <td align="center" style="font-size:x-large;"><strong><? echo $report_title.' Challan'; ?></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table width="100%" cellpadding="1" cellspacing="1">  
            <tr>
            	<td width="80"><strong>Company</strong></td>
            	<td width="20"><strong>:</strong></td>
                <td width="220"><? echo $company_library[$dataArray[0][csf('company_id')]]; ?></td>
            	<td width="80"><strong>Party</strong></td>
            	<td width="20"><strong>:</strong></td>
                <td width="220"><? echo $party_name; ?></td>
                <td width="120"><strong>Delivery Date</strong></td>
            	<td width="20"><strong>:</strong></td>
                <td width="220"><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>               
            </tr>
            <tr>
            	<td width="80"><strong>Job No</strong></td>
            	<td width="20"><strong>:</strong></td>
                <td width="220"><? echo $job_no; ?></td>
            	<td width="80"><strong>WO No</strong></td>
            	<td width="20"><strong>:</strong></td>
            	<td width="220"><? echo $order_no; ?></td>
                 <td width="120"><strong>Delivery ID</strong></td>
            	<td width="20"><strong>:</strong></td>
                <td width="220"><? echo $dataArray[0][csf('delivery_no')]; ?></td>
            </tr>
            <tr>
            	<td width="80"><strong>Remarks</strong></td>
            	<td width="20"><strong>:</strong></td>
            	<td width="220"><? echo $dataArray[0][csf('remarks')]; ?></td>
                <td width="80"></td>
            	<td width="20"></td>
                <td width="220"></td>
                <td width="120"></td>
            	<td width="20"></td>
                <td width="220"></td>
            </tr>
        </table>
        <br>
        <div style="width:100%;">
            <table align="right" cellspacing="1" cellpadding="1" width="1100" border="1" rules="all" class="rpt_table">
                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
                    <th width="50">SL</th>
                    <th width="130">Buyer PO</th>
                    <th width="130">Buyer Style Ref</th>
                    <th width="130">Buyer Name</th>
                    <th width="150">Gmts Item</th>
                    <th width="150">Process Name</th>
                    <th width="230">Wash Type</th>
                    <th width="100">Color</th>
                    <th width="50">Qty(Pcs)</th>
                    <th width="170">Remarks</th>
                </thead>
				<?
 				$i=1; $tot_delivery_qty=0;
				foreach ($dataArray as $row) 
				{

					$ex_process=array_unique(explode(",",$process_wash_type_arr[$row[csf('order_id')]]['process_type']));
					$process_name=$sub_process_name="";
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

					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td width="50"><p><? echo $i; ?></p></td>
                        <td width="130"><p><? echo $row[csf('buyer_po_no')]; ?></p></td>
                        <td width="130"><p><? echo $row[csf('buyer_style_ref')]; ?></p></td>
                        <td width="130"><p><? echo $row[csf('buyer_buyer')]; ?></p></td>
                        <td width="150"><p><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></p></td>
                        <td width="150"><p><? echo $process_name; ?></p></td>
                        <td width="230"><p><? echo $sub_process_name; ?></p></td>
                        <td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                        <td width="50" align="right"><p><? echo $row[csf('delivery_qty')]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('dtls_remarks')]; ?></p></td>
                    </tr>
					<?
					$i++;
					$tot_delivery_qty += $row[csf('delivery_qty')];
				}
				?>
				<tr bgcolor="#ddd">
					<th colspan="8" align="right"><strong>Total:</strong></th>
					<th align="right"><? echo $tot_delivery_qty; ?></th>
					<th></th>
				</tr>
            </table>	         
            <br>
			<? echo signature_table(181, $company_id, "900px"); ?>
        </div>
    </div>
	<?
	exit();
}


if($action=="embl_delivery_entry_print2")
{
	extract($_REQUEST);
	list($company_id, $update_id, $job_no, $report_title) = explode("*", $data);

	$company_library= return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library  = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$buyer_address  = return_library_array("select id, address_1 from lib_buyer", "id", "address_1");
	$location_arr   = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$color_arr      = return_library_array("select id, color_name from lib_color", 'id', 'color_name');	
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');

	
	/*if($db_type==0)
	{
		$id_cond="group_concat(b.id)";
		$ins_year_cond="year(a.insert_date)";
		$color_id_str="group_concat(b.gmts_color_id)";
		$buyer_po_id_cond="group_concat(distinct(b.buyer_po_id))";
		$buyer_buyer_cond="group_concat(distinct(b.buyer_buyer))";
		$buyer_po_no_cond="group_concat(distinct(b.buyer_po_no))";
		$buyer_style_cond="group_concat(distinct(b.buyer_style_ref))";
	}
	else if($db_type==2)
	{
		$id_cond="listagg(b.id,',') within group (order by b.id)";
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str="listagg(b.gmts_color_id,',') within group (order by b.gmts_color_id)";
		$buyer_po_id_cond="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
		$buyer_buyer_cond="listagg(b.buyer_buyer,',') within group (order by b.buyer_buyer)";
		
		$buyer_po_no_cond="listagg(b.buyer_po_no,',') within group (order by b.buyer_po_no)";
		$buyer_style_cond="listagg(b.buyer_style_ref,',') within group (order by b.buyer_style_ref)";
		$buyer_po=implode(",",array_unique(explode(",",$row[csf('buyer_po_no')])));
		//$buyer_style=implode(",",array_unique(explode(",",$row[csf('buyer_style')])));
		//$buyer_name=implode(",",array_unique(explode(",",$row[csf('buyer_buyer')])));
		
	}*/
	
	
	
	
	
	$sql_main="SELECT a.id, a.company_id, a.delivery_no, a.location_id, a.party_location, a.within_group, a.party_id, a.delivery_date, a.job_no, a.remarks, b.delivery_qty, b.order_id, b.remarks as dtls_remarks, d.subcon_job, d.order_no, c.buyer_style_ref,c.party_buyer_name as buyer_buyer, c.gmts_item_id, c.gmts_color_id as color_id, c.buyer_po_id, c.buyer_po_no
		from subcon_delivery_mst a, subcon_delivery_dtls b, subcon_ord_dtls c, subcon_ord_mst d
		where a.id=b.mst_id and b.order_id=c.id and c.mst_id=d.id and a.entry_form=303 and a.id='$update_id' and d.subcon_job='$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$dataArray = sql_select($sql_main);
	
	
	
	
	
	foreach ($dataArray as $val)
	{
		$order_id    .= $val[csf('order_id')].',';
		$buyer_po_id .= $val[csf('buyer_po_id')].',';
	}
	$order_ids    = chop($order_id,',');
	$buyer_po_ids = chop($buyer_po_id,',');

	
	if ($buyer_po_ids != 0)
	{		
		$po_sql ="SELECT a.style_ref_no, b.id, b.po_number, a.currency_id from wo_po_details_master a, wo_po_break_down b where b.id in('$buyer_po_ids') and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$po_sql_res=sql_select($po_sql);
		$buyer_po_arr = array();
		$currency_id = '';
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style_ref_no'] 	= $row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po_number']    	= $row[csf("po_number")];
			$currency_id    = $row[csf("currency_id")];
		}
	}	


	if($db_type==0) $process_type_cond="group_concat(c.process,'*',c.embellishment_type)";
	else if ($db_type==2) $process_type_cond="listagg(c.process||'*'||c.embellishment_type,',') within group (order by c.process||'*'||c.embellishment_type)";

	if ($order_ids != "")
	{
		$sql_process = "select c.mst_id as order_id, $process_type_cond as process_type from subcon_ord_breakdown c where c.mst_id in($order_ids) group by c.mst_id";
		$sql_process_res = sql_select($sql_process);
		$process_wash_type_arr = array();
		foreach ($sql_process_res as $val)
		{
			$process_wash_type_arr[$val[csf('order_id')]]['process_type'] = $val[csf('process_type')];
		}
	}


	if ($dataArray[0][csf('within_group')] == 1)  // within group yes
	{
		$party_name      = $company_library[$dataArray[0][csf('party_id')]];
		$party_address   = $location_arr[$dataArray[0][csf('party_location')]];
		$order_no        = $buyer_po_arr[$dataArray[0][csf('buyer_po_id')]]['po_number'];
		$buyer_style_ref = $buyer_po_arr[$dataArray[0][csf('buyer_po_id')]]['style_ref_no'];
	} 
	else 
	{
		$party_name      = $buyer_library[$dataArray[0][csf('party_id')]];
		$party_address   = $buyer_address[$dataArray[0][csf('party_id')]];
		$order_no        = $dataArray[0][csf('order_no')];
		$buyer_style_ref = $dataArray[0][csf('buyer_style_ref')];
	}
	$copyCount = 1;
	?>
	<style type="text/css">
		table,tr,td,th{font-size: 18px;}		
	</style>
	<?php
		for($copyCount; $copyCount <= 3; $copyCount++) {
	?>
	    <div style="width:1000px; font-size:20px">
	        <table width="100%" cellpadding="1" cellspacing="1">
	            <tr>
	                <td width="70" align="right"> 
	                    <img  src='../../<? echo $imge_arr[$company_id]; ?>' height='100%' width='100%'/>
	                </td>
	                <td>
	                    <table width="1000" cellspacing="0" align="center">
	                    	<tr>
	                    		<td align="right">
	                    			<strong>
		                    			<?php 
		                    				if ($copyCount==1) {
		                    					echo "Original Copy";
		                    				} else if($copyCount==2) {
		                    					echo "Customer Copy";
		                    				} else {
		                    					echo "Gate Copy";
		                    				}
		                    			?>
	                    			</strong>
	                    		</td>
	                    	</tr>
	                        <tr>
	                            <td align="center" style="font-size:x-large;"><strong><? echo $company_library[$company_id]; ?></strong></td>
	                        </tr>
	                        
	                        <tr class="form_caption">
	                            <td  align="center"><strong><?
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
						foreach ($nameArray as $result)
						{
						?>
							 <? echo $result[csf('plot_no')]; ?>
							 <? if($result[csf('level_no')]!="") echo ",".$result[csf('level_no')]?>
							 <? if($result[csf('road_no')]!="") echo ",".$result[csf('road_no')]; ?>
							 <? if($result[csf('block_no')]!="") echo ",".$result[csf('block_no')];?>
							 <? if($result[csf('city')]!="") echo ",".$result[csf('city')];?>
							 <? if($result[csf('zip_code')]!="") echo ",".$result[csf('zip_code')]; ?>
							 <? if($result[csf('province')]!="") echo ",".$result[csf('province')];?>
							 <? if($result[csf('country_id')]!="") echo ",".$country_arr[$result[csf('country_id')]]; ?><br>
							 Email:<? if($result[csf('email')]!="") echo $result[csf('email')].",";?>
							 Website:<? if($result[csf('website')]!="") echo $result[csf('website')];


						}
	                ?> </strong>
	                                <? //echo show_company($company_id,'',''); ?>
	                            </td>  
	                        </tr>
	                        <tr>
	                            <td align="center" style="font-size:x-large;"><strong><? echo $report_title.' Challan'; ?></strong></td>
	                        </tr>
	                    </table>
	                </td>
	            </tr>
	        </table>
	        <table width="100%" cellpadding="1" cellspacing="1">  
	            <tr>
	            	<td width="80"><strong>Customer</strong></td>
	            	<td width="20"><strong>:</strong></td>
	                <td width="220"><? echo $party_name; ?></td>
	                <td width="120"><strong>Delivery Date</strong></td>
	                <td width="20"><strong>:</strong></td>
	                <td width="220"><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td> 
	                <td width="120"><strong>Delivery ID</strong></td>
	                <td width="20"><strong>:</strong></td>
	                <td width="220"><? echo $dataArray[0][csf('delivery_no')]; ?></td>              
	            </tr>
	            <tr>
	            	<td width="80"><strong>Buyer</strong></td>
	            	<td width="20"><strong>:</strong></td>
	                <td width="220"><? echo $dataArray[0][csf('buyer_buyer')]; ?></td>
	            	<td width="80"><strong>Job No</strong></td>
	            	<td width="20"><strong>:</strong></td>
	                <td width="220"><? echo $job_no; ?></td>  
	            	<td width="80"><strong>Address</strong></td>
	            	<td width="20"><strong>:</strong></td>
	            	<td width="220"><? echo $party_address; ?></td>
	                 
	            </tr>
	            <tr>
	            	<td width="80"><strong>Remarks</strong></td>
	            	<td width="20"><strong>:</strong></td>
	            	<td width="220"><? echo $dataArray[0][csf('remarks')]; ?></td>
	                <td width="80"></td>
	            	<td width="20"></td>
	                <td width="220"></td>
	                <td width="120"></td>
	            	<td width="20"></td>
	                <td width="220"></td>
	            </tr>
	            
	        </table>
	        <br>
	        <div style="width:100%;">
	            <table align="right" cellspacing="1" cellpadding="1" width="1000" border="1" rules="all" class="rpt_table">
	                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
	                    <th width="50">SL</th>
	                    
	                    <th width="150">Style</th>
	                    
	                    <th width="220">Item Description</th>
	                    
	                    <th width="120">Color</th>
	                    <th width="80">UOM</th>
	                    <th width="80">Qty</th>
	                    <th width="220">Remarks</th>
	                </thead>
					<?
	 				$i=1; $tot_delivery_qty=0;
					foreach ($dataArray as $row) 
					{

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>">
	                        <td width="50"><p><? echo $i; ?></p></td>
	                        
	                        <td width="150"><p><? echo $row[csf('buyer_style_ref')]; ?></p></td>
	                        
	                        <td width="220"><p><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></p></td>
	                        <td width="120"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
	                        <td width="80" align="right"><p><? echo "Pcs"; ?></p></td>
	                        <td width="80" align="right"><p><? echo $row[csf('delivery_qty')]; ?></p></td>
	                        <td width="220"><p><? echo $row[csf('dtls_remarks')]; ?></p></td>
	                    </tr>
						<?
						$i++;
						$tot_delivery_qty += $row[csf('delivery_qty')];
					}
					?>
					<tr bgcolor="#ddd">
						<th colspan="5" align="right"><strong>Total:</strong></th>
						<th align="right"><? echo $tot_delivery_qty; ?></th>
						<th></th>
					</tr>
					<tr>
						<td colspan="2" align="right"><strong>Total in word: </strong></td>
						<td colspan="5" align="left"><strong><? echo number_to_words($tot_delivery_qty); ?> Pcs</td>
					</tr>
	            </table>	         
	            <br>
				
				<div>
					<? echo signature_table(181, $company_id, "1000px"); ?>
				</div>
	        </div>
	    </div>
	    <p style="page-break-after:always;"></p>
	<?
		}
	exit();
}


if($action="default_html")
{
	?>
	<tr bgcolor="#FFFFFF">
		<td align="center">1<input name="txtbuyerPoId_1" id="txtbuyerPoId_1" type="hidden" class="text_boxes" style="width:70px" value="" /></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align="center"><input type="text" name="txtCurrDelv_1" id="txtCurrDelv_1" class="text_boxes_numeric" style="width:45px;" onBlur="fnc_production_qty_ability(this.value,1); fnc_total_calculate();" /></td>
		<td align="center"><input type="text" name="txtsort_1" id="txtsort_1" class="text_boxes_numeric" style="width:40px;" onBlur="fnc_total_calculate();" /></td>
		<td align="center"><input type="text" name="txtreject_1" id="txtreject_1" class="text_boxes_numeric" style="width:40px;" onBlur="fnc_total_calculate();" /></td>
		<td align="center"><input type="text" name="txtRemarks_1" id="txtRemarks_1" class="text_boxes" style="width:60px;" />
			<input type="hidden" name="txtDtlsUpdateId_1" id="txtDtlsUpdateId_1" style="width:50px" class="text_boxes" value="" />
			<input type="hidden" name="txtColorSizeid_1" id="txtColorSizeid_1" style="width:50px" class="text_boxes" value="" />
			<input type="hidden" name="txtpoid_1" id="txtpoid_1" style="width:50px" class="text_boxes" value="" />
		</td> 
	</tr>
	
	<?
	exit();	
	
}

?>