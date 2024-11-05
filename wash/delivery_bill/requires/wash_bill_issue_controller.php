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
	
	 
	//$variable_status=return_field_value("variable_dtls","variable_setting_wash","company_name='$company' and variable_list =3 and is_deleted = 0 and status_active = 1");
	
    $sql =  sql_select("select variable_dtls,id from variable_setting_wash where company_name = $data and variable_list =3 and is_deleted = 0 and status_active = 1");
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

if ($action == "check_conversion_rate") {
	$data = explode("**", $data);
	if ($db_type == 0) {
		$conversion_date = change_date_format($data[1], "Y-m-d", "-", 1);
	} else {
		$conversion_date = change_date_format($data[1], "d-M-y", "-", 1);
	}
	$exchange_rate = set_conversion_rate($data[0], $conversion_date, $data[2]);
	//$exchange_rate = set_conversion_rate($data[0], $conversion_date);
	echo $exchange_rate;
	exit();
}
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/wash_bill_issue_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_floor', 'floor_td');" );
	exit();	 
}


if ($action=="load_drop_down_floor")
{
	$data=explode("_",$data);

	//echo $data[0]."__".$data[1]; die;

	echo create_drop_down( "cbo_floor_name", 150, "select a.id,a.floor_name from lib_prod_floor a, lib_location b where a.location_id=b.id and a.company_id='$data[1]' and a.location_id='$data[0]' and a.production_process in(7,21) and a.is_deleted=0  and a.status_active=1  order by a.floor_name",'id,floor_name', 1, '--- Select Floor ---', 0, ""  );
	
	exit();
}

if ($action=="load_drop_down_party_location")
{
	echo create_drop_down( "cbo_party_location", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Party Location--", $selected, "" );
	exit();	 
}

if ($action == "company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."'  and module_id=20 and report_id=106 and is_deleted=0 and status_active=1");

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
}

if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);

	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";
	//$company_cond
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name","id,company_name", 1, "-- Select Company --", $data[2], "$load_function");
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
		echo create_drop_down( "cbo_party_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "");
		exit();
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$data[2], "" );
		exit();
	}
}

if ($action=="job_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	$data=explode("_",$data);
	?>
	<script>
	
	
				    var selected_id = new Array(); var jobNoArr = new Array;
					function toggle( x, origColor ) 
					{
						var newColor = 'yellow';
						if ( x.style ) {
						x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
						}
					}
					
					function check_all_data() 
					{
						$("#tbl_po_list tr").each(function() 
						{
							var valTP=$(this).attr("id");
							//if(valTP==1) return;
							//alert(valTP);
							$("#"+valTP).click();
						
						
						});
					}
	
	
	
		function js_set_value(str)
		{ 
		
			splitArr = str.split('***'); // ["something", "something_else"]
			var job_no=splitArr[0];
			var delevery_id=splitArr[7]*1;
			var variable_status=splitArr[9]*1;
			var search_type_delv=splitArr[5]*1;
			var search_by_delv=splitArr[6]*1;
			
			if(variable_status==2)
			{
				if(search_type_delv==6 && search_by_delv>0)
				{
					document.getElementById('selected_order').value=str;
				    /*alert(str);*/
				   parent.emailwindow.hide();
				}
				else
				{
					if(jobNoArr.length==0)
					{
						jobNoArr.push( job_no );
					}
					else if( jQuery.inArray( job_no, jobNoArr )==-1 &&  jobNoArr.length>0)
					{
						alert("Job No Mixed is Not Allow");
						return true;
					}
					toggle( document.getElementById( 'search' + delevery_id ), '#FFFFCC' );
					if( jQuery.inArray( $('#txt_individual_id' + delevery_id).val(), selected_id ) == -1 ) 
					{
					selected_id.push( $('#txt_individual_id' + delevery_id).val() );
					}
					else 
					{
						for( var i = 0; i < selected_id.length; i++ ) 
						{
							if( selected_id[i] == $('#txt_individual_id' + delevery_id).val() ) break;
						}
						selected_id.splice( i, 1 );
						jobNoArr.splice( i, 1 );
					}
					var id = '';
					for( var i = 0; i < selected_id.length; i++ ) 
					{
						id += selected_id[i] + ',';
					}
						id = id.substr( 0, id.length - 1 );
						$('#txt_selected_id').val( id );
					}
					
					
					
					//alert(id);
					document.getElementById('selected_order').value=str;
				
			}
			else
			{
				document.getElementById('selected_order').value=str;
			//alert(str);
				parent.emailwindow.hide();
			}
		}
		
		function fnc_load_party_order_popup(company,within_group,party)
		{
			//alert();
			load_drop_down( 'wash_bill_issue_controller', company+'_'+within_group+'_'+party, 'load_drop_down_buyer', 'buyer_td' );
			$('#cbo_party_name').attr('disabled','disabled');
		}
		
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Wash Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer Po');
			else if(val==5) $('#search_by_td').html('Buyer Style');
			else if(val==6) $('#search_by_td').html(' Delivery No');
			else if(val==7) $('#search_by_td').html('IR/IB');
		}
		
	</script>
	</head>
	<body onLoad="fnc_load_party_order_popup(<? echo $data[0];?>,<? echo $data[3];?>,<? echo $data[2];?>)">
        <div align="center" style="width:100%;" >
            <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
            
             <input type="hidden" id="txt_selected_id" name="txt_selected_id" value="" />
                <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                    	<tr>               	 
                            <th width="140">Company Name</th>
                            <th width="140">Party Name</th>
                            <th width="170" class="must_entry_caption">Date Range</th>
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
									$search_by_arr=array(1=>"Wash Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style",6=>" Delivery No ",7=>"IR/IB");
									echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",4,'search_by(this.value)',0 );
								?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="checkFields();showList();" style="width:70px;" />
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
        <script>
        	var isValidated = false;
        	function checkFields() {
        		var searchString = document.getElementById('txt_search_string').value;

        		if(searchString == '') {
        			if( !form_validation('txt_date_from*txt_date_to','Date From*Date To') ) {
						return;
					}
        		}

        		isValidated = true;
        	}

        	function showList() {
        		if(!isValidated) {
        			return;
        		}

        		show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $data[3];?>+'_'+<? echo $data[4];?>, 'create_job_search_list_view', 'search_div', 'wash_bill_issue_controller', 'setFilterGrid(\'tbl_po_list\',-1)');
        		isValidated = false;
        	}
        </script>
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
	//$variable_status =$data[8];
	
	$variable_status=return_field_value("variable_dtls","variable_setting_wash","company_name='$data[0]' and variable_list =3 and is_deleted = 0 and status_active = 1");
	
	//echo $variable_status; die;
	
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { $company=""; echo "PLease Select Company name"; die;}
	if ($data[1]!=0) $buyer=" and a.party_id='$data[1]'"; else { $buyer=""; echo "PLease Select Party name"; die;}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size",'id','size_name');
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";$delivery_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==5) $style_cond=" and b.buyer_style_ref = '$search_str' ";
			else if ($search_by==4) $po_cond=" and b.buyer_po_no = '$search_str' ";
			else if ($search_by==6) $delivery_cond=" and e.delivery_prefix_num = '$search_str' ";
			else if ($search_by==7) $inter_ref=" and b.grouping = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==5) $style_cond=" and b.buyer_style_ref like '%$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.buyer_po_no like '%$search_str%'"; 
			else if ($search_by==6) $delivery_cond=" and e.delivery_prefix_num like '%$search_str%' ";
			else if ($search_by==7) $inter_ref=" and b.grouping like '%$search_str%'";   
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==5) $style_cond=" and b.buyer_style_ref like '$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.buyer_po_no like '$search_str%'";  
			else if ($search_by==6) $delivery_cond=" and e.delivery_prefix_num like '$search_str%' ";
			else if ($search_by==7) $inter_ref=" and b.grouping like '$search_str%'";
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==5) $style_cond=" and b.buyer_style_ref like '%$search_str'";  
			else if ($search_by==4) $po_cond=" and b.buyer_po_no like '%$search_str'";  
			else if ($search_by==6) $delivery_cond=" and e.delivery_prefix_num like '%$search_str' ";
			else if ($search_by==7) $inter_ref=" and b.grouping like '%$search_str'";
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
	// else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',')";

	if(($job_cond!="" && $search_by==3) || ($style_cond!="" && $search_by==4)|| ($po_cond!="" && $search_by==5) || ($inter_ref!="" && $search_by==7))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond $inter_ref", "id");
		// echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	}

	if($db_type==2 && $po_ids!="") $po_ids = $po_ids->load();

	if ($po_ids!="") {
		$po_ids=explode(",",$po_ids);
		$po_idsCond=""; $poIdsCond="";
		//echo count($po_ids); die;
		if($db_type==2 && count($po_ids)>=999)
		{
			$chunk_arr=array_chunk($po_ids,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",",$val);
				if($po_idsCond=="")
				{
					$po_idsCond.=" and ( b.buyer_po_id in ( $ids) ";
					$poIdsCond.=" and ( b.id in ( $ids) ";
				}
				else
				{
					$po_idsCond.=" or b.buyer_po_id in ( $ids) ";
					$poIdsCond.=" or b.id in ( $ids) ";
				}
			}
			$po_idsCond.=")";
			$poIdsCond.=")";
		}
		else
		{
			$ids=implode(",",$po_ids);
			$po_idsCond.=" and b.buyer_po_id in ($ids) ";
			$poIdsCond.=" and b.id in ($ids) ";
		}
	}
	else 
	{
		$po_idsCond="";
	}
	
	// if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	
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
		$color_id_str="group_concat(b.gmts_color_id)";
		$buyer_po_id_cond="year(b.buyer_po_id)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str="listagg(b.gmts_color_id,',') within group (order by b.gmts_color_id)";
		$buyer_po_id_cond="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
	}
	
	//echo  $variable_status; die;
	
	if($variable_status==2)
	{
	 $sql= "select a.id, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, e.delivery_date, $color_id_str as color_id, $buyer_po_id_cond as buyer_po_id ,b.buyer_po_no,b.buyer_style_ref ,b.party_buyer_name,e.delivery_no,e.delivery_prefix_num,e.id as delivery_id 
	 from subcon_ord_mst a, subcon_ord_dtls b, subcon_delivery_dtls d ,subcon_delivery_mst e 
	 where a.entry_form=295  and a.subcon_job=b.job_no_mst and a.id=b.mst_id and b.id=d.order_id  and e.id=d.mst_id and a.subcon_job=e.job_no and e.entry_form=303  and a.status_active=1 and b.status_active=1 and d.status_active=1 and d.bill_status!=1  $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $job_cond $style_cond $po_cond  $delivery_cond
	 group by a.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, e.delivery_date,b.buyer_po_no,b.buyer_style_ref,b.party_buyer_name,e.delivery_no,e.delivery_prefix_num,e.id
	 order by a.id DESC";
	 //and d.bill_status!=1
	}
	else
	{
		
		       if($search_by==6)
				{
					
					 $sql= "select a.id, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, e.delivery_date, $color_id_str as color_id, $buyer_po_id_cond as buyer_po_id ,b.buyer_po_no,b.buyer_style_ref ,b.party_buyer_name,e.delivery_prefix_num,e.delivery_no
	 from subcon_ord_mst a, subcon_ord_dtls b, subcon_delivery_dtls d ,subcon_delivery_mst e 
	 where a.entry_form=295 and d.bill_status!=1 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and b.id=d.order_id  and e.id=d.mst_id and a.subcon_job=e.job_no and e.entry_form=303  and a.status_active=1 and b.status_active=1 and d.status_active=1 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $job_cond $style_cond $po_cond  $delivery_cond
	 group by a.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, e.delivery_date,b.buyer_po_no,b.buyer_style_ref,b.party_buyer_name,e.delivery_prefix_num,e.delivery_no
	 order by a.id DESC";
				}
				else
				{
					  $sql= "select a.id, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, e.delivery_date, $color_id_str as color_id, $buyer_po_id_cond as buyer_po_id ,b.buyer_po_no,b.buyer_style_ref ,b.party_buyer_name 
	 from subcon_ord_mst a, subcon_ord_dtls b, subcon_delivery_dtls d ,subcon_delivery_mst e 
	 where a.entry_form=295 and d.bill_status!=1 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and b.id=d.order_id  and e.id=d.mst_id and a.subcon_job=e.job_no and e.entry_form=303  and a.status_active=1 and b.status_active=1 and d.status_active=1 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $job_cond $style_cond $po_cond  $delivery_cond
	 group by a.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, e.delivery_date,b.buyer_po_no,b.buyer_style_ref,b.party_buyer_name 
	 order by a.id DESC";
				}
		
		
	}

 //echo $sql;die;

	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="885" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Job No</th>
            <th width="60">Year</th>
            <th width="120">W/O No</th>
            <? if($variable_status==2 || $search_by==6) {?>
            <th width="120">Delivery No</th>
            <? } ?>
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
				$buyer_po=""; $buyer_style=""; $buyer_name='';
				$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
				foreach($buyer_po_id as $po_id)
				{
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
					if($buyer_name=="") $buyer_name=$buyer_arr[$buyer_po_arr[$po_id]['buyer']]; else $buyer_name.=','.$buyer_arr[$buyer_po_arr[$po_id]['buyer']];
				}
				
				if($within_group==1){
				
				$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				$buyer_name=implode(",",array_unique(explode(",",$buyer_name)));
				
				}
				elseif($within_group==2)
				{
					
					
					$buyer_po=$row[csf('buyer_po_no')];
					$buyer_style=$row[csf('buyer_style_ref')];
					$buyer_name=$row[csf('party_buyer_name')];
				}
				if($search_by==6)
				{
					
					$search_str=$row[csf('delivery_prefix_num')];
				}
				else
				{
					   $search_str=0;
				}
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf('subcon_job')].'***'.$row[csf('order_no')].'***'.$buyer_style.'***'.$buyer_name.'***'.$row[csf('company_id')].'***'.$search_by.'***'.$search_str.'***'.$row[csf('delivery_id')].'***'.$row[csf('delivery_prefix_num')].'***'.$variable_status; ?>')" style="cursor:pointer" id="search<? echo $row[csf('delivery_id')];?>" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $row[csf('order_no')]; ?></td>
                    <? if($variable_status==2 || $search_by==6) {?>
             			<td width="120" style="word-break:break-all"><? echo $row[csf('delivery_no')]; ?></td>
            		<? } ?>
                    <td width="100" style="word-break:break-all"><? echo $buyer_po; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_style; ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>	
                    <td style="word-break:break-all"><? echo $color_name; ?>
                     <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $row[csf('delivery_id')]; ?>" value="<? echo $row[csf('delivery_id')]; ?>"/>
                    
                    </td>
                </tr>
				<? 
                $i++; 
            } 
            ?>
        </tbody>
    </table>
    <? if($variable_status==2)
	{ 
	
	   if($search_by==6 && $search_str==0 || $search_by!=6)
		{
	
	?>
    <br>
<table width="450" cellspacing="0" cellpadding="0" style="border:none" align="center">
    <tr>
        <td align="center" height="30" valign="bottom">
        <div style="width:100%">
        <div style="width:50%; float:left; display:none" align="left">
        <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
        </div>
        <div style="width:50%; float:left" align="left">
        <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
        </div>
        </div>
        </td>
    </tr>
</table>

<? }
	} 
	?>
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
	$exchange_rate=$exdata[3];
	$search_type=$exdata[4];
	$delivery_id=$exdata[5];
	$company=$exdata[6];
	$job_wise_delivery_id=$exdata[7];
	
	//echo "10**".$update_id;
		
			
	if ($update_id!=0) $update_cond=" and b.id!='$update_id'"; 
	$variable_status=return_field_value("variable_dtls","variable_setting_wash","company_name='$company' and variable_list =3 and is_deleted = 0 and status_active = 1");
	
	 $sql_size_wise_del="SELECT a.id, a.mst_id, a.delivery_id,a.order_id, a.delivery_qty,a.color_size_id  from subcon_inbound_bill_dtls a, subcon_inbound_bill_mst b where b.id=a.mst_id  $update_cond  and a.process_id=304 and b.process_id=304 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_size_wise_del_res =sql_select($sql_size_wise_del);
	$updtls_size_wise_arr=array(); 
	
	foreach ($sql_size_wise_del_res as $row)
	{
		$updtls_size_wise_arr[$row[csf("order_id")]]['prev_delivery_qty']+=$row[csf("delivery_qty")];
		$updtls_size_wise_arr[$row[csf("delivery_id")]]['prev_bil_qty']+=$row[csf("delivery_qty")];
	}
	unset($sql_size_wise_del_res);
	
	
			$prev_return_delivery_qty_sql="select id,entry_form, delivery_dtls_id,delivery_qty from subcon_delivery_dtls  where entry_form=360  and  status_active=1 and is_deleted=0";
		   	$prev_return_delivery_result =sql_select($prev_return_delivery_qty_sql);
		   	$delv_return_qty_arr=array();
			foreach ($prev_return_delivery_result as $row)
			{
				$delv_return_qty_arr[$row[csf("delivery_dtls_id")]]['delivery_qty']+=$row[csf("delivery_qty")];
			}
			unset($prev_return_delivery_result);	
	
	
	//die;
	//echo "10**".$variable_status; die;
	
	$color_arrey=return_library_array( "select id,color_name from lib_color",'id','color_name');
	$size_arrey=return_library_array( "select id,size_name from  lib_size",'id','size_name');
	$buyer_po_arr=array();
	
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	
	//$sql_del="select a.id, a.mst_id, a.order_id, a.delivery_qty, a.remarks, a.color_size_id from subcon_delivery_dtls a, subcon_delivery_mst b where b.id=a.mst_id and b.job_no='$jobno' and b.entry_form=303 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	/*$sql_bill="select id, delivery_id, order_id, delivery_qty, rate, amount, remarks, color_size_id from subcon_inbound_bill_dtls where mst_id='$update_id' and process_id=304 and status_active=1 and is_deleted=0";
	$sql_bill_res =sql_select($sql_bill);
	$updtls_data_arr=array();
	
	foreach ($sql_bill_res as $row)
	{
		$updtls_data_arr[$row[csf("delivery_id")]][$row[csf("order_id")]][$row[csf("color_size_id")]]['dtlsid']=$row[csf("id")];
		$updtls_data_arr[$row[csf("delivery_id")]][$row[csf("order_id")]][$row[csf("color_size_id")]]['qty']=$row[csf("delivery_qty")];
		$updtls_data_arr[$row[csf("delivery_id")]][$row[csf("order_id")]][$row[csf("color_size_id")]]['remarks']=$row[csf("remarks")];
		$updtls_data_arr[$row[csf("delivery_id")]][$row[csf("order_id")]][$row[csf("color_size_id")]]['rate']=$row[csf("rate")];
		$updtls_data_arr[$row[csf("delivery_id")]][$row[csf("order_id")]][$row[csf("color_size_id")]]['amount']=$row[csf("amount")];
	}
	unset($sql_bill_res);*/
	//if($update_id==0) 
	
	if($db_type==0) $process_type_cond="group_concat(c.process,'*',c.embellishment_type)";
	else if ($db_type==2) $process_type_cond="listagg(c.process||'*'||c.embellishment_type,',') within group (order by c.process||'*'||c.embellishment_type)";
	
	if(($variable_status)*1==2)
	{
		if($update_id>0)
		{
			 $sql_job="select a.id, a.company_id,a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id,b.buyer_style_ref,b.buyer_po_no, b.gmts_item_id, $process_type_cond as process_type, b.order_uom, b.gmts_color_id as color_id, b.order_quantity as qnty, d.delivery_prefix_num,d.id as delivery_mst_id, d.delivery_date, e.id as delivery_id, e.delivery_qty, f.id as upid, f.rate, f.amount,f.domestic_amount, f.remarks,a.within_group,b.rate as order_rate,b.order_uom,f.delivery_qty as bil_qty
			from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e, subcon_inbound_bill_dtls f 
			where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and d.id=e.mst_id and b.id=e.order_id and e.id=f.delivery_id and f.process_id=304 and f.mst_id='$update_id' and d.entry_form=303 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.id=c.mst_id and a.subcon_job='$jobno' group by a.id,a.company_id, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id, b.buyer_po_id,b.buyer_style_ref,b.buyer_po_no, b.gmts_item_id, b.order_uom, b.gmts_color_id, b.order_quantity, d.delivery_prefix_num,d.id, d.delivery_date, e.id , e.delivery_qty, f.id, f.rate, f.amount,f.domestic_amount, f.remarks,a.within_group,b.rate,b.order_uom,f.delivery_qty order by e.id "; 
			
		}
		else
		{
			if($search_type==6 && $delivery_id!=0)
			{
			 
			  $sql_job="select a.id,a.company_id, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id,b.buyer_style_ref,b.buyer_po_no, b.gmts_item_id, $process_type_cond as process_type, b.order_uom, b.gmts_color_id as color_id, b.order_quantity as qnty, d.delivery_prefix_num,d.id as delivery_mst_id, d.delivery_date, e.id as delivery_id, e.delivery_qty, 0 as upid, 0 as rate, 0 as amount,0 as domestic_amount, null as remarks,a.within_group,b.rate as order_rate,b.order_uom
			from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e
			where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and d.id=e.mst_id and b.id=e.order_id  and d.entry_form=303 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.id=c.mst_id and a.subcon_job='$jobno' and e.bill_status!=1 and d.delivery_prefix_num='$delivery_id' group by a.id,a.company_id, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id,b.buyer_style_ref,b.buyer_po_no, b.buyer_po_id, b.gmts_item_id, b.order_uom, b.gmts_color_id, b.order_quantity, d.delivery_prefix_num,d.id, d.delivery_date, e.id, e.delivery_qty,a.within_group,b.rate,b.order_uom order by e.id";
			 
			 //and e.bill_status!=1
			}
			else
			{
			  $sql_job="select a.id,a.company_id, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id,b.buyer_style_ref,b.buyer_po_no, b.gmts_item_id, $process_type_cond as process_type, b.order_uom, b.gmts_color_id as color_id, b.order_quantity as qnty, d.delivery_prefix_num,d.id as delivery_mst_id, d.delivery_date, e.id as delivery_id, e.delivery_qty, 0 as upid, 0 as rate, 0 as amount,0 as domestic_amount, null as remarks,a.within_group,b.rate as order_rate,b.order_uom
			from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e
			where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and d.id=e.mst_id and b.id=e.order_id  and d.entry_form=303 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.id=c.mst_id and a.subcon_job='$jobno' and e.bill_status!=1 and  d.id in ($job_wise_delivery_id)
			
			group by a.id,a.company_id, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id,b.buyer_style_ref,b.buyer_po_no, b.buyer_po_id, b.gmts_item_id, b.order_uom, b.gmts_color_id, b.order_quantity, d.delivery_prefix_num,d.id, d.delivery_date, e.id, e.delivery_qty,a.within_group,b.rate,b.order_uom order by e.id";
			
			//and e.bill_status!=1
				
		}
		}
	}
	else
	{
		
		 if($search_type==6 && $delivery_id!=0)
		{
	
	   $sql_job="(select a.id, a.company_id,a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id,b.buyer_style_ref,b.buyer_po_no, b.gmts_item_id, $process_type_cond as process_type, b.order_uom, b.gmts_color_id as color_id, b.order_quantity as qnty, d.delivery_prefix_num,d.id as delivery_mst_id, d.delivery_date, e.id as delivery_id, e.delivery_qty, f.id as upid, f.rate, f.amount,f.domestic_amount, f.remarks,a.within_group,b.rate as order_rate,b.order_uom
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e, subcon_inbound_bill_dtls f 
		where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and d.id=e.mst_id and b.id=e.order_id and e.id=f.delivery_id and f.process_id=304 and f.mst_id='$update_id' and d.entry_form=303 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.id=c.mst_id and a.subcon_job='$jobno' and d.delivery_prefix_num='$delivery_id'
		group by a.id,a.company_id, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id, b.buyer_po_id,b.buyer_style_ref,b.buyer_po_no, b.gmts_item_id, b.order_uom, b.gmts_color_id, b.order_quantity, d.delivery_prefix_num,d.id, d.delivery_date, e.id , e.delivery_qty, f.id, f.rate, f.amount,f.domestic_amount, f.remarks,a.within_group,b.rate,b.order_uom
		)
		union all
		(
			select a.id,a.company_id, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id,b.buyer_style_ref,b.buyer_po_no, b.gmts_item_id, $process_type_cond as process_type, b.order_uom, b.gmts_color_id as color_id, b.order_quantity as qnty, d.delivery_prefix_num,d.id as delivery_mst_id, d.delivery_date, e.id as delivery_id, e.delivery_qty, 0 as upid, 0 as rate, 0 as amount,0 as domestic_amount, null as remarks,a.within_group,b.rate as order_rate,b.order_uom
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e
		where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and d.id=e.mst_id and b.id=e.order_id and e.bill_status!=1 and d.entry_form=303 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.id=c.mst_id and a.subcon_job='$jobno' and d.delivery_prefix_num='$delivery_id'
		
		group by a.id,a.company_id, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id,b.buyer_style_ref,b.buyer_po_no, b.buyer_po_id, b.gmts_item_id, b.order_uom, b.gmts_color_id, b.order_quantity, d.delivery_prefix_num,d.id, d.delivery_date, e.id, e.delivery_qty,a.within_group,b.rate,b.order_uom
		)
		 order by delivery_id ASC";
		 
		}
		else
		{
			  $sql_job="(select a.id, a.company_id,a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id,b.buyer_style_ref,b.buyer_po_no, b.gmts_item_id, $process_type_cond as process_type, b.order_uom, b.gmts_color_id as color_id, b.order_quantity as qnty, d.delivery_prefix_num,d.id as delivery_mst_id, d.delivery_date, e.id as delivery_id, e.delivery_qty, f.id as upid, f.rate, f.amount,f.domestic_amount, f.remarks,a.within_group,b.rate as order_rate,b.order_uom
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e, subcon_inbound_bill_dtls f 
		where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and d.id=e.mst_id and b.id=e.order_id and e.id=f.delivery_id and f.process_id=304 and f.mst_id='$update_id' and d.entry_form=303 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.id=c.mst_id and a.subcon_job='$jobno'
		group by a.id,a.company_id, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id, b.buyer_po_id,b.buyer_style_ref,b.buyer_po_no, b.gmts_item_id, b.order_uom, b.gmts_color_id, b.order_quantity, d.delivery_prefix_num,d.id, d.delivery_date, e.id , e.delivery_qty, f.id, f.rate, f.amount,f.domestic_amount, f.remarks,a.within_group,b.rate,b.order_uom
		)
		union all
		(
			select a.id,a.company_id, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id,b.buyer_style_ref,b.buyer_po_no, b.gmts_item_id, $process_type_cond as process_type, b.order_uom, b.gmts_color_id as color_id, b.order_quantity as qnty, d.delivery_prefix_num,d.id as delivery_mst_id, d.delivery_date, e.id as delivery_id, e.delivery_qty, 0 as upid, 0 as rate, 0 as amount,0 as domestic_amount, null as remarks,a.within_group,b.rate as order_rate,b.order_uom
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e
		where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and d.id=e.mst_id and b.id=e.order_id and e.bill_status!=1 and d.entry_form=303 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.id=c.mst_id and a.subcon_job='$jobno'
		
		group by a.id,a.company_id, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id,b.buyer_style_ref,b.buyer_po_no, b.buyer_po_id, b.gmts_item_id, b.order_uom, b.gmts_color_id, b.order_quantity, d.delivery_prefix_num,d.id, d.delivery_date, e.id, e.delivery_qty,a.within_group,b.rate,b.order_uom
		)
		 order by delivery_id ASC";
			
	}
		
	}
	
	
	//echo $sql_job;
	
	$sql_result =sql_select($sql_job);
	$k=0;
	$num_rowss=count($sql_result);
	 
	if(($variable_status)*1==2)
	{
		foreach ($sql_result as $row)
		{
			$k++;
			if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
			
			
			
			$quantity=0; $dtlsup_id=""; $balanceQty=0; $preDelv_qty=0; $orderQty=0; $remarks="";
			//$qc_qty=$qcdtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
			//$preDelv_qty=$pre_qty_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
			
			/*if($row[csf('order_uom')]==1){ $colorSizeqnty=$row[csf("qnty")];}
		    if($row[csf('order_uom')]==2){  $colorSizeqnty=$row[csf("qnty")]*12; ;}*/
			
			
			if($row[csf('order_uom')]==1){ $colorSizeqnty=number_format($row[csf("qnty")],0,'.','');}//$row[csf("qnty")];}
		    if($row[csf('order_uom')]==2){  $colorSizeqnty=number_format($row[csf("qnty")]*12,0,'.','');}//$row[csf("qnty")]*12; ;}
			
			
			
			//$updtls_size_wise_arr[$row[csf("order_id")]]['prev_delivery_qty']+=$row[csf("delivery_qty")];
		//$updtls_size_wise_arr[$row[csf("delivery_id")]]['prev_bil_qty']+=$row[csf("delivery_qty")];
			
			
			
			
			$size_wise_delivery_qty=$updtls_size_wise_arr[$row[csf("po_id")]]['prev_delivery_qty'];
			$delivery_id_wise_billqty=$updtls_size_wise_arr[$row[csf("delivery_id")]]['prev_bil_qty'];
			$delv_return_qty=$delv_return_qty_arr[$row[csf("delivery_id")]]['delivery_qty'];
			
			// echo "10**".$delivery_id_wise_billqty."======";
				//$size_wise_delivery_qty=$updtls_size_wise_arr[$row[csf("delvID")]][$row[csf("color_size_id")]]['qty'];
				
				if($size_wise_delivery_qty!="")
				{
					$delivery_qty=$size_wise_delivery_qty;
				}
				else
				{
					$delivery_qty=0;
				}
				
			
				if($delv_return_qty!="")
				{
					$delv_return_qty=$delv_return_qty;
				}
				else
				{
					$delv_return_qty=0;
				}
				
			if($row[csf("upid")]==0) $row[csf("upid")]='';
			if($row[csf("rate")]==0) $row[csf("rate")]='';
			if($row[csf("amount")]==0) $row[csf("amount")]='';
			
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
			if($row[csf("upid")]!="")
			{
				$Rate=$row[csf("rate")];
				$amount=$row[csf("amount")];
				$domestic_amount=$row[csf("domestic_amount")];
			}
			else
			{
				//var amount=qty*val;
				//var domisticamount=amount*exchange_rate;
				
				if($row[csf("order_uom")]==2) //
				{
					$Rateformat=$row[csf("order_rate")]/12;
					$Rate=number_format($Rateformat,6,'.','');//number_format($Rateformat,4);
				}
				if($row[csf("order_uom")]==1)
				{
					$Rate=$row[csf("order_rate")];
				}
				$actual_delivery_qty=$row[csf("delivery_qty")]-$delv_return_qty;
				$amount=$actual_delivery_qty*$Rate;
				//$amount=$row[csf("delivery_qty")]*$row[csf("order_rate")];
				$domestic_amount=$amount*$exchange_rate;
			}
			
			
			if($update_id>0)
			{ 
			
			
			
			 	//$bil_qty=$row[csf("bil_qty")];
				//$balance_bil_qty=$row[csf("delivery_qty")]-$delivery_id_wise_billqty;
				
				$bil_qty=$row[csf("bil_qty")];
				$actual_delivery_qty=$row[csf("delivery_qty")]-$delv_return_qty;
				$balance_bil_qty=$actual_delivery_qty-$delivery_id_wise_billqty;
			}
			else
			{
				//$bil_qty=$colorSizeqnty;//$row[csf("delivery_qty")];
				
				/*$bil_qty=$row[csf("delivery_qty")]-$delivery_id_wise_billqty;
				$balance_bil_qty=$row[csf("delivery_qty")]-$delivery_id_wise_billqty;*/
				
				$actual_delivery_qty=$row[csf("delivery_qty")]-$delv_return_qty;
				$bil_qty=$actual_delivery_qty-$delivery_id_wise_billqty;
				$balance_bil_qty=$actual_delivery_qty-$delivery_id_wise_billqty;
			}
			
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td align="center"><? echo $k; ?>
					<input name="txtbuyerPoId_<? echo $k; ?>" id="txtbuyerPoId_<? echo $k; ?>" type="hidden" class="text_boxes" style="width:70px" value="<? echo $row[csf("buyer_po_id")]; ?>" />
					<input name="txtdeliveryid_<? echo $k; ?>" id="txtdeliveryid_<? echo $k; ?>" type="hidden" class="text_boxes" style="width:70px" value="<? echo $row[csf("delivery_id")]; ?>" />
				</td>
				 <td align="center" id="sysnotd_<? echo $k; ?>"><a href='##' style='color:#000' onClick="print_report('<? echo $row[csf("company_id")].'*'.$row[csf("delivery_mst_id")].'*'.$row[csf("subcon_job")].'*'."Wash Delivery";?>','embl_delivery_entry_print', 'requires/wash_delivery_controller')"><font color="blue"><strong><? echo $row[csf("delivery_prefix_num")]; ?></strong></font></a></td>
				<td id="deliverydatetd_<? echo $k; ?>"><? echo change_date_format($row[csf("delivery_date")]); ?></td>  
				<td style="word-break:break-all"><? echo $buyer_po;//$row[csf("buyer_po_no")];//$buyer_po_arr[$row[csf("buyer_po_id")]]['po']; ?></td>
				 <td style="word-break:break-all"><? echo $buyer_style;//$row[csf("buyer_po_no")];//$buyer_po_arr[$row[csf("buyer_po_id")]]['po']; ?></td>
				<td style="word-break:break-all"><? echo $garments_item[$row[csf("gmts_item_id")]]; ?></td>
				<td style="word-break:break-all"  id="txtTotalBillQuantity_<? echo $k; ?>" title="<? echo $delivery_id_wise_billqty;?>"><? echo $process_name; ?></td>
				<td style="word-break:break-all"  id="txtdeleveryidwiseQuantity_<? echo $k; ?>" title="<? echo $actual_delivery_qty;//$row[csf("delivery_qty")];?>" ><? echo $sub_process_name; ?></td>
				<td style="word-break:break-all"  id="txtdeliveryqty_<? echo $k; ?>" title="<? echo $delivery_qty;?>"><? echo $color_arrey[$row[csf("color_id")]]; ?></td>
				<td style="word-break:break-all" align="right" id="txtOrderQuantity_<? echo $k; ?>" title="<? echo $colorSizeqnty;?>"><? echo $colorSizeqnty; ?></td>
                
				<td align="center" id="hiddenbilqty_<? echo $k; ?>" title="<? echo $colorSizeqnty;//$row[csf("delivery_qty")];// $palace_bil_qty;//?>" >
                <input type="text" name="txtbillqty_<? echo $k; ?>" id="txtbillqty_<? echo $k; ?>" class="text_boxes_numeric" value="<? echo $bil_qty;//$row[csf("delivery_qty")]; ?>"  placeholder="<? echo $balance_bil_qty;// $colorSizeqnty;//$row[csf("delivery_qty")];//$palace_bil_qty; ?>"  style="width:50px;"  onBlur="calculate_amount(<? echo $k; ?>)"  /></td>
				<td align="center"><input type="text" name="txtbillrate_<? echo $k; ?>" id="txtbillrate_<? echo $k; ?>" class="text_boxes_numeric" style="width:50px;" value="<? echo $Rate;//$row[csf("rate")]; ?>" onBlur="fnc_amount_calculation(this.value,<? echo $k; ?>); fnc_total_calculate();" /></td>
				<td align="center"><input type="text" name="txtbillamount_<? echo $k; ?>" id="txtbillamount_<? echo $k; ?>" class="text_boxes_numeric" style="width:50px;" value="<? echo $amount;//$row[csf("amount")]; ?>" readonly /></td>
				 <td align="center"><input type="text" name="txtdomisticamount_<? echo $k; ?>" id="txtdomisticamount_<? echo $k; ?>" class="text_boxes_numeric" style="width:90px;" value="<? echo $domestic_amount;//$row[csf("domestic_amount")]; ?>" readonly /></td>
				<td><input type="text" name="txtRemarks_<? echo $k; ?>" id="txtRemarks_<? echo $k; ?>" class="text_boxes" style="width:80px;" value="<? echo $row[csf("remarks")]; ?>" />
					<input type="hidden" name="txtDtlsUpdateId_<? echo $k; ?>" id="txtDtlsUpdateId_<? echo $k; ?>" style="width:80px" class="text_boxes" value="<? echo $row[csf("upid")]; ?>" />
					<input type="hidden" name="txtColorSizeid_<? echo $k; ?>" id="txtColorSizeid_<? echo $k; ?>" style="width:80px" class="text_boxes" value="<? //echo $row[csf("breakdown_id")]; ?>" />
					<input type="hidden" name="txtpoid_<? echo $k; ?>" id="txtpoid_<? echo $k; ?>" style="width:80px" class="text_boxes" value="<? echo $row[csf("po_id")]; ?>" />
				</td> 
			</tr>
		<?	
		}
	}
	else
	{
		foreach ($sql_result as $row)
		{
			$k++;
			if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
			$quantity=0; $dtlsup_id=""; $balanceQty=0; $preDelv_qty=0; $orderQty=0; $remarks="";
			//$qc_qty=$qcdtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
			//$preDelv_qty=$pre_qty_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
			if($row[csf('order_uom')]==1){ $colorSizeqnty=$row[csf("qnty")];}
		    if($row[csf('order_uom')]==2){  $colorSizeqnty=$row[csf("qnty")]*12; ;}
			
			if($row[csf("upid")]==0) $row[csf("upid")]='';
			if($row[csf("rate")]==0) $row[csf("rate")]='';
			if($row[csf("amount")]==0) $row[csf("amount")]='';
			
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
			if($row[csf("upid")]!="")
			{
				$Rate=$row[csf("rate")];
				$amount=$row[csf("amount")];
				$domestic_amount=$row[csf("domestic_amount")];
			}
			else
			{
				//var amount=qty*val;
				//var domisticamount=amount*exchange_rate;
				
				if($row[csf("order_uom")]==2)
				{
					$Rateformat=$row[csf("order_rate")]/12;
					$Rate=number_format($Rateformat,6,'.','');//number_format($Rateformat,4);
				}
				if($row[csf("order_uom")]==1)
				{
					$Rate=$row[csf("order_rate")];
				}
				$delv_return_qty=$delv_return_qty_arr[$row[csf("delivery_id")]]['delivery_qty'];
				$actual_delivery_qty=$row[csf("delivery_qty")]-$delv_return_qty;
				//$amount=$row[csf("delivery_qty")]*$Rate;
				$amount=$actual_delivery_qty*$Rate;
				//$amount=$row[csf("delivery_qty")]*$row[csf("order_rate")];
				$domestic_amount=$amount*$exchange_rate;
			}
			
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td align="center"><? echo $k; ?>
					<input name="txtbuyerPoId_<? echo $k; ?>" id="txtbuyerPoId_<? echo $k; ?>" type="hidden" class="text_boxes" style="width:70px" value="<? echo $row[csf("buyer_po_id")]; ?>" />
					<input name="txtdeliveryid_<? echo $k; ?>" id="txtdeliveryid_<? echo $k; ?>" type="hidden" class="text_boxes" style="width:70px" value="<? echo $row[csf("delivery_id")]; ?>" />
				</td>
				 <td align="center" id="sysnotd_<? echo $k; ?>"><a href='##' style='color:#000' onClick="print_report('<? echo $row[csf("company_id")].'*'.$row[csf("delivery_mst_id")].'*'.$row[csf("subcon_job")].'*'."Wash Delivery";?>','embl_delivery_entry_print', 'requires/wash_delivery_controller')"><font color="blue"><strong><? echo $row[csf("delivery_prefix_num")]; ?></strong></font></a></td>
				<td id="deliverydatetd_<? echo $k; ?>"><? echo change_date_format($row[csf("delivery_date")]); ?></td>  
				<td style="word-break:break-all"><? echo $buyer_po;//$row[csf("buyer_po_no")];//$buyer_po_arr[$row[csf("buyer_po_id")]]['po']; ?></td>
				 <td style="word-break:break-all"><? echo $buyer_style;//$row[csf("buyer_po_no")];//$buyer_po_arr[$row[csf("buyer_po_id")]]['po']; ?></td>
				<td style="word-break:break-all"><? echo $garments_item[$row[csf("gmts_item_id")]]; ?></td>
				<td style="word-break:break-all"><? echo $process_name; ?></td>
				<td style="word-break:break-all"><? echo $sub_process_name; ?></td>
				<td style="word-break:break-all"  id="txtdeliveryqty_<? echo $k; ?>" title="<? echo $delivery_qty;?>"><? echo $color_arrey[$row[csf("color_id")]]; ?></td>
				<td style="word-break:break-all" align="right" id="txtOrderQuantity_<? echo $k; ?>" title="<? echo $colorSizeqnty;?>"><? echo $colorSizeqnty; ?></td>
				<td align="center" id="hiddenbilqty_<? echo $k; ?>" title="<? echo $actual_delivery_qty;//$row[csf("delivery_qty")];// $palace_bil_qty;//?>" >
                <input type="text" name="txtbillqty_<? echo $k; ?>" id="txtbillqty_<? echo $k; ?>" class="text_boxes_numeric" value="<? echo $actual_delivery_qty;//$row[csf("delivery_qty")]; ?>" style="width:50px;" disabled />
                </td>
				<td align="center"><input type="text" name="txtbillrate_<? echo $k; ?>" id="txtbillrate_<? echo $k; ?>" class="text_boxes_numeric" style="width:50px;" value="<? echo $Rate;//$row[csf("rate")]; ?>" onBlur="fnc_amount_calculation(this.value,<? echo $k; ?>); fnc_total_calculate();" /></td>
				<td align="center"><input type="text" name="txtbillamount_<? echo $k; ?>" id="txtbillamount_<? echo $k; ?>" class="text_boxes_numeric" style="width:50px;" value="<? echo $amount;//$row[csf("amount")]; ?>" readonly /></td>
				 <td align="center"><input type="text" name="txtdomisticamount_<? echo $k; ?>" id="txtdomisticamount_<? echo $k; ?>" class="text_boxes_numeric" style="width:90px;" value="<? echo $domestic_amount;//$row[csf("domestic_amount")]; ?>" readonly /></td>
				<td><input type="text" name="txtRemarks_<? echo $k; ?>" id="txtRemarks_<? echo $k; ?>" class="text_boxes" style="width:80px;" value="<? echo $row[csf("remarks")]; ?>" />
					<input type="hidden" name="txtDtlsUpdateId_<? echo $k; ?>" id="txtDtlsUpdateId_<? echo $k; ?>" style="width:80px" class="text_boxes" value="<? echo $row[csf("upid")]; ?>" />
					<input type="hidden" name="txtColorSizeid_<? echo $k; ?>" id="txtColorSizeid_<? echo $k; ?>" style="width:80px" class="text_boxes" value="<? //echo $row[csf("breakdown_id")]; ?>" />
					<input type="hidden" name="txtpoid_<? echo $k; ?>" id="txtpoid_<? echo $k; ?>" style="width:80px" class="text_boxes" value="<? echo $row[csf("po_id")]; ?>" />
				</td> 
			</tr>
		<?	
		}
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$bill_process_id=304;
	
	//$variable_status=return_field_value("variable_dtls","variable_setting_wash","company_name=$cbo_company_name and variable_list =3 and is_deleted = 0 and status_active = 1");
	
	//echo "10**".$variable_status; die;
	 // echo "10**";
	   $variable_status=return_field_value("variable_dtls","variable_setting_wash","company_name=$cbo_company_name and variable_list=3 and is_deleted = 0 and status_active = 1");
	
	   	if (str_replace("'",'',$txt_update_id)!=0) $update_cond="and b.id!=$txt_update_id"; 
		 $sql_size_wise_del="SELECT a.id, a.mst_id, a.delivery_id,a.order_id, a.delivery_qty,a.color_size_id  from subcon_inbound_bill_dtls a, subcon_inbound_bill_mst b where b.id=a.mst_id  $update_cond  and a.process_id=304 and b.process_id=304 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";   
		$sql_size_wise_del_res =sql_select($sql_size_wise_del);
		$updtls_size_wise_arr=array(); 
		
		foreach ($sql_size_wise_del_res as $row)
		{
			$updtls_size_wise_arr[$row[csf("order_id")]]['qty']+=$row[csf("delivery_qty")];
			$updtls_size_wise_arr[$row[csf("delivery_id")]]['bil_qty']+=$row[csf("delivery_qty")];
		}
		unset($sql_size_wise_del_res);
	//echo "10**";
	
	//echo $variable_status; die;
	
	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
 		//table lock here 
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die;}
 		//$delivery_ids = implode(', ', $order_id_arr);
	/*	if($variable_status==2)
		{
		
		    $duplicat_order_arr=array();
		     for($k=1; $k<=$total_row; $k++)
			{
				//$txtColorSizeid="txtColorSizeid_".$k;
				////$txtbillqty="txtbillqty_".$k;
				//$txtOrderQuantity="txtOrderQuantity_".$k;
				//$cumBillQty="txtdeliveryqty_".$k;
				
				
				$txtbuyerPoId="txtbuyerPoId_".$k;
				$txtdeliveryid="txtdeliveryid_".$k;
				$sysnotd="sysnotd_".$k;
				$deliverydatetd="deliverydatetd_".$k;
				$txtbillqty="txtbillqty_".$k;
				$txtbillrate="txtbillrate_".$k;
				$txtbillamount="txtbillamount_".$k;
				$txtdomisticamount="txtdomisticamount_".$k;
		
				$txtpoid="txtpoid_".$k;
				$txtColorSizeid="txtColorSizeid_".$k;
				$txtDtlsUpdateId="txtDtlsUpdateId_".$k;
				
				
				echo  "==".$preBilQty=$updtls_size_wise_arr[str_replace("'", '',$$txtdeliveryid)]['bil_qty'];
				
				
				$balanceQty=str_replace("'", '',$$txtOrderQuantity) -(str_replace("'", '',$$cumBillQty)+$duplicat_order_arr[str_replace("'", '',$$txtColorSizeid)]);
				
				
				//$duplicat_order_arr[$$txtColorSizeid]+=$$txtColorSizeid;
				//$duplicat_order_arr[str_replace("'", '',$$txtColorSizeid)]+=str_replace("'", '',$$txtbillqty);
				
				//$balanceQty=str_replace("'", '',$$txtOrderQuantity) -(str_replace("'", '',$$cumBillQty)+$duplicat_order_arr[str_replace("'", '',$$txtColorSizeid)]);
				
				if($balanceQty<0)
				
				{
					echo "14**Bill Qty Over Order Balance.";
			        disconnect($con); die;
				}
			}
			
		}
  		die;
		*/
 		if(str_replace("'","",$txt_update_id)=="")
		{
			$id=return_next_id("id", "subcon_inbound_bill_mst", 1);

			if($db_type==0) $year_cond=" and YEAR(insert_date)"; else if($db_type==2) $year_cond=" and TO_CHAR(insert_date,'YYYY')";	
		
			$new_bill_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'WBI', date("Y",time()), 5, "select prefix_no, prefix_no_num from  subcon_inbound_bill_mst where company_id=$cbo_company_name and process_id=$bill_process_id $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
			
			$field_array="id, prefix_no, prefix_no_num, bill_no, company_id, location_id, floor_id, party_source, party_id, party_location_id, bill_date, process_id, remarks,currency,exchange_rate,delivery_id,variable_status,upcharge, discount, net_bill_amount, upcharge_remarks, discount_remarks, inserted_by, insert_date, status_active, is_deleted";
			$data_array="(".$id.",'".$new_bill_no[1]."','".$new_bill_no[2]."','".$new_bill_no[0]."',".$cbo_company_name.",".$cbo_location_name.",".$cbo_floor_name.",".$cbo_within_group.",".$cbo_party_name.",".$cbo_party_location.",".$txt_bill_date.",'".$bill_process_id."',".$txt_remarks.",".$cbo_currency.",".$txt_exchange_rate.",".$txt_delivery_id.",".$txt_variable_status.",".$txt_upcharge.",".$txt_discount.",".$txt_net_Amount.",".$txt_up_remarks.",".$txt_discount_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)"; 
			//echo "INSERT INTO subcon_inbound_bill_mst (".$field_array.") VALUES ".$data_array; die; 
			
			$return_no=$new_bill_no[0];
			
			$data_arr_dtls="";
			$id1=return_next_id( "id", "subcon_inbound_bill_dtls",1);
			$field_array1 ="id, mst_id, delivery_id, delivery_date, order_id, delivery_qty, rate, amount,domestic_amount, remarks, process_id, buyer_po_id, color_size_id, inserted_by, insert_date, status_active, is_deleted";
			$field_array_delivery="bill_status";
 			$add_comma=0;
			for($i=1; $i<=$total_row; $i++)
			{
				$txtbuyerPoId="txtbuyerPoId_".$i;
				$txtdeliveryid="txtdeliveryid_".$i;
				$sysnotd="sysnotd_".$i;
				$deliverydatetd="deliverydatetd_".$i;
				$txtbillqty="txtbillqty_".$i;
				$txtbillrate="txtbillrate_".$i;
				$txtbillamount="txtbillamount_".$i;
				$txtdomisticamount="txtdomisticamount_".$i;
				$txtRemarks="txtRemarks_".$i;
				$txtpoid="txtpoid_".$i;
				$txtColorSizeid="txtColorSizeid_".$i;
				$txtDtlsUpdateId="txtDtlsUpdateId_".$i;
				
				
				
				$date_cell=date("d-m-Y", strtotime( str_replace("'", '',$$deliverydatetd) ));
				
				if($db_type==0) $deliverydate=change_date_format($date_cell, "Y-m-d", "-",1);
				else $deliverydate=change_date_format($date_cell, "d-M-y", "-",1);
				
				//echo $$totaldeleveryidwiseQuantity;
				
				$totaldeleveryidwiseQuantity="totaldeleveryidwiseQuantity".$i;
				$total_deleveryid_wise_Quantity=str_replace("'", '',$$totaldeleveryidwiseQuantity);
				$preBilQty=$updtls_size_wise_arr[str_replace("'", '',$$txtdeliveryid)]['bil_qty'];
				$deleveryidwise_balnceQty=($total_deleveryid_wise_Quantity-$preBilQty)*1;
				
				if(str_replace("'", '',$$txtbillamount)!="")
				{
					if ($add_comma!=0) $data_array1 .=",";
					$data_array1 .="(".$id1.",".$id.",".$$txtdeliveryid.",'".$deliverydate."',".$$txtpoid.",".$$txtbillqty.",".$$txtbillrate.",".$$txtbillamount.",".$$txtdomisticamount.",".$$txtRemarks.",'".$bill_process_id."',".$$txtbuyerPoId.",".$$txtColorSizeid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$id1=$id1+1;
					$add_comma++;
					 
					if($variable_status==2)
					{
						
						if($deleveryidwise_balnceQty==str_replace("'", '',$$txtbillqty))
						{
							$data_array_delivery[str_replace("'",'',$$txtdeliveryid)] =explode("*",("1"));
							$order_id_arr[]=str_replace("'",'',$$txtdeliveryid);
						}
						else if($deleveryidwise_balnceQty>str_replace("'", '',$$txtbillqty))
						{
							
							$data_array_delivery[str_replace("'",'',$$txtdeliveryid)] =explode("*",("0"));
							$order_id_arr[]=str_replace("'",'',$$txtdeliveryid);
 						}
					}
					else
					{
						$data_array_delivery[str_replace("'",'',$$txtdeliveryid)] =explode("*",("1"));
						$order_id_arr[]=str_replace("'",'',$$txtdeliveryid);
						
					}
 				}
			}
		}
		$flag=1;
		
		//echo "10**".$id_dtls."**".$QcPassQtyArr[$$emblProdDtlsId];    die;
		// echo "10**INSERT INTO subcon_inbound_bill_mst (".$field_array.") VALUES ".$data_array;    die;
		
  //echo "10**".bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_array_delivery,$order_id_arr );    die;
		  
		
		
		
		
		$rID=sql_insert("subcon_inbound_bill_mst",$field_array,$data_array,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID1=sql_insert("subcon_inbound_bill_dtls",$field_array1,$data_array1,0);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		$rID2=execute_query(bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_array_delivery,$order_id_arr ));
		 
		 
		if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		
 //echo "10**$rID**$rID2"; die;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'","",$id)."**".$return_no."**".str_replace("'", '',$txt_exchange_rate)."**".str_replace("'", '',$txt_delivery_id)."**".str_replace("'", '',$cbo_company_name);
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
				echo "0**".str_replace("'","",$id)."**".$return_no."**".str_replace("'", '',$txt_exchange_rate)."**".str_replace("'", '',$txt_delivery_id)."**".str_replace("'", '',$cbo_company_name);
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
		$id=str_replace("'",'',$txt_update_id);
		$nameArray= sql_select("select is_posted_account, post_integration_unlock from subcon_inbound_bill_mst where id='$id'");
		$posted_account=$nameArray[0][csf('is_posted_account')];
		$post_integration_unlock=$nameArray[0][csf('post_integration_unlock')];
		if($posted_account==1 && $post_integration_unlock==0)
		{
			echo "14**All Ready Posted in Accounting.";
			disconnect($con); die;
		}
		
		$field_array="location_id*floor_id*party_source*party_id*party_location_id*bill_date*remarks*currency*exchange_rate*variable_status*upcharge*discount*net_bill_amount* upcharge_remarks*discount_remarks*updated_by*update_date";
		$data_array="".$cbo_location_name."*".$cbo_floor_name."*".$cbo_within_group."*".$cbo_party_name."*".$cbo_party_location."*".$txt_bill_date."*".$txt_remarks."*".$cbo_currency."*".$txt_exchange_rate."*".$txt_variable_status."*".$txt_upcharge."*".$txt_discount."*".$txt_net_Amount."*".$txt_up_remarks."*".$txt_discount_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$sql_dtls="Select id, delivery_id from subcon_inbound_bill_dtls where mst_id=$id and status_active=1 and is_deleted=0";
		$nameArray=sql_select( $sql_dtls );
		foreach($nameArray as $row)
		{
			$dtls_update_id_array[]=$row[csf('id')];
			$delivery_id_arr[$row[csf('id')]]=$row[csf('delivery_id')];
		}
		 
		$return_no=str_replace("'",'',$txt_bill_no);
		
		$id1=return_next_id( "id", "subcon_inbound_bill_dtls",1);
		$field_array1 ="id, mst_id, delivery_id, delivery_date, order_id, delivery_qty, rate, amount,domestic_amount,remarks, process_id, buyer_po_id, color_size_id, inserted_by, insert_date, status_active, is_deleted";
		$field_array_up ="delivery_id*delivery_date*order_id*delivery_qty*rate*amount*domestic_amount*remarks*buyer_po_id*color_size_id*updated_by*update_date";
		$field_array_delivery="bill_status";
		$add_comma=0;
		for($i=1; $i<=$total_row; $i++)
		{
			$txtbuyerPoId="txtbuyerPoId_".$i;
			$txtdeliveryid="txtdeliveryid_".$i;
			$sysnotd="sysnotd_".$i;
			$deliverydatetd="deliverydatetd_".$i;
			$txtbillqty="txtbillqty_".$i;
			$txtbillrate="txtbillrate_".$i;
			$txtbillamount="txtbillamount_".$i;
			$txtdomisticamount="txtdomisticamount_".$i;
			$txtRemarks="txtRemarks_".$i;
			$txtpoid="txtpoid_".$i;
			$txtColorSizeid="txtColorSizeid_".$i;
			$txtDtlsUpdateId="txtDtlsUpdateId_".$i;
			
			$date_cell=date("d-m-Y", strtotime( str_replace("'", '',$$deliverydatetd) ));
			
			
			$totaldeleveryidwiseQuantity="totaldeleveryidwiseQuantity".$i;
			$total_deleveryid_wise_Quantity=str_replace("'", '',$$totaldeleveryidwiseQuantity);
			$preBilQty=$updtls_size_wise_arr[str_replace("'", '',$$txtdeliveryid)]['bil_qty'];
			$deleveryidwise_balnceQty=$total_deleveryid_wise_Quantity-$preBilQty;
			//echo "10**";
			
			//echo $preBilQty."==".$deleveryidwise_balnceQty."==".$$totaldeleveryidwiseQuantity;
			
			
			
			if($db_type==0) $deliverydate=change_date_format($date_cell, "Y-m-d", "-",1);
			else $deliverydate=change_date_format($date_cell, "d-M-y", "-",1);
			
			if(str_replace("'",'',$$txtDtlsUpdateId)=="")  
			{ 
				if ($add_comma!=0) $data_array1 .=",";
				$data_array1 .="(".$id1.",".$id.",".$$txtdeliveryid.",'".$deliverydate."',".$$txtpoid.",".$$txtbillqty.",".$$txtbillrate.",".$$txtbillamount.",".$$txtdomisticamount.",".$$txtRemarks.",'".$bill_process_id."',".$$txtbuyerPoId.",".$$txtColorSizeid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				
 				   if($variable_status==2)
					{
 						if($deleveryidwise_balnceQty==str_replace("'", '',$$txtbillqty))
						{
 							
							 $data_array_delivery[str_replace("'",'',$$txtdeliveryid)] =explode("*",("1"));
							  $id_arr_delivery[]=str_replace("'",'',$$txtdeliveryid);
						}
						else if($deleveryidwise_balnceQty>str_replace("'", '',$$txtbillqty))
						{
							    $id_arr_delivery[]=str_replace("'",'',$$txtdeliveryid);
								$data_array_delivery[str_replace("'",'',$$txtdeliveryid)] =explode("*",("0"));
						}
					}
					else
					{
						  $data_array_delivery[str_replace("'",'',$$txtdeliveryid)] =explode("*",("1"));
						  $id_arr_delivery[]=str_replace("'",'',$$txtdeliveryid);
 					}
				
 				$id1=$id1+1;
				$add_comma++;
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$txtDtlsUpdateId);
				$data_array_up[str_replace("'",'',$$txtDtlsUpdateId)] =explode("*",("".$$txtdeliveryid."*'".$deliverydate."'*".$$txtpoid."*".$$txtbillqty."*".$$txtbillrate."*".$$txtbillamount."*".$$txtdomisticamount."*".$$txtRemarks."*".$$txtbuyerPoId."*".$$txtColorSizeid."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				//$delivery_id_arr[str_replace("'",'',$$txtDtlsUpdateId)]=str_replace("'",'',$$delivery_id);
  				
				   if($variable_status==2)
					{
						
						/// echo "10**";
			
			         // echo $deleveryidwise_balnceQty."==".str_replace("'", '',$$txtbillqty);
						
						if($deleveryidwise_balnceQty==str_replace("'", '',$$txtbillqty))
						{
 								$id_arr_delivery[]=str_replace("'",'',$$txtdeliveryid);
								$data_array_delivery[str_replace("'",'',$$txtdeliveryid)] =explode("*",("1"));
						}
						else if($deleveryidwise_balnceQty>str_replace("'", '',$$txtbillqty))
						{
							    $id_arr_delivery[]=str_replace("'",'',$$txtdeliveryid);
								$data_array_delivery[str_replace("'",'',$$txtdeliveryid)] =explode("*",("0"));
						}
					}
					else
					{
						  	$id_arr_delivery[]=str_replace("'",'',$$txtdeliveryid);
							$data_array_delivery[str_replace("'",'',$$txtdeliveryid)] =explode("*",("1"));
 					}
			}
		}

		$flag=1;
		$rID=sql_update("subcon_inbound_bill_mst",$field_array,$data_array,"id",$txt_update_id,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		if($data_array_up!="")
		{
			$rID1=execute_query(bulk_update_sql_statement("subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if($data_array1!="")
		{
			//echo "insert into subcon_inbound_bill_dtls (".$field_array1.") values ".$data_array1;
			$rID3=sql_insert("subcon_inbound_bill_dtls",$field_array1,$data_array1,0);
			if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		}
		//echo "10**";
		//echo bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_array_delivery,$id_arr_delivery ); die;
		$rID2=execute_query(bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_array_delivery,$id_arr_delivery ));
		if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		
		$distance_delete_id="";
	
		if(implode(',',$id_arr)!="")
		{
			$distance_delete_id=implode(',',array_diff($dtls_update_id_array,$id_arr));
		}
		else
		{
			$distance_delete_id=implode(',',$dtls_update_id_array);
		}
		
		$field_array_del="status_active*is_deleted*updated_by*update_date";
		$data_array_del="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$unlock_delivery_id_arr=array();
		
		if(str_replace("'",'',$distance_delete_id)!="")
		{
			$ex_delete_id=explode(",",$distance_delete_id);
			$rID4=execute_query(bulk_update_sql_statement( "subcon_inbound_bill_dtls", "id", $field_array_del,$data_array_del,$ex_delete_id),1);
			if($rID4==1 && $flag==1) $flag=1; else $flag=0;
			//echo "10**".print_r($ex_delete_id); die;
			foreach($ex_delete_id as $upid)
			{
				$unlock_delivery_id_arr[]=$delivery_id_arr[$upid];
				$data_delivery[$delivery_id_arr[$upid]] =explode(",",("0"));
			}
			//echo "10**".print_r($delivery_id_arr); die;
			if(implode(',',$unlock_delivery_id_arr)!='')
			{
				$rID5=execute_query(bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_delivery,$unlock_delivery_id_arr ));
				if($rID5==1 && $flag==1) $flag=1; else $flag=0;
			}
		}
		
		// echo "10**$rID**$rID1**$rID2**$rID3**$rID4**$rID5"; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_update_id)."**".$return_no."**".str_replace("'", '',$txt_exchange_rate)."**".str_replace("'", '',$txt_delivery_id)."**".str_replace("'", '',$cbo_company_name);
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
				echo "1**".str_replace("'","",$txt_update_id)."**".$return_no."**".str_replace("'", '',$txt_exchange_rate)."**".str_replace("'", '',$txt_delivery_id)."**".str_replace("'", '',$cbo_company_name);
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
		
		$delivery_mst_id=str_replace("'","",$txt_update_id);
		
		
		$nameArray= sql_select("select is_posted_account, post_integration_unlock from subcon_inbound_bill_mst where id='$delivery_mst_id'");
		$posted_account=$nameArray[0][csf('is_posted_account')];
		$post_integration_unlock=$nameArray[0][csf('post_integration_unlock')];
		if($posted_account==1 && $post_integration_unlock==0)
		{
			echo "14**All Ready Posted in Accounting.";
			disconnect($con); die;
		}
		
		
		$mrr_no=str_replace("'","",$txt_sys_id);
		$mrr_no_challan=str_replace("'","",$txt_challan_no);
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="'".$user_id."'*'".$pc_date_time."'*0*1";

		//$rID=sql_delete("subcon_delivery_mst",$field_array,$data_array,"id","".$txt_update_id."",1);
  		
		//$rID = sql_delete("pro_ex_factory_mst",$field_array,$data_array,"id",$txt_mst_id,1);
		//$dtlsrID = sql_delete("pro_ex_factory_dtls","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);
 		
 		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_update_id)."**".$mrr_no."**".$mrr_no_challan;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_update_id); 
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con); 
				echo "2**".str_replace("'","",$txt_update_id)."**".$mrr_no."**".$mrr_no_challan; 
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

if ($action=="bill_popup")
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
			load_drop_down( 'wash_bill_issue_controller', company+'_'+within_group+'_'+party_name, 'load_drop_down_buyer_pop', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Wash Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer Po');
			else if(val==5) $('#search_by_td').html('Buyer Style');
			else if(val==6) $('#search_by_td').html('IR/IB');
		}		
	</script>
	</head>
	<body onLoad="fnc_load_party_order_popup(<? echo $company;?>,<? echo $party_name;?>,<? echo $within_group;?>)">
        <div align="center" style="width:100%;" >
            <form name="searchreceivefrm_1"  id="searchreceivefrm_1" autocomplete="off">
                <table width="870" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="9"><? echo create_drop_down( "cbo_string_search_type", 140, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                    	<tr>                	 
                            <th width="140">Company Name</th>
                            <th width="50">Within Group</th>
                            <th width="120">Party Name</th>
                            <th width="70">Bill ID</th>
                            <th width="100">Search By</th>
                    		<th width="100" id="search_by_td">Wash Job No</th>
                            <th width="100" colspan="2" class="must_entry_caption">Date Range</th>
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
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:60px" placeholder="Receive ID" />
                            </td>
                            <td>
								<?
                                    $search_by_arr=array(1=>"Wash Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style",6=>"IR/IB");
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
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="checkFields();showList();" style="width:70px;" /></td>
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
        <script>
        	var isValidated = false;
        	function checkFields() {
        		var billId = document.getElementById('txt_search_common').value;
        		var searchString = document.getElementById('txt_search_string').value;

        		if(billId == '' && searchString == '') {
        			if( !form_validation('txt_date_from*txt_date_to','Date From*Date To') ) {
						return;
					}
        		}

        		isValidated = true;
        	}

        	function showList() {
        		if(!isValidated) {
        			return;
        		}

        		show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value, 'create_bill_search_list_view', 'search_div', 'wash_bill_issue_controller', 'setFilterGrid(\'tbl_po_list\',-1)');
        		isValidated = false;
        	}
        </script>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>$('#cbo_company_name').attr('disabled','disabled');</script>
	</html>
	<?
	exit();
}

if($action=="create_bill_search_list_view")
{
	// echo "<pre>"; print_r($data);die;
	$data=explode('_',$data);
	$search_type =$data[5];
	$within_group =$data[6];
	$search_by=str_replace("'","",$data[7]);
	$search_str=trim(str_replace("'","",$data[8]));

	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer_cond=" and a.party_id='$data[1]'"; else $buyer_cond="";
	if ($within_group!=0) $withinGroup=" and a.party_source='$within_group'"; else $withinGroup="";
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $bill_date = "and a.bill_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $bill_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $bill_date = "and a.bill_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $bill_date ="";
	}
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and c.subcon_job='$search_str'";
			else if($search_by==2) $search_com_cond="and d.order_no='$search_str'";
			
			else if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";// incorrect(Not Working)
			else if ($search_by==4) $po_cond=" and d.buyer_po_no = '$search_str' ";
			else if ($search_by==5) $style_cond=" and d.BUYER_STYLE_REF = '$search_str' ";
			else if ($search_by==6) $inter_ref=" and b.grouping = '$search_str' ";// incorrect(Not Working)
		}
		if ($data[4]!='') $bill_id_cond=" and a.prefix_no_num='$data[4]'"; else $bill_id_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_job_cond ="and c.subcon_job like '%$search_str%'";  
			else if($search_by==2) $search_order_cond ="and d.order_no like '%$search_str%'";  
			
			else if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'"; // incorrect(Not Working) 
			else if ($search_by==4) $po_cond =" and d.buyer_po_no like '%$search_str%'"; 
			else if ($search_by==5) $style_cond =" and d.BUYER_STYLE_REF like '%$search_str%'";
			else if ($search_by==6) $inter_ref=" and b.grouping  like '%$search_str%'";// incorrect(Not Working)
			  
		}
		if ($data[4]!='') $bill_id_cond=" and a.prefix_no_num like '%$data[4]%'"; else $bill_id_cond="";
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_job_cond ="and c.subcon_job like '$search_str%'";  
			else if($search_by==2) $search_order_cond ="and d.order_no like '$search_str%'";  
			
			else if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  // incorrect(Not Working)
			else if ($search_by==4) $po_cond =" and d.buyer_po_no like '$search_str%'";
			else if ($search_by==5) $style_cond =" and d.BUYER_STYLE_REF like '$search_str%'"; 
			else if ($search_by==6) $inter_ref=" and b.grouping  like '$search_str%'"; // incorrect(Not Working)
		}
		if ($data[4]!='') $bill_id_cond=" and a.prefix_no_num like '$data[4]%'"; else $bill_id_cond="";
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_job_cond ="and c.subcon_job like '%$search_str'";  
			else if($search_by==2) $search_order_cond ="and d.order_no like '%$search_str'";  
			
			else if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  // incorrect(Not Working)
			else if ($search_by==4) $po_cond =" and d.buyer_po_no like '%$search_str'";
			else if ($search_by==5) $style_cond =" and d.BUYER_STYLE_REF like '%$search_str'";
			else if ($search_by==6) $inter_ref=" and b.grouping  like '%$search_str'"; // incorrect(Not Working)
		}
		if ($data[4]!='') $bill_id_cond=" and a.prefix_no_num like '%$data[4]'"; else $bill_id_cond="";
	}	
	
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array("select id, company_name from lib_company",'id','company_name');
	$order_arr=array();
	$order_sql = sql_select("select a.subcon_job, a.within_group, a.party_id, b.id, b.order_no, sum(c.qnty) as qty from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and b.id=c.mst_id and a.entry_form=295 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.subcon_job, a.within_group, a.party_id, b.id, b.order_no");
	foreach($order_sql as $row)
	{
		$order_arr[$row[csf('id')]]['po']=$row[csf('order_no')];
		$order_arr[$row[csf('id')]]['job']=$row[csf('subcon_job')];
	}
	unset($order_sql);
	
	$po_ids=''; $buyer_po_arr=array();
	if($within_group==1)
	{
		if($db_type==0) $id_cond="group_concat(b.id)";
		// else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
		else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',')";
		if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5) || ($inter_ref!="" && $search_by==6) )
		{
			$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond $inter_ref", "id");
		}
		//echo $po_ids;
		if($db_type==2 && $po_ids!="") $po_ids = $po_ids->load();

		if ($po_ids!="") {
			$po_ids=explode(",",$po_ids);
			$po_idsCond=""; $poIdsCond="";
			//echo count($po_ids); die;
			if($db_type==2 && count($po_ids)>=999)
			{
				$chunk_arr=array_chunk($po_ids,999);
				foreach($chunk_arr as $val)
				{
					$ids=implode(",",$val);
					if($po_idsCond=="")
					{
						$po_idsCond.=" and ( b.buyer_po_id in ( $ids) ";
						$poIdsCond.=" and ( b.id in ( $ids) ";
					}
					else
					{
						$po_idsCond.=" or b.buyer_po_id in ( $ids) ";
						$poIdsCond.=" or b.id in ( $ids) ";
					}
				}
				$po_idsCond.=")";
				$poIdsCond.=")";
			}
			else
			{
				$ids=implode(",",$po_ids);
				$po_idsCond.=" and b.buyer_po_id in ($ids) ";
				$poIdsCond.=" and b.id in ($ids) ";
			}
		} else {
			$po_idsCond="";
		}

		// if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
		
		$po_sql ="Select a.style_ref_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
			$buyer_po_arr[$row[csf("id")]]['buyer']=$party_arr[$row[csf("buyer_name")]];
		}
		unset($po_sql_res);
	}
	$spo_ids='';
	
	if($db_type==0)
	{
		$id_cond="group_concat(b.id)";
		$insert_date_cond="year(a.insert_date)";
		$order_id_cond="group_concat(distinct(b.order_id))";
		$buyer_po_id_cond="group_concat(distinct(b.buyer_po_id))";
		$delivery_id_cond="group_concat(b.delivery_id)";
	}
	else if($db_type==2)
	{
		$insert_date_cond="TO_CHAR(a.insert_date,'YYYY')";
		$order_id_cond="listagg(b.order_id,',') within group (order by b.order_id)";
		$buyer_po_id_cond="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
		$delivery_id_cond="LISTAGG(CAST(b.delivery_id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY b.delivery_id)";
	}
	if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2))
	{
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.subcon_job=b.job_no_mst $search_com_cond", "id");
	}
	
	if ( $spo_ids!="") $spo_idsCond=" and b.job_dtls_id in ($spo_ids)"; else $spo_idsCond="";
	
	
	if ( $spo_ids!="") $orderidsCond=" and b.id in ($spo_ids)"; else $orderidsCond="";
	
			$order_buyer_po_array=array();
			$orderbuyerpo_arr=array(); 
			$order_sql ="select b.id,b.buyer_po_no,b.buyer_style_ref,b.party_buyer_name from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.entry_form='295' $orderidsCond $within_no_cond"; 
			$order_sql_res=sql_select($order_sql);
			foreach ($order_sql_res as $row)
			{
				$order_buyer_po_array[]=$row[csf("id")];
				$orderbuyerpo_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
				$orderbuyerpo_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
				$orderbuyerpo_arr[$row[csf("id")]]['party_buyer_name']=$row[csf("party_buyer_name")];
			}
	

	 $sql= "select a.id, a.bill_no, a.prefix_no_num, $insert_date_cond as year, a.location_id, a.floor_id, a.bill_date, a.party_id, a.party_source, a.party_location_id, a.remarks, $order_id_cond as order_id, $buyer_po_id_cond as buyer_po_id, $delivery_id_cond as delivery_id,a.is_posted_account,a.currency, a.exchange_rate,a.delivery_id as type_delivery_id   from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b, subcon_ord_mst c, subcon_ord_dtls d where a.id=b.mst_id and c.id=d.mst_id and b.order_id=d.id and a.status_active=1 and a.process_id=304 $company $buyer_cond $withinGroup $bill_date $bill_id_cond $search_job_cond $search_order_cond $po_cond $style_cond $spo_idsCond $po_idsCond  group by a.id, a.bill_no, a.prefix_no_num, a.insert_date, a.location_id, a.floor_id, a.bill_date, a.party_id, a.party_source, a.party_location_id, a.remarks,a.is_posted_account,a.currency, a.exchange_rate,a.delivery_id order by a.id DESC";
	// echo $sql;

	$result = sql_select($sql);
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="70">Bill No</th>
                <th width="70">Year</th>
                <th width="120">Party Name</th>
                <th width="80">Bill Date</th>
                <th width="120">Order No</th>
                <th width="100">Buyer Po</th>
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
				$order_no=''; $job_no=""; $odbuyer_po=""; $odbuyer_style="";$party_buyer_name="";
				$order_id=array_unique(explode(",",$row[csf("order_id")]));
				foreach($order_id as $val)
				{
					if($order_no=="") $order_no=$order_arr[$val]['po']; else $order_no.=",".$order_arr[$val]['po'];
					if($job_no=="") $job_no=$order_arr[$val]['job']; else $job_no.=",".$order_arr[$val]['job'];
					if($odbuyer_po=="") $odbuyer_po=$orderbuyerpo_arr[$val]['po']; else $odbuyer_po.=','.$orderbuyerpo_arr[$val]['po'];
					if($odbuyer_style=="") $odbuyer_style=$orderbuyerpo_arr[$val]['style']; else $odbuyer_style.=','.$orderbuyerpo_arr[$val]['style'];
					if($party_buyer_name=="") $party_buyer_name=$orderbuyerpo_arr[$val]['party_buyer_name']; else $party_buyer_name.=','.$orderbuyerpo_arr[$val]['party_buyer_name'];
				}
				$order_no=implode(",",array_unique(explode(",",$order_no)));
				$job_no=implode(",",array_unique(explode(",",$job_no)));
				
				$buyer_po=""; $buyer_style=""; $buyer_name="";
				$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
				foreach($buyer_po_id as $po_id)
				{
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
					if($buyer_name=="") $buyer_name=$buyer_po_arr[$po_id]['buyer']; else $buyer_name.=','.$buyer_po_arr[$po_id]['buyer'];
				}
				//$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				//$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				
				
				
				if($row[csf("party_source")]==1)
				{
					$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
					$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
					$buyer_name=implode(",",array_unique(explode(",",$buyer_name)));
				}
				else
				{
					$buyer_po=implode(",",array_unique(explode(",",$odbuyer_po)));
					$buyer_style=implode(",",array_unique(explode(",",$odbuyer_style)));
					$buyer_name=implode(",",array_unique(explode(",",$party_buyer_name)));
				}
				
				
			
				
				
				$party_name="";
				if($row[csf("party_source")]==1) $party_name=$comp[$row[csf("party_id")]]; else $party_name=$party_arr[$row[csf("party_id")]];
				$str_data="";
				$str_data=$row[csf('id')].'***'.$row[csf('bill_no')].'***'.$row[csf('location_id')].'***'.$row[csf('party_source')].'***'.$row[csf('party_id')].'***'.$row[csf('party_location_id')].'***'.change_date_format($row[csf('bill_date')]).'***'.$row[csf('remarks')].'***'.$job_no.'***'.$order_no.'***'.$buyer_style.'***'.$buyer_po.'***'.$buyer_name.'***'.$row[csf('is_posted_account')].'***'.$row[csf('currency')].'***'.$row[csf('exchange_rate')].'***'.$row[csf('type_delivery_id')].'***'.$row[csf('floor_id')].'***'.$row[csf('upcharge')].'***'.$row[csf('discount')].'***'.$row[csf('net_bill_amount')].'***'.$row[csf('upcharge_remarks')].'***'.$row[csf('discount_remarks')];
				?> 
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $str_data; ?>');" > 
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="70" align="center"><? echo $row[csf("prefix_no_num")]; ?></td>
                        <td width="70" align="center"><? echo $row[csf("year")]; ?></td>
                        <td width="120"><? echo $party_name; ?></td>		
						<td width="80"><? echo change_date_format($row[csf("bill_date")]);  ?></td>
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

if($action=="embl_bill_issue_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$size_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	//$recipe_arr = return_library_array("select id, recipe_no from pro_recipe_entry_mst", 'id', 'recipe_no');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	
	$delivery_arr=array();
	$delivery_sql = "select b.id, a.delivery_prefix_num, a.delivery_no from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$delivery_sql_res=sql_select($delivery_sql);
	foreach ($delivery_sql_res as $row)
	{
		$delivery_arr[$row[csf("id")]]=$row[csf("delivery_prefix_num")];
	}
	unset($delivery_sql_res);
	
	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	
	 $sql_mst="select id, prefix_no_num, bill_no, location_id, bill_date, party_id, party_source, process_id, party_location_id, remarks, currency, exchange_rate,upcharge, discount, net_bill_amount, upcharge_remarks, discount_remarks
	from subcon_inbound_bill_mst
	where process_id=304 and status_active=1 and is_deleted=0 and id='$data[1]'"; 

	$dataArray = sql_select($sql_mst); $party_name=""; $party_address=""; $party_address="";
	if( $dataArray[0][csf('party_source')]==1)
	{
		$party_name=$company_library[$dataArray[0][csf('party_id')]];
		
		$party_address=show_company($dataArray[0][csf('party_id')],'','');
		
		//if($party_address!="") $party_address=$party_name.', '.$party_address;
	}
	else if($dataArray[0][csf('party_source')]==2) 
	{
		$party_name=$buyer_library[$dataArray[0][csf('party_id')]];
		$nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$dataArray[0][csf('party_id')]"); 
		foreach ($nameArray as $result)
		{ 
			if($result!="") $party_address=$result['address_1'];
		}
		//if($address!="") $party_address=$party_name.', '.$party_address;
	}
	
	?>
    <div style="width:930px;">
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
        <table width="100%" cellpadding="0" cellspacing="0" >  
            <tr>
            	<td width="130"><strong>Bill ID:</strong></td>
                <td width="175"><? echo $dataArray[0][csf('bill_no')]; ?></td>
            	<td width="130"><strong>Party: </strong></td>
                <td width="175"><? echo $party_name; ?></td>
                <td width="130"><strong>Party Location:</strong></td>
                <td><? echo $location_arr[$dataArray[0][csf('party_location_id')]]; ?></td>
            </tr>
            <tr>
            	<td><strong>Party Address: </strong></td>
                <td colspan="3"><? echo $party_address; ?></td>
            	<td><strong>Bill Date: </strong></td>
                <td><? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
            </tr>
            <tr>
            	<td><strong>Remarks: </strong></td>
                <td><? echo $dataArray[0][csf('remarks')]; ?></td>
            	<td><strong>Currency:</strong></td>
                <td><? echo $currency[$dataArray[0][csf('currency')]]; ?></td>
            	<td><strong>Exchange Rate:</strong></td>
                <td><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
            </tr>
        </table>
        <br>
        <div style="width:100%;">
            <table align="right" cellspacing="0" width="930" border="1" rules="all" class="rpt_table" style="font-size:12px">
                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
                    <th width="30">SL</th>
                    <th width="50">Delivery ID</th>
                    <th width="60">Delivery Date</th>
                    <th width="90">Buyer PO</th>
                    <th width="90">Buyer Style</th>
                    <th width="90">Gmts Item</th>
                    <th width="80">Process Name</th>
                    <th width="70">Wash Type</th>
                    <th width="80">Color</th>
                    <th width="70">Size</th>
                    <th width="60">Bill Qty</th>
                    <th width="50">Rate</th>
                    <th width="60">Amount</th>
                    <th>Remarks</th>
                </thead>
				<?
				$mst_id = $data[1];
				$com_id = $data[0];
				//$job_no = $dataArray[0][csf('job_no')];
				$job_no = $data[3];

				/*$sql= "select  a.id, a.subcon_job, b.id as order_id, b.order_no, b.buyer_po_id, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.id as color_size_id, c.color_id, c.size_id, c.qnty as qty, d.delivery_qty, d.rate, d.amount, d.remarks, d.delivery_id, d.delivery_date from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_inbound_bill_dtls d where a.subcon_job=b.job_no_mst and b.id=c.mst_id and c.id=d.color_size_id 
				
				and a.entry_form=295 and d.process_id=304 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id='$data[0]' and d.mst_id='$data[1]' order by c.id ASC";*/
				
			/*	$sql= "select  a.id, a.subcon_job, b.id as order_id, b.order_no, b.buyer_po_id, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.id as color_size_id, c.color_id, c.size_id, c.qnty as qty, d.delivery_qty, d.rate, d.amount, d.remarks, d.delivery_id, d.delivery_date from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_inbound_bill_dtls d where a.subcon_job=b.job_no_mst and b.id=c.mst_id and b.id=d.order_id and c.mst_id=d.order_id
				
				and a.entry_form=295 and d.process_id=304 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id='$data[0]' and d.mst_id='$data[1]'  group by order by c.id ASC";*/
				
				if($db_type==0) $process_type_cond="group_concat(c.process,'*',c.embellishment_type)";
				else if ($db_type==2) $process_type_cond="listagg(c.process||'*'||c.embellishment_type,',') within group (order by c.process||'*'||c.embellishment_type)";
				 $sql="(select a.id, a.company_id,a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.gmts_item_id, $process_type_cond as process_type, b.order_uom, b.gmts_color_id as color_id, b.order_quantity as qnty, d.delivery_prefix_num,d.id as delivery_mst_id, d.delivery_date, e.id as delivery_id, e.delivery_qty, f.id as upid, f.rate, f.amount,f.domestic_amount, f.remarks,b.buyer_po_no,b.buyer_style_ref,f.delivery_qty as bil_qty
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e, subcon_inbound_bill_dtls f 
		where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and d.id=e.mst_id and b.id=e.order_id and e.id=f.delivery_id and f.process_id=304 and f.mst_id='$data[1]' and d.entry_form=303 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.id=c.mst_id and a.subcon_job='$job_no'
		group by a.id,a.company_id, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id, b.buyer_po_id, b.gmts_item_id, b.order_uom, b.gmts_color_id, b.order_quantity, d.delivery_prefix_num,d.id, d.delivery_date, e.id , e.delivery_qty, f.id, f.rate, f.amount,f.domestic_amount, f.remarks,b.buyer_po_no,b.buyer_style_ref,f.delivery_qty
		)
		union all
		(
			select a.id,a.company_id, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.gmts_item_id, $process_type_cond as process_type, b.order_uom, b.gmts_color_id as color_id, b.order_quantity as qnty, d.delivery_prefix_num,d.id as delivery_mst_id, d.delivery_date, e.id as delivery_id, e.delivery_qty, 0 as upid, 0 as rate, 0 as amount,0 as domestic_amount, null as remarks,b.buyer_po_no,b.buyer_style_ref,0 as bil_qty
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e
		where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and d.id=e.mst_id and b.id=e.order_id and e.bill_status!=1 and d.entry_form=303 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.id=c.mst_id and a.subcon_job='$job_no'
		
		group by a.id,a.company_id, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id, b.buyer_po_id, b.gmts_item_id, b.order_uom, b.gmts_color_id, b.order_quantity, d.delivery_prefix_num,d.id, d.delivery_date, e.id, e.delivery_qty,b.buyer_po_no,b.buyer_style_ref
		)
		 order by delivery_id ASC";
				 
				
		//	echo $sql; die;
				$sql_res=sql_select($sql);
				
 				$i=1; $grand_tot_qty=0; $k=1;

				foreach ($sql_res as $row) 
				{
					/*$embl_name=$row[csf('main_process_id')];
					if($embl_name==1) $new_subprocess_array= $emblishment_print_type;
					else if($embl_name==2) $new_subprocess_array= $emblishment_embroy_type;
					else if($embl_name==3) $new_subprocess_array= $emblishment_wash_type;
					else if($embl_name==4) $new_subprocess_array= $emblishment_spwork_type;
					else if($embl_name==5) $new_subprocess_array= $emblishment_gmts_type;
					else $new_subprocess_array=$blank_array;*/
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
					
					
					if (!in_array($row[csf("order_no")],$order_array) )
					{
						if($k!=1)
						{
						?>
							<tr class="tbl_bottom">
								<td colspan="10" align="right"><b>Order Total:</b></td>
								<td align="right"><b><? echo number_format($sub_total_qty,2); ?></b></td>
								<td>&nbsp;</td>
                                <td align="right"><b><? echo number_format($sub_total_amt,2); ?></b></td>
                                <td>&nbsp;</td>
							</tr>
						<?
							unset($sub_total_qty);
							unset($sub_total_amt);   //b.buyer_po_no,b.buyer_style_ref,
						}
						?>
							<tr bgcolor="#dddddd">
								<td colspan="4" align="left" style="font-size:12px"><b>Wash Job No: <? echo $row[csf("subcon_job")]; ?>;</b></td>
                                <td colspan="5" align="left" style="font-size:12px"><b>Work Order No: <? echo $row[csf("order_no")]; ?>;</b></td>
                                <td colspan="5" align="left" style="font-size:12px; display:none"><b>Buyer Style: <? echo $buyer_po_arr[$row[csf("buyer_po_id")]]['style']; ?>;</b></td>
                          <td colspan="5" ></td>      
							</tr>
						<?
						$order_array[]=$row[csf('order_no')];  
						$k++;
					}
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if($row[csf('rate')] > 0) {
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>">
	                        <td><? echo $i; ?></td>
	                        <td style="word-break:break-all"><? echo $delivery_arr[$row[csf("delivery_id")]]; ?></td>
	                        <td style="word-break:break-all"><? echo change_date_format($row[csf("delivery_date")]); ?></td>
	                        <td style="word-break:break-all"><? echo $row[csf("buyer_po_no")];//$buyer_po_arr[$row[csf("buyer_po_id")]]['po']; ?></td>
	                        <td style="word-break:break-all"><? echo $row[csf("buyer_style_ref")];//$buyer_po_arr[$row[csf("buyer_po_id")]]['po']; ?></td>
	                        <td style="word-break:break-all"><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></td>
	                        <td style="word-break:break-all"><? echo $process_name;//$emblishment_name_array[$embl_name]; ?>&nbsp;</td>
	                        <td style="word-break:break-all"><? echo $sub_process_name;//$new_subprocess_array[$row[csf('embl_type')]]; ?>&nbsp;</td>
	                        <td style="word-break:break-all"><? echo $color_arr[$row[csf('color_id')]]; ?>&nbsp;</td>
	                        <td style="word-break:break-all" align="center"><? echo $size_arr[$row[csf('size_id')]]; ?>&nbsp;</td>
	                        <td align="right"><? echo number_format($row[csf('bil_qty')], 2, '.', ''); ?>&nbsp;</td>
	                        <td align="right"><? echo $row[csf('rate')];//number_format($row[csf('rate')], 2, '.', ''); ?>&nbsp;</td>
	                        <td align="right"><? echo number_format($row[csf('amount')], 2, '.', ''); ?>&nbsp;</td>
	                        <td style="word-break:break-all"><? echo $row[csf('remarks')]; ?>&nbsp;</td>
	                    </tr>
						<?
						$i++;
						$sub_total_qty+=$row[csf('bil_qty')]; 
						$grand_tot_qty+=$row[csf('bil_qty')];
						
						$sub_total_amt+=$row[csf('amount')];
						$grand_tot_amt+=$row[csf('amount')];
					}
				}
				?>
                <tr class="tbl_bottom">
                    <td colspan="10" align="right"><b>Order Total:</b></td>
                    <td align="right"><b><? echo number_format($sub_total_qty,2); ?></b></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><? echo number_format($sub_total_amt,2); ?></b></td>
                    <td>&nbsp;</td>
                </tr>
                
                <tr class="tbl_bottom">
                    <td align="right" colspan="10"><strong>Grand Total</strong></td>
                    <td align="right"><? echo number_format($grand_tot_qty, 2, '.', ''); ?>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($grand_tot_amt, 2, '.', ''); ?>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <?
				if($dataArray[0][csf('party_source')]==11)
				{
					$colspan = 6;
					$colspan_net =11;
					//$in_world=14;
				}
				else
				{
					$colspan = 6;
					$colspan_net =11;
					//$in_world=17;
				}
				if($dataArray[0][csf("discount")]>0 || $dataArray[0][csf("upcharge")]>0)
				{  //,upcharge, discount, net_bill_amount, upcharge_remarks, discount_remarks,
					
				?>
				<tr bgcolor="#CCCCCC" style="font-size:15px">
					<td colspan="<?=$colspan;?>" align="left"></td>
					<td colspan="2" align="right"><b>Upcharge Remarks:</b></td>
					<td colspan="3" align="left" ><b><? echo $dataArray[0][csf("upcharge_remarks")]?></b></td>
					<td><b>Up Charge</b></td>
					<td align="right"><b><? echo number_format($dataArray[0][csf("upcharge")],4)?></b></td>
                    <td>&nbsp;</td>
				</tr>
				<tr bgcolor="#CCCCCC" style="font-size:15px">
					<td colspan="<?=$colspan;?>" align="left"></td>
					<td colspan="2"  align="right"><b>Discount Remarks:</b></td>
					<td colspan="3" align="left"><b><? echo $dataArray[0][csf("discount_remarks")] ?></b></td>
					<td><b>Discount</b></td>
					<td align="right"><b><? echo number_format($dataArray[0][csf("discount")],4)?></b></td>
                    <td>&nbsp;</td>
				</tr>
				<tr bgcolor="#CCCCCC" style="font-size:15px">
					<td colspan="<?=$colspan_net;?>" align="left"></td>
					<td><b>Net Total</b></td>
					<td align="right"><b><? $format_total_amount = ($grand_tot_amt+$dataArray[0][csf("upcharge")]-$dataArray[0][csf("discount")]);
					 echo number_format($format_total_amount,4)?></b></td>
                     <td>&nbsp;</td>
				</tr>
				<? } ?>
            </table>
            
            <br>
			<? echo signature_table(333, $com_id, "930px",$data[4], 50, $user_id); 
 			
			?>
        </div>
    </div>
	<?
	exit();
}
