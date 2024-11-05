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
    $sql =  sql_select("select quantity_control,id,validation_qty_control from variable_setting_printing_prod where company_name = $data and variable_list =6 and is_deleted = 0 and status_active = 1");
	$return_data="";
    if(count($sql)>0)
	{
		$return_data=$sql[0][csf('quantity_control')];
		$validation_qty=$sql[0][csf('validation_qty_control')];
	}
	else
	{ 
		$return_data=0; 
		$validation_qty=0; 
	}
	
	echo $return_data."**".$validation_qty;
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
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );
	exit();	 
}

if ($action=="load_drop_down_party_location")
{
	echo create_drop_down( "cbo_party_location", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Party Location--", $selected, "" );
	exit();	 
}

if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);

	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";
	
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $data[2], "$load_function");
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
	//echo $data[4];
	?>
	<script>
		// function js_set_value(str)
		// { 
		// 	document.getElementById('selected_order').value=str;
		// 	//alert(str);
		// 	parent.emailwindow.hide();
		// }

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
			var delevery_id=splitArr[7]*1;
			var variable_status=splitArr[9]*1;
			var search_type_delv=splitArr[5]*1;
			var search_by_delv=splitArr[6]*1;
			var job_no=splitArr[0];
			//alert(job_no);
			//OG-EOE-19-00023***OG-EB-19-00126***091019***A-4-Tech Ltd.***2***6******618***10***2 
		   	//alert(search_type_delv);
			
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
					
					
					//var job_no = $('#txt_job_no'+str).val();
			
					//alert(str);
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
					//selected_job_no.push( $('#txt_job_no' + str).val() );
					//selected_name.push( $('#txt_individual_name' + str).val() );
					}
					else 
					{
						for( var i = 0; i < selected_id.length; i++ ) 
						{
							if( selected_id[i] == $('#txt_individual_id' + delevery_id).val() ) break;
						}
						selected_id.splice( i, 1 );
						jobNoArr.splice( i, 1 );
						//selected_name.splice( i, 1 );
					}
					var id = '';
					//var name='';
					for( var i = 0; i < selected_id.length; i++ ) 
					{
						id += selected_id[i] + ',';
						//name += selected_name[i] + ',';
					}
						id = id.substr( 0, id.length - 1 );
						//job_number 	= job_number.substr( 0, job_number.length - 1 );
						//name = name.substr( 0, name.length - 1 );
						//alert(id)
						$('#txt_selected_id').val( id );
						//$('#txt_selected_name').val( name );
					}
					
					
					
					//alert(id);
					document.getElementById('selected_order').value=str;
				
			}
			else
			{
				document.getElementById('selected_order').value=str;
				/*alert(str);*/
				parent.emailwindow.hide();
			}
			
		}
		
		function fnc_load_party_order_popup(company,within_group,party)
		{
			//alert();
			load_drop_down( 'emb_bill_issue_controller', company+'_'+within_group+'_'+party, 'load_drop_down_buyer', 'buyer_td' );
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
			else if(val==6) $('#search_by_td').html('Delivery No');
		}

		function search_by_date(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td_date').html('Receive Date Range');
			else if(val==2) $('#search_by_td_date').html('Delivery Date Range');
		}
	</script>
	</head>
	<body onLoad="fnc_load_party_order_popup(<? echo $data[0];?>,<? echo $data[3];?>,<? echo $data[2];?>)">
        <div align="center" style="width:100%;" >
            <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">

			<input type="hidden" id="txt_selected_id" name="txt_selected_id" value="" />

                <table width="850" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="7"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                    	<tr>               	 
                            <th width="140">Company Name</th>
                            <th width="140">Party Name</th>
                            <th width="100">Date Type</th>
                            <th width="170" id="search_by_td_date">Receive Date Range</th>
                            <th width="100">Search By</th>
							<th width="100" id="search_by_td">Delivery No</th>
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
                            <td>
								<?
									$search_by_date_arr=array(1=>"Receive Date",2=>"Delivery Date");
									echo create_drop_down( "cbo_date_type",100, $search_by_date_arr,"",0, "",6,'search_by_date(this.value)',0 );
								?>
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td>
								<?
									$search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style",6=>"Delivery No", 7=>"IR/IB");
									echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",6,'search_by(this.value)',0 );
								?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $data[3];?>+'_'+<? echo $data[4];?>+'_'+document.getElementById('cbo_date_type').value+'_'+document.getElementById('cbo_year_selection').value, 'create_job_search_list_view', 'search_div', 'emb_bill_issue_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7" align="center" valign="middle">
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
	$variable_status =$data[8];
	$cbo_date_type =$data[9];
	
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { $company=""; echo "PLease Select Company name"; die;}
	if ($data[1]!=0) $buyer=" and a.party_id='$data[1]'"; else { $buyer=""; echo "PLease Select Party name"; die;}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size",'id','size_name');
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	
	if($within_group==1){
		if($search_type==1)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
				else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";

				//if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
				if ($search_by==3) $job_cond=" and job_no_mst = '$search_str' ";
				else if ($search_by==4) $style_cond=" and a.style_ref_no = '$search_str' ";
				else if ($search_by==5) $po_cond=" and b.po_number = '$search_str' ";
				else if ($search_by==6) $po_cond=" and e.delivery_no = '$search_str' ";
				else if ($search_by==7) $group_cond=" and f.grouping = '$search_str' ";
			}
		}
		else if($search_type==4 || $search_type==0)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
				
				//if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'"; 
				if ($search_by==3) $job_cond=" and job_no_mst like '%$search_str%'";   
				else if ($search_by==4) $style_cond=" and a.style_ref_no like '%$search_str%'";  
				else if ($search_by==5) $po_cond=" and b.po_number like '%$search_str%'";
				else if ($search_by==6) echo $po_cond=" and e.delivery_no like '%$search_str%'";   
				else if ($search_by==7) $group_cond=" and f.grouping like '%$search_str%' ";
			}
		}
		else if($search_type==2)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
				
				//if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'"; 
				if ($search_by==3) $job_cond=" and job_no_mst like '$search_str%'";   
				else if ($search_by==4) $style_cond=" and a.style_ref_no like '$search_str%'";  
				else if ($search_by==5) $po_cond=" and b.po_number like '$search_str%'";  
				else if ($search_by==6) $po_cond=" and e.delivery_no like '$search_str%'";
				else if ($search_by==7) $group_cond=" and f.grouping like '$search_str%' ";
			}
		}
		else if($search_type==3)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
				
				//if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'"; 
				if ($search_by==3) $job_cond=" and job_no_mst like '%$search_str'";  
				else if ($search_by==4) $style_cond=" and a.style_ref_no like '%$search_str'";  
				else if ($search_by==5) $po_cond=" and b.po_number like '%$search_str'";
				else if ($search_by==6) $po_cond=" and e.delivery_no like '%$search_str'";  
				else if ($search_by==7) $group_cond=" and f.grouping like '%$search_str' ";
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
				if ($search_by==3) $job_cond=" and job_no_mst= '$search_str' ";
				else if ($search_by==4) $within_no_cond=" and b.buyer_po_no = '$search_str' ";
				else if ($search_by==5) $within_no_cond=" and b.buyer_style_ref = '$search_str' ";
				else if ($search_by==6) $within_no_cond=" and e.delivery_no = '$search_str' ";
			}
			
			
		}
		else if($search_type==4 || $search_type==0)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
				if ($search_by==3) $job_cond=" and job_no_mst like '%$search_str%'";  
				else if ($search_by==4) $within_no_cond=" and b.buyer_po_no like '%$search_str%'";
				else if ($search_by==5) $within_no_cond=" and b.buyer_style_ref like '$search_str%'"; 
				else if ($search_by==6) $within_no_cond=" and e.delivery_no like '%$search_str%' ";
			}
			
		}
		else if($search_type==2)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
				if ($search_by==3) $job_cond=" and job_no_mst like '$search_str%'";  
				else if ($search_by==4) $within_no_cond=" and b.buyer_po_no like '$search_str%'";
				else if ($search_by==5) $within_no_cond=" and b.buyer_style_ref like '$search_str%'"; 
				else if ($search_by==6) $within_no_cond=" and e.delivery_no = '$search_str' ";
			}
			
		}
		else if($search_type==3)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
				if ($search_by==3) $job_cond=" and job_no_mst like '%$search_str'";  
				else if ($search_by==4) $within_no_cond=" and b.buyer_po_no like '%$search_str'";
				else if ($search_by==5) $within_no_cond=" and b.buyer_style_ref like '%$search_str'"; 
				else if ($search_by==6) $within_no_cond=" and e.delivery_no = '$search_str' "; 
			}
			
		}	
		
	}
	
		
	
	if($db_type==0)
	{ 
		if ($search_by == 6)
		{
			$select_delivery_date="e.delivery_date";
		}
		else
		{
			$select_delivery_date="a.delivery_date";
		}

		if ($cbo_date_type == 1)
		{
			if ($data[2]!="" &&  $data[3]!="")
			$order_rcv_del_date = "and a.receive_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; 
			else $order_rcv_date ="";

		}
		else
		{
			if ($data[2]!="" &&  $data[3]!="")
			$order_rcv_del_date = "and $select_delivery_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; 
			else $order_rcv_del_date ="";
		}

		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[10]";
		$insert_year="YEAR(a.insert_date)";
	
	}
	else
	{
		if ($search_by == 6)
		{
			$select_delivery_date="e.delivery_date";
		}
		else
		{
			$select_delivery_date="a.delivery_date";
		}

		if ($cbo_date_type == 1)
		{
			if ($data[2]!="" &&  $data[3]!="")
			$order_rcv_del_date = "and a.receive_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; 
			else $order_rcv_del_date ="";
		}
		else
		{
			if ($data[2]!="" &&  $data[3]!="")
			$order_rcv_del_date = "and $select_delivery_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; 
			else $order_rcv_del_date ="";
		}

		$year_cond=" and to_char(a.insert_date,'YYYY')=$data[10]";
		$insert_year="to_char(a.insert_date,'YYYY')";
		
	}
	
	/*$po_ids='';
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	if(($job_cond!="" && $search_by==3) || ($style_cond!="" && $search_by==4)|| ($po_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
		if ($po_ids=="")
		{
			$po_idsCond="";
			echo "Not Found."; die;
		}
	}
	
	if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";*/
	if($within_group==1)
	{
	
	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id";
	
	//$id_cond="listagg(b.id,',') within group (order by b.id)";
	if(($job_cond!="" && $search_by==3) || ($style_cond!="" && $search_by==4)|| ($po_cond!="" && $search_by==5))
	{
	if ($data[1]!=0) $buyer_job_condi=" and a.company_name='$data[1]'"; else { $buyer_job_condi=""; echo "PLease Select Party name"; die;}	
		// echo "select $id_cond from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0  $buyer_job_condi"; die;
		$po_ids = return_field_value("$id_cond", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond  $buyer_job_condi and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
		
		$po_ids=$po_ids->load();
		
		if ($po_ids!="") { $po_idsCond=" and b.buyer_po_id in ($po_ids)"; } else  { echo "Buyer Job Not Found"; die;}
		
		//echo $po_ids; die;
	}
}
	
	$buyer_po_arr=array();
	
	// $po_sql ="Select a.style_ref_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	// $po_sql_res=sql_select($po_sql);
	// foreach ($po_sql_res as $row)
	// {
	// 	$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
	// 	$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	// 	$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
	// }
	// unset($po_sql_res);
	$po_sql="select a.subcon_job, a.within_group, a.party_id, b.id, b.order_no, b.order_quantity, b.buyer_po_id,b.buyer_po_no, b.buyer_style_ref, 
   b.buyer_buyer from subcon_ord_mst a, subcon_ord_dtls b where a.embellishment_job=b.job_no_mst and a.entry_form=204  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
   //echo $po_sql;die;
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
		$buyer_po_arr[$row[csf("id")]]['buyer_buyer']=$row[csf("buyer_buyer")];
	}
	unset($po_sql_res);
	
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	//$arr=array (3=>$comp,6=>$yes_no,7=>$color_lib_arr);
	
	if($db_type==0)
	{
		$ins_year_cond="year(a.insert_date)";
		$color_id_str="group_concat(c.color_id) as color_id";
		$buyer_po_id_cond="year(b.buyer_po_id) as buyer_po_id";
		$order_id_cond="year(b.id) as order_id";

	}
	else if($db_type==2)
	{
		// $ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		// $color_id_str="listagg(c.color_id,',') within group (order by c.color_id)";
		// $buyer_po_id_cond="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
		// $order_id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as order_id";

		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str="rtrim(xmlagg(xmlelement(e,c.color_id,',').extract('//text()') order by c.color_id).GetClobVal(),',') as color_id";
		$buyer_po_id_cond="rtrim(xmlagg(xmlelement(e,b.buyer_po_id,',').extract('//text()') order by b.buyer_po_id).GetClobVal(),',') as buyer_po_id";
		$order_id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as order_id";
	}
	// $sql= "select a.id, a.embellishment_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,a.currency_id, $color_id_str as color_id, $buyer_po_id_cond as buyer_po_id,b.buyer_po_no,b.buyer_style_ref,b.buyer_buyer    
	//  from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_dtls d  
	//  where a.entry_form=311 and d.bill_status!=1 and a.embellishment_job=b.job_no_mst and a.id=b.mst_id and c.id=d.color_size_id and a.status_active=1 and b.status_active=1 and d.status_active=1 and c.status_active=1 and c.is_deleted=0  $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $job_cond $style_cond $po_cond $within_no_cond and b.id=c.mst_id  
	//  group by a.id, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,a.currency_id,b.buyer_po_no,b.buyer_style_ref,b.buyer_buyer 
	//  ";

	 if($variable_status==1)
	 {
		if($within_group==1){
			$sql= "SELECT a.id, a.embellishment_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date,f.grouping, a.order_no, $select_delivery_date, a.currency_id,a.within_group, $color_id_str, $buyer_po_id_cond ,$order_id_cond  
			from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_dtls d, subcon_delivery_mst e, wo_po_break_down f   
			where a.entry_form=311 and d.bill_status!=1 and f.id=b.buyer_po_id and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.mst_id and c.id=d.color_size_id and d.mst_id=e.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $order_rcv_del_date $year_cond $company $buyer $withinGroup $search_com_cond $po_idsCond   $style_cond $po_cond $within_no_cond $group_cond and b.id=c.mst_id  and e.entry_form=325
			group by a.id, f.grouping, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, $select_delivery_date,a.currency_id,a.within_group
			";
			//echo $sql; exit();
		}else{
			$sql= "SELECT a.id, a.embellishment_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, $select_delivery_date, a.currency_id,a.within_group, $color_id_str, $buyer_po_id_cond ,$order_id_cond  
			from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_dtls d, subcon_delivery_mst e   
			where a.entry_form=311 and d.bill_status!=1 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.mst_id and c.id=d.color_size_id and d.mst_id=e.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $order_rcv_del_date $year_cond $company $buyer $withinGroup $search_com_cond $po_idsCond   $style_cond $po_cond $within_no_cond and b.id=c.mst_id  and e.entry_form=325
			group by a.id, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, $select_delivery_date,a.currency_id,a.within_group
			";
			
		}
		  
	 }else{
		if($within_group==1){
			$sql= "SELECT a.id, a.embellishment_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date,f.grouping, a.order_no, $select_delivery_date, a.currency_id,a.within_group, $color_id_str, $buyer_po_id_cond ,$order_id_cond  
			from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_dtls d, subcon_delivery_mst e, wo_po_break_down f   
			where a.entry_form=311 and d.bill_status!=1 and f.id=b.buyer_po_id and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.mst_id and c.id=d.color_size_id and d.mst_id=e.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $order_rcv_del_date $year_cond $company $buyer $withinGroup $search_com_cond $po_idsCond   $style_cond $po_cond $within_no_cond $group_cond and b.id=c.mst_id  and e.entry_form=325
			group by a.id, f.grouping, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, $select_delivery_date,a.currency_id,a.within_group
			";
			//echo $sql; exit();
		}else{
			$sql= "SELECT a.id, a.embellishment_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id,e.delivery_no,e.delivery_prefix_num,e.id as delivery_id, a.location_id, a.party_id, a.receive_date, a.order_no, $select_delivery_date, a.currency_id,a.within_group, $color_id_str, $buyer_po_id_cond ,$order_id_cond  
			from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_dtls d, subcon_delivery_mst e   
			where a.entry_form=311 and d.bill_status!=1 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.mst_id and c.id=d.color_size_id and d.mst_id=e.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $order_rcv_del_date $year_cond $company $buyer $withinGroup $search_com_cond $po_idsCond  $style_cond $po_cond $within_no_cond and b.id=c.mst_id  and e.entry_form=325
			group by a.id, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id,e.delivery_no,e.delivery_prefix_num,e.id, a.location_id, a.party_id, a.receive_date, a.order_no, $select_delivery_date,a.currency_id,a.within_group
			";
		}
		 
	 } 

	//echo $sql;

	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="945" >
        <thead>
		<th width="30">SL</th>
            <th width="60">Job No</th>
            <th width="60">IR/IB</th>
            <th width="60">Year</th>
            <th width="120">W/O No</th>
            <? if($variable_status==2) {?>
            <th width="120">Delivery No</th>
            <? } ?>
            <th width="100">Buyer Po</th>
            <th width="100">Buyer Style</th>
            <th width="80">Ord Receive Date</th>
            <th width="80">Delivery Date</th>
            <th>Color</th>
        </thead>
        </table>
        <div style="width:965px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="945" class="rpt_table" id="tbl_po_list">
        <tbody>
            <? 
			$sql =  sql_select("select quantity_control,id from variable_setting_printing_prod where company_name = $data[0] and variable_list =6 and is_deleted = 0 and status_active = 1");
			$variable_return_data="";
			if(count($sql)>0)
			{
				$variable_return_data=$sql[0][csf('quantity_control')];
			}
			else
			{ 
				$variable_return_data=0; 
			}
				//echo $variable_return_data; die;
				
            $i=1;
            foreach($data_array as $row)
            {  
				
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($db_type==0)
				{
					$color_ids = $row[csf('color_id')];
					$buyer_po_ids = $row[csf('buyer_po_id')];
					$order_ids = $row[csf('order_id')];
				}
				else
				{
					$color_ids = $row[csf('color_id')]->load();
					$buyer_po_ids = $row[csf('buyer_po_id')]->load();
					$order_ids = $row[csf('order_id')]->load();
				}
				//$excolor_id=array_unique(explode(",",$row[csf('color_id')]));
				 $excolor_id=array_unique(explode(",",$color_ids));
				$color_name="";	
				//var_dump($excolor_id);
				foreach ($excolor_id as $color_id)
				{
					if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
				}
				// foreach ($excolor_id as $color_id)
				// {
				// 	if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
				// }
				$buyer_po=""; $buyer_style=""; $buyer_name='';
				$order_id=explode(",",$order_ids);
				foreach($order_id as $po_id)
				{
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
					
					if($row[csf('within_group')]==1){
					if($buyer_name=="") $buyer_name=$buyer_arr[$buyer_po_arr[$po_id]['buyer_buyer']]; else $buyer_name.=','.$buyer_arr[$buyer_po_arr[$po_id]['buyer_buyer']];}elseif($row[csf('within_group')]==2){if($buyer_name=="") $buyer_name=$buyer_po_arr[$po_id]['buyer_buyer']; else $buyer_name.=','.$buyer_po_arr[$po_id]['buyer_buyer'];}
				}
				//$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
				// foreach($buyer_po_id as $po_id)
				// {
				// 	if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
				// 	if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
				// 	if($buyer_name=="") $buyer_name=$buyer_arr[$buyer_po_arr[$po_id]['buyer']]; else $buyer_name.=','.$buyer_arr[$buyer_po_arr[$po_id]['buyer']];
				// }
				$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				$buyer_name=implode(",",array_unique(explode(",",$buyer_name)));

				// if ($within_group==1){
				// 	$buyer_name=$buyer_name;
				// }else{
				// 	$buyer_name=$row[csf('buyer_buyer')];
				// }
                ?>
				 <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf('embellishment_job')].'***'.$row[csf('order_no')].'***'.$buyer_style.'***'.$buyer_name.'***'.$row[csf('currency_id')].'***'.$search_by.'***'.$search_str.'***'.$row[csf('delivery_id')].'***'.$row[csf('delivery_prefix_num')].'***'.$variable_return_data; ?>')" style="cursor:pointer"  id="search<? echo $row[csf('delivery_id')];?>" >

				<td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="60"><? echo $row[csf('grouping')]; ?></td>
                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $row[csf('order_no')]; ?></td>
                     <? if($variable_status==2) {?>
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
	<? if($variable_return_data==2)
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
			<? 
		}
	} 
	?>
	<?    
	exit();
}

if($action=="load_php_dtls_form")
{
	//echo $data;
	$exdata=explode("**",$data);
	//print_r($exdata);die;
	$jobno=''; $update_id=0;
	$update_id=$exdata[0];
	$jobno=$exdata[1];
	$search_type=$exdata[3];
	$delivery_id=$exdata[4];
	$variable_status=$exdata[5];
	$separate_delivery_id=$exdata[6];
	$job_wise_delivery_id=$exdata[7];
	$color_arrey=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
	$size_arrey=return_library_array( "SELECT id,size_name from  lib_size where status_active =1 and is_deleted=0",'id','size_name');
	$buyer_po_arr=array();
	
	// $po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	// $po_sql_res=sql_select($po_sql);
	// foreach ($po_sql_res as $row)
	// {
	// 	$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
	// 	$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	// }
	// unset($po_sql_res);

	$po_sql="select a.subcon_job, a.within_group, a.party_id, b.id, b.order_no, b.order_quantity, b.buyer_po_id,b.buyer_po_no, b.buyer_style_ref, 
   b.buyer_buyer from subcon_ord_mst a, subcon_ord_dtls b where a.embellishment_job=b.job_no_mst and a.entry_form=204  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
		$buyer_po_arr[$row[csf("id")]]['buyer_buyer']=$row[csf("buyer_buyer")];
	}
	unset($po_sql_res);
	
	//$bill_process_id=14;
	//$entry_form=332;
	
	$sql_del="SELECT a.id, a.mst_id, a.delivery_id,a.order_id, a.delivery_qty,a.color_size_id  from subcon_inbound_bill_dtls a, subcon_inbound_bill_mst b where b.id=a.mst_id   and a.entry_form=332  and b.entry_form=332 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	//echo $sql_del;die;
	$sql_del_res =sql_select($sql_del);
	$updtls_data_arr=array(); $pre_qty_arr=array();
	
	$prev_updtls_data_arr=array();
	
	foreach ($sql_del_res as $row)
	{
		$updtls_data_arr[$row[csf("order_id")]][$row[csf("delivery_id")]]['qty']+=$row[csf("delivery_qty")];
		
		//$prev_updtls_data_arr[$row[csf("order_id")]][$row[csf("color_size_id")]]['qty']+=$row[csf("delivery_qty")];
	}
	unset($sql_del_res);

	$sql_size_wise_del="SELECT a.id, a.mst_id, a.delivery_id,a.order_id, a.delivery_qty,a.color_size_id  from subcon_inbound_bill_dtls a, subcon_inbound_bill_mst b where b.id=a.mst_id   and a.entry_form=332  and  a.mst_id !='$update_id' and b.entry_form=332 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	//echo $sql_size_wise_del;die;
	$sql_size_wise_del_res =sql_select($sql_size_wise_del);
	$updtls_size_wise_arr=array(); 
	
	foreach ($sql_size_wise_del_res as $row)
	{
		$updtls_size_wise_arr[$row[csf("order_id")]][$row[csf("color_size_id")]]['qty']+=$row[csf("delivery_qty")];
		
		//$updtls_size_wise_arr[$row[csf("delvID")]][$row[csf("color_size_id")]]['qty']+=$row[csf("bil_qty")];
	}
	unset($sql_size_wise_del_res);

	if($variable_status!="")
	{
		
		$variable_status=$variable_status;
		
	}
	else
	{
		$variable_status=0;
	}
	
	
	if(($variable_status)*1==2)
	{
		//echo $search_type."==".$delivery_id; die;
		if($update_id>0)
		{		
			
			$sql_job="select a.id, a.embellishment_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date,a.currency_id, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, b.main_process_id, b.order_uom, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty , c.rate as colorSizeRate,d.id as delvID,d.company_id, d.delivery_prefix_num, d.delivery_date, e.id as delivery_id, e.delivery_qty, f.id as upid, f.rate, f.amount,f.domestic_amount, f.remarks,b.buyer_po_no, b.buyer_style_ref,a.within_group,g.variable_status,f.delivery_qty as bil_qty,e.color_size_id
			from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e, subcon_inbound_bill_dtls f ,subcon_inbound_bill_mst g
			where a.entry_form=311 and a.embellishment_job=b.job_no_mst  and b.job_no_mst=c.job_no_mst   and a.id=b.mst_id and d.id=e.mst_id and c.id=e.color_size_id and e.id=f.delivery_id and g.id=f.mst_id and f.process_id=14  and f.entry_form=332 and c.qnty>0 and f.mst_id='$update_id' and d.entry_form=325 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and b.id=c.mst_id and a.embellishment_job='$jobno' order by delivery_prefix_num ASC"; 
			
		}
		else
		{
			//echo $search_type."==".$delivery_id; die;
			if($search_type==6 && $delivery_id!=0)
			{
				 $sql_job="select a.id, a.embellishment_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date,a.currency_id, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, b.main_process_id, b.order_uom, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty , c.rate as colorSizeRate,d.id as delvID, d.company_id ,d.delivery_prefix_num, d.delivery_date, e.id as delivery_id, e.delivery_qty,  0 as upid,  0 as rate, 0 as amount,0 as domestic_amount, null as remarks,b.buyer_po_no, b.buyer_style_ref,a.within_group, null as variable_status,0 as bil_qty,e.color_size_id
				from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e
				where a.entry_form=311 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.mst_id and d.id=e.mst_id and c.id=e.color_size_id and e.bill_status!=1 and d.entry_form=325 and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and c.qnty>0 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.id=c.mst_id and c.status_active=1 and c.is_deleted=0  and a.embellishment_job='$jobno' and d.delivery_prefix_num='$delivery_id' order by delivery_prefix_num ASC";
			
			}
			else
			{
				 $sql_job="select a.id, a.embellishment_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date,a.currency_id, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, b.main_process_id, b.order_uom, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty , c.rate as colorSizeRate,d.id as delvID, d.company_id ,d.delivery_prefix_num, d.delivery_date, e.id as delivery_id, e.delivery_qty,  0 as upid,  0 as rate, 0 as amount,0 as domestic_amount, null as remarks,b.buyer_po_no, b.buyer_style_ref,a.within_group, null as variable_status,0 as bil_qty,e.color_size_id
				from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e
				where a.entry_form=311 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.mst_id and d.id=e.mst_id and c.id=e.color_size_id and e.bill_status!=1 and d.entry_form=325 and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and c.qnty>0 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and b.id=c.mst_id and a.embellishment_job='$jobno' and d.id in ($job_wise_delivery_id) order by delivery_prefix_num ASC";
			}
				
		}
	}
	//echo $sql_job;die;
	
	/*$sql_size_wise_del="SELECT a.id, a.mst_id, a.delivery_id,a.order_id, a.delivery_qty,a.color_size_id  from subcon_inbound_bill_dtls a, subcon_inbound_bill_mst b where b.id=a.mst_id   and a.entry_form=332  and  a.mst_id !='$update_id' and b.entry_form=332 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_size_wise_del_res =sql_select($sql_size_wise_del);
	$updtls_size_wise_arr=array(); 
	
	foreach ($sql_size_wise_del_res as $row)
	{
		$updtls_size_wise_arr[$row[csf("order_id")]][$row[csf("color_size_id")]]['qty']+=$row[csf("delivery_qty")];
	}
	unset($sql_size_wise_del_res);*/

	//$sql_del="select a.id, a.mst_id, a.order_id, a.delivery_qty, a.remarks, a.color_size_id from subcon_delivery_dtls a, subcon_delivery_mst b where b.id=a.mst_id and b.job_no='$jobno' and b.entry_form=325 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	/*$sql_bill="select id, delivery_id, order_id, delivery_qty, rate, amount, remarks, color_size_id from subcon_inbound_bill_dtls where mst_id='$update_id' and process_id=14 and status_active=1 and is_deleted=0";
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
	
		//if($update_id>0)
		//{
			
				/* $sql_job="select a.id, a.embellishment_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date,a.currency_id, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, b.main_process_id, b.order_uom, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty , c.rate as colorSizeRate,d.id as delvID,d.company_id, d.delivery_prefix_num, d.delivery_date, e.id as delivery_id, e.delivery_qty, f.id as upid, f.rate, f.amount,f.domestic_amount, f.remarks,g.variable_status,f.delivery_qty as bil_qty,e.color_size_id
				from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e, subcon_inbound_bill_dtls f ,subcon_inbound_bill_mst g
				where a.entry_form=204 and a.embellishment_job=b.job_no_mst  and b.job_no_mst=c.job_no_mst   and a.id=b.mst_id and d.id=e.mst_id and c.id=e.color_size_id and e.id=f.delivery_id and g.id=f.mst_id and f.process_id=13  and f.entry_form=395 and c.qnty>0 and f.mst_id='$update_id' and d.entry_form=254 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0  and b.id=c.mst_id and a.embellishment_job='$jobno' order by delivery_prefix_num ASC"; 
				*/
			//	$sql_job="select a.id, a.embellishment_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date,a.currency_id, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, b.main_process_id, b.order_uom, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty , c.rate as colorSizeRate,d.id as delvID,d.company_id, d.delivery_prefix_num, d.delivery_date, e.id as delivery_id, e.delivery_qty, f.id as upid, f.rate, f.amount ,f.domestic_amount, f.remarks,b.buyer_po_no, b.buyer_style_ref,a.within_group,f.delivery_qty as bil_qty
		//from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e, subcon_inbound_bill_dtls f 
		//where a.entry_form=311 and a.embellishment_job=b.job_no_mst and a.id=b.mst_id and d.id=e.mst_id and c.id=e.color_size_id and e.id=f.delivery_id and f.process_id=14  and f.entry_form=332 and f.mst_id='$update_id' and d.entry_form=325 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0  and b.id=c.mst_id and a.embellishment_job='$jobno' order by delivery_prefix_num ASC"; 
			
		//}
		//else
		//{
			
				/* $sql_job="select a.id, a.embellishment_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date,a.currency_id, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, b.main_process_id, b.order_uom, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty , c.rate as colorSizeRate,d.id as delvID, d.company_id ,d.delivery_prefix_num, d.delivery_date, e.id as delivery_id, e.delivery_qty,  0 as upid,  0 as rate, 0 as amount,0 as domestic_amount, null as remarks, null as variable_status,0 as bil_qty,e.color_size_id
				from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e
				where a.entry_form=204 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.mst_id and d.id=e.mst_id and c.id=e.color_size_id and e.bill_status!=1 and d.entry_form=254 and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and c.qnty>0 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.id=c.mst_id and a.embellishment_job='$jobno' and d.delivery_prefix_num='$delivery_id' order by delivery_prefix_num ASC";
				*/
				/// $sql_job="select a.id, a.embellishment_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date,a.currency_id, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, b.main_process_id, b.order_uom, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty , c.rate as colorSizeRate,d.id as delvID, d.company_id ,d.delivery_prefix_num, d.delivery_date, e.id as delivery_id, e.delivery_qty,  0 as upid,  0 as rate, 0 as amount,0 as domestic_amount, null as remarks,b.buyer_po_no, b.buyer_style_ref,a.within_group
		//from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e
		//where a.entry_form=311 and a.embellishment_job=b.job_no_mst and a.id=b.mst_id and d.id=e.mst_id and c.id=e.color_size_id and e.bill_status!=1 and d.entry_form=325 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.id=c.mst_id and a.embellishment_job='$jobno'  order by delivery_prefix_num ASC";
			
		
				
		//}
	
	
	
	////////////////backup satart
	
	if(($variable_status)*1==1 || ($variable_status)*1==0)
	{
		if($search_type==6 && $delivery_id!=0)
		{
			
			$sql_job="(select a.id, a.embellishment_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date,a.currency_id, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, b.main_process_id, b.order_uom, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty , c.rate as colorSizeRate,d.id as delvID,d.company_id, d.delivery_prefix_num, d.delivery_date, e.id as delivery_id, e.delivery_qty, f.id as upid, f.rate, f.amount,f.domestic_amount, f.remarks,b.buyer_po_no, b.buyer_style_ref,a.within_group,g.variable_status,f.delivery_qty as bil_qty,e.color_size_id
			from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e, subcon_inbound_bill_dtls f ,subcon_inbound_bill_mst g
			where a.entry_form=311 and a.embellishment_job=b.job_no_mst  and b.job_no_mst=c.job_no_mst   and a.id=b.mst_id and d.id=e.mst_id and c.id=e.color_size_id and e.id=f.delivery_id and g.id=f.mst_id and f.process_id=14  and f.entry_form=332 and c.qnty>0 and f.mst_id='$update_id' and d.entry_form=325 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and c.status_active=1 and c.is_deleted=0   and b.id=c.mst_id and a.embellishment_job='$jobno' and d.delivery_prefix_num='$delivery_id')
			union all
			(
				select a.id, a.embellishment_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date,a.currency_id, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, b.main_process_id, b.order_uom, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty , c.rate as colorSizeRate,d.id as delvID, d.company_id ,d.delivery_prefix_num, d.delivery_date, e.id as delivery_id, e.delivery_qty,  0 as upid,  0 as rate, 0 as amount,0 as domestic_amount, null as remarks,b.buyer_po_no, b.buyer_style_ref,a.within_group, null as variable_status,0 as bil_qty,e.color_size_id
				from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e
				where a.entry_form=311 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.mst_id and d.id=e.mst_id and c.id=e.color_size_id and e.bill_status!=1 and d.entry_form=325 and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and c.qnty>0 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.id=c.mst_id and a.embellishment_job='$jobno' and d.delivery_prefix_num='$delivery_id'
			)
			order by delivery_prefix_num ASC";
			
		}
		else
		{
			$sql_job="(select a.id, a.embellishment_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date,a.currency_id, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, b.main_process_id, b.order_uom, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty , c.rate as colorSizeRate,d.id as delvID,d.company_id, d.delivery_prefix_num, d.delivery_date, e.id as delivery_id, e.delivery_qty, f.id as upid, f.rate, f.amount,f.domestic_amount, f.remarks,b.buyer_po_no, b.buyer_style_ref,a.within_group,g.variable_status,f.delivery_qty as bil_qty,e.color_size_id
			from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e, subcon_inbound_bill_dtls f , subcon_inbound_bill_mst g
			where a.entry_form=311 and a.embellishment_job=b.job_no_mst  and b.job_no_mst=c.job_no_mst   and a.id=b.mst_id and d.id=e.mst_id and c.id=e.color_size_id and e.id=f.delivery_id and g.id=f.mst_id and f.process_id=14  and f.entry_form=332 and c.qnty>0 and f.mst_id='$update_id' and d.entry_form=325 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and b.id=c.mst_id and a.embellishment_job='$jobno')
			union all
			(
				select a.id, a.embellishment_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date,a.currency_id, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, b.main_process_id, b.order_uom, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty , c.rate as colorSizeRate,d.id as delvID, d.company_id ,d.delivery_prefix_num, d.delivery_date, e.id as delivery_id, e.delivery_qty,  0 as upid,  0 as rate, 0 as amount,0 as domestic_amount, null as remarks,b.buyer_po_no, b.buyer_style_ref,a.within_group, null as variable_status,0 as bil_qty,e.color_size_id
				from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e
				where a.entry_form=311 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.mst_id and d.id=e.mst_id and c.id=e.color_size_id and e.bill_status!=1 and d.entry_form=325 and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and c.qnty>0 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and b.id=c.mst_id and a.embellishment_job='$jobno'
			)
			order by delivery_prefix_num ASC";
			
		//echo $sql_job;
		}
		 
	}
	
	// $sql_job="(select a.id, a.embellishment_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date,a.currency_id, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, b.main_process_id, b.order_uom, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty , c.rate as colorSizeRate,d.id as delvID,d.company_id, d.delivery_prefix_num, d.delivery_date, e.id as delivery_id, e.delivery_qty, f.id as upid, f.rate, f.amount ,f.domestic_amount, f.remarks,b.buyer_po_no, b.buyer_style_ref,a.within_group
	// 	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e, subcon_inbound_bill_dtls f 
	// 	where a.entry_form=311 and a.embellishment_job=b.job_no_mst and a.id=b.mst_id and d.id=e.mst_id and c.id=e.color_size_id and e.id=f.delivery_id and f.process_id=14  and f.entry_form=332 and f.mst_id='$update_id' and d.entry_form=325 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and c.status_active=1 and c.is_deleted=0   and b.id=c.mst_id and a.embellishment_job='$jobno')
	// 	union all
	// 	(
	// 		select a.id, a.embellishment_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date,a.currency_id, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, b.main_process_id, b.order_uom, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty , c.rate as colorSizeRate,d.id as delvID, d.company_id ,d.delivery_prefix_num, d.delivery_date, e.id as delivery_id, e.delivery_qty,  0 as upid,  0 as rate, 0 as amount,0 as domestic_amount, null as remarks,b.buyer_po_no, b.buyer_style_ref,a.within_group
	// 	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e
	// 	where a.entry_form=311 and a.embellishment_job=b.job_no_mst and a.id=b.mst_id and d.id=e.mst_id and c.id=e.color_size_id and e.bill_status!=1 and d.entry_form=325 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and b.id=c.mst_id and a.embellishment_job='$jobno'
	// 	)
	// 	 order by delivery_prefix_num ASC";
		 
		 ////////////////backup  end
	//echo $sql_job;die;
	
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
		 
		$quantity=0; $dtlsup_id=""; $balanceQty=0; $preDelv_qty=0; $orderQty=0; $remarks="";$buyer_po="";$buyer_style="";
		//$qc_qty=$qcdtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
		//$preDelv_qty=$pre_qty_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
		
		
		if($row[csf('order_uom')]==1){ $colorSizeqnty=$row[csf("qnty")];}
		if($row[csf('order_uom')]==2){  $colorSizeqnty=$row[csf("qnty")]*12; ;}
		if($row[csf('order_uom')]==1){ $colorSizeRate=$row[csf("colorSizeRate")];}
		if($row[csf('order_uom')]==2){  $colorSizeRate=$row[csf("colorSizeRate")]/12; ;}
		
		
		
		if($row[csf("upid")]>0)
		{
			
			//$bil_qty=$row[csf("delivery_qty")];
			//$bil_qty=$row[csf("bil_qty")];
			//$delivery_qty=$updtls_data_arr[$row[csf("po_id")]][$row[csf("delivery_id")]]['qty']-$bil_qty;

			if($row[csf("variable_status")]*1==1 || $row[csf("variable_status")]*1==0)
			{
				$palace_bil_qty=$row[csf("delivery_qty")];
				$bil_qty=$row[csf("delivery_qty")];
				$disabled='disabled';	
				$delivery_qty=$updtls_data_arr[$row[csf("po_id")]][$row[csf("delivery_id")]]['qty']-$bil_qty;
			}
			if($row[csf("variable_status")]*1==2)
			{
				$palace_bil_qty=$row[csf("bil_qty")];
				$bil_qty=$row[csf("bil_qty")];
				$disabled='';
				//$size_wise_delivery_qty=$updtls_size_wise_arr[$row[csf("po_id")]][$row[csf("color_size_id")]]['qty'];
				
				$size_wise_delivery_qty=$updtls_size_wise_arr[$row[csf("po_id")]][$row[csf("color_size_id")]]['qty'];
				//$size_wise_delivery_qty=$updtls_size_wise_arr[$row[csf("delvID")]][$row[csf("color_size_id")]]['qty'];
				//$delivery_qty=$size_wise_delivery_qty-$bil_qty;
				$delivery_qty=$size_wise_delivery_qty;
				//echo $size_wise_delivery_qty; die;
				//$delivery_qty=$updtls_data_arr[$row[csf("po_id")]][$row[csf("delivery_id")]]['qty']-$bil_qty;
			}
			
			//echo "10**".$bil_qty."ee".$delivery_qty; die;
			
			/*if($row[csf("variable_status")]*1==1 || $row[csf("variable_status")]*1==0)
			{
				$palace_bil_qty=$row[csf("delivery_qty")];
				$bil_qty=$row[csf("delivery_qty")];
				$disabled='disabled';	
				$delivery_qty=$updtls_data_arr[$row[csf("po_id")]][$row[csf("delivery_id")]]['qty']-$bil_qty;
			}
			if($row[csf("variable_status")]*1==2)
			{
				$palace_bil_qty=$row[csf("bil_qty")];
				$bil_qty=$row[csf("bil_qty")];
				$disabled='';
				$size_wise_delivery_qty=$updtls_size_wise_arr[$row[csf("po_id")]][$row[csf("color_size_id")]]['qty'];
				$delivery_qty=$size_wise_delivery_qty;
			}*/
		
		}
		else
		{
			
			//$delivery_qty=$updtls_data_arr[$row[csf("po_id")]][$row[csf("delivery_id")]]['qty'];
			if(($variable_status)*1==1 || ($variable_status)*1==0 )
			{
				$palace_bil_qty=$row[csf("delivery_qty")];
				$bil_qty=$row[csf("delivery_qty")];
				$disabled='disabled';
				$delivery_qty=$updtls_data_arr[$row[csf("po_id")]][$row[csf("delivery_id")]]['qty'];
			}
			if(($variable_status)*1==2)
			{
				//$bil_qty=$colorSizeqnty-$delivery_qty;
				//$bil_qty=$colorSizeqnty-$delivery_qty;
				$palace_bil_qty=$row[csf("delivery_qty")];
				$bil_qty=$row[csf("delivery_qty")];
				$size_wise_delivery_qty=$updtls_size_wise_arr[$row[csf("po_id")]][$row[csf("color_size_id")]]['qty'];
				//$size_wise_delivery_qty=$updtls_size_wise_arr[$row[csf("delvID")]][$row[csf("color_size_id")]]['qty'];
				$delivery_qty=$size_wise_delivery_qty;
				$disabled='';
				
				/*if($colorSizeqnty>$row[csf("delivery_qty")])
				{
					$bil_qty=$row[csf("delivery_qty")];
				}
				if($colorSizeqnty<$row[csf("delivery_qty")])
				{
					$bil_qty=$colorSizeqnty-$delivery_qty;
					//$bil_qty=$row[csf("delivery_qty")];
				}
				
				if($colorSizeqnty==$row[csf("delivery_qty")])
				{
					$bil_qty=$colorSizeqnty-$delivery_qty;
					//$bil_qty=$row[csf("delivery_qty")];
				}*/
				//$bil_qty=$colorSizeqnty-$delivery_qty;
				
			}
			
 			/*if(($variable_status)*1==1 || ($variable_status)*1==0 )
			{
				$palace_bil_qty=$row[csf("delivery_qty")];
				$bil_qty=$row[csf("delivery_qty")];
				$disabled='disabled';
				$delivery_qty=$updtls_data_arr[$row[csf("po_id")]][$row[csf("delivery_id")]]['qty'];
			}
			if(($variable_status)*1==2)
			{
  				$palace_bil_qty=$row[csf("delivery_qty")];
				$bil_qty=$row[csf("delivery_qty")];
				$size_wise_delivery_qty=$updtls_size_wise_arr[$row[csf("po_id")]][$row[csf("color_size_id")]]['qty'];
 				$delivery_qty=$size_wise_delivery_qty;
				$disabled='';
 			}*/
			
			
			
		}
		
		
		
		
		if($row[csf("upid")]==0) $row[csf("upid")]='';
		//if($row[csf("rate")]==0) $row[csf("rate")]='';
		if($row[csf("rate")]==0) $row[csf("rate")]=$colorSizeRate;
		if($row[csf("amount")]==0) $row[csf("amount")]='';
		/*if($row[csf("within_group")]==1)
		{
			$buyer_po=$buyer_po_arr[$row[csf("buyer_po_id")]]['po'];
			$buyer_style=$buyer_po_arr[$row[csf("buyer_po_id")]]['style'];
		}
		else
		{
			$buyer_po=$row[csf("buyer_po_no")];
			$buyer_style=$row[csf("buyer_style_ref")];
		}*/
		$buyer_po=$row[csf("buyer_po_no")];
		$buyer_style=$row[csf("buyer_style_ref")];
		?>
        <tr bgcolor="<? echo $bgcolor; ?>">
            <td align="center"><? echo $k; ?>
            	<input name="txtbuyerPoId_<? echo $k; ?>" id="txtbuyerPoId_<? echo $k; ?>" type="hidden" class="text_boxes" style="width:70px" value="<? echo $row[csf("buyer_po_id")]; ?>" />
                <input name="txtdeliveryid_<? echo $k; ?>" id="txtdeliveryid_<? echo $k; ?>" type="hidden" class="text_boxes" style="width:70px" value="<? echo $row[csf("delivery_id")]; ?>" />
            </td>
            <td align="center" id="sysnotd_<? echo $k; ?>"><a href='##' style='color:#000' onClick="print_report('<? echo $row[csf("company_id")].'*'.$row[csf("delvID")].'*'."Embroidery Delivery";?>','embl_delivery_entry_print', 'requires/emb_delivery_controller')"><font color="blue"><strong><? echo $row[csf("delivery_prefix_num")]; ?></strong></font></a></td>
            <td id="deliverydatetd_<? echo $k; ?>"><? echo change_date_format($row[csf("delivery_date")]); ?></td>
            <td style="word-break:break-all" id="txtBuyerPo_<? echo $k; ?>"><? echo $buyer_po;//$buyer_po_arr[$row[csf("buyer_po_id")]]['po']; ?></td>
            <td style="word-break:break-all"><? echo $buyer_style;//$buyer_po_arr[$row[csf("buyer_po_id")]]['style']; ?></td>
            <td style="word-break:break-all" id="txtGmtItem_<? echo $k; ?>"><? echo $garments_item[$row[csf("gmts_item_id")]]; ?></td>
            <td style="word-break:break-all" id="txtBodtyPart_<? echo $k; ?>"><? echo $body_part[$row[csf("body_part")]]; ?></td>
            <td style="word-break:break-all"><? echo $emblishment_name_array[$row[csf("main_process_id")]]; ?></td>
            <td style="word-break:break-all" id="txtEmbType_<? echo $k; ?>"><? echo $emb_type[$row[csf("embl_type")]]; ?></td>
            <td style="word-break:break-all" id="txtColor_<? echo $k; ?>"><? echo $color_arrey[$row[csf("color_id")]]; ?></td>
            <td style="word-break:break-all" id="txtSize_<? echo $k; ?>"><? echo $size_arrey[$row[csf("size_id")]]; ?></td>
            <td style="word-break:break-all" align="right" id="txtOrderQuantity_<? echo $k; ?>" title="<? echo $colorSizeqnty;?>"><? echo $colorSizeqnty; ?></td>
            <td style="word-break:break-all" align="right"  id="txtdeliveryqty_<? echo $k; ?>" title="<? echo $delivery_qty;?>" ><? echo $delivery_qty; ?></td>

             <td align="center" id="hiddenbilqty_<? echo $k; ?>" title="<? echo $row[csf("delivery_qty")]; ?>">
			 <input type="text" name="txtbillqty_<? echo $k; ?>" id="txtbillqty_<? echo $k; ?>" class="text_boxes_numeric" value="<? echo $bil_qty; //$row[csf("delivery_qty")]; ?>" style="width:50px;" onBlur="calculate_amount(<? echo $k; ?>);" <? echo $disabled; ?> placeholder="<? echo $row[csf("delivery_qty")];//$palace_bil_qty; ?>" /></td>

            <td align="center"><input type="text" name="txtInitialRate_<? echo $k; ?>" id="txtInitialRate_<? echo $k; ?>" class="text_boxes_numeric" style="width:50px;" value="<? echo $colorSizeRate; ?>" /></td>
            <td align="center"><input type="text" name="txtbillrate_<? echo $k; ?>" id="txtbillrate_<? echo $k; ?>" class="text_boxes_numeric" style="width:50px;" value="<? echo $row[csf("rate")]; ?>" onBlur="fnc_amount_calculation(this.value,<? echo $k; ?>); fnc_total_calculate();fnc_copy_final_rate(this.id,this.value);" /></td>
            <td align="center"><input type="text" name="txtbillamount_<? echo $k; ?>" id="txtbillamount_<? echo $k; ?>" class="text_boxes_numeric" style="width:50px;" value="<? echo $row[csf("amount")]; ?>" readonly /></td>
            <td align="center"><input type="text" name="txtdomisticamount_<? echo $k; ?>" id="txtdomisticamount_<? echo $k; ?>" class="text_boxes_numeric" style="width:90px;" value="<? echo $row[csf("domestic_amount")]; ?>" readonly /></td>
            <td><input type="text" name="txtRemarks_<? echo $k; ?>" id="txtRemarks_<? echo $k; ?>" class="text_boxes" style="width:80px;" value="<? echo $row[csf("remarks")]; ?>" />
                <input type="hidden" name="txtDtlsUpdateId_<? echo $k; ?>" id="txtDtlsUpdateId_<? echo $k; ?>" style="width:80px" class="text_boxes" value="<? echo $row[csf("upid")]; ?>" />
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
	$bill_process_id=14;
	$entry_form=332;

		
	$color_size_variable_status=return_field_value("validation_qty_control","variable_setting_printing_prod","company_name=$cbo_company_name and variable_list =2 and is_deleted = 0 and status_active = 1");

	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
 		//table lock here 
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die;}

		if($color_size_variable_status==1)
		{
		
		    $duplicat_order_arr=array();
		    for($k=1; $k<=$total_row; $k++)
			{
				$txtColorSizeid="txtColorSizeid_".$k;
				$txtbillqty="txtbillqty_".$k;
				$txtOrderQuantity="txtOrderQuantity_".$k;
				$cumBillQty="txtdeliveryqty_".$k;
				//$duplicat_order_arr[$$txtColorSizeid]+=$$txtColorSizeid;
				$duplicat_order_arr[str_replace("'", '',$$txtColorSizeid)]+=str_replace("'", '',$$txtbillqty);
				
				$balanceQty=str_replace("'", '',$$txtOrderQuantity) -(str_replace("'", '',$$cumBillQty)+$duplicat_order_arr[str_replace("'", '',$$txtColorSizeid)]);
				
				if($balanceQty<0)
				
				{
					echo "14**Bill Qty Over Order Balance.";
			         disconnect($con); die;
				}
			}
			
		}

		if(str_replace("'","",$txt_update_id)=="")
		{
			$id=return_next_id("id", "subcon_inbound_bill_mst", 1);

			if($db_type==0) $year_cond=" and YEAR(insert_date)"; else if($db_type==2) $year_cond=" and TO_CHAR(insert_date,'YYYY')";

			$new_bill_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'EBBI', date("Y",time()), 5, "select prefix_no, prefix_no_num from  subcon_inbound_bill_mst where company_id=$cbo_company_name and process_id=$bill_process_id and entry_form=332 $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));

			$field_array="id, prefix_no, prefix_no_num, bill_no, company_id, location_id, party_source, party_id, party_location_id, bill_date, process_id,entry_form, remarks,currency,exchange_rate,variable_status,delivery_id, inserted_by, insert_date, status_active, is_deleted";

			$data_array="(".$id.",'".$new_bill_no[1]."','".$new_bill_no[2]."','".$new_bill_no[0]."',".$cbo_company_name.",".$cbo_location_name.",".$cbo_within_group.",".$cbo_party_name.",".$cbo_party_location.",".$txt_bill_date.",'".$bill_process_id."','".$entry_form."',".$txt_remarks.",".$cbo_currency_id.",".$txt_exchange_rate.",".$txt_variable_status.",".$txt_delivery_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)"; 
			//echo "INSERT INTO subcon_inbound_bill_mst (".$field_array.") VALUES ".$data_array; disconnect($con); die; 
			
			$return_no=$new_bill_no[0];
			
			$data_arr_dtls="";
			$id1=return_next_id( "id", "subcon_inbound_bill_dtls",1);

			$field_array1 ="id, mst_id, delivery_id, delivery_date, order_id, delivery_qty, rate, amount,domestic_amount, remarks, process_id,entry_form, buyer_po_id, color_size_id, inserted_by, insert_date, status_active, is_deleted";
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
				
				if($db_type==0)
				{
					$deliverydate=change_date_format($date_cell, "Y-m-d", "-",1);
				}
				else
				{
					$deliverydate=change_date_format($date_cell, "d-M-y", "-",1);
				}
				
				if(str_replace("'", '',$$txtbillamount)!="")
				{
					if ($add_comma!=0) $data_array1 .=",";
					$data_array1 .="(".$id1.",".$id.",".$$txtdeliveryid.",'".$deliverydate."',".$$txtpoid.",".$$txtbillqty.",".$$txtbillrate.",".$$txtbillamount.",".$$txtdomisticamount.",".$$txtRemarks.",'".$bill_process_id."','".$entry_form."',".$$txtbuyerPoId.",".$$txtColorSizeid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$id1=$id1+1;
					$add_comma++;
					
					$data_array_delivery[str_replace("'",'',$$txtdeliveryid)] =explode("*",("1"));
					$order_id_arr[]=str_replace("'",'',$$txtdeliveryid);
				}
			}
		}
		$flag=1;
		
		//echo "10**".$id_dtls."**".$QcPassQtyArr[$$emblProdDtlsId];    die;
		//echo "10**INSERT INTO subcon_inbound_bill_dtls (".$field_array1.") VALUES ".$data_array1;    die;
		
		//echo "10**$total_row**INSERT INTO subcon_delivery_dtls (".$field_array_dtls.") VALUES ".$data_arr_dtls;    die;
		
		$rID=sql_insert("subcon_inbound_bill_mst",$field_array,$data_array,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID1=sql_insert("subcon_inbound_bill_dtls",$field_array1,$data_array1,0);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID2=execute_query(bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_array_delivery,$order_id_arr ));
		if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		
		//echo "10**$rID**$rID1**$rID2"; die;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'","",$id)."**".$return_no;
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
				echo "0**".str_replace("'","",$id)."**".$return_no;
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

		if($color_size_variable_status==1)
		{
		
		    $duplicat_order_arr=array();
		    for($k=1; $k<=$total_row; $k++)
			{
				$txtColorSizeid="txtColorSizeid_".$k;
				$txtbillqty="txtbillqty_".$k;
				$txtOrderQuantity="txtOrderQuantity_".$k;
				$cumBillQty="txtdeliveryqty_".$k;
				//$duplicat_order_arr[$$txtColorSizeid]+=$$txtColorSizeid;
				$duplicat_order_arr[str_replace("'", '',$$txtColorSizeid)]+=str_replace("'", '',$$txtbillqty);
				
				$balanceQty=str_replace("'", '',$$txtOrderQuantity) -(str_replace("'", '',$$cumBillQty)+$duplicat_order_arr[str_replace("'", '',$$txtColorSizeid)]);
				
				if($balanceQty<0)
				
				{
					echo "14**Bill Qty Over Order Balance.";
			         disconnect($con); die;
				}
			}
			
		}
		
		$id=str_replace("'",'',$txt_update_id);
		$nameArray= sql_select("select is_posted_account, post_integration_unlock from subcon_inbound_bill_mst where id='$id'");
		$posted_account=$nameArray[0][csf('is_posted_account')];
		$post_integration_unlock=$nameArray[0][csf('post_integration_unlock')]; 
		if($posted_account==1 && $post_integration_unlock==0)
		{
			echo "14**All Ready Posted in Accounting.";
			disconnect($con); die;
		}
		$field_array="location_id*party_source*party_id*party_location_id*bill_date*remarks*currency*exchange_rate*entry_form*variable_status*delivery_id*updated_by*update_date";

		$data_array="".$cbo_location_name."*".$cbo_within_group."*".$cbo_party_name."*".$cbo_party_location."*".$txt_bill_date."*".$txt_remarks."*".$cbo_currency_id."*".$txt_exchange_rate."*".$entry_form."*".$txt_variable_status."*".$txt_delivery_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$sql_dtls="Select id, delivery_id from subcon_inbound_bill_dtls where mst_id=$id and status_active=1 and is_deleted=0";
		$nameArray=sql_select( $sql_dtls );
		foreach($nameArray as $row)
		{
			$dtls_update_id_array[]=$row[csf('id')];
			$delivery_id_arr[$row[csf('id')]]=$row[csf('delivery_id')];
		}
		 
		$return_no=str_replace("'",'',$txt_bill_no);
		
		$id1=return_next_id( "id", "subcon_inbound_bill_dtls",1);
		
		$field_array1 ="id, mst_id, delivery_id, delivery_date, order_id, delivery_qty, rate, amount,domestic_amount, remarks, process_id,entry_form, buyer_po_id, color_size_id, inserted_by, insert_date, status_active, is_deleted";
		$field_array_up ="delivery_id*delivery_date*order_id*delivery_qty*rate*amount*domestic_amount*remarks*buyer_po_id*color_size_id*entry_form*updated_by*update_date";
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
			
			if($db_type==0)
			{
				$deliverydate=change_date_format($date_cell, "Y-m-d", "-",1);
			}
			else
			{
				$deliverydate=change_date_format($date_cell, "d-M-y", "-",1);
			}
			
			if(str_replace("'",'',$$txtDtlsUpdateId)=="")  
			{ 
				if ($add_comma!=0) $data_array1 .=",";
				$data_array1 .="(".$id1.",".$id.",".$$txtdeliveryid.",'".$deliverydate."',".$$txtpoid.",".$$txtbillqty.",".$$txtbillrate.",".$$txtbillamount.",".$$txtdomisticamount.",".$$txtRemarks.",'".$bill_process_id."','".$entry_form."',".$$txtbuyerPoId.",".$$txtColorSizeid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				
				$data_array_delivery[str_replace("'",'',$$txtdeliveryid)] =explode("*",("1"));
				$id_arr_delivery[]=str_replace("'",'',$$txtdeliveryid);
				
				$id1=$id1+1;
				$add_comma++;
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$txtDtlsUpdateId);
				$data_array_up[str_replace("'",'',$$txtDtlsUpdateId)] =explode("*",("".$$txtdeliveryid."*'".$deliverydate."'*".$$txtpoid."*".$$txtbillqty."*".$$txtbillrate."*".$$txtbillamount."*".$$txtdomisticamount."*".$$txtRemarks."*".$$txtbuyerPoId."*".$$txtColorSizeid."*".$entry_form."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				//$delivery_id_arr[str_replace("'",'',$$txtDtlsUpdateId)]=str_replace("'",'',$$delivery_id);
				
				$id_arr_delivery[]=str_replace("'",'',$$txtdeliveryid);
				$data_array_delivery[str_replace("'",'',$$txtdeliveryid)] =explode("*",("1"));
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
		
		//echo "10**$rID**$rID1**$rID2**$rID3**$rID4**$rID5"; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_update_id)."**".$return_no;
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
				echo "1**".str_replace("'","",$txt_update_id)."**".$return_no;
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
		$nameArray= sql_select("SELECT is_posted_account, post_integration_unlock from subcon_inbound_bill_mst where id='$delivery_mst_id' and status_active =1 and is_deleted=0");
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
			load_drop_down( 'emb_bill_issue_controller', company+'_'+within_group+'_'+party_name, 'load_drop_down_buyer_pop', 'buyer_td' );
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
                            <th width="70">Bill ID</th>
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
								echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "load_drop_down( 'emb_bill_issue_controller', this.value+'_'+".$within_group.", 'load_drop_down_buyer_pop', 'buyer_td' );"); ?>
                            </td>
                            <td>
							<?
								echo create_drop_down( "cbo_within_group", 50, $yes_no,"", 0, "--  --",$within_group, "load_drop_down( 'emb_bill_issue_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer_pop', 'buyer_td' );" ); ?>
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
                                    $search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style", 6=>"IR/IB");
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
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_year_selection').value, 'create_bill_search_list_view', 'search_div', 'emb_bill_issue_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
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

if($action=="create_bill_search_list_view")
{
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

		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[9]";
		$insert_year="YEAR(a.insert_date)";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $bill_date = "and a.bill_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $bill_date ="";

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
			}
			if ($data[4]!='') $bill_id_cond=" and a.bill_no='$data[4]'"; else $bill_id_cond="";
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
			if ($data[4]!='') $bill_id_cond=" and a.bill_no like '%$data[4]%'"; else $bill_id_cond="";
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
			if ($data[4]!='') $bill_id_cond=" and a.bill_no like '$data[4]%'"; else $bill_id_cond="";
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
			if ($data[4]!='') $bill_id_cond=" and a.bill_no like '%$data[4]'"; else $bill_id_cond="";
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
				else if ($search_by==6) $group_cond=" and c.grouping = '$search_str' ";
			}
			if ($data[4]!='') $bill_id_cond=" and a.bill_no='$data[4]'"; else $bill_id_cond="";
			
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
				else if ($search_by==6) $group_cond=" and c.grouping = '$search_str%' ";
				
			}
			if ($data[4]!='') $bill_id_cond=" and a.bill_no like '%$data[4]%'"; else $bill_id_cond="";
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
				else if ($search_by==6) $group_cond=" and c.grouping = '$search_str%' ";
				
			}
			if ($data[4]!='') $bill_id_cond=" and a.bill_no like '$data[4]%'"; else $bill_id_cond="";
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
				else if ($search_by==6) $group_cond=" and c.grouping = '%$search_str' ";
			}
			if ($data[4]!='') $bill_id_cond=" and a.bill_no like '%$data[4]'"; else $bill_id_cond="";
		}	
		
	}
	
	
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array("select id, company_name from lib_company",'id','company_name');
	$order_arr=array();
	
	
	$order_sql = sql_select("select a.embellishment_job, a.within_group, a.party_id,a.currency_id, b.id, b.order_no, sum(c.qnty) as qty from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.embellishment_job=b.job_no_mst and b.id=c.mst_id and a.entry_form=311 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by a.embellishment_job, a.within_group, a.party_id,a.currency_id, b.id, b.order_no ");
	foreach($order_sql as $row)
	{
		$order_arr[$row[csf('id')]]['po']=$row[csf('order_no')];
		$order_arr[$row[csf('id')]]['job']=$row[csf('embellishment_job')];
		$order_arr[$row[csf('id')]]['curr']=$row[csf('currency_id')];
	}
	
	unset($order_sql);

	$bokking_arr=array();
	$booking_sql = sql_select("select booking_no , is_approved from wo_booking_mst where status_active=1");
	foreach($booking_sql as $row)
	{
		$bokking_arr[$row[csf('booking_no')]]['is_approved']=$row[csf('is_approved')];
	}
	unset($booking_sql);
	
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
		$id_cond="listagg(b.id,',') within group (order by b.id)";
		$insert_date_cond="TO_CHAR(a.insert_date,'YYYY')";
		$order_id_cond="listagg(b.order_id,',') within group (order by b.order_id)";
		$buyer_po_id_cond="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
		$delivery_id_cond="LISTAGG(CAST(b.delivery_id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY b.delivery_id)";

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
	// $sql= "SELECT a.id, a.bill_no, a.prefix_no_num, $insert_date_cond as year, a.location_id, a.bill_date, a.party_id, a.party_source, a.party_location_id, a.remarks, $order_id_cond as order_id, $buyer_po_id_cond as buyer_po_id, $delivery_id_cond as delivery_id,a.is_posted_account,a.currency, a.exchange_rate,a.delivery_id from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.process_id=13  and a.entry_form=395 $company $buyer_cond $withinGroup $bill_date $bill_id_cond $spo_idsCond $po_idsCond  group by a.id, a.bill_no, a.prefix_no_num, a.insert_date, a.location_id, a.bill_date, a.party_id, a.party_source, a.party_location_id, a.remarks,a.is_posted_account,a.currency, a.exchange_rate,a.delivery_id order by a.id DESC";
	if($within_group==1){
		$sql= "SELECT a.id, a.bill_no, a.prefix_no_num, $insert_date_cond as year, a.location_id, a.bill_date, a.party_id, a.party_source, a.party_location_id, c.grouping, a.remarks, $order_id_cond as order_id, $buyer_po_id_cond as buyer_po_id, $delivery_id_cond as delivery_id,a.is_posted_account,a.currency, a.exchange_rate,a.delivery_id from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b, wo_po_break_down c where a.id=b.mst_id and c.id=b.buyer_po_id and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.process_id=14 and a.entry_form=332 $company $buyer_cond $withinGroup $bill_date $bill_id_cond $spo_idsCond $po_idsCond $year_cond $order_order_buyer_poCond $group_cond group by a.id, a.bill_no, c.grouping, a.prefix_no_num, a.insert_date, a.location_id, a.bill_date, a.party_id, a.party_source, a.party_location_id, a.remarks,a.is_posted_account,a.currency, a.exchange_rate,a.delivery_id order by a.id DESC";
	}else{
		$sql= "SELECT a.id, a.bill_no, a.prefix_no_num, $insert_date_cond as year, a.location_id, a.bill_date, a.party_id, a.party_source, a.party_location_id, a.remarks, $order_id_cond as order_id, $buyer_po_id_cond as buyer_po_id, $delivery_id_cond as delivery_id,a.is_posted_account,a.currency, a.exchange_rate,a.delivery_id from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.process_id=14 and a.entry_form=332 $company $buyer_cond $withinGroup $bill_date $bill_id_cond $spo_idsCond $po_idsCond $year_cond $order_order_buyer_poCond  group by a.id, a.bill_no, a.prefix_no_num, a.insert_date, a.location_id, a.bill_date, a.party_id, a.party_source, a.party_location_id, a.remarks,a.is_posted_account,a.currency, a.exchange_rate,a.delivery_id order by a.id DESC";
	}
	
	//echo $sql;die; 
	$result = sql_select($sql);
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="920" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="70">Bill No</th>
                <th width="70">Year</th>
                <th width="120">Party Name</th>
                <th width="80">Bill Date</th>
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
				$order_no=''; $job_no=""; $curr_id=""; $book_approved="";$odbuyer_po=""; $odbuyer_style=""; $odbuyer_buyer="";
				$order_id=array_unique(explode(",",$row[csf("order_id")]));
				//print_r($order_id);
				foreach($order_id as $val)
				{
					if($order_no=="") $order_no=$order_arr[$val]['po']; else $order_no.=",".$order_arr[$val]['po'];
					if($job_no=="") $job_no=$order_arr[$val]['job']; else $job_no.=",".$order_arr[$val]['job'];
					if($curr_id=="") $curr_id=$order_arr[$val]['curr']; else $curr_id.=",".$order_arr[$val]['curr'];
					if($book_approved=="") $book_approved=$bokking_arr[$order_arr[$val]['po']]['is_approved']; else $book_approved.=",".$bokking_arr[$order_arr[$val]['po']]['is_approved'];
					if($odbuyer_po=="") $odbuyer_po=$orderbuyerpo_arr[$val]['po']; else $odbuyer_po.=','.$orderbuyerpo_arr[$val]['po'];
					if($odbuyer_style=="") $odbuyer_style=$orderbuyerpo_arr[$val]['style']; else $odbuyer_style.=','.$orderbuyerpo_arr[$val]['style'];
					if($odbuyer_buyer=="") $odbuyer_buyer=$orderbuyerpo_arr[$val]['buyer']; else $odbuyer_buyer.=','.$orderbuyerpo_arr[$val]['buyer'];

				}
				
				//$order_arr[$val]['job']
				//echo $job_no.',';
				
				
				
				$order_no=implode(",",array_unique(explode(",",$order_no)));
				$job_no=implode(",",array_unique(explode(",",$job_no)));
				$curr_id=implode(",",array_unique(explode(",",$curr_id)));
				$book_approved=implode(",",array_unique(explode(",",$book_approved)));
				
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
				//$buyer_name=implode(",",array_unique(explode(",",$buyer_name)));
				
				
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
					$buyer_name=implode(",",array_unique(explode(",",$odbuyer_buyer)));
				}
				
				$party_name="";
				if($row[csf("party_source")]==1) $party_name=$comp[$row[csf("party_id")]]; else $party_name=$party_arr[$row[csf("party_id")]];
				$str_data="";

				$str_data=$row[csf('id')].'***'.$row[csf('bill_no')].'***'.$row[csf('location_id')].'***'.$row[csf('party_source')].'***'.$row[csf('party_id')].'***'.$row[csf('party_location_id')].'***'.change_date_format($row[csf('bill_date')]).'***'.$row[csf('remarks')].'***'.$job_no.'***'.$order_no.'***'.$buyer_style.'***'.$buyer_po.'***'.$buyer_name.'***'.$curr_id.'***'.$book_approved.'***'.$row[csf('is_posted_account')].'***'.$row[csf('currency')].'***'.$row[csf('exchange_rate')].'***'.$row[csf('delivery_id')];
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $str_data; ?>');" > 
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="70" align="center"><? echo $row[csf("prefix_no_num")]; ?></td>
                        <td width="70" align="center"><? echo $row[csf("year")]; ?></td>
                        <td width="120"><? echo $party_name; ?></td>		
						<td width="80"><? echo change_date_format($row[csf("bill_date")]);  ?></td>
                        <td style="word-wrap:break-word; word-break:break-word;width:120px;"><p><? echo $order_no; ?></p></td>	
						<td style="word-wrap:break-word; word-break:break-word;width:100px;"><? echo $row[csf('grouping')]; ?></td>
                        <td style="word-wrap:break-word; word-break:break-word;width:100px;"><? echo $buyer_po; ?></td>
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
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
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
	
	$sql_mst="select id, prefix_no_num, bill_no, location_id, bill_date, party_id, party_source, process_id, party_location_id, remarks from subcon_inbound_bill_mst where process_id=14 and entry_form=332 and status_active=1 and is_deleted=0 and id='$data[1]'";

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
		//$nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$dataArray[0][csf('party_id')]"); 
		$party_id=$dataArray[0][csf('party_id')];
		$nameArray=sql_select( "SELECT address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_id"); 
		foreach ($nameArray as $result)
		{ 
			if($result!="") $party_address=$result['address_1'];
		}
		//if($address!="") $party_address=$party_name.', '.$party_address;
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
                <td colspan="5"><? echo $dataArray[0][csf('remarks')]; ?></td>
            </tr>
        </table>
        <br>
        <div style="width:100%;">
            <table align="right" cellspacing="0" width="1020" border="1" rules="all" class="rpt_table" style="font-size:12px">
                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
                    <th width="30">SL</th>
                    <th width="50">Delivery ID</th>
                    <th width="60">Delivery Date</th>
                    <th width="90">Buyer PO</th>
                    <th width="90">Buyer Style</th>
                    <th width="90">Gmts Item</th>
                    <th width="80">Body Part</th>
                    <th width="80">Emb Name</th>
                    <th width="70">Process/Type</th>
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
				$job_no = $dataArray[0][csf('job_no')];

				$sql= "select  a.id, a.embellishment_job,a.currency_id, b.id as order_id, b.order_no, b.buyer_po_id, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.id as color_size_id, c.color_id, c.size_id, c.qnty as qty, d.delivery_qty, d.rate, d.amount, d.remarks, d.delivery_id, d.delivery_date,b.buyer_po_no, b.buyer_style_ref from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_inbound_bill_dtls d where a.embellishment_job=b.job_no_mst and b.id=c.mst_id and c.id=d.color_size_id 
				
				and a.entry_form=311 and d.process_id=14  and d.entry_form=332 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.company_id='$data[0]' and d.mst_id='$data[1]' order by d.delivery_id ASC";
				
				//echo $sql; die;
				$sql_res=sql_select($sql);
				
				$row_span_arr=array(); 
				foreach($sql_res as $row)
				{
					$row_span_arr[$delivery_arr[$row[csf("delivery_id")]]]+=1;
				}
						
				//print_r($row_span_arr);
 				$i=0; $grand_tot_qty=0; $k=1;  $del_id_chk=array();

				foreach ($sql_res as $row) 
				{
					$embl_name=$row[csf('main_process_id')];
					if($embl_name==1) $new_subprocess_array= $emblishment_print_type;
					else if($embl_name==2) $new_subprocess_array= $emblishment_embroy_type;
					else if($embl_name==3) $new_subprocess_array= $emblishment_wash_type;
					else if($embl_name==4) $new_subprocess_array= $emblishment_spwork_type;
					else if($embl_name==5) $new_subprocess_array= $emblishment_gmts_type;
					else $new_subprocess_array=$blank_array;
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
                                <td>&nbsp;</td>
							</tr>
						<?
							unset($sub_total_qty);
							unset($sub_total_amt);
						}
						?>
							<tr bgcolor="#dddddd">
								<td colspan="4" align="left" style="font-size:12px"><b>Embl. Job No: <? echo $row[csf("embellishment_job")]; ?>;</b></td>
                                <td colspan="3" align="left" style="font-size:12px"><b>Work Order No: <? echo $row[csf("order_no")]; ?>;</b></td>
                                <td colspan="3" align="left" style="font-size:12px"><b>Order Currency: <? echo $currency[$row[csf("currency_id")]]; ?>;</b></td>
                                <td colspan="5" align="left" style="font-size:12px; display:none"><b>Buyer Style: <? echo $buyer_po_arr[$row[csf("buyer_po_id")]]['style']; ?>;</b></td>
                                <td colspan="5" align="left" style="font-size:12px;"></td>
							</tr>
						<?
						$order_array[]=$row[csf('order_no')];  
						$k++;
					}

					if (!in_array($delivery_arr[$row[csf("delivery_id")]],$del_id_chk))
					{
						$i++; 
						$del_id_chk[]=$delivery_arr[$row[csf("delivery_id")]];
					}
					else
					{
						$i=$i;
					}
					
					$r_span=$row_span_arr[$delivery_arr[$row[csf("delivery_id")]]];
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                    	<?
                    	if(!$chk_span_arr[$delivery_arr[$row[csf("delivery_id")]]])
                    	{
                    		?>
                    			<td rowspan="<? echo $r_span; ?>"><? echo $i; ?></td>
                    			<td rowspan="<? echo $r_span; ?>" style="word-break:break-all"><? echo $delivery_arr[$row[csf("delivery_id")]]; ?></td>
                    			<td rowspan="<? echo $r_span; ?>" style="word-break:break-all"><? echo change_date_format($row[csf("delivery_date")]); ?></td>
                    		<?
                    		$chk_span_arr[$delivery_arr[$row[csf("delivery_id")]]]=420;
                    	}

                    	?>
                        <td style="word-break:break-all"><? echo $row[csf("buyer_po_no")];//$buyer_po_arr[$row[csf("buyer_po_id")]]['po']; ?></td>
                        <td style="word-break:break-all"><? echo $row[csf("buyer_style_ref")];//$buyer_po_arr[$row[csf("buyer_po_id")]]['style']; ?></td>
                        <td style="word-break:break-all"><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></td>
                        <td style="word-break:break-all"><? echo $body_part[$row[csf('body_part')]]; ?>&nbsp;</td>
                        <td style="word-break:break-all"><? echo $emblishment_name_array[$embl_name]; ?>&nbsp;</td>
                        <td style="word-break:break-all"><? echo $new_subprocess_array[$row[csf('embl_type')]]; ?>&nbsp;</td>
                        <td style="word-break:break-all"><? echo $color_arr[$row[csf('color_id')]]; ?>&nbsp;</td>
                        <td style="word-break:break-all" align="center"><? echo $size_arr[$row[csf('size_id')]]; ?>&nbsp;</td>
                        <td align="right"><? echo number_format($row[csf('delivery_qty')], 2, '.', ''); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($row[csf('rate')], 4, '.', ''); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($row[csf('amount')], 2, '.', ''); ?>&nbsp;</td>
                        <td style="word-break:break-all"><? echo $row[csf('remarks')]; ?>&nbsp;</td>
                    </tr>
					<?
					//$i++;
					$sub_total_qty+=$row[csf('delivery_qty')];
					$grand_tot_qty+=$row[csf('delivery_qty')];
					
					$sub_total_amt+=$row[csf('amount')];
					$grand_tot_amt+=$row[csf('amount')];
				}
				?>
                <tr class="tbl_bottom">
                    <td colspan="11" align="right"><b>Order Total:</b></td>
                    <td align="right"><b><? echo number_format($sub_total_qty,2); ?></b></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><? echo number_format($sub_total_amt,2); ?></b></td>
                    <td>&nbsp;</td>
                </tr>
                
                <tr class="tbl_bottom">
                    <td align="right" colspan="11"><strong>Grand Total</strong></td>
                    <td align="right"><? echo number_format($grand_tot_qty, 2, '.', ''); ?>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($grand_tot_amt, 2, '.', ''); ?>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
            <br>
			<? echo signature_table(170, $com_id, "1020px",$data[3]); ?>
        </div>
    </div>
	<?
	exit();
}
?>