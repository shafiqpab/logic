<?
include('../../../includes/common.php'); 
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

if ($action=="load_drop_down_delivery_com")
{
	$data=explode("_",$data);

	if($data[1]==1)
	{
		echo create_drop_down( "cbo_deli_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "-- Select Del. Company --", "", "fnc_load_party(3,document.getElementById('cbo_within_group').value);");
	}
	else if($data[1]==2)
	{
		echo create_drop_down( "cbo_deli_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]'  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (3)) order by buyer_name","id,buyer_name", 1, "-- Select Del. Company --","", "" );
	}

	exit();
}


if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	if($data[1]==1) $dropdown_name="cbo_location_name";
	else if($data[1]==3) $dropdown_name="cbo_deli_party_location";
	else $dropdown_name="cbo_party_location";
	$location_arr=return_library_array( "select id, location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name",'id','location_name');
	if(count($location_arr)==1) $selected = key($location_arr); else $selected=0;
	echo create_drop_down( $dropdown_name, 150, $location_arr,"", 1, "-- select Location --", $selected, "",0 );
	exit();
}
 
/*if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	if($data[1]==1) $dropdown_name="cbo_location_name";
	else if($data[1]==2) $dropdown_name="cbo_party_location";
	else if($data[1]==3) $dropdown_name="cbo_deli_party_location";
	
	echo create_drop_down( $dropdown_name, 150, "SELECT id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "" );	
	exit();
}*/


if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=17 and report_id=174 and is_deleted=0 and status_active=1");
	$print_report_format_arr=explode(",",$print_report_format);
	echo "$('#Print').hide();\n";
	echo "$('#btn_print2').hide();\n";
	echo "$('#btn_print3').hide();\n";
	echo "$('#btn_print4').hide();\n";
	echo "$('#btn_print5').hide();\n";
	echo "$('#btn_print6').hide();\n";
	echo "$('#btn_print7').hide();\n";
	echo "$('#btn_print8').hide();\n";
	echo "$('#btn_print9').hide();\n";
	echo "$('#btn_print10').hide();\n";
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==86){echo "$('#Print').show();\n";}
			if($id==84){echo "$('#btn_print2').show();\n";}
			if($id==85){echo "$('#btn_print3').show();\n";}
            if($id==137){echo "$('#btn_print4').show();\n";}
            if($id==129){echo "$('#btn_print5').show();\n";}
			if($id==360){echo "$('#btn_print6').show();\n";}
            if($id==161){echo "$('#btn_print7').show();\n";}
            if($id==230){echo "$('#btn_print8').show();\n";}
            if($id==220){echo "$('#btn_print9').show();\n";}
            if($id==235){echo "$('#btn_print10').show();\n";}
			
		}
	}
	exit();	
}



if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";
	//echo $data[2];
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- select Company --", $data[2], "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- select Party --", $data[2], "" );
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
			var within_group = $('#cbo_within_group').val();
			load_drop_down( 'trims_delivery_entry_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0)
			{
				$('#search_by_td').html('Receive System ID');
			}
			else if(val==2)
			{
				$('#search_by_td').html('W/O No');
			}
			else if(val==3)
			{
				$('#search_by_td').html('Production Id');
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

	function check_fTrims( str )
	{
		if($("#chk_fTrims").prop('checked')==true) $('#chk_fTrims').val(1); else $('#chk_fTrims').val(0);
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
                    <th><input type="checkbox" id="chk_fTrims" onClick="check_fTrims(this.value)" value="0"> F.Trims Receive</th>
                </tr>           
            </thead>
            <tbody>
                <tr class="general">
                    <td><input type="hidden" id="selected_job"><? $data=explode("_",$data); ?>  <!--  echo $data;-->
                        <? 
                        echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
                    </td>
                    <td>
                        <?php echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 0, "--  --", $data[3], "fnc_load_party_popup(1,this.value);" ); ?>
                    </td>
                    <td id="buyer_td">
                        <? echo create_drop_down( "cbo_party_name", 150, "","", 1, "-- select Party --", $data[2], "fnc_load_party_popup(1,this.value);" );   	 
                        ?>
                    </td>
                    <td>
						<?
                           $search_by_arr=array(1=>"Receive System ID",2=>"W/O No",3=>"Production Id",4=>"Buyer Po",5=>"Buyer Style",6=>"F.Trims Receive ID");
                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                    </td>
                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 100, $year,"", 1, "-- select --", date('Y'), "",0 ); ?></td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value+'_'+<? echo $data[4]; ?>+'_'+document.getElementById('chk_fTrims').value, 'create_del_workorder_list_view', 'search_div', 'trims_delivery_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
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
	$src_for_order =$data[9];
	$chk_fTrims =$data[10];
	//echo $src_for_order =$data[9]; die;
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}

	if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please select Company First."; die; }
	//echo $search_type; die;
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond=""; $search_prod="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.subcon_job='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";

			else if($search_by==3) $search_prod="and b.trims_production='$search_str'";

			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no = '$search_str' ";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref = '$search_str' ";
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.subcon_job like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'"; 

			else if($search_by==3) $search_prod="and b.trims_production like '$search_str%'"; 

			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '$search_str%'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.subcon_job like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'"; 

			else if($search_by==3) $search_prod="and b.trims_production like '%$search_str'";  

			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str'";  
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.subcon_job like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'"; 

			else if($search_by==3) $search_prod="and b.trims_production like '%$search_str%'";  

			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str%'"; 
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str%'";   
		}
	}

	if($src_for_order==2)
	{
		$search_com_cond.="and b.source_for_order=2";
	} else {
		$search_com_cond.="and b.source_for_order in(0,1)";
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
	
	$production_ids='';
	
	if($db_type==0) $id_cond="group_concat(a.id)";
	else if($db_type==2) $id_cond="listagg(a.id,',') within group (order by a.id)";
	//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";


	if(($search_prod!="" && $search_by==3))
	{
		$production_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, trims_production_mst b", "a.id=b.received_id $search_prod", "id");
	}
	//echo $po_ids; //a.order_id=b.order_id
	if ($production_ids!="") $production_idsCond=" and a.id in ($production_ids)"; else $production_idsCond="";



	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$buyer_po_arr=array();
	if($within_group==1)
	{
		$po_sql ="select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
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
	/*else if($db_type==2)
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
	}*/
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		//$color_id_str=",listagg(c.color_id,',') within group (order by c.color_id) as color_id";
		$color_id_str=",rtrim(xmlagg(xmlelement(e,c.color_id,',').extract('//text()') order by c.color_id).GetClobVal(),',') as color_id";
		if($within_group==1)
		{
			//$buyer_po_id_str=",listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)  as buyer_po_id";
			
			$buyer_po_id_str=",rtrim(xmlagg(xmlelement(e,b.buyer_po_id,',').extract('//text()') order by b.buyer_po_id).GetClobVal(),',') as buyer_po_id";
		}
		else
		{
			$buyer_po_no_str=",rtrim(xmlagg(xmlelement(e,b.buyer_po_no,',').extract('//text()') order by b.buyer_po_no).GetClobVal(),',') as buyer_po_no";
			$buyer_po_style_str=",rtrim(xmlagg(xmlelement(e,b.buyer_style_ref,',').extract('//text()') order by b.buyer_style_ref).GetClobVal(),',') as buyer_style_ref";
			
			//$buyer_po_no_str=",listagg(b.buyer_po_no,',') within group (order by b.id) as buyer_po_no";
			//$buyer_po_style_str=",listagg(b.buyer_style_ref,',') within group (order by b.id) as buyer_style";
		}
	}
	
	
	

	$variable_status=return_field_value("process_production_qty_control","variable_setting_trim_prod","company_name='$data[0]' and variable_list =3 and is_deleted = 0 and status_active = 1");
	//echo "select process_production_qty_control from variable_setting_trim_prod where company_name='$data[0]' and variable_list =3 and is_deleted = 0 and status_active = 1";
	//echo $variable_status ; die;
	if($chk_fTrims==1)
	{
		$sql= "select  a.id, a.subcon_job,a.job_no_prefix_num, a.company_id, a.wo_id, a.receive_date, a.challan_date, a.challan_no, a.store_id, a.receive_basis, $ins_year_cond as year from trims_receive_mst a , trims_receive_dtls b where a.id=b.mst_id and a.entry_form=451 and a.status_active=1 and b.status_active=1 $order_rcv_date $company group by a.id, a.subcon_job,a.job_no_prefix_num, a.company_id, a.wo_id, a.receive_date, a.challan_date, a.challan_no, a.store_id, a.receive_basis,a.insert_date order by a.id DESC";

		$req_rcv_Array=sql_select( "select a.id as wo_id ,a.requ_no as wo_no ,b.receive_basis from trims_finish_purchase_req_mst a , trims_receive_mst b where a.id=b.wo_id and b.entry_form=451 and b.receive_basis=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0" );
		foreach ($req_rcv_Array as $row)
		{
			$req_rcv_order_Array[$row[csf("receive_basis")]][$row[csf("wo_id")]]['wo_no']=$row[csf("wo_no")];
			//$req_rcv_order_Array[$row[csf("receive_basis")]][$row[csf("wo_id")]]['wo_no']=$row[csf("wo_no")];
		}
		unset($req_rcv_Array);
	
		$subcon_rcv_Array=sql_select( "select a.id as wo_id ,a.subcon_job as wo_no ,b.receive_basis from trims_subcon_ord_mst a , trims_receive_mst b where a.id=b.wo_id and b.entry_form=451 and a.entry_form=450  and b.receive_basis !=7   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0" );
		foreach ($subcon_rcv_Array as $row)
		{
			$subcon_rcv_order_Array[$row[csf("receive_basis")]][$row[csf("wo_id")]]['wo_no']=$row[csf("wo_no")];
			//$subcon_rcv_order_Array[$row[csf("receive_basis")]][$row[csf("wo_id")]]['wo_no']=$row[csf("wo_no")];
		}
		unset($subcon_rcv_Array);
		
	}else{
		if($variable_status==2)
	 	{
			$sql= "select a.id, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, a.status $color_id_str $buyer_po_id_str $buyer_po_no_str $buyer_po_style_str
			from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c 
			where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 $order_rcv_date $company $party_id_cond $search_com_cond  $production_idsCond $withinGroup $year_cond and b.id=c.mst_id and b.job_no_mst=c.job_no_mst 
			group by a.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date ,a.status
			order by a.id DESC";
	 	}
	 	else
		{
			
		/*	$sql= "select a.id, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date $color_id_str $buyer_po_id_str $buyer_po_no_str $buyer_po_style_str
			from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c , trims_production_mst d  
			where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.id=d.received_id and d.status_active=1 $order_rcv_date $company $party_id_cond $search_com_cond $production_idsCond $withinGroup $year_cond and b.id=c.mst_id  
			group by a.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date
			order by a.id DESC"
			*/
			
			  $sql= "select a.id, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,1 as tranfer_status, a.status $color_id_str $buyer_po_id_str $buyer_po_no_str $buyer_po_style_str 
			from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c , trims_production_mst d  
			where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.id=d.received_id and d.status_active=1 $order_rcv_date $company $party_id_cond $search_com_cond $production_idsCond $withinGroup $year_cond and b.id=c.mst_id  
			group by a.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date , a.status
			union all 
			select a.id, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date ,2 as tranfer_status, a.status $color_id_str $buyer_po_id_str $buyer_po_no_str $buyer_po_style_str
			from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c , trims_item_transfer_dtls d  
			where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.order_no=d.to_order_no and d.status_active=1 $order_rcv_date $company $party_id_cond $search_com_cond $production_idsCond $withinGroup $year_cond and b.id=c.mst_id  
			group by a.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, a.status" ;

			// new add and //c.id=e.break_down_details_id and b.id=e.receive_dtls_id and e.delevery_status !=3
	 	}
	}
  	

 	//echo "<pre>"; print_r($req_rcv_order_Array);
 	// echo $sql;
	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="920" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Job No</th>
            <th width="60">Year</th>
            <th width="220">W/O No</th>
            <th width="100">Buyer Po</th>
            <th width="100">Buyer Style</th>
            <th width="80">Ord Receive Date</th>
            <th width="80">Delivery Date</th>
            <th width="120"> Color</th>
        </thead>
        </table>
        <div style="width:920px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="920" class="rpt_table" id="tbl_po_list">
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
				$color=$row[csf('color_id')];
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//$excolor_id=array_unique(explode(",",$row[csf('color_id')]));
				if($chk_fTrims !=1){
					$color_name="";	
					//echo $excolor_id."ghgh"; 
					if($db_type==2) $color = $color->load();
					$color=array_unique(explode(",",$color));
					//echo $excolor_id ; 
					
					foreach ($color as $color_id)
					{
						if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=', '.$color_arr[$color_id];
					}
					
					
					if($within_group==1)
					{
						$buyer_po=""; $buyer_style="";
						//$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
						$buyer_po_id=$row[csf('buyer_po_id')];
						if($db_type==2) $buyer_po_id = $buyer_po_id->load();
						$buyer_po_id=array_unique(explode(",",$buyer_po_id));
						
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
						$buyer_po_no = $row[csf('buyer_po_no')];
						$buyer_style_ref = $row[csf('buyer_style_ref')];
						if($db_type==2) $buyer_po_no = $buyer_po_no->load();
						if($db_type==2) $buyer_style_ref = $buyer_style_ref->load();

						$buyer_po=implode(",",array_unique(explode(",",$buyer_po_no)));
						$buyer_style=implode(",",array_unique(explode(",",$buyer_style_ref)));
					}
				}
				
			
			
               /* if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
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
						//$name[csf('id')]= $name[csf('id')]->load();
						
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
				}*/
				if($chk_fTrims ==1){
					if($row[csf("receive_basis")] ==7)
					{
						$order_no=$req_rcv_order_Array[$row[csf("receive_basis")]][$row[csf("wo_id")]]['wo_no'];
					}else{
						$order_no=$subcon_rcv_order_Array[$row[csf("receive_basis")]][$row[csf("wo_id")]]['wo_no'];
					}
				}else{
					$order_no=$row[csf('order_no')];
				}
				//30_OG-FTRE-21-00014_1_2
				if($row[csf('status')]!=2 && $row[csf('status')]!=3){
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('subcon_job')].'_'.$chk_fTrims.'_'.$row[csf("receive_basis")].'_'.$row[csf("tranfer_status")]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="220"  style="word-break:break-all" ><? echo $order_no; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_po; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_style; ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>	
                    <td width="120"  style="word-break:break-all"><? echo $color_name; ?></td>
                </tr>
				<? 
                $i++; 
				}
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
		echo "document.getElementById('order_received_id').value        = ".$row[csf("id")].";\n";
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
		//echo "document.getElementById('txt_delivery_date').value		= '".change_date_format($row[csf("delivery_date")])."';\n"; 
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

if ($action=="load_php_rcv_data_to_form")
{
	$data=explode('_',$data);
	$mst_id=$data[0];
	$rcvBasis=$data[1];
	//$nameArray=sql_select( "select id, subcon_job, company_id, location_id, party_id, currency_id, party_location, delivery_date, rec_start_date, rec_end_date, receive_date, within_group, party_location, order_id, order_no,exchange_rate,remarks from subcon_ord_mst where subcon_job='$data' and entry_form=255 and status_active=1" );
	//$nameArray=sql_select( "select id, requ_no, company_id, location_id, requisition_date, pay_mode, source, manual_req, currency_id, delivery_date, req_by, remarks, template_id, status_active from trims_finish_purchase_req_mst where id='$data' and is_deleted=0");
	/*$nameArray=sql_select( "select id, entry_form, subcon_job, job_no_prefix, job_no_prefix_num, company_id, receive_basis, wo_id, receive_date, challan_date, challan_no, store_id  from trims_receive_mst where id='$data' and is_deleted=0");*/
	//if($nameArray)
	//echo "select id, entry_form, subcon_job, job_no_prefix, job_no_prefix_num, company_id, receive_basis, wo_id, receive_date, challan_date, challan_no, store_id  from trims_receive_mst where id='$data' and is_deleted=0";
	$req_rcv_Array=sql_select( "select a.id as wo_id ,a.requ_no as wo_no ,b.receive_basis from trims_finish_purchase_req_mst a , trims_receive_mst b where a.id=b.wo_id and b.entry_form=451 and b.receive_basis=7 and b.id='$mst_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0" );

	/*$nameArray=sql_select( "select a.id as wo_id ,a.subcon_job as wo_no ,a.job_no_prefix ,a.job_no_prefix_num ,a.company_id ,a.location_id, b.receive_basis ,a.order_rcv_id, a.order_rcv_no ,a.supplier_id ,a.currency_id ,a.paymode_id ,a.source_id ,a.exchange_rate ,a.wo_date ,a.delivery_date ,a.attention , b.id as rcv_id, b.subcon_job , b.receive_date, b.challan_date, b.challan_no, b.store_id from trims_subcon_ord_mst a , trims_receive_mst b where a.id=b.wo_id and b.entry_form=451 and a.entry_form=450 and b.id='$mst_id'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0" );*/

	if($rcvBasis==7){
		$sql="select a.id as wo_id ,a.requ_no as wo_no ,a.company_id ,a.location_id, b.receive_basis ,a.currency_id ,a.delivery_date , b.id as rcv_id, b.subcon_job , b.receive_date, b.challan_date, b.challan_no, b.store_id from trims_finish_purchase_req_mst a , trims_receive_mst b where a.id=b.wo_id and b.entry_form=451 and b.id='$mst_id'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0" ;
	}else{
		$sql="select a.id as wo_id ,a.subcon_job as wo_no ,a.job_no_prefix ,a.job_no_prefix_num ,a.company_id ,a.location_id, b.receive_basis ,a.order_rcv_id, a.order_rcv_no ,a.supplier_id ,a.currency_id ,a.paymode_id ,a.source_id ,a.exchange_rate ,a.wo_date ,a.delivery_date ,a.attention , b.id as rcv_id, b.subcon_job , b.receive_date, b.challan_date, b.challan_no, b.store_id from trims_subcon_ord_mst a , trims_receive_mst b where a.id=b.wo_id and b.entry_form=451 and a.entry_form=450 and b.id='$mst_id'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0" ;
	}
	//echo $sql;
	$nameArray=sql_select($sql);
	foreach ($req_rcv_Array as $row)
	{
		$req_rcv_order_Array[$row[csf("receive_basis")]][$row[csf("wo_id")]]['wo_no']=$row[csf("wo_no")];
		//$req_rcv_order_Array[$row[csf("receive_basis")]][$row[csf("wo_id")]]['wo_no']=$row[csf("wo_no")];
	}
	unset($req_rcv_Array);
	//echo "select a.id as wo_id ,a.subcon_job as wo_no ,b.receive_basis from trims_subcon_ord_mst a , trims_receive_mst b where a.id=b.wo_id and b.entry_form=451 and a.entry_form=450  and b.id='$data' and b.receive_basis !=7   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$subcon_rcv_Array=sql_select( "select a.id as wo_id ,a.subcon_job as wo_no ,b.receive_basis from trims_subcon_ord_mst a , trims_receive_mst b where a.id=b.wo_id and b.entry_form=451 and a.entry_form=450  and b.id='$mst_id' and b.receive_basis !=7   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0" );
	foreach ($subcon_rcv_Array as $row)
	{
		$subcon_rcv_order_Array[$row[csf("receive_basis")]][$row[csf("wo_id")]]['wo_no']=$row[csf("wo_no")];
		//$subcon_rcv_order_Array[$row[csf("receive_basis")]][$row[csf("wo_id")]]['wo_no']=$row[csf("wo_no")];
	}
	unset($subcon_rcv_Array);
	// 
	foreach ($nameArray as $row)
	{
		$receive_basis=$row[csf("receive_basis")];
		$is_febric_trims=1;

		if($receive_basis ==7)
		{
			$order_no=$req_rcv_order_Array[$receive_basis][$row[csf("wo_id")]]['wo_no'];
		}else{
			//echo $subcon_rcv_order_Array[$receive_basis][$row[csf("wo_id")]]['wo_no'].'=='.$receive_basis.'=='.$row[csf("wo_id")];
			$order_no=$subcon_rcv_order_Array[$receive_basis][$row[csf("wo_id")]]['wo_no'];
		}
				
		echo "document.getElementById('received_id').value          	= ".$row[csf("rcv_id")].";\n";
		echo "document.getElementById('order_received_id').value 		= '".$row[csf("order_rcv_id")]."';\n";
		echo "document.getElementById('is_fabric_trims').value 			= '".$is_febric_trims."';\n";
		echo "document.getElementById('txt_receive_basis').value 		= '".$receive_basis."';\n";
		echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_id")]."';\n";
		//echo "load_drop_down( 'requires/trims_delivery_entry_controller','".$row[csf('company_id')]."_1', 'load_drop_down_location', 'location_td' );\n";	

		echo "document.getElementById('cbo_location_name').value 		= '".$row[csf("location_id")]."';\n";
		//echo "document.getElementById('cbo_within_group').value 		= '".$row[csf("within_group")]."';\n";  
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		 
		echo "document.getElementById('hid_order_id').value          	= '".$row[csf("wo_id")]."';\n";
		echo "document.getElementById('txt_order_no').value          	= '".$order_no."';\n";
		//echo "document.getElementById('hid_order_id').value          	= '".$row[csf("receive_basis")]."';\n";
		echo "$('#txt_order_no').attr('disabled','true')".";\n";
		echo "$('#cbo_within_group').attr('disabled','true')".";\n";
		//echo "set_button_status(0,'".$_SESSION['page_permission']."', 'fnc_job_order_entry',1);\n";	
	}
	exit();	
}


if( $action=='dalivery_order_dtls_list_view' ) 
{
	$data=explode('_',$data);
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group where item_category=4 and status_active=1",'id','item_name');
	$tblRow=0;
    $is_transfer_trims=$data[7];
	 //echo $data[5].'**'.$data[0];
	/******** Finish Trims*********/
	if($data[5]==1)
	{
		if($data[0]==2)
		{
			$del_result=sql_select( "select break_down_details_id, received_id,delevery_qty,claim_qty,id AS delDtlsId,gmts_color_id, gmts_size_id,size_name,color_name,remarks,status_active,no_of_roll_bag,wo_type,delevery_status from trims_delivery_dtls  where mst_id ='$data[1]' and status_active=1 and is_deleted=0 order by id");	
			//echo "select a.id, a.mst_id, a.order_rcv_id , a.wo_id , a.wo_dtls_id, a.wo_break_id, a.rcv_qty, a.remarks from trims_receive_dtls a  where entry_form =451 and mst_id=$data[3] and a.status_active=1 and a.is_deleted=0 order by a.id";	
			foreach ($del_result as  $row) 
			{
				//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
				$del_arr[$row[csf("received_id")]][$row[csf("break_down_details_id")]]["delevery_qty"] =$row[csf("delevery_qty")];
				$del_arr[$row[csf("received_id")]][$row[csf("break_down_details_id")]]["claim_qty"] =$row[csf("claim_qty")];
				$del_arr[$row[csf("received_id")]][$row[csf("break_down_details_id")]]["delDtlsId"]=$row[csf("delDtlsId")];
				$del_arr[$row[csf("received_id")]][$row[csf("break_down_details_id")]]["gmts_color_id"]=$row[csf("gmts_color_id")];
				$del_arr[$row[csf("received_id")]][$row[csf("break_down_details_id")]]["gmts_size_id"] =$row[csf("gmts_size_id")];
				$del_arr[$row[csf("received_id")]][$row[csf("break_down_details_id")]]["size_name"] =$row[csf("size_name")];
				$del_arr[$row[csf("received_id")]][$row[csf("break_down_details_id")]]["color_name"] =$row[csf("color_name")];
				$del_arr[$row[csf("received_id")]][$row[csf("break_down_details_id")]]["remarks"] =$row[csf("remarks")];
				$del_arr[$row[csf("received_id")]][$row[csf("break_down_details_id")]]["no_of_roll_bag"] =$row[csf("no_of_roll_bag")];
				$del_arr[$row[csf("received_id")]][$row[csf("break_down_details_id")]]["wo_type"] =$row[csf("wo_type")];
				$del_arr[$row[csf("received_id")]][$row[csf("break_down_details_id")]]["delevery_status"] =$row[csf("delevery_status")];
			}
		}

		$rcv_qty_result=sql_select( "select a.order_rcv_id , a.wo_id , a.wo_dtls_id, a.wo_break_id, a.rcv_qty from trims_receive_dtls a  where entry_form =451 and mst_id !=$mst_id and a.status_active=1 and a.is_deleted=0 ");		
		foreach ($rcv_qty_result as  $row) 
		{
			//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
			$rec_qty_arr[$row[csf("order_rcv_id")]][$row[csf("wo_break_id")]]["cum_qty"] +=$row[csf("rcv_qty")];
		}

		$rcv_result=sql_select( "select a.id, a.mst_id, a.order_rcv_id , a.wo_id , a.wo_dtls_id, a.wo_break_id, a.rcv_qty, a.remarks from trims_receive_dtls a  where entry_form =451 and mst_id=$data[3] and a.status_active=1 and a.is_deleted=0 order by a.id");	
		//echo "select a.id, a.mst_id, a.order_rcv_id , a.wo_id , a.wo_dtls_id, a.wo_break_id, a.rcv_qty, a.remarks from trims_receive_dtls a  where entry_form =451 and mst_id=$data[3] and a.status_active=1 and a.is_deleted=0 order by a.id";	
		foreach ($rcv_result as  $row) 
		{
			//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
			
			if($data[6]==7){
				$wo_rcv_id=$row[csf("wo_id")];
				$wo_rcv_brk_dtls_id=$row[csf("wo_dtls_id")];
			}else{
				$wo_rcv_id=$row[csf("order_rcv_id")];
				$wo_rcv_brk_dtls_id=$row[csf("wo_break_id")];
			}
			$rcv_arr[$wo_rcv_id][$wo_rcv_brk_dtls_id]["wo_id"] =$row[csf("wo_id")];
			$rcv_arr[$wo_rcv_id][$wo_rcv_brk_dtls_id]["wo_dtls_id"] =$row[csf("wo_dtls_id")];
			$rcv_arr[$wo_rcv_id][$wo_rcv_brk_dtls_id]["rcv_qty"] =$row[csf("rcv_qty")];
			$rcv_arr[$wo_rcv_id][$wo_rcv_brk_dtls_id]["remarks"] =$row[csf("remarks")];
			$rcv_arr[$wo_rcv_id][$wo_rcv_brk_dtls_id]["id"] =$row[csf("id")];
			$wo_dtls_id .=$row[csf("wo_dtls_id")].',';
			$wo_break_id .=$row[csf("wo_break_id")].',';

			//$rec_qty_arr[$row[csf("wo_break_id")]]['qty'] +=$row[csf("rcv_qty")];
		}
		$wo_dtls_id=implode(",",array_unique(explode(",",$wo_dtls_id)));
		$wo_break_id=implode(",",array_unique(explode(",",$wo_break_id)));
		if($data[6]==7)
		{
			$wo_dtls_id=chop($wo_dtls_id,',');
			$sql="select b.id as dtls_id, b.mst_id as order_rcv_id, b.item_group_id as trim_group, b.item_description as description, b.color_id as color_id, b.size_id as size_id, b.uom as order_uom, b.quantity as qnty, b.rate, b.amount, b.remarks, b.status_active
			from trims_finish_purchase_req_dtls b
			where b.id in ($wo_dtls_id) and b.status_active=1 order by b.id";
			$qry_result=sql_select(	$sql );
			foreach ($qry_result as  $row) 
			{
				//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
				$wo_arr[$row[csf("order_rcv_id")]][$row[csf("dtls_id")]]["description"] =$row[csf("description")];
				$wo_arr[$row[csf("order_rcv_id")]][$row[csf("dtls_id")]]["color_id"] =$row[csf("color_id")];
				$wo_arr[$row[csf("order_rcv_id")]][$row[csf("dtls_id")]]["size_id"] =$row[csf("size_id")];
				$wo_arr[$row[csf("order_rcv_id")]][$row[csf("dtls_id")]]["qnty"] =$row[csf("qnty")];
				$wo_arr[$row[csf("order_rcv_id")]][$row[csf("dtls_id")]]["dtls_id"] =$row[csf("dtls_id")];
				$wo_arr[$row[csf("order_rcv_id")]][$row[csf("dtls_id")]]["order_uom"] =$row[csf("order_uom")];
				//$wo_arr[$row[csf("order_rcv_id")]][$row[csf("dtls_id")]]["section"] =$row[csf("section")];
				//$wo_arr[$row[csf("order_rcv_id")]][$row[csf("dtls_id")]]["sub_section"] =$row[csf("sub_section")];
				$wo_arr[$row[csf("order_rcv_id")]][$row[csf("dtls_id")]]["trim_group"] =$row[csf("trim_group")];
			}
		}
		else
		{
			$wo_break_id=chop($wo_break_id,',');
			$wo_dtls_id=chop($wo_dtls_id,',');
			$subcon_sql="select a.id, a.job_no_mst, a.description, a.color_id, a.size_id, a.qnty, a.rate, a.amount, a.order_rcv_break_id, a.order_rcv_id ,b.id as dtls_id ,b.order_quantity as wo_qnty, b.order_uom, b.rate, b.amount, b.section, b.sub_section, b.item_group  as trim_group , b.order_rcv_dtls_id, b.order_rcv_id, a.mst_id, d.id as rcv_id from trims_subcon_ord_breakdown a , trims_subcon_ord_dtls b, trims_subcon_ord_mst c,trims_receive_mst d where a.job_no_mst=b.job_no_mst and b.id=a.mst_id and c.id=b.mst_id and c.subcon_job=a.job_no_mst and c.id=d.wo_id and d.entry_form=451 and c.entry_form=450  and b.id in ($wo_dtls_id) and a.id in ($wo_break_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id";

			/*$sql="select a.id as wo_id ,a.subcon_job as wo_no ,a.job_no_prefix ,a.job_no_prefix_num ,a.company_id ,a.location_id, b.receive_basis ,a.order_rcv_id, a.order_rcv_no ,a.supplier_id ,a.currency_id ,a.paymode_id ,a.source_id ,a.exchange_rate ,a.wo_date ,a.delivery_date ,a.attention , b.id as rcv_id, b.subcon_job , b.receive_date, b.challan_date, b.challan_no, b.store_id from trims_subcon_ord_mst a , trims_receive_mst b where a.id=b.wo_id and b.entry_form=451 and a.entry_form=450 and b.id='$mst_id'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0" ;*/


			$qry_result=sql_select($subcon_sql);

			/*echo "select a.id, a.job_no_mst, a.description, a.color_id, a.size_id, a.qnty, a.rate, a.amount, a.order_rcv_break_id, a.order_rcv_id ,b.id as dtls_id ,b.order_quantity as wo_qnty, b.order_uom, b.rate, b.amount, b.section, b.sub_section, b.item_group  as trim_group , b.order_rcv_dtls_id, b.order_rcv_id, a.mst_id from trims_subcon_ord_breakdown a , trims_subcon_ord_dtls b where a.job_no_mst=b.job_no_mst and b.id=a.mst_id and b.id in ($wo_dtls_id) and a.id in ($wo_break_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id";*/

			foreach ($qry_result as  $row)  
			{
				//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
				$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["description"] =$row[csf("description")];
				$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["color_id"] =$row[csf("color_id")];
				$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["size_id"] =$row[csf("size_id")];
				$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["qnty"] =$row[csf("qnty")];
				$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["dtls_id"] =$row[csf("dtls_id")];
				$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["order_uom"] =$row[csf("order_uom")];
				$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["section"] =$row[csf("section")];
				$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["sub_section"] =$row[csf("sub_section")];
				$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["trim_group"] =$row[csf("trim_group")];
				$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["mst_id"] = $row[csf("mst_id")];
				$wo_arr[$row[csf("order_rcv_id")]][$row[csf("id")]]["received_id"] = $row[csf("rcv_id")];
			}
		}
	}
	else
	{
		if($data[0]==1)
		{
			    $sql = "select a.id, a.mst_id, a.job_no_mst, a.order_id, a.order_no, a.buyer_po_id, a.booking_dtls_id,b.id as receive_dtls_id, b.qnty as order_quantity , a.order_uom,a.booked_uom, a.booked_conv_fac, b.rate, a.amount, a.delivery_date, a.buyer_po_no, a.buyer_style_ref, a.buyer_buyer, a.section, a.item_group as trim_group, a.rate_domestic,  a.amount_domestic,a.booked_qty,b.item_id,b.ply, b.gmts_color_id, b.gmts_size_id, b.color_id, b.size_id, b.size_name,b.description,b.id as break_id,b.book_con_dtls_id, b.style,c.wo_type,b.plan_cut*a.booked_conv_fac as pjob_qty from subcon_ord_mst c, subcon_ord_dtls a,subcon_ord_breakdown b where c.subcon_job=a.job_no_mst and c.subcon_job=b.job_no_mst and a.id=b.mst_id and a.job_no_mst=b.job_no_mst and a.job_no_mst='$data[1]' and a.mst_id='$data[3]'  and a.order_quantity<>0 and a.booked_qty<>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id,b.id ";  
  		}
		else
		{
			$sql = "select a.id,a.mst_id,a.job_no_mst,a.order_id,a.order_no,a.buyer_po_id,a.booking_dtls_id,b.id AS receive_dtls_id,b.qnty AS order_quantity,a.order_uom,a.booked_uom,a.booked_conv_fac,b.rate,a.amount,a.delivery_date,a.buyer_po_no,a.buyer_style_ref,a.buyer_buyer,a.section,a.item_group AS trim_group,a.rate_domestic,a.amount_domestic,a.booked_qty,b.item_id,b.gmts_color_id, b.gmts_size_id,b.color_id,b.size_id, b.size_name, b.ply,b.description,b.id AS break_id,b.book_con_dtls_id, b.style,c.delevery_qty,c.claim_qty,c.id AS delDtlsId,c.color_name,c.remarks,c.status_active,c.no_of_roll_bag,c.wo_type,b.plan_cut*a.booked_conv_fac as pjob_qty FROM subcon_ord_dtls a, subcon_ord_breakdown b LEFT JOIN trims_delivery_dtls c ON c.break_down_details_id = b.id AND c.mst_id ='$data[1]' and c.status_active<>0 WHERE a.id = b.mst_id and a.job_no_mst=b.job_no_mst and a.mst_id='$data[3]'  and b.status_active=1 and b.is_deleted=0 order by a.id,b.id";
		}
	}
	$trims_groups_arr=array();
	$trim_sql ="select id, item_group,buyer_buyer ,booked_conv_fac from subcon_ord_dtls where status_active=1 and is_deleted=0 and item_group is not null ";
	$trim_sql_res=sql_select($trim_sql);
	foreach ($trim_sql_res as $row){
		$trims_groups_arr[$row[csf("id")]]['item_group']=$item_group_arr[$row[csf("item_group")]];
		$trims_groups_arr[$row[csf("id")]]['buyer_buyer']=$row[csf("buyer_buyer")];
		$trims_groups_arr[$row[csf("id")]]['booked_conv_fac']=$row[csf("booked_conv_fac")];
	}
	unset($trim_sql_res);

	$qry_result=sql_select( "select a.id, a.job_no_mst, a.description, a.color_id, a.size_id, a.qnty, a.rate, a.amount, a.order_rcv_break_id, a.order_rcv_id ,b.id as dtls_id ,b.order_quantity as wo_qnty, b.order_uom, b.rate, b.amount, b.section, b.sub_section, b.item_group  as trim_group , b.order_rcv_dtls_id, b.order_rcv_id, a.mst_id, c.rcv_qty, c.remarks  from trims_subcon_ord_breakdown a , trims_subcon_ord_dtls b, trims_receive_dtls c where a.job_no_mst=b.job_no_mst and b.id=a.mst_id and a.id=c.wo_break_id and b.mst_id=c.wo_id and b.order_rcv_id ='$data[3]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by c.id");
	
	$rec_qty_arr=array();
	foreach ($qry_result as  $row) 
	{
		//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
		//$rec_qty_arr[$row[csf("order_rcv_id")]][$row[csf("wo_break_id")]]["cum_qty"] +=$row[csf("rcv_qty")];
		$rec_qty_arr[$row[csf('order_rcv_id')]][$row[csf('section')]][$row[csf('trim_group')]][trim($row[csf('description')])][$row[csf('color_id')]][$row[csf('size_id')]][$row[csf('order_uom')]]["cum_qty"] += $row[csf('rcv_qty')];
	}

	$delevery_qty_trims_arr=array();
	$pre_sql ="select wo_type, break_down_details_id, sum(delevery_qty) as delevery_qty  from trims_delivery_dtls where received_id='$data[3]' and status_active=1 and is_deleted=0 group by break_down_details_id,wo_type";
	$pre_sql_res=sql_select($pre_sql);
	foreach ($pre_sql_res as $row)
	{
		$delevery_qty_trims_arr[$row[csf("wo_type")]][$row[csf("break_down_details_id")]]['delevery_qty']=$row[csf("delevery_qty")];
	}

	unset($pre_sql_res);

	$convertedBQty=''; $shipStatus=0;

	//echo "<pre>"; print_r($rcv_arr);
	if($data[5]==1) // F.Trims Received
	{
		foreach($wo_arr as $rcv_id=> $rcv_id_data)
		{
			foreach($rcv_id_data as $woBrkId=> $rows)
			{ 
				$rcv_qty=$rcv_arr[$rcv_id] [$woBrkId]["rcv_qty"];
				$remarks=$rcv_arr[$rcv_id] [$woBrkId]["remarks"];
				$trim_rcv_dtls_id=$rcv_arr[$rcv_id] [$woBrkId]["id"];
				//$dtlsUpdateId=$rcv_arr[$rcv_id] [$woBrkId]["id"];
				//$cum_rcv_qty=$rec_qty_arr[$rcv_id] [$woBrkId]["cum_qty"];
				$section_id=$wo_arr[$rcv_id][$woBrkId]["section"];
				$trim_group=$wo_arr[$rcv_id][$woBrkId]["trim_group"];
				$description=$wo_arr[$rcv_id][$woBrkId]["description"];
				$color_id=$wo_arr[$rcv_id][$woBrkId]["color_id"];
				$size_id=$wo_arr[$rcv_id][$woBrkId]["size_id"];
				$order_uom=$wo_arr[$rcv_id][$woBrkId]["order_uom"];
				$wo_qty=$rows['qnty'];
				$cum_rcv_qty=$delevery_qty_trims_arr[2][$trim_rcv_dtls_id]['delevery_qty'];
				
				//echo $rcv_qty.'=='.$cum_rcv_qty.'=='.$del_bal;
				if($section_id==1) $subID='1,2,3';
				else if($section_id==3) $subID='4,5,18';
				else if($section_id==5) $subID='6,7,8,9,10,11,12,13';
				else if($section_id==10) $subID='14,15';
				else if($section_id==7) $subID='19,20';
				else $subID='0';
				if($rcvBasis==7){
					$basis_wise_rcv_id=$woBrkId='';
				}else{
					$basis_wise_rcv_id=$rcv_id;
				}
				$received_id=$wo_arr[$rcv_id][$woBrkId]["received_id"];
				
				if($data[0]==2){
					$delevery_qty=$del_arr[$received_id][$woBrkId]["delevery_qty"];
					$claim_qty=$del_arr[$received_id][$woBrkId]["claim_qty"];
					$dtlsUpdateId=$del_arr[$received_id][$woBrkId]["delDtlsId"];
					$size_name=$del_arr[$received_id][$woBrkId]["size_name"];
					$color_name=$del_arr[$received_id][$woBrkId]["color_name"];
					$remarks=$del_arr[$received_id][$woBrkId]["remarks"];
					$no_of_roll_bag=$del_arr[$received_id][$woBrkId]["no_of_roll_bag"];
					$wo_type=$del_arr[$received_id][$woBrkId]["wo_type"];
					$shipStatus=$del_arr[$received_id][$woBrkId]["delevery_status"];
					//$rcv_qty=$delevery_qty;
					//echo $received_id.'nnn'.$woBrkId;
				}
				$del_bal=$rcv_qty-$cum_rcv_qty;
				$tblRow++; //$prev_qty='';
				//echo $variable_status.'nnn';
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
					<td align="left" ><p><? //echo $row[csf('buyer_po_no')]; ?></p></td>
					<td align="left" ><p><? // echo $row[csf('buyer_style_ref')]; ?></p></td>
					<td align="left" ><p><? echo $trims_section[$section_id]; ?></td>
					<td align="left" ><p><? echo $item_group_arr[$trim_group]; ?></p></td>		
					<td align="left" ><p><? //echo $row[csf('style')]; ?></p></td>
					<td align="left" ><p><? echo $description; ?></p></td>
					<td ><p><? //echo $color_library[$gmts_color_id] ?></p></td>					
					<td ><p><? //echo $size_arr[$gmts_size_id] ?></p></td>
					<td ><p><? echo $color_library[$color_id] ?></p></td>					
					<td ><p><? echo $size_arr[$size_id] ?></p></td>
					<td ><p>0</td>
					<td><? echo $unit_of_measurement[$order_uom] ?></td>
					<td align="right" id="txtWorkOrderQuantity_<? echo $tblRow; ?>"  title="<?= $rcv_qty; ?>"><? echo number_format($rcv_qty,4,'.',''); ?></td>
					<td align="right" id="txtJobQuantity_<? echo $tblRow; ?>"  title="<?= $rcv_qty; ?>"><? echo number_format($rcv_qty,4,'.',''); ?></td>
					<td align="right" id="txtOrderQuantity_<? echo $tblRow; ?>" title="<?= number_format($rcv_qty,4,'.','');?>"><? echo number_format($rcv_qty,4,'.',''); ?></td>
                    <td align="right" id="txtdeliverableQuantity_<? echo $tblRow; ?>"></td>
					<td align="right" id="txtPrevQty_<? echo $tblRow; ?>" title="<?= $cum_rcv_qty;?>"><? echo number_format($cum_rcv_qty,4,'.',''); ?></td>
					<td align="right"><input id="txtCurQty_<? echo $tblRow; ?>" name="txtCurQty[]" onKeyUp="cal_values(<? echo $tblRow; ?>);" value="<? echo $delevery_qty; ?>" type="text" style="width:70px"  class="text_boxes_numeric"  /></td>
                    
                    
                    <td align="right" id="txtWoRate_<? echo $tblRow; ?>" title="<?= number_format($row[csf('rate')],4,'.','');?>"><? echo number_format($row[csf('rate')],4,'.',''); ?></td>
                    <td align="right" id="txtWoAmaount_<? echo $tblRow; ?>" title="<?= $delevery_qty*number_format($row[csf('rate')],4,'.','');?>"></td>
                    
	                <td align="right"><input id="noOfRollBag_<? echo $tblRow; ?>" name="noOfRollBag[]" value="<? echo $no_of_roll_bag; ?>" type="text" style="width:70px"  class="text_boxes"  /></td>
					<td align="right" id="txtDelvBalance_<? echo $tblRow; ?>" title="<?= number_format($del_bal,4,'.','');?>"> <? echo number_format($del_bal,4,'.','');// $del_bal ; ?></td> 
					<td id="cboshipingStatus_<? echo $tblRow; ?>" title="<?= $shipStatus;?>"><? echo$delivery_status[$shipStatus]; ?></td>
					<td><input id="txtRemarksDtls_<? echo $tblRow; ?>" name="txtRemarksDtls[]" value="<? echo $remarks; ?>" type="text" class="text_boxes" style="width:77px" />
						<input id="hdnDtlsUpdateId_<? echo $tblRow; ?>" name="hdnDtlsUpdateId[]" type="hidden" value="<? echo  $dtlsUpdateId; ?>" class="text_boxes_numeric" style="width:40px" />
						<input id="txtOverWOquantity_<? echo $tblRow; ?>" name="txtOverWOquantity[]" type="hidden" value="<? echo number_format($rcv_qty,4,'.',''); ?>" class="text_boxes_numeric" style="width:40px" />
						<input type="hidden" id="hdn_break_down_id_<? echo $tblRow; ?>" name="hdn_break_down_id[]" value="<? echo $woBrkId; ?>">
					</td>
					<td><? echo create_drop_down( "cboStatus_".$tblRow, 60, $row_status,"", '', "-- select --",$selected,"", 0,'','','','','','',"cboStatus[]"); ?>	
					</td>
				</tr>
				<?
			}
		}
	}
	else
	{
		//echo $sql;
		$rcv_result =sql_select($sql); 
		$break_qty_arr=array();
		foreach ($rcv_result as $row)
		{
			$break_qty_arr[$row[csf("break_id")]]['order_quantity']=$row[csf("order_quantity")];
			$break_qty_arr[$row[csf("break_id")]]['id']=$row[csf("id")];
		}

		
		$variable_status_auto_qty=return_field_value("process_production_qty_control","variable_setting_trim_prod","company_name='$data[4]' and variable_list =4 and is_deleted = 0 and status_active = 1");
		$variable_status=return_field_value("production_update_area","variable_setting_trim_prod","company_name='$data[4]' and variable_list =1 and is_deleted = 0 and status_active = 1");
		$variable_status_del=return_field_value("process_production_qty_control","variable_setting_trim_prod","company_name='$data[4]' and variable_list =3 and is_deleted = 0 and status_active = 1");
		////###### material over receive control only for receive ####////
		//$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$data[4] and variable_list=23 and category = 4 and status_active =1 and is_deleted=0 order by id");
		$variable_set_invent=array();
		$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;
		if($variable_status==2)
		{
			$production_sql ="select c.break_dtls_id,sum(c.qc_pass_qty) as qc_pass_qty  from trims_production_mst a, trims_production_dtls b, trims_prod_order_breakdown c where a.id=b.mst_id and b.id=c.mst_id and a.received_id=$data[3] and c.receive_id=$data[3] and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by c.break_dtls_id";
			$production_sql_res=sql_select($production_sql);
			$production_qty_arr=array(); $brkIds=''; 
			foreach ($production_sql_res as $row)
			{
				$brkIds=explode(",",$row[csf("break_dtls_id")]); $totalOrdQty='';
				foreach ($brkIds as $key => $value) 
				{
					$totalOrdQty +=	$break_qty_arr[$value]['order_quantity'];
				}

				foreach ($brkIds as $key => $value) 
				{
					$production_qty_arr[$value]['qc_pass_qty']+=$row[csf("qc_pass_qty")]*$break_qty_arr[$value]['order_quantity']/$totalOrdQty;
				}
			}
		}
		
		
		
		 	  $sql_transfer_dtls_out="select id, mst_id, company_id ,from_order_id, from_order_no, from_party_id, from_received_id, from_job_id, from_job_dtls_id, to_order_id, to_order_no, to_party_id, to_received_id, to_job_id, to_job_dtls_id, entry_form, section_id, sub_section_id, trim_group_id, uom, color_id, size_id, item_description, quantity,remarks from trims_item_transfer_dtls where    from_received_id='$data[3]' and status_active =1 and is_deleted =0";
	      		$sql_transfer_dtls_out_result= sql_select($sql_transfer_dtls_out);
 				$transfer_out_arr=array(); 
				foreach($sql_transfer_dtls_out_result as $row)
				{
 				    $transfer_out_arr[$row[csf('section_id')]][$row[csf('trim_group_id')]][$row[csf('item_description')]][$row[csf('color_id')]][$row[csf('size_id')]][$row[csf('uom')]]['transfer_quantity_out']+=$row[csf('quantity')];
 				}
		
		
		  $sql_transfer_dtls_in="select id, mst_id, company_id ,from_order_id, from_order_no, from_party_id, from_received_id, from_job_id, from_job_dtls_id, to_order_id, to_order_no, to_party_id, to_received_id, to_job_id, to_job_dtls_id, entry_form, section_id, sub_section_id, trim_group_id, uom, color_id, size_id, item_description, quantity,remarks from trims_item_transfer_dtls where    to_received_id='$data[3]' and status_active =1 and is_deleted =0";
	      $sql_transfer_dtls_in_result= sql_select($sql_transfer_dtls_in);
 				$transfer_in_arr=array(); 
				foreach($sql_transfer_dtls_in_result as $row)
				{
				   $transfer_in_arr[$row[csf('section_id')]][$row[csf('trim_group_id')]][$row[csf('item_description')]][$row[csf('color_id')]][$row[csf('size_id')]][$row[csf('uom')]]['transfer_quantity_in']+=$row[csf('quantity')];
				 
				}
		  
		 //''print_r($transfer_in_arr); die;
		
		//echo count($production_qty_arr);
		if($variable_status==1 || count($production_qty_arr)<1)
		{
			$production_sql ="select b.id,b.break_id,b.qc_qty from trims_production_mst a, trims_production_dtls b where a.id=b.mst_id and a.received_id=$data[3] and a.status_active=1 and b.status_active=1 and b.is_deleted=0"; 
			
			$production_sql_res=sql_select($production_sql); $production_arr=array(); $break_ids=''; $production_percent=''; $break_qty_sum_arr=array(); $production_percent=''; $tblRow=0;
			foreach ($production_sql_res as $row){
				$break_ids=explode(",",$row[csf("break_id")]); $order_quantity='';
				for($i=0; $i<count($break_ids);$i++){
					$production_arr[$break_ids[$i]]['qc_qty']=$row[csf('qc_qty')];
					$order_quantity+=$break_qty_arr[$break_ids[$i]]['order_quantity'];
				}
				
				$production_percent=($row[csf("qc_qty")]*100)/$order_quantity;
				for($i=0; $i<=count($break_ids);$i++)
				{
					$break_qty_sum_arr[$break_ids[$i]]['cal_qty']+=($production_percent*$break_qty_arr[$break_ids[$i]]['order_quantity'])/100;
				}
				unset($production_sql_res);
			}
			//echo "<pre>";
			//print_r($break_qty_sum_arr);
		}


//echo $data[0]; die; 
		if($data[0]==1)
		{
			foreach($rcv_result as  $row)
			{
				$prev_qty='';
				//echo $variable_status.'nnn';
				if($variable_status_del==2) // WO to direct Delivery
				{
					//echo $variable_status_del.'nnndgf';
					$orderQuantity=$row[csf('order_quantity')];
					$overOrderQuantity =(($orderQuantity*$over_receive_limit)/100)+$orderQuantity;
					$overConvertedBQty=$overOrderQuantity;
				}
				else
				{

					if($variable_status==2){
					$convertedBQty= $production_qty_arr[$row[csf('break_id')]]['qc_pass_qty']/$trims_groups_arr[$row[csf("id")]]['booked_conv_fac'];
					}else{
						$convertedBQty= $break_qty_sum_arr[$row[csf('break_id')]]['cal_qty']/$trims_groups_arr[$row[csf("id")]]['booked_conv_fac'];
					}
					
					if($over_receive_limit!=0 || $over_receive_limit!='')
					{
						//$netOverConvertedBQty =(($convertedBQty*$over_receive_limit)/100)+$convertedBQty;
						$netOverConvertedBQty =(($row[csf('order_quantity')]*$over_receive_limit)/100)+$row[csf('order_quantity')];
						
						$overOrderQuantity=$netOverConvertedBQty;// (($row[csf('order_quantity')]*$over_receive_limit)/100)+$row[csf('order_quantity')] ;
						$orderQuantity=$row[csf('order_quantity')];
					} else {
						
						$overOrderQuantity= $row[csf('order_quantity')];
						$orderQuantity= $overOrderQuantity;
					}
					$overConvertedBQty = $convertedBQty;
					//echo $convertedBQty.'=='.$overConvertedBQty.'=='.$row[csf('source_for_order')].'=='.$variable_status.'=='.$over_receive_limit;
					if($variable_status==2 && $overConvertedBQty==''){
						$overConvertedBQty= $break_qty_sum_arr[$row[csf('break_id')]]['cal_qty']/$trims_groups_arr[$row[csf("id")]]['booked_conv_fac'];
					}

					if($row[csf('source_for_order')]==2){
						$overConvertedBQty= $rec_qty_arr[$row[csf('mst_id')]][$row[csf('section')]][$trims_groups_arr[chop($row[csf('id')],",")]['item_group_id']][trim($row[csf('description')])][$row[csf('color_id')]][$row[csf('size_id')]][$row[csf('order_uom')]]["cum_qty"];

						//echo $overConvertedBQty.'u';
						//echo $row[csf('mst_id')].'='.$row[csf('section')].'='.$row[csf('description')].'='.$row[csf('color_id')].'='.$row[csf('size_id')].'='.$row[csf('order_uom')];
						
					}

					//echo $overConvertedBQty.'=='.$row[csf('source_for_order')].'=='.$variable_status.'=='.$over_receive_limit;
					//18964.8====2==35  
				}
				
				 $transfer_quantity_in=$transfer_in_arr[$row[csf('section')]][$row[csf('trim_group')]][$row[csf('description')]][$row[csf('color_id')]][$row[csf('size_id')]][$row[csf('order_uom')]]['transfer_quantity_in'];
 				  $transfer_quantity_out=$transfer_out_arr[$row[csf('section')]][$row[csf('trim_group')]][$row[csf('description')]][$row[csf('color_id')]][$row[csf('size_id')]][$row[csf('order_uom')]]['transfer_quantity_out'];
				 
				 //echo $transfer_quantity_in."tyt".$row[csf('trim_group')];
				
				
				
				if( $row[csf("wo_type")]=='')
				{
					$row[csf("wo_type")]=0;
				}
				
			
				
				if(is_nan($overConvertedBQty)) $overConvertedBQty=0; else $overConvertedBQty = $overConvertedBQty;
				//if(is_nan($production_balance_Qty)) $production_balance_Qty=0; else $production_balance_Qty = $production_balance_Qty;
				
 				$overQty=$overOrderQuantity-$orderQuantity;
				$del_bal=($overOrderQuantity-($delevery_qty_trims_arr[$row[csf("wo_type")]][$row[csf("break_id")]]['delevery_qty']));
				//$del_bal=($overOrderQuantity-$row[csf('delevery_qty')]);

				//echo $del_bal.'='.$overOrderQuantity.'='.$delevery_qty_trims_arr[$row[csf("wo_type")]][$row[csf("break_id")]]['delevery_qty'];
				if(($overOrderQuantity*1==$del_bal*1) || ($overOrderQuantity*1 < $del_bal*1)){
					$shipStatus=1;
				} else if(($overOrderQuantity*1 > $del_bal*1) && $del_bal!=0){
					$shipStatus=2; 
				} else {
					$shipStatus=3;
				} 
				
				//echo $delevery_qty_trims_arr[$row[csf("wo_type")]][$row[csf("break_id")]]['delevery_qty'].'=='.$row[csf("wo_type")].'=='.$row[csf("break_id")].'=='.$overOrderQuantity;

				if($delevery_qty_trims_arr[$row[csf("wo_type")]][$row[csf("break_id")]]['delevery_qty']!='')
				{
					$prev_qty= number_format($delevery_qty_trims_arr[$row[csf("wo_type")]][$row[csf("break_id")]]['delevery_qty'],4,'.','');}else{ $prev_qty=0;
				} 
				
				//echo $overConvertedBQty.'='.$production_balance_Qty.'='.$prev_qty;
				$production_balance_Qty=number_format($overConvertedBQty,4,'.','')-number_format($transfer_quantity_out,4,'.','');
				$DeliverableQty=number_format($production_balance_Qty+$transfer_quantity_in,4,'.','');

				$balance_qty='';
				if($variable_status_auto_qty!=2)
				{
					//$balance_qty= number_format($overConvertedBQty-$prev_qty,4,'.','') ;
					$balance_qty= number_format($DeliverableQty-$prev_qty,4,'.','') ;
				}
				
				
				if($del_bal>0)
				{
				   $tblRow++;
					
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
					<td align="left" ><p><? echo $row[csf('buyer_po_no')]; ?></p></td>
					<td align="left" ><p><? echo $row[csf('buyer_style_ref')]; ?></p></td>
					<td align="left" ><p><? echo $trims_section[$row[csf('section')]]; ?></td>
					<td align="left" ><p><? echo $trims_groups_arr[chop($row[csf('id')],",")]['item_group']; ?></p></td>		
					<td align="left" ><p><? echo $row[csf('style')]; ?></p></td>
					<td align="left" ><p><? echo $row[csf('description')]; ?></p></td>
					<td ><p><? echo $color_library[$row[csf('gmts_color_id')]] ?></p></td>
					<td ><p><? echo $size_arr[$row[csf('gmts_size_id')]] ?></p></td>
					<td ><p><? echo $color_library[$row[csf('color_id')]] ?></p></td>
					<td ><p><?
					if($row[csf('size_id')] > 0)
                        echo $size_arr[$row[csf('size_id')]];
                    else
                        echo $row[csf('size_name')];
                     ?></p></td>
					<td ><p><? echo $row[csf('ply')] ?></p></td>					
					<td><? echo $unit_of_measurement[$row[csf('order_uom')]] ?></td>
					<td align="right" id="txtWorkOrderQuantity_<? echo $tblRow; ?>"  title="<?= $orderQuantity; ?>"><? echo number_format($overOrderQuantity,4,'.',''); ?></td>
					<td align="right" id="txtJobQuantity_<? echo $tblRow; ?>"  title="<? echo $row[csf('pjob_qty')]; ?>"><? echo number_format($row[csf('pjob_qty')],4,'.',''); ?></td>
					<td align="right" id="txtOrderQuantity_<? echo $tblRow; ?>" title="<?= number_format($DeliverableQty,4,'.','');//number_format($overConvertedBQty,4,'.','')?>"><? echo  number_format($overConvertedBQty,4,'.',''); ?></td>
                    
                    <td align="right" id="txtdeliverableQuantity_<? echo $tblRow; ?>" title="<?= $DeliverableQty;//$production_balance_Qty=number_format($overConvertedBQty,4,'.','')-number_format($transfer_quantity_out,4,'.','');?>"><? echo $balance_qty;//$DeliverableQty; ?></td>
                    
					<td align="right" id="txtPrevQty_<? echo $tblRow; ?>" title="<?= $prev_qty;?>"><? echo number_format($prev_qty,4,'.',''); ?></td>
					<td align="right"><input id="txtCurQty_<? echo $tblRow; ?>" name="txtCurQty[]" onKeyUp="cal_values(<? echo $tblRow; ?>);" value="<? echo $balance_qty; ?>" type="text" style="width:70px"  class="text_boxes_numeric"  /></td>
                    
                     <td align="right" id="txtWoRate_<? echo $tblRow; ?>" title="<?= number_format($row[csf('rate')],4,'.','');?>"><? echo number_format($row[csf('rate')],4,'.',''); ?></td>
                    <td align="right" id="txtWoAmaount_<? echo $tblRow; ?>" title="<?= $balance_qty*number_format($row[csf('rate')],4,'.','');?>"><? 
					
					 $amounts=$balance_qty*number_format($row[csf('rate')],4,'.',''); echo number_format($amounts,4);
					
			//echo $balance_qty*number_format($row[csf('rate')],4,'.',''); ?></td>
                     
	                <td align="right"><input id="noOfRollBag_<? echo $tblRow; ?>" name="noOfRollBag[]" value="" type="text" style="width:70px"  class="text_boxes"  /></td>
					<td align="right" id="txtDelvBalance_<? echo $tblRow; ?>" title="<?= number_format($del_bal,4,'.','');?>"> <? echo number_format($del_bal,4,'.','');// $del_bal ; ?></td> 
					<td id="cboshipingStatus_<? echo $tblRow; ?>" title="<?= $shipStatus;?>"><? echo $delivery_status[$shipStatus]; ?></td>
					<td><input id="txtRemarksDtls_<? echo $tblRow; ?>" name="txtRemarksDtls[]" value="<? echo $remarks; ?>" type="text" class="text_boxes" style="width:77px" />
						<input id="hdnDtlsUpdateId_<? echo $tblRow; ?>" name="hdnDtlsUpdateId[]" type="hidden" value="" class="text_boxes_numeric" style="width:40px" />
						<input id="txtOverWOquantity_<? echo $tblRow; ?>" name="txtOverWOquantity[]" type="hidden" value="<? echo number_format($overOrderQuantity,4,'.',''); ?>" class="text_boxes_numeric" style="width:40px" />
						<input type="hidden" id="hdn_break_down_id_<? echo $tblRow; ?>" name="hdn_break_down_id[]" value="<? echo $row[csf('break_id')]; ?>">
					</td>
					<td><? echo create_drop_down( "cboStatus_".$tblRow, 60, $row_status,"", '', "-- select --",$selected,"", 0,'','','','','','',"cboStatus[]"); ?>	
					</td>
				</tr>
				<?
				}
			}
		}	
		else
		{
			foreach($rcv_result as $row)
			{
				$tblRow++; $shipStatus=''; 
				if($row[csf('wo_type')]==''){
					$row[csf('wo_type')]=0;
				}
				//$orderQuantity= $row[csf('order_quantity')];
				if($variable_status_del==2) // WO to direct Delivery
				{
					$orderQuantity=$row[csf('order_quantity')];
					$overOrderQuantity =(($orderQuantity*$over_receive_limit)/100)+$orderQuantity;
					$overConvertedBQty=$overOrderQuantity;
				}
				else
				{
					if($variable_status==2){
					$convertedBQty= $production_qty_arr[$row[csf('break_id')]]['qc_pass_qty']/$trims_groups_arr[$row[csf("id")]]['booked_conv_fac'];
					}
					else{
						$convertedBQty= $break_qty_sum_arr[$row[csf('break_id')]]['cal_qty']/$trims_groups_arr[$row[csf("id")]]['booked_conv_fac'];
					}

					/************** This condition is for few days . Previous data syncronized .****************/
					if($variable_status==2 && $convertedBQty==''){
						$convertedBQty= $break_qty_sum_arr[$row[csf('break_id')]]['cal_qty']/$trims_groups_arr[$row[csf("id")]]['booked_conv_fac'];
					}


				 $transfer_quantity_in=$transfer_in_arr[$row[csf('section')]][$row[csf('trim_group')]][$row[csf('description')]][$row[csf('color_id')]][$row[csf('size_id')]][$row[csf('order_uom')]]['transfer_quantity_in'];
				 
				  $transfer_quantity_out=$transfer_out_arr[$row[csf('section')]][$row[csf('trim_group')]][$row[csf('description')]][$row[csf('color_id')]][$row[csf('size_id')]][$row[csf('order_uom')]]['transfer_quantity_out'];

					/*if($over_receive_limit!=0 || $over_receive_limit!='')
					{
						$overConvertedBQty =(($convertedBQty*$over_receive_limit)/100)+$convertedBQty;
						$overOrderQuantity=$overConvertedBQty;// (($row[csf('order_quantity')]*$over_receive_limit)/100)+$row[csf('order_quantity')] ;
						$orderQuantity=$row[csf('order_quantity')];
					} else {
						$overConvertedBQty = $convertedBQty;

						$overOrderQuantity= $row[csf('order_quantity')];
						$orderQuantity= $overOrderQuantity;
					}*/

					if($over_receive_limit!=0 || $over_receive_limit!='')
					{
						//$netOverConvertedBQty =(($convertedBQty*$over_receive_limit)/100)+$convertedBQty;
						$netOverConvertedBQty =(($row[csf('order_quantity')]*$over_receive_limit)/100)+$row[csf('order_quantity')];
						
						$overOrderQuantity=$netOverConvertedBQty;// (($row[csf('order_quantity')]*$over_receive_limit)/100)+$row[csf('order_quantity')] ;
						$orderQuantity=$row[csf('order_quantity')];
					} else {
						
						$overOrderQuantity= $row[csf('order_quantity')];
						$orderQuantity= $overOrderQuantity;
					}
					$overConvertedBQty = $convertedBQty;
				}

				if(is_nan($convertedBQty)) $convertedBQty=0; else $convertedBQty = $convertedBQty;
				//echo $row[csf("break_id")].'=='.$delevery_qty_trims_arr[$row[csf("wo_type")]][$row[csf("break_id")]]['delevery_qty'].'=='.$row[csf('delevery_qty')].'++';
				//echo $row[csf("wo_type")].'=='.$row[csf("break_id")].'=='.$row[csf("delevery_qty")].'#';
				$cumDelvQty=($delevery_qty_trims_arr[$row[csf("wo_type")]][$row[csf("break_id")]]['delevery_qty']-$row[csf('delevery_qty')]);
				
				$overQty=$overOrderQuantity-$orderQuantity;
				$del_bal=($orderQuantity-($cumDelvQty+$row[csf('delevery_qty')]));
				//$del_bal=($overOrderQuantity-$row[csf('delevery_qty')]);
				//echo $del_bal."=".$orderQuantity."=".$cumDelvQty."=".$row[csf('delevery_qty')].'++';
				if(($overOrderQuantity*1==$del_bal*1) || ($overOrderQuantity*1 < $del_bal*1)){
					$shipStatus=1;
				} else if(($overOrderQuantity*1 > $del_bal*1) && $del_bal!=0){
					$shipStatus=2; 
				} else {
					$shipStatus=3;
				} 

				$production_balance_Qty=number_format($overConvertedBQty,4,'.','')-number_format($transfer_quantity_out,4,'.','');
				$DeliverableQty=number_format($production_balance_Qty+$transfer_quantity_in,4,'.','');

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
					<td align="left" ><p><? echo $row[csf('buyer_po_no')]; ?></p></td>
					<td align="left" ><p><? echo $row[csf('buyer_style_ref')]; ?></p></td>
					<td align="left" ><p><? echo $trims_section[$row[csf('section')]]; ?></p></td>
					<td align="left" ><p><? echo $trims_groups_arr[chop($row[csf('id')],",")]['item_group']; ?></p></td>	
					<td align="left" ><p><? echo $row[csf('style')]; ?></p></td>		
					<td align="left" ><p><? echo $row[csf('description')]; ?></p></td>
					<td ><p><? echo $color_library[$row[csf('gmts_color_id')]] ?></p></td>
					<td ><p><? echo $size_arr[$row[csf('gmts_size_id')]] ?></p></td>
					<td ><p><? echo $color_library[$row[csf('color_id')]] ?></p></td>
					<td ><p><?
					 if($row[csf('size_id')] > 0)
                        echo $size_arr[$row[csf('size_id')]];
                     else
                        echo $row[csf('size_name')];
					 ?></p></td>
					<td ><p><? echo $row[csf('ply')] ?></p></td>					
					<td><? echo $unit_of_measurement[$row[csf('order_uom')]] ?></td>
					<td align="right" id="txtWorkOrderQuantity_<? echo $tblRow; ?>"  title="<?= $orderQuantity; ?>"><? echo number_format($overOrderQuantity,4,'.',''); ?></td>
					<td align="right" id="txtJobQuantity_<? echo $tblRow; ?>" title="<? echo $row[csf('pjob_qty')]; ?>"><? echo number_format($row[csf('pjob_qty')],4,'.',''); ?></td>
					<td align="right" id="txtOrderQuantity_<? echo $tblRow; ?>" title="<?= number_format($DeliverableQty,4,'.','');?>"><? 
					//if($variable_status_del==2) $overConvertedBQty=$orderQuantity ; else $overConvertedBQty=$overConvertedBQty;
					echo number_format($overConvertedBQty,4,'.',''); ?></td>
                      <td align="right" id="txtdeliverableQuantity_<? echo $tblRow; ?>" title="<?= $DeliverableQty;//$production_balance_Qty=number_format($overConvertedBQty,4,'.','')-number_format($transfer_quantity_out,4,'.','');;?>"><? 
					  $deliverableQuantity=number_format($DeliverableQty,4,'.','')-number_format($cumDelvQty,4,'.',''); 
					  echo number_format($deliverableQuantity,4,'.','');
					  //echo number_format($DeliverableQty,4,'.','')-number_format($cumDelvQty,4,'.',''); ?></td>
					<td align="right" id="txtPrevQty_<? echo $tblRow; ?>" title="<?= number_format($cumDelvQty,4,'.','');?>"><? echo number_format($cumDelvQty,4,'.',''); ?></td>
					<td><input id="txtCurQty_<? echo $tblRow; ?>" name="txtCurQty[]" onKeyUp="cal_values(<? echo $tblRow; ?>);" value="<? echo number_format($row[csf('delevery_qty')],4,'.','');?>" type="text" style="width:70px"  class="text_boxes_numeric"  /></td>
                     <td align="right" id="txtWoRate_<? echo $tblRow; ?>" title="<?= number_format($row[csf('rate')],4,'.','');?>"><? echo number_format($row[csf('rate')],4,'.',''); ?></td>
                    <td align="right" id="txtWoAmaount_<? echo $tblRow; ?>" title="<?= number_format($row[csf('delevery_qty')],4,'.','')*number_format($row[csf('rate')],4,'.','');?>"><? 
					//echo number_format($row[csf('delevery_qty')],4,'.','')*number_format($row[csf('rate')],4,'.',''); 
					 $amounts=number_format($row[csf('delevery_qty')],4,'.','')*number_format($row[csf('rate')],4,'.',''); echo number_format($amounts,4);
					
					?></td>
	                <td align="right"><input id="noOfRollBag_<? echo $tblRow; ?>" name="noOfRollBag[]" value="<? echo $row[csf('no_of_roll_bag')]; ?>" type="text" style="width:70px"  class="text_boxes"  /></td>
					<td align="right" id="txtDelvBalance_<? echo $tblRow; ?>" title="new"> <? echo number_format($del_bal,4,'.','');?></td> 
					
					<td id="cboshipingStatus_<? echo $tblRow; ?>" title="<?= $shipStatus;?>"><? echo $delivery_status[$shipStatus]; ?></td>
					<td><input id="txtRemarksDtls_<? echo $tblRow; ?>" name="txtRemarksDtls[]" value="<? echo $row[csf('remarks')]; ?>" type="text" class="text_boxes" style="width:77px" />
						<input id="hdnDtlsUpdateId_<? echo $tblRow; ?>" name="hdnDtlsUpdateId[]" type="hidden" value="<? echo $row[csf('delDtlsId')]; ?>" class="text_boxes_numeric" style="width:40px" />
						<input id="txtOverWOquantity_<? echo $tblRow; ?>" name="txtOverWOquantity[]" type="hidden" value="<? echo number_format($overOrderQuantity,4,'.',''); ?>" class="text_boxes_numeric" style="width:40px" />
						<input type="hidden" id="hdn_break_down_id_<? echo $tblRow; ?>" name="hdn_break_down_id[]" value="<? echo $row[csf('break_id')]; ?>">
					</td>
					<td><? echo create_drop_down( "cboStatus_".$tblRow, 60, $row_status,"", '', "-- select --",$row[csf('status_active')],"", 0,'','','','','','',"cboStatus[]"); ?>	
					</td>
				</tr>
				<?
			}
		
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
	////###### material over receive control only for receive ####////
	//$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$cbo_company_name and variable_list=23 and category =4 order by id");
	$variable_set_invent=array();
	$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;
	$variable_status_del=return_field_value("process_production_qty_control","variable_setting_trim_prod","company_name=$cbo_company_name and variable_list =3 and is_deleted = 0 and status_active = 1");
	
	     $received_id=str_replace("'",'',$received_id);		
 		 $wo_type=return_field_value("wo_type","subcon_ord_mst","id=$received_id  and  is_deleted = 0 and status_active = 1");
 
       // echo "10**".$wo_type; die;

	$production_sql ="select b.id as prod_dtls_id,b.break_id,b.qc_qty,b.job_dtls_id from trims_production_mst a, trims_production_dtls b where a.id=b.mst_id and a.received_id=$received_id and a.status_active=1 and b.status_active=1 and b.is_deleted=0"; 
	//echo "10**".$production_sql; die;
	$production_sql_res=sql_select($production_sql); $production_arr=array(); $break_ids='';
	foreach ($production_sql_res as $row)
	{
		$break_ids=explode(",",$row[csf("break_id")]); $order_quantity='';
		for($i=0; $i<count($break_ids);$i++)
		{
			$production_arr[$break_ids[$i]]['job_dtls_id'] = $row[csf('job_dtls_id')];
			$production_arr[$break_ids[$i]]['prod_dtls_id'] = $row[csf('prod_dtls_id')];
			$production_arr[$break_ids[$i]]['qc_qty'] +=$row[csf('qc_qty')];
			//echo $break_ids[$i]."==".$row[csf("qc_qty")]."++";
			
			//$order_quantity+=$break_qty_arr[$break_ids[$i]]['order_quantity'];
			//$production_arr[$break_ids[$i]]['receive_dtls_id']=$row[csf('receive_dtls_id')];
		}
		/*$production_percent=($row[csf("qc_qty")]*100)/$order_quantity;
		//echo $production_percent."=="; 
		for($i=0; $i<=count($break_ids);$i++)
		{
			//$break_qty_sum_arr[$break_ids[$i]]['sum_qty']=$order_quantity;
			$break_qty_sum_arr[$break_ids[$i]]['cal_qty']+=($production_percent*$break_qty_arr[$break_ids[$i]]['order_quantity'])/100;
		}*/
	} 

	$delivery_sql ="select b.break_down_details_id,b.delevery_qty,trims_del from trims_delivery_mst a, trims_delivery_dtls b where a.id=b.mst_id and a.received_id=$received_id and a.id!=$update_id and a.status_active=1 and b.status_active=1 and b.is_deleted=0";

	//echo $delivery_sql; die;
	$delivery_sql_res=sql_select($delivery_sql); $del_qty_arr=array();
	foreach ($delivery_sql_res as $row)
	{
		$del_qty_arr[$row[csf('break_down_details_id')]]['delevery_qty'] += $row[csf('delevery_qty')];
		$del_qty_arr[$row[csf('break_down_details_id')]]['trims_del'] .= $row[csf('delevery_qty')];
	}
	
	//echo "10**".$over_receive_limit;die;
	if ($operation==0) // Insert Start Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
		
		//$new_del_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'TD', date("Y",time()), 5, "select del_no_prefix,del_no_prefix_num from trims_delivery_mst where entry_form=208 and company_id=$cbo_company_name $insert_date_con order by id desc ", "del_no_prefix", "del_no_prefix_num" ));

		$company=str_replace("'","",$cbo_company_name);
		$id = return_next_id_by_sequence("TRIMS_DELIVERY_MST_PK_SEQ", "trims_delivery_mst", $con);
		
		//$id1=return_next_id( "id", "trims_delivery_dtls",1) ;
		$new_del_no = explode("*", return_next_id_by_sequence("TRIMS_DELIVERY_MST_PK_SEQ", "trims_delivery_mst",$con,1,$company,'TD',208,date("Y",time()),4 ));

		//echo "10**$id"; die;
		//echo "10**<pre>";
		//print_r($new_del_no);die;
		if($db_type==0)
		{
			$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date),'yyyy-mm-dd');
		}
		else
		{
			$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date), "", "",1);
		}
		//$id =return_next_id("id","trims_delivery_mst",1);
		
		$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
		$size_library_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
		//$id3=return_next_id( "id", "trims_delivery_breakdown", 1 ) ;
		$rID3=true;
		$field_array="id, entry_form, trims_del, del_no_prefix, del_no_prefix_num, company_id, location_id, within_group, party_id, party_location, deli_party, deli_party_location, currency_id, delivery_date, received_id, order_id, challan_no, gate_pass_no, remarks,cust_location ,receive_basis, finish_trims ,transfer_trims_status,wo_type, inserted_by, insert_date";

		$data_array="(".$id.", 208, '".$new_del_no[0]."', '".$new_del_no[1]."', '".$new_del_no[2]."', '".$cbo_company_name."', '".$cbo_location_name."', '".$cbo_within_group."', '".$cbo_party_name."', '".$cbo_party_location."', '".$cbo_deli_party_name."', '".$cbo_deli_party_location."', '".$cbo_currency."', '".$txt_delivery_date."','".$received_id."','".$hid_order_id."', '".$txt_challan_no."', '".$txt_gate_pass_no."', '".$txt_remarks."', '".$txt_cust_location."', '".$txt_receive_basis."', '".$is_fabric_trims."', '".$is_transfer_trims."',".$wo_type.",".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";
		
		$txt_job_no=$new_del_no[0];
		// echo "10**".$txt_job_no; die;
		
		if($is_fabric_trims==1)
		{ // Fabric Trims
			if($txt_receive_basis!=7)
			{ // NOT Requisition basis
				$sql="select a.id as break_id, a.job_no_mst, a.description, 0 as gmts_color_id, 0 as gmts_size_id, a.color_id, a.size_id, a.qnty, a.rate, a.amount, a.order_rcv_break_id, a.order_rcv_id ,b.id as dtls_id ,b.order_quantity as wo_qnty, b.order_uom, b.rate, b.amount, b.section, b.sub_section, b.item_group  as trim_group , b.order_rcv_dtls_id, b.order_rcv_id, a.mst_id, c.rcv_qty, c.remarks, c.wo_id, c.wo_dtls_id, c.wo_break_id, c.order_rcv_id  from trims_subcon_ord_breakdown a , trims_subcon_ord_dtls b, trims_receive_dtls c where a.job_no_mst=b.job_no_mst and b.id=a.mst_id and a.id=c.wo_break_id and b.mst_id=c.wo_id and b.order_rcv_id in ( $order_received_id ) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by c.id";
				$order_result=sql_select($sql);
				foreach ($order_result as $rows)
				{
					$order_dtls_arr[$rows[csf("break_id")]]['break_id']			=$rows[csf("break_id")];
					$order_dtls_arr[$rows[csf("break_id")]]['order_id']			=$rows[csf("wo_id")];
					$order_dtls_arr[$rows[csf("break_id")]]['booking_dtls_id']	=$rows[csf("wo_dtls_id")];
					$order_dtls_arr[$rows[csf("break_id")]]['description']		=$rows[csf("description")];
					$order_dtls_arr[$rows[csf("break_id")]]['gmts_color_id']	=$rows[csf("gmts_color_id")];
					$order_dtls_arr[$rows[csf("break_id")]]['gmts_size_id']		=$rows[csf("gmts_size_id")];
					$order_dtls_arr[$rows[csf("break_id")]]['color_id']			=$rows[csf("color_id")];
					$order_dtls_arr[$rows[csf("break_id")]]['color_name']		=$color_library_arr[$rows[csf("color_id")]];
					$order_dtls_arr[$rows[csf("break_id")]]['size_id']			=$rows[csf("size_id")];
					$order_dtls_arr[$rows[csf("break_id")]]['size_name']		=$size_library_arr[$rows[csf("size_id")]];
					$order_dtls_arr[$rows[csf("break_id")]]['order_quantity']	+=$rows[csf("wo_qnty")];
					$order_dtls_arr[$rows[csf("break_id")]]['rate']				=$rows[csf("rate")];
					$order_dtls_arr[$rows[csf("break_id")]]['amount']			=$rows[csf("amount")];
					$order_dtls_arr[$rows[csf("break_id")]]['order_uom']		=$rows[csf("order_uom")];
					$order_dtls_arr[$rows[csf("break_id")]]['section']			=$rows[csf("section")];
					$order_dtls_arr[$rows[csf("break_id")]]['sub_section']		=$rows[csf("sub_section")];
					$order_dtls_arr[$rows[csf("break_id")]]['item_group']		=$rows[csf("trim_group")];
				}
			}
			else{
				// work on later
			}
		}else
		{
			$sql = "select a.id as receive_dtls_id, a.mst_id, a.job_no_mst, a.order_id, a.order_no, a.buyer_po_id, a.booking_dtls_id, b.qnty as order_quantity , a.order_uom,a.booked_uom, a.booked_conv_fac, b.rate, a.amount, a.delivery_date, a.buyer_po_no, a.buyer_style_ref, a.buyer_buyer, a.section,a.sub_section, a.item_group as trim_group, a.rate_domestic,  a.amount_domestic,a.delivery_status,b.item_id, b.gmts_color_id, b.gmts_size_id, b.color_id, b.size_id,b.description,b.id as break_id,b.book_con_dtls_id from subcon_ord_dtls a,subcon_ord_breakdown b where a.id=b.mst_id and a.mst_id in ( $order_received_id )  and a.order_quantity<>0 and a.booked_qty<>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id ASC"; 
			$order_result=sql_select($sql);
			foreach ($order_result as $rows)
			{
				$order_dtls_arr[$rows[csf("break_id")]]['break_id']			=$rows[csf("break_id")];
				$order_dtls_arr[$rows[csf("break_id")]]['booking_dtls_id']	=$rows[csf("booking_dtls_id")];
				$order_dtls_arr[$rows[csf("break_id")]]['receive_dtls_id']	=$rows[csf("receive_dtls_id")];
				$order_dtls_arr[$rows[csf("break_id")]]['order_id']			=$rows[csf("order_id")];
				$order_dtls_arr[$rows[csf("break_id")]]['order_no']			=$rows[csf("order_no")];
				$order_dtls_arr[$rows[csf("break_id")]]['buyer_po_id']		=$rows[csf("buyer_po_id")];
				$order_dtls_arr[$rows[csf("break_id")]]['buyer_po_no']		=$rows[csf("buyer_po_no")];
				$order_dtls_arr[$rows[csf("break_id")]]['buyer_style_ref']	=$rows[csf("buyer_style_ref")];
				$order_dtls_arr[$rows[csf("break_id")]]['buyer_buyer']		=$rows[csf("buyer_buyer")];
				$order_dtls_arr[$rows[csf("break_id")]]['section']			=$rows[csf("section")];
				$order_dtls_arr[$rows[csf("break_id")]]['sub_section']		=$rows[csf("sub_section")];
				$order_dtls_arr[$rows[csf("break_id")]]['item_group']		=$rows[csf("trim_group")];
				$order_dtls_arr[$rows[csf("break_id")]]['description']		=$rows[csf("description")];
				$order_dtls_arr[$rows[csf("break_id")]]['gmts_color_id']	=$rows[csf("gmts_color_id")];
				$order_dtls_arr[$rows[csf("break_id")]]['gmts_size_id']		=$rows[csf("gmts_size_id")];
				$order_dtls_arr[$rows[csf("break_id")]]['color_id']			=$rows[csf("color_id")];
				$order_dtls_arr[$rows[csf("break_id")]]['color_name']		=$color_library_arr[$rows[csf("color_id")]];
				$order_dtls_arr[$rows[csf("break_id")]]['size_id']			=$rows[csf("size_id")];
				$order_dtls_arr[$rows[csf("break_id")]]['size_name']		=$size_library_arr[$rows[csf("size_id")]];
				$order_dtls_arr[$rows[csf("break_id")]]['order_quantity']	+=$rows[csf("order_quantity")];
				$order_dtls_arr[$rows[csf("break_id")]]['rate']				=$rows[csf("rate")];
				$order_dtls_arr[$rows[csf("break_id")]]['order_uom']		=$rows[csf("order_uom")];
				$order_dtls_arr[$rows[csf("break_id")]]['Delivery_status']	=$rows[csf("Delivery_status")];
			}
		}
		
		//echo "10**<pre>";
		//print_r($order_dtls_arr); die;
		
		//echo "10**<pre>";
		//print_r($production_arr); die;
		
		$field_array2="id, mst_id, received_id, booking_dtls_id, receive_dtls_id, job_dtls_id, production_dtls_id,  order_id, order_no, buyer_po_id, buyer_po_no,  buyer_style_ref, buyer_buyer, section,item_group, order_uom, order_quantity, delevery_qty, remarks,description, gmts_color_id, gmts_size_id, color_id, size_id,color_name,size_name,delevery_status,workoder_qty,order_receive_rate, break_down_details_id,no_of_roll_bag, inserted_by, insert_date,job_qty,wo_type";
		$field_array3="delivery_status*updated_by*update_date";
		$field_array5="delivery_status";
		
		//$id1=return_next_id( "id", "trims_delivery_dtls",1) ;
		//echo "10**".$total_row; die;
		$data_array2 ="";  $data_array3="";  $add_commaa=0; $add_commadtls=0; 
		for($i=1; $i<=$total_row; $i++)
		{	
			$shipStatus='';
			$txtPrevQty 			= "txtPrevQty_".$i;
			$txtCurQty 				= "txtCurQty_".$i;	
			$noOfRollBag 			= "noOfRollBag_".$i;
			$txtOrderQuantity		= "txtOrderQuantity_".$i;		
			$txtRemarksDtls 		= "txtRemarksDtls_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$cboStatus				= "cboStatus_".$i;
			$cboshipingStatus	    = "cboshipingStatus_".$i;
			$hdn_break_down_id	    = "hdn_break_down_id_".$i;
			$txtJobQuantity 	    = "txtJobQuantity_".$i;

			$brkID	=str_replace("'",'',$$hdn_break_down_id);
			$hdnJobDtlsId 			= $production_arr[$brkID]['job_dtls_id'];
			$hdnProductionDtlsId 	= $production_arr[$brkID]['prod_dtls_id'];
			$prodQcQty 				= $production_arr[$brkID]['qc_qty'];
			$hdnReceiveDtlsId 		= $order_dtls_arr[$brkID]['receive_dtls_id'];
			$hdnbookingDtlsId 		= $order_dtls_arr[$brkID]['booking_dtls_id'];
			$txtWorkOrderID 		= $order_dtls_arr[$brkID]['order_id'];
			$txtWorkOrder 			= $order_dtls_arr[$brkID]['order_no'];
			$txtbuyerPoId 			= $order_dtls_arr[$brkID]['buyer_po_id'];
			$txtbuyerPo 			= $order_dtls_arr[$brkID]['buyer_po_no'];
			$txtstyleRef 			= $order_dtls_arr[$brkID]['buyer_style_ref'];
			$txtbuyer 				= $order_dtls_arr[$brkID]['buyer_buyer'];
			$cboSection 			= $order_dtls_arr[$brkID]['section'];
			$cboItemGroup 			= $order_dtls_arr[$brkID]['item_group'];
			$txtItem 				= $order_dtls_arr[$brkID]['description'];
			$txtgmtscolorId 		= $order_dtls_arr[$brkID]['gmts_color_id'];
			$txtgmtssizeId 			= $order_dtls_arr[$brkID]['gmts_size_id'];
			$txtcolorID 			= $order_dtls_arr[$brkID]['color_id'];
			$txtcolor 				= $order_dtls_arr[$brkID]['color_name'];
			$txtsizeID 				= $order_dtls_arr[$brkID]['size_id'];
			$txtsize 				= $order_dtls_arr[$brkID]['size_name'];
			$txtWorkOrderQuantity 	= $order_dtls_arr[$brkID]['order_quantity'];
			$hdn_break_down_rate 	= $order_dtls_arr[$brkID]['rate'];
			$cboUom 				= $order_dtls_arr[$brkID]['order_uom'];
			$DELIvery_status 		= $order_dtls_arr[$brkID]['Delivery_status'];
			$prevDelQty 			= $del_qty_arr[$brkID]['delevery_qty'];
			$prevTrimsDel 			= $del_qty_arr[$brkID]['trims_del'];
			if($prevTrimsDel!=''){
				$prevTrimsDel=implode(", ",array_unique(explode(", ",chop($prevTrimsDel),',')));
			}
			

			//$aa	=$hdnReceiveDtlsId;
			/*if($$txtWorkOrderQuantity==$$txtDelvBalance) $shipStatus=1; 
        	else if($$txtWorkOrderQuantity>$$txtDelvBalance) $shipStatus=2; 
        	else if($$txtDelvBalance==0) $shipStatus=3;*/
			/*$totaldoqnty=($$txtCurQty+$$txtPrevQty);
			if($$txtDelvBalance==0) $shipStatus=3;
			if($$txtDelvBalance>0 && $$txtDelvBalance!=$totaldoqnty) $shipStatus=2;
			if($$txtDelvBalance>0 && $$txtDelvBalance==$$txtWorkOrderQuantity) $shipStatus=1;
			if($$txtDelvBalance<0 && $$txtCurQtye>$$txtWorkOrderQuantity) $shipStatus=1;*/
			$WorkOrderQuantity=trim($txtWorkOrderQuantity)*1;
			$CurQty=trim(str_replace("'",'',$$txtCurQty))*1;
			$PrevQty=trim(str_replace("'",'',$$txtPrevQty))*1;
			////////////////////////// over_receive_limit_qnty start
			$Cur_Do_Qty =$CurQty;
			$prev_Do_Qty=$PrevQty;
			$total_Do_Qty=$prev_Do_Qty+$Cur_Do_Qty;
			$woDoQnty=$WorkOrderQuantity;
			$over_receive_limit_qnty = ($over_receive_limit>0)?($over_receive_limit / 100) * $woDoQnty:0;			
			$allow_total_val = $woDoQnty + $over_receive_limit_qnty;

			$orderBalance=$woDoQnty-($prevDelQty+$Cur_Do_Qty);
			$prodBalance=$prodQcQty-($prevDelQty+$Cur_Do_Qty);
			
			//echo "10**".$Cur_Do_Qty."prev_Do_Qty".$prev_Do_Qty."total_Do_Qty".$total_Do_Qty."woDoQnty".$woDoQnty."over_receive_limit_qnty".$over_receive_limit_qnty."allow_total_val".$allow_total_val; 
			//1prev_Do_Qty 2 total_Do_Qty 3 woDoQnty 3.5 over_receive_limit_qnty 0.7 allow_total_val 4.2
			if($variable_status_del!=2)
			{
				if($prodBalance<0 && $prevTrimsDel!='') 
				{
				//echo "40**".$prodBalance."==".$prodQcQty."==".$prevDelQty."==".$Cur_Do_Qty; die;
				echo "40**No Balance Quantity.\nPlease check previous Delivery \nPrevious Delivery System ID = $prevTrimsDel";
				disconnect($con);
				die;
				}
			}

			$overRecvLimitMsg="Over Receive limit = $over_receive_limit% ($over_receive_limit_qnty.)";
			$allow_total_val=number_format($allow_total_val,4,'.','');
			$total_Do_Qty=number_format($total_Do_Qty,4,'.','');
			if($allow_total_val<$total_Do_Qty) {
				//echo "40**".$allow_total_val."==".$txtWorkOrderQuantity."==".$total_Do_Qty."==".$prev_Do_Qty."==".$Cur_Do_Qty; die;
				//0.0000==1.0000==0==1
				$over_msg = ($over_receive_limit>0)?"\nAllowed Quantity = $allow_total_val":"";
				echo "40**Delv. quantity can not be greater than WO quantity.\n\nWO/quantity = $woDoQnty \n$overRecvLimitMsg $over_msg";
				disconnect($con);
				die;
			}
			////////////////////////////////////////////// over_receive_limit_qnty end
			if($WorkOrderQuantity>($CurQty+$PrevQty) && ($CurQty+$PrevQty)>0)
			{
				$shipStatus=2;	
			}
			elseif($WorkOrderQuantity==($CurQty+$PrevQty) || $WorkOrderQuantity <($CurQty+$PrevQty)){
				$shipStatus=3;
			}
			else{
				$shipStatus=1;
			}
			//echo  "10**".$$txtWorkOrderQuantity.'>('.$$txtCurQty.'+'.$$txtPrevQty."___";
			//echo "".$shipStatus;die;
			//$shipStatus=$$cboshipingStatus;
			if(str_replace("'",'',$txtbuyerPoId)=="") $txtbuyerPoId=0; else $txtbuyerPoId=str_replace("'",'',$txtbuyerPoId);
			if ($add_commaa!=0) $data_array2 .=","; $add_comma=0;
			$id1 = return_next_id_by_sequence("TRIMS_DELIVERY_DTLS_PK_SEQ", "trims_delivery_dtls", $con);
			$data_array2 .="(".$id1.",".$id.",'".$received_id."','".$hdnbookingDtlsId."','".$hdnReceiveDtlsId."','".$hdnJobDtlsId."','".$hdnProductionDtlsId."','".$txtWorkOrderID."','".$txtWorkOrder."','".$txtbuyerPoId."','".$txtbuyerPo."','".$txtstyleRef."','".$txtbuyer."','".$cboSection."','".$cboItemGroup."','".$cboUom."',".str_replace(",",'',$$txtOrderQuantity).",".str_replace(",",'',$$txtCurQty).",".$$txtRemarksDtls.",'".$txtItem."','".$txtgmtscolorId."','".$txtgmtssizeId."','".$txtcolorID."','".$txtsizeID."','".$txtcolor."','".$txtsize."','".$shipStatus."','".$txtWorkOrderQuantity."','".$hdn_break_down_rate."','".$brkID."',".str_replace(",",'',$$noOfRollBag).",'".$user_id."','".$pc_date_time."',".str_replace(",",'',$$txtJobQuantity).",".str_replace(",",'',$wo_type).")";
			
			//$id1=$id1+1; 
			$add_commaa++;
			//echo "10**".str_replace("'",'',$hdnReceiveDtlsId); //die;
			if(str_replace("'",'',$hdnReceiveDtlsId)!="")
			{
				if($shipStatus > 1 )
				{
					$data_array3[str_replace("'",'',$hdnReceiveDtlsId)]=explode("*",("".$shipStatus."*".$user_id."*'".$pc_date_time."'"));
					$hdnRcvIdArr[]=str_replace("'",'',$hdnReceiveDtlsId);
				}
				
			}

			if(str_replace("'",'',$$hdn_break_down_id)!="")
			{
				//echo "10**".$brkID."**".$shipStatus;
				if($shipStatus > 1 )
				{
					//echo "**".$brkID."++";
					$data_array5[$brkID]=explode("*",("".$shipStatus.""));
					$hdnBrkIdArr[]=$brkID;
				}
			}
		}
		$flag=1; 
		if($is_fabric_trims !=1)
		{
			if($data_array3!="")
			{
				//echo "10**".bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array3,$data_array3,$hdnRcvIdArr); die;
				$rID3=execute_query(bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array3,$data_array3,$hdnRcvIdArr),1);
				if($flag==1)
				{
					if($rID3) $flag=1; else $flag=0;
				}
				
				
			}
			if($data_array5!="")
			{
				//echo "10**".bulk_update_sql_statement( "subcon_ord_breakdown", "id",$field_array5,$data_array5,$hdnBrkIdArr); die;
				$rID5=execute_query(bulk_update_sql_statement( "subcon_ord_breakdown", "id",$field_array5,$data_array5,$hdnBrkIdArr),1);
				if($flag==1)
				{
					if($rID5) $flag=1; else $flag=0;
				}
				
			}
		}
		
		//echo "10**INSERT INTO trims_delivery_mst (".$field_array.") VALUES ".$data_array; die;
		//echo "10**INSERT INTO trims_delivery_breakdown (".$field_array3.") VALUES ".$data_array3; die;
		//echo "10**INSERT INTO trims_delivery_dtls (".$field_array2.") VALUES ".$data_array2; die;
		
		$rID=sql_insert("trims_delivery_mst",$field_array,$data_array,1);
		if($rID==1) $flag=1; else $flag=0;
 		if($flag==1)
		{
 			$rID2=sql_insert("trims_delivery_dtls",$field_array2,$data_array2,1);
		    if($rID2==1) $flag=1; else $flag=0;
		}
		
		
		//echo "10**".$rID."**".$rID2."**".$rID3."**".$id; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$txt_job_no)."**".$id."**".str_replace("'",'',$txt_order_no)."**".str_replace("'",'',$received_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_job_no)."**".$id."**".str_replace("'",'',$txt_order_no)."**".str_replace("'",'',$received_id);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$txt_job_no)."**".$id."**".str_replace("'",'',$txt_order_no)."**".str_replace("'",'',$received_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_job_no)."**".$id."**".str_replace("'",'',$txt_order_no)."**".str_replace("'",'',$received_id)."**".$data_array2."**".$total_row;
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

		$bill_sql = "select c.mst_id,a.trims_bill from trims_bill_mst a, trims_bill_dtls b,trims_delivery_dtls c,subcon_ord_breakdown d where a.id=b.mst_id and c.id=b.production_dtls_id and c.break_down_details_id=d.id and c.received_id=$received_id and c.mst_id=$update_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by c.mst_id,a.trims_bill"; 
		$bill_sql_res=sql_select($bill_sql);
		if(count($bill_sql_res)>0){
			foreach ($bill_sql_res as $row){
				$bill_nos .=$row[csf("trims_bill")].', ';
			}
			echo "40**Update Not Allowed . Bill Found . ".chop($bill_nos,', '); die;
		}
		
		$color_library_arr=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name"  );
		$size_library_arr=return_library_array( "select id,size_name from lib_size  where status_active=1 and is_deleted=0", "id", "size_name"  );
		if($db_type==0){
			$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date),'yyyy-mm-dd');
		}else{
			$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date), "", "",1);
		}

		$field_array="location_id*within_group*party_id*party_location*deli_party*deli_party_location*currency_id*delivery_date*received_id*order_id*challan_no*gate_pass_no*remarks*cust_location*wo_type*updated_by*update_date";	
		$data_array="'".$cbo_location_name."'*'".$cbo_within_group."'*'".$cbo_party_name."'*'".$cbo_party_location."'*'".$cbo_deli_party_name."'*'".$cbo_deli_party_location."'*'".$cbo_currency."'*'".$txt_delivery_date."'*'".$received_id."'*'".$hid_order_id."'*'".$txt_challan_no."'*'".$txt_gate_pass_no."'*'".$txt_remarks."'*'".$txt_cust_location."'*".$wo_type."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$field_array2="received_id*booking_dtls_id*receive_dtls_id*job_dtls_id*production_dtls_id*order_id*order_no*buyer_po_id*buyer_po_no*buyer_style_ref*buyer_buyer*section*item_group*order_uom*order_quantity*delevery_qty*remarks*description*gmts_color_id* gmts_size_id*color_id*size_id*color_name*size_name*delevery_status*workoder_qty*order_receive_rate* break_down_details_id*no_of_roll_bag*status_active*updated_by*update_date*job_qty*wo_type";

		$field_array3="delevery_status*updated_by*update_date";
		$field_array4="id, mst_id, received_id, booking_dtls_id, receive_dtls_id, job_dtls_id, production_dtls_id,  order_id, order_no, buyer_po_id, buyer_po_no,  buyer_style_ref, buyer_buyer, section,   item_group, order_uom, order_quantity, delevery_qty, remarks,description, gmts_color_id, gmts_size_id, color_id, size_id,color_name,size_name,delevery_status,workoder_qty,order_receive_rate, break_down_details_id,no_of_roll_bag, inserted_by, insert_date ,job_qty,wo_type";
		$field_array5="delivery_status";
		$id1=return_next_id( "id", "trims_delivery_dtls",1) ;
		$add_comma=0;	
		$flag=1;

		$sql = "select a.id as receive_dtls_id, a.mst_id, a.job_no_mst, a.order_id, a.order_no, a.buyer_po_id, a.booking_dtls_id, b.qnty as order_quantity , a.order_uom,a.booked_uom, a.booked_conv_fac, b.rate, a.amount, a.delivery_date, a.buyer_po_no, a.buyer_style_ref, a.buyer_buyer, a.section, a.item_group as trim_group, a.rate_domestic,  a.amount_domestic,a.delivery_status,b.item_id, b.gmts_color_id, b.gmts_size_id, b.color_id, b.size_id,b.description,b.id as break_id,b.book_con_dtls_id from subcon_ord_dtls a,subcon_ord_breakdown b where   a.id=b.mst_id and a.mst_id=$received_id  and a.order_quantity<>0 and a.booked_qty<>0 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0   order by a.id ASC";
		$order_result=sql_select($sql); 
		foreach ($order_result as $rows)
		{
			$order_dtls_arr[$rows[csf("break_id")]]['break_id']			=$rows[csf("break_id")];
			$order_dtls_arr[$rows[csf("break_id")]]['booking_dtls_id']	=$rows[csf("booking_dtls_id")];
			$order_dtls_arr[$rows[csf("break_id")]]['receive_dtls_id']	=$rows[csf("receive_dtls_id")];
			$order_dtls_arr[$rows[csf("break_id")]]['order_id']			=$rows[csf("order_id")];
			$order_dtls_arr[$rows[csf("break_id")]]['order_no']			=$rows[csf("order_no")];
			$order_dtls_arr[$rows[csf("break_id")]]['buyer_po_id']		=$rows[csf("buyer_po_id")];
			$order_dtls_arr[$rows[csf("break_id")]]['buyer_po_no']		=$rows[csf("buyer_po_no")];
			$order_dtls_arr[$rows[csf("break_id")]]['buyer_style_ref']	=$rows[csf("buyer_style_ref")];
			$order_dtls_arr[$rows[csf("break_id")]]['buyer_buyer']		=$rows[csf("buyer_buyer")];
			$order_dtls_arr[$rows[csf("break_id")]]['section']			=$rows[csf("section")];
			$order_dtls_arr[$rows[csf("break_id")]]['item_group']		=$rows[csf("trim_group")];
			$order_dtls_arr[$rows[csf("break_id")]]['description']		=$rows[csf("description")];
			$order_dtls_arr[$rows[csf("break_id")]]['gmts_color_id']	=$rows[csf("gmts_color_id")];
			$order_dtls_arr[$rows[csf("break_id")]]['gmts_size_id']		=$rows[csf("gmts_size_id")];
			$order_dtls_arr[$rows[csf("break_id")]]['color_id']			=$rows[csf("color_id")];
			$order_dtls_arr[$rows[csf("break_id")]]['color_name']		=$color_library_arr[$rows[csf("color_id")]];
			$order_dtls_arr[$rows[csf("break_id")]]['size_id']			=$rows[csf("size_id")];
			$order_dtls_arr[$rows[csf("break_id")]]['size_name']		=$size_library_arr[$rows[csf("size_id")]];
			$order_dtls_arr[$rows[csf("break_id")]]['order_quantity']	=$rows[csf("order_quantity")];
			$order_dtls_arr[$rows[csf("break_id")]]['rate']				=$rows[csf("rate")];
			$order_dtls_arr[$rows[csf("break_id")]]['order_uom']		=$rows[csf("order_uom")];
			$order_dtls_arr[$rows[csf("break_id")]]['Delivery_status']	=$rows[csf("Delivery_status")];
		}

		/*$production_sql ="select b.id as prod_dtls_id,b.break_id,b.qc_qty,b.job_dtls_id from trims_production_mst a, trims_production_dtls b where a.id=b.mst_id and a.received_id=$received_id and a.status_active=1 and b.status_active=1 and b.is_deleted=0";
		$production_sql_res=sql_select($production_sql); $production_arr=array(); $break_ids='';
		foreach ($production_sql_res as $row)
		{
			$break_ids=explode(",",$row[csf("break_id")]); $order_quantity='';
			for($i=0; $i<count($break_ids);$i++)
			{
				$production_arr[$break_ids[$i]]['job_dtls_id'] = $row[csf('job_dtls_id')];
				$production_arr[$break_ids[$i]]['prod_dtls_id'] = $row[csf('prod_dtls_id')];
			}
		} */

		for($i=1; $i<=$total_row; $i++)
		{	
			$shipStatus='';
			$txtPrevQty 			= "txtPrevQty_".$i;
			$txtCurQty 				= "txtCurQty_".$i;	
			$noOfRollBag 			= "noOfRollBag_".$i;	
			$txtOrderQuantity		= "txtOrderQuantity_".$i;		
			$txtRemarksDtls 		= "txtRemarksDtls_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$cboStatus				= "cboStatus_".$i;
			$cboshipingStatus	    = "cboshipingStatus_".$i;
			$hdn_break_down_id	    = "hdn_break_down_id_".$i;
			$txtJobQuantity	        = "txtJobQuantity_".$i;

			$brkID	=str_replace("'",'',$$hdn_break_down_id);
			$hdnJobDtlsId 			= $production_arr[$brkID]['job_dtls_id'];
			$hdnProductionDtlsId 	= $production_arr[$brkID]['prod_dtls_id'];
			$prodQcQty 				= $production_arr[$brkID]['qc_qty'];
			$hdnReceiveDtlsId 		= $order_dtls_arr[$brkID]['receive_dtls_id'];
			$hdnbookingDtlsId 		= $order_dtls_arr[$brkID]['booking_dtls_id'];
			$txtWorkOrderID 		= $order_dtls_arr[$brkID]['order_id'];
			$txtWorkOrder 			= $order_dtls_arr[$brkID]['order_no'];
			$txtbuyerPoId 			= $order_dtls_arr[$brkID]['buyer_po_id'];
			$txtbuyerPo 			= $order_dtls_arr[$brkID]['buyer_po_no'];
			$txtstyleRef 			= $order_dtls_arr[$brkID]['buyer_style_ref'];
			$txtbuyer 				= $order_dtls_arr[$brkID]['buyer_buyer'];
			$cboSection 			= $order_dtls_arr[$brkID]['section'];
			$cboItemGroup 			= $order_dtls_arr[$brkID]['item_group'];
			$txtItem 				= $order_dtls_arr[$brkID]['description'];
			$txtgmtscolorId 		= $order_dtls_arr[$brkID]['gmts_color_id'];
			$txtgmtssizeId			= $order_dtls_arr[$brkID]['gmts_size_id'];
			$txtcolorID 			= $order_dtls_arr[$brkID]['color_id'];
			$txtcolor 				= $order_dtls_arr[$brkID]['color_name'];
			$txtsizeID 				= $order_dtls_arr[$brkID]['size_id'];
			$txtsize 				= $order_dtls_arr[$brkID]['size_name'];
			$txtWorkOrderQuantity 	= $order_dtls_arr[$brkID]['order_quantity'];
			$hdn_break_down_rate 	= $order_dtls_arr[$brkID]['rate'];
			$cboUom 				= $order_dtls_arr[$brkID]['order_uom'];
			$DELIvery_status 		= $order_dtls_arr[$brkID]['Delivery_status'];
			$prevDelQty 			= $del_qty_arr[$brkID]['delevery_qty'];
			$prevTrimsDel 			= $del_qty_arr[$brkID]['trims_del'];
			if($prevTrimsDel!=''){
				$prevTrimsDel=implode(", ",array_unique(explode(", ",chop($prevTrimsDel),',')));
			}
			/*if($$txtWorkOrderQuantity==$$txtDelvBalance) $shipStatus=1; 
        	else if($$txtWorkOrderQuantity>$$txtDelvBalance) $shipStatus=2; 
        	else if($$txtDelvBalance==0) $shipStatus=3;*/
			/*$totaldoqnty=($$txtCurQty+$$txtPrevQty);
			if($$txtDelvBalance==0) $shipStatus=3;
			if($$txtDelvBalance>0 && $$txtDelvBalance!=$totaldoqnty) $shipStatus=2;
			if($$txtDelvBalance>0 && $$txtDelvBalance==$$txtWorkOrderQuantity) $shipStatus=1;
			if($$txtDelvBalance<0 && $$txtCurQtye>$$txtWorkOrderQuantity) $shipStatus=1;*/
			//$shipStatus=$$cboshipingStatus;

			$WorkOrderQuantity=trim($txtWorkOrderQuantity)*1;
			$CurQty=trim(str_replace("'",'',$$txtCurQty))*1;
			$PrevQty=trim(str_replace("'",'',$$txtPrevQty))*1;
			//echo "10**".$WorkOrderQuantity.'=='.$$txtWorkOrderQuantity.'=='.$txtWorkOrderQuantity.'+';
			
			////////////////////////// over_receive_limit_qnty start
			$Cur_Do_Qty =$CurQty;
			$prev_Do_Qty=$PrevQty;
			$total_Do_Qty=$prev_Do_Qty+$Cur_Do_Qty;
			$woDoQnty=$WorkOrderQuantity;
			$over_receive_limit_qnty = ($over_receive_limit>0)?($over_receive_limit / 100) * $woDoQnty:0;			
			$allow_total_val = $woDoQnty + $over_receive_limit_qnty;
			//echo "10**".$woDoQnty.'=='.$prodQcQty.'=='.$prevDelQty.'=='.$Cur_Do_Qty.'=='.$CurQty.'=='.$PrevQty.'=='.$total_Do_Qty.'=='.$allow_total_val;
			//10**0==1==0==1==0
			//echo "10**".$Cur_Do_Qty."prev_Do_Qty".$prev_Do_Qty."total_Do_Qty".$total_Do_Qty."woDoQnty".$woDoQnty."over_receive_limit_qnty".$over_receive_limit_qnty."allow_total_val".$allow_total_val; 
			//1prev_Do_Qty 2 total_Do_Qty 3 woDoQnty 3.5 over_receive_limit_qnty 0.7 allow_total_val 4.2

			$orderBalance=$woDoQnty-($prevDelQty+$Cur_Do_Qty);
			$prodBalance=$prodQcQty-($prevDelQty+$Cur_Do_Qty);
			////-396.1645
			//-164.6736==4.3264====169

			if($variable_status_del!=2)
			{
				if($prodBalance<0 && $prevTrimsDel!='') {
				//echo "40**".$prodBalance."==".$prodQcQty."==".$prevDelQty."==".$Cur_Do_Qty; die;
				echo "40**No Balance Quantity.\nPlease check previous Delivery \nPrevious Delivery System ID = $prevTrimsDel";
				disconnect($con);
				die;
				}
			}

			/*$overRecvLimitMsg="Over Receive limit = $over_receive_limit% ($over_receive_limit_qnty.)";
			if($allow_total_val<$total_Do_Qty) 
			{
				$over_msg = ($over_receive_limit>0)?"\nAllowed Quantity = $allow_total_val":"";
				echo "40**Delv. quantity can not be greater than WO quantity.\n\nWO/quantity = $woDoQnty \n$overRecvLimitMsg $over_msg";
				die;
			}*/
			////////////////////////////////////////////// over_receive_limit_qnty end
			if($WorkOrderQuantity>($CurQty+$PrevQty) && ($CurQty+$PrevQty)>0){
				$shipStatus=2;	
			}
			elseif($WorkOrderQuantity==($CurQty+$PrevQty) || $WorkOrderQuantity <($CurQty+$PrevQty)){
				$shipStatus=3;
			}
			else{
				$shipStatus=1;
			}
			
			$aa	=str_replace("'",'',$$hdnDtlsUpdateId);
			$bb	=$hdnReceiveDtlsId;
			$brkID	=str_replace("'",'',$$hdn_break_down_id);

			if($txtbuyerPoId=="") $txtbuyerPoId=0; else $txtbuyerPoId=$txtbuyerPoId;
			if ($add_commaa!=0) $data_array4 .=","; $add_comma=0;
			//echo "10**".str_replace("'",'',$$hdnDtlsUpdateId).'++'; 
			if(str_replace("'",'',$$hdnDtlsUpdateId)!="")
			{
				$data_array2[$aa]=explode("*",("'".$received_id."'*'".$hdnbookingDtlsId."'*'".$hdnReceiveDtlsId."'*'".$hdnJobDtlsId."'*'".$hdnProductionDtlsId."'*'".$txtWorkOrderID."'*'".$txtWorkOrder."'*'".$txtbuyerPoId."'*'".$txtbuyerPo."'*'".$txtstyleRef."'*'".$txtbuyer."'*'".$cboSection."'*'".$cboItemGroup."'*'".$cboUom."'*".str_replace(",",'',$$txtOrderQuantity)."*".str_replace(",",'',$$txtCurQty)."*".$$txtRemarksDtls."*'".$txtItem."'*'".$txtgmtscolorId."'*'".$txtgmtssizeId."'*'".$txtcolorID."'*'".$txtsizeID."'*'".$txtcolor."'*'".$txtsize."'*".$shipStatus."*'".$txtWorkOrderQuantity."'*'".$hdn_break_down_rate."'*'".$brkID."'*".str_replace(",",'',$$noOfRollBag)."*".$$cboStatus."*".$user_id."*'".$pc_date_time."'*".str_replace(",",'',$$txtJobQuantity)."*".str_replace(",",'',$wo_type).""));
				$hdn_dtls_id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
			}
			else
			{
				$data_array4 .="(".$id1.",".$update_id.",'".$received_id."','".$hdnbookingDtlsId."','".$hdnReceiveDtlsId."','".$hdnJobDtlsId."','".$hdnProductionDtlsId."','".$txtWorkOrderID."','".$txtWorkOrder."','".$txtbuyerPoId."','".$txtbuyerPo."','".$txtstyleRef."','".$txtbuyer."','".$cboSection."','".$cboItemGroup."','".$cboUom."',".str_replace(",",'',$$txtOrderQuantity).",".str_replace(",",'',$$txtCurQty).",".$$txtRemarksDtls.",'".$txtItem."','".$txtgmtscolorId."','".$txtgmtssizeId."','".$txtcolorID."','".$txtsizeID."','".$txtcolor."','".$txtsize."','".$shipStatus."','".$txtWorkOrderQuantity."','".$hdn_break_down_rate."','".$brkID."',".str_replace(",",'',$$noOfRollBag).",'".$user_id."','".$pc_date_time."',".str_replace(",",'',$$txtJobQuantity).",".str_replace(",",'',$wo_type).")";
			
				$id1=$id1+1; $add_commaa++;
			}

			if(str_replace("'",'',$$hdnReceiveDtlsId)!="")
			{
				if($shipStatus >1 )
				{
					$data_array3[$bb]=explode("*",("".$shipStatus."*".$user_id."*'".$pc_date_time."'"));
					$hdnRcvIdArr[]=str_replace("'",'',$$hdnReceiveDtlsId);
				}
			}
			if(str_replace("'",'',$$hdn_break_down_id)!="")
			{
				//if($DELivery_status!=1 )
				if($shipStatus >1 )
				{
					$data_array5[$brkID]=explode("*",("".$shipStatus.""));
					$hdnBrkIdArr[]=str_replace("'",'',$$hdn_break_down_id);
				}
			}
		}
		//die;
		//echo "10**".bulk_update_sql_statement( "trims_delivery_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr); die;
		$rID=sql_update("trims_delivery_mst",$field_array,$data_array,"id",$update_id,0);  
		if($rID) $flag=1; else $flag=0;
		//echo "10**".$received_id; die;
		if($data_array2!="" && $flag==1)
		{
			//echo "10**".bulk_update_sql_statement( "trims_delivery_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr);die;
			   
			$rID2=execute_query(bulk_update_sql_statement( "trims_delivery_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr),1);
			if($rID2) $flag=1; else $flag=0;
				
			
		}

		if($data_array3!="" && $flag==1)
		{
			//echo "10**".bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array3,$data_array3,$hdnRcvIdArr);
			$rID3=execute_query(bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array3,$data_array3,$hdnRcvIdArr),1);
			if($rID3) $flag=1; else $flag=0;
		}

		if($data_array4!="" && $flag==1)
		{
			//echo "10**INSERT INTO trims_delivery_dtls (".$field_array4.") VALUES ".$data_array4; die;
			$rID4=sql_insert("trims_delivery_dtls",$field_array4,$data_array4,1);
			if($rID4==1) $flag=1; else $flag=0;
		}
		//echo "10**"; print_r($hdnBrkIdArr); die;
		if($data_array5!="" && $flag==1)
		{
			//echo "10**".bulk_update_sql_statement( "subcon_ord_breakdown", "id",$field_array5,$data_array5,$hdnBrkIdArr); die;
			$rID5=execute_query(bulk_update_sql_statement( "subcon_ord_breakdown", "id",$field_array5,$data_array5,$hdnBrkIdArr),1);
			if($rID5) $flag=1; else $flag=0;
		}
		//echo "10**".$rID."**".$rID2."**".$rID3."**".$rID4."**".$rID5; die;
		//10**1******0**1
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_dalivery_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".str_replace("'",'',$received_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_dalivery_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".str_replace("'",'',$received_id);
			}
		}
		else if($db_type==2)
		{  
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$txt_dalivery_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".str_replace("'",'',$received_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_dalivery_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".str_replace("'",'',$received_id);
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
		$flag='';
		//echo "10**select trims_bill from trims_bill_mst where challan_no=$txt_dalivery_no and status_active=1 and is_deleted=0"; die;
		//$chk_next_transaction=return_field_value("trims_bill","trims_bill_mst","challan_no like'%$txt_dalivery_no%' and status_active=1 and is_deleted=0","trims_bill");
		$chk_booking_id=return_field_value("booking_id","inv_receive_master","booking_id=$hid_order_id and status_active=1 and is_deleted=0","booking_id");
		$booking_no=return_field_value("booking_no","inv_receive_master","booking_id=$hid_order_id and status_active=1 and is_deleted=0","booking_no");
		//echo "10**".$chk_booking_id; die;
		
		/*if($chk_next_transaction !="")
		{ 
			echo "18**Delete not allowed. Bill Found. Bill No.".$chk_next_transaction;
			disconnect($con);
			die;
		}
		*/
		$bill_sql = "select c.mst_id,a.trims_bill from trims_bill_mst a, trims_bill_dtls b,trims_delivery_dtls c,subcon_ord_breakdown d where a.id=b.mst_id and c.id=b.production_dtls_id and c.break_down_details_id=d.id and c.received_id=$received_id and c.mst_id=$update_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by c.mst_id,a.trims_bill"; 
		$bill_sql_res=sql_select($bill_sql);
		if(count($bill_sql_res)>0){
			foreach ($bill_sql_res as $row){
				$bill_nos .=$row[csf("trims_bill")].', ';
			}
			echo "40**Delete Not Allowed . Bill Found . ".chop($bill_nos,', '); die;
		}
		/*else if($chk_booking_id !="")
		{ 
			echo "18**Delete not allowed. Receive Found. Work Order No.".$booking_no; die;
		}*/
		else
		{
			for($i=1; $i<=$total_row; $i++)
			{	
				$txtWorkOrderQuantity	= "txtWorkOrderQuantity_".$i;
				$txtCurQty 				= "txtCurQty_".$i;
				$txtPrevQty 			= "txtPrevQty_".$i;
				$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
				$hdnReceiveDtlsId 		= "hdnReceiveDtlsId_".$i;
				$hdn_break_down_id	    = "hdn_break_down_id_".$i;	
				$WorkOrderQuantity=trim(str_replace("'",'',$$txtWorkOrderQuantity))*1;
				$CurQty=trim(str_replace("'",'',$$txtCurQty))*1;
				$PrevQty=trim(str_replace("'",'',$$txtPrevQty))*1;
				
				if($WorkOrderQuantity>($PrevQty-$CurQty) && ($PrevQty-$CurQty)>0){
					$shipStatus=2;	
				}
				elseif($WorkOrderQuantity==($PrevQty-$CurQty) || $WorkOrderQuantity <($PrevQty-$CurQty)){
					$shipStatus=3;
				}
				else{
					$shipStatus=1;
				}
				
				$aa	=str_replace("'",'',$$hdnDtlsUpdateId);
				$bb	=str_replace("'",'',$$hdnReceiveDtlsId);
				$cc	=str_replace("'",'',$$hdn_break_down_id);

				if(str_replace("'",'',$$hdnReceiveDtlsId)!="")
				{
					$data_array3[$bb]=explode("*",("".$shipStatus."*".$user_id."*'".$pc_date_time."'"));
					$hdnRcvIdArr[]=str_replace("'",'',$$hdnReceiveDtlsId);
				}
				if(str_replace("'",'',$$hdn_break_down_id)!="")
				{
					$data_array5[$cc]=explode("*",("".$shipStatus.""));
					$hdnBrkIdArr[]=str_replace("'",'',$$hdn_break_down_id);
				}
			}
			$field_array="status_active*is_deleted*updated_by*update_date";
			$field_array3="delivery_status*updated_by*update_date";
			$field_array5="delivery_status";
			$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			$rID=sql_update("trims_delivery_mst",$field_array,$data_array,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; 
			$rID1=sql_update("trims_delivery_dtls",$field_array,$data_array,"mst_id",$update_id,1); 
			if($rID1) $flag=1; else $flag=0;
			if($data_array3!="")
			{
				//echo "10**".bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array3,$data_array3,$hdnRcvIdArr);
				$rID3=execute_query(bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array3,$data_array3,$hdnRcvIdArr),1);
				if($rID3) $flag=1; else $flag=0;
			}
			if($data_array5!="")
			{
				//echo "10**".bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array3,$data_array3,$hdnRcvIdArr);
				$rID5=execute_query(bulk_update_sql_statement( "subcon_ord_breakdown", "id",$field_array5,$data_array5,$hdnBrkIdArr),1);
				if($rID5) $flag=1; else $flag=0;
			}
		}
		//echo "10**".$rID."**".$rID1."**".$rID3."**".$rID5."**".$flag; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$txt_dalivery_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".str_replace("'",'',$received_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_dalivery_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".str_replace("'",'',$received_id);
			}
		}
		else if($db_type==2)
		{  
			if($flag==1)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$txt_dalivery_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".str_replace("'",'',$received_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_dalivery_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".str_replace("'",'',$received_id);
			}
		}
		disconnect($con);
		die;
	}
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

		$qry_result=sql_select( "select id, mst_id, order_id, job_no_mst, book_con_dtls_id, description, color_id, size_id, qnty, rate, amount from subcon_ord_breakdown where job_no_mst='$data[1]'  and status_active=1 and is_deleted=0 " );	
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
						echo create_drop_down( "txtbuyer_".$tblRow, 100, "select id, buyer_name from lib_buyer where status_active=1","id,buyer_name", 1, "-- select --",$buyer_po_arr[$row[csf('po_break_down_id')]]['buyer_name'], "",$disable_dropdown,'','','','','','',"txtbuyer[]"); 
					}
					else
					{
						?>
						<input id="txtbuyer_<? echo $tblRow; ?>" name="txtbuyer[]" value="<? echo $buyer_buyer; ?>" class="text_boxes" type="text"  style="width:87px" $disabled />
						<?
					}
					?>
				</td>
				<td><? echo create_drop_down( "cboSection_".$tblRow, 90, $trims_section,"", 1, "-- select Section --",$row[csf('section')],'',1,'','','','','','',"cboSection[]"); ?></td>			
				<td><? echo create_drop_down( "cboItemGroup_".$tblRow, 90, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- select --",$row[csf('trim_group')], "",1,'','','','','','',"cboItemGroup[]"); ?></td>
				<td><? echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- select --",$order_uom,"", 1,'','','','','','',"cboUom[]"); ?>	</td>
				<td><input id="txtOrderQuantity_<? echo $tblRow; ?>" name="txtOrderQuantity[]" value="<? echo number_format($row[csf('wo_qnty')],4,'.',''); ?>" class="text_boxes_numeric" type="text"  style="width:60px"  placeholder="" readonly /></td>
				<!-- Previous Delv Qty 	Curr. Delv Qnty 	Claim Qnty -->
				<td><input id="txtPrevQty_<? echo $tblRow; ?>" name="txtPrevQty[]" value="<? echo number_format($row[csf('rate')],4); ?>" type="text"  class="text_boxes_numeric" style="width:60px" readonly/></td>
				<td><input id="txtCurQty_<? echo $tblRow; ?>" name="txtCurQty[]"  value="<? echo number_format($row[csf('amount')],4); ?>" type="text" style="width:70px"  class="text_boxes_numeric" disabled /></td>
                 <td align="right"><input id="noOfRollBag_<? echo $tblRow; ?>" name="noOfRollBag[]" value="" type="text" style="width:70px"  class="text_boxes"  /></td>
                
				<td><input id="txtClaimQty_<? echo $tblRow; ?>" name="txtClaimQty[]" value="<? echo number_format($domRate,4); ?>" type="text"  class="text_boxes_numeric" style="width:57px" <? echo $disabled ?> /></td>
				<td><input id="txtRemarksDtls_<? echo $tblRow; ?>" name="txtRemarksDtls[]" value="<? echo number_format($domAmount,4); ?>" type="text"  class="text_boxes_numeric" style="width:77px" <? echo $disabled ?> />
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
            <td><? echo create_drop_down( "cboSection_1", 90, $trims_section,"id,section_name", 1, "-- select Section --","",'',0,'','','','','','',"cboSection[]"); ?></td>
            <td><? echo create_drop_down( "cboItemGroup_1", 90, "select id, item_name from lib_item_group where item_category=4 and  status_active=1","id,item_name", 1, "-- select --",$selected, "",0,'','','','','','',"cboItemGroup[]"); ?>	</td>
            <td><? echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 1, "-- select --",2,1, 1,'','','','','','',"cboUom[]"); ?>	</td>
            <td><input id="txtOrderQuantity_1" name="txtOrderQuantity[]" class="text_boxes_numeric" type="text"  style="width:60px"  readonly /></td>
            <td><input id="txtPrevQty_1" name="txtPrevQty[]" type="text"  class="text_boxes_numeric" style="width:60px" readonly /></td>
            <td><input id="txtCurQty_1" name="txtCurQty[]" type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td>
             <td align="right"><input id="noOfRollBag_1" name="noOfRollBag[]" value="" type="text" style="width:70px"  class="text_boxes"  /></td> 
            <td><input id="txtClaimQty_1" name="txtClaimQty[]" type="text"  class="text_boxes_numeric" style="width:57px" readonly /></td> 
            <td><input id="txtRemarksDtls_1" name="txtRemarksDtls[]" type="text"  class="text_boxes_numeric" style="width:77px" readonly  />
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
	$data=explode("_",$data);
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
			var within_group = $('#cbo_within_group').val();
			load_drop_down( 'trims_delivery_entry_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td_del' );
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
			else if(val==4)
			{
				$('#search_by_td').html('Buyer Po');
			}
			else if(val==5)
			{
				$('#search_by_td').html('Buyer Style');
			}
			else if(val==6)
			{
				$('#search_by_td').html('Job No');
			}
		}
	</script>
	</head>
	<body onLoad="fnc_load_party_popup(<? echo "$data[0]";?>,<? echo "$data[3]";?>)">
	<div align="center" style="width:100%;" >
	    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        <table width="940" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
	            <thead> 
	                <tr>
	                    <th colspan="9"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
	                </tr>
	                <tr>               	 
	                    <th width="140" class="must_entry_caption">Company Name</th>
	                    <th width="60">Within Group</th>                           
	                    <th width="140">Party Name</th>
	                    <th width="80">Search By</th>
	                    <th width="100" id="search_by_td">System ID</th>
	                    <th width="60">Year</th>
	                    <th width="170">Date Range</th>                            
	                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
	                </tr>
	            </thead>
	            <tbody>
	                <tr class="general">
	                    <td><input type="hidden" id="selected_job">  <!--  echo $data;-->
	                        <? 
	                        echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
	                    </td>
	                    <td>
	                        <?php echo create_drop_down( "cbo_within_group", 60, $yes_no,"", 0, "--  --", $data[3], "fnc_load_party_popup(1,this.value);",0 ); ?>
	                    </td>
	                    <td id="buyer_td_del">
	                        <? echo create_drop_down( "cbo_party_name", 150, "","", 1, "-- select Party --", $data[2], "fnc_load_party_popup(1,this.value);" );   	 
	                        ?>
	                    </td>
	                    <td>
	                    	<?
	                            $search_by_arr=array(1=>"System ID",2=>"W/O No",4=>"Buyer Po",5=>"Buyer Style",6=>"Job no");
	                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
	                        ?>
	                    </td>
	                    <td align="center">
	                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
	                    </td>
	                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- select --", date('Y'), "",0 ); ?></td>
	                    <td align="center">
	                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
	                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
	                    </td>
	                    <td align="center">
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value, 'create_delivery_search_list_view', 'search_div', 'trims_delivery_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
	                    </tr>
	                    <tr>
	                        <td colspan="9" align="center" valign="middle">
	                            <? echo load_month_buttons();  ?>
	                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
	                        </td>
	                    </tr>
	                    <tr>
	                        <td colspan="9" align="center" valign="top" id=""><div id="search_div"></div></td>
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

		if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please select Company First."; die; }
		//echo $search_type; die;
		$job_cond=""; $style_cond=""; $po_cond=""; $search_com="";
		if($search_type==1)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com="and a.del_no_prefix_num='$search_str'";
				else if($search_by==2) $search_com="and b.order_no='$search_str'";
				else if ($search_by==4) $search_com=" and b.buyer_po_no = '$search_str' ";
				else if ($search_by==5) $search_com=" and b.buyer_style_ref = '$search_str' ";
				else if ($search_by==6) $search_com=" and c.subcon_job = '$search_str' ";
			}
		}
		else if($search_type==2)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com="and a.del_no_prefix_num like '$search_str%'";  
				else if($search_by==2) $search_com="and b.order_no like '$search_str%'";  
				else if ($search_by==4) $search_com=" and b.buyer_po_no like '$search_str%'";
				else if ($search_by==5) $search_com=" and b.buyer_style_ref like '$search_str%'";  
				else if ($search_by==6) $search_com=" and c.subcon_job like '$search_str%'";  
			}
		}
		else if($search_type==3)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com="and a.del_no_prefix_num like '%$search_str'";  
				else if($search_by==2) $search_com="and b.order_no like '%$search_str'";  
				else if ($search_by==4) $search_com=" and b.buyer_po_no like '%$search_str'";
				else if ($search_by==5) $search_com=" and b.buyer_style_ref like '%$search_str'";  
				else if ($search_by==6) $search_com=" and c.subcon_job like '%$search_str'";  
			}
		}
		else if($search_type==4 || $search_type==0)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com="and a.del_no_prefix_num like '%$search_str%'";  
				else if($search_by==2) $search_com="and b.order_no like '%$search_str%'";  
				else if ($search_by==4) $search_com=" and b.buyer_po_no like '%$search_str%'"; 
				else if ($search_by==5) $search_com=" and b.buyer_style_ref like '%$search_str%'";   
				else if ($search_by==6) $search_com=" and c.subcon_job like '%$search_str%'";   
			}
		}

		/*if($search_str!="" && ($search_by==2 || $search_by==4 || $search_by==5))
		{
			if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'"; 
			if($db_type==0) $id_cond="group_concat(b.id) as id";
			else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id";

			//$job_dtls_ids = return_field_value("$id_cond", "trims_job_card_mst a, trims_job_card_dtls b", "a.entry_form=257 and a.trims_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $search_com_cond", "id");
			$job_dtls_ids = return_field_value("$id_cond", "subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c", "a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 $search_com_cond", "id");
		}
		
		if($db_type==2 && $job_dtls_ids!="") $job_dtls_ids = $job_dtls_ids->load();
		if ($job_dtls_ids!="")
		{
			$job_dtls_ids=array_unique(explode(",",$job_dtls_ids));
			$job_dtls_idsCond=""; $jobDtlsCond="";
			//echo count($job_dtls_ids); die;
			if($db_type==2 && count($job_dtls_ids)>=999)
			{
				$chunk_arr=array_chunk($job_dtls_ids,999);
				foreach($chunk_arr as $val)
				{
					$ids=implode(",",$val);
					if($job_dtls_idsCond=="")
					{
						$job_dtls_idsCond.=" and ( b.job_dtls_id in ( $ids) ";
					}
					else
					{
						$job_dtls_idsCond.=" or  b.job_dtls_id in ( $ids) ";
					}
				}
				$job_dtls_idsCond.=")";
			}
			else
			{
				$ids=implode(",",$job_dtls_ids);
				$job_dtls_idsCond.=" and b.job_dtls_id in ($ids) ";
			}
		}
		else if($job_dtls_ids=='' && ($search_str!="" && ($search_by==2 || $search_by==4 || $search_by==5)))
		{
			echo "Not Found"; die;
		}*/	

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
		
		
		$sql= "SELECT a.id, a.trims_del, a.del_no_prefix, a.del_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.delivery_date, a.received_id, a.order_id, a.challan_no, a.gate_pass_no,b.order_no ,$ins_year_cond as year 
		from trims_delivery_mst a, trims_delivery_dtls b left join subcon_ord_mst c on b.order_no= c.order_no and c.status_active=1 
		where a.entry_form=208 and a.id=b.mst_id and a.status_active=1 and b.status_active=1  and (b.order_no is not null)  $delivery_date $company $year_cond $party_id_cond $withinGroup $search_com 
		group by a.id, a.trims_del, a.del_no_prefix, a.del_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.delivery_date, a.received_id, a.order_id, a.challan_no, a.gate_pass_no,b.order_no ,a.insert_date order by a.id DESC";
		// echo $sql;
		$data_array=sql_select($sql);
		?>
	    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="820" >
	        <thead>
	            <th width="30">SL</th>
	            <th width="150">Delivery No</th>
	            <th width="150">Work Order No</th>
	            <th width="80">Year</th>
	            <th width="170">Challan No.</th>
	            <th width="80">Delivery Date</th>
	            <th> Within Group</th>
	        </thead>
	        </table>
	        <div style="width:820px; max-height:270px;overflow-y:scroll;" >	 
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_po_list">
	        <tbody>
	            <? 
	            $i=1;
	            foreach($data_array as $row)
	            {  
	                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                ?>
	                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('trims_del')].'_'.$row[csf('received_id')]; ?>")' style="cursor:pointer" >
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="150"><? echo $row[csf('trims_del')]; ?></td>
	                    <td width="150"><? echo $row[csf('order_no')]; ?></td>
	                    <td width="80" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
	                    <td width="170"><? echo $row[csf('challan_no')]; ?></td>
	                    <td width="80"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
	                    <td style="text-align:center;"><? echo $yes_no[$row[csf('within_group')]]; ?></td>
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
		$sql="select a.id, a.trims_del, a.del_no_prefix, a.del_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id, a.deli_party, a.deli_party_location, a.delivery_date, a.received_id, a.order_id, a.challan_no, a.gate_pass_no, a.remarks,a.receive_basis,a.finish_trims,a.cust_location from trims_delivery_mst a where a.entry_form=208 and a.id=$data and a.status_active=1 ";
		//

		$nameArray=sql_select( $sql );
		foreach ($nameArray as $row)
		{
			$order_no = return_field_value("order_no", "subcon_ord_mst", "id=".$row[csf("received_id")]."", "order_no");

			echo "document.getElementById('txt_dalivery_no').value 			= '".$row[csf("trims_del")]."';\n";
			echo "document.getElementById('txt_cust_location').value 		= '".$row[csf("cust_location")]."';\n";
			echo "document.getElementById('update_id').value 				= '".$row[csf("id")]."';\n";
			echo "document.getElementById('received_id').value 				= '".$row[csf("received_id")]."';\n";
			echo "document.getElementById('txt_order_no').value 			= '".$order_no."';\n";  
			echo "document.getElementById('hid_order_id').value 			= '".$row[csf("order_id")]."';\n";  
			echo "document.getElementById('txt_receive_basis').value 		= '".$row[csf("receive_basis")]."';\n";  
			echo "document.getElementById('is_fabric_trims').value 		= '".$row[csf("finish_trims")]."';\n";  

			echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_id")]."';\n";
			echo "document.getElementById('cbo_within_group').value 		= '".$row[csf("within_group")]."';\n";  
			
			echo "document.getElementById('txt_challan_no').value 			= '".$row[csf("challan_no")]."';\n";  
			echo "document.getElementById('txt_gate_pass_no').value 		= '".$row[csf("gate_pass_no")]."';\n";  
			echo "document.getElementById('cbo_currency').value 			= '".$row[csf("currency_id")]."';\n";  
			echo "document.getElementById('txt_remarks').value 				= '".$row[csf("remarks")]."';\n";  
			
			echo "fnc_load_party(1,'".$row[csf("within_group")]."');\n";
			echo "document.getElementById('cbo_party_name').value			= ".$row[csf("party_id")].";\n";
			echo "fnc_load_party(2,'".$row[csf("within_group")]."');\n";
			echo "document.getElementById('cbo_party_location').value		= ".$row[csf("party_location")].";\n";

			echo "load_drop_down( 'requires/trims_delivery_entry_controller', '".$row[csf("company_id")]."'+'_'+'".$row[csf("within_group")]."', 'load_drop_down_delivery_com', 'delivery_td');\n";
			//echo "fnc_load_party(3,'".$row[csf("within_group")]."');\n";
			echo "document.getElementById('cbo_deli_party_name').value		= '".$row[csf("deli_party")]."';\n";
			//echo "fnc_load_party(3,'".$row[csf("within_group")]."');\n";
			echo "load_drop_down( 'requires/trims_delivery_entry_controller', '".$row[csf("deli_party")]."'+'_'+3, 'load_drop_down_location', 'dparty_location_td');\n";
			echo "document.getElementById('cbo_deli_party_location').value	= '".$row[csf("deli_party_location")]."';\n";
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
		//echo $data;die;
		$data=explode('*',$data);
		$cbo_template_id=$data[6];
		$color_library=return_library_array( "select id,color_name from lib_color where status_active=1", "id", "color_name" );
		$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1",'id','size_name');
		$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
		$sql_company = sql_select("select * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
	  	foreach($sql_company as $company_data) 
	  	{
			if($company_data[csf('plot_no')]!='')$plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
			if($company_data[csf('level_no')]!='')$level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
			if($company_data[csf('road_no')]!='')$road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
			if($company_data[csf('block_no')]!='')$block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
			if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
			if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
			if($company_data[csf('country_id')]!=0)$country = $country_arr[$company_data[csf('country_id')]];else $country='';
			
			$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
		}
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$company_plot_no_arr=return_library_array( "select id, plot_no from lib_company",'id','plot_no');
		$buyer_po_arr=array();
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
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
				top: 20%;
			}
			/* .opacity_3
			{
				opacity:0.3; 
				position: absolute; 
				font-size: 400px; 
				left: 40%; 
				top: 20%;
			} */					
			
			@media print {
				.page-break	{ display: block; page-break-after: always;}
			}
			
			#table_1,#table_2{  background-position: center;background-repeat: no-repeat; }
			#table_1{background-image:url(../../../img/bg-1.jpg);}
			#table_2{background-image:url(../../../img/bg-2.jpg); }
			/* #table_3{background-image:url(../../../img/bg-3.jpg);} */
			
		</style>
		<?
		//echo "select id, entry_form, trims_del, del_no_prefix, del_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id,  delivery_date, received_id, order_id, challan_no, gate_pass_no, remarks,inserted_by from trims_delivery_mst where id= $data[1]";
		//$sql_mst = sql_select("select id, entry_form, trims_del, del_no_prefix, del_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id,  delivery_date, received_id, order_id, challan_no, gate_pass_no, remarks,inserted_by,cust_location from trims_delivery_mst where id= $data[1]");
		
		$sql_mst = sql_select("SELECT a.id, a.entry_form, a.trims_del, a.del_no_prefix, a.del_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.delivery_date, a.received_id, a.order_id, a.challan_no, a.gate_pass_no, a.remarks,a.inserted_by,a.cust_location, b.receive_dtls_id
		FROM trims_delivery_mst a, trims_delivery_dtls b
		where a.id=b.mst_id and a.id=$data[1]");
		$jobDtlsId = $sql_mst[0][csf("receive_dtls_id")];

		$jobData = sql_select("SELECT e.buyer_tb
		FROM subcon_ord_dtls c, subcon_ord_mst e
		where e.id=c.mst_id and c.id=$jobDtlsId");

		$inserted_by=$sql_mst[0][csf("inserted_by")]; 
		
		if($data[2]==1)
		{
			$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
			$party_loc_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_location")]];
		}
		else
		{
			$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
			$party_loc_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_id")]];
		}
		
		$fac_merchant_arr=return_library_array( "select id, team_marchant from subcon_ord_mst",'id','team_marchant');
		$fac_merchant=$fac_merchant_arr[$sql_mst[0][csf("received_id")]];

		$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
		//$copy_print = 3;
		//for($k=1; $k <= $copy_print; $k++)
		//{
	$k=0;	
	$copy_no=$data[7]; //for Dynamic Copy here 
	for($cid=1; $cid<=$copy_no; $cid++)
	{
			 $k++;
			 if($cid==1){
				$st="st Copy";
			 }elseif($cid==2){
				$st="nd Copy";
			 }elseif($cid==3){
				$st="rd Copy";
			 }else{
				$st="th Copy";
			 }
		?>
	        
	    <div style="width:1250px" class="page-break">
	        <table width="100%" id="table_<? echo $cid;?>">
				<tr>
					<td rowspan="2" width="200">
						<img src="../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
					</td>
	            	<td style="font-size:20px;" align="center"><strong>
						<? echo $company_arr[$data[0]]; ?></strong>
	                </td>
	                <td align="right" width="100">
						<? 
						echo "<b><h2> $cid $st</h2></b>";
						/*else if($k==3){
						echo "3rd Copy";
						}*/
						?> 
					</td>
	            </tr>
	            <tr>
	            	<td style="font-size:large" align="center"><? echo $company_address; ?></td>
					<!-- <td align="center">
						<?
						/*$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, vat_number,city from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
						foreach ($nameArray as $result)
						{ 
							?>
							<? echo $result[csf('city')]; ?><br>
							<b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
						}*/
						?> 
					</td> -->
	        		<td id="barcode_img_id_<? echo $k; ?>"></td>
				</tr>
				<tr>
	            	<td>&nbsp;</td>
	            	<td style="font-size:20px;" align="center"> <strong><? //echo $data[3]." Challan"; ?>Accessories Challan</strong></td>
	                <td>&nbsp;</td>
	            </tr> 
	            <tr>
	            	<td>&nbsp;</td>
	            	<td>&nbsp;</td>
	                <td>&nbsp;</td>
	            </tr> 
	        </table>
	        <br>
			<table class="rpt_table" width="100%" cellspacing="1" >
	            <tr>
	                <td valign="top" width="100"><strong> Delivery To</strong></td>
	                <td valign="top" width="150">:<strong> <? echo $party_arr[$sql_mst[0][csf("party_id")]]; ?></strong></td>
	                <td valign="top" width="250">&nbsp;</td>
	                <td valign="top" width="120"><strong>Challan No. </strong></td>
	                <td valign="top"><strong>: <? echo $data[5]; ?></strong></td>
	            </tr>
	            <tr>
	            	<td valign="top" width="120">Address</td>
	                <td valign="top">: <? echo $party_location; ?> </td>
	                <td valign="top" width="250">&nbsp;</td>
	                <td valign="top" width="100">Delivery Date</td>
	                <td valign="top" width="150">: <? echo change_date_format($sql_mst[0][csf("delivery_date")],'yyyy-mm-dd'); ?></td>
	            </tr>
	            <tr>
	            	<td valign="top" width="100">WO NO.</td>
	                <td valign="top" width="150">: <? echo $data[4];//$order_no_trims_arr[$sql_mst[0][csf("received_id")]]['order_no']; ?></td>
	                <td valign="top" width="250">&nbsp;</td>
	                <td valign="top" width="100">Remarks</td>
	                <td valign="top" width="150">: <? echo $sql_mst[0][csf("remarks")]; ?></td>
	            </tr>
	            <tr>
	            	<td valign="top" width="100">Customer Location</td>
	                <td valign="top" width="150">:<?=$sql_mst[0][csf("cust_location")];?></td>
	                <td valign="top" width="250">&nbsp;</td>
	                <td valign="top" width="100">Factory Merchant.</td>
	                <td valign="top" width="150">: <? echo $fac_merchant; ?></td>
	            </tr>
				<tr>
	            	<td valign="top" width="100">Trims Booking</td>
	                <td valign="top" width="150">:<?=$jobData[0][csf("buyer_tb")];?></td>
	                <!-- <td valign="top" width="250">&nbsp;</td>
	                <td valign="top" width="100">Factory Merchant.</td>
	                <td valign="top" width="150">: <? echo $fac_merchant; ?></td> -->
	            </tr>
	      	</table>
	         <br>
	      	<table  class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
	      		<thead>
		            <tr>
		            	<th width="40">SL</th>
	                    <th width="130">Cust. PO</th>
	                    <th width="160">Buyers Style Ref.</th>
	                    <th width="130">Buyer's Buyer </th>
	                    <th width="90">Style Name</th>
	                    <th width="80">Section</th>
		                <th width="90">Item Group</th>
		                <th width="140">Item Description</th>	
						<th width="80">Gmts Color </th>
		                <th width="70">Gmts Size</th>
	                    <th width="80">Item Color </th>
		                <th width="70">Item Size</th>				
		                <th width="60">Order UOM</th>
	                    <th width="70">WO Qty.</th>
		                <th width="80">Cum. Delv Qty</th>
		                <th width="80">Curr. Delv Qty</th>
		                <th width="80">No of Roll/Bag</th>
		                <th width="80">Delv Balance Qty</th>
		                <th>Remarks</th>
		            </tr>
	            </thead>
	            <tbody>
				<?
				$i = 1;
				//$remarks_arr=return_library_array( "select id,remarks from trims_delivery_mst", "id", "remarks" );
				$total_quantity=0;$total_delevery_quantity=0;$curr_delevery_quantity=0;$delevery_Balance_quantity=0; $total_roll_bag=0;
				$style_ref_no=return_library_array( "select job_no,style_ref_no from fabric_sales_order_mst", "Job_no", "Style_ref_no" );
			    $sql = "SELECT a.id, a.mst_id, a.booking_dtls_id, a.receive_dtls_id, a.job_dtls_id, a.production_dtls_id,  a.order_id, a.order_no, a.buyer_po_id, a.buyer_po_no,  a.buyer_style_ref, a.buyer_buyer, a.section, a.item_group as trim_group, a.order_uom, a.order_quantity,   a.delevery_qty, a.claim_qty, a.remarks, a.gmts_color_id, a.gmts_size_id,a.color_id, a.size_id, a.no_of_roll_bag, a.description, a.delevery_status, a.color_name,a.size_name, a.workoder_qty,break_down_details_id, b.style from trims_delivery_dtls a, subcon_ord_breakdown b where a.mst_id='$data[1]' and a.break_down_details_id = b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id ASC";
			  	/* 	$delevery_qty_trims_arr=array();
				$pre_sql ="select job_dtls_id, sum(delevery_qty) as delevery_qty  from trims_delivery_dtls where status_active=1 and is_deleted=0 group by job_dtls_id";
				$pre_sql_res=sql_select($pre_sql);
				foreach ($pre_sql_res as $row)
				{
					$delevery_qty_trims_arr[$row[csf("job_dtls_id")]]['delevery_qty']=$row[csf("delevery_qty")];
					
				}
				unset($pre_sql_res);
				*/
		
				$delevery_qty_trims_arr=array();
				$pre_sql ="select break_down_details_id, sum(delevery_qty) as delevery_qty  from trims_delivery_dtls where status_active=1 and is_deleted=0 group by break_down_details_id";
				$pre_sql_res=sql_select($pre_sql);
				foreach ($pre_sql_res as $row)
				{
					$delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']=$row[csf("delevery_qty")];
				}
				unset($pre_sql_res);
				$data_array=sql_select($sql);
				foreach($data_array as $row)
				{
				?>
	                <tr>
	                <td><?php echo $i; ?></td>
	                <td><p><?php echo $row[csf('buyer_po_no')]; ?></p></td>
	                <td style="word-break: break-all"><p><?php echo $row[csf('buyer_style_ref')]; ?></p></td>
	                <td><p><?php if($data[2]==1)
					{  echo $buyer_arr[$row[csf('buyer_buyer')]]; } else { echo $row[csf('buyer_buyer')];  } ?></p></td>
	                <td><?php echo $row[csf("style")]; ?></td>
	                <td><?php echo $trims_section[$row[csf('section')]]; ?></td>
	                <td><p><?php echo $item_group_arr[$row[csf('trim_group')]]; ?></p></td>
	                <td><p><?php echo $row[csf('description')]; ?></p></td>	
					<td><p><?php echo $color_library[$row[csf('gmts_color_id')]]; ?></p> </td>
	                <td><p><?php echo $size_arr[$row[csf('gmts_size_id')]]; ?></p></td>
	                <td><p><?php echo $row[csf('color_name')]; ?></p> </td>
	                <td><p><?php echo $row[csf('size_name')]; ?></p></td>				
	                <td><?php echo $unit_of_measurement[$row[csf('order_uom')]]; $unique_uom[$row[csf('order_uom')]]=$row[csf('order_uom')]; ?></td>
	                <td align="right"><?php echo number_format($row[csf('workoder_qty')],4); $total_quantity += $row[csf('workoder_qty')]; ?></td>
	                <td align="right"><?php echo  
					$cumDelvQty=$delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']-$row[csf('delevery_qty')];  
					$total_delevery_quantity += $delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']-$row[csf('delevery_qty')];
					 ?></td>
	                <td align="right"><?php echo $row[csf('delevery_qty')];  $curr_delevery_quantity += $row[csf('delevery_qty')];  ?></td>
	                <td align="center"><?php $total_roll_bag+=$row[csf('no_of_roll_bag')]; echo  $row[csf('no_of_roll_bag')]; ?></td>
	                <td align="right"><?php echo number_format($row[csf('workoder_qty')]-($row[csf('delevery_qty')]+$cumDelvQty),4); $delevery_Balance_quantity += $row[csf('workoder_qty')]-($row[csf('delevery_qty')]+$cumDelvQty);  ?></td>
	                
	                <td><?php echo  $row[csf('remarks')]; ?></td>
	                </tr>
				<?
				$i++;
	            } 
	         	if(count($unique_uom)==1){ 
				?>
	            <tr> 
					<td colspan="12"><strong>&nbsp;&nbsp;</strong></td>
					<td align="right"><strong>Total:</strong></td>
					<td align="right"><strong><? echo number_format($total_quantity,2); ?></strong></td>
					<td align="right"><strong><? echo number_format($total_delevery_quantity,2); ?></strong></td>
					<td align="right"><strong><? echo number_format($curr_delevery_quantity,2); ?></strong></td>
					 <td align="right"><strong><? echo number_format($total_roll_bag,2); ?></strong></td>
					<td align="right"><strong><? echo number_format($delevery_Balance_quantity,2); ?></strong></td>
	               
	                <td><strong>&nbsp;&nbsp;</strong></td>
				</tr>
	            <? } ?>
	        </tbody> 
	    </table>
		<?
			$user_lib_name=return_library_array("select id,user_full_name from user_passwd where id=$inserted_by", "id", "user_full_name");
			echo signature_table(174, $data[0], "1200px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
	    ?>	
	    </div>
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
	    generateBarcode("<? echo $data[5]; ?>");
	    </script>
	   <?
		}
	 	exit();
	}
	if($action=="challan_print_old") 
	{
		extract($_REQUEST);
		//echo $data;die;
		$data=explode('*',$data);
		$cbo_template_id=$data[6];
		$sql_company = sql_select("select * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
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
		
		
		
		$buyer_po_arr=array();
		
		
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
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
					top: 20%;
				}
				/* .opacity_3
				{
					opacity:0.3; 
					position: absolute; 
					font-size: 400px; 
					left: 40%; 
					top: 20%;
				} */					
				
				@media print {
					.page-break	{ display: block; page-break-after: always;}
				}
				
				#table_1,#table_2{  background-position: center;background-repeat: no-repeat; }
				#table_1{background-image:url(../../../img/bg-1.jpg);}
				#table_2{background-image:url(../../../img/bg-2.jpg); }
				/* #table_3{background-image:url(../../../img/bg-3.jpg);} */
				
			</style>
			<?
			//echo "select id, entry_form, trims_del, del_no_prefix, del_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id,  delivery_date, received_id, order_id, challan_no, gate_pass_no, remarks,inserted_by from trims_delivery_mst where id= $data[1]";
				$sql_mst = sql_select("select id, entry_form, trims_del, del_no_prefix, del_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id,  delivery_date, received_id, order_id, challan_no, gate_pass_no, remarks,inserted_by from trims_delivery_mst where id= $data[1]");
				$inserted_by=$sql_mst[0][csf("inserted_by")];
				
		
		
		if($data[2]==1)
		{
			$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
			$party_loc_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_location")]];
		}
		else
		{
			$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
			$party_loc_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_id")]];
		}
		
		//$lib_location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
		$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
		//$copy_print = 3;
		//for($k=1; $k <= $copy_print; $k++)
		//{
		$k=0;	
		$copy_no=array(1,2); //for Dynamic Copy here 
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
	            	<td style="font-size:20px;" align="center"><strong>
						<? echo $company_arr[$data[0]]; ?></strong>
	                </td>
	                <td align="right" width="100">
						<? 
						if($k==1){
						echo "<b><h2>1st Copy</h2></b>";
						}
						else if($k==2){
						echo "<b><h2>2nd Copy</h2></b>";
						}
						/*else if($k==3){
						echo "3rd Copy";
						}*/
						?> 
					</td>
	            </tr>
	            <tr>
					<td align="center">
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
	        		<td id="barcode_img_id_<? echo $k; ?>"></td>
				</tr>
				<tr>
	            	<td>&nbsp;</td>
	            	<td style="font-size:20px;" align="center"> <strong><? echo $data[3]; ?></strong></td>
	                <td>&nbsp;</td>
	            </tr> 
	            <tr>
	            	<td>&nbsp;</td>
	            	<td>&nbsp;</td>
	                <td>&nbsp;</td>
	            </tr> 
	        </table>
	        <br>
			<table class="rpt_table" width="100%" cellspacing="1" >
	            <tr>
	                <td valign="top" width="100"><strong> Delivery To</strong></td>
	                <td valign="top" width="150">:<strong> <? echo $party_arr[$sql_mst[0][csf("party_id")]]; ?></strong></td>
	                <td valign="top" width="250">&nbsp;</td>
	                <td valign="top" width="120"><strong>Challan No. </strong></td>
	                <td valign="top"><strong>: <? echo $data[5]; ?></strong></td>
	            </tr>
	            <tr>
	            	<td valign="top" width="120">Address</td>
	                <td valign="top">: <? echo $party_location; ?> </td>
	                <td valign="top" width="250">&nbsp;</td>
	                <td valign="top" width="100">Delivery Date</td>
	                <td valign="top" width="150">: <? echo change_date_format($sql_mst[0][csf("delivery_date")],'yyyy-mm-dd'); ?></td>
	            </tr>
	            <tr>
	            	<td valign="top" width="100">WO NO.</td>
	                <td valign="top" width="150">: <? echo $data[4];//$order_no_trims_arr[$sql_mst[0][csf("received_id")]]['order_no']; ?></td>
	            </tr>
	      	</table>
	         <br>
	      	<table  class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
	      		<thead>
		            <tr>
		            	<th width="40">SL</th>
	                    <th width="130">Cust. PO</th>
	                    <th width="130">Buyer's Buyer </th>
	                    <th width="80">Section</th>
		                <th width="90">Item Group</th>
		                <th width="140">Item Description</th>	
	                    <th width="80">Item Color </th>
		                <th width="70">Item Size</th>				
		                <th width="60">Order UOM</th>
	                    <th width="70">WO Qty.</th>
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
				$total_quantity=0;$total_delevery_quantity=0;$curr_delevery_quantity=0;$delevery_Balance_quantity=0;
				$style_ref_no=return_library_array( "select job_no,style_ref_no from fabric_sales_order_mst", "Job_no", "Style_ref_no" );
				$sql = "select id, mst_id, booking_dtls_id, receive_dtls_id, job_dtls_id, production_dtls_id,  order_id, order_no, buyer_po_id, buyer_po_no,  buyer_style_ref, buyer_buyer, section,   item_group as trim_group, order_uom, order_quantity,   delevery_qty, claim_qty, remarks,color_id, size_id, 
				description, delevery_status, color_name,size_name,workoder_qty,break_down_details_id from trims_delivery_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0 order by id ASC";
			  	/* 	$delevery_qty_trims_arr=array();
				$pre_sql ="select job_dtls_id, sum(delevery_qty) as delevery_qty  from trims_delivery_dtls where status_active=1 and is_deleted=0 group by job_dtls_id";
				$pre_sql_res=sql_select($pre_sql);
				foreach ($pre_sql_res as $row)
				{
					$delevery_qty_trims_arr[$row[csf("job_dtls_id")]]['delevery_qty']=$row[csf("delevery_qty")];
					
				}
				unset($pre_sql_res);
				*/
		
				$delevery_qty_trims_arr=array();
				$pre_sql ="select break_down_details_id, sum(delevery_qty) as delevery_qty  from trims_delivery_dtls where status_active=1 and is_deleted=0 group by break_down_details_id";
				$pre_sql_res=sql_select($pre_sql);
				foreach ($pre_sql_res as $row)
				{
					$delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']=$row[csf("delevery_qty")];
				}
				unset($pre_sql_res);
				$data_array=sql_select($sql);
				foreach($data_array as $row)
				{
				?>
	                <tr>
	                <td><?php echo $i; ?></td>
	                <td><p><?php echo $row[csf('buyer_po_no')]; ?></p></td>
	                <td><p><?php if($data[2]==1)
					{  echo $buyer_arr[$row[csf('buyer_buyer')]]; } else { echo $row[csf('buyer_buyer')];  } ?></p></td>
	                <td><?php echo $trims_section[$row[csf('section')]]; ?></td>
	                <td><p><?php echo $item_group_arr[$row[csf('trim_group')]]; ?></p></td>
	                <td><p><?php echo $row[csf('description')]; ?></p></td>	
	                <td><p><?php echo $row[csf('color_name')]; ?></p> </td>
	                <td><p><?php echo $row[csf('size_name')]; ?></p></td>				
	                <td><?php echo $unit_of_measurement[$row[csf('order_uom')]]; $unique_uom[$row[csf('order_uom')]]=$row[csf('order_uom')]; ?></td>
	                <td align="right"><?php echo $row[csf('workoder_qty')]; $total_quantity += $row[csf('workoder_qty')]; ?></td>
	                <td align="right"><?php echo  
					$cumDelvQty=$delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']-$row[csf('delevery_qty')];  
					$total_delevery_quantity += $delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']-$row[csf('delevery_qty')];
					 ?></td>
	                <td align="right"><?php echo $row[csf('delevery_qty')];  $curr_delevery_quantity += $row[csf('delevery_qty')];  ?></td>
	                <td align="right"><?php echo number_format($row[csf('workoder_qty')]-($row[csf('delevery_qty')]+$cumDelvQty),4); $delevery_Balance_quantity += $row[csf('workoder_qty')]-($row[csf('delevery_qty')]+$cumDelvQty);  ?></td>
	                <td><?php echo $row[csf('remarks')]; ?></td>
	                </tr>
				<?
				$i++;
	            } 
	         	if(count($unique_uom)==1){ 
				?>
	            <tr> 
					<td colspan="8"><strong>&nbsp;&nbsp;</strong></td>
					<td align="right"><strong>Total:</strong></td>
					<td align="right"><strong><? echo number_format($total_quantity,2); ?></strong></td>
					<td align="right"><strong><? echo number_format($total_delevery_quantity,2); ?></strong></td>
					<td align="right"><strong><? echo number_format($curr_delevery_quantity,2); ?></strong></td>
					<td align="right"><strong><? echo number_format($delevery_Balance_quantity,2); ?></strong></td>
	                <td><strong>&nbsp;&nbsp;</strong></td>
				</tr>
	            <? } ?>
	        </tbody> 
	    </table>
		<?
			$user_lib_name=return_library_array("select id,user_full_name from user_passwd where id=$inserted_by", "id", "user_full_name");
			echo signature_table(174, $data[0], "1200px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
	    ?>	
	    </div>
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
	    generateBarcode("<? echo $data[5]; ?>");
	    </script>
	   <?
		}
	 	exit();
	}

	if($action=="challan_print2") 
	{
		extract($_REQUEST);
		//echo $data;die;
		$data=explode('*',$data);
		$cbo_template_id=$data[6];
		$sql_company = sql_select("select * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
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
		$buyer_po_arr=array();
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
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
					top: 20%;
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
				
				#table_1,#table_2{  background-position: center;background-repeat: no-repeat; }
				#table_1{background-image:url(../../../img/bg-1.jpg);}
				#table_2{background-image:url(../../../img/bg-2.jpg); }
				/* #table_3{background-image:url(../../../img/bg-3.jpg);} */
				
			</style>
			<?
			//echo "select id, entry_form, trims_del, del_no_prefix, del_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id,  delivery_date, received_id, order_id, challan_no, gate_pass_no, remarks,inserted_by from trims_delivery_mst where id= $data[1]";
				$sql_mst = sql_select("select id, entry_form, trims_del, del_no_prefix, del_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id,  delivery_date, received_id, order_id, challan_no, gate_pass_no, remarks,inserted_by from trims_delivery_mst where id= $data[1]");
				$inserted_by=$sql_mst[0][csf("inserted_by")];
				
		
		
		if($data[2]==1)
		{
			$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
			$party_loc_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_location")]];
		}
		else
		{
			$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
			$party_loc_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_id")]];
		}
		
		//$lib_location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
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
	            	<td style="font-size:20px;" align="center"><strong>
						<? echo $company_arr[$data[0]]; ?></strong>
	                </td>
	                <td align="right" width="100">
						<? 
						if($k==1){
						echo "<b><h2>1st Copy</h2></b>";
						}
						else if($k==2){
						echo "<b><h2>2nd Copy</h2></b>";
						}
						else if($k==3){
						echo "<b><h2>3rd Copy</h2></b>";
						}
						?> 
					</td>
	            </tr>
	            <tr>
					<td align="center">
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
	        		<td id="barcode_img_id_<? echo $k; ?>"></td>
				</tr>
				<tr>
	            	<td>&nbsp;</td>
	            	<td style="font-size:20px;" align="center"> <strong><? echo $data[3]; ?></strong></td>
	                <td>&nbsp;</td>
	            </tr> 
	            <tr>
	            	<td>&nbsp;</td>
	            	<td>&nbsp;</td>
	                <td>&nbsp;</td>
	            </tr> 
	        </table>
	        <br>
			<table class="rpt_table" width="100%" cellspacing="1" >
	            <tr>
	                <td valign="top" width="100"><strong> Delivery To</strong></td>
	                <td valign="top" width="150">:<strong> <? echo $party_arr[$sql_mst[0][csf("party_id")]]; ?></strong></td>
	                <td valign="top" width="250">&nbsp;</td>
	                <td valign="top" width="120"><strong>Challan No. </strong></td>
	                <td valign="top"><strong>: <? echo $data[5]; ?></strong></td>
	            </tr>
	            <tr>
	            	<td valign="top" width="120">Address</td>
	                <td valign="top">: <? echo $party_location; ?> </td>
	                <td valign="top" width="250">&nbsp;</td>
	                <td valign="top" width="100">Delivery Date</td>
	                <td valign="top" width="150">: <? echo change_date_format($sql_mst[0][csf("delivery_date")],'yyyy-mm-dd'); ?></td>
	            </tr>
	            <tr>
	            	<td valign="top" width="100">WO NO.</td>
	                <td valign="top" width="150">: <? echo $data[4]; ?></td>
	                <td valign="top" width="250">&nbsp;</td>
	                <td valign="top" width="100">Currencey </td>
	                <td valign="top" width="150">: <? echo $currency[$sql_mst[0][csf("currency_id")]];?></td>
	            </tr>
	            <tr>
	            	<td valign="top" width="100">Remarks</td>
	                <td valign="top" width="150">: <? echo $sql_mst[0][csf("remarks")];//$order_no_trims_arr[$sql_mst[0][csf("received_id")]]['order_no']; ?></td>
	                
	            </tr>
	      	</table>
	         <br>
	      	<table  class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
	      		<thead>
		            <tr>
		            	<th width="40">SL</th>
	                    <th width="130">Cust. PO</th> 
	                    <th width="130">Internal Ref.No</th>
	                    <th width="130">Buyer's Buyer </th>
	                    <th width="80">Section</th>
		                <th width="90">Item Group</th>
		                <th width="140">Item Description</th>	
	                    <th width="80">Item Color </th>
		                <th width="70">Item Size</th>				
		                <th width="60">Order UOM</th>
	                    <th width="70">WO Qty.</th>
		                <th width="80">Cum. Delv Qty</th>
		                <th width="80">Curr. Delv Qty</th>
	                     <th width="80">No of Roll/Bag</th>
		                <th width="80">Delv Balance Qty</th>
		                <th>Remarks</th>
		            </tr>
	            </thead>
	            <tbody>
				<?
				$i = 1;
				$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
				$total_quantity=0;$total_delevery_quantity=0;$curr_delevery_quantity=0;$delevery_Balance_quantity=0;
				$style_ref_no=return_library_array( "select job_no,style_ref_no from fabric_sales_order_mst", "Job_no", "Style_ref_no" );
							
				$sql = "select a.id, a.mst_id, a.booking_dtls_id, a.receive_dtls_id, a.job_dtls_id, a.production_dtls_id, a.order_id, a.order_no, b.buyer_po_id, b.buyer_po_no,  a.buyer_style_ref, a.buyer_buyer, a.section, a.item_group as trim_group, a.order_uom, a.order_quantity,a.delevery_qty,a.claim_qty,a.remarks,a.color_id,a.size_id, 
				a.description, a.delevery_status, a.color_name,a.size_name,a.workoder_qty,a.break_down_details_id,a.no_of_roll_bag from trims_delivery_dtls a,subcon_ord_dtls b  where a.mst_id='$data[1]' and  a.receive_dtls_id=b.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id ASC";
				
				$delevery_qty_trims_arr=array();
				$pre_sql ="select break_down_details_id, sum(delevery_qty) as delevery_qty  from trims_delivery_dtls where status_active=1 and is_deleted=0 group by break_down_details_id";
				$pre_sql_res=sql_select($pre_sql);
				foreach ($pre_sql_res as $row)
				{
					$delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']=$row[csf("delevery_qty")];
				}
				unset($pre_sql_res);
				$data_array=sql_select($sql);
				$orderIds='';
				$buyer_po_id_array=array();
				foreach($data_array as $order_row)
				{
					$orderIds.=$order_row[csf('buyer_po_id')].",";
				}
				$orderIds=chop($orderIds,','); 
				$orderIds=implode(",",array_unique(explode(",",$orderIds)));
				//echo $orderIds; die;
				
				//echo "<pre>";
				//print_r($buyer_po_id_array); die;
				
				$piArray=array();
				$sql="select a.id, a.po_number,a.grouping,b.job_no,b.internal_ref from wo_po_break_down a,wo_order_entry_internal_ref b where a.job_no_mst=b.job_no and  a.id in ($orderIds)";
				$po_data=sql_select($sql);
				foreach($po_data as $row)
				{
					$piArray[$row[csf('id')]]['grouping']=$row[csf('internal_ref')];
				}
				
				foreach($data_array as $row)
				{
				?>
	                <tr>
	                <td><?php echo $i; ?></td>
	                <td><p><?php echo $row[csf('buyer_po_no')]; ?></p></td>
	                <td><p><?php echo $piArray[$row[csf('buyer_po_id')]]['grouping']; ?></p></td>
	                <td><p><?php if($data[2]==1)
					{  echo $buyer_arr[$row[csf('buyer_buyer')]]; } else { echo $row[csf('buyer_buyer')];  } ?></p></td>
	                <td><?php echo $trims_section[$row[csf('section')]]; ?></td>
	                <td><p><?php echo $item_group_arr[$row[csf('trim_group')]]; ?></p></td>
	                <td><p><?php echo $row[csf('description')]; ?></p></td>	
	                <td><p><?php echo $row[csf('color_name')]; ?></p> </td>
	                <td><p><?php echo $row[csf('size_name')]; ?></p></td>				
	                <td><?php echo $unit_of_measurement[$row[csf('order_uom')]]; $unique_uom[$row[csf('order_uom')]]=$row[csf('order_uom')]; ?></td>
	                <td align="right"><?php echo number_format($row[csf('workoder_qty')],4); $total_quantity += $row[csf('workoder_qty')]; ?></td>
	                <td align="right"><?php echo number_format( 
					$cumDelvQty=$delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']-$row[csf('delevery_qty')],4);  
					$total_delevery_quantity += $delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']-$row[csf('delevery_qty')];
					 ?></td>
	                <td align="right"><?php echo $row[csf('delevery_qty')];  $curr_delevery_quantity += $row[csf('delevery_qty')];  ?></td>
	                 <td align="right"><?php echo $row[csf('no_of_roll_bag')];?></td>
	                <td align="right"><?php echo number_format($row[csf('workoder_qty')]-($row[csf('delevery_qty')]+$cumDelvQty),4); $delevery_Balance_quantity += $row[csf('workoder_qty')]-($row[csf('delevery_qty')]+$cumDelvQty);  ?></td>
	                <td><?php echo $row[csf('remarks')]; ?></td>
	                </tr>
				<?
				$i++;
	            } 
	         	if(count($unique_uom)==1){ 
				?>
	            <tr> 
					<td colspan="9"><strong>&nbsp;&nbsp;</strong></td>
					<td align="right"><strong>Total:</strong></td>
					<td align="right"><strong><? echo number_format($total_quantity,2); ?></strong></td>
					<td align="right"><strong><? echo number_format($total_delevery_quantity,2); ?></strong></td>
					<td align="right"><strong><? echo number_format($curr_delevery_quantity,2); ?></strong></td>
	                <td><strong>&nbsp;&nbsp;</strong></td>
					<td align="right"><strong><? echo number_format($delevery_Balance_quantity,2); ?></strong></td>
	                <td><strong>&nbsp;&nbsp;</strong></td>
				</tr>
	            <? } ?>
	        </tbody> 
	    </table>
		<?
			$user_lib_name=return_library_array("select id,user_full_name from user_passwd where id=$inserted_by", "id", "user_full_name");
			echo signature_table(174, $data[0], "1200px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
	    ?>	
	    </div>
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
	    generateBarcode("<? echo $data[5]; ?>");
	    </script>
	   <?
	  	 
		 }
	 exit();
		
	 }


	 if($action=="challan_print8") 
	{
		extract($_REQUEST);
		//echo $data;die;
		// print_r($data);
		$data=explode('*',$data);
		$cbo_template_id=$data[6];
		$country_full_name = return_library_array("SELECT id,country_name from lib_country", "id", "country_name");
		$company_sql = "SELECT * FROM lib_company WHERE id = $data[0]  AND is_deleted=0 AND status_active=1 ORDER BY company_name ASC";
		$result = sql_select( $company_sql );
		foreach( $result as $row  )
		{
			if($row[csf("level_no")])		$level_no	= $row[csf("level_no")].', ';
			if($row[csf("plot_no")])		$plot_no 	= $row[csf("plot_no")].', ';
			if($row[csf("road_no")]) 		$road_no 	= $row[csf("road_no")].', ';
			if($row[csf("block_no")]!='')	$block_no 	= $row[csf("block_no")].', ';
			if($row[csf("zip_code")]!='')	$zip_code 	= $row[csf("zip_code")].', ';
			if($row[csf("city")]!='') $city 			= $row[csf("city")];
			if($row[csf("country_id")]!='')	$country 	= $country_full_name[$row[csf("country_id")]].'.';
			if($row[csf("contact_no")]!='')	$contact_no = $row[csf("contact_no")];
			if($row[csf("email")]!='')		$email 		= $row[csf("email")];
			if($row[csf("website")]!='')	$website 	= $row[csf("website")].'.'; 
		}
		$head_oofice= "House #".$plot_no."Road #".$road_no."Sector #".$block_no.$zip_code.$country;
		$company_address="Head Office :".$head_oofice.'</br> Factory address : '.$city.'</br> Email : '.$email.'</br> Mobile : '.	$contact_no;

		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$company_plot_no_arr=return_library_array( "select id, plot_no from lib_company",'id','plot_no');
		$buyer_po_arr=array();
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
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
					top: 20%;
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
				
				#table_1,#table_2{  background-position: center;background-repeat: no-repeat; }
				#table_1{background-image:url(../../../img/bg-1.jpg);}
				#table_2{background-image:url(../../../img/bg-2.jpg); }
				/* #table_3{background-image:url(../../../img/bg-3.jpg);} */
				
			</style>
			<?
				$sql_mst = sql_select("select id, entry_form, trims_del, del_no_prefix, del_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id,  delivery_date, received_id, order_id, challan_no, gate_pass_no, remarks,inserted_by from trims_delivery_mst where id= $data[1]");
				$inserted_by=$sql_mst[0][csf("inserted_by")];

				$sql_ms = sql_select("select DISTINCT a.trims_del, a.id from trims_delivery_mst a, trims_delivery_dtls b where a.id=b.mst_id and a.id < $data[1] and b.ORDER_NO='$data[4]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ORDER BY a.id DESC ");
				$pre_chall_no=$sql_ms[0][csf("TRIMS_DEL")];					
		
		if($data[2]==1)
		{
			$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
			$party_loc_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_location")]];
		}
		else
		{
			$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
			$party_loc_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_id")]];
		}
		
		//$lib_location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
		$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
		//$copy_print = 3;
		//for($k=1; $k <= $copy_print; $k++)
		//{
		$k=0;	
		$copy_no=array(1,2,3); //for Dynamic Copy here 
		// $total_quantity=0;$total_delevery_quantity=0;$curr_delevery_quantity=0;$delevery_Balance_quantity=0;
		// $total_quantitys=0; $curr_delevery_quantitys=0;
		 foreach($copy_no as $cid)
		 {
			 $k++;
		?>
	        
	    <div style="width:1300px" class="page-break">
	        <table width="100%" id="table_<? echo $cid;?>">
				<tr>
					<!-- <td rowspan="2" width="200">
						<img src="../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
					</td> -->
	            	<td style="font-size:26px;" align="center"><strong>
						<? echo $company_arr[$data[0]]; ?></strong>
	                </td>
	                <td align="right" width="100">
						<? 
						if($k==1){
						echo "<b><h2>1st Copy</h2></b>";
						}
						else if($k==2){
						echo "<b><h2>2nd Copy</h2></b>";
						}
						else if($k==3){
						echo "<b><h2>3rd Copy</h2></b>";
						}
						?> 
					</td>
	            </tr>
	            <tr>
					<td align="center">
						<?
						?><b><? echo $company_address; ?></b> <?
						?> 
					</td>
	        		<!-- <td id="barcode_img_id_<? echo $k; ?>"></td> -->
				</tr>
				<tr>
	            	<td style="font-size:20px;" align="center"> <strong><? echo $data[3]."Challan"; ?></strong></td>
	            </tr> 
	            <tr>
	            	<td>&nbsp;</td>
	            	<td>&nbsp;</td>
	                <td>&nbsp;</td>
	            </tr> 
	        </table>
	        <br>
			<table class="rpt_table" width="100%" cellspacing="1" >
	            <tr>
	                <td valign="top" width="100"><strong>System Id</strong></td>
	                <td valign="top" width="150">:<strong> <? echo $data[5]; ?></strong></td>
	                <td valign="top" width="250">&nbsp;</td>
	                <td valign="top" width="120"><strong>Challan No. </strong></td>
	                <td valign="top"><strong>: <? echo $sql_mst[0][csf("challan_no")]; ?></strong></td>
	            </tr>
	            <tr>
	            		                <td valign="top" width="100"><strong> Delivery To</strong></td>
	                <td valign="top" width="150">:<strong> <? echo $party_arr[$sql_mst[0][csf("party_id")]]; ?></strong></td>
	                <td valign="top" width="250">&nbsp;</td>
	                <td valign="top" width="100"><strong>Delivery Date</strong></td>
	                <td valign="top" width="150"><strong>: <? echo change_date_format($sql_mst[0][csf("delivery_date")],'yyyy-mm-dd'); ?></strong></td>
	            </tr>
	            <tr>
	            	<td valign="top" width="120"><strong>Address</strong></td>
	                <td valign="top"><strong>: <? echo $party_location; ?> </strong></td>
	                <td valign="top" width="250">&nbsp;</td>
	                <td valign="top" width="100"><strong>Previous Challan No: </strong></td>
	                <td valign="top" width="150">:<strong><?echo $pre_chall_no;?></strong></td>
	            </tr>
	            <tr>
	            	<td valign="top" width="100"><strong>WO NO.</strong></td>
	                <td valign="top" width="150">:<strong> <? echo $data[4]; ?></strong></td>
	            </tr>
	            <tr>
	            	<td valign="top" width="100"><strong>Remarks</strong></td>
	                <td valign="top" width="150"><strong>: <? echo $sql_mst[0][csf("remarks")];//$order_no_trims_arr[$sql_mst[0][csf("received_id")]]['order_no']; ?></strong></td>
	            </tr>
	      	</table>
	         <br>
	      	<table  class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
	      		<thead>
		            <tr>
		            	<th width="40">SL</th>
	                    <th width="130">Item Name</th> 
	                    <th width="100">Style</th>
	                    <th width="130">PO</th>
	                    <th width="130">Buyer Name</th>
	                    <th width="80">Section</th>
		                <th width="140">Item Description</th>	
	                    <th width="90">Gmst Color </th>
	                    <th width="80">Gmst Size </th>
	                    <th width="80">Item Color </th>
		                <th width="70">Item Size</th>				
		                <th width="70">Ply</th>				
		                <th width="60">UOM</th>
	                    <th width="70">Order Qty</th>
		                <th width="80">Todays Delivery  Qty</th>
		                <th>Remarks</th>
		            </tr>
	            </thead>
	            <tbody>
				<?
				$i = 1;
				$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
				$style_ref_no=return_library_array( "select job_no,style_ref_no from fabric_sales_order_mst", "Job_no", "Style_ref_no" );
							
				$sql = "select a.id, a.mst_id, a.booking_dtls_id, a.receive_dtls_id, a.job_dtls_id, a.production_dtls_id, a.order_id, a.order_no, b.buyer_po_id, b.buyer_po_no,  a.buyer_style_ref, a.buyer_buyer, a.section, a.item_group as trim_group, a.order_uom, a.order_quantity,a.delevery_qty,a.claim_qty,a.remarks,a.color_id,a.size_id, 
				a.description, a.delevery_status, a.color_name,a.size_name,a.workoder_qty,a.break_down_details_id,a.no_of_roll_bag, c.ply, c.gmts_color_id, c.gmts_size_id from trims_delivery_dtls a,subcon_ord_dtls b, subcon_ord_breakdown c  where a.break_down_details_id = c.id and b.id=c.mst_id and c.status_active=1 and c.is_deleted=0 and a.mst_id='$data[1]' and  a.receive_dtls_id=b.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id ASC";
		
				$data_array=sql_select($sql);
				$orderIds='';
				$buyer_po_id_array=array();
				foreach($data_array as $order_row)
				{
					$orderIds.=$order_row[csf('buyer_po_id')].",";
				}
				$orderIds=chop($orderIds,','); 
				$orderIds=implode(",",array_unique(explode(",",$orderIds)));
				

				$all_data_arr=array();
				foreach($data_array as $row){
					$all_data_arr[$row[csf('color_id')]][]=$row;
				}

				$total_quantitys=0; $curr_delevery_quantitys=0; $total_ply=0;
				foreach($all_data_arr as $key=> $item_data)
				{
				

				foreach($item_data  as $row)
				{			
				?>
	                <tr style="font-weight: bold;">
	                <td><?php echo $i; ?></td>
	                <td><p><?php echo $item_group_arr[$row[csf('trim_group')]]; ?></p></td>
	                <td><p><?php echo $row[csf('buyer_style_ref')]; ?></p></td>
	                <td><p><?php echo $row[csf('buyer_po_no')]; ?></p></td>
	                <td><p><?php if($data[2]==1)
					{  echo $buyer_arr[$row[csf('buyer_buyer')]]; } else { echo $row[csf('buyer_buyer')];  } ?></p></td>
	                <td><?php echo $trims_section[$row[csf('section')]]; ?></td>
	                <td><p><?php echo $row[csf('description')]; ?></p></td>	
	                <td><p><?php echo $color_library[$row[csf('gmts_color_id')]]; ?></p> </td>
	                <td><p><?php echo $size_arr[$row[csf('gmts_size_id')]]; ?></p> </td>
	                <td><p><?php echo $color_library[$row[csf('color_id')]]; ?></p> </td>
	                <td><p><?php echo $row[csf('size_name')]; ?></p></td>				
	                <td align="right"><p><?php echo $row[csf('ply')];?></p></td>				
	                <td><?php echo $unit_of_measurement[$row[csf('order_uom')]]; $unique_uom[$row[csf('order_uom')]]=$row[csf('order_uom')]; ?></td>
	                <td align="right"><?php echo number_format($row[csf('workoder_qty')],4); ?></td>
	                <td align="right"><?php echo number_format($row[csf('delevery_qty')],4);  ?></td>
	                 <td><?php echo $row[csf('remarks')]; ?></td>
	                </tr>
				<?
				$i++;
				$total_quantitys += $row[csf('workoder_qty')];
				$curr_delevery_quantitys += $row[csf('delevery_qty')];
				$total_ply += $row[csf('ply')]; 
	            }
	        } 
				?>
				 <tr> 
					<td colspan="10"><strong>&nbsp;&nbsp;</strong></td>
					<td align="right"><strong>Total:</strong></td>
					<td align="right"><strong><? echo number_format($total_ply,2); ?></strong></td>
					<td ><strong>&nbsp;&nbsp;</strong></td>
					<td align="right"><strong><? echo number_format($total_quantitys,2); ?></strong></td>
				    <td align="right"><strong><? echo number_format($curr_delevery_quantitys,2); ?></strong></td>
	                <td><strong>&nbsp;&nbsp;</strong></td>
				</tr>				
				<?
	         	
	            ?>
	        </tbody> 
	    </table>
		<?
			$user_lib_name=return_library_array("select id,user_full_name from user_passwd", "id", "user_full_name");
			echo signature_table(174, $data[0], "1200px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
	    ?>	
	    </div>
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
	    generateBarcode("<? echo $data[5]; ?>");
	    </script>
	   <?
	  	 
		 }
	 exit();
		
	 }

	
	 if($action=="challan_print3") 
	{
		extract($_REQUEST);
		//echo $data;die;
		$data=explode('*',$data);
		$cbo_template_id=$data[6];
		
		$country_full_name = return_library_array("SELECT id,country_name from lib_country", "id", "country_name");
		$company_sql = "SELECT * FROM lib_company WHERE id = $data[0]  AND is_deleted=0 AND status_active=1 ORDER BY company_name ASC";
		$result = sql_select( $company_sql );
		foreach( $result as $row  )
		{
			if($row[csf("level_no")])		$level_no	= $row[csf("level_no")].', ';
			if($row[csf("plot_no")])		$plot_no 	= $row[csf("plot_no")].', ';
			if($row[csf("road_no")]) 		$road_no 	= $row[csf("road_no")].', ';
			if($row[csf("block_no")]!='')	$block_no 	= $row[csf("block_no")].', ';
			if($row[csf("zip_code")]!='')	$zip_code 	= $row[csf("zip_code")].', ';
			if($row[csf("city")]!='') $city 			= $row[csf("city")];
			if($row[csf("country_id")]!='')	$country 	= $country_full_name[$row[csf("country_id")]].'.';
			if($row[csf("contact_no")]!='')	$contact_no = $row[csf("contact_no")];
			if($row[csf("email")]!='')		$email 		= $row[csf("email")];
			if($row[csf("website")]!='')	$website 	= $row[csf("website")].'.'; 
		}
		$head_oofice= "House #".$plot_no."Road #".$road_no."Sector #".$block_no.$zip_code.$country;
		$company_address="Head Office :".$head_oofice.'</br> Factory address : '.$city.'</br> Email : '.$email.'</br> Mobile : '.$contact_no;

		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$company_plot_no_arr=return_library_array( "select id, plot_no from lib_company",'id','plot_no');
		$buyer_po_arr=array();
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
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
				top: 20%;
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
			
			#table_1,#table_2{  background-position: center;background-repeat: no-repeat; }
			#table_1{background-image:url(../../../img/bg-1.jpg);}
			#table_2{background-image:url(../../../img/bg-2.jpg); }
			/* #table_3{background-image:url(../../../img/bg-3.jpg);} */
			
		</style>
		<?
		//echo "select id, entry_form, trims_del, del_no_prefix, del_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id,  delivery_date, received_id, order_id, challan_no, gate_pass_no, remarks,inserted_by from trims_delivery_mst where id= $data[1]";
		$sql_mst = sql_select("select a.id, a.entry_form, a.trims_del, a.del_no_prefix, a.del_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.delivery_date, a.received_id, a.order_id, a.challan_no, a.gate_pass_no, a.remarks,a.inserted_by, b.buyer_tb,a.cust_location from trims_delivery_mst a, subcon_ord_mst b where b.id=received_id and a.id= $data[1] and a.status_active=1 and b.status_active=1");
		$inserted_by=$sql_mst[0][csf("inserted_by")];
		$user_lib_name=return_library_array("select id,user_full_name from user_passwd where id=$inserted_by", "id", "user_full_name");

		if($data[2]==1)
		{
			$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
			$party_loc_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_location")]];
		}
		else
		{
			$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
			$party_loc_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_id")]];
		}
		
		$fac_merchant_arr=return_library_array( "select id, team_marchant from subcon_ord_mst",'id','team_marchant');
		$fac_merchant=$fac_merchant_arr[$sql_mst[0][csf("received_id")]];

		$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
		//$copy_print = 3;
		//for($k=1; $k <= $copy_print; $k++)
		//{
		$k=0;	
		$copy_no=array(1,2,3,4); //for Dynamic Copy here 
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
		            	<td style="font-size:xx-large" align="center"><strong><? echo $company_arr[$data[0]]; ?></strong>
		                </td>
		                <td align="right" width="100">
							<? 
							if($k==1){
							echo "<b><h2>1st Copy</h2></b>";
							}
							else if($k==2){
							echo "<b><h2>2nd Copy</h2></b>";
							}
							else if($k==3){
							echo "<b><h2>3rd Copy</h2></b>";
							}
							else if($k==4){
							echo "<b><h2>4th Copy</h2></b>";
							}
							?> 
						</td>
		            </tr>
		            <tr>
						<td style="font-size:large" align="center"><? echo $company_address; ?></td>
		        		<td id="barcode_img_id_<? echo $k; ?>"></td>
					</tr>
					<tr>
		            	<td>&nbsp;</td>
		            	<td style="font-size:25px;" align="center"> <strong><? echo $data[3].' Challan'; ?></strong></td>
		                <td>&nbsp;</td>
		            </tr> 
		            <tr>
		            	<td>&nbsp;</td>
		            	<td>&nbsp;</td>
		                <td>&nbsp;</td>
		            </tr> 
		        </table>
		        <br>
				<table class="rpt_table" width="100%" cellspacing="1" >
		            <tr>
		                <td valign="top" width="100"><strong></strong></td>
		                <td valign="top" width="150"><strong></strong></td>
		                <td valign="top" width="250">&nbsp;</td>
		                <td valign="top" width="120"><strong>Challan No. </strong></td>
		                <td valign="top"><strong>: <? echo $data[5]; ?></strong></td>
		            </tr>
		            <tr>
		                <td valign="top" width="100"><strong> Delivery To</strong></td>
		                <td valign="top" width="150">:<strong> <? echo $party_arr[$sql_mst[0][csf("party_id")]]; ?></strong></td>
		                <td valign="top" width="250">&nbsp;</td>
		                <td valign="top" width="100">Delivery Date</td>
		                <td valign="top" width="150">: <? echo change_date_format($sql_mst[0][csf("delivery_date")],'yyyy-mm-dd'); ?></td>
		            </tr>
		            <tr>
		            	<td valign="top" width="120">Address</td>
		                <td valign="top">: <? echo $party_location; ?> </td>
		                <td valign="top" width="250">&nbsp;</td>
		                <td valign="top" width="100">Currencey </td>
		                <td valign="top" width="150">: <? echo $currency[$sql_mst[0][csf("currency_id")]];?></td>
		            </tr>
		            <tr>
		            	<td valign="top" width="100">WO NO.</td>
		                <td valign="top" width="150">: <? echo $data[4]; ?></td>
		                <td valign="top" width="250">&nbsp;</td>
		            	<td valign="top" width="100">Remarks</td>
		                <td valign="top" width="150">: <? echo $sql_mst[0][csf("remarks")];?></td>
		            </tr>
		            <tr>
		            	<td valign="top" width="100">Delivery Point</td>
		                <td valign="top" width="150" id="deliveryPoint_<?php echo $k; ?>"></td>
		                <td valign="top" width="250">&nbsp;</td>
		            	<td valign="top" width="100"><strong>Insert By</strong></td>
		                <td valign="top" width="150">: <strong> <? echo $user_lib_name[$inserted_by]; ?></strong></td>
		                
		            </tr>
		            <tr>
		            	<td valign="top" width="100">Buyers Tb</td>
		                <td valign="top" width="150" > <? echo $sql_mst[0][csf("buyer_tb")];?></td>
		                <td valign="top" width="250">&nbsp;</td>
		            	<td valign="top" width="100"><strong>Factory Merchant.</strong></td>
		                <td valign="top" width="150">: <strong> <? echo $fac_merchant; ?></strong></td>
		                
		            </tr>
					<tr>
		            	<td valign="top" width="100"> Customer Location</td>
		                <td valign="top" width="150" >: <? echo $sql_mst[0][csf("cust_location")];?></td>                
		            </tr>
		      	</table>
		         <br>
		      	<table  class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
		      		<thead>
			            <tr>
			            	<th width="40">SL</th>
		                    <th width="130">Cust. PO</th> 
		                    <th width="130">Buyer's Buyer </th>
			                <th width="90">Item Group</th>
			                <th width="200">Style</th>
			                <th width="140">Item Description</th>	
		                    <th width="80">Item Color </th>
			                <th width="70">Item Size</th>				
			                <th width="60">Order UOM</th>
		                    <th width="70">WO Qty.</th>
			                <th width="80">Cum. Delv Qty</th>
			                <th width="80">Curr. Delv Qty</th>
		                     <th width="80">No of Roll/Bag</th>
			                <th width="80">Delv Balance Qty</th>
			                <th>Remarks</th>
			            </tr>
		            </thead>
		            <tbody>
					<?
					$i = 1;
					$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
					$total_quantity=0;$total_delevery_quantity=0;$curr_delevery_quantity=0;$delevery_Balance_quantity=0;
					$style_ref_no=return_library_array( "select job_no,style_ref_no from fabric_sales_order_mst", "Job_no", "Style_ref_no" );
								
					$sql = "select a.id, a.mst_id, a.booking_dtls_id, a.receive_dtls_id, a.job_dtls_id, a.production_dtls_id, a.order_id, a.order_no, b.buyer_po_id, b.buyer_po_no,  a.buyer_style_ref, a.buyer_buyer, a.section, a.item_group as trim_group, a.order_uom, a.order_quantity,a.delevery_qty,a.claim_qty,a.remarks,a.color_id,a.size_id, 
					a.description, a.delevery_status, a.color_name,a.size_name,a.workoder_qty,a.break_down_details_id,a.no_of_roll_bag,c.style, d.delivery_point
					from trims_delivery_dtls a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_ord_mst d
					where a.mst_id='$data[1]' and a.receive_dtls_id=b.id and a.break_down_details_id = c.id and b.id=c.mst_id and c.status_active=1 and a.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.delevery_qty>0 and b.mst_id = d.id
					order by b.buyer_po_no,a.buyer_buyer,a.item_group,c.style,a.description,a.color_name,a.size_name ASC";
					//a.id ASC
					
					$delevery_qty_trims_arr=array();
					$pre_sql ="select break_down_details_id, sum(delevery_qty) as delevery_qty  from trims_delivery_dtls where status_active=1 and is_deleted=0 group by break_down_details_id";
					$pre_sql_res=sql_select($pre_sql);
					foreach ($pre_sql_res as $row)
					{
						$delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']=$row[csf("delevery_qty")];
					}
					unset($pre_sql_res);
					$data_array=sql_select($sql);
					$orderIds='';
					$buyer_po_id_array=array();
					foreach($data_array as $order_row)
					{
						$orderIds.=$order_row[csf('buyer_po_id')].",";
					}
					$orderIds=chop($orderIds,','); 
					$orderIds=implode(",",array_unique(explode(",",$orderIds)));
					//echo $orderIds; die;
					
					//echo "<pre>";
					//print_r($buyer_po_id_array); die;
					
					$piArray=array();
					$sql="select a.id, a.po_number,a.grouping,b.job_no,b.internal_ref from wo_po_break_down a,wo_order_entry_internal_ref b where a.job_no_mst=b.job_no and  a.id in ($orderIds)";
					$po_data=sql_select($sql);
					foreach($po_data as $row)
					{
						$piArray[$row[csf('id')]]['grouping']=$row[csf('internal_ref')];
					}
					
					foreach($data_array as $row)
					{
					?>
		                <tr>
		                <td><?php echo $i; ?></td>
		                <td style="border:1px solid black" ><p><?php echo $row[csf('buyer_po_no')]; ?></p></td>
		                <td style="border:1px solid black" ><p><?php if($data[2]==1)
						{  echo $buyer_arr[$row[csf('buyer_buyer')]]; } else { echo $row[csf('buyer_buyer')];  } ?></p></td>
		                <td style="border:1px solid black" ><p><?php echo $item_group_arr[$row[csf('trim_group')]]; ?></p></td>
		                <td style="border:1px solid black" ><p><?php echo $row[csf('style')]; ?></p></td>	
		                <td style="border:1px solid black" ><p><?php echo $row[csf('description')]; ?></p></td>	
		                <td style="border:1px solid black" ><p><?php echo $row[csf('color_name')]; ?></p> </td>
		                <td style="border:1px solid black" ><p><?php echo $row[csf('size_name')]; ?></p></td>				
		                <td><?php echo $unit_of_measurement[$row[csf('order_uom')]]; $unique_uom[$row[csf('order_uom')]]=$row[csf('order_uom')]; ?></td>
		                <td align="right"><?php echo $row[csf('workoder_qty')]; $total_quantity += $row[csf('workoder_qty')]; ?></td>
		                <td align="right"><?php echo  
						$cumDelvQty=$delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']-$row[csf('delevery_qty')];  
						$total_delevery_quantity += $delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']-$row[csf('delevery_qty')];
						 ?></td>
		                <td align="right"><?php echo $row[csf('delevery_qty')];  $curr_delevery_quantity += $row[csf('delevery_qty')];  ?></td>
		                 <td align="right"><?php echo $row[csf('no_of_roll_bag')];?></td>
		                <td align="right"><?php echo number_format($row[csf('workoder_qty')]-($row[csf('delevery_qty')]+$cumDelvQty),4); $delevery_Balance_quantity += $row[csf('workoder_qty')]-($row[csf('delevery_qty')]+$cumDelvQty);  ?></td>
		                <td><?php echo $row[csf('remarks')]; ?></td>
		                </tr>
					<?
					$i++;
		            } 
		         	if(count($unique_uom)==1){ 
					?>
		            <tr> 
						<td colspan="8"><strong>&nbsp;&nbsp;</strong></td>
						<td align="right"><strong>Total:</strong></td>
						<td align="right"><strong><? echo number_format($total_quantity,2); ?></strong></td>
						<td align="right"><strong><? echo number_format($total_delevery_quantity,2); ?></strong></td>
						<td align="right"><strong><? echo number_format($curr_delevery_quantity,2); ?></strong></td>
		                <td><strong>&nbsp;&nbsp;</strong></td>
						<td align="right"><strong><? echo number_format($delevery_Balance_quantity,2); ?></strong></td>
		                <td><strong>&nbsp;&nbsp;</strong></td>
					</tr>
		            <? } ?>
		        </tbody> 
		    </table>
			<?
				$user_lib_name=return_library_array("select id,user_full_name from user_passwd where id=$inserted_by", "id", "user_full_name");
				echo signature_table(174, $data[0], "1200px",$cbo_template_id,70,$user_lib_name[$inserted_by]);

				$delivery_point = $data_array[0][csf('delivery_point')];
		    ?>	
		    </div>
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
		    generateBarcode("<? echo $data[5]; ?>");

		    var deliveryPoint = ': ' + "<?php echo $delivery_point; ?>";
		    document.getElementById('deliveryPoint_' + <?php echo $k; ?>).innerHTML = deliveryPoint;
		    </script>
		   <?
	  	 
	  	}
	 	exit();
		
	 }


	if($action=="challan_print4") 
	{
		extract($_REQUEST);
		//echo $data;die;
		$data=explode('*',$data);
		$cbo_template_id=$data[6];
		
		$country_full_name = return_library_array("SELECT id,country_name from lib_country", "id", "country_name");
		$company_sql = "SELECT * FROM lib_company WHERE id = $data[0]  AND is_deleted=0 AND status_active=1 ORDER BY company_name ASC";
		$result = sql_select( $company_sql );
		foreach( $result as $row  )
		{
			if($row[csf("level_no")])		$level_no	= $row[csf("level_no")].', ';
			if($row[csf("plot_no")])		$plot_no 	= $row[csf("plot_no")].', ';
			if($row[csf("road_no")]) 		$road_no 	= $row[csf("road_no")].', ';
			if($row[csf("block_no")]!='')	$block_no 	= $row[csf("block_no")].', ';
			if($row[csf("zip_code")]!='')	$zip_code 	= $row[csf("zip_code")].', ';
			if($row[csf("city")]!='') $city 			= $row[csf("city")];
			if($row[csf("country_id")]!='')	$country 	= $country_full_name[$row[csf("country_id")]].'.';
			if($row[csf("contact_no")]!='')	$contact_no = $row[csf("contact_no")];
			if($row[csf("email")]!='')		$email 		= $row[csf("email")];
			if($row[csf("website")]!='')	$website 	= $row[csf("website")].'.';
			if($row[csf("group_id")]!='')	$group_id 	= $row[csf("group_id")]; 
		}
		$group_sql = "SELECT * FROM lib_group WHERE id = $group_id  AND is_deleted=0 AND status_active=1";
		$result = sql_select( $group_sql );

		$group_details= array();

		foreach( $result as $row  )
		{
			$group_details[$row[csf("id")]]['group_name']	= $row[csf("GROUP_NAME")];
			$group_details[$row[csf("id")]]['group_address'] 	= $row[csf("ADDRESS")];
		}

		$head_oofice= "House #".$plot_no."Road #".$road_no."Sector #".$block_no.$zip_code.$country;
		$company_address="Head Office :".$head_oofice.'</br> Factory address : '.$city.'</br> Factory address : '.$group_details[$row[csf("id")]]['group_address'].'</br> Email : '.$email.'</br> Mobile : '.$contact_no;

		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$company_plot_no_arr=return_library_array( "select id, plot_no from lib_company",'id','plot_no');
		$buyer_po_arr=array();
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
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
				top: 20%;
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
			
			#table_1,#table_2{  background-position: center;background-repeat: no-repeat; }
			#table_1{background-image:url(../../../img/bg-1.jpg);}
			#table_2{background-image:url(../../../img/bg-2.jpg); }
			/* #table_3{background-image:url(../../../img/bg-3.jpg);} */
			
		</style>
		<?
		//echo "select id, entry_form, trims_del, del_no_prefix, del_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id,  delivery_date, received_id, order_id, challan_no, gate_pass_no, remarks,inserted_by from trims_delivery_mst where id= $data[1]";
		$sql_mst = sql_select("select id, entry_form, trims_del, del_no_prefix, del_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id,  delivery_date, received_id, order_id, challan_no, gate_pass_no, remarks,inserted_by from trims_delivery_mst where id= $data[1]");
		$inserted_by=$sql_mst[0][csf("inserted_by")];
		$user_lib_name=return_library_array("select id,user_full_name from user_passwd where id=$inserted_by", "id", "user_full_name");

		if($data[2]==1)
		{
			$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
			$party_loc_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_location")]];
		}
		else
		{
			$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
			$party_loc_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_id")]];
		}
		
		$fac_merchant_arr=return_library_array( "select id, team_marchant from subcon_ord_mst",'id','team_marchant');
		$fac_merchant=$fac_merchant_arr[$sql_mst[0][csf("received_id")]];

		$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
		//$copy_print = 3;
		//for($k=1; $k <= $copy_print; $k++)
		//{
		$k=0;	
		$copy_no=array(1,2,3,4); //for Dynamic Copy here 
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
		            	<td style="font-size:xx-large" align="center"><strong><? echo $company_arr[$data[0]]; ?></strong><br>
		            		<strong><? echo $group_details[$group_id]['group_name']; ?>
		                </td>
		                <td align="right" width="100">
							<? 
							if($k==1){
							echo "<b><h2>1st Copy</h2></b>";
							}
							else if($k==2){
							echo "<b><h2>2nd Copy</h2></b>";
							}
							else if($k==3){
							echo "<b><h2>3rd Copy</h2></b>";
							}
							else if($k==4){
							echo "<b><h2>4th Copy</h2></b>";
							}
							?> 
						</td>
		            </tr>
		            <tr>
						<td style="font-size:large" align="center"><? echo $company_address; ?></td>
		        		<td id="barcode_img_id_<? echo $k; ?>"></td>
					</tr>
					<tr>
		            	<td>&nbsp;</td>
		            	<td style="font-size:25px;" align="center"> <strong><? echo $data[3].' Challan'; ?></strong></td>
		                <td>&nbsp;</td>
		            </tr> 
		            <tr>
		            	<td>&nbsp;</td>
		            	<td>&nbsp;</td>
		                <td>&nbsp;</td>
		            </tr> 
		        </table>
		        <br>
				<table class="rpt_table" width="100%" cellspacing="1" >
		            <tr>
		                <td valign="top" width="100"><strong></strong></td>
		                <td valign="top" width="150"><strong></strong></td>
		                <td valign="top" width="250">&nbsp;</td>
		                <td valign="top" width="120"><strong>Challan No. </strong></td>
		                <td valign="top"><strong>: <? echo $data[5]; ?></strong></td>
		            </tr>
		            <tr>
		                <td valign="top" width="100"><strong> Delivery To</strong></td>
		                <td valign="top" width="150">:<strong> <? echo $party_arr[$sql_mst[0][csf("party_id")]]; ?></strong></td>
		                <td valign="top" width="250">&nbsp;</td>
		                <td valign="top" width="100">Delivery Date</td>
		                <td valign="top" width="150">: <? echo change_date_format($sql_mst[0][csf("delivery_date")],'yyyy-mm-dd'); ?></td>
		            </tr>
		            <tr>
		            	<td valign="top" width="120">Address</td>
		                <td valign="top">: <? echo $party_location; ?> </td>
		                <td valign="top" width="250">&nbsp;</td>
		                <td valign="top" width="100">Currencey </td>
		                <td valign="top" width="150">: <? echo $currency[$sql_mst[0][csf("currency_id")]];?></td>
		            </tr>
		            <tr>
		            	<td valign="top" width="100">WO NO.</td>
		                <td valign="top" width="150">: <? echo $data[4]; ?></td>
		                <td valign="top" width="250">&nbsp;</td>
		            	<td valign="top" width="100">Remarks</td>
		                <td valign="top" width="150">: <? echo $sql_mst[0][csf("remarks")];?></td>
		            </tr>
		            <tr>
		            	<td valign="top" width="100">Delivery Point</td>
		                <td valign="top" width="150" id="deliveryPoint_<?php echo $k; ?>"></td>
		                <td valign="top" width="250">&nbsp;</td>
		            	<td valign="top" width="100"><strong>Insert By</strong></td>
		                <td valign="top" width="150">: <strong> <? echo $user_lib_name[$inserted_by]; ?></strong></td>
		                
		            </tr>
		            <tr>
		            	<td valign="top" width="100"></td>
		                <td valign="top" width="150" ></td>
		                <td valign="top" width="250">&nbsp;</td>
		            	<td valign="top" width="100"><strong>Factory Merchant.</strong></td>
		                <td valign="top" width="150">: <strong> <? echo $fac_merchant; ?></strong></td>
		                
		            </tr>
		      	</table>
		         <br>
		      	<table  class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
		      		<thead>
			            <tr>
			            	<th width="40">SL</th>
		                    <th width="130">Cust. PO</th> 
		                    <th width="130">Buyer's Buyer </th>
			                <th width="90">Item Group</th>
			                <th width="200">Style</th>
			                <th width="140">Item Description</th>	
		                    <th width="80">Item Color </th>
			                <th width="70">Item Size</th>				
			                <th width="60">Order UOM</th>
		                    <th width="70">WO Qty.</th>
			                <th width="80">Cum. Delv Qty</th>
			                <th width="80">Curr. Delv Qty</th>
		                     <th width="80">No of Roll/Bag</th>
			                <th width="80">Delv Balance Qty</th>
			                <th>Remarks</th>
			            </tr>
		            </thead>
		            <tbody>
					<?
					$i = 1;
					$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
					$total_quantity=0;$total_delevery_quantity=0;$curr_delevery_quantity=0;$delevery_Balance_quantity=0;
					$style_ref_no=return_library_array( "select job_no,style_ref_no from fabric_sales_order_mst", "Job_no", "Style_ref_no" );
								
					$sql = "select a.id, a.mst_id, a.booking_dtls_id, a.receive_dtls_id, a.job_dtls_id, a.production_dtls_id, a.order_id, a.order_no, b.buyer_po_id, b.buyer_po_no,  a.buyer_style_ref, a.buyer_buyer, a.section, a.item_group as trim_group, a.order_uom, a.order_quantity,a.delevery_qty,a.claim_qty,a.remarks,a.color_id,a.size_id, 
					a.description, a.delevery_status, a.color_name,a.size_name,a.workoder_qty,a.break_down_details_id,a.no_of_roll_bag,c.style, d.delivery_point
					from trims_delivery_dtls a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_ord_mst d
					where a.mst_id='$data[1]' and a.receive_dtls_id=b.id and a.break_down_details_id = c.id and b.id=c.mst_id and c.status_active=1 and a.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.delevery_qty>0 and b.mst_id = d.id
					order by b.buyer_po_no,a.buyer_buyer,a.item_group,c.style,a.description,a.color_name,a.size_name ASC";
					//a.id ASC
					
					$delevery_qty_trims_arr=array();
					$pre_sql ="select break_down_details_id, sum(delevery_qty) as delevery_qty  from trims_delivery_dtls where status_active=1 and is_deleted=0 group by break_down_details_id";
					$pre_sql_res=sql_select($pre_sql);
					foreach ($pre_sql_res as $row)
					{
						$delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']=$row[csf("delevery_qty")];
					}
					unset($pre_sql_res);
					$data_array=sql_select($sql);
					$orderIds='';
					$buyer_po_id_array=array();
					foreach($data_array as $order_row)
					{
						$orderIds.=$order_row[csf('buyer_po_id')].",";
					}
					$orderIds=chop($orderIds,','); 
					$orderIds=implode(",",array_unique(explode(",",$orderIds)));
					//echo $orderIds; die;
					
					//echo "<pre>";
					//print_r($buyer_po_id_array); die;
					
					$piArray=array();
					$sql="select a.id, a.po_number,a.grouping,b.job_no,b.internal_ref from wo_po_break_down a,wo_order_entry_internal_ref b where a.job_no_mst=b.job_no and  a.id in ($orderIds)";
					$po_data=sql_select($sql);
					foreach($po_data as $row)
					{
						$piArray[$row[csf('id')]]['grouping']=$row[csf('internal_ref')];
					}
					
					foreach($data_array as $row)
					{
					?>
		                <tr>
		                <td><?php echo $i; ?></td>
		                <td style="border:1px solid black" ><p><?php echo $row[csf('buyer_po_no')]; ?></p></td>
		                <td style="border:1px solid black" ><p><?php if($data[2]==1)
						{  echo $buyer_arr[$row[csf('buyer_buyer')]]; } else { echo $row[csf('buyer_buyer')];  } ?></p></td>
		                <td style="border:1px solid black" ><p><?php echo $item_group_arr[$row[csf('trim_group')]]; ?></p></td>
		                <td style="border:1px solid black" ><p><?php echo $row[csf('style')]; ?></p></td>	
		                <td style="border:1px solid black" ><p><?php echo $row[csf('description')]; ?></p></td>	
		                <td style="border:1px solid black" ><p><?php echo $row[csf('color_name')]; ?></p> </td>
		                <td style="border:1px solid black" ><p><?php echo $row[csf('size_name')]; ?></p></td>				
		                <td><?php echo $unit_of_measurement[$row[csf('order_uom')]]; $unique_uom[$row[csf('order_uom')]]=$row[csf('order_uom')]; ?></td>
		                <td align="right"><?php echo $row[csf('workoder_qty')]; $total_quantity += $row[csf('workoder_qty')]; ?></td>
		                <td align="right"><?php echo  
						$cumDelvQty=$delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']-$row[csf('delevery_qty')];  
						$total_delevery_quantity += $delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']-$row[csf('delevery_qty')];
						 ?></td>
		                <td align="right"><?php echo $row[csf('delevery_qty')];  $curr_delevery_quantity += $row[csf('delevery_qty')];  ?></td>
		                 <td align="right"><?php echo $row[csf('no_of_roll_bag')];?></td>
		                <td align="right"><?php echo number_format($row[csf('workoder_qty')]-($row[csf('delevery_qty')]+$cumDelvQty),4); $delevery_Balance_quantity += $row[csf('workoder_qty')]-($row[csf('delevery_qty')]+$cumDelvQty);  ?></td>
		                <td><?php echo $row[csf('remarks')]; ?></td>
		                </tr>
					<?
					$i++;
		            } 
		         	if(count($unique_uom)==1){ 
					?>
		            <tr> 
						<td colspan="8"><strong>&nbsp;&nbsp;</strong></td>
						<td align="right"><strong>Total:</strong></td>
						<td align="right"><strong><? echo number_format($total_quantity,2); ?></strong></td>
						<td align="right"><strong><? echo number_format($total_delevery_quantity,2); ?></strong></td>
						<td align="right"><strong><? echo number_format($curr_delevery_quantity,2); ?></strong></td>
		                <td><strong>&nbsp;&nbsp;</strong></td>
						<td align="right"><strong><? echo number_format($delevery_Balance_quantity,2); ?></strong></td>
		                <td><strong>&nbsp;&nbsp;</strong></td>
					</tr>
		            <? } ?>
		        </tbody> 
		    </table>
			<?
				$user_lib_name=return_library_array("select id,user_full_name from user_passwd where id=$inserted_by", "id", "user_full_name");
				echo signature_table(174, $data[0], "1200px",$cbo_template_id,70,$user_lib_name[$inserted_by]);

				$delivery_point = $data_array[0][csf('delivery_point')];
		    ?>	
		    </div>
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
		    generateBarcode("<? echo $data[5]; ?>");

		    var deliveryPoint = ': ' + "<?php echo $delivery_point; ?>";
		    document.getElementById('deliveryPoint_' + <?php echo $k; ?>).innerHTML = deliveryPoint;
		    </script>
		   <?
	  	 
	  	}
	 	exit();
		
	}

	if($action=="challan_print5") 
	{
		extract($_REQUEST);
		//echo $data;die;
		$data=explode('*',$data);
		$cbo_template_id=$data[6];
		$show_color=$data[8];
		
		$country_full_name = return_library_array("SELECT id,country_name from lib_country", "id", "country_name");
		$company_sql = "SELECT * FROM lib_company WHERE id = $data[0]  AND is_deleted=0 AND status_active=1 ORDER BY company_name ASC";
		$result = sql_select( $company_sql );
		foreach( $result as $row  )
		{
			if($row[csf("level_no")])		$level_no	= "Level NO #".$row[csf("level_no")].', ';
			if($row[csf("plot_no")])		$plot_no 	= "PLOT NO #".$row[csf("plot_no")].', ';
			if($row[csf("road_no")]) 		$road_no 	= "Road #".$row[csf("road_no")].', ';
			if($row[csf("block_no")]!='')	$block_no 	= "Sector #".$row[csf("block_no")].', ';
			if($row[csf("zip_code")]!='')	$zip_code 	= $row[csf("zip_code")].', ';
			if($row[csf("city")]!='') 		$city 		= $row[csf("city")].', ';
			if($row[csf("country_id")]!='')	$country 	= $country_full_name[$row[csf("country_id")]].'.';
			if($row[csf("contact_no")]!='')	$contact_no = $row[csf("contact_no")];
			if($row[csf("email")]!='')		$email 		= $row[csf("email")];
			if($row[csf("website")]!='')	$website 	= $row[csf("website")].'.';
			if($row[csf("group_id")]!='')	$group_id 	= $row[csf("group_id")]; 
		}

		$head_oofice= $plot_no.$level_no.$road_no.$city.$block_no.$zip_code;
		$company_address=$head_oofice.'</br></br> Tel: '.$contact_no.', Email: '.$email;

		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$company_plot_no_arr=return_library_array( "select id, plot_no from lib_company",'id','plot_no');
		$buyer_po_arr=array();
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');


		$sql_mst = sql_select("SELECT id, entry_form, trims_del, del_no_prefix, del_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id,  delivery_date, received_id, order_id, challan_no, gate_pass_no, remarks,inserted_by, deli_party_location from trims_delivery_mst where id= $data[1]");
		$inserted_by=$sql_mst[0][csf("inserted_by")];
		$user_lib_name=return_library_array("select id,user_full_name from user_passwd where id=$inserted_by", "id", "user_full_name");
		$order_no = return_field_value("order_no", "subcon_ord_mst", "id=".$sql_mst[0][csf("received_id")]."", "order_no");

		if($data[2]==1)
		{
			$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
			$party_loc_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_location")]];
		}
		else
		{
			$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
			$party_loc_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_id")]];
		}
		$del_party_location = '';
		if ($sql_mst[0][csf("within_group")]==2) {

			$lib_buyer_id = $sql_mst[0][csf("party_id")];

			$com_loc = sql_select("SELECT id, address_1, address_2, address_3, address_4 from lib_buyer where id= $lib_buyer_id");

			if($com_loc[0][csf("address_1")]!=''){

				$del_party_location .= $com_loc[0][csf("address_1")];
			}

			if($com_loc[0][csf("address_2")]!=''){

				if ($del_party_location!='') {
					$del_party_location .= ", ";
				}

				$del_party_location .= $com_loc[0][csf("address_2")];
			}

			if($com_loc[0][csf("address_3")]!=''){

				if ($del_party_location!='') {
					$del_party_location .= ", ";
				}

				$del_party_location .= $com_loc[0][csf("address_3")];
			}

			if($com_loc[0][csf("address_4")]!=''){

				if ($del_party_location!='') {
					$del_party_location .= ", ";
				}

				$del_party_location .= ", ".$com_loc[0][csf("address_4")];
			}
		}
		else
		{
			$com_loc_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
			$del_party_location=$com_loc_arr[$sql_mst[0][csf("deli_party_location")]];
		}
		
		$fac_merchant_arr=return_library_array( "select id, team_marchant from subcon_ord_mst",'id','team_marchant');
		$fac_merchant=$fac_merchant_arr[$sql_mst[0][csf("received_id")]];

		$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");

		$sql = "SELECT a.ID, b.BUYER_PO_NO, a.BUYER_BUYER, a.item_group as TRIM_GROUP, a.ORDER_UOM, a.DELEVERY_QTY,a.REMARKS,a.COLOR_ID,c.SIZE_ID, a.DESCRIPTION, a.COLOR_NAME,c.SIZE_NAME, a.NO_OF_ROLL_BAG,c.STYLE,d.SUBCON_JOB
		from trims_delivery_dtls a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_ord_mst d
		where a.mst_id='$data[1]' and a.receive_dtls_id=b.id and a.break_down_details_id = c.id and b.id=c.mst_id and c.status_active=1 and a.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.delevery_qty>0 and b.mst_id = d.id
		order by b.buyer_po_no,a.buyer_buyer,a.item_group,c.style,a.description,a.color_name,a.size_name ASC";
		// echo $sql;
		$data_array=sql_select($sql);
		foreach($data_array as $row)
		{
			$job_no=$row['SUBCON_JOB'];
			if($row['BUYER_BUYER'])
			{
				if($data[2]==1){ $buyers_buyer.=$buyer_arr[$row['BUYER_BUYER']].","; } 
				else { $buyers_buyer.=$row['BUYER_BUYER'].","; }
			}
		}		

		?>
		<style>
			/* *{font-weight: bold;} */
			.divfont table tbody tr td{
				font-size: 20px; 
			}
			@media print {
				*{font-family: verdana, sans-serif; }
				div.divFooter {
					position: fixed;
					bottom:40px;
				}
				tr.page_break {page-break-before: always; }
			}
		</style>
		<div style="width:1190px" align="left">
			<table width="1180">
				<tr>
					<td style="font-size:xxx-large" align="center"><strong><? echo $company_arr[$data[0]]; ?></strong></td>
				</tr>
				<tr>
					<td style="font-size:20px" align="center"><? echo $company_address; ?></td>
				</tr>
				<tr>
					<td height="15px;"></td>
				</tr>
				<tr>
					<td style="font-size:35px;" align="center"><strong><? echo 'DELIVERY CHALLAN'; ?></strong></td>
				</tr>
				<tr>
					<td height="20px;"></td>
				</tr>
			</table>
			<br>
			<table class="rpt_table" width="1180" cellspacing="5" >
				<tr>
					<td width="120" valign="top" ><strong>DELIVERY TO</strong></td>
					<td width="400" valign="top"><strong>: <? echo $party_arr[$sql_mst[0][csf("party_id")]]; ?></strong></td>
					<td width="100">&nbsp;</td>
					<td width="150"><strong>CHALLAN NO. </strong></td>
					<td ><strong>: <? echo $data[5]; ?></strong></td>
				</tr>
				<tr>
					<td valign="top" ><strong>ADDRESS</strong></td>
					<td valign="top"><strong>: <? echo $party_location; ?></strong></td>
					<td>&nbsp;</td>
					<td valign="top" ><strong>DELIVERY DATE </strong></td>
					<td valign="top" ><strong>: <? echo change_date_format($sql_mst[0][csf("delivery_date")],'yyyy-mm-dd'); ?></strong></td>
				</tr>
				<tr>
					<td valign="top"><strong>DELIVERY PLACE</strong></td>
					<td valign="top"><strong>: <? echo $del_party_location;?></strong></td>
					<td>&nbsp;</td>
					<td valign="top" ><strong>JOB NO </strong></td>
					<td  valign="top"><strong>: <? echo $job_no;?></strong></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td valign="top" ><strong>BUYER </strong></td>
					<td valign="top"  ><strong>: <?=implode(", ",array_unique(explode(",",chop($buyers_buyer,','))))?></strong></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td valign="top" ><strong>WO NO </strong></td>
					<td valign="top" ><strong>: <?=$order_no;?></strong></td>
				</tr>
				<tr>
					<td colspan="4" height="20px;"></td>
				</tr>
			</table>
			<br>
			<table  class="rpt_table" width="1180" cellspacing="1" cellpadding="5" rules="all" border="1">
				<thead>
					<tr>
						<th style="border:4px solid black" width="40">SL</th>
						<th style="border:4px solid black" width="90">Item</th>
						<th style="border:4px solid black" width="100">PO</th> 
						<th style="border:4px solid black" width="150">Style No.</th>
						<th style="border:4px solid black" width="180">Item Description</th>	
						<?
							if($show_color==1){?><th style="border:4px solid black" width="60">Item Color </th><?}
						?>						
						<th style="border:4px solid black" width="140">Item Size</th>				
						<th style="border:4px solid black" width="50">Order UOM</th>
						<th style="border:4px solid black" width="70">Qty.</th>
						<th style="border:4px solid black" width="60">No of Roll/Pkt</th>
						<th style="border:4px solid black">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i = 1;
					foreach($data_array as $row)
					{
						if($i==12 || $i==29 || $i==46 || $i==64 || $i==82 || $i==100){ $pagebreak = " class='page_break'";} else{ $pagebreak = "";}
						?>
						<tr <?=$pagebreak; ?>>
							<td align="center" style="border:3px solid black" ><?php echo $i; ?></td>
							<td align="center" style="border:3px solid black" ><p><?php echo $item_group_arr[$row['TRIM_GROUP']]; ?></p></td>
							<td align="center" style="border:3px solid black" ><p><?php echo $row['BUYER_PO_NO']; ?></p></td>
							<td align="center" style="border:3px solid black" ><p><?php echo $row['STYLE']; ?></p></td>	
							<td align="center" style="border:3px solid black" ><p><?php echo $row['DESCRIPTION']; ?></p></td>	
							<?
								if($show_color==1){?><td style="border:3px solid black" align="center"><p><?php echo $row['COLOR_NAME']; ?></p> </td><?}
							?>						
							<td align="center" style="border:3px solid black" ><p>
							<?php
							 if($row['SIZE_ID'] > 0){
                                echo $size_arr[$row['SIZE_ID']];
                             }else{
                                echo $row['SIZE_NAME'];
							 }
                            ?></p></td>
							<td align="center" style="border:3px solid black" ><?php echo $unit_of_measurement[$row['ORDER_UOM']];  ?></td>
							<td align="right" style="border:3px solid black" ><?php echo $row['DELEVERY_QTY'];  ?></td>
							<td align="right" style="border:3px solid black" ><?php echo $row['NO_OF_ROLL_BAG'];?></td>						
							<td style="border:3px solid black;word-break: break-all;" ><?php echo $row['REMARKS']; ?></td>
						</tr>
						<?
						$i++;
						$total_delevery_qty += $row['DELEVERY_QTY'];
						$total_no_of_roll_bag += $row['NO_OF_ROLL_BAG'];
						$unique_uom[$row['ORDER_UOM']]=$row['ORDER_UOM'];
					} 
					if(count($unique_uom)==1)
					{ 
						?>
						<tr> 
							<td style="border:3px solid black" colspan="<?=($show_color==1)?7:6;?>"><strong>&nbsp;&nbsp;</strong></td>
							<td style="border:3px solid black" align="right"><strong>Total:</strong></td>
							<td style="border:3px solid black" align="right"><strong><? echo number_format($total_delevery_qty,2); ?></strong></td>
							<td style="border:3px solid black" align="right"><strong><? echo number_format($total_no_of_roll_bag,2); ?></strong></td>
							<td style="border:3px solid black"><strong>&nbsp;&nbsp;</strong></td>
						</tr>
						<? 
					} ?>
				</tbody> 
			</table>
			<div class="divFooter">
				<div class="divfont">
				<?
					$user_lib_name=return_library_array("select id,user_full_name from user_passwd where id=$inserted_by", "id", "user_full_name");
					echo signature_table(174, $data[0], "1180px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
				?>	
				</div>
				<br>
				<table width="1180">
					<tr>
						<td width="50"></td>
						<td width="600"></td>
						<td align="center" style="font-size:14px;"><strong>Product Acknowledgement</strong> </td>
					</tr>
					<tr>
						<td colspan="2"></td>
						<td align="center" style="font-size:14px;"><strong>Received the commodity in full quantity and good condition</strong></td>
					</tr>
					<tr>
						<td align="center" style="font-size:14px;"><strong>NOTE:- </strong></td>
						<td colspan="2" style="font-size:14px;"><strong>ANY COMPLAINT REGARDING SHORTAGE AND DAMAGE SHOULD BE BROUGHT TO THE NOTICE WITHIN THREE WORKING DAYS IN WRITING. </strong></td>
					</tr>
				</table>
			</div>

		</div>
		<?
	 	exit();
		
	}

    if($action=="challan_print6")
	{
		extract($_REQUEST);
		$data=explode('*',$data);
         echo load_html_head_contents($rpt_title,"../../", 1, 1, $unicode,'','');
		?>
			<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
		<?
		$cbo_template_id=$data[6];
		$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
		$sql_company = sql_select("select * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
	  	foreach($sql_company as $company_data)
	  	{
			if($company_data[csf('plot_no')]!='')$plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
			if($company_data[csf('level_no')]!='')$level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
			if($company_data[csf('road_no')]!='')$road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
			if($company_data[csf('block_no')]!='')$block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
			if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
			if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
			if($company_data[csf('country_id')]!=0)$country = $country_arr[$company_data[csf('country_id')]];else $country='';

			$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
		}
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$company_plot_no_arr=return_library_array( "select id, plot_no from lib_company",'id','plot_no');
		$buyer_po_arr=array();
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

		$sql_mst = sql_select("select id, entry_form, trims_del, del_no_prefix, del_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id,  delivery_date, received_id, order_id, challan_no, gate_pass_no, remarks,inserted_by from trims_delivery_mst where id= $data[1]");
		$inserted_by=$sql_mst[0][csf("inserted_by")];
		if($data[2]==1)
		{
			$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
			$party_loc_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_location")]];
		}
		else
		{
			$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
			$party_loc_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_id")]];
		}
		$fac_merchant_arr=return_library_array( "select id, team_marchant from subcon_ord_mst",'id','team_marchant');
		$fac_merchant=$fac_merchant_arr[$sql_mst[0][csf("received_id")]];
		$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");

        $sql_dtls = "select a.id, a.mst_id, a.booking_dtls_id, a.receive_dtls_id, a.job_dtls_id, a.production_dtls_id, a.order_id, a.order_no, a.buyer_po_id, a.
        buyer_po_no, a.buyer_style_ref, a.buyer_buyer, a.section, a.item_group as trim_group, a.order_uom, a.order_quantity, a.delevery_qty, a.claim_qty, a.
        remarks, a.color_id, a.size_id, a.description, a.delevery_status, a.color_name, a.size_name, a.workoder_qty, a.break_down_details_id, b.job_no_mst 
        from trims_delivery_dtls a left join wo_po_break_down b on a.buyer_po_id = b.id where a.mst_id=$data[1] and a.status_active=1 and a.is_deleted=0 order by a.color_id, a.size_id";
		//        echo $sql_dtls;
        $dtls_data = []; $break_down_id = [];
        $sql_dtls_result = sql_select($sql_dtls);
        foreach($sql_dtls_result as $key => $value){
            $arrKey = $value[csf('trim_group')].'*#*'.$value[csf('description')].'*#*'.$value[csf('color_name')].'*#*'.$value[csf('size_name')].'*#*'.$value[csf('order_uom')];
            $dtls_data[$arrKey]['group'] = $item_group_arr[$value[csf('trim_group')]];
            $dtls_data[$arrKey]['description'] = $value[csf('description')];
            if($data[2]==1){
                $buyer =  $buyer_arr[$value[csf('buyer_buyer')]];
            } else {
                $buyer =  $value[csf('buyer_buyer')];
            }
            $dtls_data[$arrKey]['buyer'][$key] = $buyer;
            $dtls_data[$arrKey]['job'][$key] = $value[csf('job_no_mst')];
            $dtls_data[$arrKey]['order'][$key] = $value[csf('BUYER_PO_NO')];
            $dtls_data[$arrKey]['style'][$key] = $value[csf('buyer_style_ref')];
            $dtls_data[$arrKey]['color'] = $value[csf('color_name')];
            $dtls_data[$arrKey]['size'] = $value[csf('size_name')];
            $dtls_data[$arrKey]['uom'] = $unit_of_measurement[$value[csf('order_uom')]];
            $dtls_data[$arrKey]['wo_qty'] += $value[csf('order_quantity')];
            $dtls_data[$arrKey]['del_qty'] += $value[csf('delevery_qty')];
            $dtls_data[$arrKey]['remarks'][$key] = $value[csf('remarks')];
        }
        ?>

	    <div style="width:1200px" class="page-break">
	        <table width="100%" border="0">
				<tr>
					<td rowspan="2" width="200">
						<img src="../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
					</td>
	            	<td style="font-size:x-large;" align="center"><strong>
						<? echo $company_arr[$data[0]]; ?></strong>
	                </td>
	                <td align="right" width="100">
					</td>
	            </tr>
	            <tr>
	            	<td style="font-size:15px;" align="center"><? echo $company_address; ?></td>
				</tr>
				<tr>
	            	<td>&nbsp;</td>
	            	<td style="font-size:18px;" align="center"> <strong>Delivery Entry Challan</strong></td>
	                <td>&nbsp;</td>
	            </tr
	        ></table>
	        <br>
			<table width="100%" cellspacing="1" border="0">
	            <tr>
	                <td style="font-size: 10.5pt;" valign="top" width="100"><strong> Delivery To</strong></td>
	                <td style="font-size: 10.5pt;" valign="top" width="150">:<strong> <? echo $party_arr[$sql_mst[0][csf("party_id")]]; ?></strong></td>
	                <td style="font-size: 10.5pt;" valign="top" width="250">&nbsp;</td>
	                <td style="font-size: 10.5pt;" valign="top" width="120"><strong>Challan No. </strong></td>
	                <td style="font-size: 10.5pt;" valign="top"><strong>: <? echo $data[5]; ?></strong></td>
	            </tr>
	            <tr>
	            	<td style="font-size: 10.5pt;" valign="top" width="120">Address</td>
	                <td style="font-size: 10.5pt;" valign="top">: <? echo $party_location; ?> </td>
	                <td style="font-size: 10.5pt;" valign="top" width="250">&nbsp;</td>
	                <td style="font-size: 10.5pt;" valign="top" width="100">Delivery Date</td>
	                <td style="font-size: 10.5pt;" valign="top" width="150">: <? echo change_date_format($sql_mst[0][csf("delivery_date")],'yyyy-mm-dd'); ?></td>
	            </tr>
	            <tr>
	            	<td style="font-size: 10.5pt;" valign="top" width="100">WO NO.</td>
	                <td style="font-size: 10.5pt;" valign="top" width="150">: <? echo $data[4];//$order_no_trims_arr[$sql_mst[0][csf("received_id")]]['order_no']; ?></td>
	                <td style="font-size: 10.5pt;" valign="top" width="250">&nbsp;</td>
	                <td style="font-size: 10.5pt;" valign="top" width="100">Remarks</td>
	                <td style="font-size: 10.5pt;" valign="top" width="150">: <? echo $sql_mst[0][csf("remarks")]; ?></td>
	            </tr>
	            <tr>
	            	<td style="font-size: 10.5pt;" valign="top" width="100"></td>
	                <td style="font-size: 10.5pt;" valign="top" width="150"></td>
	                <td style="font-size: 10.5pt;" valign="top" width="250">&nbsp;</td>
	                <td style="font-size: 10.5pt;" valign="top" width="100">Factory Merchant.</td>
	                <td style="font-size: 10.5pt;" valign="top" width="150">: <? echo $fac_merchant; ?></td>
	            </tr>
	      	</table>
	         <br>
	         <style>
	         .rpt_table tfoot th {
                background-image: -moz-linear-gradient( rgb(194,220,255) 10%, rgb(136,170,214) 96%);
                border: 1px solid #8DAFDA;
                color: #444;
                font-size: 13px;
                font-weight: bold;
                line-height: 12px;
                padding-right: 2px;
                padding-left: 2px;
                height: 25px;
            }
            </style>
	      	<table  class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
	      		<thead>
		            <tr>
		            	<th width="20">SL</th>
		            	<th width="90">Item Group</th>
		                <th width="140">Item Des.</th>
		                <th width="130">Buyer</th>
		                 <th width="100">Job No.</th>
	                    <th width="120">Buyer PO</th>
	                    <th width="150">Style Ref.</th>
	                    <th width="80">Item Color </th>
		                <th width="70">Item Size</th>
		                <th width="60">UOM</th>
	                    <th width="80">WO Qty.</th>
		                <th width="80">Curr. Del Qty.</th>
		                <th width="75">Del. Bal. Qty.</th>
		                <th>Remarks</th>
		            </tr>
	            </thead>
	            <tbody>
	            <?
	            $i = 1; $tot_wo_qty = 0; $tot_cur_del_qty = 0; $tot_bal_qty = 0;
	            foreach ($dtls_data as $value){
                ?>
                <tr>
                    <td valign="middle" style="font-size: 10pt; padding: 3px;" align="center"><?=$i?></td>
                    <td valign="middle" style="font-size: 10pt; padding: 3px;"><?=$value['group']?></td>
                    <td valign="middle" style="font-size: 10pt; padding: 3px;"><?=$value['description']?></td>
                    <td valign="middle" style="font-size: 10pt; padding: 3px;"><?=implode(", ", array_unique($value['buyer']))?></td>
                    <td valign="middle" style="font-size: 10pt; padding: 3px;"><?=implode(", ", array_unique($value['job']))?></td>
                    <td valign="middle" style="font-size: 10pt; padding: 3px;"><?=implode(", ", array_unique($value['order']))?></td>
                    <td valign="middle" style="font-size: 10pt; padding: 3px;"><?=implode(", ", array_unique($value['style']))?></td>
                    <td valign="middle" style="font-size: 10pt; padding: 3px;" align="center"><?=$value['color']?></td>
                    <td valign="middle" style="font-size: 10pt; padding: 3px;" align="center"><?=$value['size']?></td>
                    <td valign="middle" style="font-size: 10pt; padding: 3px;" align="center"><?=$value['uom']?></td>
                    <td valign="middle" style="font-size: 10pt; padding: 3px;" align="right"><?=number_format($value['wo_qty'], 2)?></td>
                    <td valign="middle" style="font-size: 10pt; padding: 3px;" align="right"><?=number_format($value['del_qty'], 2)?></td>
                    <td valign="middle" style="font-size: 10pt; padding: 3px;" align="right"><?=number_format($value['wo_qty'] - $value['del_qty'], 2)?></td>
                    <td valign="middle" style="font-size: 10pt; padding: 3px;"><?=implode(", ", array_unique($value['remarks']))?></td>
                </tr>
			    <?
			    $i++;
                $tot_wo_qty += $value['wo_qty'];
                $tot_cur_del_qty += $value['del_qty'];
                $tot_bal_qty += ($value['wo_qty'] - $value['del_qty']);
			    }
                ?>
	        </tbody>
	        <tfoot>
                <tr>
                    <th colspan="10" align="right">Total</th>
                    <th align="right"><?=number_format($tot_wo_qty, 2)?></th>
                    <th align="right"><?=number_format($tot_cur_del_qty, 2)?></th>
                    <th align="right"><?=number_format($tot_bal_qty, 2)?></th>
                    <th></th>

                </tr>
            </tfoot>
	    </table>
		<?
			$user_lib_name=return_library_array("select id,user_full_name from user_passwd where id=$inserted_by", "id", "user_full_name");
			echo signature_table(174, $data[0], "1200px",$cbo_template_id,60,$user_lib_name[$inserted_by]);
	    ?>
	    </div>

	   <?
	 	exit();
	}

	if($action=="challan_print_9")
	{
		extract($_REQUEST);
		$data=explode('*',$data);
		// print_r($data);
         echo load_html_head_contents($rpt_title,"../../", 1, 1, $unicode,'','');
		?>
			<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
		<?
		$cbo_template_id=$data[6];
		$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
		$sql_company = sql_select("select * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
	  	foreach($sql_company as $company_data)
	  	{
			if($company_data[csf('plot_no')]!='')$plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
			if($company_data[csf('level_no')]!='')$level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
			if($company_data[csf('road_no')]!='')$road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
			if($company_data[csf('block_no')]!='')$block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
			if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
			if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
			if($company_data[csf('country_id')]!=0)$country = $country_arr[$company_data[csf('country_id')]];else $country='';

			$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
		}
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$company_plot_no_arr=return_library_array( "select id, plot_no from lib_company",'id','plot_no');
		$buyer_po_arr=array();
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
		$size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');

		$sql_mst = sql_select("select a.id, a.entry_form, a.trims_del, a.del_no_prefix, a.del_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.delivery_date, a.received_id, a.order_id, a.challan_no, a.gate_pass_no, a.remarks,a.inserted_by, b.pay_mode, b.source from trims_delivery_mst a ,wo_booking_mst b where b.id=a.order_id and a.id= $data[1]");
		$inserted_by=$sql_mst[0][csf("inserted_by")];
		if($data[2]==1)
		{
			$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
			$party_loc_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_location")]];
		}
		else
		{
			$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
			$party_loc_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_id")]];
		}
		$fac_merchant_arr=return_library_array( "select id, team_marchant from subcon_ord_mst",'id','team_marchant');
		$fac_merchant=$fac_merchant_arr[$sql_mst[0][csf("received_id")]];
		$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

		// $sql=sql_select(" SELECT 
		// from trims_delivery_dtls a  where a.mst_id=$data[1] and a.status_active=1 and a.is_deleted=0 order by a.buyer_po_id");


		$sql_dtls = "SELECT a.production_dtls_id, a.mst_id, a.order_id, a.order_no, a.buyer_po_id, a.buyer_po_no, a.buyer_style_ref, a.buyer_buyer, a.section, a.item_group as trim_group, a.order_uom, a.order_quantity, a.delevery_qty, a.claim_qty, a.remarks, a.color_id, a.description, a.delevery_status, a.color_name, a.size_name, a.workoder_qty, a.break_down_details_id, a.gmts_color_id, a.gmts_size_id, a.insert_date, b.po_quantity, b.job_no_mst , c.order_quantity as garments_qty, b.shipment_date, d.ply 
				from trims_delivery_dtls a 
				left join wo_po_break_down b on a.buyer_po_id = b.id				
				left join wo_po_color_size_breakdown c on b.id=po_break_down_id
				left join subcon_ord_breakdown d on d.id= a.break_down_details_id
				where a.mst_id=$data[1] and a.status_active=1 and a.is_deleted=0 order by a.buyer_po_id";
				    //    echo $sql_dtls;
				$dtls_data = array(); $break_down_id = [];
				$dtls_data_marge = []; $data_head_arr=array();
				$dtls_data_in_mst=array();
				$sql_dtls_result = sql_select($sql_dtls);
				foreach($sql_dtls_result as $key => $value){
					$dtls_data[$value[csf('buyer_po_no')]][$value[csf('trim_group')]][$value[csf('section')]][$value[csf('description')]][$value[csf('gmts_color_id')]][$value[csf('gmts_size_id')]]['group'] = $item_group_arr[$value[csf('trim_group')]];
					$dtls_data[$value[csf('buyer_po_no')]][$value[csf('trim_group')]][$value[csf('section')]][$value[csf('description')]][$value[csf('gmts_color_id')]][$value[csf('gmts_size_id')]]['description'] = $value[csf('description')];
					$dtls_data[$value[csf('buyer_po_no')]][$value[csf('trim_group')]][$value[csf('section')]][$value[csf('description')]][$value[csf('gmts_color_id')]][$value[csf('gmts_size_id')]]['color'] = $value[csf('color_name')];
					$dtls_data[$value[csf('buyer_po_no')]][$value[csf('trim_group')]][$value[csf('section')]][$value[csf('description')]][$value[csf('gmts_color_id')]][$value[csf('gmts_size_id')]]['size'] = $value[csf('size_name')];
					$dtls_data[$value[csf('buyer_po_no')]][$value[csf('trim_group')]][$value[csf('section')]][$value[csf('description')]][$value[csf('gmts_color_id')]][$value[csf('gmts_size_id')]]['gmts_size_id'] = $value[csf('gmts_size_id')];
					$dtls_data[$value[csf('buyer_po_no')]][$value[csf('trim_group')]][$value[csf('section')]][$value[csf('description')]][$value[csf('gmts_color_id')]][$value[csf('gmts_size_id')]]['gmts_color_id'] = $value[csf('gmts_color_id')];
					$dtls_data[$value[csf('buyer_po_no')]][$value[csf('trim_group')]][$value[csf('section')]][$value[csf('description')]][$value[csf('gmts_color_id')]][$value[csf('gmts_size_id')]]['section'] = $value[csf('section')];
					$dtls_data[$value[csf('buyer_po_no')]][$value[csf('trim_group')]][$value[csf('section')]][$value[csf('description')]][$value[csf('gmts_color_id')]][$value[csf('gmts_size_id')]]['uom'] = $unit_of_measurement[$value[csf('order_uom')]];
					$dtls_data[$value[csf('buyer_po_no')]][$value[csf('trim_group')]][$value[csf('section')]][$value[csf('description')]][$value[csf('gmts_color_id')]][$value[csf('gmts_size_id')]]['wo_qty'] = $value[csf('order_quantity')];
					$dtls_data[$value[csf('buyer_po_no')]][$value[csf('trim_group')]][$value[csf('section')]][$value[csf('description')]][$value[csf('gmts_color_id')]][$value[csf('gmts_size_id')]]['gar_wo_qty'] = $value[csf('garments_qty')];
					$dtls_data[$value[csf('buyer_po_no')]][$value[csf('trim_group')]][$value[csf('section')]][$value[csf('description')]][$value[csf('gmts_color_id')]][$value[csf('gmts_size_id')]]['del_qty'] = $value[csf('delevery_qty')];
					$dtls_data[$value[csf('buyer_po_no')]][$value[csf('trim_group')]][$value[csf('section')]][$value[csf('description')]][$value[csf('gmts_color_id')]][$value[csf('gmts_size_id')]]['remarks'] = $value[csf('remarks')];
					$dtls_data[$value[csf('buyer_po_no')]][$value[csf('trim_group')]][$value[csf('section')]][$value[csf('description')]][$value[csf('gmts_color_id')]][$value[csf('gmts_size_id')]]['ply'] = $value[csf('ply')];

					$dtls_data_in_mst[$value[csf('mst_id')]]["order_no"]=$value[csf('order_no')];
					$dtls_data_in_mst[$value[csf('mst_id')]]["buyer_buyer"]=$value[csf('buyer_buyer')];
					$dtls_data_in_mst[$value[csf('mst_id')]]["qty"]+=$value[csf('po_quantity')];

					$data_head_arr[$value[csf('buyer_po_no')]]['buyer_style_ref']=$value[csf('buyer_style_ref')];
					$data_head_arr[$value[csf('buyer_po_no')]]['po_quantity']=$value[csf('po_quantity')];
					$data_head_arr[$value[csf('buyer_po_no')]]['shipment_date']=$value[csf('shipment_date')];
					$data_head_arr[$value[csf('buyer_po_no')]]['job_no_mst']=$value[csf('job_no_mst')];
				}
				// echo "<pre>";
				// print_r($data_head_arr);     
        ?>

	    <div style="width:1200px" class="page-break">
	        <table width="100%" border="0">
				<tr>
					<td rowspan="2" width="200">
						
					</td>
	            	<td style="font-size:x-large;" align="center"><strong>
						<? echo $company_arr[$data[0]]; ?></strong>
	                </td>
	                <td align="right" width="100">
					</td>
	            </tr>
	            <tr>
	            	<td style="font-size:15px;" align="center"><? echo $company_address; ?></td>
				</tr>
				<tr>
	            	<td>&nbsp;</td>
	            	<td style="font-size:18px;" align="center"> <strong>Trims Delivery Challan</strong></td>
	                <td>&nbsp;</td>
	            </tr
	        ></table>
	        <br>
			<table>
				<tr>
				<td colspan="5" ><strong style="font-size: 25px;"> Delivery To,</strong></td>	               					
				</tr>
				<tr>
	                <td colspan="5" ><strong style="font-size: 25px;"> <? echo $party_arr[$sql_mst[0][csf("party_id")]]; ?></strong></td>					
				</tr>
			</table>
			<table width="100%" cellspacing="1" border="0">
			    <tr>	            	               
			       <td  width="120"><strong></strong></td>
	                <td  width="150"> </td>	
					<td  width="100"><strong style="font-size: 15px;">Buyer</strong></td>
	                <td  width="150"><strong style="font-size: 15px;">: <? echo $buyer_arr[$dtls_data_in_mst[$sql_mst[0][csf("id")]]["buyer_buyer"]];  ?></strong></td>
					<td  width="120"><strong style="font-size: 15px;">System Id </strong></td>
	                <td  width="150"><strong style="font-size: 15px;">: <? echo $data[5]; ?></strong></td>				             	              
	            </tr>
				<tr>	            	               
				    <td rowspan="2"  width="120"><strong style="font-size: 18px;">Address</strong></td>
	                <td rowspan="2" width="150"><strong style="font-size: 18px;">: <? echo $party_location; ?> </strong></td>	
					<td  width="100"><strong style="font-size: 15px;">Delivery Date</strong></td>
	                <td  width="150"><strong style="font-size: 15px;">: <? echo change_date_format($sql_mst[0][csf("delivery_date")],'yyyy-mm-dd'); ?></strong></td>
					<td  width="100"><strong style="font-size: 15px;">Wo No</strong></td>
	                <td  width="150"><strong style="font-size: 15px;">: <? echo $dtls_data_in_mst[$sql_mst[0][csf("id")]]["order_no"]  ?></strong></td>	
	            </tr>			
	            <tr>	
				    <td  width="100"><strong style="font-size: 15px;">Source</strong></td>
	                <td  width="150"><strong style="font-size: 15px;">: <? echo $source[$sql_mst[0][csf("source")]]; ?></strong></td>	
					<td  width="100"><strong style="font-size: 15px;">Remarks</strong></td>
	                <td  width="150"><strong style="font-size: 15px;">: <? echo $sql_mst[0][csf("remarks")]; ?></strong></td> 	
				          	                  					 					                         					
	            </tr>	          	
				<tr>	            	               
				               
	                <td  width="100"><strong style="font-size: 15px;">Pay mode</strong></td>
	                <td  width="150"><strong style="font-size: 15px;">: <? echo $pay_mode[$sql_mst[0][csf("pay_mode")]] ?></strong></td>
					<td  width="100"><strong style="font-size: 15px;">Challan No</strong></td>
	                <td  width="150"><strong style="font-size: 15px;">: <? echo  $sql_mst[0][csf("challan_no")];  ?></strong></td>
	            </tr>	         	            
	      	</table>
	         <br>
	         <style>
	         .rpt_table tfoot th {
                background-image: -moz-linear-gradient( rgb(194,220,255) 10%, rgb(136,170,214) 96%);
                border: 1px solid #8DAFDA;
                color: #444;
                font-size: 13px;
                font-weight: bold;
                line-height: 12px;
                padding-right: 2px;
                padding-left: 2px;
                height: 25px;
            }
            </style>      	
	            <?
				// $dtls_data[$value[csf('buyer_po_no')]][$value[csf('trim_group')]][$value[csf('section')]][$value[csf('description')]][$value[csf('gmts_color_id')]][$value[csf('gmts_size_id')]]['remarks'] = $value[csf('remarks')];
	            $i = 1;  $tot_bal_qty = 0;$qty=0;

					foreach($dtls_data as $buyer_po_no => $buyer_po_arr)
					{
						$buyer_style_ref=$data_head_arr[$buyer_po_no]['buyer_style_ref'];
						$qty=$data_head_arr[$buyer_po_no]['po_quantity'];
						$job=$data_head_arr[$buyer_po_no]['job_no_mst'];
						?>
						<table  class="rpt_table" width="1400" cellspacing="1" rules="all" border="1">
						<thead>
								<tr>							
									<th align="center" colspan="2" >Style No:   <?=$buyer_style_ref;?> </th>
									<th align="center" >Qty:<?=$qty?> </th>
									<th align="center" colspan="3">PO No:  <?=$buyer_po_no?> </th>
									<th > JOB: <?=$job?></th>
									<th colspan="3">Shipment Date: <?= change_date_format($data_head_arr[$buyer_po_no]['shipment_date'])?></th>
									<th colspan="5"></th>
									
								</tr>
							</thead>
							<thead>
								<tr>
									<th width="20">SL</th>
									<th width="90">Item Group</th>
									<th width="140">Section.</th>
									<th width="130">Item Description</th>
									<th width="100">Grmnts Order Qty</th>
									<th width="120">Item Color</th>
									<th width="150">Gmts Color.</th>
									<th width="80">Gmts Size </th>
									<th width="70">Item Size</th>
									<th width="60">Ply</th>
									<th width="80">WO Qty.</th>
									<th width="80">Todays Delivery Qty.</th>
									<th width="75">UOM.</th>
									<th>Remarks</th>
								</tr>
							</thead>
							<tbody>
						<?
						foreach($buyer_po_arr as $trim_group => $trim_group_arr)
						{
							foreach($trim_group_arr as $section => $section_arrr)
							{
								foreach($section_arrr as $description => $description_arrr)
								{
									$tot_wo_qty = 0; $tot_cur_del_qty = 0;
									foreach($description_arrr as $gmts_color_id => $gmts_color_arrr)
									{
										foreach($gmts_color_arrr as $gmts_size_id => $value)
										{
					
											?>
											<tr>			
												<td valign="middle" style="font-size: 10pt; padding: 3px;" align="center"><?=$i?></td>
												<td valign="middle"  style="font-size: 10pt; padding: 3px;"><?=$value['group'];?></td>
												<td valign="middle" style="font-size: 10pt; padding: 3px;"><?=$trims_section[$value['section']];?></td>
												<td valign="middle" style="font-size: 10pt; padding: 3px;"><?=$value['description'];?></td>
												<td valign="middle" style="font-size: 10pt; padding: 3px;"><?=number_format($value['gar_wo_qty'], 2);?></td>
												<td valign="middle" style="font-size: 10pt; padding: 3px;"><?=$value['color']?></td>
												<td valign="middle" style="font-size: 10pt; padding: 3px;"><?=$color_library[$value['gmts_color_id']]?></td>
												<td valign="middle" style="font-size: 10pt; padding: 3px;" align="center"><?=$size_arr[$value['gmts_size_id']]?></td>
												<td valign="middle" style="font-size: 10pt; padding: 3px;" align="center"><?=$value['size']?></td>
												<td valign="middle" style="font-size: 10pt; padding: 3px;" align="center"><?=$value['ply']?></td>
												<td valign="middle" style="font-size: 10pt; padding: 3px;" align="right"><?=number_format($value['wo_qty'], 2)?></td>
												<td valign="middle" style="font-size: 10pt; padding: 3px;" align="right"><?=number_format($value['del_qty'], 2)?></td>
												<td valign="middle" style="font-size: 10pt; padding: 3px;" align="center"><?=$value['uom']?></td>
												<td valign="middle" style="font-size: 10pt; padding: 3px;"><?=$value['remarks']?></td>
											</tr>
											<?
											$i++;
											$tot_wo_qty += $value['wo_qty'];
											$tot_cur_del_qty += $value['del_qty'];
											
								         }
						             }
								}
							}
											?>
											<tr>
												<th colspan="10" align="right">Item Total</th>
												<th align="right"><?=number_format($tot_wo_qty, 2)?></th>
												<th align="right"><?=number_format($tot_cur_del_qty, 2)?></th>
												<th align="right"></th>
												<th></th>

											</tr>
											<?
						}
						?>
						  </tbody>
						</table>
						<?
					}
		
                ?>        
	    
		<?
		
		?>
		<br>
		<table align="center"  width="950" class="rpt_table"  cellspacing="1" rules="all" border="1">      
			<thead>    
				<tr>
					<th align="center" width="100" ><b>Sl</b></th>
					<th align="center" ><b>Terms and Conditions/Notes:</b></th>
				</tr>
			</thead>
              <?
			   $sql_select=sql_select("SELECT terms_prefix,terms from wo_booking_terms_condition where booking_no='$data[1]'");
                $i=1;
                foreach($sql_select as $tearms){          
                ?>
				 <tbody>
					<tr>
						<td><? echo $i?> </td>
						<td ><?echo $tearms[csf("terms")];?> </td>
					</tr>
				</tbody>
                <?
				$i++;
            }
            ?>
            <tr>
        </table>
		<br>
		<?

		 $sql_data = "SELECT a.item_group, a.buyer_po_no, a.order_quantity, b.id, a.delevery_qty, a.workoder_qty,a.section, b.po_quantity
		from trims_delivery_dtls a left join wo_po_break_down b on a.buyer_po_id = b.id where a.mst_id=$data[1] and a.status_active=1 and a.is_deleted=0 order by a.id";
		$sql_data_result = sql_select($sql_data);
		$buyer_po_data_arr=array();
		foreach($sql_data_result as $row){
			$buyer_po_data_arr[$row[csf("item_group")]][$row[csf("id")]]['buyer_po_no']=$row[csf('buyer_po_no')];
			$buyer_po_data_arr[$row[csf("item_group")]][$row[csf("id")]]['item_group']=$row[csf('item_group')];
			$buyer_po_data_arr[$row[csf("item_group")]][$row[csf("id")]]['order_quantity']+=$row[csf('order_quantity')];
			$buyer_po_data_arr[$row[csf("item_group")]][$row[csf("id")]]['delevery_qty']+=$row[csf('delevery_qty')];
			$buyer_po_data_arr[$row[csf("item_group")]][$row[csf("id")]]['po_quantity']=$row[csf('po_quantity')];

		}
		// echo "<pre>";
		// print_r($buyer_po_data_arr);
		
		
	      	?>
		<table  class="rpt_table" width="800" cellspacing="1" rules="all" border="1">
			<thead>
				<tr>
					<th width="20">SL</th>
					<th width="90">Item Name</th>
					<th width="140">PO Number.</th>
					<th width="130">PO Qty</th>
					<th width="100">WO Qty</th>
					<th width="100">Todays Delivery Qty</th>
				
				</tr>
			</thead>
			<tbody>
				<? 
				$order_qty="";
				$delevery_qty="";
				$i=1;
				
			foreach($buyer_po_data_arr as $buyer_po_id=> $buyer_po_id_arr){	
				$sub_workoder_qty="";$sub_order_qty="";$sub_delevery_qty="";
				foreach($buyer_po_id_arr as $buyer_po_no=> $row){
					?>
					<tr>
						<td width="20"><?=$i?></td>
						<td width="90" align="center"><?=$item_group_arr[$row["item_group"]]?></td>
						<td width="140" align="center"><?=$row["buyer_po_no"]?></td>
						<td width="130" align="right"><?=$row["po_quantity"]?></td>
						<td width="100" align="right"><?=$row["order_quantity"]?></td>
						<td width="100" align="right"><?=$row["delevery_qty"]?></td>				
					</tr>
					<? 
					$i++;
					$sub_order_qty+=$row["order_quantity"];
					$sub_delevery_qty+=$row["delevery_qty"];
					$sub_workoder_qty+=$row["po_quantity"];
				}
				?>
					<tr>
						<th colspan="3" align="right">Sub Total</th>
						<th align="right"><?= $sub_workoder_qty?></th>
						<th align="right"><?= $sub_order_qty?></th>
						<th align="right"><?= $sub_delevery_qty?></th>
					</tr>
				</tbody>
				<?
				$total_sub_order_qty+=$sub_order_qty;
				$total_sub_delevery_qty+=$sub_delevery_qty;			
			}
			?>
			<tfoot>
				<tr>
					<th colspan="4" align="right"> Total</th>
					<th align="right"><?= $total_sub_order_qty?></th>
					<th align="right"><?= $total_sub_delevery_qty?></th>
				</tr>
			</tfoot>
		</table>

		<?
			$user_lib_name=return_library_array("select id,user_full_name from user_passwd where id=$inserted_by", "id", "user_full_name");
			echo signature_table(174, $data[0], "1200px",$cbo_template_id,60,$user_lib_name[$inserted_by]);
	    ?>
	    </div>

	   <?
	 	exit();
	}

	
	if($action=="challan_print7") 
	{
		extract($_REQUEST);
		//echo $data;die;
		$data=explode('*',$data);
		$cbo_template_id=$data[6];
		$show_color=$data[8];
		
		$country_full_name = return_library_array("SELECT id,country_name from lib_country", "id", "country_name");
		$company_sql = "SELECT * FROM lib_company WHERE id = $data[0]  AND is_deleted=0 AND status_active=1 ORDER BY company_name ASC";
		$result = sql_select( $company_sql );
		foreach( $result as $row  )
		{
			if($row[csf("level_no")])		$level_no	= "Level NO #".$row[csf("level_no")].', ';
			if($row[csf("plot_no")])		$plot_no 	= "PLOT NO #".$row[csf("plot_no")].', ';
			if($row[csf("road_no")]) 		$road_no 	= "Road #".$row[csf("road_no")].', ';
			if($row[csf("block_no")]!='')	$block_no 	= "Sector #".$row[csf("block_no")].', ';
			if($row[csf("zip_code")]!='')	$zip_code 	= $row[csf("zip_code")].', ';
			if($row[csf("city")]!='') 		$city 		= $row[csf("city")].', ';
			if($row[csf("country_id")]!='')	$country 	= $country_full_name[$row[csf("country_id")]].'.';
			if($row[csf("contact_no")]!='')	$contact_no = $row[csf("contact_no")];
			if($row[csf("email")]!='')		$email 		= $row[csf("email")];
			if($row[csf("website")]!='')	$website 	= $row[csf("website")].'.';
			if($row[csf("group_id")]!='')	$group_id 	= $row[csf("group_id")]; 
		}

		$head_oofice= $plot_no.$level_no.$road_no.$city.$block_no.$zip_code;
		$company_address=$head_oofice.'</br></br> Tel: '.$contact_no.', Email: '.$email;

		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$company_plot_no_arr=return_library_array( "select id, plot_no from lib_company",'id','plot_no');
		$buyer_po_arr=array();
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');


		$sql_mst = sql_select("SELECT id, entry_form, trims_del, del_no_prefix, del_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id,  delivery_date, received_id, order_id, challan_no, gate_pass_no, remarks,inserted_by, deli_party_location from trims_delivery_mst where id= $data[1]");
		$inserted_by=$sql_mst[0][csf("inserted_by")];
		$user_lib_name=return_library_array("select id,user_full_name from user_passwd where id=$inserted_by", "id", "user_full_name");

		if($data[2]==1)
		{
			$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
			$party_loc_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_location")]];
		}
		else
		{
			$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
			$party_loc_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_id")]];
		}
		
		$del_party_location = '';
		if ($sql_mst[0][csf("within_group")]==2) {

			$lib_buyer_id = $sql_mst[0][csf("party_id")];

			$com_loc = sql_select("SELECT id, address_1, address_2, address_3, address_4 from lib_buyer where id= $lib_buyer_id");

			if($com_loc[0][csf("address_1")]!=''){

				$del_party_location .= $com_loc[0][csf("address_1")];
			}

			if($com_loc[0][csf("address_2")]!=''){

				if ($del_party_location!='') {
					$del_party_location .= ", ";
				}

				$del_party_location .= $com_loc[0][csf("address_2")];
			}

			if($com_loc[0][csf("address_3")]!=''){

				if ($del_party_location!='') {
					$del_party_location .= ", ";
				}

				$del_party_location .= $com_loc[0][csf("address_3")];
			}

			if($com_loc[0][csf("address_4")]!=''){

				if ($del_party_location!='') {
					$del_party_location .= ", ";
				}

				$del_party_location .= ", ".$com_loc[0][csf("address_4")];
			}
		}
		else
		{
			$com_loc_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
			$del_party_location=$com_loc_arr[$sql_mst[0][csf("deli_party_location")]];
		}
		$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");

		$sql = "SELECT a.ID, b.BUYER_PO_NO, a.BUYER_BUYER, a.item_group as TRIM_GROUP, a.ORDER_UOM, a.DELEVERY_QTY,a.REMARKS,a.COLOR_ID,c.SIZE_ID, a.DESCRIPTION, a.COLOR_NAME,c.SIZE_NAME, a.NO_OF_ROLL_BAG,c.STYLE,d.SUBCON_JOB
		from trims_delivery_dtls a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_ord_mst d
		where a.mst_id='$data[1]' and a.receive_dtls_id=b.id and a.break_down_details_id = c.id and b.id=c.mst_id and c.status_active=1 and a.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.delevery_qty>0 and b.mst_id = d.id
		order by b.buyer_po_no,a.buyer_buyer,a.item_group,c.style,a.description,a.color_name,a.size_name ASC";
		// echo $sql;
		$data_array=sql_select($sql);
		foreach($data_array as $row)
		{
			$job_no=$row['SUBCON_JOB'];
			if($row['BUYER_BUYER'])
			{
				if($data[2]==1){ $buyers_buyer.=$buyer_arr[$row['BUYER_BUYER']].","; } 
				else { $buyers_buyer.=$row['BUYER_BUYER'].","; }
			}
		}		

		?>
		<style>
			.divfont table tbody tr td{
				font-size: 20px; 
			}
			@media print {
				*{font-family: verdana, sans-serif; }
				div.divFooter {
					position: fixed;
					bottom:100px;
				}
				tr.page_break {page-break-before: always; }
			}
		</style>
		<div style="width:1190px" align="left">
			<table width="1180">
				<tr>
					<td style="font-size:xxx-large" align="center"><strong><? echo $company_arr[$data[0]]; ?></strong></td>
				</tr>
				<tr>
					<td style="font-size:20px" align="center"><? echo $company_address; ?></td>
				</tr>
				<tr>
					<td height="15px;"></td>
				</tr>
				<tr>
					<td style="font-size:35px;" align="center"><strong><? echo 'DELIVERY CHALLAN'; ?></strong></td>
				</tr>
				<tr>
					<td height="20px;"></td>
				</tr>
			</table>
			<br>
			<table class="rpt_table" width="1180" cellspacing="5" >
				<tr>
					<td width="120" valign="top" ><strong>DELIVERY TO</strong></td>
					<td width="400" valign="top"><strong>: <? echo $party_arr[$sql_mst[0][csf("party_id")]]; ?></strong></td>
					<td width="100">&nbsp;</td>
					<td width="150"><strong>CHALLAN NO. </strong></td>
					<td ><strong>: <? echo $data[5]; ?></strong></td>
				</tr>
				<tr>
					<td valign="top" ><strong>ADDRESS</strong></td>
					<td valign="top"><strong>: <? echo $party_location; ?></strong></td>
					<td>&nbsp;</td>
					<td valign="top" ><strong>DELIVERY DATE </strong></td>
					<td valign="top" ><strong>: <? echo change_date_format($sql_mst[0][csf("delivery_date")],'yyyy-mm-dd'); ?></strong></td>
				</tr>
				<tr>
					<td valign="top"><strong>DELIVERY PLACE</strong></td>
					<td valign="top"><strong>: <? echo $del_party_location;?></strong></td>
					<td>&nbsp;</td>
					<td valign="top" ><strong>JOB NO </strong></td>
					<td valign="top" ><strong>: <? echo $job_no;?></strong></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td valign="top" ><strong>BUYER </strong></td>
					<td valign="top"  ><strong>: <?=implode(", ",array_unique(explode(",",chop($buyers_buyer,','))))?></strong></td>
				</tr>
				<tr>
					<td colspan="4" height="20px;"></td>
				</tr>
			</table>
			<br>
			<table  class="rpt_table" width="1180" cellspacing="1" cellpadding="5" rules="all" border="1">
				<thead>
					<tr>
						<th style="border:4px solid black" width="40">SL</th>
						<th style="border:4px solid black" width="90">Item</th>
						<th style="border:4px solid black" width="100">PO</th> 
						<th style="border:4px solid black" width="150">Style No.</th>
						<th style="border:4px solid black" width="180">Item Description</th>	
						<?
							if($show_color==1){?><th style="border:4px solid black" width="60">Item Color </th><?}
						?>						
						<th style="border:4px solid black" width="140">Item Size</th>				
						<th style="border:4px solid black" width="50">Order UOM</th>
						<th style="border:4px solid black" width="70">Qty.</th>
						<th style="border:4px solid black" width="60">No of Roll/Pkt</th>
						<th style="border:4px solid black">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i = 1;
					foreach($data_array as $row)
					{
						if($i==12 || $i==29 || $i==46 || $i==64 || $i==82 || $i==100){ $pagebreak = " class='page_break'";} else{ $pagebreak = "";}
						?>
						<tr <?=$pagebreak; ?>>
							<td align="center" style="border:3px solid black" ><?php echo $i; ?></td>
							<td align="center" style="border:3px solid black" ><p><?php echo $item_group_arr[$row['TRIM_GROUP']]; ?></p></td>
							<td align="center" style="border:3px solid black" ><p><?php echo $row['BUYER_PO_NO']; ?></p></td>
							<td align="center" style="border:3px solid black" ><p><?php echo $row['STYLE']; ?></p></td>	
							<td align="center" style="border:3px solid black" ><p><?php echo $row['DESCRIPTION']; ?></p></td>	
							<?
								if($show_color==1){?><td style="border:3px solid black" align="center"><p><?php echo $row['COLOR_NAME']; ?></p> </td><?}
							?>						
							<td align="center" style="border:3px solid black" ><p>
							<?php
							if($row['SIZE_ID'] > 0){
                                echo $size_arr[$row['SIZE_ID']];
                             }else{
                                echo $row['SIZE_NAME'];
							 }
							?>
							</p></td>
							<td align="center" style="border:3px solid black" ><?php echo $unit_of_measurement[$row['ORDER_UOM']];  ?></td>
							<td align="right" style="border:3px solid black" ><?php echo $row['DELEVERY_QTY'];  ?></td>
							<td align="right" style="border:3px solid black" ><?php echo $row['NO_OF_ROLL_BAG'];?></td>						
							<td style="word-break: break-all;" style="border:3px solid black" ><?php echo $row['REMARKS']; ?></td>
						</tr>
						<?
						$i++;
						$total_delevery_qty += $row['DELEVERY_QTY'];
						$total_no_of_roll_bag += $row['NO_OF_ROLL_BAG'];
						$unique_uom[$row['ORDER_UOM']]=$row['ORDER_UOM'];
					} 
					if(count($unique_uom)==1)
					{ 
						?>
						<tr> 
							<td style="border:3px solid black" colspan="<?=($show_color==1)?7:6;?>"><strong>&nbsp;&nbsp;</strong></td>
							<td style="border:3px solid black" align="right"><strong>Total:</strong></td>
							<td style="border:3px solid black" align="right"><strong><? echo number_format($total_delevery_qty,2); ?></strong></td>
							<td style="border:3px solid black" align="right"><strong><? echo number_format($total_no_of_roll_bag,2); ?></strong></td>
							<td style="border:3px solid black"><strong>&nbsp;&nbsp;</strong></td>
						</tr>
						<? 
					} ?>
				</tbody> 
			</table>
			<div class="divFooter">
				<div class="divfont">
				<?
					$user_lib_name=return_library_array("select id,user_full_name from user_passwd where id=$inserted_by", "id", "user_full_name");
					echo signature_table(174, $data[0], "1180px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
				?>	
				</div>
				<br>
				<table width="1180">
					<tr>
						<td width="50"></td>
						<td width="600"></td>
						<td align="center" style="font-size:14px;"><strong>Product Acknowledgement</strong> </td>
					</tr>
					<tr>
						<td colspan="2"></td>
						<td align="center" style="font-size:14px;"><strong>Received the commodity in full quantity and good condition</strong></td>
					</tr>
					<tr>
						<td align="center" style="font-size:14px;"><strong>NOTE:- </strong></td>
						<td colspan="2" style="font-size:14px;"><strong>ANY COMPLAINT REGARDING SHORTAGE AND DAMAGE SHOULD BE BROUGHT TO THE NOTICE WITHIN THREE WORKING DAYS IN WRITING. </strong></td>
					</tr>
				</table>
			</div>

		</div>
		<?
	 	exit();
		
	}

	if($action=="del_multi_number_popup")
	{
		echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
		extract($_REQUEST);
		$data=explode("_",$data);
		?>

		<script>

			function toggle( x, origColor ) {
				var newColor = 'yellow';
				if ( x.style ) {
					x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
				}
			}

			function check_all_data()
			{
				
				var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
				//tbl_row_count = tbl_row_count-1;
				for( var i = 1; i <= tbl_row_count; i++ ) {
					var attrData=$('#tr_' +i).attr('onclick');
					var splitArr = attrData.split('"');
					js_set_value( splitArr[1] );
				}
			}

			var selected_id=Array();
			var selected_name=Array();
			var selected_ord=Array();

			function js_set_value(mrr)
			{
				//alert(mrr);
				var splitArr = mrr.split("_");
				$("#hidden_del_number").val(splitArr[1]); // mrr number
				$("#hidden_del_id").val(splitArr[2]); // id
				$("#hidden_ord_no").val(splitArr[3]); // order no

				toggle( document.getElementById( 'tr_' + splitArr[0] ), '#FFFFCC' );

		 		if( jQuery.inArray(splitArr[2], selected_id ) == -1 ) {			
		 			selected_name.push(splitArr[1]);
		 			selected_id.push( splitArr[2]);
		 			selected_ord.push( splitArr[3]);

		 		}
		 		else 
		 		{
		 			for( var i = 0; i < selected_id.length; i++ ) {
		 				if( selected_id[i] == splitArr[2]) break;
		 			} 			
		 			selected_name.splice( i, 1 );
		 			selected_id.splice( i, 1 );
		 			selected_ord.splice( i, 1 );
		 		}

		 		var id = ''; var name = ''; var ord = '';
		 		for( var i = 0; i < selected_id.length; i++ ) {
		 			id += selected_id[i] + ',';
		 			name += selected_name[i] + ',';
		 			ord += selected_ord[i] + ',';
		 		}

		 		id = id.substr( 0, id.length - 1 );
		 		name = name.substr( 0, name.length - 1 );
		 		ord = ord.substr( 0, ord.length - 1 );

		 		$('#hidden_del_id').val(id);
		 		$('#hidden_del_number').val(name);
		 		$('#hidden_ord_number').val(ord);
		 	}

		 	function fnc_close ()
		 	{
		 		parent.emailwindow.hide();
		 	}

		 	function fnc_load_party_popup(type,within_group,party)
			{
				//alert(within_group);
				var company = $('#cbo_company_name').val();
				var party_name = $('#cbo_party_name').val();
				var location_name = $('#cbo_location_name').val();
				//var within_group = $('#cbo_within_group').val();
				load_drop_down( 'trims_delivery_entry_controller', company+'_'+within_group+'_'+party, 'load_drop_down_buyer', 'buyer_td_del' );
				$('#cbo_party_name').attr('disabled',true);
			}
			function search_by(val)
			{
				$('#txt_search_string').val('');
				if(val==1 || val==0){
					$('#search_by_td').html('System ID');
				}else if(val==2){
					$('#search_by_td').html('W/O No');
				}else if(val==4){
					$('#search_by_td').html('Buyer Po');
				}else if(val==5){
					$('#search_by_td').html('Buyer Style');
				}
			}
			
	 	</script>
		</head>

		<body onLoad="fnc_load_party_popup(<? echo "$data[0]";?>,<? echo "$data[3]";?>,<? echo "$data[2]";?>)">
			<div align="center" style="width:100%;" >
				<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
					<table width="940" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
		            <thead> 
		                <tr>
		                    <th colspan="9"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
		                </tr>
		                <tr>               	 
		                    <th width="140" class="must_entry_caption">Company Name</th>
		                    <th width="60">Within Group</th>                           
		                    <th width="140">Party Name</th>
		                    <th width="80">Search By</th>
		                    <th width="100" id="search_by_td">System ID</th>
		                    <th width="60">Year</th>
		                    <th width="170">Date Range</th>                            
		                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
		                </tr>
		            </thead>
		            <tbody>
		                <tr class="general">
		                    <td><input type="hidden" id="selected_job">  <!--  echo $data;-->
		                        <? 
		                        echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
		                    </td>
		                    <td>
		                        <?php echo create_drop_down( "cbo_within_group", 60, $yes_no,"", 0, "--  --", $data[3], "fnc_load_party_popup(1,this.value);",1 ); ?>
		                    </td>
		                    <td id="buyer_td_del">
		                        <? echo create_drop_down( "cbo_party_name", 150, "","", 1, "-- select Party --", $data[2], "fnc_load_party_popup(1,this.value);",1 );   	 
		                        ?>
		                    </td>
		                    <td>
		                    	<?
		                            $search_by_arr=array(1=>"System ID",2=>"W/O No",4=>"Buyer Po",5=>"Buyer Style");
		                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
		                        ?>
		                    </td>
		                    <td align="center">
		                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
		                    </td>
		                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- select --", date('Y'), "",0 ); ?></td>
		                    <td align="center">
		                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
		                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
		                    </td>
		                    <td align="center">
		                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value, 'create_multi_delivery_search_list_view', 'search_div', 'trims_delivery_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)');" style="width:70px;" /></td>
		                    </tr>
		                    <tr>
		                        <td colspan="9" align="center" valign="middle">
		                            <? echo load_month_buttons();  ?>
		                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
		                        </td>
		                    </tr>
		                    <tr>
		                        <td colspan="9" align="center" valign="top" id=""><div id="search_div"></div></td>
		                    </tr>
		                </tbody>
		            </table>
		            <table width="820" cellspacing="0" cellpadding="0" style="border:none" align="center">
						<tr>
							<td align="center" height="30" valign="bottom">
								<div style="width:100%;">
									<div style="width:50%; float:left" align="left" id="button_div">
										<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
									</div>
									<div style="width:50%; float:left" align="left">
										<input type="button" name="close" onClick="fnc_close();" class="formbutton" value="Close" style="width:100px" />
									</div>
								</div>
							</td>
						</tr>
					</table>  
				<div align="center" style="margin-top:10px" valign="top" id="search_divvvv"> </div>
			</form>
		</div>
		</body>
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
		</html>
		<?
	}


	if($action=="create_multi_delivery_search_list_view")
	{	
		echo '<input type="hidden" id="hidden_del_number" value="" /><input type="hidden" id="hidden_del_id" value="" /><input type="hidden" id="hidden_ord_number" value="" />';
		$data=explode('_',$data);
		$party_id=str_replace("'","",$data[1]);
		$search_by=str_replace("'","",$data[4]);
		$search_str=trim(str_replace("'","",$data[5]));
		$search_type =$data[6];
		$within_group =$data[7];
		if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
		if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}

		if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please select Company First."; die; }
		//echo $search_type; die;
		$job_cond=""; $style_cond=""; $po_cond=""; $search_com="";
		if($search_type==1)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com="and a.del_no_prefix_num='$search_str'";
				else if($search_by==2) $search_com="and b.order_no='$search_str'";
				else if ($search_by==4) $search_com=" and b.buyer_po_no = '$search_str' ";
				else if ($search_by==5) $search_com=" and b.buyer_style_ref = '$search_str' ";
			}
		}
		else if($search_type==2)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com="and a.del_no_prefix_num like '$search_str%'";  
				else if($search_by==2) $search_com="and b.order_no like '$search_str%'";  
				else if ($search_by==4) $search_com=" and b.buyer_po_no like '$search_str%'";
				else if ($search_by==5) $search_com=" and b.buyer_style_ref like '$search_str%'";  
			}
		}
		else if($search_type==3)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com="and a.del_no_prefix_num like '%$search_str'";  
				else if($search_by==2) $search_com="and b.order_no like '%$search_str'";  
				else if ($search_by==4) $search_com=" and b.buyer_po_no like '%$search_str'";
				else if ($search_by==5) $search_com=" and b.buyer_style_ref like '%$search_str'";  
			}
		}
		else if($search_type==4 || $search_type==0)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com="and a.del_no_prefix_num like '%$search_str%'";  
				else if($search_by==2) $search_com="and b.order_no like '%$search_str%'";  
				else if ($search_by==4) $search_com=" and b.buyer_po_no like '%$search_str%'"; 
				else if ($search_by==5) $search_com=" and b.buyer_style_ref like '%$search_str%'";   
			}
		}

		if($search_str!="" && ($search_by==2 || $search_by==4 || $search_by==5))
		{
			if($db_type==0) $id_cond="group_concat(b.id) as id";
			else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id";

			$job_dtls_ids = return_field_value("$id_cond", "trims_job_card_mst a, trims_job_card_dtls b", "a.entry_form=257 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $search_com_cond", "id");// and a.trims_job=b.job_no_mst
		}

		if($db_type==2 && $job_dtls_ids!="") $job_dtls_ids = $job_dtls_ids->load();
		if ($job_dtls_ids!="")
		{
			$job_dtls_ids=explode(",",$job_dtls_ids);
			$job_dtls_idsCond=""; $jobDtlsCond="";
			//echo count($job_dtls_ids); die;
			if($db_type==2 && count($job_dtls_ids)>=999)
			{
				$chunk_arr=array_chunk($job_dtls_ids,999);
				foreach($chunk_arr as $val)
				{
					$ids=implode(",",$val);
					if($job_dtls_idsCond=="")
					{
						$job_dtls_idsCond.=" and ( b.job_dtls_id in ( $ids) ";
					}
					else
					{
						$job_dtls_idsCond.=" or  b.job_dtls_id in ( $ids) ";
					}
				}
				$job_dtls_idsCond.=")";
			}
			else
			{
				$ids=implode(",",$job_dtls_ids);
				$job_dtls_idsCond.=" and b.job_dtls_id in ($ids) ";
			}
		}
		else if($job_dtls_ids=='' && ($search_str!="" && ($search_by==2 || $search_by==4 || $search_by==5)))
		{
			echo "Not Found"; die;
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
		
		
		$sql= "select a.id, a.trims_del, a.del_no_prefix, a.del_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.delivery_date, a.received_id, a.order_id, a.challan_no, a.gate_pass_no,b.order_no ,$ins_year_cond as year from trims_delivery_mst a, trims_delivery_dtls b where a.entry_form=208 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $delivery_date $company $party_id_cond $withinGroup $search_com $withinGroup group by a.id, a.trims_del, a.del_no_prefix, a.del_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.delivery_date, a.received_id, a.order_id, a.challan_no, a.gate_pass_no,b.order_no ,a.insert_date order by a.id DESC";
		// echo $sql;
		 $data_array=sql_select($sql);
		?>
	     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="820" >
	        <thead>
	            <th width="30">SL</th>
	            <th width="150">Delivery No</th>
	            <th width="150">Work Order No</th>
	            <th width="80">Year</th>
	            <th width="170">Challan No.</th>
	            <th width="80">Delivery Date</th>
	            <th> Within Group</th>
	        </thead>
	        </table>
	        <div style="width:820px; max-height:240px;overflow-y:scroll;" >	 
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="list_view">
	        <tbody>
	            <? 
	            $i=1;
	            foreach($data_array as $row)
	            {  
	                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                //1_FAL-YIR-20-00009_39249_20708_8978
	                ?>
	                <tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $i.'_'.$row[csf('trims_del')].'_'.$row[csf('id')].'_'.$row[csf('order_no')]; ?>")' style="cursor:pointer" >
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="150"><? echo $row[csf('trims_del')]; ?></td>
	                    <td width="150"><? echo $row[csf('order_no')]; ?></td>
	                    <td width="80" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
	                    <td width="170"><? echo $row[csf('challan_no')]; ?></td>
	                    <td width="80"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
	                    <td style="text-align:center;"><? echo $yes_no[$row[csf('within_group')]]; ?></td>
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


	if($action=="multi_del_print") 
	{
		extract($_REQUEST);
		//echo $data;//die;
		$data=explode('*',$data);
		$cbo_template_id=$data[6];
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$buyer_po_arr=array();
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
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
				top: 20%;
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
			
			#table_1,#table_2{  background-position: center;background-repeat: no-repeat; }
			#table_1{background-image:url(../../../img/bg-1.jpg);}
			#table_2{background-image:url(../../../img/bg-2.jpg); }
			/* #table_3{background-image:url(../../../img/bg-3.jpg);} */
			
		</style>
		<?
		$sql_mst = sql_select("select id, entry_form, trims_del, del_no_prefix, del_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id,  delivery_date, received_id, order_id, challan_no, gate_pass_no, remarks,inserted_by from trims_delivery_mst where id in($data[1])");
		$inserted_by=$sql_mst[0][csf("inserted_by")]; 

		if($data[2]==1)
		{
			$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
			$party_loc_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_location")]];
		}
		else
		{
			$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
			$party_loc_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_id")]];
		}
		$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		$com_dtl_info=fnc_company_location_address($data[0],'',1);
		$k=0;	
		$copy_no=array(1,2,3); //for Dynamic Copy here 
		foreach($copy_no as $cid)
		{
			$k++;
			?>
		    <div style="width:1700px" class="page-break">
		        <table width="100%" id="table_<? echo $cid;?>">
					<tr>
						<td rowspan="2" width="200">
							<img src="../../<? echo $com_dtl_info[2]; ?>" height="60" width="200" style="float:left;">
						</td>
		            	<td style="font-size:20px;" align="center"><strong>
							<? echo $com_dtl_info[0]; ?></strong>
		                </td>
		                <td align="right" width="100">
							<? 
							if($k==1){
							echo "<b><h2>1st Copy</h2></b>";
							}
							else if($k==2){
							echo "<b><h2>2nd Copy</h2></b>";
							}
							else if($k==3){
							echo "<b><h2>3rd Copy</h2></b>";
							}
							?> 
						</td>
		            </tr>
		            <tr>
						<td align="center">
							<?
							echo $com_dtl_info[1];
							?> 
						</td>
		        		<td id="barcode_img_id_<? echo $k; ?>"></td>
					</tr>
					<tr>
		            	<td>&nbsp;</td>
		            	<td style="font-size:20px;" align="center"> <strong><? echo $data[3]; ?></strong></td>
		                <td>&nbsp;</td>
		            </tr> 
		            <tr>
		            	<td>&nbsp;</td>
		            	<td>&nbsp;</td>
		                <td>&nbsp;</td>
		            </tr> 
		        </table>
		        <br>
				<table class="rpt_table" width="100%" cellspacing="1" >
		            <tr>
		                <td valign="top" width="100"><strong>Delivery Point</strong></td>
		                <td valign="top"><strong id="delivery_point_td_<?php echo $cid; ?>"></strong></td>
		            </tr>
		            <tr>
		                <td valign="top" width="100"><strong>Delivery To</strong></td>
		                <td valign="top">:<strong> <? echo $party_arr[$sql_mst[0][csf("party_id")]]; ?></strong></td>
		            </tr>
		            <tr>
		            	<td valign="top" width="120">Address</td>
		                <td valign="top">: <? echo $party_location; ?> </td>
		            </tr>
		      	</table>
		        <br>
		      	<table  class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
		      		<thead>
			            <tr>
			            	<th width="40">SL</th>
		                    <th width="130">Cust. PO</th>
		                    <th width="150">Cust. WO No</th> 
		                    <th width="150">Delivery Challan</th> 
		                    <th width="60">Delivery Date</th> 
		                    <th width="80">Internal Ref.No</th>
		                    <th width="100">Trims Ref.</th>
		                    <th width="130">Cust Buyer </th>
		                    <th width="80">Section</th>
			                <th width="90">Item Group</th>
			                <th width="140">Item Description</th>

			                <th width="80">Gmts. Color </th>
			                <th width="70">Gmts. Size</th>	

		                    <th width="80">Item Color </th>
			                <th width="70">Item Size</th>				
			                <th width="60">Order UOM</th>
		                    <th width="70">WO Qty.</th>
			                <th width="80">Cum. Delv Qty</th>
			                <th width="80">Curr. Delv Qty</th>
		                    <th width="80">No of Roll/Bag</th>
			                <th width="80">Delv Balance Qty</th>
			                <th>Remarks</th>
			            </tr>
		            </thead>
		            <tbody>
					<?
					$i = 1;
					$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
					$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
					$total_quantity=0;$total_delevery_quantity=0;$curr_delevery_quantity=0;$delevery_Balance_quantity=0;
					$style_ref_no=return_library_array( "select job_no,style_ref_no from fabric_sales_order_mst", "Job_no", "Style_ref_no" );
								
					$sql = "select a.id, a.mst_id, a.booking_dtls_id, a.receive_dtls_id, a.job_dtls_id, a.production_dtls_id, a.order_id, a.order_no, b.mst_id as rcv_id, b.buyer_po_id, b.buyer_po_no,  a.buyer_style_ref, a.buyer_buyer, a.section, a.item_group as trim_group, a.order_uom, a.order_quantity,a.delevery_qty,a.claim_qty,a.remarks,a.color_id,a.size_id, a.description, a.delevery_status,a.color_name,a.size_name,a.workoder_qty,a.break_down_details_id,a.no_of_roll_bag,c.trims_del, c.delivery_date from trims_delivery_dtls a,subcon_ord_dtls b,trims_delivery_mst c  where a.mst_id in($data[1]) and  a.receive_dtls_id=b.id and a.mst_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id ASC";



					$CS_sql="SELECT c.item_group as trim_group, b.id, d.color_id, d.size_id, b.description, c.order_uom, b.color_number_id, b.gmts_sizes, e.trims_del
 						    FROM wo_booking_dtls a,
 						         wo_trim_book_con_dtls b,
 						         trims_delivery_dtls c,
 						         SUBCON_ORD_BREAKDOWN d,
 						         trims_delivery_mst e
 						   WHERE     a.id = b.wo_trim_booking_dtls_id
 						    and c.mst_id=e.id
 						         AND a.booking_no = c.order_no
 						         AND b.booking_no = c.order_no
 						         AND b.id = d.BOOK_CON_DTLS_ID
 						         AND c.order_id = d.order_id
 						         AND c.mst_id in($data[1])
 						GROUP BY c.item_group, b.id, d.color_id, d.size_id, b.description, c.order_uom, b.color_number_id, b.gmts_sizes, e.trims_del";

					$color_size_sql=sql_select($CS_sql);
					$color_size_arr=array();
					foreach ($color_size_sql as $row)
					{
						$color_size_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("description")]][$row[csf("size_id")]][$row[csf("color_id")]]['color_number_id']=$row[csf("color_number_id")];
						$color_size_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("description")]][$row[csf("size_id")]][$row[csf("color_id")]]['gmts_sizes']=$row[csf("gmts_sizes")];

					}
					/*echo "<pre>";
					print_r($color_size_arr);
					echo "<pre>";*/




					
					$delevery_qty_trims_arr=array();
					$pre_sql ="select break_down_details_id, sum(delevery_qty) as delevery_qty  from trims_delivery_dtls where status_active=1 and is_deleted=0 group by break_down_details_id";
					$pre_sql_res=sql_select($pre_sql);
					foreach ($pre_sql_res as $row)
					{
						$delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']=$row[csf("delevery_qty")];
					}
					unset($pre_sql_res);
					$data_array=sql_select($sql);
					$orderIds='';
					$buyer_po_id_array=array();
					foreach($data_array as $row)
					{
						$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['buyer_po_no']=$row[csf("buyer_po_no")];
						$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['order_no']=$row[csf("order_no")];
						$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['trims_del']=$row[csf("trims_del")];
						$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['delivery_date']=$row[csf("delivery_date")];
						$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['buyer_po_id']=$row[csf("buyer_po_id")];
						$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['buyer_buyer']=$row[csf("buyer_buyer")];
						$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['section']=$row[csf("section")];
						$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['description']=$row[csf("description")];
						$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['color_name']=$row[csf("color_name")];
						$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['size_name']=$row[csf("size_name")];

						$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['color_id']=$row[csf("color_id")];
						$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['size_id']=$row[csf("size_id")];

						$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['workoder_qty']=$row[csf("workoder_qty")];
						$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['break_down_details_id']=$row[csf("break_down_details_id")];
						$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['delevery_qty']=$row[csf("delevery_qty")];
						$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['no_of_roll_bag']=$row[csf("no_of_roll_bag")];
						$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['remarks']=$row[csf("remarks")];
						$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['rcv_id']=$row[csf("rcv_id")];
						$orderIds.=$row[csf('buyer_po_id')].",";
						$rcv_id.=$row[csf('rcv_id')].",";
					}

					$orderIds=chop($orderIds,','); $rcv_id=chop($rcv_id,','); 
					$orderIds=implode(",",array_unique(explode(",",$orderIds)));
					$rcv_id=implode(",",array_unique(explode(",",$rcv_id)));
					//echo $orderIds; die;
					
					//echo "<pre>";
					//print_r($buyer_po_id_array); die;
					
					$piArray=array();
					$sql="select a.id, a.po_number,a.grouping,b.job_no,b.internal_ref from wo_po_break_down a,wo_order_entry_internal_ref b where a.job_no_mst=b.job_no and  a.id in ($orderIds)";
					$po_data=sql_select($sql);
					foreach($po_data as $row)
					{
						$piArray[$row[csf('id')]]['grouping']=$row[csf('internal_ref')];
					}

					$trimsRefArray=array();
					$rcv_sql="select a.id, a.trims_ref, a.delivery_point from subcon_ord_mst a where a.status_active=1 and a.is_deleted=0 and a.id in ($rcv_id)";
					$rcv_sql_data=sql_select($rcv_sql);
					foreach($rcv_sql_data as $rows)
					{
						$trimsRefArray[$rows[csf('id')]]['trims_ref']=$rows[csf('trims_ref')];
						$trimsRefArray['delivery_point']=$rows[csf('delivery_point')];
					}
					?>

					<script>
						var serial = "<?php echo $cid; ?>";
						var deliveryPoint = ": <?php echo $trimsRefArray['delivery_point']; ?>";
						document.getElementById('delivery_point_td_' + serial).innerHTML = deliveryPoint;
					</script>

					<?php
					//echo "<pre>";
					//print_r($trimsRefArray);

					foreach($all_data_arr as $trimGroup => $trimGroup_arr)
					{
						$total_quantity=$cumDelvQty=$total_delevery_quantity=$delevery_Balance_quantity='';
						foreach($trimGroup_arr as $trimUOM => $trimUOM_arr)
						{
							foreach($trimUOM_arr as $trim_del => $trim_del_arr)
							{
								foreach($trim_del_arr as $id => $row)
								{
									?>
					                <tr>
					                <td><?php echo $i; ?></td>
					                <td><p><?php echo $row['buyer_po_no']; ?></p></td>
					                <td><p><?php echo $row['order_no']; ?></p></td>
					                <td><p><?php echo $trim_del; ?></p></td>
					                <td><p><?php echo change_date_format($row['delivery_date']); ?></p></td>
					                <td><p><?php echo $piArray[$row['buyer_po_id']]['grouping']; ?></p></td>
					                <td><p><?php if($data[2]==2) $trims_ref= $trimsRefArray[$row['rcv_id']]['trims_ref']; else $trims_ref=''; 
					                		echo $trims_ref;?></p></td>
					                <td><p><?php if($data[2]==1)
									{  echo $buyer_arr[$row['buyer_buyer']]; } else { echo $row['buyer_buyer'];  } ?></p></td> 
					                <td><?php echo $trims_section[$row['section']]; ?></td>
					                <td><p><?php echo $item_group_arr[$trimGroup]; ?></p></td>
					                <td><p><?php echo $row['description']; ?></p></td>	

					                <td><p><?php echo $color_library[$color_size_arr[$trimGroup][$trimUOM][$trim_del][$row["description"]][$row["size_id"]][$row["color_id"]]['color_number_id']];  ?></p> </td>
					                <td><p><?php echo $size_library[$color_size_arr[$trimGroup][$trimUOM][$trim_del][$row["description"]][$row["size_id"]][$row["color_id"]]['gmts_sizes']]; ?></p></td>

					                <td><p><?php echo $row['color_name']; ?></p> </td>
					                <td><p><?php echo $row['size_name']; ?></p></td>				
					                <td><?php echo $unit_of_measurement[$trimUOM]; $unique_uom[$trimUOM]=$trimUOM; ?></td>
					                <td align="right"><?php echo number_format($row['workoder_qty'],4); $total_quantity += $row['workoder_qty']; ?></td>
					                <td align="right"><?php  
									$cumDelvQty=$delevery_qty_trims_arr[$row["break_down_details_id"]]['delevery_qty']-$row['delevery_qty'];  
									$total_delevery_quantity += $delevery_qty_trims_arr[$row["break_down_details_id"]]['delevery_qty']-$row['delevery_qty'];
									echo number_format($cumDelvQty,4);
									 ?></td>
					                <td align="right"><?php echo number_format($row['delevery_qty'],4);  $curr_delevery_quantity += $row['delevery_qty'];  ?></td>
					                 <td align="right"><?php echo $row['no_of_roll_bag'];?></td>
					                <td align="right"><?php echo number_format($row['workoder_qty']-($row['delevery_qty']+$cumDelvQty),4); $delevery_Balance_quantity += $row['workoder_qty']-($row['delevery_qty']+$cumDelvQty);  ?></td>
					                <td><?php echo $row['remarks']; ?></td>
					                </tr>
									<?
								}
							}
						}
						$i++;
						?>
			            <tr> 
							<td colspan="16" align="right"><strong>UOM Wise Total:</strong></td>
							<td align="right"><strong><? echo number_format($total_quantity,4); ?></strong></td>
							<td align="right"><strong><? echo number_format($total_delevery_quantity,4); ?></strong></td>
							<td align="right"><strong><? echo number_format($curr_delevery_quantity,4); ?></strong></td>
			                <td><strong>&nbsp;&nbsp;</strong></td>
							<td align="right"><strong><? echo number_format($delevery_Balance_quantity,4); ?></strong></td>
			                <td><strong>&nbsp;&nbsp;</strong></td>
						</tr>
		            <?
		            } 
		         	?>
		        </tbody> 
		    </table>
			<?
				$user_lib_name=return_library_array("select id,user_full_name from user_passwd where id=$inserted_by", "id", "user_full_name");
				echo signature_table(174, $data[0], "1500px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
		    ?>	
		    </div>
		   	<?
		}
	 exit();
	}
?>